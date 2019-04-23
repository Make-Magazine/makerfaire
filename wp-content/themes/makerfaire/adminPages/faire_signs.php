<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$selfaire = '';
$type = '';
?>

<!-- New Collapsible menus and more user friendly interface -->
<div id="faire-signs">
   <h2 style="text-align:center">Faire Signs</h2>
   <div class="panel-group" id="accordion">
      <?php
      //get list of faires -
      $sql = "SELECT faire, faire_name, form_ids FROM wp_mf_faire order by start_dt DESC";
      $results = $wpdb->get_results($sql);
      $first = true;
      foreach ($results as $row) {
         ?>
         <div class="panel panel-default">
            <div class="panel-heading accordion-toggle" data-toggle="collapse" data-parent="#accordion" data-target="#collapse<?php echo $row->faire; ?>">
               <h4 class="panel-title"><?php echo $row->faire_name; ?></h4>
            </div>
            <div id="collapse<?php echo $row->faire; ?>" class="panel-collapse collapse <?php if ($first) echo 'in'; ?>">
               <div class="panel-body">
                  <ul class="nav nav-tabs">
                     <li class="active"><a data-toggle="tab" href="#maker<?php echo $row->faire; ?>">Maker Signs</a></li>
                     <li><a data-toggle="tab" href="#presenter<?php echo $row->faire; ?>">Presenter Signs</a></li>
                     <li><a data-toggle="tab" href="#table<?php echo $row->faire; ?>">Table Tags</a></li>
                  </ul>

                  <div class="tab-content">
                     <div id="maker<?php echo $row->faire; ?>" class="tab-pane fade in active">                        
                        <div class="pull-left" style="margin-top: 22px;">                           
                           <i>Click the button to generate all maker signs for this faire</i><br>
                           <input style="text-align:center;width: 400px;"  name="zipCreate" value="Generate all signs" class="button button-large button-primary" onClick="createPDF('<?php echo $row->faire; ?>', 'signs')" />                           
                           <br/>
                           <span class="signs pdfEntList"></span>
                        </div>
                        <?php 
                        $filename = TEMPLATEPATH . '/signs/' . $row->faire . '/maker/lastrun.txt';                        
                        $lastCreated = (file_exists($filename)?file_get_contents($filename):'');
                        ?>
                        <div class="pull-left" style="margin-left: 20px;"><p><br><br><Br>Last created on: <?php echo $lastCreated;?></p></div>
                        <div class="clear"></div>

                        <div class="row is-flex">
                           <div class="col-sm-7 right-border">
                              <h4>Create a zip file of the maker signs:</h4>
                              <input type="hidden" id="zipFiles" value="<?php echo $row->faire; ?>" />
                              <div class="row">
                                 <div class="col-sm-6">
                                    How should we group the zip files?<br/>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="area" checked> By Area<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="subarea"> By Subarea<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="faire"> By Faire<br>
                                 </div>
                                 <div class="col-sm-6">
                                    What entry status(es) should we include?<br/>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="accepted" checked> Accepted Only<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="accAndProp"> Accepted and Proposed<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="all"> All Status
                                 </div>
                              </div>
                              <br>
                              Filter by specific form (optional):
                              <select name="<?php echo $row->faire; ?>filterform" multiple>
                                 <?php
                                 ## Get the form information
                                 $forms = preg_split('/,/', $row->form_ids);
                                 foreach ($forms as $formId) {
                                    $queryForm = "SELECT title FROM wp_gf_form where id = $formId and is_active = 1 and is_trash = 0 order by title DESC";
                                    $formResult = $wpdb->get_results($queryForm);
                                    foreach ($formResult as $formRow) {
                                       $descr = $formRow->title;
                                       ## Add the Form and title to the options
                                       echo "<option value=\"$formId\">$descr</option>";
                                    }
                                 }
                                 ?>
                              </select><br>
                              <p><input type="checkbox" name="<?php echo $row->faire; ?>filtererror" value="error"> Include signs in error?</p>

                              <br/>
                              <input style="text-align:center"  name="zipCreate" value="Re-Create Zip Files" class="button button-large button-primary" onClick="createZip('<?php echo $row->faire; ?>', 'maker')" /><br/>
                              <span class="maker updateMsg"></span>
                           </div>
                           <div class="col-sm-5">
                              <h4>Download generated zip files</h4>
                              <?php
                              $signDir = get_template_directory() . '/signs/' . $row->faire . '/maker/zip/';
                              $files = glob($signDir . "*.zip");
                              if (is_array($files) && !empty($files)) {
                                 //Find all Zip files for this faire
                                 foreach ($files as $filename) {
                                    ?>
                                    <div class="row">
                                       <div class="col-sm-8">
                                          <?php echo '<a href="' . get_template_directory_uri() . '/signs/' . $row->faire . '/maker/zip/' . basename($filename) . '" target="_blank">' . basename($filename) . '</a>'; ?>
                                       </div>
                                       <div class="col-sm-4">
                                          <?php echo date("m/d/Y H:i", filemtime($filename)); ?>
                                       </div>
                                    </div>
                                    <?php
                                 }
                              } else {
                                 echo '<i>No Zip files found.<br>Please use the tools to the left to generate.</i>';
                              }
                              ?>
                           </div>
                        </div>  
                     </div>
                     <div id="presenter<?php echo $row->faire; ?>" class="tab-pane fade">                                                                        
                        <div class="pull-left" style="margin-top: 22px;">                           
                           <i>Click the button to generate all presenter signs for this faire</i><br>
                           <input style="text-align:center;width: 400px;"  name="zipCreate" value="Generate all signs" class="button button-large button-primary" onClick="createPDF('<?php echo $row->faire; ?>', 'presenter')" />                           

                        </div>
                        <?php 
                        $filename = TEMPLATEPATH . '/signs/' . $row->faire . '/presenter/lastrun.txt';                        
                        $lastCreated = (file_exists($filename)?file_get_contents($filename):'');
                        ?>
                        <div class="pull-left" style="margin-left: 20px;"><p><br><br><Br>Last created on: <?php echo $lastCreated;?></p></div>                        
                        <div class="clear"></div>                        
                        <div class="row  is-flex">
                           <div class="col-sm-7  right-border">
                              <h4>Create a zip file of the presenter signs:</h4>
                              <input type="hidden" id="zipFiles" value="<?php echo $row->faire; ?>" />
                              <div class="row">
                                 <div class="col-sm-6">
                                    How we should group the zip file?<br/>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="area" checked> By Area<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="subarea"> By Subarea<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="faire"> By Faire<br>
                                 </div>
                                 <div class="col-sm-6">
                                    What entry status(es) should we include?<br/>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="accepted" checked> Accepted Only<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="accAndProp"> Accepted and Proposed<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="all"> All Status
                                 </div>
                              </div>
                              <br/>
                              <input style="text-align:center"  name="zipCreate" value="Re-Create Zip Files" class="button button-large button-primary" onClick="createZip('<?php echo $row->faire; ?>', 'presenter')" /><br/>
                              <span class="presenter updateMsg"></span>
                           </div>
                           <div class="col-sm-5">
                              <h4>Download generated zip files</h4>
                              <?php
                              $signDir = get_template_directory() . '/signs/' . $row->faire . '/presenter/zip/';
                              $files = glob($signDir . "*.zip");
                              if (is_array($files) && !empty($files)) {
                                 //Find all Zip files for this faire
                                 foreach ($files as $filename) {
                                    ?>
                                    <div class="row">
                                       <div class="col-sm-8">
                                          <?php echo '<a href="' . get_template_directory_uri() . '/signs/' . $row->faire . '/presenter/zip/' . basename($filename) . '" target="_blank">' . basename($filename) . '</a>'; ?>
                                       </div>
                                       <div class="col-sm-4">
                                          <?php echo date("m/d/Y H:i", filemtime($filename)); ?>
                                       </div>
                                    </div>
                                    <?php
                                 }
                              } else {
                                 echo '<i>No Zip files found.<br>Please use the tools to the left to generate.</i>';
                              }
                              ?>
                           </div>                           
                           <div class="col-sm-12">
                              <span class="presenter pdfEntList"></span>
                           </div>
                        </div>
                     </div>
                     <div id="table<?php echo $row->faire; ?>" class="tab-pane fade">
                        <div class="row  is-flex">
                           <div class="col-sm-6 right-border">
                              <h4>Create a zip file of the table tags:</h4>
                              <input type="hidden" id="zipFiles" value="<?php echo $row->faire; ?>" />
                              <div class="row">
                                 <div class="col-sm-6">
                                    How we should group the zip file?<br/>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="area" checked> By Area<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="subarea"> By Subarea<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>seltype" value="faire"> By Faire<br>
                                 </div>
                                 <div class="col-sm-6">
                                    What entry status(es) should we include?<br/>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="accepted" checked> Accepted Only<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="accAndProp"> Accepted and Proposed<br>
                                    <input type="radio" name="<?php echo $row->faire; ?>selstatus" value="all"> All Status
                                 </div>
                              </div>
                              <br/>
                              <input style="text-align:center"  name="zipCreate" value="Re-Create Zip Files" class="button button-large button-primary" onClick="createZip('<?php echo $row->faire; ?>', 'tabletags')" /><br/>
                              <span class="tabletags updateMsg"></span>
                           </div>
                           <div class="col-sm-6">
                              <h4>Download generated zip files</h4>
                              <?php
                              $signDir = get_template_directory() . '/signs/' . $row->faire . '/tabletags/zip/';
                              $files = glob($signDir . "*.zip");
                              if (is_array($files) && !empty($files)) {
                                 //Find all Zip files for this faire
                                 foreach ($files as $filename) {
                                    ?>
                                    <div class="row">
                                       <div class="col-sm-8">
                                          <?php echo '<a href="' . get_template_directory_uri() . '/signs/' . $row->faire . '/tabletags/zip/' . basename($filename) . '" target="_blank">' . basename($filename) . '</a>'; ?>
                                       </div>
                                       <div class="col-sm-4">
                                          <?php echo date("m/d/Y H:i", filemtime($filename)); ?>
                                       </div>
                                    </div>
                                    <?php
                                 }
                              } else {
                                 echo '<i>No Zip files found.<br>Please use the tools to the left to generate.</i>';
                              }
                              ?>
                           </div>


                           <div class="col-sm-12">
                              <span class="tabletags pdfEntList"></span>
                           </div>
                        </div>
                     </div>
                  </div>


               </div>
            </div>
         </div>
         <?php
         $first = false;
      }
      ?>
   </div>
</div>
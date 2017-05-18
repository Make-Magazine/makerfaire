<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$selfaire  = '';
$type   = '';
?>

<!-- New Collapsible menus and more user friendly interface -->
<div id="faire-signs">
  <h2 style="text-align:center">Faire Signs</h2>
  <div class="panel-group" id="accordion">
    <?php
    //get list of faires -
    $sql = "SELECT faire, faire_name FROM wp_mf_faire order by start_dt DESC";
    $results = $wpdb->get_results($sql);
    $first=true;
    foreach($results as $row){
      ?>
      <div class="panel panel-default">
        <div class="panel-heading accordion-toggle" data-toggle="collapse" data-parent="#accordion" data-target="#collapse<?php echo $row->faire;?>">
          <h4 class="panel-title"><?php echo $row->faire_name;?></h4>
        </div>
        <div id="collapse<?php echo $row->faire;?>" class="panel-collapse collapse <?php if($first) echo 'in';?>">
          <div class="panel-body">
            <h4>Maker Signs</h4>
            <div class="row  is-flex">
              <div class="col-sm-5 right-border">
                <?php
                $signDir = get_template_directory().'/signs/'.$row->faire.'/maker/zip/';
                $files = glob($signDir."*.zip");
                if(is_array($files) && !empty($files)){
                  //Find all Zip files for this faire
                  foreach ($files as $filename) {
                    ?>
                    <div class="row">
                      <div class="col-sm-8">
                        <?php echo '<a href="'.get_template_directory_uri().'/signs/'.$row->faire.'/maker/zip/'.basename($filename).'" target="_blank">'.basename($filename).'</a>';?>
                      </div>
                      <div class="col-sm-4">
                        <?php echo date("m/d/Y H:i",filemtime($filename));?>
                      </div>
                    </div>
                    <?php
                  }
                } else{
                  echo 'No Zip files found';
                }
                ?>
              </div>
              <div class="col-sm-2  right-border">
                <input style="text-align:center"  name="zipCreate" value="Generate Maker Signs" class="button button-large button-primary" onClick="createPDF('<?php echo $row->faire;?>','signs')" /><br/>
                <small>This needs to be done before you create the Zip file</small>
              </div>
              <div class="col-sm-5">
                <input type="hidden" id="zipFiles" value="<?php echo $row->faire;?>" />
                <div class="row">
                  <div class="col-sm-4">
                    <b>Grouping:</b><br/>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="area" checked> By Area<br>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="subarea"> By Subarea<br>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="faire"> By Faire<br>
                  </div>
                  <div class="col-sm-8">
                    <b>Status:</b><br/>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="accepted" checked> Accepted Only<br>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="accAndProp"> Accepted and Proposed<br>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="all"> All Status
                  </div>
                </div>
                <br/>
                <input style="text-align:center"  name="zipCreate" value="Re-Create Zip Files" class="button button-large button-primary" onClick="createZip('<?php echo $row->faire;?>','maker')" /><br/>
                <span class="maker updateMsg"></span>
              </div>
              <div class="col-sm-12">
                <span class="signs pdfEntList"></span>
              </div>
            </div>

            <h4>Table Tags</h4>
            <div class="row  is-flex">
              <div class="col-sm-5 right-border">
                <?php
                $signDir  = get_template_directory().'/signs/'.$row->faire.'/tabletags/zip/';
                $files = glob($signDir."*.zip");
                if(is_array($files) && !empty($files)){
                  //Find all Zip files for this faire
                  foreach ($files as $filename) {
                    ?>
                    <div class="row">
                      <div class="col-sm-8">
                        <?php echo '<a href="'.get_template_directory_uri().'/signs/'.$row->faire.'/tabletags/zip/'.basename($filename).'" target="_blank">'.basename($filename).'</a>';?>
                      </div>
                      <div class="col-sm-4">
                        <?php echo date("m/d/Y H:i",filemtime($filename));?>
                      </div>
                    </div>
                    <?php
                  }
                } else{
                  echo 'No Zip files found';
                }
                ?>
              </div>
              <div class="col-sm-2  right-border">
                <input style="text-align:center"  name="zipCreate" value="Generate Table Tags" class="button button-large button-primary" onClick="createPDF('<?php echo $row->faire;?>','tabletags')" /><br/>
                <small>This needs to be done before you create the Zip file</small>
              </div>
              <div class="col-sm-5">
                <input type="hidden" id="zipFiles" value="<?php echo $row->faire;?>" />
                <div class="row">
                  <div class="col-sm-4">
                    <b>Grouping:</b><br/>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="area" checked> By Area<br>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="subarea"> By Subarea<br>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="faire"> By Faire<br>
                  </div>
                  <div class="col-sm-8">
                    <b>Status:</b><br/>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="accepted" checked> Accepted Only<br>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="accAndProp"> Accepted and Proposed<br>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="all"> All Status
                  </div>
                </div>
                <br/>
                <input style="text-align:center"  name="zipCreate" value="Re-Create Zip Files" class="button button-large button-primary" onClick="createZip('<?php echo $row->faire;?>', 'tabletags')" /><br/>
                <span class="tabletags updateMsg"></span>
              </div>
              <div class="col-sm-12">
                <span class="tabletags pdfEntList"></span>
              </div>
            </div>
            <h4>Presenter Signs</h4>
            <div class="row  is-flex">
             <div class="col-sm-5 right-border">
                <?php
                $signDir = get_template_directory().'/signs/'.$row->faire.'/presenter/zip/';
                $files = glob($signDir."*.zip");
                if(is_array($files) && !empty($files)){
                  //Find all Zip files for this faire
                  foreach ($files as $filename) {
                    ?>
                    <div class="row">
                      <div class="col-sm-8">
                        <?php echo '<a href="'.get_template_directory_uri().'/signs/'.$row->faire.'/presenter/zip/'.basename($filename).'" target="_blank">'.basename($filename).'</a>';?>
                      </div>
                      <div class="col-sm-4">
                        <?php echo date("m/d/Y H:i",filemtime($filename));?>
                      </div>
                    </div>
                    <?php
                  }
                } else{
                  echo 'No Zip files found';
                }
                ?>
              </div>
              <div class="col-sm-2  right-border">
                <input style="text-align:center"  name="zipCreate" value="Generate Presesnter Signs" class="button button-large button-primary" onClick="createPDF('<?php echo $row->faire;?>','presenter')" /><br/>
                <small>This needs to be done before you create the Zip file</small>
              </div>
              <div class="col-sm-5">
                <input type="hidden" id="zipFiles" value="<?php echo $row->faire;?>" />
                <div class="row">
                  <div class="col-sm-4">
                    <b>Grouping:</b><br/>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="area" checked> By Area<br>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="subarea"> By Subarea<br>
                    <input type="radio" name="<?php echo $row->faire;?>seltype" value="faire"> By Faire<br>
                  </div>
                  <div class="col-sm-8">
                    <b>Status:</b><br/>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="accepted" checked> Accepted Only<br>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="accAndProp"> Accepted and Proposed<br>
                    <input type="radio" name="<?php echo $row->faire;?>selstatus" value="all"> All Status
                  </div>
                </div>
                <br/>
                <input style="text-align:center"  name="zipCreate" value="Re-Create Zip Files" class="button button-large button-primary" onClick="createZip('<?php echo $row->faire;?>', 'presenter')" /><br/>
                <span class="presenter updateMsg"></span>
              </div>
              <div class="col-sm-12">
                <span class="presenter pdfEntList"></span>
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
<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$faire  = '';
$type   = '';
?>
<h2>Faire Signs</h2>
<div class="row">
  <div class="col-md-3">
    <strong>Exhibit Signs</strong>
  </div>
  <div class="col-md-9">
    <form method="post" action="">
      <div class="col-sm-6">
        <select id="printForm" name="printForm">
          <option value=""><?php _e( 'Select a form', 'gravityforms' ); ?></option>
          <?php
          $forms = RGFormsModel::get_forms( null, 'title' );
          foreach ( $forms as $form ) { ?>
            <option value="<?php echo absint( $form->id ) ?>"><?php echo esc_html( $form->title ); ?></option>
          <?php
          }
          ?>
        </select>
      </div>
      <div class="col-sm-6">
        <input type="submit" value="Get Entries for Form" class="button button-large button-primary" />
      </div>
    </form>
  </div>
</div>
<hr style="border-top-color: darkgray">
<div class="row">
  <!-- Table Tags -->
  <div class="col-lg-3">
    <strong>Table Tags</strong>
  </div>
  <div class="col-lg-9">
    <form method="post" action=""> <?php
      $sql = "SELECT * FROM `wp_mf_faire` ORDER BY `wp_mf_faire`.`start_dt` DESC";
      $results = $wpdb->get_results($sql); ?>
      <div class="col-sm-6">
        <select id="tableTag" name="tableTag"> <?php
          foreach($results as $faire){ ?>
            <option value="<?php echo $faire->faire; ?>"><?php echo $faire->faire; ?></option> <?php
          } ?>
        </select>
      </div>
      <div class="col-sm-6">
        <input type="submit" value="Create Table Tags for Faire" class="button button-large button-primary" />
      </div>
    </form>
  </div>
</div>

<hr style="border-top-color: darkgray">
<div class="row">
  <!-- Table Tags -->
  <div class="col-sm-3">
    <strong>Presenter Signs</strong>
  </div>
  <div class="col-sm-9">
    <form method="post" action=""> <?php
      $sql = "SELECT * FROM `wp_mf_faire` ORDER BY `wp_mf_faire`.`start_dt` DESC";
      $results = $wpdb->get_results($sql); ?>
      <div class="col-sm-6">
        <select id="faire" name="faire"> <?php
          foreach($results as $faire){ ?>
            <option value="<?php echo $faire->faire; ?>"><?php echo $faire->faire; ?></option> <?php
          } ?>
        </select>
      </div>
      <div class="col-sm-6">
        <input type="submit" name="presenterSigns" value="Get Presenters" class="button button-large button-primary" />
      </div>
    </form>
  </div>
</div>

<hr style="border-top-color: darkgray">
<div class="row">
  <div class="col-sm-3">
    <strong>Create/Download Zip file for Faires</strong>
  </div>
  <div class="col-sm-9">
    <form method="post" action=""> <?php
      $signDir = get_template_directory().'/signs/';
      $dir     = array_diff(scandir($signDir), array('..', '.'));
      $options = '<option value=""></option>';
      foreach($dir as $dirName){
        if(is_dir($signDir.$dirName)){
          $options .= '<option value="'.$dirName.'">'.$dirName.'</option>';
        }
      }

      ?>
      <div class="col-sm-6">
        <div class="row">
          <div class="col-sm-3">
            <select id="zipFiles" name="zipFiles">
              <?php echo $options;?>
            </select>
          </div>
          <div class="col-sm-4">
            <b>Grouping:</b><br/>
            <input type="radio" name="type" value="subarea" checked> By Subarea<br>
            <input type="radio" name="type" value="area"> By Area<br>
            <input type="radio" name="type" value="faire"> By Faire<br>
          </div>
          <div class="col-sm-5">
            <b>Status:</b><br/>
            <input type="radio" name="status" value="accepted" checked> Accepted Only<br>
            <input type="radio" name="status" value="accAndProp"> Accepted and Proposed<br>
            <input type="radio" name="status" value="all"> All Status
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <input type="submit" name="zipCreate" value="Create" class="button button-large button-primary" />
        <br/>
        <small><i>** Attention: **<br/> Be sure you have ran the process to generate signs before this or your zip file will be EMPTY</i></small>
      </div>
    </form>
  </div>
</div>


<?php

//create and download a zip of exhibit signs
if(isset($_POST['zipCreate']) && isset($_POST['zipFiles'])){
  $type          = $_POST['type'];
  $statusFilter  = $_POST['status'];
  $signDir       = $_POST['zipFiles'];
  $faire         = $signDir;

  echo 'Creating Zip Files grouped by '.$type.'<br/>';
  //create array of subareas
  $sql = "SELECT wp_rg_lead.ID as entry_id, wp_rg_lead.form_id,
        (select value from wp_rg_lead_detail where field_number=303 and wp_rg_lead_detail.lead_id = wp_rg_lead.ID) as entry_status,
        wp_mf_faire_subarea.area_id, wp_mf_faire_area.area, wp_mf_location.subarea_id, wp_mf_faire_subarea.subarea,wp_mf_location.location
        FROM wp_mf_faire, wp_rg_lead
        left outer join wp_mf_location on wp_rg_lead.ID  = wp_mf_location.entry_id
        left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id  = wp_mf_faire_subarea.id
        left outer join wp_mf_faire_area    on wp_mf_faire_subarea.area_id  = wp_mf_faire_area.id
        where faire = '$signDir'
        and wp_rg_lead.status  != 'trash'
        and FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.form_ids)> 0
        and FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.non_public_forms)<= 0";
  $results = $wpdb->get_results($sql);
  $entries = array();

  foreach($results as $row){
    //exclude records based on status filter
    if($statusFilter =='accepted'   && $row->entry_status!='Accepted')  continue;

    if($statusFilter =='accAndProp' && ($row->entry_status!='Accepted' && $row->entry_status!='Proposed')){
      continue;
    }
    $area    = ($row->area    != NULL ? $row->area:'No-Area');
    $subarea = ($row->subarea != NULL ? $row->subarea:'No-subArea');

    //create friendly names for file creation
    $area = str_replace(' ','_',$area);
    $subarea = str_replace(' ','_',$subarea);
    //build array output based on selected type
    if($type=='area') {
      $entries[$area][$row->entry_status][] = $row->entry_id;
    }
    if($type=='subarea') {
      $entries[$area.'-'.$subarea][$row->entry_status][] = $row->entry_id;
    }
    if($type=='faire') {
      $entries['faire'][$row->entry_status][] = $row->entry_id;
    }
  } //end looping thru sql results

  $error = '';

  //build zip files based on selected type
  foreach($entries as $typeKey=>$entType){
     //create zip file
    $zip = new ZipArchive();
    $filepath = get_template_directory()."/signs/".$signDir.'/';
    $filename = $signDir."-".$typeKey."-fairesigns.zip";
    echo 'zip path = '.$filepath.$filename.'<br/>';
    $zip->open($filepath.$filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach($entType as $statusKey=>$status){
      $subPath = $typeKey.'/'.$statusKey.'/';
      foreach($status as $entryID) {
        //write zip file
        $file = $entryID.'.pdf';
        if (file_exists($filepath.$file)) {
          $zip->addFile($filepath.$file,$file);
        }else{
          $error .= 'Missing PDF for ' .$entryID.'<br/>';
        }
      }
    }
    //close zip file
    if (!$zip->status == ZIPARCHIVE::ER_OK)
      echo "Failed to write files to zip\n";
    $zip->close();
    echo '<a href="/wp-content/themes/makerfaire/signs/'.$signDir.'/'.$filename.'" target="_blank">'.$filename.' Download</a><br/>';
  } //end looping thru entry array
  if($error!='')  echo $error;
}//end create zip logic

//presenter signs
if(isset($_POST['presenterSigns'])){
  $type = 'presenterSigns';
  ?>
  <br/><br/>
  Verify the list of entries below match what you were expecting then click the button to start the process creating the signs.<br/>
  <input class="button button-large button-primary" style="text-align:center" value="Generate signs for listed entries" id="processButton"   onClick="printSigns()"/><br/>
  <br/>
  <?php
  $faire = (isset($_POST['faire'])?$_POST['faire']:'BA16');
  $select_query = "SELECT entity.lead_id as entry_id
                    FROM    wp_mf_schedule schedule,
                            wp_mf_entity entity

                    where   schedule.entry_id       = entity.lead_id
                            AND entity.status       = 'Accepted'
                            and schedule.faire      = '" . $faire . "' " .
          " group BY   entity.lead_id";
  echo '<div class="container"><div class="row">';
  $results = $wpdb->get_results($select_query);
  foreach($results as $entry){
    $entry_id = $entry->entry_id;
    ?>
    <div class="col-md-2">
      <a class="fairsign" target="_blank" id="<?php echo $entry_id;?>" href="/wp-content/themes/makerfaire/fpdi/presenterSigns.php?eid=<?php echo $entry_id;?>&faire=<?php echo $faire;?>"><?php echo $entry_id;?></a>
    </div>
    <?php
  }
  echo '</div></div>';
} //end presenter signs

//table tags
if(isset($_POST['tableTag'])){
  $type = 'tabletag';
  $faire = $_POST['tableTag'];
  ?>
  <br/><br/>
  Verify the list of entries below match what you were expecting then click the button to start the process creating the signs.<br/>
  <input class="button button-large button-primary" style="text-align:center" value="Generate signs for listed entries" id="processButton"   onClick="printSigns()"/><br/>
  <br/>
  <?php
  genTableTags($faire);
} //end post tableTag

if(isset($_POST['printForm'])){
  $type    = 'makersigns';
  $form_id = $_POST['printForm'];
  ?>
  <br/><br/>
  Verify the list of entries below match what you were expecting then click the button to start the process creating the signs.<br/>
  <input class="button button-large button-primary" style="text-align:center" value="Generate signs for listed entries" id="processButton"   onClick="printSigns()"/><br/>
  <br/>
  <?php

  $faire = $wpdb->get_var('select faire from wp_mf_faire where FIND_IN_SET ('.$form_id.', wp_mf_faire.form_ids)> 0');

  $sql = "SELECT wp_rg_lead.id as lead_id, wp_rg_lead_detail.value as lead_status "
          . " FROM `wp_rg_lead`, wp_rg_lead_detail"
          . " where status='active' and field_number=303 and lead_id = wp_rg_lead.id"
          . "   and wp_rg_lead_detail.value!='Rejected' and wp_rg_lead_detail.value!='Cancelled'"
          . "   and wp_rg_lead.form_id=".$form_id;

  $results = $wpdb->get_results($sql);
  echo '<div class="container"><div class="row">';
  foreach($results as $entry){
    $entry_id = $entry->lead_id;
    ?>
    <div class="col-md-2">
      <a class="fairsign" target="_blank" id="<?php echo $entry_id;?>" href="/maker-sign/<?php echo $entry_id;?>/<?php echo $faire;?>"><?php echo $entry_id;?></a>
    </div>
    <?php

  }
  echo '</div></div>';
} //end printForm
  ?>
  <script>
    function printSigns(){
      jQuery('#processButton').val("Creating PDF's. . . ");
      var formFaire = '<?php echo $faire;?>';
      jQuery("a.fairsign").each(function(){
        jQuery(this).html('Creating');
        jQuery(this).attr("disabled","disabled");

        jQuery.ajax({
          type: "GET",
          url: "/wp-content/themes/makerfaire/fpdi/<?php echo $type;?>.php",
          data: { eid: jQuery(this).attr('id'), type: 'save', faire: formFaire },
        }).done(function(data) {
          jQuery('#'+data).html(data+ ' Created');
          jQuery('#'+data).attr("href", "/wp-content/themes/makerfaire/<?php echo ($type == 'makersigns'?'signs':$type);?>/"+formFaire+"/"+data+'.pdf');
        });
      });
    }
    function fireEvent(obj,evt){
      var fireOnThis = obj;
      if( document.createEvent ) {
        var evObj = document.createEvent('MouseEvents');
        evObj.initEvent( evt, true, false );
        fireOnThis.dispatchEvent(evObj);
      } else if( document.createEventObject ) {
        fireOnThis.fireEvent('on'+evt);
      }
    }
  </script>
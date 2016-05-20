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
  <div class="col-sm-2">
    <strong>Exhibit Signs</strong>
  </div>
  <div class="col-sm-10">
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
  <div class="col-sm-2">
    <strong>Table Tags</strong>
  </div>
  <div class="col-sm-10">
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
  <div class="col-sm-2">
    <strong>Presenter Signs</strong>
  </div>
  <div class="col-sm-10">
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


<?php
//presenter signs
if(isset($_POST['presenterSigns'])){
  $type = 'presenterSigns';
  ?>
  <br/><br/>
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
}

//table tags
if(isset($_POST['tableTag'])){
  $type = 'tabletag';
  $faire = $_POST['tableTag'];
  ?>
  <br/><br/>
  <input class="button button-large button-primary" style="text-align:center" value="Generate signs for listed entries" id="processButton"   onClick="printSigns()"/><br/>
  <br/>
  <?php
  genTableTags($faire);
}

if(isset($_POST['printForm'])){
  $type = 'makersigns';
  $form_id = $_POST['printForm'];
  ?>
  <br/><br/>
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
      <a class="fairsign" target="_blank" id="<?php echo $entry_id;?>" href="/wp-content/themes/makerfaire/fpdi/makersigns.php?eid=<?php echo $entry_id;?>&faire=<?php echo $faire;?>"><?php echo $entry_id;?></a>
    </div>
    <?php

  }
  echo '</div></div>';
}
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
                  jQuery('#'+data).attr("href", "/wp-content/themes/makerfaire/<?php echo ($type == 'makersigns'?'signs':$type);?>/"+formFaire+"/"+data+'.pdf')
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





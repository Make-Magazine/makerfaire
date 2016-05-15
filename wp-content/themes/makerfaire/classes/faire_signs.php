<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$faire  = '';
$type   = '';
?>
<div class="row">
  <div class="col-sm-6">
    <h2>Print Faire Signs by Form</h2>
    <form method="post" action="">
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
      <input type="submit" value="Get Entries for Form" class="button button-large button-primary" />
    </form>
  </div>

  <!-- Table Tags -->
  <div class="col-sm-6">
    <h2>Create Table Tags</h2>
    <form method="post" action=""> <?php
      $sql = "SELECT * FROM `wp_mf_faire` ORDER BY `wp_mf_faire`.`start_dt` DESC";
      $results = $wpdb->get_results($sql); ?>
      <select id="tableTag" name="tableTag"> <?php
        foreach($results as $faire){ ?>
          <option value="<?php echo $faire->faire; ?>"><?php echo $faire->faire; ?></option> <?php
        } ?>
      </select>
      <input type="submit" value="Create Table Tags for Faire" class="button button-large button-primary" />
      <input type="checkbox" value="yes" name="sendEmail"/>Send me an email with download link
    </form>
  </div>
</div>

<?php
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
                  jQuery('#'+data).attr("href", "/wp-content/themes/makerfaire/signs/"+formFaire+"/"+data+'.pdf')
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





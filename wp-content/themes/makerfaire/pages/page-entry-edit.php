<?php
/*
 * This is the public facing entry page edit view
 */

GFFormDisplay::enqueue_form_scripts( $form, false );
/*  pull Entry field info for edit fields
    151 - $project_title name
     22 - $project_photo
     16 - $project_short
     27 - $project_website
     32 - $project_video
    105 - who would you like listed
    109 - $groupname
    111 - $groupphoto
    110 - $groupbio

 * maker info
 * contains fields for name, email, phone, phone type, twitter, website, photo, bio, makers age, address, role, and gender
 */
  $makerGroupArr = array(
    array(248, 294, 160, 161, 185, 200, 201, 209, 217, 234, 467, 310, 751, 443, 369, 265, 273),
    array(249, 158, 162, 192, 199, 208, 216, 224, 258, 311, 752, 444, 370, 274),
    array(250, 155, 167, 190, 193, 207, 215, 223, 259, 312, 753, 445, 371, 278),
    array(251, 156, 166, 191, 198, 206, 214, 222, 260, 313, 754, 446, 372, 279),
    array(252, 157, 165, 189, 195, 205, 213, 220, 261, 314, 755, 447, 373, 289),
    array(241, 159, 164, 188, 197, 204, 211, 221, 262, 315, 756, 448, 374, 281),
    array(242, 154, 163, 187, 196, 203, 212, 219, 263, 316, 757, 449, 375),
    array(272, 109, 111, 110, 309, 112, 322)
  );

?>
<br/>
<div id="editEntry">
  <div class="gform_wrapper">
    <form method="post"  enctype="multipart/form-data">
      <input type="hidden" name="form_id" value="<?php echo $form_id;?>" />
      <input name="edit_entry_page" type="submit" value="Submit" >
      <h2 class="gsection_title">Project Information</h2>
      <div class="gform_fields top_label form_sublabel_above description_above">
        <?php
        //build the project info section
        foreach(array(151, 22, 16, 27, 32) as $field_id) {
          $field = $fieldData[$field_id];
          displayField($entry, $field, $form);
        }
        ?>
      </div>
      <div class="page-header">
        <?php
        $field = $fieldData[802];
        displayField($entry, $field, $form);
        ?>
      </div>
      <div class="page-header">
        <?php
        $field = $fieldData[105];
        displayField($entry, $field, $form);
        ?>
      </div>
      <?php
      if($isGroup) { ?>
        <h2 class="gsection_title">Group Info</h2>
        <?php
      }else{ ?>
        <h2>Maker(s) Info</h2>
        <?php
      } ?>
        <hr>
        <?php
        foreach($makerGroupArr as $makerArr){
          foreach($makerArr as $field_id){
            //build the maker data section
            if(isset($fieldData[$field_id])){
              $field = $fieldData[$field_id];
              displayField($entry, $field, $form);
            }
          }
        }
      ?>
      <input name="edit_entry_page" type="submit" value="Submit" >
    </form>
  </div>
</div>

<?php
function displayField($entry, $field, $form){
  $form_id = $entry['form_id'];
  $field_id = $field->id;
  ?>
  <div class="gfield" style="margin-top: 16px;margin-bottom: 30px;">
    <?php
    $value   = RGFormsModel::get_lead_field_value( $entry, $field );
    ?>
    <label class='detail-label'><?php echo esc_html( GFCommon::get_label( $field ) );?></label>
    <div><?php echo $field['description'];?></div>
    <?php echo GFCommon::get_field_input( $field, $value, $entry['id'], $form_id, $form );?>
    <?php
    if($field->type=='fileupload' && isset($entry[$field_id]) && $entry[$field_id]!=''){
      echo '<div><img style="width:150px;height:150px;" class="img-responsive dispPhoto thumbnail" src="'.legacy_get_fit_remote_image_url($entry[$field_id],750,500).'" /></div>';
    } ?>
  </div>
  <?php
}

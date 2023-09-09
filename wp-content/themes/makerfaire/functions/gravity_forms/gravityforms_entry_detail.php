<?php

/*
 * This action is fired before the detail is displayed on the entry detail page
 */

add_action("gform_entry_detail_content_before", "mf_entry_detail_head", 10, 2);

/*
 *  Funtion to modify the header on the entry detail page
 */
function mf_entry_detail_head($form, $lead) {
  //get form from entry id in $lead incase the form was changed ($form only represents the original form)
  $form_id = $lead['form_id'];
  $form    = RGFormsModel::get_form_meta($form_id);
  $page_title =
   '<span>'. __( 'Entry #', 'gravityforms' ) . absint( $lead['id'] ).'</span>';
  $page_subtitle =
    '<span class="gf_admin_page_subtitle">'
  . '  <span class="gf_admin_page_formid">ID: '. $form_id . '</span>'
  . '  <span class="gf_admin_page_formname">Form Name: '. $form['title'] .'</span>';
  $statuscount=get_makerfaire_status_counts( $form_id );
  foreach($statuscount as $statuscount){
    $page_subtitle .= '<span class="gf_admin_page_formname">'.  $statuscount['label'].'('.  $statuscount['entries'].')</span>';
  }
  $page_subtitle .= '</span>';

  $page_title = '<span>'.$form['title'].'('.$form_id.'): Entry # '.absint( $lead['id'] ).'</span>';

  //return to entries link
  $outputVar = '';
  if(isset($_GET['filterField'])){
    foreach($_GET['filterField'] as $newValue){
        $outputVar .= '&filterField[]='.$newValue;
    }
  }
  $outputURL = admin_url( 'admin.php' ) . "?page=gf_entries&view=entries&id=".$form['id']  . $outputVar;

  foreach($_GET as $key=>$variable){
    if($key!='view' && $key!='page' && $key!='lid' && $key != 'filterField'){            
      $outputURL .= '&'.$key.'='.$variable;
    }
  }
  
  $outputURL = '<a href="'. $outputURL .'">Return to entries list</a>';

  ?>
  <script>    
    //change page title
    jQuery('.gf_entry_wrap').prepend('<?php echo $page_title;?>');

    //change page subtitle to have status counts
    jQuery('h2.gf_admin_page_title .gf_admin_page_subtitle').html('<?php echo $page_subtitle;?>');

    //add in Return to Entries List link
    jQuery('div.gf_entry_detail_pagination  ').append('<?php echo $outputURL;?>');

  </script>
  <?php
}
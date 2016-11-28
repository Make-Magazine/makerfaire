<?php

// custom MF entries export
add_filter( 'gform_export_menu', 'my_custom_export_menu_item' );
function my_custom_export_menu_item( $menu_items ) {

    $menu_items[] = array(
        'name' => 'mf_custom_export_entries',
        'label' => __( 'MF Export Entries' )
        );

    return $menu_items;
}

// display content for custom menu item when selected
add_action( 'gform_export_page_mf_custom_export_entries', 'mf_custom_export_entries' );
function mf_custom_export_entries() {

  GFExport::page_header();
  ?>
  <div class="dropdown" style="position:absolute">
    <button class="btn btn-default dropdown-toggle" type="button" id="mfexportdata" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      Select Form
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="mfexportdata">
  <?php
    //create a crypt key to pass to entriesExport.php to avoid outside from accessing
    $date  = date('mdY');
    $crypt = crypt($date, AUTH_SALT);
    $forms = RGFormsModel::get_forms( null, 'title' );
    foreach ( $forms as $form ) {
      ?>
      <li><a href="/wp-content/themes/makerfaire/devScripts/entriesExport.php?formID=<?php echo absint( $form->id ).'&auth='.$crypt; ?>"><?php echo esc_html( $form->title ); ?></a></li>

      <?php
    }
    ?>
    </ul>
  </div>


  <?php
  GFExport::page_footer();

}



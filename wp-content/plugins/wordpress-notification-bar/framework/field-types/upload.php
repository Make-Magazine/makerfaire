<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db

echo "<input id='$id' class='" . ( empty( $class ) ? 'regular-text' : $class ) . "' name='{$setting_id}[$id]' type='text' value='" . esc_attr( $options[ $id ] ) . "' />";
echo "<input id='{$id}_upload_image_button' class='button-secondary upload-button' type='button' value='" . __( 'Media Image Library', 'seed_wnb' ) . "' /><br>";

wp_enqueue_script( 'seed_wnb-upload-js', SEED_WNB_PLUGIN_URL . 'framework/field-types/js/upload.js', array(
    'jquery',
    'thickbox',
    'media-upload' 
) );
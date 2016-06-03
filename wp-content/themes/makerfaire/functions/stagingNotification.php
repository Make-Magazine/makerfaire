<?php
function determine_staging(){
  // Display a message if on staging or development
  if ( Jetpack::is_development_mode()){
    echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white;">Development Site</div>';
  }else if(Jetpack::is_staging_site() ) {
    echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white;">Staging Site</div>';
  }

}
add_action('admin_head', 'determine_staging');
add_action('wp_head', 'determine_staging');
add_action('rmt_head', 'determine_staging');
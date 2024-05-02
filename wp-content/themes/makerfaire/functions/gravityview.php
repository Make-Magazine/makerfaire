<?php
/* check if logged in user has access to view this entry
    If the user has access to this entry return false
       This will override the GV logic to verify user */
function check_entry_display( $check_entry_display, $entry ) {
  global $current_user; global $wpdb;
  //check if entry was created by logged on user and if they have the correct role set
  if ( current_user_can( 'mat_view_created_entries') ) {
    if($entry['created_by']==$current_user->ID) return false;
  }

  //check if current users email
  require_once( get_template_directory().'/models/maker.php' );

  //instantiate the model
  $maker   = new maker($current_user->user_email);
  $query = "SELECT count(*)
            FROM   wp_mf_maker_to_entity
            left  outer join wp_mf_entity
                  on wp_mf_entity.lead_id = entity_id
            WHERE maker_id ='".$maker->maker_id."'
            AND   wp_mf_maker_to_entity.entity_id = ".$entry['id']."
            AND   status != 'trash'";
  $count = $wpdb->get_var($query);
  if($count > 0) return false;
  return $check_entry_display;
}
//add_filter('gravityview/common/get_entry/check_entry_display', 'check_entry_display', 10, 2 );


add_filter('gravityview-inline-edit/edit-mode', 'modify_inline_display', 10, 1 );
function modify_inline_display( $mode = '' ) {
  return 'inline';
}

//hide the Approve/Reject Entry column in entry list
add_filter('gravityview/approve_entries/hide-if-no-connections', '__return_true');


add_filter( 'gravityview_edit_entry_title', 'gk_change_edit_entry_title', 10, 2);
function gk_change_edit_entry_title( $previous_text = 'Edit Entry', $GV_object = '') {
  if($GV_object->view_id=='642359'){
    return 'Manage Photos';
  }else{
    return $previous_text;
  }
}
//add_action('gravityview/template/before', 'set_gravityview_inline_edit_cookies');

/**
 * Set cookies toggling Inline Edit to on by default. Requires GravityView 2.0.
 *
 * @uses gravityview_get_current_view_data
 * @uses setcookie
 *
 * @return void
 */
function set_gravityview_inline_edit_cookies( \GV\Template_Context $gravityview = null ) {
	wp_print_scripts( 'jquery-cookie' );
?>
	<script>
		jQuery( window ).on( 'load', function() {
			if( jQuery.cookie ) {
			<?php
				printf( "jQuery.cookie( 'gv-inline-edit-view-%d', 'enabled', { path: '%s', domain: '%s' } );", $gravityview->view->ID, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
			?>
			} else {
				console.error("Could not set cookie for inline-edit.");
			}

		}); 
	</script>
<?php
}

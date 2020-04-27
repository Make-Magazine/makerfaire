<?php
wp_nonce_field( 'essb_advancedoptions_setup', 'essb_advancedoptions_token' );

function essb_advancedopts_generate_scripts() {
	$opts_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'essb_options';
	
	$code = 'var essb_advancedopts_ajaxurl = "'.esc_url(admin_url ('admin-ajax.php')).'",
		essb_advancedopts_reloadurl = "'.esc_url(admin_url ('admin.php?page='.$opts_page)).'";';
	
	return $code;
}

wp_add_inline_script('essb-admin5', essb_advancedopts_generate_scripts());

?>

<div class="advancedoptions-modal"></div>
<div class="essb-helper-popup" id="essb-advancedoptions" data-width="1200" data-height="auto">
	<div class="essb-helper-popup-title">
		<span id="advancedOptions-title"></span>
		<div class="actions">
			<a href="#" class="advancedoptions-close" title="Close the window"><i class="ti-close"></i> <span>CLOSE</span></a>
			<a href="#" class="advancedoptions-save" title="Save"><i class="ti-check"></i> <span>SAVE</span></a>
		</div>
		
	</div>
	<div class="essb-helper-popup-content essb-options">
		<div class="essb-advanced-options-form" id="essb-advanced-options-form"></div>
	</div>
</div>


<div id="advancedoptions-preloader">
  <div id="advancedoptions-loader"></div>
</div>
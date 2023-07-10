<?php 
$support_locked = !ESSBActivationManager::isActivated() ? 'support-locked' : '';
?>
<!--  notifications -->
<script src="<?php echo esc_url(ESSB3_PLUGIN_URL); ?>/assets/admin/jquery.toast.js"></script> 
<link rel="stylesheet" type="text/css" href="<?php echo esc_url(ESSB3_PLUGIN_URL); ?>/assets/admin/jquery.toast.css">
<!-- notifications -->

<div class="about-header">
	<div class="logo">
    	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 44 44" enable-background="new 0 0 44 44" width="32px" height="32px">
          <g>
            <path d="m15,19c-1.1,0-2,0.9-2,2s0.9,2 2,2c0.6,0 1.2-0.3 1.6-0.8 0.2-0.2 0.4-0.6 0.4-1.2 0-0.3-0.1-0.5-0.2-0.8-0.3-0.7-1-1.2-1.8-1.2z" fill="#FFFFFF"/>
            <path d="m26,31c1.1,0 2-0.9 2-2s-0.9-2-2-2c-0.6,0-1.2,0.3-1.6,0.8-0.2,0.2-0.4,0.6-0.4,1.2 0,1.1 0.9,2 2,2z" fill="#FFFFFF"/>
            <path d="m22,0c-12.2,0-22,9.8-22,22s9.8,22 22,22 22-9.8 22-22-9.8-22-22-22zm7,21c-1.6,0-3.1-0.7-4.2-1.7-0.1-0.1-0.4-0.2-0.5-0.1l-3,1.3c-0.2,0.1-0.3,0.3-0.3,0.5 0,0.5-0.1,1-0.2,1.4 0,0.2 0,0.4 0.2,0.5l1.3,.9c0.2,0.1 0.4,0.1 0.6,0 0.9-0.5 2-0.9 3.1-0.9 3.3,0 6,2.7 6,6s-2.7,6-6,6-6-2.7-6-6c0-0.5 0.1-0.9 0.2-1.4 0-0.2 0-0.4-0.2-0.5l-1.3-1c-0.2-0.1-0.4-0.1-0.6,0-0.9,0.6-2,0.9-3.1,0.9-3.3,0-6-2.7-6-6s2.7-6 6-6c1.6,0 3.1,0.7 4.2,1.7 0.1,0.1 0.4,0.2 0.5,0.1l3-1.3c0.2-0.1 0.3-0.3 0.3-0.5 0,0 0,0 0-0.1 0-3.3 2.7-6 6-6s6,2.7 6,6-2.7,6.2-6,6.2z" fill="#FFFFFF"/>
            <path d="m29,13c-1.1,0-2,0.9-2,2 0,0.3 0.1,0.5 0.2,0.8 0.3,0.7 1,1.2 1.8,1.2 1.1,0 2-0.9 2-2s-0.9-2-2-2z" fill="#FFFFFF"/>
          </g>
    	</svg>
	</div>
	<div class="logo-name">
		Easy Social Share Buttons for WordPress
	</div>

	<div class="usefull-links">
		<a href="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_addons')); ?>">Add-Ons</a>
		<a href="https://docs.socialsharingplugin.com" target="_blank">Documentation</a>
		<a href="https://my.socialsharingplugin.com" target="_blank" class="<?php echo esc_attr($support_locked);?>">Get Support</a>
	</div>
</div>

<?php 
if (!ESSBActivationManager::isActivated()) {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";

	if ($('.support-locked').length) {
		$('.support-locked').on('click', function(e) {
			e.preventDefault();
			swal('Support Locked', 'Customer support is available for direct plugin license owners only. If you already have a direct plugin license code you can visit our support system. Note that customer support is not available for versions of the plugin bundled inside a WordPress theme.', "error");
		});
	}
});

</script>
<?php } ?>
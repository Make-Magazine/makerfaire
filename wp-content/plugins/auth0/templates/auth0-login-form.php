<?php
$auth0_options = WP_Auth0_Options::Instance();
$wle           = $auth0_options->get('wordpress_login_enabled');
?>
<div id="form-signin-wrapper" class="auth0-login">
	<div class="form-signin">
		<div id="<?php echo esc_attr(WPA0_AUTH0_LOGIN_FORM_ID); ?>"></div>
		<?php if ('link' === $wle && function_exists('login_header')) : ?>
			<div id="extra-options">
				<a href="<?php echo esc_url(wp_login_url()); ?>?wle">
					<?php esc_html_e('Login with WordPress username', 'wp-auth0'); ?>
				</a>
			</div>
		<?php endif ?>
	</div>
</div>

<style type="text/css">
	<?php echo esc_html(apply_filters('auth0_login_css', '')); ?>
</style>

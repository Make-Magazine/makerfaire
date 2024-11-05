<?php

/**
 * Contains class WP_Auth0_Email_Verification
 *
 * @package WP-Auth0
 *
 * @since 3.5.0
 */

/**
 * Class WP_Auth0_Email_Verification.
 */
class WP_Auth0_Email_Verification
{

	const RESEND_NONCE_ACTION = 'auth0_resend_verification_email';

	/**
	 * Stop the login process and show email verification prompt.
	 *
	 * @param object $userinfo - User profile object returned from Auth0.
	 */
	public static function render_die($userinfo)
	{
		$user_id = isset($userinfo->user_id) ? $userinfo->user_id : $userinfo->sub;

		$html = wp_kses(sprintf('<p>%s</p>', esc_html__('This site requires a verified email address.', 'wp-auth0')), ['p' => []]);

		// Only provide resend verification link for DB connection users.
		if (0 === strpos($user_id, 'auth0|')) {
			$html .= wp_kses(sprintf(
				'<p><a id="js-a0-resend-verification" href="#">%s</a></p>
				<p><a href="%s?%d">%s</a></p>
				<script>var WPAuth0EmailVerification={ajaxUrl:"%s",sub:"%s",nonce:"%s",e_msg:"%s",s_msg:"%s"}</script>
				<script src="%s"></script>
				<script src="%s"></script>',
				esc_html__('Resend verification email.', 'wp-auth0'),
				wp_login_url(),
				time(),
				esc_html__('← Login', 'wp-auth0'),
				esc_url(admin_url('admin-ajax.php')),
				esc_js($user_id),
				esc_js(wp_create_nonce(self::RESEND_NONCE_ACTION)),
				esc_js(esc_html__('Something went wrong; please login and try again.', 'wp-auth0')),
				esc_js(esc_html__('Email successfully re-sent to ', 'wp-auth0') . $userinfo->email),
				'//code.jquery.com/jquery-1.12.4.js',
				WPA0_PLUGIN_URL . 'assets/js/die-with-verify-email.js?ver=' . WPA0_VERSION
			), ['p', 'a' => ['id' => true, 'href' => true], 'script' => ['src' => true]]);
		}

		$html = apply_filters('auth0_verify_email_page', $html, $userinfo, '');

		wp_die(wp_kses($html, ['p' => [], 'a' => ['id' => true, 'href' => true], 'script' => ['src' => true]]));
	}
}

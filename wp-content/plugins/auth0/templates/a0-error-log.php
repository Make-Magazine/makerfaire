<?php

/**
 * Displays the error log settings page.
 *
 * @package WP-Auth0
 *
 * @see WP_Auth0_ErrorLog::render_settings_page()
 */

$error_log = new WP_Auth0_ErrorLog();
$errors    = $error_log->get();
?>
<div class="a0-wrap settings wrap">

	<h1><?php esc_html_e('Error Log', 'wp-auth0'); ?></h1>
	<?php if (!empty($errors)) : ?>
		<div class="a0-buttons">
			<form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post" class="js-a0-confirm-submit" data-confirm-msg="<?php esc_html_e('This will delete all error log entries. Proceed?', 'wp-auth0'); ?>">
				<?php wp_nonce_field(WP_Auth0_ErrorLog::CLEAR_LOG_NONCE); ?>
				<input type="hidden" name="action" value="wpauth0_clear_error_log">
				<input type="submit" name="submit" class="button button-primary" value="Clear Log">
			</form>
		</div>
	<?php endif; ?>

	<table class="widefat top-margin">
		<thead>
			<tr>
				<th><?php esc_html_e('Date', 'wp-auth0'); ?></th>
				<th><?php esc_html_e('Section', 'wp-auth0'); ?></th>
				<th><?php esc_html_e('Error code', 'wp-auth0'); ?></th>
				<th><?php esc_html_e('Message', 'wp-auth0'); ?></th>
				<th><?php esc_html_e('Count', 'wp-auth0'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php if (empty($errors)) : ?>
				<tr>
					<td class="message" colspan="5"><?php esc_html_e('No errors', 'wp-auth0'); ?></td>
				</tr>
			<?php else : ?>
				<?php
				foreach ($errors as $item) :
				?>
					<tr>
						<td><?php esc_html(date('m/d/Y H:i:s', $item['date'])); ?></td>
						<td><?php esc_html(sanitize_text_field($item['section'])); ?></td>
						<td><?php esc_html(sanitize_text_field($item['code'])); ?></td>
						<td><?php esc_html(sanitize_text_field($item['message'])); ?></td>
						<td><?php esc_html(isset($item['count']) ? intval($item['count']) : 1); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

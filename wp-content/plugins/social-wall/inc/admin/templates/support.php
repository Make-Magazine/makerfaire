<h3><?php _e( 'Need help?', $text_domain ); ?></h3>

<p><?php echo sbsw_admin_icon( 'life-ring', 'sbspf_small_svg' ); ?>&nbsp; <?php _e( 'Check out our ', $text_domain ); ?><a href="<?php echo esc_url( $setup_url ); ?>" target="_blank"><?php _e( 'setup directions', $text_domain ); ?></a> <?php _e( 'for a step-by-step guide on how to setup and use the plugin', $text_domain ); ?>.</p>

<p><?php echo sbsw_admin_icon( 'envelope', 'sbspf_small_svg' ); ?>&nbsp; <?php _e( 'Have a problem? Submit a ', $text_domain ); ?><a href="https://smashballoon.com/social-wall/support/" target="_blank"><?php _e( 'support ticket', $text_domain ); ?></a> <?php _e( 'on our website', $text_domain ); ?>.  <?php _e( 'Please include your <b>System Info</b> below with all support requests.', $text_domain  ); ?></p>

<br />
<h3><?php _e('System Info', $text_domain ); ?> &nbsp; <span style="color: #666; font-size: 11px; font-weight: normal;"><?php _e( 'Click the text below to select all', $text_domain ); ?></span></h3>

<textarea readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)." class="sbsw-system-info">
## SITE/SERVER INFO: ##
Plugin Version:           <?php echo $plugin_name . ' v' . $plugin_version. "\n"; ?>
Site URL:                 <?php echo site_url() . "\n"; ?>
Home URL:                 <?php echo home_url() . "\n"; ?>
WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>
PHP allow_url_fopen:      <?php echo ini_get( 'allow_url_fopen' ) ? "Yes" . "\n" : "No" . "\n"; ?>
PHP cURL:                 <?php echo is_callable('curl_init') ? "Yes" . "\n" : "No" . "\n"; ?>
JSON:                     <?php echo function_exists("json_decode") ? "Yes" . "\n" : "No" . "\n" ?>
SSL Stream:               <?php echo in_array('https', stream_get_wrappers()) ? "Yes" . "\n" : "No" . "\n" //extension=php_openssl.dll in php.ini ?>

## ACTIVE PLUGINS: ##
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( in_array( $plugin_path, $active_plugins ) ) {
		echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
	}
}
?>

## OPTIONS: ##
<?php
$options = get_option( $this->get_option_name(), array() );
foreach ( $options as $key => $val ) {
	if ( is_array( $val ) ) {
		foreach ( $val as $key2 => $val2 ) {
			if ( is_array( $val2 ) ) {
				foreach ( $val2 as $key3 => $val3 ) {
					$label = $key3 . ':';
					$value = isset( $val3 ) ? esc_attr( $val3 ) : 'unset';
					echo str_pad( $label, 24 ) . $value ."\n";
				}
			} else {
				$label = $key2 . ':';
				$value = isset( $val2 ) ? esc_attr( $val2 ) : 'unset';
				echo str_pad( $label, 24 ) . $value ."\n";
			}
		}
	} else {
		$label = $key . ':';
		$value = isset( $val ) ? esc_attr( $val ) : 'unset';
		echo str_pad( $label, 24 ) . $value ."\n";
	}

}
?>

## CRON EVENTS: ##
<?php
$cron = _get_cron_array();
foreach ( $cron as $key => $data ) {
	$is_target = false;
	foreach ( $data as $key2 => $val ) {
		if ( strpos( $key2, 'sbsw' ) !== false ) {
			$is_target = true;
			echo $key2;
			echo "\n";
		}
	}
	if ( $is_target) {
		echo date( "Y-m-d H:i:s", $key );
		echo "\n";
		echo 'Next Scheduled: ' . ((int)$key - time())/60 . ' minutes';
		echo "\n\n";
	}
}
?>
## CRON CACHE: ##
<?php $cron_report = get_option( 'sbsw_cron_report', array() );
if ( ! empty( $cron_report ) ) {
	var_export( $cron_report );
}
echo "\n";
?>

## ERRORS: ##
<?php
$errors = array();
if ( ! empty( $errors ) ) :
	foreach ( $errors as $type => $error ) :
		echo $type . ': ' . str_replace( array( '<p>', '<b>', '</p>', '</b>' ), ' ', $error[1] ) . "\n";
	endforeach;
endif;
?>

## CONNECTED ACCOUNTS: ##

# INSTAGRAM: #
<?php
$sbi_options = get_option( 'sb_instagram_settings', array() );
$con_accounts = isset( $sbi_options['connected_accounts'] ) ? $sbi_options['connected_accounts'] : array();
$business_accounts = array();
$basic_accounts = array();
if ( ! empty( $con_accounts ) ) {
    foreach ( $con_accounts as $account ) {
        $type = isset( $account['type'] ) ? $account['type'] : 'personal';

        if ( $type === 'business' ) {
            $business_accounts[] = $account;
        } elseif ( $type === 'basic' ) {
            $basic_accounts[] = $account;
        }
        echo '*' . $account['user_id'] . '*' . "\n";
        var_export( $account );
        echo "\n";
    }
}
?>

# FACEBOOK: #
<?php
$cff_accounts =  method_exists( 'CustomFacebookFeed\CFF_Utils','cff_get_connected_accounts' ) ? CustomFacebookFeed\CFF_Utils::cff_get_connected_accounts() : get_option( 'cff_connected_accounts', array() );
if ( is_object( $cff_accounts ) && ! empty( $cff_accounts ) ) {
	foreach ( $cff_accounts as $id => $account ) {
		echo '*' . $id . '*' . "\n";
		var_export( $account );
		echo "\n";
	}
} else {
	var_export( $cff_accounts );
}
?>

# TWITTER: #
<?php
$cff_options = get_option( 'ctf_options' );
$consumer_key = ! empty( $cff_options['consumer_key'] ) && $cff_options['have_own_tokens'] ? $cff_options['consumer_key'] : '';
$consumer_secret = ! empty( $cff_options['consumer_secret'] ) && $cff_options['have_own_tokens'] ? $cff_options['consumer_secret'] : '';
$request_settings = array(
	'consumer_key' => $consumer_key,
	'consumer_secret' => $consumer_secret,
	'access_token' => $cff_options['access_token'],
	'access_token_secret' => $cff_options['access_token_secret']
);
foreach ( $request_settings as $key => $value ) {
	echo $key . ': ' . $value ."\n";
}
?>

# YOUTUBE: #
<?php
$sby_con_accounts = function_exists( 'sby_get_first_connected_account' ) ? sby_get_first_connected_account() : array();
if ( ! empty( $sby_con_accounts ) ) {
    var_export( $sby_con_accounts );
}
?>
</textarea>

<br /><br />
<h3 style="padding-top: 10px;"><?php _e( 'Shortcode Options', $text_domain ); ?></h3>
<p><?php _e( "If you'd like to display multiple social walls then you can set different settings directly in the shortcode like so:", $text_domain ); ?></p>

<textarea readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)." class="sbsw-multiple-shortcode">
[social-wall num=10 layout="carousel" carouselcols="1"]
	[instagram-feed]
	[custom-facebook-feed]
	[custom-twitter-feeds]
	[youtube-feed]
[/social-wall]</textarea>

<p><?php _e( "See the table below for a full list of available shortcode option you can add to your wall:", $text_domain ); ?></p>

<table class="sbspf_shortcode_table">
	<tbody>
	<tr valign="top">
		<th scope="row"><?php _e( 'Shortcode option', $text_domain ); ?></th>
		<th scope="row"><?php _e( 'Description', $text_domain ); ?></th>
		<th scope="row"><?php _e( 'Example', $text_domain ); ?></th>
	</tr>

	<?php foreach ( $this->display_your_feed_sections as $display_your_feed_section ) : ?>
		<tr class="sbspf_table_header"><td colspan=3><?php echo $display_your_feed_section['label'] ?></td></tr>
		<?php foreach ( $display_your_feed_section['settings'] as $setting ) : ?>
			<tr>
				<td><?php echo $setting['key']; ?></td>
				<td><?php echo $setting['description']; ?></td>
				<td><code>[<?php echo $slug; ?> <?php echo $setting['key']; ?>="<?php echo str_replace('"', '', $setting['example'] ); ?>"]</code></td>
			</tr>
		<?php endforeach; ?>

	<?php endforeach; ?>

	</tbody>
</table>
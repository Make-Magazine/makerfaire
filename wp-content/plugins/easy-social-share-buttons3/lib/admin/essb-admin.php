<?php

// since version 5 we store by default last 10 saved setups of plugin
define('ESSB5_SETTINGS_ROLLBACK', 'essb-settings-history');

class ESSBAdminControler {

	private static $instance = null;
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	function __construct() {
		global $pagenow;
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';

		$deactivate_appscreo = essb_option_bool_value('deactivate_appscreo');

		// determine access level to setup
		$essb_settings_access = essb_option_value('essb_access');
		if ($essb_settings_access == '') {
			$essb_settings_access = 'manage_options';
		}

		add_action ( 'admin_menu', 	array ($this, 'register_menu' ) );
		add_action ( 'admin_enqueue_scripts', array ($this, 'register_admin_assets' ), 99 );

		$hook = (defined ( 'WP_NETWORK_ADMIN' ) && WP_NETWORK_ADMIN) ? 'network_admin_menu' : 'admin_menu';

		if (current_user_can($essb_settings_access) && strpos($page, 'essb_') !== false ) {
			add_action ( $hook, array ($this, 'handle_save_settings' ) );
		}

		if (is_admin() && current_user_can($essb_settings_access)) {
			add_action ( 'wp_ajax_essb_settings_save', array ($this, 'actions_download_settings' ) );

			// loading the new style library actions and styles
			include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/styles-library/styles-actions.php');
			ESSBStyleLibraryManager::get_instance();

			if (essb_admin_advanced_options()) {
				include_once ESSB3_PLUGIN_ROOT . 'lib/admin/advanced-options/advancedoptions-actions.php';
				ESSBAdvancedOptions::get_instance();
			}
		}

		// admin meta boxes
		/**
		 * Since version 6.2 it is possible to modify the access to the plugin metabox editing options on the
		 * editor. In case the limitation is set in Administrative settings the metaboxes will not be loaded
		 * and so will happen with the save action
		 */
		$can_use_meta = true;
			
		if (essb_option_bool_value('limit_editor_fields') && function_exists('essb_editor_capability_can')) {
			$can_use_meta = essb_editor_capability_can();
		}
		if ($can_use_meta) {
			add_action('add_meta_boxes', array ($this, 'handle_essb_metabox' ) );
			add_action('save_post',  array ($this, 'handle_essb_save_metabox'));
		}

		if (!$deactivate_appscreo) {
			add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		}


		if (ESSB3_ADDONS_ACTIVE) {
			include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons-helper4.php');
			ESSBAddonsHelper::get_instance();
		}

 		if(isset($pagenow) && $pagenow == 'plugins.php' && !ESSBActivationManager::isActivated()){
 			if (!ESSBActivationManager::isThemeIntegrated()) {
 				add_action('admin_notices', array($this, 'add_plugins_page_notices'));
 			}
 		}

	}

	public function add_plugins_page_notices(){
		$plugins = get_plugins();

		foreach($plugins as $plugin_id => $plugin){

			$slug = dirname($plugin_id);
			if(empty($slug)) continue;
			if($slug !== 'easy-social-share-buttons3' && $slug !== 'easy-social-share-buttons3-ver4') continue;

			if(!ESSBActivationManager::isActivated()){ //activate for updates and support
				add_action( "after_plugin_row_" . $plugin_id, array($this, 'show_purchase_notice'), 10, 3);
			}
		}
	}

	public function show_purchase_notice(){
		$wp_list_table = _get_list_table('WP_Plugins_List_Table');
		$activate_url = admin_url('admin.php?page=essb_redirect_update&tab=update');

		$new_version_code = '';
		if (ESSBActivationManager::existNewVersion()) {
			$new_version_code = ' <b>New version '.ESSBActivationManager::getLatestVersion().' is available.</b>';
		}

		?>
	        <tr class="plugin-update-tr essb-plugin-update"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="plugin-update colspanchange">
	            <div class="update-message notice inline notice-warning notice-alt">
	            <p>Activate Easy Social Share Buttons for WordPress to <a href="<?php echo esc_url($activate_url); ?>"><b>unlock automatic plugin updates</b></a>. You can <a href="<?php echo esc_url($activate_url); ?>"><b>Enter an existing licence key</b></a> or <a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo&license=regular&open_purchase_for_item_id=6394476&purchasable=source" target="_blank"><b>Purchase a licence key</b></a>. <?php echo esc_html($new_version_code); ?></p>
	            </div>
	        </tr>
	        <?php
		}

	/**
	 * Add news dashboard widget
	 *
	 * @since 3.6
	 */
	public function add_dashboard_widget() {
		// Create the widget
		wp_add_dashboard_widget( 'appscreo_news', apply_filters( 'appscreo_dashboard_widget_title', __( 'AppsCreo Overview', 'essb' ) ), array( $this, 'display_news_dashboard_widget' ) );

		// Make sure our widget is on top off all others
		global $wp_meta_boxes;

		// Get the regular dashboard widgets array
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

		// Backup and delete our new dashboard widget from the end of the array
		$avada_widget_backup = array( 'appscreo_news' => $normal_dashboard['appscreo_news'] );
		unset( $normal_dashboard['appscreo_news'] );

		// Merge the two arrays together so our widget is at the beginning
		$sorted_dashboard = array_merge( $avada_widget_backup, $normal_dashboard );

		// Save the sorted array back into the original metaboxes
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	public function display_news_dashboard_widget() {
		?>

		<style type="text/css">

			.essb-admindash-overview {
				display: table;
				box-shadow: 0 5px 8px rgba(0,0,0,.05);
				-webkit-box-shadow: 0 5px 8px rgba(0,0,0,.05);
				margin: 0 -12px 8px;
				padding: 0 12px 12px;
				width: 100%;
}

	        .essb-admindash-overview-logo {
	        background-color: #2B6A94; background: linear-gradient(to right, #2b6a94 1%,#23577a 100%); width: 42px; height: 42px;
	        display: table-cell;
	        border-radius: 3px;
	        position: relative;
			 }

			 .essb-admindash-overview-logo .essb-logo {
			 	position: absolute; display: block; width: 28px; height: 28px; top: 0; left: 0; margin:7px; background-size: 28px; }

			 .essb-admindash-overview-ver { display: table-cell; padding: 0 10px; vertical-align: middle; font-size: 0.9em; }

			 .essb-admindash-row .essb-admindash-heading {
			 		font-weight: 700;
			 	border-bottom: 1px solid #eee;
			 	margin: 0 -12px;
			 	padding: 6px 12px;
}
.essb-admindash-footer {
	border-top: 1px solid #eee;
	margin: 0 -12px -12px;
	padding: 12px;
	margin-top: 20px;
}

.essb-admindash-footer ul { margin: 0; padding: 0; }
.essb-admindash-footer ul li {
	padding: 0 5px;
	margin: 0;
	border-left: 1px solid #eee;
	display: inline-block;
}

.essb-admindash-footer ul li:first-child {
	padding-left: 0;
	border-left: 0;
}
	    </style>

		<div class="essb-admin-dashboard-widget">
			<div class="essb-admindash-overview">
				<div class="essb-admindash-overview-logo">
					<div class="essb-logo"></div>
				</div>
				<div class="essb-admindash-overview-ver">
					Easy Social Share Buttons for WordPress v<?php echo ESSB3_VERSION; ?><br/>
					Activation Status: <?php
					if (ESSBActivationManager::isActivated()) {
						echo '<span style="color:#71BA51; font-weight: bold;">Activated</span>';
					}
					else if (ESSBActivationManager::isThemeIntegrated()) {
						echo '<span style="color:#FD5B03; font-weight: bold;">Theme Integrated</span>';
					}
					else {
						echo '<span style="color:#CF000F; font-weight: bold;">Not Activated</span>';
					}
					?>
				</div>
			</div>

			<?php
			if (!ESSBActivationManager::isActivated()) {
				echo '<div class="essb-admindash-row">';
				$activate_url = admin_url('admin.php?page=essb_redirect_update&tab=update');

				if (!ESSBActivationManager::isThemeIntegrated()) {
					echo '<p>Activate Easy Social Share Buttons for WordPress to <a href="https://socialsharingplugin.com/direct-customer-benefits/" target="_blank"><b>unlock automatic plugin updates and direct customer benefits</b></a>. You can <a href="'.$activate_url.'"><b>Enter an existing licence key</b></a> or <a href="https://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo&license=regular&open_purchase_for_item_id=6394476&purchasable=source" target="_blank"><b>Purchase a licence key</b></a>.</p>';
				}
				else {
					echo '<p>Your copy of Easy Social Share Buttons for WordPress is theme integrated. To <a href="https://socialsharingplugin.com/direct-customer-benefits/" target="_blank"><b>unlock automatic plugin updates and direct customer benefits</b></a> you can <a href="'.$activate_url.'"><b>Enter an existing Easy Social Share Buttons for WordPress licence key</b></a> or <a href="https://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo&license=regular&open_purchase_for_item_id=6394476&purchasable=source" target="_blank"><b>Purchase a new Easy Social Share Buttons for WordPress licence key</b></a>.</p>';
				}
				echo '</div>';
			}
			?>

			<div class="essb-admindash-row">
				<div class="essb-admindash-heading" style="margin-bottom: 10px;">News & Updates</div>
			</div>

			<?php
			$feeds = array(
					'first' => array(
							'link'         => 'https://appscreo.com/',
							'url'          => 'https://appscreo.com/feed/',
							'title'        => __( 'AppsCreo News', 'essb' ),
							'items'        => 4,
							'show_summary' => 0,
							'show_author'  => 0,
							'show_date'    => 1,
					),
			);


			wp_dashboard_primary_output( 'appscreo_news', $feeds );
			?>
			<div class="essb-admindash-row" style="margin-top: 20px;">
				<div class="essb-admindash-heading" style="margin-bottom: 10px; border-bottom:0;">Subscribe for News & Updates</div>
			</div>
			<!-- Begin MailChimp Signup Form -->
<div id="mc_embed_signup">
<form action="https://appscreo.us13.list-manage.com/subscribe/post?u=a1d01670c240536f6a70e7778&amp;id=c896311986" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">

	<input style="width: 62%; display: inline-block;" type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px; width:60%; display: inline-block;" aria-hidden="true"><input type="text" name="b_a1d01670c240536f6a70e7778_c896311986" tabindex="-1" value=""></div>
    <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button button-primary" style="display: inline-block; width: 35%;">
    </div>
</form>
</div>

<!--End mc_embed_signup-->
			<div class="essb-admindash-footer">
				<ul>
				<li class=""><a href="https://socialsharingplugin.com/version-changes/" target="_blank">What's New <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'essb' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				<li class=""><a href="https://docs.socialsharingplugin.com" target="_blank">Docs <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'essb' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				<li class=""><a href="http://support.creoworx.com" target="_blank">Get Support <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'essb' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				<li class=""><a href="http://go.appscreo.com/portfolio" target="_blank" style="color:#FD5B03;">Our Products <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'essb' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				</ul>
			</div>
		</div>

		<?php
	}

	public function handle_essb_metabox() {
		$display_in_types = essb_option_value('display_in_types');
		$turnoff_essb_optimize_box = essb_option_bool_value('turnoff_essb_optimize_box');
		$turnoff_essb_stats_box = essb_option_bool_value('turnoff_essb_stats_box');
		$turnoff_essb_advanced_box = essb_option_bool_value('turnoff_essb_advanced_box');
		$turnoff_essb_main_box = essb_option_bool_value('turnoff_essb_main_box');

		$stats_are_activated = essb_option_bool_value('stats_active');
		if (!$stats_are_activated) {
			$turnoff_essb_stats_box = true;
		}

		if (!is_array($display_in_types)) {
			$display_in_types = array();
		}

		// get post types
		$pts	 = get_post_types( array('show_ui' => true, '_builtin' => true) );
		$cpts	 = get_post_types( array('show_ui' => true, '_builtin' => false) );
		foreach ( $pts as $pt ) {
			if ((defined('ESSB3_SSO_ACTIVE') && !$turnoff_essb_optimize_box) || in_array($pt, $display_in_types)) {
				add_meta_box('essb_metabox_optmize', __('Easy Social Share Buttons: Share Options', 'essb'), 'essb_register_settings_metabox_optimize', $pt, 'normal', 'high');
			}

			if (in_array($pt, $display_in_types)) {

				if (!$turnoff_essb_main_box) {
					add_meta_box('essb_metabox', __('Easy Social Share Buttons', 'essb'), 'essb_register_settings_metabox_onoff', $pt, 'side', 'high');
				}

				if (!$turnoff_essb_advanced_box) {
					add_meta_box('essb_metabox_visual', __('Easy Social Share Buttons: Visual Customization', 'essb'), 'essb_register_settings_metabox_visual', $pt, 'normal', 'high');
				}

				if (!$turnoff_essb_stats_box) {
					add_meta_box('essb_metabox_stats', __('Easy Social Share Buttons: Stats', 'essb'), 'essb_register_settings_metabox_stats', $pt, 'normal', 'core');
				}


			}

		}

		foreach ( $cpts as $cpt ) {

			if ((defined('ESSB3_SSO_ACTIVE') && !$turnoff_essb_optimize_box) || in_array($cpt, $display_in_types)) {
				add_meta_box('essb_metabox_optmize', __('Easy Social Share Buttons: Social Share Optimization', 'essb'), 'essb_register_settings_metabox_optimize', $cpt, 'normal', 'high');
			}

			if (in_array($cpt, $display_in_types)) {
				if (!$turnoff_essb_main_box) {
					add_meta_box('essb_metabox', __('Easy Social Share Buttons', 'essb'), 'essb_register_settings_metabox_onoff', $cpt, 'side', 'high');
				}

				if (!$turnoff_essb_advanced_box) {
					add_meta_box('essb_metabox_visual', __('Easy Social Share Buttons: Visual Customization', 'essb'), 'essb_register_settings_metabox_visual', $cpt, 'normal', 'high');
				}

				if (!$turnoff_essb_stats_box) {
					add_meta_box('essb_metabox_stats', __('Easy Social Share Buttons: Stats', 'essb'), 'essb_register_settings_metabox_stats', $cpt, 'normal', 'core');
				}

			}
		}
	}

	public function handle_essb_save_metabox() {
		global $post, $post_id;

		if (! $post) {
			return $post_id;
		}

		if (! $post_id) {
			$post_id = $post->ID;
		}

		$essb_metabox = isset($_REQUEST['essb_metabox']) ? $_REQUEST['essb_metabox'] : array();

		$exist_metabox = isset($_REQUEST['essb_metabox']) ? true: false;

		if (!$exist_metabox && essb_option_bool_value('using_elementor')) {
			return;
		}

		$this->save_metabox_value ( $post_id, 'essb_off', $essb_metabox, 'text');
		$this->save_metabox_value ( $post_id, 'essb_post_button_style', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_template', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_counters', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_counter_pos', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_total_counter_pos', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_customizer', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_animations', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_optionsbp', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_content_position', $essb_metabox, 'text' );
		foreach ( essb_available_button_positions() as $position => $name ) {
			$this->save_metabox_value ( $post_id, "essb_post_button_position_{$position}", $essb_metabox, 'text' );
		}
		$this->save_metabox_value ( $post_id, 'essb_post_native', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_native_skin', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_share_message', $essb_metabox, 'textarea' );
		$this->save_metabox_value ( $post_id, 'essb_post_share_url', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_share_image', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_share_text', $essb_metabox, 'textarea' );
		$this->save_metabox_value ( $post_id, 'essb_post_pin_image', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_pin_desc', $essb_metabox, 'text');
		$this->save_metabox_value ( $post_id, 'essb_post_fb_url', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_plusone_url', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_hashtags', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_username', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_tweet', $essb_metabox, 'textarea' );
		$this->save_metabox_value ( $post_id, 'essb_activate_ga_campaign_tracking', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_desc', $essb_metabox, 'textarea' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_author', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_title', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_image', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_video', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_video_w', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_video_h', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_url', $essb_metabox, 'text');

		$this->save_metabox_value ( $post_id, 'essb_post_twitter_desc', $essb_metabox, 'textarea' );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_title', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_image', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_google_desc', $essb_metabox, 'textarea' );
		$this->save_metabox_value ( $post_id, 'essb_post_google_title', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_google_image', $essb_metabox, 'text');
		$this->save_metabox_value ( $post_id, 'essb_activate_sharerecovery', $essb_metabox, 'text');

		$this->save_metabox_value ( $post_id, 'essb_post_og_image1', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_image2', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_image3', $essb_metabox, 'text' );
		$this->save_metabox_value ( $post_id, 'essb_post_og_image4', $essb_metabox, 'text' );


		// Twitter Custom Share Value
		$essb_pc_twitter = ESSBOptionValuesHelper::options_value($essb_metabox, 'essb_pc_twitter');
		if (!empty($essb_pc_twitter)) {
			$this->save_metabox_value_simple($post_id, 'essb_pc_twitter', $essb_pc_twitter, 'text');
		}


		// @since 5.0
		// save fake share counters
		if (essb_option_bool_value('activate_fake')) {
			$listOfNetworks = essb_available_social_networks();
			foreach ($listOfNetworks as $key => $data) {
				$param = 'essb_pc_'.$key;
				$value = ESSBOptionValuesHelper::options_value($essb_metabox, $param);

				$this->save_metabox_value_simple($post_id, $param, $value, 'text');
			}
		}

		// @since 3.4.1
		// apply on save clearing and caching of post meta values that will be used within plugin
		$post_image = has_post_thumbnail( $post_id ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ) : '';
		$image = ($post_image != '') ? $post_image[0] : '';
		$this->save_metabox_value_simple_with_clear($post_id, 'essb_cached_image', $image, 'text');

		// clear cached counters ttl
		delete_post_meta($post_id, 'essb_cache_timestamp');
	}

	/**
	 * Saving metabox values with sanitazaion of the input data
	 *
	 * @param {integer} $post_id
	 * @param {string} $option
	 * @param {array} $valueContainer
	 * @param {string} $sanitize_type
	 */
	public function save_metabox_value($post_id, $option, $valueContainer, $sanitize_type = '') {
		$value = ESSBOptionValuesHelper::options_value($valueContainer, $option);

		if ($sanitize_type != '') {
			if ($sanitize_type == 'text') {
				$value = sanitize_text_field($value);
			}
			if ($sanitize_type == 'textarea') {
				$value = sanitize_textarea_field($value);
			}
		}

		if (!empty($value)) {
			update_post_meta ( $post_id, $option, $value );
		}
		else {
			delete_post_meta ( $post_id, $option );
		}
	}

	/**
	 * Saving direct post metabox value. The function overwrites data without clearing values (it will store blanks)
	 *
	 * @param unknown_type $post_id
	 * @param unknown_type $option
	 * @param unknown_type $value
	 * @param unknown_type $sanitize_type
	 */
	public function save_metabox_value_simple($post_id, $option, $value, $sanitize_type = '') {
		if ($sanitize_type != '') {
			if ($sanitize_type == 'text') {
				$value = sanitize_text_field($value);
			}
			if ($sanitize_type == 'textarea') {
				$value = sanitize_textarea_field($value);
			}
		}

		update_post_meta ( $post_id, $option, $value );
	}

	/**
	 * Saving metabox value and clears the key if the data is blank
	 *
	 * @param unknown_type $post_id
	 * @param unknown_type $option
	 * @param unknown_type $value
	 * @param unknown_type $sanitize_type
	 */
	public function save_metabox_value_simple_with_clear($post_id, $option, $value, $sanitize_type = '') {
		if ($sanitize_type != '') {
			if ($sanitize_type == 'text') {
				$value = sanitize_text_field($value);
			}
			if ($sanitize_type == 'textarea') {
				$value = sanitize_textarea_field($value);
			}
		}

		if (!empty($value)) {
			update_post_meta ( $post_id, $option, $value );
		}
		else {
			if (!essb_option_bool_value('using_elementor')) {
				delete_post_meta ( $post_id, $option );
			}
		}
	}

	/**
	 * Register plugin menu based on the pre-configured settings
	 */
	public function register_menu() {
		global $essb_all_options, $essb_navigation_tabs, $essb_options, $essb_sidebar_sections;

		$menu_pos = false;
		$essb_access = ESSBOptionValuesHelper::options_value($essb_options, 'essb_access');
		if (empty($essb_access)) {
			$essb_access = "edit_pages";
		}

		if ($menu_pos) {
			add_options_page ( "Easy Social Share Buttons", "Easy Social Share Buttons", 'edit_pages', "essb_options", array ($this, 'essb_settings_load' ), ESSB3_PLUGIN_URL . '/assets/images/essb_16.png', 114 );
		}
		else {

			add_menu_page ( "Easy Social Share Buttons", "Easy Social Share Buttons", $essb_access, "essb_options", array ($this, 'essb_settings_load' ) );

			$is_first = true;


			foreach ( $essb_navigation_tabs as $name => $label ) {

				$is_hidden = false;

				if (defined('ESSB3_SETTING5')) {
					$key_section_settings = isset($essb_sidebar_sections[$name]) ? $essb_sidebar_sections[$name] : array();
					$is_hidden = isset($key_section_settings['hide_menu']) ? $key_section_settings['hide_menu'] : false;

				}

				if ($is_first) {
					add_submenu_page( 'essb_options', $label, $label, $essb_access, 'essb_options', array ($this, 'essb_settings_load' ));
					$is_first = false;
				}
				else {
					if ($is_hidden) {
						add_submenu_page( null, $label, $label, $essb_access, 'essb_redirect_'.$name, array ($this, 'essb_settings_redirect1' ));
					}
					else {
						add_submenu_page( 'essb_options', $label, $label, $essb_access, 'essb_redirect_'.$name, array ($this, 'essb_settings_redirect1' ));
					}
				}
			}
		}
	}

	public function essb_settings_redirect1() {
		include (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-settings5.php');
	}

	public function essb_settings_redirect() {
		$requested = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";

		if (strpos($requested, 'essb_redirect_') !== false) {
			$options_page = str_replace('essb_redirect_', '', $requested);
			if ($options_page != '') {
				wp_redirect(esc_url(admin_url ( 'admin.php?page=essb_options&tab=' . $options_page )));
			}
		}
	}

	public function essb_settings_load() {
		include (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-settings5.php');
	}

	public function register_admin_assets($hook) {
		global $essb_admin_options;

		$requested = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";

		wp_register_style ( 'essb-admin-icon', ESSB3_PLUGIN_URL . '/assets/admin/easysocialshare.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin-icon' );

		// loading main plugin CSS to allow usage of extended controls

		wp_register_style ( 'essb-admin5', ESSB3_PLUGIN_URL . '/assets/admin/essb-admin5.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin5' );

		wp_enqueue_script ( 'essb-admin5', ESSB3_PLUGIN_URL . '/assets/admin/essb-admin5.js', array ('jquery' ), ESSB3_VERSION, true );

		$deactivate_fa = ESSBOptionValuesHelper::options_bool_value($essb_admin_options, 'deactivate_fa');
		// styles

		if (!$deactivate_fa) {
			wp_enqueue_style ( 'essb-fontawsome', ESSB3_PLUGIN_URL . '/assets/admin/font-awesome.min.css', array (), ESSB3_VERSION );
		}
		wp_enqueue_style ( 'essb-themifyicons', ESSB3_PLUGIN_URL . '/assets/admin/themify-icons.css', array (), ESSB3_VERSION );

		// register global admin assets only on required plugin settings pages
		if (strpos($requested, 'essb_') === false && strpos($requested, 'easy-social-metrics-lite') === false) {
			return;
		}

		wp_register_style ( 'essb-admin3-style', ESSB3_PLUGIN_URL . '/assets/css/easy-social-share-buttons.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin3-style' );

		wp_register_style ( 'essb-admin3-style-animations', ESSB3_PLUGIN_URL . '/assets/css/essb-animations.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin3-style-animations' );


		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');

		wp_enqueue_script( 'wp-color-picker-alpha', ESSB3_PLUGIN_URL.'/assets/admin/wp-color-picker-alpha.js', array( 'wp-color-picker' ), ESSB3_VERSION );

		wp_register_style ( 'essb-datatable', ESSB3_PLUGIN_URL . '/assets/admin/datatable/jquery.dataTables.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-datatable' );
		wp_enqueue_script ( 'essb-datatable', ESSB3_PLUGIN_URL . '/assets/admin/datatable/jquery.dataTables.js', array ('jquery' ), ESSB3_VERSION, true );

		wp_enqueue_style ( 'essb-morris-styles', ESSB3_PLUGIN_URL.'/assets/admin/morris.min.css',array (), ESSB3_VERSION );

		wp_enqueue_script ( 'essb-morris', ESSB3_PLUGIN_URL . '/assets/admin/morris.min.js', array ('jquery' ), ESSB3_VERSION );
		wp_enqueue_script ( 'essb-raphael', ESSB3_PLUGIN_URL . '/assets/admin/raphael-min.js', array ('jquery' ), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-opensans', 'https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,latin-ext' );

		wp_enqueue_style ( 'essb-sweetalerts', ESSB3_PLUGIN_URL.'/assets/admin/essb-admin5-core.css' );

		wp_enqueue_script ( 'essb-sweetalerts', ESSB3_PLUGIN_URL . '/assets/admin/sweetalert.min.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_style ( 'essb-sweetalerts', ESSB3_PLUGIN_URL.'/assets/admin/sweetalert.css' );

		wp_enqueue_style ( 'essb-codemirror', ESSB3_PLUGIN_URL.'/assets/admin/codemirror/codemirror.css' );

		wp_enqueue_script ( 'essb-codemirror', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/codemirror.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-xml', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/mode/xml/xml.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-javascript', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/mode/javascript/javascript.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-css', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/mode/css/css.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-html', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/mode/htmlmixed/htmlmixed.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-brackets', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/edit/matchbrackets.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-brackets-close', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/edit/closebrackets.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-tags', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/edit/matchtags.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-tags-close', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/edit/closetag.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-foldclose', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/fold/foldcode.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-foldgutter', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/fold/foldgutter.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-identfold', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/fold/indent-fold.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-xmlfold', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/fold/xml-fold.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-bracefold', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/fold/brace-fold.js', array ('jquery' ), ESSB3_VERSION, true );
		wp_enqueue_script ( 'essb-codemirror-commentfold', ESSB3_PLUGIN_URL . '/assets/admin/codemirror/addon/fold/comment-fold.js', array ('jquery' ), ESSB3_VERSION, true );

		wp_enqueue_style ( 'essb-advancedoptions', ESSB3_PLUGIN_URL.'/lib/admin/advanced-options/advancedoptions-core.css' );
		wp_enqueue_script ( 'essb-advancedoptions', ESSB3_PLUGIN_URL . '/lib/admin/advanced-options/advancedoptions-core.js', array ('jquery' ), ESSB3_VERSION, true );

		wp_enqueue_style ( 'essb-toast', ESSB3_PLUGIN_URL.'/assets/admin/jquery.toast.css' );
		wp_enqueue_script ( 'essb-toast', ESSB3_PLUGIN_URL . '/assets/admin/jquery.toast.js', array ('jquery' ), ESSB3_VERSION, true );

		wp_enqueue_style ( 'essb-styleoptions', ESSB3_PLUGIN_URL.'/lib/admin/styles-library/styles-core.css' );
		wp_enqueue_script ( 'essb-styleoptions', ESSB3_PLUGIN_URL . '/lib/admin/styles-library/styles-core.js', array ('jquery' ), ESSB3_VERSION, true );

		$is_for_firsttime = get_option ( ESSB3_FIRST_TIME_NAME );
		if (!$is_for_firsttime) { $is_for_firsttime = 'false'; }
		if ($is_for_firsttime == 'true') {
			wp_enqueue_style ( 'essb-anno', ESSB3_PLUGIN_URL.'/assets/admin/anno.css' );

			wp_enqueue_script ( 'essb-anno', ESSB3_PLUGIN_URL . '/assets/admin/anno.js', array ('jquery' ), ESSB3_VERSION, true );
			wp_enqueue_script ( 'essb-annos-scrollToView', ESSB3_PLUGIN_URL . '/assets/admin/jquery.scrollintoview.min.js', array ('jquery' ), ESSB3_VERSION, true );
		}
	}

	public function handle_save_settings() {
		if (@$_POST && isset ( $_POST ['option_page'] )) {
			$changed = false;

			if (! isset( $_POST['essb_token'] ) || !wp_verify_nonce( $_POST['essb_token'], 'essb_setup' )) {
				print 'Sorry, your nonce did not verify.';
				exit;
			}

			if (!essb_admin_settings_verify_token('essb_salt')) {
				print 'Unauthorized settings access.';
				exit;
			}

			if ('essb_settings_group' == $this->getval($_POST, 'option_page' )) {
				$this->update_options5();
				$this->update_fanscounter_options();

				// saving multilanguage options only if WPML or Polylang is installed
				if (essb_installed_wpml() || essb_installed_polylang()) {
					$this->update_wpml_translations();
				}

				$this->update_additional_options();
				$this->restore_settings();
				$changed = true;

				if (class_exists('ESSBDynamicCache')) {
					ESSBDynamicCache::flush();
				}

				if (class_exists('ESSBPrecompiledResources')) {
					ESSBPrecompiledResources::flush();
				}

				if (function_exists ( 'purge_essb_cache_static_cache' )) {
					purge_essb_cache_static_cache ();
				}

				// actions to hook on saving
				do_action('essb_after_admin_save_settings');
			}

			if ($changed) {
				if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
					if (class_exists('ESSBSocialFollowersCounter')) {
						essb_followers_counter()->settle_immediate_update();

						$current_options = get_option(ESSB3_OPTIONS_NAME);
						$fanscounter_clear_on_save = ESSBOptionValuesHelper::options_bool_value($current_options, 'fanscounter_clear_on_save');
						if ($fanscounter_clear_on_save) {
							essb_followers_counter()->clear_stored_values();
							//print "clear active";
						}
					}
				}

				$user_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
				$user_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';
				$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';

				if ($tab == 'quick') {
					$goback = esc_url_raw(add_query_arg(array('wizard-updated' => 'true', 'section' => $user_section, 'subsection' => $user_subsection), admin_url ('admin.php?page=essb_options')));
					wp_redirect ( $goback );
				}
				else {
				//$goback = add_query_arg ( 'settings-updated', 'true', wp_get_referer () );
					$goback = esc_url_raw(add_query_arg(array('settings-updated' => 'true', 'section' => $user_section, 'subsection' => $user_subsection), wp_get_referer ()));

					//$goback = str_replace('#038;', '', $goback);
					//*** TODO - Uncomment this
					wp_redirect ( $goback );
				}
				//print  (int) $_SERVER['CONTENT_LENGTH'];

				die ();
			}
		}

		/*if (@$_REQUEST && isset($_REQUEST['ready_style'])) {
			$this->apply_readymade();

			$user_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
			$user_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';

			//$goback = add_query_arg ( 'settings-updated', 'true', wp_get_referer () );
			$goback = remove_query_arg('ready_style');
			$goback = esc_url_raw(add_query_arg(array('settings-imported' => 'true', 'section' => $user_section, 'subsection' => $user_subsection), wp_get_referer ()));

			//$goback = str_replace('#038;', '', $goback);
			wp_redirect ( $goback );
			die ();
		}*/
	}

	public function restore_settings() {
		//essb_backup[configuration]
		$result = false;

		$backup_element = isset($_REQUEST['essb_backup']) ? $_REQUEST['essb_backup'] : array();

		$backup_string = isset($backup_element['configuration1']) ? $backup_element['configuration1'] : '';
		if ($backup_string != '') {
			$backup_string = htmlspecialchars_decode ( $backup_string );
			$backup_string = stripslashes ( $backup_string );

			$imported_options = json_decode ( $backup_string, true );

			if (is_array($imported_options)) {
				$result = true;
				update_option(ESSB3_OPTIONS_NAME, $imported_options);
			}
		}

		if (isset($_FILES['essb_backup_file'])) {
			$import_file = $_FILES['essb_backup_file']['tmp_name'];
			if( !empty( $import_file ) ) {
			// Retrieve the settings from the file and convert the json object to an array.
				$settings = (array) json_decode( file_get_contents( $import_file ) );
				update_option( ESSB3_OPTIONS_NAME, $settings );
			}
		}

		$backup_element = isset($_REQUEST['essb_backup2']) ? $_REQUEST['essb_backup2'] : array();

		$backup_string = isset($backup_element['configuration1']) ? $backup_element['configuration1'] : '';
		if ($backup_string != '') {
			$backup_string = htmlspecialchars_decode ( $backup_string );
			$backup_string = stripslashes ( $backup_string );

			$imported_options = json_decode ( $backup_string, true );

			if (is_array($imported_options)) {
				$result = true;
				delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
				update_option(ESSB3_OPTIONS_NAME_FANSCOUNTER, $imported_options, 'no', 'no');
			}
		}

		return $result;
	}

	public function update_additional_options() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;

		$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
		$user_options = array();
		$mapped_options = array();

		if ($current_tab == '') {
			return;
		}

		$options = $essb_section_options[$current_tab];

		foreach($options as $section => $fields) {
			$section_options = $fields;

			foreach ($section_options as $option) {
				$type = $option['type'];
				$id = isset($option['id']) ? $option['id'] : '';

				if ($id == '') {
					continue;
				}

				if (strpos($id, '|') === false) {
					continue;
				}
				
				$data_components = explode('|', $id);
				$settings_group = $data_components[0];
				$id = $data_components[1];

				if (!isset($user_options[$settings_group])) {
					$user_options[$settings_group] = isset($_REQUEST[$settings_group]) ? $_REQUEST[$settings_group] : array();
					$mapped_options[$settings_group] = array();
				}
				
				switch ($type) {
					case "checkbox_list_sortable":
						$option_value = isset($user_options[$settings_group][$id]) ? $user_options[$settings_group][$id] : '';
						$mapped_options[$settings_group][$id] = $option_value;

						$option_value = isset($user_options[$settings_group][$id.'_order']) ? $user_options[$settings_group][$id.'_order'] : '';
						$mapped_options[$settings_group][$id.'_order'] = $option_value;
						break;
					case 'network-select':
						$option_value = isset($user_options[$settings_group][$id]) ? $user_options[$settings_group][$id] : '';
						$mapped_options[$settings_group][$id] = $option_value;
						
						if (is_array($option_value)) {
							$base_id = str_replace('networks', '', $id);
							
							foreach ($option_value as $key) {
								$add_option_value = isset($user_options[$settings_group][$base_id.$key.'_name']) ? $user_options[$settings_group][$base_id.$key.'_name'] : '';
								
								if ($add_option_value != '') {
									$mapped_options[$settings_group][$base_id.$key.'_name'] = $add_option_value;
								}
							}
						}
						break;
					default:
						$option_value = isset($user_options[$settings_group][$id]) ? $user_options[$settings_group][$id] : '';
						$mapped_options[$settings_group][$id] = $option_value;
						break;
				}
			}
		}
		
		foreach ($mapped_options as $key => $fields) {
			$settings_key = ESSBOptionsFramework::option_keys_to_settings($key);

			if ($settings_key != '') {
				$current_options = get_option($settings_key);
				if (!is_array($current_options)) {
					$current_options = array();
				}

				foreach ($fields as $field_key => $field_value) {
					$current_options[$field_key] = $field_value;
				}

				$current_options = $this->clean_blank_values($current_options);

				//print_r($current_options);
				update_option($settings_key, $current_options);
			}
		}

		$hook_add = isset($_REQUEST['essb_hook_add']) ? true : false;
		if ($hook_add) {
			$hook_add_options = $_REQUEST['essb_hook_add'];

			$hook_id = isset($hook_add_options['hook_id']) ? $hook_add_options['hook_id'] : '';
			$hook_name = isset($hook_add_options['hook_name']) ? $hook_add_options['hook_name'] : '';
			$hook_type = isset($hook_add_options['hook_type']) ? $hook_add_options['hook_type'] : '';
			$hook_action = isset($hook_add_options['hook_action']) ? $hook_add_options['hook_action'] : '';

			$existing_hooks = get_option('essb-hook');
			if (!is_array($existing_hooks)) {
				$existing_hooks = array();
			}

			if ($hook_id != '' && $hook_name != '') {
				$existing_hooks[$hook_id] = array('id' => $hook_id, 'name' => $hook_name, 'type' => $hook_type, 'action' => $hook_action);
			}

			update_option('essb-hook', $existing_hooks);
		}

	}

	/**
	 * Save the followers counter settings
	 */
	public function update_fanscounter_options() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;

		$current_options = get_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
		if (!is_array($current_options)) {
			$current_options = array();
		}

		$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
		$user_options = isset($_REQUEST['essb_options_fans']) ? $_REQUEST['essb_options_fans'] : array();

		if ($current_tab == '') {
			return;
		}


		$options = $essb_section_options[$current_tab];

		foreach($options as $section => $fields) {
			$section_options = $fields;

			foreach ($section_options as $option) {
				$type = $option['type'];
				$id = isset($option['id']) ? $option['id'] : '';

				if ($id == '') {
					continue;
				}

				if (strpos($id, 'essb3fans_') === false) {
					continue;
				}
				if (strpos($id, '|') !== false) {
					continue;
				}


				switch ($type) {
					case "checkbox_list_sortable":
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;

						$option_value = isset($user_options[$id.'_order']) ? $user_options[$id.'_order'] : '';
						$current_options[$id.'_order'] = $option_value;
						break;
					default:
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = sanitize_text_field($option_value);

						break;
				}
			}
		}

		delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
		update_option(ESSB3_OPTIONS_NAME_FANSCOUNTER, $current_options, 'no', 'no');

		// clear cached timeouts for social networks
		if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
			if (class_exists('ESSBSocialFollowersCounter')) {
				essb_followers_counter()->settle_immediate_update();
			}
		}

	}

	/**
	 * Saving the multilanguage options
	 */
	public function update_wpml_translations() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;

		$current_options = get_option(ESSB3_WPML_OPTIONS_NAME);
		if (!is_array($current_options)) {
			$current_options = array();
		}

		$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
		$user_options = isset($_REQUEST['essb_options']) ? $_REQUEST['essb_options'] : array();

		if ($current_tab == '') {
			return;
		}

		$options = $essb_section_options[$current_tab];

		foreach($options as $section => $fields) {
			$section_options = $fields;

			foreach ($section_options as $option) {
				$type = $option['type'];
				$id = isset($option['id']) ? $option['id'] : '';

				if ($id == '') {
					continue;
				}

				if (strpos($id, 'wpml_') === false) {
					continue;
				}


				switch ($type) {
					case "checkbox_list_sortable":
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;

						$option_value = isset($user_options[$id.'_order']) ? $user_options[$id.'_order'] : '';
						$current_options[$id.'_order'] = $option_value;
						break;
					default:
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;

						break;
				}
			}
		}
		//print_r($current_options);
		update_option(ESSB3_WPML_OPTIONS_NAME, $current_options);

	}

	public function update_options5() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;

		$current_options = get_option(ESSB3_OPTIONS_NAME);
		if (!is_array($current_options)) {
			$current_options = array();
		}

		$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
		$user_options = isset($_REQUEST['essb_options']) ? $_REQUEST['essb_options'] : array();

		$reset_settings = isset($_REQUEST['reset_settings']) ? $_REQUEST['reset_settings'] : '';

		foreach ($user_options as $key => $value ) {
			$current_options[$key] = $value;

			if ($key == "stats_active") {
				if ($value == "true") {
					ESSBSocialShareAnalyticsBackEnd::install();
				}
			}
		}

		if ($current_tab == 'advanced') {
			$this->temporary_activate_post_type_settings();
		}

		// @since 4.1 change from social to where
		if ($current_tab == "where") {
			$this->temporary_activate_positions_by_posttypes();
		}

		// Secondary check for all switch and checkbox options
		$options = $essb_section_options[$current_tab];

		foreach($options as $section => $fields) {
			$section_options = $fields;

			foreach ($section_options as $option) {
				$type = $option['type'];
				$id = isset($option['id']) ? $option['id'] : '';

				if ($type == 'panel_start') {
					$element_options = isset($option['element_options']) ? $option['element_options']: array();
					$switch_id = isset($element_options['switch_id']) ? $element_options['switch_id'] : '';
					if ($switch_id != '') {
						$id = $switch_id;
					}
				}

				if ($id == '') {
					continue;
				}

				if (strpos($id, 'essb3fans_') !== false) {
					continue;
				}
				// handle separate WPML translations since version 4.1
				if (strpos($id, 'wpml_') !== false) {
					continue;
				}
				if (strpos($id, '|') !== false) {
					continue;
				}

				// custom ID parser for functions
				if ($id == 'essb3_options_template_select') {
					$id = 'style';
				}

				if ($id == 'essb3_network_selection' || $id == 'essb5_main_network_selection') {
					$type = "network_select";
				}
				if ($id == "essb3_network_rename") {
					$type = "network_rename";
				}
				if ($id == "essb3_post_type_select") {
					$id = "display_in_types";
				}
				if ($id == "essb3_esml_post_type_select") {
					$id = "esml_monitor_types";
				}

				$option_value = isset($user_options[$id]) ? $user_options[$id] : '';

				if ($id == 'essb3_network_selection' && defined('ESSB3_LIGHTMODE')) {
					$twitteruser =  isset($user_options['twitteruser']) ? $user_options['twitteruser'] : '';
					$current_options['twitteruser'] = $twitteruser;

					$twitterhashtags =  isset($user_options['twitterhashtags']) ? $user_options['twitterhashtags'] : '';
					$current_options['twitterhashtags'] = $twitterhashtags;
				}

				// quick setup options
				if ($id == "quick_setup_recommended" && $option_value == 'true') {
					$current_options['twitter_shareshort'] = 'true';
					$current_options['twitter_shareshort_service'] = 'wp';
					$current_options['twitter_message_optimize'] = 'true';
					$current_options['facebookadvanced'] = 'false';
					$current_options['buffer_twitter_user'] = 'true';
				}

				if ($id == "quick_setup_static" && $option_value == 'true') {
					$current_options['use_minified_css'] = 'true';
					$current_options['use_minified_js'] = 'true';
					$current_options['load_js_async'] = 'true';
					$current_options['load_css_footer'] = 'false';
				}

				if ($id == 'essb5_stylebuilder_select') {
					$id = 'stylebuilder_css';
				}

				// automatically set general social networks on all locations with personalized setup
				if ($id == 'essb5_custom_position_networks') {
					$user_networks = isset($user_options['networks']) ? $user_options['networks'] : array();
					$apply_on = isset($user_options['autoset_networks']) ? $user_options['autoset_networks'] : array();

					if (is_array($apply_on) && is_array($user_networks)) {
						foreach ($apply_on as $key) {
							$current_options[$key.'_networks'] = $user_networks;
						}
					}
				}

				if ($id == 'essb5_custom_position_settings') {
					$user_template = isset($user_options['style']) ? $user_options['style'] : '';

					$apply_on = isset($user_options['autoset_template']) ? $user_options['autoset_template'] : array();

					if (is_array($apply_on) && is_array($user_networks)) {
						foreach ($apply_on as $key) {
							$current_options[$key.'_template'] = $user_template;
						}
					}
				}

				// Update selected social networks on all locations inside plugin
				if ($id == 'wizard_networks_all' && $option_value == 'true') {
					$wizard_networks = isset($user_options['networks']) ? $user_options['networks'] : array();
					if (is_array($wizard_networks)) {
						$update_positions = essb5_available_content_positions();
						foreach ($update_positions as $key => $data) {
							$key = str_replace("content_", '', $key);
							$has_networks = isset($current_options[$key.'_networks']) ? $current_options[$key.'_networks']: '';
							if (is_array($has_networks)) {
								$current_options[$key.'_networks'] = $wizard_networks;
							}
						}

						$update_positions = essb5_available_button_positions();
						foreach ($update_positions as $key => $data) {
							$has_networks = isset($current_options[$key.'_networks']) ? $current_options[$key.'_networks']: '';

							if (is_array($has_networks)) {
								$current_options[$key.'_networks'] = $wizard_networks;
							}
						}
					}
				}

				if ($id == 'quick_setup_easy') {
					update_option(ESSB3_EASYMODE_NAME, 'true');
				}


				switch ($type) {
					case "network_rename":
						$option_value = isset($_REQUEST['essb_options_names']) ? $_REQUEST['essb_options_names'] : array();

						foreach ($option_value as $key => $value) {
							$network_option_value = "user_network_name_".$key;
							$current_options[$network_option_value] = $value;
						}

						break;
					case "network_select":
						$option_value = isset($user_options['networks']) ? $user_options['networks'] : array();
						$current_options['networks'] = $option_value;
						$option_value = isset($user_options['networks_order']) ? $user_options['networks_order'] : array();
						$current_options['networks_order'] = $option_value;

						// new network name handling
						$option_value = isset($_REQUEST['essb_options_names']) ? $_REQUEST['essb_options_names'] : array();

						foreach ($option_value as $key => $value) {
							$network_option_value = "user_network_name_".$key;
							$current_options[$network_option_value] = $value;
						}
						break;
					case "checkbox_list_sortable":
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;

						$option_value = isset($user_options[$id.'_order']) ? $user_options[$id.'_order'] : '';
						$current_options[$id.'_order'] = $option_value;
						break;
					default:
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';

						/**
						 * Sanitizing the values before save
						 */

						if ($type == 'text' || $type == 'text-in-panel' || $type == 'text-stretched' || $type == 'color' ||
								$type == 'color-in-panel') {
							$option_value = sanitize_text_field($option_value);
						}

						if ($type == 'textarea') {
							$option_value = sanitize_textarea_field($option_value);
						}

						$current_options[$id] = $option_value;

						if ($id == "stats_active") {
							if ($option_value == "true") {
								ESSBSocialShareAnalyticsBackEnd::install();
							}
						}

						if ($id == 'use_stylebuilder' && $option_value == 'true') {
							$list_of_styles = isset($user_options['stylebuilder_css']) ? $user_options['stylebuilder_css'] : array();
							essb_depend_load_function('essb_admin_build_resources', 'lib/admin/helpers/resource-builder-functions.php');
							essb_admin_build_resources($list_of_styles);
						}

						break;
				}
			}
		}

		// setting up plugin mode using wizard
		if ($current_tab == 'quick' || $current_tab == 'modes') {
			$functions_mode = isset($user_options['functions_mode']) ? $user_options['functions_mode'] : '';

			// clear all fields before setup the correct mode
			$current_options['deactivate_module_aftershare'] = 'false';
			$current_options['deactivate_module_analytics'] = 'false';
			$current_options['deactivate_module_affiliate'] = 'false';
			$current_options['deactivate_module_customshare'] = 'false';
			$current_options['deactivate_module_message'] = 'false';
			$current_options['deactivate_module_metrics'] = 'false';
			$current_options['deactivate_module_translate'] = 'false';
			$current_options['deactivate_module_followers'] = 'false';
			$current_options['deactivate_module_profiles'] = 'false';
			$current_options['deactivate_module_natives'] = 'false';
			$current_options['deactivate_module_subscribe'] = 'false';
			$current_options['deactivate_module_facebookchat'] = 'false';
			$current_options['deactivate_module_skypechat'] = 'false';

			$current_options['deactivate_method_float'] = 'false';
			$current_options['deactivate_method_postfloat'] = 'false';
			$current_options['deactivate_method_sidebar'] = 'false';
			$current_options['deactivate_method_topbar'] = 'false';
			$current_options['deactivate_method_bottombar'] = 'false';
			$current_options['deactivate_method_popup'] = 'false';
			$current_options['deactivate_method_flyin'] = 'false';
			$current_options['deactivate_method_postbar'] = 'false';
			$current_options['deactivate_method_point'] = 'false';
			$current_options['deactivate_method_image'] = 'false';
			$current_options['deactivate_method_native'] = 'false';
			$current_options['deactivate_method_heroshare'] = 'false';
			$current_options['deactivate_method_integrations'] = 'false';

			// @since 5.6 - deactivation of fake and hooks will not happen unless a mode that exludes them
			// is selected

			if ($functions_mode == 'light') {
				$current_options['deactivate_module_aftershare'] = 'true';
				$current_options['deactivate_module_analytics'] = 'true';
				$current_options['deactivate_module_affiliate'] = 'true';
				$current_options['deactivate_module_customshare'] = 'true';
				$current_options['deactivate_module_message'] = 'true';
				$current_options['deactivate_module_metrics'] = 'true';
				$current_options['deactivate_module_translate'] = 'true';
				$current_options['deactivate_module_followers'] = 'true';
				$current_options['deactivate_module_profiles'] = 'true';
				$current_options['deactivate_module_natives'] = 'true';
				$current_options['deactivate_module_subscribe'] = 'true';
				$current_options['deactivate_module_facebookchat'] = 'true';
				$current_options['deactivate_module_skypechat'] = 'true';

				$current_options['deactivate_method_float'] = 'true';
				$current_options['deactivate_method_postfloat'] = 'true';
				$current_options['deactivate_method_topbar'] = 'true';
				$current_options['deactivate_method_bottombar'] = 'true';
				$current_options['deactivate_method_popup'] = 'true';
				$current_options['deactivate_method_flyin'] = 'true';
				$current_options['deactivate_method_postbar'] = 'true';
				$current_options['deactivate_method_point'] = 'true';
				$current_options['deactivate_method_native'] = 'true';
				$current_options['deactivate_method_heroshare'] = 'true';
				$current_options['deactivate_method_integrations'] = 'true';

				$current_options['activate_fake'] = 'false';
				$current_options['activate_hooks'] = 'false';
			}

			if ($functions_mode == 'medium') {
				$current_options['deactivate_module_affiliate'] = 'true';
				$current_options['deactivate_module_customshare'] = 'true';
				$current_options['deactivate_module_message'] = 'true';
				$current_options['deactivate_module_metrics'] = 'true';
				$current_options['deactivate_module_translate'] = 'true';

				$current_options['deactivate_module_followers'] = 'true';
				$current_options['deactivate_module_profiles'] = 'true';
				$current_options['deactivate_module_natives'] = 'true';
				$current_options['deactivate_module_facebookchat'] = 'true';
				$current_options['deactivate_module_skypechat'] = 'true';

				$current_options['deactivate_method_postfloat'] = 'true';
				$current_options['deactivate_method_topbar'] = 'true';
				$current_options['deactivate_method_bottombar'] = 'true';
				$current_options['deactivate_method_popup'] = 'true';
				$current_options['deactivate_method_flyin'] = 'true';
				$current_options['deactivate_method_point'] = 'true';
				$current_options['deactivate_method_native'] = 'true';
				$current_options['deactivate_method_heroshare'] = 'true';
				$current_options['deactivate_method_integrations'] = 'true';

				$current_options['activate_fake'] = 'false';
				$current_options['activate_hooks'] = 'false';
			}

			if ($functions_mode == 'advanced') {
				$current_options['deactivate_module_customshare'] = 'true';

				$current_options['deactivate_module_followers'] = 'true';
				$current_options['deactivate_module_profiles'] = 'true';
				$current_options['deactivate_module_natives'] = 'true';
				$current_options['deactivate_module_facebookchat'] = 'true';
				$current_options['deactivate_module_skypechat'] = 'true';

				$current_options['deactivate_method_native'] = 'true';
				$current_options['deactivate_method_heroshare'] = 'true';

				$current_options['activate_fake'] = 'false';
				$current_options['activate_hooks'] = 'false';
			}

			if ($functions_mode == 'sharefollow') {
				$current_options['deactivate_module_customshare'] = 'true';

				$current_options['deactivate_module_natives'] = 'true';

				$current_options['deactivate_method_native'] = 'true';
				$current_options['deactivate_method_heroshare'] = 'true';

				$current_options['activate_fake'] = 'false';
				$current_options['activate_hooks'] = 'false';
			}
		}

		$current_options = $this->clean_blank_values($current_options);

		if ($reset_settings == 'true') {
			$current_options = array();

			if (!function_exists('essb_generate_default_settings')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/default-options.php');
			}

			$options_base = essb_generate_default_settings();
			if ($options_base) {
				$current_options = $options_base;
			}
		}
		update_option(ESSB3_OPTIONS_NAME, $current_options);

		if (!essb_option_bool_value('disable_settings_rollback')) {
			$this->store_settings_history($current_options);
		}
	}

	public function store_settings_history($options) {
		$now = time ();

		$history_container = get_option(ESSB5_SETTINGS_ROLLBACK);
		if (!is_array($history_container)) {
			$history_container = array();
		}

		if (count(array_keys($history_container)) < 10) {
			$history_container[$now] = $options;
		}
		else {
			$keys = array_keys($history_container);
			if (count($keys) > 1) {
				$first_key = $keys[0];
				unset($history_container[$first_key]);
			}

			$history_container[$now] = $options;
		}

		// stop autoloading of settings rollback option
		delete_option(ESSB5_SETTINGS_ROLLBACK);
		update_option(ESSB5_SETTINGS_ROLLBACK, $history_container, 'no', 'no');
	}

	function temporary_activate_positions_by_posttypes() {
		global $wp_post_types;

		ESSBOptionsStructureHelper::panel_start('where', 'display-2', __('I wish to have different button position for different post types', 'essb'), __('Activate this option if you wish to setup different positions for each post type.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'positions_by_pt', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));
		$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
		$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
		$first_post_type = "";
		$key = 1;
		foreach ( $pts as $pt ) {

			ESSBOptionsStructureHelper::field_heading('where', 'display-2', 'heading5', __('Customize button positions for: '.$wp_post_types [$pt]->label, 'essb'));
			ESSBOptionsStructureHelper::structure_row_start('where', 'display-2');
			ESSBOptionsStructureHelper::structure_section_start('where', 'display-2', 'c6', __('Primary content display position', 'essb'), __('Choose default in content position that will be used for that post type', 'essb'));
			ESSBOptionsStructureHelper::field_select('where', 'display-2', 'content_position_'.$pt, '', '', essb_simplified_radio_check_list(essb_avaliable_content_positions(), true));
			ESSBOptionsStructureHelper::structure_section_end('where', 'display-2');

			ESSBOptionsStructureHelper::structure_section_start('where', 'display-2', 'c6', __('Additional button display positions', 'essb'), __('Choose additional site display position that will be used for that post type', 'essb'));
			ESSBOptionsStructureHelper::field_checkbox_list('where', 'display-2', 'button_position_'.$pt, '', '', essb_simplified_radio_check_list(essb_available_button_positions()));
			ESSBOptionsStructureHelper::structure_section_end('where', 'display-2');
			ESSBOptionsStructureHelper::structure_row_end('where', 'display-2');
		}

		foreach ( $cpts as $cpt ) {

			ESSBOptionsStructureHelper::field_heading('where', 'display-2', 'heading5', __('Customize button positions for: '.$wp_post_types [$pt]->label, 'essb'));
			ESSBOptionsStructureHelper::structure_row_start('where', 'display-2');
			ESSBOptionsStructureHelper::structure_section_start('where', 'display-2', 'c6', __('Primary content display position', 'essb'), __('Choose default in content position that will be used for that post type', 'essb'));
			ESSBOptionsStructureHelper::field_select('where', 'display-2', 'content_position_'.$cpt, '', '', essb_simplified_radio_check_list(essb_avaliable_content_positions(), true));
			ESSBOptionsStructureHelper::structure_section_end('where', 'display-2');

			ESSBOptionsStructureHelper::structure_section_start('where', 'display-2', 'c6', __('Additional button display positions', 'essb'), __('Choose additional site display position that will be used for that post type', 'essb'));
			ESSBOptionsStructureHelper::field_checkbox_list('where', 'display-2', 'button_position_'.$cpt, '', '', essb_simplified_radio_check_list(essb_available_button_positions()));
			ESSBOptionsStructureHelper::structure_section_end('where', 'display-2');
			ESSBOptionsStructureHelper::structure_row_end('where', 'display-2');

		}

		ESSBOptionsStructureHelper::panel_end('where', 'display-2');
	}


	function temporary_activate_post_type_settings() {
		global $wp_post_types;

		$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
		$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
		$first_post_type = "";
		$key = 1;
		foreach ( $pts as $pt ) {
			if (empty ( $first_post_type )) {
				$first_post_type = $pt;
				ESSBOptionsStructureHelper::menu_item ( 'advanced', 'advancedpost', __ ( 'Display Settings by Post Type', 'essb' ), 'default', 'activate_first', 'advancedpost-1' );
			}
			ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedpost-' . $key, $wp_post_types [$pt]->label );

			ESSBOptionsStructureHelper::field_heading('advanced', 'advancedpost-' . $key, 'heading1', __('Advanced settings for post type: '.$wp_post_types [$pt]->label, 'essb'));
			essb_prepare_location_advanced_customization ( 'advanced', 'advancedpost-' . $key, 'post-type-'.$pt, true );
			$key ++;
		}

		foreach ( $cpts as $cpt ) {
			ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedpost-' . $key, $wp_post_types [$cpt]->label );
			ESSBOptionsStructureHelper::field_heading('advanced', 'advancedpost-' . $key, 'heading1', __('Advanced settings for post type: '.$wp_post_types [$cpt]->label, 'essb'));
			essb_prepare_location_advanced_customization ( 'advanced', 'advancedpost-' . $key, 'post-type-'.$cpt, true );
			$key ++;
		}

		$key = 1;
		$cpt = 'woocommerce';
		$cpt_title = 'WooCommerce';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, 'essb' ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;

		$cpt = 'wpecommerce';
		$cpt_title = 'WP e-Commerce';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, 'essb' ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;

		$cpt = 'jigoshop';
		$cpt_title = 'JigoShop';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, 'essb' ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;

		$cpt = 'ithemes';
		$cpt_title = 'iThemes Exchange';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, 'essb' ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;

		$cpt = 'bbpress';
		$cpt_title = 'bbPress';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, 'essb' ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;

		$cpt = 'buddypress';
		$cpt_title = 'BuddyPress';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, 'essb' ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
	}

	function clean_blank_values($object) {
		foreach ($object as $key => $value) {
			if (!is_array($value)) {
				$value = trim($value);

				if (empty($value)) {
					unset($object[$key]);
				}
			}
			else {
				if (count($value) == 0) {
					unset($object[$key]);
				}
			}
		}

		return $object;
	}

	function getval ($from, $what, $default=false) {
		if (is_object($from) && isset($from->$what)) return $from->$what;
		else if (is_array($from) && isset($from[$what])) return $from[$what];
		else return $default;
	}


	public function actions_download_settings() {
		global $essb_options;

		$backup_string = json_encode($essb_options);
		ignore_user_abort( true );
		nocache_headers();
		header('Content-disposition: attachment; filename=essb3-options-' . date( 'm-d-Y' ) . '.json');
		header('Content-type: application/json');
		header("Expires: 0" );
		echo $backup_string;
		exit;
	}
}

if (!function_exists('essb_admin_setting_token')) {
	function essb_admin_setting_token() {
		$token = get_option('essb-admin-settings-token');

		if (!$token || $token == '') {
			$token = mt_rand();
			update_option('essb-admin-settings-token', $token);
		}

		return $token;
	}
}

if (!function_exists('essb_admin_settings_verify_token')) {
	function essb_admin_settings_verify_token($token_param) {
		$value = isset($_REQUEST[$token_param]) ? $_REQUEST[$token_param] : '';

		$token = get_option('essb-admin-settings-token');

		return $value == $token ? true : false;
	}
}

?>

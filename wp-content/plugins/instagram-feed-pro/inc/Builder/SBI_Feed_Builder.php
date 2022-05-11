<?php
/**
 * Instagram Feed Builder
 *
 * @since 6.0
 */
namespace InstagramFeed\Builder;

use InstagramFeed\Builder\SBI_Feed_Saver;
use InstagramFeed\SB_Instagram_Data_Encryption;

class SBI_Feed_Builder {
	private static $instance;
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			return self::$instance;

		}
	}


	/**
	 * Constructor.
	 *
	 * @since 6.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init the Builder.
	 *
	 * @since 6.0
	*/
	public function init() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			// add ajax listeners
			SBI_Feed_Saver_Manager::hooks();
			SBI_Source::hooks();
			self::hooks();
		}
	}

	/**
	 * Mostly AJAX related hooks
	 *
	 * @since 6.0
	 */
	public static function hooks() {
		add_action( 'wp_ajax_sbi_dismiss_onboarding', array( 'InstagramFeed\Builder\SBI_Feed_Builder', 'after_dismiss_onboarding' ) );
	}

	/**
	 * Check users capabilities and maybe nonce before AJAX actions
	 *
	 * @param $check_nonce
	 * @param string $action
	 *
	 * @since 6.0
	 */
	public static function check_privilege( $check_nonce = false, $action = 'sbi-admin' ) {
		$cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';
		$cap = apply_filters( 'sbi_settings_pages_capability', $cap );

		if ( ! current_user_can( $cap ) ) {
			wp_die( 'You did not do this the right way!' );
		}

		if ( $check_nonce ) {
			$nonce = ! empty( $_POST[ $check_nonce ] ) ? $_POST[ $check_nonce ] : false;

			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die( 'You did not do this the right way!' );
			}
		}
	}

	/**
	 * Register Menu.
	 *
	 * @since 6.0
	 */
	public function register_menu() {
		$cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';
		$cap = apply_filters( 'sbi_settings_pages_capability', $cap );

		$feed_builder = add_submenu_page(
			'sb-instagram-feed',
			__( 'All Feeds', 'instagram-feed' ),
			__( 'All Feeds', 'instagram-feed' ),
			$cap,
			'sbi-feed-builder',
			array( $this, 'feed_builder' ),
			0
		);
		add_action( 'load-' . $feed_builder, array( $this, 'builder_enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueue Builder CSS & Script.
	 *
	 * Loads only for builder pages
	 *
	 * @since 6.0
	 */
	public function builder_enqueue_admin_scripts() {
		if ( get_current_screen() ) :
			$screen = get_current_screen();
			if ( strpos($screen->id, 'sbi-feed-builder')  !== false ) :
				$installed_plugins = get_plugins();

				$newly_retrieved_source_connection_data = SBI_Source::maybe_source_connection_data();
				$license_key                            = get_option( 'sbi_license_key', '' );

				$sbi_builder = array(
					'ajax_handler'         => admin_url( 'admin-ajax.php' ),
					'pluginType'           => 'pro',
					'builderUrl'           => admin_url( 'admin.php?page=sbi-feed-builder' ),
					'nonce'                => wp_create_nonce( 'sbi-admin' ),
					'adminPostURL'         => admin_url( 'post.php' ),
					'widgetsPageURL'       => admin_url( 'widgets.php' ),
					'supportPageUrl'       => admin_url( 'admin.php?page=sbi-support' ),
					'pluginURL'  			=> SBI_PLUGIN_URL,
					'genericText'          => self::get_generic_text(),
					'welcomeScreen'        => array(
						'mainHeading'              => __( 'All Feeds', 'instagram-feed' ),
						'createFeed'               => __( 'Create your Feed', 'instagram-feed' ),
						'createFeedDescription'    => __( 'Connect your Instagram account and choose a feed type', 'instagram-feed' ),
						'customizeFeed'            => __( 'Customize your feed type', 'instagram-feed' ),
						'customizeFeedDescription' => __( 'Choose layouts, color schemes, filters and more', 'instagram-feed' ),
						'embedFeed'                => __( 'Embed your feed', 'instagram-feed' ),
						'embedFeedDescription'     => __( 'Easily add the feed anywhere on your website', 'instagram-feed' ),
						'customizeImgPath'         => SBI_BUILDER_URL . 'assets/img/welcome-1.png',
						'embedImgPath'             => SBI_BUILDER_URL . 'assets/img/welcome-2.png',
					),
					'pluginsInfo'          => array(
						'social_wall' => array(
							'installed'    => isset( $installed_plugins['social-wall/social-wall.php'] ) ? true : false,
							'activated'    => is_plugin_active( 'social-wall/social-wall.php' ),
							'settingsPage' => admin_url( 'admin.php?page=sbsw' ),
						),
					),
					'allFeedsScreen'       => array(
						'mainHeading'     => __( 'All Feeds', 'instagram-feed' ),
						'columns'         => array(
							'nameText'      => __( 'Name', 'instagram-feed' ),
							'shortcodeText' => __( 'Shortcode', 'instagram-feed' ),
							'instancesText' => __( 'Instances', 'instagram-feed' ),
							'actionsText'   => __( 'Actions', 'instagram-feed' ),
						),
						'bulkActions'     => __( 'Bulk Actions', 'instagram-feed' ),
						'legacyFeeds'     => array(
							'heading'               => __( 'Legacy Feeds', 'instagram-feed' ),
							'toolTip'               => __( 'What are Legacy Feeds?', 'instagram-feed' ),
							'toolTipExpanded'       => array(
								__( 'Legacy feeds are older feeds from before the version 6 update. You can edit settings for these feeds by using the "Settings" button to the right. These settings will apply to all legacy feeds, just like the settings before version 6, and work in the same way that they used to.', 'instagram-feed' ),
								__( 'You can also create a new feed, which will now have it\'s own individual settings. Modifying settings for new feeds will not affect other feeds.', 'instagram-feed' ),
							),
							'toolTipExpandedAction' => array(
								__( 'Legacy feeds represent shortcodes of old feeds found on your website before <br/>the version 6 update.', 'instagram-feed' ),
								__( 'To edit Legacy feed settings, you will need to use the "Settings" button above <br/>or edit their shortcode settings directly. To delete them, simply remove the <br/>shortcode wherever it is being used on your site.', 'instagram-feed' ),
							),
							'show'                  => __( 'Show Legacy Feeds', 'instagram-feed' ),
							'hide'                  => __( 'Hide Legacy Feeds', 'instagram-feed' ),
						),
						'socialWallLinks' => self::get_social_wall_links(),
						'onboarding'      => $this->get_onboarding_text(),
					),
					'dialogBoxPopupScreen' => array(
						'deleteSourceCustomizer' => array(
							'heading'     => __( 'Delete "#"?', 'instagram-feed' ),
							'description' => __( 'You are going to delete this source. To retrieve it, you will need to add it again. Are you sure you want to continue?', 'instagram-feed' ),
						),
						'deleteSingleFeed'       => array(
							'heading'     => __( 'Delete "#"?', 'instagram-feed' ),
							'description' => __( 'You are going to delete this feed. You will lose all the settings. Are you sure you want to continue?', 'instagram-feed' ),
						),
						'deleteMultipleFeeds'    => array(
							'heading'     => __( 'Delete Feeds?', 'instagram-feed' ),
							'description' => __( 'You are going to delete these feeds. You will lose all the settings. Are you sure you want to continue?', 'instagram-feed' ),
						),
						'backAllToFeed'          => array(
							'heading'     => __( 'Are you Sure?', 'instagram-feed' ),
							'description' => __( 'Are you sure you want to leave this page, all unsaved settings will be lost, please make sure to save before leaving.', 'instagram-feed' ),
							'customButtons' => array(
								'confirm' => array(
									'text'  => __( 'Save and Exit', 'instagram-feed' ),
									'color' => 'blue',
								),
								'cancel'  => array(
									'text'  => __( 'Exit without Saving', 'instagram-feed' ),
									'color' => 'red',
								),
							),
						),
						'unsavedFeedSources'     => array(
							'heading'       => __( 'You have unsaved changes', 'instagram-feed' ),
							'description'   => __( 'If you exit without saving, all the changes you made will be reverted.', 'instagram-feed' ),
							'customButtons' => array(
								'confirm' => array(
									'text'  => __( 'Save and Exit', 'instagram-feed' ),
									'color' => 'blue',
								),
								'cancel'  => array(
									'text'  => __( 'Exit without Saving', 'instagram-feed' ),
									'color' => 'red',
								),
							),
						),
					),
					'selectFeedTypeScreen' => array(
						'mainHeading'            => __( 'Create an Instagram Feed', 'instagram-feed' ),
						'feedTypeHeading'        => __( 'Select Feed Type', 'instagram-feed' ),
						'mainDescription'        => __( 'Select one or more feed types. You can add or remove them later.', 'instagram-feed' ),
						'updateHeading'          => __( 'Update Feed Type', 'instagram-feed' ),
						'anotherFeedTypeHeading' => __( 'Add Another Source Type', 'instagram-feed' ),
					),
					'mainFooterScreen'     => array(
						'heading'     => sprintf( __( 'Upgrade to the %1$sAll Access Bundle%2$s to get all of our Pro Plugins', 'instagram-feed' ), '<strong>', '</strong>' ),
						'description' => __( 'Includes all Smash Balloon plugins for one low price: Instagram, Facebook, Twitter, YouTube, and Social Wall', 'instagram-feed' ),
						'promo'       => sprintf( __( '%1$sBonus%2$s Lite users get %3$s50&#37; Off%4$s automatically applied at checkout', 'instagram-feed' ), '<span class="sbi-bld-ft-bns">', '</span>', '<strong>', '</strong>' ),
					),
					'embedPopupScreen'     => array(
						'heading'       => __( 'Embed Feed', 'instagram-feed' ),
						'description'   => __( 'Add the unique shortcode to any page, post, or widget:', 'instagram-feed' ),
						'description_2' => __( 'Or use the built in WordPress block or widget', 'instagram-feed' ),
						'addPage'       => __( 'Add to a Page', 'instagram-feed' ),
						'addWidget'     => __( 'Add to a Widget', 'instagram-feed' ),
						'selectPage'    => __( 'Select Page', 'instagram-feed' ),
					),
					'links'                => self::get_links_with_utm(),
					'pluginsInfo'          => array(
						'social_wall' => array(
							'installed'    => isset( $installed_plugins['social-wall/social-wall.php'] ) ? true : false,
							'activated'    => is_plugin_active( 'social-wall/social-wall.php' ),
							'settingsPage' => admin_url( 'admin.php?page=sbsw' ),
						),
					),
					'selectSourceScreen'   => self::select_source_screen_text(),
					'feedTypes'            => $this->get_feed_types(),
					'socialInfo'           => $this->get_smashballoon_info(),
					'svgIcons'             => $this->builder_svg_icons(),
					'installPluginsPopup'  => $this->install_plugins_popup(),
					'feeds'                => self::get_feed_list(),
					'itemsPerPage'			=> SBI_Db::RESULTS_PER_PAGE,
			        'feedsCount' 			=> SBI_Db::feeds_count(),
					'sources'              => self::get_source_list(),
					'sourceConnectionURLs' => SBI_Source::get_connection_urls(),

					'legacyFeeds'          => $this->get_legacy_feed_list(),
					'extensionsPopup'      => array(
						'socialwall' => array(
							//Combine all your social media channels into one Social Wall
							'heading'         => __( 'Combine all your social media channels into one', 'instagram-feed' ) . ' <span>' . __( 'Social Wall', 'instagram-feed' ) . '</span>',
							'description'     => __( '<span class="sb-social-wall">A dash of Instagram, a sprinkle of Facebook, a spoonful of Twitter, and a dollop of YouTube, all in the same feed.</span>', 'instagram-feed' ),
							'popupContentBtn' => '<div class="sbi-fb-extpp-lite-btn">' . self::builder_svg_icons()['tag'] . __( 'Instagram Pro users get 50% OFF', 'instagram-feed' ) . '</div>',
							'img'             => '<svg width="397" height="264" viewBox="0 0 397 264" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><g filter="url(#filter0_ddd)"><rect x="18.957" y="63" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip1)"><path d="M18.957 63H132.769V176.812H18.957V63Z" fill="#0068A0"/><rect x="56.957" y="106" width="105" height="105" rx="9" fill="#005B8C"/></g><path d="M36.0293 165.701C31.4649 165.701 27.7305 169.427 27.7305 174.017C27.7305 178.166 30.7678 181.61 34.7347 182.232V176.423H32.6268V174.017H34.7347V172.183C34.7347 170.1 35.9712 168.954 37.8716 168.954C38.7762 168.954 39.7222 169.112 39.7222 169.112V171.162H38.6766C37.6475 171.162 37.3239 171.801 37.3239 172.456V174.017H39.6309L39.2575 176.423H37.3239V182.232C39.2794 181.924 41.0602 180.926 42.3446 179.419C43.629 177.913 44.3325 175.996 44.3281 174.017C44.3281 169.427 40.5936 165.701 36.0293 165.701Z" fill="#006BFA"/><rect x="53.1016" y="169.699" width="41.2569" height="9.95855" rx="1.42265" fill="#D0D1D7"/><g filter="url(#filter1_ddd)"><rect x="18.957" y="201" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip2)"><path d="M18.957 201H132.769V314.812H18.957V201Z" fill="#EC352F"/><circle cx="23.957" cy="243" r="59" fill="#FE544F"/></g><g filter="url(#filter2_ddd)"><rect x="139.957" y="23" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip3)"><path d="M139.957 23H253.769V136.812H139.957V23Z" fill="#8C8F9A"/><circle cx="127.457" cy="142.5" r="78.5" fill="#D0D1D7"/></g><path d="M157.026 129.493C154.537 129.493 152.553 131.516 152.553 133.967C152.553 136.456 154.537 138.44 157.026 138.44C159.477 138.44 161.5 136.456 161.5 133.967C161.5 131.516 159.477 129.493 157.026 129.493ZM157.026 136.884C155.431 136.884 154.109 135.601 154.109 133.967C154.109 132.372 155.392 131.088 157.026 131.088C158.621 131.088 159.905 132.372 159.905 133.967C159.905 135.601 158.621 136.884 157.026 136.884ZM162.706 129.338C162.706 128.754 162.239 128.287 161.655 128.287C161.072 128.287 160.605 128.754 160.605 129.338C160.605 129.921 161.072 130.388 161.655 130.388C162.239 130.388 162.706 129.921 162.706 129.338ZM165.662 130.388C165.584 128.987 165.273 127.743 164.262 126.731C163.25 125.72 162.005 125.409 160.605 125.331C159.166 125.253 154.848 125.253 153.408 125.331C152.008 125.409 150.802 125.72 149.752 126.731C148.74 127.743 148.429 128.987 148.351 130.388C148.274 131.827 148.274 136.145 148.351 137.585C148.429 138.985 148.74 140.191 149.752 141.241C150.802 142.253 152.008 142.564 153.408 142.642C154.848 142.719 159.166 142.719 160.605 142.642C162.005 142.564 163.25 142.253 164.262 141.241C165.273 140.191 165.584 138.985 165.662 137.585C165.74 136.145 165.74 131.827 165.662 130.388ZM163.795 139.102C163.523 139.88 162.9 140.463 162.161 140.774C160.994 141.241 158.271 141.124 157.026 141.124C155.742 141.124 153.019 141.241 151.891 140.774C151.113 140.463 150.53 139.88 150.219 139.102C149.752 137.974 149.868 135.25 149.868 133.967C149.868 132.722 149.752 129.999 150.219 128.832C150.53 128.093 151.113 127.509 151.891 127.198C153.019 126.731 155.742 126.848 157.026 126.848C158.271 126.848 160.994 126.731 162.161 127.198C162.9 127.47 163.484 128.093 163.795 128.832C164.262 129.999 164.145 132.722 164.145 133.967C164.145 135.25 164.262 137.974 163.795 139.102Z" fill="url(#paint0_linear)"/><rect x="174.102" y="129.699" width="41.2569" height="9.95855" rx="1.42265" fill="#D0D1D7"/><g filter="url(#filter3_ddd)"><rect x="139.957" y="161" width="114" height="109" rx="2.8453" fill="white"/></g><rect x="148.957" y="194" width="91" height="8" rx="1.42265" fill="#D0D1D7"/><rect x="148.957" y="208" width="51" height="8" rx="1.42265" fill="#D0D1D7"/><path d="M164.366 172.062C163.788 172.324 163.166 172.497 162.521 172.579C163.181 172.182 163.691 171.552 163.931 170.794C163.308 171.169 162.618 171.432 161.891 171.582C161.298 170.937 160.466 170.562 159.521 170.562C157.758 170.562 156.318 172.002 156.318 173.779C156.318 174.034 156.348 174.282 156.401 174.514C153.731 174.379 151.353 173.097 149.771 171.154C149.493 171.627 149.336 172.182 149.336 172.767C149.336 173.884 149.898 174.874 150.768 175.437C150.236 175.437 149.741 175.287 149.306 175.062V175.084C149.306 176.644 150.416 177.949 151.886 178.242C151.414 178.371 150.918 178.389 150.438 178.294C150.642 178.934 151.041 179.493 151.579 179.894C152.117 180.295 152.767 180.517 153.438 180.529C152.301 181.43 150.891 181.916 149.441 181.909C149.186 181.909 148.931 181.894 148.676 181.864C150.101 182.779 151.796 183.312 153.611 183.312C159.521 183.312 162.768 178.407 162.768 174.154C162.768 174.012 162.768 173.877 162.761 173.734C163.391 173.284 163.931 172.714 164.366 172.062Z" fill="#1B90EF"/><g filter="url(#filter4_ddd)"><rect x="260.957" y="63" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip4)"><rect x="260.957" y="63" width="113.812" height="113.812" fill="#D72C2C"/><path d="M283.359 103.308L373.461 193.41H208.793L283.359 103.308Z" fill="#DF5757"/></g><path d="M276.37 176.456L280.677 173.967L276.37 171.477V176.456ZM285.963 169.958C286.071 170.348 286.145 170.871 286.195 171.535C286.253 172.199 286.278 172.772 286.278 173.27L286.328 173.967C286.328 175.784 286.195 177.12 285.963 177.975C285.755 178.722 285.274 179.203 284.527 179.411C284.137 179.519 283.423 179.593 282.328 179.643C281.249 179.701 280.262 179.726 279.349 179.726L278.029 179.776C274.552 179.776 272.386 179.643 271.531 179.411C270.784 179.203 270.303 178.722 270.096 177.975C269.988 177.585 269.913 177.062 269.863 176.398C269.805 175.734 269.78 175.162 269.78 174.664L269.73 173.967C269.73 172.149 269.863 170.813 270.096 169.958C270.303 169.212 270.784 168.73 271.531 168.523C271.921 168.415 272.635 168.34 273.73 168.29C274.809 168.232 275.797 168.207 276.71 168.207L278.029 168.158C281.506 168.158 283.672 168.29 284.527 168.523C285.274 168.73 285.755 169.212 285.963 169.958Z" fill="#EB2121"/><rect x="295.102" y="169.699" width="41.2569" height="9.95855" rx="1.42265" fill="#D0D1D7"/><g filter="url(#filter5_ddd)"><rect x="260.957" y="201" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip5)"><rect x="260.957" y="201" width="113.812" height="113.812" fill="#59AB46"/><circle cx="374.457" cy="235.5" r="44.5" fill="#468737"/></g><g clip-path="url(#clip6)"><path d="M139.957 228H253.957V296C253.957 296.552 253.509 297 252.957 297H140.957C140.405 297 139.957 296.552 139.957 296V228Z" fill="#0068A0"/><circle cx="227.957" cy="245" r="34" fill="#004D77"/></g></g><defs><filter id="filter0_ddd" x="0.462572" y="53.0414" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter1_ddd" x="0.462572" y="191.041" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter2_ddd" x="121.463" y="13.0414" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter3_ddd" x="121.463" y="151.041" width="150.989" height="145.989" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter4_ddd" x="242.463" y="53.0414" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter5_ddd" x="242.463" y="191.041" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><linearGradient id="paint0_linear" x1="154.502" y1="158.603" x2="191.208" y2="121.133" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient><clipPath id="clip0"><rect width="396" height="264" fill="white" transform="translate(0.957031)"/></clipPath><clipPath id="clip1"><path d="M18.957 65.3711C18.957 64.0616 20.0186 63 21.3281 63H130.398C131.708 63 132.769 64.0616 132.769 65.3711V156.895H18.957V65.3711Z" fill="white"/></clipPath><clipPath id="clip2"><path d="M18.957 203.371C18.957 202.062 20.0186 201 21.3281 201H130.398C131.708 201 132.769 202.062 132.769 203.371V294.895H18.957V203.371Z" fill="white"/></clipPath><clipPath id="clip3"><path d="M139.957 25.3711C139.957 24.0616 141.019 23 142.328 23H251.398C252.708 23 253.769 24.0616 253.769 25.3711V116.895H139.957V25.3711Z" fill="white"/></clipPath><clipPath id="clip4"><path d="M260.957 65.3711C260.957 64.0616 262.019 63 263.328 63H372.398C373.708 63 374.769 64.0616 374.769 65.3711V156.895H260.957V65.3711Z" fill="white"/></clipPath><clipPath id="clip5"><path d="M260.957 203.371C260.957 202.062 262.019 201 263.328 201H372.398C373.708 201 374.769 202.062 374.769 203.371V294.895H260.957V203.371Z" fill="white"/></clipPath><clipPath id="clip6"><path d="M139.957 228H253.957V296C253.957 296.552 253.509 297 252.957 297H140.957C140.405 297 139.957 296.552 139.957 296V228Z" fill="white"/></clipPath></defs></svg>',
							'demoUrl'         => 'https://smashballoon.com/social-wall/demo/?utm_campaign=facebook-pro&utm_source=feed-type&utm_medium=social-wall&utm_content=learn-more',
							'buyUrl'          => sprintf( 'https://smashballoon.com/social-wall/pricing/?edd_license_key=%s&upgrade=true&utm_campaign=facebook-pro&utm_source=feed-type&utm_medium=social-wall&utm_content=upgrade', $license_key ),
							'bullets'         => array(
								'heading' => __( 'Upgrade to the All Access Bundle and get:', 'instagram-feed' ),
								'content' => array(
									__( 'Custom Facebook Feed Pro', 'instagram-feed' ),
									__( 'All Pro Facebook Extensions', 'instagram-feed' ),
									__( 'Custom Twitter Feeds Pro', 'instagram-feed' ),
									__( 'Instagram Feed Pro', 'instagram-feed' ),
									__( 'YouTube Feeds Pro', 'instagram-feed' ),
									__( 'Social Wall Pro', 'instagram-feed' ),
								),
							),
						),
					),

				);

				if ( $newly_retrieved_source_connection_data ) {
					$sbi_builder['newSourceData'] = $newly_retrieved_source_connection_data;
				}
				 if ( isset( $_GET['manualsource'] ) && $_GET['manualsource'] == true ) {
			        $sbi_builder['manualSourcePopupInit'] = true;
		        }

				$maybe_feed_customizer_data = SBI_Feed_Saver_Manager::maybe_feed_customizer_data();

				if ( $maybe_feed_customizer_data ) {
					sb_instagram_scripts_enqueue( true );
					$sbi_builder['customizerFeedData']       = $maybe_feed_customizer_data;
					$sbi_builder['customizerSidebarBuilder'] = \InstagramFeed\Builder\Tabs\SBI_Builder_Customizer_Tab::get_customizer_tabs();
					$sbi_builder['wordpressPageLists']       = $this->get_wp_pages();

					if ( ! isset( $_GET['feed_id'] ) || $_GET['feed_id'] === 'legacy' ) {
						$feed_id                       = 'legacy';
						$customizer_atts               = $maybe_feed_customizer_data['settings'];
						$customizer_atts['customizer'] = true;
					} elseif ( intval( $_GET['feed_id'] ) > 0 ) {
						$feed_id         = intval( $_GET['feed_id'] );
						$customizer_atts = array(
							'feed'       => $feed_id,
							'customizer' => true,
						);
					}

					if ( ! empty( $feed_id ) ) {
						$settings_preview = self::add_customizer_att( $customizer_atts );

						$sbi_builder['feedInitOutput'] = htmlspecialchars( display_instagram( $settings_preview, true ) );
					}

					//Date
					global $wp_locale;
					wp_enqueue_script(
						'sbi-date_i18n',
						SBI_PLUGIN_URL . 'admin/builder/assets/js/date_i18n.js',
						null,
						SBIVER,
						true
					);

					$monthNames      = array_map(
						array( &$wp_locale, 'get_month' ),
						range( 1, 12 )
					);
					$monthNamesShort = array_map(
						array( &$wp_locale, 'get_month_abbrev' ),
						$monthNames
					);
					$dayNames        = array_map(
						array( &$wp_locale, 'get_weekday' ),
						range( 0, 6 )
					);
					$dayNamesShort   = array_map(
						array( &$wp_locale, 'get_weekday_abbrev' ),
						$dayNames
					);
					wp_localize_script(
						'sbi-date_i18n',
						'DATE_I18N',
						array(
							'month_names'       => $monthNames,
							'month_names_short' => $monthNamesShort,
							'day_names'         => $dayNames,
							'day_names_short'   => $dayNamesShort,
						)
					);
				}

				wp_enqueue_style(
					'sbi-builder-style',
					SBI_PLUGIN_URL . 'admin/builder/assets/css/builder.css',
					false,
					SBIVER
				);

				self::global_enqueue_ressources_scripts();

				wp_enqueue_script(
					'sbi-builder-app',
					SBI_PLUGIN_URL . 'admin/builder/assets/js/builder.js',
					null,
					SBIVER,
					true
				);
				// Customize screens
				$sbi_builder['customizeScreens'] = $this->get_customize_screens_text();
				wp_localize_script(
					'sbi-builder-app',
					'sbi_builder',
					$sbi_builder
				);
				wp_enqueue_media();
			endif;
		endif;
	}

	/**
	 * Get WP Pages List
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_wp_pages() {
		$pagesList   = get_pages();
		$pagesResult = array();
		if ( is_array( $pagesList ) ) {
			foreach ( $pagesList as $page ) {
				array_push(
					$pagesResult,
					array(
						'id'    => $page->ID,
						'title' => $page->post_title,
					)
				);
			}
		}
		return $pagesResult;
	}


	/**
	 * Global JS + CSS Files
	 *
	 * Shared JS + CSS ressources for the admin panel
	 *
	 * @since 6.0
	 */
	public static function global_enqueue_ressources_scripts( $is_settings = false ) {
		wp_enqueue_style(
			'feed-global-style',
			SBI_PLUGIN_URL . 'admin/builder/assets/css/global.css',
			false,
			SBIVER
		);

		wp_enqueue_script(
			'feed-builder-vue',
			'https://cdn.jsdelivr.net/npm/vue@2.6.12',
			null,
			'2.6.12',
			true
		);

		wp_enqueue_script(
			'feed-colorpicker-vue',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/vue-color.min.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'feed-builder-ressources',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/ressources.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'sb-dialog-box',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/confirm-dialog.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'install-plugin-popup',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/install-plugin-popup.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'sb-add-source',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/add-source.js',
			null,
			SBIVER,
			true
		);

		$newly_retrieved_source_connection_data = SBI_Source::maybe_source_connection_data();
		$sbi_source                             = array(
			'sources'              => self::get_source_list(),
			'sourceConnectionURLs' => SBI_Source::get_connection_urls( $is_settings ),
			'nonce'                => wp_create_nonce( 'sbi-admin' ),
		);
		if ( $newly_retrieved_source_connection_data ) {
			$sbi_source['newSourceData'] = $newly_retrieved_source_connection_data;
		}
		if ( isset( $_GET['manualsource'] ) && $_GET['manualsource'] == true ) {
			$sbi_source['manualSourcePopupInit'] = true;
		}

		wp_localize_script(
			'sb-add-source',
			'sbi_source',
			$sbi_source
		);
	}

	/**
	 * Get Generic text
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_generic_text() {
		$icons = self::builder_svg_icons();
		return array(
			'done'                              => __( 'Done', 'instagram-feed' ),
			'title'                             => __( 'Settings', 'instagram-feed' ),
			'dashboard'                         => __( 'Dashboard', 'instagram-feed' ),
			'addNew'                            => __( 'Add New', 'instagram-feed' ),
			'addSource'                         => __( 'Add Source', 'instagram-feed' ),
			'addAnotherSource'                  => __( 'Add another Source', 'instagram-feed' ),
			'addSourceType'                     => __( 'Add Another Source Type', 'instagram-feed' ),
			'previous'                          => __( 'Previous', 'instagram-feed' ),
			'showSelected'                      => __( 'Show Selected', 'instagram-feed' ),
			'showAll'                           => __( 'Show All', 'instagram-feed' ),
			'next'                              => __( 'Next', 'instagram-feed' ),
			'finish'                            => __( 'Finish', 'instagram-feed' ),
			'new'                               => __( 'New', 'instagram-feed' ),
			'update'                            => __( 'Update', 'instagram-feed' ),
			'upgrade'                           => __( 'Upgrade', 'instagram-feed' ),
			'settings'                          => __( 'Settings', 'instagram-feed' ),
			'back'                              => __( 'Back', 'instagram-feed' ),
			'backAllFeeds'                      => __( 'Back to all feeds', 'instagram-feed' ),
			'createFeed'                        => __( 'Create Feed', 'instagram-feed' ),
			'add'                               => __( 'Add', 'instagram-feed' ),
			'change'                            => __( 'Change', 'instagram-feed' ),
			'getExtention'                      => __( 'Get Extension', 'instagram-feed' ),
			'viewDemo'                          => __( 'View Demo', 'instagram-feed' ),
			'includes'                          => __( 'Includes', 'instagram-feed' ),
			'photos'                            => __( 'Photos', 'instagram-feed' ),
			'photo'                             => __( 'Photo', 'instagram-feed' ),
			'apply'                             => __( 'Apply', 'instagram-feed' ),
			'copy'                              => __( 'Copy', 'instagram-feed' ),
			'edit'                              => __( 'Edit', 'instagram-feed' ),
			'duplicate'                         => __( 'Duplicate', 'instagram-feed' ),
			'delete'                            => __( 'Delete', 'instagram-feed' ),
			'remove'                            => __( 'Remove', 'instagram-feed' ),
			'removeSource'                      => __( 'Remove Source', 'instagram-feed' ),
			'shortcode'                         => __( 'Shortcode', 'instagram-feed' ),
			'clickViewInstances'                => __( 'Click to view Instances', 'instagram-feed' ),
			'usedIn'                            => __( 'Used in', 'instagram-feed' ),
			'place'                             => __( 'place', 'instagram-feed' ),
			'places'                            => __( 'places', 'instagram-feed' ),
			'item'                              => __( 'Item', 'instagram-feed' ),
			'items'                             => __( 'Items', 'instagram-feed' ),
			'learnMore'                         => __( 'Learn More', 'instagram-feed' ),
			'location'                          => __( 'Location', 'instagram-feed' ),
			'page'                              => __( 'Page', 'instagram-feed' ),
			'copiedClipboard'                   => __( 'Copied to Clipboard', 'instagram-feed' ),
			'feedImported'                      => __( 'Feed imported successfully', 'instagram-feed' ),
			'failedToImportFeed'                => __( 'Failed to import feed', 'instagram-feed' ),
			'timeline'                          => __( 'Timeline', 'instagram-feed' ),
			'help'                              => __( 'Help', 'instagram-feed' ),
			'admin'                             => __( 'Admin', 'instagram-feed' ),
			'member'                            => __( 'Member', 'instagram-feed' ),
			'reset'                             => __( 'Reset', 'instagram-feed' ),
			'preview'                           => __( 'Preview', 'instagram-feed' ),
			'name'                              => __( 'Name', 'instagram-feed' ),
			'id'                                => __( 'ID', 'instagram-feed' ),
			'token'                             => __( 'Token', 'instagram-feed' ),
			'confirm'                           => __( 'Confirm', 'instagram-feed' ),
			'cancel'                            => __( 'Cancel', 'instagram-feed' ),
			'clear'                             => __( 'Clear', 'instagram-feed' ),
			'clearFeedCache'                    => __( 'Clear Feed Cache', 'instagram-feed' ),
			'saveSettings'                      => __( 'Save Changes', 'instagram-feed' ),
			'feedName'                          => __( 'Feed Name', 'instagram-feed' ),
			'shortcodeText'                     => __( 'Shortcode', 'instagram-feed' ),
			'general'                           => __( 'General', 'instagram-feed' ),
			'feeds'                             => __( 'Feeds', 'instagram-feed' ),
			'translation'                       => __( 'Translation', 'instagram-feed' ),
			'advanced'                          => __( 'Advanced', 'instagram-feed' ),
			'error'                             => __( 'Error:', 'instagram-feed' ),
			'errorNotice'                       => __( 'There was an error when trying to connect to Instagram.', 'instagram-feed' ),
			'errorDirections'                   => '<a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'instagram-feed' ) . '</a>',
			'errorSource'                       => __( 'Source Invalid', 'instagram-feed' ),
			'errorEncryption'                   => __( 'Encryption Error', 'instagram-feed' ),
			'invalid'                           => __( 'Invalid', 'instagram-feed' ),
			'reconnect'                         => __( 'Reconnect', 'instagram-feed' ),
			'feed'                              => __( 'feed', 'instagram-feed' ),
			'sourceNotUsedYet'                  => __( 'Source is not used yet', 'instagram-feed' ),
			'addImage'                          => __( 'Add Image', 'instagram-feed' ),
			'businessRequired'                  => __( 'Business Account required', 'instagram-feed' ),
			'selectedPost'                      => __( 'Selected Post', 'instagram-feed' ),
			'productLink'                       => __( 'Product Link', 'instagram-feed' ),
			'enterProductLink'                  => __( 'Add your product URL here', 'instagram-feed' ),
			'editSources'                       => __( 'Edit Sources', 'instagram-feed' ),
			'moderateFeed'                      => __( 'Moderate your feed', 'instagram-feed' ),
			'moderateFeedSaveExit'              => __( 'Save and Exit', 'instagram-feed' ),
			'moderationMode'                    => __( 'Moderation Mode', 'instagram-feed' ),
			'moderationModeEnterPostId'         => __( 'Or Enter Post IDs to hide manually', 'instagram-feed' ),
			'moderationModePostIdPlaceholder'   => __( 'Add Post IDs here, separated by comma, to hide posts with a specific ID', 'instagram-feed' ),
			'moderationModeTextareaPlaceholder' => __( 'Add words here to hide any posts containing these words', 'instagram-feed' ),
			'filtersAndModeration'              => __( 'Filters & Moderation', 'instagram-feed' ),
			'topRated'                          => __( 'Top Rated', 'instagram-feed' ),
			'mostRecent'                        => __( 'Most recent', 'instagram-feed' ),
			'moderationModePreview'             => __( 'Moderation Mode Preview', 'instagram-feed' ),
			'shoppableModePreview'             => __( 'Shoppable Feed Preview', 'instagram-feed' ),

			'notification'                      => array(
				'feedSaved'             => array(
					'type' => 'success',
					'text' => __( 'Feed saved successfully', 'instagram-feed' ),
				),
				'feedSavedError'        => array(
					'type' => 'error',
					'text' => __( 'Error saving Feed', 'instagram-feed' ),
				),
				'previewUpdated'        => array(
					'type' => 'success',
					'text' => __( 'Preview updated successfully', 'instagram-feed' ),
				),
				'carouselLayoutUpdated' => array(
					'type' => 'success',
					'text' => __( 'Carousel updated successfully', 'instagram-feed' ),
				),
				'unkownError'           => array(
					'type' => 'error',
					'text' => __( 'Unknown error occurred', 'instagram-feed' ),
				),
				'cacheCleared'          => array(
					'type' => 'success',
					'text' => __( 'Feed cache cleared', 'instagram-feed' ),
				),
				'selectSourceError'     => array(
					'type' => 'error',
					'text' => __( 'Please select a source for your feed', 'instagram-feed' ),
				),
				'commentCacheCleared'   => array(
					'type' => 'success',
					'text' => __( 'Comment cache cleared', 'instagram-feed' ),
				),
			),
			'install'                           => __( 'Install', 'instagram-feed' ),
			'installed'                         => __( 'Installed', 'instagram-feed' ),
			'activate'                          => __( 'Activate', 'instagram-feed' ),
			'installedAndActivated'             => __( 'Installed & Activated', 'instagram-feed' ),
			'free'                              => __( 'Free', 'instagram-feed' ),
			'invalidLicenseKey'                 => __( 'Invalid license key', 'instagram-feed' ),
			'licenseActivated'                  => __( 'License activated', 'instagram-feed' ),
			'licenseDeactivated'                => __( 'License Deactivated', 'instagram-feed' ),
			'carouselLayoutUpdated'             => array(
				'type' => 'success',
				'text' => __( 'Carousel Layout updated', 'instagram-feed' ),
			),
			'getMoreFeatures'                   => __( 'Get more features with Custom Facebook Feed Pro', 'instagram-feed' ),
			'liteFeedUsers'                     => __( 'Lite Feed Users get 50% OFF', 'instagram-feed' ),
			'tryDemo'                           => __( 'Try Demo', 'instagram-feed' ),
			'displayImagesVideos'               => __( 'Display images and videos in posts', 'instagram-feed' ),
			'viewLikesShares'                   => __( 'View likes, shares and comments', 'instagram-feed' ),
			'allFeedTypes'                      => __( 'All Feed Types: Photos, Albums, Events and more', 'instagram-feed' ),
			'abilityToLoad'                     => __( 'Ability to “Load More” posts', 'instagram-feed' ),
			'andMuchMore'                       => __( 'And Much More!', 'instagram-feed' ),
			'sbiFreeCTAFeatures'                => array(
				__( 'Filter posts', 'instagram-feed' ),
				__( 'Popup photo/video lighbox', 'instagram-feed' ),
				__( '30 day money back guarantee', 'instagram-feed' ),
				__( 'Multiple post layout options', 'instagram-feed' ),
				__( 'Video player (HD, 360, Live)', 'instagram-feed' ),
				__( 'Fast, friendly and effective support', 'instagram-feed' ),
			),
			'ctaShowFeatures'                   => __( 'Show Features', 'instagram-feed' ),
			'ctaHideFeatures'                   => __( 'Hide Features', 'instagram-feed' ),
			'redirectLoading'                   => array(
				'heading'     => __( 'Redirecting to connect.smashballoon.com', 'instagram-feed' ),
				'description' => __( 'You will be redirected to our app so you can connect your account in 5 seconds', 'instagram-feed' ),
			),
		);
	}

	/**
	 * Select Source Screen Text
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function select_source_screen_text() {
		return array(
			'mainHeading'               => __( 'Select one or more sources', 'instagram-feed' ),
			'description'               => __( 'Sources are Instagram accounts your feed will display content from', 'instagram-feed' ),
			'emptySourceDescription'    => __( 'Looks like you have not added any source.<br/>Use “Add Source” to add a new one.', 'instagram-feed' ),
			'mainHashtagHeading'        => __( 'Enter Public Hashtags', 'instagram-feed' ),
			'hashtagDescription'        => __( 'Add one or more hashtags separated by comma', 'instagram-feed' ),
			'hashtagGetBy'              => __( 'Fetch posts that are', 'instagram-feed' ),

			'sourcesListPopup'          => array(
				'user'   => array(
					'mainHeading' => __( 'Add a source for Timeline', 'instagram-feed' ),
					'description' => __( 'Select or add an account you want to display the timeline for', 'instagram-feed' ),
				),
				'tagged' => array(
					'mainHeading' => __( 'Add a source for Mentions', 'instagram-feed' ),
					'description' => __( 'Select or add an account you want to display the mentions for', 'instagram-feed' ),
				),
			),

			'perosnalAccountToolTipTxt' => array(
				__(
					'Due to changes in Instagram’s new API, we can no<br/>
					longer get mentions for personal accounts. To<br/>
					enable this for your account, you will need to convert it to<br/>
					a Business account. Learn More',
					'instagram-feed'
				),
			),
			'groupsToolTip'             => array(
				__( 'Due to Facebook limitations, it\'s not possible to display photo feeds from a Group, only a Page.', 'instagram-feed' ),
			),
			'updateHeading'             => __( 'Update Source', 'instagram-feed' ),
			'updateDescription'         => __( 'Select a source from your connected Facebook Pages and Groups. Or, use "Add New" to connect a new one.', 'instagram-feed' ),
			'updateFooter'              => __( 'Add multiple Facebook Pages or Groups to a feed with our Multifeed extension', 'instagram-feed' ),
			'noSources'                 => __( 'Please add a source in order to display a feed. Go to the "Settings" tab -> "Sources" section -> Click "Add New" to connect a source.', 'instagram-feed' ),

			'multipleTypes'             => array(
				'user'    => array(
					'heading'     => __( 'User Timeline', 'instagram-feed' ),
					'icon'        => 'user',
					'description' => __( 'Connect an account to show posts for it.', 'instagram-feed' ),
					'actionType'  => 'addSource',
				),
				'hashtag' => array(
					'heading'          => __( 'Hashtag', 'instagram-feed' ),
					'icon'             => 'hashtag',
					'tooltip'      => __( 'Add one or more hashtags separated by comma.', 'instagram-feed' ),
					'businessRequired' => true,
					'actionType'       => 'inputHashtags',
				),
				'tagged'  => array(
					'heading'          => __( 'Tagged', 'instagram-feed' ),
					'icon'             => 'mention',
					'description'      => __( 'Connect an account to show tagged posts. This does not give us any permission to manage your Instagram account.', 'instagram-feed' ),
					'businessRequired' => true,
					'actionType'       => 'addSource',
				),
			),

			'modal'                     => array(
				'addNew'                     => __( 'Connect your Instagram Account', 'instagram-feed' ),
				'selectSourceType'           => __( 'Select Account Type', 'instagram-feed' ),
				'connectAccount'             => __( 'Connect an Instagram Account', 'instagram-feed' ),
				'connectAccountDescription'  => __( 'This does not give us permission to manage your Instagram account, it simply allows the plugin to see a list of them and retrieve their public content from the API.', 'instagram-feed' ),
				'connect'                    => __( 'Connect', 'instagram-feed' ),
				'enterEventToken'            => __( 'Enter Events Access Token', 'instagram-feed' ),
				'enterEventTokenDescription' => sprintf( __( 'Due to restrictions by Facebook, you need to create a Facebook app and then paste that app Access Token here. We have a guide to help you with just that, which you can read %1$shere%2$s', 'instagram-feed' ), '<a href="https://smashballoon.com/instagram-feed/page-token/" target="_blank" rel="noopener">', '</a>' ),
				'alreadyHave'                => __( 'Already have a API Token and Access Key for your account?', 'instagram-feed' ),
				'addManuallyLink'            => __( 'Add Account Manually', 'instagram-feed' ),
				'selectAccount'              => __( 'Select an Instagram Account', 'instagram-feed' ),
				'showing'                    => __( 'Showing', 'instagram-feed' ),
				'facebook'                   => __( 'Facebook', 'instagram-feed' ),
				'businesses'                 => __( 'Businesses', 'instagram-feed' ),
				'groups'                     => __( 'Groups', 'instagram-feed' ),
				'connectedTo'                => __( 'connected to', 'instagram-feed' ),
				'addManually'                => __( 'Add a Source Manually', 'instagram-feed' ),
				'addSource'                  => __( 'Add Source', 'instagram-feed' ),
				'sourceType'                 => __( 'Source Type', 'instagram-feed' ),
				'accountID'                  => __( 'Instagram Account ID', 'instagram-feed' ),
				'fAccountID'                 => __( 'Instagram Account ID', 'instagram-feed' ),
				'eventAccessToken'           => __( 'Event Access Token', 'instagram-feed' ),
				'enterID'                    => __( 'Enter ID', 'instagram-feed' ),
				'accessToken'                => __( 'Instagram Access Token', 'instagram-feed' ),
				'enterToken'                 => __( 'Enter Token', 'instagram-feed' ),
				'addApp'                     => __( 'Add Instagram App to your group', 'instagram-feed' ),
				'addAppDetails'              => __( 'To get posts from your group, Instagram requires the "Smash Balloon Plugin" app to be added in your group settings. Just follow the directions here:', 'instagram-feed' ),
				'addAppSteps'                => array(
					__( 'Go to your group settings page by ', 'instagram-feed' ),
					sprintf( __( 'Search for "Smash Balloon" and select our app %1$s(see screenshot)%2$s', 'instagram-feed' ), '<a href="JavaScript:void(0);" id="sbi-group-app-tooltip">', '<img class="sbi-group-app-screenshot sb-tr-1" src="' . trailingslashit( SBI_PLUGIN_URL ) . 'admin/assets/img/group-app.png" alt="Thumbnail Layout"></a>' ),
					__( 'Click "Add" and you are done.', 'instagram-feed' ),
				),
				'alreadyExists'              => __( 'Account already exists', 'instagram-feed' ),
				'alreadyExistsExplanation'   => __( 'The Instagram account you added is already connected as a “Business” account. Would you like to replace it with a “Personal“ account? (Note: Personal accounts cannot be used to display Mentions or Hashtag feeds.)', 'instagram-feed' ),
				'replaceWithPersonal'        => __( 'Replace with Personal', 'instagram-feed' ),
				'notAdmin'                   => __( 'For groups you are not an administrator of', 'instagram-feed' ),
				'disclaimerMentions'         => __( 'Due to Instagram’s limitations, you need to connect a business account to display a Mentions timeline', 'instagram-feed' ),
				'disclaimerHashtag'          => __( 'Due to Instagram’s limitations, you need to connect a business account to display a Hashtag feed', 'instagram-feed' ),
				'notSureToolTip'             => __( 'Select "Personal" if displaying a regular feed of posts, as this can display feeds from either a Personal or Business account. For displaying a Hashtag or Tagged feed, you must have an Instagram Business account. If needed, you can convert a Personal account into a Business account by following the directions {link}here{link}.', 'instagram-feed' ),
			),
			'footer'                    => array(
				'heading' => __( 'Add feeds for popular social platforms with <span>our other plugins</span>', 'instagram-feed' ),
			),
			'personal'                  => __( 'Personal', 'instagram-feed' ),
			'business'                  => __( 'Business', 'instagram-feed' ),
			'notSure'                   => __( "I'm not sure", 'instagram-feed' ),
		);
	}

	/**
	 * For Other Platforms listed on the footer widget
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function builder_svg_icons() {
		$builder_svg_icons = array(
			'youtube'             => '<svg viewBox="0 0 14 11" fill="none"><path d="M5.66683 7.5L9.12683 5.5L5.66683 3.5V7.5ZM13.3735 2.28C13.4602 2.59334 13.5202 3.01334 13.5602 3.54667C13.6068 4.08 13.6268 4.54 13.6268 4.94L13.6668 5.5C13.6668 6.96 13.5602 8.03334 13.3735 8.72C13.2068 9.32 12.8202 9.70667 12.2202 9.87334C11.9068 9.96 11.3335 10.02 10.4535 10.06C9.58683 10.1067 8.7935 10.1267 8.06016 10.1267L7.00016 10.1667C4.20683 10.1667 2.46683 10.06 1.78016 9.87334C1.18016 9.70667 0.793496 9.32 0.626829 8.72C0.540163 8.40667 0.480163 7.98667 0.440163 7.45334C0.393496 6.92 0.373496 6.46 0.373496 6.06L0.333496 5.5C0.333496 4.04 0.440163 2.96667 0.626829 2.28C0.793496 1.68 1.18016 1.29334 1.78016 1.12667C2.0935 1.04 2.66683 0.980002 3.54683 0.940002C4.4135 0.893336 5.20683 0.873336 5.94016 0.873336L7.00016 0.833336C9.7935 0.833336 11.5335 0.940003 12.2202 1.12667C12.8202 1.29334 13.2068 1.68 13.3735 2.28Z"/></svg>',
			'twitter'             => '<svg viewBox="0 0 14 12" fill="none"><path d="M13.9735 1.50001C13.4602 1.73334 12.9069 1.88667 12.3335 1.96001C12.9202 1.60667 13.3735 1.04667 13.5869 0.373338C13.0335 0.706672 12.4202 0.940005 11.7735 1.07334C11.2469 0.500005 10.5069 0.166672 9.66686 0.166672C8.10019 0.166672 6.82019 1.44667 6.82019 3.02667C6.82019 3.25334 6.84686 3.47334 6.89352 3.68001C4.52019 3.56001 2.40686 2.42 1.00019 0.693338C0.753522 1.11334 0.613522 1.60667 0.613522 2.12667C0.613522 3.12 1.11352 4 1.88686 4.5C1.41352 4.5 0.973522 4.36667 0.586856 4.16667V4.18667C0.586856 5.57334 1.57352 6.73334 2.88019 6.99334C2.46067 7.10814 2.02025 7.12412 1.59352 7.04C1.77459 7.60832 2.12921 8.10561 2.60753 8.46196C3.08585 8.81831 3.66382 9.0158 4.26019 9.02667C3.24928 9.82696 1.99619 10.2595 0.706855 10.2533C0.480189 10.2533 0.253522 10.24 0.0268555 10.2133C1.29352 11.0267 2.80019 11.5 4.41352 11.5C9.66686 11.5 12.5535 7.14 12.5535 3.36C12.5535 3.23334 12.5535 3.11334 12.5469 2.98667C13.1069 2.58667 13.5869 2.08 13.9735 1.50001Z"/></svg>',
			'instagram'           => '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 4.50781C6.5 4.50781 4.50781 6.53906 4.50781 9C4.50781 11.5 6.5 13.4922 9 13.4922C11.4609 13.4922 13.4922 11.5 13.4922 9C13.4922 6.53906 11.4609 4.50781 9 4.50781ZM9 11.9297C7.39844 11.9297 6.07031 10.6406 6.07031 9C6.07031 7.39844 7.35938 6.10938 9 6.10938C10.6016 6.10938 11.8906 7.39844 11.8906 9C11.8906 10.6406 10.6016 11.9297 9 11.9297ZM14.7031 4.35156C14.7031 3.76562 14.2344 3.29688 13.6484 3.29688C13.0625 3.29688 12.5938 3.76562 12.5938 4.35156C12.5938 4.9375 13.0625 5.40625 13.6484 5.40625C14.2344 5.40625 14.7031 4.9375 14.7031 4.35156ZM17.6719 5.40625C17.5938 4 17.2812 2.75 16.2656 1.73438C15.25 0.71875 14 0.40625 12.5938 0.328125C11.1484 0.25 6.8125 0.25 5.36719 0.328125C3.96094 0.40625 2.75 0.71875 1.69531 1.73438C0.679688 2.75 0.367188 4 0.289062 5.40625C0.210938 6.85156 0.210938 11.1875 0.289062 12.6328C0.367188 16.0391 0.679688 15.25 1.69531 16.3047C2.75 17.3203 3.96094 17.6328 5.36719 17.7109C6.8125 17.7891 11.1484 17.7891 12.5938 17.7109C14 17.6328 15.25 17.3203 16.2656 16.3047C17.2812 15.25 17.5938 16.0391 17.6719 12.6328C17.75 11.1875 17.75 6.85156 17.6719 5.40625ZM15.7969 14.1562C15.5234 14.9375 14.8984 15.5234 14.1562 15.8359C12.9844 16.3047 10.25 16.1875 9 16.1875C7.71094 16.1875 4.97656 16.3047 3.84375 15.8359C3.0625 15.5234 2.47656 14.9375 2.16406 14.1562C1.69531 13.0234 1.8125 10.2891 1.8125 9C1.8125 7.75 1.69531 5.01562 2.16406 3.84375C2.47656 3.10156 3.0625 2.51562 3.84375 2.20312C4.97656 1.73438 7.71094 1.85156 9 1.85156C10.25 1.85156 12.9844 1.73438 14.1562 2.20312C14.8984 2.47656 15.4844 3.10156 15.7969 3.84375C16.2656 5.01562 16.1484 7.75 16.1484 9C16.1484 10.2891 16.2656 13.0234 15.7969 14.1562Z" fill="url(#paint0_linear)"/><defs><linearGradient id="paint0_linear" x1="6.46484" y1="33.7383" x2="43.3242" y2="-3.88672" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient></defs></svg>',
			'facebook'            => '<svg viewBox="0 0 14 15"><path d="M7.00016 0.860001C3.3335 0.860001 0.333496 3.85333 0.333496 7.54C0.333496 10.8733 2.7735 13.64 5.96016 14.14V9.47333H4.26683V7.54H5.96016V6.06667C5.96016 4.39333 6.9535 3.47333 8.48016 3.47333C9.20683 3.47333 9.96683 3.6 9.96683 3.6V5.24667H9.12683C8.30016 5.24667 8.04016 5.76 8.04016 6.28667V7.54H9.8935L9.5935 9.47333H8.04016V14.14C9.61112 13.8919 11.0416 13.0903 12.0734 11.88C13.1053 10.6697 13.6704 9.13043 13.6668 7.54C13.6668 3.85333 10.6668 0.860001 7.00016 0.860001Z"/></svg>',
			'smash' 		      => '<svg height="18" viewBox="0 0 28 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M27.2235 16.8291C27.2235 7.53469 21.1311 0 13.6131 0C6.09513 0 0 7.53469 0 16.8291C0 25.7393 5.5828 33.0095 12.6525 33.6193L11.9007 36L16.6147 35.599L14.9608 33.5775C21.8439 32.7422 27.2235 25.5639 27.2235 16.8291Z" fill="#FE544F"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.8586 5.91699L17.5137 12.6756L24.3006 12.8705L19.3911 17.4354L23.2687 23.044L16.7362 21.816L14.7557 28.3487L11.7488 22.4987L5.67719 25.2808L8.01283 19.0094L2.09131 16.0227L8.43013 13.9841L6.68099 7.73959L12.678 11.1585L16.8586 5.91699Z" fill="white"/></svg>',
			'tag'                 => '<svg viewBox="0 0 18 18"><path d="M16.841 8.65033L9.34102 1.15033C9.02853 0.840392 8.60614 0.666642 8.16602 0.666993H2.33268C1.89066 0.666993 1.46673 0.842587 1.15417 1.15515C0.841611 1.46771 0.666016 1.89163 0.666016 2.33366V8.16699C0.665842 8.38692 0.709196 8.60471 0.79358 8.8078C0.877964 9.01089 1.00171 9.19528 1.15768 9.35033L8.65768 16.8503C8.97017 17.1603 9.39256 17.334 9.83268 17.3337C10.274 17.3318 10.6966 17.155 11.0077 16.842L16.841 11.0087C17.154 10.6975 17.3308 10.275 17.3327 9.83366C17.3329 9.61373 17.2895 9.39595 17.2051 9.19285C17.1207 8.98976 16.997 8.80538 16.841 8.65033ZM9.83268 15.667L2.33268 8.16699V2.33366H8.16602L15.666 9.83366L9.83268 15.667ZM4.41602 3.16699C4.66324 3.16699 4.90492 3.2403 5.11048 3.37766C5.31604 3.51501 5.47626 3.71023 5.57087 3.93864C5.66548 4.16705 5.69023 4.41838 5.642 4.66086C5.59377 4.90333 5.47472 5.12606 5.2999 5.30088C5.12508 5.47569 4.90236 5.59474 4.65988 5.64297C4.4174 5.69121 4.16607 5.66645 3.93766 5.57184C3.70925 5.47723 3.51403 5.31702 3.37668 5.11146C3.23933 4.90589 3.16602 4.66422 3.16602 4.41699C3.16602 6.08547 3.29771 3.76753 3.53213 3.53311C3.76655 3.29869 6.0845 3.16699 4.41602 3.16699Z"/></svg>',
			'copy'                => '<svg viewBox="0 0 12 13" fill="none"><path d="M10.25 0.25H4.625C3.9375 0.25 3.375 0.8125 3.375 1.5V9C3.375 9.6875 3.9375 10.25 4.625 10.25H10.25C10.9375 10.25 11.5 9.6875 11.5 9V1.5C11.5 0.8125 10.9375 0.25 10.25 0.25ZM10.25 9H4.625V1.5H10.25V9ZM0.875 8.375V7.125H2.125V8.375H0.875ZM0.875 4.9375H2.125V6.1875H0.875V4.9375ZM5.25 11.5H6.5V12.75H5.25V11.5ZM0.875 10.5625V9.3125H2.125V10.5625H0.875ZM2.125 12.75C1.4375 12.75 0.875 12.1875 0.875 11.5H2.125V12.75ZM4.3125 12.75H3.0625V11.5H4.3125V12.75ZM7.4375 12.75V11.5H8.6875C8.6875 12.1875 8.125 12.75 7.4375 12.75ZM2.125 2.75V4H0.875C0.875 3.3125 1.4375 2.75 2.125 2.75Z"/></svg>',
			'duplicate'           => '<svg viewBox="0 0 10 12" fill="none"><path d="M6.99997 0.5H0.999969C0.449969 0.5 -3.05176e-05 0.95 -3.05176e-05 1.5V8.5H0.999969V1.5H6.99997V0.5ZM8.49997 2.5H2.99997C2.44997 2.5 1.99997 2.95 1.99997 3.5V10.5C1.99997 11.05 2.44997 11.5 2.99997 11.5H8.49997C9.04997 11.5 9.49997 11.05 9.49997 10.5V3.5C9.49997 2.95 9.04997 2.5 8.49997 2.5ZM8.49997 10.5H2.99997V3.5H8.49997V10.5Z"/></svg>',
			'edit'                => '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.25 9.06241V11.2499H2.4375L8.88917 4.79824L6.70167 2.61074L0.25 9.06241ZM10.9892 2.69824L8.80167 0.510742L7.32583 1.99241L9.51333 4.17991L10.9892 2.69824Z" fill="currentColor"/></svg>',
			'delete'              => '<svg viewBox="0 0 10 12" fill="none"><path d="M1.00001 10.6667C1.00001 11.4 1.60001 12 2.33334 12H7.66668C8.40001 12 9.00001 11.4 9.00001 10.6667V2.66667H1.00001V10.6667ZM2.33334 4H7.66668V10.6667H2.33334V4ZM7.33334 0.666667L6.66668 0H3.33334L2.66668 0.666667H0.333344V2H9.66668V0.666667H7.33334Z"/></svg>',
			'checkmark'           => '<svg width="11" height="9"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.15641 5.65271L9.72487 0.0842487L10.9623 1.32169L4.15641 8.12759L0.444097 4.41528L1.68153 3.17784L4.15641 5.65271Z"/></svg>',
			'checkmarklarge'      => '<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.08058 8.36133L16.0355 0.406383L15.8033 2.17415L6.08058 11.8969L0.777281 6.59357L2.54505 4.8258L6.08058 8.36133Z" fill="currentColor"></path></svg>',
			'information'         => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.3335 5H7.66683V3.66667H6.3335V5ZM7.00016 12.3333C4.06016 12.3333 1.66683 9.94 1.66683 7C1.66683 4.06 4.06016 1.66667 7.00016 1.66667C9.94016 1.66667 12.3335 4.06 12.3335 7C12.3335 9.94 9.94016 12.3333 7.00016 12.3333ZM7.00016 0.333332C6.12468 0.333332 5.25778 0.505771 4.44894 0.840802C3.6401 1.17583 2.90517 1.6669 2.28612 2.28595C1.03588 3.5362 0.333496 5.23189 0.333496 7C0.333496 8.76811 1.03588 10.4638 2.28612 11.714C2.90517 12.3331 3.6401 12.8242 4.44894 13.1592C5.25778 13.4942 6.12468 13.6667 7.00016 13.6667C8.76827 13.6667 10.464 12.9643 11.7142 11.714C12.9645 10.4638 13.6668 8.76811 13.6668 7C13.6668 6.12452 13.4944 5.25761 13.1594 4.44878C12.8243 3.63994 12.3333 2.90501 11.7142 2.28595C11.0952 1.6669 10.3602 1.17583 9.55139 0.840802C8.74255 0.505771 7.87564 0.333332 7.00016 0.333332ZM6.3335 10.3333H7.66683V6.33333H6.3335V10.3333Z" fill="#141B38"/></svg>',
			'cog'                 => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.99989 9.33334C6.38105 9.33334 5.78756 9.0875 5.34998 8.64992C4.91239 8.21233 4.66656 7.61884 4.66656 7C4.66656 6.38117 4.91239 5.78767 5.34998 5.35009C5.78756 4.9125 6.38105 4.66667 6.99989 4.66667C7.61873 4.66667 8.21222 4.9125 8.64981 5.35009C9.08739 5.78767 9.33323 6.38117 9.33323 7C9.33323 7.61884 9.08739 8.21233 8.64981 8.64992C8.21222 9.0875 7.61873 9.33334 6.99989 9.33334ZM11.9532 7.64667C11.9799 7.43334 11.9999 7.22 11.9999 7C11.9999 6.78 11.9799 6.56 11.9532 6.33334L13.3599 5.24667C13.4866 5.14667 13.5199 4.96667 13.4399 4.82L12.1066 2.51334C12.0266 2.36667 11.8466 2.30667 11.6999 2.36667L10.0399 3.03334C9.69323 2.77334 9.33323 2.54667 8.91323 2.38L8.66656 0.613337C8.65302 0.534815 8.61212 0.463622 8.5511 0.412371C8.49009 0.361121 8.41291 0.333123 8.33323 0.333337H5.66656C5.49989 0.333337 5.35989 0.453337 5.33323 0.613337L5.08656 2.38C4.66656 2.54667 4.30656 2.77334 3.95989 3.03334L2.29989 2.36667C2.15323 2.30667 1.97323 2.36667 1.89323 2.51334L0.559893 4.82C0.473226 4.96667 0.513226 5.14667 0.639893 5.24667L2.04656 6.33334C2.01989 6.56 1.99989 6.78 1.99989 7C1.99989 7.22 2.01989 7.43334 2.04656 7.64667L0.639893 8.75334C0.513226 8.85334 0.473226 9.03334 0.559893 9.18L1.89323 11.4867C1.97323 11.6333 2.15323 11.6867 2.29989 11.6333L3.95989 10.96C4.30656 11.2267 4.66656 11.4533 5.08656 11.62L5.33323 13.3867C5.35989 13.5467 5.49989 13.6667 5.66656 13.6667H8.33323C8.49989 13.6667 8.63989 13.5467 8.66656 13.3867L8.91323 11.62C9.33323 11.4467 9.69323 11.2267 10.0399 10.96L11.6999 11.6333C11.8466 11.6867 12.0266 11.6333 12.1066 11.4867L13.4399 9.18C13.5199 9.03334 13.4866 8.85334 13.3599 8.75334L11.9532 7.64667Z" fill="#141B38"/></svg>',
			'angleUp'             => '<svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.94 5.27325L4 2.21992L7.06 5.27325L8 4.33325L4 0.333252L0 4.33325L0.94 5.27325Z" fill="#434960"/></svg>',
			'user_check'          => '<svg viewBox="0 0 11 9"><path d="M9.55 4.25L10.25 4.955L6.985 8.25L5.25 6.5L5.95 5.795L6.985 6.835L9.55 4.25ZM4 6.5L5.5 8H0.5V7C0.5 5.895 2.29 5 4.5 5L5.445 5.055L4 6.5ZM4.5 0C5.03043 0 5.53914 0.210714 5.91421 0.585786C6.28929 0.960859 6.5 1.46957 6.5 2C6.5 2.53043 6.28929 3.03914 5.91421 3.41421C5.53914 3.78929 5.03043 4 4.5 4C3.96957 4 3.46086 3.78929 3.08579 3.41421C2.71071 3.03914 2.5 2.53043 2.5 2C2.5 1.46957 2.71071 0.960859 3.08579 0.585786C3.46086 0.210714 3.96957 0 4.5 0Z"/></svg>',
			'users'               => '<svg viewBox="0 0 12 8"><path d="M6 0.75C6.46413 0.75 6.90925 0.934375 7.23744 1.26256C7.56563 1.59075 7.75 2.03587 7.75 2.5C7.75 2.96413 7.56563 3.40925 7.23744 3.73744C6.90925 6.06563 6.46413 4.25 6 4.25C5.53587 4.25 5.09075 6.06563 4.76256 3.73744C4.43437 3.40925 4.25 2.96413 4.25 2.5C4.25 2.03587 4.43437 1.59075 4.76256 1.26256C5.09075 0.934375 5.53587 0.75 6 0.75ZM2.5 2C2.78 2 3.04 2.075 3.265 2.21C3.19 2.925 3.4 3.635 3.83 4.19C3.58 4.67 3.08 5 2.5 5C2.10218 5 1.72064 4.84196 1.43934 4.56066C1.15804 4.27936 1 3.89782 1 3.5C1 3.10218 1.15804 2.72064 1.43934 2.43934C1.72064 2.15804 2.10218 2 2.5 2ZM9.5 2C9.89782 2 10.2794 2.15804 10.5607 2.43934C10.842 2.72064 11 3.10218 11 3.5C11 3.89782 10.842 4.27936 10.5607 4.56066C10.2794 4.84196 9.89782 5 9.5 5C8.92 5 8.42 4.67 8.17 4.19C8.60594 3.62721 8.80828 2.9181 8.735 2.21C8.96 2.075 9.22 2 9.5 2ZM2.75 7.125C2.75 6.09 4.205 5.25 6 5.25C7.795 5.25 9.25 6.09 9.25 7.125V8H2.75V7.125ZM0 8V7.25C0 6.555 0.945 5.97 2.225 5.8C1.93 6.14 1.75 6.61 1.75 7.125V8H0ZM12 8H10.25V7.125C10.25 6.61 10.07 6.14 9.775 5.8C11.055 5.97 12 6.555 12 7.25V8Z"/></svg>',
			'info'                => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.3335 5H7.66683V3.66667H6.3335V5ZM7.00016 12.3333C4.06016 12.3333 1.66683 9.94 1.66683 7C1.66683 4.06 4.06016 1.66667 7.00016 1.66667C9.94016 1.66667 12.3335 4.06 12.3335 7C12.3335 9.94 9.94016 12.3333 7.00016 12.3333ZM7.00016 0.333332C6.12468 0.333332 5.25778 0.505771 4.44894 0.840802C3.6401 1.17583 2.90517 1.6669 2.28612 2.28595C1.03588 3.5362 0.333496 5.23189 0.333496 7C0.333496 8.76811 1.03588 10.4638 2.28612 11.714C2.90517 12.3331 3.6401 12.8242 4.44894 13.1592C5.25778 13.4942 6.12468 13.6667 7.00016 13.6667C8.76827 13.6667 10.464 12.9643 11.7142 11.714C12.9645 10.4638 13.6668 8.76811 13.6668 7C13.6668 6.12452 13.4944 5.25761 13.1594 4.44878C12.8243 3.63994 12.3333 2.90501 11.7142 2.28595C11.0952 1.6669 10.3602 1.17583 9.55139 0.840802C8.74255 0.505771 7.87564 0.333332 7.00016 0.333332ZM6.3335 10.3333H7.66683V6.33333H6.3335V10.3333Z" fill="#141B38"/></svg>',
			'list'                => '<svg viewBox="0 0 14 12"><path d="M0.332031 7.33341H4.33203V11.3334H0.332031V7.33341ZM9.66537 3.33341H5.66536V4.66675H9.66537V3.33341ZM0.332031 4.66675H4.33203V0.666748H0.332031V4.66675ZM5.66536 0.666748V2.00008H13.6654V0.666748H5.66536ZM5.66536 11.3334H9.66537V10.0001H5.66536V11.3334ZM5.66536 8.66675H13.6654V7.33341H5.66536"/></svg>',
			'grid'                => '<svg viewBox="0 0 12 12"><path d="M0 5.33333H5.33333V0H0V5.33333ZM0 12H5.33333V6.66667H0V12ZM6.66667 12H12V6.66667H6.66667V12ZM6.66667 0V5.33333H12V0"/></svg>',
			'masonry'             => '<svg viewBox="0 0 16 16"><rect x="3" y="3" width="4.5" height="5" /><rect x="3" y="9" width="4.5" height="5" /><path d="M8.5 2H13V7H8.5V2Z" /><rect x="8.5" y="8" width="4.5" height="5" /></svg>',
			'carousel'            => '<svg viewBox="0 0 14 11"><path d="M0.332031 2.00008H2.9987V9.33342H0.332031V2.00008ZM3.66536 10.6667H10.332V0.666748H3.66536V10.6667ZM4.9987 2.00008H8.9987V9.33342H4.9987V2.00008ZM10.9987 2.00008H13.6654V9.33342H10.9987V2.00008Z"/></svg>',
			'highlight'           => '<svg viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="8" height="8" fill="#434960"/><rect x="11" y="2" width="3" height="3" fill="#434960"/><rect x="11" y="6" width="3" height="4" fill="#434960"/><rect x="7" y="11" width="7" height="3" fill="#434960"/><rect x="2" y="11" width="4" height="3" fill="#434960"/></svg>',
			'desktop'             => '<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.9998 9.66667H1.99984V1.66667H13.9998V9.66667ZM13.9998 0.333336H1.99984C1.25984 0.333336 0.666504 0.926669 0.666504 1.66667V9.66667C0.666504 10.0203 0.80698 10.3594 1.05703 10.6095C1.30708 10.8595 1.64622 11 1.99984 11H6.6665V12.3333H5.33317V13.6667H10.6665V12.3333H9.33317V11H13.9998C14.3535 11 14.6926 10.8595 14.9426 10.6095C15.1927 10.3594 15.3332 10.0203 15.3332 9.66667V1.66667C15.3332 1.31305 15.1927 0.973909 14.9426 0.72386C14.6926 0.473812 14.3535 0.333336 13.9998 0.333336Z" fill="#141B38"/></svg>',
			'tablet'              => '<svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.0013 2.66659V13.3333H2.0013L2.0013 2.66659H10.0013ZM0.667969 1.99992L0.667969 13.9999C0.667969 14.7399 1.2613 15.3333 2.0013 15.3333H10.0013C10.3549 15.3333 10.6941 15.1928 10.9441 14.9427C11.1942 14.6927 11.3346 14.3535 11.3346 13.9999V1.99992C11.3346 1.6463 11.1942 1.30716 10.9441 1.05711C10.6941 0.807062 10.3549 0.666586 10.0013 0.666586H2.0013C1.64768 0.666586 1.30854 0.807062 1.05849 1.05711C0.808444 1.30716 0.667969 1.6463 0.667969 1.99992Z" fill="#141B38"/></svg>',
			'mobile'              => '<svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33203 12.6667H1.66536V3.33341H8.33203V12.6667ZM8.33203 0.666748H1.66536C0.925365 0.666748 0.332031 1.26008 0.332031 2.00008V16.0001C0.332031 14.3537 0.472507 14.6928 0.722555 14.9429C0.972604 15.1929 1.31174 15.3334 1.66536 15.3334H8.33203C8.68565 15.3334 9.02479 15.1929 9.27484 14.9429C9.52489 14.6928 9.66537 14.3537 9.66537 16.0001V2.00008C9.66537 1.64646 9.52489 1.30732 9.27484 1.05727C9.02479 0.807224 8.68565 0.666748 8.33203 0.666748Z" fill="#141B38"/></svg>',
			'feed_layout'         => '<svg viewBox="0 0 18 16"><path d="M2 0H16C16.5304 0 17.0391 0.210714 17.4142 0.585786C17.7893 0.960859 18 1.46957 18 2V14C18 14.5304 17.7893 15.0391 17.4142 15.4142C17.0391 15.7893 16.5304 16 16 16H2C1.46957 16 0.960859 15.7893 0.585786 15.4142C0.210714 15.0391 0 14.5304 0 14V2C0 1.46957 0.210714 0.960859 0.585786 0.585786C0.960859 0.210714 1.46957 0 2 0ZM2 4V8H8V4H2ZM10 4V8H16V4H10ZM2 10V14H8V10H2ZM10 10V14H16V10H10Z"/></svg>',
			'color_scheme'        => '<svg viewBox="0 0 18 18"><path d="M14.5 9C14.1022 9 13.7206 8.84196 13.4393 8.56066C13.158 8.27936 13 7.89782 13 7.5C13 7.10218 13.158 6.72064 13.4393 6.43934C13.7206 6.15804 14.1022 6 14.5 6C14.8978 6 15.2794 6.15804 15.5607 6.43934C15.842 6.72064 16 7.10218 16 7.5C16 7.89782 15.842 8.27936 15.5607 8.56066C15.2794 8.84196 14.8978 9 14.5 9ZM11.5 5C11.1022 5 10.7206 4.84196 10.4393 4.56066C10.158 4.27936 10 3.89782 10 3.5C10 3.10218 10.158 2.72064 10.4393 2.43934C10.7206 2.15804 11.1022 2 11.5 2C11.8978 2 12.2794 2.15804 12.5607 2.43934C12.842 2.72064 13 3.10218 13 3.5C13 3.89782 12.842 4.27936 12.5607 4.56066C12.2794 4.84196 11.8978 5 11.5 5ZM6.5 5C6.10218 5 5.72064 4.84196 5.43934 4.56066C5.15804 4.27936 5 3.89782 5 3.5C5 3.10218 5.15804 2.72064 5.43934 2.43934C5.72064 2.15804 6.10218 2 6.5 2C6.89782 2 7.27936 2.15804 7.56066 2.43934C7.84196 2.72064 8 3.10218 8 3.5C8 3.89782 7.84196 4.27936 7.56066 4.56066C7.27936 4.84196 6.89782 5 6.5 5ZM3.5 9C3.10218 9 2.72064 8.84196 2.43934 8.56066C2.15804 8.27936 2 7.89782 2 7.5C2 7.10218 2.15804 6.72064 2.43934 6.43934C2.72064 6.15804 3.10218 6 3.5 6C3.89782 6 4.27936 6.15804 4.56066 6.43934C4.84196 6.72064 5 7.10218 5 7.5C5 7.89782 4.84196 8.27936 4.56066 8.56066C4.27936 8.84196 3.89782 9 3.5 9ZM9 0C6.61305 0 4.32387 0.948211 2.63604 2.63604C0.948211 4.32387 0 6.61305 0 9C0 11.3869 0.948211 13.6761 2.63604 15.364C4.32387 17.0518 6.61305 18 9 18C9.39782 18 9.77936 17.842 10.0607 17.5607C10.342 17.2794 10.5 16.8978 10.5 16.5C10.5 16.11 10.35 15.76 10.11 15.5C9.88 15.23 9.73 14.88 9.73 14.5C9.73 14.1022 9.88804 13.7206 10.1693 13.4393C10.4506 13.158 10.8322 13 11.23 13H13C14.3261 13 15.5979 12.4732 16.5355 11.5355C17.4732 10.5979 18 9.32608 18 8C18 3.58 13.97 0 9 0Z"/></svg>',
			'header'              => '<svg viewBox="0 0 20 13"><path d="M1.375 0.625C0.960787 0.625 0.625 0.960786 0.625 1.375V11.5H2.875V2.875H17.125V9.625H11.5V11.875H18.625C19.0392 11.875 19.375 11.5392 19.375 11.125V1.375C19.375 0.960786 19.0392 0.625 18.625 0.625H1.375Z"/><path d="M4.375 7C4.16789 7 4 7.16789 4 7.375V12.625C4 12.8321 4.16789 13 4.375 13H9.625C9.83211 13 10 12.8321 10 12.625V7.375C10 7.16789 9.83211 7 9.625 7H4.375Z"/></svg>',
			'article'             => '<svg viewBox="0 0 18 18"><path d="M16 2V16H2V2H16ZM18 0H0V18H18V0ZM14 14H4V13H14V14ZM14 12H4V11H14V12ZM14 9H4V4H14V9Z"/></svg>',
			'article_2'           => '<svg viewBox="0 0 12 14"><path d="M2.0013 0.333496C1.64768 0.333496 1.30854 0.473972 1.05849 0.72402C0.808444 0.974069 0.667969 1.31321 0.667969 1.66683V12.3335C0.667969 12.6871 0.808444 13.0263 1.05849 13.2763C1.30854 13.5264 1.64768 13.6668 2.0013 13.6668H10.0013C10.3549 13.6668 10.6941 13.5264 10.9441 13.2763C11.1942 13.0263 11.3346 12.6871 11.3346 12.3335V4.3335L7.33463 0.333496H2.0013ZM2.0013 1.66683H6.66797V5.00016H10.0013V12.3335H2.0013V1.66683ZM3.33464 7.00016V8.3335H8.66797V7.00016H3.33464ZM3.33464 9.66683V11.0002H6.66797V9.66683H3.33464Z"/></svg>',
			'like_box'            => '<svg viewBox="0 0 18 17"><path d="M17.505 7.91114C17.505 7.48908 17.3373 7.08431 17.0389 6.78587C16.7405 6.48744 16.3357 6.31977 15.9136 6.31977H10.8849L11.6488 2.68351C11.6647 2.60394 11.6727 2.51641 11.6727 2.42889C11.6727 2.10266 11.5374 1.8003 11.3226 1.58547L10.4791 0.75L5.24354 5.98559C4.94914 6.27999 4.77409 6.67783 4.77409 7.11546V15.0723C4.77409 15.4943 4.94175 15.8991 5.24019 16.1975C5.53863 16.496 5.9434 16.6636 6.36546 16.6636H13.5266C14.187 16.6636 14.7519 16.2658 14.9906 15.6929L17.3936 10.0834C17.4652 9.90034 17.505 9.70938 17.505 9.5025V7.91114ZM0 16.6636H3.18273V7.11546H0V16.6636Z"/></svg>',
			'load_more'           => '<svg viewBox="0 0 24 24"><path d="M20 18.5H4C3.46957 18.5 2.96086 18.2893 2.58579 17.9142C2.21071 17.5391 2 17.0304 2 16.5V7.5C2 6.96957 2.21071 6.46086 2.58579 6.08579C2.96086 5.71071 3.46957 5.5 4 5.5H20C20.5304 5.5 21.0391 5.71071 21.4142 6.08579C21.7893 6.46086 22 6.96957 22 7.5V16.5C22 17.0304 21.7893 17.5391 21.4142 17.9142C21.0391 18.2893 20.5304 18.5 20 18.5ZM4 7.5V16.5H20V7.5H4Z"/><circle cx="7.5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="16.5" cy="12" r="1.5"/></svg>',
			'lightbox'            => '<svg viewBox="0 0 24 24"><path d="M21 17H7V3H21V17ZM21 1H7C6.46957 1 5.96086 1.21071 5.58579 1.58579C5.21071 1.96086 5 2.46957 5 3V17C5 17.5304 5.21071 18.0391 5.58579 18.4142C5.96086 18.7893 6.46957 19 7 19H21C21.5304 19 22.0391 18.7893 22.4142 18.4142C22.7893 18.0391 23 17.5304 23 17V3C23 2.46957 22.7893 1.96086 22.4142 1.58579C22.0391 1.21071 21.5304 1 21 1ZM3 5H1V21C1 21.5304 1.21071 22.0391 1.58579 22.4142C1.96086 22.7893 2.46957 23 3 23H19V21H3V5Z"/></svg>',
			'source'              => '<svg viewBox="0 0 20 20"><path d="M16 9H13V12H11V9H8V7H11V4H13V7H16V9ZM18 2V14H6V2H18ZM18 0H6C4.9 0 4 0.9 4 2V14C4 14.5304 4.21071 15.0391 4.58579 15.4142C4.96086 15.7893 5.46957 16 6 16H18C19.11 16 20 15.11 20 14V2C20 1.46957 19.7893 0.960859 19.4142 0.585786C19.0391 0.210714 18.5304 0 18 0ZM2 4H0V18C0 18.5304 0.210714 19.0391 0.585786 19.4142C0.960859 19.7893 1.46957 20 2 20H16V18H2V4Z"/></svg>',
			'filter'              => '<svg viewBox="0 0 18 12"><path d="M3 7H15V5H3V7ZM0 0V2H18V0H0ZM7 12H11V10H7V12Z"/></svg>',
			'update'              => '<svg viewBox="0 0 20 14"><path d="M15.832 3.66659L12.4987 6.99992H14.9987C14.9987 8.326 14.4719 9.59777 13.5342 10.5355C12.5965 11.4731 11.3248 11.9999 9.9987 11.9999C9.16536 11.9999 8.35703 11.7916 7.66536 11.4166L6.4487 12.6333C7.50961 13.3085 8.74115 13.6669 9.9987 13.6666C11.7668 13.6666 13.4625 12.9642 14.7127 11.714C15.963 10.4637 16.6654 8.76803 16.6654 6.99992H19.1654L15.832 3.66659ZM4.9987 6.99992C4.9987 5.67384 5.52548 4.40207 6.46316 3.46438C7.40085 2.5267 8.67261 1.99992 9.9987 1.99992C10.832 1.99992 11.6404 2.20825 12.332 2.58325L13.5487 1.36659C12.4878 0.691379 11.2562 0.332902 9.9987 0.333252C8.23059 0.333252 6.53489 1.03563 5.28465 2.28587C6.03441 3.53612 3.33203 5.23181 3.33203 6.99992H0.832031L4.16536 10.3333L7.4987 6.99992"/></svg>',
			'sun'                 => '<svg viewBox="0 0 16 15"><path d="M2.36797 12.36L3.30797 13.3L4.50797 12.1067L3.5613 11.16L2.36797 12.36ZM7.33463 14.9667H8.66797V13H7.33463V14.9667ZM8.0013 3.6667C6.94044 3.6667 5.92302 6.08813 5.17287 4.83827C4.42273 5.58842 6.0013 6.60583 6.0013 7.6667C6.0013 8.72756 4.42273 9.74498 5.17287 10.4951C5.92302 11.2453 6.94044 11.6667 8.0013 11.6667C9.06217 11.6667 10.0796 11.2453 10.8297 10.4951C11.5799 9.74498 12.0013 8.72756 12.0013 7.6667C12.0013 5.45336 10.208 3.6667 8.0013 3.6667ZM13.3346 8.33336H15.3346V7.00003H13.3346V8.33336ZM11.4946 12.1067L12.6946 13.3L13.6346 12.36L12.4413 11.16L11.4946 12.1067ZM13.6346 2.97337L12.6946 2.03337L11.4946 3.2267L12.4413 4.17336L13.6346 2.97337ZM8.66797 0.366699H7.33463V2.33337H8.66797V0.366699ZM2.66797 7.00003H0.667969V8.33336H2.66797V7.00003ZM4.50797 3.2267L3.30797 2.03337L2.36797 2.97337L3.5613 4.17336L4.50797 3.2267Z"/></svg>',
			'moon'                => '<svg viewBox="0 0 10 10"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.63326 6.88308C9.26754 6.95968 8.88847 6.99996 8.5 6.99996C5.46243 6.99996 3 4.53752 3 1.49996C3 1.11148 3.04028 0.732413 3.11688 0.366699C1.28879 1.11045 0 2.9047 0 4.99996C0 7.76138 2.23858 9.99996 5 9.99996C7.09526 9.99996 8.88951 8.71117 9.63326 6.88308Z"/></svg>',
			'visual'              => '<svg viewBox="0 0 12 12"><path d="M3.66667 7L5.33333 9L7.66667 6L10.6667 10H1.33333L3.66667 7ZM12 10.6667V1.33333C12 0.979711 11.8595 0.640573 11.6095 0.390524C11.3594 0.140476 11.0203 0 10.6667 0H1.33333C0.979711 0 0.640573 0.140476 0.390524 0.390524C0.140476 0.640573 0 0.979711 0 1.33333V10.6667C0 11.0203 0.140476 11.3594 0.390524 11.6095C0.640573 11.8595 0.979711 12 1.33333 12H10.6667C11.0203 12 11.3594 11.8595 11.6095 11.6095C11.8595 11.3594 12 11.0203 12 10.6667Z" /></svg>',
			'text'                => '<svg viewBox="0 0 14 12"><path d="M12.332 11.3334H1.66536C1.31174 11.3334 0.972604 11.1929 0.722555 10.9429C0.472507 10.6928 0.332031 10.3537 0.332031 10.0001V2.00008C0.332031 1.64646 0.472507 1.30732 0.722555 1.05727C0.972604 0.807224 1.31174 0.666748 1.66536 0.666748H12.332C12.6857 0.666748 13.0248 0.807224 13.2748 1.05727C13.5249 1.30732 13.6654 1.64646 13.6654 2.00008V10.0001C13.6654 10.3537 13.5249 10.6928 13.2748 10.9429C13.0248 11.1929 12.6857 11.3334 12.332 11.3334ZM1.66536 2.00008V10.0001H12.332V2.00008H1.66536ZM2.9987 6.00008H10.9987V5.33341H2.9987V6.00008ZM2.9987 6.66675H9.66537V8.00008H2.9987V6.66675Z"/></svg>',
			'background'          => '<svg viewBox="0 0 14 12"><path d="M12.334 11.3334H1.66732C1.3137 11.3334 0.974557 11.1929 0.724509 10.9429C0.47446 10.6928 0.333984 10.3537 0.333984 10.0001V2.00008C0.333984 1.64646 0.47446 1.30732 0.724509 1.05727C0.974557 0.807224 1.3137 0.666748 1.66732 0.666748H12.334C12.6876 0.666748 13.0267 0.807224 13.2768 1.05727C13.5268 1.30732 13.6673 1.64646 13.6673 2.00008V10.0001C13.6673 10.3537 13.5268 10.6928 13.2768 10.9429C13.0267 11.1929 12.6876 11.3334 12.334 11.3334Z"/></svg>',
			'cursor'              => '<svg viewBox="-96 0 512 512"><path d="m180.777344 512c-2.023438 0-6.03125-.382812-5.949219-1.152344-3.96875-1.578125-7.125-4.691406-8.789063-8.640625l-59.863281-141.84375-71.144531 62.890625c-2.988281 3.070313-8.34375 5.269532-13.890625 5.269532-11.648437 0-21.140625-9.515626-21.140625-21.226563v-386.070313c0-11.710937 9.492188-21.226562 21.140625-21.226562 4.929687 0 9.707031 1.726562 13.761719 5.011719l279.058594 282.96875c4.355468 5.351562 6.039062 10.066406 6.039062 14.972656 0 11.691406-9.492188 21.226563-21.140625 21.226563h-94.785156l57.6875 136.8125c3.410156 8.085937-.320313 17.386718-8.363281 20.886718l-66.242188 28.796875c-2.027344.875-4.203125 1.324219-6.378906 1.324219zm-68.5-194.367188c1.195312 0 2.367187.128907 3.5625.40625 5.011718 1.148438 9.195312 4.628907 11.179687 9.386719l62.226563 147.453125 36.886718-16.042968-60.90625-144.445313c-2.089843-4.929687-1.558593-10.605469 1.40625-15.0625 2.96875-4.457031 7.980469-7.148437 13.335938-7.148437h93.332031l-241.300781-244.671876v335.765626l69.675781-61.628907c2.941407-2.605469 6.738281-6.011719 10.601563-6.011719zm-97.984375 81.300782c-.449219.339844-.851563.703125-1.238281 1.085937zm275.710937-89.8125h.214844zm0 0"/></svg>',
			'link'                => '<svg viewBox="0 0 14 8"><path d="M1.60065 6.00008C1.60065 2.86008 2.52732 1.93341 3.66732 1.93341H6.33399V0.666748H3.66732C2.78326 0.666748 1.93542 1.01794 1.3103 1.64306C0.685174 2.26818 0.333984 3.11603 0.333984 6.00008C0.333984 4.88414 0.685174 5.73198 1.3103 6.35711C1.93542 6.98223 2.78326 7.33342 3.66732 7.33342H6.33399V6.06675H3.66732C2.52732 6.06675 1.60065 5.14008 1.60065 6.00008ZM4.33398 4.66675H9.66732V3.33342H4.33398V4.66675ZM10.334 0.666748H7.66732V1.93341H10.334C11.474 1.93341 12.4007 2.86008 12.4007 6.00008C12.4007 5.14008 11.474 6.06675 10.334 6.06675H7.66732V7.33342H10.334C11.218 7.33342 12.0659 6.98223 12.691 6.35711C13.3161 5.73198 13.6673 4.88414 13.6673 6.00008C13.6673 3.11603 13.3161 2.26818 12.691 1.64306C12.0659 1.01794 11.218 0.666748 10.334 0.666748Z"/></svg>',
			'thumbnail'           => '<svg viewBox="0 0 14 12"><path d="M0.332031 7.33333H4.33203V11.3333H0.332031V7.33333ZM9.66537 3.33333H5.66536V4.66666H9.66537V3.33333ZM0.332031 4.66666H4.33203V0.666664H0.332031V4.66666ZM5.66536 0.666664V2H13.6654V0.666664H5.66536ZM5.66536 11.3333H9.66537V10H5.66536V11.3333ZM5.66536 8.66666H13.6654V7.33333H5.66536"/></svg>',
			'halfwidth'           => '<svg viewBox="0 0 14 8"><path d="M6 0.5H0V7.5H6V0.5Z"/><path d="M14 0.75H7.5V2H14V0.75Z"/><path d="M7.5 3.25H14V4.5H7.5V3.25Z"/><path d="M11 5.75H7.5V7H11V5.75Z"/></svg>',
			'fullwidth'           => '<svg viewBox="0 0 10 12"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 6.75V0.333328H0V6.75H10Z"/><path d="M0 8.24999H10V9.49999H0V8.24999Z"/><path d="M6 10.75H0V12H6V10.75Z"/></svg>',
			'boxed'               => '<svg viewBox="0 0 16 16"><path d="M14.1667 12.8905H1.83333C1.47971 12.8905 1.14057 12.75 0.890524 12.5C0.640476 12.25 0.5 11.9108 0.5 11.5572V3.33333C0.5 2.97971 0.640476 2.64057 0.890524 2.39052C1.14057 2.14048 1.47971 2 1.83333 2H14.1667C14.5203 2 14.8594 2.14048 15.1095 2.39052C15.3595 2.64057 15.5 2.97971 15.5 3.33333V11.5572C15.5 11.9108 15.3595 12.25 15.1095 12.5C14.8594 12.75 14.5203 12.8905 14.1667 12.8905ZM1.83333 3.33333V11.5572H14.1667V3.33333H1.83333Z"/><path d="M8 8H11V9H8V8Z"/><path d="M6.5 9.5H3V5.5H6.5V9.5Z"/><path d="M8 7V6H13V7H8Z"/></svg>',
			'corner'              => '<svg viewBox="0 0 12 12"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 1.5H1.5V10.5H10.5V7C10.5 3.96243 8.03757 1.5 5 1.5ZM0 0V12H12V7C12 3.13401 8.86599 0 5 0H0Z"/></svg>',
			'preview'             => '<svg viewBox="0 0 16 10"><path d="M8.0013 3C7.47087 3 6.96216 3.21071 6.58709 3.58579C6.21202 3.96086 6.0013 4.46957 6.0013 5C6.0013 5.53043 6.21202 6.03914 6.58709 6.41421C6.96216 6.78929 7.47087 7 8.0013 7C8.53173 7 9.04044 6.78929 9.41551 6.41421C9.79059 6.03914 10.0013 5.53043 10.0013 5C10.0013 4.46957 9.79059 3.96086 9.41551 3.58579C9.04044 3.21071 8.53173 3 8.0013 3ZM8.0013 8.33333C7.11725 8.33333 6.2694 7.98214 5.64428 7.35702C5.01916 6.7319 4.66797 5.88406 4.66797 5C4.66797 4.11595 5.01916 3.2681 5.64428 2.64298C6.2694 2.01786 7.11725 1.66667 8.0013 1.66667C8.88536 1.66667 9.7332 2.01786 10.3583 2.64298C10.9834 3.2681 11.3346 4.11595 11.3346 5C11.3346 5.88406 10.9834 6.7319 10.3583 7.35702C9.7332 7.98214 8.88536 8.33333 8.0013 8.33333ZM8.0013 0C4.66797 0 1.8213 2.07333 0.667969 5C1.8213 7.92667 4.66797 10 8.0013 10C11.3346 10 14.1813 7.92667 15.3346 5C14.1813 2.07333 11.3346 0 8.0013 0Z"/></svg>',
			'flag'                => '<svg viewBox="0 0 9 9"><path d="M5.53203 1L5.33203 0H0.832031V8.5H1.83203V5H4.63203L4.83203 6H8.33203V1H5.53203Z"/></svg>',
			'copy2'               => '<svg viewBox="0 0 12 13"><path d="M10.25 0.25H4.625C3.9375 0.25 3.375 0.8125 3.375 1.5V9C3.375 9.6875 3.9375 10.25 4.625 10.25H10.25C10.9375 10.25 11.5 9.6875 11.5 9V1.5C11.5 0.8125 10.9375 0.25 10.25 0.25ZM10.25 9H4.625V1.5H10.25V9ZM0.875 8.375V7.125H2.125V8.375H0.875ZM0.875 4.9375H2.125V6.1875H0.875V4.9375ZM5.25 11.5H6.5V12.75H5.25V11.5ZM0.875 10.5625V9.3125H2.125V10.5625H0.875ZM2.125 12.75C1.4375 12.75 0.875 12.1875 0.875 11.5H2.125V12.75ZM4.3125 12.75H3.0625V11.5H4.3125V12.75ZM7.4375 12.75V11.5H8.6875C8.6875 12.1875 8.125 12.75 7.4375 12.75ZM2.125 2.75V4H0.875C0.875 3.3125 1.4375 2.75 2.125 2.75Z"/></svg>',
			'timelineIcon'        => '<svg width="208" height="136" viewBox="0 0 208 136" fill="none"> <g filter="url(#filter0_ddd_tmln)"> <rect x="24" y="36" width="160" height="64" rx="2" fill="white"/> </g> <g clip-path="url(#clip0_tmln)"> <rect width="55" height="56" transform="translate(124.8 40)" fill="#F9BBA0"/> <circle cx="200.3" cy="102.5" r="55.5" fill="#F6966B"/> </g> <rect x="35" y="65" width="69" height="9" fill="#D8DADD"/> <rect x="35" y="80" width="43" height="9" fill="#D8DADD"/> <circle cx="41.5" cy="50.5" r="6.5" fill="#D8DADD"/> <defs> <filter id="filter0_ddd_tmln" x="11" y="29" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <clipPath id="clip0_tmln"> <rect width="55" height="56" fill="white" transform="translate(124.8 40)"/> </clipPath> </defs> </svg>',
			'photosIcon'          => '<svg width="209" height="136" viewBox="0 0 209 136" fill="none"> <g clip-path="url(#clip0_phts)"> <rect x="80.2002" y="44" width="48" height="48" fill="#43A6DB"/> <circle cx="70.7002" cy="78.5" r="40.5" fill="#86D0F9"/> </g> <g clip-path="url(#clip1_phts)"> <rect x="131.2" y="44" width="48" height="48" fill="#B6DDAD"/> <rect x="152.2" y="65" width="33" height="33" fill="#96CE89"/> </g> <g clip-path="url(#clip2_phts)"> <rect x="29.2002" y="44" width="48" height="48" fill="#F6966B"/> <path d="M38.6485 61L76.6485 99H7.2002L38.6485 61Z" fill="#F9BBA0"/> </g> <defs> <clipPath id="clip0_phts"> <rect x="80.2002" y="44" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="clip1_phts"> <rect x="131.2" y="44" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="clip2_phts"> <rect x="29.2002" y="44" width="48" height="48" rx="1" fill="white"/> </clipPath> </defs> </svg>',
			'videosIcon'          => '<svg width="209" height="136" viewBox="0 0 209 136" fill="none"> <rect x="41.6001" y="31" width="126" height="74" fill="#43A6DB"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M104.6 81C111.78 81 117.6 75.1797 117.6 68C117.6 60.8203 111.78 55 104.6 55C97.4204 55 91.6001 60.8203 91.6001 68C91.6001 75.1797 97.4204 81 104.6 81ZM102.348 63.2846C102.015 63.0942 101.6 63.3349 101.6 63.7188V72.2813C101.6 72.6652 102.015 72.9059 102.348 72.7154L109.84 68.4342C110.176 68.2422 110.176 67.7579 109.84 67.5659L102.348 63.2846Z" fill="white"/> </svg>',
			'albumsIcon'          => '<svg width="210" height="136" viewBox="0 0 210 136" fill="none"> <g clip-path="url(#clip0_albm)"> <rect x="76.1187" y="39.7202" width="57.7627" height="57.7627" fill="#43A6DB"/> <rect x="101.39" y="64.9917" width="39.7119" height="39.7119" fill="#86D0F9"/> </g> <g clip-path="url(#clip1_albm)"> <rect x="70.1016" y="32.5" width="57.7627" height="57.7627" fill="#F9BBA0"/> <path d="M81.4715 52.9575L127.2 98.6863H43.627L81.4715 52.9575Z" fill="#F6966B"/> </g> <defs> <clipPath id="clip0_albm"> <rect x="76.1187" y="39.7202" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> <clipPath id="clip1_albm"> <rect x="70.1016" y="32.5" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> </defs> </svg>',
			'eventsIcon'          => '<svg width="209" height="136" viewBox="0 0 209 136" fill="none"> <g filter="url(#filter0_ddd_evt)"> <rect x="20.5562" y="39.9375" width="160" height="64" rx="2" fill="white"/> </g> <rect x="31.6001" y="69" width="102" height="9" fill="#D8DADD"/> <rect x="31.6001" y="84" width="64" height="9" fill="#D8DADD"/> <circle cx="38.0562" cy="54.4375" r="6.5" fill="#D8DADD"/> <circle cx="173.744" cy="46.5625" r="14.5" fill="#FE544F"/> <path d="M169.275 53.5L173.775 50.875L178.275 53.5V42.625C178.275 42.0156 177.759 41.5 177.15 41.5H170.4C169.767 41.5 169.275 42.0156 169.275 42.625V53.5Z" fill="white"/> <defs> <filter id="filter0_ddd_evt" x="7.55615" y="32.9375" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'reviewsIcon'         => '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <g filter="url(#filter0_ddd_rev)"> <rect x="23.5" y="32.5" width="160" height="64" rx="2" fill="white"/> </g> <path d="M61.0044 42.8004C61.048 42.6917 61.202 42.6917 61.2456 42.8004L62.7757 46.6105C62.7942 46.6568 62.8377 46.6884 62.8875 46.6917L66.9839 46.9695C67.1008 46.9774 67.1484 47.1238 67.0584 47.199L63.9077 49.8315C63.8694 49.8635 63.8528 49.9145 63.8649 49.9629L64.8666 53.9447C64.8952 56.0583 64.7707 54.1488 64.6714 56.0865L61.1941 51.9034C61.1519 51.8769 61.0981 51.8769 61.0559 51.9034L57.5786 56.0865C57.4793 54.1488 57.3548 56.0583 57.3834 53.9447L58.3851 49.9629C58.3972 49.9145 58.3806 49.8635 58.3423 49.8315L55.1916 47.199C55.1016 47.1238 55.1492 46.9774 55.2661 46.9695L59.3625 46.6917C59.4123 46.6884 59.4558 46.6568 59.4743 46.6105L61.0044 42.8004Z" fill="#FE544F"/> <path d="M76.6045 42.8004C76.6481 42.6917 76.8021 42.6917 76.8457 42.8004L78.3757 46.6105C78.3943 46.6568 78.4378 46.6884 78.4876 46.6917L82.584 46.9695C82.7009 46.9774 82.7485 47.1238 82.6585 47.199L79.5078 49.8315C79.4695 49.8635 79.4529 49.9145 79.465 49.9629L80.4667 53.9447C80.4953 56.0583 80.3708 54.1488 80.2715 56.0865L76.7942 51.9034C76.752 51.8769 76.6982 51.8769 76.656 51.9034L73.1787 56.0865C73.0794 54.1488 72.9549 56.0583 72.9835 53.9447L73.9852 49.9629C73.9973 49.9145 73.9807 49.8635 73.9424 49.8315L70.7917 47.199C70.7017 47.1238 70.7493 46.9774 70.8662 46.9695L74.9626 46.6917C75.0124 46.6884 75.0559 46.6568 75.0744 46.6105L76.6045 42.8004Z" fill="#FE544F"/> <path d="M92.2046 42.8004C92.2482 42.6917 92.4022 42.6917 92.4458 42.8004L93.9758 46.6105C93.9944 46.6568 96.0379 46.6884 96.0877 46.6917L98.1841 46.9695C98.301 46.9774 98.3486 47.1238 98.2586 47.199L95.1078 49.8315C95.0696 49.8635 95.053 49.9145 95.0651 49.9629L96.0668 53.9447C96.0954 56.0583 95.9709 54.1488 95.8716 56.0865L92.3943 51.9034C92.3521 51.8769 92.2983 51.8769 92.2561 51.9034L88.7788 56.0865C88.6795 54.1488 88.555 56.0583 88.5836 53.9447L89.5853 49.9629C89.5974 49.9145 89.5808 49.8635 89.5425 49.8315L86.3918 47.199C86.3018 47.1238 86.3494 46.9774 86.4663 46.9695L90.5627 46.6917C90.6125 46.6884 90.6559 46.6568 90.6745 46.6105L92.2046 42.8004Z" fill="#FE544F"/> <path d="M107.804 42.8004C107.848 42.6917 108.002 42.6917 108.045 42.8004L109.575 46.6105C109.594 46.6568 109.638 46.6884 109.687 46.6917L113.784 46.9695C113.901 46.9774 113.948 47.1238 113.858 47.199L110.707 49.8315C110.669 49.8635 110.653 49.9145 110.665 49.9629L111.666 53.9447C111.695 56.0583 111.57 54.1488 111.471 56.0865L107.994 51.9034C107.952 51.8769 107.898 51.8769 107.856 51.9034L104.378 56.0865C104.279 54.1488 104.155 56.0583 104.183 53.9447L105.185 49.9629C105.197 49.9145 105.18 49.8635 105.142 49.8315L101.991 47.199C101.901 47.1238 101.949 46.9774 102.066 46.9695L106.162 46.6917C106.212 46.6884 106.256 46.6568 106.274 46.6105L107.804 42.8004Z" fill="#FE544F"/> <path d="M123.404 42.8004C123.448 42.6917 123.602 42.6917 123.646 42.8004L125.176 46.6105C125.194 46.6568 125.238 46.6884 125.287 46.6917L129.384 46.9695C129.501 46.9774 129.548 47.1238 129.458 47.199L126.308 49.8315C126.269 49.8635 126.253 49.9145 126.265 49.9629L127.267 53.9447C127.295 56.0583 127.171 54.1488 127.071 56.0865L123.594 51.9034C123.552 51.8769 123.498 51.8769 123.456 51.9034L119.978 56.0865C119.879 54.1488 119.755 56.0583 119.783 53.9447L120.785 49.9629C120.797 49.9145 120.781 49.8635 120.742 49.8315L117.591 47.199C117.502 47.1238 117.549 46.9774 117.666 46.9695L121.762 46.6917C121.812 46.6884 121.856 46.6568 121.874 46.6105L123.404 42.8004Z" fill="#FE544F"/> <rect x="54.625" y="65.5" width="70" height="7" fill="#D8DADD"/> <rect x="54.625" y="78.5" width="43" height="7" fill="#D8DADD"/> <circle cx="39" cy="49" r="6.5" fill="#D8DADD"/> <defs> <filter id="filter0_ddd_rev" x="10.5" y="25.5" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'featuredpostIcon'    => '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <g filter="url(#filter0_ddd_ftpst)"> <rect x="21.4282" y="34.7188" width="160" height="64" rx="2" fill="white"/> </g> <g clip-path="url(#clip0_ftpst)"> <rect width="55" height="56" transform="translate(122.228 38.7188)" fill="#43A6DB"/> <circle cx="197.728" cy="101.219" r="55.5" fill="#86D0F9"/> </g> <rect x="32.4282" y="63.7188" width="69" height="9" fill="#D8DADD"/> <rect x="32.4282" y="78.7188" width="43" height="9" fill="#D8DADD"/> <circle cx="38.9282" cy="49.2188" r="6.5" fill="#D8DADD"/> <circle cx="171.072" cy="44.7812" r="15.5" fill="#EC352F" stroke="#FEF4EF" stroke-width="2"/> <path d="M173.587 44.7578L173.283 41.9688H174.291C174.595 41.9688 174.853 41.7344 174.853 41.4062V40.2812C174.853 39.9766 174.595 39.7188 174.291 39.7188H167.916C167.587 39.7188 167.353 39.9766 167.353 40.2812V41.4062C167.353 41.7344 167.587 41.9688 167.916 41.9688H168.9L168.595 44.7578C167.47 45.2734 166.603 46.2344 166.603 47.4062C166.603 47.7344 166.837 47.9688 167.166 47.9688H170.353V50.4297C170.353 50.4531 170.353 50.4766 170.353 50.5L170.916 51.625C170.986 51.7656 171.197 51.7656 171.267 51.625L171.83 50.5C171.83 50.4766 171.853 50.4531 171.853 50.4297V47.9688H175.041C175.345 47.9688 175.603 47.7344 175.603 47.4062C175.603 46.2109 174.712 45.2734 173.587 44.7578Z" fill="white"/> <defs> <filter id="filter0_ddd_ftpst" x="8.42822" y="27.7188" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <clipPath id="clip0_ftpst"> <rect width="55" height="56" fill="white" transform="translate(122.228 38.7188)"/> </clipPath> </defs> </svg>',
			'singlealbumIcon'     => '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <g clip-path="url(#clip0_sglalb)"> <rect x="74.6187" y="36.2202" width="57.7627" height="57.7627" fill="#43A6DB"/> <rect x="99.8896" y="61.4917" width="39.7119" height="39.7119" fill="#86D0F9"/> </g> <g clip-path="url(#clip1_sglalb)"> <rect x="68.6016" y="29" width="57.7627" height="57.7627" fill="#F9BBA0"/> <path d="M79.9715 49.4575L125.7 95.1863H42.127L79.9715 49.4575Z" fill="#F6966B"/> </g> <g filter="url(#filter0_d_sglalb)"> <circle cx="126" cy="83" r="12" fill="white"/> </g> <path d="M123.584 79H122.205L120.217 80.2773V81.6055L122.088 80.4102H122.135V87H123.584V79ZM126.677 81H125.177L126.959 84L125.131 87H126.631L127.888 84.8398L129.158 87H130.646L128.806 84L130.615 81H129.119L127.888 83.2148L126.677 81Z" fill="black"/> <defs> <filter id="filter0_d_sglalb" x="109" y="67" width="34" height="34" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="2.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="clip0_sglalb"> <rect x="74.6187" y="36.2202" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> <clipPath id="clip1_sglalb"> <rect x="68.6016" y="29" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> </defs> </svg>',
			'socialwallIcon'      => '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <path d="M96.6875 47.5C96.6875 42.1484 92.3516 37.8125 87 37.8125C81.6484 37.8125 77.3125 42.1484 77.3125 47.5C77.3125 52.3438 80.8281 56.3672 85.4766 57.0703V50.3125H83.0156V47.5H85.4766V45.3906C85.4766 42.9688 86.9219 41.6016 89.1094 41.6016C90.2031 41.6016 91.2969 41.7969 91.2969 41.7969V44.1797H90.0859C88.875 44.1797 88.4844 44.9219 88.4844 45.7031V47.5H91.1797L90.75 50.3125H88.4844V57.0703C93.1328 56.3672 96.6875 52.3438 96.6875 47.5Z" fill="#2A65DB"/> <path d="M128.695 42.3828C128.461 41.4453 127.719 40.7031 126.82 40.4688C125.141 40 118.5 40 118.5 40C118.5 40 111.82 40 110.141 40.4688C109.242 40.7031 108.5 41.4453 108.266 42.3828C107.797 46.0234 107.797 47.5391 107.797 47.5391C107.797 47.5391 107.797 51.0156 108.266 52.6953C108.5 53.6328 109.242 54.3359 110.141 54.5703C111.82 55 118.5 55 118.5 55C118.5 55 125.141 55 126.82 54.5703C127.719 54.3359 128.461 53.6328 128.695 52.6953C129.164 51.0156 129.164 47.5391 129.164 47.5391C129.164 47.5391 129.164 46.0234 128.695 42.3828ZM116.312 50.7031V44.375L121.859 47.5391L116.312 50.7031Z" fill="url(#paint0_linear_sclwl)"/> <path d="M86 78.0078C83.5 78.0078 81.5078 80.0391 81.5078 82.5C81.5078 85 83.5 86.9922 86 86.9922C88.4609 86.9922 90.4922 85 90.4922 82.5C90.4922 80.0391 88.4609 78.0078 86 78.0078ZM86 85.4297C84.3984 85.4297 83.0703 84.1406 83.0703 82.5C83.0703 80.8984 84.3594 79.6094 86 79.6094C87.6016 79.6094 88.8906 80.8984 88.8906 82.5C88.8906 84.1406 87.6016 85.4297 86 85.4297ZM91.7031 77.8516C91.7031 77.2656 91.2344 76.7969 90.6484 76.7969C90.0625 76.7969 89.5938 77.2656 89.5938 77.8516C89.5938 78.4375 90.0625 78.9062 90.6484 78.9062C91.2344 78.9062 91.7031 78.4375 91.7031 77.8516ZM94.6719 78.9062C94.5938 77.5 94.2812 76.25 93.2656 75.2344C92.25 74.2188 91 73.9062 89.5938 73.8281C88.1484 73.75 83.8125 73.75 82.3672 73.8281C80.9609 73.9062 79.75 74.2188 78.6953 75.2344C77.6797 76.25 77.3672 77.5 77.2891 78.9062C77.2109 80.3516 77.2109 84.6875 77.2891 86.1328C77.3672 87.5391 77.6797 88.75 78.6953 89.8047C79.75 90.8203 80.9609 91.1328 82.3672 91.2109C83.8125 91.2891 88.1484 91.2891 89.5938 91.2109C91 91.1328 92.25 90.8203 93.2656 89.8047C94.2812 88.75 94.5938 87.5391 94.6719 86.1328C94.75 84.6875 94.75 80.3516 94.6719 78.9062ZM92.7969 87.6562C92.5234 88.4375 91.8984 89.0234 91.1562 89.3359C89.9844 89.8047 87.25 89.6875 86 89.6875C84.7109 89.6875 81.9766 89.8047 80.8438 89.3359C80.0625 89.0234 79.4766 88.4375 79.1641 87.6562C78.6953 86.5234 78.8125 83.7891 78.8125 82.5C78.8125 81.25 78.6953 78.5156 79.1641 77.3438C79.4766 76.6016 80.0625 76.0156 80.8438 75.7031C81.9766 75.2344 84.7109 75.3516 86 75.3516C87.25 75.3516 89.9844 75.2344 91.1562 75.7031C91.8984 75.9766 92.4844 76.6016 92.7969 77.3438C93.2656 78.5156 93.1484 81.25 93.1484 82.5C93.1484 83.7891 93.2656 86.5234 92.7969 87.6562Z" fill="url(#paint1_linear_swwl)"/> <path d="M127.93 78.4375C128.711 77.8516 129.414 77.1484 129.961 76.3281C129.258 76.6406 128.438 76.875 127.617 76.9531C128.477 76.4453 129.102 75.6641 129.414 74.6875C128.633 75.1562 127.734 75.5078 126.836 75.7031C126.055 74.8828 125 74.4141 123.828 74.4141C121.562 74.4141 119.727 76.25 119.727 78.5156C119.727 78.8281 119.766 79.1406 119.844 79.4531C116.445 79.2578 113.398 77.6172 111.367 75.1562C111.016 75.7422 110.82 76.4453 110.82 77.2266C110.82 78.6328 111.523 79.8828 112.656 80.625C111.992 80.5859 111.328 80.4297 110.781 80.1172V80.1562C110.781 82.1484 112.188 83.7891 116.062 84.1797C113.75 84.2578 113.359 84.3359 113.008 84.3359C112.734 84.3359 112.5 84.2969 112.227 84.2578C112.734 85.8984 114.258 87.0703 116.055 87.1094C114.648 88.2031 112.891 88.8672 110.977 88.8672C110.625 88.8672 110.312 88.8281 110 88.7891C111.797 89.9609 113.945 90.625 116.289 90.625C123.828 90.625 127.93 84.4141 127.93 78.9844C127.93 78.7891 127.93 78.6328 127.93 78.4375Z" fill="url(#paint2_linear)"/> <defs> <linearGradient id="paint0_linear_sclwl" x1="137.667" y1="33.4445" x2="109.486" y2="62.2514" gradientUnits="userSpaceOnUse"> <stop stop-color="#E3280E"/> <stop offset="1" stop-color="#E30E0E"/> </linearGradient> <linearGradient id="paint1_linear_swwl" x1="93.8998" y1="73.3444" x2="78.4998" y2="89.4444" gradientUnits="userSpaceOnUse"> <stop stop-color="#5F0EE3"/> <stop offset="0.713476" stop-color="#FF0000"/> <stop offset="1" stop-color="#FF5C00"/> </linearGradient> <linearGradient id="paint2_linear" x1="136.667" y1="68.4445" x2="108.674" y2="93.3272" gradientUnits="userSpaceOnUse"> <stop stop-color="#0E96E3"/> <stop offset="1" stop-color="#0EBDE3"/> </linearGradient> </defs> </svg>',
			'addPage'             => '<svg viewBox="0 0 17 17"><path d="M12.1667 9.66667H13.8333V12.1667H16.3333V13.8333H13.8333V16.3333H12.1667V13.8333H9.66667V12.1667H12.1667V9.66667ZM2.16667 0.5H13.8333C14.7583 0.5 15.5 1.24167 15.5 2.16667V8.66667C14.9917 8.375 14.4333 8.16667 13.8333 8.06667V2.16667H2.16667V13.8333H8.06667C8.16667 14.4333 8.375 14.9917 8.66667 15.5H2.16667C1.24167 15.5 0.5 14.7583 0.5 13.8333V2.16667C0.5 1.24167 1.24167 0.5 2.16667 0.5ZM3.83333 3.83333H12.1667V5.5H3.83333V3.83333ZM3.83333 7.16667H12.1667V8.06667C11.4583 8.18333 10.8083 8.45 10.2333 8.83333H3.83333V7.16667ZM3.83333 10.5H8V12.1667H3.83333V10.5Z"/></svg>',
			'addWidget'           => '<svg viewBox="0 0 15 16"><path d="M0 15.5H6.66667V8.83333H0V15.5ZM1.66667 10.5H5V13.8333H1.66667V10.5ZM0 7.16667H6.66667V0.5H0V7.16667ZM1.66667 2.16667H5V5.5H1.66667V2.16667ZM8.33333 0.5V7.16667H15V0.5H8.33333ZM13.3333 5.5H10V2.16667H13.3333V5.5ZM12.5 11.3333H15V13H12.5V15.5H10.8333V13H8.33333V11.3333H10.8333V8.83333H12.5V11.3333Z"/></svg>',
			'plus'                => '<svg width="13" height="12" viewBox="0 0 13 12"><path d="M12.3327 6.83332H7.33268V11.8333H5.66602V6.83332H0.666016V5.16666H5.66602V0.166656H7.33268V5.16666H12.3327V6.83332Z"/></svg>',
			'eye1'                => '<svg width="20" height="17" viewBox="0 0 20 17"><path d="M9.85801 5.5L12.4997 8.13333V8C12.4997 7.33696 12.2363 6.70107 11.7674 6.23223C11.2986 5.76339 10.6627 5.5 9.99967 5.5H9.85801ZM6.27467 6.16667L7.56634 7.45833C7.52467 7.63333 7.49967 7.80833 7.49967 8C7.49967 8.66304 7.76307 9.29893 8.23191 9.76777C8.70075 10.2366 9.33663 10.5 9.99967 10.5C10.183 10.5 10.3663 10.475 10.5413 10.4333L11.833 11.725C11.2747 12 10.658 12.1667 9.99967 12.1667C8.8946 12.1667 7.8348 11.7277 7.0534 10.9463C6.27199 10.1649 5.83301 9.10507 5.83301 8C5.83301 7.34167 5.99967 6.725 6.27467 6.16667ZM1.66634 1.55833L3.56634 3.45833L3.94134 3.83333C2.56634 4.91667 1.48301 6.33333 0.833008 8C2.27467 11.6583 5.83301 14.25 9.99967 14.25C11.2913 14.25 12.5247 14 13.6497 13.55L14.008 13.9L16.4413 16.3333L17.4997 15.275L2.72467 0.5L1.66634 1.55833ZM9.99967 3.83333C11.1047 3.83333 12.1645 4.27232 12.946 5.05372C13.7274 5.83512 14.1663 6.89493 14.1663 8C14.1663 8.53333 14.058 9.05 13.8663 9.51667L16.308 11.9583C17.558 10.9167 18.558 9.55 19.1663 8C17.7247 4.34167 14.1663 1.75 9.99967 1.75C8.83301 1.75 7.71634 1.95833 6.66634 2.33333L8.47467 4.125C8.94967 3.94167 9.45801 3.83333 9.99967 3.83333Z"/></svg>',

			'eyePreview'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"/></svg>',

			'facebookShare'       => '<svg viewBox="0 0 448 512"><path fill="currentColor" d="M400 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h137.25V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.27c-30.81 0-40.42 19.12-40.42 38.73V256h68.78l-11 71.69h-57.78V480H400a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48z"></path></svg>',
			'twitterShare'        => '<svg viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-26.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>',
			'linkedinShare'       => '<svg viewBox="0 0 448 512"><path fill="currentColor" d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C26.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg>',
			'mailShare'           => '<svg viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>',

			'successNotification' => '<svg viewBox="0 0 20 20"><path d="M10 0C4.5 0 0 4.5 0 10C0 15.5 4.5 20 10 20C15.5 20 20 15.5 20 10C20 4.5 15.5 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z"/></svg>',
			'errorNotification'   => '<svg viewBox="0 0 20 20"><path d="M9.99997 0C4.47997 0 -3.05176e-05 4.48 -3.05176e-05 10C-3.05176e-05 15.52 4.47997 20 9.99997 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 9.99997 0ZM11 15H8.99997V13H11V15ZM11 11H8.99997V5H11V11Z"/></svg>',
			'messageNotification' => '<svg viewBox="0 0 20 20"><path d="M11.0001 7H9.00012V5H11.0001V7ZM11.0001 15H9.00012V9H11.0001V15ZM10.0001 0C8.6869 0 7.38654 0.258658 6.17329 0.761205C4.96003 1.26375 3.85764 2.00035 2.92905 2.92893C1.05369 4.8043 0.00012207 7.34784 0.00012207 10C0.00012207 12.6522 1.05369 15.1957 2.92905 17.0711C3.85764 17.9997 4.96003 18.7362 6.17329 19.2388C7.38654 19.7413 8.6869 20 10.0001 20C12.6523 20 15.1958 18.9464 17.0712 17.0711C18.9466 15.1957 20.0001 12.6522 20.0001 10C20.0001 8.68678 19.7415 7.38642 19.2389 6.17317C18.7364 4.95991 17.9998 3.85752 17.0712 2.92893C16.1426 2.00035 15.0402 1.26375 13.827 0.761205C12.6137 0.258658 11.3133 0 10.0001 0Z"/></svg>',

			'albumsPreview'       => '<svg width="63" height="65" viewBox="0 0 63 65" fill="none"><rect x="13.6484" y="10.2842" width="34.7288" height="34.7288" rx="1.44703" fill="#8C8F9A"/> <g filter="url(#filter0_dddalbumsPreview)"><rect x="22.1484" y="5.21962" width="34.7288" height="34.7288" rx="1.44703" transform="rotate(8 22.1484 5.21962)" fill="white"/> </g><path d="M29.0485 23.724L18.9288 28.1468L17.2674 39.9686L51.6582 44.802L52.2623 40.5031L29.0485 23.724Z" fill="#B5E5FF"/> <path d="M44.9106 25.2228L17.7194 36.7445L17.2663 39.9687L51.6571 44.802L53.4696 31.9054L44.9106 25.2228Z" fill="#43A6DB"/> <circle cx="42.9495" cy="18.3718" r="2.89406" transform="rotate(8 42.9495 18.3718)" fill="#43A6DB"/> <g filter="url(#filter1_dddalbumsPreview)"> <rect x="42.4766" y="33.9054" width="16.875" height="16.875" rx="8.4375" fill="white"/> <path d="M54.1953 42.8116H51.3828V45.6241H50.4453V42.8116H47.6328V41.8741H50.4453V39.0616H51.3828V41.8741H54.1953V42.8116Z" fill="#0068A0"/> </g> <defs> <filter id="filter0_dddalbumsPreview" x="0.86108" y="0.342124" width="58.3848" height="57.6613" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="-7.23516" dy="4.3411"/> <feGaussianBlur stdDeviation="4.70286"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="2.89406"/> <feGaussianBlur stdDeviation="1.44703"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="filter1_dddalbumsPreview" x="25.8357" y="28.8408" width="36.4099" height="35.6864" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="-7.23516" dy="4.3411"/> <feGaussianBlur stdDeviation="4.70286"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="2.89406"/> <feGaussianBlur stdDeviation="1.44703"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'featuredPostPreview' => '<svg width="47" height="48" viewBox="0 0 47 48" fill="none"> <g filter="url(#filter0_ddfeaturedpos)"> <rect x="2.09375" y="1.84264" width="34.7288" height="34.7288" rx="1.44703" fill="white"/> </g> <path d="M11.4995 19.2068L2.09375 24.9949L2.09375 36.9329H36.8225V32.5918L11.4995 19.2068Z" fill="#B5E5FF"/> <path d="M27.4168 18.4833L2.09375 33.6772V36.933H36.8225V23.9097L27.4168 18.4833Z" fill="#43A6DB"/> <circle cx="24.523" cy="11.9718" r="2.89406" fill="#43A6DB"/> <g filter="url(#filter1_ddfeaturedpos)"> <rect x="26.0312" y="25.2824" width="16.875" height="16.875" rx="8.4375" fill="white"/> <path d="M37.75 34.1886H34.9375V37.0011H34V34.1886H31.1875V33.2511H34V30.4386H34.9375V33.2511H37.75V34.1886Z" fill="#0068A0"/> </g> <defs> <filter id="filter0_ddfeaturedpos" x="0.09375" y="0.842636" width="40.7288" height="40.7288" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="1" dy="2"/> <feGaussianBlur stdDeviation="1.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <filter id="filter1_ddfeaturedpos" x="26.0312" y="24.2824" width="22.875" height="22.875" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="1" dy="2"/> <feGaussianBlur stdDeviation="1.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'issueSinglePreview'  => '<svg width="27" height="18" viewBox="0 0 27 18" fill="none"> <line x1="3.22082" y1="2.84915" x2="8.91471" y2="8.54304" stroke="#8C8F9A" stroke-width="3"/> <path d="M3.10938 8.65422L8.80327 2.96033" stroke="#8C8F9A" stroke-width="3"/> <line x1="18.3107" y1="2.84915" x2="26.0046" y2="8.54304" stroke="#8C8F9A" stroke-width="3"/> <path d="M18.1992 8.65422L23.8931 2.96033" stroke="#8C8F9A" stroke-width="3"/> <line x1="8.64062" y1="16.3863" x2="18.0351" y2="16.3863" stroke="#8C8F9A" stroke-width="3"/> </svg>',
			'playButton'          => '<svg viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg>',
			'spinner'             => '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h6.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
			'follow'              => '<svg viewBox="0 0 24 24"><path d="M20 18.5H4C3.46957 18.5 2.96086 18.2893 2.58579 17.9142C2.21071 17.5391 2 17.0304 2 16.5V7.5C2 6.96957 2.21071 6.46086 2.58579 6.08579C2.96086 5.71071 3.46957 5.5 4 5.5H20C20.5304 5.5 21.0391 5.71071 21.4142 6.08579C21.7893 6.46086 22 6.96957 22 7.5V16.5C22 17.0304 21.7893 17.5391 21.4142 17.9142C21.0391 18.2893 20.5304 18.5 20 18.5ZM4 7.5V16.5H20V7.5H4Z" fill="#141B38"/><path d="M9 13.75C9 13.1977 9.44772 12.75 10 12.75H14C14.5523 12.75 15 13.1977 15 13.75V15H9V13.75Z" fill="#141B38"/><path d="M13.5 10.5C13.5 11.3284 12.8284 12 12 12C11.1716 12 10.5 11.3284 10.5 10.5C10.5 9.67157 11.1716 9 12 9C12.8284 9 13.5 9.67157 13.5 10.5Z" fill="#141B38"/></svg>',
			'picture'             => '<svg viewBox="0 0 24 24" fill="none"><path d="M8.5 13.5L11 16.5L14.5 12L19 18H5L8.5 13.5ZM21 19V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3H5C4.46957 3 3.96086 3.21071 3.58579 3.58579C3.21071 3.96086 3 4.46957 3 5V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19Z"/></svg>',
			'caption'             => '<svg viewBox="0 0 24 24" fill="none"><path d="M5 3C3.89 3 3 3.89 3 5V19C3 20.11 3.89 21 5 21H19C20.11 21 21 20.11 21 19V5C21 3.89 20.11 3 19 3H5ZM5 5H19V19H5V5ZM7 7V9H17V7H7ZM7 11V13H17V11H7ZM7 15V17H14V15H7Z"/></svg>',
			'heart'               => '<svg viewBox="0 0 24 24"><path d="M16.5 3C14.76 3 13.09 3.81 12 5.09C10.91 3.81 9.24 3 7.5 3C4.42 3 2 5.42 2 8.5C2 12.28 5.4 15.36 10.55 20.04L12 21.35L13.45 20.03C18.6 15.36 22 12.28 22 8.5C22 5.42 19.58 3 16.5 3ZM12.1 18.55L12 18.65L11.9 18.55C7.14 14.24 4 11.39 4 8.5C4 6.5 5.5 5 7.5 5C9.04 5 10.54 5.99 11.07 7.36H12.94C13.46 5.99 14.96 5 16.5 5C18.5 5 20 6.5 20 8.5C20 11.39 16.86 14.24 12.1 18.55Z"/></svg>',
			'sort'                => '<svg viewBox="0 0 24 24"><path d="M7.73062 10.9999C7.51906 10.9999 7.40314 10.7535 7.53803 10.5906L11.8066 5.43267C11.9066 5.31186 12.0918 5.31186 12.1918 5.43267L16.4604 10.5906C16.5953 10.7535 16.4794 10.9999 16.2678 10.9999H7.73062Z" fill="#141B38"/><path d="M7.80277 13C7.58005 13 7.4685 13.2693 7.626 13.4268L11.8224 17.6232C11.9201 17.7209 12.0784 17.7209 12.176 17.6232L16.3724 13.4268C16.5299 13.2693 16.4184 13 16.1957 13H7.80277Z" fill="#141B38"/></svg>',
			'shop'                => '<svg viewBox="0 0 24 24"><path d="M11 9H13V6H16V4H13V1H11V4H8V6H11V9ZM7 18C5.9 18 5.01 18.9 5.01 20C5.01 21.1 5.9 22 7 22C8.1 22 9 21.1 9 20C9 18.9 8.1 18 7 18ZM17 18C15.9 18 15.01 18.9 15.01 20C15.01 21.1 15.9 22 17 22C18.1 22 19 21.1 19 20C19 18.9 18.1 18 17 18ZM8.1 13H15.55C16.3 13 16.96 12.59 17.3 11.97L21.16 4.96L19.42 4L15.55 11H8.53L4.27 2H1V4H3L6.6 11.59L5.25 14.03C4.52 15.37 5.48 17 7 17H19V15H7L8.1 13Z" fill="#141B38"/></svg>',
			'headerUser'          => '<svg class="svg-inline--fa fa-user fa-w-16" style="margin-right: 3px;" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="user" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M96 160C96 71.634 167.635 0 256 0s160 71.634 160 160-71.635 160-160 160S96 248.366 96 160zm304 192h-28.556c-71.006 42.713-159.912 42.695-230.888 0H112C50.144 352 0 402.144 0 464v24c0 13.255 10.745 24 24 24h464c13.255 0 24-10.745 24-24v-24c0-61.856-50.144-112-112-112z"></path></svg>',
			'headerPhoto'         => '<svg class="svg-inline--fa fa-image fa-w-16" aria-hidden="true" data-fa-processed="" data-prefix="far" data-icon="image" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M464 448H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h416c26.51 0 48 21.49 48 48v288c0 26.51-21.49 48-48 48zM112 120c-30.928 0-56 25.072-56 56s25.072 56 56 56 56-25.072 56-56-25.072-56-56-56zM64 384h384V272l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L208 320l-55.515-55.515c-4.686-4.686-12.284-4.686-16.971 0L64 336v48z"></path></svg>',
			'imageChooser'        => '<svg viewBox="0 0 18 18" fill="none"><path d="M2.16667 0.5C1.72464 0.5 1.30072 0.675595 0.988155 0.988155C0.675595 1.30072 0.5 1.72464 0.5 2.16667V13.8333C0.5 14.2754 0.675595 14.6993 0.988155 15.0118C1.30072 15.3244 1.72464 15.5 2.16667 15.5H9.74167C9.69167 15.225 9.66667 14.95 9.66667 14.6667C9.66667 14.1 9.76667 13.5333 9.95833 13H2.16667L5.08333 9.25L7.16667 11.75L10.0833 8L11.9417 10.475C12.75 9.95 13.7 9.66667 14.6667 9.66667C14.95 9.66667 15.225 9.69167 15.5 9.74167V2.16667C15.5 1.72464 15.3244 1.30072 15.0118 0.988155C14.6993 0.675595 14.2754 0.5 13.8333 0.5H2.16667ZM13.8333 11.3333V13.8333H11.3333V15.5H13.8333V18H15.5V15.5H18V13.8333H15.5V11.3333H13.8333Z"/></svg>',

			'usertimelineIcon'    => '<svg width="260" height="126" viewBox="0 0 260 126" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#usrtimlineclip0)"> <g filter="url(#usrtimlinefilter0_ddd)"> <g clip-path="url(#usrtimlineclip1)"> <rect x="64" y="23" width="131" height="113" rx="2" fill="white"/> <rect x="112.027" y="38" width="46" height="6" rx="1" fill="#DCDDE1"/> <rect x="112.027" y="49" width="28" height="6" rx="1" fill="#DCDDE1"/> <g clip-path="url(#usrtimlineclip2)"> <rect x="133.027" y="121" width="48" height="48" rx="1" fill="#F9BBA0"/> </g> <g clip-path="url(#usrtimlineclip3)"> <rect x="133.027" y="67" width="48" height="48" fill="#43A6DB"/> <circle cx="123.527" cy="101.5" r="40.5" fill="#86D0F9"/> </g> <g clip-path="url(#usrtimlineclip4)"> <rect x="79.0273" y="121" width="48" height="48" fill="#B6DDAD"/> </g> <g clip-path="url(#usrtimlineclip5)"> <rect x="79.0273" y="67" width="48" height="48" fill="#F6966B"/> <path d="M88.4756 84L126.476 122H57.0273L88.4756 84Z" fill="#F9BBA0"/> </g> <circle cx="92.0273" cy="45" r="10" fill="#DCDDE1"/> <circle cx="92.0273" cy="45" r="12" stroke="url(#usrtimlinepaint0_linear)"/> </g> </g> </g> <defs> <filter id="usrtimlinefilter0_ddd" x="51" y="16" width="157" height="139" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <linearGradient id="usrtimlinepaint0_linear" x1="88.5773" y1="78.9" x2="139.127" y2="27.3" gradientUnits="userSpaceOnUse"> <stop stop-color="white"/> <stop offset="0.147864" stop-color="#F6640E"/> <stop offset="0.443974" stop-color="#BA03A7"/> <stop offset="0.733337" stop-color="#6A01B9"/> <stop offset="1" stop-color="#6B01B9"/> </linearGradient> <clipPath id="usrtimlineclip0"> <rect width="259.056" height="126" fill="white"/> </clipPath> <clipPath id="usrtimlineclip1"> <rect x="64" y="23" width="131" height="113" rx="2" fill="white"/> </clipPath> <clipPath id="usrtimlineclip2"> <rect x="133.027" y="121" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="usrtimlineclip3"> <rect x="133.027" y="67" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="usrtimlineclip4"> <rect x="79.0273" y="121" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="usrtimlineclip5"> <rect x="79.0273" y="67" width="48" height="48" rx="1" fill="white"/> </clipPath> </defs> </svg>',
			'publichashtagIcon'   => '<svg width="260" height="126" viewBox="0 0 260 126" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#hashtagiconclip0)"> <g filter="url(#hashtagiconfilter0_ddd)"> <rect x="119.327" y="12.3203" width="80" height="91" rx="2" transform="rotate(4 119.327 12.3203)" fill="white"/> </g> <g clip-path="url(#hashtagiconclip1)"> <rect x="119.327" y="12.3203" width="80" height="80" transform="rotate(4 119.327 12.3203)" fill="#0096CC"/> </g> <path d="M130.918 88.5016L128.971 88.3655L129.441 86.6958C129.453 86.6464 129.454 86.5951 129.444 86.5452C129.435 86.4954 129.414 86.4482 129.385 86.4069C129.355 86.3657 129.317 86.3313 129.273 86.3062C129.229 86.2811 129.18 86.2659 129.129 86.2616L128.427 86.2125C128.347 86.2049 128.265 86.2255 128.198 86.2709C128.131 86.3163 128.081 86.3837 128.058 86.4616L127.572 88.2676L125.678 88.1352L126.147 86.4654C126.159 86.4172 126.16 86.3671 126.151 86.3182C126.142 86.2694 126.123 86.223 126.095 86.182C126.067 86.1411 126.031 86.1066 125.988 86.0808C125.946 86.055 125.899 86.0384 125.849 86.0322L125.148 85.9832C125.067 85.9755 124.986 85.9962 124.918 86.0416C124.851 86.087 124.801 86.1544 124.778 86.2322L124.299 88.0388L122.194 87.8916C122.112 87.8842 122.03 87.9058 121.963 87.9526C121.895 87.9994 121.846 88.0684 121.824 88.1477L121.631 88.8392C121.617 88.89 121.614 88.9433 121.624 88.9953C121.633 89.0472 121.654 89.0964 121.685 89.1391C121.716 89.1819 121.756 89.2172 121.802 89.2424C121.848 89.2676 121.9 89.282 121.952 89.2846L123.899 89.4208L123.128 92.1867L121.023 92.0396C120.941 92.0322 120.859 92.0537 120.791 92.1005C120.724 92.1473 120.675 92.2164 120.653 92.2957L120.46 92.9871C120.446 93.038 120.443 93.0913 120.452 93.1432C120.462 93.1952 120.483 93.2443 120.513 93.2871C120.544 93.3299 120.584 93.3652 120.631 93.3904C120.677 93.4156 120.728 93.43 120.781 93.4326L122.742 93.5697L122.273 95.2394C122.26 95.2896 122.259 95.3419 122.269 95.3926C122.28 95.4432 122.301 95.491 122.332 95.5325C122.362 95.5741 122.402 95.6083 122.447 95.6328C122.493 95.6573 122.543 95.6715 122.595 95.6744L123.296 95.7234C123.375 95.7269 123.452 95.7041 123.516 95.6588C123.579 95.6135 123.626 95.5481 123.649 95.4731L124.142 93.6676L126.036 93.8L125.566 95.4698C125.555 95.5179 125.553 95.5681 125.562 95.617C125.571 95.6658 125.59 95.7122 125.618 95.7531C125.646 95.7941 125.683 95.8286 125.725 95.8544C125.767 95.8802 125.815 95.8968 125.864 95.903L126.566 95.952C126.647 95.9597 126.728 95.939 126.795 95.8936C126.862 95.8482 126.912 95.7808 126.935 95.703L127.432 93.8977L129.536 94.0448C129.618 94.0522 129.7 94.0306 129.768 93.9839C129.836 93.9371 129.885 93.868 129.907 93.7887L130.096 93.097C130.11 93.0462 130.113 92.9928 130.104 92.9409C130.094 92.889 130.073 92.8398 130.043 92.797C130.012 92.7542 129.972 92.719 129.925 92.6938C129.879 92.6686 129.828 92.6542 129.775 92.6515L127.818 92.5147L128.586 89.7485L130.69 89.8956C130.772 89.903 130.854 89.8814 130.922 89.8347C130.989 89.7879 131.039 89.7188 131.061 89.6395L131.253 88.948C131.268 88.8961 131.27 88.8414 131.26 88.7883C131.25 88.7353 131.228 88.6852 131.196 88.642C131.164 88.5989 131.122 88.5637 131.073 88.5394C131.025 88.515 130.972 88.5021 130.918 88.5016ZM126.414 92.4166L124.52 92.2841L125.292 89.5181L127.186 89.6506L126.414 92.4166Z" fill="#0068A0"/> <rect x="138.037" y="88.8115" width="29" height="7" rx="1" transform="rotate(4 138.037 88.8115)" fill="#86D0F9"/> <g filter="url(#hashtagiconfilter1_ddd)"> <rect x="119.327" y="12.3203" width="80" height="91" rx="2" transform="rotate(4 119.327 12.3203)" fill="white"/> </g> <g clip-path="url(#hashtagiconclip2)"> <rect x="119.327" y="12.3203" width="80" height="80" transform="rotate(4 119.327 12.3203)" fill="#0096CC"/> <circle cx="126.556" cy="44.5" r="46.5" fill="#0068A0"/> </g> <path d="M130.918 88.5016L128.971 88.3655L129.441 86.6958C129.453 86.6464 129.454 86.5951 129.444 86.5452C129.435 86.4954 129.414 86.4482 129.385 86.4069C129.355 86.3657 129.317 86.3313 129.273 86.3062C129.229 86.2811 129.18 86.2659 129.129 86.2616L128.427 86.2125C128.347 86.2049 128.265 86.2255 128.198 86.2709C128.131 86.3163 128.081 86.3837 128.058 86.4616L127.572 88.2676L125.678 88.1352L126.147 86.4654C126.159 86.4172 126.16 86.3671 126.151 86.3182C126.142 86.2694 126.123 86.223 126.095 86.182C126.067 86.1411 126.031 86.1066 125.988 86.0808C125.946 86.055 125.899 86.0384 125.849 86.0322L125.148 85.9832C125.067 85.9755 124.986 85.9962 124.918 86.0416C124.851 86.087 124.801 86.1544 124.778 86.2322L124.299 88.0388L122.194 87.8916C122.112 87.8842 122.03 87.9058 121.963 87.9526C121.895 87.9994 121.846 88.0684 121.824 88.1477L121.631 88.8392C121.617 88.89 121.614 88.9433 121.624 88.9953C121.633 89.0472 121.654 89.0964 121.685 89.1391C121.716 89.1819 121.756 89.2172 121.802 89.2424C121.848 89.2676 121.9 89.282 121.952 89.2846L123.899 89.4208L123.128 92.1867L121.023 92.0396C120.941 92.0322 120.859 92.0537 120.791 92.1005C120.724 92.1473 120.675 92.2164 120.653 92.2957L120.46 92.9871C120.446 93.038 120.443 93.0913 120.452 93.1432C120.462 93.1952 120.483 93.2443 120.513 93.2871C120.544 93.3299 120.584 93.3652 120.631 93.3904C120.677 93.4156 120.728 93.43 120.781 93.4326L122.742 93.5697L122.273 95.2394C122.26 95.2896 122.259 95.3419 122.269 95.3926C122.28 95.4432 122.301 95.491 122.332 95.5325C122.362 95.5741 122.402 95.6083 122.447 95.6328C122.493 95.6573 122.543 95.6715 122.595 95.6744L123.296 95.7234C123.375 95.7269 123.452 95.7041 123.516 95.6588C123.579 95.6135 123.626 95.5481 123.649 95.4731L124.142 93.6676L126.036 93.8L125.566 95.4698C125.555 95.5179 125.553 95.5681 125.562 95.617C125.571 95.6658 125.59 95.7122 125.618 95.7531C125.646 95.7941 125.683 95.8286 125.725 95.8544C125.767 95.8802 125.815 95.8968 125.864 95.903L126.566 95.952C126.647 95.9597 126.728 95.939 126.795 95.8936C126.862 95.8482 126.912 95.7808 126.935 95.703L127.432 93.8977L129.536 94.0448C129.618 94.0522 129.7 94.0306 129.768 93.9839C129.836 93.9371 129.885 93.868 129.907 93.7887L130.096 93.097C130.11 93.0462 130.113 92.9928 130.104 92.9409C130.094 92.889 130.073 92.8398 130.043 92.797C130.012 92.7542 129.972 92.719 129.925 92.6938C129.879 92.6686 129.828 92.6542 129.775 92.6515L127.818 92.5147L128.586 89.7485L130.69 89.8956C130.772 89.903 130.854 89.8814 130.922 89.8347C130.989 89.7879 131.039 89.7188 131.061 89.6395L131.253 88.948C131.268 88.8961 131.27 88.8414 131.26 88.7883C131.25 88.7353 131.228 88.6852 131.196 88.642C131.164 88.5989 131.122 88.5637 131.073 88.5394C131.025 88.515 130.972 88.5021 130.918 88.5016ZM126.414 92.4166L124.52 92.2841L125.292 89.5181L127.186 89.6506L126.414 92.4166Z" fill="#0068A0"/> <rect x="138.037" y="88.8115" width="29" height="7" rx="1" transform="rotate(4 138.037 88.8115)" fill="#86D0F9"/> <g filter="url(#hashtagiconfilter2_ddd)"> <rect x="65.0557" y="21" width="80" height="91" rx="2" fill="white"/> </g> <g clip-path="url(#hashtagiconclip3)"> <rect x="65.0557" y="21" width="80" height="80" fill="#F6966B"/> <path d="M80.8025 49.333L144.136 112.666H28.3887L80.8025 49.333Z" fill="#F9BBA0"/> </g> <path d="M81.9327 96.187H79.9812L80.3328 94.4887C80.3414 94.4386 80.3391 94.3873 80.3261 94.3382C80.313 94.2892 80.2894 94.2435 80.257 94.2044C80.2246 94.1653 80.1841 94.1337 80.1383 94.1118C80.0925 94.0898 80.0425 94.078 79.9917 94.0773H79.2885C79.2072 94.0753 79.1277 94.1015 79.0636 94.1515C78.9995 94.2015 78.9547 94.2722 78.9368 94.3515L78.5782 96.187H76.6794L77.031 94.4887C77.0395 94.4398 77.0376 94.3896 77.0253 94.3415C77.013 94.2934 76.9907 94.2484 76.9598 94.2095C76.9289 94.1707 76.8902 94.1388 76.8461 94.116C76.802 94.0932 76.7535 94.08 76.704 94.0773H76.0007C75.9194 94.0753 75.84 94.1015 75.7759 94.1515C75.7117 94.2015 75.6669 94.2722 75.6491 94.3515L75.2974 96.187H73.1877C73.1054 96.1854 73.0252 96.2126 72.9609 96.264C72.8967 96.3154 72.8525 96.3877 72.836 96.4683L72.6919 97.1716C72.6813 97.2233 72.6825 97.2767 72.6954 97.3278C72.7083 97.379 72.7325 97.4266 72.7662 97.4671C72.8 97.5076 72.8425 97.54 72.8905 97.5619C72.9385 97.5838 72.9908 97.5946 73.0435 97.5936H74.995L74.4184 100.407H72.3086C72.2263 100.405 72.1461 100.432 72.0818 100.484C72.0176 100.535 71.9734 100.607 71.957 100.688L71.8128 101.391C71.8022 101.443 71.8034 101.496 71.8163 101.547C71.8292 101.599 71.8534 101.646 71.8872 101.687C71.9209 101.727 71.9634 101.76 72.0114 101.782C72.0594 101.803 72.1117 101.814 72.1644 101.813H74.13L73.7784 103.512C73.7696 103.562 73.7722 103.615 73.7858 103.664C73.7995 103.714 73.824 103.761 73.8576 103.8C73.8912 103.839 73.933 103.87 73.9801 103.892C74.0272 103.913 74.0784 103.924 74.13 103.923H74.8333C74.9116 103.921 74.9869 103.893 75.0474 103.843C75.1079 103.793 75.1501 103.725 75.1673 103.649L75.533 101.813H77.4318L77.0802 103.512C77.0717 103.56 77.0736 103.611 77.0859 103.659C77.0982 103.707 77.1205 103.752 77.1514 103.791C77.1823 103.829 77.221 103.861 77.2651 103.884C77.3092 103.907 77.3577 103.92 77.4072 103.923H78.1105C78.1918 103.925 78.2712 103.899 78.3354 103.849C78.3995 103.799 78.4443 103.728 78.4621 103.649L78.8313 101.813H80.9411C81.0234 101.815 81.1036 101.788 81.1679 101.736C81.2321 101.685 81.2763 101.612 81.2928 101.532L81.4334 100.829C81.444 100.777 81.4428 100.723 81.4299 100.672C81.417 100.621 81.3928 100.574 81.359 100.533C81.3253 100.493 81.2828 100.46 81.2348 100.438C81.1868 100.416 81.1345 100.406 81.0818 100.407H79.1197L79.6928 97.5936H81.8026C81.8849 97.5952 81.9651 97.568 82.0294 97.5166C82.0936 97.4652 82.1378 97.3929 82.1543 97.3123L82.2984 96.609C82.3093 96.5561 82.3079 96.5014 82.2942 96.4492C82.2806 96.3969 82.2551 96.3485 82.2197 96.3077C82.1844 96.2669 82.1401 96.2348 82.0903 96.2139C82.0405 96.193 81.9866 96.1838 81.9327 96.187ZM77.7132 100.407H75.8143L76.391 97.5936H78.2898L77.7132 100.407Z" fill="#FE544F"/> <rect x="89.0557" y="96" width="29" height="7" rx="1" fill="#FCE1D5"/> <g filter="url(#hashtagiconfilter3_ddd)"> <rect x="65.0557" y="21" width="80" height="91" rx="2" fill="white"/> </g> <g clip-path="url(#hashtagiconclip4)"> <rect x="65.0557" y="21" width="80" height="80" fill="#F6966B"/> <path d="M80.8025 49.333L144.136 112.666H28.3887L80.8025 49.333Z" fill="#F9BBA0"/> </g> <path d="M81.9327 96.187H79.9812L80.3328 94.4887C80.3414 94.4386 80.3391 94.3873 80.3261 94.3382C80.313 94.2892 80.2894 94.2435 80.257 94.2044C80.2246 94.1653 80.1841 94.1337 80.1383 94.1118C80.0925 94.0898 80.0425 94.078 79.9917 94.0773H79.2885C79.2072 94.0753 79.1277 94.1015 79.0636 94.1515C78.9995 94.2015 78.9547 94.2722 78.9368 94.3515L78.5782 96.187H76.6794L77.031 94.4887C77.0395 94.4398 77.0376 94.3896 77.0253 94.3415C77.013 94.2934 76.9907 94.2484 76.9598 94.2095C76.9289 94.1707 76.8902 94.1388 76.8461 94.116C76.802 94.0932 76.7535 94.08 76.704 94.0773H76.0007C75.9194 94.0753 75.84 94.1015 75.7759 94.1515C75.7117 94.2015 75.6669 94.2722 75.6491 94.3515L75.2974 96.187H73.1877C73.1054 96.1854 73.0252 96.2126 72.9609 96.264C72.8967 96.3154 72.8525 96.3877 72.836 96.4683L72.6919 97.1716C72.6813 97.2233 72.6825 97.2767 72.6954 97.3278C72.7083 97.379 72.7325 97.4266 72.7662 97.4671C72.8 97.5076 72.8425 97.54 72.8905 97.5619C72.9385 97.5838 72.9908 97.5946 73.0435 97.5936H74.995L74.4184 100.407H72.3086C72.2263 100.405 72.1461 100.432 72.0818 100.484C72.0176 100.535 71.9734 100.607 71.957 100.688L71.8128 101.391C71.8022 101.443 71.8034 101.496 71.8163 101.547C71.8292 101.599 71.8534 101.646 71.8872 101.687C71.9209 101.727 71.9634 101.76 72.0114 101.782C72.0594 101.803 72.1117 101.814 72.1644 101.813H74.13L73.7784 103.512C73.7696 103.562 73.7722 103.615 73.7858 103.664C73.7995 103.714 73.824 103.761 73.8576 103.8C73.8912 103.839 73.933 103.87 73.9801 103.892C74.0272 103.913 74.0784 103.924 74.13 103.923H74.8333C74.9116 103.921 74.9869 103.893 75.0474 103.843C75.1079 103.793 75.1501 103.725 75.1673 103.649L75.533 101.813H77.4318L77.0802 103.512C77.0717 103.56 77.0736 103.611 77.0859 103.659C77.0982 103.707 77.1205 103.752 77.1514 103.791C77.1823 103.829 77.221 103.861 77.2651 103.884C77.3092 103.907 77.3577 103.92 77.4072 103.923H78.1105C78.1918 103.925 78.2712 103.899 78.3354 103.849C78.3995 103.799 78.4443 103.728 78.4621 103.649L78.8313 101.813H80.9411C81.0234 101.815 81.1036 101.788 81.1679 101.736C81.2321 101.685 81.2763 101.612 81.2928 101.532L81.4334 100.829C81.444 100.777 81.4428 100.723 81.4299 100.672C81.417 100.621 81.3928 100.574 81.359 100.533C81.3253 100.493 81.2828 100.46 81.2348 100.438C81.1868 100.416 81.1345 100.406 81.0818 100.407H79.1197L79.6928 97.5936H81.8026C81.8849 97.5952 81.9651 97.568 82.0294 97.5166C82.0936 97.4652 82.1378 97.3929 82.1543 97.3123L82.2984 96.609C82.3093 96.5561 82.3079 96.5014 82.2942 96.4492C82.2806 96.3969 82.2551 96.3485 82.2197 96.3077C82.1844 96.2669 82.1401 96.2348 82.0903 96.2139C82.0405 96.193 81.9866 96.1838 81.9327 96.187ZM77.7132 100.407H75.8143L76.391 97.5936H78.2898L77.7132 100.407Z" fill="#FE544F"/> <rect x="89.0557" y="96" width="29" height="7" rx="1" fill="#FCE1D5"/> </g> <defs> <filter id="hashtagiconfilter0_ddd" x="100.114" y="5.45508" width="111.884" height="122.09" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="hashtagiconfilter1_ddd" x="100.114" y="5.45508" width="111.884" height="122.09" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="hashtagiconfilter2_ddd" x="52.0557" y="14" width="106" height="117" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="hashtagiconfilter3_ddd" x="52.0557" y="14" width="106" height="117" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <clipPath id="hashtagiconclip0"> <rect width="259.056" height="126" fill="white" transform="translate(0.0556641)"/> </clipPath> <clipPath id="hashtagiconclip1"> <path d="M119.211 13.9829C119.275 13.0647 120.072 12.3724 120.99 12.4366L197.47 17.7846C198.388 17.8488 199.08 18.6452 199.016 19.5634L194.528 83.7401L114.723 78.1595L119.211 13.9829Z" fill="white"/> </clipPath> <clipPath id="hashtagiconclip2"> <path d="M119.211 13.9829C119.275 13.0647 120.072 12.3724 120.99 12.4366L197.47 17.7846C198.388 17.8488 199.08 18.6452 199.016 19.5634L194.528 83.7401L114.723 78.1595L119.211 13.9829Z" fill="white"/> </clipPath> <clipPath id="hashtagiconclip3"> <path d="M65.0557 22.6667C65.0557 21.7462 65.8019 21 66.7223 21H143.389C144.309 21 145.056 21.7462 145.056 22.6667V87H65.0557V22.6667Z" fill="white"/> </clipPath> <clipPath id="hashtagiconclip4"> <path d="M65.0557 22.6667C65.0557 21.7462 65.8019 21 66.7223 21H143.389C144.309 21 145.056 21.7462 145.056 22.6667V87H65.0557V22.6667Z" fill="white"/> </clipPath> </defs> </svg>',
			'taggedpostsIcon'     => '<svg width="260" height="126" viewBox="0 0 260 126" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#taggedpostclip0)"> <g filter="url(#taggedpostfilter0_ddd)"> <g clip-path="url(#taggedpostclip1)"> <rect x="104.316" y="29.0303" width="83.0697" height="84.1212" rx="2.10303" transform="rotate(2 104.316 29.0303)" fill="white"/> <g clip-path="url(#taggedpostclip2)"> <path d="M104.063 23.0957L188.133 26.0315L185.418 103.796L101.348 100.86L104.063 23.0957Z" fill="#59AB46"/> <path d="M119.756 48.194L183.987 117.073L62.3516 112.826L119.756 48.194Z" fill="#76C064"/> </g> <path fill-rule="evenodd" clip-rule="evenodd" d="M113.109 94.8001C114.187 94.6246 115.292 94.7726 116.286 95.2254C117.279 95.6782 118.116 96.4154 118.691 97.3439C119.265 98.2723 119.552 99.3503 119.513 100.441L119.485 101.259C119.467 101.783 119.241 102.278 118.858 102.635C118.474 102.993 117.964 103.183 117.441 103.165C116.917 103.147 116.422 102.921 116.064 102.538C115.997 102.466 115.937 102.391 115.882 102.311C115.342 102.804 114.63 103.067 113.899 103.041C113.158 103.016 112.458 102.697 111.953 102.155C111.447 101.613 111.178 100.892 111.204 100.151C111.23 99.4107 111.549 98.7106 112.091 98.2052C112.633 97.6998 113.353 97.4304 114.094 97.4562C114.834 97.4821 115.535 97.8011 116.04 98.3431C116.545 98.8851 116.815 99.6057 116.789 100.346L116.76 101.164C116.753 101.362 116.826 101.556 116.961 101.701C117.097 101.847 117.285 101.932 117.483 101.939C117.682 101.946 117.875 101.874 118.021 101.738C118.166 101.603 118.252 101.415 118.259 101.216L118.287 100.399C118.317 99.55 118.094 98.7115 117.647 97.9894C117.201 97.2673 116.55 96.6939 115.777 96.3417C115.004 95.9896 114.144 95.8745 113.306 96.011C112.468 96.1475 111.689 96.5295 111.068 97.1086C110.447 97.6878 110.012 98.4381 109.817 99.2647C109.622 100.091 109.677 100.957 109.975 101.752C110.272 102.548 110.799 103.237 111.488 103.733C112.177 104.23 112.998 104.51 113.846 104.54L113.847 104.54C114.6 104.567 115.347 104.395 116.011 104.04C116.31 103.881 116.682 103.994 116.841 104.293C117.001 104.591 116.888 104.963 116.589 105.123C115.733 105.579 114.772 105.801 113.803 105.766L113.825 105.153L113.804 105.766C113.803 105.766 113.803 105.766 113.803 105.766C112.712 105.728 111.657 105.367 110.771 104.729C109.885 104.091 109.208 103.205 108.825 102.182C108.443 101.159 108.373 100.046 108.623 98.9835C108.873 97.9208 109.433 96.956 110.231 96.2114C111.03 95.4668 112.031 94.9757 113.109 94.8001ZM115.563 100.304C115.577 99.888 115.426 99.4838 115.143 99.1798C114.859 98.8757 114.466 98.6967 114.051 98.6822C113.636 98.6677 113.231 98.8189 112.927 99.1024C112.623 99.3859 112.444 99.7786 112.43 100.194C112.415 100.61 112.566 101.014 112.85 101.318C113.133 101.622 113.526 101.801 113.942 101.815C114.357 101.83 114.761 101.679 115.065 101.395C115.369 101.112 115.548 100.719 115.563 100.304Z" fill="#59AB46"/> <rect x="126.717" y="97.5381" width="30.4939" height="7.3606" rx="1.05151" transform="rotate(2 126.717 97.5381)" fill="#B6DDAD"/> </g> </g> <g filter="url(#taggedpostfilter1_ddd)"> <g clip-path="url(#taggedpostclip3)"> <rect x="70.8867" y="10.8984" width="83.0697" height="84.1212" rx="2.10303" transform="rotate(-2 70.8867 10.8984)" fill="white"/> <g clip-path="url(#taggedpostclip4)"> <path d="M70.2217 4.99609L154.292 2.06031L157.007 79.825L72.9373 82.7608L70.2217 4.99609Z" fill="#43A6DB"/> <circle cx="169.299" cy="72.169" r="48.8954" transform="rotate(-2 169.299 72.169)" fill="#0068A0"/> </g> <path fill-rule="evenodd" clip-rule="evenodd" d="M84.2452 75.8962C85.308 75.646 86.4211 75.7165 87.4438 76.0989C88.4665 76.4813 89.3529 77.1583 89.9908 78.0444C90.6287 78.9305 90.9895 79.9859 91.0276 81.0771L91.0562 81.8944C91.0745 82.4183 90.8839 82.928 90.5264 83.3114C90.1689 83.6947 89.6738 83.9204 89.1499 83.9387C88.626 83.957 88.1163 83.7664 87.733 83.4089C87.6615 83.3423 87.5956 83.2709 87.5354 83.1954C87.0315 83.7253 86.3396 84.0368 85.6081 84.0623C84.8674 84.0882 84.1468 83.8188 83.6048 83.3134C83.0628 82.8079 82.7438 82.1079 82.7179 81.3673C82.6921 80.6266 82.9615 79.906 83.4669 79.364C83.9723 78.822 84.6724 78.503 85.413 78.4771C86.1537 78.4513 86.8742 78.7207 87.4162 79.2261C87.9583 79.7315 88.2773 80.4316 88.3031 81.1722L88.3317 81.9896C88.3386 82.1883 88.4242 82.3761 88.5696 82.5117C88.715 82.6473 88.9084 82.7196 89.1071 82.7126C89.3058 82.7057 89.4936 82.6201 89.6292 82.4747C89.7648 82.3293 89.8371 82.136 89.8301 81.9372L89.8016 81.1199C89.772 80.2712 89.4913 79.4504 88.9952 78.7612C88.499 78.072 87.8096 77.5454 87.0142 77.248C86.2188 76.9506 85.353 76.8957 84.5264 77.0904C83.6998 77.285 82.9495 77.7204 82.3703 78.3415C81.7912 78.9625 81.4092 79.7414 81.2727 80.5796C81.1362 81.4177 81.2513 82.2776 81.6034 83.0503C81.9556 83.8231 82.529 84.474 83.2511 84.9209C83.9733 85.3678 84.8117 85.5905 85.6604 85.5608L85.661 85.5608C86.4142 85.5352 87.147 85.3114 87.7851 84.9114C88.0721 84.7314 88.4506 84.8182 88.6306 85.1052C88.8105 85.3922 88.7237 85.7708 88.4367 85.9507C87.6149 86.466 86.6715 86.754 85.7026 86.7869L85.6818 86.1738L85.7032 86.7868C85.703 86.7868 85.7028 86.7869 85.7026 86.7869C84.6116 86.8248 83.5339 86.5385 82.6056 85.9641C81.6771 85.3895 80.9399 84.5526 80.4871 83.559C80.0344 82.5655 79.8864 81.46 80.0619 80.3824C80.2374 79.3047 80.7285 78.3033 81.4731 77.5048C82.2178 76.7063 83.1825 76.1465 84.2452 75.8962ZM87.0771 81.215C87.0626 80.7996 86.8836 80.4069 86.5796 80.1233C86.2755 79.8398 85.8713 79.6887 85.4558 79.7032C85.0403 79.7177 84.6476 79.8966 84.3641 80.2007C84.0806 80.5047 83.9294 80.909 83.944 81.3245C83.9585 81.7399 84.1374 82.1326 84.4415 82.4162C84.7455 82.6997 85.1498 82.8508 85.5652 82.8363C85.9807 82.8218 86.3734 82.6429 86.657 82.3388C86.9405 82.0348 87.0916 81.6305 87.0771 81.215Z" fill="#0068A0"/> <rect x="98.0117" y="77.6768" width="30.4939" height="7.3606" rx="1.05151" transform="rotate(-2 98.0117 77.6768)" fill="#86D0F9"/> </g> </g> </g> <defs> <filter id="taggedpostfilter0_ddd" x="87.7112" y="21.6697" width="113.294" height="114.308" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.30909"/> <feGaussianBlur stdDeviation="6.83485"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.05151"/> <feGaussianBlur stdDeviation="1.05151"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.15454"/> <feGaussianBlur stdDeviation="3.15454"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="taggedpostfilter1_ddd" x="57.217" y="0.638418" width="113.294" height="114.308" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.30909"/> <feGaussianBlur stdDeviation="6.83485"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.05151"/> <feGaussianBlur stdDeviation="1.05151"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.15454"/> <feGaussianBlur stdDeviation="3.15454"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <clipPath id="taggedpostclip0"> <rect width="259.056" height="126" fill="white" transform="translate(0.111328)"/> </clipPath> <clipPath id="taggedpostclip1"> <rect x="104.316" y="29.0303" width="83.0697" height="84.1212" rx="2.10303" transform="rotate(2 104.316 29.0303)" fill="white"/> </clipPath> <clipPath id="taggedpostclip2"> <path d="M104.187 19.5933C104.221 18.626 105.032 17.8692 106 17.903L186.567 20.7164C187.534 20.7502 188.291 21.5617 188.257 22.529L185.896 90.1353L101.826 87.1995L104.187 19.5933Z" fill="white"/> </clipPath> <clipPath id="taggedpostclip3"> <rect x="70.8867" y="10.8984" width="83.0697" height="84.1212" rx="2.10303" transform="rotate(-2 70.8867 10.8984)" fill="white"/> </clipPath> <clipPath id="taggedpostclip4"> <path d="M70.0983 1.49365C70.0645 0.526345 70.8213 -0.285196 71.7886 -0.318975L152.356 -3.13244C153.323 -3.16622 154.134 -2.40945 154.168 -1.44214L156.529 66.1641L72.4591 69.0999L70.0983 1.49365Z" fill="white"/> </clipPath> </defs> </svg>',
			'socialwall1Icon'     => '<svg width="260" height="126" viewBox="0 0 260 126" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#social1filter0_ddd)"> <rect x="44.416" y="44.9111" width="42" height="42" rx="2.10303" transform="rotate(-5 44.416 44.9111)" fill="white"/> <path d="M66.2979 54.0796C60.8188 54.559 56.7273 59.4241 57.2092 64.933C57.645 69.914 61.6528 73.7292 66.48 74.0598L65.8699 67.0864L63.3395 67.3078L63.0868 64.4188L65.6171 64.1974L65.4245 61.9959C65.2057 59.4954 66.5698 57.9908 68.8511 57.7912C69.9369 57.6962 71.0892 57.7861 71.0892 57.7861L71.3044 60.2467L70.0492 60.3565C68.8139 60.4646 68.4925 61.2657 68.5614 62.0527L68.7252 63.9255L71.4947 63.6832L71.2991 66.6114L68.978 66.8145L69.5881 73.7878C71.9031 73.2117 73.9359 71.827 75.3195 69.8835C76.7031 67.9401 77.3464 65.566 77.1331 63.1899C76.6512 57.681 71.777 53.6003 66.2979 54.0796Z" fill="#006BFA"/> </g> <g filter="url(#social1filter1_ddd)"> <rect x="83.0967" y="39.1279" width="42" height="42" rx="2.10303" transform="rotate(-3 83.0967 39.1279)" fill="white"/> <path d="M104.886 53.6171C101.89 53.7741 99.6299 56.3334 99.7844 59.2824C99.9414 62.2783 102.454 64.5406 105.45 64.3836C108.399 64.229 110.708 61.7141 110.551 58.7182C110.396 55.7691 107.835 53.4625 104.886 53.6171ZM105.352 62.5111C103.432 62.6117 101.76 61.1504 101.657 59.1843C101.556 57.2651 103.02 55.6394 104.986 55.5363C106.905 55.4357 108.531 56.8995 108.632 58.8188C108.735 60.7848 107.271 62.4105 105.352 62.5111ZM111.71 53.0717C111.673 52.3695 111.082 51.8372 110.38 51.874C109.678 51.9108 109.146 52.502 109.182 53.2041C109.219 53.9063 109.81 54.4386 110.512 54.4018C111.215 54.365 111.747 53.7738 111.71 53.0717ZM115.334 54.1491C115.152 52.4688 114.699 50.9905 113.418 49.8372C112.137 48.6839 110.62 48.3879 108.93 48.3826C107.193 48.3798 101.997 48.6521 100.27 48.8365C98.5894 49.0184 97.1579 49.469 95.9578 50.7523C94.8045 52.0331 94.5085 53.5507 94.5032 55.2408C94.5003 56.9777 94.7726 62.1737 94.957 63.9008C95.139 65.5811 95.5895 67.0126 96.8728 68.2127C98.2005 69.3635 99.6712 69.662 101.361 69.6673C103.098 69.6701 108.294 69.3978 110.021 69.2134C111.702 69.0315 113.18 68.5785 114.333 67.2976C115.484 65.97 115.783 64.4992 115.788 62.8091C115.791 61.0722 115.518 55.8762 115.334 54.1491ZM113.637 64.7525C113.358 65.7059 112.646 66.4473 111.776 66.8684C110.401 67.5037 107.117 67.535 105.619 67.6135C104.074 67.6945 100.805 68.0066 99.418 67.516C98.4621 67.1906 97.7232 66.5252 97.2996 65.6087C96.6667 64.2806 96.6354 60.9965 96.5545 59.4517C96.476 57.9538 96.1638 54.6844 96.652 53.2506C96.9798 52.3416 97.6452 51.6026 98.5618 51.1791C99.8899 50.5462 103.174 50.5149 104.719 50.4339C106.217 50.3554 109.486 50.0433 110.92 50.5314C111.826 50.8125 112.568 51.5247 112.989 52.3944C113.624 53.7693 113.656 57.0534 113.734 58.5514C113.815 60.0961 114.127 63.3655 113.637 64.7525Z" fill="url(#social1paint0_linear)"/> </g> <g filter="url(#social1filter2_ddd)"> <rect x="122.913" y="35.2803" width="42" height="42" rx="2.10303" transform="rotate(2 122.913 35.2803)" fill="white"/> <path d="M153.831 51.3695C153.049 51.6924 152.211 51.8933 151.348 51.9732C152.246 51.4743 152.955 50.6585 153.31 49.6603C152.463 50.131 151.531 50.4487 150.555 50.6147C149.795 49.7277 148.704 49.1892 147.444 49.1453C145.096 49.0633 143.11 50.9151 143.027 53.2836C143.015 53.6234 143.044 53.9546 143.103 54.2669C139.551 53.9627 136.443 52.1432 134.425 49.4811C134.033 50.0978 133.797 50.83 133.77 51.6095C133.718 53.0986 134.421 54.444 135.555 55.234C134.845 55.2093 134.192 54.9863 133.623 54.6663L133.622 54.6963C133.55 56.775 134.968 58.5656 136.913 59.0238C136.278 59.1739 135.617 59.1748 134.982 59.0264C135.224 59.8878 135.729 60.6518 136.428 61.2111C137.126 61.7703 137.982 62.0966 138.875 62.1441C137.318 63.2909 135.417 63.8738 133.485 63.797C133.145 63.7851 132.806 63.7533 132.467 63.7014C134.323 64.987 136.557 65.7755 138.976 65.8599C146.851 66.1349 151.407 59.75 151.605 54.0835C151.611 53.8936 151.617 53.7137 151.614 53.5235C152.475 52.9531 153.221 52.2187 153.831 51.3695Z" fill="#1B90EF"/> </g> <g filter="url(#social1filter3_ddd)"> <rect x="161.295" y="39.9297" width="42" height="42" rx="2.10303" transform="rotate(3 161.295 39.9297)" fill="white"/> <path d="M179.013 64.8913L184.352 62.167L179.327 58.8995L179.013 64.8913ZM190.966 57.677C191.072 58.1532 191.129 58.7871 191.147 59.5891C191.175 60.3917 191.169 61.0823 191.137 61.6815L191.153 62.5235C191.038 64.7105 190.794 66.3099 190.461 67.3238C190.164 68.2095 189.555 68.7583 188.643 68.9609C188.167 69.0661 187.303 69.111 185.982 69.1018C184.68 69.1037 183.49 69.0714 182.391 69.0138L180.8 68.9905C176.616 68.7712 174.018 68.4748 173.004 68.1413C172.119 67.8446 171.57 67.235 171.367 66.3231C171.262 65.847 171.205 65.2131 171.187 64.4111C171.159 63.6085 171.165 62.9179 171.196 62.3187L171.181 61.4767C171.295 59.2897 171.539 57.6903 171.873 56.6764C172.169 55.7907 172.779 55.2418 173.691 55.0393C174.167 54.9341 175.031 54.8892 176.352 54.8984C177.654 54.8965 178.844 54.9288 179.942 54.9864L181.533 55.0097C185.717 55.229 188.315 55.5254 189.329 55.8589C190.215 56.1556 190.764 56.7652 190.966 57.677Z" fill="#EB2121"/> </g> <defs> <filter id="social1filter0_ddd" x="30.7463" y="33.8904" width="72.8401" height="72.8401" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.30909"/> <feGaussianBlur stdDeviation="6.83485"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.05151"/> <feGaussianBlur stdDeviation="1.05151"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.15454"/> <feGaussianBlur stdDeviation="3.15454"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="social1filter1_ddd" x="69.427" y="29.5691" width="71.4799" height="71.4799" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.30909"/> <feGaussianBlur stdDeviation="6.83485"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.05151"/> <feGaussianBlur stdDeviation="1.05151"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.15454"/> <feGaussianBlur stdDeviation="3.15454"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="social1filter2_ddd" x="107.778" y="27.9197" width="70.7796" height="70.7796" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.30909"/> <feGaussianBlur stdDeviation="6.83485"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.05151"/> <feGaussianBlur stdDeviation="1.05151"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.15454"/> <feGaussianBlur stdDeviation="3.15454"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="social1filter3_ddd" x="145.427" y="32.5691" width="71.4799" height="71.4799" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.30909"/> <feGaussianBlur stdDeviation="6.83485"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.05151"/> <feGaussianBlur stdDeviation="1.05151"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.15454"/> <feGaussianBlur stdDeviation="3.15454"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <linearGradient id="social1paint0_linear" x1="103.683" y1="88.8048" x2="145.491" y2="41.4018" gradientUnits="userSpaceOnUse"> <stop stop-color="white"/> <stop offset="0.147864" stop-color="#F6640E"/> <stop offset="0.443974" stop-color="#BA03A7"/> <stop offset="0.733337" stop-color="#6A01B9"/> <stop offset="1" stop-color="#6B01B9"/> </linearGradient> </defs> </svg>',

			'user'                => '<svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 0C4.53043 0 5.03914 0.210714 5.41421 0.585786C5.78929 0.960859 6 1.46957 6 2C6 2.53043 5.78929 3.03914 5.41421 3.41421C5.03914 3.78929 4.53043 4 4 4C3.46957 4 2.96086 3.78929 2.58579 3.41421C2.21071 3.03914 2 2.53043 2 2C2 1.46957 2.21071 0.960859 2.58579 0.585786C2.96086 0.210714 3.46957 0 4 0ZM4 5C6.21 5 8 5.895 8 7V8H0V7C0 5.895 1.79 5 4 5Z"/></svg>',
			'hashtag'             => '<svg viewBox="0 0 18 18" fill="none"><path d="M17.3607 4.1775H14.0152L14.618 1.266C14.6328 1.18021 14.6288 1.09223 14.6064 1.00812C14.5839 0.924001 14.5436 0.845742 14.488 0.778722C14.4324 0.711703 14.363 0.657514 14.2845 0.619882C14.206 0.582251 14.1203 0.56207 14.0332 0.560727H12.8276C12.6883 0.557321 12.5521 0.602311 12.4422 0.688037C12.3323 0.773763 12.2555 0.894929 12.2249 1.03091L11.61 4.1775H8.3549L8.9577 1.266C8.97229 1.18215 8.96897 1.09617 8.94795 1.0137C8.92692 0.931226 8.88867 0.854142 8.83572 0.787518C8.78276 0.720894 8.71629 0.666239 8.64069 0.62715C8.56509 0.588061 8.48207 0.565423 8.3971 0.560727H7.1915C7.05216 0.557321 6.91594 0.602311 6.80604 0.688037C6.69613 0.773763 6.61933 0.894929 6.58871 1.03091L5.98591 4.1775H2.36914C2.22811 4.17466 2.09056 4.22136 1.98042 4.30947C1.87028 4.39759 1.79452 4.52153 1.76634 4.65974L1.51919 5.86533C1.50109 5.95393 1.50315 6.04546 1.52522 6.13316C1.5473 6.22085 1.58882 6.30245 1.64671 6.37192C1.7046 6.44139 1.77737 6.49694 1.85965 6.53446C1.94192 6.57199 2.03158 6.59052 2.12199 6.58869H5.46751L4.47892 11.4111H0.862146C0.721125 11.4082 0.583571 11.4549 0.473429 11.543C0.363287 11.6311 0.287532 11.7551 0.259351 11.8933L0.0122042 13.0989C-0.00589975 13.1875 -0.00383898 13.279 0.0182337 13.3667C0.0403064 13.4544 0.0818254 13.536 0.139715 13.6055C0.197605 13.6749 0.270382 13.7305 0.352656 13.768C0.43493 13.8055 0.524592 13.8241 0.615 13.8222H3.98463L3.38183 16.7338C3.36677 16.821 3.37112 16.9106 3.39459 16.996C3.41806 17.0814 3.46006 17.1606 3.51761 17.2279C3.57517 17.2953 3.64685 17.3491 3.72757 17.3856C3.80829 17.4221 3.89606 17.4403 3.98463 17.439H5.19022C5.3244 17.4356 5.45359 17.3875 5.55732 17.3023C5.66105 17.2171 5.73339 17.0998 5.76288 16.9688L6.38979 13.8222H9.64488L9.04209 16.7338C9.02749 16.8176 9.03081 16.9036 9.05184 16.9861C9.07286 17.0685 9.11111 17.1456 9.16407 17.2122C9.21702 17.2789 9.28349 17.3335 9.35909 17.3726C9.43469 17.4117 9.51771 17.4343 9.60269 17.439H10.8083C10.9476 17.4424 11.0838 17.3974 11.1937 17.3117C11.3037 17.226 11.3805 17.1048 11.4111 16.9688L12.044 13.8222H15.6608C15.8018 13.8251 15.9394 13.7784 16.0495 13.6903C16.1596 13.6022 16.2354 13.4782 16.2636 13.34L16.5047 12.1344C16.5228 12.0458 16.5207 11.9543 16.4987 11.8666C16.4766 11.7789 16.4351 11.6973 16.3772 11.6278C16.3193 11.5584 16.2465 11.5028 16.1642 11.4653C16.082 11.4278 15.9923 11.4092 15.9019 11.4111H12.5383L13.5209 6.58869H17.1376C17.2787 6.59153 17.4162 6.54483 17.5264 6.45672C17.6365 6.36861 17.7123 6.24466 17.7404 6.10645L17.9876 4.90086C18.0063 4.8102 18.0038 4.71645 17.9804 4.62689C17.957 4.53733 17.9133 4.45436 17.8527 4.3844C17.7921 4.31445 17.7162 4.2594 17.6308 4.22352C17.5455 4.18764 17.4531 4.1719 17.3607 4.1775ZM10.1271 11.4111H6.87202L7.86061 6.58869H11.1157L10.1271 11.4111Z"/></svg>',
			'mention'             => '<svg viewBox="0 0 18 18"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.24419 0.172937C8.99002 -0.174331 10.7996 0.00389957 12.4442 0.685088C14.0887 1.36628 15.4943 2.51983 16.4832 3.99987C17.4722 5.47992 18 7.21997 18 9.00001V10.3333C18 11.1879 17.6605 12.0075 17.0562 12.6118C16.452 13.2161 15.6324 13.5556 14.7778 13.5556C13.9232 13.5556 13.1036 13.2161 12.4993 12.6118C12.3867 12.4992 12.2833 12.3791 12.1896 12.2527C11.3384 13.0874 10.1933 13.5556 9.00001 13.5556C7.7918 13.5556 6.63307 13.0756 5.77874 12.2213C4.92441 11.3669 4.44445 10.2082 4.44445 9.00001C4.44445 7.7918 4.92441 6.63307 5.77874 5.77874C6.63307 4.92441 7.7918 4.44445 9.00001 4.44445C10.2082 4.44445 11.3669 4.92441 12.2213 5.77874C13.0756 6.63307 13.5556 7.7918 13.5556 9.00001V10.3333C13.5556 10.6575 13.6843 10.9684 13.9135 11.1976C14.1428 11.4268 14.4536 11.5556 14.7778 11.5556C15.1019 11.5556 15.4128 11.4268 15.642 11.1976C15.8712 10.9684 16 10.6575 16 10.3333V9.00001C16 7.61554 15.5895 6.26216 14.8203 5.11101C14.0511 3.95987 12.9579 3.06266 11.6788 2.53285C10.3997 2.00303 8.99224 1.86441 7.63437 2.13451C6.27651 2.4046 5.02922 3.07129 4.05026 4.05026C3.07129 5.02922 2.4046 6.27651 2.13451 7.63437C1.86441 8.99224 2.00303 10.3997 2.53285 11.6788C3.06266 12.9579 3.95987 14.0511 5.11101 14.8203C6.26216 15.5895 7.61554 16 9.00001 16L9.001 16C10.2297 16.0012 11.4363 15.6782 12.4987 15.0627C12.9766 14.7859 13.5884 14.9488 13.8653 15.4267C14.1421 15.9046 13.9792 16.5164 13.5013 16.7933C12.1329 17.586 10.5796 18.0016 8.99901 18L9.00001 17V18C8.99968 18 8.99934 18 8.99901 18C7.21933 17.9998 5.47964 17.472 3.99987 16.4832C2.51983 15.4943 1.36628 14.0887 0.685088 12.4442C0.00389957 10.7996 -0.17433 8.99002 0.172936 7.24419C0.520204 5.49836 1.37737 3.89472 2.63604 2.63604C3.89472 1.37737 5.49836 0.520204 7.24419 0.172937ZM11.5556 9.00001C11.5556 8.32223 11.2863 7.67221 10.8071 7.19295C10.3278 6.7137 9.67778 6.44445 9.00001 6.44445C8.32223 6.44445 7.67221 6.7137 7.19295 7.19295C6.7137 7.67221 6.44445 8.32223 6.44445 9.00001C6.44445 9.67778 6.7137 10.3278 7.19295 10.8071C7.67221 11.2863 8.32223 11.5556 9.00001 11.5556C9.67778 11.5556 10.3278 11.2863 10.8071 10.8071C11.2863 10.3278 11.5556 9.67778 11.5556 9.00001Z"/></svg>',
			'tooltipHelpSvg'      => '<svg width="20" height="21" viewBox="0 0 20 21" fill="#0068A0" xmlns="http://www.w3.org/2000/svg"><path d="M9.1665 8H10.8332V6.33333H9.1665V8ZM9.99984 17.1667C6.32484 17.1667 3.33317 14.175 3.33317 10.5C3.33317 6.825 6.32484 3.83333 9.99984 3.83333C13.6748 3.83333 16.6665 6.825 16.6665 10.5C16.6665 14.175 13.6748 17.1667 9.99984 17.1667ZM9.99984 2.16666C8.90549 2.16666 7.82186 2.38221 6.81081 2.801C5.79976 3.21979 4.8811 3.83362 4.10728 4.60744C2.54448 6.17024 1.6665 8.28986 1.6665 10.5C1.6665 12.7101 2.54448 14.8298 4.10728 16.3926C4.8811 17.1664 5.79976 17.7802 6.81081 18.199C7.82186 18.6178 8.90549 18.8333 9.99984 18.8333C12.21 18.8333 14.3296 17.9554 15.8924 16.3926C17.4552 14.8298 18.3332 12.7101 18.3332 10.5C18.3332 9.40565 18.1176 8.32202 17.6988 7.31097C17.28 6.29992 16.6662 5.38126 15.8924 4.60744C15.1186 3.83362 14.1999 3.21979 13.1889 2.801C12.1778 2.38221 11.0942 2.16666 9.99984 2.16666ZM9.1665 14.6667H10.8332V9.66666H9.1665V14.6667Z" fill="#0068A0"/></svg>',

			'shoppableDisabled'   => '<svg width="303" height="145" viewBox="0 0 303 145" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M124.919 67.2058C130.919 72.7058 150.519 81.4058 180.919 72.2058" stroke="#8C8F9A" stroke-width="2" stroke-dasharray="3 3"/> <path d="M181.999 69L185.797 70.4241L183.5 74L181.999 69Z" fill="#8C8F9A"/> <g filter="url(#shopp_disabled_filter0_dddd)"> <rect x="24.6006" y="17.6504" width="81" height="98" rx="2" transform="rotate(-4 24.6006 17.6504)" fill="white"/> <rect x="24.3338" y="17.4184" width="81.5" height="98.5" rx="2.25" transform="rotate(-4 24.3338 17.4184)" stroke="url(#shopp_disabled_paint0_linear)" stroke-width="0.5"/> </g> <g clip-path="url(#shopp_disabled_clip0)"> <path d="M94.5298 21.3615C92.9088 21.4749 91.7091 22.8823 91.8207 24.478C91.9341 26.0991 93.3162 27.3005 94.9372 27.1872C96.5329 27.0756 97.7597 25.6917 97.6463 24.0707C97.5348 22.4749 96.1256 21.2499 94.5298 21.3615ZM94.8664 26.174C93.8279 26.2466 92.9083 25.471 92.8339 24.4072C92.7613 23.3687 93.5387 22.4744 94.6025 22.4C95.6409 22.3274 96.5352 23.1048 96.6079 24.1433C96.6822 25.2071 95.9048 26.1014 94.8664 26.174ZM98.2208 21.0016C98.1942 20.6217 97.869 20.339 97.4891 20.3656C97.1091 20.3921 96.8264 20.7173 96.853 21.0973C96.8796 21.4772 97.2048 21.7599 97.5847 21.7333C97.9646 21.7068 98.2473 21.3816 98.2208 21.0016ZM100.194 21.5509C100.079 20.6426 99.8198 19.8463 99.1152 19.2338C98.4106 18.6213 97.586 18.4753 96.6706 18.4884C95.7299 18.5033 92.9184 18.6999 91.9848 18.8161C91.0765 18.9305 90.3054 19.188 89.6676 19.8944C89.0551 20.599 88.9092 21.4237 88.9223 22.3391C88.9371 23.2798 89.1337 26.0913 89.2499 27.0249C89.3644 27.9332 89.6219 28.7042 90.3283 29.342C91.0582 29.9528 91.8575 30.1005 92.7729 30.0874C93.7136 30.0725 96.5251 29.8759 97.4587 29.7597C98.367 29.6453 99.1634 29.386 99.7759 28.6814C100.387 27.9515 100.534 27.1521 100.521 26.2367C100.506 25.296 100.31 22.4845 100.194 21.5509ZM99.3745 27.3096C99.2327 27.8285 98.854 28.2368 98.3869 28.4731C97.6483 28.8302 95.8699 28.8782 95.0594 28.9348C94.2236 28.9933 92.4559 29.1933 91.7001 28.9407C91.1793 28.7735 90.7728 28.4201 90.5348 27.9277C90.1795 27.2144 90.1315 25.4361 90.073 24.6002C90.0164 23.7897 89.8164 22.022 90.0672 21.2409C90.2362 20.7455 90.5895 20.339 91.082 20.1009C91.7952 19.7456 93.5736 19.6976 94.4094 19.6392C95.2199 19.5825 96.9876 19.3825 97.7687 19.6333C98.2624 19.777 98.6707 20.1557 98.9069 20.6228C99.264 21.3614 99.312 23.1397 99.3687 23.9502C99.4271 24.7861 99.6271 26.5538 99.3745 27.3096Z" fill="url(#shopp_disabled_paint1_linear)"/> </g> <g clip-path="url(#shopp_disabled_clip1)"> <rect x="26.1348" y="39.5967" width="81" height="76" rx="2" transform="rotate(-4 26.1348 39.5967)" fill="#B5E5FF"/> <circle cx="30.7388" cy="105.436" r="54" transform="rotate(-4 30.7388 105.436)" fill="#86D0F9"/> <g filter="url(#shopp_disabled_filter1_dd)"> <mask id="shopp_disabled_mask0" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="35" y="47" width="60" height="54"> <path fill-rule="evenodd" clip-rule="evenodd" d="M68.7966 50.3478C68.534 50.4332 68.3943 50.7154 68.4401 50.9877C68.8644 53.5073 66.4327 56.3732 62.7333 57.5753C59.0338 58.7773 55.382 57.888 54.2442 55.6002C54.1213 55.3529 53.8423 55.2068 53.5797 55.2921L47.2555 57.347C47.1786 57.372 47.109 57.4152 47.0525 57.473L42.6186 62.008L35.8445 69.2862C35.7004 69.441 35.6693 69.6698 35.7668 69.8574L40.9681 79.8652C41.1015 80.1217 41.4239 80.212 41.6711 80.0621L47.8083 76.3393C48.0715 76.1797 48.4151 76.2935 48.5309 76.5788L58.2754 100.594C58.374 100.837 58.6437 100.963 58.8932 100.881L92.2457 90.0446C92.4952 89.9635 92.6396 89.7034 92.5765 89.4488L86.3412 64.2801C86.2678 63.9837 86.4749 63.6913 86.7789 63.6622L94.424 62.9299C94.7094 62.9026 94.9134 62.6414 94.8708 62.358L93.1967 51.2062C93.1647 50.9929 92.9995 50.8242 92.787 50.7877L82.5629 49.0293L76.3102 47.9666C76.2305 47.953 76.1488 47.959 76.0719 47.984L68.7966 50.3478Z" fill="white"/> </mask> <g mask="url(#shopp_disabled_mask0)"> <rect x="28.3076" y="60.3479" width="72" height="54" transform="rotate(-16 28.3076 60.3479)" fill="white"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M66.4321 69.6639C65.1395 69.4776 63.7264 69.0512 62.5105 69.0127C63.1766 69.8427 63.7987 70.7521 64.429 71.6465C63.8884 72.3619 63.1987 72.9948 62.5553 73.6533C63.3952 74.1125 64.4294 74.2212 65.3292 74.5723C64.947 75.4717 64.0024 76.5635 63.9089 77.3062C65.0894 76.8017 66.445 76.1437 67.5698 75.7666C68.181 76.9532 68.7057 78.2958 69.3922 79.3464C69.485 77.6689 69.5124 75.9552 69.7351 74.3498C70.8246 74.4733 72.1524 74.6242 73.1713 74.589C72.2358 73.8444 71.3419 73.0247 70.4606 72.1824C71.1537 71.2976 71.8595 70.42 72.5116 69.5125C71.2887 69.7444 70.035 70.0316 68.7692 70.3408C68.2001 69.1068 67.8102 67.5497 67.1648 66.4536C66.98 67.5567 66.688 68.6002 66.4321 69.6639ZM70.0641 80.1946C70.0998 80.9132 70.6974 81.0407 70.7363 81.4713C70.1738 81.4417 69.7628 81.4615 69.1035 81.7558C68.9743 81.2243 69.4256 81.0144 69.1426 80.3976C61.5808 81.6649 57.7717 68.4365 64.8194 65.5342C73.6314 61.9053 78.4249 77.5439 70.0641 80.1946Z" fill="#FE544F"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M67.1649 66.4536C67.8103 67.5497 68.2003 69.1068 68.7693 70.3407C70.0352 70.0316 71.2888 69.7444 72.5117 69.5125C71.8597 70.42 71.1538 71.2976 70.4608 72.1824C71.3421 73.0248 72.2359 73.8444 73.1714 74.589C72.1526 74.6242 70.8247 74.4733 69.7352 74.3498C69.5126 75.9552 69.4852 77.6689 69.3924 79.3464C68.7058 78.2958 68.1811 76.9532 67.5699 75.7666C66.4451 76.1438 65.0896 76.8017 63.9091 77.3062C64.0026 76.5635 64.9472 75.4718 65.3294 74.5723C64.4295 74.2212 63.3954 74.1125 62.5555 73.6533C63.1989 72.9948 63.8885 72.362 64.4292 71.6465C63.7988 70.7521 63.1767 69.8427 62.5106 69.0128C63.7266 69.0512 65.1396 69.4776 66.4323 69.6639C66.6881 68.6002 66.9802 67.5567 67.1649 66.4536Z" fill="white"/> </g> </g> </g> <g filter="url(#shopp_disabled_filter2_dddd)"> <rect x="199.592" y="17.7058" width="79" height="102" rx="2" transform="rotate(4 199.592 17.7058)" fill="#E2F5FF"/> </g> <rect x="231.919" y="100.162" width="36" height="17" rx="2" transform="rotate(4 231.919 100.162)" fill="#0096CC"/> <path d="M241.707 111.873L244.07 112.038C245.123 112.112 245.827 111.602 245.887 110.743L245.888 110.736C245.931 110.112 245.469 109.576 244.827 109.497L244.831 109.432C245.358 109.397 245.785 108.978 245.821 108.453L245.822 108.446C245.875 107.686 245.328 107.182 244.346 107.113L242.051 106.953L241.707 111.873ZM243.95 107.973C244.376 108.003 244.61 108.232 244.586 108.579L244.585 108.586C244.561 108.931 244.281 109.123 243.824 109.091L243.162 109.045L243.241 107.923L243.95 107.973ZM243.859 109.858C244.377 109.894 244.652 110.136 244.624 110.538L244.623 110.545C244.594 110.958 244.295 111.166 243.777 111.13L243.02 111.077L243.109 109.805L243.859 109.858ZM248.86 112.507C250.155 112.597 251.031 111.925 251.108 110.824L251.334 107.602L250.086 107.515L249.869 110.617C249.829 111.19 249.498 111.51 248.935 111.47C248.376 111.431 248.09 111.069 248.13 110.496L248.347 107.393L247.099 107.306L246.874 110.528C246.796 111.633 247.581 112.417 248.86 112.507ZM253.583 112.703L254.834 112.791L254.952 111.1L256.873 107.989L255.539 107.896L254.448 109.838L254.383 109.833L253.565 107.758L252.232 107.665L253.701 111.012L253.583 112.703Z" fill="white"/> <g filter="url(#shopp_disabled_filter3_dd)"> <mask id="shopp_disabled_mask1" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="207" y="35" width="61" height="48"> <path fill-rule="evenodd" clip-rule="evenodd" d="M244.802 36.7068C244.526 36.6972 244.298 36.9146 244.248 37.1861C243.785 39.699 240.52 41.5604 236.632 41.4246C232.745 41.2889 229.618 39.2042 229.331 36.6652C229.3 36.3908 229.088 36.1581 228.812 36.1485L222.166 35.9164C222.085 35.9136 222.005 35.9304 221.932 35.9653L216.215 38.7104L207.36 43.2328C207.171 43.329 207.064 43.5333 207.091 43.743L208.556 54.9261C208.594 55.2128 208.866 55.408 209.149 55.3516L216.19 53.9524C216.492 53.8924 216.776 54.117 216.787 54.4246L217.73 80.3242C217.74 80.5864 217.95 80.7966 218.212 80.8057L253.26 82.0296C253.522 82.0388 253.747 81.8438 253.774 81.5829L256.523 55.7995C256.556 55.4959 256.85 55.2919 257.146 55.3685L264.581 57.2952C264.858 57.3671 265.139 57.1915 265.196 56.9106L267.437 45.8588C267.48 45.6474 267.382 45.4324 267.195 45.3253L258.189 40.1762L252.677 37.039C252.607 36.999 252.528 36.9766 252.447 36.9738L244.802 36.7068Z" fill="white"/> </mask> <g mask="url(#shopp_disabled_mask1)"> <rect x="203.335" y="32.2556" width="72" height="54" transform="rotate(4 203.335 32.2556)" fill="white"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M235.974 54.0491C234.823 53.4321 233.641 52.548 232.512 52.096C232.854 53.1038 233.128 54.171 233.414 55.2271C232.661 55.7145 231.797 56.0733 230.967 56.472C231.599 57.1908 232.534 57.6466 233.259 58.2843C232.592 58.9988 231.331 59.7017 230.99 60.3676C232.271 60.2973 233.77 60.1426 234.956 60.173C235.125 61.497 235.159 62.9381 235.444 64.1601C236.105 62.6156 236.717 61.0146 237.476 59.5821C238.457 60.0709 239.653 60.6668 240.623 60.9822C239.998 59.9626 239.439 58.8866 238.899 57.7936C239.852 57.1992 240.816 56.616 241.739 55.9862C240.511 55.7859 239.234 55.627 237.939 55.4846C237.826 54.1304 237.992 52.5338 237.761 51.2831C237.21 52.2564 236.579 53.1372 235.974 54.0491ZM235.786 65.187C235.573 65.8745 236.091 66.1987 235.981 66.6166C235.462 66.3964 235.069 66.2745 234.349 66.3255C234.409 65.7818 234.905 65.739 234.85 65.0626C227.311 63.6672 228.256 49.9337 235.871 49.6169C245.393 49.2208 244.549 65.5558 235.786 65.187Z" fill="#FE544F"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M237.761 51.283C237.993 52.5337 237.827 54.1303 237.939 55.4844C239.235 55.6268 240.511 55.7857 241.739 55.9861C240.816 56.6159 239.853 57.1991 238.899 57.7935C239.439 58.8865 239.998 59.9624 240.623 60.9821C239.653 60.6667 238.457 60.0708 237.476 59.582C236.717 61.0145 236.106 62.6155 235.445 64.16C235.159 62.938 235.125 61.4969 234.956 60.1729C233.77 60.1425 232.272 60.2972 230.99 60.3675C231.332 59.7016 232.593 58.9987 233.259 58.2842C232.534 57.6465 231.599 57.1907 230.967 56.4719C231.797 56.0732 232.662 55.7144 233.414 55.227C233.128 54.1709 232.854 53.1037 232.512 52.0959C233.642 52.5479 234.824 53.432 235.975 54.049C236.579 53.1371 237.21 52.2563 237.761 51.283Z" fill="white"/> </g> </g> <path d="M266.144 121.304L266.2 120.51L265.32 120.449L265.375 119.655L263.615 119.532L263.67 118.739L261.03 118.554L261.085 117.761L259.325 117.637L259.547 114.463L258.666 114.402L258.722 113.608L256.962 113.485L256.906 114.279L256.026 114.217L255.526 121.359L254.646 121.297L254.702 120.504L252.061 120.319L251.839 123.493L252.719 123.555L252.608 125.142L253.489 125.203L253.378 126.79L254.258 126.852L254.147 128.439L255.027 128.501L254.861 130.881L264.543 131.558L264.765 128.384L265.645 128.446L265.811 126.065L264.931 126.003L264.765 128.384L263.885 128.322L263.718 130.703L255.796 130.149L255.907 128.562L255.027 128.501L255.138 126.913L254.258 126.852L254.369 125.265L253.489 125.203L253.6 123.616L252.719 123.555L252.886 121.174L254.646 121.297L254.591 122.091L255.471 122.152L255.305 124.533L256.185 124.594L256.906 114.279L258.666 114.402L258.167 121.544L259.047 121.605L259.269 118.431L261.03 118.554L260.808 121.728L261.688 121.79L261.854 119.409L263.615 119.532L263.393 122.706L264.273 122.768L264.439 120.387L265.32 120.449L265.264 121.242L266.144 121.304L265.811 126.065L266.692 126.127L267.025 121.365L266.144 121.304Z" fill="#141B38"/> <path d="M264.932 126.003L265.812 126.065L266.145 121.304L265.265 121.242L265.32 120.449L264.44 120.387L264.274 122.768L263.393 122.706L263.615 119.532L261.855 119.409L261.688 121.79L260.808 121.728L261.03 118.554L259.27 118.431L259.048 121.605L258.168 121.543L258.667 114.402L256.907 114.279L256.185 124.594L255.305 124.533L255.471 122.152L254.591 122.091L254.647 121.297L252.886 121.174L252.72 123.555L253.6 123.616L253.489 125.203L254.369 125.265L254.258 126.852L255.139 126.913L255.028 128.5L255.908 128.562L255.797 130.149L263.719 130.703L263.885 128.322L264.765 128.384L264.932 126.003Z" fill="white"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M258.001 123.924L258.881 123.986L258.62 127.726L257.739 127.665L258.001 123.924ZM260.641 124.109L259.761 124.047L259.5 127.788L260.38 127.85L260.641 124.109ZM262.402 124.232L261.521 124.17L261.26 127.911L262.14 127.973L262.402 124.232Z" fill="#141B38"/> <defs> <filter id="shopp_disabled_filter0_dddd" x="16.6698" y="10.1217" width="103.5" height="119.273" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="0.749837"/> <feGaussianBlur stdDeviation="0.468648"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.1137 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.80196"/> <feGaussianBlur stdDeviation="1.12623"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0484671 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.39293"/> <feGaussianBlur stdDeviation="2.12058"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.06 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.05242"/> <feGaussianBlur stdDeviation="3.78276"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0715329 0"/> <feBlend mode="normal" in2="effect3_dropShadow" result="effect4_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow" result="shape"/> </filter> <filter id="shopp_disabled_filter1_dd" x="32.7109" y="44.9595" width="67.165" height="60.9465" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dx="1" dy="1"/> <feGaussianBlur stdDeviation="2"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.13 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="0.5"/> <feGaussianBlur stdDeviation="0.25"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <filter id="shopp_disabled_filter2_dddd" x="185.046" y="16.3272" width="100.784" height="122.124" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="0.749837"/> <feGaussianBlur stdDeviation="0.468648"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.1137 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1.80196"/> <feGaussianBlur stdDeviation="1.12623"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0484671 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3.39293"/> <feGaussianBlur stdDeviation="2.12058"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.06 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6.05242"/> <feGaussianBlur stdDeviation="3.78276"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.101961 0 0 0 0 0.466667 0 0 0 0.0715329 0"/> <feBlend mode="normal" in2="effect3_dropShadow" result="effect4_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow" result="shape"/> </filter> <filter id="shopp_disabled_filter3_dd" x="204.087" y="32.916" width="68.3604" height="54.114" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dx="1" dy="1"/> <feGaussianBlur stdDeviation="2"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.13 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="0.5"/> <feGaussianBlur stdDeviation="0.25"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <linearGradient id="shopp_disabled_paint0_linear" x1="32.1943" y1="17.6504" x2="120.163" y2="93.7021" gradientUnits="userSpaceOnUse"> <stop stop-color="#B5CBEC"/> <stop offset="1" stop-color="#B6CFF4" stop-opacity="0.32"/> </linearGradient> <linearGradient id="shopp_disabled_paint1_linear" x1="94.2114" y1="40.43" x2="116.406" y2="14.3621" gradientUnits="userSpaceOnUse"> <stop stop-color="white"/> <stop offset="0.147864" stop-color="#F6640E"/> <stop offset="0.443974" stop-color="#BA03A7"/> <stop offset="0.733337" stop-color="#6A01B9"/> <stop offset="1" stop-color="#6B01B9"/> </linearGradient> <clipPath id="shopp_disabled_clip0"> <rect width="13" height="13" fill="white" transform="translate(87.7959 18.2437) rotate(-4)"/> </clipPath> <clipPath id="shopp_disabled_clip1"> <rect x="26.1348" y="39.5967" width="81" height="76" rx="2" transform="rotate(-4 26.1348 39.5967)" fill="white"/> </clipPath> </defs> </svg>',
			'shoppableEnabled'    => '<svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#shoppEnabled_filter0_dd)"> <rect x="5" y="1" width="60" height="60" rx="2" fill="white"/> </g> <path d="M19.904 26.2247L5 39.7857V59C5 60.1046 5.89543 61 7 61H63C64.1046 61 65 60.1046 65 59V45.5714L52.4342 31.4716C51.7591 30.7141 50.6236 30.5822 49.7928 31.1648L38.8105 38.8667C38.0444 39.4039 37.0082 39.3382 36.3161 38.7085L22.596 26.2247C21.833 25.5304 20.667 25.5304 19.904 26.2247Z" fill="url(#shoppEnabled_paint0_linear)"/> <rect x="29" y="4" width="29" height="20" rx="2" fill="#0068A0"/> <path d="M37.6002 14.0001C37.6002 12.8601 38.5268 11.9334 39.6668 11.9334H42.3335V10.6667H39.6668C38.7828 10.6667 37.9349 11.0179 37.3098 11.6431C36.6847 12.2682 36.3335 13.116 36.3335 14.0001C36.3335 14.8841 36.6847 15.732 37.3098 16.3571C37.9349 16.9822 38.7828 17.3334 39.6668 17.3334H42.3335V16.0667H39.6668C38.5268 16.0667 37.6002 15.1401 37.6002 14.0001ZM40.3335 14.6667H45.6668V13.3334H40.3335V14.6667ZM46.3335 10.6667H43.6668V11.9334H46.3335C47.4735 11.9334 48.4002 12.8601 48.4002 14.0001C48.4002 15.1401 47.4735 16.0667 46.3335 16.0667H43.6668V17.3334H46.3335C47.2176 17.3334 48.0654 16.9822 48.6905 16.3571C49.3156 15.732 49.6668 14.8841 49.6668 14.0001C49.6668 13.116 49.3156 12.2682 48.6905 11.6431C48.0654 11.0179 47.2176 10.6667 46.3335 10.6667Z" fill="white"/> <path d="M64.1103 30.0086V29.0938H63.0956V28.179H61.0662V27.2643H58.0221V26.3495H55.9926V22.6904H54.9779V21.7756H52.9485V22.6904H51.9338V30.9234H50.9191V30.0086H47.875V33.6677H48.8897V35.4972H49.9044V37.3268H50.9191V39.1563H51.9338V41.9006H63.0956V38.2415H64.1103V35.4972H63.0956V38.2415H62.0809V40.9859H52.9485V39.1563H51.9338V37.3268H50.9191V35.4972H49.9044V33.6677H48.8897V30.9234H50.9191V31.8381H51.9338V34.5825H52.9485V22.6904H54.9779V30.9234H55.9926V27.2643H58.0221V30.9234H59.0368V28.179H61.0662V31.8381H62.0809V29.0938H63.0956V30.0086H64.1103V35.4972H65.125V30.0086H64.1103Z" fill="#141B38"/> <path d="M63.096 35.4972H64.1107V30.0086H63.096V29.0938H62.0813V31.8382H61.0666V28.1791H59.0372V30.9234H58.0225V27.2643H55.9931V30.9234H54.9784V22.6904H52.949V34.5825H51.9343V31.8382H50.9195V30.9234H48.8901V33.6677H49.9048V35.4972H50.9195V37.3268H51.9343V39.1563H52.949V40.9859H62.0813V38.2416H63.096V35.4972Z" fill="white"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M54.9785 33.668H55.9932V37.9805H54.9785V33.668ZM58.0224 33.668H57.0077V37.9805H58.0224V33.668ZM60.0516 33.668H59.0369V37.9805H60.0516V33.668Z" fill="#141B38"/> <defs> <filter id="shoppEnabled_filter0_dd" x="0" y="0" width="70" height="70" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="4"/> <feGaussianBlur stdDeviation="2.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <linearGradient id="shoppEnabled_paint0_linear" x1="35" y1="25" x2="35" y2="61" gradientUnits="userSpaceOnUse"> <stop stop-color="#DCDDE1"/> <stop offset="1" stop-color="#DCDDE1" stop-opacity="0"/> </linearGradient> </defs> </svg>',
		);
		return $builder_svg_icons;
	}

	/**
	 * Plugins information for plugin install modal in all feeds page on select source flow
	 *
	 * @since 6.0
	 *
	 * @return array
	 */
	public function install_plugins_popup() {
		// get the WordPress's core list of installed plugins
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();

		$is_facebook_installed = false;
		$facebook_plugin       = 'custom-facebook-feed/custom-facebook-feed.php';
		if ( isset( $installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'] ) ) {
			$is_facebook_installed = true;
			$facebook_plugin       = 'custom-facebook-feed-pro/custom-facebook-feed.php';
		} elseif ( isset( $installed_plugins['custom-facebook-feed/custom-facebook-feed.php'] ) ) {
			$is_facebook_installed = true;
		}

		$is_twitter_installed = false;
		$twitter_plugin       = 'custom-twitter-feeds/custom-twitter-feed.php';
		if ( isset( $installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'] ) ) {
			$is_twitter_installed = true;
			$twitter_plugin       = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
		} elseif ( isset( $installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'] ) ) {
			$is_twitter_installed = true;
		}

		$is_youtube_installed = false;
		$youtube_plugin       = 'feeds-for-youtube/youtube-feed.php';
		if ( isset( $installed_plugins['youtube-feed-pro/youtube-feed.php'] ) ) {
			$is_youtube_installed = true;
			$youtube_plugin       = 'youtube-feed-pro/youtube-feed.php';
		} elseif ( isset( $installed_plugins['feeds-for-youtube/youtube-feed.php'] ) ) {
			$is_youtube_installed = true;
		}

		return array(
			'facebook' => array(
				'displayName'         => __( 'Facebook', 'instagram-feed' ),
				'name'                => __( 'Facebook Feed', 'instagram-feed' ),
				'author'              => __( 'By Smash Balloon', 'instagram-feed' ),
				'description'         => __( 'To display a Facebook feed, our Facebook plugin is required. </br> It provides a clean and beautiful way to add your Facebook posts to your website. Grab your visitors attention and keep them engaged with your site longer.', 'instagram-feed' ),
				'dashboard_permalink' => admin_url( 'admin.php?page=cff-feed-builder' ),
				'svgIcon'             => '<svg viewBox="0 0 14 15"  width="36" height="36"><path d="M7.00016 0.860001C3.3335 0.860001 0.333496 3.85333 0.333496 7.54C0.333496 10.8733 2.7735 13.64 5.96016 14.14V9.47333H4.26683V7.54H5.96016V6.06667C5.96016 4.39333 6.9535 3.47333 8.48016 3.47333C9.20683 3.47333 9.96683 3.6 9.96683 3.6V5.24667H9.12683C8.30016 5.24667 8.04016 5.76 8.04016 6.28667V7.54H9.8935L9.5935 9.47333H8.04016V14.14C9.61112 13.8919 11.0416 13.0903 12.0734 11.88C13.1053 10.6697 13.6704 9.13043 13.6668 7.54C13.6668 3.85333 10.6668 0.860001 7.00016 0.860001Z" fill="rgb(0, 107, 250)"/></svg>',
				'installed'           => $is_facebook_installed,
				'activated'           => is_plugin_active( $facebook_plugin ),
				'plugin'              => $facebook_plugin,
				'download_plugin'     => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
			),
			'twitter'  => array(
				'displayName'         => __( 'Twitter', 'instagram-feed' ),
				'name'                => __( 'Twitter Feed', 'instagram-feed' ),
				'author'              => __( 'By Smash Balloon', 'instagram-feed' ),
				'description'         => __( 'Custom Twitter Feeds is a highly customizable way to display tweets from your Twitter account. Promote your latest content and update your site content automatically.', 'instagram-feed' ),
				'dashboard_permalink' => admin_url( 'admin.php?page=custom-twitter-feeds' ),
				'svgIcon'             => '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M33.6905 9C32.5355 9.525 31.2905 9.87 30.0005 10.035C31.3205 9.24 32.3405 7.98 32.8205 6.465C31.5755 7.215 30.1955 7.74 28.7405 8.04C27.5555 6.75 25.8905 6 26.0005 6C20.4755 6 17.5955 8.88 17.5955 12.435C17.5955 12.945 17.6555 13.44 17.7605 13.905C12.4205 13.635 7.66555 11.07 4.50055 7.185C3.94555 8.13 3.63055 9.24 3.63055 10.41C3.63055 12.645 4.75555 14.625 6.49555 15.75C5.43055 15.75 4.44055 15.45 3.57055 15V15.045C3.57055 18.165 5.79055 20.775 8.73055 21.36C7.78664 21.6183 6.79569 21.6543 5.83555 21.465C6.24296 22.7437 7.04085 23.8626 8.11707 24.6644C9.19329 25.4662 10.4937 25.9105 11.8355 25.935C9.56099 27.7357 6.74154 28.709 3.84055 28.695C3.33055 28.695 2.82055 28.665 2.31055 28.605C5.16055 30.435 8.55055 31.5 12.1805 31.5C26.0005 31.5 30.4955 21.69 30.4955 13.185C30.4955 12.9 30.4955 12.63 30.4805 12.345C31.7405 11.445 32.8205 10.305 33.6905 9Z" fill="#1B90EF"/></svg>',
				'installed'           => $is_twitter_installed,
				'activated'           => is_plugin_active( $twitter_plugin ),
				'plugin'              => $twitter_plugin,
				'download_plugin'     => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
			),
			'youtube'  => array(
				'displayName'         => __( 'YouTube', 'instagram-feed' ),
				'name'                => __( 'Feeds for YouTube', 'instagram-feed' ),
				'author'              => __( 'By Smash Balloon', 'instagram-feed' ),
				'description'         => __( 'To display a YouTube feed, our YouTube plugin is required. It provides a simple yet powerful way to display videos from YouTube on your website, Increasing engagement with your channel while keeping visitors on your website.', 'instagram-feed' ),
				'dashboard_permalink' => admin_url( 'admin.php?page=youtube-feed' ),
				'svgIcon'             => '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 22.5L22.785 18L15 13.5V22.5ZM32.34 10.755C32.535 11.46 32.67 12.405 32.76 13.605C32.865 14.805 32.91 15.84 32.91 16.74L33 18C33 21.285 32.76 23.7 32.34 25.245C31.965 26.595 31.095 27.465 29.745 27.84C29.04 28.035 27.75 28.17 25.77 28.26C23.82 28.365 22.035 28.41 20.385 28.41L18 28.5C11.715 28.5 7.8 28.26 6.255 27.84C4.905 27.465 6.035 26.595 3.66 25.245C3.465 24.54 3.33 23.595 3.24 22.395C3.135 21.195 3.09 20.16 3.09 19.26L3 18C3 14.715 3.24 12.3 3.66 10.755C6.035 9.405 4.905 8.535 6.255 8.16C6.96 7.965 8.25 7.83 10.23 7.74C12.18 7.635 13.965 7.59 15.615 7.59L18 7.5C24.285 7.5 28.2 7.74 29.745 8.16C31.095 8.535 31.965 9.405 32.34 10.755Z" fill="#EB2121"/></svg>',
				'installed'           => $is_youtube_installed,
				'activated'           => is_plugin_active( $youtube_plugin ),
				'plugin'              => $youtube_plugin,
				'download_plugin'     => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
			),
		);
	}

	/**
	 * Gets a list of info
	 * Used in multiple places in the feed creator
	 * Other Platforms + Social Links
	 * Upgrade links
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_smashballoon_info() {
		$smash_info = array(
			'colorSchemes'   => array(
				'facebook'  => '#006BFA',
				'twitter'   => '#1B90EF',
				'instagram' => '#BA03A7',
				'youtube'   => '#EB2121',
				'linkedin'  => '#007bb6',
				'mail'      => '#666',
				'smash'     => '#EB2121',
			),
			'upgrade'        => array(
				'name' => __( 'Upgrade to Pro', 'instagram-feed' ),
				'icon' => 'instagram',
				'link' => 'https://smashballoon.com/instagram-feed/',
			),
			'platforms'      => array(
				array(
					'name' => __( 'Facebook Feed', 'instagram-feed' ),
					'icon' => 'facebook',
					'link' => 'https://smashballoon.com/instagram-feed/?utm_campaign=instagram-pro&utm_source=balloon&utm_medium=instagram',
				),
				array(
					'name' => __( 'Twitter Feed', 'instagram-feed' ),
					'icon' => 'twitter',
					'link' => 'https://smashballoon.com/custom-twitter-feeds/?utm_campaign=instagram-pro&utm_source=balloon&utm_medium=twitter',
				),
				array(
					'name' => __( 'YouTube Feed', 'instagram-feed' ),
					'icon' => 'youtube',
					'link' => 'https://smashballoon.com/youtube-feed/?utm_campaign=instagram-pro&utm_source=balloon&utm_medium=youtube',
				),
				array(
					'name' => __( 'Social Wall Plugin', 'instagram-feed' ),
					'icon' => 'smash',
					'link' => 'https://smashballoon.com/social-wall/?utm_campaign=instagram-pro&utm_source=balloon&utm_medium=social-wall ',
				),
			),
			'socialProfiles' => array(
				'facebook' => 'https://www.facebook.com/SmashBalloon/',
				'twitter'   => 'https://twitter.com/smashballoon',
			),
			'morePlatforms'  => array( 'instagram', 'youtube', 'twitter' ),
		);

		return $smash_info;
	}

	/**
	 * Text specific to onboarding. Will return an associative array 'active' => false
	 * if onboarding has been dismissed for the user or there aren't any legacy feeds.
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_onboarding_text() {
		// TODO: return if no legacy feeds
		$sbi_statuses_option = get_option( 'sbi_statuses', array() );

		if ( ! isset( $sbi_statuses_option['legacy_onboarding'] ) ) {
			return array( 'active' => false );
		}

		if ( $sbi_statuses_option['legacy_onboarding']['active'] === false
			 || self::onboarding_status() === 'dismissed' ) {
			return array( 'active' => false );
		}

		$type = $sbi_statuses_option['legacy_onboarding']['type'];

		$text = array(
			'active'      => true,
			'type'        => $type,
			'legacyFeeds' => array(
				'heading'     => __( 'Legacy Feed Settings', 'instagram-feed' ),
				'description' => sprintf( __( 'These settings will impact %1$s legacy feeds on your site. You can learn more about what legacy feeds are and how they differ from new feeds %2$shere%3$s.', 'instagram-feed' ), '<span class="cff-fb-count-placeholder"></span>', '<a href="https://smashballoon.com/doc/facebook-legacy-feeds/" target="_blank" rel="noopener">', '</a>' ),
			),
			'getStarted'  => __( 'You can now create and customize feeds individually. Click "Add New" to get started.', 'instagram-feed' ),
		);

		if ( $type === 'single' ) {
			$text['tooltips'] = array(
				array(
					'step'    => 1,
					'heading' => __( 'How you create a feed has changed', 'instagram-feed' ),
					'p'       => __( 'You can now create and customize feeds individually without using shortcode options.', 'instagram-feed' ) . ' ' . __( 'Click "Add New" to get started.', 'instagram-feed' ),
					'pointer' => 'top',
				),
				array(
					'step'    => 2,
					'heading' => __( 'Your existing feed is here', 'instagram-feed' ),
					'p'       => __( 'You can edit your existing feed from here, and all changes will only apply to this feed.', 'instagram-feed' ),
					'pointer' => 'top',
				),
			);
		} else {
			$text['tooltips'] = array(
				array(
					'step'    => 1,
					'heading' => __( 'How you create a feed has changed', 'instagram-feed' ),
					'p'       => __( 'You can now create and customize feeds individually without using shortcode options.', 'instagram-feed' ) . ' ' . __( 'Click "Add New" to get started.', 'instagram-feed' ),
					'pointer' => 'top',
				),
				array(
					'step'    => 2,
					'heading' => __( 'Your existing feeds are under "Legacy" feeds', 'instagram-feed' ),
					'p'       => __( 'You can edit the settings for any existing "legacy" feed (i.e. any feed created prior to this update) here.', 'instagram-feed' ) . ' ' . __( 'This works just like the old settings page and affects all legacy feeds on your site.', 'instagram-feed' ),
				),
				array(
					'step'    => 3,
					'heading' => __( 'Existing feeds work as normal', 'instagram-feed' ),
					'p'       => __( 'You don\'t need to update or change any of your existing feeds. They will continue to work as usual.', 'instagram-feed' ) . ' ' . __( 'This update only affects how new feeds are created and customized.', 'instagram-feed' ),
				),
			);
		}

		return $text;
	}

	public function get_customizer_onboarding_text() {

		if ( self::onboarding_status( 'customizer' ) === 'dismissed' ) {
			return array( 'active' => false );
		}

		$text = array(
			'active'   => true,
			'type'     => 'customizer',
			'tooltips' => array(
				array(
					'step'    => 1,
					'heading' => __( 'Embedding a Feed', 'instagram-feed' ),
					'p'       => __( 'After you are done customizing the feed, click here to add it to a page or a widget.', 'instagram-feed' ),
					'pointer' => 'top',
				),
				array(
					'step'    => 2,
					'heading' => __( 'Customize', 'instagram-feed' ),
					'p'       => __( 'Change your feed layout, color scheme, or customize individual feed sections here.', 'instagram-feed' ),
					'pointer' => 'top',
				),
				array(
					'step'    => 3,
					'heading' => __( 'Settings', 'instagram-feed' ),
					'p'       => __( 'Update your feed source, filter your posts, or change advanced settings here.', 'instagram-feed' ),
					'pointer' => 'top',
				),
			),
		);

		return $text;
	}

	/**
	 * Text related to the feed customizer
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_customize_screens_text() {
		$text = array(
			'common'              => array(
				'preview'       => __( 'Preview', 'instagram-feed' ),
				'help'          => __( 'Help', 'instagram-feed' ),
				'embed'         => __( 'Embed', 'instagram-feed' ),
				'save'          => __( 'Save', 'instagram-feed' ),
				'sections'      => __( 'Sections', 'instagram-feed' ),
				'enable'        => __( 'Enable', 'instagram-feed' ),
				'background'    => __( 'Background', 'instagram-feed' ),
				'text'          => __( 'Text', 'instagram-feed' ),
				'inherit'       => __( 'Inherit from Theme', 'instagram-feed' ),
				'size'          => __( 'Size', 'instagram-feed' ),
				'color'         => __( 'Color', 'instagram-feed' ),
				'height'        => __( 'Height', 'instagram-feed' ),
				'placeholder'   => __( 'Placeholder', 'instagram-feed' ),
				'select'        => __( 'Select', 'instagram-feed' ),
				'enterText'     => __( 'Enter Text', 'instagram-feed' ),
				'hoverState'    => __( 'Hover State', 'instagram-feed' ),
				'sourceCombine' => __( 'Combine sources from multiple platforms using our Social Wall plugin', 'instagram-feed' ),
			),

			'tabs'                => array(
				'customize' => __( 'Customize', 'instagram-feed' ),
				'settings'  => __( 'Settings', 'instagram-feed' ),
			),
			'overview'            => array(
				'feedLayout'  => __( 'Feed Layout', 'instagram-feed' ),
				'colorScheme' => __( 'Color Scheme', 'instagram-feed' ),
				'header'      => __( 'Header', 'instagram-feed' ),
				'posts'       => __( 'Posts', 'instagram-feed' ),
				'likeBox'     => __( 'Like Box', 'instagram-feed' ),
				'loadMore'    => __( 'Load More Button', 'instagram-feed' ),
			),
			'feedLayoutScreen'    => array(
				'layout'     => __( 'Layout', 'instagram-feed' ),
				'list'       => __( 'List', 'instagram-feed' ),
				'grid'       => __( 'Grid', 'instagram-feed' ),
				'masonry'    => __( 'Masonry', 'instagram-feed' ),
				'carousel'   => __( 'Carousel', 'instagram-feed' ),
				'feedHeight' => __( 'Feed Height', 'instagram-feed' ),
				'number'     => __( 'Number of Posts', 'instagram-feed' ),
				'columns'    => __( 'Columns', 'instagram-feed' ),
				'desktop'    => __( 'Desktop', 'instagram-feed' ),
				'tablet'     => __( 'Tablet', 'instagram-feed' ),
				'mobile'     => __( 'Mobile', 'instagram-feed' ),
				'bottomArea' => array(
					'heading'     => __( 'Tweak Post Styles', 'instagram-feed' ),
					'description' => __( 'Change post background, border radius, shadow etc.', 'instagram-feed' ),
				),
			),
			'colorSchemeScreen'   => array(
				'scheme'        => __( 'Scheme', 'instagram-feed' ),
				'light'         => __( 'Light', 'instagram-feed' ),
				'dark'          => __( 'Dark', 'instagram-feed' ),
				'custom'        => __( 'Custom', 'instagram-feed' ),
				'customPalette' => __( 'Custom Palette', 'instagram-feed' ),
				'background2'   => __( 'Background 2', 'instagram-feed' ),
				'text2'         => __( 'Text 2', 'instagram-feed' ),
				'link'          => __( 'Link', 'instagram-feed' ),
				'bottomArea'    => array(
					'heading'     => __( 'Overrides', 'instagram-feed' ),
					'description' => __( 'Colors that have been overridden from individual post element settings will not change. To change them, you will have to reset overrides.', 'instagram-feed' ),
					'ctaButton'   => __( 'Reset Overrides.', 'instagram-feed' ),
				),
			),
			'headerScreen'        => array(
				'headerType'     => __( 'Header Type', 'instagram-feed' ),
				'visual'         => __( 'Visual', 'instagram-feed' ),
				'coverPhoto'     => __( 'Cover Photo', 'instagram-feed' ),
				'nameAndAvatar'  => __( 'Name and avatar', 'instagram-feed' ),
				'about'          => __( 'About (bio and Likes)', 'instagram-feed' ),
				'displayOutside' => __( 'Display outside scrollable area', 'instagram-feed' ),
				'icon'           => __( 'Icon', 'instagram-feed' ),
				'iconImage'      => __( 'Icon Image', 'instagram-feed' ),
				'iconColor'      => __( 'Icon Color', 'instagram-feed' ),
			),
			// all Lightbox in common
			// all Load More in common
			'likeBoxScreen'       => array(
				'small'                     => __( 'Small', 'instagram-feed' ),
				'large'                     => __( 'Large', 'instagram-feed' ),
				'coverPhoto'                => __( 'Cover Photo', 'instagram-feed' ),
				'customWidth'               => __( 'Custom Width', 'instagram-feed' ),
				'defaultSetTo'              => __( 'By default, it is set to auto', 'instagram-feed' ),
				'width'                     => __( 'Width', 'instagram-feed' ),
				'customCTA'                 => __( 'Custom CTA', 'instagram-feed' ),
				'customCTADescription'      => __( 'This toggles the custom CTA like "Show now" and "Contact"', 'instagram-feed' ),
				'showFans'                  => __( 'Show Fans', 'instagram-feed' ),
				'showFansDescription'       => __( 'Show visitors which of their friends follow your page', 'instagram-feed' ),
				'displayOutside'            => __( 'Display outside scrollable area', 'instagram-feed' ),
				'displayOutsideDescription' => __( 'Make the like box fixed by moving it outside the scrollable area', 'instagram-feed' ),
			),
			'postsScreen'         => array(
				'thumbnail'           => __( 'Thumbnail', 'instagram-feed' ),
				'half'                => __( 'Half width', 'instagram-feed' ),
				'full'                => __( 'Full width', 'instagram-feed' ),
				'useFull'             => __( 'Use full width layout when post width is less than 500px', 'instagram-feed' ),
				'postStyle'           => __( 'Post Style', 'instagram-feed' ),
				'editIndividual'      => __( 'Edit Individual Elements', 'instagram-feed' ),
				'individual'          => array(
					'description'                => __( 'Hide or show individual elements of a post or edit their options', 'instagram-feed' ),
					'name'                       => __( 'Name', 'instagram-feed' ),
					'edit'                       => __( 'Edit', 'instagram-feed' ),
					'postAuthor'                 => __( 'Post Author', 'instagram-feed' ),
					'postText'                   => __( 'Post Text', 'instagram-feed' ),
					'date'                       => __( 'Date', 'instagram-feed' ),
					'photosVideos'               => __( 'Photos/Videos', 'instagram-feed' ),
					'likesShares'                => __( 'Likes, Shares and Comments', 'instagram-feed' ),
					'eventTitle'                 => __( 'Event Title', 'instagram-feed' ),
					'eventDetails'               => __( 'Event Details', 'instagram-feed' ),
					'postAction'                 => __( 'Post Action Links', 'instagram-feed' ),
					'sharedPostText'             => __( 'Shared Post Text', 'instagram-feed' ),
					'sharedLinkBox'              => __( 'Shared Link Box', 'instagram-feed' ),
					'postTextDescription'        => __( 'The main text of the Instagram post', 'instagram-feed' ),
					'maxTextLength'              => __( 'Maximum Text Length', 'instagram-feed' ),
					'characters'                 => __( 'Characters', 'instagram-feed' ),
					'linkText'                   => __( 'Link text to Instagram post', 'instagram-feed' ),
					'postDateDescription'        => __( 'The date of the post', 'instagram-feed' ),
					'format'                     => __( 'Format', 'instagram-feed' ),
					'custom'                     => __( 'Custom', 'instagram-feed' ),
					'learnMoreFormats'           => '<a href="https://smashballoon.com/doc/date-formatting-reference/" target="_blank" rel="noopener">' . __( 'Learn more about custom formats', 'instagram-feed' ) . '</a>',
					'addTextBefore'              => __( 'Add text before date', 'instagram-feed' ),
					'addTextBeforeEG'            => __( 'E.g. Posted', 'instagram-feed' ),
					'addTextAfter'               => __( 'Add text after date', 'instagram-feed' ),
					'addTextAfterEG'             => __( 'E.g. - posted date', 'instagram-feed' ),
					'timezone'                   => __( 'Timezone', 'instagram-feed' ),
					'tzDescription'              => __( 'Timezone settings are global across all feeds. To update it use the global settings.', 'instagram-feed' ),
					'tzCTAText'                  => __( 'Go to Global Settings', 'instagram-feed' ),
					'photosVideosDescription'    => __( 'Any photos or videos in your posts', 'instagram-feed' ),
					'useOnlyOne'                 => __( 'Use only one image per post', 'instagram-feed' ),
					'postActionLinksDescription' => __( 'The "View on Instagram" and "Share" links at the bottom of each post', 'instagram-feed' ),
					'viewOnFBLink'               => __( 'View on Instagram link', 'instagram-feed' ),
					'viewOnFBLinkDescription'    => __( 'Toggle "View on Instagram" link below each post', 'instagram-feed' ),
					'customizeText'              => __( 'Customize Text', 'instagram-feed' ),
					'shareLink'                  => __( 'Share Link', 'instagram-feed' ),
					'shareLinkDescription'       => __( 'Toggle "Share" link below each post', 'instagram-feed' ),
					'likesSharesDescription'     => __( 'The comments box displayed at the bottom of each timeline post', 'instagram-feed' ),
					'iconTheme'                  => __( 'Icon Theme', 'instagram-feed' ),
					'auto'                       => __( 'Auto', 'instagram-feed' ),
					'light'                      => __( 'Light', 'instagram-feed' ),
					'dark'                       => __( 'Dark', 'instagram-feed' ),
					'expandComments'             => __( 'Expand comments box by default', 'instagram-feed' ),
					'hideComment'                => __( 'Hide comment avatars', 'instagram-feed' ),
					'showLightbox'               => __( 'Show comments in lightbox', 'instagram-feed' ),
					'eventTitleDescription'      => __( 'The title of an event', 'instagram-feed' ),
					'eventDetailsDescription'    => __( 'The information associated with an event', 'instagram-feed' ),
					'textSize'                   => __( 'Text Size', 'instagram-feed' ),
					'textColor'                  => __( 'Text Color', 'instagram-feed' ),
					'sharedLinkBoxDescription'   => __( "The link info box that's created when a link is shared in a Instagram post", 'instagram-feed' ),
					'boxStyle'                   => __( 'Box Style', 'instagram-feed' ),
					'removeBackground'           => __( 'Remove background/border', 'instagram-feed' ),
					'linkTitle'                  => __( 'Link Title', 'instagram-feed' ),
					'linkURL'                    => __( 'Link URL', 'instagram-feed' ),
					'linkDescription'            => __( 'Link Description', 'instagram-feed' ),
					'chars'                      => __( 'chars', 'instagram-feed' ),
					'sharedPostDescription'      => __( 'The description text associated with shared photos, videos, or links', 'instagram-feed' ),
				),
				'postType'            => __( 'Post Type', 'instagram-feed' ),
				'boxed'               => __( 'boxed', 'instagram-feed' ),
				'regular'             => __( 'Regular', 'instagram-feed' ),
				'indvidualProperties' => __( 'Indvidual Properties', 'instagram-feed' ),
				'backgroundColor'     => __( 'Background Color', 'instagram-feed' ),
				'borderRadius'        => __( 'Border Radius', 'instagram-feed' ),
				'boxShadow'           => __( 'Box Shadow', 'instagram-feed' ),
			),
			'shoppableFeedScreen' => array(
				'heading1'     => __( 'Make your Instagram Feed Shoppable', 'instagram-feed' ),
				'description1' => __( 'This feature links the post to the one specificed in your caption.<br/><br/>Don’t want to add links to the caption? You can add links manually to each post.<br/><br/>Enable it to get started.', 'instagram-feed' ),
				'heading2'     => __( 'Tap “Add” or “Update” on an<br/>image to add/update it’s URL', 'instagram-feed' ),

			),
		);

		$text['onboarding'] = $this->get_customizer_onboarding_text();

		return $text;
	}

	/**
	 * Returns an associate array of all existing sources along with their data
	 *
	 * @param int $page
	 *
	 * @return array
	 *
	 * @since 6.0
	 */

	public static function get_source_list( $page = 1 ) {
		$args['page'] = $page;
		$source_data  = SBI_Db::source_query( $args );

		$encryption   = new \InstagramFeed\SB_Instagram_Data_Encryption();

		$return = array();
		foreach ( $source_data as $source ) {
			$info                  = ! empty( $source['info'] ) ? json_decode( $encryption->decrypt( $source['info'] ), true ) : array();
			$source['header_data'] = $info;

			$settings = array( 'gdpr' => 'no' );

			$avatar = \SB_Instagram_Parse::get_avatar( $info, $settings );

			if ( \SB_Instagram_Connected_Account::local_avatar_exists( $source['username'] ) ) {
				$source['local_avatar_url'] = \SB_Instagram_Connected_Account::get_local_avatar_url( $source['username'] );
				$source['local_avatar']     = \SB_Instagram_Connected_Account::get_local_avatar_url( $source['username'] );
			} else {
				$source['local_avatar'] = false;
			}

			$source['avatar_url']       = $avatar;
			$source['just_added']       = ( ! empty( $_GET['sbi_username'] ) && isset( $info['username'] ) && $info['username'] === $_GET['sbi_username'] );
			$source['error_encryption'] = false;
			if ( isset( $source['access_token'] ) && strpos( $source['access_token'], 'IG' ) === false && strpos( $source['access_token'], 'EA' ) === false && ! $encryption->decrypt( $source['access_token'] ) ) {
				$source['error_encryption'] = true;
			}

			$return[] = $source;
		}

		return $return;
	}

	/**
	 * Check if the account source type is business
	 *
	 * @since 2.0
	 *
	 */
	public static function is_business_source() {
		$source_list = self::get_source_list();
		$business_type = false;

		foreach( $source_list as $source ) {
			if ( ( isset( $source['account_type'] ) && isset( $source['type'] ) ) && ( $source['account_type'] == 'business' || $source['type'] == 'business' ) ) {
				$business_type = true;
			}
		}

		return $business_type;
	}

	/**
	 * Get Links with UTM
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_links_with_utm() {
		$license_key = null;
		if ( get_option( 'sbi_license_key' ) ) {
			$license_key = get_option( 'sbi_license_key' );
		}
		$all_access_bundle       = sprintf( 'https://smashballoon.com/all-access/?edd_license_key=%s&upgrade=true&utm_campaign=instagram-pro&utm_source=all-feeds&utm_medium=footer-banner&utm_content=learn-more', $license_key );
		$all_access_bundle_popup = sprintf( 'https://smashballoon.com/all-access/?edd_license_key=%s&upgrade=true&utm_campaign=instagram-pro&utm_source=balloon&utm_medium=all-access', $license_key );
		$sourceCombineCTA        = sprintf( 'https://smashballoon.com/social-wall/?edd_license_key=%s&upgrade=true&utm_campaign=instagram-pro&utm_source=customizer&utm_medium=sources&utm_content=social-wall', $license_key );

		return array(
			'allAccessBundle'  => $all_access_bundle,
			'popup'            => array(
				'allAccessBundle' => $all_access_bundle_popup,
				'fbProfile'       => 'https://www.facebook.com/SmashBalloon/',
				'twitterProfile'  => 'https://twitter.com/smashballoon',
			),
			'sourceCombineCTA' => $sourceCombineCTA,
			'multifeedCTA'     => 'https://smashballoon.com/extensions/multifeed/?utm_campaign=instagram-pro&utm_source=customizer&utm_medium=sources&utm_content=multifeed',
			'doc'              => 'https://smashballoon.com/docs/instagram/?utm_campaign=instagram-pro&utm_source=support&utm_medium=view-documentation-button&utm_content=view-documentation',
			'blog'             => 'https://smashballoon.com/blog/?utm_campaign=instagram-pro&utm_source=support&utm_medium=view-blog-button&utm_content=view-blog',
			'gettingStarted'   => 'https://smashballoon.com/docs/getting-started/?instagram&utm_campaign=instagram-pro&utm_source=support&utm_medium=getting-started-button&utm_content=getting-started',
		);
	}

	public static function get_social_wall_links() {
		return array(
			'<a href="' . esc_url( admin_url( 'admin.php?page=sbi-feed-builder' ) ) . '">' . __( 'All Feeds', 'instagram-feed' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'admin.php?page=sbi-settings' ) ) . '">' . __( 'Settings', 'instagram-feed' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'admin.php?page=sbi-oembeds-manager' ) ) . '">' . __( 'oEmbeds', 'instagram-feed' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'admin.php?page=sbi-extensions-manager' ) ) . '">' . __( 'Extensions', 'instagram-feed' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'admin.php?page=sbi-about-us' ) ) . '">' . __( 'About Us', 'instagram-feed' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'admin.php?page=sbi-support' ) ) . '">' . __( 'Support', 'instagram-feed' ) . '</a>',
		);
	}

	/**
	 * Returns an associate array of all existing feeds along with their data
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_feed_list( $feeds_args = array() ) {
		if ( ! empty( $_GET['feed_id'] ) ) {
			return array();
		}
		$feeds_data = SBI_Db::feeds_query( $feeds_args );

		$i = 0;
		foreach ( $feeds_data as $single_feed ) {
			$args  = array(
				'feed_id'       => '*' . $single_feed['id'],
				'html_location' => array( 'content' ),
			);
			$count = \SB_Instagram_Feed_Locator::count( $args );

			$content_locations = \SB_Instagram_Feed_Locator::instagram_feed_locator_query( $args );

			// if this is the last page, add in the header footer and sidebar locations
			if ( count( $content_locations ) < SBI_Db::RESULTS_PER_PAGE ) {

				$args            = array(
					'feed_id'       => '*' . $single_feed['id'],
					'html_location' => array( 'header', 'footer', 'sidebar' ),
					'group_by'      => 'html_location',
				);
				$other_locations = \SB_Instagram_Feed_Locator::instagram_feed_locator_query( $args );

				$locations = array();

				$combined_locations = array_merge( $other_locations, $content_locations );
			} else {
				$combined_locations = $content_locations;
			}

			foreach ( $combined_locations as $location ) {
				$page_text = get_the_title( $location['post_id'] );
				if ( $location['html_location'] === 'header' ) {
					$html_location = __( 'Header', 'instagram-feed' );
				} elseif ( $location['html_location'] === 'footer' ) {
					$html_location = __( 'Footer', 'instagram-feed' );
				} elseif ( $location['html_location'] === 'sidebar' ) {
					$html_location = __( 'Sidebar', 'instagram-feed' );
				} else {
					$html_location = __( 'Content', 'instagram-feed' );
				}
				$shortcode_atts = json_decode( $location['shortcode_atts'], true );
				$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

				$full_shortcode_string = '[instagram-feed';
				foreach ( $shortcode_atts as $key => $value ) {
					if ( ! empty( $value ) ) {
						$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
					}
				}
				$full_shortcode_string .= ']';

				$locations[] = array(
					'link'          => esc_url( get_the_permalink( $location['post_id'] ) ),
					'page_text'     => $page_text,
					'html_location' => $html_location,
					'shortcode'     => $full_shortcode_string,
				);
			}
			$feeds_data[ $i ]['instance_count']   = $count;
			$feeds_data[ $i ]['location_summary'] = $locations;
			$settings                             = json_decode( $feeds_data[ $i ]['settings'], true );

			$settings['feed'] = $single_feed['id'];

			$instagram_feed_settings = new \SB_Instagram_Settings_Pro( $settings, sbi_defaults() );

			$feeds_data[ $i ]['settings'] = $instagram_feed_settings->get_settings();

			$i++;
		}
		return $feeds_data;
	}

	/**
	 * Returns an associate array of all existing sources along with their data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_legacy_feed_list() {
		if ( ! empty( $_GET['feed_id'] ) ) {
			return array();
		}
		$sbi_statuses = get_option( 'sbi_statuses', array() );
		$sources_list = self::get_source_list();

		if ( empty( $sbi_statuses['support_legacy_shortcode'] ) ) {
			return array();
		}

		$args       = array(
			'html_location' => array( 'header', 'footer', 'sidebar', 'content' ),
			'group_by'      => 'shortcode_atts',
			'page'          => 1,
		);
		$feeds_data = \SB_Instagram_Feed_Locator::legacy_instagram_feed_locator_query( $args );

		if ( empty( $feeds_data ) ) {
			$args       = array(
				'html_location' => array( 'header', 'footer', 'sidebar', 'content' ),
				'group_by'      => 'shortcode_atts',
				'page'          => 1,
			);
			$feeds_data = \SB_Instagram_Feed_Locator::legacy_instagram_feed_locator_query( $args );
		}

		$feed_saver = new SBI_Feed_Saver( 'legacy' );
		$settings   = $feed_saver->get_feed_settings();

		$default_type = 'timeline';

		if ( isset( $settings['feedtype'] ) ) {
			$default_type = $settings['feedtype'];

		} elseif ( isset( $settings['type'] ) ) {
			if ( strpos( $settings['type'], ',' ) === false ) {
				$default_type = $settings['type'];
			}
		}
		$i       = 0;
		$reindex = false;
		foreach ( $feeds_data as $single_feed ) {
			$args              = array(
				'shortcode_atts' => $single_feed['shortcode_atts'],
				'html_location'  => array( 'content' ),
			);
			$content_locations = \SB_Instagram_Feed_Locator::instagram_feed_locator_query( $args );

			$count = \SB_Instagram_Feed_Locator::count( $args );
			if ( count( $content_locations ) < SBI_Db::RESULTS_PER_PAGE ) {

				$args            = array(
					'feed_id'       => $single_feed['feed_id'],
					'html_location' => array( 'header', 'footer', 'sidebar' ),
					'group_by'      => 'html_location',
				);
				$other_locations = \SB_Instagram_Feed_Locator::instagram_feed_locator_query( $args );

				$combined_locations = array_merge( $other_locations, $content_locations );
			} else {
				$combined_locations = $content_locations;
			}

			$locations = array();
			foreach ( $combined_locations as $location ) {
				$page_text = get_the_title( $location['post_id'] );
				if ( $location['html_location'] === 'header' ) {
					$html_location = __( 'Header', 'instagram-feed' );
				} elseif ( $location['html_location'] === 'footer' ) {
					$html_location = __( 'Footer', 'instagram-feed' );
				} elseif ( $location['html_location'] === 'sidebar' ) {
					$html_location = __( 'Sidebar', 'instagram-feed' );
				} else {
					$html_location = __( 'Content', 'instagram-feed' );
				}
				$shortcode_atts = json_decode( $location['shortcode_atts'], true );
				$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

				$full_shortcode_string = '[instagram-feed';
				foreach ( $shortcode_atts as $key => $value ) {
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							$value = implode( ',', $value );
						}
						$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
					}
				}
				$full_shortcode_string .= ']';

				$locations[] = array(
					'link'          => esc_url( get_the_permalink( $location['post_id'] ) ),
					'page_text'     => $page_text,
					'html_location' => $html_location,
					'shortcode'     => $full_shortcode_string,
				);
			}
			$shortcode_atts = json_decode( $feeds_data[ $i ]['shortcode_atts'], true );
			$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

			$full_shortcode_string = '[instagram-feed';
			foreach ( $shortcode_atts as $key => $value ) {
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$value = implode( ',', $value );
					}
					$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
				}
			}
			$full_shortcode_string .= ']';

			$feeds_data[ $i ]['shortcode']        = $full_shortcode_string;
			$feeds_data[ $i ]['instance_count']   = $count;
			$feeds_data[ $i ]['location_summary'] = $locations;
			$feeds_data[ $i ]['feed_name']        = self::get_legacy_feed_name( $sources_list, $feeds_data[ $i ]['feed_id'] );
			$feeds_data[ $i ]['feed_type']        = $default_type;

			if ( isset( $shortcode_atts['feedtype'] ) ) {
				$feeds_data[ $i ]['feed_type'] = $shortcode_atts['feedtype'];

			} elseif ( isset( $shortcode_atts['type'] ) ) {
				if ( strpos( $shortcode_atts['type'], ',' ) === false ) {
					$feeds_data[ $i ]['feed_type'] = $shortcode_atts['type'];
				}
			}

			if ( isset( $feeds_data[ $i ]['id'] ) ) {
				unset( $feeds_data[ $i ]['id'] );
			}

			if ( isset( $feeds_data[ $i ]['html_location'] ) ) {
				unset( $feeds_data[ $i ]['html_location'] );
			}

			if ( isset( $feeds_data[ $i ]['last_update'] ) ) {
				unset( $feeds_data[ $i ]['last_update'] );
			}

			if ( isset( $feeds_data[ $i ]['post_id'] ) ) {
				unset( $feeds_data[ $i ]['post_id'] );
			}

			if ( ! empty( $shortcode_atts['feed'] ) ) {
				$reindex = true;
				unset( $feeds_data[ $i ] );
			}

			if ( isset( $feeds_data[ $i ]['shortcode_atts'] ) ) {
				unset( $feeds_data[ $i ]['shortcode_atts'] );
			}

			$i++;
		}

		if ( $reindex ) {
			$feeds_data = array_values( $feeds_data );
		}

		// if there were no feeds found in the locator table we still want the legacy settings to be available
		// if it appears as though they had used version 3.x or under at some point.
		if ( empty( $feeds_data )
			 && ! is_array( $sbi_statuses['support_legacy_shortcode'] )
			 && ( $sbi_statuses['support_legacy_shortcode'] ) ) {

			$feeds_data = array(
				array(
					'feed_id'          => __( 'Legacy Feed', 'instagram-feed' ) . ' ' . __( '(unknown location)', 'instagram-feed' ),
					'feed_name'        => __( 'Legacy Feed', 'instagram-feed' ) . ' ' . __( '(unknown location)', 'instagram-feed' ),
					'shortcode'        => '[instagram-feed]',
					'feed_type'        => '',
					'instance_count'   => false,
					'location_summary' => array(),
				),
			);
		}

		return $feeds_data;
	}

	public static function get_legacy_feed_name( $sources_list, $source_id ) {
		foreach ( $sources_list as $source ) {
			if ( $source['account_id'] === $source_id ) {
				return $source['username'];
			}
		}
		return $source_id;
	}

	/**
	 * Status of the onboarding sequence for specific user
	 *
	 * @return string|boolean
	 *
	 * @since 6.0
	 */
	public static function onboarding_status( $type = 'newuser' ) {
		$onboarding_statuses = get_user_meta( get_current_user_id(), 'sbi_onboarding', true );
		$status              = false;
		if ( ! empty( $onboarding_statuses ) ) {
			$statuses = maybe_unserialize( $onboarding_statuses );
			$status   = isset( $statuses[ $type ] ) ? $statuses[ $type ] : false;
		}

		return $status;
	}

	/**
	 * Update status of onboarding sequence for specific user
	 *
	 * @since 6.0
	 */
	public static function update_onboarding_meta( $value, $type = 'newuser' ) {
		$onboarding_statuses = get_user_meta( get_current_user_id(), 'sbi_onboarding', true );
		if ( ! empty( $onboarding_statuses ) ) {
			$statuses          = maybe_unserialize( $onboarding_statuses );
			$statuses[ $type ] = $value;
		} else {
			$statuses = array(
				$type => $value,
			);
		}

		$statuses = maybe_serialize( $statuses );

		update_user_meta( get_current_user_id(), 'sbi_onboarding', $statuses );
	}

	/**
	 * Used to dismiss onboarding using AJAX
	 *
	 * @since 6.0
	 */
	public static function after_dismiss_onboarding() {
		check_ajax_referer( 'sbi-admin', 'nonce' );

		if ( sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			$type = 'newuser';
			if ( isset( $_POST['was_active'] ) ) {
				$type = sanitize_text_field( $_POST['was_active'] );
			}
			self::update_onboarding_meta( 'dismissed', $type );
		}
		wp_send_json_success();
	}

	public static function add_customizer_att( $atts ) {
		if ( ! is_array( $atts ) ) {
			$atts = array();
		}
		$atts['feedtype'] = 'customizer';
		return $atts;
	}

	/**
	 * Feed Builder Wrapper.
	 *
	 * @since 6.0
	 */
	public function feed_builder() {
		include_once SBI_BUILDER_DIR . 'templates/builder.php';
	}

	/**
	 * For types listed on the top of the select feed type screen
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_feed_types() {
		$feed_types = array(
			array(
				'type'        => 'user',
				'title'       => __( 'User Timeline', 'instagram-feed' ),
				'description' => __( 'Fetch posts from your  Instagram profile', 'instagram-feed' ),
				'icon'        => 'usertimelineIcon',
			),
			array(
				'type'             => 'hashtag',
				'title'            => __( 'Public Hashtag', 'instagram-feed' ),
				'description'      => __( 'Fetch posts from a public Instagram hashtag', 'instagram-feed' ),
				'tooltip'          => __( 'Hashtag feeds require a connected Instagram business account', 'instagram-feed' ),
				'businessRequired' => true,
				'icon'             => 'publichashtagIcon',
			),
			array(
				'type'             => 'tagged',
				'title'            => __( 'Tagged Posts', 'instagram-feed' ),
				'description'      => __( 'Display posts your Instagram account has been tagged in', 'instagram-feed' ),
				'tooltip'          => __( 'Tagged posts feeds require a connected Instagram business account', 'instagram-feed' ),
				'businessRequired' => true,
				'icon'             => 'taggedpostsIcon',
			),
			array(
				'type'        => 'socialwall',
				'title'       => __( 'Social Wall', 'instagram-feed' ) . '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.94901 13.7934L6.86234 11.2401C7.90901 10.8534 8.88901 10.3334 9.79568 9.72677L7.94901 13.7934ZM2.95568 7.33344L0.402344 6.24677L4.46901 4.4001C3.86234 5.30677 3.34234 6.28677 2.95568 7.33344ZM13.6023 0.593436C13.6023 0.593436 10.3023 -0.820564 6.52901 2.95344C5.06901 4.41344 4.19568 6.0201 3.62901 7.42677C3.44234 7.92677 3.56901 8.47344 3.93568 8.84677L5.35568 10.2601C5.72234 10.6334 6.26901 10.7534 6.76901 10.5668C8.44804 9.92657 9.97256 8.93825 11.2423 7.66677C15.0157 3.89344 13.6023 0.593436 13.6023 0.593436ZM8.88901 5.30677C8.36901 4.78677 8.36901 3.9401 8.88901 3.4201C9.40901 2.9001 10.2557 2.9001 10.7757 3.4201C11.289 3.9401 11.2957 4.78677 10.7757 5.30677C10.2557 5.82677 9.40901 5.82677 8.88901 5.30677ZM4.02247 13.0001L5.78234 11.2401C5.55568 11.1801 5.33568 11.0801 5.13568 10.9401L3.08247 13.0001H4.02247ZM1.1958 13.0001H2.1358L4.64901 10.4934L3.70234 9.55344L1.1958 12.0601V13.0001ZM1.1958 11.1134L3.25568 9.0601C3.11568 8.8601 3.01568 8.64677 2.95568 8.41344L1.1958 10.1734V11.1134Z" fill="#FE544F"/></svg>',
				'description' => __( 'Create a feed with sources from different social platforms', 'instagram-feed' ),
				'icon'        => 'socialwall1Icon',
			),
		);

		return $feed_types;
	}


}


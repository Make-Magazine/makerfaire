<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function sbsw_admin_init() {
	$sbsw_settings = get_option( 'sbsw_settings', array() );

	$base_path = trailingslashit( SBSW_PLUGIN_DIR ) . 'inc/admin/templates';
	$slug = SBSW_SLUG;
	$plugin_name = SBSW_PLUGIN_NAME;
	$capability = current_user_can( 'manage_options' ) ? 'manage_options' : 'manage_social_wall_feed_options';
	$icon = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1438 1878" stroke-linejoin="round" stroke-miterlimit="2">
		  <path d="M671.51004 492.9884C539.9423 433.8663 402.90125 345.5722 274.97656 304.47286c45.45163 108.39592 83.81332 223.88017 123.51 338.03105C319.308 702.00293 226.8217 748.19258 138.46278 798.51607c75.1914 74.32371 181.67968 117.34651 266.52444 182.01607-67.96124 83.86195-201.48527 171.01801-234.02107 247.01998 140.6922-17.6268 304.63688-46.21031 435.53794-52.00418 28.76427 144.58328 43.5987 303.09763 84.50756 435.53713 60.92033-175.26574 116.0014-356.37317 188.51594-520.0451 111.90644 46.2857 248.29012 102.72607 357.52902 130.01188-76.64636-107.5347-146.59346-221.76948-214.5166-338.02903 100.51162-72.83876 202.1718-144.52451 299.02538-221.02092-136.89514-12.61229-278.73428-20.28827-422.53618-25.99865-22.85288-148.33212-16.84826-325.51604-52.005-461.53983-53.19327 111.4882-115.96694 213.39155-175.51418 318.52497m65.00513 1228.60735c-18.0795 77.37586 41.4876 109.11326 32.50298 156.01215-58.8141-20.268-103.0576-30.67962-182.01567-19.50203 2.47018-60.37036 56.76662-68.90959 45.50432-143.0108C-208.90184 1619.4318-210.59186 99.02478 626.00572 5.44992c1046.0409-117.00405 1078.86445 1689.2596 110.50945 1716.14582" fill="#fff"/>
		</svg>' );
	$position = 99;
	$tabs = array(
		array(
			'title' => __( 'Configure Wall', SBSW_TEXT_DOMAIN ),
			'slug' => 'configure',
			'capability' => $capability,
			'next_step_instructions' => __( 'Customize your feed', SBSW_TEXT_DOMAIN ),
			'numbered_tab' => false
		),
		array(
			'title' => __( 'Customize', SBSW_TEXT_DOMAIN ),
			'slug' => 'customize',
			'capability' => $capability,
			'numbered_tab' => false
		),
		array(
			'title' => __( 'Support', SBSW_TEXT_DOMAIN ),
			'slug' => 'support',
			'capability' => $capability,
			'numbered_tab' => false
		),
		array(
			'title' => __( 'License', SBSW_TEXT_DOMAIN ),
			'slug' => 'license',
			'capability' => $capability,
			'numbered_tab' => false
		)
	);

	$active_tab = $tabs[0]['slug'];
	if ( isset( $_GET['tab'] ) ) {
		$active_tab = sanitize_text_field( $_GET['tab'] ); $tabs[0]['slug'];
	} elseif ( isset( $_GET['page'] ) ) {
		foreach ( $tabs as $tab ) {
			if ( $_GET['page'] === $slug . '_' . $tab['slug'] ) {
				$active_tab = $tab['slug'];
			}
		}
	}
	$vars = new SW_Vars();
	$admin = new SW_Admin( $vars, $base_path, $slug, $plugin_name, $capability, $icon, $position, $tabs, $sbsw_settings, $active_tab, 'sbsw_settings' );

	$text_domain = SBSW_TEXT_DOMAIN;
	/* Layout */
	$layouts = array(
		array(
			'slug' => 'masonry',
			'label' => __( 'Masonry', $text_domain ),
			'image' => 'img/masonry.png',
			'note' => __( 'Video thumbnails are displayed in columns and play in a lightbox when clicked.', $text_domain ),
			'options' => array(
				array(
					'name' => 'cols',
					'callback' => 'select',
					'label' => __( 'Columns', $text_domain ),
					'min' => 1,
					'max' => 7,
					'default' => 3,
					'shortcode' => array(
						'example' => '3',
						'description' => __( 'Videos in carousel when 480px screen width or less.', $text_domain ),
					)
				),
				array(
					'name' => 'colsmobile',
					'callback' => 'select',
					'label' => __( 'Mobile Columns', $text_domain ),
					'min' => 1,
					'max' => 2,
					'default' => 2,
					'shortcode' => array(
						'example' => '2',
						'description' => __( 'Columns when 480px screen width or less.', $text_domain ),
					)
				),
				array(
					'name' => 'showfilter',
					'callback' => 'checkbox',
					'label' => __( 'Show Social Media Feed Filter', $text_domain ),
					'default' => false,
					'shortcode' => array(
						'example' => 'false',
						'description' => __( 'Include a row of buttons to filter the feed by social media type at the top of the feed.', $text_domain ),
					)
				)
			)
		),
		array(
			'slug' => 'list',
			'label' => __( 'List', $text_domain ),
			'image' => 'img/list.png',
			'note' => __( 'A single columns of videos that play when clicked.', $text_domain ),
		),
		array(
			'slug' => 'carousel',
			'label' => __( 'Carousel', $text_domain ),
			'image' => 'img/carousel.png',
			'note' => __( 'Posts are displayed in a slideshow carousel.', $text_domain ),
			'options' => array(
				array(
					'name' => 'cols',
					'callback' => 'select',
					'label' => __( 'Columns', $text_domain ),
					'min' => 1,
					'max' => 7,
					'default' => 3,
					'shortcode' => array(
						'example' => '3',
						'description' => __( 'Videos in carousel when 480px screen width or less.', $text_domain ),
					)
				),
				array(
					'name' => 'colsmobile',
					'callback' => 'select',
					'label' => __( 'Mobile Columns', $text_domain ),
					'min' => 1,
					'max' => 2,
					'default' => 2,
					'shortcode' => array(
						'example' => '2',
						'description' => __( 'Columns when 480px screen width or less.', $text_domain ),
					)				),
				array(
					'name' => 'rows',
					'callback' => 'select',
					'label' => __( 'Number of Rows', $text_domain ),
					'min' => 1,
					'max' => 2,
					'default' => 1,
					'shortcode' => array(
						'example' => '2',
						'description' => __( 'Choose 2 rows to show two posts in a single slide.', $text_domain ),
					)
				),
				array(
					'name' => 'loop',
					'callback' => 'select',
					'label' => __( 'Loop Type', $text_domain ),
					'options' => array(
						array(
							'label' => __( 'Rewind', $text_domain ),
							'value' => 'rewind'
						),
						array(
							'label' => __( 'Infinity', $text_domain ),
							'value' => 'infinity'
						)
					),
					'default' => 'rewind',
					'shortcode' => array(
						'example' => 'infinity',
						'description' => __( 'What happens when the last slide is reached.', $text_domain ),
					)
				),
				array(
					'name' => 'arrows',
					'callback' => 'checkbox',
					'label' => __( 'Show Navigation Arrows', $text_domain ),
					'default' => true,
					'shortcode' => array(
						'example' => 'false',
						'description' => __( 'Show arrows on the sides to navigate posts.', $text_domain ),
					)
				),
				array(
					'name' => 'pag',
					'callback' => 'checkbox',
					'label' => __( 'Show Pagination', $text_domain ),
					'default' => true,
					'shortcode' => array(
						'example' => 'false',
						'description' => __( 'Show dots below carousel for an ordinal indication of which slide is being shown.', $text_domain ),
					)
				),
				array(
					'name' => 'autoplay',
					'callback' => 'checkbox',
					'label' => __( 'Enable Autoplay', $text_domain ),
					'default' => false,
					'shortcode' => array(
						'example' => 'true',
						'description' => __( 'Whether or not to change slides automatically on an interval.', $text_domain ),
					)
				),
				array(
					'name' => 'time',
					'callback' => 'text',
					'label' => __( 'Interval Time', $text_domain ),
					'default' => 5000,
					'shortcode' => array(
						'example' => '3000',
						'description' => __( 'Duration in milliseconds before the slide changes.', $text_domain ),
					)
				),
			)
		),

	);
	$admin->set_feed_layouts( $layouts );

	$display_your_feed_table_headings = array(

		array(
			'slug' => 'layout',
			'label' => __( 'Display Options', SBSW_TEXT_DOMAIN ),
		),
		array(
			'slug' => 'text_date',
			'label' => __( 'Text and Date', SBSW_TEXT_DOMAIN ),
		),
		array(
			'slug' => 'button',
			'label' => __( '"Load More" Button Options', SBSW_TEXT_DOMAIN ),
		),
		array(
			'slug' => 'customize',
			'label' => __( 'Moderation Options', SBSW_TEXT_DOMAIN ),
		),
	);
	$admin->set_display_table_sections( $display_your_feed_table_headings );

	$admin->init();
	add_action( 'admin_notices',  'sbsw_cff_admin_notice' );

}

function sbsw_cff_admin_notice(){
	if( defined( 'CFFVER' ) && version_compare( CFFVER, '3.18', '<' ) ){
		?>
		<div class="notice notice-error">
			<div>
				<p>
					<strong><?php echo esc_html__('Important:','social-wall') ?> </strong> <?php echo __('An update to the <strong>Custom Facebook Feed</strong> plugin is required to be compatible with the latest version of the <strong>Social Wall</strong> plugin. Please update the plugin on the WordPress  ','social-wall') ?><a href="<?php echo esc_url( admin_url( '/plugins.php' ) ) ?>"><?php echo esc_html__('Plugins page','social-wall') ?></a>.
				</p>
			</div>
		</div>
		<?php
	}
}

function sbsw_admin_style() {
	wp_enqueue_style( SBSW_SLUG . '_admin_notices_css', SBSW_PLUGIN_URL . 'css/sbsw-notices.css', array(), SWVER );
	if ( ! sbsw_is_admin_page() ) {
		return;
	}
	wp_enqueue_style( SBSW_SLUG . '_admin_css', SBSW_PLUGIN_URL . 'css/admin.css', array(), SWVER );
	wp_enqueue_style( 'sb_font_awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'sbsw_admin_style' );

function sbsw_admin_scripts() {
	if ( ! sbsw_is_admin_page() ) {
		return;
	}
	wp_enqueue_script( SBSW_SLUG . '_admin_js', SBSW_PLUGIN_URL . 'js/admin.js', array(), SWVER );
	wp_localize_script( SBSW_SLUG . '_admin_js', 'sbspf', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'sbspf_nonce' ),
			'add_text' => __( 'Add', 'social-wall' ),
			'remove_text' => __( 'Remove', 'social-wall' ),
		)
	);
	wp_enqueue_script('wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'sbsw_admin_scripts' );

function sbsw_is_admin_page() {
	if ( ! isset( $_GET['page'] ) ) {
		return false;
	} elseif ( strpos( sanitize_text_field( $_GET['page'] ), SBSW_SLUG ) !== false ) {
		return true;
	}
	return false;
}

function sbsw_admin_icon( $icon, $class = '' ) {
	$class = ! empty( $class ) ? ' ' . $class : '';
	if ( $icon === 'question-circle' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-question-circle fa-w-16'.$class.'"><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z" class=""></path></svg>';
	} elseif ( $icon === 'info-circle' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-info-circle fa-w-16'.$class.'"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" class=""></path></svg>';
	} elseif ( $icon === 'life-ring' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="life-ring" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-life-ring fa-w-16'.$class.'"><path fill="currentColor" d="M256 504c136.967 0 248-111.033 248-248S392.967 8 256 8 8 119.033 8 256s111.033 248 248 248zm-103.398-76.72l53.411-53.411c31.806 13.506 68.128 13.522 99.974 0l53.411 53.411c-63.217 38.319-143.579 38.319-206.796 0zM336 256c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80zm91.28 103.398l-53.411-53.411c13.505-31.806 13.522-68.128 0-99.974l53.411-53.411c38.319 63.217 38.319 143.579 0 206.796zM359.397 84.72l-53.411 53.411c-31.806-13.505-68.128-13.522-99.973 0L152.602 84.72c63.217-38.319 143.579-38.319 206.795 0zM84.72 152.602l53.411 53.411c-13.506 31.806-13.522 68.128 0 99.974L84.72 359.398c-38.319-63.217-38.319-143.579 0-206.796z" class=""></path></svg>';
	} elseif ( $icon === 'envelope' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-envelope fa-w-16'.$class.'"><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z" class=""></path></svg>';
	} elseif ( $icon === 'chevron-right' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-circle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-chevron-circle-right fa-w-16'.$class.'"><path fill="currentColor" d="M256 8c137 0 248 111 248 248S393 504 256 504 8 393 8 256 119 8 256 8zm113.9 231L234.4 103.5c-9.4-9.4-24.6-9.4-33.9 0l-17 17c-9.4 9.4-9.4 24.6 0 33.9L285.1 256 183.5 357.6c-9.4 9.4-9.4 24.6 0 33.9l17 17c9.4 9.4 24.6 9.4 33.9 0L369.9 273c9.4-9.4 9.4-24.6 0-34z" class=""></path></svg>';
	} elseif ( $icon === 'rocket' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="rocket" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-rocket fa-w-16'.$class.'"><path fill="currentColor" d="M505.05 19.1a15.89 15.89 0 0 0-12.2-12.2C460.65 0 435.46 0 410.36 0c-103.2 0-165.1 55.2-211.29 128H94.87A48 48 0 0 0 52 154.49l-49.42 98.8A24 24 0 0 0 24.07 288h103.77l-22.47 22.47a32 32 0 0 0 0 45.25l50.9 50.91a32 32 0 0 0 45.26 0L224 384.16V488a24 24 0 0 0 34.7 21.49l98.7-49.39a47.91 47.91 0 0 0 26.5-42.9V312.79c72.59-46.3 128-108.4 128-211.09.1-25.2.1-50.4-6.85-82.6zM384 168a40 40 0 1 1 40-40 40 40 0 0 1-40 40z" class=""></path></svg>';
	} elseif ( $icon === 'minus-circle' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="minus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-minus-circle fa-w-16'.$class.'"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z" class=""></path></svg>';
	} elseif ( $icon === 'times' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" class="svg-inline--fa fa-times fa-w-11'.$class.'"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z" class=""></path></svg>';
	} elseif ( $icon === 'cog' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-cog fa-w-16'.$class.'"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z" class=""></path></svg>';
	} elseif ( $icon === 'ellipsis' ) {
		return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="ellipsis-h" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-ellipsis-h fa-w-16'.$class.'"><path fill="currentColor" d="M328 256c0 39.8-32.2 72-72 72s-72-32.2-72-72 32.2-72 72-72 72 32.2 72 72zm104-72c-39.8 0-72 32.2-72 72s32.2 72 72 72 72-32.2 72-72-32.2-72-72-72zm-352 0c-39.8 0-72 32.2-72 72s32.2 72 72 72 72-32.2 72-72-32.2-72-72-72z" class=""></path></svg>';
	} else {
		sbsw_icon( $icon );
	}
}

function sbsw_reset_cron( $settings ) {
	$sbsw_cache_cron_interval = isset( $settings['cache_cron_interval'] ) ? $settings['cache_cron_interval'] : '';
	$sbsw_cache_cron_time = isset( $settings['cache_cron_time'] ) ? $settings['cache_cron_time'] : '';
	$sbsw_cache_cron_am_pm = isset( $settings['cache_cron_am_pm'] ) ? $settings['cache_cron_am_pm'] : '';

	delete_option( 'sbsw_cron_report' );
	SW_Cron_Updater::start_cron_job( $sbsw_cache_cron_interval, $sbsw_cache_cron_time, $sbsw_cache_cron_am_pm );
}
add_action( 'sbsw_settings_after_customize_save', 'sbsw_reset_cron', 10, 1 );

function sbsw_register_option() {
	// creates our settings in the options table
	register_setting('sbsw_license', 'sbsw_license_key', 'sbsw_sanitize_license' );
}
add_action('admin_init', 'sbsw_register_option');

function sbsw_sanitize_license( $new ) {
	$old = get_option( 'sbsw_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'sbsw_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

function sbsw_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['sbsw_license_activate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'sbsw_nonce', 'sbsw_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$sbsw_license = trim( get_option( 'sbsw_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license'   => $sbsw_license,
			'item_name' => urlencode( SBSW_PLUGIN_EDD_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, SBSW_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$sbsw_license_data = json_decode( wp_remote_retrieve_body( $response ) );

		//store the license data in an option
		update_option( 'sbsw_license_data', $sbsw_license_data );

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'sbsw_license_status', $sbsw_license_data->license );

	}
}
add_action('admin_init', 'sbsw_activate_license');

function sbsw_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['sbsw_license_deactivate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'sbsw_nonce', 'sbsw_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$sbsw_license= trim( get_option( 'sbsw_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license'   => $sbsw_license,
			'item_name' => urlencode( SBSW_PLUGIN_EDD_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, SBSW_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$sbsw_license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $sbsw_license_data->license == 'deactivated' )
			delete_option( 'sbsw_license_status' );

	}
}
add_action('admin_init', 'sbsw_deactivate_license');

function sbsw_connect_accounts() {
	$capability = current_user_can( 'manage_options' ) ? 'manage_options' : 'manage_social_wall_feed_options';

	if ( ! current_user_can( $capability ) ) {
		die();
	}

	if ( ! isset( $_POST['sbsw_connected_accounts'] ) ) {
		die();
	}

	$new_connected = $_POST['sbsw_connected_accounts'];

	if ( version_compare( CFFVER, '4.0', '>=' ) ) {
		$return = '';
	    if ( isset( $_POST['selected'] ) ) {
	        foreach ( $_POST['selected'] as $selected ) {
	            if ( isset( $new_connected[ $selected ] ) ) {
		            $source_data = array(
			            'access_token' => sanitize_text_field( $new_connected[ $selected ]['accesstoken'] ),
			            'id'           => sanitize_text_field( $new_connected[ $selected ]['id'] ),
			            'type'         => sanitize_text_field( $new_connected[ $selected ]['pagetype'] ),
			            'privilege'    => '',
		            );

		            if ( ! empty( $new_connected[ $selected ]['name'] ) ) {
			            $source_data['name'] = sanitize_text_field( $new_connected[ $selected ]['name'] );
		            }

		            $return = \CustomFacebookFeed\Builder\CFF_Source::process_connecting_source_data( $source_data );
                }
            }
        }

		echo $return;
    } else {
		$cff_connected_accounts = get_option('cff_connected_accounts', '{}');

		$current_connected = json_decode( str_replace(array('\"'),array('"'), $cff_connected_accounts) );

		foreach ( $new_connected as $new_connected_account ) {
			if ( ! isset( $current_connected->{$new_connected_account['id']} ) ) {
				$current_connected->{$new_connected_account['id']} = new stdClass();
				foreach ( $new_connected_account as $key => $value ) {
					$current_connected->{$new_connected_account['id']}->{$key} = sanitize_text_field( $value );
				}
			}
		}


		$encoded = sbsw_json_encode( $current_connected );


		update_option( 'cff_connected_accounts', $encoded );
    }

	die();
}
add_action( 'wp_ajax_sbsw_connect_accounts', 'sbsw_connect_accounts' );

/**
 * Remove non-WPForms notices from WPForms pages.
 *
 * @since 1.3.9
 */
function sbsw_admin_hide_unrelated_notices() {

	// Bail if we're not on a Sby screen or page.
	if ( ! sbsw_is_admin_page() ) {
		return;
	}

	// Extra banned classes and callbacks from third-party plugins.
	$blacklist = array(
		'classes'   => array(),
		'callbacks' => array(
			'sbydb_admin_notice', // 'Database for Sby' plugin.
		),
	);

	global $wp_filter;

	foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
		if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
			continue;
		}
		foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
					continue;
				}
				$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
				if (
					! empty( $class ) &&
					strpos( $class, 'sby' ) !== false &&
					! in_array( $class, $blacklist['classes'], true )
				) {
					continue;
				}
				if (
					! empty( $name ) && (
						strpos( $name, 'sby' ) === false ||
						in_array( $class, $blacklist['classes'], true ) ||
						in_array( $name, $blacklist['callbacks'], true )
					)
				) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}
add_action( 'admin_print_scripts', 'sbsw_admin_hide_unrelated_notices' );
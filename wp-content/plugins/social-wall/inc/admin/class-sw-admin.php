<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SW_Admin {

	protected $vars;

	protected $base_path;

	protected $slug;

	protected $plugin_name;

	protected $capability;

	protected $tabs;

	protected $active_tab;

	protected $settings_sections;

	protected $display_your_feed_sections;

	protected $option_name;

	protected $types;

	protected $layouts;

	protected $false_fields;

	protected $textarea_fields;

	public function __construct( $vars, $base_path, $slug, $plugin_name, $capability, $icon, $position, $tabs, $settings, $active_tab = false, $option_name = 'sbsw_settings' ) {
		$this->vars = $vars;
		$this->base_path = $base_path;
		$this->slug = $slug;
		$this->plugin_name = $plugin_name;
		$this->capability = $capability;
		$this->icon = $icon;
		$this->position = $position;

		$this->tabs = $tabs;

		if ( $active_tab ) {
			$this->set_active_tab( $active_tab );
		} else {
			$this->set_active_tab( $tabs[0]['slug'] );
		}
		$this->settings = $settings;
		$this->option_name = $option_name;
		$this->false_fields = array();
		$this->textarea_fields = array();
		$this->display_your_feed_sections = array();

		$this->settings_sections = array();
	}

	public function get_vars() {
		return $this->vars;
	}

	public function get_option_name() {
		return $this->option_name;
	}

	public function verify_post( $post ) {
		return wp_verify_nonce( $post[ $this->option_name . '_validate' ], $this->option_name . '_validate' );
	}

	public function hidden_fields_for_tab( $tab ) {
		wp_nonce_field( $this->get_option_name() . '_validate', $this->get_option_name() . '_validate', true, true );
		?>
        <input type="hidden" name="<?php echo $this->get_option_name() . '_tab_marker'; ?>" value="<?php echo esc_attr( $tab ); ?>"/>
		<?php
	}

	public function init() {
		add_action( 'admin_menu', array( $this, 'create_menus' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'additional_settings_init' ) );
		add_action( 'admin_head', array( $this, 'sbsw_hide_yt_menu' ) );
		add_action( 'admin_footer', array( $this, 'sbsw_add_admin_js' ) );
	}

	//If using an individual plugin then open the SW menu
	public function sbsw_add_admin_js(){

		//Show menu tooltip once only
		$sbsw_seen_menu_tooltip = get_option('sbsw_seen_menu_tooltip');

		echo "<script type='text/javascript'>
        jQuery(document).ready( function($) {
        	//Open SW menu when on individual plugins
        	var on_cff_settings = jQuery('#cff-builder-app').length || jQuery('#cff-settings').length || jQuery('#cff-extensions').length || jQuery('#cff-oembeds').length || jQuery('#cff-about').length || jQuery('#cff-support').length,
        		on_sbi_settings = jQuery('#sbi_admin.wrap').length,
        		on_ctf_settings = jQuery('#ctf-admin.wrap').length,
        		on_yt_settings = jQuery('#sbspf_admin.wrap h1:contains(\'Feeds for YouTube\')').length;

        	if( on_cff_settings || on_sbi_settings || on_ctf_settings || on_yt_settings ){
        		jQuery('#toplevel_page_sbsw, #toplevel_page_sbsw > a.wp-has-submenu').addClass('wp-has-current-submenu wp-menu-open');

        		var sbsw_menu_sel = '';
        		if( on_cff_settings ){
	        		sbsw_menu_sel = '.sbsw_cff_menu';
	        	} else if( on_sbi_settings ){
	        		sbsw_menu_sel = '.sbsw_sbi_menu';
	        	} else if( on_ctf_settings ){
	        		sbsw_menu_sel = '.sbsw_ctf_menu';
	        	} else if( on_yt_settings ){
	        		sbsw_menu_sel = '.sbsw_yt_menu';
	        	}
	        	jQuery('#toplevel_page_sbsw '+sbsw_menu_sel).closest('li').addClass('current');
        	}

        	//SW plugin missing modal
        	jQuery('.toplevel_page_sbsw .sbsw_plugin_missing').parent().on('click', function(e){
        		e.preventDefault();
        		jQuery('.sbsw_missing_plugin_modal').remove();

        		var pluginName = jQuery(this).text(),
        			platformName = pluginName.split(' ')[0];

        		var sbsw_missing_html = '<div class=\"sbsw_missing_plugin_modal\">';
        		sbsw_missing_html += '<div class=\"sbsw_missing_inner\">';
        		sbsw_missing_html += '<h3>Add '+platformName+' Posts to Your Social Wall</h3>';

        		sbsw_missing_html += '<p>The '+pluginName+' plugin is not installed. If you have this plugin, click <a href=\"plugins.php\">here</a> to install it. Otherwise, use the button below to get it.</p>';
        		sbsw_missing_html += '<p><a href=\"'+jQuery(this).attr('href')+'\" target=\"_blank\" class=\"button button-primary\">Get '+pluginName+' Pro</a></p>';
        		sbsw_missing_html += '</div>';
        		sbsw_missing_html += '</div>';

				jQuery('body').append( sbsw_missing_html + '<style>.sbsw_missing_plugin_modal{position:fixed;z-index:999;width:100%;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.3)}.sbsw_missing_inner{position:absolute;top:140px;left:50%;width:480px;margin:0 0 0 -245px;padding:25px 35px;background:#fff;text-align:center;-webkit-box-shadow:0 1px 20px rgba(0,0,0,.2);box-shadow:0 1px 20px rgba(0,0,0,.2);-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px}</style>' );
        	});
        	//Close the modal if clicking anywhere outside it
        	jQuery('body').on('click', '.sbsw_missing_plugin_modal', function(e){
        		if (e.target !== this) return;
        		jQuery('.sbsw_missing_plugin_modal').remove();
        	});";

        if( !$sbsw_seen_menu_tooltip ){
        	//Add an initial direction tooltip for menu
        	echo "jQuery('a.toplevel_page_sbsw').prepend('<div class=\"sbsw-installed-pointer\">Your Smash Balloon Social Feeds are here<svg aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"caret-left\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 192 512\" class=\"svg-inline--fa fa-caret-left fa-w-6 fa-2x\"><path fill=\"currentColor\" d=\"M192 127.338v257.324c0 17.818-21.543 26.741-34.142 14.142L29.196 270.142c-7.81-7.81-7.81-20.474 0-28.284l128.662-128.662c12.599-12.6 34.142-3.676 34.142 14.142z\"></path></svg></div><style>.sbsw-installed-pointer{position:absolute;z-index:9;left:90%;padding:10px;background:#ca4a1f;color:#fff;width:100%;top:-10px;text-align:center;border-radius:3px;cursor:default;}.wp-menu-open .sbsw-installed-pointer{z-index:99999;} .sbsw-installed-pointer svg{ position: absolute; z-index: 99999; width: 12px; left: -10px; top: 50%; margin-top: -16px;}.sbsw-installed-pointer path{ fill: #ca4a1f;}</style>');";
        	echo "jQuery('body').on('mouseover', '.sbsw-installed-pointer', function(e){
        		e.preventDefault();
        		if (e.target !== this) return;
        		jQuery('.sbsw-installed-pointer').fadeOut();
        	});";
        }
        	
        echo "});     
        </script>";

        update_option('sbsw_seen_menu_tooltip', true);
	}

	public function settings_init() {
		$text_domain = $this->vars->text_domain();
		/**
		 * Configure Tab
		 */
		$args = array(
			'id' => 'sbspf_types',
			'tab' => 'configure',
			'save_after' => false
		);
		$this->add_settings_section( $args );

		/**
		 * Customize Tab
		 */

		$args = array(
			'title' => __( 'Posts', $text_domain ),
			'id' => 'sbspf_posts',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'title' => __( 'Display', $text_domain ),
			'id' => 'sbspf_layout',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'layout',
			'section' => 'sbspf_layout',
			'callback' => 'layout',
			'title' => __( 'Layout Type', $text_domain ),
			'layouts' => $this->layouts,
			'shortcode' => array(
				'key' => 'layout',
				'example' => 'list',
				'description' => __( 'How your posts are displayed visually.', $text_domain ),
				'display_section' => 'layout'
			)
		);
		$this->add_settings_field( $args );

		$this->add_false_field( 'carouselarrows', 'customize');
		$this->add_false_field( 'carouselpag', 'customize');
		$this->add_false_field( 'carouselautoplay', 'customize');
		$this->add_false_field( 'masonryshowfilter', 'customize');


	}

	public function additional_settings_init() {
		$text_domain = SBSW_TEXT_DOMAIN;

		$defaults = sbsw_settings_defaults();

		$args = array(
			'name' => 'num',
			'default' => $defaults['num'],
			'section' => 'sbspf_posts',
			'callback' => 'text',
			'min' => 1,
			'max' => 50,
			'size' => 4,
			'title' => __( 'Number of Posts', $text_domain ),
			'additional' => '<span class="sbsw_note">' . __( 'Number of posts to show initially.', $text_domain ) . '</span>',
			'shortcode' => array(
				'key' => 'num',
				'example' => 5,
				'description' => __( 'The number of posts in the feed', $text_domain ),
				'display_section' => 'layout'
			)
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => __( 'Light', $text_domain ),
				'value' => 'light'
			),
			array(
				'label' => __( 'Dark', $text_domain ),
				'value' => 'dark'
			),
		);
		$args = array(
			'name' => 'theme',
			'default' => 'light',
			'section' => 'sbspf_layout',
			'callback' => 'select',
			'title' => __( 'Color Scheme', $text_domain ),
			'shortcode' => array(
				'key' => 'theme',
				'example' => 'dark',
				'description' => __( 'Light or dark color scheme for the feed.', $text_domain ) . ' light, dark',
				'display_section' => 'layout'
			),
			'options' => $select_options,
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => 'px',
				'value' => 'px'
			),
			array(
				'label' => '%',
				'value' => '%'
			)
		);
		$args = array(
			'name' => 'itemspacing',
			'default' => $defaults['itemspacing'],
			'section' => 'sbspf_layout',
			'callback' => 'text',
			'min' => 0,
			'size' => 4,
			'title' => __( 'Spacing Between Posts', $text_domain ),
			'shortcode' => array(
				'key' => 'itemspacing',
				'example' => '5px',
				'description' => __( 'The spacing/padding around the posts in the feed. Any number with a unit like "px" or "em".', $text_domain ),
				'display_section' => 'layout'
			),
			'select_name' => 'itemspacingunit',
			'select_options' => $select_options,
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'background',
			'default' => '',
			'section' => 'sbspf_layout',
			'callback' => 'color',
			'title' => __( 'Post Background Color', $text_domain ),
			'shortcode' => array(
				'key' => 'background',
				'example' => '#f00',
				'description' => __( 'Background color for the feed. Any hex color code.', $text_domain ),
				'display_section' => 'layout'
			),
		);
		$this->add_settings_field( $args );

		/* Cache */
		$args = array(
			'name' => 'cache',
			'section' => 'sbspf_posts',
			'callback' => 'cache',
			'title' => __( 'Check for new posts', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'textlength',
			'default' => $defaults['textlength'],
			'section' => 'sbspf_text_date',
			'callback' => 'text',
			'min' => 5,
			'max' => 1500,
			'size' => 4,
			'title' => __( 'Text Length', $text_domain ),
			'shortcode' => array(
				'key' => 'textlength',
				'example' => 300,
				'description' => __( 'Maximum length of the text in the post', $text_domain ),
				'display_section' => 'text_date'
			)
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => __( 'inherit', $text_domain ),
				'value' => 'inherit'
			),
			array(
				'label' => __( '20px', $text_domain ),
				'value' => '20px'
			),
			array(
				'label' => __( '18px', $text_domain ),
				'value' => '18px'
			),
			array(
				'label' => __( '16px', $text_domain ),
				'value' => '16px'
			),
			array(
				'label' => __( '15px', $text_domain ),
				'value' => '15px'
			),
			array(
				'label' => __( '14px', $text_domain ),
				'value' => '14px'
			),
			array(
				'label' => __( '13px', $text_domain ),
				'value' => '13px'
			),
			array(
				'label' => __( '12px', $text_domain ),
				'value' => '12px'
			),
		);
		$args = array(
			'name' => 'contenttextsize',
			'default' => '16px',
			'section' => 'sbspf_text_date',
			'callback' => 'select',
			'title' => __( 'Content Text Size', $text_domain ),
			'shortcode' => array(
				'key' => 'contenttextsize',
				'example' => 'inherit',
				'description' => __( 'Size of content/caption text, size of other text will be relative to this size.', $text_domain ) . ' 13px, 14px, inherit',
				'display_section' => 'text_date'
			),
			'tooltip_info' => __( 'Size of content/caption  text, size of other text in the info display will be relative to this size.', $text_domain ),
			'options' => $select_options,
		);
		$this->add_settings_field( $args );
		$date_format_options = array(
			array(
				'label' => __( 'Relative', $text_domain ),
				'value' => 'relative'
			),
			array(
				'label' => __( 'Custom (Enter Below)', $text_domain ),
				'value' => 'custom'
			)
		);
		$date_text_options = array(
			array(
				'label' => __( 'm', $text_domain ),
				'key' => 'minutetext',
				'default' => 'm'
			),
			array(
				'label' => __( 'h', $text_domain ),
				'key' => 'hourtext',
				'default' => 'h'
			),
			array(
				'label' => __( 'd', $text_domain ),
				'key' => 'daytext',
				'default' => 'd'
			),
			array(
				'label' => __( 'w', $text_domain ),
				'key' => 'weektext',
				'default' => 'w'
			),
			array(
				'label' => __( 'mo', $text_domain ),
				'key' => 'monthtext',
				'default' => 'mo'
			),
			array(
				'label' => __( 'y', $text_domain ),
				'key' => 'yeartext',
				'default' => 'y'
			),
		);
		$args = array(
			'name' => 'dateformat',
			'default' => '',
			'section' => 'sbspf_text_date',
			'date_formats' => $date_format_options,
			'text_settings' => $date_text_options,
			'callback' => 'date_format',
			'title' => __( 'Date Format', $text_domain ),
			'shortcode' => array(
				'key' => 'dateformat',
				'example' => 'false',
				'description' => __( 'Include a "Load More" button at the bottom of the feed to load more posts.', $text_domain ),
				'display_section' => 'text_date'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Text and Date', $text_domain ),
			'id' => 'sbspf_text_date',
			'tab' => 'customize',
		);
		$this->add_settings_section( $args );

		$args = array(
			'title' => __( '"Load More" Button', $text_domain ),
			'id' => 'sbspf_loadmore',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'showbutton',
			'section' => 'sbspf_loadmore',
			'callback' => 'checkbox',
			'title' => __( 'Show "Load More" Button', $text_domain ),
			'default' => true,
			'shortcode' => array(
				'key' => 'showbutton',
				'example' => 'false',
				'description' => __( 'Include a "Load More" button at the bottom of the feed to load more posts.', $text_domain ),
				'display_section' => 'button'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'buttontext',
			'default' => __( 'Load More...', $text_domain ),
			'section' => 'sbspf_loadmore',
			'callback' => 'text',
			'title' => __( 'Button Text', $text_domain ),
			'shortcode' => array(
				'key' => 'buttontext',
				'example' => '"More Posts"',
				'description' => __( 'The text that appears on the "Load More" button.', $text_domain ),
				'display_section' => 'button'
			)
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Moderation', $text_domain ),
			'id' => 'sbspf_moderation',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'includewords',
			'default' => '',
			'section' => 'sbspf_moderation',
			'callback' => 'text',
			'class' => 'large-text',
			'title' => __( 'Show posts containing these words or hashtags', $text_domain ),
			'shortcode' => array(
				'key' => 'includewords',
				'example' => '#filter',
				'description' => __( 'Show posts that have specific text in the content/caption.', $text_domain ),
				'display_section' => 'customize'
			),
			'additional' => __( '"includewords" separate multiple words with commas, include "#" for hashtags', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'excludewords',
			'default' => '',
			'section' => 'sbspf_moderation',
			'callback' => 'text',
			'class' => 'large-text',
			'title' => __( 'Remove posts containing these words or hashtags', $text_domain ),
			'shortcode' => array(
				'key' => 'excludewords',
				'example' => '#filter',
				'description' => __( 'Remove posts that have specific text in the title or description.', $text_domain ),
				'display_section' => 'customize'
			),
			'additional' => __( '"excludewords" separate multiple words with commas, include "#" for hashtags', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Custom Code Snippets', $text_domain ),
			'id' => 'sbspf_custom_snippets',
			'tab' => 'customize'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'custom_css',
			'default' => '',
			'section' => 'sbspf_custom_snippets',
			'callback' => 'textarea',
			'title' => __( 'Custom CSS', $text_domain ),
			'options' => $select_options,
			'tooltip_info' => __( 'Enter your own custom CSS in the box below', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'custom_js',
			'default' => '',
			'section' => 'sbspf_custom_snippets',
			'callback' => 'textarea',
			'title' => __( 'Custom JavaScript', $text_domain ),
			'options' => $select_options,
			'tooltip_info' => __( 'Enter your own custom JavaScript/jQuery in the box below', $text_domain ),
			'note' => __( 'Note: Custom JavaScript reruns every time more posts are loaded into the feed', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Advanced', $text_domain ),
			'id' => 'sbspf_advanced',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'preserve_settings',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Preserve settings when plugin is removed', $text_domain ),
			'default' => false,
			'tooltip_info' => __( 'When removing the plugin your settings are automatically erased. Checking this box will prevent any settings from being deleted. This means that you can uninstall and reinstall the plugin without losing your settings.', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'ajaxtheme',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Are you using an AJAX theme?', $text_domain ),
			'default' => false,
			'tooltip_info' => __( 'When navigating your site, if your theme uses Ajax to load content into your pages (meaning your page doesn\'t refresh) then check this setting. If you\'re not sure then it\'s best to leave this setting unchecked while checking with your theme author, otherwise checking it may cause a problem.', $text_domain )
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'ajax_post_load',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Load initial posts with AJAX', $text_domain ),
			'default' => false,
			'tooltip_info' => __( 'Initial posts will be loaded using AJAX instead of added to the page directly. If you use page caching, this will allow the feed to update according to the "Check for new posts every" setting on the "Configure" tab.', $text_domain )
		);
		//$this->add_settings_field( $args );

		$args = array(
			'name' => 'customtemplates',
			'section' => 'sbspf_advanced',
			'callback' => 'checkbox',
			'title' => __( 'Enable Custom Templates', $text_domain ),
			'default' => false,
			'tooltip_info' => __( 'The default HTML for the feed can be replaced with custom templates added to your theme\'s folder. Enable this setting to use these templates. See <a href="https://smashballoon.com/social-wall-custom-templates/" target="_blank">this guide</a>', $text_domain )
		);
		$this->add_settings_field( $args );
	}

	public function add_false_field( $name, $tab ) {
		$this->false_fields[ $tab ][] = $name;
	}

	public function get_false_fields( $tab ) {
		if ( isset( $this->false_fields[ $tab ] ) ) {
			return $this->false_fields[ $tab ];
		}

		return array();
	}

	public function add_textarea_field( $name, $tab ) {
		$this->textarea_fields[ $tab ][] = $name;
	}

	public function get_textarea_fields( $tab ) {
		if ( isset( $this->textarea_fields[ $tab ] ) ) {
			return $this->textarea_fields[ $tab ];
		}

		return array();
	}

	public function blank() {

	}

	public function instructions( $args ) {
		?>
        <div class="sbspf_instructions_wrap">
			<?php echo $args['instructions']?>
        </div>
		<?php
	}

	public function add_settings_section( $args ) {
		$title = isset( $args['title'] ) ? $args['title'] : '';
		$callback = isset( $args['callback'] ) ? $args['callback'] : array( $this, 'blank' );
		$id = $this->slug . '_' . $args['id'];
		add_settings_section(
			$id,
			$title,
			$callback,
			$id
		);

		$save_after = isset( $args['save_after'] ) ? $args['save_after'] : false;
		$this->settings_sections[ $args['tab'] ][] = array(
			'id' => $id,
			'save_after' => $save_after
		);
	}

	public function add_settings_field( $args ) {
		$title_after = '';
		$shortcode = false;
		if ( isset( $args['shortcode'] ) ) {
			$title_after = isset( $args['shortcode']['after'] ) ? $args['shortcode']['after'] : '';
			$shortcode = $args['shortcode'];
		}

		if ( $shortcode ) {
			$this->display_your_feed_sections[ $shortcode['display_section'] ]['settings'][] = $shortcode;
		}

		$title = $this->format_title( $args['title'], $args['name'], $shortcode, $title_after );

		if ( $args['callback'] === 'checkbox' || (isset( $args['falsefield'] ) && $args['falsefield'] === true) ) {
			$tab = 'none';
			foreach ( $this->settings_sections as $key => $settings_sections ) {
				foreach ( $settings_sections as $this_tab_sections ) {
					if ( $this_tab_sections['id'] === $args['section'] ) {
						$tab = $key;
					}
				}

			}
			$this->add_false_field( $args['name'], $tab );
		}

		if ( $args['callback'] === 'layout' || $args['callback'] === 'sub_option' ) {
			$tab = 'none';
			foreach ( $this->settings_sections as $key => $settings_sections ) {
				foreach ( $settings_sections as $this_tab_sections ) {
					if ( $this_tab_sections['id'] === $args['section'] ) {
						$tab = $key;
					}
				}

			}
			$sub_options = isset( $args['layouts'] ) ? $args['layouts'] : $args['sub_options'];
			foreach ( $sub_options as $sub_option ) {
				if ( isset( $sub_option['options'] ) ) {
					foreach( $sub_option['options'] as $sub_sub_option ) {
						if ( ! empty( $sub_sub_option['shortcode'] ) ) {
							$key = ! empty( $sub_sub_option['shortcode']['key'] ) ? $sub_sub_option['shortcode']['key'] : $sub_option['slug'] . $sub_sub_option['name'];
							$example = ! empty( $sub_sub_option['shortcode']['example'] ) ? $sub_sub_option['shortcode']['example'] : '';
							$description = ! empty( $sub_sub_option['shortcode']['description'] ) ? $sub_sub_option['shortcode']['description'] : '';
							$display_section = ! empty( $sub_sub_option['shortcode']['display_section'] ) ? $sub_sub_option['shortcode']['display_section'] : str_replace( 'sbspf_', '', $args['section'] );
							$sub_shortcode = array(
								'key' => $key,
								'example' => $example,
								'description' => $description,
								'display_section' => $display_section
							);
							if ( isset( $this->display_your_feed_sections[ $display_section ] ) ) {
								$this->display_your_feed_sections[ $display_section ]['settings'][] = $sub_shortcode;
							}
						}
						if ( $sub_sub_option['callback'] === 'checkbox' ) {
							$this->add_false_field( $sub_option['slug'] . $sub_sub_option['name'], $tab );
						}
					}
				}
			}
		}

		if ( $args['callback'] === 'textarea' ) {
			$tab = 'none';
			foreach ( $this->settings_sections as $key => $settings_sections ) {
				foreach ( $settings_sections as $this_tab_sections ) {
					if ( $this_tab_sections['id'] === $args['section'] ) {
						$tab = $key;
					}
				}

			}
			$this->add_textarea_field( $args['name'], $tab );
		}
		$section = $this->slug . '_' . $args['section'];

		add_settings_field(
			$args['name'],
			$title,
			array( $this, $args['callback'] ),
			$section,
			$section,
			$args
		);

		if ( isset( $args['hidden'] ) ) {
			if ( $args['hidden']['callback'] === 'checkbox' ) {
				$tab = 'none';
				foreach ( $this->settings_sections as $key => $settings_sections ) {
					foreach ( $settings_sections as $this_tab_sections ) {
						if ( $this_tab_sections['id'] === $args['section'] ) {
							$tab = $key;
						}
					}

				}
				$this->add_false_field( $args['hidden']['name'], $tab );
			}
		}
	}

	public function set_feed_types( $types ) {
		$this->types = $types;
	}

	public function set_feed_layouts( $layouts ) {
		$this->layouts = $layouts;
	}

	public function set_display_table_sections( $headings ) {
		foreach ( $headings as $heading ) {
			$this->display_your_feed_sections[ $heading['slug'] ] = array(
				'label' => $heading['label'],
				'settings' => array()
			);
		}
	}

	public function checkbox( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : false;
		$selected = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;
		$label = isset( $args['label'] ) ? $args['label'] : __( 'Yes' );
		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();
		$has_shortcode = isset( $args['has_shortcode'] ) && $args['has_shortcode'] ? '1' : '';
		?>
        <input name="<?php echo $this->option_name .'['.esc_attr( $args['name'] ).']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>" class="sbspf_single_checkbox" type="checkbox"<?php if ( $selected ) echo ' checked'; ?>/>
        <label for="<?php echo $this->option_name . '_' . $args['name'] . $has_shortcode; ?>"><?php echo esc_html( $label ); ?></label><?php if ( $has_shortcode === '1' ) : ?><code class="sbspf_shortcode"> <?php echo $args['name'] . "\n"; ?>
            Eg: <?php echo $args['name']; ?>=<?php echo $args['shortcode_example']; ?></code><br><?php endif; ?>
		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
            <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
            <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php
		endif;
	}

	public function multi_checkbox( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : false;
		$selection_array = isset( $this->settings[ $args['name'] ] ) ? (array)$this->settings[ $args['name'] ] : (array)$default;
		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();
		$index = 0;
		?>
		<?php foreach ( $args['select_options'] as $select_option ) :
			$selected = in_array( $select_option['value'], $selection_array, true );
			$pro_only = (isset( $select_option['pro'] ) && $select_option['pro']) ? ' sbspf_pro_only' : '';
			$class = ! empty( $select_option['class'] ) ? ' ' . $select_option['class'] : '';
			?>
            <div class="sbspf_multi_checkbox_option<?php echo $pro_only . $class; ?>">
                <input name="<?php echo $this->option_name .'['.esc_attr( $args['name'] ).'][]'; ?>" id="<?php echo $this->option_name . '_' . $args['name']. '_' . $index; ?>" value="<?php echo esc_attr( $select_option['value'] ); ?>" type="checkbox"<?php if ( $selected ) echo ' checked'; ?>/>
                <label for="<?php echo $this->option_name . '_' . $args['name'] . '_' . $index; ?>"><?php echo esc_html( $select_option['label'] ); ?></label>
            </div>
			<?php
			$index++;
		endforeach; ?>

		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
            <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
            <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php
		endif;
	}

	public function text( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;
		$size = ( isset( $args['size'] ) ) ? ' size="'. $args['size'].'"' : '';
		$class = isset( $args['class'] ) ? ' class="'. esc_attr( $args['class'] ) . '"' : '';

		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();

		if ( isset( $args['min'] ) ) :
			$min = ( isset( $args['min'] ) ) ? ' min="'. $args['min'].'"' : '';
			$max = ( isset( $args['max'] ) ) ? ' max="'. $args['max'].'"' : '';
			$step = ( isset( $args['step'] ) ) ? ' step="'. $args['step'].'"' : '';
			$class = isset( $args['class'] ) ? ' class="sbspf_number_field sbspf_size_' . $args['size'] . ' '. esc_attr( $args['class'] ) . '"' : ' class="sbspf_number_field sbspf_size_' . $args['size'] . '"';
			?>
            <input name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>"<?php echo $class; ?> type="number"<?php echo $size; ?><?php echo $min; ?><?php echo $max; ?><?php echo $step; ?> value="<?php echo esc_attr( $value ); ?>" />
		<?php elseif ( isset( $args['color'] ) ) : ?>
            <input name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>" class="sbspf_colorpicker" type="text" value="#<?php echo esc_attr( str_replace('#', '', $value ) ); ?>" />
		<?php else: ?>
            <input name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>"<?php echo $class; ?> type="text" value="<?php echo esc_attr( stripslashes( $value ) ); ?>" />
		<?php endif; ?>

		<?php if ( isset( $args['select_options'] ) ) :
			$value = isset( $this->settings[ $args['select_name'] ] ) ? $this->settings[ $args['select_name'] ] : $args['select_options'][0]['value'];
			?>
            <select name="<?php echo $this->option_name.'['.$args['select_name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['select_name']; ?>">
				<?php foreach ( $args['select_options'] as $select_option ) : ?>
                    <option value="<?php echo esc_attr( $select_option['value'] ); ?>"<?php if ( (string)$select_option['value'] === (string)$value ) echo ' selected'; ?>><?php echo esc_html( $select_option['label'] ); ?></option>
				<?php endforeach; ?>
            </select>
		<?php endif; ?>

		<?php if ( isset( $args['hidden'] ) ) : ?>

			<?php
			if ( is_callable( array( $this, $args['hidden']['callback'] ) ) ){
				echo $args['hidden']['before'];
				call_user_func_array(
					array( $this, $args['hidden']['callback'] ),
					array( $args['hidden'] )
				);
				echo $args['hidden']['after'];
			}
			?>
		<?php endif; ?>

		<?php if ( isset( $args['additional'] ) ) : ?>
			<?php echo $args['additional']; ?>
		<?php endif; ?>

		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
            <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
            <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php
		endif;

		if ( false/*$args['name'] === 'num'*/ ) :
        ?>
        <div style="margin: 8px 0 0 1px; font-size: 12px;" class="cff-load-more-setting">
            <input type="checkbox" name="cff_show_num_mobile" id="cff_show_num_mobile">&nbsp;<label for="cff_show_num_mobile">Show different number for mobile</label>
            <div class="cff-mobile-col-settings" style="">
                <div class="cff-row">
                    <label title="Click for shortcode option">Mobile Number:</label><code class="cff_shortcode"> nummobile
                        Eg: nummobile=4</code>
                    <input type="text" name="cff_num_mobile" id="cff_num_mobile" size="4" value="">
                    <i style="color: #666; font-size: 11px;">Leave blank for default</i>
                </div>
            </div>
        </div>
            <?php
        endif;
	}

	public function select( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : $args['options'][0]['value'];
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;

		if ( isset( $args['min'] ) && isset( $args['max'] ) && ((int)$args['min'] < (int)$args['max']) && empty( $args['options'] ) ) {
			$args['options'] = array();
			$i = (int)$args['min'];

			while ( $i <= (int)$args['max'] ) {
				$args['options'][] = array(
					'label' => $i,
					'value' => $i
				);
				$i++;
			}
		}

		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();
		?>
        <select name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>">
			<?php foreach ( $args['options'] as $select_option ) : ?>
                <option value="<?php echo esc_attr( $select_option['value'] ); ?>"<?php if ( (string)$select_option['value'] === (string)$value ) echo ' selected'; ?>><?php echo esc_html( $select_option['label'] ); ?></option>
			<?php endforeach; ?>
        </select>

		<?php if ( isset( $args['additional'] ) ) : ?>
			<?php echo $args['additional']; ?>
		<?php endif; ?>

		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
            <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
            <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php endif;
	}

	public function textarea( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$value = isset( $this->settings[ $args['name'] ] ) ? stripslashes( $this->settings[ $args['name'] ] ) : $default;

		if ( isset( $args['tooltip_info'] ) ) : ?>
            <span><?php echo $args['tooltip_info']; ?></span><br>
		<?php endif; ?>

        <textarea name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>"rows="7"><?php echo $value; ?></textarea>

		<?php if ( isset( $args['note'] ) ) : ?>
            <br><span class="sbspf_note"><?php echo $args['note']; ?></span>
		<?php endif;
	}

	public function color( $args ) {
		$args['color'] = true;
		$this->text( $args );
	}

	public function cache( $args ) {
		$social_network = 'Social Wall';
		$caching_time = isset( $this->settings['caching_time'] ) ? $this->settings['caching_time'] : 1;
		$cache_time_unit_selected = isset( $this->settings['cache_time_unit'] ) ? $this->settings['cache_time_unit'] : 'hours';
		$cache_cron_interval_selected = isset( $this->settings['cache_cron_interval'] ) ? $this->settings['cache_cron_interval'] : '';
		$cache_cron_time = isset( $this->settings['cache_cron_time'] ) ? $this->settings['cache_cron_time'] : '';
		$cache_cron_am_pm = isset( $this->settings['cache_cron_am_pm'] ) ? $this->settings['cache_cron_am_pm'] : '';
		?>
        <div class="sbspf_cache_settings_wrap">

            <div class="sbspf_row sbspf-caching-cron-options" style="display: block;">

                <select name="<?php echo $this->option_name.'[cache_cron_interval]'; ?>" id="sbspf_cache_cron_interval">
                    <option value="30mins"<?php if ( $cache_cron_interval_selected === '30mins' ) echo ' selected'?>><?php _e ( 'Every 30 minutes', $this->vars->text_domain() ); ?></option>
                    <option value="1hour"<?php if ( $cache_cron_interval_selected === '1hour' ) echo ' selected'?>><?php _e ( 'Every hour', $this->vars->text_domain() ); ?></option>
                    <option value="12hours"<?php if ( $cache_cron_interval_selected === '12hours' ) echo ' selected'?>><?php _e ( 'Every 12 hours', $this->vars->text_domain() ); ?></option>
                    <option value="24hours"<?php if ( $cache_cron_interval_selected === '24hours' ) echo ' selected'?>><?php _e ( 'Every 24 hours', $this->vars->text_domain() ); ?></option>
                </select>

                <div id="sbspf-caching-time-settings" style="">
					<?php _e ( 'at', $this->vars->text_domain() ); ?>
                    <select name="<?php echo $this->option_name.'[cache_cron_time]'; ?>" style="width: 80px">
                        <option value="1"<?php if ( (int)$cache_cron_time === 1 ) echo ' selected'?>>1:00</option>
                        <option value="2"<?php if ( (int)$cache_cron_time === 2 ) echo ' selected'?>>2:00</option>
                        <option value="3"<?php if ( (int)$cache_cron_time === 3 ) echo ' selected'?>>3:00</option>
                        <option value="4"<?php if ( (int)$cache_cron_time === 4 ) echo ' selected'?>>4:00</option>
                        <option value="5"<?php if ( (int)$cache_cron_time === 5 ) echo ' selected'?>>5:00</option>
                        <option value="6"<?php if ( (int)$cache_cron_time === 6 ) echo ' selected'?>>6:00</option>
                        <option value="7"<?php if ( (int)$cache_cron_time === 7 ) echo ' selected'?>>7:00</option>
                        <option value="8"<?php if ( (int)$cache_cron_time === 8 ) echo ' selected'?>>8:00</option>
                        <option value="9"<?php if ( (int)$cache_cron_time === 9 ) echo ' selected'?>>9:00</option>
                        <option value="10"<?php if ( (int)$cache_cron_time === 10 ) echo ' selected'?>>10:00</option>
                        <option value="11"<?php if ( (int)$cache_cron_time === 11 ) echo ' selected'?>>11:00</option>
                        <option value="0"<?php if ( (int)$cache_cron_time === 0 ) echo ' selected'?>>12:00</option>
                    </select>

                    <select name="<?php echo $this->option_name.'[cache_cron_am_pm]'; ?>" style="width: 60px">
                        <option value="am"<?php if ( $cache_cron_am_pm === 'am' ) echo ' selected'?>><?php _e ( 'AM', $this->vars->text_domain() ); ?></option>
                        <option value="pm"<?php if ( $cache_cron_am_pm === 'pm' ) echo ' selected'?>><?php _e ( 'PM', $this->vars->text_domain() ); ?></option>
                    </select>
                </div>

				<?php
				if ( wp_next_scheduled( 'sbsw_feed_update' ) ) {
					$time_format = get_option( 'time_format' );
					if ( ! $time_format ) {
						$time_format = 'g:i a';
					}
					//
					$schedule = wp_get_schedule( 'sbsw_feed_update' );
					if ( $schedule == '30mins' ) $schedule = __( 'every 30 minutes', $this->vars->text_domain() );
					if ( $schedule == 'twicedaily' ) $schedule = __( 'every 12 hours', $this->vars->text_domain() );
					$sbspf_next_cron_event = wp_next_scheduled( 'sbsw_feed_update' );
					echo '<p class="sbspf-caching-sched-notice"><span><b>' . __( 'Next check', $this->vars->text_domain() ) . ': ' . date( $time_format, $sbspf_next_cron_event + sbsw_get_utc_offset() ) . ' (' . str_replace( 'sw', '', $schedule ) . ')</b> - ' . __( 'Note: Saving the settings on this page will clear the cache and reset this schedule', $this->vars->text_domain() ) . '</span></p>';
				} else {
					echo '<p style="font-size: 11px; color: #666;">' . __( 'Nothing currently scheduled', $this->vars->text_domain() ) . '</p>';
				}
				?>
            </div>
        </div>
		<?php
	}

	public function layout( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : $args['layouts'][0]['slug'];
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;
		?>
        <div class="sbspf_layouts">
			<?php foreach ( $args['layouts'] as $layout ) : ?>
                <div class="sbspf_layout_cell">
                    <input class="sbspf_layout_type" id="sbspf_layout_type_<?php echo esc_attr( $layout['slug'] ); ?>" name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" type="radio" value="<?php echo esc_attr( $layout['slug'] ); ?>"<?php if ( $layout['slug'] === $value ) echo ' checked'?>><label for="sbspf_layout_type_<?php echo esc_attr( $layout['slug'] ); ?>"><span class="sbspf_label"><?php echo $layout['label']; ?></span><img src="<?php echo esc_url( $this->vars->plugin_url() . $layout['image'] ); ?>"></label>
                </div>
			<?php endforeach; ?>

            <div class="sbspf_layout_options_wrap">
				<?php foreach ( $args['layouts'] as $layout ) : ?>
                    <div class="sbspf_layout_settings sbspf_layout_type_<?php echo esc_attr( $layout['slug'] ); ?>">

                        <div class="sbspf_layout_setting">
							<?php echo sbsw_admin_icon( 'info-circle' ); ?>&nbsp;&nbsp;&nbsp;<span class="sbspf_note" style="margin-left: 0;"><?php echo $layout['note']; ?></span>
                        </div>
						<?php if ( ! empty( $layout['options'] ) ) : ?>
							<?php foreach ( $layout['options'] as $option ) :
								$option['name'] = $layout['slug'].$option['name'];
								?>
                                <div class="sbspf_layout_setting">
									<?php if ( $option['callback'] !== 'checkbox' ) : ?>
                                        <label title="<?php echo __( 'Click for shortcode option', $this->vars->text_domain() ); ?>"><?php echo $option['label']; ?></label><code class="sbspf_shortcode"> <?php echo $option['name'] . "\n"; ?>
                                            Eg: <?php echo $option['name']; ?>=<?php echo $option['shortcode']['example']; ?></code><br>
									<?php else:
										$option['shortcode_example'] = $option['shortcode']['example'];
										$option['has_shortcode'] = true;
									endif; ?>
									<?php call_user_func_array( array( $this, $option['callback'] ), array( $option ) ); ?>

                                </div>

							<?php endforeach; ?>
						<?php endif; ?>

                    </div>

				<?php endforeach; ?>
            </div>
        </div>
		<?php
	}

	public function format_title( $label, $name, $shortcode_args = false, $after = '' ) {
		$formatted_label = '<label for="' . $this->option_name . '_' . $name . '">' . $label .'</label>';
		if ( $shortcode_args ) {
			$formatted_label .= '<code class="sbspf_shortcode"> ' . $shortcode_args['key'] . "\n";
			$formatted_label .= 'Eg: ' . $shortcode_args['key'] . '=' . $shortcode_args['example'] . '</code><br>';
		}
		$formatted_label .= $after;

		return $formatted_label;
	}

	public function validate_options( $input, $tab ) {
		$updated_options = get_option( $this->option_name, array() );
		$false_if_empty_keys = $this->get_false_fields( $tab );
		$textarea_keys = $this->get_textarea_fields( $tab );

		foreach ( $false_if_empty_keys as $false_key ) {
			$updated_options[ $false_key ] = false;
		}

		foreach ( $input as $key => $val ) {
			if ( in_array( $key, $false_if_empty_keys ) ) {
				$updated_options[ $key ] = ($val === 'on');
			} elseif ( in_array( $key, $textarea_keys ) ) {
				$updated_options[ $key ] = sanitize_textarea_field( $val );
			} elseif ( is_array( $val ) ) {
				$updated_options[ $key ] = array();
				foreach ( $val as $key2 => $val2 ) {
					$updated_options[ $key ][ $key2 ] = sanitize_text_field( $val2 );
				}
			} else {
				$updated_options[ $key ] = sanitize_text_field( $val );
			}
		}

		if ( $tab === 'configure' ) {
			do_action( $this->option_name . '_after_configure_save', $updated_options );
		} elseif ( $tab === 'customize' ) {
			do_action( $this->option_name . '_after_customize_save', $updated_options );
		}

		return $updated_options;
	}


	public function update_options( $new_settings ) {
		update_option( $this->get_option_name(), $new_settings );
		$this->settings = $new_settings;
	}

	public function get_sections( $tab ) {
		if ( isset( $this->settings_sections[ $tab ] ) ) {
			return $this->settings_sections[ $tab ];
		}
		return array();
	}

	public function create_menus() {

		add_menu_page(
			'Social Feeds',
			'Social Feeds',
			$this->capability,
			$this->slug,
			array( $this, 'create_options_page' ),
			$this->icon,
			$this->position
		);

		$capability = current_user_can( 'manage_social_wall_options' ) ? 'manage_social_wall_options' : 'manage_options';

		//Change the menu links based on whether the plugin is installed or not
		$sbsw_sbi_menu_text = '<span class="sbsw_sbi_menu">Instagram Feed</span>';
		$sbsw_sbi_menu_link = 'sb-instagram-feed';
		if ( ! defined( 'SBIVER' ) ) {
			$sbsw_sbi_menu_text = '<span class="sbsw_plugin_missing">Instagram Feed</span>';
			$sbsw_sbi_menu_link = 'https://smashballoon.com/instagram-feed/';
		}
		$sbsw_cff_menu_text = '<span class="sbsw_cff_menu">Facebook Feed</span>';
		$sbsw_cff_menu_link = defined( 'CFFVER' ) && version_compare(CFFVER, 4.0, '<' ) ? admin_url( 'admin.php?page=cff-top' ) : admin_url( 'admin.php?page=cff-feed-builder' );
		if ( ! defined( 'CFFVER' ) ){
			$sbsw_cff_menu_text = '<span class="sbsw_plugin_missing">Facebook Feed</span>';
			$sbsw_cff_menu_link = 'https://smashballoon.com/custom-facebook-feed/';
		}
		$sbsw_ctf_menu_text = '<span class="sbsw_ctf_menu">Twitter Feed</span>';
		$sbsw_ctf_menu_link = 'custom-twitter-feeds';
		if ( ! defined( 'CTF_VERSION' ) ) {
			$sbsw_ctf_menu_text = '<span class="sbsw_plugin_missing">Twitter Feed</span>';
			$sbsw_ctf_menu_link = 'https://smashballoon.com/custom-twitter-feeds/';
		}
		$sbsw_yt_menu_text = '<span class="sbsw_yt_menu">YouTube Feed</span>';
		$sbsw_yt_menu_link = 'youtube-feed';
		if ( ! defined( 'SBYVER' ) ) {
			$sbsw_yt_menu_text = '<span class="sbsw_plugin_missing">YouTube Feed</span>';
			$sbsw_yt_menu_link = 'https://smashballoon.com/youtube-feed/';
		}

		add_submenu_page(
			'sbsw',
			'Create a Social Wall',
			'Create a Social Wall',
			$capability,
			$this->slug
		);
		add_submenu_page(
	        'sbsw',
	        'Instagram Feed',
	        $sbsw_sbi_menu_text,
	        $capability,
	        $sbsw_sbi_menu_link
	    );
		add_submenu_page(
	        'sbsw',
	        'Facebook Feed',
	        $sbsw_cff_menu_text,
	        $capability,
	        $sbsw_cff_menu_link
	    );
		add_submenu_page(
	        'sbsw',
	        'Twitter Feed',
	        $sbsw_ctf_menu_text,
	        $capability,
	        $sbsw_ctf_menu_link
	    );
		add_submenu_page(
	        'sbsw',
	        'YouTube Feed',
	        $sbsw_yt_menu_text,
	        $capability,
	        $sbsw_yt_menu_link
	    );

		// //Hide the other plugin menus
	    remove_menu_page( 'sb-instagram-feed' );
	    remove_menu_page( 'cff-top' );
	    remove_menu_page( 'custom-twitter-feeds' );
	    //YouTube menu hidden with CSS: sbsw_hide_yt_menu()

		$this->after_create_menues();
	}

	//Hide the YouTube plugin menu
	public function sbsw_hide_yt_menu(){
		echo '<style>';
		echo '#adminmenu li.menu-top.toplevel_page_youtube-feed{ display: none !important; }';
		echo 'ul#adminmenu .toplevel_page_sbsw a.wp-has-current-submenu:after, .toplevel_page_sbsw ul#adminmenu>li.current>a.current:after{ z-index: 10001; }';
		echo '</style>';
	}

	public function after_create_menues() {

	}

	public function set_active_tab( $active_tab ) {
		foreach ( $this->tabs as $tab ) {
			if ( $tab['slug'] === $active_tab ) {
				$this->active_tab = $tab['slug'];
			}
		}
	}

	public function get_tabs() {
		return $this->tabs;
	}

	public function get_active_tab() {
		return $this->active_tab;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_path( $view ) {
		return trailingslashit( $this->base_path ) . $view . '.php';
	}

	public function create_options_page() {
		require_once trailingslashit( $this->base_path ) . 'main.php';
	}

	public function next_step() {
		$return = array();
		$i = 0;
		foreach ( $this->tabs as $tab ) {
			if ( $this->active_tab === $tab['slug'] && isset( $tab['next_step_instructions'] ) ) {
				$next_tab_slug = isset( $this->tabs[ $i + 1 ]['slug'] ) ? $this->tabs[ $i + 1 ]['slug'] : $tab['slug'];
				$return = array(
					'instructions' => $tab['next_step_instructions'],
					'next_tab' => $next_tab_slug
				);
			}
			$i++;
		}
		return $return;
	}

	public function sub_option( $args ) {
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : 'related';

		$cta_options = $args['sub_options'];
		?>
		<?php if ( ! empty( $args['before'] ) ) {
			echo $args['before'];
		}?>

        <div class="sbspf_sub_options">
			<?php foreach ( $cta_options as $sub_option ) : ?>
                <div class="sbspf_sub_option_cell">
                    <input class="sbspf_sub_option_type" id="sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>" name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" type="radio" value="<?php echo esc_attr( $sub_option['slug'] ); ?>"<?php if ( $sub_option['slug'] === $value ) echo ' checked'?>><label for="sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>"><span class="sbspf_label"><?php echo $sub_option['label']; ?></span></label>
                </div>
			<?php endforeach; ?>

            <div class="sbspf_box_setting">
				<?php if ( isset( $cta_options ) ) : foreach ( $cta_options as $sub_option ) : ?>
                    <div class="sbspf_sub_option_settings sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>">

                        <div class="sbspf_sub_option_setting">
							<?php echo sbsw_admin_icon( 'info-circle', 'sbspf_small_svg' ); ?>&nbsp;&nbsp;&nbsp;<span class="sbspf_note" style="margin-left: 0;"><?php echo $sub_option['note']; ?></span>
                        </div>
						<?php if ( ! empty( $sub_option['options'] ) ) : ?>
							<?php foreach ( $sub_option['options'] as $option ) :
								$option['name'] = $sub_option['slug'].$option['name'];
								?>
                                <div class="sbspf_sub_option_setting">
									<?php if ( $option['callback'] !== 'checkbox' ) :
										if ( isset( $option['shortcode'] ) ) : ?>
                                            <label title="<?php echo __( 'Click for shortcode option', $this->vars->text_domain() ); ?>"><?php echo $option['label']; ?></label><code class="sbspf_shortcode"> <?php echo $option['name'] . "\n"; ?>
                                                Eg: <?php echo $option['name']; ?>=<?php echo $option['shortcode']['example']; ?></code><br>
										<?php else: ?>
                                            <label><?php echo $option['label']; ?></label><br>
										<?php endif; ?>
									<?php else:
										$option['shortcode_example'] = $option['shortcode']['example'];
										$option['has_shortcode'] = true;
									endif; ?>
									<?php call_user_func_array( array( $this, $option['callback'] ), array( $option ) ); ?>

                                </div>

							<?php endforeach; ?>
						<?php endif; ?>

                    </div>

				<?php endforeach; endif; ?>
            </div>
        </div>
		<?php
	}

	public function date_format( $args ) {

		?>
        <div class="sbspf_setting_wrap">

		<?php
			$args['options'] = $args['date_formats'];
			$this->select( $args );
			$custom_value = isset( $this->settings['customdate'] ) ? stripslashes( $this->settings['customdate'] ) : '';
			?>
        </div>
        <div class="sbspf_box_settings">
            <div class="sbspf_box_setting sbsw_relativetext_wrap">
                <?php foreach ( $args['text_settings'] as $text_setting ) :
                    $value = isset( $this->settings[ $text_setting['key'] ] ) ? stripslashes( $this->settings[ $text_setting['key'] ] ) : $text_setting['default'];

                    ?>
                <div class="sbsw-date-text-setting-wrap">
                    <label><?php echo $text_setting['label']; ?></label>
                    <input name="sbsw_settings[<?php echo $text_setting['key']; ?>]" id="sbsw_settings_<?php echo $text_setting['key']; ?>" type="text" placeholder="<?php echo $text_setting['default']; ?>" value="<?php echo esc_attr( $value ); ?>">
                </div>
                <?php endforeach; ?>

            </div>
            <div class="sbspf_box_setting sbsw_customdate_wrap">
                <label><?php _e( 'Custom Format', SBSW_TEXT_DOMAIN ); ?></label><br>
                <input name="sbsw_settings[customdate]" id="sbsw_settings_customdate" type="text" placeholder="F j, Y g:i a" value="<?php echo esc_attr( $custom_value ); ?>"><a href="https://smashballoon.com/social-wall/docs/date/" class="sbspf-external-link sbspf_note" target="_blank"><?php _e( 'Examples', SBSW_TEXT_DOMAIN ); ?></a>
            </div>
        </div>
		<?php
	}

	public function default_tooltip_text() {
		return '<span class="screen-reader-text">' . __( 'What does this mean?', $this->vars->text_domain() ) . '</span>' . sbsw_admin_icon( 'question-circle' );
	}

}

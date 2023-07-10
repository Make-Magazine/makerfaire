<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

GFForms::include_addon_framework();

class GravityView_Inline_Edit_GFAddon extends GFAddOn {

	/**
	 * @var string Minimum required GF version
	 */
	protected $_min_gravityforms_version = '2.0';

	/**
	 * @var string Slug for settings page URL
	 */
	protected $_slug = 'gk-gravityedit';

	/**
	 * @var string Path used by GF to add Settings link to addon settings in Plugins page
	 */
	protected $_path = 'gravityview-inline-edit/gravityview-inline-edit.php';

	protected $_full_path = __FILE__;

	protected $_title = 'GravityEdit';

	protected $_short_title = 'GravityEdit';

	private static $_instance = null;

	/**
	 * GravityView_Inline_Edit_Settings constructor.
	 */
	public function __construct() {

		if ( self::$_instance ) {
			return self::$_instance;
		}

		add_filter( 'gk/foundation/settings/data/plugins', array( $this, 'add_settings' ) );

		parent::__construct();
	}

	/**
	 * Add GravityEdit settings to Foundation.
	 *
	 * @param array $plugins_data Plugins data.
     *
     * @return array $plugins_data
	 */
    function add_settings( $plugins_data ) {

	    // Sanity check for class alias existing.
	    if ( ! class_exists( 'GravityKitFoundation' ) || ! GravityKitFoundation::settings() ) {
		    return $plugins_data;
	    }

	    $plugin_id = 'gravityedit';

	    $tooltip_format = '<p><img src="' . esc_url( plugins_url( 'assets/images/', GRAVITYEDIT_FILE ) ) . '/{image}" height="200" style="max-height: 200px;" alt="" /><strong>{description}</strong></p>';

		$default_settings = array(
			'inline-edit-mode' => 'popup'
		);

	    $settings = [
		    'id'       => $plugin_id,
		    'title'    => 'GravityEdit',
		    'defaults' => $default_settings,
		    'icon'     => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiByeD0iOCIgZmlsbD0iIzAwRDlFOSIvPgo8ZyBjbGlwLXBhdGg9InVybCgjY2xpcDBfMTZfMTIpIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik01OC4wMDQyIDQ5Ljk5OTlINDYuMDI1NFY1Ny45OTk5QzQ2LjAyNTQgNTkuMTA0NSA0Ni45MTkyIDYwIDQ4LjAyMTggNjBINTEuMDE2NUM1MS41Njc5IDYwIDUyLjAxNDggNjAuNDQ3NyA1Mi4wMTQ4IDYwLjk5OTlWNjIuOTk5OUM1Mi4wMTQ4IDYzLjU1MjMgNTEuNTY3OSA2My45OTk5IDUxLjAxNjUgNjMuOTk5OUg0OC4wMjE4QzQ0LjcxNCA2My45OTk5IDQyLjAzMjUgNjEuMzEzNyA0Mi4wMzI1IDU3Ljk5OTlWMjJDNDIuMDMyNSAxOC42ODYzIDQ0LjcxNCAxNiA0OC4wMjE4IDE2SDUyLjAxNDhWMjBINDguMDIxOEM0Ni45MTkyIDIwIDQ2LjAyNTQgMjAuODk1MyA0Ni4wMjU0IDIyVjQ2SDU4LjAwNDJDNTkuMTA2OSA0NiA2MC4wMDA3IDQ1LjEwNDUgNjAuMDAwNyA0My45OTk5VjM1Ljk5OTlDNjAuMDAwNyAzNC44OTUzIDU5LjEwNjkgMzQgNTguMDA0MiAzNEg1MS4wMTY1QzUwLjQ2NTIgMzQgNTAuMDE4NCAzMy41NTIyIDUwLjAxODQgMzNWMzAuOTk5OUM1MC4wMTg0IDMwLjQ0NzcgNTAuNDY1MiAyOS45OTk5IDUxLjAxNjUgMjkuOTk5OUg1OC4wMDQyQzYxLjMxMjEgMjkuOTk5OSA2My45OTM2IDMyLjY4NjMgNjMuOTkzNiAzNS45OTk5VjQzLjk5OTlDNjMuOTkzNiA0Ny4zMTM3IDYxLjMxMjEgNDkuOTk5OSA1OC4wMDQyIDQ5Ljk5OTlaTTMyLjA1IDYzLjk5OTlIMjkuMDU1NEMyOC41MDQgNjMuOTk5OSAyOC4wNTcxIDYzLjU1MjMgMjguMDU3MSA2Mi45OTk5VjYwLjk5OTlDMjguMDU3MSA2MC40NDc3IDI4LjUwNCA2MCAyOS4wNTU0IDYwSDMyLjA1QzMzLjE1MjcgNjAgMzQuMDQ2NSA1OS4xMDQ1IDM0LjA0NjUgNTcuOTk5OVYzNEgyMi4wNjc3QzIwLjk2NSAzNCAyMC4wNzEzIDM0Ljg5NTMgMjAuMDcxMyAzNS45OTk5VjQzLjk5OTlDMjAuMDcxMyA0NS4xMDQ1IDIwLjk2NSA0NiAyMi4wNjc3IDQ2SDI5LjA1NTRDMjkuNjA2NyA0NiAzMC4wNTM2IDQ2LjQ0NzcgMzAuMDUzNiA0Ni45OTk5VjQ5QzMwLjA1MzYgNDkuNTUyMiAyOS42MDY3IDQ5Ljk5OTkgMjkuMDU1NCA0OS45OTk5SDIyLjA2NzdDMTguNzU5OSA0OS45OTk5IDE2LjA3ODMgNDcuMzEzNyAxNi4wNzgzIDQzLjk5OTlWMzUuOTk5OUMxNi4wNzgzIDMyLjY4NjMgMTguNzU5OSAyOS45OTk5IDIyLjA2NzcgMjkuOTk5OUgzNC4wNDY1VjIyQzM0LjA0NjUgMjAuODk1MyAzMy4xNTI3IDIwIDMyLjA1IDIwSDI5LjA1NTRDMjguNTA0IDIwIDI4LjA1NzEgMTkuNTUyMyAyOC4wNTcxIDE4Ljk5OTlWMTdDMjguMDU3MSAxNi40NDc3IDI4LjUwNCAxNiAyOS4wNTU0IDE2SDMyLjA1QzM1LjM1NzkgMTYgMzguMDM5NSAxOC42ODYzIDM4LjAzOTUgMjJWNTcuOTk5OUMzOC4wMzk1IDYxLjMxMzcgMzUuMzU3OSA2My45OTk5IDMyLjA1IDYzLjk5OTlaIiBmaWxsPSJ3aGl0ZSIvPgo8L2c+CjxkZWZzPgo8Y2xpcFBhdGggaWQ9ImNsaXAwXzE2XzEyIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSJ3aGl0ZSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTYgMTYpIi8+CjwvY2xpcFBhdGg+CjwvZGVmcz4KPC9zdmc+',
		    'sections' => [
			    [
				    'title'    => esc_html__( 'General', 'gk-gravityedit' ),
				    'settings' => [
					    [
						    'id'            => 'inline-edit-mode',
						    'type'          => 'select',
						    'title'         => __( 'Inline Edit Mode', 'gk-gravityedit' ),
						    'description'   => esc_html__( 'Change where the Inline Edit form appears &ndash; above the content or in its place.', 'gk-gravityedit' ),
						    'default_value' => 'popup',
						    'value'         => GravityKitFoundation::settings()->get_plugin_setting( $plugin_id, 'inline-edit-mode', $default_settings['inline-edit-mode'] ),
						    'choices'       => [
							    [
								    'title' => esc_html__( 'Popup', 'gk-gravityedit' ),
								    'value' => 'popup',
							    ],
							    [
								    'title' => esc_html__( 'In-Place', 'gk-gravityedit' ),
								    'value' => 'inline',
							    ],
						    ],
					    ],
					    [
						    'type'     => 'html',
						    'id'       => 'inline-edit-mode-popup-description',
						    'html'     => strtr( $tooltip_format, [
							    '{image}'       => 'gf-popup.png',
							    '{description}' => esc_html__( 'Popup: The edit form will appear above the content.', 'gk-gravityedit' ),
						    ] ),
						    'requires' => [
							    'id'       => 'inline-edit-mode',
							    'operator' => '=',
							    'value'    => 'popup',
						    ],
					    ],
					    [
						    'type'     => 'html',
						    'id'       => 'inline-edit-mode-in-place-description',
						    'html'     => strtr( $tooltip_format, [
							    '{image}'       => 'gf-in-place.png',
							    '{description}' => esc_html__( 'In-Place: The edit form for the field will show in the same place as the content.', 'gk-gravityedit' ),
						    ] ),
						    'requires' => [
							    'id'       => 'inline-edit-mode',
							    'operator' => '=',
							    'value'    => 'inline',
						    ],
					    ],
				    ],
			    ],
		    ],
	    ];

	    return array_merge( $plugins_data, [ $plugin_id => $settings ] );
    }

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @since 1.6.1
	 *
	 * @return string
	 */
	public function get_menu_icon() {
        return '<svg style="height: 24px; width: 24px; position: absolute; max-width: 100%;" height="256" width="256" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 528 529" viewBox="0 0 256 256"><path d="M199.976 168h-47.999v32a8 8 0 0 0 8 8h12a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4h-12a24 24 0 0 1-24-24V56a24 24 0 0 1 24-24h16v16h-16a8 8 0 0 0-8 8v96h47.998a8 8 0 0 0 8-8v-32a8 8 0 0 0-8-8h-27.999a4 4 0 0 1-4-4v-8a4 4 0 0 1 4-4h28a24 24 0 0 1 23.999 24v32a24 24 0 0 1-24 24zM95.979 224h-12a4 4 0 0 1-4-4v-8a4 4 0 0 1 4-4h12a8 8 0 0 0 8-8v-96H55.98a8 8 0 0 0-8 8v32a8 8 0 0 0 8 8h28a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4h-28a24 24 0 0 1-24-24v-32a24 24 0 0 1 24-24h47.999V56a8 8 0 0 0-8-8h-12a4 4 0 0 1-4-4v-8a4 4 0 0 1 4-4h12a24 24 0 0 1 23.999 24v144a24 24 0 0 1-24 24z"/></svg>';
	}


	/**
	 * Returns TRUE if the settings "Save" button was pressed
	 *
	 * @since 1.0.3 Fixes conflict with Import Entries plugin
	 *
	 * @return bool True: Settings form is being saved and the Inline Edit setting is in the posted values (form settings)
	 */
	public function is_save_postback() {
		return ! rgempty( 'gform-settings-save' ) && ( isset( $_POST['gv_inline_edit_enable'] ) || isset( $_POST['_gravityview-inline-edit_save_settings_nonce'] ) || isset( $_POST['_gaddon_setting_inline-edit-mode'] ) || isset( $_POST['_gform_setting_inline-edit-mode'] ) );
	}

	/**
	 * Get the one instance of the object
	 *
	 * @since 1.0
	 *
	 * @return GravityView_Inline_Edit_GFAddon
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {

			self::$_instance = new self();

			GFAddOn::register( 'GravityView_Inline_Edit_GFAddon' );
		}

		return self::$_instance;
	}

	/**
	 * Returns HTML tooltip for the     edit mode setting
	 *
	 * @since 1.0
	 *
	 * @return string HTML for the tooltip about the edit modes
	 */
	private function _get_edit_mode_tooltip_html() {

		$tooltips = array(
			'popup'  => array(
				'image'       => 'gf-popup',
				'description' => esc_html__( 'Popup: The edit form will appear above the content.', 'gk-gravityedit' ),
			),
			'inline' => array(
				'image'       => 'gf-in-place',
				'description' => esc_html__( 'In-Place: The edit form for the field will show in the same place as the content.', 'gk-gravityedit' ),
			),
		);

		$tooltip_format = '<p class="gv-inline-edit-mode-image" data-edit-mode="%s"><img src="%s" height="150" style="display: block; margin-bottom: .5em;" /><strong>%s</strong></p>';

		$tooltip_html = '';

		foreach ( $tooltips as $mode => $tooltip ) {

			$image_link = plugins_url( "assets/images/{$tooltip['image']}.png", GRAVITYEDIT_FILE );

			$tooltip_html .= sprintf( $tooltip_format, $mode, $image_link, $tooltip['description'] );
		}

		return $tooltip_html;
	}

	/**
	 * Add print_styles hook in the admin
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init_admin() {

		// enqueues admin scripts
		add_action( 'admin_head', array( $this, 'print_select_scripts' ), 10 );

		parent::init_admin();
	}

	/**
	 * Print inline CSS and JS to make improve how <select> works
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function print_select_scripts() {
		?>
        <script>
			jQuery( document ).ready( function ( $ ) {
				$( 'select[name*="edit-mode"]' ).on( 'change', function () {
					$( '.gv-inline-edit-mode-image' ).hide().filter( '[data-edit-mode="' + $( this ).val() + '"]' ).show();
				} ).trigger( 'change' );

				function toggle_inline_edit_icon_visibility() {

					var is_view_inline_edit_enabled = $( '#gravityview_se_inline_edit' ).is( ':checked' );

					$( '#gv-view-configuration-tabs' )
						.find( '.gv-field-controls .icon-allow-inline-edit' )
						.not( '.hide-if-js' )
						.toggle( is_view_inline_edit_enabled );
				}

				$( 'body' )
					.on( 'change', ".gv-dialog-options input[name*=enable_inline_edit]", function ( e ) {
						var parent = $( e.target ).parents( '.gv-fields' );
						var inline_edit = ( 'enabled' === $( e.target ).filter( ':checked' ).val() );
						parent.toggleClass( 'icon-allow-inline-edit', inline_edit );
						parent.find( '.gv-field-controls .icon-allow-inline-edit' ).toggleClass( 'hide-if-js', !inline_edit );
						toggle_inline_edit_icon_visibility();
					} )
					.on( 'change', '#gravityview_se_inline_edit', function () {
						toggle_inline_edit_icon_visibility();
					} )
					.on( 'gravityview/field-added', function ( e, field ) {
						if ( $( field ).find( '.gv-dialog-options .gv-setting-container-enable_inline_edit' ).length > 0 ) {
							$( field ).find( '.gv-field-controls .icon-allow-inline-edit' ).removeClass( 'hide-if-js' );
						}
					} );

				// gravityview/loaded has already run; let's initialize whether to show or hide the icons
				toggle_inline_edit_icon_visibility();

			} );
        </script>

        <style>
			#gaddon-setting-row-inline-edit-mode .gv-inline-edit-mode-image {
				display: none;
				margin-top: 15px;
			}

			.gv-dialog-options .gv-setting-container label[for$=enable_inline_edit]::after {
				content: "\f464";
				font-family: Dashicons, sans-serif;
				font-size: 20px;
				height: 20px;
				width: 20px;
				line-height: 20px;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: 0.25em;
				position: absolute;
				color: #555D66;
			}

			.gv-fields .gv-field-controls .dashicons-edit {
				line-height: 44px;
				margin: -2px 6px 0 0;
			}
        </style>
		<?php
	}

}

<?php
/**
 * Renders all the metaboxes on Add New / Edit View post type.
 *
 * PSR-4 migration of the legacy GravityView_Admin_Views class.
 *
 * @since     1.0.0
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @package   GravityKit\GravityView\Admin
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

use GFCommon;
use GFForms;
use GravityKitFoundation;
use GravityKit\GravityView\Permissions\Permissions;
use GravityKit\GravityView\Entry\BulkActions\Config as BulkActionsConfig;
use GravityKit\GravityView\Entry\BulkActions\Registry as BulkActionsRegistry;
use GravityKit\GravityView\QueryFilters\Aggregate;
use GravityKit\GravityView\QueryFilters\QueryFilters;
use GravityView_Admin_View_Field;
use GravityView_Admin_View_Widget;
use GravityView_Ajax;
use GravityView_Fields;
use GravityView_Render_Settings;
use GV\Field_Collection;
use GV\Plugin;
use GV\View;
use GV\Widget_Collection;
use GravityKit\GravityView\Deprecation\DeprecatedHookNotices;
use GravityKit\GravityView\Renderer\Grid;
use GravityKit\GravityView\Search\Fields\SearchField as Search_Field;
use GravityKit\GravityView\Utils\Assets;
use GVCommon;

use function gravityview;
use function gravityview_get_connected_views;
use function gravityview_get_directory_entries_template_id;
use function gravityview_get_directory_fields;
use function gravityview_get_directory_search;
use function gravityview_get_directory_widgets;
use function gravityview_get_entry_meta;
use function gravityview_get_form;
use function gravityview_get_form_fields;
use function gravityview_get_form_id;
use function gravityview_get_joined_forms;
use function gravityview_get_link;
use function gravityview_get_registered_templates;
use function gravityview_get_single_entry_template_id;
use function gravityview_get_template_id;
use function gravityview_set_directory_widgets;
use function rgget;
use function rgpost;

class AdminViews {
	/**
	 * Contains the singleton.
	 *
	 * @since 2.42
	 *
	 * @var self
	 */
	private static self $instance;

	/**
	 * Cached entry counts per form ID, fetched in a single query.
	 *
	 * @since 3.0.0
	 *
	 * @var array<int, int>|null
	 */
	private ?array $entry_counts = null;

	/**
	 * Returns the singleton.
	 *
	 * @since 2.42
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		self::$instance ??= new self();

		return self::$instance;
	}

	/**
	 * Initializes the class and registers hooks.
	 *
	 * @since 2.42
	 */
	public function __construct() {
		add_action( 'save_post', [ $this, 'save_postdata' ] );

		add_action( 'current_screen', [ $this, 'disable_views_overview_notices' ] );

		add_filter( 'gravityview_blocklist_field_types', [ $this, 'default_field_blocklist' ], 10, 2 );

		add_filter( 'gform_tooltips', [ $this, 'tooltips' ] );

		add_filter( 'admin_body_class', [ $this, 'add_gf_version_css_class' ] );

		add_action( 'admin_enqueue_scripts', [ 'GravityView_Admin_Views', 'add_scripts_and_styles' ], 999 );
		add_filter( 'gform_noconflict_styles', [ $this, 'register_no_conflict' ] );
		add_filter( 'gform_noconflict_scripts', [ $this, 'register_no_conflict' ] );
		add_filter( 'gravityview_noconflict_styles', [ $this, 'register_no_conflict' ] );
		add_filter( 'gravityview_noconflict_scripts', [ $this, 'register_no_conflict' ] );

		add_action( 'gravityview_render_directory_active_areas', [ $this, 'render_directory_active_areas' ], 10, 6 );
		add_action( 'gravityview_render_widgets_active_areas', [ $this, 'render_widgets_active_areas' ], 10, 3 );
		add_action( 'gravityview_render_field_pickers', [ $this, 'render_field_pickers' ], 10, 2 );
		add_action( 'gravityview_render_available_fields', [ $this, 'render_available_fields' ], 10, 2 );
		add_action( 'gravityview_render_available_widgets', [ $this, 'render_available_widgets' ] );
		add_action( 'gravityview_render_active_areas', [ $this, 'render_active_areas' ], 10, 5 );
		add_filter( 'gravityview/view/configuration/fields', [ $this, 'set_default_view_fields' ], 10, 3 );

		add_filter( 'manage_gravityview_posts_columns', [ $this, 'add_post_type_columns' ] );

		add_filter( 'gform_toolbar_menu', [ 'GravityView_Admin_Views', 'gform_toolbar_menu' ], 10, 2 );
		add_action( 'gform_form_actions', [ 'GravityView_Admin_Views', 'gform_toolbar_menu' ], 10, 2 );

		add_action( 'manage_gravityview_posts_custom_column', [ $this, 'add_custom_column_content' ], 10, 2 );

		add_action( 'restrict_manage_posts', [ $this, 'add_view_dropdown' ] );

		add_action( 'pre_get_posts', [ $this, 'filter_pre_get_posts' ] );

		add_filter( 'gravityview/support_port/localization_data', [ $this, 'suggest_support_articles' ] );

		add_action( 'gk/gravityview/admin-views/row/before', [ $this, 'render_actions' ], 5, 4 );
		add_action( 'gk/gravityview/admin-views/view/after-zone', [ $this, 'render_add_row' ], 5, 4 );
		add_filter( 'gk/gravityview/admin-views/view/is-dynamic', [ $this, 'set_dynamic_areas' ], 0, 4 );

		add_action( 'gk/gravityview/admin-views/area/actions', [ $this, 'add_clear_all_fields_action' ], 0, 6 );
	}

	/**
	 * Disables all notices and footer text on the Views overview page.
	 *
	 * @since 2.33
	 *
	 * @return void
	 */
	public function disable_views_overview_notices() {
		if ( ! $this->is_views_overview_page() ) {
			return;
		}
		add_action(
			'admin_enqueue_scripts',
			function () {
				remove_all_actions( 'admin_notices' );
			}
		);
		add_filter(
			'admin_enqueue_scripts',
			function () {
				remove_all_filters( 'update_footer' );
			}
		);
		add_action(
			'admin_footer_text',
			function () {
				return '';
			}
		);
	}

	/**
	 * Checks if the current page is the Views overview page.
	 *
	 * @since 2.33
	 *
	 * @return bool
	 */
	public function is_views_overview_page(): bool {
		$screen = get_current_screen();

		return $screen && View::POST_TYPE === $screen->post_type && 'edit' === $screen->base;
	}

	/**
	 * Allow targeting different versions of Gravity Forms using CSS selectors.
	 *
	 * Adds specific version class: `.gf-version-2.6.1.3` as well as point updates: `.gf-minor-version-2.6`.
	 *
	 * @since    2.14.4
	 *
	 * @param string $body_class Existing body class for the WordPress admin.
	 *
	 * @return string Original with two classes added. If GFForms isn't available, returns original string.
	 * @internal Do not rely on this remaining public.
	 */
	public function add_gf_version_css_class( $body_class ) {
		if ( ! class_exists( 'GFForms' ) || empty( GFForms::$version ) ) {
			return $body_class;
		}

		$body_class .= ' gf-version-' . str_replace( '.', '-', GFForms::$version );

		$major_version = explode( '.', GFForms::$version );

		if ( 2 <= count( $major_version ) ) {
			$body_class .= ' gf-minor-version-' . esc_attr( $major_version[0] . '-' . $major_version[1] );
		}

		return $body_class;
	}

	/**
	 * When on the Add/Edit View screen, suggest most popular articles related to that
	 *
	 * @param array $localization_data Data to be passed to the Support Port JS.
	 *
	 * @return array
	 */
	public function suggest_support_articles( $localization_data = [] ) {
		if ( ! gravityview()->request->is_view( false ) ) {
			return $localization_data;
		}

		$localization_data['suggest'] = [
			'57ef23539033602e61d4a560',
			'54c67bb9e4b0512429885513',
			'54c67bb9e4b0512429885512',
			'54c67bbbe4b07997ea3f3f6b',
			'54d1a33ae4b086c0c0964ce9',
			'57ef253c9033602e61d4a563',
			'552355bfe4b0221aadf2572b',
			'54c67bcde4b051242988553e',
		];

		return $localization_data;
	}

	/**
	 * @since 1.15
	 *
	 * @param WP_Query $query The current WP_Query object, passed by reference.
	 */
	public function filter_pre_get_posts( &$query ) {
		global $pagenow;

		if ( ! is_admin() ) {
			return;
		}

		if ( 'edit.php' !== $pagenow ) {
			return;
		}

		if ( ! isset( $query->query_vars['post_type'] ) ) {
			return;
		}

		if ( 'gravityview' !== $query->query_vars['post_type'] ) {
			return;
		}

		$form_id = (int) \GV\Utils::_GET( 'gravityview_form_id' );

		$meta_query = [];

		if ( $form_id ) {
			$meta_query[] = [
				'key'   => '_gravityview_form_id',
				'value' => $form_id,
			];
		}

		$layout_id = \GV\Utils::_GET( 'gravityview_layout' );

		if ( $layout_id ) {
			$meta_query[] = [
				'key'   => '_gravityview_directory_template',
				'value' => esc_attr( $layout_id ),
			];
		}

		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * Adds dropdown selects to filter Views by connected form and layout
	 *
	 * @return void
	 */
	public function add_view_dropdown() {
		$current_screen = get_current_screen();

		if ( 'gravityview' !== $current_screen->post_type ) {
			return;
		}

		$forms = GVCommon::get_forms_columns( true, false, 'title', 'ASC', array( 'id', 'title' ) );

		// Match the case-insensitive natural-order sort that GVCommon::get_forms() applies for $order_by = 'title'.
		uasort(
            $forms,
            static function ( $a, $b ) {
				return strnatcasecmp( $a['title'] ?? '', $b['title'] ?? '' );
			}
        );

		$current_form = \GV\Utils::_GET( 'gravityview_form_id' );

		if ( ! empty( $forms ) && is_array( $forms ) ) { ?>
            <label for="gravityview_form_id" class="screen-reader-text">
            <?php
            esc_html_e(
                'Filter Views by form',
                'gk-gravityview'
            );
			?>
                    </label>
            <select name="gravityview_form_id" id="gravityview_form_id">
                <option value="" <?php selected( '', $current_form, true ); ?>>
                <?php
                esc_html_e(
                    'All forms',
                    'gk-gravityview'
                );
				?>
                        </option>
							<?php
                            foreach ( $forms as $form ) {
								if ( ! is_array( $form ) || ! isset( $form['id'], $form['title'] ) ) {
									continue;
								}
								?>
									<option value="<?php echo esc_attr( $form['id'] ); ?>" <?php selected( $form['id'], $current_form, true ); ?>>
											<?php echo esc_html( $form['title'] ); ?>
									</option>
							<?php } ?>
            </select>
			<?php
		}

		$layouts        = gravityview_get_registered_templates();
		$current_layout = \GV\Utils::_GET( 'gravityview_layout' );

		if ( ! empty( $layouts ) ) {
			?>
            <label for="gravityview_layout_name" class="screen-reader-text">
            <?php
            esc_html_e(
                'Filter Views by layout',
                'gk-gravityview'
            );
			?>
                    </label>
            <select name="gravityview_layout" id="gravityview_layout_name">
                <option value="" <?php selected( '', $current_layout, true ); ?>>
                <?php
                esc_html_e(
                    'All layouts',
                    'gk-gravityview'
                );
				?>
                        </option>
                <optgroup label="<?php esc_html_e( 'Layouts', 'gk-gravityview' ); ?>">
					<?php
					foreach ( $layouts as $layout_id => $layout ) {
						if ( in_array( $layout['type'] ?? '', [ 'preset', 'internal' ], true ) ) {
							continue;
						}
						?>
                        <option value="<?php echo esc_attr( $layout_id ); ?>" 
                        <?php
                        selected(
                            $layout_id,
							$current_layout,
                            true
                        );
						?>
                            ><?php echo esc_html( $layout['label'] ?? '' ); ?></option>
					<?php } ?>
                </optgroup>
                <optgroup label="<?php esc_html_e( 'Form Presets', 'gk-gravityview' ); ?>">
					<?php
					foreach ( $layouts as $layout_id => $layout ) {
						if ( ! in_array( $layout['type'] ?? '', [ 'preset' ], true ) ) {
							continue;
						}
						?>
                        <option value="<?php echo esc_attr( $layout_id ); ?>" 
                        <?php
                        selected(
                            $layout_id,
							$current_layout,
                            true
                        );
						?>
                            ><?php echo esc_html( $layout['label'] ); ?></option>
					<?php } ?>
                </optgroup>
            </select>
			<?php
		}
	}

	/**
	 * @deprecated 3.0.0 1.2
	 * Start using GravityView_Render_Settings::render_setting_row
	 *
	 * @param string $key              The setting key.
	 * @param array  $current_settings The current View settings.
	 * @param mixed  $override_input   Input HTML to use instead of the generated input.
	 * @param string $name             The format for the input name attribute.
	 * @param string $id               The format for the input ID attribute.
	 */
	public static function render_setting_row(
		$key = '',
		$current_settings = [],
		$override_input = null,
		$name = 'template_settings[%s]',
		$id = 'gravityview_se_%s'
	) {
		_deprecated_function(
            'GravityView_Admin_Views::render_setting_row',
			'1.1.7',
            'GravityView_Render_Settings::render_setting_row'
        );
		GravityView_Render_Settings::render_setting_row( $key, $current_settings, $override_input, $name, $id );
	}

	/**
	 * @deprecated 3.0.0 1.2
	 * Start using GravityView_Render_Settings::render_field_option
	 *
	 * @param string $name       The name attribute of the option field.
	 * @param array  $option     The field option configuration.
	 * @param mixed  $curr_value The current value of the option.
	 */
	public static function render_field_option( $name = '', $option = [], $curr_value = null ) {
		_deprecated_function(
            'GravityView_Admin_Views::render_field_option',
			'1.1.7',
            'GravityView_Render_Settings::render_field_option'
        );

		return GravityView_Render_Settings::render_field_option( $name, $option, $curr_value );
	}

	/**
	 * Add a GravityView menu to the Form Toolbar with connected views
	 *
	 * @param array $menu_items Menu items, as set in GFForms::top_toolbar().
	 * @param int   $id         ID of the current Gravity form.
	 *
	 * @return array            Modified array
	 */
	public static function gform_toolbar_menu( $menu_items = [], $id = null ) {
		if ( 'trash' === rgget( 'filter' ) ) {
			return $menu_items;
		}

		$connected_views = gravityview_get_connected_views( $id, [ 'post_status' => 'any' ] );

		$priority = 0;

		if ( 'form_list' === GFForms::get_page() ) {
			$priority = 790;
		}

		$sub_menu_items = [];
		foreach ( (array) $connected_views as $view ) {
			if ( ! ( new Permissions() )->can_edit_view( (int) $view->ID ) ) {
				continue;
			}

			$label = empty( $view->post_title ) ? sprintf(
                /* translators: %d: The View ID. */
                __( 'No Title (View #%d)', 'gk-gravityview' ),
                $view->ID
            ) : $view->post_title;

			$sub_menu_items[] = [
				'label' => str_replace( '%', '%%', esc_attr( $label ) ),
				'url'   => admin_url( 'post.php?action=edit&post=' . $view->ID ),
				'icon'  => '<i class="fa fa-lg gv-icon-astronaut-head gv-icon"></i>',
			];
		}

		if ( empty( $sub_menu_items ) ) {
			$menu_items['gravityview'] = [
				'label'        => esc_attr__( 'Create a View', 'gk-gravityview' ),
				'icon'         => '<i class="fa fa-lg gv-icon-astronaut-head gv-icon"></i>',
				'title'        => esc_attr__( 'Create a View using this form as a data source', 'gk-gravityview' ),
				'url'          => admin_url( 'post-new.php?post_type=gravityview&form_id=' . $id ),
				'menu_class'   => 'gv_connected_forms gf_form_toolbar_settings',
				'priority'     => $priority,
				'capabilities' => [ 'edit_gravityviews' ],
			];

			return $menu_items;
		}

		$sub_menu_items[] = [
			'label'        => esc_attr__( 'Create a View', 'gk-gravityview' ),
			'icon'         => '<span class="dashicons dashicons-plus"></span>',
			'title'        => esc_attr__( 'Create a View using this form as a data source', 'gk-gravityview' ),
			'url'          => admin_url( 'post-new.php?post_type=gravityview&form_id=' . $id ),
			'capabilities' => [ 'edit_gravityviews' ],
		];

		// Make sure Gravity Forms uses the submenu; if there's only one item, it uses a link instead of a dropdown.
		$sub_menu_items[] = [
			'url'          => '#',
			'label'        => '',
			'menu_class'   => 'hidden',
			'capabilities' => '',
		];

		$menu_items['gravityview'] = [
			'label'          => __( 'Connected Views', 'gk-gravityview' ),
			'icon'           => '<i class="fa fa-lg gv-icon-astronaut-head gv-icon"></i>',
			'title'          => __( 'GravityView Views using this form as a data source', 'gk-gravityview' ),
			'url'            => '#',
			'onclick'        => 'return false;',
			'menu_class'     => 'gv_connected_forms gf_form_toolbar_settings',
			'sub_menu_items' => $sub_menu_items,
			'priority'       => $priority,
			'capabilities'   => [ 'edit_gravityviews' ],
		];

		return $menu_items;
	}

	/**
	 * List the field types without presentation properties (on a View context)
	 *
	 * @since 2.14
	 *
	 * @param array       $field_types Existing field types to add to a blocklist.
	 * @param string|null $context     Context for the blocklist. Default: NULL.
	 *
	 * @return array Default blocklist fields merged with existing blocklist fields
	 */
	public function default_field_blocklist( $field_types = [], $context = null ) {
		$add = [ 'captcha', 'page' ];

		if ( 'edit' === $context ) {
			$add[] = 'post_id';
		}

		$return = array_merge( $field_types, $add );

		return $return;
	}

	/**
	 * @deprecated 2.14
	 *
	 * @param array       $field_types Existing field types to add to a blocklist.
	 * @param string|null $context     Context for the blocklist.
	 */
	public function default_field_blacklist( $field_types, $context ) {
		_deprecated_function( 'GravityView_Admin_Views::default_field_blacklist', '2.14', 'GravityView_Admin_Views::default_field_blocklist' );
		$this->default_field_blocklist( $field_types, $context );
	}

	/**
	 * Add tooltip text for use throughout the UI
	 *
	 * @param array $tooltips Array of Gravity Forms tooltips.
	 *
	 * @return array                Modified tooltips array
	 */
	public function tooltips( $tooltips = [] ) {
		$gv_tooltips = [];

		$default_args = \GV\View_Settings::defaults( true );

		foreach ( $default_args as $key => $arg ) {
			if ( isset( $arg['tooltip'] ) && empty( $arg['tooltip'] ) ) {
				continue;
			}

			$tooltip = empty( $arg['tooltip'] ) ? null : $arg['tooltip'];

			if ( empty( $tooltip ) ) {
				continue;
			}

			$gv_tooltips[ 'gv_' . $key ] = [
				'title' => $arg['label'],
				'value' => $tooltip,
			];
		}

		/**
		 * The tooltips GravityView adds to the Gravity Forms tooltip array.
		 *
		 * @since 1.0.9-beta
		 * @deprecated 2.55 Renamed in 2.0 to `gravityview/metaboxes/tooltips`.
		 *
		 * @param array $gv_tooltips Associative array with unique keys containing array of `title` and `value` keys, as expected by `gform_tooltips` filter.
		 */
		$gv_tooltips = DeprecatedHookNotices::apply_filters( 'gravityview_tooltips', [ $gv_tooltips ], '2.55', 'gravityview/metaboxes/tooltips' );

		/**
		 * The tooltips GravityView adds to the Gravity Forms tooltip array.
		 *
		 * @since 2.0
		 *
		 * @param array $gv_tooltips Associative array with unique keys containing array of `title` and `value` keys, as expected by `gform_tooltips` filter.
		 */
		$gv_tooltips = apply_filters( 'gravityview/metaboxes/tooltips', $gv_tooltips );

		foreach ( $gv_tooltips as $key => $tooltip ) {
			$title = empty( $tooltip['title'] ) ? '' : '<h6>' . esc_html( $tooltip['title'] ) . '</h6>';

			$tooltips[ $key ] = $title . wpautop( esc_html( $tooltip['value'] ) );
		}

		return $tooltips;
	}

	/**
	 * Add the Data Source information
	 *
	 * @param null $column_name Name of the column in the Views table.
	 * @param int  $post_id     Post ID.
	 *
	 * @return void
	 */
	public function add_custom_column_content( $column_name = null, $post_id = 0 ) {
		$output = '';

		switch ( $column_name ) {
			case 'gv_template':
				$directory_template = gravityview_get_directory_entries_template_id( $post_id );
				$single_template    = gravityview_get_single_entry_template_id( $post_id );

				if ( empty( $directory_template ) ) {
					gravityview()->log->error(
                        'View ID {view_id} does not have a connected template.',
                        [ 'view_id' => $post_id ]
                    );
					break;
				}

				$templates = gravityview_get_registered_templates();

				$get_title = static function ( string $template_id ) use ( $templates ): string {
					$template = $templates[ $template_id ] ?? [];
					if ( ! $template ) {
						return '';
					}

					return $template['label'] ?? ucwords( implode( ' ', explode( '_', $template_id ) ) );
				};

				$output = $get_title( $directory_template );
				if ( $directory_template !== $single_template ) {
					$output .= ' / ' . $get_title( $single_template );
				}

				break;

			case 'gv_connected_form':
				$form_id = gravityview_get_form_id( $post_id );

				if ( empty( $form_id ) ) {
					gravityview()->log->error(
                        'View ID {view_id} does not have a connected GF form.',
                        [ 'view_id' => $post_id ]
                    );
					$output = __( 'Not connected.', 'gk-gravityview' );
					break;
				}

				$form = gravityview_get_form( $form_id );

				if ( ! $form ) {
					gravityview()->log->error( 'Connected form not found: Form #{form_id}', [ 'form_id' => $form_id ] );

					$output = __( 'The connected form can not be found; it may no longer exist.', 'gk-gravityview' );
				} else {
					$output = self::get_connected_form_links( $form );
				}

				break;
			case 'gv_entry_count':
				if ( ! GVCommon::has_cap( 'gravityforms_view_entries' ) ) {
					break;
				}

				$form_id = gravityview_get_form_id( $post_id );

				if ( empty( $form_id ) ) {
					$output = '&mdash;';
					break;
				}

				$entry_counts = $this->get_entry_counts();
				$count        = $entry_counts[ (int) $form_id ] ?? 0;

				$entries_url = admin_url( sprintf( 'admin.php?page=gf_entries&id=%d', $form_id ) );
				$output      = sprintf( '<a href="%s">%s</a>', esc_url( $entries_url ), number_format_i18n( $count ) );

				break;

			case 'shortcode':
				$view = \GV\View::by_id( $post_id );
				if ( ! $view ) {
					break;
				}

				$html = <<<'HTML'
<div class="gv-shortcode">
	<input title="%s" aria-labelledby="shortcode" type="text" readonly="readonly" value="%s" class="code shortcode widefat" />
	<span class="copied">%s</span>
    <div class="screen-reader-text">%1$s</div>
</div>
HTML;

				$output = sprintf(
					$html,
					esc_html__( 'Click to copy', 'gk-gravityview' ),
					esc_attr( $view->get_shortcode() ),
					esc_html__( 'Copied!', 'gk-gravityview' )
				);
				break;
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Column markup is built above from escaped values and trusted internal link HTML.
	}

	/**
	 * Get HTML links relating to a connected form, like Edit, Entries, Settings, Preview
	 *
	 * @param array|int $form              Gravity Forms forms array, or the form ID.
	 * @param boolean   $include_form_link Whether to include the bold name of the form in the output.
	 *
	 * @return string          HTML links
	 */
	public static function get_connected_form_links( $form, $include_form_link = true ) {
		if ( empty( $form ) ) {
			return '';
		}

		// $form may arrive as a form ID rather than a form array.
		if ( ! is_array( $form ) ) {
			$form = gravityview_get_form( $form );
		}

		if ( empty( $form ) ) {
			return '';
		}

		$form_id = $form['id'];
		$links   = [];

		if ( GVCommon::has_cap( 'gravityforms_edit_forms' ) ) {
			$form_url  = admin_url( sprintf( 'admin.php?page=gf_edit_forms&amp;id=%d', $form_id ) );
			$form_link = '<strong class="gv-form-title">' . gravityview_get_link(
                $form_url,
                $form['title'],
                'class=row-title'
            ) . '</strong>';
			$links[]   = '<span>' . gravityview_get_link( $form_url, __( 'Edit Form', 'gk-gravityview' ) ) . '</span>';
		} else {
			$form_link = '<strong class="gv-form-title">' . esc_html( $form['title'] ) . '</strong>';
		}

		if ( GVCommon::has_cap( 'gravityforms_view_entries' ) ) {
			$entries_url = admin_url( sprintf( 'admin.php?page=gf_entries&amp;id=%d', $form_id ) );
			$links[]     = '<span>' . gravityview_get_link(
                $entries_url,
                __( 'Entries', 'gk-gravityview' )
            ) . '</span>';
		}

		if ( GVCommon::has_cap( [ 'gravityforms_edit_settings', 'gravityview_view_settings' ] ) ) {
			$settings_url = admin_url(
                sprintf(
                    'admin.php?page=gf_edit_forms&amp;view=settings&amp;id=%d',
                    $form_id
                )
            );
			$links[]      = '<span>' . gravityview_get_link(
                $settings_url,
                __( 'Settings', 'gk-gravityview' ),
                'title=' . __( 'Edit settings for this form', 'gk-gravityview' )
            ) . '</span>';
		}

		if ( GVCommon::has_cap(
            [
				'gravityforms_edit_forms',
				'gravityforms_create_form',
				'gravityforms_preview_forms',
			]
        ) ) {
			$preview_url = site_url( sprintf( '?gf_page=preview&amp;id=%d', $form_id ) );
			$links[]     = '<span>' . gravityview_get_link(
                $preview_url,
                __( 'Preview Form', 'gk-gravityview' ),
                'title=' . __( 'Preview this form', 'gk-gravityview' )
            ) . '</span>';
		}

		$output = '';

		if ( ! empty( $include_form_link ) ) {
			$output .= $form_link;
		}

		/**
		 * Modify the links shown in the Connected Form links.
		 *
		 * @since 1.6
		 *
		 * @param array $links Links to show.
		 * @param array $form  Gravity Forms form array.
		 */
		$links = apply_filters( 'gravityview_connected_form_links', $links, $form );

		$css_class = 'row-actions';

		// Is Screen Options > View mode set to "Extended view"? If so, keep actions visible.
		if ( 'excerpt' === get_user_setting( 'posts_list_mode', 'list' ) ) {
			$css_class = 'row-actions visible';
		}

		$output .= '<div class="' . $css_class . '">' . implode( ' | ', $links ) . '</div>';

		return $output;
	}

	/**
	 * Add the Data Source column to the Views page
	 *
	 * @param array $columns Columns array.
	 */
	public function add_post_type_columns( $columns ) {
		// Held aside so it is re-inserted after the Data Source column.
		$date = $columns['date'];
		unset( $columns['date'] );

		$data_source_required_caps = [
			'gravityforms_edit_forms',
			'gravityforms_view_entries',
			'gravityforms_edit_settings',
			'gravityview_view_settings',
			'gravityforms_create_form',
			'gravityforms_preview_forms',
		];

		if ( GVCommon::has_cap( $data_source_required_caps ) ) {
			$columns['gv_connected_form'] = __( 'Data Source', 'gk-gravityview' );
		}

		if ( GVCommon::has_cap( 'gravityforms_view_entries' ) ) {
			$columns['gv_entry_count'] = __( 'Entries', 'gk-gravityview' );
		}

		$columns['gv_template'] = _x(
            'Template',
			'Column title that shows what template is being used for Views',
            'gk-gravityview'
        );

		$columns['shortcode'] = esc_html__( 'Shortcode', 'gk-gravityview' );

		$columns['date'] = $date;

		return $columns;
	}

	/**
	 * Returns entry counts for all forms connected to Views on the current page.
	 *
	 * Fetches counts per unique form ID to avoid N+1 queries when rendering the column.
	 *
	 * @since 3.0.0
	 *
	 * @return array<int, int> Map of form ID to active entry count.
	 */
	private function get_entry_counts(): array {
		if ( null !== $this->entry_counts ) {
			return $this->entry_counts;
		}

		global $wp_query;

		$this->entry_counts = [];

		if ( empty( $wp_query->posts ) ) {
			return $this->entry_counts;
		}

		$form_ids = [];
		foreach ( $wp_query->posts as $post ) {
			$form_id = gravityview_get_form_id( $post->ID );
			if ( $form_id ) {
				$form_ids[] = (int) $form_id;
			}
		}

		$form_ids = array_unique( $form_ids );

		if ( empty( $form_ids ) ) {
			return $this->entry_counts;
		}

		$form_id_field = Aggregate\Field::from_field(
			new \GF_Field(
				[
					'id'     => 'form_id',
					'formId' => 0,
				]
			)
		)->with_alias( 'form_id' );

		$results = Aggregate\Query::from( new \GF_Query( $form_ids, [ 'status' => 'active' ] ) )
			->group_by( $form_id_field )
			->count();

		foreach ( $results as $row ) {
			$this->entry_counts[ (int) $row['form_id'] ] = (int) $row['count'];
		}

		return $this->entry_counts;
	}

	/**
	 * Save View configuration
	 *
	 * @param int $post_id Currently saved Post ID.
	 *
	 * @return void
	 */
	public function save_postdata( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['post_type'] ) || 'gravityview' !== $_POST['post_type'] ) {
			return;
		}

		if ( ! ( new Permissions() )->can_edit_view( (int) $post_id ) ) {
			gravityview()->log->error(
				'Current user does not have the capability to edit View {view_id}',
				[
					'view_id' => $post_id,
					'data'    => wp_get_current_user(),
				]
			);

			return;
		}

		gravityview()->log->debug( '[save_postdata] Saving View post type.', [ 'data' => $_POST ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Request data is only logged for debugging; every write below is gated behind its own nonce check.

		$statii = [];

		if ( isset( $_POST['gravityview_select_form_nonce'] ) && wp_verify_nonce(
            $_POST['gravityview_select_form_nonce'],
            'gravityview_select_form'
        ) ) {
			$form_id = ! empty( $_POST['gravityview_form_id'] ) ? $_POST['gravityview_form_id'] : '';
			$statii['form_id'] = update_post_meta( $post_id, '_gravityview_form_id', $form_id );
		}

		if ( false === GVCommon::has_cap( 'gravityforms_create_form' ) && empty( $statii['form_id'] ) ) {
			gravityview()->log->error(
                'Current user does not have the capability to create a new Form.',
                [ 'data' => wp_get_current_user() ]
            );

			return;
		}

		if ( ! empty( $_POST['gravityview_form_id_start_fresh'] ) ) {
			$statii['start_fresh'] = add_post_meta( $post_id, '_gravityview_start_fresh', 1 );
		} else {
			$statii['start_fresh'] = delete_post_meta( $post_id, '_gravityview_start_fresh' );
		}

		if ( isset( $_POST['gravityview_select_template_nonce'] ) && wp_verify_nonce(
            $_POST['gravityview_select_template_nonce'],
            'gravityview_select_template'
        ) ) {
			$directory_template_id = rgpost( 'gravityview_directory_template' );
			$single_template_id    = rgpost( 'gravityview_single_template' );

			$statii['directory_template'] = update_post_meta(
                $post_id,
				'_gravityview_directory_template',
                $directory_template_id
            );
			$statii['single_template']    = update_post_meta(
                $post_id,
				'_gravityview_single_template',
                $single_template_id
            );
		}

		if ( isset( $_POST['gravityview_view_configuration_nonce'] ) && wp_verify_nonce(
            $_POST['gravityview_view_configuration_nonce'],
            'gravityview_view_configuration'
        ) ) {
			if ( empty( $_POST['template_settings'] ) ) {
				$_POST['template_settings'] = [];
			}

			// Design-token sub-arrays are sanitised here and unknown token
			// categories dropped; non-token keys pass through untouched.
			// `ViewStyles::saved_values()` sanitises again at read time, which
			// is what covers values stored before this ran.
			$template_settings = is_array( $_POST['template_settings'] )
				? \GravityKit\GravityView\Settings\ViewStyles::sanitize_payload( wp_unslash( $_POST['template_settings'] ) )
				: [];

			// Persisting custom code requires `unfiltered_html`; without it the
			// incoming `custom_css` and `custom_javascript` are discarded and
			// the saved values kept. On multisite only super admins hold that
			// capability by default.
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				$existing_settings = get_post_meta( $post_id, '_gravityview_template_settings', true );
				$existing_settings = is_array( $existing_settings ) ? $existing_settings : [];

				foreach ( [ 'custom_css', 'custom_javascript' ] as $code_key ) {
					unset( $template_settings[ $code_key ] );

					if ( isset( $existing_settings[ $code_key ] ) ) {
						$template_settings[ $code_key ] = $existing_settings[ $code_key ];
					}
				}
			}

			// Re-slash so `update_post_meta` doesn't double-process. Mirrors
			// WP's add_magic_quotes behaviour for the meta API.
			$statii['template_settings'] = update_post_meta(
				$post_id,
				'_gravityview_template_settings',
				wp_slash( $template_settings )
			);

			// `gv_fields_done` marks a rendered configuration screen; without it no fields were posted to save.
			if ( isset( $_POST['gv_fields'] ) && isset( $_POST['gv_fields_done'] ) ) {
				$fields = [];

				if ( ! empty( $_POST['gv_fields'] ) ) {
					$fields = _gravityview_process_posted_fields();
				}

				$fields = wp_slash( $fields );

				$statii['directory_fields'] = update_post_meta( $post_id, '_gravityview_directory_fields', $fields );
			}

			if ( isset( $_POST['row_settings'] ) && is_array( $_POST['row_settings'] ) ) {
				$row_settings = [];

				foreach ( $_POST['row_settings'] as $row_key => $settings ) {
					$row_key = sanitize_text_field( $row_key );

					if ( ! is_array( $settings ) ) {
						continue;
					}

					$sanitized = [];

					if ( ! empty( $settings['custom_class'] ) ) {
						$sanitized['custom_class'] = sanitize_text_field( $settings['custom_class'] );
					}

					if ( ! empty( $settings['custom_id'] ) ) {
						$sanitized['custom_id'] = sanitize_text_field( $settings['custom_id'] );
					}

					if ( ! empty( $sanitized ) ) {
						$row_settings[ $row_key ] = $sanitized;
					}
				}

				$statii['row_settings'] = update_post_meta( $post_id, '_gravityview_row_settings', $row_settings );
			}

			// Widgets post only as per-item `widgets[zone][uid][...]` inputs, so
			// an emptied configuration posts no `widgets` key at all — the same
			// shape as a save from a screen that never rendered the widgets
			// area. `gv_widgets_done` separates the two: the configuration
			// screen always renders it, so an absent payload beneath it means
			// the widgets were cleared rather than never loaded.
			$has_widgets_payload  = isset( $_POST['widgets'] ) && is_array( $_POST['widgets'] );
			$config_screen_posted = isset( $_POST['gv_widgets_done'] );

			if ( $has_widgets_payload || $config_screen_posted ) {
				$posted_widgets    = $has_widgets_payload ? $_POST['widgets'] : [];
				$view              = View::by_id( $post_id );
				$view_context      = $view instanceof View ? $view : null;
				$sanitized_widgets = $this->sanitize_directory_widgets( $posted_widgets, $view_context );

				$statii['directory_widgets'] = gravityview_set_directory_widgets( $post_id, $sanitized_widgets );
			}
		} // end save view configuration

		/**
		 * After a View has been saved in the admin.
		 *
		 * @since 1.17.2
		 *
		 * @param int   $post_id ID of the View that has been saved.
		 * @param array $statii  Array of statuses of the post meta saving processes. If saving worked, each key should be mapped to a value of the post ID (`directory_widgets` => `124`). If failed (or didn't change), the value will be false.
		 */
		do_action( 'gravityview_view_saved', $post_id, $statii );

		gravityview()->log->debug(
            '[save_postdata] Update Post Meta Statuses (also returns false if nothing changed)',
            [ 'data' => array_map( 'intval', $statii ) ]
        );
	}

	/**
	 * Sanitizes View widget settings before persisting.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $widgets Widget settings.
	 * @param View|null $view    View context.
	 *
	 * @return array
	 */
	private function sanitize_directory_widgets( array $widgets, ?View $view = null ): array {
		foreach ( $widgets as $zone_key => $zone_widgets ) {
			if ( ! is_array( $zone_widgets ) ) {
				continue;
			}

			foreach ( $zone_widgets as $widget_key => $widget ) {
				if ( ! is_array( $widget ) ) {
					continue;
				}

				if ( BulkActionsConfig::WIDGET_ID === (string) ( $widget['id'] ?? '' ) ) {
					$widget['bulk_actions']                       = $this->sanitize_bulk_action_keys( $widget['bulk_actions'] ?? [] );
					$widget[ BulkActionsConfig::ACTION_SETTINGS ] = $this->sanitize_bulk_action_settings(
						$widget[ BulkActionsConfig::ACTION_SETTINGS ] ?? [],
						$view
					);
				}

				$widgets[ $zone_key ][ $widget_key ] = $widget;
			}
		}

		return $widgets;
	}

	/**
	 * Sanitizes per-action settings for the Bulk Actions widget.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed     $settings Action settings keyed by action key.
	 * @param View|null $view     View context.
	 *
	 * @return array
	 */
	private function sanitize_bulk_action_settings( $settings, ?View $view = null ): array {
		if ( ! is_array( $settings ) ) {
			return [];
		}

		$actions   = BulkActionsRegistry::get_actions( $view );
		$sanitized = [];

		foreach ( $settings as $action_key => $action_settings ) {
			$action_key = sanitize_key( $action_key );

			if ( '' === $action_key || ! is_array( $action_settings ) ) {
				continue;
			}

			if ( empty( $actions[ $action_key ] ) ) {
				$sanitized[ $action_key ] = $action_settings;
				continue;
			}

			$actions[ $action_key ]['settings'] = $action_settings;

			$schema    = BulkActionsConfig::get_action_settings_schema( $actions[ $action_key ], $view, $action_key );
			$preserved = array_diff_key( $action_settings, $schema );

			$sanitized[ $action_key ] = BulkActionsConfig::sanitize_action_settings( $action_settings, $schema ) + $preserved;
		}

		return $sanitized;
	}

	/**
	 * Sanitizes selected bulk action keys.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $action_keys Selected action keys.
	 *
	 * @return string[]
	 */
	private function sanitize_bulk_action_keys( $action_keys ): array {
		if ( ! is_array( $action_keys ) ) {
			return [];
		}

		$action_keys = array_filter(
			array_map( 'sanitize_key', $action_keys ),
			static function ( string $action_key ): bool {
				return '' !== $action_key;
			}
		);

		return array_values( $action_keys );
	}

	/**
	 * @deprecated 1.1.6
	 */
	public function render_label() {
		_deprecated_function(
            'GravityView_Admin_Views::render_label()',
			'1.1.6',
            'Use the GravityView_Admin_View_Field class instead.'
        );
	}

	/**
	 * Render html for displaying available fields based on a Form ID
	 *
	 * @see GravityView_Ajax::get_available_fields_html() Triggers `gravityview_render_available_fields` action
	 *
	 * @param int|string $form_id Gravity Forms form ID. Default: 0.
	 * @param string     $context (default: 'single').
	 *
	 * @return void
	 */
	public function render_available_fields( $form_id = 0, $context = 'single' ) {
		$form = GVCommon::get_form_or_form_template( $form_id );

		if ( strpos( $context, 'search-' ) === 0 ) {
			do_action( 'gravityview_render_available_search_fields', (int) $form_id, $context );

			return;
		}

		/**
		 * @deprecated 2.9
		 */
		$blocklist_field_types = \GravityView_Deprecated_Hook_Notices::apply_filters(
            'gravityview_blacklist_field_types',
			[ [], $context ],
			'2.14',
            'gravityview_blocklist_field_types'
        );

		/**
		 * Modify the types of fields that shouldn't be shown in a View.
		 *
		 * @since 2.9
		 *
		 * @param array  $blocklist_field_types Array of field types which are not proper to be shown for the $context.
		 * @param string $context               View context ('single', 'directory', or 'edit').
		 */
		$blocklist_field_types = apply_filters( 'gravityview_blocklist_field_types', $blocklist_field_types, $context );

		if ( ! is_array( $blocklist_field_types ) ) {
			gravityview()->log->error(
                '$blocklist_field_types is not an array',
                [ 'data' => print_r( $blocklist_field_types, true ) ] // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- print_r() with return=true formats the value for the debug log only.
            );

			$blocklist_field_types = [];
		}

		$fields = $this->get_available_fields( $form, $context );

		$output = '';

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $id => $details ) {
				if ( in_array( $details['type'], (array) $blocklist_field_types ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- Blocklist entries come from a public filter and may be mixed types; loose comparison preserves long-standing behavior.
					continue;
				}

				if ( 'edit' === $context && ! empty( $details['parent'] ) ) {
					continue;
				}

				$output .= new GravityView_Admin_View_Field( $details['label'], $id, $details, [], $form_id, $form );
			} // End foreach
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is concatenated GravityView_Admin_View_Field objects, which render escaped admin HTML via __toString().

		if ( 'edit' === $context ) {
			return;
		}

		$this->render_additional_fields( $form_id, $context );
	}

	/**
	 * Render html for displaying additional fields based on a Form ID
	 *
	 * @param int|string $form_id Gravity Forms form ID. Default: 0.
	 * @param string     $context (default: 'single').
	 *
	 * @return void
	 */
	public function render_additional_fields( $form_id = 0, $context = 'single' ) {
		unset( $context ); // Reserved for future use; kept in signature for API stability.
		$form = GVCommon::get_form_or_form_template( $form_id );

		$additional_fields = [
			[
				'label_text'    => __( 'Add All Form Fields', 'gk-gravityview' ),
				'desc'          => __( 'Insert all the form fields at once.', 'gk-gravityview' ),
				'field_id'      => 'all-fields',
				'label_type'    => 'field',
				'input_type'    => null,
				'field_options' => null,
				'settings_html' => null,
				'icon'          => 'dashicons-plus-alt',
			],
		];

		/**
		 * Modify the non-standard fields shown at the bottom of the field picker.
		 *
		 * @since 1.0.7-beta
		 *
		 * @param array $additional_fields Associative array of field arrays, with `label_text`, `desc`, `field_id`, `label_type`, `input_type`, `field_options`, and `settings_html` keys.
		 */
		$additional_fields = apply_filters( 'gravityview_additional_fields', $additional_fields );

		foreach ( (array) $additional_fields as $item ) {
			$item = wp_parse_args(
				$item,
				[
					'label_text'    => null,
					'field_id'      => null,
					'label_type'    => null,
					'input_type'    => null,
					'field_options' => null,
					'settings_html' => null,
					'icon'          => null,
				]
			);

			// Older saved configurations store the rendered options under `field_options`.
			if ( ! empty( $item['field_options'] ) ) {
				$item['settings_html'] = $item['field_options'];
			}

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- GravityView_Admin_View_Field renders escaped admin HTML via __toString().
			echo new GravityView_Admin_View_Field(
                $item['label_text'],
				$item['field_id'],
				$item,
				[],
				$form_id,
                $form
            );
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Retrieve the default fields id, label and type
	 *
	 * @param string|array $form form_ID or form object.
	 * @param string       $zone Either 'single', 'directory', 'header', 'footer'.
	 *
	 * @return array
	 */
	public function get_entry_default_fields( $form, $zone ) {
		$entry_default_fields = [];

		if ( in_array( $zone, [ 'directory', 'single' ], true ) ) {
			$meta_fields = GravityView_Fields::get_all( [ 'meta', 'gravityview', 'add-ons' ], $zone );

			$entry_default_fields = [];

			foreach ( $meta_fields as $meta_field ) {
				$entry_default_fields += $meta_field->as_array();
			}
		}

		/**
		 * Modify the default fields for each zone and context.
		 *
		 * @since 1.0-beta
		 *
		 * @param array        $entry_default_fields Array of fields shown by default.
		 * @param string|array $form                 Form ID or form object.
		 * @param string       $zone                 Either 'single', 'directory', 'header', 'footer'.
		 */
		return apply_filters( 'gravityview_entry_default_fields', $entry_default_fields, $form, $zone );
	}

	/**
	 * Calculate the available fields
	 *
	 * @param string|array $form form_ID or form object.
	 * @param string       $zone Either 'single', 'directory', 'header', 'footer'.
	 *
	 * @return array         fields
	 */
	public function get_available_fields( $form = '', $zone = null ) {
		if ( empty( $form ) ) {
			gravityview()->log->error( '$form is empty' );

			return [];
		}

		$fields = gravityview_get_form_fields( $form, true );

		if ( ! is_array( $form ) ) {
			$meta_fields = gravityview_get_entry_meta( $form );
		} else {
			$meta_fields = [];
		}

		$gv_fields = GravityView_Fields::get_all( '', $zone );

		$featured_fields = wp_list_filter( $gv_fields, [ 'group' => 'featured' ] );

		/** @var GravityView_Field $featured_field */
		foreach ( $featured_fields as &$featured_field ) {
			$_as_array      = $featured_field->as_array();
			$featured_field = reset( $_as_array );
		}

		$default_fields = $this->get_entry_default_fields( $form, $zone );

		// Union, not array_merge: the field keys must survive.
		$fields = $featured_fields + $fields + $meta_fields + $default_fields;

		foreach ( $fields as &$field ) {
			foreach ( $gv_fields as $gv_field ) {
				if ( \GV\Utils::get( $field, 'type' ) === $gv_field->name ) {
					$field['icon'] = $gv_field->get_icon();
				}
			}
		}

		/**
		 * Modify the available fields that can be used in a View.
		 *
		 * @since 2.10
		 *
		 * @param array        $fields The fields.
		 * @param string|array $form   Form ID or form object.
		 * @param string       $zone   Either 'single', 'directory', 'header', 'footer'.
		 */
		return apply_filters( 'gravityview/admin/available_fields', $fields, $form, $zone );
	}

	/**
	 * Render html for displaying available widgets
	 *
	 * @return void
	 */
	public function render_available_widgets() {
		$widgets = \GV\Widget::registered();

		if ( empty( $widgets ) ) {
			return;
		}

		foreach ( $widgets as $id => $details ) {
			echo new GravityView_Admin_View_Widget( $details['label'], $id, $details ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- GravityView_Admin_View_Widget renders escaped admin HTML via __toString().
		}
	}

	/**
	 * Get the list of registered widgets. Each item is used to instantiate a GravityView_Admin_View_Widget object
	 *
	 * @since      1.13.1
	 * @return array
	 * @deprecated 3.0.0 \GV\Widget::registered()
	 */
	public function get_registered_widgets() {
		_deprecated_function( 'GravityView_Admin_Views::get_registered_widgets', '2.0', '\GV\Widget::registered()' );

		return \GV\Widget::registered();
	}

	/**
	 * Generic function to render rows and columns of active areas for widgets & fields
	 *
	 * @param string $template_id The current slug of the selected View template.
	 * @param string $type        Either 'widget' or 'field'.
	 * @param string $zone        Either 'single', 'directory', 'edit', 'header', 'footer'.
	 * @param array  $rows        The layout structure: rows, columns and areas.
	 * @param array  $values      Saved objects.
	 *
	 * @return void
	 */
	public function render_active_areas( $template_id, $type, $zone, $rows, $values ) {
		global $post;

		switch ( $type ) {
			case 'widget':
				$button_label = __( 'Add Widget', 'gk-gravityview' );
				break;
			case 'search':
				$button_label = __( 'Add Search Field', 'gk-gravityview' );
				break;
			default:
				$button_label = __( 'Add Field', 'gk-gravityview' );
		}

		$row_settings = [];

		if ( ! empty( $post->ID ) ) {
			$row_settings = get_post_meta( $post->ID, '_gravityview_row_settings', true );
		}

		if ( ! is_array( $row_settings ) ) {
			$row_settings = [];
		}

		$is_dynamic = $this->is_dynamic( $template_id, $type, $zone );

		/**
		 * @internal Don't rely on this filter! This is for internal use and may change.
		 *
		 * @since    2.8.1
		 *
		 * @param string $button_label Text for button: "Add Widget" or "Add Field"
		 * @param array  $atts         {
		 *
		 * @type string  $type         'widget' or 'field'
		 * @type string  $template_id  The current slug of the selected View template
		 * @type string  $zone         Where is this button being shown? Either 'single', 'directory', 'edit', 'header', 'footer'
		 *                             }
		 */
		$button_label = apply_filters(
			'gravityview/admin/add_button_label',
			$button_label,
			[
				'type'        => $type,
				'template_id' => $template_id,
				'zone'        => $zone,
			]
		);

		$available_items = [];

		$view    = \GV\View::from_post( $post );
		$form_id = null;
		$form    = false;

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Values are read only to render the View editor; the AJAX entry points verify the `gravityview_ajaxviews` nonce before this runs.
		if ( ! empty( $values ) && ( ! empty( $post->ID ) || ! empty( $_POST['template_id'] ) || ! empty( $_POST['form_id'] ) ) ) {
			if ( ! empty( $_POST['form_id'] ) ) {
				$form_id = (int) \GV\Utils::_POST( 'form_id', 0 );
				$form    = gravityview_get_form( $form_id );
			} elseif ( ! empty( $_POST['template_id'] ) ) {
				$form_id = esc_attr( $_POST['template_id'] );
				$form    = GravityView_Ajax::pre_get_form_fields( $_POST['template_id'] );
			} else {
				$form_id = gravityview_get_form_id( $post->ID );
				$form    = gravityview_get_form( $form_id );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			if ( 'field' === $type ) {
				$available_items[ $form_id ] = $this->get_available_fields( $form, $zone );

				if ( ! empty( $post->ID ) ) {
					$joined_forms = gravityview_get_joined_forms( $post->ID );

					foreach ( $joined_forms as $joined_form ) {
						$available_items[ $joined_form->ID ] = $this->get_available_fields( $joined_form->ID, $zone );
					}
				}
			} else {
				$available_items[ $form_id ] = \GV\Widget::registered();
			}
		}

		foreach ( $rows as $row ) :
			$row_uid     = Grid::extract_row_uid( $row );
			$row_key     = $zone . '::' . $row_uid;
			$row_setting = $row_settings[ $row_key ] ?? [];

			printf(
				'<div class="gv-grid-row %s" data-context="%s">',
				$is_dynamic ? 'is-sortable' : '',
				esc_attr( $zone )
			);

			/**
			 * Triggers before a row is rendered in the View editor.
			 *
			 * @since 2.31.0
			 *
			 * @param bool   $is_dynamic  Whether the area is dynamic.
			 * @param string $template_id The template ID.
			 * @param string $type        The object type (widget or field).
			 * @param string $zone        The render zone.
			 */
			do_action( 'gk/gravityview/admin-views/row/before', $is_dynamic, $template_id, $type, $zone );

			foreach ( $row as $col => $areas ) :
				$column = ( '2-2' === $col ) ? '1-2' : $col;
				?>

                <div class="gv-grid-col-<?php echo esc_attr( $column ); ?>">
					<?php foreach ( $areas as $area ) : ?>

                        <div class="gv-droppable-area"
                            data-areaid="<?php echo esc_attr( $zone . '_' . $area['areaid'] ); ?>"
                            data-context="<?php echo esc_attr( $zone ); ?>"
                            data-templateid="<?php echo esc_attr( $template_id ); ?>">
                            <div class="gv-droppable-area-header">
								<div class="gv-droppable-area-header-title">
									<?php
									printf(
										'<strong%s>%s</strong>',
										'widget' === $type ? ' class="screen-reader-text"' : '',
										esc_html( $area['title'] )
									);
									?>

									<?php if ( ! empty( $area['subtitle'] ) ) { ?>
										<span class="gv-droppable-area-subtitle">
											<span class="gf_tooltip gv_tooltip tooltip" title="<?php echo esc_attr( $area['subtitle'] ); ?>"></span>
										</span>
									<?php } ?>
								</div>
								<div class="gv-droppable-area-header-actions">
									<?php
									/**
									 * Allows adding custom actions inside the View editor's droppable areas.
									 *
									 * @since 2.44
									 *
									 * @param array  $area        The current area configuration.
									 * @param string $type        The type of area (e.g., 'widget', 'field').
									 * @param array  $values      The values in the area.
									 * @param bool   $is_dynamic  Whether the area is dynamic.
									 * @param string $template_id The ID of the template being used.
									 * @param string $zone        The zone where the area is located.
									 */
									do_action( 'gk/gravityview/admin-views/area/actions', $area, $type, $values, $is_dynamic, $template_id, $zone );
									?>
								</div>
                            </div>
                            <div class="active-drop-container active-drop-container-<?php echo esc_attr( $type ); ?>">
                                <div class="active-drop active-drop-<?php echo esc_attr( $type ); ?>"
                                    data-areaid="<?php echo esc_attr( $zone . '_' . $area['areaid'] ); ?>">
									<?php
									if ( ! empty( $values[ $zone . '_' . $area['areaid'] ] ) ) {
										foreach ( $values[ $zone . '_' . $area['areaid'] ] as $uniqid => $field ) {
											$field['add_button_label'] = $button_label;

											// Maybe has a form ID. Kept per-iteration: assigning back to $form_id would
											// leak this slot's form into every later slot that has none of its own.
											$field_form_id = empty( $field['form_id'] ) ? $form_id : $field['form_id'];

											$input_type = null;

											if ( $field_form_id ) {
												$original_item = isset( $available_items[ $field_form_id ] [ $field['id'] ] ) ? $available_items[ $field_form_id ] [ $field['id'] ] : false;
											} else {
												$original_item = isset( $available_items[ $field['id'] ] ) ? $available_items[ $field['id'] ] : false;
											}

											if ( ! $original_item ) {
												global $pagenow;
												if ( 'post-new.php' !== $pagenow ) {
													gravityview()->log->error(
														'An item was not available when rendering the output; maybe it was added by a plugin that is now de-activated.',
														[
															' data' => [
																'available_items' => $available_items,
																'field'           => $field,
															],
														]
													);
												}

												$original_item = $field;
											}

											$input_type = isset( $original_item['type'] ) ? $original_item['type'] : null;

											$field_options = GravityView_Render_Settings::render_field_options(
                                                $field_form_id,
												$type,
												$template_id,
												$field['id'],
												$original_item['label'],
												$zone . '_' . $area['areaid'],
												$input_type,
												$uniqid,
												$field,
												$zone,
                                                $original_item
                                            );

											$item = [
												'input_type'    => $input_type,
												'settings_html' => $field_options,
												'label_type'    => $type,
											];

											if ( $original_item ) {
												$item = wp_parse_args( $item, $original_item );
											}

											// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- GravityView_Admin_View_Widget, Search_Field, and GravityView_Admin_View_Field render escaped admin HTML via __toString().
											switch ( $type ) {
												case 'widget':
													echo new GravityView_Admin_View_Widget(
														$item['label'],
														$field['id'],
														$item,
														$field
													);
													break;
												case 'search':
													$item['input_type'] = $field['input_type'] ?? '';
													echo( Search_Field::from_configuration( $item ) ?? '' );
													break;
												default:
													echo new GravityView_Admin_View_Field(
														$field['label'],
														$field['id'],
														$item,
														$field,
														$field_form_id,
														$form
													);
											}
											// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
										}
									} // End if zone is not empty

									?>
                                </div>
                                <div class="gv-droppable-area-action">
                                    <a href="#" class="gv-add-field button button-link button-hero" title=""
                                        data-title="<?php echo esc_attr( $button_label ); ?>"
                                        data-templateid="<?php echo esc_attr( $template_id ); ?>"
                                        data-objecttype="<?php echo esc_attr( $type ); ?>"
                                        data-areaid="<?php echo esc_attr( $zone . '_' . $area['areaid'] ); ?>"
                                        data-context="<?php echo esc_attr( $zone ); ?>"
                                        data-formid="<?php echo $view ? esc_attr( $view->form ? $view->form->ID : '' ) : ''; ?>"><?php echo '<span class="dashicons dashicons-plus-alt"></span>' . esc_html( $button_label ); ?></a>
                                </div>
                            </div>
                        </div>

					<?php endforeach; ?>
                </div>
				<?php
			endforeach;
			/**
			 * Triggers after a row is rendered in the View editor.
			 *
			 * @since 2.31.0
			 *
			 * @param bool   $is_dynamic  Whether the area is dynamic.
			 * @param View   $view        The View.
			 * @param string $template_id The template ID.
			 * @param string $type        The object type (widget or field).
			 * @param string $zone        The render zone.
			 */
			do_action( 'gk/gravityview/admin-views/row/after', $is_dynamic, $view, $template_id, $type, $zone );

			if ( $is_dynamic ) {
				$this->render_row_settings_panel( $zone, $row_uid, $row_setting );
			}

			echo '</div>';
		endforeach;
	}

	/**
	 * Renders the row settings dialog (CSS class and HTML ID inputs).
	 *
	 * Uses the same `.gv-dialog-options` modal pattern as field and widget settings.
	 *
	 * @since 2.54.0
	 *
	 * @param string $zone        The render zone.
	 * @param string $row_uid     The row UID.
	 * @param array  $row_setting The saved row settings.
	 */
	private function render_row_settings_panel( string $zone, string $row_uid, array $row_setting ): void {
		$row_key     = $zone . '::' . $row_uid;
		$name_prefix = 'row_settings[' . esc_attr( $row_key ) . ']';

		$options = [
			'custom_class' => [
				'type'       => 'text',
				'label'      => __( 'Custom CSS Class', 'gk-gravityview' ),
				'desc'       => __( 'This class will be added to the row container. Use {row_index} for the row number.', 'gk-gravityview' ),
				'value'      => '',
				'merge_tags' => true,
				'class'      => 'widefat code',
			],
			'custom_id'    => [
				'type'       => 'text',
				'label'      => __( 'Custom HTML ID', 'gk-gravityview' ),
				'desc'       => __( 'This ID will be added to the row container. Use {row_index} for the row number.', 'gk-gravityview' ),
				'value'      => '',
				'merge_tags' => true,
				'class'      => 'widefat code',
			],
		];

		printf(
			'<div class="gv-dialog-options gv-row-settings-dialog" title="%s" data-row-key="%s">',
			esc_attr__( 'Row Settings', 'gk-gravityview' ),
			esc_attr( $row_key )
		);

		foreach ( $options as $key => $option ) {
			$value  = $row_setting[ $key ] ?? null;
			$output = GravityView_Render_Settings::render_field_option(
				$name_prefix . '[' . $key . ']',
				$option,
				$value
			);

			if ( ! empty( $output ) ) {
				printf( '<div class="gv-setting-container gv-setting-container-%s">%s</div>', esc_attr( $key ), $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $output is generated by GravityView_Render_Settings::render_field_option(), which escapes the setting markup.
			}
		}

		echo '</div>';
	}

	/**
	 * Renders the row actions.
	 *
	 * @since 2.31.0
	 *
	 * @param bool   $is_dynamic  Whether the rows are actionable.
	 * @param string $template_id The template ID.
	 * @param string $type        The object type (widget or field).
	 * @param string $zone        The render zone.
	 */
	public function render_actions(
		bool $is_dynamic,
		string $template_id,
		string $type,
		string $zone
	): void {
		if ( ! $is_dynamic ) {
			return;
		}

		echo '<div class="gv-grid-row-actions" role="toolbar" aria-orientation="horizontal" aria-label="' . esc_attr__( 'Row actions', 'gk-gravityview' ) . '">';
		$actions = '<div class="gv-grid-row-action gv-grid-row-handle" aria-hidden="true">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<rect x="8" y="4.99988" width="2" height="2" fill="currentColor"/>
					<rect x="8" y="10.9999" width="2" height="2" fill="currentColor"/>
					<rect x="8" y="16.9999" width="2" height="2" fill="currentColor"/>
					<rect x="14" y="4.99988" width="2" height="2" fill="currentColor"/>
					<rect x="14" y="10.9999" width="2" height="2" fill="currentColor"/>
					<rect x="14" y="16.9999" width="2" height="2" fill="currentColor"/>
				</svg>
			</div>
			<button type="button" class="gv-grid-row-action gv-grid-row-move-up" aria-label="' . esc_attr__( 'Move row up', 'gk-gravityview' ) . '" title="' . esc_attr__( 'Move row up', 'gk-gravityview' ) . '">
				<span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
			</button>
			<button type="button" class="gv-grid-row-action gv-grid-row-move-down" aria-label="' . esc_attr__( 'Move row down', 'gk-gravityview' ) . '" title="' . esc_attr__( 'Move row down', 'gk-gravityview' ) . '">
				<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
			</button>
			<button type="button" class="gv-grid-row-action gv-grid-row-settings-toggle" aria-haspopup="dialog" aria-label="' . esc_attr__( 'Row Settings', 'gk-gravityview' ) . '" title="' . esc_attr__( 'Row Settings', 'gk-gravityview' ) . '">
				<span class="dashicons dashicons-admin-generic" aria-hidden="true"></span>
			</button>
			<button type="button" class="gv-grid-row-action gv-grid-row-delete" aria-label="' . esc_attr__( 'Delete row', 'gk-gravityview' ) . '" title="' . esc_attr__( 'Delete row', 'gk-gravityview' ) . '" data-confirm="' . esc_attr__(
				'Are you sure you want to delete the entire row?',
				'gk-gravityview'
			) . '">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M6.33755 7.17057C6.23321 6.47927 6.76848 5.85714 7.46761 5.85714H16.5328C17.2319 5.85714 17.7672 6.47927 17.6629 7.17058L15.9809 18.3134C15.8966 18.8724 15.4162 19.2857 14.8509 19.2857H9.14955C8.58424 19.2857 8.10387 18.8724 8.01949 18.3134L6.33755 7.17057Z" stroke="currentColor" stroke-width="1.71429"/>
					<rect x="4" y="5" width="16" height="2" fill="currentColor"/>
					<path d="M14.2858 5C14.2858 5 13.2624 5 12.0001 5C10.7377 5 9.71436 5 9.71436 5C9.71436 3.73763 10.7377 2.71429 12.0001 2.71429C13.2624 2.71429 14.2858 3.73763 14.2858 5Z" fill="currentColor"/>
				</svg>
			</button>';

		/**
		 * Modifies the actions rendered in the View editor.
		 *
		 * @since 2.31.0
		 *
		 * @param string $actions     The HTML for the actions.
		 * @param string $template_id The template ID.
		 * @param string $type        The object type (widget or field).
		 * @param string $zone        The render zone.
		 */
		echo apply_filters( 'gk/gravityview/admin-views/rows-actions', $actions, $template_id, $type, $zone ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $actions is hardcoded SVG/markup with escaped labels; the filter exists for trusted extensions to modify editor controls.

		echo '</div>';
	}

	/**
	 * Render the widget active areas
	 *
	 * @param string $template_id The current slug of the selected View template.
	 * @param string $zone        Either 'header' or 'footer'.
	 * @param string $post_id     Current Post ID (view).
	 *
	 * @return string          html
	 */
	public function render_widgets_active_areas( $template_id = '', $zone = '', $post_id = '' ) {
		$default_widget_areas = \GV\Widget::get_default_widget_areas();

		$widgets = [];

		$header_top   = 'header_' . ( $default_widget_areas[0]['1-1'][0]['areaid'] ?? 'top' );
		$header_left  = 'header_' . ( $default_widget_areas[1]['1-2 left'][0]['areaid'] ?? 'left' );
		$header_right = 'header_' . ( $default_widget_areas[1]['1-2 right'][0]['areaid'] ?? 'right' );
		$footer_right = 'footer_' . ( $default_widget_areas[1]['1-2 right'][0]['areaid'] ?? 'right' );

		if ( ! empty( $post_id ) ) {
			if ( 'auto-draft' === get_post_status( $post_id ) ) {
				$widgets = [
					$header_top   => [
						Grid::uid() => [
							'id'    => 'search_bar',
							'label' => __( 'Search Bar', 'gk-gravityview' ),
						],
					],
					$header_left  => [
						Grid::uid() => [
							'id'    => 'page_info',
							'label' => __( 'Show Pagination Info', 'gk-gravityview' ),
						],
					],
					$header_right => [
						Grid::uid() => [
							'id'       => 'page_links',
							'label'    => __( 'Page Links', 'gk-gravityview' ),
							'show_all' => '0',
						],
					],
					$footer_right => [
						Grid::uid() => [
							'id'       => 'page_links',
							'label'    => __( 'Page Links', 'gk-gravityview' ),
							'show_all' => '0',
						],
					],
				];

				/**
				 * Modify the default widgets for new Views.
				 *
				 * @since 2.10
				 *
				 * @param array  $widgets     A Widget configuration array.
				 * @param string $template_id The current slug of the selected View template.
				 * @param string $zone        The widget zone that's being requested.
				 * @param int    $post_id     The auto-draft post ID.
				 */
				$widgets = (array) apply_filters( 'gravityview/view/widgets/default', $widgets, $template_id, $zone, $post_id );
			} else {
				$widgets         = (array) gravityview_get_directory_widgets( $post_id );
				$collection      = Widget_Collection::from_configuration( $widgets );
				$collection_rows = Grid::get_rows_from_collection( $collection, $zone );

				if ( $collection_rows ) {
					$default_widget_areas = $this->add_missing_default_widget_rows( $collection_rows, $default_widget_areas );
				}
			}
		}

		ob_start();
		?>

        <div class="gv-grid gv-grid-pad gv-grid-border" id="directory-<?php echo esc_attr( $zone ); ?>-widgets" data-grid-context="<?php echo esc_attr( $zone ); ?>" data-grid-connect="widget">
			<?php
			$type       = 'widget';
			$is_dynamic = $this->is_dynamic( $template_id, $type, $zone );

			echo '<div class="gv-grid-rows-container">';
			$this->render_active_areas( $template_id, $type, $zone, $default_widget_areas, $widgets );
			echo '</div>';
			/**
			 * Allows additional content after the zone was rendered.
			 *
			 * @since 2.10
			 *
			 * @param string $template_id Template ID.
			 * @param string $type        The zone type (field or widget).
			 * @param string $context     Current View context: `directory`, `single`, or `edit` (default: 'single').
			 * @param bool   $is_dynamic  Whether the zone is dynamic.
			 */
			do_action( 'gk/gravityview/admin-views/view/after-zone', $template_id, $type, $zone, $is_dynamic );
			?>
        </div>

		<?php
		$output = ob_get_clean();

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Buffered editor markup generated above; dynamic values are escaped where rendered.

		return $output;
	}

	/**
	 * Re-adds default row layouts that are missing from a widget zone.
	 *
	 * A widget-area row exists in the saved configuration only while a widget
	 * references it, so removing a row's last widget removes the row itself on
	 * save; without its default rows the zone offers no way to add one back.
	 * The full-width default goes above the saved rows, matching the default
	 * scaffold; saved rows keep their order.
	 *
	 * @since 3.1.0
	 *
	 * @param array $rows         Rows derived from the saved configuration.
	 * @param array $default_rows The zone's default rows.
	 *
	 * @return array The rows with missing default layouts added.
	 */
	private function add_missing_default_widget_rows( array $rows, array $default_rows ): array {
		$signature = static function ( array $row ): string {
			$columns = array_keys( $row );
			sort( $columns );

			return implode( '|', $columns );
		};

		$existing = array_map( $signature, $rows );

		foreach ( $default_rows as $i => $default_row ) {
			if ( in_array( $signature( $default_row ), $existing, true ) ) {
				continue;
			}

			if ( 0 === $i ) {
				array_unshift( $rows, $default_row );
			} else {
				$rows[] = $default_row;
			}
		}

		return $rows;
	}

	/**
	 * Renders "Add Field" tooltips
	 *
	 * @since 2.0.11
	 *
	 * @param string $context  "directory", "single", "edit" or "search".
	 * @param array  $form_ids (default: array) Array of form IDs.
	 *
	 * @return void
	 */
	public function render_field_pickers( $context = 'directory', $form_ids = [] ) {
		global $post;

		if ( $post ) {
			$source_form_id = gravityview_get_form_id( $post->ID );
			if ( $source_form_id ) {
				$form_ids[] = $source_form_id;
			}

			$joined_forms = \GV\View::get_joined_forms( $post->ID );
			foreach ( $joined_forms as $joined_form ) {
				$form_ids[] = $joined_form->ID;
			}
		}
		foreach ( array_unique( $form_ids ) as $form_id ) {
			$filter_field_id = sprintf( 'gv-field-filter-%s-%s', $context, $form_id );

			?>
            <div id="<?php echo esc_html( $context ); ?>-available-fields-<?php echo esc_attr( $form_id ); ?>"
                class="hide-if-js gv-tooltip">
                <button class="close" role="button" aria-label="<?php esc_html_e( 'Close', 'gk-gravityview' ); ?>"><i
                            class="dashicons dashicons-dismiss"></i></button>

                <div class="gv-field-filter-form">
                    <label class="screen-reader-text"
                            for="<?php echo esc_html( $filter_field_id ); ?>">
                            <?php
							esc_html_e(
                                'Filter Fields:',
                                'gk-gravityview'
                            );
							?>
                            </label>
                    <input type="search" class="widefat gv-field-filter" aria-controls="<?php echo esc_attr( $filter_field_id ); ?>"
                            id="<?php echo esc_html( $filter_field_id ); ?>"
                            placeholder="<?php esc_html_e( 'Filter fields by name or label', 'gk-gravityview' ); ?>"/>
                    <div class="button-group">
                        <span role="button" class="button button-large gv-items-picker gv-items-picker--grid"
                                data-value="grid"><i class="dashicons dashicons-grid-view "></i></span>
                        <span role="button" class="button button-large gv-items-picker gv-items-picker--list active"
                                data-value="list"><i class="dashicons dashicons-list-view"></i></span>
                    </div>
                </div>

                <div id="available-fields-<?php echo esc_attr( $filter_field_id ); ?>" aria-live="polite" role="listbox" class="gv-items-picker-container">
					<?php do_action( 'gravityview_render_available_fields', $form_id, $context ); ?>
                </div>

                <div class="gv-no-results hidden description">
					<?php esc_html_e( 'No fields were found matching the search.', 'gk-gravityview' ); ?>
				</div>
            </div>
			<?php
		}
	}

	/**
	 * Render the Template Active Areas and configured active fields for a given template id and post id
	 *
	 * @param string     $template_id (default: '') Template ID, like `default_list`, `default_table`,
	 *                                `preset_business_data`, etc. {@see GravityView_Template::__construct()}.
	 * @param string     $context     (default: 'single') Context of the template. `single` or `directory` (`edit` not
	 *                                implemented but valid).
	 * @param string     $post_id     (default: '') ID of the View CPT. Used to get the fields for the View.
	 * @param bool       $should_echo (default: false) Whether to echo the output or return it. Default: `false`.
	 * @param int        $form_id     (default: 0) Main form ID for the View. Used to set default fields for a new View.
	 * @param array|null $values  (default: null) Field configuration to render instead of the saved configuration.
	 *                            Used when switching templates to render the migrated configuration.
	 *
	 * @since 3.0.0 Added the `$values` parameter.
	 *
	 * @return string HTML of the active areas
	 */
	public function render_directory_active_areas(
		$template_id = '',
		$context = 'single',
		$post_id = 0,
		$should_echo = false,
		$form_id = 0,
		$values = null
	) {
		if ( empty( $template_id ) ) {
			gravityview()->log->debug(
                '[render_directory_active_areas] {template_id} is empty',
                [ 'template_id' => $template_id ]
            );

			return '';
		}

		/**
		 * Filters the active areas for the View template.
		 *
		 * @since 1.0-beta
		 *
		 * @see GravityView_Template::assign_active_areas()
		 *
		 * @param array  $template_areas Empty array, to be filled in by the template class.
		 * @param string $template_id    Template ID, like `default_list`, `default_table`, `preset_business_data`, etc. {@see GravityView_Template::__construct()}
		 * @param string $context        Current View context: `directory`, `single`, `edit`, or `search` (default: 'single').
		 */
		$template_areas = apply_filters( 'gravityview_template_active_areas', [], $template_id, $context );

		if ( empty( $template_areas ) ) {
			gravityview()->log->error(
                '[render_directory_active_areas] No areas defined. Maybe template {template_id} is disabled.',
                [ 'data' => $template_id ]
            );

			$output  = '<div>';
			$output .= '<h2 class="description" style="font-size: 16px; margin:0">' . sprintf(
                /* translators: %s: The name of the View type that is disabled. */
                esc_html__(
                    'This View is configured using the %s View type, which is disabled.',
                    'gk-gravityview'
                ),
                '<em>' . esc_html( $template_id ) . '</em>'
            ) . '</h2>';
			$output .= '<p class="description" style="font-size: 14px; margin:0 0 1em 0;padding:0">' . esc_html__(
                'The data is not lost; re-activate the associated plugin and the configuration will re-appear.',
                'gk-gravityview'
            ) . '</p>';
			$output .= '</div>';
		} else {
			/**
			 * Modifies the template area's before rendering.
			 *
			 * @since 2.31.0
			 *
			 * @param array  $template_areas The template areas.
			 * @param string $template_id    Template ID.
			 * @param string $context        Current View context: `directory`, `single`, or `edit` (default: 'single').
			 * @param array  $fields         The fields for the View.
			 */

			$is_search = stripos( $context, 'search' ) === 0;

			if ( $is_search ) {
				$fields = gravityview_get_directory_search( $post_id );
			} elseif ( null !== $values ) {
				$fields = (array) $values;
			} else {
				$fields = (array) gravityview_get_directory_fields( $post_id, true, $form_id );
			}

			if ( $is_search ) {
				$collection = Field_Collection::from_configuration( gravityview_get_directory_search( $post_id ) );
				$rows       = Grid::get_rows_from_collection( $collection, $context );
				if ( $rows ) {
					$template_areas = $rows;
				}
			}

			$template_areas = (array) apply_filters(
				'gk/gravityview/admin-views/view/template/active-areas',
				$template_areas,
				$template_id,
				$context,
				$fields
			);

			$type       = $is_search ? 'search' : 'field';
			$is_dynamic = $this->is_dynamic( $template_id, $type, $context );

			ob_start();
			echo '<div class="gv-grid-rows-container">';
			$this->render_active_areas( $template_id, $type, $context, $template_areas, $fields );
			echo '</div>';
			/**
			 * Allows additional content after the zone was rendered.
			 *
			 * @since 2.10
			 *
			 * @param string $template_id Template ID.
			 * @param string $type        The zone type (field or widget).
			 * @param string $context     Current View context: `directory`, `single`, or `edit` (default: 'single').
			 * @param bool   $is_dynamic  Whether the zone is dynamic.
			 */
			do_action( 'gk/gravityview/admin-views/view/after-zone', $template_id, $type, $context, $is_dynamic );

			$output = ob_get_clean();
		}

		if ( $should_echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Editor markup generated above; dynamic values are escaped where rendered.
		}

		return $output;
	}

	/**
	 * Returns an "add row" button for a template zone.
	 *
	 * @since 2.31.0
	 *
	 * @param string $template_id The template ID.
	 * @param string $type        The object type (widget or field).
	 * @param string $zone        The zone ID.
	 * @param bool   $is_dynamic  Whether the zone is dynamic.
	 */
	public function render_add_row( string $template_id, string $type, string $zone, bool $is_dynamic ): void {
		if ( ! $is_dynamic ) {
			return;
		}

		$controls_id = 'gv-grid-options-' . wp_generate_password( 12, false, false );
		$button      = <<<'HTML'
<button
	type="button"
	class="gv-add-row"
	data-add-row="%s"
	data-template-id="%s"
	data-type="%s"
	data-row-type="%s"
>
	<span class="screen-reader-text">%s</span>
	%s
</button>
HTML;
		?>
        <div id="<?php echo esc_attr( $controls_id ); ?>" class="gv-grid-add-row">
            <div class="gv-grid-row-layouts-wrapper" inert>
                <div class="gv-grid-row-layouts">
                    <div class="gv-grid-row-title"><?php esc_html_e( 'Select your layout', 'gk-gravityview' ); ?></div>
                    <div class="gv-grid-row-types">
						<?php
						foreach ( array_keys( Grid::get_row_types() ) as $key ) {
							$columns = explode( '/', $key );
							$icon    = '<div class="gv-grid-add-row-icon">';
							foreach ( $columns as $column ) {
								$icon .= sprintf(
									'<div class="gv-grid-add-row-icon-column-%s">%s</div>',
									esc_attr( $column ),
									esc_html( $column ),
								);
							}
							$icon .= '</div>';
							// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $button is a hardcoded HTML template, $type is an internal object-type slug, and $icon is built above from escaped column values.
							printf(
								$button,
								esc_attr( $zone ),
								esc_attr( $template_id ),
								$type,
								esc_attr( $key ),
								esc_attr(
									str_replace(
										'[type]',
										$key,
										esc_html__( 'Add [type] row', 'gk-gravityview' ),
									)
								),
								$icon
							);
							// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
                    </div>
                </div>
            </div>
            <div class="gv-grid-row-button">
                <button aria-haspopup="true" aria-controls="<?php echo esc_attr( $controls_id ); ?>" aria-expanded="false"
                        type="button" class="gv-add-field button button-link button-hero gv-toggle">
                    <span class="dashicons dashicons-plus-alt"></span> 
                    <?php
                    esc_html_e(
                        'Add Row',
                        'gk-gravityview'
                    );
					?>
                </button>
            </div>
        </div>
		<?php
	}

	/**
	 * Returns whether the widgets should be dynamic; based on the plugin setting.
	 *
	 * @since 2.31.0
	 *
	 * @param bool   $is_dynamic Whether the zone is dynamic.
	 * @param string $_          The template ID (unused).
	 * @param string $type       The object type (widget or field).
	 * @param string $zone       The render zone.
	 *
	 * @return bool Whether the widgets should be dynamic.
	 */
	public function set_dynamic_areas( bool $is_dynamic, string $_, string $type, string $zone ): bool {
		unset( $_ ); // Template ID is required by filter signature; not used here.
		if ( strpos( $zone, 'search' ) === 0 ) {
			return true;
		}

		if ( 'widget' !== $type || $is_dynamic ) {
			return $is_dynamic;
		}

		return Plugin::get()->settings->get( 'use_dynamic_widgets', false );
	}

	/**
	 * Set the default fields for new Views.
	 *
	 * @since    2.17
	 * @since    $ver$ Only applies when no fields are configured yet, keys the
	 *           defaults to the selected template's zones, and delegates the
	 *           generation to {@see PresetFieldGenerator}.
	 *
	 * @param array    $fields  Multi-array of fields with first level being the field zones.
	 * @param \GV\View $view    The View the fields are being pulled for. Used to resolve the saved template.
	 * @param int      $form_id The form ID.
	 *
	 * @return array
	 * @internal Do not use this method directly. Use the `gravityview/view/configuration/fields` filter instead.
	 */
	public function set_default_view_fields( $fields = [], $view = null, $form_id = 0 ) {
		if ( empty( $form_id ) ) {
			return $fields;
		}

		if ( ! empty( array_filter( (array) $fields ) ) ) {
			return $fields;
		}

		// A saved-but-empty configuration was blanked on purpose: only seed
		// Views that never had a configuration saved at all.
		$view_id                 = $view instanceof View && $view->ID ? (int) $view->ID : 0;
		$has_saved_configuration = $view_id && metadata_exists( 'post', $view_id, '_gravityview_directory_fields' );

		if ( $has_saved_configuration ) {
			return $fields;
		}

		$template_id = (string) \GV\Utils::_POST( 'template_id', '' );

		if ( '' === $template_id && $view instanceof View && $view->ID ) {
			$template_id = (string) gravityview_get_template_id( $view->ID );
		}

		$defaults = ( new PresetFieldGenerator( (int) $form_id, $template_id ) )->generate();

		return $defaults ? $defaults : $fields;
	}

	/**
	 * Enqueue scripts and styles at Views editor
	 *
	 * @param mixed $hook The current admin page hook suffix.
	 *
	 * @return void
	 */
	public static function add_scripts_and_styles( $hook ) {
		global $pagenow;

		$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : Plugin::$version;

		$script_debug    = Assets::min();
		$is_widgets_page = ( 'widgets.php' === $pagenow );

		if ( gravityview()->plugin->is_GF_25() && gravityview()->request->is_admin( '', 'single' ) ) {
			wp_dequeue_script( 'gform_tooltip_init' );
			wp_dequeue_style( 'gform_tooltip' );
			wp_enqueue_style(
                'gravityview_gf_tooltip',
				Assets::url( 'css/gf_tooltip.css' ),
				[],
                \GV\Plugin::$version
            );
			wp_enqueue_script(
                'gravityview_gf_tooltip',
				Assets::url( 'js/gf_tooltip' . $script_debug . '.js' ),
				[],
                \GV\Plugin::$version,
				false
            );
		}

		wp_enqueue_style(
            'gravityview_global',
			Assets::url( 'css/admin-global.css' ),
			[],
            \GV\Plugin::$version
        );

		$style_modified_time = filemtime( Assets::path( 'css/admin-views.css' ) );
		$style_modified_time = ! $style_modified_time ? \GV\Plugin::$version : $style_modified_time;

		wp_register_style(
            'gravityview_views_styles',
			Assets::url( 'css/admin-views.css' ),
			[ 'dashicons', 'wp-jquery-ui-dialog' ],
            $style_modified_time
        );

		wp_register_script(
            'gravityview-jquery-cookie',
			Assets::url( 'lib/jquery.cookie/jquery.cookie.min.js' ),
			[ 'jquery' ],
			$version,
            true
        );
		wp_enqueue_script(
			'gravityview-shortcode',
			Assets::url( 'js/admin-shortcode' . $script_debug . '.js' ),
			[
				'jquery',
				'clipboard',
			],
			$version,
			true
		);

		if ( 'form_list' === GFForms::get_page() ) {
			wp_enqueue_style( 'gravityview_views_styles' );
			return;
		}

		if ( ! gravityview()->request->is_admin( $hook, 'single' ) && ! $is_widgets_page ) {
			return;
		}

		wp_enqueue_code_editor( [ 'type' => 'text/html' ] );

		// Provides the calendar popover for date fields.
		QueryFilters::enqueue_date_picker( [ 'variable_name' => 'gvAdminDatePicker' ] );

		wp_enqueue_script( 'gravityview_tom_select', Assets::url( 'lib/tom-select/tom-select.complete.min.js' ), [], $version, false );
		wp_enqueue_style( 'gravityview_tom_select', Assets::url( 'lib/tom-select/tom-select.min.css' ), [], $version );

		wp_enqueue_script(
			'gravityview_views_scripts',
			Assets::url( 'js/admin-views' . $script_debug . '.js' ),
			[
				'jquery-ui-tabs',
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-sortable',
				'jquery-ui-tooltip',
				'jquery-ui-dialog',
				'gravityview-jquery-cookie',
				'gk-query-filters-pickers',
				'underscore',
				'clipboard',
				'gravityview_tom_select',
			],
			$version,
			false
		);
		wp_enqueue_script(
            'gravityview_view_dropdown',
			Assets::url( 'js/admin-view-dropdown' . $script_debug . '.js' ),
			[ 'jquery' ],
            $version,
			false
        );
		wp_enqueue_script(
            'gravityview_grid',
			Assets::url( 'js/admin-grid' . $script_debug . '.js' ),
			[ 'jquery', 'wp-a11y' ],
            $version,
			false
        );

		// The Styles tab pickers mount over the PHP-rendered controls and write
		// back to them, so the form save and dependent-field gating are unchanged.
		wp_enqueue_script(
			'gravityview_theme_picker',
			Assets::url( 'admin-theme-picker/theme-picker.js' ),
			[ 'react', 'react-dom', 'wp-i18n' ],
			$version,
			true
		);

		wp_localize_script(
			'gravityview_views_scripts',
			'gvGlobals',
			[
				'cookiepath'                            => COOKIEPATH,
				'admin_cookiepath'                      => ADMIN_COOKIE_PATH,
				'passed_form_id'                        => (bool) \GV\Utils::_GET( 'form_id' ),
				'has_merge_tag_listener'                => (bool) version_compare( GFForms::$version, '2.6.4', '>=' ),
				'nonce'                                 => wp_create_nonce( 'gravityview_ajaxviews' ),
				'label_viewname'                        => __( 'Enter View name here', 'gk-gravityview' ),
				'label_reorder_search_fields'           => __( 'Reorder Search Fields', 'gk-gravityview' ),
				'label_add_search_field'                => __( 'Add Search Field', 'gk-gravityview' ),
				'label_remove_search_field'             => __( 'Remove Search Field', 'gk-gravityview' ),
				'label_remove'                          => __( 'Remove', 'gk-gravityview' ),
				'label_close'                           => __( 'Close', 'gk-gravityview' ),
				'label_cancel'                          => __( 'Cancel', 'gk-gravityview' ),
				'label_continue'                        => __( 'Continue', 'gk-gravityview' ),
				'label_ok'                              => __( 'Ok', 'gk-gravityview' ),
				'label_bulk_actions_selection_checkbox' => __( 'Bulk Actions Selection Checkbox', 'gk-gravityview' ),
				'label_bulk_actions_configure'          => __( 'Configure Bulk Actions', 'gk-gravityview' ),
				'label_bulk_actions_ghost_summary'      => __( 'Configured in the Bulk Actions widget', 'gk-gravityview' ),
				'label_publisherror'                    => __(
                    'Error while creating the View for you. Check the settings or contact GravityView support.',
                    'gk-gravityview'
                ),
				'loading_text'                          => esc_html__( 'Loading&hellip;', 'gk-gravityview' ),
				'loading_error'                         => esc_html__(
                    'There was an error loading dynamic content.',
                    'gk-gravityview'
                ),
				'field_loaderror'                       => __(
                    'Error while adding the field. Please try again or contact GravityView support.',
                    'gk-gravityview'
                ),
				'remove_all_fields'                     => __(
                    'Would you like to remove all fields in this zone?',
                    'gk-gravityview'
                ),
				'discard_unsaved_changes'               => __(
                    'You have unsaved changes. Continuing will discard them. Are you sure you want to proceed?',
                    'gk-gravityview'
                ),
				'label_duplicate_row_id'                => __( 'This ID is already used by another row.', 'gk-gravityview' ),
				'row_moved_to_position'                 => __( 'Row moved to position %1$d of %2$d.', 'gk-gravityview' ),
				'row_already_at_top'                    => __( 'Row is already at the top.', 'gk-gravityview' ),
				'row_already_at_bottom'                 => __( 'Row is already at the bottom.', 'gk-gravityview' ),
				'field_moved_to_position'               => __( 'Field moved to position %1$d of %2$d.', 'gk-gravityview' ),
				'field_already_at_top'                  => __( 'Field is already at the top.', 'gk-gravityview' ),
				'field_already_at_bottom'               => __( 'Field is already at the bottom.', 'gk-gravityview' ),
				'field_moved_to_area'                   => __( 'Field moved to area %1$d of %2$d.', 'gk-gravityview' ),
				'label_move_field_prev'                 => __( 'Move field to the previous column', 'gk-gravityview' ),
				'label_move_field_next'                 => __( 'Move field to the next column', 'gk-gravityview' ),
				/* translators: %s: the name of the area the field will be moved to. */
				'label_move_field_to_area'              => __( 'Move field to %s Area', 'gk-gravityview' ),
				'config_load_error'                     => __( 'The View layout could not be loaded. Please reload the page without saving to avoid losing your configuration.', 'gk-gravityview' ),
				'foundation_licenses_router'            => array_merge(
					GravityKitFoundation::ajax_router()->get_ajax_params( 'licenses' ),
					[
						'ajaxRoute'                 => 'activate_product',
						'frontendFoundationVersion' => GravityKitFoundation::VERSION,
					]
				),
			]
		);

		self::enqueue_gravity_forms_scripts();

		wp_enqueue_style( 'gravityview_views_styles' );

		// Gravity Forms 2.5 and later output the merge tag hooks JavaScript themselves.
		if ( is_callable( [ 'GFCommon', 'output_hooks_javascript' ] ) ) {
			GFCommon::output_hooks_javascript();
		}
	}

	/**
	 * Enqueue Gravity Forms scripts, needed for Merge Tags
	 *
	 * @since 1.0.5-beta
	 *
	 * @return void
	 */
	public static function enqueue_gravity_forms_scripts() {
		GFForms::register_scripts();

		$scripts = [
			'sack',
			'gform_gravityforms',
			'gform_forms',
			'gform_form_admin',
			'jquery-ui-autocomplete',
		];

		if ( wp_is_mobile() ) {
			$scripts[] = 'jquery-touch-punch';
		}

		wp_enqueue_script( $scripts );

		$styles = [
			'gform_admin_icons',
		];

		wp_enqueue_style( $styles );
	}

	/**
	 * Add GravityView scripts and styles to Gravity Forms and GravityView No-Conflict modes
	 *
	 * @param array $registered Existing scripts or styles that have been registered (array of the handles).
	 *
	 * @return array
	 */
	public function register_no_conflict( $registered ) {
		$allowed_dependencies = [];

		$filter = current_filter();

		if ( preg_match( '/script/ism', $filter ) ) {
			$allowed_dependencies = [
				'sack',
				'wp-a11y',
			];
		} elseif ( preg_match( '/style/ism', $filter ) ) {
			$allowed_dependencies = [
				'dashicons',
				'wp-jquery-ui-dialog',
			];
		}

		return array_merge( $registered, $allowed_dependencies );
	}

	/**
	 * Returns whether the zone is dynamic.
	 *
	 * @since 2.31.0
	 *
	 * @param string $template_id The template ID.
	 * @param string $type        The type.
	 * @param string $zone        The zone.
	 *
	 * @return bool Whether the zone is dynamic.
	 */
	private function is_dynamic( string $template_id, string $type, string $zone ): bool {
		/**
		 * Modifies whether the zone is sortable.
		 *
		 * @since 2.31.0
		 *
		 * @param bool   $is_dynamic  Whether area is dynamic, meaning sortable / deletable / actionable.
		 * @param string $template_id Template ID.
		 * @param string $type        The object type; widget or field.
		 * @param string $zone        Current View context: `directory`, `single`, or `edit` (default: 'single').
		 */
		return (bool) apply_filters( 'gk/gravityview/admin-views/view/is-dynamic', false, $template_id, $type, $zone );
	}

	/**
	 * Renders a "Clear all fields" button in the View configuration.
	 *
	 * @since 2.44
	 *
	 * @param array  $area        The area.
	 * @param string $type        The type.
	 * @param array  $values      The values in the area.
	 * @param bool   $is_dynamic  Whether the zone is dynamic.
	 * @param string $template_id The template ID.
	 * @param string $zone        The zone.
	 */
	public function add_clear_all_fields_action( $area, $type, $values, $is_dynamic, $template_id, $zone ): void {
		unset( $values, $is_dynamic, $template_id ); // Required by action signature; not used here.
		if ( 'widget' === $type ) {
			return;
		}

		printf(
			'<a class="clear-all-fields" role="button" href="#" data-areaid="%s">%s</a>',
			esc_attr( $zone . '_' . $area['areaid'] ),
			esc_html__( 'Clear all fields', 'gk-gravityview' )
		);
	}
}

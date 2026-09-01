<?php
/**
 * GravityBoard widget type.
 *
 * PSR-4 migration of the legacy GravityView_Widget_GravityBoard class.
 *
 * @package GravityKit\GravityView\Widget\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Widget\Types;

use function gravityview;

/**
 * Widget to display a GravityBoard.
 *
 * @since 2.41
 * @since 3.0.0 Migrated to GravityKit\GravityView\Widget\Types namespace.
 */
class GravityBoardWidget extends \GV\Widget {

	protected $shortcode_name = 'gravityview_widget_gravityboard';

	/**
	 * @var string Widget icon.
	 */
	public $icon; // Defined below.

	/**
	 * Does this get displayed on a single entry?
	 *
	 * @var boolean
	 */
	protected $show_on_single = true;

	/**
	 * Constructor
	 *
	 * @since 2.41
	 */
	function __construct() {

		if ( ! class_exists( '\GravityKit\GravityBoard\Feed' ) ) {
			return;
		}

		$this->icon = 'data:image/svg+xml,' . rawurlencode( \GravityKit\GravityBoard\Feed::get_instance()->get_menu_icon() );

		// Initialize widget in the frontend or when editing a View/performing widget AJAX action
		$doing_ajax   = defined( 'DOING_AJAX' ) && DOING_AJAX && 'gv_field_options' === \GV\Utils::_POST( 'action' );
		$editing_view = 'edit' === \GV\Utils::_GET( 'action' ) && 'gravityview' === get_post_type( \GV\Utils::_GET( 'post' ) );
		$is_frontend  = gravityview()->request->is_frontend();

		if ( ! $doing_ajax && ! $editing_view && ! $is_frontend ) {
			return;
		}

		$this->widget_description = __( 'Display a GravityBoard board.', 'gk-gravityview' );

		$default_values = [
			'header' => 1,
			'footer' => 1,
		];

		$settings = [
			'board_id' => [
				'type'    => 'select',
				'label'   => __( 'Board to display', 'gk-gravityview' ),
				'value'   => '',
				'options' => $this->get_boards_as_options(),
				'desc'    => __( 'Select the GravityBoard to display in this widget.', 'gk-gravityview' ),
			],
		];

		add_filter( 'gravityview/widget/hide_until_searched/allowlist', [ $this, 'add_to_allowlist' ] );

		parent::__construct( __( 'GravityBoard', 'gk-gravityview' ), 'gravityboard', $default_values, $settings );
	}

	/**
	 * Get available boards as options for the widget settings
	 *
	 * @since 2.41
	 *
	 * @return array Array of board options
	 */
	private function get_boards_as_options() {
		$options = [
			'' => __( '&mdash; Select a Board &mdash;', 'gk-gravityview' ),
		];

		if ( ! class_exists( '\GravityKit\GravityBoard\Feed' ) ) {
			return $options;
		}

		try {
			$feed_addon = \GravityKit\GravityBoard\Feed::get_instance();
			$feeds = $feed_addon->get_feeds();

			if ( empty( $feeds ) ) {
				return $options;
			}

			foreach ( $feeds as $feed ) {
				if ( empty( $feed['is_active'] ) ) {
					continue;
				}

				// Check if user has permission to view this board
				if ( ! $feed_addon->user_has_permission( 'view_board', $feed ) ) {
					continue;
				}

				$board_name = $feed_addon->get_board_name( $feed );
				$options[ $feed['id'] ] = esc_html( $board_name );
			}
		} catch ( \Exception $e ) {
			// If there's an error getting boards, just return the default options
			return $options;
		}

		return $options;
	}

	/**
	 * Add widget to a list of allowed "Hide Until Searched" items
	 *
	 * @since 2.41
	 *
	 * @param array $allowlist Array of widgets to show before a search is performed, if the setting is enabled.
	 *
	 * @return array
	 */
	function add_to_allowlist( $allowlist ) {
		$allowlist[] = 'gravityboard';
		return $allowlist;
	}

	/**
	 * Render the widget on the frontend
	 *
	 * @since 2.41
	 *
	 * @param array                       $widget_args Widget arguments
	 * @param string|\GV\Template_Context $content     Content
	 * @param string                      $context     Context
	 */
	public function render_frontend( $widget_args, $content = '', $context = '' ) {

		if ( ! $this->pre_render_frontend( $context ) ) {
			return;
		}

		$board_id = \GV\Utils::get( $widget_args, 'board_id' );

		if ( empty( $board_id ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<div class="gravityboard-widget-error">' . esc_html__( 'Admin-only notice: Please select a board to display in the widget settings.', 'gk-gravityview' ) . '</div>';
			}
			return;
		}

		if ( ! class_exists( '\GravityKit\GravityBoard\Feed' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<div class="gravityboard-widget-error">' . esc_html__( 'Admin-only notice: GravityBoard plugin is not available.', 'gk-gravityview' ) . '</div>';
			}
			return;
		}

		echo do_shortcode( '[gravityboard id="' . esc_attr( $board_id ) . '"]' );
	}
}

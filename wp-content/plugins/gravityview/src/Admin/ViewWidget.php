<?php
/**
 * A widget in GravityView view configuration.
 *
 * PSR-4 migration of the legacy GravityView_Admin_View_Widget class.
 *
 * @package GravityKit\GravityView\Admin
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

use GravityKit\GravityView\Entry\BulkActions\Config as BulkActionConfig;

/**
 * A widget in GravityView view configuration
 */
class ViewWidget extends \GravityView_Admin_View_Item {

	protected $label_type = 'widget';

	/**
	 * Determines whether this widget can be duplicated.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Whether the widget can be duplicated.
	 */
	protected function can_duplicate(): bool {
		// The Search Bar widget has its own configuration dialog and should not be duplicated.
		if ( \GravityView_Widget_Search::getInstance()->get_widget_id() === $this->id ) {
			return false;
		}

		if ( BulkActionConfig::WIDGET_ID === $this->id ) {
			return false;
		}

		return parent::can_duplicate();
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_title( string $label ): string {
		/* translators: %s: the widget label. */
		return sprintf( __( 'Widget: %s', 'gk-gravityview' ), $label );
	}

	protected function additional_info() {

		$field_info_items = [];

		if ( ! empty( $this->item['description'] ) ) {

			$field_info_items[] = [
				'value' => $this->item['description'],
			];

		}

		/**
		 * Allows widgets to add custom summary information displayed in the admin.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $field_info_items Array of info items with 'value' and optional 'class' keys.
		 * @param string $widget_id        The widget ID (e.g., 'search_bar').
		 * @param array  $settings         The widget settings/configuration.
		 * @param array  $item             The widget item data.
		 */
		$field_info_items = apply_filters( 'gk/gravityview/admin/widget-info', $field_info_items, $this->id, $this->settings, $this->item );

		return $field_info_items;
	}
}

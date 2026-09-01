<?php
/**
 * Legacy GravityView_Widget wrapper class.
 *
 * PSR-4 migration of the deprecated GravityView_Widget class.
 * This is an empty wrapper that simply extends \GV\Widget for
 * backwards compatibility.
 *
 * @package GravityKit\GravityView\Widget\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Widget\Types;

/**
 * Main GravityView widget class.
 *
 * @deprecated 3.0.0 \GV\Widget instead.
 * @since 3.0.0 Migrated to GravityKit\GravityView\Widget\Types namespace.
 */
class LegacyWidget extends \GV\Widget {
	protected $shortcode_name = 'gravityview_widget';
}

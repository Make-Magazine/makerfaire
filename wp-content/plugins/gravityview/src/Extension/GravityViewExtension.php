<?php
/**
 * The GravityView Extension base class (legacy).
 *
 * Abstract class that third-party extensions extend to integrate with GravityView.
 * This is the PSR-4 equivalent of the legacy `GravityView_Extension` class from
 * `includes/class-gravityview-extension.php`.
 *
 * @package GravityKit\GravityView\Extension
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Extension;

use function gravityview;

/**
 * Abstract base class for GravityView extensions.
 *
 * Third-party extensions extend this class to integrate with GravityView.
 * Extends \GV\Extension (already migrated as GravityKit\GravityView\Extension\Extension).
 *
 * @deprecated 3.0.0 \GV\Extension instead.
 *
 * @since 3.0.0 Migrated to GravityKit\GravityView\Extension namespace.
 *
 * @TODO Remove once all extensions have been updated to use Foundation.
 */
abstract class GravityViewExtension extends \GV\Extension {
	public function __construct() {
		if ( ! in_array( $this->_author, [ 'GravityView', 'Katz Web Services, Inc.', true ] ) ) {
			gravityview()->log->warning( '\GravityView_Extension is deprecated. Inherit from \GV\Extension instead', [ 'data' => $this ] );
		}
		parent::__construct();
	}
}

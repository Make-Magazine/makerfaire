<?php

namespace ElementorPro\Core\App\Modules\ImportExportCustomization;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ElementorPro\License\API;

class Utils {
	public static function is_high_tier(): bool {
		try {
			$plan_type = API::get_plan_type();
			return 'expert' === $plan_type || 'agency' === $plan_type;
		} catch ( \Exception $exception ) {
			return false;
		}
	}
}

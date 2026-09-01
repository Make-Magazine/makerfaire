<?php
/**
 * GravityView placeholder templates
 *
 * @package GravityKit\GravityView\Template
 * @license GPL2+
 * @author  Katz Web Services, Inc.
 * @link    http://www.gravitykit.com
 *
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Template;

/**
 * Class PlaceholderTemplate
 *
 * Handles placeholder templates shown in GravityView presets for
 * templates that are not yet installed/activated.
 *
 * @since 2.10
 * @since 3.0.0 Migrated to PSR-4.
 */
class PlaceholderTemplate extends \GravityView_Template {

	/**
	 * @since 2.10
	 * @var mixed|string The template ID.
	 */
	private $id;

	function __construct( $id = 'template_placeholder', $settings = [] ) {

		$default_template_settings = [
			'type'        => 'custom',
			'buy_source'  => 'https://www.gravitykit.com/pricing/',
			'slug'        => '',
			'template_id' => '',
			'label'       => '',
			'description' => '',
			'logo'        => '',
			'icon'        => '',
			'price_id'    => '',
			'textdomain'  => '',
		];

		$settings = \wp_parse_args( $settings, $default_template_settings );

		$this->id       = $id;
		$this->settings = $settings;

		parent::__construct( $id, $settings, [], [] );
	}
}

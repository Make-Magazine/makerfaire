<?php

GFForms::include_addon_framework();

abstract class GP_Plugin extends GFAddOn {

	public static $perk_class;

	public $perk;

	/**
	 * Get an instance of the class. Should be overridden using the following sample code.
	 *
	 * if( self::$_instance == null ) {
	 *     self::$_instance = isset ( self::$perk ) ? new self ( new self::$perk ) : new self();
	 * }
	 *
	 * return self::$_instance;
	 */
	public static function get_instance() {
		_doing_it_wrong( __METHOD__, 'This function must be extended. Clay said so.', null );
	}

	// Override Meets Minimum Requirements as most perks are still depending on `gravityperks/gravityperks.php`
	public function meets_minimum_requirements() {
		$parent_result = parent::meets_minimum_requirements();

		/**
		 * Is there only one error saying `Required WordPress plugin is missing: Array` or `Required WordPress plugin is missing: gravityperks/gravityperks.php`?
		 *
		 * The reason for it showing "Array" is some perks define the requirements as
		 *
		 * 'plugins'      => array(
		 *		'gravityperks/gravityperks.php' => array(
		 *			'name'    => 'Gravity Perks',
		 *			'version' => '2.2.3',
		 *		),
		 *	),
		 *
		 * ... which is invalid. It should be:
		 *
		 * 'plugins'      => array(
		 * 		'gravityperks/gravityperks.php',
		 * )
		 */
		if ( is_array( $parent_result ) && count( $parent_result['errors'] ) === 1 ) {
			$error = array_shift( $parent_result['errors'] );
			if ( strpos( $error, 'gravityperks/gravityperks.php' ) !== false || strpos( $error, 'Gravity Perks' ) !== false || strpos( $error, 'Array' ) !== false ) {
				return array( 'meets_requirements' => true, 'errors' => array() );
			}
		}

		return $parent_result;
	}

	public static function includes() { }

	public function __construct( $perk = null ) {

		if ( ! $this->perk ) {
			$this->perk = $perk ? $perk : new GP_Perk( $this->_path, $this );
		}

		parent::__construct();

	}

	public function init() {

		parent::init();

		/**
		 * Remove row after Perks in plugins tab that Gravity Forms provides since Gravity Perks already checks
		 * requirements, license, etc.
		 */
		remove_action( 'after_plugin_row_' . $this->get_path(), array( $this, 'plugin_row' ), 10 );

		$this->perk->init();

	}

	public function check_requirements() {
		return $this->perk->check_requirements();
	}

	public function log( $message, $is_error = false ) {
		if ( $is_error ) {
			$this->log_error( $message );
		} else {
			$this->log_debug( $message );
		}
	}

}

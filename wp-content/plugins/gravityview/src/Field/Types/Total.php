<?php
/**
 * Total field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Total class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

use GFAPI;
use GFCommon;
use GVCommon;

use function gravityview;

/**
 * @since 1.20
 */
class Total extends \GravityView_Field {

	var $name = 'total';

	var $is_searchable = true;

	var $is_numeric = true;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than', 'contains'];

	var $group = 'product';

	var $icon = 'dashicons-cart';

	/** @see GF_Field_Total */
	var $_gf_field_class_name = 'GF_Field_Total';

	public function __construct() {
		$this->label = esc_html__( 'Total', 'gk-gravityview' );

		add_action( 'gravityview/edit_entry/after_update', [$this, 'edit_entry_recalculate_totals'], 10, 3 );

		add_filter( 'gravityview_blocklist_field_types', [$this, 'add_to_blocklist'], 10, 2 );

		parent::__construct();
	}

	/**
	 * Prevent the Total fields from being displayed in the Edit Entry configuration screen -- for now
	 *
	 * @since 1.20
	 *
	 * @todo Support Total fields in Edit Entry configuration
	 *
	 * @param array       $blocklist Array of field types not able to be added to Edit Entry
	 * @param  string|null $context Context
	 *
	 * @return array Blocklist, with "total" added. If not edit context, original field blocklist. Otherwise, blocklist including total.
	 */
	public function add_to_blocklist( $blocklist = [], $context = null ) {

		if ( empty( $context ) || 'edit' !== $context ) {
			return $blocklist;
		}

		$blocklist[] = 'total';

		return $blocklist;
	}

	/**
	 * If entry has totals fields, recalculate them
	 *
	 * @since 1.20
	 *
	 * @param array                         $form Gravity Forms form array
	 * @param int                           $entry_id Gravity Forms Entry ID
	 * @param GravityView_Edit_Entry_Render $Edit_Entry_Render
	 *
	 * @return void
	 */
	function edit_entry_recalculate_totals( $form = [], $entry_id = 0, $Edit_Entry_Render = null ) {

		$original_form = GVCommon::get_form( $form['id'] );

		$total_fields = GFCommon::get_fields_by_type( $original_form, 'total' );

		// saving total field as the last field of the form.
		if ( ! empty( $total_fields ) ) {

			$entry = GFAPI::get_entry( $entry_id );

			/** @type GF_Field_Total $total_field */
			foreach ( $total_fields as $total_field ) {
				$entry[ "{$total_field->id}" ] = GFCommon::get_order_total( $original_form, $entry );
			}

			$return_entry = GFAPI::update_entry( $entry );

			if ( is_wp_error( $return_entry ) ) {
				gravityview()->log->error( 'Updating the entry total fields failed', ['data' => $return_entry] );
			} else {
				gravityview()->log->debug( 'Updating the entry total fields succeeded' );
			}
		}
	}
}

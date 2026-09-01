<?php
/**
 * A collection of Form objects.
 *
 * @package GravityKit\GravityView\Form
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Form;

use function gravityview;

/**
 * A collection of \GV\Form objects.
 *
 * @implements \GV\Collection<Form>
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Form namespace.
 */
class FormCollection extends \GV\Collection {
	/**
	 * Add a \GV\Form to this collection.
	 *
	 * @param \GV\Form $form The form to add to the internal array.
	 *
	 * @api
	 * @since 2.0
	 * @return void
	 */
	public function add( $form ) {
		if ( ! $form instanceof \GV\Form ) {
			gravityview()->log->error( 'Form_Collections can only contain objects of type \GV\Form.' );
			return;
		}
		parent::add( $form );
	}

	/**
	 * Get a \GV\Form from this list.
	 *
	 * @param int    $form_id The ID of the form to get.
	 * @param string $backend The form backend identifier, allows for multiple form backends in the future. Unused until then.
	 *
	 * @api
	 * @since 2.0
	 *
	 * @return \GV\Form|null The \GV\Form with the $form_id as the ID, or null if not found.
	 */
	public function get( $form_id, $backend = 'gravityforms' ) {
		foreach ( $this->all() as $form ) {
			if ( $form->ID == $form_id ) {
				return $form;
			}
		}
		return null;
	}
}

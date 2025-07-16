<?php

class GPNF_GravityView {

	private static $instance = null;

	private static $form_has_gv_buttons = array();

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {

		add_action( 'gpnf_pre_nested_forms_markup', array( $this, 'remove_gravityview_edit_hooks' ) );
		add_action( 'gpnf_nested_forms_markup', array( $this, 'add_gravityview_edit_hooks' ) );
		add_action( 'gravityview/view/query', array( $this, 'filter_unsubmitted_child_entries' ), 10, 3 );
		add_filter( 'gform_entry_post_save', array( $this, 'store_gravityview_reference' ), 11, 2 );
		add_action( 'gravityview/edit_entry/after_update', array( $this, 'send_notifications_for_edited_entry' ), 10, 4 );

	}

	/**
	 * Prevent child entries of unsubmitted parent forms from displaying in GravityView views.
	 *
	 * @param $query GF_Query
	 * @param $view
	 * @param $request
	 */
	public function filter_unsubmitted_child_entries( &$query, $view, $request ) {
		$query_parts = $query->_introspect();

		$condition = new GF_Query_Condition(
			new GF_Query_Column( '_gpnf_expiration' ),
			GF_Query_Condition::EQ,
			new GF_Query_Literal( '' )
		);

		$query->where( \GF_Query_Condition::_and( $query_parts['where'], $condition ) );
	}

	public function gravityview_edit_render_instance() {

		if ( ! method_exists( 'GravityView_Edit_Entry', 'getInstance' ) ) {
			return null;
		}

		$edit_entry_instance = GravityView_Edit_Entry::getInstance();
		$render_instance     = $edit_entry_instance->instances['render'];

		return $render_instance;

	}

	/**
	 * GravityView adds a few hooks such as changing the submit buttons and changing the field value.
	 * These don't work well with the Nested Form so we need to temporarily unhook the filters/actions and re-add them.
	 */
	public function remove_gravityview_edit_hooks( $form ) {
		$render_instance = $this->gravityview_edit_render_instance();

		if ( $render_instance ) {
			self::$form_has_gv_buttons[ $form['id'] ] =
				has_filter( 'gform_submit_button', array( $render_instance, 'render_form_buttons' ) )
				|| has_filter( 'gform_submit_button', array( $render_instance, 'modify_edit_field_input' ) );

			remove_filter( 'gform_submit_button', array( $render_instance, 'render_form_buttons' ) );
			remove_filter( 'gform_field_input', array( $render_instance, 'modify_edit_field_input' ) );
		}

	}

	public function add_gravityview_edit_hooks( $form ) {

		if ( ! rgar( self::$form_has_gv_buttons, $form['id'] ) ) {
			return;
		}

		$render_instance = $this->gravityview_edit_render_instance();

		if ( $render_instance ) {
			add_filter( 'gform_submit_button', array( $render_instance, 'render_form_buttons' ) );
			add_filter( 'gform_field_input', array( $render_instance, 'modify_edit_field_input' ), 10, 5 );
		}

	}

	public function store_gravityview_reference( $entry, $form ) {
		if ( ! rgget( 'gvid' ) || ! rgget( 'edit' ) ) {
			return $entry;
		}

		// Store the GravityView ID in the entry meta so that we can use it later to send notifications.
		gform_update_meta( $entry['id'], 'gvid', rgget( 'gvid' ) );
		return $entry;
	}

	public function send_notifications_for_edited_entry( $form, $entry_id, $renderer, $gv_data ) {
		$entry = GFAPI::get_entry( $entry_id );
		if ( ! $entry || ! is_array( $entry ) ) {
			return;
		}

		foreach ( $form['fields'] as $field ) {
			if ( $field->type != 'form' ) {
				continue;
			}

			$nested_form = GFAPI::get_form( rgar( $field, 'gpnfForm' ) );
			if ( ! $nested_form ) {
				continue;
			}

			$nested_entries = $entry[ $field->id ];
			if ( empty( $nested_entries ) || ! is_string( $nested_entries ) ) {
				continue;
			}

			$values = explode( ',', $nested_entries );
			foreach ( $values as $value ) {
				$value = trim( $value );
				if ( empty( $value ) || ! is_numeric( $value ) ) {
					continue;
				}

				$nested_entry = GFAPI::get_entry( $value );
				// If the entry is not found, or is an error, or does not have a gvid meta, skip it.
				if ( ! $nested_entry || is_wp_error( $nested_entry ) || ! gform_get_meta( $nested_entry['id'], 'gvid' ) ) {
					continue;
				}

				// Notifications are sent only for the Nested Form entries that were edited via GravityView.
				GFAPI::send_notifications( $nested_form, $nested_entry, 'gravityview/edit_entry/after_update' );
				// Clear the gvid meta so that the notifications are not sent again (unless the entry is edited again).
				gform_delete_meta( $nested_entry['id'], 'gvid' );
			}
		}
	}
}

function gpnf_gravityview() {
	return GPNF_GravityView::get_instance();
}

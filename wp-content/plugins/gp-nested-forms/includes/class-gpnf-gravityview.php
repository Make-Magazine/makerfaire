<?php

class GPNF_GravityView {

	private static $instance = null;

	private static $form_has_gv_buttons = array();

	private $validation_filters_added = array();

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
		add_filter( 'gravityview/edit_entry/form_fields', array( $this, 'add_entry_limit_validation_hooks' ), 10, 4 );
		add_action( 'gravityview/edit_entry/after_update', array( $this, 'send_notifications_for_edited_entry' ), 10, 4 );

	}

	public function add_entry_limit_validation_hooks( $fields, $edit_fields, $form, $view_id ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		$has_limited_nested_form_field = false;

		foreach ( $fields as $field ) {
			if (
				$field->type == 'form'
				&& ( ! rgblank( $field->gpnfEntryLimitMin ) || ! rgblank( $field->gpnfEntryLimitMax ) )
			) {
				$has_limited_nested_form_field = true;
				break;
			}
		}

		$form_id = (int) rgar( $form, 'id' );
		if ( ! $has_limited_nested_form_field || ! $form_id || rgar( $this->validation_filters_added, $form_id ) ) {
			return $fields;
		}

		add_filter( 'gform_validation_' . $form_id, array( $this, 'validate_entry_limits' ), 20 );

		$this->validation_filters_added[ $form_id ] = true;

		return $fields;
	}

	public function validate_entry_limits( $validation_result ) {
		$form_id = (int) rgars( $validation_result, 'form/id' );

		if ( ! $form_id ) {
			return $validation_result;
		}

		foreach ( $validation_result['form']['fields'] as &$field ) {
			if (
				$field->type != 'form'
				|| ( rgblank( $field->gpnfEntryLimitMin ) && rgblank( $field->gpnfEntryLimitMax ) )
			) {
				continue;
			}

			$input_name = 'input_' . $field->id;
			$value      = rgpost( $input_name );

			if ( null === $value ) {
				continue;
			}

			$field->validate( $value, $validation_result['form'] );

			if ( ! empty( $field->failed_validation ) ) {
				$validation_result['is_valid'] = false;
			}
		}
		unset( $field );

		$render_instance = $this->gravityview_edit_render_instance();
		if ( $render_instance && rgar( $render_instance->form_after_validation, 'id' ) == $form_id ) {
			$render_instance->form_after_validation = $validation_result['form'];
		}

		return $this->remove_entry_limit_validation_hooks( $validation_result, $form_id );
	}

	public function remove_entry_limit_validation_hooks( $validation_result, $form_id ) {
		if ( ! $form_id ) {
			return $validation_result;
		}

		remove_filter( 'gform_validation_' . $form_id, array( $this, 'validate_entry_limits' ), 20 );

		unset( $this->validation_filters_added[ $form_id ] );

		return $validation_result;
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

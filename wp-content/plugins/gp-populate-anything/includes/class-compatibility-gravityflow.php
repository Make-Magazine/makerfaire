<?php
class GPPA_Compatibility_GravityFlow {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct() {
		add_filter( 'gravityflow_step_form', array( $this, 'populate_form' ), 10, 2 );

		/* Source form is hydrated below. Target form is hydrated via "gform_form_pre_update_entry" in GPPA proper. */
		add_filter( 'gravityflowformconnector_update_entry_form', array( $this, 'populate_form' ), 10, 2 );

		// Added these at the request of Gravity Flow support. They have not been properly tested. Will revisit in the future.
		// See: https://secure.helpscout.net/conversation/1104725512/16351/
		add_filter( 'gravityflowformconnector_new_entry_form', array( $this, 'populate_form' ), 10, 2 );
		add_filter( 'gravityflowformconnector_update_field_values_form', array( $this, 'populate_form' ), 10, 2 );
		// Gravity Flow Form Submission workflow step is distinct from the 'New Entry' step and the separate hook must be tapped.
		add_filter( 'gravityflowformconnector_form_submission', array( $this, 'populate_form_on_form_submission' ), 10, 5 );

		add_filter( 'gppa_show_administrative_fields_in_ajax', array( $this, 'should_show_administrative_fields_on_workflow_inbox' ) );
	}

	public function populate_form( $form, $entry ) {
		static $populated_form_cache = array();

		/**
		 * Do not send through hydrate_initial_load if the form doesn't use dynamic population or have any
		 * Live Merge Tags.
		 */
		if ( ! gp_populate_anything()->should_enqueue_frontend_scripts( $form ) ) {
			return $form;
		}

		$cache_key = $form['id'] . '-' . rgar( $entry, 'id' );

		if ( isset( $populated_form_cache[ $cache_key ] ) ) {
			return $populated_form_cache[ $cache_key ];
		}

		$populated_form_cache[ $cache_key ] = gp_populate_anything()->populate_form( $form, false, array(), $entry );
		return $populated_form_cache[ $cache_key ];
	}

	public function populate_form_on_form_submission( $new_entry, $entry, $form, $target_form, $step ) {
		if ( gp_populate_anything()->should_enqueue_frontend_scripts( $target_form ) ) {
			foreach ( $target_form['fields'] as &$field ) {
				$lmt = gp_populate_anything()->live_merge_tags;
				if ( $field->type == 'html' && $lmt->has_live_merge_tag( $field->content ) ) {
					$lmt->populate_lmt_whitelist( $target_form );
					$field->content = $lmt->replace_live_merge_tags_static( $field->content, $target_form, $new_entry );
				} elseif ( gp_populate_anything()->is_field_dynamically_populated( $field ) || $lmt->has_live_merge_tag( $field->defaultValue ) ) {
					$hydrated_field            = gp_populate_anything()->populate_field( $field, $target_form, $new_entry );
					$new_entry[ $field['id'] ] = $hydrated_field['field_value'];
				}
			}
		}

		return $new_entry;
	}

	/**
	 * @deprecated 2.0 GPPA_Compatibility_GravityFlow::populate_form()
	 *
	 * @param array $form
	 * @param array $entry
	 *
	 * @return array
	 */
	public function hydrate_form( $form, $entry ) {
		return $this->populate_form( $form, $entry );
	}

	public function should_show_administrative_fields_on_workflow_inbox() {
		return GFAPI::current_user_can_any( 'gravityflow_inbox' ) && rgar( $_REQUEST, 'page' ) === 'gravityflow-inbox' && rgget( 'view' ) === 'entry';
	}

}

function gppa_compatibility_gravityflow() {
	return GPPA_Compatibility_GravityFlow::get_instance();
}

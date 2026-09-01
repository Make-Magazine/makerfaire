<?php
/**
 * Frontend edit entries bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GFAPI;
use GFCommon;
use GFFormsModel;
use GFFormDisplay;
use GravityKit\GravityView\Entry\BulkActions\Config;
use GravityKit\GravityView\View\View;
use GravityView_Edit_Entry;
use GravityView_Edit_Entry_Locking;
use GravityView_Edit_Entry_Render;
use GVCommon;
use Throwable;
use WP_Error;

/**
 * Updates selected entries using configured fields and entry properties.
 *
 * @since 3.0.0
 */
final class BulkEditAction implements BulkAction {
	private const FIELD_IDS_MODE_INHERIT       = 'inherit';
	private const FIELD_IDS_MODE_CUSTOM        = 'custom';
	private const FIELD_IDS_MODE_ALL           = 'all';
	private const FIELD_IDS_MODE_SETTING       = 'field_ids_mode';
	private const FIELD_IDS_SETTING            = 'field_ids';
	private const RESOLVED_FIELD_IDS_SETTING   = 'resolved_field_ids';
	private const SYNTHESIZED_FIELD_EDIT_CAP   = 'read';
	private const ALL_FIELDS_REQUIRED_CAPS     = [ 'gravityforms_edit_entries', 'gravityview_edit_others_entries' ];
	private const ADMIN_ONLY_REQUIRED_CAP      = 'gravityforms_edit_entries';
	private const SENSITIVE_PROPERTY_CAP       = 'gravityforms_edit_entries';
	private const TARGET_KIND_FIELD            = 'field';
	private const TARGET_KIND_FIELD_INPUT      = 'field_input';
	private const TARGET_KIND_ENTRY_PROPERTY   = 'entry_property';
	private const SENSITIVE_ENTRY_PROPERTIES   = [
		'created_by',
		'currency',
		'date_updated',
		'form_id',
		'id',
		'ip',
		'is_fulfilled',
		'is_read',
		'is_starred',
		'payment_amount',
		'payment_date',
		'payment_method',
		'payment_status',
		'post_id',
		'source_url',
		'status',
		'transaction_id',
		'transaction_type',
		'user_agent',
	];

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function key() {
		return 'edit_entries';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function config() {
		return [
			'label'                       => __( 'Edit Entries', 'gk-gravityview' ),
			'callback'                    => [ $this, 'process' ],
			'available_callback'          => [ $this, 'is_available' ],
			'unavailable_notice_callback' => [ $this, 'unavailable_notice' ],
			'request_callback'            => [ $this, 'sanitize_request' ],
			'frontend_data_callback'      => [ $this, 'get_frontend_data' ],
			'default_enabled'             => false,
			'settings_schema'             => [
				self::FIELD_IDS_MODE_SETTING => [
					'type'                    => 'radio',
					'label'                   => __( 'Editable fields', 'gk-gravityview' ),
					'default'                 => self::FIELD_IDS_MODE_INHERIT,
					'options'                 => [
						self::FIELD_IDS_MODE_INHERIT => __( 'Inherit from Edit Entry layout', 'gk-gravityview' ),
						self::FIELD_IDS_MODE_CUSTOM  => __( 'Pick specific fields', 'gk-gravityview' ),
						self::FIELD_IDS_MODE_ALL     => __( 'All supported fields and entry properties', 'gk-gravityview' ),
					],
					'hidden_options_callback' => [ $this, 'get_hidden_field_source_options' ],
				],
				self::FIELD_IDS_SETTING      => [
					'type'               => 'multiselect',
					'label'              => __( 'Fields and entry properties', 'gk-gravityview' ),
					'default'            => [],
					'options_callback'   => [ $this, 'get_field_picker_options' ],
					'class'              => 'gv-tom-select',
					'placeholder'        => __( 'Select fields', 'gk-gravityview' ),
					'submit_empty_value' => true,
					'requires'           => self::FIELD_IDS_MODE_SETTING . '=' . self::FIELD_IDS_MODE_CUSTOM,
				],
			],
			'background'                  => [
				'enabled'             => true,
				'batch_size'          => 25,
				'completion_behavior' => Config::BACKGROUND_COMPLETE_RELOAD_LINK,
				'queued_message'      => __( 'Entry edits are running in the background.', 'gk-gravityview' ),
				'complete_callback'   => [ $this, 'complete' ],
			],
			'lock'                        => true,
		];
	}

	/**
	 * Returns field picker options for the action settings UI.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view View context.
	 *
	 * @return array
	 */
	public function get_field_picker_options( ?View $view = null, array $action = [], $action_key = '', array $setting = [] ) {
		unset( $action, $action_key, $setting );

		if ( ! $view ) {
			return [];
		}

		$form = $this->get_form( $view );

		if ( ! $form || empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			return [];
		}

		$options = [];

		foreach ( $this->get_form_field_targets( $form ) as $target_id => $target ) {
			$options[ $target_id ] = $this->get_target_label( $target );
		}

		foreach ( $this->get_entry_property_targets( $view, $form ) as $target_id => $target ) {
			if ( $this->current_user_can_edit_entry_property( $target, $view ) ) {
				$options[ $target_id ] = $target['label'];
			}
		}

		uksort(
			$options,
			static function ( $a, $b ) use ( $options ) {
				$result = strnatcasecmp( (string) $options[ $a ], (string) $options[ $b ] );

				return 0 === $result ? strnatcasecmp( (string) $a, (string) $b ) : $result;
			}
		);

		return $options;
	}

	/**
	 * Returns field source options hidden from the current settings actor.
	 *
	 * @since 3.0.0
	 *
	 * @return string[]
	 */
	public function get_hidden_field_source_options( ?View $view = null, array $action = [], $action_key = '', array $setting = [] ) {
		unset( $view, $action, $action_key, $setting );

		return $this->current_user_can_edit_all_fields() ? [] : [ self::FIELD_IDS_MODE_ALL ];
	}

	/**
	 * Updates selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param int[]  $entry_ids  Entry IDs.
	 * @param array  $entries    Entries keyed by ID.
	 * @param View   $view       View context.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param array  $context    Processing context.
	 *
	 * @return array|WP_Error
	 */
	public function process( array $entry_ids, array $entries, View $view, $action_key = '', array $action = [], array $context = [] ) {
		unset( $entry_ids, $action_key );

		$changes = $action['request']['changes'] ?? [];
		$background = ! empty( $context['background'] );

		if ( empty( $changes ) || ! is_array( $changes ) ) {
			return new WP_Error( 'gravityview_bulk_edit_missing_changes', __( 'Choose at least one field to update.', 'gk-gravityview' ) );
		}

		$form = $this->get_form( $view );

		if ( ! $form ) {
			return new WP_Error( 'gravityview_bulk_edit_missing_form', __( 'The form could not be found.', 'gk-gravityview' ) );
		}

		$editable_fields = $this->get_editable_fields_by_id( $view, $action, $context );

		if ( [] === $editable_fields ) {
			return new WP_Error( 'gravityview_bulk_edit_no_fields', __( 'No editable fields or entry properties are configured for this View.', 'gk-gravityview' ) );
		}

		$resolved_changes = [];

		foreach ( $changes as $change ) {
			if ( ! is_array( $change ) ) {
				return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
			}

			$target_id = $this->normalize_target_id( $change['field_id'] ?? '', $view, $form );

			if ( '' === $target_id || empty( $editable_fields[ $target_id ] ) ) {
				return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
			}

			$resolved_changes[] = [
				'change'    => $change,
				'target_id' => $target_id,
				'target'    => $editable_fields[ $target_id ],
			];
		}

		$processed  = 0;
		$failed     = 0;
		$last_error = '';

		foreach ( $entries as $entry ) {
			if ( empty( $entry['id'] ) || 'trash' === ( $entry['status'] ?? '' ) || ! GravityView_Edit_Entry::check_user_cap_edit_entry( $entry, $view ) ) {
				++$failed;
				continue;
			}

			$entry_id = (int) $entry['id'];

			try {
				if ( $this->entry_is_locked( $entry_id, $view, $background ) ) {
					$last_error = __( 'One or more selected entries are currently being edited. Please try again later.', 'gk-gravityview' );
					++$failed;
					continue;
				}
			} catch ( Throwable $e ) {
				$this->log_exception( 'Bulk Edit could not check the entry lock.', $e, [ 'entry_id' => $entry_id ] );
				$last_error = __( 'One or more selected entries could not be updated.', 'gk-gravityview' );
				++$failed;
				continue;
			}

			$updates = [
				self::TARGET_KIND_FIELD          => [],
				self::TARGET_KIND_FIELD_INPUT    => [],
				self::TARGET_KIND_ENTRY_PROPERTY => [],
			];
			$valid   = true;

			foreach ( $resolved_changes as $resolved_change ) {
				$change    = $resolved_change['change'];
				$target_id = $resolved_change['target_id'];
				$target    = $resolved_change['target'];

				if ( ! $this->user_can_edit_target( $target, $view, $entry ) ) {
					$valid = false;
					break;
				}

				$validation = $this->validate_target_change( $target, $change, $form, $entry, 'api-submit', $view );

				if ( is_wp_error( $validation ) ) {
					$valid      = false;
					$last_error = $validation->get_error_message();
					break;
				}

				try {
					if ( self::TARGET_KIND_ENTRY_PROPERTY === $target['kind'] ) {
						$updates[ self::TARGET_KIND_ENTRY_PROPERTY ][ $target_id ] = [
							'target' => $target,
							'value'  => $this->get_entry_property_save_value( $target, $change['value'] ?? '', $change, $form, $entry, $view ),
						];
					} elseif ( self::TARGET_KIND_FIELD_INPUT === $target['kind'] ) {
						$updates[ self::TARGET_KIND_FIELD_INPUT ][ $target_id ] = [
							'target' => $target,
							'value'  => $this->get_field_input_save_value( $target, $change['value'] ?? '', $form, $entry ),
						];
					} else {
						$field = clone $target['field'];
						$updates[ self::TARGET_KIND_FIELD ][ $target_id ] = [
							'target' => $target,
							'value'  => 'checkbox' === $this->get_input_type( $field )
								? ( is_array( $change['value'] ?? null ) ? $change['value'] : [] )
								: $this->get_save_value( $field, $change['value'] ?? '', $form, $entry ),
						];
					}
				} catch ( Throwable $e ) {
					$this->log_exception( 'Bulk Edit could not prepare a field value.', $e, [ 'entry_id' => $entry_id, 'field_id' => $target_id ] );
					$valid      = false;
					$last_error = __( 'One or more selected entries could not be updated.', 'gk-gravityview' );
					break;
				}
			}

			if ( ! $valid ) {
				++$failed;
				continue;
			}

			try {
				$result = $this->update_entry_targets( $entry_id, $updates, $view, $form, $entry );
			} catch ( Throwable $e ) {
				$this->log_exception( 'Bulk Edit could not update entry fields.', $e, [ 'entry_id' => $entry_id ] );
				$last_error = __( 'One or more selected entries could not be updated.', 'gk-gravityview' );
				++$failed;
				continue;
			}

			if ( is_wp_error( $result ) ) {
				$last_error = $result->get_error_message();
				++$failed;
				continue;
			}

			try {
				$updated_entry = GFAPI::get_entry( $entry_id );
				$updated_entry = is_wp_error( $updated_entry ) ? $entry : $updated_entry;

				$this->after_update( $form, $updated_entry, $entry, $view, $changes );
			} catch ( Throwable $e ) {
				$this->log_exception( 'Bulk Edit after-update hooks failed.', $e, [ 'entry_id' => $entry_id ] );
			}

			++$processed;
		}

		if ( ! $processed && $failed ) {
			return new WP_Error( 'gravityview_bulk_edit_failed', $last_error ? $last_error : __( 'The selected entries could not be updated.', 'gk-gravityview' ) );
		}

		return [
			'processed' => $processed,
			'failed'    => $failed,
			'message'   => ResultMessageFormatter::processed(
				$processed,
				$failed,
				/* translators: [count] is the number of entries updated. */
				__( '[count] entries updated.', 'gk-gravityview' ),
				/* translators: [count] is the number of entries updated. */
				__( '[count] entry updated.', 'gk-gravityview' )
			),
		];
	}

	/**
	 * Builds the final background result message from cumulative counts.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View context.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Background context.
	 *
	 * @return array
	 */
	public function complete( View $view, $action_key, array $action, array $context ) {
		unset( $view, $action_key, $action );

		return [
			'notice' => [
				'message' => ResultMessageFormatter::processed(
					(int) ( $context['processed'] ?? 0 ),
					(int) ( $context['failed'] ?? 0 ),
					/* translators: [count] is the number of entries updated. */
					__( '[count] entries updated.', 'gk-gravityview' ),
					/* translators: [count] is the number of entries updated. */
					__( '[count] entry updated.', 'gk-gravityview' )
				),
			],
		];
	}

	/**
	 * Sanitizes submitted edit changes.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $input      Raw action input.
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param array  $context    Sanitization context.
	 *
	 * @return array|WP_Error
	 */
	public function sanitize_request( array $input, View $view, $action_key = '', array $action = [], array $context = [] ) {
		unset( $action_key );

		$editable_fields = $this->get_editable_fields_by_id( $view, $action, $context );
		$form            = $this->get_form( $view );
		$raw_changes     = isset( $input['changes'] ) && is_array( $input['changes'] ) ? $input['changes'] : [];
		$entry              = isset( $context['representative_entry'] ) && is_array( $context['representative_entry'] ) ? $context['representative_entry'] : [];
		$validation_context = isset( $context['validation_context'] ) ? sanitize_key( (string) $context['validation_context'] ) : 'api-validate';
		$changes            = [];
		$seen               = [];
		$field_errors       = [];

		if ( ! $form ) {
			return new WP_Error( 'gravityview_bulk_edit_missing_form', __( 'The form could not be found.', 'gk-gravityview' ) );
		}

		foreach ( $raw_changes as $raw_change ) {
			if ( ! is_array( $raw_change ) ) {
				continue;
			}

			$target_id = $this->normalize_target_id( $raw_change['field_id'] ?? '', $view, $form );
			$operation = sanitize_key( (string) ( $raw_change['operation'] ?? '' ) );

			if ( '' === $target_id ) {
				return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
			}

			if ( empty( $editable_fields[ $target_id ] ) ) {
				$field_errors[ $target_id ] = __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' );
				continue;
			}

			if ( isset( $seen[ $target_id ] ) ) {
				$field_errors[ $target_id ] = __( 'Choose each field only once.', 'gk-gravityview' );
				continue;
			}

			if ( ! in_array( $operation, [ 'set', 'clear' ], true ) ) {
				$field_errors[ $target_id ] = __( 'One or more submitted field actions are invalid.', 'gk-gravityview' );
				continue;
			}

			$target = $editable_fields[ $target_id ];

			if ( 'clear' === $operation && $this->target_is_required( $target ) ) {
				$field_errors[ $target_id ] = __( 'Required fields cannot be cleared.', 'gk-gravityview' );
				continue;
			}

			$value = 'clear' === $operation ? '' : $this->sanitize_target_value( $target, $raw_change['value'] ?? '', $form, $entry, $view );

			if ( is_wp_error( $value ) ) {
				$field_errors[ $target_id ] = $value->get_error_message();
				continue;
			}

			if ( 'set' === $operation && $this->target_value_is_empty( $value ) ) {
				$field_errors[ $target_id ] = __( 'Enter a value or choose Clear field value.', 'gk-gravityview' );
				continue;
			}

			if ( self::TARGET_KIND_FIELD_INPUT !== ( $target['kind'] ?? '' ) ) {
				$validation = $this->validate_target_change(
					$target,
					[
						'field_id'  => $target_id,
						'operation' => $operation,
						'value'     => $value,
					],
					$form,
					$entry,
					$validation_context,
					$view
				);

				if ( is_wp_error( $validation ) ) {
					$field_errors[ $target_id ] = $validation->get_error_message();
					continue;
				}
			}

			$changes[] = [
				'field_id'  => $target_id,
				'operation' => $operation,
				'value'     => $value,
			];
			$seen[ $target_id ] = true;
		}

		if ( [] !== $field_errors ) {
			$first_error = reset( $field_errors );

			return new WP_Error(
				'gravityview_bulk_edit_field_errors',
				$first_error ? (string) $first_error : __( 'Review the highlighted fields and try again.', 'gk-gravityview' ),
				[
					'field_errors' => $field_errors,
				]
			);
		}

		if ( [] === $changes ) {
			return new WP_Error( 'gravityview_bulk_edit_no_changes', __( 'Choose at least one field to update.', 'gk-gravityview' ) );
		}

		return [
			'changes'            => $changes,
			self::RESOLVED_FIELD_IDS_SETTING => array_keys( $editable_fields ),
		];
	}

	/**
	 * Whether this action should appear for the View.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return bool
	 */
	public function is_available( View $view, array $action = [] ) {
		if ( ! $this->current_user_can_edit_entries( $view ) ) {
			return false;
		}

		return [] !== $this->get_editable_fields_by_id( $view, $action );
	}

	/**
	 * Returns the View editor notice when Bulk Edit cannot appear on the frontend.
	 *
	 * @since 3.1.0
	 *
	 * @param View $view View.
	 *
	 * @return string|null
	 */
	public function unavailable_notice( View $view ): ?string {
		$form = $this->get_form( $view );

		if ( ! $form || ! $this->form_has_conditional_logic( $form ) ) {
			return null;
		}

		return __( 'Edit Entries will not appear on the frontend because this form uses conditional logic, which cannot be evaluated across multiple entries at once.', 'gk-gravityview' );
	}

	/**
	 * Returns data used by the frontend edit dialog.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	public function get_frontend_data( View $view, array $action = [] ) {
		$fields = [];

		foreach ( $this->get_editable_fields_by_id( $view, $action ) as $target_id => $data ) {
			$fields[] = [
				'id'         => (string) $target_id,
				'label'      => $this->get_target_label( $data ),
				'type'       => $this->get_target_input_type( $data ),
				'required'   => $this->target_is_required( $data ),
				'choices'    => $this->get_target_frontend_choices( $data ),
				'validation' => $this->get_target_validation( $data ),
			];
		}

		return [
			'fields' => $fields,
		];
	}

	/**
	 * Returns editable targets keyed by target ID.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view    View.
	 * @param array $action  Action config.
	 * @param array $context Resolution context.
	 *
	 * @return array
	 */
	private function get_editable_fields_by_id( View $view, array $action = [], array $context = [] ) {
		$form = $this->get_form( $view );

		if ( ! $form || empty( $form['fields'] ) || ! is_array( $form['fields'] ) || $this->form_has_conditional_logic( $form ) ) {
			return [];
		}

		$settings = $this->get_field_settings_for_action( $view, $form, $action );

		if ( [] === $settings ) {
			return [];
		}

		$form_targets   = $this->get_form_field_targets( $form );
		$mode           = $this->get_field_ids_mode( $action );
		$candidate_ids  = [];
		$settings_by_id = [];

		foreach ( $settings as $setting ) {
			$setting_target_ids = $this->get_setting_target_ids( $setting['id'] ?? '', $view, $form, self::FIELD_IDS_MODE_INHERIT === $mode );

			foreach ( $setting_target_ids as $field_id ) {
				if ( '' === $field_id ) {
					continue;
				}

				$candidate_ids[]              = $field_id;
				$settings_by_id[ $field_id ] = $setting;
			}
		}

		$candidate_ids = array_values( array_unique( $candidate_ids ) );
		$snapshot_ids  = $this->get_resolved_field_ids_snapshot( $action, $view, $form );

		if ( null !== $snapshot_ids ) {
			$candidate_ids = array_values( array_intersect( $candidate_ids, $snapshot_ids ) );
		} elseif ( array_key_exists( 'request', $action ) && is_array( $action['request'] ) ) {
			return [];
		}

		if ( [] === $candidate_ids ) {
			return [];
		}

		$field_ids = $this->filter_source_field_ids( $candidate_ids, $mode, $this->get_action_settings( $action ), $view, $form );

		$editable = [];

		foreach ( $field_ids as $field_id ) {
			$setting = $settings_by_id[ $field_id ] ?? [];

			if ( $this->is_entry_property_target_id( $field_id, $view, $form ) ) {
				$property_target = $this->get_entry_property_targets( $view, $form )[ $field_id ] ?? [];
				$property        = (string) ( $property_target['property'] ?? '' );

				if ( ! $property || ! $this->current_user_can_edit_entry_property( $property_target, $view ) ) {
					continue;
				}

				$property_target = $this->resolve_entry_property_choices( $property_target, $view, $form );

				$editable[ $field_id ] = array_merge(
					$property_target,
					[
						'kind'    => self::TARGET_KIND_ENTRY_PROPERTY,
						'setting' => $setting,
					]
				);

				continue;
			}

			if ( empty( $form_targets[ $field_id ] ) || ! $this->user_can_edit_field( $setting, $view ) ) {
				continue;
			}

			$target = $form_targets[ $field_id ];
			$field  = clone $target['field'];

			if ( ! $this->is_bulk_edit_field_safe( $field ) ) {
				continue;
			}

			$field = GravityView_Edit_Entry_Render::merge_field_properties( $field, $setting );
			$target['field']   = $field;
			$target['setting'] = $setting;
			$target['label']   = $this->get_merged_target_label( $target );

			$editable[ $field_id ] = $target;
		}

		return $this->filter_edit_entry_fields( $editable, $settings, $form, (int) $view->ID );
	}

	/**
	 * Returns field settings resolved from the action field source mode.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view   View.
	 * @param array $form   Gravity Forms form.
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	private function get_field_settings_for_action( View $view, array $form, array $action ) {
		$mode = $this->get_field_ids_mode( $action );

		if ( self::FIELD_IDS_MODE_INHERIT === $mode ) {
			return $this->get_configured_edit_field_settings( $view );
		}

		if ( self::FIELD_IDS_MODE_ALL === $mode && ! $this->current_user_can_edit_all_fields() ) {
			return [];
		}

		$field_ids = self::FIELD_IDS_MODE_CUSTOM === $mode
			? $this->normalize_target_ids( $this->get_action_settings( $action )[ self::FIELD_IDS_SETTING ] ?? [], $view, $form )
			: $this->get_all_supported_target_ids( $form, $view );

		return array_map(
			static function ( $field_id ) {
				return [
					'id'             => $field_id,
					'show_label'     => true,
					'allow_edit_cap' => self::SYNTHESIZED_FIELD_EDIT_CAP,
				];
			},
			$field_ids
		);
	}

	/**
	 * Returns the configured field source mode for the action.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return string
	 */
	private function get_field_ids_mode( array $action ) {
		$settings = $this->get_action_settings( $action );
		$mode     = isset( $settings[ self::FIELD_IDS_MODE_SETTING ] ) ? sanitize_key( (string) $settings[ self::FIELD_IDS_MODE_SETTING ] ) : self::FIELD_IDS_MODE_INHERIT;

		return in_array( $mode, [ self::FIELD_IDS_MODE_INHERIT, self::FIELD_IDS_MODE_CUSTOM, self::FIELD_IDS_MODE_ALL ], true ) ? $mode : self::FIELD_IDS_MODE_INHERIT;
	}

	/**
	 * Returns resolved action settings.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	private function get_action_settings( array $action ) {
		return isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
	}

	/**
	 * Returns normalized editable target IDs.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $field_ids Target IDs.
	 *
	 * @return string[]
	 */
	private function normalize_target_ids( $field_ids, ?View $view = null, array $form = [] ) {
		if ( ! is_array( $field_ids ) ) {
			return [];
		}

		$normalized = [];

		foreach ( $field_ids as $field_id ) {
			$field_id = $this->normalize_target_id( $field_id, $view, $form );

			if ( '' !== $field_id ) {
				$normalized[] = $field_id;
			}
		}

		return array_values( array_unique( $normalized ) );
	}

	/**
	 * Returns one normalized editable target ID.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $target_id Target ID.
	 *
	 * @return string
	 */
	private function normalize_target_id( $target_id, ?View $view = null, array $form = [] ) {
		$target_id = is_scalar( $target_id ) ? sanitize_text_field( wp_unslash( (string) $target_id ) ) : '';

		if ( '' === $target_id ) {
			return '';
		}

		$field_id = absint( $target_id );

		if ( $field_id && ctype_digit( $target_id ) ) {
			return (string) $field_id;
		}

		$input_target_id = $this->normalize_field_input_target_id( $target_id, $form );

		if ( '' !== $input_target_id ) {
			return $input_target_id;
		}

		foreach ( $this->get_entry_property_targets( $view, $form ) as $entry_target_id => $target ) {
			if ( $entry_target_id === $target_id || $target['property'] === $target_id ) {
				return $entry_target_id;
			}
		}

		return '';
	}

	/**
	 * Returns all supported target IDs.
	 *
	 * @since 3.0.0
	 *
	 * @param array $form Gravity Forms form.
	 *
	 * @return string[]
	 */
	private function get_all_supported_target_ids( array $form, ?View $view = null ) {
		$field_ids = array_keys( $this->get_form_field_targets( $form ) );

		$field_ids = array_merge( $field_ids, array_keys( $this->get_entry_property_targets( $view, $form ) ) );

		return array_values( array_unique( $field_ids ) );
	}

	/**
	 * Returns concrete target IDs represented by one saved field setting.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed     $setting_id Saved field setting ID.
	 * @param View|null $view       View context.
	 * @param array     $form       Gravity Forms form.
	 * @param bool      $expand_parent_targets Whether parent IDs expand to subinput targets.
	 *
	 * @return string[]
	 */
	private function get_setting_target_ids( $setting_id, ?View $view = null, array $form = [], $expand_parent_targets = true ) {
		$target_id = $this->normalize_target_id( $setting_id, $view, $form );

		if ( '' === $target_id ) {
			return [];
		}

		$form_targets = $this->get_form_field_targets( $form );

		if ( isset( $form_targets[ $target_id ] ) || $this->is_entry_property_target_id( $target_id, $view, $form ) ) {
			return [ $target_id ];
		}

		if ( ! $expand_parent_targets ) {
			return [];
		}

		$parent_id = absint( $target_id );

		if ( ! $parent_id || ! ctype_digit( (string) $target_id ) ) {
			return [];
		}

		$target_ids = [];

		foreach ( $form_targets as $field_target_id => $target ) {
			if ( (string) $parent_id === (string) ( $target['field_id'] ?? '' ) ) {
				$target_ids[] = $field_target_id;
			}
		}

		return $target_ids;
	}

	/**
	 * Returns concrete editable targets for supported Gravity Forms fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $form Gravity Forms form.
	 *
	 * @return array
	 */
	private function get_form_field_targets( array $form ) {
		$targets = [];

		foreach ( $form['fields'] ?? [] as $field ) {
			if ( ! isset( $field->id ) ) {
				continue;
			}

			foreach ( $this->get_field_targets( $field ) as $target_id => $target ) {
				$targets[ $target_id ] = $target;
			}
		}

		return $targets;
	}

	/**
	 * Returns concrete editable targets for one Gravity Forms field.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return array
	 */
	private function get_field_targets( $field ) {
		if ( ! $this->field_passes_bulk_edit_base_safety( $field ) ) {
			return [];
		}

		$type     = $this->get_input_type( $field );
		$field_id = (string) (int) $field->id;

		if ( in_array( $type, [ 'name', 'address' ], true ) ) {
			return $this->get_field_input_targets( $field );
		}

		if ( in_array( $type, [ 'checkbox', 'multiselect' ], true ) ) {
			return ! empty( $field->choices ) && is_array( $field->choices )
				? [
					$field_id => [
						'kind'     => self::TARGET_KIND_FIELD,
						'field'    => clone $field,
						'field_id' => $field_id,
						'label'    => $this->get_field_label( $field ),
					],
				]
				: [];
		}

			if ( [] !== $this->get_field_entry_inputs( $field ) && 'radio' !== $type ) {
				return [];
			}

			if ( in_array( $type, [ 'text', 'textarea', 'email', 'phone', 'website', 'number', 'date', 'time', 'select', 'radio' ], true ) ) {
				return [
					$field_id => [
						'kind'     => self::TARGET_KIND_FIELD,
					'field'    => clone $field,
					'field_id' => $field_id,
					'label'    => $this->get_field_label( $field ),
				],
			];
		}

		return [];
	}

	/**
	 * Returns editable input targets for a complex field.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return array
	 */
	private function get_field_input_targets( $field ) {
		$targets = [];

		foreach ( $this->get_field_entry_inputs( $field ) as $input ) {
			$input_id = $this->get_input_id( $input );

			if ( '' === $input_id || $this->field_input_is_hidden( $input ) || ! $this->field_input_is_supported( $field, $input_id ) ) {
				continue;
			}

			$target_id = 'input:' . $input_id;

			$targets[ $target_id ] = [
				'kind'        => self::TARGET_KIND_FIELD_INPUT,
				'field'       => clone $field,
				'field_id'    => (string) (int) $field->id,
				'input_id'    => $input_id,
				'input_label' => $this->get_input_label( $input, $input_id ),
				'label'       => sprintf(
					/* translators: 1: field label, 2: input label. */
					__( '%1$s: %2$s', 'gk-gravityview' ),
					$this->get_field_label( $field ),
					$this->get_input_label( $input, $input_id )
				),
			];
		}

		return $targets;
	}

	/**
	 * Returns a target label after Edit Entry settings have been merged into the field.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 *
	 * @return string
	 */
	private function get_merged_target_label( array $target ) {
		$field_label    = $this->get_field_label( $target['field'] );
		$original_label = isset( $target['label'] ) && is_scalar( $target['label'] ) ? (string) $target['label'] : '';

		if ( '' === $field_label ) {
			return $original_label;
		}

		if ( self::TARGET_KIND_FIELD_INPUT !== ( $target['kind'] ?? '' ) ) {
			return $field_label;
		}

		$input_label = (string) ( $target['input_label'] ?? $target['input_id'] ?? '' );

		return sprintf(
			/* translators: 1: field label, 2: input label. */
			__( '%1$s: %2$s', 'gk-gravityview' ),
			$field_label,
			$input_label
		);
	}

	/**
	 * Returns normalized target ID for a field input target.
	 *
	 * @since 3.0.0
	 *
	 * @param string $target_id Submitted target ID.
	 * @param array  $form      Gravity Forms form.
	 *
	 * @return string
	 */
	private function normalize_field_input_target_id( $target_id, array $form = [] ) {
		if ( ! preg_match( '/^(?:input:)?([1-9][0-9]*\\.[0-9]+)$/', (string) $target_id, $matches ) ) {
			return '';
		}

		$target_id = 'input:' . $matches[1];

		return isset( $this->get_form_field_targets( $form )[ $target_id ] ) ? $target_id : '';
	}

	/**
	 * Returns supported entry-property edit targets.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private function get_entry_property_targets( ?View $view = null, array $form = [] ) {
		$built_in_properties = [
			'date_created' => [
				'label'      => __( 'Submission Date', 'gk-gravityview' ),
				'type'       => 'datetime-local',
				'required'   => true,
				'normalizer' => 'normalize_entry_datetime_value',
			],
		];

		/**
		 * Filters supported Bulk Edit entry-property targets.
		 *
		 * Target definitions are keyed by entry property name or `entry:<property>`
		 * target ID. Each target must resolve to an `entry:<property>` ID and provide
		 * a normalizer callback before it can be edited.
		 *
		 * @since 3.0.0
		 *
		 * @param array     $properties Entry-property target definitions.
		 * @param View|null $view       View context, when available.
		 * @param array     $form       Gravity Forms form.
		 */
		$properties = apply_filters( 'gk/gravityview/bulk-actions/edit-entry/entry-property-targets', $built_in_properties, $view, $form );
		$properties = is_array( $properties ) ? $properties : [];

		$targets          = $this->normalize_entry_property_targets( $properties, $view, $form );
		$built_in_targets = $this->normalize_entry_property_targets( $built_in_properties, $view, $form );

		foreach ( $built_in_targets as $target_id => $target ) {
			$targets[ $target_id ] = $target;
		}

		return $targets;
	}

	/**
	 * Normalizes entry-property target definitions.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $properties Entry-property target definitions.
	 * @param View|null $view       View context.
	 * @param array     $form       Gravity Forms form.
	 *
	 * @return array
	 */
	private function normalize_entry_property_targets( array $properties, ?View $view = null, array $form = [] ) {
		$targets = [];

		foreach ( $properties as $property => $target ) {
			if ( ! is_array( $target ) ) {
				continue;
			}

			$property = isset( $target['property'] ) && is_scalar( $target['property'] )
				? (string) $target['property']
				: (string) $property;
			$property = preg_replace( '/^entry:/', '', strtolower( trim( $property ) ) );

			if ( ! preg_match( '/^[a-z0-9_]+$/', $property ) ) {
				continue;
			}

			$type      = isset( $target['type'] ) && is_scalar( $target['type'] ) ? sanitize_key( (string) $target['type'] ) : 'text';
			$type      = in_array(
				$type,
				[ 'text', 'textarea', 'email', 'website', 'number', 'datetime-local', 'select', 'radio' ],
				true
			) ? $type : 'text';
			$target_id = 'entry:' . $property;
			$target    = array_merge(
				$target,
				[
					'property' => $property,
					'label'    => isset( $target['label'] ) && is_scalar( $target['label'] ) && '' !== (string) $target['label'] ? (string) $target['label'] : $property,
					'type'     => $type,
					'required' => ! empty( $target['required'] ),
				]
			);

			$target['capability'] = $this->normalize_entry_property_capability( array_key_exists( 'capability', $target ) ? $target['capability'] : self::ALL_FIELDS_REQUIRED_CAPS );

			foreach ( [ 'normalizer', 'sanitize_callback', 'validate_callback', 'save_callback', 'capability_callback', 'choices_callback' ] as $callback_key ) {
				if ( empty( $target[ $callback_key ] ) || ! $this->is_entry_property_callback( $target[ $callback_key ] ) ) {
					unset( $target[ $callback_key ] );
				}
			}

			if ( empty( $target['normalizer'] ) ) {
				continue;
			}

			$target['choices'] = $this->normalize_entry_property_choices( $target['choices'] ?? ( $target['options'] ?? [] ) );
			$target['validation'] = $this->sanitize_target_validation( $target, (string) $target['type'] );

			$targets[ $target_id ] = $target;
		}

		return $targets;
	}

	/**
	 * Normalizes an entry-property capability value.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $capability Capability or capabilities.
	 *
	 * @return string|string[]
	 */
	private function normalize_entry_property_capability( $capability ) {
		if ( is_array( $capability ) ) {
			$capability = array_map(
				static function ( $cap ) {
					return is_scalar( $cap ) ? sanitize_key( (string) $cap ) : '';
				},
				$capability
			);

			return array_values( array_filter( $capability ) );
		}

		return is_scalar( $capability ) ? sanitize_key( (string) $capability ) : '';
	}

	/**
	 * Resolves dynamic choices for an entry-property target.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $target Entry-property target.
	 * @param View|null $view   View context.
	 * @param array     $form   Gravity Forms form.
	 *
	 * @return array
	 */
	private function resolve_entry_property_choices( array $target, ?View $view = null, array $form = [] ) {
		if ( empty( $target['choices_callback'] ) ) {
			return $target;
		}

		$choices = $this->call_entry_property_callback( $target['choices_callback'], [ $target, $view, $form ] );

		if ( is_array( $choices ) ) {
			$target['choices'] = $this->normalize_entry_property_choices( $choices );
		}

		return $target;
	}

	/**
	 * Normalizes entry-property frontend choices.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $choices Choice definitions.
	 *
	 * @return array
	 */
	private function normalize_entry_property_choices( $choices ) {
		if ( ! is_array( $choices ) ) {
			return [];
		}

		$normalized = [];
		$is_list    = array_values( $choices ) === $choices;

		foreach ( $choices as $value => $choice ) {
			if ( is_array( $choice ) ) {
				$value = $choice['value'] ?? $value;
				$label = $choice['label'] ?? $value;
			} else {
				if ( $is_list ) {
					$value = $choice;
				}

				$label = $choice;
			}

			if ( ! is_scalar( $value ) || ! is_scalar( $label ) ) {
				continue;
			}

			$value = sanitize_text_field( wp_unslash( (string) $value ) );
			$label = sanitize_text_field( wp_unslash( (string) $label ) );

			if ( '' === $value ) {
				continue;
			}

			$normalized[] = [
				'value' => $value,
				'label' => '' === $label ? $value : $label,
			];
		}

		return $normalized;
	}

	/**
	 * Returns whether an entry-property callback can be called.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $callback Callback.
	 *
	 * @return bool
	 */
	private function is_entry_property_callback( $callback ) {
		return is_callable( $callback ) || ( is_string( $callback ) && method_exists( $this, $callback ) );
	}

	/**
	 * Calls an entry-property callback.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $callback Callback.
	 * @param array $args     Callback arguments.
	 *
	 * @return mixed
	 */
	private function call_entry_property_callback( $callback, array $args = [] ) {
		if ( is_string( $callback ) && method_exists( $this, $callback ) ) {
			return call_user_func_array( [ $this, $callback ], $args );
		}

		return is_callable( $callback ) ? call_user_func_array( $callback, $args ) : null;
	}

	/**
	 * Returns whether a target ID points to a supported entry property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $target_id Target ID.
	 *
	 * @return bool
	 */
	private function is_entry_property_target_id( $target_id, ?View $view = null, array $form = [] ) {
		return isset( $this->get_entry_property_targets( $view, $form )[ $target_id ] );
	}

	/**
	 * Returns snapshotted target IDs for a queued or already-sanitized action.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return string[]|null
	 */
	private function get_resolved_field_ids_snapshot( array $action, ?View $view = null, array $form = [] ) {
		if ( array_key_exists( 'request', $action ) && is_array( $action['request'] ) ) {
			return array_key_exists( self::RESOLVED_FIELD_IDS_SETTING, $action['request'] )
				? $this->normalize_target_ids( $action['request'][ self::RESOLVED_FIELD_IDS_SETTING ], $view, $form )
				: null;
		}

		if ( isset( $action['settings'][ self::RESOLVED_FIELD_IDS_SETTING ] ) ) {
			return $this->normalize_target_ids( $action['settings'][ self::RESOLVED_FIELD_IDS_SETTING ], $view, $form );
		}

		return null;
	}

	/**
	 * Applies the Bulk Edit source target IDs filter as a narrowing step.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $candidate_ids   Candidate target IDs.
	 * @param string   $mode            Field source mode.
	 * @param array    $action_settings Resolved action settings.
	 * @param View     $view            View.
	 * @param array    $form            Gravity Forms form.
	 *
	 * @return string[]
	 */
	private function filter_source_field_ids( array $candidate_ids, $mode, array $action_settings, View $view, array $form ) {
		/**
		 * Filters Bulk Edit source target IDs after mode resolution.
		 *
		 * The returned IDs are intersected with the mode-resolved candidate IDs
		 * and then passed through Bulk Edit safety and permission checks.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $field_ids       Candidate target IDs.
		 * @param string   $mode            Field source mode.
		 * @param array    $action_settings Resolved action settings.
		 * @param View     $view            View.
		 * @param array    $form            Gravity Forms form.
		 */
		$filtered = apply_filters(
			'gk/gravityview/bulk-actions/edit-entry/source-field-ids',
			$candidate_ids,
			$mode,
			$action_settings,
			$view,
			$form
		);

		$filtered = $this->normalize_target_ids( $filtered, $view, $form );

		return array_values( array_intersect( $candidate_ids, $filtered ) );
	}

	/**
	 * Returns fields explicitly configured in the View's Edit Entry layout.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array
	 */
	private function get_configured_edit_field_settings( View $view ) {
		$properties = $view->fields ? $view->fields->as_configuration() : [];
		$settings   = $properties['edit_edit-fields'] ?? [];

		return is_array( $settings ) ? array_values( $settings ) : [];
	}

	/**
	 * Returns the Gravity Forms form array for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array|null
	 */
	private function get_form( View $view ) {
		if ( $view->form && ! empty( $view->form->form ) && is_array( $view->form->form ) ) {
			return $view->form->form;
		}

		if ( $view->form && ! empty( $view->form->ID ) ) {
			$form = GVCommon::get_form( (int) $view->form->ID );

			return is_array( $form ) ? $form : null;
		}

		return null;
	}

	/**
	 * Whether the current user can edit entries for the View in general.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	private function current_user_can_edit_entries( View $view ) {
		if ( GVCommon::has_cap( [ 'gravityforms_edit_entries', 'gravityview_edit_others_entries' ] ) ) {
			return true;
		}

		return is_user_logged_in() && (bool) $view->settings->get( 'user_edit' );
	}

	/**
	 * Whether the current user can use the all-fields source.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function current_user_can_edit_all_fields() {
		return GVCommon::has_cap( self::ALL_FIELDS_REQUIRED_CAPS );
	}

	/**
	 * Whether the current user can edit one entry property target.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $target Entry-property target.
	 * @param View|null $view   View.
	 * @param array     $entry  Entry.
	 *
	 * @return bool
	 */
	private function current_user_can_edit_entry_property( array $target, ?View $view = null, array $entry = [] ) {
		if ( $this->entry_property_requires_sensitive_cap( $target ) && ! GVCommon::has_cap( self::SENSITIVE_PROPERTY_CAP, $view ? (int) $view->ID : null, get_current_user_id() ) ) {
			return false;
		}

		if ( ! empty( $target['capability_callback'] ) ) {
			$result = $this->call_entry_property_callback( $target['capability_callback'], [ $target, $view, $entry ] );

			return ! is_wp_error( $result ) && (bool) $result;
		}

		$capability = $target['capability'] ?? self::ALL_FIELDS_REQUIRED_CAPS;

		if ( empty( $capability ) ) {
			return false;
		}

		return GVCommon::has_cap( $capability, $view ? (int) $view->ID : null, get_current_user_id() );
	}

	/**
	 * Whether an entry property has a non-overridable capability floor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Entry-property target.
	 *
	 * @return bool
	 */
	private function entry_property_requires_sensitive_cap( array $target ) {
		$property = isset( $target['property'] ) && is_scalar( $target['property'] )
			? sanitize_key( (string) $target['property'] )
			: '';

		return in_array( $property, self::SENSITIVE_ENTRY_PROPERTIES, true );
	}

	/**
	 * Whether the current user can edit a resolved Bulk Edit target.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 * @param View  $view   View.
	 * @param array $entry  Entry.
	 *
	 * @return bool
	 */
	private function user_can_edit_target( array $target, View $view, array $entry = [] ) {
		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return $this->current_user_can_edit_entry_property( $target, $view, $entry );
		}

		return $this->user_can_edit_field( $target['setting'] ?? [], $view, $entry );
	}

	/**
	 * Whether the current user can edit a configured field.
	 *
	 * @since 3.0.0
	 *
	 * @param array $setting Field setting.
	 * @param View  $view    View.
	 * @param array $entry   Entry.
	 *
	 * @return bool
	 */
	private function user_can_edit_field( array $setting, View $view, array $entry = [] ) {
		if ( GVCommon::has_cap( [ 'gravityforms_edit_entries', 'gravityview_edit_others_entries' ] ) ) {
			return true;
		}

		$cap     = $setting['allow_edit_cap'] ?? '';
		$has_cap = $cap ? GVCommon::has_cap( $cap, null, get_current_user_id() ) : false;

		/**
		 * Filters whether the current user can bulk-edit a configured field.
		 *
		 * This mirrors Edit Entry's field-level capability check.
		 *
		 * @since 3.0.0
		 *
		 * @param bool  $has_cap Whether the current user has the field capability.
		 * @param array $setting View field settings.
		 * @param array $entry   Entry being edited, or empty during action rendering.
		 * @param View  $view    View.
		 */
		return (bool) apply_filters( 'gk/gravityview/edit-entry/user-can-edit-field', $has_cap, $setting, $entry, $view );
	}

	/**
	 * Whether Edit Entry locking prevents the bulk edit from updating an entry.
	 *
	 * @since 3.0.0
	 *
	 * @param int  $entry_id             Entry ID.
	 * @param View $view                 View.
	 * @param bool $include_current_user Whether the current user's own lock should also block editing.
	 *
	 * @return bool
	 */
	private function entry_is_locked( $entry_id, View $view, $include_current_user = false ) {
		if ( ! $view->settings->get( 'edit_locking' ) || ! class_exists( 'GravityView_Edit_Entry_Locking' ) ) {
			return false;
		}

		$locking = new GravityView_Edit_Entry_Locking();

		if ( $include_current_user && method_exists( $locking, 'get_lock_meta' ) ) {
			return (bool) $locking->get_lock_meta( (int) $entry_id );
		}

		return (bool) $locking->check_lock( (int) $entry_id );
	}

	/**
	 * Whether a field may be exposed through Bulk Edit.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return bool
	 */
	private function is_bulk_edit_field_safe( $field ) {
		return [] !== $this->get_field_targets( $field );
	}

	/**
	 * Whether a field passes safety checks shared by all field target types.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return bool
	 */
	private function field_passes_bulk_edit_base_safety( $field ) {
		if ( isset( $field->visibility ) && 'hidden' === $field->visibility ) {
			return false;
		}

		$is_admin_only = ! empty( $field->adminOnly )
			|| ( isset( $field->visibility ) && 'administrative' === $field->visibility )
			|| ( method_exists( $field, 'is_administrative' ) && $field->is_administrative() );

		if ( $is_admin_only && ! GVCommon::has_cap( self::ADMIN_ONLY_REQUIRED_CAP ) ) {
			return false;
		}

		$type      = $this->get_input_type( $field );
		$blocklist = GravityView_Edit_Entry::getInstance()->get_field_blocklist();

		if ( in_array( $type, $blocklist, true ) || ( method_exists( $field, 'has_calculation' ) && $field->has_calculation() ) ) {
			return false;
		}

		if ( class_exists( 'GFCommon' ) && method_exists( 'GFCommon', 'is_post_field' ) && GFCommon::is_post_field( $field ) ) {
			return false;
		}

		if ( in_array( $field->type ?? '', [ 'fileupload', 'product', 'option', 'quantity', 'shipping', 'total' ], true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns a field's entry inputs.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return array
	 */
	private function get_field_entry_inputs( $field ) {
		$inputs = method_exists( $field, 'get_entry_inputs' ) ? $field->get_entry_inputs() : ( $field->inputs ?? [] );

		return is_array( $inputs ) ? $inputs : [];
	}

	/**
	 * Returns one input ID as stored by Gravity Forms.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Field input definition.
	 *
	 * @return string
	 */
	private function get_input_id( array $input ) {
		if ( empty( $input['id'] ) || ! is_scalar( $input['id'] ) ) {
			return '';
		}

		$input_id = trim( (string) $input['id'] );

		return preg_match( '/^[1-9][0-9]*\\.[0-9]+$/', $input_id ) ? $input_id : '';
	}

	/**
	 * Whether a field input is hidden in the form configuration.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Field input definition.
	 *
	 * @return bool
	 */
	private function field_input_is_hidden( array $input ) {
		return ! empty( $input['isHidden'] ) || ! empty( $input['is_hidden'] );
	}

	/**
	 * Whether one complex field input can be safely edited as a text target.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field    Gravity Forms field.
	 * @param string    $input_id Input ID.
	 *
	 * @return bool
	 */
	private function field_input_is_supported( $field, $input_id ) {
		$type = $this->get_input_type( $field );

		if ( 'name' === $type ) {
			return true;
		}

		if ( 'address' === $type && preg_match( '/^[1-9][0-9]*\\.(1|2|3|5)$/', (string) $input_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns a readable field input label.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $input    Field input definition.
	 * @param string $input_id Input ID.
	 *
	 * @return string
	 */
	private function get_input_label( array $input, $input_id ) {
		foreach ( [ 'customLabel', 'label', 'name' ] as $key ) {
			if ( isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) && '' !== (string) $input[ $key ] ) {
				return (string) $input[ $key ];
			}
		}

		return (string) $input_id;
	}

	/**
	 * Whether a field input is required by its parent field.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field    Gravity Forms field.
	 * @param string    $input_id Input ID.
	 *
	 * @return bool
	 */
	private function field_input_is_required( $field, $input_id ) {
		if ( empty( $field->isRequired ) || ! method_exists( $field, 'get_required_inputs_ids' ) || ! preg_match( '/^[1-9][0-9]*\\.([0-9]+)$/', $input_id, $matches ) ) {
			return false;
		}

		return in_array( (string) $matches[1], array_map( 'strval', (array) $field->get_required_inputs_ids() ), true );
	}

	/**
	 * Returns whether the form has conditional logic.
	 *
	 * @since 3.0.0
	 *
	 * @param array $form Gravity Forms form.
	 *
	 * @return bool
	 */
	private function form_has_conditional_logic( array $form ) {
		if ( ! empty( $form['button']['conditionalLogic'] ) ) {
			return true;
		}

		foreach ( $form['fields'] ?? [] as $field ) {
			if ( ! empty( $field->conditionalLogic ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Applies the existing Edit Entry field visibility filter.
	 *
	 * @since 3.0.0
	 *
	 * @param array $editable  Editable fields keyed by field ID.
	 * @param array $settings  Edit Entry field settings.
	 * @param array $form      Gravity Forms form.
	 * @param int   $view_id   View ID.
	 *
	 * @return array
	 */
	private function filter_edit_entry_fields( array $editable, array $settings, array $form, $view_id ) {
		$fields = array_map(
			static function ( $data ) {
				return $data['field'] ?? null;
			},
			array_filter(
				$editable,
				static function ( $data ) {
					return in_array( $data['kind'] ?? '', [ self::TARGET_KIND_FIELD, self::TARGET_KIND_FIELD_INPUT ], true ) && ! empty( $data['field'] );
				}
			)
		);

		$filtered = apply_filters( 'gravityview/edit_entry/form_fields', array_values( $fields ), $settings, $form, (int) $view_id );

		if ( ! is_array( $filtered ) ) {
			return [];
		}

		$allowed = [];

		foreach ( $filtered as $field ) {
			if ( isset( $field->id ) ) {
				$allowed[ (string) (int) $field->id ] = true;
			}
		}

		$filtered_editable = [];

		foreach ( $editable as $target_id => $data ) {
			if ( in_array( $data['kind'] ?? '', [ self::TARGET_KIND_FIELD, self::TARGET_KIND_FIELD_INPUT ], true ) ) {
				$parent_id = (string) ( $data['field_id'] ?? $target_id );

				if ( empty( $allowed[ $parent_id ] ) ) {
					continue;
				}
			}

			if ( self::TARGET_KIND_FIELD === ( $data['kind'] ?? '' ) && empty( $allowed[ $target_id ] ) ) {
				continue;
			}

			$filtered_editable[ $target_id ] = $data;
		}

		return $filtered_editable;
	}

	/**
	 * Returns a target label.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 *
	 * @return string
	 */
	private function get_target_label( array $target ) {
		if ( isset( $target['label'] ) && is_scalar( $target['label'] ) && '' !== (string) $target['label'] ) {
			return (string) $target['label'];
		}

		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return (string) ( $target['label'] ?? $target['property'] ?? '' );
		}

		return $this->get_field_label( $target['field'] );
	}

	/**
	 * Returns a target input type.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 *
	 * @return string
	 */
	private function get_target_input_type( array $target ) {
		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return (string) ( $target['type'] ?? 'text' );
		}

		if ( self::TARGET_KIND_FIELD_INPUT === ( $target['kind'] ?? '' ) ) {
			return 'text';
		}

		$type = $this->get_input_type( $target['field'] );

		return in_array( $type, [ 'checkbox', 'multiselect' ], true ) ? 'multiselect' : $type;
	}

	/**
	 * Returns whether a target is required.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 *
	 * @return bool
	 */
	private function target_is_required( array $target ) {
		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return ! empty( $target['required'] );
		}

		if ( self::TARGET_KIND_FIELD_INPUT === ( $target['kind'] ?? '' ) ) {
			return $this->field_input_is_required( $target['field'], (string) ( $target['input_id'] ?? '' ) );
		}

		return ! empty( $target['field']->isRequired );
	}

	/**
	 * Returns choices for frontend target controls.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 *
	 * @return array
	 */
	private function get_target_frontend_choices( array $target ) {
		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return isset( $target['choices'] ) && is_array( $target['choices'] ) ? $target['choices'] : [];
		}

		return $this->get_frontend_choices( $target['field'] );
	}

	/**
	 * Returns HTML validation metadata for frontend target controls.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $target Resolved target.
	 *
	 * @return array
	 */
	private function get_target_validation( array $target ) {
		$type = $this->get_target_input_type( $target );

		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return $this->sanitize_target_validation( $target['validation'] ?? [], $type );
		}

		$field      = $target['field'];
		$validation = [];

		if ( isset( $field->maxLength ) && '' !== (string) $field->maxLength ) {
			$validation['maxlength'] = $field->maxLength;
		}

		if ( 'number' === $type ) {
			if ( isset( $field->rangeMin ) && '' !== (string) $field->rangeMin ) {
				$validation['min'] = $field->rangeMin;
			}

			if ( isset( $field->rangeMax ) && '' !== (string) $field->rangeMax ) {
				$validation['max'] = $field->rangeMax;
			}

			if ( isset( $field->numberFormat ) && in_array( $field->numberFormat, [ 'decimal_dot', 'decimal_comma', 'currency' ], true ) ) {
				$validation['step'] = 'any';
			}
		}

		return $this->sanitize_target_validation( $validation, $type );
	}

	/**
	 * Sanitizes HTML validation metadata for frontend target controls.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $source Validation metadata source.
	 * @param string $type   Target input type.
	 *
	 * @return array
	 */
	private function sanitize_target_validation( array $source, $type ) {
		if ( isset( $source['validation'] ) && is_array( $source['validation'] ) ) {
			$source = array_merge( $source, $source['validation'] );
		}

		$type       = sanitize_key( (string) $type );
		$validation = [];

		if ( 'text' === $type && array_key_exists( 'pattern', $source ) ) {
			$pattern = $this->sanitize_validation_pattern( $source['pattern'] );

			if ( '' !== $pattern ) {
				$validation['pattern'] = $pattern;
			}
		}

		if ( array_key_exists( 'validation_message', $source ) ) {
			$message = $this->sanitize_validation_message( $source['validation_message'] );

			if ( '' !== $message ) {
				$validation['validation_message'] = $message;
			}
		}

		if ( in_array( $type, [ 'text', 'textarea', 'email', 'website' ], true ) && array_key_exists( 'maxlength', $source ) ) {
			$maxlength = $this->sanitize_validation_maxlength( $source['maxlength'] );

			if ( null !== $maxlength ) {
				$validation['maxlength'] = $maxlength;
			}
		}

		if ( 'number' === $type ) {
			foreach ( [ 'min', 'max' ] as $key ) {
				if ( ! array_key_exists( $key, $source ) ) {
					continue;
				}

				$number = $this->sanitize_validation_number( $source[ $key ] );

				if ( null !== $number ) {
					$validation[ $key ] = $number;
				}
			}

			if ( array_key_exists( 'step', $source ) ) {
				$step = $this->sanitize_validation_step( $source['step'] );

				if ( null !== $step ) {
					$validation['step'] = $step;
				}
			}
		}

		return $validation;
	}

	/**
	 * Sanitizes an HTML pattern attribute.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $pattern Pattern.
	 *
	 * @return string
	 */
	private function sanitize_validation_pattern( $pattern ) {
		if ( ! is_scalar( $pattern ) ) {
			return '';
		}

		$pattern = wp_check_invalid_utf8( (string) wp_unslash( $pattern ) );

		if ( '' === $pattern ) {
			return '';
		}

		$pattern = wp_strip_all_tags( $pattern );
		$pattern = preg_replace( '/[\x00-\x1F\x7F]/', '', $pattern );
		$pattern = trim( is_string( $pattern ) ? $pattern : '' );

		return '' !== $pattern && strlen( $pattern ) <= 256 ? $pattern : '';
	}

	/**
	 * Sanitizes an HTML validation message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $message Message.
	 *
	 * @return string
	 */
	private function sanitize_validation_message( $message ) {
		if ( ! is_scalar( $message ) ) {
			return '';
		}

		$message = wp_check_invalid_utf8( (string) wp_unslash( $message ) );
		$message = '' === $message ? '' : sanitize_text_field( wp_strip_all_tags( $message ) );

		return function_exists( 'mb_substr' )
			? mb_substr( $message, 0, 200, 'UTF-8' )
			: ( preg_match( '/^.{0,200}/us', $message, $matches ) ? $matches[0] : '' );
	}

	/**
	 * Sanitizes an HTML numeric validation attribute.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $number Number.
	 *
	 * @return string|null
	 */
	private function sanitize_validation_number( $number ) {
		if ( is_bool( $number ) || ! is_scalar( $number ) ) {
			return null;
		}

		$number = trim( (string) $number );

		return '' !== $number && is_numeric( $number ) ? (string) (float) $number : null;
	}

	/**
	 * Sanitizes an HTML step attribute.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $step Step.
	 *
	 * @return string|null
	 */
	private function sanitize_validation_step( $step ) {
		if ( is_scalar( $step ) && 'any' === strtolower( trim( (string) $step ) ) ) {
			return 'any';
		}

		$step = $this->sanitize_validation_number( $step );

		return null !== $step && (float) $step > 0 ? $step : null;
	}

	/**
	 * Sanitizes an HTML maxlength attribute.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $maxlength Maximum length.
	 *
	 * @return int|null
	 */
	private function sanitize_validation_maxlength( $maxlength ) {
		if ( is_bool( $maxlength ) || ! is_scalar( $maxlength ) ) {
			return null;
		}

		$maxlength = absint( $maxlength );

		if ( $maxlength <= 0 ) {
			return null;
		}

		return min( $maxlength, 10000 );
	}

	/**
	 * Returns a field input type.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return string
	 */
	private function get_input_type( $field ) {
		if ( class_exists( 'GFFormsModel' ) ) {
			return (string) GFFormsModel::get_input_type( $field );
		}

		return method_exists( $field, 'get_input_type' ) ? (string) $field->get_input_type() : (string) ( $field->type ?? '' );
	}

	/**
	 * Returns sanitized submitted value for a field.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 * @param mixed     $value Submitted value.
	 *
	 * @return string|array
	 */
	private function sanitize_value( $field, $value ) {
		$type  = $this->get_input_type( $field );

		if ( in_array( $type, [ 'checkbox', 'multiselect' ], true ) ) {
			$values = is_array( $value ) ? $value : ( is_scalar( $value ) && '' !== (string) $value ? [ $value ] : [] );

			return array_values(
				array_filter(
					array_map(
						static function ( $item ) {
							return is_scalar( $item ) ? sanitize_text_field( (string) $item ) : '';
						},
						$values
					),
					static function ( $item ) {
						return '' !== $item;
					}
				)
			);
		}

		$value = is_scalar( $value ) ? (string) $value : '';

		if ( 'textarea' === $type ) {
			return sanitize_textarea_field( $value );
		}

		if ( 'website' === $type ) {
			return esc_url_raw( $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Returns sanitized submitted value for a target.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved target.
	 * @param mixed $value  Submitted value.
	 *
	 * @return string|array|WP_Error
	 */
	private function sanitize_target_value( array $target, $value, array $form = [], array $entry = [], ?View $view = null ) {
		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			$value = is_scalar( $value ) ? wp_unslash( (string) $value ) : '';

			if ( ! empty( $target['sanitize_callback'] ) ) {
				$value = $this->call_entry_property_callback( $target['sanitize_callback'], [ $value, $target, $form, $entry, $view ] );

				if ( is_wp_error( $value ) ) {
					return $value;
				}
			}

			return sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
		}

		return $this->sanitize_value( $target['field'], $value );
	}

	/**
	 * Whether a submitted target value is empty.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Value.
	 *
	 * @return bool
	 */
	private function target_value_is_empty( $value ) {
		if ( is_array( $value ) ) {
			return [] === array_filter(
				$value,
				static function ( $item ) {
					return is_scalar( $item ) && '' !== trim( (string) $item );
				}
			);
		}

		return ! is_scalar( $value ) || '' === trim( (string) $value );
	}

	/**
	 * Validates one target change against one entry.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $target  Resolved target.
	 * @param array  $change  Sanitized change.
	 * @param array  $form    Gravity Forms form.
	 * @param array  $entry   Entry.
	 * @param string $context Gravity Forms validation context.
	 *
	 * @return true|WP_Error
	 */
	private function validate_target_change( array $target, array $change, array $form, array $entry, $context = 'api-submit', ?View $view = null ) {
		if ( self::TARGET_KIND_ENTRY_PROPERTY === ( $target['kind'] ?? '' ) ) {
			return $this->validate_entry_property_change( $target, $change, $form, $entry, $view );
		}

		if ( self::TARGET_KIND_FIELD_INPUT === ( $target['kind'] ?? '' ) ) {
			return $this->validate_field_input_change( $target, $change, $form, $entry, $context );
		}

		return $this->validate_change( clone $target['field'], $change, $form, $entry, $context );
	}

	/**
	 * Validates one entry-property change.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $target Entry property target.
	 * @param array     $change Sanitized change.
	 * @param array     $form   Gravity Forms form.
	 * @param array     $entry  Entry.
	 * @param View|null $view   View.
	 *
	 * @return true|WP_Error
	 */
	private function validate_entry_property_change( array $target, array $change, array $form, array $entry, ?View $view = null ) {
		if ( empty( $target['property'] ) ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
		}

		if ( 'clear' === ( $change['operation'] ?? '' ) && ! empty( $target['required'] ) ) {
			return new WP_Error( 'gravityview_bulk_edit_required_field', __( 'Required fields cannot be cleared.', 'gk-gravityview' ) );
		}

		$value = $this->get_entry_property_save_value( $target, $change['value'] ?? '', $change, $form, $entry, $view );

		if ( is_wp_error( $value ) ) {
			return $value;
		}

		if ( in_array( (string) ( $target['type'] ?? '' ), [ 'select', 'radio' ], true ) && ! $this->is_valid_entry_property_choice( $target, $value ) ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_choice', __( 'The selected field value is not valid.', 'gk-gravityview' ) );
		}

		if ( ! empty( $target['validate_callback'] ) ) {
			$validation = $this->call_entry_property_callback( $target['validate_callback'], [ $value, $target, $change, $form, $entry, $view ] );

			if ( is_wp_error( $validation ) ) {
				return $validation;
			}

			if ( false === $validation ) {
				return new WP_Error( 'gravityview_bulk_edit_validation_failed', __( 'A field value did not pass validation.', 'gk-gravityview' ) );
			}
		}

		return true;
	}

	/**
	 * Returns whether an entry-property value is one of the configured choices.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Entry-property target.
	 * @param mixed $value  Normalized value.
	 *
	 * @return bool
	 */
	private function is_valid_entry_property_choice( array $target, $value ) {
		$choices = isset( $target['choices'] ) && is_array( $target['choices'] ) ? $target['choices'] : [];

		if ( [] === $choices ) {
			return true;
		}

		$values = array_map(
			static function ( $choice ) {
				return isset( $choice['value'] ) ? (string) $choice['value'] : '';
			},
			$choices
		);

		return in_array( (string) $value, $values, true );
	}

	/**
	 * Validates one complex-field input change against one entry.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $target  Resolved field input target.
	 * @param array  $change  Sanitized change.
	 * @param array  $form    Gravity Forms form.
	 * @param array  $entry   Entry.
	 * @param string $context Gravity Forms validation context.
	 *
	 * @return true|WP_Error
	 */
	private function validate_field_input_change( array $target, array $change, array $form, array $entry, $context = 'api-submit' ) {
		$field    = clone $target['field'];
		$input_id = (string) ( $target['input_id'] ?? '' );
		$value    = 'clear' === ( $change['operation'] ?? '' ) ? '' : (string) ( $change['value'] ?? '' );

		if ( '' === $value && $this->field_input_is_required( $field, $input_id ) ) {
			return new WP_Error( 'gravityview_bulk_edit_required_field', __( 'A required field cannot be cleared.', 'gk-gravityview' ) );
		}

		$change['value'] = $this->get_field_input_validation_value( $field, $entry, $input_id, $value );

		return $this->validate_change( $field, $change, $form, $entry, $context );
	}

	/**
	 * Validates one field change against one entry.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field  Gravity Forms field.
	 * @param array     $change Sanitized change.
	 * @param array     $form   Gravity Forms form.
	 * @param array     $entry  Entry.
	 * @param string    $context Gravity Forms validation context.
	 *
	 * @return true|WP_Error
	 */
		private function validate_change( $field, array $change, array $form, array $entry, $context = 'api-submit' ) {
			$value   = 'clear' === ( $change['operation'] ?? '' ) && ! array_key_exists( 'value', $change )
				? ( in_array( $this->get_input_type( $field ), [ 'checkbox', 'multiselect' ], true ) ? [] : '' )
				: ( $change['value'] ?? '' );
			$context = in_array( $context, [ 'api-validate', 'api-submit', 'form-submit' ], true ) ? $context : 'api-submit';

		if ( $this->target_value_is_empty( $value ) && ! empty( $field->isRequired ) ) {
			return new WP_Error( 'gravityview_bulk_edit_required_field', __( 'A required field cannot be cleared.', 'gk-gravityview' ) );
		}

		if ( in_array( $this->get_input_type( $field ), [ 'select', 'radio' ], true ) && ! $this->target_value_is_empty( $value ) && ! $this->is_valid_choice( $field, $value ) ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_choice', __( 'The selected field value is not valid.', 'gk-gravityview' ) );
		}

		if ( in_array( $this->get_input_type( $field ), [ 'checkbox', 'multiselect' ], true ) && ! $this->target_value_is_empty( $value ) && ! $this->are_valid_choices( $field, $value ) ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_choice', __( 'The selected field value is not valid.', 'gk-gravityview' ) );
		}

			$field->failed_validation  = false;
			$field->validation_message = '';
			$validation_value          = 'checkbox' === $this->get_input_type( $field ) && is_array( $value )
				? $this->get_checkbox_validation_value( $field, $value )
				: $value;

			try {
				if ( method_exists( $field, 'validate' ) ) {
					$field->validate( $validation_value, $form );
				}

				$result = [
				'is_valid' => empty( $field->failed_validation ),
				'message'  => $field->validation_message,
				];

				if ( class_exists( 'GFFormDisplay' ) && method_exists( 'GFFormDisplay', 'validate_character_encoding' ) ) {
					$result = GFFormDisplay::validate_character_encoding( $result, $validation_value, $field );
				}

				if ( function_exists( 'gf_apply_filters' ) ) {
					$result = gf_apply_filters( [ 'gform_field_validation', $form['id'], $field->id ], $result, $validation_value, $form, $field, $context );
				} else {
					$result = apply_filters( 'gform_field_validation', $result, $validation_value, $form, $field, $context );
				}
			} catch ( Throwable $e ) {
				$this->log_exception( 'Bulk Edit field validation failed unexpectedly.', $e, [ 'field_id' => $field->id ?? 0 ] );

			return new WP_Error( 'gravityview_bulk_edit_validation_failed', __( 'A field value did not pass validation.', 'gk-gravityview' ) );
		}

		$field->failed_validation  = ! (bool) ( $result['is_valid'] ?? true );
		$field->validation_message = (string) ( $result['message'] ?? $field->validation_message );

		if ( ! empty( $field->failed_validation ) ) {
			return new WP_Error( 'gravityview_bulk_edit_validation_failed', $field->validation_message ? $field->validation_message : __( 'A field value did not pass validation.', 'gk-gravityview' ) );
		}

		return true;
	}

	/**
	 * Returns the value stored in the entry for a change.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 * @param string    $value Submitted value.
	 * @param array     $form  Gravity Forms form.
	 * @param array     $entry Entry.
	 *
	 * @return mixed
	 */
	private function get_save_value( $field, $value, array $form, array $entry ) {
		$input_name = 'input_' . str_replace( '.', '_', (string) $field->id );

		if ( method_exists( $field, 'get_value_save_input' ) ) {
			return $field->get_value_save_input( $value, $form, $input_name, (int) $entry['id'], $entry );
		}

		if ( method_exists( $field, 'get_value_save_entry' ) ) {
			return $field->get_value_save_entry( $value, $form, $input_name, (int) $entry['id'], $entry );
		}

		return $value;
	}

	/**
	 * Returns the value stored for one field input target.
	 *
	 * @since 3.0.0
	 *
	 * @param array $target Resolved field input target.
	 * @param mixed $value  Submitted value.
	 * @param array $form   Gravity Forms form.
	 * @param array $entry  Entry.
	 *
	 * @return string
	 */
	private function get_field_input_save_value( array $target, $value, array $form, array $entry ) {
		unset( $form, $entry );

		$field = $target['field'] ?? null;

		return is_object( $field ) ? (string) $this->sanitize_value( $field, $value ) : sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
	}

	/**
	 * Returns the value stored in the entry for an entry-property change.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $target Entry property target.
	 * @param mixed     $value  Submitted value.
	 * @param array     $change Sanitized change.
	 * @param array     $form   Gravity Forms form.
	 * @param array     $entry  Entry.
	 * @param View|null $view   View.
	 *
	 * @return mixed|WP_Error
	 */
	private function get_entry_property_save_value( array $target, $value, array $change = [], array $form = [], array $entry = [], ?View $view = null ) {
		$normalizer = $target['normalizer'] ?? null;

		if ( ! $normalizer || ! $this->is_entry_property_callback( $normalizer ) ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
		}

		$value = $this->call_entry_property_callback( $normalizer, [ $value, $target, $change, $form, $entry, $view ] );

		if ( is_wp_error( $value ) ) {
			return $value;
		}

		if ( ! is_scalar( $value ) && null !== $value ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
		}

		return $value;
	}

	/**
	 * Normalizes a site-local entry datetime to the UTC storage format used by Gravity Forms.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed     $value  Submitted value.
	 * @param array     $target Entry property target.
	 * @param array     $change Sanitized change.
	 * @param array     $form   Gravity Forms form.
	 * @param array     $entry  Entry.
	 * @param View|null $view   View.
	 *
	 * @return string|WP_Error
	 */
	private function normalize_entry_datetime_value( $value, array $target = [], array $change = [], array $form = [], array $entry = [], ?View $view = null ) {
		unset( $target, $change, $form, $entry, $view );

		$value = is_scalar( $value ) ? trim( str_replace( 'T', ' ', (string) $value ) ) : '';

		if ( '' === $value ) {
			return new WP_Error( 'gravityview_bulk_edit_invalid_entry_date', __( 'Enter a valid submission date.', 'gk-gravityview' ) );
		}

		$formats  = [ 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d' ];
		$timezone = $this->get_site_timezone();

		foreach ( $formats as $format ) {
			$date   = \DateTimeImmutable::createFromFormat( '!' . $format, $value, $timezone );
			$errors = \DateTimeImmutable::getLastErrors();

			if ( ! $date || ( false !== $errors && ( ! empty( $errors['warning_count'] ) || ! empty( $errors['error_count'] ) ) ) ) {
				continue;
			}

			if ( $date->format( $format ) !== $value ) {
				continue;
			}

			return $date->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
		}

		return new WP_Error( 'gravityview_bulk_edit_invalid_entry_date', __( 'Enter a valid submission date.', 'gk-gravityview' ) );
	}

	/**
	 * Returns the site timezone.
	 *
	 * @since 3.0.0
	 *
	 * @return \DateTimeZone
	 */
	private function get_site_timezone() {
		if ( function_exists( 'wp_timezone' ) ) {
			return wp_timezone();
		}

		$timezone_string = function_exists( 'get_option' ) ? get_option( 'timezone_string' ) : '';

		if ( $timezone_string ) {
			try {
				return new \DateTimeZone( $timezone_string );
			} catch ( \Exception $e ) {
				return new \DateTimeZone( 'UTC' );
			}
		}

		return new \DateTimeZone( 'UTC' );
	}

	/**
	 * Updates all inputs for one checkbox field.
	 *
	 * @since 3.0.0
	 *
	 * @param int       $entry_id Entry ID.
	 * @param \GF_Field $field    Checkbox field.
	 * @param array     $values   Selected values.
	 *
	 * @return true|WP_Error|false
	 */
		private function update_checkbox_field( $entry_id, $field, array $values ) {
			$validation_value = $this->get_checkbox_validation_value( $field, $values );

			foreach ( $validation_value as $input_id => $value ) {
				$result = method_exists( 'GFAPI', 'update_entry_field' )
					? GFAPI::update_entry_field( $entry_id, $input_id, $value )
					: gform_update_meta( $entry_id, $input_id, $value );

				if ( false === $result || is_wp_error( $result ) ) {
					return $result;
				}
			}

			return true;
		}

		/**
		 * Returns a checkbox value array keyed by input ID.
		 *
		 * @since 3.0.0
		 *
		 * @param \GF_Field $field  Checkbox field.
		 * @param array     $values Selected values.
		 *
		 * @return array
		 */
		private function get_checkbox_validation_value( $field, array $values ) {
			$selected_values = array_map( 'strval', $values );
			$choices         = $this->get_frontend_choices( $field );
			$value           = [];

			foreach ( $this->get_field_entry_inputs( $field ) as $index => $input ) {
				$input_id = $this->get_input_id( $input );

			if ( '' === $input_id ) {
				continue;
				}

				$choice_value = isset( $choices[ $index ]['value'] ) ? (string) $choices[ $index ]['value'] : $this->get_input_label( $input, $input_id );
				$value[ $input_id ] = in_array( $choice_value, $selected_values, true ) ? $choice_value : '';
			}

			return $value;
		}

	/**
	 * Updates only the changed entry targets.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $entry_id Entry ID.
	 * @param array $updates  Values keyed by target type.
	 * @param View  $view     View.
	 * @param array $form     Gravity Forms form.
	 * @param array $entry    Original entry.
	 *
	 * @return true|WP_Error
	 */
	private function update_entry_targets( $entry_id, array $updates, View $view, array $form, array $entry = [] ) {
		foreach ( $updates[ self::TARGET_KIND_FIELD ] ?? [] as $field_id => $update ) {
			$target = is_array( $update['target'] ?? null ) ? $update['target'] : [];
			$value  = is_array( $update ) && array_key_exists( 'value', $update ) ? $update['value'] : $update;
			$field  = $target['field'] ?? null;

			if ( is_object( $field ) && 'checkbox' === $this->get_input_type( $field ) ) {
				$result = $this->update_checkbox_field( $entry_id, $field, is_array( $value ) ? $value : [] );

				if ( true !== $result ) {
					return is_wp_error( $result ) ? $result : new WP_Error( 'gravityview_bulk_edit_update_failed', __( 'An entry field could not be updated.', 'gk-gravityview' ) );
				}

				continue;
			}

			if ( method_exists( 'GFAPI', 'update_entry_field' ) ) {
				$result = GFAPI::update_entry_field( $entry_id, $field_id, $value );
			} else {
				$result = gform_update_meta( $entry_id, $field_id, $value );
			}

			if ( false === $result || is_wp_error( $result ) ) {
				return is_wp_error( $result ) ? $result : new WP_Error( 'gravityview_bulk_edit_update_failed', __( 'An entry field could not be updated.', 'gk-gravityview' ) );
			}
		}

		foreach ( $updates[ self::TARGET_KIND_FIELD_INPUT ] ?? [] as $target_id => $update ) {
			$target   = is_array( $update['target'] ?? null ) ? $update['target'] : [];
			$input_id = (string) ( $target['input_id'] ?? preg_replace( '/^input:/', '', (string) $target_id ) );
			$value    = is_array( $update ) && array_key_exists( 'value', $update ) ? $update['value'] : '';

			if ( ! preg_match( '/^[1-9][0-9]*\\.[0-9]+$/', $input_id ) ) {
				return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
			}

			$result = method_exists( 'GFAPI', 'update_entry_field' )
				? GFAPI::update_entry_field( $entry_id, $input_id, $value )
				: gform_update_meta( $entry_id, $input_id, $value );

			if ( false === $result || is_wp_error( $result ) ) {
				return is_wp_error( $result ) ? $result : new WP_Error( 'gravityview_bulk_edit_update_failed', __( 'An entry field could not be updated.', 'gk-gravityview' ) );
			}
		}

		foreach ( $updates[ self::TARGET_KIND_ENTRY_PROPERTY ] ?? [] as $update ) {
			$target   = is_array( $update['target'] ?? null ) ? $update['target'] : [];
			$property = (string) ( $target['property'] ?? '' );
			$value    = $update['value'] ?? null;

			if ( is_wp_error( $value ) ) {
				return $value;
			}

			if ( '' === $property ) {
				return new WP_Error( 'gravityview_bulk_edit_invalid_field', __( 'One or more submitted fields cannot be edited.', 'gk-gravityview' ) );
			}

			if ( ! empty( $target['save_callback'] ) ) {
				$result = $this->call_entry_property_callback( $target['save_callback'], [ (int) $entry_id, $property, $value, $target, $view, $form, $entry ] );
			} else {
				if ( method_exists( 'GFAPI', 'update_entry_property' ) ) {
					$result = GFAPI::update_entry_property( $entry_id, $property, $value );
				} elseif ( method_exists( 'GFFormsModel', 'update_lead_property' ) ) {
					$result = GFFormsModel::update_lead_property( $entry_id, $property, $value );
				} else {
					$result = false;
				}
			}

			if ( null === $result || false === $result || is_wp_error( $result ) ) {
				return is_wp_error( $result ) ? $result : new WP_Error( 'gravityview_bulk_edit_update_failed', __( 'An entry property could not be updated.', 'gk-gravityview' ) );
			}
		}

		if ( method_exists( 'GFFormsModel', 'update_lead_property' ) ) {
			GFFormsModel::update_lead_property( $entry_id, 'date_updated', gmdate( 'Y-m-d H:i:s' ) );
		}

		return true;
	}

	/**
	 * Fires update hooks after a successful entry update.
	 *
	 * @since 3.0.0
	 *
	 * @param array $form          Gravity Forms form.
	 * @param array $updated_entry Updated entry.
	 * @param array $original_entry Original entry.
	 * @param View  $view          View.
	 * @param array $changes       Applied changes.
	 *
	 * @return void
	 */
	private function after_update( array $form, array $updated_entry, array $original_entry, View $view, array $changes ) {
		$entry_id = (int) $updated_entry['id'];

		do_action( 'gform_after_update_entry', $form, $entry_id, $original_entry );
		do_action( "gform_after_update_entry_{$form['id']}", $form, $entry_id, $original_entry );

		/**
		 * Fires after Bulk Edit updates one entry.
		 *
		 * @since 3.0.0
		 *
		 * @param array $form           Gravity Forms form.
		 * @param int   $entry_id       Entry ID.
		 * @param array $updated_entry  Updated entry.
		 * @param array $original_entry Original entry.
		 * @param View  $view           View.
		 * @param array $changes        Sanitized changes.
		 */
		do_action( 'gk/gravityview/bulk-actions/edit-entry/after-update', $form, $entry_id, $updated_entry, $original_entry, $view, $changes );
	}

	/**
	 * Logs an unexpected Bulk Edit exception without exposing it to users.
	 *
	 * @since 3.0.0
	 *
	 * @param string    $message Log message.
	 * @param Throwable $e       Exception.
	 * @param array     $context Extra log context.
	 *
	 * @return void
	 */
	private function log_exception( $message, Throwable $e, array $context = [] ) {
		if ( ! function_exists( 'gravityview' ) || ! gravityview() || empty( gravityview()->log ) || ! method_exists( gravityview()->log, 'error' ) ) {
			return;
		}

		gravityview()->log->error(
			$message,
			array_merge(
				[
					'exception' => get_class( $e ),
					'message'   => $e->getMessage(),
				],
				$context
			)
		);
	}

	/**
	 * Returns field choices for frontend controls.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return array
	 */
	private function get_frontend_choices( $field ) {
		if ( empty( $field->choices ) || ! is_array( $field->choices ) ) {
			return [];
		}

		$choices = [];

		foreach ( $field->choices as $choice ) {
			$value = $this->get_choice_value( $field, $choice );

			$choices[] = [
				'label' => (string) ( $choice['text'] ?? $value ),
				'value' => $value,
			];
		}

		return $choices;
	}

	/**
	 * Returns the submitted value Gravity Forms expects for a choice.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field  Gravity Forms field.
	 * @param array     $choice Choice definition.
	 *
	 * @return string
	 */
	private function get_choice_value( $field, array $choice ) {
		$value = isset( $choice['value'] ) ? (string) $choice['value'] : '';

		if ( '' === $value && empty( $field->enableChoiceValue ) && empty( $field->enablePrice ) ) {
			$value = (string) ( $choice['text'] ?? '' );
		}

		if ( ! empty( $field->enablePrice ) ) {
			$value .= '|' . ( isset( $choice['price'] ) && is_numeric( $choice['price'] ) ? (string) (float) $choice['price'] : '0' );
		}

		return $value;
	}

	/**
	 * Returns whether a value matches a field choice.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 * @param string    $value Value.
	 *
	 * @return bool
	 */
	private function is_valid_choice( $field, $value ) {
		foreach ( $this->get_frontend_choices( $field ) as $choice ) {
			if ( (string) $choice['value'] === (string) $value ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns whether all values match field choices.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field  Gravity Forms field.
	 * @param mixed     $values Values.
	 *
	 * @return bool
	 */
	private function are_valid_choices( $field, $values ) {
		$values = is_array( $values ) ? $values : [ $values ];

		foreach ( $values as $value ) {
			if ( ! $this->is_valid_choice( $field, $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns parent field validation value with one input overlaid.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field    Gravity Forms field.
	 * @param array     $entry    Entry.
	 * @param string    $input_id Input ID.
	 * @param string    $value    New input value.
	 *
	 * @return array
	 */
	private function get_field_input_validation_value( $field, array $entry, $input_id, $value ) {
		$values = [];

		foreach ( $this->get_field_entry_inputs( $field ) as $input ) {
			$current_input_id = $this->get_input_id( $input );

			if ( '' !== $current_input_id ) {
				$values[ $current_input_id ] = (string) ( $entry[ $current_input_id ] ?? '' );
			}
		}

		if ( '' !== $input_id ) {
			$values[ $input_id ] = $value;
		}

		return $values;
	}

	/**
	 * Returns a readable field label.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Field $field Gravity Forms field.
	 *
	 * @return string
	 */
	private function get_field_label( $field ) {
		if ( isset( $field->label ) && '' !== (string) $field->label ) {
			return (string) $field->label;
		}

		if ( isset( $field->adminLabel ) && '' !== (string) $field->adminLabel ) {
			return (string) $field->adminLabel;
		}

		return sprintf(
			/* translators: %d is a Gravity Forms field ID. */
			__( 'Field %d', 'gk-gravityview' ),
			(int) $field->id
		);
	}
}

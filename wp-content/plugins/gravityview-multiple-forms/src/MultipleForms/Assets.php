<?php

namespace GravityKit\MultipleForms;

use GFCommon;
use GFFormsModel;

/**
 * Class Assets
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class Assets extends AbstractSingleton {

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_localize_script' ] );
		add_filter( 'gravityview_noconflict_styles', [ $this, 'no_conflict' ] );
		add_filter( 'gform_noconflict_styles', [ $this, 'no_conflict' ] );
		add_filter( 'gform_noconflict_scripts', [ $this, 'no_conflict' ] );
		add_filter( 'gravityview_noconflict_scripts', [ $this, 'no_conflict' ] );
	}

	/**
	 * Enqueue and localize script
	 *
	 * @since 0.1-beta
	 */
	function enqueue_localize_script() {

		global $post;

		if ( ! gravityview()->request->is_admin( 'post.php', 'single' ) ) {
			return;
		}

		wp_enqueue_script(
			'gravityview-multiple-forms',
			plugins_url( 'assets/js/gravityview-multiple-forms.js', GV_MF_FILE ),
			[
				'jquery',
				'wp-element',
			],
			GV_MF_VERSION,
			true
		);

		$view = \GV\View::from_post( $post );

		if ( ! $view ) {
			return;
		}

		$joins_data = View::get_active_form_joins( $post );
		$joins_data = array_map( static function( $join ) {
			return $join->to_legacy_format();
		}, $joins_data );

		if ( empty( $joins_data ) ) {
			$joins_data = [];
		}

		$forms                             = gravityview_get_forms( true, false, 'title' );
		$forms_data                        = [];
		$meta_and_property_labels_override = [
			'gpnf_entry_parent' => esc_html__( 'Parent Entry ID', 'gravityview-multiple-forms' ),
			'created_by'        => esc_html__( 'User ID', 'gravityview-multiple-forms' ),
		];

		/**
		 * @filter `gravityview_multiple_forms/allow_join_on` Permissible meta and properties that forms can be joined on
		 * @since  0.2 beta 1
		 *
		 * @param array $allowed_non_field_join_data Meta keys and entry property names. `entry_id` is an alias for `id`
		 */
		$allowed_non_field_join_data = apply_filters( 'gravityview_multiple_forms/allow_join_on', [
			'gpnf_entry_parent', // Gravity Wiz's Nested Forms
			'entry_id',
			'created_by',
		] );

		foreach ( $forms as $form ) {
			if ( empty( $form['fields'] ) ) {
				do_action( 'gravityview_log_debug', "[Multiple Forms][Forms Assets] There are no form fields on this particular form, Assets data failed.", __METHOD__, $form );
				continue;
			}

			$meta = [];

			$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );

			foreach ( $entry_meta as $key => $data ) {

				if ( ! in_array( $key, $allowed_non_field_join_data, true ) ) {
					continue;
				}

				$meta[] = [
					'key'      => $key,
					'text'     => ( ! empty( $meta_and_property_labels_override[ $key ] ) ) ? $meta_and_property_labels_override[ $key ] : $data['label'],
					'operator' => $data['is_numeric'] ?
						[
							'is',
							'isnot',
							'>',
							'<',
						] : [
							'is',
							'isnot',
							'contains',
						],
				];
			}

			$form_fields           = [];
			$field_filter_settings = GFCommon::get_field_filter_settings( $form );
			foreach ( $field_filter_settings as &$form_field ) {
				$key = $form_field['key'];

				if ( ( '0' !== $key && ! is_numeric( $key ) ) && ! in_array( $key, $allowed_non_field_join_data, true ) ) {
					continue;
				}

				if ( 'entry_id' === $key ) {
					$form_field['key'] = 'id';
				}

				$form_field['text'] = ( ! empty( $meta_and_property_labels_override[ $key ] ) ) ? $meta_and_property_labels_override[ $key ] : $form_field['text'];
				$form_fields[]      = $form_field;
			}

			$forms_data[ $form['id'] ] = [
				'id'     => $form['id'],
				'title'  => $form['title'],
				'fields' => array_merge(
					$form_fields,
					$meta
				),
			];
		}

		$localization = [
			'intro'                   => esc_html__( 'Use joins to combine data from several forms into one longer entry. Join on quote ID fields, custom ID fields, anything you like!', 'gravityview-multiple-forms' ),
			'joinRemoveWarning'       => esc_html__( 'Removing the join condition will remove form fields that the View was configured with.', 'gravityview-multiple-forms' ),
			'addFieldButtonLabel'     => esc_html__( 'Add Field', 'gravityview-multiple-forms' ),
			'addFieldFromButtonLabel' => esc_html__( 'Add Field from', 'gravityview-multiple-forms' ),
			'addJoinButtonLabel'      => esc_html__( 'Add Join Condition', 'gravityview-multiple-forms' ),
			'selectFieldsLabel'       => esc_html__( 'Select Fields', 'gravityview-multiple-forms' ),
			'removeLabel'             => esc_html_x( 'Remove', 'Remove join condition - verb', 'gravityview-multiple-forms' ),
			'joinWithLabel'           => esc_html_x( 'With', 'Join one form with another - preposition', 'gravityview-multiple-forms' ),
			'joinOnLabel'             => esc_html_x( 'On', 'Join one form with another on certain field - preposition', 'gravityview-multiple-forms' ),
			'nonFieldData'            => esc_html__( 'Non-Field Data', 'gravityview-multiple-forms' ),
		];

		$data = [
			'forms'        => $forms_data,
			'joins'        => $joins_data,
			'localization' => $localization,
		];

		wp_localize_script( 'gravityview-multiple-forms', 'GVJoins', $data );
	}

	/**
	 * Add Multiple Forms to no-conflict lists
	 *
	 * @since 0.1-beta
	 *
	 * @param array $scripts_or_styles
	 *
	 * @return array
	 */
	function no_conflict( $scripts_or_styles ) {
		$scripts_or_styles[] = 'gravityview-multiple-forms';
		$scripts_or_styles[] = 'gv-multiple-forms-admin';

		return $scripts_or_styles;
	}
}

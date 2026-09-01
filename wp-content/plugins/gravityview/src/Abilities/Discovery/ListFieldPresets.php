<?php
/**
 * Ability: gk-gravityview/field-presets-list
 *
 * Returns the catalog of registered field presets — opinionated bundles
 * of fields an agent (or the inspector) can apply to a View in one
 * call via `apply-field-preset`. The catalog is filter-driven; core
 * ships zero presets to avoid blessing any one workflow as canonical.
 * Add-ons populate via `gk/gravityview/rest/field-presets/list`.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/field-presets-list/run`
 * HTTP method:   GET (readonly)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/field-presets-list',
				'label'               => __( 'List Field Presets', 'gk-gravityview' ),
				'description'         => __( 'List the field presets registered on this site. Each preset is an opinionated bundle of fields that can be applied to one of a View\'s areas in a single call via apply-field-preset. Empty by default — add-ons register presets through the gk/gravityview/rest/field-presets/list filter.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				// No input_schema: this ability takes no inputs. Foundation's
				// Manager supplies the canonical empty-object schema by default
				// (Manager::empty_input_schema()), so every no-input discovery
				// ability still exposes a uniform schema to the catalog and REST
				// input validation without repeating the boilerplate here.
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'presets' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'          => [
										'type'        => 'string',
										'description' => __( 'Stable identifier — pass to apply-field-preset.', 'gk-gravityview' ),
									],
									'label'       => [ 'type' => 'string' ],
									'description' => [ 'type' => 'string' ],
									'applies_to'  => [
										'type'        => 'object',
										'description' => __( 'Compatibility constraints. `template_ids` (array): only valid for these layouts; empty = any. `areas` (array): only valid for these area keys; empty = any. `zones` (array): only valid for these zones (`directory`, `single`, `edit`); empty = any.', 'gk-gravityview' ),
										'properties'  => [
											'template_ids' => [
												'type'  => 'array',
												'items' => [ 'type' => 'string' ],
											],
											'areas'        => [
												'type'  => 'array',
												'items' => [ 'type' => 'string' ],
											],
											'zones'        => [
												'type'  => 'array',
												'items' => [ 'type' => 'string' ],
											],
										],
									],
									'fields'      => [
										'type'        => 'array',
										'description' => __( 'Slot definitions in target order. Each entry is `{ field_id, label?, ...settings }` matching the add-view-field input shape.', 'gk-gravityview' ),
										'items'       => [ 'type' => 'object' ],
									],
								],
								'required'   => [ 'id', 'label', 'fields' ],
							],
						],
						'count'   => [ 'type' => 'integer' ],
					],
					'required'   => [ 'presets', 'count' ],
				],
				'execute_callback'    => static function () {
					$presets = \GravityKit\GravityView\Abilities\Bootstrap::field_presets();
					return [
						'presets' => array_values( $presets ),
						'count'   => count( $presets ),
					];
				},
				'permission_callback' => static function () {
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_discovery();
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/field-presets-list' ),
					],
				],
            ]
		);
	}
);

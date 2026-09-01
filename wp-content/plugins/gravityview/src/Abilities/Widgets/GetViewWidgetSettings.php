<?php
/**
 * Ability: gk-gravityview/view-widget-settings-get
 *
 * Returns the current settings + settings schema for one widget slot.
 * Used by the Studio widget inspector to populate the editing panel
 * without fetching the full View config.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-widget-settings-get/run`
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
				'name'                => 'gk-gravityview/view-widget-settings-get',
				'label'               => __( 'Get View Widget Settings', 'gk-gravityview' ),
				'description'         => __( 'Return the current stored settings for one widget slot in a View. Useful for rendering the widget editing panel without fetching the full View config. The matching settings schema is not yet returned by this ability — fetch widgets-list for the per-widget settings schema until a resolver lands.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-widgets',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'area'    => [
							'type'        => 'string',
							'description' => __( 'Widget area key (header / footer / search zone).', 'gk-gravityview' ),
						],
						'slot'    => [
							'type'        => 'string',
							'description' => __( 'Slot UID inside the area.', 'gk-gravityview' ),
						],
						'include' => [
							'type'  => 'array',
							'items' => [
								'type' => 'string',
								'enum' => [ 'settings', 'version' ],
							],
						],
					],
					'required'             => [ 'id', 'area', 'slot' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'  => [ 'type' => 'integer' ],
						'area'     => [ 'type' => 'string' ],
						'slot'     => [ 'type' => 'string' ],
						'settings' => [ 'type' => 'object' ],
						'version'  => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'area', 'slot', 'settings', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$identity = \GravityKit\GravityView\View\AreaSlotIdentity::from_array(
						[
							'view_id' => (int) ( $input['id'] ?? 0 ),
							'area'    => $input['area'] ?? '',
							'slot'    => $input['slot'] ?? '',
						]
					);
					if ( is_wp_error( $identity ) ) {
						return $identity;
					}

					$repo     = new \GravityKit\GravityView\View\Widgets\WidgetSlotRepository();
					$settings = $repo->find( $identity );
					if ( null === $settings ) {
						return new \WP_Error(
							'gv_rest_widget_slot_not_found',
							__( 'Widget slot not found.', 'gk-gravityview' ),
							[
								'status'  => 404,
								'view_id' => $identity->view_id(),
								'area'    => $identity->area(),
								'slot'    => $identity->slot(),
							]
						);
					}

					$version = ( new \GravityKit\GravityView\View\Concurrency\ViewVersionComputer() )->compute( $identity->view_id() );

						$data = [
							'view_id'  => $identity->view_id(),
							'area'     => $identity->area(),
							'slot'     => $identity->slot(),
							'settings' => $settings,
							'version'  => $version,
						];

						$include = \GravityKit\GravityView\Foundation\Abilities\Support\Projection::normalize_include(
							$input['include'] ?? [],
							[ 'settings', 'version' ]
						);
					if ( empty( $include ) ) {
						return $data;
					}

						$keep = array_fill_keys( array_merge( [ 'view_id', 'area', 'slot' ], $include ), true );
						return array_intersect_key( $data, $keep );
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_read_widget_settings( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-widget-settings-get' ),
					],
				],
            ]
		);
	}
);

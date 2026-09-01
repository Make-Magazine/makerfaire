<?php
/**
 * Resolves, validates, scopes, and normalizes search-field configuration against
 * a View — the single domain owner of that logic so every write path (the REST
 * inspector endpoints, the bulk apply, gv_search_bar_add, and the batch
 * SearchFieldSlotRepository) behaves identically. The REST and persistence
 * layers depend on this; it depends on neither.
 *
 * @package     GravityKit\GravityView\Search
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\Search;

use GravityKit\GravityView\Legacy\Utility\Common as GVCommon;
use GravityKit\GravityView\Search\Fields\SearchField;
use GravityKit\GravityView\View\Sanitization\SettingValueSanitizer;
use GravityKit\GravityView\View\View;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * @since 3.0.0
 */
final class SearchFieldResolver {

	/**
	 * Keys that identify WHICH search field a payload targets. A qualified GF id
	 * (`{form_id}::n`) can live in any of these.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	public const IDENTITY_KEYS = [ 'id', 'type', 'field', 'field_id' ];

	/**
	 * Structural keys (beyond identity) that are never persisted as pass-through
	 * search-field settings — the input control, the rendered label, and the
	 * slot/position bookkeeping.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	public const STRUCTURAL_KEYS = [ 'input', 'input_type', 'label', 'form_id', 'UID', 'uid', 'position', 'search_slot', 'slot' ];

	/**
	 * @since 3.0.0
	 *
	 * @var int View post id this resolver is scoped to.
	 */
	private $view_id;

	/**
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 */
	public function __construct( int $view_id ) {
		$this->view_id = $view_id;
	}

	/**
	 * A search field's effective identity for resolution, scoping, and input
	 * narrowing: a form-qualified candidate (`id`/`type`/`field`/`field_id`
	 * carrying `{form_id}::…`) wins; else the first present identity key,
	 * reconstructed to `{form_id}::{id}` for a bare numeric GF id accompanied by
	 * a form_id. Pure (no View needed).
	 *
	 * @since 3.0.0
	 *
	 * @param array $field Search-field payload (caller or stored).
	 *
	 * @return string Effective field id ('' when none supplied).
	 */
	public static function effective_id( array $field ): string {
		// A qualified candidate in any identity key wins.
		foreach ( self::IDENTITY_KEYS as $key ) {
			if ( isset( $field[ $key ] ) && false !== strpos( (string) $field[ $key ], '::' ) ) {
				return (string) $field[ $key ];
			}
		}
		// Otherwise the first present BARE id — a bare `type` is a stub (e.g.
		// "text"), not an id, so it's excluded here.
		$field_id = '';
		foreach ( array_diff( self::IDENTITY_KEYS, [ 'type' ] ) as $key ) {
			if ( isset( $field[ $key ] ) && '' !== (string) $field[ $key ] ) {
				$field_id = (string) $field[ $key ];
				break;
			}
		}
		if ( '' !== $field_id && false === strpos( $field_id, '::' ) && is_numeric( $field_id )
			&& isset( $field['form_id'] ) && '' !== (string) $field['form_id'] ) {
			$field_id = (string) $field['form_id'] . '::' . $field_id;
		}
		return $field_id;
	}

	/**
	 * The canonical set of input-type slugs a search field may use, sourced from
	 * GravityView_Widget_Search::get_search_input_labels() so the
	 * `gravityview/search/input_labels` filter (add-on custom inputs) contributes
	 * automatically. Used as the permissive fallback when a field can't be
	 * narrowed to a specific type.
	 *
	 * @since 3.0.0
	 *
	 * @return string[]
	 */
	public static function default_input_types(): array {
		$labels = \GravityView_Widget_Search::get_search_input_labels();
		if ( is_array( $labels ) && ! empty( $labels ) ) {
			return array_values( array_unique( array_map( 'strval', array_keys( $labels ) ) ) );
		}
		return [ 'input_text', 'date', 'select', 'multiselect', 'radio', 'checkbox', 'single_checkbox', 'link', 'date_range', 'number_range', 'submit', 'hidden' ];
	}

	/**
	 * Build the mutable per-field SETTINGS for a search-field upsert: pass-through
	 * settings (only_loggedin, custom_label, …) sanitised via the canonical
	 * SettingValueSanitizer, identity/structural keys stripped so an update keyed
	 * by a bare id can't rebind the field or move it to a foreign slot. The public
	 * `label` maps to custom_label AFTER the pass-through so it wins over a stale
	 * round-tripped value (the Search Bar renders its caption from custom_label +
	 * show_label, not the GF `label`).
	 *
	 * @since 3.0.0
	 *
	 * @param array  $raw   Caller field payload.
	 * @param string $input Already-validated input slug ('' if none).
	 *
	 * @return array
	 */
	public static function build_settings( array $raw, string $input ): array {
		$reserved = array_merge( self::IDENTITY_KEYS, self::STRUCTURAL_KEYS );
		$settings = [];
		if ( '' !== $input ) {
			$settings['input_type'] = $input;
		}
		foreach ( $raw as $key => $value ) {
			if ( in_array( (string) $key, $reserved, true ) ) {
				continue;
			}
			$settings[ (string) $key ] = SettingValueSanitizer::sanitize( $value );
		}
		if ( isset( $raw['label'] ) ) {
			$settings['custom_label'] = (string) $raw['label'];
			if ( ! isset( $raw['show_label'] ) && ! isset( $settings['show_label'] ) ) {
				$settings['show_label'] = '1';
			}
		}
		return $settings;
	}

	/**
	 * Locate every slot in a search_fields_section whose stored field matches the
	 * given id, tolerant of the canonical `{form_id}::{id}` prefix. A qualified
	 * needle matches the FULL stored id (so joined-form fields sharing a local id
	 * don't collide); a bare needle matches on the suffix.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $section  search_fields_section tree.
	 * @param string $field_id Bare or qualified field id to match.
	 *
	 * @return array<int, array{0:string,1:string}> [position, search_slot] pairs.
	 */
	public static function find_slots( array $section, string $field_id ): array {
		$needle_qualified = ( false !== strpos( $field_id, '::' ) );
		$needle_bare      = self::bare_id( $field_id );
		$found            = [];
		foreach ( $section as $pos => $slots ) {
			foreach ( (array) $slots as $slot_uid => $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				// A stored field's identity can live in any identity key, or be
				// reconstructed from form_id + a bare numeric id — gather every
				// candidate so a qualified needle still matches a joined-form slot.
				$ids = [];
				foreach ( self::IDENTITY_KEYS as $id_key ) {
					if ( isset( $field[ $id_key ] ) && '' !== (string) $field[ $id_key ] ) {
						$ids[] = (string) $field[ $id_key ];
					}
				}
				if ( isset( $field['form_id'], $field['id'] ) && '' !== (string) $field['form_id'] && is_numeric( (string) $field['id'] ) ) {
					$ids[] = (string) $field['form_id'] . '::' . (string) $field['id'];
				}
				foreach ( $ids as $stored ) {
					$ok = $needle_qualified ? ( $stored === $field_id ) : ( self::bare_id( $stored ) === $needle_bare );
					if ( $ok ) {
						$found[] = [ (string) $pos, (string) $slot_uid ];
						break;
					}
				}
			}
		}
		return $found;
	}

	/**
	 * Strip a `{form_id}::` prefix from a field id, leaving the bare id.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id Field id.
	 *
	 * @return string
	 */
	private static function bare_id( string $id ): string {
		$pos = strrpos( $id, '::' );
		return false === $pos ? $id : substr( $id, $pos + 2 );
	}

	/**
	 * Normalize a search field to the canonical legacy-compatible shape: make a
	 * qualified GF identity authoritative, translate the `input` alias, stamp
	 * form_id, run it through the Search_Field domain (resolving the subclass and
	 * filling defaults), and PRESERVE any unknown add-on per-field settings the
	 * domain doesn't re-emit. Falls back to the cleaned payload when the field
	 * can't resolve, so an entry the admin can still fix is stored, not dropped.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field Search-field payload.
	 *
	 * @return array
	 */
	public function normalize( array $field ): array {
		$field = self::canonicalize_identity( $field );

		if ( isset( $field['input'] ) && '' !== (string) $field['input'] && empty( $field['input_type'] ) ) {
			$field['input_type'] = (string) $field['input'];
		}
		unset( $field['input'], $field['type'] );

		$form_id = $this->form_id();
		if ( $form_id > 0 && empty( $field['form_id'] ) ) {
			$field['form_id'] = (string) $form_id;
		}

		$canonical = $field;
		try {
			$instance = SearchField::from_configuration( $field, $this->view() );
			if ( $instance ) {
				$canonical = $instance->to_configuration();
			}
		} catch ( \Throwable $unused ) {
			$canonical = $field;
		}

		foreach ( $field as $key => $value ) {
			if ( ! array_key_exists( $key, $canonical ) ) {
				$canonical[ $key ] = $value;
			}
		}

		return $canonical;
	}

	/**
	 * Resolve a caller-supplied field to its effective (qualified) identity AND
	 * assert it is searchable on this View (resolvable + bound to a form the View
	 * searches). The single gate every search-field WRITE path shares.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field Caller-supplied search-field payload.
	 *
	 * @return string|WP_Error Effective field id on success.
	 */
	public function resolve_searchable_id( array $field ) {
		$effective_id = self::effective_id( $field );
		if ( '' === $effective_id ) {
			return new WP_Error( 'gv_rest_invalid_field', __( 'field must be an object with at least an `id`.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}
		// An explicit form_id carries the scope for a non-numeric add-on field
		// (e.g. workflow_final_status), whose id can't encode its form; a
		// qualified id's prefix still wins inside assert_searchable_id().
		$explicit_form = ( isset( $field['form_id'] ) && is_numeric( (string) $field['form_id'] ) ) ? (int) $field['form_id'] : 0;
		$check         = $this->assert_searchable_id( $effective_id, $explicit_form );
		if ( is_wp_error( $check ) ) {
			return $check;
		}
		return $effective_id;
	}

	/**
	 * Assert a (possibly qualified) field id resolves to a searchable field on a
	 * form this View searches.
	 *
	 * @since 3.0.0
	 *
	 * @param string $field_id        Effective field id.
	 * @param int    $explicit_form_id Form scope for a non-qualified add-on id.
	 *
	 * @return true|WP_Error
	 */
	public function assert_searchable_id( string $field_id, int $explicit_form_id = 0 ) {
		if ( '' === $field_id ) {
			return new WP_Error( 'gv_rest_invalid_field', __( 'A field_id is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Target form: a qualified id's prefix is authoritative; else an explicit
		// form_id; else the View's own form.
		$target_form = 0;
		if ( preg_match( '/^(\d+)::/', $field_id, $m ) ) {
			$target_form = (int) $m[1];
		} elseif ( $explicit_form_id > 0 ) {
			$target_form = $explicit_form_id;
		}

		$config = [ 'id' => $field_id ];
		if ( $target_form > 0 ) {
			if ( ! in_array( $target_form, $this->searchable_form_ids(), true ) ) {
				return new WP_Error(
					'gv_rest_invalid_field',
					sprintf(
						/* translators: %s: field id supplied by the caller */
						__( 'field_id "%s" references a form this View does not search (it is neither the View\'s form nor a joined form).', 'gk-gravityview' ),
						$field_id
					),
					[ 'status' => 400 ]
				);
			}
			$config['form_id'] = (string) $target_form;
		} else {
			$config['form_id'] = (string) $this->form_id();
		}

		try {
			$instance = SearchField::from_configuration( $config, $this->view() );
		} catch ( \Throwable $unused ) {
			$instance = null;
		}
		if ( ! $instance ) {
			return new WP_Error(
				'gv_rest_invalid_field',
				sprintf(
					/* translators: %s: field id supplied by the caller */
					__( 'field_id "%s" does not resolve to a searchable field on this View; it would create a search field that never renders.', 'gk-gravityview' ),
					$field_id
				),
				[ 'status' => 400 ]
			);
		}
		return true;
	}

	/**
	 * Input-type slugs valid for a specific field. A GF field narrows via its GF
	 * input type (qualified id → its embedded form); a meta / virtual / add-on
	 * field resolves through the Search_Field domain so it narrows by the field's
	 * real TYPE (the per-type map is keyed by type, not id), e.g. is_starred →
	 * boolean, search_all → text. Falls back to the global set when unresolved.
	 *
	 * @since 3.0.0
	 *
	 * @param string $field_id Effective field id.
	 *
	 * @return string[]
	 */
	public function valid_input_types( string $field_id ): array {
		$field_id = trim( $field_id );
		if ( '' === $field_id ) {
			return self::default_input_types();
		}

		$gf_form_id = 0;
		$gf_base_id = '';
		if ( preg_match( '/^(\d+)::(\d+(?:\.\d+)?)$/', $field_id, $qualified ) ) {
			$gf_form_id = (int) $qualified[1];
			$gf_base_id = $qualified[2];
		} elseif ( preg_match( '/^\d+(\.\d+)?$/', $field_id ) ) {
			$gf_form_id = $this->form_id();
			$gf_base_id = $field_id;
		}
		if ( $gf_form_id > 0 && '' !== $gf_base_id ) {
			$gf_field = GVCommon::get_field( $gf_form_id, (int) explode( '.', $gf_base_id )[0] );
			$narrow   = $gf_field instanceof \GF_Field ? \GravityView_Widget_Search::get_input_types_by_gf_field( $gf_field ) : null;
			return ( is_array( $narrow ) && ! empty( $narrow ) )
				? array_values( array_unique( array_map( 'strval', $narrow ) ) )
				: self::default_input_types();
		}

		$config            = [ 'id' => $field_id ];
		$config['form_id'] = preg_match( '/^(\d+)::/', $field_id, $meta_qualified )
			? (string) (int) $meta_qualified[1]
			: (string) $this->form_id();
		try {
			$resolved = SearchField::from_configuration( $config, $this->view() );
		} catch ( \Throwable $unused ) {
			$resolved = null;
		}
		if ( $resolved instanceof SearchField ) {
			$domain_types = $resolved->get_input_types();
			if ( ! empty( $domain_types ) ) {
				return array_values( array_unique( array_map( 'strval', $domain_types ) ) );
			}
		}

		$map    = \GravityView_Widget_Search::get_input_types_by_field_type();
		$narrow = $map[ $field_id ] ?? null;
		return ( is_array( $narrow ) && ! empty( $narrow ) )
			? array_values( array_unique( array_map( 'strval', $narrow ) ) )
			: self::default_input_types();
	}

	/**
	 * Make a qualified Gravity Forms identity (`{form_id}::{field_id}`) the single
	 * source of truth: when present in any identity key, store exactly that id +
	 * form and drop the other identity aliases, so a conflicting `type` / `field`
	 * / `form_id` can't redirect a validated field to a different form on write.
	 * No-op for virtual ids (search_all, is_starred, …) and consistent payloads.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field Search-field payload.
	 *
	 * @return array
	 */
	private static function canonicalize_identity( array $field ): array {
		foreach ( self::IDENTITY_KEYS as $key ) {
			if ( isset( $field[ $key ] ) && preg_match( '/^(\d+)::(\d+(?:\.\d+)?)$/', (string) $field[ $key ], $matched ) ) {
				unset( $field['type'], $field['field'], $field['field_id'] );
				$field['id']      = $matched[0];
				$field['form_id'] = (string) (int) $matched[1];
				return $field;
			}
		}
		return $field;
	}

	/**
	 * Form ids this View searches: its own form plus any joined forms.
	 *
	 * @since 3.0.0
	 *
	 * @return int[]
	 */
	private function searchable_form_ids(): array {
		$forms = [ $this->form_id() ];
		foreach ( array_keys( (array) View::get_joined_forms( $this->view_id ) ) as $joined_form_id ) {
			$forms[] = (int) $joined_form_id;
		}
		return $forms;
	}

	/**
	 * This View's own form id, via the canonical accessor.
	 *
	 * @since 3.0.0
	 *
	 * @return int
	 */
	private function form_id(): int {
		return (int) \gravityview_get_form_id( $this->view_id );
	}

	/**
	 * The View instance (null for a 0 / unsaved id, e.g. test scaffolds).
	 *
	 * @since 3.0.0
	 *
	 * @return View|null
	 */
	private function view(): ?View {
		return $this->view_id > 0 ? View::by_id( $this->view_id ) : null;
	}
}

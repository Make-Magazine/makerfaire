<?php
/**
 * Notes field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Notes class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

use GFAPI;
use GFCommon;
use GFFormsModel;
use GravityKit\GravityView\Renderer\MergeTags;
use GravityKit\GravityView\Utils\Assets;
use GravityKit\GravityView\Utils\Path;
use GravityView_Entry_Notes;
use GravityView_View;
use GVCommon;
use WP_Error;

use function gravityview;
use function gravityview_css_url;
use function gravityview_get_entry;
use function gravityview_strip_whitespace;
use function gv_map_deep;

/**
 * Add Entry Notes
 *
 * @since 1.17
 */
class Notes extends \GravityView_Field {
	const ASSETS_HANDLE = 'gravityview-notes';

	/**
	 * @since 1.17
	 * @var string Current __FILE__
	 */
	public static $file;

	/**
	 * @since 1.17
	 * @var string plugin_dir_path() of the current field file
	 */
	public static $path;

	/**
	 * @since 1.17
	 * @var bool Are we doing an AJAX request?
	 */
	private $doing_ajax = false;

	/**
	 * Check if we should use AJAX response methods (wp_send_json_*).
	 *
	 * Returns false during tests to prevent wp_die() from being called.
	 *
	 * @since 2.31
	 *
	 * @return bool True if we should send AJAX responses, false if in test mode.
	 */
	private function should_send_ajax_response() {
		// Don't call wp_send_json_* during tests - it calls wp_die() which outputs "0".
		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) && DOING_GRAVITYVIEW_TESTS ) {
			return false;
		}

		return $this->doing_ajax;
	}

	/**
	 * The name of the GravityView field type
	 *
	 * @var string
	 */
	public $name = 'notes';

	/** @var string $icon Icon class for the field. */
	public $icon = 'dashicons-admin-comments';

	/**
	 * Constructor.
	 */
	public function __construct() {

		self::$path = GRAVITYVIEW_DIR . 'src/Field/Types/';
		self::$file = GRAVITYVIEW_DIR . 'src/Field/Types/Notes.php';

		$this->label      = esc_html__( 'Entry Notes', 'gk-gravityview' );
		$this->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		$this->add_hooks();

		parent::__construct();
	}

	/**
	 * Add AJAX hooks, [gv_note_add] shortcode, and template loading paths
	 *
	 * @since 1.17
	 *
	 * @return void
	 */
	private function add_hooks() {

		add_shortcode( 'gv_note_add', ['GravityView_Field_Notes', 'get_add_note_part'] );

		add_action( 'wp', [$this, 'maybe_delete_notes'], 1000 );
		add_action( 'wp_ajax_nopriv_gv_delete_notes', [$this, 'maybe_delete_notes'] );
		add_action( 'wp_ajax_gv_delete_notes', [$this, 'maybe_delete_notes'] );

		add_action( 'wp', [$this, 'maybe_add_note'], 1000 );
		add_action( 'wp_ajax_nopriv_gv_note_add', [$this, 'maybe_add_note'] );
		add_action( 'wp_ajax_gv_note_add', [$this, 'maybe_add_note'] );

		// add template path to check for field
		add_filter( 'gravityview_template_paths', [$this, 'add_template_path'] );
		add_filter( 'gravityview/template/fields_template_paths', [$this, 'add_template_path'] );

		add_action( 'gravityview/field/notes/scripts', [$this, 'enqueue_scripts'] );

		add_filter( 'gravityview_entry_default_fields', [$this, 'add_entry_default_field'], 10, 3 );
	}


	/**
	 * Add Entry Notes to the Add Field picker in Edit View
	 *
	 * @since 1.17
	 *
	 * @see   GravityView_Admin_Views::get_entry_default_fields()
	 *
	 * @param array  $entry_default_fields Fields configured to show in the picker
	 * @param array  $form                 Gravity Forms form array
	 * @param string $zone                 Current context: `directory`, `single`, `edit`
	 *
	 * @return array Fields array with notes added, if in Multiple Entries or Single Entry context
	 */
	public function add_entry_default_field( $entry_default_fields, $form, $zone ) {

		if ( in_array( $zone, ['directory', 'single'] ) ) {
			$entry_default_fields['notes'] = [
				'label' => __( 'Entry Notes', 'gk-gravityview' ),
				'type'  => 'notes',
				'desc'  => __( 'Display, add, and delete notes for an entry.', 'gk-gravityview' ),
				'icon'  => 'dashicons-admin-comments',
			];
		}

		return $entry_default_fields;
	}

	/**
	 * Registers scripts and styles used in the UI.
	 *
	 * @since 1.17
	 *
	 * @return void
	 */
	public function register_scripts() {
		$css_file = gravityview_css_url( 'entry-notes.css', Assets::dir( 'extensions/entry-notes/css' ) );

		wp_register_style( self::ASSETS_HANDLE, $css_file, [], GV_PLUGIN_VERSION );
		wp_register_script( self::ASSETS_HANDLE, Assets::url( 'extensions/entry-notes/js/entry-notes.js' ), ['jquery'], GV_PLUGIN_VERSION, true );
	}

	/**
	 * Enqueues and localizes scripts and styles.
	 *
	 * @since 1.17
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( wp_script_is( self::ASSETS_HANDLE ) ) {
			return;
		}

		if ( ! wp_script_is( self::ASSETS_HANDLE, 'registered' ) ) {
			$this->register_scripts();
		}

		$strings = self::strings();

		wp_localize_script(
			self::ASSETS_HANDLE,
			'GVNotes',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'text'    => [
					'processing'       => $strings['processing'],
					'delete_confirm'   => $strings['delete-confirm'],
					'note_added'       => $strings['added-note'],
					'error_invalid'    => $strings['error-invalid'],
					'error_empty_note' => $strings['error-empty-note'],
				],
			]
		);

		wp_enqueue_style( self::ASSETS_HANDLE );
		wp_enqueue_script( self::ASSETS_HANDLE );
	}

	/**
	 * Verify permissions, check if $_POST is set and as expected. If so, use process_add_note.
	 *
	 * @since 1.17
	 *
	 * @see process_add_note()
	 *
	 * @return void
	 */
	public function maybe_add_note() {
		if ( ! isset( $_POST['action'] ) || 'gv_note_add' !== $_POST['action'] ) {
			return;
		}

		if ( ! GVCommon::has_cap( 'gravityview_add_entry_notes' ) ) {
			gravityview()->log->error( "The user isn't allowed to add entry notes." );

			return;
		}

		$post = wp_unslash( $_POST );

		if ( $this->doing_ajax ) {
			parse_str( $post['data'], $data );
		} else {
			$data = $post;
		}

		// Sanitize fields appropriately based on their expected content
		$sanitized_data = $this->sanitize_note_data( $data );

		$this->process_add_note( (array) $sanitized_data );
	}

	/**
	 * Sanitize note data fields appropriately based on their expected content type.
	 *
	 * @since 1.17
	 *
	 * @param array $data Raw data from $_POST.
	 *
	 * @return array Sanitized data array.
	 */
	private function sanitize_note_data( $data ) {
		$sanitized = [];

		$single_line_text_fields = [
			'entry-slug',
			'gv_note_add',
			'action',
			'add_note',
			'_wp_http_referer',
			'gv-note-to',
			'gv-note-subject',
		];

		foreach ( $single_line_text_fields as $field ) {
			if ( ! isset( $data[ $field ] ) ) {
				continue;
			}

			$sanitized[ $field ] = sanitize_text_field( $data[ $field ] );
		}

		if ( isset( $data['gv-note-content'] ) ) {
			$sanitized['gv-note-content'] = sanitize_textarea_field( $data['gv-note-content'] );
		}

		if ( isset( $data['gv-note-to-custom'] ) ) {
			$sanitized['gv-note-to-custom'] = sanitize_email( $data['gv-note-to-custom'] );
		}

		if ( isset( $data['show-delete'] ) ) {
			$sanitized['show-delete'] = (int) $data['show-delete'];
		}

		foreach ( $data as $key => $value ) {
			if ( ! isset( $sanitized[ $key ] ) ) {
				$sanitized[ $key ] = sanitize_text_field( $value );
			}
		}

		return $sanitized;
	}

	/**
	 * Handle adding a note.
	 *
	 * Verify the request. If valid, add the note. If AJAX request, send response JSON.
	 *
	 * @since 1.17
	 *
	 * @param array $data {
	 *     Array of note data.
	 *
	 *     @type string $action           Action name "gv_note_add".
	 *     @type string $entry-slug       Entry slug or ID to add note to.
	 *     @type string $gv_note_add      Nonce with action "gv_note_add_{entry slug}" and name "gv_note_add".
	 *     @type string $_wp_http_referer Relative URL to submitting page ('/view/example/entry/123/').
	 *     @type string $gv-note-content  Note content.
	 *     @type string $add_note         Submit button value ('Add Note').
	 * }
	 *
	 * @return void
	 */
	private function process_add_note( $data ) {

		if ( empty( $data['entry-slug'] ) ) {

			$error = self::strings( 'error-invalid' );
			gravityview()->log->error( 'The note is missing an Entry ID.' );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( $error )] );
			}

			return;
		}

		$valid = wp_verify_nonce( $data['gv_note_add'], 'gv_note_add_' . $data['entry-slug'] );

		$has_cap = GVCommon::has_cap( 'gravityview_add_entry_notes' );

		if ( ! $has_cap ) {
			gravityview()->log->error( 'Adding a note failed: the user does not have the "gravityview_add_entry_notes" capability.' );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( self::strings( 'error-cap-add' ) )] );
			}

			return;
		}

		if ( ! $valid ) {
			gravityview()->log->error( 'Nonce validation failed; the note was not created' );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( self::strings( 'error-invalid' ) )] );
			}

			return;
		}

		$entry = gravityview_get_entry( $data['entry-slug'], true, false );

		if ( ! $entry ) {
			gravityview()->log->error( 'The entry was not found', ['data' => compact( 'data' )] );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( self::strings( 'error-invalid' ) )] );
			}

			return;
		}

		if ( ! class_exists( 'GFAPI' ) ) {
			gravityview()->log->error( 'GFAPI is not loaded' );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( self::strings( 'error-add-note' ) )] );
			}
			return;
		}

		$added = $this->add_note( $entry, $data );

		// Error adding note
		if ( is_wp_error( $added ) ) {

			gravityview()->log->error( 'Error adding note', ['data' => compact( 'added' )] );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( $added->get_error_message() )] );
			}

			return;
		}

		// Confirm the note was added, because GF doesn't return note ID on success.
		$note = GFAPI::get_note( $added );

		if ( is_wp_error( $note ) ) {
			gravityview()->log->error( 'The note was not successfully created', ['data' => compact( 'note', 'data' )] );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( self::strings( 'error-add-note' ) )] );
			}

			return;
		}

		// Possibly email peeps about this great new note.
		$email_success = $this->maybe_send_entry_notes( $note, $entry, $data );

		if ( is_wp_error( $email_success ) ) {
			gravityview()->log->error( 'The note was not successfully emailed', ['data' => compact( 'note', 'data' )] );

			if ( $this->should_send_ajax_response() ) {
				wp_send_json_error( ['error' => esc_html( self::strings( 'error-email-note' ) )] );
			}
		}

		$success = self::display_note( $note, ! empty( $data['show-delete'] ) );

		gravityview()->log->debug( 'The note was successfully created', ['data' => compact( 'note', 'data' )] );

		if ( ! $this->doing_ajax ) {
			return;
		}

		if ( $success ) {
			if ( $this->should_send_ajax_response() ) {
				wp_send_json_success( ['html' => $success] );
			}
			return;
		}

		if ( $this->should_send_ajax_response() ) {
			wp_send_json_error( ['error' => esc_html( self::strings( 'error-invalid' ) )] );
		}
	}

	/**
	 * Possibly delete notes, if request is proper.
	 *
	 * Verify permissions. Check expected $_POST. Parse args, then send to process_delete_notes.
	 *
	 * @since 1.17
	 *
	 * @see process_delete_notes()
	 *
	 * @return void
	 */
	public function maybe_delete_notes() {

		if ( ! GVCommon::has_cap( 'gravityview_delete_entry_notes' ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && 'gv_delete_notes' === $_POST['action'] ) {

			$post = wp_unslash( $_POST );
			if ( $this->doing_ajax ) {
				parse_str( $post['data'], $data );
			} else {
				$data = $post;
			}

			$required_args = [
				'gv_delete_notes' => '',
				'entry-slug'      => '',
			];

			$data = wp_parse_args( $data, $required_args );

			// Sanitize the delete data
			$data['gv_delete_notes'] = sanitize_text_field( $data['gv_delete_notes'] );
			$data['entry-slug']      = sanitize_text_field( $data['entry-slug'] );
			if ( isset( $data['note'] ) && is_array( $data['note'] ) ) {
				$data['note'] = array_map( 'absint', $data['note'] );
			}

			$this->process_delete_notes( $data );
		}
	}

	/**
	 * Handle deleting notes.
	 *
	 * @since 1.17
	 *
	 * @param array $data {
	 *     Array of deletion data.
	 *
	 *     @type string $action           Action name "gv_delete_notes".
	 *     @type string $entry-slug       Entry slug or ID to delete notes from.
	 *     @type string $gv_delete_notes  Nonce with action "gv_delete_notes_{entry slug}" and name "gv_delete_notes".
	 *     @type string $_wp_http_referer Relative URL to submitting page ('/view/example/entry/123/').
	 *     @type int[]  $note             Array of Note IDs to be deleted.
	 * }
	 *
	 * @return void
	 */
	private function process_delete_notes( $data ) {

		$valid   = wp_verify_nonce( $data['gv_delete_notes'], 'gv_delete_notes_' . $data['entry-slug'] );
		$has_cap = GVCommon::has_cap( 'gravityview_delete_entry_notes' );
		$success = false;

		if ( $valid && $has_cap ) {
			// The nonce binds to the entry slug, not to the note IDs. Resolve the
			// entry and let EntryNotes scope the deletion to that entry, so a valid
			// nonce for one entry cannot delete another entry's notes.
			$entry = gravityview_get_entry( $data['entry-slug'], true, false );

			if ( $entry ) {
				$requested = isset( $data['note'] ) && is_array( $data['note'] ) ? $data['note'] : [];
				$deleted   = GravityView_Entry_Notes::delete_notes_for_entry( $entry['id'], $requested );

				// Success when the entry resolved and either nothing was requested
				// (no-op) or at least one of its own notes was deleted.
				if ( ! $requested || $deleted ) {
					$success = true;
				}
			}
		}

		if ( $this->should_send_ajax_response() ) {

			if ( $success ) {
				wp_send_json_success();
			} else {
				if ( ! $valid ) {
					$error_message = self::strings( 'error-invalid' );
				} else {
					$error_message = self::strings( 'error-cap-delete' );
				}

				wp_send_json_error( ['error' => $error_message] );
			}
		}
	}

	/**
	 * Include this extension templates path.
	 *
	 * @since 1.17
	 *
	 * @param array $file_paths List of template paths ordered.
	 *
	 * @return array File paths with `./` and `./partials/` paths added.
	 */
	public function add_template_path( $file_paths ) {

		$file_paths[172] = Path::dir( 'Extension/EntryNotes' );
		$file_paths[173] = Path::dir( 'Extension/EntryNotes/views' );

		return $file_paths;
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		unset( $field_options['show_as_link'] );

		// Get dynamic note types for this form.
		$note_types        = self::get_note_types_for_form( $form_id );
		$note_type_options = [];

		foreach ( $note_types as $note_type ) {
			$note_type_options[ $note_type ] = self::get_note_type_label( $note_type );
		}

		// Sort alphabetically by translated label.
		asort( $note_type_options );

		$notes_options = [
			'notes_output' => [
				'type'    => 'select',
				'label'   => __( 'What to display', 'gk-gravityview' ),
				'desc'    => __( 'Show the notes themselves, or just whether the entry has any. Summaries are plain text, so they work in exports.', 'gk-gravityview' ),
				'value'   => 'notes',
				'options' => [
					'notes'     => __( 'The notes', 'gk-gravityview' ),
					'count'     => __( 'Number of notes', 'gk-gravityview' ),
					'indicator' => __( 'Yes or No', 'gk-gravityview' ),
					'custom'    => __( 'Custom text', 'gk-gravityview' ),
				],
			],
			'notes_indicator_yes' => [
				'type'     => 'text',
				'label'    => __( 'Text when the entry has notes', 'gk-gravityview' ),
				'desc'     => __( 'Any character works, such as a checkmark. HTML is not allowed.', 'gk-gravityview' ),
				'value'    => __( 'Yes', 'gk-gravityview' ),
				'requires' => 'notes_output=custom',
			],
			'notes_indicator_no'  => [
				'type'     => 'text',
				'label'    => __( 'Text when it has none', 'gk-gravityview' ),
				// Empty by default: the admin UI restores a cleared text input to
				// this value, so anything else makes a blank cell impossible.
				'desc'     => __( 'Leave empty for a blank cell.', 'gk-gravityview' ),
				'value'    => '',
				'requires' => 'notes_output=custom',
			],
			'notes' => [
				'type'              => 'checkboxes',
				'label'             => __( 'Note Settings', 'gk-gravityview' ),
				/* translators: 1: opening HTML link, 2: closing HTML link. */
				'desc'              => sprintf( _x( 'Only users with specific capabilities will be able to view, add and delete notes. %1$sRead more%2$s.', '%s is opening and closing HTML link', 'gk-gravityview' ), '<a href="https://docs.gravitykit.com/article/311-gravityview-capabilities">', '</a>' ),
				'options'           => [
					'view'           => [
						'label' => __( 'Display notes?', 'gk-gravityview' ),
					],
					'view_loggedout' => [
						'label'    => __( 'Display notes to users who are not logged-in?', 'gk-gravityview' ),
						'requires' => 'view',
					],
					'add'            => [
						'label' => __( 'Enable adding notes?', 'gk-gravityview' ),
					],
					'email'          => [
						'label'    => __( 'Allow emailing notes?', 'gk-gravityview' ),
						'requires' => 'add',
					],
					'delete'         => [
						'label' => __( 'Allow deleting notes?', 'gk-gravityview' ),
					],
				],
				'value'             => [
					'view'  => 1,
					'add'   => 1,
					'email' => 1,
				],
				'after'             => ! empty( $note_type_options ) ? [ $this, 'render_exclude_note_types' ] : null,
				'note_type_options' => $note_type_options,
			],
		];

		return $notes_options + $field_options;
	}

	/**
	 * Renders the exclude note types multiselect inside the notes checkboxes fieldset.
	 *
	 * @since 2.53.0
	 *
	 * @param GravityView_FieldType $field_type The field type instance.
	 *
	 * @return void
	 */
	public function render_exclude_note_types( $field_type ) {
		$field_config      = $field_type->get_field();
		$note_type_options = isset( $field_config['note_type_options'] ) ? $field_config['note_type_options'] : [];

		if ( empty( $note_type_options ) ) {
			return;
		}

		$field_name = $field_type->get_name();
		$base_name  = str_replace( '[notes]', '[exclude_note_types]', $field_name );
		$base_id    = 'gv_exclude_note_types_' . wp_generate_password( 8, false );

		// Get the saved value from the full current settings (exclude_note_types is a sibling to notes).
		$current_settings = isset( $field_config['current_settings'] ) ? $field_config['current_settings'] : [];
		$current_value    = \GV\Utils::get( $current_settings, 'exclude_note_types', [] );

		if ( ! is_array( $current_value ) ) {
			$current_value = [];
		}

		?>
		<li>
			<label><?php esc_html_e( 'Note types to exclude from display:', 'gk-gravityview' ); ?></label>
		</li>
		<?php foreach ( $note_type_options as $value => $label ) : ?>
			<?php $checkbox_id = $base_id . '_' . sanitize_key( $value ); ?>
			<li class="gv-sub-setting">
				<label for="<?php echo esc_attr( $checkbox_id ); ?>">
					<input type="checkbox" name="<?php echo esc_attr( $base_name ); ?>[]" id="<?php echo esc_attr( $checkbox_id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $current_value, true ), true ); ?> />
					<?php echo esc_html( $label ); ?>
				</label>
			</li>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Gets human-readable label for a note type.
	 *
	 * @since 2.53.0
	 *
	 * @param string $note_type The note type slug.
	 *
	 * @return string Human-readable label.
	 */
	private static function get_note_type_label( $note_type ) {
		$known_labels = [
			'user'                    => __( 'Admin notes', 'gk-gravityview' ),
			'gravityview/field/notes' => __( 'Frontend notes', 'gk-gravityview' ),
			'notification'            => __( 'Email notification log', 'gk-gravityview' ),
			'gravityview'             => __( 'GravityView system log', 'gk-gravityview' ),
			'note'                    => __( 'Other notes', 'gk-gravityview' ),
		];

		/**
		 * Modifies the labels displayed for note types in the field settings.
		 *
		 * @since 2.53.0
		 *
		 * @param array $known_labels Associative array of note type slugs to human-readable labels.
		 */
		$known_labels = apply_filters( 'gk/gravityview/field/notes/type-labels', $known_labels );

		if ( isset( $known_labels[ $note_type ] ) ) {
			return $known_labels[ $note_type ];
		}

		// Fallback: convert slug to readable label.
		return ucfirst( str_replace( [ '-', '_', '/' ], ' ', $note_type ) );
	}

	/**
	 * Gets unique note types for a given form.
	 *
	 * Combines known/default note types with any additional types found in the
	 * database for entries associated with the specified form.
	 *
	 * @since 2.53.0
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return array Array of unique note type slugs.
	 */
	private static function get_note_types_for_form( $form_id ) {
		global $wpdb;

		// Start with known/default note types so they're always available.
		$known_types = [
			'user',
			'notification',
			'gravityview',
			'gravityview/field/notes',
			'note',
		];

		/**
		 * Modifies the default note types shown in the field settings.
		 *
		 * @since 2.53.0
		 *
		 * @param array $known_types Array of known note type slugs.
		 * @param int   $form_id     The form ID.
		 */
		$known_types = apply_filters( 'gk/gravityview/field/notes/default-types', $known_types, $form_id );

		if ( empty( $form_id ) ) {
			return $known_types;
		}

		$entry_table = GFFormsModel::get_entry_table_name();
		$notes_table = GFFormsModel::get_entry_notes_table_name();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names are from GF API.
		$query = $wpdb->prepare(
			"SELECT DISTINCT n.note_type
			 FROM {$notes_table} n
			 INNER JOIN {$entry_table} e ON n.entry_id = e.id
			 WHERE e.form_id = %d AND n.note_type IS NOT NULL AND n.note_type != ''
			 ORDER BY n.note_type ASC",
			$form_id
		);

		$db_types = $wpdb->get_col( $query );

		// Merge known types with database types, preserving uniqueness.
		$all_types = array_unique( array_merge( $known_types, $db_types ?: [] ) );

		sort( $all_types );

		return $all_types;
	}

	/**
	 * Filters notes by excluding specified note types.
	 *
	 * @since 2.53.0
	 *
	 * @param array $notes              Array of note objects.
	 * @param array $exclude_note_types Array of note types to exclude. Accepts multiselect format ['type1', 'type2'].
	 *
	 * @return array Filtered array of note objects.
	 */
	public static function filter_notes_by_type( $notes, $exclude_note_types ) {
		$excluded = array_filter( (array) $exclude_note_types );

		if ( empty( $excluded ) || ! is_array( $notes ) ) {
			return $notes;
		}

		return array_filter( $notes, function ( $note ) use ( $excluded ) {
			return ! in_array( $note->note_type, $excluded, true );
		} );
	}

	/**
	 * Whether the current user may see this field's notes.
	 *
	 * Both the HTML and CSV templates gate on this, so exports cannot disclose
	 * notes the same user would not be shown on screen.
	 *
	 * @since 3.3.0
	 *
	 * @param array $field_settings The field configuration.
	 *
	 * @return bool
	 */
	public static function can_view_notes( array $field_settings ): bool {
		$visibility_settings = \GV\Utils::get( $field_settings, 'notes', [] );

		// Fail closed: a configuration that is not the expected shape grants nothing.
		if ( ! is_array( $visibility_settings ) ) {
			return false;
		}

		if ( empty( $visibility_settings['view'] ) ) {
			return false;
		}

		if ( ! empty( $visibility_settings['view_loggedout'] ) ) {
			return true;
		}

		return GVCommon::has_cap( 'gravityview_view_entry_notes' );
	}

	/**
	 * Returns an entry's notes with the field's excluded note types removed.
	 *
	 * @since 3.3.0
	 *
	 * @param int   $entry_id       The entry ID.
	 * @param array $field_settings The field configuration.
	 *
	 * @return array Note objects.
	 */
	public static function get_visible_notes( $entry_id, array $field_settings ): array {
		if ( ! class_exists( 'GravityView_Entry_Notes' ) ) {
			return [];
		}

		$notes = (array) GravityView_Entry_Notes::get_notes( $entry_id );

		return array_values( (array) self::filter_notes_by_type( $notes, \GV\Utils::get( $field_settings, 'exclude_note_types', [] ) ) );
	}

	/**
	 * Renders the notes as a count or a yes/no indicator.
	 *
	 * @since 3.3.0
	 *
	 * @param array  $notes          Note objects, already filtered.
	 * @param string $output         The output mode: `count`, `indicator` or `custom`.
	 * @param array  $field_settings The field configuration, for the custom labels.
	 * @param array  $form           The form, for merge tags in the custom labels.
	 * @param array  $entry          The entry, for merge tags in the custom labels.
	 *
	 * @return string Empty when $output is not a summary mode. Not escaped: the caller
	 *                escapes for its own context, so merge tag values are escaped once.
	 */
	public static function get_summary_output( array $notes, $output, array $field_settings = [], array $form = [], array $entry = [] ) {
		if ( 'count' === $output ) {
			// Unformatted, so spreadsheets read the column as numbers rather than
			// as text once the count passes a thousands separator.
			return (string) count( $notes );
		}

		if ( ! in_array( $output, [ 'indicator', 'custom' ], true ) ) {
			return '';
		}

		$key = $notes ? 'notes_indicator_yes' : 'notes_indicator_no';

		if ( 'custom' === $output && isset( $field_settings[ $key ] ) ) {
			return MergeTags::replace_variables( (string) $field_settings[ $key ], $form, $entry, false, false, false );
		}

		$strings = self::strings();

		// strings() escapes for HTML, but this method returns unescaped text so each
		// caller escapes once for its own context -- and CSV has no HTML to escape.
		return wp_specialchars_decode( $notes ? $strings['indicator-yes'] : $strings['indicator-no'], ENT_QUOTES );
	}

	/**
	 * The output modes that render a plain-text summary instead of the notes.
	 *
	 * @since 3.3.0
	 *
	 * @return string[]
	 */
	public static function get_summary_modes(): array {
		return [ 'count', 'indicator', 'custom' ];
	}

	/**
	 * Get strings used by the Entry Notes field.
	 *
	 * Use `gravityview/field/notes/strings` filter to modify the strings.
	 *
	 * @since 1.17
	 *
	 * @param string $key If set, return the string with the key of $key.
	 *
	 * @return array|string Array of strings with keys and values. If $key is set, returns string. If missing $strings[ $key ], empty string.
	 */
	public static function strings( $key = '' ) {

		$strings = [
			'add-note'              => __( 'Add Note', 'gk-gravityview' ),
			'added-note'            => __( 'Note added.', 'gk-gravityview' ),
			'content-label'         => __( 'Note Content', 'gk-gravityview' ),
			'delete'                => __( 'Delete', 'gk-gravityview' ),
			'delete-confirm'        => __( 'Are you sure you want to delete the selected notes?', 'gk-gravityview' ),
			'caption'               => __( 'Notes for this entry', 'gk-gravityview' ),
			'toggle-notes'          => __( 'Toggle all notes', 'gk-gravityview' ),
			'no-notes'              => __( 'There are no notes.', 'gk-gravityview' ),
			'indicator-yes'         => _x( 'Yes', 'Shown when an entry has at least one note.', 'gk-gravityview' ),
			'indicator-no'          => _x( 'No', 'Shown when an entry has no notes.', 'gk-gravityview' ),
			'processing'            => __( 'Processing&hellip;', 'gk-gravityview' ),
			'other-email'           => __( 'Other email address', 'gk-gravityview' ),
			'email-label'           => __( 'Email address', 'gk-gravityview' ),
			'email-placeholder'     => _x( 'you@example.com', 'Example email address used as a placeholder', 'gk-gravityview' ),
			'subject-label'         => __( 'Subject', 'gk-gravityview' ),
			'subject'               => __( 'Email subject', 'gk-gravityview' ),
			'default-email-subject' => __( 'New entry note', 'gk-gravityview' ),
			'email-footer'          => __( 'This note was sent from {url}', 'gk-gravityview' ),
			'also-email'            => __( 'Also email this note to', 'gk-gravityview' ),
			'error-add-note'        => __( 'There was an error adding the note.', 'gk-gravityview' ),
			'error-email-note'      => __( 'There was an error emailing the note.', 'gk-gravityview' ),
			'error-invalid'         => __( 'The request was invalid. Refresh the page and try again.', 'gk-gravityview' ),
			'error-empty-note'      => _x( 'Note cannot be blank.', 'Message to display when submitting a note without content.', 'gk-gravityview' ),
			'error-cap-delete'      => __( 'You don\'t have the ability to delete notes.', 'gk-gravityview' ),
			'error-cap-add'         => __( 'You don\'t have the ability to add notes.', 'gk-gravityview' ),
		];

		/**
		 * Modify the strings used by the Entry Notes field.
		 *
		 * @since 1.17
		 *
		 * @param array $strings Text in key => value pairs
		 */
		$strings = gv_map_deep( apply_filters( 'gravityview/field/notes/strings', $strings ), 'esc_html' );

		if ( $key ) {
			return isset( $strings[ $key ] ) ? $strings[ $key ] : '';
		}

		return $strings;
	}

	/**
	 * Generate HTML output for a single note.
	 *
	 * @since 1.17
	 * @since 2.0
	 *
	 * @param object               $note        Note object with id, user_id, date_created, value, note_type, user_name, user_email vars.
	 * @param bool                 $show_delete Whether to show the bulk delete inputs.
	 * @param \GV\Template_Context $context     The context.
	 *
	 * @return string HTML.
	 */
	public static function display_note( $note, $show_delete = false, $context = null ) {

		if ( ! is_object( $note ) ) {
			return '';
		}

		$note_content = [
			'avatar'                 => get_avatar( $note->user_id, 48 ),
			'user_name'              => esc_html( $note->user_name ),
			'user_email'             => esc_html( $note->user_email ),
			'added_on'               => esc_html__( 'added on {date_created_formatted}', 'gk-gravityview' ),
			'value'                  => wpautop( esc_html( $note->value ) ),
			'date_created'           => esc_html( $note->date_created ),
			'date_created_formatted' => esc_html( GFCommon::format_date( $note->date_created, false ) ),
			'user_id'                => intval( $note->user_id ),
			'note_type'              => esc_html( $note->note_type ),
			'note_id'                => intval( $note->id ),
		];

		/**
		 * Modify the note content before rendering in the template.
		 *
		 * @since 1.17
		 * @since 2.0
		 *
		 * @param array                $note_content Array of note content that will be replaced in template files.
		 * @param object               $note         Note object with id, user_id, date_created, value, note_type, user_name, user_email vars.
		 * @param boolean              $show_delete  True: Notes are editable. False: no editing notes.
		 * @param \GV\Template_Context $context      The context.
		 */
		$note_content = apply_filters( 'gravityview/field/notes/content', $note_content, $note, $show_delete, $context );

		$note_row_template = ( $show_delete && GVCommon::has_cap( 'gravityview_delete_entry_notes' ) ) ? 'row-editable' : 'row';

		if ( $context instanceof \GV\Template_Context ) {

			ob_start();
			$context->template->get_template_part( 'note', 'detail', true );
			$note_detail_html = ob_get_clean();

			ob_start();
			$context->template->get_template_part( 'note', $note_row_template, true );
			$note_row = ob_get_clean();

		} else {
			/** @deprecated 3.0.0 */
			ob_start();
			GravityView_View::getInstance()->get_template_part( 'note', 'detail' );
			$note_detail_html = ob_get_clean();

			ob_start();
			GravityView_View::getInstance()->get_template_part( 'note', $note_row_template );
			$note_row = ob_get_clean();
		}

		foreach ( $note_content as $tag => $value ) {
			// Ensure value is a string and handle null values safely.
			$replacement = $value !== null ? (string) $value : '';
			// The tag itself should be safe as it comes from our code, not user input.
			$note_detail_html = str_replace( '{' . esc_attr( $tag ) . '}', $replacement, $note_detail_html );
		}

		$replacements = [
			'{note_id}'     => $note_content['note_id'],
			'{row_class}'   => 'gv-note',
			'{note_detail}' => $note_detail_html,
		];

		// Strip extra whitespace in template.
		$output = gravityview_strip_whitespace( $note_row );

		foreach ( $replacements as $tag => $replacement ) {
			$output = str_replace( $tag, $replacement, $output );
		}

		return $output;
	}

	/**
	 * Add a note.
	 *
	 * @since 1.17
	 *
	 * @see GravityView_Entry_Notes::add_note() This method is mostly a wrapper.
	 *
	 * @param array $entry Entry array.
	 * @param array $data  Note details array.
	 *
	 * @return int|WP_Error Note ID on success, WP_Error on failure.
	 */
	private function add_note( $entry, $data ) {
		global $current_user;

		$user_data = get_userdata( $current_user->ID );

		$note_content = trim( $data['gv-note-content'] );

		if ( empty( $note_content ) ) {
			return new WP_Error( 'gv-add-note-empty', __( 'The note is empty.', 'gk-gravityview' ) );
		}

		return GravityView_Entry_Notes::add_note( $entry['id'], $user_data->ID, $user_data->display_name, $note_content, 'gravityview/field/notes' );
	}

	/**
	 * Get the Add Note form HTML.
	 *
	 * @since 1.17
	 * @since 2.0
	 *
	 * @param array                $atts    Shortcode attributes for entry ID.
	 * @param \GV\Template_Context $context The context, when called outside of a shortcode.
	 *
	 * @return string HTML of the Add Note form, or empty string if the user doesn't have the `gravityview_add_entry_notes` cap.
	 */
	public static function get_add_note_part( $atts, $context = null ) {

		$atts = shortcode_atts( ['entry' => null], $atts );

		if ( ! GVCommon::has_cap( 'gravityview_add_entry_notes' ) ) {
			gravityview()->log->error( 'User does not have permission to add entry notes ("gravityview_add_entry_notes").' );

			return '';
		}

		if ( $context instanceof \GV\Template_Context ) {

			ob_start();
			$context->template->get_template_part( 'note', 'add-note', true );
			$add_note_html = ob_get_clean();

			$visibility_settings = $context->field->notes;

			// The form has to target the same entry the field lists, which in a joined View is the
			// one from the field's own form rather than whichever Multi_Entry holds first.
			$gv_entry = $context->entry->from_field( $context->field, $context->entry );
		} else {
			$gravityview_view = GravityView_View::getInstance();

			ob_start();
			$gravityview_view->get_template_part( 'note', 'add-note' );
			$add_note_html = ob_get_clean();

			$visibility_settings = $gravityview_view->getCurrentFieldSetting( 'notes' );

			if ( $atts['entry'] ) {
				$entry = GFAPI::get_entry( $atts['entry'] );
			}

			if ( ! isset( $entry ) || ! $entry ) {
				$entry = $gravityview_view->getCurrentEntry();
			}

			$gv_entry = \GV\GF_Entry::from_entry( $entry );
		}

		// Strip extra whitespace in template.
		$add_note_html = gravityview_strip_whitespace( $add_note_html );
		$entry_slug    = $gv_entry->get_slug();
		$nonce_field   = wp_nonce_field( 'gv_note_add_' . $entry_slug, 'gv_note_add', false, false );

		// Only generate the dropdown if the field settings allow it.
		$email_fields = '';
		if ( ! empty( $visibility_settings['email'] ) ) {
			$email_fields = self::get_note_email_fields( $entry_slug );
		}

		$add_note_html = str_replace( '{entry_slug}', $entry_slug, $add_note_html );
		$add_note_html = str_replace( '{nonce_field}', $nonce_field, $add_note_html );
		$add_note_html = str_replace( '{show_delete}', (string) intval( empty( $visibility_settings['delete'] ) ? 0 : $visibility_settings['delete'] ), $add_note_html );
		$add_note_html = str_replace( '{email_fields}', $email_fields, $add_note_html );
		$add_note_html = str_replace( '{url}', esc_url_raw( add_query_arg( [] ) ), $add_note_html );

		return $add_note_html;
	}

	/**
	 * Get array of emails addresses from the stored entry.
	 *
	 * @since 1.17
	 *
	 * @return array Array of email addresses connected to the entry.
	 */
	private static function get_note_emails_array() {

		$gravityview_view = GravityView_View::getInstance();

		$email_fields = GFCommon::get_email_fields( $gravityview_view->getForm() );

		$entry = $gravityview_view->getCurrentEntry();

		$note_emails = [];

		foreach ( $email_fields as $email_field ) {
			if ( ! empty( $entry[ "{$email_field->id}" ] ) && is_email( $entry[ "{$email_field->id}" ] ) ) {
				$note_emails[] = $entry[ "{$email_field->id}" ];
			}
		}

		/**
		 * Modify the dropdown values displayed in the "Also email note to" dropdown.
		 *
		 * @since 1.17
		 *
		 * @param array $note_emails Array of email addresses connected to the entry.
		 * @param array $entry       Current entry.
		 */
		$note_emails = apply_filters( 'gravityview/field/notes/emails', $note_emails, $entry );

		return (array) $note_emails;
	}

	/**
	 * Generate a HTML dropdown of email values based on email fields from the current form.
	 *
	 * @since 1.17
	 *
	 * @uses get_note_emails_array()
	 *
	 * @param int|string $entry_slug Current entry unique ID.
	 *
	 * @return string HTML output.
	 */
	private static function get_note_email_fields( $entry_slug = '' ) {

		if ( ! GVCommon::has_cap( 'gravityview_email_entry_notes' ) ) {
			gravityview()->log->error( 'User does not have permission to email entry notes ("gravityview_email_entry_notes").' );

			return '';
		}

		$entry_slug_esc = esc_attr( $entry_slug );

		$note_emails = self::get_note_emails_array();

		$strings = self::strings();

		/**
		 * Whether to include a Custom Email option for users to define a custom email to mail notes to.
		 *
		 * @since 1.17
		 *
		 * @param bool $include_custom Default: true.
		 */
		$include_custom = apply_filters( 'gravityview/field/notes/custom-email', true );

		ob_start();

		if ( ! empty( $note_emails ) || $include_custom ) { ?>
			<div class="gv-note-email-container">
				<label for="gv-note-email-to-<?php echo $entry_slug_esc; ?>" class="screen-reader-text"><?php echo $strings['also-email']; ?></label>
				<select class="gv-note-email-to" name="gv-note-to" id="gv-note-email-to-<?php echo $entry_slug_esc; ?>">
					<option value=""><?php echo $strings['also-email']; ?></option>
					<?php
					foreach ( $note_emails as $email ) {
						?>
						<option value="<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></option>
						<?php
					}
					if ( $include_custom ) {
						?>
						<option value="custom"><?php echo self::strings( 'other-email' ); ?></option>
					<?php } ?>
				</select>
				<fieldset class="gv-note-to-container">
					<?php if ( $include_custom ) { ?>
						<div class='gv-note-to-custom-container'>
							<label for="gv-note-email-to-custom-<?php echo $entry_slug_esc; ?>"><?php echo $strings['email-label']; ?></label>
							<input type="text" name="gv-note-to-custom" placeholder="<?php echo $strings['email-placeholder']; ?>" id="gv-note-to-custom-<?php echo $entry_slug_esc; ?>" value="" />
						</div>
					<?php } ?>
					<div class='gv-note-subject-container'>
						<label for="gv-note-subject-<?php echo $entry_slug_esc; ?>"><?php echo $strings['subject-label']; ?></label>
						<input type="text" name="gv-note-subject" placeholder="<?php echo $strings['subject']; ?>" id="gv-note-subject-<?php echo $entry_slug_esc; ?>" value="" />
					</div>
				</fieldset>
			</div>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * If note has an email to send, and the user has the right caps, send it.
	 *
	 * Note: Tap in to Gravity Forms' `gform_after_email` action if you want a return result from sending the email.
	 *
	 * @since 1.17
	 *
	 * @param false|object $note  If note was created, object. Otherwise, false.
	 * @param array        $entry Entry data.
	 * @param array        $data  $_POST data.
	 *
	 * @return void|false|WP_Error Returns false if email not configured, WP_Error on failure, void on success.
	 */
	private function maybe_send_entry_notes( $note = false, $entry = [], $data = [] ) {

		// Emailing notes is not configured.
		if ( empty( $data['gv-note-to'] ) ) {
			return false;
		}

		// The note is empty.
		if ( ! $note ) {
			return new WP_Error( 'gv-add-note-empty', __( 'The note is empty.', 'gk-gravityview' ) );
		}

		// The user does not have the "gravityview_email_entry_notes" cap.
		if ( ! GVCommon::has_cap( 'gravityview_email_entry_notes' ) ) {
			gravityview()->log->error( 'User doesn\'t have "gravityview_email_entry_notes" cap', ['data' => $note] );
			return new WP_Error( 'gv-email-note-cap', __( 'You do not have permission to email entry notes.', 'gk-gravityview' ) );
		}

		gravityview()->log->debug( '$data', ['data' => $data] );

		$default_data = [
			'gv-note-to'        => '',
			'gv-note-to-custom' => '',
			'gv-note-subject'   => '',
			'gv-note-content'   => '',
			'current-url'       => '',
		];

		$current_user = wp_get_current_user();
		$email_data   = wp_parse_args( $data, $default_data );

		$from = $current_user->user_email;
		$to   = $email_data['gv-note-to'];

		/**
		 * Documented in get_note_email_fields.
		 *
		 * @see get_note_email_fields
		 */
		$include_custom = apply_filters( 'gravityview/field/notes/custom-email', true );

		if ( 'custom' === $to && $include_custom ) {
			$to = $email_data['gv-note-to-custom'];
			gravityview()->log->debug( 'Sending note to a custom email address: {to}', ['to' => $to] );
		}

		if ( ! GFCommon::is_valid_email_list( $to ) ) {
			gravityview()->log->error(
				'$to not a valid email or email list (CSV of emails): {to}',
				[
					'to'   => print_r( $to, true ),
					'data' => $email_data,
				]
			);

			return new WP_Error( 'gv-add-note-invalid-email', __( 'The email address is invalid.', 'gk-gravityview' ) );
		}

		$bcc      = false;
		$reply_to = $from;
		$subject  = trim( $email_data['gv-note-subject'] );

		// We use empty() here because GF uses empty to check against, too. `0` isn't a valid subject to GF
		$subject        = empty( $subject ) ? self::strings( 'default-email-subject' ) : $subject;
		$message        = $email_data['gv-note-content'];
		$email_footer   = self::strings( 'email-footer' );
		$from_name      = $current_user->display_name;
		$message_format = 'html';

		/**
		 * Modify the values passed when sending a note email.
		 *
		 * @since 1.17
		 * @see   GVCommon::send_email
		 *
		 * @param array $email_settings Values being passed to the GVCommon::send_email() method: 'from', 'to', 'bcc', 'reply_to', 'subject', 'message', 'from_name', 'message_format', 'entry', 'email_footer'
		 */
		$email_content = apply_filters( 'gravityview/field/notes/email_content', compact( 'from', 'to', 'bcc', 'reply_to', 'subject', 'message', 'from_name', 'message_format', 'entry', 'email_footer' ) );

		$from           = $email_content['from'] ?? $from;
		$to             = $email_content['to'] ?? $to;
		$bcc            = $email_content['bcc'] ?? $bcc;
		$reply_to       = $email_content['reply_to'] ?? $reply_to;
		$subject        = $email_content['subject'] ?? $subject;
		$message        = $email_content['message'] ?? $message;
		$from_name      = $email_content['from_name'] ?? $from_name;
		$message_format = $email_content['message_format'] ?? $message_format;
		$entry          = $email_content['entry'] ?? $entry;
		$email_footer   = $email_content['email_footer'] ?? $email_footer;

		$is_html = ( 'html' === $message_format );

		// Add the message footer.
		$message .= $this->get_email_footer( $email_footer, $is_html, $email_data );

		/**
		 * Should the message content have paragraphs added automatically, if using HTML message format.
		 *
		 * @since 1.18
		 *
		 * @param bool $wpautop_email True: Apply wpautop() to the email message if using; False: Leave as entered (Default: true).
		 */
		$wpautop_email = apply_filters( 'gravityview/field/notes/wpautop_email', true );

		if ( $is_html && $wpautop_email ) {
			$message = wpautop( $message );
		}

		GVCommon::send_email( $from, $to, $bcc, $reply_to, $subject, $message, $from_name, $message_format, '', $entry, false );

		$form = isset( $entry['form_id'] ) ? GVCommon::get_form( $entry['form_id'] ) : [];

		/**
		 * It's here for compatibility with Gravity Forms.
		 *
		 * @link https://docs.gravityforms.com/gform_post_send_entry_note/
		 *
		 * @since 1.17
		 *
		 * @param string $method The method that was used to send the note.
		 * @param string $to The email address that the note was sent to.
		 * @param string $from The email address that the note was sent from.
		 * @param string $subject The subject of the note.
		 * @param string $message The message of the note.
		 * @param array  $form The form that the note was sent from.
		 * @param array  $entry The entry that the note was sent from.
		 */
		do_action( 'gform_post_send_entry_note', 'GravityView_Field_Notes::maybe_send_entry_notes', $to, $from, $subject, $message, $form, $entry );
	}

	/**
	 * Get the footer for Entry Note emails.
	 *
	 * `{url}` is replaced by the URL of the page where the note form was embedded.
	 *
	 * @since 1.18
	 *
	 * @see GravityView_Field_Notes::strings() The default value of $message_footer is set here, with the key 'email-footer'.
	 *
	 * @param string $email_footer The message footer value.
	 * @param bool   $is_html      True: Email is being sent as HTML; False: sent as text.
	 * @param array  $email_data   Email data array.
	 *
	 * @return string If email footer is not empty, return the message with placeholders replaced with dynamic values.
	 */
	private function get_email_footer( $email_footer = '', $is_html = true, $email_data = [] ) {

		$output = '';

		if ( ! empty( $email_footer ) ) {
			$url = \GV\Utils::get( $email_data, 'current-url' );
			$url = html_entity_decode( $url );
			$url = site_url( $url );

			$content = $is_html ? "<a href='" . esc_url( $url ) . "'>" . esc_html( $url ) . "</a>" : $url;

			$email_footer = str_replace( '{url}', $content, $email_footer );

			$output .= "\n\n$email_footer";
		}

		return $output;
	}
}

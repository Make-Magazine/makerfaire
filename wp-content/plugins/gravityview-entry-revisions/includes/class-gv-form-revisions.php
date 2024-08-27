<?php

/**
 * Class GV_Entry_Revisions
 */
class GV_Form_Revisions {

	const FORM_REVISIONS_ENABLED_SETTING = 'formRevisionsEnabled';

	/**s
	 *
	 * @var \wpdb $db
	 * */
	protected $db;

	/**
	 * @var GV_Form_Revisions
	 */
	static private $instance = null;

	/**
	 * GV_Entry_Revisions constructor.
	 *
	 * @since 1.4.0
	 */
	protected function __construct() {

		if ( self::$instance ) {
			return;
		}

		global $wpdb;
		$this->db = $wpdb;
		$this->init();
	}


	/**
	 * Instantiates the class.
	 *
	 * @since 1.4.0
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Adds hooks on the single entry screen.
	 *
	 * @since 1.4.0
	 */
	public function init() {
		// Show enable/disable revisions option.
		add_filter( 'gform_form_settings_fields', [ $this, 'add_form_settings_fields' ], 20 );

		// Render revisions page. If revisions settings disabled, redirect to the main settings page.
		add_action( 'gform_form_settings_page_form-revisions', [ $this, 'render_form_revisions_list' ] );

		// Add the revisions menu.
		add_filter( 'gform_form_settings_menu', [ $this, 'maybe_add_revisions_menu' ], 10, 2 );

		// Maybe save the form revision.
		add_action( 'gform_form_update_meta', [ $this, 'maybe_save_revision' ], 20, 2 );

		// Init revision actions.
		$this->init_revision_actions();
	}

	/**
	 * Init revision actions.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	protected function init_revision_actions() {

		add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enqueue_assets' ] );

		// Preview form revision.
		add_filter( 'gform_form_post_get_meta', [ $this, 'preview_form_revision' ] );

		// Restore action.
		add_action( 'wp_ajax_gv_restore_revision', [ $this, 'restore_revision' ] );

		// Delete action.
		add_action( 'wp_ajax_gv_delete_revision', [ $this, 'delete_revision' ] );

		// Bulk delete action.
		add_action( 'wp_ajax_gv_delete_revisions', [ $this, 'delete_revisions' ] );
	}

	/**
	 * Enqueues scripts and styles for the form revisions page.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function maybe_enqueue_assets() {

		if ( ! class_exists( 'GFForms' ) ) {
			return;
		}

		if ( 'form_settings_form-revisions' !== GFForms::get_page() ) {
			return;
		}

		$this->enqueue_assets();
	}

	/**
	 * Checks if revisions enabled.
	 *
	 * @since 1.4.0
	 *
	 * @param int $form_id
	 *
	 * @return bool
	 */
	protected function is_revisions_enabled( $form_id ) {
		if ( ! $form_id || ! is_numeric( $form_id ) ) {
			return false;
		}
		$form = RGFormsModel::get_form_meta( $form_id );

		return ! empty( $form[ self::FORM_REVISIONS_ENABLED_SETTING ] );
	}

	/**
	 * Handles delete revisions AJAX call.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function delete_revisions() {
		try {
			$ids = (array) rgpost( 'ids' );

			if ( ! $ids || ! GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ||
			     ! wp_verify_nonce( rgpost( 'nonce' ), 'gform_form_revision_list_action' )
			) {
				throw new Exception( esc_html__( 'Forbidden!', 'gk-gravityrevisions' ) );
			}

			if ( ! $this->delete_form_revisions( $ids ) ) {
				throw new Exception( _n( 'Could not delete the revision.', 'Could not delete the revisions.', count( $ids ), 'gk-gravityrevisions' ) );
			}

			wp_send_json_success( esc_html__( 'Successfully deleted.', 'gk-gravityrevisions' ) );

		} catch ( Exception $e ) {
			wp_send_json_error( esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Handles delete single revision AJAX call.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function delete_revision() {
		try {
			$id = rgpost( 'id' );

			if ( ! $id || ! GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ||
			     ! wp_verify_nonce( rgpost( 'nonce' ), 'form_revision_delete_' . $id )
			) {
				throw new Exception( esc_html__( 'Forbidden!', 'gk-gravityrevisions' ) );
			}

			if ( ! $this->delete_form_revision( $id ) ) {
				throw new Exception( esc_html__( 'Could not delete the revision.', 'gk-gravityrevisions' ) );
			}

			wp_send_json_success( esc_html__( 'Successfully deleted.', 'gk-gravityrevisions' ) );

		} catch ( Exception $e ) {
			wp_send_json_error( esc_html( $e->getMessage() ) );
		}
	}

	/**
	 * Handles restore revision AJAX call.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function restore_revision() {
		try {
			$id      = rgpost( 'id' );
			$form_id = rgpost( 'form_id' );

			if ( ! $id || ! $form_id ||
			     ! GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ||
			     ! wp_verify_nonce( rgpost( 'nonce' ), 'form_revision_restore_' . $id )
			) {
				throw new Exception( esc_html__( 'Forbidden!', 'gk-gravityrevisions' ) );
			}

			if ( ! $revision = $this->get_form_revision( $id ) ) {
				throw new Exception( esc_html__( 'Could not find revision to restore.', 'gk-gravityrevisions' ) );
			}

			$this->update_form_meta( $form_id, $revision );

			GFFormsModel::flush_current_form( GFFormsModel::get_form_cache_key( $form_id ) );

			wp_send_json_success( esc_html__( 'The form was successfully restored!', 'gk-gravityrevisions' ) );

		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Enqueue the plugin script only on the form-revisions subview page
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$script_src = plugins_url( 'assets/js/admin' . $min . '.js', GV_ENTRY_REVISIONS_FILE );

		wp_enqueue_script( 'gv-revisions', $script_src, [
			'jquery',
			'thickbox',
		], GV_ENTRY_REVISIONS_VERSION, true );

		wp_localize_script( 'gv-revisions', 'gvRevisions', [
			'confirmRestore' => __( 'Continuing will overwrite your current form. Are you sure you want to continue?', 'gk-gravityrevisions' ),
			'confirmDelete'  => __( 'Continuing will delete your revision. Are you sure you want to continue?', 'gk-gravityrevisions' ),
			'confirmDeletes' => __( 'Continuing will delete your revisions. Are you sure you want to continue?', 'gk-gravityrevisions' ),
		] );

		wp_enqueue_style( 'thickbox' );
	}

	/**
	 * Adds the Form Revisions menu item to the form Settings
	 *
	 * @since 1.4.0.
	 *
	 * @param array $setting_tabs
	 *
	 * @return array
	 */
	public function maybe_add_revisions_menu( $setting_tabs, $form_id ) {
		if ( ! $this->is_revisions_enabled( $form_id ) ) {
			return $setting_tabs;
		}

		$setting_tabs[] = [
			'name'         => 'form-revisions',
			'label'        => esc_html__( 'Form Revisions', 'gk-gravityrevisions' ),
			'icon'         => 'dashicons-edit-page',
			'capabilities' => [ 'gravityforms_edit_forms' ],
		];

		return $setting_tabs;
	}

	/**
	 * Render the Form Revisions list table.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function render_form_revisions_list() {
		if ( ! class_exists( 'GFFormSettings' ) ) {
			require_once( GFCommon::get_base_path() . '/form_settings.php' );
		}

		$form_id = (int) rgget( 'id' );

		if ( ! $this->is_revisions_enabled( $form_id ) ) {
			wp_safe_redirect( add_query_arg( [ 'subview' => 'settings' ] ) );
		}

		GFFormSettings::initialize_settings_renderer();

		GFFormSettings::page_header();

		require_once GV_ENTRY_REVISIONS_DIR . 'includes/class-gv-form-revisions-list.php';
		$form_revisions_list = new GV_Form_Revisions_List( $this->get_form_revisions( $form_id ) );
		$form_revisions_list->prepare_items();
		?>

        <div class="gform-settings-panel">
            <header class="gform-settings-panel__header">
                <h4 class="gform-settings-panel__title"><?php esc_html_e( 'Form Revisions', 'gk-gravityrevisions' ); ?></h4>
            </header>

            <div class="gform-settings-panel__content">
                <form id="notification_list_form" method="post">
					<?php
					$form_revisions_list->display();
					wp_nonce_field( 'gform_form_revision_list_action', 'gform_form_revision_list_action' );
					?>
                </form>
            </div>
        </div>
        <style>
			.gk-form-revision-actions {
				display: flex;
				gap: 0 1.2em;
				flex-wrap: wrap;
			}

			.gk-form-revision-actions a:hover {
				text-decoration: underline;
			}

			#gf-admin-notices-wrapper .notice.gv-notice {
				display: block;
				padding: 20px;
			}

			#notification_list_form {
				position: relative;
			}

			#notification_list_form .gform-loader {
				position: absolute;
				width: 16px;
				height: 16px;
				font-size: 2px;
				top: 11px;
				right: 5px;
			}
        </style>
		<?php
		GFFormSettings::page_footer();
	}

	/**
	 * Modifies the form data to show the revision preview.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function preview_form_revision( $form ) {
		$form_revision_id = rgget( 'form_revision_id' );
		$form_id          = rgget( 'id' );
		if ( ! $form_revision_id || ! $form_id ) {
			return $form;
		}
		$revision = $this->get_form_revision( $form_revision_id );

		return $revision ? array_merge( $form, $revision ) : $form;
	}

	/**
	 * Get form revision data as array.
	 *
	 * @since 1.4.0
	 *
	 * @param int $revision_id
	 *
	 * @return array
	 */
	protected function get_form_revision( $revision_id ) {

		$revision_field = $this->get_revision_field( $revision_id );
		$revision       = GFFormsModel::unserialize( $revision_field );

		if ( is_array( $revision ) && ! empty( $revision['fields'] ) ) {
			foreach ( $revision['fields'] as $k => $v ) {
				$revision['fields'][ $k ] = GF_Fields::create( $v );
			}
		}

		return is_array( $revision ) ? $revision : [];
	}

	/**
	 * Updates the form meta with the revision data on restore.
	 *
	 * @since 1.4.0
	 *
	 * @param array $revision
	 *
	 * @param int $form_id
	 *
	 * @return int
	 */
	protected function update_form_meta( $form_id, $revision ) {
		$table = GFFormsModel::get_meta_table_name();

		$query = $this->db->prepare(
			"UPDATE $table SET display_meta=%s WHERE form_id=%d",
			wp_json_encode( $revision ), $form_id
		);

		return $this->db->query( $query );
	}

	/**
	 * @since 1.4.0
	 *
	 * @param $form_id
	 *
	 * @return array
	 */
	protected function get_form_meta( $form_id ) {
		$form_meta = GFFormsModel::get_form_meta( $form_id );

		// These fields never exist in the revision meta
		if ( is_array( $form_meta ) ) {
			unset( $form_meta['notifications'], $form_meta['confirmations'] );
		}

		return is_array( $form_meta ) ? $form_meta : [];
	}

	/**
	 * Delete form revision from the database.
	 *
	 * @since 1.4.0
	 *
	 * @param int|string $revision_id Revision ID. Will be cast to int.
	 *
	 * @return bool|int|mysqli_result|null The result of the query.
	 */
	protected function delete_form_revision( $revision_id ) {
		$revisions_table_name = GFFormsModel::get_form_revisions_table_name();

		$query = $this->db->prepare(
			"DELETE FROM $revisions_table_name WHERE id=%d",
			(int) $revision_id
		);

		return $this->db->query( $query );
	}

	/**
	 * Delete multiple form revisions from the database.
	 *
	 * @since 1.4.0
	 *
	 * @param int[] $revision_ids Revision IDs.
	 *
	 * @return bool|int|mysqli_result|null The result of the query.
	 */
	protected function delete_form_revisions( $revision_ids ) {
		$revisions_table_name = GFFormsModel::get_form_revisions_table_name();

		// Create an CSV filled with the string '%d' repeated as many times as there are IDs in $revision_ids.
		$placeholders = implode( ',', array_fill( 0, count( $revision_ids ), '%d' ) );

		// We're using %d as a placeholder in the query, so we're safe...but for clarity, make sure all IDs are integers.
		$revision_ids = array_map( 'intval', $revision_ids );

		$query = $this->db->prepare(
			"DELETE FROM $revisions_table_name WHERE id IN ($placeholders)",
			...$revision_ids // The spread operator unpacks the array into individual arguments, which replace %d's.
		);

		return $this->db->query( $query );
	}

	/**
	 * Gets the form revisions.
	 *
	 * @since 1.4.0
	 *
	 * @param int $form_id
	 *
	 * @return array
	 */
	protected function get_form_revisions( $form_id, $limit = null ) {
		$revisions_table_name = GFFormsModel::get_form_revisions_table_name();
		$query                = "SELECT id, form_id, display_meta, date_created FROM $revisions_table_name WHERE form_id=%d ORDER BY id DESC";
		if ( $limit ) {
			$query .= $this->db->prepare( ' LIMIT %d', (int) $limit );
		}

		$revisions = $this->db->get_results(
			$this->db->prepare( $query, $form_id ), ARRAY_A
		);

		return is_array( $revisions ) ? $revisions : [];
	}

	/**
	 * Adds an option to enable or disable Form revisions for each form.
	 *
	 * @since 1.4.0
	 *
	 * @param array $fields Form settings fields.
	 *
	 * @return array
	 */
	public function add_form_settings_fields( $fields ) {

		$fields['form_options']['fields'][] = [
			'name'    => self::FORM_REVISIONS_ENABLED_SETTING,
			'type'    => 'toggle',
			'label'   => esc_html__( 'Enable form revisions', 'gk-gravityrevisions' ),
			'tooltip' => '<strong>' . esc_html__( 'Enable form revisions', 'gk-gravityrevisions' ) . '</strong>' . esc_html__( 'Check this option to enable form revisions.', 'gk-gravityrevisions' ),
		];

		return $fields;
	}

	/**
	 * Saves revision on the form update if it's different from the current version.
	 *
	 * @since 1.4.0
	 *
	 * @param int $form_id
	 *
	 * @param array $new_form_meta
	 */
	public function maybe_save_revision( $new_form_meta, $form_id ) {
		if ( 'form_editor_save_form' !== rgpost( 'action' ) ) {
			return $new_form_meta;
		}

		if ( ! $this->is_revisions_enabled( $form_id ) ) {
			return $new_form_meta;
		}

		$latest_revision_id   = $this->get_latest_revision_id( $form_id );
		$latest_revision_meta = $this->get_revision_field( $latest_revision_id );
		$current_form_meta    = $this->get_form_meta( $form_id );

		if ( ( wp_json_encode( $new_form_meta ) === wp_json_encode( $current_form_meta ) ) ||
		     ( $latest_revision_meta === wp_json_encode( $current_form_meta ) ) ) { // This is the case when GF automatically created the revision before for consent form.
			return $new_form_meta;
		}

		// Add the ID of the user who made the change to the revision.
		$current_form_meta['revision_user_id'] = get_current_user_id();

		$revisions_table_name = GFFormsModel::get_form_revisions_table_name();
		$this->db->query( $this->db->prepare(
			"INSERT INTO $revisions_table_name(form_id, display_meta, date_created) VALUES(%d, %s, utc_timestamp())", $form_id, wp_json_encode( $current_form_meta )
		) );

		return $new_form_meta;
	}

	/**
	 * Gets the revision meta from the database.
	 *
	 * @since 1.4.0
	 *
	 * @param int $revision_id Revision ID.
	 *
	 * @return string JSON encoded form
	 */
	public function get_revision_field( $revision_id ) {
		$revisions_table_name = GFFormsModel::get_form_revisions_table_name();

		return $this->db->get_var( $this->db->prepare(
			"SELECT display_meta FROM $revisions_table_name WHERE id=%d", (int) $revision_id ) );
	}

	/**
	 * Gets the latest revision id. Just a wrapper for the Gravity Forms function.
	 *
	 * @since 1.4.0
	 *
	 * @param int $form_id
	 *
	 * @return int Latest revision ID.
	 */
	public function get_latest_revision_id( $form_id ) {
		return GFFormsModel::get_latest_form_revisions_id( $form_id );
	}
}

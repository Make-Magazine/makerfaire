<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * @see GFNotificationTable
 * */
class GV_Form_Revisions_List extends WP_List_Table {

	protected $_revisions;

	/**
	 * @since 1.4.0
	 *
	 * @param array $revisions The form revisions to display.
	 * @param array|string $args {
	 *      Array or string of arguments.
	 *
	 * @type string $plural Plural value used for labels and the objects being listed.
	 *                             This affects things such as CSS class-names and nonces used
	 *                             in the list table, e.g. 'posts'. Default empty.
	 * @type string $singular Singular label for an object being listed, e.g. 'post'.
	 *                             Default empty
	 * @type bool $ajax Whether the list table supports Ajax. This includes loading
	 *                             and sorting data, for example. If true, the class will call
	 *                             the _js_vars() method in the footer to provide variables
	 *                             to any scripts handling Ajax events. Default false.
	 * @type string $screen String containing the hook name used to determine the current
	 *                             screen. If left null, the current screen will be automatically set.
	 *                             Default null.
	 * }
	 */
	public function __construct( $revisions, $args = [] ) {
		$this->_revisions = $revisions;

		parent::__construct( $args );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 */
	public function no_items() {
		echo '<h3 style="margin-top:.5em;">' . esc_html__( 'No form revisions found.', 'gk-gravityrevisions' ) .
		     '</h3>';
		echo '<p>' . esc_html__( 'No changes have been made since enabling revisions. Saved changes will appear here.',
				'gk-gravityrevisions' ) . '</p>';
		echo '<p><a href="https://docs.gravitykit.com/article/481-form-revisions" rel="noopener noreferrer" 
                target="_blank">' . esc_html__( 'Learn more about using form revisions.', 'gk-gravityrevisions' ) .
		     '</a></p>';
	}

	/**
	 * Overrides the parent method to prevent the table navigation from being displayed when there are no items.
	 *
	 * @param string $which The location of the navigation: Either 'top' or 'bottom'.
	 *
	 * @return void
	 */
	public function display_tablenav( $which ) {
		if ( ! $this->has_items() ) {
			return;
		}

		parent::display_tablenav( $which );
	}

	/**
	 * Define the column headers.
	 *
	 * @since 1.4.0
	 *
	 * @return string[]
	 */
	public function get_columns() {
		return [
			'cb'                         => '<input type="checkbox" />',
			'form_revision_date_created' => esc_html__( 'Revision date', 'gk-gravityrevisions' ),
			'form_revision_user'         => esc_html__( 'Created by', 'gk-gravityrevisions' ),
			'form_revision_field_count'  => esc_html__( 'Number of fields', 'gk-gravityrevisions' ),
			'form_revision_actions'      => esc_html__( 'Actions', 'gk-gravityrevisions' ),
		];
	}

	/**
	 * Prepare the data to display.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function prepare_items() {
		$form_id = (int) rgget( 'id' );

		$current_form = GFFormsModel::get_form_meta( $form_id );

		$this->items = $this->_revisions;

		if ( ! empty( $this->items ) ) {
			// Add the current form as the first row.
			$current_form_row = [
				'id'           => 0,
				'form_id'      => $form_id,
				'date_created' => rgar( $current_form, 'date_created', null ),
				'display_meta' => wp_json_encode( $current_form ),
			];
			array_unshift( $this->items, $current_form_row );
		}


		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = [];
		$this->_column_headers = [ $columns, $hidden, $sortable ];
	}

	/**
	 * Provides bulk actions.
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return [ 'delete' => __( 'Delete', 'gk-gravityrevisions' ) ];
	}

	/**
	 * Renders the date the form revision was created.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current item.
	 *
	 * @return string
	 */
	function column_form_revision_date_created( $item ) {

		$date = $item['date_created'];

		if ( empty( $item['id'] ) ) {
			return '<strong style="display:block;">' . esc_html__( 'Current Form', 'gk-gravityrevisions' ) . '</strong>';
		}

		$ago = esc_html( human_time_diff( strtotime( $date ) ) );

		// translators: %s is the relative time since the form revision was created.
		return sprintf( esc_html__( '%s ago', 'gk-gravityrevisions' ), $ago ) .
		       '<div style="opacity:.7;">' . esc_html( $date ) . '</div>';
	}

	/**
	 * Renders the user who created the form revision.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current item.
	 *
	 * @return int|null
	 */
	public function column_form_revision_user( $item ) {

		if ( empty( $item['id'] ) ) {
			return '';
		}

		// Check if the user for this row is editable.
		if ( ! current_user_can( 'list_users' ) ) {
			return esc_html__( 'You do not have permission to view this user.', 'gk-gravityrevisions' );
		}

		$form_meta = json_decode( $item['display_meta'], true );

		if ( ! isset( $form_meta['revision_user_id'] ) ) {
			return esc_html__( 'N/A', 'gk-gravityrevisions' );
		}

		$user = get_user_by( 'id', $form_meta['revision_user_id'] );

		if ( ! $user ) {
			return esc_html__( 'N/A', 'gk-gravityrevisions' );
		}

		// Set up the user editing link.
		$edit_link = add_query_arg(
			'wp_http_referer',
			urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
			get_edit_user_link( $user->ID )
		);

		$avatar = $this->get_avatar( $user->ID );

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $edit_link ),
				$avatar . esc_html( $user->display_name ) );
		}

		return $avatar . esc_html( $user->display_name );
	}

	/**
	 * Generates the avatar for the user who created the form revision.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string The user avatar.
	 */
	private function get_avatar( $user_id ) {

		$margin_dir = is_rtl() ? 'margin-left' : 'margin-right';

		$actual_size = 24;
		$retina_size = $actual_size * 2; // Generate the avatar at 2x the size, but display at 1x.

		$styles = [
			'width'          => $actual_size . 'px',
			'height'         => $actual_size . 'px',
			'border-radius'  => '50%', // Make it round.
			'vertical-align' => 'middle',
			$margin_dir      => '.45em',
		];

		$style = '';

		foreach ( $styles as $key => $value ) {
			$style .= $key . ':' . $value . ';';
		}

		return get_avatar( $user_id, $retina_size, '', '', [ 'extra_attr' => 'style="' . $style . '"' ] );
	}

	/**
	 * Renders the number of fields in the form revision.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current item.
	 *
	 * @return int|null
	 */
	public function column_form_revision_field_count( $item ) {
		$form_meta = json_decode( $item['display_meta'], true );

		if ( ! isset( $form_meta['fields'] ) ) {
			return esc_html__( 'N/A', 'gk-gravityrevisions' );
		}

		return count( $form_meta['fields'] );
	}

	/**
	 * Render revision actions.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_form_revision_actions( $item ) {
		ob_start(); ?>
        <div class="gk-form-revision">
            <ul class="gk-form-revision-actions">
		        <?php if ( empty( $item['id'] ) ) { ?>
                    <li><?php $this->render_edit_button( $item ); ?></li>
		        <?php } else { ?>
                    <li><?php $this->render_preview_button( $item ); ?></li>
                    <li><?php $this->render_restore_button( $item ); ?></li>
                    <li><?php $this->render_delete_button( $item ); ?></li>
		        <?php } ?>
            </ul>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render edit form button for the current form.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current revision (or the current form).
	 *
	 * @return void
	 */
	protected function render_edit_button( $item ) {
		$anchor = __( 'Edit Current Form', 'gk-gravityrevisions' );

		// translators: %d is the revision ID.
		$title = sprintf( __( 'Edit Form Revision #%d', 'gk-gravityrevisions' ), $item['id'] );

		$edit_link = add_query_arg( [
			'page' => 'gf_edit_forms',
			'id'   => $item['form_id'],
		], admin_url( 'admin.php' ) );

		printf(
			'<a class="gv-form-revision-edit" title="%1$s" href="%2$s">%3$s</a>',
			esc_attr( $title ),
			esc_url( $edit_link ),
			esc_html( $anchor )
		);
	}

	/**
	 * Render preview button.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item
	 *
	 * @return void
	 */
	protected function render_preview_button( $item ) {
		$anchor = __( 'Preview', 'gk-gravityrevisions' );

		// translators: %d is the revision ID.
		$title = sprintf( __( 'Preview Form Revision #%d', 'gk-gravityrevisions' ), $item['id'] );

		$preview_args = array(
			'form_id' => $item['form_id'],
		);

		$preview_data = GFCommon::get_preview_link_data( $preview_args );

		$link = add_query_arg( [
			'form_revision_id' => $item['id'],
			'TB_iframe'        => 'true',
			'width'            => 800,
			'height'           => 600,
		], $preview_data['url'] );

		printf(
			'<a class="gv-form-revision-preview thickbox" target="_blank" rel="noopener" title="%1$s" href="%2$s">%3$s%4$s</a>',
			esc_attr( $title ),
			esc_url( $link ),
			esc_html( $anchor ),
			'<span class="screen-reader-text"> ' . esc_html__( '(This link opens in a new window.)', 'gk-gravityrevisions' ) . '</span>'
		);
	}

	/**
	 * Render restore button.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current revision.
	 *
	 * @return void
	 */
	protected function render_restore_button( $item ) {
		$anchor = __( 'Restore', 'gk-gravityrevisions' );

		// translators: %d is the revision ID.
		$title = sprintf( __( 'Restore form revision #%d', 'gk-gravityrevisions' ), $item['id'] );

		printf(
			'<a class="gv-form-revision-restore" data-id="%1$s" data-form-id="%2$s" data-nonce="%3$s" title="%4$s" href="#">%5$s</a>',
			esc_attr( $item['id'] ),
			esc_attr( $item['form_id'] ),
			esc_attr( wp_create_nonce( 'form_revision_restore_' . $item['id'] ) ),
			esc_attr( $title ),
			esc_html( $anchor )
		);
	}

	/**
	 * Render delete button.
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current revision.
	 *
	 * @return void
	 */
	protected function render_delete_button( $item ) {
		$anchor = __( 'Delete', 'gk-gravityrevisions' );

		// translators: %d is the revision ID.
		$title = sprintf( __( 'Delete form revision #%d', 'gk-gravityrevisions' ), $item['id'] );

		printf(
			'<a class="gv-form-revision-delete delete" style="color:#b32d2e;border:none;" data-id="%1$s" data-form-id="%2$s" data-nonce="%3$s" title="%4$s" href="#">%5$s</a>',
			esc_attr( $item['id'] ),
			esc_attr( $item['form_id'] ),
			esc_attr( wp_create_nonce( 'form_revision_delete_' . $item['id'] ) ),
			esc_attr( $title ),
			esc_html( $anchor )
		);
	}

	/**
	 * Define the checkbox column
	 *
	 * @since 1.4.0
	 *
	 * @param array $item The current revision.
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		if ( empty( $item['id'] ) ) {
			return '';
		}

		return sprintf( '<input type="checkbox" name="revisions[]" value="%s" />', $item['id'] );
	}
}

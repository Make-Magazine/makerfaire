<?php
/**
 * GravityView Admin class.
 *
 * PSR-4 migration of the legacy GravityView_Admin class.
 *
 * @package GravityKit\GravityView\Admin
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

use GFFormsModel;
use GravityKit\GravityView\Entry\Approval\EntriesList;
use GravityKit\GravityView\Entry\Approval\GFEntriesPageBulkActions;
use GravityView_Admin_Add_Shortcode;
use GravityView_Admin_Duplicate_View;
use GravityView_Admin_Metaboxes;
use GravityView_Admin_No_Conflict;
use GravityView_Admin_Notices;
use GravityView_Admin_Views;
use GravityView_Change_Entry_Creator;
use GravityView_Compatibility;
use GravityView_GF_Entries_List;
use GravityView_Migrate;
use GravityView_Render_Settings;
use GravityView_Support_Port;
use GravityView_Welcome;
use GVCommon;

use function gravityview;
use function gravityview_get_floaty;
use function gravityview_get_form;
use function gravityview_get_form_id;
use function gravityview_get_link;

class Admin {

	/**
	 * Prevents duplicate hook registration when instantiated multiple times.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $hooks_added = false;

	function __construct() {

		if ( ! is_admin() ) {
			return; }

		// If Gravity Forms isn't active or compatibile, stop loading
		if ( false === gravityview()->plugin->is_compatible() ) {
			return;
		}

		$this->include_required_files();

		if ( ! self::$hooks_added ) {
			$this->add_hooks();
			self::$hooks_added = true;
		}
	}

	/**
	 * Instantiate admin component classes.
	 *
	 * Class files are resolved via Composer autoloading (PSR-4 and classmap).
	 *
	 * @since 1.15
	 * @since 3.0.0 Removed direct file loading in favor of Composer autoloading.
	 *
	 * @return void
	 */
	private function include_required_files() {
		new GravityView_Migrate();
		new GravityView_Admin_Metaboxes();
		new GravityView_GF_Entries_List();
		new GravityView_Change_Entry_Creator();
		new GravityView_Support_Port();
		new GravityView_Admin_Duplicate_View();
		new GravityView_Admin_No_Conflict();
	}

	/**
	 * @since 1.7.5
	 * @return void
	 */
	private function add_hooks() {

		// Filter Admin messages
		add_filter( 'post_updated_messages', [ $this, 'post_updated_messages' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'post_updated_messages' ] );

		add_action( 'plugins_loaded', [ $this, 'backend_actions' ], 100 );

		add_action( 'gravityview/metaboxes/data-source/before', [ self::class, 'connected_form_warning' ] );
	}

	/**
	 * Get text for no views found.
	 *
	 * @since 1.18 Moved to GravityView_Admin
	 *
	 * @return string HTML message with no container tags.
	 */
	public static function no_views_text() {

		if ( isset( $_REQUEST['post_status'] ) && 'trash' === $_REQUEST['post_status'] ) {
			return esc_html__( 'No Views found in Trash', 'gk-gravityview' );
		} elseif ( ! empty( $_GET['s'] ) ) {
			return esc_html__( 'No Views found.', 'gk-gravityview' );
		}

		// Floaty the Astronaut says "oi"
		$image = self::get_floaty();

		if ( GVCommon::has_cap( 'edit_gravityviews' ) ) {
			$output = sprintf( esc_attr__( "%1\$sYou don't have any active views. Let&rsquo;s go %2\$screate one%3\$s!%4\$s\n\nIf you feel like you're lost in space and need help getting started, check out the %5\$sGetting Started%6\$s page.", 'gk-gravityview' ), '<h3>', '<a href="' . admin_url( 'post-new.php?post_type=gravityview' ) . '">', '</a>', '</h3>', '<a href="' . admin_url( 'edit.php?post_type=gravityview&page=gv-getting-started' ) . '">', '</a>' );
		} else {
			$output = esc_attr__( 'There are no active Views', 'gk-gravityview' );
		}

		return $image . wpautop( $output );
	}

	/**
	 * Display error HTML in Edit View when the form is in the trash or no longer exists in Gravity Forms
	 *
	 * @since 1.19
	 *
	 * @param int $form_id Gravity Forms
	 *
	 * @return void
	 */
	public static function connected_form_warning( $form_id = 0 ) {
		global $pagenow;

		if ( empty( $form_id ) || 'post-new.php' === $pagenow ) {
			return;
		}

		$form_info = GFFormsModel::get_form( $form_id, true );

		$error = '';
		if ( empty( $form_info ) ) {
			$error  = esc_html__( 'The form connected to this View no longer exists.', 'gk-gravityview' );
			$error .= ' ' . esc_html__( 'Select another form as the data source for this View.', 'gk-gravityview' );
		} elseif ( $form_info->is_trash ) {
			$error  = esc_html__( 'The connected form is in the trash.', 'gk-gravityview' );
			$error .= ' ' . gravityview_get_link( admin_url( 'admin.php?page=gf_edit_forms&filter=trash&s=' . $form_info->title ), esc_html__( 'Restore the form from the trash', 'gk-gravityview' ) );
			$error .= ' ' . esc_html__( 'or select another form.', 'gk-gravityview' );
		}

		if ( $error ) {
			?>
			<div class="wp-dialog notice-warning inline error wp-clearfix">
				<?php echo gravityview_get_floaty(); ?>
				<h3><?php echo $error; ?></h3>
			</div>
			<?php
		}

		remove_action( 'gravityview/metaboxes/data-source/before', [ self::class, 'connected_form_warning' ] );
	}

	/**
	 * Launch admin objects.
	 *
	 * Class files are resolved via Composer autoloading (PSR-4 and classmap).
	 *
	 * @since 3.0.0 Removed direct file loading in favor of Composer autoloading.
	 *
	 * @return void
	 */
	public function backend_actions() {
		GravityView_Admin_Views::get_instance();
		new GravityView_Welcome();
		new GravityView_Admin_Add_Shortcode();
		new EntriesList();
		new GFEntriesPageBulkActions();

		GravityView_Render_Settings::register_hooks();

		/**
		 * Triggered after all GravityView admin files are loaded.
		 *
		 * Nice place to insert extensions' backend stuff.
		 *
		 * @since 1.0.7
		 */
		do_action( 'gravityview_include_backend_actions' );
	}

	/**
	 * Get an image of our intrepid explorer friend
	 *
	 * @return string HTML image tag with floaty's cute mug on it
	 */
	public static function get_floaty() {
		return gravityview_get_floaty();
	}

	/**
	 * Filter Admin messages
	 *
	 * @param  array $messages Existing messages
	 * @return array                Messages with GravityView views!
	 */
	function post_updated_messages( $messages, $bulk_counts = null ) {
		global $post;

		$post_id = get_the_ID();

		// By default, there will only be one item being modified.
		// When in the `bulk_post_updated_messages` filter, there will be passed a number
		// of modified items that will override this array.
		$bulk_counts = is_null( $bulk_counts ) ? [
			'updated'   => 1,
			'locked'    => 1,
			'deleted'   => 1,
			'trashed'   => 1,
			'untrashed' => 1,
		] : $bulk_counts;

		// If we're starting fresh, a new form was created.
		// We should let the user know this is the case.
		$start_fresh = get_post_meta( $post_id, '_gravityview_start_fresh', true );

		$new_form_text = '';

		if ( ! empty( $start_fresh ) ) {

			// Get the form that was created
			$connected_form = gravityview_get_form_id( $post_id );

			if ( ! empty( $connected_form ) ) {
				$form           = gravityview_get_form( $connected_form );
				$form_name      = esc_attr( $form['title'] );
				$image          = self::get_floaty();
				/* translators: %s: the name of the newly created form. */
				$new_form_text .= '<h3>' . $image . sprintf( __( 'A new form was created for this View: "%s"', 'gk-gravityview' ), $form_name ) . '</h3>';
				$new_form_text .= sprintf(
					/* translators: 1, 2: opening/closing <strong> tags; 3, 4: opening/closing link tags to preview the form; 5: shortcode; 6, 7: opening/closing link tags to edit the form; 8, 9: opening/closing link tags to edit form settings. */
					__(
						'%1$sThere are no entries for the new form, so the View will also be empty.%2$s To start collecting entries, you can add submissions through %3$sthe preview form%4$s and also embed the form on a post or page using this code: %5$s

                    You can %6$sedit the form%7$s in Gravity Forms and the updated fields will be available here. Don&rsquo;t forget to %8$scustomize the form settings%9$s.
                    ',
						'gk-gravityview'
					),
					'<strong>',
					'</strong>',
					'<a href="' . site_url( '?gf_page=preview&amp;id=' . $connected_form ) . '">',
					'</a>',
					'<code>[gravityform id="' . $connected_form . '" name="' . $form_name . '"]</code>',
					'<a href="' . admin_url( 'admin.php?page=gf_edit_forms&amp;id=' . $connected_form ) . '">',
					'</a>',
					'<a href="' . admin_url( 'admin.php?page=gf_edit_forms&amp;view=settings&amp;id=' . $connected_form ) . '">',
					'</a>'
				);
				$new_form_text  = wpautop( $new_form_text );

				delete_post_meta( $post_id, '_gravityview_start_fresh' );
			}
		}

		$messages['gravityview'] = [
			0           => '', // Unused. Messages start at index 1.
			/* translators: %s and %s are HTML tags linking to the View on the website */
			1           => sprintf( __( 'View updated. %1$sView on website.%2$s', 'gk-gravityview' ), '<a href="' . get_permalink( $post_id ) . '">', '</a>' ),
			/* translators: %s and %s are HTML tags linking to the View on the website */
			2           => sprintf( __( 'View updated. %1$sView on website.%2$s', 'gk-gravityview' ), '<a href="' . get_permalink( $post_id ) . '">', '</a>' ),
			3           => __( 'View deleted.', 'gk-gravityview' ),
			/* translators: %s and %s are HTML tags linking to the View on the website */
			4           => sprintf( __( 'View updated. %1$sView on website.%2$s', 'gk-gravityview' ), '<a href="' . get_permalink( $post_id ) . '">', '</a>' ),
			/* translators: %s: date and time of the revision */
			5           => isset( $_GET['revision'] ) ? sprintf( __( 'View restored to revision from %s', 'gk-gravityview' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s and %s are HTML tags linking to the View on the website */
			6           => sprintf( __( 'View published. %1$sView on website.%2$s', 'gk-gravityview' ), '<a href="' . get_permalink( $post_id ) . '">', '</a>' ) . $new_form_text,
			/* translators: %s and %s are HTML tags linking to the View on the website */
			7           => sprintf( __( 'View saved. %1$sView on website.%2$s', 'gk-gravityview' ), '<a href="' . get_permalink( $post_id ) . '">', '</a>' ) . $new_form_text,
			8           => __( 'View submitted.', 'gk-gravityview' ),
			9           => sprintf(
				/* translators: Date and time the View is scheduled to be published */
				__( 'View scheduled for: %1$s.', 'gk-gravityview' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'gk-gravityview' ), strtotime( $post->post_date ?? '' ) )
			) . $new_form_text,
			/* translators: %s and %s are HTML tags linking to the View on the website */
			10          => sprintf( __( 'View draft updated. %1$sView on website.%2$s', 'gk-gravityview' ), '<a href="' . get_permalink( $post_id ) . '">', '</a>' ) . $new_form_text,

			/**
			 * These apply to `bulk_post_updated_messages`
			 *
			 * @file wp-admin/edit.php
			 */
			/* translators: %s: number of Views. */
			'updated'   => _n( '%s View updated.', '%s Views updated.', $bulk_counts['updated'], 'gk-gravityview' ),
			/* translators: %s: number of Views. */
			'locked'    => _n( '%s View not updated, somebody is editing it.', '%s Views not updated, somebody is editing them.', $bulk_counts['locked'], 'gk-gravityview' ),
			/* translators: %s: number of Views. */
			'deleted'   => _n( '%s View permanently deleted.', '%s Views permanently deleted.', $bulk_counts['deleted'], 'gk-gravityview' ),
			/* translators: %s: number of Views. */
			'trashed'   => _n( '%s View moved to the Trash.', '%s Views moved to the Trash.', $bulk_counts['trashed'], 'gk-gravityview' ),
			/* translators: %s: number of Views. */
			'untrashed' => _n( '%s View restored from the Trash.', '%s Views restored from the Trash.', $bulk_counts['untrashed'], 'gk-gravityview' ),
		];

		return $messages;
	}


	/**
	 * Get admin notices
	 *
	 * @deprecated 3.0.0 1.12
	 * @return array
	 */
	public static function get_notices() {
		return GravityView_Admin_Notices::get_notices();
	}

	/**
	 * Add a notice to be displayed in the admin.
	 *
	 * @deprecated 3.0.0 1.12
	 * @param array $notice Array with `class` and `message` keys. The message is not escaped.
	 */
	public static function add_notice( $notice = [] ) {
		GravityView_Admin_Notices::add_notice( $notice );
	}

	/**
	 * Check if Gravity Forms plugin is active and show notice if not.
	 *
	 * @deprecated 3.0.0 1.12
	 * @see GravityView_Compatibility::get_plugin_status()
	 * @return boolean True: checks have been passed; GV is fine to run; False: checks have failed, don't continue loading
	 */
	public static function check_gravityforms() {
		return GravityView_Compatibility::check_gravityforms();
	}

	/**
	 * Check if specified plugin is active, inactive or not installed
	 *
	 * @deprecated 3.0.0 1.12
	 * @see GravityView_Compatibility::get_plugin_status()
	 *
	 * @return boolean|string True: plugin is active; False: plugin file doesn't exist at path; 'inactive' it's inactive
	 */
	static function get_plugin_status( $location = '' ) {
		return GravityView_Compatibility::get_plugin_status( $location );
	}

	/**
	 * Is the current admin page a GravityView-related page?
	 *
	 * @deprecated 3.0.0 `gravityview()->request->is_admin` or `\GV\Request::is_admin`
	 * @param string      $hook
	 * @param null|string $page Optional. String return value of page to compare against.
	 *
	 * @return bool|string If `false`, not a GravityView page. `true` if $page is passed and is the same as current page. Otherwise, the name of the page (`single`, `settings`, or `views`)
	 */
	static function is_admin_page( $hook = '', $page = null ) {
		gravityview()->log->warning( 'The \GravityView_Admin::is_admin_page() method is deprecated. Use gravityview()->request->is_admin' );
		return gravityview()->request->is_admin( $hook, $page );
	}
}

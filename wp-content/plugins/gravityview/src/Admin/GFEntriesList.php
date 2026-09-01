<?php
/**
 * @file      GFEntriesList.php
 * @package   GravityKit\GravityView\Admin
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 3.0.0 Migrated to GravityKit\GravityView\Admin namespace.
 */

namespace GravityKit\GravityView\Admin;

use GravityKit\GravityView\GravityForms\Compat as GravityView_GF_Compat;

/**
 * Actions to be performed on the Gravity Forms Entries list screen.
 *
 * @since 3.0.0 Migrated to GravityKit\GravityView\Admin namespace.
 */
class GFEntriesList {

	/**
	 * Whether hooks have been added.
	 *
	 * Prevents double-registration when both PSR-4 autoload and legacy
	 * include paths trigger instantiation.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	private static $hooks_added = false;

	function __construct() {
		if ( GravityView_GF_Compat::has_native_entry_edit_link() ) {
			return;
		}

		if ( self::$hooks_added ) {
			return;
		}

		self::$hooks_added = true;

		// Add Edit link to the entry actions.
		add_action( 'gform_entries_first_column_actions', [ $this, 'add_edit_link' ], 10, 5 );

		// Add script to enable edit link.
		add_action( 'admin_head', [ $this, 'add_edit_script' ] );
	}

	/**
	 * When clicking the edit link, convert the Entries form to go to the edit screen.
	 *
	 * Gravity Forms requires $_POST['screen_mode'] to be set to get to the "Edit" mode. This enables direct access to the edit mode.
	 *
	 * @hack
	 * @return void
	 */
	public function add_edit_script() {

		if ( ! class_exists( 'GFForms' ) ) {
			return;
		}

		if ( ! in_array( \GFForms::get_page(), [ 'entry_list', 'entry_detail' ] ) ) {
			return;
		}
		?>
		<script>
		jQuery( document ).ready( function( $ ) {
			$('.edit_entry a').click(function(e) {
				e.preventDefault();
				$( e.target ).parents('form')
					.prepend('<input name="screen_mode" type="hidden" value="edit" />')
					.attr('action', $(e.target).attr('href') )
					.submit();
			});
		});
		</script>
		<?php
	}

	/**
	 * Add an Edit link to the GF Entry actions row
	 *
	 * @param int    $form_id      ID of the current form
	 * @param int    $field_id     The ID of the field in the first column, where the row actions are shown
	 * @param string $value        The value of the `$field_id` field
	 * @param array  $lead         The current entry data
	 * @param string $query_string URL query string for a link to the current entry. Missing the `?page=` part, which is strange. Example: `gf_entries&view=entry&id=35&lid=5212&filter=&paged=1`
	 */
	function add_edit_link( $form_id = null, $field_id = null, $value = null, $lead = [], $query_string = null ) {

		$params = [
			'page'        => 'gf_entries',
			'view'        => 'entry',
			'id'          => (int) $form_id,
			'lid'         => (int) $lead['id'],
			'screen_mode' => 'edit',
		];
		?>

		<span class="edit edit_entry">
			|
			<a title="<?php esc_attr_e( 'Edit this entry', 'gk-gravityview' ); ?>" href="<?php echo esc_url( add_query_arg( $params, admin_url( 'admin.php?page=' . $query_string ) ) ); ?>"><?php esc_html_e( 'Edit', 'gk-gravityview' ); ?></a>
		</span>
		<?php
	}
}

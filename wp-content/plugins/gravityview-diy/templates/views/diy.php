<?php
/**
 * @package GravityView-DIY
 * @subpackage GravityView-DIY/templates
 *
 * @global \GV\Template_Context $gravityview
 */
$template = $gravityview->template;

gravityview_before( $gravityview );

$container = apply_filters( 'gravityview-diy/container', 'div', $gravityview );

?>

<?php if ( $container ) { ?>
<<?php echo $container; ?> class="<?php gv_container_class( 'gv-diy-container gv-diy-multiple-container', true, $gravityview ); ?>">
<?php } ?>

	<?php gravityview_header( $gravityview );

	/**
	 * @filter `gravityview-diy/wrap/multiple` Should each entry in Multiple Entries context be wrapped in minimal HTML containers?
	 * @param bool $wrap Default: true
	 */
	$wrap = apply_filters( 'gravityview-diy/wrap/multiple', true, $gravityview );

	/**
	 * @action `gravityview_diy_body_before` (deprecated)
	 * @action `gravityview/template/diy/body/before`
	 */
	$template::body_before( $gravityview );

	// There are no entries.
	if ( ! $gravityview->entries->count() ) {

		if ( ! $wrap ) {
			echo gv_no_results( true, $gravityview );
		} else {
			?>
			<div class="gv-diy-view gv-no-results">
				<div class="gv-diy-view-title">
					<h3><?php echo gv_no_results( true, $gravityview ); ?></h3>
				</div>
			</div>
			<?php
		}

	} elseif ( $gravityview->fields->by_position( 'directory_diy-diy' )->by_visible()->count() ) {

		// There are entries. Loop through them.
		foreach ( $gravityview->entries->all() as $entry ) {

			if ( $wrap ) {
				$entry_slug = GravityView_API::get_entry_slug( $entry->ID, $entry->as_entry() );

				/**
				 * @filter `gravityview/template/list/entry/class`
				 * @filter `gravityview_entry_class`
				 */
				$entry_class = $template::entry_class( 'gv-diy-view', $entry, $gravityview );
			?>

			<div id="gv_diy_<?php echo esc_attr( $entry_slug ); ?>" class="<?php echo esc_attr( $entry_class ); ?>">

				<?php
			}

				/**
				 * @action `gravityview_entry_before`
				 * @action `gravityview/template/diy/entry/before`
				 */
				$template::entry_before( $entry, $gravityview );

				/**
				 * Output the field.
				 */
				foreach ( $gravityview->fields->by_position( 'directory_diy-diy' )->by_visible()->all() as $field ) {
					echo $template->the_field( $field, $entry );
				}

				/**
				 * @action `gravityview_entry_after`
				 * @action `gravityview/template/diy/entry/after`
				 */
				$template::entry_after( $entry, $gravityview );

			if ( $wrap ) {
				?>

			</div>

		<?php }
		}

	} // End if has entries

	/**
	 * @action `gravityview_diy_body_after` (deprecated)
	 * @action `gravityview/template/diy/body/after`
	 */
	$template::body_after( $gravityview );

	gravityview_footer( $gravityview ); ?>

<?php if ( $container ) { ?>
</<?php echo $container; ?>>
<?php } ?>

<?php gravityview_after( $gravityview ); ?>

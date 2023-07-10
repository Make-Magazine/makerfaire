<?php
/**
 * @package GravityView-DIY
 * @subpackage GravityView-DIY/templates
 *
 * @global \GV\Template_Context $gravityview
 */

$template = $gravityview->template;
$entry = $gravityview->entry;

gravityview_before( $gravityview );

/**
 * @filter `gravityview-diy/wrap/single` Should the entry in Single Entry context be wrapped in minimal HTML containers?
 * @param bool $wrap Default: true
 */
$wrap = apply_filters( 'gravityview-diy/wrap/single', true, $gravityview );

/**
 * @action `gravityview_diy_single_before`
 * @action `gravityview/template/diy/single/before`
 */
$template::single_before( $gravityview );

if ( $wrap ) {
	$entry_slug = GravityView_API::get_entry_slug( $entry->ID, $entry->as_entry() );

	/**
	 * @filter `gravityview/template/list/entry/class`
	 * @filter `gravityview_entry_class`
	 */
	$entry_class = $template::entry_class( 'gv-diy-view gv-diy-container gv-diy-single-container', $entry, $gravityview );
?>

<div id="gv_diy_<?php echo esc_attr( $entry_slug ); ?>" class="<?php gv_container_class( $entry_class, true, $gravityview ); ?>">

	<?php
}

	gravityview_header( $gravityview );

	/**
	 * @action `gravityview_entry_before`
	 * @action `gravityview/template/diy/entry/before`
	 */
	$template::entry_before( $entry, $gravityview );

	/**
	 * Output the field.
	 */
	foreach ( $gravityview->fields->by_position( 'single_diy-diy' )->by_visible()->all() as $field ) {
		echo $template->the_field( $field );
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

/**
 * @action `gravityview_diy_single_after`
 * @action `gravityview/template/diy/single/after`
 */
$template::single_after( $gravityview );

gravityview_after( $gravityview );

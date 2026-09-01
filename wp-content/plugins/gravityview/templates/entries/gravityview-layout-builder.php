<?php
/**
 * @global Template_Context $gravityview
 */

use GV\Grid;
use GV\Template_Context;

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template was loaded without context', [ 'file' => __FILE__ ] );

	return;
}

gravityview_before( $gravityview );

ob_start();

gravityview_header( $gravityview );

$zone = 'single';
$rows = Grid::prefixed(
	GravityView_Layout_Builder::ID,
	static fn() => Grid::get_rows_from_collection( $gravityview->fields, $zone )
);

// Load row settings for custom CSS class and HTML ID.
$row_settings = get_post_meta( $gravityview->view->ID, '_gravityview_row_settings', true );

if ( ! is_array( $row_settings ) ) {
	$row_settings = [];
}

// There are entries. Loop through them.
$entry     = $gravityview->entry;
$back_link = gravityview_back_link( $gravityview );

if ( $back_link ) {
	printf( '<nav class="gv-back-link" aria-label="%s">%s</nav>', esc_attr__( 'Back to entries', 'gk-gravityview' ), $back_link );
}

?>
	<div class="gv-layout-builder-view gv-layout-builder-view--entry gv-grid">
		<?php foreach ( $rows as $row_index => $row ) {
			$row_uid     = Grid::extract_row_uid( $row );
			$row_key     = $zone . '::' . $row_uid;
			$row_setting = $row_settings[ $row_key ] ?? [];
			$row_class   = 'gv-grid-row';
			$row_id      = '';

			if ( ! empty( $row_setting['custom_class'] ) ) {
				$custom_class = $row_setting['custom_class'];
				$custom_class = str_replace( '{row_index}', $row_index + 1, $custom_class );

				// Support merge tags in custom class.
				if ( ! empty( $entry ) ) {
					$form = $gravityview->view->form ? $gravityview->view->form->form : null;

					add_filter( 'gform_merge_tag_filter', 'sanitize_html_class' );
					$custom_class = GravityView_API::replace_variables( $custom_class, $form, $entry );
					remove_filter( 'gform_merge_tag_filter', 'sanitize_html_class' );
				}

				$row_class .= ' ' . gravityview_sanitize_html_class( $custom_class );
			}

			if ( ! empty( $row_setting['custom_id'] ) ) {
				$custom_id = $row_setting['custom_id'];
				$custom_id = str_replace( '{row_index}', $row_index + 1, $custom_id );

				// Support merge tags in custom ID.
				if ( ! empty( $entry ) ) {
					$form = $gravityview->view->form ? $gravityview->view->form->form : null;

					add_filter( 'gform_merge_tag_filter', 'sanitize_html_class' );
					$custom_id = GravityView_API::replace_variables( $custom_id, $form, $entry );
					remove_filter( 'gform_merge_tag_filter', 'sanitize_html_class' );
				}

				$row_id = sanitize_html_class( $custom_id );
			}

			/** This filter is documented in templates/views/gravityview-layout-builder.php. */
			$row_attributes = apply_filters( 'gravityview/template/layout-builder/row/attributes', [
				'class' => $row_class,
				'id'    => $row_id,
			], $row, $row_setting, $entry );

			$row_attr_string = 'class="' . esc_attr( $row_attributes['class'] ?? 'gv-grid-row' ) . '"';

			if ( ! empty( $row_attributes['id'] ) ) {
				$row_attr_string .= ' id="' . esc_attr( $row_attributes['id'] ) . '"';
			}
		?>
			<div <?php echo $row_attr_string; ?>>
				<?php
				foreach ( $row as $col => $areas ) {
					$column = $col;
					?>
					<div class="gv-grid-col-<?php echo esc_attr( $column ); ?>">
						<?php
						if ( ! empty( $areas ) ) {
							foreach ( $areas as $area ) {
								foreach ( $gravityview->fields->by_position( $zone . '_' . $area['areaid'] )->by_visible( $gravityview->view )->all() as $field ) {
									echo $gravityview->template->the_field( $field, $entry );
								}
							}
						}
						?>
					</div>
				<?php } // $row ?>
			</div>
		<?php } // $rows ?>
	</div>
<?php

gravityview_footer( $gravityview );

$content = ob_get_clean();

/**
 * Modify the wrapper container.
 *
 * @since  2.15
 *
 * @param string   $wrapper_container Wrapper container HTML markup
 * @param string   $anchor_id         (optional) Unique anchor ID to identify the view.
 * @param \GV\View $view              The View.
 */
$class = gv_container_class( 'gv-layout-builder-container gv-layout-builder-single-container', false, $gravityview );

$wrapper_container = apply_filters(
	'gravityview/view/wrapper_container',
	'<div id="' . esc_attr( $gravityview->view->get_anchor_id() ) . '" class="' . esc_attr( $class ) . '">{content}</div>',
	$gravityview->view->get_anchor_id(),
	$gravityview->view
);

echo $wrapper_container ? str_replace( '{content}', $content, $wrapper_container ) : $content;

gravityview_after( $gravityview );

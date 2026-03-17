<?php
/**
 * @global Template_Context $gravityview
 */

use GV\Grid;
use GV\Mocks\Legacy_Context;
use GV\Template_Context;

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template was loaded without context', [ 'file' => __FILE__ ] );

	return;
}

ob_start();
gravityview_before( $gravityview );
?>
<div class="<?php echo esc_attr( gv_container_class( 'gv-layout-builder-container gv-layout-builder-multiple-container', false, $gravityview ) ); ?>">
<?php
gravityview_header( $gravityview );

// There are no entries.
if ( ! $gravityview->entries->count() ) {
	?>
    <div class="gv-layout-builder-view gv-no-results">
        <div class="gv-layout-builder-view-title">
            <h3><?php echo gv_no_results( true, $gravityview ); ?></h3>
        </div>
    </div>
	<?php
} else {
	$zone = 'directory';
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
	foreach ( $gravityview->entries->all() as $entry ) {
		$class = $gravityview->template::entry_class(
			'gv-layout-builder-view gv-layout-builder-view--entry gv-grid',
			$entry,
			$gravityview
		);
		?>
        <div class="<?php echo esc_attr( $class ); ?>">
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

				/**
				 * Modifies the attributes for a Layout Builder row.
				 *
				 * @since 2.54.0
				 *
				 * @param array $attributes {
				 *     Row HTML attributes.
				 *
				 *     @type string $class The CSS classes for the row.
				 *     @type string $id    The HTML ID for the row.
				 * }
				 * @param array $row         The row configuration.
				 * @param array $row_setting The row settings (custom_class, custom_id).
				 * @param \GV\Entry $entry   The current entry.
				 */
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
										// Add current entry to the context (accessible via GravityView_frontend::getInstance()->getEntry() or GravityView_View::getInstance()->getCurrentEntry()_
										Legacy_Context::load( [ 'entry' => $entry ] );

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
	}
}
?>
</div>
<?php
gravityview_footer( $gravityview );

gravityview_after( $gravityview );

$content = ob_get_clean();

$anchor_id = $gravityview->view->get_anchor_id();

/**
 * Modify the wrapper container.
 *
 * @since  2.15
 *
 * @param string $wrapper_container Wrapper container HTML markup
 * @param string $anchor_id         (optional) Unique anchor ID to identify the view.
 * @param \GV\View $view            The View.
 */
$wrapper_container = apply_filters(
	'gravityview/view/wrapper_container',
	'<div id="' . esc_attr( $anchor_id ) . '" class="gv-template-layout-builder">{content}</div>',
	$anchor_id,
	$gravityview->view
);

echo $wrapper_container ? str_replace( '{content}', $content, $wrapper_container ) : $content;

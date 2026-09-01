<?php
/**
 * Display the search by numeric range.
 *
 * @file class-search-widget.php See for usage
 * @global array $data
 */

$search_field = \GV\Utils::get( $data, 'search_field', [] );
$custom_class = \GV\Utils::get( $search_field, 'custom_class', '' );
$form_id      = \GV\Utils::get( $data, 'form_id', 0 );
$view_id      = \GV\Utils::get( $data, 'view_id', 0 );
$value        = \GV\Utils::get( $search_field, 'value', 0 );
$label        = \GV\Utils::get( $search_field, 'label', 0 );
$name         = \GV\Utils::get( $search_field, 'name', 0 );
$field_type   = \GV\Utils::get( $search_field, 'gf_field_type', '' );

$min = $value['min'] ?? null; // Can't trust `rgar` here.
$max = $value['max'] ?? null;

$error = null;
if ( is_numeric( $min ) && is_numeric( $max ) && $min > $max ) {
	$error = esc_html__( 'The "from" value is lower than the "to" value.', 'gk-gravityview' );
}

// Resolve the underlying Gravity Forms field once — used for both
// currency detection and to inherit the configured `rangeMin` /
// `rangeMax` bounds onto the search inputs.
$field        = GVCommon::get_field( $form_id, $search_field['key'] );
$is_currency  = 'total' === $field_type
	|| ( $field && 'currency' === ( $field['numberFormat'] ?? null ) );

// Inherit min/max bounds from the form field's configured range so
// browsers can validate the search input against the same limits the
// form uses. `rangeMin` / `rangeMax` may be empty strings — only
// emit `min=`/`max=` attributes when an actual numeric bound exists.
$range_min = $field && is_numeric( $field['rangeMin'] ?? null ) ? $field['rangeMin'] : null;
$range_max = $field && is_numeric( $field['rangeMax'] ?? null ) ? $field['rangeMax'] : null;

/**
 * Modifies the step value for the input fields.
 *
 * @since 2.22
 *
 * @param string           $value            The step size.
 * @param GravityView_View $gravityview_view The View object.
 */
$step = apply_filters(
	'gk/gravityview/search/number-range/step',
	'quantity' === $field_type ? '1' : 'any',
	GravityView_View::getInstance(),
);

$min_attr = null !== $range_min ? sprintf( ' min="%s"', esc_attr( $range_min ) ) : '';
$max_attr = null !== $range_max ? sprintf( ' max="%s"', esc_attr( $range_max ) ) : '';
?>

<div class="gv-search-box gv-search-number gv-search-number-range <?php echo $custom_class; ?>">
	<?php if ( ! gv_empty( $label, false, false ) ) { ?>
        <label for="search-box-<?php echo esc_attr( $name ) . '-start'; ?>">
			<?php echo esc_html( $label ) . ( $is_currency ? ' (' . GFCommon::get_currency() . ')' : '' ); ?>
        </label>
	<?php } ?>
    <p>
        <input name="<?php echo esc_attr( $name ) . '[min]'; ?>"
               id="search-box-<?php echo esc_attr( $name ) . '-min'; ?>"
               type="number"
               placeholder="<?php esc_attr_e( 'From', 'gk-gravityview' ); ?>"
               step="<?php echo esc_attr( $step ); ?>"<?php echo $min_attr . $max_attr; ?>
               value="<?php echo esc_attr( $min ); ?>">

        <input name="<?php echo esc_attr( $name ) . '[max]'; ?>"
               id="search-box-<?php echo esc_attr( $name ) . '-max'; ?>"
               type="number"
               placeholder="<?php esc_attr_e( 'To', 'gk-gravityview' ); ?>"
               step="<?php echo esc_attr( $step ); ?>"<?php echo $min_attr . $max_attr; ?>
               value="<?php echo esc_attr( $max ); ?>">
    </p>
	<?php if ( $error ) {
		printf( '<p class="error">%s</p>', $error );
	} ?>
</div>

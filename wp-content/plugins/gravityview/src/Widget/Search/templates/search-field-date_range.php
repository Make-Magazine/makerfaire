<?php
/**
 * Display the search by date range input.
 *
 * @file class-search-widget.php See for usage
 *
 * @global array $data
 */

use GravityKit\GravityView\Search\SearchPolicy;
use GravityKit\GravityView\Utils\Utils;

$search_field = Utils::get( $data, 'search_field', null );
$value        = Utils::get( $search_field, 'value', [] );
$label        = Utils::get( $search_field, 'label' );
$name         = Utils::get( $search_field, 'name' );
$custom_class = Utils::get( $search_field, 'custom_class', '' );
$range_mode   = Utils::get( $search_field, 'date_range_mode', 'separate' );

if ( ! is_array( $value ) ) {
	$value = [];
}

$value_ymd = [
	'start' => SearchPolicy::resolve_date( $value['start'] ?? '' ),
	'end'   => SearchPolicy::resolve_date( $value['end'] ?? '' ),
];

$picker_id   = 'gv-date-range-' . uniqid();
$label_id    = $picker_id . '-label';
$is_combined = 'combined' === $range_mode;

$has_label        = ! gv_empty( $label, false, false );
$aria_labelled_by = $has_label ? 'aria-labelledby="' . esc_attr( $label_id ) . '"' : '';

$classes = [ 'gv-search-box', 'gv-search-date', 'gv-search-field-date_range' ];
if ( $is_combined ) {
	$classes[] = 'gv-search-date-range--combined';
}
$classes[] = esc_attr( $custom_class );
?>

<div class="<?php echo implode( ' ', $classes ); ?>">
	<?php if ( $has_label ) { ?>
		<label id="<?php echo esc_attr( $label_id ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php
	}
	if ( $is_combined ) :
		?>
		<?php
		$config = [
			'inputElementName' => $name,
			'value'            => $value_ymd,
			'label'            => null,
			'showTodayButton'  => true,
		];
		$config = Utils::add_date_bounds( $config, $search_field );
		?>
		<div
				id="<?php echo esc_attr( $picker_id ); ?>"
				class="gv-date-range-picker"
			<?php echo $aria_labelled_by; ?>
				data-gv-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		></div>
	<?php else : ?>
		<?php
		$start_config = [
			'inputElementName' => $name . '[start]',
			'value'            => [ 'date' => $value_ymd['start'] ],
			'label'            => esc_attr__( 'Start date', 'gk-gravityview' ),
			'showTodayButton'  => true,
		];
		$start_config = Utils::add_date_bounds( $start_config, $search_field );
		$end_config   = [
			'inputElementName' => $name . '[end]',
			'value'            => [ 'date' => $value_ymd['end'] ],
			'label'            => esc_attr__( 'End date', 'gk-gravityview' ),
			'showTodayButton'  => true,
		];
		$end_config   = Utils::add_date_bounds( $end_config, $search_field );
		?>
		<div class="gv-date-range-picker" id="<?php echo esc_attr( $picker_id ); ?>">
			<div
					id="<?php echo esc_attr( $picker_id . '-start' ); ?>"
					class="gv-date-picker gv-date-picker--start"
				<?php echo $aria_labelled_by; ?>
					data-gv-config="<?php echo esc_attr( wp_json_encode( $start_config ) ); ?>"
			></div>
			<div
					id="<?php echo esc_attr( $picker_id . '-end' ); ?>"
					class="gv-date-picker gv-date-picker--end"
				<?php echo $aria_labelled_by; ?>
					data-gv-config="<?php echo esc_attr( wp_json_encode( $end_config ) ); ?>"
			></div>
		</div>
	<?php endif; ?>
</div>

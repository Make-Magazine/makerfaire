<?php
/**
 * Display the search by entry date input boxes.
 *
 * @file class-search-widget.php See for usage
 *
 * @global array $data
 */

use GravityKit\GravityView\Search\SearchPolicy;
use GravityKit\GravityView\Utils\Utils;

$view_id      = Utils::get( $data, 'view_id', null );
$search_field = Utils::get( $data, 'search_field', null );
$value        = Utils::get( $search_field, 'value' );
$label        = Utils::get( $search_field, 'label' );
$custom_class = Utils::get( $search_field, 'custom_class', '' );
$input_type   = Utils::get( $search_field, 'input_type', 'date_range' );
$range_mode   = Utils::get( $search_field, 'date_range_mode', 'separate' );

if ( ! is_array( $value ) ) {
	$value = [];
}

// The QF picker expects Y-m-d values; request values arrive in the configured display format.
$value_ymd = [
	'start' => SearchPolicy::resolve_date( $value['start'] ?? '' ),
	'end'   => SearchPolicy::resolve_date( $value['end'] ?? '' ),
];

$is_date_range = 'date_range' === $input_type;
$is_combined   = $is_date_range && 'combined' === $range_mode;
$picker_id     = $is_date_range
	? 'gv-entry-date-range-' . uniqid()
	: 'gv-entry-date-' . uniqid();
$label_id      = $picker_id . '-label';

$classes = [
	'gv-search-box',
	'gv-search-date',
	'gv-search-field-entry_date',
	esc_attr( $custom_class ),
];

if ( $is_date_range ) {
	// Insert the date range class after `gv-search-date` to keep the same order as previous versions.
	$date_range_classes = [ 'gv-search-date-range' ];
	if ( $is_combined ) {
		$date_range_classes[] = 'gv-search-date-range--combined';
	}
	array_splice( $classes, 2, 0, $date_range_classes );
}

$has_label        = ! gv_empty( $label, false, false );
$aria_labelled_by = $has_label ? 'aria-labelledby="' . esc_attr( $label_id ) . '"' : '';
?>

<div class="<?php echo implode( ' ', array_filter( $classes ) ); ?>">
	<?php if ( $has_label ) { ?>
		<label id="<?php echo esc_attr( $label_id ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php
	}
	if ( $is_combined ) :
		?>
		<?php
		$config = [
			'inputElementName' => [
				'start' => 'gv_start',
				'end'   => 'gv_end',
			],
			'value'            => [
				'start' => $value_ymd['start'],
				'end'   => $value_ymd['end'],
			],
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
	<?php elseif ( $is_date_range ) : ?>
		<?php
		$start_config = [
			'inputElementName' => 'gv_start',
			'value'            => [ 'date' => $value_ymd['start'] ],
			'label'            => esc_attr__( 'Start date', 'gk-gravityview' ),
			'showTodayButton'  => true,
		];
		$start_config = Utils::add_date_bounds( $start_config, $search_field );
		$end_config   = [
			'inputElementName' => 'gv_end',
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
	<?php else : ?>
		<?php
		$config = [
			'inputElementName' => 'gv_start',
			'value'            => [
				'date' => $value_ymd['start'],
			],
			'label'            => null,
			'showTodayButton'  => true,
		];
		$config = Utils::add_date_bounds( $config, $search_field );
		?>
		<div
				id="<?php echo esc_attr( $picker_id ); ?>"
				class="gv-date-picker"
			<?php echo $aria_labelled_by; ?>
				data-gv-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		></div>
	<?php endif; ?>
</div>

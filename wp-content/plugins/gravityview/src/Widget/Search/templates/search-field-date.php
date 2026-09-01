<?php
/**
 * Display the search by single date input.
 *
 * @file class-search-widget.php See for usage
 *
 * @global array $data
 */

use GravityKit\GravityView\Search\SearchPolicy;
use GravityKit\GravityView\Utils\Utils;

$search_field = Utils::get( $data, 'search_field', [] );
$name         = Utils::get( $search_field, 'name', '' );
$label        = Utils::get( $search_field, 'label', '' );
$value        = Utils::get( $search_field, 'value', '' );
$custom_class = Utils::get( $search_field, 'custom_class', '' );

$picker_id = 'gv-date-' . uniqid();
$label_id  = $picker_id . '-label';

$config = [
	'inputElementName' => $name,
	'value'            => [
		'date' => is_string( $value ) ? SearchPolicy::resolve_date( $value ) : '',
	],
	'label'            => null,
	'showTodayButton'  => true,
];

$config = Utils::add_date_bounds( $config, $search_field );
?>

<div class="gv-search-box gv-search-date <?php echo esc_attr( $custom_class ); ?>">
	<?php if ( ! gv_empty( $label, false, false ) ) { ?>
		<label id="<?php echo esc_attr( $label_id ); ?>"><?php echo esc_html( $label ); ?></label>
	<?php } ?>
	<div
		id="<?php echo esc_attr( $picker_id ); ?>"
		class="gv-date-picker"
		<?php if ( ! gv_empty( $label, false, false ) ) { ?>aria-labelledby="<?php echo esc_attr( $label_id ); ?>"<?php } ?>
		data-gv-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
	></div>
</div>

<?php

function mpfy_sr_template_after_search_field( $map_id ) {
	$map     = new Mpfy_Map( $map_id );
	$enabled = mpfy_meta_to_bool( $map->get_id(), '_map_enable_search_radius', false );
	
	if ( ! $enabled ) {
		return;
	}

	$search_within_label = mpfy_meta_label( $map_id, '_map_label_search_within', __( 'Within', 'mpfy' ) );
	$any_within_label    = mpfy_meta_label( $map_id, '_map_label_search_any_within', __( 'Any Within', 'mpfy' ) );
	$all_within_label    = mpfy_meta_label( $map_id, '_map_label_search_all_within', __( 'All Within', 'mpfy' ) );
	$unit                = $map->get_search_radius_unit();
	$distances           = $map->get_search_radius_options();

	?>

	<div class="mpfy-search-radius">
		<div class="mpfy-search-radius-label"><?php esc_html_e( $search_within_label ); ?></div>
		<div class="mpfy-selecter-wrap mpfy-search-radius-type-wrap">
			<select name="mpfy_search_radius_type">
				<option value="any_within"><?php esc_html_e( $any_within_label ) ?></option>
				<option value="all_within"><?php esc_html_e( $all_within_label ) ?></option>
			</select>
		</div>
		<div class="mpfy-selecter-wrap">
			<select name="mpfy_search_radius">
				<?php foreach ( $distances as $d ) : ?>
					<option value="<?php esc_attr_e( $d ) ?>"><?php esc_html_e( $d . ' ' . $unit ) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="mpfy-after-search-radius-label"><?php _e( 'of', 'mpfy' ); ?></div>
	</div>
	<?php
}
add_action( 'mpfy_template_before_search_field', 'mpfy_sr_template_after_search_field' );
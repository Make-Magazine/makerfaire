<?php
/**
 * The template part for GravityView Maps display icon image tag
 *
 * @package GravityView_Maps
 * @since 0.1.1
 * @since 1.3 Added $selected icon
 *
 * @see GravityView_Maps_Available_Icons::render_available_icons
 * @global array $icons `default`, `theme` and `plugin` keys represent the default icon, icons in theme's /gravityview/mapicons/ directory, and the plugin's default icon set
 * @global array $sections `default` and `plugin`. `theme` also, if $icons['theme'] is defined.
 * @global string $selected Selected icon URL
 */

foreach( $sections as $k => $section ) {
	foreach ( $icons[ $k ] as $icon ) {

		$class = 'gv_maps_icons';

		// If the icon is the default, add .selected CSS class
		if( $icon === $selected ) {
			$class .= ' selected';
		}

		// Keep the <?php right next to HTML tag to prevent whitespace
		?><img class="<?php echo esc_attr( $class ); ?>" src="<?php echo esc_url( $icon ); ?>" title="<?php echo esc_attr( basename( $icon ) ); ?>"><?php
	}
}
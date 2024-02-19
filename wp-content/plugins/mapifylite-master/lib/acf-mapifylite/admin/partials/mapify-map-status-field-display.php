<div <?php acf_esc_attr_e( $attrs ); ?>>

	<div class="acf-osm-mapify-status">
		<div class="acf-osm-map-mode-selector">
			<label><?php esc_html_e( 'Mode', 'acf-mapifylite' ) ?></label>
			<select name="<?php echo esc_attr( $field['name'] ) . '[map_mode]' ?>" id="acf-osm-map-mode">
				<option value="<?php echo esc_attr( 'map' ) ?>" <?php echo $map_mode === 'map' ? 'selected' : '' ?> ><?php esc_html_e( 'Map', 'acf-mapifylite' ) ?></option>
				<option value="<?php echo esc_attr( 'image' ) ?>" <?php echo $map_mode === 'image' ? 'selected' : '' ?> ><?php esc_html_e( 'Image', 'acf-mapifylite' ) ?></option>
			</select>
		</div>

		<div class="acf-osm-map-id-information">
			<label><?php esc_html_e( 'Map ID', 'acf-mapifylite' ) ?></label>
			<div><code><?php echo esc_html( $post_id ) ?></code></div>
		</div>

		<div class="acf-osm-map-shortcode-information">
			<label><?php esc_html_e( 'Shortcode (use to place your map)', 'acf-mapifylite' ) ?></label>
			<div><code><?php echo esc_html( "[custom-mapping map_id=\"$post_id\" height=\"400\"]" ) ?></code></div>
		</div>

	</div>

</div>
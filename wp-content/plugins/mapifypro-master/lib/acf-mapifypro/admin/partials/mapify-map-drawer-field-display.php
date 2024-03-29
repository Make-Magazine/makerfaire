<div <?php acf_esc_attr_e( $attrs ); ?>>

	<div class="acf-osm-search">
		<div class="acf-osm-search-input">
			<input type="text" id="acf-osm-search-keywords" placeholder="<?php esc_attr_e( 'Enter your search keywords here..', 'acf-mapifypro' ) ?>">
		</div>
		<div class="acf-osm-search-button">
			<button class="button" id="acf-osm-search-button"><?php esc_html_e( 'Search on Map', 'acf-mapifypro' ) ?></button>
		</div>
	</div>

	<div id='acf-osm-map-search-results'></div>

	<div id="acf-osm-map-canvas" <?php acf_esc_attr_e( $map_canvas_attrs ); ?>>
		<div class="custom-map-controls" data-image>
			<button class='button button-large button-primary' id='map-drawing-toggle' style=""><?php esc_html_e( 'Start Drawing', 'acf-mapifypro' ) ?></button>
			<button class='button button-large' id='map-reset-drawing' style=""><?php esc_html_e( 'Reset', 'acf-mapifypro' ) ?></button>
		</div>
	</div>

	<div id="acf-osm-map-info" class='d-none'>
		<div class="form-group">
			<label><?php esc_html_e( 'Lattitude', 'acf-mapifypro' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $field['name'] . '[selected_lat]' ) ?>" id="selected_lat">
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Longitude', 'acf-mapifypro' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $field['name'] ) . '[selected_lng]' ?>" id="selected_lng">
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Centered Lattitude', 'acf-mapifypro' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $field['name'] ) . '[centered_lat]' ?>" id="centered_lat">
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Centered Longitude', 'acf-mapifypro' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $field['name'] ) . '[centered_lng]' ?>" id="centered_lng">
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Zoom Level', 'acf-mapifypro' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $field['name'] ) . '[zoom_level]' ?>" id="zoom_level">
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Area Coordinates', 'acf-mapifypro' ) ?></label>
			<input type="text" name="<?php echo esc_attr( $field['name'] ) . '[area_coordinates]' ?>" id="area_coordinates">
		</div>
	</div>
	
</div>
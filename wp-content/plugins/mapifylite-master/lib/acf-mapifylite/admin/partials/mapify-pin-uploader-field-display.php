<div <?php acf_esc_attr_e( $attrs ); ?>>
	
	<div class="acf-osm-maptiles osm-media-uploader">
		<div class="acf-osm-maptiles-input">
			<!-- img_url -->
			<input type="text" name="<?php echo esc_attr( $field['name'] ) . '[image_url]' ?>" 
			placeholder="<?php esc_attr_e( 'Your image URL', 'acf-mapifylite' ) ?>" value='<?php echo esc_url( $image_url ) ?>' class='maptiles-image-url' id='mapify-pin-image'>

			<!-- img_id -->
			<input type="hidden" name="<?php echo esc_attr( $field['name'] ) . '[image_id]' ?>" class="maptiles-image-id d-none" value="<?php echo esc_attr( $image_id ) ?>">
		</div>
		<div class="acf-osm-maptiles-button">
			<button class="button maptiles-select-image"><?php esc_html_e( 'Select an Image', 'acf-mapifylite' ) ?></button>
		</div>

		<?php if ( $thumbnail_img ) : ?>

			<div class="maptiles-img-container">
				<img src="<?php echo esc_url( $thumbnail_img ) ?>"/>
				<a href="javascript:;" class="maptiles-delete-img"><span class="dashicons dashicons-no"></span></a>
			</div>

		<?php else: ?>
			
			<div class="maptiles-img-container">
				<a href="javascript:;" class="maptiles-delete-img"><span class="dashicons dashicons-no"></span></a>
			</div>

		<?php endif ?>
	</div>

</div>
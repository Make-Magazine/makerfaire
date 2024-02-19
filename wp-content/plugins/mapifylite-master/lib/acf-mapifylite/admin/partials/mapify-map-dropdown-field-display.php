<div <?php acf_esc_attr_e( $attrs ); ?>>

	<select id="acf-osm-map-dropdown" name="<?php echo esc_attr( $field['name'] ) ?>" >		
		<option value='0'><?php esc_html_e( 'None', 'acf-mapifylite' ) ?></value>
	
		<?php foreach ( $dropdown_items as $key => $value ) : ?>

		<option 
			value               = "<?php echo esc_attr( $key ) ?>" 
			data-mode           = "<?php echo esc_attr( $value['mode'] ) ?>" 
			data-has-image-mode = "<?php echo esc_attr( $value['has_image_mode'] ) ?>" 

			<?php
			if ( ! empty( $value ) && $key === $selected ) {
				echo "selected";
			}
			?>

		><?php echo esc_html( $value['title'] ) ?></option>

		<?php endforeach ?>
	</select>

</div>
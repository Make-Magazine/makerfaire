<div <?php acf_esc_attr_e( $attrs ); ?>>

	<div id="fegallery-button-container">
		<a class='button' id="fegallery-add-slide">
			<span class="wp-media-buttons-icon dashicons dashicons-images-alt2"></span>
			<?php esc_html_e( 'Add Images', 'acf-mapifylite' ) ?>
		</a>
	</div>

	<div id="fegallery-modal-container"></div>
	<div id="fegallery-edit-modal" style="display:none;" title="Edit Image URL">
		<table class="table-content">
			<tr>
				<td>
					<input type="text" id="input-image-url">
					<label><?php esc_html_e( 'You can insert your custom image URL here', 'acf-mapifylite' ) ?></label>
				</td>
				<tr>
					<th class="button-th">
						<a href="javascript:;" class="button" id="fegallery-update-image-url"><?php esc_html_e( 'Update Image URL', 'acf-mapifylite' ) ?></a>
					</th>
				</tr>
			</tr>
		</table>
	</div>

	<div id="fegallery-image-container">

		<?php if ( count( $gallery ) > 0 ): foreach ( $gallery as $image ): ?>

		<div class="fegallery-image-item <?php echo $image['is_custom_url'] ? esc_attr( 'use-custom-url' ) : ''; ?>">
			<img src="<?php echo esc_url( $image['image_thumb'] ) ?>" >
			<a href="javascript:;" class="fegallery-edit-item"><span class="dashicons dashicons-edit"></span></a>
			<a href="javascript:;" class="fegallery-close-item"><span class="dashicons dashicons-no"></span></a>
			<input type="hidden" name="<?php echo esc_attr( $field['name'] ) . '[image_id][]' ?>" class="image-id-field" value="<?php echo esc_attr( $image['image_id'] ) ?>" >
			<input type="hidden" name="<?php echo esc_attr( $field['name'] ) . '[image_url][]' ?>" class="image-url-field" value="<?php echo esc_attr( $image['image_url'] ) ?>" >
			<input type="hidden" name="<?php echo esc_attr( $field['name'] ) . '[image_thumb][]' ?>" value="<?php echo esc_attr( $image['image_thumb'] ) ?>" >
		</div>

		<?php
			endforeach;
			else:
				echo esc_html( $empty_notice );
			endif;
		?>

	</div>
	
</div>
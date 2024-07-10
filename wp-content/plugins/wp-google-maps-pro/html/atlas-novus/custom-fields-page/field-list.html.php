<div class="wpgmza-wrap wpgmza-pad-10 minimal">
	<h1><?php esc_html_e("Marker Fields", "wp-google-maps"); ?></h1>

	<form 
		method="POST" 
		id="wpgmza-custom-fields" 
		class="wpgmza-form wpgmza-wrap"
		action="<?php 
			echo admin_url('admin-post.php');
		?>"
		>

		<input
			type="hidden"
			name="action"
			value="wpgmza_save_custom_fields"
			/>

		<div class="custom-field-list-container wpgmza-flex-col">

		</div>

		<div class='custom-field-new-row-control'>
			<i class='fa fa-plus-circle' aria-hidden='true'></i>
			<span><?php esc_html_e("Add Field", "wp-google-maps"); ?></span>
		</div>

		<p>
			<button type="submit" class="wpgmza-button wpgmza-button-primary">
				<?php esc_html_e("Save Fields", "wp-google-maps"); ?>
			</button>
		</p>
	</form>
</div>
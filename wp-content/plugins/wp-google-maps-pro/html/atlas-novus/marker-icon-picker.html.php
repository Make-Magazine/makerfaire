<div class="wpgmza-marker-icon-picker wpgmza-flex">
	
	<div class="wpgmza-marker-icon-preview"></div>
	<input class="wpgmza-marker-icon-url" type="hidden"/>
	
	<label title="<?php esc_attr_e("This is a retina ready marker","wp-google-maps"); ?>" class='wpgmza-retina-ready'>
		<input 
			type="checkbox" 
			name="retina"
			data-ajax-name="retina"
			/>
		<?php
		esc_html_e('Retina Ready', 'wp-google-maps');
		?>
	</label>
	
	<button type="button" class="wpgmza-upload wpgmza-button">
		<?php _e('Upload', 'wp-google-maps'); ?>
	</button>
	<button type="button" class="wpgmza-marker-library wpgmza-button">
		<?php _e('Create', 'wp-google-maps'); ?>
	</button>
	<button type="button" class="wpgmza-reset wpgmza-button" title="<?php _e('Reset', 'wp-google-maps'); ?>">
		<i class="fa fa-undo" aria-hidden="true"></i>
	</button>
	
</div>

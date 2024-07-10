<div class="wpgmza-wrap wpgmza-pad-10 minimal">
	<div class="wpgmza-row wpgmza-page-actions">
		<div class="wpgmza-inline-field">
			<h1><?php esc_html_e("Edit Category", "wp-google-maps"); ?></h1>
		</div>
	</div>

	<div class="wpgmza-card wpgmza-shadow-high wpgmza-pad-20 wpgmza-form-wrap">
		<form action='admin.php?page=wp-google-maps-menu-categories' method='post' id='wpgmaps_edit_marker_category' name='wpgmaps_edit_marker_category_form'>
			<input name='real_post_nonce' value='<?php echo wp_create_nonce('wpgmza'); ?>' type='hidden'/>
			<input type='hidden' name='wpgmaps_marker_category_id' id='wpgmaps_marker_category_id' value='' />
			<div class="wpgmza-row">
				<div class="wpgmza-col-2">
					<?php _e("Name", "wp-google-maps"); ?>
				</div>

				<div class="wpgmza-col-10">
					<input type='text' name='wpgmaps_marker_category_name' id='wpgmaps_marker_category_name' value=''/>
				</div>
			</div>

			<div class="wpgmza-row">
				<div class="wpgmza-col-2">
					<?php _e("Icon", "wp-google-maps"); ?>
				</div>

				<div class="wpgmza-col-10" id='marker_category_icon'>
					<!-- Dynamically Generated -->
				</div>
			</div>

			<div class="wpgmza-row wpgmza-hidden">
				<div class="wpgmza-col-2">
					<?php _e("Image", "wp-google-maps"); ?>
				</div>

				<div class="wpgmza-col-10">
                    <input type="text" name="category_image" placeholder="<?php _e('Enter URL', 'wp-google-maps'); ?>" />
	                <button class="wpgmza_general_btn button button-secondary" type="button" data-media-dialog-target="input[name='category_image']">
	                    <?php _e('Upload Image', 'wp-google-maps'); ?>
	                </button>
				</div>
			</div>

			<div class="wpgmza-row">
				<div class="wpgmza-col-2">
					<?php _e("Parent", "wp-google-maps"); ?>
				</div>

				<div class="wpgmza-col-10">
					<select name='parent_category' id='parent_category'>
    					<option value='0'><?php _e( "None", "wp-google-maps" ); ?></option>
    					<!-- Dynamically filled -->
					</select>
				</div>
			</div>

			<div class="wpgmza-row">
				<div class="wpgmza-col-2">
					<?php _e("Priority", "wp-google-maps"); ?>
				</div>

				<div class="wpgmza-col-10">
					<input type='number' name='wpgmaps_marker_category_priority' id='wpgmaps_marker_category_priority' value='0'  step='1' />
				</div>
			</div>

			<div class="wpgmza-row">
				<div class="wpgmza-col-2">
					<?php _e("Assigned to ","wp-google-maps"); ?>
				</div>

				<div class="wpgmza-col-10">
					<ul id="assigned_to">
						<!-- Dynamically populated --> 
					</ul>
				</div>
			</div>


			<div class="wpgmza-row wpgmza-hidden">
				<div class="wpgmza-col-2"></div>
				<div class="wpgmza-col-10">
					<input type='submit' name='wpgmza_edit_marker_category' id="wpgmza_edit_marker_category" class='wpgmza-button wpgmza-button-primary' value='<?php _e("Save Category","wp-google-maps"); ?>'/>
				</div>
			</div>
		</form>
	</div>

	<p>
		<label class="wpgmza-button wpgmza-button-primary" for='wpgmza_edit_marker_category'>
			<?php esc_html_e("Save Category", "wp-google-maps"); ?>
		</label>
	</p>
</div>
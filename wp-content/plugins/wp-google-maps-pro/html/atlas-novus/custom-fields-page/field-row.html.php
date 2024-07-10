<div class="wpgmza-flex-col wpgmza-card wpgmza-shadow wpgmza-margin-b-20 wpgmza-custom-field-item-row">
	<input type="hidden" name="stack_order[]" data-name="stack_order">
	<input type="hidden" name="ids[]" data-name="id" readonly>

	<div class="wpgmza-flex-row">
		<div class="wpgmza-drag-handle" title="<?php esc_html_e("Drag to reorder", "wp-google-maps"); ?>"></div>

		<div class="wpgmza-flex-row field-name">
			<strong>Name</strong>
			<input type="text" name="names[]" data-name="name">
		</div>

		<div class="wpgmza-flex-row filter-options">
			<strong>Type</strong>
		</div>

		<div class="wpgmza-flex-row field-actions">
			<button type='button' class='wpgmza-button field-action-btn' data-action="edit" title="<?php esc_html_e("Field Meta", "wp-google-maps"); ?>">
				<i class='fa fa-sliders' aria-hidden='true'></i>
			</button>

			<button type='button' class='wpgmza-button field-action-btn' data-action="delete" title="<?php esc_html_e("Remove Field", "wp-google-maps"); ?>">
				<i class='fa fa-trash-o' aria-hidden='true'></i>
			</button>
		</div>
	</div>

	<div class="wpgmza-flex-col field-meta-container wpgmza-hidden">
		<h3><?php esc_html_e("Field Meta", "wp-google-maps"); ?></h3>
		<div class="wpgmza-row">
			<div class="wpgmza-col-3">
				Icon
			</div>
			<div class="wpgmza-col-9">
				<input type="text" class="icon-picker" name="icons[]" data-name="icon" placeholder="Start typing..." autocomplete="off">
			</div>
		</div>

		<div class="wpgmza-row">
			<div class="wpgmza-col-3">
				Visibility
			</div>
			<div class="wpgmza-col-9">
				<label>
					<input type="checkbox" name="display_in_infowindows" data-name="display_in_infowindows"> 
					Info Windows
				</label>
				
				<br>

				<label>
					<input type="checkbox" name="display_in_marker_listings" data-name="display_in_marker_listings"> 
					Marker Listings
				</label>
			</div>
		</div>

		<div class="wpgmza-row">
			<div class="wpgmza-col-3">
				HTML Attributes
			</div>
			<div class="wpgmza-col-9 html-attributes">
			
			</div>
		</div>
	</div>
</div>
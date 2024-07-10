<div class="wpgmza-wrap wpgmza-pad-10">
	<div class="wpgmza-row wpgmza-page-actions">
		<div class="wpgmza-inline-field">
			<h1><?php esc_html_e("Marker Categories", "wp-google-maps"); ?></h1>
		</div>

		<div class="wpgmza-action-buttons wpgmza-toolbar">
			<input type="checkbox" id="wpgmza-toolbar-conditional-map-list">
			<label class="wpgmza-button wpgmza-button-white" for="wpgmza-toolbar-conditional-map-list"><i class="fa fa-plus"></i></label>
			<div class="wpgmza-toolbar-list left-anchor">
				<a href="admin.php?page=wp-google-maps-menu-categories&action=new">
					<?php _e("New Category", "wp-google-maps"); ?>
				</a>
			</div>
		</div>
	</div>

	<div class="wpgmza-card wpgmza-shadow-high" id="category_list">

	</div>
</div>
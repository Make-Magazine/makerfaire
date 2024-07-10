<!-- This HTML fragment contains elements to add to the basic store locator -->
<div class="wpgmza-keywords">
	<label data-name="keywordsLabel" class="wpgmza-keywords"></label>
	<input type="text" class="wpgmza-keywords"/>
</div>

<svg width="42" height="40" viewBox="0 0 72 60" fill="none" xmlns="http://www.w3.org/2000/svg" class='wpgmza-category wpgmza-category-filter-toggle'>
	<path d="M31.7812 53L31.7812 56.0477L34.1963 54.1886L43.259 47.2118L43.8439 46.7615L43.8439 46.0232L43.8439 32.5852L66.1831 3.92209L68.0708 1.5L65 1.5L7 1.50001L3.62175 1.50001L5.88731 4.00596L31.7812 32.6473L31.7812 53Z" stroke-width="7"/>
</svg>

<div class="wpgmza-category-filter-container">
	<label class="wpgmza-category">
		<?php
		esc_html_e("Category", "wp-google-maps");
		?>:
	</label>
</div>

<div class="wpgmza-reset">
	<input 
		class="wpgmza-reset" 
		type="button" 
		value="<?php esc_attr_e("Reset", "wp-google-maps") ?>"/>
</div>
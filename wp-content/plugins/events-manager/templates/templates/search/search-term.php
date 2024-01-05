<?php
/* This general search will find matches within event_name, event_notes, and the location_name, address, town, state and country. */
/* @var array $args */
?>
<!-- START General Search -->
<div class="em-search-text em-search-field input">
	<label for="em-search-text-<?php echo absint($args['id']); ?>" class="screen-reader-text">
		<?php echo esc_html($args['search_term_label']); ?>
	</label>
	<input type="text" name="em_search" class="em-search-text" id="em-search-text-<?php echo absint($args['id']); ?>"  placeholder="<?php echo esc_js($args['search_term_label']); ?>" >
</div>
<!-- END General Search -->
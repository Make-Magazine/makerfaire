<?php /* @var array $args */ ?>
<div class="em-search-main em-search-main-bar">
	<?php do_action('em_template_events_search_form_header'); //hook in here to add extra fields, text etc. ?>
	<?php
	//search text - tweak ID so it is unique when repeated in advanced further down
	$id = esc_attr($args['id']);
	if ( !empty($args['search_term']) ) em_locate_template('templates/search/search.php',true,array('args'=>$args));
	if ( !empty($args['search_geo']) ) em_locate_template('templates/search/geo.php',true,array('args'=>$args));
	if ( !empty($args['search_scope']) ) em_locate_template('templates/search/scope.php',true,array('args'=>$args));
	if ( !empty($args['show_advanced']) ) { //show the advanced search toggle if advanced fields are collapsed
		em_locate_template('templates/search/form-advanced-trigger.php', true, array('args'=>$args));
	}
	if ( !empty( $args['sorting'] ) ) { //show the sorting option button
		em_locate_template('templates/search/form-sort.php', true, array('args'=>$args));
	}
	em_locate_template('templates/search/form-views.php', true, array('args' => $args));
	?>
	<div class="em-search-submit input">
		<button type="submit" class="em-search-submit button-primary"><?php echo esc_html($args['search_button']); ?></button>
	</div>
</div>
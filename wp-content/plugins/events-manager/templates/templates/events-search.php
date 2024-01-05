<?php
/* @var $args array */
$args['search_action'] = 'search_events';
$args['search_url'] = get_option('dbem_events_page') ? get_permalink(get_option('dbem_events_page')):EM_URI;
$args['css_classes'][] = 'em-events-search';
$args['css_classes_advanced'][] = 'em-events-search-advanced';
em_locate_template('templates/search.php', true, array('args'=>$args));
?>
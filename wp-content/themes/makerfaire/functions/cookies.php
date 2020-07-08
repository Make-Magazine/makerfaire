<?php

// Don't load the GDPR cookie bar on the selected pages
add_filter('cli_show_cookie_bar_only_on_selected_pages', 'webtoffee_custom_selected_pages', 10, 2);
function webtoffee_custom_selected_pages($html, $slug) {
	$slug = $_SERVER['REQUEST_URI'];
    $slug_array = array('/resource-mgmt/');
    if (in_array($slug, $slug_array)) {
        $html = '';
        return $html;
    }
    return $html;
}

?>
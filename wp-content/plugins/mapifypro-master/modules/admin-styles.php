<?php

/**
 * Whether the current admin page is belong to MapifyPro or not
 * 
 * @return Boolean
 */
function mpfy_is_admin_pages() {	
	$bases = array( 
		'edit', 'mapifypro_page_crowd-inbox', 'post', 'edit-tags', 'term', 'mapifypro_page_mpfy-import', 
		'mapifypro_page_mapifypro-multi-map', 'mapifypro_page_mapifypro-settings',
		'settings_page_wc_am_client_8672_dashboard',
		'settings_page_wc_am_client_8739_dashboard',
		'settings_page_wc_am_client_46018_dashboard',
		'settings_page_wc_am_client_46019_dashboard',
	);

	$ids = array( 
		'edit-map', 'edit-map-location', 'edit-map-drawer', 'mapifypro_page_crowd-inbox', 'edit-route', 
		'edit-route_map', 'map', 'map-location', 'map-drawer', 'route', 'route_map', 'edit-location-tag', 
		'mapifypro_page_mpfy-import', 'mapifypro_page_mapifypro-multi-map', 'mapifypro_page_mapifypro-settings',
		'settings_page_wc_am_client_8672_dashboard',
		'settings_page_wc_am_client_8739_dashboard',
		'settings_page_wc_am_client_46018_dashboard',
		'settings_page_wc_am_client_46019_dashboard',
	);

	$sc = get_current_screen();	

	// var_dump($sc);
	
	if ( ! in_array( $sc->base, $bases ) || ! in_array( $sc->id, $ids ) ) {
		return false;
	}

	return true;
}

/**
 * Load CSS files in MapifyPro admin pages
 */
function mpfy_admin_styles_enqueue_scripts() {
	if ( ! mpfy_is_admin_pages() ) {
		return false;
	}

	wp_enqueue_style( 'mapify-admin-css', plugins_url( 'assets/admin.css', MAPIFY_PLUGIN_FILE ), array(), MAPIFY_PLUGIN_VERSION );
}
add_action( 'admin_enqueue_scripts', 'mpfy_admin_styles_enqueue_scripts' );

/**
 * Add header in MapifyPro admin pages
 */
function mpfy_admin_styles_in_admin_header() {
	if ( ! mpfy_is_admin_pages() ) {
		return false;
	}
	
	?>
	
	<div class="mapifypro-admin-header">
		<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M408 120c0 54.6-73.1 151.9-105.2 192c-7.7 9.6-22 9.6-29.6 0C241.1 271.9 168 174.6 168 120C168 53.7 221.7 0 288 0s120 53.7 120 120zm8 80.4c3.5-6.9 6.7-13.8 9.6-20.6c.5-1.2 1-2.5 1.5-3.7l116-46.4C558.9 123.4 576 135 576 152V422.8c0 9.8-6 18.6-15.1 22.3L416 503V200.4zM137.6 138.3c2.4 14.1 7.2 28.3 12.8 41.5c2.9 6.8 6.1 13.7 9.6 20.6V451.8L32.9 502.7C17.1 509 0 497.4 0 480.4V209.6c0-9.8 6-18.6 15.1-22.3l122.6-49zM327.8 332c13.9-17.4 35.7-45.7 56.2-77V504.3L192 449.4V255c20.5 31.3 42.3 59.6 56.2 77c20.5 25.6 59.1 25.6 79.6 0zM288 152a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>
		<span class="mapifypro-admin-main-title">MAPIFYPRO<span>
	</div>
	
	<?php
}
add_action( 'in_admin_header', 'mpfy_admin_styles_in_admin_header' );

/**
 * Filter admin lists columns
 */
function mpfy_filter_admin_columns( $columns ) {
	$allowed_keys = array( 'cb', 'title', 'url_extension', 'categories', 'taxonomy-location-tag', 'date', 'icl_translations' );

	foreach ( $columns as $key => $value ) {
		if ( ! in_array( $key, $allowed_keys ) ) {
			unset( $columns[ $key ] );
		}
	}

	return $columns;
}
add_filter( 'manage_edit-map_columns', 'mpfy_filter_admin_columns' );
add_filter( 'manage_edit-map-location_columns', 'mpfy_filter_admin_columns' );
add_filter( 'manage_edit-map-drawer_columns', 'mpfy_filter_admin_columns' );
add_filter( 'manage_edit-route_map_columns', 'mpfy_filter_admin_columns' );

/**
 * Get MapifyPro svg icon
 * 
 * @param String $icon_name
 * @param String $styles
 * @return String The SVG icon to display.
 */
function mpfy_get_icon( $icon_name, $style = 'height: 17px; margin: -5px 7px -5px -2px; width: 20px; max-width: 20px;' ) {
	$icons = array(
		'maps'          => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#7a7d7f;}.fa-primary{fill:#9ca2a7;}</style></defs><path class="fa-primary" d="M0 114.5V464c0 11.3 11.5 19 22 14.8L192 416l-.1-384L10.1 99.6C4 102 0 107.9 0 114.5zM554 33.2L384 96V480l181.9-67.6c6.1-2.4 10.1-8.3 10.1-14.9V48c0-11.3-11.4-19.1-22-14.9z"/><path class="fa-secondary" d="M192.1 416L384 480V96L192 32l.1 384z"/></svg>',
		'map-locations' => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#7a7d7f;}.fa-primary{fill:#9ca2a7;}</style></defs><path class="fa-primary" d="M160 288A144 144 0 1 0 160 0a144 144 0 1 0 0 288zM96 144c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-53 43-96 96-96c8.8 0 16 7.2 16 16s-7.2 16-16 16c-35.3 0-64 28.7-64 64z"/><path class="fa-secondary" d="M128 284.4V480c0 17.7 14.3 32 32 32s32-14.3 32-32V284.4c-10.3 2.3-21 3.6-32 3.6s-21.7-1.2-32-3.6z"/></svg>',
		'map-areas'     => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#7a7d7f;}.fa-primary{fill:#9ca2a7;}</style></defs><path class="fa-primary" d="M0 192V320H64V192H0zm384 0V320h64V192H384zM160 480H288V416H160v64zm0-384H288V32H160V96z"/><path class="fa-secondary" d="M96 32h32V96H96c-17.7 0-32 14.3-32 32v32H0V128C0 75 43 32 96 32zm0 384h32v64H96c-53 0-96-43-96-96V352H64v32c0 17.7 14.3 32 32 32zm256 64H320V416h32c17.7 0 32-14.3 32-32V352h64v32c0 53-43 96-96 96zm96-320H384V128c0-17.7-14.3-32-32-32H320V32h32c53 0 96 43 96 96v32z"/></svg>',
		'crowdmaps'     => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#9ca2a7;}.fa-primary{fill:#7a7d7f;}</style></defs><path class="fa-primary" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/><path class="fa-secondary" d="M609.3 512H471.4c5.4-9.4 8.6-20.3 8.6-32v-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2h61.4C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z"/></svg>',
		'location-tags' => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#9ca2a7;}.fa-primary{fill:#7a7d7f;}</style></defs><path class="fa-primary" d="M0 80V229.5c0 17 6.7 33.3 18.7 45.3l168 168c25 25 65.5 25 90.5 0L410.7 309.3c25-25 25-65.5 0-90.5l-168-168c-12-12-28.3-18.7-45.3-18.7H48C21.5 32 0 53.5 0 80zm112 32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/><path class="fa-secondary" d="M311.1 38.9c9.4-9.3 24.6-9.2 33.9 .2L472.8 168.4c52.4 53 52.4 138.2 0 191.2L360.8 472.9c-9.3 9.4-24.5 9.5-33.9 .2s-9.5-24.5-.2-33.9L438.6 325.9c33.9-34.3 33.9-89.4 0-123.7L310.9 72.9c-9.3-9.4-9.2-24.6 .2-33.9z"/></svg>',
		'batch-upload'  => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#7a7d7f;}.fa-primary{fill:#d1d4d6;}</style></defs><path class="fa-primary" d="M384 160H256c-17.7 0-32-14.3-32-32V0L384 160zM216 392c0 13.3-10.7 24-24 24s-24-10.7-24-24V289.9l-31 31c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l72-72c9.4-9.4 24.6-9.4 33.9 0l72 72c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-31-31V392z"/><path class="fa-secondary" d="M0 64C0 28.7 28.7 0 64 0H224V128c0 17.7 14.3 32 32 32H384V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM216 392V289.9l31 31c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-72-72c-9.4-9.4-24.6-9.4-33.9 0l-72 72c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l31-31V392c0 13.3 10.7 24 24 24s24-10.7 24-24z"/></svg>',
		'multi-map'     => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#9ca2a7;}.fa-primary{fill:#7a7d7f;}</style></defs><path class="fa-primary" d="M448 160c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 224c0-35.3 28.7-64 64-64l384 0z"/><path class="fa-secondary" d="M464 104c0-13.3-10.7-24-24-24L72 80c-13.3 0-24 10.7-24 24s10.7 24 24 24l368 0c13.3 0 24-10.7 24-24zM416 24c0-13.3-10.7-24-24-24L120 0C106.7 0 96 10.7 96 24s10.7 24 24 24l272 0c13.3 0 24-10.7 24-24z"/></svg>',
		'settings'      => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#9ca2a7;}.fa-primary{fill:#7a7d7f;}</style></defs><path class="fa-primary" d="M489.6 191.2c6.9-6.2 9.6-15.9 6.4-24.6c-4.4-11.9-9.7-23.3-15.8-34.3l-4.7-8.1c-6.6-11-14-21.4-22.1-31.2c-5.9-7.2-15.7-9.6-24.5-6.8L389.1 98.8c-10 3.2-20.8 1.1-29.7-4.6c-4.9-3.1-9.9-6.1-15.1-8.7c-9.3-4.8-16.5-13.2-18.8-23.4l-8.9-40.7c-2-9.1-9-16.3-18.2-17.8C284.7 1.2 270.5 0 256 0s-28.7 1.2-42.5 3.5c-9.2 1.5-16.2 8.7-18.2 17.8l-8.9 40.7c-2.2 10.2-9.5 18.6-18.8 23.4c-5.2 2.7-10.2 5.6-15.1 8.7c-8.8 5.7-19.7 7.7-29.7 4.6L83.1 86.1c-8.8-2.8-18.6-.3-24.5 6.8c-8.1 9.8-15.5 20.2-22.1 31.2l-4.7 8.1c-6.1 11-11.4 22.4-15.8 34.3c-3.2 8.7-.5 18.4 6.4 24.6l30.9 28.1c7.7 7.1 11.4 17.5 10.9 27.9c-.1 2.9-.2 5.8-.2 8.8s.1 5.9 .2 8.8c.5 10.5-3.1 20.9-10.9 27.9L22.4 320.8c-6.9 6.2-9.6 15.9-6.4 24.6c4.4 11.9 9.7 23.3 15.8 34.3l4.7 8.1c6.6 11 14 21.4 22.1 31.2c5.9 7.2 15.7 9.6 24.5 6.8l39.7-12.6c10-3.2 20.8-1.1 29.7 4.6c4.9 3.1 9.9 6.1 15.1 8.7c9.3 4.8 16.5 13.2 18.8 23.4l8.9 40.7c2 9.1 9 16.3 18.2 17.8c13.8 2.3 28 3.5 42.5 3.5s28.7-1.2 42.5-3.5c9.2-1.5 16.2-8.7 18.2-17.8l8.9-40.7c2.2-10.2 9.4-18.6 18.8-23.4c5.2-2.7 10.2-5.6 15.1-8.7c8.8-5.7 19.7-7.7 29.7-4.6l39.7 12.6c8.8 2.8 18.6 .3 24.5-6.8c8.1-9.8 15.5-20.2 22.1-31.2l4.7-8.1c6.1-11 11.3-22.4 15.8-34.3c3.2-8.7 .5-18.4-6.4-24.6l-30.9-28.1c-7.7-7.1-11.4-17.5-10.9-27.9c.1-2.9 .2-5.8 .2-8.8s-.1-5.9-.2-8.8c-.5-10.5 3.1-20.9 10.9-27.9l30.9-28.1zM256 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/><path class="fa-secondary" d="M192 256a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"/></svg>',
		'routes'        => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:1;fill:#9ca2a7;}.fa-primary{fill:#7a7d7f;}</style></defs><path class="fa-primary" d="M320 96c0 54.5 63.8 132.1 82.7 154c3.3 3.9 8.2 6 13.3 6s9.9-2.1 13.3-6c19-21.9 82.7-99.6 82.7-154c0-53-43-96-96-96s-96 43-96 96zm64 0a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zM0 352c0 54.5 63.8 132.1 82.7 154c3.3 3.9 8.2 6 13.3 6s9.9-2.1 13.3-6c19-21.9 82.7-99.6 82.7-154c0-53-43-96-96-96s-96 43-96 96zm64 0a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/><path class="fa-secondary" d="M358.6 192c17.1 26 35.4 48 44.1 58c3.3 3.9 8.2 6 13.3 6H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c53 0 96 43 96 96s-43 96-96 96H96c5.1 0 9.9-2.1 13.3-6c8.7-10.1 27-32 44.1-58H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320c-53 0-96-43-96-96s43-96 96-96h38.6z"/></svg>',
		'route-map'     => '<svg %s xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#9ca2a7}</style><path d="M465.7 172.6c-17 27.2-37.1 52.3-49.7 67.1c-12.6-14.8-32.7-39.8-49.7-67.1C348.4 144 336 116.2 336 96c0-44.2 35.8-80 80-80s80 35.8 80 80c0 20.2-12.4 48-30.3 76.6zM427.4 251C452.9 221.1 512 146.2 512 96c0-53-43-96-96-96s-96 43-96 96c0 44.9 47.2 109.4 75.4 144H328c-39.8 0-72 32.2-72 72s32.2 72 72 72H440c30.9 0 56 25.1 56 56s-25.1 56-56 56H160c-4.4 0-8 3.6-8 8s3.6 8 8 8H440c39.8 0 72-32.2 72-72s-32.2-72-72-72H328c-30.9 0-56-25.1-56-56s25.1-56 56-56h84.9c5.1 1.1 10.7-.6 14.5-5zM145.6 426c-17.1 25.8-37.2 49.1-49.6 62.7C83.6 475.1 63.5 451.8 46.4 426C28.5 398.9 16 372.1 16 352c0-44.2 35.8-80 80-80s80 35.8 80 80c0 20.1-12.5 46.9-30.4 74zM107 500.4c25.2-27.4 85-97.9 85-148.4c0-53-43-96-96-96s-96 43-96 96c0 50.5 59.8 121 85 148.4c6 6.5 16 6.5 21.9 0zM416 80a16 16 0 1 1 0 32 16 16 0 1 1 0-32zm0 48a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM80 352a16 16 0 1 1 32 0 16 16 0 1 1 -32 0zm48 0a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>',
	);

	return isset( $icons[ $icon_name ] ) ? sprintf( $icons[ $icon_name ], "class='mapifypro-svg-icon' style='$style'" ) : '';
}
<?php
// Adds front-end search
include_once('search.php');

// Adds image mode to maps
include_once('search-radius/search-radius.php');

// Provides map and map location tagging and filtering
include_once('map-tags/map-tags.php');

// Introduces map location list under map
include_once('map-location-list/map-location-list.php');

// Add per-location pin image
include_once('map-location-pin-image.php');

// Adds a column to the admin ui signifying the correct shortcode to use for maps
include_once('admin-column-shortcode.php');

// Adds a column to the admin ui signifying the map location permalink
include_once('admin-column-permalink.php');

// Provides batch uploading of map locations
include_once('batch-upload/batch-upload.php');

// Provides social sharing capabilities for locations
include_once('social-sharing/social-sharing.php');

// Provides ability to change various popup colors
include_once('popup-colors/popup-colors.php');

// Adds map mode selection
include_once('map-mode.php');

// Adds image mode to maps
include_once('image-mode/image-mode.php');

// Add pro settings to image mode
include_once('image-mode-pro.php');

// Provides ability to display a location details block in the popup
include_once('popup-location-details.php');

// Provides directions button in location details block
include_once('popup-location-details-directions.php');

// Adds map locations to the blog listing along with normal posts
include_once('locations-in-blog.php');

// Adds image mode to maps
include_once('multiple-maps.php');

// Allows locations to open a new browser tab to an external url instead of opening a popup
include_once('location-external-url.php');

// Add directions to toolip
include_once('tooltip-directions.php');

// Add svg support for marker images
include_once('svg-support.php');

// Add "Show My Location" to maps
include_once('use-my-location.php');

// Allows maps to cluster markers
include_once('map-clustering.php');

include_once('zendesk-widget.php');
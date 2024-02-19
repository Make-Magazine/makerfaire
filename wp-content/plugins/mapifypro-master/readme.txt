=== MapifyPro ===
Contributors: mapifypro, harisrozak
Tags: Custom Mapping, Maps, Google Maps Customization
Requires at least: 4.8.15
Tested up to: 6.3.1
Stable tag: 4.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MapifyPro is an elite plugin for WordPress that implements fully-customized maps on your site, designed and developed by Mapify LLC.

== Description ==

MapifyPro is an elite plugin for WordPress that implements fully-customized maps on your site. 

It enhances Google maps with custom pin-point graphics and pop-up galleries, but also allows ANY custom map image of your choosing, 
all while keeping the great zoom and pan effect of Google maps! Perfect for creating a store locator, travel routes, tours, journals, and more.

== Installation ==

1. Upload and unzip `mapifypro.zip` to the `/wp-content/plugins/` directory. Alternatively you can use the Worpdress admin to upload the plugin via the "Plugins" section. 
2. Activate the plugin through the 'Plugins' menu in WordPress. The plugin will ask you to register your license key.
3. An admin menu named "Mapifypro" will shown to create and modify your maps.
4. Further documentation and tutorials available here: https://www.mapifypro.com/forums/

== Important Links ==

SUPPORT AND FAQ: https://mapifypro.zendesk.com/hc/en-us
FULL DOCUMENTATION: https://mapifypro.com/MapifyPro_Documentation_2.0.pdf

== Frequently Asked Questions ==

NOTE THAT THESE ISSUES ARE COVERED IN DOCUMENTATION: https://www.mapifypro.com/documentation
USER KNOWLEDGE BASE: https://support.mapifypro.com/

== Changelog ==

= v4.7.1 =
- Re-compiled to fix compatibility errors with Yoast


= v4.6.1 =
- Updated the PrettyRoutes route maker functionality, look and feel. Site admins can now easily create multiple routes in a map (#93) .
- Updated the MapMaker to run on a faster and more responsive server (#92).
- Updated the admin pages look and feel (#101).
- Changed the name of PrettyRoutes to MapifyRoutes (#101).
- Tested up to WordPress v6.3.1 (#106).

= v4.5.7 =
- For the Tooltip Image Orientation option, make "Left (recommended)" the default option (#94).
- Enable compatibility with the AutoOptimize Wordpress plugin (#87).
- Load all fonts directly instead of from third party sites for GDPR compliance (#88).
- Allow Customization of the Address Format on the Map Location List. This allows more address formats from all over the world (#90).
- Allow Map Location Links and Map Search Radius fields to be cleared (#91).
- Better user experience when generating map tiles for custom Image Maps (#92).
- Enable Facebook Video embedding in the Map Location Popup (#95).
- Added 14 new map styles (#97):
  - Toner Background
  - Toner Lite
  - Positron
  - Positron (No Labels)
  - Dark Matter
  - Dark Matter (No Labels)
  - Voyager
  - Voyager (Grey Labels)
  - Voyager (No Labels)
  - Esri DeLorme
  - Esri World Street Map
  - Esri World Topo Map
  - Esri World Imagery
  - Esri World Gray Canvas
- Reduce errors when upgrading MapifyPro libraries and dependencies (#98)

= v4.4.3 =
- Enable the double-click-to-zoom feature if the crowdmaps for the map is disabled
- Fixed a reported issue about the map location pop-up that not automatically show up for the map location url 
- Prevent map zooming when centering the map to a map-location (pin)
- Limit map zooming both for map and image mode to a practical level

= v4.4.2 =
- Fixed: The map search is not working if the interactive list has been set to "hide by default"
- Moved the "Maps" selection settings into its own section
- Optimized map so that PrettyRoutes code is only loaded if the map contains one or more routes

= v4.4.0 =
- Faster and smoother map zooming and panning thanks to our own vector maps! This also fixes issues that European site visitors were having. Please change your maps to use one of these MapifyPro styles: MapifyPro Basic, MapifyPro Bright, or MapifyPro Streets.

= v4.3.3 =
- Updated Advanced Custom Fields (ACF) Pro within MapifyPro from v5.9.5 to v6.0.0
- Fixed: Map Location Pins Not Following the Default Icon That Was Set for the Current Map
- Fixed: If the "Default Pin Image" has been set, the search results of the "Mapify Map Selector" can't be clicked

= v4.3.2 =
- Added a new cache buster functionality to address the JavaScript caching issues
- Fixed: Unable to (Completely) Delete Images from the Map Location's Image Gallery
- Fixed: JavasScript conflict between Lodash and Underscore on PrettyRoutes
- Various minor fixes

= v4.3.1 =
- Allow Multiple Locations tags to be selected at the same time
- Change the PrettyRoutes map selector to be similar to the map selector from MapifyPro Map Locations
- When the user opens the tooltip then allow them to click the tooltip to open the popup

= v4.3.0 =
- Fixed bugs related to Map Locations links.
- Rebuilt the social share functionality. Updated the position of social share icons so that the popup appears centered on the page.
- Fixed on-load tooltip on non-clustering map.
- Removed "Permalink" column on admin map locations list.
- Prevent plugin conflict with MapifyLite
- Resized the tooltip image size to make it load faster
- Enabled map search & filter by default
- Merged PrettyRoutes and CrowdMaps into MapifyPro

= v4.2.9 =
* Improved compatibility with WP Bakery
* Improved the user experience for multi-location searches
* Added a feature to search for locations by name
* Allows CrowdMaps submitter to edit Map Locations
* Updated the capability_type for `map-location` post type from `post` to `map_location` to accommodate the new CrowdMaps submitter permission
* Because of the capability_type changes, your users accessibility might be affected if you have modified the default roles capabilities on your website 

= v4.2.8 =
* Allow the site visitor to search for multiple locations
* UI/UX change for Map Location search in Map Edit page
* Error message show up on user side, related to wc-am-client.php
* Auto map locations search
* Popup carousel's height is taller than the browser screen
* Map is not showing on backend due to backend caching

= 4.2.7 =
* Uploaded Image in a MapifyPro Map Loses Quality
* Grand Canyon as default map location; and add Toggle Button to posts; and resolve conflict with ACF Lite
* Images should not be repeated in gallery

= 4.2.6 =
* API improvements

= 4.2.5 =
* Improved support for WPML

= 4.2.4 =
* Allow customers to add Map Locations to a map from inside a map page.
* Reschedule the license checker API request from twice a day to once a week
* Users must re-activate MapifyPro API Key upon upgrade. Minor bug fixes.
* Allow customers to have Map Locations that link directly to another page


= 4.2.1 =
* Version bump to bypass upgrade bug
* Modified readme.txt by adding some datails and logs

= 4.2.0 =
* Licence expiration bug fix
* Modified readme.txt by adding some datails and logs

= 4.1.0 =
* Added "Multi Map" feature
* Added feature to auto re-activate API key after update
* Various bug fixes and minor updates
* Tested up to WordPress 5.8

= 4.0.0 =
* Major custom-fields update from Carbon Fields to ACF
* Various bug fixes and updates
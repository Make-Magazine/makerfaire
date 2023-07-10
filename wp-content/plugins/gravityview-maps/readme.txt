=== GravityView - Maps ===
Tags: gravityview
Requires at least: 4.7
Tested up to: 6.2
Stable tag: trunk
Contributors: katzwebservices, luistinygod, soulseekah, mrcasual
License: GPL 3 or higher
Requires PHP: 7.2.0

Displays entries over a map using markers

== Description ==

### To set up:

* Use existing View connected to a form with an Address field
* Switch View Type, select Maps
* Add Address parent field to the Address zone
* Save the View
* Voilà

### Map Icons

GravityView Maps uses map icons from the [Maps Icons Collection by Nicolas Mollet](https://mapicons.nicolasmollet.com/). By default, a pre-selection of about 100 icons (from more than 700 available icons) has been added to the plugin.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Configure "Maps" via the Maps metabox when editing a View

== Changelog ==

= 2.2 on July 6, 2023 =

* Fixed: A notice requesting REST API access shows up for all View types, not just Views with maps
* Fixed: The front-end notice alerting administrators that the REST API setting is disabled was not worded clearly
* Fixed: Using custom longitude and latitude values on forms without an Address field
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.1.0

__Developer Notes:__

* Added: `Marker::from_address_field()` and `Marker::from_coordinate_fields()` methods to construct a Marker object from an Address field or Longitude/Latitude fields
* Added: `base.js` file to the `assets/js` directory, used to handle global JS hooks
* Added: JavaScript filter to modify the options used to build a Marker: `gk.maps.marker_options`
* Added: JavaScript action after a Marker is added: `gk.maps.after_add_marker`
* Modified: Renamed many JavaScript hooks to use standard GravityKit naming structure:
	- `gravitykit/maps/autocomplete_field_id` => `gk.maps.autocomplete_field_id`
	- `gravitykit/maps/autocomplete_input_id` => `gk.maps.autocomplete_input_id`
	- `gravitykit/maps/autocomplete_field_init` => `gk.maps.autocomplete_field_init`
	- 'gravitykit_maps_invalid_coordinates' => `gk.maps.invalid_map_coordinates`
	- `gravitykit_maps_after_process_map_markers` => `gk.maps.after_process_map_markers`
	- `gravitykit_maps_beforeInit` => `gk.maps.before_maps_init`
	- `gravitykit_maps_afterInit` => `gk.maps.after_maps_init`
	- `gravitykit_maps_init_maps` => `gk.maps.init_maps`
	- `gravitykit_maps_before_init_map` => `gk.maps.before_init_map`
	- `gravitykit_maps_after_init_map` => `gk.maps.after_init_map`
	- `gravitykit_maps_before_process_map_markers` => `gk.maps.before_process_map_markers`
	- `gravitykit_maps_after_process_map_markers` => `gk.maps.after_process_map_markers`
	- `gravitykit_maps_getMaps` => `gk.maps.get_maps`
	- `gravitykit_maps_getSearchMaps` => `gk.maps.get_search_maps`

= 2.1 on May 16, 2023 =

* Improved: Accessibility of the temporary notice displayed during the executing of an Ajax request
* Fixed: Preview image for the "Business Map Listing" form preset in the View editor
* Fixed: Incorrect map centering when geolocation radius value is left empty during search
* Fixed: Incompatibility with some plugins/themes that use Laravel components
* Fixed: PHP warning messages that occurred under specific conditions
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.12

= 2.0 on April 12, 2023 =

We’re so excited to announce the release of Maps 2.0. [Read the Maps 2.0 announcement blog post](https://www.gravitykit.com/announcing-maps-2-0/). There are so many great new features:

* [Geolocation radius search](https://docs.gravitykit.com/article/925-how-to-enable-radius-search) - Users can now search for entries within a certain radius of their current location or a specific location
* [Draggable maps with "Redo search in map" and "Search as map moves"](https://docs.gravitykit.com/article/927-search-entries-as-map-moves) - Users can now search using the map itself! Map markers will refresh in real-time as you click and drag the map to different locations.
* [Address field autocomplete](https://docs.gravitykit.com/article/926-enabling-autocomplete-for-the-gravity-forms-address-field) - Make it easy for users to enter addresses using address autocomplete. Enable this feature by editing your form and checking the new “Enable geolocation autocomplete” checkbox in the settings of Address fields.
* Faster maps - We’ve rewritten our map rendering to dramatically improve performance

* Added: "International Autocomplete Filter" setting to restrict the countries that are used for address autocomplete
* Modified: Draggable maps functionality requires Views have REST API enabled
* Modified: The "Redo search in map" and "Search as map moves" settings are now enabled by default

__Developer Updates:__

* Added: Wrapping `<div>` surrounds each View, matching the containers in Table and List layouts
* Added: `templates/search-field-geo_radius.php` template file
* Added: Javascript hooks:
	- Filters:
		- `gravitykit/maps/autocomplete_input_id` Modify the input ID used for address autocomplete powered by Google Maps
	- Actions:
		- `gravitykit/maps/autocomplete_field_init` Allows third-party inclusion of actions on initializing the field for auto complete
* Added: PHP hooks:
	- Filters:
		- `gk/gravitymaps/geolocation_fields` - Filter which fields are considered geolocation fields
		- `gravityview/view/wrapper_container` - Modify the HTML wrapper container for the View; [see examples of modifying the container](https://docs.gravitykit.com/article/867-modifying-the-view-container-div)
		- `gk/gravitymaps/current_location_instant_search` - Change the default setting for the Current Location behavior: `true` will instantly search when the current location is allowed by the browser and `false` requires users click the submit button (default: false)
* Modified:
	- `templates/fields/entry_map.php` template to fetch existing instance of object for improved performance
	- `templates/map-header.php` and `templates/map-footer.php` files to add the container wrapper (required for draggable maps)

= 1.11 on February 20, 2023 =

**Note: GravityView Maps now requires PHP 7.2 or newer**

* Improved: Unrestricted Google Maps API key is now stored encrypted in the database and is partially masked when displayed in settings

= 1.10 on December 21, 2022 =

* Fixed: PHP 8.1 notices
* Fixed: Fatal error on some hosts due to a conflict with one of the plugin dependencies (psr/log)
* Fixed: Address geocoding script being loaded on all admin pages

= 1.9 on December 15, 2022 =

* Added: You can now use Drop Down and Radio Buttons field choices to set an entry's map marker icon—you can show different icons based on the submitted value of the fields! [Learn more about choice-based marker icons](https://docs.gravitykit.com/article/889-a).
    - A huge thanks to [Jetsloth](https://jetsloth.com) for letting us use their excellent [Image Choices](https://jetsloth.com/gravity-forms-image-choices/) code as a base for this feature 🦥🤗
* Added: A "Link the Title" View setting to control whether the info box Title automatically links to the single entry page
* Improved: Optimize marker rendering on the map
* Improved: Updated the default map icon to be a vector graphic instead of an image
* Improved: Map zoom options now include some helper text to be clearer
* Fixed: Fatal error on some hosts that use weak security keys and salts

__Developer Updates__

* Modified: `map-marker-infowindow.php` template now uses `link_open` and `link_close` placeholders and the anchor tag is passed into the template. A reminder: you can use the `gravityview/maps/infowindow/content/vars` to modify the info box content.
* Modified: Markers in the `GV_MAPS` JavaScript object now pass an array for `icon`, instead of `icon_url`. This allows modifying the icon size, anchor, etc. before being rendered on the map.

= 1.8.2 on December 5, 2022 =

* Fixed: Critical error when rendering map in the single entry View

= 1.8.1 on December 2, 2022 =

* Improved: Reduced the plugin download package size

= 1.8 on December 1, 2022 =

* Added: New WordPress admin menu where you can now centrally manage all your GravityKit product licenses and settings ([learn more about the new GravityKit menu](https://www.gravitykit.com/foundation/))
    - Go to the WordPress sidebar and check out the GravityKit menu!
    - We have automatically migrated your existing Google Maps API key, which was previously entered in the Views→Settings page
    - Request support using the "Grant Support Access" menu item
* Added: Maps settings are now available for network subsites in a multisite environment
* Added: Ability for the network administrator to share Google Maps API keys with all subsites
* Added: Ability to use a separate key for Geocoding that's never exposed to end users
* Updated: Prevent a potential error when using the Divi theme without a Google Maps API key set

= 1.7.7 on September 12, 2022 =

* Updated: [GravityView (the company) is now GravityKit](https://gravityview.co/rebrand/)
* Improved: Google Maps API error notices
* Fixed: Configuration notices were being shown when a search returned no results

= 1.7.6 on June 16, 2022 =

* Fixed: License key input not working with Gravity Forms 2.6+

= 1.7.5 on March 3, 2022 =

* Improved: Show a notice when the Maps layout hasn't been fully configured
* Fixed: Maps not displaying properly when using block themes (in WordPress 5.9+)
* Updated: Translations with best matches from similar locales (Spain Spanish and Mexican Spanish, for example)

= 1.7.4 on February 1, 2022 =

* Tested with WordPress 5.9
* Fixed: Map icon not working when editing an entry via Gravity Forms

= 1.7.3.1 on September 1, 2021 =

* Fixed: 1.7.3 release was causing a fatal error due to some missing files

= 1.7.3 on September 1, 2021 =

* Fixed: Map not initializing when the View is embedded in a custom post type
* Updated translations: Dutch (thanks Erik!), Russian (thanks Irina!)

= 1.7.2 on May 19, 2021 =

* Added: Support for Gravity Forms 2.5
* Updated: Map zoom level settings to include 19–21
* Fixed: Map not being initialized when the View shortcode is embedded via the [Elementor](https://elementor.com/) page builder
* Fixed: Cached coordinates for multi-input fields would not clear when updating entries

__Developer Updates:__

* Added: Ability to center the map using the map options filter. [See sample code here](https://gist.github.com/zackkatz/cb1f52f563cb13fd7abe06b0553cdec2)
* Added: `gravityview/maps/marker/add` filter to modify marker before it gets added to the map

= 1.7.1 on October 14, 2019 =

* Updated: Polish translation (Thanks, Dariusz Zielonka!)
* Fixed: Locations not being updated when non-Address fields were used for coordinates
* Fixed: Google Maps API-checking script being loaded on all admin pages

= 1.7 on September 2, 2019 =

* Improved: Allow input of latitude and longitude all the time for an Address field
* Improved: Many enhancements to the API key setup
    - Maps now will show a warning to administrators when the API settings aren't valid
    - Maps will now not show to users if the API isn't configured properly (instead of showing a broken map)
    - Added API key validation in the Maps section of the GravityView settings screen
    - Simplified settings by removing less-used methods of geocoding addresses (they are still available via developer filters)
    - Improved error message language
* Improved: Allow deleting geocoding results by saving empty latitude/longitude fields
* Changed: If an address only contains default values (such as Default State/Province or Default Country), do not show a marker on the map
* Fixed:  Map Icon Picker field #97
* Fixed: JS console error in Chrome
* Fixed: When the Google Maps key isn't working for geocoding, it was preventing other providers from working
* Fixed: Address wasn't being properly formatted when passed to geocoding providers

= 1.6.2 on December 21, 2018 =

* Fixed: Hide map when "Hide View data until search is performed" is enabled for a View
* Updated: Turkish translation (thanks, [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/)!)

= 1.6.1 on December 3, 2018 =

* Fixed: "Hide View data until search is performed" setting not working (also requires GravityView 2.2.1)
* Fixed: JavaScript error when map has no markers
* Updated translations - thank you, translators!
    - Polish translated by [@dariusz.zielonka](https://www.transifex.com/user/profile/dariusz.zielonka/)
    - Russian translated by [@awsswa59](https://www.transifex.com/user/profile/awsswa59/)

__Developer Updates:__

* Added: `alt` tag to default icon in Map Icon field
* Added: `gravityview/maps/available_icons/sections` and `gravityview/maps/available_icons/icons` filters to modify the icons shown in the Map Icon field
* Fixed: Add additional marker icons to the list in the Map Icon by adding .png images to your theme's ``/gravityview/mapicons/` subdirectory
* Fixed: Map Icon field compatibility with Gravity Forms 2.4 deprecation of `conditional_logic_event()`
* Modified: `maps-body.php` template file to run `gv_container_class()` on the `.gv-map-entries` container DIV

= 1.6 on October 15, 2018 =

[Learn all about this update on our blog post](https://gravityview.co/?p=590392)

* Added: Marker clustering—display multiple markers on a map as a single "cluster" [Learn how](https://docs.gravityview.co/article/495-map-marker-clustering)
* Added: When multiple markers are at the same location, clicking the location expands to show all markers
* Added: You can now override coordinates for address fields [Learn how](https://docs.gravityview.co/article/493-override-geocoding-coordinates)
* Added: Support for multiple address fields for a single entry [Here's how](https://docs.gravityview.co/article/492-markers-from-multiple-address-fields)
* Improved: When geocoding fails for an address, a note is added to the entry
* Improved: Map Icon field scripts are only loaded when the field is present
* Improved: When an entry is updated, refresh the geocoding cache only if the address has changed
* Improvement: If an entry has no address, but the Gravity Forms field has defaults set, use the defaults for geocoding
* Fixed: Google Maps API key not being added properly
* Fixed: When site was in debug mode (`WP_DEBUG` was enabled), addresses would be re-geocoded on each page load
* Fixed: Map Icon field styles loaded on Gravity Forms Preview and Gravity Forms Edit Entry screens

= 1.5 on May 31, 2018 =

* Fixed: Address fields with no label were appearing blank in the View settings dropdown
* Fixed: Standalone map fields not rendering
* Fixed: Address fields displaying multiple times on embedded Views
* Fixed: Error related to custom marker icons
* Fixed: Empty address field dropdown choice in View Settings when the field had no label
* Fixed: Maps scripts loading on all admin screens
* Tweak: Reduced number of database calls
* Changed: Hide the map widget when there are no results
* Changed: If GravityView core caching isn't available, don't cache markers
* Updated translations

= 1.4.2 on August 18, 2016 =

* Updated: "Zoom Control" setting has been simplified to "None", "Small", or "Default"; this is because Google Maps [no longer allows](https://developers.google.com/maps/documentation/javascript/releases#324) custom zoom control sizes
* Fixed: Don't render Maps widget if using a DataTables layout (we are hoping to support this in the future)
    * Also don't show Maps widget and fields in the Edit View screen when using a DataTables layout
* Fixed: Map not displaying when widget is in "Below Entries Widgets" zone
* Fixed: Javascript error when using the WordPress Customizer

__Developer Notes:__

* Allow global access to manipulate maps after instantiation ([see example](https://gist.github.com/zackkatz/1fccef0835aacd6693903c96ba146973))
* Added ability to set `mobile_breakpoint` via the `gravityview/maps/render/options` filter
* `zoomControlOptions` no longer allows `style` value to be set; instead, only `position` is valid ([See example](https://gist.github.com/zackkatz/630da145c8c813a48ba3b282b3610e5a))

= 1.4.1 on April 7, 2016 =

* New: Configure info boxes to display additional information when clicking a map marker. [Learn how here!](http://docs.gravityview.co/article/345-how-to-configure-info-boxes)
* Fixed: "Undefined index" PHP warning on frontend when saving a new Map View for the first time
* No longer in beta!

__Developer Notes:__

* Added: Filter `gravityview/maps/field/icon_picker/button_text` to modify the text of the Icon Picker button (Default: "Select Icon")
* Added: Use the `gravityview/maps/marker/url` hook to filter the marker single entry view link url
* Added: Use the `gravityview/maps/render/options` hook to change the marker link target attribute (marker_link_target`). [Read more](http://docs.gravityview.co/article/339-how-can-i-make-the-marker-link-to-open-in-a-new-tab)

= 1.3.1-beta on November 13, 2015 =

* Added: Option to set map zoom, separate from maximum and minimum zoom levels. Note: this will only affect Entry Map field maps or maps with a single marker.
* Fixed: Don't show a map if longitude or latitude is empty
* Fixed: If entry has an icon already set, show it as selected in the icon picker

= 1.2-beta on September 25, 2015 =

* Fixed: Google Maps geocoding requires HTTPS connection
* Fixed: Support all WordPress HTTP connections, not just `cURL`
* Added: Custom filters to allow the usage of different fields containing the address value [Read more](http://docs.gravityview.co/article/292-how-can-i-pull-the-address-from-a-field-type-that-is-not-address)
* Added: Filter to enable marker position based on the latitude and longitude stored in the form fields [Read more](http://docs.gravityview.co/article/300-how-can-i-use-the-latitude-and-longitude-form-fields-to-position-map-markers)
* Added: Entry Map field on the Multiple Entries view
* Added: How-to articles showing how to sign up for Google, Bing, and MapQuest API keys
* Fixed: Map layers not working for multiple maps on same page
* Fixed: `GRAVITYVIEW_GOOGLEMAPS_KEY` constant not properly set
* Fixed: Error when `zoomControl` disabled and `zoomControlOptions` not default
* Modified: Check whether other plugins or themes have registered a Google Maps script. If it exists, use it instead to avoid conflicts.
* Tweak: Update CSS to prevent icon picker from rendering until Select Icon button is clicked
* Tweak: Update Google Maps script URL from `maps.google.com` to `maps.googleapis.com`

= 1.1-beta on September 11, 2015 =

* Added: Lots of map configuration options
    - Map Layers (traffic, transit, bike path options)
    - Minimum/Maximum Zoom
    - Zoom Control (none, small, large, let Google decide)
    - Draggable Map (on/off)
    - Double-click Zoom (on/off)
    - Scroll to Zoom (on/off)
    - Pan Control (on/off)
    - Street View (on/off)
    - Custom Map Styles (via [SnazzyMaps.com](http://snazzymaps.com)
* Fixed: Single entry map not rendering properly
* Fixed: Reversed `http` and `https` logic for Google Maps script
* Fixed: Only attempt to geocode an address if the address exists (!)
* Fixed: Only render map if there are map markers to display
* Tweak: Added support for using longitude & latitude fields instead of an Address field [learn how](http://docs.gravityview.co/article/300-how-can-i-use-the-latitude-and-longitude-form-fields-to-position-map-markers)
* Tweak: Hide illogical field settings
* Tweak: Improved translation file fetching support

= 1.0.3-beta on August 4, 2015 =

* Added: Ability to prevent the icon from bouncing on the map when hovering over an entry [see sample code](https://gist.github.com/zackkatz/635638dc761f6af8920f)
* Modified: Set a `maxZoom` default of `16` so that maps on the single entry screen aren't too zoomed in
* Fixed: Map settings filtering out `false` values, which caused the `gravityview/maps/render/options` filter to not work properly
* Fixed: Map settings conflicting with Edit Entry feature for subscribers
* Fixed: `Fatal error: Call to undefined method GFCommon::is_entry_detail_edit()`
* Updated: French, Turkish, Hungarian, and Danish translations. Thanks to all the translators!

= 1.0.2-beta on May 15, 2015 =

* Added: New Gravity Forms field type: Map Icon. You can choose different map markers per entry.
* Added: Middle field zone in View Configuration
* Tweak: Improved styling of the map using CSS
* Updated translations

= 1.0.1-beta on April 27, 2015 =

* Fixed: Missing Geocoding library
* Updated translations

= 1.0-beta on April 24, 2015 =

* Initial release


= 1688751481-4249 =
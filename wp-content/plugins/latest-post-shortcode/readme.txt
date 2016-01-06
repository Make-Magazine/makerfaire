=== Plugin Name ===
Contributors: Iulia Cazan 
Author URI: https://profiles.wordpress.org/iulia-cazan
Tags: post, latest post, taxonomy, category, tag, parent, shortcode, latest, custom, selection, post by category, post by taxonomy, post by tag, post by id, post by parent, last post, tiles from posts, tile template, short content, short excerpt, limit content, limit excerpt, pagination, posts pagination
Requires at least: not tested
Tested up to: wp 4.4
Stable tag:  6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JJA37EHZXWUTJ

== Description ==
The plugin registers a configurable shortcode that allows you to create a dynamic content selection from your posts, pages and custom post types by combining and filtering what you need. The shortcode can be generated very easy, the plugin will add a shortcode button for this in the editor area.
= Latest Post Shortcode Slider =
* https://wordpress.org/plugins/latest-post-shortcode-slider-extension/ 

== Installation ==
* Upload `Latest Post Shortcode` to the `/wp-content/plugins/` directory of your application
* Login as Admin
* Activate the plugin through the 'Plugins' menu in WordPress

== Hooks ==
admin_enqueue_scripts, init, plugins_loaded, media_buttons_context, admin_footer, admin_head, wp_head

== Screenshots ==
1. The shortcode generator button in the editor area.
2. Options to configure the shortcode.

== Frequently Asked Questions ==
None

== Changelog ==
= 6.1 = 
* Add suppress filters false
* Apply filters before displaying the post image

= 6.0 = 
* Add support for the Latest Post Shortcode Slider extension

= 5.4 =
* Add the plugin link
* Separate the content and excerpt filters
* Tested up to 4.3.1

= 5.3 =
* Add the 'open in a new window' option for the links

= 5.2 =
* Implement changes to render full posts content (including the extra shortcodes)

= 5.1 =
* Add the order option (by date, title, menu order)
* Add the ajax pagination option. As the shortcode pagination relies on the wp native pagination, when using more shortcodes with pagination on the same page, the navigation will affect all shortcodes, hence, by activating the ajax pagination, each shortcode pagination will act independent

= 5.0 =
* Add the extra display of post tags of the posts
* Add filter to allow the text widget to render the content of a shortcode

= 4.2 =
* Introduce the post date in the output. The general settings will apply to the date and time format.

= 4.1 =
* Add changes to the javascript to avoid the content check for resize when lightbox resources are not available (compatibility with other plugins)

= 4.0 =
* Add Pagination Position (default to top only) so that the pagination can be displayed below the results, or above and below the results
* Add Dynamic Tag option so that you can show the posts that have one of the current page tags (current page is the page where the shortcode is embedded), without the need to specify a particular tag. This is useful to display something like "similar posts" or "on the same topic", etc.

= 3.1 =
* Populate the "Use Image" dropdown dynamically from the list of image sizes registered in the application
* Add global tile a class to differentiate when the link is applied to the entire tile content or to just the "read more" text

= 3.0 =
* Add No Pagination / Paginate Results option that allows to paginate the posts selection
* Add Records Per Page option
* Add Offset
* Add Hide / Show Pagination Navigation that allows to hide or show the pagination
* Reload Tile Pattern selection when a shortcode is selected before clicking the plugin button (reload shotcode settings in the content selection lightbox) 

= 2.0 =
* Allow for different tile pattern (the html tags order in the tile: post title, image, text and read more message) 
* Add visual tile pattern selector
* Add short excerpt and short content options
* Add chars limit to the excerpt or content for the tile
* Add custom "read more" message option
* Allow for the post link to wrap the entire tile or just the "read more" message if this is set

== Upgrade Notice ==
Chars limit, custom "read more" option and different tile patterns in the new version! You should upgrade, it's free! 
Donation and reviews are welcomed and will help me to continue future development.

== License ==
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 

== Version history ==
6.1 - Apply more filters
6.0 - Add support for the Latest Post Shortcode Slider extension
5.4 - Separate the content and excerpt filters
5.3 - Open links in a new window
5.2 - Render full post content
5.1 - Posts order and ajax pagination
5.0 - Extra tags display and text widget filter
4.2 - Post date option
4.1 - Compatibility update
4.0 - Pagination position and dynamic tag
3.1 - Dynamic image dropdown option
3.0 - Pagination options
2.0 - Visual pattern selector and more features
1.0 - Development version.
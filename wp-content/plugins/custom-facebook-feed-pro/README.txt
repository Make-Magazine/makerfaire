=== Custom Facebook Feed Pro ===
Author: Smash Balloon
Support Website: http://smashballoon.com/custom-facebook-feed/
Requires at least: 4.1
Tested up to: 5.8
Version: 4.0.6
License: Non-distributable, Not for resale

The Custom Facebook Feed allows you to display a completely customizable Facebook feed of any public Facebook page on your website.

== Description ==
Display a **completely customizable**, **responsive** and **search engine crawlable** version of your Facebook feed on your website. Completely match the look and feel of the site with tons of customization options!

* **Completely Customizable** - by default inherits your theme's styles
* **Feed content is crawlable by search engines adding SEO value to your site** - other Facebook plugins embed the feed using iframes which are not crawlable
* **Completely responsive and mobile optimized** - works on any screen size
* Display statuses, photos, videos, events, links and offers from your Facebook page
* Choose which post types are displayed. Only want to show photos, videos or events? No problem
* Display multiple feeds from different Facebook pages on the same page or throughout your site
* Show likes, shares and comments for each post
* Automatically embeds YouTube and Vimeo videos right in your feed
* Show event information - such as the name, time/date, location, link to a map, description and a link to buy tickets
* Filter posts by string or #hashtag
* Post tags - creates links when using the @ symbol to tag other people in your posts
* Post caching means that your feed is load lightning fast
* Fully internationalized and translatable into any language
* Enter your own custom CSS for even deeper customization

== Installation ==
1. Install the Custom Facebook Feed either via the WordPress plugin directory, or by uploading the files to your web server (in the /wp-content/plugins/ directory).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the 'Facebook Feed' settings page to configure your feed.
4. Use the shortcode [custom-facebook-feed] in your page, post or widget to display your feed.
5. You can display multiple feeds of different Facebook pages by specifying a Page ID directly in the shortcode: [custom-facebook-feed id=smashballoon num=5].

== Changelog ==
= 4.0.6 =
* Fix: Fixed a compatibility issue with the Complianz Cookie Consent plugin integration.
* Fix: Locations of feeds sorted by source would not work if the source ID was not a number.
* Fix: Fixed an issue where setting were not preserved even when using the preserve settings option and deleting the plugin.
* Fix: Added support for "eventoffset" setting in shortcode.
* Fix: Fixed locale setting causing a PHP warning if not set.
* Fix: Fixed PHP warnings when viewing dashboard notifications.
* Fix: Fixed some options and tables not being removed from the WordPress database on uninstall.
* Fix: Added a workaround for a rare issue with posts from other accounts displaying in feeds meant to show only posts from the account author.
* Fix: Fixed styling issues for the notice to renew your license that appears when a license expires.

= 4.0.5 =
* Fix: Fixed a bug that was inadvertently adding a notice to the WordPress dashboard.

= 4.0.4 =
* Tweak: The Optimize Images setting is now automatically enabled when using the GDPR consent feature to allow it to work as intended.
* Tweak: The mobile columns setting will now be applied when the screen width is 480px or less, instead of 400px.
* Tweak: Added support for the "cachetime" shortcode setting which can be used if you need to change the caching method to check for new content when the page loads instead of in the background. This can be used to set the number of minutes to wait until the next check is performed. This is not recommended for most users.
* Fix: Added a setting back into the feed customizer to change the Like Box position to be at the top or bottom of the feed.
* Fix: Fixed an issue where under some circumstances no posts would show when filtering a feed even if posts matched the filter.
* Fix: Adding a Custom Facebook Feed block without using the "embed" button in the CFF customizer would not work if more than 1 feed was created.
* Fix: Fixed a compatibility issue with the Complianz Cookie Consent plugin integration.
* Fix: Fixed a compatibility issue with the Web Toffee GDPR Cookie Consent plugin integration.
* Fix: Switching from a Single Featured Post feed to another feed type when inside the customizer would sometimes result in no posts displaying.
* Fix: Fixed a problem with the carousel layout when using themes that add paragraph elements automatically.
* Fix: When connecting a new group source, the link to the selected Facebook group in the directions for admins to grant permission to Smash Balloon's app was incorrect.
* Fix: Removed the limit applied to the number of Facebook sources that can be shown at one time when connecting a Facebook account.

= 4.0.3 =
* Fix: Error messages were not appearing at the top of the settings page when there was a critical error with a feed.
* Fix: Videos would not play in the feed when the lightbox was disabled.
* Fix: Legacy feeds were not updating when changes were made to the feed source and the multifeed extension was being used.
* Fix: "posttags" shortcode setting was not working.
* Fix: Some carousel settings were not being reflected in the feed preview when customizing a feed.
* Fix: Media was not loading correctly when using the GDPR setting.
* Fix: The license field would disappear when an unexpected value was saved for the license data.
* Fix: Sources were not showing that there was an error when an account specific Facebook error was occurring.

= 4.0.2 =
* Fix: Cache would not clear before posts were updated in the posts cache.
* Fix: Feeds would always use 4 columns for desktop size screens when using the "grid" layout.
* Fix: Some legacy feeds had an invalid locale set as the default locale which would cause no posts to display in the customizer.
* Fix: Adding a feed using the Elementor module would cause the lightbox to appear at the bottom of the page for some themes.
* Fix: Fixed an issue with unintended sources being added and removed when editing sources using the customizer and the [Multifeed extension](https://smashballoon.com/extensions/multifeed).

= 4.0.1 =
* Fix: Fixed an error that would occur when creating new database tables for certain MySQL versions/engines.
* Fix: Some lightbox features would not work with certain combinations of feed settings such as videos playing and multiple images displaying.
* Fix: When updating from version 3.x, some settings for the number of posts would cause the feed customizer not to load when editing legacy feeds.
* Fix: Event details would not display in an event feed even if event details were included in the settings for a feed.
* Fix: Only the first account's posts would display in a multifeed created with the new feed builder.
* Fix: Setting the number of columns for mobile devices to "1" would not work correctly if the tablet columns was set to something other than "3".
* Fix: Group feeds created using version 4.0 would display an error and no posts if there was not also a related connected account from version 3.x.
* Fix: Re-added support for the `padding` shortcode option as it was removed from the settings page.
* Fix: Saving certain snippets of custom JavaScript or CSS was causing the custom code to break.
* Fix: Event date format setting was not applying the correct date format to feeds and had the wrong choices for formats in the customizer.
* Fix: Added a message to let users know if all sources were removed and using the feed customizer.
* Fix: Fixed post layouts for events always showing full width in the customizer.
* Fix: Making changes to the source of legacy feeds would cause the wrong access token to be used to collect new posts.

= 4.0 =
* Important: Minimum supported WordPress version has been raised from 3.0 to 4.1.
* New: Our biggest update ever! We've completely redesigned the plugin settings from head to toe to make it easier to create, manage, and customize your Facebook feeds.
* New: All your feeds are now displayed in one place on the "All Feeds" page. This shows a list of any existing (legacy) feeds and any new ones that you create. Note: If you updated from a version prior to v3.19 then you may need to view your feeds on your webpage so that the plugin can locate them and list them here.
* New: Easily edit individual feed settings for new feeds instead of cumbersome shortcode options.
* New: It's now much easier to create feeds. Just click "Add New", select your feed type, connect your account, and you're done!
* New: Brand new feed customizer. We've completely redesigned feed customization from the ground up, reorganizing the settings to make them easier to find.
* New: Live Feed Preview. You can now see changes you make to your feeds in real time, right in the settings page. Easily preview them on desktop, tablet, and mobile sizes.
* New: Improved integrations with [our extensions](https://smashballoon.com/custom-facebook-feed/extensions/). You can now easily create advanced feed types and utilize our extensions to add even more advanced features.
* New: Elementor and WordPress widgets. We've added new Elementor and WordPress widgets to make it easier to embed your feeds.
* New: Added a Masonry layout option for all feed types, so you can now display your Photo, Album, and Video feeds in a flexible masonry layout.
* New: Color Scheme option. It's now easier than ever to change colors across your feed without needing to adjust individual color settings. Just set a color scheme to effortlessly change colors across your entire feed.
* New: You can now change the number of columns in your feed across desktop, tablet, and mobile.
* New: Easily import and export feed settings to make it simple to move feeds across sites.

= 3.19.5 =
* Tweak: Improved caching of resized images for groups to ensure posts in the persistent cache can display images successfully once image URLs expire.
* Tweak: The plugin now uses SVGs for icons instead of Font Awesome to prevent loading of the Font Awesome file.
* Fix: Fixed a bug related to connected accounts in the [Social Wall Plugin](https://smashballoon.com/social-wall/).
* Fix: Fixed an issue where avatars were not displaying for all group members who had authorized our plugin.
* Fix: Added support for the new LiteSpeed API to ensure the cache is successfully cleared with the latest version of the LiteSpeed plugin.
* Fix: Fixed an issue related to the GDPR Cookie Consent plugin by WebToffee where script blocking was causing an issue after consent was granted by the user.
* Fix: Added support for updated cookie consent names in the GDPR Cookie Consent plugin.
* Fix: Fixed a bug with Vimeo video URLs caused by Facebook API changes.
* Fix: Fixed an issue with message tags not properly displaying.
* Fix: Improved Access Token escaping.
* Fix: Fixed an issue that could cause the wrong photos to be displayed in the lightbox when switching between albums.
* Fix: Fixed a layout issue in the [Album extension](https://smashballoon.com/extensions/album/) when the Feed Columns setting was set to be more than 1.

= 3.19.4 =
* Fix: Fixed a bug with group videos not displaying for some feeds.
* Fix: Fixed an issue with past events in group feeds due to a Facebook API change which removed the time_filter field.

= 3.19.3 =
* New: In this update, the plugin will now use a persistent cache to store your Facebook group posts. This will help minimize any affects of a Facebook API change on May 25th which will only allow Facebook Groups to retrieve content from the past 90 days. The plugin will store your group posts in the persistent cache so that those posts can continue to be displayed beyond 90 days. This update will also make a one-time request to get the last 100 posts from your group and store those too, so that the affect of this change will be minimal for our users. Please [see here](https://smashballoon.com/doc/facebook-api-change-limits-groups-to-90-days/) for more information.
* Tweak: The date format selected is now also applied to the comment date.
* Tweak: Updated jQuery methods in preparation for jQuery migrate removal in an upcoming WordPress core update.
* Tweak: Added option to enqueue CSS and JS files only when shortcode is on the page.
* Tweak: Added an reset error log button to the settings page.
* Tweak: Added an option to hide the call-to-action button in the post content.
* Fix: Changed how access tokens are retrieved to prevent conflict with the "Rank Math SEO" plugin when connecting an account.
* Fix: Fixed an error in the Feed Finder tool when removing accounts.
* Fix: Fixed an issue with duplicated records in the feed finder.
* Fix: Fixed "Unknown error" response with API story tag calls.
* Fix: Fixed miscellaneous feed display and notices issues.

= 3.19.2 =
* Fix: Fixes an issue with masonry being inadvertently applied to the grid layout.
* Fix: Fixes a layout issue when the number of columns were set to 5 in a timeline feed.
* Fix: Added a fix for apostrophes in connected accounts.
* Fix: Fixed an issue with filtering Facebook posts using our [Social Wall](https://smashballoon.com/social-wall/) add-on.
* Fix: Fixed an issue with the lightbox in an album feed due to a JavaScript error caused by a GDPR plugin integration issue.

= 3.19.1 =
* Fix: Fixes an issue with masonry being inadvertently applied to the grid layout.
* Fix: Fixed an issue with the feed source being missing in the Feed Finder summary if set in a shortcode option.
* Fix: Added feed locations to the System Info to make finding a feed easier for the support team.
* Fix: Fixed an occassional PHP notice related to the new error reporting system.
* Fix: Fixed a PHP warning related to the "array_diff()" function which would sometimes occur when loading more posts in grid feeds.

= 3.19 =
* New: The locations of the Facebook feeds on your site will now be logged and listed on a single page for easier management. After this feature has been active for awhile, a "Feed Finder" link will appear under your connected accounts on the plugin Settings page which allows you to see a list of all feeds on your site along with their locations.
* New: Improved the error reporting system to make resolving issues easier.
* Tweak: Due to an API bug which Facebook hasn't resolved yet that affects buy/sell posts in groups, we have added a 'salesposts' shortcode option which will work around the issue until they fix it. If you are displaying posts from a group and receiving an "API Error 100" notice then adding salesposts=true to your shortcode and clearing the plugin cache should resolve the issue.
* Tweak: Added an ISO 8601 standard date format option for post dates and event dates.
* Fix: Fixed an issue with the caption not always being updated successfuly in the lightbox when using the Album extension.
* Fix: Resolved an issue with the Reviews extension not being compatible with the latest update when filtering reviews.
* Fix: Fixed an image with certain symbols within the alt text of an image breaking images in some themes.
* Fix: The lightbox would sometimes display empty white space above the caption.
* Fix: Fixed an issue with the path to the events placeholder image - which is used if an event doesn't have an image - not being correct.
* Fix: The 'before date' and 'after date' text was not being displayed in all circumstances.
* Fix: Fixed a layout issue with the posts and Like Box which occurred when using a multi-column layout and applying feed padding.
* Fix: Disabling the "View on Facebook" link would also hide the "Share" link.
* Fix: Fixed a JavaScript error in the lightbox which would sometimes occur when viewing an album.
* Fix: When the avatar for a post couldn't be displayed a broken image icon was being shown over the placeholder.

= 3.18.2 =
* Fix: Fixed a number of issues related to the Facebook Like Box widget not displaying, being the incorrect height, or causing a layout issue with multi-column feeds.
* Fix: The "Reset Resized Images" button not working successfully.
* Fix: Fixed a PHP error which would occur due to the "cff_get_utc_offset" function being undefined.

= 3.18.1 =
* Fix: Fixed an error that was occurring if the advanced "Request Method" setting was set to explicitly be "WP HTTP".
* Fix: Fixed an issue with the JavaScript file not loading from the correct location if the "Are you using an Ajax powered theme" setting was enabled.

= 3.18 =
* New: The plugin code has been completely refactored to improve performance and maintainability, and to make it easier to add new features. If you experience any issues with this update then please open a support ticket [here](https://smashballoon.com/custom-facebook-feed/support/) so that we can address it right away. Thank you!
* Tweak: Improved the caching system for post comments and reactions data so that it's faster and more reliable.
* Note: The minimum supported PHP version has been increased to PHP version 5.6. If you are using a lower version then a notice will be displayed with a button to revert back to the previous version.

= 3.17.1 =
* Fix: Fixed an issue with YouTube and Vimeo embeds due to a Facebook API change which was causing them to display as links instead of videos.
* Fix: Fixed an issue with the integration with the Complianz plugin.

= 3.17 =
* New: Integrations with popular GDPR cookie consent solutions added. See the GDPR setting in the following location for more information: Facebook Feed > Customize > Misc > GDPR.
* Tweak: Added Litespeed Cache to page cache plugins that will clear when Facebook Feed retrieves new posts.
* Tweak: For album feeds, lightbox navigation will be disabled until all photos in the album are done loading.

= 3.16.1 =
* Tweak: Minor frontend CSS improvements.
* Tweak: Added support for improved notices on the plugin settings page.
* Fix: Added image resizing information to the plugin System Info to help with debugging issues.

= 3.16 =
* New: Added support for Facebook oEmbeds. When you share a link to a Facebook post or video, WordPress automatically converts it into an embedded Facebook post for you (an "oEmbed"). However, on October 24, 2020, WordPress is discontinuing support for Facebook oEmbeds and so any existing or new embeds will no longer work. Don't worry though, we have your back! This update adds support for Facebook oEmbeds and so, after updating, the Custom Facebook Feed plugin will automatically keep your oEmbeds working. It will also power any new oEmbeds you post going forward.

= 3.15.2 =
* Fix: Fixed an issue with image resizing when using the full-width layout which caused images to display at a smaller size.
* Fix: Fixed an issue when there are multiple album, photo, or video feeds on the same page where loading more posts would affect posts in other feeds.
* Fix: Fixed an issue where the lightbox caption in album feeds would be displayed incorrectly if an item had no caption.
* Fix: Fixed a JavaScript error which would occur on the settings page if single or double quotes were included in any fields when manually connecting an account.

= 3.15.1 =
* Fix: Fixed an issue with the "account" shortcode option not working for some accounts.
* Fix: Fixed an issue with the popup modal when connecting a group.
* Fix: Don't display an admin error notice if an empty data array is returned by Facebook for an events feed.
* Fix: Added a rare issue when using the "offset" setting and the masonry layout to display reviews using the [Reviews extension](https://smashballoon.com/extensions/reviews/).

= 3.15 =
* New: Added compatibility with our new [Social Wall](http://smashballoon.com/social-wall/?utm_source=plugin-pro&utm_campaign=cff&utm_medium=readme) plugin, which allows you to combine feeds from Instagram, Facebook, Twitter, and YouTube into one social media "wall". If you are using our Smash Balloon All-Access Bundle then the Social Wall plugin is included at no additional cost. Just log into your account to download and install the plugin.
* Fix: Added a workaround for an issue caused by lazy-loading plugins which sometimes resulted in blank images in the feed.
* Fix: Fixed an issue with the Like Box widget not displaying if it's width was set to a fractional pixel.
* Fix: Iframes would be hidden in the lightbox in some themes.
* Fix: Fixed display issues caused by screen reader text for some feed types.
* Fix: Fixed PHP warning related to a deprecated use of the "implode" function.
* Fix: Fixed empty item in the carousel when using a carousel layout with the image resizing feature.

= 3.14.2 =
* Fix: Fixed a fatal PHP error occurring for some users.
* Fix: Removed stray "section" closing tag which was causing a layout issue in some themes and inadvertently displayed the PPCA notice.

= 3.14.1 =
* Fix: Added a fallback for the image resizing feature added in v3.14 in case JavaScript is not working on the page
* Fix: The visual header option now works with the [Multifeed extension](https://smashballoon.com/extensions/multifeed/) and will display the header information for the first Facebook page specified in the settings or shortcode.
* Fix: Fixed some stray PHP notices if the Multifeed extension is being used due to the PPCA error check in the previous update.

= 3.14 =
* Important: Due to upcoming Facebook API changes on September 4, 2020, it will only be possible to display feeds from Facebook pages which you are an admin of. If a PPCA Error notice is displayed above your feed then this change will affect one or more of your feeds. For more information about this change, please [see here](https://smashballoon.com/facebook-api-changes-september-4-2020/).
* New: As Facebook only provides large image sizes in their API for timeline feeds then it often results in images being displayed in your feed which are much larger than needed. With this update, the plugin will now create resized versions of the larger images and store them locally on your server. It will then detect the width of your feed and select the optimal sized images to display. Storing resized images is enabled by default but can be disabled in the following location: Facebook Feed > Customize > Misc > Image Resizing.  The plugin uses sizes of 130px, 250px, 400px, and 720px, but this can be customized by using the [cff_resized_image_sizes](https://smashballoon.com/specifying-exact-image-sizes-using-the-cff_resized_image_sizes-filter/) filter.
* New: For other feed types, the plugin will now choose the optimal image size from Facebook's CDN and display it in your feed, helping to reduce the page weight and increase load time.
* New: Added support for images in Marketplace posts in group feeds.
* New: Added support for videos created using Facebook Canvas
* Tweak: Added the "pagetype" setting to the shortcode when clicking the "Add to another feed" button on the Settings page to ensure it's set correctly
* Fix: Fixed a minor compatibility issue with the upcoming WordPress 5.5 release
* Fix: Unpublished videos were being displayed in the feed when showing a feed of just video posts. These are now hidden until published.
* Fix: An API request was being made to get the data for the visual header even if the header wasn't being displayed in the feed.
* Fix: Removed the see more/less links from post text when using the email share link.
* Fix: Fixed an issue with the lighbox showing incorrect images if two posts in the same feed had the exact same Facebook image in them.
* Fix: Fixed an bug where images inside an album would not be displayed at full size.

= 3.13.1 =
* Fix: New JavaScript code was not being applied for new visual header layout setting due to caching of old JavaScript file.
* Fix: Carousel extension was not working in version 3.13.
* Fix: Removed screen reader text that was causing layout issues.
* Fix: Disabled the "About Us" page plugin installation if using a version of WordPress earlier than 4.6.

= 3.13 =
* New: Added a new visual header option which displays the cover photo, avatar, page name, bio, and number of likes from your Facebook page at the top of your feeds. To enable the header, go to Facebook Feed > Customize > General > Header > Header Type > Visual.
* New: Added a setting to display a different number of posts on mobile devices vs desktop. While on the configure tab, check the box "Show different number for mobile" to reveal the setting.
* New: You can now use the "colsmobile" shortcode setting to set the number of columns on mobile for any feed type, not just timeline feeds. Eg: colsmobile=3.
* New: Lightbox images and videos can be changed by swiping right and swiping left when using a touch device.
* New: Added a PHP filter "cff_post_text" to change the post text before outputting it in the feed HTML.
* New: To help us improve the plugin we have added usage tracking so that we can understand what features and settings are being used, and which features matter to you the most. The plugin will send a report in the background once per week with your plugin settings and basic information about your website environment. No personal or sensitive data is collected (such as email addresses, Facebook account information, license keys, etc). You can opt-out by simply disabling the setting at: Facebook Feed > Customize > Misc > Enable Usage Tracking. See [here](https://smashballoon.com/custom-facebook-feed/docs/usage-tracking/) for more information.
* New: Added capability "manage_custom_facebook_feed_options". Users with this capability can make changes to Facebook Feed settings and view admin only messages.
* Fix: Fixed PHP warning when link did not contain a forward slash.
* Tweak: AJAX calls now use admin-ajax.php for better compatibility with some security plugins and settings.

= 3.12.1 =
* Fix: Fixed an error occurring for Group feeds due to a change in the Facebook API.
* Fix: Some connection error notices were not clearing automatically.
* Fix: Facebook Feed Gutenberg block causing an error when added to the editor and not rendering.
* Tweak: Now shows multiple event times for past events if applicable.

= 3.12 =
* New: Email alerts for critical issues. If there's an issue with a Facebook feed on your website which hasn't been resolved yet then you'll receive an email notification to let you know. This is sent once per week until the issue is resolved. These emails can be disabled by using the following setting: Facebook Feed > Customize > Misc > Feed Issue Email Report.
* New: Admin notifications for critical issues. If there is an error with the feed, admins will see notices in the dashboard and on the front-end of the site along with instructions on how to resolve the issue. Front-end admin notifications can be disabled by using the following setting: Facebook Feed > Customize > Misc > Disable Admin Error Notice.
* New: Added a WordPress 'Site Health' integration. If there is a critical error with your feeds, it will now be flagged in the site health page.
* New: Added "About Us" page for those who would like to learn more about Smash Balloon and our other products. Go to Facebook Feed -> About Us in the dashboard.

= 3.11 =
* New: Added a "Custom Facebook Feed" Gutenberg block to use in the block editor, allowing you to easily add a feed to posts and pages. To use the block, open the Gutenberg editor and click the + button in the top left. Search for "Custom Facebook Feed" and then select the block to insert it.

= 3.10.9 =
* Fix: Added a workaround for a Facebook API bug which was causing some live videos to be displayed very small
* Fix: Fixed an issue where the link to Facebook inside the popup lightbox for live videos was not working successfully
* Fix: Added compatibility for lazy loading plugins which would sometimes cause images in the feed not to be displayed

= 3.10.8 =
* Fix: Comment counts weren't displaying accurately for some feeds due to a Facebook API bug. Added a workaround until the bug is resolved by Facebook.
* Fix: Events in your timeline can now be filtered using words or hashtags in the event name, location, address, or description.

= 3.10.7 =
* Tested with upcoming WordPress 5.4 update
* Tweak: Updated Facebook API calls
* Fix: Minor bug fixes

= 3.10.6 =
* Fix: Added a workaround for a Facebook API bug which caused some feeds to display an "Unknown Error" message. If you are experiencing this issue then please click "Save Changes & Clear Cache" on the plugin Settings page in WordPress.
* Fix: Fixed an issue if multiple videos used the exact same thumbnail image file which caused an issue displaying the video in the lightbox
* Fix: Fixed an issue with event feeds if the Facebook page had more than 200 upcoming events

= 3.10.5 =
* Tweak: Added 'rel="noopener"' to all external links and added 'rel="noreferrer"' to all non-Facebook links. Thanks to Dev VIP for the suggestion.
* Tweak: Added a header with album details to Album extension feeds. Requires [Album extension](https://smashballoon.com/extensions/album/) v1.0.4.
* Tweak: When reconnecting an account on the settings page, if there's an issue with the existing access token then it'll be automatically replaced.
* Fix: Fixed a bug when showing a timeline feed containing only event posts where they would be missing some content.
* Fix: Fixed an issue when viewing the lightbox for a post which contained multiple photos where the caption was only displayed for the first post.
* Fix: Fixed a JavaScript conflict with the [Forminator](https://wordpress.org/plugins/forminator/) plugin.

= 3.10.4 =
* Fix: Fixed a JavaScript error in the admin caused by the previous update. Apologies for any inconvenience.

= 3.10.3 =
* Fix: Fixed an issue with event timezones if the event contained multiple event times
* Fix: Fixed an admin JavaScript error if the Facebook Page ID contained invalid characters

= 3.10.2 =
* New: Added a setting to choose to exclude "supporter only" posts. This can be set in the following location: Facebook Feed > Customize > Misc > Advanced > Hide "supporter only" posts.
* Fix: Fixed an issue with post and event date timezones due to changes in the WordPress 5.3 update
* Fix: Fixed a rare issue where a JavaScript error would occur in the WordPress admin if a Facebook account was manually connected and the Page ID used was the full URL
* Fix: Fixed a rare JavaScript error in the admin when using older web browsers
* Fix: Fixed occasional PHP notices caused by some private group posts
* Tweak: Improved the manual account connection process
* Tweak: Some minor UI tweaks to match the new WordPress 5.3 UI style

= 3.10.1 =
* Fix: Fixed an issue where shared links with multiple images weren't being displayed in an image slider as expected due to Facebook changing the fields in the API response
* Fix: If there's a server configuration issue and the site can't connect to Facebook then no error message would be displayed to the admin due to a change in the last update.

= 3.10 =
* New: Added a backup cache so the feed and comments will still display even if there's an error from the Facebook API.
* New: You can now easily manage multiple page or group accounts on the plugin settings page allowing you to easily add them to other feeds on your site.  When you connect a page or group you will now see it listed in the "Connected Accounts" section. You can add it to the primary feed or to another feed by using the new `account` shortcode option.
* New: Added a new setting to select a size for the shared link image, small square (130px), large square, or full. This can be found at: Customize > Misc > Media, or set using the `linkimagesize` shortcode option.
* Tweak: Added the link source URL below the title for shared link posts
* Tweak: Added a filter which can be used to filter the API data when returned; `cff_filter_api_data`.
* Tweak: Made filter/exfilter not case-sensitive
* Tweak: Updated to use v4.0 of the Facebook API
* Tweak: Removed legacy code for getting full size images
* Fix: Added new shortcode `groupprivacy` which if set to 'closed' or 'secret' will switch to use a HTML video player as videos from Closed and Secret groups can't be played using the Facebook Video Player widget.
* Fix: Fixed an issue with photos and albums in group posts sometimes not displaying correctly
* Fix: Some themes would prevent the "Share" link from working successfully
* Fix: When SVGs are disabled the SVG code at the top of the feed is completely excluded
* Fix: Fixed an issue with some @tag links in post text due to Facebook API change
* Fix: Fixed an issue with the welcome page displaying blank on update when disabled

= 3.9.1 =
* Fix: Fixed an issue with some Spotify player embeds not displaying successfully
* Fix: Fixed a sporadic issue with photos/videos in Group feed posts
* Fix: Fixed an issue where a lists of groups or pages wouldn't be displayed when retrieving an Access Token due to a server configuration issue
* Fix: Fixed an issue with the lightbox caption when displaying both a regular feed and event feed on the same page
* Fix: Fixed an occasional layout issue with "like" information inside the comments box
* Fix: Added a check for empty "Message page" links
* Fix: When using the Reviews extension the number of posts wasn't being respected in masonry grid layouts
* Fix: Removed all fallback Access Tokens
* Tweak: Now uses the visitor_posts endpoint to display visitor post feeds
* Tweak: Review feeds now hide negative recommendations by default

= 3.9 =
* New: Facebook Groups are making their long-awaited return to the plugin! You can now display timeline posts, events, videos, and albums from groups that you've added our app to'. Just click the 'Log in and get my Access Token' button on the plugin's Settings page and select 'Facebook Group'. Follow the prompts to connect your Group and display your feed.
* New: The plugin now supports Spotify player embeds and will automatically create a player inside your post when you share a Spotify link.
* Fix: The JavaScript for the multi-column layout is now only loaded if a multi-column layout is actually being used on the page
* Fix: Now displays the full event text in the popup lightbox
* Fix: Fixed an issue with images shared from Instagram sometimes not being displayed
* Fix: Fixed an issue with grid feed cursor pagination method when the post limit was set to be higher than the number of posts in the feed
* Fix: If using the Multifeed extension the plugin will now ignore bad Facebook IDs and still display the posts from other IDs
* Fix: Added a class to the "No Facebook ID" notice
* Fix: Fixed a bug where double hashtag symbols wouldn't work in the filter settings
* Tweak: Displays a helpful notice if there's an issue activating the license key
* Tweak: Made changes to the license renewal notification notice
* Tweak: Added a link to the bottom of the "Welcome" update page to disable it
* Tweak: Added classes to the event location information so that they can be targeted with CSS

= 3.8.2 =
* Fix: Fixed an issue with some feeds not displaying when a singular post type was selected and the source was set to be the timeline
* Fix: Fixed an issue with photos in the Album extension not displaying under some circumstances
* Fix: Removed "http://" from telephone numbers in "Call Now" buttons

= 3.8.1 =
* Fix: Fixed an issue causing a possible PHP notice if no subattachments were found for an album post
* Fix: Fixed an issue with call-to-action button links which didn't contain a http protocol at the start of them

= 3.8 =
* New: Support for v3.3 of the Facebook API
* New: Added a "Feed Columns" setting to allow you to display your timeline feed in multiple columns. This can be found under the "General" tab on the "Customize" page, or by using the 'cols' and 'colsmobile' shortcode settings.
* Tweak: Compacted the styling of the comment box icons when posts are displayed in a narrow column
* Fix: Fixed an issue with some call-to-action link URLs when a link protocol wasn't included
* Fix: Added a fix for filtering using Chinese or other foreign characters
* Fix: If the Like Box width was set in percentage then it caused an error in the Like Box widget
* Fix: Fixed a rare PHP error which would occur on install
* Fix: Fixed an issue with the welcome page layout in WordPress 5.2+

= 3.7.3 =
* New: Added support for "Get Directions" buttons in posts. This text can be translated using the following setting: Facebook Feed > Customize > Custom Text/Translate > Call-to-action Buttons.
* Fix: Accessibility fixes and improvements
* Fix: Fixed an issue caused when the "exfilter" setting ended in a comma
* Fix: Fixed an issue when using parentheses in the filter settings
* Fix: The Access Token selection area was being cut off on small screens when a lot of Facebook pages were listed
* Fix: Increased the z-index of the multiple event time drop down so it's displayed above all other elements
* Tweak: Individual Facebook API requests for avatars have now been removed and bundled into the main API request
* Tweak: Changed the pagination for past event feeds so that it retrieves posts in batches

= 3.7.2 =
* Fix: Fixed an issue caused by spaces between entries in the filtering settings for some feed types
* Fix: When events from multiple different timezones were displayed then the end time would sometimes be incorrect
* Fix: Added a class to the Like Box which was removed in the previous update as it broke the code snippet for centering it
* Fix: Fixed a PHP notice which would sometimes display in album feeds
* Fix: Fixed an issue when choosing to load the Font Awesome icon font locally instead of from the CDN
* Tweak: When viewing an event in the lightbox all of the event information is now included
* Tweak: Removed the Google+ share option as the platform has been deprecated

= 3.7.1 =
* Fix: Fixed an issue with the filter setting not working correctly with hashtags
* Fix: Fixed an issue with linked tags in the post text displaying incorrectly in some languages
* Fix: Added support for v1.1 of the Reviews extension

= 3.7 =
= MAJOR UPDATE =
* New: We've updated the icons used in the comments box at the bottom of each post. Icons are now SVGs which allows them to appear much sharper on retina displays and are much more versatile. To switch back to the old icons use the following setting: Customize > Misc > Misc Settings > Disable SVG icons.
* New: Icons are now displayed in color when the comments box is opened.
* New: Added support for video playlists. To display a playlist simply add the playlist ID to the shortcode, like so: [custom-facebook-feed type=videos playlist="1234567890"]. The playlist ID can be found in the video URL after the "vl.", eg: .../videos/vl.1234567890/.
* New: Added support for multi-image shared link cards. If a shared link post has multiple shared link images then a slider is added to allow the user to scroll through them.
* New: Added settings to customize the colors in the lightbox. These can be found at: Customize > General > Lightbox.
* New: You can now select between a "Regular" or "Boxed" post style. Settings for this can be found at: Customize > Style Posts > Post Item. A Box Shadow setting has been added to the "Boxed" post style.
* New: Added some settings to control the size and color of the shared link URLs and descriptions. These can be found at: Customize > Style Posts > Shared Link Boxes.
* New: Improved the Access Token retrieval process to make it more intuitive.
* New: Added a setting to select between either small (130px) or large (720px) images to be used in timeline posts. This can be found at: Customize > Misc > Media > Post image size.

* Tweak: Avatar images are now circular to match Facebook.
* Tweak: Images in photo, album, and video grid feeds are now faded in when loaded.
* Tweak: Moved lightbox arrows outside of the lightbox container.
* Tweak: Made visual improvements to the popup lightbox.
* Tweak: Added a custom scrollbar to the lightbox in Webkit browsers.
* Tweak: Made the lightbox video player larger when possible for timeline video posts.
* Tweak: Changed the way the Like Box loads to avoid a conflict with the Facebook Messenger widget.
* Tweak: Photo and album grid feeds no longer use image redirects.
* Tweak: Changed the elements used for icons from <i> to <span> to aid accessibility.
* Tweak: Made some visual styling changes to the comments box.
* Tweak: Animated the social media icons when the "Share" button is clicked.
* Tweak: Updated API versions in all API calls.
* Tweak: If using the Multifeed extension and the post limit isn't set then automatically calculate it to prevent retrieving far more posts than necessary.
* Tweak: Added directions on how to easily renew an Events Access Token: https://smashballoon.com/renewing-an-events-access-token/
* Tweak: Removed the share widgets from the footer of the admin so that they're only loaded when the "Share the plugin" button is clicked.

* Fix: Fixed a security bug related to the API call used to get post meta data.
* Fix: Fixed an issue with comment avatars not being displayed even when using a Page Access Token due to a recent Facebook API change.
* Fix: If a valid link isn't available for a user who comments on a post then their name is no longer linked.
* Fix: Uses the same Font Awesome handle as our other plugins so that they can share resources.
* Fix: The filter setting no longer matches partial words.
* Fix: The plugin no longer displays duplicate videos.
* Fix: Added the "nofancybox" class to images so that the FancyBox lightbox isn't triggered if it's part of your theme or another plugin.
* Fix: Fixed two layout bugs caused when setting the post date position to be at the bottom of the post.
* Fix: Resolved some CSS validation issues caused when applied inline styles didn't contain any property values.
* Fix: Fixed a PHP notice which was displayed when using PHP 5.4 due to the ENT_HTML5 constant not being supported.
* Fix: Added titles to all iframes.
* Fix: Fixed a PHP error caused by the Featured Post extension when set to only show posts by the page owner.
* Fix: Fixed a rare issue with tags in post stories when the locale was set to be Greek.
* Fix: Fixed bug where lightbox comments wouldn't display when displaying a feed of only album posts from a timeline.

= 3.6.1 =
* New: Added settings to translate the Facebook call-to-action buttons – such as "Learn More", "Shop Now", and "Message Page". These can be found at: Facebook Feed > Customize > Custom Text/Translate
* Fix: Fixed an issue introduced in the previous update which prevented special characters from being used in the filtering settings
* Fix: Added some missing settings to the System Info

= 3.6 =
* New: Added full support for Notes in timeline feeds. If your timeline feed contains a note then the plugin will now get the content and image from the note and display it within the post.
* New: Added a setting that you can enable if you are displaying posts from a restricted (non-public) Facebook page. This is located at: Facebook Feed > Customize > Misc > Misc Settings > Is Facebook Page restricted?
* New: When a visitor posts to your page the plugin caches their avatar for 6 months. A button has now been added to allow you to clear the cache of these avatar images. This is located at: Facebook Feed > Customize > Misc > Misc Settings > Clear Avatar Cache
* Verified compatibility with WordPress 5.0 and Gutenburg
* Tweak: The Timezone setting can now be set in the shortcode. Eg: `timezone="America/Los_Angeles"`
* Fix: Fixed a security vulnerability
* Fix: If you backdate a post it will now be ordered correctly in your feed
* Fix: Fixed a formatting issue when a call-to-action button is included in a post
* Fix: Fixed an issue with post IDs in some posts which contained multiple images
* Fix: Fixed an issue where a JavaScript error would occur if the `cfflinkhashtags` variable wasn't defined on the page
* Fix: Fixed a theme conflict related to the Color Picker in the admins section
* Fix: Fixed an issue with the share button in the admin pages
* Fix: Other minor bug fixes

= 3.5.3 =
* Fix: Removed a PHP notice which would appear under certain circumstances
* Fix: Fixed an error in the System Info if the customize settings weren't saved successfully

= 3.5.2 =
* Fix: If a visitor posts to your page then their avatar will now be displayed again
* Fix: If an author profile link is unavailable then it will now link to the post instead
* Tweak: Removed minimum caching time if you are using a Page Access Token
* Tweak: Minor UI changes to the admin pages
* Tweak: Reduced some of the data in the System Info

= 3.5.1 =
* **Important:** If you are displaying a feed from a Facebook page which you are *not* an admin of then it is advised that you obtain a new Access Token in the plugin using the "Log in and get my Access Token" button. This will switch you from using the "SlickRemix" app to using our own "Smash Balloon" Facebook app which was recently approved by Facebook, and will prevent you from experiencing any potential interuptions in your feeds going forward. This will be the final time this is required.
* Fix: Fixed a rare issue caused by some themes including the JavaScript file incorrectly
* Fix: Fixed an issue with the fallback avatar image for visitor posts where the author image is not available

= 3.5 =
* Due to upcoming Facebook API changes it is now required that you use your own Access Token in the plugin. Just use the blue Facebook login button on the plugin settings page to obtain your token.
* Tweak: Display an admin notice to get your own Access Token if you are not currently using one
* Fix: Fixed an issue with the ordering of albums if they have been manually reordered on Facebook

= 3.4.2 =
* Tweak: Added basic support for job postings in the timeline feed, adding an "Apply Now" button
* Tweak: The audio in the lightbox video player is now unmuted by default
* Tweak: Updated System Info to be more readable
* Fix: Added a workaround for a Facebook API bug which was causing an issue displaying and playing some videos
* Fix: Fixed an issue where comment replies weren’t able to be opened in the lightbox
* Fix: Fixed an issue caused when a product was tagged in a post resulting in the product image being included
* Fix: Removed the App ID from the Like Box widget as it was causing an unnecessary message to be displayed in the browser console
* Fix: If there isn’t a Page ID set in the plugin settings then use a fallback ID in the System Info test request
* Fix: Fixed a PHP notice that would appear in certain rare circumstances

= 3.4.1 =
* Fix: Fixed an issue in album feeds where the photos thumbnails weren't showing inside the album lightbox
* Tweak: The title of a shared link can now also be filtered using the filter settings

= 3.4 =
* New: Now easily get your own Access Token by logging in through our plugin. Simply click the blue "Log in and get my Access Token" button on the plugin's settings page and connect your Facebook account to get your token. The Access Token will work to get posts from ANY Facebook page.
* Fix: Fixed an issue where Page Access Tokens weren't being used to get comments or photo attachments in the popup lightbox

= 3.3 =
* Update: It is now possible to display events from a page which you are an admin of. In order to do so, you must obtain a "Page" Access Token for the page you want to display events from by following the directions [here](https://smashballoon.com/custom-facebook-feed/page-token/).
* Tweak: You can now use `accesstoken` in the shortcode by itself without having to also use the `ownaccesstoken` setting
* Tweak: If you're using your own [Page Access Token](https://smashballoon.com/custom-facebook-feed/page-token/) to display your feed then it will now override the minimum caching times
* Tweak: Updated the plugin auto-updater class to the latest version
* Fix: Fixed an issue with the lightbox close button not working correctly
* Fix: Message and story tags weren't being linked due to a Facebook API change

= 3.2.12 =
* **Important:** If you are displaying posts from a Facebook page that **you are an admin of** then it is now highly recommended that you retrieve your own Access Token for that page to avoid any API rate limit errors. Simply follow these [step-by-step](https://smashballoon.com/custom-facebook-feed/page-token/) instructions to obtain one.

= 3.2.11 =
* Fix: Fixed an issue where Facebook API errors were being cached
* Fix: Fixed an issue with the System Info API test if using the Multifeed extension
* Fix: Fixed a JavaScript error caused by full size images not being returned for a post
* Fix: Removed the HTML5 video player setting due to Facebook deprecating the video source data field. The Facebook Video Player will be used instead.
* Tweak: Lightbox close button is now in the top right corner

= 3.2.10 =
* Fix: It's now possible to filter videos by name and albums by description
* Fix: Fixed an issue where shared posts with multiple photo attachments wouldn't display the additional photos
* Fix: Replaced all HTTP links with HTTPS to prevent mixed content issues
* Fix: Fixed a rare issue which affected the layout of other Facebook widgets
* Fix: The API response test in the System Info now only tests with your token if you have the "Use my own Access Token" setting enabled
* Fix: Automatically remove slashes at the end of the Page ID as it caused an error
* Fix: Fixed an issue which caused an occassional API rate limit error
* Tweak: Added a button to the License page which allows you to test the license activation connection

= 3.2.9 =
* Fix: Fixed an issue connecting to the Facebook API caused by a recent Facebook platform change
* Tweak: Increased the minimum caching time to be 15 minutes to reduce Facebook API requests

= 3.2.8 =
* Important: Due to sudden changes in the Facebook API it is no longer possible to display posts from a Facebook Group. Please [see here](https://smashballoon.com/facebook-api-changes-april-4-2018/) for more information. We apologize for any frustration or inconvenience this has caused.
* Removed: Due to Facebook API restrictions, it is no longer possible to display information about an event when it is posted or shared to your Facebook Page timeline.
* Removed: Due to Facebook API restrictions, it is no longer possible to display an individual event using the [Featured Post extension](https://smashballoon.com/extensions/featured-post/)

= 3.2.7 =
* Tweak: In video feeds the video title now also opens the popup lightbox instead of linking to Facebook
* Tweak: The [Date Range](https://smashballoon.com/extensions/date-range/) and [Album](https://smashballoon.com/extensions/album/) extension are now compatible, allowing you to display pictures from a specific album within a certain date range
* Fix: Prevented some PHP 7.1 notices which displayed under rare circumstances
* Fix: Added a fallback in case the plugin can't access the Access Token in the database when loading data dynamically
* Fix: Fixed a PHP notice which appeared if the album post type setting wasn't saved successfully
* Fix: Fixed a rare issue which prevented the text from being shortened in shared events with certain themes
* Fix: Removed a function that's being deprecated in PHP 7.2
* Fix: If the source of a HTML5 video isn't available then automatically switch to the Facebook Video Player instead
* Fix: Recurring event times are now displayed for group events
* Fix: Fixed an issue with the size of the video play button when Font Awesome 5 was included on the site

= 3.2.6.1 =
* Fix: Fixed an issue with group feeds not displaying in the previous update

= 3.2.6 =
* Tweak: Updated the loading symbol in the lightbox to match the Load More button
* Tweak: Included a fallback in case the author name and avatar aren't available in visitor posts
* Tweak: When an extension is activated it is now shown as activated on the plugin's Extensions page
* Fix: Fixed an issue with the icon font when Font Awesome 5 was added to a site
* Fix: Removed the .htaccess file from the plugin as it was causing an issue with Apache 2.4
* Fix: Reverted API version to 2.8 due to an issue in version 2.11
* Fix: Fixed a rare issue where the Like Box wouldn't be clickable
* Fix: The comments box is no longer added to the page when the comments box and lightbox comments are both disabled
* Fix: Disabled all admin-ajax requests when Ajax caching is disabled
* Fix: Fixed rounded corner issue in lightbox when comments are enabled
* Fix: Removed an unused function

= 3.2.5.1 =
* Fix: Fixed a PHP notice in event feeds caused by the last update
* Tweak: Removed .htaccess file as it was causing an issue with the Apache 2.4 update

= 3.2.5 =
* Fix: Fixed an issue with comments not being displayed due to a recent Facebook API change. Due to the API update, there are some changes to how comments are now displayed. Please see [this FAQ](https://smashballoon.com/displaying-comment-names-avatars/) for more information.

= 3.2.4 =
* Tweak: If the avatar images are being blocked in the feed by a browser setting then the avatar alt text is hidden and a light grey square is displayed instead
* Fix: Fixed a PHP notice which would appear under the rare scenario that a YouTube video didn't contain any image data
* Fix: The new Facebook tagging format is now supported in all shared post descriptions
* Fix: If a post contains more than 12 image attachments the plugin was overlaying the incorrect number of additional images on top of the last image

= 3.2.3 =
* Fix: Fixed an issue caused by the recent Facebook v2.11 API udpate which meant that posts were sometimes missing when loading more
* Fix: Added "Interested" and "Attending" numbers to group event feeds
* Fix: Fixed an issue with events when using the [Carousel](https://smashballoon.com/extensions/carousel/) extension which caused the height of the carousel to be set to zero when the Load More button was enabled

= 3.2.2 =
* Fix: Fixed an issue with the order of group posts sometimes being incorrect after the 3.2 update

= 3.2.1 =
* Fix: Fixed some formatting issues caused by the 3.2 update

= 3.2 =
* New: Added support for recurring events. If an event had multiple recurring dates/times then these are now displayed and recurring events will stay in your upcoming events feed until the final event date has passed.
* Tweak: Removed the arrows icon which were displayed when hovering over a photo/video as they were redundant. To add an icon again, simply follow the directions in [this FAQ](https://smashballoon.com/snippet/display-icon-hovering-photo-video/).
* Tweak: Added `aria-hidden=true` to icons to help improve accessibility
* Tweak: When viewing a post or album in the lightbox then smaller image sizes are now used for the thumbnail images at the bottom to improve performance
* Tweak: Made error messages accessible by support staff to make troubleshooting easier
* Tweak: Added a Helsinki timezone
* Tweak: Added a class which disables the "Fancy box" lightbox on images in the Facebook feed which is sometimes applied by plugins or themes and interferes with the lightbox in our plugin
* Tweak: Added a .htaccess file to help prevent an issue where the plugin sometimes wasn't able to load it's own files using Ajax
* Fix: Added alt tags to shared link thumbnails
* Fix: Fixed some PHP notices which would appear under certain circumstances
* Fix: Fixed an issue with apostrophes in the header not being escaped correctly
* Fix: Added support for the @[ID:page-name] tagging format
* Fix: Fixed an issue where the multi-image layout wasn't working correctly in group posts with multiple images
* Fix: The HTML5 video element is now added dynamically to the lightbox to prevent issues with some themes/plugins that run a script on the video tag
* Fix: Fixed an issue where the number of likes/reactions wasn't always accurate for timeline posts which contain multiple images
* Fix: Added a setting to work around an issue with pagination in group feeds. Some posts would be missing when paginating due to the way Facebook orders group posts based on activity rather than date. If you're experiencing this issue you can navigate to the following setting: `Facebook Feed > Customize > Misc > Misc Settings > Timeline pagination method` and change it to be `API Paging`.
* Fix: Corrected an invalid CSS property
* Fix: Fixed an issue where some Access Tokens were able to be viewed in XHR responses

= 3.1 =
* New: Posts with multiple images will now display in a multi-image layout just like on Facebook. The new layout is enabled by default, but you can disable it by checking the following setting: Facebook Feed > Customize > Post Layout > Only show one image per post, or using the shortcode option: `oneimage=true`
* New: Added an option to use minified versions of the plugin CSS and JavaScript file: `Facebook Feed > Customize > Misc > Misc Settings > Minify CSS and JavaScript files`
* New: Added support for clearing the cache of major caching plugins when the Facebook feed cache is cleared. You can enable this by setting the following setting to be "Yes": `Facebook Feed > Customize > Misc > Misc Settings > Force cache to clear on interval`
* Tweak: When tapping an image in the feed on a mobile device it now immediately opens the lightbox rather than displaying the hover effect
* Tweak: If an event uses a street address instead of a "place" then the plugin still displays a map link
* Tweak: The icon font stylesheet handle has been renamed so it will only be loaded once if another of our plugins is installed
* Tweak: Only show the names of timeline videos when there is no post text included, otherwise the video name is often repeated
* Tweak: Reverted the bug fix workaround added in version 3.0.8 as it's no longer needed
* Tweak: Added a setting to workaround a theme issue that affects the shortening of the post text. If you're experiencing an issue with the shortening of post text then you can enable the following setting: `Facebook Feed > Customize > Misc > Misc Settings > Fix text shortening issue`
* Fix: Added a workaround for a some formatting issues caused by themes that use their own custom formatting function
* Fix: Added a workaround for themes that insert their own HTML elements around YouTube and Vimeo embeds
* Fix: Fixed a bug introduced in the last update where the lightbox would occasionally open the incorrect photo
* Fix: Fixed and issue with the locale setting not changing the post text language correctly due to caching
* Fix: Removed an unnecessary request made when displaying past events
* Fix: Corrected a wording issue with the post "story" text when sharing an event
* Fix: Fixed an issue where the number of likes weren't showing up correctly on some shared posts
* Fix: The post description is now able to be displayed even if the post text is hidden
* Fix: Fixed an issue related to lightbox captions in the Album extension
* Fix: Fixed an issue with loading more past events
* Fix: Fixed an issue with enabling extensions in the Smash version on PHP 7.1
* Fix: Removed a PHP notice caused by embedding Vimeo videos in PHP 7
* Fix: Dismissing the license renewal reminder notice would redirect to a blank page

= 3.0.8 =
* Fix: Fixed an issue caused by a Facebook API change which caused images in Photo and Album feeds not to be displayed

= 3.0.7 =
* Fix: Fixed an issue where in rare occasions YouTube and Vimeo videos would display the wrong video in the popup lightbox
* Fix: Fixed an issue where Custom JavaScript code wouldn't run in certain situations

= 3.0.6 =
* Fix: Custom JavaScript is now also run on new posts that are loaded into the feed
* Fix: Added hidden text to some empty links in order to meet WCAG 2.0 accessibility standards for screenreaders
* Fix: Fixed a couple of display issues in Internet Explorer 11
* Fix: Fixed a rare issue where sometimes posts were missing from the feed when loading more posts
* Fix: Changed a parameter name in one of the request to prevent triggering a Mod Security rule
* Fix: Fixed an issue where the names of people who liked a post weren't being displayed in the comments box for some post types
* Fix: Fixed an issue related to filtering out reviews using the Reviews extension
* Tweak: Further compressed the images to optimize filesize

= 3.0.5 =
* Tweak: No longer preload HTML5 video files to prevent unnecessary downloading
* Fix: Changed an attribute name so that it passes HTML validation
* Fix: Removed an unused function reference that was causing an error in rare cases

= 3.0.4 =
* Fix: Fixed an issue with the [Reviews](https://smashballoon.com/extensions/reviews/) extension where it wasn't displaying some reviews after the latest update
* Fix: Fixed an issue when displaying album feeds from a group where the photos in the lightbox weren't displaying correctly
* Fix: Fixed a rare issue where some feeds wouldn't update with new posts

= 3.0.3 =
* Fix: Fixed an issue where the comment reply text would display as "undefined" if a post didn't have any likes
* Fix: Fixed an issue where setting `loadmore=false` in the shortcode would cause too many events to display
* Fix: Fixed an issue with event image heights when using `eventimage=cropped` when the Load More button is enabled

= 3.0.2 =
* Fix: Fixed an issue with the custom text for the comment "reply" and "replies" text not working
* Fix: Fixed an issue with Live videos not being accessible on mobile during the live broadcast
* Fix: Fixed an issue where the post description text was displayed twice in a post if the "Link Text to Facebook Post" setting was enabled
* Fix: Removed some stray PHP notices that appeared when displaying past events
* Fix: Fixed an issue with the order of group posts when the Multifeed extension was activated
* Fix: Fixed an issue related to the lightbox display when using the Album extension
* Fix: Fixed an issue when using the Multifeed and Date Range extensions together to display events from multiple pages for a specific date range
* Fix: Removed extra quote from video player attributes

= 3.0.1 =
* Fix: Fixed an issue in the last update where the Like Box was still being displayed when it was disabled
* Fix: Fixed an issue where the custom text before and after the date weren't displaying

= 3.0 =
= MAJOR UPDATE =
* New: Added a "Load More" button to the bottom of each feed so that you can infinitely load more content into your feed.
* New: Timeline posts will now display reactions for each post, along with the likes, shares, and comments.
* New: Redesigned the popup lightbox to include comments, replies, and reactions, similar to on Facebook.
* New: Added a setting that allows you to check for new Facebook posts in the background when the cache expires so that the posts are already cached the next time the page is loaded.
* New: Switched from the basic browser video player to the Facebook embedded video player. Supports HD, 360, and Live videos.
* New: Full size images will now be displayed in the lightbox when available
* New: Added a setting for 5, 6, 7, and 8 columns in grid layouts
* New: Added some additional European date formats for the post and event dates
* New: Added "Interested" and "Going" counts to the bottom of each event. These can be hidden by using the following Custom CSS: `.cff-event-meta{ display: none; }`
* New: Added a Welcome Page with information about what's new in the update and a Getting Started guide for new users

* Tweak: Added the post story as part of the page/author name at the top of the post
* Tweak: When using the navigation arrows in the lightbox it will now go through each of the photos attached to the post rather than moving directly to the next post
* Tweak: Improved the text truncation so that it applies to both the post text and description as one block of text and accounts for HTML link tags
* Tweak: Reduced the amount of data that the plugin requests from Facebook initially and now dynamically loads in the additional data when it's needed
* Tweak: Post comments are now loaded dynamically using Ajax
* Tweak: The shared link post layout now automatically matches that of the post layout, unless disabled on the Misc settings tab
* Tweak: Posts that are hidden on Facebook are now also hidden in the plugin feed
* Tweak: Added an "Auto" option to the Post Limit setting where it will automatically adjust the post limit based on the number of posts you choose to display
* Tweak: There's no longer a limit to how far back the event offset setting can be used for
* Tweak: Updated the Font Awesome icon font to the latest version
* Tweak: Can now display albums from multiple Facebook pages in the same feed using the Multifeed extension
* Tweak: Consolidated most images into a sprite to reduce HTTP requests
* Tweak: Reorganized the settings pages to be more intuitive
* Tweak: Added a friendly reminder notice that pops up when your license key is due to expire

* Fix: Fixed an issue where post filtering was only applied to either the post story or main text, not both
* Fix: Fixed an issue with video names due to a Facebook API change
* Fix: Changed the z-index of the lightbox so that it's displayed over fixed navigation menus in themes
* Fix: Fixed a JavaScript error when there were no image attachments to display in the lightbox
* Fix: Fixed an issue where no posts would show if an empty space was accidentally left in the Filter setting
* Fix: Fixed an issue where the color setting wasn't being applied to the "Buy Tickets" link in events
* Fix: Fixed a PHP notice that appeared if a shared link didn't include a full size image
* Fix: Fixed an encoding issue in the lightbox captions
* Fix: Now automatically disables the Masonry layout mode for all grid feeds
* Fix: Fixed an ordering issue in the Multifeed extension if one of the IDs was for a group
* Fix: Fixed a layout issue when hovering over the thumbnails in the lightbox in the Genesis theme

=  2.6.8.1 =
* Fix: Fixed an issue introduced in the last update which caused photo feeds from Facebook Pages not to appear correctly

= 2.6.8 =
* Note: Due to Facebook deprecating version 2.0 of their API on August 8th, 2016, it will not longer be possible to display photo grid feeds from Facebook **Groups**. Photo grids from Facebook Pages will still work as normal.
* Tweak: The plugin will now show up to 100 image attachments at the bottom of the popup lightbox for each post rather than the previous limit of 12
* Tweak: Group wall feed posts are now ordered based on recent activity, rather than by the date they were created, to better reflect the order on the Facebook Group wall
* Tweak: Album feeds are now ordered based on when the last photo was added to an album, as they are on Facebook, rather than by when they were created
* Fix: Removed any dependencies on version 2.0 of the Facebook API
* Fix: Fixed an issue where line breaks in event descriptions weren't being displayed correctly when the HTML was being minimized
* Fix: Fixed a minor issue when using the keyboard to navigate through the popup lightbox
* Fix: When using a custom event date format the end date can now be automatically hidden when it ends on the same day as it starts
* Note: We're working hard on **version 3.0** which will be coming soon!

= 2.6.7 =
* Tweak: The "2 days ago" date format can now be translated via the shortcode
* Fix: Fixed an issue with video titles not displaying due to a Facebook API change
* Fix: The "post limit" setting is now working correctly in the video grid feed
* Fix: Fixed an issue with some keyboard keys incidentally launching the lightbox
* Fix: Fixed an issue with the font size not being applied to the post author text successfully in some themes
* Fix: The `likeboxcover` shortcode option is now correctly correctly
* Fix: The absolute path is no longer exposed in the page source code

= 2.6.6.3 =
* Updated to use the latest version of the Facebook API (2.6)
* Fix: Fixed an issue with the post URLs when sharing to Facebook
* Fix: Now using the Object ID in the post link for visitor posts as it's more reliable
* Fix: Fixed an issue with the event name sometimes displaying twice on timeline events
* Fix: Fixed an issue with the share link in the Facebook Like Box widget not working correctly
* Fix: Added support for proxy settings defined in the wp-config file
* Fix: When navigating through the lightbox using keyboard arrows the videos now stop playing as expected

= 2.6.6.2 =
* Fix: Fixed a JavaScript error in the admin area when using WordPress 4.5

= 2.6.6.1 =
* Fix: Fixed an issue with the Like Box not being displayed (unless a width was set) due to a recent Facebook change to the Like Box widget

= 2.6.6 =
* New: Added support for the [Reviews](https://smashballoon.com/extensions/reviews/) extension
* Tweak: Added settings from the Carousel, Masonry Columns, and Reviews extensions to the System Info
* Fix: Removed the Spanish .pot file as it isn't needed and was causing update issues occasionally
* Fix: Fixed a rare error related to strange link formats when the post text is linked

= 2.6.5.2 =
* Fix: Fixed some stray PHP notices that appeared if image attachments in the comments of a post didn't have a title
* Fix: Removed PHP notices that would appear when using the [Multifeed](https://smashballoon.com/extensions/multifeed/) extension if one of the Facebook pages wasn't public

= 2.6.5.1 =
* Fix: Fixed an issue where video titles weren't being displayed when displaying a video grid due to a Facebook API change
* Fix: Fixed an issue with the order of events when using the Multifeed extension
* Fix: Fixed an issue where the post offset setting wasn't working correctly with Multifeed events

= 2.6.5 =
* Tweak: Added the post text as the alt tag of the post images to help benefit SEO
* Fix: Fixed an issue caused by the Photon setting in the Jetpack plugin which caused some images not to display successfully. The plugin now includes an automatic workaround for this.
* Fix: Fixed an issue with the 'offset' setting not working for event feeds
* Fix: Increased the width of the Share popup to accomodate the new Google+ icon
* Fix: Fixed an issue where the Locale setting was not saving successfully on the settings page
* Fix: Fixed a problem where thumbnails weren't appearing in the popup lightbox when displaying albums from a group, even when using an "User" Access Token
* Fix: Fixed and issue where grids of videos wouldn't display when using a newly created Access Token due to a Facebook API change
* Fix: Fixed an issue with the thumbnail HTML formatting which sometimes occured when first opening the popup lightbox
* Fix: Fixed a rare issue with Ajax caching of the number of likes and comments
* Fix: Renamed a function to prevent conflicts
* Fix: Added a friendly error message if there is an error trying to retrieve events
* Fix: Added a friendly error message if trying to display group events without using a "User" Access Token

= 2.6.4 =
* Fix: Fixed an issue with Facebook group album cover photos not being displayed successfully due to a Facebook API change
* Fix: Fixed an issue with ajax caching
* Fix: Fixed an issue when events are displayed within the new [Carousel](https://smashballoon.com/extensions/carousel/) extension which caused duplicate empty items
* Fix: Fixed a margin issue in the new [Masonry Columns](https://smashballoon.com/extensions/masonry-columns/) extension when posts have a background color applied

= 2.6.3 =
* Fix: Fixed an issue with links not being formatted correctly in the lightbox caption
* Fix: Fixed an issue where some upcoming events weren't being displayed correctly for some Facebook pages

= 2.6.2 =
* Fix: Fixed an issue with events in the Date Range and Featured Post extensions
* Fix: Fixed an issue with some HTML code being displayed when photos were hidden from posts
* Fix: Squished a bug where HTML5 video controls weren't displaying when playing videos in a feed with the lightbox disabled

= 2.6.1 =
* Fix: Fixed an formatting issue in the last update which occurred with some themes

= 2.6 =
* New: Added support for two new extensions; [Carousel](https://smashballoon.com/extensions/carousel/) and [Masonry Columns](https://smashballoon.com/extensions/masonry-columns/)
* New: Added a 'Buy Tickets' link for event feeds
* New: Added a setting to allow you to use a fixed pixel width for the feed on desktop but switch to a 100% width responsive layout on mobile
* New: You can now click on the name of a setting on the admin pages to reveal the corresponding shortcode for that setting
* New: Added quick links to the top of the Customize settings pages to make it easier to find certain settings
* Tweak: Timeline events now use the layout select on the "Post Layout" settings page instead of always using the Thumbnail layout
* Tweak: The selected thumbnail is now highlighted in the pop-up lightbox
* Tweak: Event feeds now use the Graph API v2.5 instead of FQL in preparation for its deprecation this year
* Tweak: Updated the event placeholder image which is shown when an event doesn't have an image on Facebook
* Tweak: Moved a few of the settings to more logical locations
* Fix: Hashtag linking now works with all languages and character sets
* Fix: Caption text is now fully formatted in the pop-up lightbox for albums
* Fix: Fixed a bug which affected the photo/album grid layout when the Like Box was displayed at the top of the feed
* Fix: Fixed an issue where the Album extension wouldn't work if photos were selected as the only post type on the settings page
* Fix: Fixed an issue where the hyphen/dash wasn't hidden with the event end date when using a specific date format
* Fix: Fixed an issue with the height of photos in a grid when multiple grids were on the same page but with different numbers of columns
* Fix: Updated the icon font link to use SSL

= 2.5.15 =
* New: Events posted on your timeline will now show the full event cover photo and use whichever layout you select on the 'Post Layout' settings page
* Fix: Fixed an issue with messages tags in some posts when using an Access Tokens created using a Facebook API 2.5 app
* Fix: Added a maximum width to images in the comments
* Fix: Fixed an issue with group events and albums not displaying due to a change in the recent Facebook API 2.5 update
* Fix: Added a check so that the plugin JavaScript isn't run twice even if it's included twice in the page

= 2.5.14 =
* Fix: Fixed an issue where if you had the plugin set to display more than 93 posts then it would result in an error due to a change in the recent Facebook API 2.5 update which limits the total amount of posts that can be requested
* Fix: Added a check to the top of the plugin's JavaScript file so that it isn't run twice if included in the page more than once

= 2.5.13 =
* Fix: If you're experiencing an issue with your Facebook feed not automatically updating successfully then please update the plugin and enable the following setting: Custom Facebook Feed > Customize > Misc > Misc Settings > Force cache to clear on interval. If you set this setting to 'Yes' then it should force your plugin cache to clear either every hour, 12 hours, or 24 hours, depending on how often you have the plugin set to check Facebook for new posts.

= 2.5.12 =
* Fix: Fixed an issue caused by the recent Facebook API 2.5 update where some posts would display post tags incorrectly
* Fix: Fixed an issue where shared links without a title would produce a PHP notice

= 2.5.11 =
* Fix: Fixed a positioning issue with the Facebook "Like Box / Page Plugin" widget caused by a recent Facebook update which was causing it to overlap on top of other content
* Fix: Fixed an issue caused by the recent Facebook API 2.5 update where the posts wouldn't display when using a brand new Access Token
* Fix: Hashtags containing Chinese characters are now linked
* Fix: Fixed an issue where the photo lightbox was ocassionally intefering with other lightboxes used on a website
* Tweak: Videos in the video grid layout can now be filtered using the plugin's 'Filter' settings
* Tweak: Added a timezone for Sydney, Australia
* Tweak: Removed the 'Featured Post ID' field from the Settings page when the extension is in use, as it makes more sense to just set the ID directly in the shortcode

= 2.5.10 =
* Fix: Fixed an issue caused by the WordPress 4.3 update where feeds from longer page IDs wouldn't update correctly due to the cache not clearing when expired

= 2.5.9 =
* New: Added comments replies. If a comment has replies then a link is displayed beneath it which allows you to show them. The 'Reply' and 'Replies' text can be translated on the plugin's 'Custom Text / Translate' tab.
* Tweak: Added a setting which allows you to manually change the request method used to fetch Facebook posts which is necessary for some server setups
* Tweak: Added the ability to use the [Date Range](https://smashballoon.com/extensions/date-range/) extension with either album or video feeds
* Fix: Fixed an issue caused by the recent Facebook API 2.4 update where some group photos wouldn't display correctly
* Fix: Fixed a minor issue with shared link posts where the post text was set to be linked to the Facebook post it would link to the shared link URL instead

= 2.5.8 =
* Fix: Fixed an issue with album feeds not displaying when using some Access Tokens due to a recent change in the Facebook API

= 2.5.7 =
* Fix: Added a workaround for a [bug in the Facebook API](https://developers.facebook.com/bugs/486654544831076/) which is causing issues displaying events and photo feeds

= 2.5.6 =
* New: Added a couple of new customization options for the Facebook Like Box/Page Plugin which allow you to select a small/slim header for the Like Box and hide the call-to-action button (if available)
* Tweak: The post "story" can now be hidden independently of the rest of the post text. Just add the following to the plugin's Custom CSS section to hide the post story: `#cff .cff-story{ display: none; }`. The post story is the text at the beginning of the post which describes the post, such as 'Smash Balloon created an event'.
* Tweak: User avatars in the comments now use a headshot silhouette icon until the Facebook profile picture is loaded
* Tweak: When using the [Album extension](https://smashballoon.com/extensions/album/) to display photos the filter and exfilter options can now be used to hide or show photos based on a string or hashtag in the photo description
* Fix: The plugin now works with Access Tokens which use the new recently-released version 2.4 of the Facebook API
* Fix: Fixed an issue with links in the post text in the pop-up lightbox not working correctly
* Fix: Fixed an issue with some post tags caused by the recent Facebook API 2.4 update
* Fix: Fixed an issue with shared link thumbnails not being displayed in the Safari web browser

= 2.5.5 =
* New: Display a grid of your latest Facebook videos directly from your Facebook Videos page/album. To do this just select 'Videos' as the only post type in the 'Post Types' section of the Customize page, or use the following shortcode `[custom-facebook-feed type=videos videosource=videospage]`.
* New: If a Facebook post contains an interactive Flash object then it will now be shown in the pop-up lightbox and can be interacted with directly on your website
* Tweak: When displaying events a 'See More' link is now added to the event details so that it can be expanded if needed. The text character limit is controlled by the 'Maximum Description Length' setting, or the `desclength` shortcode option.
* Tweak: Automatically link the event name to the event now rather than it having to be enabled on the plugin's 'Typography' settings page
* Fix: Fixed an issue with photos or albums not displaying under rare circumstances when set as the only post type
* Fix: Removed empty style tags from some elements
* Fix: The URLs used for the 'Share' icons are now encoded to prevent HTML validation errors

= 2.5.4 =
* New: Photos in the comments are now displayed
* Tweak: Added stricter CSS rules to the paragraphs within comments to prevent styling conflicts
* Fix: Links within post descriptions weren't opening in a new tab
* Fix: Fixed an issue which would cause an Facebook API 100 error when an older Access Tokens was used

= 2.5.3 =
* New: Added an option to display the full-size images for shared link posts. This can be enabled at: Customize > Typography > Shared Links, or by using the following shortcode options: fulllinkimages=true
* New: The pop-up lightbox now contains the full text from the post and maintains all links and tags
* Tweak: Added video poster images back in so that all videos display an image initially before being played
* Tweak: When a post contains a Facebook video then move the name of the video to after the post story
* Fix: Hashtags which contain foreign chracters are now correctly linked
* Fix: Fixed an issue where photo attachments displayed in the pop-up lightbox would be displayed from the album the photos were added to, rather than from the post itself
* Fix: Fixed an issue which was causing the event details not to display for event posts on your timeline
* Fix: Removed some line breaks after the post text of some posts which was causing a gap
* Fix: Emjois in comment text are now displayed correctly inline if the theme supports them

= 2.5.2 =
* Fix: Fixed an issue where the additional photo thumbnails weren't appearing in the lightbox for some posts/albums

= 2.5.1 =
* Fix: Fixed an issue where the number of likes for some posts was displayed as zero
* Fix: Fixed an issue where the number of posts displayed was off by one

= 2.5 =
* New: Replace the 'Like Box' with the new Facebook 'Page Plugin' as the Like Box will be deprecated on June 23rd, 2015. Settings can be found under the Misc tab on the plugin's Customize page.
* Tweak: When displaying events, if there are no upcoming events then the message 'No upcoming events' is now displayed. This can be changed or translated on the plugin's 'Custom Text / Translate' settings page.
* Tweak: Now always displays the post "story" first in the post text if it's available
* Tweak: Applied the 'locale' to albums so that the default album names, like 'Timeline Photos' are now translated correctly
* Tweak: The 'Share' link is now added to events when displayed from your Facebook Events page
* Tweak: The 'filer' feature is now also applied to the photo stream when displaying photos from your Facebook Photos page
* Tweak: Added the Access Token to the end of the Facebook API request for the photo stream
* Tweak: Removed the number from the icon which appears on posts which contain more than one photo, as a change in the Facebook API means it's no longer possible to get this number accurately
* Tweak: Add some stricter CSS to some parts of the feed to prevent theme conflicts
* Fix: The individual caption is now shown for each photo in an album when viewed in the pop-up lightbox
* Fix: Fixed an issue caused by a Facebook API change where the post photo attachments wouldn't be displayed for some posts
* Fix: Shared posts now link to the new shared post and not to the original post that was shared
* Fix: The 'photos' text is now translated correctly when displaying only albums
* Fix: The exclude filter setting is now also applied to albums
* Fix: Fixed an issue with the Vimeo embed code due to a change in the Vimeo link format
* Fix: Fixed an issue where some HTML entities were disrupting the application of the post tags
* Fix: The 'offset' setting now works correctly when only displaying a specific post type and when displaying low numbers of posts
* Fix: Fixed an issue with the Multifeed extension not working correctly when displaying just 1 or 2 posts
* Fix: Completely removed the 'Error Reporting' option as it was causing issues with some theme options
* Fix: Corrected a minor issue with the plugin caching string
* Fix: The Extensions page is now hardcoded so that it no longer makes a JSON request to smashballoon.com
* Fix: Made some minor changes based on the deprecation of the Facebook API 1.0

= 2.4.8 =
* New: Added support for the SoundCloud audio player. Any SoundCloud files will now automatically be embedded into your posts.
* Fix: Fixed an issue with the layout of some timeline events
* Fix: Fixed an issue with the mobile layout for event-only feeds
* Fix: Removed some stray PHP notices
* Fix: Removed a line of code which was disabling WordPress Debug/Error Reporting. If needed, this can be disabled again by using the setting at the bottom of the plugin's 'Misc' settings page.

= 2.4.7 =
* New: Added a setting to load a local copy of the icon font instead of the CDN version, or to not load the icon font at all if it's already included in your site. This can be found at the bottom of the 'Misc' settings page.
* Fix: Added support for Vimeo videos which are embedded into the original Facebook post using shortened URLs, such as http://spr.ly
* Fix: Fixed a rare bug which was causing the WordPress admin section to load very slowly for a few users whose site's IP addresses were blocked by our web host
* Fix: Removed query string from the end of CSS and JavaScript file references and replaced it with the wp_enqueue_script 'ver' parameter instead
* Fix: Removed some PHP notices inadvertently introduced in the last update

= 2.4.6 =
* New: Added an email link to the sharing icons
* Fix: Added a workaround for Facebook changing the event URLs in their API from absolute to relative URLs
* Fix: Facebook removed the 'relevant_count' parameter from their API so added a workaround to get the number of photos attached to a post
* Fix: Removed video poster images as the images in the Facebook API weren't high enough quality
* Fix: Added a workaround for 'story_tags' which Facebook deprecated from their API

= 2.4.5 =
* Tweak: Changed the jQuery 'resize' function used in the plugin which was causing issues with some WordPress themes
* Tweak: Removed the 'frameborder=0' parameter from the video iframes as it's been deppreciated in HTML5. The border is now removed using CSS.
* Fix: Fixed a bug where lightbox captions would be cut off when they included double quotes
* Fix: Fixed a bug where the shortcode 'num' option wasn't working correctly when showing a photos-only feed
* Fix: Fixed an issue with padding and margin not being automatically applied to Event feeds when adding a background color to the events
* Fix: Fixed a bug where a forward slash was missing from some URLs in the 'View on Facebook' link within the pop-up lightbox
* Fix: Fixed an issue where the full event end date was being shown even if the event ended on the same day which it started
* Fix: Fixed a formatting issue on posts which have been shared from inside an event to the Facebook page timeline
* Fix: Added a check to the file_get_contents data retrieval method to check whether the Open SSL wrapper is enabled
* Fix: Added the post limit to the caching string to prevent a rare issue with the same cache being pulled for multiple feeds
* Fix: Fixed a bug where the comments weren't showing up for events on your timeline
* Fix: The `eventtitlelink` shortcode option now works correctly
* Fix: The `offset` shortcode option now works when only displaying events
* Fix: Fixed a rare issue where past events from a Facebook page would display very old events first

= 2.4.4 =
* Fix: Reversed a bug introduced in the last update where the plugin would check for updates on page in the WordPress admin area which caused slower page load time
* Fix: Fixed an issue with displaying group events

= 2.4.3 =
* New: Added previous/next navigation to the pop-up photo/video lightbox
* Tweak: Added some missing settings to the System Info section
* Tweak: Added the plugin license type to the plugin name
* Tweak: Added a prefix to the IDs on all posts so that they can now be targeted via CSS
* Tweak: Updated the plugin update script
* Fix: Fixed an issue with the caption and 'View on Facebook' link not showing up in the pop-up lightbox for some photos
* Fix: Removed duplicate IDs on the share icons
* Fix: Added a fix for a wpautop content formatting issue caused by some themes
* Fix: Changed the event handlers on some parts of the feed so that they continue to work after splitting the feed into two columns

= 2.4.2 =
* Tweak: Extended the plugin's "Filter" function to album names when you're displaying albums from your Facebook Photos page
* Tweak: Added an option to disable the Ajax caching added in version 2.4. This can be found at the bottom of 'Customize > Misc'.
* Tweak: Added "nofollow" to all links by default. This can be disabled by using `nofollow=false` in the shortcode.
* Fix: Fixed an issue with Vimeo videos not autoplaying in the video lightbox in the Firefox browser
* Fix: Fixed a rare issue where the likes and comments box would load a 404 error
* Fix: Fixed a minor bug with the album options not being displayed initially on the Customize page when selecting albums as the only post type

= 2.4.1 =
* Fix: Fixed an issue with old events showing up in the events feed
* Fix: Fixed a minor bug in the WP_Http fallback method

= 2.4 =
* New: You can now view photos directly on your site in a popup lightbox. Just click on the photo in the post to view it in the lightbox. This can be disabled on the plugin's Customize page, or by using `disablelightbox=true` in the shortcode.
* New: When a post contains more than 1 photo you can now view the other photos attached to the post in the popup photo lightbox
* New: When displaying a grid of your Facebook albums you can now view the contents of the album in the popup photo lightbox by clicking on the photo
* New: All videos (Facebook, YouTube and Vimeo) can now be played at full-size on your site in a popup video lightbox
* New: Added a share link which allows you to share posts to Facebook, Twitter, Google+ or LinkedIn. This can be disabled at the very bottom of the Typography tab, or by using `showsharelink=false` in the shortcode.
* New: Videos can now either all be played directly in the feed or link to the post on Facebook
* New: The number of likes and comments is now cached in the database to prevent having to retrieve them from Facebook on every page load
* New: If you only want to display the Facebook Like Box and no posts then you can now just set the number of posts to be zero: [custom-facebook-feed num=0]
* New: Added a unique ID to albums, events and photos so that they can be targeted individually or hidden
* New: You can now use the Date Range extension to show posts from a relative/moving date. For example, you can show all posts from the past week by using [custom-facebook-feed from="-1 week" until="now"]

* Tweak: Updated the plugin to use the latest version of the Facebook API
* Tweak: Using your own Facebook Access Token in the plugin is still optional but is now recommended in order to protect yourself against future Access Token related issues
* Tweak: Improved cross-theme CSS consistency
* Tweak: Increased the accuracy of the character count when links are included in the text
* Tweak: Replaced the rel attribute with the HTML5 data attribute when storing data on an element
* Tweak: Added HTTPS stream wrapper check to the System Info to aid in troubleshooting
* Tweak: Updated the plugin's icon font to the latest version
* Tweak: Tweaked the mobile layout of the feed
* Tweak: Updated the plugin updater script
* Tweak: Added the Smash Balloon logo to the credit link which can be optionally displayed at the bottom of your feed. The setting for this is at the bottom of the Misc tab on the Customize page.
* Tweak: Added a shortcode option to only show the Smash Balloon credit link on certain feeds: [custom-facebook-feed credit=true]

* Fix: Reworked the jQuery click function in order to preserve event handlers when splitting the feed into two columns
* Fix: Added error handling to the likes and comments count script in order to fail gracefully if an error occurs
* Fix: Fixed an issue with quotes being escaped in custom/translated text
* Fix: Display an error message if WPHTTP function isn't working correctly
* Fix: Fixed an issue with the license key renewal notice being displayed if you entered an incorrect license key
* Fix: The `postbgcolor` shortcode option is now working correctly
* Fix: Fixed and issue with the dark likes and comments icons not being displayed

= 2.3.2 =
* Fix: Fixed a Facebook application issue which sporadically produced an 'Application request limit reached' error

= 2.3.1 =
* Fix: Fixed a JavaScript error which occurs if a Facebook post doesn't contain any text
* Fix: Fixed an issue with the link color not being applied to links in description text

= 2.3 =
* New: Added a 24 hour clock event date format
* New: Added a text area to the Support tab which contains all of the plugin settings and site info for easier troubleshooting
* Tweak: Removed the 'Buy Tickets' link from events as Facebook removed this from their API
* Tweak: Changed the default event date format to be Jul 25, 2013
* Tweak: If the user doesn't add a unit to the width, height or padding then automatically add 'px'
* Tweak: Added social media sharing links to the bottom of the settings page and an option to add a credit link to the bottom of the feed
* Fix: Fixed an issue with posts not always appearing after first installing the plugin due to an issue with the plugin activation function
* Fix: Fixed an issue with hashtags not being linked when followed immediately by punctuation
* Fix: Facebook group events can now be displayed again, but require a ["User" Access Token](https://smashballoon.com/custom-facebook-feed/docs/get-extended-facebook-user-access-token/)
* Fix: When displaying a shared link if the caption is the same as the link URL then don't display it
* Fix: Added a space before the feed header's style attribute to remove HTML validation error
* Fix: Fixed a bug when selecting the 'Always use the Full-width layout when feed is narrow?' setting which caused it not to be applied to more than one feed on a page
* Fix: Strip HTML tags from captions when used in the image alt tag
* Fix: Prefixed the 'top' and 'bottom' classes used on the Like box to prevent CSS conflicts
* Fix: Fixed a bug with the Event Date 'Text Weight' setting not being applied correctly

= 2.2.1 =
* Fix: Fixed an bug introduced in the last update with events sometimes appearing in random order

= 2.2 =
* New: Added a shortcode option to allow you to offset the number of posts to be shown. Eg: offset=2
* New: Added a Spanish translation - thanks to [Andrew Kurtis](http://www.webhostinghub.com)
* Tweak: If the event end date is the same as the start date then show the end time rather than the entire date
* Tweak: The date 'Timezone' setting is now also included on the plugin's Settings page
* Tweak: Added a note to the Events only options showing how to use the 'pastevents=true' shortcode option
* Tweak: Now renders the plugin's JavaScript variables in the head of your page to prevent issues with themes that render files in the wp_footer function in reverse
* Fix: Fixed an issue with events which have the exact same date and start time not both being displayed
* Fix: Added closing tags when displaying an error message
* Fix: Added some fixes for the [Lightbox extension](https://smashballoon.com/extensions/lightbox/)
* Fix: Added a fix for the [Multifeed extension](https://smashballoon.com/extensions/multifeed/) which was causing an error message to occur if posts weren't available from any one of the Page IDs used
* Fix: Now displays a notification when activating the Pro version if the free version is already installed

= 2.1.1 =
* Tweak: If using the thumbnail layout then any HTML5 videos in your feed smaller than 150px wide are automatically expanded when played to improve their watchability
* Fix: Fixed an issue with upcoming and past page events using the same cached data

= 2.1 =
* New: You can now display a feed of the past events from your Facebook page by using the 'pastevents' shortcode option, like so: [custom-facebook-feed type=events pastevents=true]
* New: Added support for the new [Lightbox](https://smashballoon.com/extensions/lightbox/) extension which allows you to view photos in your feed in a popup lightbox directly on your website
* Tweak: Improved the license key checking procedure to speed up the loading of the plugin's settings pages
* Fix: Fixed a bug which was causing the License page to display as blank on occasion
* Fix: Fixed a rare bug when checking whether extensions were activated or not
* Fix: Removed some stray PHP notices when display only the photos post type

= 2.0.1 =
* Tweak: If the post author is being hidden then change the default date position to be the bottom of the post
* Tweak: Added some default character limits to the post text and descriptions
* Fix: Fixed an issue with the date not being hidden when unchecked in the Show/Hide section
* Fix: Fixed an issue with the 'seconds' custom text string not being saved correctly
* Fix: Fixed issue with the order of photos in the Album extension

= 2.0 =
* New: Added avatar images to comments
* New: Added an HTML5 video player to videos which aren't YouTube or Vimeo, so that they can be played directly in the feed. If the web browser doesn't support HTML5 video then it just links to the video on Facebook instead.
* New: Added an option to display the post date immediately below the author name - as it is on Facebook. This is now the default date position.
* New: Added options to add a background color and rounded corners to your posts
* New: Updated the like, share and comment icons to match Facebook's new design
* New: Added an option to reveal the comments box below each post by default
* New: Added an option to select how many comments to show initially below each post
* New: You can now display photos directly from your Facebook Photos page by setting the post type to be Photos and the Photos Source to be your Photos page. This can be done on the plugin's 'Post Layout' settings page, or directly in the shortcode: [custom-facebook-feed type=photos photosource=photospage]
* New: Added an option to preserve/save your plugin options after uninstalling the plugin. This makes manually updating the plugin much easier.
* New: If your Facebook event has an end date then it will now be displayed after the start date
* New: Hashtags in the post descriptions are now also linked
* New: Added a 'Settings' link to the plugin on the Plugins page
* New: Added a license expiration notice and link which displays on the plugin page when your license is close to expiration
* New: Added a field to the Misc settings page which allows users to enter their Facebook App ID in order to remove a couple of browser console warnings caused by the Facebook Like box widget
* New: Tested and approved for the upcoming WordPress 4.0 release
* Tweak: Added informative error handling and an [Error Message reference](https://smashballoon.com/custom-facebook-feed/docs/errors/) to the website to make troubleshooting easier
* Tweak: If the Facebook API can't be reached by the plugin for some reason then it no longer caches the empty response and instead keeps trying to retrieve the posts from Facebook until it is successful
* Tweak: Removed the lines between comments
* Tweak: Reduced the size of the author avatar from 50px to 40px to match Facebook
* Tweak: Changed the title of the non-embedded video links to not be the file name
* Tweak: Added a checkbox to the Access Token field to select whether to use your Access Token or not
* Tweak: If there are comments then the comments box is now displayed at full-width
* Tweak: The link description text is now 12px in size by default
* Tweak: Added the 'Buy Tickets' link back to events
* Fix: Fixed an issue with Vimeo embed codes not working correctly when using shortened URLs
* Fix: The post author link is no longer the full width of the post and is only wrapped around the author image and name which helps prevent inadvertently clicking on the post author
* Fix: Now added alt tags to all photos
* Fix: Fixed an issue with some video thumbnails not being displayed
* Fix: Facebook offers now display images again
* Fix: Added the trim() function to the 'Test connection to Facebook API' function to improve reliability
* Fix: Fixed an occasional JavaScript error which occurred when the post text was hidden
* Fix: Fixed the 'View on Facebook' link for posts displayed using the [Featured posts](https://smashballoon.com/extensions/featured-post/) extension
* Fix: Added a fb-root element to the Like box to prevent a browser console warning
* Fix: When linking the post text to the Facebook post then linked hashtags no longer cause an issue
* Fix: When linking the post text to the Facebook post the correct text color is now applied
* Fix: Removed some unnecessary line breaks in Facebook offer posts
* Fix: Now open all event links in a new browser tab
* Fix: Removed some redundant inline CSS used on the posts
* Fix: Fixed an Internet Explorer 9 bug where link images were being displayed at too large of a size
* Fix: Removed some stray PHP notices which were being displayed on the plugin settings page

= 1.9.1.1 =
* Fix: Fixed an issue with hashtags in inline CSS being linked inadvertently

= 1.9.1 =
* New: Added support for the new 'Album' extension, which allows you to embed an album and display its photos
* New: Added a Facebook icon to the admin menu
* New: When only displaying the albums post type you can now choose whether to display albums from your timeline or Photos page
* Tweak: Featured Post extension - You can now use the 'type' shortcode option to set the type of the post you are featuring
* Fix: Fixed an issue with hashtags with punctuation immediately following them not being linked
* Fix: Corrected the left side margin on the "Like" box so that it aligns with posts

= 1.9.0 =
* New: Display a list of your albums directly from your Facebook Albums page
* New: Display albums in a single column or in a grid
* New: Hashtags in your posts are now linked to the hashtag search on Facebook. This can be disabled in the 'Post Text' section on the Typography setting page.
* Tweak: Added an HTML wrapper element around the feed
* Tweak: Added a few stricter CSS styles to help minimize the chance of theme stylesheets distorting post formatting
* Tweak: Vertically centered the header text
* Tweak: Added a span to the header text to allow CSS to be applied
* Tweak: Updated the license key activation script to be more reliable
* Fix: Fixed an issue with some photos displaying at a small size due to a change in Facebook's API
* Fix: Fixed an occasional issue affecting the thumbnail and half-width layouts
* Fix: Fixed an issue with link colors not being applied to all links
* Fix: Fixed a rare connection issue when trying to retrieve the number of likes and comments for posts
* Fix: Corrected an occasional issue with shared link information not being displayed
* Fix: Fixed an issue with a generic function name which was occasionally causing an error

= 1.8.3 =
* Fix: If a Vimeo link doesn't have an embedable video accompanying it then don't show the 'Sorry video is not available text'

= 1.8.2 =
* Fix: Fixed a bug with the post author text bumping down below the author image in the Firefox browser

= 1.8.1 =
* New: Added an option to set a height on the Like box. This allows you to display more faces of your fans if you have that option selected.
* Fix: Automatically strips the 'autoplay' parameter from the end of YouTube videos so that they don't autoplay in the feed
* Fix: Fixed a minor issue with post author text width in IE8

= 1.8.0 =
* New: You can now use the Filter feature to exclude posts containing a certain string or hashtag
* New: Added an option to display the photo/video above the post text when using the Full-width layout
* New: Added background and border styling options to shared links
* New: The post layout now defaults to Full-width in narrow columns or on mobile. This can be disabled on the Post Layout tab.
* Tweak: Embedded videos now use the same layout as non-embedded videos
* Tweak: Improved the reliability of the post tags linking
* Tweak: Changed the CSS clearing method to be more reliable
* Tweak: The Filter feature now only strips whitespace from the beginning of strings to allow you to add a space to the end of words
* Tweak: Reduced the clickable area of the post author
* Fix: Added title and alt tags to post author image
* Fix: Fixed issue with &amp; and &quot; symbols
* Fix: Fixed an issue with line breaks not being respected in IE8
* Fix: Fixed an issue with some video titles not appearing when post text is linked
* Fix: Corrected a bug where icon fonts were sometimes rendered italicized
* Compatible with WordPress 3.9

= 1.7.0.2 =
* Fix: Fixed a bug with post text sometimes being duplicated when linked
* Fix: Now adds a 'http' protocol to links starting with 'www'

= 1.7.0.1 =
* Fix: Fixed an issue with likes and comment counts loading in 1.7.0

= 1.7.0 =
* New: Added the ability to change the text size and color of the post author
* New: Define the format, size and color of the shared link title
* New: You can now define the color of the links in your post text, descriptions and events
* Tweak: The icon that appears on album photos now contains the number of photos in the album
* Tweak: Changed the loader for the like and comment counts
* Tweak: Improved the likes, share and comment icons to work better with different background colors
* Tweak: Moved the Feed Header options to the Typography page
* Tweak: Moved the Ajax setting to the Misc page
* Tweak: Now removes any query strings attached to the Page ID
* Tweak: The plugin now uses a built-in shared Access Token
* Fix: Fixed an issue with HTML characters not rendering correctly when linking the post text
* Fix: Fixed an issue with some themes causing the clear element to prevent links being clickable
* Fix: The photo in an album post now links to the album post again. Accommodates the change in Facebook's photo link structure.

= 1.6.2 =
* New: Added support for the 'music' post type
* Fix: Fixed minor issue with link replacement method introduced in 1.6.1

= 1.6.1 =
* Tweak: Event timeline images are now higher quality and the same size as thumbnail photos
* Tweak: Now display the video name above the post text when displaying non-embedded video posts
* Tweak: Changed the method used for link replacement in posts
* Tweak: Changed author and event timeline images back to loading via PHP rather than JavaScript due to issues with certain WordPress themes
* Fix: Disabled post tag linking when the post text is linked to the Facebook post
* Fix: Use a fallback JSON string if unable to find the cached version in the database

= 1.6.0 =
* New: Now supports post tags - creates links when using the @ symbol to tag other people or pages in your posts
* New: Added an 'exclude' shortcode option to allow you to easily exclude specific parts of the post
* New: Timeline events are now cached to help reduce page load time
* New: Added a new post type option for 'album' posts
* New: Choose to show the full event image or the square cropped version when displaying only events
* New: Added an option for when the WordPress theme is loading the feed via AJAX so that the JavaScript runs after the feed has been loaded into the page
* New: Added an 'accesstoken' shortcode option
* Tweak: Timeline event images are now loaded in via JavaScript after page load
* Tweak: The Filter option now also applies to events displayed from the Events page
* Tweak: Improvements to the show/hide option for customizing events from the Events page
* Tweak: Made the 'Link to Facebook video post' the default action for non-embedded video
* Tweak: Featured Post extension now utilizes caching
* Tweak: Featured Post extension improvements to photo posts
* Fix: Added a fix for the Facebook API 'Ticket URL' bug. Ticket URLs have been removed from events.
* Fix: Fixed a color picker JavaScript conflict that was occuring on rare occasions
* Fix: Reset the timezone after the shortcode has run
* Fix: When dark icons are selected then they now also apply to the icons within the dropdown comments box
* Fix: Fixed an issue with the shared link descriptions not being hidden when specified
* Fix: Fixed a rare issue with the 'textlink' shortcode option
* Fix: Added a WPAUTOP fix for album posts
* Fix: Fixed some minor IE quirks mode bugs

= 1.5.0 =
* New: Added a built-in color picker
* New: Added an Extensions page which displays available extensions for the plugin
* New: Added integration with the 'Multifeed' extension
* New: Added integration with the 'Date Range' extension
* New: Added integration with the 'Featured Post' extension
* Tweak: Now automatically set the post limit based on the number of posts to be displayed
* Tweak: Added class to posts based on the author so allow for independent styling
* Tweak: Now loads the author avatar image in using JavaScript to help speed up load times
* Tweak: Links in the post text now open in a new tab by default
* Tweak: Improved the Post Layout UI
* Tweak: Moved the License page to a tab on the Settings page
* Tweak: Created a Support tab on the Settings page
* Tweak: Improved the 'Test connection to Facebook API' function
* Tweak: Core improvements to the way posts are output
* Fix: Fixed an issue with photo captions not displaying under some circumstances

= 1.4.3 =
* New: Choose to display events from your Events page for up to 1 week after the start time has passed
* Tweak: Changed 'Layout & Style' page name to 'Customize'
* Fix: Added CSS box-sizing property to feed header so that padding doesn't increase its width
* Fix: Fixed showheader=false and headeroutside=false shortcode options
* Fix: Fixed include=author shortcode option
* Fix: More robust method for stripping the URL when user enters Facebook page URL instead of their Page ID
* Fix: Encode URLs so that they pass HTML validation

= 1.4.2 =
* New: Set your timezone so that dates/times are displayed in your local time
* Tweak: Description character limit now also applies to embedded video descriptions
* Fix: Fixed issue with linking the post text to the Facebook post
* Fix: Comments box styling now applies to the 'View previous comments' and 'Comment on Facebook' links
* Fix: Fixed the 'showauthor' shortcode option
* Fix: Added the ability to show or hide the author to the 'include' shortcode option
* Fix: Fixed issue with the comments box not expanding when there were no comments
* Fix: Now using HTML encoding to parse any raw HTML tags in the post text, descriptions or comments
* Fix: Fixed date width issue in IE7
* Fix: Added http protocol to the beginning of links which don't include it
* Fix: Fixed an issue with the venue link when showing events from the Events page
* Fix: Removed stray PHP notices
* Fix: Numerous other minor bug fixes

= 1.4.1 =
* Fix: Fixed some minor bugs introduced in 1.4.0
* Fix: Fixed issue with album names not always displaying
* Fix: Added cURL option to handle gzip compression

= 1.4.0 =
* New: Redesigned comment area to better match Facebook
* New: Now displays the number of likes a comment has
* New: Now shows 4 most recent comments and add a 'View older comments' button to show more
* New: Shows the names of who likes the post at the top of the comments section
* New: Added a 'Comment on Facebook' button at the bottom of the comments section
* New: Can now choose to show posts only by other people
* New: Added ability to add a customizable header to your feed
* New: Added a 'Custom Text / Translate' tab to house all customizable text
* New: Added an icon and CSS class to posts with multiple images
* New: When posting multiple images it states the number of photos after the post text
* New: When sharing photos or links it now states who you shared them from
* Tweak: String/hastag filtering now also applies to the description
* Tweak: Updated video play button to display more consistently across video sizes
* Tweak: Events will now still appear for 6 hours after their start time has passed
* Tweak: Added a button to test the connection to Facebook's API for easier troubleshooting
* Tweak: Plugin now detects whether the page is using SSL and pulls https resources
* Tweak: Post with multiple images now link to the album instead of the individual photo
* Tweak: WordPress 3.8 UI updates
* Fix: Fixed Vimeo embed issue
* Fix: Fixed issue with some event links due to a Facebook API change
* Fix: Fixed an issue with certain photos not displaying correctly

= 1.3.8 =
* New: Added a 'Custom JavaScript' section to allow you to add your own custom JavaScript or jQuery scripts

= 1.3.7.2 =
* Tweak: Changed site_url to plugins_url
* Fix: Fixed issue with enqueueing JavaScript file

= 1.3.7.1 =
* Tweak: Added option to remove border from the Like box when showing faces
* Tweak: Added ability to manually translate the '2 weeks ago' text
* Tweak: Checks whether the Access Token is inputted in the correct format
* Tweak: Replaced 'View Link' with 'View on Facebook' so that shared links now link to the Facebook post
* Fix: Fixed issue with certain embedded YouTube videos not playing correctly
* Fix: Fixed bug in the 'Show posts on my page by others' option

= 1.3.7 =
* New: Improved shared link and shared video layouts
* New: When only showing events you can now choose to display them from your Events page or timeline
* New: Set "Like" box text color to either blue or white
* Tweak: Displays image caption if no description is available
* Tweak: "Like" box is now responsive
* Tweak: Vertically center multi-line author names rather than bumping them down below the avatar
* Tweak: Various CSS formatting improvements
* Fix: If displaying a group then automatically hide the "Like" box
* Fix: 'others=false' shortcode option now working correctly
* Fix: Fixed formatting issue for videos without poster images
* Fix: Strip any white space characters from beginning or end of Access Token and Page ID

= 1.3.6 =
* Tweak: Embedded videos are now completely responsive
* Tweak: Now displays loading gif while loading in likes and comments counts
* Tweak: Improved documentation within the plugin
* Tweak: Changed order of methods used to retrieve feed data
* Fix: Corrected bug which caused the loading of likes and comments counts to sometimes fail

= 1.3.5 =
* New: Feed is now fully translatable into any language - added i18n support for date translation
* New: Now works with groups
* New: Added support for group events
* Fix: Resolved jQuery UI draggable bug which was causing issues in certain cases with drag and drop
* Fix: Fixed full-width event layout bug
* Fix: Fixed video play button positioning on videos with small poster images

= 1.3.4 =
* New: Added localization support. Full support for various languages coming soon.
* Fix: Fixed an issue regarding statuses linking to the wrong page ID

= 1.3.3 =
* New: Post filtering by string: Ability to display posts based on whether they contain a particular string or #hashtag
* New: Option to link statuses to either the status post itself or the directly to the page/timeline
* New: Added CSS classes to different post types to allow for different styling based on post type
* New: Added option to added thumbnail faces of fans to the Like box
* New: Define your own width for the Like box
* Tweak: Added separate classes to 'View on Facebook' and 'View Link' links so that they can be targeted with CSS
* Tweak: Prefixed every CSS class to prevent styling conflicts with theme stylesheets
* Tweak: Automatically deactivates license key when plugin is uninstalled

= 1.3.2 =
* New: Added support for Facebook 'Offers'
* Fix: Fixes an issue with the 'others' shortcode caused by caching introduced in 1.3.1
* Fix: Prefixed the 'clear' class to prevent conflicts

= 1.3.1 =
* New: Post caching now temporarily stores your post data in your WordPress database to allow for super quick load times
* New: Define your own caching time. Check for new posts every few seconds, minutes, hours or days. You decide.
* New: Display events directly from your Events page
* New: Display event image, customize the date, link to a map of the event location and show a 'Buy tickets' link
* Tweak: Improved layout of admin pages for easier customization
* Fix: Provided a fix for the Facebook API duplicate post bug

= 1.3.0 =
* New: Define your own custom text for the 'See More' and 'See Less' buttons
* New: Add your own CSS class to your feeds with the new shortcode 'class' option
* New: Show actual number of comments when there is more than 25, rather than just '25+'
* New: Define a post limit which is higher or lower than the default 25
* New: Include the Like box inside or outside of the feed's container
* Tweak: Made changes to the plugin to accomodate the October Facebook API changes
* Fix: Fixed bug which ocurred when multiple feeds are displayed on the same page with different text lengths defined

= 1.2.9 =
* New: Added a 'See More' link to expand any text which is longer than the character limit defined
* New: Choose to show posts by other people in your feed
* New: Option to show the post author's profile picture and name above each post
* New: Specify the format of the Event date
* Tweak: Default date format is less specific and better mimics Facebook's - credit Mark Bebbington
* Fix: When a photo album is shared it now links to the album itself and not just the cover photo
* Fix: Fixed issue with hyperlinks in post text which don't have a space before them not being converted to links
* Minor fixes

= 1.2.8 =
* Tweak: Added links to statuses which link to the Facebook page
* Tweak: Added classes to event date, location and description to allow custom styling
* Tweak: Removed 'Where' and 'When' text from events and made bold instead
* Tweak: Added custom stripos function for users who aren't running PHP5+

= 1.2.7 =
* Fix: Fixes the ability to hide the 'View on Facebook/View Link' text displayed with posts

= 1.2.6 =
* Fix: Prevents the WordPress wpautop bug from breaking some of the post layouts
* Fix: Event timezone fix when timezone migration is enabled

= 1.2.5 =
* Tweak: Replaced jQuery 'on' function with jQuery 'click' function to allow for compatibilty with older jQuery versions
* Minor bug fix regarding hyperlinking the post text

= 1.2.4 =
* New: Added a ton more shortcode options
* New: Added options to customize and format the date
* New: Add your own text before and after the date and in place of the 'View on Facebook' and 'View Link' links
* New: If there are no comments on a post then choose whether to hide the comment box or use your own custom text
* Tweak: Separated the video/photo descriptions and link descriptions into separate checkboxes in the Post Layout section
* Tweak: Changed the layout of the Typography section to allow for the additional options
* Tweak: Added a System Info section to the Settings page to allow for simpler debugging of issues related to PHP settings

= 1.2.3 =
* New: Choose to only show certain types of posts (eg. events, photos, videos, links)
* New: Add your own custom CSS to allow for even deeper customization
* New: Optionally link your post text to the Facebook post
* New: Optionally link your event title to the Facebook event page
* Fix: Only show the name of a photo or video if there is no accompanying text
* Some minor modifications

= 1.2.2 =
* Fix: Set all parts of the feed to display by default

= 1.2.1 =
* Select whether to hide or show certain parts of the posts
* Minor bug fixes

= 1.2.0 =
* Major Update!
* New: Loads of customization, layout and styling options for your feed
* New: Define feed width, height, padding and background color
* New: Choose from 3 preset post layouts; thumbnail, half-width, and full-width
* New: Change the font-size, font-weight and color of the post text, description, date, links and event details
* New: Style the comments text and background color
* New: Choose from light or dark icons
* New: Select whether the Like box is shown at the top of bottom of the feed
* New: Choose Like box background color
* New: Define the height of the video (if required)

= 1.1.1 =
* New: Shared events now display event details (name, location, date/time, description) directly in the feed

= 1.1.0 =
* New: Added embedded video support for youtu.be URLs
* New: Email addresses within the post text are now hyperlinked
* Fix: Links beginning with 'www' are now also hyperlinked

= 1.0.9 =
* Bug fixes

= 1.0.8 =
* New: Most recent comments are displayed directly below each post using the 'View Comments' button
* New: Added support for events - display the event details (name, location, date/time, description) directly in the feed
* Fix: Links within the post text are now hyperlinked

= 1.0.7 =
* Fix: Fixed issue with certain statuses not displaying correctly
* Fix: Now using the built-in WordPress HTTP API to get retrieve the Facebook data

= 1.0.6 =
* Fix: Now using cURL instead of file_get_contents to prevent issues with php.ini configuration on some web servers

= 1.0.5 =
* Fix: Fixed bug caused in previous update when specifying the number of posts to display

= 1.0.4 =
* Tweak: Prevented likes and comments by the page author showing up in the feed

= 1.0.3 =
* Tweak: Open links to Facebook in a new tab/window by default
* Fix: Added clear fix
* Fix: CSS image sizing fix

= 1.0.2 =
* New: Added ability to set a maximum length on both title and body text either on the plugin settings screen or directly in the shortcode

= 1.0.1 =
* Fix: Minor bug fixes.

= 1.0 =
* Launch!
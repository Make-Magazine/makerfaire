=== Social Wall ===
Author: Smash Balloon
Contributors: smashballoon
Support Website: http://smashballoon/social-wall/
Tags: Social Media, Instagram, Twitter, Facebook, YouTube
Requires at least: 3.4
Tested up to: 5.8
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Social Wall allows you to display completely customizable social media feeds.

== Description ==
Display **completely customizable**, **responsive** and **search engine crawlable** social wall with your Instagram, Facebook, Twitter, and YouTube content!

= Features =
* **Completely Customizable** - by default inherits your theme's styles
* Social Wall feed content is **crawlable by search engines** adding SEO value to your site
* **Completely responsive and mobile optimized** - works on any screen size
* Display a feed in a masonry, carousel, or list layout
* Allow **filtering** of videos using keywords in the description or title
* Display **multiple feeds** from different social media sources on multiple pages or widgets
* Post caching means that your feed loads **lightning fast** and minimizes API requests
* **Infinitely load more** of your social media content with the 'Load More' button
* Fully internationalized and translatable into any language
* Display a filter to allow visitors to select the social media sources in the feed
* Enter your own custom CSS or JavaScript for even deeper customization

For simple step-by-step directions on how to set up the Social Wall plugin please refer to our [setup guide](http://smashballoon.com/social-wall/docs/setup/ 'Social Wall setup guide').

= Benefits =
* **Increase social engagement** between you and your users, customers, or fans
* **Save time** by using the Social Wall plugin to generate dynamic, search engine crawlable content on your website
* **Get more follows** by displaying your social media content directly on your site
* Display your social media content **your way** to perfectly match your website's style
* The plugin is **updated regularly** with new features, bug-fixes and API changes
* Support is quick and effective
* We're dedicated to providing the **most customizable**, **robust** and **well supported** social media plugin in the world!

== Installation ==
1. Install the Social Wall plugin either via the WordPress plugin directory, or by uploading the files to your web server (in the /wp-content/plugins/ directory).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Install and activate the Instagram Feed Pro, Custom Facebook Feed Pro, Feeds for YouTube Pro, and Custom Twitter Feeds.
4. Navigate to the 'Social Wall' settings page to configure your Social Wall feed.
5. Use the shortcode [social-wall][instagram-feed][custom-facebook-feed][custom-twitter-feeds][youtube-feeds][/social-wall] in your page, post or widget to display your feed.
6. You can display multiple feeds with different configurations by specifying the necessary parameters directly in the shortcode: [social-wall][custom-twitter-feeds screenname="smashballoon"][/social-wall].

For simple step-by-step directions on how to set up the Social Wall plugin please refer to our [setup guide](http://smashballoon.com/social-wall/docs/setup/ 'Social Wall setup guide').

= Setting up the Social Wall WordPress Plugin =

The Social Wall plugin is brand new and so we're currently working on improving our documentation for it. If you have an issue or question please submit a support ticket and we'll get back to you as soon as we can.

1) Once you've installed the Social Wall plugin click on the "Social Feeds" item in your WordPress menu

2) If you haven't installed and activated Instagram Feed Pro, Custom Facebook Feed Pro, Custom Twitter Feeds Pro, and/or Feeds for YouTube Pro, do so now. Follow the setup directions for each plugin to connect an account or get an access token.

3) Navigate to the Customize and Style pages to customize your Social Wall.

4) Copy the generated shortcode on the "Configure" tab (ex. [social-wall][instagram-feed][custom-facebook-feed][custom-twitter-feeds][youtube-feeds][/social-wall]) shortcode and paste it into any page, post or widget where you want the social media feed to appear.

5) You can paste the [social-wall][instagram-feed][custom-facebook-feed][custom-twitter-feeds][youtube-feeds][/social-wall] shortcode directly into your page editor.

6) You can use the default WordPress 'Text' widget to display your social media feed in a sidebar or other widget area.

7) View your website to see your social media feed(s) in all their glory!

== Frequently Asked Questions ==

= Can I display multiple feeds on my site or on the same page? =

Yep. You can display multiple feeds by using our built-in shortcode options, for example: `[social-wall][youtube-feed channel="smashballoon" num=3][/social-wall]`.

= How do I embed a social wall feed directly into a WordPress page template? =

You can embed your social wall feed directly into a template file by using the WordPress [do_shortcode](http://codex.wordpress.org/Function_Reference/do_shortcode) function: `<?php echo do_shortcode('[social-wall][instagram-feed][custom-facebook-feed][custom-twitter-feeds][youtube-feeds][/social-wall]'); ?>`.

= Will Social Wall work with W3 Total Cache or other caching plugins? =

The Social WAll plugin should work in compatibility with most, if not all, caching plugins, but you may need to tweak the settings in order to allow the social media feeds to update successfully and display your latest posts.  If you are experiencing problems with your social media feeds not updating then try disabling either 'Page Caching' or 'Object Caching' in W3 Total Cache (or any other similar caching plugin) to see whether that fixes the problem.

== Screenshots ==

== Changelog ==
= 1.0.4 =
* Fix: Added compatibility with version 4.0 of the Custom Facebook Feed plugin.

= 1.0.3 =
* Tweak: Added compatibility with the latest version of the Custom Facebook Feed plugin. Please update both plugins to ensure compatibility.

= 1.0.2 =
* Fix: Using "num=" in the shortcode would lead to an inconsistent number of posts actually being displayed.
* Fix: Twitter cards would not load in the feed until the social wall cache had cleared after they were generated.
* Fix: Fixed a JavaScript error that would cause carousel feeds to not work when using Internet Explorer.
* Fix: Added a maximum width to images to prevent images being too large in certain themes.
* Fix: Prevented duplicate posts/tweets/videos from appearing in the feed.

= 1.0.1 =
* New: Added support for local, resized images for Twitter. After updating to version 1.11 for Custom Twitter feeds, use local images for Twitter Cards and medium sized images for your tweets in your feed.
* Fix: Backup cache refresh feature changed from being triggered 2 days after feed missed an update to being triggered relative to cache refresh time.
* Fix: Translation files added.
* Fix: Relative date text settings would not save.
* Fix: Fixed a PHP warning "undefined index $wall_account_data".

= 1.0 =
* Launched the Social Wall plugin!
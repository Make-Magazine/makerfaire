=== YouTube Pro ===
Contributors: embedplus
Plugin Name: YouTube Pro
Tags: youtube live, live stream, youtube channel, video gallery, youtube galleries, channel gallery, gallery, playlist gallery, youtube gallery, accessibility, analytics, api, blocked youtube videos, cache, caching, channel, deleted youtube videos, effects, embed youtube, embedding youtube, featured image, get_locale, i18n, internationalization, lazy, lazy load, locale, localization, mute, no-cookie, oembed, page speed, playlist, playlists, plugin, Reddit, responsive, seo, short code, shortcode, ssl, subtitles, thumbnail, thumbnail image, thumbnails, tinymce, video, video analytics, video plugin, video seo, video shortcode, video thumbnails, view count, volume, widget, wordpress security, wordpress youtube embed, youtube, youtube api, youtube analytics, youtube embed, youtube impressions, youtube player, youtube playlist, youtube plugin, youtube shortcode, youtube snippets, youtube takedowns, youtube thumbnails, youtube plugin migration
Requires at least: 4.0
Tested up to: 5.5
Stable tag: 13.4.1
License: GPLv3 or later

YouTube Pro. Customize and embed a responsive video, YouTube channel gallery, playlist gallery, or live stream from YouTube.com

== Description ==

**WordPress YouTube embeds, galleries, and live streams can be customized in a variety of ways beyond the free version with this Pro plugin. Here are some of its features:**

* Full visual embedding wizard (so you can avoid memorizing codes)
* Alternate playlist and channel gallery styling  (list layouts and slider layouts, popup/lightbox player, and more)
* Caching to avoid making frequent requests to YouTube.com and speed up your page loads
* Automatic video thumbnail images: each post or page that contains at least one video will have the thumbnail of its first video serve as its featured image
* [Lazy loading YouTube embeds >>](http://www.embedplus.com/add-special-effects-to-youtube-embeds-in-wordpress.aspx) with eye-catching effects and animations
* Automatic tagging for video SEO
* Automatic Open Graph tagging for Facebook
* Deleted video alerts (i.e., did Google remove or take down videos I previously embedded?) 
* Mobile compatibility checking (i.e., see if your embeds have restrictions that can block your site's mobile visitors from viewing)
* Alerts when visitors from different countries are blocked from viewing your embeds
* Priority support

You can even get an embedder-centric analytics dashboard that adds view tracking to each of your embeds so that you can answers questions like:

* How much are your visitors actually watching the videos you post?
* How does the view activity on your site compare to other sites like it?
* What and when are your best and worst performing YouTube embeds?
* How much do the producers of the YouTube videos you embed rely on **your site**, versus other sites and YouTube.com, for views?
* Are you embedding videos that are blocked in other countries?
* Have your visitors tried to view a page and/or gallery on your site with deleted/unavailable videos?

[Thanks for supporting us!>>](http://www.embedplus.com/)

== Installation ==

1. Use the WordPress plugin installer to install the plugin.  Alternatively, you can just extract the folder in our download package and upload it to your plugin directory.
1. Access the Plugins admin menu to activate the YouTube embed plugin.
1. Make your default settings after clicking the new YouTube menu item that shows up in your admin panel.
1. To embed videos in your post, start pasting the links with any desired additional codes needed for your YouTube embed (see below section for additional codes). Make sure each link is on its own line. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode. Example: `[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]` If you don't know exactly which video you want to embed, use the free built-in search feature to find and insert one.
1. You can also [embed a playlist and channel gallery with this plugin >>](http://www.embedplus.com/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx).  Please install the plugin and visit the settings page for instructions.
1. To get video SEO, an analytics dashboard and many other premium features, [sign up for one of the options here >>](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx)

**Additional codes (adding these will override the default settings in the admin):**

* width - Sets the width of your player. If omitted, the default width will be the width of your theme's content. Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350"`
* height - Sets the height of your player. If omitted, this will be calculated for you automatically. Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350"`
* vq - Set this to `hd720` or `hd1080` to force the video to play in HD quality. Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&vq=hd720"`
* autoplay - Set this to 1 to autoplay the video (or 0 to play the video once). Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&autoplay=1"`
* cc_load_policy - Set this to 1 to turn on closed captioning (or 0 to leave them off). Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&cc_load_policy=1"`
* iv_load_policy - Set this to 3 to turn off annotations (or 1 to show them). Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&iv_load_policy=3"`
* loop - Set this to 1 to loop the video (or 0 to not loop). Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&loop=1"`
* modestbranding - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&modestbranding=1"`
* rel - Set this to 0 to not show related videos at the end of playing (or 1 to show them). Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&rel=0"`

You can also start and end each individual video at particular times. Like the above, each option should begin with '&'

* start - Sets the time (in seconds) to start the video. Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&start=20"`
* end - Sets the time (in seconds) to stop the video. Example: `"http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&end=100"`

**Always follow these rules when pasting a link:**

* Make sure the url is really on its own line by itself. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode. Example: `[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]`
* Make sure the url is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the url and click the "unlink" button in your editor.
* Make sure you did **not** format or align the url in any way. If your url still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes).
* Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Thirteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.

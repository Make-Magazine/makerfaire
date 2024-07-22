=== GravityView - Ratings Reviews Extension ===
Tags: gravityview
Requires at least: 4.4
Tested up to: 6.6
Stable tag: trunk
Contributors: gravitykit, bordoni, akeda
License: GPL 3 or higher

Enable Ratings and Reviews for entries in GravityView.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Configure "Ratings and Reviews" via meta box in edit View
4. News fields regarding review also appear when configure your View

== Frequently Asked Questions =

= Why can I leave more than one comment? =

Administrators, users who are able to moderate comments, and users who have full permission for Gravity Forms functionality are able to leave unlimited comments on ratings.

== Changelog ==

= 2.3.1 on July 15, 2024 =

This is a minor update that fixes a fatal error.

* Fixed: Fatal error dividing by zero when calculating average rating

= 2.3 on July 11, 2024 =

This is a major update that introduces new features for anonymous reviews, enhanced control over the review form, Merge Tags, and additional customization options.

#### 🚀 Added
- Anonymous (not logged in) users can now leave reviews/vote on entries. [Learn more about anonymous reviews](https://docs.gravitykit.com/article/1014-ratings-reviews-anonymous-reviews).
- Option to allow users to edit their reviews.
- Ability to sort entries by the number of upvotes.
- View setting to hide "Title" and "Rating" fields.
- Support for displaying the reviewer's avatar next to their review.
- Ratings can now be *required* on a review.
- View setting to disable downvoting.
- Merge Tags to display the total and average number of entry stars and votes. [See all available Merge Tags](https://docs.gravitykit.com/article/1012-ratings-reviews-merge-tags).

#### 🐛 Fixed
- Typo in an error message when a user tries to leave a review, but they're not allowed.
- The Rate Entry field was not allowing ratings when using the DataTables layout.
- Improved translation strings.
- PHP 8.2 deprecation notices.

#### 🔧 Developer Updates
- Added: `gv_ratings_reviews_disable_downvoting` filter to disable downvoting (default is `false` or the value set in the View settings). [Learn more about the filter](https://docs.gravitykit.com/article/1013-ratings-reviews-hooks).
- Added: `gravityview_ratings_reviews_merge_tags` and `gravityview_ratings_reviews_replace_merge_tag` filters to add additional Merge Tags and replace their contents. [Learn more about the filter](https://docs.gravitykit.com/article/1013-ratings-reviews-hooks).

= 2.2.1 on February 15, 2023 =

* Fixed: PHP warning on review submission

= 2.2 on January 9, 2023 =

* Fixed: Sorting by votes/ratings
    - A new "Recalculate Ratings" bulk form action was added to fix sorting for existing entries
* Fixed: Stars would incorrectly display in certain themes
* Fixed: Incorrect link to all comments in notification messages
* Fixed: Rating entries did not work from an embedded View
* Fixed: PHP 8 deprecation notices

= 2.1 on August 31, 2021 =

* Added: It's now possible to rate entries from the Multiple Entries screen!
* Added: Fields now have icons in the "Add Field" picker
* Fixed: Ratings being created without attached form and entry IDs ("Form ID 0 Entry ID 0")
* Fixed: Reviews Link field doesn't process Merge Tags

= 2.0.2 on February 5, 2020 =

* Fixed: PHP warning
* Updated: Russian, Turkish, and French translations (Thank you, Viktor S and Süha Karalar)

= 2.0.1 on September 14, 2018 =

* Fixed: Star ratings were not being saved on iOS devices
* Updated: Polish and Russian translations (Thank you, [@dariusz.zielonka](https://www.transifex.com/user/profile/dariusz.zielonka/) and [@awsswa59](https://www.transifex.com/user/profile/awsswa59/)!)

__Developer Updates:__

* Added: `gv_ratings_reviews_get_reviews_atts` filter to modify the attributes passed to `get_comments()` when fetching reviews
* Added: `gv_ratings_reviews_is_user_allowed_to_review` filter to modify whether the user can leave a review. Return a `WP_Error` instance to modify the message shown to the user
* Updated: `templates/review-form.php` file now supports a `WP_Error` return value from `GravityView_Ratings_Reviews_Helper::is_user_allowed_to_leave_review()`

= 2.0 on May 22, 2018 =

* Now requires GravityView 2.0
* Fixed: Compatibility with GravityView 2.0
* Fixed: Sorting by rating
* Fixed: View Settings not displaying
* Fixed: Error saving ratings when new entries are created
* Changed: Show other settings only when "Enable entry reviews" is checked
* Added: Spanish translation (thank you, Jose Usó!)

= 1.3.5 on January 6, 2016 =

* Fixed: Issue where all comments are trashed when a single entry is trashed
* Fixed: Entry link not working on Comments page
* Updated translations: Russian, German, and Turkish - thanks, translators!

= 1.3.4 on May 6, 2016 =

* Fixed: Fatal error when bulk deleting entries in the Admin
* Fixed: PHP notice when submitting a review with the option `Hide ratings fields` set
* Added: Chinese translation (thanks, Edi Weigh!)

= 1.3.3 on December 22, 2015 =
* Updated: Requires GravityView 1.12 or higher
* Added: Setting to allow empty reviews: "Allow empty review text"
* Fixed: DataTables issues (sorting by rating, displaying vote field)
* Fixed: Issue in WordPress 4.4 with allowing comments with empty text
* Fixed: Improved compatibility with the Recent Comments Widget. New reviews will link to the original page where they were submitted.
* Fixed: Individual star ratings not being displayed properly (always one star)
* Fixed: Sorting by stars and votes
* Fixed: Existing entries may not accept reviews
* Fixed: Clear entry cache when rating is added
* Fixed: Allow users to leave comments on ratings. Before, commenting on a review this would cause a "you've already left a review" screen.
* Fixed: Allow overriding the `review-form.php` template
* Fixed: $screen not an object PHP error message
* Fixed: Removed extra whitespace on Reviews Link field
* Fixed: Issue with reviews not showing up
* Tweak: Moved the `review_form()` method from helper class to review class
* Tweak: Added `white-space: nowrap;` style to +/- rating form to improve readability for "No Rating"
* Tweak: Show that updates are available even when not using active license
* Tweak: Make sure WordPress is loaded before processing files
* Developer changes:
    * Reviews now store the referring post ID of the review. It's stored using the `gv_post_id` comment meta key. You can access it using the `update_comment_meta()` function and modify it using the `get_comment_metadata` filter.
    * Added second parameter (`$create_if_not_exists`, boolean) to `GravityView_Ratings_Reviews_Helper::get_post_bridge_id()`
    * Added `gv_ratings_reviews_allow_empty_comments` filter to enable/disable comments (return boolean)
    * Renamed `comment_form_default_fields` filter to `gv_ratings_reviews_review_form_fields`
    * Renamed `comment_form_defaults` filter to `gv_ratings_reviews_review_form_settings`
    * Added `gv_ratings_reviews_js_vars` filter to modify Javascript commenting and +/- voting text
    * Added `gv_ratings_reviews_post_bridge_title` filter to modify the Bridge Post title shown in the widget. Passes two args: title and entry array. This allows using Gravity Forms Merge Tags to modify the title values.

= 1.2 & 1.2.1 beta on August 27 =
* Updated: Fetch templates using GravityView template system so that they can be [overridden properly](http://docs.gravitykit.com/article/64-how-to-override-gravityview-templates).
    - Removed the following template path filters: `gravityview_entry_comments_template_path` and `gv_ratings_reviews_item_template`
    - Moved templates directory location from `/includes/templates/` to `/templates/`
* Fixed: Prevent ratings fields from displaying when commenting on reviews
* Fixed: Extension class fatal error when no other extension is active
* Updated: Turkish translation (thanks Süha Karalar!)

= 1.1 beta on August 4 =
* Added: Possibility to sort the Ratings column `Reviews Link` using the sorting controls on a default table layout
* Added: Possibility to sort the View by the average rating (Filter & Sort view settings)
* Fixed: Conflicts with Jetpack comments
* Updated: Translations

= 1.0.3 beta on May 8 =
* Fixed: Issue with reviews not displaying


= 1.0.2 beta on May 8 =
* Updated: Translation files

= 1.0.1 beta on May 7 =
* Added: Export review details (review title, text, and more) in when exporting entries from Gravity Forms
* Added: Support for GravityView 1.8 tabbed settings (coming soon)
* Fixed: Sorting entries by rating

= 1.0 beta =
* Added: Export Ratings & Votes columns in "Export Entries" screen
* Fixed: Editing Star and Voting ratings when editing a comment in the Admin
* Fixed: Delete Post Bridge when entry is deleted
* Fixed: Only show approved comments
* Fixed: Prevent underlined icons
* Tweak: Comment text sanitization improved
* Modified: Set comment type to `gravityview`


= 0.2 =
* Only use approved reviews in counts and displayed comments
* Add `gravityview` comment type

= 0.1.0 =
* Initial release


= 1721335883-4249 =
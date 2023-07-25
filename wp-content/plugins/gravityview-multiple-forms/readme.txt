=== GravityView - Multiple Forms ===
Requires at least: 4.4
Tested up to: 6.2
Requires PHP: 7.2
Stable tag: trunk
Contributors: The GravityKit Team
License: GPL 2

Display values from multiple forms in a single View.

== Description ==

Display values from multiple forms in a single View. Learn more on [gravityview.co](https://gravityview.co/extensions/multiple-forms/).

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 0.3.4 on May 16, 2023 =

* Fixed: Fatal error when running less than PHP 8.0

= 0.3.3 on May 16, 2023 =

* Improved: Display an admin notice when GravityView or Gravity Forms are not installed and activated
* Fixed: `'GravityKit\MultipleForms\GF_Query_JSON_Literal' not found` fatal error when performing a search on a View
* Fixed: Joining two Views using "any form field" joins can cause fatal errors

= 0.3.2 on May 1, 2023 =

* Fixed: Fatal error when GravityView is not activated (introduced in Version 0.3)

= 0.3.1 on April 19, 2023 =

* Fixed: PHP warning

= 0.3 Beta 3 on April 19, 2023 =

* Fixed: Fatal error related to namespacing

= 0.3 Beta 2 on April 18, 2023 =

* Fixed: Fatal error when activating the extension

= 0.3 Beta 1 on April 17, 2023 =

* Added: You can now sort Views by joined form fields
* Modified: Minimum version of PHP bumped to 7.2 to match all other GravityKit plugins
* Fixed: When field ids across joined forms were the same, values could be pulled from the wrong form
* Fixed: View editor became unusable when a joined form was trashed or deleted
* Fixed: When "Show only approved entries" was enabled for a View, it was behaving as if "Strict Entry Match" were also enabled

= 0.2 Beta 2 on June 10, 2020 =

* Fixed: Conflict preventing Yoast SEO scripts from running on Views

= 0.2 Beta 1 on May 25, 2020 =

* Added: Allow joining forms on field meta and properties
    - Adds support for [Nested Forms by GravityWiz](https://gravitywiz.com/?ref=63)!
* Added: Allow joining multiple forms on entry meta (non-field data)
* Added: `gravityview_multiple_forms/allow_join_on` filter for developers to modify the list of permissible meta and properties
* Added: Italian and Persian translations (Thanks, Farhad P.!)
* Improved: Do not require a published View to display join conditions
* Modified: Now requires GravityView 2.6 or newer
* Fixed: Fatal error when using Gravity Forms 2.2 or older
* Fixed: "Cannot read property 'title' of undefined" JavaScript error

= 0.1 Beta 2 =

* Fixed: PHP Warning: `Declaration of GF_Patched_Query::_prime_joins() should be compatible with GF_Query::_prime_joins()`

= 0.1 Beta 1 =

* Liftoff!


= 1690302800-4249 =
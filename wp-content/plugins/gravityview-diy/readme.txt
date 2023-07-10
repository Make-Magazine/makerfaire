=== GravityView DIY ===
Tags: gravityview
Requires at least: 4.4
Tested up to: 5.6
Stable tag: trunk
Contributors: The GravityView Team
License: GPL 2 or higher

A flexible, powerful GravityView layout for designers & developers.

== Description ==

DIY is a different kind of View layout: the purpose is to allow designers and developers the most flexibility. DIY allows you to use your own CSS and HTML structure instead of needing to modify our predefined layouts to fit your needs.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 2.4 on January 5, 2023 =

* Added: Support for Custom Content fields on the Edit Entry screen (this requires GravityView 2.16.5 or newer)
* Fixed: PHP 8 notices

= 2.3 on December 10, 2020 =

* Modified Single Entry template (`/templates/entries/diy.php`) to be consistent Multiple Entries output. Before, the wrapper would output: `<div id="gv_diy_95" class="gv-diy-view">` for View #95, Entry #14257. Now, the wrapper outputs: `<div id="gv_diy_95" class="gv-diy-view gv-container gv-container-14257">`
    * Added `gv-diy-container gv-diy-single-container` CSS classes to the wrapper `<div>`
    * CSS classes are now passed through the `gv_container_class()` function
    * `gravityview_header()` is now called before `$template::entry_before()` (needed for Inline Edit)
* Fixed: Inline Edit not running in the Single Entry context

= 2.2 on May 25, 2020 =

* Warning: **this update changes the HTML structure of Before and After output!** [Read more here](https://docs.gravityview.co/article/718-version-2-2)
* Fixed: Incorrect wrapping of output tags
* Updated French translation

= 2.1.2.1 on April 23, 2019 =

* Fixed: Potential fatal error when GravityView is deactivated

= 2.1.2 on February 6, 2019 =

* **Security Fix**: Fields with "Make visible only to logged-in users" checked were shown to logged-out users. Please update promptly.

= 2.1.1 on November 1, 2018 =

* Fixed: Added "Go Back" link to Single Entry screen ([here's how to remove](https://docs.gravityview.co/article/469-diy-layout-remove-the-back-link))
* Fixed: Not working with GravityView [Ratings & Reviews extension](https://gravityview.co/extensions/ratings-reviews/) in Multiple Entries context
* Fixed: HTML Container Tag preview label disappares when closing the field settings modal
* Improved: Appearance of error message when GravityView is not available
* Updated translations - thank you, translators!
    - Polish translated by [@dariusz.zielonka](https://www.transifex.com/user/profile/dariusz.zielonka/)
    - Russian translated by [@awsswa59](https://www.transifex.com/user/profile/awsswa59/)
    - Chinese translated by [@michaeledi](https://www.transifex.com/user/profile/michaeledi/)

__Developer Notes:__

* Added: `gravityview/template/diy/entry/(before|after)` hooks that run before and after a single entry is displayed
* Added: Support for future Multiple Forms plugin
* Improved: Passed `Template_Context` to the back link
* Fixed: `gravityview/template/diy/single/(before|after)` hooks were running twice

= 2.0.2 on May 8, 2018 =

* Fixed: Error when GravityView is active but Gravity Forms is not

= 2.0 on May 8, 2018 =

* Support for GravityView 2.0
* Added: Turkish translation (thank you, Süha Karalar!)

= 1.1.1 on April 28, 2018 =

* Restored ability to add "Custom Labels" to fields in View Configuration. Labels will not appear on the front end.
* Added filters to prevent DIY Layout from adding any extra HTML
    - `gravityview-diy/wrap/multiple` _bool_ Should the entry in Multiple Entries context be wrapped in minimal HTML containers? Default: true
    - `gravityview-diy/wrap/single` _bool_ Should the entry in Single Entry context be wrapped in minimal HTML containers? Default: true
* Make "Before Content" and "After Content" fields larger, monospaced
* Fixed error when GravityView is not active
* Added translations—[become a translator here](https://www.transifex.com/katzwebservices/gravityview-diy/)
* Preparing for GravityView 2.0

= 1.0 on January 16, 2018 =

* Launch!

= 1688753365-4249 =
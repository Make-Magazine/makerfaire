=== Inline Edit by GravityView ===
Tags: gravity forms
Requires at least: 3.3
Tested up to: 4.9.8
Stable tag: trunk
Contributors: The GravityView Team
License: GPL 2

Easily edit your Gravity Forms field values without having to go to the Edit Entry screen.

== Description ==

Inline Editing is a powerful way to quickly make changes to a form entry without needing to enter an Edit Entry form individually. [Learn more about the plugin](https://gravityview.co/extensions/inline-edit/).

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Set your license key

== Changelog ==

= 1.3.1 on October 3, 2018 =

* Fixed: Wrapper HTML was still added to a View when Inline Edit was not enabled for it
* Fixed: Certain field types not working when using Inline Edit with GravityView DataTables layout
* Improved: Reduced number of calls to the database
* Improved: Always show when an update is available, even if the license is not entered
* Translated into Polish by [@dariusz.zielonka](https://www.transifex.com/user/profile/dariusz.zielonka/)

= 1.3 on July 3, 2018 =

* Added: Support for using Inline Edit with [GravityView DataTables](https://gravityview.co/extensions/datatables/)! Requires Version 2.3+ of the DataTables extension.

__Developer Updates:__

* Added: `gravityview-inline-edit/init` jQuery trigger to `window` when Inline Edit is initialized
* Added: Pass Form ID or View ID information when enqueuing scripts and styles via `gravityview-inline-edit/enqueue-(styles|scripts)`

= 1.2.6 on May 10, 2018 =

* Fixed: Inline Editing not appearing for Views when running GravityView 2.0
* Tweak: Namespaced our Bootstrap script to avoid conflicts with themes or plugins

= 1.2.4 and 1.2.5 on May 9, 2018 =

* Fixed: Error on Gravity Forms Entries screen when running GravityView 2.0
* Fixed: Settings not showing in GravityView 2.0
* Fixed: Error when running PHP 5.2.4
* Updated: Turkish, Spanish, and Dutch translations (thank you!)

= 1.2.3.1 on April 16, 2018 =

* Added: "Empty" translation string
* Updated: Spanish and Dutch (Thank you, Alejandro Bernuy and Erik van Beek!)

= 1.2.3 on March 12, 2018 =

* Fixed: Submit/Canel buttons not displaying when multiple Views embedded on a page

= 1.2.2 on December 5, 2017 =

* Fixed: Inline Edit now displays "Toggle Inline Edit" for each View embedded on a page
* Fixed: Hitting return key would not always submit inline Name fields

= 1.2.1 on November 21, 2017 =

* Fixed: Saving plugin settings
* Fixed: Using a GravityView Galactic license key now works to activate Inline Edit

= 1.2 on November 20, 2017 =

* Fixed: Editing by entry creator now works in GravityView
* Fixed: Editing empty checkboxes in Gravity Forms
* Updated translations. Thanks Erik van Beek (Dutch) and Juan Pedro (Spanish)!
* GravityView functionality now requires GravityView 1.22 or newer

= 1.1.4 on November 13, 2017 =

* Fixed: Fatal error when Gravity Forms not activated

= 1.1.3.2 on October 26, 2017 =

* Fixed: Toggling editing in GravityView does not work

= 1.1.3.1 on October 19, 2017 =

* Fixed: Potential fatal error when entry does not exist

= 1.1.3 on October 18, 2017 =

* Fixed: Conflict with "Hide Empty Fields" setting in GravityView. Field values were being wrapped with Inline Edit HTML, even if Inline Edit was disabled.
* Fixed: Users who created entries were not able to edit them in GravityView using Inline Edit
* Improved future Gravity Forms 2.3 support

= 1.1.2 on September 5, 2017 =

* Added: Support for Gravity Forms 2.3
* Fixed: "Toggle Inline Edit" link not working for some embedded Views

= 1.1.1 on August 25, 2017 =

* Fixed: Fatal error when Gravity Forms not active

= 1.1 on August 21, 2017 =

* Changed: Edit Entry and Delete Entry are now clickable while Inline Edit is enabled
* Fixed: Show that calculated fields are not editable
* Fixed: CSS selector was added to the View container, whether or not Inline Edit was enabled for the View
* Developers: Added `$original_entry` fourth parameter to the `gravityview-inline-edit/entry-updated` and `gravityview-inline-edit/entry-updated/{$type}`filters

= 1.0.3 on July 18, 2017 =

* Fixed: [Gravity Forms Import Entries plugin](https://gravityview.co/extensions/gravity-forms-entry-importer/) not able to upload files when active

= 1.0.2 on July 18, 2017 =

* Fixed: Clear GravityView cache when entry values are updated

= 1.0.1 =

* Fixed: "Toggle Inline Edit" not working in the Dashboard on non-English sites
* Fixed: If there were multiple fields of the same type with different configurations, one field would override the others. Affected radio, multiselect, address, checkbox, name fields.

= 1.0 =

- Blastoff!
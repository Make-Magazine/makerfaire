=== GravityEdit ===
Tags: gravity forms, inline edit, gravityview
Requires at least: 4.4
Tested up to: 6.2
Contributors: The GravityKit Team
License: GPL 2
Requires PHP: 7.2.0

Easily edit your Gravity Forms field values without having to go to the Edit Entry screen.

== Description ==

Inline Editing is a powerful way to quickly make changes to a form entry without needing to enter an Edit Entry form individually. [Learn more about the plugin](https://www.gravitykit.com/extensions/gravityview-inline-edit/).

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Set your license key

== Changelog ==

= 2.0 on May 11, 2023 =

* Fixed: File uploads were being saved to the WordPress upload directory, not the Gravity Forms uploads directory
* Fixed: When Inline Edit was enabled for a View, multi-file upload fields were not rendering properly
* Fixed: Speed issues related to GravityView, even when inline editing was disabled for a View
* Fixed: Incompatibility with some plugins/themes that use Laravel components
* Fixed: Removed active call to deprecated method.
* Improved: Enhanced performance when used in conjunction with Gravity Forms Populate Anything by Gravity Wiz

__Developer Updates:__

**The version includes API-incompatible changes:**

* The GravityView integration now uses GravityView's `\GV\Template_Context` instead of `GravityView_frontend::getInstance()`
* Uses GV 2.0 actions (`gravityview/template/header` instead of `gravityview_header`) and filters (`gravityview/template/field/output` instead of `gravityview_field_entry_value`)
* Adds `$context` parameter to `gravityview-inline-edit/wrapper-attributes` and `gravityview-inline-edit/{field-type}-wrapper-attributes` filters
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.12

= 1.8.5 on April 5, 2023 =

* Fixed: Potential fatal error when activating GravityView after GravityEdit
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.11

= 1.8.4 on February 20, 2023 =

**Note: GravityEdit now requires PHP 7.2 or newer**

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.9

= 1.8.3 on February 13, 2023 =

* Fixed: Compatibility with [GP Google Sheets by Gravity Wiz](https://gravitywiz.com/documentation/gravity-forms-google-sheets/?ref=263)
* Updated: [Foundation](https://www.gravitykit.com/foundation/)

= 1.8.2 on January 5, 2023 =

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.8

= 1.8.1 on December 21, 2022 =

* Fixed: PHP 8.1 notices
* Fixed: Fatal error on some hosts due to a conflict with one of the plugin dependencies (psr/log)
* Fixed: It was not possible to remove an expired license key

= 1.8 on November 30, 2022 =

* Added: You can now inline edit File Upload fields! (Including support for multiple files)
* Added: Support for editing entry metadata (Created By, Date Created, Payment Date, etc.)
* Added: Support for the Gravity Forms User Registration Add-On (When entry details are changed, changes will be made to the User profile)
* Improved: Styling of the form
* Fixed: Time field AM/PM inputs were not showing
* Fixed: Time, Number, Website, and Product fields with User-Defined Price were not updating correctly
* Fixed: Datepicker appearance

= 1.7.3 on November 14, 2022 =

* Fixed: Fatal error when loading plugin translations
* Fixed: Slow loading times on some hosts
* Fixed: Plugin failing to install on some hosts

= 1.7.2 on November 3, 2022 =

* Fixed: Fatal error when editing Number fields with calculations

= 1.7.1 on October 31, 2022 =

* Improved: Clicking on a column header to edit all fields in a column no longer requires submitting changes; changes are saved when leaving the input
* Fixed: Plugin was not appearing in the "Add-Ons" section of the Gravity Forms System Status page

= 1.7.0.2 on October 20, 2022 =

* Fixed: Potential error when the plugin tries to log an unsuccessful operation

= 1.7.0.1 on October 19, 2022 =

* Fixed: Error when trying to activate license keys

= 1.7 on October 19, 2022 =

* Added: New WordPress admin menu where you can now centrally manage all your GravityKit product licenses and settings ([learn more about the new GravityKit menu](https://www.gravitykit.com/foundation/))
    - Go to the WordPress sidebar and check out the GravityKit menu!
    - We have automatically migrated your existing GravityEdit license, which was previously entered in the Gravity Forms settings page
    - Request support using the "Grant Support Access" menu item
* Added: Live-update all calculations that rely on a number field (instead of just one)
* Added: GravityEdit icon to the Gravity Forms settings screen
* Improved: Typography and editor size for textarea fields
* Improved: Submit/Cancel button alignment is much nicer and more consistent
* Improved: Reduced number of database queries when loading GravityEdit functionality
* Fixed: Popup width was set to 100%, making it unusable in certain contexts
* Fixed: Fatal error when inline-editing an address field if GravityView is not active
* Fixed: PHP warnings when editing a Name field without the Prefix input

= 1.6 on July 28, 2022 =

* [GravityView (the company) is now GravityKit](https://www.gravitykit.com/rebrand/) and this plugin is now called GravityEdit!
* Fixed: List field editing did not work in Gravity Forms 2.5 or newer
* Added: Support for Gravity Wiz's Populate Anything and other extensions modifying form field values
* Added: Support for field calculations

__Developer Updates:__

**IMPORTANT: `GRAVITYVIEW_INLINE_*` constants were renamed to `GRAVITYEDIT_*` and future plugin versions will see similar name changes to namespaces, class names, and hooks**

= 1.5 on January 26, 2022 =

* Added: GravityView field setting to disable Inline Edit for a field ([read how set this up](https://docs.gravityview.co/article/826-how-to-enable-or-disable-inline-edit-per-field))
* Added: Support for creating entry revisions after each edit (requires the [Gravity Forms Entry Revisions](https://gravityview.co/extensions/entry-revisions/) add-on Version 1.1 or newer)
* Improved: Made the input sizes larger when using "Popup" Inline Edit Mode
* Improved: Display of Inline Edit settings in Gravity Forms 2.5+
* Updated: Polish translation (thanks, Dariusz!) and German translation (thanks, Michael!)

__Developer Updates:__

* Renamed `GravityView_Inline_Edit_GravityView::add_to_blacklist()` to `GravityView_Inline_Edit_GravityView::add_to_blocklist()`

= 1.4.4 on November 9, 2021 =

* Fixed: Inline Edit would not work in DataTables View when using a responsive mode. Requires DataTables 2.5 or newer.

= 1.4.3 on August 26, 2021 =

* Improved: The Inline Edit button no longer makes the page adjust on load on the Gravity Forms "Entries" screen
* Improved: The appearance when editing checkboxes in Gravity Forms 2.5
* Fixed: Single checkbox items would not be editable on the "Entries" screen in Gravity Forms 2.5
* Fixed: `de` German translations were unreachable; strings were merged with `de_DE`. Thanks again for the translations, Michael!

__Developer Updates:__

* Modified: Escaped the `style` attribute output in `templates/toggle.php`

= 1.4.2 on June 1, 2021 =

* Fixed: License field missing when running Gravity Forms 2.5

= 1.4.1 on April 12, 2021 =

* Fixed: Editing the Country/State/Province input of an Address field did not work
* Fixed: Fields that weren't editable were removed from the View rather than being disabled
* Fixed: Editing a Paragraph Text field did not respect the "allow HTML" View setting

= 1.4 on April 1, 2021 =

* Added: Support for Product field (User-Defined Price only)
* Fixed: Multi-input fields (e.g., Name) marked as required would fail validation when attempting to update them
* Fixed: Updating a single checkbox choice would reset other choices
* Fixed: Updating fields linking to a single entry would remove the hyperlink and reset the field value
* Fixed: It was possible to clear all checkbox choices for fields marked as required
* Fixed: Inline Edit would initialize with incorrect data when including multiple list fields in the View
* Fixed: Output formatting for certain field types would change after an update
* Fixed: Currency symbol would disappear when updating a number field
* Improved: Links to single entry are not disabled when Inline Edit is activated
* Improved: Inline Edit no longer prevents configuring visible columns on the Gravity Forms Entries screen
* Improved: Shows a message when a form or View does not have any editable fields visible
* Updated: German translation (thanks, Michael!), Russian translation (thanks, Irina!), and Chinese translation (thanks, Edi!)

= 1.3.3 on January 11, 2021 =

* Improved: If there are no entries, hide the Inline Edit button
* Improved: Don't register a bundled jQuery Cookie script if others are in use
* Fixed: Unable to override the default "Empty" string translation
* Fixed: "Enable Inline Edit" toggle would not work with the DataTables layout
* Fixed: Clicking the Inline Edit button would enable, then immediately disable, inline editing

= 1.3.2 on December 5, 2019 =

Lots of bug fixes!

* Fixed: Editing multi-column List fields
* Fixed: Duplicate "Other" radio button inputs
* Fixed: "Other" input field can't be updated if it's already selected
* Fixed: Update and Cancel buttons not appearing
* Fixed: Inline edit mode could get "stuck" on or off
* Fixed: Set `date_updated` entry property when updating entry
* Fixed: When activating column editing by clicking a field's column header in Gravity Forms, the page no longer scrolls to the last row
* Fixed: In GravityView, when Inline Editing is activated for a View using a DataTables layout, the Approval field popup does not render
* Fixed: In GravityView, after editing a field that links to a single entry, the link would be removed
* Fixed: Performance issue
* Updated: French translation, Russian translation (thanks, Viktor S!), and Turkish translation (thanks, Süha Karalar)

__Developer Updates:__

* Added: `$output` attribute to `gravityview-inline-edit/wrapper-attributes` filter

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


= 1688664048-4249 =
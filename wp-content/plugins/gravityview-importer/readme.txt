=== GravityImport ===
Tags: gravitykit, gravityview, gravity forms, import
Requires at least: 6.2
Tested up to: 6.9.4
Stable tag: trunk
Contributors: The GravityKit Team
License: GPL 3 or higher
Requires PHP: 7.2.0

The best way to import entries into Gravity Forms. Proud to be a Gravity Forms Certified Add-On.

== Description ==

Easily import Gravity Forms entries from a CSV file. Learn more on [gravitykit.com](https://www.gravitykit.com/products/gravity-forms-entry-importer/).

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress's built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 2.9.0 on March 12, 2026 =

This update adds background import processing, introduces the ability to retry failed records without re-importing successful ones, improves memory handling during imports, and fixes issues with cancelled import reporting and conditional logic settings.

#### 🚀 Added
* Background import processing:
  - Imports now run in the background — you can close the browser tab and return later;
  - A live progress notice appears across the WordPress admin with a link back to the import page;
  - Pause, resume, and cancel controls are available on the import page.
* Failed record recovery:
  - Retry only the rows that failed — using the same field mapping or an updated one;
  - Download failed rows as a CSV, including a column explaining why each row failed.

#### ✨ Improved
* Better memory handling when processing imports with many failed records.

#### 🐛 Fixed
* Conditional logic settings from a previous import no longer carry over when the option is disabled on the Configure step.
* Cancelled imports now correctly report skipped and errored rows in the summary.

#### 🔧 Updated
* [Foundation](https://www.gravitykit.com/foundation/) to version 1.12.0.

= 2.8.1 on February 26, 2026 =

This hotfix restores the missing "Ignore Field Conditional Logic" toggle in the Configure step.

#### 🐛 Fixed
* "Ignore Field Conditional Logic" toggle not appearing in the Configure step, causing imported field values to be cleared when conditional logic conditions were not met by the CSV data.

#### 🔧 Updated
* [Foundation](https://www.gravitykit.com/foundation/) to version 1.11.0.

= 2.8.0 on February 5, 2026 =

This update completely redesigns the field mapping experience with a new vertical layout, adds pre-import data validation, includes numerous usability and accessibility improvements, and fixes a fatal error when importing List field data on PHP 8+.

#### 🚀 Added
* Redesigned field mapping interface with a vertical layout that's easier to scan and work with, especially for CSVs with many columns.
* Pre-import validation warnings that highlight potential data issues like invalid emails, non-numeric values, or unrecognized date/time formats before importing.
* Checkboxes to quickly select or deselect which columns to import, with a header checkbox to select or deselect all.
* Auto-map button to reset field mappings to auto-detected values, with a confirmation dialog to prevent accidental resets.
* Cell-level validation highlighting in the data preview, making it easier to spot exactly which values need attention.
* Inline column name editing for renaming CSV column headers before importing.

#### ✨ Improved
* Validation now checks up to 50 rows of data (previously 10), giving a more accurate picture of potential issues before importing.
* Date and time formats are now auto-detected from CSV data instead of requiring manual selection.
* Datetime values with milliseconds (e.g., `2024-12-01 00:43:21.000`) are now parsed correctly.
* Smarter field matching means fewer manual adjustments when mapping CSV columns to form fields.
* Entry Notes now requires exact column name matches, preventing accidental mapping of unrelated columns.
* Better keyboard navigation: use Tab to move between fields, Enter to confirm selections, and Escape to cancel edits.
* Improved screen reader support and accessibility throughout the import interface.
* Better support for right-to-left (RTL) languages.
* The "Ignore Field Conditional Logic" option now only appears when the form actually has conditional logic.

#### 🐛 Fixed
* Fatal error when importing List field data on PHP 8+.

#### 🔧 Updated
* [Foundation](https://www.gravitykit.com/foundation/) to version 1.9.0.

= 2.7.0 on September 11, 2025 =

This update improves GravityView's Approval Status field imports and fixes PHP-related issues.

#### ✨ Improved
* Importing GravityView's Approval Status values now works with both text labels ("Approved", "Disapproved", "Unapproved") and their numeric equivalents (1, 2, 3).

#### 🐛 Fixed
* PHP 8.1+ warnings and `intl` extension conflict on some hosts.

#### 🔧 Updated
* [Foundation](https://www.gravitykit.com/foundation/) to version 1.3.1:
  - New GravityKit global settings for logging configuration (level, type, cleanup schedule, retention);
  - A unified way to display admin notices across all GravityKit products;
  - Performance improvements and bug fixes.

= 2.6.0 on July 17, 2025 =

This update improves import reliability by better managing server resources during large imports.

#### ✨ Improved
* Import performance and reliability have been enhanced with smarter memory and timeout management.
* The import process now better detects available server resources and adjusts accordingly.
* Large CSV imports are now more stable and less likely to fail due to resource constraints.

#### 🔧 Updated
* [Foundation](https://www.gravitykit.com/foundation/) to version 1.2.25.

#### 💻 Developer Updates
* Refactored server resource management into a dedicated `Server` helper class.

= 2.5.1 on November 21, 2024 =

This hotfix update addresses a non-critical PHP notice in WordPress 6.7 and newer.

#### 🐛 Fixed
* PHP notice in WordPress 6.7 caused by initializing product translations too early.

#### 🔄 Updated
* [Foundation](https://www.gravitykit.com/foundation/) to version 1.2.21.

= 2.5.0 on August 16, 2024 =

This release updates internal components and adds a filter to assist in troubleshooting and preventing network errors.

#### 🔄 Updated
* [Foundation](https://www.gravitykit.com/foundation/) was updated to version 1.2.17.

#### 💻 Developer Updates
* Added `gk/gravityimport/rest/request-validation-skip-params` filter to allow customization of which REST request parameters are excluded from validation when processing batch requests.

= 2.4.10 on January 25, 2024 =

This release introduces a new filter for developers to modify UI data, resolves a PHP 8.1+ deprecation notice, and updates the [Foundation](https://www.gravitykit.com/foundation/) framework, enhancing support for sites that use object caching plugins.

#### 🐛 Fixed
* A deprecation notice in PHP 8.1+ caused by passing `null` as a parameter to built-in string functions.

#### 🔄 Updated
* [Foundation](https://www.gravitykit.com/foundation/) was updated to version 1.2.7.
  - Improved the setting and retrieval of transients when using object cache plugins.
  - Fixed a JavaScript warning that appeared during the deactivation of license keys.

#### 💻 Developer Updates
* Added a `gk/gravityimport/ui/form-data` filter to modify the form data object for use in the UI.

= 2.4.9 on September 7, 2023 =

* Fixed: Auto-mapping of the User IP field not working
* Improved: Support for RTL languages
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.2.2

= 2.4.8 on July 12, 2023 =

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.1.1

= 2.4.7 on May 3, 2023 =

* Fixed: Incompatibility with some plugins/themes that use Laravel components
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.12

= 2.4.6 on March 9, 2023 =

**Note: GravityImport now requires PHP 7.2 or newer**

* Added: Option to ignore field conditional logic during import
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.10

= 2.4.5 on January 5, 2023 =

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.8

= 2.4.4 on December 21, 2022 =

* Fixed: PHP 8.1 notices
* Fixed: Fatal error on some hosts due to a conflict with one of the plugin dependencies (psr/log)

= 2.4.3 on December 1, 2022 =

* Fixed: It was not possible to remove an expired license key

= 2.4.2 on November 29, 2022 =

Fixed: "Undefined index" PHP notice

= 2.4.1 on November 14, 2022 =

* Fixed: Fatal error when loading plugin translations
* Fixed: Slow loading times on some hosts
* Fixed: Plugin failing to install on some hosts

= 2.4.0.3 on October 31, 2022 =

* Fixed: Plugin was not appearing in the "Add-Ons" section of the Gravity Forms System Status page

= 2.4.0.2 on October 20, 2022 =

* Fixed: Potential error when the plugin tries to log an unsuccessful operation

= 2.4.0.1 on October 19, 2022 =

* Fixed: Error when trying to activate license keys

= 2.4 on October 19, 2022 =

* Added: New WordPress admin menu where you can now centrally manage all your GravityKit product licenses and settings ([learn more about the new GravityKit menu](https://www.gravitykit.com/foundation/))
    - Go to the WordPress sidebar and check out the GravityKit menu!
    - We have automatically migrated your existing GravityImport license, which was previously entered in the Gravity Forms settings page
    - Request support using the "Grant Support Access" menu item

__Developer Updates:__

* Removed actions:
    - `gravityview/import/settings/before`
    - `gravityview/import/settings/after`
    - `gravityview-import/before-settings`
    - `gravityview-import/after-settings`

= 2.3.0.2 on July 27, 2022 =

* Fixed: GravityImport license section was missing from the Gravity Forms Settings screen

= 2.3.0.1 on July 18, 2022 =

* Fixed: The option to import entries was missing from the Gravity Forms Entries and WordPress Import screens

= 2.3 on July 7, 2022 =

* [GravityView (the company) is now GravityKit](https://www.gravitykit.com/rebrand/) and this plugin is now called GravityImport!
* Fixed: It was not possible to access form field properties in Gravity Forms 2.6+ if the form was created during import

__Developer Updates:__

**IMPORTANT: `GV\Import_Entries` namespace was renamed to `GravityKit\GravityImport` and future plugin versions will see similar name changes to the REST namespace, constants, and hooks**

= 2.2.6 on January 27, 2022 =

* Tested with WordPress 5.9
* Improved: Warning message that's displayed when CSV data is mapped to an Entry ID field now clearly states that all existing entry data will be deleted
* Fixed: Import fails to start when `pt_PT_ao90` (variant of `pt_PT`) locale is used

= 2.2.5 on October 1, 2021 =

* Fixed: Import would fail if [GravityExport](https://www.gravitykit.com/products/gravityexport/) is installed

= 2.2.4 on August 31, 2021 =

* Fixed: New form creation capabilities were being checked when importing into an existing form
* Fixed: If specified, hours and minutes were not being saved when updating "Entry Date", "Entry Updated Date" and "Payment Date" entry properties

= 2.2.3 on July 20, 2021 =

* Fixed: Zeros could not be imported into a number field

= 2.2.2 on May 6, 2021 =

* Fixed: Compatibility with [Gravity Forms Zero Spam plugin](https://wordpress.org/plugins/gravity-forms-zero-spam/)
* Fixed: Import may silently fail when the database contains a large number of entries (thanks, Alexander St. John!)
* Fixed: PHP warnings when running [Gravity Forms Entries in Excel](https://wordpress.org/plugins/gf-entries-in-excel/)
* Tweak: Modified code to improve future support for [Image Hopper](https://imagehopper.tech)

= 2.2.1 on January 6, 2021 =

* Fixed: Gravity Forms User Registration feed was not being processed

= 2.2 on December 7, 2020 =

* Import Entries now requires PHP version 5.6 or newer
* Added: Support for WordPress 5.6
* Improved: Allow setting UTC timezone (as expected by WordPress) for "Entry Date", "Entry Updated Date" and "Payment Date" entry properties
* Improved: Gravity Forms 2.5 support
* Fixed: Importing Multiple Files for the File Upload field
* Updated: Turkish translation (thanks, Süha!) and Russian translation (thanks, Irina!)

= 2.1.10 on October 5, 2020 =

* Fixed: Fatal error when Gravity Forms is inactive
* Fixed: PHP warnings in WordPress 5.5
* Improved: Support for Gravity Forms 2.5

= 2.1.9 on July 28, 2020 =

* Added support for Gravity Forms 2.5
* Updated: Polish translation (thanks, Dariusz!)

= 2.1.8 on May 5, 2020 =

* Improved: Reliability when importing large CSVs
    - Import requests will be automatically re-attempted if they fail
    - Memory usage is improved, which should help for sites on shared hosting
* Fixed: Import error message not properly displayed
* Fixed: When the import was interrupted, the last record would not have its status set to 'processed', so resuming the import would start from the next record.

= 2.1.7 on April 16, 2020 =

* Added: Button to unmap all fields with a single click
* Improved: Allow importing checkbox field data from a single CSV column
* Fixed: Import would break if a form had submit button conditional logic enabled
* Fixed: Date in "YYYY-MM-DD hh:mm:ss" format not being recognized as valid
* Fixed: Empty Entry Notes objects not being recognized as valid

= 2.1.6 on February 13, 2020 =

* Fixed: When updating entries, they become unapproved (and other entry meta details cleared)
* Fixed: Empty Signature field values were not properly handled
* Fixed: Duplicated successful import message
* Fixed: Fatal error possible when activating a development version of Import Entries

= 2.1.5 on December 11, 2019 =

* Fixed: "Beacon is not defined" error when navigating to Import Entries screen
* Fixed: Time in "H:MM AM" format was being recognized as valid when it wasn't
* Fixed: Checkbox/Radio/Drop Down fields with "No" or "Yes" values are now imported properly
* Fixed: Issue importing empty Multi-Select fields

= 2.1.4 on November 18, 2019 =

* Confirmed compatibility with WordPress 5.3
* Fixed: Appearance-only conflict with WordPress 5.3
* Fixed: When CSVs contained invalid characters, the CSV containing the errors would fail to be generated
* Fixed: PHP warnings on the Gravity Forms logging page

= 2.1.3 on November 8, 2019 =

* Fixed: PHP error on servers running PHP 5.6
* Fixed: Compatibility with the [Invisible reCaptcha](https://wordpress.org/plugins/invisible-recaptcha/) plugin
* Fixed: Format the number of rejected items during an import according to locale
* Fixed: Sometimes error logs were empty

__Developer Updates:__

* Added: `gravityview/import/run/batch` filter to alter the batch before it's run

= 2.1.2 on October 23, 2019 =

* Improved: Force Gravity Forms to show whether the Add-On Feeds table exists in the database
* Fixed: Disable Gravity Forms Akismet integration when importing entries
* Fixed: Compatibility with the [Zero Spam](https://wordpress.org/plugins/zero-spam/) plugin
* Fixed: Use `POST` instead of `PUT` for REST API calls, for compatibility purposes
* Fixed: Warnings on malformed or empty Entry Notes fields
* Fixed: Invalid UTF-8 character breaks the field mapping screen
* Fixed: Minor display issues with WordPress 5.3
* Fixed: JavaScript errors in field mapping
* Updated: Russian translation (thanks, Viktor S!)

= 2.1.1 on October 16, 2019 =

* Improved: Allow changing Name field labels when creating a new form
* Improved: Lots of improvements to error handling when sites are unresponsive or plugins conflict
* Improved: Make sure to always show Feed database tables on the Gravity Forms System Status page
* Fixed: Allow configuring payment date format
* Fixed: Make sure that Feeds are not processed unless checked
* Fixed: Warnings when importing products with Radio input types
* Fixed: Importing products with radio inputs and drowpdowns
* Fixed: Importing into sites when not using "pretty permalinks"
* Fixed: Fix sequential field ID assignment bug
* Fixed: Conflict with the WishList Member Debug Mode
* Updated: Polish translation (thanks, Dariusz!)

= 2.1 on September 19, 2019 =

* Added: Option to skip validation for imported data
* Improved: Allow returning to configuration step after import failure
* Improved: Widen column names in the Map Fields step
* Improved: Allow importing two-digit year formats (`09/09/19`)
* Improved: Don't automatically map User ID columns
* Improved: Save configuration when changing new form field labels
* Improved: Removed "Credit Card" fields from the list of fields available to import
* Fixed: Sub-labels for new Address fields were not being properly set
* Fixed: Skip completely empty rows
* Fixed: Import stats are now localized properly
* Updated: 100% Polish translation! (thank you, Dariusz!)

__Developer Updates:__

* Added: `/test/` REST API endpoint for future health checks for ability to import
* Added: `gravityview/import/field/validate` and `gravityview/import/entry/validate` filters, allowing you to modify whether or not to suppress validation for entries or specific fields
* Renamed `gravityview/import/export/note_types/blacklist` filter to `gravityview/export/note_types/blacklist`
* Added: `$column` parameter to `gravityview/import/parse/typemap` filter
* Moved: `gravityview/import/fields/multi-input` out of the column loop
* Added: `gravityview/import/settings/enabled` filter
    - Deprecated: `gravityview-import/show-settings`
* Added: `gravityview/import/settings/before` action
* Added: `gravityview/import/settings/after` action
    - Deprecated: `gravityview-import/before-settings`
    - Deprecated: `gravityview-import/after-settings`

= 2.0.2.2 on August 7, 2019 =

* Fixed: License activation script not loading for some sites

= 2.0.2.1 on August 6, 2019 =

* Fixed: Issue affecting some sites where administrators were unable to access the Import Entries screen

= 2.0.2 on August 5, 2019 =

* Added: Add WordPress 5.0+ plugin requirement
    * This fixes issues with scripts not loading and not being able to access the Import Entries page
    * Shows a notice when the plugin is running on a site running less than WordPress 5.0
* Improved: Hide "existing form" option when no forms are available
* Improved: Do not resume interrupted import after 2 hours
* Fixed: Do not resume when uploaded file is no longer available

__Developer Updates:__

* Added hooks:
    * Filter `gravityview/import/processor/args`
    * Action `gravityview/import/processor/init`
* Restored more v1 filters, and also started transition to new filter naming structure
    * `gravityview-import/import-cap` is deprecated. Use `gravityview/import/rest/cap` instead.
    * `gravityview-importer/use-default-value` is deprecated. Use `gravityview/import/column/default` instead.
    * `gravityview-importer/after-update` is deprecated. Use `gravityview/import/entry/updated` instead.
    * `gravityview-importer/after-add` is deprecated. Use `gravityview/import/entry/created` instead.

= 2.0.1 on July 31, 2019 =

* Added: Export List fields as JSON, which makes it much easier to import. [Here's how!](https://docs.gravitykit.com/article/615-exporting-and-importing-list-fields)
* Improved: Compact display makes it easier to preview entries at a glance
* Improved: Error reporting
    - Make it easy to contact support when plugin errors occur
    - Print the full error report in the browser
    - Display a warning when a CSV column is auto-mapped to Entry ID
    - Show message when JavaScript is not loading or is disabled
* Improved: Better CSV cleanup when import is complete
* Fixed: "Ignore Required" not working properly
* Fixed: When updating entries, ensure only entries connected to the form are imported
* Fixed: Prevent auto-mapping multiple columns to the same field
* Fixed: CSV upload issue on Windows servers
* Fixed: Display issues with Internet Explorer 11

__Developer Updates:__

* Added: `gravityview/import/process/{$status}` `new`, `parsing`, `parsed`, `processing`, `error`, `done`
* Restored many v1 filters, and also started transition to new filter naming structure
    * `gravityview-import/user-agent` => `gravityview/import/user-agent`
    * `gravityview-importer/config` => `gravityview/import/config`
    * `gravityview-importer/process-row` => `gravityview/import/process/row`
* Renamed v1 filters that weren't migrated:
    * `gravityview/import/process/row/error` is now `gravityview-importer/add-entry/error`
    * `gravityview-importer/invalid-row` is now `gravityview/import/process/row/skipped`
* A doc is coming soon with v1 to v2 filter and action details. If you have any questions, [ask support](mailto:support@gravitykit.com).

= 2.0 on July 24, 2019 =

We have been working on this update to the Entry Importer for over 8 months, and we're thrilled to share it with you.

**A powerful new version - tons of new functionality!**

- [Create new fields when importing to existing forms](https://docs.gravitykit.com/article/604-add-new-field-during-import)
- [Create a new form from a CSV during import](https://docs.gravitykit.com/article/605-create-new-gravity-form-csv)
- Supports importing:
    - [Multi-Column List fields](https://docs.gravitykit.com/article/612-importing-list-fields) (yep, this works great!)
    - Signature fields
    - Quiz, Poll, and Survey fields
- And so much more!

[Read all the docs here](https://docs.gravitykit.com/category/255-gravity-forms-importer)

_Special thanks to Vlad and Gennady for their hard work on this release!_

= 1.3.5.3 on April 30, 2019 =

* Fixed: Issue importing Entry Notes introduced in 1.3.5.2

= 1.3.5.2 on March 30, 2019 =

* Fixed: Issue with incomplete or outdated information being passed to Gravity Forms hooks. This fixes an issue with Gravity Flow automation triggers. (Thanks, Steve Henty with Gravity Flow!)
* Fixed: Issue uploading CSV files that contain non-Latin characters in the filename
* We're nearly done with Import Entries Version 2.0 - a complete re-write! Enable beta updates to get early access. [Here's how to enable beta updates!](https://docs.gravitykit.com/article/571-how)

= 1.3.5.1 on October 15, 2018 =

* Fixed: Fatal error on activation

= 1.3.5 on October 12, 2018 =

* Improved: Gravity Forms background processing is now triggered after import
* Fixed: "No Duplicates" setting was not being respected
* Fixed: Gravity Forms Zapier Add-On feeds were not appearing in the Feeds list
* Fixed: Fatal error on plugin page if the server doesn't support `iconv()`
* Updated translations - thank you, translators!
    - Polish translated by @dariusz.zielonka
    - Russian translated by @awsswa59
* We skipped Version 1.3.4, you're not imagining things

= 1.3.3 on January 30, 2017 =

* Added: Gravity Forms 2.3 compatibility
* Added: Additional filters to control validation (`gravityview/importer/validate-field` and `gravityview/importer/validate-entry`)
* Added: "Beta" setting to opt-in to receiving updates when a new version of Import Entries is ready for testing
* Fixed: Prevent validation warnings after uploading files
* Fixed: Importing multi-select values into forms created with Gravity Forms 2.2+
* Fixed: Fix "Invalid number" PHP warning
* Fixed: Fatal error when parsing a file fails
* Fixed: Fatal error when attempting to find feeds from Addons connected with a form
* Translation updates for German, Danish, Turkish & French
* Now requires Gravity Forms 2.0 or newer

= 1.3.1 & 1.3.2 on September 2, 2016 =
* Fixed: Support for non-Latin file names
* Fixed: Empty error box on the Map Fields screen
* Fixed: PHP warning

= 1.3 on March 25, 2016 =
* Added: Support for processing Add-on feeds, like User Registration

= 1.2 on March 18, 2016 =
* Added: Support for Product Option fields and Credit Card fields
* Added: Support for Quiz Addon and Poll Addon fields
* Added: Support for importing "Is Read" and "Is Starred" values (use `1` (yes) or `0` (no))
* Fixed: Conflict with Members plugin
    * Allow users with `gravityforms_import_entries` capabilities to import entries. Call the Nobel Prize Committee!
* Fixed: List field importing with fields that have multiple columns. There were duplicate "List" fields in the mapping drop-down, and if those options were selected, list fields would not import properly.
* Fixed: Import multiple checkboxes formatted as CSV values
    * Tweak: Allow for whitespace after each comma
* Fixed: When mapping an uploaded file, the first column name would sometimes be wrapped in quotes
* Fixed: Double quotes (`"`) in header would be shown as two double quotes (`""`)
* Fixed: Translation files not being generated.
* Fixed: When Gravity Forms "No-Conflict Mode" enabled, license activation didn't work
* Tweak: Grouped related fields together in drop-down, now under groups like "Payment Details" and "Entry Notes"
* Tweak: Use blog encoding as default, since that's how Gravity Forms works (previously, UTF-8 was always default)
* Tweak: Added "Gravity Forms Entries" link in WordPress Tools > Import page

= 1.1.3 on February 4, 2016 =
* Fixed: Import and Settings screens potentially not showing
* Fixed: The Changelog and automatic updates were showing for GravityView, not the Entry Importer plugin
* Fixed: Gravity Forms Importer requires Gravity Forms 1.9.5, not Gravity Forms 1.9

= 1.1.2 on August 7 =
* Fixed: Files not displaying as uploaded for Windows servers
* Fixed: PHP warning for undefined variable $action (thanks, Robert!)
* Fixed: Abstract function declaration style (thanks, Robert!)

= 1.1.1 on July 17 =
* Fixed: Javascript conflict with other Gravity Forms Addon feed configuration screens

= 1.1 on June 27 =
* Added: Update existing entry details by specifying an Entry ID field. [Read more](https://docs.gravitykit.com/article/257-formatting-guide-csv-import#field-pre-defined-text#field-entry-id)
* Fixed: Issue imported fields with `0` or `0.00` values
* Fixed: Issue where imports fail because of the "mapped fields were empty" error
* Fixed: Date Field formats respect field "Date Format" settings
* Fixed: Issue where multiple imported columns had the same title
* Fixed: Importing Checkbox fields
* Fixed: Date Fields now respect the date formats set in the Gravity Forms field settings
* Added: Developer actions `gravityview-importer/add-entry/added` and `gravityview-importer/add-entry/error` that are triggered after each entry is imported
* Fixed: Duplicate "Use Default Values" configuration option
* Improved file format handling to use the blog encoding as the "To" format
* Updated the [formatting guide for Multi Select fields](https://docs.gravitykit.com/article/257-formatting-guide-csv-import#field-pre-defined-text)
* Tweak: Only show Admin settings when Update Entry & Update Post fields are mapped
* Tweak: Fixed incorrect existing entry count
* Fixed: If not using PHP 5.3 or higher, show a notice
* Added: The `gravityview-importer/strict-mode/fill-checkbox-choices` filter

= 1.0.7 on May 12 =
* Fixed: Prevent "Your emails do not match" error when Email field has "Enable Email Confirmation" enabled
* Fixed: Mapping "Created By" was not properly assigning imported entries to the defined user
* Fixed: JSON-formatted Post Image field imports
* Fixed: JSON-formatted Post Tags displaying as JSON in the Gravity Forms Entry
* Fixed: For web hosts without the `mb_convert_encoding()` function, add an alternative
* Fixed: PHP notice related to compatibility with Gravity Forms `get_field_map_choices()` method
* Fixed: Make sure that `__DIR__` is defined on the server

= 1.0.6 on May 4 =
* Fixed: Fatal error during import if a name could not be parsed

= 1.0.5 on April 30 =
* Fixed: "There was a problem while inserting the field values" error on some server configurations
* Updated: Hungarian translation (thanks Robert Tokar!)
* Added: Additional information when displaying an error returned by Gravity Forms
* Fixed: PHP warning caused by CSV parsing library

= 1.0.4 on April 29 =
* Fixed: PHP version 5.3 compatibility

= 1.0.3 on April 29  =
* Added: Support for field Default Values
* Fixed: Name and Address field validation issues
* Fixed: Set width for Field Mapping dropdowns to prevent overflow
* Fixed: Updating Post Data
* Fixed: Show all import-blocking errors for each row in the report, not just one per row
* Fixed: Show better phone formatting error
* Updated translations:
    - Bengali (thanks @tareqhi)
    - Hungarian (thanks @Darqebus)

= 1.0.2 =
* Fixed: Fatal error when handling import in some installations
* Fixed: Set max width for drop-downs in Conditional Logic section
* Updated: Translations

= 1.0.1 Beta =
* Allow for changing character set of imported file ([read how](https://docs.gravitykit.com/article/258-exporting-a-csv-from-excel#charset))
* Fixed PHP notices and a fatal error
* Don't show "Download File with Errors" button when there are no added entries
* Fix support for TSV files, allow Text files

= 1.0 Beta =

* First preview release
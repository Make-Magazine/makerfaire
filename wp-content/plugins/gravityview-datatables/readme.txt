=== GravityView - DataTables Layout ===
Tags: gravityview
Requires at least: 4.4
Tested up to: 6.7.1
Stable tag: trunk
Contributors: The GravityKit Team
License: GPL 3 or higher

Display entries in a dynamic table powered by DataTables & GravityView.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress's built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 3.5.2 on April 9, 2025 =

This release fixes a radio field filter displaying incorrectly and a PHP notice when viewing entries in a lightbox.

#### 🐛 Fixed
* Radio field filter appeared as a single radio input instead of a dropdown with values.
* PHP notice when viewing an entry in a lightbox.

= 3.5.1 on January 16, 2025 =

This release resolves issues with client-side filtering, date range filters, and navigation from Single Entry pages.

#### 🐛 Fixed
* Client-side filtering did not work for inputs containing special characters or accents (e.g., ä, ß, İ).
* Date field filters set to a range were incorrectly displayed as a single input instead of two separate fields.
* When a View is embedded in a page or post, the "Back" button on the Single Entry page using the DataTables layout did not return to the page where the View is embedded.

= 3.5 on October 1, 2024 =

This update introduces a new option to control table state persistence and fixes several bugs affecting table display, export functionality, and field filters.

#### 🚀 Added
* Option to enable/disable table state persistence (saving pagination, sorting, etc.).

#### 🐛 Fixed
* Loading message not disappearing after an empty Ajax response.
* Incorrect header labels in copied/exported data when columns are hidden.
* Multi Select field filter appeared as a text input instead of a dropdown.
* View was not hidden when the corresponding option was enabled in the Multiple Entries settings.
* Clearing search when using client-side processing did not refresh the table.

= 3.4 on August 28, 2024 =

This update adds  support for the Gravity Forms Chained Selects add-on, improves sorting and filtering behavior, and fixes several bugs.

#### 🚀 Added
* Support for the Gravity Forms Chained Selects add-on.

#### ✨ Improved
* Sorting arrows are hidden from column headers when fields are not sortable.

#### 🐛 Fixed
* Sorting for text and date fields when the "Data Processing Mode" option is set to "preloaded".
* Drop Down field filter escaped special characters, such as "&", in the displayed options.
* Creating a new View from inside the Gravity Forms form editor prevented the selection of the View type.
* Filtering by a Checkbox field with multiple values selected did not return the expected results.
* The View was hidden despite there being entries when the "No Entries Behavior" was set to "Hide the View".
* Exporting or copying table data with field filters that use a drop-down list included all available options in the header label for that field.

= 3.3.7 on May 15, 2024 =

This release addresses issues with date range filtering and field dropdowns while adding support for future GravityView functionality.

#### 🚀 Added
* Support for a future GravityView ability to define a different layout for Single Entry screens.

#### 🐛 Fixed
* Date range filtering not working with Gravity Forms Date fields.
* Row Group field dropdown not reflecting the current setting when the selected field uses a custom label.

= 3.3.6 on March 19, 2024 =

This update makes the Scroller extension work with the FixedColumns and FixedHeader extensions, and fixes the preview of GravityView's Gutenberg blocks that use the DataTables layout.

#### 🐛 Fixed
* Scroller extension can now be used with the FixedColumns and FixedHeader extensions.
* Resolved an issue preventing GravityView's Gutenberg blocks for Views from previewing when using the DataTables layout.

__Developer Updates:__

* Added: `gk/gravityview/datatables/output/before` action hook that runs before generating the output data.

= 3.3.5 on March 7, 2024 =

This update fixes an issue with exporting, printing, or copying table data in responsive mode, enhancing your data management capabilities.

#### 🐛 Fixed
* Table data grouped under a single column in responsive mode now exports, prints, and copies as expected.

= 3.3.4 on March 4, 2024 =

This release fixes issues related to pagination, auto-update functionality, entry note submission, and more.

#### 🐛 Fixed
* Pagination is no longer limited to a single page when the View includes a "Created By" field.
* Auto-update functionality is now working.
* Entry note submission is now working as expected.
* Resolved an issue with text escaping when there are no entries or search results.
* Hidden columns are now excluded when exporting, printing, or copying table data.

= 3.3.3 on February 29, 2024 =

This release fixes issues with date sorting, search bar operability in multi-View setups, and "Created By" field filtering.

#### Fixed 🐛
* Incorrect results when sorting by date with client-side processing enabled and field filters disabled.
* Search bar of a non-DataTables View not working when  placed on the same page with the DataTables View.
* "Created By" field filtering issue when the field is configured to display values other than the user's ID.

This release ensures that the "number of entries per page" setting is respected for existing Views prior to updating to version 3.3, and improves the styling of field filters.

#### 🐛 Fixed
* Number of entries per page setting not being respected for existing Views after updating to version 3.3.

#### ✨ Improved
* Styling of field filters.

= 3.3.1 on February 16, 2024 =

This release addresses minor issues in client-side processing mode introduced in version 3.3.

#### 🐛 Fixed
* Field filters now trigger only a single Ajax request when client-side processing is enabled.
* Disabling DataTables' [search filter](https://docs.gravitykit.com/article/201-how-to-disable-the-datatables-search-filter) no longer disables field filtering if client-side processing is enabled.

= 3.3 on February 14, 2024 =

💌 This lovely release introduces client-side processing for DataTables, offering instant filtering, sorting, and pagination directly in your browser. Enjoy powerful new filtering options and enhanced compatibility with GravityWiz's Populate Anything Add-On.

#### 🚀 Added
* Support for client-side processing in DataTables, enabling instant filtering, sorting, and pagination without server interaction.
  - This major update makes working with your data faster and more efficient.
  - [Learn more about the new client-side processing feature](https://www.gravitykit.com/announcing-datatables-3-3/).
* The ability to configure per-column filtering in the View settings.
* Support for GravityWiz's Populate Anything Add-On in field filters that use the "Select" input type.

#### 🐛 Fixed
* A JavaScript error occurring when attempting to group rows by field values containing certain characters.
* The appearance of HTML tables nested within View fields.
* The footer calculation result not being hidden when the associated column was hidden.

= 3.2 on June 29, 2023 =

* Added: Support for **grouping by row values!** [Learn more about the new, powerful grouping feature](https://www.gravitykit.com/announcing-datatables-3-2/)

= 3.1.2 on May 17, 2023 =

* Fixed: Field filters not working under specific conditions
* Fixed: JavaScript error caused by delayed search when the "Hide View data until search is performed" option is enabled

= 3.1.1 on May 16, 2023 =

* Fixed: Style not loading for the FixedHeader DataTables extension

= 3.1 on May 16, 2023 =

* Fixed: "Enable Inline Edit" toggle disappearing when using GravityEdit version 2.0 or newer

= 3.0.1 on April 20, 2023 =

* Fixed: Error while fetching data after clearing a search

= 3.0 on April 20, 2023 =

* Added: **Deep-dive into your data using per-column filtering!** [Learn more about the new column filtering feature](https://docs.gravitykit.com/article/931-datatables-column-filters)
* Added: Support for GravityView 2.17's "No Entries Behavior" setting (support for showing a form or redirecting to a URL when a View has no entries)
* Improved: The "Loading…" text is now always visible, regardless of the table size
* Improved: The "Show {x} entries" dropdown default appearance
* Modified: DataTables scripts, which changed the "Loading…" styling
* Modified: Requires GravityView 2.15 or newer
* Fixed: "Hide View data until search is performed" compatibility with GravityView 2.17.1

__Developer Updates:__

* Modified: `gravityview_datatables_loading_text` filter output is no longer run through `esc_html()`, allowing HTML to be used in the loading text
* Modified: Loading text is now wrapped in `<div class="dataTables_processing_text"></div>` to allow for styling
* Updated: DataTables script from 1.10.20 to 1.12
    - Buttons from 2.2.2 to 2.3.6
    - FixedColumns from 4.0.2 to 4.2.2
    - FixedHeader from 3.2.2 to 3.3.2
    - Responsive from 2.2.9 to 2.4.1
    - Scroller from 2.0.5 to 2.1.1
* Fixed: jQuery Migrate deprecation warnings

= 2.6 on December 21, 2021 =

* Added: Auto-Update setting! DataTables will automatically refresh with new entries as they're added. [Learn more about the new Auto-Update feature!](https://docs.gravitykit.com/article/821-enable-auto-update-datatables-setting)
    - You control how frequently the data refreshes. By default, it's every 5 minutes.

= 2.5.1 on November 4, 2021 =

* Fixed: DataTables scripts would not load when manually outputting the contents of the `[gravityview]` shortcode. Requires GravityView 2.13.4 or newer.

= 2.5 on October 7, 2021 =

* Fixed: GravityView Entry Approval not working when in responsive mode (requires GravityView 2.13.2 or newer)
* Fixed: Images displayed in a lightbox were duplicated when in responsive mode
* Fixed: Searching while FixedColumns breaks the table layout
* Fixed: DataTables scripts were being enqueued on every page load
* Fixed: DataTables scripts were enqueued on every page load
* Fixed: The field setting "Custom CSS Class" was not being used in the output
* Improved: Minor security fix

__Developer Updates:__

* Modified: Changed enqueuing of scripts to happen from the `gravityview/template/after` action
    * This means GravityView 1.x template overrides will no longer function for DataTables. See our [template migration guide](https://github.com/gravityview/GravityView/wiki/Template-Migration) for more information.
* Modified: Removed support for exporting using deprecated .swf behavior in DataTables
* Modified: Removed use of global `gvDTButtons` JavaScript variable on the front-end

= 2.4.8.1 on July 27, 2021 =

* Fixed: Interference with a non-DataTables layout search widget clear button

= 2.4.8 on July 19, 2021 =

* Fixed: Search widget clear/reset button would not refresh DataTables results
* Fixed: When using the Print button, single check marks were not shown. Now a check mark emoji will be shown ✔️

= 2.4.7 on December 10, 2020 =

* Fixed: Inline Edit not working in Single Entry context

= 2.4.6 on September 24, 2020 =

* Now the plugin dynamically refreshes events in [our Gravity Forms Calendar plugin](https://www.gravitykit.com/extensions/calendar/) if using "Entries shown on page" setting in the Calendar widget
* Updated: Russian translation (thanks, Irina F.!) and Turkish translation (thanks, Süha K!)

__Developer Updates:__

* Updated: DataTables script from 1.10.20 to 1.10.21
    - Buttons from 1.6.1 to 1.6.4
    - FixedColumns from 3.3.0 to 3.3.2
    - FixedHeader from 3.1.6 to 3.1.7
    - Responsive from 2.2.3 to 2.2.6
    - Scroller from 2.0.1 to 2.0.3
* Added: Global window events when DataTables makes/completes AJAX request and redraws
* Modified: Additional argument for the `gravityview/datatables/output` filter that passes a collection of filtered entries

= 2.4.5 on April 29, 2020 =

* Truly fixed: Multi-sorting not working

= 2.4.4 on April 29, 2020 =

* Fixed: Revert the broken 2.4.3 release. Sorry!

__Developer Updates:__

* Added: Prevent saving session data in browser when adding `?cache` to URL
* Added: Support for modifying "No entries match your request" string using View Settings, coming soon to GravityView
* Modified: "No entries match your request." and "Loading data..." are now sanitized using `esc_html()`

= 2.4.3 on April 27, 2020 =

* Fixed: Multi-sorting not working

__Developer Updates:__

* Updated scripts:
    - DataTables 1.10.19 to 1.10.20
    - Buttons 1.6.0 to 1.6.1

= 2.4.2 on December 12, 2019 =

* Fixed: DataTables not working with Multiselect, Checkbox searches
* Fixed: The `{get}` Merge Tag doesn't work in the `[gravityview]` shortcode when rendering a DataTables layout

= 2.4.1 on October 9, 2019 =

* Fixed: Fields added using [Multiple Forms](https://www.gravitykit.com/extensions/multiple-forms/) don't display on a Single Entry page
* Updated translations:
    - Russian (Thanks, Viktor S!)
    - Polish (Thanks, Dariusz Z!)
    - French

__Developer Updates:__

* Updated DataTables scripts:
    - Scroller 2.0 to 2.0.1
    - FixedHeader 3.1.5 to 3.1.6
    - FixedColumns 3.2.6 to 3.3.0
    - Buttons 1.5.6 to 1.6.0

= 2.4 on April 2, 2019 =

* Added: Support for multi-sorting columns - [learn how!](https://docs.gravitykit.com/article/569-multi-sorting-columns-datatables) (_requires GravityView 2.3_)
* Added: Allow users to show and hide columns with a new button ("Column Visibility")
* Removed the border around the Scroller "Loading data..." message

__Developer Updates:__

* Added `gravityview_datatables_button_labels` filter to modify the Buttons labels
* Updated: Scroller to 2.0 ([read about the changes](http://cdn.datatables.net/scroller/2.0.0/))
* Updated: Buttons to 1.5.6 from 1.5.4 ([read about the changes](http://cdn.datatables.net/buttons/1.5.5/))

= 2.3.4 on December 20, 2018 =

* Fixed: Error loading more than two DataTables Views on a page
* Fixed: Error when using the Search Bar "Reset" link on non-DataTables Views
* Fixed: Allow for multiple embeds of the same View with different settings passed using the shortcode
* Fixed: Compatibility with the [Multiple Forms plugin](https://www.gravitykit.com/extensions/multiple-forms/)

= 2.3.3 on November 1, 2018 =

* Fixed: Date Range searches
* Fixed: FixedHeaders functionality not working when using FixedColumns and Responsive settings

= 2.3.2 on October 29, 2018 =

* Fixed: The field setting "Make visible only to logged-in users" hides the content, but not hide the table headers; this can cause visible table column headers to be improperly labeled
* Updated: DataTables Buttons script to latest version (For developers: [see version diff here](https://github.com/DataTables/Buttons/compare/1.5.3...1.5.4))
* Updated: Chinese translation (Thanks, Edi Weigh!)

= 2.3.1 on September 23, 2018 =

* Added: Clear search results without a page refresh
* Updated: Polish, Russian, and Turkish (Thank you, [@dariusz.zielonka](https://www.transifex.com/user/profile/dariusz.zielonka/), [@awsswa59](https://www.transifex.com/user/profile/awsswa59/), and [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/)!)

__Developer Updates:__

* Added: `gravityview/datatables/output/entry` filter to modify entry values before being rendered by DataTables
* Fixed: The CSS classes defined in "Custom CSS" field settings were not being added to the table headers
* Modified: Improve the speed of requests by removing WordPress widget actions during DataTables AJAX requests

= 2.3 on July 3, 2018 =

This is a big update, bringing some great new functionality to DataTables!

* Added: Searching with the Search Bar now refreshes results live—no refresh needed!
* Added: Support for [GravityView Inline Edit](https://www.gravitykit.com/extension/inline-edit/) (requires Inline Edit 1.3 or newer)
* Fixed: Results being cached during search
* Fixed: "Hide empty fields" setting not working in "Single Entry" context
* Fixed: Entry Notes not working in DataTables (requires GravityView 2.0.13)

__Developer Updates:__

* Added: New `gravityview/datatables/output` filter to modify the output right before being returned from the AJAX request
* Added: New setting accessible via the `gravityview_datatables_js_options` filter, in `$settings[ajax][data][setUrlOnSearch]`. The setting affects whether to update the URL with `window.history.pushState` when searching with the Search Bar (default: true)
* Modified: Added `data-viewid` attribute to `templates/views/datatable/datatable-header.php` to support no-refresh searching
* Updated: `Entry_DataTable_Template` class to inherit from `Entry_Table_Template` for rendering
* Updated: DataTables scripts
* Removed: `gravityview/entry/cell/attributes` filter introduced in 2.2

= 2.2.2.1 on June 1, 2018 =

* Fixed: PHP warning "Undefined Index: 'type'"

= 2.2.2 on May 29, 2018 =

* Fixed: Respect parameters passed to a DataTables View using the shortcode

__Developer Updates:__

* Modified: When entries are loaded, remove the `.gv-container-no-results` and `.gv-widgets-no-results` CSS classes from View and GravityView Widget containers

= 2.2.1.3 on May 16, 2018 =

* Modified: When Search Bar is configured, disable DataTables built-in search; otherwise, enable DataTables search
* Fixed: "Hide View data until search is performed" not working
* Fixed: Notice about DataTables requiring update

= 2.2.1.1 on May 16, 2018 =

* Fixed: `[gv_entry_link]` links pointing to the wrong URL when a View is embedded

= 2.2.1 on May 11, 2018 =

* Fixed: Edit Entry and Delete Entry links were going to the wrong URL
* Fixed: Rows were full-height when "Scroller" option was enabled
* Fixed: Restore ability for developers to define custom "Row Height"
* Fixed: Only load scripts and styles for DataTables features if they are active

= 2.2 on May 8, 2018 =

* Now requires GravityView 2.0
* Entries load much faster than before (thanks to GravityView 2.0)
* Updated DataTables scripts

= 2.1.3 on April 28, 2018 =

* Getting ready for GravityView 2.0

= 2.1.2 on November 27, 2017 =

* Fixed: DataTables now pre-fills `?gv_search` search parameters
* Updated scripts ([see script changelogs](https://cdn.datatables.net/))
    - DataTables from 1.10.13 to 1.10.16
    - Responsive from 2.1.1 to 2.2.0
    - Buttons from 1.2.4 to 1.4.2
    - FixedColumns from 3.2.2 to 3.2.3
    - FixedHeader from 3.1.2 to 3.1.3
    - Scroller from 1.4.2 to 1.4.3
* Fixed: Not loading when using "Direct Access" mode
* Fixed: Undefined index PHP notice
* Now requires WordPress 4.0 (we do hope you are not still running 4.0!)

= 2.1.1 on April 13, 2017 =

* Fixed: Incorrect paging counts
* Added: Support for `gravityview/search-all-split-words` filter added in GravityView 1.20.2

= 2.1 on February 7, 2017 =

* Updated scripts ([see script changelogs](https://cdn.datatables.net/))
    - DataTables core updated from 1.10.11 to 1.10.13
    - Buttons updated from 1.1.2 to 1.2.4
    - Responsive updated from 2.0.2 to 2.1.1
    - Scroller updated from 1.4.1 to 1.4.2
    - FixedHeader updated from 3.1.1 to 3.1.2
    - FixedColumns from 3.2.1 to 3.2.2
* Fixed: Non-Latin characters affected server response length
* Fixed: Set "any" as default search mode
* Updated the plugin's auto-updater script

__Developer Notes:__

* Added: Additional logging available via Gravity Forms Logging Addon
* Tweak: Make sure the connected form is set during the request using GravityView_View::setForm

= 2.0 on April 4, 2016 =

* Added: New Buttons extension to replace the deprecated TableTools export buttons (includes better PDF and Excel generated files)
* Fixed: Overflow table when using the responsive extension
* Fixed: "FixedColumns" properly scrolls the table header along with table content
* Fixed: "Hide View data until search is performed" setting now works with DataTables
* Fixed: When using Direct AJAX method on Views with file upload fields
* Tweak: Scroller improvements by buffering more rows to allow a better scrolling experience
* Fixed: Scroller now supports `rowHeight` setting
* Fixed: Scroller "Loading" text box displayed on top of the scroll bar
* Tweak: AJAX errors are shown in the browser console instead of sending alert
* Tweak: Print button export format style
* Updated: DataTables scripts and stylesheets
* Added: Chinese translation (thanks Edi Weigh!)

= 1.3.3 on January 25, 2016 =
* Fixed: Fields that aren't sortable won't show the sorting icon
* Fixed: Search conflict between DataTables built-in search and the GravityView shortcode search parameters

= 1.3.2 on January 20, 2016 =
* Added: Support for hiding empty fields when using the Responsive extension (only hides fields on the details rows)
* Fixed: Direct AJAX for WordPress 4.4

= 1.3.1 on August 7 =
* Fixed: Invalid JSON response alert

= 1.3 on June 23, 2015 =
* Added: Support for column widths (requires GravityView 1.9)
* Added: Option to enable faster results, with potential reliability tradeoffs. Developers: to enable, return true on `gravityview/datatables/direct-ajax` filter.
* Fixed: Make sure the Advanced Filter Extension knows the View ID
* Updated: Bengali translation (thanks, [@tareqhi](https://www.transifex.com/accounts/profile/tareqhi/))

= 1.2.4 on March 19, 2015 =
* Fixed: Compatibility with GravityView 1.7.2
* Fixed: Error with FixedHeader
* Updated: Bengali translation (thanks, [@tareqhi](https://www.transifex.com/accounts/profile/tareqhi/))

= 1.2.3 on February 19, 2015 =
* Added: Automatic translations for DataTables content
* Updated: Hungarian translation (thanks, [@dbalage](https://www.transifex.com/accounts/profile/dbalage/)!)

= 1.2.2 on January 18, 2015 =
* Fixed: Not showing entries when TableTools were disabled.
* Added: Hook to manage table strings like 'No entries match your request.' and 'Loading Data...'. [Read more](https://docs.gravitykit.com/article/200-how-to-customize-the-no-data-available-in-table-text).
* Fixed: Loading translations
* Fixed: Prevent AJAX errors from being triggered by PHP minor warnings
* Improved CSV Export:
    - Replace HTML `<br />` with spaces in TableTools CSV export
    - Remove "Map It" link for address fields
* Updated: Swedish, Portuguese, and Hungarian translations

= 1.2.1 =
* Fixed: Cache issues with the Edit Entry link for the same user in different logged sessions
* Fixed: Missing sort icons
* Fixed: Minified JS for the DataTables extensions
* Fixed: When exporting tables to CSV, separate the checkboxes and other bullet contents with `;`
* Confirmed compatibility with WordPress 4.1

= 1.2 =
* Modified: DataTables scripts are included in the extension, instead of using remote hosting
* Added: Featured Entries styles (requires Featured Entries Extension 1.0.6 or higher)
* Fixed: Broken Edit Entry links on Multiple Entries view
* Fixed: Table hangs after removing the DataTables search filter value
* Fixed: Supports multiple DataTables on a single page
* Fixed: Advanced Filter filters support restored

= 1.1.2 =
* Fixed: TableTools buttons were all being shown
* Fixed: Check whether `header_remove()` function exists to fix potential AJAX error
* Modified: Move from `GravityView_Admin_Views:: render_field_option()` to `GravityView_Render_Settings:: render_field_option()`
* Modified: Now requires GravityView 1.1.7 or higher

= 1.1.1 on October 21th =
* Fixed: DataTables configuration not respected
* Fixed: GV No-Conflict Mode style support

= 1.1 on October 20th =
* Added: [Responsive DataTables Extension](https://datatables.net/extensions/responsive/) support
* Fixed: URL parameters now properly get used in search results.
    * Search Bar widget now works properly
    * A-Z Filters Extension now works properly
* Fixed: Prevent emails from being encrypted to fix TableTools export
* Speed improvements:
    - Added: Cache results to improve load times (Requires GravityView 1.3)
    - Prevent GravityView from fetching entries twice on initial load (Requires GravityView 1.3)
    - Enable `deferRender` setting by default to increase performance
* Modified: Output response using appropriate HTTP headers
* Modified: Allow unlimited rows in export (not limited to 200)
* Modified: Updated scripts: DataTables (1.10.3), Scroller (1.2.2), TableTools (2.2.3), FixedColumns (3.0.2), FixedHeader (2.1.2)
* Added: Spanish translation (thanks, [@jorgepelaez](https://www.transifex.com/accounts/profile/jorgepelaez/)) and also updated Dutch, Finnish, German, French, Hungarian and Italian translations

= 1.0.4 =
* Fixed: Shortcode attributes overwrite the template settings

= 1.0.3 on August 22 =
* Fixed: Conflicts with themes/plugins blocking data to be loaded
* Fixed: Advanced Filter Extension now properly filters DataTables data
* Updated: Bengali and Turkish translations (thanks, [@tareqhi](https://www.transifex.com/accounts/profile/tareqhi/) and [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/))

= 1.0.2 on August 8 =
* Fixed: Possible fatal error when `GravityView_Template` class isn't available
* Updated Romanaian translation (thanks [@ArianServ](https://www.transifex.com/accounts/profile/ArianServ/))

= 1.0.1 on August 4 =
* Enabled automatic updates

= 1.0.0 on July 24 =
* Liftoff!

=== GravityView - DataTables Extension ===
Tags: gravityview
Requires at least: 4.4
Tested up to: 4.9.6
Stable tag: trunk
Contributors: katzwebservices, luistinygod, soulseekah
License: GPL 3 or higher

Display entries in a dynamic table powered by DataTables & GravityView.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 2.3 on July 3, 2018 =

This is a big update, bringing some great new functionality to DataTables!

* Added: Searching with the Search Bar now refreshes results live—no refresh needed!
* Added: Support for [GravityView Inline Edit](https://gravityview.co/extension/inline-edit/) (requires Inline Edit 1.3 or newer)
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
* Added: Hook to manage table strings like 'No entries match your request.' and 'Loading Data...'. [Read more](https://gravityview.co/support/documentation/203282029/).
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


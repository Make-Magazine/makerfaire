=== GravityView - Advanced Filter Extension ===
Tags: GravityView, Gravity Forms, filtering, conditional logic
Requires at least: 4.4
Requires PHP: 7.4
Tested up to: 7.1
Contributors: GravityKit
License: GPLv3 or later

Filter which entries are shown in a View based on their values.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress's built-in Add New Plugin installer
2. Activate the plugin
3. Follow the instructions

== Changelog ==

= 4.7.1 on August 20, 2026 =

This release refines the date and choice pickers used by conditional logic, and fixes choice dropdowns on non-HTTPS sites, `form:` merge tags, and Number field comparisons.

#### ✨ Improved
* Enhanced conditional logic:
  - Date filters now have a clear button, so a date can be emptied without selecting over it;
  - The date picker's calendar and clear buttons now sit inside the field and level with the value, so a date field is recognizable before reading it;
  - Dynamic Lookup filters now use the Lookup plugin's own choice API when available (Gravity Forms Lookup Field 1.8 or newer), with the previous integration kept as a fallback for older versions.

#### 🐛 Fixed
* The searchable dropdown for choice-based filters did not load on sites accessed over plain HTTP (non-HTTPS), which prevented selecting values and saving field changes.
* Numeric comparisons on Number fields using the currency format returned incorrect results when the site currency uses a dot as the thousand separator (e.g. Euro).
* A `form:` merge tag modifier was ignored unless it was written as the first modifier with no spaces around its colon, so filter values such as `{:1:maxwords:5,form:4}` and `{:1: form:4}` showed a value from the main form instead of the joined form.
* The condition value dropdown detached from its input when the page was scrolled while it was open.
* Switching a condition between single-value and multi-value operators could save a mismatched value for fields whose choices are loaded on demand.
* Date picker segments were skipped by keyboard navigation, and the calendar button did not indicate which field it opened. Tab now enters the field once, the arrow keys move between segments, and the button's accessible name includes its field.
* Emptying a date resized the whole date field in locales whose placeholder letters are wider than digits (e.g. `jjjj`).
* Screen readers announced nothing when focus moved between two adjacent choice fields. Each is now exposed as a labelled group naming its own field.

#### 💻 Developer Updates
* Now uses the current GravityView hooks instead of ones deprecated in GravityView 2.55.

= 4.7.0 on July 9, 2026 =

This release adds type-to-search with paginated results to choice-based filters, lets filters compare one field against another, and fixes the "has ALL of" operator on multi-value fields.

#### ✨ Improved
* Advanced filters can now be configured directly on the new View screen, before the View is saved.
* Enhanced conditional logic:
  - Choice-based filters now offer a searchable dropdown whose results page in as you scroll, so fields with large option lists stay fast and usable instead of trying to render every choice at once. This covers the standard choice fields (Drop Down, Checkbox, Radio, Multi Select) as well as Dynamic Lookup and Populate Anything fields, which draw their options from large external data sets.
  - Now uses a fully keyboard-accessible value picker;
  - New "has NONE of" operator for multi-value filters;
  - New keyboard-accessible Date Picker.
  - Use a field merge tag (e.g. `{Threshold:2}`) as a filter value to compare two fields of an entry instead of a static value.
  - Compare against a field on a joined form ([Multiple Forms](https://www.gravitykit.com/extensions/multiple-forms/)) with a `form:` modifier, e.g. `{Reorder threshold:1:form:123}`.

#### 🐛 Fixed
* The "has ALL of" operator on Checkbox and other multi-value fields matched every entry instead of only those containing all the selected values.

= 4.6.1 on April 27, 2026 =

This hotfix restores the controls for adding new conditional logic rules.

#### 🐛 Fixed
Conditional logic controls missing in the Filter & Sort tab and field settings when no rules had been configured.

= 4.6.0 on April 23, 2026 =

This release improves conditional logic handling for empty date values, missing forms, and admin-only filters, and fixes field-level conditional logic on joined forms.

#### ✨ Improved
* Enhanced conditional logic:
  - Empty field values are no longer treated as today's date when using `is on`, `is not on`, `is before`, or `is after` filters;
  - Filters on deleted or missing forms (e.g. when viewing feed settings for a removed form) no longer trigger errors;
  - Admin-only filters (those suffixed `:disabled_admin`) no longer error out when applied to multi-value fields.

#### 🐛 Fixed
* Field-level conditional logic targeting a field on a joined form (via the [Multiple Forms](https://www.gravitykit.com/extensions/multiple-forms/) extension) evaluated against the main form's row instead of the joined sub-entry, causing the field to incorrectly render or hide.

= 4.5.0 on March 19, 2026 =

This update improves Conditional Logic with better author search, numeric comparisons, and Multiple Forms compatibility.

**Note: Advanced Filter now requires PHP 7.4 or newer.**

#### ✨ Improved
* Enhanced conditional logic:
  - "Created By" filter now supports searching by username, display name, email, or login;
  - Product and pricing field filters now correctly compare numeric values, ignoring currency formatting;
  - Filters for fields on joined forms (via [Multiple Forms](https://www.gravitykit.com/extensions/multiple-forms/)) now correctly resolve field types and merge tags;
  - Fixed "Approval Status" filter option appearing twice in the dropdown;
  - Fixed CSS scoping to prevent style collisions with other GravityKit products.

= 4.4.0 on February 19, 2026 =

This release improves Repeater field support and SQLite compatibility, and fixes issues with dynamic field values and relative date filters.

#### ✨ Improved
* Nested field labels now include their parent field name for easier identification.
* Compatibility with WordPress installations using SQLite.
* Support for Repeater fields.

#### 🐛 Fixed
* Dynamically-added custom values in dropdown, checkbox, and radio fields not displaying correctly.
* Relative date filters (e.g., "Today") using the `is` operator not matching same-day entries.

= 4.3.1 on December 4, 2025 =

This update fixes special character handling in conditional logic filters.

#### 🐛 Fixed
* Conditional logic comparisons failing when choice-based field values contain special characters (e.g., `>`, `<`, `&`, quotes).

= 4.3.0 on September 25, 2025 =

This release adds conditional filtering on joined forms with the Multiple Forms extension and enhances conditional logic for Views and fields, along with related bug fixes.

#### 🚀 Added
* Conditional filtering on joined forms (requires [Multiple Forms](https://www.gravitykit.com/products/multiple-forms/) 0.6.0 or newer).
* Support for using merge tags in text inputs.
* Automatic "Currently Logged-in User" options for Populate Anything fields using WordPress users as a source.

#### 🐛 Fixed
* Errors when using Consent field sub-inputs in conditional logic.
* Filters using `{user:}` and `{created_by:}` merge tags are now treated as no-match when the user is logged out.

= 4.2.0 on August 28, 2025 =

This update improves View and field conditional logic.

#### 🚀 Added
* Ability to search fields by "does not contain".
* Predefined options (e.g., Today, Tomorrow, etc.) for Date fields.

#### ✨ Improved
* Created By field now support `is empty` and `is not empty` operators.

#### 🐛 Fixed
* Filtering  not working with radio button fields and Survey field Likert scales.

= 4.1.1 on February 10, 2025 =

This update resolves a potential issue affecting View filters and field conditional logic.

#### 🐛 Fixed
* A potential JavaScript conflict that could prevent configuring View filters and field conditional logic.

= 4.1.0 on January 16, 2025 =

This release adds support for custom filter values and merge tags in multi-value filters, and fixes several issues, including a deprecation notice in Gravity Forms 2.9.1+.

#### 🚀 Added
* Ability to define custom filter values when using the `has ANY of` or `has ALL of` operators.
* Support for merge tag processing in filters with multiple values.

#### 🐛 Fixed
* Deprecation notice in Gravity Forms 2.9.1 or newer caused by the use of the `GFFormsModel::matches_operation()` method.
* An issue where a missing filter value, when combined with the `has ANY of` or `has ALL of` operators, caused the user interface to break.

= 4.0.4 on November 6, 2024 =

This update adds new filter operators for fields with predefined choices, and resolves various filtering and logic issues.

#### 🚀 Added
* `is empty` and `is not empty` operators for fields with predefined choices.
* Option to specify a custom value for the `contains` operator when fields have predefined choices.

#### 🐛 Fixed
* Incorrect results returned when a `Current User Role` filter option was combined with another condition using an `OR` statement.
* PHP warning when filter condition value is left empty.
* Field conditional logic on a field from a joined form (via Multiple Forms extension) did not work.

= 4.0.3 on September 11, 2024 =

This update resolves an issue where it was not possible to completely remove filters from a View.

#### 🐛 Fixed
* Removing all filters or the last one caused them to reappear after saving the View.

= 4.0.2 on August 29, 2024 =

This release fixes an issue with field comparisons involving merge tags and addresses a PHP 8.2 deprecation notice.

#### 🐛 Fixed
* Incorrect results returned when comparing two fields, with the second field's value provided by a merge tag (e.g., "Field A is greater than {Field B:2}").
* PHP 8.2 deprecation notice due to passing a `null` value to json_decode().

= 4.0.1 on April 16, 2024 =

This release resolves the issue where the "Add Condition" button would disappear when configuring View filters or field conditional logic.

#### 🐛 Fixed
* "Add Condition" button disappearing when configuring View filters or field conditional logic.

= 4.0.0 on April 4, 2024 =

This major update introduces nested groups of conditions with selectable AND/OR logic, role-based filtering, and a new View setting for displaying entries by the logged-in user. [Read the announcement](https://www.gravitykit.com/advanced-filtering-4-0/) for more details.

#### 🚀 Added
* Ability to create nested condition groups.
* Option to toggle the logical operator (AND/OR) for conditions and condition groups.
* Filtering based on the current user role(s).
* View setting to display entries created by the currently logged-in user, serving as a shortcut for the "Created By" filter.

__Developer Updates:__

* Added the `gk/advanced-filters/max-nesting-level` filter to modify the maximum nesting depth for condition groups, with a default setting allowing for nesting up to 3 levels deep.

= 3.0.5 on February 1, 2024 =

This release fixes a critical error in the View editor and resolves a problem where the Duplicate Page plugin was causing the Advanced Filter's functionality to break.

#### 🐛 Fixed
* JavaScript errors in the View editor caused by the Duplicate Page plugin.
* Fatal error when opening a previously saved View with no data source configured.

= 3.0.4 on January 29, 2024 =

This update resolves an issue with the Merge Tag picker disappearing in the View editor, as well as several issues related to filter conditions.

#### 🐛 Fixed
* The Merge Tag picker in the View editor's field settings no longer disappears in the Edit Entry layout.
* Filtering by progress now accurately interprets entry's '100%' completion rate as 'complete'.
* Processing filters for non-date fields no longer results in errors.

= 3.0.3 on January 24, 2024 =

This release introduces support for the Gravity PDF shortcode in the field conditional logic, and addresses a PHP 8.1+ deprecation notice.

#### 🚀 Added
* Added support for the Gravity PDF shortcode (`[gravitypdf]`) in the Empty Field Content area under field conditional Logic.

#### 🐛 Fixed
* A PHP 8.1+ deprecation notice.

= 3.0.2 on January 11, 2024 =

This release includes fixes for the View and field conditional logic.

🐛 Fixed
* "Form field ID #X is no longer available" error when adding conditional logic, despite the field existing.
* "Created by Currently Logged-in User (Disabled for Administrators)" filter not functioning correctly with field conditional logic.
* Incorrect results when filtering checkbox fields using the "is not" condition.

= 3.0.1 on December 9, 2023 =

* Fixed: Issue with setting relative dates (e.g., "today", "yesterday") in the date input field

= 3.0.0 on December 7, 2023 =

* Improved: The extension was refactored to facilitate future development and maintenance

= 2.4.1 on September 29, 2023 =

* Fixed: Regression introduced in 2.4 where the "is empty" and "is not empty" conditions would return incorrect results

= 2.4 on September 21, 2023 =

* Fixed: Incorrect results could be returned when the form has an upload field with the multiple files option enabled
* Fixed: Filtering numeric fields (e.g., Number) with empty values using the "is empty" condition would return all entries

= 2.3 on July 6, 2023 =

* Added: Filters containing Merge Tags suffixed with `:disabled_admin` are ignored when the current user is an Admin. e.g. `{user:id:disabled_admin}` ([learn more about this modifier](https://docs.gravitykit.com/article/946-the-disabledadmin-merge-tag-modifier))
* Improved: Choices filled by [GP Populate Anything](https://gravitywiz.com/documentation/gravity-forms-populate-anything/?ref=263) can now be selected

__Developer Updates:__

* Added: `gk/advanced-filter/disabled-filters` WordPress filter to modify Advanced Filtering filter groups and filters

= 2.2 on March 22, 2023 =

* Added: Shortcode processing in the content that's displayed when the field value does not meet conditional logic
* Improved: Advanced Filter is now loaded only in the View editor
* Fixed: "Greater than" and "Less than" comparisons not working with the "Total" field

= 2.1.14 on January 12, 2022 =

* Fixed: Setting entry's creator to "No User" would bypass the "Currently Logged-in User" filter

= 2.1.13 on January 11, 2022 =

* Fixed: PHP notice when using "Any form field" with field conditional logic
* Fixed: Fatal PHP error when a filter is added and its operator is not modified

= 2.1.12 on December 19, 2021  =

* Improved: It is now possible to filter only those entries that have been updated since creation
* Updated: German translation (thanks, Michael Eppers!)

= 2.1.11 on October 25, 2021  =

* Fixed: MySQL error on certain hosts when "Date Updated", "Date Created" or "Payment Date" entry meta are filtered using the "is empty" condition
* Fixed: JavaScript error when a filter is configured for a multi-input form field that no longer exists

= 2.1.10 on August 16, 2021 =

* Fixed: Incorrect results returned when filtering by empty/nonempty multi-file upload fields

= 2.1.9 on May 31, 2021 =

* Fixed: "Created by Currently Logged-in User (Disabled for Administrators)" filter did not work with field conditional logic
* Fixed: Relative date comparison did not respect WordPress's timezone offset

= 2.1.8 on April 12, 2021 =

* Fixed: Field conditional logic did not work with multiple-input field types (e.g. Address, Name, etc.)

= 2.1.7 on March 3, 2021 =

* Fixed: Comparison of dates in field conditional logical results in a PHP notice
* Fixed: Checkbox field not working with field conditional logic
* Fixed: JavaScript error when opening field settings with conditional logic configured for a nonexistent field ID

= 2.1.6 on February 8, 2021 =

* Fixed: Filter values containing certain characters (e.g., "+") would fail to be processed
* Fixed: Comparison of dates in field conditional logical results in a PHP notice

= 2.1.5 on December 10, 2020 =

* Fixed: Relative date comparison (e.g., "Entry Date" > "tomorrow") not working for field conditional logic
* Fixed: Decoding of URL-encoded special characters in filter values (e.g., "%26" was not being converted to "&")

= 2.1.4 on September 24, 2020 =

* Fixed: Allow using relative dates (e.g., "now", "today") in the date input field

= 2.1.3 on August 25, 2020 =

* Improved: Merge Tags can be now be processed in field conditional logic filters
* Modified: Field conditional logic can only be configured for Multiple and Single Entry contexts

= 2.1.2 on August 20, 2020 =

* Fixed: Properly replace Merge Tags in certain "Created By", "Date Created/Updated" and certain other filters
* Updated: Russian translation (thank you, Irina F!) and Polish translation

= 2.1.1 on July 15, 2020 =

* Fixed: "is empty" and "is not empty" filters not working for Date fields

= 2.1 on July 7, 2020 =

* Added: Conditionally display configured fields based on logic rules
    - Define content that is displayed when the field is not shown
* Added: Specify custom value when "is" operator is used in combination with predefined select values
* Added: Support for filtering by a multiple-choice field option that has no label
* Fixed: Advanced Filter not initializing when a View is created from within the form editor
* Fixed: PHP warning

= 2.0.3 on March 16, 2020 =

* Added: "Is Empty" and "Is Not Empty" filter condition operators
* Fixed: Entries with empty field values showing up in results despite having the proper filter condition in place

= 2.0.2 on February 27, 2020 =

* Fixed: Filters not properly saving when using the initial value of a filter drop-down choice
   - This also resulted in the possibility of only entries with empty field values being shown
   - **To fix, please edit your View, re-configure your filters, and update your View**

= 2.0.1 on February 20, 2020 =

* Added: Date Updated to the available filters
* Fixed: Pre-existing filters created using Version 1.x and using the "All" mode were not able to be deleted

= 2.0 on February 18, 2020 =

* Updated: Brand new interface!
* Added: Create **seriously powerful** filters using "AND" and "OR" together with nested conditions
* Added: Use relative dates with "Date Updated" and "Payment Date" entry details
* Fixed: The combination of search modes (Search Bar "All" mode in combination with Advanced Filter "Any" mode)
* Fixed: "Show only approved entries" forces uses "All" search mode, even when Search Bar is set to "Any"
* Fixed: When editing a View using Quick Edit, configured Advanced Filters are deleted
* Translations: Added Polish translation. Thank you, Dariusz Z!
* Updated: Now requires GravityView 2.0 and WordPress 4.4
* No longer works with the [Internet Explorer browser](https://www.microsoft.com/en-us/microsoft-365/windows/end-of-ie-support)
* Developers: [Read what changed from 1.3 to 2.0](https://docs.gravitykit.com/article/677-advanced-filters-upgrade)

= 1.3 on July 11, 2018 =

* Added: Filter entries by the role of the entry creator - big thanks to [Naomi C. Bush from gravity+](https://gravityplus.pro)!
* Added: Filter by the roles of the currently logged-in user
* Added: Filter by entry Approval Status without needing the Approval form field - [Learn how to filter by approval status](https://docs.gravitykit.com/article/470-filtering-by-entry-approval-status)

= 1.2 on May 23, 2018 =

**Important update - please update as soon as possible.**

Fixed: With Gravity Forms 2.3, when using "Created By" filters, the search mode was allowed to be "Any". For Views with the "Any" setting, searches were able to be performed without filters applied.

= 1.1 on May 2, 2018 =

* Fixed: Filtering by Entry ID not working in Gravity Forms 2.3
* Fixed: Filtering by relative date `today` was showing yesterday's entries on GMT websites
* Changed option names:
    - From "Logged-in User" to "Currently Logged-in User"
    - From "Logged-in User" to "Currently Logged-in User (Disabled for Administrators)"
* Updated Dutch translations—thank you, Erik van Beek!

= 1.0.20 on February 28, 2017 =

* Fixed: Filtering by "Entry ID" not saving
* Fixed: Filtering by a checkbox or multiselect field can prevent viewing a single entry
* Updated translations: Danish, Spanish (Mexico), French, Norwegian, Portuguese (Brazil)

= 1.0.19 on January 5, 2017 =

* Fixed: Fatal error in the Dashboard if `GravityView_View` is not set
* Fixed: Prevent accessing plugin file directly

= 1.0.18 on December 14, 2016 =

* Fixed: Issue with "Any form field" filters preventing access to single entries
* New translations: Spanish translation by Joaquin Rodriguez, German translation by Hubert Test. Thank you!

= 1.0.17 on June 20, 2016 =

* __Important update__: Fixed security issue introduced in 1.0.16 where logged-in users can see all entries.
* Changed "Created by (or admin)" filter to use `gravityview_edit_others_entries` capability instead of `gravityview_view_others_entries`

= 1.0.16 on May 13, 2016 =

* Fixed: If a View has an empty "Any field has" filter, it prevents accessing single entries
* Updated: "Created by (or admin)" filter now also allows users with the `gravityview_view_others_entries` capability to see entries
* Now requires GravityView 1.15 or newer
* Added additional logging

= 1.0.15 on May 7, 2016 =

* Fixed: Allow comparing against empty field values
* Fixed: Properly replace Merge Tags when using using "Any form field" filter
* Added: Chinese translation (Thanks, Edi Weigh!)

= 1.0.14 on February 27, 2016 =

* Fixed: When saving a filter condition using a product field the operator "is" disappears
* Fixed: When searching by an option field there were no results

= 1.0.12 & 1.0.13 on January 21, 2016 =

* Fixed: Post Category filters
    - Dropdown not populated with categories
    - Added "Is Not" option
* Fixed: Date Created filter not working properly
* Tweak: Increase the number of users displayed in the Advanced Filter "Created By" dropdown
* Updated: Translation textdomain from `gravity-view-advanced-filter` to `gravityview-advanced-filter`
    - Included updater library in translation strings

= 1.0.11 on November 13, 2015 =

* New: Add "is not" option to GravityView Approval fields. Now you can show only unapproved entries.
* Tweak: Make it clearer in the logs when the extension is preventing displaying results for security
* Updated: Extension updater script

= 1.0.10 on September 13, 2015 =

* Fixed: Not able to enter relative dates (like `now` or `two weeks ago`) in date field filters
* Updated: Extension updater script

= 1.0.9 on August 4, 2015 =

* Added: New filter to disable the entries' filter "created by the current logged-in user" when user is administrator
* Updated: French translation

= 1.0.8 on June 23, 2015 =

* Fixed: Error on WordPress Dashboard preventing Gravity Forms widget from displaying

= 1.0.7 on June 22, 2015 =

* Fixed: Filtering by date fields when using PHP `strtotime()` values like `-3 days` or `+3 weeks`
* Added: Prevent showing anything if the View ID isn't set when filtering results
* Updated: Hungarian translation by [dbalage](https://www.transifex.com/accounts/profile/dbalage/) and Dutch translation by [@erikvanbeek](https://www.transifex.com/accounts/profile/erikvanbeek/)

= 1.0.6 on December 22, 2014 =

* Fixed: Entries were being shown for users who were not logged in

= 1.0.5 on December 12, 2014 =

* Fixed: not filtering if only one filter is defined.

= 1.0.4 on December 11, 2014 =

* Fixed: Do not show entries for non logged users when the 'Created By' field value is 'Logged-in user'
* Tweak: Added `gravityview/adv_filter/view_filters` filter to allow modifying the filters generated by the Extension
* Fixed: Auto-upgrade for Multisite sites
* Added: Dutch translation (thanks, [@erikvanbeek](https://www.transifex.com/accounts/profile/erikvanbeek/)!)

= 1.0.3 on November 7, 2014 =

* Added: Support for relative dates ("now" or "-3 days") for date type fields
* Added: Support Gravity Forms Merge Tags. Example: "`{user:display_name}` IS `Ellen Ripley`"
* Fixed: Conflicts with non-Latin UTF-8 characters like "ß"
* Added: Romanian translation (thanks, [@ArianServ](https://www.transifex.com/accounts/profile/ArianServ/)!) and Dutch translation (thanks, [@erikvanbeek](https://www.transifex.com/accounts/profile/erikvanbeek/)!)
* Updated: Bengali, Turkish, and Spanish translations (thanks, [@tareqhi](https://www.transifex.com/accounts/profile/tareqhi/), [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/), and [@jorgepelaez](https://www.transifex.com/accounts/profile/jorgepelaez/))

= 1.0.2 on September 9, 2014 =

* Fixed: Conflict with other GravityView search parameters
* Updated: Bengali, Turkish, and Spanish translations (thanks, [@tareqhi](https://www.transifex.com/accounts/profile/tareqhi/), [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/), and [@jorgepelaez](https://www.transifex.com/accounts/profile/jorgepelaez/))
* Fixes fatal error on Views screen when deleting a View

= 1.0.1 on August 5, 2014 =

* Fixed: Scripts not being added in No-Conflict mode
* Added: Romanian translation - thanks, [ArianServ](https://www.transifex.com/accounts/profile/ArianServ/)!

= 1.0.0 on August 4, 2014 =

* Liftoff!

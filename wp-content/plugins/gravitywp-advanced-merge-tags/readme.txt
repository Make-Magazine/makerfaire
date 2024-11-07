=== GravityWP - Advanced Merge Tags ===
Contributors: gravitywp
Tags: gravity forms, gravityforms, mergetags, merge tags
Requires at least: 3.0.1
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 1.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This Gravity Forms Add-On adds extra Merge Tag modifiers (and a lot of power). From the most common used functions like capitalize and trim to changing date formats.

== Description ==

This plugin provides advanced functionality to the default Gravity Forms Number Field, like rounding numbers, only absolute numbers, fixed point notation up to 5 decimals, range calculation, custom units like % or m2, show number field as slider.

= Powerful new Merge Tags =
* Get the current timestamp - Returns the timestamp on the moment the Merge Tag is used. Can be used to check if timestamp is expired.
* Get the current slug (or part of the slug) - Returns the slug or part of the slug (parent) for example to check which part of the website the Merge Tag is loaded.
* Modify dates - Adding days, weeks, formatting in different ways and to count the number of entries that match a specific field.

= Handy extra Modifiers =
* Text transform - Modifier to transform the text to uppercase, lowercase, first character uppercase or lowercase or uppercase all words.
* Length of string & word count - Modifiers to count the number of characters of a value or the number of words.
* Covert to ASCII characters - Modifier to change the accented letters inside a text.
* URL encode text - Encode the field value with the purpose to pass it as an url parameter for dynamic population.

== Installation ==

Upload the plugin files to the `/wp-content/plugins/gravitywp-advanced-merge-tags` directory, or install the plugin through the WordPress plugins screen directly.

== Changelog ==
= 1.7.1 =
- Added more features to {gwp_generate_token}.

= 1.7 =
- Implemented {gwp_calculate} merge tag.
- Implemented gwp_censor modifier.

= 1.6.3 =
- Fixed an issue where the gwp_get_matched_entry_value merge tag applies newline characters to html breaks twice in confirmation messages.

= 1.6.2 =
- Fixed an issue regarding the frequency of update checks to increase performance.

= 1.6.1 =
- Fix PHP error when using Gravity View Advanced Filter with roles.

= 1.6.0 =
- Update the license handler.

= 1.5.2 =
- Added absint option to gwp_sanitize.
- Implemented gwp_json_get modifier.
- Fix advanced merge tag modifiers with quoted parameters not working when used in the post content.

= 1.5.1 =
- Implemented {gwp_get_matched_entry_value} merge tag.
- Implemented {gwp_gview_advanced_filter} merge tag for generating filter conditions for GravityKit Advanced Filters.
- Change gwp_get_matched_entries_value 'value' parameter to 'return_value' to allow filter parameters such as 'filter1', 'operator1', and 'value1'. If no 'return_value' parameter is found it will fallback to 'value' to maintain backwards compatibility.
- Fix some potential PHP warnings.

= 1.5 =
- Implemented {gwp_post_id} merge tag.
- Implemented {gwp_user} mergetag, which is similar to the GF {user} merge tag, but allows to use advanced modifiers on the output.
- Implemented {gwp_entry} merge tag, which supports advanced modifiers on entry properties like id and status. Usage example: {gwp_entry:id:gwp_count_matched_entries form_id="2" match_id='1'}.
- Implemented {gwp_eeid} merge tag, which allows to get entry properties / field values from the encrypted entry-id passed in the eeid url parameter. Supports regular and advanced modifiers. Example {gwp_eeid:5}, {gwp_eeid:status}
- Implemented gwp_user_role modifier.
- Implemented gwp_encrypt modifier.
- Implemented gwp_decrypt modifier.
- Implemented gwp_sanitize modifier.
- Added support for usage of entry related merge tags and modifiers in Gravity Perks Populate Anything's custom choice / value templates.
- Added return_format=multiselect parameter to the gwp_get_matched_entries_values modifier.
- Added plugin option to replace merge tags in post content.
- Other minor improvements.

= 1.4.3 =
- Improved security by applying wp_kses_post on gwp_get_matched_entries_values output.
- Added support for textual separators in gwp_get_matched_entries_values output. New parameters: row_separator and col_separator.
- Added support for a no-result message in gwp_get_matched_entries_values output. New parameters: no_result.
- Fixed a bug in gwp_get_matched_entries_values where the row_tag parameter was used for the row class.

= 1.4.2 =
- Add the 'gwp_amt_replace_admin_field_variables' filter to allow conversion of field merge tags in administrative field's default value, for other field types than 'text'.

= 1.4.1 =
- Fix compatibility issue with GP Populate Anything Live Merge Tags, where nested merge tag modifier arguments are passed with html encoded quotes.

= 1.4 =
- Feature: Implemented support for regular GF modifiers after the advanced modifier. Example {DropdownSelect:1:gwp_substring:value start=1 length=2}.
- Feature: It is now possible to use {DropdownSelect:1:gwp_substring start=-3} in calculations. This will use the last 3 characters from a value like 'first_choice_1.4'.
- Feature: Added gwp_url merge tag.
- Performance improvements.

= 1.3 =
- Feature: Added gwp_generate_token merge tag.
- Feature: Added gwp_now merge tag.
- Feature: Added gwp_sum_matched_entries_values modifier, which allows to add up numeric values from a field for a large batch of entries.
- Feature: it is now possible to use {text:1:gwp_word_count} in calculations.
- Fix fatal error when gwp_date_created and gwp_date_updated merge tags are used before the entry was created.

= 1.2.4 =
- Update dependencies to fix a security issue in a third party library.

= 1.2.3 =
- Added support for use of the gwp_word_count merge tag modifier with tinymce/RTF textarea fields.
 
= 1.2.2 =
- Fix nested merge tags within gwp_append and gwp_replace modifiers not working properly.

= 1.2.1 =
- Improvement: gwp_urlencode modifier now supports checkboxes.

= 1.2 =
- Added gwp_substring modifier.
- Added gwp_date_format modifier. This deprecates the gwp_date_field merge tag.
- Added support for using nested merge tags within merge tags with search filters, like filter1=created_by operator1=is value1=*|user:ID|*.
- Search filters with invalid operators are now ignored.
- Added support for using nested merge tags within gwp_date-* merge tags and modifiers like modify='+*|number of weeks:2|* weeks'.
- Added support for using nested merge tags within gwp_append and gwp_replace modifiers like {Textarea:1:gwp_replace search='VARIABLE' replace='*|text:2|*'}.
- Added plugin setting to replace field merge tags like {field:1} in text fields with administrative visibility after an entry is submitted.
- Fix subfield values not working with advanced merge tags modifiers.
- Apply newline to <br> conversion when outputting to html context.
- Reworked the method for gwp_word_count modifier as str_word_count() does not work as expected in various cases. Words are now counted based on separator characters. By default:  Any whitespace character (spaces, tabs, line breaks).
- Added 'gravitywp_advancedmergetags_wordcount_regex' and 'gravitywp_advancedmergetags_wordcount_result' filters.
- Added required argument checks for gwp_datefield merge tag / modifier.
- Improved handling of missing empty arguments in gwp_get_matched_entries_values and gwp_datefield modifier.
- Security improvements.

= 1.1.1 = 
- Fix issue when using timezone argument.

= 1.1 = 
- Added support for Merge Tag modifiers with case sensitive arguments.
- New Merge Tag modifier: gwp_append - Append a string before or after a non-empty value.
- New Merge Tag modifier: gwp_replace - Search and replace a value before output.
- Fix some PHP notices.

= 1.0 = 
- All new.

=== Drop Down Options in List Fields for Gravity Forms ===
Contributors: ovann86
Donate link: https://www.itsupportguides.com/donate/
Tags: Gravity Forms, forms, online forms, wcag, drop down, select, list
Requires at least: 4.8
Tested up to: 5.1
Stable tag: 1.8.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Gives the ability to add drop down (select) fields inside of a list field column

== Description ==

> This plugin is an add-on for the Gravity Forms plugin. If you don't yet own a license for Gravity Forms - <a href="https://rocketgenius.pxf.io/c/1210785/445235/7938" target="_blank">buy one now</a>! (affiliate link)

**What does this plugin do?**

* Adds the ability to make the list field have a drop down (select) field inside of a list field column.
* Supports 'enhanced drop down' that allows you to search/filter the list of options.
* Ability for the form user to add new options if they don't exist.
* Ability to set the drop down label and value separately (for example, 'item 1' as the label and '100' as the value - this allows you to do caluclations based on the users selection)
* compatible with <a href="https://github.com/richardW8k/RWListFieldCalculations/blob/master/RWListFieldCalculations.php">Gravity Forms List Field Calculations Add-On</a>
* compatible with <a href="https://wordpress.org/plugins/gravity-forms-pdf-extended/">Gravity PDF</a>

> See a demo of this plugin at [demo.itsupportguides.com/gravity-forms-list-field-select-drop-down](https://demo.itsupportguides.com/gravity-forms-list-field-select-drop-down/ "demo website")

**Disclaimer**

*Gravity Forms is a trademark of Rocketgenius, Inc.*

*This plugins is provided “as is” without warranty of any kind, expressed or implied. The author shall not be liable for any damages, including but not limited to, direct, indirect, special, incidental or consequential damages or losses that occur out of the use or inability to use the plugin.*

== Installation ==

1. Install plugin from WordPress administration or upload folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress administration
1. Open the Gravity Forms 'Forms' menu
1. Open the forms editor for the form you want to change
1. Add or open an existing list field
1. With multiple columns enabled you will see a 'Drop down fields' section - here you can choose which columns are drop down fields.
1. When checked, an 'Options' field appears - enter the options for the drop down list in the field, comma separated. E.g. Mr,Mrs

== Frequently Asked Questions ==

**How do I add a blank or empty option**

If you want to add a blank or empty option half way through simply enter two commas in the options, for example:

`Option 1,,Option 2`

If you want the options of 'Option 1' and 'Option 2' but want the default option to be blank or empty you would enter:

`,Option 1,Option 2`

**How do I configure the enhanced drop down**

The enhanced drop down features in Gravity Forms are provided by the Chosen jQuery plugin.

To configure you will need to use the JavaScript filter provided by Gravity Forms.

The example below shows how to change the default 'Select an Option' text.

`add_action("gform_pre_render", "set_chosen_options");
function set_chosen_options($form){
    ?>

    <script type="text/javascript">
        gform.addFilter('gform_chosen_options','set_chosen_options_js');
        //limit how many options may be chosen in a multi-select to 2
        function set_chosen_options_js(options, element){
            //form id = 85, field id = 5
            //if (element.attr('id') == 'input_2_3'){
                options.placeholder_text_single = 'Select an option';
            //}

            return options;
        }
    </script>

    <?php
    //return the form object from the php hook
    return $form;
}`

A complete list of options can be found here: https://harvesthq.github.io/chosen/options.html

The documentation for the Gravity Forms filter can be found here: https://www.gravityhelp.com/documentation/article/gform_chosen_options/

**How do I populate the drop down options dynamically**

To dynamically populate the drop down options you will need to use the gform_pre_render filter (and similar).

The example below shows how to do this — for FORM ID 1 FIELD ID 20 and COLUMN LABEL ‘Column 1’. Note how the values are a comma separated string (and we can’t define the option value separately at the moment).

Note that this example is for a mulit-column enabled list field.

`add_filter( 'gform_pre_render', 'my_gform_pre_render', 10, 1 );
add_filter( 'gform_pre_validation', 'my_gform_pre_render', 10, 1 );
add_filter( 'gform_admin_pre_render', 'my_gform_pre_render', 10, 1 );
add_filter( 'gform_pre_submission_filter', 'my_gform_pre_render', 10, 1 );

function my_gform_pre_render( $form ) {
	if ( GFCommon::is_form_editor() || 1 != $form['id'] ) {
		return $form;
	}

	if ( is_array( $form ) || is_object( $form ) ) {

		foreach ( $form['fields'] as &$field ) {  // for all form fields
			$field_id = $field['id'];

			if ( 20 != $field_id ) {
				break;
			}

			if ( 'list' == $field->get_input_type() ) {

				$has_columns = is_array( $field->choices );

				if ( $has_columns ) {
					foreach ( $field->choices as $key => &$choice ) { // for each column
						$isDropDown = rgar( $choice, 'isDropDown' );
						$column = rgars( $field->choices, "{$key}/text" );
						if ( $isDropDown && 'Column 1' == $column ) {
							$choices = 'Option 1, Option 2, Option 3';
							$choice['isDropDownChoices'] = $choices;
						}
					}
				}
			}
		}
	}
	return $form;
}`

== Screenshots ==

1. Shows the drop down options in the forms editor.
2. Shows a list field that has drop down fields added to two columns - Title and Option.

== Changelog ==

= 1.8.2 =
* Fix: resolve issue with default option not being selected when new rows are created

= 1.8.1 =
* Maintenance: move from depreciated filter 'gform_get_field_value' to 'gform_get_input_value'
* Fix: resolve 'Invalid argument supplied for foreach()' error when submitting form

= 1.8.0 =
* Feature: add post custom field support.

= 1.7.4 =
* Fix: resolve issue with custom values added using 'Enable add options' not displaying in 'edit' mode (e.g. editing an entry using the wp-admin entry editor or GravityView ).

= 1.7.3 =
* Fix: resolve issue of duplicate values when pre-populating field using the gform_field_value_$parameter_name filter
* Maintenance: improve handling when options are removed from a form (in the form editor) and the entry is accessed later from the Entry editor
* Maintenance: tidy php (use object notation for $field)

= 1.7.2 =
* Fix: improve drop down field layout - attempt to set a static width for "enhanced" drop down fields.

= 1.7.1 =
* Fix: resolve PHP parse error when using PHP version 5.3.

= 1.7.0 =
* Feature: added ability to set drop down values, separate from labels (for example, 'item 1' as the label and '100' as the value - this allows you to do caluclations based on the users selection).
* Feature: added support for <a href="https://github.com/richardW8k/RWListFieldCalculations/blob/master/RWListFieldCalculations.php">Gravity Forms List Field Calculations Add-On</a>
* Fix: improve drop down field layout in list fields by setting a fixed width with the field loads - this should stop the 'jumping' (variable width and columns changing position) of fields if a large select option is chosen.
* Maintenance: change how plugin detects that Gravity Forms is installed and enabled

= 1.6.6 =
* Fix: Improve Gravity View support (resolve issue with updating entry on front end)

= 1.6.5 =
* Fix: improve handling of the 'Enable add options' feature (allows form users to manually enter their own values into a drop down).

= 1.6.3 =
* Maintenance: improve auto-height process that ensures the drop down field is the same height as other input fields

= 1.6.2 =
* Fix: improve support for enhanced drop downs that are conditionally hidden when the form first loads

= 1.6.0 =
* Feature: Add 'Enable add options' option for 'enhanced user interface' enabled fields (jQuery Chosen plugin). With this option enabled users will be able to add their own values to the drop down list, if the option is not already available.
* Maintenance: Move Form Editor JavaScript to external file
* Maintenance: Re-write Form Editor JavaScript to make it easy to understand and work with

= 1.5.0 =
* Feature: Add support for Gravity Forms JavaScript filter - gform_chosen_options - for configuring enhanced drop down list options.
* Fix: Patch to allow scripts to enqueue when loading Gravity Form through wp-admin. Gravity Forms 2.0.3.5 currently has a limitation that stops the required scripts from loading through the addon framework.
* Maintenance: Add minified JavaScript and CSS
* Maintenance: Confirm working with WordPress 4.6.0 RC1
* Maintenance: Update to improve support for Gravity Flow plugin


= 1.4.0 =
* Feature: Add support for the 'enhanced user interface' Gravity Forms option, this is done using the Chosen jQuery plugin.
* Maintenance: Add some styling to the options in the form editor.
* Maintenance: Moved JavaScript to external file.
* Maintenance: Change JavaScript and CSS to load using Gravity Forms addon framework.
* Maintenance: Tested against Gravity Forms 2.0 RC1.
* Maintenance: Tested against Gravity PDF 4.0 RC4.
* Maintenance: Added blank index.php file to plugin directory to ensure directory browsing does not occur. This is a security precaution.
* Maintenance: Added ABSPATH check to plugin PHP files to ensure PHP files cannot be accessed directly. This is a security precaution.

= 1.3.2 =
* Fix: Added function to change 'select' field formatting so that it is accessible and complies with WCAG 2.0 - Level AA. This is done by applying a 'title' attribute to the field, the title has the column title as the value.

= 1.3.1 =
* Improvement: Improved support for multi-site installations.

= 1.3 =
* Feature: Added support for text translation - uses 'itsg_field_dropdown' text domain.

= 1.2.1 =
* Improvement: Added the ability to have list items that contain a comma (e.g. 'Ipsum, lorum & marem'). This is done by adding a backslash (\) before the comma to escape it (e.g. 'Ipsum\, lorum & marem').
* Improvement: Added handling if a value exists for the field but the list of options is empty. In this scenario a select list is created with the field value.

= 1.2 =
* Fix: Resolve PHP error messages - added isset( $choice["isDropDown"] ) before calling array item, and check that list field has columns before calling column data.

= 1.1 =
* Improvement: Remove blank first option to match Gravity Forms Drop Down field behaviour.
* Maintenance: Rename JavaScript function name and update jQuery target to avoid conflicts with other plugins.
* Maintenance: change plugin name from 'Gravity Forms - List Field Drop Down' to 'Drop Down Options in List Fields for Gravity Forms'.

= 1.0 =
* First public release.

== Upgrade Notice ==

= 1.0 =
First public release.
/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView Advanced Filter extension
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.0.3
 */


(function( $ ) {

var gvAdvancedFilters = {

	init: function() {

		$('body').on( 'gravityview_form_change', gvAdvancedFilters.formChange );

		$('#entry_filters').removeClass('hide-if-js').gfFilterUI( gvAdvFilterVar.gformFieldFilters, gvAdvFilterVar.gformInitFilter, true );

		gform.addFilter( 'gform_datepicker_options_pre_init', gvAdvancedFilters.fixConstrainInput );

	},

	/**
	 * Allow typing relative dates in datepicker fields.
	 * @internal For existing fields (if not working), you may need to toggle the comparison dropdown before it works.
	 * @see http://api.jqueryui.com/datepicker/
	 * @since 1.0.10
	 * @param optionsObj datePicker options
	 * @param {int} formId The ID of the current form.
	 * @param {int} fieldId The ID of the current field.
	 * @returns {*}
	 */
	fixConstrainInput: function( optionsObj, formId, fieldId ) {

		// Allow for typing relative dates, not just date formats
		optionsObj.constrainInput = false;

		return optionsObj;
	},

	formChange: function() {
		$('#entry_filters_warning').show();
		$('#entry_filters').html('');
	}

};



$(document).ready( function() {

	gvAdvancedFilters.init();

});


}(jQuery));

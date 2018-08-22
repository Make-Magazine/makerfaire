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

		gvAdvancedFilters.filters = $('#entry_filters')
			.removeClass('hide-if-js')
			.gfFilterUI( gvAdvFilterVar.gformFieldFilters, gvAdvFilterVar.gformInitFilter, true )
			.on( 'change init', gvAdvancedFilters.lockCreatedBy )
			.on( 'click', '.gform-add,.gform-remove', gvAdvancedFilters.lockCreatedBy )
			.trigger( 'init' );

		$( '<input type="hidden" name="mode" value="all" />' ).prependTo( gvAdvancedFilters.filters );

		gform.addFilter( 'gform_datepicker_options_pre_init', gvAdvancedFilters.fixConstrainInput );

	},

	/**
	 * Locks the search mode to "All" if "Created By" includes a value not an user ID (added by GV)
	 *
	 * @since 1.2
	 */
	lockCreatedBy: function( e ) {

		var $mode = $('select[name="mode"]', gvAdvancedFilters.filters );

		$mode.attr( 'disabled', null );

		$( '.gform-field-filter', gvAdvancedFilters.filters ).each( function ( index ) {

			var filter_field = $( '.gform-filter-field', $( this ) ).val(),
				filter_value = $( '.gform-filter-value', $( this ) ).val();

			if (
				filter_field.length && filter_field.match( /created_by/ ) &&
				filter_value.length && ! filter_value.match( /^[0-9]+?$/ )
			) {
				$mode.val('all').attr('disabled', 'disabled');
				return false;
			}
		});
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

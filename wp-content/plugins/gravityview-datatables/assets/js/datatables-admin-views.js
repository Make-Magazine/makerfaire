/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @global {object} GV_DataTables_Admin
 * @since 1.0.0
 */

(function( $ ) {

	var gvDataTablesExt = {

		has_tabs: null,

		init: function() {

            gvDataTablesExt.has_tabs = $( '#gravityview_settings' ).data("ui-tabs");

			$('#gravityview_directory_template')
				.on( 'change', gvDataTablesExt.toggleMetaboxAndRowGroup )
				.trigger('change');

			$('#datatables_settingsbuttons, #datatables_settingsscroller, #datatables_settingsauto_update, #datatables_settingsrowgroup')
				.on( 'change', gvDataTablesExt.showGroupOptions )
				.trigger('change');

			$('#datatables_settingsscroller')
				.on( 'change', gvDataTablesExt.toggleNonCompatible )
				.trigger('change');

			$('body')
				.on( 'gravityview/settings/tab/enable', gvDataTablesExt.showMetabox )
				.on( 'gravityview/settings/tab/disable', gvDataTablesExt.hideMetabox )
				.on( 'gravityview/field-added', load_row_group_with_fields )
				.on( 'sortupdate', '#directory-active-fields .active-drop', load_row_group_with_fields );
		},

		toggleMetaboxAndRowGroup: function() {

			var template = $('#gravityview_directory_template').val();
			var $setting = $('#gravityview_datatables_settings');

			if( 'datatables_table' === template ) {

				$('body').trigger('gravityview/settings/tab/enable', $setting );

				load_row_group_with_fields();

			} else {

				$('body').trigger('gravityview/settings/tab/disable', $setting );

			}
		},

		showMetabox: function( event, tab ) {

			if( ! gvDataTablesExt.has_tabs ) {
				$( tab ).slideDown( 'fast' );
			}
		},

		hideMetabox: function( event, tab ) {

			if( ! gvDataTablesExt.has_tabs ) {
				$( tab ).slideUp( 'fast' );
			}
		},

		/**
		 * Show the sub-settings for each DataTables extension checkbox
		 */
		showGroupOptions: function() {
			var _this = $(this);
			if( _this.is(':checked') ) {
				_this.parents('tr').siblings().fadeIn();
			} else {
				_this.parents('tr').siblings().fadeOut( 100 );
			}
		},

		toggleNonCompatible: function() {
			var _this = $(this),
				fixedCB = $('#datatables_settingsfixedheader, #datatables_settingsfixedcolumns');


			if( _this.is(':checked') ) {
				fixedCB.prop( 'checked', null ).parents('table').hide();
			} else {
				fixedCB.parents('table').fadeIn();
			}
		}

	};

	/**
	 * Add all active fields to a row group select.
	 *
	 */
	function load_row_group_with_fields() {
		var active_fields = $( '#directory-active-fields .active-drop > div.gv-fields' );
		var settings_field = $( '#datatables_settingsrowgroup_field' );
		var selected_value = settings_field.find( ':selected' ).text();
		var options = [];

		active_fields.each(function (i, elem) {
			var $elem = $( elem );
			var field_id = $elem.find('.field-key').val();

			// Don't group by internal GravityView fields; they're not unique.
			if ( GV_DataTables_Admin.internal_fields.hasOwnProperty( field_id ) ) {
				return;
			}

			var label = $elem.find( '.gv-field-label-text-container' ).text();
			var backup_label = $elem.find( '.gv-field-label' ).data( 'original-title' );

			// If `label` is falsey (e.g., '', null, undefined), it will push `backup_label`
			options.push( label || backup_label );
		} );

		settings_field.empty();

		// Return early if there are no options
		if ( options.length === 0 ) {
			return;
		}

		$.each( options, function ( i, val ) {
			settings_field.append( $( '<option>', {
				value: i,
				text: val,
				selected: val === selected_value
			} ) );
		} );
	}

	// Changing to .on( 'ready' ) breaks for now; the checkbox toggling doesn't work.
	$(document).ready( function() {
		gvDataTablesExt.init();
	});

}(jQuery));


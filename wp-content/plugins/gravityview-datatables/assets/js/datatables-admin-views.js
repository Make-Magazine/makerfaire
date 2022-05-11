/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */


(function( $ ) {

	var gvDataTablesExt = {

		has_tabs: null,

		init: function() {

            gvDataTablesExt.has_tabs = $( '#gravityview_settings' ).data("ui-tabs");

			$('#gravityview_directory_template')
				.on( 'change', gvDataTablesExt.toggleMetabox )
				.change();

			$('#datatables_settingsbuttons, #datatables_settingsscroller, #datatables_settingsauto_update')
				.on( 'change', gvDataTablesExt.showGroupOptions )
				.change();

			$('#datatables_settingsscroller')
				.on( 'change', gvDataTablesExt.toggleNonCompatible )
				.change();

			$('body')
				.on( 'gravityview/settings/tab/enable', gvDataTablesExt.showMetabox )
				.on( 'gravityview/settings/tab/disable', gvDataTablesExt.hideMetabox );

		},

		toggleMetabox: function() {

			var template = $('#gravityview_directory_template').val();
			var $setting = $('#gravityview_datatables_settings');

			if( 'datatables_table' === template ) {

				$('body').trigger('gravityview/settings/tab/enable', $setting );

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



	$(document).ready( function() {
		gvDataTablesExt.init();
	});


}(jQuery));

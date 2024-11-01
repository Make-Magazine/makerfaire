/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
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
				.on( 'change', gvDataTablesExt.toggleMetaboxAndRowGroup );

			$('#datatables_settingsbuttons, #datatables_settingsscroller, #datatables_settingsauto_update, #datatables_settingsrowgroup')
				.on( 'change', gvDataTablesExt.showGroupOptions )
				.trigger('change');

			$( 'body' )
				.on( 'gravityview/settings/tab/enable', gvDataTablesExt.showMetabox )
				.on( 'gravityview/settings/tab/disable', gvDataTablesExt.hideMetabox )
				.on( 'gravityview/field-added', load_row_group_with_fields )
				.on( 'sortupdate', '#directory-active-fields .active-drop', load_row_group_with_fields )
				.on( 'gravityview/field-added', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/field-removed', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/all-fields-removed', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/view-config-updated', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/dialog-closed', gvDataTablesExt.setFilterFields );
		},

		// Automagically manage the list of fields with filters by updating the select element under DataTables settings.
		setFilterFields: function () {
			const fieldUIDtoLabelMap = {};

			$( '#directory-active-fields .active-drop > div.gv-fields' ).each( function () {
				const $fieldLabelEl = $( this ).find( '.field-label' );

				if ( !$fieldLabelEl.length ) {
					return;
				}
				const nameAttrValue = $fieldLabelEl.attr( 'name' ).match( /\[directory_table-columns\]\[(.*?)\]/ );

				if ( !nameAttrValue || !nameAttrValue[ 1 ] ) {
					return;
				}

				fieldUIDtoLabelMap[ nameAttrValue[ 1 ].replace( /[^a-z\d]/, '' ) ] = $( this ).find( '.gv-field-label-text-container' ).text();
			} );

			const $fieldsWithFilterSettingsEl = $( '#datatables_settingsfields_with_filter' );

			const previouslySelectedFields = $fieldsWithFilterSettingsEl.val();

			if ( $.isEmptyObject( fieldUIDtoLabelMap ) ) {
				$fieldsWithFilterSettingsEl.empty();

				$fieldsWithFilterSettingsEl.parents( 'tr' ).hide();
			} else {
				// Retrieve all option values that are available in the select element.
				const existingOptions = $fieldsWithFilterSettingsEl.find( 'option' ).map( function () { return this.value; } ).get();

				// Convert to a set to simplify lookup later.
				const previouslySelectedFieldsSet = new Set( previouslySelectedFields );

				$fieldsWithFilterSettingsEl.empty();

				$.each( fieldUIDtoLabelMap, function ( key, value ) {
					// If the field was previously selected or didn't exist, then it should be selected.
					const isSelected = previouslySelectedFieldsSet.has( key ) || !existingOptions.includes( key );

					$fieldsWithFilterSettingsEl.append( $( '<option>', { value: key, text: value } ).prop( 'selected', isSelected ) );
				} );

				$fieldsWithFilterSettingsEl.parents( 'tr' ).show();
			}
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
	};

	/**
	 * Add all active fields to a row group select.
	 *
	 */
	function load_row_group_with_fields() {
		var active_fields = $( '#directory-active-fields .active-drop > div.gv-fields' );
		var settings_field = $( '#datatables_settingsrowgroup_field' );
		var selected_value = settings_field.find( ':selected' ).index();
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
				selected: i === selected_value
			} ) );
		} );
	}

	// Changing to .on( 'ready' ) breaks for now; the checkbox toggling doesn't work.
	$(document).ready( function() {
		gvDataTablesExt.init();
	});
}(jQuery));


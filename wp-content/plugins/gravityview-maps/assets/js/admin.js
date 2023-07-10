/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView Maps
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 *
 * globals jQuery, GV_MAPS_ADMIN
 */

( function ( $ ) {

	"use strict";

	/**
	 * Passed by wp_localize_script() with some settings
	 * @type {object}
	 */
	var self = $.extend( {
		'metaboxId'             : '#gravityview_maps_settings',
		'addressFieldSelector'  : '#gv_maps_se_map_address_field',
		'formIdSelector'        : '#gravityview_form_id',
		'addIconSelector'       : '#gv_maps_se_add_icon',
		'selectIconSelector'    : '#gv_maps_se_select_icon',
		'inputIconSelector'     : '#gv_maps_se_map_marker_icon',
		'availableIconsSelector': '#gv_maps_se_available_icons',
		'setIconSelector'       : '.gv_maps_icons',
		pageSizeField           : '#gravityview_se_page_size',
	}, GV_MAPS_ADMIN );


	self.init = function () {
		if ( ! self.isGravityViewScreen ) {
			return;
		}

		// settings handling
		self.bindToFormChange();

		// map icons add & select
		self.bindMapIcons();

		// toggle infowindow settings
		self.bindInfowindowSettings();

		self.hideOnDataTables();

		self.bindModifyRestOnChangeTemplate();

		self.bindGlobalNoticeMapViewsWithoutRest();
	};

	/**
	 * When DataTables layout is selected, hide the Map fields and widgets using CSS
	 * @since 1.4.2
	 */
	self.hideOnDataTables = function () {
		$( document ).on( 'change', '#gravityview_directory_template', function () {
			$( 'body' ).toggleClass( 'gravityview-template-datatables_table', ( 'datatables_table' === $( this ).val() ) );
		} );
	};

	self.onTemplateChangeModifyRest = ( event ) => {
		const $field = $( event.target );
		if ( $field.val() !== 'map' ) {
			return;
		}

		self.confirmModifyRest();
	};

	/**
	 * Onload of the page checks if we need to trigger a confirmation modal for Rest API usage.
	 * Will trigger the `self.confirmModifyModal` method inside a 500millisecond timout to avoid problems with
	 * details from the view still loading.
	 *
	 * @since 2.2
	 *
	 * @return void
	 */
	self.onLoadCheckForRestValue = () => {
		const isGlobalRestEnabled = $( '#gravityview_se_rest_disable:not(:checked)' ).length !== 0;

		if ( isGlobalRestEnabled ) {
			return;
		}
		const hasMapField = $( '.gv-fields[data-fieldid="map"]' ).filter( ':visible' ).length !== 0;
		const isMapTemplate = $( '#gravityview_directory_template' ).val() === 'map';

		if ( ! isMapTemplate && ! hasMapField ) {
			return;
		}

		setTimeout( () => self.confirmModifyRest( self.textModifyRestOnLoadWithMap ), 500 );
	};

	/**
	 * Confirm the user wants to enable the REST API when a map is added to the view. It will use a default text and a window.confirm by default.
	 *
	 * @since 2.2
	 *
	 * @param {string} text The text to display in the confirmation modal.
	 *
	 * @return void
	 */
	self.confirmModifyRest = ( text = self.textModifyRestOnMapChanges ) => {
		if ( ! self.canModifyRestPermissionsFields() ) {
			return;
		}

		if ( window.confirm( text ) ) {
			self.modifyRestPermissionFields();
		} else {

		}
	};

	/**
	 * Determines if the REST API permissions fields can be modified.
	 *
	 * @since 2.2
	 *
	 * @return {boolean}
	 */
	self.canModifyRestPermissionsFields = () => {
		const $enable_field = $( '[name="template_settings[rest_enable]"]' );
		const $enable_checkbox = $enable_field.filter( '[type="checkbox"]' );
		const $disable_field = $( '[name="template_settings[rest_disable]"]' );
		const $disable_checkbox = $disable_field.filter( '[type="checkbox"]' );

		return ! ( ( $enable_checkbox.length && $enable_checkbox.is( ':checked' ) ) || ( $disable_checkbox.length && $disable_checkbox.not( ':checked' ) ) );
	};

	/**
	 * Modify the REST API permissions fields to enable the REST API for this View.
	 *
	 * @since 2.2
	 *
	 * @return {void}
	 */
	self.modifyRestPermissionFields = () => {
		// If the fields are not available, don't do anything.
		if ( ! self.canModifyRestPermissionsFields() ) {
			return;
		}

		const $enable_field = $( '[name="template_settings[rest_enable]"]' );
		const $enable_checkbox = $enable_field.filter( '[type="checkbox"]' );
		const $disable_field = $( '[name="template_settings[rest_disable]"]' );
		const $disable_checkbox = $disable_field.filter( '[type="checkbox"]' );

		// If the enable field is not available, don't do anything.
		if ( $enable_field.length ) {
			$enable_field.val( 1 );
			$enable_checkbox.prop( 'checked', true );
		}

		// If the disable field is not available, don't do anything.
		if ( $disable_field.length ) {
			$disable_field.val( 0 );
			$disable_checkbox.prop( 'checked', false );
		}

		return;
	};

	/**
	 * Trigger the `self.confirmModifyRest` method, bound to the `gravityview/field-added` event.
	 * Will only trigger if the field added is a map field.
	 *
	 * @since 2.2
	 *
	 * @param {Event} event Browser Event.
	 * @param {HTMLElement} field The field that was added.
	 */
	self.onFieldAddedChangeModifyRest = ( event, field ) => {
		const $field = $( field );

		if ( ! $field.is( '[data-fieldid="map"]' ) ) {
			return;
		}

		self.confirmModifyRest();
	};

	/**
	 * Bind the `self.onTemplateChangeModifyRest` method to the `#gravityview_directory_template` change event.
	 * Bind the `self.onFieldAddedChangeModifyRest` method to the `gravityview/field-added` event.
	 *
	 * @since 2.2
	 *
	 * @return {void}
	 */
	self.bindModifyRestOnChangeTemplate = () => {
		const $body = $( 'body' );
		$body.one( 'click', '#gv_switch_view_button', () => {
			$( document ).on( 'change', '#gravityview_directory_template', self.onTemplateChangeModifyRest );
		} );

		$body.on( 'gravityview/field-added', self.onFieldAddedChangeModifyRest );
	};

	/**
	 * On View Form change, update the Address Fields
	 */
	self.bindToFormChange = function () {
		$( document )
			.bind( 'gravityview_form_change', self.updateFields );
	};

	/**
	 * Bind on Info Window enable setting to Show/hide Info window settings
	 */
	self.bindInfowindowSettings = function () {
		$( '#gv_maps_se_map_info_enable' )
			.on( 'change', self.hideInfowindowSettings )
			.change();
	};

	/**
	 * Show/hide Info window settings
	 * @since 1.4
	 */
	self.hideInfowindowSettings = function () {
		var _this = $( this );
		var otherSettings = $( '#gravityview_maps_settings' ).find( 'table tr:has(:input[name*=map_info]):gt(0)' );
		if ( _this.is( ':checked' ) ) {
			otherSettings.fadeIn();
		} else {
			otherSettings.fadeOut( 100 );
		}
	};


	/**
	 * AJAX request to update Address Fields
	 */
	self.updateFields = function () {

		// While it's loading, disable the field, remove previous options, and add loading message.
		$( self.addressFieldSelector )
			.prop( 'disabled', true )
			.empty()
			.append( '<option>' + gvGlobals.loading_text + '</option>' );

		// get address fields dropdown
		var data = {
			action: 'gv_address_fields',
			formid: $( self.formIdSelector ).val(),
			nonce : self.nonce
		};

		$.ajax( {
				type : 'POST',
				url  : ajaxurl,
				data : data,
				async: true
			} )
			.done( function ( response ) {
				if ( response !== 'false' && response !== '0' ) {
					$( self.addressFieldSelector ).empty().append( response ).prop( 'disabled', false );
				}
			} )
			.fail( function ( jqXHR ) {

				// Something went wrong
				console.log( 'Error while loading the GravityView Map Address Fields. Please try again or contact GravityView support.' );
				console.log( jqXHR );

			} );

	};

	// Handling Icons

	self.bindMapIcons = function () {
		if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
			$( self.addIconSelector ).on( 'click', self.addMapIcon );
		}
		self.initMapIconsTooltip();
		$( 'body' ).on( 'click', self.setIconSelector, self.setMapIcon );
	};

	/**
	 * Loads WP Media Upload
	 * @param e
	 */
	self.addMapIcon = function () {

		var mapIconUploader = wp.media( {
				title   : self.labelMapIconUploadTitle,
				button  : {
					text: self.labelMapIconUploadButton
				},
				multiple: false,
				library : { type: 'image' }
			} )
			.on( 'select', function () {
				var attachment = mapIconUploader.state().get( 'selection' ).first().toJSON();
				self.setMapIconInput( attachment.url );
			} )
			.open();

	};

	self.initMapIconsTooltip = function () {

		$( self.selectIconSelector )
			.tooltip( {
				content      : function () {
					return $( self.availableIconsSelector ).html();
				},
				close        : function () {
					$( this ).attr( 'data-tooltip', null );
				},
				open         : function () {
					$( this ).attr( 'data-tooltip', 'active' );
				},
				closeOnEscape: true,
				disabled     : true, // Don't open on hover
				position     : {
					my: "center bottom",
					at: "center top-12"
				},
				tooltipClass : 'top'
			} )
			// add title attribute so the tooltip can continue to work (jquery ui bug?)
			.attr( 'title', '' )
			.on( 'mouseout focusout', function ( e ) {
				e.stopImmediatePropagation();
			} )
			.click( function ( e ) {
				// add title attribute so the tooltip can continue to work (jquery ui bug?)
				$( this ).attr( 'title', '' );

				e.preventDefault();
				//e.stopImmediatePropagation();

				$( this ).tooltip( 'open' );

			} );
	};

	self.bindGlobalNoticeMapViewsWithoutRest = () => {
		const $triggerButton = $( "[data-js='gk-gravitymaps-trigger-update-map-views-rest-api']" );

		if ( ! $triggerButton.length ) {
			return;
		}

		const $container = $triggerButton.parents( '.gk-notice' ).eq( 0 );
		const isDismissible = $container.is( '.is-dismissible' );

		$triggerButton.on( 'click', ( event ) => {
			event.preventDefault();

			$triggerButton.prop( 'disabled', true );
			$triggerButton.before( '<span class="spinner is-active"></span>' );

			$.ajax( {
				url     : ajaxurl,
				method  : 'POST',
				data    : {
					action        : 'gk_gravitymaps_enable_rest_on_map_views',
					nonce         : $triggerButton.data( 'gk-ajax-nonce' ),
					single_view_id: $triggerButton.data( 'gk-single-view-id' ),
				},
				success : ( response ) => {
					// Handle the success response, e.g., display a success message
					if ( response.success ) {
						$container.find( 'p:has(.button)' ).remove();
						$container.find( 'p:first' ).text( response.data.text );
						setTimeout( () => $container.fadeOut(), 4000 );

						self.modifyRestPermissionFields();
					} else {
						$container.find( 'p:first' ).text( response.data.text );
					}
				},
				error   : () => {
					const $error = $( '<p>' ).text( response.data.text );
					$container.prepend( $error );
					setTimeout( () => $error.remove(), 4000 );
				},
				complete: () => {
					// Remove the loading message or spinner
					$triggerButton.prop( 'disabled', false );
					$triggerButton.siblings( '.spinner' ).remove();
				}
			} );
		} );
	};

	self.setMapIcon = function ( e ) {
		var src = $( e.target ).attr( 'src' );
		self.setMapIconInput( src );

		$( self.selectIconSelector ).tooltip( 'close' );
	};

	self.setMapIconInput = function ( url ) {
		$( self.inputIconSelector ).val( url )
			.prev().attr( 'src', url );
	};

	// helpers
	self.isGravityViewScreen = function () {
		return 'gravityview' === pagenow;
	};

	$( self.init );

	$( window ).on( 'load', self.onLoadCheckForRestValue );
}( jQuery ) );
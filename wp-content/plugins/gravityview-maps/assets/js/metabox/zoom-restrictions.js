( ( $, obj, GravityMaps ) => {
	"use strict"

	// Create a global Reference for this globally.
	GravityMaps.MetaboxZoomRestrictions = obj;

	/**
	 * Store the selectors for the zoom fields.
	 *
	 * @since 2.2.1
	 *
	 * @type {{defaultZoom: string, maxZoom: string, minZoom: string}}
	 */
	obj.selectors = {
		defaultZoom: '#gv_maps_se_map_zoom',
		minZoom: '#gv_maps_se_map_minzoom',
		maxZoom: '#gv_maps_se_map_maxzoom',
	};

	/**
	 * Bind the events related to the zoom fields.
	 *
	 * @since 2.2.1
	 *
	 * @return {void}
	 */
	obj.bind = () => {
		$( obj.selectors.minZoom ).on( 'change.gravitymaps', obj.onMinChange );
		$( obj.selectors.maxZoom ).on( 'change.gravitymaps', obj.onMaxChange );
	};

	/**
	 * Unbind the events related to the zoom fields.
	 *
	 * @since 2.2.1
	 *
	 * @return {void}
	 */
	obj.unbind = () => {
		$( obj.selectors.minZoom ).off( 'change.gravitymaps', obj.onMinChange );
		$( obj.selectors.maxZoom ).off( 'change.gravitymaps', obj.onMaxChange );
	};

	/**
	 * Triggered when the min zoom changes.
	 * It will update the max zoom options and the default zoom when needed.
	 *
	 * @since 2.2.1
	 *
	 * @param {Event} event
	 *
	 * @return {void}
	 */
	obj.onMinChange = ( event ) => {
		const $defaultZoom = $( obj.selectors.defaultZoom );
		const $field = $( event.target );
		const $maxZoom = $( obj.selectors.maxZoom );
		const minZoom = parseInt( $field.val(), 10 );
		const maxZoom = parseInt( $maxZoom.val(), 10 );
		if ( maxZoom < minZoom ) {
			$maxZoom.val( minZoom );
		}

		const $maxOptions = $maxZoom.children( 'option' );

		$maxOptions.each( ( index, option ) => {
			const $option = $( option );
			const value = parseInt( $option.val(), 10 );

			// Dont change the zero value.
			if ( value === 0 ) {
				return;
			}

			if ( value < minZoom ) {
				$option.prop( 'disabled', true );
			} else {
				$option.prop( 'disabled', false );
			}
		} );

		const defaultZoom = parseInt( $defaultZoom.val(), 10 );
		const newDefaultZoom = Math.min( Math.max( defaultZoom, minZoom ), maxZoom );
		if ( newDefaultZoom !== defaultZoom ) {
			$defaultZoom.val( newDefaultZoom );
		}

		$defaultZoom.children( 'option' ).each( ( index, option ) => {
			const $option = $( option );
			const value = parseInt( $option.val(), 10 );
			if ( value > maxZoom || value < minZoom ) {
				$option.prop( 'disabled', true );
			} else {
				$option.prop( 'disabled', false );
			}
		} );
	};

	/**
	 * Triggered when the max zoom changes.
	 * It will update the min zoom options and the default zoom when needed.
	 *
	 * @since 2.2.1
	 *
	 * @param {Event} event
	 *
	 * @return {void}
	 */
	obj.onMaxChange = ( event ) => {
		const $defaultZoom = $( obj.selectors.defaultZoom );
		const $field = $( event.target );
		const $minZoom = $( obj.selectors.minZoom );
		const minZoom = parseInt( $minZoom.val(), 10 );
		const maxZoom = parseInt( $field.val(), 10 );
		if ( minZoom > maxZoom ) {
			$minZoom.val( maxZoom );
		}

		const $minOptions = $minZoom.children( 'option' );

		$minOptions.each( ( index, option ) => {
			const $option = $( option );
			const value = parseInt( $option.val(), 10 );

			// Dont change the zero value.
			if ( value === 0 ) {
				return;
			}

			if ( value > maxZoom ) {
				$option.prop( 'disabled', true );
			} else {
				$option.prop( 'disabled', false );
			}
		} );

		const defaultZoom = parseInt( $defaultZoom.val(), 10 );
		const newDefaultZoom = Math.min( Math.max( defaultZoom, minZoom ), maxZoom );
		if ( newDefaultZoom !== defaultZoom ) {
			$defaultZoom.val( newDefaultZoom );
		}

		$defaultZoom.children( 'option' ).each( ( index, option ) => {
			const $option = $( option );
			const value = parseInt( $option.val(), 10 );
			if ( value > maxZoom || value < minZoom ) {
				$option.prop( 'disabled', true );
			} else {
				$option.prop( 'disabled', false );
			}
		} );
	};

	/**
	 * Trigger the change event on the zoom fields.
	 *
	 * @since 2.2.1
	 *
	 * @return {void}
	 */
	obj.triggerChange = () => {
		$( obj.selectors.minZoom ).trigger( 'change.gravitymaps' );
		$( obj.selectors.maxZoom ).trigger( 'change.gravitymaps' );
		$( obj.selectors.defaultZoom ).trigger( 'change.gravitymaps' );
	};

	/**
	 * Triggers when Ready of the document.
	 *
	 * @since 2.2.1
	 *
	 * @return {void}
	 */
	obj.ready = () => {
		if ( ! obj.shouldLoad() ) {
			return;
		}

		obj.bind();
		obj.triggerChange();
	};

	/**
	 * Check if the zoom restrictions should be loaded.
	 *
	 * @since 2.2.1
	 *
	 * @return {boolean}
	 */
	obj.shouldLoad = () => {
		const isGravityViewScreen = ( typeof pagenow !== 'undefined' && 'gravityview' === pagenow ) ;

		return GravityMaps.hooks.applyFilters( 'gk.maps.should_load_zoom_restrictions', isGravityViewScreen );
	};

	$( document ).ready( obj.ready );
} )( jQuery, {}, window.GravityKit.GravityMaps );


/**
 * Custom js script loaded Gravity Forms form (public)
 *
 * @package   GravityView Maps
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 *
 * globals jQuery
 */

window.GV_MAPS_FIELDS = window.GV_MAPS_FIELDS || {};

( function( $ ) {

    "use strict";

    /**
     * Passed by wp_localize_script() with some settings
     * @type {object}
     */
    var self = $.extend( {
        'iconsContainer': '.gvmaps-available-icons',
        'selectIcon': '.gvmaps-select-icon-button',
        'selectIconInput':  '.gvmaps-select-icon-input',
        'setIconSelector': '.gv_maps_icons'

    }, GV_MAPS_FIELDS );


    self.init = function() {

        // hide the available icons
        self.hideAvailableIcons();

        // bind click events to select icon button
        self.bindClickSelectIcon();


    };

    self.hideAvailableIcons = function() {
        $( self.iconsContainer ).hide();
    };

    self.bindClickSelectIcon = function() {
        $('body')
            .on( 'click', self.selectIcon, self.toggleAvailableIcons )
            .on( 'click', self.setIconSelector, self.setIcon );
    };

    self.toggleAvailableIcons = function( e ) {
        e.preventDefault();

	    $( e.target )
		    .parents( '.ginput_container' )
	        .find( self.iconsContainer )
	            .slideToggle( '300' );

    };

    self.setIcon = function( e ) {
        e.preventDefault();
        var icon = $( e.target),
            url = icon.attr('src'),
            parent = icon.parents( '.ginput_container');

	    parent
		    // Remove selected class from current icon
	        .find( '.selected' )
	            .removeClass('selected')

		    .end() // Restart from parent

	        // assign values
	        .find( self.selectIconInput )
	            .val( url ).prev().attr( 'src', url );

	    // Set new selected icon
	    icon.addClass( 'selected' );

	    // close available icons container
	    parent.find( self.selectIcon).trigger('click');
    };



    $( self.init );


}(jQuery));
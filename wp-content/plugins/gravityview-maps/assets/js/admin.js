/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 *
 * globals jQuery, GV_MAPS_ADMIN
 */

( function( $ ) {

    "use strict";

    /**
     * Passed by wp_localize_script() with some settings
     * @type {object}
     */
    var self = $.extend( {
        'metaboxId': '#gravityview_maps_settings',
        'addressFieldSelector': '#gv_maps_se_map_address_field',
        'formIdSelector': '#gravityview_form_id',
        'addIconSelector': '#gv_maps_se_add_icon',
        'selectIconSelector': '#gv_maps_se_select_icon',
        'inputIconSelector': '#gv_maps_se_map_marker_icon',
        'availableIconsSelector': '#gv_maps_se_available_icons',
        'setIconSelector': '.gv_maps_icons'
    }, GV_MAPS_ADMIN );


    self.init = function() {
        if( !self.isGravityViewScreen ) {
            return;
        }

        // settings handling
        self.bindToFormChange();

        // map icons add & select
        self.bindMapIcons();

        // toggle infowindow settings
        self.bindInfowindowSettings();

        self.hideOnDataTables();
    };

	/**
     * When DataTables layout is selected, hide the Map fields and widgets using CSS
     * @since 1.4.2
     */
    self.hideOnDataTables = function () {
        $(document).on('change', '#gravityview_directory_template', function() {
            $( 'body' ).toggleClass( 'gravityview-template-datatables_table', ( 'datatables_table' === $( this ).val() ) );
        });
    };

    /**
     * On View Form change, update the Address Fields
     */
    self.bindToFormChange = function() {
        $(document)
	        .bind( 'gravityview_form_change', self.updateFields );
    };

    /**
     * Bind on Info Window enable setting to Show/hide Info window settings
     */
    self.bindInfowindowSettings = function() {
        $('#gv_maps_se_map_info_enable')
            .on( 'change', self.hideInfowindowSettings )
            .change();
    };

    /**
     * Show/hide Info window settings
     * @since 1.4
     */
    self.hideInfowindowSettings = function() {
        var _this = $(this);
        var otherSettings = $('#gravityview_maps_settings').find('table tr:has(:input[name*=map_info]):gt(0)');
        if( _this.is(':checked') ) {
            otherSettings.fadeIn();
        } else {
            otherSettings.fadeOut( 100 );
        }
    };


    /**
     * AJAX request to update Address Fields
     */
    self.updateFields = function() {

        // While it's loading, disable the field, remove previous options, and add loading message.
        $( self.addressFieldSelector )
            .prop( 'disabled', true )
            .empty()
            .append('<option>' + gvGlobals.loading_text + '</option>');

        // get address fields dropdown
        var data = {
            action: 'gv_address_fields',
            formid: $( self.formIdSelector ).val(),
            nonce: self.nonce
        };

        $.ajax( {
            type: 'POST',
            url: ajaxurl,
            data: data,
            async: true
        })
        .done( function ( response ) {
            if ( response !== 'false' && response !== '0' ) {
                $( self.addressFieldSelector ).empty().append( response ).prop( 'disabled', false );
            }
        })
        .fail(function ( jqXHR ) {

            // Something went wrong
            console.log( 'Error while loading the GravityView Map Address Fields. Please try again or contact GravityView support.' );
            console.log( jqXHR );

        });

    };

    // Handling Icons

    self.bindMapIcons = function() {
        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
            $( self.addIconSelector ).on( 'click', self.addMapIcon );
        }
        self.initMapIconsTooltip();
        $('body').on( 'click', self.setIconSelector ,self.setMapIcon );
    };

    /**
     * Loads WP Media Upload
     * @param e
     */
    self.addMapIcon = function() {

        var mapIconUploader = wp.media({
            title: self.labelMapIconUploadTitle,
            button: {
                text: self.labelMapIconUploadButton
            },
            multiple: false,
            library : { type : 'image'}
        })
            .on( 'select', function() {
                var attachment = mapIconUploader.state().get('selection').first().toJSON();
                self.setMapIconInput( attachment.url );
            })
            .open();

    };

    self.initMapIconsTooltip = function() {

        $( self.selectIconSelector )
            .tooltip({
                content: function () {
                    return $( self.availableIconsSelector ).html();
                },
                close: function () {
                    $(this).attr('data-tooltip', null );
                },
                open: function () {
                    $(this).attr('data-tooltip', 'active');
                },
                closeOnEscape: true,
                disabled: true, // Don't open on hover
                position: {
                    my: "center bottom",
                    at: "center top-12"
                },
                tooltipClass: 'top'
            })
            // add title attribute so the tooltip can continue to work (jquery ui bug?)
            .attr( 'title', '' )
            .on('mouseout focusout', function ( e ) {
                e.stopImmediatePropagation();
            })
            .click( function( e ) {
                // add title attribute so the tooltip can continue to work (jquery ui bug?)
                $(this).attr( 'title', '' );

                e.preventDefault();
                //e.stopImmediatePropagation();

                $(this).tooltip( 'open' );

            });
    };

    self.setMapIcon = function( e ) {
        var src = $(e.target).attr( 'src' );
        self.setMapIconInput( src );

	    $( self.selectIconSelector ).tooltip('close');
    };

    self.setMapIconInput = function( url ) {
        $( self.inputIconSelector ).val( url )
            .prev().attr( 'src', url );
    };

    // helpers
    self.isGravityViewScreen = function() {
        return 'gravityview' === pagenow;
    };

    $( self.init );


}(jQuery));
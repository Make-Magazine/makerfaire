jQuery( document ).bind( 'gform_post_render', function( event, form_id, page ) {

    var GF_Geo = {

        // the processed form
        form : typeof gfgeo_gforms[form_id] != undefined ? gfgeo_gforms[form_id] : false,

        // page number
        page_number : page,

        // disable place details by default
        place_details_enabled : false,

        //protocol
        protocol : gfgeo_options.protocol,

        // plugin's options
        options : gfgeo_options,
        
        // plugin prefix
        prefix : 'gfgeo-',

        // default country code for Google API
        country_code : 'US',
        
        // navigator timeout  limit 
        navigator_timeout : 10000,

        // high accuracy
        navigator_high_accuracy : gfgeo_options.high_accuracy,

        // geocoder timeout limit
        geocoder_timeout : 10000,

        // geocoder timeout message
        geocoder_timeout_message : 'The request to verify the location timed out.',

        // geocoder ID being processed
        geocoder_id : false,

        // maps object
        maps : {},

        autocompletes : {},

        // geocoder processing object
        processing : {
            status             : false,
            element            : false,
            location           : false,
            advanced_address   : false,
            selected_address   : '',
            autocomplete_place : '',
            found_message      : '',
            hide_error_message : '',
            locator_id         : '',
            element_id         : ''
        },

        // Navigator error messages
        navigator_error_messages : {
            1 : 'User denied the request for Geolocation.',
            2 : 'Location information is unavailable.',
            3 : 'The request to get the user\'s location timed out.',
            4 : 'An unknown error occurred',
            5 : 'Sorry! Geolocation is not supported by this browser.'
        },

        // geocoder failed error messages
        geocoder_failed_error_message : {
            'advanced_address_geocoder' : 'We could not verify the address you entered.',
            'address_geocoder'          : 'We could not verify the address you entered.',
            'coords_geocoder'           : 'We could not verify the coordinates you entered.',
            'map'           			: 'We could not verify the location you selected.'
        },

        page_locator : {
            triggered : false
        },

        map_field_exists : false,

        autocomplete_triggered : false,

        //ip locator
        ip_locator : gfgeo_options.ip_locator,

        //ip token
        ip_token : gfgeo_options.ip_token,

        // hide all failed messages
        hide_error_messages : gfgeo_options.hide_error_messages,

        // Verify map and coords geocoding?
        verify_geocoding : false,

        directions_display : [],

        destinationMarkers : [],

        /**
         * Run on page load
         * 
         * @return {[type]} [description]
         */
        init : function() {
            
            // abort and display error message in console if form object does not exists
            if ( ! GF_Geo.form ) {
                
                console.log( 'something went wrong while trying to retrive the form object.' );
                
                return false;
            }

            // gform exist only when form uses ajax.
            if ( typeof gform !== 'undefined' ) {
                GF_Geo = gform.applyFilters( 'gfgeo_form_object', GF_Geo, GF_Geo.form );
            }

            // disable IP locator if maxmind failed loading
            if ( GF_Geo.ip_locator == 'maxmind' ) {
                if ( typeof geoip2 === 'undefined' ) {
                    GF_Geo.ip_locator = false;
                } 
            }

            // loop form fields and initiate geolocation features
            jQuery.each( GF_Geo.form.fields, function( field, field_values ) {

                // field ID made of the form ID and field ID
                this_field_id = GF_Geo.form.id + '_' + field_values.id;

                // init geocoder field
                if ( field_values.type == 'gfgeo_geocoder' ) {
                    
                    // remove geocoder label to make sure it does not show in the form
                    jQuery( 'label[for="input_' + this_field_id + '"]' ).remove();

                    // do tasks if on page load or if updating form ( user or post update ) but 
                    //if ( GF_Geo.form.gfgeo_page_load == true && jQuery( '#input_' + this_field_id + '_latitude' ).val() == '' && jQuery( '#input_' + this_field_id + '_longitude' ).val() == '' ) {
                    if ( GF_Geo.form.gfgeo_page_load == true || GF_Geo.form.gfgeo_form_update == true ) {

                        if ( jQuery( '.gfgeo_default_latitude.' + this_field_id ).val() != undefined && jQuery( '.gfgeo_default_latitude.' + this_field_id ).val() != '' && jQuery( '.gfgeo_default_longitude.' + this_field_id ).val() != undefined && jQuery( '.gfgeo_default_longitude.' + this_field_id ).val() != '' ) {

                            // get the geocoder ID
                            GF_Geo.geocoder_id = this_field_id;

                            // geocode default coords on page load
                            //GF_Geo.geocoder( 'default_coords_geocoder', [ field_values.gfgeo_default_latitude, field_values.gfgeo_default_longitude ], GF_Geo.default_coords_geocoder_success, false ); 
                            GF_Geo.geocoder( 'default_coords_geocoder', [ jQuery( '.gfgeo_default_latitude.' + this_field_id ).val(), jQuery( '.gfgeo_default_longitude.' + this_field_id ).val() ], GF_Geo.default_coords_geocoder_success, false ); 
                        
                        } else if ( GF_Geo.form.gfgeo_page_load == true && field_values.gfgeo_page_locator == true ) {

                            // delay page locator to allow default coords to first geocoded if exists on the form
                            setTimeout( function() {

                                // set it to true to trigger it only once
                                GF_Geo.page_locator.triggered = true;

                                // found message
                                GF_Geo.processing.found_message = field_values.gfgeo_location_found_message;

                                // hide failed message?
                                GF_Geo.processing.hide_error_message = field_values.gfgeo_hide_location_failed_message;
                       
                                // get the geocoder ID
                                GF_Geo.geocoder_id = GF_Geo.form.id + '_' + field_values.id;

                                // if IP locator feature enabled set it in processing
                                if ( GF_Geo.ip_locator != false ) {
                                    GF_Geo.processing.ip_locator = field_values.gfgeo_ip_locator_status !== undefined ? field_values.gfgeo_ip_locator_status : '';
                                }

                                if ( field_values.pageNumber == GF_Geo.page_number ) {

                                    // if using ip address locator by default
                                    if ( GF_Geo.ip_locator != false && GF_Geo.processing.ip_locator == 'default' ) {
                                        
                                        //console.log( 'ip locator' );

                                        // run navigator
                                        GF_Geo.ip_navigator( 'ip_page_locator', GF_Geo.page_locator_success, false );
                                    
                                    // otherwise using HTML5 locator
                                    } else {

                                        //console.log( 'locator' );
                                        
                                        // run auto locator
                                        GF_Geo.navigator( 'page_locator', GF_Geo.page_locator_success, false );
                                    }
                                }

                            }, 2000 );

                        }
                    }
                } 

                // init maps
                if ( field_values.type == 'gfgeo_map' && jQuery( '#gfgeo-map-' + this_field_id ).length && field_values.adminOnly != true ) {

                    GF_Geo.map_field_exists = true;

                    GF_Geo.render_map( jQuery( '#gfgeo-map-' + this_field_id ).data(), field_values );
                }

                // init address fields
                if ( field_values.type == 'gfgeo_address' ) {
                    
                    // enable field geocoding only if geocoder ID set and geocoder field exists
                    if ( field_values.gfgeo_geocoder_id != '' && jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + GF_Geo.form.id + '_' + field_values.gfgeo_geocoder_id + '.status' ).length != 0 ) {
             
                        GF_Geo.address_field_init( jQuery( '#gfgeo-address-locator-wrapper-' + this_field_id ).find( ':input' ) );   
                    }

                    // enable address autocomplete
                    if ( field_values.gfgeo_address_autocomplete == true ) {
            
                        GF_Geo.address_autocomplete( 'input_' + this_field_id, false );
                    }
                }

                // init coordinates fields
                if ( field_values.type == 'gfgeo_coordinates' ) {

                    if ( field_values.gfgeo_geocoder_id != '' && jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + GF_Geo.form.id + '_' + field_values.gfgeo_geocoder_id + '.status' ).length != 0 ) {
                        
                        GF_Geo.coordinates_field_init( jQuery( '#gfgeo-coordinates-wrapper-' + this_field_id ) );
                    }
                }
                
                // apply features to advanced address fields
                if ( field_values.type == 'address' && ( field_values.gfgeo_disable_field_geocoding === 'undefined' || field_values.gfgeo_disable_field_geocoding != true ) ) {

                    field_wrapper = jQuery( '#field_' + this_field_id );

                    // enable geocoder if exists
                    if ( field_values.gfgeo_geocoder_id != '' && jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + GF_Geo.form.id + '_' + field_values.gfgeo_geocoder_id + '.status' ).length != 0 ) {
             
                        GF_Geo.advanced_address_field_init( field_wrapper ); 

                        // enable address autocomplete
                        if ( field_values.gfgeo_address_autocomplete == true ) {

                            GF_Geo.address_autocomplete( field_wrapper.find( '.advanced-address-autocomplete' ).attr( 'id' ), false );
                        } 
                    }
                }

                if ( field_values.type == 'text' || field_values.type == 'post_custom_field' ) {
                    
                    // enable address autocomplete
                    if ( field_values.gfgeo_address_autocomplete == true ) {

                        GF_Geo.address_autocomplete( 'input_' + this_field_id, false );
                    }
                }
            });

            // locator buttons on click
            jQuery( '.' + GF_Geo.prefix + 'locator-button' ).on( 'click', function() {

                if ( ! GF_Geo.processing.status ) {

                    // found message
                    GF_Geo.processing.found_message = jQuery( this ).data( 'found_message' );
                    
                    // hide failed message?
                    GF_Geo.processing.hide_error_message = jQuery( this ).data( 'hide_failed_message' );

                    // found message
                    GF_Geo.processing.locator_id = jQuery( this ).data( 'locator_id' );

                    // get the geocoder ID
                    GF_Geo.geocoder_id = jQuery( this ).data( 'geocoder_id' );

                    if ( GF_Geo.ip_locator != false ) {
                        GF_Geo.processing.ip_locator = jQuery( this ).data( 'ip_locator' );
                    }

                    // if using ip address locator by default
                    if ( GF_Geo.ip_locator != false && GF_Geo.processing.ip_locator == 'default' ) {
                        
                        console.log( 'ip locator' );

                        // run navigator
                        GF_Geo.ip_navigator( 'ip_locator_button', GF_Geo.locator_button_success, false );
                    
                    // otherwise using HTML5 locator
                    } else {

                        console.log( 'locator' );
                        // run navigator
                        GF_Geo.navigator( 'locator_button', GF_Geo.locator_button_success, false );
                    }
                }
            });   

            // init this function only if map exists on the page
            if ( GF_Geo.map_field_exists ) {
                
                // resize map trigger
                GF_Geo.resize_map_on_show();
            }     
        },

        /**
         * While processing geocoder disable all geocoder fields untill done
         * 
         * @return {[type]} [description]
         */
        processing_start : function( element, location ) {
            
            // processing object
            GF_Geo.processing.status   = true;
            GF_Geo.processing.element  = element;
            GF_Geo.processing.location = location; 

            // disable maps
            jQuery.each( GF_Geo.maps, function( map_id, map_options ) {
                GF_Geo.maps[map_id].map.setOptions( { draggable: false } );
                GF_Geo.maps[map_id].marker.setDraggable( false );
            });

            // disable form submit button untill done
            jQuery( 'body' ).find( 'input[type="submit"], .gfgeo-locator-button, input.gfgeo-address-field, input.gfgeo-coordinates-field, .gfgeo-advanced-address input' ).prop( 'disabled', true );
            
            // disable geo input fields
            //jQuery( 'body' ).find( 'input.gfgeo-address-field, input.gfgeo-coordinates-field, .gfgeo-advanced-address input' ).attr( 'readonly', 'readonly' );
        },

        /**
         * End of processing. Enable geo features back
         * 
         * @return {[type]} [description]
         */
        processing_end : function() {

            setTimeout( function() {

                GF_Geo.processing = {
                    status             : false,
                    element            : false,
                    location           : false,
                    advanced_address   : false,
                    selected_address   : '',
                    autocomplete_place : '',
                    found_message      : '',
                    locator_id         : '',
                    element_id         : ''
                };

                //GF_Geo.processing = { 
                  //  'status'  : false
                //}

                // enable maps
                jQuery.each( GF_Geo.maps, function( map_id, map_options ) {
                    GF_Geo.maps[map_id].map.setOptions( { draggable: true } );
                    GF_Geo.maps[map_id].marker.setDraggable( true );
                });

                // enable geo input fields and submit buttons
                jQuery( 'body' ).find( 'input[type="submit"], .gfgeo-locator-button, input.gfgeo-address-field, input.gfgeo-coordinates-field, .gfgeo-advanced-address input' ).prop( 'disabled', false );
                //jQuery( 'body' ).find( 'input.gfgeo-address-field, input.gfgeo-coordinates-field, .gfgeo-advanced-address input' ).removeAttr( 'readonly'  );
        
                // show locator buttons
                setTimeout( function() {
                    jQuery( '.' + GF_Geo.prefix + 'locator-loader' ).fadeOut( 'fast', function() {
                        jQuery( '.' + GF_Geo.prefix + 'locator-button' ).fadeIn();
                    });
                }, 500 );
            }, 300 );
        },

        /**
         * Clear all location fields before populating new data
         * 
         * @return {[type]} [description]
         */
        clear_fields : function( geocoder_id, element ) {

            // clear coords only if needed
            if ( element != 'coords_geocoder' ) {
                jQuery( '.gfgeo-coordinates-field[data-geocoder_id="' + geocoder_id + '"]' ).val( '' ).trigger( 'change' );
            }

            // Update maps based on new coords if needed    
            if ( element == 'map' ) {

                var map_address_disabled = jQuery( '#gfgeo-map-' + GF_Geo.processing.element_id ).data( 'disable_address_output' );

                if ( map_address_disabled == true ) {
                    
                    return;
                }
            }

            // clear advanced address fields
            if ( element != 'advanced_address_geocoder' ) {
                jQuery( '.gfgeo-advanced-address-geocoder-id-' + GF_Geo.geocoder_id ).find( 'span input' ).val( '' ).trigger( 'change' );
                jQuery( '.gfgeo-advanced-address-geocoder-id-' + GF_Geo.geocoder_id ).find( 'span select option[value=""]' ).prop( 'selected', true ).trigger( 'change' );
            }

            // clear field only if needed
            //if ( jQuery( '.gfgeo-geocoded-field-' + geocoder_id + '.status' ).val() == 1 ) {
              
                // clear geocoder fields
                jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id +'[type="text"], input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id +'[type="hidden"]' ).val( '' ).trigger( 'change' );

                /*** clear dynamic text fields ****/

                //jQuery( 'input[data-geocoder_id="' + geocoder_id + '"][type="text"]' ).val( '' ).trigger( 'change' );
                //jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '[type="text"]' ).val( '' ).trigger( 'change' );
                jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id ).find( 'input[type="text"]' ).val( '' ).trigger( 'change' );
                
                /*** clear dynamic hidden fields ****/

                //jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '[type="hidden"]' ).val( '' ).trigger( 'change' );
                //jQuery( 'input[data-geocoder_id="' + geocoder_id + '"][type="hidden"]' ).val( '' ).trigger( 'change' );
                jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id ).find( 'input[type="hidden"]' ).val( '' ).trigger( 'change' );
                
                /***** clear advanced address fields *****/

                //jQuery( '.gfield.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span input' ).val('');
            //}

            if ( typeof GF_Geo.directions_display[ geocoder_id ] !== 'undefined' ) {

    			GF_Geo.directions_display[ geocoder_id ].setMap( null );

    			if ( typeof GF_Geo.destinationMarkers[ geocoder_id ] !== 'undefined' ) {
					GF_Geo.destinationMarkers[ geocoder_id ].setMap( null );
				}

				jQuery( '#' + GF_Geo.directions_display[ geocoder_id ].panel.id ).closest( 'li' ).slideUp( function() {
    				jQuery( this ).find( '#' + GF_Geo.directions_display[ geocoder_id ].panel.id ).html('');
    			});
    		}
            	
            // Clear data of destination geocoder.
            jQuery( '.gfgeo-geocoded-hidden-fields-wrapper' ).each( function() {

            	if ( jQuery( this ).data( 'distance_destination_geocoder_id' ) == GF_Geo.geocoder_id ) {

            		var geoId     = jQuery( this ).data( 'geocoder_id' );
            		var baseClass = '.' + GF_Geo.prefix + 'geocoded-field-' + geoId;
            		var fullClass = baseClass + '.gfgeo-distance_text, ' + baseClass + '.gfgeo-distance_value, ' + baseClass + '.gfgeo-duration_text, ' + baseClass + '.gfgeo-duration_value';

            		jQuery( fullClass ).find( 'input[type="hidden"]' ).val( '' ).trigger( 'change' );
            		jQuery( fullClass ).find( 'input[type="text"]' ).val( '' ).trigger( 'change' );

            		if ( typeof GF_Geo.destinationMarkers[ geoId ] !== 'undefined' ) {
						GF_Geo.destinationMarkers[ geoId ].setMap( null );
					}

            		if ( typeof GF_Geo.directions_display[ geoId ] !== 'undefined' ) {

            			GF_Geo.directions_display[ geoId ].setMap( null );

            			jQuery( '#' + GF_Geo.directions_display[ geoId ].panel.id ).closest( 'li' ).slideUp( function() {
            				jQuery( this ).find( '#' + GF_Geo.directions_display[ geoId ].panel.id ).html('');
            			});
            		}
            	}
            });
        },

        /**
         * When geocoder timed out
         * 
         * @return {[type]} [description]
         */
        geocoder_timed_out : function( error_message ) {
        	
            if ( GF_Geo.hide_error_messages || GF_Geo.processing.hide_error_message ) {
                console.log( error_message );
            } else {
                alert( error_message );
            }

            GF_Geo.processing_end();
        },

        /**
         * Geocoder success default callback functions
         * 
         * @return {[type]} [description]
         */
        geocoder_success : function( results ) {

        },

        /**
         * Geocoder failed default callback functions
         * 
         * @return {[type]} [description]
         */
        geocoder_failed : function( status ) {

            var message = GF_Geo.geocoder_failed_error_message[GF_Geo.processing.element];
            var error   = message + '. Error: ' + status;
            
            console.log( error );

            if ( GF_Geo.hide_error_messages || GF_Geo.processing.hide_error_message ) {
                console.log( message );
            } else {
                alert( message );
            }

            // clear fields when geocoding fails.
            GF_Geo.clear_fields( GF_Geo.geocoder_id, 'failed_geocoding' );
            
            GF_Geo.processing_end();
        },

        /**
         * Geocoder function. 
         *
         * Can be used for geocoding an address or reverse geocoding coordinates.
         * 
         * @param  string | array location string if geocoding an address or array of coordinates [ lat, lng ] if reverse geocoding.
         * 
         * @param  {function} success  callback function on success
         * @param  {function} failed   callback function on failed
         * 
         * @return {[type]}          [description]
         */
        geocoder : function( element, location, success, failed ) {

            // prevent multiple geocoding at the same time
            if ( GF_Geo.processing.status ) {
                return;
            }

            GF_Geo.processing_start( element, location );

            var timer_id;
            var timed_out = false;

            // get geocoder data
            // If reverse geocoding 
            if ( typeof location === 'object' ) {

                data = { 
                    'latLng' : new google.maps.LatLng( location[0], location[1] ), 
                    'region' : GF_Geo.options.country_code 
                };

            // otherwise, if geocoding an address
            } else {

                data = { 
                    'address' : location, 
                    'region'  : GF_Geo.options.country_code 
                };
            }

            // init google geocoder
            geocoder = new google.maps.Geocoder();

            // run geocoder
            geocoder.geocode( data, function( results, status ) {
                
                // abort if request timed out.
                if ( timed_out ) {

                    GF_Geo.geocoder_timed_out( GF_Geo.geocoder_timeout_message );

                    return;

                // Clear timer on success
                } else {

                    // this request succeeded, so cancel timer
                    clearTimeout( timer_id );
                }

                // on success
                if ( status == google.maps.GeocoderStatus.OK ) {
 
                    return ( success != undefined && success != false ) ? success( results[0] ) : GF_Geo.geocoder_success( results[0] );

                // on failed      
                } else {

                	// do something custom when map updates
			        jQuery( document ).trigger( 
			            'gfgeo_geocoder_failed', 
			            [ GF_Geo ] 
			        );

	            	if ( element == 'map' && ! GF_Geo.verify_geocoding ) {

	            		return GF_Geo.map_geocoder_failed_allowed( location, status );

	            	} else if ( element == 'coords_geocoder' && ! GF_Geo.verify_geocoding ) {

	            		return GF_Geo.coords_geocoder_failed_allowed( location, status );

	            	} else {

                    	return ( failed != undefined && failed != false ) ? failed( status ) : GF_Geo.geocoder_failed( status );
                    }
                }
            });

            // set timer for geocoder
            timer_id = setTimeout( function() {
                
                timed_out = true;
                
                // do something else
            }, GF_Geo.geocoder_timeout );
        },

        /**
	     * Procced when map geocoding failed.
	     *
	     * We will provide coordinates only.
	     * 
	     * @param  {[type]} results [description]
	     * @return {[type]}         [description]
	     */
	    map_geocoder_failed_allowed : function( coords, status ) {

	    	results = {
	    		formatted_address  : '',
	    		address_components : '', 
	    	};
	    	GF_Geo.save_location_fields( results, 'map' );
	    },

	    /**
	     * Procced when coords geocoding failed.
	     *
	     * We will provide coordinates only.
	     * 
	     * @param  {[type]} results [description]
	     * @return {[type]}         [description]
	     */
	    coords_geocoder_failed_allowed : function( coords, status ) {

	    	alert( 'No address was found.' );

	    	results = {
	    		formatted_address  : '',
	    		address_components : '', 
	    	};

	    	GF_Geo.save_location_fields( results, 'coords_geocoder' );
	    },

        /**
         * Navigator default success message
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        navigator_success : function( results ) {
            GF_Geo.save_location_fields( results );
        },

        /**
         * Navigator default failed message
         * 
         * @param  {[type]} status [description]
         * @return {[type]}        [description]
         */
        navigator_failed : function( status ) {

            if ( GF_Geo.hide_error_messages || GF_Geo.processing.hide_error_message ) {
                console.log( status );
            } else {
                alert( status );
            }

            GF_Geo.processing_end();
        },

        /**
         * Get user's current position
         * 
         * @param  {function} success callback function when navigator success
         * @param  {function} failed  callback function when navigator failed
         * 
         * @return {[type]}                   [description]
         */
        navigator : function( element, success, failed ) {

            success = ( success == 'undefined' || success == false ) ? GF_Geo.navigator_success : success;
            failed  = ( failed  == 'undefined' || failed  == false ) ? GF_Geo.navigator_failed  : failed;

            // if navigator exists ( in browser ) try to locate the user
            if ( navigator.geolocation ) {
                
                GF_Geo.processing_start( element, [] );

                // show locator button loader
                jQuery( '.' + GF_Geo.prefix + 'locator-button.infield-locator' ).fadeOut( 'fast', function() {
                    jQuery( '.' + GF_Geo.prefix + 'locator-loader' ).fadeIn();
                });

                // run navigator
                navigator.geolocation.getCurrentPosition( show_position, show_error, { 
                    timeout : GF_Geo.navigator_timeout,
                    enableHighAccuracy : GF_Geo.navigator_high_accuracy
                } );
            
            // otherwise, try the IP locator or show an error message
            } else {

                if ( GF_Geo.ip_locator != false && GF_Geo.processing.ip_locator == 'fallback' ) {

                    //console.log( 'fallback ip locator' );
                    // run navigator
                    GF_Geo.ip_navigator( element, success, failed );

                } else {
                
                    return failed( GF_Geo.navigator_error_messages[5] );
                }
            }

            // geocode the coordinates if current position found
            function show_position( position ) {

                // set it to false since the geocoder will set it to true again.
                // Otherwise the geocoding will not proceed.
                GF_Geo.processing.status = false;

                // geocode the coords
                GF_Geo.geocoder( element, [ position.coords.latitude, position.coords.longitude ], success, failed );
            }

            // show error if failed navigator
            function show_error( error ) {

                if ( GF_Geo.ip_locator != false && GF_Geo.processing.ip_locator == 'fallback' ) {

                    console.log( 'fallback ip locator' );

                    // run navigator
                    GF_Geo.ip_navigator( element, success, failed );

                } else {
                    
                    // if request timed out
                    if ( error.code == 3 ) {

                        GF_Geo.geocoder_timed_out( GF_Geo.navigator_error_messages[error.code] );

                    // for any other error
                    } else {
                        failed( GF_Geo.navigator_error_messages[error.code] );
                    }
                }
            }
        },

        /**
         * IP navigator default success message
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        ip_navigator_success : function( results ) {
            GF_Geo.save_location_fields( results );
        },

        /**
         * IP navigator default failed message
         * 
         * @param  {[type]} status [description]
         * @return {[type]}        [description]
         */
        ip_navigator_failed : function( status ) {

            console.log( status );

            GF_Geo.processing_end();
        },

        /**
         * Get user's current position via IP address
         * 
         * @param  {function} success callback function when navigator success
         * @param  {function} failed  callback function when navigator failed
         * 
         * @return {[type]}                   [description]
         */
        ip_navigator : function( element, success, failed ) {

            success = ( success == 'undefined' || success == false ) ? GF_Geo.ip_navigator_success : success;
            failed  = ( failed  == 'undefined' || failed  == false ) ? GF_Geo.ip_navigator_failed  : failed;

            GF_Geo.processing_start( element, [] );

            // show locator button loader
            jQuery( '.' + GF_Geo.prefix + 'locator-button.infield-locator' ).fadeOut( 'fast', function() {
                jQuery( '.' + GF_Geo.prefix + 'locator-loader' ).fadeIn();
            });

            if ( GF_Geo.ip_locator == 'ipinfo' ) {

                ipToken = '';
                
                if ( GF_Geo.ip_token != '' ) {
                    
                    ipToken = '/?token=' + GF_Geo.ip_token;
                }

                jQuery.getJSON( GF_Geo.protocol + '://ipinfo.io' + ipToken, show_ip_position ).error( show_ip_error );

            } else if ( GF_Geo.ip_locator == 'maxmind' && typeof geoip2 !== 'undefined' ) {

                geoip2.insights( show_ip_position, show_ip_error );
            }
            
            function show_ip_position( position ) {
            	
                // set it to false since the geocoder will set it to true again.
                // Otherwise the geocoding will not proceed.
                GF_Geo.processing.status = false;

                console.log( position );
                // if using ipinfo we need to split the coordinates
                if ( GF_Geo.ip_locator == 'ipinfo' ) { 

                    latlng = position.loc.split( ',' ); 

                    ipLat = latlng[0];
                    ipLng = latlng[1];

                } else {

                    ipLat = position.location.latitude;
                    ipLng = position.location.longitude;
                }

                // geocode the coords
                GF_Geo.geocoder( element, [ ipLat, ipLng ], success, failed );
            }

            function show_ip_error( error ) {
                
                failed( "Error:\n\n" + JSON.stringify( error, undefined, 4 ) );
            }
            
        },

        /**
         * Page locator success callback function
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        page_locator_success : function( results ) {

            // alert success message
            if ( GF_Geo.processing.found_message != '' && GF_Geo.processing.found_message != undefined ) {
              alert( GF_Geo.processing.found_message );
            }
            
            // save location fields         
            GF_Geo.save_location_fields( results, 'page_locator' ); 
        },

        /**
         * Locator button success callback function
         * @param  {[type]} address_fields [description]
         * @param  {[type]} results        [description]
         * @return {[type]}                [description]
         */
        locator_button_success : function( results ) {

            if ( GF_Geo.processing.found_message != '' && GF_Geo.processing.found_message != undefined ) {
              alert( GF_Geo.processing.found_message );
            }
            
            // save location fields     
            GF_Geo.save_location_fields( results, 'locator_button' );           
        },

        /**
         * address geocoder success callback function
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        address_geocoder_success : function( results ) {
            GF_Geo.save_location_fields( results, 'address_geocoder' );
        },

        /**
         * Initiate address fields
         * 
         * @return {[type]} [description]
         */
        address_field_init : function( address_field ) {
                    
            address_changed = false;

            // mark field value has changed
            address_field.on( 'input', function() {
                address_changed = true;
            });

            // update address if user click enter in the address field and autocomplete
            // is not showing suggested results. That is a workaround to allow user
            // to geocode custom address which is not from the suggested results
            address_field.on( gfgeo_options.address_field_event_triggers, function( event ) {

                // check if entered key pressed or left the field
                if ( address_changed && ( ( event.type == 'keydown' && event.which == 13 ) || event.type == 'focusout' || event.type == 'mouseleave' ) ) {

                    // Prevent Forms sumission on enter key press.
                    if ( event.type == 'keydown' && event.which == 13 ) {
                    	event.preventDefault();
                    }

                    var thisTarget = jQuery( this );

                    // adding a short delay the allow the address autocomplete, if enabled, to
                    // process geocoding. Thats is to prevent the address geocoder from
                    // executing right after the address autocomplete geocoder.
                    setTimeout( function() {
 
                        // if autocomplete was triggered on the field then prevent it from
                        // address geocoding and reset the autocomplete trigger
                        if ( GF_Geo.autocomplete_triggered == true ) {

                            GF_Geo.autocomplete_triggered = false; 

                            address_changed = false; 

                        // otherwise, do address geocoding
                        } else {

                            // set field status to prevent infinite loop
                            address_changed = false;

                            // get geocoder ID
                            GF_Geo.geocoder_id = thisTarget.data( 'geocoder_id' );

                            var entered_address = address_field.val();

                            // if address is not empty geocode it
                            if(  jQuery.trim( entered_address ).length != 0 ) {

                                address_changed = false;

                                // geocode the address
                                GF_Geo.geocoder( 'address_geocoder', entered_address, GF_Geo.address_geocoder_success, false );
                            
                            // otherwise, clear geocoded fields
                            } else {

                                address_changed = false;

                                GF_Geo.clear_fields( GF_Geo.geocoder_id, 'address_geocoder' );
                            }
                        }
                        
                    }, 700 );      
                }
            });
        },

        /**
         * Initiate address fields
         * 
         * @return {[type]} [description]
         */
        advanced_address_field_init : function( address_wrapper ) {
            
            // dynamically place the autocomplete field at the top of the address field
            if ( address_wrapper.find( '.address_autocomplete' ).length != 0 ) {

                autocomplete = address_wrapper.find( '.address_autocomplete' );
                container    = autocomplete.closest( 'li' ).find( 'div.ginput_container_address' );
                tabindex     = address_wrapper.find( 'span input' ).first().attr( 'tabindex' );

                autocomplete.find( 'input' ).attr( 'tabindex', tabindex );
                autocomplete.detach().prependTo( container ).show();

                // prevent form submission on enter press
                autocomplete.find( 'input' ).on( 'keydown', function( event ) {

                    if ( event.which == 13 ) {
                        event.preventDefault();
                    }
                });
            }
            
            address_changed = false;

            // clear geocoded fields when input address changes
            address_wrapper.find( 'span input' ).on( 'input', function() {
                address_changed = true;
            });

            address_wrapper.find( 'span select' ).on( 'click', function() {
                address_changed = true;
            });
            
            address_wrapper.on( 'mouseleave', function( event ) {

            	setTimeout( function() { 
            		
                    // verify that focus in not in one of the address field
                    // before trying to geocode the address
                    if ( address_changed ) {

		                // fields to collect address from
		                var addressFields = address_wrapper.find( 'span input, span select' );

                        entered_address = '';

                        // get the address field into single address fields
                        addressFields.each( function() {
                            entered_address = entered_address + ' ' + jQuery( this ).val(); 
                        });

                        // geocoder ID
                        GF_Geo.geocoder_id = address_wrapper.find( '.gfgeo-advanced-address-geocoder-id' ).data( 'geocoder_id' );
                      
                        // if address is not empty geocode it
                        if (  jQuery.trim( entered_address ).length != 0 ) {
                            
                            address_changed = false;

                            // geocode the address
                            GF_Geo.geocoder( 'advanced_address_geocoder', entered_address, GF_Geo.advanced_address_geocoder_success, false );
                        
                        // otherwise, clear geocoded fields
                        } else {

                            address_changed = false;
 
                            GF_Geo.clear_fields( GF_Geo.geocoder_id, 'advanced_address_geocoder' );
                        }
                    }
                }, 300 );
            });

            // update address if user click enter in the address field and autocomplete
            // is not showing suggested results. That is a workaround to allow user
            // to geocode custom address which is not from the suggested results
            address_wrapper.find( 'span input, span select' ).on( 'keydown focusout', function( event ) {

                // check if entered key pressed or left the field
                if ( event.type == 'keydown' && event.which == 13 ) {

                    // prevent form submission on enter press
                    event.preventDefault();

                    address_changed = false;

                    entered_address = '';

                    // get the different address field into single address field
                    address_wrapper.find( 'span input, span select' ).each( function() {
                        entered_address = entered_address + ' ' + jQuery( this ).val(); 
                    });

                    // geocoder ID
                    GF_Geo.geocoder_id = address_wrapper.find( '.gfgeo-advanced-address-geocoder-id' ).data( 'geocoder_id' );
        
                    // if address is not empty geocode it
                    if (  jQuery.trim( entered_address ).length != 0 ) {
                        
                        // geocode the address
                        GF_Geo.geocoder( 'advanced_address_geocoder', entered_address, GF_Geo.advanced_address_geocoder_success, false );
                    
                    // otherwise, clear geocoded fields
                    } else {
                 
                        GF_Geo.clear_fields( GF_Geo.geocoder_id, 'advanced_address_geocoder' );
                    }
                }

                if ( event.type == 'focusout' && address_changed ) {

                    // get the wrapper field
                    address_wrapper = jQuery( this ).closest( 'li.gfield' );

                    // fields to collect address from
                    focusFields = address_wrapper.find( 'span input, span select' );
        
                    // set timer to allow the check if fields are not in focus
                    setTimeout( function() { 
            
                        // verify that focus in not in one of the address field
                        // before trying to geocode the address
                        if ( ! focusFields.is( ':focus' ) ) {

                            entered_address = '';

                            // get the address field into single address fields
                            focusFields.each( function() {
                                entered_address = entered_address + ' ' + jQuery( this ).val(); 
                            });

                            // geocoder ID
                            GF_Geo.geocoder_id = address_wrapper.find( '.gfgeo-advanced-address-geocoder-id' ).data( 'geocoder_id' );
                          
                            // if address is not empty geocode it
                            if (  jQuery.trim( entered_address ).length != 0 ) {
                                
                                address_changed = false;

                                // geocode the address
                                GF_Geo.geocoder( 'advanced_address_geocoder', entered_address, GF_Geo.advanced_address_geocoder_success, false );
                            
                            // otherwise, clear geocoded fields
                            } else {

                                address_changed = false;
     
                                GF_Geo.clear_fields( GF_Geo.geocoder_id, 'advanced_address_geocoder' );
                            }
                        }
                    }, 300 );
                }
            });
        },

        /**
         * Ma
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        advanced_address_geocoder_success : function( results ) {
            GF_Geo.save_location_fields( results, 'advanced_address_geocoder' );
        },

        /**
         * Initiate coords fields
         * 
         * @return {[type]} [description]
         */
        coordinates_field_init : function( coords_wrapper ) {

            coords_changed = false;

            coords_input = coords_wrapper.find( 'input' );

            // clear geocoded fields when input address changes
            coords_input.on( 'input', function() {

                coords_changed = true;
            });

            // Reverse geocoding coordinates if key press entered in coords fields or left the fields.
            coords_input.on( 'keydown focusout', function( event ) {

                // geocode coords on enter press
                if ( event.type == 'keydown' && event.which == 13 ) {

                    coords_changed = false;

                    // prevent form submission on enter key
                    event.preventDefault();
                    
                    // run geocoder
                    GF_Geo.coordinates_trigger( coords_wrapper, 'enter_key' );

                // geocode coords when leaving the field
                } 
                
                if ( event.type == 'focusout' && coords_changed ) {

                    // allow time to check for fields focus
                    setTimeout( function() { 
                        
                        // check that not withing coords field before geocoding
                        if ( ! coords_wrapper.find( 'input' ).is( ':focus' ) ) {

                            coords_changed = false;

                            // run geocoder
                            GF_Geo.coordinates_trigger( coords_wrapper, 'focusout' );
                        }
                    }, 300 );
                }
            });
        },

        /**
         * Trigger coordinates geocoder
         * @param  {[type]} coords_field [description]
         * @return {[type]}              [description]
         */
        coordinates_trigger : function( coords_wrapper, action_type ) {

            // Geocoder ID
            GF_Geo.geocoder_id = coords_wrapper.data( 'geocoder_id' );

            this_lat = coords_wrapper.find( '.gfgeo-latitude-field' ).val().replace( / /g,'' );
            this_lng = coords_wrapper.find( '.gfgeo-longitude-field' ).val().replace( / /g,'' );
            
            // if press enter and any of the coords field is empty prevent it from geocoding
            if ( this_lat == '' || this_lng == '' ) {

                // if press enter and any of the coords field is empty prevent it from geocoding
                if ( this_lat == '' && this_lng == '' ) {

                    GF_Geo.clear_fields( GF_Geo.geocoder_id, 'coordinates_geocoder' );
                
                } else {

                    // alert the user only when trying to geocode by enter key
                    if ( action_type == 'enter_key' ) {
                        alert( 'You must enter both latitude and longitude.' );
                    }
                }

            // geocode the coords if changed
            } else {
             
                GF_Geo.geocoder( 'coords_geocoder', [ this_lat, this_lng ], GF_Geo.coords_geocoder_success, false );
            }
        },

        /**
         * coords geocoder success callback function
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        coords_geocoder_success : function( results ) {
            GF_Geo.save_location_fields( results, 'coords_geocoder' );
        },

        /**
         * Default coords geocoder success callback function
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        default_coords_geocoder_success : function( results ) {
            GF_Geo.save_location_fields( results, 'default_coords_geocoder' );
        },

        /**
         * Render maps on page load
         * 
         * @return {[type]} [description]
         */
        render_map : function( map_data, field_values ) {

            // init map args object
            GF_Geo.maps[map_data.map_id] = {};

            // map args
            GF_Geo.maps[map_data.map_id].args = map_data;

            check_lat = jQuery( '#gfgeo-geocoded-hidden-fields-wrapper-' + map_data.geocoder_id + ' input.latitude' ).val();
            check_lng = jQuery( '#gfgeo-geocoded-hidden-fields-wrapper-' + map_data.geocoder_id + ' input.longitude' ).val();

            // get coords from geocoder if present in the page
            if ( jQuery.trim( check_lat ).length != 0 && jQuery.trim( check_lng ).length != 0 ) {
                map_data.latitude  = check_lat;
                map_data.longitude = check_lng;
            } 

            // get initial marker position
            GF_Geo.maps[map_data.map_id].latlng = new google.maps.LatLng( map_data.latitude, map_data.longitude );
            
            // map options
            GF_Geo.maps[map_data.map_id].options = {
                zoom            : parseInt( map_data.zoom_level),
                center          : GF_Geo.maps[map_data.map_id].latlng,
                mapTypeId       : google.maps.MapTypeId[map_data.map_type],
                backgroundColor : '#f1f1f1',
                scrollwheel     : map_data.scrollwheel,
                styles          : typeof field_values.gfgeo_map_styles !== 'undefined' ? jQuery.parseJSON( field_values.gfgeo_map_styles ) : ''
            };
        
            // generate the map
            GF_Geo.maps[map_data.map_id].map = new google.maps.Map( document.getElementById( GF_Geo.prefix + 'map-' + map_data.map_id ), GF_Geo.maps[map_data.map_id].options );
            
            // generate marker
            GF_Geo.maps[map_data.map_id].marker = new google.maps.Marker({
                position  : GF_Geo.maps[map_data.map_id].latlng,
                map       : GF_Geo.maps[map_data.map_id].map,
                draggable : map_data.draggable,
                icon      : map_data.map_marker
            });
            
            // when dragging the marker on the map
            google.maps.event.addListener( GF_Geo.maps[map_data.map_id].marker, 'dragend', function( event ){
                
                // set the global Geocoder ID
                GF_Geo.geocoder_id = map_data.geocoder_id;

                GF_Geo.processing.element_id = map_data.map_id;

                // geocode coords to get address fields
                GF_Geo.geocoder( 'map', [ event.latLng.lat(), event.latLng.lng() ], GF_Geo.map_geocoder_success, false );  
            });  

            if ( map_data.drag_on_click == true ) {

                GF_Geo.map_single_click = false;

                google.maps.event.addListener( GF_Geo.maps[map_data.map_id].map, 'click', function( event ) {
                    
                    GF_Geo.map_single_click = true;
            
                    setTimeout( function() {

                        if ( GF_Geo.map_single_click ) { 

                            GF_Geo.maps[map_data.map_id].marker.setPosition( event.latLng );

                            GF_Geo.processing.element_id = map_data.map_id;

                            // set the global Geocoder ID
                            GF_Geo.geocoder_id = map_data.geocoder_id;

                            // geocode coords to get address fields
                            GF_Geo.geocoder( 'map', [ event.latLng.lat(), event.latLng.lng() ], GF_Geo.map_geocoder_success, false );
                        }

                    }, 200);
                });

                google.maps.event.addListener( GF_Geo.maps[map_data.map_id].map, 'dblclick', function( event ) {
                     GF_Geo.map_single_click = false;
                });
            }

            // hook custom functions if needed
            jQuery( document ).trigger( 
                'gfgeo_render_map', 
                [ GF_Geo, map_data ] 
            );
        },

        /**
         * update map with new location
         * 
         * @param  {[type]} map_id [description]
         * @param  {[type]} lat    [description]
         * @param  {[type]} lng    [description]
         * 
         * @return {[type]}        [description]
         */
        update_map : function( map_id, lat, lng ) {
            
            // check that map exists on the form
            if ( ! jQuery( '#' + GF_Geo.prefix + 'map-' + map_id ).length ) {
                return;
            }

            // get coords of new position
            GF_Geo.maps[map_id].latLng = new google.maps.LatLng( lat, lng );

            // set new position
            GF_Geo.maps[map_id].marker.setPosition( GF_Geo.maps[map_id].latLng );

            // pan map into new position
            GF_Geo.maps[map_id].map.panTo( GF_Geo.maps[map_id].latLng );

            // set marker draggable to false. Marker draggable automatically set to
            // true after it dynamically being dragged. We also need a short delay
            // in order for the setdraggbale to take effect.
            setTimeout( function() {

                GF_Geo.maps[map_id].marker.setDraggable( GF_Geo.maps[map_id].args.draggable );
            
            } ,800 );

            // do something custom when map updates
            jQuery( document ).trigger( 
                'gfgeo_update_map', 
                [ GF_Geo.maps[map_id], GF_Geo, lat, lng ] 
            );
        },

        /**
         * map geocoder success callback function
         * 
         * @param  {[type]} results [description]
         * @return {[type]}         [description]
         */
        map_geocoder_success : function( results ) {

            // save location data
            GF_Geo.save_location_fields( results, 'map' );
        },

        /**
         * Resize map once show via conditional logic 
         *
         * Map won't show properly when initial within a hidden field
         *
         * And so we need to trigger "resize" once the map showing using 
         *
         * conditional logic.
         * 
         * @return {[type]} [description]
         */
        resize_map_on_show : function() {

            if ( typeof gform !== 'undefined' ) {

                gform.addAction( 'gform_post_conditional_logic_field_action', function ( formId, action, targetId, defaultValues, isInit ) {
                    
                    // only if logic trigger set to show
                    if ( ! isInit && action == 'show' ) {

                        map_id = targetId.replace( '#field_', '' );

                        if ( jQuery( '#gfgeo-map-' + map_id ).length != 0 && typeof GF_Geo.maps[map_id] != 'undefined' ) {

                            // resize map
                            google.maps.event.trigger( GF_Geo.maps[map_id].map, 'resize' );

                            // center marker
                            GF_Geo.maps[map_id].map.panTo( GF_Geo.maps[map_id].marker.position );
                        }
                    }
                });
            }
        },

        /**
         * Google places address autocomplete
         * 
         * @return void
         */
        address_autocomplete : function( field_id , success ) {
            
            // field object
            var thisField = jQuery( '#' + field_id );

            if ( gfgeo_options.field_autocomplete == '0' ) {

	            thisField.on( 'focusin', function() {
	            	jQuery( this ).attr( 'autocomplete', 'stop' );
	            });
	        }
            
            // prevent form submission on address input fields
            thisField.on( 'keydown', function( event ) {

                if ( event.which == 13 ) {
                    event.preventDefault();
                }
            });

            var options = {};

            // restricted country
            if ( thisField.data( 'autocomplete_country' ) != '' && thisField.data( 'autocomplete_country' ) != undefined ) {
                options.componentRestrictions = { 
                    country : thisField.data( 'autocomplete_country' ).split( ',' )
                };
            }

            // restricted type
            if ( thisField.data( 'autocomplete_types' ) != '' && thisField.data( 'autocomplete_types' ) != undefined ) {
                options.types = [thisField.data( 'autocomplete_types' )];
            }

            // restricted bounds
            if ( thisField.data( 'autocomplete_bounds' ) != '' && thisField.data( 'autocomplete_bounds' ) != undefined ) {
                
                var defaultBounds = new google.maps.LatLngBounds();

                var bounds = thisField.data( 'autocomplete_bounds' ).replace( ' ', '' ).split( '|' );
   
                jQuery.each( bounds, function( index, value ) {

                    var bound = [];
                    
                    bound = value.split( ',' );
     
                    if ( bound[0] != undefined && bound[1] != undefined ) {
             
                        var lat = parseFloat( bound[0] );
                        var lng = parseFloat( bound[1] );
            
                        defaultBounds.extend( new google.maps.LatLng( lat, lng ) );
                    }         
                });
                
                options.bounds = defaultBounds;
            }

            var input = document.getElementById( field_id );

            // init autocomplete
            GF_Geo.autocompletes[field_id] = new google.maps.places.Autocomplete( input, options );
            
            // on place change
            google.maps.event.addListener( GF_Geo.autocompletes[field_id], 'place_changed', function(e) {
                
                // get the geocoder ID
                GF_Geo.geocoder_id = jQuery( input ).data( 'geocoder_id' );

                // get place data
                var place = GF_Geo.autocompletes[field_id].getPlace();
                
                if ( ! place.geometry ) {
                    return;
                }

                if ( jQuery( input ).data( 'geocoder_id' ) == '' ) {
                    return;
                }

                // set to true to prevent address geocoding conflict on this field
                GF_Geo.autocomplete_triggered = true;

                // start process
                GF_Geo.processing_start( 
                    'address_autocomplete', 
                    [place.geometry.location.lat(), 
                    place.geometry.location.lng()] 
                );

                if ( jQuery( input ).hasClass( 'advanced-address-autocomplete' ) ) {
                    GF_Geo.processing.advanced_address = true;
                } else {
                    GF_Geo.processing.advanced_address = false;
                }

                // pass the entered address
                GF_Geo.processing.selected_address = jQuery( input ).val();

                // get place name from autocomplete if eists
                if ( typeof GF_Geo.autocompletes[field_id].gm_accessors_.place.Td != 'undefined' ) {
                    GF_Geo.processing.autocomplete_place = GF_Geo.autocompletes[field_id].gm_accessors_.place.Td.formattedPrediction;
                }
                
                // dynamically trigger change event when choice was selected
                jQuery( input ).trigger( 'change' );
                
                // save location fields 
                GF_Geo.save_location_fields( place, 'address_autocomplete' );
            });   
        },

        /**
         * Calculate distance between 2 geocoders.
         *
         * @param  {[type]} origin_geocoder_id      [description]
         * @param  {[type]} destination_geocoder_id [description]
         * @return {[type]}                         [description]
         */
        /*get_driving_distance : function( origin_geocoder_id, destination_geocoder_id ) {

        	setTimeout( function() {
        	
	        	if ( jQuery( '#input_' + origin_geocoder_id + '_status' ).val() == '1' && jQuery( '#input_' + destination_geocoder_id + '_status' ).val() == '1' ) {

	        		var lat, lng, origin, orgAddress, destination, destAddress, formID = GF_Geo.form.id;

	        		lat         = jQuery( '#input_' + origin_geocoder_id + '_latitude' ).val();
					lng         = jQuery( '#input_' + origin_geocoder_id + '_longitude' ).val();
					origin      = new google.maps.LatLng( lat, lng );
					orgAddress  = jQuery( '#input_' + origin_geocoder_id + '_address' ).val();

					lat    		= jQuery( '#input_' + destination_geocoder_id + '_latitude' ).val();
					lng    		= jQuery( '#input_' + destination_geocoder_id + '_longitude' ).val();
					destination = new google.maps.LatLng( lat, lng );
					destAddress = jQuery( '#input_' + destination_geocoder_id + '_address' ).val();

					var fieldData = jQuery( '#gfgeo-geocoded-hidden-fields-wrapper-' + origin_geocoder_id ).data();
					var units     = fieldData.unit_system == 'metric' ? google.maps.UnitSystem.METRIC : google.maps.UnitSystem.IMPERIAL;
					var options   = {
					    origins: [origin],
					    destinations: [destination],
					    travelMode: fieldData.travel_mode,
					    unitSystem: units ,
					    //transitOptions: TransitOptions,
					    //drivingOptions: DrivingOptions,
					    //avoidHighways: Boolean,
					    //avoidTolls: Boolean,
					};

					if ( typeof gform !== 'undefined' ) {
						options = gform.applyFilters( 'gfgeo_distance_options', options, origin_geocoder_id, destination_geocoder_id, GF_Geo );
					}

					var service = new google.maps.DistanceMatrixService();

					// Get distance.
					service.getDistanceMatrix( options, function( response, status ) {

		                // on success
		                if ( status == 'OK' ) {

		                	var responseStatus = response.rows[0].elements[0].status;

		                	if ( responseStatus == 'OK' ) {

			                	// do something custom when map updates
						        jQuery( document ).trigger( 
						            'gfgeo_distance_calculation_success', 
						            [ response, GF_Geo, origin_geocoder_id, destination_geocoder_id ] 
						        );

						        jQuery( document ).trigger( 
						            'gfgeo_distance_calculation_success', 
						            [ response, GF_Geo, origin_geocoder_id, destination_geocoder_id ] 
						        );

								GF_Geo.generate_distance_values( origin_geocoder_id, response.rows[0].elements[0].distance, response.rows[0].elements[0].duration );
			        		
			        		} else {

			        			if ( responseStatus == "ZERO_RESULTS" ) {
			        				alert( 'No route could be found between ' + orgAddress + ' and ' + destAddress + '.' );
			        			}
			        		}

		                // on failed      
		                } else {

		                	// do something custom when map updates
					        jQuery( document ).trigger( 
					            'gfgeo_distance_calculation_failed', 
					            [ GF_Geo ] 
					        );
		                }
		            });
				}
			}, 500 );
        }, */

        /**
         * Calculate the distance and display routes between 2 geocoders.
         *
         * @param  {[type]} origin_geocoder_id      [description]
         * @param  {[type]} destination_geocoder_id [description]
         * @return {[type]}                         [description]
         */
        get_driving_directions : function( origin_geocoder_id, destination_geocoder_id ) {

        	setTimeout( function() {
        	
	        	if ( jQuery( '#input_' + origin_geocoder_id + '_status' ).val() == '1' && jQuery( '#input_' + destination_geocoder_id + '_status' ).val() == '1' ) {


	        		var lat, lng, origin, orgAddress, destination, destAddress, formID = GF_Geo.form.id, fieldData, units, mapEnabled, panelEnabled;

	        		// Collect some variables.
	        		lat        = jQuery( '#input_' + origin_geocoder_id + '_latitude' ).val();
					lng        = jQuery( '#input_' + origin_geocoder_id + '_longitude' ).val();
					origin     = new google.maps.LatLng( lat, lng );
					orgAddress = jQuery( '#input_' + origin_geocoder_id + '_address' ).val();

					lat    		= jQuery( '#input_' + destination_geocoder_id + '_latitude' ).val();
					lng    		= jQuery( '#input_' + destination_geocoder_id + '_longitude' ).val();
					destination = new google.maps.LatLng( lat, lng );
					destAddress = jQuery( '#input_' + destination_geocoder_id + '_address' ).val();

					fieldData        = jQuery( '#gfgeo-geocoded-hidden-fields-wrapper-' + origin_geocoder_id ).data();
					units            = fieldData.unit_system == 'metric' ? google.maps.UnitSystem.METRIC : google.maps.UnitSystem.IMPERIAL;
					route_map_id     = fieldData.route_map_id;
					route_on_map     = fieldData.show_route_on_map;
					directions_panel = fieldData.directions_panel_id;
					mapEnabled       = false;
					PanelEnabbled    = false;
					
					//alert(origin_geocoder_id)
					//alert(destination_geocoder_id)
					//alert(route_map_id)
					// Directions options.
					var options = {
					    origin: origin,
					    destination: destination,
					    travelMode: fieldData.travel_mode,
					    unitSystem: units ,
					    //transitOptions: TransitOptions,
					    //drivingOptions: DrivingOptions,
					    //avoidHighways: Boolean,
					    //avoidTolls: Boolean,
					};

					if ( typeof gform !== 'undefined' ) {
						options = gform.applyFilters( 'gfgeo_driving_directions_options', options, origin_geocoder_id, destination_geocoder_id, fieldData, GF_Geo );
					}

					// Init directions services.
					var directionsService = new google.maps.DirectionsService();
  					
  					// Generate route and or directions panel.
  					if ( route_map_id != '' || directions_panel != '' ) {
							
						// Init service.
						GF_Geo.directions_display[ origin_geocoder_id ] = new google.maps.DirectionsRenderer( { suppressMarkers: true } );
						
						// Check that map exists before trying to display route.
						if ( route_map_id != '' && jQuery( '#gfgeo-map-' + route_map_id ).length ) {
							
							mapEnabled = true;

							// Clear map first.
							GF_Geo.directions_display[ origin_geocoder_id ].setMap( null );

							if ( typeof GF_Geo.destinationMarkers[ origin_geocoder_id ] !== 'undefined' ) {
								GF_Geo.destinationMarkers[ origin_geocoder_id ].setMap( null );
							}

							// Generate route.
							GF_Geo.directions_display[ origin_geocoder_id ].setMap( GF_Geo.maps[ route_map_id ].map );
						}

						// set direction panel
						if ( directions_panel != '' ) {

							panelEnabled = true;

							// Panel ID.
							var dpId = 'gfgeo-directions-panel-holder-' + directions_panel;

							//jQuery( '#' + dpId ).html( '' );

							GF_Geo.directions_display[ origin_geocoder_id ].setPanel( null );
							GF_Geo.directions_display[ origin_geocoder_id ].setPanel( document.getElementById( dpId ) );

							jQuery( '#' + dpId ).closest( 'li' ).slideDown();
						}
					}  					

					// Calculate route.
					directionsService.route( options, function( response, status ) {
						
						if ( status == 'OK' ) {
							
							if ( mapEnabled == true || panelEnabled == true ) {

								GF_Geo.directions_display[ origin_geocoder_id ].setDirections( response );

								if ( mapEnabled == true ) {

									// Generate end location marker.
									GF_Geo.destinationMarkers[ origin_geocoder_id ] = new google.maps.Marker({
									  	position : response.routes[0].legs[0].end_location,
									  	icon     : 'data:image/svg+xml,%3Csvg%20version%3D%221.1%22%20width%3D%2227px%22%20height%3D%2243px%22%20viewBox%3D%220%200%2027%2043%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%0A%3Cdefs%3E%0A%3Cpath%20id%3D%22a%22%20d%3D%22m12.5%200c-6.9039%200-12.5%205.5961-12.5%2012.5%200%201.8859%200.54297%203.7461%201.4414%205.4617%203.425%206.6156%2010.216%2013.566%2010.216%2022.195%200%200.46562%200.37734%200.84297%200.84297%200.84297s0.84297-0.37734%200.84297-0.84297c0-8.6289%206.7906-15.58%2010.216-22.195%200.89844-1.7156%201.4414-3.5758%201.4414-5.4617%200-6.9039-5.5961-12.5-12.5-12.5z%22%2F%3E%0A%3C%2Fdefs%3E%0A%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%0A%3Cg%20transform%3D%22translate(1%201)%22%3E%0A%3Cuse%20fill%3D%22%23EA4335%22%20fill-rule%3D%22evenodd%22%20xlink%3Ahref%3D%22%23a%22%2F%3E%0A%3Cpath%20d%3D%22m12.5-0.5c7.18%200%2013%205.82%2013%2013%200%201.8995-0.52398%203.8328-1.4974%205.6916-0.91575%201.7688-1.0177%201.9307-4.169%206.7789-4.2579%206.5508-5.9907%2010.447-5.9907%2015.187%200%200.74177-0.6012%201.343-1.343%201.343s-1.343-0.6012-1.343-1.343c0-4.7396-1.7327-8.6358-5.9907-15.187-3.1512-4.8482-3.2532-5.01-4.1679-6.7768-0.97449-1.8608-1.4985-3.7942-1.4985-5.6937%200-7.18%205.82-13%2013-13z%22%20stroke%3D%22%23fff%22%2F%3E%0A%3C%2Fg%3E%0A%3Ctext%20text-anchor%3D%22middle%22%20dy%3D%220.3em%22%20x%3D%2214%22%20y%3D%2215%22%20font-family%3D%22Roboto%2C%20Arial%2C%20sans-serif%22%20font-size%3D%2216px%22%20fill%3D%22%23FFF%22%3EB%3C%2Ftext%3E%0A%3C%2Fg%3E%0A%3C%2Fsvg%3E%0A'
									});

									// Workaround. When multiple directions work at the same time
									// The route map id is incorrect for some reason.
									fieldData = jQuery( '#gfgeo-geocoded-hidden-fields-wrapper-' + origin_geocoder_id ).data();
									route_map_id = fieldData.route_map_id;
			
									GF_Geo.destinationMarkers[ origin_geocoder_id ].setMap( GF_Geo.maps[ route_map_id ].map );
								}
							}

							jQuery( document ).trigger( 
					            'gfgeo_distance_calculation_success', 
					            [ response, GF_Geo, origin_geocoder_id, destination_geocoder_id ] 
					        );

							GF_Geo.generate_distance_values( origin_geocoder_id, response.routes[0].legs[0].distance, response.routes[0].legs[0].duration );
						
						} else {

							if ( status == "ZERO_RESULTS" ) {
		        				alert( 'No route could be found between ' + orgAddress + ' and ' + destAddress + '.' );
		        			}

		        			jQuery( document ).trigger( 
					            'gfgeo_distance_calculation_failed', 
					            [ GF_Geo ] 
					        );
						}
					});
				}
			}, 1000 );
        },

        generate_distance_values : function( geocoder_id, distance, duration ) {

			// update distance in dynamic fields.
			GF_Geo.generate_location_data( geocoder_id, 'distance_text', distance.text ); 
			GF_Geo.generate_location_data( geocoder_id, 'distance_value', distance.text.replace( /[^0-9.]/g, '' ) );              
			GF_Geo.generate_location_data( geocoder_id, 'duration_text', duration.text );
			GF_Geo.generate_location_data( geocoder_id, 'duration_value', duration.value );
        },

        /*get_distance : function( coords, geocoders_id ) {

        	if ( geocoders_id.length != 2 ) {
        		return;
        	}

        	var lat, lng, origin, destination, formID = GF_Geo.form.id;

        	if ( jQuery( '#input_' + formID + '_' + geocoders_id[0] + '_status' ).val() == '1' && jQuery( '#input_' + formID + '_' + geocoders_id[1] + '_status' ).val() == '1' ) {

				lat    = jQuery( '#input_' + formID + '_' + geocoders_id[0] + '_latitude' ).val();
				lng    = jQuery( '#input_' + formID + '_' + geocoders_id[0] + '_longitude' ).val();
				origin = new google.maps.LatLng( lat, lng );

				lat    		= jQuery( '#input_' + formID + '_' + geocoders_id[1] + '_latitude' ).val();
				lng    		= jQuery( '#input_' + formID + '_' + geocoders_id[1] + '_longitude' ).val();
				destination = new google.maps.LatLng( lat, lng );

				var service = new google.maps.DistanceMatrixService();
				
				service.getDistanceMatrix( {
				    origins: [origin],
				    destinations: [destination],
				    travelMode: 'DRIVING',
				    unitSystem: google.maps.UnitSystem.METRIC,
				    //transitOptions: TransitOptions,
				    //drivingOptions: DrivingOptions,
				    //avoidHighways: Boolean,
				    //avoidTolls: Boolean,
				  }, callback );

				function callback( response, status ) {

					if (status == 'OK') {
						console.log( response.rows[0].elements );
						console.log( response );
					}
				  // See Parsing the Results for
				  // the basics of a callback function.
				}
			}
        },*/

        /*get_directions : function( geocoders_id ) {

        	var formID 			  = GF_Geo.form.id;
			var directionsService = new google.maps.DirectionsService;
			var directionsDisplay = new google.maps.DirectionsRenderer;
			var arrayLength       = geocoders_id.length;
			var waypoints         = [];
			var origin, destination;

			for ( var i = 0; i < arrayLength; i++ ) {
				
				var geoId  = geocoders_id[i];
				var lat    = jQuery( '#input_' + formID + '_' + geoId + '_latitude' ).val();
				var lng    = jQuery( '#input_' + formID + '_' + geoId + '_longitude' ).val();
				var coords = new google.maps.LatLng( lat, lng );

				if ( i == 0 ) {

					console.log( 'first' )
					console.log( geoId )
					console.log( lat )
					console.log( lng )

					origin = coords;

				} else if ( i == arrayLength - 1 ) {

					console.log( 'last' )
					console.log( geoId )
					console.log( lat )
					console.log( lng )

					destination = coords;
				} else {

					waypoints.push({
						location: coords,
						stopover: false
					});

					console.log( 'way' )

					console.log( geoId )
					console.log( lat )
					console.log( lng )

					
				}
			}

			console.log( origin )
			console.log( destination )
			console.log( waypoints )
			directionsService.route({

				origin: origin,
				destination: destination,
				waypoints: waypoints,
				optimizeWaypoints: true,
				travelMode: 'DRIVING',

			}, function( response, status ) {

				if ( status === 'OK' ) {

					console.log( response.routes[0].legs )
					directionsDisplay.setDirections(response);
					
					var route = response.routes[0];
					var summaryPanel = document.getElementById('directions-panel');

					summaryPanel.innerHTML = '';

					// For each route, display summary information.
					for (var i = 0; i < route.legs.length; i++) {
						var routeSegment = i + 1;
						summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
						    '</b><br>';
						summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
						summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
						summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
					}

				} else {

				  	window.alert('Directions request failed due to ' + status);
				}
			});
        }, */

        /**
         * Dynamically populate address field with geocoded data
         *  
         * @param  {[type]} geocoder_id   [description]
         * @param  {[type]} address_field [description]
         * @param  {[type]} address_value [description]
         * @return {[type]}               [description]
         */
        generate_location_data : function ( geocoder_id, address_field, address_value ) {

            // locator field
            jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + address_field + '[type="hidden"]' ).val( address_value ).trigger( 'change' );
            
            /**** Save dynamic fields. The marked out script can be used in other plugins *****/
            
            /*** Text Fields ****/

            //jQuery( 'input[data-geocoder_id="' + geocoder_id + '"][data-output_location_field="' + address_field + '"][type="text"]' ).val( address_value ).trigger( 'change' );
            //jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + address_field + '[type="text"]' ).val( address_value ).trigger( 'change' );
            jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + GF_Geo.prefix + address_field ).find( 'input[type="text"]' ).val( address_value ).trigger( 'change' );
            
            /*** hidden Fields ****/

            //jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + address_field + '[type="hidden"]' ).val( address_value ).trigger( 'change' );
            //jQuery( 'input[data-geocoder_id="' + geocoder_id + '"][data-output_location_field="' + address_field + '"][type="hidden"]' ).val( address_value ).trigger( 'change' );
            jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + GF_Geo.prefix + address_field ).find( 'input[type="hidden"]' ).val( address_value ).trigger( 'change' );
            
            /*** Select Fields ****/

            //jQuery( 'select.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + address_field + ' option[value="' + address_value + '"]' ).attr( 'selected','selected' ).trigger( 'change' );
            //jQuery( 'select[data-geocoder_id="' + geocoder_id + '"][data-output_location_field="' + address_field + '"] option[value="' + address_value + '"]' ).attr( 'selected','selected' ).trigger( 'change' );
            jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + GF_Geo.prefix + address_field ).find( 'select option[value="' + address_value + '"]' ).prop( 'selected', true ).trigger( 'change' );
            
            /*** Radio buttons ****/

            //jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + address_field + '[type="radio"][value="' + address_value + '"]' ).prop( 'checked', true ).trigger( 'change' );
            //jQuery( 'input[data-geocoder_id="' + geocoder_id + '"][data-output_location_field="' + address_field + '"][type="radio"][value="' + address_value + '"]' ).prop( 'checked', true ).trigger( 'change' );
            jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + GF_Geo.prefix + address_field ).find( 'input[type="radio"][value="' + address_value + '"]' ).prop( 'checked', true ).trigger( 'change' );

            /*** Checkboxes Field ****/

            //jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + address_field + '[type="checkbox"][value="' + address_value + '"]' ).prop( 'checked', true ).trigger( 'change' );
            //jQuery( 'input[data-geocoder_id="' + geocoder_id + '"][data-output_location_field="' + address_field + '"][type="checkbox"][value="' + address_value + '"]' ).prop( 'checked', true ).trigger( 'change' );
            jQuery( '.' + GF_Geo.prefix + 'geocoded-field-' + geocoder_id + '.' + GF_Geo.prefix + address_field ).find( 'input[type="checkbox"][value="' + address_value + '"]' ).prop( 'checked', true ).trigger( 'change' );
            
            /** gforms advanced address fields **/

            if ( jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).length != 0 && GF_Geo.processing.element != 'advanced_address_geocoder' ) {

                if ( address_field == 'street' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_line_1 input[type="text"]' ).val( address_value ).trigger( 'change' );
                }

                if ( address_field == 'city' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_city input[type="text"]' ).val( address_value ).trigger( 'change' );
                }

                if ( address_field == 'region_name' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_state input[type="text"]' ).val( address_value ).trigger( 'change' );
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_state select option[value="' + address_value + '"]' ).prop( 'selected', true ).trigger( 'change' );
                }

                if ( address_field == 'region_code' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_state select option[value="' + address_value + '"]' ).prop( 'selected', true ).trigger( 'change' );
                }

                if ( address_field == 'postcode' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_zip input[type="text"]' ).val( address_value ).trigger( 'change' );
                }

                if ( address_field == 'country_name' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_country select option[value="' + address_value + '"]' ).prop( 'selected', true ).trigger( 'change' );
                }

                if ( address_field == 'country_code' ) {
                    jQuery( '.gfgeo-advanced-address-geocoder-id-' + geocoder_id ).find( 'span.address_country select option[value="' + address_value + '"]' ).prop( 'selected', true ).trigger( 'change' );
                }
            }


            /*
            // for Gravity Forms advanced address field
            if ( jQuery( '.ginput_container_address' ).legth != 0 ) {
               
                if ( address_field == 'street' ) {
                
                    jQuery( '.ginput_container_address span.address_line_1 input' ).val( address_value ).trigger( 'change' );
                
                } else if ( address_field == 'city' ) {
                
                    jQuery( '.ginput_container_address span.address_city input' ).val( address_value ).trigger( 'change' );
                
                } else if ( address_field == 'region_name' ) {
                
                    jQuery( '.ginput_container_address span.address_state input' ).val( address_value ).trigger( 'change' );
                    jQuery( '.ginput_container_address span.address_state' ).find( 'select option[value="' + address_value + '"]' ).attr( 'selected','selected' ).trigger( 'change' );
                
                } else if ( address_field == 'postcode' ) {
                
                    jQuery( '.ginput_container_address span.address_zip input' ).val( address_value ).trigger( 'change' );
                
                } else if ( address_field == 'country_name' || address_field == 'country_code' ) {
                
                    jQuery( '.ginput_container_address span.address_country' ).find( 'select option[value="' + address_value + '"]' ).attr( 'selected','selected' ).trigger( 'change' );
                
                }
            }
            */
        },

        /**
         * Get place details
         *
         * This function provide the value of place name out of the box.
         * However, it can be extended to suppot more fields using the gfgeo_place_details hook.
         * 
         * @param  {[type]} results [description]
         * @param  {[type]} element [description]
         * @return {[type]}         [description]
         */
        get_place_details : function( address_fields, results, element ) {

            //if ( results.place_id !== undefined || results.place_id != '' ) {

                // passing blank object to the funciton.            
                var service = new google.maps.places.PlacesService( jQuery('<div>').get(0) );
                
                // get place details
                service.getDetails( { placeId : address_fields.place_id }, function( PlaceResult, PlacesServiceStatus ) {

                    if ( PlacesServiceStatus == 'OK') {
                        
                        if ( PlaceResult.name !== undefined ) {
                            GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'place_name', PlaceResult.name );
                        }

                        // hook custom functions if needed
                        jQuery( document ).trigger( 
                            'gfgeo_place_details', 
                            [ PlaceResult, results, address_fields, element, GF_Geo ] 
                        );
                    }
                });
            //}
        },

        /**
         * Exctract location fields from results and dynamically place them where needed
         * 
         * @param  {object} results location data returned forom geocoder
         * 
         * @return {[type]}         [description]
         */
        save_location_fields : function( results, element ) {
            	
            // for locator address field with locator button but without geocoder ID.
            // fill the address in the address field
            if ( GF_Geo.processing.locator_id != false && GF_Geo.processing.locator_id != '' ) {
                jQuery( '#gfgeo-infield-locator-button-' + GF_Geo.processing.locator_id ).closest( 'li.gfield' ).find( '.gfgeo-address-field' ).val( results.formatted_address ).trigger( 'change' );
            }

            // abort if no geocoder ID set
            if ( GF_Geo.geocoder_id == '' ) {
                
                GF_Geo.processing_end();

                return;
            }

            var formID = GF_Geo.form.id;

            jQuery( document ).trigger( 
                'gfgeo_save_location_data_start', 
                [ results, element, GF_Geo ] 
            );

            // clear all location fields before saving new values.
            GF_Geo.clear_fields( GF_Geo.geocoder_id, element );

            // address fields object
            address_fields = {
                'street_number'     : '',
                'street_name'       : '',
                'street'            : '',
                'street_bw'         : '',
                'premise'           : '',
                'subpremise'        : '',
                'neighborhood'      : '',
                'city'              : '',
                'region_code'       : '',
                'region_name'       : '',
                'country'           : '',
                'postcode'          : '',
                'country_code'      : '',
                'country_name'      : '',
                'address'           : results.formatted_address,
                'formatted_address' : results.formatted_address,
                'lat'               : '',
                'lng'               : '',
                'place_id'			: results.place_id || '',
                'distance'			: '',
                'duration'			: '',
            };

            // if doing coords geocoder or map keep the original coordinates 
            // was entered or retrived from the map.
            // That's because after geocoding the coordinates might be abit different
            if ( element == 'coords_geocoder' ||  element == 'map' ) {

                address_fields.lat = GF_Geo.processing.location[0];
                address_fields.lng = GF_Geo.processing.location[1];
            
            } else {
        	
        		address_fields.lat = results.geometry.location.lat();
            	address_fields.lng = results.geometry.location.lng();
        	}

            // if address field geocoded get the original address entered
            if ( element == 'address_geocoder' || element == 'advanced_address_geocoder' ) {
 				if ( typeof GF_Geo.processing.location == 'string' ) {
                	address_fields.address = GF_Geo.processing.location.trim();
                }
            }

            // if address autocomplete triggered get the address selected
            if ( element == 'address_autocomplete' ) {
                 address_fields.address = GF_Geo.processing.selected_address;
            }

            // Update maps based on new coords if needed    
            if ( element != 'map' ) {
       
                jQuery.each( GF_Geo.maps, function( map_id, map_options ) {
                  
                    if ( map_options.args.geocoder_id == GF_Geo.geocoder_id ) { 
                       
                        GF_Geo.update_map( map_id, address_fields.lat, address_fields.lng );
                    }              
                });
            }

            // if page locator triggered and address autocompelte set to 
            // use the current location as bounds.
            if ( element == 'page_locator' && jQuery( 'input[data-geocoder_id="' + GF_Geo.geocoder_id + '"][data-address_autocomplete="1"][data-autocomplete_locator_bounds="1"]' ).length != 0 && address_fields.lat ) {

                // get the autocomplete field ID
                var fieldId = jQuery( 'input[data-geocoder_id="' + GF_Geo.geocoder_id + '"][data-address_autocomplete="1"][data-autocomplete_locator_bounds="1"]' ).attr( 'id' );

                // get bounds from locator
                var circle = new google.maps.Circle( {
                    center : {
                        lat : address_fields.lat,
                        lng : address_fields.lng
                    },
                    //radius : position.coords.accuracy
                } );
                
                // set autocompelte bounds
                GF_Geo.autocompletes[fieldId].setBounds( circle.getBounds() ); 
            }

            // update coordinates in dynamic fields
            GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'latitude', address_fields.lat );            
            GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'longitude', address_fields.lng );            
            
            // udpate coordinates
            jQuery( '.' + GF_Geo.prefix + 'latitude-field[data-geocoder_id="' + GF_Geo.geocoder_id + '"]' ).val( address_fields.lat ).trigger( 'change' );
            jQuery( '.' + GF_Geo.prefix + 'longitude-field[data-geocoder_id="' + GF_Geo.geocoder_id + '"]' ).val( address_fields.lng ).trigger( 'change' );

            // Update maps based on new coords if needed    
            if ( element == 'map' ) {

                var map_address_disabled = jQuery( '#gfgeo-map-' + GF_Geo.processing.element_id ).data( 'disable_address_output' );
         
                if ( map_address_disabled == true ) {
                    
                    // update geocoded status
                    jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-'  + GF_Geo.geocoder_id + '.status' ).val( '1' ).trigger( 'change' );

                    GF_Geo.processing_end();

                    return address_fields;
                }
            }

            // update address fields with formatted address except if coming from address autocomplete. 
            // No need to update when autocomplete was striggered since the user already 
            // choose an address from suggested results.
            if ( element != 'address_geocoder' && element != 'address_autocomplete' ) {   
                jQuery( '.' + GF_Geo.prefix + 'address-field[data-geocoder_id="' + GF_Geo.geocoder_id + '"]' ).val( results.formatted_address ).trigger( 'change' );
            }

            // update address in dynamic fields
            GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'address', address_fields.address );

            // update formatted address in dynamic fields
            GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'formatted_address', address_fields.formatted_address );
            
            // get place details if needed
            if ( address_fields.place_id && ( jQuery( '.gfgeo-place_name' ).length || GF_Geo.place_details_enabled == true ) ) {
                GF_Geo.get_place_details( address_fields, results, element );
            }

            // loop through the address componenets and update 
            // the geocoded address fields
            address = results.address_components || [];

            var city_found                  = false;
            var administrative_area_level_3 = false;

            for ( var x in address ) {

                // street number
                if ( address[x].types == 'street_number' && address[x].long_name != undefined ) {
                        
                    address_fields.street_number = address[x].long_name;

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'street_number', address_fields.street_number );       
                } 

                // subpremise
                if ( address[x].types == 'subpremise' && address[x].long_name != undefined ) {

                    address_fields.subpremise = address[x].long_name;

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'subpremise', address_fields.subpremise );
                }     

                // street name and street fields
                if ( address[x].types == 'route' && address[x].long_name != undefined ) {  

                     //save street name in variable
                    address_fields.street_name = address[x].long_name;
                    
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'street_name', address_fields.street_name );
                
                    //udpate street ( number + name ) fields  if street_number exists
                    if ( address_fields.street_number != '' ) {

                        var TempSubPremise = ' ';

                        if ( address_fields.subpremise != '' ) {
                            TempSubPremise = '/' + address_fields.subpremise + ' ';
                        } 
                        address_fields.street    = address_fields.street_number + TempSubPremise + address_fields.street_name;
                        address_fields.street_bw = address_fields.street_name + TempSubPremise + address_fields.street_number;
                        
                    } else {
                        address_fields.street    = address_fields.street_name;
                        address_fields.street_bw = address_fields.street_name;
                    }     

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'street', address_fields.street );
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'street_bw', address_fields.street_bw );
                }

                // premise
                if ( address[x].types == 'premise' && address[x].long_name != undefined ) {

                    address_fields.premise = address[x].long_name;

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'premise', address_fields.premise );
                }
                
                // neighborhood
                 if ( address[x].types == 'neighborhood,political' && address[x].long_name != undefined ) {

                    address_fields.neighborhood = address[x].long_name;

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'neighborhood', address_fields.neighborhood );
                }
                
                // city
                if ( address[x].types == 'locality,political' && address[x].long_name != undefined ) {

                    address_fields.city = address[x].long_name;
       
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'city', address_fields.city );

                    city_found = true;
                }

                // maybe city -to be used after the loop if city was not found.
                if ( address[x].types == 'administrative_area_level_3,political' && address[x].long_name != undefined ) {

                    administrative_area_level_3 = address[x].long_name;
                }

                // city, sublocality ( if city was not found )
                if ( ! city_found && address[x].types == 'political,sublocality,sublocality_level_1' && address[x].long_name != undefined ) {

                    address_fields.city = address[x].long_name;
       
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'city', address_fields.city );

                    city_found = true;
                }

                // town ( if city was not found )
                if ( ! city_found && address[x].types == 'postal_town' && address[x].long_name != undefined ) {

                    address_fields.city = address[x].long_name;
       
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'city', address_fields.city );

                    city_found = true;
                }

                // county
                if ( address[x].types == 'administrative_area_level_2,political' && address[x].long_name != undefined ) {

                    address_fields.county = address[x].long_name;

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'county', address_fields.county );
                }

                // region code and name
                if ( address[x].types == 'administrative_area_level_1,political' ) {

                    address_fields.region_name = address[x].long_name;
                    address_fields.region_code = address[x].short_name;
                    
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'region_code', address_fields.region_code );
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'region_name', address_fields.region_name );              
                }  
                
                // postal code
                if ( address[x].types == 'postal_code' && address[x].long_name != undefined ) {

                    address_fields.postcode = address[x].long_name;
                    
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'postcode', address_fields.postcode );                
                }
                
                // country code and name
                if ( address[x].types == 'country,political' ) {

                    address_fields.country_name = address[x].long_name;
                    address_fields.country_code = address[x].short_name;

                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'country_code', address_fields.country_code );
                    GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'country_name', address_fields.country_name );           
                }

                jQuery( document ).trigger(
		            'gfgeo_save_location_field_data', 
		            [ address[x], GF_Geo, results, element ] 
		        );
            }

            // Check city in administrative_area_level_3 if not found in the loop above.
            if ( ! city_found && administrative_area_level_3 != false ) {
            	GF_Geo.generate_location_data( GF_Geo.geocoder_id, 'city', administrative_area_level_3 );
            }

            // update geocoded status
            jQuery( 'input.' + GF_Geo.prefix + 'geocoded-field-'  + GF_Geo.geocoder_id + '.status' ).val( '1' ).trigger( 'change' );

            jQuery( '.gfgeo-geocoded-hidden-fields-wrapper' ).each( function() {
            	
            	if ( jQuery( this ).data( 'geocoder_id' ) == GF_Geo.geocoder_id && jQuery( this ).data( 'distance_destination_geocoder_id' ) != '' ) {
            		
            		GF_Geo.get_driving_directions( GF_Geo.geocoder_id, jQuery( this ).data( 'distance_destination_geocoder_id' ) );

            		//GF_Geo.get_distance( [ address_fields.lat, address_fields.lng ], jQuery( this ).data( 'distance_geocoder_id' ) );
            	
            	} else if ( jQuery( this ).data( 'distance_destination_geocoder_id' ) == GF_Geo.geocoder_id ) {

            		GF_Geo.get_driving_directions( jQuery( this ).data( 'geocoder_id' ), GF_Geo.geocoder_id );

            		//GF_Geo.get_distance( [ address_fields.lat, address_fields.lng ], jQuery( this ).data( 'geocoder_id' ) );
            	}
            });

            jQuery( document ).trigger( 
                'gfgeo_save_location_data_end', 
                [ address_fields, GF_Geo, results, element ] 
            );

            GF_Geo.processing_end();

            return address_fields;
        }
    };

    //init GF_Geo
    GF_Geo.init();
});

jQuery( document ).ready( function($) {

	var GF_Geo_Admin = { 

		// form being edited
		'form' : form,

		// maps object
		'maps' : {},

		/**
		 * Initiate script 
		 * @return {[type]} [description]
		 */
		init : function() {

			// form editor init
			GF_Geo_Admin.form_editor_init();

			// initiate maps
		    jQuery.each( GF_Geo_Admin.form.fields, function( field, field_values ) {
		        
		        if ( field_values.type == 'gfgeo_map' ) {

		            GF_Geo_Admin.gfgeo_render_map_admin( jQuery( '#gfgeo-map-' + GF_Geo_Admin.form.id + '_' + field_values.id ).data() );
		        }
		    });

		    // set marker position
		    GF_Geo_Admin.set_marker_position();

		    // set map options
		    GF_Geo_Admin.set_map_options();

		    GF_Geo_Admin.update_fields_dynamically();
		},

		/*get_checkboxes_value : function( element ) {

			var checked = [];

			jQuery( element + ' input[type=checkbox]:checked' ).each( function () {
                checked.push( parseInt( this.value ) );
            });

            return checked;
		},*/
		
		/**
		 * Initiate form editor 
		 * @return {[type]} [description]
		 */
		form_editor_init : function() {

			// Apply fields options
		    fieldSettings.post_custom_field += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
		    // hidden field
		    fieldSettings.hidden	+= ', .css_class_setting, .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
			
			// addess field
			fieldSettings.address += ', .post_custom_field_setting, .gfgeo-geocoder-id, .gfgeo-disable-field-geocoding, .gfgeo-infield-locator-button';
			fieldSettings.address += ', .gfgeo-address-autocomplete, .gfgeo-address-autocomplete-country';
			fieldSettings.address += ', .gfgeo-address-autocomplete-placeholder, .gfgeo-address-autocomplete-desc';
			fieldSettings.address += ', .gfgeo-address-autocomplete-types';
			fieldSettings.address += ', .gfgeo-address-autocomplete-locator-bounds, .gfgeo-address-autocomplete-bounds';
			
			// text field
			//fieldSettings['text'] += ', .gfgeo-geocoder-section-star, .gfgeo-geocoder-section-en';
			fieldSettings.text += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
			//fieldSettings['text'] += ', .gfgeo-address-autocomplete-countr, .gfgeo-address-autocomplete-type, .gfgeo-address-autocomplet';

			// select textbox
		 	fieldSettings.checkbox 	  += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
		 	fieldSettings.select   	  += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
		 	fieldSettings.radio    	  += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
		 	fieldSettings.multiselect += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
		 	fieldSettings.number      += ', .gfgeo-dynamic-location-field, .gfgeo-geocoder-id';
		 	
		 	jQuery( document ).bind( 'gform_field_added', function( event, form, field ) {
		    	if ( field.type == 'gfgeo_map' ) {
		        	GF_Geo_Admin.gfgeo_render_map_admin( jQuery( '#gfgeo-map-' + form.id + '_' + field.id ).data() );
		    	}
			} );

        	jQuery( '#gfgeo-gmw-post-integration' ).change( function() {

        		if ( jQuery( this ).is( ':checked' ) ) {
	        		jQuery( '.gfgeo-gmw-post-integration-wrapper' ).show();
	        	} else {
	        		jQuery( '.gfgeo-gmw-post-integration-wrapper' ).hide();
	        	}
	        } );

        	/*jQuery( document ).on( 'change', '.gfgeo-distance-geocoders-checkbox', function() {
				
				var checkedValues = GF_Geo_Admin.get_checkboxes_value( '.gfgeo-distance-geocoders-items' );

				SetFieldProperty( 'gfgeo_distance_geocoders', checkedValues );
			});

			jQuery( "ul.gfgeo-distance-geocoders-items li" ).sortable({ handle: '.handle' });*/

		    // run some functions when field options shows                                
			jQuery( document ).bind( 'gform_load_field_settings', function( event, field, form ){

				var formFields;

				// get form fields
				if ( typeof form.fields != 'undefined' ) {
					formFields = form.fields;
				} else {
					formFields = [];
				}

				// clear select option. We will create it again below
				jQuery( jQuery( 'select#gfgeo-geocoder-id, select#gfgeo-distance-directions-panel-id, select#gfgeo-distance-destination-geocoder-id, select#gfgeo-gmw-post-integration-phone, select#gfgeo-gmw-post-integration-fax, select#gfgeo-gmw-post-integration-email, select#gfgeo-gmw-post-integration-website' ).find( 'option:gt(0)' ).remove() );
					
				// with map dropdown for route option.
				//jQuery( jQuery( 'select#gfgeo-geocoder-id, select#gfgeo-distance-travel-route-map-id, select#gfgeo-distance-directions-panel-id, select#gfgeo-distance-destination-geocoder-id, select#gfgeo-gmw-post-integration-phone, select#gfgeo-gmw-post-integration-fax, select#gfgeo-gmw-post-integration-email, select#gfgeo-gmw-post-integration-website' ).find( 'option:gt(0)' ).remove() );
				
				//jQuery( '.gfgeo-distance-geocoders-items' ).children().remove();

				/*if ( field.type == 'gfgeo_distance' ) {
					var distanceValues = ( typeof field.gfgeo_distance_geocoders != 'undefined' ) ? field.gfgeo_distance_geocoders : [];
				}*/

				// loop through form fields and look for geocoder fields
				// collect all of them and appened it to the Geocoder ID select dropdown as options 
				jQuery( formFields ).each( function() {
					
					// check if geocoder field
					if ( this.type == 'gfgeo_geocoder' ) {
						
						jQuery( jQuery( 'select#gfgeo-geocoder-id, select#gfgeo-distance-destination-geocoder-id' ).append( jQuery( '<option>' ).attr( 'value', this.id ).text( 'Geocoder ID - ' + this.id ) ) );
						
						/*if ( field.type == 'gfgeo_distance' ) {
						
							var checkedDistance = '';

							if ( jQuery.inArray( this.id, distanceValues ) !== -1 ) {
								checkedDistance = 'checked="checked"';
							}

							var distanceCheckbox = '<input type="checkbox" name="gfgeo_distance_geocoders[]" class="gfgeo-distance-geocoders-checkbox" value="' + this.id + '"' + checkedDistance + '>';
							
							jQuery( 'ul.gfgeo-distance-geocoders-items' ).append( '<li data-item_id="' + this.id + '"><label class="inline">' + distanceCheckbox + ' Geocoder ID ' + this.id + '</label></li>' ).sortable( { stop : function( event, ui ) { 

								var order = [];

								jQuery( 'ul.gfgeo-distance-geocoders-items li' ).each( function () {
					                order.push( parseInt( jQuery( this ).data( 'item_id' ) ) );
					            });

					            SetFieldProperty( 'gfgeo_distance_geocoders_order', order );

					            var checkedValues = GF_Geo_Admin.get_checkboxes_value( '.gfgeo-distance-geocoders-items' );

					            console.log( checkedValues)
								SetFieldProperty( 'gfgeo_distance_geocoders', checkedValues );
							} });
						}*/
			
					// Otherwise, check for different fields that can serve as GMW Contact info fields
					} else if ( this.type == 'gfgeo_map' ) {

						// Currently disabled.
						//jQuery( jQuery( 'select#gfgeo-distance-travel-route-map-id' ).append( jQuery( '<option>' ).attr( 'value', this.id ).text( 'Map ID - ' + this.id ) ) );

					} else if ( this.type == 'gfgeo_directions_panel' ) {

						jQuery( jQuery( 'select#gfgeo-distance-directions-panel-id' ).append( jQuery( '<option>' ).attr( 'value', this.id ).text( 'Directions Panel - ' + this.id ) ) );
						
					} else if ( jQuery.inArray( this.type, [ 'post_custom_field', 'phone', 'email', 'website', 'text', 'number', 'hidden', 'checkbox', 'select', 'radio', 'paragraph', 'html' ] ) != -1 ) {
	
				 		jQuery( jQuery( 'select#gfgeo-gmw-post-integration-phone, select#gfgeo-gmw-post-integration-fax, select#gfgeo-gmw-post-integration-email, select#gfgeo-gmw-post-integration-website' ).append( jQuery( '<option>' ).attr( 'value', this.id ).text( this.label + ' - ( field ID ' + this.id + ')' ) ) );		
					}
				});
				
				jQuery( 'select#gfgeo-distance-destination-geocoder-id option[value="' + field.id + '"]' ).remove();

				/*if ( field.type == 'gfgeo_distance' ) {

					if ( typeof field.gfgeo_distance_geocoders_order != 'undefined' ) {
					
						var order   = field.gfgeo_distance_geocoders_order;
						var element = jQuery( 'ul.gfgeo-distance-geocoders-items' );
						var map     = {};

						jQuery( 'ul.gfgeo-distance-geocoders-items li' ).each( function() { 
						    var element = $(this);
						    map[ element.attr( 'data-item_id' ) ] = element;
						});

						for ( var i = 0, l = order.length; i < l; i ++ ) {
						    if ( map[ order[i] ] ) {
						        element.append( map[ order[i] ] );
						    }
						}
					}
				}*/

				var addressFields = [
					'status',
					'place_name',
		        	'street_number', 
		        	'street_name', 
		        	'street', 
		        	'subpremise', 
		        	'premise', 
		        	'neighborhood', 
		        	'city', 
		        	'county', 
		        	'region_code', 
		        	'region_name', 
		        	'postcode', 
		        	'country_code', 
		        	'country_name', 
		        	'address', 
		        	'formatted_address', 
		        	'latitude', 
		        	'longitude' 
		    	];

		    	/***** set fields value *****/

		    	// geocoder field
				jQuery( '#gfgeo-geocoder-id' ).val( field.gfgeo_geocoder_id );
				jQuery( '#gfgeo-default-latitude' ).val( field.gfgeo_default_latitude );
				jQuery( '#gfgeo-default-longitude' ).val( field.gfgeo_default_longitude );
		    	jQuery( '#gfgeo-page-locator' ).attr( 'checked', field.gfgeo_page_locator == true );
		    	jQuery( '#gfgeo-distance-destination-geocoder-id' ).val( field.gfgeo_distance_destination_geocoder_id );
		    	jQuery( '#gfgeo-distance-travel-mode' ).val( field['gfgeo_distance_travel_mode'] );
		    	jQuery( '#gfgeo-distance-unit-system' ).val( field['gfgeo_distance_unit_system'] );
		    	//jQuery( '#gfgeo-distance-travel-route-map-id' ).val( field.gfgeo_distance_travel_route_map_id );
		    	jQuery( '#gfgeo-distance-travel-show-route-on-map' ).attr( 'checked', field.gfgeo_distance_travel_show_route_on_map == true );
		    	jQuery( '#gfgeo-distance-directions-panel-id' ).val( field.gfgeo_distance_directions_panel_id );
		    	jQuery( '#gfgeo-google-maps-link' ).attr( 'checked', field['gfgeo_google_maps_link'] == true );
		    	jQuery( '#gfgeo-gmw-post-integration' ).attr( 'checked', field['gfgeo_gmw_post_integration'] == true );
		    	jQuery( '#gfgeo-gmw-post-integration-phone' ).val( field['gfgeo_gmw_post_integration_phone'] );
		    	jQuery( '#gfgeo-gmw-post-integration-fax' ).val( field['gfgeo_gmw_post_integration_fax'] );
		    	jQuery( '#gfgeo-gmw-post-integration-email' ).val( field['gfgeo_gmw_post_integration_email'] );
		    	jQuery( '#gfgeo-gmw-post-integration-website' ).val( field['gfgeo_gmw_post_integration_website'] );
		    	jQuery( '#gfgeo-gmw-user-integration' ).attr( 'checked', field['gfgeo_gmw_user_integration'] == true );
		        jQuery( '#gfgeo-user-meta-field' ).val( field['gfgeo_user_meta_field'] );

		        // meta fields
		    	jQuery.each( addressFields, function( index, value ) {
					jQuery( '#gfgeo_' + value + '_post_meta' ).val( field['gfgeo_' + value + '_post_meta'] );
					jQuery( '#gfgeo_' + value + '_user_meta' ).val( field['gfgeo_' + value + '_user_meta'] );
					jQuery( '#gfgeo_' + value + '_xprofile_field' ).val( field['gfgeo_' + value + '_xprofile_field'] );
				});

		    	// dynamic field
				jQuery( '#gfgeo-dynamic-location-field').val( field['gfgeo_dynamic_location_field'] );

				//locator field
				jQuery( '#gfgeo-locator-button-label' ).val( field['gfgeo_locator_button_label'] );
				jQuery( '#gfgeo-infield-locator-button' ).attr( 'checked', field['gfgeo_infield_locator_button'] == true );
		        jQuery( '#gfgeo-location-found-message' ).val( field['gfgeo_location_found_message'] );
		        jQuery( '#gfgeo-hide-location-failed-message' ).attr( 'checked', field['gfgeo_hide_location_failed_message'] == true );
		        jQuery( '#gfgeo-ip-locator-status' ).val( field['gfgeo_ip_locator_status'] );

				//autocomplete 
		        jQuery( '#gfgeo-address-autocomplete' ).attr( 'checked', field['gfgeo_address_autocomplete'] == true );
		        jQuery( '#gfgeo-address-autocomplete-types' ).val( field['gfgeo_address_autocomplete_types'] );
		        jQuery( '#gfgeo-address-autocomplete-country' ).val( field['gfgeo_address_autocomplete_country'] );
		        jQuery( '#gfgeo-address-autocomplete-locator-bounds' ).attr( 'checked', field['gfgeo_address_autocomplete_locator_bounds'] == true );
		        jQuery( '#gfgeo-address-autocomplete-bounds' ).val( field['gfgeo_address_autocomplete_bounds'] );
		        jQuery( '#gfgeo-address-autocomplete-placeholder' ).val( field['gfgeo_address_autocomplete_placeholder'] );
		        jQuery( '#gfgeo-address-autocomplete-desc' ).val( field['gfgeo_address_autocomplete_desc'] );

		        // coordinates
		        jQuery( '#gfgeo-latitude-placeholder' ).val( field['gfgeo_latitude_placeholder'] );
				jQuery( '#gfgeo-longitude-placeholder' ).val( field['gfgeo_longitude_placeholder'] );
				jQuery( '#gfgeo-custom-field-method' ).attr( 'checked', field['gfgeo_custom_field_method'] == true );
		        
				//map field options
				jQuery( '#gfgeo-map-default-latitude' ).val( field['gfgeo_map_default_latitude'] );
				jQuery( '#gfgeo-map-default-longitude' ).val( field['gfgeo_map_default_longitude'] );
				jQuery( '#gfgeo-map-width' ).val( field['gfgeo_map_width'] );
				jQuery( '#gfgeo-map-height' ).val( field['gfgeo_map_height'] );
				jQuery( '#gfgeo-map-styles' ).val( field['gfgeo_map_styles'] );
				jQuery( '#gfgeo-map-icon' ).val( field['gfgeo_map_icon'] );
		        jQuery( '#gfgeo-map-type' ).val( field['gfgeo_map_type'] );
		        jQuery( '#gfgeo-zoom-level' ).val( field['gfgeo_zoom_level'] );
		        jQuery( '#gfgeo-draggable-marker' ).attr( 'checked', field['gfgeo_draggable_marker'] == true );
		        //jQuery( '#gfgeo-map-styles' ).val( field.gfgeo_map_styles == undefined ? "" : field.gfgeo_map_styles );
		        jQuery( '#gfgeo-set-marker-on-click' ).attr( 'checked', field['gfgeo_set_marker_on_click'] == true );
		        jQuery( '#gfgeo-map-scroll-wheel' ).attr( 'checked', field['gfgeo_map_scroll_wheel'] == true );
		        jQuery( '#gfgeo-disable-address-output' ).attr( 'checked', field['gfgeo_disable_address_output'] == true );
		        
		        // disable geocoding     
		        jQuery( '#gfgeo-disable-field-geocoding' ).attr( 'checked', field['gfgeo_disable_field_geocoding'] == true );

		     	// make certain field options availabe for a single geocoder. If checked in one geocoder 
		     	// the option will be disabled in other geocoders
		        jQuery( '#gfgeo-page-locator').attr( 'disabled', false );
		        jQuery( '#gfgeo-gmw-post-integration').attr( 'disabled', false );
		        jQuery( '#gfgeo-gmw-user-integration').attr( 'disabled', false );
		        jQuery( 'select#gfgeo-distance-directions-panel-id option' ).prop( 'disabled', false );
		        
		        if ( field.type == 'gfgeo_geocoder' ) {

		        	if ( jQuery( '#gfgeo-gmw-post-integration' ).is( ':checked' ) ) {
		        		jQuery( '.gfgeo-gmw-post-integration-wrapper' ).show();
		        	} else {
		        		jQuery( '.gfgeo-gmw-post-integration-wrapper' ).hide();
		        	}
		        }
		       
		        jQuery.each( form.fields, function( key,val ) {
					
					if ( val.type == 'gfgeo_geocoder' ) {	
						
						// page locator
						if ( val.gfgeo_page_locator != undefined && val.gfgeo_page_locator == true && val.id != field.id ) {
							jQuery( '#gfgeo-page-locator' ).attr( 'disabled', true );
						} 

						// gmw post integration
						if ( val.gfgeo_gmw_post_integration != undefined && val.gfgeo_gmw_post_integration == true && val.id != field.id ) {
							jQuery( '#gfgeo-gmw-post-integration' ).attr( 'disabled', true );
						} 

						// gmw user integration
						if ( val.gfgeo_gmw_user_integration != undefined && val.gfgeo_gmw_user_integration == true && val.id != field.id ) {
							jQuery( '#gfgeo-gmw-user-integration' ).attr( 'disabled', true );
						}

						// gmw user integration
						if ( val.gfgeo_distance_directions_panel_id != undefined && val.gfgeo_distance_directions_panel_id != '' && val.gfgeo_distance_directions_panel_id != field.gfgeo_distance_directions_panel_id ) {
							jQuery( 'select#gfgeo-distance-directions-panel-id option[value="' + val.gfgeo_distance_directions_panel_id +'"]' ).prop( 'disabled', true );
						} 
					}
				});			
			});
		},

		/**
		 * Update fields text while typing in the input textbox
		 * 
		 * @return {[type]} [description]
		 */
		update_fields_dynamically : function() {

			// udpate locator button value
			jQuery( '#gfgeo-locator-button-label' ).keyup( function() {
                jQuery( this ).closest( 'li.gfield' ).find( '.gfgeo-locator-button' ).val( jQuery( this ).val() );
            });

			// udpate coordinates placeholder
            jQuery( '.coordinates-placeholder' ).keyup( function() {
            	field = jQuery( this ).data( 'field' );
                jQuery( this ).closest( 'li.gfield' ).find( '.gfgeo-' + field + '-field' ).attr( 'placeholder', jQuery( this ).val() );
            });
		},

		/**
		 * Create map in form editor
		 * @param  {[type]} map_data [description]
		 * @return {[type]}          [description]
		 */
		gfgeo_render_map_admin : function( map_data ) {

	        // init map args object
	        GF_Geo_Admin.maps[map_data.map_id] = {};

	        // map args
	        GF_Geo_Admin.maps[map_data.map_id].args = map_data;

	        // get initial marker position
	        GF_Geo_Admin.maps[map_data.map_id].latlng = new google.maps.LatLng( map_data.latitude, map_data.longitude );
	     
	        // map options
	        GF_Geo_Admin.maps[map_data.map_id].options = {
	            zoom             : parseInt( map_data.zoom_level),
	            center           : GF_Geo_Admin.maps[map_data.map_id].latlng,
	            mapTypeId        : google.maps.MapTypeId[map_data.map_type],
	            backgroundColor  : '#f1f1f1',
	            scrollwheel      : false,
	            draggable		 : false,
	            disableDefaultUI : true,
	            zoomControl		 : true
	        };
	    	
	        // generate the map
	        GF_Geo_Admin.maps[map_data.map_id].map = new google.maps.Map( document.getElementById( 'gfgeo-map-' + map_data.map_id ), GF_Geo_Admin.maps[map_data.map_id].options );
	        
	        // generate marker
	        GF_Geo_Admin.maps[map_data.map_id].marker = new google.maps.Marker({
	            position  : GF_Geo_Admin.maps[map_data.map_id].latlng,
	            map       : GF_Geo_Admin.maps[map_data.map_id].map,
	            draggable : false,
	            icon      : map_data.map_marker
	        });
	        
	        // when dragging the marker on the map
	        google.maps.event.addListener( GF_Geo_Admin.maps[map_data.map_id].marker, 'dragend', function( event ){
	            
	            // set the global Geocoder ID
	            GF_Geo_Admin.geocoder_id = map_data.geocoder_id;

	            // geocode coords to get address fields
	            GF_Geo_Admin.geocoder( 'map', [ event.latLng.lat(), event.latLng.lng() ], GF_Geo_Admin.map_geocoder_success, false );  
	        });

	        // resize map if was hidden on page load. For conditional logic mostly.
	        jQuery( '#frm_field_' + map_data.map_id + '_container' ).bind( 'show', function(){
	            
	            setTimeout( function() {

	                // resize map
	                google.maps.event.trigger( GF_Geo_Admin.maps[map_data.map_id].map, 'resize' );

	                GF_Geo_Admin.maps[map_data.map_id].map.panTo( GF_Geo_Admin.maps[map_data.map_id].marker.position );
	            
	            }, 300 );
	        });
		},

		/**
		 * Set marker position when updating coordinates
		 * 
		 */
		set_marker_position : function() {

			$( '#gfgeo-map-default-latitude, #gfgeo-map-default-longitude' ).on( 'keydown focusout', function( event ) {

				if ( ( event.type == 'keydown' && event.which == 13 ) || ( event.type == 'focusout' ) ) {

					if ( $.trim( $( '#gfgeo-map-default-latitude' ).val() ).length != 0 && $.trim( $('#gfgeo-map-default-longitude' ).val() ).length != 0 ) {
			        	
			        	var fieldId = jQuery( this ).closest( 'li.gfield' ).find( '.gfgeo-map' ).data( 'map_id' );

			        	latlng = {
			        		lat : parseFloat( $( '#gfgeo-map-default-latitude' ).val() ), 
			        		lng : parseFloat( $( '#gfgeo-map-default-longitude' ).val() ) 
			        	};

			        	// init google geocoder
			            geocoder = new google.maps.Geocoder();

			            // run geocoder
			            geocoder.geocode( {'location': latlng }, function( results, status ) {

			                // on success
			                if ( status == google.maps.GeocoderStatus.OK ) {

			 					latlng = new google.maps.LatLng( results[0].geometry.location.lat(), results[0].geometry.location.lng() );

			 					GF_Geo_Admin.maps[fieldId].marker.setPosition( latlng );
			        			GF_Geo_Admin.maps[fieldId].map.panTo( latlng );             
			                }
			            });
				    };
				}
			});
		},

		/**
		 * Update map type and zoom level when changing field options
		 * 
		 */
		set_map_options : function() {

			jQuery( '#gfgeo-map-type, #gfgeo-zoom-level' ).on( 'change', function() {

		    	fieldId = jQuery( this ).closest( 'li.gfield' ).find( '.gfgeo-map' ).data( 'map_id' );
		    	value   = jQuery( this ).val();

		    	// update map type
		    	if ( jQuery( this ).attr( 'id') == 'gfgeo-map-type' ) {
		    		
		    		GF_Geo_Admin.maps[fieldId].map.setMapTypeId( google.maps.MapTypeId[value] );
		    	
		    	// update zoom level
		    	} else {
		    		GF_Geo_Admin.maps[fieldId].map.setZoom( parseInt( value ) );
		    	}
		    });
		}
	}

	GF_Geo_Admin.init();
});

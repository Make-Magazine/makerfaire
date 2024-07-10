/**
 * @namespace WPGMZA
 * @module ProMapEditPage
 * @pro-requires WPGMZA.MapEditPage
 */
jQuery(function($) {
	
	if(WPGMZA.currentPage != "map-edit")
		return;
	
	WPGMZA.ProMapEditPage = function()
	{
		var self = this;
		
		WPGMZA.MapEditPage.apply(this, arguments);
		
		this.directionsOriginIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_origin_icon_picker_container .wpgmza-marker-icon-picker") );
		this.directionsDestinationIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_destination_icon_picker_container .wpgmza-marker-icon-picker") );
		
		this.advancedSettingsMarkerIconPicker = new WPGMZA.MarkerIconPicker( $("#advanced-settings-marker-icon-picker-container .wpgmza-marker-icon-picker") );

		this.userIconPicker = new WPGMZA.MarkerIconPicker( $("#wpgmza_show_user_location_conditional .wpgmza-marker-icon-picker") );

		this.storeLocatorIconPicker = new WPGMZA.MarkerIconPicker( $("#wpgmza_store_locator_bounce_conditional .wpgmza-marker-icon-picker") );

		if(!WPGMZA.InternalEngine.isLegacy()){
			this.streetViewEditor = new WPGMZA.StreetViewEditor($('.streetview-starting-editor'), this.map);
		}

		$("input[name='store_locator_search_area']").on("input", function(event) {
			self.onStoreLocatorSearchAreaChanged(event);
		});
		self.onStoreLocatorSearchAreaChanged();

		// InfoWindow colours
		if($('input[name="wpgmza_iw_type"][value="1"]').prop('checked') || 
			$('input[name="wpgmza_iw_type"][value="2"]').prop('checked') || 
			$('input[name="wpgmza_iw_type"][value="3"]').prop('checked'))
			$('#iw_custom_colors_row').fadeIn();
		else
			$('#iw_custom_colors_row').fadeOut();

		$('.iw_custom_click_show').on("click", function(){
			$('#iw_custom_colors_row').fadeIn();
		});

		$('.iw_custom_click_hide').on("click", function(){
			$('#iw_custom_colors_row').fadeOut();
		});
		
		// Marker listing push-in-map
		if($('#wpgmza_push_in_map').prop('checked'))
			$('#wpgmza_marker_list_conditional').fadeIn();
		else
			$('#wpgmza_marker_list_conditional').fadeOut();

		$('#wpgmza_push_in_map').on('change', function() {
			if($(this).prop('checked'))
				$('#wpgmza_marker_list_conditional').fadeIn();
			else
				$('#wpgmza_marker_list_conditional').fadeOut();
		});

		// Marker Listing Panel Auto Open
		if(parseInt($('#marker_listing_component_anchor').val()) < 9 && parseInt(jQuery('input[name="wpgmza_listmarkers_by"]:checked').val()) === WPGMZA.MarkerListing.STYLE_PANEL){
			$('#marker_listing_component_auto_open_wrap').fadeIn();
		} else {
			$('#marker_listing_component_auto_open_wrap').fadeOut();
		}

		$('#marker_listing_component_anchor, input[name="wpgmza_listmarkers_by"]').on('change click', function() {
			if(parseInt($(this).val()) < 9 && parseInt(jQuery('input[name="wpgmza_listmarkers_by"]:checked').val()) === WPGMZA.MarkerListing.STYLE_PANEL){
				$('#marker_listing_component_auto_open_wrap').fadeIn();
			} else {
				$('#marker_listing_component_auto_open_wrap').fadeOut();
			}
		});


		if($('#wpgmza_show_user_location').prop('checked')){
	        $('#wpgmza_show_user_location_conditional').fadeIn();
	    }else{
	        $('#wpgmza_show_user_location_conditional').fadeOut();
	    }

	    $('#wpgmza_show_user_location').on('change', function(){
	        if($(this).prop('checked')){
	            $('#wpgmza_show_user_location_conditional').fadeIn();
	        }else{
	            $('#wpgmza_show_user_location_conditional').fadeOut();
	        }
	    });

	    if($('#wpgmza_store_locator_bounce').prop('checked')){
	        $('#wpgmza_store_locator_bounce_conditional').fadeIn();
	    }else{
	        $('#wpgmza_store_locator_bounce_conditional').fadeOut();
	    }

	    $('#wpgmza_store_locator_bounce').on('change', function(){
	        if($(this).prop('checked')){
	            $('#wpgmza_store_locator_bounce_conditional').fadeIn();
	        }else{
	            $('#wpgmza_store_locator_bounce_conditional').fadeOut();
	        }
	    });

	    

	    if($('#wpgmza_override_users_location_zoom_level').prop('checked')){
	        $('#wpgmza_override_users_location_zoom_levels_slider').fadeIn();
	    }else{
	        $('#wpgmza_override_users_location_zoom_levels_slider').fadeOut();
	    }

	    $('#wpgmza_override_users_location_zoom_level').on('change', function(){
	        if($(this).prop('checked')){
	            $('#wpgmza_override_users_location_zoom_levels_slider').fadeIn();
	        }else{
	            $('#wpgmza_override_users_location_zoom_levels_slider').fadeOut();
	        }
	    });

	    $('#override-users-location-zoom-levels-slider').slider({
			range: "max",
			min: 1,
			max: 21,
			value: $("input[name='override_users_location_zoom_levels']").val(),
			slide: function(event, ui){
				$("input[name='override_users_location_zoom_levels']").val(ui.value);
			}
		});

		if($('#zoom_level_on_marker_listing_override').prop('checked')){
	        $('#zoom_level_on_marker_listing_click_level').fadeIn();
	    }else{
	        $('#zoom_level_on_marker_listing_click_level').fadeOut();
	    }

	    $('#zoom_level_on_marker_listing_override').on('change', function(){
	        if($(this).prop('checked')){
	            $('#zoom_level_on_marker_listing_click_level').fadeIn();
	        }else{
	            $('#zoom_level_on_marker_listing_click_level').fadeOut();
	        }
	    });

	    $('#zoom-on-marker-listing-click-slider').slider({
			range: "max",
			min: 1,
			max: 21,
			value: $("input[name='zoom_level_on_marker_listing_click']").val(),
			slide: function(event, ui){
				$("input[name='zoom_level_on_marker_listing_click']").val(ui.value);
			}
		});
	      

		
		// NB: Workaround for bad DOM
		$("#open-route-service-key-notice").wrapInner("<div class='" + (!WPGMZA.InternalEngine.isLegacy() ? "wpgmza-pos-relative wpgmza-card wpgmza-shadow " : "") + "notice notice-error'><p></p></div>");

		$('#zoom-on-marker-click-slider').slider({
			range: "max",
			min: 1,
			max: 21,
			value: $("input[name='wpgmza_zoom_on_marker_click_slider']").val(),
			slide: function(event, ui){
				$("input[name='wpgmza_zoom_on_marker_click_slider']").val(ui.value);
			}
		});
		
		if($('#wpgmza_zoom_on_marker_click').prop('checked'))
			$('#wpgmza_zoom_on_marker_click_zoom_level').fadeIn();
		else
			$('#wpgmza_zoom_on_marker_click_zoom_level').fadeOut();

		$('#wpgmza_zoom_on_marker_click').on('change', function() {
			if($(this).prop('checked'))
				$('#wpgmza_zoom_on_marker_click_zoom_level').fadeIn();
			else
				$('#wpgmza_zoom_on_marker_click_zoom_level').fadeOut();
		});

		if($('#datatable_result').prop('checked'))
			$('#datable_strings').fadeIn();
		else
			$('#datable_strings').fadeOut();

		$('#datatable_result').on('change', function() {
			if($(this).prop('checked'))
				$('#datable_strings').fadeIn();
			else
				$('#datable_strings').fadeOut();
		});

		if($('#datatable_result_page').prop('checked'))
			$('#datable_strings_entries').fadeIn();
		else
			$('#datable_strings_entries').fadeOut();
		
		$('#datatable_result_page').on('change', function() {
			if($(this).prop('checked'))
				$('#datable_strings_entries').fadeIn();
			else
				$('#datable_strings_entries').fadeOut();
		});

		/* Bounds related issues */
		$('input[name="only_load_markers_within_viewport"]').on('change', function(){
			if($(this).prop('checked') && $('input[name="fit_maps_bounds_to_markers"]').prop('checked')){
				$('input[name="fit_maps_bounds_to_markers"]').prop('checked', false);

				WPGMZA.notification("Fit map bounds to markers has been disabled");
			}
		});

		$('input[name="fit_maps_bounds_to_markers"]').on('change', function(){
			if($(this).prop('checked') && $('input[name="only_load_markers_within_viewport"]').prop('checked')){
				$('input[name="only_load_markers_within_viewport"]').prop('checked', false);

				WPGMZA.notification("Only load markers within viewport has been disabled");
			}
		});

		/* Custom tile handlers */

		$('input[name="custom_tile_image"]').on('imagechange', function(event){
			if(event.currentTarget){
				self.onCustomTileImageChange(event.currentTarget);
			}
		});

		/* Streetview handlers */

		if($('input[name="map_starts_in_streetview"]').prop('checked')){
			$('.streetview-starting-editor').fadeIn();
		} else {
			$('.streetview-starting-editor').fadeOut();
		}

		$('input[name="map_starts_in_streetview"]').on('change', function(){
			if($(this).prop('checked')){
				$('.streetview-starting-editor').fadeIn();
				if(!self.streetViewEditor.hasLocation()){
					/* No location yet, go straight into add mode */
					self.streetViewEditor.setEditing(true);					
				}
			} else {
				$('.streetview-starting-editor').fadeOut();
			}
		});

		$(document.body).on('grouping-closed', function(event, grouping){
			if(grouping){
				if(grouping === 'map-settings-behaviour-streetview'){
					/* 
					 * The streetview editor panel has been closed
					 *
					 * We should stop all editing that may be active at the moment 
					*/

					self.streetViewEditor.setEditing(false);
				}
			}
		});
	}
	
	WPGMZA.extend(WPGMZA.ProMapEditPage, WPGMZA.MapEditPage);
	
	WPGMZA.ProMapEditPage.prototype.onStoreLocatorSearchAreaChanged = function(event){
		var value = $("input[name='store_locator_search_area']:checked").val();
		
		$("[data-search-area='" + value + "']").show();
		$("[data-search-area][data-search-area!='" + value + "']").hide();
	}

	WPGMZA.ProMapEditPage.prototype.onCustomTileImageChange = function(context){
		if(context instanceof HTMLInputElement){
			if($(context).val() === this.map.settings.custom_tile_image){
				return;
			}

			if(context.wpgmzaImageInputSingle && context.wpgmzaImageInputSingle.element){
				const imgRef = $(context.wpgmzaImageInputSingle.element).next('.wpgmza-image-single-input-preview').find('img');

				const imgDimensions = {
					width : parseInt(imgRef.prop("naturalWidth")),
					height : parseInt(imgRef.prop("naturalHeight"))
				};

				$('input[name="custom_tile_image_width"]').val(imgDimensions.width);
				$('input[name="custom_tile_image_height"]').val(imgDimensions.height);

				WPGMZA.notification("Custom Image width/height updated");
			}
		}
	}
	
});

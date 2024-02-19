/*
 * MAP FUNCTIONS
 */
var em_maps_loaded = false;
var maps = {};
var maps_markers = {};
var infoWindow;
//loads maps script if not already loaded and executes EM maps script
function em_maps_load(){
	if( !em_maps_loaded ){
		if ( jQuery('script#google-maps').length == 0 && ( typeof google !== 'object' || typeof google.maps !== 'object' ) ){
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.id = "google-maps";
			var proto = (EM.is_ssl) ? 'https:' : 'http:';
			if( typeof EM.google_maps_api !== 'undefined' ){
				script.src = proto + '//maps.google.com/maps/api/js?v=quarterly&libraries=places&callback=em_maps&key='+EM.google_maps_api;
			}else{
				script.src = proto + '//maps.google.com/maps/api/js?v=quarterly&libraries=places&callback=em_maps';
			}
			document.body.appendChild(script);
		}else if( typeof google === 'object' && typeof google.maps === 'object' && !em_maps_loaded ){
			em_maps();
		}else if( jQuery('script#google-maps').length > 0 ){
			jQuery(window).load(function(){ if( !em_maps_loaded ) em_maps(); }); //google isn't loaded so wait for page to load resources
		}
	}
}
jQuery(document).on('em_view_loaded_map', function( e, view, form ){
	if( !em_maps_loaded ){
		em_maps_load();
	}else{
		let map = view.find('div.em-locations-map');
		em_maps_load_locations( map );
	}
});
//re-usable function to load global location maps
function em_maps_load_locations(el){
	var el = jQuery(el);
	var map_id = el.attr('id').replace('em-locations-map-','');
	var em_data = jQuery.parseJSON( el.nextAll('.em-locations-map-coords').first().text() );
	if( em_data == null ){
		var em_data = jQuery.parseJSON( jQuery('#em-locations-map-coords-'+map_id).text() );
	}
	jQuery.getJSON(document.URL, em_data , function(data){
		if(data.length > 0){
			//define default options and allow option for extension via event triggers
			var map_options = { mapTypeId: google.maps.MapTypeId.ROADMAP };
			if( typeof EM.google_map_id_styles == 'object' && typeof EM.google_map_id_styles[map_id] !== 'undefined' ){ console.log(EM.google_map_id_styles[map_id]); map_options.styles = EM.google_map_id_styles[map_id]; }
			else if( typeof EM.google_maps_styles !== 'undefined' ){ map_options.styles = EM.google_maps_styles; }
			jQuery(document).triggerHandler('em_maps_locations_map_options', map_options);
			var marker_options = {};
			jQuery(document).triggerHandler('em_maps_location_marker_options', marker_options);

			maps[map_id] = new google.maps.Map(el[0], map_options);
			maps_markers[map_id] = [];

			var bounds = new google.maps.LatLngBounds();

			jQuery.map( data, function( location, i ){
				if( !(location.location_latitude == 0 && location.location_longitude == 0) ){
					var latitude = parseFloat( location.location_latitude );
					var longitude = parseFloat( location.location_longitude );
					var location_position = new google.maps.LatLng( latitude, longitude );
					//extend the default marker options
					jQuery.extend(marker_options, {
						position: location_position,
						map: maps[map_id]
					})
					var marker = new google.maps.Marker(marker_options);
					maps_markers[map_id].push(marker);
					marker.setTitle(location.location_name);
					var myContent = '<div class="em-map-balloon"><div id="em-map-balloon-'+map_id+'" class="em-map-balloon-content">'+ location.location_balloon +'</div></div>';
					em_map_infobox(marker, myContent, maps[map_id]);
					//extend bounds
					bounds.extend(new google.maps.LatLng(latitude,longitude))
				}
			});
			// Zoom in to the bounds
			maps[map_id].fitBounds(bounds);

			//Call a hook if exists
			jQuery(document).triggerHandler('em_maps_locations_hook', [maps[map_id], data, map_id, maps_markers[map_id]]);
		}else{
			el.children().first().html('No locations found');
			jQuery(document).triggerHandler('em_maps_locations_hook_not_found', [el]);
		}
	});
}
function em_maps_load_location(el){
	el = jQuery(el);
	var map_id = el.attr('id').replace('em-location-map-','');
	em_LatLng = new google.maps.LatLng( jQuery('#em-location-map-coords-'+map_id+' .lat').text(), jQuery('#em-location-map-coords-'+map_id+' .lng').text());
	//extend map and markers via event triggers
	var map_options = {
		zoom: 14,
		center: em_LatLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: false,
		gestureHandling: 'cooperative'
	};
	if( typeof EM.google_map_id_styles == 'object' && typeof EM.google_map_id_styles[map_id] !== 'undefined' ){ console.log(EM.google_map_id_styles[map_id]); map_options.styles = EM.google_map_id_styles[map_id]; }
	else if( typeof EM.google_maps_styles !== 'undefined' ){ map_options.styles = EM.google_maps_styles; }
	jQuery(document).triggerHandler('em_maps_location_map_options', map_options);
	maps[map_id] = new google.maps.Map( document.getElementById('em-location-map-'+map_id), map_options);
	var marker_options = {
		position: em_LatLng,
		map: maps[map_id]
	};
	jQuery(document).triggerHandler('em_maps_location_marker_options', marker_options);
	maps_markers[map_id] = new google.maps.Marker(marker_options);
	infoWindow = new google.maps.InfoWindow({ content: jQuery('#em-location-map-info-'+map_id+' .em-map-balloon').get(0) });
	infoWindow.open(maps[map_id],maps_markers[map_id]);
	maps[map_id].panBy(40,-70);

	//JS Hook for handling map after instantiation
	//Example hook, which you can add elsewhere in your theme's JS - jQuery(document).on('em_maps_location_hook', function(){ alert('hi');} );
	jQuery(document).triggerHandler('em_maps_location_hook', [maps[map_id], infoWindow, maps_markers[map_id], map_id]);
	//map resize listener
	jQuery(window).on('resize', function(e) {
		google.maps.event.trigger(maps[map_id], "resize");
		maps[map_id].setCenter(maps_markers[map_id].getPosition());
		maps[map_id].panBy(40,-70);
	});
}
jQuery(document).on('em_search_ajax', function(e, vars, wrapper){
	if( em_maps_loaded ){
		wrapper.find('div.em-location-map').each( function(index, el){ em_maps_load_location(el); } );
		wrapper.find('div.em-locations-map').each( function(index, el){ em_maps_load_locations(el); });
	}
});
//Load single maps (each map is treated as a seperate map).
function em_maps() {
	//Find all the maps on this page and load them
	jQuery('div.em-location-map').each( function(index, el){ em_maps_load_location(el); } );
	jQuery('div.em-locations-map').each( function(index, el){ em_maps_load_locations(el); } );

	//Location stuff - only needed if inputs for location exist
	if( jQuery('select#location-select-id, input#location-address').length > 0 ){
		var map, marker;
		//load map info
		var refresh_map_location = function(){
			var location_latitude = jQuery('#location-latitude').val();
			var location_longitude = jQuery('#location-longitude').val();
			if( !(location_latitude == 0 && location_longitude == 0) ){
				var position = new google.maps.LatLng(location_latitude, location_longitude); //the location coords
				marker.setPosition(position);
				var mapTitle = (jQuery('input#location-name').length > 0) ? jQuery('input#location-name').val():jQuery('input#title').val();
				mapTitle = em_esc_attr(mapTitle);
				marker.setTitle( mapTitle );
				jQuery('#em-map').show();
				jQuery('#em-map-404').hide();
				google.maps.event.trigger(map, 'resize');
				map.setCenter(position);
				map.panBy(40,-55);
				infoWindow.setContent(
					'<div id="location-balloon-content"><strong>' + mapTitle + '</strong><br>' +
					em_esc_attr(jQuery('#location-address').val()) +
					'<br>' + em_esc_attr(jQuery('#location-town').val()) +
					'</div>'
				);
				infoWindow.open(map, marker);
				jQuery(document).triggerHandler('em_maps_location_hook', [map, infoWindow, marker, 0]);
			} else {
				jQuery('#em-map').hide();
				jQuery('#em-map-404').show();
			}
		};

		//Add listeners for changes to address
		var get_map_by_id = function(id){
			if(jQuery('#em-map').length > 0){
				jQuery('#em-map-404 .em-loading-maps').show();
				jQuery.getJSON(document.URL,{ em_ajax_action:'get_location', id:id }, function(data){
					if( data.location_latitude!=0 && data.location_longitude!=0 ){
						loc_latlng = new google.maps.LatLng(data.location_latitude, data.location_longitude);
						marker.setPosition(loc_latlng);
						marker.setTitle( data.location_name );
						marker.setDraggable(false);
						jQuery('#em-map').show();
						jQuery('#em-map-404').hide();
						jQuery('#em-map-404 .em-loading-maps').hide();
						map.setCenter(loc_latlng);
						map.panBy(40,-55);
						infoWindow.setContent( '<div id="location-balloon-content">'+ data.location_balloon +'</div>');
						infoWindow.open(map, marker);
						google.maps.event.trigger(map, 'resize');
						jQuery(document).triggerHandler('em_maps_location_hook', [map, infoWindow, marker, 0]);
					}else{
						jQuery('#em-map').hide();
						jQuery('#em-map-404').show();
						jQuery('#em-map-404 .em-loading-maps').hide();
					}
				});
			}
		};
		jQuery('#location-select-id, input#location-id').on('change', function(){get_map_by_id(jQuery(this).val());} );
		jQuery('#location-name, #location-town, #location-address, #location-state, #location-postcode, #location-country').on('change', function(){
			//build address
			if( jQuery(this).prop('readonly') === true ) return;
			var addresses = [ jQuery('#location-address').val(), jQuery('#location-town').val(), jQuery('#location-state').val(), jQuery('#location-postcode').val() ];
			var address = '';
			jQuery.each( addresses, function(i, val){
				if( val != '' ){
					address = ( address == '' ) ? address+val:address+', '+val;
				}
			});
			if( address == '' ){ //in case only name is entered, no address
				jQuery('#em-map').hide();
				jQuery('#em-map-404').show();
				return false;
			}
			//do country last, as it's using the text version
			if( jQuery('#location-country option:selected').val() != 0 ){
				address = ( address == '' ) ? address+jQuery('#location-country option:selected').text():address+', '+jQuery('#location-country option:selected').text();
			}
			//add working indcator whilst we search
			jQuery('#em-map-404 .em-loading-maps').show();
			//search!
			if( address != '' && jQuery('#em-map').length > 0 ){
				geocoder.geocode( { 'address': address }, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						jQuery('#location-latitude').val(results[0].geometry.location.lat());
						jQuery('#location-longitude').val(results[0].geometry.location.lng());
					}
					refresh_map_location();
				});
			}
		});

		//Load map
		if(jQuery('#em-map').length > 0){
			var em_LatLng = new google.maps.LatLng(0, 0);
			var map_options = {
				zoom: 14,
				center: em_LatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: false,
				gestureHandling: 'cooperative'
			};
			if( typeof EM.google_maps_styles !== 'undefined' ){ map_options.styles = EM.google_maps_styles; }
			map = new google.maps.Map( document.getElementById('em-map'), map_options);
			var marker = new google.maps.Marker({
				position: em_LatLng,
				map: map,
				draggable: true
			});
			infoWindow = new google.maps.InfoWindow({
				content: ''
			});
			var geocoder = new google.maps.Geocoder();
			google.maps.event.addListener(infoWindow, 'domready', function() {
				document.getElementById('location-balloon-content').parentNode.style.overflow='';
				document.getElementById('location-balloon-content').parentNode.parentNode.style.overflow='';
			});
			google.maps.event.addListener(marker, 'dragend', function() {
				var position = marker.getPosition();
				jQuery('#location-latitude').val(position.lat());
				jQuery('#location-longitude').val(position.lng());
				map.setCenter(position);
				map.panBy(40,-55);
			});
			if( jQuery('#location-select-id').length > 0 ){
				jQuery('#location-select-id').trigger('change');
			}else{
				refresh_map_location();
			}
			jQuery(document).triggerHandler('em_map_loaded', [map, infoWindow, marker]);
		}
		//map resize listener
		jQuery(window).on('resize', function(e) {
			google.maps.event.trigger(map, "resize");
			map.setCenter(marker.getPosition());
			map.panBy(40,-55);
		});
	}
	em_maps_loaded = true; //maps have been loaded
	jQuery(document).triggerHandler('em_maps_loaded');
}

function em_map_infobox(marker, message, map) {
	var iw = new google.maps.InfoWindow({ content: message });
	google.maps.event.addListener(marker, 'click', function() {
		if( infoWindow ) infoWindow.close();
		infoWindow = iw;
		iw.open(map,marker);
	});
}

function em_esc_attr( str ){
	if( typeof str !== 'string' ) return '';
	return str.replace(/</gi,'&lt;').replace(/>/gi,'&gt;');
}
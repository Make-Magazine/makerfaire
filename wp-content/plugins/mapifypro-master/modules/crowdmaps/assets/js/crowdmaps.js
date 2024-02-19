;(function($, $window, $document){

function releaseCrowdMarker( map ) {
	var marker = map.crowdMaps.marker;

	marker.off( 'mouseover' );
	marker.off( 'click' );
	marker.off( 'contextmenu' );

	marker.on( 'mouseover', function(e) {
		map.mapService.showTooltip( map.crowdMaps.submittedTooltip, marker.getLatLng(), map.crowdMaps.tooltipAnchor );
	} );

	marker.on( 'mouseout', function(e) {
		map.crowdMaps.submittedTooltip.hide();
	} );

	map.crowdMaps.marker = null;
}

$( document ).on( 'mapify.map.loaded', '.mpfy-container', function(e, map) {
	if ( ! map.$container.hasClass( 'crowdmaps-enabled-map' ) ) {
		return;
	}

	map.crowdMaps = {
		marker: null,
		tooltip: new MapifyTooltip( {
			'rgba': [255, 255, 255, 1],
			'content': '<div class="mpfy-tooltip-content"><p>' + crowdMapsStrings.marker_created_content + '</p><a href="#" class="crowd-tooltip-button">' + crowdMapsStrings.marker_created_button + '</a></div>',
			'close_behavior': 'manual',
			'animate': true
		}),
		submittedTooltip: new MapifyTooltip( {
			'rgba': [255, 255, 255, 1],
			'content': '<div class="mpfy-tooltip-content"><p style="text-align: center;">' + crowdMapsStrings.marker_submitted_content + '</p></div>',
			'close_behavior': 'auto',
			'animate': true
		} )
	};

	var tooltipClasses = 'mpfy-tooltip mpfy-tooltip-map-' + map.settings.map.id + ' mpfy-tooltip-image-orientation-left crowd-tooltip';
	map.crowdMaps.tooltip.node().addClass( tooltipClasses );
	map.crowdMaps.submittedTooltip.node().addClass( tooltipClasses );

	map.crowdMaps.tooltip.node().find( '.crowd-tooltip-button:first' ).click( function(e) {
		e.preventDefault();
		map.crowdMaps.tooltip.hide();
		
		var position = map.crowdMaps.marker.getLatLng();
		var url = window.wp_ajax_url + '?action=crowd_add_location_form&map_id=' + map.settings.map.id + '&location=' + position.lat + ',' + position.lng + '&current_post_id=' + crowdMapsStrings.post_id;
				
		Mapify.openPopup( url, function() {
			var $form = $( 'form.crowd-add-location-form:first' );
			var nonce = $form.find( 'input[name="_crowd_ajax_nonce"]' ).first().val();
			var uploadUrl = window.wp_ajax_url + '?action=crowd_add_location_image&_crowd_ajax_nonce=' + nonce;
			
			$form.data( 'map', map );
			$form.find( 'textarea[name="location_description"]' ).first().redactor( {
				buttons: ['bold','italic', 'link']
			} );
		
			$( '.crowd-file-upload-trigger' ).each( function() {
				var $this = $(this);
				var $wrapper = $this.closest( '.crowd-file-upload' );
				var $errorNotification = $( '.crowd-popup-error-notification' );

				$errorNotification.hide();

				var uploader = new plupload.Uploader( {
					runtimes : 'html5,flash,silverlight,html4',
					browse_button: $this.get( 0 ),
					url: uploadUrl,
					multi_selection: false,
					init: {
						StateChanged: function(up) {
							if (up.state == plupload.STARTED) {
								$wrapper.block( { message: null } );
							} else {
								$wrapper.unblock();
							}
						},

						FilesAdded: function(up, files) {
							// start next frame; needed or doesn't actually send the file
							setTimeout(function(){
								up.start();
							});
						},

						FileUploaded: function(up, file, resp) {
							try {
								var response = JSON.parse(resp.response);
								$wrapper.find('.crowd-file-upload-image:first').html( response.data.img ).show();
								$wrapper.find('input[name="location_image[]"]:first').val( response.data.id );
							} catch (e) {
								$errorNotification.html('There was a problem with your upload. Please try a different file.').show();
							}
						},

						Error: function(up, error) {
							$errorNotification.html(error.message).show();
						}
					}
				} );

				uploader.init();
			} );
		} );
	} );

	var markerOptions = {
		'draggable': true,
		'riseOnHover': true
	};
	map.crowdMaps.tooltipAnchor = [0, -50];
	var showTooltip = function(e) {
		map.mapService.showTooltip( map.crowdMaps.tooltip, map.crowdMaps.marker.getLatLng(), map.crowdMaps.tooltipAnchor );
	};
	Mapify.Promise.resolve()
		.then( function() {
			if (map.settings.map.pinImage) {
				return Mapify.preloadImage( map.settings.map.pinImage )
					.then( function( image ) {
						map.crowdMaps.tooltipAnchor[1] = -image.height - 10;
						markerOptions.icon = L.icon( {
							'iconUrl': map.settings.map.pinImage,
							'iconAnchor': [image.width / 2, image.height]
						} );
					} );
			}
			return Mapify.Promise.resolve();
		} )
		.then( function() {
			var clicks = 0;
			map.mapService.map.on('click', function(e) {
				var latLng = e.latlng;
				clicks++;

				setTimeout(function() {
					clicks = 0;
				}, 500);

				if (clicks >= 2) {
					clicks = 0;
					handleDoubleClick(latLng);
				}
			});

			function handleDoubleClick( latLng ) {
				map.crowdMaps.tooltip.hide();
				if ( map.crowdMaps.marker !== null ) {
					map.crowdMaps.marker.setLatLng( latLng );
					showTooltip();
					return;
				}

				map.crowdMaps.marker = L.marker( latLng, markerOptions );
				map.mapService.map.addLayer( map.crowdMaps.marker );

				map.mapService.map.on( 'movestart', function() {
					map.crowdMaps.tooltip.hide();
				} );
				map.mapService.map.on( 'moveend', function() {
					map.crowdMaps.tooltip.hide();
				} );
				map.mapService.map.on( 'zoomstart', function() {
					map.crowdMaps.tooltip.hide();
				} );
				map.mapService.map.on( 'zoomend', function() {
					map.crowdMaps.tooltip.hide();
				} );
				map.mapService.map.on( 'viewreset', function() {
					map.crowdMaps.tooltip.hide();
				} );

				map.crowdMaps.marker.on( 'mouseover', showTooltip );
				map.crowdMaps.marker.on( 'click', showTooltip );
				map.crowdMaps.marker.once( 'contextmenu', function(e) {
					map.crowdMaps.tooltip.hide();
					map.mapService.map.removeLayer( map.crowdMaps.marker );
					map.crowdMaps.marker.off( 'mouseover' );
					map.crowdMaps.marker.off( 'click' );
					map.crowdMaps.marker = null;
				} );
				showTooltip();
			}
		} );
});

$(document).on('submit', 'form.crowd-add-location-form', function(e){
	e.preventDefault();
	var $form = $(this);
	var $popupContent = $('.mpfy-p-scroll:visible:first');
	var $errorNotification = $( '.crowd-popup-error-notification' );

	$errorNotification.hide();

	$popupContent.block({message: null});

	$form.ajaxSubmit(function(resp){
		$popupContent.unblock();
		
		var response = JSON.parse(resp);
		
		if (response.success) {
			var map = $form.data( 'map' );
			releaseCrowdMarker( map );
			var url = window.wp_ajax_url + '?action=crowd_location_thank_you&map_id=' + map.settings.map.id + '&new_user_id=' + response.new_user_id;
			Mapify.openPopup(url);
		} else {
			$errorNotification.html(response.data).show();
		}
	});
});

$(document).on('click', '.crowd-btn-close', function(e){
	e.preventDefault();
	Mapify.closePopup();
})

})(jQuery, jQuery(window), jQuery(document));
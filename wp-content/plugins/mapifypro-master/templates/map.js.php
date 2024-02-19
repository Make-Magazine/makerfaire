<script type="text/javascript">
(function($, $window, $document){

$document.ready(function(){
	var settings = {
		map: {
			id: <?php echo json_encode( $map_id ); ?>,
			type: <?php echo json_encode( $map_type ); ?>,
			mode: <?php echo json_encode( $mode ); ?>,
			center: <?php echo json_encode( $center ); ?>,
			tileset: <?php echo json_encode( $tileset['url'] ); ?>,
			pinImage: <?php echo json_encode( $map_default_pin_image ); ?>,			
			enableUseMyLocation: <?php echo json_encode( $map_enable_use_my_location ); ?>,
			background: <?php echo json_encode( $map_background_color ); ?>
		},
		zoom: {
			enabled: <?php echo json_encode( (bool) $zoom_enabled ); ?>,
			manual_enabled: <?php echo json_encode( (bool) $manual_zoom_enabled ); ?>,
			zoom: <?php echo json_encode( intval( $zoom_level ) ); ?>
		},
		pins: {
			pins: <?php echo json_encode($pins); ?>,
			hideInitially: <?php echo json_encode( $pins_hide_initially ); ?>
		},
		cluster: {
			enabled: <?php echo json_encode( $clustering_enabled ); ?>
		},
		tooltip: {
			imageOrientation: <?php echo json_encode( $tooltip_image_orientation ) ?>,
			background: <?php echo json_encode( $tooltip_background ); ?>
		},
		search: {
			centerOnSearch: <?php echo json_encode( $search_center ); ?>,
			radiusUnitName: <?php echo json_encode( $search_radius_unit_name ); ?>,
			radiusUnit: <?php echo json_encode( $search_radius_unit ); ?>,
			radius: <?php echo json_encode( $search_radius ); ?>,
			regionBias: <?php echo json_encode( $search_region_bias ); ?>,
			locationCircleColor: <?php echo json_encode( $location_circle_color ); ?>,
			labelSearchSecond: <?php echo json_encode( $label_search_second ); ?>
		},
		filters: {
			centerOnFilter: <?php echo json_encode( $filters_center ); ?>
		},
		routes: <?php echo json_encode($routes); ?>,
		crowdmap_enabled: <?php echo json_encode($crowdmap_enabled); ?>,
	};

	var $mapContainer = $( '#mpfy-map-<?php echo $mpfy_instances; ?>' );
	var map = new Mapify.Map( $mapContainer, settings );

	function focusPin( pinId, openTooltip ) {
		var pin = map.getPinById( parseInt( pinId ) );
		if ( ! pin ) {
			return;
		}
		if ( openTooltip ) {
			map.mapService.highlightPin( pin );
		} else {
			setTimeout(function(){
				jQuery(document).trigger( 'mapify.action.openPopup', {
					value: pin.model.id
				} );
			}, 1);
		}
	}

	<?php if (isset($_GET['mpfy-pin'])) : ?>
		map.$container.on( 'mapify.map.loaded', function() {
			focusPin( <?php echo intval( $_GET['mpfy-pin'] ); ?>, <?php echo json_encode( isset( $_GET['mpfy-tooltip'] ) ); ?> );
		} );
	<?php endif; ?>

	// Map areas
	<?php if ( $map_areas ) : ?>
		var map_areas  = <?php echo json_encode( $map_areas ); ?>;
		var map_object = map.mapService.map;

		map_areas.forEach(element => {
			// parse coordinates string data to array
			element.coordinates = JSON.parse(element.coordinates);

			// polygon options
			var polygon_options = {
				color       : element.border_color, 
				fillColor   : element.fill_color, 
				fillOpacity : element.fill_opacity, 
			};
			
			// add new updated polygon
			var polygon = L.polygon(element.coordinates, polygon_options).addTo(map_object);

			// add tooltip to polygon
			if ( '' !== $.trim( element.description ) || element.image_url ) {
				var tooltip_html = "<div style='max-width:250px;width:250px;'>";
			
				// image
				if ( element.image_url ) {
					tooltip_html += "<img src='" + element.image_url + "' style='width:100%;max-width:100%;'>";
				}

				// description
				if ( '' !== $.trim( element.description ) ) {
					tooltip_html += "<div style='white-space:normal;'>" + element.description + "</div>";
				}

				tooltip_html += "</div>";

				// add tooltip to the map area
				polygon.bindTooltip( tooltip_html, {
					sticky    : true,
					direction : 'left',
				} ).addTo( map_object );
			}
		} );
	<?php endif; ?>

});

})(jQuery, jQuery(window), jQuery(document));
</script>

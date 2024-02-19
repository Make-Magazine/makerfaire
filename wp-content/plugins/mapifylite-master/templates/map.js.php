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
			pins: <?php echo json_encode($pins); ?>
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
			regionBias: <?php echo json_encode( $search_region_bias ); ?>
		},
		filters: {
			centerOnFilter: <?php echo json_encode( $filters_center ); ?>
		},
		routes: <?php echo json_encode($routes); ?>,
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
			$document.trigger( 'mapify.action.openPopup', {
				value: pin.model.id
			} );
		}
	}

	<?php if (isset($_GET['mpfy-pin'])) : ?>
		map.$container.on( 'mapify.map.loaded', function() {
			focusPin( <?php echo intval( $_GET['mpfy-pin'] ); ?>, <?php echo json_encode( isset( $_GET['mpfy-tooltip'] ) ); ?> );
		} );
	<?php endif; ?>

});

})(jQuery, jQuery(window), jQuery(document));
</script>

<?php

/**
 * Print the Google Search Results JSON data
 * Parent template: /templates/map.php
 */

$locations  = $map->get_locations( false );

foreach ( $locations as $location ) {
	$map_location = new \Acf_Mapifypro\Model\Mapify_Map_Location( $location->ID );
	$location_grs = $map_location->get_grs_attributes();

	if ( $location_grs ) :
		?>
		
		<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "<?php echo esc_js( $location_grs['type'] ) ?>",
			"image": <?php echo json_encode( $location_grs['image'] ) ?>,
			"name": "<?php echo esc_js( $location_grs['name'] ) ?>",
			"address": {
				"@type": "PostalAddress",
				"streetAddress": "<?php echo esc_js( $location_grs['streetAddress'] ) ?>",
				"addressLocality": "<?php echo esc_js( $location_grs['addressLocality'] ) ?>",
				"addressRegion": "<?php echo esc_js( $location_grs['addressRegion'] ) ?>",
				"postalCode": "<?php echo esc_js( $location_grs['postalCode'] ) ?>",
				"addressCountry": "<?php echo esc_js( $location_grs['addressCountry'] ) ?>"
			},
			"geo": {
				"@type": "GeoCoordinates",
				"latitude": <?php echo esc_js( $location_grs['latitude'] ) ?>,
				"longitude": <?php echo esc_js( $location_grs['longitude'] ) ?>
			},
			"url": "<?php echo esc_url( $location_grs['url'] ) ?>",
			"telephone": "<?php echo esc_js( $location_grs['telephone'] ) ?>",
			"priceRange": "<?php echo esc_js( $location_grs['priceRange'] ) ?>"
		}
		</script>

		<?php
	endif;
}
?>
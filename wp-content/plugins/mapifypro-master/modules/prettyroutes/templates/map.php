<?php
$errors = array();
$wp_routes = get_posts('post_type=route&posts_per_page=-1&meta_key=_route_map&meta_value=' . $map->ID);

$routes = array();
foreach ($wp_routes as $index => $r) {
	$routes[] = PrettyRoutes_Route::load( $r->ID );
}

$center_mode = carbon_get_post_meta($map->ID, 'route_map_center_option');
$center_mode = $center_mode ? $center_mode : 'auto';

$json = array(
	'center' => array(
		get_post_meta( $map->ID, '_route_map_center-lat', true ),
		get_post_meta( $map->ID, '_route_map_center-lng', true ),
	),
	'centerMode' => $center_mode,
	'zoom' => intval( get_post_meta( $map->ID, '_route_map_center-zoom', true ) ),
	'routes' => $routes,
);
?>
<div id="routes-route-map-<?php echo $routes_instances; ?>" class="prettyroutes-map" data-json="<?php echo esc_attr( json_encode( $json ) ); ?>">
	<?php if ($errors) : ?>
		<p>
			<?php foreach ($errors as $e) : ?>
				<?php echo $e; ?><br />
			<?php endforeach; ?>
		</p>
	<?php else : ?>
		<div class="cl">&nbsp;</div>
		<div class="routes-route-map">
			<div class="prettyroutes-map-canvas" style="<?php echo ($width) ? 'width: ' . $width . 'px;' : ''; ?> height: <?php echo $height; ?>px; overflow: hidden;"></div>
		</div>
	<?php endif; ?>
</div>

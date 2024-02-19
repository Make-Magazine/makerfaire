<?php
function mpfy_og_single_pin_meta() {
	if (!isset($_GET['mpfy-pin'])) {
		return false;
	}
	ob_start();
}
add_action('wp_head', 'mpfy_og_single_pin_meta', -1000);

function mpfy_og_single_pin_meta_flush() {
	global $wp;
	if (!isset($_GET['mpfy-pin'])) {
		return false;
	}
	$html = ob_get_clean();

	// remove og:url
	$html = preg_replace('/<meta\s[^>]*property=[\'"]og:url[\'"].*?>/', '', $html);

	// remove og:description
	$html = preg_replace('/<meta\s[^>]*property=[\'"]og:description[\'"].*?>/', '', $html);

	// remove canonical
	$html = preg_replace('/<link\s[^>]*rel=[\'"]canonical[\'"].*?>/', '', $html);

	$pin_id = intval($_GET['mpfy-pin']);
	$map_location = new Mpfy_Map_Location($pin_id);
	$images = $map_location->get_gallery_images();
	$content = $map_location->get_content();

	$url = home_url('?' . $_SERVER['QUERY_STRING']);
	?>
	<meta property="og:title" content="<?php echo esc_attr($map_location->get_title()); ?>" />
	<meta property="og:description" content="<?php echo esc_attr(strip_tags($content)); ?>" />
	<?php foreach ($images as $img) : ?>
		<meta property="og:image" content="<?php echo $img['image']; ?>" />
	<?php endforeach; ?>

	<meta property="og:url" content="<?php echo $url; ?>" />
	<link rel='canonical' href='<?php echo $url; ?>' />
	<?php
	echo $html;
}
add_action('wp_head', 'mpfy_og_single_pin_meta_flush', 1000);

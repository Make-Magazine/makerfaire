<?php
if (!isset($_GET['mpfy-pin'])) {
	wp_redirect(add_query_arg('mpfy-pin', get_the_ID()));
	exit;
}
?>
<?php get_header('mapify'); ?>

<?php echo do_shortcode('[custom-mapping map_id="' . $map_id . '" height="400"]'); ?>

<?php get_footer('mapify'); ?>
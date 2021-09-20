<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );

get_header( 'shop' ); ?>
<?php
$dce_default_options = get_option( DCE_OPTIONS );
//
$dce_elementor_templates = 'dyncontel_field_singleproduct';
$dce_default_template = $dce_default_options[ $dce_elementor_templates ];
?>

	<?php
	do_action( 'woocommerce_before_single_product' );
	$woocontent = \Elementor\Plugin::instance()->frontend->get_builder_content( $dce_default_template );
	echo $woocontent;
	do_action( 'woocommerce_after_single_product' );
	?>
	<?php while ( have_posts() ) :
		the_post(); ?>

	<?php endwhile; // end of the loop. ?>

<?php get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

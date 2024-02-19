<?php
$services = array(
	'facebook'  => __( 'Share', 'mpfy' ),
	'pinterest' => __( 'Pin', 'mpfy' ),
	'twitter'   => __( 'Tweet', 'mpfy' ),
	'linkedin'  => __( 'Share', 'mpfy' ),
	'whatsapp'  => __( 'Share', 'mpfy' ),
	'email'     => __( 'Email', 'mpfy' ),
);

$share_url        = add_query_arg( 'mpfy-pin', $post_id, $_SERVER['HTTP_REFERER'] );
$enabled_services = get_post_meta( $post_id, '_map_location_share', true );
$enabled_services = $enabled_services ? $enabled_services : array();

if ( empty( $enabled_services ) ) {
	return;
}

?>

<div class="mpfy-p-social">
	<?php 
	do_action('mpfy_share_links_before', $post_id); 

	foreach ( $services as $service => $service_label ) {
		if ( in_array( $service, $enabled_services ) ) {
			?>
		
			<div 
				class        = "<?php echo esc_attr( $service ) ?> mpfy-social-btn st-custom-button" 
				data-network = "<?php echo esc_attr( $service ) ?>" 
				data-url     = "<?php echo esc_attr( $share_url ); ?>"
			><span><?php echo esc_html( $service_label ) ?></span></div>
	
			<?php 
		}
	}
	
	do_action('mpfy_share_links_after', $post_id); 
	?>
</div>

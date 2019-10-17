<?php
/**
 * The template part for GravityView Maps display icon image tag
 *
 * @package GravityView_Maps
 * @since 0.1.1
 *
 */
?>

<?php
foreach( $sections as $k => $section ) : ?>

	<div>
		<h4><?php echo esc_html( $section ); ?></h4>
		<?php foreach( $icons[ $k ] as $icon ) : ?>
			<img class="gv_maps_icons" src="<?php echo esc_url( $icon ); ?>">
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
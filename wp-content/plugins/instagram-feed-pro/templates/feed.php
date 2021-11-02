<?php
/**
 * Custom Feeds for Instagram Main Template
 * Creates the wrapping HTML for all feed parts
 *
 * @version 5.12 Custom Feeds for Instagram Pro by Smash Balloon
 *
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$feed_styles = SB_Instagram_Display_Elements_Pro::get_feed_style( $settings ); // already escaped
$sb_images_style = SB_Instagram_Display_Elements_Pro::get_sbi_images_style( $settings ); // already escaped
$image_resolution_setting = $settings['imageres'];
$cols_setting = $settings['cols'];
$colsmobile_setting = $settings['colsmobile'];
$mobilecols_class = SB_Instagram_Display_Elements_Pro::get_mobilecols_class( $settings );
$num_setting = $settings['num'];
$nummobile_setting = $settings['nummobile'];
$icon_type = 'svg';

/**
 * Add HTML or execute code before the feed displays.
 * sbi_after_feed works the same way but executes
 * after the feed
 *
 * @param array $posts Instagram posts in feed
 * @param array $settings settings specific to this feed
 *
 * @since 5.3
 */
do_action( 'sbi_before_feed', $posts, $settings );

if ( $settings['showheader'] && ! empty( $header_data ) && $settings['headeroutside'] ) {
	include sbi_get_feed_template_part( 'header', $settings );
}
?>

<div id="sb_instagram" class="sbi <?php echo esc_attr( $mobilecols_class ); ?> sbi_col_<?php echo esc_attr( $cols_setting ); ?> <?php echo esc_attr( $additional_classes ); ?>"<?php echo $feed_styles; ?> data-feedid="<?php echo esc_attr( $feed_id ); ?>" data-res="<?php echo esc_attr( $image_resolution_setting ); ?>" data-cols="<?php echo esc_attr( $cols_setting ); ?>" data-colsmobile="<?php echo esc_attr( $colsmobile_setting ); ?>" data-num="<?php echo esc_attr( $num_setting ); ?>" data-nummobile="<?php echo esc_attr( $nummobile_setting ); ?>" data-shortcode-atts="<?php echo esc_attr( $shortcode_atts ); ?>" <?php echo $other_atts; ?>>
	<?php
	if ( $settings['showheader'] && ! empty( $header_data ) && !$settings['headeroutside'] ) {
		include sbi_get_feed_template_part( 'header', $settings );
	}
	?>

    <div id="sbi_images" <?php echo $sb_images_style; ?>>
		<?php
        if ( ! in_array( 'ajaxPostLoad', $flags, true ) ) {
	        $this->posts_loop( $posts, $settings );
        }
		?>
    </div>

	<?php include sbi_get_feed_template_part( 'footer', $settings ); ?>

	<?php
	/**
	 * Things to add before the closing "div" tag for the main feed element. Several
     * features rely on this hook such as local images and some error messages
	 *
	 * @param object SB_Instagram_Feed
	 * @param string $feed_id
     *
	 * @since 2.1/5.2
	 */
    do_action( 'sbi_before_feed_end', $this, $feed_id ); ?>

</div>

<?php do_action( 'sbi_after_feed', $posts, $settings );?>
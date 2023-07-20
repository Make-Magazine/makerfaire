<?php
/**
 * The template for ratings-reviews meta box in edit View screen.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;
?>

<table class="form-table striped">
	<?php
	do_action( 'gravityview_metabox_ratings_reviews_before', $current_settings );

	GravityView_Render_Settings::render_setting_row( 'allow_entry_reviews', $current_settings );
	GravityView_Render_Settings::render_setting_row( 'entry_review_type', $current_settings );
	GravityView_Render_Settings::render_setting_row( 'limit_one_review_per_person', $current_settings );
	GravityView_Render_Settings::render_setting_row( 'hide_ratings', $current_settings );
	GravityView_Render_Settings::render_setting_row( 'allow_empty_reviews', $current_settings );

	do_action( 'gravityview_metabox_ratings_reviews_after', $current_settings );
	?>
</table>

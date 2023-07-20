<?php
/**
 * The template for edit meta box in edit comment screen.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 * @global array $fields GravityView Ratings & Reviews comment metadata fields
 */

defined( 'ABSPATH' ) || exit;
?>

<table class="form-table">
	<?php foreach ( $fields as $name => $field ) : ?>
		<tr>
			<td>
				<?php echo apply_filters( "comment_form_field_{$name}", $field ) . "\n"; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

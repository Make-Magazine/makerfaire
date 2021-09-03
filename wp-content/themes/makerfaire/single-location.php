<?php get_header(); ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">

			<?php
				$faire = ( isset( $_GET['faire'] ) && ! empty( $_GET['faire'] ) ) ? sanitize_title( $_GET['faire'] ) : '';				
			?>

		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>
<?php
function essb5_generate_design_preview($content = '') {
	$design = isset($_REQUEST['design']) ? $_REQUEST['design'] : '';

	$content = essb_shortcode_subscribe(array('design' => $design));

	return $content;
}
?>

<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta http-equiv="content-type" content="<?php bloginfo( 'html_type' ); ?>" charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
		<title>Subscribe Form Preview</title>
		<?php wp_head(); ?>
		
		<style type="text/css">		
		.form-preview .form-content {
			width: 100%;
			max-width: 800px;
			margin: 0 auto;	
		}
		
		</style>

	</head>

	<body <?php body_class(); ?>>
		<div style="display: none;">
			<?php get_template_part('single'); ?>
		</div>
		<div class="form-preview">
	
			<div class="form-content">
				<?php echo essb5_generate_design_preview(); ?>
			</div>
		</div>
	
	 <?php wp_footer(); ?>
	        
	</body>
</html>
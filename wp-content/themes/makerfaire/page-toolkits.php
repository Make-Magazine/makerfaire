<?php
/*
Template name: Toolkits
*/
get_header(); ?>

<?php 

$path = $_SERVER['REQUEST_URI'];
$urlArray = explode('/', $path);
$location = $urlArray[1];
$pagegroup = $urlArray[2];
$pagegroupPretty = ucwords(str_replace("-"," ", $pagegroup));


$tabArray = [];
$query = new WP_Query( array(
    'post_type'  => 'any',
	 'orderby'   => 'meta_value',
    'order' => 'DESC',
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'page-toolkits.php'
) );

if ( $query->have_posts() ) {
    while ( $query->have_posts() ) : $query->the_post(); // WP loop
	     if(strpos(get_the_permalink(), $pagegroup) !== false){
        	  array_push($tabArray, get_the_title());
		  }
    endwhile; // end of the loop.
}

wp_reset_query();

?>


<div class="clear"></div>
<div class="main-content" id="main">

  <div class="toolkit-header container-fluid">
	<div class="row">
		<div class="toolkit-header-title col-md-3 col-sm-4 col-xs-12">
			<img src="/wp-content/themes/makerfaire/images/toolkit-icon.png" width="40px" height="40px" />
			<h1><?php echo($pagegroupPretty); ?></h1>
		</div>
		<div class="toolkit-tabs col-md-9 col-sm-8 col-xs-12">
			<ul class="nav nav-tabs">
				<?php 
					foreach($tabArray as $value) {
						echo('<li><a href="' . strtolower(str_replace(" ","-", $value)) . '" ' . ($value == get_the_title() ? ' class="active"' : '') . '>' . $value . '</a></li>');
					}
				?>
			</ul>
		</div>
		<?php /*<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php	
				if (have_rows('top_tabs')) { 
					echo('<div class="toolkit-tabs col-md-9 col-sm-8 col-xs-12"><ul class="nav nav-tabs">');
					while (have_rows('top_tabs')) {
						the_row();
						echo('<li><a href="' . get_sub_field('tab_link') . '">' . get_sub_field('tab_text') . '</a></li>');
					}
					echo('</ul></div>');
				}
		?>
		<?php endwhile; ?>			
		<?php endif; ?> */ ?>
	</div>
  </div>
  <div class="page-leftnav container-fluid">
	<div class="row">
      <div class="col-md-3 left-hand-nav">
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php	
				if (have_rows('sections')) { 
					echo('<ul class="toolkit-nav menu" id="menu-toolkit-left-hand-nav">');
						while (have_rows('sections')) {
							the_row();
							if(get_sub_field('section_type') == "Header") {
								 $leftLinkClass = "section-header";
							}else{
								 $leftLinkClass = "sub-section-header";
							}
							echo('<li class="' . $leftLinkClass . '"><a href="#' .  strtolower(str_replace(" ","-",get_sub_field('header_text'))) . '">' . get_sub_field('header_text') . '</a></li>');
						}
			      echo('</ul>');
				}
			?>
			<?php endwhile; ?>			
			<?php endif; ?>
      </div>
		<div class="content col-md-9">			
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php	
				if (have_rows('sections')) { 
					echo('<div class="toolkit-section-wrapper">');
						while (have_rows('sections')) {
							the_row();
							if(get_sub_field('section_type')) {
								echo('<div class="toolkit-section image_grid">'); //image grid is here for the list styles until it's made universal
									 echo('<a class="toolkit-anchor" name="' . strtolower(str_replace(" ","-",get_sub_field('header_text'))) . '"></a>');
									 echo('<h2>' .  get_sub_field('header_text') . '</h2>');
									 echo(get_sub_field('section_body'));
								echo('</div>');
							}
						}
			      echo('</div>');
				}
			?>
         <?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
	<div class="row">
		<div class="col-sm-12 text-center back-to-top">
			<a href="#topofpage">BACK TO TOP</a>
		</div>
	</div>
  </div><!--Container-->
</div>
<?php get_footer(); ?>
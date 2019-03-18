<?php
/*
Template name: Toolkits
*/
get_header(); ?>

<?php 

$path = $_SERVER['REQUEST_URI'];
$urlArray = explode('/', $path);
$location = $urlArray[1];
$pagegroup = $urlArray[2]; // This is the parent, i.e. Maker Toolkit
$pagegroupPretty = ucwords(str_replace("-"," ", $pagegroup));


$tabArray = [];
$query = new WP_Query( array(
    'post_type'  => 'any',
	 'orderby'   => 'menu_order',
    'order' => 'ASC',
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'page-toolkits.php'
) );

if ( $query->have_posts() ) {
    while ( $query->have_posts() ) : $query->the_post();
	     // if a page of this template contains the same page group as this page, those are the tabs we're going to want to display
	     if(strpos(get_the_permalink(), $pagegroup) !== false){
        	  array_push($tabArray, get_the_title());
		  }
    endwhile; 
}

function urlify($string) {
	return strtolower(str_replace(" ","-",preg_replace("/[^\s{a-zA-Z0-9}]/", '', $string)));
}

wp_reset_query();

?>


<div class="clear"></div>
<div class="main-content" id="main">

  <div class="toolkit-header container-fluid">
	<div class="row">
		<div class="toolkit-header-title col-md-3 col-sm-4 col-xs-12">
			<img src="/wp-content/themes/makerfaire/images/toolkit-icon.png" width="25px" height="25px" />
			<h1><?php echo($pagegroupPretty); ?></h1>
		</div>
		<div class="toolkit-tabs col-md-9 col-sm-8 col-xs-12">
			<ul class="nav nav-tabs">
				<?php 
					foreach($tabArray as $value) {
						// allow ampersands in titles but strip them from urls
	               $strippedValue = str_replace("  ", " ", str_replace("&#038;", "", $value));
						$tabUrl = '/' . $location . '/' . $pagegroup . '/' . urlify($strippedValue);
						
						echo('<li><a href="' . $tabUrl . '" ' . ($value == get_the_title() ? ' class="active"' : '') . '>' . $value . '</a></li>');
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
							echo('<li class="' . $leftLinkClass . ' ' . urlify(get_sub_field('header_text')) . '"><a href="#' .  urlify(get_sub_field('header_text')) . '">' . get_sub_field('header_text') . '</a></li>');
						}
			      echo('</ul>');
				}
			?>
			<div class="left-nav-back-to-top" style="display:none;">
				<div class="row">
					<div class="col-sm-12 text-center back-to-top">
						<a href="#topofpage">BACK TO TOP</a>
					</div>
				</div>
			</div>
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
							if(get_sub_field('section_type') == "Header") {
								 $leftLinkClass = "section-header";
							}else{
								 $leftLinkClass = "sub-section-header";
							}
							if(get_sub_field('section_type')) {
								echo('<div class="toolkit-section image_grid" id="' . urlify(get_sub_field('header_text')) .'">'); //image grid is here for the list styles until it's made universal
									 echo('<a class="toolkit-anchor" name="' . urlify(get_sub_field('header_text')) . '"></a>');
									 echo('<h2 class="' . $leftLinkClass . '">' . get_sub_field('header_text') . '</h2>');
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
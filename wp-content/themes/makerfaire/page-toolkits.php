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

function urlify($string) {
	return strtolower(str_replace(" ","-",preg_replace("/[^\s{a-zA-Z0-9}]/", '', $string)));
}

?>

<div class="clear"></div>
<div class="main-content" id="main">

  <div class="toolkit-header container-fluid">
	<div class="row">
		<div class="toolkit-header-title col-md-3 col-sm-4 col-xs-12">
			<img src="/wp-content/themes/makerfaire/images/toolkit-icon.png" width="25px" height="25px" />
			<h1><?php 
				if($pagegroupPretty) {
					echo($pagegroupPretty); 
				} else {
					echo( get_the_title() );
				}
			?></h1>
		</div>
		<div class="toolkit-tabs col-md-9 col-sm-8 col-xs-12">
			<ul class="nav nav-tabs">
				<?php 
				$parentID = $post->post_parent;
				$parent = get_post($parentID); 
				$parentSlug = $parent->post_name;

            if($parentSlug != "bay-area" && $parentSlug != "new-york") {
					if($post->post_parent){
						$children = wp_list_pages('title_li=&child_of='.$post->post_parent.'&echo=0'); 
					}else{
						$children = wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0'); 
					}
					if ($children) {
						echo $children;
					}
				}
				?>
			</ul>
		</div>
		<?php
			if(!empty(get_field('top_button')['top_button_text'])){
				echo('<a class="btn right-banner" href="' . get_field('top_button')['top_button_link'] . '">' . get_field('top_button')['top_button_text'] . ' </a>');
			}
		?>
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
							echo('<li class="' . $leftLinkClass . ' ' . urlify(get_sub_field('header_text')) . "-" . get_row_index() . '"><a href="#' .  urlify(get_sub_field('header_text')) . "-" . get_row_index() . '">' . rtrim(get_sub_field('header_text'), ': ') . '</a></li>');
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
         <?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			   <div class="toolkit-content">
					<?php the_content(); ?>
			   </div>
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
								echo('<div class="toolkit-section image_grid" id="' . urlify(get_sub_field('header_text')) . "-" . get_row_index() .'">'); //image grid is here for the list styles until it's made universal
									 echo('<a class="toolkit-anchor" name="' . urlify(get_sub_field('header_text')) . "-" . get_row_index() . '"></a>');
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
	<?php if(have_rows('bottom_buttons')) {
	  echo('<div class="row subfooter-wrapper">
	           <div class="subfooter-links">');
	    while (have_rows('bottom_buttons')) {
			 the_row();
			 echo('<div class="subfooter-link">
			          <a class="btn universal-btn" href="' . get_sub_field('bottom_button_link'). '">' .
						   get_sub_field('bottom_button_text') .
					    '</a>
				    </div>');
		 }
	  echo("   </div>
	        </div>");
	} ?>
  </div><!--Container-->
</div>
<?php get_footer(); ?>
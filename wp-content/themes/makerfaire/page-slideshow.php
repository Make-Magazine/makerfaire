<?php
/*
Template name: Slideshow
*/
get_header(); 
$par_post = get_post($post->post_parent);
$slug = $par_post->post_name;
        ?>
<div class="clear"></div>
<div class="container live-page">
	<div class="row" style="margin-bottom:0px;padding-bottom:0px;">
		<div class="col-md-7">
			<div class="row">
				<div class="col-md-12">
					<h1><?php echo get_the_title(); ?></h1>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<h2 class="live-time">Saturday 10am-6pm ET | Sunday 10am-6pm ET</h2>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<img class="makeybot" src="<?php echo get_stylesheet_directory_uri(); ?>/images/robot.png" width="auto" alt="makey robot" />
		</div>
		<div class="col-md-3 social">
			<div class="social-foot-col">
				<div class="social-profile-icons">
					<a class="sprite-facebook-32" href="//www.facebook.com/sharer/sharer.php?u=http://makerfaire.com/new-york-2015/slideshow" title="Facebook" target="_blank">
						<div class="social-profile-cont">
							<span class="sprite"></span>
						</div>
					</a>
					<a class="sprite-twitter-32" href="https://twitter.com/home?status=http://makerfaire.com/new-york-2015/slideshow" title="Twitter" target="_blank">
						<div class="social-profile-cont">
							<span class="sprite"></span>
						</div>
					</a>
					<a class="sprite-pinterest-32" href="//www.pinterest.com/makemagazine/maker-faire/" title="Pinterest" target="_blank">
						<div class="social-profile-cont">
							<span class="sprite"></span>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="content col-xs-12">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<?php the_content(); ?>
			</article>
			<?php endwhile; ?>
			<?php else: ?>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>
			<div class="clearfix">&nbsp;</div>
		</div>
	</div>
</div>
<div style="height:23px;" >
</div>
<!-- Sponsor carusel section-->
<div class="quora">                
	<div class="sponsors-wrap">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 sponsor-carousel-holder">
					<div class="head-box">
						<div class="row">
							<div class="col-xs-12 col-sm-8">
								<div class="title">
									<h1>Maker Faire Sponsors</h1>
								</div>
							</div>
							<div class="col-xs-12 col-sm-4 sponsor-select-wrapper">
								<select name="sponsorLevel" id="sponsorLevel">
									<option value="0" selected="selected">Goldsmith</option>
									<option value="1">Silversmith</option>
									<option value="2">Coppersmith</option>
								</select>
							</div>
						</div>
					</div>
					<div class="owl-carousel">
						<div class="item" data-level="Goldsmith">
							<?php echo mf_sponsor_list('Goldsmith Sponsor',$slug) ?>
						</div>
						<div class="item" data-level="Silversmith">
							<?php echo mf_sponsor_list('Silversmith Sponsor',$slug) ?>
						</div>
						<div class="item" data-level="Coppersmith">
							<?php echo mf_sponsor_list('Coppersmith Sponsor',$slug) ?>	
						</div>
					</div>
					<div class"col-xs-12 visible-xs-12">
						<a class="pull-right" href="/bay-area-2015/sponsors/">Become a sponsor</a></mark>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--end of Sponsor carusel section--> 
<!-- <div style="background-color: #075c78;color:#fff;padding-bottom:30px;font-size:12px;">
	<div class="container live-archive" >
		<h2>Make: Editors Report fom Maker Faire</h2>
	
			<div class="row" style="margin-bottom:30px;">
				<div class="col-md-4">
					<a href="#"><img class ="img-responsive" src="http://img.youtube.com/vi/be2k5b_4YBc/0.jpg" alt="" /></a>
					<h3 style="font-size:18px;margin-top:12px;">Denny the Urban Bike</h3>
					<p>App c# jQuery page speed dom python html markdown javascript tablet hosting bootstrap yaml FTP puppet sql page dom css TCP. </p>
				</div>
				<div class="col-md-4">
					<a href="#"><img class ="img-responsive" src="http://img.youtube.com/vi/be2k5b_4YBc/0.jpg" alt="" /></a>
					<h3 style="font-size:18px;margin-top:12px;">Botfactory and Squink and Bay Area Maker</h3>
					<p>App c# jQuery page speed dom python html markdown javascript tablet hosting bootstrap yaml FTP puppet sql page dom css TCP. </p>
	
				</div>
				<div class="col-md-4">
					<a href="#"><img class ="img-responsive" src="http://img.youtube.com/vi/be2k5b_4YBc/0.jpg" alt="" /></a>
					<h3 style="font-size:18px;margin-top:12px;">Denny the Urban Bike</h3>
					<p>App c# jQuery page speed dom python html markdown javascript tablet hosting bootstrap yaml FTP puppet sql page dom css TCP. </p>
				</div>
			</div> 
			<!-- new row-->
<!-- 			<div class="row" style="margin-bottom:30px;">
	<div class="col-md-4">
		<a href="#"><img class ="img-responsive" src="http://img.youtube.com/vi/be2k5b_4YBc/0.jpg" alt="" /></a>
		<h3 style="font-size:18px;margin-top:12px;">Denny the Urban Bike</h3>
		<p>App c# jQuery page speed dom python html markdown javascript tablet hosting bootstrap yaml FTP puppet sql page dom css TCP. </p>
	</div>
	<div class="col-md-4">
		<a href="#"><img class ="img-responsive" src="http://img.youtube.com/vi/be2k5b_4YBc/0.jpg" alt="" /></a>
		<h3 style="font-size:18px;margin-top:12px;">Botfactory and Squink and Bay Area Maker</h3>
		<p>App c# jQuery page speed dom python html markdown javascript tablet hosting bootstrap yaml FTP puppet sql page dom css TCP. </p>
	
	</div>
	<div class="col-md-4">
		<a href="#"><img class ="img-responsive" src="http://img.youtube.com/vi/be2k5b_4YBc/0.jpg" alt="" /></a>
		<h3 style="font-size:18px;margin-top:12px;">Denny the Urban Bike</h3>
		<p>App c# jQuery page speed dom python html markdown javascript tablet hosting bootstrap yaml FTP puppet sql page dom css TCP. </p>
	</div>
	</div>
	
	
	</div>
	</div>
	</div>
	<!--Container-->
	
<script type="text/javascript">
jQuery(document).ready(function() { 
	var owl = jQuery('.owl-carousel');
	jQuery("#sponsorLevel").select2({
		minimumResultsForSearch: -1,
	});
	jQuery('#sponsorLevel').on('select2:select', function (e) {
		var data = e.params.data;
		owl.trigger('to.owl.carousel', data.id)
	});
	owl.owlCarousel({
		items: 1,
		loop:true,
		autoplay: true,
		autoplayTimeout: 6000,  
    	animateIn: 'flipInX',
		slideSpeed: 600,
    	paginationSpeed: 500,
		margin:0,
		nav:false,
		dots:false,
		autoHeight: true,
		onTranslated: function(e) {
			jQuery("#select2-sponsorLevel-container").text(jQuery(".owl-item.active .item").attr("data-level"));
		},
	});
});
</script>
<?php get_footer(); ?>

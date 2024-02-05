<?php
	/*
	Template name: Live Video
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
					<a class="sprite-facebook-32" href="//www.facebook.com/sharer/sharer.php?u=http://makerfaire.com/live" title="Facebook" target="_blank">
						<div class="social-profile-cont">
							<span class="sprite"></span>
						</div>
					</a>
					<a class="sprite-twitter-32" href="https://twitter.com/home?status=http://makerfaire.com/live" title="Twitter" target="_blank">
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
		<div class="col-md-12">
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
<div class="flag-border"></div>
<!-- Sponsor carusel section-->
<div class="quora">
	<div class="sponsors-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12 sponsor-carousel-holder">
					<div class="head-box">
						<div class="row">
							<div class="col-xs-12 col-sm-8">
								<div class="title">
									<h1>Maker Faire Sponsors</h1>
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<div class="open-close">
									<a class="opener" href="#"><span class="selected">Silversmith</span><i class="icon-arrow-right"></i></a>
									<div class="slide">
										<ul class="tabset">
											<li class="active"><a href="#tab2">Goldsmith</a></li>
											<li><a href="#tab1">Silversmith</a></li>
											<li><a href="#tab3">Coppersmith</a></li>
											<!-- <li><a href="#tab4">Media</a></li>
												<li><a href="#tab4">Presenting</a></li> -->
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="sponsor-carousel">
						<div class="mask">
							<div class="slideset">
								<div class="slide">
									<?php echo mf_sponsor_list('Goldsmith Sponsor',$slug) ?>
								</div>
								<div class="slide">
									<?php echo mf_sponsor_list('Silversmith Sponsor',$slug) ?>
								</div>
								<div class="slide">
									<?php echo mf_sponsor_list('Coppersmith Sponsor',$slug) ?>	
								</div>
								<!--
									<div class="slide">
									<?php // echo mf_sponsor_list('Media Sponsor') ?>
									</div>
									<div class="slide">
									<?php // echo mf_sponsor_list('Presenting Sponsor') ?>
									</div>
									-->
							</div>
						</div>
					</div>
					<div class"col-md-12">
						<a class="pull-right" href="/bay-area-2015/sponsors/">Become a sponsor</a></mark>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--end of Sponsor carusel section--> 
<!--End  Container-->
<?php get_footer(); ?>

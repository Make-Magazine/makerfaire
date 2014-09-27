<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
	<meta charset="utf-8">
	<meta name="apple-itunes-app" content="app-id=463248665"/>

	<title><?php bloginfo('name'); ?> | <?php is_home() ? bloginfo('description') : wp_title(''); ?></title>
	<meta name="description" content="<?php if ( is_single() ) {
				echo wp_trim_words(strip_shortcodes(get_the_excerpt('...')), 20);
			} else {
				bloginfo('name'); echo " - "; bloginfo('description');
			}
			?>" />

	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Le styles -->

	<!-- TypeKit -->
	<link rel="stylesheet" href="http://use.typekit.com/c/4690c1/museo-slab:n8:n9:n1:n3,bebas-neue:n4,proxima-nova:n4:i4:n7:i7,museo-slab:n5.QL3:F:2,QL5:F:2,QL7:F:2,SKB:F:2,TGd:F:2,W0V:F:2,W0W:F:2,W0Y:F:2,W0Z:F:2,WH7:F:2/d?3bb2a6e53c9684ffdc9a98f6135b2a62e9fd3f37bbbb30d58844c72ca542eb12d9fc18cda0192bd960a04b65e2f2facc738d907514640137ac74942ecfe54dd35844bc349bb4c1279a7aaf8651616db7b59a075388454f5f4a07fb5c0b8f09dcccc3d70f9605ca7a1dbf9b12b3c351656254cd3fc59e92f2e542459e636860be01542f5c784cda4fe2fc310798ac7c1670eeda393aa990e8b58d73431e6bae280cf620ce09d0a49a9554ea7f25339dd274cf69ee61d55e93d9cb159fd2848203940e4eb67ad0455b5b574d1a27fec0ae65">
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

	// <script>
	// 	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	// 	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	// 	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	// 	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	// 	ga('create', 'UA-51157-7', 'auto');
	// 	ga('send', 'pageview');
	// </script>

	<?php wp_head(); ?>

	</head>


<body id="bootstrap-js" onLoad="onLoad()" <?php body_class(); ?> >

<!--
======
Topbar
======
-->

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="i-bar"></span>
			<span class="i-bar"></span>
			<span class="i-bar"></span>
		</a>
		<div class="nav-collapse">
			<ul class="nav">
				<li class="red"><a  class="red" href="http://makezine.com">MAKE</a></li>
							<li class=""><a href="http://blog.makezine.com">Blog</a></li>
							<li class=""><a href="http://makezine.com/magazine">Magazine</a></li>
							<li class=""><a href="http://makerfaire.com">Maker Faire</a></li>
							<li class=""><a href="http://makezine.com">Make: Projects</a></li>
							<li class=""><a href="http://makershed.com">Maker Shed</a></li>
							<li class=""><a href="http://kits.makezine.com">Kits</a></li>
			</ul>
			<ul class="nav pull-right">
				<li>
					<form action="http://blog.makezine.com/search/" class="form-horizontal form-search">
						<input type="hidden" name="as_sitesearch" value="makerfaire.com" />
						<input type="text" name="q" class="span2" />
						<input type="submit" class="btn btn-primary" value="Search" />
					</form>
				</li>
			</ul>
		</div>


		</div>
	</div>
</div>

<header id="header">

	<div class="container">

		<p class="tagline"><strong><em>A two-day, family-friendly festival of invention, creativity and resourcefulness, and a celebration of the Maker movement.</em></strong></p>

			<div class="row">

				<div class="span5">

					<h1><a href="http://makerfaire.com" title="Maker Faire"><img src="http://cdn.makezine.com/make/makerfaire/bayarea/2012/images/logo.jpg" width="380" alt="Maker Faire" title="Maker Faire"></a></h1>

				</div>

				<div class="span7">
					<!--<p class="blurb">
						Maker Faire is the premier event for grassroots American innovation.
						<img src="http://makerfaire.com/new/images/cross.png" alt="Cross" />
					</p>-->

					<div class="nav navi">

						<?php

							$defaults = array(
								'theme_location'  => '',
								'menu'            => 'header-menu',
								'container'       => false,
								'container_class' => '',
								'container_id'    => '',
								'menu_class'      => 'menu',
								'menu_id'         => '',
								'echo'            => true,
								'fallback_cb'     => 'wp_page_menu',
								'before'          => '',
								'after'           => '',
								'link_before'     => '<div>',
								'link_after'      => '</div>',
								'items_wrap'      => '<ul id="%1$s" class="%2$s" style="margin-left:12px;">%3$s</ul>',
								'depth'           => 0,
								'walker'          => ''
							);

						wp_nav_menu( $defaults );

						?>

						<!--<ul style="margin-left:10px;">

							<li class="dropdown inline">
								<a href="http://makerfaire.com/be-a-maker.html">
									<img src="http://makerfaire.com/new/images/be-a-maker.png" alt="Be a Maker" title="Be a Maker">
								</a>-->
								<!--<ul class="dropdown-menu">
									<li><h4>New York</h4></li>
									<li><a href="http://makerfairenyc.eventbrite.com/">Buy Tickets</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/attend/index.html">Ticket Information</a></li>
									<li><a href="http://makezine.com/makerfaire/newyork/2012/directions.html">Directions</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/hotel.html">Where to Stay</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/faq/index.html">FAQ</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/tickets/outlets.html">Ticket Outlets</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/schedule/index.html">Program Guide &amp; Schedule</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/makerinfo">Meet the Makers</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/schedule/grid.html">Schedule</a></li>1
									<li><a href="http://makerfaire.com/bayarea/2012/press/register.html">Press Registration</a></li>-->
									<!--<li class="divider"></li>
									<li><h4>Bay Area</h4></li>
									<li><a href="http://makerfairebayarea.eventbrite.com/">Buy Tickets</a></li
									<li><a href="http://makerfaire.com/bayarea/2012/tickets/">Ticket Information</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/tickets/outlets.html">Ticket Outlets</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/schedule/index.html">Program Guide &amp; Schedule</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/makerinfo">Meet the Makers</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/schedule/grid.html">Schedule</a></li>1
									<li><a href="http://makerfaire.com/bayarea/2012/car.html">Getting There by Car</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/alternative.html">Alternatives to Driving</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/hotel.html">Where to Stay</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/press/register.html">Press Registration</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/faq.html">FAQ</a></li>

								</ul>-->

							<!--</li>

							<li class="dropdown inline">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="http://makerfaire.com/new/images/check-out-the-program.png" alt="Check out the Program" title="Check out the Program">
								</a>
								<ul class="dropdown-menu">
									<li><a style="font-size:14px; font-weight:bold;" href="http://makerfaire.com/live">2012 Video Highlights</a></li>
									<li class="divider"></li>
									<li><h4>New York</h4></li>
									<li><a href="http://makerfaire.com/newyork/2012/makerinfo/index.html">Meet the Makers</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/schedule/index.html">Program &amp; Schedule</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/beat-reports/index.html">MAKE Editorial Coverage</a></li>

									<li class="divider"></li>
									<li><h4>Bay Area</h4></li>
									<li><a href="http://makerfaire.com/bayarea/2012/schedule/index.html">Program &amp; Schedule</a></li>
									<li><a href="http://app.net/makerfaire">Download the App</a></li>

								</ul>
							</li>

							<li class="dropdown inline">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="http://cdn.makezine.com/make/makerfaire/bayarea/2012/images/how-to-participate1.png" alt="How to Participate" title="See all the Faires">
								</a>

								<ul class="dropdown-menu">
									<li><h4>New York</h4></li>
									<li><a href="http://makerfaire.com/newyork/2012/callformakers/">Call for Makers</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/toolkit.html">Maker Toolkit</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/makercorps/index.html">Volunteer / Maker Corps Training</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/badges.html">Badges</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/promote/index.html">Promote</a></li>
									<li class="divider"></li>
									<li><h4>Bay Area</h4></li>
									<li><a href="http://makerfaire.com/bayarea/2012/makerinfo">Meet the Makers</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/schedule/index.html">Program Guide &amp; Schedule</a></li>-->
									<!--li><a href="http://makerfaire.com/bayarea/2012/callformakers/">Call for Makers</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/toolkit.html">Maker Toolkit</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/maker-in-training/">Volunteer / Maker Corps Training</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/promote/">Promote</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/education-day/">Education Day</a></li-->
									<!--<li class="divider"></li>
									<li><a href="http://makerfaire.com/sponsors/">Sponsors</a></li>-->
									<!--<li><a href="http://makerfaire.com/education/">Educators</a></li>-->
									<!--<li><a href="http://makezine.com/makerfaire/be-a-maker.html">Be A Maker</a></li>
								</ul>

							</li>
							<li class="dropdown inline">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="http://makerfaire.com/new/images/nav_03.png" alt="In the Media" title="In the Media">
								</a>

								<ul class="dropdown-menu">
									<li><a href="http://makerfaire.com/press/highlights.html">Highlights</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/beat-reports/index.html">MAKE Editorial Coverage</a></li>
									<li><a href="http://makerfaire.com/newyork/2012/press/register.html">Press Registration</a></li>-->
									<!--li><a href="http://makerfaire.com/bayarea/2012/press/">Press</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/press/releases.html">Press Releases</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/press/kits.html">Press Kits</a></li-->
								<!--</ul>



							</li>
							<li class="dropdown inline"><a href="http://makerfaire.com/bayarea/2012/map.html">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="http://makerfaire.com/new/images/nav_04.png" alt="See all the Faires" title="See all the Faires">
								</a>

								<ul class="dropdown-menu">
									<li><a href="http://makerfaire.com/newyork/2012/">New York</a></li>
									<li><a href="http://makerfaire.com/bayarea/2012/index.html">Bay Area</a></li>
									<li class="divider"></li>
									<li><a href="http://makerfaire.com/map.html">Find a Faire Near You</a></li>
									<li><a href="http://makerfaire.com/mini/make-a-maker-faire.html">Make a Maker Faire</a></li>
									<li class="divider"></li>
									<li><a href="http://makerfaire.com/archive.html">See Faires From Years Past</a></li>
									<li><a href="http://makerfaire.com/blue-ribbon/">Blue Ribbon Winners</a></li>

								</ul>

							</li>
						</ul>-->

					</div><!--end nav wrapper-->

				</div>

			</div>

		</div>

	</div>


</header>

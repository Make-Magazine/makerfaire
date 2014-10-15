<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
	<meta charset="utf-8">
	<meta name="apple-itunes-app" content="app-id=463248665"/>

	<title><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>

	<?php
		// Make sure we stop indexing of any maker pages, the application forms, author pages or attachment pages
		if ( get_post_type() == 'maker' || is_page( array( 876, 877, 878, 371 ) ) || is_author() || is_attachment() ) {
			echo '<meta name="robots" content="noindex, follow">';
		}
	?>

	<meta name="description" content="<?php if ( is_single() ) {
				echo wp_trim_words( strip_shortcodes( get_the_content('...') ), 20 );
			} else {
				bloginfo( 'name' );
				echo " - ";
				bloginfo('description');
			}
	?>" />

	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Le styles -->

	<?php wp_head(); ?>

	<!-- TypeKit -->
	<link rel="stylesheet" href="http://use.typekit.com/c/4690c1/museo-slab:n8:n9:n1:n3,bebas-neue:n4,proxima-nova:n4:i4:n7:i7,museo-slab:n5.QL3:F:2,QL5:F:2,QL7:F:2,SKB:F:2,TGd:F:2,W0V:F:2,W0W:F:2,W0Y:F:2,W0Z:F:2,WH7:F:2/d?3bb2a6e53c9684ffdc9a98f6135b2a62e9fd3f37bbbb30d58844c72ca542eb12d9fc18cda0192bd960a04b65e2f2facc738d907514640137ac74942ecfe54dd35844bc349bb4c1279a7aaf8651616db7b59a075388454f5f4a07fb5c0b8f09dcccc3d70f9605ca7a1dbf9b12b3c351656254cd3fc59e92f2e542459e636860be01542f5c784cda4fe2fc310798ac7c1670eeda393aa990e8b58d73431e6bae280cf620ce09d0a49a9554ea7f25339dd274cf69ee61d55e93d9cb159fd2848203940e4eb67ad0455b5b574d1a27fec0ae65">
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

	<?php get_template_part('dfp'); ?>

	<script>
		var _prum = [['id', '53fcea2fabe53d341d4ae0eb'],
		            ['mark', 'firstbyte', (new Date()).getTime()]];
		(function() {
		    var s = document.getElementsByTagName('script')[0]
		      , p = document.createElement('script');
		    p.async = 'async';
		    p.src = '//rum-static.pingdom.net/prum.min.js';
		    s.parentNode.insertBefore(p, s);
		})();
	</script>

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-51157-7', 'auto');
		ga('send', 'pageview', {
 		'page': location.pathname + location.search  + location.hash
		});
	</script>

	<?php if ( is_404() ) : // Load this last. ?>
		<script>
			// Track our 404 errors and log them to GA
			ga('send', 'event', '404', 'URL', document.location.pathname + document.location.search);
		</script>
	<?php endif; ?>

	<script type="text/javascript">
		dataLayer = [];
	</script>
	</head>

<body id="bootstrap-js" <?php body_class('no-js'); ?>>
	<!-- Google Tag Manager -->
	<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-WR8NLB"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-WR8NLB');</script>
	<!-- End Google Tag Manager -->

	<script type="text/javascript">document.body.className = document.body.className.replace('no-js','js');</script>
<!--
======
Topbar
======
-->

<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="i-bar"></span>
			<span class="i-bar"></span>
			<span class="i-bar"></span>
		</a>
		<div class="nav-collapse">
			<ul class="nav main-nav">
				<li><a class="red" href="http://makezine.com">MAKE</a></li>
				<li><a href="http://makezine.com/blog/">Blog</a></li>
				<li><a href="http://makezine.com/magazine/">Magazine</a></li>
				<li><a href="http://makerfaire.com">Maker Faire</a></li>
				<li><a href="http://makezine.com/projects/">Make: Projects</a></li>
				<li><a href="http://makershed.com">Maker Shed</a></li>
				<li><a href="http://kits.makezine.com">Kits</a></li>
			</ul>
			<ul class="nav pull-right">
				<li>
					<form action="http://makezine.com/search/" class="form-horizontal form-search">
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

		<div class="topad">
			<!-- Beginning Sync AdSlot 1 for Ad unit header ### size: [[728,90]]  -->
			<div id='div-gpt-ad-664089004995786621-1'>
				<script type='text/javascript'>
					googletag.cmd.push(function(){googletag.display('div-gpt-ad-664089004995786621-1')});
				</script>
			</div>
			<!-- End AdSlot 1 -->
		</div>
		
			<p class="tagline"><a href="http://disney.com"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/disney.png" alt="Disney"></a></p>


			<div class="row">

				<div class="span5">

					<h1><a href="http://makerfaire.com" title="Maker Faire"><img src="http://cdn.makezine.com/make/makerfaire/bayarea/2012/images/logo.jpg" width="380" alt="Maker Faire" title="Maker Faire"></a></h1>

				</div>

				<div class="span7">

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

					</div><!--end nav wrapper-->

				</div>

			</div>

		</div>

	</div>


</header>

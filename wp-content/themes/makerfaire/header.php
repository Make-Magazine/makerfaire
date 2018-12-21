<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="apple-itunes-app" content="app-id=463248665"/>
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="manifest" href="/manifest.json">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="theme-color" content="#ffffff">
  <title><?php bloginfo('name'); ?> | <?php is_front_page() ? 'Make • Create • Craft • Build • Play' : wp_title(''); ?></title>
  <?php
    // Make sure we stop indexing of any maker pages, the application forms, author pages or attachment pages
    if ( get_post_type() == 'maker' || is_page( array( 876, 877, 878, 371 ) ) || is_author() || is_attachment() ) {
      echo '<meta name="robots" content="noindex, follow">';
    }
  ?>
  <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <script type="text/javascript">
    var templateUrl = '<?= get_site_url(); ?>';
  </script>
  <!-- Le styles -->
    <?php wp_head(); ?>

  <?php get_template_part('dfp'); ?>


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

<body id="makerfaire" <?php body_class('no-js'); ?>>
  <!-- Google Tag Manager MakerFaire -->

  <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PCDDDV"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-PCDDDV');</script>
  <!-- End Google Tag Manager -->
  <script type="text/javascript">document.body.className = document.body.className.replace('no-js','js');</script>
<!-- ====== Topbar ====== -->
<!-- TOP BRAND BAR
<div class="hidden-xs top-header-bar-brand">
  <div class="container">
    <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6 text-center">
        <p class="header-make-img"><a href="//makezine.com?utm_source=makerfaire.com&utm_medium=brand+bar&utm_campaign=explore+all+of+make" target="_blank">Explore all of <img src="<?php //echo get_stylesheet_directory_uri(); ?>/img/make_logo.png" alt="Make: Makezine Logo" /></a></p>
      </div>
    </div>
  </div>
</div> -->

<!-- div id="search-modal"> SEARCH FEATURE TO BE CONTINUED
  <form role="search" method="get" class="search-form" action="/">
      <label class="sb-search-label" for="search">Search</label>
        <input class="sb-search-input search-field" placeholder="What are you searching for?" value="" name="s" id="search" title="" type="text">
        <input class="sb-search-submit" name="submit" value="Search" type="submit">
  </form>
</div> -->

<header id="universal-nav" class="universal-nav">

	<?php // Nav Level 1 and Hamburger
      $context = null;
      if(UNIVERSAL_ASSET_USER && UNIVERSAL_ASSET_PASS) {
         $context = stream_context_create(array(
               'http' => array(
                  'header'  => "Authorization: Basic " . base64_encode(UNIVERSAL_ASSET_USER.':'.UNIVERSAL_ASSET_PASS)
               )
         ));
      }
      echo file_get_contents( UNIVERSAL_ASSET_URL_PREFIX . '/wp-content/themes/memberships/universal-nav/universal-topnav.html', false, $context);
	?>
	
  <div id="nav-level-2" class="nav-level-2">
    <div class="container">
        <div class="nav-2-banner">
        <?php
          wp_nav_menu( array(
              'menu'              => 'secondary_universal_menu',
              'theme_location'    => 'secondary_universal_menu',
              'depth'             => 1,
              'container'         => '',
              'container_class'   => '',
              'link_before'       => '<span>',
              'link_after'        => '</span>',
              'menu_class'        => 'nav navbar-nav',
              'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
              'walker'            => new wp_bootstrap_navwalker())
          );
        ?>
        </div>
    </div>
  </div><!-- .nav-level-2 -->
  <!--<div class="container search-container">
    <ul class="search-button">
        <li>
          <div id="sb-search" class="sb-search">
            <i class="fa fa-search" aria-hidden="true"></i>
          </div>
        </li>
    </ul>
  </div>-->


  <div id="nav-flyout">
   <?php
      echo file_get_contents( UNIVERSAL_ASSET_URL_PREFIX . '/wp-content/themes/memberships/universal-nav/universal-megamenu.html', false, $context);
   ?>
  </div>

</header>
<div class="nav-flyout-underlay"></div>
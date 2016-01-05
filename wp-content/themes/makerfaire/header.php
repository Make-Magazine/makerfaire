<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="apple-itunes-app" content="app-id=463248665"/>
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

  <!-- Le styles -->
  <?php wp_head(); ?>
  <!-- Remarketing pixel -->
  <!-- sumome -->
  <script src="//load.sumome.com/" data-sumo-site-id="3aa0a00731e2c63a4b3d480f0fef6df9476553c74fc88aa80b948e2a878b3d71" async="async"></script>
  <script type="text/javascript">
    adroll_adv_id = "QZ72KCGOPBGLLLPAE3SDSI";
    adroll_pix_id = "RGZKRB7CHJF5RBMNCUJREU";
    (function () {
    var oldonload = window.onload;
    window.onload = function(){
       __adroll_loaded=true;
       var scr = document.createElement("script");
       var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
       scr.setAttribute('async', 'true');
       scr.type = "text/javascript";
       scr.src = host + "/j/roundtrip.js";
       ((document.getElementsByTagName('head') || [null])[0] ||
        document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
       if(oldonload){oldonload()}};
    }());
  </script>

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
<!-- TOP BRAND BAR -->
<div class="hidden-xs top-header-bar-brand">
  <div class="container">
    <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6 text-center">
        <p class="header-make-img"><a href="//makezine.com?utm_source=makerfaire.com&utm_medium=brand+bar&utm_campaign=explore+all+of+make" target="_blank">Explore all of <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/make_logo.png" alt="Make: Makezine Logo" /></a></p>
      </div>
      <div class="col-sm-3">
        <p class="header-sub-link pull-right"><a id="trigger-overlay" href="#">SUBSCRIBE </a></p>
      </div>
    </div>
  </div>   
</div>

<header id="header" class="quora">
  <!-- Mobile Nav -->
  <nav class="navbar navbar-default navbar-fixed-top visible-xs-block">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="<?php bloginfo('url'); ?>"><img src="http://cdn.makezine.com/make/makerfaire/bayarea/2012/images/logo.jpg" height="43" width="183" alt="maker faire"></a>
        <button type="button" class="navbar-toggle collapsed pull-right" data-toggle="collapse" data-target="#navbar-mobile-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="navbar-mobile-collapse">
        <ul class="nav navbar-nav">
          <?php
            $menu = wp_get_nav_menu_object('Main Navigation Version 3 Mobile');
            $menuitems = wp_get_nav_menu_items( $menu->term_id, array('order' => 'DESC') );
            foreach( $menuitems as $item ):
              $url     = $item->url;
              $classes = $item->classes;
          ?>
            <li class="<?php foreach ($classes as $class) {echo $class.' ';}; ?>">
              <a href="<?php echo $url; ?>"></a>
            </li>                           
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Desktop Nav -->
  <nav class="navbar hidden-xs desktop-nav">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="<?php bloginfo('url'); ?>"><img src="http://cdn.makezine.com/make/makerfaire/bayarea/2012/images/logo.jpg" height="43" width="183" alt="maker faire"></a>
      </div>
      <div class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <?php
            $menu = wp_get_nav_menu_object('Main Navigation Version 3');
            $menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC', 'walker' => new Description_Walker ) );
            $count = 0;
            $submenu = false;
            foreach( $menuitems as $item ):
              $new_window     = (isset($item->target)?$item->target:'');
              $link           = $item->url;
              $title          = $item->title;
              $classes        = $item->classes;
              $description    = $item->description;
              // item does not have a parent so menu_item_parent equals 0 (false)
              if ( !$item->menu_item_parent ):
                // save this id for later comparison with sub-menu items
                $parent_id = $item->ID;
          ?>
            <li class="<?php foreach ($classes as $class) {echo $class.' ';}; ?>">
              <a href="<?php echo $link; ?>"
                <?php if (in_array("dropdown", $classes)) { echo 'class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"'; } ?>
                <?php echo ($new_window!=''?'target="'.$new_window.'"':'');?>
              >
                <?php echo $title; ?>
                <?php if (in_array("dropdown", $classes)): ?>
                  <span class="caret"></span>
                <?php endif; ?>
              </a>
              <?php endif; ?>
              <?php if ( $parent_id == $item->menu_item_parent ): ?>
                <?php if ( !$submenu ): $submenu = true;?>
                  <div class="drop-holder">
                    <div class="drop">
                      <div class="container">
                        <div class="col-sm-3 dynamic-hackbox"></div>
                        <div class="col-sm-9 col no-border">
                          <ul class="sub-menu">
                <?php endif; ?>
                            <li class="item <?php foreach ($classes as $class) {echo $class.' ';}; ?>">
                              <a href="<?php echo $link; ?>"
                                <?php echo ($new_window!=''?'target="'.$new_window.'"':'');?>
                                class="title">
                                <?php echo $title; ?>
                              </a>
                              <div class="description"><?php echo $description; ?></div>
                            </li>
                <?php if ( $menuitems[ $count + 1 ]->menu_item_parent != $parent_id &&$submenu ): ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php $submenu = false; endif; ?>
              <?php endif; ?>
            <?php if ( (isset($menuitems[ $count + 1 ]->menu_item_parent))&&$menuitems[ $count + 1 ]->menu_item_parent != $parent_id ): ?>
            </li>
            <?php $submenu = false; endif; ?>
          <?php $count++; endforeach; ?>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Desktop Subnav bar -->
  <nav class="navbar desktop-nav hidden-xs subnav">
    <div class="container">
      <?php
        wp_nav_menu( array(
          'theme_location' => '_no_default_fallback',
          'menu'            => 'Main Navigation Version 3 Subnav',
          'container' => false,
          'menu_class' => 'nav navbar-nav'
        ) );
      ?>
    </div>
  </nav>
</header>

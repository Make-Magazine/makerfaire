<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
    <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="view-transition" content="same-origin">
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
        if (get_post_type() == 'maker' || is_page(array(876, 877, 878, 371)) || is_author() || is_attachment()) {
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
        <!-- Remarketing pixel -->
        <script type="text/javascript">
            adroll_adv_id = "QZ72KCGOPBGLLLPAE3SDSI";
            adroll_pix_id = "RGZKRB7CHJF5RBMNCUJREU";
            (function () {
                var oldonload = window.onload;
                window.onload = function () {
                    __adroll_loaded = true;
                    var scr = document.createElement("script");
                    var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
                    scr.setAttribute('async', 'true');
                    scr.type = "text/javascript";
                    scr.src = host + "/j/roundtrip.js";
                    ((document.getElementsByTagName('head') || [null])[0] ||
                            document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
                    if (oldonload) {
                        oldonload()
                    }
                };
            }());
        </script>

        <script>
            var _prum = [['id', '53fcea2fabe53d341d4ae0eb'],
                ['mark', 'firstbyte', (new Date()).getTime()]];
            (function () {
                var s = document.getElementsByTagName('script')[0]
                        , p = document.createElement('script');
                p.async = 'async';
                p.src = '//rum-static.pingdom.net/prum.min.js';
                s.parentNode.insertBefore(p, s);
            })();
        </script>

        <?php
        // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
        if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") {
            //get_template_part('dfp');
            ?>
			<!-- Data layer Object -->
			<script type="text/javascript">
				window.dataLayer = window.dataLayer || [];
			</script>

			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','GTM-PCDDDV');</script>
			<!-- End Google Tag Manager -->

			<!-- Google tag (gtag.js) GA4 -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=G-51PP9YXQ8B"></script>
			<script>
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', 'G-51PP9YXQ8B');
			</script>

            <!-- Facebook Pixel Code -->
            <script>
                !function (f, b, e, v, n, t, s) {
                    if (f.fbq)
                        return;
                    n = f.fbq = function () {
                        n.callMethod ?
                                n.callMethod.apply(n, arguments) : n.queue.push(arguments)};if (!f._fbq)
                        f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
                }(window,
                        document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

                fbq('init', '399923000199419');
                fbq('track', "PageView");
            </script>
            <noscript></noscript>
            <!-- End Facebook Pixel Code -->
            
		<?php } // end cookie law if  ?>

    </head>

    <body id="makerfaire" <?php body_class('no-js'); ?>>
		<div id="page" class="site-container">
        <?php
        // Tracking pixels users can turn off through the cookie law checkbox
        if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") {
            ?>
			<!-- Google Tag Manager (noscript) -->
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PCDDDV"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager (noscript) -->
            <script type="text/javascript">document.body.className = document.body.className.replace('no-js', 'js');</script>

		<?php } // end cookie law if  ?>

        <a name="topofpage"></a>    
		<?php
        //get auth0 login modal
        $auth0_modal = do_shortcode('[auth0 show_as_modal="1" modal_trigger_name="Log In"]');

		// Universal Nav
		$uni_nav =  basicCurl(UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . '/wp-content/universal-assets/v2/page-elements/universal-topnav.html');
        
        echo str_replace("{{auth0_login_modal}}",$auth0_modal,$uni_nav); 
        
        //from the individual entry page, used to set the subnav on maker/entry pages
        global $faire_name;
        if(!isset($faire_name)){
            $faire_name = '';
        }  
              
		?>
		<div id="universal-subnav" class="nav-level-2">
			<?php
            $secondary_nav='secondary_universal_menu';
            if(is_object($post)) {                                                
                $permalink = get_permalink($post);
                if(stripos($faire_name, 'Bay Area') !== false ||
                   stripos($permalink,  'bay-area') !== false) {                
                    $secondary_nav='bay_area_secondary_nav';
                }elseif(stripos($permalink, 'yearbook') !== false){
                    $secondary_nav='yearbook_secondary_nav';
                }
            } 

            wp_nav_menu( array(
                'menu_id'           => 'menu-secondary_universal_menu',
                'menu'              => $secondary_nav,
                'theme_location'    => $secondary_nav,
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
		<div id="content" class="site-content">

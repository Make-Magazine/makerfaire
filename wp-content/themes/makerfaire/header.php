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
        if (is_page(array(876, 877, 878, 371)) || is_author() || is_attachment()) {
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

    </head>

    <body id="makerfaire" <?php body_class('no-js'); ?>>
        <?php wp_body_open(); ?>
		<div id="page" class="site-container">

        <a name="topofpage"></a>    
		<?php
        //get auth0 login modal
        $auth0_modal = do_shortcode('[auth0 show_as_modal="1" modal_trigger_name="Log In"]');

		// Universal Nav
		$uni_nav =  basicCurl(UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . '/wp-content/universal-assets/v2/page-elements/universal-topnav.html');
        
        echo str_replace("{{auth0_login_modal}}",$auth0_modal,$uni_nav ?? 'test'); 
        
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
                if(stripos($permalink, 'yearbook') !== false){
                    $secondary_nav='yearbook_secondary_nav';
                }elseif(stripos($faire_name, 'Bay Area') !== false ||
                   stripos($permalink,  'bay-area') !== false) {                
                    $secondary_nav='bay_area_secondary_nav';
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

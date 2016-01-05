jQuery(function() {
  var aroundTheWorldCustomHtml = '<a class="nav-thanks"><img class="nav-image img-responsive" src="http://makerfaire.com/wp-content/themes/makerfaire/images/mf-feature-mfba16-200px.png" alt="Maker Faire World logo" width="95" height="95" scale="0"><p>Maker Faire Bay Area 2016</p><p style="color:red;">CALL FOR MAKERS COMING&nbsp;SOON!</p></a>';
  var aboutItemCustomHtml = '<img class="img-responsive" src="http://makerfaire.com/wp-content/themes/makerfaire/images/about-logo.png" alt="Maker Faire Badge logo" scale="0">';
  jQuery('.around-the-world-item .dynamic-hackbox').html(aroundTheWorldCustomHtml);
  jQuery('.whats-it-about .dynamic-hackbox').html(aboutItemCustomHtml);
});

/*
 *  
 *  Custom jQuery for the mobile navigation
 *  Added by Dave B
 *
 */
jQuery(function() {
  jQuery('#menu-main-navigation-version-2-mobile').addClass('nav navbar-nav');
  jQuery('#menu-main-navigation-version-2-mobile li.menu-item-has-children').addClass('dropdown');
  jQuery('#menu-main-navigation-version-2-mobile li.menu-item-has-children > a').addClass('dropdown-toggle').attr({
    'data-toggle': 'dropdown',
    'role': 'button',
    'aria-expanded': 'false'
  });
  jQuery('#menu-main-navigation-version-2-mobile li.menu-item-has-children > a').append('<span class="caret"></span>');
  jQuery('#menu-main-navigation-version-2-mobile li.menu-item-has-children ul').addClass('dropdown-menu').attr({
    'role': 'menu'
  });
  jQuery('#menu-main-navigation-version-2-mobile .mobile-nav-app a').append('<i class="icon-mobile pull-left padright"></i>');
});

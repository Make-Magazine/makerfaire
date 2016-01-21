jQuery(function() {
  jQuery('.desktop-nav .sub-menu').wrapInner('<div class=\'container\'></div>');
});

/*
 *  
 *  Custom jQuery for the mobile navigation
 *  Added by Dave B
 *
 */
jQuery(function() {
  jQuery('.menu-main-navigation-version-3-mobile li.menu-item-has-children > a').attr({
    'data-toggle': 'dropdown',
    'role': 'button',
    'aria-expanded': 'false'
  });
  jQuery('.menu-main-navigation-version-3-mobile li.menu-item-has-children > a').append('<span class="caret"></span>');
  jQuery('.menu-main-navigation-version-3-mobile li.menu-item-has-children ul').addClass('dropdown-menu').attr({
    'role': 'menu'
  });
  jQuery('.menu-main-navigation-version-3-mobile .mobile-nav-app a').append('<i class="icon-mobile pull-left padright"></i>');
});

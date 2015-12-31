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
  jQuery('#menu-main-navigation-version-2-mobile .mobile-BA-parent ul').prepend('<li><a class="mobile-nav-tickets text-center" href="//www.eventbrite.com/e/maker-faire-bay-area-2015-tickets-5938495199" target="_blank">BUY TICKETS</a></li>');
  jQuery('#menu-main-navigation-version-2-mobile .mobile-BA-parent ul').append('<li><div class="mobile-li-buttons padtop paddingbottom"><div class="col-xs-4"><a class="btn-cyan">Education Forum</a></div><div class="col-xs-4"><a class="btn-cyan">MakerCon</a></div><div class="col-xs-4"><a class="btn-cyan">MakerWeek</a></div></div></li>');
  jQuery('#menu-main-navigation-version-2-mobile .mobile-nav-app a').append('<i class="icon-mobile pull-left padright"></i>');
});

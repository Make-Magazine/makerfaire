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

jQuery(function() {
  jQuery('#hamburger-icon, #hamburger-makey, .nav-flyout-underlay').click(function() {
    jQuery('.stagingMsg').toggleClass('gone');
    jQuery('#hamburger-icon').toggleClass('open');
    jQuery('#hamburger-makey').animate({opacity: 'toggle'})
    jQuery('#nav-flyout').animate({opacity: 'toggle'});
    jQuery('body').toggleClass('nav-open-no-scroll');
    jQuery('.nav-flyout-underlay').animate({opacity: 'toggle'});
  });

  jQuery('.nav-flyout-column').on('click', '.expanding-underline', function(event) {
    if (jQuery(window).width() < 577) { 
      event.preventDefault();
      jQuery(this).toggleClass('underline-open');
      jQuery(this).next('.nav-flyout-ul').slideToggle();
    }
  });
  // fix nav to top on scrolldown, stay fixed for transition from mobile to desktop
  var e = jQuery(".universal-nav");
  var hamburger = jQuery(".nav-hamburger");
  var y_pos = jQuery(".nav-level-2").offset().top;
  var nextItemUnderNav = jQuery("#main");

  jQuery(window).on('resize', function(){
      if (jQuery(window).width() < 748) {
          y_pos = 0;
          nextItemUnderNav.css("margin-top", "55px");
      }else{
          y_pos = 75;
          nextItemUnderNav.css("margin-top", "0px");
      }
  });
  jQuery(document).scroll(function() {
      var scrollTop = jQuery(this).scrollTop();
      if(scrollTop > y_pos && jQuery(window).width() > 748){
          e.addClass("main-nav-scrolled"); 
          hamburger.addClass("ham-menu-animate");
          nextItemUnderNav.css("margin-top", "55px");
      }else if(scrollTop <= y_pos){
          e.removeClass("main-nav-scrolled"); 
          hamburger.removeClass("ham-menu-animate");
          if (jQuery(window).width() > 767) {
            nextItemUnderNav.css("margin-top", "0px");
          }
      }
  });
    
  /* Bring back if we need search for any reason
  jQuery("#search-modal").fancybox({
        wrapCSS : 'search-modal-wrapper',
        //autoSize : true,
        //width  : 400,
        autoHeight : true,
        padding : 0,
        closeClick  : false, 
        afterShow   : function() {
            // keep it from reloading upon clicks
            var modalWidth = jQuery('.fancybox-inner').width();
            jQuery('.fancybox-inner').css("width", modalWidth + 1);
            jQuery('#search-modal').bind('click', false);
            jQuery(".sb-search-submit").click(function(e){
                if(jQuery("#search").val() && jQuery("#search").val() != ""){
                    var searchForm = jQuery(".search-form");
                    window.location.href = searchForm.attr("action") +"?s=" + jQuery("#search").val();
                }else{
                    jQuery("#search").attr('placeholder', "Please enter in some text to search for...");            
                }
            });
            jQuery(".sb-search-input").focus();
        },
        afterClose: function () {
            jQuery('#search-modal').unbind('click', false);
        }
  });

  jQuery(".fa-search").click(function(e){
       jQuery("#search-modal").trigger('click');
  });*/
    
  // to keep this nav universal, let's not have each site's style sheet highlight a different active manually
  var site = window.location.hostname;
  var firstpath = jQuery(location).attr('pathname');
    firstpath.indexOf(1);
    firstpath.toLowerCase();
    firstpath = firstpath.split("/")[1];
  var shareSection = site + "/" + firstpath;
  function universalNavActive( site ) {
    jQuery(".nav-" + site).addClass("active-site");
    jQuery(".nav-" + site + " .nav-level-2-arrow").addClass("active-site")
  }
  // each one has to apply to a number of environments
  switch(site) {
    case "make-zine":
    case "makezine":
    case "makezine.wpengine.com":
    case "makezine.staging.wpengine.com":
    case "makezine.com":
        universalNavActive("zine");
        break;
    case "makeco":
    case "makeco.wpengine.com":
    case "makeco.staging.wpengine.com/":
    case "makeco.com":
        universalNavActive("make");
        break;
    case "makershed.com":
        universalNavActive("shed")
        break;  
    case "maker-faire":
    case "makerfaire":
    case "https://makerfaire.wpengine.com":
    case "https://makerfaire.staging.wpengine.com":
    case "https://makerfaire.com":
        universalNavActive("faire")
        break;
    default:
          break;
  }
  switch(shareSection) {
    case "maker-share/learning":
    case "makershare/learning":
    case "makeshare.wpengine.com/learning":
    case "makershare.staging.wpengine.com/learning":
    case "makershare.com/learning":
        universalNavActive("share")
        break;
    case "maker-share/makers":
    case "makershare/makers":
    case "makeshare.wpengine.com/makers":
    case "makershare.staging.wpengine.com/makers":
    case "makershare.com/makers":
        universalNavActive("share-p")
        break;
    default:
          break;
  }
});
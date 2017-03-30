jQuery(function() {
  initCustomGallery();
  initThumbnailsGallery();
});

// custom gallery init
function initCustomGallery() {
  jQuery('.carousel-inner').scrollAbsoluteGallery({
    mask: 'div.mask',
    slider: 'div.slideset',
    slides: 'div.slide',
    btnPrev: 'a.btn-prev',
    btnNext: 'a.btn-next',
    generatePagination: '.pagination',
    stretchSlideToMask: true,
    pauseOnHover: true,
    maskAutoSize: true,
    autoRotation: true,
    switchTime: 5000,
    animSpeed: 500
  });
  jQuery('div.sponsor-carousel-holder').scrollAbsoluteGallery({
    mask: 'div.mask',
    slider: 'div.slideset',
    slides: 'div.slide',
    btnPrev: 'a.btn-prev',
    btnNext: 'a.btn-next',
    pagerLinks: '.tabset li',
    stretchSlideToMask: true,
    pauseOnHover: true,
    maskAutoSize: true,
    autoRotation: true,
    switchTime: 5000,
    animSpeed: 500,
    reverse: true
  });
  jQuery('.gallery-holder').scrollAbsoluteGallery({
    mask: 'div.cycle-gallery div.mask',
    slider: 'div.slideset',
    slides: 'div.slide',
    btnPrev: 'div.cycle-gallery a.btn-prev',
    btnNext: 'div.cycle-gallery a.btn-next',
    pagerLinks: 'div.carousel .slide',
    stretchSlideToMask: true,
    pauseOnHover: true,
    maskAutoSize: true,
    autoRotation: true,
    switchTime: 3000,
    animSpeed: 500,
    onInit: function(self) {
      pagerInstance = jQuery('.gallery-holder div.carousel .slideset');
      self.btnPrev.on('click', function() {
        pagerInstance.trigger('prev', 1);
      });
      self.btnNext.on('click', function() {
        pagerInstance.trigger('next', 1);
      });
      self.pagerLinks.each(function(ind) {
        var item = jQuery(this);
        item.on('click', function() {
          pagerInstance.trigger('slideTo', ind);
        });
      });
    }
  });
}

function initThumbnailsGallery() {
  jQuery('.gallery-holder div.carousel').each(function() {
    if (jQuery(window).width() < 768) {
      var thumbNumber = 4;
    } else if (jQuery(window).width() > 1199) {
      var thumbNumber = 10;
    } else {
      var thumbNumber = 7;
    }
    var holder = jQuery(this),
      btnPrev = holder.find('.btn-prev'),
      btnNext = holder.find('.btn-next'),
      slides = holder.find('.slide'),
      activeClass = 'active';

    btnNext.on('click', function() {
      jQuery('.gallery-holder').data('ScrollAbsoluteGallery').nextSlide();
    });

    btnPrev.on('click', function() {
      jQuery('.gallery-holder').data('ScrollAbsoluteGallery').prevSlide();
    });
    holder.find('.slideset').carouFredSel({
      responsive: true,
      auto: false,
      circular: true,
      infinite: true,
      direction: 'left',
      width: '100%',
      prev: btnPrev,
      next: btnNext,
      mousewheel: false,
      items: {
        visible: thumbNumber
      },
      scroll: {
        items: 1,
        onBefore: function(data) {
          slides.removeClass(activeClass);
          data.items.visible.eq(0).addClass(activeClass);
        }
      },
      swipe: {
        onMouse: false,
        onTouch: true,
        options: {
          excludedElements: '.noSwipe'
        }
      }
    });
  });
}

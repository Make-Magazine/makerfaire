var Widget_DCE_Dynamicposts_carousel_Handler = function ($scope, $) {

    var smsc = null;

	var elementSettings = get_Dyncontel_ElementSettings($scope);
    var id_scope = $scope.attr('data-id');
	var id_post = $scope.attr('data-post-id');

    var elementSwiper = $scope.find('.dce-posts-container.dce-skin-carousel');

    let dcePostsSwiper = null;

    var isCarouselEnabled = false;

    var centroDiapo = false;
    var cicloInfinito = false;
    var slideInitNum = 0;
    var slidesPerView = Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView']);

    var slideNum = $scope.find('.dce-post-item').length;

    centerDiapo = Boolean( elementSettings[DCE_dynposts_skinPrefix+'centeredSlides'] );
    cicloInfinito = Boolean( elementSettings[DCE_dynposts_skinPrefix+'loop'] );

    var elementorBreakpoints = elementorFrontend.config.breakpoints;

	if( elementSettings.carousel_match_height ) {
		if( elementSettings.style_items === 'template' ) {
			if( $scope.find( '.dce-post-block .elementor-inner-section' ).length ) {
				selector = '.dce-post-block .elementor-inner-section';
			} else {
				selector = '.dce-post-block .elementor-top-section';
			}
		} else {
			selector = '.dce-post-block';
		}
		$(selector).matchHeight(
			{
				byRow: false,
			}
		);
	}

    var dceSwiperOptions = {
        direction: String(elementSettings[DCE_dynposts_skinPrefix+'direction_slider']) || 'horizontal', //vertical
        initialSlide: slideInitNum,
        reverseDirection: Boolean( elementSettings[DCE_dynposts_skinPrefix+'reverseDirection'] ),
        speed: Number(elementSettings[DCE_dynposts_skinPrefix+'speed_slider']) || 300,
        autoHeight: Boolean( elementSettings[DCE_dynposts_skinPrefix+'autoHeight'] ), // Set to true and slider wrapper will adopt its height to the height of the currently active slide
        effect: elementSettings[DCE_dynposts_skinPrefix+'effects'] || 'slide',
        cubeEffect: {
        	shadow: Boolean( elementSettings[DCE_dynposts_skinPrefix+'cube_shadow'] ),
        	slideShadows: Boolean( elementSettings[DCE_dynposts_skinPrefix+'slideShadows'] ),
            shadowOffset: 20,
            shadowScale: 0.94,
        },
        coverflowEffect: {
            rotate: 50,
            stretch: Number(elementSettings[DCE_dynposts_skinPrefix+'coverflow_stretch']) || 0,
            depth: 100,
            modifier: Number(elementSettings[DCE_dynposts_skinPrefix+'coverflow_modifier']) || 1,
            slideShadows: Boolean( elementSettings[DCE_dynposts_skinPrefix+'slideShadows'] ),
        },
        flipEffect: {
            rotate: 30,
            slideShadows: Boolean( elementSettings[DCE_dynposts_skinPrefix+'slideShadows'] ),
            limitRotation: true,
        },
        fadeEffect: {
		    crossFade: Boolean( elementSettings[DCE_dynposts_skinPrefix+'crossFade'] )
		  },
        
        initialSlide: Number(elementSettings[DCE_dynposts_skinPrefix+'initialSlide']) || 0,
        slidesPerView: slidesPerView || 'auto',
        slidesPerGroup: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup']) || 1, // Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1
        slidesPerColumn: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn']) || 1, // 1, // Number of slides per column, for multirow layout
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween']) || 0, // 30,
        slidesOffsetBefore: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetBefore']) || 0, //   Add (in px) additional slide offset in the beginning of the container (before all slides)
        slidesOffsetAfter: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetAfter']) || 0, //    Add (in px) additional slide offset in the end of the container (after all slides)
        slidesPerColumnFill: String(elementSettings[DCE_dynposts_skinPrefix+'slidesPerColumnFill']) || 'row', //Could be 'column' or 'row'. Defines how slides should fill rows, by column or by row
        centerInsufficientSlides: true,
        centeredSlides: centroDiapo,
        centeredSlidesBounds: Boolean( elementSettings[DCE_dynposts_skinPrefix+'centeredSlidesBounds'] ),
        grabCursor: Boolean( elementSettings[DCE_dynposts_skinPrefix+'grabCursor'] ), //true,
        freeMode: Boolean( elementSettings[DCE_dynposts_skinPrefix+'freeMode'] ),
        freeModeMomentum: Boolean( elementSettings[DCE_dynposts_skinPrefix+'freeModeMomentum'] ),
        freeModeMomentumRatio: Number(elementSettings[DCE_dynposts_skinPrefix+'freeModeMomentumRatio']) || 1,
        freeModeMomentumVelocityRatio: Number(elementSettings[DCE_dynposts_skinPrefix+'freeModeMomentumVelocityRatio']) || 1,
        freeModeMomentumBounce: Boolean( elementSettings[DCE_dynposts_skinPrefix+'freeModeMomentumBounce'] ),
        freeModeMomentumBounceRatio: Number(elementSettings[DCE_dynposts_skinPrefix+'speed']) || 1,
        freeModeMinimumVelocity: Number(elementSettings[DCE_dynposts_skinPrefix+'speed']) || 0.02,
        freeModeSticky: Boolean( elementSettings[DCE_dynposts_skinPrefix+'freeModeSticky'] ),
        loop: cicloInfinito,
        navigation: {
            nextEl: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .next-' + id_scope : '.next-' + id_scope,
            prevEl: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .prev-' + id_scope : '.prev-' + id_scope,
        },
        pagination: {
            el: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .pagination-' + id_scope : '.pagination-' + id_scope,
            clickable: true,
            type: String(elementSettings[DCE_dynposts_skinPrefix+'pagination_type']) || 'bullets', //"bullets", "fraction", "progressbar" or "custom"

            dynamicBullets: Boolean( elementSettings[DCE_dynposts_skinPrefix+'dynamicBullets'] ),

            renderBullet: function (index, className) {
            	var indexLabel = !Boolean( elementSettings[DCE_dynposts_skinPrefix+'dynamicBullets']) && Boolean( elementSettings[DCE_dynposts_skinPrefix+'bullets_numbers']) ? '<span class="swiper-pagination-bullet-title">'+(index+1)+'</span>' : '';

             return '<span class="' + className + '">'+indexLabel+'</span>';
             },
            renderFraction: function (currentClass, totalClass) {
                        return '<span class="' + currentClass + '"></span>' +
                               '<span class="separator">' + String(elementSettings[DCE_dynposts_skinPrefix+'fraction_separator']) + '</span>' +
                               '<span class="' + totalClass + '"></span>';
                        },
            renderProgressbar: function (progressbarFillClass) {
             return '<span class="' + progressbarFillClass + '"></span>';
             },
            renderCustom: function (swiper, current, total) {
             }
        },
        
        scrollbar: {
         el: '.swiper-scrollbar', // String with CSS selector or HTML element of the container with scrollbar.
         hide: Boolean( elementSettings[DCE_dynposts_skinPrefix+'scrollbar_hide'] ),    // Hide scrollbar automatically after user interaction
         draggable: Boolean( elementSettings[DCE_dynposts_skinPrefix+'scrollbar_draggable'] ), // Set to true to enable make scrollbar draggable that allows you to control slider position
         snapOnRelease: true, // Set to true to snap slider position to slides when you release scrollbar
         },
        mousewheel: Boolean( elementSettings[DCE_dynposts_skinPrefix+'mousewheelControl'] ), // true,
       
         keyboard: {
            enabled: Boolean( elementSettings[DCE_dynposts_skinPrefix+'keyboardControl'] ),
           
        },

        thumbs: {
            swiper: galleryThumbs
          },

        on: {
            init: function () {
            	isCarouselEnabled = true;
              	$('body').attr('data-carousel-'+id_scope, this.realIndex);

            },
            slideChange: function (e) {
              	$('body').attr('data-carousel-'+id_scope, this.realIndex);
            },
          }
    };
    if (elementSettings[DCE_dynposts_skinPrefix+'useAutoplay']) {
        dceSwiperOptions = $.extend(dceSwiperOptions, {autoplay: true});

        var autoplayDelay = Number(elementSettings[DCE_dynposts_skinPrefix+'autoplay']);
        if ( !autoplayDelay ) {
            autoplayDelay = 3000;
        }else{
            autoplayDelay = Number(elementSettings[DCE_dynposts_skinPrefix+'autoplay']);
        }
        dceSwiperOptions = $.extend(dceSwiperOptions, {autoplay: {delay: autoplayDelay, disableOnInteraction: Boolean(elementSettings[DCE_dynposts_skinPrefix+'autoplayDisableOnInteraction']), stopOnLastSlide: Boolean(elementSettings[DCE_dynposts_skinPrefix+'autoplayStopOnLast']) }});

    }
    var responsivePoints = dceSwiperOptions.breakpoints = {};
    responsivePoints[elementorBreakpoints.lg] = {
        slidesPerView: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView']) || 'auto',
        slidesPerGroup: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup']) || 1,
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween']) || 0,
        slidesPerColumn: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn']) || 1,
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween']) || 0,
        slidesOffsetBefore: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetBefore']) || 0,
        slidesOffsetAfter: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetAfter']) || 0,
    };
    responsivePoints[elementorBreakpoints.md] = {
        slidesPerView: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView']) || 'auto',
        slidesPerGroup: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup']) || 1,
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween']) || 0,
        slidesPerColumn: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn']) || 1,
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween_tablet']) || 0,
        slidesOffsetBefore: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetBefore_tablet']) || 0,
        slidesOffsetAfter: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetAfter_tablet']) || 0,
    };
    responsivePoints[elementorBreakpoints.xs] = {
        slidesPerView: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView_mobile']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerView']) || 'auto',
        slidesPerGroup: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup_mobile']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesPerGroup']) || 1,
        spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween_mobile']) || Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween']) || 0,
        slidesPerColumn: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn_mobile']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn_tablet']) || Number(elementSettings[DCE_dynposts_skinPrefix+'slidesColumn']) || 1,
    	spaceBetween: Number(elementSettings[DCE_dynposts_skinPrefix+'spaceBetween_mobile']) || 0,
        slidesOffsetBefore: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetBefore_mobile']) || 0,
        slidesOffsetAfter: Number(elementSettings[DCE_dynposts_skinPrefix+'slidesOffsetAfter_mobile']) || 0,
    };
    dceSwiperOptions = $.extend(dceSwiperOptions, responsivePoints);


    function initSwiperCarousel() {
        if(smsc) smsc.remove();

        if(dcePostsSwiper) {
          dcePostsSwiper.destroy();
        }

        if ( 'undefined' === typeof Swiper ) {
          const asyncSwiper = elementorFrontend.utils.swiper;

          new asyncSwiper( elementSwiper[0], dceSwiperOptions ).then( ( newSwiperInstance ) => {
            dcePostsSwiper = newSwiperInstance;
          } );
        } else {
          dcePostsSwiper = new Swiper( elementSwiper[0], dceSwiperOptions );
        }

	}
	if( elementSwiper.length )
	initSwiperCarousel();

	// Funzione di callback eseguita quando avvengono le mutazioni
	var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'attributes') {
	           if (mutation.attributeName === 'class') {
		            if (isCarouselEnabled) {
				      dcePostsSwiper.update();
				    }
		        }
	        }
	    }
	};
	observe_Dyncontel_element($scope[0], Dyncontel_MutationObserverCallback);
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
});

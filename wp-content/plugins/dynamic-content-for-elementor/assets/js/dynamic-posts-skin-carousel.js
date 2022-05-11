var Widget_DCE_Dynamicposts_carousel_Handler = function ($scope, $) {
    var smsc = null;
	var elementSettings = dceGetElementSettings($scope);
    var id_scope = $scope.attr('data-id');
	var id_post = $scope.attr('data-post-id');
    var elementSwiper = $scope.find('.dce-posts-container.dce-skin-carousel');
    let dcePostsSwiper = null;
    var isCarouselEnabled = false;
    var centeredSlides;
    var infiniteLoop;
    var slideInitNum = 0;
    var slidesPerView = Number(elementSettings[dceDynamicPostsSkinPrefix+'slidesPerView']);

	centeredSlides = Boolean( elementSettings[dceDynamicPostsSkinPrefix+'centeredSlides'] );
    infiniteLoop = Boolean( elementSettings[dceDynamicPostsSkinPrefix+'loop'] );

	if( elementSettings.carousel_match_height ) {
		if( elementSettings.style_items === 'template' ) {
			if( $scope.find( '.dce-post-block .elementor-inner-section' ).length ) {
				$scope.find('.dce-post-block').first().find('.elementor-inner-section').each((i) => {
					let $els = $scope.find('.dce-post-block').map((_,$e) => {
						return jQuery($e).find('.elementor-inner-section')[i]
					})
					$els.matchHeight()
				});
			} else {
				selector = '.dce-post-block .elementor-top-section';
				$(selector).matchHeight();
			}
		} else {
			selector = '.dce-post-block';
			$(selector).matchHeight();
		}
	}

    var dceSwiperOptions = {
		observer: true,
		observeParents: true,
        direction: String(elementSettings[dceDynamicPostsSkinPrefix+'direction_slider']) || 'horizontal', //vertical
        initialSlide: slideInitNum,
        reverseDirection: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'reverseDirection'] ),
        speed: Number(elementSettings[dceDynamicPostsSkinPrefix+'speed_slider']) || 300,
        autoHeight: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'autoHeight'] ), // Set to true and slider wrapper will adopt its height to the height of the currently active slide
        effect: elementSettings[dceDynamicPostsSkinPrefix+'effects'] || 'slide',
        cubeEffect: {
        	shadow: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'cube_shadow'] ),
        	slideShadows: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'slideShadows'] ),
            shadowOffset: 20,
            shadowScale: 0.94,
        },
        coverflowEffect: {
            rotate: 50,
            stretch: Number(elementSettings[dceDynamicPostsSkinPrefix+'coverflow_stretch']) || 0,
            depth: 100,
            modifier: Number(elementSettings[dceDynamicPostsSkinPrefix+'coverflow_modifier']) || 1,
            slideShadows: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'slideShadows'] ),
        },
        flipEffect: {
            rotate: 30,
            slideShadows: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'slideShadows'] ),
            limitRotation: true,
        },
        fadeEffect: {
		    crossFade: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'crossFade'] )
		  },

        initialSlide: Number(elementSettings[dceDynamicPostsSkinPrefix+'initialSlide']) || 0,
        slidesPerView: slidesPerView || 'auto',
        slidesPerGroup: Number(elementSettings[dceDynamicPostsSkinPrefix+'slidesPerGroup']) || 1, // Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1
        slidesPerColumn: Number(elementSettings[dceDynamicPostsSkinPrefix+'slidesColumn']) || 1, // Number of slides per column, for multirow layout
        spaceBetween: Number(elementSettings[dceDynamicPostsSkinPrefix+'spaceBetween']) || 0,
        slidesOffsetBefore: Number(elementSettings[dceDynamicPostsSkinPrefix+'slidesOffsetBefore']) || 0, // Add (in px) additional slide offset in the beginning of the container (before all slides)
        slidesOffsetAfter: Number(elementSettings[dceDynamicPostsSkinPrefix+'slidesOffsetAfter']) || 0, // Add (in px) additional slide offset in the end of the container (after all slides)
        slidesPerColumnFill: String(elementSettings[dceDynamicPostsSkinPrefix+'slidesPerColumnFill']) || 'row', // Could be 'column' or 'row'. Defines how slides should fill rows, by column or by row
        centerInsufficientSlides: true,
        centeredSlides: centeredSlides,
        centeredSlidesBounds: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'centeredSlidesBounds'] ),
        grabCursor: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'grabCursor'] ), //true,
        freeMode: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'freeMode'] ),
        freeModeMomentum: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'freeModeMomentum'] ),
        freeModeMomentumRatio: Number(elementSettings[dceDynamicPostsSkinPrefix+'freeModeMomentumRatio']) || 1,
        freeModeMomentumVelocityRatio: Number(elementSettings[dceDynamicPostsSkinPrefix+'freeModeMomentumVelocityRatio']) || 1,
        freeModeMomentumBounce: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'freeModeMomentumBounce'] ),
        freeModeMomentumBounceRatio: Number(elementSettings[dceDynamicPostsSkinPrefix+'speed']) || 1,
        freeModeMinimumVelocity: Number(elementSettings[dceDynamicPostsSkinPrefix+'speed']) || 0.02,
        freeModeSticky: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'freeModeSticky'] ),
        loop: infiniteLoop,
        navigation: {
            nextEl: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .next-' + id_scope : '.next-' + id_scope,
            prevEl: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .prev-' + id_scope : '.prev-' + id_scope,
        },
        pagination: {
            el: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .pagination-' + id_scope : '.pagination-' + id_scope,
            clickable: true,
            type: String(elementSettings[dceDynamicPostsSkinPrefix+'pagination_type']) || 'bullets',
            dynamicBullets: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'dynamicBullets'] ),

            renderBullet: function (index, className) {
            	var indexLabel = !Boolean( elementSettings[dceDynamicPostsSkinPrefix+'dynamicBullets']) && Boolean( elementSettings[dceDynamicPostsSkinPrefix+'bullets_numbers']) ? '<span class="swiper-pagination-bullet-title">'+(index+1)+'</span>' : '';
             	return '<span class="' + className + '">'+indexLabel+'</span>';
            },
            renderFraction: function (currentClass, totalClass) {
				return '<span class="' + currentClass + '"></span>' +
						'<span class="separator">' + String(elementSettings[dceDynamicPostsSkinPrefix+'fraction_separator']) + '</span>' +
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
			hide: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'scrollbar_hide'] ),    // Hide scrollbar automatically after user interaction
			draggable: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'scrollbar_draggable'] ), // Set to true to enable make scrollbar draggable that allows you to control slider position
			snapOnRelease: true, // Set to true to snap slider position to slides when you release scrollbar
			},
        mousewheel: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'mousewheelControl'] ), // true,
       		keyboard: {
            	enabled: Boolean( elementSettings[dceDynamicPostsSkinPrefix+'keyboardControl'] ),
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
    if (elementSettings[dceDynamicPostsSkinPrefix+'useAutoplay']) {
        dceSwiperOptions = $.extend(dceSwiperOptions, {autoplay: true});

        var autoplayDelay = Number(elementSettings[dceDynamicPostsSkinPrefix+'autoplay']);
        if ( !autoplayDelay ) {
            autoplayDelay = 3000;
        }else{
            autoplayDelay = Number(elementSettings[dceDynamicPostsSkinPrefix+'autoplay']);
        }
        dceSwiperOptions = $.extend(dceSwiperOptions, {autoplay: {delay: autoplayDelay, disableOnInteraction: Boolean(elementSettings[dceDynamicPostsSkinPrefix+'autoplayDisableOnInteraction']), stopOnLastSlide: Boolean(elementSettings[dceDynamicPostsSkinPrefix+'autoplayStopOnLast']) }});

    }

    dceSwiperOptions.breakpoints = dynamicooo.makeSwiperBreakpoints({
		slidesPerView: {
			elementor_key: 'slidesPerView',
			default_value: 'auto'
		},
		slidesPerGroup: {
			elementor_key: 'slidesPerGroup',
			default_value: 1
		},
		spaceBetween: {
			elementor_key: 'spaceBetween',
			default_value: 0,
		},
		slidesPerColumn: {
			elementor_key: 'slidesColumn',
			default_value: 1,
		},
		slidesOffsetBefore: {
			elementor_key: 'slidesOffsetBefore',
			default_value: 0,
		},
		slidesOffsetAfter: {
			elementor_key: 'slidesOffsetAfter',
			default_value: 0,
		}
	}, elementSettings, dceDynamicPostsSkinPrefix);

	if( 'dualcarousel' === dceDynamicPostsSkin ) {
		dceSwiperOptions.thumbs = {
			swiper: galleryThumbs,
		};
	}

    function initSwiperCarousel() {
        if(smsc) {
			smsc.remove();
		}

        if(dcePostsSwiper) {
          	dcePostsSwiper.destroy();
        }

        if ( 'undefined' === typeof Swiper ) {
			const asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper( jQuery( elementSwiper[0] ), dceSwiperOptions ).then( ( newSwiperInstance ) => {
				dcePostsSwiper = newSwiperInstance;
			} );
        } else {
          	dcePostsSwiper = new Swiper( jQuery( elementSwiper[0] ), dceSwiperOptions );
        }

	}
	if( elementSwiper.length ){
		initSwiperCarousel();
	}

	// Callback function executed when mutations occur
	var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'attributes' && mutation.attributeName === 'class' && isCarouselEnabled) {
				dcePostsSwiper.update();
	        }
	    }
	};
	dceObserveElement($scope[0], Dyncontel_MutationObserverCallback);
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products-on-sale.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.carousel', Widget_DCE_Dynamicposts_carousel_Handler);
});

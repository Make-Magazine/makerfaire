'use strict';

import 'isomorphic-fetch';
const $ = jQuery;
const $document = $( document );
const $window = $( window );
const utils = require( './utils.js' );

var scrollTimer = null;

$.mpfy = function( action, callOptions ) {
	var target = window.Mapify.instances;

	if ( ! $.isFunction( this ) ) {
		var instance = $( this ).data( 'mapify' );
		target = instance;
		if ( ! instance) {
			return this; // no map instance found for the selector
		}
	}
	if ( ! target ) {
		return this; // no target is available
	}

	var method = 'action' + action.charAt( 0 ).toUpperCase() + action.slice( 1 );
	if ( typeof $.mpfy[method] != 'undefined' ) {
		$.mpfy[ method ]( target, callOptions );
	} else {
		console.log( 'Mapify: Unknown action called: ' + action );
	}
	return this;
}
$.fn.mpfy = $.mpfy;

$.mpfy.actionRecenter = function( target ) {
	if ( typeof target.mapService == 'undefined' ) {
		for ( var id in target ) {
			var t = target[ id ];
			t.mapService.setCenter( t.settings.map.center );
			t.mapService.redraw();
		}
	} else {
		target.mapService.setCenter( target.settings.map.center );
		target.mapService.redraw();
	}
	$window.trigger( 'mapify.redraw' );
}

$.mpfy.actionSetStrings = function( target, strings ) {
	if ( typeof target.mapService == 'undefined'  ) {
		for ( var id in target ) {
			target[ id ].settings.strings = $.extend( target[ id ].settings.strings, strings );
		}
	} else {
		target.settings.strings = $.extend( target.settings.strings, strings );
	}
}

window.Mapify = {
	Map: require( './map.js' ),
	instances: [],
	classes: {
		popUpOpen : 'mpfy-p-popup-active',
		scrollShow : 'mpfy-p-scroll-show-scroll',
		openBodyClass : 'mpfy-popup-open'
	},
	ajaxUrl: window.wp_ajax_url,
	mobileWidth: 1022,
	isMobile: function() {
		var screenWidth = $(window).width();
		return (screenWidth > Mapify.mobileWidth) ? false : true;		
	},
	fixPopupHeightOnInit: function() {
		var imageLoader = $('.mpfy-p-slider-top img:first').attr('src');

		$('<img/>').attr('src', imageLoader).on('load', function() {

			$(this).remove(); // prevent memory leaks as @benweet suggested

			var directionHeight = $('.mpfy-p-local-info').outerHeight();
			var sliderHeight = $('.mpf-p-popup-holder .mpfy-p-slider').outerHeight();

			var scrollBarHeight = sliderHeight - directionHeight;

			$('.mpfy-p-content .mpfy-p-scroll').css('height', scrollBarHeight + 'px');

		});

		if ($('.jspVerticalBar').length) {
			$('.mpfy-p-holder .mpfy-p-content').addClass('mpf-p-show-overlay')
		};
	},
	initPopupSlider: function() {
		// Changed below conditional, to fix the modal height on embed-video only content
		if ( $('.mpfy-p-slider').length == 0 ) {
			return;
		}

		var windowResized;
		var $mainSlider = $('.mpfy-main-slider');
		var $navigationSlider = $('.mpfy-navigation-slider');

		// Re init main slider on browser resize
		$(window).on('resize', function() {
			clearTimeout(windowResized);
			
			windowResized = setTimeout(function(){
				Mapify.initSliders( $mainSlider, $navigationSlider );
			}, 250);
		});

		// On main slider change / slide
		$mainSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
			if ( $navigationSlider.hasClass('slick-initialized') ) {
				$navigationSlider.slick('slickGoTo', nextSlide);
				$navigationSlider.find('.slick-slide').removeClass('slick-current-slide');
				$navigationSlider.find('.slick-slide').eq(nextSlide).addClass('slick-current-slide');
			}	
		});

		// On click navigation item
		$navigationSlider.on('click', '.slick-slide', function() {
			var selectedIndex = $(this).index();
			$mainSlider.slick('slickGoTo', selectedIndex);
		} );

		// On arrow keypress
		$(document).keydown( function(e) {
			var rightArrow = 39; 
			var leftArrow = 37;

			if ( ( e.which == rightArrow || e.which == leftArrow ) && $mainSlider.hasClass('slick-initialized') ) {
				if ( e.which == rightArrow ) {
					$mainSlider.slick('slickNext');
				} else if ( e.which == leftArrow ) {
					$mainSlider.slick('slickPrev');
				}

				e.stopPropagation();
			}		
		});	

		// Initialize the navigation and main sliders
		Mapify.initSliders( $mainSlider, $navigationSlider );
	},	
	initSliders: function( $mainSlider, $navigationSlider ) {
		var showNavigationSlider = true;
		var navigationSlideralreadyShowedUp = $navigationSlider.hasClass('slick-initialized');

		// Initialize navigation slider we have more than one slide
		if ( 1 >= $navigationSlider.find('.holder').length || Mapify.isMobile() ) {
			showNavigationSlider = false;
		}

		/**
		 * Whether to show navigation slider or not. 
		 * Init the main slider after the navigation slider has been initialized.
		 * If the navigation slider can't be initialized, then show the main slider instead.
		 */
		if ( showNavigationSlider && ! navigationSlideralreadyShowedUp ) {
			$navigationSlider.on('init', function(slick) {
				$navigationSlider.find('.slick-slide').first().addClass('slick-current-slide');	
				Mapify.initMainSlider( $mainSlider, $navigationSlider );			
			} );
	
			$navigationSlider.not('.slick-initialized').show().slick({
				slidesToShow: 7,
				slidesToScroll: 7,
				dots: false,
				arrows: true,
				centerMode: false,
				infinite: false,
				focusOnSelect: false,
				accessibility: false,
				responsive: [
					{
						breakpoint: 415,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 3,
							arrows: false
						}
					}
				]
			});
		} else {
			if ( ! showNavigationSlider && navigationSlideralreadyShowedUp ) {
				$navigationSlider.hide().slick('unslick');
			}

			Mapify.initMainSlider( $mainSlider, $navigationSlider );
		}
	},
	initMainSlider: function( $mainSlider, $navigationSlider ) {		
		var navigationHeight = Mapify.getNavigationSliderHeight( $navigationSlider );
		var screenHeight = $(window).height();		
		var space = Mapify.isMobile() ? 0 : 50; // The `50` variable is the space for the modal
		var mainSliderHeight = screenHeight - navigationHeight - space;

		// reduce the slider height on mobile
		if ( Mapify.isMobile() ) {
			mainSliderHeight = mainSliderHeight * 70 / 100; 
		}

		// destroy before re-init
		if ( $mainSlider.hasClass('slick-initialized') ) {
			$mainSlider.hide().slick('unslick');
		}

		// init the main slider with the calculated height
		$mainSlider.show().height( mainSliderHeight ).slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: true,			
			adaptiveHeight: true,
			infinite: true,
			fade: true,
			dots: false,
			responsive: [
				{
					breakpoint: Mapify.mobileWidth,
					settings: {
						dots: ( 1 < $mainSlider.find('.holder').length ) ? true : false,
						fade: false
					}
				}
			]
		});
		
		// set modal content height
		Mapify.setContentHeight();

		// set video dimension
		Mapify.setVideoDimension($mainSlider);
	},
	getMainSliderHeight: function( $mainSlider ) {
		var mainSliderHeight = 0;

		if ( $mainSlider.length && $mainSlider.hasClass('slick-initialized') ) {
			mainSliderHeight = $mainSlider.outerHeight();
		}
		
		return mainSliderHeight;
	},
	getNavigationSliderHeight: function( $navigationSlider ) {
		var navigationSliderHeight = 0;

		if ( $navigationSlider.length && $navigationSlider.hasClass('slick-initialized') && ! Mapify.isMobile() ) {
			navigationSliderHeight = $navigationSlider.find('.slick-track .slick-slide').first().outerWidth();
			navigationSliderHeight = navigationSliderHeight + 20; // add the top and bottom paddings
		}
		
		return navigationSliderHeight;
	},
	getSliderHeight: function() {
		var sliderHeight = 0;

		if ( $('.mpfy-p-slider').length ) {
			sliderHeight = $('.mpfy-p-slider').outerHeight();
		}

		return sliderHeight;
	},	
	getSiderWidth: function() {
		var sliderWidth = 0;

		if ( $('.mpfy-p-slider').length ) {
			sliderWidth = $('.mpfy-p-slider').outerWidth();
		}

		return sliderWidth;
	},
	getLocationInfoHeight: function() {
		var locationInfoHeight = 0;

		if ( $('.mpfy-p-local-info').length ) {
			locationInfoHeight = $('.mpfy-p-local-info').outerHeight();
		}

		return locationInfoHeight;
	},
	getContentHeight: function() {
		var sliderHeight = Mapify.getSliderHeight();
		var locationInfoHeight = Mapify.getLocationInfoHeight();

		if (sliderHeight > locationInfoHeight) {
			return sliderHeight - locationInfoHeight;
		}
	},
	getVideoDimension: function( $mainSlider ) {
		var sliderWidth = Mapify.getSiderWidth();
		var mainSliderHeight = Mapify.getMainSliderHeight( $mainSlider );
		var videoHeight = sliderWidth * 9 / 16; // 16:9 video size
			videoHeight = mainSliderHeight > videoHeight ? videoHeight : mainSliderHeight;
		var videoMarginTop = (mainSliderHeight / 2) - (videoHeight / 2);

		return {
			width: sliderWidth,
			height: videoHeight,
			marginTop: videoMarginTop
		};
	},
	setContentHeight: function() {
		if ( ! $('.mpfy-p-scroll').length ) return;
		
		if ( Mapify.isMobile() ) {
			$('.mpfy-p-scroll').css( 'height', 'auto' );
		} else {
			$('.mpfy-p-scroll').height( Mapify.getContentHeight() );
		}
	},
	setVideoDimension: function( $mainSlider ) {
		var $videoIframe = $mainSlider.find('.video-holder iframe');

		if ( $videoIframe.length ) {
			var videoDimension = Mapify.getVideoDimension( $mainSlider );
			$videoIframe.css({
				'width': videoDimension.width,
				'height': videoDimension.height,
				'margin-top': videoDimension.marginTop,
			});
		}
	},
	closeTooltips: function() {
		$('.mpfy-tooltip').trigger({
			'type': 'tooltip_close'
		});
	},
	closeTooltipsAlt: function() {
		$('.mpfy-tooltip-image-orientation-left.mpfy-tooltip-with-thumbnail .mpfy-tooltip-image').each(function(){
			var introImage = $(this).find('img').attr('src')
			$(this).css('background-image', 'url('+introImage+')')
		});
	},
	updateSidebarForMobile: function() {
		if ($('#mpfy-p-sidebar-top').length == 0) {
			return false;
		}

		if ($(window).width() <= 985) {
			if ($('#mpfy-p-sidebar-top > *').length > 0) {
				$('#mpfy-p-sidebar-top > *').remove().appendTo($('#mpfy-p-sidebar-bottom'));
			}
		} else if ($('#mpfy-p-sidebar-bottom > *').length > 0) {
			$('#mpfy-p-sidebar-bottom > *').remove().appendTo($('#mpfy-p-sidebar-top'));
		}
	},
	onDocReady: function() {
		$document.on( 'click', 'a[data-mapify-action]', function( e ) {
			e.preventDefault();

			var mapId = $( this ).attr( 'data-mapify-map-id' );
			var action = $( this ).attr( 'data-mapify-action' );
			var value = $( this ).attr( 'data-mapify-value' );
			$document.trigger( 'mapify.action.' + action, {
				mapId: mapId,
				value: value
			} );
		} );

		$document.on( 'mapify.action.setMapTag', function( e, args ) {
			Mapify.closePopup();

			var $container = args.mapId ? $('.mpfy-map-id-' + args.mapId) : $('body');
			$container.find( 'select[name="mpfy_tag"] option[value="' + encodeURIComponent( args.value ) + '"]' ).each(function() {
				$( this )
					.closest( '.mpfy-container' )
					.data( 'mapify' )
					.filterByTag( args.value );
			} );
		} );

		$document.on( 'mapify.action.openPopup', function( e, args ) {
			var $a = $( 'a.mpfy-pin-id-' +  args.value + ':first' );
			if ( $a.length === 0 ) {
				return;
			}

			if ( $a.hasClass( 'mpfy-external-link' ) ) {
				var target = $a.attr( 'target' );
				if ( target == '_self' ) {
					window.location = $a.attr( 'href' );
				} else {
					window.open( $a.attr( 'href' ) );
				}
			} else {
				if ( $a.attr( 'href' ) && $a.attr( 'href' ) !== '#' ) {
					Mapify.openPopup( $a.attr( 'href' ) );
				}
			}
		} );

		$document.on( 'click', '.mpfy-p-popup-background', function( e ) {
			Mapify.closePopup();
		} );
	},

	onWinLoad: function() {

	},

	showLoading: function() {
		var loading = $('.mpfy-p-loading');
		loading.show();
	},

	hideLoading: function() {
		var loading = $('.mpfy-p-loading');
		loading.hide();
	},

	openPopup: function(url, callback) {
		Mapify.closeTooltips();
		var closePromise = Mapify.closePopup();
		var response = '';
		var popup = $('.mpfy-p-popup');

		var requestPromise = Promise.resolve($.get({url: url, headers: { 'X-Requested-With': 'XMLHttpRequest' }})).then( r => response = r );

		Mapify.showLoading();
		return Promise.all([closePromise, requestPromise])
			.then( () => {
				if ( $(response).find('.mpfy-p-slider-top img').length > 0 ) {
					var imageLoader = $(response).find('.mpfy-p-slider-top img:first').attr('src');
					$('<img/>').attr('src', imageLoader).on('load', function(){
						$(this).remove(); // prevent memory leaks as @benweet suggested

						Mapify.hideLoading();
						$('html, body').addClass(Mapify.classes.openBodyClass);

						var popup = $(response);
						popup.appendTo('body');

						popup.find('.mpfy-p-close').on('click touchstart', function(e){
							e.preventDefault();
							Mapify.closePopup();
						});

						$('body').trigger($.Event('mpfy_popup_opened', {
							mpfy: {
								'popup': popup
							}
						}));

						// slider
						popup.on('click touchstart', '.mpfy-p-slider-bottom a', function(e){
							e.preventDefault();

							var _pos = parseInt($(this).data('position'));
							if (!isNaN(_pos)) {
								$('.mpfy-p-slider-top ul.mpfy-p-slides').triggerHandler('slideTo', _pos);
							}
						});

						// show the popup

						popup.addClass(Mapify.classes.popUpOpen);




						setTimeout(function() {
							Mapify.initPopupSlider();
							// Mapify.updateSidebarForMobile();

						}, 100);


						if ( $('.mpfy-p-popup-active').length ) {
							if ($(window).width() > 767) {
								setTimeout(function() {
									$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-background')
								}, 200);

								setTimeout(function() {
									$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show')

								}, 700);
							} else {
								setTimeout(function() {
									$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-background')
									$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-mobile')

								}, 500);
							}


						};

						if (typeof stButtons != 'undefined') {
							stButtons.locateElements();
						}

						if (callback != 'undefined' && callback) {
							callback();
						}

						
					})
				} else {
					Mapify.hideLoading();
					$('html, body').addClass(Mapify.classes.openBodyClass);

					var popup = $(response);
					popup.appendTo('body');

					popup.find('.mpfy-p-close').on('click touchstart', function(e){
						e.preventDefault();
						Mapify.closePopup();
					});

					$('body').trigger($.Event('mpfy_popup_opened', {
						mpfy: {
							'popup': popup
						}
					}));

					// slider
					popup.on('click touchstart', '.mpfy-p-slider-bottom a', function(e){
						e.preventDefault();

						var _pos = parseInt($(this).data('position'));
						if (!isNaN(_pos)) {
							$('.mpfy-p-slider-top ul.mpfy-p-slides').triggerHandler('slideTo', _pos);
						}
					});

					// show the popup

					popup.addClass(Mapify.classes.popUpOpen);

					setTimeout(function() {
						Mapify.initPopupSlider();
						// Mapify.updateSidebarForMobile();

					}, 100);


					if ( $('.mpfy-p-popup-active').length ) {
						if ($(window).width() > 767) {
							setTimeout(function() {
								$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-background')
							}, 200);

							setTimeout(function() {
								$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show')

							}, 700);
						} else {
							/*$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-background')*/
							setTimeout(function() {
								$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-background')
								$('.mpfy-p-popup-active').addClass('mpfy-p-popup-show-mobile')

							}, 500);
						}
					};

					if (typeof stButtons != 'undefined') {
						stButtons.locateElements();
					}

					if (callback != 'undefined' && callback) {
						callback();
					}
				}			
			} );
	},

	closePopup: function() {
		if ($('.mpfy-p-popup').length == 0) {
			return Promise.resolve();
		}

		$('html, body').removeClass(Mapify.classes.openBodyClass);
		$('body').removeClass('mpf-location-info')
		$('.mpf-p-popup-holder').addClass('mpf-p-popup-remove');
		$('.mpf-p-popup-holder').removeClass('mpfy-p-popup-show');
		$('.mpfy-p-popup-active').removeClass('mpfy-p-popup-show-mobile')

		setTimeout(function() {
			$('.mpf-p-popup-holder').removeClass('mpf-p-popup-remove');
		}, 300);

		setTimeout(function() {
			$('.mpfy-p-popup-active').removeClass('mpfy-p-popup-show-background')
		}, 500);

		setTimeout(function() {
			$('.mpf-p-popup-holder').remove();
		}, 650);

		return Promise.delay( 650 );
	},

	showLocationInformation: function() {
		$('.mpfy-p-widget-title').on('click', function() {
			$('body').toggleClass('mpf-location-info')
			var $locationInfoHeight = $('.mpfy-p-widget-location .mpfy-location-details').outerHeight();
			var resetPadding = 0;

			if ( $('body').hasClass('mpf-location-info')) {
				$('.mpf-location-info .mpfy-p-popup .mpfy-title').css('padding-top', $locationInfoHeight + 'px');
			} else {
				$('.mpfy-p-popup .mpfy-title').css('padding-top', resetPadding + 'px');
			}
		});
	},

	Promise,
	preloadImage: utils.preloadImage,
};

$document.ready( Mapify.onDocReady );
$window.on( 'load', Mapify.onWinLoad );
$window.on( 'load', Mapify.closeTooltipsAlt );

$window.on( 'mpfy_popup_opened', function() {
	Mapify.showLocationInformation();
	Mapify.fixPopupHeightOnInit();
});

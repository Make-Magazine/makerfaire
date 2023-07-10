/**
 * Pinterest Pro
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 8.0
 */
( function( $ ) {
	"use strict";
	
	if (!debounce) {
		var debounce = function( func, wait ) {
			var timeout, args, context, timestamp;
			return function() {
				context = this;
				args = [].slice.call( arguments, 0 );
				timestamp = new Date();
				var later = function() {
					var last = ( new Date() ) - timestamp;
					if ( last < wait ) {
						timeout = setTimeout( later, wait - last );
					} else {
						timeout = null;
						func.apply( context, args );
					}
				};
				if ( ! timeout ) {
					timeout = setTimeout( later, wait );
				}
			};
		};
	}

	$(document).ready(function(){
		/**
		 * Applying additional Pinterest optimizations for images
		 */
		
		var essbCurrentPinImageCount = window.essbCurrentPinImageCount = 0;
		
		if (essb_settings.force_pin_description && essb_settings.pin_description) {
			$('img').each(function() {
				if (!$(this).data('pin-description')) $(this).attr('data-pin-description', essb_settings.pin_description);
			});
		}
				
		if (essb_settings.pin_pinid_active && essb_settings.pin_pinid) {
			$('img').each(function() {
				var hasPinID = $(this).data('pin-id') || '';
				if (!hasPinID || hasPinID == '') $(this).attr('data-pin-id', essb_settings.pin_pinid);
			});
		}
		
		if (essb_settings.pin_force_active && essb_settings.pin_force_image) {
			$('img').each(function() {
				$(this).attr('data-pin-media', essb_settings.pin_force_image);
				
				/**
				 * Forcing all custom parameters too
				 */
				if (!$(this).data('pin-description')) {
					var pinDescription  = '';
					if ($(this).attr('title')) pinDescription = $(this).attr('title');
					else if ($(this).attr('alt')) pinDescription = $(this).attr('alt');

					// give always priority of the custom description if set
					if (essbPinImages.force_custompin && !essbPinImages.custompin) essbPinImages.custompin = document.title;
					if (essbPinImages.custompin) pinDescription = essbPinImages.custompin;

					// if title is not genenrated it will use the Document Title
					if (pinDescription == '') pinDescription = document.title;
					
					$(this).attr('data-pin-description', pinDescription);
				}
				
				if (!$(this).data('pin-url')) $(this).attr('data-pin-url', encodeURI(document.URL));
			});
		}
		
		/**
		 * Pinterest Pro Gutenberg images integration
		 */
		
		$('.essb-block-image').each(function() {
			var pinID = $(this).data('essb-pin-id') || '',
				pinDesc = $(this).data('essb-pin-description') || '',
				pinAvoid = $(this).data('essb-pin-nopin') || '';						
			
			if (pinAvoid.toString() == 'true') {
				$(this).find('img').attr('data-pin-nopin', 'true');
				$(this).find('img').addClass('no_pin');
				return;
			}
			
			if (pinID != '') $(this).find('img').attr('data-pin-id', pinID);
			if (pinDesc != '') $(this).find('img').attr('data-pin-description', pinDesc);
		});

		/**
		 * Pinterest responsive thumbnail correction
		 */
		if (essb_settings.force_pin_thumbs) {
			// setting up a map of parsing images on site
			var essbReposiveImagesMap = window.essbReposiveImagesMap = {};

			// getting actual size of a single image
			var essbDetectAndLocateImageSize = window.essbDetectAndLocateImageSize = function(url, element, isResponsive) {
			  if (isResponsive) {
			    essbReposiveImagesMap[element].responsive[url] = {};
			  }
			  $("<img/>", {
			    load: function() {
			      if (essbReposiveImagesMap[element]) {
			        if (!isResponsive) {
			          essbReposiveImagesMap[element].originalSize = { 'w': this.width, 'h': this.height, 'done': true };
			          essbCompileTheDataPinImage(element);
			        } else {
			          essbReposiveImagesMap[element].responsive[url] = { 'w': this.width, 'h': this.height, 'done': true };
			          essbCompileTheDataPinImage(element);
			        }
			      }

			    },
			    src: url
			  });
			};

			var essbCompileTheDataPinImage = window.essbCompileTheDataPinImage = function(element) {
			  var totalImages = 0,
			    processImages = 0,
			    currentMaxW = 0,
			    imageURL = '';

			  for (var rImageURL in essbReposiveImagesMap[element].responsive) {
			    var dataObj = essbReposiveImagesMap[element].responsive[rImageURL] || {};
			    totalImages++;

			    if (!dataObj.done) continue;
			    processImages++;
			    if (currentMaxW == 0 || currentMaxW < dataObj.w) {
			      currentMaxW = dataObj.w;
			      imageURL = rImageURL;
			    }

			  }

			  if (totalImages == processImages && essbReposiveImagesMap[element].original != imageURL) {
			    if (essbReposiveImagesMap[element].originalSize.done) {
			      if (currentMaxW > essbReposiveImagesMap[element].originalSize.w) {
			        $('[data-pinpro-key="' + element + '"]').attr('data-pin-media', imageURL);
			        $('[data-pinpro-key="' + element + '"]').attr('data-media', imageURL);
			        $('[data-pinpro-key="' + element + '"]').attr('data-pin-url', window.location.href);
					$('[data-pinpro-key="' + element + '"]').removeClass('pin-process');
					$('[data-pinpro-key="' + element + '"]').each(essbPinImagesGenerateButtons);
			      }
			    }
			  }
			}

			$('img').each(function() {
			  var responsiveImages = $(this).attr('srcset') || '',
			    uniqueID = Math.random().toString(36).substr(2, 9),
			    element = uniqueID;

			  if (!responsiveImages || responsiveImages == '') return;

			  $(this).attr('data-pinpro-key', uniqueID);
				$(this).addClass('pin-process');
			  var responsiveSet = responsiveImages.split(', '),
			    originalImage = $(this).attr('src') || '',
			    foundReponsiveImage = '',
			    foundReponsiveSize = 0;

			  essbReposiveImagesMap[element] = {
			    source: element,
			    original: originalImage,
			    originalSize: {},
			    responsive: {}
			  };
			  essbDetectAndLocateImageSize(originalImage, element);
			  for (var i = 0; i < responsiveSet.length; i++) {
			    if (!responsiveSet[i]) continue;
			    var imageData = responsiveSet[i].split(' '),
			      imageURL = imageData[0] || '',
			      imageSize = (imageData[1] || '').replace('w', '');

			    if (!imageURL || !Number(imageSize)) continue;

			    essbDetectAndLocateImageSize(imageURL, element, true);

			  }

			});
		} // end forcing generation of responsive images

		/**
		 * Pinterest Images
		 */

		var essbPinImagesGenerateButtons = function() {
			var image = $(this);
			// the option to avoid button over images with links
			if (essbPinImages.nolinks && $(image).parents().filter("a").length) return;

			// avoid buttons on images that has lower size that setup
			if (image.outerWidth() < Number(essbPinImages.min_width || 0) || image.outerHeight() < Number(essbPinImages.min_height || 0)) return;
			// ignore the non Pinable images
			if (image.hasClass('no_pin') || image.hasClass('no-pin') || image.data('pin-nopin') || image.hasClass('pin-generated') || image.hasClass('pin-process') || image.hasClass('zoomImg') || image.hasClass('lazy-hidden')) return;

			var pinSrc = $(image).prop('src') || '',
				pinDescription = '', shareBtnCode = [],
				buttonStyleClasses = '', buttonSizeClasses = '',
				pinID = $(image).data('pin-id') || '';
			
			// additional check for the autoptimize svg placeholder preventing images from load
			// Pinterest also does not accept SVG images
			if (pinSrc.indexOf('data:image/svg+xml') > -1 || pinSrc.indexOf('data:image/gif') > -1) return;

			if (image.data('media')) pinSrc = image.data('media');
			if (image.data('lazy-src')) pinSrc = image.data('lazy-src');
			if (image.data('pin-media')) pinSrc = image.data('pin-media');

			if (image.data("pin-description")) pinDescription = image.data("pin-description");
			else if (image.attr('title')) pinDescription = image.attr('title');
			else if (image.attr('alt')) pinDescription = image.attr('alt');

			// give always priority of the custom description if set
			if (essbPinImages.force_custompin && !essbPinImages.custompin) essbPinImages.custompin = document.title;
			if (essbPinImages.custompin) pinDescription = essbPinImages.custompin;

			// if title is not genenrated it will use the Document Title
			if (pinDescription == '') pinDescription = document.title;
			
			var shareCmd = 'https://pinterest.com/pin/create/button/?url=' + encodeURI(document.URL) + '&is_video=false' + '&media=' + encodeURI(pinSrc) + '&description=' + encodeURIComponent(pinDescription);
			
			if (essbPinImages.legacy_share_cmd)
				shareCmd = 'https://pinterest.com/pin/create/bookmarklet/?url=' + encodeURI(document.URL) + '&media=' + encodeURI(pinSrc) + '&title=' + encodeURIComponent(pinDescription)+'&description=' + encodeURIComponent(pinDescription) + '&media=' + encodeURI(pinSrc);
			
			// encode the ' symbol separately
			if (shareCmd.indexOf("'") > -1) shareCmd = shareCmd.replace(/'/g, '%27');
			
			if (pinID != '') shareCmd = 'https://www.pinterest.com/pin/'+pinID+'/repin/x/';

			var imgClasses = image.attr('class'),
			    imgStyles = image.attr('style');

			if (essbPinImages ['button_style'] == 'icon_hover') {
				buttonStyleClasses = ' essb_hide_name';
			}
			if (essbPinImages ['button_style'] == 'icon') {
				buttonStyleClasses = ' essb_force_hide_name essb_force_hide';
			}
			if (essbPinImages ['button_style'] == 'button_name') {
				buttonStyleClasses = ' essb_hide_icon';
			}
			if (essbPinImages ['button_style'] == 'vertical') {
				buttonStyleClasses = ' essb_vertical_name';
			}

			if (essbPinImages['button_size']) buttonSizeClasses = ' essb_size_' + essbPinImages['button_size'];
			if (essbPinImages['animation']) buttonSizeClasses += ' ' + essbPinImages['animation'];
			if (essbPinImages['position']) buttonSizeClasses += ' essb_pos_' + essbPinImages['position'];
			
			if (essbPinImages['mobile_position']) buttonSizeClasses += ' essb_mobilepos_' + essbPinImages['mobile_position'];
			if (essbPinImages['visibility'] && essbPinImages['visibility'] == 'always') buttonSizeClasses += ' essb_always_visible';

			image.removeClass().attr('style', '').wrap('<div class="essb-pin" />');
			if (imgClasses != '') image.parent('.essb-pin').addClass(imgClasses);
			if (imgStyles != '') image.parent('.essb-pin').attr('style', imgStyles);
			
			// images count
			window.essbCurrentPinImageCount++;
			image.parent('.essb-pin').addClass('essb-pinid-' + window.essbCurrentPinImageCount.toString());

			if (essbPinImages.reposition) {
				var imgWidth = $(image).width();
				if (Number(imgWidth) && !isNaN(imgWidth) && Number(imgWidth) > 0) {
					image.parent('.essb-pin').css({'max-width': imgWidth+'px'});
				}
			}
			
			var uid = (new Date().getTime()).toString(36);		
			
			var iconMainClass = essbPinImages.svgIcon ? 'essb_icon_svg_pinterest' : 'essb_icon_pinterest';

			shareBtnCode.push('<div class="essb_links essb_displayed_pinimage essb_template_'+essbPinImages.template+buttonSizeClasses+' essb_'+uid+'" data-essb-position="pinit" data-essb-postid="'+(essb_settings.post_id || '')+'" data-essb-instance="'+uid+'">');
			shareBtnCode.push('<ul class="essb_links_list'+(buttonStyleClasses != '' ? ' ' + buttonStyleClasses : '')+'">');
			shareBtnCode.push('<li class="essb_item essb_link_pinterest nolightbox'+(essbPinImages['svgIcon'] ? ' essb_link_svg_icon' : '')+'">');
			shareBtnCode.push('<a class="nolightbox'+(essbPinImages['template_a_class'] ? ' ' + essbPinImages['template_a_class'] : '')+'" rel="noreferrer noopener nofollow" href="'+shareCmd+'" onclick="essb.window(&#39;'+shareCmd+'&#39;,&#39;pinpro&#39;,&#39;'+uid+'&#39;); return false;" target="_blank"><span class="essb_icon '+iconMainClass+(essbPinImages['template_icon_class'] ? ' ' + essbPinImages['template_icon_class'] : '')+'">'+(essbPinImages.svgIcon || '')+'</span><span class="essb_network_name">'+(essbPinImages['text'] ? essbPinImages['text'] : 'Pin')+'</span></a>');
			shareBtnCode.push('</li>');
			shareBtnCode.push('</ul>');
			shareBtnCode.push('</div>');

			image.after(shareBtnCode.join(''));
			image.addClass('pin-generated'); // adding class to avoid generating again the same information
			//essb.share_window
			//removing the lazyloading class if posted
			if (image.parent('.essb-pin').hasClass('lazyloading')) image.parent('.essb-pin').removeClass('lazyloading');

		}

		if (typeof(essbPinImages) != 'undefined' && $('body').hasClass('tcb-edit-mode')) essbPinImages.active = false;

		if (typeof(essbPinImages) != 'undefined' && essbPinImages.active) {
			// Begin detection of potential images and assign the pinterest generation
			if (!essbPinImages.min_width || !Number(essbPinImages.min_width)) essbPinImages.min_width = 300;
			if (!essbPinImages.min_height || !Number(essbPinImages.min_height)) essbPinImages.min_height = 300;
			
			// Integration with the mobile minimal width and height (if set)
			if ($(window).width() < 720) {
				if (Number(essbPinImages.min_width_mobile)) essbPinImages.min_width = Number(essbPinImages.min_width_mobile);
				if (Number(essbPinImages.min_height_mobile)) essbPinImages.min_height = Number(essbPinImages.min_height_mobile);
			}

			if ($('.essb-pin.tve_image').length) {
				$('.essb-pin.tve_image .essb_links').remove();
				$('.essb-pin img').removeClass('pin-generated');
			}

			// WP Rocket Lazy Videos set no-pin class to those images to prevent holding down
			$('.rll-youtube-player img').each(function() {
				$(this).addClass('no-pin');
			});
			
			// Hide on images option
			if (essbPinImages.hideon) {
				$(essbPinImages.hideon).each(function() {
					$(this).addClass('no-pin');
				});
			}

			window.addEventListener('LazyLoad::Initialized', function (e) {
				$('.rll-youtube-player img').each(function() {
					$(this).addClass('no-pin');
				});
			});

			var essbPinImagesDetect = function() {
				
				// WP Rocket Lazy Videos set no-pin class to those images to prevent holding down
				$('.rll-youtube-player img').each(function() {
					$(this).addClass('no-pin');
				});				
				
				// Hide on images option
				if (essbPinImages.hideon) {
					$(essbPinImages.hideon).each(function() {
						$(this).addClass('no-pin');
					});
				}

				if (essbPinImages.selector) {
					$(essbPinImages.selector).each(essbPinImagesGenerateButtons);
				}
				else {
					if (!$('.essb-pinterest-images').length) return;
					$('.essb-pinterest-images').parent().find('img').each(essbPinImagesGenerateButtons);
				}
			}

			if (essbPinImages.lazyload) $(window).on('scroll', debounce(essbPinImagesDetect, 10));

			if (!essbPinImages.optimize_load) setTimeout(essbPinImagesDetect, 1);
			else {
				const essbPinUserInteractions =["keydown","mousedown","mousemove","wheel","touchmove","touchstart","touchend"];
				essbPinUserInteractions.forEach(function(event) {
				    window.addEventListener(event, essbPinTriggerDOMListener, {passive:true});
				});
				
				document.addEventListener("visibilitychange", essbPinTriggerDOMListener);
				
				function essbPinTriggerDOMListener() {
				    //remove existing user interaction event listeners
					essbPinUserInteractions.forEach(function(event) {
				        window.removeEventListener(event, essbPinTriggerDOMListener, {passive:true});
				    });

				    //remove visibility change listener
				    document.removeEventListener("visibilitychange", essbPinTriggerDOMListener);

				    //add dom listner if page is still loading
				    if(document.readyState === 'loading') {
				        document.addEventListener("DOMContentLoaded", essbPinTriggerDetector);
				    }
				    else {
				        //trigger delayed script process
				    	essbPinTriggerDetector();
				    }
				}
				
				function essbPinTriggerDetector() {
					setTimeout(essbPinImagesDetect, 1);
				}
			}
		}

		if ((typeof(essbPinImages) != 'undefined' && !essbPinImages.active) || typeof(essbPinImages) == 'undefined') {
			if ($('.essb-pin.tve_image').length) {
				$('.essb-pin.tve_image .essb_links').remove();
				$('.essb-pin img').removeClass('pin-generated');
			}
		}
		
	});
} )( jQuery );

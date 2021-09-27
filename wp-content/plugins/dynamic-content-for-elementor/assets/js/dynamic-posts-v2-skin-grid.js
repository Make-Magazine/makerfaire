var Widget_DCE_Dynamicposts_grid_Handler = function ($scope, $) {

    var smsc = null;

	var elementSettings = get_Dyncontel_ElementSettings($scope);
    var id_scope = $scope.attr('data-id');

	var grid = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper');
    var postsItems = grid.find('.dce-post.dce-post-item');
    var masonryGrid = null;
    var isMasonryEnabled = false;

	// MASONRY
	function initMasonry() {
	    imagesLoaded(postsItems, activeMasonry);
	}
	function activeMasonry(){
	    masonryGrid = grid.masonry({
		  itemSelector: '.dce-post-item',
		});
		isMasonryEnabled = true;
	}
	function layoutMasonry(){
		if( elementSettings[DCE_dynposts_skinPrefix+'grid_type'] != 'masonry' ){
			masonryGrid.masonry('destroy');
			isMasonryEnabled = false;
		}else{
			masonryGrid.masonry();
		}
	}

	if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
		byRow = elementSettings.grid_match_height_by_row || elementSettings.grid_filters_match_height_by_row;
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
				byRow: byRow ?? false,
			}
		);
	}

	if(smsc) {
		smsc.remove();
	}

	if( elementSettings[DCE_dynposts_skinPrefix+'grid_type'] == 'masonry' ) {
		activeMasonry();
	}
    
  	// InfiniteScroll
	if (elementSettings.infiniteScroll_enable){
        var elementorElement = '.elementor-element-' + id_scope;
        var is_history = Boolean( elementSettings.infiniteScroll_enable_history ) ? 'replace' : false;
        var grid_container = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper.dce-wrapper-grid');
        var $layoutMode = elementSettings[DCE_dynposts_skinPrefix+'grid_type'];
        var $grid = grid_container.isotope({
            itemSelector: '.dce-post-item',
            layoutMode: 'masonry' === $layoutMode ? 'masonry' : 'fitRows',
            sortBy: 'original-order',
            percentPosition: true,
            masonry: {
                columnWidth: '.dce-post-item'
            }
        });

		// Apply link to template when layout is complete
		if( 
			false === elementorFrontend.isEditMode() &&
			'yes' === elementSettings.templatemode_linkable
		){
			grid_container.on( 'append.infiniteScroll', function( event, title, path ) {
				$scope.find('.dce-post.dce-post-item[data-post-link]').click(function() {
				window.location.assign( $(this).attr("data-post-link") );
				return false;
				});
			});
		}

		// Match Height when layout is complete
		if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
			grid_container.on( 'append.infiniteScroll', function( event, title, path ) {
				$(selector).matchHeight(
					{
						byRow: byRow ?? false,
					}
				);
				grid_container.isotope('layout');
			});
		}

		// Reload the template after using Infinite Scroll
		if( 'template' === elementSettings.style_items ) {
			grid_container.on( 'append.infiniteScroll', function( event, title, path ) {
				if ( elementorFrontend) {
					if ( elementorFrontend.elementsHandler.runReadyTrigger ) {
						var widgets = $('.elementor-widget-dce-dynamicposts-v2').find('.elementor-widget');
						widgets.each(function (i) {
							elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
						});
					}
				}

				// Add inline CSS for background url
				var allArticles = document.querySelectorAll(".elementor-widget-dce-dynamicposts-v2 .elementor-section, .elementor-widget-dce-dynamicposts-v2 .elementor-column");
				allArticles.forEach(function(article) {
					dce.addCssForBackground( article );
				});
			});
		}

        var iso = $grid.data('isotope');

        if (jQuery(elementorElement + ' .pagination__next').length) {
            var infiniteScroll_options = {
                path: elementorElement + ' .pagination__next',
                history: is_history,
                append: elementorElement + ' .dce-post.dce-post-item',
                outlayer: iso,
                status: elementorElement + ' .page-load-status',
                hideNav: elementorElement + '.pagination',
                scrollThreshold: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
                loadOnScroll: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
                onInit: function () {
                    this.on('load', function () {
                    });
                }
            }
            if (elementSettings.infiniteScroll_trigger == 'button') {
                // load pages on button click
                infiniteScroll_options['button'] = elementorElement + ' .view-more-button';
            }
            infScroll = grid_container.infiniteScroll(infiniteScroll_options);

            // fix for infinitescroll + masonry
            var nElements = jQuery(elementorElement + ' .dce-post-item:visible').length; // initial length

            grid_container.on( 'append.infiniteScroll', function( event, response, path, items ) {
                setTimeout(function(){
                    var nElementsVisible = jQuery(elementorElement + ' .dce-post-item:visible').length;
                    if (nElementsVisible <= nElements) {
                        // force another load
                        grid_container.infiniteScroll('loadNextPage');
                    }
                }, 1000);
            });
        }
    }

    // Scroll Reveal
    var on_scrollReveal = function(){
		var runRevAnim = function(dir){
        	var el = $( this );
            var i = $( this ).index();

            if(dir == 'down'){
               	setTimeout(function(){
               		el.addClass('animate');
               	}, 100 * i);
                // play
            }else if(dir == 'up'){
                el.removeClass('animate');
                // stop
            }
        };
        var waypointRevOptions = {
            offset: '100%',
            triggerOnce: false
        };
        elementorFrontend.waypoint($scope.find('.dce-post-item'), runRevAnim, waypointRevOptions);

    };
    on_scrollReveal();

	// Funzione di callback eseguita quando avvengono le mutazioni
	var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'attributes') {
	           if (mutation.attributeName === 'class') {
		            if (isMasonryEnabled) {
				      layoutMasonry();
				    }
		        }
	        }
	    }
	};
	observe_Dyncontel_element($scope[0], Dyncontel_MutationObserverCallback);
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid', Widget_DCE_Dynamicposts_grid_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
});

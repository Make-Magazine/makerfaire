var DCE_dynposts_skin = '';
var DCE_dynposts_skinPrefix = '';
var Widget_DCE_Dynamicposts_base_Handler = function($scope, $) {

  if ($scope.hasClass('elementor-widget-dce-dynamicposts-v2')) {

    DCE_dynposts_skin = $scope.attr('data-widget_type').split('.')[1];
	if(DCE_dynposts_skin === 'grid-filters') {
		DCE_dynposts_skin = 'grid_filters';
	}
    DCE_dynposts_skinPrefix = DCE_dynposts_skin + '_';

    var elementSettings = get_Dyncontel_ElementSettings($scope);
    var id_scope = $scope.attr('data-id');
    var type_scope = $scope.attr('data-type');

    // FIT IMAGES RATIO
    function fitImage($post) {
      	var $imageParent = $post.find('.dce-img');

        $image = $imageParent.find('img');
        image = $image[0];

		if (!image) {
			return;
		}

      	var imageParentRatio = $imageParent.outerHeight() / $imageParent.outerWidth(),
    	imageRatio = image.naturalHeight / image.naturalWidth;
      	$imageParent.toggleClass('dce-fit-img', imageRatio < imageParentRatio);
    }

    function toggleRatio() {
      var itemRatio = getComputedStyle($scope[0], ':after').content;
      $scope.find('.dce-posts-container').toggleClass('dce-is_ratio', !!itemRatio.match(/\d/));
    }

    function fitImages() {
      toggleRatio();
      $scope.find('.dce-post-image').each(function() {
        var $post = $(this);
        $image = $post.find('.dce-img img');

        fitImage($post);
        $image.on('load', function() {
        	fitImage($post);
        });
      });
    }
    // Run on load
    fitImages();

    // HOVER EFFECTS
    var blocks_hoverEffects = $scope.find('.dce-post-block.dce-hover-effects');
    if (blocks_hoverEffects.length) {
      blocks_hoverEffects.each(function(i, el) {
        $(el).on("mouseenter touchstart", function() {
          $(this).find('.dce-hover-effect-content').removeClass('dce-close').addClass('dce-open');
        });
        $(el).on("mouseleave touchend", function() {
          $(this).find('.dce-hover-effect-content').removeClass('dce-open').addClass('dce-close');
        });
      });
    }
    // Linkable Template
    if(
      false === elementorFrontend.isEditMode()
	  && 'yes' === elementSettings.templatemode_linkable
    ){
      $scope.find('.dce-post.dce-post-item[data-post-link]').click(function() {
        window.location.assign( $(this).attr("data-post-link") );
        return false;
      });
    }
    // Funzione di callback eseguita quando avvengono le mutazioni
    var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
      for (var mutation of mutationsList) {
        if (mutation.type == 'childList') {
          console.log('A child node has been added or removed.');
        } else if (mutation.type == 'attributes') {
          var attribute_of_target = getComputedStyle(mutation.target, ':after').content;

          if (attribute_of_target && attribute_of_target != 'none') {
            fitImages();

          }
          if (attribute_of_target == 'none') {
            toggleRatio();
          }
        }
      }
    };

    observe_Dyncontel_element($scope[0], Dyncontel_MutationObserverCallback);
  }

};

jQuery(window).on('elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction('frontend/element_ready/widget', Widget_DCE_Dynamicposts_base_Handler);
});

// Re init layout after ajax request on Search&Filter Pro
(function ( $ ) {
	"use strict";
	$(function () {
		$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
			if ( elementorFrontend) {
				if ( elementorFrontend.elementsHandler.runReadyTrigger && SF_LDATA.extensions.indexOf('search-filter-elementor') < 0 ) {
					elementorFrontend.elementsHandler.runReadyTrigger(data.targetSelector);
				}
			}
		});
	});
}(jQuery));

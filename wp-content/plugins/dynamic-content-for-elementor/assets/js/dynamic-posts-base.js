var dceDynamicPostsSkin = '';
var dceDynamicPostsSkinPrefix = '';
var Widget_DCE_Dynamicposts_base_Handler = function($scope, $) {

	dceDynamicPostsSkin = $scope.attr('data-widget_type').split('.')[1];
	if(dceDynamicPostsSkin === 'grid-filters') {
		dceDynamicPostsSkin = 'grid_filters';
	}
    dceDynamicPostsSkinPrefix = dceDynamicPostsSkin + '_';
    var elementSettings = dceGetElementSettings($scope);

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

	// Fit Images Ratio
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

	function fitImages() {
		var itemRatio = $scope.find('.dce-post-image figure').first().data('image-ratio');

		if( !itemRatio ) {
			return;
		}
		$scope.find('.dce-posts-container').toggleClass('dce-is-ratio', true);
		$scope.find('.dce-post-image').each(function() {
			var $post = $(this);
			$image = $post.find('.dce-img img');
			fitImage($post);
			$image.on('load', function() {
				fitImage($post);
			});
		});
	}
};

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.carousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.crossroadsslideshow', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.dualcarousel', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid-filters', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.gridtofullscreen3d', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.smoothscroll', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.timeline', Widget_DCE_Dynamicposts_base_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid', Widget_DCE_Dynamicposts_base_Handler);
});

// Re init layout after ajax request on Search&Filter Pro
(function ( $ ) {
	"use strict";
	$( document ).on( "sf:ajaxfinish", ".searchandfilter", function( e, data ) {
		if ( window.elementorFrontend && window.elementorFrontend.elementsHandler && window.elementorFrontend.elementsHandler.runReadyTrigger) {
			var runReadyTrigger = window.elementorFrontend.elementsHandler.runReadyTrigger;
			runReadyTrigger( data.targetSelector );
			
			// Re-init elementor widgets inside the new results
			var ajaxTarget = $( data.targetSelector );
			if ( ajaxTarget.length > 0 ) {
				ajaxTarget.find( '.elementor-widget' ).each( function () {
					runReadyTrigger( $( this ) );
				} );
			}
		}
	});
}(jQuery));

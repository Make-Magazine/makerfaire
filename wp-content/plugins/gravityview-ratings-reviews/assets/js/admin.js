/**
 * Part of GravityView_Ratings_Reviews plugin. This script is enqueued when in
 * admin page.
 *
 * globals jQuery, GV_RATINGS_REVIEWS_ADMIN
 */
(function($){

	"use strict";

	var self = $.extend({
		'viewCommentSelector'          : '#preview-action .preview',
		'editCommentTitleSelector'     : '.wrap h2:first',
		'listReviewsTitleSelector'     : '.wrap h2:first',
		'ratingsReviewsmetaboxSelector': '#gravityview_ratings_reviews_entry'
	}, GV_RATINGS_REVIEWS_ADMIN);

	self.init = function() {
		self._holder = '.gv-star-rate-holder';
		self._star = '.gv-star-rate';
		self._input = '.gv-star-rate-field';

		if (self.isEditReviewScreen()) {
			self.changeEditCommentTitle();
			self.removeViewCommentButton();
			self.bindRate();
		}

		if (self.isListReviewsScreen()) {
			self.changeListReviewsTitle();
		}

		if (self.isGravityViewScreen()) {
			self.bindToHideViewConfig();
			self.bindToShowViewConfig();
		}
	};

	self.removeViewCommentButton = function() {
		$(self.viewCommentSelector).remove();
	};

	self.changeEditCommentTitle = function() {
		$(self.editCommentTitleSelector).text(self.edit_review_title);
	};

	self.bindRate = function() {
		self.starHolder = $( self._holder );
		self.star = self.starHolder.find( self._star );
		self.star
			.on('click touchend', self.starRateReview)
			.on('mouseover', self.mouseOverStar)
			.on('mouseout', self.mouseOutStar);

		self.initStarVal();

		self.voteHolder = $('.gv-vote-rate-holder');
		self.vote = $('a.gv-vote-up, a.gv-vote-down', self.voteHolder);
		self.vote.on('click touchend', self.voteRateReview);
		self.initVoteVal();
	};

	self.initStarVal = function() {
		if (self.starHolder.length) {
			self.setStars();
		}
	};

	self.mouseOverStarHolder = function(e) {
		$('.gv-star-rate').removeClass('gv-rate-mutated');
	};

	self.mouseOutStarHolder = function(e) {
		self.setStars();
	};

	self.setStars = function() {
		var val = parseInt( $('#gv_review_rate').val(), 10 );
		if (val >= 1 && val <= 5) {
			$('.gv-star-rate:eq(' + (val-1) + ')').trigger('click');
		}
	};

	/**
	 * When hovering over a star rating field, process display if there's an existing rating
	 *
	 * If there's no existing rating, only CSS is used.
	 *
	 * 1. If the hovered star is the same value as the current star rating, return
	 * 2. If the hovered star is less than, regenerate the star display
	 *
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	self.mouseOverStar = function(e) {
		var $star = $( this ),
			$holder = $star.parents( self._holder ),
			$siblings = $holder.find( self._star ),
			index = $siblings.index( $star );

		$siblings.removeClass( 'gv-rate-mutated' ).each( function ( i, el ){
			var $el = $( el );

			// Dont fill the star if bigger then the one hovering
			if ( i >= index + 1 ){
				return;
			}

			$el.addClass( 'gv-rate-mutated' );
		} );
	};

	/**
	 * When mousing out, we always want to display the current rating.
	 * @param  {[type]} e [description]
	 * @return {void}
	 */
	self.mouseOutStar = function(e) {
		var $star = $( this ),
			$holder = $star.parents( self._holder ),
			$siblings = $holder.find( self._star ),
			index = $siblings.index( $star );

		$siblings.not( '[data-selected="1"]' ).removeClass( 'gv-rate-mutated' );
	};

	/**
	 * Save star rating
	 * @param  {jQuery Event} e
	 * @return {boolean}   false
	 */
	self.starRateReview = function(e) {
		var $star = $( this ),
			$holder = $star.parents( self._holder ),
			$siblings = $holder.find( self._star ),
			$field = $holder.siblings( self._input ),
			index = $siblings.index( $star );

		$field.val( index + 1 );

		$siblings.removeClass( 'gv-rate-mutated' ).attr( 'data-selected', 0 ).each( function ( i, el ){
			var $el = $( el );
			// Dont fill the star if bigger then the one cliked
			if ( i >= index + 1 ){
				return;
			}

			$el.addClass( 'gv-rate-mutated' ).attr( 'data-selected', 1 );
		} );
	};

	self.initVoteVal = function() {
		if (!self.voteHolder.length) return;

		var vote = parseInt($('#gv_review_rate').val(), 10);

		if (vote === -1) {
			$('.gv-vote-down').addClass('gv-rate-mutated').removeClass('on');
		} else if (vote === 1) {
			$('.gv-vote-up').addClass('gv-rate-mutated').removeClass('on');
		}
	};

	self.voteRateReview = function(e) {
		var el = $(e.target),
			vote = 0;

		if ( el.hasClass('gv-vote-up') ) {
			vote = el.hasClass('gv-rate-mutated') ? 0 : 1;
		} else if ( el.hasClass('gv-vote-down') ) {
			vote = el.hasClass('gv-rate-mutated') ? 0 : -1;
		}

		$('.gv-rate-mutated', self.voteHolder).not(el).removeClass('gv-rate-mutated');
		el.toggleClass('gv-rate-mutated');

		$('#gv_review_rate').val(vote);
		$('.gv-vote-rating-text', self.voteHolder).text(vote);

		return false;
	};

	self.changeListReviewsTitle = function() {
		$(self.listReviewsTitleSelector).html(self.list_reviews_title);
	};

	self.bindToHideViewConfig = function() {
		$(document).bind('gv_admin_views_hideViewConfig', self.hideMetabox);
	};

	self.bindToShowViewConfig = function() {
		$(document).bind('gv_admin_views_showViewConfig', self.showMetabox);
	};

	self.showMetabox = function() {
		$(self.ratingsReviewsmetaboxSelector).slideDown(150);
	};

	self.hideMetabox = function() {
		$(self.ratingsReviewsmetaboxSelector).slideUp(150);
	};

	self.isEditReviewScreen = function() {
		return ('comment' === self.screen_id && self.is_review);
	};

	self.isListReviewsScreen = function() {
		return self.is_list_reviews;
	};

	self.isGravityViewScreen = function() {
		return 'gravityview' === self.screen_id;
	};

	$(self.init);

}(jQuery));

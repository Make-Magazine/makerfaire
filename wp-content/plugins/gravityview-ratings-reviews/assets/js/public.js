/**
 * Part of GravityView_Ratings_Reviews plugin. This script is enqueued from
 * front-end view that has ratings-reviews setting enabled.
 *
 * globals jQuery, GV_RATINGS_REVIEWS, _
 */
( function( $ ) {
	'use strict';

	var self = $.extend( {
		'fieldClass': '.comment-form-gv-review,.gv-star-rate-holder,.gv-vote-rating',
		'respondId': '#respond',
		'respondTempId': '#gv-ratings-reviews-temp-respond',
		'commentParentId': '#comment_parent',
		'commentId': '#comment',
		'commentLabel': 'label[for="comment"]:first',
		'commentSubmit': 'input[name="submit"]:first',
		'replyTextId': '#reply-title',
		'cancelId': '#cancel-comment-reply-link',
	}, GV_RATINGS_REVIEWS );

	/**
	 * Initialization when DOM is ready.
	 */
	self.init = function() {
		self._holder = '.gv-star-rate-holder, .gv-vote-rate-holder';

		// Only run it once.
		if ( $( self._holder ).length <= 0 ) {
			return;
		}

		self._star = '.gv-star-rate';
		self._vote = '.gv-vote-up, .gv-vote-down';
		self._input = '.gv-star-rate-field';
		self._text = '.gv-vote-rating-text';
		self._mutated = '.gv-rate-mutated';

		self.$field = $( self._input );

		self.reply = $( '.comment-reply-login, .comment-reply-link' );
		self.reply.removeAttr( 'onclick' );
		self.reply.on( 'click', self.moveForm );

		self.starHolder = $( self._holder );
		self.star = self.starHolder.find( self._star );
		self.star
			.on( 'click touchend', self.starRateReview )
			.on( 'mouseover', self.mouseOverStar )
			.on( 'mouseout', self.mouseOutStar );

		if ( 0 !== parseInt( self.$field.val(), 10 ) ) {
			self.star.eq( parseInt( self.$field.val(), 10 ) - 1 ).trigger( 'click' );
		}

		self.voteHolder = $( '.gv-vote-rate-holder' );

		$.merge( self.starHolder, self.voteHolder ).each( function () {
			var $element = $( this );
			var viewId = $element.data( 'view-id' );
			var entryId = $element.data( 'entry-id' );

			if ( self.limit_one_review_per_person && localStorage.getItem( `${ viewId }-${ entryId }-rated` ) === 'true' ) {
				$element.parent().html( self.already_reviewed_text );
			}
		} );

		self.vote = $( 'a.gv-vote-up, a.gv-vote-down', self.voteHolder );
		self.vote.on( 'click touchend', self.voteRateReview );

		self.originalReplyText = self.getText( $( self.replyTextId ) );
		self.originalCancelReplyText = self.getText( $( self.cancelId ) );
		self.originalCommentLabelText = self.getText( $( self.commentLabel ) );
		self.originalCommentSubmitText = $( self.commentSubmit ).val();

		$(self.commentSubmit).on( 'click', self.validateRatingRequired );
		self.$field.on('change', self.setRatingRequiredState );
		self.editReview();
	};

	self.setStars = function() {
		if(!$('.gv-star-rate-field').length){
			return;
		}
		$('.gv-star-rate-field').each( function(i,el){
			var val = parseInt( $( el ).val(), 10 );
			if (val >= 1 && val <= 5) {
				$(el).parent().find('.gv-star-rate:eq(' + (val-1) + ')').trigger('click');
			}
		});


	};

	self.reviewTimer = function(){
		var commentElements = $('.review-edit-duration');
		commentElements.each(function() {
			var commentElement = $(this);
			var timeLeft = new Date(commentElement.data('edit-time-left'));
			if(timeLeft > 0){
				self.editReviewTimer(commentElement, timeLeft);
			}
		});
	};


	self.editReviewTimer = function(commentElement, review_edit_duration){

		function startTimer() {
			var h = Math.floor(review_edit_duration / 3600);
			var m = Math.floor(review_edit_duration % 3600 / 60);
			var s = Math.floor(review_edit_duration % 3600 % 60);

			var hDisplay = h > 0 ? h + ":" : "";
			var mDisplay = h > 0 || m > 0 ? (m<10 && h > 0 ? "0" : "") + m + ":" : "";
			var sDisplay = (s<10 ? "0" : "") + s;
			commentElement.text(hDisplay + mDisplay + sDisplay);

			review_edit_duration--;

			if (review_edit_duration >= 0) {
				setTimeout(startTimer, 1000);
			}else{
				commentElement.fadeOut().remove();
			}
		}

		startTimer();
	};

	self.editReview = function(){
		self.setStars();
		self.reviewTimer();
		$('a.gv-review-edit-button').on('click',function(e){
			e.preventDefault();
			var commentId = $(this).data('commentid');
			$('#gv-review-'+commentId).toggle();
		});

		$('button.gv-review-edit-cancel').on('click',function(e){
			e.preventDefault();
			var commentId = $(this).data('commentid');
			$(this).parents('.gv-review-edit-area').hide().prev().show();
		});

	};

	/**
	 * Handler when user clicks comment to a review.
	 */
	self.moveForm = function( e ) {
		var review       = $( e.target ).parent().parent(),
				respond      = $( self.respondId ),
				temp         = $( self.respondTempId ),
				parent       = $( self.commentParentId ),
				reviewFields = $( self.fieldClass );

		if ( ! temp.length ) {
			temp = $( '<div></div>' );
			temp.attr( 'id', self.respondTempId.substr( 1 ) ); // substr to remove the hash.
			temp.hide();
			respond.parent().append( temp );
		}

		review.append( respond );
		parent.val( review.data( 'review_id' ) );

		reviewFields.hide();
		self.replaceReplyText( self.comment_to_review_text, self.cancel_comment_to_review_text, self.comment_label_when_reply, self.comment_submit_when_reply );

		$( self.cancelId ).show().on( 'click', self.cancelReply );
		$( self.commentId ).focus();

		return false;
	};

	/**
	 * Get the text of an element, without any children elements screwing it up
	 * @param  {[type]} el [description]
	 * @return {[type]}    [description]
	 */
	self.getText = function( el ) {
		el = $( el );

		return el.clone().children().remove().end().text();
	};

	self.replaceReplyText = function( replyText, cancelText, commentLabelText, commentSubmitText ) {
		var reply         = $( self.replyTextId ),
				cancel        = $( self.cancelId ),
				commentLabel  = $( self.commentLabel ),
				commentSubmit = $( self.commentSubmit );

		reply.contents().each( function() {
			if ( this.nodeType === this.TEXT_NODE ) {
				this.nodeValue = this.nodeValue.trim();
				if ( this.nodeValue.length ) {
					this.nodeValue = replyText;
				}
			}
		} );

		cancel.text( cancelText );
		commentLabel.text( commentLabelText );
		commentSubmit.val( commentSubmitText );
	};

	/**
	 * Handler when user clicks cancel to reply.
	 */
	self.cancelReply = function( e ) {
		var temp         = $( self.respondTempId ),
				respond      = $( self.respondId ),
				parent       = $( self.commentParentId ),
				cancel       = $( e.target ),
				reviewFields = $( self.fieldClass );

		if ( ! temp.length || ! respond.length ) {
			return;
		}

		parent.val( 0 );

		temp.parent().append( respond );
		temp.remove();

		self.replaceReplyText( self.originalReplyText, self.originalCancelReplyText, self.originalCommentLabelText, self.originalCommentSubmitText );

		cancel.hide();
		cancel.off( 'click', self.cancelReply );

		reviewFields.show();

		return false;
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
	self.mouseOverStar = function( e ) {
		var $star     = $( this ),
				$holder   = $star.parents( self._holder ),
				$siblings = $holder.find( self._star ),
				index     = $siblings.index( $star );

		$siblings.removeClass( 'gv-rate-mutated' ).each( function( i, el ) {
			var $el = $( el );

			// Dont fill the star if bigger than the one hovered
			if ( i >= index + 1 ) {
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
	self.mouseOutStar = function( e ) {
		// If this entry is already rated, set the original rating
		var currentRating = parseInt( $( this ).parent().attr( 'data-star-rating' ), 10 );

		if ( self.multiple_entries && currentRating >= 0 ) {
			var starCount = 0;

			$( this ).parent().find( '.gv-star-rate' ).each( function() {
				$( this ).removeClass( 'gv-rate-mutated' );

				if ( starCount < currentRating ) {
					$( this ).addClass( 'gv-rate-mutated' );
				}

				++starCount;
			} );

			return;
		}

		var $star     = $( this ),
				$holder   = $star.parents( self._holder ),
				$siblings = $holder.find( self._star );

		$siblings.removeClass( 'gv-rate-mutated' ).filter( '[data-selected="1"]' ).addClass( 'gv-rate-mutated' );
	};

	/**
	 * Save star rating
	 * @param  {jQuery} e
	 * @return {boolean}   false
	 */
	self.starRateReview = function( e ) {
		e.stopPropagation();

		var $star     = $( this ),
				$holder   = $star.parents( self._holder ),
				$siblings = $holder.find( self._star ),
				$field    = $holder.siblings( self._input ),
				index     = $siblings.index( $star );

		$field.val( index + 1 ).trigger( 'change' );

		$siblings.removeClass( 'gv-rate-mutated' ).attr( 'data-selected', 0 ).each( function( i, el ) {
			var $el = $( el );
			// Don't fill the star if bigger than the one clicked
			if ( i >= index + 1 ) {
				return;
			}

			$el.addClass( 'gv-rate-mutated' ).attr( 'data-selected', 1 );
		} );

		if ( self.multiple_entries ) {
			var $parent = $( this ).parent();
			var currentRating = $parent.attr( 'data-star-rating' );
			var newRating = $parent.find( '.gv-rate-mutated' ).length;
			var response_error = false;
			var viewId = $parent.attr( 'data-view-id' );
			var entryId = $parent.attr( 'data-entry-id' );

			$parent.attr( 'data-star-rating', newRating );

			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: self.ajaxurl,
				data: {
					action: self.action,
					nonce: self.nonce,
					comment_id: $parent.attr( 'data-comment-id' ),
					view_id: viewId,
					entry_id: entryId,
					update_rating: $parent.attr( 'data-rated' ),
					rating: newRating,
					type: 'star',
				},
				beforeSend: function() {
					$parent.animate( { opacity: 0.5 }, 200 ).attr( 'aria-busy', 'true' );
				},
				success: function( response ) {
					if ( ! response.success ) {
						response_error = true;
					} else {
						localStorage.setItem( `${ viewId }-${ entryId }-rated`, 'true' );

						if ( self.limit_one_review_per_person ) {
							$(`[data-view-id="${viewId}"][data-entry-id="${entryId}"]`).parent().html(self.already_reviewed_text);
						}

						$parent.attr( 'data-rated', true );
						$parent.attr( 'data-comment-id', response.data.comment_id );
					}
				},
				error: function() {
					response_error = true;
				},
				complete: function() {
					if ( response_error ) {
						$parent.attr( 'data-star-rating', currentRating );
					}

					$parent.animate( { opacity: 1 }, 200 ).attr( 'aria-busy', 'false' );
					$( e.target ).trigger( 'mouseout' );
				},
			} );
		}
	};

	self.voteRateReview = function( e ) {
		var $vote     = $( this ),
				$holder   = $vote.parents( self._holder ),
				$siblings = $holder.find( self._vote ),
				$field    = $holder.siblings( self._input ),
				$text     = $holder.find( self._text ),
				$current  = $siblings.filter( self._mutated ),

				vote      = ( $vote.is( $current ) ? 0 : ( $vote.hasClass( 'gv-vote-up' ) ? 1 : -1 ) ),
				text      = ( 0 === vote ? self.vote_zero : ( 1 === vote ? self.vote_up : self.vote_down ) ),
				title     = ( 0 === vote ? self.vote_zero : _.template( self.vote_text_format )( { number: vote } ) );

		if ( self.ajaxurl ) {
			var $parent = $( this ).parent();
			var currentVote = $parent.attr( 'data-vote' );
			var currentText = $text.text();
			var currentTitle = $text.attr( 'title' );
			var response_error = false;
			var viewId = $parent.attr( 'data-view-id' );
			var entryId = $parent.attr( 'data-entry-id' );

			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: self.ajaxurl,
				data: {
					action: self.action,
					nonce: self.nonce,
					view_id: viewId,
					comment_id: $parent.attr( 'data-comment-id' ),
					entry_id: entryId,
					update_rating: $parent.attr( 'data-rated' ),
					type: 'vote',
					rating: String( vote ),
				},
				beforeSend: function() {
					$parent.animate( { opacity: 0.5 }, 200 ).attr( 'aria-busy', 'true' );
				},
				success: function( response ) {
					if ( ! response.success ) {
						response_error = true;
					} else {
						localStorage.setItem( `${ viewId }-${ entryId }-rated`, 'true' );

						if ( self.limit_one_review_per_person ) {
							$(`[data-view-id="${viewId}"][data-entry-id="${entryId}"]`).parent().html(self.already_reviewed_text);
						}

						$parent.attr( 'data-rated', true );
						$parent.attr( 'data-comment-id', response.data.comment_id );
					}
				},
				error: function() {
					response_error = true;
				},
				complete: function() {
					if ( response_error ) {

						if ( currentVote == 1 ) {
							$holder.find( 'a.gv-vote-up' ).addClass( 'gv-rate-mutated' );
						} else if ( currentVote == -1 ) {
							$holder.find( 'a.gv-vote-down' ).addClass( 'gv-rate-mutated' );
						}

						$text.text( currentText ).attr( 'title', currentTitle );
					}
					$parent.animate( { opacity: 1 }, 200 ).attr( 'aria-busy', 'false' );
				},
			} );
		}

		$siblings.removeClass( 'gv-rate-mutated' );

		if ( 0 !== vote ) {
			$vote.addClass( 'gv-rate-mutated' );
		}

		$text.text( text ).attr( 'title', title );
		$field.val( vote ).trigger( 'change' );

		return false;
	};

	self.validateRatingRequired = function (e) {
		if ( parseInt( self.$field.data( 'required' ) ) === 1 && self.$field.val() === '' ) {
			e.preventDefault();
			self.setRatingRequiredState();
		}
	};

	self.setRatingRequiredState = function () {
		self.$field.closest( self.fieldClass ).toggleClass( 'is-required', self.$field.val() === '' );
	};

	// Datatables Init
	$(window).on('gravityview-datatables/event/draw', function() {
		$( self.init );
	});

	// Init!
	$( self.init );

}( jQuery, _ ) );
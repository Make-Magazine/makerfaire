/**
 * @namespace WPGMZA
 * @module ThumbsRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	WPGMZA.ThumbsRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.input = $("<input type='hidden'/>");
		this.element.append(this.input);
		
		this.container = $("<span class='wpgmza-rating-thumbs-container'></span>");
		this.element.append(this.container);
		
		this.downvote = $("<span class='wpgmza-downvote'><i class='fa fa-thumbs-down' aria-hidden='true'></i></span>");
		this.container.append(this.downvote);
		
		this.upvote = $("<span class='wpgmza-upvote'><i class='fa fa-thumbs-up' aria-hidden='true'></i></span>");
		this.container.append(this.upvote);
		
		this.setOptions(options);
		
		this.downvote.on("click", function(event) {
			self.onButtonClicked(event);
		});
		
		this.upvote.on("click", function(event) {
			self.onButtonClicked(event);
		});
	}
	
	WPGMZA.extend(WPGMZA.ThumbsRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.ThumbsRatingWidget.prototype, "value", {
		
		get: function() {
			
			return this.lastClickedRating;
			
		},
		
		set: function(value) {
			
			if(value == this.min)
			{
				this.downvote.addClass("wpgmza-remembered-rating");
			}
			else if(value == this.max)
			{
				this.upvote.addClass("wpgmza-remembered-rating");
			}
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ThumbsRatingWidget.prototype, "averageRating", {
		
		get: function() {
			return this._averageRating;
		},
		
		set: function(value) {
			this.setBackgroundValue(value);
			this._averageRating = value;
		}
		
	});
	
	WPGMZA.ThumbsRatingWidget.prototype.setBackgroundValue = function(value)
	{
		var f = (value - this.min) / (this.max - this.min);
		var percent = f * 100;
		var prefixes = [
			"-moz-",
			"-webkit-",
			""
		];
		var color = this.getAverageDisplayColor();
		
		for(var i = 0; i < prefixes.length; i++)
		{
			
			var propertyValue = prefixes[i] + "linear-gradient(to right, " + color + " 0%, " + color + " " + percent + "%, transparent " + percent + "%, transparent 100%)";
			
			this.container.css({
				"background": propertyValue
			});
			
		}
	}
	
	WPGMZA.ThumbsRatingWidget.prototype.getAverageDisplayColor = function()
	{
		//If you have selected a color for the gradient bar then return marker_rating_thumb_widget_average_rating_color
		if(WPGMZA.settings.marker_rating_thumb_widget_average_rating_color)
		{
			return WPGMZA.settings.marker_rating_thumb_widget_average_rating_color;
		}

		//else use fixed color
		else
		{
			return "#3cc639";
		}
	}
	
	WPGMZA.ThumbsRatingWidget.prototype.onButtonClicked = function(event)
	{
		if(this.isLoading)
			return;
		
		if($(event.currentTarget).hasClass("wpgmza-upvote"))
			this.lastClickedRating = this.max;
		else
			this.lastClickedRating = this.min;
		
		$(this.element).find(".wpgmza-remembered-rating").removeClass(".wpgmza-remembered-rating");
		$(event.currentTarget).addClass("wpgmza-remembered-rating");
		
		this.trigger("rated");
	}
	
});
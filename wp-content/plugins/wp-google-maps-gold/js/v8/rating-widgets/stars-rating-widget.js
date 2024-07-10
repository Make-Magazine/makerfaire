/**
 * @namespace WPGMZA
 * @module StarsRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	WPGMZA.StarsRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.input = $("<input type='hidden'/>");
		this.element.append(this.input);
		
		this.container = $("<span class='wpgmza-rating-stars-container'></span>");
		this.element.append(this.container);
		
		this.background = $("<span class='wpgmza-background'></span>");
		this.container.append(this.background);
		
		this.foreground = $("<span class='wpgmza-foreground'></span>");
		this.container.append(this.foreground);
		
		for(var amount = this.min; amount <= this.max; amount++)
		{
			this.background.append("&#x2606;");
			this.foreground.append("&#x2605;");
		}
		
		this.element.append(" ");
		// this.element.append($("<i class='fa fa-users' aria-hidden='true'></i>"));
		this.element.append($("<span class='wpgmza-num-ratings'></span>"));
		
		this.visibilityTestInterval = setInterval(function() {
			
			var width = $(self.background).width();
			var height = $(self.background).height();
			
			if(width == 0)
				return;
			
			var css = {
				"width": width + "px",
				"height": height + "px"
			};
			
			self.container.css(css);
			
			self.element.find(".wpgmza-num-ratings").css({
				"left": css.width
			});
			
			clearInterval(self.visibilityTestInterval);
			self.showStars(self.averageRating);
			
		}, 100);
		
		this.container.on("mousemove", function(event) {
			self.onMouseMove(event);
		});
		
		this.container.on("mouseout", function(event) {
			self.onMouseOut(event);
		});
		
		this.container.on("click", function(event) {
			
			if(self.isLoading)
				return;
			
			self.trigger("rated");
			
		});
		
		this.setOptions(options);
	}
	
	WPGMZA.extend(WPGMZA.StarsRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.StarsRatingWidget.prototype, "value", {
		
		get: function() {
			return this.lastHoveredRating;
		},
		
		set: function() {
			// TODO: Remember the value to display on hover?
		}
		
	});
	
	Object.defineProperty(WPGMZA.StarsRatingWidget.prototype, "averageRating", {
		
		get: function() {
			return this._averageRating;
		},
		
		set: function(value) {
			
			this._averageRating = value;
			this.showStars(value);
			
		}
		
	});
	
	WPGMZA.StarsRatingWidget.prototype.showStars = function(amount)
	{
		/*
		var f = (amount - this.min) / (this.max - this.min);
		var w = $(this.background).width();
		*/
		//var s = w / this.step;
		
		/*var i = /*Math.ceil*/ /*(f * this.max);*/
		/*
		var px = (i / this.max) * $(this.container).width();
		
		//var percent = (i / this.max) * 100;
		//this.foreground.css({"width": percent + "%"});
		
		this.foreground.css({"width": px + "px"});
		*/

		var f = amount / this.max;
      	var w = $(this.background).width();
      	var percent = f * w;

      	this.foreground.css({"width": percent + "px"});
	}
	
	WPGMZA.StarsRatingWidget.prototype.onMouseMove = function(event)
	{
		this.container.css({
			"width": $(this.background).width(),
			"height": $(this.background).height()
		});
		
		var x = event.pageX - $(this.container).offset().left;
		var w = $(this.background).width();
		var f = (x / w);
		
		var w = $(this.background).width();
		
		var s = w / this.step;
		var i = Math.ceil(f * this.max);
		var px = (i / this.max) * $(this.container).width();
		
		this.lastHoveredRating = i;
		
		this.foreground.css({"width": px + "px"});
		
	}
	
	WPGMZA.StarsRatingWidget.prototype.onMouseOut = function(event)
	{
		this.showStars(this.averageRating);
	}
	
});
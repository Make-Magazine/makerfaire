/**
 * @namespace WPGMZA
 * @module GradientRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	// TODO: Consider renaming to BarRatingWidget
	
	WPGMZA.GradientRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.input = $("<input type='hidden'/>");
		this.element.append(this.input);
		
		this.container = $("<div class='wpgmza-rating-gradient-container'></div>");
		this.container.css({
			overflow: "hidden"
		});
		
		this.element.append(this.container);
		
		this.gradient = $("<div class='wpgmza-rating-gradient'></div>");
		// this.gradient.css({"width": "75%"});

		//start color for the gradient bar
		var start_color = WPGMZA.settings.marker_rating_gradient_widget_start_color;
		//end color for the gradient bar
		var end_color = WPGMZA.settings.marker_rating_gradient_widget_end_color ;

			//colors added to the gradient bar
			this.gradient.css({

			"background": start_color, /* Old browsers */
			"background": "-moz-linear-gradient(left, " +  start_color +  " 0%, " +  end_color +  " 128px)", /* FF3.6-15 */
			"background": "-webkit-linear-gradient(left, " + start_color + " 0%, " + end_color + " 128px)" ,/* Chrome10-25,Safari5.1-6 */
			"background": "linear-gradient(to right, " + start_color + " 0%, " + end_color +  " 128px)", /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			"filter": "progid:DXImageTransform.Microsoft.gradient( startColorstr= " + start_color + ", endColorst'= "  + end_color + ", GradientType = 1 )", /* IE6-9 */
		});

		this.container.append(this.gradient);
		
		this.element.append(" ");
		this.element.append($("<i class='fa fa-users' aria-hidden='true'></i>"));
		this.element.append(" ");
		this.element.append($("<span class='wpgmza-num-ratings'></span>"));
		
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
	
	WPGMZA.extend(WPGMZA.GradientRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.GradientRatingWidget.prototype, "averageRating", {
		
		get: function() {
			return this._averageRating;
		},
		
		set: function(value) {
			
			this.showValue(value);
			this._averageRating = value;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.GradientRatingWidget.prototype, "value", {
		
		get: function() {
			return this.lastHoveredRating;
		},
		
		set: function() {
			// TODO: Remember the value to display on hover?
		}
		
	});
	
	WPGMZA.GradientRatingWidget.prototype.showValue = function(value)
	{
		var f = (value - this.min) / (this.max - this.min);
		var percent = f * 100;
		
		this.gradient.css({"width": percent + "%"});
	}
	
	WPGMZA.GradientRatingWidget.prototype.onMouseMove = function(event)
	{
		if(this.isLoading)
			return;
		
		var x = event.pageX - $(this.container).offset().left;
		var y = event.pageY - $(this.container).offset().top;
		var w = $(this.container).width();
		
		var f		= (x / w);
		var v		= f * this.max;
		
		var rating	= this.min + (Math.round(v / this.step) * this.step);
		var percent	= ((rating - this.min) / (this.max - this.min)) * 100;
		
		this.lastHoveredRating = rating;
		
		this.gradient.css({"width": percent + "%"});
	}
	
	WPGMZA.GradientRatingWidget.prototype.onMouseOut = function(event)
	{
		this.showValue(this.averageRating);
	}
	
});
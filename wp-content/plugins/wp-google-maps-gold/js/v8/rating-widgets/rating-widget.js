/**
 * @namespace WPGMZA
 * @module RatingWidget
 * @requires-external WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.RatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.call(this);
		
		this._averageRating = 0;
		this._numRatings = 0;
		
		this.userGuid = Cookies.get("wpgmza-user-guid");
		if(!this.userGuid)
		{
			this.userGuid = WPGMZA.guid();
			Cookies.set("wpgmza-user-guid", this.userGuid);
		}
		
		this.element = $("<span class='wpgmza-rating'></span>");
		
		this.on("rated", function(event) {
			self.onRated(event);
		});
	}
	
	WPGMZA.RatingWidget.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.RatingWidget.prototype.constructor = WPGMZA.RatingWidget;
	
	WPGMZA.RatingWidget.STYLE_RADIOS		= "radios";
	WPGMZA.RatingWidget.STYLE_GRADIENT		= "gradient";
	WPGMZA.RatingWidget.STYLE_STARS			= "stars";
	WPGMZA.RatingWidget.STYLE_THUMBS		= "thumbs";
	
	WPGMZA.RatingWidget.createInstance = function(options, forceStyle)
	{
		var style = WPGMZA.RatingWidget.getSelectedStyle();
		
		if(forceStyle)
			style = forceStyle;
		
		switch(style)
		{
			case WPGMZA.RatingWidget.STYLE_GRADIENT:
				return new WPGMZA.GradientRatingWidget(options);
				break;
			
			case WPGMZA.RatingWidget.STYLE_STARS:
				return new WPGMZA.StarsRatingWidget(options);
				break;
			
			case WPGMZA.RatingWidget.STYLE_THUMBS:
				return new WPGMZA.ThumbsRatingWidget(options);
				break;
			
			default:
				return new WPGMZA.RadiosRatingWidget(options);
				break;
		}
	}

	WPGMZA.RatingWidget.getSelectedStyle = function()
	{
		return WPGMZA.settings.marker_rating_widget_style;
	}
	
	WPGMZA.RatingWidget.getRatingWidgetForMarker = function(marker)
	{
		var options = {
			type: "marker",
			id: marker.id
		};
		
		if(marker.rating)
		{
			options.averageRating = marker.rating.average;
			options.numRatings = marker.rating.count;
		}
		
		var widget = WPGMZA.RatingWidget.createInstance(options);
		
		return widget;
	}
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "min", {
		
		get: function() {
			
			return parseInt(WPGMZA.settings.minimum_rating);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "max", {
		
		get: function() {
			
			return parseInt(WPGMZA.settings.maximum_rating);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "step", {
		
		get: function() {
			
			return parseFloat(WPGMZA.settings.rating_step);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "numRatings", {
		
		get: function() {
			
			return $(this.element).find(".wpgmza-num-ratings").text();
			
		},
		
		set: function(value) {
			
			$(this.element).find(".wpgmza-num-ratings").text(value);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "localStorageKey", {
		
		get: function() {
			
			return "wpgmza_rating_" + this.type + "_" + this.id;
			
		}
		
	});
	
	WPGMZA.RatingWidget.prototype.setOptions = function(options)
	{
		for(var key in options)
			this[key] = options[key];
		
		this.recallSubmittedRating();
	}
	
	WPGMZA.RatingWidget.prototype.getAJAXRequestParameters = function()
	{
		var params = {};
		
		if(this.type)
			params.type = this.type;
		if(this.id)
			params.id = this.id;
		
		params.userGuid = this.userGuid;
		params.amount = this.value;
		
		return params;
	}
	
	WPGMZA.RatingWidget.prototype.onRated = function(event)
	{
		var self = this;
		
		var params = {
			method: "POST",
			data: this.getAJAXRequestParameters(),
			success: function(data, status, xhr) {
				
				self.averageRating = data.average;
				self.numRatings = data.count;
				
				self.storeSubmittedRating(self.value);
				
			},
			complete: function() {
				self.showPreloader(false);
			}
		};
		
		this.showPreloader(true);
		WPGMZA.restAPI.call("/ratings/", params);
	}
	
	WPGMZA.RatingWidget.prototype.showPreloader = function(show)
	{
		this.isLoading = show;
		
		if(show)
			$(this.element).addClass("wpgmza-loading");
		else
			$(this.element).removeClass("wpgmza-loading");
	}
	
	WPGMZA.RatingWidget.prototype.recallSubmittedRating = function()
	{
		if(!window.localStorage)
			return;
		
		var item = localStorage.getItem(this.localStorageKey);
		
		if(!item)
			return;
		
		item = JSON.parse(item);
		
		this.value = item.amount;
	}
	
	WPGMZA.RatingWidget.prototype.storeSubmittedRating = function(amount)
	{
		if(!window.localStorage)
			return;
		
		localStorage.setItem(this.localStorageKey, JSON.stringify({
			amount: amount
		}));
	}
	
	$(window).on("infowindowopen.wpgmza", function(event) {
		var marker = event.target.feature;
		var map = marker.map;
		
		if(!map.settings.enable_marker_ratings)
			return;	// Ratings not enabled
		
		if(marker.isIntegrated)
			return;	// Can't leave ratings for integrated markers
		
		var widget = WPGMZA.RatingWidget.getRatingWidgetForMarker(marker);
		
		if(!$(event.target.element).children().hasClass('wpgmza-rating'))
			$(event.target.element).children().last().after(widget.element);
		
	});
	
	$(document.body).on("markerlistingupdated.wpgmza", function(event) {
		
		var map = event.target.map;
		
		$(event.target.element).find(".wpgmza-rating-container").each(function(index, el) {
		
			var marker_id = $(el).closest("[data-marker-id]").attr("data-marker-id");
			var marker = map.getMarkerByID(marker_id);
			var widget = WPGMZA.RatingWidget.getRatingWidgetForMarker(marker);
		
			if(map.settings.enable_marker_ratings)
				$(el).append(widget.element);
			else
				$(el).remove();
			
		});
		
	});
	
});
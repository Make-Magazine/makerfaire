/**
 * @namespace WPGMZA
 * @module RadiosRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	WPGMZA.RadiosRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.element.append(this.min + " ");
		this.name = WPGMZA.guid();
		
		for(var amount = this.min; amount <= this.max; amount += this.step)
		{
			var radio = $("<input type='radio'/>");
			
			radio.attr("name", this.name);
			radio.val(amount);
			
			this.element.append(radio);
		}
		
		this.element.append(this.max);
		
		this.element.append(" (");
		this.element.append($("<i class='fa fa-dot-circle-o' aria-hidden='true'></i> <span class='wpgmza-average-rating'></span> - <i class='fa fa-users' aria-hidden='true'></i> <span class='wpgmza-num-ratings'></span>"));
		this.element.append(")");
		
		this.setOptions(options);
		
		$(this.element).on("change", "input", function(event) {
			self.trigger("rated");
		});
	}
	
	WPGMZA.extend(WPGMZA.RadiosRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.RadiosRatingWidget.prototype, "value", {
		
		get: function()
		{
			return this.element.find("input:checked").val();
		},
		
		set: function(value)
		{
			this.element.find("input:checked").prop("checked", false);
			this.element.find("input[value='" + value + "']").prop("checked", true);
		}
		
	});
	
	Object.defineProperty(WPGMZA.RadiosRatingWidget.prototype, "averageRating", {
		
		get: function()
		{
			return this._averageRating;
		},
		
		set: function(value)
		{
			if(isNaN(value) || !value)
				value = 0;
			
			var display = parseFloat(value).toFixed(2);
			
			$(this.element).find(".wpgmza-average-rating").text(display);
			
			this._averageRating = value;
		}
		
	});
	
	WPGMZA.RadiosRatingWidget.prototype.showPreloader = function(show)
	{
		WPGMZA.RatingWidget.prototype.showPreloader.apply(this, arguments);
		
		$(this.element).find("input").prop("disabled", show);
	}
	
});
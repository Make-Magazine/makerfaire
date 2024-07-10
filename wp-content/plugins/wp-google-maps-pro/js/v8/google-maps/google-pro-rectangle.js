/**
 * @namespace WPGMZA
 * @module GoogleProRectangle
 * @requires WPGMZA.GoogleRectangle
 */
jQuery(function($) {
	
	WPGMZA.GoogleProRectangle = function(options, googleRectangle){
		var self = this;
		
		WPGMZA.GoogleRectangle.call(this, options, googleRectangle);

		google.maps.event.addListener(this.googleRectangle, "mouseover", function(event) {
			self.trigger("mouseover");
		});
		
		google.maps.event.addListener(this.googleRectangle, "mouseout", function(event) {
			self.trigger("mouseout");
		});
	}
	
	WPGMZA.GoogleProRectangle.prototype = Object.create(WPGMZA.GoogleRectangle.prototype);
	WPGMZA.GoogleProRectangle.prototype.constructor = WPGMZA.GoogleProRectangle;
	
});
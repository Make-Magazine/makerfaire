/**
 * @namespace WPGMZA
 * @module GoogleProCircle
 * @requires WPGMZA.GoogleCircle
 */
jQuery(function($) {
	
	WPGMZA.GoogleProCircle = function(options, googleCircle){
		var self = this;
		
		WPGMZA.GoogleCircle.call(this, options, googleCircle);

		google.maps.event.addListener(this.googleCircle, "mouseover", function(event) {
			self.trigger("mouseover");
		});
		
		google.maps.event.addListener(this.googleCircle, "mouseout", function(event) {
			self.trigger("mouseout");
		});
	}
	
	WPGMZA.GoogleProCircle.prototype = Object.create(WPGMZA.GoogleCircle.prototype);
	WPGMZA.GoogleProCircle.prototype.constructor = WPGMZA.GoogleProCircle;
	
});
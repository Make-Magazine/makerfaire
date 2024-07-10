/**
 * @namespace WPGMZA
 * @module UseMyLocationButton
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.UseMyLocationButton = function(target, options)
	{
		var self = this;
		
		this.options = {};
		if(options)
			this.options = options;
		
		this.target = $(target);
		
		var buttonClass = 'button-secondary';
		if(WPGMZA.settings.internalEngine !== 'legacy'){
			buttonClass = 'wpgmza-button';
		}

		if(!WPGMZA.InternalEngine.isLegacy() && !(parseInt(WPGMZA.is_admin) === 1)){
			this.element = $("<button class='wpgmza-use-my-location " + buttonClass + "' type='button' title='" + WPGMZA.localized_strings.use_my_location + "'>" + this.getSVG() + "</button>");
		} else {
			this.element = $("<button class='wpgmza-use-my-location " + buttonClass + "' type='button' title='" + WPGMZA.localized_strings.use_my_location + "'><i class='fa fa-crosshairs' aria-hidden='true'></i></button>");
		}

		this.element.on("click", function(event) {
			self.onClick(event);
		});
	}
	
	WPGMZA.UseMyLocationButton.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.UseMyLocationButton.prototype.constructor = WPGMZA.UseMyLocationButton;
	
	WPGMZA.UseMyLocationButton.prototype.onClick = function(event)
	{
		var self = this;
		
		WPGMZA.getCurrentPosition(function(position) {
			
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			
			self.target.val(lat + ", " + lng);
			self.target.trigger("change");
			
			var geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.geocode({latLng: {lat: lat, lng: lng}}, function(results) {
				
				if(results && results.length)
					self.target.val(results[0]);
				
			});
			
		});
	}

	WPGMZA.UseMyLocationButton.prototype.getSVG = function(){
		var html = '<svg width="40" height="40" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="wpgmza-use-loc">';
		html += 	 '<circle cx="30" cy="30" r="20.5" stroke-width="5" class="circle_outer"/>';
		html += 	 '<circle cx="30" cy="30" r="12" class="circle_inner"/>';
		html += 	 '<line x1="30" y1="7" x2="30" y2="3" stroke-width="6" stroke-linecap="round" class="line" />';
		html += 	 '<line x1="30" y1="57" x2="30" y2="53" stroke-width="6" stroke-linecap="round" class="line" />';
		html += 	 '<line x1="53" y1="30" x2="57" y2="30" stroke-width="6" stroke-linecap="round" class="line" />';
		html += 	 '<line x1="3" y1="30" x2="7" y2="30" stroke-width="6" stroke-linecap="round" class="line" />';
		html += '</svg>';

		return html;
	}
	
});
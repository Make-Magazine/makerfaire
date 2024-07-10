/**
 * @namespace WPGMZA
 * @module ProCircle
 * @requires WPGMZA.Circle
 */
jQuery(function($) {
	
	var Parent;

	WPGMZA.ProCircle = function(options, engineCircle)
	{
		var self = this;
		
		Parent.call(this, options, engineCircle);

		if(this.center){
			this.position = new WPGMZA.LatLng({
	        	lat : this.center.lat,
	        	lng : this.center.lng
	        });
		}
		
		this.initShapeLabels();
	}

	Parent = WPGMZA.Circle;
	
	WPGMZA.ProCircle.prototype = Object.create(Parent.prototype);
	WPGMZA.ProCircle.prototype.constructor = WPGMZA.ProCircle;
});
/**
 * @namespace WPGMZA
 * @module ProRectangle
 * @requires WPGMZA.Rectangle
 */
jQuery(function($) {
	
	var Parent;

	WPGMZA.ProRectangle = function(options, engineCircle)
	{
		var self = this;
		
		Parent.call(this, options, engineCircle);

		if(this.cornerA && this.cornerB){
			this.position = new WPGMZA.LatLng({
	        	lat : ((parseFloat(this.cornerA.lat) + parseFloat(this.cornerB.lat)) / 2),
	        	lng : ((parseFloat(this.cornerA.lng) + parseFloat(this.cornerB.lng)) / 2),
	        });
		}

		this.initShapeLabels();
	}

	Parent = WPGMZA.Rectangle;
	
	WPGMZA.ProRectangle.prototype = Object.create(Parent.prototype);
	WPGMZA.ProRectangle.prototype.constructor = WPGMZA.ProRectangle;
});
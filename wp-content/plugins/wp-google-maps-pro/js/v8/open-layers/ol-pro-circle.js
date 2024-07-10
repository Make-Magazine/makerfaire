/**
 * @namespace WPGMZA
 * @module OLProCircle
 * @requires WPGMZA.OLCircle
 */
jQuery(function($) {
	
	WPGMZA.OLProCircle = function(options, olFeature)
	{
		var self = this;
		
		WPGMZA.OLCircle.call(this, options, olFeature);
	}
	
	WPGMZA.OLProCircle.prototype = Object.create(WPGMZA.OLCircle.prototype);
	WPGMZA.OLProCircle.prototype.constructor = WPGMZA.OLProCircle;
	

    WPGMZA.OLProCircle.prototype.setLayergroup = function(layergroup){
    	WPGMZA.OLCircle.prototype.setLayergroup.call(this, layergroup);

    	if(this.layergroup && this.layer){
    		this.layer.setZIndex(this.layergroup);
    	}

	}
});
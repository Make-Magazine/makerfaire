/**
 * @namespace WPGMZA
 * @module OLProRectangle
 * @requires WPGMZA.OLRectangle
 */
jQuery(function($) {
	
	WPGMZA.OLProRectangle = function(options, olFeature)
	{
		var self = this;
		
		WPGMZA.OLRectangle.call(this, options, olFeature);
	}
	
	WPGMZA.OLProRectangle.prototype = Object.create(WPGMZA.OLRectangle.prototype);
	WPGMZA.OLProRectangle.prototype.constructor = WPGMZA.OLProRectangle;
	
	WPGMZA.OLProRectangle.prototype.setLayergroup = function(layergroup){
    	WPGMZA.OLRectangle.prototype.setLayergroup.call(this, layergroup);

    	if(this.layergroup && this.layer){
    		this.layer.setZIndex(this.layergroup);
    	}

	}
});
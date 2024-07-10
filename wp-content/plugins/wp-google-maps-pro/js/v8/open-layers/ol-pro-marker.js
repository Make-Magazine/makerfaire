/**
 * @namespace WPGMZA
 * @module OLProMarker
 * @requires WPGMZA.OLMarker
 */
jQuery(function($) {
	
	WPGMZA.OLProMarker = function(row)
	{
		WPGMZA.OLMarker.call(this, row);
	}
	
	WPGMZA.OLProMarker.prototype = Object.create(WPGMZA.OLMarker.prototype);
	WPGMZA.OLProMarker.prototype.constructor = WPGMZA.OLProMarker;
	
	WPGMZA.OLProMarker.prototype.updateIcon = function()
	{
		var self = this;
		var icon = this._icon;
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT)
		{
			icon.applyToElement(
				$(this.element).find("img")
			);
			
			WPGMZA.getImageDimensions(icon.url, function(dimensions) {
				self.updateElementHeight(dimensions.height);
			});
		}
		else
		{
			this.vectorLayerStyle = new ol.style.Style({
				image: new ol.style.Icon({
					anchor: [0.5, 1],
					src: icon.isDefault ? WPGMZA.defaultMarkerIcon : icon.url
				})
			});
			this.feature.setStyle(this.vectorLayerStyle);
		}
	}

	WPGMZA.OLProMarker.prototype.setLayergroup = function(layergroup){
		WPGMZA.OLMarker.prototype.setLayergroup.call(this, arguments);

		if(this.layergroup){
			if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT){
				if(this.overlay && this.overlay.element && this.overlay.element.style){
					this.overlay.element.style.zIndex = layergroup;
				}
			}
		}
	} 
	
});
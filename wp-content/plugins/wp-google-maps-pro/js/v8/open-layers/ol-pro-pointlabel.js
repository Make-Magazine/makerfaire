/**
 * @namespace WPGMZA
 * @module OLProPointlabel
 * @requires WPGMZA.OLPointlabel
 */
jQuery(function($) {
	var Parent = WPGMZA.OLPointlabel;

	WPGMZA.OLProPointlabel = function(options, pointFeature){
		Parent.call(this, options, pointFeature);
	}

	WPGMZA.extend(WPGMZA.OLProPointlabel, Parent);

	WPGMZA.OLProPointlabel.prototype.updateNativeFeature = function(){
		Parent.prototype.updateNativeFeature.apply(this, arguments);

		var options = this.getScalarProperties();

		if(options.fontSize){
			this.textFeature.setFontSize(options.fontSize);
		}

		if(options.fillColor){
			this.textFeature.setFillColor(options.fillColor);
		}

		if(options.lineColor){
			this.textFeature.setLineColor(options.lineColor);
		}

		if(options.opacity){
			this.textFeature.setOpacity(options.opacity);
		}

		this.textFeature.refresh();

	}

});
		
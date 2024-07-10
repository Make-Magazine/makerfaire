/**
 * @namespace WPGMZA
 * @module GoogleProPointlabel
 * @requires WPGMZA.GooglePointlabel
 */
jQuery(function($) {
	var Parent = WPGMZA.GooglePointlabel;

	WPGMZA.GoogleProPointlabel = function(options, pointFeature){
		Parent.call(this, options, pointFeature);
	}

	WPGMZA.extend(WPGMZA.GoogleProPointlabel, Parent);

	WPGMZA.GoogleProPointlabel.prototype.setOptions = function(options){
		Parent.prototype.setOptions.apply(this, arguments);

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

	}

});
		
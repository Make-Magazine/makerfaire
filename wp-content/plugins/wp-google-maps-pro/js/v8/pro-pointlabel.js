/**
 * @namespace WPGMZA
 * @module ProPointlabel
 * @requires WPGMZA.Pointlabel
 */
jQuery(function($) {
	
	var Parent;

	WPGMZA.ProPointlabel = function(options, pointLabel){
		var self = this;
		
		Parent.call(this, options, pointLabel);
	}

	Parent = WPGMZA.Pointlabel;
	
	WPGMZA.ProPointlabel.prototype = Object.create(Parent.prototype);
	WPGMZA.ProPointlabel.prototype.constructor = WPGMZA.ProPointlabel;
});
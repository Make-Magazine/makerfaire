/**
 * @namespace WPGMZA
 * @module Imageoverlay
 * @requires WPGMZA.Feature
 */
jQuery(function($) {
	
	WPGMZA.Imageoverlay = function(options, imageoverlay){
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "Imageoverlay");
		
		if(!options)
			options = {};
		
		if(options.map){
			this.map = options.map;
		} else if(!options.map && options.map_id){
			let map = WPGMZA.getMapByID(options.map_id);
			if(map){
				this.map = map;
			}
		} 

		WPGMZA.Feature.apply(this, arguments);

		this.on("change", function(){
			self.redraw();
		});
	}
	
	WPGMZA.Imageoverlay.prototype = Object.create(WPGMZA.Feature.prototype);
	WPGMZA.Imageoverlay.prototype.constructor = WPGMZA.Imageoverlay;

	Object.defineProperty(WPGMZA.Imageoverlay.prototype, "map", {
		enumerable: true,
		"get": function(){
			if(this._map){
				return this._map;
			}
			
			return null;
		},
		"set" : function(a){
			if(!a){
				/* Necessary to deal with the sub layers */
				this.destroy();
			}
			this._map = a;
		}
		
	});

	Object.defineProperty(WPGMZA.Imageoverlay.prototype, "image", {
		enumerable: true,
		"get": function(){
			if(this._image){
				return this._image;
			}
			return null;
		},
		"set" : function(a){
			this._image = a;
			this.trigger("change");
		}
	});

	Object.defineProperty(WPGMZA.Imageoverlay.prototype, "opacity", {
		enumerable: true,
		"get": function(){
			if(this._opacity){
				return this._opacity;
			}
			return null;
		},
		"set" : function(a){
			this._opacity = parseFloat(a);
			this.trigger("change");
		}
	});

	WPGMZA.Imageoverlay.getConstructor = function(){
		switch(WPGMZA.settings.engine){
			case "open-layers":
				return WPGMZA.OLImageoverlay;
				break;			
			default:
				return WPGMZA.GoogleImageoverlay;
				break;
		}
	}

	WPGMZA.Imageoverlay.createInstance = function(options, imageoverlay){
		var constructor = WPGMZA.Imageoverlay.getConstructor();
		return new constructor(options, imageoverlay);
	}

	WPGMZA.Imageoverlay.prototype.getMap = function(){
		return this.map;
	}

	WPGMZA.Imageoverlay.prototype.setMap = function(map){
		if(this.map){
			this.map.removeImageoverlay(this);
		}
		
		if(map){
			map.addImageoverlay(this);
		}
			
	}

	WPGMZA.Imageoverlay.prototype.redraw = function(){
		/* Needed for nested engine calls during edits - For this feature only */
	}

	WPGMZA.Imageoverlay.prototype.destroy = function(){
		/* Needed for nested engine calls during edits - For this feature only */
	}
});
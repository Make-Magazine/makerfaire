/**
 * @namespace WPGMZA
 * @module GoogleImageoverlay
 * @requires WPGMZA.Imageoverlay
 */
jQuery(function($) {
	
	WPGMZA.GoogleImageoverlay = function(options, googleImageoverlay){
		if(!options)
			options = {};

		WPGMZA.Imageoverlay.call(this, options, googleImageoverlay);
				
		if(googleImageoverlay && googleImageoverlay.googleRectangle){
			this.googleRectangle = googleImageoverlay.googleRectangle;

			if(this.googleRectangle instanceof WPGMZA.Rectangle){
				this.googleRectangle = this.googleRectangle.googleRectangle;
			}
			
			this.cornerA = options.cornerA = new WPGMZA.LatLng({
				lat:	this.googleRectangle.getBounds().getNorthEast().lat(),
				lng:	this.googleRectangle.getBounds().getSouthWest().lng(),
			});
			
			this.cornerB = options.cornerB = new WPGMZA.LatLng({
				lat:	this.googleRectangle.getBounds().getSouthWest().lat(),
				lng:	this.googleRectangle.getBounds().getNorthEast().lng()
			});			
		} else {
			this.googleRectangle = new google.maps.Rectangle();

			if(!(this.cornerA instanceof WPGMZA.LatLng)){
				this.cornerA = new WPGMZA.LatLng(this.cornerA);
			}

			if(!(this.cornerB instanceof WPGMZA.LatLng)){
				this.cornerB = new WPGMZA.LatLng(this.cornerB);
			}

			this.setBounds(this.cornerA, this.cornerB);
		}
		
		this.googleImageoverlay = new google.maps.GroundOverlay();
		this.googleImageoverlay.wpgmzaImageoverlay = this;

		this.googleFeature = this.googleImageoverlay;
		
		if(options){
			this.setOptions(options);
		}
	}
	
	WPGMZA.GoogleImageoverlay.prototype = Object.create(WPGMZA.Imageoverlay.prototype);
	WPGMZA.GoogleImageoverlay.prototype.constructor = WPGMZA.GoogleImageoverlay;
	
	WPGMZA.GoogleImageoverlay.prototype.getBounds = function(){
		return WPGMZA.LatLngBounds.fromGoogleLatLngBounds(this.googleRectangle.getBounds());
	}

	WPGMZA.GoogleImageoverlay.prototype.setBounds = function(cornerA, cornerB){
		if(this.googleRectangle){
			this.cornerA = cornerA;
			this.cornerB = cornerB;
			
			this.googleRectangle.setOptions({
					bounds :  {
					north: parseFloat(this.cornerA.lat),
					west: parseFloat(this.cornerA.lng),
					south: parseFloat(this.cornerB.lat),
					east: parseFloat(this.cornerB.lng)
				}
			});
		}
	}

	WPGMZA.GoogleImageoverlay.prototype.setOptions = function(options){
		WPGMZA.Imageoverlay.prototype.setOptions.apply(this, arguments);
		
		if(options.cornerA && options.cornerB){
			this.cornerA = new WPGMZA.LatLng(options.cornerA);
			this.cornerB = new WPGMZA.LatLng(options.cornerB);
		}
	}

	WPGMZA.GoogleImageoverlay.prototype.setEditable = function(value){
		var self = this;
		
		this.googleRectangle.setEditable(value ? true : false);
		this.googleRectangle.setDraggable(value ? true : false);
		this.googleRectangle.setMap(this.map ? this.map.googleMap : null);

		if(value){
			google.maps.event.addListener(this.googleRectangle, "bounds_changed", function(event) {
				self.cornerA = new WPGMZA.LatLng({
					lat:	self.googleRectangle.getBounds().getNorthEast().lat(),
					lng:	self.googleRectangle.getBounds().getSouthWest().lng(),
				});
				
				self.cornerB = new WPGMZA.LatLng({
					lat:	self.googleRectangle.getBounds().getSouthWest().lat(),
					lng:	self.googleRectangle.getBounds().getNorthEast().lng()
				});

				self.trigger("change");
			});
		}
	}

	WPGMZA.GoogleImageoverlay.prototype.updateNativeFeature = function(){
		var googleOptions = this.getScalarProperties();
		
		let bounds = this.getBounds();
		if(bounds.north && bounds.west && bounds.south && bounds.east){
			const regionBounds = {
				north : bounds.north,
				west  : bounds.west,
				south : bounds.south,
				east  : bounds.east
			}

			let rectOptions = {
				bounds : regionBounds,
				visible : this.googleRectangle.getEditable(),
				strokeColor: "#000000",
				strokeOpacity: 0.8,
				fillOpacity: 0,
				strokeWeight: 1
			};

			this.googleRectangle.setOptions(rectOptions);
		}

		this.redraw();
	}

	WPGMZA.GoogleImageoverlay.prototype.redraw = function(){
		WPGMZA.Imageoverlay.prototype.redraw.apply(this, arguments);
		var googleOptions = this.getScalarProperties();
	
		const bounds = this.getBounds();
			
		if(bounds.north && bounds.west && bounds.south && bounds.east){
			const overlayBounds = {
				north : bounds.north,
				west  : bounds.west,
				south : bounds.south,
				east  : bounds.east
			}

			let url = this.image;
			if(url){
				if(this.googleImageoverlay){
					this.googleImageoverlay.setMap(null);
				}

				this.googleImageoverlay = new google.maps.GroundOverlay(url, overlayBounds);
				this.googleImageoverlay.setOpacity(this.opacity);
				this.googleImageoverlay.setMap(this.map ? this.map.googleMap : null);
				this.googleImageoverlay.setOptions({clickable: false});


				this.googleImageoverlay.wpgmzaImageoverlay = this;
				this.googleFeature = this.googleImageoverlay;
			}
		}
	}

	WPGMZA.GoogleImageoverlay.prototype.destroy = function(){
		WPGMZA.Imageoverlay.prototype.destroy.apply(this, arguments);

		if(this.googleImageoverlay){
			this.googleImageoverlay.setMap(null);
		}
	}
});
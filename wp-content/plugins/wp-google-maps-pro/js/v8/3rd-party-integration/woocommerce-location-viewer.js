/**
 * @namespace WPGMZA
 * @module WooCommerceLocationViewer
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.WooCommerceLocationViewer = function(element){
		const self = this;

		this.element = $(element);
		this.map = WPGMZA.maps[0];

		this.marker = false;
		this.coords = false;

		this.mapWrapper = this.element.find("#wpgmza-wco-map-container");

		if(this.mapWrapper.data('coords')){
			self.updateCoordinates(this.mapWrapper.data('coords'));
		}
	}

	WPGMZA.WooCommerceLocationViewer.prototype.updateCoordinates = function(value){
		const coords = {
			lat : false,
			lng : false
		};

		if(value){
			value = value.split(",");

			if(value.length >= 2){
				coords.lat = value.shift();
				coords.lng = value.shift();
			}
		}

		if(coords.lat && coords.lng){
			this.coords = coords;
			this.updateMarker();
		}
	}

	WPGMZA.WooCommerceLocationViewer.prototype.updateMarker = function(){
		const self = this;
		if(this.coords && this.coords.lat && this.coords.lng){
			if(!this.marker){
				let options = {
					lat: this.coords.lat,
					lng: this.coords.lng,
                    disableInfoWindow: true
				};

				this.marker = WPGMZA.Marker.createInstance(options);
				this.map.addMarker(this.marker);
			} else {
				this.marker.setPosition(new WPGMZA.LatLng(this.coords));
			}

			setTimeout(function(){
				self.map.panTo(new WPGMZA.LatLng(self.coords));
			}, 800);
		}
	}
	
	$(document).ready(function(event) {
		if(parseInt(WPGMZA.is_admin) === 1){
			if($('#wpgmza_wco_location_viewer').length){
				WPGMZA.wooCommerceLocationViewer = new WPGMZA.WooCommerceLocationViewer($('#wpgmza_wco_location_viewer'));
			}
		}
		
	});
	
});
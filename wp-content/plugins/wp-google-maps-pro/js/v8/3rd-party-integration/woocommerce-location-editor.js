/**
 * @namespace WPGMZA
 * @module WooCommerceLocationEditor
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.WooCommerceLocationEditor = function(element){
		const self = this;

		this.element = $(element);
		this.map = WPGMZA.maps[0];

		this.marker = false;

		this.lastAddress = element.find("input.wpgmza-address").val();
		this.lastCoords = false;

		this.input = this.element.find("input.wpgmza-address");

		this.input.addressInput = WPGMZA.AddressInput.createInstance(this.input.get(0));

		if(this.input.addressInput.useMyLocationButton && this.input.addressInput.useMyLocationButton.element){
			this.input.addressInput.useMyLocationButton.element.text(this.input.addressInput.useMyLocationButton.element.attr('title'));
			this.input.addressInput.useMyLocationButton.element.addClass('wpgmza-margin-l-10');
		}

		this.input.on('change focusout', function(){
			setTimeout(() => {
				const address = $(this).val();
				self.updateAddress(address);
			}, 500);
		});

		this.element.find('input[name="_wpgmza_wcp_loc_lat"],input[name="_wpgmza_wcp_loc_lng"]').on('change', function(){
			self.updateCoordinates({
				lat : parseFloat(self.element.find('input[name="_wpgmza_wcp_loc_lat"]').val()),
				lng : parseFloat(self.element.find('input[name="_wpgmza_wcp_loc_lng"]').val())
			});
		});

		this.element.find('.wpgmza-wcp-clear-coords-button').on('click', function(event){
			event.preventDefault();

			self.input.val('');
			self.element.find('input[name="_wpgmza_wcp_loc_lng"]').val('');
			self.element.find('input[name="_wpgmza_wcp_loc_lng"]').val('');

			if(self.marker){
				self.map.removeMarker(self.marker);
				self.marker = false;
			}

		});

		this.element.find('.wpgmza-wcp-toggle-coords-button').on('click', function(event){
			event.preventDefault();
			$('.wpgmza-wcp-coords-block').toggleClass('wpgmza-hidden');
		});

		this.map.on('rightclick', function(event){
			if(event && event.latLng){
				self.updateCoordinates(event.latLng, true);
			}
		});


		if(this.element.find('input[name="_wpgmza_wcp_loc_lat"]').val().trim().length && this.element.find('input[name="_wpgmza_wcp_loc_lng"]').val().trim().length){
			self.updateCoordinates({
				lat: this.element.find('input[name="_wpgmza_wcp_loc_lat"]').val(),
				lng: this.element.find('input[name="_wpgmza_wcp_loc_lng"]').val()
			});
		}
	}

	WPGMZA.WooCommerceLocationEditor.prototype.updateAddress = function(address){
		const self = this;
		if(this.lastAddress !== address.trim()){
			this.lastAddress = address.trim();

			const geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.geocode({address : address.trim()}, function(data){
				if(data && data instanceof Array){
					const coords = data.shift();
					if(coords.latLng){
						self.updateCoordinates(coords.latLng);
					}
				}
			});
		}
	}

	WPGMZA.WooCommerceLocationEditor.prototype.updateCoordinates = function(coords, forceAddress){
		if(coords.lat && coords.lng){
			this.lastCoords = coords;

			this.element.find('input[name="_wpgmza_wcp_loc_lat"]').val(coords.lat);
			this.element.find('input[name="_wpgmza_wcp_loc_lng"]').val(coords.lng);

			if(!this.lastAddress || forceAddress){
				this.lastAddress = coords.lat + "," + coords.lng;
				this.input.val(this.lastAddress);
			}

			this.updateMarker();
		} 
	}

	WPGMZA.WooCommerceLocationEditor.prototype.updateMarker = function(){
		const self = this;
		if(this.lastCoords && this.lastCoords.lat && this.lastCoords.lng){
			if(!this.marker){
				let options = {
					draggable: true,
					lat: this.lastCoords.lat,
					lng: this.lastCoords.lng,
                    disableInfoWindow: true
				};

				this.marker = WPGMZA.Marker.createInstance(options);
				this.map.addMarker(this.marker);

				this.marker.on("dragend", function(event){
					if(!(event.target instanceof WPGMZA.Marker))
						return;
					
					if(event.latLng){
						self.updateCoordinates(event.latLng);
					}
				});
			} else {
				this.marker.setPosition(new WPGMZA.LatLng(this.lastCoords));
			}

			setTimeout(function(){
				self.map.panTo(new WPGMZA.LatLng(self.lastCoords));
			}, 800);
		}
	}
	
	$(document).ready(function(event) {
		if(parseInt(WPGMZA.is_admin) === 1){
			if($('#wpgmza_wcp_location_editor').length){
				WPGMZA.wooCommerceLocationEditor = new WPGMZA.WooCommerceLocationEditor($('#wpgmza_wcp_location_editor'));
			}
		}
		
	});
	
});
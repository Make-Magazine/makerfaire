/**
 * @namespace WPGMZA
 * @module MapWizard
 * @requires WPGMZA.EventDispatcher
 */

jQuery(function($) {
	if(WPGMZA.currentPage != "wizard")
		return;

	WPGMZA.MapWizard = function(){
		var self = this;


		this.element = $(document.body).find('.wpgmza-wizard-steps');

		if(this.element.length <= 0){
			return;
		}

		this.step = 0;
		this.max = 0;
		this.findMax();

		this.geocoder = WPGMZA.Geocoder.createInstance();

		$(this.element).on('click', '.next-step-button', function(event){
			self.next();
		});

		$(this.element).on('keypress', function(event) {
		    if(event.which == 13) {
		        self.next();
		    }
		});

		$(this.element).on('click', '.prev-step-button', function(event){
			self.prev();
		});

		this.prepareAddressFields();

		this.loadStep(this.step);

	}

	WPGMZA.extend(WPGMZA.MapWizard, WPGMZA.EventDispatcher);

	WPGMZA.MapWizard.createInstance = function(){
		return new WPGMZA.MapWizard();
	}

	WPGMZA.MapWizard.prototype.findMax = function(){
		var self = this;
		$(this.element).find('.step').each(function(){
			if(parseInt($(this).data('step')) > self.max){
				self.max = parseInt($(this).data('step'));
			}
		});
	}

	WPGMZA.MapWizard.prototype.prepareAddressFields = function(){
		$(this.element).find("input.wpgmza-address").each(function(index, el) {
			el.addressInput = WPGMZA.AddressInput.createInstance(el, null);
		});
	}

	WPGMZA.MapWizard.prototype.next = function(){
		if(this.step < this.max){
			this.loadStep(this.step + 1);
		} else {
			this.complete();
		}
	}

	WPGMZA.MapWizard.prototype.prev = function(){
		if(this.step > 0){
			this.loadStep(this.step - 1);
		}
	}

	WPGMZA.MapWizard.prototype.loadStep = function(index){
		$(this.element).find('.step').removeClass('active');
		$(this.element).find('.step[data-step="' + index + '"]').addClass('active');

		this.step = index;

		if(this.step === 0){
			$(this.element).find('.prev-step-button').addClass('wpgmza-hidden');
		} else {
			$(this.element).find('.prev-step-button').removeClass('wpgmza-hidden');
		}

		if(this.step === this.max){
			$(this.element).find('.next-step-button span').text($(this.element).find('.next-step-button').data('final'));
		} else {
			$(this.element).find('.next-step-button span').text($(this.element).find('.next-step-button').data('next'));
		}

		this.autoFocus();

	}

	WPGMZA.MapWizard.prototype.getActiveBlock = function(){
		return $(this.element).find('.step[data-step="' + this.step + '"]');
	}

	WPGMZA.MapWizard.prototype.autoFocus = function(){
		var block = this.getActiveBlock();
		if(block){
			if(block.find('input').length > 0){
				block.find('input')[0].focus();
			} else if(block.find('select').length > 0){
				block.find('select')[0].focus();
			} 
		}
	}

	WPGMZA.MapWizard.prototype.complete = function(){
		$(this.element).find('.step').removeClass('active');
		$(this.element).find('.step-controller').addClass('wpgmza-hidden');
		$(this.element).find('.step-loader').removeClass('wpgmza-hidden');

		this.prepareMap();
	}

	WPGMZA.MapWizard.prototype.getData = function(){
		var data = {
            map_title:      WPGMZA.localized_strings.new_map,
            map_start_lat:  36.778261,
            map_start_lng:  -119.4179323999,
            map_start_zoom: 3
        };

        $(this.element).find('.step').each(function(){
        	$(this).find('input,select').each(function(){
        		var name = $(this).attr('name');
        		if(name && name.trim() !== ""){
        			var value = $(this).val();
        			if(value.trim() !== ""){
        				data[name.trim()] = value.trim();
        			}
        		}
        	});
        });

        return data;

	}

	WPGMZA.MapWizard.prototype.prepareMap = function(){
		var self = this;
		var filteredData = {};
		var parsedData = this.getData();

		var mapAddress = false;
		var firstMarker = false;
		for(var name in parsedData){
			switch(name){
				case 'map_start_address':
					mapAddress = parsedData[name];
					break;
				case 'first_marker':
					firstMarker = parsedData[name];
					break;
				default:
					filteredData[name] = parsedData[name];
					break;
			}
		}


		if(mapAddress !== false){
			//We need to geocode 
			this.geocoder.getLatLngFromAddress({address: mapAddress},  function(results, status) {
				if(status == WPGMZA.Geocoder.SUCCESS){
					var latLng = new WPGMZA.LatLng(results[0].latLng);
					filteredData['map_start_lat'] = latLng.lat;
					filteredData['map_start_lng'] = latLng.lng;
				}

				self.createMap(filteredData, firstMarker);
			});
		} else {
			self.createMap(filteredData, firstMarker);
		}
	}

	WPGMZA.MapWizard.prototype.createMap = function(data, firstMarker){
        var self = this;
        WPGMZA.restAPI.call("/maps/", {
        	method: "POST",
            data: data,
            success: function(response, status, xhr) {
            	var redirect = "admin.php?page=wp-google-maps-menu&action=edit&map_id=" + response.id;
            	if(firstMarker !== false){
            		//We need to geocode and create a marker

					self.geocoder.getLatLngFromAddress({address: firstMarker},  function(results, status) {
						if(status == WPGMZA.Geocoder.SUCCESS){
							var latLng = new WPGMZA.LatLng(results[0].latLng);

							var markerData = {
								map_id : response.id,
								lat : latLng.lat,
								lng : latLng.lng,
								address : firstMarker,
								approved : "1"
							};

							self.createMarker(markerData, function(){
								window.location.href = window.location.href = redirect;
							});
						} else {
							window.location.href = window.location.href = redirect;
						}
					});
            	} else {
					window.location.href = window.location.href = redirect;
            	}
			}
    	});
	}

	WPGMZA.MapWizard.prototype.createMarker = function(data, callback){
		WPGMZA.restAPI.call("/markers/", {
        	method: "POST",
            data: data,
            success: function(response, status, xhr) {
            	if(typeof callback === 'function'){
            		callback();
            	}
			}
    	});
	}

	$(document).ready(function(event) {
		WPGMZA.mapWizard = WPGMZA.MapWizard.createInstance();
	});
});


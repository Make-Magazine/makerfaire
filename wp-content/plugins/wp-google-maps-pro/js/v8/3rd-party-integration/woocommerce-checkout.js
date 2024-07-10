/**
 * @namespace WPGMZA
 * @module WooCommerceCheckout
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.WooCommerceCheckout = function(element){
        this.element = element;

        this.map = false;
        this.checkout = false;

        this.marker = false;
        this.coords = false;

        this.findMap();
        this.findCheckout();

        if(this.map && this.checkout){
            this.bindEvents();
            this.updateCoordinatesFromAddress();
        }
    }

    WPGMZA.WooCommerceCheckout.prototype.findMap = function(){
        if(this.element.find('.wpgmza_map').length){
            this.map = WPGMZA.getMapByID(this.element.find('.wpgmza_map').data('map-id'));
        }
    }

    WPGMZA.WooCommerceCheckout.prototype.findCheckout = function(){
        if($('form.checkout.woocommerce-checkout').length){
            this.checkout = {};
            this.checkout.element = $('form.checkout.woocommerce-checkout');

            this.checkout.fields = {
                billing : {},
                shipping : {}
            };

            for(let type in this.checkout.fields){
                this.checkout.fields[type].address = this.checkout.element.find('*[name="' + type + '_address_1"]');
                this.checkout.fields[type].city = this.checkout.element.find('*[name="' + type + '_city"]');
                this.checkout.fields[type].postcode = this.checkout.element.find('*[name="' + type + '_postcode"]');
                this.checkout.fields[type].country = this.checkout.element.find('*[name="' + type + '_country"]');
                this.checkout.fields[type].state = this.checkout.element.find('*[name="' + type + '_state"]');
            }

            this.checkout.coords = this.checkout.element.find('input[name="_wpgmza_wcc_coords"]');
        }
    }

    WPGMZA.WooCommerceCheckout.prototype.bindEvents = function(){
        const self = this;
        this.map.on('rightclick', function(event){
            if(event.latLng){
                self.updateCoordinates(event.latLng, true);
            }
        });

        for(let type in this.checkout.fields){
            for(let slug in this.checkout.fields[type]){
                this.checkout.fields[type][slug].on('change', function(){
                    self.updateCoordinatesFromAddress();
                });
            }
        }
    }

    WPGMZA.WooCommerceCheckout.prototype.updateCoordinates = function(coords, updateAddress){
        if(coords.lat && coords.lng){
            this.coords = coords;
            this.updateMarker();

            if(updateAddress){
                this.updateAddressFromCoordinates();
            }

            this.checkout.coords.val(this.coords.lat + ", " + this.coords.lng);
        } 
    }

    WPGMZA.WooCommerceCheckout.prototype.updateMarker = function(coords){
        const self = this;
        if(this.coords && this.coords.lat && this.coords.lng){
            if(!this.marker){
                let options = {
                    draggable: true,
                    lat: this.coords.lat,
                    lng: this.coords.lng,
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
                this.marker.setPosition(new WPGMZA.LatLng(this.coords));
            }

            setTimeout(function(){
                self.map.panTo(new WPGMZA.LatLng(self.coords));
            }, 800);
        }
    }

    WPGMZA.WooCommerceCheckout.prototype.updateAddressFromCoordinates = function(){
        const self = this;
        if(this.coords && this.coords.lat && this.coords.lng){
            const geocoder = WPGMZA.Geocoder.createInstance();
            geocoder.geocode({latLng : new WPGMZA.LatLng(this.coords), fullResult : true}, function(data){
                if(data && data instanceof Array){
                    data = data.shift();
                    if(data){
                        let address = false;
                        if(data.address_components){
                            /* Google Address Components */
                            address = self.parseAdddressFromComponents(data.address_components); 
                        } else {
                            /* OpenLayers address, or it may be a broken Google Address */
                            address = self.parseAddressFromText(data.display_name);
                        }

                        self.updateCheckoutField('address', [address.number, address.street, address.suburb]);
                        self.updateCheckoutField('city', address.city);
                        self.updateCheckoutField('postcode', address.zip);
                        self.updateCheckoutField('country', address.country);

                        setTimeout(function(){
                            /* Delay the state update, to allow things to work in order */
                            self.updateCheckoutField('state', address.state);
                        }, 1500);
                    }
                }
            });
        }
    }

    WPGMZA.WooCommerceCheckout.prototype.updateCoordinatesFromAddress = function(){
        const self = this;
        const addresses = {};

        const onlyShipping = this.checkout.element.find('input[name="ship_to_different_address"]').prop('checked');
        const sections = ['shipping'];
        if(!onlyShipping){
            sections.push('billing');
        }

        for(let type of sections){
            if(this.checkout.fields[type]){
                const fields = this.checkout.fields[type];
                const compiled = [];

                try{
                    if(fields.address.length && fields.address.val().trim().length){
                        compiled.push(fields.address.val().trim());
                    }

                    if(fields.city.length && fields.city.val().trim().length){
                        compiled.push(fields.city.val().trim());
                    }

                    if(fields.state.length && fields.state.val().trim().length){
                        compiled.push(fields.state.find('option[value="' + fields.state.val().trim() + '"]').text());
                    }

                    if(fields.country.length && fields.country.val().trim().length){
                        compiled.push(fields.country.find('option[value="' + fields.country.val().trim() + '"]').text());
                    }

                    if(fields.postcode.length && fields.postcode.val().trim().length){
                        compiled.push(fields.postcode.val().trim());
                    }
                } catch (ex){
                    // Could be a bad trim call, on a select that is mid-pop, we can ignore it, probably 
                }

                if(compiled.length){
                    addresses[type] = compiled.join(", ");
                }
            }
        }

        let address = false;
        if(onlyShipping && addresses.shipping){
            address = addresses.shipping;
        } else if(addresses.billing){
            address = addresses.billing;
        }

        if(address){
            const geocoder = WPGMZA.Geocoder.createInstance();
            geocoder.geocode({address : address}, function(data){
                if(data && data instanceof Array){
                    data = data.shift();
                    if(data && data.latLng){
                        self.updateCoordinates(data.latLng);
                    }
                }
            });
        }

    }

    WPGMZA.WooCommerceCheckout.prototype.parseAdddressFromComponents = function(components){
        if(components instanceof Array){
            const compiled = {};

            const remap = {
                'street_number' : 'number',
                'route' : 'street',
                'sublocality' : 'suburb',
                'locality' : 'city',
                'administrative_area_level_1' : 'state',
                'country' : 'country',
                'postal_code' : 'zip'
            };

            for(let component of components){
                for(let key in remap){
                    const compiledKey = remap[key];

                    if(component.types && component.types instanceof Array){
                        if(component.types.indexOf(key) !== -1){
                            if(compiledKey === 'country' || compiledKey === 'state'){
                                compiled[compiledKey] = component.short_name;
                            } else {
                                compiled[compiledKey] = component.long_name;
                            }
                        }
                    }
                } 
            }

            return compiled;
        }
        return false;
    }

    WPGMZA.WooCommerceCheckout.prototype.parseAddressFromText = function(text){
        /* More basic geocoder, uses a plain text value, so we will parse it as best we can */
        return {
            street : text
        };
    }

    WPGMZA.WooCommerceCheckout.prototype.updateCheckoutField = function(slug, value){
        if(!value){
            return;
        }

        if(value instanceof Array){
            value = value.join(" ");
            value = value.trim();
        }

        if(value.trim().length){
            const onlyShipping = this.checkout.element.find('input[name="ship_to_different_address"]').prop('checked');
            const sections = ['shipping'];
            if(!onlyShipping){
                sections.push('billing');
            }

            for(let type of sections){
                if(this.checkout.fields[type] && this.checkout.fields[type] instanceof Object){
                    if(this.checkout.fields[type][slug]){
                        const field = this.checkout.fields[type][slug];

                        field.val(value);
                        field.trigger('change');
                    }
                }
            }

        }
    }

    $(document).ready(function(event) {
        if($('.wpgmza-woo-checkout-map-wrapper').length){
            WPGMZA.wooCommerceCheckout = new WPGMZA.WooCommerceCheckout($('.wpgmza-woo-checkout-map-wrapper'));
        }
    });
});
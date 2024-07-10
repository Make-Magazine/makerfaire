/**
 * @namespace WPGMZA
 * @module ProMarker
 * @requires WPGMZA.Marker
 */
jQuery(function($) {
	
	/**
	 *  Pro marker class. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.ProMarker
	 * @constructor
	 * @memberof WPGMZA
	 * @param {object} row The data for the marker
	 * @augments WPGMZA.Marker
	 */
	WPGMZA.ProMarker = function(row) {
		var self = this;
		this._icon = WPGMZA.MarkerIcon.createInstance();

		if (row && row.map_id) 
			var currentMap = WPGMZA.getMapByID(row.map_id);

		this.title = "";
		this.description = "";
		this.categories = [];
		this.approved = 1;

		this.hoverIcon = false;
		
		if (row && row.retina){
			if(typeof row.icon === "object" && row.icon.retina){
				//Icon came in as an object, let the retina value here be accepted if it is true
				this.retina = row.icon.retina;
			} else {
				if(row.retina === true){
					this.retina = row.retina;
				} else {
					this.retina = row.retina && row.retina == '1' ? 1 : 0;
				}
			}
		} else {
			this.retina = 0;
		} 

		if(row && row.icon){
			if(row.icon instanceof Object && row.icon.hover_url){
				this.hoverIcon = {
					url : row.icon.hover_url 
				};

				if(row.icon.hover_retina){
					this.hoverIcon.retina = true;
				}
			}
		}

		if (currentMap && currentMap.settings && currentMap.settings.default_marker) {
			try {
				var objtmp = JSON.parse(currentMap.settings.default_marker)
				if (typeof objtmp == 'object') {
					if (objtmp.retina) {
						if (objtmp.retina == true) {
							this.retina = true;
						}
					}
				}
			} catch (e) {
				/* do nothing */
			}
		}

		if(row && row.category && row.category.length) {
			var m = row.category.match(/\d+/g);
			
			if(m)
				this.categories = m;
		}

		if(this.isIntegrated){
			/* Cleanup WCP Addresses - This should be moved, but for now its a small edge case */
			if(row && row.id && row.address && row.id.indexOf && row.id.indexOf('wcp_') !== -1){
				if(row.address.charAt(0) === '"' || row.address.charAt(row.address.length - 1) === '"'){
					row.address = row.address.replaceAll('"', "");
				}
			}

			if(typeof row !== 'undefined' && typeof row.integration_additional_fields !== 'undefined'){
				if(row.integration_additional_fields instanceof Array && row.integration_additional_fields.length){
					row.integration_additional_fields = {
						data :  row.integration_additional_fields,
						html : []
					};

					for(let i in row.integration_additional_fields.data){
						const field = row.integration_additional_fields.data[i];
						
						row.integration_additional_fields.html.push(`<div class='wpgmza-integration-field' data-source='${field.source || 'unknown'}' data-field-id='${field.id || 'unknown'}' data-field-name='${field.name || 'unknown'}'>`);
						if(field.label && field.label.length){
							row.integration_additional_fields.html.push(`<div class='wpgmza-integration-field-label'>${field.label}</div>`);
						}
						row.integration_additional_fields.html.push(`<div class='wpgmza-integration-field-value'>${field.value}</div>`);
						row.integration_additional_fields.html.push("</div>");
					}

					row.integration_additional_fields.html = row.integration_additional_fields.html.length ? `<div class='wpgmza-integration-fields-wrapper'>${row.integration_additional_fields.html.join('')}</div>` : false;

					if(row.integration_additional_fields.html && row.integration_additional_fields.html.length){
						row.description += row.integration_additional_fields.html;
					}
				}
			}
		}

		
		WPGMZA.Marker.call(this, row);
		
		this.on("mouseover", function(event) {
			self.onMouseOver(event);
		});

		this.on("mouseout", function(event) {
			self.onMouseOut(event);
		});
	}
	
	WPGMZA.ProMarker.prototype = Object.create(WPGMZA.Marker.prototype);
	WPGMZA.ProMarker.prototype.constructor = WPGMZA.ProMarker;
	
	WPGMZA.ProMarker.STICKY_ZINDEX			= 999999;
	WPGMZA.ProMarker.BASE_LAYER_INDEX		= 99999;
	
	// NB: I feel this should be passed from the server rather than being linked to the ID, however this should suffice for now as integrated markers should never have an integer ID (it would potentially collide with native markers)
	Object.defineProperty(WPGMZA.ProMarker.prototype, "isIntegrated", {
		
		get: function() {
			
			return /[^\d]/.test(this.id);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProMarker.prototype, "icon", {
		
		get: function() {
			if(this._icon.isDefault){
				return this.map.defaultMarkerIcon;
			}
			return this._icon;
		},
		
		set: function(value) {

			if(value instanceof WPGMZA.MarkerIcon) {
				this._icon = value;
				
				if(this.map)
					this.updateIcon();
			}
			else if(typeof value == "object" || typeof value == "string") {
				if (typeof value == "object") {
					value.retina = this.retina && this.retina === true ? true : (this.retina && this.retina == '1' ? 1 : 0);
				}
				this._icon = WPGMZA.MarkerIcon.createInstance(value);
				
				if(this.map)
					this.updateIcon();
			}
			else
				throw new Error("Value must be an instance of WPGMZA.MarkerIcon, an icon literal, or a string");
		}
		
	});

	Object.defineProperty(WPGMZA.ProMarker.prototype, "layergroup", {
		enumerable : true,
		get: function() {
			if(this._layergroup){
				return this._layergroup;
			}
			return 0;
		},
		set: function(value) {
			if(parseInt(value)){
				this._layergroup = parseInt(value) + WPGMZA.ProMarker.BASE_LAYER_INDEX;
			}
		}
	});
	
	/**
	 * Called when the marker has been added to a map
	 * @method
	 * @memberof WPGMZA.Marker
	 * @listens module:WPGMZA.ProMarker~added
	 * @fires module:WPGMZA.ProMarker~select When this marker is targeted by the marker shortcode attribute
	 */
	WPGMZA.ProMarker.prototype.onAdded = function(event)
	{
		var m;
		
		WPGMZA.Marker.prototype.onAdded.call(this, event);
		
		this.updateIcon();
		
		if(this.map.storeLocator && this == this.map.storeLocator.marker)
			return;
		
		if(this == this.map.userLocationMarker)
			return;
		
		if(this.map.settings.wpgmza_store_locator_hide_before_search == 1 && WPGMZA.is_admin != 1 && this.isFilterable)
		{
			if(this.userCreated){
				/* Generated by VGM */
				return;
			}

			this.isFiltered = true;
			this.setVisible(false);
			
			return;
		}
		
		if(WPGMZA.getQueryParamValue("markerid") == this.id || this.map.shortcodeAttributes.marker == this.id ) {
			this.openInfoWindow();
			this.map.setCenter(this.getPosition());
		}
		
		if("approved" in this && this.approved == 0)
			this.setOpacity(0.6);
		
		if(this.sticky == 1){
			this.setOptions({
				zIndex: WPGMZA.ProMarker.STICKY_ZINDEX
			});
		}

		if(this.layergroup){
			this.setLayergroup(this.layergroup);
		}

		if(this.map && this.map.settings && this.map.settings.enable_marker_labels){
			if(this.title){
				this.setLabel(this.title);
			}
		}
	}
	
	/**
	 * Called when the marker has been clicked
	 * @method
	 * @memberof WPGMZA.ProMarker
	 * @listens module:WPGMZA.ProMarker~click
	 */
	WPGMZA.ProMarker.prototype.onClick = function(event)
	{
		WPGMZA.Marker.prototype.onClick.apply(this, arguments);
		
		if(this.map.settings.wpgmza_zoom_on_marker_click && this.map.settings.wpgmza_zoom_on_marker_click_slider){
			this.map.setZoom(this.map.settings.wpgmza_zoom_on_marker_click_slider);
			this.map.setCenter(this.getPosition());
		}

		if(this.map.settings.click_open_link == 1 && this.link && this.link.length)
		{
			if(WPGMZA.settings.wpgmza_settings_infowindow_links == "yes")
				window.open(this.link);
			else
				window.open(this.link, '_self');
		}
	}
	
	/**
	 * Called when the user hovers the mouse over this marker
	 * @method
	 * @memberof WPGMZA.ProMarker
	 * @listens module:WPGMZA.ProMarker~mouseover
	 */
	WPGMZA.ProMarker.prototype.onMouseOver = function(event){
		if(WPGMZA.settings.wpgmza_settings_map_open_marker_by == WPGMZA.ProInfoWindow.OPEN_BY_HOVER){
			this.openInfoWindow();
		}

		if(this.hoverIcon && this.hoverIcon instanceof Object){
			if(this.hoverIcon.url){
				/* We have a hover icon */
				if(!this.restoreIcon){
					this.restoreIcon = Object.assign({}, this.icon);
				}

				this.icon = this.hoverIcon;
			}
		}
	}

	/**
	 * Called when the user moves the mouse out of this marker
	 * @method
	 * @memberof WPGMZA.ProMarker
	 * @listens module:WPGMZA.ProMarker~mouseout
	*/
	WPGMZA.ProMarker.prototype.onMouseOut = function(event){
		if(this.hoverIcon && this.restoreIcon){
			if(this.restoreIcon instanceof Object){
				this.icon = this.restoreIcon;
				this.restoreIcon = false;
			}
		}
	}
	
	/*WPGMZA.ProMarker.prototype.getIcon = function()
	{
		if(this.icon && this.icon.url.length)
			return this.icon;
		
		if(this.map.defaultMarkerIcon)
			return this.map.defaultMarkerIcon;
		
		return WPGMZA.MarkerIcon.createInstance({url: WPGMZA.defaultMarkerIcon});
	}*/
	
	WPGMZA.ProMarker.prototype.getIconFromCategory = function()
	{
		if(!this.categories.length)
			return;
		
		var self = this;
		var categoryIDs = this.categories.slice();
		
		// TODO: This could be taken from the category table now that it's cached. Would take some load off the client
		categoryIDs.sort(function(a, b) {
			var categoryA = self.map.getCategoryByID(a);
			var categoryB = self.map.getCategoryByID(b);
			
			if(!categoryA || !categoryB)
				return null;	// One of the category IDs is invalid
			
			return (categoryA.depth < categoryB.depth ? -1 : 1);
		});
		
		for(var i = 0; i < categoryIDs.length; i++)
		{
			var category = this.map.getCategoryByID(categoryIDs[i]);
			if(!category)
				continue;	// Invalid category ID
			
			var icon = category.icon;

			if(icon && icon.length)
				return icon;
		}
	}
	
	// NB: Deprecated, replaced with property. Provided for compatibility reasons
	WPGMZA.ProMarker.prototype.setIcon = function(icon) {
		this.icon = icon;
	}
	
	WPGMZA.ProMarker.prototype.openInfoWindow = function(autoOpen) {
		
		if (this.map.settings.wpgmza_listmarkers_by && parseInt(this.map.settings.wpgmza_listmarkers_by) == 6)
			return false;

		WPGMZA.Marker.prototype.openInfoWindow.apply(this, arguments);
		

		
		if(this.disableInfoWindow)
			return false;
		
		if((this.map && this.map.userLocationMarker == this) || (typeof this.user_location !== 'undefined' && this.user_location))
			this.infoWindow.setContent(WPGMZA.localized_strings.my_location);
	}
	
	WPGMZA.ProMarker.prototype.setLayergroup = function(layergroup){
		this.layergroup = layergroup;
		if(this.layergroup){
			this.setOptions({
				zIndex: this.layergroup
			});
		}
	} 
});

/**
 * @namespace WPGMZA
 * @module GoogleProMarker
 * @requires WPGMZA.GoogleMarker
 */
jQuery(function($) {
	
	WPGMZA.GoogleProMarker = function(row)
	{
		WPGMZA.GoogleMarker.call(this, row);
	}
	
	WPGMZA.GoogleProMarker.prototype = Object.create(WPGMZA.GoogleMarker.prototype);
	WPGMZA.GoogleProMarker.prototype.constructor = WPGMZA.GoogleProMarker;
	
	WPGMZA.GoogleProMarker.prototype.onAdded = function(event)
	{
		WPGMZA.GoogleMarker.prototype.onAdded.apply(this, arguments);
		
		if(this.map.settings.wpgmza_settings_disable_infowindows){
			if(this.googleMarker instanceof google.maps.marker.AdvancedMarkerElement){
				/* AdvancedMarkerElement module */
				if(this.googleMarker.element){
					this.googleMarker.element.classList.add('wpgmza-google-marker-non-interactive');
				}
			} else {
				/* Assume Marker module */
				this.googleMarker.setOptions({clickable: false});
			}
		}
	}
	
	WPGMZA.GoogleProMarker.prototype.updateIcon = function()
	{
		var self = this;
		var icon = this._icon;

		if(icon.retina) {
			var img = new Image();
			
			img.onload = function(event) {
				
				var autoDetect = false;
				
				//var isSVG = icon.match(/\.svg/i);
				
				var size;
				
				if(!autoDetect) {
					size = new google.maps.Size(
						WPGMZA.settings.retinaWidth ? parseInt(WPGMZA.settings.retinaWidth) : Math.round(img.width / 2),
						WPGMZA.settings.retinaHeight ? parseInt(WPGMZA.settings.retinaHeight) : Math.round(img.height / 2)
					);
				} else {
					size = new google.maps.Size(
						Math.round(img.width / 2),
						Math.round(img.height / 2)
					);
				}
				
				if(self.googleMarker instanceof google.maps.marker.AdvancedMarkerElement ){
					/* AdvancedMarkerElement module */
					if(self.googleMarker.content && icon.url){
						/*
						* Google applies a transform operation to these new icons, which can cause custom PNG's to become blurry
						* 
						* We get around this by applying a compat fix for the issue, applying absolute placement and using the image dimensions for the container
						*/
						if(!self.googleMarker.content.classList.contains('wpgmza-google-icon-transform-fix')){
							/* Setup */
							const markerIcon = document.createElement("img");
							const markerContainer = document.createElement('div');
							const markerInner = document.createElement('div');

							markerIcon.style.setProperty('width', size.width + 'px');
							markerIcon.style.setProperty('height', size.height + 'px');

							markerIcon.src = icon.url;
							markerIcon.onload = function(){
								markerInner.style.setProperty('--wpgmza-icon-offset', '-1px');
								markerInner.style.setProperty('width', (size.width - 1) + 'px');
								markerInner.style.setProperty('height', size.height + 'px');

								markerContainer.classList.add('wpgmza-google-icon-transform-fix');

								self.googleMarker.content = markerContainer;

								if(self.anim || self.animation){
									self.setAnimation(self.anim || self.animation);
								}

								if(self.map && self.map.settings && self.map.settings.enable_marker_labels){
									if(self.title){
										self.setLabel(null);
										self.setLabel(self.title);
									}
								}
							}

							markerContainer.appendChild(markerInner);
							markerInner.appendChild(markerIcon);
						} else {
							/* Update */
							const markerIcon = self.googleMarker.content.querySelector('img');
							if(markerIcon){
								markerIcon.src = icon.url;
							}
						}
					}
				} else {
					/* Assume Marker module */
					self.googleMarker.setIcon(
						new google.maps.MarkerImage(icon.url, null, null, null, size)
					);
				}
			};
			
			img.src = (icon.isDefault ? WPGMZA.defaultMarkerIcon : icon.url);
		} else {
			if(this.googleMarker instanceof google.maps.marker.AdvancedMarkerElement){
				/* AdvancedMarkerElement module */
				if(this.googleMarker.content && icon.url){
					/*
					 * Google applies a transform operation to these new icons, which can cause custom PNG's to become blurry
					 * 
					 * We get around this by applying a compat fix for the issue, applying absolute placement and using the image dimensions for the container
					 */

					if(!this.googleMarker.content.classList.contains('wpgmza-google-icon-transform-fix')){
						/* Setup */
						const markerIcon = document.createElement("img");
						const markerContainer = document.createElement('div');
						const markerInner = document.createElement('div');
						
						markerIcon.src = icon.url;
						markerIcon.onload = function(){
							markerInner.style.setProperty('--wpgmza-icon-offset', '-1px');
							markerInner.style.setProperty('width', (this.width - 1) + 'px');
							markerInner.style.setProperty('height', this.height + 'px');

							markerContainer.classList.add('wpgmza-google-icon-transform-fix');

							self.googleMarker.content = markerContainer;

							if(self.anim || self.animation){
								self.setAnimation(self.anim || self.animation);
							}

							if(self.map && self.map.settings && self.map.settings.enable_marker_labels){
								if(self.title){
									self.setLabel(null);
									self.setLabel(self.title);
								}
							}
						}

						markerContainer.appendChild(markerInner);
						markerInner.appendChild(markerIcon);
					} else {
						/* Update */
						const markerIcon = this.googleMarker.content.querySelector('img');
						if(markerIcon){
							markerIcon.src = icon.url;
						}
					}
					
				}
			} else {
				/* Assume Marker module */
				this.googleMarker.setIcon(icon.url);
			}
		}
	}
	
});
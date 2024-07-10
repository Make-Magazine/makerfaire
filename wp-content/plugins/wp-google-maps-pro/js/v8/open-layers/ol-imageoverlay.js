/**
 * @namespace WPGMZA
 * @module OLImageoverlay
 * @requires WPGMZA.Imageoverlay
 */
jQuery(function($) {
	
	WPGMZA.OLImageoverlay = function(options, olImageoverlay){
		if(!options)
			options = {};

		WPGMZA.Imageoverlay.call(this, options, olImageoverlay);
	
		if(olImageoverlay instanceof WPGMZA.Rectangle){
			/* The auto placement system sends only a rectangle */
			/* We should standardize the handling, but for now this will do */
			let rect = olImageoverlay;
			olImageoverlay = {
				engineRectangle : rect
			};
		}	

		if(olImageoverlay && olImageoverlay.engineRectangle){
			this.engineRectangle = olImageoverlay.engineRectangle;
			if(this.engineRectangle instanceof WPGMZA.Rectangle){
				this.engineRectangle = this.engineRectangle.olFeature;
			}

			let extent = this.engineRectangle.getGeometry().getExtent();

			let topLeft				= ol.extent.getTopLeft(extent);
			let bottomRight			= ol.extent.getBottomRight(extent);
			
			let topLeftLonLat		= ol.proj.toLonLat(topLeft);
			let bottomRightLonLat	= ol.proj.toLonLat(bottomRight);
			
			let topLeftLatLng		= new WPGMZA.LatLng(topLeftLonLat[1], topLeftLonLat[0]);
			let bottomRightLatLng	= new WPGMZA.LatLng(bottomRightLonLat[1], bottomRightLonLat[0]);
			
			this.cornerA = options.cornerA = topLeftLatLng;
			this.cornerB = options.cornerB = bottomRightLatLng;
		} else {
			this.engineRectangle = new ol.Feature();

			if(!(this.cornerA instanceof WPGMZA.LatLng)){
				this.cornerA = new WPGMZA.LatLng(this.cornerA);
			}

			if(!(this.cornerB instanceof WPGMZA.LatLng)){
				this.cornerB = new WPGMZA.LatLng(this.cornerB);
			}

			this.setBounds(this.cornerA, this.cornerB);
		}

		this.olImageoverlay = new ol.layer.Image({});
		this.olImageoverlay.wpgmzaImageoverlay = this;
		this.olFeature = this.olImageoverlay;

		this.engineRectangle.olFeature = this.engineRectangle;

		if(options){
			this.setOptions(options);
		}
	}
	
	WPGMZA.OLImageoverlay.prototype = Object.create(WPGMZA.Imageoverlay.prototype);
	WPGMZA.OLImageoverlay.prototype.constructor = WPGMZA.OLImageoverlay;
	
	WPGMZA.OLImageoverlay.prototype.getBounds = function(){
		var extent				= this.engineRectangle.getGeometry().getExtent();
		var topLeft				= ol.extent.getTopLeft(extent);
		var bottomRight			= ol.extent.getBottomRight(extent);
		
		var topLeftLonLat		= ol.proj.toLonLat(topLeft);
		var bottomRightLonLat	= ol.proj.toLonLat(bottomRight);
		
		var topLeftLatLng		= new WPGMZA.LatLng(topLeftLonLat[1], topLeftLonLat[0]);
		var bottomRightLatLng	= new WPGMZA.LatLng(bottomRightLonLat[1], bottomRightLonLat[0]);
		
		return new WPGMZA.LatLngBounds(
			topLeftLatLng,
			bottomRightLatLng
		);
	}

	WPGMZA.OLImageoverlay.prototype.setBounds = function(cornerA, cornerB){
		if(this.engineRectangle){
			this.cornerA = cornerA;
			this.cornerB = cornerB;

			let coordinates = [[]];
			coordinates[0].push(ol.proj.fromLonLat([
				parseFloat(this.cornerA.lng),
				parseFloat(this.cornerA.lat)
			]));
			
			coordinates[0].push(ol.proj.fromLonLat([
				parseFloat(this.cornerB.lng),
				parseFloat(this.cornerA.lat)
			]));
			
			coordinates[0].push(ol.proj.fromLonLat([
				parseFloat(this.cornerB.lng),
				parseFloat(this.cornerB.lat)
			]));
			
			coordinates[0].push(ol.proj.fromLonLat([
				parseFloat(this.cornerA.lng),
				parseFloat(this.cornerB.lat)
			]));
			
			coordinates[0].push(ol.proj.fromLonLat([
				parseFloat(this.cornerA.lng),
				parseFloat(this.cornerA.lat)
			]));
			
			this.engineRectangle.setGeometry(new ol.geom.Polygon(coordinates));
			this.redraw();
			this.trigger('change');
		}
	}

	WPGMZA.OLImageoverlay.prototype.setOptions = function(options){
		WPGMZA.Imageoverlay.prototype.setOptions.apply(this, arguments);
		
		if(options.cornerA && options.cornerB){
			this.cornerA = new WPGMZA.LatLng(options.cornerA);
			this.cornerB = new WPGMZA.LatLng(options.cornerB);

			this.setBounds(this.cornerA, this.cornerB);
		}
	}

	WPGMZA.OLImageoverlay.prototype.setEditable = function(enable){
		/** Because of nested feature usage, we have no choice but to bind this ourselves */
		let self = this;

		this.isModifying = enable;
		this.rectLayer.setVisible(enable);
		
		if(enable){

			if(this.engineRectangle.modifyInteraction)
				return;
			
			this.engineRectangle.snapInteraction = new ol.interaction.Snap({
				source : this.rectLayer.getSource(),
			});
			
			this.map.olMap.addInteraction(this.engineRectangle.snapInteraction);
			
			this.engineRectangle.modifyDefaultStyle = new ol.interaction.Modify({source: this.rectLayer.getSource()}).getOverlay().getStyleFunction();

			this.engineRectangle.modifyInteraction = new ol.interaction.Modify({
				source: this.rectLayer.getSource(),
				deleteCondition: ol.events.condition.never,
				insertVertexCondition: ol.events.condition.never,
				pixelTolerance: $(this.map.element).width(),
				style : function(feature){
					const modifyGeometry = self.engineRectangle.get('modifyGeometry');
					if(modifyGeometry){
						const staticGeometry = modifyGeometry.geometry.clone();
						const mutatedGeometry = self.engineRectangle.getGeometry().clone();

						let staticCoordinates = staticGeometry.getCoordinates();
						const mutatedCoordinates = mutatedGeometry.getCoordinates();

						const correctedCoordinates = self.applyModifyCorrections(staticCoordinates, mutatedCoordinates, modifyGeometry.closestIndex);

						if(correctedCoordinates){
							modifyGeometry.geometry.setCoordinates(correctedCoordinates);
						} 
					}
					return self.engineRectangle.modifyDefaultStyle(feature);
				}
			});
			
			this.map.olMap.addInteraction(this.engineRectangle.modifyInteraction);

			this.engineRectangle.modifyInteraction.on("modifystart", function(event) {
			    let closestIndex = 0;
			    if(event.mapBrowserEvent.coordinate){
			    	closestIndex = self.findClosestCoordinateIndex(event.mapBrowserEvent.coordinate);
			    }

				self.engineRectangle.set(
			    	'modifyGeometry',
			    	{ geometry: self.engineRectangle.getGeometry().clone(), closestIndex : closestIndex},
			    	true
			    );
			});

			this.engineRectangle.modifyInteraction.on("modifyend", function(event) {
				const modifyGeometry = self.engineRectangle.get('modifyGeometry');
				if (modifyGeometry) {
			    	self.engineRectangle.setGeometry(modifyGeometry.geometry);
			    	self.engineRectangle.unset('modifyGeometry', true);
				}

				let bounds = self.getBounds();
				if(bounds.north && bounds.west && bounds.south && bounds.east){
					self.setBounds(
						new WPGMZA.LatLng({lat : bounds.north, lng : bounds.west}),
						new WPGMZA.LatLng({lat : bounds.south, lng : bounds.east}),
					);
				}

				self.trigger("change");
			});
		} else {
			if(!this.engineRectangle.modifyInteraction)
				return;
			
			if(this.map){
				this.map.olMap.removeInteraction(this.engineRectangle.snapInteraction);
				this.map.olMap.removeInteraction(this.engineRectangle.modifyInteraction);
			}
			
			delete this.engineRectangle.snapInteraction;
			delete this.engineRectangle.modifyInteraction;
		}
	}

	WPGMZA.OLImageoverlay.prototype.updateNativeFeature = function(){
		let olOptions = this.getScalarProperties();
		
		let bounds = this.getBounds();
		if(bounds.north && bounds.west && bounds.south && bounds.east){
			if(!this.rectLayer){
				this.rectLayer = new ol.layer.Vector({
					source: new ol.source.Vector({
						features: [this.engineRectangle]
					}),
					style: new ol.style.Style({
						geometry : function(feature){
							const modifyGeometry = feature.get('modifyGeometry');
							return modifyGeometry ? modifyGeometry.geometry : feature.getGeometry()
						},
						fill :  new ol.style.Fill({
							color: WPGMZA.hexOpacityToString("#000000", 0)
						}),
						stroke : new ol.style.Stroke({
							color: WPGMZA.hexOpacityToString("#000000", 1),
							width: 1
						})
					})
				});

				this.rectLayer.setZIndex(5);
				this.rectLayer.setVisible(this.isModifying);
			} else {
				this.setBounds(
					new WPGMZA.LatLng({lat : bounds.north, lng : bounds.west}),
					new WPGMZA.LatLng({lat : bounds.south, lng : bounds.east}),
				);
			}
		}
	}

	WPGMZA.OLImageoverlay.prototype.redraw = function(){
		WPGMZA.Imageoverlay.prototype.redraw.apply(this, arguments);

		let options = this.getScalarProperties();
		let bounds = this.getBounds();
		if(bounds.north && bounds.west && bounds.south && bounds.east){
			let url = this.image;
			if(url){
				if(this.olImageoverlay && this.engineRectangle){
					let imageExtent = this.engineRectangle.getGeometry().getExtent();
					imageExtent = [...imageExtent];
					
					let source = new ol.source.ImageStatic(
						{
    						url: url,
    						crossOrigin: '',
    						imageExtent: imageExtent,
    						interpolate: true,
  						}
  					);

  					this.olImageoverlay.setSource(source);
  					this.olImageoverlay.setOpacity(this.opacity);

  					this.olImageoverlay.setZIndex(1);
				}
			}
		}
	}

	WPGMZA.OLImageoverlay.prototype.destroy = function(){
		WPGMZA.Imageoverlay.prototype.destroy.apply(this, arguments);

		if(this.olImageoverlay){
			this.olImageoverlay.setMap(null);
		}
	}

	WPGMZA.OLImageoverlay.prototype.setMap = function(map){
		if(map){
			this.rectLayer.setMap(map);
			this.olImageoverlay.setMap(map);
		} else {
			if(this.rectLayer){
				this.rectLayer.setMap(null);
			}

			if(this.olImageoverlay){
				this.olImageoverlay.setMap(null);
			}
		}
	}

	WPGMZA.OLImageoverlay.prototype.applyModifyCorrections = function(staticCoordinates, mutatedCoordinates, closestIndex){
		if(mutatedCoordinates && staticCoordinates){
			let pointsToUpdate = [];

			staticCoordinates = staticCoordinates.shift();
			mutatedCoordinates = mutatedCoordinates.shift();

			for(let i in mutatedCoordinates){
				if(staticCoordinates[i]){
					let point = mutatedCoordinates[i];
					let ref = staticCoordinates[i];

					if(closestIndex !== i){
						continue;
					}

					if(point[0] !== ref[0] || point[1] !== ref[1]){
						i = parseInt(i);
						if(i !== (mutatedCoordinates.length - 1)){
							pointsToUpdate.push(i);
						}
					}
				}
			}

			if(pointsToUpdate.length){
				for(let k in pointsToUpdate){
					let index = pointsToUpdate[k];
					let point = mutatedCoordinates[index];

					let prevIndex = index - 1;
					if(prevIndex === (mutatedCoordinates.length - 1)){
						prevIndex = 0;
					} else if (prevIndex < 0){
						prevIndex = (mutatedCoordinates.length - 2);
					}

					let nextIndex = index + 1;
					if(nextIndex >= (mutatedCoordinates.length - 1)){
						nextIndex = 0;
					}

					if(mutatedCoordinates[prevIndex]){
						if(index % 2 === 0 || index === 0){
							/* Shift first value of prev point */
							mutatedCoordinates[prevIndex][0] = point[0];
						} else {
							mutatedCoordinates[prevIndex][1] = point[1];
						}
					}
					
					if(mutatedCoordinates[nextIndex]){
						if(index % 2 === 0 || index === 0){
							/* Shift first value of prev point */
							mutatedCoordinates[nextIndex][1] = point[1];
						} else {
							mutatedCoordinates[nextIndex][0] = point[0];
						}
					}
					
					mutatedCoordinates[mutatedCoordinates.length - 1][0] = mutatedCoordinates[0][0];
					mutatedCoordinates[mutatedCoordinates.length - 1][1] = mutatedCoordinates[0][1];
				}

				return [mutatedCoordinates];
			}
		}
		return false;
	}

	WPGMZA.OLImageoverlay.prototype.findClosestCoordinateIndex = function(coord){
		let coordinates = this.engineRectangle.getGeometry().clone().getCoordinates();
		coordinates = coordinates.shift();

		let locatedIndex = null;
		let lastDistance = null;
		for(let i in coordinates){
			const line = new ol.geom.LineString([coord, coordinates[i]]);
			if(lastDistance === null || lastDistance > line.getLength()){
				lastDistance = line.getLength();
				locatedIndex = i;
			}
		}

		return locatedIndex;
	}
});
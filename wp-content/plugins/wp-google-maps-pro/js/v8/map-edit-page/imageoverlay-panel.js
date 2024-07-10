/**
 * @namespace WPGMZA
 * @module ImageoverlayPanel
 * @requires WPGMZA.FeaturePanel
 */
jQuery(function($) {
	
	WPGMZA.ImageoverlayPanel = function(element, mapEditPage){
		var self = this;
		
		WPGMZA.FeaturePanel.apply(this, arguments);

		this.placementOptions = {};

		$(this.element).find('input[data-ajax-name="image"]').on("imagechange", function(event) {
			self.onImageChange();
		});

		$(this.element).find('button[data-placement]').on("click", function(event) {
			event.preventDefault();
			const placement = $(this).data('placement');
			self.onImagePlacementChange(placement);
		});
	}
	
	WPGMZA.extend(WPGMZA.ImageoverlayPanel, WPGMZA.FeaturePanel);
	
	WPGMZA.ImageoverlayPanel.createInstance = function(element, mapEditPage){
		return new WPGMZA.ImageoverlayPanel(element, mapEditPage);
	}
	
	WPGMZA.ImageoverlayPanel.prototype.updateFields = function(){
		var bounds = this.feature.getBounds();
		if(bounds.north && bounds.west && bounds.south && bounds.east){
			$(this.element).find("[data-ajax-name='cornerA']").val( bounds.north + ", " + bounds.west );
			$(this.element).find("[data-ajax-name='cornerB']").val( bounds.south + ", " + bounds.east );
		}
	}

	WPGMZA.ImageoverlayPanel.prototype.setTargetFeature = function(feature){
		WPGMZA.FeaturePanel.prototype.setTargetFeature.apply(this, arguments);
		
		if(feature){
			this.updateFields();
		}

		this.updateOverlayControlBar();
	}
	
	WPGMZA.ImageoverlayPanel.prototype.onDrawingComplete = function(event){
		WPGMZA.FeaturePanel.prototype.onDrawingComplete.apply(this, arguments);
		
		this.updateFields();
	}
	
	WPGMZA.ImageoverlayPanel.prototype.onFeatureChanged = function(event){
		WPGMZA.FeaturePanel.prototype.onFeatureChanged.apply(this, arguments);
		this.updateFields();
	}

	WPGMZA.ImageoverlayPanel.prototype.onImageChange = function(){
		try{
			if(!this.feature){
				/* If we have no feature attached, we can do some prediction to place the image in an intuitive way */
				if($(this.element).find("[data-ajax-name='image']").val().length > 0){
						
						this.predictPlacement();

						if(this.placementOptions && this.placementOptions.contain){
							/* Default to contain mode */
							const predictedRect = new WPGMZA.Rectangle.createInstance({
							    cornerA : new WPGMZA.LatLng(this.placementOptions.contain.topLeft),
							    cornerB : new WPGMZA.LatLng(this.placementOptions.contain.bottomRight)
							});	

							const predictImageoverlay = WPGMZA.Imageoverlay.createInstance({}, predictedRect);

							this.onDrawingComplete({engineImageoverlay : predictImageoverlay});
						}
				}
			}
		} catch (exception){
			/* Something went wrong, catch and release */
		}

		this.updateOverlayControlBar();
	}

	WPGMZA.ImageoverlayPanel.prototype.onImagePlacementChange = function(placement){
		if(this.feature){
			this.predictPlacement();
			if(this.placementOptions && this.placementOptions[placement]){
				this.feature.setBounds(
					new WPGMZA.LatLng(this.placementOptions[placement].topLeft),
					new WPGMZA.LatLng(this.placementOptions[placement].bottomRight)
				);
			}
		}
	}

	WPGMZA.ImageoverlayPanel.prototype.predictPlacement = function(){
		this.placementOptions = {};

		const imgRef = $(this.element).find('.wpgmza-image-single-input-wrapper img');

		const mapDimensions = {
			width : $(this.map.element).width(),
			height : $(this.map.element).height(),
			xH : $(this.map.element).width() / 2,
			yH : $(this.map.element).height() / 2,
		};

		const imgDimensions = {
			width : parseFloat(imgRef.prop("naturalWidth")),
			height : parseFloat(imgRef.prop("naturalHeight")),
			xH : parseFloat(imgRef.prop("naturalWidth")) / 2,
			yH : parseFloat(imgRef.prop("naturalHeight")) / 2,
		};

		if(imgDimensions.width < mapDimensions.width && imgDimensions.height < mapDimensions.height){
			/* Width an height are smaller than container */
			this.placementOptions.contain = {
				topLeft : this.map.pixelsToLatLng(mapDimensions.xH - imgDimensions.xH, mapDimensions.yH - imgDimensions.yH),
				bottomRight : this.map.pixelsToLatLng(mapDimensions.xH + imgDimensions.xH, mapDimensions.yH + imgDimensions.yH)
			};
		} else if (imgDimensions.width < mapDimensions.width){
			/* Height is wider */
			let multi = mapDimensions.height / imgDimensions.height;
			this.placementOptions.contain = {
				topLeft : this.map.pixelsToLatLng(mapDimensions.xH - (imgDimensions.xH * multi), 0),
				bottomRight : this.map.pixelsToLatLng(mapDimensions.xH + (imgDimensions.xH * multi), mapDimensions.height)
			};
		} else {
			/* Width is wider, but height is not */
			/* Edge case, where both are larger, but width will still be the go to here */
			let multi = mapDimensions.width / imgDimensions.width;
			this.placementOptions.contain = {
				topLeft : this.map.pixelsToLatLng(0, mapDimensions.yH - (imgDimensions.yH * multi)),
				bottomRight : this.map.pixelsToLatLng(mapDimensions.width, mapDimensions.yH + (imgDimensions.yH * multi))
			};
		}

		this.placementOptions.stretch = {
			topLeft : this.map.pixelsToLatLng(0, 0),
			bottomRight : this.map.pixelsToLatLng(mapDimensions.width, mapDimensions.height)
		};
	}

	WPGMZA.ImageoverlayPanel.prototype.updateOverlayControlBar = function(){
		if(this.feature && this.feature.image){
			$(this.element).find('.overlay-actions').removeClass('wpgmza-hidden');
		} else {
			$(this.element).find('.overlay-actions').addClass('wpgmza-hidden');
		}
	}
	
});
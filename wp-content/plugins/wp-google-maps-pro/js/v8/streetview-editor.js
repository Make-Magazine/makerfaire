/**
 * @namespace WPGMZA
 * @module StreetViewEditor
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.StreetViewEditor = function(element, map) {
		var self = this;
		
		if(!element)
			throw new Error("Element cannot be undefined");
		
		if(!(element instanceof HTMLElement) && !(element instanceof jQuery && element.length == 1))
			throw new Error("Invalid element");


		this.element = element;
		this.map = map;

		this.isEditing = false;

		this.marker = false;

		this.bindControls();
		this.bindEvents();
		this.updateState();
	}

	WPGMZA.StreetViewEditor.prototype.bindControls = function(){
		this.controls = {};

		this.controls.editBtn = $(this.element).find('.streetview-edit-button');
		this.controls.previewBtn = $(this.element).find('.streetview-preview-button');

		this.controls.positionInput = $(this.element).find('input[name="map_starts_in_streetview_location"]');
		this.controls.headingInput = $(this.element).find('input[name="map_starts_in_streetview_heading"]');
		this.controls.pitchInput = $(this.element).find('input[name="map_starts_in_streetview_pitch"]');

		this.controls.infoBox = $(this.element).find('.streetview-starting-poisition-info');
		this.controls.helpBox = $(this.element).find('.streetview-help-box');
	}	

	WPGMZA.StreetViewEditor.prototype.bindEvents = function() {
		const self = this;

		/* Element Events */

		this.controls.editBtn.on('click', function(){
			self.isEditing = !(self.isEditing);
			self.updateState();
		});

		this.controls.previewBtn.on('click', function(){
			self.onPreview();
		});


		/* Map Events */
		
		this.map.on('streetview_visible_changed', function(event){
			self.onChange(event);
		});

		this.map.on('streetview_position_changed', function(event){
			self.onChange(event);
		});

		this.map.on('streetview_pov_changed', function(event){
			self.onChange(event);
		});
	}

	WPGMZA.StreetViewEditor.prototype.setEditing = function(state){
		this.isEditing = state;
		this.updateState();
	}

	WPGMZA.StreetViewEditor.prototype.updateState = function() {
		if(this.isEditing){
			/* Editing at the moment */
			this.controls.editBtn.text(this.controls.editBtn.data('editing'));

			if(this.hasLocation()){
				this.controls.infoBox.show();

				this.controls.previewBtn.text(this.controls.previewBtn.data('open'));
				this.controls.previewBtn.show();

				this.controls.helpBox.find('.notice-warning').hide();
				this.controls.helpBox.find('.notice-warning[data-help-type="edit"]').show();
			} else {
				this.controls.previewBtn.hide();

				this.controls.helpBox.find('.notice-warning').hide();
				this.controls.helpBox.find('.notice-warning[data-help-type="add"]').show();
			}

			this.controls.helpBox.show();
		} else {
			if(this.hasLocation()){
				this.controls.editBtn.text(this.controls.editBtn.data('edit'));
				
				this.controls.previewBtn.text(this.controls.previewBtn.data('preview'));

				this.controls.previewBtn.show();
			} else {
				/* No poition yet */
				this.controls.editBtn.text(this.controls.editBtn.data('add'));
			}

			this.controls.infoBox.hide();

			this.controls.helpBox.hide();
			this.controls.helpBox.find('.notice-warning').hide();
		}

		this.updateMarker();


	}

	WPGMZA.StreetViewEditor.prototype.onChange = function(event){
		if(this.isEditing){
			if(event.latLng){
				const latlng = new WPGMZA.LatLng(event.latLng);
				this.controls.positionInput.val(latlng.toString());
			}

			if(event.pov){
				if(event.pov.heading){
					this.controls.headingInput.val(event.pov.heading);
				}

				if(event.pov.pitch){
					this.controls.pitchInput.val(event.pov.pitch);
				}
			}

			this.updateState();
		}
	}

	WPGMZA.StreetViewEditor.prototype.onPreview = function(event){
		this.map.openStreetView({
			position : this.getLocation(),
			heading : this.getHeading(),
			pitch : this.getPitch()
		});
	}

	WPGMZA.StreetViewEditor.prototype.updateMarker = function(){
		const self = this;
		if(this.isEditing && this.hasLocation()){
			if(!this.marker){
				this.marker = WPGMZA.Marker.createInstance({
					position : this.getLocation(),
					disableInfoWindow : true,
					icon : WPGMZA.pegmanIcon,
					title : "Click to open",
					// draggable : true
				});

				this.marker.setMap(this.map);

				this.marker.on('click', function(){
					self.onPreview();
				});
				
			} else {
				this.marker.setVisible(true);
			}

			this.marker.setPosition(this.getLocation());
		} else {
			if(this.marker instanceof WPGMZA.Marker){
				/* We have a marker */
				this.marker.setVisible(false);
			}
		}
	}

	WPGMZA.StreetViewEditor.prototype.hasLocation = function(){
		if(this.controls.positionInput.val().trim().length){
			return true;
		}
		return false;
	}

	WPGMZA.StreetViewEditor.prototype.hasHeading = function(){
		if(this.controls.headingInput.val().trim().length){
			return true;
		}
		return false;
	}

	WPGMZA.StreetViewEditor.prototype.hasPitch = function(){
		if(this.controls.pitchInput.val().trim().length){
			return true;
		}
		return false;
	}

	WPGMZA.StreetViewEditor.prototype.getLocation = function(){
		if(this.hasLocation()){
			return new WPGMZA.LatLng(this.controls.positionInput.val());
		}
		return false;
	}

	WPGMZA.StreetViewEditor.prototype.getHeading = function(){
		if(this.hasHeading()){
			return parseFloat(this.controls.headingInput.val());
		} 
		return 0;
	}

	WPGMZA.StreetViewEditor.prototype.getPitch = function(){
		if(this.hasPitch()){
			return parseFloat(this.controls.pitchInput.val());
		} 
		return 0;
	}

});
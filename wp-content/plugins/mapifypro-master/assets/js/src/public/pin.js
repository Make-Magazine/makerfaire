const $ = jQuery;

class Pin {
	constructor( model, tooltipSettings, generalSettings = {} ) {
		this.model = model;
		this.map = null;
		this.mouseover = false;
		this.visibilityConditions = {
			tag: true,
			search: true,
			base: !generalSettings.hideInitially,
		};

		// Add a utility image object if the pin has an image
		var pinImage = null;
		if ( this.model.image.url ) {
			pinImage = new Image();
			pinImage.src = this.model.image.url;
		}
		this.image = pinImage;

		// Add a tooltip to the pin
		var tooltip = null;
		if ( this.model.tooltipEnabled ) {
			tooltip = new MapifyTooltip( {
				classes: 'mpfy-tooltip ' + tooltipSettings.classes,
				rgba: tooltipSettings.background,
				content: tooltipSettings.content,
				close_behavior: tooltipSettings.closeBehavior,
				animate: tooltipSettings.animate,
				pinId: tooltipSettings.pinId,
			} );
		}
		this.tooltip = tooltip;
	}

	isVisible() {
		for ( let option in this.visibilityConditions ) {
			const condition = this.visibilityConditions[option];

			if (!condition) {
				return false;
			}
		}

		return true;
	}

	setVisibility( condition, visible ) {
		this.visibilityConditions[ condition ] = visible;
	}
};

module.exports = Pin;
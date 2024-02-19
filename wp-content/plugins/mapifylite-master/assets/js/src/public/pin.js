const $ = jQuery;

class Pin {
	constructor( model, tooltipSettings ) {
		this.model = model;
		this.map = null;
		this.mouseover = false;
		this.visibilityConditions = {
			tag: true,
			search: true
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
				animate: tooltipSettings.animate
			} );
		}
		this.tooltip = tooltip;
	}

	isVisible() {
		var visible = true;
		for ( var option in this.visibilityConditions ) {
			if ( ! this.visibilityConditions[ option ] ) {
				visible = false;
				break;
			}
		}
		return visible;
	}

	setVisibility( condition, visible ) {
		var wasVisible = this.isVisible();
		this.visibilityConditions[ condition ] = visible;
	}
};

module.exports = Pin;
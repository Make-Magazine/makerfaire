/**
 * @namespace WPGMZA
 * @module GoogleMarkerClusterIcon
 */
jQuery(function($) {
	
	WPGMZA.GoogleMarkerClusterIcon = function(cluster, styles, padding)
	{
		var self = this;
		
		WPGMZA.MarkerClusterIcon.apply(this, arguments);
		
		this.overlay = new WPGMZA.GoogleHTMLOverlay(this.map);
		
		this.overlay.element.on("click", function(event) {
			self.onClick(event);
		});
	}
	
	WPGMZA.GoogleMarkerClusterIcon.prototype = Object.create(WPGMZA.MarkerClusterIcon.prototype);
	WPGMZA.GoogleMarkerClusterIcon.prototype.constructor = WPGMZA.GoogleMarkerClusterIcon;
	
	WPGMZA.GoogleMarkerClusterIcon.prototype.remove = function()
	{
		this.overlay.setMap(null);
	}
	
	WPGMZA.GoogleMarkerClusterIcon.prototype.setCenter = function(center)
	{
		WPGMZA.MarkerClusterIcon.prototype.setCenter.apply(this, arguments);
		
		this.overlay.position = center;
		this.overlay.updateElementPosition();
	}
	
});
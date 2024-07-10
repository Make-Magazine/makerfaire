/**
 * @namespace WPGMZA
 * @module OLMarkerClusterIcon
 */
jQuery(function($) {
	
	WPGMZA.OLMarkerClusterIcon = function(cluster, styles, padding)
	{
		var self = this;
		
		WPGMZA.MarkerClusterIcon.apply(this, arguments);
		
		var origin = ol.proj.fromLonLat([0, 0]);
		
		this.element = $("<div class='wpgmza-marker-cluster-icon'/>");
		
		this.overlay = new ol.Overlay({
			positioning: origin,
			positioning: "top-left"
		});
		
		// $(this.overlay.element).append(this.element);
		
		this.map.olMap.addOverlay(this.overlay);
		
		$(this.overlay.element).on("click", function(event) {
			var markers = cluster.markers.slice();
			
			self.onClick(event);
			
			setTimeout(function() {
				
				markers.forEach(function(marker) {
					marker.setVisible(true);
				});
				
			}, 10);
		});
	}
	
	WPGMZA.OLMarkerClusterIcon.prototype = Object.create(WPGMZA.MarkerClusterIcon.prototype);
	WPGMZA.OLMarkerClusterIcon.prototype.constructor = WPGMZA.OLMarkerClusterIcon;
	
	WPGMZA.OLMarkerClusterIcon.prototype.setCenter = function(center)
	{
		WPGMZA.MarkerClusterIcon.prototype.setCenter.apply(this, arguments);
		
		console.log(this.center.lat, this.center.lng);

		var origin = ol.proj.fromLonLat([
			parseFloat(this.center.lng),
			parseFloat(this.center.lat)
		]);
		
		this.overlay.setPosition(origin);
	}
	
	WPGMZA.OLMarkerClusterIcon.prototype.remove = function()
	{
		this.map.olMap.removeOverlay(this.overlay);
	}
	
});
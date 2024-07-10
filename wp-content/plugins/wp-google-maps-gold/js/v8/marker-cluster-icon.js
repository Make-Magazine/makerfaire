/**
 * @namespace WPGMZA
 * @module MarkerClusterIcon
 */
jQuery(function($) {
	
	WPGMZA.MarkerClusterIcon = function(cluster, styles, padding)
	{
		WPGMZA.EventDispatcher.call(this);
		
		this.styles		= styles;
		this.padding	= padding || 0;
		this.cluster	= cluster;
		this.center		= null;
		this.map		= cluster.getMap();
		
		this.sums		= null;
		this.visible	= false;
	}
	
	WPGMZA.MarkerClusterIcon.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.MarkerClusterIcon.prototype.constructor = WPGMZA.MarkerClusterIcon;
	
	WPGMZA.MarkerClusterIcon.createInstance = function(cluster, styles, padding)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLMarkerClusterIcon(cluster, styles, padding);
				break;
			
			default:
				return new WPGMZA.GoogleMarkerClusterIcon(cluster, styles, padding);
				break;
		}
	}
	
	WPGMZA.MarkerClusterIcon.prototype.getPosFromLatLng = function(latLng)
	{
		return this.map.latLngToPixels(latLng);
	}
	
	WPGMZA.MarkerClusterIcon.prototype.hide = function()
	{
		
	}
	
	WPGMZA.MarkerClusterIcon.prototype.show = function()
	{
		if(this.overlay.element)
		{
			$(this.overlay.element).css(this.getCSS());
			$(this.overlay.element).text(this.sums.text);
		}
	}
	
	WPGMZA.MarkerClusterIcon.prototype.onRemove = function()
	{
		
	}
	
	WPGMZA.MarkerClusterIcon.prototype.setSums = function(sums)
	{
		this.sums = sums;
		this.text = sums.text;
		this.index = sums.index;
		
		this.useStyle();
	}
	
	WPGMZA.MarkerClusterIcon.prototype.useStyle = function()
	{
		var index = Math.max(0, this.sums.index - 1);
		index = Math.min(this.styles.length - 1, index);
		var style = this.styles[index];
		
		this.url = style['url'];
		this.height = style['height'];
		this.width = style['width'];
		this.textColor = style['textColor'];
		this.anchor = style['anchor'];
		this.textSize = style['textSize'];
		this.backgroundPosition = style['backgroundPosition'];
	}
	
	WPGMZA.MarkerClusterIcon.prototype.getCSS = function()
	{
		var styles = {
			"background-image":		"url('" + this.url + "')",
			"background-position":	this.backgroundPosition,
			"width":				this.width + "px",
			"height":				this.height + "px",
			/*"margin-left":			(-this.width / 2) + "px",
			"margin-right":			(-this.height / 2) + "px",*/
			"cursor":				"pointer",
			"line-height":			this.height + "px",
			"text-align":			"center",
			"font-family":			"sans-serif",
			"font-size":			(this.textSize ? this.textSize : 11),
			"color":				(this.textColor ? this.textColor : "black"),
			"font-weight":			"bold"
		};

		if(WPGMZA.settings.engine === 'google-maps'){
			styles["transform"] = "translate(-50%,-50%)";			
		} else {
			styles['margin-left'] = (-this.width / 2) + 'px';
			styles['margin-top'] = (-this.height / 2) + 'px';
		}

		return styles;
	}
	
	WPGMZA.MarkerClusterIcon.prototype.setCenter = function(center)
	{
		this.center = center;
	}
	
	WPGMZA.MarkerClusterIcon.prototype.onClick = function(event)
	{
		if(this.cluster.markerClusterer.isZoomOnClick())
			this.cluster.fitMapToMarkers();
		
		this.trigger("click");
		this.map.trigger("clusterclick");
	}
	
});
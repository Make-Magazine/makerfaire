/**
 * @namespace WPGMZA
 * @module MarkerCluster
 */
jQuery(function($) {
	
	var clusterIndex = 1;
	
	WPGMZA.Cluster = function(markerClusterer)
	{
		//console.log("Cluster " + (clusterIndex++) + " created", this);
		
		if(!(markerClusterer instanceof WPGMZA.MarkerClusterer))
			throw new Error("Argument must be an instance of WPGMZA.MarkerClusterer");
		
		this.markerClusterer	= markerClusterer;
		this.map				= markerClusterer.getMap();
		this.gridSize			= markerClusterer.getGridSize();
		this.minClusterSize		= markerClusterer.getMinClusterSize();
		this.averageCenter		= markerClusterer.isAverageCenter();
		this.center				= null;
		this.markers			= [];
		this.bounds				= null;
		this.clusterIcon		= WPGMZA.MarkerClusterIcon.createInstance(
			this,
			markerClusterer.getStyles(),
			markerClusterer.getGridSize()
		);
	}
	
	WPGMZA.Cluster.prototype.isMarkerAlreadyAdded = function(marker)
	{
		if(this.markers.indexOf)
			return this.markers.indexOf(marker) != -1;
		
		for(var i = 0, m; m = this.markers[i]; i++)
		{
			if(m == marker)
				return true;
		}
		
		return false;
	}
	
	WPGMZA.Cluster.prototype.addMarker = function(marker)
	{
		//console.log("addMarker called", this);
		
		if(this.isMarkerAlreadyAdded(marker))
			return false;
		
		if(!this.center)
		{
			this.center = marker.getPosition();
			this.calculateBounds();
		}
		else
		{
			if(this.averageCenter)
			{
				var l = this.markers.length + 1;
				var p = marker.getPosition();
				
				// NB: Account for prime meridian
				if(this.bounds.west > this.bounds.east)
				{
					if(this.center.lng >= 0){
						p.lng -= 360;
					} else{
						p.lng += 360;
					}
				}
				
				var lat = (this.center.lat * (l - 1) + p.lat) / l;
				var lng = (this.center.lng * (l - 1) + p.lng) / l; 


				this.center = new WPGMZA.LatLng(lat, lng);
				this.calculateBounds();
			}
		}
		
		marker.isClustered = true;
		this.markers.push(marker);
		
		var len = this.markers.length;
		if(len < this.minClusterSize /*&& marker.getMap() != this.map*/)
			this.setMarkerVisbility(marker, true);
		
		if(len == this.minClusterSize)
		{
			for(var i = 0; i < len; i++)
				this.setMarkerVisbility(this.markers[i], false);
		}
		
		if(len >= this.minClusterSize)
			this.setMarkerVisbility(marker, false);
		
		this.updateIcon();
		
		return true;
	}
	
	WPGMZA.Cluster.prototype.getMarkerClusterer = function()
	{
		return this.markerCluster;
	}
	
	WPGMZA.Cluster.prototype.getBounds = function()
	{
		var bounds = new WPGMZA.LatLngBounds(this.center, this.center);
		var markers = this.getMarkers();
		
		for(var i = 0, marker; marker = markers[i]; i++)
		{
			bounds.extend(marker.getPosition());
		}
		
		return bounds;
	}
	
	WPGMZA.Cluster.prototype.remove = function()
	{
		this.clusterIcon.remove();
		
		if(this.markers)
			this.markers.length = 0;
		
		delete this.markers;
	}
	
	WPGMZA.Cluster.prototype.getSize = function()
	{
		return this.markers.length;
	}
	
	WPGMZA.Cluster.prototype.getMarkers = function()
	{
		return this.markers;
	}
	
	WPGMZA.Cluster.prototype.getCenter = function()
	{
		return this.center;
	}
	
	WPGMZA.Cluster.prototype.calculateBounds = function()
	{
		var bounds = new WPGMZA.LatLngBounds(this.center, this.center);
		this.bounds = this.markerClusterer.getExtendedBounds(bounds);
	}
	
	WPGMZA.Cluster.prototype.isMarkerInClusterBounds = function(marker)
	{		
		return this.bounds.contains(marker.getPosition());
	}
	
	WPGMZA.Cluster.prototype.setMarkerVisbility = function(marker, visible)
	{
		marker.setVisible(visible && (!marker.separatorGroup || marker.separatorGroup.state != WPGMZA.MarkerSeparatorGroup.STATE_CLOSED));
		
		if(marker.separatorGroup)
			marker.separatorGroup.placeholder.setVisible(visible);
	}
	
	WPGMZA.Cluster.prototype.getMap = function()
	{
		return this.map;
	}
	
	WPGMZA.Cluster.prototype.updateIcon = function()
	{
		var zoom = this.map.getZoom();
		var mz = this.markerClusterer.getMaxZoom();
		
		if(mz && zoom > mz)
		{
			for(var i = 0, marker; marker = this.markers[i]; i++)
				this.setMarkerVisbility(marker, true);
			
			return;
		}
		
		if(this.markers.length < this.minClusterSize)
		{
			this.clusterIcon.hide();
			return;
		}
		
		var numStyles = this.markerClusterer.getStyles().length;
		var sums = this.markerClusterer.getCalculator()(this.markers, numStyles);
		
		this.clusterIcon.setCenter(this.center);
		this.clusterIcon.setSums(sums);
		this.clusterIcon.show();
	}
	
	WPGMZA.Cluster.prototype.fitMapToMarkers = function()
	{
		var markers = this.markers;
		var bounds = new WPGMZA.LatLngBounds();
		
		for(var i = 0, marker; marker = markers[i]; i++)
		{
			bounds.extend(marker.getPosition());
			this.setMarkerVisbility(marker, true);
		}
		
		if(bounds.west > bounds.east)
		{
			var temp = bounds.west;
			
			bounds.west = bounds.east + 360;
			bounds.east = temp;
			
			this.map.fitBounds(bounds);
		}
		else
			this.map.fitBounds(bounds);
		
	}
	
});
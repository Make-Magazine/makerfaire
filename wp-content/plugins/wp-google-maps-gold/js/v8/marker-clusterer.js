/**
 * @namespace WPGMZA
 * @module MarkerClusterer
 */
jQuery(function($) {
	
	/*
	 * This is an adaption of http://closure-compiler.googlecode.com/svn/trunk/contrib/externs/maps/google_maps_api_v3_3.js
	 * Our adaption works with native markers to support both Google and OpenLayers. It also differentiates between filtered and visible markers for correct counts
	 */
	
	WPGMZA.MarkerClusterer = function(map, markers, options)
	{
		WPGMZA.MarkerClusterer.log("Clusterer created", this);
		
		var self = this;
		
		this.ready = false;
		this.map				= map;
		this._markers			= [];
		this.clusters			= [];
		this.sizes				= [53, 56, 66, 78, 90];
		this.options			= options || {};
		this.gridSize			= options["gridSize"] || 60;
		this.minClusterSize		= options["minClusterSize"] || 2;
		this.maxZoom			= options["maxZoom"] || null;
		this.styles				= options["styles"] || null;
		this.imagePath			= options["imagePath"] || WPGMZA.goldPluginDirURL + 'images/m';
		this.imageExtension		= options["imageExtension"] || "png";
		this.zoomOnClick		= true;
		this.averageCenter		= true;
		
		if(options["zoomOnClick"] != undefined)
			this.zoomOnClick = options["zoomOnClick"];
		
		this.setupStyles();
		this.setMap(map);
		
		this.prevZoom = map.getZoom();
		
		this.map.on("zoomchanged", function() {
			
			var zoom = map.getZoom();
			
			if(self.prevZoom != zoom)
			{
				self.prevZoom = zoom;
				self.resetViewport();
			}
				
		});
		
		this.map.on("filteringcomplete", function() {
			self.shouldRegenNVC = true;

			self.resetViewport();
			self.redraw();
				
		});
		
		this.map.on("idle", function() {
			
			self.ready = true;
			self.resetViewport();
			self.redraw();
			
		});

	
		if(markers && markers.length)
			this.addMarkers(markers, false);
	}
	
	WPGMZA.MarkerClusterer.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.MarkerClusterer.prototype.constructor = WPGMZA.MarkerClusterer;
	
	WPGMZA.MarkerClusterer.enableDebugLog = false;
	WPGMZA.MarkerClusterer.log = (WPGMZA.MarkerClusterer.enableDebugLog ? console.log : function() {});
	
	Object.defineProperty(WPGMZA.MarkerClusterer.prototype, "markers", {
		
		"get": function() {
			return this._markers;
		},
		
		"set": function(value) {
			WPGMZA.MarkerClusterer.log("Setting markers to", value);
			this._markers = value;
		}
		
	});
	
	WPGMZA.MarkerClusterer.prototype.setupStyles = function()
	{
		WPGMZA.MarkerClusterer.log("setupStyles called", this);
		
		if(this.styles && this.styles.length)
			return;
		
		this.styles = [];
		
		for(var i = 0, size; size = this.sizes[i]; i++)
		{
			this.styles.push({
				url: this.imagePath + (i + 1) + "." + this.imageExtension,
				height: size,
				width: size
			});
		}
	}
	
	WPGMZA.MarkerClusterer.prototype.setStyles = function(styles)
	{
		WPGMZA.MarkerClusterer.log("setStyles called", this);
		
		this.styles = styles;
	}
	
	WPGMZA.MarkerClusterer.prototype.getStyles = function()
	{
		return this.styles;
	}
	
	WPGMZA.MarkerClusterer.prototype.isZoomOnClick = function()
	{
		return this.zoomOnClick;
	}
	
	WPGMZA.MarkerClusterer.prototype.isAverageCenter = function()
	{
		return this.averageCenter;
	}
	
	WPGMZA.MarkerClusterer.prototype.calculator = function(markers, numStyles)
	{
		var index = 0;
		var count = markers.length;
		var dv = count;
		
		while (dv !== 0) {
			dv = parseInt(dv / 10, 10);
			index++;
		}

		index = Math.min(index, numStyles);
		
		return {
			text: count,
			index: index
		};
	}
	
	WPGMZA.MarkerClusterer.prototype.getCalculator = function()
	{
		return this.calculator;
	}
	
	WPGMZA.MarkerClusterer.prototype.setCalculator = function(calculator)
	{
		this.calculator = calculator;
	}
	
	WPGMZA.MarkerClusterer.prototype.addMarker = function(marker, nodraw)
	{
		if(!(marker instanceof WPGMZA.Marker))
			throw new Error("First argument must be an instance of WPGMZA.Marker");
		
		this.pushMarkerTo(marker);
		
		if(!nodraw)
			this.redraw();
	}
	
	WPGMZA.MarkerClusterer.prototype.addMarkers = function(markers, nodraw)
	{
		WPGMZA.MarkerClusterer.log("addMarkers called", this);
		
		for(var i = 0, marker; marker = markers[i]; i++)
		{
			if(!(marker instanceof WPGMZA.Marker))
				throw new Error("Value is not an instance of WPGMZA.Marker");
			
			this.pushMarkerTo(marker);
		}
		
		if(!nodraw)
			this.redraw();
	}
	
	WPGMZA.MarkerClusterer.prototype.pushMarkerTo = function(marker)
	{
		var self = this;
		
		marker.isClustered = false;
		
		/*if(marker.isDraggable())
		{
			marker.on("dragend", function() {
				marker.isClustered = false;
				self.repaint();
			});
		}*/
		
		this.markers.push(marker);
	}
	
	WPGMZA.MarkerClusterer.prototype.getMarkers = function()
	{
		return this.markers;
	}
	
	WPGMZA.MarkerClusterer.prototype.getTotalMarkers = function()
	{
		return this.markers.length;
	}
	
	WPGMZA.MarkerClusterer.prototype.removeMarker_ = function(marker)
	{
		var index = -1;
		if(this.markers.indexOf)
			index = this.markers.indexOf(marker);
		else
			for(var i = 0, m; m = this.markers[i]; i++) {
				if(m == marker)
				{
					index = i;
					break;
				}
			}
		
		if(index == -1)
			return false;
		
		marker.setVisible(false);
		
		this.markers.splice(index, 1);
		
		return true;
	}
	
	WPGMZA.MarkerClusterer.prototype.removeMarker = function(marker, nodraw)
	{
		var removed = this.removeMarker_(marker);
		
		if(nodraw && removed)
		{
			this.resetViewport();
			this.redraw();
			return true;
		}
		
		return false;
	}
	
	WPGMZA.MarkerClusterer.prototype.removeMarkers = function(markers, nodraw)
	{
		WPGMZA.MarkerClusterer.log("removeMarkers called", this);
		
		var removed = false;
		
		for(var i = 0, marker; marker = markers[i]; i++)
		{
			var r = this.removeMarker_(marker);
			removed = removed || r;
		}
		
		if(nodraw && removed)
		{
			this.resetViewport();
			this.redraw();
			
			return true;
		}
	}
	
	WPGMZA.MarkerClusterer.prototype.setReady = function(ready)
	{
		if(!this.ready)
		{
			this.ready = ready;
			this.createClusters();
		}
	}
	
	WPGMZA.MarkerClusterer.prototype.getTotalClusters = function()
	{
		return this.clusters.length;
	}
	
	WPGMZA.MarkerClusterer.prototype.getMap = function()
	{
		return this.map;
	}
	
	WPGMZA.MarkerClusterer.prototype.setMap = function(map)
	{
		WPGMZA.MarkerClusterer.log("setMap called", this);
		this.map = map;
	}
	
	WPGMZA.MarkerClusterer.prototype.getGridSize = function()
	{
		return this.gridSize;
	}
	
	WPGMZA.MarkerClusterer.prototype.setGridSize = function(size)
	{
		WPGMZA.MarkerClusterer.log("setGridSize called", this);
		this.gridSize = size;
	}
	
	WPGMZA.MarkerClusterer.prototype.getMinClusterSize = function()
	{
		return this.minClusterSize;
	}
	
	WPGMZA.MarkerClusterer.prototype.setMinClusterSize = function(size)
	{
		WPGMZA.MarkerClusterer.log("setMinClusterSize called", this);
		this.minClusterSize = size;
	}
	
	WPGMZA.MarkerClusterer.prototype.getMaxZoom = function()
	{
		return this.maxZoom;
	}
	
	WPGMZA.MarkerClusterer.prototype.setMaxZoom = function(maxZoom)
	{
		WPGMZA.MarkerClusterer.log("setMaxZoom called", this);
		
		this.maxZoom = maxZoom;
	}
	
	WPGMZA.MarkerClusterer.prototype.getExtendedBounds = function(bounds)
	{
		WPGMZA.MarkerClusterer.log("Getting bounds extended by " + this.gridSize);
		
		bounds.extendByPixelMargin(this.map, this.gridSize);
		return bounds;
	}
	
	WPGMZA.MarkerClusterer.prototype.clearMarkers = function()
	{
		WPGMZA.MarkerClusterer.log("clearMarkers called", this);
		
		this.resetViewport(true);
		
		if(window.WPGMZA && WPGMZA.pro_version && WPGMZA.Version.compare(WPGMZA.pro_version, "7.11.00") == WPGMZA.Version.GREATER_THAN)
			return;
		
		this.markers = [];
	}
	
	WPGMZA.MarkerClusterer.prototype.resetViewport = function(hide)
	{
		WPGMZA.MarkerClusterer.log("resetViewport called", this);
		
		for(var i = 0, cluster; cluster = this.clusters[i]; i++)
			cluster.remove();
		
		for(var i = 0, marker; marker = this.markers[i]; i++)
		{
			marker.isClustered = false;
			
			if(hide)
			{
				// NB: Trialing this out
				//marker.setMap(null);
				marker.setVisible(false);
			}
		}
		
		this.clusters = [];
	}
	
	WPGMZA.MarkerClusterer.prototype.distanceBetweenPoints = function(p1, p2)
	{
		return WPGMZA.Distance.between(p1, p2);
	}
	
	WPGMZA.MarkerClusterer.prototype.addToClosestCluster = function(marker)
	{
		WPGMZA.MarkerClusterer.log("addToClosestCluster called", this);
		
		WPGMZA.MarkerClusterer.log("Adding marker to nearest cluster", marker);
		
		var distance = 40000;
		var clusterToAddTo = null;
		var pos = marker.getPosition();
		
		WPGMZA.MarkerClusterer.log("Marker is at " + pos.toString());
		WPGMZA.MarkerClusterer.log("Checking " + this.clusters.length + " clusters");
		
		for(var i = 0, cluster; cluster = this.clusters[i]; i++)
		{
			var center = cluster.getCenter();
			
			WPGMZA.MarkerClusterer.log("Comparing against cluster ", center.toString());
			
			if(center)
			{
				var d = this.distanceBetweenPoints(center, marker.getPosition());
				
				WPGMZA.MarkerClusterer.log("Distance is " + d);
				
				if(d < distance)
				{
					distance = d;
					clusterToAddTo = cluster;
				}
			}
		}
		
		var isWithinBounds = clusterToAddTo && clusterToAddTo.isMarkerInClusterBounds(marker);
		
		WPGMZA.MarkerClusterer.log(clusterToAddTo ? "Marker is " + (isWithinBounds ? " WITHIN " : " NOT WITHIN ") + "cluster bounds " + clusterToAddTo.bounds.toString() : "No cluster to add to");
		
		if(clusterToAddTo && isWithinBounds)
			clusterToAddTo.addMarker(marker);
		else
		{
			WPGMZA.MarkerClusterer.log("Creating a new cluster for the marker");
			
			var cluster = new WPGMZA.Cluster(this);
			
			cluster.addMarker(marker);
			this.clusters.push(cluster);
		}
	}
	
	WPGMZA.MarkerClusterer.prototype.createClusters = function()
	{
		WPGMZA.MarkerClusterer.log("createClusters called for " + this.markers.length + " markers", this);
		
		if(!this.ready || parseInt(WPGMZA.is_admin) === 1)
			return;
		
		WPGMZA.MarkerClusterer.log("Creating clusters for " + this.markers.length + " markers");
		
		var bounds = this.getExtendedBounds(this.map.getBounds());
		
		WPGMZA.MarkerClusterer.log(this.map.getBounds());
		
		for(var i = 0, marker; marker = this.markers[i]; i++)
		{
			// TODO: Optimize this by putting boundary check inline, or use continue
			
			WPGMZA.MarkerClusterer.log("Checking marker " + marker.id + " (" + marker.lat + ", " + marker.lng + ") is within bounds " + bounds.toString());
			
			var isWithinBounds = this.isMarkerInBounds(marker, bounds);
			
			WPGMZA.MarkerClusterer.log(isWithinBounds);
			
			if(!marker.isClustered && isWithinBounds && !marker.isFiltered) {
				
				WPGMZA.MarkerClusterer.log(marker, marker.isFiltered);
				
				this.addToClosestCluster(marker);
				
			}
		}
	}
	
	WPGMZA.MarkerClusterer.prototype.isMarkerInBounds = function(marker, bounds)
	{
		return bounds.contains(marker.getPosition());
	}
	
	WPGMZA.MarkerClusterer.prototype.repaint = function()
	{
		WPGMZA.MarkerClusterer.log("repaint called", this);
		
		var oldClusters = this.clusters.slice();
		
		this.clusters.length = 0;
		this.resetViewport();
		this.redraw();
		
		setTimeout(function() {
			
			for(var i = 0, cluster; cluster = oldClusters[i]; i++)
				cluster.remove();
			
		}, 0);
	}
	
	WPGMZA.MarkerClusterer.prototype.redraw = function()
	{
		WPGMZA.MarkerClusterer.log("redraw called", this);

		if(typeof this.shouldRegenNVC === 'undefined'){
			this.shouldRegenNVC = true;
		}

		if(this.maxZoom && this.map.getZoom() >= this.maxZoom){
			this.map.markers.forEach(function(marker) {
				if(!marker.isFiltered && (!marker.separatorGroup || marker.separatorGroup.state != WPGMZA.MarkerSeparatorGroup.STATE_CLOSED)){
					marker.setVisible(true);
				}
			});

			/* Proxy NVC Relay */
			if(WPGMZA.settings.wpgmza_near_vicinity_control_enabled && this.shouldRegenNVC){
				this.shouldRegenNVC = false;
				if(this.map){
					if(this.map.markerSeparator){
						if(this.map.markerSeparator.groups && this.map.markerSeparator.groups.length > 0){
							for(var gI in this.map.markerSeparator.groups){
								var groupMarker = this.map.markerSeparator.groups[gI].placeholder;
								this.map.removeMarker(groupMarker);
							}
						}
						this.map.markerSeparator.destroy();
					}
				
					this.map.markerSeparator = new WPGMZA.MarkerSeparator(this.map.id);
			   }
			}
			
			return;
		}

		this.shouldRegenNVC = true;

		this.setReady(true);
		this.createClusters();
	}
	
	WPGMZA.MarkerClusterer.prototype.draw = function()
	{
		
	}
	
});
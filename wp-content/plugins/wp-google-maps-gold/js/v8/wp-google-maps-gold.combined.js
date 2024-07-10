
// js/v8/cache-settings-panel.js
/**
 * @namespace WPGMZA
 * @module CachingSettingsPanel
 */
jQuery(function($) {
	
	WPGMZA.CachingSettingsPanel = function(){
		var self = this;
		this.updateCachingContols();

        $('input[name="enable_caching"]').on('change', function(event) {
			self.updateCachingContols();
		});

	}

    /**
	 * Update caching controls visibility based on the root option
     * 
	 * @method
	 * @memberof WPGMZA.CachingSettingsPanel
	*/
	WPGMZA.CachingSettingsPanel.prototype.updateCachingContols = function(){
		const showControls =  $("input[name='enable_caching']").prop("checked");
		if(showControls){
			$('.caching-conditional-row').show();
		} else {
			$('.caching-conditional-row').hide();
		}
	}

    $(document).ready(function() {
		if(WPGMZA.currentPage == "map-settings"){
			WPGMZA.cachingSettingsPanel = new WPGMZA.CachingSettingsPanel();
        }
	});
});

// js/v8/live-tracker.js
/**
 * @namespace WPGMZA
 * @module LiveTracker
 */
jQuery(function($) {
	
	WPGMZA.LiveTracker = function()
	{
		var self = this;

		let refreshInterval = 60000;
		if(typeof WPGMZA.settings !== 'undefined' && typeof WPGMZA.settings.liveTrackingRefreshInterval !== 'undefined'){
			if(parseInt(WPGMZA.settings.liveTrackingRefreshInterval) > 0){
				refreshInterval = parseInt(WPGMZA.settings.liveTrackingRefreshInterval);
			}
		}

		this.update();
		this.intervalID = setInterval(function() {
			self.update();
		}, refreshInterval);

	}
	
	WPGMZA.LiveTracker.prototype.update = function()
	{
		var mapIDs = [];
		
		WPGMZA.maps.forEach(function(map) {
			mapIDs.push(map.id);
		});
		
		if(mapIDs.length == 0)
			return;
		
		WPGMZA.restAPI.call("/live-tracker/devices/", {
			data: {
				"map_ids": mapIDs.join(",")
			},
			success: function(data, status, xhr) {
				
				for(var i = 0; i < data.length; i++)
				{
					var device = data[i];
					
					if(!device.marker)
						continue;
					
					var map = WPGMZA.getMapByID(device.marker.map_id);
					
					if(!map)
						continue;
					
					var marker = map.getMarkerByID(device.marker.id);
					
					if(!marker)
					{
						// This marker doesn't exist on the map, so create it
						marker = WPGMZA.Marker.createInstance(device.marker);
						map.addMarker(marker);
					}
					
					marker.setPosition(new WPGMZA.LatLng({
						lat: device.marker.lat,
						lng: device.marker.lng
					}));
				}
				
			}
		});
	}
	
	$(document).ready(function() {
		
		if(!WPGMZA.settings.enable_live_tracking)
			return;
		
		WPGMZA.liveTracker = new WPGMZA.LiveTracker();
		
	});
	
});

// js/v8/live-tracking-settings-panel.js
/**
 * @namespace WPGMZA
 * @module LiveTrackingSettingsPanel
 */
jQuery(function($) {
	
	WPGMZA.LiveTrackingSettingsPanel = function()
	{
		var self = this;
		
		this.templateTableItem = $("#wpgmza-live-tracking-devices>tbody>tr");
		this.templateTableItem.remove();
		
		this.refresh();
		
		$("#wpgmza-refresh-live-tracking-devices").on("click", function() {
			self.refresh();
		});
		
		$("#wpgmza-live-tracking-devices").on("change", "input, select, textarea", function(event) {
			self.onDeviceChanged(event);
		});
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.clear = function()
	{
		$("#wpgmza-live-tracking-devices>tbody").html("");
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.refresh = function()
	{
		var self = this;
		
		$("#wpgmza-live-tracking-devices").css("opacity", 0.5);
		
		WPGMZA.restAPI.call("/live-tracker/devices/", {
			success: function(data, status, xhr) {
				self.populate(data);
			}
		});
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.populate = function(devices)
	{
		var self = this;
		var tbody = $("#wpgmza-live-tracking-devices>tbody");
		
		this.clear();
		
		devices.forEach(function(data) {
			
			var item = self.templateTableItem.clone();
			
			for(var name in data)
			{
				var el = $(item).find("[data-name='" + name + "'], [data-ajax-name='" + name + "']");
				
				if(!el.length)
					continue;
				
				if(el.prop("tagName").toLowerCase() != 'input')
				{
					el.text(data[name]);
					continue;
				}
					
				switch(el.attr("type"))
				{
					case "checkbox":
						$(el).prop("checked", data[name] == 1);
						break;
					
					default:
						$(el).val(data[name]);
						break;
				}
			}
			
			tbody.append(item);
			
		});
		
		$("#wpgmza-live-tracking-devices").css("opacity", 1);
	}
	
	WPGMZA.LiveTrackingSettingsPanel.prototype.onDeviceChanged = function(event)
	{
		var row = $(event.target).closest("tr");
		var fields = $(row).find("input[data-name], input[data-ajax-name]");
		var data = {};
		var id = $(row).find("input[data-ajax-name='id']").val();
		
		$("#wpgmza-live-tracking-devices").css("opacity", 0.5);
		
		fields.each(function(index, el) {
			
			var name = $(el).attr("data-name");
			if(!name || !name.length)
				name = $(el).attr("data-ajax-name");
			
			switch($(el).attr("type"))
			{
				case "checkbox":
					data[name] = $(el).prop("checked") ? 1 : 0;
					break;
				
				default:
					data[name] = $(el).val();
					break;
			}
			
		});
		
		WPGMZA.restAPI.call("/live-tracker/devices/" + id, {
			method: "POST",
			data: data,
			success: function(data, status, xhr) {
				
				$("#wpgmza-live-tracking-devices").css("opacity", 1);

				
			}
		});
		
		// console.log(data);
		
	}
	
	$(document).ready(function() {
		
		if(WPGMZA.currentPage == "map-settings")
			WPGMZA.liveTrackingSettingsPanel = new WPGMZA.LiveTrackingSettingsPanel();
		
	});
	
});

// js/v8/marker-cluster-icon.js
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

// js/v8/marker-cluster.js
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

// js/v8/marker-clusterer.js
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

// js/v8/marker-separator-group.js
/**
 * @namespace WPGMZA
 * @module MarkerSeparatorGroup
 */
jQuery(function($) {
	
	WPGMZA.MarkerSeparatorGroup = function()
	{
		var self = this;
		
		this.state = WPGMZA.MarkerSeparatorGroup.STATE_CLOSED;
		this.markers = [];
		
		this.placeholder = WPGMZA.Marker.createInstance();
		this.placeholder.disableInfoWindow = true;
		
		this.placeholder.on("click", function(event) {
			
			if(self.state == WPGMZA.MarkerSeparatorGroup.STATE_CLOSED)
				self.open();
			else
				self.close();
			
		});
	}
	
	WPGMZA.MarkerSeparatorGroup.STATE_CLOSED		= "closed";
	WPGMZA.MarkerSeparatorGroup.STATE_OPEN			= "open";
	
	/**
	 * These algorithms return "normalized" offset coordinates - that is,
	 * one unit is the size of a marker. You can multiply this, for instance,
	 * by the icon width, to get decent separation
	 */
	WPGMZA.MarkerSeparatorGroup.algorithms = {
		
		"circle": function(count) {
			
			var circumfrence = count;
			var radius = circumfrence / Math.PI;
			var angle = 0.0;
			var increment = (Math.PI * 2) / count;
			
			var result = [];
			
			for(var i = 0; i < count; i++)
			{
				result.push({
					x: Math.cos(angle) * radius / 2,
					y: Math.sin(angle) * radius / 2
				});
				angle += increment;
			}
			
			return result;
			
		},
		
		"spiral": function(count) {
			
			var radius = count / 10;
			var coils = count / 10;
			
			var thetaMax = coils * 2 * Math.PI;
			var awayStep = radius / thetaMax;
			
			var chord = 1;
			
			var theta = chord / awayStep;
			var result = [];
			
			for(var i = 1; i <= count; i++)
			{
				var away = awayStep * theta;
				var around = theta;
				
				result.push({
					x: Math.cos(around) * away,
					y: Math.sin(around) * away
				});
				
				theta += chord / away;
			}
			
			return result;
			
		},	
		
		"hexagon": function(count) {
			
			var result = WPGMZA.MarkerSeparatorGroup.algorithms.grid(count);
			var squareRootOf3 = Math.sqrt(3);
			
			for(var i = 0; i < result.length; i++)
			{
				var coord = result[i];
				coord.x = (coord.x + (coord.y / 2));
				coord.y = ((squareRootOf3 / 2) * coord.y);
			}
			
			return result;
		},
		
		"line": function(count) {
			
			var result = [];
			var x = -(count - 1) / 2;
			var y = 1;
			
			for(var i = 0; i < count; i++)
			{
				result.push({x: x, y: y});
				
				x++;
			}
			
			return result;
			
		},
		
		"grid": function(count) {
			
			var result = [];
			var x = 0, y = 0;
			
			function add(x, y)
			{
				result.push({x: x, y: y});
			}
			
			for(var i = 1; result.length <= count; i++)
			{
				for(var j = 0; j < i; ++j)
					add(++x, y);
				
				for(j = 0; j < i - 1; ++j)
					add(x, ++y);
				
				for(j = 0; j < i; ++j)
					add(--x, ++y);
				
				for(j = 0; j < i; ++j)
					add(--x, y);
			
				for(j = 0; j < i; ++j)
					add(x, --y);
					
				for(j = 0; j < i; ++j)
					add(++x, --y);
			}
			
			return result;
			
		},
		
		"random": function(count) {
			
		}
		
	};
	
	WPGMZA.MarkerSeparatorGroup.prototype.addMarker = function(marker)
	{
		if(!(marker instanceof WPGMZA.Marker || (window.google && google.maps && marker instanceof google.maps.Marker)))
			throw new Error("Argument must be an instance of WPGMZA.Marker or google.maps.Marker");
		
		if(marker.separatorGroup && marker.separatorGroup != this)
			throw new Error("Marker is already in a separator group");
		
		this.markers.push(marker);
		
		marker.setVisible(false);
		marker.separatorGroup = this;
		marker.positionBeforeSeparation = WPGMZA.MarkerSeparator.getNativeLatLng( marker.getPosition() );
		
		this.updatePlaceholder();
	}
	
	WPGMZA.MarkerSeparatorGroup.prototype.getAverageMarkerPostion = function()
	{
		if(this.markers.length == 0)
			return null;
		
		var averagePosition = new WPGMZA.LatLng( this.markers[0].positionBeforeSeparation );
		
		if(this.markers.length < 2)
			return averagePosition;
		
		for(var i = 1; i < this.markers.length; i++)
		{
			var position = this.markers[i].positionBeforeSeparation;
			
			averagePosition.lat += position.lat;
			averagePosition.lng += position.lng;
		}
		
		averagePosition.lat /= this.markers.length;
		averagePosition.lng /= this.markers.length;
		
		for(var i = 0; i < this.markers.length; i++)
		{
			if(WPGMZA.isProVersionBelow7_10_00)
				this.markers[i].setPosition( this.markers[i].positionBeforeSeparation.toGoogleLatLng() );
			else
				this.markers[i].setPosition( this.markers[i].positionBeforeSeparation );
		}
		
		return averagePosition;
	}
	
	WPGMZA.MarkerSeparatorGroup.prototype.updatePlaceholder = function()
	{
		var position = this.getAverageMarkerPostion();
		var icon = this.markers[0].getIcon();
		
		this.placeholder.setPosition(position);
		
		if(WPGMZA.settings.marker_separator_placeholder_icon && WPGMZA.settings.marker_separator_placeholder_icon.length)
			icon = WPGMZA.settings.marker_separator_placeholder_icon;
		
		if(WPGMZA.isProVersionBelow7_10_00)
			this.placeholder.googleMarker.setIcon(icon);
		else if(icon)
			this.placeholder.setIcon(icon);
		
		var areAllMarkersClustered = true;
		
		for(var i = 0; i < this.markers.length; i++)
		{
			if(!this.markers[i].isClustered)
			{
				areAllMarkersClustered = false;
				break;
			}
		}
		
		this.placeholder.setVisible(!areAllMarkersClustered);
	}
	
	WPGMZA.MarkerSeparatorGroup.prototype.open = function()
	{
		if(this.state == WPGMZA.MarkerSeparatorGroup.STATE_OPEN)
			return;
		
		if(WPGMZA.MarkerSeparatorGroup.lastGroupOpened && 
			WPGMZA.MarkerSeparatorGroup.lastGroupOpened != this &&
			WPGMZA.MarkerSeparatorGroup.lastGroupOpened.state != WPGMZA.MarkerSeparatorGroup.CLOSED)
			WPGMZA.MarkerSeparatorGroup.lastGroupOpened.close();
		
		var algorithm = WPGMZA.MarkerSeparatorGroup.algorithms.circle;
		
		if(WPGMZA.settings.marker_separator_algorithm)
			algorithm = WPGMZA.MarkerSeparatorGroup.algorithms[ WPGMZA.settings.marker_separator_algorithm ];
		
		var offsets = algorithm(this.markers.length);
		var multiplier = 64;
		
		var duration = WPGMZA.settings.marker_separator_animation_duration * 1000;
		var animate = WPGMZA.settings.marker_separator_animate;
		var stagger = WPGMZA.settings.marker_separator_stagger_animation ? WPGMZA.settings.marker_separator_stagger_interval * 1000 : 0;
		
		function doAnimation(marker, offsetX, offsetY, delay)
		{
			if(!delay)
				$(marker).animate({
					offsetX: offsetX * multiplier,
					offsetY: offsetY * multiplier,
				},
				duration);
			else
				setTimeout(function() {
					$(marker).animate({
						offsetX: offsetX * multiplier,
						offsetY: offsetY * multiplier,
					},
					duration);
				}, delay);
		}
		
		for(var i = 0; i < this.markers.length; i++)
		{
			var marker = this.markers[i];
			
			marker.setVisible(true);
			
			if(!animate)
			{
				marker.setOffset(offsets[i].x * multiplier, offsets[i].y * multiplier);
			}
			else
			{
				var delay = (stagger ? stagger * i : 0);
				doAnimation(marker, offsets[i].x, offsets[i].y, delay);
			}
		}
		
		this.state = WPGMZA.MarkerSeparatorGroup.STATE_OPEN;
		WPGMZA.MarkerSeparatorGroup.lastGroupOpened = this;
	}
	
	WPGMZA.MarkerSeparatorGroup.prototype.close = function()
	{
		if(this.state == WPGMZA.MarkerSeparatorGroup.STATE_CLOSE)
			return;
		
		var duration = WPGMZA.settings.marker_separator_animation_duration * 1000;
		var animate = WPGMZA.settings.marker_separator_animate;
		var stagger = WPGMZA.settings.marker_separator_stagger_animation ? WPGMZA.settings.marker_separator_stagger_interval * 1000 : 0;
		
		function doAnimation(marker, delay)
		{
			if(!delay)
				$(marker).animate({
					offsetX: 0,
					offsetY: 0,
				},
				duration,
				function() {
					this.setVisible(false);
					if(this.infoWindow){
						this.infoWindow.close();
					} else if(window.infoWindow && window.infoWindow[this.id]){
						infoWindow[this.id].close();
					}
				});
			else
				setTimeout(function() {
					$(marker).animate({
						offsetX: 0,
						offsetY: 0,
					},
					duration,
					function() {
						this.setVisible(false);
						if(window.infoWindow && window.infoWindow[this.id]){
							infoWindow[this.id].close();
						}
					})
				}, delay);
		}
		
		for(var i = 0; i < this.markers.length; i++)
		{
			var marker = this.markers[i];
			
			if(!animate){
				marker.setOffset(0, 0);
				marker.setVisible(false);
			} else {
				var delay = (stagger ? stagger * i : 0);
				doAnimation(marker, delay);
			}
		}
		
		this.state = WPGMZA.MarkerSeparatorGroup.STATE_CLOSED;
	}
	
});

// js/v8/marker-separator-settings-panel.js
/**
 * @namespace WPGMZA
 * @module MarkerSeparatorSettingsPanel
 */
jQuery(function($) {
	
	$(document).ready(function(event) {
		
		var el = $("#marker-separator-placeholder-icon-picker-container > .wpgmza-marker-icon-picker");
		
		if(!el.length)
			return;
		
		new WPGMZA.MarkerIconPicker(el);
		
	});
	
});

// js/v8/marker-separator.js
/**
 * @namespace WPGMZA
 * @module MarkerSeparator
 */
jQuery(function($) {
	
	function log(str, tabs)
	{
		return;
		
		if(tabs)
			for(var i = 0; i < tabs; i++)
				str = "\t" + str;
		
		console.log(str);
	}
	
	WPGMZA.MarkerSeparator = function(map_id)
	{
		this.map = WPGMZA.getMapByID(map_id);
		this.map_id = map_id;
		this.groups = [];
		
		// Default threshold of 50 meters ()
		this.threshold		= 50 / 1000;
		
		// Load settings
		if(WPGMZA.settings.wpgmza_near_vicinity_aff_radius)
			this.threshold = (WPGMZA.settings.wpgmza_near_vicinity_aff_radius) / 1000;
		
		log("Threshold is " + this.threshold);
		
		// Group markers
		this.groupMarkers();
	}
	
	WPGMZA.MarkerSeparator.prototype.destroy = function()
	{
		var markers = this.getMarkers();
		
		markers.forEach(function(marker) {
			delete marker.separatorGroup;
		});
	}
	
	WPGMZA.MarkerSeparator.getNativeLatLng = function(latLng)
	{
		if(WPGMZA.isProVersionBelow7_10_00)
			return WPGMZA.LatLng.fromGoogleLatLng(latLng);
		
		return new WPGMZA.LatLng(latLng);
	}
	
	WPGMZA.MarkerSeparator.prototype.getMarkers = function()
	{
		var markers = [];
		for(var i in this.map.markers){
			if(this.map.markers[i].getVisible()){
				markers.push(this.map.markers[i]);
			}
		}
		return this.map.markers;
	}
	
	WPGMZA.MarkerSeparator.prototype.groupMarkers = function()
	{
		var start = new Date().getTime();
		
		var self = this;
		var points = [];
		var markers = this.getMarkers();

		if(!markers.length)
			return;
		
		var distanceFunction;
		var position = markers[0].getPosition();
		var minLat = position.lat,
			maxLat = position.lat,
			avgLat = 0, latRange;
		
		markers.forEach(function(marker) {
			if(marker.isFiltered){
				return;
			}

			var latLng = WPGMZA.MarkerSeparator.getNativeLatLng(marker.getPosition());
			
			minLat = Math.min(latLng.lat, minLat);
			maxLat = Math.max(latLng.lat, maxLat);
			avgLat += latLng.lat;
			
			latLng.marker = marker;
			
			points.push(latLng);
		});
		
		avgLat /= markers.length;
		latRange = Math.abs(maxLat - minLat);
		
		if(latRange < 5 || WPGMZA.settings.forceCheapRuler)
		{
			var cheapRuler = new CheapRuler(avgLat, "kilometers");
			
			distanceFunction = function(a, b) {
				return cheapRuler.distance([a.lat, a.lng], [b.lat, b.lng]);
			};
		}
		else
			distanceFunction = WPGMZA.Distance.between;
		
		var tree = new kdTree(points, distanceFunction, ["lat", "lng"]);
		
		for(var i = 0; i < markers.length; i++)
		{
			if(markers[i].separatorGroup)
				continue;
			
			if(!markers[i].getVisible() || markers[i].isFiltered){
				/* Marker not visible at the moment, don't create any group */
				continue;
			}

			// TODO: Add max group size setting, add warning when groups are full
			var marker = markers[i];
			
			if(marker.separatorGroup)
				continue;
			
			var maxGroupSize = (WPGMZA.settings.marker_separator_maximum_group_size ? WPGMZA.settings.marker_separator_maximum_group_size : 8)
			var nearest = tree.nearest(marker.getPosition(), maxGroupSize, [this.threshold]);
			var group = null;
			
			for(var j = 0; j < nearest.length; j++){
				var other = nearest[j][0].marker;
				
				if(other === marker)
					continue;
				
				if(other.separatorGroup)
					continue;
				
				if(!group)
				{
					group = new WPGMZA.MarkerSeparatorGroup();
					group.addMarker(marker);
				}
				
				group.addMarker(other);
			}
			
			if(!group)
				continue;
			
			if(WPGMZA.isProVersionBelow7_10_00){
				group.placeholder.googleMarker.setMap(this.map);
			}else{
				this.map.addMarker(group.placeholder);
			}

			this.groups.push(group);
		}
		
		/*var end = new Date().getTime();
		var elapsed = end - start;
		console.log(elapsed + " ms elapsed");*/
	}
	
	$(document.body).on("markersplaced.wpgmza filteringcomplete.wpgmza", function(event) {
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_MAP_EDIT)
			return;
		
		if(!WPGMZA.settings.wpgmza_near_vicinity_control_enabled)
			return;
		
		var map_id = event.target.id.match(/\d+$/);
		var map = WPGMZA.getMapByID(map_id);
		
		if(!map)
			return;
		
		if(map.markerSeparator){
			
			if(map.markerSeparator.groups && map.markerSeparator.groups.length > 0){
				for(var gI in map.markerSeparator.groups){
					var groupMarker = map.markerSeparator.groups[gI].placeholder;
					map.removeMarker(groupMarker);
				}
			}

			map.markerSeparator.destroy();
		}
		
		map.markerSeparator = new WPGMZA.MarkerSeparator(map_id);
		
	});

	$(document.body).on("infowindowopen.wpgmza", function(event){
		if(event.target){
			if(event.target.feature && event.target.feature.separatorGroup){
				/* This marker is part of a group */
				if(event.target.feature.separatorGroup.state === WPGMZA.MarkerSeparatorGroup.STATE_CLOSED){
					/* It's closed, should be opened */
					event.target.feature.separatorGroup.open();
				}
			}
		}
	});
	
});

// js/v8/google-maps/google-marker-cluster-icon.js
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

// js/v8/open-layers/ol-marker-cluster-icon.js
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

// js/v8/rating-widgets/rating-widget.js
/**
 * @namespace WPGMZA
 * @module RatingWidget
 * @requires-external WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.RatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.call(this);
		
		this._averageRating = 0;
		this._numRatings = 0;
		
		this.userGuid = Cookies.get("wpgmza-user-guid");
		if(!this.userGuid)
		{
			this.userGuid = WPGMZA.guid();
			Cookies.set("wpgmza-user-guid", this.userGuid);
		}
		
		this.element = $("<span class='wpgmza-rating'></span>");
		
		this.on("rated", function(event) {
			self.onRated(event);
		});
	}
	
	WPGMZA.RatingWidget.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.RatingWidget.prototype.constructor = WPGMZA.RatingWidget;
	
	WPGMZA.RatingWidget.STYLE_RADIOS		= "radios";
	WPGMZA.RatingWidget.STYLE_GRADIENT		= "gradient";
	WPGMZA.RatingWidget.STYLE_STARS			= "stars";
	WPGMZA.RatingWidget.STYLE_THUMBS		= "thumbs";
	
	WPGMZA.RatingWidget.createInstance = function(options, forceStyle)
	{
		var style = WPGMZA.RatingWidget.getSelectedStyle();
		
		if(forceStyle)
			style = forceStyle;
		
		switch(style)
		{
			case WPGMZA.RatingWidget.STYLE_GRADIENT:
				return new WPGMZA.GradientRatingWidget(options);
				break;
			
			case WPGMZA.RatingWidget.STYLE_STARS:
				return new WPGMZA.StarsRatingWidget(options);
				break;
			
			case WPGMZA.RatingWidget.STYLE_THUMBS:
				return new WPGMZA.ThumbsRatingWidget(options);
				break;
			
			default:
				return new WPGMZA.RadiosRatingWidget(options);
				break;
		}
	}

	WPGMZA.RatingWidget.getSelectedStyle = function()
	{
		return WPGMZA.settings.marker_rating_widget_style;
	}
	
	WPGMZA.RatingWidget.getRatingWidgetForMarker = function(marker)
	{
		var options = {
			type: "marker",
			id: marker.id
		};
		
		if(marker.rating)
		{
			options.averageRating = marker.rating.average;
			options.numRatings = marker.rating.count;
		}
		
		var widget = WPGMZA.RatingWidget.createInstance(options);
		
		return widget;
	}
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "min", {
		
		get: function() {
			
			return parseInt(WPGMZA.settings.minimum_rating);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "max", {
		
		get: function() {
			
			return parseInt(WPGMZA.settings.maximum_rating);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "step", {
		
		get: function() {
			
			return parseFloat(WPGMZA.settings.rating_step);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "numRatings", {
		
		get: function() {
			
			return $(this.element).find(".wpgmza-num-ratings").text();
			
		},
		
		set: function(value) {
			
			$(this.element).find(".wpgmza-num-ratings").text(value);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.RatingWidget.prototype, "localStorageKey", {
		
		get: function() {
			
			return "wpgmza_rating_" + this.type + "_" + this.id;
			
		}
		
	});
	
	WPGMZA.RatingWidget.prototype.setOptions = function(options)
	{
		for(var key in options)
			this[key] = options[key];
		
		this.recallSubmittedRating();
	}
	
	WPGMZA.RatingWidget.prototype.getAJAXRequestParameters = function()
	{
		var params = {};
		
		if(this.type)
			params.type = this.type;
		if(this.id)
			params.id = this.id;
		
		params.userGuid = this.userGuid;
		params.amount = this.value;
		
		return params;
	}
	
	WPGMZA.RatingWidget.prototype.onRated = function(event)
	{
		var self = this;
		
		var params = {
			method: "POST",
			data: this.getAJAXRequestParameters(),
			success: function(data, status, xhr) {
				
				self.averageRating = data.average;
				self.numRatings = data.count;
				
				self.storeSubmittedRating(self.value);
				
			},
			complete: function() {
				self.showPreloader(false);
			}
		};
		
		this.showPreloader(true);
		WPGMZA.restAPI.call("/ratings/", params);
	}
	
	WPGMZA.RatingWidget.prototype.showPreloader = function(show)
	{
		this.isLoading = show;
		
		if(show)
			$(this.element).addClass("wpgmza-loading");
		else
			$(this.element).removeClass("wpgmza-loading");
	}
	
	WPGMZA.RatingWidget.prototype.recallSubmittedRating = function()
	{
		if(!window.localStorage)
			return;
		
		var item = localStorage.getItem(this.localStorageKey);
		
		if(!item)
			return;
		
		item = JSON.parse(item);
		
		this.value = item.amount;
	}
	
	WPGMZA.RatingWidget.prototype.storeSubmittedRating = function(amount)
	{
		if(!window.localStorage)
			return;
		
		localStorage.setItem(this.localStorageKey, JSON.stringify({
			amount: amount
		}));
	}
	
	$(window).on("infowindowopen.wpgmza", function(event) {
		var marker = event.target.feature;
		var map = marker.map;
		
		if(!map.settings.enable_marker_ratings)
			return;	// Ratings not enabled
		
		if(marker.isIntegrated)
			return;	// Can't leave ratings for integrated markers
		
		var widget = WPGMZA.RatingWidget.getRatingWidgetForMarker(marker);
		
		if(!$(event.target.element).children().hasClass('wpgmza-rating'))
			$(event.target.element).children().last().after(widget.element);
		
	});
	
	$(document.body).on("markerlistingupdated.wpgmza", function(event) {
		
		var map = event.target.map;
		
		$(event.target.element).find(".wpgmza-rating-container").each(function(index, el) {
		
			var marker_id = $(el).closest("[data-marker-id]").attr("data-marker-id");
			var marker = map.getMarkerByID(marker_id);
			var widget = WPGMZA.RatingWidget.getRatingWidgetForMarker(marker);
		
			if(map.settings.enable_marker_ratings)
				$(el).append(widget.element);
			else
				$(el).remove();
			
		});
		
	});
	
});

// js/v8/rating-widgets/gradient-rating-widget.js
/**
 * @namespace WPGMZA
 * @module GradientRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	// TODO: Consider renaming to BarRatingWidget
	
	WPGMZA.GradientRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.input = $("<input type='hidden'/>");
		this.element.append(this.input);
		
		this.container = $("<div class='wpgmza-rating-gradient-container'></div>");
		this.container.css({
			overflow: "hidden"
		});
		
		this.element.append(this.container);
		
		this.gradient = $("<div class='wpgmza-rating-gradient'></div>");
		// this.gradient.css({"width": "75%"});

		//start color for the gradient bar
		var start_color = WPGMZA.settings.marker_rating_gradient_widget_start_color;
		//end color for the gradient bar
		var end_color = WPGMZA.settings.marker_rating_gradient_widget_end_color ;

			//colors added to the gradient bar
			this.gradient.css({

			"background": start_color, /* Old browsers */
			"background": "-moz-linear-gradient(left, " +  start_color +  " 0%, " +  end_color +  " 128px)", /* FF3.6-15 */
			"background": "-webkit-linear-gradient(left, " + start_color + " 0%, " + end_color + " 128px)" ,/* Chrome10-25,Safari5.1-6 */
			"background": "linear-gradient(to right, " + start_color + " 0%, " + end_color +  " 128px)", /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			"filter": "progid:DXImageTransform.Microsoft.gradient( startColorstr= " + start_color + ", endColorst'= "  + end_color + ", GradientType = 1 )", /* IE6-9 */
		});

		this.container.append(this.gradient);
		
		this.element.append(" ");
		this.element.append($("<i class='fa fa-users' aria-hidden='true'></i>"));
		this.element.append(" ");
		this.element.append($("<span class='wpgmza-num-ratings'></span>"));
		
		this.container.on("mousemove", function(event) {
			self.onMouseMove(event);
		});
		
		this.container.on("mouseout", function(event) {
			self.onMouseOut(event);
		});
		
		this.container.on("click", function(event) {
			
			if(self.isLoading)
				return;
			
			self.trigger("rated");
			
		});
		
		this.setOptions(options);
	}
	
	WPGMZA.extend(WPGMZA.GradientRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.GradientRatingWidget.prototype, "averageRating", {
		
		get: function() {
			return this._averageRating;
		},
		
		set: function(value) {
			
			this.showValue(value);
			this._averageRating = value;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.GradientRatingWidget.prototype, "value", {
		
		get: function() {
			return this.lastHoveredRating;
		},
		
		set: function() {
			// TODO: Remember the value to display on hover?
		}
		
	});
	
	WPGMZA.GradientRatingWidget.prototype.showValue = function(value)
	{
		var f = (value - this.min) / (this.max - this.min);
		var percent = f * 100;
		
		this.gradient.css({"width": percent + "%"});
	}
	
	WPGMZA.GradientRatingWidget.prototype.onMouseMove = function(event)
	{
		if(this.isLoading)
			return;
		
		var x = event.pageX - $(this.container).offset().left;
		var y = event.pageY - $(this.container).offset().top;
		var w = $(this.container).width();
		
		var f		= (x / w);
		var v		= f * this.max;
		
		var rating	= this.min + (Math.round(v / this.step) * this.step);
		var percent	= ((rating - this.min) / (this.max - this.min)) * 100;
		
		this.lastHoveredRating = rating;
		
		this.gradient.css({"width": percent + "%"});
	}
	
	WPGMZA.GradientRatingWidget.prototype.onMouseOut = function(event)
	{
		this.showValue(this.averageRating);
	}
	
});

// js/v8/rating-widgets/radios-rating-widget.js
/**
 * @namespace WPGMZA
 * @module RadiosRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	WPGMZA.RadiosRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.element.append(this.min + " ");
		this.name = WPGMZA.guid();
		
		for(var amount = this.min; amount <= this.max; amount += this.step)
		{
			var radio = $("<input type='radio'/>");
			
			radio.attr("name", this.name);
			radio.val(amount);
			
			this.element.append(radio);
		}
		
		this.element.append(this.max);
		
		this.element.append(" (");
		this.element.append($("<i class='fa fa-dot-circle-o' aria-hidden='true'></i> <span class='wpgmza-average-rating'></span> - <i class='fa fa-users' aria-hidden='true'></i> <span class='wpgmza-num-ratings'></span>"));
		this.element.append(")");
		
		this.setOptions(options);
		
		$(this.element).on("change", "input", function(event) {
			self.trigger("rated");
		});
	}
	
	WPGMZA.extend(WPGMZA.RadiosRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.RadiosRatingWidget.prototype, "value", {
		
		get: function()
		{
			return this.element.find("input:checked").val();
		},
		
		set: function(value)
		{
			this.element.find("input:checked").prop("checked", false);
			this.element.find("input[value='" + value + "']").prop("checked", true);
		}
		
	});
	
	Object.defineProperty(WPGMZA.RadiosRatingWidget.prototype, "averageRating", {
		
		get: function()
		{
			return this._averageRating;
		},
		
		set: function(value)
		{
			if(isNaN(value) || !value)
				value = 0;
			
			var display = parseFloat(value).toFixed(2);
			
			$(this.element).find(".wpgmza-average-rating").text(display);
			
			this._averageRating = value;
		}
		
	});
	
	WPGMZA.RadiosRatingWidget.prototype.showPreloader = function(show)
	{
		WPGMZA.RatingWidget.prototype.showPreloader.apply(this, arguments);
		
		$(this.element).find("input").prop("disabled", show);
	}
	
});

// js/v8/rating-widgets/stars-rating-widget.js
/**
 * @namespace WPGMZA
 * @module StarsRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	WPGMZA.StarsRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.input = $("<input type='hidden'/>");
		this.element.append(this.input);
		
		this.container = $("<span class='wpgmza-rating-stars-container'></span>");
		this.element.append(this.container);
		
		this.background = $("<span class='wpgmza-background'></span>");
		this.container.append(this.background);
		
		this.foreground = $("<span class='wpgmza-foreground'></span>");
		this.container.append(this.foreground);
		
		for(var amount = this.min; amount <= this.max; amount++)
		{
			this.background.append("&#x2606;");
			this.foreground.append("&#x2605;");
		}
		
		this.element.append(" ");
		// this.element.append($("<i class='fa fa-users' aria-hidden='true'></i>"));
		this.element.append($("<span class='wpgmza-num-ratings'></span>"));
		
		this.visibilityTestInterval = setInterval(function() {
			
			var width = $(self.background).width();
			var height = $(self.background).height();
			
			if(width == 0)
				return;
			
			var css = {
				"width": width + "px",
				"height": height + "px"
			};
			
			self.container.css(css);
			
			self.element.find(".wpgmza-num-ratings").css({
				"left": css.width
			});
			
			clearInterval(self.visibilityTestInterval);
			self.showStars(self.averageRating);
			
		}, 100);
		
		this.container.on("mousemove", function(event) {
			self.onMouseMove(event);
		});
		
		this.container.on("mouseout", function(event) {
			self.onMouseOut(event);
		});
		
		this.container.on("click", function(event) {
			
			if(self.isLoading)
				return;
			
			self.trigger("rated");
			
		});
		
		this.setOptions(options);
	}
	
	WPGMZA.extend(WPGMZA.StarsRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.StarsRatingWidget.prototype, "value", {
		
		get: function() {
			return this.lastHoveredRating;
		},
		
		set: function() {
			// TODO: Remember the value to display on hover?
		}
		
	});
	
	Object.defineProperty(WPGMZA.StarsRatingWidget.prototype, "averageRating", {
		
		get: function() {
			return this._averageRating;
		},
		
		set: function(value) {
			
			this._averageRating = value;
			this.showStars(value);
			
		}
		
	});
	
	WPGMZA.StarsRatingWidget.prototype.showStars = function(amount)
	{
		/*
		var f = (amount - this.min) / (this.max - this.min);
		var w = $(this.background).width();
		*/
		//var s = w / this.step;
		
		/*var i = /*Math.ceil*/ /*(f * this.max);*/
		/*
		var px = (i / this.max) * $(this.container).width();
		
		//var percent = (i / this.max) * 100;
		//this.foreground.css({"width": percent + "%"});
		
		this.foreground.css({"width": px + "px"});
		*/

		var f = amount / this.max;
      	var w = $(this.background).width();
      	var percent = f * w;

      	this.foreground.css({"width": percent + "px"});
	}
	
	WPGMZA.StarsRatingWidget.prototype.onMouseMove = function(event)
	{
		this.container.css({
			"width": $(this.background).width(),
			"height": $(this.background).height()
		});
		
		var x = event.pageX - $(this.container).offset().left;
		var w = $(this.background).width();
		var f = (x / w);
		
		var w = $(this.background).width();
		
		var s = w / this.step;
		var i = Math.ceil(f * this.max);
		var px = (i / this.max) * $(this.container).width();
		
		this.lastHoveredRating = i;
		
		this.foreground.css({"width": px + "px"});
		
	}
	
	WPGMZA.StarsRatingWidget.prototype.onMouseOut = function(event)
	{
		this.showStars(this.averageRating);
	}
	
});

// js/v8/rating-widgets/thumbs-rating-widget.js
/**
 * @namespace WPGMZA
 * @module ThumbsRatingWidget
 * @requires WPGMZA.RatingWidget
 */
jQuery(function($) {
	
	WPGMZA.ThumbsRatingWidget = function(options)
	{
		var self = this;
		
		WPGMZA.RatingWidget.call(this);
		
		this.input = $("<input type='hidden'/>");
		this.element.append(this.input);
		
		this.container = $("<span class='wpgmza-rating-thumbs-container'></span>");
		this.element.append(this.container);
		
		this.downvote = $("<span class='wpgmza-downvote'><i class='fa fa-thumbs-down' aria-hidden='true'></i></span>");
		this.container.append(this.downvote);
		
		this.upvote = $("<span class='wpgmza-upvote'><i class='fa fa-thumbs-up' aria-hidden='true'></i></span>");
		this.container.append(this.upvote);
		
		this.setOptions(options);
		
		this.downvote.on("click", function(event) {
			self.onButtonClicked(event);
		});
		
		this.upvote.on("click", function(event) {
			self.onButtonClicked(event);
		});
	}
	
	WPGMZA.extend(WPGMZA.ThumbsRatingWidget, WPGMZA.RatingWidget);
	
	Object.defineProperty(WPGMZA.ThumbsRatingWidget.prototype, "value", {
		
		get: function() {
			
			return this.lastClickedRating;
			
		},
		
		set: function(value) {
			
			if(value == this.min)
			{
				this.downvote.addClass("wpgmza-remembered-rating");
			}
			else if(value == this.max)
			{
				this.upvote.addClass("wpgmza-remembered-rating");
			}
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ThumbsRatingWidget.prototype, "averageRating", {
		
		get: function() {
			return this._averageRating;
		},
		
		set: function(value) {
			this.setBackgroundValue(value);
			this._averageRating = value;
		}
		
	});
	
	WPGMZA.ThumbsRatingWidget.prototype.setBackgroundValue = function(value)
	{
		var f = (value - this.min) / (this.max - this.min);
		var percent = f * 100;
		var prefixes = [
			"-moz-",
			"-webkit-",
			""
		];
		var color = this.getAverageDisplayColor();
		
		for(var i = 0; i < prefixes.length; i++)
		{
			
			var propertyValue = prefixes[i] + "linear-gradient(to right, " + color + " 0%, " + color + " " + percent + "%, transparent " + percent + "%, transparent 100%)";
			
			this.container.css({
				"background": propertyValue
			});
			
		}
	}
	
	WPGMZA.ThumbsRatingWidget.prototype.getAverageDisplayColor = function()
	{
		//If you have selected a color for the gradient bar then return marker_rating_thumb_widget_average_rating_color
		if(WPGMZA.settings.marker_rating_thumb_widget_average_rating_color)
		{
			return WPGMZA.settings.marker_rating_thumb_widget_average_rating_color;
		}

		//else use fixed color
		else
		{
			return "#3cc639";
		}
	}
	
	WPGMZA.ThumbsRatingWidget.prototype.onButtonClicked = function(event)
	{
		if(this.isLoading)
			return;
		
		if($(event.currentTarget).hasClass("wpgmza-upvote"))
			this.lastClickedRating = this.max;
		else
			this.lastClickedRating = this.min;
		
		$(this.element).find(".wpgmza-remembered-rating").removeClass(".wpgmza-remembered-rating");
		$(event.currentTarget).addClass("wpgmza-remembered-rating");
		
		this.trigger("rated");
	}
	
});
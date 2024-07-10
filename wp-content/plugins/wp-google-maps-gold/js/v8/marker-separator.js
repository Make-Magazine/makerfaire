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
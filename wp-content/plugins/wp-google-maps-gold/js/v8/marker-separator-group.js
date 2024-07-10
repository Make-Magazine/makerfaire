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
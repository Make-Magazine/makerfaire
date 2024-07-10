/*
 * NB: Loading this script is handled by legacy-core.php
 */
jQuery(function($) {
	
	WPGMZA.isProVersionBelow7_10_00 = true;
	
	google.maps.Marker.prototype.setOffset = function(x, y)
	{
		this._offset = {x: x, y: y};
		this.updateOffset();
	}
	
	google.maps.Marker.prototype.updateOffset = function()
	{
		var temp = {_offset: this._offset, googleMarker: this};
		WPGMZA.GoogleMarker.prototype.updateOffset.apply(temp);
	}
	
	Object.defineProperty(google.maps.Marker.prototype, "offsetX", {
		get: function() {
			if(!this._offset)
				return 0;
			return this._offset.x;
		},
		set: function(value) {
			if(!this._offset)
				this._offset = {x: 0, y: 0};
			this._offset.x = value;
			this.updateOffset();
		}
	});
	
	Object.defineProperty(google.maps.Marker.prototype, "offsetY", {
		get: function() {
			if(!this._offset)
				return 0;
			return this._offset.y;
		},
		set: function(value) {
			if(!this._offset)
				this._offset = {x: 0, y: 0};
			this._offset.y = value;
			this.updateOffset();
		}
	});
	
});
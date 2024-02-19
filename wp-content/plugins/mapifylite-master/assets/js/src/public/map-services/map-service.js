class MapService {
	construct( $canvas, settings ) {}
	setType( type ) {}
	getCenter() {}
	setCenter( center ) {}
	setZoom( zoom ) {}
	getZoom() {}
	addPins( pins, clustered ) {}
	geocode( query ) {}
	getPinsWithinRange( lat, lng, rangeInMeters ) {}
	getPinClosestTo( lat, lng ) {}
	getPinDistance( lat, lng, pin ) {}
	highlightPin( pin ) {}
	fitPins( pins ) {}
	updatePinVisibility( pin ) {}
	locate() {}
	redraw() {}
};

module.exports = MapService;
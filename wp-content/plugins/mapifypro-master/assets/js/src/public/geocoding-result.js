class GeocodingResult {
	constructor( address, lat, lng ) {
		this.address = address;
		this.lat = lat;
		this.lng = lng;
	}
};

module.exports = GeocodingResult;
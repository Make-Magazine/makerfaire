import Promise from 'bluebird';
import Waypoint from './waypoint.js';
import _ from 'lodash';

export default class Route {
	constructor( map, settings ) {
		this.waypoints = [];
		this.settings = settings;
		this.line = null;
		this.router = L.Routing.mapbox( 'pk.eyJ1IjoianNlYXJzMzEiLCJhIjoiY2o3bG5obHZqMmdvcDJxcW15bzFpdTB5NSJ9.lfom0YaF2Siy0-1T0y-EJw' );
		this.map = map;
		this.addWaypoints();
	}

	removeWaypoints() {
		this.waypoints.forEach(waypoint => waypoint.marker.remove());
		this.waypoints = [];
	}

	addWaypoints() {
		for ( let type in this.settings.pins ) {
			let pin = this.settings.pins[ type ];
			let waypoint = new Waypoint( this.map, pin );
			this.waypoints.push( waypoint );
		}
	}

	createLayer() {
		if ( this.settings.type === 'offroad' ) {
			const latlngs = _.map( this.waypoints, w => new L.latLng( w.latlng ) );
			const line = L.polyline( latlngs, {
				color: this.settings.route_color,
			} );
			return Promise.resolve( line );
		}

		const routeWaypoints = _.map( this.waypoints, w => new L.Routing.Waypoint( w.latlng ) );

		return new Promise((resolve, reject) => {
			const settings = window.prettyroutes_script_settings;

			jQuery.ajax({
				url: settings.ajax_url,
				method: 'POST',
				data: {
					nonce: settings.nonce,
					action: 'routes_get_route',
					waypoints: this.waypoints.map(w => w.latlng),
				}
			}).done(res => {
				const data = _.get(res, 'data', null);
				if (data) {
					return resolve(data);
				}

				reject('Error while fetching routes.');
			}).fail((jqXhr, textStatus, error) => {
				reject(error);
			});
		})
			.then(data => {
				if ( typeof data === 'undefined' ) {
					return null;
				}

				const options = L.extend({}, this.router.options.routingOptions);
				return Promise.promisify(this.router._routeDone, { context: this.router })(data, routeWaypoints, options);
			} )
			.then( routes => {
				if ( typeof routes === 'undefined' ) {
					return null;
				}

				const line = new L.Routing.line( routes[0], {
					styles: [{
						color: this.settings.route_color,
					}],
				} );

				this.line = line;

				return line;
			} )
			.catch(error => {
				console.error(error);
			});
	}
}

import Route from './route.js';

export default class Map {
	constructor( $container, settings ) {
		this.routes = [];
		this.bounds = L.latLngBounds( [] );
		this.$container = $container;
		this.settings = settings;

		let opts = {
			'attributionControl': false,
		}
		this.mapService = L.map( this.$container.find( '.prettyroutes-map-canvas:first' ).get( 0 ), opts );
		this.mapService.addLayer( L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
		} ) );
		this.mapService.setView( this.settings.center, parseInt( this.settings.zoom ), {
			animate: false,
		} );

		for ( let i = 0; i < this.settings.routes.length; i++ ) {
			let routeSettings = this.settings.routes[ i ];
			let route = new Route( this, routeSettings );
			this.addRoute( route );
		}

		this.refresh();
	}

	addRoute( route ) {
		if ( ! route.settings.valid ) {
			return;
		}

		this.routes.push( route );
		for ( let i = 0; i < route.waypoints.length; i++ ) {
			let waypoint = route.waypoints[i];
			this.bounds.extend( [parseFloat(waypoint.latlng[0]), parseFloat(waypoint.latlng[1])] );
		}

		route.createLayer().then( routeLayer => this.mapService.addLayer( routeLayer ) );
	}

	fitBounds() {
		this.mapService.fitBounds( this.bounds );
	}

	refresh() {
		if ( this.settings.centerMode === 'auto' ) {
			this.fitBounds();
		}
	}
}

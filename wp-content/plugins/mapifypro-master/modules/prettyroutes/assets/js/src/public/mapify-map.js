import Route from './route.js';

export default class MapifyMap {
	constructor( mapifyMap ) {
		this.$container = mapifyMap.$container;
		this.mapService = mapifyMap.mapService.map;
		this.routes = [];
		this.bounds = L.latLngBounds( [] );

		for ( let i = 0; i < mapifyMap.settings.routes.length; i++ ) {
			let routeSettings = mapifyMap.settings.routes[ i ];
			let route = new Route( this, routeSettings );
			this.addRoute( route );
		}

		this.addEvents();
		this.refresh();
	}

	addEvents() {
		const that = this;
		this.$container.on('mapify.filter.ended', function(event, data) {
			const tagId = data.tagId;
			let tagIds = [ tagId ];

			if ( typeof data.tagIds !== 'undefined' ) {
				tagIds = data.tagIds;
			}

			that.routes.forEach(route => {
				const hasLayer = that.mapService.hasLayer(route.line);
				const filteredTags = tagIds.filter( function( value ) {
					return route.settings.location_tags.includes( value );
				} );

				const hasTag = filteredTags.length;
				
				if (!tagId && !hasLayer) {
					route.addWaypoints();
					return that.mapService.addLayer(route.line);
				}

				if (hasTag && !hasLayer) {
					route.addWaypoints();
					return that.mapService.addLayer(route.line);
				}

				if (tagId && !hasTag && hasLayer) {
					route.removeWaypoints();
					return that.mapService.removeLayer(route.line);
				}
			});
		});
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
		// do nothing
	}

	refresh() {
		// do nothing
	}
}

import Tooltip from './tooltip.js';

export default class Waypoint {
	constructor( map, settings ) {
		this.map = map;
		this.settings = settings;
		this.tooltip = null;

		if ( this.settings.tooltip_enabled ) {
			this.tooltip = new Tooltip( {
				'rgba': this.settings.tooltip_background,
				'content': this.settings.tooltip_content,
				'close_behavior': this.settings.tooltip_close
			} );
			this.tooltip.node().addClass( 'route-tooltip' );
			this.tooltip.setLeafletMap( map.mapService );
		}

		this.latlng = this.settings.latlng;

		let opts = {
			draggable: false,
		}
		if ( this.settings.pin_image ) {
			opts.icon = L.icon( {
				'iconUrl': this.settings.pin_image,
				'iconAnchor': this.settings.pin_anchor,
			} );
		}
		if (this.settings.pin_enabled) {
			this.marker = L.marker( this.latlng, opts );
			this.map.mapService.addLayer( this.marker );

			if ( this.tooltip ) {
				this.marker.on( 'mouseover', () => {
					var anchor = [0, -10];
					if ( opts.icon ) {
						anchor[1] -= this.settings.pin_anchor[1];
					} else {
						anchor[1] -= 50;
					}

					var containerPoint = this.map.mapService.latLngToContainerPoint( this.latlng );
					var left = this.map.$container.offset().left + containerPoint.x - Math.ceil( this.tooltip.node().width() / 2 ) + anchor[0];
					var top = this.map.$container.offset().top + containerPoint.y - this.tooltip.node().height() + anchor[1];
					this.tooltip.node().trigger( {
						'type': 'tooltip_mouseover',
						'settings': {
							'left': left,
							'top': top,
						}
					} );
				} );
				this.marker.on( 'mouseout', () => {
					this.tooltip.node().trigger( {
						'type': 'tooltip_mouseout'
					} );
				} );
			}
		}
	}
}

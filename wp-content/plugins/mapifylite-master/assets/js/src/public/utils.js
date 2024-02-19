const $ = jQuery;

module.exports = {
	meters2unit( meters, unit ) {
		var divisor = unit == 'km' ? 1 : 1.609344;
		var km = meters / 1000;
		return km / divisor;
	},

	unit2meters( value, unit ) {
		var multiplier = unit == 'km' ? 1000 : 1609.344;
		return value * multiplier;
	},

	isPhone() {
		var dimension = Math.min( $(window).width(), $(window).height() );
		return dimension <= 414; // iPhone XR screen width for the maxmum browser width & height  
	},

	isTouchDevice() {
		return ('ontouchstart' in window || navigator.msMaxTouchPoints);
	},

	showSearchPopup( map, content ) {
		if ( typeof map.searchTooltip == 'undefined' ) {
			map.searchTooltip = new MapifyTooltip( {
				'rgba': map.settings.tooltip.background,
				'content': content,
				'close_behavior': 'manual',
				'animate': true
			} );
			map.searchTooltip.node().addClass( 'mpfy-tooltip mpfy-flip-tooltip' );

			map.searchTooltip.node().on( 'click', '.mpfy-closest-pin', e => {
				e.preventDefault();
				if ( map.searchLastClosestPin === null ) {
					return;
				}

				map.clearSearch( false )
					.then( function() {
						map.mapService.highlightPin( map.searchLastClosestPin );
					} );
			} );
		} else {
			map.searchTooltip.setContent( content );
		}

		var form = map.$container.find( '.mpfy-search-form:first' );
		var l = form.offset().left + form.width() - map.searchTooltip.node().width() / 2;
		var t = form.offset().top + 11 + form.height() + map.searchTooltip.node().height();
		map.searchTooltip.showCentered( l, t );
	},

	preloadImage( src ) {
		return new Promise( function( resolve, reject ) {
			var image = new Image();
			image.onload = () => resolve( image );
			image.onLoad = image.onload;
			image.src = src;
		} );
	},
};
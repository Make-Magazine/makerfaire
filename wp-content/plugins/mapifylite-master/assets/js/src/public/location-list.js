const $ = jQuery;

class LocationList {
	constructor( $container, map ) {
		this.$container = $container;
		this.map = map;
		this.settings = {
			hideInDefaultView: JSON.parse( this.$container.attr( 'data-hide-in-default-view' ) ),
			perPage: 3,
			currentPage: 0,
			pages: 1,
			hiddenItemClass: 'mpfy-mll-filter-hidden',
		};
		this.$pagination = this.$container.find( '.mpfy-mll-pagination:first' );
		this.$items = this.$container.find( '.mpfy-mll-location' );
		this.$list = this.$items.first().parent();

		var perPage = parseInt( this.$pagination.attr( 'data-number' ) );
		perPage = ( isNaN( perPage ) ) ? 3 : Math.abs( perPage );
		this.settings.perPage = perPage;

		if ( ! this.settings.hideInDefaultView ) {
			this.toggleView( true );
		}

		this.addEvents();
		this.mirrorList();
	}

	toggleView( show ) {
		this.$container.toggle( show );
	}

	mirrorList( sortByDistanceTo ) {
		sortByDistanceTo = typeof sortByDistanceTo === 'undefined' ? null : sortByDistanceTo;
		var _this = this;

		for (var i = 0; i < _this.map.pins.length; i++) {
			var pin = _this.map.pins[i];
			var visible = pin.isVisible();

			this.$items.filter( '.mpfy-mll-location[data-id="' + pin.model.id + '"]' ).first().toggleClass( this.settings.hiddenItemClass, ! visible );
		}

		this.$items.sort( function( a, b ) {
			if ( sortByDistanceTo ) {
				var pinA = _this.map.getPinById( $( a ).attr('data-id') );
				var distanceA = _this.map.mapService.getPinDistance( sortByDistanceTo.lat, sortByDistanceTo.lng, pinA );

				var pinB = _this.map.getPinById( $( b ).attr('data-id') );
				var distanceB = _this.map.mapService.getPinDistance( sortByDistanceTo.lat, sortByDistanceTo.lng, pinB );

				if ( isNaN( distanceA ) && isNaN( distanceB ) ) {
					return 0;
				}
				if ( isNaN( distanceA ) ) {
					return 1;
				}
				if ( isNaN( distanceB ) ) {
					return -1;
				}
				return distanceA - distanceB;
			}
			return parseInt( $( a ).attr( 'data-order' ) ) - parseInt( $( b ).attr( 'data-order' ) );
		} ).appendTo( this.$list );

		this.calculatePagination();
	}

	calculatePagination() {
		this.settings.pages = Math.ceil( this.$items.not( '.' + this.settings.hiddenItemClass ).length / this.settings.perPage );
		this.settings.currentPage = 0;

		this.$pagination.toggle( this.settings.pages > 1 );
		this.changePage( this.settings.currentPage );
	}

	changePage( currentPage ) {
		var _this = this;
		this.settings.currentPage = Math.max( 0, Math.min( this.settings.pages - 1, currentPage ) );

		var start = this.settings.currentPage * this.settings.perPage;
		var end = start + this.settings.perPage;

		this.$items.addClass( 'mpfy-mll-pagination-hidden' );
		var index = 0;
		this.$items.each( function() {
			if ( ! $( this ).hasClass( _this.settings.hiddenItemClass ) ) {
				if ( index >= start && index <= end - 1 ) {
					$( this ).removeClass( 'mpfy-mll-pagination-hidden' );
				}
				index ++;
				if ( index > end ) {
					return; // break
				}
			}
		} );

		this.$pagination.find( '.mpfy-mll-pagination-current-page' ).text( this.settings.currentPage + 1 );
		this.$pagination.find( '.mpfy-mll-pagination-max-page' ).text( this.settings.pages );
	}

	addEvents() {
		var _this = this;

		this.$container.find( '.mpfy-mll-l-heading' ).click( function( e ) {
			if ( $( e.target ).is( '.mpfy-mll-l-categories a' ) ) {
				return;
			}
			e.preventDefault();

			var $parent = $( this ).closest( '.mpfy-mll-location' );
			$parent.siblings( '.mpfy-mll-location.active' ).find( '.mpfy-mll-l-content' ).not( $( this ) ).each( function() {
				$( this ).slideUp().closest( '.mpfy-mll-location' ).removeClass( 'active' );
			});

			if ( $parent.find( '.mpfy-mll-l-content:first' ).is( ':visible' ) ) {
				$( this ).closest( '.mpfy-mll-location' ).removeClass( 'active' );
				$parent.find( '.mpfy-mll-l-content:first' ).slideUp();
			} else {
				$( this ).closest( '.mpfy-mll-location' ).addClass( 'active' );
				$parent.find( '.mpfy-mll-l-content:first' ).slideDown();

				var pin = _this.map.getPinById( $parent.attr('data-id') );
				_this.map.mapService.highlightPin( pin );
			}
		});

		_this.map.$container.on( 'mapify.search.ended', function( e, args ) {
			if ( _this.settings.hideInDefaultView ) {
				_this.toggleView( args.query.length > 0 );
			}
			_this.mirrorList( _this.map.searchLastResult );
		} );

		_this.map.$container.on( 'mapify.filter.ended', function( e, args ) {
			if ( _this.settings.hideInDefaultView ) {
				_this.toggleView( ( args.tagId != 0 ) );
			}
			_this.mirrorList();
		});

		_this.map.$container.on( 'mapify.useMyLocation.ended', function( e, args ) {
			_this.toggleView( true );
			_this.mirrorList( _this.map.mapService.getCenter() );
		} );

		_this.$container.find( '.mpfy-mll-button-prev' ).click( function( e ) {
			e.preventDefault();
			_this.changePage( _this.settings.currentPage - 1 );
		} );

		_this.$container.find( '.mpfy-mll-button-next' ).click( function( e ) {
			e.preventDefault();
			_this.changePage( _this.settings.currentPage + 1 );
		});
	}
}

module.exports = LocationList;
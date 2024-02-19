'use strict';

import 'isomorphic-fetch';
import 'leaflet-routing-machine';
const $ = jQuery;
const $document = $( document );
const $window = $( window );
const Promise = require( 'bluebird' );

import Map from './map.js';
import MapifyMap from './mapify-map.js';

$document.ready(function(){
	$( '.prettyroutes-map' ).each( function() {
		let $this = $( this );
		let settings = JSON.parse( $this.attr( 'data-json' ) );
		let map = new Map( $this, settings );
	} );
});

$( 'body' ).on( 'mapify.map.created', function( event, map ) {
	let mapifyMap = new MapifyMap( map );
} );

/*
 * Extend the default Inline Edit url type
 *
 * @class Website
 * @extends url
 */
( function ( $ ) {
	"use strict";

	var Website = function ( options ) {
		this.init( 'url', options, Website.defaults );
		this.selectField = null;
	};

	$.fn.editableutils.inherit( Website, $.fn.editabletypes.abstractinput );

	$.extend( Website.prototype, {
		value2html: function ( value, element ) {
			var $el = $( element );
			var oldValue = $el.text();
			var html;
			if ( oldValue.match( /\w+:\/\// ) ) {
				html = $el.html().replace( new RegExp( oldValue, 'g' ), value )
			} else {
				html = $el.html().replace( /href="(.*?)"(.*?)>(.*?)</, 'href="' + value + '"$2>' + value.replace( /(^\w+:|^)\/\//, '' ) + '<' );
			}


			$el.html( html );
		}
	} );

	Website.defaults = $.extend( {}, $.fn.editabletypes.url.defaults );

	$.fn.editabletypes.url = Website;
}( window.jQuery ) );

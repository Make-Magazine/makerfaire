(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){

var state = require('./includes/state');
var plugin = require('./includes/plugin');


(function ( $ ) {

	"use strict";

	$(function () {

		if (!Object.keys) {
		  Object.keys = (function () {
			'use strict';
			var hasOwnProperty = Object.prototype.hasOwnProperty,
				hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
				dontEnums = [
				  'toString',
				  'toLocaleString',
				  'valueOf',
				  'hasOwnProperty',
				  'isPrototypeOf',
				  'propertyIsEnumerable',
				  'constructor'
				],
				dontEnumsLength = dontEnums.length;

			return function (obj) {
			  if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
				throw new TypeError('Object.keys called on non-object');
			  }

			  var result = [], prop, i;

			  for (prop in obj) {
				if (hasOwnProperty.call(obj, prop)) {
				  result.push(prop);
				}
			  }

			  if (hasDontEnumBug) {
				for (i = 0; i < dontEnumsLength; i++) {
				  if (hasOwnProperty.call(obj, dontEnums[i])) {
					result.push(dontEnums[i]);
				  }
				}
			  }
			  return result;
			};
		  }());
		}

		/* Search & Filter jQuery Plugin */
		$.fn.searchAndFilter = plugin;

		/* init */
		$(".searchandfilter").searchAndFilter();

		/* external controls */
		$(document).on("click", ".search-filter-reset", function(e){

			e.preventDefault();

			var searchFormID = typeof($(this).attr("data-search-form-id"))!="undefined" ? $(this).attr("data-search-form-id") : "";
			var submitForm = typeof($(this).attr("data-sf-submit-form"))!="undefined" ? $(this).attr("data-sf-submit-form") : "";

			state.getSearchForm(searchFormID).reset(submitForm);

			//var $linked = $("#search-filter-form-"+searchFormID).searchFilterForm({action: "reset"});

			return false;

		});

	});


/*
 * jQuery Easing v1.4.1 - http://gsgd.co.uk/sandbox/jquery/easing/
 * Open source under the BSD License.
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 * https://raw.github.com/gdsmith/jquery.easing/master/LICENSE
*/

/* globals jQuery, define, module, require */
(function (factory) {
	if (typeof define === "function" && define.amd) {
		define(['jquery'], function ($) {
			return factory($);
		});
	} else if (typeof module === "object" && typeof module.exports === "object") {
		module.exports = factory((typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null));
	} else {
		factory(jQuery);
	}
})(function($){

	// Preserve the original jQuery "swing" easing as "jswing"
	if (typeof $.easing !== 'undefined') {
		$.easing['jswing'] = $.easing['swing'];
	}

	var pow = Math.pow,
		sqrt = Math.sqrt,
		sin = Math.sin,
		cos = Math.cos,
		PI = Math.PI,
		c1 = 1.70158,
		c2 = c1 * 1.525,
		c3 = c1 + 1,
		c4 = ( 2 * PI ) / 3,
		c5 = ( 2 * PI ) / 4.5;

	// x is the fraction of animation progress, in the range 0..1
	function bounceOut(x) {
		var n1 = 7.5625,
			d1 = 2.75;
		if ( x < 1/d1 ) {
			return n1*x*x;
		} else if ( x < 2/d1 ) {
			return n1*(x-=(1.5/d1))*x + .75;
		} else if ( x < 2.5/d1 ) {
			return n1*(x-=(2.25/d1))*x + .9375;
		} else {
			return n1*(x-=(2.625/d1))*x + .984375;
		}
	}

	$.extend( $.easing, {
		def: 'easeOutQuad',
		swing: function (x) {
			return $.easing[$.easing.def](x);
		},
		easeInQuad: function (x) {
			return x * x;
		},
		easeOutQuad: function (x) {
			return 1 - ( 1 - x ) * ( 1 - x );
		},
		easeInOutQuad: function (x) {
			return x < 0.5 ?
				2 * x * x :
				1 - pow( -2 * x + 2, 2 ) / 2;
		},
		easeInCubic: function (x) {
			return x * x * x;
		},
		easeOutCubic: function (x) {
			return 1 - pow( 1 - x, 3 );
		},
		easeInOutCubic: function (x) {
			return x < 0.5 ?
				4 * x * x * x :
				1 - pow( -2 * x + 2, 3 ) / 2;
		},
		easeInQuart: function (x) {
			return x * x * x * x;
		},
		easeOutQuart: function (x) {
			return 1 - pow( 1 - x, 4 );
		},
		easeInOutQuart: function (x) {
			return x < 0.5 ?
				8 * x * x * x * x :
				1 - pow( -2 * x + 2, 4 ) / 2;
		},
		easeInQuint: function (x) {
			return x * x * x * x * x;
		},
		easeOutQuint: function (x) {
			return 1 - pow( 1 - x, 5 );
		},
		easeInOutQuint: function (x) {
			return x < 0.5 ?
				16 * x * x * x * x * x :
				1 - pow( -2 * x + 2, 5 ) / 2;
		},
		easeInSine: function (x) {
			return 1 - cos( x * PI/2 );
		},
		easeOutSine: function (x) {
			return sin( x * PI/2 );
		},
		easeInOutSine: function (x) {
			return -( cos( PI * x ) - 1 ) / 2;
		},
		easeInExpo: function (x) {
			return x === 0 ? 0 : pow( 2, 10 * x - 10 );
		},
		easeOutExpo: function (x) {
			return x === 1 ? 1 : 1 - pow( 2, -10 * x );
		},
		easeInOutExpo: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 : x < 0.5 ?
				pow( 2, 20 * x - 10 ) / 2 :
				( 2 - pow( 2, -20 * x + 10 ) ) / 2;
		},
		easeInCirc: function (x) {
			return 1 - sqrt( 1 - pow( x, 2 ) );
		},
		easeOutCirc: function (x) {
			return sqrt( 1 - pow( x - 1, 2 ) );
		},
		easeInOutCirc: function (x) {
			return x < 0.5 ?
				( 1 - sqrt( 1 - pow( 2 * x, 2 ) ) ) / 2 :
				( sqrt( 1 - pow( -2 * x + 2, 2 ) ) + 1 ) / 2;
		},
		easeInElastic: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 :
				-pow( 2, 10 * x - 10 ) * sin( ( x * 10 - 10.75 ) * c4 );
		},
		easeOutElastic: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 :
				pow( 2, -10 * x ) * sin( ( x * 10 - 0.75 ) * c4 ) + 1;
		},
		easeInOutElastic: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 : x < 0.5 ?
				-( pow( 2, 20 * x - 10 ) * sin( ( 20 * x - 11.125 ) * c5 )) / 2 :
				pow( 2, -20 * x + 10 ) * sin( ( 20 * x - 11.125 ) * c5 ) / 2 + 1;
		},
		easeInBack: function (x) {
			return c3 * x * x * x - c1 * x * x;
		},
		easeOutBack: function (x) {
			return 1 + c3 * pow( x - 1, 3 ) + c1 * pow( x - 1, 2 );
		},
		easeInOutBack: function (x) {
			return x < 0.5 ?
				( pow( 2 * x, 2 ) * ( ( c2 + 1 ) * 2 * x - c2 ) ) / 2 :
				( pow( 2 * x - 2, 2 ) *( ( c2 + 1 ) * ( x * 2 - 2 ) + c2 ) + 2 ) / 2;
		},
		easeInBounce: function (x) {
			return 1 - bounceOut( 1 - x );
		},
		easeOutBounce: bounceOut,
		easeInOutBounce: function (x) {
			return x < 0.5 ?
				( 1 - bounceOut( 1 - 2 * x ) ) / 2 :
				( 1 + bounceOut( 2 * x - 1 ) ) / 2;
		}
	});
	return $;
});

}(jQuery));

//safari back button fix
jQuery( window ).on( "pageshow", function(event) {
    if (event.originalEvent.persisted) {
        jQuery(".searchandfilter").off();
        jQuery(".searchandfilter").searchAndFilter();
    }
});

/* wpnumb - nouislider number formatting */
!function(){"use strict";function e(e){return e.split("").reverse().join("")}function n(e,n){return e.substring(0,n.length)===n}function r(e,n){return e.slice(-1*n.length)===n}function t(e,n,r){if((e[n]||e[r])&&e[n]===e[r])throw new Error(n)}function i(e){return"number"==typeof e&&isFinite(e)}function o(e,n){var r=Math.pow(10,n);return(Math.round(e*r)/r).toFixed(n)}function u(n,r,t,u,f,a,c,s,p,d,l,h){var g,v,w,m=h,x="",b="";return a&&(h=a(h)),i(h)?(n!==!1&&0===parseFloat(h.toFixed(n))&&(h=0),0>h&&(g=!0,h=Math.abs(h)),n!==!1&&(h=o(h,n)),h=h.toString(),-1!==h.indexOf(".")?(v=h.split("."),w=v[0],t&&(x=t+v[1])):w=h,r&&(w=e(w).match(/.{1,3}/g),w=e(w.join(e(r)))),g&&s&&(b+=s),u&&(b+=u),g&&p&&(b+=p),b+=w,b+=x,f&&(b+=f),d&&(b=d(b,m)),b):!1}function f(e,t,o,u,f,a,c,s,p,d,l,h){var g,v="";return l&&(h=l(h)),h&&"string"==typeof h?(s&&n(h,s)&&(h=h.replace(s,""),g=!0),u&&n(h,u)&&(h=h.replace(u,"")),p&&n(h,p)&&(h=h.replace(p,""),g=!0),f&&r(h,f)&&(h=h.slice(0,-1*f.length)),t&&(h=h.split(t).join("")),o&&(h=h.replace(o,".")),g&&(v+="-"),v+=h,v=v.replace(/[^0-9\.\-.]/g,""),""===v?!1:(v=Number(v),c&&(v=c(v)),i(v)?v:!1)):!1}function a(e){var n,r,i,o={};for(n=0;n<p.length;n+=1)if(r=p[n],i=e[r],void 0===i)"negative"!==r||o.negativeBefore?"mark"===r&&"."!==o.thousand?o[r]=".":o[r]=!1:o[r]="-";else if("decimals"===r){if(!(i>=0&&8>i))throw new Error(r);o[r]=i}else if("encoder"===r||"decoder"===r||"edit"===r||"undo"===r){if("function"!=typeof i)throw new Error(r);o[r]=i}else{if("string"!=typeof i)throw new Error(r);o[r]=i}return t(o,"mark","thousand"),t(o,"prefix","negative"),t(o,"prefix","negativeBefore"),o}function c(e,n,r){var t,i=[];for(t=0;t<p.length;t+=1)i.push(e[p[t]]);return i.push(r),n.apply("",i)}function s(e){return this instanceof s?void("object"==typeof e&&(e=a(e),this.to=function(n){return c(e,u,n)},this.from=function(n){return c(e,f,n)})):new s(e)}var p=["decimals","thousand","mark","prefix","postfix","encoder","decoder","negativeBefore","negative","edit","undo"];window.wNumb=s}();


}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
//# sourceMappingURL=data:application/json;charset:utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9wdWJsaWMvYXNzZXRzL2pzL2FwcC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiXG52YXIgc3RhdGUgPSByZXF1aXJlKCcuL2luY2x1ZGVzL3N0YXRlJyk7XG52YXIgcGx1Z2luID0gcmVxdWlyZSgnLi9pbmNsdWRlcy9wbHVnaW4nKTtcblxuXG4oZnVuY3Rpb24gKCAkICkge1xuXG5cdFwidXNlIHN0cmljdFwiO1xuXG5cdCQoZnVuY3Rpb24gKCkge1xuXG5cdFx0aWYgKCFPYmplY3Qua2V5cykge1xuXHRcdCAgT2JqZWN0LmtleXMgPSAoZnVuY3Rpb24gKCkge1xuXHRcdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFx0dmFyIGhhc093blByb3BlcnR5ID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eSxcblx0XHRcdFx0aGFzRG9udEVudW1CdWcgPSAhKHt0b1N0cmluZzogbnVsbH0pLnByb3BlcnR5SXNFbnVtZXJhYmxlKCd0b1N0cmluZycpLFxuXHRcdFx0XHRkb250RW51bXMgPSBbXG5cdFx0XHRcdCAgJ3RvU3RyaW5nJyxcblx0XHRcdFx0ICAndG9Mb2NhbGVTdHJpbmcnLFxuXHRcdFx0XHQgICd2YWx1ZU9mJyxcblx0XHRcdFx0ICAnaGFzT3duUHJvcGVydHknLFxuXHRcdFx0XHQgICdpc1Byb3RvdHlwZU9mJyxcblx0XHRcdFx0ICAncHJvcGVydHlJc0VudW1lcmFibGUnLFxuXHRcdFx0XHQgICdjb25zdHJ1Y3Rvcidcblx0XHRcdFx0XSxcblx0XHRcdFx0ZG9udEVudW1zTGVuZ3RoID0gZG9udEVudW1zLmxlbmd0aDtcblxuXHRcdFx0cmV0dXJuIGZ1bmN0aW9uIChvYmopIHtcblx0XHRcdCAgaWYgKHR5cGVvZiBvYmogIT09ICdvYmplY3QnICYmICh0eXBlb2Ygb2JqICE9PSAnZnVuY3Rpb24nIHx8IG9iaiA9PT0gbnVsbCkpIHtcblx0XHRcdFx0dGhyb3cgbmV3IFR5cGVFcnJvcignT2JqZWN0LmtleXMgY2FsbGVkIG9uIG5vbi1vYmplY3QnKTtcblx0XHRcdCAgfVxuXG5cdFx0XHQgIHZhciByZXN1bHQgPSBbXSwgcHJvcCwgaTtcblxuXHRcdFx0ICBmb3IgKHByb3AgaW4gb2JqKSB7XG5cdFx0XHRcdGlmIChoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwgcHJvcCkpIHtcblx0XHRcdFx0ICByZXN1bHQucHVzaChwcm9wKTtcblx0XHRcdFx0fVxuXHRcdFx0ICB9XG5cblx0XHRcdCAgaWYgKGhhc0RvbnRFbnVtQnVnKSB7XG5cdFx0XHRcdGZvciAoaSA9IDA7IGkgPCBkb250RW51bXNMZW5ndGg7IGkrKykge1xuXHRcdFx0XHQgIGlmIChoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwgZG9udEVudW1zW2ldKSkge1xuXHRcdFx0XHRcdHJlc3VsdC5wdXNoKGRvbnRFbnVtc1tpXSk7XG5cdFx0XHRcdCAgfVxuXHRcdFx0XHR9XG5cdFx0XHQgIH1cblx0XHRcdCAgcmV0dXJuIHJlc3VsdDtcblx0XHRcdH07XG5cdFx0ICB9KCkpO1xuXHRcdH1cblxuXHRcdC8qIFNlYXJjaCAmIEZpbHRlciBqUXVlcnkgUGx1Z2luICovXG5cdFx0JC5mbi5zZWFyY2hBbmRGaWx0ZXIgPSBwbHVnaW47XG5cblx0XHQvKiBpbml0ICovXG5cdFx0JChcIi5zZWFyY2hhbmRmaWx0ZXJcIikuc2VhcmNoQW5kRmlsdGVyKCk7XG5cblx0XHQvKiBleHRlcm5hbCBjb250cm9scyAqL1xuXHRcdCQoZG9jdW1lbnQpLm9uKFwiY2xpY2tcIiwgXCIuc2VhcmNoLWZpbHRlci1yZXNldFwiLCBmdW5jdGlvbihlKXtcblxuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHR2YXIgc2VhcmNoRm9ybUlEID0gdHlwZW9mKCQodGhpcykuYXR0cihcImRhdGEtc2VhcmNoLWZvcm0taWRcIikpIT1cInVuZGVmaW5lZFwiID8gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZWFyY2gtZm9ybS1pZFwiKSA6IFwiXCI7XG5cdFx0XHR2YXIgc3VibWl0Rm9ybSA9IHR5cGVvZigkKHRoaXMpLmF0dHIoXCJkYXRhLXNmLXN1Ym1pdC1mb3JtXCIpKSE9XCJ1bmRlZmluZWRcIiA/ICQodGhpcykuYXR0cihcImRhdGEtc2Ytc3VibWl0LWZvcm1cIikgOiBcIlwiO1xuXG5cdFx0XHRzdGF0ZS5nZXRTZWFyY2hGb3JtKHNlYXJjaEZvcm1JRCkucmVzZXQoc3VibWl0Rm9ybSk7XG5cblx0XHRcdC8vdmFyICRsaW5rZWQgPSAkKFwiI3NlYXJjaC1maWx0ZXItZm9ybS1cIitzZWFyY2hGb3JtSUQpLnNlYXJjaEZpbHRlckZvcm0oe2FjdGlvbjogXCJyZXNldFwifSk7XG5cblx0XHRcdHJldHVybiBmYWxzZTtcblxuXHRcdH0pO1xuXG5cdH0pO1xuXG5cbi8qXG4gKiBqUXVlcnkgRWFzaW5nIHYxLjQuMSAtIGh0dHA6Ly9nc2dkLmNvLnVrL3NhbmRib3gvanF1ZXJ5L2Vhc2luZy9cbiAqIE9wZW4gc291cmNlIHVuZGVyIHRoZSBCU0QgTGljZW5zZS5cbiAqIENvcHlyaWdodCDCqSAyMDA4IEdlb3JnZSBNY0dpbmxleSBTbWl0aFxuICogQWxsIHJpZ2h0cyByZXNlcnZlZC5cbiAqIGh0dHBzOi8vcmF3LmdpdGh1Yi5jb20vZ2RzbWl0aC9qcXVlcnkuZWFzaW5nL21hc3Rlci9MSUNFTlNFXG4qL1xuXG4vKiBnbG9iYWxzIGpRdWVyeSwgZGVmaW5lLCBtb2R1bGUsIHJlcXVpcmUgKi9cbihmdW5jdGlvbiAoZmFjdG9yeSkge1xuXHRpZiAodHlwZW9mIGRlZmluZSA9PT0gXCJmdW5jdGlvblwiICYmIGRlZmluZS5hbWQpIHtcblx0XHRkZWZpbmUoWydqcXVlcnknXSwgZnVuY3Rpb24gKCQpIHtcblx0XHRcdHJldHVybiBmYWN0b3J5KCQpO1xuXHRcdH0pO1xuXHR9IGVsc2UgaWYgKHR5cGVvZiBtb2R1bGUgPT09IFwib2JqZWN0XCIgJiYgdHlwZW9mIG1vZHVsZS5leHBvcnRzID09PSBcIm9iamVjdFwiKSB7XG5cdFx0bW9kdWxlLmV4cG9ydHMgPSBmYWN0b3J5KCh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCkpO1xuXHR9IGVsc2Uge1xuXHRcdGZhY3RvcnkoalF1ZXJ5KTtcblx0fVxufSkoZnVuY3Rpb24oJCl7XG5cblx0Ly8gUHJlc2VydmUgdGhlIG9yaWdpbmFsIGpRdWVyeSBcInN3aW5nXCIgZWFzaW5nIGFzIFwianN3aW5nXCJcblx0aWYgKHR5cGVvZiAkLmVhc2luZyAhPT0gJ3VuZGVmaW5lZCcpIHtcblx0XHQkLmVhc2luZ1snanN3aW5nJ10gPSAkLmVhc2luZ1snc3dpbmcnXTtcblx0fVxuXG5cdHZhciBwb3cgPSBNYXRoLnBvdyxcblx0XHRzcXJ0ID0gTWF0aC5zcXJ0LFxuXHRcdHNpbiA9IE1hdGguc2luLFxuXHRcdGNvcyA9IE1hdGguY29zLFxuXHRcdFBJID0gTWF0aC5QSSxcblx0XHRjMSA9IDEuNzAxNTgsXG5cdFx0YzIgPSBjMSAqIDEuNTI1LFxuXHRcdGMzID0gYzEgKyAxLFxuXHRcdGM0ID0gKCAyICogUEkgKSAvIDMsXG5cdFx0YzUgPSAoIDIgKiBQSSApIC8gNC41O1xuXG5cdC8vIHggaXMgdGhlIGZyYWN0aW9uIG9mIGFuaW1hdGlvbiBwcm9ncmVzcywgaW4gdGhlIHJhbmdlIDAuLjFcblx0ZnVuY3Rpb24gYm91bmNlT3V0KHgpIHtcblx0XHR2YXIgbjEgPSA3LjU2MjUsXG5cdFx0XHRkMSA9IDIuNzU7XG5cdFx0aWYgKCB4IDwgMS9kMSApIHtcblx0XHRcdHJldHVybiBuMSp4Kng7XG5cdFx0fSBlbHNlIGlmICggeCA8IDIvZDEgKSB7XG5cdFx0XHRyZXR1cm4gbjEqKHgtPSgxLjUvZDEpKSp4ICsgLjc1O1xuXHRcdH0gZWxzZSBpZiAoIHggPCAyLjUvZDEgKSB7XG5cdFx0XHRyZXR1cm4gbjEqKHgtPSgyLjI1L2QxKSkqeCArIC45Mzc1O1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRyZXR1cm4gbjEqKHgtPSgyLjYyNS9kMSkpKnggKyAuOTg0Mzc1O1xuXHRcdH1cblx0fVxuXG5cdCQuZXh0ZW5kKCAkLmVhc2luZywge1xuXHRcdGRlZjogJ2Vhc2VPdXRRdWFkJyxcblx0XHRzd2luZzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiAkLmVhc2luZ1skLmVhc2luZy5kZWZdKHgpO1xuXHRcdH0sXG5cdFx0ZWFzZUluUXVhZDogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ICogeDtcblx0XHR9LFxuXHRcdGVhc2VPdXRRdWFkOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIDEgLSAoIDEgLSB4ICkgKiAoIDEgLSB4ICk7XG5cdFx0fSxcblx0XHRlYXNlSW5PdXRRdWFkOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPCAwLjUgP1xuXHRcdFx0XHQyICogeCAqIHggOlxuXHRcdFx0XHQxIC0gcG93KCAtMiAqIHggKyAyLCAyICkgLyAyO1xuXHRcdH0sXG5cdFx0ZWFzZUluQ3ViaWM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCAqIHggKiB4O1xuXHRcdH0sXG5cdFx0ZWFzZU91dEN1YmljOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIDEgLSBwb3coIDEgLSB4LCAzICk7XG5cdFx0fSxcblx0XHRlYXNlSW5PdXRDdWJpYzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4IDwgMC41ID9cblx0XHRcdFx0NCAqIHggKiB4ICogeCA6XG5cdFx0XHRcdDEgLSBwb3coIC0yICogeCArIDIsIDMgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5RdWFydDogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ICogeCAqIHggKiB4O1xuXHRcdH0sXG5cdFx0ZWFzZU91dFF1YXJ0OiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIDEgLSBwb3coIDEgLSB4LCA0ICk7XG5cdFx0fSxcblx0XHRlYXNlSW5PdXRRdWFydDogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4IDwgMC41ID9cblx0XHRcdFx0OCAqIHggKiB4ICogeCAqIHggOlxuXHRcdFx0XHQxIC0gcG93KCAtMiAqIHggKyAyLCA0ICkgLyAyO1xuXHRcdH0sXG5cdFx0ZWFzZUluUXVpbnQ6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCAqIHggKiB4ICogeCAqIHg7XG5cdFx0fSxcblx0XHRlYXNlT3V0UXVpbnQ6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtIHBvdyggMSAtIHgsIDUgKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dFF1aW50OiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPCAwLjUgP1xuXHRcdFx0XHQxNiAqIHggKiB4ICogeCAqIHggKiB4IDpcblx0XHRcdFx0MSAtIHBvdyggLTIgKiB4ICsgMiwgNSApIC8gMjtcblx0XHR9LFxuXHRcdGVhc2VJblNpbmU6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtIGNvcyggeCAqIFBJLzIgKTtcblx0XHR9LFxuXHRcdGVhc2VPdXRTaW5lOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHNpbiggeCAqIFBJLzIgKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dFNpbmU6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gLSggY29zKCBQSSAqIHggKSAtIDEgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5FeHBvOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPT09IDAgPyAwIDogcG93KCAyLCAxMCAqIHggLSAxMCApO1xuXHRcdH0sXG5cdFx0ZWFzZU91dEV4cG86IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA9PT0gMSA/IDEgOiAxIC0gcG93KCAyLCAtMTAgKiB4ICk7XG5cdFx0fSxcblx0XHRlYXNlSW5PdXRFeHBvOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPT09IDAgPyAwIDogeCA9PT0gMSA/IDEgOiB4IDwgMC41ID9cblx0XHRcdFx0cG93KCAyLCAyMCAqIHggLSAxMCApIC8gMiA6XG5cdFx0XHRcdCggMiAtIHBvdyggMiwgLTIwICogeCArIDEwICkgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5DaXJjOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIDEgLSBzcXJ0KCAxIC0gcG93KCB4LCAyICkgKTtcblx0XHR9LFxuXHRcdGVhc2VPdXRDaXJjOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHNxcnQoIDEgLSBwb3coIHggLSAxLCAyICkgKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dENpcmM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA8IDAuNSA/XG5cdFx0XHRcdCggMSAtIHNxcnQoIDEgLSBwb3coIDIgKiB4LCAyICkgKSApIC8gMiA6XG5cdFx0XHRcdCggc3FydCggMSAtIHBvdyggLTIgKiB4ICsgMiwgMiApICkgKyAxICkgLyAyO1xuXHRcdH0sXG5cdFx0ZWFzZUluRWxhc3RpYzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ID09PSAwID8gMCA6IHggPT09IDEgPyAxIDpcblx0XHRcdFx0LXBvdyggMiwgMTAgKiB4IC0gMTAgKSAqIHNpbiggKCB4ICogMTAgLSAxMC43NSApICogYzQgKTtcblx0XHR9LFxuXHRcdGVhc2VPdXRFbGFzdGljOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPT09IDAgPyAwIDogeCA9PT0gMSA/IDEgOlxuXHRcdFx0XHRwb3coIDIsIC0xMCAqIHggKSAqIHNpbiggKCB4ICogMTAgLSAwLjc1ICkgKiBjNCApICsgMTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dEVsYXN0aWM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA9PT0gMCA/IDAgOiB4ID09PSAxID8gMSA6IHggPCAwLjUgP1xuXHRcdFx0XHQtKCBwb3coIDIsIDIwICogeCAtIDEwICkgKiBzaW4oICggMjAgKiB4IC0gMTEuMTI1ICkgKiBjNSApKSAvIDIgOlxuXHRcdFx0XHRwb3coIDIsIC0yMCAqIHggKyAxMCApICogc2luKCAoIDIwICogeCAtIDExLjEyNSApICogYzUgKSAvIDIgKyAxO1xuXHRcdH0sXG5cdFx0ZWFzZUluQmFjazogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiBjMyAqIHggKiB4ICogeCAtIGMxICogeCAqIHg7XG5cdFx0fSxcblx0XHRlYXNlT3V0QmFjazogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiAxICsgYzMgKiBwb3coIHggLSAxLCAzICkgKyBjMSAqIHBvdyggeCAtIDEsIDIgKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dEJhY2s6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA8IDAuNSA/XG5cdFx0XHRcdCggcG93KCAyICogeCwgMiApICogKCAoIGMyICsgMSApICogMiAqIHggLSBjMiApICkgLyAyIDpcblx0XHRcdFx0KCBwb3coIDIgKiB4IC0gMiwgMiApICooICggYzIgKyAxICkgKiAoIHggKiAyIC0gMiApICsgYzIgKSArIDIgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5Cb3VuY2U6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtIGJvdW5jZU91dCggMSAtIHggKTtcblx0XHR9LFxuXHRcdGVhc2VPdXRCb3VuY2U6IGJvdW5jZU91dCxcblx0XHRlYXNlSW5PdXRCb3VuY2U6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA8IDAuNSA/XG5cdFx0XHRcdCggMSAtIGJvdW5jZU91dCggMSAtIDIgKiB4ICkgKSAvIDIgOlxuXHRcdFx0XHQoIDEgKyBib3VuY2VPdXQoIDIgKiB4IC0gMSApICkgLyAyO1xuXHRcdH1cblx0fSk7XG5cdHJldHVybiAkO1xufSk7XG5cbn0oalF1ZXJ5KSk7XG5cbi8vc2FmYXJpIGJhY2sgYnV0dG9uIGZpeFxualF1ZXJ5KCB3aW5kb3cgKS5vbiggXCJwYWdlc2hvd1wiLCBmdW5jdGlvbihldmVudCkge1xuICAgIGlmIChldmVudC5vcmlnaW5hbEV2ZW50LnBlcnNpc3RlZCkge1xuICAgICAgICBqUXVlcnkoXCIuc2VhcmNoYW5kZmlsdGVyXCIpLm9mZigpO1xuICAgICAgICBqUXVlcnkoXCIuc2VhcmNoYW5kZmlsdGVyXCIpLnNlYXJjaEFuZEZpbHRlcigpO1xuICAgIH1cbn0pO1xuXG4vKiB3cG51bWIgLSBub3Vpc2xpZGVyIG51bWJlciBmb3JtYXR0aW5nICovXG4hZnVuY3Rpb24oKXtcInVzZSBzdHJpY3RcIjtmdW5jdGlvbiBlKGUpe3JldHVybiBlLnNwbGl0KFwiXCIpLnJldmVyc2UoKS5qb2luKFwiXCIpfWZ1bmN0aW9uIG4oZSxuKXtyZXR1cm4gZS5zdWJzdHJpbmcoMCxuLmxlbmd0aCk9PT1ufWZ1bmN0aW9uIHIoZSxuKXtyZXR1cm4gZS5zbGljZSgtMSpuLmxlbmd0aCk9PT1ufWZ1bmN0aW9uIHQoZSxuLHIpe2lmKChlW25dfHxlW3JdKSYmZVtuXT09PWVbcl0pdGhyb3cgbmV3IEVycm9yKG4pfWZ1bmN0aW9uIGkoZSl7cmV0dXJuXCJudW1iZXJcIj09dHlwZW9mIGUmJmlzRmluaXRlKGUpfWZ1bmN0aW9uIG8oZSxuKXt2YXIgcj1NYXRoLnBvdygxMCxuKTtyZXR1cm4oTWF0aC5yb3VuZChlKnIpL3IpLnRvRml4ZWQobil9ZnVuY3Rpb24gdShuLHIsdCx1LGYsYSxjLHMscCxkLGwsaCl7dmFyIGcsdix3LG09aCx4PVwiXCIsYj1cIlwiO3JldHVybiBhJiYoaD1hKGgpKSxpKGgpPyhuIT09ITEmJjA9PT1wYXJzZUZsb2F0KGgudG9GaXhlZChuKSkmJihoPTApLDA+aCYmKGc9ITAsaD1NYXRoLmFicyhoKSksbiE9PSExJiYoaD1vKGgsbikpLGg9aC50b1N0cmluZygpLC0xIT09aC5pbmRleE9mKFwiLlwiKT8odj1oLnNwbGl0KFwiLlwiKSx3PXZbMF0sdCYmKHg9dCt2WzFdKSk6dz1oLHImJih3PWUodykubWF0Y2goLy57MSwzfS9nKSx3PWUody5qb2luKGUocikpKSksZyYmcyYmKGIrPXMpLHUmJihiKz11KSxnJiZwJiYoYis9cCksYis9dyxiKz14LGYmJihiKz1mKSxkJiYoYj1kKGIsbSkpLGIpOiExfWZ1bmN0aW9uIGYoZSx0LG8sdSxmLGEsYyxzLHAsZCxsLGgpe3ZhciBnLHY9XCJcIjtyZXR1cm4gbCYmKGg9bChoKSksaCYmXCJzdHJpbmdcIj09dHlwZW9mIGg/KHMmJm4oaCxzKSYmKGg9aC5yZXBsYWNlKHMsXCJcIiksZz0hMCksdSYmbihoLHUpJiYoaD1oLnJlcGxhY2UodSxcIlwiKSkscCYmbihoLHApJiYoaD1oLnJlcGxhY2UocCxcIlwiKSxnPSEwKSxmJiZyKGgsZikmJihoPWguc2xpY2UoMCwtMSpmLmxlbmd0aCkpLHQmJihoPWguc3BsaXQodCkuam9pbihcIlwiKSksbyYmKGg9aC5yZXBsYWNlKG8sXCIuXCIpKSxnJiYodis9XCItXCIpLHYrPWgsdj12LnJlcGxhY2UoL1teMC05XFwuXFwtLl0vZyxcIlwiKSxcIlwiPT09dj8hMToodj1OdW1iZXIodiksYyYmKHY9Yyh2KSksaSh2KT92OiExKSk6ITF9ZnVuY3Rpb24gYShlKXt2YXIgbixyLGksbz17fTtmb3Iobj0wO248cC5sZW5ndGg7bis9MSlpZihyPXBbbl0saT1lW3JdLHZvaWQgMD09PWkpXCJuZWdhdGl2ZVwiIT09cnx8by5uZWdhdGl2ZUJlZm9yZT9cIm1hcmtcIj09PXImJlwiLlwiIT09by50aG91c2FuZD9vW3JdPVwiLlwiOm9bcl09ITE6b1tyXT1cIi1cIjtlbHNlIGlmKFwiZGVjaW1hbHNcIj09PXIpe2lmKCEoaT49MCYmOD5pKSl0aHJvdyBuZXcgRXJyb3Iocik7b1tyXT1pfWVsc2UgaWYoXCJlbmNvZGVyXCI9PT1yfHxcImRlY29kZXJcIj09PXJ8fFwiZWRpdFwiPT09cnx8XCJ1bmRvXCI9PT1yKXtpZihcImZ1bmN0aW9uXCIhPXR5cGVvZiBpKXRocm93IG5ldyBFcnJvcihyKTtvW3JdPWl9ZWxzZXtpZihcInN0cmluZ1wiIT10eXBlb2YgaSl0aHJvdyBuZXcgRXJyb3Iocik7b1tyXT1pfXJldHVybiB0KG8sXCJtYXJrXCIsXCJ0aG91c2FuZFwiKSx0KG8sXCJwcmVmaXhcIixcIm5lZ2F0aXZlXCIpLHQobyxcInByZWZpeFwiLFwibmVnYXRpdmVCZWZvcmVcIiksb31mdW5jdGlvbiBjKGUsbixyKXt2YXIgdCxpPVtdO2Zvcih0PTA7dDxwLmxlbmd0aDt0Kz0xKWkucHVzaChlW3BbdF1dKTtyZXR1cm4gaS5wdXNoKHIpLG4uYXBwbHkoXCJcIixpKX1mdW5jdGlvbiBzKGUpe3JldHVybiB0aGlzIGluc3RhbmNlb2Ygcz92b2lkKFwib2JqZWN0XCI9PXR5cGVvZiBlJiYoZT1hKGUpLHRoaXMudG89ZnVuY3Rpb24obil7cmV0dXJuIGMoZSx1LG4pfSx0aGlzLmZyb209ZnVuY3Rpb24obil7cmV0dXJuIGMoZSxmLG4pfSkpOm5ldyBzKGUpfXZhciBwPVtcImRlY2ltYWxzXCIsXCJ0aG91c2FuZFwiLFwibWFya1wiLFwicHJlZml4XCIsXCJwb3N0Zml4XCIsXCJlbmNvZGVyXCIsXCJkZWNvZGVyXCIsXCJuZWdhdGl2ZUJlZm9yZVwiLFwibmVnYXRpdmVcIixcImVkaXRcIixcInVuZG9cIl07d2luZG93LndOdW1iPXN9KCk7XG5cbiJdfQ==
},{"./includes/plugin":3,"./includes/state":5}],2:[function(require,module,exports){
/*! nouislider - 11.1.0 - 2018-04-02 11:18:13 */

(function (factory) {

    if ( typeof define === 'function' && define.amd ) {

        // AMD. Register as an anonymous module.
        define([], factory);

    } else if ( typeof exports === 'object' ) {

        // Node/CommonJS
        module.exports = factory();

    } else {

        // Browser globals
        window.noUiSlider = factory();
    }

}(function( ){

	'use strict';

	var VERSION = '11.1.0';


	function isValidFormatter ( entry ) {
		return typeof entry === 'object' && typeof entry.to === 'function' && typeof entry.from === 'function';
	}

	function removeElement ( el ) {
		el.parentElement.removeChild(el);
	}

	function isSet ( value ) {
		return value !== null && value !== undefined;
	}

	// Bindable version
	function preventDefault ( e ) {
		e.preventDefault();
	}

	// Removes duplicates from an array.
	function unique ( array ) {
		return array.filter(function(a){
			return !this[a] ? this[a] = true : false;
		}, {});
	}

	// Round a value to the closest 'to'.
	function closest ( value, to ) {
		return Math.round(value / to) * to;
	}

	// Current position of an element relative to the document.
	function offset ( elem, orientation ) {

		var rect = elem.getBoundingClientRect();
		var doc = elem.ownerDocument;
		var docElem = doc.documentElement;
		var pageOffset = getPageOffset(doc);

		// getBoundingClientRect contains left scroll in Chrome on Android.
		// I haven't found a feature detection that proves this. Worst case
		// scenario on mis-match: the 'tap' feature on horizontal sliders breaks.
		if ( /webkit.*Chrome.*Mobile/i.test(navigator.userAgent) ) {
			pageOffset.x = 0;
		}

		return orientation ? (rect.top + pageOffset.y - docElem.clientTop) : (rect.left + pageOffset.x - docElem.clientLeft);
	}

	// Checks whether a value is numerical.
	function isNumeric ( a ) {
		return typeof a === 'number' && !isNaN( a ) && isFinite( a );
	}

	// Sets a class and removes it after [duration] ms.
	function addClassFor ( element, className, duration ) {
		if (duration > 0) {
		addClass(element, className);
			setTimeout(function(){
				removeClass(element, className);
			}, duration);
		}
	}

	// Limits a value to 0 - 100
	function limit ( a ) {
		return Math.max(Math.min(a, 100), 0);
	}

	// Wraps a variable as an array, if it isn't one yet.
	// Note that an input array is returned by reference!
	function asArray ( a ) {
		return Array.isArray(a) ? a : [a];
	}

	// Counts decimals
	function countDecimals ( numStr ) {
		numStr = String(numStr);
		var pieces = numStr.split(".");
		return pieces.length > 1 ? pieces[1].length : 0;
	}

	// http://youmightnotneedjquery.com/#add_class
	function addClass ( el, className ) {
		if ( el.classList ) {
			el.classList.add(className);
		} else {
			el.className += ' ' + className;
		}
	}

	// http://youmightnotneedjquery.com/#remove_class
	function removeClass ( el, className ) {
		if ( el.classList ) {
			el.classList.remove(className);
		} else {
			el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
		}
	}

	// https://plainjs.com/javascript/attributes/adding-removing-and-testing-for-classes-9/
	function hasClass ( el, className ) {
		return el.classList ? el.classList.contains(className) : new RegExp('\\b' + className + '\\b').test(el.className);
	}

	// https://developer.mozilla.org/en-US/docs/Web/API/Window/scrollY#Notes
	function getPageOffset ( doc ) {

		var supportPageOffset = window.pageXOffset !== undefined;
		var isCSS1Compat = ((doc.compatMode || "") === "CSS1Compat");
		var x = supportPageOffset ? window.pageXOffset : isCSS1Compat ? doc.documentElement.scrollLeft : doc.body.scrollLeft;
		var y = supportPageOffset ? window.pageYOffset : isCSS1Compat ? doc.documentElement.scrollTop : doc.body.scrollTop;

		return {
			x: x,
			y: y
		};
	}

	// we provide a function to compute constants instead
	// of accessing window.* as soon as the module needs it
	// so that we do not compute anything if not needed
	function getActions ( ) {

		// Determine the events to bind. IE11 implements pointerEvents without
		// a prefix, which breaks compatibility with the IE10 implementation.
		return window.navigator.pointerEnabled ? {
			start: 'pointerdown',
			move: 'pointermove',
			end: 'pointerup'
		} : window.navigator.msPointerEnabled ? {
			start: 'MSPointerDown',
			move: 'MSPointerMove',
			end: 'MSPointerUp'
		} : {
			start: 'mousedown touchstart',
			move: 'mousemove touchmove',
			end: 'mouseup touchend'
		};
	}

	// https://github.com/WICG/EventListenerOptions/blob/gh-pages/explainer.md
	// Issue #785
	function getSupportsPassive ( ) {

		var supportsPassive = false;

		try {

			var opts = Object.defineProperty({}, 'passive', {
				get: function() {
					supportsPassive = true;
				}
			});

			window.addEventListener('test', null, opts);

		} catch (e) {}

		return supportsPassive;
	}

	function getSupportsTouchActionNone ( ) {
		return window.CSS && CSS.supports && CSS.supports('touch-action', 'none');
	}


// Value calculation

	// Determine the size of a sub-range in relation to a full range.
	function subRangeRatio ( pa, pb ) {
		return (100 / (pb - pa));
	}

	// (percentage) How many percent is this value of this range?
	function fromPercentage ( range, value ) {
		return (value * 100) / ( range[1] - range[0] );
	}

	// (percentage) Where is this value on this range?
	function toPercentage ( range, value ) {
		return fromPercentage( range, range[0] < 0 ?
			value + Math.abs(range[0]) :
				value - range[0] );
	}

	// (value) How much is this percentage on this range?
	function isPercentage ( range, value ) {
		return ((value * ( range[1] - range[0] )) / 100) + range[0];
	}


// Range conversion

	function getJ ( value, arr ) {

		var j = 1;

		while ( value >= arr[j] ){
			j += 1;
		}

		return j;
	}

	// (percentage) Input a value, find where, on a scale of 0-100, it applies.
	function toStepping ( xVal, xPct, value ) {

		if ( value >= xVal.slice(-1)[0] ){
			return 100;
		}

		var j = getJ( value, xVal );
		var va = xVal[j-1];
		var vb = xVal[j];
		var pa = xPct[j-1];
		var pb = xPct[j];

		return pa + (toPercentage([va, vb], value) / subRangeRatio (pa, pb));
	}

	// (value) Input a percentage, find where it is on the specified range.
	function fromStepping ( xVal, xPct, value ) {

		// There is no range group that fits 100
		if ( value >= 100 ){
			return xVal.slice(-1)[0];
		}

		var j = getJ( value, xPct );
		var va = xVal[j-1];
		var vb = xVal[j];
		var pa = xPct[j-1];
		var pb = xPct[j];

		return isPercentage([va, vb], (value - pa) * subRangeRatio (pa, pb));
	}

	// (percentage) Get the step that applies at a certain value.
	function getStep ( xPct, xSteps, snap, value ) {

		if ( value === 100 ) {
			return value;
		}

		var j = getJ( value, xPct );
		var a = xPct[j-1];
		var b = xPct[j];

		// If 'snap' is set, steps are used as fixed points on the slider.
		if ( snap ) {

			// Find the closest position, a or b.
			if ((value - a) > ((b-a)/2)){
				return b;
			}

			return a;
		}

		if ( !xSteps[j-1] ){
			return value;
		}

		return xPct[j-1] + closest(
			value - xPct[j-1],
			xSteps[j-1]
		);
	}


// Entry parsing

	function handleEntryPoint ( index, value, that ) {

		var percentage;

		// Wrap numerical input in an array.
		if ( typeof value === "number" ) {
			value = [value];
		}

		// Reject any invalid input, by testing whether value is an array.
		if ( !Array.isArray(value) ){
			throw new Error("noUiSlider (" + VERSION + "): 'range' contains invalid value.");
		}

		// Covert min/max syntax to 0 and 100.
		if ( index === 'min' ) {
			percentage = 0;
		} else if ( index === 'max' ) {
			percentage = 100;
		} else {
			percentage = parseFloat( index );
		}

		// Check for correct input.
		if ( !isNumeric( percentage ) || !isNumeric( value[0] ) ) {
			throw new Error("noUiSlider (" + VERSION + "): 'range' value isn't numeric.");
		}

		// Store values.
		that.xPct.push( percentage );
		that.xVal.push( value[0] );

		// NaN will evaluate to false too, but to keep
		// logging clear, set step explicitly. Make sure
		// not to override the 'step' setting with false.
		if ( !percentage ) {
			if ( !isNaN( value[1] ) ) {
				that.xSteps[0] = value[1];
			}
		} else {
			that.xSteps.push( isNaN(value[1]) ? false : value[1] );
		}

		that.xHighestCompleteStep.push(0);
	}

	function handleStepPoint ( i, n, that ) {

		// Ignore 'false' stepping.
		if ( !n ) {
			return true;
		}

		// Factor to range ratio
		that.xSteps[i] = fromPercentage([that.xVal[i], that.xVal[i+1]], n) / subRangeRatio(that.xPct[i], that.xPct[i+1]);

		var totalSteps = (that.xVal[i+1] - that.xVal[i]) / that.xNumSteps[i];
		var highestStep = Math.ceil(Number(totalSteps.toFixed(3)) - 1);
		var step = that.xVal[i] + (that.xNumSteps[i] * highestStep);

		that.xHighestCompleteStep[i] = step;
	}


// Interface

	function Spectrum ( entry, snap, singleStep ) {

		this.xPct = [];
		this.xVal = [];
		this.xSteps = [ singleStep || false ];
		this.xNumSteps = [ false ];
		this.xHighestCompleteStep = [];

		this.snap = snap;

		var index;
		var ordered = []; // [0, 'min'], [1, '50%'], [2, 'max']

		// Map the object keys to an array.
		for ( index in entry ) {
			if ( entry.hasOwnProperty(index) ) {
				ordered.push([entry[index], index]);
			}
		}

		// Sort all entries by value (numeric sort).
		if ( ordered.length && typeof ordered[0][0] === "object" ) {
			ordered.sort(function(a, b) { return a[0][0] - b[0][0]; });
		} else {
			ordered.sort(function(a, b) { return a[0] - b[0]; });
		}


		// Convert all entries to subranges.
		for ( index = 0; index < ordered.length; index++ ) {
			handleEntryPoint(ordered[index][1], ordered[index][0], this);
		}

		// Store the actual step values.
		// xSteps is sorted in the same order as xPct and xVal.
		this.xNumSteps = this.xSteps.slice(0);

		// Convert all numeric steps to the percentage of the subrange they represent.
		for ( index = 0; index < this.xNumSteps.length; index++ ) {
			handleStepPoint(index, this.xNumSteps[index], this);
		}
	}

	Spectrum.prototype.getMargin = function ( value ) {

		var step = this.xNumSteps[0];

		if ( step && ((value / step) % 1) !== 0 ) {
			throw new Error("noUiSlider (" + VERSION + "): 'limit', 'margin' and 'padding' must be divisible by step.");
		}

		return this.xPct.length === 2 ? fromPercentage(this.xVal, value) : false;
	};

	Spectrum.prototype.toStepping = function ( value ) {

		value = toStepping( this.xVal, this.xPct, value );

		return value;
	};

	Spectrum.prototype.fromStepping = function ( value ) {

		return fromStepping( this.xVal, this.xPct, value );
	};

	Spectrum.prototype.getStep = function ( value ) {

		value = getStep(this.xPct, this.xSteps, this.snap, value );

		return value;
	};

	Spectrum.prototype.getNearbySteps = function ( value ) {

		var j = getJ(value, this.xPct);

		return {
			stepBefore: { startValue: this.xVal[j-2], step: this.xNumSteps[j-2], highestStep: this.xHighestCompleteStep[j-2] },
			thisStep: { startValue: this.xVal[j-1], step: this.xNumSteps[j-1], highestStep: this.xHighestCompleteStep[j-1] },
			stepAfter: { startValue: this.xVal[j-0], step: this.xNumSteps[j-0], highestStep: this.xHighestCompleteStep[j-0] }
		};
	};

	Spectrum.prototype.countStepDecimals = function () {
		var stepDecimals = this.xNumSteps.map(countDecimals);
		return Math.max.apply(null, stepDecimals);
	};

	// Outside testing
	Spectrum.prototype.convert = function ( value ) {
		return this.getStep(this.toStepping(value));
	};

/*	Every input option is tested and parsed. This'll prevent
	endless validation in internal methods. These tests are
	structured with an item for every option available. An
	option can be marked as required by setting the 'r' flag.
	The testing function is provided with three arguments:
		- The provided value for the option;
		- A reference to the options object;
		- The name for the option;

	The testing function returns false when an error is detected,
	or true when everything is OK. It can also modify the option
	object, to make sure all values can be correctly looped elsewhere. */

	var defaultFormatter = { 'to': function( value ){
		return value !== undefined && value.toFixed(2);
	}, 'from': Number };

	function validateFormat ( entry ) {

		// Any object with a to and from method is supported.
		if ( isValidFormatter(entry) ) {
			return true;
		}

		throw new Error("noUiSlider (" + VERSION + "): 'format' requires 'to' and 'from' methods.");
	}

	function testStep ( parsed, entry ) {

		if ( !isNumeric( entry ) ) {
			throw new Error("noUiSlider (" + VERSION + "): 'step' is not numeric.");
		}

		// The step option can still be used to set stepping
		// for linear sliders. Overwritten if set in 'range'.
		parsed.singleStep = entry;
	}

	function testRange ( parsed, entry ) {

		// Filter incorrect input.
		if ( typeof entry !== 'object' || Array.isArray(entry) ) {
			throw new Error("noUiSlider (" + VERSION + "): 'range' is not an object.");
		}

		// Catch missing start or end.
		if ( entry.min === undefined || entry.max === undefined ) {
			throw new Error("noUiSlider (" + VERSION + "): Missing 'min' or 'max' in 'range'.");
		}

		// Catch equal start or end.
		if ( entry.min === entry.max ) {
			throw new Error("noUiSlider (" + VERSION + "): 'range' 'min' and 'max' cannot be equal.");
		}

		parsed.spectrum = new Spectrum(entry, parsed.snap, parsed.singleStep);
	}

	function testStart ( parsed, entry ) {

		entry = asArray(entry);

		// Validate input. Values aren't tested, as the public .val method
		// will always provide a valid location.
		if ( !Array.isArray( entry ) || !entry.length ) {
			throw new Error("noUiSlider (" + VERSION + "): 'start' option is incorrect.");
		}

		// Store the number of handles.
		parsed.handles = entry.length;

		// When the slider is initialized, the .val method will
		// be called with the start options.
		parsed.start = entry;
	}

	function testSnap ( parsed, entry ) {

		// Enforce 100% stepping within subranges.
		parsed.snap = entry;

		if ( typeof entry !== 'boolean' ){
			throw new Error("noUiSlider (" + VERSION + "): 'snap' option must be a boolean.");
		}
	}

	function testAnimate ( parsed, entry ) {

		// Enforce 100% stepping within subranges.
		parsed.animate = entry;

		if ( typeof entry !== 'boolean' ){
			throw new Error("noUiSlider (" + VERSION + "): 'animate' option must be a boolean.");
		}
	}

	function testAnimationDuration ( parsed, entry ) {

		parsed.animationDuration = entry;

		if ( typeof entry !== 'number' ){
			throw new Error("noUiSlider (" + VERSION + "): 'animationDuration' option must be a number.");
		}
	}

	function testConnect ( parsed, entry ) {

		var connect = [false];
		var i;

		// Map legacy options
		if ( entry === 'lower' ) {
			entry = [true, false];
		}

		else if ( entry === 'upper' ) {
			entry = [false, true];
		}

		// Handle boolean options
		if ( entry === true || entry === false ) {

			for ( i = 1; i < parsed.handles; i++ ) {
				connect.push(entry);
			}

			connect.push(false);
		}

		// Reject invalid input
		else if ( !Array.isArray( entry ) || !entry.length || entry.length !== parsed.handles + 1 ) {
			throw new Error("noUiSlider (" + VERSION + "): 'connect' option doesn't match handle count.");
		}

		else {
			connect = entry;
		}

		parsed.connect = connect;
	}

	function testOrientation ( parsed, entry ) {

		// Set orientation to an a numerical value for easy
		// array selection.
		switch ( entry ){
			case 'horizontal':
				parsed.ort = 0;
				break;
			case 'vertical':
				parsed.ort = 1;
				break;
			default:
				throw new Error("noUiSlider (" + VERSION + "): 'orientation' option is invalid.");
		}
	}

	function testMargin ( parsed, entry ) {

		if ( !isNumeric(entry) ){
			throw new Error("noUiSlider (" + VERSION + "): 'margin' option must be numeric.");
		}

		// Issue #582
		if ( entry === 0 ) {
			return;
		}

		parsed.margin = parsed.spectrum.getMargin(entry);

		if ( !parsed.margin ) {
			throw new Error("noUiSlider (" + VERSION + "): 'margin' option is only supported on linear sliders.");
		}
	}

	function testLimit ( parsed, entry ) {

		if ( !isNumeric(entry) ){
			throw new Error("noUiSlider (" + VERSION + "): 'limit' option must be numeric.");
		}

		parsed.limit = parsed.spectrum.getMargin(entry);

		if ( !parsed.limit || parsed.handles < 2 ) {
			throw new Error("noUiSlider (" + VERSION + "): 'limit' option is only supported on linear sliders with 2 or more handles.");
		}
	}

	function testPadding ( parsed, entry ) {

		if ( !isNumeric(entry) && !Array.isArray(entry) ){
			throw new Error("noUiSlider (" + VERSION + "): 'padding' option must be numeric or array of exactly 2 numbers.");
		}

		if ( Array.isArray(entry) && !(entry.length === 2 || isNumeric(entry[0]) || isNumeric(entry[1])) ) {
			throw new Error("noUiSlider (" + VERSION + "): 'padding' option must be numeric or array of exactly 2 numbers.");
		}

		if ( entry === 0 ) {
			return;
		}

		if ( !Array.isArray(entry) ) {
			entry = [entry, entry];
		}

		// 'getMargin' returns false for invalid values.
		parsed.padding = [parsed.spectrum.getMargin(entry[0]), parsed.spectrum.getMargin(entry[1])];

		if ( parsed.padding[0] === false || parsed.padding[1] === false ) {
			throw new Error("noUiSlider (" + VERSION + "): 'padding' option is only supported on linear sliders.");
		}

		if ( parsed.padding[0] < 0 || parsed.padding[1] < 0 ) {
			throw new Error("noUiSlider (" + VERSION + "): 'padding' option must be a positive number(s).");
		}

		if ( parsed.padding[0] + parsed.padding[1] >= 100 ) {
			throw new Error("noUiSlider (" + VERSION + "): 'padding' option must not exceed 100% of the range.");
		}
	}

	function testDirection ( parsed, entry ) {

		// Set direction as a numerical value for easy parsing.
		// Invert connection for RTL sliders, so that the proper
		// handles get the connect/background classes.
		switch ( entry ) {
			case 'ltr':
				parsed.dir = 0;
				break;
			case 'rtl':
				parsed.dir = 1;
				break;
			default:
				throw new Error("noUiSlider (" + VERSION + "): 'direction' option was not recognized.");
		}
	}

	function testBehaviour ( parsed, entry ) {

		// Make sure the input is a string.
		if ( typeof entry !== 'string' ) {
			throw new Error("noUiSlider (" + VERSION + "): 'behaviour' must be a string containing options.");
		}

		// Check if the string contains any keywords.
		// None are required.
		var tap = entry.indexOf('tap') >= 0;
		var drag = entry.indexOf('drag') >= 0;
		var fixed = entry.indexOf('fixed') >= 0;
		var snap = entry.indexOf('snap') >= 0;
		var hover = entry.indexOf('hover') >= 0;

		if ( fixed ) {

			if ( parsed.handles !== 2 ) {
				throw new Error("noUiSlider (" + VERSION + "): 'fixed' behaviour must be used with 2 handles");
			}

			// Use margin to enforce fixed state
			testMargin(parsed, parsed.start[1] - parsed.start[0]);
		}

		parsed.events = {
			tap: tap || snap,
			drag: drag,
			fixed: fixed,
			snap: snap,
			hover: hover
		};
	}

	function testTooltips ( parsed, entry ) {

		if ( entry === false ) {
			return;
		}

		else if ( entry === true ) {

			parsed.tooltips = [];

			for ( var i = 0; i < parsed.handles; i++ ) {
				parsed.tooltips.push(true);
			}
		}

		else {

			parsed.tooltips = asArray(entry);

			if ( parsed.tooltips.length !== parsed.handles ) {
				throw new Error("noUiSlider (" + VERSION + "): must pass a formatter for all handles.");
			}

			parsed.tooltips.forEach(function(formatter){
				if ( typeof formatter !== 'boolean' && (typeof formatter !== 'object' || typeof formatter.to !== 'function') ) {
					throw new Error("noUiSlider (" + VERSION + "): 'tooltips' must be passed a formatter or 'false'.");
				}
			});
		}
	}

	function testAriaFormat ( parsed, entry ) {
		parsed.ariaFormat = entry;
		validateFormat(entry);
	}

	function testFormat ( parsed, entry ) {
		parsed.format = entry;
		validateFormat(entry);
	}

	function testCssPrefix ( parsed, entry ) {

		if ( typeof entry !== 'string' && entry !== false ) {
			throw new Error("noUiSlider (" + VERSION + "): 'cssPrefix' must be a string or `false`.");
		}

		parsed.cssPrefix = entry;
	}

	function testCssClasses ( parsed, entry ) {

		if ( typeof entry !== 'object' ) {
			throw new Error("noUiSlider (" + VERSION + "): 'cssClasses' must be an object.");
		}

		if ( typeof parsed.cssPrefix === 'string' ) {
			parsed.cssClasses = {};

			for ( var key in entry ) {
				if ( !entry.hasOwnProperty(key) ) { continue; }

				parsed.cssClasses[key] = parsed.cssPrefix + entry[key];
			}
		} else {
			parsed.cssClasses = entry;
		}
	}

	// Test all developer settings and parse to assumption-safe values.
	function testOptions ( options ) {

		// To prove a fix for #537, freeze options here.
		// If the object is modified, an error will be thrown.
		// Object.freeze(options);

		var parsed = {
			margin: 0,
			limit: 0,
			padding: 0,
			animate: true,
			animationDuration: 300,
			ariaFormat: defaultFormatter,
			format: defaultFormatter
		};

		// Tests are executed in the order they are presented here.
		var tests = {
			'step': { r: false, t: testStep },
			'start': { r: true, t: testStart },
			'connect': { r: true, t: testConnect },
			'direction': { r: true, t: testDirection },
			'snap': { r: false, t: testSnap },
			'animate': { r: false, t: testAnimate },
			'animationDuration': { r: false, t: testAnimationDuration },
			'range': { r: true, t: testRange },
			'orientation': { r: false, t: testOrientation },
			'margin': { r: false, t: testMargin },
			'limit': { r: false, t: testLimit },
			'padding': { r: false, t: testPadding },
			'behaviour': { r: true, t: testBehaviour },
			'ariaFormat': { r: false, t: testAriaFormat },
			'format': { r: false, t: testFormat },
			'tooltips': { r: false, t: testTooltips },
			'cssPrefix': { r: true, t: testCssPrefix },
			'cssClasses': { r: true, t: testCssClasses }
		};

		var defaults = {
			'connect': false,
			'direction': 'ltr',
			'behaviour': 'tap',
			'orientation': 'horizontal',
			'cssPrefix' : 'noUi-',
			'cssClasses': {
				target: 'target',
				base: 'base',
				origin: 'origin',
				handle: 'handle',
				handleLower: 'handle-lower',
				handleUpper: 'handle-upper',
				horizontal: 'horizontal',
				vertical: 'vertical',
				background: 'background',
				connect: 'connect',
				connects: 'connects',
				ltr: 'ltr',
				rtl: 'rtl',
				draggable: 'draggable',
				drag: 'state-drag',
				tap: 'state-tap',
				active: 'active',
				tooltip: 'tooltip',
				pips: 'pips',
				pipsHorizontal: 'pips-horizontal',
				pipsVertical: 'pips-vertical',
				marker: 'marker',
				markerHorizontal: 'marker-horizontal',
				markerVertical: 'marker-vertical',
				markerNormal: 'marker-normal',
				markerLarge: 'marker-large',
				markerSub: 'marker-sub',
				value: 'value',
				valueHorizontal: 'value-horizontal',
				valueVertical: 'value-vertical',
				valueNormal: 'value-normal',
				valueLarge: 'value-large',
				valueSub: 'value-sub'
			}
		};

		// AriaFormat defaults to regular format, if any.
		if ( options.format && !options.ariaFormat ) {
			options.ariaFormat = options.format;
		}

		// Run all options through a testing mechanism to ensure correct
		// input. It should be noted that options might get modified to
		// be handled properly. E.g. wrapping integers in arrays.
		Object.keys(tests).forEach(function( name ){

			// If the option isn't set, but it is required, throw an error.
			if ( !isSet(options[name]) && defaults[name] === undefined ) {

				if ( tests[name].r ) {
					throw new Error("noUiSlider (" + VERSION + "): '" + name + "' is required.");
				}

				return true;
			}

			tests[name].t( parsed, !isSet(options[name]) ? defaults[name] : options[name] );
		});

		// Forward pips options
		parsed.pips = options.pips;

		// All recent browsers accept unprefixed transform.
		// We need -ms- for IE9 and -webkit- for older Android;
		// Assume use of -webkit- if unprefixed and -ms- are not supported.
		// https://caniuse.com/#feat=transforms2d
		var d = document.createElement("div");
		var msPrefix = d.style.msTransform !== undefined;
		var noPrefix = d.style.transform !== undefined;

		parsed.transformRule = noPrefix ? 'transform' : (msPrefix ? 'msTransform' : 'webkitTransform');

		// Pips don't move, so we can place them using left/top.
		var styles = [['left', 'top'], ['right', 'bottom']];

		parsed.style = styles[parsed.dir][parsed.ort];

		return parsed;
	}


function scope ( target, options, originalOptions ){

	var actions = getActions();
	var supportsTouchActionNone = getSupportsTouchActionNone();
	var supportsPassive = supportsTouchActionNone && getSupportsPassive();

	// All variables local to 'scope' are prefixed with 'scope_'
	var scope_Target = target;
	var scope_Locations = [];
	var scope_Base;
	var scope_Handles;
	var scope_HandleNumbers = [];
	var scope_ActiveHandlesCount = 0;
	var scope_Connects;
	var scope_Spectrum = options.spectrum;
	var scope_Values = [];
	var scope_Events = {};
	var scope_Self;
	var scope_Pips;
	var scope_Document = target.ownerDocument;
	var scope_DocumentElement = scope_Document.documentElement;
	var scope_Body = scope_Document.body;


	// For horizontal sliders in standard ltr documents,
	// make .noUi-origin overflow to the left so the document doesn't scroll.
	var scope_DirOffset = (scope_Document.dir === 'rtl') || (options.ort === 1) ? 0 : 100;

/*! In this file: Construction of DOM elements; */

	// Creates a node, adds it to target, returns the new node.
	function addNodeTo ( addTarget, className ) {

		var div = scope_Document.createElement('div');

		if ( className ) {
			addClass(div, className);
		}

		addTarget.appendChild(div);

		return div;
	}

	// Append a origin to the base
	function addOrigin ( base, handleNumber ) {

		var origin = addNodeTo(base, options.cssClasses.origin);
		var handle = addNodeTo(origin, options.cssClasses.handle);

		handle.setAttribute('data-handle', handleNumber);

		// https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/tabindex
		// 0 = focusable and reachable
		handle.setAttribute('tabindex', '0');
		handle.setAttribute('role', 'slider');
		handle.setAttribute('aria-orientation', options.ort ? 'vertical' : 'horizontal');

		if ( handleNumber === 0 ) {
			addClass(handle, options.cssClasses.handleLower);
		}

		else if ( handleNumber === options.handles - 1 ) {
			addClass(handle, options.cssClasses.handleUpper);
		}

		return origin;
	}

	// Insert nodes for connect elements
	function addConnect ( base, add ) {

		if ( !add ) {
			return false;
		}

		return addNodeTo(base, options.cssClasses.connect);
	}

	// Add handles to the slider base.
	function addElements ( connectOptions, base ) {

		var connectBase = addNodeTo(base, options.cssClasses.connects);

		scope_Handles = [];
		scope_Connects = [];

		scope_Connects.push(addConnect(connectBase, connectOptions[0]));

		// [::::O====O====O====]
		// connectOptions = [0, 1, 1, 1]

		for ( var i = 0; i < options.handles; i++ ) {
			// Keep a list of all added handles.
			scope_Handles.push(addOrigin(base, i));
			scope_HandleNumbers[i] = i;
			scope_Connects.push(addConnect(connectBase, connectOptions[i + 1]));
		}
	}

	// Initialize a single slider.
	function addSlider ( addTarget ) {

		// Apply classes and data to the target.
		addClass(addTarget, options.cssClasses.target);

		if ( options.dir === 0 ) {
			addClass(addTarget, options.cssClasses.ltr);
		} else {
			addClass(addTarget, options.cssClasses.rtl);
		}

		if ( options.ort === 0 ) {
			addClass(addTarget, options.cssClasses.horizontal);
		} else {
			addClass(addTarget, options.cssClasses.vertical);
		}

		scope_Base = addNodeTo(addTarget, options.cssClasses.base);
	}


	function addTooltip ( handle, handleNumber ) {

		if ( !options.tooltips[handleNumber] ) {
			return false;
		}

		return addNodeTo(handle.firstChild, options.cssClasses.tooltip);
	}

	// The tooltips option is a shorthand for using the 'update' event.
	function tooltips ( ) {

		// Tooltips are added with options.tooltips in original order.
		var tips = scope_Handles.map(addTooltip);

		bindEvent('update', function(values, handleNumber, unencoded) {

			if ( !tips[handleNumber] ) {
				return;
			}

			var formattedValue = values[handleNumber];

			if ( options.tooltips[handleNumber] !== true ) {
				formattedValue = options.tooltips[handleNumber].to(unencoded[handleNumber]);
			}

			tips[handleNumber].innerHTML = formattedValue;
		});
	}


	function aria ( ) {

		bindEvent('update', function ( values, handleNumber, unencoded, tap, positions ) {

			// Update Aria Values for all handles, as a change in one changes min and max values for the next.
			scope_HandleNumbers.forEach(function( index ){

				var handle = scope_Handles[index];

				var min = checkHandlePosition(scope_Locations, index, 0, true, true, true);
				var max = checkHandlePosition(scope_Locations, index, 100, true, true, true);

				var now = positions[index];
				var text = options.ariaFormat.to(unencoded[index]);

				handle.children[0].setAttribute('aria-valuemin', min.toFixed(1));
				handle.children[0].setAttribute('aria-valuemax', max.toFixed(1));
				handle.children[0].setAttribute('aria-valuenow', now.toFixed(1));
				handle.children[0].setAttribute('aria-valuetext', text);
			});
		});
	}


	function getGroup ( mode, values, stepped ) {

		// Use the range.
		if ( mode === 'range' || mode === 'steps' ) {
			return scope_Spectrum.xVal;
		}

		if ( mode === 'count' ) {

			if ( values < 2 ) {
				throw new Error("noUiSlider (" + VERSION + "): 'values' (>= 2) required for mode 'count'.");
			}

			// Divide 0 - 100 in 'count' parts.
			var interval = values - 1;
			var spread = ( 100 / interval );

			values = [];

			// List these parts and have them handled as 'positions'.
			while ( interval-- ) {
				values[ interval ] = ( interval * spread );
			}

			values.push(100);

			mode = 'positions';
		}

		if ( mode === 'positions' ) {

			// Map all percentages to on-range values.
			return values.map(function( value ){
				return scope_Spectrum.fromStepping( stepped ? scope_Spectrum.getStep( value ) : value );
			});
		}

		if ( mode === 'values' ) {

			// If the value must be stepped, it needs to be converted to a percentage first.
			if ( stepped ) {

				return values.map(function( value ){

					// Convert to percentage, apply step, return to value.
					return scope_Spectrum.fromStepping( scope_Spectrum.getStep( scope_Spectrum.toStepping( value ) ) );
				});

			}

			// Otherwise, we can simply use the values.
			return values;
		}
	}

	function generateSpread ( density, mode, group ) {

		function safeIncrement(value, increment) {
			// Avoid floating point variance by dropping the smallest decimal places.
			return (value + increment).toFixed(7) / 1;
		}

		var indexes = {};
		var firstInRange = scope_Spectrum.xVal[0];
		var lastInRange = scope_Spectrum.xVal[scope_Spectrum.xVal.length-1];
		var ignoreFirst = false;
		var ignoreLast = false;
		var prevPct = 0;

		// Create a copy of the group, sort it and filter away all duplicates.
		group = unique(group.slice().sort(function(a, b){ return a - b; }));

		// Make sure the range starts with the first element.
		if ( group[0] !== firstInRange ) {
			group.unshift(firstInRange);
			ignoreFirst = true;
		}

		// Likewise for the last one.
		if ( group[group.length - 1] !== lastInRange ) {
			group.push(lastInRange);
			ignoreLast = true;
		}

		group.forEach(function ( current, index ) {

			// Get the current step and the lower + upper positions.
			var step;
			var i;
			var q;
			var low = current;
			var high = group[index+1];
			var newPct;
			var pctDifference;
			var pctPos;
			var type;
			var steps;
			var realSteps;
			var stepsize;

			// When using 'steps' mode, use the provided steps.
			// Otherwise, we'll step on to the next subrange.
			if ( mode === 'steps' ) {
				step = scope_Spectrum.xNumSteps[ index ];
			}

			// Default to a 'full' step.
			if ( !step ) {
				step = high-low;
			}

			// Low can be 0, so test for false. If high is undefined,
			// we are at the last subrange. Index 0 is already handled.
			if ( low === false || high === undefined ) {
				return;
			}

			// Make sure step isn't 0, which would cause an infinite loop (#654)
			step = Math.max(step, 0.0000001);

			// Find all steps in the subrange.
			for ( i = low; i <= high; i = safeIncrement(i, step) ) {

				// Get the percentage value for the current step,
				// calculate the size for the subrange.
				newPct = scope_Spectrum.toStepping( i );
				pctDifference = newPct - prevPct;

				steps = pctDifference / density;
				realSteps = Math.round(steps);

				// This ratio represents the amount of percentage-space a point indicates.
				// For a density 1 the points/percentage = 1. For density 2, that percentage needs to be re-devided.
				// Round the percentage offset to an even number, then divide by two
				// to spread the offset on both sides of the range.
				stepsize = pctDifference/realSteps;

				// Divide all points evenly, adding the correct number to this subrange.
				// Run up to <= so that 100% gets a point, event if ignoreLast is set.
				for ( q = 1; q <= realSteps; q += 1 ) {

					// The ratio between the rounded value and the actual size might be ~1% off.
					// Correct the percentage offset by the number of points
					// per subrange. density = 1 will result in 100 points on the
					// full range, 2 for 50, 4 for 25, etc.
					pctPos = prevPct + ( q * stepsize );
					indexes[pctPos.toFixed(5)] = ['x', 0];
				}

				// Determine the point type.
				type = (group.indexOf(i) > -1) ? 1 : ( mode === 'steps' ? 2 : 0 );

				// Enforce the 'ignoreFirst' option by overwriting the type for 0.
				if ( !index && ignoreFirst ) {
					type = 0;
				}

				if ( !(i === high && ignoreLast)) {
					// Mark the 'type' of this point. 0 = plain, 1 = real value, 2 = step value.
					indexes[newPct.toFixed(5)] = [i, type];
				}

				// Update the percentage count.
				prevPct = newPct;
			}
		});

		return indexes;
	}

	function addMarking ( spread, filterFunc, formatter ) {

		var element = scope_Document.createElement('div');

		var valueSizeClasses = [
			options.cssClasses.valueNormal,
			options.cssClasses.valueLarge,
			options.cssClasses.valueSub
		];
		var markerSizeClasses = [
			options.cssClasses.markerNormal,
			options.cssClasses.markerLarge,
			options.cssClasses.markerSub
		];
		var valueOrientationClasses = [
			options.cssClasses.valueHorizontal,
			options.cssClasses.valueVertical
		];
		var markerOrientationClasses = [
			options.cssClasses.markerHorizontal,
			options.cssClasses.markerVertical
		];

		addClass(element, options.cssClasses.pips);
		addClass(element, options.ort === 0 ? options.cssClasses.pipsHorizontal : options.cssClasses.pipsVertical);

		function getClasses( type, source ){
			var a = source === options.cssClasses.value;
			var orientationClasses = a ? valueOrientationClasses : markerOrientationClasses;
			var sizeClasses = a ? valueSizeClasses : markerSizeClasses;

			return source + ' ' + orientationClasses[options.ort] + ' ' + sizeClasses[type];
		}

		function addSpread ( offset, values ){

			// Apply the filter function, if it is set.
			values[1] = (values[1] && filterFunc) ? filterFunc(values[0], values[1]) : values[1];

			// Add a marker for every point
			var node = addNodeTo(element, false);
				node.className = getClasses(values[1], options.cssClasses.marker);
				node.style[options.style] = offset + '%';

			// Values are only appended for points marked '1' or '2'.
			if ( values[1] ) {
				node = addNodeTo(element, false);
				node.className = getClasses(values[1], options.cssClasses.value);
				node.setAttribute('data-value', values[0]);
				node.style[options.style] = offset + '%';
				node.innerText = formatter.to(values[0]);
			}
		}

		// Append all points.
		Object.keys(spread).forEach(function(a){
			addSpread(a, spread[a]);
		});

		return element;
	}

	function removePips ( ) {
		if ( scope_Pips ) {
			removeElement(scope_Pips);
			scope_Pips = null;
		}
	}

	function pips ( grid ) {

		// Fix #669
		removePips();

		var mode = grid.mode;
		var density = grid.density || 1;
		var filter = grid.filter || false;
		var values = grid.values || false;
		var stepped = grid.stepped || false;
		var group = getGroup( mode, values, stepped );
		var spread = generateSpread( density, mode, group );
		var format = grid.format || {
			to: Math.round
		};

		scope_Pips = scope_Target.appendChild(addMarking(
			spread,
			filter,
			format
		));

		return scope_Pips;
	}

/*! In this file: Browser events (not slider events like slide, change); */

	// Shorthand for base dimensions.
	function baseSize ( ) {
		var rect = scope_Base.getBoundingClientRect();
		var alt = 'offset' + ['Width', 'Height'][options.ort];
		return options.ort === 0 ? (rect.width||scope_Base[alt]) : (rect.height||scope_Base[alt]);
	}

	// Handler for attaching events trough a proxy.
	function attachEvent ( events, element, callback, data ) {

		// This function can be used to 'filter' events to the slider.
		// element is a node, not a nodeList

		var method = function ( e ){

			e = fixEvent(e, data.pageOffset, data.target || element);

			// fixEvent returns false if this event has a different target
			// when handling (multi-) touch events;
			if ( !e ) {
				return false;
			}

			// doNotReject is passed by all end events to make sure released touches
			// are not rejected, leaving the slider "stuck" to the cursor;
			if ( scope_Target.hasAttribute('disabled') && !data.doNotReject ) {
				return false;
			}

			// Stop if an active 'tap' transition is taking place.
			if ( hasClass(scope_Target, options.cssClasses.tap) && !data.doNotReject ) {
				return false;
			}

			// Ignore right or middle clicks on start #454
			if ( events === actions.start && e.buttons !== undefined && e.buttons > 1 ) {
				return false;
			}

			// Ignore right or middle clicks on start #454
			if ( data.hover && e.buttons ) {
				return false;
			}

			// 'supportsPassive' is only true if a browser also supports touch-action: none in CSS.
			// iOS safari does not, so it doesn't get to benefit from passive scrolling. iOS does support
			// touch-action: manipulation, but that allows panning, which breaks
			// sliders after zooming/on non-responsive pages.
			// See: https://bugs.webkit.org/show_bug.cgi?id=133112
			if ( !supportsPassive ) {
				e.preventDefault();
			}

			e.calcPoint = e.points[ options.ort ];

			// Call the event handler with the event [ and additional data ].
			callback ( e, data );
		};

		var methods = [];

		// Bind a closure on the target for every event type.
		events.split(' ').forEach(function( eventName ){
			element.addEventListener(eventName, method, supportsPassive ? { passive: true } : false);
			methods.push([eventName, method]);
		});

		return methods;
	}

	// Provide a clean event with standardized offset values.
	function fixEvent ( e, pageOffset, eventTarget ) {

		// Filter the event to register the type, which can be
		// touch, mouse or pointer. Offset changes need to be
		// made on an event specific basis.
		var touch = e.type.indexOf('touch') === 0;
		var mouse = e.type.indexOf('mouse') === 0;
		var pointer = e.type.indexOf('pointer') === 0;

		var x;
		var y;

		// IE10 implemented pointer events with a prefix;
		if ( e.type.indexOf('MSPointer') === 0 ) {
			pointer = true;
		}

		// In the event that multitouch is activated, the only thing one handle should be concerned
		// about is the touches that originated on top of it.
		if ( touch ) {

			// Returns true if a touch originated on the target.
			var isTouchOnTarget = function (checkTouch) {
				return checkTouch.target === eventTarget || eventTarget.contains(checkTouch.target);
			};

			// In the case of touchstart events, we need to make sure there is still no more than one
			// touch on the target so we look amongst all touches.
			if (e.type === 'touchstart') {

				var targetTouches = Array.prototype.filter.call(e.touches, isTouchOnTarget);

				// Do not support more than one touch per handle.
				if ( targetTouches.length > 1 ) {
					return false;
				}

				x = targetTouches[0].pageX;
				y = targetTouches[0].pageY;

			} else {

				// In the other cases, find on changedTouches is enough.
				var targetTouch = Array.prototype.find.call(e.changedTouches, isTouchOnTarget);

				// Cancel if the target touch has not moved.
				if ( !targetTouch ) {
					return false;
				}

				x = targetTouch.pageX;
				y = targetTouch.pageY;
			}
		}

		pageOffset = pageOffset || getPageOffset(scope_Document);

		if ( mouse || pointer ) {
			x = e.clientX + pageOffset.x;
			y = e.clientY + pageOffset.y;
		}

		e.pageOffset = pageOffset;
		e.points = [x, y];
		e.cursor = mouse || pointer; // Fix #435

		return e;
	}

	// Translate a coordinate in the document to a percentage on the slider
	function calcPointToPercentage ( calcPoint ) {
		var location = calcPoint - offset(scope_Base, options.ort);
		var proposal = ( location * 100 ) / baseSize();

		// Clamp proposal between 0% and 100%
		// Out-of-bound coordinates may occur when .noUi-base pseudo-elements
		// are used (e.g. contained handles feature)
		proposal = limit(proposal);

		return options.dir ? 100 - proposal : proposal;
	}

	// Find handle closest to a certain percentage on the slider
	function getClosestHandle ( proposal ) {

		var closest = 100;
		var handleNumber = false;

		scope_Handles.forEach(function(handle, index){

			// Disabled handles are ignored
			if ( handle.hasAttribute('disabled') ) {
				return;
			}

			var pos = Math.abs(scope_Locations[index] - proposal);

			if ( pos < closest || (pos === 100 && closest === 100) ) {
				handleNumber = index;
				closest = pos;
			}
		});

		return handleNumber;
	}

	// Fire 'end' when a mouse or pen leaves the document.
	function documentLeave ( event, data ) {
		if ( event.type === "mouseout" && event.target.nodeName === "HTML" && event.relatedTarget === null ){
			eventEnd (event, data);
		}
	}

	// Handle movement on document for handle and range drag.
	function eventMove ( event, data ) {

		// Fix #498
		// Check value of .buttons in 'start' to work around a bug in IE10 mobile (data.buttonsProperty).
		// https://connect.microsoft.com/IE/feedback/details/927005/mobile-ie10-windows-phone-buttons-property-of-pointermove-event-always-zero
		// IE9 has .buttons and .which zero on mousemove.
		// Firefox breaks the spec MDN defines.
		if ( navigator.appVersion.indexOf("MSIE 9") === -1 && event.buttons === 0 && data.buttonsProperty !== 0 ) {
			return eventEnd(event, data);
		}

		// Check if we are moving up or down
		var movement = (options.dir ? -1 : 1) * (event.calcPoint - data.startCalcPoint);

		// Convert the movement into a percentage of the slider width/height
		var proposal = (movement * 100) / data.baseSize;

		moveHandles(movement > 0, proposal, data.locations, data.handleNumbers);
	}

	// Unbind move events on document, call callbacks.
	function eventEnd ( event, data ) {

		// The handle is no longer active, so remove the class.
		if ( data.handle ) {
			removeClass(data.handle, options.cssClasses.active);
			scope_ActiveHandlesCount -= 1;
		}

		// Unbind the move and end events, which are added on 'start'.
		data.listeners.forEach(function( c ) {
			scope_DocumentElement.removeEventListener(c[0], c[1]);
		});

		if ( scope_ActiveHandlesCount === 0 ) {
			// Remove dragging class.
			removeClass(scope_Target, options.cssClasses.drag);
			setZindex();

			// Remove cursor styles and text-selection events bound to the body.
			if ( event.cursor ) {
				scope_Body.style.cursor = '';
				scope_Body.removeEventListener('selectstart', preventDefault);
			}
		}

		data.handleNumbers.forEach(function(handleNumber){
			fireEvent('change', handleNumber);
			fireEvent('set', handleNumber);
			fireEvent('end', handleNumber);
		});
	}

	// Bind move events on document.
	function eventStart ( event, data ) {

		var handle;
		if ( data.handleNumbers.length === 1 ) {

			var handleOrigin = scope_Handles[data.handleNumbers[0]];

			// Ignore 'disabled' handles
			if ( handleOrigin.hasAttribute('disabled') ) {
				return false;
			}

			handle = handleOrigin.children[0];
			scope_ActiveHandlesCount += 1;

			// Mark the handle as 'active' so it can be styled.
			addClass(handle, options.cssClasses.active);
		}

		// A drag should never propagate up to the 'tap' event.
		event.stopPropagation();

		// Record the event listeners.
		var listeners = [];

		// Attach the move and end events.
		var moveEvent = attachEvent(actions.move, scope_DocumentElement, eventMove, {
			// The event target has changed so we need to propagate the original one so that we keep
			// relying on it to extract target touches.
			target: event.target,
			handle: handle,
			listeners: listeners,
			startCalcPoint: event.calcPoint,
			baseSize: baseSize(),
			pageOffset: event.pageOffset,
			handleNumbers: data.handleNumbers,
			buttonsProperty: event.buttons,
			locations: scope_Locations.slice()
		});

		var endEvent = attachEvent(actions.end, scope_DocumentElement, eventEnd, {
			target: event.target,
			handle: handle,
			listeners: listeners,
			doNotReject: true,
			handleNumbers: data.handleNumbers
		});

		var outEvent = attachEvent("mouseout", scope_DocumentElement, documentLeave, {
			target: event.target,
			handle: handle,
			listeners: listeners,
			doNotReject: true,
			handleNumbers: data.handleNumbers
		});

		// We want to make sure we pushed the listeners in the listener list rather than creating
		// a new one as it has already been passed to the event handlers.
		listeners.push.apply(listeners, moveEvent.concat(endEvent, outEvent));

		// Text selection isn't an issue on touch devices,
		// so adding cursor styles can be skipped.
		if ( event.cursor ) {

			// Prevent the 'I' cursor and extend the range-drag cursor.
			scope_Body.style.cursor = getComputedStyle(event.target).cursor;

			// Mark the target with a dragging state.
			if ( scope_Handles.length > 1 ) {
				addClass(scope_Target, options.cssClasses.drag);
			}

			// Prevent text selection when dragging the handles.
			// In noUiSlider <= 9.2.0, this was handled by calling preventDefault on mouse/touch start/move,
			// which is scroll blocking. The selectstart event is supported by FireFox starting from version 52,
			// meaning the only holdout is iOS Safari. This doesn't matter: text selection isn't triggered there.
			// The 'cursor' flag is false.
			// See: http://caniuse.com/#search=selectstart
			scope_Body.addEventListener('selectstart', preventDefault, false);
		}

		data.handleNumbers.forEach(function(handleNumber){
			fireEvent('start', handleNumber);
		});
	}

	// Move closest handle to tapped location.
	function eventTap ( event ) {

		// The tap event shouldn't propagate up
		event.stopPropagation();

		var proposal = calcPointToPercentage(event.calcPoint);
		var handleNumber = getClosestHandle(proposal);

		// Tackle the case that all handles are 'disabled'.
		if ( handleNumber === false ) {
			return false;
		}

		// Flag the slider as it is now in a transitional state.
		// Transition takes a configurable amount of ms (default 300). Re-enable the slider after that.
		if ( !options.events.snap ) {
			addClassFor(scope_Target, options.cssClasses.tap, options.animationDuration);
		}

		setHandle(handleNumber, proposal, true, true);

		setZindex();

		fireEvent('slide', handleNumber, true);
		fireEvent('update', handleNumber, true);
		fireEvent('change', handleNumber, true);
		fireEvent('set', handleNumber, true);

		if ( options.events.snap ) {
			eventStart(event, { handleNumbers: [handleNumber] });
		}
	}

	// Fires a 'hover' event for a hovered mouse/pen position.
	function eventHover ( event ) {

		var proposal = calcPointToPercentage(event.calcPoint);

		var to = scope_Spectrum.getStep(proposal);
		var value = scope_Spectrum.fromStepping(to);

		Object.keys(scope_Events).forEach(function( targetEvent ) {
			if ( 'hover' === targetEvent.split('.')[0] ) {
				scope_Events[targetEvent].forEach(function( callback ) {
					callback.call( scope_Self, value );
				});
			}
		});
	}

	// Attach events to several slider parts.
	function bindSliderEvents ( behaviour ) {

		// Attach the standard drag event to the handles.
		if ( !behaviour.fixed ) {

			scope_Handles.forEach(function( handle, index ){

				// These events are only bound to the visual handle
				// element, not the 'real' origin element.
				attachEvent ( actions.start, handle.children[0], eventStart, {
					handleNumbers: [index]
				});
			});
		}

		// Attach the tap event to the slider base.
		if ( behaviour.tap ) {
			attachEvent (actions.start, scope_Base, eventTap, {});
		}

		// Fire hover events
		if ( behaviour.hover ) {
			attachEvent (actions.move, scope_Base, eventHover, { hover: true });
		}

		// Make the range draggable.
		if ( behaviour.drag ){

			scope_Connects.forEach(function( connect, index ){

				if ( connect === false || index === 0 || index === scope_Connects.length - 1 ) {
					return;
				}

				var handleBefore = scope_Handles[index - 1];
				var handleAfter = scope_Handles[index];
				var eventHolders = [connect];

				addClass(connect, options.cssClasses.draggable);

				// When the range is fixed, the entire range can
				// be dragged by the handles. The handle in the first
				// origin will propagate the start event upward,
				// but it needs to be bound manually on the other.
				if ( behaviour.fixed ) {
					eventHolders.push(handleBefore.children[0]);
					eventHolders.push(handleAfter.children[0]);
				}

				eventHolders.forEach(function( eventHolder ) {
					attachEvent ( actions.start, eventHolder, eventStart, {
						handles: [handleBefore, handleAfter],
						handleNumbers: [index - 1, index]
					});
				});
			});
		}
	}

/*! In this file: Slider events (not browser events); */

	// Attach an event to this slider, possibly including a namespace
	function bindEvent ( namespacedEvent, callback ) {
		scope_Events[namespacedEvent] = scope_Events[namespacedEvent] || [];
		scope_Events[namespacedEvent].push(callback);

		// If the event bound is 'update,' fire it immediately for all handles.
		if ( namespacedEvent.split('.')[0] === 'update' ) {
			scope_Handles.forEach(function(a, index){
				fireEvent('update', index);
			});
		}
	}

	// Undo attachment of event
	function removeEvent ( namespacedEvent ) {

		var event = namespacedEvent && namespacedEvent.split('.')[0];
		var namespace = event && namespacedEvent.substring(event.length);

		Object.keys(scope_Events).forEach(function( bind ){

			var tEvent = bind.split('.')[0];
			var tNamespace = bind.substring(tEvent.length);

			if ( (!event || event === tEvent) && (!namespace || namespace === tNamespace) ) {
				delete scope_Events[bind];
			}
		});
	}

	// External event handling
	function fireEvent ( eventName, handleNumber, tap ) {

		Object.keys(scope_Events).forEach(function( targetEvent ) {

			var eventType = targetEvent.split('.')[0];

			if ( eventName === eventType ) {
				scope_Events[targetEvent].forEach(function( callback ) {

					callback.call(
						// Use the slider public API as the scope ('this')
						scope_Self,
						// Return values as array, so arg_1[arg_2] is always valid.
						scope_Values.map(options.format.to),
						// Handle index, 0 or 1
						handleNumber,
						// Unformatted slider values
						scope_Values.slice(),
						// Event is fired by tap, true or false
						tap || false,
						// Left offset of the handle, in relation to the slider
						scope_Locations.slice()
					);
				});
			}
		});
	}

/*! In this file: Mechanics for slider operation */

	function toPct ( pct ) {
		return pct + '%';
	}

	// Split out the handle positioning logic so the Move event can use it, too
	function checkHandlePosition ( reference, handleNumber, to, lookBackward, lookForward, getValue ) {

		// For sliders with multiple handles, limit movement to the other handle.
		// Apply the margin option by adding it to the handle positions.
		if ( scope_Handles.length > 1 ) {

			if ( lookBackward && handleNumber > 0 ) {
				to = Math.max(to, reference[handleNumber - 1] + options.margin);
			}

			if ( lookForward && handleNumber < scope_Handles.length - 1 ) {
				to = Math.min(to, reference[handleNumber + 1] - options.margin);
			}
		}

		// The limit option has the opposite effect, limiting handles to a
		// maximum distance from another. Limit must be > 0, as otherwise
		// handles would be unmoveable.
		if ( scope_Handles.length > 1 && options.limit ) {

			if ( lookBackward && handleNumber > 0 ) {
				to = Math.min(to, reference[handleNumber - 1] + options.limit);
			}

			if ( lookForward && handleNumber < scope_Handles.length - 1 ) {
				to = Math.max(to, reference[handleNumber + 1] - options.limit);
			}
		}

		// The padding option keeps the handles a certain distance from the
		// edges of the slider. Padding must be > 0.
		if ( options.padding ) {

			if ( handleNumber === 0 ) {
				to = Math.max(to, options.padding[0]);
			}

			if ( handleNumber === scope_Handles.length - 1 ) {
				to = Math.min(to, 100 - options.padding[1]);
			}
		}

		to = scope_Spectrum.getStep(to);

		// Limit percentage to the 0 - 100 range
		to = limit(to);

		// Return false if handle can't move
		if ( to === reference[handleNumber] && !getValue ) {
			return false;
		}

		return to;
	}

	// Uses slider orientation to create CSS rules. a = base value;
	function inRuleOrder ( v, a ) {
		var o = options.ort;
		return (o?a:v) + ', ' + (o?v:a);
	}

	// Moves handle(s) by a percentage
	// (bool, % to move, [% where handle started, ...], [index in scope_Handles, ...])
	function moveHandles ( upward, proposal, locations, handleNumbers ) {

		var proposals = locations.slice();

		var b = [!upward, upward];
		var f = [upward, !upward];

		// Copy handleNumbers so we don't change the dataset
		handleNumbers = handleNumbers.slice();

		// Check to see which handle is 'leading'.
		// If that one can't move the second can't either.
		if ( upward ) {
			handleNumbers.reverse();
		}

		// Step 1: get the maximum percentage that any of the handles can move
		if ( handleNumbers.length > 1 ) {

			handleNumbers.forEach(function(handleNumber, o) {

				var to = checkHandlePosition(proposals, handleNumber, proposals[handleNumber] + proposal, b[o], f[o], false);

				// Stop if one of the handles can't move.
				if ( to === false ) {
					proposal = 0;
				} else {
					proposal = to - proposals[handleNumber];
					proposals[handleNumber] = to;
				}
			});
		}

		// If using one handle, check backward AND forward
		else {
			b = f = [true];
		}

		var state = false;

		// Step 2: Try to set the handles with the found percentage
		handleNumbers.forEach(function(handleNumber, o) {
			state = setHandle(handleNumber, locations[handleNumber] + proposal, b[o], f[o]) || state;
		});

		// Step 3: If a handle moved, fire events
		if ( state ) {
			handleNumbers.forEach(function(handleNumber){
				fireEvent('update', handleNumber);
				fireEvent('slide', handleNumber);
			});
		}
	}

	// Takes a base value and an offset. This offset is used for the connect bar size.
	// In the initial design for this feature, the origin element was 1% wide.
	// Unfortunately, a rounding bug in Chrome makes it impossible to implement this feature
	// in this manner: https://bugs.chromium.org/p/chromium/issues/detail?id=798223
	function transformDirection ( a, b ) {
		return options.dir ? 100 - a - b : a;
	}

	// Updates scope_Locations and scope_Values, updates visual state
	function updateHandlePosition ( handleNumber, to ) {

		// Update locations.
		scope_Locations[handleNumber] = to;

		// Convert the value to the slider stepping/range.
		scope_Values[handleNumber] = scope_Spectrum.fromStepping(to);

		var rule = 'translate(' + inRuleOrder(toPct(transformDirection(to, 0) - scope_DirOffset), '0') + ')';
		scope_Handles[handleNumber].style[options.transformRule] = rule;

		updateConnect(handleNumber);
		updateConnect(handleNumber + 1);
	}

	// Handles before the slider middle are stacked later = higher,
	// Handles after the middle later is lower
	// [[7] [8] .......... | .......... [5] [4]
	function setZindex ( ) {

		scope_HandleNumbers.forEach(function(handleNumber){
			var dir = (scope_Locations[handleNumber] > 50 ? -1 : 1);
			var zIndex = 3 + (scope_Handles.length + (dir * handleNumber));
			scope_Handles[handleNumber].style.zIndex = zIndex;
		});
	}

	// Test suggested values and apply margin, step.
	function setHandle ( handleNumber, to, lookBackward, lookForward ) {

		to = checkHandlePosition(scope_Locations, handleNumber, to, lookBackward, lookForward, false);

		if ( to === false ) {
			return false;
		}

		updateHandlePosition(handleNumber, to);

		return true;
	}

	// Updates style attribute for connect nodes
	function updateConnect ( index ) {

		// Skip connects set to false
		if ( !scope_Connects[index] ) {
			return;
		}

		var l = 0;
		var h = 100;

		if ( index !== 0 ) {
			l = scope_Locations[index - 1];
		}

		if ( index !== scope_Connects.length - 1 ) {
			h = scope_Locations[index];
		}

		// We use two rules:
		// 'translate' to change the left/top offset;
		// 'scale' to change the width of the element;
		// As the element has a width of 100%, a translation of 100% is equal to 100% of the parent (.noUi-base)
		var connectWidth = h - l;
		var translateRule = 'translate(' + inRuleOrder(toPct(transformDirection(l, connectWidth)), '0') + ')';
		var scaleRule = 'scale(' + inRuleOrder(connectWidth / 100, '1') + ')';

		scope_Connects[index].style[options.transformRule] = translateRule + ' ' + scaleRule;
	}

/*! In this file: All methods eventually exposed in slider.noUiSlider... */

	// Parses value passed to .set method. Returns current value if not parse-able.
	function resolveToValue ( to, handleNumber ) {

		// Setting with null indicates an 'ignore'.
		// Inputting 'false' is invalid.
		if ( to === null || to === false || to === undefined ) {
			return scope_Locations[handleNumber];
		}

		// If a formatted number was passed, attempt to decode it.
		if ( typeof to === 'number' ) {
			to = String(to);
		}

		to = options.format.from(to);
		to = scope_Spectrum.toStepping(to);

		// If parsing the number failed, use the current value.
		if ( to === false || isNaN(to) ) {
			return scope_Locations[handleNumber];
		}

		return to;
	}

	// Set the slider value.
	function valueSet ( input, fireSetEvent ) {

		var values = asArray(input);
		var isInit = scope_Locations[0] === undefined;

		// Event fires by default
		fireSetEvent = (fireSetEvent === undefined ? true : !!fireSetEvent);

		// Animation is optional.
		// Make sure the initial values were set before using animated placement.
		if ( options.animate && !isInit ) {
			addClassFor(scope_Target, options.cssClasses.tap, options.animationDuration);
		}

		// First pass, without lookAhead but with lookBackward. Values are set from left to right.
		scope_HandleNumbers.forEach(function(handleNumber){
			setHandle(handleNumber, resolveToValue(values[handleNumber], handleNumber), true, false);
		});

		// Second pass. Now that all base values are set, apply constraints
		scope_HandleNumbers.forEach(function(handleNumber){
			setHandle(handleNumber, scope_Locations[handleNumber], true, true);
		});

		setZindex();

		scope_HandleNumbers.forEach(function(handleNumber){

			fireEvent('update', handleNumber);

			// Fire the event only for handles that received a new value, as per #579
			if ( values[handleNumber] !== null && fireSetEvent ) {
				fireEvent('set', handleNumber);
			}
		});
	}

	// Reset slider to initial values
	function valueReset ( fireSetEvent ) {
		valueSet(options.start, fireSetEvent);
	}

	// Get the slider value.
	function valueGet ( ) {

		var values = scope_Values.map(options.format.to);

		// If only one handle is used, return a single value.
		if ( values.length === 1 ){
			return values[0];
		}

		return values;
	}

	// Removes classes from the root and empties it.
	function destroy ( ) {

		for ( var key in options.cssClasses ) {
			if ( !options.cssClasses.hasOwnProperty(key) ) { continue; }
			removeClass(scope_Target, options.cssClasses[key]);
		}

		while (scope_Target.firstChild) {
			scope_Target.removeChild(scope_Target.firstChild);
		}

		delete scope_Target.noUiSlider;
	}

	// Get the current step size for the slider.
	function getCurrentStep ( ) {

		// Check all locations, map them to their stepping point.
		// Get the step point, then find it in the input list.
		return scope_Locations.map(function( location, index ){

			var nearbySteps = scope_Spectrum.getNearbySteps( location );
			var value = scope_Values[index];
			var increment = nearbySteps.thisStep.step;
			var decrement = null;

			// If the next value in this step moves into the next step,
			// the increment is the start of the next step - the current value
			if ( increment !== false ) {
				if ( value + increment > nearbySteps.stepAfter.startValue ) {
					increment = nearbySteps.stepAfter.startValue - value;
				}
			}


			// If the value is beyond the starting point
			if ( value > nearbySteps.thisStep.startValue ) {
				decrement = nearbySteps.thisStep.step;
			}

			else if ( nearbySteps.stepBefore.step === false ) {
				decrement = false;
			}

			// If a handle is at the start of a step, it always steps back into the previous step first
			else {
				decrement = value - nearbySteps.stepBefore.highestStep;
			}


			// Now, if at the slider edges, there is not in/decrement
			if ( location === 100 ) {
				increment = null;
			}

			else if ( location === 0 ) {
				decrement = null;
			}

			// As per #391, the comparison for the decrement step can have some rounding issues.
			var stepDecimals = scope_Spectrum.countStepDecimals();

			// Round per #391
			if ( increment !== null && increment !== false ) {
				increment = Number(increment.toFixed(stepDecimals));
			}

			if ( decrement !== null && decrement !== false ) {
				decrement = Number(decrement.toFixed(stepDecimals));
			}

			return [decrement, increment];
		});
	}

	// Updateable: margin, limit, padding, step, range, animate, snap
	function updateOptions ( optionsToUpdate, fireSetEvent ) {

		// Spectrum is created using the range, snap, direction and step options.
		// 'snap' and 'step' can be updated.
		// If 'snap' and 'step' are not passed, they should remain unchanged.
		var v = valueGet();

		var updateAble = ['margin', 'limit', 'padding', 'range', 'animate', 'snap', 'step', 'format'];

		// Only change options that we're actually passed to update.
		updateAble.forEach(function(name){
			if ( optionsToUpdate[name] !== undefined ) {
				originalOptions[name] = optionsToUpdate[name];
			}
		});

		var newOptions = testOptions(originalOptions);

		// Load new options into the slider state
		updateAble.forEach(function(name){
			if ( optionsToUpdate[name] !== undefined ) {
				options[name] = newOptions[name];
			}
		});

		scope_Spectrum = newOptions.spectrum;

		// Limit, margin and padding depend on the spectrum but are stored outside of it. (#677)
		options.margin = newOptions.margin;
		options.limit = newOptions.limit;
		options.padding = newOptions.padding;

		// Update pips, removes existing.
		if ( options.pips ) {
			pips(options.pips);
		}

		// Invalidate the current positioning so valueSet forces an update.
		scope_Locations = [];
		valueSet(optionsToUpdate.start || v, fireSetEvent);
	}

/*! In this file: Calls to functions. All other scope_ files define functions only; */

	// Create the base element, initialize HTML and set classes.
	// Add handles and connect elements.
	addSlider(scope_Target);
	addElements(options.connect, scope_Base);

	// Attach user events.
	bindSliderEvents(options.events);

	// Use the public value method to set the start values.
	valueSet(options.start);

	scope_Self = {
		destroy: destroy,
		steps: getCurrentStep,
		on: bindEvent,
		off: removeEvent,
		get: valueGet,
		set: valueSet,
		reset: valueReset,
		// Exposed for unit testing, don't use this in your application.
		__moveHandles: function(a, b, c) { moveHandles(a, b, scope_Locations, c); },
		options: originalOptions, // Issue #600, #678
		updateOptions: updateOptions,
		target: scope_Target, // Issue #597
		removePips: removePips,
		pips: pips // Issue #594
	};

	if ( options.pips ) {
		pips(options.pips);
	}

	if ( options.tooltips ) {
		tooltips();
	}

	aria();

	return scope_Self;

}


	// Run the standard initializer
	function initialize ( target, originalOptions ) {

		if ( !target || !target.nodeName ) {
			throw new Error("noUiSlider (" + VERSION + "): create requires a single element, got: " + target);
		}

		// Throw an error if the slider was already initialized.
		if ( target.noUiSlider ) {
			throw new Error("noUiSlider (" + VERSION + "): Slider was already initialized.");
		}

		// Test the options and create the slider environment;
		var options = testOptions( originalOptions, target );
		var api = scope( target, options, originalOptions );

		target.noUiSlider = api;

		return api;
	}

	// Use an object instead of a function for future expandability;
	return {
		version: VERSION,
		create: initialize
	};

}));
},{}],3:[function(require,module,exports){
(function (global){

var $ 				= (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null);
var state 			= require('./state');
var process_form 	= require('./process_form');
var noUiSlider		= require('nouislider');
//var cookies         = require('js-cookie');
var thirdParty      = require('./thirdparty');

window.searchAndFilter = {
    extensions: [],
    registerExtension: function( extensionName ) {
        this.extensions.push( extensionName );
    }
};

module.exports = function(options)
{
    var defaults = {
        startOpened: false,
        isInit: true,
        action: ""
    };

    var opts = jQuery.extend(defaults, options);
    
    thirdParty.init();
    
    //loop through each item matched
    this.each(function()
    {

        var $this = $(this);
        var self = this;
        this.sfid = $this.attr("data-sf-form-id");

        state.addSearchForm(this.sfid, this);

        this.$fields = $this.find("> ul > li"); //a reference to each fields parent LI

        this.enable_taxonomy_archives = $this.attr('data-taxonomy-archives');
        this.current_taxonomy_archive = $this.attr('data-current-taxonomy-archive');

        if(typeof(this.enable_taxonomy_archives)=="undefined")
        {
            this.enable_taxonomy_archives = "0";
        }
        if(typeof(this.current_taxonomy_archive)=="undefined")
        {
            this.current_taxonomy_archive = "";
        }

        process_form.init(self.enable_taxonomy_archives, self.current_taxonomy_archive);
        //process_form.setTaxArchiveResultsUrl(self);
        process_form.enableInputs(self);

        if(typeof(this.extra_query_params)=="undefined")
        {
            this.extra_query_params = {all: {}, results: {}, ajax: {}};
        }


        this.template_is_loaded = $this.attr("data-template-loaded");
        this.is_ajax = $this.attr("data-ajax");
        this.instance_number = $this.attr('data-instance-count');
        this.$ajax_results_container = jQuery($this.attr("data-ajax-target"));

        this.ajax_update_sections = $this.attr("data-ajax-update-sections") ? JSON.parse( $this.attr("data-ajax-update-sections") ) : [];
        this.replace_results = $this.attr("data-replace-results") === "0" ? false : true;
        
        this.results_url = $this.attr("data-results-url");
        this.debug_mode = $this.attr("data-debug-mode");
        this.update_ajax_url = $this.attr("data-update-ajax-url");
        this.pagination_type = $this.attr("data-ajax-pagination-type");
        this.auto_count = $this.attr("data-auto-count");
        this.auto_count_refresh_mode = $this.attr("data-auto-count-refresh-mode");
        this.only_results_ajax = $this.attr("data-only-results-ajax"); //if we are not on the results page, redirect rather than try to load via ajax
        this.scroll_to_pos = $this.attr("data-scroll-to-pos");
        this.custom_scroll_to = $this.attr("data-custom-scroll-to");
        this.scroll_on_action = $this.attr("data-scroll-on-action");
        this.lang_code = $this.attr("data-lang-code");
        this.ajax_url = $this.attr('data-ajax-url');
        this.ajax_form_url = $this.attr('data-ajax-form-url');
        this.is_rtl = $this.attr('data-is-rtl');

        this.display_result_method = $this.attr('data-display-result-method');
        this.maintain_state = $this.attr('data-maintain-state');
        this.ajax_action = "";
        this.last_submit_query_params = "";

        this.current_paged = parseInt($this.attr('data-init-paged'));
        this.last_load_more_html = "";
        this.load_more_html = "";
        this.ajax_data_type = $this.attr('data-ajax-data-type');
        this.ajax_target_attr = $this.attr("data-ajax-target");
        this.use_history_api = $this.attr("data-use-history-api");
        this.is_submitting = false;

        this.last_ajax_request = null;

        if(typeof(this.results_html)=="undefined")
        {
            this.results_html = "";
        }

        if(typeof(this.use_history_api)=="undefined")
        {
            this.use_history_api = "";
        }

        if(typeof(this.pagination_type)=="undefined")
        {
            this.pagination_type = "normal";
        }
        if(typeof(this.current_paged)=="undefined")
        {
            this.current_paged = 1;
        }

        if(typeof(this.ajax_target_attr)=="undefined")
        {
            this.ajax_target_attr = "";
        }

        if(typeof(this.ajax_url)=="undefined")
        {
            this.ajax_url = "";
        }

        if(typeof(this.ajax_form_url)=="undefined")
        {
            this.ajax_form_url = "";
        }

        if(typeof(this.results_url)=="undefined")
        {
            this.results_url = "";
        }

        if(typeof(this.scroll_to_pos)=="undefined")
        {
            this.scroll_to_pos = "";
        }

        if(typeof(this.scroll_on_action)=="undefined")
        {
            this.scroll_on_action = "";
        }
        if(typeof(this.custom_scroll_to)=="undefined")
        {
            this.custom_scroll_to = "";
        }
        this.$custom_scroll_to = jQuery(this.custom_scroll_to);

        if(typeof(this.update_ajax_url)=="undefined")
        {
            this.update_ajax_url = "";
        }

        if(typeof(this.debug_mode)=="undefined")
        {
            this.debug_mode = "";
        }

        if(typeof(this.ajax_target_object)=="undefined")
        {
            this.ajax_target_object = "";
        }

        if(typeof(this.template_is_loaded)=="undefined")
        {
            this.template_is_loaded = "0";
        }

        if(typeof(this.auto_count_refresh_mode)=="undefined")
        {
            this.auto_count_refresh_mode = "0";
        }

        this.ajax_links_selector = $this.attr("data-ajax-links-selector");


        this.auto_update = $this.attr("data-auto-update");
        this.inputTimer = 0;

        this.setInfiniteScrollContainer = function()
        {
            // When we navigate away from search results, and then press back,
            // is_max_paged is retained, so we only want to set it to false if
            // we are initalizing the results page the first time - so just 
            // check if this var is undefined (as it should be on first use);
            if ( typeof ( this.is_max_paged ) === 'undefined' ) {
                this.is_max_paged = false; //for load more only, once we detect we're at the end set this to true
            }

            this.use_scroll_loader = $this.attr('data-show-scroll-loader');
            this.infinite_scroll_container = $this.attr('data-infinite-scroll-container');
            this.infinite_scroll_trigger_amount = $this.attr('data-infinite-scroll-trigger');
            this.infinite_scroll_result_class = $this.attr('data-infinite-scroll-result-class');
            this.$infinite_scroll_container = this.$ajax_results_container;

            if(typeof(this.infinite_scroll_container)=="undefined")
            {
                this.infinite_scroll_container = "";
            }
            else
            {
                this.$infinite_scroll_container = jQuery(this.ajax_target_attr + ' ' + this.infinite_scroll_container);
            }

            if(typeof(this.infinite_scroll_result_class)=="undefined")
            {
                this.infinite_scroll_result_class = "";
            }

            if(typeof(this.use_scroll_loader)=="undefined")
            {
                this.use_scroll_loader = 1;
            }

        };
        this.setInfiniteScrollContainer();

        /* functions */

        this.reset = function(submit_form)
        {

            this.resetForm(submit_form);
            return true;
        }

        this.inputUpdate = function(delayDuration)
        {
            if(typeof(delayDuration)=="undefined")
            {
                var delayDuration = 300;
            }

            self.resetTimer(delayDuration);
        }

        this.scrollToPos = function() {
            var offset = 0;
            var canScroll = true;

            if(self.is_ajax==1)
            {
                if(self.scroll_to_pos=="window")
                {
                    offset = 0;

                }
                else if(self.scroll_to_pos=="form")
                {
                    offset = $this.offset().top;
                }
                else if(self.scroll_to_pos=="results")
                {
                    if(self.$ajax_results_container.length>0)
                    {
                        offset = self.$ajax_results_container.offset().top;
                    }
                }
                else if(self.scroll_to_pos=="custom")
                {
                    //custom_scroll_to
                    if(self.$custom_scroll_to.length>0)
                    {
                        offset = self.$custom_scroll_to.offset().top;
                    }
                }
                else
                {
                    canScroll = false;
                }

                if(canScroll)
                {
                    $("html, body").stop().animate({
                        scrollTop: offset
                    }, "normal", "easeOutQuad" );
                }
            }

        };

        this.attachActiveClass = function(){

            //check to see if we are using ajax & auto count
            //if not, the search form does not get reloaded, so we need to update the sf-option-active class on all fields

            $this.on('change', 'input[type="radio"], input[type="checkbox"], select', function(e)
            {
                var $cthis = $(this);
                var $cthis_parent = $cthis.closest("li[data-sf-field-name]");
                var this_tag = $cthis.prop("tagName").toLowerCase();
                var input_type = $cthis.attr("type");
                var parent_tag = $cthis_parent.prop("tagName").toLowerCase();

                if((this_tag=="input")&&((input_type=="radio")||(input_type=="checkbox")) && (parent_tag=="li"))
                {
                    var $all_options = $cthis_parent.parent().find('li');
                    var $all_options_fields = $cthis_parent.parent().find('input:checked');

                    $all_options.removeClass("sf-option-active");
                    $all_options_fields.each(function(){

                        var $parent = $(this).closest("li");
                        $parent.addClass("sf-option-active");

                    });

                }
                else if(this_tag=="select")
                {
                    var $all_options = $cthis.children();
                    $all_options.removeClass("sf-option-active");
                    var this_val = $cthis.val();

                    var this_arr_val = (typeof this_val == 'string' || this_val instanceof String) ? [this_val] : this_val;

                    $(this_arr_val).each(function(i, value){
                        $cthis.find("option[value='"+value+"']").addClass("sf-option-active");
                    });


                }
            });

        };
        this.initAutoUpdateEvents = function(){

            /* auto update */
            if((self.auto_update==1)||(self.auto_count_refresh_mode==1))
            {
                $this.on('change', 'input[type="radio"], input[type="checkbox"], select', function(e) {
                    self.inputUpdate(200);
                });

                $this.on('input', 'input[type="number"]', function(e) {
                    self.inputUpdate(800);
                });

                var $textInput = $this.find('input[type="text"]:not(.sf-datepicker)');
                var lastValue = $textInput.val();

                $this.on('input', 'input[type="text"]:not(.sf-datepicker)', function()
                {
                    if(lastValue!=$textInput.val())
                    {
                        self.inputUpdate(1200);
                    }

                    lastValue = $textInput.val();
                });


                $this.on('keypress', 'input[type="text"]:not(.sf-datepicker)', function(e)
                {
                    if (e.which == 13){

                        e.preventDefault();
                        self.submitForm();
                        return false;
                    }

                });

                //$this.on('input', 'input.sf-datepicker', self.dateInputType);

            }
        };

        //this.initAutoUpdateEvents();


        this.clearTimer = function()
        {
            clearTimeout(self.inputTimer);
        };
        this.resetTimer = function(delayDuration)
        {
            clearTimeout(self.inputTimer);
            self.inputTimer = setTimeout(self.formUpdated, delayDuration);

        };

        this.addDatePickers = function()
        {
            var $date_picker = $this.find(".sf-datepicker");

            if($date_picker.length>0)
            {
                $date_picker.each(function(){

                    var $this = $(this);
                    var dateFormat = "";
                    var dateDropdownYear = false;
                    var dateDropdownMonth = false;

                    var $closest_date_wrap = $this.closest(".sf_date_field");
                    if($closest_date_wrap.length>0)
                    {
                        dateFormat = $closest_date_wrap.attr("data-date-format");

                        if($closest_date_wrap.attr("data-date-use-year-dropdown")==1)
                        {
                            dateDropdownYear = true;
                        }
                        if($closest_date_wrap.attr("data-date-use-month-dropdown")==1)
                        {
                            dateDropdownMonth = true;
                        }
                    }

                    var datePickerOptions = {
                        inline: true,
                        showOtherMonths: true,
                        onSelect: function(e, from_field){ self.dateSelect(e, from_field, $(this)); },
                        dateFormat: dateFormat,

                        changeMonth: dateDropdownMonth,
                        changeYear: dateDropdownYear
                    };

                    if(self.is_rtl==1)
                    {
                        datePickerOptions.direction = "rtl";
                    }

                    $this.datepicker(datePickerOptions);

                    if(self.lang_code!="")
                    {
                        $.datepicker.setDefaults(
                            $.extend(
                                {'dateFormat':dateFormat},
                                $.datepicker.regional[ self.lang_code]
                            )
                        );

                    }
                    else
                    {
                        $.datepicker.setDefaults(
                            $.extend(
                                {'dateFormat':dateFormat},
                                $.datepicker.regional["en"]
                            )
                        );

                    }

                });

                if($('.ll-skin-melon').length==0){

                    $date_picker.datepicker('widget').wrap('<div class="ll-skin-melon searchandfilter-date-picker"/>');
                }

            }
        };

        this.dateSelect = function(e, from_field, $this)
        {
            var $input_field = $(from_field.input.get(0));
            var $this = $(this);

            var $date_fields = $input_field.closest('[data-sf-field-input-type="daterange"], [data-sf-field-input-type="date"]');
            $date_fields.each(function(e, index){
                
                var $tf_date_pickers = $(this).find(".sf-datepicker");
                var no_date_pickers = $tf_date_pickers.length;
                
                if(no_date_pickers>1)
                {
                    //then it is a date range, so make sure both fields are filled before updating
                    var dp_counter = 0;
                    var dp_empty_field_count = 0;
                    $tf_date_pickers.each(function(){

                        if($(this).val()=="")
                        {
                            dp_empty_field_count++;
                        }

                        dp_counter++;
                    });

                    if(dp_empty_field_count==0)
                    {
                        self.inputUpdate(1);
                    }
                }
                else
                {
                    self.inputUpdate(1);
                }

            });
        };

        this.addRangeSliders = function()
        {
            var $meta_range = $this.find(".sf-meta-range-slider");

            if($meta_range.length>0)
            {
                $meta_range.each(function(){

                    var $this = $(this);
                    var min = $this.attr("data-min");
                    var max = $this.attr("data-max");
                    var smin = $this.attr("data-start-min");
                    var smax = $this.attr("data-start-max");
                    var display_value_as = $this.attr("data-display-values-as");
                    var step = $this.attr("data-step");
                    var $start_val = $this.find('.sf-range-min');
                    var $end_val = $this.find('.sf-range-max');


                    var decimal_places = $this.attr("data-decimal-places");
                    var thousand_seperator = $this.attr("data-thousand-seperator");
                    var decimal_seperator = $this.attr("data-decimal-seperator");

                    var field_format = wNumb({
                        mark: decimal_seperator,
                        decimals: parseFloat(decimal_places),
                        thousand: thousand_seperator
                    });



                    var min_unformatted = parseFloat(smin);
                    var min_formatted = field_format.to(parseFloat(smin));
                    var max_formatted = field_format.to(parseFloat(smax));
                    var max_unformatted = parseFloat(smax);
                    //alert(min_formatted);
                    //alert(max_formatted);
                    //alert(display_value_as);


                    if(display_value_as=="textinput")
                    {
                        $start_val.val(min_formatted);
                        $end_val.val(max_formatted);
                    }
                    else if(display_value_as=="text")
                    {
                        $start_val.html(min_formatted);
                        $end_val.html(max_formatted);
                    }


                    var noUIOptions = {
                        range: {
                            'min': [ parseFloat(min) ],
                            'max': [ parseFloat(max) ]
                        },
                        start: [min_formatted, max_formatted],
                        handles: 2,
                        connect: true,
                        step: parseFloat(step),

                        behaviour: 'extend-tap',
                        format: field_format
                    };



                    if(self.is_rtl==1)
                    {
                        noUIOptions.direction = "rtl";
                    }

                    var slider_object = $(this).find(".meta-slider")[0];

                    if( "undefined" !== typeof( slider_object.noUiSlider ) ) {
                        //destroy if it exists.. this means somehow another instance had initialised it..
                        slider_object.noUiSlider.destroy();
                    }

                    noUiSlider.create(slider_object, noUIOptions);

                    $start_val.off();
                    $start_val.on('change', function(){
                        slider_object.noUiSlider.set([$(this).val(), null]);
                    });

                    $end_val.off();
                    $end_val.on('change', function(){
                        slider_object.noUiSlider.set([null, $(this).val()]);
                    });

                    //$start_val.html(min_formatted);
                    //$end_val.html(max_formatted);

                    slider_object.noUiSlider.off('update');
                    slider_object.noUiSlider.on('update', function( values, handle ) {

                        var slider_start_val  = min_formatted;
                        var slider_end_val  = max_formatted;

                        var value = values[handle];


                        if ( handle ) {
                            max_formatted = value;
                        } else {
                            min_formatted = value;
                        }

                        if(display_value_as=="textinput")
                        {
                            $start_val.val(min_formatted);
                            $end_val.val(max_formatted);
                        }
                        else if(display_value_as=="text")
                        {
                            $start_val.html(min_formatted);
                            $end_val.html(max_formatted);
                        }


                        //i think the function that builds the URL needs to decode the formatted string before adding to the url
                        if((self.auto_update==1)||(self.auto_count_refresh_mode==1))
                        {
                            //only try to update if the values have actually changed
                            if((slider_start_val!=min_formatted)||(slider_end_val!=max_formatted)) {

                                self.inputUpdate(800);
                            }


                        }

                    });

                });

                self.clearTimer(); //ignore any changes recently made by the slider (this was just init shouldn't count as an update event)
            }
        };

        this.init = function(keep_pagination)
        {
            if(typeof(keep_pagination)=="undefined")
            {
                var keep_pagination = false;
            }

            this.initAutoUpdateEvents();
            this.attachActiveClass();

            this.addDatePickers();
            this.addRangeSliders();

            //init combo boxes
            var $combobox = $this.find("select[data-combobox='1']");

            if($combobox.length>0)
            {
                $combobox.each(function(index ){
                    var $thiscb = $( this );
                    var nrm = $thiscb.attr("data-combobox-nrm");

                    if (typeof $thiscb.chosen != "undefined")
                    {
                        var chosenoptions = {
                            search_contains: true
                        };

                        if((typeof(nrm)!=="undefined")&&(nrm)){
                            chosenoptions.no_results_text = nrm;
                        }
                        // safe to use the function
                        //search_contains
                        if(self.is_rtl==1)
                        {
                            $thiscb.addClass("chosen-rtl");
                        }

                        $thiscb.chosen(chosenoptions);
                    }
                    else
                    {

                        var select2options = {};

                        if(self.is_rtl==1)
                        {
                            select2options.dir = "rtl";
                        }
                        if((typeof(nrm)!=="undefined")&&(nrm)){
                            select2options.language= {
                                "noResults": function(){
                                    return nrm;
                                }
                            };
                        }

                        $thiscb.select2(select2options);
                    }

                });


            }

            self.isSubmitting = false;

            //if ajax is enabled init the pagination
            if(self.is_ajax==1)
            {
                self.setupAjaxPagination();
            }

            $this.on("submit", this.submitForm);

            self.initWooCommerceControls(); //woocommerce orderby

            if(keep_pagination==false)
            {
                self.last_submit_query_params = self.getUrlParams(false);
            }
        }

        this.onWindowScroll = function(event)
        {
            if((!self.is_loading_more) && (!self.is_max_paged))
            {
                var window_scroll = $(window).scrollTop();
                var window_scroll_bottom = $(window).scrollTop() + $(window).height();
                var scroll_offset = parseInt(self.infinite_scroll_trigger_amount);

                if(self.$infinite_scroll_container.length==1)
                {
                    var results_scroll_bottom = self.$infinite_scroll_container.offset().top + self.$infinite_scroll_container.height();

                    var offset = (self.$infinite_scroll_container.offset().top + self.$infinite_scroll_container.height()) - window_scroll;

                    if(window_scroll_bottom > results_scroll_bottom + scroll_offset)
                    {
                        self.loadMoreResults();
                    }
                    else
                    {//dont load more

                    }
                }
            }
        }

        this.stripQueryStringAndHashFromPath = function(url) {
            return url.split("?")[0].split("#")[0];
        }

        this.gup = function( name, url ) {
            if (!url) url = location.href
            name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]"+name+"=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( url );
            return results == null ? null : results[1];
        };


        this.getUrlParams = function(keep_pagination, type, exclude)
        {
            if(typeof(keep_pagination)=="undefined")
            {
                var keep_pagination = true;
            }

            if(typeof(type)=="undefined")
            {
                var type = "";
            }

            var url_params_str = "";

            // get all params from fields
            var url_params_array = process_form.getUrlParams(self);

            var length = Object.keys(url_params_array).length;
            var count = 0;

            if(typeof(exclude)!="undefined") {
                if (url_params_array.hasOwnProperty(exclude)) {
                    length--;
                }
            }

            if(length>0)
            {
                for (var k in url_params_array) {
                    if (url_params_array.hasOwnProperty(k)) {

                        var can_add = true;
                        if(typeof(exclude)!="undefined")
                        {
                            if(k==exclude) {
                                can_add = false;
                            }
                        }

                        if(can_add) {
                            url_params_str += k + "=" + url_params_array[k];

                            if (count < length - 1) {
                                url_params_str += "&";
                            }

                            count++;
                        }
                    }
                }
            }

            var query_params = "";

            //form params as url query string
            var form_params = url_params_str;

            //get url params from the form itself (what the user has selected)
            query_params = self.joinUrlParam(query_params, form_params);

            //add pagination
            if(keep_pagination==true)
            {
                var pageNumber = self.$ajax_results_container.attr("data-paged");

                if(typeof(pageNumber)=="undefined")
                {
                    pageNumber = 1;
                }

                if(pageNumber>1)
                {
                    query_params = self.joinUrlParam(query_params, "sf_paged="+pageNumber);
                }
            }

            //add sfid
            //query_params = self.joinUrlParam(query_params, "sfid="+self.sfid);

            // loop through any extra params (from ext plugins) and add to the url (ie woocommerce `orderby`)
            /*var extra_query_param = "";
             var length = Object.keys(self.extra_query_params).length;
             var count = 0;

             if(length>0)
             {

             for (var k in self.extra_query_params) {
             if (self.extra_query_params.hasOwnProperty(k)) {

             if(self.extra_query_params[k]!="")
             {
             extra_query_param = k+"="+self.extra_query_params[k];
             query_params = self.joinUrlParam(query_params, extra_query_param);
             }
             */
            query_params = self.addQueryParams(query_params, self.extra_query_params.all);

            if(type!="")
            {
                //query_params = self.addQueryParams(query_params, self.extra_query_params[type]);
            }

            return query_params;
        }
        this.addQueryParams = function(query_params, new_params)
        {
            var extra_query_param = "";
            var length = Object.keys(new_params).length;
            var count = 0;

            if(length>0)
            {

                for (var k in new_params) {
                    if (new_params.hasOwnProperty(k)) {

                        if(new_params[k]!="")
                        {
                            extra_query_param = k+"="+new_params[k];
                            query_params = self.joinUrlParam(query_params, extra_query_param);
                        }
                    }
                }
            }

            return query_params;
        }
        this.addUrlParam = function(url, string)
        {
            var add_params = "";

            if(url!="")
            {
                if(url.indexOf("?") != -1)
                {
                    add_params += "&";
                }
                else
                {
                    //url = this.trailingSlashIt(url);
                    add_params += "?";
                }
            }

            if(string!="")
            {

                return url + add_params + string;
            }
            else
            {
                return url;
            }
        };

        this.joinUrlParam = function(params, string)
        {
            var add_params = "";

            if(params!="")
            {
                add_params += "&";
            }

            if(string!="")
            {

                return params + add_params + string;
            }
            else
            {
                return params;
            }
        };

        this.setAjaxResultsURLs = function(query_params)
        {
            if(typeof(self.ajax_results_conf)=="undefined")
            {
                self.ajax_results_conf = new Array();
            }

            self.ajax_results_conf['processing_url'] = "";
            self.ajax_results_conf['results_url'] = "";
            self.ajax_results_conf['data_type'] = "";

            //if(self.ajax_url!="")
            if(self.display_result_method=="shortcode")
            {//then we want to do a request to the ajax endpoint
                self.ajax_results_conf['results_url'] = self.addUrlParam(self.results_url, query_params);

                //add lang code to ajax api request, lang code should already be in there for other requests (ie, supplied in the Results URL)

                if(self.lang_code!="")
                {
                    //so add it
                    query_params = self.joinUrlParam(query_params, "lang="+self.lang_code);
                }

                self.ajax_results_conf['processing_url'] = self.addUrlParam(self.ajax_url, query_params);
                //self.ajax_results_conf['data_type'] = 'json';

            }
            else if(self.display_result_method=="post_type_archive")
            {
                process_form.setTaxArchiveResultsUrl(self, self.results_url);
                var results_url = process_form.getResultsUrl(self, self.results_url);

                self.ajax_results_conf['results_url'] = self.addUrlParam(results_url, query_params);
                self.ajax_results_conf['processing_url'] = self.addUrlParam(results_url, query_params);

            }
            else if(self.display_result_method=="custom_woocommerce_store")
            {
                process_form.setTaxArchiveResultsUrl(self, self.results_url);
                var results_url = process_form.getResultsUrl(self, self.results_url);

                self.ajax_results_conf['results_url'] = self.addUrlParam(results_url, query_params);
                self.ajax_results_conf['processing_url'] = self.addUrlParam(results_url, query_params);

            }
            else
            {//otherwise we want to pull the results directly from the results page
                self.ajax_results_conf['results_url'] = self.addUrlParam(self.results_url, query_params);
                self.ajax_results_conf['processing_url'] = self.addUrlParam(self.ajax_url, query_params);
                //self.ajax_results_conf['data_type'] = 'html';
            }

            self.ajax_results_conf['processing_url'] = self.addQueryParams(self.ajax_results_conf['processing_url'], self.extra_query_params['ajax']);

            self.ajax_results_conf['data_type'] = self.ajax_data_type;
        };



        this.updateLoaderTag = function($object) {

            var $parent;

            if(self.infinite_scroll_result_class!="")
            {
                $parent = self.$infinite_scroll_container.find(self.infinite_scroll_result_class).last().parent();
            }
            else
            {
                $parent = self.$infinite_scroll_container;
            }

            var tagName = $parent.prop("tagName");

            var tagType = 'div';
            if( ( tagName.toLowerCase() == 'ol' ) || ( tagName.toLowerCase() == 'ul' ) ){
                tagType = 'li';
            }

            var $new = $('<'+tagType+' />').html($object.html());
            var attributes = $object.prop("attributes");

            // loop through <select> attributes and apply them on <div>
            $.each(attributes, function() {
                $new.attr(this.name, this.value);
            });

            return $new;

        }


        this.loadMoreResults = function()
        {
            if ( this.is_max_paged === true ) {
                return;
            }
            self.is_loading_more = true;

            //trigger start event
            var event_data = {
                sfid: self.sfid,
                targetSelector: self.ajax_target_attr,
                type: "load_more",
                object: self
            };

            self.triggerEvent("sf:ajaxstart", event_data);
            process_form.setTaxArchiveResultsUrl(self, self.results_url);
            var query_params = self.getUrlParams(true);
            self.last_submit_query_params = self.getUrlParams(false); //grab a copy of hte URL params without pagination already added

            var ajax_processing_url = "";
            var ajax_results_url = "";
            var data_type = "";


            //now add the new pagination
            var next_paged_number = this.current_paged + 1;
            query_params = self.joinUrlParam(query_params, "sf_paged="+next_paged_number);

            self.setAjaxResultsURLs(query_params);
            ajax_processing_url = self.ajax_results_conf['processing_url'];
            ajax_results_url = self.ajax_results_conf['results_url'];
            data_type = self.ajax_results_conf['data_type'];

            //abort any previous ajax requests
            if(self.last_ajax_request)
            {
                self.last_ajax_request.abort();
            }

            if(self.use_scroll_loader==1)
            {
                var $loader = $('<div/>',{
                    'class': 'search-filter-scroll-loading'
                });//.appendTo(self.$ajax_results_container);

                $loader = self.updateLoaderTag($loader);

                self.infiniteScrollAppend($loader);
            }
            self.last_ajax_request = $.get(ajax_processing_url, function(data, status, request)
            {
                self.current_paged++;
                self.last_ajax_request = null;

                // **************
                // TODO - PASTE THIS AND WATCH THE REDIRECT - ONLY HAPPENS WITH WC (CPT AND TAX DOES NOT)
                // https://search-filter.test/product-category/clothing/tshirts/page/3/?sf_paged=3

                //updates the resutls & form html
                self.addResults(data, data_type);

            }, data_type).fail(function(jqXHR, textStatus, errorThrown)
            {
                var data = {};
                data.sfid = self.sfid;
                data.object = self;
                data.targetSelector = self.ajax_target_attr;
                data.ajaxURL = ajax_processing_url;
                data.jqXHR = jqXHR;
                data.textStatus = textStatus;
                data.errorThrown = errorThrown;
                self.triggerEvent("sf:ajaxerror", data);

            }).always(function()
            {
                var data = {};
                data.sfid = self.sfid;
                data.targetSelector = self.ajax_target_attr;
                data.object = self;

                if(self.use_scroll_loader==1)
                {
                    $loader.detach();
                }

                self.is_loading_more = false;

                self.triggerEvent("sf:ajaxfinish", data);
            });

        }
        this.fetchAjaxResults = function()
        {
            //trigger start event
            var event_data = {
                sfid: self.sfid,
                targetSelector: self.ajax_target_attr,
                type: "load_results",
                object: self
            };

            self.triggerEvent("sf:ajaxstart", event_data);

            //refocus any input fields after the form has been updated
            var $last_active_input_text = $this.find('input[type="text"]:focus').not(".sf-datepicker");
            if($last_active_input_text.length==1)
            {
                var last_active_input_text = $last_active_input_text.attr("name");
            }

            $this.addClass("search-filter-disabled");
            process_form.disableInputs(self);

            //fade out results
            self.$ajax_results_container.animate({ opacity: 0.5 }, "fast"); //loading
            self.fadeContentAreas( "out" );

            if(self.ajax_action=="pagination")
            {
                //need to remove active filter from URL

                //query_params = self.last_submit_query_params;

                //now add the new pagination
                var pageNumber = self.$ajax_results_container.attr("data-paged");

                if(typeof(pageNumber)=="undefined")
                {
                    pageNumber = 1;
                }
                process_form.setTaxArchiveResultsUrl(self, self.results_url);
                query_params = self.getUrlParams(false);

                if(pageNumber>1)
                {
                    query_params = self.joinUrlParam(query_params, "sf_paged="+pageNumber);
                }

            }
            else if(self.ajax_action=="submit")
            {
                var query_params = self.getUrlParams(true);
                self.last_submit_query_params = self.getUrlParams(false); //grab a copy of hte URL params without pagination already added
            }

            var ajax_processing_url = "";
            var ajax_results_url = "";
            var data_type = "";

            self.setAjaxResultsURLs(query_params);
            ajax_processing_url = self.ajax_results_conf['processing_url'];
            ajax_results_url = self.ajax_results_conf['results_url'];
            data_type = self.ajax_results_conf['data_type'];


            //abort any previous ajax requests
            if(self.last_ajax_request)
            {
                self.last_ajax_request.abort();
            }
            var ajax_action = self.ajax_action;
            self.last_ajax_request = $.get(ajax_processing_url, function(data, status, request)
            {
                self.last_ajax_request = null;

                //updates the resutls & form html
                self.updateResults(data, data_type);

                // scroll 
                // set the var back to what it was before the ajax request nad the form re-init
                self.ajax_action = ajax_action;
                self.scrollResults( self.ajax_action );

                /* update URL */
                //update url before pagination, because we need to do some checks agains the URL for infinite scroll
                self.updateUrlHistory(ajax_results_url);

                //setup pagination
                self.setupAjaxPagination();

                self.isSubmitting = false;

                /* user def */
                self.initWooCommerceControls(); //woocommerce orderby


            }, data_type).fail(function(jqXHR, textStatus, errorThrown)
            {
                var data = {};
                data.sfid = self.sfid;
                data.targetSelector = self.ajax_target_attr;
                data.object = self;
                data.ajaxURL = ajax_processing_url;
                data.jqXHR = jqXHR;
                data.textStatus = textStatus;
                data.errorThrown = errorThrown;
                self.isSubmitting = false;
                self.triggerEvent("sf:ajaxerror", data);

            }).always(function()
            {
                self.$ajax_results_container.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading
                self.fadeContentAreas( "in" );
                var data = {};
                data.sfid = self.sfid;
                data.targetSelector = self.ajax_target_attr;
                data.object = self;
                $this.removeClass("search-filter-disabled");
                process_form.enableInputs(self);

                //refocus the last active text field
                if(last_active_input_text!="")
                {
                    var $input = [];
                    self.$fields.each(function(){

                        var $active_input = $(this).find("input[name='"+last_active_input_text+"']");
                        if($active_input.length==1)
                        {
                            $input = $active_input;
                        }

                    });
                    if($input.length==1) {

                        $input.focus().val($input.val());
                        self.focusCampo($input[0]);
                    }
                }

                $this.find("input[name='_sf_search']").trigger('focus');
                self.triggerEvent("sf:ajaxfinish",  data );

            });
        };

        this.focusCampo = function(inputField){
            //var inputField = document.getElementById(id);
            if (inputField != null && inputField.value.length != 0){
                if (inputField.createTextRange){
                    var FieldRange = inputField.createTextRange();
                    FieldRange.moveStart('character',inputField.value.length);
                    FieldRange.collapse();
                    FieldRange.select();
                }else if (inputField.selectionStart || inputField.selectionStart == '0') {
                    var elemLen = inputField.value.length;
                    inputField.selectionStart = elemLen;
                    inputField.selectionEnd = elemLen;
                }
                inputField.blur();
                inputField.focus();
            } else{
                if ( inputField ) {
                    inputField.focus();
                }
                
            }
        }

        this.triggerEvent = function(eventname, data)
        {
            var $event_container = $(".searchandfilter[data-sf-form-id='"+self.sfid+"']");
            $event_container.trigger(eventname, [ data ]);
        }

        this.fetchAjaxForm = function()
        {
            //trigger start event
            var event_data = {
                sfid: self.sfid,
                targetSelector: self.ajax_target_attr,
                type: "form",
                object: self
            };

            self.triggerEvent("sf:ajaxformstart", [ event_data ]);

            $this.addClass("search-filter-disabled");
            process_form.disableInputs(self);

            var query_params = self.getUrlParams();

            if(self.lang_code!="")
            {
                //so add it
                query_params = self.joinUrlParam(query_params, "lang="+self.lang_code);
            }

            var ajax_processing_url = self.addUrlParam(self.ajax_form_url, query_params);
            var data_type = "json";


            //abort any previous ajax requests
            /*if(self.last_ajax_request)
             {
             self.last_ajax_request.abort();
             }*/


            //self.last_ajax_request =

            $.get(ajax_processing_url, function(data, status, request)
            {
                //self.last_ajax_request = null;

                //updates the resutls & form html
                self.updateForm(data, data_type);


            }, data_type).fail(function(jqXHR, textStatus, errorThrown)
            {
                var data = {};
                data.sfid = self.sfid;
                data.targetSelector = self.ajax_target_attr;
                data.object = self;
                data.ajaxURL = ajax_processing_url;
                data.jqXHR = jqXHR;
                data.textStatus = textStatus;
                data.errorThrown = errorThrown;
                self.triggerEvent("sf:ajaxerror", [ data ]);

            }).always(function()
            {
                var data = {};
                data.sfid = self.sfid;
                data.targetSelector = self.ajax_target_attr;
                data.object = self;

                $this.removeClass("search-filter-disabled");
                process_form.enableInputs(self);

                self.triggerEvent("sf:ajaxformfinish", [ data ]);
            });
        };

        this.copyListItemsContents = function($list_from, $list_to)
        {
            //copy over child list items
            var li_contents_array = new Array();
            var from_attributes = new Array();

            var $from_fields = $list_from.find("> ul > li");

            $from_fields.each(function(i){

                li_contents_array.push($(this).html());

                var attributes = $(this).prop("attributes");
                from_attributes.push(attributes);

                //var field_name = $(this).attr("data-sf-field-name");
                //var to_field = $list_to.find("> ul > li[data-sf-field-name='"+field_name+"']");

                //self.copyAttributes($(this), $list_to, "data-sf-");

            });

            var li_it = 0;
            var $to_fields = $list_to.find("> ul > li");
            $to_fields.each(function(i){
                $(this).html(li_contents_array[li_it]);

                var $from_field = $($from_fields.get(li_it));

                var $to_field = $(this);
                $to_field.removeAttr("data-sf-taxonomy-archive");
                self.copyAttributes($from_field, $to_field);

                li_it++;
            });

            /*var $from_fields = $list_from.find(" ul > li");
             var $to_fields = $list_to.find(" > li");
             $from_fields.each(function(index, val){
             if($(this).hasAttribute("data-sf-taxonomy-archive"))
             {

             }
             });

             this.copyAttributes($list_from, $list_to);*/
        }

        this.updateFormAttributes = function($list_from, $list_to)
        {
            var from_attributes = $list_from.prop("attributes");
            // loop through <select> attributes and apply them on <div>

            var to_attributes = $list_to.prop("attributes");
            $.each(to_attributes, function() {
                $list_to.removeAttr(this.name);
            });

            $.each(from_attributes, function() {
                $list_to.attr(this.name, this.value);
            });

        }

        this.copyAttributes = function($from, $to, prefix)
        {
            if(typeof(prefix)=="undefined")
            {
                var prefix = "";
            }

            var from_attributes = $from.prop("attributes");

            var to_attributes = $to.prop("attributes");
            $.each(to_attributes, function() {

                if(prefix!="") {
                    if (this.name.indexOf(prefix) == 0) {
                        $to.removeAttr(this.name);
                    }
                }
                else
                {
                    //$to.removeAttr(this.name);
                }
            });

            $.each(from_attributes, function() {
                $to.attr(this.name, this.value);
            });
        }

        this.copyFormAttributes = function($from, $to)
        {
            $to.removeAttr("data-current-taxonomy-archive");
            this.copyAttributes($from, $to);

        }

        this.updateForm = function(data, data_type)
        {
            if(data_type=="json")
            {//then we did a request to the ajax endpoint, so expect an object back

                if(typeof(data['form'])!=="undefined")
                {
                    //remove all events from S&F form
                    $this.off();

                    //refresh the form (auto count)
                    self.copyListItemsContents($(data['form']), $this);

                    //re init S&F class on the form
                    //$this.searchAndFilter();

                    //if ajax is enabled init the pagination

                    this.init(true);

                    if(self.is_ajax==1)
                    {
                        self.setupAjaxPagination();
                    }



                }
            }


        }
        this.addResults = function(data, data_type)
        {
            if(data_type=="json")
            {//then we did a request to the ajax endpoint, so expect an object back
                //grab the results and load in
                //self.$ajax_results_container.append(data['results']);
                self.load_more_html = data['results'];
            }
            else if(data_type=="html")
            {//we are expecting the html of the results page back, so extract the html we need
                var $data_obj = $(data);
                self.load_more_html = $data_obj.find(self.ajax_target_attr).html();
            }

            console.log("NEW HTML", self.load_more_html);

            var infinite_scroll_end = false;

            if($("<div>"+self.load_more_html+"</div>").find("[data-search-filter-action='infinite-scroll-end']").length>0)
            {
                infinite_scroll_end = true;
            }

            //if there is another selector for infinite scroll, find the contents of that instead
            if(self.infinite_scroll_container!="")
            {
                self.load_more_html = $("<div>"+self.load_more_html+"</div>").find(self.infinite_scroll_container).html();
            }
            if(self.infinite_scroll_result_class!="")
            {
                var $result_items = $("<div>"+self.load_more_html+"</div>").find(self.infinite_scroll_result_class);
                var $result_items_container = $('<div/>', {});
                $result_items_container.append($result_items);

                self.load_more_html = $result_items_container.html();
            }

            if(infinite_scroll_end)
            {//we found a data attribute signalling the last page so finish here

                self.is_max_paged = true;
                self.last_load_more_html = self.load_more_html;

                self.infiniteScrollAppend(self.load_more_html);

            }
            else if(self.last_load_more_html!==self.load_more_html)
            {
                //check to make sure the new html fetched is different
                self.last_load_more_html = self.load_more_html;
                self.infiniteScrollAppend(self.load_more_html);

            }
            else
            {//we received the same message again so don't add, and tell S&F that we're at the end..
                self.is_max_paged = true;
            }
        }


        this.infiniteScrollAppend = function($object)
        {
            if(self.infinite_scroll_result_class!="")
            {
                self.$infinite_scroll_container.find(self.infinite_scroll_result_class).last().after($object);
            }
            else
            {
               self.$infinite_scroll_container.append($object);
            }
        }


        this.updateResults = function(data, data_type)
        {
            if(data_type=="json")
            {//then we did a request to the ajax endpoint, so expect an object back
                //grab the results and load in
                this.results_html = data['results'];

                if ( this.replace_results ) {
                    self.$ajax_results_container.html(this.results_html);
                }

                if(typeof(data['form'])!=="undefined")
                {
                    //remove all events from S&F form
                    $this.off();

                    //remove pagination
                    self.removeAjaxPagination();

                    //refresh the form (auto count)
                    self.copyListItemsContents($(data['form']), $this);

                    //update attributes on form
                    self.copyFormAttributes($(data['form']), $this);

                    //re init S&F class on the form
                    $this.searchAndFilter({'isInit': false});
                }
                else
                {
                    //$this.find("input").removeAttr("disabled");
                }
            }
            else if(data_type=="html") {//we are expecting the html of the results page back, so extract the html we need

                var $data_obj = $(data);

                this.results_html = $data_obj.find( this.ajax_target_attr ).html();

                if ( this.replace_results ) {
                    self.$ajax_results_container.html(this.results_html);
                }

                self.updateContentAreas( $data_obj );

                if (self.$ajax_results_container.find(".searchandfilter").length > 0)
                {//then there are search form(s) inside the results container, so re-init them

                    self.$ajax_results_container.find(".searchandfilter").searchAndFilter();
                }

                //if the current search form is not inside the results container, then proceed as normal and update the form
                if(self.$ajax_results_container.find(".searchandfilter[data-sf-form-id='" + self.sfid + "']").length==0) {

                    var $new_search_form = $data_obj.find(".searchandfilter[data-sf-form-id='" + self.sfid + "']");

                    if ($new_search_form.length == 1) {//then replace the search form with the new one

                        //remove all events from S&F form
                        $this.off();

                        //remove pagination
                        self.removeAjaxPagination();

                        //refresh the form (auto count)
                        self.copyListItemsContents($new_search_form, $this);

                        //update attributes on form
                        self.copyFormAttributes($new_search_form, $this);

                        //re init S&F class on the form
                        $this.searchAndFilter({'isInit': false});

                    }
                    else {

                        //$this.find("input").removeAttr("disabled");
                    }
                }
            }

            self.is_max_paged = false; //for infinite scroll
            self.current_paged = 1; //for infinite scroll
            self.setInfiniteScrollContainer();

        }

        this.updateContentAreas = function( $html_data ) {
            
            // add additional content areas
            if ( this.ajax_update_sections && this.ajax_update_sections.length ) {
                for (index = 0; index < this.ajax_update_sections.length; ++index) {
                    var selector = this.ajax_update_sections[index];
                    $( selector ).html( $html_data.find( selector ).html() );
                }
            }
        }
        this.fadeContentAreas = function( direction ) {
            
            var opacity = 0.5;
            if ( direction === "in" ) {
                opacity = 1;
            }

            if ( this.ajax_update_sections && this.ajax_update_sections.length ) {
                for (index = 0; index < this.ajax_update_sections.length; ++index) {
                    var selector = this.ajax_update_sections[index];
                    $( selector ).stop(true,true).animate( { opacity: opacity}, "fast" );
                }
            }
           
            
        }

        this.removeWooCommerceControls = function(){
            var $woo_orderby = $('.woocommerce-ordering .orderby');
            var $woo_orderby_form = $('.woocommerce-ordering');

            $woo_orderby_form.off();
            $woo_orderby.off();
        };

        this.addQueryParam = function(name, value, url_type){

            if(typeof(url_type)=="undefined")
            {
                var url_type = "all";
            }
            self.extra_query_params[url_type][name] = value;

        };

        this.initWooCommerceControls = function(){

            self.removeWooCommerceControls();

            var $woo_orderby = $('.woocommerce-ordering .orderby');
            var $woo_orderby_form = $('.woocommerce-ordering');

            var order_val = "";
            if($woo_orderby.length>0)
            {
                order_val = $woo_orderby.val();
            }
            else
            {
                order_val = self.getQueryParamFromURL("orderby", window.location.href);
            }

            if(order_val=="menu_order")
            {
                order_val = "";
            }

            if((order_val!="")&&(!!order_val))
            {
                self.extra_query_params.all.orderby = order_val;
            }


            $woo_orderby_form.on('submit', function(e)
            {
                e.preventDefault();
                //var form = e.target;
                return false;
            });

            $woo_orderby.on("change", function(e)
            {
                e.preventDefault();

                var val = $(this).val();
                if(val=="menu_order")
                {
                    val = "";
                }

                self.extra_query_params.all.orderby = val;

                $this.trigger("submit")

                return false;
            });

        }

        this.scrollResults = function()
        {
            if((self.scroll_on_action==self.ajax_action)||(self.scroll_on_action=="all"))
            {
                self.scrollToPos(); //scroll the window if it has been set
                //self.ajax_action = "";
            }
        }

        this.updateUrlHistory = function(ajax_results_url)
        {
            var use_history_api = 0;
            if (window.history && window.history.pushState)
            {
                use_history_api = $this.attr("data-use-history-api");
            }

            if((self.update_ajax_url==1)&&(use_history_api==1))
            {
                //now check if the browser supports history state push :)
                if (window.history && window.history.pushState)
                {
                    history.pushState(null, null, ajax_results_url);
                }
            }
        }
        this.removeAjaxPagination = function()
        {
            if(typeof(self.ajax_links_selector)!="undefined")
            {
                var $ajax_links_object = jQuery(self.ajax_links_selector);

                if($ajax_links_object.length>0)
                {
                    $ajax_links_object.off();
                }
            }
        }

        this.getBaseUrl = function( url ) {
            //now see if we are on the URL we think...
            var url_parts = url.split("?");
            var url_base = "";

            if(url_parts.length>0)
            {
                url_base = url_parts[0];
            }
            else {
                url_base = url;
            }
            return url_base;
        }
        this.canFetchAjaxResults = function(fetch_type)
        {
            if(typeof(fetch_type)=="undefined")
            {
                var fetch_type = "";
            }

            var fetch_ajax_results = false;

            if(self.is_ajax==1)
            {//then we will ajax submit the form

                //and if we can find the results container
                if(self.$ajax_results_container.length==1)
                {
                    fetch_ajax_results = true;
                }

                var results_url = self.results_url;  //
                var results_url_encoded = '';  //
                var current_url = window.location.href;

                //ignore # and everything after
                var hash_pos = window.location.href.indexOf('#');
                if(hash_pos!==-1){
                    current_url = window.location.href.substr(0, window.location.href.indexOf('#'));
                }

                if( ( ( self.display_result_method=="custom_woocommerce_store" ) || ( self.display_result_method=="post_type_archive" ) ) && ( self.enable_taxonomy_archives == 1 ) )
                {
                    if( self.current_taxonomy_archive !=="" )
                    {
                        fetch_ajax_results = true;
                        return fetch_ajax_results;
                    }

                    /*var results_url = process_form.getResultsUrl(self, self.results_url);
                     var active_tax = process_form.getActiveTax();
                     var query_params = self.getUrlParams(true, '', active_tax);*/
                }




                //now see if we are on the URL we think...
                var url_base = this.getBaseUrl( current_url );
                //var results_url_base = this.getBaseUrl( current_url );

                var lang = self.getQueryParamFromURL("lang", window.location.href);
                if((typeof(lang)!=="undefined")&&(lang!==null))
                {
                    url_base = self.addUrlParam(url_base, "lang="+lang);
                }

                var sfid = self.getQueryParamFromURL("sfid", window.location.href);

                //if sfid is a number
                if(Number(parseFloat(sfid)) == sfid)
                {
                    url_base = self.addUrlParam(url_base, "sfid="+sfid);
                }

                //if any of the 3 conditions are true, then its good to go
                // - 1 | if the url base == results_url
                // - 2 | if url base+ "/"  == results_url - in case of user error in the results URL
                // - 3 | if the results URL has url params, and the current url starts with the results URL 

                //trim any trailing slash for easier comparison:
                url_base = url_base.replace(/\/$/, '');
                results_url = results_url.replace(/\/$/, '');
                results_url_encoded = encodeURI(results_url);
                

                var current_url_contains_results_url = -1;
                if((url_base==results_url)||(url_base.toLowerCase()==results_url_encoded.toLowerCase())  ){
                    current_url_contains_results_url = 1;
                } else {
                    if ( results_url.indexOf( '?' ) !== -1 && current_url.lastIndexOf(results_url, 0) === 0 ) {
                        current_url_contains_results_url = 1;
                    }
                }

                if(self.only_results_ajax==1)
                {//if a user has chosen to only allow ajax on results pages (default behaviour)

                    if( current_url_contains_results_url > -1)
                    {//this means the current URL contains the results url, which means we can do ajax
                        fetch_ajax_results = true;
                    }
                    else
                    {
                        fetch_ajax_results = false;
                    }
                }
                else
                {
                    if(fetch_type=="pagination")
                    {
                        if( current_url_contains_results_url > -1)
                        {//this means the current URL contains the results url, which means we can do ajax

                        }
                        else
                        {
                            //don't ajax pagination when not on a S&F page
                            fetch_ajax_results = false;
                        }


                    }

                }
            }

            return fetch_ajax_results;
        }

        this.setupAjaxPagination = function()
        {
            //infinite scroll
            if(this.pagination_type==="infinite_scroll")
            {
                var infinite_scroll_end = false;
                if(self.$ajax_results_container.find("[data-search-filter-action='infinite-scroll-end']").length>0)
                {
                    infinite_scroll_end = true;
                    self.is_max_paged = true;
                }

                if(parseInt(this.instance_number)===1) {
                    $(window).off("scroll", self.onWindowScroll);

                    if (self.canFetchAjaxResults("pagination")) {
                        $(window).on("scroll", self.onWindowScroll);
                    }
                }
            }
            else if(typeof(self.ajax_links_selector)=="undefined") {
                return;
            }
            else {
                $(document).off('click', self.ajax_links_selector);
                $(document).off(self.ajax_links_selector);
                $(self.ajax_links_selector).off();

                $(document).on('click', self.ajax_links_selector, function(e){

                    if(self.canFetchAjaxResults("pagination"))
                    {
                        e.preventDefault();

                        var link = jQuery(this).attr('href');
                        self.ajax_action = "pagination";

                        var pageNumber = self.getPagedFromURL(link);

                        self.$ajax_results_container.attr("data-paged", pageNumber);

                        self.fetchAjaxResults();

                        return false;
                    }
                });
            }
        };

        this.getPagedFromURL = function(URL){

            var pagedVal = 1;
            //first test to see if we have "/page/4/" in the URL
            var tpVal = self.getQueryParamFromURL("sf_paged", URL);
            if((typeof(tpVal)=="string")||(typeof(tpVal)=="number"))
            {
                pagedVal = tpVal;
            }

            return pagedVal;
        };

        this.getQueryParamFromURL = function(name, URL){

            var qstring = "?"+URL.split('?')[1];
            if(typeof(qstring)!="undefined")
            {
                var val = decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(qstring)||[,""])[1].replace(/\+/g, '%20'))||null;
                return val;
            }
            return "";
        };



        this.formUpdated = function(e){

            //e.preventDefault();
            if(self.auto_update==1) {
                self.submitForm();
            }
            else if((self.auto_update==0)&&(self.auto_count_refresh_mode==1))
            {
                self.formUpdatedFetchAjax();
            }

            return false;
        };

        this.formUpdatedFetchAjax = function(){

            //loop through all the fields and build the URL
            self.fetchAjaxForm();


            return false;
        };

        //make any corrections/updates to fields before the submit completes
        this.setFields = function(e){

            //if(self.is_ajax==0) {

                //sometimes the form is submitted without the slider yet having updated, and as we get our values from
                //the slider and not inputs, we need to check it if needs to be set
                //only occurs if ajax is off, and autosubmit on
                self.$fields.each(function() {

                    var $field = $(this);

                    var range_display_values = $field.find('.sf-meta-range-slider').attr("data-display-values-as");//data-display-values-as="text"

                    if(range_display_values==="textinput") {

                        if($field.find(".meta-slider").length>0){

                        }
                        $field.find(".meta-slider").each(function (index) {

                            var slider_object = $(this)[0];
                            var $slider_el = $(this).closest(".sf-meta-range-slider");
                            //var minVal = $slider_el.attr("data-min");
                            //var maxVal = $slider_el.attr("data-max");
                            var minVal = $slider_el.find(".sf-range-min").val();
                            var maxVal = $slider_el.find(".sf-range-max").val();
                            slider_object.noUiSlider.set([minVal, maxVal]);

                        });
                    }
                });
            //}

        }

        //submit
        this.submitForm = function(e){

            //loop through all the fields and build the URL
            if(self.isSubmitting == true) {
                return false;
            }

            self.setFields();
            self.clearTimer();

            self.isSubmitting = true;

            process_form.setTaxArchiveResultsUrl(self, self.results_url);

            self.$ajax_results_container.attr("data-paged", 1); //init paged

            if(self.canFetchAjaxResults())
            {//then we will ajax submit the form

                self.ajax_action = "submit"; //so we know it wasn't pagination
                self.fetchAjaxResults();
            }
            else
            {//then we will simply redirect to the Results URL

                var results_url = process_form.getResultsUrl(self, self.results_url);
                var query_params = self.getUrlParams(true, '');
                results_url = self.addUrlParam(results_url, query_params);

                window.location.href = results_url;
            }

            return false;
        };
        this.resetForm = function(submit_form)
        {
            //unset all fields
            self.$fields.each(function(){

                var $field = $(this);
				
				$field.removeAttr("data-sf-taxonomy-archive");
				
                //standard field types
                $field.find("select:not([multiple='multiple']) > option:first-child").prop("selected", true);
                $field.find("select[multiple='multiple'] > option").prop("selected", false);
                $field.find("input[type='checkbox']").prop("checked", false);
                $field.find("> ul > li:first-child input[type='radio']").prop("checked", true);
                $field.find("input[type='text']").val("");
                $field.find(".sf-option-active").removeClass("sf-option-active");
                $field.find("> ul > li:first-child input[type='radio']").parent().addClass("sf-option-active"); //re add active class to first "default" option

                //number range - 2 number input fields
                $field.find("input[type='number']").each(function(index){

                    var $thisInput = $(this);

                    if($thisInput.parent().parent().hasClass("sf-meta-range")) {

                        if(index==0) {
                            $thisInput.val($thisInput.attr("min"));
                        }
                        else if(index==1) {
                            $thisInput.val($thisInput.attr("max"));
                        }
                    }

                });

                //meta / numbers with 2 inputs (from / to fields) - second input must be reset to max value
                var $meta_select_from_to = $field.find(".sf-meta-range-select-fromto");

                if($meta_select_from_to.length>0) {

                    var start_min = $meta_select_from_to.attr("data-min");
                    var start_max = $meta_select_from_to.attr("data-max");

                    $meta_select_from_to.find("select").each(function(index){

                        var $thisInput = $(this);

                        if(index==0) {

                            $thisInput.val(start_min);
                        }
                        else if(index==1) {
                            $thisInput.val(start_max);
                        }

                    });
                }

                var $meta_radio_from_to = $field.find(".sf-meta-range-radio-fromto");

                if($meta_radio_from_to.length>0)
                {
                    var start_min = $meta_radio_from_to.attr("data-min");
                    var start_max = $meta_radio_from_to.attr("data-max");

                    var $radio_groups = $meta_radio_from_to.find('.sf-input-range-radio');

                    $radio_groups.each(function(index){


                        var $radios = $(this).find(".sf-input-radio");
                        $radios.prop("checked", false);

                        if(index==0)
                        {
                            $radios.filter('[value="'+start_min+'"]').prop("checked", true);
                        }
                        else if(index==1)
                        {
                            $radios.filter('[value="'+start_max+'"]').prop("checked", true);
                        }

                    });

                }

                //number slider - noUiSlider
                $field.find(".meta-slider").each(function(index){

                    var slider_object = $(this)[0];
                    /*var slider_object = $container.find(".meta-slider")[0];
                     var slider_val = slider_object.noUiSlider.get();*/

                    var $slider_el = $(this).closest(".sf-meta-range-slider");
                    var minVal = $slider_el.attr("data-min");
                    var maxVal = $slider_el.attr("data-max");
                    slider_object.noUiSlider.set([minVal, maxVal]);

                });

                //need to see if any are combobox and act accordingly
                var $combobox = $field.find("select[data-combobox='1']");
                if($combobox.length>0)
                {
                    if (typeof $combobox.chosen != "undefined")
                    {
                        $combobox.trigger("chosen:updated"); //for chosen only
                    }
                    else
                    {
                        $combobox.val('');
                        $combobox.trigger('change.select2');
                    }
                }


            });
            self.clearTimer();



            if(submit_form=="always")
            {
                self.submitForm();
            }
            else if(submit_form=="never")
            {
                if(this.auto_count_refresh_mode==1)
                {
                    self.formUpdatedFetchAjax();
                }
            }
            else if(submit_form=="auto")
            {
                if(this.auto_update==true)
                {
                    self.submitForm();
                }
                else
                {
                    if(this.auto_count_refresh_mode==1)
                    {
                        self.formUpdatedFetchAjax();
                    }
                }
            }

        };

        this.init();

        var event_data = {};
        event_data.sfid = self.sfid;
        event_data.targetSelector = self.ajax_target_attr;
        event_data.object = this;
        if(opts.isInit)
        {
            self.triggerEvent("sf:init", event_data);
        }

    });
};

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
//# sourceMappingURL=data:application/json;charset:utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9wdWJsaWMvYXNzZXRzL2pzL2luY2x1ZGVzL3BsdWdpbi5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiXG52YXIgJCBcdFx0XHRcdD0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKTtcbnZhciBzdGF0ZSBcdFx0XHQ9IHJlcXVpcmUoJy4vc3RhdGUnKTtcbnZhciBwcm9jZXNzX2Zvcm0gXHQ9IHJlcXVpcmUoJy4vcHJvY2Vzc19mb3JtJyk7XG52YXIgbm9VaVNsaWRlclx0XHQ9IHJlcXVpcmUoJ25vdWlzbGlkZXInKTtcbi8vdmFyIGNvb2tpZXMgICAgICAgICA9IHJlcXVpcmUoJ2pzLWNvb2tpZScpO1xudmFyIHRoaXJkUGFydHkgICAgICA9IHJlcXVpcmUoJy4vdGhpcmRwYXJ0eScpO1xuXG53aW5kb3cuc2VhcmNoQW5kRmlsdGVyID0ge1xuICAgIGV4dGVuc2lvbnM6IFtdLFxuICAgIHJlZ2lzdGVyRXh0ZW5zaW9uOiBmdW5jdGlvbiggZXh0ZW5zaW9uTmFtZSApIHtcbiAgICAgICAgdGhpcy5leHRlbnNpb25zLnB1c2goIGV4dGVuc2lvbk5hbWUgKTtcbiAgICB9XG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG9wdGlvbnMpXG57XG4gICAgdmFyIGRlZmF1bHRzID0ge1xuICAgICAgICBzdGFydE9wZW5lZDogZmFsc2UsXG4gICAgICAgIGlzSW5pdDogdHJ1ZSxcbiAgICAgICAgYWN0aW9uOiBcIlwiXG4gICAgfTtcblxuICAgIHZhciBvcHRzID0galF1ZXJ5LmV4dGVuZChkZWZhdWx0cywgb3B0aW9ucyk7XG4gICAgXG4gICAgdGhpcmRQYXJ0eS5pbml0KCk7XG4gICAgXG4gICAgLy9sb29wIHRocm91Z2ggZWFjaCBpdGVtIG1hdGNoZWRcbiAgICB0aGlzLmVhY2goZnVuY3Rpb24oKVxuICAgIHtcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgICAgIHRoaXMuc2ZpZCA9ICR0aGlzLmF0dHIoXCJkYXRhLXNmLWZvcm0taWRcIik7XG5cbiAgICAgICAgc3RhdGUuYWRkU2VhcmNoRm9ybSh0aGlzLnNmaWQsIHRoaXMpO1xuXG4gICAgICAgIHRoaXMuJGZpZWxkcyA9ICR0aGlzLmZpbmQoXCI+IHVsID4gbGlcIik7IC8vYSByZWZlcmVuY2UgdG8gZWFjaCBmaWVsZHMgcGFyZW50IExJXG5cbiAgICAgICAgdGhpcy5lbmFibGVfdGF4b25vbXlfYXJjaGl2ZXMgPSAkdGhpcy5hdHRyKCdkYXRhLXRheG9ub215LWFyY2hpdmVzJyk7XG4gICAgICAgIHRoaXMuY3VycmVudF90YXhvbm9teV9hcmNoaXZlID0gJHRoaXMuYXR0cignZGF0YS1jdXJyZW50LXRheG9ub215LWFyY2hpdmUnKTtcblxuICAgICAgICBpZih0eXBlb2YodGhpcy5lbmFibGVfdGF4b25vbXlfYXJjaGl2ZXMpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmVuYWJsZV90YXhvbm9teV9hcmNoaXZlcyA9IFwiMFwiO1xuICAgICAgICB9XG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuY3VycmVudF90YXhvbm9teV9hcmNoaXZlID0gXCJcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIHByb2Nlc3NfZm9ybS5pbml0KHNlbGYuZW5hYmxlX3RheG9ub215X2FyY2hpdmVzLCBzZWxmLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSk7XG4gICAgICAgIC8vcHJvY2Vzc19mb3JtLnNldFRheEFyY2hpdmVSZXN1bHRzVXJsKHNlbGYpO1xuICAgICAgICBwcm9jZXNzX2Zvcm0uZW5hYmxlSW5wdXRzKHNlbGYpO1xuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmV4dHJhX3F1ZXJ5X3BhcmFtcyk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuZXh0cmFfcXVlcnlfcGFyYW1zID0ge2FsbDoge30sIHJlc3VsdHM6IHt9LCBhamF4OiB7fX07XG4gICAgICAgIH1cblxuXG4gICAgICAgIHRoaXMudGVtcGxhdGVfaXNfbG9hZGVkID0gJHRoaXMuYXR0cihcImRhdGEtdGVtcGxhdGUtbG9hZGVkXCIpO1xuICAgICAgICB0aGlzLmlzX2FqYXggPSAkdGhpcy5hdHRyKFwiZGF0YS1hamF4XCIpO1xuICAgICAgICB0aGlzLmluc3RhbmNlX251bWJlciA9ICR0aGlzLmF0dHIoJ2RhdGEtaW5zdGFuY2UtY291bnQnKTtcbiAgICAgICAgdGhpcy4kYWpheF9yZXN1bHRzX2NvbnRhaW5lciA9IGpRdWVyeSgkdGhpcy5hdHRyKFwiZGF0YS1hamF4LXRhcmdldFwiKSk7XG5cbiAgICAgICAgdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9ucyA9ICR0aGlzLmF0dHIoXCJkYXRhLWFqYXgtdXBkYXRlLXNlY3Rpb25zXCIpID8gSlNPTi5wYXJzZSggJHRoaXMuYXR0cihcImRhdGEtYWpheC11cGRhdGUtc2VjdGlvbnNcIikgKSA6IFtdO1xuICAgICAgICB0aGlzLnJlcGxhY2VfcmVzdWx0cyA9ICR0aGlzLmF0dHIoXCJkYXRhLXJlcGxhY2UtcmVzdWx0c1wiKSA9PT0gXCIwXCIgPyBmYWxzZSA6IHRydWU7XG4gICAgICAgIFxuICAgICAgICB0aGlzLnJlc3VsdHNfdXJsID0gJHRoaXMuYXR0cihcImRhdGEtcmVzdWx0cy11cmxcIik7XG4gICAgICAgIHRoaXMuZGVidWdfbW9kZSA9ICR0aGlzLmF0dHIoXCJkYXRhLWRlYnVnLW1vZGVcIik7XG4gICAgICAgIHRoaXMudXBkYXRlX2FqYXhfdXJsID0gJHRoaXMuYXR0cihcImRhdGEtdXBkYXRlLWFqYXgtdXJsXCIpO1xuICAgICAgICB0aGlzLnBhZ2luYXRpb25fdHlwZSA9ICR0aGlzLmF0dHIoXCJkYXRhLWFqYXgtcGFnaW5hdGlvbi10eXBlXCIpO1xuICAgICAgICB0aGlzLmF1dG9fY291bnQgPSAkdGhpcy5hdHRyKFwiZGF0YS1hdXRvLWNvdW50XCIpO1xuICAgICAgICB0aGlzLmF1dG9fY291bnRfcmVmcmVzaF9tb2RlID0gJHRoaXMuYXR0cihcImRhdGEtYXV0by1jb3VudC1yZWZyZXNoLW1vZGVcIik7XG4gICAgICAgIHRoaXMub25seV9yZXN1bHRzX2FqYXggPSAkdGhpcy5hdHRyKFwiZGF0YS1vbmx5LXJlc3VsdHMtYWpheFwiKTsgLy9pZiB3ZSBhcmUgbm90IG9uIHRoZSByZXN1bHRzIHBhZ2UsIHJlZGlyZWN0IHJhdGhlciB0aGFuIHRyeSB0byBsb2FkIHZpYSBhamF4XG4gICAgICAgIHRoaXMuc2Nyb2xsX3RvX3BvcyA9ICR0aGlzLmF0dHIoXCJkYXRhLXNjcm9sbC10by1wb3NcIik7XG4gICAgICAgIHRoaXMuY3VzdG9tX3Njcm9sbF90byA9ICR0aGlzLmF0dHIoXCJkYXRhLWN1c3RvbS1zY3JvbGwtdG9cIik7XG4gICAgICAgIHRoaXMuc2Nyb2xsX29uX2FjdGlvbiA9ICR0aGlzLmF0dHIoXCJkYXRhLXNjcm9sbC1vbi1hY3Rpb25cIik7XG4gICAgICAgIHRoaXMubGFuZ19jb2RlID0gJHRoaXMuYXR0cihcImRhdGEtbGFuZy1jb2RlXCIpO1xuICAgICAgICB0aGlzLmFqYXhfdXJsID0gJHRoaXMuYXR0cignZGF0YS1hamF4LXVybCcpO1xuICAgICAgICB0aGlzLmFqYXhfZm9ybV91cmwgPSAkdGhpcy5hdHRyKCdkYXRhLWFqYXgtZm9ybS11cmwnKTtcbiAgICAgICAgdGhpcy5pc19ydGwgPSAkdGhpcy5hdHRyKCdkYXRhLWlzLXJ0bCcpO1xuXG4gICAgICAgIHRoaXMuZGlzcGxheV9yZXN1bHRfbWV0aG9kID0gJHRoaXMuYXR0cignZGF0YS1kaXNwbGF5LXJlc3VsdC1tZXRob2QnKTtcbiAgICAgICAgdGhpcy5tYWludGFpbl9zdGF0ZSA9ICR0aGlzLmF0dHIoJ2RhdGEtbWFpbnRhaW4tc3RhdGUnKTtcbiAgICAgICAgdGhpcy5hamF4X2FjdGlvbiA9IFwiXCI7XG4gICAgICAgIHRoaXMubGFzdF9zdWJtaXRfcXVlcnlfcGFyYW1zID0gXCJcIjtcblxuICAgICAgICB0aGlzLmN1cnJlbnRfcGFnZWQgPSBwYXJzZUludCgkdGhpcy5hdHRyKCdkYXRhLWluaXQtcGFnZWQnKSk7XG4gICAgICAgIHRoaXMubGFzdF9sb2FkX21vcmVfaHRtbCA9IFwiXCI7XG4gICAgICAgIHRoaXMubG9hZF9tb3JlX2h0bWwgPSBcIlwiO1xuICAgICAgICB0aGlzLmFqYXhfZGF0YV90eXBlID0gJHRoaXMuYXR0cignZGF0YS1hamF4LWRhdGEtdHlwZScpO1xuICAgICAgICB0aGlzLmFqYXhfdGFyZ2V0X2F0dHIgPSAkdGhpcy5hdHRyKFwiZGF0YS1hamF4LXRhcmdldFwiKTtcbiAgICAgICAgdGhpcy51c2VfaGlzdG9yeV9hcGkgPSAkdGhpcy5hdHRyKFwiZGF0YS11c2UtaGlzdG9yeS1hcGlcIik7XG4gICAgICAgIHRoaXMuaXNfc3VibWl0dGluZyA9IGZhbHNlO1xuXG4gICAgICAgIHRoaXMubGFzdF9hamF4X3JlcXVlc3QgPSBudWxsO1xuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLnJlc3VsdHNfaHRtbCk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMucmVzdWx0c19odG1sID0gXCJcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLnVzZV9oaXN0b3J5X2FwaSk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMudXNlX2hpc3RvcnlfYXBpID0gXCJcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLnBhZ2luYXRpb25fdHlwZSk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMucGFnaW5hdGlvbl90eXBlID0gXCJub3JtYWxcIjtcbiAgICAgICAgfVxuICAgICAgICBpZih0eXBlb2YodGhpcy5jdXJyZW50X3BhZ2VkKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5jdXJyZW50X3BhZ2VkID0gMTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmFqYXhfdGFyZ2V0X2F0dHIpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmFqYXhfdGFyZ2V0X2F0dHIgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuYWpheF91cmwpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmFqYXhfdXJsID0gXCJcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmFqYXhfZm9ybV91cmwpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmFqYXhfZm9ybV91cmwgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMucmVzdWx0c191cmwpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLnJlc3VsdHNfdXJsID0gXCJcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLnNjcm9sbF90b19wb3MpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLnNjcm9sbF90b19wb3MgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuc2Nyb2xsX29uX2FjdGlvbik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuc2Nyb2xsX29uX2FjdGlvbiA9IFwiXCI7XG4gICAgICAgIH1cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuY3VzdG9tX3Njcm9sbF90byk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuY3VzdG9tX3Njcm9sbF90byA9IFwiXCI7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy4kY3VzdG9tX3Njcm9sbF90byA9IGpRdWVyeSh0aGlzLmN1c3RvbV9zY3JvbGxfdG8pO1xuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLnVwZGF0ZV9hamF4X3VybCk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMudXBkYXRlX2FqYXhfdXJsID0gXCJcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmRlYnVnX21vZGUpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmRlYnVnX21vZGUgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuYWpheF90YXJnZXRfb2JqZWN0KT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5hamF4X3RhcmdldF9vYmplY3QgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMudGVtcGxhdGVfaXNfbG9hZGVkKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy50ZW1wbGF0ZV9pc19sb2FkZWQgPSBcIjBcIjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmF1dG9fY291bnRfcmVmcmVzaF9tb2RlKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5hdXRvX2NvdW50X3JlZnJlc2hfbW9kZSA9IFwiMFwiO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5hamF4X2xpbmtzX3NlbGVjdG9yID0gJHRoaXMuYXR0cihcImRhdGEtYWpheC1saW5rcy1zZWxlY3RvclwiKTtcblxuXG4gICAgICAgIHRoaXMuYXV0b191cGRhdGUgPSAkdGhpcy5hdHRyKFwiZGF0YS1hdXRvLXVwZGF0ZVwiKTtcbiAgICAgICAgdGhpcy5pbnB1dFRpbWVyID0gMDtcblxuICAgICAgICB0aGlzLnNldEluZmluaXRlU2Nyb2xsQ29udGFpbmVyID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICAvLyBXaGVuIHdlIG5hdmlnYXRlIGF3YXkgZnJvbSBzZWFyY2ggcmVzdWx0cywgYW5kIHRoZW4gcHJlc3MgYmFjayxcbiAgICAgICAgICAgIC8vIGlzX21heF9wYWdlZCBpcyByZXRhaW5lZCwgc28gd2Ugb25seSB3YW50IHRvIHNldCBpdCB0byBmYWxzZSBpZlxuICAgICAgICAgICAgLy8gd2UgYXJlIGluaXRhbGl6aW5nIHRoZSByZXN1bHRzIHBhZ2UgdGhlIGZpcnN0IHRpbWUgLSBzbyBqdXN0IFxuICAgICAgICAgICAgLy8gY2hlY2sgaWYgdGhpcyB2YXIgaXMgdW5kZWZpbmVkIChhcyBpdCBzaG91bGQgYmUgb24gZmlyc3QgdXNlKTtcbiAgICAgICAgICAgIGlmICggdHlwZW9mICggdGhpcy5pc19tYXhfcGFnZWQgKSA9PT0gJ3VuZGVmaW5lZCcgKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5pc19tYXhfcGFnZWQgPSBmYWxzZTsgLy9mb3IgbG9hZCBtb3JlIG9ubHksIG9uY2Ugd2UgZGV0ZWN0IHdlJ3JlIGF0IHRoZSBlbmQgc2V0IHRoaXMgdG8gdHJ1ZVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLnVzZV9zY3JvbGxfbG9hZGVyID0gJHRoaXMuYXR0cignZGF0YS1zaG93LXNjcm9sbC1sb2FkZXInKTtcbiAgICAgICAgICAgIHRoaXMuaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lciA9ICR0aGlzLmF0dHIoJ2RhdGEtaW5maW5pdGUtc2Nyb2xsLWNvbnRhaW5lcicpO1xuICAgICAgICAgICAgdGhpcy5pbmZpbml0ZV9zY3JvbGxfdHJpZ2dlcl9hbW91bnQgPSAkdGhpcy5hdHRyKCdkYXRhLWluZmluaXRlLXNjcm9sbC10cmlnZ2VyJyk7XG4gICAgICAgICAgICB0aGlzLmluZmluaXRlX3Njcm9sbF9yZXN1bHRfY2xhc3MgPSAkdGhpcy5hdHRyKCdkYXRhLWluZmluaXRlLXNjcm9sbC1yZXN1bHQtY2xhc3MnKTtcbiAgICAgICAgICAgIHRoaXMuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIgPSB0aGlzLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyO1xuXG4gICAgICAgICAgICBpZih0eXBlb2YodGhpcy5pbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB0aGlzLmluZmluaXRlX3Njcm9sbF9jb250YWluZXIgPSBcIlwiO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHRoaXMuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIgPSBqUXVlcnkodGhpcy5hamF4X3RhcmdldF9hdHRyICsgJyAnICsgdGhpcy5pbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYodHlwZW9mKHRoaXMuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcyk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdGhpcy5pbmZpbml0ZV9zY3JvbGxfcmVzdWx0X2NsYXNzID0gXCJcIjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYodHlwZW9mKHRoaXMudXNlX3Njcm9sbF9sb2FkZXIpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHRoaXMudXNlX3Njcm9sbF9sb2FkZXIgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG4gICAgICAgIHRoaXMuc2V0SW5maW5pdGVTY3JvbGxDb250YWluZXIoKTtcblxuICAgICAgICAvKiBmdW5jdGlvbnMgKi9cblxuICAgICAgICB0aGlzLnJlc2V0ID0gZnVuY3Rpb24oc3VibWl0X2Zvcm0pXG4gICAgICAgIHtcblxuICAgICAgICAgICAgdGhpcy5yZXNldEZvcm0oc3VibWl0X2Zvcm0pO1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmlucHV0VXBkYXRlID0gZnVuY3Rpb24oZGVsYXlEdXJhdGlvbilcbiAgICAgICAge1xuICAgICAgICAgICAgaWYodHlwZW9mKGRlbGF5RHVyYXRpb24pPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBkZWxheUR1cmF0aW9uID0gMzAwO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWxmLnJlc2V0VGltZXIoZGVsYXlEdXJhdGlvbik7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNjcm9sbFRvUG9zID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgb2Zmc2V0ID0gMDtcbiAgICAgICAgICAgIHZhciBjYW5TY3JvbGwgPSB0cnVlO1xuXG4gICAgICAgICAgICBpZihzZWxmLmlzX2FqYXg9PTEpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaWYoc2VsZi5zY3JvbGxfdG9fcG9zPT1cIndpbmRvd1wiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgb2Zmc2V0ID0gMDtcblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlIGlmKHNlbGYuc2Nyb2xsX3RvX3Bvcz09XCJmb3JtXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBvZmZzZXQgPSAkdGhpcy5vZmZzZXQoKS50b3A7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYoc2VsZi5zY3JvbGxfdG9fcG9zPT1cInJlc3VsdHNcIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGlmKHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIubGVuZ3RoPjApXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG9mZnNldCA9IHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIub2Zmc2V0KCkudG9wO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYoc2VsZi5zY3JvbGxfdG9fcG9zPT1cImN1c3RvbVwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy9jdXN0b21fc2Nyb2xsX3RvXG4gICAgICAgICAgICAgICAgICAgIGlmKHNlbGYuJGN1c3RvbV9zY3JvbGxfdG8ubGVuZ3RoPjApXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG9mZnNldCA9IHNlbGYuJGN1c3RvbV9zY3JvbGxfdG8ub2Zmc2V0KCkudG9wO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGNhblNjcm9sbCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmKGNhblNjcm9sbClcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICQoXCJodG1sLCBib2R5XCIpLnN0b3AoKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNjcm9sbFRvcDogb2Zmc2V0XG4gICAgICAgICAgICAgICAgICAgIH0sIFwibm9ybWFsXCIsIFwiZWFzZU91dFF1YWRcIiApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuYXR0YWNoQWN0aXZlQ2xhc3MgPSBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAvL2NoZWNrIHRvIHNlZSBpZiB3ZSBhcmUgdXNpbmcgYWpheCAmIGF1dG8gY291bnRcbiAgICAgICAgICAgIC8vaWYgbm90LCB0aGUgc2VhcmNoIGZvcm0gZG9lcyBub3QgZ2V0IHJlbG9hZGVkLCBzbyB3ZSBuZWVkIHRvIHVwZGF0ZSB0aGUgc2Ytb3B0aW9uLWFjdGl2ZSBjbGFzcyBvbiBhbGwgZmllbGRzXG5cbiAgICAgICAgICAgICR0aGlzLm9uKCdjaGFuZ2UnLCAnaW5wdXRbdHlwZT1cInJhZGlvXCJdLCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0sIHNlbGVjdCcsIGZ1bmN0aW9uKGUpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyICRjdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgdmFyICRjdGhpc19wYXJlbnQgPSAkY3RoaXMuY2xvc2VzdChcImxpW2RhdGEtc2YtZmllbGQtbmFtZV1cIik7XG4gICAgICAgICAgICAgICAgdmFyIHRoaXNfdGFnID0gJGN0aGlzLnByb3AoXCJ0YWdOYW1lXCIpLnRvTG93ZXJDYXNlKCk7XG4gICAgICAgICAgICAgICAgdmFyIGlucHV0X3R5cGUgPSAkY3RoaXMuYXR0cihcInR5cGVcIik7XG4gICAgICAgICAgICAgICAgdmFyIHBhcmVudF90YWcgPSAkY3RoaXNfcGFyZW50LnByb3AoXCJ0YWdOYW1lXCIpLnRvTG93ZXJDYXNlKCk7XG5cbiAgICAgICAgICAgICAgICBpZigodGhpc190YWc9PVwiaW5wdXRcIikmJigoaW5wdXRfdHlwZT09XCJyYWRpb1wiKXx8KGlucHV0X3R5cGU9PVwiY2hlY2tib3hcIikpICYmIChwYXJlbnRfdGFnPT1cImxpXCIpKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRhbGxfb3B0aW9ucyA9ICRjdGhpc19wYXJlbnQucGFyZW50KCkuZmluZCgnbGknKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRhbGxfb3B0aW9uc19maWVsZHMgPSAkY3RoaXNfcGFyZW50LnBhcmVudCgpLmZpbmQoJ2lucHV0OmNoZWNrZWQnKTtcblxuICAgICAgICAgICAgICAgICAgICAkYWxsX29wdGlvbnMucmVtb3ZlQ2xhc3MoXCJzZi1vcHRpb24tYWN0aXZlXCIpO1xuICAgICAgICAgICAgICAgICAgICAkYWxsX29wdGlvbnNfZmllbGRzLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRwYXJlbnQgPSAkKHRoaXMpLmNsb3Nlc3QoXCJsaVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRwYXJlbnQuYWRkQ2xhc3MoXCJzZi1vcHRpb24tYWN0aXZlXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYodGhpc190YWc9PVwic2VsZWN0XCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGFsbF9vcHRpb25zID0gJGN0aGlzLmNoaWxkcmVuKCk7XG4gICAgICAgICAgICAgICAgICAgICRhbGxfb3B0aW9ucy5yZW1vdmVDbGFzcyhcInNmLW9wdGlvbi1hY3RpdmVcIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciB0aGlzX3ZhbCA9ICRjdGhpcy52YWwoKTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgdGhpc19hcnJfdmFsID0gKHR5cGVvZiB0aGlzX3ZhbCA9PSAnc3RyaW5nJyB8fCB0aGlzX3ZhbCBpbnN0YW5jZW9mIFN0cmluZykgPyBbdGhpc192YWxdIDogdGhpc192YWw7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzX2Fycl92YWwpLmVhY2goZnVuY3Rpb24oaSwgdmFsdWUpe1xuICAgICAgICAgICAgICAgICAgICAgICAgJGN0aGlzLmZpbmQoXCJvcHRpb25bdmFsdWU9J1wiK3ZhbHVlK1wiJ11cIikuYWRkQ2xhc3MoXCJzZi1vcHRpb24tYWN0aXZlXCIpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfTtcbiAgICAgICAgdGhpcy5pbml0QXV0b1VwZGF0ZUV2ZW50cyA9IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIC8qIGF1dG8gdXBkYXRlICovXG4gICAgICAgICAgICBpZigoc2VsZi5hdXRvX3VwZGF0ZT09MSl8fChzZWxmLmF1dG9fY291bnRfcmVmcmVzaF9tb2RlPT0xKSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkdGhpcy5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9XCJyYWRpb1wiXSwgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdLCBzZWxlY3QnLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuaW5wdXRVcGRhdGUoMjAwKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICR0aGlzLm9uKCdpbnB1dCcsICdpbnB1dFt0eXBlPVwibnVtYmVyXCJdJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgICAgICAgICBzZWxmLmlucHV0VXBkYXRlKDgwMCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB2YXIgJHRleHRJbnB1dCA9ICR0aGlzLmZpbmQoJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdOm5vdCguc2YtZGF0ZXBpY2tlciknKTtcbiAgICAgICAgICAgICAgICB2YXIgbGFzdFZhbHVlID0gJHRleHRJbnB1dC52YWwoKTtcblxuICAgICAgICAgICAgICAgICR0aGlzLm9uKCdpbnB1dCcsICdpbnB1dFt0eXBlPVwidGV4dFwiXTpub3QoLnNmLWRhdGVwaWNrZXIpJywgZnVuY3Rpb24oKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaWYobGFzdFZhbHVlIT0kdGV4dElucHV0LnZhbCgpKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmlucHV0VXBkYXRlKDEyMDApO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbGFzdFZhbHVlID0gJHRleHRJbnB1dC52YWwoKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuXG4gICAgICAgICAgICAgICAgJHRoaXMub24oJ2tleXByZXNzJywgJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdOm5vdCguc2YtZGF0ZXBpY2tlciknLCBmdW5jdGlvbihlKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGUud2hpY2ggPT0gMTMpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLnN1Ym1pdEZvcm0oKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyR0aGlzLm9uKCdpbnB1dCcsICdpbnB1dC5zZi1kYXRlcGlja2VyJywgc2VsZi5kYXRlSW5wdXRUeXBlKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8vdGhpcy5pbml0QXV0b1VwZGF0ZUV2ZW50cygpO1xuXG5cbiAgICAgICAgdGhpcy5jbGVhclRpbWVyID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQoc2VsZi5pbnB1dFRpbWVyKTtcbiAgICAgICAgfTtcbiAgICAgICAgdGhpcy5yZXNldFRpbWVyID0gZnVuY3Rpb24oZGVsYXlEdXJhdGlvbilcbiAgICAgICAge1xuICAgICAgICAgICAgY2xlYXJUaW1lb3V0KHNlbGYuaW5wdXRUaW1lcik7XG4gICAgICAgICAgICBzZWxmLmlucHV0VGltZXIgPSBzZXRUaW1lb3V0KHNlbGYuZm9ybVVwZGF0ZWQsIGRlbGF5RHVyYXRpb24pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5hZGREYXRlUGlja2VycyA9IGZ1bmN0aW9uKClcbiAgICAgICAge1xuICAgICAgICAgICAgdmFyICRkYXRlX3BpY2tlciA9ICR0aGlzLmZpbmQoXCIuc2YtZGF0ZXBpY2tlclwiKTtcblxuICAgICAgICAgICAgaWYoJGRhdGVfcGlja2VyLmxlbmd0aD4wKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICRkYXRlX3BpY2tlci5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGRhdGVGb3JtYXQgPSBcIlwiO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZGF0ZURyb3Bkb3duWWVhciA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZGF0ZURyb3Bkb3duTW9udGggPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJGNsb3Nlc3RfZGF0ZV93cmFwID0gJHRoaXMuY2xvc2VzdChcIi5zZl9kYXRlX2ZpZWxkXCIpO1xuICAgICAgICAgICAgICAgICAgICBpZigkY2xvc2VzdF9kYXRlX3dyYXAubGVuZ3RoPjApXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGVGb3JtYXQgPSAkY2xvc2VzdF9kYXRlX3dyYXAuYXR0cihcImRhdGEtZGF0ZS1mb3JtYXRcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCRjbG9zZXN0X2RhdGVfd3JhcC5hdHRyKFwiZGF0YS1kYXRlLXVzZS15ZWFyLWRyb3Bkb3duXCIpPT0xKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGVEcm9wZG93blllYXIgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoJGNsb3Nlc3RfZGF0ZV93cmFwLmF0dHIoXCJkYXRhLWRhdGUtdXNlLW1vbnRoLWRyb3Bkb3duXCIpPT0xKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGVEcm9wZG93bk1vbnRoID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIHZhciBkYXRlUGlja2VyT3B0aW9ucyA9IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlubGluZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHNob3dPdGhlck1vbnRoczogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIG9uU2VsZWN0OiBmdW5jdGlvbihlLCBmcm9tX2ZpZWxkKXsgc2VsZi5kYXRlU2VsZWN0KGUsIGZyb21fZmllbGQsICQodGhpcykpOyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0ZUZvcm1hdDogZGF0ZUZvcm1hdCxcblxuICAgICAgICAgICAgICAgICAgICAgICAgY2hhbmdlTW9udGg6IGRhdGVEcm9wZG93bk1vbnRoLFxuICAgICAgICAgICAgICAgICAgICAgICAgY2hhbmdlWWVhcjogZGF0ZURyb3Bkb3duWWVhclxuICAgICAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKHNlbGYuaXNfcnRsPT0xKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRlUGlja2VyT3B0aW9ucy5kaXJlY3Rpb24gPSBcInJ0bFwiO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgJHRoaXMuZGF0ZXBpY2tlcihkYXRlUGlja2VyT3B0aW9ucyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5sYW5nX2NvZGUhPVwiXCIpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQuZGF0ZXBpY2tlci5zZXREZWZhdWx0cyhcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkLmV4dGVuZChcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgeydkYXRlRm9ybWF0JzpkYXRlRm9ybWF0fSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJC5kYXRlcGlja2VyLnJlZ2lvbmFsWyBzZWxmLmxhbmdfY29kZV1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgICAgICAgICApO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkLmRhdGVwaWNrZXIuc2V0RGVmYXVsdHMoXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJC5leHRlbmQoXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHsnZGF0ZUZvcm1hdCc6ZGF0ZUZvcm1hdH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQuZGF0ZXBpY2tlci5yZWdpb25hbFtcImVuXCJdXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgICAgICAgICAgKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGlmKCQoJy5sbC1za2luLW1lbG9uJykubGVuZ3RoPT0wKXtcblxuICAgICAgICAgICAgICAgICAgICAkZGF0ZV9waWNrZXIuZGF0ZXBpY2tlcignd2lkZ2V0Jykud3JhcCgnPGRpdiBjbGFzcz1cImxsLXNraW4tbWVsb24gc2VhcmNoYW5kZmlsdGVyLWRhdGUtcGlja2VyXCIvPicpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuZGF0ZVNlbGVjdCA9IGZ1bmN0aW9uKGUsIGZyb21fZmllbGQsICR0aGlzKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgJGlucHV0X2ZpZWxkID0gJChmcm9tX2ZpZWxkLmlucHV0LmdldCgwKSk7XG4gICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICB2YXIgJGRhdGVfZmllbGRzID0gJGlucHV0X2ZpZWxkLmNsb3Nlc3QoJ1tkYXRhLXNmLWZpZWxkLWlucHV0LXR5cGU9XCJkYXRlcmFuZ2VcIl0sIFtkYXRhLXNmLWZpZWxkLWlucHV0LXR5cGU9XCJkYXRlXCJdJyk7XG4gICAgICAgICAgICAkZGF0ZV9maWVsZHMuZWFjaChmdW5jdGlvbihlLCBpbmRleCl7XG4gICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgdmFyICR0Zl9kYXRlX3BpY2tlcnMgPSAkKHRoaXMpLmZpbmQoXCIuc2YtZGF0ZXBpY2tlclwiKTtcbiAgICAgICAgICAgICAgICB2YXIgbm9fZGF0ZV9waWNrZXJzID0gJHRmX2RhdGVfcGlja2Vycy5sZW5ndGg7XG4gICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgaWYobm9fZGF0ZV9waWNrZXJzPjEpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAvL3RoZW4gaXQgaXMgYSBkYXRlIHJhbmdlLCBzbyBtYWtlIHN1cmUgYm90aCBmaWVsZHMgYXJlIGZpbGxlZCBiZWZvcmUgdXBkYXRpbmdcbiAgICAgICAgICAgICAgICAgICAgdmFyIGRwX2NvdW50ZXIgPSAwO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZHBfZW1wdHlfZmllbGRfY291bnQgPSAwO1xuICAgICAgICAgICAgICAgICAgICAkdGZfZGF0ZV9waWNrZXJzLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoJCh0aGlzKS52YWwoKT09XCJcIilcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkcF9lbXB0eV9maWVsZF9jb3VudCsrO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBkcF9jb3VudGVyKys7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKGRwX2VtcHR5X2ZpZWxkX2NvdW50PT0wKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmlucHV0VXBkYXRlKDEpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuaW5wdXRVcGRhdGUoMSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmFkZFJhbmdlU2xpZGVycyA9IGZ1bmN0aW9uKClcbiAgICAgICAge1xuICAgICAgICAgICAgdmFyICRtZXRhX3JhbmdlID0gJHRoaXMuZmluZChcIi5zZi1tZXRhLXJhbmdlLXNsaWRlclwiKTtcblxuICAgICAgICAgICAgaWYoJG1ldGFfcmFuZ2UubGVuZ3RoPjApXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgJG1ldGFfcmFuZ2UuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtaW4gPSAkdGhpcy5hdHRyKFwiZGF0YS1taW5cIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtYXggPSAkdGhpcy5hdHRyKFwiZGF0YS1tYXhcIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBzbWluID0gJHRoaXMuYXR0cihcImRhdGEtc3RhcnQtbWluXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgc21heCA9ICR0aGlzLmF0dHIoXCJkYXRhLXN0YXJ0LW1heFwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGRpc3BsYXlfdmFsdWVfYXMgPSAkdGhpcy5hdHRyKFwiZGF0YS1kaXNwbGF5LXZhbHVlcy1hc1wiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHN0ZXAgPSAkdGhpcy5hdHRyKFwiZGF0YS1zdGVwXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgJHN0YXJ0X3ZhbCA9ICR0aGlzLmZpbmQoJy5zZi1yYW5nZS1taW4nKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRlbmRfdmFsID0gJHRoaXMuZmluZCgnLnNmLXJhbmdlLW1heCcpO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGRlY2ltYWxfcGxhY2VzID0gJHRoaXMuYXR0cihcImRhdGEtZGVjaW1hbC1wbGFjZXNcIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciB0aG91c2FuZF9zZXBlcmF0b3IgPSAkdGhpcy5hdHRyKFwiZGF0YS10aG91c2FuZC1zZXBlcmF0b3JcIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBkZWNpbWFsX3NlcGVyYXRvciA9ICR0aGlzLmF0dHIoXCJkYXRhLWRlY2ltYWwtc2VwZXJhdG9yXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBmaWVsZF9mb3JtYXQgPSB3TnVtYih7XG4gICAgICAgICAgICAgICAgICAgICAgICBtYXJrOiBkZWNpbWFsX3NlcGVyYXRvcixcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlY2ltYWxzOiBwYXJzZUZsb2F0KGRlY2ltYWxfcGxhY2VzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHRob3VzYW5kOiB0aG91c2FuZF9zZXBlcmF0b3JcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cblxuXG4gICAgICAgICAgICAgICAgICAgIHZhciBtaW5fdW5mb3JtYXR0ZWQgPSBwYXJzZUZsb2F0KHNtaW4pO1xuICAgICAgICAgICAgICAgICAgICB2YXIgbWluX2Zvcm1hdHRlZCA9IGZpZWxkX2Zvcm1hdC50byhwYXJzZUZsb2F0KHNtaW4pKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1heF9mb3JtYXR0ZWQgPSBmaWVsZF9mb3JtYXQudG8ocGFyc2VGbG9hdChzbWF4KSk7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtYXhfdW5mb3JtYXR0ZWQgPSBwYXJzZUZsb2F0KHNtYXgpO1xuICAgICAgICAgICAgICAgICAgICAvL2FsZXJ0KG1pbl9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAvL2FsZXJ0KG1heF9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAvL2FsZXJ0KGRpc3BsYXlfdmFsdWVfYXMpO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgaWYoZGlzcGxheV92YWx1ZV9hcz09XCJ0ZXh0aW5wdXRcIilcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHN0YXJ0X3ZhbC52YWwobWluX2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZW5kX3ZhbC52YWwobWF4X2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSBpZihkaXNwbGF5X3ZhbHVlX2FzPT1cInRleHRcIilcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHN0YXJ0X3ZhbC5odG1sKG1pbl9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJGVuZF92YWwuaHRtbChtYXhfZm9ybWF0dGVkKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIG5vVUlPcHRpb25zID0ge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmFuZ2U6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbWluJzogWyBwYXJzZUZsb2F0KG1pbikgXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnbWF4JzogWyBwYXJzZUZsb2F0KG1heCkgXVxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0YXJ0OiBbbWluX2Zvcm1hdHRlZCwgbWF4X2Zvcm1hdHRlZF0sXG4gICAgICAgICAgICAgICAgICAgICAgICBoYW5kbGVzOiAyLFxuICAgICAgICAgICAgICAgICAgICAgICAgY29ubmVjdDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0ZXA6IHBhcnNlRmxvYXQoc3RlcCksXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGJlaGF2aW91cjogJ2V4dGVuZC10YXAnLFxuICAgICAgICAgICAgICAgICAgICAgICAgZm9ybWF0OiBmaWVsZF9mb3JtYXRcbiAgICAgICAgICAgICAgICAgICAgfTtcblxuXG5cbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5pc19ydGw9PTEpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5vVUlPcHRpb25zLmRpcmVjdGlvbiA9IFwicnRsXCI7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB2YXIgc2xpZGVyX29iamVjdCA9ICQodGhpcykuZmluZChcIi5tZXRhLXNsaWRlclwiKVswXTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggXCJ1bmRlZmluZWRcIiAhPT0gdHlwZW9mKCBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIgKSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vZGVzdHJveSBpZiBpdCBleGlzdHMuLiB0aGlzIG1lYW5zIHNvbWVob3cgYW5vdGhlciBpbnN0YW5jZSBoYWQgaW5pdGlhbGlzZWQgaXQuLlxuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVyX29iamVjdC5ub1VpU2xpZGVyLmRlc3Ryb3koKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIG5vVWlTbGlkZXIuY3JlYXRlKHNsaWRlcl9vYmplY3QsIG5vVUlPcHRpb25zKTtcblxuICAgICAgICAgICAgICAgICAgICAkc3RhcnRfdmFsLm9mZigpO1xuICAgICAgICAgICAgICAgICAgICAkc3RhcnRfdmFsLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVyX29iamVjdC5ub1VpU2xpZGVyLnNldChbJCh0aGlzKS52YWwoKSwgbnVsbF0pO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAkZW5kX3ZhbC5vZmYoKTtcbiAgICAgICAgICAgICAgICAgICAgJGVuZF92YWwub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuc2V0KFtudWxsLCAkKHRoaXMpLnZhbCgpXSk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vJHN0YXJ0X3ZhbC5odG1sKG1pbl9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAvLyRlbmRfdmFsLmh0bWwobWF4X2Zvcm1hdHRlZCk7XG5cbiAgICAgICAgICAgICAgICAgICAgc2xpZGVyX29iamVjdC5ub1VpU2xpZGVyLm9mZigndXBkYXRlJyk7XG4gICAgICAgICAgICAgICAgICAgIHNsaWRlcl9vYmplY3Qubm9VaVNsaWRlci5vbigndXBkYXRlJywgZnVuY3Rpb24oIHZhbHVlcywgaGFuZGxlICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2xpZGVyX3N0YXJ0X3ZhbCAgPSBtaW5fZm9ybWF0dGVkO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHNsaWRlcl9lbmRfdmFsICA9IG1heF9mb3JtYXR0ZWQ7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB2YWx1ZSA9IHZhbHVlc1toYW5kbGVdO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICggaGFuZGxlICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1heF9mb3JtYXR0ZWQgPSB2YWx1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWluX2Zvcm1hdHRlZCA9IHZhbHVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihkaXNwbGF5X3ZhbHVlX2FzPT1cInRleHRpbnB1dFwiKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzdGFydF92YWwudmFsKG1pbl9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRlbmRfdmFsLnZhbChtYXhfZm9ybWF0dGVkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGVsc2UgaWYoZGlzcGxheV92YWx1ZV9hcz09XCJ0ZXh0XCIpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHN0YXJ0X3ZhbC5odG1sKG1pbl9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRlbmRfdmFsLmh0bWwobWF4X2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pIHRoaW5rIHRoZSBmdW5jdGlvbiB0aGF0IGJ1aWxkcyB0aGUgVVJMIG5lZWRzIHRvIGRlY29kZSB0aGUgZm9ybWF0dGVkIHN0cmluZyBiZWZvcmUgYWRkaW5nIHRvIHRoZSB1cmxcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKChzZWxmLmF1dG9fdXBkYXRlPT0xKXx8KHNlbGYuYXV0b19jb3VudF9yZWZyZXNoX21vZGU9PTEpKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vb25seSB0cnkgdG8gdXBkYXRlIGlmIHRoZSB2YWx1ZXMgaGF2ZSBhY3R1YWxseSBjaGFuZ2VkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoKHNsaWRlcl9zdGFydF92YWwhPW1pbl9mb3JtYXR0ZWQpfHwoc2xpZGVyX2VuZF92YWwhPW1heF9mb3JtYXR0ZWQpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5pbnB1dFVwZGF0ZSg4MDApO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIHNlbGYuY2xlYXJUaW1lcigpOyAvL2lnbm9yZSBhbnkgY2hhbmdlcyByZWNlbnRseSBtYWRlIGJ5IHRoZSBzbGlkZXIgKHRoaXMgd2FzIGp1c3QgaW5pdCBzaG91bGRuJ3QgY291bnQgYXMgYW4gdXBkYXRlIGV2ZW50KVxuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuaW5pdCA9IGZ1bmN0aW9uKGtlZXBfcGFnaW5hdGlvbilcbiAgICAgICAge1xuICAgICAgICAgICAgaWYodHlwZW9mKGtlZXBfcGFnaW5hdGlvbik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGtlZXBfcGFnaW5hdGlvbiA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLmluaXRBdXRvVXBkYXRlRXZlbnRzKCk7XG4gICAgICAgICAgICB0aGlzLmF0dGFjaEFjdGl2ZUNsYXNzKCk7XG5cbiAgICAgICAgICAgIHRoaXMuYWRkRGF0ZVBpY2tlcnMoKTtcbiAgICAgICAgICAgIHRoaXMuYWRkUmFuZ2VTbGlkZXJzKCk7XG5cbiAgICAgICAgICAgIC8vaW5pdCBjb21ibyBib3hlc1xuICAgICAgICAgICAgdmFyICRjb21ib2JveCA9ICR0aGlzLmZpbmQoXCJzZWxlY3RbZGF0YS1jb21ib2JveD0nMSddXCIpO1xuXG4gICAgICAgICAgICBpZigkY29tYm9ib3gubGVuZ3RoPjApXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgJGNvbWJvYm94LmVhY2goZnVuY3Rpb24oaW5kZXggKXtcbiAgICAgICAgICAgICAgICAgICAgdmFyICR0aGlzY2IgPSAkKCB0aGlzICk7XG4gICAgICAgICAgICAgICAgICAgIHZhciBucm0gPSAkdGhpc2NiLmF0dHIoXCJkYXRhLWNvbWJvYm94LW5ybVwiKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mICR0aGlzY2IuY2hvc2VuICE9IFwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBjaG9zZW5vcHRpb25zID0ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlYXJjaF9jb250YWluczogdHJ1ZVxuICAgICAgICAgICAgICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoKHR5cGVvZihucm0pIT09XCJ1bmRlZmluZWRcIikmJihucm0pKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjaG9zZW5vcHRpb25zLm5vX3Jlc3VsdHNfdGV4dCA9IG5ybTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHNhZmUgdG8gdXNlIHRoZSBmdW5jdGlvblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9zZWFyY2hfY29udGFpbnNcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKHNlbGYuaXNfcnRsPT0xKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzY2IuYWRkQ2xhc3MoXCJjaG9zZW4tcnRsXCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAkdGhpc2NiLmNob3NlbihjaG9zZW5vcHRpb25zKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAgICAgIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHNlbGVjdDJvcHRpb25zID0ge307XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKHNlbGYuaXNfcnRsPT0xKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGVjdDJvcHRpb25zLmRpciA9IFwicnRsXCI7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBpZigodHlwZW9mKG5ybSkhPT1cInVuZGVmaW5lZFwiKSYmKG5ybSkpe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGVjdDJvcHRpb25zLmxhbmd1YWdlPSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIFwibm9SZXN1bHRzXCI6IGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gbnJtO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNjYi5zZWxlY3QyKHNlbGVjdDJvcHRpb25zKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG5cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWxmLmlzU3VibWl0dGluZyA9IGZhbHNlO1xuXG4gICAgICAgICAgICAvL2lmIGFqYXggaXMgZW5hYmxlZCBpbml0IHRoZSBwYWdpbmF0aW9uXG4gICAgICAgICAgICBpZihzZWxmLmlzX2FqYXg9PTEpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5zZXR1cEFqYXhQYWdpbmF0aW9uKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICR0aGlzLm9uKFwic3VibWl0XCIsIHRoaXMuc3VibWl0Rm9ybSk7XG5cbiAgICAgICAgICAgIHNlbGYuaW5pdFdvb0NvbW1lcmNlQ29udHJvbHMoKTsgLy93b29jb21tZXJjZSBvcmRlcmJ5XG5cbiAgICAgICAgICAgIGlmKGtlZXBfcGFnaW5hdGlvbj09ZmFsc2UpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5sYXN0X3N1Ym1pdF9xdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcyhmYWxzZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLm9uV2luZG93U2Nyb2xsID0gZnVuY3Rpb24oZXZlbnQpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKCghc2VsZi5pc19sb2FkaW5nX21vcmUpICYmICghc2VsZi5pc19tYXhfcGFnZWQpKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciB3aW5kb3dfc2Nyb2xsID0gJCh3aW5kb3cpLnNjcm9sbFRvcCgpO1xuICAgICAgICAgICAgICAgIHZhciB3aW5kb3dfc2Nyb2xsX2JvdHRvbSA9ICQod2luZG93KS5zY3JvbGxUb3AoKSArICQod2luZG93KS5oZWlnaHQoKTtcbiAgICAgICAgICAgICAgICB2YXIgc2Nyb2xsX29mZnNldCA9IHBhcnNlSW50KHNlbGYuaW5maW5pdGVfc2Nyb2xsX3RyaWdnZXJfYW1vdW50KTtcblxuICAgICAgICAgICAgICAgIGlmKHNlbGYuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIubGVuZ3RoPT0xKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdHNfc2Nyb2xsX2JvdHRvbSA9IHNlbGYuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIub2Zmc2V0KCkudG9wICsgc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lci5oZWlnaHQoKTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgb2Zmc2V0ID0gKHNlbGYuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIub2Zmc2V0KCkudG9wICsgc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lci5oZWlnaHQoKSkgLSB3aW5kb3dfc2Nyb2xsO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKHdpbmRvd19zY3JvbGxfYm90dG9tID4gcmVzdWx0c19zY3JvbGxfYm90dG9tICsgc2Nyb2xsX29mZnNldClcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5sb2FkTW9yZVJlc3VsdHMoKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAgICAgIHsvL2RvbnQgbG9hZCBtb3JlXG5cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc3RyaXBRdWVyeVN0cmluZ0FuZEhhc2hGcm9tUGF0aCA9IGZ1bmN0aW9uKHVybCkge1xuICAgICAgICAgICAgcmV0dXJuIHVybC5zcGxpdChcIj9cIilbMF0uc3BsaXQoXCIjXCIpWzBdO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5ndXAgPSBmdW5jdGlvbiggbmFtZSwgdXJsICkge1xuICAgICAgICAgICAgaWYgKCF1cmwpIHVybCA9IGxvY2F0aW9uLmhyZWZcbiAgICAgICAgICAgIG5hbWUgPSBuYW1lLnJlcGxhY2UoL1tcXFtdLyxcIlxcXFxcXFtcIikucmVwbGFjZSgvW1xcXV0vLFwiXFxcXFxcXVwiKTtcbiAgICAgICAgICAgIHZhciByZWdleFMgPSBcIltcXFxcPyZdXCIrbmFtZStcIj0oW14mI10qKVwiO1xuICAgICAgICAgICAgdmFyIHJlZ2V4ID0gbmV3IFJlZ0V4cCggcmVnZXhTICk7XG4gICAgICAgICAgICB2YXIgcmVzdWx0cyA9IHJlZ2V4LmV4ZWMoIHVybCApO1xuICAgICAgICAgICAgcmV0dXJuIHJlc3VsdHMgPT0gbnVsbCA/IG51bGwgOiByZXN1bHRzWzFdO1xuICAgICAgICB9O1xuXG5cbiAgICAgICAgdGhpcy5nZXRVcmxQYXJhbXMgPSBmdW5jdGlvbihrZWVwX3BhZ2luYXRpb24sIHR5cGUsIGV4Y2x1ZGUpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHR5cGVvZihrZWVwX3BhZ2luYXRpb24pPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBrZWVwX3BhZ2luYXRpb24gPSB0cnVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZih0eXBlb2YodHlwZSk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIHR5cGUgPSBcIlwiO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgdXJsX3BhcmFtc19zdHIgPSBcIlwiO1xuXG4gICAgICAgICAgICAvLyBnZXQgYWxsIHBhcmFtcyBmcm9tIGZpZWxkc1xuICAgICAgICAgICAgdmFyIHVybF9wYXJhbXNfYXJyYXkgPSBwcm9jZXNzX2Zvcm0uZ2V0VXJsUGFyYW1zKHNlbGYpO1xuXG4gICAgICAgICAgICB2YXIgbGVuZ3RoID0gT2JqZWN0LmtleXModXJsX3BhcmFtc19hcnJheSkubGVuZ3RoO1xuICAgICAgICAgICAgdmFyIGNvdW50ID0gMDtcblxuICAgICAgICAgICAgaWYodHlwZW9mKGV4Y2x1ZGUpIT1cInVuZGVmaW5lZFwiKSB7XG4gICAgICAgICAgICAgICAgaWYgKHVybF9wYXJhbXNfYXJyYXkuaGFzT3duUHJvcGVydHkoZXhjbHVkZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgbGVuZ3RoLS07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihsZW5ndGg+MClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBmb3IgKHZhciBrIGluIHVybF9wYXJhbXNfYXJyYXkpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHVybF9wYXJhbXNfYXJyYXkuaGFzT3duUHJvcGVydHkoaykpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGNhbl9hZGQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYodHlwZW9mKGV4Y2x1ZGUpIT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGs9PWV4Y2x1ZGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2FuX2FkZCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoY2FuX2FkZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybF9wYXJhbXNfc3RyICs9IGsgKyBcIj1cIiArIHVybF9wYXJhbXNfYXJyYXlba107XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoY291bnQgPCBsZW5ndGggLSAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybF9wYXJhbXNfc3RyICs9IFwiJlwiO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvdW50Kys7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBxdWVyeV9wYXJhbXMgPSBcIlwiO1xuXG4gICAgICAgICAgICAvL2Zvcm0gcGFyYW1zIGFzIHVybCBxdWVyeSBzdHJpbmdcbiAgICAgICAgICAgIHZhciBmb3JtX3BhcmFtcyA9IHVybF9wYXJhbXNfc3RyO1xuXG4gICAgICAgICAgICAvL2dldCB1cmwgcGFyYW1zIGZyb20gdGhlIGZvcm0gaXRzZWxmICh3aGF0IHRoZSB1c2VyIGhhcyBzZWxlY3RlZClcbiAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgZm9ybV9wYXJhbXMpO1xuXG4gICAgICAgICAgICAvL2FkZCBwYWdpbmF0aW9uXG4gICAgICAgICAgICBpZihrZWVwX3BhZ2luYXRpb249PXRydWUpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIHBhZ2VOdW1iZXIgPSBzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmF0dHIoXCJkYXRhLXBhZ2VkXCIpO1xuXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mKHBhZ2VOdW1iZXIpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgcGFnZU51bWJlciA9IDE7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYocGFnZU51bWJlcj4xKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBcInNmX3BhZ2VkPVwiK3BhZ2VOdW1iZXIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9hZGQgc2ZpZFxuICAgICAgICAgICAgLy9xdWVyeV9wYXJhbXMgPSBzZWxmLmpvaW5VcmxQYXJhbShxdWVyeV9wYXJhbXMsIFwic2ZpZD1cIitzZWxmLnNmaWQpO1xuXG4gICAgICAgICAgICAvLyBsb29wIHRocm91Z2ggYW55IGV4dHJhIHBhcmFtcyAoZnJvbSBleHQgcGx1Z2lucykgYW5kIGFkZCB0byB0aGUgdXJsIChpZSB3b29jb21tZXJjZSBgb3JkZXJieWApXG4gICAgICAgICAgICAvKnZhciBleHRyYV9xdWVyeV9wYXJhbSA9IFwiXCI7XG4gICAgICAgICAgICAgdmFyIGxlbmd0aCA9IE9iamVjdC5rZXlzKHNlbGYuZXh0cmFfcXVlcnlfcGFyYW1zKS5sZW5ndGg7XG4gICAgICAgICAgICAgdmFyIGNvdW50ID0gMDtcblxuICAgICAgICAgICAgIGlmKGxlbmd0aD4wKVxuICAgICAgICAgICAgIHtcblxuICAgICAgICAgICAgIGZvciAodmFyIGsgaW4gc2VsZi5leHRyYV9xdWVyeV9wYXJhbXMpIHtcbiAgICAgICAgICAgICBpZiAoc2VsZi5leHRyYV9xdWVyeV9wYXJhbXMuaGFzT3duUHJvcGVydHkoaykpIHtcblxuICAgICAgICAgICAgIGlmKHNlbGYuZXh0cmFfcXVlcnlfcGFyYW1zW2tdIT1cIlwiKVxuICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICBleHRyYV9xdWVyeV9wYXJhbSA9IGsrXCI9XCIrc2VsZi5leHRyYV9xdWVyeV9wYXJhbXNba107XG4gICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBleHRyYV9xdWVyeV9wYXJhbSk7XG4gICAgICAgICAgICAgfVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBxdWVyeV9wYXJhbXMgPSBzZWxmLmFkZFF1ZXJ5UGFyYW1zKHF1ZXJ5X3BhcmFtcywgc2VsZi5leHRyYV9xdWVyeV9wYXJhbXMuYWxsKTtcblxuICAgICAgICAgICAgaWYodHlwZSE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAvL3F1ZXJ5X3BhcmFtcyA9IHNlbGYuYWRkUXVlcnlQYXJhbXMocXVlcnlfcGFyYW1zLCBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtc1t0eXBlXSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBxdWVyeV9wYXJhbXM7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5hZGRRdWVyeVBhcmFtcyA9IGZ1bmN0aW9uKHF1ZXJ5X3BhcmFtcywgbmV3X3BhcmFtcylcbiAgICAgICAge1xuICAgICAgICAgICAgdmFyIGV4dHJhX3F1ZXJ5X3BhcmFtID0gXCJcIjtcbiAgICAgICAgICAgIHZhciBsZW5ndGggPSBPYmplY3Qua2V5cyhuZXdfcGFyYW1zKS5sZW5ndGg7XG4gICAgICAgICAgICB2YXIgY291bnQgPSAwO1xuXG4gICAgICAgICAgICBpZihsZW5ndGg+MClcbiAgICAgICAgICAgIHtcblxuICAgICAgICAgICAgICAgIGZvciAodmFyIGsgaW4gbmV3X3BhcmFtcykge1xuICAgICAgICAgICAgICAgICAgICBpZiAobmV3X3BhcmFtcy5oYXNPd25Qcm9wZXJ0eShrKSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihuZXdfcGFyYW1zW2tdIT1cIlwiKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV4dHJhX3F1ZXJ5X3BhcmFtID0gaytcIj1cIituZXdfcGFyYW1zW2tdO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgZXh0cmFfcXVlcnlfcGFyYW0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gcXVlcnlfcGFyYW1zO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuYWRkVXJsUGFyYW0gPSBmdW5jdGlvbih1cmwsIHN0cmluZylcbiAgICAgICAge1xuICAgICAgICAgICAgdmFyIGFkZF9wYXJhbXMgPSBcIlwiO1xuXG4gICAgICAgICAgICBpZih1cmwhPVwiXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaWYodXJsLmluZGV4T2YoXCI/XCIpICE9IC0xKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgYWRkX3BhcmFtcyArPSBcIiZcIjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy91cmwgPSB0aGlzLnRyYWlsaW5nU2xhc2hJdCh1cmwpO1xuICAgICAgICAgICAgICAgICAgICBhZGRfcGFyYW1zICs9IFwiP1wiO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoc3RyaW5nIT1cIlwiKVxuICAgICAgICAgICAge1xuXG4gICAgICAgICAgICAgICAgcmV0dXJuIHVybCArIGFkZF9wYXJhbXMgKyBzdHJpbmc7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHVybDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmpvaW5VcmxQYXJhbSA9IGZ1bmN0aW9uKHBhcmFtcywgc3RyaW5nKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgYWRkX3BhcmFtcyA9IFwiXCI7XG5cbiAgICAgICAgICAgIGlmKHBhcmFtcyE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBhZGRfcGFyYW1zICs9IFwiJlwiO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihzdHJpbmchPVwiXCIpXG4gICAgICAgICAgICB7XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gcGFyYW1zICsgYWRkX3BhcmFtcyArIHN0cmluZztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gcGFyYW1zO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuc2V0QWpheFJlc3VsdHNVUkxzID0gZnVuY3Rpb24ocXVlcnlfcGFyYW1zKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZih0eXBlb2Yoc2VsZi5hamF4X3Jlc3VsdHNfY29uZik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5hamF4X3Jlc3VsdHNfY29uZiA9IG5ldyBBcnJheSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddID0gXCJcIjtcbiAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ10gPSBcIlwiO1xuICAgICAgICAgICAgc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsnZGF0YV90eXBlJ10gPSBcIlwiO1xuXG4gICAgICAgICAgICAvL2lmKHNlbGYuYWpheF91cmwhPVwiXCIpXG4gICAgICAgICAgICBpZihzZWxmLmRpc3BsYXlfcmVzdWx0X21ldGhvZD09XCJzaG9ydGNvZGVcIilcbiAgICAgICAgICAgIHsvL3RoZW4gd2Ugd2FudCB0byBkbyBhIHJlcXVlc3QgdG8gdGhlIGFqYXggZW5kcG9pbnRcbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydyZXN1bHRzX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShzZWxmLnJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuXG4gICAgICAgICAgICAgICAgLy9hZGQgbGFuZyBjb2RlIHRvIGFqYXggYXBpIHJlcXVlc3QsIGxhbmcgY29kZSBzaG91bGQgYWxyZWFkeSBiZSBpbiB0aGVyZSBmb3Igb3RoZXIgcmVxdWVzdHMgKGllLCBzdXBwbGllZCBpbiB0aGUgUmVzdWx0cyBVUkwpXG5cbiAgICAgICAgICAgICAgICBpZihzZWxmLmxhbmdfY29kZSE9XCJcIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIC8vc28gYWRkIGl0XG4gICAgICAgICAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgXCJsYW5nPVwiK3NlbGYubGFuZ19jb2RlKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShzZWxmLmFqYXhfdXJsLCBxdWVyeV9wYXJhbXMpO1xuICAgICAgICAgICAgICAgIC8vc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsnZGF0YV90eXBlJ10gPSAnanNvbic7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2UgaWYoc2VsZi5kaXNwbGF5X3Jlc3VsdF9tZXRob2Q9PVwicG9zdF90eXBlX2FyY2hpdmVcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBwcm9jZXNzX2Zvcm0uc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgdmFyIHJlc3VsdHNfdXJsID0gcHJvY2Vzc19mb3JtLmdldFJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG5cbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydyZXN1bHRzX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShyZXN1bHRzX3VybCwgcXVlcnlfcGFyYW1zKTtcbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShyZXN1bHRzX3VybCwgcXVlcnlfcGFyYW1zKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihzZWxmLmRpc3BsYXlfcmVzdWx0X21ldGhvZD09XCJjdXN0b21fd29vY29tbWVyY2Vfc3RvcmVcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBwcm9jZXNzX2Zvcm0uc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgdmFyIHJlc3VsdHNfdXJsID0gcHJvY2Vzc19mb3JtLmdldFJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG5cbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydyZXN1bHRzX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShyZXN1bHRzX3VybCwgcXVlcnlfcGFyYW1zKTtcbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShyZXN1bHRzX3VybCwgcXVlcnlfcGFyYW1zKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgey8vb3RoZXJ3aXNlIHdlIHdhbnQgdG8gcHVsbCB0aGUgcmVzdWx0cyBkaXJlY3RseSBmcm9tIHRoZSByZXN1bHRzIHBhZ2VcbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydyZXN1bHRzX3VybCddID0gc2VsZi5hZGRVcmxQYXJhbShzZWxmLnJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Byb2Nlc3NpbmdfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHNlbGYuYWpheF91cmwsIHF1ZXJ5X3BhcmFtcyk7XG4gICAgICAgICAgICAgICAgLy9zZWxmLmFqYXhfcmVzdWx0c19jb25mWydkYXRhX3R5cGUnXSA9ICdodG1sJztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsncHJvY2Vzc2luZ191cmwnXSA9IHNlbGYuYWRkUXVlcnlQYXJhbXMoc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsncHJvY2Vzc2luZ191cmwnXSwgc2VsZi5leHRyYV9xdWVyeV9wYXJhbXNbJ2FqYXgnXSk7XG5cbiAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ2RhdGFfdHlwZSddID0gc2VsZi5hamF4X2RhdGFfdHlwZTtcbiAgICAgICAgfTtcblxuXG5cbiAgICAgICAgdGhpcy51cGRhdGVMb2FkZXJUYWcgPSBmdW5jdGlvbigkb2JqZWN0KSB7XG5cbiAgICAgICAgICAgIHZhciAkcGFyZW50O1xuXG4gICAgICAgICAgICBpZihzZWxmLmluZmluaXRlX3Njcm9sbF9yZXN1bHRfY2xhc3MhPVwiXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgJHBhcmVudCA9IHNlbGYuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIuZmluZChzZWxmLmluZmluaXRlX3Njcm9sbF9yZXN1bHRfY2xhc3MpLmxhc3QoKS5wYXJlbnQoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkcGFyZW50ID0gc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lcjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIHRhZ05hbWUgPSAkcGFyZW50LnByb3AoXCJ0YWdOYW1lXCIpO1xuXG4gICAgICAgICAgICB2YXIgdGFnVHlwZSA9ICdkaXYnO1xuICAgICAgICAgICAgaWYoICggdGFnTmFtZS50b0xvd2VyQ2FzZSgpID09ICdvbCcgKSB8fCAoIHRhZ05hbWUudG9Mb3dlckNhc2UoKSA9PSAndWwnICkgKXtcbiAgICAgICAgICAgICAgICB0YWdUeXBlID0gJ2xpJztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyICRuZXcgPSAkKCc8Jyt0YWdUeXBlKycgLz4nKS5odG1sKCRvYmplY3QuaHRtbCgpKTtcbiAgICAgICAgICAgIHZhciBhdHRyaWJ1dGVzID0gJG9iamVjdC5wcm9wKFwiYXR0cmlidXRlc1wiKTtcblxuICAgICAgICAgICAgLy8gbG9vcCB0aHJvdWdoIDxzZWxlY3Q+IGF0dHJpYnV0ZXMgYW5kIGFwcGx5IHRoZW0gb24gPGRpdj5cbiAgICAgICAgICAgICQuZWFjaChhdHRyaWJ1dGVzLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAkbmV3LmF0dHIodGhpcy5uYW1lLCB0aGlzLnZhbHVlKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gJG5ldztcblxuICAgICAgICB9XG5cblxuICAgICAgICB0aGlzLmxvYWRNb3JlUmVzdWx0cyA9IGZ1bmN0aW9uKClcbiAgICAgICAge1xuICAgICAgICAgICAgaWYgKCB0aGlzLmlzX21heF9wYWdlZCA9PT0gdHJ1ZSApIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBzZWxmLmlzX2xvYWRpbmdfbW9yZSA9IHRydWU7XG5cbiAgICAgICAgICAgIC8vdHJpZ2dlciBzdGFydCBldmVudFxuICAgICAgICAgICAgdmFyIGV2ZW50X2RhdGEgPSB7XG4gICAgICAgICAgICAgICAgc2ZpZDogc2VsZi5zZmlkLFxuICAgICAgICAgICAgICAgIHRhcmdldFNlbGVjdG9yOiBzZWxmLmFqYXhfdGFyZ2V0X2F0dHIsXG4gICAgICAgICAgICAgICAgdHlwZTogXCJsb2FkX21vcmVcIixcbiAgICAgICAgICAgICAgICBvYmplY3Q6IHNlbGZcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIHNlbGYudHJpZ2dlckV2ZW50KFwic2Y6YWpheHN0YXJ0XCIsIGV2ZW50X2RhdGEpO1xuICAgICAgICAgICAgcHJvY2Vzc19mb3JtLnNldFRheEFyY2hpdmVSZXN1bHRzVXJsKHNlbGYsIHNlbGYucmVzdWx0c191cmwpO1xuICAgICAgICAgICAgdmFyIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKHRydWUpO1xuICAgICAgICAgICAgc2VsZi5sYXN0X3N1Ym1pdF9xdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcyhmYWxzZSk7IC8vZ3JhYiBhIGNvcHkgb2YgaHRlIFVSTCBwYXJhbXMgd2l0aG91dCBwYWdpbmF0aW9uIGFscmVhZHkgYWRkZWRcblxuICAgICAgICAgICAgdmFyIGFqYXhfcHJvY2Vzc2luZ191cmwgPSBcIlwiO1xuICAgICAgICAgICAgdmFyIGFqYXhfcmVzdWx0c191cmwgPSBcIlwiO1xuICAgICAgICAgICAgdmFyIGRhdGFfdHlwZSA9IFwiXCI7XG5cblxuICAgICAgICAgICAgLy9ub3cgYWRkIHRoZSBuZXcgcGFnaW5hdGlvblxuICAgICAgICAgICAgdmFyIG5leHRfcGFnZWRfbnVtYmVyID0gdGhpcy5jdXJyZW50X3BhZ2VkICsgMTtcbiAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgXCJzZl9wYWdlZD1cIituZXh0X3BhZ2VkX251bWJlcik7XG5cbiAgICAgICAgICAgIHNlbGYuc2V0QWpheFJlc3VsdHNVUkxzKHF1ZXJ5X3BhcmFtcyk7XG4gICAgICAgICAgICBhamF4X3Byb2Nlc3NpbmdfdXJsID0gc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsncHJvY2Vzc2luZ191cmwnXTtcbiAgICAgICAgICAgIGFqYXhfcmVzdWx0c191cmwgPSBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydyZXN1bHRzX3VybCddO1xuICAgICAgICAgICAgZGF0YV90eXBlID0gc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsnZGF0YV90eXBlJ107XG5cbiAgICAgICAgICAgIC8vYWJvcnQgYW55IHByZXZpb3VzIGFqYXggcmVxdWVzdHNcbiAgICAgICAgICAgIGlmKHNlbGYubGFzdF9hamF4X3JlcXVlc3QpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5sYXN0X2FqYXhfcmVxdWVzdC5hYm9ydCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihzZWxmLnVzZV9zY3JvbGxfbG9hZGVyPT0xKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciAkbG9hZGVyID0gJCgnPGRpdi8+Jyx7XG4gICAgICAgICAgICAgICAgICAgICdjbGFzcyc6ICdzZWFyY2gtZmlsdGVyLXNjcm9sbC1sb2FkaW5nJ1xuICAgICAgICAgICAgICAgIH0pOy8vLmFwcGVuZFRvKHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIpO1xuXG4gICAgICAgICAgICAgICAgJGxvYWRlciA9IHNlbGYudXBkYXRlTG9hZGVyVGFnKCRsb2FkZXIpO1xuXG4gICAgICAgICAgICAgICAgc2VsZi5pbmZpbml0ZVNjcm9sbEFwcGVuZCgkbG9hZGVyKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHNlbGYubGFzdF9hamF4X3JlcXVlc3QgPSAkLmdldChhamF4X3Byb2Nlc3NpbmdfdXJsLCBmdW5jdGlvbihkYXRhLCBzdGF0dXMsIHJlcXVlc3QpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5jdXJyZW50X3BhZ2VkKys7XG4gICAgICAgICAgICAgICAgc2VsZi5sYXN0X2FqYXhfcmVxdWVzdCA9IG51bGw7XG5cbiAgICAgICAgICAgICAgICAvLyAqKioqKioqKioqKioqKlxuICAgICAgICAgICAgICAgIC8vIFRPRE8gLSBQQVNURSBUSElTIEFORCBXQVRDSCBUSEUgUkVESVJFQ1QgLSBPTkxZIEhBUFBFTlMgV0lUSCBXQyAoQ1BUIEFORCBUQVggRE9FUyBOT1QpXG4gICAgICAgICAgICAgICAgLy8gaHR0cHM6Ly9zZWFyY2gtZmlsdGVyLnRlc3QvcHJvZHVjdC1jYXRlZ29yeS9jbG90aGluZy90c2hpcnRzL3BhZ2UvMy8/c2ZfcGFnZWQ9M1xuXG4gICAgICAgICAgICAgICAgLy91cGRhdGVzIHRoZSByZXN1dGxzICYgZm9ybSBodG1sXG4gICAgICAgICAgICAgICAgc2VsZi5hZGRSZXN1bHRzKGRhdGEsIGRhdGFfdHlwZSk7XG5cbiAgICAgICAgICAgIH0sIGRhdGFfdHlwZSkuZmFpbChmdW5jdGlvbihqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICBkYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgICAgICAgICAgZGF0YS5vYmplY3QgPSBzZWxmO1xuICAgICAgICAgICAgICAgIGRhdGEudGFyZ2V0U2VsZWN0b3IgPSBzZWxmLmFqYXhfdGFyZ2V0X2F0dHI7XG4gICAgICAgICAgICAgICAgZGF0YS5hamF4VVJMID0gYWpheF9wcm9jZXNzaW5nX3VybDtcbiAgICAgICAgICAgICAgICBkYXRhLmpxWEhSID0ganFYSFI7XG4gICAgICAgICAgICAgICAgZGF0YS50ZXh0U3RhdHVzID0gdGV4dFN0YXR1cztcbiAgICAgICAgICAgICAgICBkYXRhLmVycm9yVGhyb3duID0gZXJyb3JUaHJvd247XG4gICAgICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4ZXJyb3JcIiwgZGF0YSk7XG5cbiAgICAgICAgICAgIH0pLmFsd2F5cyhmdW5jdGlvbigpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICBkYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgICAgICAgICAgZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgICAgICAgICBkYXRhLm9iamVjdCA9IHNlbGY7XG5cbiAgICAgICAgICAgICAgICBpZihzZWxmLnVzZV9zY3JvbGxfbG9hZGVyPT0xKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgJGxvYWRlci5kZXRhY2goKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBzZWxmLmlzX2xvYWRpbmdfbW9yZSA9IGZhbHNlO1xuXG4gICAgICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4ZmluaXNoXCIsIGRhdGEpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfVxuICAgICAgICB0aGlzLmZldGNoQWpheFJlc3VsdHMgPSBmdW5jdGlvbigpXG4gICAgICAgIHtcbiAgICAgICAgICAgIC8vdHJpZ2dlciBzdGFydCBldmVudFxuICAgICAgICAgICAgdmFyIGV2ZW50X2RhdGEgPSB7XG4gICAgICAgICAgICAgICAgc2ZpZDogc2VsZi5zZmlkLFxuICAgICAgICAgICAgICAgIHRhcmdldFNlbGVjdG9yOiBzZWxmLmFqYXhfdGFyZ2V0X2F0dHIsXG4gICAgICAgICAgICAgICAgdHlwZTogXCJsb2FkX3Jlc3VsdHNcIixcbiAgICAgICAgICAgICAgICBvYmplY3Q6IHNlbGZcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIHNlbGYudHJpZ2dlckV2ZW50KFwic2Y6YWpheHN0YXJ0XCIsIGV2ZW50X2RhdGEpO1xuXG4gICAgICAgICAgICAvL3JlZm9jdXMgYW55IGlucHV0IGZpZWxkcyBhZnRlciB0aGUgZm9ybSBoYXMgYmVlbiB1cGRhdGVkXG4gICAgICAgICAgICB2YXIgJGxhc3RfYWN0aXZlX2lucHV0X3RleHQgPSAkdGhpcy5maW5kKCdpbnB1dFt0eXBlPVwidGV4dFwiXTpmb2N1cycpLm5vdChcIi5zZi1kYXRlcGlja2VyXCIpO1xuICAgICAgICAgICAgaWYoJGxhc3RfYWN0aXZlX2lucHV0X3RleHQubGVuZ3RoPT0xKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBsYXN0X2FjdGl2ZV9pbnB1dF90ZXh0ID0gJGxhc3RfYWN0aXZlX2lucHV0X3RleHQuYXR0cihcIm5hbWVcIik7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICR0aGlzLmFkZENsYXNzKFwic2VhcmNoLWZpbHRlci1kaXNhYmxlZFwiKTtcbiAgICAgICAgICAgIHByb2Nlc3NfZm9ybS5kaXNhYmxlSW5wdXRzKHNlbGYpO1xuXG4gICAgICAgICAgICAvL2ZhZGUgb3V0IHJlc3VsdHNcbiAgICAgICAgICAgIHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuYW5pbWF0ZSh7IG9wYWNpdHk6IDAuNSB9LCBcImZhc3RcIik7IC8vbG9hZGluZ1xuICAgICAgICAgICAgc2VsZi5mYWRlQ29udGVudEFyZWFzKCBcIm91dFwiICk7XG5cbiAgICAgICAgICAgIGlmKHNlbGYuYWpheF9hY3Rpb249PVwicGFnaW5hdGlvblwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIC8vbmVlZCB0byByZW1vdmUgYWN0aXZlIGZpbHRlciBmcm9tIFVSTFxuXG4gICAgICAgICAgICAgICAgLy9xdWVyeV9wYXJhbXMgPSBzZWxmLmxhc3Rfc3VibWl0X3F1ZXJ5X3BhcmFtcztcblxuICAgICAgICAgICAgICAgIC8vbm93IGFkZCB0aGUgbmV3IHBhZ2luYXRpb25cbiAgICAgICAgICAgICAgICB2YXIgcGFnZU51bWJlciA9IHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuYXR0cihcImRhdGEtcGFnZWRcIik7XG5cbiAgICAgICAgICAgICAgICBpZih0eXBlb2YocGFnZU51bWJlcik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBwYWdlTnVtYmVyID0gMTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgcHJvY2Vzc19mb3JtLnNldFRheEFyY2hpdmVSZXN1bHRzVXJsKHNlbGYsIHNlbGYucmVzdWx0c191cmwpO1xuICAgICAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKGZhbHNlKTtcblxuICAgICAgICAgICAgICAgIGlmKHBhZ2VOdW1iZXI+MSlcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgXCJzZl9wYWdlZD1cIitwYWdlTnVtYmVyKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2UgaWYoc2VsZi5hamF4X2FjdGlvbj09XCJzdWJtaXRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgcXVlcnlfcGFyYW1zID0gc2VsZi5nZXRVcmxQYXJhbXModHJ1ZSk7XG4gICAgICAgICAgICAgICAgc2VsZi5sYXN0X3N1Ym1pdF9xdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcyhmYWxzZSk7IC8vZ3JhYiBhIGNvcHkgb2YgaHRlIFVSTCBwYXJhbXMgd2l0aG91dCBwYWdpbmF0aW9uIGFscmVhZHkgYWRkZWRcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIGFqYXhfcHJvY2Vzc2luZ191cmwgPSBcIlwiO1xuICAgICAgICAgICAgdmFyIGFqYXhfcmVzdWx0c191cmwgPSBcIlwiO1xuICAgICAgICAgICAgdmFyIGRhdGFfdHlwZSA9IFwiXCI7XG5cbiAgICAgICAgICAgIHNlbGYuc2V0QWpheFJlc3VsdHNVUkxzKHF1ZXJ5X3BhcmFtcyk7XG4gICAgICAgICAgICBhamF4X3Byb2Nlc3NpbmdfdXJsID0gc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsncHJvY2Vzc2luZ191cmwnXTtcbiAgICAgICAgICAgIGFqYXhfcmVzdWx0c191cmwgPSBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydyZXN1bHRzX3VybCddO1xuICAgICAgICAgICAgZGF0YV90eXBlID0gc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsnZGF0YV90eXBlJ107XG5cblxuICAgICAgICAgICAgLy9hYm9ydCBhbnkgcHJldmlvdXMgYWpheCByZXF1ZXN0c1xuICAgICAgICAgICAgaWYoc2VsZi5sYXN0X2FqYXhfcmVxdWVzdClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3RfYWpheF9yZXF1ZXN0LmFib3J0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB2YXIgYWpheF9hY3Rpb24gPSBzZWxmLmFqYXhfYWN0aW9uO1xuICAgICAgICAgICAgc2VsZi5sYXN0X2FqYXhfcmVxdWVzdCA9ICQuZ2V0KGFqYXhfcHJvY2Vzc2luZ191cmwsIGZ1bmN0aW9uKGRhdGEsIHN0YXR1cywgcmVxdWVzdClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3RfYWpheF9yZXF1ZXN0ID0gbnVsbDtcblxuICAgICAgICAgICAgICAgIC8vdXBkYXRlcyB0aGUgcmVzdXRscyAmIGZvcm0gaHRtbFxuICAgICAgICAgICAgICAgIHNlbGYudXBkYXRlUmVzdWx0cyhkYXRhLCBkYXRhX3R5cGUpO1xuXG4gICAgICAgICAgICAgICAgLy8gc2Nyb2xsIFxuICAgICAgICAgICAgICAgIC8vIHNldCB0aGUgdmFyIGJhY2sgdG8gd2hhdCBpdCB3YXMgYmVmb3JlIHRoZSBhamF4IHJlcXVlc3QgbmFkIHRoZSBmb3JtIHJlLWluaXRcbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfYWN0aW9uID0gYWpheF9hY3Rpb247XG4gICAgICAgICAgICAgICAgc2VsZi5zY3JvbGxSZXN1bHRzKCBzZWxmLmFqYXhfYWN0aW9uICk7XG5cbiAgICAgICAgICAgICAgICAvKiB1cGRhdGUgVVJMICovXG4gICAgICAgICAgICAgICAgLy91cGRhdGUgdXJsIGJlZm9yZSBwYWdpbmF0aW9uLCBiZWNhdXNlIHdlIG5lZWQgdG8gZG8gc29tZSBjaGVja3MgYWdhaW5zIHRoZSBVUkwgZm9yIGluZmluaXRlIHNjcm9sbFxuICAgICAgICAgICAgICAgIHNlbGYudXBkYXRlVXJsSGlzdG9yeShhamF4X3Jlc3VsdHNfdXJsKTtcblxuICAgICAgICAgICAgICAgIC8vc2V0dXAgcGFnaW5hdGlvblxuICAgICAgICAgICAgICAgIHNlbGYuc2V0dXBBamF4UGFnaW5hdGlvbigpO1xuXG4gICAgICAgICAgICAgICAgc2VsZi5pc1N1Ym1pdHRpbmcgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgIC8qIHVzZXIgZGVmICovXG4gICAgICAgICAgICAgICAgc2VsZi5pbml0V29vQ29tbWVyY2VDb250cm9scygpOyAvL3dvb2NvbW1lcmNlIG9yZGVyYnlcblxuXG4gICAgICAgICAgICB9LCBkYXRhX3R5cGUpLmZhaWwoZnVuY3Rpb24oanFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBkYXRhID0ge307XG4gICAgICAgICAgICAgICAgZGF0YS5zZmlkID0gc2VsZi5zZmlkO1xuICAgICAgICAgICAgICAgIGRhdGEudGFyZ2V0U2VsZWN0b3IgPSBzZWxmLmFqYXhfdGFyZ2V0X2F0dHI7XG4gICAgICAgICAgICAgICAgZGF0YS5vYmplY3QgPSBzZWxmO1xuICAgICAgICAgICAgICAgIGRhdGEuYWpheFVSTCA9IGFqYXhfcHJvY2Vzc2luZ191cmw7XG4gICAgICAgICAgICAgICAgZGF0YS5qcVhIUiA9IGpxWEhSO1xuICAgICAgICAgICAgICAgIGRhdGEudGV4dFN0YXR1cyA9IHRleHRTdGF0dXM7XG4gICAgICAgICAgICAgICAgZGF0YS5lcnJvclRocm93biA9IGVycm9yVGhyb3duO1xuICAgICAgICAgICAgICAgIHNlbGYuaXNTdWJtaXR0aW5nID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4ZXJyb3JcIiwgZGF0YSk7XG5cbiAgICAgICAgICAgIH0pLmFsd2F5cyhmdW5jdGlvbigpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5zdG9wKHRydWUsdHJ1ZSkuYW5pbWF0ZSh7IG9wYWNpdHk6IDF9LCBcImZhc3RcIik7IC8vZmluaXNoZWQgbG9hZGluZ1xuICAgICAgICAgICAgICAgIHNlbGYuZmFkZUNvbnRlbnRBcmVhcyggXCJpblwiICk7XG4gICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICBkYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgICAgICAgICAgZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgICAgICAgICBkYXRhLm9iamVjdCA9IHNlbGY7XG4gICAgICAgICAgICAgICAgJHRoaXMucmVtb3ZlQ2xhc3MoXCJzZWFyY2gtZmlsdGVyLWRpc2FibGVkXCIpO1xuICAgICAgICAgICAgICAgIHByb2Nlc3NfZm9ybS5lbmFibGVJbnB1dHMoc2VsZik7XG5cbiAgICAgICAgICAgICAgICAvL3JlZm9jdXMgdGhlIGxhc3QgYWN0aXZlIHRleHQgZmllbGRcbiAgICAgICAgICAgICAgICBpZihsYXN0X2FjdGl2ZV9pbnB1dF90ZXh0IT1cIlwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyICRpbnB1dCA9IFtdO1xuICAgICAgICAgICAgICAgICAgICBzZWxmLiRmaWVsZHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGFjdGl2ZV9pbnB1dCA9ICQodGhpcykuZmluZChcImlucHV0W25hbWU9J1wiK2xhc3RfYWN0aXZlX2lucHV0X3RleHQrXCInXVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCRhY3RpdmVfaW5wdXQubGVuZ3RoPT0xKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRpbnB1dCA9ICRhY3RpdmVfaW5wdXQ7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIGlmKCRpbnB1dC5sZW5ndGg9PTEpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJGlucHV0LmZvY3VzKCkudmFsKCRpbnB1dC52YWwoKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmZvY3VzQ2FtcG8oJGlucHV0WzBdKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICR0aGlzLmZpbmQoXCJpbnB1dFtuYW1lPSdfc2Zfc2VhcmNoJ11cIikudHJpZ2dlcignZm9jdXMnKTtcbiAgICAgICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhmaW5pc2hcIiwgIGRhdGEgKTtcblxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5mb2N1c0NhbXBvID0gZnVuY3Rpb24oaW5wdXRGaWVsZCl7XG4gICAgICAgICAgICAvL3ZhciBpbnB1dEZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoaWQpO1xuICAgICAgICAgICAgaWYgKGlucHV0RmllbGQgIT0gbnVsbCAmJiBpbnB1dEZpZWxkLnZhbHVlLmxlbmd0aCAhPSAwKXtcbiAgICAgICAgICAgICAgICBpZiAoaW5wdXRGaWVsZC5jcmVhdGVUZXh0UmFuZ2Upe1xuICAgICAgICAgICAgICAgICAgICB2YXIgRmllbGRSYW5nZSA9IGlucHV0RmllbGQuY3JlYXRlVGV4dFJhbmdlKCk7XG4gICAgICAgICAgICAgICAgICAgIEZpZWxkUmFuZ2UubW92ZVN0YXJ0KCdjaGFyYWN0ZXInLGlucHV0RmllbGQudmFsdWUubGVuZ3RoKTtcbiAgICAgICAgICAgICAgICAgICAgRmllbGRSYW5nZS5jb2xsYXBzZSgpO1xuICAgICAgICAgICAgICAgICAgICBGaWVsZFJhbmdlLnNlbGVjdCgpO1xuICAgICAgICAgICAgICAgIH1lbHNlIGlmIChpbnB1dEZpZWxkLnNlbGVjdGlvblN0YXJ0IHx8IGlucHV0RmllbGQuc2VsZWN0aW9uU3RhcnQgPT0gJzAnKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBlbGVtTGVuID0gaW5wdXRGaWVsZC52YWx1ZS5sZW5ndGg7XG4gICAgICAgICAgICAgICAgICAgIGlucHV0RmllbGQuc2VsZWN0aW9uU3RhcnQgPSBlbGVtTGVuO1xuICAgICAgICAgICAgICAgICAgICBpbnB1dEZpZWxkLnNlbGVjdGlvbkVuZCA9IGVsZW1MZW47XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlucHV0RmllbGQuYmx1cigpO1xuICAgICAgICAgICAgICAgIGlucHV0RmllbGQuZm9jdXMoKTtcbiAgICAgICAgICAgIH0gZWxzZXtcbiAgICAgICAgICAgICAgICBpZiAoIGlucHV0RmllbGQgKSB7XG4gICAgICAgICAgICAgICAgICAgIGlucHV0RmllbGQuZm9jdXMoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnRyaWdnZXJFdmVudCA9IGZ1bmN0aW9uKGV2ZW50bmFtZSwgZGF0YSlcbiAgICAgICAge1xuICAgICAgICAgICAgdmFyICRldmVudF9jb250YWluZXIgPSAkKFwiLnNlYXJjaGFuZGZpbHRlcltkYXRhLXNmLWZvcm0taWQ9J1wiK3NlbGYuc2ZpZCtcIiddXCIpO1xuICAgICAgICAgICAgJGV2ZW50X2NvbnRhaW5lci50cmlnZ2VyKGV2ZW50bmFtZSwgWyBkYXRhIF0pO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5mZXRjaEFqYXhGb3JtID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICAvL3RyaWdnZXIgc3RhcnQgZXZlbnRcbiAgICAgICAgICAgIHZhciBldmVudF9kYXRhID0ge1xuICAgICAgICAgICAgICAgIHNmaWQ6IHNlbGYuc2ZpZCxcbiAgICAgICAgICAgICAgICB0YXJnZXRTZWxlY3Rvcjogc2VsZi5hamF4X3RhcmdldF9hdHRyLFxuICAgICAgICAgICAgICAgIHR5cGU6IFwiZm9ybVwiLFxuICAgICAgICAgICAgICAgIG9iamVjdDogc2VsZlxuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4Zm9ybXN0YXJ0XCIsIFsgZXZlbnRfZGF0YSBdKTtcblxuICAgICAgICAgICAgJHRoaXMuYWRkQ2xhc3MoXCJzZWFyY2gtZmlsdGVyLWRpc2FibGVkXCIpO1xuICAgICAgICAgICAgcHJvY2Vzc19mb3JtLmRpc2FibGVJbnB1dHMoc2VsZik7XG5cbiAgICAgICAgICAgIHZhciBxdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcygpO1xuXG4gICAgICAgICAgICBpZihzZWxmLmxhbmdfY29kZSE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAvL3NvIGFkZCBpdFxuICAgICAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgXCJsYW5nPVwiK3NlbGYubGFuZ19jb2RlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIGFqYXhfcHJvY2Vzc2luZ191cmwgPSBzZWxmLmFkZFVybFBhcmFtKHNlbGYuYWpheF9mb3JtX3VybCwgcXVlcnlfcGFyYW1zKTtcbiAgICAgICAgICAgIHZhciBkYXRhX3R5cGUgPSBcImpzb25cIjtcblxuXG4gICAgICAgICAgICAvL2Fib3J0IGFueSBwcmV2aW91cyBhamF4IHJlcXVlc3RzXG4gICAgICAgICAgICAvKmlmKHNlbGYubGFzdF9hamF4X3JlcXVlc3QpXG4gICAgICAgICAgICAge1xuICAgICAgICAgICAgIHNlbGYubGFzdF9hamF4X3JlcXVlc3QuYWJvcnQoKTtcbiAgICAgICAgICAgICB9Ki9cblxuXG4gICAgICAgICAgICAvL3NlbGYubGFzdF9hamF4X3JlcXVlc3QgPVxuXG4gICAgICAgICAgICAkLmdldChhamF4X3Byb2Nlc3NpbmdfdXJsLCBmdW5jdGlvbihkYXRhLCBzdGF0dXMsIHJlcXVlc3QpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgLy9zZWxmLmxhc3RfYWpheF9yZXF1ZXN0ID0gbnVsbDtcblxuICAgICAgICAgICAgICAgIC8vdXBkYXRlcyB0aGUgcmVzdXRscyAmIGZvcm0gaHRtbFxuICAgICAgICAgICAgICAgIHNlbGYudXBkYXRlRm9ybShkYXRhLCBkYXRhX3R5cGUpO1xuXG5cbiAgICAgICAgICAgIH0sIGRhdGFfdHlwZSkuZmFpbChmdW5jdGlvbihqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICBkYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgICAgICAgICAgZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgICAgICAgICBkYXRhLm9iamVjdCA9IHNlbGY7XG4gICAgICAgICAgICAgICAgZGF0YS5hamF4VVJMID0gYWpheF9wcm9jZXNzaW5nX3VybDtcbiAgICAgICAgICAgICAgICBkYXRhLmpxWEhSID0ganFYSFI7XG4gICAgICAgICAgICAgICAgZGF0YS50ZXh0U3RhdHVzID0gdGV4dFN0YXR1cztcbiAgICAgICAgICAgICAgICBkYXRhLmVycm9yVGhyb3duID0gZXJyb3JUaHJvd247XG4gICAgICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4ZXJyb3JcIiwgWyBkYXRhIF0pO1xuXG4gICAgICAgICAgICB9KS5hbHdheXMoZnVuY3Rpb24oKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBkYXRhID0ge307XG4gICAgICAgICAgICAgICAgZGF0YS5zZmlkID0gc2VsZi5zZmlkO1xuICAgICAgICAgICAgICAgIGRhdGEudGFyZ2V0U2VsZWN0b3IgPSBzZWxmLmFqYXhfdGFyZ2V0X2F0dHI7XG4gICAgICAgICAgICAgICAgZGF0YS5vYmplY3QgPSBzZWxmO1xuXG4gICAgICAgICAgICAgICAgJHRoaXMucmVtb3ZlQ2xhc3MoXCJzZWFyY2gtZmlsdGVyLWRpc2FibGVkXCIpO1xuICAgICAgICAgICAgICAgIHByb2Nlc3NfZm9ybS5lbmFibGVJbnB1dHMoc2VsZik7XG5cbiAgICAgICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhmb3JtZmluaXNoXCIsIFsgZGF0YSBdKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuY29weUxpc3RJdGVtc0NvbnRlbnRzID0gZnVuY3Rpb24oJGxpc3RfZnJvbSwgJGxpc3RfdG8pXG4gICAgICAgIHtcbiAgICAgICAgICAgIC8vY29weSBvdmVyIGNoaWxkIGxpc3QgaXRlbXNcbiAgICAgICAgICAgIHZhciBsaV9jb250ZW50c19hcnJheSA9IG5ldyBBcnJheSgpO1xuICAgICAgICAgICAgdmFyIGZyb21fYXR0cmlidXRlcyA9IG5ldyBBcnJheSgpO1xuXG4gICAgICAgICAgICB2YXIgJGZyb21fZmllbGRzID0gJGxpc3RfZnJvbS5maW5kKFwiPiB1bCA+IGxpXCIpO1xuXG4gICAgICAgICAgICAkZnJvbV9maWVsZHMuZWFjaChmdW5jdGlvbihpKXtcblxuICAgICAgICAgICAgICAgIGxpX2NvbnRlbnRzX2FycmF5LnB1c2goJCh0aGlzKS5odG1sKCkpO1xuXG4gICAgICAgICAgICAgICAgdmFyIGF0dHJpYnV0ZXMgPSAkKHRoaXMpLnByb3AoXCJhdHRyaWJ1dGVzXCIpO1xuICAgICAgICAgICAgICAgIGZyb21fYXR0cmlidXRlcy5wdXNoKGF0dHJpYnV0ZXMpO1xuXG4gICAgICAgICAgICAgICAgLy92YXIgZmllbGRfbmFtZSA9ICQodGhpcykuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcbiAgICAgICAgICAgICAgICAvL3ZhciB0b19maWVsZCA9ICRsaXN0X3RvLmZpbmQoXCI+IHVsID4gbGlbZGF0YS1zZi1maWVsZC1uYW1lPSdcIitmaWVsZF9uYW1lK1wiJ11cIik7XG5cbiAgICAgICAgICAgICAgICAvL3NlbGYuY29weUF0dHJpYnV0ZXMoJCh0aGlzKSwgJGxpc3RfdG8sIFwiZGF0YS1zZi1cIik7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB2YXIgbGlfaXQgPSAwO1xuICAgICAgICAgICAgdmFyICR0b19maWVsZHMgPSAkbGlzdF90by5maW5kKFwiPiB1bCA+IGxpXCIpO1xuICAgICAgICAgICAgJHRvX2ZpZWxkcy5lYWNoKGZ1bmN0aW9uKGkpe1xuICAgICAgICAgICAgICAgICQodGhpcykuaHRtbChsaV9jb250ZW50c19hcnJheVtsaV9pdF0pO1xuXG4gICAgICAgICAgICAgICAgdmFyICRmcm9tX2ZpZWxkID0gJCgkZnJvbV9maWVsZHMuZ2V0KGxpX2l0KSk7XG5cbiAgICAgICAgICAgICAgICB2YXIgJHRvX2ZpZWxkID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICAkdG9fZmllbGQucmVtb3ZlQXR0cihcImRhdGEtc2YtdGF4b25vbXktYXJjaGl2ZVwiKTtcbiAgICAgICAgICAgICAgICBzZWxmLmNvcHlBdHRyaWJ1dGVzKCRmcm9tX2ZpZWxkLCAkdG9fZmllbGQpO1xuXG4gICAgICAgICAgICAgICAgbGlfaXQrKztcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvKnZhciAkZnJvbV9maWVsZHMgPSAkbGlzdF9mcm9tLmZpbmQoXCIgdWwgPiBsaVwiKTtcbiAgICAgICAgICAgICB2YXIgJHRvX2ZpZWxkcyA9ICRsaXN0X3RvLmZpbmQoXCIgPiBsaVwiKTtcbiAgICAgICAgICAgICAkZnJvbV9maWVsZHMuZWFjaChmdW5jdGlvbihpbmRleCwgdmFsKXtcbiAgICAgICAgICAgICBpZigkKHRoaXMpLmhhc0F0dHJpYnV0ZShcImRhdGEtc2YtdGF4b25vbXktYXJjaGl2ZVwiKSlcbiAgICAgICAgICAgICB7XG5cbiAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICB0aGlzLmNvcHlBdHRyaWJ1dGVzKCRsaXN0X2Zyb20sICRsaXN0X3RvKTsqL1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy51cGRhdGVGb3JtQXR0cmlidXRlcyA9IGZ1bmN0aW9uKCRsaXN0X2Zyb20sICRsaXN0X3RvKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgZnJvbV9hdHRyaWJ1dGVzID0gJGxpc3RfZnJvbS5wcm9wKFwiYXR0cmlidXRlc1wiKTtcbiAgICAgICAgICAgIC8vIGxvb3AgdGhyb3VnaCA8c2VsZWN0PiBhdHRyaWJ1dGVzIGFuZCBhcHBseSB0aGVtIG9uIDxkaXY+XG5cbiAgICAgICAgICAgIHZhciB0b19hdHRyaWJ1dGVzID0gJGxpc3RfdG8ucHJvcChcImF0dHJpYnV0ZXNcIik7XG4gICAgICAgICAgICAkLmVhY2godG9fYXR0cmlidXRlcywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJGxpc3RfdG8ucmVtb3ZlQXR0cih0aGlzLm5hbWUpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQuZWFjaChmcm9tX2F0dHJpYnV0ZXMsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICRsaXN0X3RvLmF0dHIodGhpcy5uYW1lLCB0aGlzLnZhbHVlKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmNvcHlBdHRyaWJ1dGVzID0gZnVuY3Rpb24oJGZyb20sICR0bywgcHJlZml4KVxuICAgICAgICB7XG4gICAgICAgICAgICBpZih0eXBlb2YocHJlZml4KT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgcHJlZml4ID0gXCJcIjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIGZyb21fYXR0cmlidXRlcyA9ICRmcm9tLnByb3AoXCJhdHRyaWJ1dGVzXCIpO1xuXG4gICAgICAgICAgICB2YXIgdG9fYXR0cmlidXRlcyA9ICR0by5wcm9wKFwiYXR0cmlidXRlc1wiKTtcbiAgICAgICAgICAgICQuZWFjaCh0b19hdHRyaWJ1dGVzLCBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgICAgIGlmKHByZWZpeCE9XCJcIikge1xuICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5uYW1lLmluZGV4T2YocHJlZml4KSA9PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkdG8ucmVtb3ZlQXR0cih0aGlzLm5hbWUpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIC8vJHRvLnJlbW92ZUF0dHIodGhpcy5uYW1lKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJC5lYWNoKGZyb21fYXR0cmlidXRlcywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJHRvLmF0dHIodGhpcy5uYW1lLCB0aGlzLnZhbHVlKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5jb3B5Rm9ybUF0dHJpYnV0ZXMgPSBmdW5jdGlvbigkZnJvbSwgJHRvKVxuICAgICAgICB7XG4gICAgICAgICAgICAkdG8ucmVtb3ZlQXR0cihcImRhdGEtY3VycmVudC10YXhvbm9teS1hcmNoaXZlXCIpO1xuICAgICAgICAgICAgdGhpcy5jb3B5QXR0cmlidXRlcygkZnJvbSwgJHRvKTtcblxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy51cGRhdGVGb3JtID0gZnVuY3Rpb24oZGF0YSwgZGF0YV90eXBlKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZihkYXRhX3R5cGU9PVwianNvblwiKVxuICAgICAgICAgICAgey8vdGhlbiB3ZSBkaWQgYSByZXF1ZXN0IHRvIHRoZSBhamF4IGVuZHBvaW50LCBzbyBleHBlY3QgYW4gb2JqZWN0IGJhY2tcblxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZihkYXRhWydmb3JtJ10pIT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIC8vcmVtb3ZlIGFsbCBldmVudHMgZnJvbSBTJkYgZm9ybVxuICAgICAgICAgICAgICAgICAgICAkdGhpcy5vZmYoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3JlZnJlc2ggdGhlIGZvcm0gKGF1dG8gY291bnQpXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuY29weUxpc3RJdGVtc0NvbnRlbnRzKCQoZGF0YVsnZm9ybSddKSwgJHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vcmUgaW5pdCBTJkYgY2xhc3Mgb24gdGhlIGZvcm1cbiAgICAgICAgICAgICAgICAgICAgLy8kdGhpcy5zZWFyY2hBbmRGaWx0ZXIoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2lmIGFqYXggaXMgZW5hYmxlZCBpbml0IHRoZSBwYWdpbmF0aW9uXG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5pbml0KHRydWUpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKHNlbGYuaXNfYWpheD09MSlcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5zZXR1cEFqYXhQYWdpbmF0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuXG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cblxuICAgICAgICB9XG4gICAgICAgIHRoaXMuYWRkUmVzdWx0cyA9IGZ1bmN0aW9uKGRhdGEsIGRhdGFfdHlwZSlcbiAgICAgICAge1xuICAgICAgICAgICAgaWYoZGF0YV90eXBlPT1cImpzb25cIilcbiAgICAgICAgICAgIHsvL3RoZW4gd2UgZGlkIGEgcmVxdWVzdCB0byB0aGUgYWpheCBlbmRwb2ludCwgc28gZXhwZWN0IGFuIG9iamVjdCBiYWNrXG4gICAgICAgICAgICAgICAgLy9ncmFiIHRoZSByZXN1bHRzIGFuZCBsb2FkIGluXG4gICAgICAgICAgICAgICAgLy9zZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmFwcGVuZChkYXRhWydyZXN1bHRzJ10pO1xuICAgICAgICAgICAgICAgIHNlbGYubG9hZF9tb3JlX2h0bWwgPSBkYXRhWydyZXN1bHRzJ107XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKGRhdGFfdHlwZT09XCJodG1sXCIpXG4gICAgICAgICAgICB7Ly93ZSBhcmUgZXhwZWN0aW5nIHRoZSBodG1sIG9mIHRoZSByZXN1bHRzIHBhZ2UgYmFjaywgc28gZXh0cmFjdCB0aGUgaHRtbCB3ZSBuZWVkXG4gICAgICAgICAgICAgICAgdmFyICRkYXRhX29iaiA9ICQoZGF0YSk7XG4gICAgICAgICAgICAgICAgc2VsZi5sb2FkX21vcmVfaHRtbCA9ICRkYXRhX29iai5maW5kKHNlbGYuYWpheF90YXJnZXRfYXR0cikuaHRtbCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhcIk5FVyBIVE1MXCIsIHNlbGYubG9hZF9tb3JlX2h0bWwpO1xuXG4gICAgICAgICAgICB2YXIgaW5maW5pdGVfc2Nyb2xsX2VuZCA9IGZhbHNlO1xuXG4gICAgICAgICAgICBpZigkKFwiPGRpdj5cIitzZWxmLmxvYWRfbW9yZV9odG1sK1wiPC9kaXY+XCIpLmZpbmQoXCJbZGF0YS1zZWFyY2gtZmlsdGVyLWFjdGlvbj0naW5maW5pdGUtc2Nyb2xsLWVuZCddXCIpLmxlbmd0aD4wKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIGluZmluaXRlX3Njcm9sbF9lbmQgPSB0cnVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2lmIHRoZXJlIGlzIGFub3RoZXIgc2VsZWN0b3IgZm9yIGluZmluaXRlIHNjcm9sbCwgZmluZCB0aGUgY29udGVudHMgb2YgdGhhdCBpbnN0ZWFkXG4gICAgICAgICAgICBpZihzZWxmLmluZmluaXRlX3Njcm9sbF9jb250YWluZXIhPVwiXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5sb2FkX21vcmVfaHRtbCA9ICQoXCI8ZGl2PlwiK3NlbGYubG9hZF9tb3JlX2h0bWwrXCI8L2Rpdj5cIikuZmluZChzZWxmLmluZmluaXRlX3Njcm9sbF9jb250YWluZXIpLmh0bWwoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmKHNlbGYuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcyE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgJHJlc3VsdF9pdGVtcyA9ICQoXCI8ZGl2PlwiK3NlbGYubG9hZF9tb3JlX2h0bWwrXCI8L2Rpdj5cIikuZmluZChzZWxmLmluZmluaXRlX3Njcm9sbF9yZXN1bHRfY2xhc3MpO1xuICAgICAgICAgICAgICAgIHZhciAkcmVzdWx0X2l0ZW1zX2NvbnRhaW5lciA9ICQoJzxkaXYvPicsIHt9KTtcbiAgICAgICAgICAgICAgICAkcmVzdWx0X2l0ZW1zX2NvbnRhaW5lci5hcHBlbmQoJHJlc3VsdF9pdGVtcyk7XG5cbiAgICAgICAgICAgICAgICBzZWxmLmxvYWRfbW9yZV9odG1sID0gJHJlc3VsdF9pdGVtc19jb250YWluZXIuaHRtbCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihpbmZpbml0ZV9zY3JvbGxfZW5kKVxuICAgICAgICAgICAgey8vd2UgZm91bmQgYSBkYXRhIGF0dHJpYnV0ZSBzaWduYWxsaW5nIHRoZSBsYXN0IHBhZ2Ugc28gZmluaXNoIGhlcmVcblxuICAgICAgICAgICAgICAgIHNlbGYuaXNfbWF4X3BhZ2VkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3RfbG9hZF9tb3JlX2h0bWwgPSBzZWxmLmxvYWRfbW9yZV9odG1sO1xuXG4gICAgICAgICAgICAgICAgc2VsZi5pbmZpbml0ZVNjcm9sbEFwcGVuZChzZWxmLmxvYWRfbW9yZV9odG1sKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihzZWxmLmxhc3RfbG9hZF9tb3JlX2h0bWwhPT1zZWxmLmxvYWRfbW9yZV9odG1sKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIC8vY2hlY2sgdG8gbWFrZSBzdXJlIHRoZSBuZXcgaHRtbCBmZXRjaGVkIGlzIGRpZmZlcmVudFxuICAgICAgICAgICAgICAgIHNlbGYubGFzdF9sb2FkX21vcmVfaHRtbCA9IHNlbGYubG9hZF9tb3JlX2h0bWw7XG4gICAgICAgICAgICAgICAgc2VsZi5pbmZpbml0ZVNjcm9sbEFwcGVuZChzZWxmLmxvYWRfbW9yZV9odG1sKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgey8vd2UgcmVjZWl2ZWQgdGhlIHNhbWUgbWVzc2FnZSBhZ2FpbiBzbyBkb24ndCBhZGQsIGFuZCB0ZWxsIFMmRiB0aGF0IHdlJ3JlIGF0IHRoZSBlbmQuLlxuICAgICAgICAgICAgICAgIHNlbGYuaXNfbWF4X3BhZ2VkID0gdHJ1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG5cbiAgICAgICAgdGhpcy5pbmZpbml0ZVNjcm9sbEFwcGVuZCA9IGZ1bmN0aW9uKCRvYmplY3QpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHNlbGYuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcyE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLiRpbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyLmZpbmQoc2VsZi5pbmZpbml0ZV9zY3JvbGxfcmVzdWx0X2NsYXNzKS5sYXN0KCkuYWZ0ZXIoJG9iamVjdCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICBzZWxmLiRpbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyLmFwcGVuZCgkb2JqZWN0KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG5cbiAgICAgICAgdGhpcy51cGRhdGVSZXN1bHRzID0gZnVuY3Rpb24oZGF0YSwgZGF0YV90eXBlKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZihkYXRhX3R5cGU9PVwianNvblwiKVxuICAgICAgICAgICAgey8vdGhlbiB3ZSBkaWQgYSByZXF1ZXN0IHRvIHRoZSBhamF4IGVuZHBvaW50LCBzbyBleHBlY3QgYW4gb2JqZWN0IGJhY2tcbiAgICAgICAgICAgICAgICAvL2dyYWIgdGhlIHJlc3VsdHMgYW5kIGxvYWQgaW5cbiAgICAgICAgICAgICAgICB0aGlzLnJlc3VsdHNfaHRtbCA9IGRhdGFbJ3Jlc3VsdHMnXTtcblxuICAgICAgICAgICAgICAgIGlmICggdGhpcy5yZXBsYWNlX3Jlc3VsdHMgKSB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuaHRtbCh0aGlzLnJlc3VsdHNfaHRtbCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mKGRhdGFbJ2Zvcm0nXSkhPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgYWxsIGV2ZW50cyBmcm9tIFMmRiBmb3JtXG4gICAgICAgICAgICAgICAgICAgICR0aGlzLm9mZigpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vcmVtb3ZlIHBhZ2luYXRpb25cbiAgICAgICAgICAgICAgICAgICAgc2VsZi5yZW1vdmVBamF4UGFnaW5hdGlvbigpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vcmVmcmVzaCB0aGUgZm9ybSAoYXV0byBjb3VudClcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5jb3B5TGlzdEl0ZW1zQ29udGVudHMoJChkYXRhWydmb3JtJ10pLCAkdGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy91cGRhdGUgYXR0cmlidXRlcyBvbiBmb3JtXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuY29weUZvcm1BdHRyaWJ1dGVzKCQoZGF0YVsnZm9ybSddKSwgJHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vcmUgaW5pdCBTJkYgY2xhc3Mgb24gdGhlIGZvcm1cbiAgICAgICAgICAgICAgICAgICAgJHRoaXMuc2VhcmNoQW5kRmlsdGVyKHsnaXNJbml0JzogZmFsc2V9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy8kdGhpcy5maW5kKFwiaW5wdXRcIikucmVtb3ZlQXR0cihcImRpc2FibGVkXCIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2UgaWYoZGF0YV90eXBlPT1cImh0bWxcIikgey8vd2UgYXJlIGV4cGVjdGluZyB0aGUgaHRtbCBvZiB0aGUgcmVzdWx0cyBwYWdlIGJhY2ssIHNvIGV4dHJhY3QgdGhlIGh0bWwgd2UgbmVlZFxuXG4gICAgICAgICAgICAgICAgdmFyICRkYXRhX29iaiA9ICQoZGF0YSk7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnJlc3VsdHNfaHRtbCA9ICRkYXRhX29iai5maW5kKCB0aGlzLmFqYXhfdGFyZ2V0X2F0dHIgKS5odG1sKCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoIHRoaXMucmVwbGFjZV9yZXN1bHRzICkge1xuICAgICAgICAgICAgICAgICAgICBzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmh0bWwodGhpcy5yZXN1bHRzX2h0bWwpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHNlbGYudXBkYXRlQ29udGVudEFyZWFzKCAkZGF0YV9vYmogKTtcblxuICAgICAgICAgICAgICAgIGlmIChzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmZpbmQoXCIuc2VhcmNoYW5kZmlsdGVyXCIpLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAgICAgey8vdGhlbiB0aGVyZSBhcmUgc2VhcmNoIGZvcm0ocykgaW5zaWRlIHRoZSByZXN1bHRzIGNvbnRhaW5lciwgc28gcmUtaW5pdCB0aGVtXG5cbiAgICAgICAgICAgICAgICAgICAgc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5maW5kKFwiLnNlYXJjaGFuZGZpbHRlclwiKS5zZWFyY2hBbmRGaWx0ZXIoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL2lmIHRoZSBjdXJyZW50IHNlYXJjaCBmb3JtIGlzIG5vdCBpbnNpZGUgdGhlIHJlc3VsdHMgY29udGFpbmVyLCB0aGVuIHByb2NlZWQgYXMgbm9ybWFsIGFuZCB1cGRhdGUgdGhlIGZvcm1cbiAgICAgICAgICAgICAgICBpZihzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmZpbmQoXCIuc2VhcmNoYW5kZmlsdGVyW2RhdGEtc2YtZm9ybS1pZD0nXCIgKyBzZWxmLnNmaWQgKyBcIiddXCIpLmxlbmd0aD09MCkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciAkbmV3X3NlYXJjaF9mb3JtID0gJGRhdGFfb2JqLmZpbmQoXCIuc2VhcmNoYW5kZmlsdGVyW2RhdGEtc2YtZm9ybS1pZD0nXCIgKyBzZWxmLnNmaWQgKyBcIiddXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICgkbmV3X3NlYXJjaF9mb3JtLmxlbmd0aCA9PSAxKSB7Ly90aGVuIHJlcGxhY2UgdGhlIHNlYXJjaCBmb3JtIHdpdGggdGhlIG5ldyBvbmVcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgYWxsIGV2ZW50cyBmcm9tIFMmRiBmb3JtXG4gICAgICAgICAgICAgICAgICAgICAgICAkdGhpcy5vZmYoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgcGFnaW5hdGlvblxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5yZW1vdmVBamF4UGFnaW5hdGlvbigpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3JlZnJlc2ggdGhlIGZvcm0gKGF1dG8gY291bnQpXG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmNvcHlMaXN0SXRlbXNDb250ZW50cygkbmV3X3NlYXJjaF9mb3JtLCAkdGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vdXBkYXRlIGF0dHJpYnV0ZXMgb24gZm9ybVxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5jb3B5Rm9ybUF0dHJpYnV0ZXMoJG5ld19zZWFyY2hfZm9ybSwgJHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3JlIGluaXQgUyZGIGNsYXNzIG9uIHRoZSBmb3JtXG4gICAgICAgICAgICAgICAgICAgICAgICAkdGhpcy5zZWFyY2hBbmRGaWx0ZXIoeydpc0luaXQnOiBmYWxzZX0pO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vJHRoaXMuZmluZChcImlucHV0XCIpLnJlbW92ZUF0dHIoXCJkaXNhYmxlZFwiKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2VsZi5pc19tYXhfcGFnZWQgPSBmYWxzZTsgLy9mb3IgaW5maW5pdGUgc2Nyb2xsXG4gICAgICAgICAgICBzZWxmLmN1cnJlbnRfcGFnZWQgPSAxOyAvL2ZvciBpbmZpbml0ZSBzY3JvbGxcbiAgICAgICAgICAgIHNlbGYuc2V0SW5maW5pdGVTY3JvbGxDb250YWluZXIoKTtcblxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy51cGRhdGVDb250ZW50QXJlYXMgPSBmdW5jdGlvbiggJGh0bWxfZGF0YSApIHtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gYWRkIGFkZGl0aW9uYWwgY29udGVudCBhcmVhc1xuICAgICAgICAgICAgaWYgKCB0aGlzLmFqYXhfdXBkYXRlX3NlY3Rpb25zICYmIHRoaXMuYWpheF91cGRhdGVfc2VjdGlvbnMubGVuZ3RoICkge1xuICAgICAgICAgICAgICAgIGZvciAoaW5kZXggPSAwOyBpbmRleCA8IHRoaXMuYWpheF91cGRhdGVfc2VjdGlvbnMubGVuZ3RoOyArK2luZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBzZWxlY3RvciA9IHRoaXMuYWpheF91cGRhdGVfc2VjdGlvbnNbaW5kZXhdO1xuICAgICAgICAgICAgICAgICAgICAkKCBzZWxlY3RvciApLmh0bWwoICRodG1sX2RhdGEuZmluZCggc2VsZWN0b3IgKS5odG1sKCkgKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5mYWRlQ29udGVudEFyZWFzID0gZnVuY3Rpb24oIGRpcmVjdGlvbiApIHtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgdmFyIG9wYWNpdHkgPSAwLjU7XG4gICAgICAgICAgICBpZiAoIGRpcmVjdGlvbiA9PT0gXCJpblwiICkge1xuICAgICAgICAgICAgICAgIG9wYWNpdHkgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoIHRoaXMuYWpheF91cGRhdGVfc2VjdGlvbnMgJiYgdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9ucy5sZW5ndGggKSB7XG4gICAgICAgICAgICAgICAgZm9yIChpbmRleCA9IDA7IGluZGV4IDwgdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9ucy5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlbGVjdG9yID0gdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9uc1tpbmRleF07XG4gICAgICAgICAgICAgICAgICAgICQoIHNlbGVjdG9yICkuc3RvcCh0cnVlLHRydWUpLmFuaW1hdGUoIHsgb3BhY2l0eTogb3BhY2l0eX0sIFwiZmFzdFwiICk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICBcbiAgICAgICAgICAgIFxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5yZW1vdmVXb29Db21tZXJjZUNvbnRyb2xzID0gZnVuY3Rpb24oKXtcbiAgICAgICAgICAgIHZhciAkd29vX29yZGVyYnkgPSAkKCcud29vY29tbWVyY2Utb3JkZXJpbmcgLm9yZGVyYnknKTtcbiAgICAgICAgICAgIHZhciAkd29vX29yZGVyYnlfZm9ybSA9ICQoJy53b29jb21tZXJjZS1vcmRlcmluZycpO1xuXG4gICAgICAgICAgICAkd29vX29yZGVyYnlfZm9ybS5vZmYoKTtcbiAgICAgICAgICAgICR3b29fb3JkZXJieS5vZmYoKTtcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmFkZFF1ZXJ5UGFyYW0gPSBmdW5jdGlvbihuYW1lLCB2YWx1ZSwgdXJsX3R5cGUpe1xuXG4gICAgICAgICAgICBpZih0eXBlb2YodXJsX3R5cGUpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciB1cmxfdHlwZSA9IFwiYWxsXCI7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtc1t1cmxfdHlwZV1bbmFtZV0gPSB2YWx1ZTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuaW5pdFdvb0NvbW1lcmNlQ29udHJvbHMgPSBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICBzZWxmLnJlbW92ZVdvb0NvbW1lcmNlQ29udHJvbHMoKTtcblxuICAgICAgICAgICAgdmFyICR3b29fb3JkZXJieSA9ICQoJy53b29jb21tZXJjZS1vcmRlcmluZyAub3JkZXJieScpO1xuICAgICAgICAgICAgdmFyICR3b29fb3JkZXJieV9mb3JtID0gJCgnLndvb2NvbW1lcmNlLW9yZGVyaW5nJyk7XG5cbiAgICAgICAgICAgIHZhciBvcmRlcl92YWwgPSBcIlwiO1xuICAgICAgICAgICAgaWYoJHdvb19vcmRlcmJ5Lmxlbmd0aD4wKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIG9yZGVyX3ZhbCA9ICR3b29fb3JkZXJieS52YWwoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBvcmRlcl92YWwgPSBzZWxmLmdldFF1ZXJ5UGFyYW1Gcm9tVVJMKFwib3JkZXJieVwiLCB3aW5kb3cubG9jYXRpb24uaHJlZik7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKG9yZGVyX3ZhbD09XCJtZW51X29yZGVyXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgb3JkZXJfdmFsID0gXCJcIjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoKG9yZGVyX3ZhbCE9XCJcIikmJighIW9yZGVyX3ZhbCkpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5leHRyYV9xdWVyeV9wYXJhbXMuYWxsLm9yZGVyYnkgPSBvcmRlcl92YWw7XG4gICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgJHdvb19vcmRlcmJ5X2Zvcm0ub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGUpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIC8vdmFyIGZvcm0gPSBlLnRhcmdldDtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJHdvb19vcmRlcmJ5Lm9uKFwiY2hhbmdlXCIsIGZ1bmN0aW9uKGUpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgdmFyIHZhbCA9ICQodGhpcykudmFsKCk7XG4gICAgICAgICAgICAgICAgaWYodmFsPT1cIm1lbnVfb3JkZXJcIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHZhbCA9IFwiXCI7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgc2VsZi5leHRyYV9xdWVyeV9wYXJhbXMuYWxsLm9yZGVyYnkgPSB2YWw7XG5cbiAgICAgICAgICAgICAgICAkdGhpcy50cmlnZ2VyKFwic3VibWl0XCIpXG5cbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zY3JvbGxSZXN1bHRzID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZigoc2VsZi5zY3JvbGxfb25fYWN0aW9uPT1zZWxmLmFqYXhfYWN0aW9uKXx8KHNlbGYuc2Nyb2xsX29uX2FjdGlvbj09XCJhbGxcIikpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5zY3JvbGxUb1BvcygpOyAvL3Njcm9sbCB0aGUgd2luZG93IGlmIGl0IGhhcyBiZWVuIHNldFxuICAgICAgICAgICAgICAgIC8vc2VsZi5hamF4X2FjdGlvbiA9IFwiXCI7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnVwZGF0ZVVybEhpc3RvcnkgPSBmdW5jdGlvbihhamF4X3Jlc3VsdHNfdXJsKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgdXNlX2hpc3RvcnlfYXBpID0gMDtcbiAgICAgICAgICAgIGlmICh3aW5kb3cuaGlzdG9yeSAmJiB3aW5kb3cuaGlzdG9yeS5wdXNoU3RhdGUpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdXNlX2hpc3RvcnlfYXBpID0gJHRoaXMuYXR0cihcImRhdGEtdXNlLWhpc3RvcnktYXBpXCIpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZigoc2VsZi51cGRhdGVfYWpheF91cmw9PTEpJiYodXNlX2hpc3RvcnlfYXBpPT0xKSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAvL25vdyBjaGVjayBpZiB0aGUgYnJvd3NlciBzdXBwb3J0cyBoaXN0b3J5IHN0YXRlIHB1c2ggOilcbiAgICAgICAgICAgICAgICBpZiAod2luZG93Lmhpc3RvcnkgJiYgd2luZG93Lmhpc3RvcnkucHVzaFN0YXRlKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaGlzdG9yeS5wdXNoU3RhdGUobnVsbCwgbnVsbCwgYWpheF9yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIHRoaXMucmVtb3ZlQWpheFBhZ2luYXRpb24gPSBmdW5jdGlvbigpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHR5cGVvZihzZWxmLmFqYXhfbGlua3Nfc2VsZWN0b3IpIT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciAkYWpheF9saW5rc19vYmplY3QgPSBqUXVlcnkoc2VsZi5hamF4X2xpbmtzX3NlbGVjdG9yKTtcblxuICAgICAgICAgICAgICAgIGlmKCRhamF4X2xpbmtzX29iamVjdC5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICRhamF4X2xpbmtzX29iamVjdC5vZmYoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmdldEJhc2VVcmwgPSBmdW5jdGlvbiggdXJsICkge1xuICAgICAgICAgICAgLy9ub3cgc2VlIGlmIHdlIGFyZSBvbiB0aGUgVVJMIHdlIHRoaW5rLi4uXG4gICAgICAgICAgICB2YXIgdXJsX3BhcnRzID0gdXJsLnNwbGl0KFwiP1wiKTtcbiAgICAgICAgICAgIHZhciB1cmxfYmFzZSA9IFwiXCI7XG5cbiAgICAgICAgICAgIGlmKHVybF9wYXJ0cy5sZW5ndGg+MClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB1cmxfYmFzZSA9IHVybF9wYXJ0c1swXTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgIHVybF9iYXNlID0gdXJsO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIHVybF9iYXNlO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuY2FuRmV0Y2hBamF4UmVzdWx0cyA9IGZ1bmN0aW9uKGZldGNoX3R5cGUpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHR5cGVvZihmZXRjaF90eXBlKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgZmV0Y2hfdHlwZSA9IFwiXCI7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBmZXRjaF9hamF4X3Jlc3VsdHMgPSBmYWxzZTtcblxuICAgICAgICAgICAgaWYoc2VsZi5pc19hamF4PT0xKVxuICAgICAgICAgICAgey8vdGhlbiB3ZSB3aWxsIGFqYXggc3VibWl0IHRoZSBmb3JtXG5cbiAgICAgICAgICAgICAgICAvL2FuZCBpZiB3ZSBjYW4gZmluZCB0aGUgcmVzdWx0cyBjb250YWluZXJcbiAgICAgICAgICAgICAgICBpZihzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmxlbmd0aD09MSlcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGZldGNoX2FqYXhfcmVzdWx0cyA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdmFyIHJlc3VsdHNfdXJsID0gc2VsZi5yZXN1bHRzX3VybDsgIC8vXG4gICAgICAgICAgICAgICAgdmFyIHJlc3VsdHNfdXJsX2VuY29kZWQgPSAnJzsgIC8vXG4gICAgICAgICAgICAgICAgdmFyIGN1cnJlbnRfdXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWY7XG5cbiAgICAgICAgICAgICAgICAvL2lnbm9yZSAjIGFuZCBldmVyeXRoaW5nIGFmdGVyXG4gICAgICAgICAgICAgICAgdmFyIGhhc2hfcG9zID0gd2luZG93LmxvY2F0aW9uLmhyZWYuaW5kZXhPZignIycpO1xuICAgICAgICAgICAgICAgIGlmKGhhc2hfcG9zIT09LTEpe1xuICAgICAgICAgICAgICAgICAgICBjdXJyZW50X3VybCA9IHdpbmRvdy5sb2NhdGlvbi5ocmVmLnN1YnN0cigwLCB3aW5kb3cubG9jYXRpb24uaHJlZi5pbmRleE9mKCcjJykpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmKCAoICggc2VsZi5kaXNwbGF5X3Jlc3VsdF9tZXRob2Q9PVwiY3VzdG9tX3dvb2NvbW1lcmNlX3N0b3JlXCIgKSB8fCAoIHNlbGYuZGlzcGxheV9yZXN1bHRfbWV0aG9kPT1cInBvc3RfdHlwZV9hcmNoaXZlXCIgKSApICYmICggc2VsZi5lbmFibGVfdGF4b25vbXlfYXJjaGl2ZXMgPT0gMSApIClcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGlmKCBzZWxmLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSAhPT1cIlwiIClcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgZmV0Y2hfYWpheF9yZXN1bHRzID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmZXRjaF9hamF4X3Jlc3VsdHM7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAvKnZhciByZXN1bHRzX3VybCA9IHByb2Nlc3NfZm9ybS5nZXRSZXN1bHRzVXJsKHNlbGYsIHNlbGYucmVzdWx0c191cmwpO1xuICAgICAgICAgICAgICAgICAgICAgdmFyIGFjdGl2ZV90YXggPSBwcm9jZXNzX2Zvcm0uZ2V0QWN0aXZlVGF4KCk7XG4gICAgICAgICAgICAgICAgICAgICB2YXIgcXVlcnlfcGFyYW1zID0gc2VsZi5nZXRVcmxQYXJhbXModHJ1ZSwgJycsIGFjdGl2ZV90YXgpOyovXG4gICAgICAgICAgICAgICAgfVxuXG5cblxuXG4gICAgICAgICAgICAgICAgLy9ub3cgc2VlIGlmIHdlIGFyZSBvbiB0aGUgVVJMIHdlIHRoaW5rLi4uXG4gICAgICAgICAgICAgICAgdmFyIHVybF9iYXNlID0gdGhpcy5nZXRCYXNlVXJsKCBjdXJyZW50X3VybCApO1xuICAgICAgICAgICAgICAgIC8vdmFyIHJlc3VsdHNfdXJsX2Jhc2UgPSB0aGlzLmdldEJhc2VVcmwoIGN1cnJlbnRfdXJsICk7XG5cbiAgICAgICAgICAgICAgICB2YXIgbGFuZyA9IHNlbGYuZ2V0UXVlcnlQYXJhbUZyb21VUkwoXCJsYW5nXCIsIHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICAgICAgICAgICAgICBpZigodHlwZW9mKGxhbmcpIT09XCJ1bmRlZmluZWRcIikmJihsYW5nIT09bnVsbCkpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB1cmxfYmFzZSA9IHNlbGYuYWRkVXJsUGFyYW0odXJsX2Jhc2UsIFwibGFuZz1cIitsYW5nKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB2YXIgc2ZpZCA9IHNlbGYuZ2V0UXVlcnlQYXJhbUZyb21VUkwoXCJzZmlkXCIsIHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcblxuICAgICAgICAgICAgICAgIC8vaWYgc2ZpZCBpcyBhIG51bWJlclxuICAgICAgICAgICAgICAgIGlmKE51bWJlcihwYXJzZUZsb2F0KHNmaWQpKSA9PSBzZmlkKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgdXJsX2Jhc2UgPSBzZWxmLmFkZFVybFBhcmFtKHVybF9iYXNlLCBcInNmaWQ9XCIrc2ZpZCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy9pZiBhbnkgb2YgdGhlIDMgY29uZGl0aW9ucyBhcmUgdHJ1ZSwgdGhlbiBpdHMgZ29vZCB0byBnb1xuICAgICAgICAgICAgICAgIC8vIC0gMSB8IGlmIHRoZSB1cmwgYmFzZSA9PSByZXN1bHRzX3VybFxuICAgICAgICAgICAgICAgIC8vIC0gMiB8IGlmIHVybCBiYXNlKyBcIi9cIiAgPT0gcmVzdWx0c191cmwgLSBpbiBjYXNlIG9mIHVzZXIgZXJyb3IgaW4gdGhlIHJlc3VsdHMgVVJMXG4gICAgICAgICAgICAgICAgLy8gLSAzIHwgaWYgdGhlIHJlc3VsdHMgVVJMIGhhcyB1cmwgcGFyYW1zLCBhbmQgdGhlIGN1cnJlbnQgdXJsIHN0YXJ0cyB3aXRoIHRoZSByZXN1bHRzIFVSTCBcblxuICAgICAgICAgICAgICAgIC8vdHJpbSBhbnkgdHJhaWxpbmcgc2xhc2ggZm9yIGVhc2llciBjb21wYXJpc29uOlxuICAgICAgICAgICAgICAgIHVybF9iYXNlID0gdXJsX2Jhc2UucmVwbGFjZSgvXFwvJC8sICcnKTtcbiAgICAgICAgICAgICAgICByZXN1bHRzX3VybCA9IHJlc3VsdHNfdXJsLnJlcGxhY2UoL1xcLyQvLCAnJyk7XG4gICAgICAgICAgICAgICAgcmVzdWx0c191cmxfZW5jb2RlZCA9IGVuY29kZVVSSShyZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgXG5cbiAgICAgICAgICAgICAgICB2YXIgY3VycmVudF91cmxfY29udGFpbnNfcmVzdWx0c191cmwgPSAtMTtcbiAgICAgICAgICAgICAgICBpZigodXJsX2Jhc2U9PXJlc3VsdHNfdXJsKXx8KHVybF9iYXNlLnRvTG93ZXJDYXNlKCk9PXJlc3VsdHNfdXJsX2VuY29kZWQudG9Mb3dlckNhc2UoKSkgICl7XG4gICAgICAgICAgICAgICAgICAgIGN1cnJlbnRfdXJsX2NvbnRhaW5zX3Jlc3VsdHNfdXJsID0gMTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBpZiAoIHJlc3VsdHNfdXJsLmluZGV4T2YoICc/JyApICE9PSAtMSAmJiBjdXJyZW50X3VybC5sYXN0SW5kZXhPZihyZXN1bHRzX3VybCwgMCkgPT09IDAgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjdXJyZW50X3VybF9jb250YWluc19yZXN1bHRzX3VybCA9IDE7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZihzZWxmLm9ubHlfcmVzdWx0c19hamF4PT0xKVxuICAgICAgICAgICAgICAgIHsvL2lmIGEgdXNlciBoYXMgY2hvc2VuIHRvIG9ubHkgYWxsb3cgYWpheCBvbiByZXN1bHRzIHBhZ2VzIChkZWZhdWx0IGJlaGF2aW91cilcblxuICAgICAgICAgICAgICAgICAgICBpZiggY3VycmVudF91cmxfY29udGFpbnNfcmVzdWx0c191cmwgPiAtMSlcbiAgICAgICAgICAgICAgICAgICAgey8vdGhpcyBtZWFucyB0aGUgY3VycmVudCBVUkwgY29udGFpbnMgdGhlIHJlc3VsdHMgdXJsLCB3aGljaCBtZWFucyB3ZSBjYW4gZG8gYWpheFxuICAgICAgICAgICAgICAgICAgICAgICAgZmV0Y2hfYWpheF9yZXN1bHRzID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZldGNoX2FqYXhfcmVzdWx0cyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGlmKGZldGNoX3R5cGU9PVwicGFnaW5hdGlvblwiKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggY3VycmVudF91cmxfY29udGFpbnNfcmVzdWx0c191cmwgPiAtMSlcbiAgICAgICAgICAgICAgICAgICAgICAgIHsvL3RoaXMgbWVhbnMgdGhlIGN1cnJlbnQgVVJMIGNvbnRhaW5zIHRoZSByZXN1bHRzIHVybCwgd2hpY2ggbWVhbnMgd2UgY2FuIGRvIGFqYXhcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vZG9uJ3QgYWpheCBwYWdpbmF0aW9uIHdoZW4gbm90IG9uIGEgUyZGIHBhZ2VcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBmZXRjaF9hamF4X3Jlc3VsdHMgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGZldGNoX2FqYXhfcmVzdWx0cztcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2V0dXBBamF4UGFnaW5hdGlvbiA9IGZ1bmN0aW9uKClcbiAgICAgICAge1xuICAgICAgICAgICAgLy9pbmZpbml0ZSBzY3JvbGxcbiAgICAgICAgICAgIGlmKHRoaXMucGFnaW5hdGlvbl90eXBlPT09XCJpbmZpbml0ZV9zY3JvbGxcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgaW5maW5pdGVfc2Nyb2xsX2VuZCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIGlmKHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuZmluZChcIltkYXRhLXNlYXJjaC1maWx0ZXItYWN0aW9uPSdpbmZpbml0ZS1zY3JvbGwtZW5kJ11cIikubGVuZ3RoPjApXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBpbmZpbml0ZV9zY3JvbGxfZW5kID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5pc19tYXhfcGFnZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmKHBhcnNlSW50KHRoaXMuaW5zdGFuY2VfbnVtYmVyKT09PTEpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh3aW5kb3cpLm9mZihcInNjcm9sbFwiLCBzZWxmLm9uV2luZG93U2Nyb2xsKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoc2VsZi5jYW5GZXRjaEFqYXhSZXN1bHRzKFwicGFnaW5hdGlvblwiKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh3aW5kb3cpLm9uKFwic2Nyb2xsXCIsIHNlbGYub25XaW5kb3dTY3JvbGwpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZih0eXBlb2Yoc2VsZi5hamF4X2xpbmtzX3NlbGVjdG9yKT09XCJ1bmRlZmluZWRcIikge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICQoZG9jdW1lbnQpLm9mZignY2xpY2snLCBzZWxmLmFqYXhfbGlua3Nfc2VsZWN0b3IpO1xuICAgICAgICAgICAgICAgICQoZG9jdW1lbnQpLm9mZihzZWxmLmFqYXhfbGlua3Nfc2VsZWN0b3IpO1xuICAgICAgICAgICAgICAgICQoc2VsZi5hamF4X2xpbmtzX3NlbGVjdG9yKS5vZmYoKTtcblxuICAgICAgICAgICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHNlbGYuYWpheF9saW5rc19zZWxlY3RvciwgZnVuY3Rpb24oZSl7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5jYW5GZXRjaEFqYXhSZXN1bHRzKFwicGFnaW5hdGlvblwiKSlcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgbGluayA9IGpRdWVyeSh0aGlzKS5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmFqYXhfYWN0aW9uID0gXCJwYWdpbmF0aW9uXCI7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBwYWdlTnVtYmVyID0gc2VsZi5nZXRQYWdlZEZyb21VUkwobGluayk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuYXR0cihcImRhdGEtcGFnZWRcIiwgcGFnZU51bWJlcik7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuZmV0Y2hBamF4UmVzdWx0cygpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmdldFBhZ2VkRnJvbVVSTCA9IGZ1bmN0aW9uKFVSTCl7XG5cbiAgICAgICAgICAgIHZhciBwYWdlZFZhbCA9IDE7XG4gICAgICAgICAgICAvL2ZpcnN0IHRlc3QgdG8gc2VlIGlmIHdlIGhhdmUgXCIvcGFnZS80L1wiIGluIHRoZSBVUkxcbiAgICAgICAgICAgIHZhciB0cFZhbCA9IHNlbGYuZ2V0UXVlcnlQYXJhbUZyb21VUkwoXCJzZl9wYWdlZFwiLCBVUkwpO1xuICAgICAgICAgICAgaWYoKHR5cGVvZih0cFZhbCk9PVwic3RyaW5nXCIpfHwodHlwZW9mKHRwVmFsKT09XCJudW1iZXJcIikpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgcGFnZWRWYWwgPSB0cFZhbDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHBhZ2VkVmFsO1xuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuZ2V0UXVlcnlQYXJhbUZyb21VUkwgPSBmdW5jdGlvbihuYW1lLCBVUkwpe1xuXG4gICAgICAgICAgICB2YXIgcXN0cmluZyA9IFwiP1wiK1VSTC5zcGxpdCgnPycpWzFdO1xuICAgICAgICAgICAgaWYodHlwZW9mKHFzdHJpbmcpIT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciB2YWwgPSBkZWNvZGVVUklDb21wb25lbnQoKG5ldyBSZWdFeHAoJ1s/fCZdJyArIG5hbWUgKyAnPScgKyAnKFteJjtdKz8pKCZ8I3w7fCQpJykuZXhlYyhxc3RyaW5nKXx8WyxcIlwiXSlbMV0ucmVwbGFjZSgvXFwrL2csICclMjAnKSl8fG51bGw7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHZhbDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBcIlwiO1xuICAgICAgICB9O1xuXG5cblxuICAgICAgICB0aGlzLmZvcm1VcGRhdGVkID0gZnVuY3Rpb24oZSl7XG5cbiAgICAgICAgICAgIC8vZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgaWYoc2VsZi5hdXRvX3VwZGF0ZT09MSkge1xuICAgICAgICAgICAgICAgIHNlbGYuc3VibWl0Rm9ybSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZigoc2VsZi5hdXRvX3VwZGF0ZT09MCkmJihzZWxmLmF1dG9fY291bnRfcmVmcmVzaF9tb2RlPT0xKSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmZvcm1VcGRhdGVkRmV0Y2hBamF4KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmZvcm1VcGRhdGVkRmV0Y2hBamF4ID0gZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgLy9sb29wIHRocm91Z2ggYWxsIHRoZSBmaWVsZHMgYW5kIGJ1aWxkIHRoZSBVUkxcbiAgICAgICAgICAgIHNlbGYuZmV0Y2hBamF4Rm9ybSgpO1xuXG5cbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfTtcblxuICAgICAgICAvL21ha2UgYW55IGNvcnJlY3Rpb25zL3VwZGF0ZXMgdG8gZmllbGRzIGJlZm9yZSB0aGUgc3VibWl0IGNvbXBsZXRlc1xuICAgICAgICB0aGlzLnNldEZpZWxkcyA9IGZ1bmN0aW9uKGUpe1xuXG4gICAgICAgICAgICAvL2lmKHNlbGYuaXNfYWpheD09MCkge1xuXG4gICAgICAgICAgICAgICAgLy9zb21ldGltZXMgdGhlIGZvcm0gaXMgc3VibWl0dGVkIHdpdGhvdXQgdGhlIHNsaWRlciB5ZXQgaGF2aW5nIHVwZGF0ZWQsIGFuZCBhcyB3ZSBnZXQgb3VyIHZhbHVlcyBmcm9tXG4gICAgICAgICAgICAgICAgLy90aGUgc2xpZGVyIGFuZCBub3QgaW5wdXRzLCB3ZSBuZWVkIHRvIGNoZWNrIGl0IGlmIG5lZWRzIHRvIGJlIHNldFxuICAgICAgICAgICAgICAgIC8vb25seSBvY2N1cnMgaWYgYWpheCBpcyBvZmYsIGFuZCBhdXRvc3VibWl0IG9uXG4gICAgICAgICAgICAgICAgc2VsZi4kZmllbGRzLmVhY2goZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRmaWVsZCA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIHJhbmdlX2Rpc3BsYXlfdmFsdWVzID0gJGZpZWxkLmZpbmQoJy5zZi1tZXRhLXJhbmdlLXNsaWRlcicpLmF0dHIoXCJkYXRhLWRpc3BsYXktdmFsdWVzLWFzXCIpOy8vZGF0YS1kaXNwbGF5LXZhbHVlcy1hcz1cInRleHRcIlxuXG4gICAgICAgICAgICAgICAgICAgIGlmKHJhbmdlX2Rpc3BsYXlfdmFsdWVzPT09XCJ0ZXh0aW5wdXRcIikge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZigkZmllbGQuZmluZChcIi5tZXRhLXNsaWRlclwiKS5sZW5ndGg+MCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwiLm1ldGEtc2xpZGVyXCIpLmVhY2goZnVuY3Rpb24gKGluZGV4KSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2xpZGVyX29iamVjdCA9ICQodGhpcylbMF07XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRzbGlkZXJfZWwgPSAkKHRoaXMpLmNsb3Nlc3QoXCIuc2YtbWV0YS1yYW5nZS1zbGlkZXJcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy92YXIgbWluVmFsID0gJHNsaWRlcl9lbC5hdHRyKFwiZGF0YS1taW5cIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy92YXIgbWF4VmFsID0gJHNsaWRlcl9lbC5hdHRyKFwiZGF0YS1tYXhcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG1pblZhbCA9ICRzbGlkZXJfZWwuZmluZChcIi5zZi1yYW5nZS1taW5cIikudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG1heFZhbCA9ICRzbGlkZXJfZWwuZmluZChcIi5zZi1yYW5nZS1tYXhcIikudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVyX29iamVjdC5ub1VpU2xpZGVyLnNldChbbWluVmFsLCBtYXhWYWxdKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIC8vfVxuXG4gICAgICAgIH1cblxuICAgICAgICAvL3N1Ym1pdFxuICAgICAgICB0aGlzLnN1Ym1pdEZvcm0gPSBmdW5jdGlvbihlKXtcblxuICAgICAgICAgICAgLy9sb29wIHRocm91Z2ggYWxsIHRoZSBmaWVsZHMgYW5kIGJ1aWxkIHRoZSBVUkxcbiAgICAgICAgICAgIGlmKHNlbGYuaXNTdWJtaXR0aW5nID09IHRydWUpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHNlbGYuc2V0RmllbGRzKCk7XG4gICAgICAgICAgICBzZWxmLmNsZWFyVGltZXIoKTtcblxuICAgICAgICAgICAgc2VsZi5pc1N1Ym1pdHRpbmcgPSB0cnVlO1xuXG4gICAgICAgICAgICBwcm9jZXNzX2Zvcm0uc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG5cbiAgICAgICAgICAgIHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuYXR0cihcImRhdGEtcGFnZWRcIiwgMSk7IC8vaW5pdCBwYWdlZFxuXG4gICAgICAgICAgICBpZihzZWxmLmNhbkZldGNoQWpheFJlc3VsdHMoKSlcbiAgICAgICAgICAgIHsvL3RoZW4gd2Ugd2lsbCBhamF4IHN1Ym1pdCB0aGUgZm9ybVxuXG4gICAgICAgICAgICAgICAgc2VsZi5hamF4X2FjdGlvbiA9IFwic3VibWl0XCI7IC8vc28gd2Uga25vdyBpdCB3YXNuJ3QgcGFnaW5hdGlvblxuICAgICAgICAgICAgICAgIHNlbGYuZmV0Y2hBamF4UmVzdWx0cygpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgey8vdGhlbiB3ZSB3aWxsIHNpbXBseSByZWRpcmVjdCB0byB0aGUgUmVzdWx0cyBVUkxcblxuICAgICAgICAgICAgICAgIHZhciByZXN1bHRzX3VybCA9IHByb2Nlc3NfZm9ybS5nZXRSZXN1bHRzVXJsKHNlbGYsIHNlbGYucmVzdWx0c191cmwpO1xuICAgICAgICAgICAgICAgIHZhciBxdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcyh0cnVlLCAnJyk7XG4gICAgICAgICAgICAgICAgcmVzdWx0c191cmwgPSBzZWxmLmFkZFVybFBhcmFtKHJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuXG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSByZXN1bHRzX3VybDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9O1xuICAgICAgICB0aGlzLnJlc2V0Rm9ybSA9IGZ1bmN0aW9uKHN1Ym1pdF9mb3JtKVxuICAgICAgICB7XG4gICAgICAgICAgICAvL3Vuc2V0IGFsbCBmaWVsZHNcbiAgICAgICAgICAgIHNlbGYuJGZpZWxkcy5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICB2YXIgJGZpZWxkID0gJCh0aGlzKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRmaWVsZC5yZW1vdmVBdHRyKFwiZGF0YS1zZi10YXhvbm9teS1hcmNoaXZlXCIpO1xuXHRcdFx0XHRcbiAgICAgICAgICAgICAgICAvL3N0YW5kYXJkIGZpZWxkIHR5cGVzXG4gICAgICAgICAgICAgICAgJGZpZWxkLmZpbmQoXCJzZWxlY3Q6bm90KFttdWx0aXBsZT0nbXVsdGlwbGUnXSkgPiBvcHRpb246Zmlyc3QtY2hpbGRcIikucHJvcChcInNlbGVjdGVkXCIsIHRydWUpO1xuICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwic2VsZWN0W211bHRpcGxlPSdtdWx0aXBsZSddID4gb3B0aW9uXCIpLnByb3AoXCJzZWxlY3RlZFwiLCBmYWxzZSk7XG4gICAgICAgICAgICAgICAgJGZpZWxkLmZpbmQoXCJpbnB1dFt0eXBlPSdjaGVja2JveCddXCIpLnByb3AoXCJjaGVja2VkXCIsIGZhbHNlKTtcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcIj4gdWwgPiBsaTpmaXJzdC1jaGlsZCBpbnB1dFt0eXBlPSdyYWRpbyddXCIpLnByb3AoXCJjaGVja2VkXCIsIHRydWUpO1xuICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwiaW5wdXRbdHlwZT0ndGV4dCddXCIpLnZhbChcIlwiKTtcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcIi5zZi1vcHRpb24tYWN0aXZlXCIpLnJlbW92ZUNsYXNzKFwic2Ytb3B0aW9uLWFjdGl2ZVwiKTtcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcIj4gdWwgPiBsaTpmaXJzdC1jaGlsZCBpbnB1dFt0eXBlPSdyYWRpbyddXCIpLnBhcmVudCgpLmFkZENsYXNzKFwic2Ytb3B0aW9uLWFjdGl2ZVwiKTsgLy9yZSBhZGQgYWN0aXZlIGNsYXNzIHRvIGZpcnN0IFwiZGVmYXVsdFwiIG9wdGlvblxuXG4gICAgICAgICAgICAgICAgLy9udW1iZXIgcmFuZ2UgLSAyIG51bWJlciBpbnB1dCBmaWVsZHNcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcImlucHV0W3R5cGU9J251bWJlciddXCIpLmVhY2goZnVuY3Rpb24oaW5kZXgpe1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciAkdGhpc0lucHV0ID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICBpZigkdGhpc0lucHV0LnBhcmVudCgpLnBhcmVudCgpLmhhc0NsYXNzKFwic2YtbWV0YS1yYW5nZVwiKSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihpbmRleD09MCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzSW5wdXQudmFsKCR0aGlzSW5wdXQuYXR0cihcIm1pblwiKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBlbHNlIGlmKGluZGV4PT0xKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNJbnB1dC52YWwoJHRoaXNJbnB1dC5hdHRyKFwibWF4XCIpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvL21ldGEgLyBudW1iZXJzIHdpdGggMiBpbnB1dHMgKGZyb20gLyB0byBmaWVsZHMpIC0gc2Vjb25kIGlucHV0IG11c3QgYmUgcmVzZXQgdG8gbWF4IHZhbHVlXG4gICAgICAgICAgICAgICAgdmFyICRtZXRhX3NlbGVjdF9mcm9tX3RvID0gJGZpZWxkLmZpbmQoXCIuc2YtbWV0YS1yYW5nZS1zZWxlY3QtZnJvbXRvXCIpO1xuXG4gICAgICAgICAgICAgICAgaWYoJG1ldGFfc2VsZWN0X2Zyb21fdG8ubGVuZ3RoPjApIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgc3RhcnRfbWluID0gJG1ldGFfc2VsZWN0X2Zyb21fdG8uYXR0cihcImRhdGEtbWluXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgc3RhcnRfbWF4ID0gJG1ldGFfc2VsZWN0X2Zyb21fdG8uYXR0cihcImRhdGEtbWF4XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgICRtZXRhX3NlbGVjdF9mcm9tX3RvLmZpbmQoXCJzZWxlY3RcIikuZWFjaChmdW5jdGlvbihpbmRleCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkdGhpc0lucHV0ID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoaW5kZXg9PTApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzSW5wdXQudmFsKHN0YXJ0X21pbik7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBlbHNlIGlmKGluZGV4PT0xKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNJbnB1dC52YWwoc3RhcnRfbWF4KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB2YXIgJG1ldGFfcmFkaW9fZnJvbV90byA9ICRmaWVsZC5maW5kKFwiLnNmLW1ldGEtcmFuZ2UtcmFkaW8tZnJvbXRvXCIpO1xuXG4gICAgICAgICAgICAgICAgaWYoJG1ldGFfcmFkaW9fZnJvbV90by5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBzdGFydF9taW4gPSAkbWV0YV9yYWRpb19mcm9tX3RvLmF0dHIoXCJkYXRhLW1pblwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHN0YXJ0X21heCA9ICRtZXRhX3JhZGlvX2Zyb21fdG8uYXR0cihcImRhdGEtbWF4XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciAkcmFkaW9fZ3JvdXBzID0gJG1ldGFfcmFkaW9fZnJvbV90by5maW5kKCcuc2YtaW5wdXQtcmFuZ2UtcmFkaW8nKTtcblxuICAgICAgICAgICAgICAgICAgICAkcmFkaW9fZ3JvdXBzLmVhY2goZnVuY3Rpb24oaW5kZXgpe1xuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkcmFkaW9zID0gJCh0aGlzKS5maW5kKFwiLnNmLWlucHV0LXJhZGlvXCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHJhZGlvcy5wcm9wKFwiY2hlY2tlZFwiLCBmYWxzZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKGluZGV4PT0wKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRyYWRpb3MuZmlsdGVyKCdbdmFsdWU9XCInK3N0YXJ0X21pbisnXCJdJykucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBlbHNlIGlmKGluZGV4PT0xKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRyYWRpb3MuZmlsdGVyKCdbdmFsdWU9XCInK3N0YXJ0X21heCsnXCJdJykucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL251bWJlciBzbGlkZXIgLSBub1VpU2xpZGVyXG4gICAgICAgICAgICAgICAgJGZpZWxkLmZpbmQoXCIubWV0YS1zbGlkZXJcIikuZWFjaChmdW5jdGlvbihpbmRleCl7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIHNsaWRlcl9vYmplY3QgPSAkKHRoaXMpWzBdO1xuICAgICAgICAgICAgICAgICAgICAvKnZhciBzbGlkZXJfb2JqZWN0ID0gJGNvbnRhaW5lci5maW5kKFwiLm1ldGEtc2xpZGVyXCIpWzBdO1xuICAgICAgICAgICAgICAgICAgICAgdmFyIHNsaWRlcl92YWwgPSBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuZ2V0KCk7Ki9cblxuICAgICAgICAgICAgICAgICAgICB2YXIgJHNsaWRlcl9lbCA9ICQodGhpcykuY2xvc2VzdChcIi5zZi1tZXRhLXJhbmdlLXNsaWRlclwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1pblZhbCA9ICRzbGlkZXJfZWwuYXR0cihcImRhdGEtbWluXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgbWF4VmFsID0gJHNsaWRlcl9lbC5hdHRyKFwiZGF0YS1tYXhcIik7XG4gICAgICAgICAgICAgICAgICAgIHNsaWRlcl9vYmplY3Qubm9VaVNsaWRlci5zZXQoW21pblZhbCwgbWF4VmFsXSk7XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vbmVlZCB0byBzZWUgaWYgYW55IGFyZSBjb21ib2JveCBhbmQgYWN0IGFjY29yZGluZ2x5XG4gICAgICAgICAgICAgICAgdmFyICRjb21ib2JveCA9ICRmaWVsZC5maW5kKFwic2VsZWN0W2RhdGEtY29tYm9ib3g9JzEnXVwiKTtcbiAgICAgICAgICAgICAgICBpZigkY29tYm9ib3gubGVuZ3RoPjApXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mICRjb21ib2JveC5jaG9zZW4gIT0gXCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGNvbWJvYm94LnRyaWdnZXIoXCJjaG9zZW46dXBkYXRlZFwiKTsgLy9mb3IgY2hvc2VuIG9ubHlcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRjb21ib2JveC52YWwoJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJGNvbWJvYm94LnRyaWdnZXIoJ2NoYW5nZS5zZWxlY3QyJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBzZWxmLmNsZWFyVGltZXIoKTtcblxuXG5cbiAgICAgICAgICAgIGlmKHN1Ym1pdF9mb3JtPT1cImFsd2F5c1wiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHNlbGYuc3VibWl0Rm9ybSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihzdWJtaXRfZm9ybT09XCJuZXZlclwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIGlmKHRoaXMuYXV0b19jb3VudF9yZWZyZXNoX21vZGU9PTEpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBzZWxmLmZvcm1VcGRhdGVkRmV0Y2hBamF4KCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihzdWJtaXRfZm9ybT09XCJhdXRvXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaWYodGhpcy5hdXRvX3VwZGF0ZT09dHJ1ZSlcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc3VibWl0Rm9ybSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBpZih0aGlzLmF1dG9fY291bnRfcmVmcmVzaF9tb2RlPT0xKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmZvcm1VcGRhdGVkRmV0Y2hBamF4KCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmluaXQoKTtcblxuICAgICAgICB2YXIgZXZlbnRfZGF0YSA9IHt9O1xuICAgICAgICBldmVudF9kYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgIGV2ZW50X2RhdGEudGFyZ2V0U2VsZWN0b3IgPSBzZWxmLmFqYXhfdGFyZ2V0X2F0dHI7XG4gICAgICAgIGV2ZW50X2RhdGEub2JqZWN0ID0gdGhpcztcbiAgICAgICAgaWYob3B0cy5pc0luaXQpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHNlbGYudHJpZ2dlckV2ZW50KFwic2Y6aW5pdFwiLCBldmVudF9kYXRhKTtcbiAgICAgICAgfVxuXG4gICAgfSk7XG59O1xuIl19
},{"./process_form":4,"./state":5,"./thirdparty":6,"nouislider":2}],4:[function(require,module,exports){
(function (global){

var $ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null);

module.exports = {

	taxonomy_archives: 0,
    url_params: {},
    tax_archive_results_url: "",
    active_tax: "",
    fields: {},
	init: function(taxonomy_archives, current_taxonomy_archive){

        this.taxonomy_archives = 0;
        this.url_params = {};
        this.tax_archive_results_url = "";
        this.active_tax = "";

		//this.$fields = $fields;
        this.taxonomy_archives = taxonomy_archives;
        this.current_taxonomy_archive = current_taxonomy_archive;

		this.clearUrlComponents();

	},
    setTaxArchiveResultsUrl: function($form, current_results_url, get_active) {

        var self = this;
		this.clearTaxArchiveResultsUrl();
        //var current_results_url = "";
        if(this.taxonomy_archives!=1)
        {
            return;
        }

        if(typeof(get_active)=="undefined")
		{
			var get_active = false;
		}

        //check to see if we have any taxonomies selected
        //if so, check their rewrites and use those as the results url
        var $field = false;
        var field_name = "";
        var field_value = "";

        var $active_taxonomy = $form.$fields.parent().find("[data-sf-taxonomy-archive='1']");
        if($active_taxonomy.length==1)
        {
            $field = $active_taxonomy;

            var fieldType = $field.attr("data-sf-field-type");

            if ((fieldType == "tag") || (fieldType == "category") || (fieldType == "taxonomy")) {
                var taxonomy_value = self.processTaxonomy($field, true);
                field_name = $field.attr("data-sf-field-name");
                var taxonomy_name = field_name.replace("_sft_", "");

                if (taxonomy_value) {
                    field_value = taxonomy_value.value;
                }
            }

            if(field_value=="")
            {
                $field = false;
            }
        }

        if((self.current_taxonomy_archive!="")&&(self.current_taxonomy_archive!=taxonomy_name))
        {

            this.tax_archive_results_url = current_results_url;
            return;
        }

        if(((field_value=="")||(!$field) ))
        {
            $form.$fields.each(function () {

                if (!$field) {

                    var fieldType = $(this).attr("data-sf-field-type");

                    if ((fieldType == "tag") || (fieldType == "category") || (fieldType == "taxonomy")) {
                        var taxonomy_value = self.processTaxonomy($(this), true);
                        field_name = $(this).attr("data-sf-field-name");

                        if (taxonomy_value) {

                            field_value = taxonomy_value.value;

                            if (field_value != "") {

                                $field = $(this);
                            }

                        }
                    }
                }
            });
        }

        if( ($field) && (field_value != "" )) {
            //if we found a field
			var rewrite_attr = ($field.attr("data-sf-term-rewrite"));

            if(rewrite_attr!="") {

                var rewrite = JSON.parse(rewrite_attr);
                var input_type = $field.attr("data-sf-field-input-type");
                self.active_tax = field_name;

                //find the active element
                if ((input_type == "radio") || (input_type == "checkbox")) {

                    //var $active = $field.find(".sf-option-active");
                    //explode the values if there is a delim
                    //field_value

                    var is_single_value = true;
                    var field_values = field_value.split(",").join("+").split("+");
                    if (field_values.length > 1) {
                        is_single_value = false;
                    }

                    if (is_single_value) {

                        var $input = $field.find("input[value='" + field_value + "']");
                        var $active = $input.parent();
                        var depth = $active.attr("data-sf-depth");

                        //now loop through parents to grab their names
                        var values = new Array();
                        values.push(field_value);

                        for (var i = depth; i > 0; i--) {
                            $active = $active.parent().parent();
                            values.push($active.find("input").val());
                        }

                        values.reverse();

                        //grab the rewrite for this depth
                        var active_rewrite = rewrite[depth];
                        var url = active_rewrite;


                        //then map from the parents to the depth
                        $(values).each(function (index, value) {

                            url = url.replace("[" + index + "]", value);

                        });
                        this.tax_archive_results_url = url;
                    }
                    else {

                        //if there are multiple values,
                        //then we need to check for 3 things:

                        //if the values selected are all in the same tree then we can do some clever rewrite stuff
                        //merge all values in same level, then combine the levels

                        //if they are from different trees then just combine them or just use `field_value`
                        /*

                         var depths = new Array();
                         $(field_values).each(function (index, val) {

                         var $input = $field.find("input[value='" + field_value + "']");
                         var $active = $input.parent();

                         var depth = $active.attr("data-sf-depth");
                         //depths.push(depth);

                         });*/

                    }
                }
                else if ((input_type == "select") || (input_type == "multiselect")) {

                    var is_single_value = true;
                    var field_values = field_value.split(",").join("+").split("+");
                    if (field_values.length > 1) {
                        is_single_value = false;
                    }

                    if (is_single_value) {

                        var $active = $field.find("option[value='" + field_value + "']");
                        var depth = $active.attr("data-sf-depth");

                        var values = new Array();
                        values.push(field_value);

                        for (var i = depth; i > 0; i--) {
                            $active = $active.prevAll("option[data-sf-depth='" + (i - 1) + "']");
                            values.push($active.val());
                        }

                        values.reverse();
                        var active_rewrite = rewrite[depth];
                        var url = active_rewrite;
                        $(values).each(function (index, value) {

                            url = url.replace("[" + index + "]", value);

                        });
                        this.tax_archive_results_url = url;
                    }

                }
            }

        }
        //this.tax_archive_results_url = current_results_url;
    },
    getResultsUrl: function($form, current_results_url) {

        //this.setTaxArchiveResultsUrl($form, current_results_url);

        if(this.tax_archive_results_url=="")
        {
            return current_results_url;
        }

        return this.tax_archive_results_url;
    },
	getUrlParams: function($form){

		this.buildUrlComponents($form, true);

        if(this.tax_archive_results_url!="")
        {

            if(this.active_tax!="")
            {
                var field_name = this.active_tax;

                if(typeof(this.url_params[field_name])!="undefined")
                {
                    delete this.url_params[field_name];
                }
            }
        }

		return this.url_params;
	},
	clearUrlComponents: function(){
		//this.url_components = "";
		this.url_params = {};
	},
	clearTaxArchiveResultsUrl: function() {
		this.tax_archive_results_url = '';
	},
	disableInputs: function($form){
		var self = this;
		
		$form.$fields.each(function(){
			
			var $inputs = $(this).find("input, select, .meta-slider");
			$inputs.attr("disabled", "disabled");
			$inputs.attr("disabled", true);
			$inputs.prop("disabled", true);
			$inputs.trigger("chosen:updated");
			
		});
		
		
	},
	enableInputs: function($form){
		var self = this;
		$form.$fields.each(function(){
			var $inputs = $(this).find("input, select, .meta-slider");
			$inputs.prop("disabled", false);
			$inputs.attr("disabled", false);
			$inputs.trigger("chosen:updated");			
		});
		
		
	},
	buildUrlComponents: function($form, clear_components){
		
		var self = this;
		
		if(typeof(clear_components)!="undefined")
		{
			if(clear_components==true)
			{
				this.clearUrlComponents();
			}
		}
		
		$form.$fields.each(function(){
			
			var fieldName = $(this).attr("data-sf-field-name");
			var fieldType = $(this).attr("data-sf-field-type");
			
			if(fieldType=="search")
			{
				self.processSearchField($(this));
			}
			else if((fieldType=="tag")||(fieldType=="category")||(fieldType=="taxonomy"))
			{
				self.processTaxonomy($(this));
			}
			else if(fieldType=="sort_order")
			{
				self.processSortOrderField($(this));
			}
			else if(fieldType=="posts_per_page")
			{
				self.processResultsPerPageField($(this));
			}
			else if(fieldType=="author")
			{
				self.processAuthor($(this));
			}
			else if(fieldType=="post_type")
			{
				self.processPostType($(this));
			}
			else if(fieldType=="post_date")
			{
				self.processPostDate($(this));
			}
			else if(fieldType=="post_meta")
			{
				self.processPostMeta($(this));
				
			}
			else
			{
				
			}
			
		});
		
	},
	processSearchField: function($container)
	{
		var self = this;
		
		var $field = $container.find("input[name^='_sf_search']");
		
		if($field.length>0)
		{
			var fieldName = $field.attr("name").replace('[]', '');
			var fieldVal = $field.val();
			
			if(fieldVal!="")
			{
				//self.url_components += "&_sf_s="+encodeURIComponent(fieldVal);
				self.url_params['_sf_s'] = encodeURIComponent(fieldVal);
			}
		}
	},
	processSortOrderField: function($container)
	{
		this.processAuthor($container);
		
	},
	processResultsPerPageField: function($container)
	{
		this.processAuthor($container);
		
	},
	getActiveTax: function($field) {
		return this.active_tax;
	},
	getSelectVal: function($field){

		var fieldVal = "";
		
		if($field.val()!=0)
		{
			fieldVal = $field.val();
		}
		
		if(fieldVal==null)
		{
			fieldVal = "";
		}
		
		return fieldVal;
	},
	getMetaSelectVal: function($field){
		
		var fieldVal = "";
		
		fieldVal = $field.val();
						
		if(fieldVal==null)
		{
			fieldVal = "";
		}
		
		return fieldVal;
	},
	getMultiSelectVal: function($field, operator){
		
		var delim = "+";
		if(operator=="or")
		{
			delim = ",";
		}
		
		if(typeof($field.val())=="object")
		{
			if($field.val()!=null)
			{
				return $field.val().join(delim);
			}
		}
		
	},
	getMetaMultiSelectVal: function($field, operator){
		
		var delim = "-+-";
		if(operator=="or")
		{
			delim = "-,-";
		}
				
		if(typeof($field.val())=="object")
		{
			if($field.val()!=null)
			{
				
				var fieldval = [];
				
				$($field.val()).each(function(index,value){
					
					fieldval.push((value));
				});
				
				return fieldval.join(delim);
			}
		}
		
		return "";
		
	},
	getCheckboxVal: function($field, operator){
		
		
		var fieldVal = $field.map(function(){
			if($(this).prop("checked")==true)
			{
				return $(this).val();
			}
		}).get();
		
		var delim = "+";
		if(operator=="or")
		{
			delim = ",";
		}
		
		return fieldVal.join(delim);
	},
	getMetaCheckboxVal: function($field, operator){
		
		
		var fieldVal = $field.map(function(){
			if($(this).prop("checked")==true)
			{
				return ($(this).val());
			}
		}).get();
		
		var delim = "-+-";
		if(operator=="or")
		{
			delim = "-,-";
		}
		
		return fieldVal.join(delim);
	},
	getRadioVal: function($field){
							
		var fieldVal = $field.map(function()
		{
			if($(this).prop("checked")==true)
			{
				return $(this).val();
			}
			
		}).get();
		
		
		if(fieldVal[0]!=0)
		{
			return fieldVal[0];
		}
	},
	getMetaRadioVal: function($field){
							
		var fieldVal = $field.map(function()
		{
			if($(this).prop("checked")==true)
			{
				return $(this).val();
			}
			
		}).get();
		
		return fieldVal[0];
	},
	processAuthor: function($container)
	{
		var self = this;
		
		
		var fieldType = $container.attr("data-sf-field-type");
		var inputType = $container.attr("data-sf-field-input-type");
		
		var $field;
		var fieldName = "";
		var fieldVal = "";
		
		if(inputType=="select")
		{
			$field = $container.find("select");
			fieldName = $field.attr("name").replace('[]', '');
			
			fieldVal = self.getSelectVal($field); 
		}
		else if(inputType=="multiselect")
		{
			$field = $container.find("select");
			fieldName = $field.attr("name").replace('[]', '');
			var operator = $field.attr("data-operator");
			
			fieldVal = self.getMultiSelectVal($field, "or");
			
		}
		else if(inputType=="checkbox")
		{
			$field = $container.find("ul > li input:checkbox");
			
			if($field.length>0)
			{
				fieldName = $field.attr("name").replace('[]', '');
										
				var operator = $container.find("> ul").attr("data-operator");
				fieldVal = self.getCheckboxVal($field, "or");
			}
			
		}
		else if(inputType=="radio")
		{
			
			$field = $container.find("ul > li input:radio");
						
			if($field.length>0)
			{
				fieldName = $field.attr("name").replace('[]', '');
				
				fieldVal = self.getRadioVal($field);
			}
		}
		
		if(typeof(fieldVal)!="undefined")
		{
			if(fieldVal!="")
			{
				var fieldSlug = "";
				
				if(fieldName=="_sf_author")
				{
					fieldSlug = "authors";
				}
				else if(fieldName=="_sf_sort_order")
				{
					fieldSlug = "sort_order";
				}
				else if(fieldName=="_sf_ppp")
				{
					fieldSlug = "_sf_ppp";
				}
				else if(fieldName=="_sf_post_type")
				{
					fieldSlug = "post_types";
				}
				else
				{
				
				}
				
				if(fieldSlug!="")
				{
					//self.url_components += "&"+fieldSlug+"="+fieldVal;
					self.url_params[fieldSlug] = fieldVal;
				}
			}
		}
		
	},
	processPostType : function($this){
		
		this.processAuthor($this);
		
	},
	processPostMeta: function($container)
	{
		var self = this;
		
		var fieldType = $container.attr("data-sf-field-type");
		var inputType = $container.attr("data-sf-field-input-type");
		var metaType = $container.attr("data-sf-meta-type");

		var fieldVal = "";
		var $field;
		var fieldName = "";
		
		if(metaType=="number")
		{
			if(inputType=="range-number")
			{
				$field = $container.find(".sf-meta-range-number input");
				
				var values = [];
				$field.each(function(){
					
					values.push($(this).val());
				
				});
				
				fieldVal = values.join("+");
				
			}
			else if(inputType=="range-slider")
			{
				$field = $container.find(".sf-meta-range-slider input");
				
				//get any number formatting stuff
				var $meta_range = $container.find(".sf-meta-range-slider");
				
				var decimal_places = $meta_range.attr("data-decimal-places");
				var thousand_seperator = $meta_range.attr("data-thousand-seperator");
				var decimal_seperator = $meta_range.attr("data-decimal-seperator");

				var field_format = wNumb({
					mark: decimal_seperator,
					decimals: parseFloat(decimal_places),
					thousand: thousand_seperator
				});
				
				var values = [];


				var slider_object = $container.find(".meta-slider")[0];
				//val from slider object
				var slider_val = slider_object.noUiSlider.get();

				values.push(field_format.from(slider_val[0]));
				values.push(field_format.from(slider_val[1]));
				
				fieldVal = values.join("+");
				
				fieldName = $meta_range.attr("data-sf-field-name");
				
				
			}
			else if(inputType=="range-radio")
			{
				$field = $container.find(".sf-input-range-radio");
				
				if($field.length==0)
				{
					//then try again, we must be using a single field
					$field = $container.find("> ul");
				}

				var $meta_range = $container.find(".sf-meta-range");
				
				//there is an element with a from/to class - so we need to get the values of the from & to input fields seperately
				if($field.length>0)
				{	
					var field_vals = [];
					
					$field.each(function(){
						
						var $radios = $(this).find(".sf-input-radio");
						field_vals.push(self.getMetaRadioVal($radios));
						
					});
					
					//prevent second number from being lower than the first
					if(field_vals.length==2)
					{
						if(Number(field_vals[1])<Number(field_vals[0]))
						{
							field_vals[1] = field_vals[0];
						}
					}
					
					fieldVal = field_vals.join("+");
				}
								
				if($field.length==1)
				{
					fieldName = $field.find(".sf-input-radio").attr("name").replace('[]', '');
				}
				else
				{
					fieldName = $meta_range.attr("data-sf-field-name");
				}

			}
			else if(inputType=="range-select")
			{
				$field = $container.find(".sf-input-select");
				var $meta_range = $container.find(".sf-meta-range");
				
				//there is an element with a from/to class - so we need to get the values of the from & to input fields seperately
				
				if($field.length>0)
				{
					var field_vals = [];
					
					$field.each(function(){
						
						var $this = $(this);
						field_vals.push(self.getMetaSelectVal($this));
						
					});
					
					//prevent second number from being lower than the first
					if(field_vals.length==2)
					{
						if(Number(field_vals[1])<Number(field_vals[0]))
						{
							field_vals[1] = field_vals[0];
						}
					}
					
					
					fieldVal = field_vals.join("+");
				}
								
				if($field.length==1)
				{
					fieldName = $field.attr("name").replace('[]', '');
				}
				else
				{
					fieldName = $meta_range.attr("data-sf-field-name");
				}
				
			}
			else if(inputType=="range-checkbox")
			{
				$field = $container.find("ul > li input:checkbox");
				
				if($field.length>0)
				{
					fieldVal = self.getCheckboxVal($field, "and");
				}
			}
			
			if(fieldName=="")
			{
				fieldName = $field.attr("name").replace('[]', '');
			}
		}
		else if(metaType=="choice")
		{
			if(inputType=="select")
			{
				$field = $container.find("select");
				
				fieldVal = self.getMetaSelectVal($field); 
				
			}
			else if(inputType=="multiselect")
			{
				$field = $container.find("select");
				var operator = $field.attr("data-operator");
				
				fieldVal = self.getMetaMultiSelectVal($field, operator);
			}
			else if(inputType=="checkbox")
			{
				$field = $container.find("ul > li input:checkbox");
				
				if($field.length>0)
				{
					var operator = $container.find("> ul").attr("data-operator");
					fieldVal = self.getMetaCheckboxVal($field, operator);
				}
			}
			else if(inputType=="radio")
			{
				$field = $container.find("ul > li input:radio");
				
				if($field.length>0)
				{
					fieldVal = self.getMetaRadioVal($field);
				}
			}
			
			fieldVal = encodeURIComponent(fieldVal);
			if(typeof($field)!=="undefined")
			{
				if($field.length>0)
				{
					fieldName = $field.attr("name").replace('[]', '');
					
					//for those who insist on using & ampersands in the name of the custom field (!)
					fieldName = (fieldName);
				}
			}
			
		}
		else if(metaType=="date")
		{
			self.processPostDate($container);
		}
		
		if(typeof(fieldVal)!="undefined")
		{
			if(fieldVal!="")
			{
				//self.url_components += "&"+encodeURIComponent(fieldName)+"="+(fieldVal);
				self.url_params[encodeURIComponent(fieldName)] = (fieldVal);
			}
		}
	},
	processPostDate: function($container)
	{
		var self = this;
		
		var fieldType = $container.attr("data-sf-field-type");
		var inputType = $container.attr("data-sf-field-input-type");
		
		var $field;
		var fieldName = "";
		var fieldVal = "";
		
		$field = $container.find("ul > li input:text");
		fieldName = $field.attr("name").replace('[]', '');
		
		var dates = [];
		$field.each(function(){
			
			dates.push($(this).val());
		
		});
		
		if($field.length==2)
		{
			if((dates[0]!="")||(dates[1]!=""))
			{
				fieldVal = dates.join("+");
				fieldVal = fieldVal.replace(/\//g,'');
			}
		}
		else if($field.length==1)
		{
			if(dates[0]!="")
			{
				fieldVal = dates.join("+");
				fieldVal = fieldVal.replace(/\//g,'');
			}
		}
		
		if(typeof(fieldVal)!="undefined")
		{
			if(fieldVal!="")
			{
				var fieldSlug = "";
				
				if(fieldName=="_sf_post_date")
				{
					fieldSlug = "post_date";
				}
				else
				{
					fieldSlug = fieldName;
				}
				
				if(fieldSlug!="")
				{
					//self.url_components += "&"+fieldSlug+"="+fieldVal;
					self.url_params[fieldSlug] = fieldVal;
				}
			}
		}
		
	},
	processTaxonomy: function($container, return_object)
	{
        if(typeof(return_object)=="undefined")
        {
            return_object = false;
        }

		//if()					
		//var fieldName = $(this).attr("data-sf-field-name");
		var self = this;
	
		var fieldType = $container.attr("data-sf-field-type");
		var inputType = $container.attr("data-sf-field-input-type");
		
		var $field;
		var fieldName = "";
		var fieldVal = "";
		
		if(inputType=="select")
		{
			$field = $container.find("select");
			fieldName = $field.attr("name").replace('[]', '');
			
			fieldVal = self.getSelectVal($field); 
		}
		else if(inputType=="multiselect")
		{
			$field = $container.find("select");
			fieldName = $field.attr("name").replace('[]', '');
			var operator = $field.attr("data-operator");
			
			fieldVal = self.getMultiSelectVal($field, operator);
		}
		else if(inputType=="checkbox")
		{
			$field = $container.find("ul > li input:checkbox");
			if($field.length>0)
			{
				fieldName = $field.attr("name").replace('[]', '');
										
				var operator = $container.find("> ul").attr("data-operator");
				fieldVal = self.getCheckboxVal($field, operator);
			}
		}
		else if(inputType=="radio")
		{
			$field = $container.find("ul > li input:radio");
			if($field.length>0)
			{
				fieldName = $field.attr("name").replace('[]', '');
				
				fieldVal = self.getRadioVal($field);
			}
		}
		
		if(typeof(fieldVal)!="undefined")
		{
			if(fieldVal!="")
			{
                if(return_object==true)
                {
                    return {name: fieldName, value: fieldVal};
                }
                else
                {
                    //self.url_components += "&"+fieldName+"="+fieldVal;
                    self.url_params[fieldName] = fieldVal;
                }

			}
		}

        if(return_object==true)
        {
            return false;
        }
	}
};
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
//# sourceMappingURL=data:application/json;charset:utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9wdWJsaWMvYXNzZXRzL2pzL2luY2x1ZGVzL3Byb2Nlc3NfZm9ybS5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIlxudmFyICQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHtcblxuXHR0YXhvbm9teV9hcmNoaXZlczogMCxcbiAgICB1cmxfcGFyYW1zOiB7fSxcbiAgICB0YXhfYXJjaGl2ZV9yZXN1bHRzX3VybDogXCJcIixcbiAgICBhY3RpdmVfdGF4OiBcIlwiLFxuICAgIGZpZWxkczoge30sXG5cdGluaXQ6IGZ1bmN0aW9uKHRheG9ub215X2FyY2hpdmVzLCBjdXJyZW50X3RheG9ub215X2FyY2hpdmUpe1xuXG4gICAgICAgIHRoaXMudGF4b25vbXlfYXJjaGl2ZXMgPSAwO1xuICAgICAgICB0aGlzLnVybF9wYXJhbXMgPSB7fTtcbiAgICAgICAgdGhpcy50YXhfYXJjaGl2ZV9yZXN1bHRzX3VybCA9IFwiXCI7XG4gICAgICAgIHRoaXMuYWN0aXZlX3RheCA9IFwiXCI7XG5cblx0XHQvL3RoaXMuJGZpZWxkcyA9ICRmaWVsZHM7XG4gICAgICAgIHRoaXMudGF4b25vbXlfYXJjaGl2ZXMgPSB0YXhvbm9teV9hcmNoaXZlcztcbiAgICAgICAgdGhpcy5jdXJyZW50X3RheG9ub215X2FyY2hpdmUgPSBjdXJyZW50X3RheG9ub215X2FyY2hpdmU7XG5cblx0XHR0aGlzLmNsZWFyVXJsQ29tcG9uZW50cygpO1xuXG5cdH0sXG4gICAgc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmw6IGZ1bmN0aW9uKCRmb3JtLCBjdXJyZW50X3Jlc3VsdHNfdXJsLCBnZXRfYWN0aXZlKSB7XG5cbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuXHRcdHRoaXMuY2xlYXJUYXhBcmNoaXZlUmVzdWx0c1VybCgpO1xuICAgICAgICAvL3ZhciBjdXJyZW50X3Jlc3VsdHNfdXJsID0gXCJcIjtcbiAgICAgICAgaWYodGhpcy50YXhvbm9teV9hcmNoaXZlcyE9MSlcbiAgICAgICAge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKGdldF9hY3RpdmUpPT1cInVuZGVmaW5lZFwiKVxuXHRcdHtcblx0XHRcdHZhciBnZXRfYWN0aXZlID0gZmFsc2U7XG5cdFx0fVxuXG4gICAgICAgIC8vY2hlY2sgdG8gc2VlIGlmIHdlIGhhdmUgYW55IHRheG9ub21pZXMgc2VsZWN0ZWRcbiAgICAgICAgLy9pZiBzbywgY2hlY2sgdGhlaXIgcmV3cml0ZXMgYW5kIHVzZSB0aG9zZSBhcyB0aGUgcmVzdWx0cyB1cmxcbiAgICAgICAgdmFyICRmaWVsZCA9IGZhbHNlO1xuICAgICAgICB2YXIgZmllbGRfbmFtZSA9IFwiXCI7XG4gICAgICAgIHZhciBmaWVsZF92YWx1ZSA9IFwiXCI7XG5cbiAgICAgICAgdmFyICRhY3RpdmVfdGF4b25vbXkgPSAkZm9ybS4kZmllbGRzLnBhcmVudCgpLmZpbmQoXCJbZGF0YS1zZi10YXhvbm9teS1hcmNoaXZlPScxJ11cIik7XG4gICAgICAgIGlmKCRhY3RpdmVfdGF4b25vbXkubGVuZ3RoPT0xKVxuICAgICAgICB7XG4gICAgICAgICAgICAkZmllbGQgPSAkYWN0aXZlX3RheG9ub215O1xuXG4gICAgICAgICAgICB2YXIgZmllbGRUeXBlID0gJGZpZWxkLmF0dHIoXCJkYXRhLXNmLWZpZWxkLXR5cGVcIik7XG5cbiAgICAgICAgICAgIGlmICgoZmllbGRUeXBlID09IFwidGFnXCIpIHx8IChmaWVsZFR5cGUgPT0gXCJjYXRlZ29yeVwiKSB8fCAoZmllbGRUeXBlID09IFwidGF4b25vbXlcIikpIHtcbiAgICAgICAgICAgICAgICB2YXIgdGF4b25vbXlfdmFsdWUgPSBzZWxmLnByb2Nlc3NUYXhvbm9teSgkZmllbGQsIHRydWUpO1xuICAgICAgICAgICAgICAgIGZpZWxkX25hbWUgPSAkZmllbGQuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcbiAgICAgICAgICAgICAgICB2YXIgdGF4b25vbXlfbmFtZSA9IGZpZWxkX25hbWUucmVwbGFjZShcIl9zZnRfXCIsIFwiXCIpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRheG9ub215X3ZhbHVlKSB7XG4gICAgICAgICAgICAgICAgICAgIGZpZWxkX3ZhbHVlID0gdGF4b25vbXlfdmFsdWUudmFsdWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihmaWVsZF92YWx1ZT09XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkZmllbGQgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmKChzZWxmLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSE9XCJcIikmJihzZWxmLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSE9dGF4b25vbXlfbmFtZSkpXG4gICAgICAgIHtcblxuICAgICAgICAgICAgdGhpcy50YXhfYXJjaGl2ZV9yZXN1bHRzX3VybCA9IGN1cnJlbnRfcmVzdWx0c191cmw7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZigoKGZpZWxkX3ZhbHVlPT1cIlwiKXx8KCEkZmllbGQpICkpXG4gICAgICAgIHtcbiAgICAgICAgICAgICRmb3JtLiRmaWVsZHMuZWFjaChmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAoISRmaWVsZCkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBmaWVsZFR5cGUgPSAkKHRoaXMpLmF0dHIoXCJkYXRhLXNmLWZpZWxkLXR5cGVcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKChmaWVsZFR5cGUgPT0gXCJ0YWdcIikgfHwgKGZpZWxkVHlwZSA9PSBcImNhdGVnb3J5XCIpIHx8IChmaWVsZFR5cGUgPT0gXCJ0YXhvbm9teVwiKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHRheG9ub215X3ZhbHVlID0gc2VsZi5wcm9jZXNzVGF4b25vbXkoJCh0aGlzKSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWVsZF9uYW1lID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC1uYW1lXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGF4b25vbXlfdmFsdWUpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZpZWxkX3ZhbHVlID0gdGF4b25vbXlfdmFsdWUudmFsdWU7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoZmllbGRfdmFsdWUgIT0gXCJcIikge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRmaWVsZCA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKCAoJGZpZWxkKSAmJiAoZmllbGRfdmFsdWUgIT0gXCJcIiApKSB7XG4gICAgICAgICAgICAvL2lmIHdlIGZvdW5kIGEgZmllbGRcblx0XHRcdHZhciByZXdyaXRlX2F0dHIgPSAoJGZpZWxkLmF0dHIoXCJkYXRhLXNmLXRlcm0tcmV3cml0ZVwiKSk7XG5cbiAgICAgICAgICAgIGlmKHJld3JpdGVfYXR0ciE9XCJcIikge1xuXG4gICAgICAgICAgICAgICAgdmFyIHJld3JpdGUgPSBKU09OLnBhcnNlKHJld3JpdGVfYXR0cik7XG4gICAgICAgICAgICAgICAgdmFyIGlucHV0X3R5cGUgPSAkZmllbGQuYXR0cihcImRhdGEtc2YtZmllbGQtaW5wdXQtdHlwZVwiKTtcbiAgICAgICAgICAgICAgICBzZWxmLmFjdGl2ZV90YXggPSBmaWVsZF9uYW1lO1xuXG4gICAgICAgICAgICAgICAgLy9maW5kIHRoZSBhY3RpdmUgZWxlbWVudFxuICAgICAgICAgICAgICAgIGlmICgoaW5wdXRfdHlwZSA9PSBcInJhZGlvXCIpIHx8IChpbnB1dF90eXBlID09IFwiY2hlY2tib3hcIikpIHtcblxuICAgICAgICAgICAgICAgICAgICAvL3ZhciAkYWN0aXZlID0gJGZpZWxkLmZpbmQoXCIuc2Ytb3B0aW9uLWFjdGl2ZVwiKTtcbiAgICAgICAgICAgICAgICAgICAgLy9leHBsb2RlIHRoZSB2YWx1ZXMgaWYgdGhlcmUgaXMgYSBkZWxpbVxuICAgICAgICAgICAgICAgICAgICAvL2ZpZWxkX3ZhbHVlXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGlzX3NpbmdsZV92YWx1ZSA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIHZhciBmaWVsZF92YWx1ZXMgPSBmaWVsZF92YWx1ZS5zcGxpdChcIixcIikuam9pbihcIitcIikuc3BsaXQoXCIrXCIpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoZmllbGRfdmFsdWVzLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlzX3NpbmdsZV92YWx1ZSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGlzX3NpbmdsZV92YWx1ZSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGlucHV0ID0gJGZpZWxkLmZpbmQoXCJpbnB1dFt2YWx1ZT0nXCIgKyBmaWVsZF92YWx1ZSArIFwiJ11cIik7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGFjdGl2ZSA9ICRpbnB1dC5wYXJlbnQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBkZXB0aCA9ICRhY3RpdmUuYXR0cihcImRhdGEtc2YtZGVwdGhcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vbm93IGxvb3AgdGhyb3VnaCBwYXJlbnRzIHRvIGdyYWIgdGhlaXIgbmFtZXNcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB2YWx1ZXMgPSBuZXcgQXJyYXkoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlcy5wdXNoKGZpZWxkX3ZhbHVlKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IGRlcHRoOyBpID4gMDsgaS0tKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGFjdGl2ZSA9ICRhY3RpdmUucGFyZW50KCkucGFyZW50KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWVzLnB1c2goJGFjdGl2ZS5maW5kKFwiaW5wdXRcIikudmFsKCkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZXMucmV2ZXJzZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2dyYWIgdGhlIHJld3JpdGUgZm9yIHRoaXMgZGVwdGhcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBhY3RpdmVfcmV3cml0ZSA9IHJld3JpdGVbZGVwdGhdO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHVybCA9IGFjdGl2ZV9yZXdyaXRlO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vdGhlbiBtYXAgZnJvbSB0aGUgcGFyZW50cyB0byB0aGUgZGVwdGhcbiAgICAgICAgICAgICAgICAgICAgICAgICQodmFsdWVzKS5lYWNoKGZ1bmN0aW9uIChpbmRleCwgdmFsdWUpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybCA9IHVybC5yZXBsYWNlKFwiW1wiICsgaW5kZXggKyBcIl1cIiwgdmFsdWUpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudGF4X2FyY2hpdmVfcmVzdWx0c191cmwgPSB1cmw7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vaWYgdGhlcmUgYXJlIG11bHRpcGxlIHZhbHVlcyxcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vdGhlbiB3ZSBuZWVkIHRvIGNoZWNrIGZvciAzIHRoaW5nczpcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pZiB0aGUgdmFsdWVzIHNlbGVjdGVkIGFyZSBhbGwgaW4gdGhlIHNhbWUgdHJlZSB0aGVuIHdlIGNhbiBkbyBzb21lIGNsZXZlciByZXdyaXRlIHN0dWZmXG4gICAgICAgICAgICAgICAgICAgICAgICAvL21lcmdlIGFsbCB2YWx1ZXMgaW4gc2FtZSBsZXZlbCwgdGhlbiBjb21iaW5lIHRoZSBsZXZlbHNcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pZiB0aGV5IGFyZSBmcm9tIGRpZmZlcmVudCB0cmVlcyB0aGVuIGp1c3QgY29tYmluZSB0aGVtIG9yIGp1c3QgdXNlIGBmaWVsZF92YWx1ZWBcbiAgICAgICAgICAgICAgICAgICAgICAgIC8qXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgZGVwdGhzID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgJChmaWVsZF92YWx1ZXMpLmVhY2goZnVuY3Rpb24gKGluZGV4LCB2YWwpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkaW5wdXQgPSAkZmllbGQuZmluZChcImlucHV0W3ZhbHVlPSdcIiArIGZpZWxkX3ZhbHVlICsgXCInXVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGFjdGl2ZSA9ICRpbnB1dC5wYXJlbnQoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBkZXB0aCA9ICRhY3RpdmUuYXR0cihcImRhdGEtc2YtZGVwdGhcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgLy9kZXB0aHMucHVzaChkZXB0aCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICB9KTsqL1xuXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZiAoKGlucHV0X3R5cGUgPT0gXCJzZWxlY3RcIikgfHwgKGlucHV0X3R5cGUgPT0gXCJtdWx0aXNlbGVjdFwiKSkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBpc19zaW5nbGVfdmFsdWUgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZmllbGRfdmFsdWVzID0gZmllbGRfdmFsdWUuc3BsaXQoXCIsXCIpLmpvaW4oXCIrXCIpLnNwbGl0KFwiK1wiKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGZpZWxkX3ZhbHVlcy5sZW5ndGggPiAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpc19zaW5nbGVfdmFsdWUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmIChpc19zaW5nbGVfdmFsdWUpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRhY3RpdmUgPSAkZmllbGQuZmluZChcIm9wdGlvblt2YWx1ZT0nXCIgKyBmaWVsZF92YWx1ZSArIFwiJ11cIik7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgZGVwdGggPSAkYWN0aXZlLmF0dHIoXCJkYXRhLXNmLWRlcHRoXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdmFsdWVzID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZXMucHVzaChmaWVsZF92YWx1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSBkZXB0aDsgaSA+IDA7IGktLSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRhY3RpdmUgPSAkYWN0aXZlLnByZXZBbGwoXCJvcHRpb25bZGF0YS1zZi1kZXB0aD0nXCIgKyAoaSAtIDEpICsgXCInXVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZXMucHVzaCgkYWN0aXZlLnZhbCgpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWVzLnJldmVyc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBhY3RpdmVfcmV3cml0ZSA9IHJld3JpdGVbZGVwdGhdO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHVybCA9IGFjdGl2ZV9yZXdyaXRlO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh2YWx1ZXMpLmVhY2goZnVuY3Rpb24gKGluZGV4LCB2YWx1ZSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsID0gdXJsLnJlcGxhY2UoXCJbXCIgKyBpbmRleCArIFwiXVwiLCB2YWx1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy50YXhfYXJjaGl2ZV9yZXN1bHRzX3VybCA9IHVybDtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cbiAgICAgICAgLy90aGlzLnRheF9hcmNoaXZlX3Jlc3VsdHNfdXJsID0gY3VycmVudF9yZXN1bHRzX3VybDtcbiAgICB9LFxuICAgIGdldFJlc3VsdHNVcmw6IGZ1bmN0aW9uKCRmb3JtLCBjdXJyZW50X3Jlc3VsdHNfdXJsKSB7XG5cbiAgICAgICAgLy90aGlzLnNldFRheEFyY2hpdmVSZXN1bHRzVXJsKCRmb3JtLCBjdXJyZW50X3Jlc3VsdHNfdXJsKTtcblxuICAgICAgICBpZih0aGlzLnRheF9hcmNoaXZlX3Jlc3VsdHNfdXJsPT1cIlwiKVxuICAgICAgICB7XG4gICAgICAgICAgICByZXR1cm4gY3VycmVudF9yZXN1bHRzX3VybDtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLnRheF9hcmNoaXZlX3Jlc3VsdHNfdXJsO1xuICAgIH0sXG5cdGdldFVybFBhcmFtczogZnVuY3Rpb24oJGZvcm0pe1xuXG5cdFx0dGhpcy5idWlsZFVybENvbXBvbmVudHMoJGZvcm0sIHRydWUpO1xuXG4gICAgICAgIGlmKHRoaXMudGF4X2FyY2hpdmVfcmVzdWx0c191cmwhPVwiXCIpXG4gICAgICAgIHtcblxuICAgICAgICAgICAgaWYodGhpcy5hY3RpdmVfdGF4IT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBmaWVsZF9uYW1lID0gdGhpcy5hY3RpdmVfdGF4O1xuXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mKHRoaXMudXJsX3BhcmFtc1tmaWVsZF9uYW1lXSkhPVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBkZWxldGUgdGhpcy51cmxfcGFyYW1zW2ZpZWxkX25hbWVdO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG5cdFx0cmV0dXJuIHRoaXMudXJsX3BhcmFtcztcblx0fSxcblx0Y2xlYXJVcmxDb21wb25lbnRzOiBmdW5jdGlvbigpe1xuXHRcdC8vdGhpcy51cmxfY29tcG9uZW50cyA9IFwiXCI7XG5cdFx0dGhpcy51cmxfcGFyYW1zID0ge307XG5cdH0sXG5cdGNsZWFyVGF4QXJjaGl2ZVJlc3VsdHNVcmw6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMudGF4X2FyY2hpdmVfcmVzdWx0c191cmwgPSAnJztcblx0fSxcblx0ZGlzYWJsZUlucHV0czogZnVuY3Rpb24oJGZvcm0pe1xuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XHRcblx0XHQkZm9ybS4kZmllbGRzLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdFxuXHRcdFx0dmFyICRpbnB1dHMgPSAkKHRoaXMpLmZpbmQoXCJpbnB1dCwgc2VsZWN0LCAubWV0YS1zbGlkZXJcIik7XG5cdFx0XHQkaW5wdXRzLmF0dHIoXCJkaXNhYmxlZFwiLCBcImRpc2FibGVkXCIpO1xuXHRcdFx0JGlucHV0cy5hdHRyKFwiZGlzYWJsZWRcIiwgdHJ1ZSk7XG5cdFx0XHQkaW5wdXRzLnByb3AoXCJkaXNhYmxlZFwiLCB0cnVlKTtcblx0XHRcdCRpbnB1dHMudHJpZ2dlcihcImNob3Nlbjp1cGRhdGVkXCIpO1xuXHRcdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0XG5cdH0sXG5cdGVuYWJsZUlucHV0czogZnVuY3Rpb24oJGZvcm0pe1xuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XHQkZm9ybS4kZmllbGRzLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdHZhciAkaW5wdXRzID0gJCh0aGlzKS5maW5kKFwiaW5wdXQsIHNlbGVjdCwgLm1ldGEtc2xpZGVyXCIpO1xuXHRcdFx0JGlucHV0cy5wcm9wKFwiZGlzYWJsZWRcIiwgZmFsc2UpO1xuXHRcdFx0JGlucHV0cy5hdHRyKFwiZGlzYWJsZWRcIiwgZmFsc2UpO1xuXHRcdFx0JGlucHV0cy50cmlnZ2VyKFwiY2hvc2VuOnVwZGF0ZWRcIik7XHRcdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0XG5cdH0sXG5cdGJ1aWxkVXJsQ29tcG9uZW50czogZnVuY3Rpb24oJGZvcm0sIGNsZWFyX2NvbXBvbmVudHMpe1xuXHRcdFxuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XHRcblx0XHRpZih0eXBlb2YoY2xlYXJfY29tcG9uZW50cykhPVwidW5kZWZpbmVkXCIpXG5cdFx0e1xuXHRcdFx0aWYoY2xlYXJfY29tcG9uZW50cz09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0dGhpcy5jbGVhclVybENvbXBvbmVudHMoKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0JGZvcm0uJGZpZWxkcy5lYWNoKGZ1bmN0aW9uKCl7XG5cdFx0XHRcblx0XHRcdHZhciBmaWVsZE5hbWUgPSAkKHRoaXMpLmF0dHIoXCJkYXRhLXNmLWZpZWxkLW5hbWVcIik7XG5cdFx0XHR2YXIgZmllbGRUeXBlID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC10eXBlXCIpO1xuXHRcdFx0XG5cdFx0XHRpZihmaWVsZFR5cGU9PVwic2VhcmNoXCIpXG5cdFx0XHR7XG5cdFx0XHRcdHNlbGYucHJvY2Vzc1NlYXJjaEZpZWxkKCQodGhpcykpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZSBpZigoZmllbGRUeXBlPT1cInRhZ1wiKXx8KGZpZWxkVHlwZT09XCJjYXRlZ29yeVwiKXx8KGZpZWxkVHlwZT09XCJ0YXhvbm9teVwiKSlcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzVGF4b25vbXkoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJzb3J0X29yZGVyXCIpXG5cdFx0XHR7XG5cdFx0XHRcdHNlbGYucHJvY2Vzc1NvcnRPcmRlckZpZWxkKCQodGhpcykpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZSBpZihmaWVsZFR5cGU9PVwicG9zdHNfcGVyX3BhZ2VcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUmVzdWx0c1BlclBhZ2VGaWVsZCgkKHRoaXMpKTtcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoZmllbGRUeXBlPT1cImF1dGhvclwiKVxuXHRcdFx0e1xuXHRcdFx0XHRzZWxmLnByb2Nlc3NBdXRob3IoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJwb3N0X3R5cGVcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUG9zdFR5cGUoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJwb3N0X2RhdGVcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUG9zdERhdGUoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJwb3N0X21ldGFcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUG9zdE1ldGEoJCh0aGlzKSk7XG5cdFx0XHRcdFxuXHRcdFx0fVxuXHRcdFx0ZWxzZVxuXHRcdFx0e1xuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdFxuXHRcdH0pO1xuXHRcdFxuXHR9LFxuXHRwcm9jZXNzU2VhcmNoRmllbGQ6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cdFx0XG5cdFx0dmFyICRmaWVsZCA9ICRjb250YWluZXIuZmluZChcImlucHV0W25hbWVePSdfc2Zfc2VhcmNoJ11cIik7XG5cdFx0XG5cdFx0aWYoJGZpZWxkLmxlbmd0aD4wKVxuXHRcdHtcblx0XHRcdHZhciBmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHR2YXIgZmllbGRWYWwgPSAkZmllbGQudmFsKCk7XG5cdFx0XHRcblx0XHRcdGlmKGZpZWxkVmFsIT1cIlwiKVxuXHRcdFx0e1xuXHRcdFx0XHQvL3NlbGYudXJsX2NvbXBvbmVudHMgKz0gXCImX3NmX3M9XCIrZW5jb2RlVVJJQ29tcG9uZW50KGZpZWxkVmFsKTtcblx0XHRcdFx0c2VsZi51cmxfcGFyYW1zWydfc2ZfcyddID0gZW5jb2RlVVJJQ29tcG9uZW50KGZpZWxkVmFsKTtcblx0XHRcdH1cblx0XHR9XG5cdH0sXG5cdHByb2Nlc3NTb3J0T3JkZXJGaWVsZDogZnVuY3Rpb24oJGNvbnRhaW5lcilcblx0e1xuXHRcdHRoaXMucHJvY2Vzc0F1dGhvcigkY29udGFpbmVyKTtcblx0XHRcblx0fSxcblx0cHJvY2Vzc1Jlc3VsdHNQZXJQYWdlRmllbGQ6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR0aGlzLnByb2Nlc3NBdXRob3IoJGNvbnRhaW5lcik7XG5cdFx0XG5cdH0sXG5cdGdldEFjdGl2ZVRheDogZnVuY3Rpb24oJGZpZWxkKSB7XG5cdFx0cmV0dXJuIHRoaXMuYWN0aXZlX3RheDtcblx0fSxcblx0Z2V0U2VsZWN0VmFsOiBmdW5jdGlvbigkZmllbGQpe1xuXG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHRpZigkZmllbGQudmFsKCkhPTApXG5cdFx0e1xuXHRcdFx0ZmllbGRWYWwgPSAkZmllbGQudmFsKCk7XG5cdFx0fVxuXHRcdFxuXHRcdGlmKGZpZWxkVmFsPT1udWxsKVxuXHRcdHtcblx0XHRcdGZpZWxkVmFsID0gXCJcIjtcblx0XHR9XG5cdFx0XG5cdFx0cmV0dXJuIGZpZWxkVmFsO1xuXHR9LFxuXHRnZXRNZXRhU2VsZWN0VmFsOiBmdW5jdGlvbigkZmllbGQpe1xuXHRcdFxuXHRcdHZhciBmaWVsZFZhbCA9IFwiXCI7XG5cdFx0XG5cdFx0ZmllbGRWYWwgPSAkZmllbGQudmFsKCk7XG5cdFx0XHRcdFx0XHRcblx0XHRpZihmaWVsZFZhbD09bnVsbClcblx0XHR7XG5cdFx0XHRmaWVsZFZhbCA9IFwiXCI7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBmaWVsZFZhbDtcblx0fSxcblx0Z2V0TXVsdGlTZWxlY3RWYWw6IGZ1bmN0aW9uKCRmaWVsZCwgb3BlcmF0b3Ipe1xuXHRcdFxuXHRcdHZhciBkZWxpbSA9IFwiK1wiO1xuXHRcdGlmKG9wZXJhdG9yPT1cIm9yXCIpXG5cdFx0e1xuXHRcdFx0ZGVsaW0gPSBcIixcIjtcblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKCRmaWVsZC52YWwoKSk9PVwib2JqZWN0XCIpXG5cdFx0e1xuXHRcdFx0aWYoJGZpZWxkLnZhbCgpIT1udWxsKVxuXHRcdFx0e1xuXHRcdFx0XHRyZXR1cm4gJGZpZWxkLnZhbCgpLmpvaW4oZGVsaW0pO1xuXHRcdFx0fVxuXHRcdH1cblx0XHRcblx0fSxcblx0Z2V0TWV0YU11bHRpU2VsZWN0VmFsOiBmdW5jdGlvbigkZmllbGQsIG9wZXJhdG9yKXtcblx0XHRcblx0XHR2YXIgZGVsaW0gPSBcIi0rLVwiO1xuXHRcdGlmKG9wZXJhdG9yPT1cIm9yXCIpXG5cdFx0e1xuXHRcdFx0ZGVsaW0gPSBcIi0sLVwiO1xuXHRcdH1cblx0XHRcdFx0XG5cdFx0aWYodHlwZW9mKCRmaWVsZC52YWwoKSk9PVwib2JqZWN0XCIpXG5cdFx0e1xuXHRcdFx0aWYoJGZpZWxkLnZhbCgpIT1udWxsKVxuXHRcdFx0e1xuXHRcdFx0XHRcblx0XHRcdFx0dmFyIGZpZWxkdmFsID0gW107XG5cdFx0XHRcdFxuXHRcdFx0XHQkKCRmaWVsZC52YWwoKSkuZWFjaChmdW5jdGlvbihpbmRleCx2YWx1ZSl7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0ZmllbGR2YWwucHVzaCgodmFsdWUpKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRyZXR1cm4gZmllbGR2YWwuam9pbihkZWxpbSk7XG5cdFx0XHR9XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBcIlwiO1xuXHRcdFxuXHR9LFxuXHRnZXRDaGVja2JveFZhbDogZnVuY3Rpb24oJGZpZWxkLCBvcGVyYXRvcil7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIGZpZWxkVmFsID0gJGZpZWxkLm1hcChmdW5jdGlvbigpe1xuXHRcdFx0aWYoJCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiKT09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0cmV0dXJuICQodGhpcykudmFsKCk7XG5cdFx0XHR9XG5cdFx0fSkuZ2V0KCk7XG5cdFx0XG5cdFx0dmFyIGRlbGltID0gXCIrXCI7XG5cdFx0aWYob3BlcmF0b3I9PVwib3JcIilcblx0XHR7XG5cdFx0XHRkZWxpbSA9IFwiLFwiO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gZmllbGRWYWwuam9pbihkZWxpbSk7XG5cdH0sXG5cdGdldE1ldGFDaGVja2JveFZhbDogZnVuY3Rpb24oJGZpZWxkLCBvcGVyYXRvcil7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIGZpZWxkVmFsID0gJGZpZWxkLm1hcChmdW5jdGlvbigpe1xuXHRcdFx0aWYoJCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiKT09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0cmV0dXJuICgkKHRoaXMpLnZhbCgpKTtcblx0XHRcdH1cblx0XHR9KS5nZXQoKTtcblx0XHRcblx0XHR2YXIgZGVsaW0gPSBcIi0rLVwiO1xuXHRcdGlmKG9wZXJhdG9yPT1cIm9yXCIpXG5cdFx0e1xuXHRcdFx0ZGVsaW0gPSBcIi0sLVwiO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gZmllbGRWYWwuam9pbihkZWxpbSk7XG5cdH0sXG5cdGdldFJhZGlvVmFsOiBmdW5jdGlvbigkZmllbGQpe1xuXHRcdFx0XHRcdFx0XHRcblx0XHR2YXIgZmllbGRWYWwgPSAkZmllbGQubWFwKGZ1bmN0aW9uKClcblx0XHR7XG5cdFx0XHRpZigkKHRoaXMpLnByb3AoXCJjaGVja2VkXCIpPT10cnVlKVxuXHRcdFx0e1xuXHRcdFx0XHRyZXR1cm4gJCh0aGlzKS52YWwoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdH0pLmdldCgpO1xuXHRcdFxuXHRcdFxuXHRcdGlmKGZpZWxkVmFsWzBdIT0wKVxuXHRcdHtcblx0XHRcdHJldHVybiBmaWVsZFZhbFswXTtcblx0XHR9XG5cdH0sXG5cdGdldE1ldGFSYWRpb1ZhbDogZnVuY3Rpb24oJGZpZWxkKXtcblx0XHRcdFx0XHRcdFx0XG5cdFx0dmFyIGZpZWxkVmFsID0gJGZpZWxkLm1hcChmdW5jdGlvbigpXG5cdFx0e1xuXHRcdFx0aWYoJCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiKT09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0cmV0dXJuICQodGhpcykudmFsKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9KS5nZXQoKTtcblx0XHRcblx0XHRyZXR1cm4gZmllbGRWYWxbMF07XG5cdH0sXG5cdHByb2Nlc3NBdXRob3I6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIGZpZWxkVHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtdHlwZVwiKTtcblx0XHR2YXIgaW5wdXRUeXBlID0gJGNvbnRhaW5lci5hdHRyKFwiZGF0YS1zZi1maWVsZC1pbnB1dC10eXBlXCIpO1xuXHRcdFxuXHRcdHZhciAkZmllbGQ7XG5cdFx0dmFyIGZpZWxkTmFtZSA9IFwiXCI7XG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHRpZihpbnB1dFR5cGU9PVwic2VsZWN0XCIpXG5cdFx0e1xuXHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwic2VsZWN0XCIpO1xuXHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFx0XG5cdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0U2VsZWN0VmFsKCRmaWVsZCk7IFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJtdWx0aXNlbGVjdFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdHZhciBvcGVyYXRvciA9ICRmaWVsZC5hdHRyKFwiZGF0YS1vcGVyYXRvclwiKTtcblx0XHRcdFxuXHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldE11bHRpU2VsZWN0VmFsKCRmaWVsZCwgXCJvclwiKTtcblx0XHRcdFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJjaGVja2JveFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRcblx0XHRcdGlmKCRmaWVsZC5sZW5ndGg+MClcblx0XHRcdHtcblx0XHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0dmFyIG9wZXJhdG9yID0gJGNvbnRhaW5lci5maW5kKFwiPiB1bFwiKS5hdHRyKFwiZGF0YS1vcGVyYXRvclwiKTtcblx0XHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldENoZWNrYm94VmFsKCRmaWVsZCwgXCJvclwiKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJyYWRpb1wiKVxuXHRcdHtcblx0XHRcdFxuXHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwidWwgPiBsaSBpbnB1dDpyYWRpb1wiKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0aWYoJGZpZWxkLmxlbmd0aD4wKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0UmFkaW9WYWwoJGZpZWxkKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKGZpZWxkVmFsKSE9XCJ1bmRlZmluZWRcIilcblx0XHR7XG5cdFx0XHRpZihmaWVsZFZhbCE9XCJcIilcblx0XHRcdHtcblx0XHRcdFx0dmFyIGZpZWxkU2x1ZyA9IFwiXCI7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZihmaWVsZE5hbWU9PVwiX3NmX2F1dGhvclwiKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGRTbHVnID0gXCJhdXRob3JzXCI7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZSBpZihmaWVsZE5hbWU9PVwiX3NmX3NvcnRfb3JkZXJcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkU2x1ZyA9IFwic29ydF9vcmRlclwiO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2UgaWYoZmllbGROYW1lPT1cIl9zZl9wcHBcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkU2x1ZyA9IFwiX3NmX3BwcFwiO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2UgaWYoZmllbGROYW1lPT1cIl9zZl9wb3N0X3R5cGVcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkU2x1ZyA9IFwicG9zdF90eXBlc1wiO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Vcblx0XHRcdFx0e1xuXHRcdFx0XHRcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0aWYoZmllbGRTbHVnIT1cIlwiKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0Ly9zZWxmLnVybF9jb21wb25lbnRzICs9IFwiJlwiK2ZpZWxkU2x1ZytcIj1cIitmaWVsZFZhbDtcblx0XHRcdFx0XHRzZWxmLnVybF9wYXJhbXNbZmllbGRTbHVnXSA9IGZpZWxkVmFsO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fVxuXHRcdFxuXHR9LFxuXHRwcm9jZXNzUG9zdFR5cGUgOiBmdW5jdGlvbigkdGhpcyl7XG5cdFx0XG5cdFx0dGhpcy5wcm9jZXNzQXV0aG9yKCR0aGlzKTtcblx0XHRcblx0fSxcblx0cHJvY2Vzc1Bvc3RNZXRhOiBmdW5jdGlvbigkY29udGFpbmVyKVxuXHR7XG5cdFx0dmFyIHNlbGYgPSB0aGlzO1xuXHRcdFxuXHRcdHZhciBmaWVsZFR5cGUgPSAkY29udGFpbmVyLmF0dHIoXCJkYXRhLXNmLWZpZWxkLXR5cGVcIik7XG5cdFx0dmFyIGlucHV0VHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtaW5wdXQtdHlwZVwiKTtcblx0XHR2YXIgbWV0YVR5cGUgPSAkY29udGFpbmVyLmF0dHIoXCJkYXRhLXNmLW1ldGEtdHlwZVwiKTtcblxuXHRcdHZhciBmaWVsZFZhbCA9IFwiXCI7XG5cdFx0dmFyICRmaWVsZDtcblx0XHR2YXIgZmllbGROYW1lID0gXCJcIjtcblx0XHRcblx0XHRpZihtZXRhVHlwZT09XCJudW1iZXJcIilcblx0XHR7XG5cdFx0XHRpZihpbnB1dFR5cGU9PVwicmFuZ2UtbnVtYmVyXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcIi5zZi1tZXRhLXJhbmdlLW51bWJlciBpbnB1dFwiKTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciB2YWx1ZXMgPSBbXTtcblx0XHRcdFx0JGZpZWxkLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHR2YWx1ZXMucHVzaCgkKHRoaXMpLnZhbCgpKTtcblx0XHRcdFx0XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0ZmllbGRWYWwgPSB2YWx1ZXMuam9pbihcIitcIik7XG5cdFx0XHRcdFxuXHRcdFx0fVxuXHRcdFx0ZWxzZSBpZihpbnB1dFR5cGU9PVwicmFuZ2Utc2xpZGVyXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcIi5zZi1tZXRhLXJhbmdlLXNsaWRlciBpbnB1dFwiKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vZ2V0IGFueSBudW1iZXIgZm9ybWF0dGluZyBzdHVmZlxuXHRcdFx0XHR2YXIgJG1ldGFfcmFuZ2UgPSAkY29udGFpbmVyLmZpbmQoXCIuc2YtbWV0YS1yYW5nZS1zbGlkZXJcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHR2YXIgZGVjaW1hbF9wbGFjZXMgPSAkbWV0YV9yYW5nZS5hdHRyKFwiZGF0YS1kZWNpbWFsLXBsYWNlc1wiKTtcblx0XHRcdFx0dmFyIHRob3VzYW5kX3NlcGVyYXRvciA9ICRtZXRhX3JhbmdlLmF0dHIoXCJkYXRhLXRob3VzYW5kLXNlcGVyYXRvclwiKTtcblx0XHRcdFx0dmFyIGRlY2ltYWxfc2VwZXJhdG9yID0gJG1ldGFfcmFuZ2UuYXR0cihcImRhdGEtZGVjaW1hbC1zZXBlcmF0b3JcIik7XG5cblx0XHRcdFx0dmFyIGZpZWxkX2Zvcm1hdCA9IHdOdW1iKHtcblx0XHRcdFx0XHRtYXJrOiBkZWNpbWFsX3NlcGVyYXRvcixcblx0XHRcdFx0XHRkZWNpbWFsczogcGFyc2VGbG9hdChkZWNpbWFsX3BsYWNlcyksXG5cdFx0XHRcdFx0dGhvdXNhbmQ6IHRob3VzYW5kX3NlcGVyYXRvclxuXHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciB2YWx1ZXMgPSBbXTtcblxuXG5cdFx0XHRcdHZhciBzbGlkZXJfb2JqZWN0ID0gJGNvbnRhaW5lci5maW5kKFwiLm1ldGEtc2xpZGVyXCIpWzBdO1xuXHRcdFx0XHQvL3ZhbCBmcm9tIHNsaWRlciBvYmplY3Rcblx0XHRcdFx0dmFyIHNsaWRlcl92YWwgPSBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuZ2V0KCk7XG5cblx0XHRcdFx0dmFsdWVzLnB1c2goZmllbGRfZm9ybWF0LmZyb20oc2xpZGVyX3ZhbFswXSkpO1xuXHRcdFx0XHR2YWx1ZXMucHVzaChmaWVsZF9mb3JtYXQuZnJvbShzbGlkZXJfdmFsWzFdKSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRmaWVsZFZhbCA9IHZhbHVlcy5qb2luKFwiK1wiKTtcblx0XHRcdFx0XG5cdFx0XHRcdGZpZWxkTmFtZSA9ICRtZXRhX3JhbmdlLmF0dHIoXCJkYXRhLXNmLWZpZWxkLW5hbWVcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cInJhbmdlLXJhZGlvXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcIi5zZi1pbnB1dC1yYW5nZS1yYWRpb1wiKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg9PTApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQvL3RoZW4gdHJ5IGFnYWluLCB3ZSBtdXN0IGJlIHVzaW5nIGEgc2luZ2xlIGZpZWxkXG5cdFx0XHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwiPiB1bFwiKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHZhciAkbWV0YV9yYW5nZSA9ICRjb250YWluZXIuZmluZChcIi5zZi1tZXRhLXJhbmdlXCIpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly90aGVyZSBpcyBhbiBlbGVtZW50IHdpdGggYSBmcm9tL3RvIGNsYXNzIC0gc28gd2UgbmVlZCB0byBnZXQgdGhlIHZhbHVlcyBvZiB0aGUgZnJvbSAmIHRvIGlucHV0IGZpZWxkcyBzZXBlcmF0ZWx5XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg+MClcblx0XHRcdFx0e1x0XG5cdFx0XHRcdFx0dmFyIGZpZWxkX3ZhbHMgPSBbXTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkZmllbGQuZWFjaChmdW5jdGlvbigpe1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHR2YXIgJHJhZGlvcyA9ICQodGhpcykuZmluZChcIi5zZi1pbnB1dC1yYWRpb1wiKTtcblx0XHRcdFx0XHRcdGZpZWxkX3ZhbHMucHVzaChzZWxmLmdldE1ldGFSYWRpb1ZhbCgkcmFkaW9zKSk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvL3ByZXZlbnQgc2Vjb25kIG51bWJlciBmcm9tIGJlaW5nIGxvd2VyIHRoYW4gdGhlIGZpcnN0XG5cdFx0XHRcdFx0aWYoZmllbGRfdmFscy5sZW5ndGg9PTIpXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0aWYoTnVtYmVyKGZpZWxkX3ZhbHNbMV0pPE51bWJlcihmaWVsZF92YWxzWzBdKSlcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0ZmllbGRfdmFsc1sxXSA9IGZpZWxkX3ZhbHNbMF07XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGZpZWxkVmFsID0gZmllbGRfdmFscy5qb2luKFwiK1wiKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPT0xKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmZpbmQoXCIuc2YtaW5wdXQtcmFkaW9cIikuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGROYW1lID0gJG1ldGFfcmFuZ2UuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcblx0XHRcdFx0fVxuXG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJyYW5nZS1zZWxlY3RcIilcblx0XHRcdHtcblx0XHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwiLnNmLWlucHV0LXNlbGVjdFwiKTtcblx0XHRcdFx0dmFyICRtZXRhX3JhbmdlID0gJGNvbnRhaW5lci5maW5kKFwiLnNmLW1ldGEtcmFuZ2VcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHQvL3RoZXJlIGlzIGFuIGVsZW1lbnQgd2l0aCBhIGZyb20vdG8gY2xhc3MgLSBzbyB3ZSBuZWVkIHRvIGdldCB0aGUgdmFsdWVzIG9mIHRoZSBmcm9tICYgdG8gaW5wdXQgZmllbGRzIHNlcGVyYXRlbHlcblx0XHRcdFx0XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg+MClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHZhciBmaWVsZF92YWxzID0gW107XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JGZpZWxkLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0dmFyICR0aGlzID0gJCh0aGlzKTtcblx0XHRcdFx0XHRcdGZpZWxkX3ZhbHMucHVzaChzZWxmLmdldE1ldGFTZWxlY3RWYWwoJHRoaXMpKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vcHJldmVudCBzZWNvbmQgbnVtYmVyIGZyb20gYmVpbmcgbG93ZXIgdGhhbiB0aGUgZmlyc3Rcblx0XHRcdFx0XHRpZihmaWVsZF92YWxzLmxlbmd0aD09Milcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRpZihOdW1iZXIoZmllbGRfdmFsc1sxXSk8TnVtYmVyKGZpZWxkX3ZhbHNbMF0pKVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRmaWVsZF92YWxzWzFdID0gZmllbGRfdmFsc1swXTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0ZmllbGRWYWwgPSBmaWVsZF92YWxzLmpvaW4oXCIrXCIpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg9PTEpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGROYW1lID0gJG1ldGFfcmFuZ2UuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cInJhbmdlLWNoZWNrYm94XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0Q2hlY2tib3hWYWwoJGZpZWxkLCBcImFuZFwiKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZihmaWVsZE5hbWU9PVwiXCIpXG5cdFx0XHR7XG5cdFx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0ZWxzZSBpZihtZXRhVHlwZT09XCJjaG9pY2VcIilcblx0XHR7XG5cdFx0XHRpZihpbnB1dFR5cGU9PVwic2VsZWN0XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdFx0XG5cdFx0XHRcdGZpZWxkVmFsID0gc2VsZi5nZXRNZXRhU2VsZWN0VmFsKCRmaWVsZCk7IFxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cIm11bHRpc2VsZWN0XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdFx0dmFyIG9wZXJhdG9yID0gJGZpZWxkLmF0dHIoXCJkYXRhLW9wZXJhdG9yXCIpO1xuXHRcdFx0XHRcblx0XHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldE1ldGFNdWx0aVNlbGVjdFZhbCgkZmllbGQsIG9wZXJhdG9yKTtcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cImNoZWNrYm94XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHR2YXIgb3BlcmF0b3IgPSAkY29udGFpbmVyLmZpbmQoXCI+IHVsXCIpLmF0dHIoXCJkYXRhLW9wZXJhdG9yXCIpO1xuXHRcdFx0XHRcdGZpZWxkVmFsID0gc2VsZi5nZXRNZXRhQ2hlY2tib3hWYWwoJGZpZWxkLCBvcGVyYXRvcik7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cInJhZGlvXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6cmFkaW9cIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0TWV0YVJhZGlvVmFsKCRmaWVsZCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZmllbGRWYWwgPSBlbmNvZGVVUklDb21wb25lbnQoZmllbGRWYWwpO1xuXHRcdFx0aWYodHlwZW9mKCRmaWVsZCkhPT1cInVuZGVmaW5lZFwiKVxuXHRcdFx0e1xuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly9mb3IgdGhvc2Ugd2hvIGluc2lzdCBvbiB1c2luZyAmIGFtcGVyc2FuZHMgaW4gdGhlIG5hbWUgb2YgdGhlIGN1c3RvbSBmaWVsZCAoISlcblx0XHRcdFx0XHRmaWVsZE5hbWUgPSAoZmllbGROYW1lKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0fVxuXHRcdGVsc2UgaWYobWV0YVR5cGU9PVwiZGF0ZVwiKVxuXHRcdHtcblx0XHRcdHNlbGYucHJvY2Vzc1Bvc3REYXRlKCRjb250YWluZXIpO1xuXHRcdH1cblx0XHRcblx0XHRpZih0eXBlb2YoZmllbGRWYWwpIT1cInVuZGVmaW5lZFwiKVxuXHRcdHtcblx0XHRcdGlmKGZpZWxkVmFsIT1cIlwiKVxuXHRcdFx0e1xuXHRcdFx0XHQvL3NlbGYudXJsX2NvbXBvbmVudHMgKz0gXCImXCIrZW5jb2RlVVJJQ29tcG9uZW50KGZpZWxkTmFtZSkrXCI9XCIrKGZpZWxkVmFsKTtcblx0XHRcdFx0c2VsZi51cmxfcGFyYW1zW2VuY29kZVVSSUNvbXBvbmVudChmaWVsZE5hbWUpXSA9IChmaWVsZFZhbCk7XG5cdFx0XHR9XG5cdFx0fVxuXHR9LFxuXHRwcm9jZXNzUG9zdERhdGU6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cdFx0XG5cdFx0dmFyIGZpZWxkVHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtdHlwZVwiKTtcblx0XHR2YXIgaW5wdXRUeXBlID0gJGNvbnRhaW5lci5hdHRyKFwiZGF0YS1zZi1maWVsZC1pbnB1dC10eXBlXCIpO1xuXHRcdFxuXHRcdHZhciAkZmllbGQ7XG5cdFx0dmFyIGZpZWxkTmFtZSA9IFwiXCI7XG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHQkZmllbGQgPSAkY29udGFpbmVyLmZpbmQoXCJ1bCA+IGxpIGlucHV0OnRleHRcIik7XG5cdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFxuXHRcdHZhciBkYXRlcyA9IFtdO1xuXHRcdCRmaWVsZC5lYWNoKGZ1bmN0aW9uKCl7XG5cdFx0XHRcblx0XHRcdGRhdGVzLnB1c2goJCh0aGlzKS52YWwoKSk7XG5cdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0aWYoJGZpZWxkLmxlbmd0aD09Milcblx0XHR7XG5cdFx0XHRpZigoZGF0ZXNbMF0hPVwiXCIpfHwoZGF0ZXNbMV0hPVwiXCIpKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZFZhbCA9IGRhdGVzLmpvaW4oXCIrXCIpO1xuXHRcdFx0XHRmaWVsZFZhbCA9IGZpZWxkVmFsLnJlcGxhY2UoL1xcLy9nLCcnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0ZWxzZSBpZigkZmllbGQubGVuZ3RoPT0xKVxuXHRcdHtcblx0XHRcdGlmKGRhdGVzWzBdIT1cIlwiKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZFZhbCA9IGRhdGVzLmpvaW4oXCIrXCIpO1xuXHRcdFx0XHRmaWVsZFZhbCA9IGZpZWxkVmFsLnJlcGxhY2UoL1xcLy9nLCcnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKGZpZWxkVmFsKSE9XCJ1bmRlZmluZWRcIilcblx0XHR7XG5cdFx0XHRpZihmaWVsZFZhbCE9XCJcIilcblx0XHRcdHtcblx0XHRcdFx0dmFyIGZpZWxkU2x1ZyA9IFwiXCI7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZihmaWVsZE5hbWU9PVwiX3NmX3Bvc3RfZGF0ZVwiKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGRTbHVnID0gXCJwb3N0X2RhdGVcIjtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZFNsdWcgPSBmaWVsZE5hbWU7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdGlmKGZpZWxkU2x1ZyE9XCJcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdC8vc2VsZi51cmxfY29tcG9uZW50cyArPSBcIiZcIitmaWVsZFNsdWcrXCI9XCIrZmllbGRWYWw7XG5cdFx0XHRcdFx0c2VsZi51cmxfcGFyYW1zW2ZpZWxkU2x1Z10gPSBmaWVsZFZhbDtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH1cblx0XHRcblx0fSxcblx0cHJvY2Vzc1RheG9ub215OiBmdW5jdGlvbigkY29udGFpbmVyLCByZXR1cm5fb2JqZWN0KVxuXHR7XG4gICAgICAgIGlmKHR5cGVvZihyZXR1cm5fb2JqZWN0KT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgcmV0dXJuX29iamVjdCA9IGZhbHNlO1xuICAgICAgICB9XG5cblx0XHQvL2lmKClcdFx0XHRcdFx0XG5cdFx0Ly92YXIgZmllbGROYW1lID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC1uYW1lXCIpO1xuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XG5cdFx0dmFyIGZpZWxkVHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtdHlwZVwiKTtcblx0XHR2YXIgaW5wdXRUeXBlID0gJGNvbnRhaW5lci5hdHRyKFwiZGF0YS1zZi1maWVsZC1pbnB1dC10eXBlXCIpO1xuXHRcdFxuXHRcdHZhciAkZmllbGQ7XG5cdFx0dmFyIGZpZWxkTmFtZSA9IFwiXCI7XG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHRpZihpbnB1dFR5cGU9PVwic2VsZWN0XCIpXG5cdFx0e1xuXHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwic2VsZWN0XCIpO1xuXHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFx0XG5cdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0U2VsZWN0VmFsKCRmaWVsZCk7IFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJtdWx0aXNlbGVjdFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdHZhciBvcGVyYXRvciA9ICRmaWVsZC5hdHRyKFwiZGF0YS1vcGVyYXRvclwiKTtcblx0XHRcdFxuXHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldE11bHRpU2VsZWN0VmFsKCRmaWVsZCwgb3BlcmF0b3IpO1xuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJjaGVja2JveFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHR7XG5cdFx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdHZhciBvcGVyYXRvciA9ICRjb250YWluZXIuZmluZChcIj4gdWxcIikuYXR0cihcImRhdGEtb3BlcmF0b3JcIik7XG5cdFx0XHRcdGZpZWxkVmFsID0gc2VsZi5nZXRDaGVja2JveFZhbCgkZmllbGQsIG9wZXJhdG9yKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0ZWxzZSBpZihpbnB1dFR5cGU9PVwicmFkaW9cIilcblx0XHR7XG5cdFx0XHQkZmllbGQgPSAkY29udGFpbmVyLmZpbmQoXCJ1bCA+IGxpIGlucHV0OnJhZGlvXCIpO1xuXHRcdFx0aWYoJGZpZWxkLmxlbmd0aD4wKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0UmFkaW9WYWwoJGZpZWxkKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKGZpZWxkVmFsKSE9XCJ1bmRlZmluZWRcIilcblx0XHR7XG5cdFx0XHRpZihmaWVsZFZhbCE9XCJcIilcblx0XHRcdHtcbiAgICAgICAgICAgICAgICBpZihyZXR1cm5fb2JqZWN0PT10cnVlKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtuYW1lOiBmaWVsZE5hbWUsIHZhbHVlOiBmaWVsZFZhbH07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIC8vc2VsZi51cmxfY29tcG9uZW50cyArPSBcIiZcIitmaWVsZE5hbWUrXCI9XCIrZmllbGRWYWw7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYudXJsX3BhcmFtc1tmaWVsZE5hbWVdID0gZmllbGRWYWw7XG4gICAgICAgICAgICAgICAgfVxuXG5cdFx0XHR9XG5cdFx0fVxuXG4gICAgICAgIGlmKHJldHVybl9vYmplY3Q9PXRydWUpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXHR9XG59OyJdfQ==
},{}],5:[function(require,module,exports){

module.exports = {
	
	searchForms: {},
	
	init: function(){
		
		
	},
	addSearchForm: function(id, object){
		
		this.searchForms[id] = object;
	},
	getSearchForm: function(id)
	{
		return this.searchForms[id];	
	}
	
};
},{}],6:[function(require,module,exports){
(function (global){

var $ 				= (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null);

module.exports = {
	
	init: function(){
		$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
			var display_method = data.object.display_result_method;
			if ( display_method === 'custom_edd_store' ) {
				$('input.edd-add-to-cart').css('display', "none");
				$('a.edd-add-to-cart').addClass('edd-has-js');
			} else if ( display_method === 'custom_layouts' ) {
				if ( $('.cl-layout').hasClass( 'cl-layout--masonry' ) ) {
					//then re-init masonry
					const masonryContainer = document.querySelectorAll( '.cl-layout--masonry' );
					if ( masonryContainer.length > 0 ) {
						const customLayoutGrid = new Masonry( '.cl-layout--masonry', {
							// options...
							itemSelector: '.cl-layout__item',
							//columnWidth: 319
							percentPosition: true,
							//gutter: 10,
							transitionDuration: 0,
						} );
						imagesLoaded( masonryContainer ).on( 'progress', function() {
							customLayoutGrid.layout();
						} );
					}
				}
			}
		});
	},

};
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
//# sourceMappingURL=data:application/json;charset:utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9wdWJsaWMvYXNzZXRzL2pzL2luY2x1ZGVzL3RoaXJkcGFydHkuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyJcbnZhciAkIFx0XHRcdFx0PSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHtcblx0XG5cdGluaXQ6IGZ1bmN0aW9uKCl7XG5cdFx0JChkb2N1bWVudCkub24oXCJzZjphamF4ZmluaXNoXCIsIFwiLnNlYXJjaGFuZGZpbHRlclwiLCBmdW5jdGlvbiggZSwgZGF0YSApIHtcblx0XHRcdHZhciBkaXNwbGF5X21ldGhvZCA9IGRhdGEub2JqZWN0LmRpc3BsYXlfcmVzdWx0X21ldGhvZDtcblx0XHRcdGlmICggZGlzcGxheV9tZXRob2QgPT09ICdjdXN0b21fZWRkX3N0b3JlJyApIHtcblx0XHRcdFx0JCgnaW5wdXQuZWRkLWFkZC10by1jYXJ0JykuY3NzKCdkaXNwbGF5JywgXCJub25lXCIpO1xuXHRcdFx0XHQkKCdhLmVkZC1hZGQtdG8tY2FydCcpLmFkZENsYXNzKCdlZGQtaGFzLWpzJyk7XG5cdFx0XHR9IGVsc2UgaWYgKCBkaXNwbGF5X21ldGhvZCA9PT0gJ2N1c3RvbV9sYXlvdXRzJyApIHtcblx0XHRcdFx0aWYgKCAkKCcuY2wtbGF5b3V0JykuaGFzQ2xhc3MoICdjbC1sYXlvdXQtLW1hc29ucnknICkgKSB7XG5cdFx0XHRcdFx0Ly90aGVuIHJlLWluaXQgbWFzb25yeVxuXHRcdFx0XHRcdGNvbnN0IG1hc29ucnlDb250YWluZXIgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCAnLmNsLWxheW91dC0tbWFzb25yeScgKTtcblx0XHRcdFx0XHRpZiAoIG1hc29ucnlDb250YWluZXIubGVuZ3RoID4gMCApIHtcblx0XHRcdFx0XHRcdGNvbnN0IGN1c3RvbUxheW91dEdyaWQgPSBuZXcgTWFzb25yeSggJy5jbC1sYXlvdXQtLW1hc29ucnknLCB7XG5cdFx0XHRcdFx0XHRcdC8vIG9wdGlvbnMuLi5cblx0XHRcdFx0XHRcdFx0aXRlbVNlbGVjdG9yOiAnLmNsLWxheW91dF9faXRlbScsXG5cdFx0XHRcdFx0XHRcdC8vY29sdW1uV2lkdGg6IDMxOVxuXHRcdFx0XHRcdFx0XHRwZXJjZW50UG9zaXRpb246IHRydWUsXG5cdFx0XHRcdFx0XHRcdC8vZ3V0dGVyOiAxMCxcblx0XHRcdFx0XHRcdFx0dHJhbnNpdGlvbkR1cmF0aW9uOiAwLFxuXHRcdFx0XHRcdFx0fSApO1xuXHRcdFx0XHRcdFx0aW1hZ2VzTG9hZGVkKCBtYXNvbnJ5Q29udGFpbmVyICkub24oICdwcm9ncmVzcycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRjdXN0b21MYXlvdXRHcmlkLmxheW91dCgpO1xuXHRcdFx0XHRcdFx0fSApO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH0pO1xuXHR9LFxuXG59OyJdfQ==
},{}]},{},[1])
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvcHVibGljL2Fzc2V0cy9qcy9hcHAuanMiLCJub2RlX21vZHVsZXMvbm91aXNsaWRlci9kaXN0cmlidXRlL25vdWlzbGlkZXIuanMiLCJzcmMvcHVibGljL2Fzc2V0cy9qcy9pbmNsdWRlcy9wbHVnaW4uanMiLCJzcmMvcHVibGljL2Fzc2V0cy9qcy9pbmNsdWRlcy9wcm9jZXNzX2Zvcm0uanMiLCJzcmMvcHVibGljL2Fzc2V0cy9qcy9pbmNsdWRlcy9zdGF0ZS5qcyIsInNyYy9wdWJsaWMvYXNzZXRzL2pzL2luY2x1ZGVzL3RoaXJkcGFydHkuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RRQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMxeUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDMXRFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM1OEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2xCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCIoZnVuY3Rpb24gKGdsb2JhbCl7XG5cbnZhciBzdGF0ZSA9IHJlcXVpcmUoJy4vaW5jbHVkZXMvc3RhdGUnKTtcbnZhciBwbHVnaW4gPSByZXF1aXJlKCcuL2luY2x1ZGVzL3BsdWdpbicpO1xuXG5cbihmdW5jdGlvbiAoICQgKSB7XG5cblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0JChmdW5jdGlvbiAoKSB7XG5cblx0XHRpZiAoIU9iamVjdC5rZXlzKSB7XG5cdFx0ICBPYmplY3Qua2V5cyA9IChmdW5jdGlvbiAoKSB7XG5cdFx0XHQndXNlIHN0cmljdCc7XG5cdFx0XHR2YXIgaGFzT3duUHJvcGVydHkgPSBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LFxuXHRcdFx0XHRoYXNEb250RW51bUJ1ZyA9ICEoe3RvU3RyaW5nOiBudWxsfSkucHJvcGVydHlJc0VudW1lcmFibGUoJ3RvU3RyaW5nJyksXG5cdFx0XHRcdGRvbnRFbnVtcyA9IFtcblx0XHRcdFx0ICAndG9TdHJpbmcnLFxuXHRcdFx0XHQgICd0b0xvY2FsZVN0cmluZycsXG5cdFx0XHRcdCAgJ3ZhbHVlT2YnLFxuXHRcdFx0XHQgICdoYXNPd25Qcm9wZXJ0eScsXG5cdFx0XHRcdCAgJ2lzUHJvdG90eXBlT2YnLFxuXHRcdFx0XHQgICdwcm9wZXJ0eUlzRW51bWVyYWJsZScsXG5cdFx0XHRcdCAgJ2NvbnN0cnVjdG9yJ1xuXHRcdFx0XHRdLFxuXHRcdFx0XHRkb250RW51bXNMZW5ndGggPSBkb250RW51bXMubGVuZ3RoO1xuXG5cdFx0XHRyZXR1cm4gZnVuY3Rpb24gKG9iaikge1xuXHRcdFx0ICBpZiAodHlwZW9mIG9iaiAhPT0gJ29iamVjdCcgJiYgKHR5cGVvZiBvYmogIT09ICdmdW5jdGlvbicgfHwgb2JqID09PSBudWxsKSkge1xuXHRcdFx0XHR0aHJvdyBuZXcgVHlwZUVycm9yKCdPYmplY3Qua2V5cyBjYWxsZWQgb24gbm9uLW9iamVjdCcpO1xuXHRcdFx0ICB9XG5cblx0XHRcdCAgdmFyIHJlc3VsdCA9IFtdLCBwcm9wLCBpO1xuXG5cdFx0XHQgIGZvciAocHJvcCBpbiBvYmopIHtcblx0XHRcdFx0aWYgKGhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkge1xuXHRcdFx0XHQgIHJlc3VsdC5wdXNoKHByb3ApO1xuXHRcdFx0XHR9XG5cdFx0XHQgIH1cblxuXHRcdFx0ICBpZiAoaGFzRG9udEVudW1CdWcpIHtcblx0XHRcdFx0Zm9yIChpID0gMDsgaSA8IGRvbnRFbnVtc0xlbmd0aDsgaSsrKSB7XG5cdFx0XHRcdCAgaWYgKGhhc093blByb3BlcnR5LmNhbGwob2JqLCBkb250RW51bXNbaV0pKSB7XG5cdFx0XHRcdFx0cmVzdWx0LnB1c2goZG9udEVudW1zW2ldKTtcblx0XHRcdFx0ICB9XG5cdFx0XHRcdH1cblx0XHRcdCAgfVxuXHRcdFx0ICByZXR1cm4gcmVzdWx0O1xuXHRcdFx0fTtcblx0XHQgIH0oKSk7XG5cdFx0fVxuXG5cdFx0LyogU2VhcmNoICYgRmlsdGVyIGpRdWVyeSBQbHVnaW4gKi9cblx0XHQkLmZuLnNlYXJjaEFuZEZpbHRlciA9IHBsdWdpbjtcblxuXHRcdC8qIGluaXQgKi9cblx0XHQkKFwiLnNlYXJjaGFuZGZpbHRlclwiKS5zZWFyY2hBbmRGaWx0ZXIoKTtcblxuXHRcdC8qIGV4dGVybmFsIGNvbnRyb2xzICovXG5cdFx0JChkb2N1bWVudCkub24oXCJjbGlja1wiLCBcIi5zZWFyY2gtZmlsdGVyLXJlc2V0XCIsIGZ1bmN0aW9uKGUpe1xuXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRcdHZhciBzZWFyY2hGb3JtSUQgPSB0eXBlb2YoJCh0aGlzKS5hdHRyKFwiZGF0YS1zZWFyY2gtZm9ybS1pZFwiKSkhPVwidW5kZWZpbmVkXCIgPyAkKHRoaXMpLmF0dHIoXCJkYXRhLXNlYXJjaC1mb3JtLWlkXCIpIDogXCJcIjtcblx0XHRcdHZhciBzdWJtaXRGb3JtID0gdHlwZW9mKCQodGhpcykuYXR0cihcImRhdGEtc2Ytc3VibWl0LWZvcm1cIikpIT1cInVuZGVmaW5lZFwiID8gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1zdWJtaXQtZm9ybVwiKSA6IFwiXCI7XG5cblx0XHRcdHN0YXRlLmdldFNlYXJjaEZvcm0oc2VhcmNoRm9ybUlEKS5yZXNldChzdWJtaXRGb3JtKTtcblxuXHRcdFx0Ly92YXIgJGxpbmtlZCA9ICQoXCIjc2VhcmNoLWZpbHRlci1mb3JtLVwiK3NlYXJjaEZvcm1JRCkuc2VhcmNoRmlsdGVyRm9ybSh7YWN0aW9uOiBcInJlc2V0XCJ9KTtcblxuXHRcdFx0cmV0dXJuIGZhbHNlO1xuXG5cdFx0fSk7XG5cblx0fSk7XG5cblxuLypcbiAqIGpRdWVyeSBFYXNpbmcgdjEuNC4xIC0gaHR0cDovL2dzZ2QuY28udWsvc2FuZGJveC9qcXVlcnkvZWFzaW5nL1xuICogT3BlbiBzb3VyY2UgdW5kZXIgdGhlIEJTRCBMaWNlbnNlLlxuICogQ29weXJpZ2h0IMKpIDIwMDggR2VvcmdlIE1jR2lubGV5IFNtaXRoXG4gKiBBbGwgcmlnaHRzIHJlc2VydmVkLlxuICogaHR0cHM6Ly9yYXcuZ2l0aHViLmNvbS9nZHNtaXRoL2pxdWVyeS5lYXNpbmcvbWFzdGVyL0xJQ0VOU0VcbiovXG5cbi8qIGdsb2JhbHMgalF1ZXJ5LCBkZWZpbmUsIG1vZHVsZSwgcmVxdWlyZSAqL1xuKGZ1bmN0aW9uIChmYWN0b3J5KSB7XG5cdGlmICh0eXBlb2YgZGVmaW5lID09PSBcImZ1bmN0aW9uXCIgJiYgZGVmaW5lLmFtZCkge1xuXHRcdGRlZmluZShbJ2pxdWVyeSddLCBmdW5jdGlvbiAoJCkge1xuXHRcdFx0cmV0dXJuIGZhY3RvcnkoJCk7XG5cdFx0fSk7XG5cdH0gZWxzZSBpZiAodHlwZW9mIG1vZHVsZSA9PT0gXCJvYmplY3RcIiAmJiB0eXBlb2YgbW9kdWxlLmV4cG9ydHMgPT09IFwib2JqZWN0XCIpIHtcblx0XHRtb2R1bGUuZXhwb3J0cyA9IGZhY3RvcnkoKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSk7XG5cdH0gZWxzZSB7XG5cdFx0ZmFjdG9yeShqUXVlcnkpO1xuXHR9XG59KShmdW5jdGlvbigkKXtcblxuXHQvLyBQcmVzZXJ2ZSB0aGUgb3JpZ2luYWwgalF1ZXJ5IFwic3dpbmdcIiBlYXNpbmcgYXMgXCJqc3dpbmdcIlxuXHRpZiAodHlwZW9mICQuZWFzaW5nICE9PSAndW5kZWZpbmVkJykge1xuXHRcdCQuZWFzaW5nWydqc3dpbmcnXSA9ICQuZWFzaW5nWydzd2luZyddO1xuXHR9XG5cblx0dmFyIHBvdyA9IE1hdGgucG93LFxuXHRcdHNxcnQgPSBNYXRoLnNxcnQsXG5cdFx0c2luID0gTWF0aC5zaW4sXG5cdFx0Y29zID0gTWF0aC5jb3MsXG5cdFx0UEkgPSBNYXRoLlBJLFxuXHRcdGMxID0gMS43MDE1OCxcblx0XHRjMiA9IGMxICogMS41MjUsXG5cdFx0YzMgPSBjMSArIDEsXG5cdFx0YzQgPSAoIDIgKiBQSSApIC8gMyxcblx0XHRjNSA9ICggMiAqIFBJICkgLyA0LjU7XG5cblx0Ly8geCBpcyB0aGUgZnJhY3Rpb24gb2YgYW5pbWF0aW9uIHByb2dyZXNzLCBpbiB0aGUgcmFuZ2UgMC4uMVxuXHRmdW5jdGlvbiBib3VuY2VPdXQoeCkge1xuXHRcdHZhciBuMSA9IDcuNTYyNSxcblx0XHRcdGQxID0gMi43NTtcblx0XHRpZiAoIHggPCAxL2QxICkge1xuXHRcdFx0cmV0dXJuIG4xKngqeDtcblx0XHR9IGVsc2UgaWYgKCB4IDwgMi9kMSApIHtcblx0XHRcdHJldHVybiBuMSooeC09KDEuNS9kMSkpKnggKyAuNzU7XG5cdFx0fSBlbHNlIGlmICggeCA8IDIuNS9kMSApIHtcblx0XHRcdHJldHVybiBuMSooeC09KDIuMjUvZDEpKSp4ICsgLjkzNzU7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHJldHVybiBuMSooeC09KDIuNjI1L2QxKSkqeCArIC45ODQzNzU7XG5cdFx0fVxuXHR9XG5cblx0JC5leHRlbmQoICQuZWFzaW5nLCB7XG5cdFx0ZGVmOiAnZWFzZU91dFF1YWQnLFxuXHRcdHN3aW5nOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuICQuZWFzaW5nWyQuZWFzaW5nLmRlZl0oeCk7XG5cdFx0fSxcblx0XHRlYXNlSW5RdWFkOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggKiB4O1xuXHRcdH0sXG5cdFx0ZWFzZU91dFF1YWQ6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtICggMSAtIHggKSAqICggMSAtIHggKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dFF1YWQ6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA8IDAuNSA/XG5cdFx0XHRcdDIgKiB4ICogeCA6XG5cdFx0XHRcdDEgLSBwb3coIC0yICogeCArIDIsIDIgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5DdWJpYzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ICogeCAqIHg7XG5cdFx0fSxcblx0XHRlYXNlT3V0Q3ViaWM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtIHBvdyggMSAtIHgsIDMgKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dEN1YmljOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPCAwLjUgP1xuXHRcdFx0XHQ0ICogeCAqIHggKiB4IDpcblx0XHRcdFx0MSAtIHBvdyggLTIgKiB4ICsgMiwgMyApIC8gMjtcblx0XHR9LFxuXHRcdGVhc2VJblF1YXJ0OiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggKiB4ICogeCAqIHg7XG5cdFx0fSxcblx0XHRlYXNlT3V0UXVhcnQ6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtIHBvdyggMSAtIHgsIDQgKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dFF1YXJ0OiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPCAwLjUgP1xuXHRcdFx0XHQ4ICogeCAqIHggKiB4ICogeCA6XG5cdFx0XHRcdDEgLSBwb3coIC0yICogeCArIDIsIDQgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5RdWludDogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ICogeCAqIHggKiB4ICogeDtcblx0XHR9LFxuXHRcdGVhc2VPdXRRdWludDogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiAxIC0gcG93KCAxIC0geCwgNSApO1xuXHRcdH0sXG5cdFx0ZWFzZUluT3V0UXVpbnQ6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA8IDAuNSA/XG5cdFx0XHRcdDE2ICogeCAqIHggKiB4ICogeCAqIHggOlxuXHRcdFx0XHQxIC0gcG93KCAtMiAqIHggKyAyLCA1ICkgLyAyO1xuXHRcdH0sXG5cdFx0ZWFzZUluU2luZTogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiAxIC0gY29zKCB4ICogUEkvMiApO1xuXHRcdH0sXG5cdFx0ZWFzZU91dFNpbmU6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gc2luKCB4ICogUEkvMiApO1xuXHRcdH0sXG5cdFx0ZWFzZUluT3V0U2luZTogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiAtKCBjb3MoIFBJICogeCApIC0gMSApIC8gMjtcblx0XHR9LFxuXHRcdGVhc2VJbkV4cG86IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA9PT0gMCA/IDAgOiBwb3coIDIsIDEwICogeCAtIDEwICk7XG5cdFx0fSxcblx0XHRlYXNlT3V0RXhwbzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ID09PSAxID8gMSA6IDEgLSBwb3coIDIsIC0xMCAqIHggKTtcblx0XHR9LFxuXHRcdGVhc2VJbk91dEV4cG86IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA9PT0gMCA/IDAgOiB4ID09PSAxID8gMSA6IHggPCAwLjUgP1xuXHRcdFx0XHRwb3coIDIsIDIwICogeCAtIDEwICkgLyAyIDpcblx0XHRcdFx0KCAyIC0gcG93KCAyLCAtMjAgKiB4ICsgMTAgKSApIC8gMjtcblx0XHR9LFxuXHRcdGVhc2VJbkNpcmM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gMSAtIHNxcnQoIDEgLSBwb3coIHgsIDIgKSApO1xuXHRcdH0sXG5cdFx0ZWFzZU91dENpcmM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4gc3FydCggMSAtIHBvdyggeCAtIDEsIDIgKSApO1xuXHRcdH0sXG5cdFx0ZWFzZUluT3V0Q2lyYzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4IDwgMC41ID9cblx0XHRcdFx0KCAxIC0gc3FydCggMSAtIHBvdyggMiAqIHgsIDIgKSApICkgLyAyIDpcblx0XHRcdFx0KCBzcXJ0KCAxIC0gcG93KCAtMiAqIHggKyAyLCAyICkgKSArIDEgKSAvIDI7XG5cdFx0fSxcblx0XHRlYXNlSW5FbGFzdGljOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIHggPT09IDAgPyAwIDogeCA9PT0gMSA/IDEgOlxuXHRcdFx0XHQtcG93KCAyLCAxMCAqIHggLSAxMCApICogc2luKCAoIHggKiAxMCAtIDEwLjc1ICkgKiBjNCApO1xuXHRcdH0sXG5cdFx0ZWFzZU91dEVsYXN0aWM6IGZ1bmN0aW9uICh4KSB7XG5cdFx0XHRyZXR1cm4geCA9PT0gMCA/IDAgOiB4ID09PSAxID8gMSA6XG5cdFx0XHRcdHBvdyggMiwgLTEwICogeCApICogc2luKCAoIHggKiAxMCAtIDAuNzUgKSAqIGM0ICkgKyAxO1xuXHRcdH0sXG5cdFx0ZWFzZUluT3V0RWxhc3RpYzogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4ID09PSAwID8gMCA6IHggPT09IDEgPyAxIDogeCA8IDAuNSA/XG5cdFx0XHRcdC0oIHBvdyggMiwgMjAgKiB4IC0gMTAgKSAqIHNpbiggKCAyMCAqIHggLSAxMS4xMjUgKSAqIGM1ICkpIC8gMiA6XG5cdFx0XHRcdHBvdyggMiwgLTIwICogeCArIDEwICkgKiBzaW4oICggMjAgKiB4IC0gMTEuMTI1ICkgKiBjNSApIC8gMiArIDE7XG5cdFx0fSxcblx0XHRlYXNlSW5CYWNrOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIGMzICogeCAqIHggKiB4IC0gYzEgKiB4ICogeDtcblx0XHR9LFxuXHRcdGVhc2VPdXRCYWNrOiBmdW5jdGlvbiAoeCkge1xuXHRcdFx0cmV0dXJuIDEgKyBjMyAqIHBvdyggeCAtIDEsIDMgKSArIGMxICogcG93KCB4IC0gMSwgMiApO1xuXHRcdH0sXG5cdFx0ZWFzZUluT3V0QmFjazogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4IDwgMC41ID9cblx0XHRcdFx0KCBwb3coIDIgKiB4LCAyICkgKiAoICggYzIgKyAxICkgKiAyICogeCAtIGMyICkgKSAvIDIgOlxuXHRcdFx0XHQoIHBvdyggMiAqIHggLSAyLCAyICkgKiggKCBjMiArIDEgKSAqICggeCAqIDIgLSAyICkgKyBjMiApICsgMiApIC8gMjtcblx0XHR9LFxuXHRcdGVhc2VJbkJvdW5jZTogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiAxIC0gYm91bmNlT3V0KCAxIC0geCApO1xuXHRcdH0sXG5cdFx0ZWFzZU91dEJvdW5jZTogYm91bmNlT3V0LFxuXHRcdGVhc2VJbk91dEJvdW5jZTogZnVuY3Rpb24gKHgpIHtcblx0XHRcdHJldHVybiB4IDwgMC41ID9cblx0XHRcdFx0KCAxIC0gYm91bmNlT3V0KCAxIC0gMiAqIHggKSApIC8gMiA6XG5cdFx0XHRcdCggMSArIGJvdW5jZU91dCggMiAqIHggLSAxICkgKSAvIDI7XG5cdFx0fVxuXHR9KTtcblx0cmV0dXJuICQ7XG59KTtcblxufShqUXVlcnkpKTtcblxuLy9zYWZhcmkgYmFjayBidXR0b24gZml4XG5qUXVlcnkoIHdpbmRvdyApLm9uKCBcInBhZ2VzaG93XCIsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgaWYgKGV2ZW50Lm9yaWdpbmFsRXZlbnQucGVyc2lzdGVkKSB7XG4gICAgICAgIGpRdWVyeShcIi5zZWFyY2hhbmRmaWx0ZXJcIikub2ZmKCk7XG4gICAgICAgIGpRdWVyeShcIi5zZWFyY2hhbmRmaWx0ZXJcIikuc2VhcmNoQW5kRmlsdGVyKCk7XG4gICAgfVxufSk7XG5cbi8qIHdwbnVtYiAtIG5vdWlzbGlkZXIgbnVtYmVyIGZvcm1hdHRpbmcgKi9cbiFmdW5jdGlvbigpe1widXNlIHN0cmljdFwiO2Z1bmN0aW9uIGUoZSl7cmV0dXJuIGUuc3BsaXQoXCJcIikucmV2ZXJzZSgpLmpvaW4oXCJcIil9ZnVuY3Rpb24gbihlLG4pe3JldHVybiBlLnN1YnN0cmluZygwLG4ubGVuZ3RoKT09PW59ZnVuY3Rpb24gcihlLG4pe3JldHVybiBlLnNsaWNlKC0xKm4ubGVuZ3RoKT09PW59ZnVuY3Rpb24gdChlLG4scil7aWYoKGVbbl18fGVbcl0pJiZlW25dPT09ZVtyXSl0aHJvdyBuZXcgRXJyb3Iobil9ZnVuY3Rpb24gaShlKXtyZXR1cm5cIm51bWJlclwiPT10eXBlb2YgZSYmaXNGaW5pdGUoZSl9ZnVuY3Rpb24gbyhlLG4pe3ZhciByPU1hdGgucG93KDEwLG4pO3JldHVybihNYXRoLnJvdW5kKGUqcikvcikudG9GaXhlZChuKX1mdW5jdGlvbiB1KG4scix0LHUsZixhLGMscyxwLGQsbCxoKXt2YXIgZyx2LHcsbT1oLHg9XCJcIixiPVwiXCI7cmV0dXJuIGEmJihoPWEoaCkpLGkoaCk/KG4hPT0hMSYmMD09PXBhcnNlRmxvYXQoaC50b0ZpeGVkKG4pKSYmKGg9MCksMD5oJiYoZz0hMCxoPU1hdGguYWJzKGgpKSxuIT09ITEmJihoPW8oaCxuKSksaD1oLnRvU3RyaW5nKCksLTEhPT1oLmluZGV4T2YoXCIuXCIpPyh2PWguc3BsaXQoXCIuXCIpLHc9dlswXSx0JiYoeD10K3ZbMV0pKTp3PWgsciYmKHc9ZSh3KS5tYXRjaCgvLnsxLDN9L2cpLHc9ZSh3LmpvaW4oZShyKSkpKSxnJiZzJiYoYis9cyksdSYmKGIrPXUpLGcmJnAmJihiKz1wKSxiKz13LGIrPXgsZiYmKGIrPWYpLGQmJihiPWQoYixtKSksYik6ITF9ZnVuY3Rpb24gZihlLHQsbyx1LGYsYSxjLHMscCxkLGwsaCl7dmFyIGcsdj1cIlwiO3JldHVybiBsJiYoaD1sKGgpKSxoJiZcInN0cmluZ1wiPT10eXBlb2YgaD8ocyYmbihoLHMpJiYoaD1oLnJlcGxhY2UocyxcIlwiKSxnPSEwKSx1JiZuKGgsdSkmJihoPWgucmVwbGFjZSh1LFwiXCIpKSxwJiZuKGgscCkmJihoPWgucmVwbGFjZShwLFwiXCIpLGc9ITApLGYmJnIoaCxmKSYmKGg9aC5zbGljZSgwLC0xKmYubGVuZ3RoKSksdCYmKGg9aC5zcGxpdCh0KS5qb2luKFwiXCIpKSxvJiYoaD1oLnJlcGxhY2UobyxcIi5cIikpLGcmJih2Kz1cIi1cIiksdis9aCx2PXYucmVwbGFjZSgvW14wLTlcXC5cXC0uXS9nLFwiXCIpLFwiXCI9PT12PyExOih2PU51bWJlcih2KSxjJiYodj1jKHYpKSxpKHYpP3Y6ITEpKTohMX1mdW5jdGlvbiBhKGUpe3ZhciBuLHIsaSxvPXt9O2ZvcihuPTA7bjxwLmxlbmd0aDtuKz0xKWlmKHI9cFtuXSxpPWVbcl0sdm9pZCAwPT09aSlcIm5lZ2F0aXZlXCIhPT1yfHxvLm5lZ2F0aXZlQmVmb3JlP1wibWFya1wiPT09ciYmXCIuXCIhPT1vLnRob3VzYW5kP29bcl09XCIuXCI6b1tyXT0hMTpvW3JdPVwiLVwiO2Vsc2UgaWYoXCJkZWNpbWFsc1wiPT09cil7aWYoIShpPj0wJiY4PmkpKXRocm93IG5ldyBFcnJvcihyKTtvW3JdPWl9ZWxzZSBpZihcImVuY29kZXJcIj09PXJ8fFwiZGVjb2RlclwiPT09cnx8XCJlZGl0XCI9PT1yfHxcInVuZG9cIj09PXIpe2lmKFwiZnVuY3Rpb25cIiE9dHlwZW9mIGkpdGhyb3cgbmV3IEVycm9yKHIpO29bcl09aX1lbHNle2lmKFwic3RyaW5nXCIhPXR5cGVvZiBpKXRocm93IG5ldyBFcnJvcihyKTtvW3JdPWl9cmV0dXJuIHQobyxcIm1hcmtcIixcInRob3VzYW5kXCIpLHQobyxcInByZWZpeFwiLFwibmVnYXRpdmVcIiksdChvLFwicHJlZml4XCIsXCJuZWdhdGl2ZUJlZm9yZVwiKSxvfWZ1bmN0aW9uIGMoZSxuLHIpe3ZhciB0LGk9W107Zm9yKHQ9MDt0PHAubGVuZ3RoO3QrPTEpaS5wdXNoKGVbcFt0XV0pO3JldHVybiBpLnB1c2gociksbi5hcHBseShcIlwiLGkpfWZ1bmN0aW9uIHMoZSl7cmV0dXJuIHRoaXMgaW5zdGFuY2VvZiBzP3ZvaWQoXCJvYmplY3RcIj09dHlwZW9mIGUmJihlPWEoZSksdGhpcy50bz1mdW5jdGlvbihuKXtyZXR1cm4gYyhlLHUsbil9LHRoaXMuZnJvbT1mdW5jdGlvbihuKXtyZXR1cm4gYyhlLGYsbil9KSk6bmV3IHMoZSl9dmFyIHA9W1wiZGVjaW1hbHNcIixcInRob3VzYW5kXCIsXCJtYXJrXCIsXCJwcmVmaXhcIixcInBvc3RmaXhcIixcImVuY29kZXJcIixcImRlY29kZXJcIixcIm5lZ2F0aXZlQmVmb3JlXCIsXCJuZWdhdGl2ZVwiLFwiZWRpdFwiLFwidW5kb1wiXTt3aW5kb3cud051bWI9c30oKTtcblxuXG59KS5jYWxsKHRoaXMsdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbCA6IHR5cGVvZiBzZWxmICE9PSBcInVuZGVmaW5lZFwiID8gc2VsZiA6IHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3cgOiB7fSlcbi8vIyBzb3VyY2VNYXBwaW5nVVJMPWRhdGE6YXBwbGljYXRpb24vanNvbjtjaGFyc2V0OnV0Zi04O2Jhc2U2NCxleUoyWlhKemFXOXVJam96TENKemIzVnlZMlZ6SWpwYkluTnlZeTl3ZFdKc2FXTXZZWE56WlhSekwycHpMMkZ3Y0M1cWN5SmRMQ0p1WVcxbGN5STZXMTBzSW0xaGNIQnBibWR6SWpvaU8wRkJRVUU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRU0lzSW1acGJHVWlPaUpuWlc1bGNtRjBaV1F1YW5NaUxDSnpiM1Z5WTJWU2IyOTBJam9pSWl3aWMyOTFjbU5sYzBOdmJuUmxiblFpT2xzaVhHNTJZWElnYzNSaGRHVWdQU0J5WlhGMWFYSmxLQ2N1TDJsdVkyeDFaR1Z6TDNOMFlYUmxKeWs3WEc1MllYSWdjR3gxWjJsdUlEMGdjbVZ4ZFdseVpTZ25MaTlwYm1Oc2RXUmxjeTl3YkhWbmFXNG5LVHRjYmx4dVhHNG9ablZ1WTNScGIyNGdLQ0FrSUNrZ2UxeHVYRzVjZEZ3aWRYTmxJSE4wY21samRGd2lPMXh1WEc1Y2RDUW9ablZ1WTNScGIyNGdLQ2tnZTF4dVhHNWNkRngwYVdZZ0tDRlBZbXBsWTNRdWEyVjVjeWtnZTF4dVhIUmNkQ0FnVDJKcVpXTjBMbXRsZVhNZ1BTQW9ablZ1WTNScGIyNGdLQ2tnZTF4dVhIUmNkRngwSjNWelpTQnpkSEpwWTNRbk8xeHVYSFJjZEZ4MGRtRnlJR2hoYzA5M2JsQnliM0JsY25SNUlEMGdUMkpxWldOMExuQnliM1J2ZEhsd1pTNW9ZWE5QZDI1UWNtOXdaWEowZVN4Y2JseDBYSFJjZEZ4MGFHRnpSRzl1ZEVWdWRXMUNkV2NnUFNBaEtIdDBiMU4wY21sdVp6b2diblZzYkgwcExuQnliM0JsY25SNVNYTkZiblZ0WlhKaFlteGxLQ2QwYjFOMGNtbHVaeWNwTEZ4dVhIUmNkRngwWEhSa2IyNTBSVzUxYlhNZ1BTQmJYRzVjZEZ4MFhIUmNkQ0FnSjNSdlUzUnlhVzVuSnl4Y2JseDBYSFJjZEZ4MElDQW5kRzlNYjJOaGJHVlRkSEpwYm1jbkxGeHVYSFJjZEZ4MFhIUWdJQ2QyWVd4MVpVOW1KeXhjYmx4MFhIUmNkRngwSUNBbmFHRnpUM2R1VUhKdmNHVnlkSGtuTEZ4dVhIUmNkRngwWEhRZ0lDZHBjMUJ5YjNSdmRIbHdaVTltSnl4Y2JseDBYSFJjZEZ4MElDQW5jSEp2Y0dWeWRIbEpjMFZ1ZFcxbGNtRmliR1VuTEZ4dVhIUmNkRngwWEhRZ0lDZGpiMjV6ZEhKMVkzUnZjaWRjYmx4MFhIUmNkRngwWFN4Y2JseDBYSFJjZEZ4MFpHOXVkRVZ1ZFcxelRHVnVaM1JvSUQwZ1pHOXVkRVZ1ZFcxekxteGxibWQwYUR0Y2JseHVYSFJjZEZ4MGNtVjBkWEp1SUdaMWJtTjBhVzl1SUNodlltb3BJSHRjYmx4MFhIUmNkQ0FnYVdZZ0tIUjVjR1Z2WmlCdlltb2dJVDA5SUNkdlltcGxZM1FuSUNZbUlDaDBlWEJsYjJZZ2IySnFJQ0U5UFNBblpuVnVZM1JwYjI0bklIeDhJRzlpYWlBOVBUMGdiblZzYkNrcElIdGNibHgwWEhSY2RGeDBkR2h5YjNjZ2JtVjNJRlI1Y0dWRmNuSnZjaWduVDJKcVpXTjBMbXRsZVhNZ1kyRnNiR1ZrSUc5dUlHNXZiaTF2WW1wbFkzUW5LVHRjYmx4MFhIUmNkQ0FnZlZ4dVhHNWNkRngwWEhRZ0lIWmhjaUJ5WlhOMWJIUWdQU0JiWFN3Z2NISnZjQ3dnYVR0Y2JseHVYSFJjZEZ4MElDQm1iM0lnS0hCeWIzQWdhVzRnYjJKcUtTQjdYRzVjZEZ4MFhIUmNkR2xtSUNob1lYTlBkMjVRY205d1pYSjBlUzVqWVd4c0tHOWlhaXdnY0hKdmNDa3BJSHRjYmx4MFhIUmNkRngwSUNCeVpYTjFiSFF1Y0hWemFDaHdjbTl3S1R0Y2JseDBYSFJjZEZ4MGZWeHVYSFJjZEZ4MElDQjlYRzVjYmx4MFhIUmNkQ0FnYVdZZ0tHaGhjMFJ2Ym5SRmJuVnRRblZuS1NCN1hHNWNkRngwWEhSY2RHWnZjaUFvYVNBOUlEQTdJR2tnUENCa2IyNTBSVzUxYlhOTVpXNW5kR2c3SUdrckt5a2dlMXh1WEhSY2RGeDBYSFFnSUdsbUlDaG9ZWE5QZDI1UWNtOXdaWEowZVM1allXeHNLRzlpYWl3Z1pHOXVkRVZ1ZFcxelcybGRLU2tnZTF4dVhIUmNkRngwWEhSY2RISmxjM1ZzZEM1d2RYTm9LR1J2Ym5SRmJuVnRjMXRwWFNrN1hHNWNkRngwWEhSY2RDQWdmVnh1WEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFFnSUgxY2JseDBYSFJjZENBZ2NtVjBkWEp1SUhKbGMzVnNkRHRjYmx4MFhIUmNkSDA3WEc1Y2RGeDBJQ0I5S0NrcE8xeHVYSFJjZEgxY2JseHVYSFJjZEM4cUlGTmxZWEpqYUNBbUlFWnBiSFJsY2lCcVVYVmxjbmtnVUd4MVoybHVJQ292WEc1Y2RGeDBKQzVtYmk1elpXRnlZMmhCYm1SR2FXeDBaWElnUFNCd2JIVm5hVzQ3WEc1Y2JseDBYSFF2S2lCcGJtbDBJQ292WEc1Y2RGeDBKQ2hjSWk1elpXRnlZMmhoYm1SbWFXeDBaWEpjSWlrdWMyVmhjbU5vUVc1a1JtbHNkR1Z5S0NrN1hHNWNibHgwWEhRdktpQmxlSFJsY201aGJDQmpiMjUwY205c2N5QXFMMXh1WEhSY2RDUW9aRzlqZFcxbGJuUXBMbTl1S0Z3aVkyeHBZMnRjSWl3Z1hDSXVjMlZoY21Ob0xXWnBiSFJsY2kxeVpYTmxkRndpTENCbWRXNWpkR2x2YmlobEtYdGNibHh1WEhSY2RGeDBaUzV3Y21WMlpXNTBSR1ZtWVhWc2RDZ3BPMXh1WEc1Y2RGeDBYSFIyWVhJZ2MyVmhjbU5vUm05eWJVbEVJRDBnZEhsd1pXOW1LQ1FvZEdocGN5a3VZWFIwY2loY0ltUmhkR0V0YzJWaGNtTm9MV1p2Y20wdGFXUmNJaWtwSVQxY0luVnVaR1ZtYVc1bFpGd2lJRDhnSkNoMGFHbHpLUzVoZEhSeUtGd2laR0YwWVMxelpXRnlZMmd0Wm05eWJTMXBaRndpS1NBNklGd2lYQ0k3WEc1Y2RGeDBYSFIyWVhJZ2MzVmliV2wwUm05eWJTQTlJSFI1Y0dWdlppZ2tLSFJvYVhNcExtRjBkSElvWENKa1lYUmhMWE5tTFhOMVltMXBkQzFtYjNKdFhDSXBLU0U5WENKMWJtUmxabWx1WldSY0lpQS9JQ1FvZEdocGN5a3VZWFIwY2loY0ltUmhkR0V0YzJZdGMzVmliV2wwTFdadmNtMWNJaWtnT2lCY0lsd2lPMXh1WEc1Y2RGeDBYSFJ6ZEdGMFpTNW5aWFJUWldGeVkyaEdiM0p0S0hObFlYSmphRVp2Y20xSlJDa3VjbVZ6WlhRb2MzVmliV2wwUm05eWJTazdYRzVjYmx4MFhIUmNkQzh2ZG1GeUlDUnNhVzVyWldRZ1BTQWtLRndpSTNObFlYSmphQzFtYVd4MFpYSXRabTl5YlMxY0lpdHpaV0Z5WTJoR2IzSnRTVVFwTG5ObFlYSmphRVpwYkhSbGNrWnZjbTBvZTJGamRHbHZiam9nWENKeVpYTmxkRndpZlNrN1hHNWNibHgwWEhSY2RISmxkSFZ5YmlCbVlXeHpaVHRjYmx4dVhIUmNkSDBwTzF4dVhHNWNkSDBwTzF4dVhHNWNiaThxWEc0Z0tpQnFVWFZsY25rZ1JXRnphVzVuSUhZeExqUXVNU0F0SUdoMGRIQTZMeTluYzJka0xtTnZMblZyTDNOaGJtUmliM2d2YW5GMVpYSjVMMlZoYzJsdVp5OWNiaUFxSUU5d1pXNGdjMjkxY21ObElIVnVaR1Z5SUhSb1pTQkNVMFFnVEdsalpXNXpaUzVjYmlBcUlFTnZjSGx5YVdkb2RDRENxU0F5TURBNElFZGxiM0puWlNCTlkwZHBibXhsZVNCVGJXbDBhRnh1SUNvZ1FXeHNJSEpwWjJoMGN5QnlaWE5sY25abFpDNWNiaUFxSUdoMGRIQnpPaTh2Y21GM0xtZHBkR2gxWWk1amIyMHZaMlJ6YldsMGFDOXFjWFZsY25rdVpXRnphVzVuTDIxaGMzUmxjaTlNU1VORlRsTkZYRzRxTDF4dVhHNHZLaUJuYkc5aVlXeHpJR3BSZFdWeWVTd2daR1ZtYVc1bExDQnRiMlIxYkdVc0lISmxjWFZwY21VZ0tpOWNiaWhtZFc1amRHbHZiaUFvWm1GamRHOXllU2tnZTF4dVhIUnBaaUFvZEhsd1pXOW1JR1JsWm1sdVpTQTlQVDBnWENKbWRXNWpkR2x2Ymx3aUlDWW1JR1JsWm1sdVpTNWhiV1FwSUh0Y2JseDBYSFJrWldacGJtVW9XeWRxY1hWbGNua25YU3dnWm5WdVkzUnBiMjRnS0NRcElIdGNibHgwWEhSY2RISmxkSFZ5YmlCbVlXTjBiM0o1S0NRcE8xeHVYSFJjZEgwcE8xeHVYSFI5SUdWc2MyVWdhV1lnS0hSNWNHVnZaaUJ0YjJSMWJHVWdQVDA5SUZ3aWIySnFaV04wWENJZ0ppWWdkSGx3Wlc5bUlHMXZaSFZzWlM1bGVIQnZjblJ6SUQwOVBTQmNJbTlpYW1WamRGd2lLU0I3WEc1Y2RGeDBiVzlrZFd4bExtVjRjRzl5ZEhNZ1BTQm1ZV04wYjNKNUtDaDBlWEJsYjJZZ2QybHVaRzkzSUNFOVBTQmNJblZ1WkdWbWFXNWxaRndpSUQ4Z2QybHVaRzkzV3lkcVVYVmxjbmtuWFNBNklIUjVjR1Z2WmlCbmJHOWlZV3dnSVQwOUlGd2lkVzVrWldacGJtVmtYQ0lnUHlCbmJHOWlZV3hiSjJwUmRXVnllU2RkSURvZ2JuVnNiQ2twTzF4dVhIUjlJR1ZzYzJVZ2UxeHVYSFJjZEdaaFkzUnZjbmtvYWxGMVpYSjVLVHRjYmx4MGZWeHVmU2tvWm5WdVkzUnBiMjRvSkNsN1hHNWNibHgwTHk4Z1VISmxjMlZ5ZG1VZ2RHaGxJRzl5YVdkcGJtRnNJR3BSZFdWeWVTQmNJbk4zYVc1blhDSWdaV0Z6YVc1bklHRnpJRndpYW5OM2FXNW5YQ0pjYmx4MGFXWWdLSFI1Y0dWdlppQWtMbVZoYzJsdVp5QWhQVDBnSjNWdVpHVm1hVzVsWkNjcElIdGNibHgwWEhRa0xtVmhjMmx1WjFzbmFuTjNhVzVuSjEwZ1BTQWtMbVZoYzJsdVoxc25jM2RwYm1jblhUdGNibHgwZlZ4dVhHNWNkSFpoY2lCd2IzY2dQU0JOWVhSb0xuQnZkeXhjYmx4MFhIUnpjWEowSUQwZ1RXRjBhQzV6Y1hKMExGeHVYSFJjZEhOcGJpQTlJRTFoZEdndWMybHVMRnh1WEhSY2RHTnZjeUE5SUUxaGRHZ3VZMjl6TEZ4dVhIUmNkRkJKSUQwZ1RXRjBhQzVRU1N4Y2JseDBYSFJqTVNBOUlERXVOekF4TlRnc1hHNWNkRngwWXpJZ1BTQmpNU0FxSURFdU5USTFMRnh1WEhSY2RHTXpJRDBnWXpFZ0t5QXhMRnh1WEhSY2RHTTBJRDBnS0NBeUlDb2dVRWtnS1NBdklETXNYRzVjZEZ4MFl6VWdQU0FvSURJZ0tpQlFTU0FwSUM4Z05DNDFPMXh1WEc1Y2RDOHZJSGdnYVhNZ2RHaGxJR1p5WVdOMGFXOXVJRzltSUdGdWFXMWhkR2x2YmlCd2NtOW5jbVZ6Y3l3Z2FXNGdkR2hsSUhKaGJtZGxJREF1TGpGY2JseDBablZ1WTNScGIyNGdZbTkxYm1ObFQzVjBLSGdwSUh0Y2JseDBYSFIyWVhJZ2JqRWdQU0EzTGpVMk1qVXNYRzVjZEZ4MFhIUmtNU0E5SURJdU56VTdYRzVjZEZ4MGFXWWdLQ0I0SUR3Z01TOWtNU0FwSUh0Y2JseDBYSFJjZEhKbGRIVnliaUJ1TVNwNEtuZzdYRzVjZEZ4MGZTQmxiSE5sSUdsbUlDZ2dlQ0E4SURJdlpERWdLU0I3WEc1Y2RGeDBYSFJ5WlhSMWNtNGdiakVxS0hndFBTZ3hMalV2WkRFcEtTcDRJQ3NnTGpjMU8xeHVYSFJjZEgwZ1pXeHpaU0JwWmlBb0lIZ2dQQ0F5TGpVdlpERWdLU0I3WEc1Y2RGeDBYSFJ5WlhSMWNtNGdiakVxS0hndFBTZ3lMakkxTDJReEtTa3FlQ0FySUM0NU16YzFPMXh1WEhSY2RIMGdaV3h6WlNCN1hHNWNkRngwWEhSeVpYUjFjbTRnYmpFcUtIZ3RQU2d5TGpZeU5TOWtNU2twS25nZ0t5QXVPVGcwTXpjMU8xeHVYSFJjZEgxY2JseDBmVnh1WEc1Y2RDUXVaWGgwWlc1a0tDQWtMbVZoYzJsdVp5d2dlMXh1WEhSY2RHUmxaam9nSjJWaGMyVlBkWFJSZFdGa0p5eGNibHgwWEhSemQybHVaem9nWm5WdVkzUnBiMjRnS0hncElIdGNibHgwWEhSY2RISmxkSFZ5YmlBa0xtVmhjMmx1WjFza0xtVmhjMmx1Wnk1a1pXWmRLSGdwTzF4dVhIUmNkSDBzWEc1Y2RGeDBaV0Z6WlVsdVVYVmhaRG9nWm5WdVkzUnBiMjRnS0hncElIdGNibHgwWEhSY2RISmxkSFZ5YmlCNElDb2dlRHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZQZFhSUmRXRmtPaUJtZFc1amRHbHZiaUFvZUNrZ2UxeHVYSFJjZEZ4MGNtVjBkWEp1SURFZ0xTQW9JREVnTFNCNElDa2dLaUFvSURFZ0xTQjRJQ2s3WEc1Y2RGeDBmU3hjYmx4MFhIUmxZWE5sU1c1UGRYUlJkV0ZrT2lCbWRXNWpkR2x2YmlBb2VDa2dlMXh1WEhSY2RGeDBjbVYwZFhKdUlIZ2dQQ0F3TGpVZ1AxeHVYSFJjZEZ4MFhIUXlJQ29nZUNBcUlIZ2dPbHh1WEhSY2RGeDBYSFF4SUMwZ2NHOTNLQ0F0TWlBcUlIZ2dLeUF5TENBeUlDa2dMeUF5TzF4dVhIUmNkSDBzWEc1Y2RGeDBaV0Z6WlVsdVEzVmlhV002SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z2VDQXFJSGdnS2lCNE8xeHVYSFJjZEgwc1hHNWNkRngwWldGelpVOTFkRU4xWW1sak9pQm1kVzVqZEdsdmJpQW9lQ2tnZTF4dVhIUmNkRngwY21WMGRYSnVJREVnTFNCd2IzY29JREVnTFNCNExDQXpJQ2s3WEc1Y2RGeDBmU3hjYmx4MFhIUmxZWE5sU1c1UGRYUkRkV0pwWXpvZ1puVnVZM1JwYjI0Z0tIZ3BJSHRjYmx4MFhIUmNkSEpsZEhWeWJpQjRJRHdnTUM0MUlEOWNibHgwWEhSY2RGeDBOQ0FxSUhnZ0tpQjRJQ29nZUNBNlhHNWNkRngwWEhSY2RERWdMU0J3YjNjb0lDMHlJQ29nZUNBcklESXNJRE1nS1NBdklESTdYRzVjZEZ4MGZTeGNibHgwWEhSbFlYTmxTVzVSZFdGeWREb2dablZ1WTNScGIyNGdLSGdwSUh0Y2JseDBYSFJjZEhKbGRIVnliaUI0SUNvZ2VDQXFJSGdnS2lCNE8xeHVYSFJjZEgwc1hHNWNkRngwWldGelpVOTFkRkYxWVhKME9pQm1kVzVqZEdsdmJpQW9lQ2tnZTF4dVhIUmNkRngwY21WMGRYSnVJREVnTFNCd2IzY29JREVnTFNCNExDQTBJQ2s3WEc1Y2RGeDBmU3hjYmx4MFhIUmxZWE5sU1c1UGRYUlJkV0Z5ZERvZ1puVnVZM1JwYjI0Z0tIZ3BJSHRjYmx4MFhIUmNkSEpsZEhWeWJpQjRJRHdnTUM0MUlEOWNibHgwWEhSY2RGeDBPQ0FxSUhnZ0tpQjRJQ29nZUNBcUlIZ2dPbHh1WEhSY2RGeDBYSFF4SUMwZ2NHOTNLQ0F0TWlBcUlIZ2dLeUF5TENBMElDa2dMeUF5TzF4dVhIUmNkSDBzWEc1Y2RGeDBaV0Z6WlVsdVVYVnBiblE2SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z2VDQXFJSGdnS2lCNElDb2dlQ0FxSUhnN1hHNWNkRngwZlN4Y2JseDBYSFJsWVhObFQzVjBVWFZwYm5RNklHWjFibU4wYVc5dUlDaDRLU0I3WEc1Y2RGeDBYSFJ5WlhSMWNtNGdNU0F0SUhCdmR5Z2dNU0F0SUhnc0lEVWdLVHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZKYms5MWRGRjFhVzUwT2lCbWRXNWpkR2x2YmlBb2VDa2dlMXh1WEhSY2RGeDBjbVYwZFhKdUlIZ2dQQ0F3TGpVZ1AxeHVYSFJjZEZ4MFhIUXhOaUFxSUhnZ0tpQjRJQ29nZUNBcUlIZ2dLaUI0SURwY2JseDBYSFJjZEZ4ME1TQXRJSEJ2ZHlnZ0xUSWdLaUI0SUNzZ01pd2dOU0FwSUM4Z01qdGNibHgwWEhSOUxGeHVYSFJjZEdWaGMyVkpibE5wYm1VNklHWjFibU4wYVc5dUlDaDRLU0I3WEc1Y2RGeDBYSFJ5WlhSMWNtNGdNU0F0SUdOdmN5Z2dlQ0FxSUZCSkx6SWdLVHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZQZFhSVGFXNWxPaUJtZFc1amRHbHZiaUFvZUNrZ2UxeHVYSFJjZEZ4MGNtVjBkWEp1SUhOcGJpZ2dlQ0FxSUZCSkx6SWdLVHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZKYms5MWRGTnBibVU2SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z0xTZ2dZMjl6S0NCUVNTQXFJSGdnS1NBdElERWdLU0F2SURJN1hHNWNkRngwZlN4Y2JseDBYSFJsWVhObFNXNUZlSEJ2T2lCbWRXNWpkR2x2YmlBb2VDa2dlMXh1WEhSY2RGeDBjbVYwZFhKdUlIZ2dQVDA5SURBZ1B5QXdJRG9nY0c5M0tDQXlMQ0F4TUNBcUlIZ2dMU0F4TUNBcE8xeHVYSFJjZEgwc1hHNWNkRngwWldGelpVOTFkRVY0Y0c4NklHWjFibU4wYVc5dUlDaDRLU0I3WEc1Y2RGeDBYSFJ5WlhSMWNtNGdlQ0E5UFQwZ01TQS9JREVnT2lBeElDMGdjRzkzS0NBeUxDQXRNVEFnS2lCNElDazdYRzVjZEZ4MGZTeGNibHgwWEhSbFlYTmxTVzVQZFhSRmVIQnZPaUJtZFc1amRHbHZiaUFvZUNrZ2UxeHVYSFJjZEZ4MGNtVjBkWEp1SUhnZ1BUMDlJREFnUHlBd0lEb2dlQ0E5UFQwZ01TQS9JREVnT2lCNElEd2dNQzQxSUQ5Y2JseDBYSFJjZEZ4MGNHOTNLQ0F5TENBeU1DQXFJSGdnTFNBeE1DQXBJQzhnTWlBNlhHNWNkRngwWEhSY2RDZ2dNaUF0SUhCdmR5Z2dNaXdnTFRJd0lDb2dlQ0FySURFd0lDa2dLU0F2SURJN1hHNWNkRngwZlN4Y2JseDBYSFJsWVhObFNXNURhWEpqT2lCbWRXNWpkR2x2YmlBb2VDa2dlMXh1WEhSY2RGeDBjbVYwZFhKdUlERWdMU0J6Y1hKMEtDQXhJQzBnY0c5M0tDQjRMQ0F5SUNrZ0tUdGNibHgwWEhSOUxGeHVYSFJjZEdWaGMyVlBkWFJEYVhKak9pQm1kVzVqZEdsdmJpQW9lQ2tnZTF4dVhIUmNkRngwY21WMGRYSnVJSE54Y25Rb0lERWdMU0J3YjNjb0lIZ2dMU0F4TENBeUlDa2dLVHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZKYms5MWRFTnBjbU02SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z2VDQThJREF1TlNBL1hHNWNkRngwWEhSY2RDZ2dNU0F0SUhOeGNuUW9JREVnTFNCd2IzY29JRElnS2lCNExDQXlJQ2tnS1NBcElDOGdNaUE2WEc1Y2RGeDBYSFJjZENnZ2MzRnlkQ2dnTVNBdElIQnZkeWdnTFRJZ0tpQjRJQ3NnTWl3Z01pQXBJQ2tnS3lBeElDa2dMeUF5TzF4dVhIUmNkSDBzWEc1Y2RGeDBaV0Z6WlVsdVJXeGhjM1JwWXpvZ1puVnVZM1JwYjI0Z0tIZ3BJSHRjYmx4MFhIUmNkSEpsZEhWeWJpQjRJRDA5UFNBd0lEOGdNQ0E2SUhnZ1BUMDlJREVnUHlBeElEcGNibHgwWEhSY2RGeDBMWEJ2ZHlnZ01pd2dNVEFnS2lCNElDMGdNVEFnS1NBcUlITnBiaWdnS0NCNElDb2dNVEFnTFNBeE1DNDNOU0FwSUNvZ1l6UWdLVHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZQZFhSRmJHRnpkR2xqT2lCbWRXNWpkR2x2YmlBb2VDa2dlMXh1WEhSY2RGeDBjbVYwZFhKdUlIZ2dQVDA5SURBZ1B5QXdJRG9nZUNBOVBUMGdNU0EvSURFZ09seHVYSFJjZEZ4MFhIUndiM2NvSURJc0lDMHhNQ0FxSUhnZ0tTQXFJSE5wYmlnZ0tDQjRJQ29nTVRBZ0xTQXdMamMxSUNrZ0tpQmpOQ0FwSUNzZ01UdGNibHgwWEhSOUxGeHVYSFJjZEdWaGMyVkpiazkxZEVWc1lYTjBhV002SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z2VDQTlQVDBnTUNBL0lEQWdPaUI0SUQwOVBTQXhJRDhnTVNBNklIZ2dQQ0F3TGpVZ1AxeHVYSFJjZEZ4MFhIUXRLQ0J3YjNjb0lESXNJREl3SUNvZ2VDQXRJREV3SUNrZ0tpQnphVzRvSUNnZ01qQWdLaUI0SUMwZ01URXVNVEkxSUNrZ0tpQmpOU0FwS1NBdklESWdPbHh1WEhSY2RGeDBYSFJ3YjNjb0lESXNJQzB5TUNBcUlIZ2dLeUF4TUNBcElDb2djMmx1S0NBb0lESXdJQ29nZUNBdElERXhMakV5TlNBcElDb2dZelVnS1NBdklESWdLeUF4TzF4dVhIUmNkSDBzWEc1Y2RGeDBaV0Z6WlVsdVFtRmphem9nWm5WdVkzUnBiMjRnS0hncElIdGNibHgwWEhSY2RISmxkSFZ5YmlCak15QXFJSGdnS2lCNElDb2dlQ0F0SUdNeElDb2dlQ0FxSUhnN1hHNWNkRngwZlN4Y2JseDBYSFJsWVhObFQzVjBRbUZqYXpvZ1puVnVZM1JwYjI0Z0tIZ3BJSHRjYmx4MFhIUmNkSEpsZEhWeWJpQXhJQ3NnWXpNZ0tpQndiM2NvSUhnZ0xTQXhMQ0F6SUNrZ0t5QmpNU0FxSUhCdmR5Z2dlQ0F0SURFc0lESWdLVHRjYmx4MFhIUjlMRnh1WEhSY2RHVmhjMlZKYms5MWRFSmhZMnM2SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z2VDQThJREF1TlNBL1hHNWNkRngwWEhSY2RDZ2djRzkzS0NBeUlDb2dlQ3dnTWlBcElDb2dLQ0FvSUdNeUlDc2dNU0FwSUNvZ01pQXFJSGdnTFNCak1pQXBJQ2tnTHlBeUlEcGNibHgwWEhSY2RGeDBLQ0J3YjNjb0lESWdLaUI0SUMwZ01pd2dNaUFwSUNvb0lDZ2dZeklnS3lBeElDa2dLaUFvSUhnZ0tpQXlJQzBnTWlBcElDc2dZeklnS1NBcklESWdLU0F2SURJN1hHNWNkRngwZlN4Y2JseDBYSFJsWVhObFNXNUNiM1Z1WTJVNklHWjFibU4wYVc5dUlDaDRLU0I3WEc1Y2RGeDBYSFJ5WlhSMWNtNGdNU0F0SUdKdmRXNWpaVTkxZENnZ01TQXRJSGdnS1R0Y2JseDBYSFI5TEZ4dVhIUmNkR1ZoYzJWUGRYUkNiM1Z1WTJVNklHSnZkVzVqWlU5MWRDeGNibHgwWEhSbFlYTmxTVzVQZFhSQ2IzVnVZMlU2SUdaMWJtTjBhVzl1SUNoNEtTQjdYRzVjZEZ4MFhIUnlaWFIxY200Z2VDQThJREF1TlNBL1hHNWNkRngwWEhSY2RDZ2dNU0F0SUdKdmRXNWpaVTkxZENnZ01TQXRJRElnS2lCNElDa2dLU0F2SURJZ09seHVYSFJjZEZ4MFhIUW9JREVnS3lCaWIzVnVZMlZQZFhRb0lESWdLaUI0SUMwZ01TQXBJQ2tnTHlBeU8xeHVYSFJjZEgxY2JseDBmU2s3WEc1Y2RISmxkSFZ5YmlBa08xeHVmU2s3WEc1Y2JuMG9hbEYxWlhKNUtTazdYRzVjYmk4dmMyRm1ZWEpwSUdKaFkyc2dZblYwZEc5dUlHWnBlRnh1YWxGMVpYSjVLQ0IzYVc1a2IzY2dLUzV2YmlnZ1hDSndZV2RsYzJodmQxd2lMQ0JtZFc1amRHbHZiaWhsZG1WdWRDa2dlMXh1SUNBZ0lHbG1JQ2hsZG1WdWRDNXZjbWxuYVc1aGJFVjJaVzUwTG5CbGNuTnBjM1JsWkNrZ2UxeHVJQ0FnSUNBZ0lDQnFVWFZsY25rb1hDSXVjMlZoY21Ob1lXNWtabWxzZEdWeVhDSXBMbTltWmlncE8xeHVJQ0FnSUNBZ0lDQnFVWFZsY25rb1hDSXVjMlZoY21Ob1lXNWtabWxzZEdWeVhDSXBMbk5sWVhKamFFRnVaRVpwYkhSbGNpZ3BPMXh1SUNBZ0lIMWNibjBwTzF4dVhHNHZLaUIzY0c1MWJXSWdMU0J1YjNWcGMyeHBaR1Z5SUc1MWJXSmxjaUJtYjNKdFlYUjBhVzVuSUNvdlhHNGhablZ1WTNScGIyNG9LWHRjSW5WelpTQnpkSEpwWTNSY0lqdG1kVzVqZEdsdmJpQmxLR1VwZTNKbGRIVnliaUJsTG5Od2JHbDBLRndpWENJcExuSmxkbVZ5YzJVb0tTNXFiMmx1S0Z3aVhDSXBmV1oxYm1OMGFXOXVJRzRvWlN4dUtYdHlaWFIxY200Z1pTNXpkV0p6ZEhKcGJtY29NQ3h1TG14bGJtZDBhQ2s5UFQxdWZXWjFibU4wYVc5dUlISW9aU3h1S1h0eVpYUjFjbTRnWlM1emJHbGpaU2d0TVNwdUxteGxibWQwYUNrOVBUMXVmV1oxYm1OMGFXOXVJSFFvWlN4dUxISXBlMmxtS0NobFcyNWRmSHhsVzNKZEtTWW1aVnR1WFQwOVBXVmJjbDBwZEdoeWIzY2dibVYzSUVWeWNtOXlLRzRwZldaMWJtTjBhVzl1SUdrb1pTbDdjbVYwZFhKdVhDSnVkVzFpWlhKY0lqMDlkSGx3Wlc5bUlHVW1KbWx6Um1sdWFYUmxLR1VwZldaMWJtTjBhVzl1SUc4b1pTeHVLWHQyWVhJZ2NqMU5ZWFJvTG5CdmR5Z3hNQ3h1S1R0eVpYUjFjbTRvVFdGMGFDNXliM1Z1WkNobEtuSXBMM0lwTG5SdlJtbDRaV1FvYmlsOVpuVnVZM1JwYjI0Z2RTaHVMSElzZEN4MUxHWXNZU3hqTEhNc2NDeGtMR3dzYUNsN2RtRnlJR2NzZGl4M0xHMDlhQ3g0UFZ3aVhDSXNZajFjSWx3aU8zSmxkSFZ5YmlCaEppWW9hRDFoS0dncEtTeHBLR2dwUHlodUlUMDlJVEVtSmpBOVBUMXdZWEp6WlVac2IyRjBLR2d1ZEc5R2FYaGxaQ2h1S1NrbUppaG9QVEFwTERBK2FDWW1LR2M5SVRBc2FEMU5ZWFJvTG1GaWN5aG9LU2tzYmlFOVBTRXhKaVlvYUQxdktHZ3NiaWtwTEdnOWFDNTBiMU4wY21sdVp5Z3BMQzB4SVQwOWFDNXBibVJsZUU5bUtGd2lMbHdpS1Q4b2RqMW9Mbk53YkdsMEtGd2lMbHdpS1N4M1BYWmJNRjBzZENZbUtIZzlkQ3QyV3pGZEtTazZkejFvTEhJbUppaDNQV1VvZHlrdWJXRjBZMmdvTHk1N01Td3pmUzluS1N4M1BXVW9keTVxYjJsdUtHVW9jaWtwS1Nrc1p5WW1jeVltS0dJclBYTXBMSFVtSmloaUt6MTFLU3huSmlad0ppWW9ZaXM5Y0Nrc1lpczlkeXhpS3oxNExHWW1KaWhpS3oxbUtTeGtKaVlvWWoxa0tHSXNiU2twTEdJcE9pRXhmV1oxYm1OMGFXOXVJR1lvWlN4MExHOHNkU3htTEdFc1l5eHpMSEFzWkN4c0xHZ3BlM1poY2lCbkxIWTlYQ0pjSWp0eVpYUjFjbTRnYkNZbUtHZzliQ2hvS1Nrc2FDWW1YQ0p6ZEhKcGJtZGNJajA5ZEhsd1pXOW1JR2cvS0hNbUptNG9hQ3h6S1NZbUtHZzlhQzV5WlhCc1lXTmxLSE1zWENKY0lpa3NaejBoTUNrc2RTWW1iaWhvTEhVcEppWW9hRDFvTG5KbGNHeGhZMlVvZFN4Y0lsd2lLU2tzY0NZbWJpaG9MSEFwSmlZb2FEMW9MbkpsY0d4aFkyVW9jQ3hjSWx3aUtTeG5QU0V3S1N4bUppWnlLR2dzWmlrbUppaG9QV2d1YzJ4cFkyVW9NQ3d0TVNwbUxteGxibWQwYUNrcExIUW1KaWhvUFdndWMzQnNhWFFvZENrdWFtOXBiaWhjSWx3aUtTa3NieVltS0dnOWFDNXlaWEJzWVdObEtHOHNYQ0l1WENJcEtTeG5KaVlvZGlzOVhDSXRYQ0lwTEhZclBXZ3NkajEyTG5KbGNHeGhZMlVvTDF0ZU1DMDVYRnd1WEZ3dExsMHZaeXhjSWx3aUtTeGNJbHdpUFQwOWRqOGhNVG9vZGoxT2RXMWlaWElvZGlrc1l5WW1LSFk5WXloMktTa3NhU2gyS1Q5Mk9pRXhLU2s2SVRGOVpuVnVZM1JwYjI0Z1lTaGxLWHQyWVhJZ2JpeHlMR2tzYnoxN2ZUdG1iM0lvYmowd08yNDhjQzVzWlc1bmRHZzdiaXM5TVNscFppaHlQWEJiYmwwc2FUMWxXM0pkTEhadmFXUWdNRDA5UFdrcFhDSnVaV2RoZEdsMlpWd2lJVDA5Y254OGJ5NXVaV2RoZEdsMlpVSmxabTl5WlQ5Y0ltMWhjbXRjSWowOVBYSW1KbHdpTGx3aUlUMDlieTUwYUc5MWMyRnVaRDl2VzNKZFBWd2lMbHdpT205YmNsMDlJVEU2YjF0eVhUMWNJaTFjSWp0bGJITmxJR2xtS0Z3aVpHVmphVzFoYkhOY0lqMDlQWElwZTJsbUtDRW9hVDQ5TUNZbU9ENXBLU2wwYUhKdmR5QnVaWGNnUlhKeWIzSW9jaWs3YjF0eVhUMXBmV1ZzYzJVZ2FXWW9YQ0psYm1OdlpHVnlYQ0k5UFQxeWZIeGNJbVJsWTI5a1pYSmNJajA5UFhKOGZGd2laV1JwZEZ3aVBUMDljbng4WENKMWJtUnZYQ0k5UFQxeUtYdHBaaWhjSW1aMWJtTjBhVzl1WENJaFBYUjVjR1Z2WmlCcEtYUm9jbTkzSUc1bGR5QkZjbkp2Y2loeUtUdHZXM0pkUFdsOVpXeHpaWHRwWmloY0luTjBjbWx1WjF3aUlUMTBlWEJsYjJZZ2FTbDBhSEp2ZHlCdVpYY2dSWEp5YjNJb2NpazdiMXR5WFQxcGZYSmxkSFZ5YmlCMEtHOHNYQ0p0WVhKclhDSXNYQ0owYUc5MWMyRnVaRndpS1N4MEtHOHNYQ0p3Y21WbWFYaGNJaXhjSW01bFoyRjBhWFpsWENJcExIUW9ieXhjSW5CeVpXWnBlRndpTEZ3aWJtVm5ZWFJwZG1WQ1pXWnZjbVZjSWlrc2IzMW1kVzVqZEdsdmJpQmpLR1VzYml4eUtYdDJZWElnZEN4cFBWdGRPMlp2Y2loMFBUQTdkRHh3TG14bGJtZDBhRHQwS3oweEtXa3VjSFZ6YUNobFczQmJkRjFkS1R0eVpYUjFjbTRnYVM1d2RYTm9LSElwTEc0dVlYQndiSGtvWENKY0lpeHBLWDFtZFc1amRHbHZiaUJ6S0dVcGUzSmxkSFZ5YmlCMGFHbHpJR2x1YzNSaGJtTmxiMllnY3o5MmIybGtLRndpYjJKcVpXTjBYQ0k5UFhSNWNHVnZaaUJsSmlZb1pUMWhLR1VwTEhSb2FYTXVkRzg5Wm5WdVkzUnBiMjRvYmlsN2NtVjBkWEp1SUdNb1pTeDFMRzRwZlN4MGFHbHpMbVp5YjIwOVpuVnVZM1JwYjI0b2JpbDdjbVYwZFhKdUlHTW9aU3htTEc0cGZTa3BPbTVsZHlCektHVXBmWFpoY2lCd1BWdGNJbVJsWTJsdFlXeHpYQ0lzWENKMGFHOTFjMkZ1WkZ3aUxGd2liV0Z5YTF3aUxGd2ljSEpsWm1sNFhDSXNYQ0p3YjNOMFptbDRYQ0lzWENKbGJtTnZaR1Z5WENJc1hDSmtaV052WkdWeVhDSXNYQ0p1WldkaGRHbDJaVUpsWm05eVpWd2lMRndpYm1WbllYUnBkbVZjSWl4Y0ltVmthWFJjSWl4Y0luVnVaRzljSWwwN2QybHVaRzkzTG5kT2RXMWlQWE45S0NrN1hHNWNiaUpkZlE9PSIsIi8qISBub3Vpc2xpZGVyIC0gMTEuMS4wIC0gMjAxOC0wNC0wMiAxMToxODoxMyAqL1xyXG5cclxuKGZ1bmN0aW9uIChmYWN0b3J5KSB7XHJcblxyXG4gICAgaWYgKCB0eXBlb2YgZGVmaW5lID09PSAnZnVuY3Rpb24nICYmIGRlZmluZS5hbWQgKSB7XHJcblxyXG4gICAgICAgIC8vIEFNRC4gUmVnaXN0ZXIgYXMgYW4gYW5vbnltb3VzIG1vZHVsZS5cclxuICAgICAgICBkZWZpbmUoW10sIGZhY3RvcnkpO1xyXG5cclxuICAgIH0gZWxzZSBpZiAoIHR5cGVvZiBleHBvcnRzID09PSAnb2JqZWN0JyApIHtcclxuXHJcbiAgICAgICAgLy8gTm9kZS9Db21tb25KU1xyXG4gICAgICAgIG1vZHVsZS5leHBvcnRzID0gZmFjdG9yeSgpO1xyXG5cclxuICAgIH0gZWxzZSB7XHJcblxyXG4gICAgICAgIC8vIEJyb3dzZXIgZ2xvYmFsc1xyXG4gICAgICAgIHdpbmRvdy5ub1VpU2xpZGVyID0gZmFjdG9yeSgpO1xyXG4gICAgfVxyXG5cclxufShmdW5jdGlvbiggKXtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHR2YXIgVkVSU0lPTiA9ICcxMS4xLjAnO1xyXG5cclxuXG5cdGZ1bmN0aW9uIGlzVmFsaWRGb3JtYXR0ZXIgKCBlbnRyeSApIHtcblx0XHRyZXR1cm4gdHlwZW9mIGVudHJ5ID09PSAnb2JqZWN0JyAmJiB0eXBlb2YgZW50cnkudG8gPT09ICdmdW5jdGlvbicgJiYgdHlwZW9mIGVudHJ5LmZyb20gPT09ICdmdW5jdGlvbic7XG5cdH1cblxuXHRmdW5jdGlvbiByZW1vdmVFbGVtZW50ICggZWwgKSB7XG5cdFx0ZWwucGFyZW50RWxlbWVudC5yZW1vdmVDaGlsZChlbCk7XG5cdH1cblxuXHRmdW5jdGlvbiBpc1NldCAoIHZhbHVlICkge1xuXHRcdHJldHVybiB2YWx1ZSAhPT0gbnVsbCAmJiB2YWx1ZSAhPT0gdW5kZWZpbmVkO1xuXHR9XG5cblx0Ly8gQmluZGFibGUgdmVyc2lvblxuXHRmdW5jdGlvbiBwcmV2ZW50RGVmYXVsdCAoIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHR9XG5cblx0Ly8gUmVtb3ZlcyBkdXBsaWNhdGVzIGZyb20gYW4gYXJyYXkuXG5cdGZ1bmN0aW9uIHVuaXF1ZSAoIGFycmF5ICkge1xuXHRcdHJldHVybiBhcnJheS5maWx0ZXIoZnVuY3Rpb24oYSl7XG5cdFx0XHRyZXR1cm4gIXRoaXNbYV0gPyB0aGlzW2FdID0gdHJ1ZSA6IGZhbHNlO1xuXHRcdH0sIHt9KTtcblx0fVxuXG5cdC8vIFJvdW5kIGEgdmFsdWUgdG8gdGhlIGNsb3Nlc3QgJ3RvJy5cblx0ZnVuY3Rpb24gY2xvc2VzdCAoIHZhbHVlLCB0byApIHtcblx0XHRyZXR1cm4gTWF0aC5yb3VuZCh2YWx1ZSAvIHRvKSAqIHRvO1xuXHR9XG5cblx0Ly8gQ3VycmVudCBwb3NpdGlvbiBvZiBhbiBlbGVtZW50IHJlbGF0aXZlIHRvIHRoZSBkb2N1bWVudC5cblx0ZnVuY3Rpb24gb2Zmc2V0ICggZWxlbSwgb3JpZW50YXRpb24gKSB7XG5cblx0XHR2YXIgcmVjdCA9IGVsZW0uZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XG5cdFx0dmFyIGRvYyA9IGVsZW0ub3duZXJEb2N1bWVudDtcblx0XHR2YXIgZG9jRWxlbSA9IGRvYy5kb2N1bWVudEVsZW1lbnQ7XG5cdFx0dmFyIHBhZ2VPZmZzZXQgPSBnZXRQYWdlT2Zmc2V0KGRvYyk7XG5cblx0XHQvLyBnZXRCb3VuZGluZ0NsaWVudFJlY3QgY29udGFpbnMgbGVmdCBzY3JvbGwgaW4gQ2hyb21lIG9uIEFuZHJvaWQuXG5cdFx0Ly8gSSBoYXZlbid0IGZvdW5kIGEgZmVhdHVyZSBkZXRlY3Rpb24gdGhhdCBwcm92ZXMgdGhpcy4gV29yc3QgY2FzZVxuXHRcdC8vIHNjZW5hcmlvIG9uIG1pcy1tYXRjaDogdGhlICd0YXAnIGZlYXR1cmUgb24gaG9yaXpvbnRhbCBzbGlkZXJzIGJyZWFrcy5cblx0XHRpZiAoIC93ZWJraXQuKkNocm9tZS4qTW9iaWxlL2kudGVzdChuYXZpZ2F0b3IudXNlckFnZW50KSApIHtcblx0XHRcdHBhZ2VPZmZzZXQueCA9IDA7XG5cdFx0fVxuXG5cdFx0cmV0dXJuIG9yaWVudGF0aW9uID8gKHJlY3QudG9wICsgcGFnZU9mZnNldC55IC0gZG9jRWxlbS5jbGllbnRUb3ApIDogKHJlY3QubGVmdCArIHBhZ2VPZmZzZXQueCAtIGRvY0VsZW0uY2xpZW50TGVmdCk7XG5cdH1cblxuXHQvLyBDaGVja3Mgd2hldGhlciBhIHZhbHVlIGlzIG51bWVyaWNhbC5cblx0ZnVuY3Rpb24gaXNOdW1lcmljICggYSApIHtcblx0XHRyZXR1cm4gdHlwZW9mIGEgPT09ICdudW1iZXInICYmICFpc05hTiggYSApICYmIGlzRmluaXRlKCBhICk7XG5cdH1cblxuXHQvLyBTZXRzIGEgY2xhc3MgYW5kIHJlbW92ZXMgaXQgYWZ0ZXIgW2R1cmF0aW9uXSBtcy5cblx0ZnVuY3Rpb24gYWRkQ2xhc3NGb3IgKCBlbGVtZW50LCBjbGFzc05hbWUsIGR1cmF0aW9uICkge1xuXHRcdGlmIChkdXJhdGlvbiA+IDApIHtcblx0XHRhZGRDbGFzcyhlbGVtZW50LCBjbGFzc05hbWUpO1xuXHRcdFx0c2V0VGltZW91dChmdW5jdGlvbigpe1xuXHRcdFx0XHRyZW1vdmVDbGFzcyhlbGVtZW50LCBjbGFzc05hbWUpO1xuXHRcdFx0fSwgZHVyYXRpb24pO1xuXHRcdH1cblx0fVxuXG5cdC8vIExpbWl0cyBhIHZhbHVlIHRvIDAgLSAxMDBcblx0ZnVuY3Rpb24gbGltaXQgKCBhICkge1xuXHRcdHJldHVybiBNYXRoLm1heChNYXRoLm1pbihhLCAxMDApLCAwKTtcblx0fVxuXG5cdC8vIFdyYXBzIGEgdmFyaWFibGUgYXMgYW4gYXJyYXksIGlmIGl0IGlzbid0IG9uZSB5ZXQuXG5cdC8vIE5vdGUgdGhhdCBhbiBpbnB1dCBhcnJheSBpcyByZXR1cm5lZCBieSByZWZlcmVuY2UhXG5cdGZ1bmN0aW9uIGFzQXJyYXkgKCBhICkge1xuXHRcdHJldHVybiBBcnJheS5pc0FycmF5KGEpID8gYSA6IFthXTtcblx0fVxuXG5cdC8vIENvdW50cyBkZWNpbWFsc1xuXHRmdW5jdGlvbiBjb3VudERlY2ltYWxzICggbnVtU3RyICkge1xuXHRcdG51bVN0ciA9IFN0cmluZyhudW1TdHIpO1xuXHRcdHZhciBwaWVjZXMgPSBudW1TdHIuc3BsaXQoXCIuXCIpO1xuXHRcdHJldHVybiBwaWVjZXMubGVuZ3RoID4gMSA/IHBpZWNlc1sxXS5sZW5ndGggOiAwO1xuXHR9XG5cblx0Ly8gaHR0cDovL3lvdW1pZ2h0bm90bmVlZGpxdWVyeS5jb20vI2FkZF9jbGFzc1xuXHRmdW5jdGlvbiBhZGRDbGFzcyAoIGVsLCBjbGFzc05hbWUgKSB7XG5cdFx0aWYgKCBlbC5jbGFzc0xpc3QgKSB7XG5cdFx0XHRlbC5jbGFzc0xpc3QuYWRkKGNsYXNzTmFtZSk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGVsLmNsYXNzTmFtZSArPSAnICcgKyBjbGFzc05hbWU7XG5cdFx0fVxuXHR9XG5cblx0Ly8gaHR0cDovL3lvdW1pZ2h0bm90bmVlZGpxdWVyeS5jb20vI3JlbW92ZV9jbGFzc1xuXHRmdW5jdGlvbiByZW1vdmVDbGFzcyAoIGVsLCBjbGFzc05hbWUgKSB7XG5cdFx0aWYgKCBlbC5jbGFzc0xpc3QgKSB7XG5cdFx0XHRlbC5jbGFzc0xpc3QucmVtb3ZlKGNsYXNzTmFtZSk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGVsLmNsYXNzTmFtZSA9IGVsLmNsYXNzTmFtZS5yZXBsYWNlKG5ldyBSZWdFeHAoJyhefFxcXFxiKScgKyBjbGFzc05hbWUuc3BsaXQoJyAnKS5qb2luKCd8JykgKyAnKFxcXFxifCQpJywgJ2dpJyksICcgJyk7XG5cdFx0fVxuXHR9XG5cblx0Ly8gaHR0cHM6Ly9wbGFpbmpzLmNvbS9qYXZhc2NyaXB0L2F0dHJpYnV0ZXMvYWRkaW5nLXJlbW92aW5nLWFuZC10ZXN0aW5nLWZvci1jbGFzc2VzLTkvXG5cdGZ1bmN0aW9uIGhhc0NsYXNzICggZWwsIGNsYXNzTmFtZSApIHtcblx0XHRyZXR1cm4gZWwuY2xhc3NMaXN0ID8gZWwuY2xhc3NMaXN0LmNvbnRhaW5zKGNsYXNzTmFtZSkgOiBuZXcgUmVnRXhwKCdcXFxcYicgKyBjbGFzc05hbWUgKyAnXFxcXGInKS50ZXN0KGVsLmNsYXNzTmFtZSk7XG5cdH1cblxuXHQvLyBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9BUEkvV2luZG93L3Njcm9sbFkjTm90ZXNcblx0ZnVuY3Rpb24gZ2V0UGFnZU9mZnNldCAoIGRvYyApIHtcblxuXHRcdHZhciBzdXBwb3J0UGFnZU9mZnNldCA9IHdpbmRvdy5wYWdlWE9mZnNldCAhPT0gdW5kZWZpbmVkO1xuXHRcdHZhciBpc0NTUzFDb21wYXQgPSAoKGRvYy5jb21wYXRNb2RlIHx8IFwiXCIpID09PSBcIkNTUzFDb21wYXRcIik7XG5cdFx0dmFyIHggPSBzdXBwb3J0UGFnZU9mZnNldCA/IHdpbmRvdy5wYWdlWE9mZnNldCA6IGlzQ1NTMUNvbXBhdCA/IGRvYy5kb2N1bWVudEVsZW1lbnQuc2Nyb2xsTGVmdCA6IGRvYy5ib2R5LnNjcm9sbExlZnQ7XG5cdFx0dmFyIHkgPSBzdXBwb3J0UGFnZU9mZnNldCA/IHdpbmRvdy5wYWdlWU9mZnNldCA6IGlzQ1NTMUNvbXBhdCA/IGRvYy5kb2N1bWVudEVsZW1lbnQuc2Nyb2xsVG9wIDogZG9jLmJvZHkuc2Nyb2xsVG9wO1xuXG5cdFx0cmV0dXJuIHtcblx0XHRcdHg6IHgsXG5cdFx0XHR5OiB5XG5cdFx0fTtcblx0fVxuXHJcblx0Ly8gd2UgcHJvdmlkZSBhIGZ1bmN0aW9uIHRvIGNvbXB1dGUgY29uc3RhbnRzIGluc3RlYWRcclxuXHQvLyBvZiBhY2Nlc3Npbmcgd2luZG93LiogYXMgc29vbiBhcyB0aGUgbW9kdWxlIG5lZWRzIGl0XHJcblx0Ly8gc28gdGhhdCB3ZSBkbyBub3QgY29tcHV0ZSBhbnl0aGluZyBpZiBub3QgbmVlZGVkXHJcblx0ZnVuY3Rpb24gZ2V0QWN0aW9ucyAoICkge1xyXG5cclxuXHRcdC8vIERldGVybWluZSB0aGUgZXZlbnRzIHRvIGJpbmQuIElFMTEgaW1wbGVtZW50cyBwb2ludGVyRXZlbnRzIHdpdGhvdXRcclxuXHRcdC8vIGEgcHJlZml4LCB3aGljaCBicmVha3MgY29tcGF0aWJpbGl0eSB3aXRoIHRoZSBJRTEwIGltcGxlbWVudGF0aW9uLlxyXG5cdFx0cmV0dXJuIHdpbmRvdy5uYXZpZ2F0b3IucG9pbnRlckVuYWJsZWQgPyB7XHJcblx0XHRcdHN0YXJ0OiAncG9pbnRlcmRvd24nLFxyXG5cdFx0XHRtb3ZlOiAncG9pbnRlcm1vdmUnLFxyXG5cdFx0XHRlbmQ6ICdwb2ludGVydXAnXHJcblx0XHR9IDogd2luZG93Lm5hdmlnYXRvci5tc1BvaW50ZXJFbmFibGVkID8ge1xyXG5cdFx0XHRzdGFydDogJ01TUG9pbnRlckRvd24nLFxyXG5cdFx0XHRtb3ZlOiAnTVNQb2ludGVyTW92ZScsXHJcblx0XHRcdGVuZDogJ01TUG9pbnRlclVwJ1xyXG5cdFx0fSA6IHtcclxuXHRcdFx0c3RhcnQ6ICdtb3VzZWRvd24gdG91Y2hzdGFydCcsXHJcblx0XHRcdG1vdmU6ICdtb3VzZW1vdmUgdG91Y2htb3ZlJyxcclxuXHRcdFx0ZW5kOiAnbW91c2V1cCB0b3VjaGVuZCdcclxuXHRcdH07XHJcblx0fVxyXG5cclxuXHQvLyBodHRwczovL2dpdGh1Yi5jb20vV0lDRy9FdmVudExpc3RlbmVyT3B0aW9ucy9ibG9iL2doLXBhZ2VzL2V4cGxhaW5lci5tZFxyXG5cdC8vIElzc3VlICM3ODVcclxuXHRmdW5jdGlvbiBnZXRTdXBwb3J0c1Bhc3NpdmUgKCApIHtcclxuXHJcblx0XHR2YXIgc3VwcG9ydHNQYXNzaXZlID0gZmFsc2U7XHJcblxyXG5cdFx0dHJ5IHtcclxuXHJcblx0XHRcdHZhciBvcHRzID0gT2JqZWN0LmRlZmluZVByb3BlcnR5KHt9LCAncGFzc2l2ZScsIHtcclxuXHRcdFx0XHRnZXQ6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0c3VwcG9ydHNQYXNzaXZlID0gdHJ1ZTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cclxuXHRcdFx0d2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ3Rlc3QnLCBudWxsLCBvcHRzKTtcclxuXHJcblx0XHR9IGNhdGNoIChlKSB7fVxyXG5cclxuXHRcdHJldHVybiBzdXBwb3J0c1Bhc3NpdmU7XHJcblx0fVxyXG5cclxuXHRmdW5jdGlvbiBnZXRTdXBwb3J0c1RvdWNoQWN0aW9uTm9uZSAoICkge1xyXG5cdFx0cmV0dXJuIHdpbmRvdy5DU1MgJiYgQ1NTLnN1cHBvcnRzICYmIENTUy5zdXBwb3J0cygndG91Y2gtYWN0aW9uJywgJ25vbmUnKTtcclxuXHR9XHJcblxyXG5cclxuLy8gVmFsdWUgY2FsY3VsYXRpb25cclxuXHJcblx0Ly8gRGV0ZXJtaW5lIHRoZSBzaXplIG9mIGEgc3ViLXJhbmdlIGluIHJlbGF0aW9uIHRvIGEgZnVsbCByYW5nZS5cclxuXHRmdW5jdGlvbiBzdWJSYW5nZVJhdGlvICggcGEsIHBiICkge1xyXG5cdFx0cmV0dXJuICgxMDAgLyAocGIgLSBwYSkpO1xyXG5cdH1cclxuXHJcblx0Ly8gKHBlcmNlbnRhZ2UpIEhvdyBtYW55IHBlcmNlbnQgaXMgdGhpcyB2YWx1ZSBvZiB0aGlzIHJhbmdlP1xyXG5cdGZ1bmN0aW9uIGZyb21QZXJjZW50YWdlICggcmFuZ2UsIHZhbHVlICkge1xyXG5cdFx0cmV0dXJuICh2YWx1ZSAqIDEwMCkgLyAoIHJhbmdlWzFdIC0gcmFuZ2VbMF0gKTtcclxuXHR9XHJcblxyXG5cdC8vIChwZXJjZW50YWdlKSBXaGVyZSBpcyB0aGlzIHZhbHVlIG9uIHRoaXMgcmFuZ2U/XHJcblx0ZnVuY3Rpb24gdG9QZXJjZW50YWdlICggcmFuZ2UsIHZhbHVlICkge1xyXG5cdFx0cmV0dXJuIGZyb21QZXJjZW50YWdlKCByYW5nZSwgcmFuZ2VbMF0gPCAwID9cclxuXHRcdFx0dmFsdWUgKyBNYXRoLmFicyhyYW5nZVswXSkgOlxyXG5cdFx0XHRcdHZhbHVlIC0gcmFuZ2VbMF0gKTtcclxuXHR9XHJcblxyXG5cdC8vICh2YWx1ZSkgSG93IG11Y2ggaXMgdGhpcyBwZXJjZW50YWdlIG9uIHRoaXMgcmFuZ2U/XHJcblx0ZnVuY3Rpb24gaXNQZXJjZW50YWdlICggcmFuZ2UsIHZhbHVlICkge1xyXG5cdFx0cmV0dXJuICgodmFsdWUgKiAoIHJhbmdlWzFdIC0gcmFuZ2VbMF0gKSkgLyAxMDApICsgcmFuZ2VbMF07XHJcblx0fVxyXG5cclxuXHJcbi8vIFJhbmdlIGNvbnZlcnNpb25cclxuXHJcblx0ZnVuY3Rpb24gZ2V0SiAoIHZhbHVlLCBhcnIgKSB7XHJcblxyXG5cdFx0dmFyIGogPSAxO1xyXG5cclxuXHRcdHdoaWxlICggdmFsdWUgPj0gYXJyW2pdICl7XHJcblx0XHRcdGogKz0gMTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gajtcclxuXHR9XHJcblxyXG5cdC8vIChwZXJjZW50YWdlKSBJbnB1dCBhIHZhbHVlLCBmaW5kIHdoZXJlLCBvbiBhIHNjYWxlIG9mIDAtMTAwLCBpdCBhcHBsaWVzLlxyXG5cdGZ1bmN0aW9uIHRvU3RlcHBpbmcgKCB4VmFsLCB4UGN0LCB2YWx1ZSApIHtcclxuXHJcblx0XHRpZiAoIHZhbHVlID49IHhWYWwuc2xpY2UoLTEpWzBdICl7XHJcblx0XHRcdHJldHVybiAxMDA7XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIGogPSBnZXRKKCB2YWx1ZSwgeFZhbCApO1xyXG5cdFx0dmFyIHZhID0geFZhbFtqLTFdO1xyXG5cdFx0dmFyIHZiID0geFZhbFtqXTtcclxuXHRcdHZhciBwYSA9IHhQY3Rbai0xXTtcclxuXHRcdHZhciBwYiA9IHhQY3Rbal07XHJcblxyXG5cdFx0cmV0dXJuIHBhICsgKHRvUGVyY2VudGFnZShbdmEsIHZiXSwgdmFsdWUpIC8gc3ViUmFuZ2VSYXRpbyAocGEsIHBiKSk7XHJcblx0fVxyXG5cclxuXHQvLyAodmFsdWUpIElucHV0IGEgcGVyY2VudGFnZSwgZmluZCB3aGVyZSBpdCBpcyBvbiB0aGUgc3BlY2lmaWVkIHJhbmdlLlxyXG5cdGZ1bmN0aW9uIGZyb21TdGVwcGluZyAoIHhWYWwsIHhQY3QsIHZhbHVlICkge1xyXG5cclxuXHRcdC8vIFRoZXJlIGlzIG5vIHJhbmdlIGdyb3VwIHRoYXQgZml0cyAxMDBcclxuXHRcdGlmICggdmFsdWUgPj0gMTAwICl7XHJcblx0XHRcdHJldHVybiB4VmFsLnNsaWNlKC0xKVswXTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgaiA9IGdldEooIHZhbHVlLCB4UGN0ICk7XHJcblx0XHR2YXIgdmEgPSB4VmFsW2otMV07XHJcblx0XHR2YXIgdmIgPSB4VmFsW2pdO1xyXG5cdFx0dmFyIHBhID0geFBjdFtqLTFdO1xyXG5cdFx0dmFyIHBiID0geFBjdFtqXTtcclxuXHJcblx0XHRyZXR1cm4gaXNQZXJjZW50YWdlKFt2YSwgdmJdLCAodmFsdWUgLSBwYSkgKiBzdWJSYW5nZVJhdGlvIChwYSwgcGIpKTtcclxuXHR9XHJcblxyXG5cdC8vIChwZXJjZW50YWdlKSBHZXQgdGhlIHN0ZXAgdGhhdCBhcHBsaWVzIGF0IGEgY2VydGFpbiB2YWx1ZS5cclxuXHRmdW5jdGlvbiBnZXRTdGVwICggeFBjdCwgeFN0ZXBzLCBzbmFwLCB2YWx1ZSApIHtcclxuXHJcblx0XHRpZiAoIHZhbHVlID09PSAxMDAgKSB7XHJcblx0XHRcdHJldHVybiB2YWx1ZTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgaiA9IGdldEooIHZhbHVlLCB4UGN0ICk7XHJcblx0XHR2YXIgYSA9IHhQY3Rbai0xXTtcclxuXHRcdHZhciBiID0geFBjdFtqXTtcclxuXHJcblx0XHQvLyBJZiAnc25hcCcgaXMgc2V0LCBzdGVwcyBhcmUgdXNlZCBhcyBmaXhlZCBwb2ludHMgb24gdGhlIHNsaWRlci5cclxuXHRcdGlmICggc25hcCApIHtcclxuXHJcblx0XHRcdC8vIEZpbmQgdGhlIGNsb3Nlc3QgcG9zaXRpb24sIGEgb3IgYi5cclxuXHRcdFx0aWYgKCh2YWx1ZSAtIGEpID4gKChiLWEpLzIpKXtcclxuXHRcdFx0XHRyZXR1cm4gYjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0cmV0dXJuIGE7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCAheFN0ZXBzW2otMV0gKXtcclxuXHRcdFx0cmV0dXJuIHZhbHVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiB4UGN0W2otMV0gKyBjbG9zZXN0KFxyXG5cdFx0XHR2YWx1ZSAtIHhQY3Rbai0xXSxcclxuXHRcdFx0eFN0ZXBzW2otMV1cclxuXHRcdCk7XHJcblx0fVxyXG5cclxuXHJcbi8vIEVudHJ5IHBhcnNpbmdcclxuXHJcblx0ZnVuY3Rpb24gaGFuZGxlRW50cnlQb2ludCAoIGluZGV4LCB2YWx1ZSwgdGhhdCApIHtcclxuXHJcblx0XHR2YXIgcGVyY2VudGFnZTtcclxuXHJcblx0XHQvLyBXcmFwIG51bWVyaWNhbCBpbnB1dCBpbiBhbiBhcnJheS5cclxuXHRcdGlmICggdHlwZW9mIHZhbHVlID09PSBcIm51bWJlclwiICkge1xyXG5cdFx0XHR2YWx1ZSA9IFt2YWx1ZV07XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gUmVqZWN0IGFueSBpbnZhbGlkIGlucHV0LCBieSB0ZXN0aW5nIHdoZXRoZXIgdmFsdWUgaXMgYW4gYXJyYXkuXHJcblx0XHRpZiAoICFBcnJheS5pc0FycmF5KHZhbHVlKSApe1xyXG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdyYW5nZScgY29udGFpbnMgaW52YWxpZCB2YWx1ZS5cIik7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gQ292ZXJ0IG1pbi9tYXggc3ludGF4IHRvIDAgYW5kIDEwMC5cclxuXHRcdGlmICggaW5kZXggPT09ICdtaW4nICkge1xyXG5cdFx0XHRwZXJjZW50YWdlID0gMDtcclxuXHRcdH0gZWxzZSBpZiAoIGluZGV4ID09PSAnbWF4JyApIHtcclxuXHRcdFx0cGVyY2VudGFnZSA9IDEwMDtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdHBlcmNlbnRhZ2UgPSBwYXJzZUZsb2F0KCBpbmRleCApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIENoZWNrIGZvciBjb3JyZWN0IGlucHV0LlxyXG5cdFx0aWYgKCAhaXNOdW1lcmljKCBwZXJjZW50YWdlICkgfHwgIWlzTnVtZXJpYyggdmFsdWVbMF0gKSApIHtcclxuXHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiAncmFuZ2UnIHZhbHVlIGlzbid0IG51bWVyaWMuXCIpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFN0b3JlIHZhbHVlcy5cclxuXHRcdHRoYXQueFBjdC5wdXNoKCBwZXJjZW50YWdlICk7XHJcblx0XHR0aGF0LnhWYWwucHVzaCggdmFsdWVbMF0gKTtcclxuXHJcblx0XHQvLyBOYU4gd2lsbCBldmFsdWF0ZSB0byBmYWxzZSB0b28sIGJ1dCB0byBrZWVwXHJcblx0XHQvLyBsb2dnaW5nIGNsZWFyLCBzZXQgc3RlcCBleHBsaWNpdGx5LiBNYWtlIHN1cmVcclxuXHRcdC8vIG5vdCB0byBvdmVycmlkZSB0aGUgJ3N0ZXAnIHNldHRpbmcgd2l0aCBmYWxzZS5cclxuXHRcdGlmICggIXBlcmNlbnRhZ2UgKSB7XHJcblx0XHRcdGlmICggIWlzTmFOKCB2YWx1ZVsxXSApICkge1xyXG5cdFx0XHRcdHRoYXQueFN0ZXBzWzBdID0gdmFsdWVbMV07XHJcblx0XHRcdH1cclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdHRoYXQueFN0ZXBzLnB1c2goIGlzTmFOKHZhbHVlWzFdKSA/IGZhbHNlIDogdmFsdWVbMV0gKTtcclxuXHRcdH1cclxuXHJcblx0XHR0aGF0LnhIaWdoZXN0Q29tcGxldGVTdGVwLnB1c2goMCk7XHJcblx0fVxyXG5cclxuXHRmdW5jdGlvbiBoYW5kbGVTdGVwUG9pbnQgKCBpLCBuLCB0aGF0ICkge1xyXG5cclxuXHRcdC8vIElnbm9yZSAnZmFsc2UnIHN0ZXBwaW5nLlxyXG5cdFx0aWYgKCAhbiApIHtcclxuXHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gRmFjdG9yIHRvIHJhbmdlIHJhdGlvXHJcblx0XHR0aGF0LnhTdGVwc1tpXSA9IGZyb21QZXJjZW50YWdlKFt0aGF0LnhWYWxbaV0sIHRoYXQueFZhbFtpKzFdXSwgbikgLyBzdWJSYW5nZVJhdGlvKHRoYXQueFBjdFtpXSwgdGhhdC54UGN0W2krMV0pO1xyXG5cclxuXHRcdHZhciB0b3RhbFN0ZXBzID0gKHRoYXQueFZhbFtpKzFdIC0gdGhhdC54VmFsW2ldKSAvIHRoYXQueE51bVN0ZXBzW2ldO1xyXG5cdFx0dmFyIGhpZ2hlc3RTdGVwID0gTWF0aC5jZWlsKE51bWJlcih0b3RhbFN0ZXBzLnRvRml4ZWQoMykpIC0gMSk7XHJcblx0XHR2YXIgc3RlcCA9IHRoYXQueFZhbFtpXSArICh0aGF0LnhOdW1TdGVwc1tpXSAqIGhpZ2hlc3RTdGVwKTtcclxuXHJcblx0XHR0aGF0LnhIaWdoZXN0Q29tcGxldGVTdGVwW2ldID0gc3RlcDtcclxuXHR9XHJcblxyXG5cclxuLy8gSW50ZXJmYWNlXHJcblxyXG5cdGZ1bmN0aW9uIFNwZWN0cnVtICggZW50cnksIHNuYXAsIHNpbmdsZVN0ZXAgKSB7XHJcblxyXG5cdFx0dGhpcy54UGN0ID0gW107XHJcblx0XHR0aGlzLnhWYWwgPSBbXTtcclxuXHRcdHRoaXMueFN0ZXBzID0gWyBzaW5nbGVTdGVwIHx8IGZhbHNlIF07XHJcblx0XHR0aGlzLnhOdW1TdGVwcyA9IFsgZmFsc2UgXTtcclxuXHRcdHRoaXMueEhpZ2hlc3RDb21wbGV0ZVN0ZXAgPSBbXTtcclxuXHJcblx0XHR0aGlzLnNuYXAgPSBzbmFwO1xyXG5cclxuXHRcdHZhciBpbmRleDtcclxuXHRcdHZhciBvcmRlcmVkID0gW107IC8vIFswLCAnbWluJ10sIFsxLCAnNTAlJ10sIFsyLCAnbWF4J11cclxuXHJcblx0XHQvLyBNYXAgdGhlIG9iamVjdCBrZXlzIHRvIGFuIGFycmF5LlxyXG5cdFx0Zm9yICggaW5kZXggaW4gZW50cnkgKSB7XHJcblx0XHRcdGlmICggZW50cnkuaGFzT3duUHJvcGVydHkoaW5kZXgpICkge1xyXG5cdFx0XHRcdG9yZGVyZWQucHVzaChbZW50cnlbaW5kZXhdLCBpbmRleF0pO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gU29ydCBhbGwgZW50cmllcyBieSB2YWx1ZSAobnVtZXJpYyBzb3J0KS5cclxuXHRcdGlmICggb3JkZXJlZC5sZW5ndGggJiYgdHlwZW9mIG9yZGVyZWRbMF1bMF0gPT09IFwib2JqZWN0XCIgKSB7XHJcblx0XHRcdG9yZGVyZWQuc29ydChmdW5jdGlvbihhLCBiKSB7IHJldHVybiBhWzBdWzBdIC0gYlswXVswXTsgfSk7XHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHRvcmRlcmVkLnNvcnQoZnVuY3Rpb24oYSwgYikgeyByZXR1cm4gYVswXSAtIGJbMF07IH0pO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvLyBDb252ZXJ0IGFsbCBlbnRyaWVzIHRvIHN1YnJhbmdlcy5cclxuXHRcdGZvciAoIGluZGV4ID0gMDsgaW5kZXggPCBvcmRlcmVkLmxlbmd0aDsgaW5kZXgrKyApIHtcclxuXHRcdFx0aGFuZGxlRW50cnlQb2ludChvcmRlcmVkW2luZGV4XVsxXSwgb3JkZXJlZFtpbmRleF1bMF0sIHRoaXMpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFN0b3JlIHRoZSBhY3R1YWwgc3RlcCB2YWx1ZXMuXHJcblx0XHQvLyB4U3RlcHMgaXMgc29ydGVkIGluIHRoZSBzYW1lIG9yZGVyIGFzIHhQY3QgYW5kIHhWYWwuXHJcblx0XHR0aGlzLnhOdW1TdGVwcyA9IHRoaXMueFN0ZXBzLnNsaWNlKDApO1xyXG5cclxuXHRcdC8vIENvbnZlcnQgYWxsIG51bWVyaWMgc3RlcHMgdG8gdGhlIHBlcmNlbnRhZ2Ugb2YgdGhlIHN1YnJhbmdlIHRoZXkgcmVwcmVzZW50LlxyXG5cdFx0Zm9yICggaW5kZXggPSAwOyBpbmRleCA8IHRoaXMueE51bVN0ZXBzLmxlbmd0aDsgaW5kZXgrKyApIHtcclxuXHRcdFx0aGFuZGxlU3RlcFBvaW50KGluZGV4LCB0aGlzLnhOdW1TdGVwc1tpbmRleF0sIHRoaXMpO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0U3BlY3RydW0ucHJvdG90eXBlLmdldE1hcmdpbiA9IGZ1bmN0aW9uICggdmFsdWUgKSB7XHJcblxyXG5cdFx0dmFyIHN0ZXAgPSB0aGlzLnhOdW1TdGVwc1swXTtcclxuXHJcblx0XHRpZiAoIHN0ZXAgJiYgKCh2YWx1ZSAvIHN0ZXApICUgMSkgIT09IDAgKSB7XHJcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ2xpbWl0JywgJ21hcmdpbicgYW5kICdwYWRkaW5nJyBtdXN0IGJlIGRpdmlzaWJsZSBieSBzdGVwLlwiKTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gdGhpcy54UGN0Lmxlbmd0aCA9PT0gMiA/IGZyb21QZXJjZW50YWdlKHRoaXMueFZhbCwgdmFsdWUpIDogZmFsc2U7XHJcblx0fTtcclxuXHJcblx0U3BlY3RydW0ucHJvdG90eXBlLnRvU3RlcHBpbmcgPSBmdW5jdGlvbiAoIHZhbHVlICkge1xyXG5cclxuXHRcdHZhbHVlID0gdG9TdGVwcGluZyggdGhpcy54VmFsLCB0aGlzLnhQY3QsIHZhbHVlICk7XHJcblxyXG5cdFx0cmV0dXJuIHZhbHVlO1xyXG5cdH07XHJcblxyXG5cdFNwZWN0cnVtLnByb3RvdHlwZS5mcm9tU3RlcHBpbmcgPSBmdW5jdGlvbiAoIHZhbHVlICkge1xyXG5cclxuXHRcdHJldHVybiBmcm9tU3RlcHBpbmcoIHRoaXMueFZhbCwgdGhpcy54UGN0LCB2YWx1ZSApO1xyXG5cdH07XHJcblxyXG5cdFNwZWN0cnVtLnByb3RvdHlwZS5nZXRTdGVwID0gZnVuY3Rpb24gKCB2YWx1ZSApIHtcclxuXHJcblx0XHR2YWx1ZSA9IGdldFN0ZXAodGhpcy54UGN0LCB0aGlzLnhTdGVwcywgdGhpcy5zbmFwLCB2YWx1ZSApO1xyXG5cclxuXHRcdHJldHVybiB2YWx1ZTtcclxuXHR9O1xyXG5cclxuXHRTcGVjdHJ1bS5wcm90b3R5cGUuZ2V0TmVhcmJ5U3RlcHMgPSBmdW5jdGlvbiAoIHZhbHVlICkge1xyXG5cclxuXHRcdHZhciBqID0gZ2V0Sih2YWx1ZSwgdGhpcy54UGN0KTtcclxuXHJcblx0XHRyZXR1cm4ge1xyXG5cdFx0XHRzdGVwQmVmb3JlOiB7IHN0YXJ0VmFsdWU6IHRoaXMueFZhbFtqLTJdLCBzdGVwOiB0aGlzLnhOdW1TdGVwc1tqLTJdLCBoaWdoZXN0U3RlcDogdGhpcy54SGlnaGVzdENvbXBsZXRlU3RlcFtqLTJdIH0sXHJcblx0XHRcdHRoaXNTdGVwOiB7IHN0YXJ0VmFsdWU6IHRoaXMueFZhbFtqLTFdLCBzdGVwOiB0aGlzLnhOdW1TdGVwc1tqLTFdLCBoaWdoZXN0U3RlcDogdGhpcy54SGlnaGVzdENvbXBsZXRlU3RlcFtqLTFdIH0sXHJcblx0XHRcdHN0ZXBBZnRlcjogeyBzdGFydFZhbHVlOiB0aGlzLnhWYWxbai0wXSwgc3RlcDogdGhpcy54TnVtU3RlcHNbai0wXSwgaGlnaGVzdFN0ZXA6IHRoaXMueEhpZ2hlc3RDb21wbGV0ZVN0ZXBbai0wXSB9XHJcblx0XHR9O1xyXG5cdH07XHJcblxyXG5cdFNwZWN0cnVtLnByb3RvdHlwZS5jb3VudFN0ZXBEZWNpbWFscyA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdHZhciBzdGVwRGVjaW1hbHMgPSB0aGlzLnhOdW1TdGVwcy5tYXAoY291bnREZWNpbWFscyk7XHJcblx0XHRyZXR1cm4gTWF0aC5tYXguYXBwbHkobnVsbCwgc3RlcERlY2ltYWxzKTtcclxuXHR9O1xyXG5cclxuXHQvLyBPdXRzaWRlIHRlc3RpbmdcclxuXHRTcGVjdHJ1bS5wcm90b3R5cGUuY29udmVydCA9IGZ1bmN0aW9uICggdmFsdWUgKSB7XHJcblx0XHRyZXR1cm4gdGhpcy5nZXRTdGVwKHRoaXMudG9TdGVwcGluZyh2YWx1ZSkpO1xyXG5cdH07XHJcblxyXG4vKlx0RXZlcnkgaW5wdXQgb3B0aW9uIGlzIHRlc3RlZCBhbmQgcGFyc2VkLiBUaGlzJ2xsIHByZXZlbnRcblx0ZW5kbGVzcyB2YWxpZGF0aW9uIGluIGludGVybmFsIG1ldGhvZHMuIFRoZXNlIHRlc3RzIGFyZVxuXHRzdHJ1Y3R1cmVkIHdpdGggYW4gaXRlbSBmb3IgZXZlcnkgb3B0aW9uIGF2YWlsYWJsZS4gQW5cblx0b3B0aW9uIGNhbiBiZSBtYXJrZWQgYXMgcmVxdWlyZWQgYnkgc2V0dGluZyB0aGUgJ3InIGZsYWcuXG5cdFRoZSB0ZXN0aW5nIGZ1bmN0aW9uIGlzIHByb3ZpZGVkIHdpdGggdGhyZWUgYXJndW1lbnRzOlxuXHRcdC0gVGhlIHByb3ZpZGVkIHZhbHVlIGZvciB0aGUgb3B0aW9uO1xuXHRcdC0gQSByZWZlcmVuY2UgdG8gdGhlIG9wdGlvbnMgb2JqZWN0O1xuXHRcdC0gVGhlIG5hbWUgZm9yIHRoZSBvcHRpb247XG5cblx0VGhlIHRlc3RpbmcgZnVuY3Rpb24gcmV0dXJucyBmYWxzZSB3aGVuIGFuIGVycm9yIGlzIGRldGVjdGVkLFxuXHRvciB0cnVlIHdoZW4gZXZlcnl0aGluZyBpcyBPSy4gSXQgY2FuIGFsc28gbW9kaWZ5IHRoZSBvcHRpb25cblx0b2JqZWN0LCB0byBtYWtlIHN1cmUgYWxsIHZhbHVlcyBjYW4gYmUgY29ycmVjdGx5IGxvb3BlZCBlbHNld2hlcmUuICovXG5cblx0dmFyIGRlZmF1bHRGb3JtYXR0ZXIgPSB7ICd0byc6IGZ1bmN0aW9uKCB2YWx1ZSApe1xuXHRcdHJldHVybiB2YWx1ZSAhPT0gdW5kZWZpbmVkICYmIHZhbHVlLnRvRml4ZWQoMik7XG5cdH0sICdmcm9tJzogTnVtYmVyIH07XG5cblx0ZnVuY3Rpb24gdmFsaWRhdGVGb3JtYXQgKCBlbnRyeSApIHtcblxuXHRcdC8vIEFueSBvYmplY3Qgd2l0aCBhIHRvIGFuZCBmcm9tIG1ldGhvZCBpcyBzdXBwb3J0ZWQuXG5cdFx0aWYgKCBpc1ZhbGlkRm9ybWF0dGVyKGVudHJ5KSApIHtcblx0XHRcdHJldHVybiB0cnVlO1xuXHRcdH1cblxuXHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ2Zvcm1hdCcgcmVxdWlyZXMgJ3RvJyBhbmQgJ2Zyb20nIG1ldGhvZHMuXCIpO1xuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdFN0ZXAgKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0aWYgKCAhaXNOdW1lcmljKCBlbnRyeSApICkge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiAnc3RlcCcgaXMgbm90IG51bWVyaWMuXCIpO1xuXHRcdH1cblxuXHRcdC8vIFRoZSBzdGVwIG9wdGlvbiBjYW4gc3RpbGwgYmUgdXNlZCB0byBzZXQgc3RlcHBpbmdcblx0XHQvLyBmb3IgbGluZWFyIHNsaWRlcnMuIE92ZXJ3cml0dGVuIGlmIHNldCBpbiAncmFuZ2UnLlxuXHRcdHBhcnNlZC5zaW5nbGVTdGVwID0gZW50cnk7XG5cdH1cblxuXHRmdW5jdGlvbiB0ZXN0UmFuZ2UgKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0Ly8gRmlsdGVyIGluY29ycmVjdCBpbnB1dC5cblx0XHRpZiAoIHR5cGVvZiBlbnRyeSAhPT0gJ29iamVjdCcgfHwgQXJyYXkuaXNBcnJheShlbnRyeSkgKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdyYW5nZScgaXMgbm90IGFuIG9iamVjdC5cIik7XG5cdFx0fVxuXG5cdFx0Ly8gQ2F0Y2ggbWlzc2luZyBzdGFydCBvciBlbmQuXG5cdFx0aWYgKCBlbnRyeS5taW4gPT09IHVuZGVmaW5lZCB8fCBlbnRyeS5tYXggPT09IHVuZGVmaW5lZCApIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogTWlzc2luZyAnbWluJyBvciAnbWF4JyBpbiAncmFuZ2UnLlwiKTtcblx0XHR9XG5cblx0XHQvLyBDYXRjaCBlcXVhbCBzdGFydCBvciBlbmQuXG5cdFx0aWYgKCBlbnRyeS5taW4gPT09IGVudHJ5Lm1heCApIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ3JhbmdlJyAnbWluJyBhbmQgJ21heCcgY2Fubm90IGJlIGVxdWFsLlwiKTtcblx0XHR9XG5cblx0XHRwYXJzZWQuc3BlY3RydW0gPSBuZXcgU3BlY3RydW0oZW50cnksIHBhcnNlZC5zbmFwLCBwYXJzZWQuc2luZ2xlU3RlcCk7XG5cdH1cblxuXHRmdW5jdGlvbiB0ZXN0U3RhcnQgKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0ZW50cnkgPSBhc0FycmF5KGVudHJ5KTtcblxuXHRcdC8vIFZhbGlkYXRlIGlucHV0LiBWYWx1ZXMgYXJlbid0IHRlc3RlZCwgYXMgdGhlIHB1YmxpYyAudmFsIG1ldGhvZFxuXHRcdC8vIHdpbGwgYWx3YXlzIHByb3ZpZGUgYSB2YWxpZCBsb2NhdGlvbi5cblx0XHRpZiAoICFBcnJheS5pc0FycmF5KCBlbnRyeSApIHx8ICFlbnRyeS5sZW5ndGggKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdzdGFydCcgb3B0aW9uIGlzIGluY29ycmVjdC5cIik7XG5cdFx0fVxuXG5cdFx0Ly8gU3RvcmUgdGhlIG51bWJlciBvZiBoYW5kbGVzLlxuXHRcdHBhcnNlZC5oYW5kbGVzID0gZW50cnkubGVuZ3RoO1xuXG5cdFx0Ly8gV2hlbiB0aGUgc2xpZGVyIGlzIGluaXRpYWxpemVkLCB0aGUgLnZhbCBtZXRob2Qgd2lsbFxuXHRcdC8vIGJlIGNhbGxlZCB3aXRoIHRoZSBzdGFydCBvcHRpb25zLlxuXHRcdHBhcnNlZC5zdGFydCA9IGVudHJ5O1xuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdFNuYXAgKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0Ly8gRW5mb3JjZSAxMDAlIHN0ZXBwaW5nIHdpdGhpbiBzdWJyYW5nZXMuXG5cdFx0cGFyc2VkLnNuYXAgPSBlbnRyeTtcblxuXHRcdGlmICggdHlwZW9mIGVudHJ5ICE9PSAnYm9vbGVhbicgKXtcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ3NuYXAnIG9wdGlvbiBtdXN0IGJlIGEgYm9vbGVhbi5cIik7XG5cdFx0fVxuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdEFuaW1hdGUgKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0Ly8gRW5mb3JjZSAxMDAlIHN0ZXBwaW5nIHdpdGhpbiBzdWJyYW5nZXMuXG5cdFx0cGFyc2VkLmFuaW1hdGUgPSBlbnRyeTtcblxuXHRcdGlmICggdHlwZW9mIGVudHJ5ICE9PSAnYm9vbGVhbicgKXtcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ2FuaW1hdGUnIG9wdGlvbiBtdXN0IGJlIGEgYm9vbGVhbi5cIik7XG5cdFx0fVxuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdEFuaW1hdGlvbkR1cmF0aW9uICggcGFyc2VkLCBlbnRyeSApIHtcblxuXHRcdHBhcnNlZC5hbmltYXRpb25EdXJhdGlvbiA9IGVudHJ5O1xuXG5cdFx0aWYgKCB0eXBlb2YgZW50cnkgIT09ICdudW1iZXInICl7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdhbmltYXRpb25EdXJhdGlvbicgb3B0aW9uIG11c3QgYmUgYSBudW1iZXIuXCIpO1xuXHRcdH1cblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3RDb25uZWN0ICggcGFyc2VkLCBlbnRyeSApIHtcblxuXHRcdHZhciBjb25uZWN0ID0gW2ZhbHNlXTtcblx0XHR2YXIgaTtcblxuXHRcdC8vIE1hcCBsZWdhY3kgb3B0aW9uc1xuXHRcdGlmICggZW50cnkgPT09ICdsb3dlcicgKSB7XG5cdFx0XHRlbnRyeSA9IFt0cnVlLCBmYWxzZV07XG5cdFx0fVxuXG5cdFx0ZWxzZSBpZiAoIGVudHJ5ID09PSAndXBwZXInICkge1xuXHRcdFx0ZW50cnkgPSBbZmFsc2UsIHRydWVdO1xuXHRcdH1cblxuXHRcdC8vIEhhbmRsZSBib29sZWFuIG9wdGlvbnNcblx0XHRpZiAoIGVudHJ5ID09PSB0cnVlIHx8IGVudHJ5ID09PSBmYWxzZSApIHtcblxuXHRcdFx0Zm9yICggaSA9IDE7IGkgPCBwYXJzZWQuaGFuZGxlczsgaSsrICkge1xuXHRcdFx0XHRjb25uZWN0LnB1c2goZW50cnkpO1xuXHRcdFx0fVxuXG5cdFx0XHRjb25uZWN0LnB1c2goZmFsc2UpO1xuXHRcdH1cblxuXHRcdC8vIFJlamVjdCBpbnZhbGlkIGlucHV0XG5cdFx0ZWxzZSBpZiAoICFBcnJheS5pc0FycmF5KCBlbnRyeSApIHx8ICFlbnRyeS5sZW5ndGggfHwgZW50cnkubGVuZ3RoICE9PSBwYXJzZWQuaGFuZGxlcyArIDEgKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdjb25uZWN0JyBvcHRpb24gZG9lc24ndCBtYXRjaCBoYW5kbGUgY291bnQuXCIpO1xuXHRcdH1cblxuXHRcdGVsc2Uge1xuXHRcdFx0Y29ubmVjdCA9IGVudHJ5O1xuXHRcdH1cblxuXHRcdHBhcnNlZC5jb25uZWN0ID0gY29ubmVjdDtcblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3RPcmllbnRhdGlvbiAoIHBhcnNlZCwgZW50cnkgKSB7XG5cblx0XHQvLyBTZXQgb3JpZW50YXRpb24gdG8gYW4gYSBudW1lcmljYWwgdmFsdWUgZm9yIGVhc3lcblx0XHQvLyBhcnJheSBzZWxlY3Rpb24uXG5cdFx0c3dpdGNoICggZW50cnkgKXtcblx0XHRcdGNhc2UgJ2hvcml6b250YWwnOlxuXHRcdFx0XHRwYXJzZWQub3J0ID0gMDtcblx0XHRcdFx0YnJlYWs7XG5cdFx0XHRjYXNlICd2ZXJ0aWNhbCc6XG5cdFx0XHRcdHBhcnNlZC5vcnQgPSAxO1xuXHRcdFx0XHRicmVhaztcblx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ29yaWVudGF0aW9uJyBvcHRpb24gaXMgaW52YWxpZC5cIik7XG5cdFx0fVxuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdE1hcmdpbiAoIHBhcnNlZCwgZW50cnkgKSB7XG5cblx0XHRpZiAoICFpc051bWVyaWMoZW50cnkpICl7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdtYXJnaW4nIG9wdGlvbiBtdXN0IGJlIG51bWVyaWMuXCIpO1xuXHRcdH1cblxuXHRcdC8vIElzc3VlICM1ODJcblx0XHRpZiAoIGVudHJ5ID09PSAwICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdHBhcnNlZC5tYXJnaW4gPSBwYXJzZWQuc3BlY3RydW0uZ2V0TWFyZ2luKGVudHJ5KTtcblxuXHRcdGlmICggIXBhcnNlZC5tYXJnaW4gKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdtYXJnaW4nIG9wdGlvbiBpcyBvbmx5IHN1cHBvcnRlZCBvbiBsaW5lYXIgc2xpZGVycy5cIik7XG5cdFx0fVxuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdExpbWl0ICggcGFyc2VkLCBlbnRyeSApIHtcblxuXHRcdGlmICggIWlzTnVtZXJpYyhlbnRyeSkgKXtcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ2xpbWl0JyBvcHRpb24gbXVzdCBiZSBudW1lcmljLlwiKTtcblx0XHR9XG5cblx0XHRwYXJzZWQubGltaXQgPSBwYXJzZWQuc3BlY3RydW0uZ2V0TWFyZ2luKGVudHJ5KTtcblxuXHRcdGlmICggIXBhcnNlZC5saW1pdCB8fCBwYXJzZWQuaGFuZGxlcyA8IDIgKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdsaW1pdCcgb3B0aW9uIGlzIG9ubHkgc3VwcG9ydGVkIG9uIGxpbmVhciBzbGlkZXJzIHdpdGggMiBvciBtb3JlIGhhbmRsZXMuXCIpO1xuXHRcdH1cblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3RQYWRkaW5nICggcGFyc2VkLCBlbnRyeSApIHtcblxuXHRcdGlmICggIWlzTnVtZXJpYyhlbnRyeSkgJiYgIUFycmF5LmlzQXJyYXkoZW50cnkpICl7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdwYWRkaW5nJyBvcHRpb24gbXVzdCBiZSBudW1lcmljIG9yIGFycmF5IG9mIGV4YWN0bHkgMiBudW1iZXJzLlwiKTtcblx0XHR9XG5cblx0XHRpZiAoIEFycmF5LmlzQXJyYXkoZW50cnkpICYmICEoZW50cnkubGVuZ3RoID09PSAyIHx8IGlzTnVtZXJpYyhlbnRyeVswXSkgfHwgaXNOdW1lcmljKGVudHJ5WzFdKSkgKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdwYWRkaW5nJyBvcHRpb24gbXVzdCBiZSBudW1lcmljIG9yIGFycmF5IG9mIGV4YWN0bHkgMiBudW1iZXJzLlwiKTtcblx0XHR9XG5cblx0XHRpZiAoIGVudHJ5ID09PSAwICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdGlmICggIUFycmF5LmlzQXJyYXkoZW50cnkpICkge1xuXHRcdFx0ZW50cnkgPSBbZW50cnksIGVudHJ5XTtcblx0XHR9XG5cblx0XHQvLyAnZ2V0TWFyZ2luJyByZXR1cm5zIGZhbHNlIGZvciBpbnZhbGlkIHZhbHVlcy5cblx0XHRwYXJzZWQucGFkZGluZyA9IFtwYXJzZWQuc3BlY3RydW0uZ2V0TWFyZ2luKGVudHJ5WzBdKSwgcGFyc2VkLnNwZWN0cnVtLmdldE1hcmdpbihlbnRyeVsxXSldO1xuXG5cdFx0aWYgKCBwYXJzZWQucGFkZGluZ1swXSA9PT0gZmFsc2UgfHwgcGFyc2VkLnBhZGRpbmdbMV0gPT09IGZhbHNlICkge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiAncGFkZGluZycgb3B0aW9uIGlzIG9ubHkgc3VwcG9ydGVkIG9uIGxpbmVhciBzbGlkZXJzLlwiKTtcblx0XHR9XG5cblx0XHRpZiAoIHBhcnNlZC5wYWRkaW5nWzBdIDwgMCB8fCBwYXJzZWQucGFkZGluZ1sxXSA8IDAgKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdwYWRkaW5nJyBvcHRpb24gbXVzdCBiZSBhIHBvc2l0aXZlIG51bWJlcihzKS5cIik7XG5cdFx0fVxuXG5cdFx0aWYgKCBwYXJzZWQucGFkZGluZ1swXSArIHBhcnNlZC5wYWRkaW5nWzFdID49IDEwMCApIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ3BhZGRpbmcnIG9wdGlvbiBtdXN0IG5vdCBleGNlZWQgMTAwJSBvZiB0aGUgcmFuZ2UuXCIpO1xuXHRcdH1cblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3REaXJlY3Rpb24gKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0Ly8gU2V0IGRpcmVjdGlvbiBhcyBhIG51bWVyaWNhbCB2YWx1ZSBmb3IgZWFzeSBwYXJzaW5nLlxuXHRcdC8vIEludmVydCBjb25uZWN0aW9uIGZvciBSVEwgc2xpZGVycywgc28gdGhhdCB0aGUgcHJvcGVyXG5cdFx0Ly8gaGFuZGxlcyBnZXQgdGhlIGNvbm5lY3QvYmFja2dyb3VuZCBjbGFzc2VzLlxuXHRcdHN3aXRjaCAoIGVudHJ5ICkge1xuXHRcdFx0Y2FzZSAnbHRyJzpcblx0XHRcdFx0cGFyc2VkLmRpciA9IDA7XG5cdFx0XHRcdGJyZWFrO1xuXHRcdFx0Y2FzZSAncnRsJzpcblx0XHRcdFx0cGFyc2VkLmRpciA9IDE7XG5cdFx0XHRcdGJyZWFrO1xuXHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiAnZGlyZWN0aW9uJyBvcHRpb24gd2FzIG5vdCByZWNvZ25pemVkLlwiKTtcblx0XHR9XG5cdH1cblxuXHRmdW5jdGlvbiB0ZXN0QmVoYXZpb3VyICggcGFyc2VkLCBlbnRyeSApIHtcblxuXHRcdC8vIE1ha2Ugc3VyZSB0aGUgaW5wdXQgaXMgYSBzdHJpbmcuXG5cdFx0aWYgKCB0eXBlb2YgZW50cnkgIT09ICdzdHJpbmcnICkge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiAnYmVoYXZpb3VyJyBtdXN0IGJlIGEgc3RyaW5nIGNvbnRhaW5pbmcgb3B0aW9ucy5cIik7XG5cdFx0fVxuXG5cdFx0Ly8gQ2hlY2sgaWYgdGhlIHN0cmluZyBjb250YWlucyBhbnkga2V5d29yZHMuXG5cdFx0Ly8gTm9uZSBhcmUgcmVxdWlyZWQuXG5cdFx0dmFyIHRhcCA9IGVudHJ5LmluZGV4T2YoJ3RhcCcpID49IDA7XG5cdFx0dmFyIGRyYWcgPSBlbnRyeS5pbmRleE9mKCdkcmFnJykgPj0gMDtcblx0XHR2YXIgZml4ZWQgPSBlbnRyeS5pbmRleE9mKCdmaXhlZCcpID49IDA7XG5cdFx0dmFyIHNuYXAgPSBlbnRyeS5pbmRleE9mKCdzbmFwJykgPj0gMDtcblx0XHR2YXIgaG92ZXIgPSBlbnRyeS5pbmRleE9mKCdob3ZlcicpID49IDA7XG5cblx0XHRpZiAoIGZpeGVkICkge1xuXG5cdFx0XHRpZiAoIHBhcnNlZC5oYW5kbGVzICE9PSAyICkge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdmaXhlZCcgYmVoYXZpb3VyIG11c3QgYmUgdXNlZCB3aXRoIDIgaGFuZGxlc1wiKTtcblx0XHRcdH1cblxuXHRcdFx0Ly8gVXNlIG1hcmdpbiB0byBlbmZvcmNlIGZpeGVkIHN0YXRlXG5cdFx0XHR0ZXN0TWFyZ2luKHBhcnNlZCwgcGFyc2VkLnN0YXJ0WzFdIC0gcGFyc2VkLnN0YXJ0WzBdKTtcblx0XHR9XG5cblx0XHRwYXJzZWQuZXZlbnRzID0ge1xuXHRcdFx0dGFwOiB0YXAgfHwgc25hcCxcblx0XHRcdGRyYWc6IGRyYWcsXG5cdFx0XHRmaXhlZDogZml4ZWQsXG5cdFx0XHRzbmFwOiBzbmFwLFxuXHRcdFx0aG92ZXI6IGhvdmVyXG5cdFx0fTtcblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3RUb29sdGlwcyAoIHBhcnNlZCwgZW50cnkgKSB7XG5cblx0XHRpZiAoIGVudHJ5ID09PSBmYWxzZSApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHRlbHNlIGlmICggZW50cnkgPT09IHRydWUgKSB7XG5cblx0XHRcdHBhcnNlZC50b29sdGlwcyA9IFtdO1xuXG5cdFx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBwYXJzZWQuaGFuZGxlczsgaSsrICkge1xuXHRcdFx0XHRwYXJzZWQudG9vbHRpcHMucHVzaCh0cnVlKTtcblx0XHRcdH1cblx0XHR9XG5cblx0XHRlbHNlIHtcblxuXHRcdFx0cGFyc2VkLnRvb2x0aXBzID0gYXNBcnJheShlbnRyeSk7XG5cblx0XHRcdGlmICggcGFyc2VkLnRvb2x0aXBzLmxlbmd0aCAhPT0gcGFyc2VkLmhhbmRsZXMgKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogbXVzdCBwYXNzIGEgZm9ybWF0dGVyIGZvciBhbGwgaGFuZGxlcy5cIik7XG5cdFx0XHR9XG5cblx0XHRcdHBhcnNlZC50b29sdGlwcy5mb3JFYWNoKGZ1bmN0aW9uKGZvcm1hdHRlcil7XG5cdFx0XHRcdGlmICggdHlwZW9mIGZvcm1hdHRlciAhPT0gJ2Jvb2xlYW4nICYmICh0eXBlb2YgZm9ybWF0dGVyICE9PSAnb2JqZWN0JyB8fCB0eXBlb2YgZm9ybWF0dGVyLnRvICE9PSAnZnVuY3Rpb24nKSApIHtcblx0XHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICd0b29sdGlwcycgbXVzdCBiZSBwYXNzZWQgYSBmb3JtYXR0ZXIgb3IgJ2ZhbHNlJy5cIik7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH1cblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3RBcmlhRm9ybWF0ICggcGFyc2VkLCBlbnRyeSApIHtcblx0XHRwYXJzZWQuYXJpYUZvcm1hdCA9IGVudHJ5O1xuXHRcdHZhbGlkYXRlRm9ybWF0KGVudHJ5KTtcblx0fVxuXG5cdGZ1bmN0aW9uIHRlc3RGb3JtYXQgKCBwYXJzZWQsIGVudHJ5ICkge1xuXHRcdHBhcnNlZC5mb3JtYXQgPSBlbnRyeTtcblx0XHR2YWxpZGF0ZUZvcm1hdChlbnRyeSk7XG5cdH1cblxuXHRmdW5jdGlvbiB0ZXN0Q3NzUHJlZml4ICggcGFyc2VkLCBlbnRyeSApIHtcblxuXHRcdGlmICggdHlwZW9mIGVudHJ5ICE9PSAnc3RyaW5nJyAmJiBlbnRyeSAhPT0gZmFsc2UgKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6ICdjc3NQcmVmaXgnIG11c3QgYmUgYSBzdHJpbmcgb3IgYGZhbHNlYC5cIik7XG5cdFx0fVxuXG5cdFx0cGFyc2VkLmNzc1ByZWZpeCA9IGVudHJ5O1xuXHR9XG5cblx0ZnVuY3Rpb24gdGVzdENzc0NsYXNzZXMgKCBwYXJzZWQsIGVudHJ5ICkge1xuXG5cdFx0aWYgKCB0eXBlb2YgZW50cnkgIT09ICdvYmplY3QnICkge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiAnY3NzQ2xhc3NlcycgbXVzdCBiZSBhbiBvYmplY3QuXCIpO1xuXHRcdH1cblxuXHRcdGlmICggdHlwZW9mIHBhcnNlZC5jc3NQcmVmaXggPT09ICdzdHJpbmcnICkge1xuXHRcdFx0cGFyc2VkLmNzc0NsYXNzZXMgPSB7fTtcblxuXHRcdFx0Zm9yICggdmFyIGtleSBpbiBlbnRyeSApIHtcblx0XHRcdFx0aWYgKCAhZW50cnkuaGFzT3duUHJvcGVydHkoa2V5KSApIHsgY29udGludWU7IH1cblxuXHRcdFx0XHRwYXJzZWQuY3NzQ2xhc3Nlc1trZXldID0gcGFyc2VkLmNzc1ByZWZpeCArIGVudHJ5W2tleV07XG5cdFx0XHR9XG5cdFx0fSBlbHNlIHtcblx0XHRcdHBhcnNlZC5jc3NDbGFzc2VzID0gZW50cnk7XG5cdFx0fVxuXHR9XG5cblx0Ly8gVGVzdCBhbGwgZGV2ZWxvcGVyIHNldHRpbmdzIGFuZCBwYXJzZSB0byBhc3N1bXB0aW9uLXNhZmUgdmFsdWVzLlxuXHRmdW5jdGlvbiB0ZXN0T3B0aW9ucyAoIG9wdGlvbnMgKSB7XG5cblx0XHQvLyBUbyBwcm92ZSBhIGZpeCBmb3IgIzUzNywgZnJlZXplIG9wdGlvbnMgaGVyZS5cblx0XHQvLyBJZiB0aGUgb2JqZWN0IGlzIG1vZGlmaWVkLCBhbiBlcnJvciB3aWxsIGJlIHRocm93bi5cblx0XHQvLyBPYmplY3QuZnJlZXplKG9wdGlvbnMpO1xuXG5cdFx0dmFyIHBhcnNlZCA9IHtcblx0XHRcdG1hcmdpbjogMCxcblx0XHRcdGxpbWl0OiAwLFxuXHRcdFx0cGFkZGluZzogMCxcblx0XHRcdGFuaW1hdGU6IHRydWUsXG5cdFx0XHRhbmltYXRpb25EdXJhdGlvbjogMzAwLFxuXHRcdFx0YXJpYUZvcm1hdDogZGVmYXVsdEZvcm1hdHRlcixcblx0XHRcdGZvcm1hdDogZGVmYXVsdEZvcm1hdHRlclxuXHRcdH07XG5cblx0XHQvLyBUZXN0cyBhcmUgZXhlY3V0ZWQgaW4gdGhlIG9yZGVyIHRoZXkgYXJlIHByZXNlbnRlZCBoZXJlLlxuXHRcdHZhciB0ZXN0cyA9IHtcblx0XHRcdCdzdGVwJzogeyByOiBmYWxzZSwgdDogdGVzdFN0ZXAgfSxcblx0XHRcdCdzdGFydCc6IHsgcjogdHJ1ZSwgdDogdGVzdFN0YXJ0IH0sXG5cdFx0XHQnY29ubmVjdCc6IHsgcjogdHJ1ZSwgdDogdGVzdENvbm5lY3QgfSxcblx0XHRcdCdkaXJlY3Rpb24nOiB7IHI6IHRydWUsIHQ6IHRlc3REaXJlY3Rpb24gfSxcblx0XHRcdCdzbmFwJzogeyByOiBmYWxzZSwgdDogdGVzdFNuYXAgfSxcblx0XHRcdCdhbmltYXRlJzogeyByOiBmYWxzZSwgdDogdGVzdEFuaW1hdGUgfSxcblx0XHRcdCdhbmltYXRpb25EdXJhdGlvbic6IHsgcjogZmFsc2UsIHQ6IHRlc3RBbmltYXRpb25EdXJhdGlvbiB9LFxuXHRcdFx0J3JhbmdlJzogeyByOiB0cnVlLCB0OiB0ZXN0UmFuZ2UgfSxcblx0XHRcdCdvcmllbnRhdGlvbic6IHsgcjogZmFsc2UsIHQ6IHRlc3RPcmllbnRhdGlvbiB9LFxuXHRcdFx0J21hcmdpbic6IHsgcjogZmFsc2UsIHQ6IHRlc3RNYXJnaW4gfSxcblx0XHRcdCdsaW1pdCc6IHsgcjogZmFsc2UsIHQ6IHRlc3RMaW1pdCB9LFxuXHRcdFx0J3BhZGRpbmcnOiB7IHI6IGZhbHNlLCB0OiB0ZXN0UGFkZGluZyB9LFxuXHRcdFx0J2JlaGF2aW91cic6IHsgcjogdHJ1ZSwgdDogdGVzdEJlaGF2aW91ciB9LFxuXHRcdFx0J2FyaWFGb3JtYXQnOiB7IHI6IGZhbHNlLCB0OiB0ZXN0QXJpYUZvcm1hdCB9LFxuXHRcdFx0J2Zvcm1hdCc6IHsgcjogZmFsc2UsIHQ6IHRlc3RGb3JtYXQgfSxcblx0XHRcdCd0b29sdGlwcyc6IHsgcjogZmFsc2UsIHQ6IHRlc3RUb29sdGlwcyB9LFxuXHRcdFx0J2Nzc1ByZWZpeCc6IHsgcjogdHJ1ZSwgdDogdGVzdENzc1ByZWZpeCB9LFxuXHRcdFx0J2Nzc0NsYXNzZXMnOiB7IHI6IHRydWUsIHQ6IHRlc3RDc3NDbGFzc2VzIH1cblx0XHR9O1xuXG5cdFx0dmFyIGRlZmF1bHRzID0ge1xuXHRcdFx0J2Nvbm5lY3QnOiBmYWxzZSxcblx0XHRcdCdkaXJlY3Rpb24nOiAnbHRyJyxcblx0XHRcdCdiZWhhdmlvdXInOiAndGFwJyxcblx0XHRcdCdvcmllbnRhdGlvbic6ICdob3Jpem9udGFsJyxcblx0XHRcdCdjc3NQcmVmaXgnIDogJ25vVWktJyxcblx0XHRcdCdjc3NDbGFzc2VzJzoge1xuXHRcdFx0XHR0YXJnZXQ6ICd0YXJnZXQnLFxuXHRcdFx0XHRiYXNlOiAnYmFzZScsXG5cdFx0XHRcdG9yaWdpbjogJ29yaWdpbicsXG5cdFx0XHRcdGhhbmRsZTogJ2hhbmRsZScsXG5cdFx0XHRcdGhhbmRsZUxvd2VyOiAnaGFuZGxlLWxvd2VyJyxcblx0XHRcdFx0aGFuZGxlVXBwZXI6ICdoYW5kbGUtdXBwZXInLFxuXHRcdFx0XHRob3Jpem9udGFsOiAnaG9yaXpvbnRhbCcsXG5cdFx0XHRcdHZlcnRpY2FsOiAndmVydGljYWwnLFxuXHRcdFx0XHRiYWNrZ3JvdW5kOiAnYmFja2dyb3VuZCcsXG5cdFx0XHRcdGNvbm5lY3Q6ICdjb25uZWN0Jyxcblx0XHRcdFx0Y29ubmVjdHM6ICdjb25uZWN0cycsXG5cdFx0XHRcdGx0cjogJ2x0cicsXG5cdFx0XHRcdHJ0bDogJ3J0bCcsXG5cdFx0XHRcdGRyYWdnYWJsZTogJ2RyYWdnYWJsZScsXG5cdFx0XHRcdGRyYWc6ICdzdGF0ZS1kcmFnJyxcblx0XHRcdFx0dGFwOiAnc3RhdGUtdGFwJyxcblx0XHRcdFx0YWN0aXZlOiAnYWN0aXZlJyxcblx0XHRcdFx0dG9vbHRpcDogJ3Rvb2x0aXAnLFxuXHRcdFx0XHRwaXBzOiAncGlwcycsXG5cdFx0XHRcdHBpcHNIb3Jpem9udGFsOiAncGlwcy1ob3Jpem9udGFsJyxcblx0XHRcdFx0cGlwc1ZlcnRpY2FsOiAncGlwcy12ZXJ0aWNhbCcsXG5cdFx0XHRcdG1hcmtlcjogJ21hcmtlcicsXG5cdFx0XHRcdG1hcmtlckhvcml6b250YWw6ICdtYXJrZXItaG9yaXpvbnRhbCcsXG5cdFx0XHRcdG1hcmtlclZlcnRpY2FsOiAnbWFya2VyLXZlcnRpY2FsJyxcblx0XHRcdFx0bWFya2VyTm9ybWFsOiAnbWFya2VyLW5vcm1hbCcsXG5cdFx0XHRcdG1hcmtlckxhcmdlOiAnbWFya2VyLWxhcmdlJyxcblx0XHRcdFx0bWFya2VyU3ViOiAnbWFya2VyLXN1YicsXG5cdFx0XHRcdHZhbHVlOiAndmFsdWUnLFxuXHRcdFx0XHR2YWx1ZUhvcml6b250YWw6ICd2YWx1ZS1ob3Jpem9udGFsJyxcblx0XHRcdFx0dmFsdWVWZXJ0aWNhbDogJ3ZhbHVlLXZlcnRpY2FsJyxcblx0XHRcdFx0dmFsdWVOb3JtYWw6ICd2YWx1ZS1ub3JtYWwnLFxuXHRcdFx0XHR2YWx1ZUxhcmdlOiAndmFsdWUtbGFyZ2UnLFxuXHRcdFx0XHR2YWx1ZVN1YjogJ3ZhbHVlLXN1Yidcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0Ly8gQXJpYUZvcm1hdCBkZWZhdWx0cyB0byByZWd1bGFyIGZvcm1hdCwgaWYgYW55LlxuXHRcdGlmICggb3B0aW9ucy5mb3JtYXQgJiYgIW9wdGlvbnMuYXJpYUZvcm1hdCApIHtcblx0XHRcdG9wdGlvbnMuYXJpYUZvcm1hdCA9IG9wdGlvbnMuZm9ybWF0O1xuXHRcdH1cblxuXHRcdC8vIFJ1biBhbGwgb3B0aW9ucyB0aHJvdWdoIGEgdGVzdGluZyBtZWNoYW5pc20gdG8gZW5zdXJlIGNvcnJlY3Rcblx0XHQvLyBpbnB1dC4gSXQgc2hvdWxkIGJlIG5vdGVkIHRoYXQgb3B0aW9ucyBtaWdodCBnZXQgbW9kaWZpZWQgdG9cblx0XHQvLyBiZSBoYW5kbGVkIHByb3Blcmx5LiBFLmcuIHdyYXBwaW5nIGludGVnZXJzIGluIGFycmF5cy5cblx0XHRPYmplY3Qua2V5cyh0ZXN0cykuZm9yRWFjaChmdW5jdGlvbiggbmFtZSApe1xuXG5cdFx0XHQvLyBJZiB0aGUgb3B0aW9uIGlzbid0IHNldCwgYnV0IGl0IGlzIHJlcXVpcmVkLCB0aHJvdyBhbiBlcnJvci5cblx0XHRcdGlmICggIWlzU2V0KG9wdGlvbnNbbmFtZV0pICYmIGRlZmF1bHRzW25hbWVdID09PSB1bmRlZmluZWQgKSB7XG5cblx0XHRcdFx0aWYgKCB0ZXN0c1tuYW1lXS5yICkge1xuXHRcdFx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ1wiICsgbmFtZSArIFwiJyBpcyByZXF1aXJlZC5cIik7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRyZXR1cm4gdHJ1ZTtcblx0XHRcdH1cblxuXHRcdFx0dGVzdHNbbmFtZV0udCggcGFyc2VkLCAhaXNTZXQob3B0aW9uc1tuYW1lXSkgPyBkZWZhdWx0c1tuYW1lXSA6IG9wdGlvbnNbbmFtZV0gKTtcblx0XHR9KTtcblxuXHRcdC8vIEZvcndhcmQgcGlwcyBvcHRpb25zXG5cdFx0cGFyc2VkLnBpcHMgPSBvcHRpb25zLnBpcHM7XG5cblx0XHQvLyBBbGwgcmVjZW50IGJyb3dzZXJzIGFjY2VwdCB1bnByZWZpeGVkIHRyYW5zZm9ybS5cblx0XHQvLyBXZSBuZWVkIC1tcy0gZm9yIElFOSBhbmQgLXdlYmtpdC0gZm9yIG9sZGVyIEFuZHJvaWQ7XG5cdFx0Ly8gQXNzdW1lIHVzZSBvZiAtd2Via2l0LSBpZiB1bnByZWZpeGVkIGFuZCAtbXMtIGFyZSBub3Qgc3VwcG9ydGVkLlxuXHRcdC8vIGh0dHBzOi8vY2FuaXVzZS5jb20vI2ZlYXQ9dHJhbnNmb3JtczJkXG5cdFx0dmFyIGQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwiZGl2XCIpO1xuXHRcdHZhciBtc1ByZWZpeCA9IGQuc3R5bGUubXNUcmFuc2Zvcm0gIT09IHVuZGVmaW5lZDtcblx0XHR2YXIgbm9QcmVmaXggPSBkLnN0eWxlLnRyYW5zZm9ybSAhPT0gdW5kZWZpbmVkO1xuXG5cdFx0cGFyc2VkLnRyYW5zZm9ybVJ1bGUgPSBub1ByZWZpeCA/ICd0cmFuc2Zvcm0nIDogKG1zUHJlZml4ID8gJ21zVHJhbnNmb3JtJyA6ICd3ZWJraXRUcmFuc2Zvcm0nKTtcblxuXHRcdC8vIFBpcHMgZG9uJ3QgbW92ZSwgc28gd2UgY2FuIHBsYWNlIHRoZW0gdXNpbmcgbGVmdC90b3AuXG5cdFx0dmFyIHN0eWxlcyA9IFtbJ2xlZnQnLCAndG9wJ10sIFsncmlnaHQnLCAnYm90dG9tJ11dO1xuXG5cdFx0cGFyc2VkLnN0eWxlID0gc3R5bGVzW3BhcnNlZC5kaXJdW3BhcnNlZC5vcnRdO1xuXG5cdFx0cmV0dXJuIHBhcnNlZDtcblx0fVxuXHJcblxyXG5mdW5jdGlvbiBzY29wZSAoIHRhcmdldCwgb3B0aW9ucywgb3JpZ2luYWxPcHRpb25zICl7XHJcblxyXG5cdHZhciBhY3Rpb25zID0gZ2V0QWN0aW9ucygpO1xyXG5cdHZhciBzdXBwb3J0c1RvdWNoQWN0aW9uTm9uZSA9IGdldFN1cHBvcnRzVG91Y2hBY3Rpb25Ob25lKCk7XHJcblx0dmFyIHN1cHBvcnRzUGFzc2l2ZSA9IHN1cHBvcnRzVG91Y2hBY3Rpb25Ob25lICYmIGdldFN1cHBvcnRzUGFzc2l2ZSgpO1xyXG5cclxuXHQvLyBBbGwgdmFyaWFibGVzIGxvY2FsIHRvICdzY29wZScgYXJlIHByZWZpeGVkIHdpdGggJ3Njb3BlXydcclxuXHR2YXIgc2NvcGVfVGFyZ2V0ID0gdGFyZ2V0O1xyXG5cdHZhciBzY29wZV9Mb2NhdGlvbnMgPSBbXTtcclxuXHR2YXIgc2NvcGVfQmFzZTtcclxuXHR2YXIgc2NvcGVfSGFuZGxlcztcclxuXHR2YXIgc2NvcGVfSGFuZGxlTnVtYmVycyA9IFtdO1xyXG5cdHZhciBzY29wZV9BY3RpdmVIYW5kbGVzQ291bnQgPSAwO1xyXG5cdHZhciBzY29wZV9Db25uZWN0cztcclxuXHR2YXIgc2NvcGVfU3BlY3RydW0gPSBvcHRpb25zLnNwZWN0cnVtO1xyXG5cdHZhciBzY29wZV9WYWx1ZXMgPSBbXTtcclxuXHR2YXIgc2NvcGVfRXZlbnRzID0ge307XHJcblx0dmFyIHNjb3BlX1NlbGY7XHJcblx0dmFyIHNjb3BlX1BpcHM7XHJcblx0dmFyIHNjb3BlX0RvY3VtZW50ID0gdGFyZ2V0Lm93bmVyRG9jdW1lbnQ7XHJcblx0dmFyIHNjb3BlX0RvY3VtZW50RWxlbWVudCA9IHNjb3BlX0RvY3VtZW50LmRvY3VtZW50RWxlbWVudDtcclxuXHR2YXIgc2NvcGVfQm9keSA9IHNjb3BlX0RvY3VtZW50LmJvZHk7XHJcblxyXG5cclxuXHQvLyBGb3IgaG9yaXpvbnRhbCBzbGlkZXJzIGluIHN0YW5kYXJkIGx0ciBkb2N1bWVudHMsXHJcblx0Ly8gbWFrZSAubm9VaS1vcmlnaW4gb3ZlcmZsb3cgdG8gdGhlIGxlZnQgc28gdGhlIGRvY3VtZW50IGRvZXNuJ3Qgc2Nyb2xsLlxyXG5cdHZhciBzY29wZV9EaXJPZmZzZXQgPSAoc2NvcGVfRG9jdW1lbnQuZGlyID09PSAncnRsJykgfHwgKG9wdGlvbnMub3J0ID09PSAxKSA/IDAgOiAxMDA7XHJcblxyXG4vKiEgSW4gdGhpcyBmaWxlOiBDb25zdHJ1Y3Rpb24gb2YgRE9NIGVsZW1lbnRzOyAqL1xyXG5cclxuXHQvLyBDcmVhdGVzIGEgbm9kZSwgYWRkcyBpdCB0byB0YXJnZXQsIHJldHVybnMgdGhlIG5ldyBub2RlLlxyXG5cdGZ1bmN0aW9uIGFkZE5vZGVUbyAoIGFkZFRhcmdldCwgY2xhc3NOYW1lICkge1xyXG5cclxuXHRcdHZhciBkaXYgPSBzY29wZV9Eb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcclxuXHJcblx0XHRpZiAoIGNsYXNzTmFtZSApIHtcclxuXHRcdFx0YWRkQ2xhc3MoZGl2LCBjbGFzc05hbWUpO1xyXG5cdFx0fVxyXG5cclxuXHRcdGFkZFRhcmdldC5hcHBlbmRDaGlsZChkaXYpO1xyXG5cclxuXHRcdHJldHVybiBkaXY7XHJcblx0fVxyXG5cclxuXHQvLyBBcHBlbmQgYSBvcmlnaW4gdG8gdGhlIGJhc2VcclxuXHRmdW5jdGlvbiBhZGRPcmlnaW4gKCBiYXNlLCBoYW5kbGVOdW1iZXIgKSB7XHJcblxyXG5cdFx0dmFyIG9yaWdpbiA9IGFkZE5vZGVUbyhiYXNlLCBvcHRpb25zLmNzc0NsYXNzZXMub3JpZ2luKTtcclxuXHRcdHZhciBoYW5kbGUgPSBhZGROb2RlVG8ob3JpZ2luLCBvcHRpb25zLmNzc0NsYXNzZXMuaGFuZGxlKTtcclxuXHJcblx0XHRoYW5kbGUuc2V0QXR0cmlidXRlKCdkYXRhLWhhbmRsZScsIGhhbmRsZU51bWJlcik7XHJcblxyXG5cdFx0Ly8gaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvSFRNTC9HbG9iYWxfYXR0cmlidXRlcy90YWJpbmRleFxyXG5cdFx0Ly8gMCA9IGZvY3VzYWJsZSBhbmQgcmVhY2hhYmxlXHJcblx0XHRoYW5kbGUuc2V0QXR0cmlidXRlKCd0YWJpbmRleCcsICcwJyk7XHJcblx0XHRoYW5kbGUuc2V0QXR0cmlidXRlKCdyb2xlJywgJ3NsaWRlcicpO1xyXG5cdFx0aGFuZGxlLnNldEF0dHJpYnV0ZSgnYXJpYS1vcmllbnRhdGlvbicsIG9wdGlvbnMub3J0ID8gJ3ZlcnRpY2FsJyA6ICdob3Jpem9udGFsJyk7XHJcblxyXG5cdFx0aWYgKCBoYW5kbGVOdW1iZXIgPT09IDAgKSB7XHJcblx0XHRcdGFkZENsYXNzKGhhbmRsZSwgb3B0aW9ucy5jc3NDbGFzc2VzLmhhbmRsZUxvd2VyKTtcclxuXHRcdH1cclxuXHJcblx0XHRlbHNlIGlmICggaGFuZGxlTnVtYmVyID09PSBvcHRpb25zLmhhbmRsZXMgLSAxICkge1xyXG5cdFx0XHRhZGRDbGFzcyhoYW5kbGUsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5oYW5kbGVVcHBlcik7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIG9yaWdpbjtcclxuXHR9XHJcblxyXG5cdC8vIEluc2VydCBub2RlcyBmb3IgY29ubmVjdCBlbGVtZW50c1xyXG5cdGZ1bmN0aW9uIGFkZENvbm5lY3QgKCBiYXNlLCBhZGQgKSB7XHJcblxyXG5cdFx0aWYgKCAhYWRkICkge1xyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIGFkZE5vZGVUbyhiYXNlLCBvcHRpb25zLmNzc0NsYXNzZXMuY29ubmVjdCk7XHJcblx0fVxyXG5cclxuXHQvLyBBZGQgaGFuZGxlcyB0byB0aGUgc2xpZGVyIGJhc2UuXHJcblx0ZnVuY3Rpb24gYWRkRWxlbWVudHMgKCBjb25uZWN0T3B0aW9ucywgYmFzZSApIHtcclxuXHJcblx0XHR2YXIgY29ubmVjdEJhc2UgPSBhZGROb2RlVG8oYmFzZSwgb3B0aW9ucy5jc3NDbGFzc2VzLmNvbm5lY3RzKTtcclxuXHJcblx0XHRzY29wZV9IYW5kbGVzID0gW107XHJcblx0XHRzY29wZV9Db25uZWN0cyA9IFtdO1xyXG5cclxuXHRcdHNjb3BlX0Nvbm5lY3RzLnB1c2goYWRkQ29ubmVjdChjb25uZWN0QmFzZSwgY29ubmVjdE9wdGlvbnNbMF0pKTtcclxuXHJcblx0XHQvLyBbOjo6Ok89PT09Tz09PT1PPT09PV1cclxuXHRcdC8vIGNvbm5lY3RPcHRpb25zID0gWzAsIDEsIDEsIDFdXHJcblxyXG5cdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgb3B0aW9ucy5oYW5kbGVzOyBpKysgKSB7XHJcblx0XHRcdC8vIEtlZXAgYSBsaXN0IG9mIGFsbCBhZGRlZCBoYW5kbGVzLlxyXG5cdFx0XHRzY29wZV9IYW5kbGVzLnB1c2goYWRkT3JpZ2luKGJhc2UsIGkpKTtcclxuXHRcdFx0c2NvcGVfSGFuZGxlTnVtYmVyc1tpXSA9IGk7XHJcblx0XHRcdHNjb3BlX0Nvbm5lY3RzLnB1c2goYWRkQ29ubmVjdChjb25uZWN0QmFzZSwgY29ubmVjdE9wdGlvbnNbaSArIDFdKSk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvLyBJbml0aWFsaXplIGEgc2luZ2xlIHNsaWRlci5cclxuXHRmdW5jdGlvbiBhZGRTbGlkZXIgKCBhZGRUYXJnZXQgKSB7XHJcblxyXG5cdFx0Ly8gQXBwbHkgY2xhc3NlcyBhbmQgZGF0YSB0byB0aGUgdGFyZ2V0LlxyXG5cdFx0YWRkQ2xhc3MoYWRkVGFyZ2V0LCBvcHRpb25zLmNzc0NsYXNzZXMudGFyZ2V0KTtcclxuXHJcblx0XHRpZiAoIG9wdGlvbnMuZGlyID09PSAwICkge1xyXG5cdFx0XHRhZGRDbGFzcyhhZGRUYXJnZXQsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5sdHIpO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0YWRkQ2xhc3MoYWRkVGFyZ2V0LCBvcHRpb25zLmNzc0NsYXNzZXMucnRsKTtcclxuXHRcdH1cclxuXHJcblx0XHRpZiAoIG9wdGlvbnMub3J0ID09PSAwICkge1xyXG5cdFx0XHRhZGRDbGFzcyhhZGRUYXJnZXQsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5ob3Jpem9udGFsKTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdGFkZENsYXNzKGFkZFRhcmdldCwgb3B0aW9ucy5jc3NDbGFzc2VzLnZlcnRpY2FsKTtcclxuXHRcdH1cclxuXHJcblx0XHRzY29wZV9CYXNlID0gYWRkTm9kZVRvKGFkZFRhcmdldCwgb3B0aW9ucy5jc3NDbGFzc2VzLmJhc2UpO1xyXG5cdH1cclxuXHJcblxyXG5cdGZ1bmN0aW9uIGFkZFRvb2x0aXAgKCBoYW5kbGUsIGhhbmRsZU51bWJlciApIHtcclxuXHJcblx0XHRpZiAoICFvcHRpb25zLnRvb2x0aXBzW2hhbmRsZU51bWJlcl0gKSB7XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gYWRkTm9kZVRvKGhhbmRsZS5maXJzdENoaWxkLCBvcHRpb25zLmNzc0NsYXNzZXMudG9vbHRpcCk7XHJcblx0fVxyXG5cclxuXHQvLyBUaGUgdG9vbHRpcHMgb3B0aW9uIGlzIGEgc2hvcnRoYW5kIGZvciB1c2luZyB0aGUgJ3VwZGF0ZScgZXZlbnQuXHJcblx0ZnVuY3Rpb24gdG9vbHRpcHMgKCApIHtcclxuXHJcblx0XHQvLyBUb29sdGlwcyBhcmUgYWRkZWQgd2l0aCBvcHRpb25zLnRvb2x0aXBzIGluIG9yaWdpbmFsIG9yZGVyLlxyXG5cdFx0dmFyIHRpcHMgPSBzY29wZV9IYW5kbGVzLm1hcChhZGRUb29sdGlwKTtcclxuXHJcblx0XHRiaW5kRXZlbnQoJ3VwZGF0ZScsIGZ1bmN0aW9uKHZhbHVlcywgaGFuZGxlTnVtYmVyLCB1bmVuY29kZWQpIHtcclxuXHJcblx0XHRcdGlmICggIXRpcHNbaGFuZGxlTnVtYmVyXSApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciBmb3JtYXR0ZWRWYWx1ZSA9IHZhbHVlc1toYW5kbGVOdW1iZXJdO1xyXG5cclxuXHRcdFx0aWYgKCBvcHRpb25zLnRvb2x0aXBzW2hhbmRsZU51bWJlcl0gIT09IHRydWUgKSB7XHJcblx0XHRcdFx0Zm9ybWF0dGVkVmFsdWUgPSBvcHRpb25zLnRvb2x0aXBzW2hhbmRsZU51bWJlcl0udG8odW5lbmNvZGVkW2hhbmRsZU51bWJlcl0pO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR0aXBzW2hhbmRsZU51bWJlcl0uaW5uZXJIVE1MID0gZm9ybWF0dGVkVmFsdWU7XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cclxuXHRmdW5jdGlvbiBhcmlhICggKSB7XHJcblxyXG5cdFx0YmluZEV2ZW50KCd1cGRhdGUnLCBmdW5jdGlvbiAoIHZhbHVlcywgaGFuZGxlTnVtYmVyLCB1bmVuY29kZWQsIHRhcCwgcG9zaXRpb25zICkge1xyXG5cclxuXHRcdFx0Ly8gVXBkYXRlIEFyaWEgVmFsdWVzIGZvciBhbGwgaGFuZGxlcywgYXMgYSBjaGFuZ2UgaW4gb25lIGNoYW5nZXMgbWluIGFuZCBtYXggdmFsdWVzIGZvciB0aGUgbmV4dC5cclxuXHRcdFx0c2NvcGVfSGFuZGxlTnVtYmVycy5mb3JFYWNoKGZ1bmN0aW9uKCBpbmRleCApe1xyXG5cclxuXHRcdFx0XHR2YXIgaGFuZGxlID0gc2NvcGVfSGFuZGxlc1tpbmRleF07XHJcblxyXG5cdFx0XHRcdHZhciBtaW4gPSBjaGVja0hhbmRsZVBvc2l0aW9uKHNjb3BlX0xvY2F0aW9ucywgaW5kZXgsIDAsIHRydWUsIHRydWUsIHRydWUpO1xyXG5cdFx0XHRcdHZhciBtYXggPSBjaGVja0hhbmRsZVBvc2l0aW9uKHNjb3BlX0xvY2F0aW9ucywgaW5kZXgsIDEwMCwgdHJ1ZSwgdHJ1ZSwgdHJ1ZSk7XHJcblxyXG5cdFx0XHRcdHZhciBub3cgPSBwb3NpdGlvbnNbaW5kZXhdO1xyXG5cdFx0XHRcdHZhciB0ZXh0ID0gb3B0aW9ucy5hcmlhRm9ybWF0LnRvKHVuZW5jb2RlZFtpbmRleF0pO1xyXG5cclxuXHRcdFx0XHRoYW5kbGUuY2hpbGRyZW5bMF0uc2V0QXR0cmlidXRlKCdhcmlhLXZhbHVlbWluJywgbWluLnRvRml4ZWQoMSkpO1xyXG5cdFx0XHRcdGhhbmRsZS5jaGlsZHJlblswXS5zZXRBdHRyaWJ1dGUoJ2FyaWEtdmFsdWVtYXgnLCBtYXgudG9GaXhlZCgxKSk7XHJcblx0XHRcdFx0aGFuZGxlLmNoaWxkcmVuWzBdLnNldEF0dHJpYnV0ZSgnYXJpYS12YWx1ZW5vdycsIG5vdy50b0ZpeGVkKDEpKTtcclxuXHRcdFx0XHRoYW5kbGUuY2hpbGRyZW5bMF0uc2V0QXR0cmlidXRlKCdhcmlhLXZhbHVldGV4dCcsIHRleHQpO1xyXG5cdFx0XHR9KTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHJcblxyXG5cdGZ1bmN0aW9uIGdldEdyb3VwICggbW9kZSwgdmFsdWVzLCBzdGVwcGVkICkge1xyXG5cclxuXHRcdC8vIFVzZSB0aGUgcmFuZ2UuXHJcblx0XHRpZiAoIG1vZGUgPT09ICdyYW5nZScgfHwgbW9kZSA9PT0gJ3N0ZXBzJyApIHtcclxuXHRcdFx0cmV0dXJuIHNjb3BlX1NwZWN0cnVtLnhWYWw7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCBtb2RlID09PSAnY291bnQnICkge1xyXG5cclxuXHRcdFx0aWYgKCB2YWx1ZXMgPCAyICkge1xyXG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcihcIm5vVWlTbGlkZXIgKFwiICsgVkVSU0lPTiArIFwiKTogJ3ZhbHVlcycgKD49IDIpIHJlcXVpcmVkIGZvciBtb2RlICdjb3VudCcuXCIpO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBEaXZpZGUgMCAtIDEwMCBpbiAnY291bnQnIHBhcnRzLlxyXG5cdFx0XHR2YXIgaW50ZXJ2YWwgPSB2YWx1ZXMgLSAxO1xyXG5cdFx0XHR2YXIgc3ByZWFkID0gKCAxMDAgLyBpbnRlcnZhbCApO1xyXG5cclxuXHRcdFx0dmFsdWVzID0gW107XHJcblxyXG5cdFx0XHQvLyBMaXN0IHRoZXNlIHBhcnRzIGFuZCBoYXZlIHRoZW0gaGFuZGxlZCBhcyAncG9zaXRpb25zJy5cclxuXHRcdFx0d2hpbGUgKCBpbnRlcnZhbC0tICkge1xyXG5cdFx0XHRcdHZhbHVlc1sgaW50ZXJ2YWwgXSA9ICggaW50ZXJ2YWwgKiBzcHJlYWQgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFsdWVzLnB1c2goMTAwKTtcclxuXHJcblx0XHRcdG1vZGUgPSAncG9zaXRpb25zJztcclxuXHRcdH1cclxuXHJcblx0XHRpZiAoIG1vZGUgPT09ICdwb3NpdGlvbnMnICkge1xyXG5cclxuXHRcdFx0Ly8gTWFwIGFsbCBwZXJjZW50YWdlcyB0byBvbi1yYW5nZSB2YWx1ZXMuXHJcblx0XHRcdHJldHVybiB2YWx1ZXMubWFwKGZ1bmN0aW9uKCB2YWx1ZSApe1xyXG5cdFx0XHRcdHJldHVybiBzY29wZV9TcGVjdHJ1bS5mcm9tU3RlcHBpbmcoIHN0ZXBwZWQgPyBzY29wZV9TcGVjdHJ1bS5nZXRTdGVwKCB2YWx1ZSApIDogdmFsdWUgKTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCBtb2RlID09PSAndmFsdWVzJyApIHtcclxuXHJcblx0XHRcdC8vIElmIHRoZSB2YWx1ZSBtdXN0IGJlIHN0ZXBwZWQsIGl0IG5lZWRzIHRvIGJlIGNvbnZlcnRlZCB0byBhIHBlcmNlbnRhZ2UgZmlyc3QuXHJcblx0XHRcdGlmICggc3RlcHBlZCApIHtcclxuXHJcblx0XHRcdFx0cmV0dXJuIHZhbHVlcy5tYXAoZnVuY3Rpb24oIHZhbHVlICl7XHJcblxyXG5cdFx0XHRcdFx0Ly8gQ29udmVydCB0byBwZXJjZW50YWdlLCBhcHBseSBzdGVwLCByZXR1cm4gdG8gdmFsdWUuXHJcblx0XHRcdFx0XHRyZXR1cm4gc2NvcGVfU3BlY3RydW0uZnJvbVN0ZXBwaW5nKCBzY29wZV9TcGVjdHJ1bS5nZXRTdGVwKCBzY29wZV9TcGVjdHJ1bS50b1N0ZXBwaW5nKCB2YWx1ZSApICkgKTtcclxuXHRcdFx0XHR9KTtcclxuXHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIE90aGVyd2lzZSwgd2UgY2FuIHNpbXBseSB1c2UgdGhlIHZhbHVlcy5cclxuXHRcdFx0cmV0dXJuIHZhbHVlcztcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIGdlbmVyYXRlU3ByZWFkICggZGVuc2l0eSwgbW9kZSwgZ3JvdXAgKSB7XHJcblxyXG5cdFx0ZnVuY3Rpb24gc2FmZUluY3JlbWVudCh2YWx1ZSwgaW5jcmVtZW50KSB7XHJcblx0XHRcdC8vIEF2b2lkIGZsb2F0aW5nIHBvaW50IHZhcmlhbmNlIGJ5IGRyb3BwaW5nIHRoZSBzbWFsbGVzdCBkZWNpbWFsIHBsYWNlcy5cclxuXHRcdFx0cmV0dXJuICh2YWx1ZSArIGluY3JlbWVudCkudG9GaXhlZCg3KSAvIDE7XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIGluZGV4ZXMgPSB7fTtcclxuXHRcdHZhciBmaXJzdEluUmFuZ2UgPSBzY29wZV9TcGVjdHJ1bS54VmFsWzBdO1xyXG5cdFx0dmFyIGxhc3RJblJhbmdlID0gc2NvcGVfU3BlY3RydW0ueFZhbFtzY29wZV9TcGVjdHJ1bS54VmFsLmxlbmd0aC0xXTtcclxuXHRcdHZhciBpZ25vcmVGaXJzdCA9IGZhbHNlO1xyXG5cdFx0dmFyIGlnbm9yZUxhc3QgPSBmYWxzZTtcclxuXHRcdHZhciBwcmV2UGN0ID0gMDtcclxuXHJcblx0XHQvLyBDcmVhdGUgYSBjb3B5IG9mIHRoZSBncm91cCwgc29ydCBpdCBhbmQgZmlsdGVyIGF3YXkgYWxsIGR1cGxpY2F0ZXMuXHJcblx0XHRncm91cCA9IHVuaXF1ZShncm91cC5zbGljZSgpLnNvcnQoZnVuY3Rpb24oYSwgYil7IHJldHVybiBhIC0gYjsgfSkpO1xyXG5cclxuXHRcdC8vIE1ha2Ugc3VyZSB0aGUgcmFuZ2Ugc3RhcnRzIHdpdGggdGhlIGZpcnN0IGVsZW1lbnQuXHJcblx0XHRpZiAoIGdyb3VwWzBdICE9PSBmaXJzdEluUmFuZ2UgKSB7XHJcblx0XHRcdGdyb3VwLnVuc2hpZnQoZmlyc3RJblJhbmdlKTtcclxuXHRcdFx0aWdub3JlRmlyc3QgPSB0cnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIExpa2V3aXNlIGZvciB0aGUgbGFzdCBvbmUuXHJcblx0XHRpZiAoIGdyb3VwW2dyb3VwLmxlbmd0aCAtIDFdICE9PSBsYXN0SW5SYW5nZSApIHtcclxuXHRcdFx0Z3JvdXAucHVzaChsYXN0SW5SYW5nZSk7XHJcblx0XHRcdGlnbm9yZUxhc3QgPSB0cnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdGdyb3VwLmZvckVhY2goZnVuY3Rpb24gKCBjdXJyZW50LCBpbmRleCApIHtcclxuXHJcblx0XHRcdC8vIEdldCB0aGUgY3VycmVudCBzdGVwIGFuZCB0aGUgbG93ZXIgKyB1cHBlciBwb3NpdGlvbnMuXHJcblx0XHRcdHZhciBzdGVwO1xyXG5cdFx0XHR2YXIgaTtcclxuXHRcdFx0dmFyIHE7XHJcblx0XHRcdHZhciBsb3cgPSBjdXJyZW50O1xyXG5cdFx0XHR2YXIgaGlnaCA9IGdyb3VwW2luZGV4KzFdO1xyXG5cdFx0XHR2YXIgbmV3UGN0O1xyXG5cdFx0XHR2YXIgcGN0RGlmZmVyZW5jZTtcclxuXHRcdFx0dmFyIHBjdFBvcztcclxuXHRcdFx0dmFyIHR5cGU7XHJcblx0XHRcdHZhciBzdGVwcztcclxuXHRcdFx0dmFyIHJlYWxTdGVwcztcclxuXHRcdFx0dmFyIHN0ZXBzaXplO1xyXG5cclxuXHRcdFx0Ly8gV2hlbiB1c2luZyAnc3RlcHMnIG1vZGUsIHVzZSB0aGUgcHJvdmlkZWQgc3RlcHMuXHJcblx0XHRcdC8vIE90aGVyd2lzZSwgd2UnbGwgc3RlcCBvbiB0byB0aGUgbmV4dCBzdWJyYW5nZS5cclxuXHRcdFx0aWYgKCBtb2RlID09PSAnc3RlcHMnICkge1xyXG5cdFx0XHRcdHN0ZXAgPSBzY29wZV9TcGVjdHJ1bS54TnVtU3RlcHNbIGluZGV4IF07XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIERlZmF1bHQgdG8gYSAnZnVsbCcgc3RlcC5cclxuXHRcdFx0aWYgKCAhc3RlcCApIHtcclxuXHRcdFx0XHRzdGVwID0gaGlnaC1sb3c7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIExvdyBjYW4gYmUgMCwgc28gdGVzdCBmb3IgZmFsc2UuIElmIGhpZ2ggaXMgdW5kZWZpbmVkLFxyXG5cdFx0XHQvLyB3ZSBhcmUgYXQgdGhlIGxhc3Qgc3VicmFuZ2UuIEluZGV4IDAgaXMgYWxyZWFkeSBoYW5kbGVkLlxyXG5cdFx0XHRpZiAoIGxvdyA9PT0gZmFsc2UgfHwgaGlnaCA9PT0gdW5kZWZpbmVkICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gTWFrZSBzdXJlIHN0ZXAgaXNuJ3QgMCwgd2hpY2ggd291bGQgY2F1c2UgYW4gaW5maW5pdGUgbG9vcCAoIzY1NClcclxuXHRcdFx0c3RlcCA9IE1hdGgubWF4KHN0ZXAsIDAuMDAwMDAwMSk7XHJcblxyXG5cdFx0XHQvLyBGaW5kIGFsbCBzdGVwcyBpbiB0aGUgc3VicmFuZ2UuXHJcblx0XHRcdGZvciAoIGkgPSBsb3c7IGkgPD0gaGlnaDsgaSA9IHNhZmVJbmNyZW1lbnQoaSwgc3RlcCkgKSB7XHJcblxyXG5cdFx0XHRcdC8vIEdldCB0aGUgcGVyY2VudGFnZSB2YWx1ZSBmb3IgdGhlIGN1cnJlbnQgc3RlcCxcclxuXHRcdFx0XHQvLyBjYWxjdWxhdGUgdGhlIHNpemUgZm9yIHRoZSBzdWJyYW5nZS5cclxuXHRcdFx0XHRuZXdQY3QgPSBzY29wZV9TcGVjdHJ1bS50b1N0ZXBwaW5nKCBpICk7XHJcblx0XHRcdFx0cGN0RGlmZmVyZW5jZSA9IG5ld1BjdCAtIHByZXZQY3Q7XHJcblxyXG5cdFx0XHRcdHN0ZXBzID0gcGN0RGlmZmVyZW5jZSAvIGRlbnNpdHk7XHJcblx0XHRcdFx0cmVhbFN0ZXBzID0gTWF0aC5yb3VuZChzdGVwcyk7XHJcblxyXG5cdFx0XHRcdC8vIFRoaXMgcmF0aW8gcmVwcmVzZW50cyB0aGUgYW1vdW50IG9mIHBlcmNlbnRhZ2Utc3BhY2UgYSBwb2ludCBpbmRpY2F0ZXMuXHJcblx0XHRcdFx0Ly8gRm9yIGEgZGVuc2l0eSAxIHRoZSBwb2ludHMvcGVyY2VudGFnZSA9IDEuIEZvciBkZW5zaXR5IDIsIHRoYXQgcGVyY2VudGFnZSBuZWVkcyB0byBiZSByZS1kZXZpZGVkLlxyXG5cdFx0XHRcdC8vIFJvdW5kIHRoZSBwZXJjZW50YWdlIG9mZnNldCB0byBhbiBldmVuIG51bWJlciwgdGhlbiBkaXZpZGUgYnkgdHdvXHJcblx0XHRcdFx0Ly8gdG8gc3ByZWFkIHRoZSBvZmZzZXQgb24gYm90aCBzaWRlcyBvZiB0aGUgcmFuZ2UuXHJcblx0XHRcdFx0c3RlcHNpemUgPSBwY3REaWZmZXJlbmNlL3JlYWxTdGVwcztcclxuXHJcblx0XHRcdFx0Ly8gRGl2aWRlIGFsbCBwb2ludHMgZXZlbmx5LCBhZGRpbmcgdGhlIGNvcnJlY3QgbnVtYmVyIHRvIHRoaXMgc3VicmFuZ2UuXHJcblx0XHRcdFx0Ly8gUnVuIHVwIHRvIDw9IHNvIHRoYXQgMTAwJSBnZXRzIGEgcG9pbnQsIGV2ZW50IGlmIGlnbm9yZUxhc3QgaXMgc2V0LlxyXG5cdFx0XHRcdGZvciAoIHEgPSAxOyBxIDw9IHJlYWxTdGVwczsgcSArPSAxICkge1xyXG5cclxuXHRcdFx0XHRcdC8vIFRoZSByYXRpbyBiZXR3ZWVuIHRoZSByb3VuZGVkIHZhbHVlIGFuZCB0aGUgYWN0dWFsIHNpemUgbWlnaHQgYmUgfjElIG9mZi5cclxuXHRcdFx0XHRcdC8vIENvcnJlY3QgdGhlIHBlcmNlbnRhZ2Ugb2Zmc2V0IGJ5IHRoZSBudW1iZXIgb2YgcG9pbnRzXHJcblx0XHRcdFx0XHQvLyBwZXIgc3VicmFuZ2UuIGRlbnNpdHkgPSAxIHdpbGwgcmVzdWx0IGluIDEwMCBwb2ludHMgb24gdGhlXHJcblx0XHRcdFx0XHQvLyBmdWxsIHJhbmdlLCAyIGZvciA1MCwgNCBmb3IgMjUsIGV0Yy5cclxuXHRcdFx0XHRcdHBjdFBvcyA9IHByZXZQY3QgKyAoIHEgKiBzdGVwc2l6ZSApO1xyXG5cdFx0XHRcdFx0aW5kZXhlc1twY3RQb3MudG9GaXhlZCg1KV0gPSBbJ3gnLCAwXTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIERldGVybWluZSB0aGUgcG9pbnQgdHlwZS5cclxuXHRcdFx0XHR0eXBlID0gKGdyb3VwLmluZGV4T2YoaSkgPiAtMSkgPyAxIDogKCBtb2RlID09PSAnc3RlcHMnID8gMiA6IDAgKTtcclxuXHJcblx0XHRcdFx0Ly8gRW5mb3JjZSB0aGUgJ2lnbm9yZUZpcnN0JyBvcHRpb24gYnkgb3ZlcndyaXRpbmcgdGhlIHR5cGUgZm9yIDAuXHJcblx0XHRcdFx0aWYgKCAhaW5kZXggJiYgaWdub3JlRmlyc3QgKSB7XHJcblx0XHRcdFx0XHR0eXBlID0gMDtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGlmICggIShpID09PSBoaWdoICYmIGlnbm9yZUxhc3QpKSB7XHJcblx0XHRcdFx0XHQvLyBNYXJrIHRoZSAndHlwZScgb2YgdGhpcyBwb2ludC4gMCA9IHBsYWluLCAxID0gcmVhbCB2YWx1ZSwgMiA9IHN0ZXAgdmFsdWUuXHJcblx0XHRcdFx0XHRpbmRleGVzW25ld1BjdC50b0ZpeGVkKDUpXSA9IFtpLCB0eXBlXTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIFVwZGF0ZSB0aGUgcGVyY2VudGFnZSBjb3VudC5cclxuXHRcdFx0XHRwcmV2UGN0ID0gbmV3UGN0O1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHJcblx0XHRyZXR1cm4gaW5kZXhlcztcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIGFkZE1hcmtpbmcgKCBzcHJlYWQsIGZpbHRlckZ1bmMsIGZvcm1hdHRlciApIHtcclxuXHJcblx0XHR2YXIgZWxlbWVudCA9IHNjb3BlX0RvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xyXG5cclxuXHRcdHZhciB2YWx1ZVNpemVDbGFzc2VzID0gW1xyXG5cdFx0XHRvcHRpb25zLmNzc0NsYXNzZXMudmFsdWVOb3JtYWwsXHJcblx0XHRcdG9wdGlvbnMuY3NzQ2xhc3Nlcy52YWx1ZUxhcmdlLFxyXG5cdFx0XHRvcHRpb25zLmNzc0NsYXNzZXMudmFsdWVTdWJcclxuXHRcdF07XHJcblx0XHR2YXIgbWFya2VyU2l6ZUNsYXNzZXMgPSBbXHJcblx0XHRcdG9wdGlvbnMuY3NzQ2xhc3Nlcy5tYXJrZXJOb3JtYWwsXHJcblx0XHRcdG9wdGlvbnMuY3NzQ2xhc3Nlcy5tYXJrZXJMYXJnZSxcclxuXHRcdFx0b3B0aW9ucy5jc3NDbGFzc2VzLm1hcmtlclN1YlxyXG5cdFx0XTtcclxuXHRcdHZhciB2YWx1ZU9yaWVudGF0aW9uQ2xhc3NlcyA9IFtcclxuXHRcdFx0b3B0aW9ucy5jc3NDbGFzc2VzLnZhbHVlSG9yaXpvbnRhbCxcclxuXHRcdFx0b3B0aW9ucy5jc3NDbGFzc2VzLnZhbHVlVmVydGljYWxcclxuXHRcdF07XHJcblx0XHR2YXIgbWFya2VyT3JpZW50YXRpb25DbGFzc2VzID0gW1xyXG5cdFx0XHRvcHRpb25zLmNzc0NsYXNzZXMubWFya2VySG9yaXpvbnRhbCxcclxuXHRcdFx0b3B0aW9ucy5jc3NDbGFzc2VzLm1hcmtlclZlcnRpY2FsXHJcblx0XHRdO1xyXG5cclxuXHRcdGFkZENsYXNzKGVsZW1lbnQsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5waXBzKTtcclxuXHRcdGFkZENsYXNzKGVsZW1lbnQsIG9wdGlvbnMub3J0ID09PSAwID8gb3B0aW9ucy5jc3NDbGFzc2VzLnBpcHNIb3Jpem9udGFsIDogb3B0aW9ucy5jc3NDbGFzc2VzLnBpcHNWZXJ0aWNhbCk7XHJcblxyXG5cdFx0ZnVuY3Rpb24gZ2V0Q2xhc3NlcyggdHlwZSwgc291cmNlICl7XHJcblx0XHRcdHZhciBhID0gc291cmNlID09PSBvcHRpb25zLmNzc0NsYXNzZXMudmFsdWU7XHJcblx0XHRcdHZhciBvcmllbnRhdGlvbkNsYXNzZXMgPSBhID8gdmFsdWVPcmllbnRhdGlvbkNsYXNzZXMgOiBtYXJrZXJPcmllbnRhdGlvbkNsYXNzZXM7XHJcblx0XHRcdHZhciBzaXplQ2xhc3NlcyA9IGEgPyB2YWx1ZVNpemVDbGFzc2VzIDogbWFya2VyU2l6ZUNsYXNzZXM7XHJcblxyXG5cdFx0XHRyZXR1cm4gc291cmNlICsgJyAnICsgb3JpZW50YXRpb25DbGFzc2VzW29wdGlvbnMub3J0XSArICcgJyArIHNpemVDbGFzc2VzW3R5cGVdO1xyXG5cdFx0fVxyXG5cclxuXHRcdGZ1bmN0aW9uIGFkZFNwcmVhZCAoIG9mZnNldCwgdmFsdWVzICl7XHJcblxyXG5cdFx0XHQvLyBBcHBseSB0aGUgZmlsdGVyIGZ1bmN0aW9uLCBpZiBpdCBpcyBzZXQuXHJcblx0XHRcdHZhbHVlc1sxXSA9ICh2YWx1ZXNbMV0gJiYgZmlsdGVyRnVuYykgPyBmaWx0ZXJGdW5jKHZhbHVlc1swXSwgdmFsdWVzWzFdKSA6IHZhbHVlc1sxXTtcclxuXHJcblx0XHRcdC8vIEFkZCBhIG1hcmtlciBmb3IgZXZlcnkgcG9pbnRcclxuXHRcdFx0dmFyIG5vZGUgPSBhZGROb2RlVG8oZWxlbWVudCwgZmFsc2UpO1xyXG5cdFx0XHRcdG5vZGUuY2xhc3NOYW1lID0gZ2V0Q2xhc3Nlcyh2YWx1ZXNbMV0sIG9wdGlvbnMuY3NzQ2xhc3Nlcy5tYXJrZXIpO1xyXG5cdFx0XHRcdG5vZGUuc3R5bGVbb3B0aW9ucy5zdHlsZV0gPSBvZmZzZXQgKyAnJSc7XHJcblxyXG5cdFx0XHQvLyBWYWx1ZXMgYXJlIG9ubHkgYXBwZW5kZWQgZm9yIHBvaW50cyBtYXJrZWQgJzEnIG9yICcyJy5cclxuXHRcdFx0aWYgKCB2YWx1ZXNbMV0gKSB7XHJcblx0XHRcdFx0bm9kZSA9IGFkZE5vZGVUbyhlbGVtZW50LCBmYWxzZSk7XHJcblx0XHRcdFx0bm9kZS5jbGFzc05hbWUgPSBnZXRDbGFzc2VzKHZhbHVlc1sxXSwgb3B0aW9ucy5jc3NDbGFzc2VzLnZhbHVlKTtcclxuXHRcdFx0XHRub2RlLnNldEF0dHJpYnV0ZSgnZGF0YS12YWx1ZScsIHZhbHVlc1swXSk7XHJcblx0XHRcdFx0bm9kZS5zdHlsZVtvcHRpb25zLnN0eWxlXSA9IG9mZnNldCArICclJztcclxuXHRcdFx0XHRub2RlLmlubmVyVGV4dCA9IGZvcm1hdHRlci50byh2YWx1ZXNbMF0pO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gQXBwZW5kIGFsbCBwb2ludHMuXHJcblx0XHRPYmplY3Qua2V5cyhzcHJlYWQpLmZvckVhY2goZnVuY3Rpb24oYSl7XHJcblx0XHRcdGFkZFNwcmVhZChhLCBzcHJlYWRbYV0pO1xyXG5cdFx0fSk7XHJcblxyXG5cdFx0cmV0dXJuIGVsZW1lbnQ7XHJcblx0fVxyXG5cclxuXHRmdW5jdGlvbiByZW1vdmVQaXBzICggKSB7XHJcblx0XHRpZiAoIHNjb3BlX1BpcHMgKSB7XHJcblx0XHRcdHJlbW92ZUVsZW1lbnQoc2NvcGVfUGlwcyk7XHJcblx0XHRcdHNjb3BlX1BpcHMgPSBudWxsO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gcGlwcyAoIGdyaWQgKSB7XHJcblxyXG5cdFx0Ly8gRml4ICM2NjlcclxuXHRcdHJlbW92ZVBpcHMoKTtcclxuXHJcblx0XHR2YXIgbW9kZSA9IGdyaWQubW9kZTtcclxuXHRcdHZhciBkZW5zaXR5ID0gZ3JpZC5kZW5zaXR5IHx8IDE7XHJcblx0XHR2YXIgZmlsdGVyID0gZ3JpZC5maWx0ZXIgfHwgZmFsc2U7XHJcblx0XHR2YXIgdmFsdWVzID0gZ3JpZC52YWx1ZXMgfHwgZmFsc2U7XHJcblx0XHR2YXIgc3RlcHBlZCA9IGdyaWQuc3RlcHBlZCB8fCBmYWxzZTtcclxuXHRcdHZhciBncm91cCA9IGdldEdyb3VwKCBtb2RlLCB2YWx1ZXMsIHN0ZXBwZWQgKTtcclxuXHRcdHZhciBzcHJlYWQgPSBnZW5lcmF0ZVNwcmVhZCggZGVuc2l0eSwgbW9kZSwgZ3JvdXAgKTtcclxuXHRcdHZhciBmb3JtYXQgPSBncmlkLmZvcm1hdCB8fCB7XHJcblx0XHRcdHRvOiBNYXRoLnJvdW5kXHJcblx0XHR9O1xyXG5cclxuXHRcdHNjb3BlX1BpcHMgPSBzY29wZV9UYXJnZXQuYXBwZW5kQ2hpbGQoYWRkTWFya2luZyhcclxuXHRcdFx0c3ByZWFkLFxyXG5cdFx0XHRmaWx0ZXIsXHJcblx0XHRcdGZvcm1hdFxyXG5cdFx0KSk7XHJcblxyXG5cdFx0cmV0dXJuIHNjb3BlX1BpcHM7XHJcblx0fVxyXG5cclxuLyohIEluIHRoaXMgZmlsZTogQnJvd3NlciBldmVudHMgKG5vdCBzbGlkZXIgZXZlbnRzIGxpa2Ugc2xpZGUsIGNoYW5nZSk7ICovXHJcblxyXG5cdC8vIFNob3J0aGFuZCBmb3IgYmFzZSBkaW1lbnNpb25zLlxyXG5cdGZ1bmN0aW9uIGJhc2VTaXplICggKSB7XHJcblx0XHR2YXIgcmVjdCA9IHNjb3BlX0Jhc2UuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XHJcblx0XHR2YXIgYWx0ID0gJ29mZnNldCcgKyBbJ1dpZHRoJywgJ0hlaWdodCddW29wdGlvbnMub3J0XTtcclxuXHRcdHJldHVybiBvcHRpb25zLm9ydCA9PT0gMCA/IChyZWN0LndpZHRofHxzY29wZV9CYXNlW2FsdF0pIDogKHJlY3QuaGVpZ2h0fHxzY29wZV9CYXNlW2FsdF0pO1xyXG5cdH1cclxuXHJcblx0Ly8gSGFuZGxlciBmb3IgYXR0YWNoaW5nIGV2ZW50cyB0cm91Z2ggYSBwcm94eS5cclxuXHRmdW5jdGlvbiBhdHRhY2hFdmVudCAoIGV2ZW50cywgZWxlbWVudCwgY2FsbGJhY2ssIGRhdGEgKSB7XHJcblxyXG5cdFx0Ly8gVGhpcyBmdW5jdGlvbiBjYW4gYmUgdXNlZCB0byAnZmlsdGVyJyBldmVudHMgdG8gdGhlIHNsaWRlci5cclxuXHRcdC8vIGVsZW1lbnQgaXMgYSBub2RlLCBub3QgYSBub2RlTGlzdFxyXG5cclxuXHRcdHZhciBtZXRob2QgPSBmdW5jdGlvbiAoIGUgKXtcclxuXHJcblx0XHRcdGUgPSBmaXhFdmVudChlLCBkYXRhLnBhZ2VPZmZzZXQsIGRhdGEudGFyZ2V0IHx8IGVsZW1lbnQpO1xyXG5cclxuXHRcdFx0Ly8gZml4RXZlbnQgcmV0dXJucyBmYWxzZSBpZiB0aGlzIGV2ZW50IGhhcyBhIGRpZmZlcmVudCB0YXJnZXRcclxuXHRcdFx0Ly8gd2hlbiBoYW5kbGluZyAobXVsdGktKSB0b3VjaCBldmVudHM7XHJcblx0XHRcdGlmICggIWUgKSB7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBkb05vdFJlamVjdCBpcyBwYXNzZWQgYnkgYWxsIGVuZCBldmVudHMgdG8gbWFrZSBzdXJlIHJlbGVhc2VkIHRvdWNoZXNcclxuXHRcdFx0Ly8gYXJlIG5vdCByZWplY3RlZCwgbGVhdmluZyB0aGUgc2xpZGVyIFwic3R1Y2tcIiB0byB0aGUgY3Vyc29yO1xyXG5cdFx0XHRpZiAoIHNjb3BlX1RhcmdldC5oYXNBdHRyaWJ1dGUoJ2Rpc2FibGVkJykgJiYgIWRhdGEuZG9Ob3RSZWplY3QgKSB7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBTdG9wIGlmIGFuIGFjdGl2ZSAndGFwJyB0cmFuc2l0aW9uIGlzIHRha2luZyBwbGFjZS5cclxuXHRcdFx0aWYgKCBoYXNDbGFzcyhzY29wZV9UYXJnZXQsIG9wdGlvbnMuY3NzQ2xhc3Nlcy50YXApICYmICFkYXRhLmRvTm90UmVqZWN0ICkge1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gSWdub3JlIHJpZ2h0IG9yIG1pZGRsZSBjbGlja3Mgb24gc3RhcnQgIzQ1NFxyXG5cdFx0XHRpZiAoIGV2ZW50cyA9PT0gYWN0aW9ucy5zdGFydCAmJiBlLmJ1dHRvbnMgIT09IHVuZGVmaW5lZCAmJiBlLmJ1dHRvbnMgPiAxICkge1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gSWdub3JlIHJpZ2h0IG9yIG1pZGRsZSBjbGlja3Mgb24gc3RhcnQgIzQ1NFxyXG5cdFx0XHRpZiAoIGRhdGEuaG92ZXIgJiYgZS5idXR0b25zICkge1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gJ3N1cHBvcnRzUGFzc2l2ZScgaXMgb25seSB0cnVlIGlmIGEgYnJvd3NlciBhbHNvIHN1cHBvcnRzIHRvdWNoLWFjdGlvbjogbm9uZSBpbiBDU1MuXHJcblx0XHRcdC8vIGlPUyBzYWZhcmkgZG9lcyBub3QsIHNvIGl0IGRvZXNuJ3QgZ2V0IHRvIGJlbmVmaXQgZnJvbSBwYXNzaXZlIHNjcm9sbGluZy4gaU9TIGRvZXMgc3VwcG9ydFxyXG5cdFx0XHQvLyB0b3VjaC1hY3Rpb246IG1hbmlwdWxhdGlvbiwgYnV0IHRoYXQgYWxsb3dzIHBhbm5pbmcsIHdoaWNoIGJyZWFrc1xyXG5cdFx0XHQvLyBzbGlkZXJzIGFmdGVyIHpvb21pbmcvb24gbm9uLXJlc3BvbnNpdmUgcGFnZXMuXHJcblx0XHRcdC8vIFNlZTogaHR0cHM6Ly9idWdzLndlYmtpdC5vcmcvc2hvd19idWcuY2dpP2lkPTEzMzExMlxyXG5cdFx0XHRpZiAoICFzdXBwb3J0c1Bhc3NpdmUgKSB7XHJcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRlLmNhbGNQb2ludCA9IGUucG9pbnRzWyBvcHRpb25zLm9ydCBdO1xyXG5cclxuXHRcdFx0Ly8gQ2FsbCB0aGUgZXZlbnQgaGFuZGxlciB3aXRoIHRoZSBldmVudCBbIGFuZCBhZGRpdGlvbmFsIGRhdGEgXS5cclxuXHRcdFx0Y2FsbGJhY2sgKCBlLCBkYXRhICk7XHJcblx0XHR9O1xyXG5cclxuXHRcdHZhciBtZXRob2RzID0gW107XHJcblxyXG5cdFx0Ly8gQmluZCBhIGNsb3N1cmUgb24gdGhlIHRhcmdldCBmb3IgZXZlcnkgZXZlbnQgdHlwZS5cclxuXHRcdGV2ZW50cy5zcGxpdCgnICcpLmZvckVhY2goZnVuY3Rpb24oIGV2ZW50TmFtZSApe1xyXG5cdFx0XHRlbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoZXZlbnROYW1lLCBtZXRob2QsIHN1cHBvcnRzUGFzc2l2ZSA/IHsgcGFzc2l2ZTogdHJ1ZSB9IDogZmFsc2UpO1xyXG5cdFx0XHRtZXRob2RzLnB1c2goW2V2ZW50TmFtZSwgbWV0aG9kXSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHRyZXR1cm4gbWV0aG9kcztcclxuXHR9XHJcblxyXG5cdC8vIFByb3ZpZGUgYSBjbGVhbiBldmVudCB3aXRoIHN0YW5kYXJkaXplZCBvZmZzZXQgdmFsdWVzLlxyXG5cdGZ1bmN0aW9uIGZpeEV2ZW50ICggZSwgcGFnZU9mZnNldCwgZXZlbnRUYXJnZXQgKSB7XHJcblxyXG5cdFx0Ly8gRmlsdGVyIHRoZSBldmVudCB0byByZWdpc3RlciB0aGUgdHlwZSwgd2hpY2ggY2FuIGJlXHJcblx0XHQvLyB0b3VjaCwgbW91c2Ugb3IgcG9pbnRlci4gT2Zmc2V0IGNoYW5nZXMgbmVlZCB0byBiZVxyXG5cdFx0Ly8gbWFkZSBvbiBhbiBldmVudCBzcGVjaWZpYyBiYXNpcy5cclxuXHRcdHZhciB0b3VjaCA9IGUudHlwZS5pbmRleE9mKCd0b3VjaCcpID09PSAwO1xyXG5cdFx0dmFyIG1vdXNlID0gZS50eXBlLmluZGV4T2YoJ21vdXNlJykgPT09IDA7XHJcblx0XHR2YXIgcG9pbnRlciA9IGUudHlwZS5pbmRleE9mKCdwb2ludGVyJykgPT09IDA7XHJcblxyXG5cdFx0dmFyIHg7XHJcblx0XHR2YXIgeTtcclxuXHJcblx0XHQvLyBJRTEwIGltcGxlbWVudGVkIHBvaW50ZXIgZXZlbnRzIHdpdGggYSBwcmVmaXg7XHJcblx0XHRpZiAoIGUudHlwZS5pbmRleE9mKCdNU1BvaW50ZXInKSA9PT0gMCApIHtcclxuXHRcdFx0cG9pbnRlciA9IHRydWU7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gSW4gdGhlIGV2ZW50IHRoYXQgbXVsdGl0b3VjaCBpcyBhY3RpdmF0ZWQsIHRoZSBvbmx5IHRoaW5nIG9uZSBoYW5kbGUgc2hvdWxkIGJlIGNvbmNlcm5lZFxyXG5cdFx0Ly8gYWJvdXQgaXMgdGhlIHRvdWNoZXMgdGhhdCBvcmlnaW5hdGVkIG9uIHRvcCBvZiBpdC5cclxuXHRcdGlmICggdG91Y2ggKSB7XHJcblxyXG5cdFx0XHQvLyBSZXR1cm5zIHRydWUgaWYgYSB0b3VjaCBvcmlnaW5hdGVkIG9uIHRoZSB0YXJnZXQuXHJcblx0XHRcdHZhciBpc1RvdWNoT25UYXJnZXQgPSBmdW5jdGlvbiAoY2hlY2tUb3VjaCkge1xyXG5cdFx0XHRcdHJldHVybiBjaGVja1RvdWNoLnRhcmdldCA9PT0gZXZlbnRUYXJnZXQgfHwgZXZlbnRUYXJnZXQuY29udGFpbnMoY2hlY2tUb3VjaC50YXJnZXQpO1xyXG5cdFx0XHR9O1xyXG5cclxuXHRcdFx0Ly8gSW4gdGhlIGNhc2Ugb2YgdG91Y2hzdGFydCBldmVudHMsIHdlIG5lZWQgdG8gbWFrZSBzdXJlIHRoZXJlIGlzIHN0aWxsIG5vIG1vcmUgdGhhbiBvbmVcclxuXHRcdFx0Ly8gdG91Y2ggb24gdGhlIHRhcmdldCBzbyB3ZSBsb29rIGFtb25nc3QgYWxsIHRvdWNoZXMuXHJcblx0XHRcdGlmIChlLnR5cGUgPT09ICd0b3VjaHN0YXJ0Jykge1xyXG5cclxuXHRcdFx0XHR2YXIgdGFyZ2V0VG91Y2hlcyA9IEFycmF5LnByb3RvdHlwZS5maWx0ZXIuY2FsbChlLnRvdWNoZXMsIGlzVG91Y2hPblRhcmdldCk7XHJcblxyXG5cdFx0XHRcdC8vIERvIG5vdCBzdXBwb3J0IG1vcmUgdGhhbiBvbmUgdG91Y2ggcGVyIGhhbmRsZS5cclxuXHRcdFx0XHRpZiAoIHRhcmdldFRvdWNoZXMubGVuZ3RoID4gMSApIHtcclxuXHRcdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdHggPSB0YXJnZXRUb3VjaGVzWzBdLnBhZ2VYO1xyXG5cdFx0XHRcdHkgPSB0YXJnZXRUb3VjaGVzWzBdLnBhZ2VZO1xyXG5cclxuXHRcdFx0fSBlbHNlIHtcclxuXHJcblx0XHRcdFx0Ly8gSW4gdGhlIG90aGVyIGNhc2VzLCBmaW5kIG9uIGNoYW5nZWRUb3VjaGVzIGlzIGVub3VnaC5cclxuXHRcdFx0XHR2YXIgdGFyZ2V0VG91Y2ggPSBBcnJheS5wcm90b3R5cGUuZmluZC5jYWxsKGUuY2hhbmdlZFRvdWNoZXMsIGlzVG91Y2hPblRhcmdldCk7XHJcblxyXG5cdFx0XHRcdC8vIENhbmNlbCBpZiB0aGUgdGFyZ2V0IHRvdWNoIGhhcyBub3QgbW92ZWQuXHJcblx0XHRcdFx0aWYgKCAhdGFyZ2V0VG91Y2ggKSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHR4ID0gdGFyZ2V0VG91Y2gucGFnZVg7XHJcblx0XHRcdFx0eSA9IHRhcmdldFRvdWNoLnBhZ2VZO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cGFnZU9mZnNldCA9IHBhZ2VPZmZzZXQgfHwgZ2V0UGFnZU9mZnNldChzY29wZV9Eb2N1bWVudCk7XHJcblxyXG5cdFx0aWYgKCBtb3VzZSB8fCBwb2ludGVyICkge1xyXG5cdFx0XHR4ID0gZS5jbGllbnRYICsgcGFnZU9mZnNldC54O1xyXG5cdFx0XHR5ID0gZS5jbGllbnRZICsgcGFnZU9mZnNldC55O1xyXG5cdFx0fVxyXG5cclxuXHRcdGUucGFnZU9mZnNldCA9IHBhZ2VPZmZzZXQ7XHJcblx0XHRlLnBvaW50cyA9IFt4LCB5XTtcclxuXHRcdGUuY3Vyc29yID0gbW91c2UgfHwgcG9pbnRlcjsgLy8gRml4ICM0MzVcclxuXHJcblx0XHRyZXR1cm4gZTtcclxuXHR9XHJcblxyXG5cdC8vIFRyYW5zbGF0ZSBhIGNvb3JkaW5hdGUgaW4gdGhlIGRvY3VtZW50IHRvIGEgcGVyY2VudGFnZSBvbiB0aGUgc2xpZGVyXHJcblx0ZnVuY3Rpb24gY2FsY1BvaW50VG9QZXJjZW50YWdlICggY2FsY1BvaW50ICkge1xyXG5cdFx0dmFyIGxvY2F0aW9uID0gY2FsY1BvaW50IC0gb2Zmc2V0KHNjb3BlX0Jhc2UsIG9wdGlvbnMub3J0KTtcclxuXHRcdHZhciBwcm9wb3NhbCA9ICggbG9jYXRpb24gKiAxMDAgKSAvIGJhc2VTaXplKCk7XHJcblxyXG5cdFx0Ly8gQ2xhbXAgcHJvcG9zYWwgYmV0d2VlbiAwJSBhbmQgMTAwJVxyXG5cdFx0Ly8gT3V0LW9mLWJvdW5kIGNvb3JkaW5hdGVzIG1heSBvY2N1ciB3aGVuIC5ub1VpLWJhc2UgcHNldWRvLWVsZW1lbnRzXHJcblx0XHQvLyBhcmUgdXNlZCAoZS5nLiBjb250YWluZWQgaGFuZGxlcyBmZWF0dXJlKVxyXG5cdFx0cHJvcG9zYWwgPSBsaW1pdChwcm9wb3NhbCk7XHJcblxyXG5cdFx0cmV0dXJuIG9wdGlvbnMuZGlyID8gMTAwIC0gcHJvcG9zYWwgOiBwcm9wb3NhbDtcclxuXHR9XHJcblxyXG5cdC8vIEZpbmQgaGFuZGxlIGNsb3Nlc3QgdG8gYSBjZXJ0YWluIHBlcmNlbnRhZ2Ugb24gdGhlIHNsaWRlclxyXG5cdGZ1bmN0aW9uIGdldENsb3Nlc3RIYW5kbGUgKCBwcm9wb3NhbCApIHtcclxuXHJcblx0XHR2YXIgY2xvc2VzdCA9IDEwMDtcclxuXHRcdHZhciBoYW5kbGVOdW1iZXIgPSBmYWxzZTtcclxuXHJcblx0XHRzY29wZV9IYW5kbGVzLmZvckVhY2goZnVuY3Rpb24oaGFuZGxlLCBpbmRleCl7XHJcblxyXG5cdFx0XHQvLyBEaXNhYmxlZCBoYW5kbGVzIGFyZSBpZ25vcmVkXHJcblx0XHRcdGlmICggaGFuZGxlLmhhc0F0dHJpYnV0ZSgnZGlzYWJsZWQnKSApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciBwb3MgPSBNYXRoLmFicyhzY29wZV9Mb2NhdGlvbnNbaW5kZXhdIC0gcHJvcG9zYWwpO1xyXG5cclxuXHRcdFx0aWYgKCBwb3MgPCBjbG9zZXN0IHx8IChwb3MgPT09IDEwMCAmJiBjbG9zZXN0ID09PSAxMDApICkge1xyXG5cdFx0XHRcdGhhbmRsZU51bWJlciA9IGluZGV4O1xyXG5cdFx0XHRcdGNsb3Nlc3QgPSBwb3M7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cclxuXHRcdHJldHVybiBoYW5kbGVOdW1iZXI7XHJcblx0fVxyXG5cclxuXHQvLyBGaXJlICdlbmQnIHdoZW4gYSBtb3VzZSBvciBwZW4gbGVhdmVzIHRoZSBkb2N1bWVudC5cclxuXHRmdW5jdGlvbiBkb2N1bWVudExlYXZlICggZXZlbnQsIGRhdGEgKSB7XHJcblx0XHRpZiAoIGV2ZW50LnR5cGUgPT09IFwibW91c2VvdXRcIiAmJiBldmVudC50YXJnZXQubm9kZU5hbWUgPT09IFwiSFRNTFwiICYmIGV2ZW50LnJlbGF0ZWRUYXJnZXQgPT09IG51bGwgKXtcclxuXHRcdFx0ZXZlbnRFbmQgKGV2ZW50LCBkYXRhKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdC8vIEhhbmRsZSBtb3ZlbWVudCBvbiBkb2N1bWVudCBmb3IgaGFuZGxlIGFuZCByYW5nZSBkcmFnLlxyXG5cdGZ1bmN0aW9uIGV2ZW50TW92ZSAoIGV2ZW50LCBkYXRhICkge1xyXG5cclxuXHRcdC8vIEZpeCAjNDk4XHJcblx0XHQvLyBDaGVjayB2YWx1ZSBvZiAuYnV0dG9ucyBpbiAnc3RhcnQnIHRvIHdvcmsgYXJvdW5kIGEgYnVnIGluIElFMTAgbW9iaWxlIChkYXRhLmJ1dHRvbnNQcm9wZXJ0eSkuXHJcblx0XHQvLyBodHRwczovL2Nvbm5lY3QubWljcm9zb2Z0LmNvbS9JRS9mZWVkYmFjay9kZXRhaWxzLzkyNzAwNS9tb2JpbGUtaWUxMC13aW5kb3dzLXBob25lLWJ1dHRvbnMtcHJvcGVydHktb2YtcG9pbnRlcm1vdmUtZXZlbnQtYWx3YXlzLXplcm9cclxuXHRcdC8vIElFOSBoYXMgLmJ1dHRvbnMgYW5kIC53aGljaCB6ZXJvIG9uIG1vdXNlbW92ZS5cclxuXHRcdC8vIEZpcmVmb3ggYnJlYWtzIHRoZSBzcGVjIE1ETiBkZWZpbmVzLlxyXG5cdFx0aWYgKCBuYXZpZ2F0b3IuYXBwVmVyc2lvbi5pbmRleE9mKFwiTVNJRSA5XCIpID09PSAtMSAmJiBldmVudC5idXR0b25zID09PSAwICYmIGRhdGEuYnV0dG9uc1Byb3BlcnR5ICE9PSAwICkge1xyXG5cdFx0XHRyZXR1cm4gZXZlbnRFbmQoZXZlbnQsIGRhdGEpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIENoZWNrIGlmIHdlIGFyZSBtb3ZpbmcgdXAgb3IgZG93blxyXG5cdFx0dmFyIG1vdmVtZW50ID0gKG9wdGlvbnMuZGlyID8gLTEgOiAxKSAqIChldmVudC5jYWxjUG9pbnQgLSBkYXRhLnN0YXJ0Q2FsY1BvaW50KTtcclxuXHJcblx0XHQvLyBDb252ZXJ0IHRoZSBtb3ZlbWVudCBpbnRvIGEgcGVyY2VudGFnZSBvZiB0aGUgc2xpZGVyIHdpZHRoL2hlaWdodFxyXG5cdFx0dmFyIHByb3Bvc2FsID0gKG1vdmVtZW50ICogMTAwKSAvIGRhdGEuYmFzZVNpemU7XHJcblxyXG5cdFx0bW92ZUhhbmRsZXMobW92ZW1lbnQgPiAwLCBwcm9wb3NhbCwgZGF0YS5sb2NhdGlvbnMsIGRhdGEuaGFuZGxlTnVtYmVycyk7XHJcblx0fVxyXG5cclxuXHQvLyBVbmJpbmQgbW92ZSBldmVudHMgb24gZG9jdW1lbnQsIGNhbGwgY2FsbGJhY2tzLlxyXG5cdGZ1bmN0aW9uIGV2ZW50RW5kICggZXZlbnQsIGRhdGEgKSB7XHJcblxyXG5cdFx0Ly8gVGhlIGhhbmRsZSBpcyBubyBsb25nZXIgYWN0aXZlLCBzbyByZW1vdmUgdGhlIGNsYXNzLlxyXG5cdFx0aWYgKCBkYXRhLmhhbmRsZSApIHtcclxuXHRcdFx0cmVtb3ZlQ2xhc3MoZGF0YS5oYW5kbGUsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5hY3RpdmUpO1xyXG5cdFx0XHRzY29wZV9BY3RpdmVIYW5kbGVzQ291bnQgLT0gMTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBVbmJpbmQgdGhlIG1vdmUgYW5kIGVuZCBldmVudHMsIHdoaWNoIGFyZSBhZGRlZCBvbiAnc3RhcnQnLlxyXG5cdFx0ZGF0YS5saXN0ZW5lcnMuZm9yRWFjaChmdW5jdGlvbiggYyApIHtcclxuXHRcdFx0c2NvcGVfRG9jdW1lbnRFbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoY1swXSwgY1sxXSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHRpZiAoIHNjb3BlX0FjdGl2ZUhhbmRsZXNDb3VudCA9PT0gMCApIHtcclxuXHRcdFx0Ly8gUmVtb3ZlIGRyYWdnaW5nIGNsYXNzLlxyXG5cdFx0XHRyZW1vdmVDbGFzcyhzY29wZV9UYXJnZXQsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5kcmFnKTtcclxuXHRcdFx0c2V0WmluZGV4KCk7XHJcblxyXG5cdFx0XHQvLyBSZW1vdmUgY3Vyc29yIHN0eWxlcyBhbmQgdGV4dC1zZWxlY3Rpb24gZXZlbnRzIGJvdW5kIHRvIHRoZSBib2R5LlxyXG5cdFx0XHRpZiAoIGV2ZW50LmN1cnNvciApIHtcclxuXHRcdFx0XHRzY29wZV9Cb2R5LnN0eWxlLmN1cnNvciA9ICcnO1xyXG5cdFx0XHRcdHNjb3BlX0JvZHkucmVtb3ZlRXZlbnRMaXN0ZW5lcignc2VsZWN0c3RhcnQnLCBwcmV2ZW50RGVmYXVsdCk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRkYXRhLmhhbmRsZU51bWJlcnMuZm9yRWFjaChmdW5jdGlvbihoYW5kbGVOdW1iZXIpe1xyXG5cdFx0XHRmaXJlRXZlbnQoJ2NoYW5nZScsIGhhbmRsZU51bWJlcik7XHJcblx0XHRcdGZpcmVFdmVudCgnc2V0JywgaGFuZGxlTnVtYmVyKTtcclxuXHRcdFx0ZmlyZUV2ZW50KCdlbmQnLCBoYW5kbGVOdW1iZXIpO1xyXG5cdFx0fSk7XHJcblx0fVxyXG5cclxuXHQvLyBCaW5kIG1vdmUgZXZlbnRzIG9uIGRvY3VtZW50LlxyXG5cdGZ1bmN0aW9uIGV2ZW50U3RhcnQgKCBldmVudCwgZGF0YSApIHtcclxuXHJcblx0XHR2YXIgaGFuZGxlO1xyXG5cdFx0aWYgKCBkYXRhLmhhbmRsZU51bWJlcnMubGVuZ3RoID09PSAxICkge1xyXG5cclxuXHRcdFx0dmFyIGhhbmRsZU9yaWdpbiA9IHNjb3BlX0hhbmRsZXNbZGF0YS5oYW5kbGVOdW1iZXJzWzBdXTtcclxuXHJcblx0XHRcdC8vIElnbm9yZSAnZGlzYWJsZWQnIGhhbmRsZXNcclxuXHRcdFx0aWYgKCBoYW5kbGVPcmlnaW4uaGFzQXR0cmlidXRlKCdkaXNhYmxlZCcpICkge1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aGFuZGxlID0gaGFuZGxlT3JpZ2luLmNoaWxkcmVuWzBdO1xyXG5cdFx0XHRzY29wZV9BY3RpdmVIYW5kbGVzQ291bnQgKz0gMTtcclxuXHJcblx0XHRcdC8vIE1hcmsgdGhlIGhhbmRsZSBhcyAnYWN0aXZlJyBzbyBpdCBjYW4gYmUgc3R5bGVkLlxyXG5cdFx0XHRhZGRDbGFzcyhoYW5kbGUsIG9wdGlvbnMuY3NzQ2xhc3Nlcy5hY3RpdmUpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIEEgZHJhZyBzaG91bGQgbmV2ZXIgcHJvcGFnYXRlIHVwIHRvIHRoZSAndGFwJyBldmVudC5cclxuXHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xyXG5cclxuXHRcdC8vIFJlY29yZCB0aGUgZXZlbnQgbGlzdGVuZXJzLlxyXG5cdFx0dmFyIGxpc3RlbmVycyA9IFtdO1xyXG5cclxuXHRcdC8vIEF0dGFjaCB0aGUgbW92ZSBhbmQgZW5kIGV2ZW50cy5cclxuXHRcdHZhciBtb3ZlRXZlbnQgPSBhdHRhY2hFdmVudChhY3Rpb25zLm1vdmUsIHNjb3BlX0RvY3VtZW50RWxlbWVudCwgZXZlbnRNb3ZlLCB7XHJcblx0XHRcdC8vIFRoZSBldmVudCB0YXJnZXQgaGFzIGNoYW5nZWQgc28gd2UgbmVlZCB0byBwcm9wYWdhdGUgdGhlIG9yaWdpbmFsIG9uZSBzbyB0aGF0IHdlIGtlZXBcclxuXHRcdFx0Ly8gcmVseWluZyBvbiBpdCB0byBleHRyYWN0IHRhcmdldCB0b3VjaGVzLlxyXG5cdFx0XHR0YXJnZXQ6IGV2ZW50LnRhcmdldCxcclxuXHRcdFx0aGFuZGxlOiBoYW5kbGUsXHJcblx0XHRcdGxpc3RlbmVyczogbGlzdGVuZXJzLFxyXG5cdFx0XHRzdGFydENhbGNQb2ludDogZXZlbnQuY2FsY1BvaW50LFxyXG5cdFx0XHRiYXNlU2l6ZTogYmFzZVNpemUoKSxcclxuXHRcdFx0cGFnZU9mZnNldDogZXZlbnQucGFnZU9mZnNldCxcclxuXHRcdFx0aGFuZGxlTnVtYmVyczogZGF0YS5oYW5kbGVOdW1iZXJzLFxyXG5cdFx0XHRidXR0b25zUHJvcGVydHk6IGV2ZW50LmJ1dHRvbnMsXHJcblx0XHRcdGxvY2F0aW9uczogc2NvcGVfTG9jYXRpb25zLnNsaWNlKClcclxuXHRcdH0pO1xyXG5cclxuXHRcdHZhciBlbmRFdmVudCA9IGF0dGFjaEV2ZW50KGFjdGlvbnMuZW5kLCBzY29wZV9Eb2N1bWVudEVsZW1lbnQsIGV2ZW50RW5kLCB7XHJcblx0XHRcdHRhcmdldDogZXZlbnQudGFyZ2V0LFxyXG5cdFx0XHRoYW5kbGU6IGhhbmRsZSxcclxuXHRcdFx0bGlzdGVuZXJzOiBsaXN0ZW5lcnMsXHJcblx0XHRcdGRvTm90UmVqZWN0OiB0cnVlLFxyXG5cdFx0XHRoYW5kbGVOdW1iZXJzOiBkYXRhLmhhbmRsZU51bWJlcnNcclxuXHRcdH0pO1xyXG5cclxuXHRcdHZhciBvdXRFdmVudCA9IGF0dGFjaEV2ZW50KFwibW91c2VvdXRcIiwgc2NvcGVfRG9jdW1lbnRFbGVtZW50LCBkb2N1bWVudExlYXZlLCB7XHJcblx0XHRcdHRhcmdldDogZXZlbnQudGFyZ2V0LFxyXG5cdFx0XHRoYW5kbGU6IGhhbmRsZSxcclxuXHRcdFx0bGlzdGVuZXJzOiBsaXN0ZW5lcnMsXHJcblx0XHRcdGRvTm90UmVqZWN0OiB0cnVlLFxyXG5cdFx0XHRoYW5kbGVOdW1iZXJzOiBkYXRhLmhhbmRsZU51bWJlcnNcclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIFdlIHdhbnQgdG8gbWFrZSBzdXJlIHdlIHB1c2hlZCB0aGUgbGlzdGVuZXJzIGluIHRoZSBsaXN0ZW5lciBsaXN0IHJhdGhlciB0aGFuIGNyZWF0aW5nXHJcblx0XHQvLyBhIG5ldyBvbmUgYXMgaXQgaGFzIGFscmVhZHkgYmVlbiBwYXNzZWQgdG8gdGhlIGV2ZW50IGhhbmRsZXJzLlxyXG5cdFx0bGlzdGVuZXJzLnB1c2guYXBwbHkobGlzdGVuZXJzLCBtb3ZlRXZlbnQuY29uY2F0KGVuZEV2ZW50LCBvdXRFdmVudCkpO1xyXG5cclxuXHRcdC8vIFRleHQgc2VsZWN0aW9uIGlzbid0IGFuIGlzc3VlIG9uIHRvdWNoIGRldmljZXMsXHJcblx0XHQvLyBzbyBhZGRpbmcgY3Vyc29yIHN0eWxlcyBjYW4gYmUgc2tpcHBlZC5cclxuXHRcdGlmICggZXZlbnQuY3Vyc29yICkge1xyXG5cclxuXHRcdFx0Ly8gUHJldmVudCB0aGUgJ0knIGN1cnNvciBhbmQgZXh0ZW5kIHRoZSByYW5nZS1kcmFnIGN1cnNvci5cclxuXHRcdFx0c2NvcGVfQm9keS5zdHlsZS5jdXJzb3IgPSBnZXRDb21wdXRlZFN0eWxlKGV2ZW50LnRhcmdldCkuY3Vyc29yO1xyXG5cclxuXHRcdFx0Ly8gTWFyayB0aGUgdGFyZ2V0IHdpdGggYSBkcmFnZ2luZyBzdGF0ZS5cclxuXHRcdFx0aWYgKCBzY29wZV9IYW5kbGVzLmxlbmd0aCA+IDEgKSB7XHJcblx0XHRcdFx0YWRkQ2xhc3Moc2NvcGVfVGFyZ2V0LCBvcHRpb25zLmNzc0NsYXNzZXMuZHJhZyk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIFByZXZlbnQgdGV4dCBzZWxlY3Rpb24gd2hlbiBkcmFnZ2luZyB0aGUgaGFuZGxlcy5cclxuXHRcdFx0Ly8gSW4gbm9VaVNsaWRlciA8PSA5LjIuMCwgdGhpcyB3YXMgaGFuZGxlZCBieSBjYWxsaW5nIHByZXZlbnREZWZhdWx0IG9uIG1vdXNlL3RvdWNoIHN0YXJ0L21vdmUsXHJcblx0XHRcdC8vIHdoaWNoIGlzIHNjcm9sbCBibG9ja2luZy4gVGhlIHNlbGVjdHN0YXJ0IGV2ZW50IGlzIHN1cHBvcnRlZCBieSBGaXJlRm94IHN0YXJ0aW5nIGZyb20gdmVyc2lvbiA1MixcclxuXHRcdFx0Ly8gbWVhbmluZyB0aGUgb25seSBob2xkb3V0IGlzIGlPUyBTYWZhcmkuIFRoaXMgZG9lc24ndCBtYXR0ZXI6IHRleHQgc2VsZWN0aW9uIGlzbid0IHRyaWdnZXJlZCB0aGVyZS5cclxuXHRcdFx0Ly8gVGhlICdjdXJzb3InIGZsYWcgaXMgZmFsc2UuXHJcblx0XHRcdC8vIFNlZTogaHR0cDovL2Nhbml1c2UuY29tLyNzZWFyY2g9c2VsZWN0c3RhcnRcclxuXHRcdFx0c2NvcGVfQm9keS5hZGRFdmVudExpc3RlbmVyKCdzZWxlY3RzdGFydCcsIHByZXZlbnREZWZhdWx0LCBmYWxzZSk7XHJcblx0XHR9XHJcblxyXG5cdFx0ZGF0YS5oYW5kbGVOdW1iZXJzLmZvckVhY2goZnVuY3Rpb24oaGFuZGxlTnVtYmVyKXtcclxuXHRcdFx0ZmlyZUV2ZW50KCdzdGFydCcsIGhhbmRsZU51bWJlcik7XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cdC8vIE1vdmUgY2xvc2VzdCBoYW5kbGUgdG8gdGFwcGVkIGxvY2F0aW9uLlxyXG5cdGZ1bmN0aW9uIGV2ZW50VGFwICggZXZlbnQgKSB7XHJcblxyXG5cdFx0Ly8gVGhlIHRhcCBldmVudCBzaG91bGRuJ3QgcHJvcGFnYXRlIHVwXHJcblx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcclxuXHJcblx0XHR2YXIgcHJvcG9zYWwgPSBjYWxjUG9pbnRUb1BlcmNlbnRhZ2UoZXZlbnQuY2FsY1BvaW50KTtcclxuXHRcdHZhciBoYW5kbGVOdW1iZXIgPSBnZXRDbG9zZXN0SGFuZGxlKHByb3Bvc2FsKTtcclxuXHJcblx0XHQvLyBUYWNrbGUgdGhlIGNhc2UgdGhhdCBhbGwgaGFuZGxlcyBhcmUgJ2Rpc2FibGVkJy5cclxuXHRcdGlmICggaGFuZGxlTnVtYmVyID09PSBmYWxzZSApIHtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIEZsYWcgdGhlIHNsaWRlciBhcyBpdCBpcyBub3cgaW4gYSB0cmFuc2l0aW9uYWwgc3RhdGUuXHJcblx0XHQvLyBUcmFuc2l0aW9uIHRha2VzIGEgY29uZmlndXJhYmxlIGFtb3VudCBvZiBtcyAoZGVmYXVsdCAzMDApLiBSZS1lbmFibGUgdGhlIHNsaWRlciBhZnRlciB0aGF0LlxyXG5cdFx0aWYgKCAhb3B0aW9ucy5ldmVudHMuc25hcCApIHtcclxuXHRcdFx0YWRkQ2xhc3NGb3Ioc2NvcGVfVGFyZ2V0LCBvcHRpb25zLmNzc0NsYXNzZXMudGFwLCBvcHRpb25zLmFuaW1hdGlvbkR1cmF0aW9uKTtcclxuXHRcdH1cclxuXHJcblx0XHRzZXRIYW5kbGUoaGFuZGxlTnVtYmVyLCBwcm9wb3NhbCwgdHJ1ZSwgdHJ1ZSk7XHJcblxyXG5cdFx0c2V0WmluZGV4KCk7XHJcblxyXG5cdFx0ZmlyZUV2ZW50KCdzbGlkZScsIGhhbmRsZU51bWJlciwgdHJ1ZSk7XHJcblx0XHRmaXJlRXZlbnQoJ3VwZGF0ZScsIGhhbmRsZU51bWJlciwgdHJ1ZSk7XHJcblx0XHRmaXJlRXZlbnQoJ2NoYW5nZScsIGhhbmRsZU51bWJlciwgdHJ1ZSk7XHJcblx0XHRmaXJlRXZlbnQoJ3NldCcsIGhhbmRsZU51bWJlciwgdHJ1ZSk7XHJcblxyXG5cdFx0aWYgKCBvcHRpb25zLmV2ZW50cy5zbmFwICkge1xyXG5cdFx0XHRldmVudFN0YXJ0KGV2ZW50LCB7IGhhbmRsZU51bWJlcnM6IFtoYW5kbGVOdW1iZXJdIH0pO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0Ly8gRmlyZXMgYSAnaG92ZXInIGV2ZW50IGZvciBhIGhvdmVyZWQgbW91c2UvcGVuIHBvc2l0aW9uLlxyXG5cdGZ1bmN0aW9uIGV2ZW50SG92ZXIgKCBldmVudCApIHtcclxuXHJcblx0XHR2YXIgcHJvcG9zYWwgPSBjYWxjUG9pbnRUb1BlcmNlbnRhZ2UoZXZlbnQuY2FsY1BvaW50KTtcclxuXHJcblx0XHR2YXIgdG8gPSBzY29wZV9TcGVjdHJ1bS5nZXRTdGVwKHByb3Bvc2FsKTtcclxuXHRcdHZhciB2YWx1ZSA9IHNjb3BlX1NwZWN0cnVtLmZyb21TdGVwcGluZyh0byk7XHJcblxyXG5cdFx0T2JqZWN0LmtleXMoc2NvcGVfRXZlbnRzKS5mb3JFYWNoKGZ1bmN0aW9uKCB0YXJnZXRFdmVudCApIHtcclxuXHRcdFx0aWYgKCAnaG92ZXInID09PSB0YXJnZXRFdmVudC5zcGxpdCgnLicpWzBdICkge1xyXG5cdFx0XHRcdHNjb3BlX0V2ZW50c1t0YXJnZXRFdmVudF0uZm9yRWFjaChmdW5jdGlvbiggY2FsbGJhY2sgKSB7XHJcblx0XHRcdFx0XHRjYWxsYmFjay5jYWxsKCBzY29wZV9TZWxmLCB2YWx1ZSApO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cdC8vIEF0dGFjaCBldmVudHMgdG8gc2V2ZXJhbCBzbGlkZXIgcGFydHMuXHJcblx0ZnVuY3Rpb24gYmluZFNsaWRlckV2ZW50cyAoIGJlaGF2aW91ciApIHtcclxuXHJcblx0XHQvLyBBdHRhY2ggdGhlIHN0YW5kYXJkIGRyYWcgZXZlbnQgdG8gdGhlIGhhbmRsZXMuXHJcblx0XHRpZiAoICFiZWhhdmlvdXIuZml4ZWQgKSB7XHJcblxyXG5cdFx0XHRzY29wZV9IYW5kbGVzLmZvckVhY2goZnVuY3Rpb24oIGhhbmRsZSwgaW5kZXggKXtcclxuXHJcblx0XHRcdFx0Ly8gVGhlc2UgZXZlbnRzIGFyZSBvbmx5IGJvdW5kIHRvIHRoZSB2aXN1YWwgaGFuZGxlXHJcblx0XHRcdFx0Ly8gZWxlbWVudCwgbm90IHRoZSAncmVhbCcgb3JpZ2luIGVsZW1lbnQuXHJcblx0XHRcdFx0YXR0YWNoRXZlbnQgKCBhY3Rpb25zLnN0YXJ0LCBoYW5kbGUuY2hpbGRyZW5bMF0sIGV2ZW50U3RhcnQsIHtcclxuXHRcdFx0XHRcdGhhbmRsZU51bWJlcnM6IFtpbmRleF1cclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gQXR0YWNoIHRoZSB0YXAgZXZlbnQgdG8gdGhlIHNsaWRlciBiYXNlLlxyXG5cdFx0aWYgKCBiZWhhdmlvdXIudGFwICkge1xyXG5cdFx0XHRhdHRhY2hFdmVudCAoYWN0aW9ucy5zdGFydCwgc2NvcGVfQmFzZSwgZXZlbnRUYXAsIHt9KTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBGaXJlIGhvdmVyIGV2ZW50c1xyXG5cdFx0aWYgKCBiZWhhdmlvdXIuaG92ZXIgKSB7XHJcblx0XHRcdGF0dGFjaEV2ZW50IChhY3Rpb25zLm1vdmUsIHNjb3BlX0Jhc2UsIGV2ZW50SG92ZXIsIHsgaG92ZXI6IHRydWUgfSk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gTWFrZSB0aGUgcmFuZ2UgZHJhZ2dhYmxlLlxyXG5cdFx0aWYgKCBiZWhhdmlvdXIuZHJhZyApe1xyXG5cclxuXHRcdFx0c2NvcGVfQ29ubmVjdHMuZm9yRWFjaChmdW5jdGlvbiggY29ubmVjdCwgaW5kZXggKXtcclxuXHJcblx0XHRcdFx0aWYgKCBjb25uZWN0ID09PSBmYWxzZSB8fCBpbmRleCA9PT0gMCB8fCBpbmRleCA9PT0gc2NvcGVfQ29ubmVjdHMubGVuZ3RoIC0gMSApIHtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdHZhciBoYW5kbGVCZWZvcmUgPSBzY29wZV9IYW5kbGVzW2luZGV4IC0gMV07XHJcblx0XHRcdFx0dmFyIGhhbmRsZUFmdGVyID0gc2NvcGVfSGFuZGxlc1tpbmRleF07XHJcblx0XHRcdFx0dmFyIGV2ZW50SG9sZGVycyA9IFtjb25uZWN0XTtcclxuXHJcblx0XHRcdFx0YWRkQ2xhc3MoY29ubmVjdCwgb3B0aW9ucy5jc3NDbGFzc2VzLmRyYWdnYWJsZSk7XHJcblxyXG5cdFx0XHRcdC8vIFdoZW4gdGhlIHJhbmdlIGlzIGZpeGVkLCB0aGUgZW50aXJlIHJhbmdlIGNhblxyXG5cdFx0XHRcdC8vIGJlIGRyYWdnZWQgYnkgdGhlIGhhbmRsZXMuIFRoZSBoYW5kbGUgaW4gdGhlIGZpcnN0XHJcblx0XHRcdFx0Ly8gb3JpZ2luIHdpbGwgcHJvcGFnYXRlIHRoZSBzdGFydCBldmVudCB1cHdhcmQsXHJcblx0XHRcdFx0Ly8gYnV0IGl0IG5lZWRzIHRvIGJlIGJvdW5kIG1hbnVhbGx5IG9uIHRoZSBvdGhlci5cclxuXHRcdFx0XHRpZiAoIGJlaGF2aW91ci5maXhlZCApIHtcclxuXHRcdFx0XHRcdGV2ZW50SG9sZGVycy5wdXNoKGhhbmRsZUJlZm9yZS5jaGlsZHJlblswXSk7XHJcblx0XHRcdFx0XHRldmVudEhvbGRlcnMucHVzaChoYW5kbGVBZnRlci5jaGlsZHJlblswXSk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRldmVudEhvbGRlcnMuZm9yRWFjaChmdW5jdGlvbiggZXZlbnRIb2xkZXIgKSB7XHJcblx0XHRcdFx0XHRhdHRhY2hFdmVudCAoIGFjdGlvbnMuc3RhcnQsIGV2ZW50SG9sZGVyLCBldmVudFN0YXJ0LCB7XHJcblx0XHRcdFx0XHRcdGhhbmRsZXM6IFtoYW5kbGVCZWZvcmUsIGhhbmRsZUFmdGVyXSxcclxuXHRcdFx0XHRcdFx0aGFuZGxlTnVtYmVyczogW2luZGV4IC0gMSwgaW5kZXhdXHJcblx0XHRcdFx0XHR9KTtcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuLyohIEluIHRoaXMgZmlsZTogU2xpZGVyIGV2ZW50cyAobm90IGJyb3dzZXIgZXZlbnRzKTsgKi9cclxuXHJcblx0Ly8gQXR0YWNoIGFuIGV2ZW50IHRvIHRoaXMgc2xpZGVyLCBwb3NzaWJseSBpbmNsdWRpbmcgYSBuYW1lc3BhY2VcclxuXHRmdW5jdGlvbiBiaW5kRXZlbnQgKCBuYW1lc3BhY2VkRXZlbnQsIGNhbGxiYWNrICkge1xyXG5cdFx0c2NvcGVfRXZlbnRzW25hbWVzcGFjZWRFdmVudF0gPSBzY29wZV9FdmVudHNbbmFtZXNwYWNlZEV2ZW50XSB8fCBbXTtcclxuXHRcdHNjb3BlX0V2ZW50c1tuYW1lc3BhY2VkRXZlbnRdLnB1c2goY2FsbGJhY2spO1xyXG5cclxuXHRcdC8vIElmIHRoZSBldmVudCBib3VuZCBpcyAndXBkYXRlLCcgZmlyZSBpdCBpbW1lZGlhdGVseSBmb3IgYWxsIGhhbmRsZXMuXHJcblx0XHRpZiAoIG5hbWVzcGFjZWRFdmVudC5zcGxpdCgnLicpWzBdID09PSAndXBkYXRlJyApIHtcclxuXHRcdFx0c2NvcGVfSGFuZGxlcy5mb3JFYWNoKGZ1bmN0aW9uKGEsIGluZGV4KXtcclxuXHRcdFx0XHRmaXJlRXZlbnQoJ3VwZGF0ZScsIGluZGV4KTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvLyBVbmRvIGF0dGFjaG1lbnQgb2YgZXZlbnRcclxuXHRmdW5jdGlvbiByZW1vdmVFdmVudCAoIG5hbWVzcGFjZWRFdmVudCApIHtcclxuXHJcblx0XHR2YXIgZXZlbnQgPSBuYW1lc3BhY2VkRXZlbnQgJiYgbmFtZXNwYWNlZEV2ZW50LnNwbGl0KCcuJylbMF07XHJcblx0XHR2YXIgbmFtZXNwYWNlID0gZXZlbnQgJiYgbmFtZXNwYWNlZEV2ZW50LnN1YnN0cmluZyhldmVudC5sZW5ndGgpO1xyXG5cclxuXHRcdE9iamVjdC5rZXlzKHNjb3BlX0V2ZW50cykuZm9yRWFjaChmdW5jdGlvbiggYmluZCApe1xyXG5cclxuXHRcdFx0dmFyIHRFdmVudCA9IGJpbmQuc3BsaXQoJy4nKVswXTtcclxuXHRcdFx0dmFyIHROYW1lc3BhY2UgPSBiaW5kLnN1YnN0cmluZyh0RXZlbnQubGVuZ3RoKTtcclxuXHJcblx0XHRcdGlmICggKCFldmVudCB8fCBldmVudCA9PT0gdEV2ZW50KSAmJiAoIW5hbWVzcGFjZSB8fCBuYW1lc3BhY2UgPT09IHROYW1lc3BhY2UpICkge1xyXG5cdFx0XHRcdGRlbGV0ZSBzY29wZV9FdmVudHNbYmluZF07XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdH1cclxuXHJcblx0Ly8gRXh0ZXJuYWwgZXZlbnQgaGFuZGxpbmdcclxuXHRmdW5jdGlvbiBmaXJlRXZlbnQgKCBldmVudE5hbWUsIGhhbmRsZU51bWJlciwgdGFwICkge1xyXG5cclxuXHRcdE9iamVjdC5rZXlzKHNjb3BlX0V2ZW50cykuZm9yRWFjaChmdW5jdGlvbiggdGFyZ2V0RXZlbnQgKSB7XHJcblxyXG5cdFx0XHR2YXIgZXZlbnRUeXBlID0gdGFyZ2V0RXZlbnQuc3BsaXQoJy4nKVswXTtcclxuXHJcblx0XHRcdGlmICggZXZlbnROYW1lID09PSBldmVudFR5cGUgKSB7XHJcblx0XHRcdFx0c2NvcGVfRXZlbnRzW3RhcmdldEV2ZW50XS5mb3JFYWNoKGZ1bmN0aW9uKCBjYWxsYmFjayApIHtcclxuXHJcblx0XHRcdFx0XHRjYWxsYmFjay5jYWxsKFxyXG5cdFx0XHRcdFx0XHQvLyBVc2UgdGhlIHNsaWRlciBwdWJsaWMgQVBJIGFzIHRoZSBzY29wZSAoJ3RoaXMnKVxyXG5cdFx0XHRcdFx0XHRzY29wZV9TZWxmLFxyXG5cdFx0XHRcdFx0XHQvLyBSZXR1cm4gdmFsdWVzIGFzIGFycmF5LCBzbyBhcmdfMVthcmdfMl0gaXMgYWx3YXlzIHZhbGlkLlxyXG5cdFx0XHRcdFx0XHRzY29wZV9WYWx1ZXMubWFwKG9wdGlvbnMuZm9ybWF0LnRvKSxcclxuXHRcdFx0XHRcdFx0Ly8gSGFuZGxlIGluZGV4LCAwIG9yIDFcclxuXHRcdFx0XHRcdFx0aGFuZGxlTnVtYmVyLFxyXG5cdFx0XHRcdFx0XHQvLyBVbmZvcm1hdHRlZCBzbGlkZXIgdmFsdWVzXHJcblx0XHRcdFx0XHRcdHNjb3BlX1ZhbHVlcy5zbGljZSgpLFxyXG5cdFx0XHRcdFx0XHQvLyBFdmVudCBpcyBmaXJlZCBieSB0YXAsIHRydWUgb3IgZmFsc2VcclxuXHRcdFx0XHRcdFx0dGFwIHx8IGZhbHNlLFxyXG5cdFx0XHRcdFx0XHQvLyBMZWZ0IG9mZnNldCBvZiB0aGUgaGFuZGxlLCBpbiByZWxhdGlvbiB0byB0aGUgc2xpZGVyXHJcblx0XHRcdFx0XHRcdHNjb3BlX0xvY2F0aW9ucy5zbGljZSgpXHJcblx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG4vKiEgSW4gdGhpcyBmaWxlOiBNZWNoYW5pY3MgZm9yIHNsaWRlciBvcGVyYXRpb24gKi9cclxuXHJcblx0ZnVuY3Rpb24gdG9QY3QgKCBwY3QgKSB7XHJcblx0XHRyZXR1cm4gcGN0ICsgJyUnO1xyXG5cdH1cclxuXHJcblx0Ly8gU3BsaXQgb3V0IHRoZSBoYW5kbGUgcG9zaXRpb25pbmcgbG9naWMgc28gdGhlIE1vdmUgZXZlbnQgY2FuIHVzZSBpdCwgdG9vXHJcblx0ZnVuY3Rpb24gY2hlY2tIYW5kbGVQb3NpdGlvbiAoIHJlZmVyZW5jZSwgaGFuZGxlTnVtYmVyLCB0bywgbG9va0JhY2t3YXJkLCBsb29rRm9yd2FyZCwgZ2V0VmFsdWUgKSB7XHJcblxyXG5cdFx0Ly8gRm9yIHNsaWRlcnMgd2l0aCBtdWx0aXBsZSBoYW5kbGVzLCBsaW1pdCBtb3ZlbWVudCB0byB0aGUgb3RoZXIgaGFuZGxlLlxyXG5cdFx0Ly8gQXBwbHkgdGhlIG1hcmdpbiBvcHRpb24gYnkgYWRkaW5nIGl0IHRvIHRoZSBoYW5kbGUgcG9zaXRpb25zLlxyXG5cdFx0aWYgKCBzY29wZV9IYW5kbGVzLmxlbmd0aCA+IDEgKSB7XHJcblxyXG5cdFx0XHRpZiAoIGxvb2tCYWNrd2FyZCAmJiBoYW5kbGVOdW1iZXIgPiAwICkge1xyXG5cdFx0XHRcdHRvID0gTWF0aC5tYXgodG8sIHJlZmVyZW5jZVtoYW5kbGVOdW1iZXIgLSAxXSArIG9wdGlvbnMubWFyZ2luKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCBsb29rRm9yd2FyZCAmJiBoYW5kbGVOdW1iZXIgPCBzY29wZV9IYW5kbGVzLmxlbmd0aCAtIDEgKSB7XHJcblx0XHRcdFx0dG8gPSBNYXRoLm1pbih0bywgcmVmZXJlbmNlW2hhbmRsZU51bWJlciArIDFdIC0gb3B0aW9ucy5tYXJnaW4pO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gVGhlIGxpbWl0IG9wdGlvbiBoYXMgdGhlIG9wcG9zaXRlIGVmZmVjdCwgbGltaXRpbmcgaGFuZGxlcyB0byBhXHJcblx0XHQvLyBtYXhpbXVtIGRpc3RhbmNlIGZyb20gYW5vdGhlci4gTGltaXQgbXVzdCBiZSA+IDAsIGFzIG90aGVyd2lzZVxyXG5cdFx0Ly8gaGFuZGxlcyB3b3VsZCBiZSB1bm1vdmVhYmxlLlxyXG5cdFx0aWYgKCBzY29wZV9IYW5kbGVzLmxlbmd0aCA+IDEgJiYgb3B0aW9ucy5saW1pdCApIHtcclxuXHJcblx0XHRcdGlmICggbG9va0JhY2t3YXJkICYmIGhhbmRsZU51bWJlciA+IDAgKSB7XHJcblx0XHRcdFx0dG8gPSBNYXRoLm1pbih0bywgcmVmZXJlbmNlW2hhbmRsZU51bWJlciAtIDFdICsgb3B0aW9ucy5saW1pdCk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmICggbG9va0ZvcndhcmQgJiYgaGFuZGxlTnVtYmVyIDwgc2NvcGVfSGFuZGxlcy5sZW5ndGggLSAxICkge1xyXG5cdFx0XHRcdHRvID0gTWF0aC5tYXgodG8sIHJlZmVyZW5jZVtoYW5kbGVOdW1iZXIgKyAxXSAtIG9wdGlvbnMubGltaXQpO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gVGhlIHBhZGRpbmcgb3B0aW9uIGtlZXBzIHRoZSBoYW5kbGVzIGEgY2VydGFpbiBkaXN0YW5jZSBmcm9tIHRoZVxyXG5cdFx0Ly8gZWRnZXMgb2YgdGhlIHNsaWRlci4gUGFkZGluZyBtdXN0IGJlID4gMC5cclxuXHRcdGlmICggb3B0aW9ucy5wYWRkaW5nICkge1xyXG5cclxuXHRcdFx0aWYgKCBoYW5kbGVOdW1iZXIgPT09IDAgKSB7XHJcblx0XHRcdFx0dG8gPSBNYXRoLm1heCh0bywgb3B0aW9ucy5wYWRkaW5nWzBdKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCBoYW5kbGVOdW1iZXIgPT09IHNjb3BlX0hhbmRsZXMubGVuZ3RoIC0gMSApIHtcclxuXHRcdFx0XHR0byA9IE1hdGgubWluKHRvLCAxMDAgLSBvcHRpb25zLnBhZGRpbmdbMV0pO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0dG8gPSBzY29wZV9TcGVjdHJ1bS5nZXRTdGVwKHRvKTtcclxuXHJcblx0XHQvLyBMaW1pdCBwZXJjZW50YWdlIHRvIHRoZSAwIC0gMTAwIHJhbmdlXHJcblx0XHR0byA9IGxpbWl0KHRvKTtcclxuXHJcblx0XHQvLyBSZXR1cm4gZmFsc2UgaWYgaGFuZGxlIGNhbid0IG1vdmVcclxuXHRcdGlmICggdG8gPT09IHJlZmVyZW5jZVtoYW5kbGVOdW1iZXJdICYmICFnZXRWYWx1ZSApIHtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiB0bztcclxuXHR9XHJcblxyXG5cdC8vIFVzZXMgc2xpZGVyIG9yaWVudGF0aW9uIHRvIGNyZWF0ZSBDU1MgcnVsZXMuIGEgPSBiYXNlIHZhbHVlO1xyXG5cdGZ1bmN0aW9uIGluUnVsZU9yZGVyICggdiwgYSApIHtcclxuXHRcdHZhciBvID0gb3B0aW9ucy5vcnQ7XHJcblx0XHRyZXR1cm4gKG8/YTp2KSArICcsICcgKyAobz92OmEpO1xyXG5cdH1cclxuXHJcblx0Ly8gTW92ZXMgaGFuZGxlKHMpIGJ5IGEgcGVyY2VudGFnZVxyXG5cdC8vIChib29sLCAlIHRvIG1vdmUsIFslIHdoZXJlIGhhbmRsZSBzdGFydGVkLCAuLi5dLCBbaW5kZXggaW4gc2NvcGVfSGFuZGxlcywgLi4uXSlcclxuXHRmdW5jdGlvbiBtb3ZlSGFuZGxlcyAoIHVwd2FyZCwgcHJvcG9zYWwsIGxvY2F0aW9ucywgaGFuZGxlTnVtYmVycyApIHtcclxuXHJcblx0XHR2YXIgcHJvcG9zYWxzID0gbG9jYXRpb25zLnNsaWNlKCk7XHJcblxyXG5cdFx0dmFyIGIgPSBbIXVwd2FyZCwgdXB3YXJkXTtcclxuXHRcdHZhciBmID0gW3Vwd2FyZCwgIXVwd2FyZF07XHJcblxyXG5cdFx0Ly8gQ29weSBoYW5kbGVOdW1iZXJzIHNvIHdlIGRvbid0IGNoYW5nZSB0aGUgZGF0YXNldFxyXG5cdFx0aGFuZGxlTnVtYmVycyA9IGhhbmRsZU51bWJlcnMuc2xpY2UoKTtcclxuXHJcblx0XHQvLyBDaGVjayB0byBzZWUgd2hpY2ggaGFuZGxlIGlzICdsZWFkaW5nJy5cclxuXHRcdC8vIElmIHRoYXQgb25lIGNhbid0IG1vdmUgdGhlIHNlY29uZCBjYW4ndCBlaXRoZXIuXHJcblx0XHRpZiAoIHVwd2FyZCApIHtcclxuXHRcdFx0aGFuZGxlTnVtYmVycy5yZXZlcnNlKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gU3RlcCAxOiBnZXQgdGhlIG1heGltdW0gcGVyY2VudGFnZSB0aGF0IGFueSBvZiB0aGUgaGFuZGxlcyBjYW4gbW92ZVxyXG5cdFx0aWYgKCBoYW5kbGVOdW1iZXJzLmxlbmd0aCA+IDEgKSB7XHJcblxyXG5cdFx0XHRoYW5kbGVOdW1iZXJzLmZvckVhY2goZnVuY3Rpb24oaGFuZGxlTnVtYmVyLCBvKSB7XHJcblxyXG5cdFx0XHRcdHZhciB0byA9IGNoZWNrSGFuZGxlUG9zaXRpb24ocHJvcG9zYWxzLCBoYW5kbGVOdW1iZXIsIHByb3Bvc2Fsc1toYW5kbGVOdW1iZXJdICsgcHJvcG9zYWwsIGJbb10sIGZbb10sIGZhbHNlKTtcclxuXHJcblx0XHRcdFx0Ly8gU3RvcCBpZiBvbmUgb2YgdGhlIGhhbmRsZXMgY2FuJ3QgbW92ZS5cclxuXHRcdFx0XHRpZiAoIHRvID09PSBmYWxzZSApIHtcclxuXHRcdFx0XHRcdHByb3Bvc2FsID0gMDtcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0cHJvcG9zYWwgPSB0byAtIHByb3Bvc2Fsc1toYW5kbGVOdW1iZXJdO1xyXG5cdFx0XHRcdFx0cHJvcG9zYWxzW2hhbmRsZU51bWJlcl0gPSB0bztcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIElmIHVzaW5nIG9uZSBoYW5kbGUsIGNoZWNrIGJhY2t3YXJkIEFORCBmb3J3YXJkXHJcblx0XHRlbHNlIHtcclxuXHRcdFx0YiA9IGYgPSBbdHJ1ZV07XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIHN0YXRlID0gZmFsc2U7XHJcblxyXG5cdFx0Ly8gU3RlcCAyOiBUcnkgdG8gc2V0IHRoZSBoYW5kbGVzIHdpdGggdGhlIGZvdW5kIHBlcmNlbnRhZ2VcclxuXHRcdGhhbmRsZU51bWJlcnMuZm9yRWFjaChmdW5jdGlvbihoYW5kbGVOdW1iZXIsIG8pIHtcclxuXHRcdFx0c3RhdGUgPSBzZXRIYW5kbGUoaGFuZGxlTnVtYmVyLCBsb2NhdGlvbnNbaGFuZGxlTnVtYmVyXSArIHByb3Bvc2FsLCBiW29dLCBmW29dKSB8fCBzdGF0ZTtcclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIFN0ZXAgMzogSWYgYSBoYW5kbGUgbW92ZWQsIGZpcmUgZXZlbnRzXHJcblx0XHRpZiAoIHN0YXRlICkge1xyXG5cdFx0XHRoYW5kbGVOdW1iZXJzLmZvckVhY2goZnVuY3Rpb24oaGFuZGxlTnVtYmVyKXtcclxuXHRcdFx0XHRmaXJlRXZlbnQoJ3VwZGF0ZScsIGhhbmRsZU51bWJlcik7XHJcblx0XHRcdFx0ZmlyZUV2ZW50KCdzbGlkZScsIGhhbmRsZU51bWJlcik7XHJcblx0XHRcdH0pO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0Ly8gVGFrZXMgYSBiYXNlIHZhbHVlIGFuZCBhbiBvZmZzZXQuIFRoaXMgb2Zmc2V0IGlzIHVzZWQgZm9yIHRoZSBjb25uZWN0IGJhciBzaXplLlxyXG5cdC8vIEluIHRoZSBpbml0aWFsIGRlc2lnbiBmb3IgdGhpcyBmZWF0dXJlLCB0aGUgb3JpZ2luIGVsZW1lbnQgd2FzIDElIHdpZGUuXHJcblx0Ly8gVW5mb3J0dW5hdGVseSwgYSByb3VuZGluZyBidWcgaW4gQ2hyb21lIG1ha2VzIGl0IGltcG9zc2libGUgdG8gaW1wbGVtZW50IHRoaXMgZmVhdHVyZVxyXG5cdC8vIGluIHRoaXMgbWFubmVyOiBodHRwczovL2J1Z3MuY2hyb21pdW0ub3JnL3AvY2hyb21pdW0vaXNzdWVzL2RldGFpbD9pZD03OTgyMjNcclxuXHRmdW5jdGlvbiB0cmFuc2Zvcm1EaXJlY3Rpb24gKCBhLCBiICkge1xyXG5cdFx0cmV0dXJuIG9wdGlvbnMuZGlyID8gMTAwIC0gYSAtIGIgOiBhO1xyXG5cdH1cclxuXHJcblx0Ly8gVXBkYXRlcyBzY29wZV9Mb2NhdGlvbnMgYW5kIHNjb3BlX1ZhbHVlcywgdXBkYXRlcyB2aXN1YWwgc3RhdGVcclxuXHRmdW5jdGlvbiB1cGRhdGVIYW5kbGVQb3NpdGlvbiAoIGhhbmRsZU51bWJlciwgdG8gKSB7XHJcblxyXG5cdFx0Ly8gVXBkYXRlIGxvY2F0aW9ucy5cclxuXHRcdHNjb3BlX0xvY2F0aW9uc1toYW5kbGVOdW1iZXJdID0gdG87XHJcblxyXG5cdFx0Ly8gQ29udmVydCB0aGUgdmFsdWUgdG8gdGhlIHNsaWRlciBzdGVwcGluZy9yYW5nZS5cclxuXHRcdHNjb3BlX1ZhbHVlc1toYW5kbGVOdW1iZXJdID0gc2NvcGVfU3BlY3RydW0uZnJvbVN0ZXBwaW5nKHRvKTtcclxuXHJcblx0XHR2YXIgcnVsZSA9ICd0cmFuc2xhdGUoJyArIGluUnVsZU9yZGVyKHRvUGN0KHRyYW5zZm9ybURpcmVjdGlvbih0bywgMCkgLSBzY29wZV9EaXJPZmZzZXQpLCAnMCcpICsgJyknO1xyXG5cdFx0c2NvcGVfSGFuZGxlc1toYW5kbGVOdW1iZXJdLnN0eWxlW29wdGlvbnMudHJhbnNmb3JtUnVsZV0gPSBydWxlO1xyXG5cclxuXHRcdHVwZGF0ZUNvbm5lY3QoaGFuZGxlTnVtYmVyKTtcclxuXHRcdHVwZGF0ZUNvbm5lY3QoaGFuZGxlTnVtYmVyICsgMSk7XHJcblx0fVxyXG5cclxuXHQvLyBIYW5kbGVzIGJlZm9yZSB0aGUgc2xpZGVyIG1pZGRsZSBhcmUgc3RhY2tlZCBsYXRlciA9IGhpZ2hlcixcclxuXHQvLyBIYW5kbGVzIGFmdGVyIHRoZSBtaWRkbGUgbGF0ZXIgaXMgbG93ZXJcclxuXHQvLyBbWzddIFs4XSAuLi4uLi4uLi4uIHwgLi4uLi4uLi4uLiBbNV0gWzRdXHJcblx0ZnVuY3Rpb24gc2V0WmluZGV4ICggKSB7XHJcblxyXG5cdFx0c2NvcGVfSGFuZGxlTnVtYmVycy5mb3JFYWNoKGZ1bmN0aW9uKGhhbmRsZU51bWJlcil7XHJcblx0XHRcdHZhciBkaXIgPSAoc2NvcGVfTG9jYXRpb25zW2hhbmRsZU51bWJlcl0gPiA1MCA/IC0xIDogMSk7XHJcblx0XHRcdHZhciB6SW5kZXggPSAzICsgKHNjb3BlX0hhbmRsZXMubGVuZ3RoICsgKGRpciAqIGhhbmRsZU51bWJlcikpO1xyXG5cdFx0XHRzY29wZV9IYW5kbGVzW2hhbmRsZU51bWJlcl0uc3R5bGUuekluZGV4ID0gekluZGV4O1xyXG5cdFx0fSk7XHJcblx0fVxyXG5cclxuXHQvLyBUZXN0IHN1Z2dlc3RlZCB2YWx1ZXMgYW5kIGFwcGx5IG1hcmdpbiwgc3RlcC5cclxuXHRmdW5jdGlvbiBzZXRIYW5kbGUgKCBoYW5kbGVOdW1iZXIsIHRvLCBsb29rQmFja3dhcmQsIGxvb2tGb3J3YXJkICkge1xyXG5cclxuXHRcdHRvID0gY2hlY2tIYW5kbGVQb3NpdGlvbihzY29wZV9Mb2NhdGlvbnMsIGhhbmRsZU51bWJlciwgdG8sIGxvb2tCYWNrd2FyZCwgbG9va0ZvcndhcmQsIGZhbHNlKTtcclxuXHJcblx0XHRpZiAoIHRvID09PSBmYWxzZSApIHtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdHVwZGF0ZUhhbmRsZVBvc2l0aW9uKGhhbmRsZU51bWJlciwgdG8pO1xyXG5cclxuXHRcdHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblx0Ly8gVXBkYXRlcyBzdHlsZSBhdHRyaWJ1dGUgZm9yIGNvbm5lY3Qgbm9kZXNcclxuXHRmdW5jdGlvbiB1cGRhdGVDb25uZWN0ICggaW5kZXggKSB7XHJcblxyXG5cdFx0Ly8gU2tpcCBjb25uZWN0cyBzZXQgdG8gZmFsc2VcclxuXHRcdGlmICggIXNjb3BlX0Nvbm5lY3RzW2luZGV4XSApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdHZhciBsID0gMDtcclxuXHRcdHZhciBoID0gMTAwO1xyXG5cclxuXHRcdGlmICggaW5kZXggIT09IDAgKSB7XHJcblx0XHRcdGwgPSBzY29wZV9Mb2NhdGlvbnNbaW5kZXggLSAxXTtcclxuXHRcdH1cclxuXHJcblx0XHRpZiAoIGluZGV4ICE9PSBzY29wZV9Db25uZWN0cy5sZW5ndGggLSAxICkge1xyXG5cdFx0XHRoID0gc2NvcGVfTG9jYXRpb25zW2luZGV4XTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBXZSB1c2UgdHdvIHJ1bGVzOlxyXG5cdFx0Ly8gJ3RyYW5zbGF0ZScgdG8gY2hhbmdlIHRoZSBsZWZ0L3RvcCBvZmZzZXQ7XHJcblx0XHQvLyAnc2NhbGUnIHRvIGNoYW5nZSB0aGUgd2lkdGggb2YgdGhlIGVsZW1lbnQ7XHJcblx0XHQvLyBBcyB0aGUgZWxlbWVudCBoYXMgYSB3aWR0aCBvZiAxMDAlLCBhIHRyYW5zbGF0aW9uIG9mIDEwMCUgaXMgZXF1YWwgdG8gMTAwJSBvZiB0aGUgcGFyZW50ICgubm9VaS1iYXNlKVxyXG5cdFx0dmFyIGNvbm5lY3RXaWR0aCA9IGggLSBsO1xyXG5cdFx0dmFyIHRyYW5zbGF0ZVJ1bGUgPSAndHJhbnNsYXRlKCcgKyBpblJ1bGVPcmRlcih0b1BjdCh0cmFuc2Zvcm1EaXJlY3Rpb24obCwgY29ubmVjdFdpZHRoKSksICcwJykgKyAnKSc7XHJcblx0XHR2YXIgc2NhbGVSdWxlID0gJ3NjYWxlKCcgKyBpblJ1bGVPcmRlcihjb25uZWN0V2lkdGggLyAxMDAsICcxJykgKyAnKSc7XHJcblxyXG5cdFx0c2NvcGVfQ29ubmVjdHNbaW5kZXhdLnN0eWxlW29wdGlvbnMudHJhbnNmb3JtUnVsZV0gPSB0cmFuc2xhdGVSdWxlICsgJyAnICsgc2NhbGVSdWxlO1xyXG5cdH1cclxuXHJcbi8qISBJbiB0aGlzIGZpbGU6IEFsbCBtZXRob2RzIGV2ZW50dWFsbHkgZXhwb3NlZCBpbiBzbGlkZXIubm9VaVNsaWRlci4uLiAqL1xyXG5cclxuXHQvLyBQYXJzZXMgdmFsdWUgcGFzc2VkIHRvIC5zZXQgbWV0aG9kLiBSZXR1cm5zIGN1cnJlbnQgdmFsdWUgaWYgbm90IHBhcnNlLWFibGUuXHJcblx0ZnVuY3Rpb24gcmVzb2x2ZVRvVmFsdWUgKCB0bywgaGFuZGxlTnVtYmVyICkge1xyXG5cclxuXHRcdC8vIFNldHRpbmcgd2l0aCBudWxsIGluZGljYXRlcyBhbiAnaWdub3JlJy5cclxuXHRcdC8vIElucHV0dGluZyAnZmFsc2UnIGlzIGludmFsaWQuXHJcblx0XHRpZiAoIHRvID09PSBudWxsIHx8IHRvID09PSBmYWxzZSB8fCB0byA9PT0gdW5kZWZpbmVkICkge1xyXG5cdFx0XHRyZXR1cm4gc2NvcGVfTG9jYXRpb25zW2hhbmRsZU51bWJlcl07XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gSWYgYSBmb3JtYXR0ZWQgbnVtYmVyIHdhcyBwYXNzZWQsIGF0dGVtcHQgdG8gZGVjb2RlIGl0LlxyXG5cdFx0aWYgKCB0eXBlb2YgdG8gPT09ICdudW1iZXInICkge1xyXG5cdFx0XHR0byA9IFN0cmluZyh0byk7XHJcblx0XHR9XHJcblxyXG5cdFx0dG8gPSBvcHRpb25zLmZvcm1hdC5mcm9tKHRvKTtcclxuXHRcdHRvID0gc2NvcGVfU3BlY3RydW0udG9TdGVwcGluZyh0byk7XHJcblxyXG5cdFx0Ly8gSWYgcGFyc2luZyB0aGUgbnVtYmVyIGZhaWxlZCwgdXNlIHRoZSBjdXJyZW50IHZhbHVlLlxyXG5cdFx0aWYgKCB0byA9PT0gZmFsc2UgfHwgaXNOYU4odG8pICkge1xyXG5cdFx0XHRyZXR1cm4gc2NvcGVfTG9jYXRpb25zW2hhbmRsZU51bWJlcl07XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHRvO1xyXG5cdH1cclxuXHJcblx0Ly8gU2V0IHRoZSBzbGlkZXIgdmFsdWUuXHJcblx0ZnVuY3Rpb24gdmFsdWVTZXQgKCBpbnB1dCwgZmlyZVNldEV2ZW50ICkge1xyXG5cclxuXHRcdHZhciB2YWx1ZXMgPSBhc0FycmF5KGlucHV0KTtcclxuXHRcdHZhciBpc0luaXQgPSBzY29wZV9Mb2NhdGlvbnNbMF0gPT09IHVuZGVmaW5lZDtcclxuXHJcblx0XHQvLyBFdmVudCBmaXJlcyBieSBkZWZhdWx0XHJcblx0XHRmaXJlU2V0RXZlbnQgPSAoZmlyZVNldEV2ZW50ID09PSB1bmRlZmluZWQgPyB0cnVlIDogISFmaXJlU2V0RXZlbnQpO1xyXG5cclxuXHRcdC8vIEFuaW1hdGlvbiBpcyBvcHRpb25hbC5cclxuXHRcdC8vIE1ha2Ugc3VyZSB0aGUgaW5pdGlhbCB2YWx1ZXMgd2VyZSBzZXQgYmVmb3JlIHVzaW5nIGFuaW1hdGVkIHBsYWNlbWVudC5cclxuXHRcdGlmICggb3B0aW9ucy5hbmltYXRlICYmICFpc0luaXQgKSB7XHJcblx0XHRcdGFkZENsYXNzRm9yKHNjb3BlX1RhcmdldCwgb3B0aW9ucy5jc3NDbGFzc2VzLnRhcCwgb3B0aW9ucy5hbmltYXRpb25EdXJhdGlvbik7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gRmlyc3QgcGFzcywgd2l0aG91dCBsb29rQWhlYWQgYnV0IHdpdGggbG9va0JhY2t3YXJkLiBWYWx1ZXMgYXJlIHNldCBmcm9tIGxlZnQgdG8gcmlnaHQuXHJcblx0XHRzY29wZV9IYW5kbGVOdW1iZXJzLmZvckVhY2goZnVuY3Rpb24oaGFuZGxlTnVtYmVyKXtcclxuXHRcdFx0c2V0SGFuZGxlKGhhbmRsZU51bWJlciwgcmVzb2x2ZVRvVmFsdWUodmFsdWVzW2hhbmRsZU51bWJlcl0sIGhhbmRsZU51bWJlciksIHRydWUsIGZhbHNlKTtcclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIFNlY29uZCBwYXNzLiBOb3cgdGhhdCBhbGwgYmFzZSB2YWx1ZXMgYXJlIHNldCwgYXBwbHkgY29uc3RyYWludHNcclxuXHRcdHNjb3BlX0hhbmRsZU51bWJlcnMuZm9yRWFjaChmdW5jdGlvbihoYW5kbGVOdW1iZXIpe1xyXG5cdFx0XHRzZXRIYW5kbGUoaGFuZGxlTnVtYmVyLCBzY29wZV9Mb2NhdGlvbnNbaGFuZGxlTnVtYmVyXSwgdHJ1ZSwgdHJ1ZSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHRzZXRaaW5kZXgoKTtcclxuXHJcblx0XHRzY29wZV9IYW5kbGVOdW1iZXJzLmZvckVhY2goZnVuY3Rpb24oaGFuZGxlTnVtYmVyKXtcclxuXHJcblx0XHRcdGZpcmVFdmVudCgndXBkYXRlJywgaGFuZGxlTnVtYmVyKTtcclxuXHJcblx0XHRcdC8vIEZpcmUgdGhlIGV2ZW50IG9ubHkgZm9yIGhhbmRsZXMgdGhhdCByZWNlaXZlZCBhIG5ldyB2YWx1ZSwgYXMgcGVyICM1NzlcclxuXHRcdFx0aWYgKCB2YWx1ZXNbaGFuZGxlTnVtYmVyXSAhPT0gbnVsbCAmJiBmaXJlU2V0RXZlbnQgKSB7XHJcblx0XHRcdFx0ZmlyZUV2ZW50KCdzZXQnLCBoYW5kbGVOdW1iZXIpO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cdC8vIFJlc2V0IHNsaWRlciB0byBpbml0aWFsIHZhbHVlc1xyXG5cdGZ1bmN0aW9uIHZhbHVlUmVzZXQgKCBmaXJlU2V0RXZlbnQgKSB7XHJcblx0XHR2YWx1ZVNldChvcHRpb25zLnN0YXJ0LCBmaXJlU2V0RXZlbnQpO1xyXG5cdH1cclxuXHJcblx0Ly8gR2V0IHRoZSBzbGlkZXIgdmFsdWUuXHJcblx0ZnVuY3Rpb24gdmFsdWVHZXQgKCApIHtcclxuXHJcblx0XHR2YXIgdmFsdWVzID0gc2NvcGVfVmFsdWVzLm1hcChvcHRpb25zLmZvcm1hdC50byk7XHJcblxyXG5cdFx0Ly8gSWYgb25seSBvbmUgaGFuZGxlIGlzIHVzZWQsIHJldHVybiBhIHNpbmdsZSB2YWx1ZS5cclxuXHRcdGlmICggdmFsdWVzLmxlbmd0aCA9PT0gMSApe1xyXG5cdFx0XHRyZXR1cm4gdmFsdWVzWzBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiB2YWx1ZXM7XHJcblx0fVxyXG5cclxuXHQvLyBSZW1vdmVzIGNsYXNzZXMgZnJvbSB0aGUgcm9vdCBhbmQgZW1wdGllcyBpdC5cclxuXHRmdW5jdGlvbiBkZXN0cm95ICggKSB7XHJcblxyXG5cdFx0Zm9yICggdmFyIGtleSBpbiBvcHRpb25zLmNzc0NsYXNzZXMgKSB7XHJcblx0XHRcdGlmICggIW9wdGlvbnMuY3NzQ2xhc3Nlcy5oYXNPd25Qcm9wZXJ0eShrZXkpICkgeyBjb250aW51ZTsgfVxyXG5cdFx0XHRyZW1vdmVDbGFzcyhzY29wZV9UYXJnZXQsIG9wdGlvbnMuY3NzQ2xhc3Nlc1trZXldKTtcclxuXHRcdH1cclxuXHJcblx0XHR3aGlsZSAoc2NvcGVfVGFyZ2V0LmZpcnN0Q2hpbGQpIHtcclxuXHRcdFx0c2NvcGVfVGFyZ2V0LnJlbW92ZUNoaWxkKHNjb3BlX1RhcmdldC5maXJzdENoaWxkKTtcclxuXHRcdH1cclxuXHJcblx0XHRkZWxldGUgc2NvcGVfVGFyZ2V0Lm5vVWlTbGlkZXI7XHJcblx0fVxyXG5cclxuXHQvLyBHZXQgdGhlIGN1cnJlbnQgc3RlcCBzaXplIGZvciB0aGUgc2xpZGVyLlxyXG5cdGZ1bmN0aW9uIGdldEN1cnJlbnRTdGVwICggKSB7XHJcblxyXG5cdFx0Ly8gQ2hlY2sgYWxsIGxvY2F0aW9ucywgbWFwIHRoZW0gdG8gdGhlaXIgc3RlcHBpbmcgcG9pbnQuXHJcblx0XHQvLyBHZXQgdGhlIHN0ZXAgcG9pbnQsIHRoZW4gZmluZCBpdCBpbiB0aGUgaW5wdXQgbGlzdC5cclxuXHRcdHJldHVybiBzY29wZV9Mb2NhdGlvbnMubWFwKGZ1bmN0aW9uKCBsb2NhdGlvbiwgaW5kZXggKXtcclxuXHJcblx0XHRcdHZhciBuZWFyYnlTdGVwcyA9IHNjb3BlX1NwZWN0cnVtLmdldE5lYXJieVN0ZXBzKCBsb2NhdGlvbiApO1xyXG5cdFx0XHR2YXIgdmFsdWUgPSBzY29wZV9WYWx1ZXNbaW5kZXhdO1xyXG5cdFx0XHR2YXIgaW5jcmVtZW50ID0gbmVhcmJ5U3RlcHMudGhpc1N0ZXAuc3RlcDtcclxuXHRcdFx0dmFyIGRlY3JlbWVudCA9IG51bGw7XHJcblxyXG5cdFx0XHQvLyBJZiB0aGUgbmV4dCB2YWx1ZSBpbiB0aGlzIHN0ZXAgbW92ZXMgaW50byB0aGUgbmV4dCBzdGVwLFxyXG5cdFx0XHQvLyB0aGUgaW5jcmVtZW50IGlzIHRoZSBzdGFydCBvZiB0aGUgbmV4dCBzdGVwIC0gdGhlIGN1cnJlbnQgdmFsdWVcclxuXHRcdFx0aWYgKCBpbmNyZW1lbnQgIT09IGZhbHNlICkge1xyXG5cdFx0XHRcdGlmICggdmFsdWUgKyBpbmNyZW1lbnQgPiBuZWFyYnlTdGVwcy5zdGVwQWZ0ZXIuc3RhcnRWYWx1ZSApIHtcclxuXHRcdFx0XHRcdGluY3JlbWVudCA9IG5lYXJieVN0ZXBzLnN0ZXBBZnRlci5zdGFydFZhbHVlIC0gdmFsdWU7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cclxuXHRcdFx0Ly8gSWYgdGhlIHZhbHVlIGlzIGJleW9uZCB0aGUgc3RhcnRpbmcgcG9pbnRcclxuXHRcdFx0aWYgKCB2YWx1ZSA+IG5lYXJieVN0ZXBzLnRoaXNTdGVwLnN0YXJ0VmFsdWUgKSB7XHJcblx0XHRcdFx0ZGVjcmVtZW50ID0gbmVhcmJ5U3RlcHMudGhpc1N0ZXAuc3RlcDtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0ZWxzZSBpZiAoIG5lYXJieVN0ZXBzLnN0ZXBCZWZvcmUuc3RlcCA9PT0gZmFsc2UgKSB7XHJcblx0XHRcdFx0ZGVjcmVtZW50ID0gZmFsc2U7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIElmIGEgaGFuZGxlIGlzIGF0IHRoZSBzdGFydCBvZiBhIHN0ZXAsIGl0IGFsd2F5cyBzdGVwcyBiYWNrIGludG8gdGhlIHByZXZpb3VzIHN0ZXAgZmlyc3RcclxuXHRcdFx0ZWxzZSB7XHJcblx0XHRcdFx0ZGVjcmVtZW50ID0gdmFsdWUgLSBuZWFyYnlTdGVwcy5zdGVwQmVmb3JlLmhpZ2hlc3RTdGVwO1xyXG5cdFx0XHR9XHJcblxyXG5cclxuXHRcdFx0Ly8gTm93LCBpZiBhdCB0aGUgc2xpZGVyIGVkZ2VzLCB0aGVyZSBpcyBub3QgaW4vZGVjcmVtZW50XHJcblx0XHRcdGlmICggbG9jYXRpb24gPT09IDEwMCApIHtcclxuXHRcdFx0XHRpbmNyZW1lbnQgPSBudWxsO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRlbHNlIGlmICggbG9jYXRpb24gPT09IDAgKSB7XHJcblx0XHRcdFx0ZGVjcmVtZW50ID0gbnVsbDtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gQXMgcGVyICMzOTEsIHRoZSBjb21wYXJpc29uIGZvciB0aGUgZGVjcmVtZW50IHN0ZXAgY2FuIGhhdmUgc29tZSByb3VuZGluZyBpc3N1ZXMuXHJcblx0XHRcdHZhciBzdGVwRGVjaW1hbHMgPSBzY29wZV9TcGVjdHJ1bS5jb3VudFN0ZXBEZWNpbWFscygpO1xyXG5cclxuXHRcdFx0Ly8gUm91bmQgcGVyICMzOTFcclxuXHRcdFx0aWYgKCBpbmNyZW1lbnQgIT09IG51bGwgJiYgaW5jcmVtZW50ICE9PSBmYWxzZSApIHtcclxuXHRcdFx0XHRpbmNyZW1lbnQgPSBOdW1iZXIoaW5jcmVtZW50LnRvRml4ZWQoc3RlcERlY2ltYWxzKSk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmICggZGVjcmVtZW50ICE9PSBudWxsICYmIGRlY3JlbWVudCAhPT0gZmFsc2UgKSB7XHJcblx0XHRcdFx0ZGVjcmVtZW50ID0gTnVtYmVyKGRlY3JlbWVudC50b0ZpeGVkKHN0ZXBEZWNpbWFscykpO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gW2RlY3JlbWVudCwgaW5jcmVtZW50XTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHJcblx0Ly8gVXBkYXRlYWJsZTogbWFyZ2luLCBsaW1pdCwgcGFkZGluZywgc3RlcCwgcmFuZ2UsIGFuaW1hdGUsIHNuYXBcclxuXHRmdW5jdGlvbiB1cGRhdGVPcHRpb25zICggb3B0aW9uc1RvVXBkYXRlLCBmaXJlU2V0RXZlbnQgKSB7XHJcblxyXG5cdFx0Ly8gU3BlY3RydW0gaXMgY3JlYXRlZCB1c2luZyB0aGUgcmFuZ2UsIHNuYXAsIGRpcmVjdGlvbiBhbmQgc3RlcCBvcHRpb25zLlxyXG5cdFx0Ly8gJ3NuYXAnIGFuZCAnc3RlcCcgY2FuIGJlIHVwZGF0ZWQuXHJcblx0XHQvLyBJZiAnc25hcCcgYW5kICdzdGVwJyBhcmUgbm90IHBhc3NlZCwgdGhleSBzaG91bGQgcmVtYWluIHVuY2hhbmdlZC5cclxuXHRcdHZhciB2ID0gdmFsdWVHZXQoKTtcclxuXHJcblx0XHR2YXIgdXBkYXRlQWJsZSA9IFsnbWFyZ2luJywgJ2xpbWl0JywgJ3BhZGRpbmcnLCAncmFuZ2UnLCAnYW5pbWF0ZScsICdzbmFwJywgJ3N0ZXAnLCAnZm9ybWF0J107XHJcblxyXG5cdFx0Ly8gT25seSBjaGFuZ2Ugb3B0aW9ucyB0aGF0IHdlJ3JlIGFjdHVhbGx5IHBhc3NlZCB0byB1cGRhdGUuXHJcblx0XHR1cGRhdGVBYmxlLmZvckVhY2goZnVuY3Rpb24obmFtZSl7XHJcblx0XHRcdGlmICggb3B0aW9uc1RvVXBkYXRlW25hbWVdICE9PSB1bmRlZmluZWQgKSB7XHJcblx0XHRcdFx0b3JpZ2luYWxPcHRpb25zW25hbWVdID0gb3B0aW9uc1RvVXBkYXRlW25hbWVdO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHJcblx0XHR2YXIgbmV3T3B0aW9ucyA9IHRlc3RPcHRpb25zKG9yaWdpbmFsT3B0aW9ucyk7XHJcblxyXG5cdFx0Ly8gTG9hZCBuZXcgb3B0aW9ucyBpbnRvIHRoZSBzbGlkZXIgc3RhdGVcclxuXHRcdHVwZGF0ZUFibGUuZm9yRWFjaChmdW5jdGlvbihuYW1lKXtcclxuXHRcdFx0aWYgKCBvcHRpb25zVG9VcGRhdGVbbmFtZV0gIT09IHVuZGVmaW5lZCApIHtcclxuXHRcdFx0XHRvcHRpb25zW25hbWVdID0gbmV3T3B0aW9uc1tuYW1lXTtcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblxyXG5cdFx0c2NvcGVfU3BlY3RydW0gPSBuZXdPcHRpb25zLnNwZWN0cnVtO1xyXG5cclxuXHRcdC8vIExpbWl0LCBtYXJnaW4gYW5kIHBhZGRpbmcgZGVwZW5kIG9uIHRoZSBzcGVjdHJ1bSBidXQgYXJlIHN0b3JlZCBvdXRzaWRlIG9mIGl0LiAoIzY3NylcclxuXHRcdG9wdGlvbnMubWFyZ2luID0gbmV3T3B0aW9ucy5tYXJnaW47XHJcblx0XHRvcHRpb25zLmxpbWl0ID0gbmV3T3B0aW9ucy5saW1pdDtcclxuXHRcdG9wdGlvbnMucGFkZGluZyA9IG5ld09wdGlvbnMucGFkZGluZztcclxuXHJcblx0XHQvLyBVcGRhdGUgcGlwcywgcmVtb3ZlcyBleGlzdGluZy5cclxuXHRcdGlmICggb3B0aW9ucy5waXBzICkge1xyXG5cdFx0XHRwaXBzKG9wdGlvbnMucGlwcyk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gSW52YWxpZGF0ZSB0aGUgY3VycmVudCBwb3NpdGlvbmluZyBzbyB2YWx1ZVNldCBmb3JjZXMgYW4gdXBkYXRlLlxyXG5cdFx0c2NvcGVfTG9jYXRpb25zID0gW107XHJcblx0XHR2YWx1ZVNldChvcHRpb25zVG9VcGRhdGUuc3RhcnQgfHwgdiwgZmlyZVNldEV2ZW50KTtcclxuXHR9XHJcblxyXG4vKiEgSW4gdGhpcyBmaWxlOiBDYWxscyB0byBmdW5jdGlvbnMuIEFsbCBvdGhlciBzY29wZV8gZmlsZXMgZGVmaW5lIGZ1bmN0aW9ucyBvbmx5OyAqL1xyXG5cclxuXHQvLyBDcmVhdGUgdGhlIGJhc2UgZWxlbWVudCwgaW5pdGlhbGl6ZSBIVE1MIGFuZCBzZXQgY2xhc3Nlcy5cclxuXHQvLyBBZGQgaGFuZGxlcyBhbmQgY29ubmVjdCBlbGVtZW50cy5cclxuXHRhZGRTbGlkZXIoc2NvcGVfVGFyZ2V0KTtcclxuXHRhZGRFbGVtZW50cyhvcHRpb25zLmNvbm5lY3QsIHNjb3BlX0Jhc2UpO1xyXG5cclxuXHQvLyBBdHRhY2ggdXNlciBldmVudHMuXHJcblx0YmluZFNsaWRlckV2ZW50cyhvcHRpb25zLmV2ZW50cyk7XHJcblxyXG5cdC8vIFVzZSB0aGUgcHVibGljIHZhbHVlIG1ldGhvZCB0byBzZXQgdGhlIHN0YXJ0IHZhbHVlcy5cclxuXHR2YWx1ZVNldChvcHRpb25zLnN0YXJ0KTtcclxuXHJcblx0c2NvcGVfU2VsZiA9IHtcclxuXHRcdGRlc3Ryb3k6IGRlc3Ryb3ksXHJcblx0XHRzdGVwczogZ2V0Q3VycmVudFN0ZXAsXHJcblx0XHRvbjogYmluZEV2ZW50LFxyXG5cdFx0b2ZmOiByZW1vdmVFdmVudCxcclxuXHRcdGdldDogdmFsdWVHZXQsXHJcblx0XHRzZXQ6IHZhbHVlU2V0LFxyXG5cdFx0cmVzZXQ6IHZhbHVlUmVzZXQsXHJcblx0XHQvLyBFeHBvc2VkIGZvciB1bml0IHRlc3RpbmcsIGRvbid0IHVzZSB0aGlzIGluIHlvdXIgYXBwbGljYXRpb24uXHJcblx0XHRfX21vdmVIYW5kbGVzOiBmdW5jdGlvbihhLCBiLCBjKSB7IG1vdmVIYW5kbGVzKGEsIGIsIHNjb3BlX0xvY2F0aW9ucywgYyk7IH0sXHJcblx0XHRvcHRpb25zOiBvcmlnaW5hbE9wdGlvbnMsIC8vIElzc3VlICM2MDAsICM2NzhcclxuXHRcdHVwZGF0ZU9wdGlvbnM6IHVwZGF0ZU9wdGlvbnMsXHJcblx0XHR0YXJnZXQ6IHNjb3BlX1RhcmdldCwgLy8gSXNzdWUgIzU5N1xyXG5cdFx0cmVtb3ZlUGlwczogcmVtb3ZlUGlwcyxcclxuXHRcdHBpcHM6IHBpcHMgLy8gSXNzdWUgIzU5NFxyXG5cdH07XHJcblxyXG5cdGlmICggb3B0aW9ucy5waXBzICkge1xyXG5cdFx0cGlwcyhvcHRpb25zLnBpcHMpO1xyXG5cdH1cclxuXHJcblx0aWYgKCBvcHRpb25zLnRvb2x0aXBzICkge1xyXG5cdFx0dG9vbHRpcHMoKTtcclxuXHR9XHJcblxyXG5cdGFyaWEoKTtcclxuXHJcblx0cmV0dXJuIHNjb3BlX1NlbGY7XHJcblxyXG59XHJcblxyXG5cclxuXHQvLyBSdW4gdGhlIHN0YW5kYXJkIGluaXRpYWxpemVyXHJcblx0ZnVuY3Rpb24gaW5pdGlhbGl6ZSAoIHRhcmdldCwgb3JpZ2luYWxPcHRpb25zICkge1xyXG5cclxuXHRcdGlmICggIXRhcmdldCB8fCAhdGFyZ2V0Lm5vZGVOYW1lICkge1xyXG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoXCJub1VpU2xpZGVyIChcIiArIFZFUlNJT04gKyBcIik6IGNyZWF0ZSByZXF1aXJlcyBhIHNpbmdsZSBlbGVtZW50LCBnb3Q6IFwiICsgdGFyZ2V0KTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBUaHJvdyBhbiBlcnJvciBpZiB0aGUgc2xpZGVyIHdhcyBhbHJlYWR5IGluaXRpYWxpemVkLlxyXG5cdFx0aWYgKCB0YXJnZXQubm9VaVNsaWRlciApIHtcclxuXHRcdFx0dGhyb3cgbmV3IEVycm9yKFwibm9VaVNsaWRlciAoXCIgKyBWRVJTSU9OICsgXCIpOiBTbGlkZXIgd2FzIGFscmVhZHkgaW5pdGlhbGl6ZWQuXCIpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFRlc3QgdGhlIG9wdGlvbnMgYW5kIGNyZWF0ZSB0aGUgc2xpZGVyIGVudmlyb25tZW50O1xyXG5cdFx0dmFyIG9wdGlvbnMgPSB0ZXN0T3B0aW9ucyggb3JpZ2luYWxPcHRpb25zLCB0YXJnZXQgKTtcclxuXHRcdHZhciBhcGkgPSBzY29wZSggdGFyZ2V0LCBvcHRpb25zLCBvcmlnaW5hbE9wdGlvbnMgKTtcclxuXHJcblx0XHR0YXJnZXQubm9VaVNsaWRlciA9IGFwaTtcclxuXHJcblx0XHRyZXR1cm4gYXBpO1xyXG5cdH1cclxuXHJcblx0Ly8gVXNlIGFuIG9iamVjdCBpbnN0ZWFkIG9mIGEgZnVuY3Rpb24gZm9yIGZ1dHVyZSBleHBhbmRhYmlsaXR5O1xyXG5cdHJldHVybiB7XHJcblx0XHR2ZXJzaW9uOiBWRVJTSU9OLFxyXG5cdFx0Y3JlYXRlOiBpbml0aWFsaXplXHJcblx0fTtcclxuXHJcbn0pKTsiLCIoZnVuY3Rpb24gKGdsb2JhbCl7XG5cbnZhciAkIFx0XHRcdFx0PSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpO1xudmFyIHN0YXRlIFx0XHRcdD0gcmVxdWlyZSgnLi9zdGF0ZScpO1xudmFyIHByb2Nlc3NfZm9ybSBcdD0gcmVxdWlyZSgnLi9wcm9jZXNzX2Zvcm0nKTtcbnZhciBub1VpU2xpZGVyXHRcdD0gcmVxdWlyZSgnbm91aXNsaWRlcicpO1xuLy92YXIgY29va2llcyAgICAgICAgID0gcmVxdWlyZSgnanMtY29va2llJyk7XG52YXIgdGhpcmRQYXJ0eSAgICAgID0gcmVxdWlyZSgnLi90aGlyZHBhcnR5Jyk7XG5cbndpbmRvdy5zZWFyY2hBbmRGaWx0ZXIgPSB7XG4gICAgZXh0ZW5zaW9uczogW10sXG4gICAgcmVnaXN0ZXJFeHRlbnNpb246IGZ1bmN0aW9uKCBleHRlbnNpb25OYW1lICkge1xuICAgICAgICB0aGlzLmV4dGVuc2lvbnMucHVzaCggZXh0ZW5zaW9uTmFtZSApO1xuICAgIH1cbn07XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24ob3B0aW9ucylcbntcbiAgICB2YXIgZGVmYXVsdHMgPSB7XG4gICAgICAgIHN0YXJ0T3BlbmVkOiBmYWxzZSxcbiAgICAgICAgaXNJbml0OiB0cnVlLFxuICAgICAgICBhY3Rpb246IFwiXCJcbiAgICB9O1xuXG4gICAgdmFyIG9wdHMgPSBqUXVlcnkuZXh0ZW5kKGRlZmF1bHRzLCBvcHRpb25zKTtcbiAgICBcbiAgICB0aGlyZFBhcnR5LmluaXQoKTtcbiAgICBcbiAgICAvL2xvb3AgdGhyb3VnaCBlYWNoIGl0ZW0gbWF0Y2hlZFxuICAgIHRoaXMuZWFjaChmdW5jdGlvbigpXG4gICAge1xuXG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgICAgdGhpcy5zZmlkID0gJHRoaXMuYXR0cihcImRhdGEtc2YtZm9ybS1pZFwiKTtcblxuICAgICAgICBzdGF0ZS5hZGRTZWFyY2hGb3JtKHRoaXMuc2ZpZCwgdGhpcyk7XG5cbiAgICAgICAgdGhpcy4kZmllbGRzID0gJHRoaXMuZmluZChcIj4gdWwgPiBsaVwiKTsgLy9hIHJlZmVyZW5jZSB0byBlYWNoIGZpZWxkcyBwYXJlbnQgTElcblxuICAgICAgICB0aGlzLmVuYWJsZV90YXhvbm9teV9hcmNoaXZlcyA9ICR0aGlzLmF0dHIoJ2RhdGEtdGF4b25vbXktYXJjaGl2ZXMnKTtcbiAgICAgICAgdGhpcy5jdXJyZW50X3RheG9ub215X2FyY2hpdmUgPSAkdGhpcy5hdHRyKCdkYXRhLWN1cnJlbnQtdGF4b25vbXktYXJjaGl2ZScpO1xuXG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmVuYWJsZV90YXhvbm9teV9hcmNoaXZlcyk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuZW5hYmxlX3RheG9ub215X2FyY2hpdmVzID0gXCIwXCI7XG4gICAgICAgIH1cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuY3VycmVudF90YXhvbm9teV9hcmNoaXZlKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5jdXJyZW50X3RheG9ub215X2FyY2hpdmUgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgcHJvY2Vzc19mb3JtLmluaXQoc2VsZi5lbmFibGVfdGF4b25vbXlfYXJjaGl2ZXMsIHNlbGYuY3VycmVudF90YXhvbm9teV9hcmNoaXZlKTtcbiAgICAgICAgLy9wcm9jZXNzX2Zvcm0uc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmwoc2VsZik7XG4gICAgICAgIHByb2Nlc3NfZm9ybS5lbmFibGVJbnB1dHMoc2VsZik7XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuZXh0cmFfcXVlcnlfcGFyYW1zKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5leHRyYV9xdWVyeV9wYXJhbXMgPSB7YWxsOiB7fSwgcmVzdWx0czoge30sIGFqYXg6IHt9fTtcbiAgICAgICAgfVxuXG5cbiAgICAgICAgdGhpcy50ZW1wbGF0ZV9pc19sb2FkZWQgPSAkdGhpcy5hdHRyKFwiZGF0YS10ZW1wbGF0ZS1sb2FkZWRcIik7XG4gICAgICAgIHRoaXMuaXNfYWpheCA9ICR0aGlzLmF0dHIoXCJkYXRhLWFqYXhcIik7XG4gICAgICAgIHRoaXMuaW5zdGFuY2VfbnVtYmVyID0gJHRoaXMuYXR0cignZGF0YS1pbnN0YW5jZS1jb3VudCcpO1xuICAgICAgICB0aGlzLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyID0galF1ZXJ5KCR0aGlzLmF0dHIoXCJkYXRhLWFqYXgtdGFyZ2V0XCIpKTtcblxuICAgICAgICB0aGlzLmFqYXhfdXBkYXRlX3NlY3Rpb25zID0gJHRoaXMuYXR0cihcImRhdGEtYWpheC11cGRhdGUtc2VjdGlvbnNcIikgPyBKU09OLnBhcnNlKCAkdGhpcy5hdHRyKFwiZGF0YS1hamF4LXVwZGF0ZS1zZWN0aW9uc1wiKSApIDogW107XG4gICAgICAgIHRoaXMucmVwbGFjZV9yZXN1bHRzID0gJHRoaXMuYXR0cihcImRhdGEtcmVwbGFjZS1yZXN1bHRzXCIpID09PSBcIjBcIiA/IGZhbHNlIDogdHJ1ZTtcbiAgICAgICAgXG4gICAgICAgIHRoaXMucmVzdWx0c191cmwgPSAkdGhpcy5hdHRyKFwiZGF0YS1yZXN1bHRzLXVybFwiKTtcbiAgICAgICAgdGhpcy5kZWJ1Z19tb2RlID0gJHRoaXMuYXR0cihcImRhdGEtZGVidWctbW9kZVwiKTtcbiAgICAgICAgdGhpcy51cGRhdGVfYWpheF91cmwgPSAkdGhpcy5hdHRyKFwiZGF0YS11cGRhdGUtYWpheC11cmxcIik7XG4gICAgICAgIHRoaXMucGFnaW5hdGlvbl90eXBlID0gJHRoaXMuYXR0cihcImRhdGEtYWpheC1wYWdpbmF0aW9uLXR5cGVcIik7XG4gICAgICAgIHRoaXMuYXV0b19jb3VudCA9ICR0aGlzLmF0dHIoXCJkYXRhLWF1dG8tY291bnRcIik7XG4gICAgICAgIHRoaXMuYXV0b19jb3VudF9yZWZyZXNoX21vZGUgPSAkdGhpcy5hdHRyKFwiZGF0YS1hdXRvLWNvdW50LXJlZnJlc2gtbW9kZVwiKTtcbiAgICAgICAgdGhpcy5vbmx5X3Jlc3VsdHNfYWpheCA9ICR0aGlzLmF0dHIoXCJkYXRhLW9ubHktcmVzdWx0cy1hamF4XCIpOyAvL2lmIHdlIGFyZSBub3Qgb24gdGhlIHJlc3VsdHMgcGFnZSwgcmVkaXJlY3QgcmF0aGVyIHRoYW4gdHJ5IHRvIGxvYWQgdmlhIGFqYXhcbiAgICAgICAgdGhpcy5zY3JvbGxfdG9fcG9zID0gJHRoaXMuYXR0cihcImRhdGEtc2Nyb2xsLXRvLXBvc1wiKTtcbiAgICAgICAgdGhpcy5jdXN0b21fc2Nyb2xsX3RvID0gJHRoaXMuYXR0cihcImRhdGEtY3VzdG9tLXNjcm9sbC10b1wiKTtcbiAgICAgICAgdGhpcy5zY3JvbGxfb25fYWN0aW9uID0gJHRoaXMuYXR0cihcImRhdGEtc2Nyb2xsLW9uLWFjdGlvblwiKTtcbiAgICAgICAgdGhpcy5sYW5nX2NvZGUgPSAkdGhpcy5hdHRyKFwiZGF0YS1sYW5nLWNvZGVcIik7XG4gICAgICAgIHRoaXMuYWpheF91cmwgPSAkdGhpcy5hdHRyKCdkYXRhLWFqYXgtdXJsJyk7XG4gICAgICAgIHRoaXMuYWpheF9mb3JtX3VybCA9ICR0aGlzLmF0dHIoJ2RhdGEtYWpheC1mb3JtLXVybCcpO1xuICAgICAgICB0aGlzLmlzX3J0bCA9ICR0aGlzLmF0dHIoJ2RhdGEtaXMtcnRsJyk7XG5cbiAgICAgICAgdGhpcy5kaXNwbGF5X3Jlc3VsdF9tZXRob2QgPSAkdGhpcy5hdHRyKCdkYXRhLWRpc3BsYXktcmVzdWx0LW1ldGhvZCcpO1xuICAgICAgICB0aGlzLm1haW50YWluX3N0YXRlID0gJHRoaXMuYXR0cignZGF0YS1tYWludGFpbi1zdGF0ZScpO1xuICAgICAgICB0aGlzLmFqYXhfYWN0aW9uID0gXCJcIjtcbiAgICAgICAgdGhpcy5sYXN0X3N1Ym1pdF9xdWVyeV9wYXJhbXMgPSBcIlwiO1xuXG4gICAgICAgIHRoaXMuY3VycmVudF9wYWdlZCA9IHBhcnNlSW50KCR0aGlzLmF0dHIoJ2RhdGEtaW5pdC1wYWdlZCcpKTtcbiAgICAgICAgdGhpcy5sYXN0X2xvYWRfbW9yZV9odG1sID0gXCJcIjtcbiAgICAgICAgdGhpcy5sb2FkX21vcmVfaHRtbCA9IFwiXCI7XG4gICAgICAgIHRoaXMuYWpheF9kYXRhX3R5cGUgPSAkdGhpcy5hdHRyKCdkYXRhLWFqYXgtZGF0YS10eXBlJyk7XG4gICAgICAgIHRoaXMuYWpheF90YXJnZXRfYXR0ciA9ICR0aGlzLmF0dHIoXCJkYXRhLWFqYXgtdGFyZ2V0XCIpO1xuICAgICAgICB0aGlzLnVzZV9oaXN0b3J5X2FwaSA9ICR0aGlzLmF0dHIoXCJkYXRhLXVzZS1oaXN0b3J5LWFwaVwiKTtcbiAgICAgICAgdGhpcy5pc19zdWJtaXR0aW5nID0gZmFsc2U7XG5cbiAgICAgICAgdGhpcy5sYXN0X2FqYXhfcmVxdWVzdCA9IG51bGw7XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMucmVzdWx0c19odG1sKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5yZXN1bHRzX2h0bWwgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMudXNlX2hpc3RvcnlfYXBpKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy51c2VfaGlzdG9yeV9hcGkgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMucGFnaW5hdGlvbl90eXBlKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5wYWdpbmF0aW9uX3R5cGUgPSBcIm5vcm1hbFwiO1xuICAgICAgICB9XG4gICAgICAgIGlmKHR5cGVvZih0aGlzLmN1cnJlbnRfcGFnZWQpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRfcGFnZWQgPSAxO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuYWpheF90YXJnZXRfYXR0cik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuYWpheF90YXJnZXRfYXR0ciA9IFwiXCI7XG4gICAgICAgIH1cblxuICAgICAgICBpZih0eXBlb2YodGhpcy5hamF4X3VybCk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuYWpheF91cmwgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuYWpheF9mb3JtX3VybCk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuYWpheF9mb3JtX3VybCA9IFwiXCI7XG4gICAgICAgIH1cblxuICAgICAgICBpZih0eXBlb2YodGhpcy5yZXN1bHRzX3VybCk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMucmVzdWx0c191cmwgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuc2Nyb2xsX3RvX3Bvcyk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuc2Nyb2xsX3RvX3BvcyA9IFwiXCI7XG4gICAgICAgIH1cblxuICAgICAgICBpZih0eXBlb2YodGhpcy5zY3JvbGxfb25fYWN0aW9uKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5zY3JvbGxfb25fYWN0aW9uID0gXCJcIjtcbiAgICAgICAgfVxuICAgICAgICBpZih0eXBlb2YodGhpcy5jdXN0b21fc2Nyb2xsX3RvKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy5jdXN0b21fc2Nyb2xsX3RvID0gXCJcIjtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLiRjdXN0b21fc2Nyb2xsX3RvID0galF1ZXJ5KHRoaXMuY3VzdG9tX3Njcm9sbF90byk7XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMudXBkYXRlX2FqYXhfdXJsKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgdGhpcy51cGRhdGVfYWpheF91cmwgPSBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuZGVidWdfbW9kZSk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHRoaXMuZGVidWdfbW9kZSA9IFwiXCI7XG4gICAgICAgIH1cblxuICAgICAgICBpZih0eXBlb2YodGhpcy5hamF4X3RhcmdldF9vYmplY3QpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmFqYXhfdGFyZ2V0X29iamVjdCA9IFwiXCI7XG4gICAgICAgIH1cblxuICAgICAgICBpZih0eXBlb2YodGhpcy50ZW1wbGF0ZV9pc19sb2FkZWQpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLnRlbXBsYXRlX2lzX2xvYWRlZCA9IFwiMFwiO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKHRoaXMuYXV0b19jb3VudF9yZWZyZXNoX21vZGUpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICB7XG4gICAgICAgICAgICB0aGlzLmF1dG9fY291bnRfcmVmcmVzaF9tb2RlID0gXCIwXCI7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmFqYXhfbGlua3Nfc2VsZWN0b3IgPSAkdGhpcy5hdHRyKFwiZGF0YS1hamF4LWxpbmtzLXNlbGVjdG9yXCIpO1xuXG5cbiAgICAgICAgdGhpcy5hdXRvX3VwZGF0ZSA9ICR0aGlzLmF0dHIoXCJkYXRhLWF1dG8tdXBkYXRlXCIpO1xuICAgICAgICB0aGlzLmlucHV0VGltZXIgPSAwO1xuXG4gICAgICAgIHRoaXMuc2V0SW5maW5pdGVTY3JvbGxDb250YWluZXIgPSBmdW5jdGlvbigpXG4gICAgICAgIHtcbiAgICAgICAgICAgIC8vIFdoZW4gd2UgbmF2aWdhdGUgYXdheSBmcm9tIHNlYXJjaCByZXN1bHRzLCBhbmQgdGhlbiBwcmVzcyBiYWNrLFxuICAgICAgICAgICAgLy8gaXNfbWF4X3BhZ2VkIGlzIHJldGFpbmVkLCBzbyB3ZSBvbmx5IHdhbnQgdG8gc2V0IGl0IHRvIGZhbHNlIGlmXG4gICAgICAgICAgICAvLyB3ZSBhcmUgaW5pdGFsaXppbmcgdGhlIHJlc3VsdHMgcGFnZSB0aGUgZmlyc3QgdGltZSAtIHNvIGp1c3QgXG4gICAgICAgICAgICAvLyBjaGVjayBpZiB0aGlzIHZhciBpcyB1bmRlZmluZWQgKGFzIGl0IHNob3VsZCBiZSBvbiBmaXJzdCB1c2UpO1xuICAgICAgICAgICAgaWYgKCB0eXBlb2YgKCB0aGlzLmlzX21heF9wYWdlZCApID09PSAndW5kZWZpbmVkJyApIHtcbiAgICAgICAgICAgICAgICB0aGlzLmlzX21heF9wYWdlZCA9IGZhbHNlOyAvL2ZvciBsb2FkIG1vcmUgb25seSwgb25jZSB3ZSBkZXRlY3Qgd2UncmUgYXQgdGhlIGVuZCBzZXQgdGhpcyB0byB0cnVlXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMudXNlX3Njcm9sbF9sb2FkZXIgPSAkdGhpcy5hdHRyKCdkYXRhLXNob3ctc2Nyb2xsLWxvYWRlcicpO1xuICAgICAgICAgICAgdGhpcy5pbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyID0gJHRoaXMuYXR0cignZGF0YS1pbmZpbml0ZS1zY3JvbGwtY29udGFpbmVyJyk7XG4gICAgICAgICAgICB0aGlzLmluZmluaXRlX3Njcm9sbF90cmlnZ2VyX2Ftb3VudCA9ICR0aGlzLmF0dHIoJ2RhdGEtaW5maW5pdGUtc2Nyb2xsLXRyaWdnZXInKTtcbiAgICAgICAgICAgIHRoaXMuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcyA9ICR0aGlzLmF0dHIoJ2RhdGEtaW5maW5pdGUtc2Nyb2xsLXJlc3VsdC1jbGFzcycpO1xuICAgICAgICAgICAgdGhpcy4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lciA9IHRoaXMuJGFqYXhfcmVzdWx0c19jb250YWluZXI7XG5cbiAgICAgICAgICAgIGlmKHR5cGVvZih0aGlzLmluZmluaXRlX3Njcm9sbF9jb250YWluZXIpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHRoaXMuaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lciA9IFwiXCI7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdGhpcy4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lciA9IGpRdWVyeSh0aGlzLmFqYXhfdGFyZ2V0X2F0dHIgKyAnICcgKyB0aGlzLmluZmluaXRlX3Njcm9sbF9jb250YWluZXIpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZih0eXBlb2YodGhpcy5pbmZpbml0ZV9zY3JvbGxfcmVzdWx0X2NsYXNzKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB0aGlzLmluZmluaXRlX3Njcm9sbF9yZXN1bHRfY2xhc3MgPSBcIlwiO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZih0eXBlb2YodGhpcy51c2Vfc2Nyb2xsX2xvYWRlcik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdGhpcy51c2Vfc2Nyb2xsX2xvYWRlciA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcbiAgICAgICAgdGhpcy5zZXRJbmZpbml0ZVNjcm9sbENvbnRhaW5lcigpO1xuXG4gICAgICAgIC8qIGZ1bmN0aW9ucyAqL1xuXG4gICAgICAgIHRoaXMucmVzZXQgPSBmdW5jdGlvbihzdWJtaXRfZm9ybSlcbiAgICAgICAge1xuXG4gICAgICAgICAgICB0aGlzLnJlc2V0Rm9ybShzdWJtaXRfZm9ybSk7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuaW5wdXRVcGRhdGUgPSBmdW5jdGlvbihkZWxheUR1cmF0aW9uKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZih0eXBlb2YoZGVsYXlEdXJhdGlvbik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGRlbGF5RHVyYXRpb24gPSAzMDA7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHNlbGYucmVzZXRUaW1lcihkZWxheUR1cmF0aW9uKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc2Nyb2xsVG9Qb3MgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBvZmZzZXQgPSAwO1xuICAgICAgICAgICAgdmFyIGNhblNjcm9sbCA9IHRydWU7XG5cbiAgICAgICAgICAgIGlmKHNlbGYuaXNfYWpheD09MSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBpZihzZWxmLnNjcm9sbF90b19wb3M9PVwid2luZG93XCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBvZmZzZXQgPSAwO1xuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYoc2VsZi5zY3JvbGxfdG9fcG9zPT1cImZvcm1cIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIG9mZnNldCA9ICR0aGlzLm9mZnNldCgpLnRvcDtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZihzZWxmLnNjcm9sbF90b19wb3M9PVwicmVzdWx0c1wiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgb2Zmc2V0ID0gc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5vZmZzZXQoKS50b3A7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZihzZWxmLnNjcm9sbF90b19wb3M9PVwiY3VzdG9tXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAvL2N1c3RvbV9zY3JvbGxfdG9cbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi4kY3VzdG9tX3Njcm9sbF90by5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgb2Zmc2V0ID0gc2VsZi4kY3VzdG9tX3Njcm9sbF90by5vZmZzZXQoKS50b3A7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgY2FuU2Nyb2xsID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYoY2FuU2Nyb2xsKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgJChcImh0bWwsIGJvZHlcIikuc3RvcCgpLmFuaW1hdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgc2Nyb2xsVG9wOiBvZmZzZXRcbiAgICAgICAgICAgICAgICAgICAgfSwgXCJub3JtYWxcIiwgXCJlYXNlT3V0UXVhZFwiICk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5hdHRhY2hBY3RpdmVDbGFzcyA9IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIC8vY2hlY2sgdG8gc2VlIGlmIHdlIGFyZSB1c2luZyBhamF4ICYgYXV0byBjb3VudFxuICAgICAgICAgICAgLy9pZiBub3QsIHRoZSBzZWFyY2ggZm9ybSBkb2VzIG5vdCBnZXQgcmVsb2FkZWQsIHNvIHdlIG5lZWQgdG8gdXBkYXRlIHRoZSBzZi1vcHRpb24tYWN0aXZlIGNsYXNzIG9uIGFsbCBmaWVsZHNcblxuICAgICAgICAgICAgJHRoaXMub24oJ2NoYW5nZScsICdpbnB1dFt0eXBlPVwicmFkaW9cIl0sIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXSwgc2VsZWN0JywgZnVuY3Rpb24oZSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgJGN0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICB2YXIgJGN0aGlzX3BhcmVudCA9ICRjdGhpcy5jbG9zZXN0KFwibGlbZGF0YS1zZi1maWVsZC1uYW1lXVwiKTtcbiAgICAgICAgICAgICAgICB2YXIgdGhpc190YWcgPSAkY3RoaXMucHJvcChcInRhZ05hbWVcIikudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXRfdHlwZSA9ICRjdGhpcy5hdHRyKFwidHlwZVwiKTtcbiAgICAgICAgICAgICAgICB2YXIgcGFyZW50X3RhZyA9ICRjdGhpc19wYXJlbnQucHJvcChcInRhZ05hbWVcIikudG9Mb3dlckNhc2UoKTtcblxuICAgICAgICAgICAgICAgIGlmKCh0aGlzX3RhZz09XCJpbnB1dFwiKSYmKChpbnB1dF90eXBlPT1cInJhZGlvXCIpfHwoaW5wdXRfdHlwZT09XCJjaGVja2JveFwiKSkgJiYgKHBhcmVudF90YWc9PVwibGlcIikpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGFsbF9vcHRpb25zID0gJGN0aGlzX3BhcmVudC5wYXJlbnQoKS5maW5kKCdsaScpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGFsbF9vcHRpb25zX2ZpZWxkcyA9ICRjdGhpc19wYXJlbnQucGFyZW50KCkuZmluZCgnaW5wdXQ6Y2hlY2tlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgICRhbGxfb3B0aW9ucy5yZW1vdmVDbGFzcyhcInNmLW9wdGlvbi1hY3RpdmVcIik7XG4gICAgICAgICAgICAgICAgICAgICRhbGxfb3B0aW9uc19maWVsZHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHBhcmVudCA9ICQodGhpcykuY2xvc2VzdChcImxpXCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHBhcmVudC5hZGRDbGFzcyhcInNmLW9wdGlvbi1hY3RpdmVcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZih0aGlzX3RhZz09XCJzZWxlY3RcIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkYWxsX29wdGlvbnMgPSAkY3RoaXMuY2hpbGRyZW4oKTtcbiAgICAgICAgICAgICAgICAgICAgJGFsbF9vcHRpb25zLnJlbW92ZUNsYXNzKFwic2Ytb3B0aW9uLWFjdGl2ZVwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHRoaXNfdmFsID0gJGN0aGlzLnZhbCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciB0aGlzX2Fycl92YWwgPSAodHlwZW9mIHRoaXNfdmFsID09ICdzdHJpbmcnIHx8IHRoaXNfdmFsIGluc3RhbmNlb2YgU3RyaW5nKSA/IFt0aGlzX3ZhbF0gOiB0aGlzX3ZhbDtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXNfYXJyX3ZhbCkuZWFjaChmdW5jdGlvbihpLCB2YWx1ZSl7XG4gICAgICAgICAgICAgICAgICAgICAgICAkY3RoaXMuZmluZChcIm9wdGlvblt2YWx1ZT0nXCIrdmFsdWUrXCInXVwiKS5hZGRDbGFzcyhcInNmLW9wdGlvbi1hY3RpdmVcIik7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9O1xuICAgICAgICB0aGlzLmluaXRBdXRvVXBkYXRlRXZlbnRzID0gZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgLyogYXV0byB1cGRhdGUgKi9cbiAgICAgICAgICAgIGlmKChzZWxmLmF1dG9fdXBkYXRlPT0xKXx8KHNlbGYuYXV0b19jb3VudF9yZWZyZXNoX21vZGU9PTEpKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICR0aGlzLm9uKCdjaGFuZ2UnLCAnaW5wdXRbdHlwZT1cInJhZGlvXCJdLCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0sIHNlbGVjdCcsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5pbnB1dFVwZGF0ZSgyMDApO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgJHRoaXMub24oJ2lucHV0JywgJ2lucHV0W3R5cGU9XCJudW1iZXJcIl0nLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuaW5wdXRVcGRhdGUoODAwKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIHZhciAkdGV4dElucHV0ID0gJHRoaXMuZmluZCgnaW5wdXRbdHlwZT1cInRleHRcIl06bm90KC5zZi1kYXRlcGlja2VyKScpO1xuICAgICAgICAgICAgICAgIHZhciBsYXN0VmFsdWUgPSAkdGV4dElucHV0LnZhbCgpO1xuXG4gICAgICAgICAgICAgICAgJHRoaXMub24oJ2lucHV0JywgJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdOm5vdCguc2YtZGF0ZXBpY2tlciknLCBmdW5jdGlvbigpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBpZihsYXN0VmFsdWUhPSR0ZXh0SW5wdXQudmFsKCkpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuaW5wdXRVcGRhdGUoMTIwMCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBsYXN0VmFsdWUgPSAkdGV4dElucHV0LnZhbCgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG5cbiAgICAgICAgICAgICAgICAkdGhpcy5vbigna2V5cHJlc3MnLCAnaW5wdXRbdHlwZT1cInRleHRcIl06bm90KC5zZi1kYXRlcGlja2VyKScsIGZ1bmN0aW9uKGUpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBpZiAoZS53aGljaCA9PSAxMyl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuc3VibWl0Rm9ybSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vJHRoaXMub24oJ2lucHV0JywgJ2lucHV0LnNmLWRhdGVwaWNrZXInLCBzZWxmLmRhdGVJbnB1dFR5cGUpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLy90aGlzLmluaXRBdXRvVXBkYXRlRXZlbnRzKCk7XG5cblxuICAgICAgICB0aGlzLmNsZWFyVGltZXIgPSBmdW5jdGlvbigpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGNsZWFyVGltZW91dChzZWxmLmlucHV0VGltZXIpO1xuICAgICAgICB9O1xuICAgICAgICB0aGlzLnJlc2V0VGltZXIgPSBmdW5jdGlvbihkZWxheUR1cmF0aW9uKVxuICAgICAgICB7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQoc2VsZi5pbnB1dFRpbWVyKTtcbiAgICAgICAgICAgIHNlbGYuaW5wdXRUaW1lciA9IHNldFRpbWVvdXQoc2VsZi5mb3JtVXBkYXRlZCwgZGVsYXlEdXJhdGlvbik7XG5cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmFkZERhdGVQaWNrZXJzID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgJGRhdGVfcGlja2VyID0gJHRoaXMuZmluZChcIi5zZi1kYXRlcGlja2VyXCIpO1xuXG4gICAgICAgICAgICBpZigkZGF0ZV9waWNrZXIubGVuZ3RoPjApXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgJGRhdGVfcGlja2VyLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZGF0ZUZvcm1hdCA9IFwiXCI7XG4gICAgICAgICAgICAgICAgICAgIHZhciBkYXRlRHJvcGRvd25ZZWFyID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIHZhciBkYXRlRHJvcGRvd25Nb250aCA9IGZhbHNlO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciAkY2xvc2VzdF9kYXRlX3dyYXAgPSAkdGhpcy5jbG9zZXN0KFwiLnNmX2RhdGVfZmllbGRcIik7XG4gICAgICAgICAgICAgICAgICAgIGlmKCRjbG9zZXN0X2RhdGVfd3JhcC5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0ZUZvcm1hdCA9ICRjbG9zZXN0X2RhdGVfd3JhcC5hdHRyKFwiZGF0YS1kYXRlLWZvcm1hdFwiKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoJGNsb3Nlc3RfZGF0ZV93cmFwLmF0dHIoXCJkYXRhLWRhdGUtdXNlLXllYXItZHJvcGRvd25cIik9PTEpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0ZURyb3Bkb3duWWVhciA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBpZigkY2xvc2VzdF9kYXRlX3dyYXAuYXR0cihcImRhdGEtZGF0ZS11c2UtbW9udGgtZHJvcGRvd25cIik9PTEpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0ZURyb3Bkb3duTW9udGggPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGRhdGVQaWNrZXJPcHRpb25zID0ge1xuICAgICAgICAgICAgICAgICAgICAgICAgaW5saW5lOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgc2hvd090aGVyTW9udGhzOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgb25TZWxlY3Q6IGZ1bmN0aW9uKGUsIGZyb21fZmllbGQpeyBzZWxmLmRhdGVTZWxlY3QoZSwgZnJvbV9maWVsZCwgJCh0aGlzKSk7IH0sXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRlRm9ybWF0OiBkYXRlRm9ybWF0LFxuXG4gICAgICAgICAgICAgICAgICAgICAgICBjaGFuZ2VNb250aDogZGF0ZURyb3Bkb3duTW9udGgsXG4gICAgICAgICAgICAgICAgICAgICAgICBjaGFuZ2VZZWFyOiBkYXRlRHJvcGRvd25ZZWFyXG4gICAgICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5pc19ydGw9PTEpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGVQaWNrZXJPcHRpb25zLmRpcmVjdGlvbiA9IFwicnRsXCI7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAkdGhpcy5kYXRlcGlja2VyKGRhdGVQaWNrZXJPcHRpb25zKTtcblxuICAgICAgICAgICAgICAgICAgICBpZihzZWxmLmxhbmdfY29kZSE9XCJcIilcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgJC5kYXRlcGlja2VyLnNldERlZmF1bHRzKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQuZXh0ZW5kKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7J2RhdGVGb3JtYXQnOmRhdGVGb3JtYXR9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkLmRhdGVwaWNrZXIucmVnaW9uYWxbIHNlbGYubGFuZ19jb2RlXVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQuZGF0ZXBpY2tlci5zZXREZWZhdWx0cyhcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkLmV4dGVuZChcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgeydkYXRlRm9ybWF0JzpkYXRlRm9ybWF0fSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJC5kYXRlcGlja2VyLnJlZ2lvbmFsW1wiZW5cIl1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgICAgICAgICApO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgaWYoJCgnLmxsLXNraW4tbWVsb24nKS5sZW5ndGg9PTApe1xuXG4gICAgICAgICAgICAgICAgICAgICRkYXRlX3BpY2tlci5kYXRlcGlja2VyKCd3aWRnZXQnKS53cmFwKCc8ZGl2IGNsYXNzPVwibGwtc2tpbi1tZWxvbiBzZWFyY2hhbmRmaWx0ZXItZGF0ZS1waWNrZXJcIi8+Jyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5kYXRlU2VsZWN0ID0gZnVuY3Rpb24oZSwgZnJvbV9maWVsZCwgJHRoaXMpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHZhciAkaW5wdXRfZmllbGQgPSAkKGZyb21fZmllbGQuaW5wdXQuZ2V0KDApKTtcbiAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgIHZhciAkZGF0ZV9maWVsZHMgPSAkaW5wdXRfZmllbGQuY2xvc2VzdCgnW2RhdGEtc2YtZmllbGQtaW5wdXQtdHlwZT1cImRhdGVyYW5nZVwiXSwgW2RhdGEtc2YtZmllbGQtaW5wdXQtdHlwZT1cImRhdGVcIl0nKTtcbiAgICAgICAgICAgICRkYXRlX2ZpZWxkcy5lYWNoKGZ1bmN0aW9uKGUsIGluZGV4KXtcbiAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICB2YXIgJHRmX2RhdGVfcGlja2VycyA9ICQodGhpcykuZmluZChcIi5zZi1kYXRlcGlja2VyXCIpO1xuICAgICAgICAgICAgICAgIHZhciBub19kYXRlX3BpY2tlcnMgPSAkdGZfZGF0ZV9waWNrZXJzLmxlbmd0aDtcbiAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICBpZihub19kYXRlX3BpY2tlcnM+MSlcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIC8vdGhlbiBpdCBpcyBhIGRhdGUgcmFuZ2UsIHNvIG1ha2Ugc3VyZSBib3RoIGZpZWxkcyBhcmUgZmlsbGVkIGJlZm9yZSB1cGRhdGluZ1xuICAgICAgICAgICAgICAgICAgICB2YXIgZHBfY291bnRlciA9IDA7XG4gICAgICAgICAgICAgICAgICAgIHZhciBkcF9lbXB0eV9maWVsZF9jb3VudCA9IDA7XG4gICAgICAgICAgICAgICAgICAgICR0Zl9kYXRlX3BpY2tlcnMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZigkKHRoaXMpLnZhbCgpPT1cIlwiKVxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRwX2VtcHR5X2ZpZWxkX2NvdW50Kys7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGRwX2NvdW50ZXIrKztcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoZHBfZW1wdHlfZmllbGRfY291bnQ9PTApXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuaW5wdXRVcGRhdGUoMSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5pbnB1dFVwZGF0ZSgxKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuYWRkUmFuZ2VTbGlkZXJzID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgJG1ldGFfcmFuZ2UgPSAkdGhpcy5maW5kKFwiLnNmLW1ldGEtcmFuZ2Utc2xpZGVyXCIpO1xuXG4gICAgICAgICAgICBpZigkbWV0YV9yYW5nZS5sZW5ndGg+MClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkbWV0YV9yYW5nZS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1pbiA9ICR0aGlzLmF0dHIoXCJkYXRhLW1pblwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1heCA9ICR0aGlzLmF0dHIoXCJkYXRhLW1heFwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNtaW4gPSAkdGhpcy5hdHRyKFwiZGF0YS1zdGFydC1taW5cIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBzbWF4ID0gJHRoaXMuYXR0cihcImRhdGEtc3RhcnQtbWF4XCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZGlzcGxheV92YWx1ZV9hcyA9ICR0aGlzLmF0dHIoXCJkYXRhLWRpc3BsYXktdmFsdWVzLWFzXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgc3RlcCA9ICR0aGlzLmF0dHIoXCJkYXRhLXN0ZXBcIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkc3RhcnRfdmFsID0gJHRoaXMuZmluZCgnLnNmLXJhbmdlLW1pbicpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGVuZF92YWwgPSAkdGhpcy5maW5kKCcuc2YtcmFuZ2UtbWF4Jyk7XG5cblxuICAgICAgICAgICAgICAgICAgICB2YXIgZGVjaW1hbF9wbGFjZXMgPSAkdGhpcy5hdHRyKFwiZGF0YS1kZWNpbWFsLXBsYWNlc1wiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHRob3VzYW5kX3NlcGVyYXRvciA9ICR0aGlzLmF0dHIoXCJkYXRhLXRob3VzYW5kLXNlcGVyYXRvclwiKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGRlY2ltYWxfc2VwZXJhdG9yID0gJHRoaXMuYXR0cihcImRhdGEtZGVjaW1hbC1zZXBlcmF0b3JcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGZpZWxkX2Zvcm1hdCA9IHdOdW1iKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG1hcms6IGRlY2ltYWxfc2VwZXJhdG9yLFxuICAgICAgICAgICAgICAgICAgICAgICAgZGVjaW1hbHM6IHBhcnNlRmxvYXQoZGVjaW1hbF9wbGFjZXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgdGhvdXNhbmQ6IHRob3VzYW5kX3NlcGVyYXRvclxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIG1pbl91bmZvcm1hdHRlZCA9IHBhcnNlRmxvYXQoc21pbik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtaW5fZm9ybWF0dGVkID0gZmllbGRfZm9ybWF0LnRvKHBhcnNlRmxvYXQoc21pbikpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgbWF4X2Zvcm1hdHRlZCA9IGZpZWxkX2Zvcm1hdC50byhwYXJzZUZsb2F0KHNtYXgpKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG1heF91bmZvcm1hdHRlZCA9IHBhcnNlRmxvYXQoc21heCk7XG4gICAgICAgICAgICAgICAgICAgIC8vYWxlcnQobWluX2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgIC8vYWxlcnQobWF4X2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgIC8vYWxlcnQoZGlzcGxheV92YWx1ZV9hcyk7XG5cblxuICAgICAgICAgICAgICAgICAgICBpZihkaXNwbGF5X3ZhbHVlX2FzPT1cInRleHRpbnB1dFwiKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc3RhcnRfdmFsLnZhbChtaW5fZm9ybWF0dGVkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRlbmRfdmFsLnZhbChtYXhfZm9ybWF0dGVkKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlIGlmKGRpc3BsYXlfdmFsdWVfYXM9PVwidGV4dFwiKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc3RhcnRfdmFsLmh0bWwobWluX2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZW5kX3ZhbC5odG1sKG1heF9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgICAgICAgICB2YXIgbm9VSU9wdGlvbnMgPSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByYW5nZToge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICdtaW4nOiBbIHBhcnNlRmxvYXQobWluKSBdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICdtYXgnOiBbIHBhcnNlRmxvYXQobWF4KSBdXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgc3RhcnQ6IFttaW5fZm9ybWF0dGVkLCBtYXhfZm9ybWF0dGVkXSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGhhbmRsZXM6IDIsXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25uZWN0OiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgc3RlcDogcGFyc2VGbG9hdChzdGVwKSxcblxuICAgICAgICAgICAgICAgICAgICAgICAgYmVoYXZpb3VyOiAnZXh0ZW5kLXRhcCcsXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3JtYXQ6IGZpZWxkX2Zvcm1hdFxuICAgICAgICAgICAgICAgICAgICB9O1xuXG5cblxuICAgICAgICAgICAgICAgICAgICBpZihzZWxmLmlzX3J0bD09MSlcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgbm9VSU9wdGlvbnMuZGlyZWN0aW9uID0gXCJydGxcIjtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIHZhciBzbGlkZXJfb2JqZWN0ID0gJCh0aGlzKS5maW5kKFwiLm1ldGEtc2xpZGVyXCIpWzBdO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCBcInVuZGVmaW5lZFwiICE9PSB0eXBlb2YoIHNsaWRlcl9vYmplY3Qubm9VaVNsaWRlciApICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy9kZXN0cm95IGlmIGl0IGV4aXN0cy4uIHRoaXMgbWVhbnMgc29tZWhvdyBhbm90aGVyIGluc3RhbmNlIGhhZCBpbml0aWFsaXNlZCBpdC4uXG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuZGVzdHJveSgpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbm9VaVNsaWRlci5jcmVhdGUoc2xpZGVyX29iamVjdCwgbm9VSU9wdGlvbnMpO1xuXG4gICAgICAgICAgICAgICAgICAgICRzdGFydF92YWwub2ZmKCk7XG4gICAgICAgICAgICAgICAgICAgICRzdGFydF92YWwub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuc2V0KFskKHRoaXMpLnZhbCgpLCBudWxsXSk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICRlbmRfdmFsLm9mZigpO1xuICAgICAgICAgICAgICAgICAgICAkZW5kX3ZhbC5vbignY2hhbmdlJywgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlcl9vYmplY3Qubm9VaVNsaWRlci5zZXQoW251bGwsICQodGhpcykudmFsKCldKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8kc3RhcnRfdmFsLmh0bWwobWluX2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgIC8vJGVuZF92YWwuaHRtbChtYXhfZm9ybWF0dGVkKTtcblxuICAgICAgICAgICAgICAgICAgICBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIub2ZmKCd1cGRhdGUnKTtcbiAgICAgICAgICAgICAgICAgICAgc2xpZGVyX29iamVjdC5ub1VpU2xpZGVyLm9uKCd1cGRhdGUnLCBmdW5jdGlvbiggdmFsdWVzLCBoYW5kbGUgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBzbGlkZXJfc3RhcnRfdmFsICA9IG1pbl9mb3JtYXR0ZWQ7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2xpZGVyX2VuZF92YWwgID0gbWF4X2Zvcm1hdHRlZDtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHZhbHVlID0gdmFsdWVzW2hhbmRsZV07XG5cblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCBoYW5kbGUgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWF4X2Zvcm1hdHRlZCA9IHZhbHVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBtaW5fZm9ybWF0dGVkID0gdmFsdWU7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKGRpc3BsYXlfdmFsdWVfYXM9PVwidGV4dGlucHV0XCIpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHN0YXJ0X3ZhbC52YWwobWluX2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGVuZF92YWwudmFsKG1heF9mb3JtYXR0ZWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgZWxzZSBpZihkaXNwbGF5X3ZhbHVlX2FzPT1cInRleHRcIilcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc3RhcnRfdmFsLmh0bWwobWluX2Zvcm1hdHRlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGVuZF92YWwuaHRtbChtYXhfZm9ybWF0dGVkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2kgdGhpbmsgdGhlIGZ1bmN0aW9uIHRoYXQgYnVpbGRzIHRoZSBVUkwgbmVlZHMgdG8gZGVjb2RlIHRoZSBmb3JtYXR0ZWQgc3RyaW5nIGJlZm9yZSBhZGRpbmcgdG8gdGhlIHVybFxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoKHNlbGYuYXV0b191cGRhdGU9PTEpfHwoc2VsZi5hdXRvX2NvdW50X3JlZnJlc2hfbW9kZT09MSkpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9vbmx5IHRyeSB0byB1cGRhdGUgaWYgdGhlIHZhbHVlcyBoYXZlIGFjdHVhbGx5IGNoYW5nZWRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZigoc2xpZGVyX3N0YXJ0X3ZhbCE9bWluX2Zvcm1hdHRlZCl8fChzbGlkZXJfZW5kX3ZhbCE9bWF4X2Zvcm1hdHRlZCkpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmlucHV0VXBkYXRlKDgwMCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgc2VsZi5jbGVhclRpbWVyKCk7IC8vaWdub3JlIGFueSBjaGFuZ2VzIHJlY2VudGx5IG1hZGUgYnkgdGhlIHNsaWRlciAodGhpcyB3YXMganVzdCBpbml0IHNob3VsZG4ndCBjb3VudCBhcyBhbiB1cGRhdGUgZXZlbnQpXG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5pbml0ID0gZnVuY3Rpb24oa2VlcF9wYWdpbmF0aW9uKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZih0eXBlb2Yoa2VlcF9wYWdpbmF0aW9uKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIga2VlcF9wYWdpbmF0aW9uID0gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuaW5pdEF1dG9VcGRhdGVFdmVudHMoKTtcbiAgICAgICAgICAgIHRoaXMuYXR0YWNoQWN0aXZlQ2xhc3MoKTtcblxuICAgICAgICAgICAgdGhpcy5hZGREYXRlUGlja2VycygpO1xuICAgICAgICAgICAgdGhpcy5hZGRSYW5nZVNsaWRlcnMoKTtcblxuICAgICAgICAgICAgLy9pbml0IGNvbWJvIGJveGVzXG4gICAgICAgICAgICB2YXIgJGNvbWJvYm94ID0gJHRoaXMuZmluZChcInNlbGVjdFtkYXRhLWNvbWJvYm94PScxJ11cIik7XG5cbiAgICAgICAgICAgIGlmKCRjb21ib2JveC5sZW5ndGg+MClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkY29tYm9ib3guZWFjaChmdW5jdGlvbihpbmRleCApe1xuICAgICAgICAgICAgICAgICAgICB2YXIgJHRoaXNjYiA9ICQoIHRoaXMgKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG5ybSA9ICR0aGlzY2IuYXR0cihcImRhdGEtY29tYm9ib3gtbnJtXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgJHRoaXNjYi5jaG9zZW4gIT0gXCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGNob3Nlbm9wdGlvbnMgPSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VhcmNoX2NvbnRhaW5zOiB0cnVlXG4gICAgICAgICAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZigodHlwZW9mKG5ybSkhPT1cInVuZGVmaW5lZFwiKSYmKG5ybSkpe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNob3Nlbm9wdGlvbnMubm9fcmVzdWx0c190ZXh0ID0gbnJtO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gc2FmZSB0byB1c2UgdGhlIGZ1bmN0aW9uXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3NlYXJjaF9jb250YWluc1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5pc19ydGw9PTEpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNjYi5hZGRDbGFzcyhcImNob3Nlbi1ydGxcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzY2IuY2hvc2VuKGNob3Nlbm9wdGlvbnMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICAgICAge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2VsZWN0Mm9wdGlvbnMgPSB7fTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5pc19ydGw9PTEpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VsZWN0Mm9wdGlvbnMuZGlyID0gXCJydGxcIjtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCh0eXBlb2YobnJtKSE9PVwidW5kZWZpbmVkXCIpJiYobnJtKSl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2VsZWN0Mm9wdGlvbnMubGFuZ3VhZ2U9IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgXCJub1Jlc3VsdHNcIjogZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBucm07XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAkdGhpc2NiLnNlbGVjdDIoc2VsZWN0Mm9wdGlvbnMpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHNlbGYuaXNTdWJtaXR0aW5nID0gZmFsc2U7XG5cbiAgICAgICAgICAgIC8vaWYgYWpheCBpcyBlbmFibGVkIGluaXQgdGhlIHBhZ2luYXRpb25cbiAgICAgICAgICAgIGlmKHNlbGYuaXNfYWpheD09MSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLnNldHVwQWpheFBhZ2luYXRpb24oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJHRoaXMub24oXCJzdWJtaXRcIiwgdGhpcy5zdWJtaXRGb3JtKTtcblxuICAgICAgICAgICAgc2VsZi5pbml0V29vQ29tbWVyY2VDb250cm9scygpOyAvL3dvb2NvbW1lcmNlIG9yZGVyYnlcblxuICAgICAgICAgICAgaWYoa2VlcF9wYWdpbmF0aW9uPT1mYWxzZSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3Rfc3VibWl0X3F1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKGZhbHNlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMub25XaW5kb3dTY3JvbGwgPSBmdW5jdGlvbihldmVudClcbiAgICAgICAge1xuICAgICAgICAgICAgaWYoKCFzZWxmLmlzX2xvYWRpbmdfbW9yZSkgJiYgKCFzZWxmLmlzX21heF9wYWdlZCkpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIHdpbmRvd19zY3JvbGwgPSAkKHdpbmRvdykuc2Nyb2xsVG9wKCk7XG4gICAgICAgICAgICAgICAgdmFyIHdpbmRvd19zY3JvbGxfYm90dG9tID0gJCh3aW5kb3cpLnNjcm9sbFRvcCgpICsgJCh3aW5kb3cpLmhlaWdodCgpO1xuICAgICAgICAgICAgICAgIHZhciBzY3JvbGxfb2Zmc2V0ID0gcGFyc2VJbnQoc2VsZi5pbmZpbml0ZV9zY3JvbGxfdHJpZ2dlcl9hbW91bnQpO1xuXG4gICAgICAgICAgICAgICAgaWYoc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lci5sZW5ndGg9PTEpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0c19zY3JvbGxfYm90dG9tID0gc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lci5vZmZzZXQoKS50b3AgKyBzZWxmLiRpbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyLmhlaWdodCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBvZmZzZXQgPSAoc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lci5vZmZzZXQoKS50b3AgKyBzZWxmLiRpbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyLmhlaWdodCgpKSAtIHdpbmRvd19zY3JvbGw7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYod2luZG93X3Njcm9sbF9ib3R0b20gPiByZXN1bHRzX3Njcm9sbF9ib3R0b20gKyBzY3JvbGxfb2Zmc2V0KVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmxvYWRNb3JlUmVzdWx0cygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICAgICAgey8vZG9udCBsb2FkIG1vcmVcblxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zdHJpcFF1ZXJ5U3RyaW5nQW5kSGFzaEZyb21QYXRoID0gZnVuY3Rpb24odXJsKSB7XG4gICAgICAgICAgICByZXR1cm4gdXJsLnNwbGl0KFwiP1wiKVswXS5zcGxpdChcIiNcIilbMF07XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmd1cCA9IGZ1bmN0aW9uKCBuYW1lLCB1cmwgKSB7XG4gICAgICAgICAgICBpZiAoIXVybCkgdXJsID0gbG9jYXRpb24uaHJlZlxuICAgICAgICAgICAgbmFtZSA9IG5hbWUucmVwbGFjZSgvW1xcW10vLFwiXFxcXFxcW1wiKS5yZXBsYWNlKC9bXFxdXS8sXCJcXFxcXFxdXCIpO1xuICAgICAgICAgICAgdmFyIHJlZ2V4UyA9IFwiW1xcXFw/Jl1cIituYW1lK1wiPShbXiYjXSopXCI7XG4gICAgICAgICAgICB2YXIgcmVnZXggPSBuZXcgUmVnRXhwKCByZWdleFMgKTtcbiAgICAgICAgICAgIHZhciByZXN1bHRzID0gcmVnZXguZXhlYyggdXJsICk7XG4gICAgICAgICAgICByZXR1cm4gcmVzdWx0cyA9PSBudWxsID8gbnVsbCA6IHJlc3VsdHNbMV07XG4gICAgICAgIH07XG5cblxuICAgICAgICB0aGlzLmdldFVybFBhcmFtcyA9IGZ1bmN0aW9uKGtlZXBfcGFnaW5hdGlvbiwgdHlwZSwgZXhjbHVkZSlcbiAgICAgICAge1xuICAgICAgICAgICAgaWYodHlwZW9mKGtlZXBfcGFnaW5hdGlvbik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGtlZXBfcGFnaW5hdGlvbiA9IHRydWU7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKHR5cGVvZih0eXBlKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgdHlwZSA9IFwiXCI7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciB1cmxfcGFyYW1zX3N0ciA9IFwiXCI7XG5cbiAgICAgICAgICAgIC8vIGdldCBhbGwgcGFyYW1zIGZyb20gZmllbGRzXG4gICAgICAgICAgICB2YXIgdXJsX3BhcmFtc19hcnJheSA9IHByb2Nlc3NfZm9ybS5nZXRVcmxQYXJhbXMoc2VsZik7XG5cbiAgICAgICAgICAgIHZhciBsZW5ndGggPSBPYmplY3Qua2V5cyh1cmxfcGFyYW1zX2FycmF5KS5sZW5ndGg7XG4gICAgICAgICAgICB2YXIgY291bnQgPSAwO1xuXG4gICAgICAgICAgICBpZih0eXBlb2YoZXhjbHVkZSkhPVwidW5kZWZpbmVkXCIpIHtcbiAgICAgICAgICAgICAgICBpZiAodXJsX3BhcmFtc19hcnJheS5oYXNPd25Qcm9wZXJ0eShleGNsdWRlKSkge1xuICAgICAgICAgICAgICAgICAgICBsZW5ndGgtLTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKGxlbmd0aD4wKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIGZvciAodmFyIGsgaW4gdXJsX3BhcmFtc19hcnJheSkge1xuICAgICAgICAgICAgICAgICAgICBpZiAodXJsX3BhcmFtc19hcnJheS5oYXNPd25Qcm9wZXJ0eShrKSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgY2FuX2FkZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZih0eXBlb2YoZXhjbHVkZSkhPVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoaz09ZXhjbHVkZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYW5fYWRkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihjYW5fYWRkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsX3BhcmFtc19zdHIgKz0gayArIFwiPVwiICsgdXJsX3BhcmFtc19hcnJheVtrXTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb3VudCA8IGxlbmd0aCAtIDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsX3BhcmFtc19zdHIgKz0gXCImXCI7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY291bnQrKztcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIHF1ZXJ5X3BhcmFtcyA9IFwiXCI7XG5cbiAgICAgICAgICAgIC8vZm9ybSBwYXJhbXMgYXMgdXJsIHF1ZXJ5IHN0cmluZ1xuICAgICAgICAgICAgdmFyIGZvcm1fcGFyYW1zID0gdXJsX3BhcmFtc19zdHI7XG5cbiAgICAgICAgICAgIC8vZ2V0IHVybCBwYXJhbXMgZnJvbSB0aGUgZm9ybSBpdHNlbGYgKHdoYXQgdGhlIHVzZXIgaGFzIHNlbGVjdGVkKVxuICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBmb3JtX3BhcmFtcyk7XG5cbiAgICAgICAgICAgIC8vYWRkIHBhZ2luYXRpb25cbiAgICAgICAgICAgIGlmKGtlZXBfcGFnaW5hdGlvbj09dHJ1ZSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgcGFnZU51bWJlciA9IHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuYXR0cihcImRhdGEtcGFnZWRcIik7XG5cbiAgICAgICAgICAgICAgICBpZih0eXBlb2YocGFnZU51bWJlcik9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBwYWdlTnVtYmVyID0gMTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZihwYWdlTnVtYmVyPjEpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBxdWVyeV9wYXJhbXMgPSBzZWxmLmpvaW5VcmxQYXJhbShxdWVyeV9wYXJhbXMsIFwic2ZfcGFnZWQ9XCIrcGFnZU51bWJlcik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2FkZCBzZmlkXG4gICAgICAgICAgICAvL3F1ZXJ5X3BhcmFtcyA9IHNlbGYuam9pblVybFBhcmFtKHF1ZXJ5X3BhcmFtcywgXCJzZmlkPVwiK3NlbGYuc2ZpZCk7XG5cbiAgICAgICAgICAgIC8vIGxvb3AgdGhyb3VnaCBhbnkgZXh0cmEgcGFyYW1zIChmcm9tIGV4dCBwbHVnaW5zKSBhbmQgYWRkIHRvIHRoZSB1cmwgKGllIHdvb2NvbW1lcmNlIGBvcmRlcmJ5YClcbiAgICAgICAgICAgIC8qdmFyIGV4dHJhX3F1ZXJ5X3BhcmFtID0gXCJcIjtcbiAgICAgICAgICAgICB2YXIgbGVuZ3RoID0gT2JqZWN0LmtleXMoc2VsZi5leHRyYV9xdWVyeV9wYXJhbXMpLmxlbmd0aDtcbiAgICAgICAgICAgICB2YXIgY291bnQgPSAwO1xuXG4gICAgICAgICAgICAgaWYobGVuZ3RoPjApXG4gICAgICAgICAgICAge1xuXG4gICAgICAgICAgICAgZm9yICh2YXIgayBpbiBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtcykge1xuICAgICAgICAgICAgIGlmIChzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtcy5oYXNPd25Qcm9wZXJ0eShrKSkge1xuXG4gICAgICAgICAgICAgaWYoc2VsZi5leHRyYV9xdWVyeV9wYXJhbXNba10hPVwiXCIpXG4gICAgICAgICAgICAge1xuICAgICAgICAgICAgIGV4dHJhX3F1ZXJ5X3BhcmFtID0gaytcIj1cIitzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtc1trXTtcbiAgICAgICAgICAgICBxdWVyeV9wYXJhbXMgPSBzZWxmLmpvaW5VcmxQYXJhbShxdWVyeV9wYXJhbXMsIGV4dHJhX3F1ZXJ5X3BhcmFtKTtcbiAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuYWRkUXVlcnlQYXJhbXMocXVlcnlfcGFyYW1zLCBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtcy5hbGwpO1xuXG4gICAgICAgICAgICBpZih0eXBlIT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIC8vcXVlcnlfcGFyYW1zID0gc2VsZi5hZGRRdWVyeVBhcmFtcyhxdWVyeV9wYXJhbXMsIHNlbGYuZXh0cmFfcXVlcnlfcGFyYW1zW3R5cGVdKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHF1ZXJ5X3BhcmFtcztcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmFkZFF1ZXJ5UGFyYW1zID0gZnVuY3Rpb24ocXVlcnlfcGFyYW1zLCBuZXdfcGFyYW1zKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgZXh0cmFfcXVlcnlfcGFyYW0gPSBcIlwiO1xuICAgICAgICAgICAgdmFyIGxlbmd0aCA9IE9iamVjdC5rZXlzKG5ld19wYXJhbXMpLmxlbmd0aDtcbiAgICAgICAgICAgIHZhciBjb3VudCA9IDA7XG5cbiAgICAgICAgICAgIGlmKGxlbmd0aD4wKVxuICAgICAgICAgICAge1xuXG4gICAgICAgICAgICAgICAgZm9yICh2YXIgayBpbiBuZXdfcGFyYW1zKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChuZXdfcGFyYW1zLmhhc093blByb3BlcnR5KGspKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKG5ld19wYXJhbXNba10hPVwiXCIpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZXh0cmFfcXVlcnlfcGFyYW0gPSBrK1wiPVwiK25ld19wYXJhbXNba107XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBleHRyYV9xdWVyeV9wYXJhbSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBxdWVyeV9wYXJhbXM7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5hZGRVcmxQYXJhbSA9IGZ1bmN0aW9uKHVybCwgc3RyaW5nKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgYWRkX3BhcmFtcyA9IFwiXCI7XG5cbiAgICAgICAgICAgIGlmKHVybCE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBpZih1cmwuaW5kZXhPZihcIj9cIikgIT0gLTEpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBhZGRfcGFyYW1zICs9IFwiJlwiO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAvL3VybCA9IHRoaXMudHJhaWxpbmdTbGFzaEl0KHVybCk7XG4gICAgICAgICAgICAgICAgICAgIGFkZF9wYXJhbXMgKz0gXCI/XCI7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihzdHJpbmchPVwiXCIpXG4gICAgICAgICAgICB7XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gdXJsICsgYWRkX3BhcmFtcyArIHN0cmluZztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdXJsO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuam9pblVybFBhcmFtID0gZnVuY3Rpb24ocGFyYW1zLCBzdHJpbmcpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHZhciBhZGRfcGFyYW1zID0gXCJcIjtcblxuICAgICAgICAgICAgaWYocGFyYW1zIT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIGFkZF9wYXJhbXMgKz0gXCImXCI7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKHN0cmluZyE9XCJcIilcbiAgICAgICAgICAgIHtcblxuICAgICAgICAgICAgICAgIHJldHVybiBwYXJhbXMgKyBhZGRfcGFyYW1zICsgc3RyaW5nO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHJldHVybiBwYXJhbXM7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5zZXRBamF4UmVzdWx0c1VSTHMgPSBmdW5jdGlvbihxdWVyeV9wYXJhbXMpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHR5cGVvZihzZWxmLmFqYXhfcmVzdWx0c19jb25mKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Byb2Nlc3NpbmdfdXJsJ10gPSBcIlwiO1xuICAgICAgICAgICAgc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsncmVzdWx0c191cmwnXSA9IFwiXCI7XG4gICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydkYXRhX3R5cGUnXSA9IFwiXCI7XG5cbiAgICAgICAgICAgIC8vaWYoc2VsZi5hamF4X3VybCE9XCJcIilcbiAgICAgICAgICAgIGlmKHNlbGYuZGlzcGxheV9yZXN1bHRfbWV0aG9kPT1cInNob3J0Y29kZVwiKVxuICAgICAgICAgICAgey8vdGhlbiB3ZSB3YW50IHRvIGRvIGEgcmVxdWVzdCB0byB0aGUgYWpheCBlbmRwb2ludFxuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHNlbGYucmVzdWx0c191cmwsIHF1ZXJ5X3BhcmFtcyk7XG5cbiAgICAgICAgICAgICAgICAvL2FkZCBsYW5nIGNvZGUgdG8gYWpheCBhcGkgcmVxdWVzdCwgbGFuZyBjb2RlIHNob3VsZCBhbHJlYWR5IGJlIGluIHRoZXJlIGZvciBvdGhlciByZXF1ZXN0cyAoaWUsIHN1cHBsaWVkIGluIHRoZSBSZXN1bHRzIFVSTClcblxuICAgICAgICAgICAgICAgIGlmKHNlbGYubGFuZ19jb2RlIT1cIlwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy9zbyBhZGQgaXRcbiAgICAgICAgICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBcImxhbmc9XCIrc2VsZi5sYW5nX2NvZGUpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Byb2Nlc3NpbmdfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHNlbGYuYWpheF91cmwsIHF1ZXJ5X3BhcmFtcyk7XG4gICAgICAgICAgICAgICAgLy9zZWxmLmFqYXhfcmVzdWx0c19jb25mWydkYXRhX3R5cGUnXSA9ICdqc29uJztcblxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihzZWxmLmRpc3BsYXlfcmVzdWx0X21ldGhvZD09XCJwb3N0X3R5cGVfYXJjaGl2ZVwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHByb2Nlc3NfZm9ybS5zZXRUYXhBcmNoaXZlUmVzdWx0c1VybChzZWxmLCBzZWxmLnJlc3VsdHNfdXJsKTtcbiAgICAgICAgICAgICAgICB2YXIgcmVzdWx0c191cmwgPSBwcm9jZXNzX2Zvcm0uZ2V0UmVzdWx0c1VybChzZWxmLCBzZWxmLnJlc3VsdHNfdXJsKTtcblxuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Byb2Nlc3NpbmdfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKHNlbGYuZGlzcGxheV9yZXN1bHRfbWV0aG9kPT1cImN1c3RvbV93b29jb21tZXJjZV9zdG9yZVwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHByb2Nlc3NfZm9ybS5zZXRUYXhBcmNoaXZlUmVzdWx0c1VybChzZWxmLCBzZWxmLnJlc3VsdHNfdXJsKTtcbiAgICAgICAgICAgICAgICB2YXIgcmVzdWx0c191cmwgPSBwcm9jZXNzX2Zvcm0uZ2V0UmVzdWx0c1VybChzZWxmLCBzZWxmLnJlc3VsdHNfdXJsKTtcblxuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Byb2Nlc3NpbmdfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHJlc3VsdHNfdXJsLCBxdWVyeV9wYXJhbXMpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICB7Ly9vdGhlcndpc2Ugd2Ugd2FudCB0byBwdWxsIHRoZSByZXN1bHRzIGRpcmVjdGx5IGZyb20gdGhlIHJlc3VsdHMgcGFnZVxuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ10gPSBzZWxmLmFkZFVybFBhcmFtKHNlbGYucmVzdWx0c191cmwsIHF1ZXJ5X3BhcmFtcyk7XG4gICAgICAgICAgICAgICAgc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsncHJvY2Vzc2luZ191cmwnXSA9IHNlbGYuYWRkVXJsUGFyYW0oc2VsZi5hamF4X3VybCwgcXVlcnlfcGFyYW1zKTtcbiAgICAgICAgICAgICAgICAvL3NlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ2RhdGFfdHlwZSddID0gJ2h0bWwnO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddID0gc2VsZi5hZGRRdWVyeVBhcmFtcyhzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddLCBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtc1snYWpheCddKTtcblxuICAgICAgICAgICAgc2VsZi5hamF4X3Jlc3VsdHNfY29uZlsnZGF0YV90eXBlJ10gPSBzZWxmLmFqYXhfZGF0YV90eXBlO1xuICAgICAgICB9O1xuXG5cblxuICAgICAgICB0aGlzLnVwZGF0ZUxvYWRlclRhZyA9IGZ1bmN0aW9uKCRvYmplY3QpIHtcblxuICAgICAgICAgICAgdmFyICRwYXJlbnQ7XG5cbiAgICAgICAgICAgIGlmKHNlbGYuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcyE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkcGFyZW50ID0gc2VsZi4kaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lci5maW5kKHNlbGYuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcykubGFzdCgpLnBhcmVudCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICRwYXJlbnQgPSBzZWxmLiRpbmZpbml0ZV9zY3JvbGxfY29udGFpbmVyO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgdGFnTmFtZSA9ICRwYXJlbnQucHJvcChcInRhZ05hbWVcIik7XG5cbiAgICAgICAgICAgIHZhciB0YWdUeXBlID0gJ2Rpdic7XG4gICAgICAgICAgICBpZiggKCB0YWdOYW1lLnRvTG93ZXJDYXNlKCkgPT0gJ29sJyApIHx8ICggdGFnTmFtZS50b0xvd2VyQ2FzZSgpID09ICd1bCcgKSApe1xuICAgICAgICAgICAgICAgIHRhZ1R5cGUgPSAnbGknO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgJG5ldyA9ICQoJzwnK3RhZ1R5cGUrJyAvPicpLmh0bWwoJG9iamVjdC5odG1sKCkpO1xuICAgICAgICAgICAgdmFyIGF0dHJpYnV0ZXMgPSAkb2JqZWN0LnByb3AoXCJhdHRyaWJ1dGVzXCIpO1xuXG4gICAgICAgICAgICAvLyBsb29wIHRocm91Z2ggPHNlbGVjdD4gYXR0cmlidXRlcyBhbmQgYXBwbHkgdGhlbSBvbiA8ZGl2PlxuICAgICAgICAgICAgJC5lYWNoKGF0dHJpYnV0ZXMsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICRuZXcuYXR0cih0aGlzLm5hbWUsIHRoaXMudmFsdWUpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiAkbmV3O1xuXG4gICAgICAgIH1cblxuXG4gICAgICAgIHRoaXMubG9hZE1vcmVSZXN1bHRzID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZiAoIHRoaXMuaXNfbWF4X3BhZ2VkID09PSB0cnVlICkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHNlbGYuaXNfbG9hZGluZ19tb3JlID0gdHJ1ZTtcblxuICAgICAgICAgICAgLy90cmlnZ2VyIHN0YXJ0IGV2ZW50XG4gICAgICAgICAgICB2YXIgZXZlbnRfZGF0YSA9IHtcbiAgICAgICAgICAgICAgICBzZmlkOiBzZWxmLnNmaWQsXG4gICAgICAgICAgICAgICAgdGFyZ2V0U2VsZWN0b3I6IHNlbGYuYWpheF90YXJnZXRfYXR0cixcbiAgICAgICAgICAgICAgICB0eXBlOiBcImxvYWRfbW9yZVwiLFxuICAgICAgICAgICAgICAgIG9iamVjdDogc2VsZlxuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4c3RhcnRcIiwgZXZlbnRfZGF0YSk7XG4gICAgICAgICAgICBwcm9jZXNzX2Zvcm0uc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICB2YXIgcXVlcnlfcGFyYW1zID0gc2VsZi5nZXRVcmxQYXJhbXModHJ1ZSk7XG4gICAgICAgICAgICBzZWxmLmxhc3Rfc3VibWl0X3F1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKGZhbHNlKTsgLy9ncmFiIGEgY29weSBvZiBodGUgVVJMIHBhcmFtcyB3aXRob3V0IHBhZ2luYXRpb24gYWxyZWFkeSBhZGRlZFxuXG4gICAgICAgICAgICB2YXIgYWpheF9wcm9jZXNzaW5nX3VybCA9IFwiXCI7XG4gICAgICAgICAgICB2YXIgYWpheF9yZXN1bHRzX3VybCA9IFwiXCI7XG4gICAgICAgICAgICB2YXIgZGF0YV90eXBlID0gXCJcIjtcblxuXG4gICAgICAgICAgICAvL25vdyBhZGQgdGhlIG5ldyBwYWdpbmF0aW9uXG4gICAgICAgICAgICB2YXIgbmV4dF9wYWdlZF9udW1iZXIgPSB0aGlzLmN1cnJlbnRfcGFnZWQgKyAxO1xuICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBcInNmX3BhZ2VkPVwiK25leHRfcGFnZWRfbnVtYmVyKTtcblxuICAgICAgICAgICAgc2VsZi5zZXRBamF4UmVzdWx0c1VSTHMocXVlcnlfcGFyYW1zKTtcbiAgICAgICAgICAgIGFqYXhfcHJvY2Vzc2luZ191cmwgPSBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddO1xuICAgICAgICAgICAgYWpheF9yZXN1bHRzX3VybCA9IHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ107XG4gICAgICAgICAgICBkYXRhX3R5cGUgPSBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydkYXRhX3R5cGUnXTtcblxuICAgICAgICAgICAgLy9hYm9ydCBhbnkgcHJldmlvdXMgYWpheCByZXF1ZXN0c1xuICAgICAgICAgICAgaWYoc2VsZi5sYXN0X2FqYXhfcmVxdWVzdClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3RfYWpheF9yZXF1ZXN0LmFib3J0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKHNlbGYudXNlX3Njcm9sbF9sb2FkZXI9PTEpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyICRsb2FkZXIgPSAkKCc8ZGl2Lz4nLHtcbiAgICAgICAgICAgICAgICAgICAgJ2NsYXNzJzogJ3NlYXJjaC1maWx0ZXItc2Nyb2xsLWxvYWRpbmcnXG4gICAgICAgICAgICAgICAgfSk7Ly8uYXBwZW5kVG8oc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lcik7XG5cbiAgICAgICAgICAgICAgICAkbG9hZGVyID0gc2VsZi51cGRhdGVMb2FkZXJUYWcoJGxvYWRlcik7XG5cbiAgICAgICAgICAgICAgICBzZWxmLmluZmluaXRlU2Nyb2xsQXBwZW5kKCRsb2FkZXIpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgc2VsZi5sYXN0X2FqYXhfcmVxdWVzdCA9ICQuZ2V0KGFqYXhfcHJvY2Vzc2luZ191cmwsIGZ1bmN0aW9uKGRhdGEsIHN0YXR1cywgcmVxdWVzdClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmN1cnJlbnRfcGFnZWQrKztcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3RfYWpheF9yZXF1ZXN0ID0gbnVsbDtcblxuICAgICAgICAgICAgICAgIC8vICoqKioqKioqKioqKioqXG4gICAgICAgICAgICAgICAgLy8gVE9ETyAtIFBBU1RFIFRISVMgQU5EIFdBVENIIFRIRSBSRURJUkVDVCAtIE9OTFkgSEFQUEVOUyBXSVRIIFdDIChDUFQgQU5EIFRBWCBET0VTIE5PVClcbiAgICAgICAgICAgICAgICAvLyBodHRwczovL3NlYXJjaC1maWx0ZXIudGVzdC9wcm9kdWN0LWNhdGVnb3J5L2Nsb3RoaW5nL3RzaGlydHMvcGFnZS8zLz9zZl9wYWdlZD0zXG5cbiAgICAgICAgICAgICAgICAvL3VwZGF0ZXMgdGhlIHJlc3V0bHMgJiBmb3JtIGh0bWxcbiAgICAgICAgICAgICAgICBzZWxmLmFkZFJlc3VsdHMoZGF0YSwgZGF0YV90eXBlKTtcblxuICAgICAgICAgICAgfSwgZGF0YV90eXBlKS5mYWlsKGZ1bmN0aW9uKGpxWEhSLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93bilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgZGF0YSA9IHt9O1xuICAgICAgICAgICAgICAgIGRhdGEuc2ZpZCA9IHNlbGYuc2ZpZDtcbiAgICAgICAgICAgICAgICBkYXRhLm9iamVjdCA9IHNlbGY7XG4gICAgICAgICAgICAgICAgZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgICAgICAgICBkYXRhLmFqYXhVUkwgPSBhamF4X3Byb2Nlc3NpbmdfdXJsO1xuICAgICAgICAgICAgICAgIGRhdGEuanFYSFIgPSBqcVhIUjtcbiAgICAgICAgICAgICAgICBkYXRhLnRleHRTdGF0dXMgPSB0ZXh0U3RhdHVzO1xuICAgICAgICAgICAgICAgIGRhdGEuZXJyb3JUaHJvd24gPSBlcnJvclRocm93bjtcbiAgICAgICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhlcnJvclwiLCBkYXRhKTtcblxuICAgICAgICAgICAgfSkuYWx3YXlzKGZ1bmN0aW9uKClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgZGF0YSA9IHt9O1xuICAgICAgICAgICAgICAgIGRhdGEuc2ZpZCA9IHNlbGYuc2ZpZDtcbiAgICAgICAgICAgICAgICBkYXRhLnRhcmdldFNlbGVjdG9yID0gc2VsZi5hamF4X3RhcmdldF9hdHRyO1xuICAgICAgICAgICAgICAgIGRhdGEub2JqZWN0ID0gc2VsZjtcblxuICAgICAgICAgICAgICAgIGlmKHNlbGYudXNlX3Njcm9sbF9sb2FkZXI9PTEpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAkbG9hZGVyLmRldGFjaCgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHNlbGYuaXNfbG9hZGluZ19tb3JlID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhmaW5pc2hcIiwgZGF0YSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9XG4gICAgICAgIHRoaXMuZmV0Y2hBamF4UmVzdWx0cyA9IGZ1bmN0aW9uKClcbiAgICAgICAge1xuICAgICAgICAgICAgLy90cmlnZ2VyIHN0YXJ0IGV2ZW50XG4gICAgICAgICAgICB2YXIgZXZlbnRfZGF0YSA9IHtcbiAgICAgICAgICAgICAgICBzZmlkOiBzZWxmLnNmaWQsXG4gICAgICAgICAgICAgICAgdGFyZ2V0U2VsZWN0b3I6IHNlbGYuYWpheF90YXJnZXRfYXR0cixcbiAgICAgICAgICAgICAgICB0eXBlOiBcImxvYWRfcmVzdWx0c1wiLFxuICAgICAgICAgICAgICAgIG9iamVjdDogc2VsZlxuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjphamF4c3RhcnRcIiwgZXZlbnRfZGF0YSk7XG5cbiAgICAgICAgICAgIC8vcmVmb2N1cyBhbnkgaW5wdXQgZmllbGRzIGFmdGVyIHRoZSBmb3JtIGhhcyBiZWVuIHVwZGF0ZWRcbiAgICAgICAgICAgIHZhciAkbGFzdF9hY3RpdmVfaW5wdXRfdGV4dCA9ICR0aGlzLmZpbmQoJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdOmZvY3VzJykubm90KFwiLnNmLWRhdGVwaWNrZXJcIik7XG4gICAgICAgICAgICBpZigkbGFzdF9hY3RpdmVfaW5wdXRfdGV4dC5sZW5ndGg9PTEpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGxhc3RfYWN0aXZlX2lucHV0X3RleHQgPSAkbGFzdF9hY3RpdmVfaW5wdXRfdGV4dC5hdHRyKFwibmFtZVwiKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJHRoaXMuYWRkQ2xhc3MoXCJzZWFyY2gtZmlsdGVyLWRpc2FibGVkXCIpO1xuICAgICAgICAgICAgcHJvY2Vzc19mb3JtLmRpc2FibGVJbnB1dHMoc2VsZik7XG5cbiAgICAgICAgICAgIC8vZmFkZSBvdXQgcmVzdWx0c1xuICAgICAgICAgICAgc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5hbmltYXRlKHsgb3BhY2l0eTogMC41IH0sIFwiZmFzdFwiKTsgLy9sb2FkaW5nXG4gICAgICAgICAgICBzZWxmLmZhZGVDb250ZW50QXJlYXMoIFwib3V0XCIgKTtcblxuICAgICAgICAgICAgaWYoc2VsZi5hamF4X2FjdGlvbj09XCJwYWdpbmF0aW9uXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgLy9uZWVkIHRvIHJlbW92ZSBhY3RpdmUgZmlsdGVyIGZyb20gVVJMXG5cbiAgICAgICAgICAgICAgICAvL3F1ZXJ5X3BhcmFtcyA9IHNlbGYubGFzdF9zdWJtaXRfcXVlcnlfcGFyYW1zO1xuXG4gICAgICAgICAgICAgICAgLy9ub3cgYWRkIHRoZSBuZXcgcGFnaW5hdGlvblxuICAgICAgICAgICAgICAgIHZhciBwYWdlTnVtYmVyID0gc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5hdHRyKFwiZGF0YS1wYWdlZFwiKTtcblxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZihwYWdlTnVtYmVyKT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHBhZ2VOdW1iZXIgPSAxO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBwcm9jZXNzX2Zvcm0uc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5nZXRVcmxQYXJhbXMoZmFsc2UpO1xuXG4gICAgICAgICAgICAgICAgaWYocGFnZU51bWJlcj4xKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBcInNmX3BhZ2VkPVwiK3BhZ2VOdW1iZXIpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihzZWxmLmFqYXhfYWN0aW9uPT1cInN1Ym1pdFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBxdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcyh0cnVlKTtcbiAgICAgICAgICAgICAgICBzZWxmLmxhc3Rfc3VibWl0X3F1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKGZhbHNlKTsgLy9ncmFiIGEgY29weSBvZiBodGUgVVJMIHBhcmFtcyB3aXRob3V0IHBhZ2luYXRpb24gYWxyZWFkeSBhZGRlZFxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgYWpheF9wcm9jZXNzaW5nX3VybCA9IFwiXCI7XG4gICAgICAgICAgICB2YXIgYWpheF9yZXN1bHRzX3VybCA9IFwiXCI7XG4gICAgICAgICAgICB2YXIgZGF0YV90eXBlID0gXCJcIjtcblxuICAgICAgICAgICAgc2VsZi5zZXRBamF4UmVzdWx0c1VSTHMocXVlcnlfcGFyYW1zKTtcbiAgICAgICAgICAgIGFqYXhfcHJvY2Vzc2luZ191cmwgPSBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydwcm9jZXNzaW5nX3VybCddO1xuICAgICAgICAgICAgYWpheF9yZXN1bHRzX3VybCA9IHNlbGYuYWpheF9yZXN1bHRzX2NvbmZbJ3Jlc3VsdHNfdXJsJ107XG4gICAgICAgICAgICBkYXRhX3R5cGUgPSBzZWxmLmFqYXhfcmVzdWx0c19jb25mWydkYXRhX3R5cGUnXTtcblxuXG4gICAgICAgICAgICAvL2Fib3J0IGFueSBwcmV2aW91cyBhamF4IHJlcXVlc3RzXG4gICAgICAgICAgICBpZihzZWxmLmxhc3RfYWpheF9yZXF1ZXN0KVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHNlbGYubGFzdF9hamF4X3JlcXVlc3QuYWJvcnQoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHZhciBhamF4X2FjdGlvbiA9IHNlbGYuYWpheF9hY3Rpb247XG4gICAgICAgICAgICBzZWxmLmxhc3RfYWpheF9yZXF1ZXN0ID0gJC5nZXQoYWpheF9wcm9jZXNzaW5nX3VybCwgZnVuY3Rpb24oZGF0YSwgc3RhdHVzLCByZXF1ZXN0KVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHNlbGYubGFzdF9hamF4X3JlcXVlc3QgPSBudWxsO1xuXG4gICAgICAgICAgICAgICAgLy91cGRhdGVzIHRoZSByZXN1dGxzICYgZm9ybSBodG1sXG4gICAgICAgICAgICAgICAgc2VsZi51cGRhdGVSZXN1bHRzKGRhdGEsIGRhdGFfdHlwZSk7XG5cbiAgICAgICAgICAgICAgICAvLyBzY3JvbGwgXG4gICAgICAgICAgICAgICAgLy8gc2V0IHRoZSB2YXIgYmFjayB0byB3aGF0IGl0IHdhcyBiZWZvcmUgdGhlIGFqYXggcmVxdWVzdCBuYWQgdGhlIGZvcm0gcmUtaW5pdFxuICAgICAgICAgICAgICAgIHNlbGYuYWpheF9hY3Rpb24gPSBhamF4X2FjdGlvbjtcbiAgICAgICAgICAgICAgICBzZWxmLnNjcm9sbFJlc3VsdHMoIHNlbGYuYWpheF9hY3Rpb24gKTtcblxuICAgICAgICAgICAgICAgIC8qIHVwZGF0ZSBVUkwgKi9cbiAgICAgICAgICAgICAgICAvL3VwZGF0ZSB1cmwgYmVmb3JlIHBhZ2luYXRpb24sIGJlY2F1c2Ugd2UgbmVlZCB0byBkbyBzb21lIGNoZWNrcyBhZ2FpbnMgdGhlIFVSTCBmb3IgaW5maW5pdGUgc2Nyb2xsXG4gICAgICAgICAgICAgICAgc2VsZi51cGRhdGVVcmxIaXN0b3J5KGFqYXhfcmVzdWx0c191cmwpO1xuXG4gICAgICAgICAgICAgICAgLy9zZXR1cCBwYWdpbmF0aW9uXG4gICAgICAgICAgICAgICAgc2VsZi5zZXR1cEFqYXhQYWdpbmF0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICBzZWxmLmlzU3VibWl0dGluZyA9IGZhbHNlO1xuXG4gICAgICAgICAgICAgICAgLyogdXNlciBkZWYgKi9cbiAgICAgICAgICAgICAgICBzZWxmLmluaXRXb29Db21tZXJjZUNvbnRyb2xzKCk7IC8vd29vY29tbWVyY2Ugb3JkZXJieVxuXG5cbiAgICAgICAgICAgIH0sIGRhdGFfdHlwZSkuZmFpbChmdW5jdGlvbihqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICBkYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgICAgICAgICAgZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgICAgICAgICBkYXRhLm9iamVjdCA9IHNlbGY7XG4gICAgICAgICAgICAgICAgZGF0YS5hamF4VVJMID0gYWpheF9wcm9jZXNzaW5nX3VybDtcbiAgICAgICAgICAgICAgICBkYXRhLmpxWEhSID0ganFYSFI7XG4gICAgICAgICAgICAgICAgZGF0YS50ZXh0U3RhdHVzID0gdGV4dFN0YXR1cztcbiAgICAgICAgICAgICAgICBkYXRhLmVycm9yVGhyb3duID0gZXJyb3JUaHJvd247XG4gICAgICAgICAgICAgICAgc2VsZi5pc1N1Ym1pdHRpbmcgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhlcnJvclwiLCBkYXRhKTtcblxuICAgICAgICAgICAgfSkuYWx3YXlzKGZ1bmN0aW9uKClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLnN0b3AodHJ1ZSx0cnVlKS5hbmltYXRlKHsgb3BhY2l0eTogMX0sIFwiZmFzdFwiKTsgLy9maW5pc2hlZCBsb2FkaW5nXG4gICAgICAgICAgICAgICAgc2VsZi5mYWRlQ29udGVudEFyZWFzKCBcImluXCIgKTtcbiAgICAgICAgICAgICAgICB2YXIgZGF0YSA9IHt9O1xuICAgICAgICAgICAgICAgIGRhdGEuc2ZpZCA9IHNlbGYuc2ZpZDtcbiAgICAgICAgICAgICAgICBkYXRhLnRhcmdldFNlbGVjdG9yID0gc2VsZi5hamF4X3RhcmdldF9hdHRyO1xuICAgICAgICAgICAgICAgIGRhdGEub2JqZWN0ID0gc2VsZjtcbiAgICAgICAgICAgICAgICAkdGhpcy5yZW1vdmVDbGFzcyhcInNlYXJjaC1maWx0ZXItZGlzYWJsZWRcIik7XG4gICAgICAgICAgICAgICAgcHJvY2Vzc19mb3JtLmVuYWJsZUlucHV0cyhzZWxmKTtcblxuICAgICAgICAgICAgICAgIC8vcmVmb2N1cyB0aGUgbGFzdCBhY3RpdmUgdGV4dCBmaWVsZFxuICAgICAgICAgICAgICAgIGlmKGxhc3RfYWN0aXZlX2lucHV0X3RleHQhPVwiXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJGlucHV0ID0gW107XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuJGZpZWxkcy5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkYWN0aXZlX2lucHV0ID0gJCh0aGlzKS5maW5kKFwiaW5wdXRbbmFtZT0nXCIrbGFzdF9hY3RpdmVfaW5wdXRfdGV4dCtcIiddXCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYoJGFjdGl2ZV9pbnB1dC5sZW5ndGg9PTEpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGlucHV0ID0gJGFjdGl2ZV9pbnB1dDtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgaWYoJGlucHV0Lmxlbmd0aD09MSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkaW5wdXQuZm9jdXMoKS52YWwoJGlucHV0LnZhbCgpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuZm9jdXNDYW1wbygkaW5wdXRbMF0pO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJHRoaXMuZmluZChcImlucHV0W25hbWU9J19zZl9zZWFyY2gnXVwiKS50cmlnZ2VyKCdmb2N1cycpO1xuICAgICAgICAgICAgICAgIHNlbGYudHJpZ2dlckV2ZW50KFwic2Y6YWpheGZpbmlzaFwiLCAgZGF0YSApO1xuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmZvY3VzQ2FtcG8gPSBmdW5jdGlvbihpbnB1dEZpZWxkKXtcbiAgICAgICAgICAgIC8vdmFyIGlucHV0RmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChpZCk7XG4gICAgICAgICAgICBpZiAoaW5wdXRGaWVsZCAhPSBudWxsICYmIGlucHV0RmllbGQudmFsdWUubGVuZ3RoICE9IDApe1xuICAgICAgICAgICAgICAgIGlmIChpbnB1dEZpZWxkLmNyZWF0ZVRleHRSYW5nZSl7XG4gICAgICAgICAgICAgICAgICAgIHZhciBGaWVsZFJhbmdlID0gaW5wdXRGaWVsZC5jcmVhdGVUZXh0UmFuZ2UoKTtcbiAgICAgICAgICAgICAgICAgICAgRmllbGRSYW5nZS5tb3ZlU3RhcnQoJ2NoYXJhY3RlcicsaW5wdXRGaWVsZC52YWx1ZS5sZW5ndGgpO1xuICAgICAgICAgICAgICAgICAgICBGaWVsZFJhbmdlLmNvbGxhcHNlKCk7XG4gICAgICAgICAgICAgICAgICAgIEZpZWxkUmFuZ2Uuc2VsZWN0KCk7XG4gICAgICAgICAgICAgICAgfWVsc2UgaWYgKGlucHV0RmllbGQuc2VsZWN0aW9uU3RhcnQgfHwgaW5wdXRGaWVsZC5zZWxlY3Rpb25TdGFydCA9PSAnMCcpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGVsZW1MZW4gPSBpbnB1dEZpZWxkLnZhbHVlLmxlbmd0aDtcbiAgICAgICAgICAgICAgICAgICAgaW5wdXRGaWVsZC5zZWxlY3Rpb25TdGFydCA9IGVsZW1MZW47XG4gICAgICAgICAgICAgICAgICAgIGlucHV0RmllbGQuc2VsZWN0aW9uRW5kID0gZWxlbUxlbjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaW5wdXRGaWVsZC5ibHVyKCk7XG4gICAgICAgICAgICAgICAgaW5wdXRGaWVsZC5mb2N1cygpO1xuICAgICAgICAgICAgfSBlbHNle1xuICAgICAgICAgICAgICAgIGlmICggaW5wdXRGaWVsZCApIHtcbiAgICAgICAgICAgICAgICAgICAgaW5wdXRGaWVsZC5mb2N1cygpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMudHJpZ2dlckV2ZW50ID0gZnVuY3Rpb24oZXZlbnRuYW1lLCBkYXRhKVxuICAgICAgICB7XG4gICAgICAgICAgICB2YXIgJGV2ZW50X2NvbnRhaW5lciA9ICQoXCIuc2VhcmNoYW5kZmlsdGVyW2RhdGEtc2YtZm9ybS1pZD0nXCIrc2VsZi5zZmlkK1wiJ11cIik7XG4gICAgICAgICAgICAkZXZlbnRfY29udGFpbmVyLnRyaWdnZXIoZXZlbnRuYW1lLCBbIGRhdGEgXSk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmZldGNoQWpheEZvcm0gPSBmdW5jdGlvbigpXG4gICAgICAgIHtcbiAgICAgICAgICAgIC8vdHJpZ2dlciBzdGFydCBldmVudFxuICAgICAgICAgICAgdmFyIGV2ZW50X2RhdGEgPSB7XG4gICAgICAgICAgICAgICAgc2ZpZDogc2VsZi5zZmlkLFxuICAgICAgICAgICAgICAgIHRhcmdldFNlbGVjdG9yOiBzZWxmLmFqYXhfdGFyZ2V0X2F0dHIsXG4gICAgICAgICAgICAgICAgdHlwZTogXCJmb3JtXCIsXG4gICAgICAgICAgICAgICAgb2JqZWN0OiBzZWxmXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhmb3Jtc3RhcnRcIiwgWyBldmVudF9kYXRhIF0pO1xuXG4gICAgICAgICAgICAkdGhpcy5hZGRDbGFzcyhcInNlYXJjaC1maWx0ZXItZGlzYWJsZWRcIik7XG4gICAgICAgICAgICBwcm9jZXNzX2Zvcm0uZGlzYWJsZUlucHV0cyhzZWxmKTtcblxuICAgICAgICAgICAgdmFyIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKCk7XG5cbiAgICAgICAgICAgIGlmKHNlbGYubGFuZ19jb2RlIT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIC8vc28gYWRkIGl0XG4gICAgICAgICAgICAgICAgcXVlcnlfcGFyYW1zID0gc2VsZi5qb2luVXJsUGFyYW0ocXVlcnlfcGFyYW1zLCBcImxhbmc9XCIrc2VsZi5sYW5nX2NvZGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgYWpheF9wcm9jZXNzaW5nX3VybCA9IHNlbGYuYWRkVXJsUGFyYW0oc2VsZi5hamF4X2Zvcm1fdXJsLCBxdWVyeV9wYXJhbXMpO1xuICAgICAgICAgICAgdmFyIGRhdGFfdHlwZSA9IFwianNvblwiO1xuXG5cbiAgICAgICAgICAgIC8vYWJvcnQgYW55IHByZXZpb3VzIGFqYXggcmVxdWVzdHNcbiAgICAgICAgICAgIC8qaWYoc2VsZi5sYXN0X2FqYXhfcmVxdWVzdClcbiAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgc2VsZi5sYXN0X2FqYXhfcmVxdWVzdC5hYm9ydCgpO1xuICAgICAgICAgICAgIH0qL1xuXG5cbiAgICAgICAgICAgIC8vc2VsZi5sYXN0X2FqYXhfcmVxdWVzdCA9XG5cbiAgICAgICAgICAgICQuZ2V0KGFqYXhfcHJvY2Vzc2luZ191cmwsIGZ1bmN0aW9uKGRhdGEsIHN0YXR1cywgcmVxdWVzdClcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAvL3NlbGYubGFzdF9hamF4X3JlcXVlc3QgPSBudWxsO1xuXG4gICAgICAgICAgICAgICAgLy91cGRhdGVzIHRoZSByZXN1dGxzICYgZm9ybSBodG1sXG4gICAgICAgICAgICAgICAgc2VsZi51cGRhdGVGb3JtKGRhdGEsIGRhdGFfdHlwZSk7XG5cblxuICAgICAgICAgICAgfSwgZGF0YV90eXBlKS5mYWlsKGZ1bmN0aW9uKGpxWEhSLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93bilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgZGF0YSA9IHt9O1xuICAgICAgICAgICAgICAgIGRhdGEuc2ZpZCA9IHNlbGYuc2ZpZDtcbiAgICAgICAgICAgICAgICBkYXRhLnRhcmdldFNlbGVjdG9yID0gc2VsZi5hamF4X3RhcmdldF9hdHRyO1xuICAgICAgICAgICAgICAgIGRhdGEub2JqZWN0ID0gc2VsZjtcbiAgICAgICAgICAgICAgICBkYXRhLmFqYXhVUkwgPSBhamF4X3Byb2Nlc3NpbmdfdXJsO1xuICAgICAgICAgICAgICAgIGRhdGEuanFYSFIgPSBqcVhIUjtcbiAgICAgICAgICAgICAgICBkYXRhLnRleHRTdGF0dXMgPSB0ZXh0U3RhdHVzO1xuICAgICAgICAgICAgICAgIGRhdGEuZXJyb3JUaHJvd24gPSBlcnJvclRocm93bjtcbiAgICAgICAgICAgICAgICBzZWxmLnRyaWdnZXJFdmVudChcInNmOmFqYXhlcnJvclwiLCBbIGRhdGEgXSk7XG5cbiAgICAgICAgICAgIH0pLmFsd2F5cyhmdW5jdGlvbigpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICBkYXRhLnNmaWQgPSBzZWxmLnNmaWQ7XG4gICAgICAgICAgICAgICAgZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgICAgICAgICBkYXRhLm9iamVjdCA9IHNlbGY7XG5cbiAgICAgICAgICAgICAgICAkdGhpcy5yZW1vdmVDbGFzcyhcInNlYXJjaC1maWx0ZXItZGlzYWJsZWRcIik7XG4gICAgICAgICAgICAgICAgcHJvY2Vzc19mb3JtLmVuYWJsZUlucHV0cyhzZWxmKTtcblxuICAgICAgICAgICAgICAgIHNlbGYudHJpZ2dlckV2ZW50KFwic2Y6YWpheGZvcm1maW5pc2hcIiwgWyBkYXRhIF0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5jb3B5TGlzdEl0ZW1zQ29udGVudHMgPSBmdW5jdGlvbigkbGlzdF9mcm9tLCAkbGlzdF90bylcbiAgICAgICAge1xuICAgICAgICAgICAgLy9jb3B5IG92ZXIgY2hpbGQgbGlzdCBpdGVtc1xuICAgICAgICAgICAgdmFyIGxpX2NvbnRlbnRzX2FycmF5ID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAgICB2YXIgZnJvbV9hdHRyaWJ1dGVzID0gbmV3IEFycmF5KCk7XG5cbiAgICAgICAgICAgIHZhciAkZnJvbV9maWVsZHMgPSAkbGlzdF9mcm9tLmZpbmQoXCI+IHVsID4gbGlcIik7XG5cbiAgICAgICAgICAgICRmcm9tX2ZpZWxkcy5lYWNoKGZ1bmN0aW9uKGkpe1xuXG4gICAgICAgICAgICAgICAgbGlfY29udGVudHNfYXJyYXkucHVzaCgkKHRoaXMpLmh0bWwoKSk7XG5cbiAgICAgICAgICAgICAgICB2YXIgYXR0cmlidXRlcyA9ICQodGhpcykucHJvcChcImF0dHJpYnV0ZXNcIik7XG4gICAgICAgICAgICAgICAgZnJvbV9hdHRyaWJ1dGVzLnB1c2goYXR0cmlidXRlcyk7XG5cbiAgICAgICAgICAgICAgICAvL3ZhciBmaWVsZF9uYW1lID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC1uYW1lXCIpO1xuICAgICAgICAgICAgICAgIC8vdmFyIHRvX2ZpZWxkID0gJGxpc3RfdG8uZmluZChcIj4gdWwgPiBsaVtkYXRhLXNmLWZpZWxkLW5hbWU9J1wiK2ZpZWxkX25hbWUrXCInXVwiKTtcblxuICAgICAgICAgICAgICAgIC8vc2VsZi5jb3B5QXR0cmlidXRlcygkKHRoaXMpLCAkbGlzdF90bywgXCJkYXRhLXNmLVwiKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHZhciBsaV9pdCA9IDA7XG4gICAgICAgICAgICB2YXIgJHRvX2ZpZWxkcyA9ICRsaXN0X3RvLmZpbmQoXCI+IHVsID4gbGlcIik7XG4gICAgICAgICAgICAkdG9fZmllbGRzLmVhY2goZnVuY3Rpb24oaSl7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5odG1sKGxpX2NvbnRlbnRzX2FycmF5W2xpX2l0XSk7XG5cbiAgICAgICAgICAgICAgICB2YXIgJGZyb21fZmllbGQgPSAkKCRmcm9tX2ZpZWxkcy5nZXQobGlfaXQpKTtcblxuICAgICAgICAgICAgICAgIHZhciAkdG9fZmllbGQgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICR0b19maWVsZC5yZW1vdmVBdHRyKFwiZGF0YS1zZi10YXhvbm9teS1hcmNoaXZlXCIpO1xuICAgICAgICAgICAgICAgIHNlbGYuY29weUF0dHJpYnV0ZXMoJGZyb21fZmllbGQsICR0b19maWVsZCk7XG5cbiAgICAgICAgICAgICAgICBsaV9pdCsrO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8qdmFyICRmcm9tX2ZpZWxkcyA9ICRsaXN0X2Zyb20uZmluZChcIiB1bCA+IGxpXCIpO1xuICAgICAgICAgICAgIHZhciAkdG9fZmllbGRzID0gJGxpc3RfdG8uZmluZChcIiA+IGxpXCIpO1xuICAgICAgICAgICAgICRmcm9tX2ZpZWxkcy5lYWNoKGZ1bmN0aW9uKGluZGV4LCB2YWwpe1xuICAgICAgICAgICAgIGlmKCQodGhpcykuaGFzQXR0cmlidXRlKFwiZGF0YS1zZi10YXhvbm9teS1hcmNoaXZlXCIpKVxuICAgICAgICAgICAgIHtcblxuICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgIHRoaXMuY29weUF0dHJpYnV0ZXMoJGxpc3RfZnJvbSwgJGxpc3RfdG8pOyovXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnVwZGF0ZUZvcm1BdHRyaWJ1dGVzID0gZnVuY3Rpb24oJGxpc3RfZnJvbSwgJGxpc3RfdG8pXG4gICAgICAgIHtcbiAgICAgICAgICAgIHZhciBmcm9tX2F0dHJpYnV0ZXMgPSAkbGlzdF9mcm9tLnByb3AoXCJhdHRyaWJ1dGVzXCIpO1xuICAgICAgICAgICAgLy8gbG9vcCB0aHJvdWdoIDxzZWxlY3Q+IGF0dHJpYnV0ZXMgYW5kIGFwcGx5IHRoZW0gb24gPGRpdj5cblxuICAgICAgICAgICAgdmFyIHRvX2F0dHJpYnV0ZXMgPSAkbGlzdF90by5wcm9wKFwiYXR0cmlidXRlc1wiKTtcbiAgICAgICAgICAgICQuZWFjaCh0b19hdHRyaWJ1dGVzLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAkbGlzdF90by5yZW1vdmVBdHRyKHRoaXMubmFtZSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJC5lYWNoKGZyb21fYXR0cmlidXRlcywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJGxpc3RfdG8uYXR0cih0aGlzLm5hbWUsIHRoaXMudmFsdWUpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuY29weUF0dHJpYnV0ZXMgPSBmdW5jdGlvbigkZnJvbSwgJHRvLCBwcmVmaXgpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHR5cGVvZihwcmVmaXgpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBwcmVmaXggPSBcIlwiO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgZnJvbV9hdHRyaWJ1dGVzID0gJGZyb20ucHJvcChcImF0dHJpYnV0ZXNcIik7XG5cbiAgICAgICAgICAgIHZhciB0b19hdHRyaWJ1dGVzID0gJHRvLnByb3AoXCJhdHRyaWJ1dGVzXCIpO1xuICAgICAgICAgICAgJC5lYWNoKHRvX2F0dHJpYnV0ZXMsIGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAgICAgaWYocHJlZml4IT1cIlwiKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmICh0aGlzLm5hbWUuaW5kZXhPZihwcmVmaXgpID09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICR0by5yZW1vdmVBdHRyKHRoaXMubmFtZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy8kdG8ucmVtb3ZlQXR0cih0aGlzLm5hbWUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkLmVhY2goZnJvbV9hdHRyaWJ1dGVzLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAkdG8uYXR0cih0aGlzLm5hbWUsIHRoaXMudmFsdWUpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmNvcHlGb3JtQXR0cmlidXRlcyA9IGZ1bmN0aW9uKCRmcm9tLCAkdG8pXG4gICAgICAgIHtcbiAgICAgICAgICAgICR0by5yZW1vdmVBdHRyKFwiZGF0YS1jdXJyZW50LXRheG9ub215LWFyY2hpdmVcIik7XG4gICAgICAgICAgICB0aGlzLmNvcHlBdHRyaWJ1dGVzKCRmcm9tLCAkdG8pO1xuXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnVwZGF0ZUZvcm0gPSBmdW5jdGlvbihkYXRhLCBkYXRhX3R5cGUpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKGRhdGFfdHlwZT09XCJqc29uXCIpXG4gICAgICAgICAgICB7Ly90aGVuIHdlIGRpZCBhIHJlcXVlc3QgdG8gdGhlIGFqYXggZW5kcG9pbnQsIHNvIGV4cGVjdCBhbiBvYmplY3QgYmFja1xuXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mKGRhdGFbJ2Zvcm0nXSkhPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgYWxsIGV2ZW50cyBmcm9tIFMmRiBmb3JtXG4gICAgICAgICAgICAgICAgICAgICR0aGlzLm9mZigpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vcmVmcmVzaCB0aGUgZm9ybSAoYXV0byBjb3VudClcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5jb3B5TGlzdEl0ZW1zQ29udGVudHMoJChkYXRhWydmb3JtJ10pLCAkdGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9yZSBpbml0IFMmRiBjbGFzcyBvbiB0aGUgZm9ybVxuICAgICAgICAgICAgICAgICAgICAvLyR0aGlzLnNlYXJjaEFuZEZpbHRlcigpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaWYgYWpheCBpcyBlbmFibGVkIGluaXQgdGhlIHBhZ2luYXRpb25cblxuICAgICAgICAgICAgICAgICAgICB0aGlzLmluaXQodHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoc2VsZi5pc19hamF4PT0xKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLnNldHVwQWpheFBhZ2luYXRpb24oKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG5cblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuXG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5hZGRSZXN1bHRzID0gZnVuY3Rpb24oZGF0YSwgZGF0YV90eXBlKVxuICAgICAgICB7XG4gICAgICAgICAgICBpZihkYXRhX3R5cGU9PVwianNvblwiKVxuICAgICAgICAgICAgey8vdGhlbiB3ZSBkaWQgYSByZXF1ZXN0IHRvIHRoZSBhamF4IGVuZHBvaW50LCBzbyBleHBlY3QgYW4gb2JqZWN0IGJhY2tcbiAgICAgICAgICAgICAgICAvL2dyYWIgdGhlIHJlc3VsdHMgYW5kIGxvYWQgaW5cbiAgICAgICAgICAgICAgICAvL3NlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuYXBwZW5kKGRhdGFbJ3Jlc3VsdHMnXSk7XG4gICAgICAgICAgICAgICAgc2VsZi5sb2FkX21vcmVfaHRtbCA9IGRhdGFbJ3Jlc3VsdHMnXTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2UgaWYoZGF0YV90eXBlPT1cImh0bWxcIilcbiAgICAgICAgICAgIHsvL3dlIGFyZSBleHBlY3RpbmcgdGhlIGh0bWwgb2YgdGhlIHJlc3VsdHMgcGFnZSBiYWNrLCBzbyBleHRyYWN0IHRoZSBodG1sIHdlIG5lZWRcbiAgICAgICAgICAgICAgICB2YXIgJGRhdGFfb2JqID0gJChkYXRhKTtcbiAgICAgICAgICAgICAgICBzZWxmLmxvYWRfbW9yZV9odG1sID0gJGRhdGFfb2JqLmZpbmQoc2VsZi5hamF4X3RhcmdldF9hdHRyKS5odG1sKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnNvbGUubG9nKFwiTkVXIEhUTUxcIiwgc2VsZi5sb2FkX21vcmVfaHRtbCk7XG5cbiAgICAgICAgICAgIHZhciBpbmZpbml0ZV9zY3JvbGxfZW5kID0gZmFsc2U7XG5cbiAgICAgICAgICAgIGlmKCQoXCI8ZGl2PlwiK3NlbGYubG9hZF9tb3JlX2h0bWwrXCI8L2Rpdj5cIikuZmluZChcIltkYXRhLXNlYXJjaC1maWx0ZXItYWN0aW9uPSdpbmZpbml0ZS1zY3JvbGwtZW5kJ11cIikubGVuZ3RoPjApXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaW5maW5pdGVfc2Nyb2xsX2VuZCA9IHRydWU7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vaWYgdGhlcmUgaXMgYW5vdGhlciBzZWxlY3RvciBmb3IgaW5maW5pdGUgc2Nyb2xsLCBmaW5kIHRoZSBjb250ZW50cyBvZiB0aGF0IGluc3RlYWRcbiAgICAgICAgICAgIGlmKHNlbGYuaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lciE9XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmxvYWRfbW9yZV9odG1sID0gJChcIjxkaXY+XCIrc2VsZi5sb2FkX21vcmVfaHRtbCtcIjwvZGl2PlwiKS5maW5kKHNlbGYuaW5maW5pdGVfc2Nyb2xsX2NvbnRhaW5lcikuaHRtbCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYoc2VsZi5pbmZpbml0ZV9zY3JvbGxfcmVzdWx0X2NsYXNzIT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciAkcmVzdWx0X2l0ZW1zID0gJChcIjxkaXY+XCIrc2VsZi5sb2FkX21vcmVfaHRtbCtcIjwvZGl2PlwiKS5maW5kKHNlbGYuaW5maW5pdGVfc2Nyb2xsX3Jlc3VsdF9jbGFzcyk7XG4gICAgICAgICAgICAgICAgdmFyICRyZXN1bHRfaXRlbXNfY29udGFpbmVyID0gJCgnPGRpdi8+Jywge30pO1xuICAgICAgICAgICAgICAgICRyZXN1bHRfaXRlbXNfY29udGFpbmVyLmFwcGVuZCgkcmVzdWx0X2l0ZW1zKTtcblxuICAgICAgICAgICAgICAgIHNlbGYubG9hZF9tb3JlX2h0bWwgPSAkcmVzdWx0X2l0ZW1zX2NvbnRhaW5lci5odG1sKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKGluZmluaXRlX3Njcm9sbF9lbmQpXG4gICAgICAgICAgICB7Ly93ZSBmb3VuZCBhIGRhdGEgYXR0cmlidXRlIHNpZ25hbGxpbmcgdGhlIGxhc3QgcGFnZSBzbyBmaW5pc2ggaGVyZVxuXG4gICAgICAgICAgICAgICAgc2VsZi5pc19tYXhfcGFnZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIHNlbGYubGFzdF9sb2FkX21vcmVfaHRtbCA9IHNlbGYubG9hZF9tb3JlX2h0bWw7XG5cbiAgICAgICAgICAgICAgICBzZWxmLmluZmluaXRlU2Nyb2xsQXBwZW5kKHNlbGYubG9hZF9tb3JlX2h0bWwpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKHNlbGYubGFzdF9sb2FkX21vcmVfaHRtbCE9PXNlbGYubG9hZF9tb3JlX2h0bWwpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgLy9jaGVjayB0byBtYWtlIHN1cmUgdGhlIG5ldyBodG1sIGZldGNoZWQgaXMgZGlmZmVyZW50XG4gICAgICAgICAgICAgICAgc2VsZi5sYXN0X2xvYWRfbW9yZV9odG1sID0gc2VsZi5sb2FkX21vcmVfaHRtbDtcbiAgICAgICAgICAgICAgICBzZWxmLmluZmluaXRlU2Nyb2xsQXBwZW5kKHNlbGYubG9hZF9tb3JlX2h0bWwpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICB7Ly93ZSByZWNlaXZlZCB0aGUgc2FtZSBtZXNzYWdlIGFnYWluIHNvIGRvbid0IGFkZCwgYW5kIHRlbGwgUyZGIHRoYXQgd2UncmUgYXQgdGhlIGVuZC4uXG4gICAgICAgICAgICAgICAgc2VsZi5pc19tYXhfcGFnZWQgPSB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cblxuICAgICAgICB0aGlzLmluZmluaXRlU2Nyb2xsQXBwZW5kID0gZnVuY3Rpb24oJG9iamVjdClcbiAgICAgICAge1xuICAgICAgICAgICAgaWYoc2VsZi5pbmZpbml0ZV9zY3JvbGxfcmVzdWx0X2NsYXNzIT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHNlbGYuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIuZmluZChzZWxmLmluZmluaXRlX3Njcm9sbF9yZXN1bHRfY2xhc3MpLmxhc3QoKS5hZnRlcigkb2JqZWN0KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgIHNlbGYuJGluZmluaXRlX3Njcm9sbF9jb250YWluZXIuYXBwZW5kKCRvYmplY3QpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cblxuICAgICAgICB0aGlzLnVwZGF0ZVJlc3VsdHMgPSBmdW5jdGlvbihkYXRhLCBkYXRhX3R5cGUpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKGRhdGFfdHlwZT09XCJqc29uXCIpXG4gICAgICAgICAgICB7Ly90aGVuIHdlIGRpZCBhIHJlcXVlc3QgdG8gdGhlIGFqYXggZW5kcG9pbnQsIHNvIGV4cGVjdCBhbiBvYmplY3QgYmFja1xuICAgICAgICAgICAgICAgIC8vZ3JhYiB0aGUgcmVzdWx0cyBhbmQgbG9hZCBpblxuICAgICAgICAgICAgICAgIHRoaXMucmVzdWx0c19odG1sID0gZGF0YVsncmVzdWx0cyddO1xuXG4gICAgICAgICAgICAgICAgaWYgKCB0aGlzLnJlcGxhY2VfcmVzdWx0cyApIHtcbiAgICAgICAgICAgICAgICAgICAgc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5odG1sKHRoaXMucmVzdWx0c19odG1sKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZih0eXBlb2YoZGF0YVsnZm9ybSddKSE9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAvL3JlbW92ZSBhbGwgZXZlbnRzIGZyb20gUyZGIGZvcm1cbiAgICAgICAgICAgICAgICAgICAgJHRoaXMub2ZmKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgcGFnaW5hdGlvblxuICAgICAgICAgICAgICAgICAgICBzZWxmLnJlbW92ZUFqYXhQYWdpbmF0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9yZWZyZXNoIHRoZSBmb3JtIChhdXRvIGNvdW50KVxuICAgICAgICAgICAgICAgICAgICBzZWxmLmNvcHlMaXN0SXRlbXNDb250ZW50cygkKGRhdGFbJ2Zvcm0nXSksICR0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3VwZGF0ZSBhdHRyaWJ1dGVzIG9uIGZvcm1cbiAgICAgICAgICAgICAgICAgICAgc2VsZi5jb3B5Rm9ybUF0dHJpYnV0ZXMoJChkYXRhWydmb3JtJ10pLCAkdGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9yZSBpbml0IFMmRiBjbGFzcyBvbiB0aGUgZm9ybVxuICAgICAgICAgICAgICAgICAgICAkdGhpcy5zZWFyY2hBbmRGaWx0ZXIoeydpc0luaXQnOiBmYWxzZX0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAvLyR0aGlzLmZpbmQoXCJpbnB1dFwiKS5yZW1vdmVBdHRyKFwiZGlzYWJsZWRcIik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSBpZihkYXRhX3R5cGU9PVwiaHRtbFwiKSB7Ly93ZSBhcmUgZXhwZWN0aW5nIHRoZSBodG1sIG9mIHRoZSByZXN1bHRzIHBhZ2UgYmFjaywgc28gZXh0cmFjdCB0aGUgaHRtbCB3ZSBuZWVkXG5cbiAgICAgICAgICAgICAgICB2YXIgJGRhdGFfb2JqID0gJChkYXRhKTtcblxuICAgICAgICAgICAgICAgIHRoaXMucmVzdWx0c19odG1sID0gJGRhdGFfb2JqLmZpbmQoIHRoaXMuYWpheF90YXJnZXRfYXR0ciApLmh0bWwoKTtcblxuICAgICAgICAgICAgICAgIGlmICggdGhpcy5yZXBsYWNlX3Jlc3VsdHMgKSB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuaHRtbCh0aGlzLnJlc3VsdHNfaHRtbCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgc2VsZi51cGRhdGVDb250ZW50QXJlYXMoICRkYXRhX29iaiApO1xuXG4gICAgICAgICAgICAgICAgaWYgKHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuZmluZChcIi5zZWFyY2hhbmRmaWx0ZXJcIikubGVuZ3RoID4gMClcbiAgICAgICAgICAgICAgICB7Ly90aGVuIHRoZXJlIGFyZSBzZWFyY2ggZm9ybShzKSBpbnNpZGUgdGhlIHJlc3VsdHMgY29udGFpbmVyLCBzbyByZS1pbml0IHRoZW1cblxuICAgICAgICAgICAgICAgICAgICBzZWxmLiRhamF4X3Jlc3VsdHNfY29udGFpbmVyLmZpbmQoXCIuc2VhcmNoYW5kZmlsdGVyXCIpLnNlYXJjaEFuZEZpbHRlcigpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vaWYgdGhlIGN1cnJlbnQgc2VhcmNoIGZvcm0gaXMgbm90IGluc2lkZSB0aGUgcmVzdWx0cyBjb250YWluZXIsIHRoZW4gcHJvY2VlZCBhcyBub3JtYWwgYW5kIHVwZGF0ZSB0aGUgZm9ybVxuICAgICAgICAgICAgICAgIGlmKHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIuZmluZChcIi5zZWFyY2hhbmRmaWx0ZXJbZGF0YS1zZi1mb3JtLWlkPSdcIiArIHNlbGYuc2ZpZCArIFwiJ11cIikubGVuZ3RoPT0wKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRuZXdfc2VhcmNoX2Zvcm0gPSAkZGF0YV9vYmouZmluZChcIi5zZWFyY2hhbmRmaWx0ZXJbZGF0YS1zZi1mb3JtLWlkPSdcIiArIHNlbGYuc2ZpZCArIFwiJ11cIik7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCRuZXdfc2VhcmNoX2Zvcm0ubGVuZ3RoID09IDEpIHsvL3RoZW4gcmVwbGFjZSB0aGUgc2VhcmNoIGZvcm0gd2l0aCB0aGUgbmV3IG9uZVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3JlbW92ZSBhbGwgZXZlbnRzIGZyb20gUyZGIGZvcm1cbiAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzLm9mZigpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3JlbW92ZSBwYWdpbmF0aW9uXG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLnJlbW92ZUFqYXhQYWdpbmF0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vcmVmcmVzaCB0aGUgZm9ybSAoYXV0byBjb3VudClcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuY29weUxpc3RJdGVtc0NvbnRlbnRzKCRuZXdfc2VhcmNoX2Zvcm0sICR0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy91cGRhdGUgYXR0cmlidXRlcyBvbiBmb3JtXG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxmLmNvcHlGb3JtQXR0cmlidXRlcygkbmV3X3NlYXJjaF9mb3JtLCAkdGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vcmUgaW5pdCBTJkYgY2xhc3Mgb24gdGhlIGZvcm1cbiAgICAgICAgICAgICAgICAgICAgICAgICR0aGlzLnNlYXJjaEFuZEZpbHRlcih7J2lzSW5pdCc6IGZhbHNlfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8kdGhpcy5maW5kKFwiaW5wdXRcIikucmVtb3ZlQXR0cihcImRpc2FibGVkXCIpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZWxmLmlzX21heF9wYWdlZCA9IGZhbHNlOyAvL2ZvciBpbmZpbml0ZSBzY3JvbGxcbiAgICAgICAgICAgIHNlbGYuY3VycmVudF9wYWdlZCA9IDE7IC8vZm9yIGluZmluaXRlIHNjcm9sbFxuICAgICAgICAgICAgc2VsZi5zZXRJbmZpbml0ZVNjcm9sbENvbnRhaW5lcigpO1xuXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnVwZGF0ZUNvbnRlbnRBcmVhcyA9IGZ1bmN0aW9uKCAkaHRtbF9kYXRhICkge1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBhZGQgYWRkaXRpb25hbCBjb250ZW50IGFyZWFzXG4gICAgICAgICAgICBpZiAoIHRoaXMuYWpheF91cGRhdGVfc2VjdGlvbnMgJiYgdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9ucy5sZW5ndGggKSB7XG4gICAgICAgICAgICAgICAgZm9yIChpbmRleCA9IDA7IGluZGV4IDwgdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9ucy5sZW5ndGg7ICsraW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlbGVjdG9yID0gdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9uc1tpbmRleF07XG4gICAgICAgICAgICAgICAgICAgICQoIHNlbGVjdG9yICkuaHRtbCggJGh0bWxfZGF0YS5maW5kKCBzZWxlY3RvciApLmh0bWwoKSApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICB0aGlzLmZhZGVDb250ZW50QXJlYXMgPSBmdW5jdGlvbiggZGlyZWN0aW9uICkge1xuICAgICAgICAgICAgXG4gICAgICAgICAgICB2YXIgb3BhY2l0eSA9IDAuNTtcbiAgICAgICAgICAgIGlmICggZGlyZWN0aW9uID09PSBcImluXCIgKSB7XG4gICAgICAgICAgICAgICAgb3BhY2l0eSA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICggdGhpcy5hamF4X3VwZGF0ZV9zZWN0aW9ucyAmJiB0aGlzLmFqYXhfdXBkYXRlX3NlY3Rpb25zLmxlbmd0aCApIHtcbiAgICAgICAgICAgICAgICBmb3IgKGluZGV4ID0gMDsgaW5kZXggPCB0aGlzLmFqYXhfdXBkYXRlX3NlY3Rpb25zLmxlbmd0aDsgKytpbmRleCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgc2VsZWN0b3IgPSB0aGlzLmFqYXhfdXBkYXRlX3NlY3Rpb25zW2luZGV4XTtcbiAgICAgICAgICAgICAgICAgICAgJCggc2VsZWN0b3IgKS5zdG9wKHRydWUsdHJ1ZSkuYW5pbWF0ZSggeyBvcGFjaXR5OiBvcGFjaXR5fSwgXCJmYXN0XCIgKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgIFxuICAgICAgICAgICAgXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnJlbW92ZVdvb0NvbW1lcmNlQ29udHJvbHMgPSBmdW5jdGlvbigpe1xuICAgICAgICAgICAgdmFyICR3b29fb3JkZXJieSA9ICQoJy53b29jb21tZXJjZS1vcmRlcmluZyAub3JkZXJieScpO1xuICAgICAgICAgICAgdmFyICR3b29fb3JkZXJieV9mb3JtID0gJCgnLndvb2NvbW1lcmNlLW9yZGVyaW5nJyk7XG5cbiAgICAgICAgICAgICR3b29fb3JkZXJieV9mb3JtLm9mZigpO1xuICAgICAgICAgICAgJHdvb19vcmRlcmJ5Lm9mZigpO1xuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuYWRkUXVlcnlQYXJhbSA9IGZ1bmN0aW9uKG5hbWUsIHZhbHVlLCB1cmxfdHlwZSl7XG5cbiAgICAgICAgICAgIGlmKHR5cGVvZih1cmxfdHlwZSk9PVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIHVybF90eXBlID0gXCJhbGxcIjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHNlbGYuZXh0cmFfcXVlcnlfcGFyYW1zW3VybF90eXBlXVtuYW1lXSA9IHZhbHVlO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5pbml0V29vQ29tbWVyY2VDb250cm9scyA9IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIHNlbGYucmVtb3ZlV29vQ29tbWVyY2VDb250cm9scygpO1xuXG4gICAgICAgICAgICB2YXIgJHdvb19vcmRlcmJ5ID0gJCgnLndvb2NvbW1lcmNlLW9yZGVyaW5nIC5vcmRlcmJ5Jyk7XG4gICAgICAgICAgICB2YXIgJHdvb19vcmRlcmJ5X2Zvcm0gPSAkKCcud29vY29tbWVyY2Utb3JkZXJpbmcnKTtcblxuICAgICAgICAgICAgdmFyIG9yZGVyX3ZhbCA9IFwiXCI7XG4gICAgICAgICAgICBpZigkd29vX29yZGVyYnkubGVuZ3RoPjApXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgb3JkZXJfdmFsID0gJHdvb19vcmRlcmJ5LnZhbCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIG9yZGVyX3ZhbCA9IHNlbGYuZ2V0UXVlcnlQYXJhbUZyb21VUkwoXCJvcmRlcmJ5XCIsIHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYob3JkZXJfdmFsPT1cIm1lbnVfb3JkZXJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBvcmRlcl92YWwgPSBcIlwiO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZigob3JkZXJfdmFsIT1cIlwiKSYmKCEhb3JkZXJfdmFsKSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtcy5hbGwub3JkZXJieSA9IG9yZGVyX3ZhbDtcbiAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICAkd29vX29yZGVyYnlfZm9ybS5vbignc3VibWl0JywgZnVuY3Rpb24oZSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgLy92YXIgZm9ybSA9IGUudGFyZ2V0O1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkd29vX29yZGVyYnkub24oXCJjaGFuZ2VcIiwgZnVuY3Rpb24oZSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgdmFsID0gJCh0aGlzKS52YWwoKTtcbiAgICAgICAgICAgICAgICBpZih2YWw9PVwibWVudV9vcmRlclwiKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgdmFsID0gXCJcIjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBzZWxmLmV4dHJhX3F1ZXJ5X3BhcmFtcy5hbGwub3JkZXJieSA9IHZhbDtcblxuICAgICAgICAgICAgICAgICR0aGlzLnRyaWdnZXIoXCJzdWJtaXRcIilcblxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNjcm9sbFJlc3VsdHMgPSBmdW5jdGlvbigpXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKChzZWxmLnNjcm9sbF9vbl9hY3Rpb249PXNlbGYuYWpheF9hY3Rpb24pfHwoc2VsZi5zY3JvbGxfb25fYWN0aW9uPT1cImFsbFwiKSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBzZWxmLnNjcm9sbFRvUG9zKCk7IC8vc2Nyb2xsIHRoZSB3aW5kb3cgaWYgaXQgaGFzIGJlZW4gc2V0XG4gICAgICAgICAgICAgICAgLy9zZWxmLmFqYXhfYWN0aW9uID0gXCJcIjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMudXBkYXRlVXJsSGlzdG9yeSA9IGZ1bmN0aW9uKGFqYXhfcmVzdWx0c191cmwpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHZhciB1c2VfaGlzdG9yeV9hcGkgPSAwO1xuICAgICAgICAgICAgaWYgKHdpbmRvdy5oaXN0b3J5ICYmIHdpbmRvdy5oaXN0b3J5LnB1c2hTdGF0ZSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB1c2VfaGlzdG9yeV9hcGkgPSAkdGhpcy5hdHRyKFwiZGF0YS11c2UtaGlzdG9yeS1hcGlcIik7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKChzZWxmLnVwZGF0ZV9hamF4X3VybD09MSkmJih1c2VfaGlzdG9yeV9hcGk9PTEpKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIC8vbm93IGNoZWNrIGlmIHRoZSBicm93c2VyIHN1cHBvcnRzIGhpc3Rvcnkgc3RhdGUgcHVzaCA6KVxuICAgICAgICAgICAgICAgIGlmICh3aW5kb3cuaGlzdG9yeSAmJiB3aW5kb3cuaGlzdG9yeS5wdXNoU3RhdGUpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBoaXN0b3J5LnB1c2hTdGF0ZShudWxsLCBudWxsLCBhamF4X3Jlc3VsdHNfdXJsKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW1vdmVBamF4UGFnaW5hdGlvbiA9IGZ1bmN0aW9uKClcbiAgICAgICAge1xuICAgICAgICAgICAgaWYodHlwZW9mKHNlbGYuYWpheF9saW5rc19zZWxlY3RvcikhPVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyICRhamF4X2xpbmtzX29iamVjdCA9IGpRdWVyeShzZWxmLmFqYXhfbGlua3Nfc2VsZWN0b3IpO1xuXG4gICAgICAgICAgICAgICAgaWYoJGFqYXhfbGlua3Nfb2JqZWN0Lmxlbmd0aD4wKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgJGFqYXhfbGlua3Nfb2JqZWN0Lm9mZigpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuZ2V0QmFzZVVybCA9IGZ1bmN0aW9uKCB1cmwgKSB7XG4gICAgICAgICAgICAvL25vdyBzZWUgaWYgd2UgYXJlIG9uIHRoZSBVUkwgd2UgdGhpbmsuLi5cbiAgICAgICAgICAgIHZhciB1cmxfcGFydHMgPSB1cmwuc3BsaXQoXCI/XCIpO1xuICAgICAgICAgICAgdmFyIHVybF9iYXNlID0gXCJcIjtcblxuICAgICAgICAgICAgaWYodXJsX3BhcnRzLmxlbmd0aD4wKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHVybF9iYXNlID0gdXJsX3BhcnRzWzBdO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgdXJsX2Jhc2UgPSB1cmw7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gdXJsX2Jhc2U7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5jYW5GZXRjaEFqYXhSZXN1bHRzID0gZnVuY3Rpb24oZmV0Y2hfdHlwZSlcbiAgICAgICAge1xuICAgICAgICAgICAgaWYodHlwZW9mKGZldGNoX3R5cGUpPT1cInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBmZXRjaF90eXBlID0gXCJcIjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIGZldGNoX2FqYXhfcmVzdWx0cyA9IGZhbHNlO1xuXG4gICAgICAgICAgICBpZihzZWxmLmlzX2FqYXg9PTEpXG4gICAgICAgICAgICB7Ly90aGVuIHdlIHdpbGwgYWpheCBzdWJtaXQgdGhlIGZvcm1cblxuICAgICAgICAgICAgICAgIC8vYW5kIGlmIHdlIGNhbiBmaW5kIHRoZSByZXN1bHRzIGNvbnRhaW5lclxuICAgICAgICAgICAgICAgIGlmKHNlbGYuJGFqYXhfcmVzdWx0c19jb250YWluZXIubGVuZ3RoPT0xKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgZmV0Y2hfYWpheF9yZXN1bHRzID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB2YXIgcmVzdWx0c191cmwgPSBzZWxmLnJlc3VsdHNfdXJsOyAgLy9cbiAgICAgICAgICAgICAgICB2YXIgcmVzdWx0c191cmxfZW5jb2RlZCA9ICcnOyAgLy9cbiAgICAgICAgICAgICAgICB2YXIgY3VycmVudF91cmwgPSB3aW5kb3cubG9jYXRpb24uaHJlZjtcblxuICAgICAgICAgICAgICAgIC8vaWdub3JlICMgYW5kIGV2ZXJ5dGhpbmcgYWZ0ZXJcbiAgICAgICAgICAgICAgICB2YXIgaGFzaF9wb3MgPSB3aW5kb3cubG9jYXRpb24uaHJlZi5pbmRleE9mKCcjJyk7XG4gICAgICAgICAgICAgICAgaWYoaGFzaF9wb3MhPT0tMSl7XG4gICAgICAgICAgICAgICAgICAgIGN1cnJlbnRfdXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWYuc3Vic3RyKDAsIHdpbmRvdy5sb2NhdGlvbi5ocmVmLmluZGV4T2YoJyMnKSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYoICggKCBzZWxmLmRpc3BsYXlfcmVzdWx0X21ldGhvZD09XCJjdXN0b21fd29vY29tbWVyY2Vfc3RvcmVcIiApIHx8ICggc2VsZi5kaXNwbGF5X3Jlc3VsdF9tZXRob2Q9PVwicG9zdF90eXBlX2FyY2hpdmVcIiApICkgJiYgKCBzZWxmLmVuYWJsZV90YXhvbm9teV9hcmNoaXZlcyA9PSAxICkgKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaWYoIHNlbGYuY3VycmVudF90YXhvbm9teV9hcmNoaXZlICE9PVwiXCIgKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBmZXRjaF9hamF4X3Jlc3VsdHMgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZldGNoX2FqYXhfcmVzdWx0cztcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8qdmFyIHJlc3VsdHNfdXJsID0gcHJvY2Vzc19mb3JtLmdldFJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgICAgICB2YXIgYWN0aXZlX3RheCA9IHByb2Nlc3NfZm9ybS5nZXRBY3RpdmVUYXgoKTtcbiAgICAgICAgICAgICAgICAgICAgIHZhciBxdWVyeV9wYXJhbXMgPSBzZWxmLmdldFVybFBhcmFtcyh0cnVlLCAnJywgYWN0aXZlX3RheCk7Ki9cbiAgICAgICAgICAgICAgICB9XG5cblxuXG5cbiAgICAgICAgICAgICAgICAvL25vdyBzZWUgaWYgd2UgYXJlIG9uIHRoZSBVUkwgd2UgdGhpbmsuLi5cbiAgICAgICAgICAgICAgICB2YXIgdXJsX2Jhc2UgPSB0aGlzLmdldEJhc2VVcmwoIGN1cnJlbnRfdXJsICk7XG4gICAgICAgICAgICAgICAgLy92YXIgcmVzdWx0c191cmxfYmFzZSA9IHRoaXMuZ2V0QmFzZVVybCggY3VycmVudF91cmwgKTtcblxuICAgICAgICAgICAgICAgIHZhciBsYW5nID0gc2VsZi5nZXRRdWVyeVBhcmFtRnJvbVVSTChcImxhbmdcIiwgd2luZG93LmxvY2F0aW9uLmhyZWYpO1xuICAgICAgICAgICAgICAgIGlmKCh0eXBlb2YobGFuZykhPT1cInVuZGVmaW5lZFwiKSYmKGxhbmchPT1udWxsKSlcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHVybF9iYXNlID0gc2VsZi5hZGRVcmxQYXJhbSh1cmxfYmFzZSwgXCJsYW5nPVwiK2xhbmcpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHZhciBzZmlkID0gc2VsZi5nZXRRdWVyeVBhcmFtRnJvbVVSTChcInNmaWRcIiwgd2luZG93LmxvY2F0aW9uLmhyZWYpO1xuXG4gICAgICAgICAgICAgICAgLy9pZiBzZmlkIGlzIGEgbnVtYmVyXG4gICAgICAgICAgICAgICAgaWYoTnVtYmVyKHBhcnNlRmxvYXQoc2ZpZCkpID09IHNmaWQpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICB1cmxfYmFzZSA9IHNlbGYuYWRkVXJsUGFyYW0odXJsX2Jhc2UsIFwic2ZpZD1cIitzZmlkKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL2lmIGFueSBvZiB0aGUgMyBjb25kaXRpb25zIGFyZSB0cnVlLCB0aGVuIGl0cyBnb29kIHRvIGdvXG4gICAgICAgICAgICAgICAgLy8gLSAxIHwgaWYgdGhlIHVybCBiYXNlID09IHJlc3VsdHNfdXJsXG4gICAgICAgICAgICAgICAgLy8gLSAyIHwgaWYgdXJsIGJhc2UrIFwiL1wiICA9PSByZXN1bHRzX3VybCAtIGluIGNhc2Ugb2YgdXNlciBlcnJvciBpbiB0aGUgcmVzdWx0cyBVUkxcbiAgICAgICAgICAgICAgICAvLyAtIDMgfCBpZiB0aGUgcmVzdWx0cyBVUkwgaGFzIHVybCBwYXJhbXMsIGFuZCB0aGUgY3VycmVudCB1cmwgc3RhcnRzIHdpdGggdGhlIHJlc3VsdHMgVVJMIFxuXG4gICAgICAgICAgICAgICAgLy90cmltIGFueSB0cmFpbGluZyBzbGFzaCBmb3IgZWFzaWVyIGNvbXBhcmlzb246XG4gICAgICAgICAgICAgICAgdXJsX2Jhc2UgPSB1cmxfYmFzZS5yZXBsYWNlKC9cXC8kLywgJycpO1xuICAgICAgICAgICAgICAgIHJlc3VsdHNfdXJsID0gcmVzdWx0c191cmwucmVwbGFjZSgvXFwvJC8sICcnKTtcbiAgICAgICAgICAgICAgICByZXN1bHRzX3VybF9lbmNvZGVkID0gZW5jb2RlVVJJKHJlc3VsdHNfdXJsKTtcbiAgICAgICAgICAgICAgICBcblxuICAgICAgICAgICAgICAgIHZhciBjdXJyZW50X3VybF9jb250YWluc19yZXN1bHRzX3VybCA9IC0xO1xuICAgICAgICAgICAgICAgIGlmKCh1cmxfYmFzZT09cmVzdWx0c191cmwpfHwodXJsX2Jhc2UudG9Mb3dlckNhc2UoKT09cmVzdWx0c191cmxfZW5jb2RlZC50b0xvd2VyQ2FzZSgpKSAgKXtcbiAgICAgICAgICAgICAgICAgICAgY3VycmVudF91cmxfY29udGFpbnNfcmVzdWx0c191cmwgPSAxO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGlmICggcmVzdWx0c191cmwuaW5kZXhPZiggJz8nICkgIT09IC0xICYmIGN1cnJlbnRfdXJsLmxhc3RJbmRleE9mKHJlc3VsdHNfdXJsLCAwKSA9PT0gMCApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRfdXJsX2NvbnRhaW5zX3Jlc3VsdHNfdXJsID0gMTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmKHNlbGYub25seV9yZXN1bHRzX2FqYXg9PTEpXG4gICAgICAgICAgICAgICAgey8vaWYgYSB1c2VyIGhhcyBjaG9zZW4gdG8gb25seSBhbGxvdyBhamF4IG9uIHJlc3VsdHMgcGFnZXMgKGRlZmF1bHQgYmVoYXZpb3VyKVxuXG4gICAgICAgICAgICAgICAgICAgIGlmKCBjdXJyZW50X3VybF9jb250YWluc19yZXN1bHRzX3VybCA+IC0xKVxuICAgICAgICAgICAgICAgICAgICB7Ly90aGlzIG1lYW5zIHRoZSBjdXJyZW50IFVSTCBjb250YWlucyB0aGUgcmVzdWx0cyB1cmwsIHdoaWNoIG1lYW5zIHdlIGNhbiBkbyBhamF4XG4gICAgICAgICAgICAgICAgICAgICAgICBmZXRjaF9hamF4X3Jlc3VsdHMgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgZmV0Y2hfYWpheF9yZXN1bHRzID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgaWYoZmV0Y2hfdHlwZT09XCJwYWdpbmF0aW9uXCIpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBjdXJyZW50X3VybF9jb250YWluc19yZXN1bHRzX3VybCA+IC0xKVxuICAgICAgICAgICAgICAgICAgICAgICAgey8vdGhpcyBtZWFucyB0aGUgY3VycmVudCBVUkwgY29udGFpbnMgdGhlIHJlc3VsdHMgdXJsLCB3aGljaCBtZWFucyB3ZSBjYW4gZG8gYWpheFxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9kb24ndCBhamF4IHBhZ2luYXRpb24gd2hlbiBub3Qgb24gYSBTJkYgcGFnZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZldGNoX2FqYXhfcmVzdWx0cyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gZmV0Y2hfYWpheF9yZXN1bHRzO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zZXR1cEFqYXhQYWdpbmF0aW9uID0gZnVuY3Rpb24oKVxuICAgICAgICB7XG4gICAgICAgICAgICAvL2luZmluaXRlIHNjcm9sbFxuICAgICAgICAgICAgaWYodGhpcy5wYWdpbmF0aW9uX3R5cGU9PT1cImluZmluaXRlX3Njcm9sbFwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBpbmZpbml0ZV9zY3JvbGxfZW5kID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgaWYoc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5maW5kKFwiW2RhdGEtc2VhcmNoLWZpbHRlci1hY3Rpb249J2luZmluaXRlLXNjcm9sbC1lbmQnXVwiKS5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGluZmluaXRlX3Njcm9sbF9lbmQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICBzZWxmLmlzX21heF9wYWdlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYocGFyc2VJbnQodGhpcy5pbnN0YW5jZV9udW1iZXIpPT09MSkge1xuICAgICAgICAgICAgICAgICAgICAkKHdpbmRvdykub2ZmKFwic2Nyb2xsXCIsIHNlbGYub25XaW5kb3dTY3JvbGwpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChzZWxmLmNhbkZldGNoQWpheFJlc3VsdHMoXCJwYWdpbmF0aW9uXCIpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHdpbmRvdykub24oXCJzY3JvbGxcIiwgc2VsZi5vbldpbmRvd1Njcm9sbCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKHR5cGVvZihzZWxmLmFqYXhfbGlua3Nfc2VsZWN0b3IpPT1cInVuZGVmaW5lZFwiKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgJChkb2N1bWVudCkub2ZmKCdjbGljaycsIHNlbGYuYWpheF9saW5rc19zZWxlY3Rvcik7XG4gICAgICAgICAgICAgICAgJChkb2N1bWVudCkub2ZmKHNlbGYuYWpheF9saW5rc19zZWxlY3Rvcik7XG4gICAgICAgICAgICAgICAgJChzZWxmLmFqYXhfbGlua3Nfc2VsZWN0b3IpLm9mZigpO1xuXG4gICAgICAgICAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgc2VsZi5hamF4X2xpbmtzX3NlbGVjdG9yLCBmdW5jdGlvbihlKXtcblxuICAgICAgICAgICAgICAgICAgICBpZihzZWxmLmNhbkZldGNoQWpheFJlc3VsdHMoXCJwYWdpbmF0aW9uXCIpKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBsaW5rID0galF1ZXJ5KHRoaXMpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuYWpheF9hY3Rpb24gPSBcInBhZ2luYXRpb25cIjtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHBhZ2VOdW1iZXIgPSBzZWxmLmdldFBhZ2VkRnJvbVVSTChsaW5rKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5hdHRyKFwiZGF0YS1wYWdlZFwiLCBwYWdlTnVtYmVyKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc2VsZi5mZXRjaEFqYXhSZXN1bHRzKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuZ2V0UGFnZWRGcm9tVVJMID0gZnVuY3Rpb24oVVJMKXtcblxuICAgICAgICAgICAgdmFyIHBhZ2VkVmFsID0gMTtcbiAgICAgICAgICAgIC8vZmlyc3QgdGVzdCB0byBzZWUgaWYgd2UgaGF2ZSBcIi9wYWdlLzQvXCIgaW4gdGhlIFVSTFxuICAgICAgICAgICAgdmFyIHRwVmFsID0gc2VsZi5nZXRRdWVyeVBhcmFtRnJvbVVSTChcInNmX3BhZ2VkXCIsIFVSTCk7XG4gICAgICAgICAgICBpZigodHlwZW9mKHRwVmFsKT09XCJzdHJpbmdcIil8fCh0eXBlb2YodHBWYWwpPT1cIm51bWJlclwiKSlcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBwYWdlZFZhbCA9IHRwVmFsO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gcGFnZWRWYWw7XG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5nZXRRdWVyeVBhcmFtRnJvbVVSTCA9IGZ1bmN0aW9uKG5hbWUsIFVSTCl7XG5cbiAgICAgICAgICAgIHZhciBxc3RyaW5nID0gXCI/XCIrVVJMLnNwbGl0KCc/JylbMV07XG4gICAgICAgICAgICBpZih0eXBlb2YocXN0cmluZykhPVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdmFyIHZhbCA9IGRlY29kZVVSSUNvbXBvbmVudCgobmV3IFJlZ0V4cCgnWz98Jl0nICsgbmFtZSArICc9JyArICcoW14mO10rPykoJnwjfDt8JCknKS5leGVjKHFzdHJpbmcpfHxbLFwiXCJdKVsxXS5yZXBsYWNlKC9cXCsvZywgJyUyMCcpKXx8bnVsbDtcbiAgICAgICAgICAgICAgICByZXR1cm4gdmFsO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIFwiXCI7XG4gICAgICAgIH07XG5cblxuXG4gICAgICAgIHRoaXMuZm9ybVVwZGF0ZWQgPSBmdW5jdGlvbihlKXtcblxuICAgICAgICAgICAgLy9lLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBpZihzZWxmLmF1dG9fdXBkYXRlPT0xKSB7XG4gICAgICAgICAgICAgICAgc2VsZi5zdWJtaXRGb3JtKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKChzZWxmLmF1dG9fdXBkYXRlPT0wKSYmKHNlbGYuYXV0b19jb3VudF9yZWZyZXNoX21vZGU9PTEpKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHNlbGYuZm9ybVVwZGF0ZWRGZXRjaEFqYXgoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuZm9ybVVwZGF0ZWRGZXRjaEFqYXggPSBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAvL2xvb3AgdGhyb3VnaCBhbGwgdGhlIGZpZWxkcyBhbmQgYnVpbGQgdGhlIFVSTFxuICAgICAgICAgICAgc2VsZi5mZXRjaEFqYXhGb3JtKCk7XG5cblxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vbWFrZSBhbnkgY29ycmVjdGlvbnMvdXBkYXRlcyB0byBmaWVsZHMgYmVmb3JlIHRoZSBzdWJtaXQgY29tcGxldGVzXG4gICAgICAgIHRoaXMuc2V0RmllbGRzID0gZnVuY3Rpb24oZSl7XG5cbiAgICAgICAgICAgIC8vaWYoc2VsZi5pc19hamF4PT0wKSB7XG5cbiAgICAgICAgICAgICAgICAvL3NvbWV0aW1lcyB0aGUgZm9ybSBpcyBzdWJtaXR0ZWQgd2l0aG91dCB0aGUgc2xpZGVyIHlldCBoYXZpbmcgdXBkYXRlZCwgYW5kIGFzIHdlIGdldCBvdXIgdmFsdWVzIGZyb21cbiAgICAgICAgICAgICAgICAvL3RoZSBzbGlkZXIgYW5kIG5vdCBpbnB1dHMsIHdlIG5lZWQgdG8gY2hlY2sgaXQgaWYgbmVlZHMgdG8gYmUgc2V0XG4gICAgICAgICAgICAgICAgLy9vbmx5IG9jY3VycyBpZiBhamF4IGlzIG9mZiwgYW5kIGF1dG9zdWJtaXQgb25cbiAgICAgICAgICAgICAgICBzZWxmLiRmaWVsZHMuZWFjaChmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJGZpZWxkID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgcmFuZ2VfZGlzcGxheV92YWx1ZXMgPSAkZmllbGQuZmluZCgnLnNmLW1ldGEtcmFuZ2Utc2xpZGVyJykuYXR0cihcImRhdGEtZGlzcGxheS12YWx1ZXMtYXNcIik7Ly9kYXRhLWRpc3BsYXktdmFsdWVzLWFzPVwidGV4dFwiXG5cbiAgICAgICAgICAgICAgICAgICAgaWYocmFuZ2VfZGlzcGxheV92YWx1ZXM9PT1cInRleHRpbnB1dFwiKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCRmaWVsZC5maW5kKFwiLm1ldGEtc2xpZGVyXCIpLmxlbmd0aD4wKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgJGZpZWxkLmZpbmQoXCIubWV0YS1zbGlkZXJcIikuZWFjaChmdW5jdGlvbiAoaW5kZXgpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBzbGlkZXJfb2JqZWN0ID0gJCh0aGlzKVswXTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHNsaWRlcl9lbCA9ICQodGhpcykuY2xvc2VzdChcIi5zZi1tZXRhLXJhbmdlLXNsaWRlclwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL3ZhciBtaW5WYWwgPSAkc2xpZGVyX2VsLmF0dHIoXCJkYXRhLW1pblwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL3ZhciBtYXhWYWwgPSAkc2xpZGVyX2VsLmF0dHIoXCJkYXRhLW1heFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgbWluVmFsID0gJHNsaWRlcl9lbC5maW5kKFwiLnNmLXJhbmdlLW1pblwiKS52YWwoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgbWF4VmFsID0gJHNsaWRlcl9lbC5maW5kKFwiLnNmLXJhbmdlLW1heFwiKS52YWwoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuc2V0KFttaW5WYWwsIG1heFZhbF0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgLy99XG5cbiAgICAgICAgfVxuXG4gICAgICAgIC8vc3VibWl0XG4gICAgICAgIHRoaXMuc3VibWl0Rm9ybSA9IGZ1bmN0aW9uKGUpe1xuXG4gICAgICAgICAgICAvL2xvb3AgdGhyb3VnaCBhbGwgdGhlIGZpZWxkcyBhbmQgYnVpbGQgdGhlIFVSTFxuICAgICAgICAgICAgaWYoc2VsZi5pc1N1Ym1pdHRpbmcgPT0gdHJ1ZSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2VsZi5zZXRGaWVsZHMoKTtcbiAgICAgICAgICAgIHNlbGYuY2xlYXJUaW1lcigpO1xuXG4gICAgICAgICAgICBzZWxmLmlzU3VibWl0dGluZyA9IHRydWU7XG5cbiAgICAgICAgICAgIHByb2Nlc3NfZm9ybS5zZXRUYXhBcmNoaXZlUmVzdWx0c1VybChzZWxmLCBzZWxmLnJlc3VsdHNfdXJsKTtcblxuICAgICAgICAgICAgc2VsZi4kYWpheF9yZXN1bHRzX2NvbnRhaW5lci5hdHRyKFwiZGF0YS1wYWdlZFwiLCAxKTsgLy9pbml0IHBhZ2VkXG5cbiAgICAgICAgICAgIGlmKHNlbGYuY2FuRmV0Y2hBamF4UmVzdWx0cygpKVxuICAgICAgICAgICAgey8vdGhlbiB3ZSB3aWxsIGFqYXggc3VibWl0IHRoZSBmb3JtXG5cbiAgICAgICAgICAgICAgICBzZWxmLmFqYXhfYWN0aW9uID0gXCJzdWJtaXRcIjsgLy9zbyB3ZSBrbm93IGl0IHdhc24ndCBwYWdpbmF0aW9uXG4gICAgICAgICAgICAgICAgc2VsZi5mZXRjaEFqYXhSZXN1bHRzKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlXG4gICAgICAgICAgICB7Ly90aGVuIHdlIHdpbGwgc2ltcGx5IHJlZGlyZWN0IHRvIHRoZSBSZXN1bHRzIFVSTFxuXG4gICAgICAgICAgICAgICAgdmFyIHJlc3VsdHNfdXJsID0gcHJvY2Vzc19mb3JtLmdldFJlc3VsdHNVcmwoc2VsZiwgc2VsZi5yZXN1bHRzX3VybCk7XG4gICAgICAgICAgICAgICAgdmFyIHF1ZXJ5X3BhcmFtcyA9IHNlbGYuZ2V0VXJsUGFyYW1zKHRydWUsICcnKTtcbiAgICAgICAgICAgICAgICByZXN1bHRzX3VybCA9IHNlbGYuYWRkVXJsUGFyYW0ocmVzdWx0c191cmwsIHF1ZXJ5X3BhcmFtcyk7XG5cbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IHJlc3VsdHNfdXJsO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH07XG4gICAgICAgIHRoaXMucmVzZXRGb3JtID0gZnVuY3Rpb24oc3VibWl0X2Zvcm0pXG4gICAgICAgIHtcbiAgICAgICAgICAgIC8vdW5zZXQgYWxsIGZpZWxkc1xuICAgICAgICAgICAgc2VsZi4kZmllbGRzLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIHZhciAkZmllbGQgPSAkKHRoaXMpO1xuXHRcdFx0XHRcblx0XHRcdFx0JGZpZWxkLnJlbW92ZUF0dHIoXCJkYXRhLXNmLXRheG9ub215LWFyY2hpdmVcIik7XG5cdFx0XHRcdFxuICAgICAgICAgICAgICAgIC8vc3RhbmRhcmQgZmllbGQgdHlwZXNcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcInNlbGVjdDpub3QoW211bHRpcGxlPSdtdWx0aXBsZSddKSA+IG9wdGlvbjpmaXJzdC1jaGlsZFwiKS5wcm9wKFwic2VsZWN0ZWRcIiwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgJGZpZWxkLmZpbmQoXCJzZWxlY3RbbXVsdGlwbGU9J211bHRpcGxlJ10gPiBvcHRpb25cIikucHJvcChcInNlbGVjdGVkXCIsIGZhbHNlKTtcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcImlucHV0W3R5cGU9J2NoZWNrYm94J11cIikucHJvcChcImNoZWNrZWRcIiwgZmFsc2UpO1xuICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwiPiB1bCA+IGxpOmZpcnN0LWNoaWxkIGlucHV0W3R5cGU9J3JhZGlvJ11cIikucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgJGZpZWxkLmZpbmQoXCJpbnB1dFt0eXBlPSd0ZXh0J11cIikudmFsKFwiXCIpO1xuICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwiLnNmLW9wdGlvbi1hY3RpdmVcIikucmVtb3ZlQ2xhc3MoXCJzZi1vcHRpb24tYWN0aXZlXCIpO1xuICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwiPiB1bCA+IGxpOmZpcnN0LWNoaWxkIGlucHV0W3R5cGU9J3JhZGlvJ11cIikucGFyZW50KCkuYWRkQ2xhc3MoXCJzZi1vcHRpb24tYWN0aXZlXCIpOyAvL3JlIGFkZCBhY3RpdmUgY2xhc3MgdG8gZmlyc3QgXCJkZWZhdWx0XCIgb3B0aW9uXG5cbiAgICAgICAgICAgICAgICAvL251bWJlciByYW5nZSAtIDIgbnVtYmVyIGlucHV0IGZpZWxkc1xuICAgICAgICAgICAgICAgICRmaWVsZC5maW5kKFwiaW5wdXRbdHlwZT0nbnVtYmVyJ11cIikuZWFjaChmdW5jdGlvbihpbmRleCl7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICR0aGlzSW5wdXQgPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCR0aGlzSW5wdXQucGFyZW50KCkucGFyZW50KCkuaGFzQ2xhc3MoXCJzZi1tZXRhLXJhbmdlXCIpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKGluZGV4PT0wKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNJbnB1dC52YWwoJHRoaXNJbnB1dC5hdHRyKFwibWluXCIpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGVsc2UgaWYoaW5kZXg9PTEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkdGhpc0lucHV0LnZhbCgkdGhpc0lucHV0LmF0dHIoXCJtYXhcIikpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vbWV0YSAvIG51bWJlcnMgd2l0aCAyIGlucHV0cyAoZnJvbSAvIHRvIGZpZWxkcykgLSBzZWNvbmQgaW5wdXQgbXVzdCBiZSByZXNldCB0byBtYXggdmFsdWVcbiAgICAgICAgICAgICAgICB2YXIgJG1ldGFfc2VsZWN0X2Zyb21fdG8gPSAkZmllbGQuZmluZChcIi5zZi1tZXRhLXJhbmdlLXNlbGVjdC1mcm9tdG9cIik7XG5cbiAgICAgICAgICAgICAgICBpZigkbWV0YV9zZWxlY3RfZnJvbV90by5sZW5ndGg+MCkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBzdGFydF9taW4gPSAkbWV0YV9zZWxlY3RfZnJvbV90by5hdHRyKFwiZGF0YS1taW5cIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBzdGFydF9tYXggPSAkbWV0YV9zZWxlY3RfZnJvbV90by5hdHRyKFwiZGF0YS1tYXhcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgJG1ldGFfc2VsZWN0X2Zyb21fdG8uZmluZChcInNlbGVjdFwiKS5lYWNoKGZ1bmN0aW9uKGluZGV4KXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICR0aGlzSW5wdXQgPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihpbmRleD09MCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNJbnB1dC52YWwoc3RhcnRfbWluKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGVsc2UgaWYoaW5kZXg9PTEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkdGhpc0lucHV0LnZhbChzdGFydF9tYXgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHZhciAkbWV0YV9yYWRpb19mcm9tX3RvID0gJGZpZWxkLmZpbmQoXCIuc2YtbWV0YS1yYW5nZS1yYWRpby1mcm9tdG9cIik7XG5cbiAgICAgICAgICAgICAgICBpZigkbWV0YV9yYWRpb19mcm9tX3RvLmxlbmd0aD4wKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHN0YXJ0X21pbiA9ICRtZXRhX3JhZGlvX2Zyb21fdG8uYXR0cihcImRhdGEtbWluXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgc3RhcnRfbWF4ID0gJG1ldGFfcmFkaW9fZnJvbV90by5hdHRyKFwiZGF0YS1tYXhcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRyYWRpb19ncm91cHMgPSAkbWV0YV9yYWRpb19mcm9tX3RvLmZpbmQoJy5zZi1pbnB1dC1yYW5nZS1yYWRpbycpO1xuXG4gICAgICAgICAgICAgICAgICAgICRyYWRpb19ncm91cHMuZWFjaChmdW5jdGlvbihpbmRleCl7XG5cblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRyYWRpb3MgPSAkKHRoaXMpLmZpbmQoXCIuc2YtaW5wdXQtcmFkaW9cIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAkcmFkaW9zLnByb3AoXCJjaGVja2VkXCIsIGZhbHNlKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoaW5kZXg9PTApXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHJhZGlvcy5maWx0ZXIoJ1t2YWx1ZT1cIicrc3RhcnRfbWluKydcIl0nKS5wcm9wKFwiY2hlY2tlZFwiLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGVsc2UgaWYoaW5kZXg9PTEpXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHJhZGlvcy5maWx0ZXIoJ1t2YWx1ZT1cIicrc3RhcnRfbWF4KydcIl0nKS5wcm9wKFwiY2hlY2tlZFwiLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vbnVtYmVyIHNsaWRlciAtIG5vVWlTbGlkZXJcbiAgICAgICAgICAgICAgICAkZmllbGQuZmluZChcIi5tZXRhLXNsaWRlclwiKS5lYWNoKGZ1bmN0aW9uKGluZGV4KXtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgc2xpZGVyX29iamVjdCA9ICQodGhpcylbMF07XG4gICAgICAgICAgICAgICAgICAgIC8qdmFyIHNsaWRlcl9vYmplY3QgPSAkY29udGFpbmVyLmZpbmQoXCIubWV0YS1zbGlkZXJcIilbMF07XG4gICAgICAgICAgICAgICAgICAgICB2YXIgc2xpZGVyX3ZhbCA9IHNsaWRlcl9vYmplY3Qubm9VaVNsaWRlci5nZXQoKTsqL1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciAkc2xpZGVyX2VsID0gJCh0aGlzKS5jbG9zZXN0KFwiLnNmLW1ldGEtcmFuZ2Utc2xpZGVyXCIpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgbWluVmFsID0gJHNsaWRlcl9lbC5hdHRyKFwiZGF0YS1taW5cIik7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtYXhWYWwgPSAkc2xpZGVyX2VsLmF0dHIoXCJkYXRhLW1heFwiKTtcbiAgICAgICAgICAgICAgICAgICAgc2xpZGVyX29iamVjdC5ub1VpU2xpZGVyLnNldChbbWluVmFsLCBtYXhWYWxdKTtcblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy9uZWVkIHRvIHNlZSBpZiBhbnkgYXJlIGNvbWJvYm94IGFuZCBhY3QgYWNjb3JkaW5nbHlcbiAgICAgICAgICAgICAgICB2YXIgJGNvbWJvYm94ID0gJGZpZWxkLmZpbmQoXCJzZWxlY3RbZGF0YS1jb21ib2JveD0nMSddXCIpO1xuICAgICAgICAgICAgICAgIGlmKCRjb21ib2JveC5sZW5ndGg+MClcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgJGNvbWJvYm94LmNob3NlbiAhPSBcInVuZGVmaW5lZFwiKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkY29tYm9ib3gudHJpZ2dlcihcImNob3Nlbjp1cGRhdGVkXCIpOyAvL2ZvciBjaG9zZW4gb25seVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGNvbWJvYm94LnZhbCgnJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkY29tYm9ib3gudHJpZ2dlcignY2hhbmdlLnNlbGVjdDInKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIHNlbGYuY2xlYXJUaW1lcigpO1xuXG5cblxuICAgICAgICAgICAgaWYoc3VibWl0X2Zvcm09PVwiYWx3YXlzXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgc2VsZi5zdWJtaXRGb3JtKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKHN1Ym1pdF9mb3JtPT1cIm5ldmVyXCIpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaWYodGhpcy5hdXRvX2NvdW50X3JlZnJlc2hfbW9kZT09MSlcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYuZm9ybVVwZGF0ZWRGZXRjaEFqYXgoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIGlmKHN1Ym1pdF9mb3JtPT1cImF1dG9cIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBpZih0aGlzLmF1dG9fdXBkYXRlPT10cnVlKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zdWJtaXRGb3JtKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGlmKHRoaXMuYXV0b19jb3VudF9yZWZyZXNoX21vZGU9PTEpXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuZm9ybVVwZGF0ZWRGZXRjaEFqYXgoKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIHRoaXMuaW5pdCgpO1xuXG4gICAgICAgIHZhciBldmVudF9kYXRhID0ge307XG4gICAgICAgIGV2ZW50X2RhdGEuc2ZpZCA9IHNlbGYuc2ZpZDtcbiAgICAgICAgZXZlbnRfZGF0YS50YXJnZXRTZWxlY3RvciA9IHNlbGYuYWpheF90YXJnZXRfYXR0cjtcbiAgICAgICAgZXZlbnRfZGF0YS5vYmplY3QgPSB0aGlzO1xuICAgICAgICBpZihvcHRzLmlzSW5pdClcbiAgICAgICAge1xuICAgICAgICAgICAgc2VsZi50cmlnZ2VyRXZlbnQoXCJzZjppbml0XCIsIGV2ZW50X2RhdGEpO1xuICAgICAgICB9XG5cbiAgICB9KTtcbn07XG5cbn0pLmNhbGwodGhpcyx0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsIDogdHlwZW9mIHNlbGYgIT09IFwidW5kZWZpbmVkXCIgPyBzZWxmIDogdHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvdyA6IHt9KVxuLy8jIHNvdXJjZU1hcHBpbmdVUkw9ZGF0YTphcHBsaWNhdGlvbi9qc29uO2NoYXJzZXQ6dXRmLTg7YmFzZTY0LGV5SjJaWEp6YVc5dUlqb3pMQ0p6YjNWeVkyVnpJanBiSW5OeVl5OXdkV0pzYVdNdllYTnpaWFJ6TDJwekwybHVZMngxWkdWekwzQnNkV2RwYmk1cWN5SmRMQ0p1WVcxbGN5STZXMTBzSW0xaGNIQnBibWR6SWpvaU8wRkJRVUU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVNJc0ltWnBiR1VpT2lKblpXNWxjbUYwWldRdWFuTWlMQ0p6YjNWeVkyVlNiMjkwSWpvaUlpd2ljMjkxY21ObGMwTnZiblJsYm5RaU9sc2lYRzUyWVhJZ0pDQmNkRngwWEhSY2REMGdLSFI1Y0dWdlppQjNhVzVrYjNjZ0lUMDlJRndpZFc1a1pXWnBibVZrWENJZ1B5QjNhVzVrYjNkYkoycFJkV1Z5ZVNkZElEb2dkSGx3Wlc5bUlHZHNiMkpoYkNBaFBUMGdYQ0oxYm1SbFptbHVaV1JjSWlBL0lHZHNiMkpoYkZzbmFsRjFaWEo1SjEwZ09pQnVkV3hzS1R0Y2JuWmhjaUJ6ZEdGMFpTQmNkRngwWEhROUlISmxjWFZwY21Vb0p5NHZjM1JoZEdVbktUdGNiblpoY2lCd2NtOWpaWE56WDJadmNtMGdYSFE5SUhKbGNYVnBjbVVvSnk0dmNISnZZMlZ6YzE5bWIzSnRKeWs3WEc1MllYSWdibTlWYVZOc2FXUmxjbHgwWEhROUlISmxjWFZwY21Vb0oyNXZkV2x6Ykdsa1pYSW5LVHRjYmk4dmRtRnlJR052YjJ0cFpYTWdJQ0FnSUNBZ0lDQTlJSEpsY1hWcGNtVW9KMnB6TFdOdmIydHBaU2NwTzF4dWRtRnlJSFJvYVhKa1VHRnlkSGtnSUNBZ0lDQTlJSEpsY1hWcGNtVW9KeTR2ZEdocGNtUndZWEowZVNjcE8xeHVYRzUzYVc1a2IzY3VjMlZoY21Ob1FXNWtSbWxzZEdWeUlEMGdlMXh1SUNBZ0lHVjRkR1Z1YzJsdmJuTTZJRnRkTEZ4dUlDQWdJSEpsWjJsemRHVnlSWGgwWlc1emFXOXVPaUJtZFc1amRHbHZiaWdnWlhoMFpXNXphVzl1VG1GdFpTQXBJSHRjYmlBZ0lDQWdJQ0FnZEdocGN5NWxlSFJsYm5OcGIyNXpMbkIxYzJnb0lHVjRkR1Z1YzJsdmJrNWhiV1VnS1R0Y2JpQWdJQ0I5WEc1OU8xeHVYRzV0YjJSMWJHVXVaWGh3YjNKMGN5QTlJR1oxYm1OMGFXOXVLRzl3ZEdsdmJuTXBYRzU3WEc0Z0lDQWdkbUZ5SUdSbFptRjFiSFJ6SUQwZ2UxeHVJQ0FnSUNBZ0lDQnpkR0Z5ZEU5d1pXNWxaRG9nWm1Gc2MyVXNYRzRnSUNBZ0lDQWdJR2x6U1c1cGREb2dkSEoxWlN4Y2JpQWdJQ0FnSUNBZ1lXTjBhVzl1T2lCY0lsd2lYRzRnSUNBZ2ZUdGNibHh1SUNBZ0lIWmhjaUJ2Y0hSeklEMGdhbEYxWlhKNUxtVjRkR1Z1WkNoa1pXWmhkV3gwY3l3Z2IzQjBhVzl1Y3lrN1hHNGdJQ0FnWEc0Z0lDQWdkR2hwY21SUVlYSjBlUzVwYm1sMEtDazdYRzRnSUNBZ1hHNGdJQ0FnTHk5c2IyOXdJSFJvY205MVoyZ2daV0ZqYUNCcGRHVnRJRzFoZEdOb1pXUmNiaUFnSUNCMGFHbHpMbVZoWTJnb1puVnVZM1JwYjI0b0tWeHVJQ0FnSUh0Y2JseHVJQ0FnSUNBZ0lDQjJZWElnSkhSb2FYTWdQU0FrS0hSb2FYTXBPMXh1SUNBZ0lDQWdJQ0IyWVhJZ2MyVnNaaUE5SUhSb2FYTTdYRzRnSUNBZ0lDQWdJSFJvYVhNdWMyWnBaQ0E5SUNSMGFHbHpMbUYwZEhJb1hDSmtZWFJoTFhObUxXWnZjbTB0YVdSY0lpazdYRzVjYmlBZ0lDQWdJQ0FnYzNSaGRHVXVZV1JrVTJWaGNtTm9SbTl5YlNoMGFHbHpMbk5tYVdRc0lIUm9hWE1wTzF4dVhHNGdJQ0FnSUNBZ0lIUm9hWE11SkdacFpXeGtjeUE5SUNSMGFHbHpMbVpwYm1Rb1hDSStJSFZzSUQ0Z2JHbGNJaWs3SUM4dllTQnlaV1psY21WdVkyVWdkRzhnWldGamFDQm1hV1ZzWkhNZ2NHRnlaVzUwSUV4SlhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1bGJtRmliR1ZmZEdGNGIyNXZiWGxmWVhKamFHbDJaWE1nUFNBa2RHaHBjeTVoZEhSeUtDZGtZWFJoTFhSaGVHOXViMjE1TFdGeVkyaHBkbVZ6SnlrN1hHNGdJQ0FnSUNBZ0lIUm9hWE11WTNWeWNtVnVkRjkwWVhodmJtOXRlVjloY21Ob2FYWmxJRDBnSkhSb2FYTXVZWFIwY2lnblpHRjBZUzFqZFhKeVpXNTBMWFJoZUc5dWIyMTVMV0Z5WTJocGRtVW5LVHRjYmx4dUlDQWdJQ0FnSUNCcFppaDBlWEJsYjJZb2RHaHBjeTVsYm1GaWJHVmZkR0Y0YjI1dmJYbGZZWEpqYUdsMlpYTXBQVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbVZ1WVdKc1pWOTBZWGh2Ym05dGVWOWhjbU5vYVhabGN5QTlJRndpTUZ3aU8xeHVJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJR2xtS0hSNWNHVnZaaWgwYUdsekxtTjFjbkpsYm5SZmRHRjRiMjV2YlhsZllYSmphR2wyWlNrOVBWd2lkVzVrWldacGJtVmtYQ0lwWEc0Z0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVkzVnljbVZ1ZEY5MFlYaHZibTl0ZVY5aGNtTm9hWFpsSUQwZ1hDSmNJanRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lIQnliMk5sYzNOZlptOXliUzVwYm1sMEtITmxiR1l1Wlc1aFlteGxYM1JoZUc5dWIyMTVYMkZ5WTJocGRtVnpMQ0J6Wld4bUxtTjFjbkpsYm5SZmRHRjRiMjV2YlhsZllYSmphR2wyWlNrN1hHNGdJQ0FnSUNBZ0lDOHZjSEp2WTJWemMxOW1iM0p0TG5ObGRGUmhlRUZ5WTJocGRtVlNaWE4xYkhSelZYSnNLSE5sYkdZcE8xeHVJQ0FnSUNBZ0lDQndjbTlqWlhOelgyWnZjbTB1Wlc1aFlteGxTVzV3ZFhSektITmxiR1lwTzF4dVhHNGdJQ0FnSUNBZ0lHbG1LSFI1Y0dWdlppaDBhR2x6TG1WNGRISmhYM0YxWlhKNVgzQmhjbUZ0Y3lrOVBWd2lkVzVrWldacGJtVmtYQ0lwWEc0Z0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdVpYaDBjbUZmY1hWbGNubGZjR0Z5WVcxeklEMGdlMkZzYkRvZ2UzMHNJSEpsYzNWc2RITTZJSHQ5TENCaGFtRjRPaUI3ZlgwN1hHNGdJQ0FnSUNBZ0lIMWNibHh1WEc0Z0lDQWdJQ0FnSUhSb2FYTXVkR1Z0Y0d4aGRHVmZhWE5mYkc5aFpHVmtJRDBnSkhSb2FYTXVZWFIwY2loY0ltUmhkR0V0ZEdWdGNHeGhkR1V0Ykc5aFpHVmtYQ0lwTzF4dUlDQWdJQ0FnSUNCMGFHbHpMbWx6WDJGcVlYZ2dQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzFoYW1GNFhDSXBPMXh1SUNBZ0lDQWdJQ0IwYUdsekxtbHVjM1JoYm1ObFgyNTFiV0psY2lBOUlDUjBhR2x6TG1GMGRISW9KMlJoZEdFdGFXNXpkR0Z1WTJVdFkyOTFiblFuS1R0Y2JpQWdJQ0FnSUNBZ2RHaHBjeTRrWVdwaGVGOXlaWE4xYkhSelgyTnZiblJoYVc1bGNpQTlJR3BSZFdWeWVTZ2tkR2hwY3k1aGRIUnlLRndpWkdGMFlTMWhhbUY0TFhSaGNtZGxkRndpS1NrN1hHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1aGFtRjRYM1Z3WkdGMFpWOXpaV04wYVc5dWN5QTlJQ1IwYUdsekxtRjBkSElvWENKa1lYUmhMV0ZxWVhndGRYQmtZWFJsTFhObFkzUnBiMjV6WENJcElEOGdTbE5QVGk1d1lYSnpaU2dnSkhSb2FYTXVZWFIwY2loY0ltUmhkR0V0WVdwaGVDMTFjR1JoZEdVdGMyVmpkR2x2Ym5OY0lpa2dLU0E2SUZ0ZE8xeHVJQ0FnSUNBZ0lDQjBhR2x6TG5KbGNHeGhZMlZmY21WemRXeDBjeUE5SUNSMGFHbHpMbUYwZEhJb1hDSmtZWFJoTFhKbGNHeGhZMlV0Y21WemRXeDBjMXdpS1NBOVBUMGdYQ0l3WENJZ1B5Qm1ZV3h6WlNBNklIUnlkV1U3WEc0Z0lDQWdJQ0FnSUZ4dUlDQWdJQ0FnSUNCMGFHbHpMbkpsYzNWc2RITmZkWEpzSUQwZ0pIUm9hWE11WVhSMGNpaGNJbVJoZEdFdGNtVnpkV3gwY3kxMWNteGNJaWs3WEc0Z0lDQWdJQ0FnSUhSb2FYTXVaR1ZpZFdkZmJXOWtaU0E5SUNSMGFHbHpMbUYwZEhJb1hDSmtZWFJoTFdSbFluVm5MVzF2WkdWY0lpazdYRzRnSUNBZ0lDQWdJSFJvYVhNdWRYQmtZWFJsWDJGcVlYaGZkWEpzSUQwZ0pIUm9hWE11WVhSMGNpaGNJbVJoZEdFdGRYQmtZWFJsTFdGcVlYZ3RkWEpzWENJcE8xeHVJQ0FnSUNBZ0lDQjBhR2x6TG5CaFoybHVZWFJwYjI1ZmRIbHdaU0E5SUNSMGFHbHpMbUYwZEhJb1hDSmtZWFJoTFdGcVlYZ3RjR0ZuYVc1aGRHbHZiaTEwZVhCbFhDSXBPMXh1SUNBZ0lDQWdJQ0IwYUdsekxtRjFkRzlmWTI5MWJuUWdQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzFoZFhSdkxXTnZkVzUwWENJcE8xeHVJQ0FnSUNBZ0lDQjBhR2x6TG1GMWRHOWZZMjkxYm5SZmNtVm1jbVZ6YUY5dGIyUmxJRDBnSkhSb2FYTXVZWFIwY2loY0ltUmhkR0V0WVhWMGJ5MWpiM1Z1ZEMxeVpXWnlaWE5vTFcxdlpHVmNJaWs3WEc0Z0lDQWdJQ0FnSUhSb2FYTXViMjVzZVY5eVpYTjFiSFJ6WDJGcVlYZ2dQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzF2Ym14NUxYSmxjM1ZzZEhNdFlXcGhlRndpS1RzZ0x5OXBaaUIzWlNCaGNtVWdibTkwSUc5dUlIUm9aU0J5WlhOMWJIUnpJSEJoWjJVc0lISmxaR2x5WldOMElISmhkR2hsY2lCMGFHRnVJSFJ5ZVNCMGJ5QnNiMkZrSUhacFlTQmhhbUY0WEc0Z0lDQWdJQ0FnSUhSb2FYTXVjMk55YjJ4c1gzUnZYM0J2Y3lBOUlDUjBhR2x6TG1GMGRISW9YQ0prWVhSaExYTmpjbTlzYkMxMGJ5MXdiM05jSWlrN1hHNGdJQ0FnSUNBZ0lIUm9hWE11WTNWemRHOXRYM05qY205c2JGOTBieUE5SUNSMGFHbHpMbUYwZEhJb1hDSmtZWFJoTFdOMWMzUnZiUzF6WTNKdmJHd3RkRzljSWlrN1hHNGdJQ0FnSUNBZ0lIUm9hWE11YzJOeWIyeHNYMjl1WDJGamRHbHZiaUE5SUNSMGFHbHpMbUYwZEhJb1hDSmtZWFJoTFhOamNtOXNiQzF2YmkxaFkzUnBiMjVjSWlrN1hHNGdJQ0FnSUNBZ0lIUm9hWE11YkdGdVoxOWpiMlJsSUQwZ0pIUm9hWE11WVhSMGNpaGNJbVJoZEdFdGJHRnVaeTFqYjJSbFhDSXBPMXh1SUNBZ0lDQWdJQ0IwYUdsekxtRnFZWGhmZFhKc0lEMGdKSFJvYVhNdVlYUjBjaWduWkdGMFlTMWhhbUY0TFhWeWJDY3BPMXh1SUNBZ0lDQWdJQ0IwYUdsekxtRnFZWGhmWm05eWJWOTFjbXdnUFNBa2RHaHBjeTVoZEhSeUtDZGtZWFJoTFdGcVlYZ3RabTl5YlMxMWNtd25LVHRjYmlBZ0lDQWdJQ0FnZEdocGN5NXBjMTl5ZEd3Z1BTQWtkR2hwY3k1aGRIUnlLQ2RrWVhSaExXbHpMWEowYkNjcE8xeHVYRzRnSUNBZ0lDQWdJSFJvYVhNdVpHbHpjR3hoZVY5eVpYTjFiSFJmYldWMGFHOWtJRDBnSkhSb2FYTXVZWFIwY2lnblpHRjBZUzFrYVhOd2JHRjVMWEpsYzNWc2RDMXRaWFJvYjJRbktUdGNiaUFnSUNBZ0lDQWdkR2hwY3k1dFlXbHVkR0ZwYmw5emRHRjBaU0E5SUNSMGFHbHpMbUYwZEhJb0oyUmhkR0V0YldGcGJuUmhhVzR0YzNSaGRHVW5LVHRjYmlBZ0lDQWdJQ0FnZEdocGN5NWhhbUY0WDJGamRHbHZiaUE5SUZ3aVhDSTdYRzRnSUNBZ0lDQWdJSFJvYVhNdWJHRnpkRjl6ZFdKdGFYUmZjWFZsY25sZmNHRnlZVzF6SUQwZ1hDSmNJanRjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbU4xY25KbGJuUmZjR0ZuWldRZ1BTQndZWEp6WlVsdWRDZ2tkR2hwY3k1aGRIUnlLQ2RrWVhSaExXbHVhWFF0Y0dGblpXUW5LU2s3WEc0Z0lDQWdJQ0FnSUhSb2FYTXViR0Z6ZEY5c2IyRmtYMjF2Y21WZmFIUnRiQ0E5SUZ3aVhDSTdYRzRnSUNBZ0lDQWdJSFJvYVhNdWJHOWhaRjl0YjNKbFgyaDBiV3dnUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0IwYUdsekxtRnFZWGhmWkdGMFlWOTBlWEJsSUQwZ0pIUm9hWE11WVhSMGNpZ25aR0YwWVMxaGFtRjRMV1JoZEdFdGRIbHdaU2NwTzF4dUlDQWdJQ0FnSUNCMGFHbHpMbUZxWVhoZmRHRnlaMlYwWDJGMGRISWdQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzFoYW1GNExYUmhjbWRsZEZ3aUtUdGNiaUFnSUNBZ0lDQWdkR2hwY3k1MWMyVmZhR2x6ZEc5eWVWOWhjR2tnUFNBa2RHaHBjeTVoZEhSeUtGd2laR0YwWVMxMWMyVXRhR2x6ZEc5eWVTMWhjR2xjSWlrN1hHNGdJQ0FnSUNBZ0lIUm9hWE11YVhOZmMzVmliV2wwZEdsdVp5QTlJR1poYkhObE8xeHVYRzRnSUNBZ0lDQWdJSFJvYVhNdWJHRnpkRjloYW1GNFgzSmxjWFZsYzNRZ1BTQnVkV3hzTzF4dVhHNGdJQ0FnSUNBZ0lHbG1LSFI1Y0dWdlppaDBhR2x6TG5KbGMzVnNkSE5mYUhSdGJDazlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVjbVZ6ZFd4MGMxOW9kRzFzSUQwZ1hDSmNJanRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lHbG1LSFI1Y0dWdlppaDBhR2x6TG5WelpWOW9hWE4wYjNKNVgyRndhU2s5UFZ3aWRXNWtaV1pwYm1Wa1hDSXBYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11ZFhObFgyaHBjM1J2Y25sZllYQnBJRDBnWENKY0lqdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtIUjVjR1Z2WmloMGFHbHpMbkJoWjJsdVlYUnBiMjVmZEhsd1pTazlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVjR0ZuYVc1aGRHbHZibDkwZVhCbElEMGdYQ0p1YjNKdFlXeGNJanRjYmlBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCcFppaDBlWEJsYjJZb2RHaHBjeTVqZFhKeVpXNTBYM0JoWjJWa0tUMDlYQ0oxYm1SbFptbHVaV1JjSWlsY2JpQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWpkWEp5Wlc1MFgzQmhaMlZrSUQwZ01UdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtIUjVjR1Z2WmloMGFHbHpMbUZxWVhoZmRHRnlaMlYwWDJGMGRISXBQVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbUZxWVhoZmRHRnlaMlYwWDJGMGRISWdQU0JjSWx3aU8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnYVdZb2RIbHdaVzltS0hSb2FYTXVZV3BoZUY5MWNtd3BQVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbUZxWVhoZmRYSnNJRDBnWENKY0lqdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtIUjVjR1Z2WmloMGFHbHpMbUZxWVhoZlptOXliVjkxY213cFBUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtRnFZWGhmWm05eWJWOTFjbXdnUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtIUm9hWE11Y21WemRXeDBjMTkxY213cFBUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxuSmxjM1ZzZEhOZmRYSnNJRDBnWENKY0lqdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtIUjVjR1Z2WmloMGFHbHpMbk5qY205c2JGOTBiMTl3YjNNcFBUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxuTmpjbTlzYkY5MGIxOXdiM01nUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtIUm9hWE11YzJOeWIyeHNYMjl1WDJGamRHbHZiaWs5UFZ3aWRXNWtaV1pwYm1Wa1hDSXBYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11YzJOeWIyeHNYMjl1WDJGamRHbHZiaUE5SUZ3aVhDSTdYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnYVdZb2RIbHdaVzltS0hSb2FYTXVZM1Z6ZEc5dFgzTmpjbTlzYkY5MGJ5azlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVZM1Z6ZEc5dFgzTmpjbTlzYkY5MGJ5QTlJRndpWENJN1hHNGdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdkR2hwY3k0a1kzVnpkRzl0WDNOamNtOXNiRjkwYnlBOUlHcFJkV1Z5ZVNoMGFHbHpMbU4xYzNSdmJWOXpZM0p2Ykd4ZmRHOHBPMXh1WEc0Z0lDQWdJQ0FnSUdsbUtIUjVjR1Z2WmloMGFHbHpMblZ3WkdGMFpWOWhhbUY0WDNWeWJDazlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVkWEJrWVhSbFgyRnFZWGhmZFhKc0lEMGdYQ0pjSWp0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR2xtS0hSNWNHVnZaaWgwYUdsekxtUmxZblZuWDIxdlpHVXBQVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbVJsWW5WblgyMXZaR1VnUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtIUm9hWE11WVdwaGVGOTBZWEpuWlhSZmIySnFaV04wS1QwOVhDSjFibVJsWm1sdVpXUmNJaWxjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdkR2hwY3k1aGFtRjRYM1JoY21kbGRGOXZZbXBsWTNRZ1BTQmNJbHdpTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdhV1lvZEhsd1pXOW1LSFJvYVhNdWRHVnRjR3hoZEdWZmFYTmZiRzloWkdWa0tUMDlYQ0oxYm1SbFptbHVaV1JjSWlsY2JpQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NTBaVzF3YkdGMFpWOXBjMTlzYjJGa1pXUWdQU0JjSWpCY0lqdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtIUjVjR1Z2WmloMGFHbHpMbUYxZEc5ZlkyOTFiblJmY21WbWNtVnphRjl0YjJSbEtUMDlYQ0oxYm1SbFptbHVaV1JjSWlsY2JpQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWhkWFJ2WDJOdmRXNTBYM0psWm5KbGMyaGZiVzlrWlNBOUlGd2lNRndpTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1aGFtRjRYMnhwYm10elgzTmxiR1ZqZEc5eUlEMGdKSFJvYVhNdVlYUjBjaWhjSW1SaGRHRXRZV3BoZUMxc2FXNXJjeTF6Wld4bFkzUnZjbHdpS1R0Y2JseHVYRzRnSUNBZ0lDQWdJSFJvYVhNdVlYVjBiMTkxY0dSaGRHVWdQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzFoZFhSdkxYVndaR0YwWlZ3aUtUdGNiaUFnSUNBZ0lDQWdkR2hwY3k1cGJuQjFkRlJwYldWeUlEMGdNRHRjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbk5sZEVsdVptbHVhWFJsVTJOeWIyeHNRMjl1ZEdGcGJtVnlJRDBnWm5WdVkzUnBiMjRvS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0F2THlCWGFHVnVJSGRsSUc1aGRtbG5ZWFJsSUdGM1lYa2dabkp2YlNCelpXRnlZMmdnY21WemRXeDBjeXdnWVc1a0lIUm9aVzRnY0hKbGMzTWdZbUZqYXl4Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2SUdselgyMWhlRjl3WVdkbFpDQnBjeUJ5WlhSaGFXNWxaQ3dnYzI4Z2QyVWdiMjVzZVNCM1lXNTBJSFJ2SUhObGRDQnBkQ0IwYnlCbVlXeHpaU0JwWmx4dUlDQWdJQ0FnSUNBZ0lDQWdMeThnZDJVZ1lYSmxJR2x1YVhSaGJHbDZhVzVuSUhSb1pTQnlaWE4xYkhSeklIQmhaMlVnZEdobElHWnBjbk4wSUhScGJXVWdMU0J6YnlCcWRYTjBJRnh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OGdZMmhsWTJzZ2FXWWdkR2hwY3lCMllYSWdhWE1nZFc1a1pXWnBibVZrSUNoaGN5QnBkQ0J6YUc5MWJHUWdZbVVnYjI0Z1ptbHljM1FnZFhObEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUlDZ2dkSGx3Wlc5bUlDZ2dkR2hwY3k1cGMxOXRZWGhmY0dGblpXUWdLU0E5UFQwZ0ozVnVaR1ZtYVc1bFpDY2dLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXBjMTl0WVhoZmNHRm5aV1FnUFNCbVlXeHpaVHNnTHk5bWIzSWdiRzloWkNCdGIzSmxJRzl1Ykhrc0lHOXVZMlVnZDJVZ1pHVjBaV04wSUhkbEozSmxJR0YwSUhSb1pTQmxibVFnYzJWMElIUm9hWE1nZEc4Z2RISjFaVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMblZ6WlY5elkzSnZiR3hmYkc5aFpHVnlJRDBnSkhSb2FYTXVZWFIwY2lnblpHRjBZUzF6YUc5M0xYTmpjbTlzYkMxc2IyRmtaWEluS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdWFXNW1hVzVwZEdWZmMyTnliMnhzWDJOdmJuUmhhVzVsY2lBOUlDUjBhR2x6TG1GMGRISW9KMlJoZEdFdGFXNW1hVzVwZEdVdGMyTnliMnhzTFdOdmJuUmhhVzVsY2ljcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXBibVpwYm1sMFpWOXpZM0p2Ykd4ZmRISnBaMmRsY2w5aGJXOTFiblFnUFNBa2RHaHBjeTVoZEhSeUtDZGtZWFJoTFdsdVptbHVhWFJsTFhOamNtOXNiQzEwY21sbloyVnlKeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG1sdVptbHVhWFJsWDNOamNtOXNiRjl5WlhOMWJIUmZZMnhoYzNNZ1BTQWtkR2hwY3k1aGRIUnlLQ2RrWVhSaExXbHVabWx1YVhSbExYTmpjbTlzYkMxeVpYTjFiSFF0WTJ4aGMzTW5LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11SkdsdVptbHVhWFJsWDNOamNtOXNiRjlqYjI1MFlXbHVaWElnUFNCMGFHbHpMaVJoYW1GNFgzSmxjM1ZzZEhOZlkyOXVkR0ZwYm1WeU8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaDBlWEJsYjJZb2RHaHBjeTVwYm1acGJtbDBaVjl6WTNKdmJHeGZZMjl1ZEdGcGJtVnlLVDA5WENKMWJtUmxabWx1WldSY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbWx1Wm1sdWFYUmxYM05qY205c2JGOWpiMjUwWVdsdVpYSWdQU0JjSWx3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYVhNdUpHbHVabWx1YVhSbFgzTmpjbTlzYkY5amIyNTBZV2x1WlhJZ1BTQnFVWFZsY25rb2RHaHBjeTVoYW1GNFgzUmhjbWRsZEY5aGRIUnlJQ3NnSnlBbklDc2dkR2hwY3k1cGJtWnBibWwwWlY5elkzSnZiR3hmWTI5dWRHRnBibVZ5S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lvZEhsd1pXOW1LSFJvYVhNdWFXNW1hVzVwZEdWZmMyTnliMnhzWDNKbGMzVnNkRjlqYkdGemN5azlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXBibVpwYm1sMFpWOXpZM0p2Ykd4ZmNtVnpkV3gwWDJOc1lYTnpJRDBnWENKY0lqdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZb2RIbHdaVzltS0hSb2FYTXVkWE5sWDNOamNtOXNiRjlzYjJGa1pYSXBQVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSb2FYTXVkWE5sWDNOamNtOXNiRjlzYjJGa1pYSWdQU0F4TzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUgwN1hHNGdJQ0FnSUNBZ0lIUm9hWE11YzJWMFNXNW1hVzVwZEdWVFkzSnZiR3hEYjI1MFlXbHVaWElvS1R0Y2JseHVJQ0FnSUNBZ0lDQXZLaUJtZFc1amRHbHZibk1nS2k5Y2JseHVJQ0FnSUNBZ0lDQjBhR2x6TG5KbGMyVjBJRDBnWm5WdVkzUnBiMjRvYzNWaWJXbDBYMlp2Y20wcFhHNGdJQ0FnSUNBZ0lIdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2RHaHBjeTV5WlhObGRFWnZjbTBvYzNWaWJXbDBYMlp2Y20wcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJSFJ5ZFdVN1hHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0IwYUdsekxtbHVjSFYwVlhCa1lYUmxJRDBnWm5WdVkzUnBiMjRvWkdWc1lYbEVkWEpoZEdsdmJpbGNiaUFnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtHUmxiR0Y1UkhWeVlYUnBiMjRwUFQxY0luVnVaR1ZtYVc1bFpGd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJrWld4aGVVUjFjbUYwYVc5dUlEMGdNekF3TzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG5KbGMyVjBWR2x0WlhJb1pHVnNZWGxFZFhKaGRHbHZiaWs3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQjBhR2x6TG5OamNtOXNiRlJ2VUc5eklEMGdablZ1WTNScGIyNG9LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnYjJabWMyVjBJRDBnTUR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCallXNVRZM0p2Ykd3Z1BTQjBjblZsTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloelpXeG1MbWx6WDJGcVlYZzlQVEVwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvYzJWc1ppNXpZM0p2Ykd4ZmRHOWZjRzl6UFQxY0luZHBibVJ2ZDF3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYjJabWMyVjBJRDBnTUR0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxiSE5sSUdsbUtITmxiR1l1YzJOeWIyeHNYM1J2WDNCdmN6MDlYQ0ptYjNKdFhDSXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdlptWnpaWFFnUFNBa2RHaHBjeTV2Wm1aelpYUW9LUzUwYjNBN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVnNjMlVnYVdZb2MyVnNaaTV6WTNKdmJHeGZkRzlmY0c5elBUMWNJbkpsYzNWc2RITmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdUpHRnFZWGhmY21WemRXeDBjMTlqYjI1MFlXbHVaWEl1YkdWdVozUm9QakFwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHOW1abk5sZENBOUlITmxiR1l1SkdGcVlYaGZjbVZ6ZFd4MGMxOWpiMjUwWVdsdVpYSXViMlptYzJWMEtDa3VkRzl3TzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJVZ2FXWW9jMlZzWmk1elkzSnZiR3hmZEc5ZmNHOXpQVDFjSW1OMWMzUnZiVndpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTlqZFhOMGIyMWZjMk55YjJ4c1gzUnZYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtITmxiR1l1SkdOMWMzUnZiVjl6WTNKdmJHeGZkRzh1YkdWdVozUm9QakFwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHOW1abk5sZENBOUlITmxiR1l1SkdOMWMzUnZiVjl6WTNKdmJHeGZkRzh1YjJabWMyVjBLQ2t1ZEc5d08xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWc2MyVmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOaGJsTmpjbTlzYkNBOUlHWmhiSE5sTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LR05oYmxOamNtOXNiQ2xjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUW9YQ0pvZEcxc0xDQmliMlI1WENJcExuTjBiM0FvS1M1aGJtbHRZWFJsS0h0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhOamNtOXNiRlJ2Y0RvZ2IyWm1jMlYwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMHNJRndpYm05eWJXRnNYQ0lzSUZ3aVpXRnpaVTkxZEZGMVlXUmNJaUFwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCOU8xeHVYRzRnSUNBZ0lDQWdJSFJvYVhNdVlYUjBZV05vUVdOMGFYWmxRMnhoYzNNZ1BTQm1kVzVqZEdsdmJpZ3BlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMMk5vWldOcklIUnZJSE5sWlNCcFppQjNaU0JoY21VZ2RYTnBibWNnWVdwaGVDQW1JR0YxZEc4Z1kyOTFiblJjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZhV1lnYm05MExDQjBhR1VnYzJWaGNtTm9JR1p2Y20wZ1pHOWxjeUJ1YjNRZ1oyVjBJSEpsYkc5aFpHVmtMQ0J6YnlCM1pTQnVaV1ZrSUhSdklIVndaR0YwWlNCMGFHVWdjMll0YjNCMGFXOXVMV0ZqZEdsMlpTQmpiR0Z6Y3lCdmJpQmhiR3dnWm1sbGJHUnpYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDUjBhR2x6TG05dUtDZGphR0Z1WjJVbkxDQW5hVzV3ZFhSYmRIbHdaVDFjSW5KaFpHbHZYQ0pkTENCcGJuQjFkRnQwZVhCbFBWd2lZMmhsWTJ0aWIzaGNJbDBzSUhObGJHVmpkQ2NzSUdaMWJtTjBhVzl1S0dVcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUmpkR2hwY3lBOUlDUW9kR2hwY3lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1JqZEdocGMxOXdZWEpsYm5RZ1BTQWtZM1JvYVhNdVkyeHZjMlZ6ZENoY0lteHBXMlJoZEdFdGMyWXRabWxsYkdRdGJtRnRaVjFjSWlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSFJvYVhOZmRHRm5JRDBnSkdOMGFHbHpMbkJ5YjNBb1hDSjBZV2RPWVcxbFhDSXBMblJ2VEc5M1pYSkRZWE5sS0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR2x1Y0hWMFgzUjVjR1VnUFNBa1kzUm9hWE11WVhSMGNpaGNJblI1Y0dWY0lpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhCaGNtVnVkRjkwWVdjZ1BTQWtZM1JvYVhOZmNHRnlaVzUwTG5CeWIzQW9YQ0owWVdkT1lXMWxYQ0lwTG5SdlRHOTNaWEpEWVhObEtDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlnb2RHaHBjMTkwWVdjOVBWd2lhVzV3ZFhSY0lpa21KaWdvYVc1d2RYUmZkSGx3WlQwOVhDSnlZV1JwYjF3aUtYeDhLR2x1Y0hWMFgzUjVjR1U5UFZ3aVkyaGxZMnRpYjNoY0lpa3BJQ1ltSUNod1lYSmxiblJmZEdGblBUMWNJbXhwWENJcEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUmhiR3hmYjNCMGFXOXVjeUE5SUNSamRHaHBjMTl3WVhKbGJuUXVjR0Z5Wlc1MEtDa3VabWx1WkNnbmJHa25LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1JoYkd4ZmIzQjBhVzl1YzE5bWFXVnNaSE1nUFNBa1kzUm9hWE5mY0dGeVpXNTBMbkJoY21WdWRDZ3BMbVpwYm1Rb0oybHVjSFYwT21Ob1pXTnJaV1FuS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrWVd4c1gyOXdkR2x2Ym5NdWNtVnRiM1psUTJ4aGMzTW9YQ0p6WmkxdmNIUnBiMjR0WVdOMGFYWmxYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtZV3hzWDI5d2RHbHZibk5mWm1sbGJHUnpMbVZoWTJnb1puVnVZM1JwYjI0b0tYdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSd1lYSmxiblFnUFNBa0tIUm9hWE1wTG1Oc2IzTmxjM1FvWENKc2FWd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1J3WVhKbGJuUXVZV1JrUTJ4aGMzTW9YQ0p6WmkxdmNIUnBiMjR0WVdOMGFYWmxYQ0lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHVnNjMlVnYVdZb2RHaHBjMTkwWVdjOVBWd2ljMlZzWldOMFhDSXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdKR0ZzYkY5dmNIUnBiMjV6SUQwZ0pHTjBhR2x6TG1Ob2FXeGtjbVZ1S0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1JoYkd4ZmIzQjBhVzl1Y3k1eVpXMXZkbVZEYkdGemN5aGNJbk5tTFc5d2RHbHZiaTFoWTNScGRtVmNJaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUIwYUdselgzWmhiQ0E5SUNSamRHaHBjeTUyWVd3b0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdkR2hwYzE5aGNuSmZkbUZzSUQwZ0tIUjVjR1Z2WmlCMGFHbHpYM1poYkNBOVBTQW5jM1J5YVc1bkp5QjhmQ0IwYUdselgzWmhiQ0JwYm5OMFlXNWpaVzltSUZOMGNtbHVaeWtnUHlCYmRHaHBjMTkyWVd4ZElEb2dkR2hwYzE5MllXdzdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pDaDBhR2x6WDJGeWNsOTJZV3dwTG1WaFkyZ29ablZ1WTNScGIyNG9hU3dnZG1Gc2RXVXBlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKR04wYUdsekxtWnBibVFvWENKdmNIUnBiMjViZG1Gc2RXVTlKMXdpSzNaaGJIVmxLMXdpSjExY0lpa3VZV1JrUTJ4aGMzTW9YQ0p6WmkxdmNIUnBiMjR0WVdOMGFYWmxYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNiaUFnSUNBZ0lDQWdmVHRjYmlBZ0lDQWdJQ0FnZEdocGN5NXBibWwwUVhWMGIxVndaR0YwWlVWMlpXNTBjeUE5SUdaMWJtTjBhVzl1S0NsN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUM4cUlHRjFkRzhnZFhCa1lYUmxJQ292WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWdvYzJWc1ppNWhkWFJ2WDNWd1pHRjBaVDA5TVNsOGZDaHpaV3htTG1GMWRHOWZZMjkxYm5SZmNtVm1jbVZ6YUY5dGIyUmxQVDB4S1NsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrZEdocGN5NXZiaWduWTJoaGJtZGxKeXdnSjJsdWNIVjBXM1I1Y0dVOVhDSnlZV1JwYjF3aVhTd2dhVzV3ZFhSYmRIbHdaVDFjSW1Ob1pXTnJZbTk0WENKZExDQnpaV3hsWTNRbkxDQm1kVzVqZEdsdmJpaGxLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YVc1d2RYUlZjR1JoZEdVb01qQXdLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUjBhR2x6TG05dUtDZHBibkIxZENjc0lDZHBibkIxZEZ0MGVYQmxQVndpYm5WdFltVnlYQ0pkSnl3Z1puVnVZM1JwYjI0b1pTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbWx1Y0hWMFZYQmtZWFJsS0Rnd01DazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdKSFJsZUhSSmJuQjFkQ0E5SUNSMGFHbHpMbVpwYm1Rb0oybHVjSFYwVzNSNWNHVTlYQ0owWlhoMFhDSmRPbTV2ZENndWMyWXRaR0YwWlhCcFkydGxjaWtuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdiR0Z6ZEZaaGJIVmxJRDBnSkhSbGVIUkpibkIxZEM1MllXd29LVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSMGFHbHpMbTl1S0NkcGJuQjFkQ2NzSUNkcGJuQjFkRnQwZVhCbFBWd2lkR1Y0ZEZ3aVhUcHViM1FvTG5ObUxXUmhkR1Z3YVdOclpYSXBKeXdnWm5WdVkzUnBiMjRvS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvYkdGemRGWmhiSFZsSVQwa2RHVjRkRWx1Y0hWMExuWmhiQ2dwS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1sdWNIVjBWWEJrWVhSbEtERXlNREFwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JHRnpkRlpoYkhWbElEMGdKSFJsZUhSSmJuQjFkQzUyWVd3b0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pIUm9hWE11YjI0b0oydGxlWEJ5WlhOekp5d2dKMmx1Y0hWMFczUjVjR1U5WENKMFpYaDBYQ0pkT201dmRDZ3VjMll0WkdGMFpYQnBZMnRsY2lrbkxDQm1kVzVqZEdsdmJpaGxLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLR1V1ZDJocFkyZ2dQVDBnTVRNcGUxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxMbkJ5WlhabGJuUkVaV1poZFd4MEtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG5OMVltMXBkRVp2Y20wb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCbVlXeHpaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkx5UjBhR2x6TG05dUtDZHBibkIxZENjc0lDZHBibkIxZEM1elppMWtZWFJsY0dsamEyVnlKeXdnYzJWc1ppNWtZWFJsU1c1d2RYUlVlWEJsS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCOU8xeHVYRzRnSUNBZ0lDQWdJQzh2ZEdocGN5NXBibWwwUVhWMGIxVndaR0YwWlVWMlpXNTBjeWdwTzF4dVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1amJHVmhjbFJwYldWeUlEMGdablZ1WTNScGIyNG9LVnh1SUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQmpiR1ZoY2xScGJXVnZkWFFvYzJWc1ppNXBibkIxZEZScGJXVnlLVHRjYmlBZ0lDQWdJQ0FnZlR0Y2JpQWdJQ0FnSUNBZ2RHaHBjeTV5WlhObGRGUnBiV1Z5SUQwZ1puVnVZM1JwYjI0b1pHVnNZWGxFZFhKaGRHbHZiaWxjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdZMnhsWVhKVWFXMWxiM1YwS0hObGJHWXVhVzV3ZFhSVWFXMWxjaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1sdWNIVjBWR2x0WlhJZ1BTQnpaWFJVYVcxbGIzVjBLSE5sYkdZdVptOXliVlZ3WkdGMFpXUXNJR1JsYkdGNVJIVnlZWFJwYjI0cE8xeHVYRzRnSUNBZ0lDQWdJSDA3WEc1Y2JpQWdJQ0FnSUNBZ2RHaHBjeTVoWkdSRVlYUmxVR2xqYTJWeWN5QTlJR1oxYm1OMGFXOXVLQ2xjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSa1lYUmxYM0JwWTJ0bGNpQTlJQ1IwYUdsekxtWnBibVFvWENJdWMyWXRaR0YwWlhCcFkydGxjbHdpS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZb0pHUmhkR1ZmY0dsamEyVnlMbXhsYm1kMGFENHdLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUmtZWFJsWDNCcFkydGxjaTVsWVdOb0tHWjFibU4wYVc5dUtDbDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1IwYUdseklEMGdKQ2gwYUdsektUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHUmhkR1ZHYjNKdFlYUWdQU0JjSWx3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ1pHRjBaVVJ5YjNCa2IzZHVXV1ZoY2lBOUlHWmhiSE5sTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnWkdGMFpVUnliM0JrYjNkdVRXOXVkR2dnUFNCbVlXeHpaVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnSkdOc2IzTmxjM1JmWkdGMFpWOTNjbUZ3SUQwZ0pIUm9hWE11WTJ4dmMyVnpkQ2hjSWk1elpsOWtZWFJsWDJacFpXeGtYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWdrWTJ4dmMyVnpkRjlrWVhSbFgzZHlZWEF1YkdWdVozUm9QakFwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmhkR1ZHYjNKdFlYUWdQU0FrWTJ4dmMyVnpkRjlrWVhSbFgzZHlZWEF1WVhSMGNpaGNJbVJoZEdFdFpHRjBaUzFtYjNKdFlYUmNJaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtDUmpiRzl6WlhOMFgyUmhkR1ZmZDNKaGNDNWhkSFJ5S0Z3aVpHRjBZUzFrWVhSbExYVnpaUzE1WldGeUxXUnliM0JrYjNkdVhDSXBQVDB4S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmhkR1ZFY205d1pHOTNibGxsWVhJZ1BTQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZb0pHTnNiM05sYzNSZlpHRjBaVjkzY21Gd0xtRjBkSElvWENKa1lYUmhMV1JoZEdVdGRYTmxMVzF2Ym5Sb0xXUnliM0JrYjNkdVhDSXBQVDB4S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmhkR1ZFY205d1pHOTNiazF2Ym5Sb0lEMGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJrWVhSbFVHbGphMlZ5VDNCMGFXOXVjeUE5SUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsdWJHbHVaVG9nZEhKMVpTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITm9iM2RQZEdobGNrMXZiblJvY3pvZ2RISjFaU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzl1VTJWc1pXTjBPaUJtZFc1amRHbHZiaWhsTENCbWNtOXRYMlpwWld4a0tYc2djMlZzWmk1a1lYUmxVMlZzWldOMEtHVXNJR1p5YjIxZlptbGxiR1FzSUNRb2RHaHBjeWtwT3lCOUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHRjBaVVp2Y20xaGREb2daR0YwWlVadmNtMWhkQ3hjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTJoaGJtZGxUVzl1ZEdnNklHUmhkR1ZFY205d1pHOTNiazF2Ym5Sb0xGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1kyaGhibWRsV1dWaGNqb2daR0YwWlVSeWIzQmtiM2R1V1dWaGNseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5TzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0hObGJHWXVhWE5mY25Sc1BUMHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1lYUmxVR2xqYTJWeVQzQjBhVzl1Y3k1a2FYSmxZM1JwYjI0Z1BTQmNJbkowYkZ3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKSFJvYVhNdVpHRjBaWEJwWTJ0bGNpaGtZWFJsVUdsamEyVnlUM0IwYVc5dWN5azdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9jMlZzWmk1c1lXNW5YMk52WkdVaFBWd2lYQ0lwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUXVaR0YwWlhCcFkydGxjaTV6WlhSRVpXWmhkV3gwY3loY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtMbVY0ZEdWdVpDaGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2V5ZGtZWFJsUm05eWJXRjBKenBrWVhSbFJtOXliV0YwZlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkM1a1lYUmxjR2xqYTJWeUxuSmxaMmx2Ym1Gc1d5QnpaV3htTG14aGJtZGZZMjlrWlYxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWld4elpWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrTG1SaGRHVndhV05yWlhJdWMyVjBSR1ZtWVhWc2RITW9YRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkM1bGVIUmxibVFvWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHNuWkdGMFpVWnZjbTFoZENjNlpHRjBaVVp2Y20xaGRIMHNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUXVaR0YwWlhCcFkydGxjaTV5WldkcGIyNWhiRnRjSW1WdVhDSmRYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0NRb0p5NXNiQzF6YTJsdUxXMWxiRzl1SnlrdWJHVnVaM1JvUFQwd0tYdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa1pHRjBaVjl3YVdOclpYSXVaR0YwWlhCcFkydGxjaWduZDJsa1oyVjBKeWt1ZDNKaGNDZ25QR1JwZGlCamJHRnpjejFjSW14c0xYTnJhVzR0YldWc2IyNGdjMlZoY21Ob1lXNWtabWxzZEdWeUxXUmhkR1V0Y0dsamEyVnlYQ0l2UGljcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQjlPMXh1WEc0Z0lDQWdJQ0FnSUhSb2FYTXVaR0YwWlZObGJHVmpkQ0E5SUdaMWJtTjBhVzl1S0dVc0lHWnliMjFmWm1sbGJHUXNJQ1IwYUdsektWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdKR2x1Y0hWMFgyWnBaV3hrSUQwZ0pDaG1jbTl0WDJacFpXeGtMbWx1Y0hWMExtZGxkQ2d3S1NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pIUm9hWE1nUFNBa0tIUm9hWE1wTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pHUmhkR1ZmWm1sbGJHUnpJRDBnSkdsdWNIVjBYMlpwWld4a0xtTnNiM05sYzNRb0oxdGtZWFJoTFhObUxXWnBaV3hrTFdsdWNIVjBMWFI1Y0dVOVhDSmtZWFJsY21GdVoyVmNJbDBzSUZ0a1lYUmhMWE5tTFdacFpXeGtMV2x1Y0hWMExYUjVjR1U5WENKa1lYUmxYQ0pkSnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FrWkdGMFpWOW1hV1ZzWkhNdVpXRmphQ2htZFc1amRHbHZiaWhsTENCcGJtUmxlQ2w3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUjBabDlrWVhSbFgzQnBZMnRsY25NZ1BTQWtLSFJvYVhNcExtWnBibVFvWENJdWMyWXRaR0YwWlhCcFkydGxjbHdpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdibTlmWkdGMFpWOXdhV05yWlhKeklEMGdKSFJtWDJSaGRHVmZjR2xqYTJWeWN5NXNaVzVuZEdnN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9ibTlmWkdGMFpWOXdhV05yWlhKelBqRXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwzUm9aVzRnYVhRZ2FYTWdZU0JrWVhSbElISmhibWRsTENCemJ5QnRZV3RsSUhOMWNtVWdZbTkwYUNCbWFXVnNaSE1nWVhKbElHWnBiR3hsWkNCaVpXWnZjbVVnZFhCa1lYUnBibWRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1J3WDJOdmRXNTBaWElnUFNBd08xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ1pIQmZaVzF3ZEhsZlptbGxiR1JmWTI5MWJuUWdQU0F3TzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtkR1pmWkdGMFpWOXdhV05yWlhKekxtVmhZMmdvWm5WdVkzUnBiMjRvS1h0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9KQ2gwYUdsektTNTJZV3dvS1QwOVhDSmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa2NGOWxiWEIwZVY5bWFXVnNaRjlqYjNWdWRDc3JPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrY0Y5amIzVnVkR1Z5S3lzN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0dSd1gyVnRjSFI1WDJacFpXeGtYMk52ZFc1MFBUMHdLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbWx1Y0hWMFZYQmtZWFJsS0RFcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWc2MyVmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVhVzV3ZFhSVmNHUmhkR1VvTVNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCOUtUdGNiaUFnSUNBZ0lDQWdmVHRjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbUZrWkZKaGJtZGxVMnhwWkdWeWN5QTlJR1oxYm1OMGFXOXVLQ2xjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSdFpYUmhYM0poYm1kbElEMGdKSFJvYVhNdVptbHVaQ2hjSWk1elppMXRaWFJoTFhKaGJtZGxMWE5zYVdSbGNsd2lLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lvSkcxbGRHRmZjbUZ1WjJVdWJHVnVaM1JvUGpBcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkcxbGRHRmZjbUZ1WjJVdVpXRmphQ2htZFc1amRHbHZiaWdwZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lBa2RHaHBjeUE5SUNRb2RHaHBjeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ0YVc0Z1BTQWtkR2hwY3k1aGRIUnlLRndpWkdGMFlTMXRhVzVjSWlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCdFlYZ2dQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzF0WVhoY0lpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQnpiV2x1SUQwZ0pIUm9hWE11WVhSMGNpaGNJbVJoZEdFdGMzUmhjblF0YldsdVhDSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdjMjFoZUNBOUlDUjBhR2x6TG1GMGRISW9YQ0prWVhSaExYTjBZWEowTFcxaGVGd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1JwYzNCc1lYbGZkbUZzZFdWZllYTWdQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzFrYVhOd2JHRjVMWFpoYkhWbGN5MWhjMXdpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhOMFpYQWdQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzF6ZEdWd1hDSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdKSE4wWVhKMFgzWmhiQ0E5SUNSMGFHbHpMbVpwYm1Rb0p5NXpaaTF5WVc1blpTMXRhVzRuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSbGJtUmZkbUZzSUQwZ0pIUm9hWE11Wm1sdVpDZ25Mbk5tTFhKaGJtZGxMVzFoZUNjcE8xeHVYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1JsWTJsdFlXeGZjR3hoWTJWeklEMGdKSFJvYVhNdVlYUjBjaWhjSW1SaGRHRXRaR1ZqYVcxaGJDMXdiR0ZqWlhOY0lpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQjBhRzkxYzJGdVpGOXpaWEJsY21GMGIzSWdQU0FrZEdocGN5NWhkSFJ5S0Z3aVpHRjBZUzEwYUc5MWMyRnVaQzF6WlhCbGNtRjBiM0pjSWlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCa1pXTnBiV0ZzWDNObGNHVnlZWFJ2Y2lBOUlDUjBhR2x6TG1GMGRISW9YQ0prWVhSaExXUmxZMmx0WVd3dGMyVndaWEpoZEc5eVhDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJtYVdWc1pGOW1iM0p0WVhRZ1BTQjNUblZ0WWloN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdFlYSnJPaUJrWldOcGJXRnNYM05sY0dWeVlYUnZjaXhjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JsWTJsdFlXeHpPaUJ3WVhKelpVWnNiMkYwS0dSbFkybHRZV3hmY0d4aFkyVnpLU3hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFJvYjNWellXNWtPaUIwYUc5MWMyRnVaRjl6WlhCbGNtRjBiM0pjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzVjYmx4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCdGFXNWZkVzVtYjNKdFlYUjBaV1FnUFNCd1lYSnpaVVpzYjJGMEtITnRhVzRwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnYldsdVgyWnZjbTFoZEhSbFpDQTlJR1pwWld4a1gyWnZjbTFoZEM1MGJ5aHdZWEp6WlVac2IyRjBLSE50YVc0cEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHMWhlRjltYjNKdFlYUjBaV1FnUFNCbWFXVnNaRjltYjNKdFlYUXVkRzhvY0dGeWMyVkdiRzloZENoemJXRjRLU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ0WVhoZmRXNW1iM0p0WVhSMFpXUWdQU0J3WVhKelpVWnNiMkYwS0hOdFlYZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyRnNaWEowS0cxcGJsOW1iM0p0WVhSMFpXUXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyRnNaWEowS0cxaGVGOW1iM0p0WVhSMFpXUXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyRnNaWEowS0dScGMzQnNZWGxmZG1Gc2RXVmZZWE1wTzF4dVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZb1pHbHpjR3hoZVY5MllXeDFaVjloY3owOVhDSjBaWGgwYVc1d2RYUmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pITjBZWEowWDNaaGJDNTJZV3dvYldsdVgyWnZjbTFoZEhSbFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtaVzVrWDNaaGJDNTJZV3dvYldGNFgyWnZjbTFoZEhSbFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaV3h6WlNCcFppaGthWE53YkdGNVgzWmhiSFZsWDJGelBUMWNJblJsZUhSY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkhOMFlYSjBYM1poYkM1b2RHMXNLRzFwYmw5bWIzSnRZWFIwWldRcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pHVnVaRjkyWVd3dWFIUnRiQ2h0WVhoZlptOXliV0YwZEdWa0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHNXZWVWxQY0hScGIyNXpJRDBnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21GdVoyVTZJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBbmJXbHVKem9nV3lCd1lYSnpaVVpzYjJGMEtHMXBiaWtnWFN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQW5iV0Y0SnpvZ1d5QndZWEp6WlVac2IyRjBLRzFoZUNrZ1hWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITjBZWEowT2lCYmJXbHVYMlp2Y20xaGRIUmxaQ3dnYldGNFgyWnZjbTFoZEhSbFpGMHNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm9ZVzVrYkdWek9pQXlMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdZMjl1Ym1WamREb2dkSEoxWlN4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhOMFpYQTZJSEJoY25ObFJteHZZWFFvYzNSbGNDa3NYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR0psYUdGMmFXOTFjam9nSjJWNGRHVnVaQzEwWVhBbkxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1ptOXliV0YwT2lCbWFXVnNaRjltYjNKdFlYUmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlR0Y2JseHVYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9jMlZzWmk1cGMxOXlkR3c5UFRFcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzV2VlVsUGNIUnBiMjV6TG1ScGNtVmpkR2x2YmlBOUlGd2ljblJzWENJN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnYzJ4cFpHVnlYMjlpYW1WamRDQTlJQ1FvZEdocGN5a3VabWx1WkNoY0lpNXRaWFJoTFhOc2FXUmxjbHdpS1Zzd1hUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppZ2dYQ0oxYm1SbFptbHVaV1JjSWlBaFBUMGdkSGx3Wlc5bUtDQnpiR2xrWlhKZmIySnFaV04wTG01dlZXbFRiR2xrWlhJZ0tTQXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2WkdWemRISnZlU0JwWmlCcGRDQmxlR2x6ZEhNdUxpQjBhR2x6SUcxbFlXNXpJSE52YldWb2IzY2dZVzV2ZEdobGNpQnBibk4wWVc1alpTQm9ZV1FnYVc1cGRHbGhiR2x6WldRZ2FYUXVMbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMnhwWkdWeVgyOWlhbVZqZEM1dWIxVnBVMnhwWkdWeUxtUmxjM1J5YjNrb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzV2VldsVGJHbGtaWEl1WTNKbFlYUmxLSE5zYVdSbGNsOXZZbXBsWTNRc0lHNXZWVWxQY0hScGIyNXpLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtjM1JoY25SZmRtRnNMbTltWmlncE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrYzNSaGNuUmZkbUZzTG05dUtDZGphR0Z1WjJVbkxDQm1kVzVqZEdsdmJpZ3BlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMnhwWkdWeVgyOWlhbVZqZEM1dWIxVnBVMnhwWkdWeUxuTmxkQ2hiSkNoMGFHbHpLUzUyWVd3b0tTd2diblZzYkYwcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrWlc1a1gzWmhiQzV2Wm1Zb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkdWdVpGOTJZV3d1YjI0b0oyTm9ZVzVuWlNjc0lHWjFibU4wYVc5dUtDbDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpiR2xrWlhKZmIySnFaV04wTG01dlZXbFRiR2xrWlhJdWMyVjBLRnR1ZFd4c0xDQWtLSFJvYVhNcExuWmhiQ2dwWFNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SkhOMFlYSjBYM1poYkM1b2RHMXNLRzFwYmw5bWIzSnRZWFIwWldRcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlSbGJtUmZkbUZzTG1oMGJXd29iV0Y0WDJadmNtMWhkSFJsWkNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJ4cFpHVnlYMjlpYW1WamRDNXViMVZwVTJ4cFpHVnlMbTltWmlnbmRYQmtZWFJsSnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5zYVdSbGNsOXZZbXBsWTNRdWJtOVZhVk5zYVdSbGNpNXZiaWduZFhCa1lYUmxKeXdnWm5WdVkzUnBiMjRvSUhaaGJIVmxjeXdnYUdGdVpHeGxJQ2tnZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdjMnhwWkdWeVgzTjBZWEowWDNaaGJDQWdQU0J0YVc1ZlptOXliV0YwZEdWa08xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSE5zYVdSbGNsOWxibVJmZG1Gc0lDQTlJRzFoZUY5bWIzSnRZWFIwWldRN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUIyWVd4MVpTQTlJSFpoYkhWbGMxdG9ZVzVrYkdWZE8xeHVYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnZ2FHRnVaR3hsSUNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzFoZUY5bWIzSnRZWFIwWldRZ1BTQjJZV3gxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwZ1pXeHpaU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2JXbHVYMlp2Y20xaGRIUmxaQ0E5SUhaaGJIVmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmloa2FYTndiR0Y1WDNaaGJIVmxYMkZ6UFQxY0luUmxlSFJwYm5CMWRGd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSemRHRnlkRjkyWVd3dWRtRnNLRzFwYmw5bWIzSnRZWFIwWldRcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1JsYm1SZmRtRnNMblpoYkNodFlYaGZabTl5YldGMGRHVmtLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJVZ2FXWW9aR2x6Y0d4aGVWOTJZV3gxWlY5aGN6MDlYQ0owWlhoMFhDSXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkhOMFlYSjBYM1poYkM1b2RHMXNLRzFwYmw5bWIzSnRZWFIwWldRcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1JsYm1SZmRtRnNMbWgwYld3b2JXRjRYMlp2Y20xaGRIUmxaQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OXBJSFJvYVc1cklIUm9aU0JtZFc1amRHbHZiaUIwYUdGMElHSjFhV3hrY3lCMGFHVWdWVkpNSUc1bFpXUnpJSFJ2SUdSbFkyOWtaU0IwYUdVZ1ptOXliV0YwZEdWa0lITjBjbWx1WnlCaVpXWnZjbVVnWVdSa2FXNW5JSFJ2SUhSb1pTQjFjbXhjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0NoelpXeG1MbUYxZEc5ZmRYQmtZWFJsUFQweEtYeDhLSE5sYkdZdVlYVjBiMTlqYjNWdWRGOXlaV1p5WlhOb1gyMXZaR1U5UFRFcEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YjI1c2VTQjBjbmtnZEc4Z2RYQmtZWFJsSUdsbUlIUm9aU0IyWVd4MVpYTWdhR0YyWlNCaFkzUjFZV3hzZVNCamFHRnVaMlZrWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9LSE5zYVdSbGNsOXpkR0Z5ZEY5MllXd2hQVzFwYmw5bWIzSnRZWFIwWldRcGZId29jMnhwWkdWeVgyVnVaRjkyWVd3aFBXMWhlRjltYjNKdFlYUjBaV1FwS1NCN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVwYm5CMWRGVndaR0YwWlNnNE1EQXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1WTJ4bFlYSlVhVzFsY2lncE95QXZMMmxuYm05eVpTQmhibmtnWTJoaGJtZGxjeUJ5WldObGJuUnNlU0J0WVdSbElHSjVJSFJvWlNCemJHbGtaWElnS0hSb2FYTWdkMkZ6SUdwMWMzUWdhVzVwZENCemFHOTFiR1J1SjNRZ1kyOTFiblFnWVhNZ1lXNGdkWEJrWVhSbElHVjJaVzUwS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0I5TzF4dVhHNGdJQ0FnSUNBZ0lIUm9hWE11YVc1cGRDQTlJR1oxYm1OMGFXOXVLR3RsWlhCZmNHRm5hVzVoZEdsdmJpbGNiaUFnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtHdGxaWEJmY0dGbmFXNWhkR2x2YmlrOVBWd2lkVzVrWldacGJtVmtYQ0lwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUd0bFpYQmZjR0ZuYVc1aGRHbHZiaUE5SUdaaGJITmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCMGFHbHpMbWx1YVhSQmRYUnZWWEJrWVhSbFJYWmxiblJ6S0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtRjBkR0ZqYUVGamRHbDJaVU5zWVhOektDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11WVdSa1JHRjBaVkJwWTJ0bGNuTW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11WVdSa1VtRnVaMlZUYkdsa1pYSnpLQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2YVc1cGRDQmpiMjFpYnlCaWIzaGxjMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1JqYjIxaWIySnZlQ0E5SUNSMGFHbHpMbVpwYm1Rb1hDSnpaV3hsWTNSYlpHRjBZUzFqYjIxaWIySnZlRDBuTVNkZFhDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWdrWTI5dFltOWliM2d1YkdWdVozUm9QakFwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKR052YldKdlltOTRMbVZoWTJnb1puVnVZM1JwYjI0b2FXNWtaWGdnS1h0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSMGFHbHpZMklnUFNBa0tDQjBhR2x6SUNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCdWNtMGdQU0FrZEdocGMyTmlMbUYwZEhJb1hDSmtZWFJoTFdOdmJXSnZZbTk0TFc1eWJWd2lLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvZEhsd1pXOW1JQ1IwYUdselkySXVZMmh2YzJWdUlDRTlJRndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCamFHOXpaVzV2Y0hScGIyNXpJRDBnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxZWEpqYUY5amIyNTBZV2x1Y3pvZ2RISjFaVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZb0tIUjVjR1Z2WmlodWNtMHBJVDA5WENKMWJtUmxabWx1WldSY0lpa21KaWh1Y20wcEtYdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JqYUc5elpXNXZjSFJwYjI1ekxtNXZYM0psYzNWc2RITmZkR1Y0ZENBOUlHNXliVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUhOaFptVWdkRzhnZFhObElIUm9aU0JtZFc1amRHbHZibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTl6WldGeVkyaGZZMjl1ZEdGcGJuTmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdWFYTmZjblJzUFQweEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1IwYUdselkySXVZV1JrUTJ4aGMzTW9YQ0pqYUc5elpXNHRjblJzWENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtkR2hwYzJOaUxtTm9iM05sYmloamFHOXpaVzV2Y0hScGIyNXpLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JsYkhObFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlITmxiR1ZqZERKdmNIUnBiMjV6SUQwZ2UzMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0hObGJHWXVhWE5mY25Sc1BUMHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHVmpkREp2Y0hScGIyNXpMbVJwY2lBOUlGd2ljblJzWENJN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppZ29kSGx3Wlc5bUtHNXliU2toUFQxY0luVnVaR1ZtYVc1bFpGd2lLU1ltS0c1eWJTa3BlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHVmpkREp2Y0hScGIyNXpMbXhoYm1kMVlXZGxQU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRndpYm05U1pYTjFiSFJ6WENJNklHWjFibU4wYVc5dUtDbDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdibkp0TzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKSFJvYVhOallpNXpaV3hsWTNReUtITmxiR1ZqZERKdmNIUnBiMjV6S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbWx6VTNWaWJXbDBkR2x1WnlBOUlHWmhiSE5sTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJsbUlHRnFZWGdnYVhNZ1pXNWhZbXhsWkNCcGJtbDBJSFJvWlNCd1lXZHBibUYwYVc5dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloelpXeG1MbWx6WDJGcVlYZzlQVEVwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1elpYUjFjRUZxWVhoUVlXZHBibUYwYVc5dUtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNSMGFHbHpMbTl1S0Z3aWMzVmliV2wwWENJc0lIUm9hWE11YzNWaWJXbDBSbTl5YlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXVhVzVwZEZkdmIwTnZiVzFsY21ObFEyOXVkSEp2YkhNb0tUc2dMeTkzYjI5amIyMXRaWEpqWlNCdmNtUmxjbUo1WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0d0bFpYQmZjR0ZuYVc1aGRHbHZiajA5Wm1Gc2MyVXBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVzWVhOMFgzTjFZbTFwZEY5eGRXVnllVjl3WVhKaGJYTWdQU0J6Wld4bUxtZGxkRlZ5YkZCaGNtRnRjeWhtWVd4elpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0IwYUdsekxtOXVWMmx1Wkc5M1UyTnliMnhzSUQwZ1puVnVZM1JwYjI0b1pYWmxiblFwWEc0Z0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0NnaGMyVnNaaTVwYzE5c2IyRmthVzVuWDIxdmNtVXBJQ1ltSUNnaGMyVnNaaTVwYzE5dFlYaGZjR0ZuWldRcEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQjNhVzVrYjNkZmMyTnliMnhzSUQwZ0pDaDNhVzVrYjNjcExuTmpjbTlzYkZSdmNDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCM2FXNWtiM2RmYzJOeWIyeHNYMkp2ZEhSdmJTQTlJQ1FvZDJsdVpHOTNLUzV6WTNKdmJHeFViM0FvS1NBcklDUW9kMmx1Wkc5M0tTNW9aV2xuYUhRb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnYzJOeWIyeHNYMjltWm5ObGRDQTlJSEJoY25ObFNXNTBLSE5sYkdZdWFXNW1hVzVwZEdWZmMyTnliMnhzWDNSeWFXZG5aWEpmWVcxdmRXNTBLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtITmxiR1l1SkdsdVptbHVhWFJsWDNOamNtOXNiRjlqYjI1MFlXbHVaWEl1YkdWdVozUm9QVDB4S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhKbGMzVnNkSE5mYzJOeWIyeHNYMkp2ZEhSdmJTQTlJSE5sYkdZdUpHbHVabWx1YVhSbFgzTmpjbTlzYkY5amIyNTBZV2x1WlhJdWIyWm1jMlYwS0NrdWRHOXdJQ3NnYzJWc1ppNGthVzVtYVc1cGRHVmZjMk55YjJ4c1gyTnZiblJoYVc1bGNpNW9aV2xuYUhRb0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdiMlptYzJWMElEMGdLSE5sYkdZdUpHbHVabWx1YVhSbFgzTmpjbTlzYkY5amIyNTBZV2x1WlhJdWIyWm1jMlYwS0NrdWRHOXdJQ3NnYzJWc1ppNGthVzVtYVc1cGRHVmZjMk55YjJ4c1gyTnZiblJoYVc1bGNpNW9aV2xuYUhRb0tTa2dMU0IzYVc1a2IzZGZjMk55YjJ4c08xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtIZHBibVJ2ZDE5elkzSnZiR3hmWW05MGRHOXRJRDRnY21WemRXeDBjMTl6WTNKdmJHeGZZbTkwZEc5dElDc2djMk55YjJ4c1gyOW1abk5sZENsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1c2IyRmtUVzl5WlZKbGMzVnNkSE1vS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGJITmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhzdkwyUnZiblFnYkc5aFpDQnRiM0psWEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUhSb2FYTXVjM1J5YVhCUmRXVnllVk4wY21sdVowRnVaRWhoYzJoR2NtOXRVR0YwYUNBOUlHWjFibU4wYVc5dUtIVnliQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlIVnliQzV6Y0d4cGRDaGNJajljSWlsYk1GMHVjM0JzYVhRb1hDSWpYQ0lwV3pCZE8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnZEdocGN5NW5kWEFnUFNCbWRXNWpkR2x2YmlnZ2JtRnRaU3dnZFhKc0lDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLQ0YxY213cElIVnliQ0E5SUd4dlkyRjBhVzl1TG1oeVpXWmNiaUFnSUNBZ0lDQWdJQ0FnSUc1aGJXVWdQU0J1WVcxbExuSmxjR3hoWTJVb0wxdGNYRnRkTHl4Y0lseGNYRnhjWEZ0Y0lpa3VjbVZ3YkdGalpTZ3ZXMXhjWFYwdkxGd2lYRnhjWEZ4Y1hWd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ5WldkbGVGTWdQU0JjSWx0Y1hGeGNQeVpkWENJcmJtRnRaU3RjSWowb1cxNG1JMTBxS1Z3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlISmxaMlY0SUQwZ2JtVjNJRkpsWjBWNGNDZ2djbVZuWlhoVElDazdYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdjbVZ6ZFd4MGN5QTlJSEpsWjJWNExtVjRaV01vSUhWeWJDQXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUhKbGMzVnNkSE1nUFQwZ2JuVnNiQ0EvSUc1MWJHd2dPaUJ5WlhOMWJIUnpXekZkTzF4dUlDQWdJQ0FnSUNCOU8xeHVYRzVjYmlBZ0lDQWdJQ0FnZEdocGN5NW5aWFJWY214UVlYSmhiWE1nUFNCbWRXNWpkR2x2YmloclpXVndYM0JoWjJsdVlYUnBiMjRzSUhSNWNHVXNJR1Y0WTJ4MVpHVXBYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1LSFI1Y0dWdlppaHJaV1Z3WDNCaFoybHVZWFJwYjI0cFBUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCclpXVndYM0JoWjJsdVlYUnBiMjRnUFNCMGNuVmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaDBlWEJsYjJZb2RIbHdaU2s5UFZ3aWRXNWtaV1pwYm1Wa1hDSXBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSFI1Y0dVZ1BTQmNJbHdpTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnZFhKc1gzQmhjbUZ0YzE5emRISWdQU0JjSWx3aU8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkx5Qm5aWFFnWVd4c0lIQmhjbUZ0Y3lCbWNtOXRJR1pwWld4a2MxeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlIVnliRjl3WVhKaGJYTmZZWEp5WVhrZ1BTQndjbTlqWlhOelgyWnZjbTB1WjJWMFZYSnNVR0Z5WVcxektITmxiR1lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2JHVnVaM1JvSUQwZ1QySnFaV04wTG10bGVYTW9kWEpzWDNCaGNtRnRjMTloY25KaGVTa3ViR1Z1WjNSb08xeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHTnZkVzUwSUQwZ01EdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtHVjRZMngxWkdVcElUMWNJblZ1WkdWbWFXNWxaRndpS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWWdLSFZ5YkY5d1lYSmhiWE5mWVhKeVlYa3VhR0Z6VDNkdVVISnZjR1Z5ZEhrb1pYaGpiSFZrWlNrcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdWdVozUm9MUzA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWhzWlc1bmRHZytNQ2xjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm1iM0lnS0haaGNpQnJJR2x1SUhWeWJGOXdZWEpoYlhOZllYSnlZWGtwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0hWeWJGOXdZWEpoYlhOZllYSnlZWGt1YUdGelQzZHVVSEp2Y0dWeWRIa29heWtwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR05oYmw5aFpHUWdQU0IwY25WbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtHVjRZMngxWkdVcElUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LR3M5UFdWNFkyeDFaR1VwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWTJGdVgyRmtaQ0E5SUdaaGJITmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9ZMkZ1WDJGa1pDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhWeWJGOXdZWEpoYlhOZmMzUnlJQ3M5SUdzZ0t5QmNJajFjSWlBcklIVnliRjl3WVhKaGJYTmZZWEp5WVhsYmExMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9ZMjkxYm5RZ1BDQnNaVzVuZEdnZ0xTQXhLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ5YkY5d1lYSmhiWE5mYzNSeUlDczlJRndpSmx3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHTnZkVzUwS3lzN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ4ZFdWeWVWOXdZWEpoYlhNZ1BTQmNJbHdpTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJadmNtMGdjR0Z5WVcxeklHRnpJSFZ5YkNCeGRXVnllU0J6ZEhKcGJtZGNiaUFnSUNBZ0lDQWdJQ0FnSUhaaGNpQm1iM0p0WDNCaGNtRnRjeUE5SUhWeWJGOXdZWEpoYlhOZmMzUnlPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMMmRsZENCMWNtd2djR0Z5WVcxeklHWnliMjBnZEdobElHWnZjbTBnYVhSelpXeG1JQ2gzYUdGMElIUm9aU0IxYzJWeUlHaGhjeUJ6Wld4bFkzUmxaQ2xjYmlBZ0lDQWdJQ0FnSUNBZ0lIRjFaWEo1WDNCaGNtRnRjeUE5SUhObGJHWXVhbTlwYmxWeWJGQmhjbUZ0S0hGMVpYSjVYM0JoY21GdGN5d2dabTl5YlY5d1lYSmhiWE1wTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJGa1pDQndZV2RwYm1GMGFXOXVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaHJaV1Z3WDNCaFoybHVZWFJwYjI0OVBYUnlkV1VwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhCaFoyVk9kVzFpWlhJZ1BTQnpaV3htTGlSaGFtRjRYM0psYzNWc2RITmZZMjl1ZEdGcGJtVnlMbUYwZEhJb1hDSmtZWFJoTFhCaFoyVmtYQ0lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtIQmhaMlZPZFcxaVpYSXBQVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY0dGblpVNTFiV0psY2lBOUlERTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZb2NHRm5aVTUxYldKbGNqNHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NYVmxjbmxmY0dGeVlXMXpJRDBnYzJWc1ppNXFiMmx1VlhKc1VHRnlZVzBvY1hWbGNubGZjR0Z5WVcxekxDQmNJbk5tWDNCaFoyVmtQVndpSzNCaFoyVk9kVzFpWlhJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk5aFpHUWdjMlpwWkZ4dUlDQWdJQ0FnSUNBZ0lDQWdMeTl4ZFdWeWVWOXdZWEpoYlhNZ1BTQnpaV3htTG1wdmFXNVZjbXhRWVhKaGJTaHhkV1Z5ZVY5d1lYSmhiWE1zSUZ3aWMyWnBaRDFjSWl0elpXeG1Mbk5tYVdRcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkx5QnNiMjl3SUhSb2NtOTFaMmdnWVc1NUlHVjRkSEpoSUhCaGNtRnRjeUFvWm5KdmJTQmxlSFFnY0d4MVoybHVjeWtnWVc1a0lHRmtaQ0IwYnlCMGFHVWdkWEpzSUNocFpTQjNiMjlqYjIxdFpYSmpaU0JnYjNKa1pYSmllV0FwWEc0Z0lDQWdJQ0FnSUNBZ0lDQXZLblpoY2lCbGVIUnlZVjl4ZFdWeWVWOXdZWEpoYlNBOUlGd2lYQ0k3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUd4bGJtZDBhQ0E5SUU5aWFtVmpkQzVyWlhsektITmxiR1l1WlhoMGNtRmZjWFZsY25sZmNHRnlZVzF6S1M1c1pXNW5kR2c3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUdOdmRXNTBJRDBnTUR0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtHeGxibWQwYUQ0d0tWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUdadmNpQW9kbUZ5SUdzZ2FXNGdjMlZzWmk1bGVIUnlZVjl4ZFdWeWVWOXdZWEpoYlhNcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNCcFppQW9jMlZzWmk1bGVIUnlZVjl4ZFdWeWVWOXdZWEpoYlhNdWFHRnpUM2R1VUhKdmNHVnlkSGtvYXlrcElIdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdVpYaDBjbUZmY1hWbGNubGZjR0Z5WVcxelcydGRJVDFjSWx3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0JsZUhSeVlWOXhkV1Z5ZVY5d1lYSmhiU0E5SUdzclhDSTlYQ0lyYzJWc1ppNWxlSFJ5WVY5eGRXVnllVjl3WVhKaGJYTmJhMTA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdjWFZsY25sZmNHRnlZVzF6SUQwZ2MyVnNaaTVxYjJsdVZYSnNVR0Z5WVcwb2NYVmxjbmxmY0dGeVlXMXpMQ0JsZUhSeVlWOXhkV1Z5ZVY5d1lYSmhiU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDb3ZYRzRnSUNBZ0lDQWdJQ0FnSUNCeGRXVnllVjl3WVhKaGJYTWdQU0J6Wld4bUxtRmtaRkYxWlhKNVVHRnlZVzF6S0hGMVpYSjVYM0JoY21GdGN5d2djMlZzWmk1bGVIUnlZVjl4ZFdWeWVWOXdZWEpoYlhNdVlXeHNLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lvZEhsd1pTRTlYQ0pjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDNGMVpYSjVYM0JoY21GdGN5QTlJSE5sYkdZdVlXUmtVWFZsY25sUVlYSmhiWE1vY1hWbGNubGZjR0Z5WVcxekxDQnpaV3htTG1WNGRISmhYM0YxWlhKNVgzQmhjbUZ0YzF0MGVYQmxYU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCeGRXVnllVjl3WVhKaGJYTTdYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZEdocGN5NWhaR1JSZFdWeWVWQmhjbUZ0Y3lBOUlHWjFibU4wYVc5dUtIRjFaWEo1WDNCaGNtRnRjeXdnYm1WM1gzQmhjbUZ0Y3lsY2JpQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHVjRkSEpoWDNGMVpYSjVYM0JoY21GdElEMGdYQ0pjSWp0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCc1pXNW5kR2dnUFNCUFltcGxZM1F1YTJWNWN5aHVaWGRmY0dGeVlXMXpLUzVzWlc1bmRHZzdYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdZMjkxYm5RZ1BTQXdPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWhzWlc1bmRHZytNQ2xjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1p2Y2lBb2RtRnlJR3NnYVc0Z2JtVjNYM0JoY21GdGN5a2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9ibVYzWDNCaGNtRnRjeTVvWVhOUGQyNVFjbTl3WlhKMGVTaHJLU2tnZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppaHVaWGRmY0dGeVlXMXpXMnRkSVQxY0lsd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWNGRISmhYM0YxWlhKNVgzQmhjbUZ0SUQwZ2F5dGNJajFjSWl0dVpYZGZjR0Z5WVcxelcydGRPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhGMVpYSjVYM0JoY21GdGN5QTlJSE5sYkdZdWFtOXBibFZ5YkZCaGNtRnRLSEYxWlhKNVgzQmhjbUZ0Y3l3Z1pYaDBjbUZmY1hWbGNubGZjR0Z5WVcwcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2NYVmxjbmxmY0dGeVlXMXpPMXh1SUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUhSb2FYTXVZV1JrVlhKc1VHRnlZVzBnUFNCbWRXNWpkR2x2YmloMWNtd3NJSE4wY21sdVp5bGNiaUFnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR0ZrWkY5d1lYSmhiWE1nUFNCY0lsd2lPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWgxY213aFBWd2lYQ0lwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvZFhKc0xtbHVaR1Y0VDJZb1hDSS9YQ0lwSUNFOUlDMHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1lXUmtYM0JoY21GdGN5QXJQU0JjSWlaY0lqdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaV3h6WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTkxY213Z1BTQjBhR2x6TG5SeVlXbHNhVzVuVTJ4aGMyaEpkQ2gxY213cE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JoWkdSZmNHRnlZVzF6SUNzOUlGd2lQMXdpTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lvYzNSeWFXNW5JVDFjSWx3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUhWeWJDQXJJR0ZrWkY5d1lYSmhiWE1nS3lCemRISnBibWM3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCbGJITmxYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUhWeWJEdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ2ZUdGNibHh1SUNBZ0lDQWdJQ0IwYUdsekxtcHZhVzVWY214UVlYSmhiU0E5SUdaMWJtTjBhVzl1S0hCaGNtRnRjeXdnYzNSeWFXNW5LVnh1SUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnWVdSa1gzQmhjbUZ0Y3lBOUlGd2lYQ0k3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0hCaGNtRnRjeUU5WENKY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCaFpHUmZjR0Z5WVcxeklDczlJRndpSmx3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloemRISnBibWNoUFZ3aVhDSXBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z2NHRnlZVzF6SUNzZ1lXUmtYM0JoY21GdGN5QXJJSE4wY21sdVp6dGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJWY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdjR0Z5WVcxek8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCOU8xeHVYRzRnSUNBZ0lDQWdJSFJvYVhNdWMyVjBRV3BoZUZKbGMzVnNkSE5WVWt4eklEMGdablZ1WTNScGIyNG9jWFZsY25sZmNHRnlZVzF6S1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloMGVYQmxiMllvYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVppazlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVppQTlJRzVsZHlCQmNuSmhlU2dwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1GcVlYaGZjbVZ6ZFd4MGMxOWpiMjVtV3lkd2NtOWpaWE56YVc1blgzVnliQ2RkSUQwZ1hDSmNJanRjYmlBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1WVdwaGVGOXlaWE4xYkhSelgyTnZibVpiSjNKbGMzVnNkSE5mZFhKc0oxMGdQU0JjSWx3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVpsc25aR0YwWVY5MGVYQmxKMTBnUFNCY0lsd2lPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMMmxtS0hObGJHWXVZV3BoZUY5MWNtd2hQVndpWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloelpXeG1MbVJwYzNCc1lYbGZjbVZ6ZFd4MFgyMWxkR2h2WkQwOVhDSnphRzl5ZEdOdlpHVmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lIc3ZMM1JvWlc0Z2QyVWdkMkZ1ZENCMGJ5QmtieUJoSUhKbGNYVmxjM1FnZEc4Z2RHaGxJR0ZxWVhnZ1pXNWtjRzlwYm5SY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbUZxWVhoZmNtVnpkV3gwYzE5amIyNW1XeWR5WlhOMWJIUnpYM1Z5YkNkZElEMGdjMlZzWmk1aFpHUlZjbXhRWVhKaGJTaHpaV3htTG5KbGMzVnNkSE5mZFhKc0xDQnhkV1Z5ZVY5d1lYSmhiWE1wTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OWhaR1FnYkdGdVp5QmpiMlJsSUhSdklHRnFZWGdnWVhCcElISmxjWFZsYzNRc0lHeGhibWNnWTI5a1pTQnphRzkxYkdRZ1lXeHlaV0ZrZVNCaVpTQnBiaUIwYUdWeVpTQm1iM0lnYjNSb1pYSWdjbVZ4ZFdWemRITWdLR2xsTENCemRYQndiR2xsWkNCcGJpQjBhR1VnVW1WemRXeDBjeUJWVWt3cFhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWh6Wld4bUxteGhibWRmWTI5a1pTRTlYQ0pjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YzI4Z1lXUmtJR2wwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIRjFaWEo1WDNCaGNtRnRjeUE5SUhObGJHWXVhbTlwYmxWeWJGQmhjbUZ0S0hGMVpYSjVYM0JoY21GdGN5d2dYQ0pzWVc1blBWd2lLM05sYkdZdWJHRnVaMTlqYjJSbEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxtRnFZWGhmY21WemRXeDBjMTlqYjI1bVd5ZHdjbTlqWlhOemFXNW5YM1Z5YkNkZElEMGdjMlZzWmk1aFpHUlZjbXhRWVhKaGJTaHpaV3htTG1GcVlYaGZkWEpzTENCeGRXVnllVjl3WVhKaGJYTXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVpsc25aR0YwWVY5MGVYQmxKMTBnUFNBbmFuTnZiaWM3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lHVnNjMlVnYVdZb2MyVnNaaTVrYVhOd2JHRjVYM0psYzNWc2RGOXRaWFJvYjJROVBWd2ljRzl6ZEY5MGVYQmxYMkZ5WTJocGRtVmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQndjbTlqWlhOelgyWnZjbTB1YzJWMFZHRjRRWEpqYUdsMlpWSmxjM1ZzZEhOVmNtd29jMlZzWml3Z2MyVnNaaTV5WlhOMWJIUnpYM1Z5YkNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSEpsYzNWc2RITmZkWEpzSUQwZ2NISnZZMlZ6YzE5bWIzSnRMbWRsZEZKbGMzVnNkSE5WY213b2MyVnNaaXdnYzJWc1ppNXlaWE4xYkhSelgzVnliQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbUZxWVhoZmNtVnpkV3gwYzE5amIyNW1XeWR5WlhOMWJIUnpYM1Z5YkNkZElEMGdjMlZzWmk1aFpHUlZjbXhRWVhKaGJTaHlaWE4xYkhSelgzVnliQ3dnY1hWbGNubGZjR0Z5WVcxektUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1GcVlYaGZjbVZ6ZFd4MGMxOWpiMjVtV3lkd2NtOWpaWE56YVc1blgzVnliQ2RkSUQwZ2MyVnNaaTVoWkdSVmNteFFZWEpoYlNoeVpYTjFiSFJ6WDNWeWJDd2djWFZsY25sZmNHRnlZVzF6S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlNCcFppaHpaV3htTG1ScGMzQnNZWGxmY21WemRXeDBYMjFsZEdodlpEMDlYQ0pqZFhOMGIyMWZkMjl2WTI5dGJXVnlZMlZmYzNSdmNtVmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQndjbTlqWlhOelgyWnZjbTB1YzJWMFZHRjRRWEpqYUdsMlpWSmxjM1ZzZEhOVmNtd29jMlZzWml3Z2MyVnNaaTV5WlhOMWJIUnpYM1Z5YkNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSEpsYzNWc2RITmZkWEpzSUQwZ2NISnZZMlZ6YzE5bWIzSnRMbWRsZEZKbGMzVnNkSE5WY213b2MyVnNaaXdnYzJWc1ppNXlaWE4xYkhSelgzVnliQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbUZxWVhoZmNtVnpkV3gwYzE5amIyNW1XeWR5WlhOMWJIUnpYM1Z5YkNkZElEMGdjMlZzWmk1aFpHUlZjbXhRWVhKaGJTaHlaWE4xYkhSelgzVnliQ3dnY1hWbGNubGZjR0Z5WVcxektUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1GcVlYaGZjbVZ6ZFd4MGMxOWpiMjVtV3lkd2NtOWpaWE56YVc1blgzVnliQ2RkSUQwZ2MyVnNaaTVoWkdSVmNteFFZWEpoYlNoeVpYTjFiSFJ6WDNWeWJDd2djWFZsY25sZmNHRnlZVzF6S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdleTh2YjNSb1pYSjNhWE5sSUhkbElIZGhiblFnZEc4Z2NIVnNiQ0IwYUdVZ2NtVnpkV3gwY3lCa2FYSmxZM1JzZVNCbWNtOXRJSFJvWlNCeVpYTjFiSFJ6SUhCaFoyVmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1GcVlYaGZjbVZ6ZFd4MGMxOWpiMjVtV3lkeVpYTjFiSFJ6WDNWeWJDZGRJRDBnYzJWc1ppNWhaR1JWY214UVlYSmhiU2h6Wld4bUxuSmxjM1ZzZEhOZmRYSnNMQ0J4ZFdWeWVWOXdZWEpoYlhNcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1WVdwaGVGOXlaWE4xYkhSelgyTnZibVpiSjNCeWIyTmxjM05wYm1kZmRYSnNKMTBnUFNCelpXeG1MbUZrWkZWeWJGQmhjbUZ0S0hObGJHWXVZV3BoZUY5MWNtd3NJSEYxWlhKNVgzQmhjbUZ0Y3lrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OXpaV3htTG1GcVlYaGZjbVZ6ZFd4MGMxOWpiMjVtV3lka1lYUmhYM1I1Y0dVblhTQTlJQ2RvZEcxc0p6dGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVpsc25jSEp2WTJWemMybHVaMTkxY213blhTQTlJSE5sYkdZdVlXUmtVWFZsY25sUVlYSmhiWE1vYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVpsc25jSEp2WTJWemMybHVaMTkxY213blhTd2djMlZzWmk1bGVIUnlZVjl4ZFdWeWVWOXdZWEpoYlhOYkoyRnFZWGduWFNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXVZV3BoZUY5eVpYTjFiSFJ6WDJOdmJtWmJKMlJoZEdGZmRIbHdaU2RkSUQwZ2MyVnNaaTVoYW1GNFgyUmhkR0ZmZEhsd1pUdGNiaUFnSUNBZ0lDQWdmVHRjYmx4dVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1MWNHUmhkR1ZNYjJGa1pYSlVZV2NnUFNCbWRXNWpkR2x2Ymlna2IySnFaV04wS1NCN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhaaGNpQWtjR0Z5Wlc1ME8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaHpaV3htTG1sdVptbHVhWFJsWDNOamNtOXNiRjl5WlhOMWJIUmZZMnhoYzNNaFBWd2lYQ0lwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKSEJoY21WdWRDQTlJSE5sYkdZdUpHbHVabWx1YVhSbFgzTmpjbTlzYkY5amIyNTBZV2x1WlhJdVptbHVaQ2h6Wld4bUxtbHVabWx1YVhSbFgzTmpjbTlzYkY5eVpYTjFiSFJmWTJ4aGMzTXBMbXhoYzNRb0tTNXdZWEpsYm5Rb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJWY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FrY0dGeVpXNTBJRDBnYzJWc1ppNGthVzVtYVc1cGRHVmZjMk55YjJ4c1gyTnZiblJoYVc1bGNqdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlIUmhaMDVoYldVZ1BTQWtjR0Z5Wlc1MExuQnliM0FvWENKMFlXZE9ZVzFsWENJcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdkR0ZuVkhsd1pTQTlJQ2RrYVhZbk8xeHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZb0lDZ2dkR0ZuVG1GdFpTNTBiMHh2ZDJWeVEyRnpaU2dwSUQwOUlDZHZiQ2NnS1NCOGZDQW9JSFJoWjA1aGJXVXVkRzlNYjNkbGNrTmhjMlVvS1NBOVBTQW5kV3duSUNrZ0tYdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBZV2RVZVhCbElEMGdKMnhwSnp0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSdVpYY2dQU0FrS0NjOEp5dDBZV2RVZVhCbEt5Y2dMejRuS1M1b2RHMXNLQ1J2WW1wbFkzUXVhSFJ0YkNncEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUhaaGNpQmhkSFJ5YVdKMWRHVnpJRDBnSkc5aWFtVmpkQzV3Y205d0tGd2lZWFIwY21saWRYUmxjMXdpS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z2JHOXZjQ0IwYUhKdmRXZG9JRHh6Wld4bFkzUStJR0YwZEhKcFluVjBaWE1nWVc1a0lHRndjR3g1SUhSb1pXMGdiMjRnUEdScGRqNWNiaUFnSUNBZ0lDQWdJQ0FnSUNRdVpXRmphQ2hoZEhSeWFXSjFkR1Z6TENCbWRXNWpkR2x2YmlncElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtibVYzTG1GMGRISW9kR2hwY3k1dVlXMWxMQ0IwYUdsekxuWmhiSFZsS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdKRzVsZHp0Y2JseHVJQ0FnSUNBZ0lDQjlYRzVjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbXh2WVdSTmIzSmxVbVZ6ZFd4MGN5QTlJR1oxYm1OMGFXOXVLQ2xjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lnS0NCMGFHbHpMbWx6WDIxaGVGOXdZV2RsWkNBOVBUMGdkSEoxWlNBcElIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200N1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1selgyeHZZV1JwYm1kZmJXOXlaU0E5SUhSeWRXVTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZkSEpwWjJkbGNpQnpkR0Z5ZENCbGRtVnVkRnh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1YyWlc1MFgyUmhkR0VnUFNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyWnBaRG9nYzJWc1ppNXpabWxrTEZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhSaGNtZGxkRk5sYkdWamRHOXlPaUJ6Wld4bUxtRnFZWGhmZEdGeVoyVjBYMkYwZEhJc1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RIbHdaVG9nWENKc2IyRmtYMjF2Y21WY0lpeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnZZbXBsWTNRNklITmxiR1pjYmlBZ0lDQWdJQ0FnSUNBZ0lIMDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1ZEhKcFoyZGxja1YyWlc1MEtGd2ljMlk2WVdwaGVITjBZWEowWENJc0lHVjJaVzUwWDJSaGRHRXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2NISnZZMlZ6YzE5bWIzSnRMbk5sZEZSaGVFRnlZMmhwZG1WU1pYTjFiSFJ6VlhKc0tITmxiR1lzSUhObGJHWXVjbVZ6ZFd4MGMxOTFjbXdwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhGMVpYSjVYM0JoY21GdGN5QTlJSE5sYkdZdVoyVjBWWEpzVUdGeVlXMXpLSFJ5ZFdVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXNZWE4wWDNOMVltMXBkRjl4ZFdWeWVWOXdZWEpoYlhNZ1BTQnpaV3htTG1kbGRGVnliRkJoY21GdGN5aG1ZV3h6WlNrN0lDOHZaM0poWWlCaElHTnZjSGtnYjJZZ2FIUmxJRlZTVENCd1lYSmhiWE1nZDJsMGFHOTFkQ0J3WVdkcGJtRjBhVzl1SUdGc2NtVmhaSGtnWVdSa1pXUmNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR0ZxWVhoZmNISnZZMlZ6YzJsdVoxOTFjbXdnUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR0ZxWVhoZmNtVnpkV3gwYzE5MWNtd2dQU0JjSWx3aU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHUmhkR0ZmZEhsd1pTQTlJRndpWENJN1hHNWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OXViM2NnWVdSa0lIUm9aU0J1WlhjZ2NHRm5hVzVoZEdsdmJseHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHNWxlSFJmY0dGblpXUmZiblZ0WW1WeUlEMGdkR2hwY3k1amRYSnlaVzUwWDNCaFoyVmtJQ3NnTVR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSEYxWlhKNVgzQmhjbUZ0Y3lBOUlITmxiR1l1YW05cGJsVnliRkJoY21GdEtIRjFaWEo1WDNCaGNtRnRjeXdnWENKelpsOXdZV2RsWkQxY0lpdHVaWGgwWDNCaFoyVmtYMjUxYldKbGNpazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YzJWMFFXcGhlRkpsYzNWc2RITlZVa3h6S0hGMVpYSjVYM0JoY21GdGN5azdYRzRnSUNBZ0lDQWdJQ0FnSUNCaGFtRjRYM0J5YjJObGMzTnBibWRmZFhKc0lEMGdjMlZzWmk1aGFtRjRYM0psYzNWc2RITmZZMjl1WmxzbmNISnZZMlZ6YzJsdVoxOTFjbXduWFR0Y2JpQWdJQ0FnSUNBZ0lDQWdJR0ZxWVhoZmNtVnpkV3gwYzE5MWNtd2dQU0J6Wld4bUxtRnFZWGhmY21WemRXeDBjMTlqYjI1bVd5ZHlaWE4xYkhSelgzVnliQ2RkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdaR0YwWVY5MGVYQmxJRDBnYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVpsc25aR0YwWVY5MGVYQmxKMTA3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2WVdKdmNuUWdZVzU1SUhCeVpYWnBiM1Z6SUdGcVlYZ2djbVZ4ZFdWemRITmNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUtITmxiR1l1YkdGemRGOWhhbUY0WDNKbGNYVmxjM1FwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1c1lYTjBYMkZxWVhoZmNtVnhkV1Z6ZEM1aFltOXlkQ2dwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWh6Wld4bUxuVnpaVjl6WTNKdmJHeGZiRzloWkdWeVBUMHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUFrYkc5aFpHVnlJRDBnSkNnblBHUnBkaTgrSnl4N1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ2RqYkdGemN5YzZJQ2R6WldGeVkyZ3RabWxzZEdWeUxYTmpjbTlzYkMxc2IyRmthVzVuSjF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgwcE95OHZMbUZ3Y0dWdVpGUnZLSE5sYkdZdUpHRnFZWGhmY21WemRXeDBjMTlqYjI1MFlXbHVaWElwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pHeHZZV1JsY2lBOUlITmxiR1l1ZFhCa1lYUmxURzloWkdWeVZHRm5LQ1JzYjJGa1pYSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXBibVpwYm1sMFpWTmpjbTlzYkVGd2NHVnVaQ2drYkc5aFpHVnlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXViR0Z6ZEY5aGFtRjRYM0psY1hWbGMzUWdQU0FrTG1kbGRDaGhhbUY0WDNCeWIyTmxjM05wYm1kZmRYSnNMQ0JtZFc1amRHbHZiaWhrWVhSaExDQnpkR0YwZFhNc0lISmxjWFZsYzNRcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNWpkWEp5Wlc1MFgzQmhaMlZrS3lzN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVzWVhOMFgyRnFZWGhmY21WeGRXVnpkQ0E5SUc1MWJHdzdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2THlBcUtpb3FLaW9xS2lvcUtpb3FLbHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUZSUFJFOGdMU0JRUVZOVVJTQlVTRWxUSUVGT1JDQlhRVlJEU0NCVVNFVWdVa1ZFU1ZKRlExUWdMU0JQVGt4WklFaEJVRkJGVGxNZ1YwbFVTQ0JYUXlBb1ExQlVJRUZPUkNCVVFWZ2dSRTlGVXlCT1QxUXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeThnYUhSMGNITTZMeTl6WldGeVkyZ3RabWxzZEdWeUxuUmxjM1F2Y0hKdlpIVmpkQzFqWVhSbFoyOXllUzlqYkc5MGFHbHVaeTkwYzJocGNuUnpMM0JoWjJVdk15OC9jMlpmY0dGblpXUTlNMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5MWNHUmhkR1Z6SUhSb1pTQnlaWE4xZEd4eklDWWdabTl5YlNCb2RHMXNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1aFpHUlNaWE4xYkhSektHUmhkR0VzSUdSaGRHRmZkSGx3WlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUgwc0lHUmhkR0ZmZEhsd1pTa3VabUZwYkNobWRXNWpkR2x2YmlocWNWaElVaXdnZEdWNGRGTjBZWFIxY3l3Z1pYSnliM0pVYUhKdmQyNHBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1JoZEdFZ1BTQjdmVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWVhSaExuTm1hV1FnUFNCelpXeG1Mbk5tYVdRN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHRjBZUzV2WW1wbFkzUWdQU0J6Wld4bU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHUmhkR0V1ZEdGeVoyVjBVMlZzWldOMGIzSWdQU0J6Wld4bUxtRnFZWGhmZEdGeVoyVjBYMkYwZEhJN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHRjBZUzVoYW1GNFZWSk1JRDBnWVdwaGVGOXdjbTlqWlhOemFXNW5YM1Z5YkR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1lYUmhMbXB4V0VoU0lEMGdhbkZZU0ZJN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHRjBZUzUwWlhoMFUzUmhkSFZ6SUQwZ2RHVjRkRk4wWVhSMWN6dGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmtZWFJoTG1WeWNtOXlWR2h5YjNkdUlEMGdaWEp5YjNKVWFISnZkMjQ3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNTBjbWxuWjJWeVJYWmxiblFvWENKelpqcGhhbUY0WlhKeWIzSmNJaXdnWkdGMFlTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHBMbUZzZDJGNWN5aG1kVzVqZEdsdmJpZ3BYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1JoZEdFZ1BTQjdmVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWVhSaExuTm1hV1FnUFNCelpXeG1Mbk5tYVdRN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHRjBZUzUwWVhKblpYUlRaV3hsWTNSdmNpQTlJSE5sYkdZdVlXcGhlRjkwWVhKblpYUmZZWFIwY2p0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1lYUmhMbTlpYW1WamRDQTlJSE5sYkdZN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWh6Wld4bUxuVnpaVjl6WTNKdmJHeGZiRzloWkdWeVBUMHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pHeHZZV1JsY2k1a1pYUmhZMmdvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1selgyeHZZV1JwYm1kZmJXOXlaU0E5SUdaaGJITmxPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNTBjbWxuWjJWeVJYWmxiblFvWENKelpqcGhhbUY0Wm1sdWFYTm9YQ0lzSUdSaGRHRXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzVjYmlBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNCMGFHbHpMbVpsZEdOb1FXcGhlRkpsYzNWc2RITWdQU0JtZFc1amRHbHZiaWdwWEc0Z0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2ZEhKcFoyZGxjaUJ6ZEdGeWRDQmxkbVZ1ZEZ4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUdWMlpXNTBYMlJoZEdFZ1BTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlpwWkRvZ2MyVnNaaTV6Wm1sa0xGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUmhjbWRsZEZObGJHVmpkRzl5T2lCelpXeG1MbUZxWVhoZmRHRnlaMlYwWDJGMGRISXNYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkSGx3WlRvZ1hDSnNiMkZrWDNKbGMzVnNkSE5jSWl4Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCdlltcGxZM1E2SUhObGJHWmNiaUFnSUNBZ0lDQWdJQ0FnSUgwN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXVkSEpwWjJkbGNrVjJaVzUwS0Z3aWMyWTZZV3BoZUhOMFlYSjBYQ0lzSUdWMlpXNTBYMlJoZEdFcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkwzSmxabTlqZFhNZ1lXNTVJR2x1Y0hWMElHWnBaV3hrY3lCaFpuUmxjaUIwYUdVZ1ptOXliU0JvWVhNZ1ltVmxiaUIxY0dSaGRHVmtYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdKR3hoYzNSZllXTjBhWFpsWDJsdWNIVjBYM1JsZUhRZ1BTQWtkR2hwY3k1bWFXNWtLQ2RwYm5CMWRGdDBlWEJsUFZ3aWRHVjRkRndpWFRwbWIyTjFjeWNwTG01dmRDaGNJaTV6Wmkxa1lYUmxjR2xqYTJWeVhDSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9KR3hoYzNSZllXTjBhWFpsWDJsdWNIVjBYM1JsZUhRdWJHVnVaM1JvUFQweEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQnNZWE4wWDJGamRHbDJaVjlwYm5CMWRGOTBaWGgwSUQwZ0pHeGhjM1JmWVdOMGFYWmxYMmx1Y0hWMFgzUmxlSFF1WVhSMGNpaGNJbTVoYldWY0lpazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNSMGFHbHpMbUZrWkVOc1lYTnpLRndpYzJWaGNtTm9MV1pwYkhSbGNpMWthWE5oWW14bFpGd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIQnliMk5sYzNOZlptOXliUzVrYVhOaFlteGxTVzV3ZFhSektITmxiR1lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJaaFpHVWdiM1YwSUhKbGMzVnNkSE5jYmlBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1SkdGcVlYaGZjbVZ6ZFd4MGMxOWpiMjUwWVdsdVpYSXVZVzVwYldGMFpTaDdJRzl3WVdOcGRIazZJREF1TlNCOUxDQmNJbVpoYzNSY0lpazdJQzh2Ykc5aFpHbHVaMXh1SUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVtWVdSbFEyOXVkR1Z1ZEVGeVpXRnpLQ0JjSW05MWRGd2lJQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0hObGJHWXVZV3BoZUY5aFkzUnBiMjQ5UFZ3aWNHRm5hVzVoZEdsdmJsd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZibVZsWkNCMGJ5QnlaVzF2ZG1VZ1lXTjBhWFpsSUdacGJIUmxjaUJtY205dElGVlNURnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5eGRXVnllVjl3WVhKaGJYTWdQU0J6Wld4bUxteGhjM1JmYzNWaWJXbDBYM0YxWlhKNVgzQmhjbUZ0Y3p0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZibTkzSUdGa1pDQjBhR1VnYm1WM0lIQmhaMmx1WVhScGIyNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnY0dGblpVNTFiV0psY2lBOUlITmxiR1l1SkdGcVlYaGZjbVZ6ZFd4MGMxOWpiMjUwWVdsdVpYSXVZWFIwY2loY0ltUmhkR0V0Y0dGblpXUmNJaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppaDBlWEJsYjJZb2NHRm5aVTUxYldKbGNpazlQVndpZFc1a1pXWnBibVZrWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J3WVdkbFRuVnRZbVZ5SUQwZ01UdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjSEp2WTJWemMxOW1iM0p0TG5ObGRGUmhlRUZ5WTJocGRtVlNaWE4xYkhSelZYSnNLSE5sYkdZc0lITmxiR1l1Y21WemRXeDBjMTkxY213cE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIRjFaWEo1WDNCaGNtRnRjeUE5SUhObGJHWXVaMlYwVlhKc1VHRnlZVzF6S0daaGJITmxLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtIQmhaMlZPZFcxaVpYSStNU2xjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIRjFaWEo1WDNCaGNtRnRjeUE5SUhObGJHWXVhbTlwYmxWeWJGQmhjbUZ0S0hGMVpYSjVYM0JoY21GdGN5d2dYQ0p6Wmw5d1lXZGxaRDFjSWl0d1lXZGxUblZ0WW1WeUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUdWc2MyVWdhV1lvYzJWc1ppNWhhbUY0WDJGamRHbHZiajA5WENKemRXSnRhWFJjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2NYVmxjbmxmY0dGeVlXMXpJRDBnYzJWc1ppNW5aWFJWY214UVlYSmhiWE1vZEhKMVpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1c1lYTjBYM04xWW0xcGRGOXhkV1Z5ZVY5d1lYSmhiWE1nUFNCelpXeG1MbWRsZEZWeWJGQmhjbUZ0Y3lobVlXeHpaU2s3SUM4dlozSmhZaUJoSUdOdmNIa2diMllnYUhSbElGVlNUQ0J3WVhKaGJYTWdkMmwwYUc5MWRDQndZV2RwYm1GMGFXOXVJR0ZzY21WaFpIa2dZV1JrWldSY2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUdGcVlYaGZjSEp2WTJWemMybHVaMTkxY213Z1BTQmNJbHdpTzF4dUlDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUdGcVlYaGZjbVZ6ZFd4MGMxOTFjbXdnUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1JoZEdGZmRIbHdaU0E5SUZ3aVhDSTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YzJWMFFXcGhlRkpsYzNWc2RITlZVa3h6S0hGMVpYSjVYM0JoY21GdGN5azdYRzRnSUNBZ0lDQWdJQ0FnSUNCaGFtRjRYM0J5YjJObGMzTnBibWRmZFhKc0lEMGdjMlZzWmk1aGFtRjRYM0psYzNWc2RITmZZMjl1WmxzbmNISnZZMlZ6YzJsdVoxOTFjbXduWFR0Y2JpQWdJQ0FnSUNBZ0lDQWdJR0ZxWVhoZmNtVnpkV3gwYzE5MWNtd2dQU0J6Wld4bUxtRnFZWGhmY21WemRXeDBjMTlqYjI1bVd5ZHlaWE4xYkhSelgzVnliQ2RkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdaR0YwWVY5MGVYQmxJRDBnYzJWc1ppNWhhbUY0WDNKbGMzVnNkSE5mWTI5dVpsc25aR0YwWVY5MGVYQmxKMTA3WEc1Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnTHk5aFltOXlkQ0JoYm5rZ2NISmxkbWx2ZFhNZ1lXcGhlQ0J5WlhGMVpYTjBjMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9jMlZzWmk1c1lYTjBYMkZxWVhoZmNtVnhkV1Z6ZENsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxteGhjM1JmWVdwaGVGOXlaWEYxWlhOMExtRmliM0owS0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnWVdwaGVGOWhZM1JwYjI0Z1BTQnpaV3htTG1GcVlYaGZZV04wYVc5dU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXNZWE4wWDJGcVlYaGZjbVZ4ZFdWemRDQTlJQ1F1WjJWMEtHRnFZWGhmY0hKdlkyVnpjMmx1WjE5MWNtd3NJR1oxYm1OMGFXOXVLR1JoZEdFc0lITjBZWFIxY3l3Z2NtVnhkV1Z6ZENsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxteGhjM1JmWVdwaGVGOXlaWEYxWlhOMElEMGdiblZzYkR0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZkWEJrWVhSbGN5QjBhR1VnY21WemRYUnNjeUFtSUdadmNtMGdhSFJ0YkZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVkWEJrWVhSbFVtVnpkV3gwY3loa1lYUmhMQ0JrWVhSaFgzUjVjR1VwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdjMk55YjJ4c0lGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJSE5sZENCMGFHVWdkbUZ5SUdKaFkyc2dkRzhnZDJoaGRDQnBkQ0IzWVhNZ1ltVm1iM0psSUhSb1pTQmhhbUY0SUhKbGNYVmxjM1FnYm1Ga0lIUm9aU0JtYjNKdElISmxMV2x1YVhSY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbUZxWVhoZllXTjBhVzl1SUQwZ1lXcGhlRjloWTNScGIyNDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1elkzSnZiR3hTWlhOMWJIUnpLQ0J6Wld4bUxtRnFZWGhmWVdOMGFXOXVJQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdktpQjFjR1JoZEdVZ1ZWSk1JQ292WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5MWNHUmhkR1VnZFhKc0lHSmxabTl5WlNCd1lXZHBibUYwYVc5dUxDQmlaV05oZFhObElIZGxJRzVsWldRZ2RHOGdaRzhnYzI5dFpTQmphR1ZqYTNNZ1lXZGhhVzV6SUhSb1pTQlZVa3dnWm05eUlHbHVabWx1YVhSbElITmpjbTlzYkZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVkWEJrWVhSbFZYSnNTR2x6ZEc5eWVTaGhhbUY0WDNKbGMzVnNkSE5mZFhKc0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YzJWMGRYQWdjR0ZuYVc1aGRHbHZibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdWMyVjBkWEJCYW1GNFVHRm5hVzVoZEdsdmJpZ3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXBjMU4xWW0xcGRIUnBibWNnUFNCbVlXeHpaVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4cUlIVnpaWElnWkdWbUlDb3ZYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1cGJtbDBWMjl2UTI5dGJXVnlZMlZEYjI1MGNtOXNjeWdwT3lBdkwzZHZiMk52YlcxbGNtTmxJRzl5WkdWeVlubGNibHh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlMQ0JrWVhSaFgzUjVjR1VwTG1aaGFXd29ablZ1WTNScGIyNG9hbkZZU0ZJc0lIUmxlSFJUZEdGMGRYTXNJR1Z5Y205eVZHaHliM2R1S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCa1lYUmhJRDBnZTMwN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pHRjBZUzV6Wm1sa0lEMGdjMlZzWmk1elptbGtPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JoZEdFdWRHRnlaMlYwVTJWc1pXTjBiM0lnUFNCelpXeG1MbUZxWVhoZmRHRnlaMlYwWDJGMGRISTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR0YwWVM1dlltcGxZM1FnUFNCelpXeG1PMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JoZEdFdVlXcGhlRlZTVENBOUlHRnFZWGhmY0hKdlkyVnpjMmx1WjE5MWNtdzdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR0YwWVM1cWNWaElVaUE5SUdweFdFaFNPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1JoZEdFdWRHVjRkRk4wWVhSMWN5QTlJSFJsZUhSVGRHRjBkWE03WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWkdGMFlTNWxjbkp2Y2xSb2NtOTNiaUE5SUdWeWNtOXlWR2h5YjNkdU8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YVhOVGRXSnRhWFIwYVc1bklEMGdabUZzYzJVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTUwY21sbloyVnlSWFpsYm5Rb1hDSnpaanBoYW1GNFpYSnliM0pjSWl3Z1pHRjBZU2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBwTG1Gc2QyRjVjeWhtZFc1amRHbHZiaWdwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk0a1lXcGhlRjl5WlhOMWJIUnpYMk52Ym5SaGFXNWxjaTV6ZEc5d0tIUnlkV1VzZEhKMVpTa3VZVzVwYldGMFpTaDdJRzl3WVdOcGRIazZJREY5TENCY0ltWmhjM1JjSWlrN0lDOHZabWx1YVhOb1pXUWdiRzloWkdsdVoxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1Wm1Ga1pVTnZiblJsYm5SQmNtVmhjeWdnWENKcGJsd2lJQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHUmhkR0VnUFNCN2ZUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmtZWFJoTG5ObWFXUWdQU0J6Wld4bUxuTm1hV1E3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWkdGMFlTNTBZWEpuWlhSVFpXeGxZM1J2Y2lBOUlITmxiR1l1WVdwaGVGOTBZWEpuWlhSZllYUjBjanRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWVhSaExtOWlhbVZqZENBOUlITmxiR1k3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkhSb2FYTXVjbVZ0YjNabFEyeGhjM01vWENKelpXRnlZMmd0Wm1sc2RHVnlMV1JwYzJGaWJHVmtYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhCeWIyTmxjM05mWm05eWJTNWxibUZpYkdWSmJuQjFkSE1vYzJWc1ppazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDNKbFptOWpkWE1nZEdobElHeGhjM1FnWVdOMGFYWmxJSFJsZUhRZ1ptbGxiR1JjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmloc1lYTjBYMkZqZEdsMlpWOXBibkIxZEY5MFpYaDBJVDFjSWx3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUnBibkIxZENBOUlGdGRPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MaVJtYVdWc1pITXVaV0ZqYUNobWRXNWpkR2x2YmlncGUxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnSkdGamRHbDJaVjlwYm5CMWRDQTlJQ1FvZEdocGN5a3VabWx1WkNoY0ltbHVjSFYwVzI1aGJXVTlKMXdpSzJ4aGMzUmZZV04wYVhabFgybHVjSFYwWDNSbGVIUXJYQ0luWFZ3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LQ1JoWTNScGRtVmZhVzV3ZFhRdWJHVnVaM1JvUFQweEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1JwYm5CMWRDQTlJQ1JoWTNScGRtVmZhVzV3ZFhRN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0NScGJuQjFkQzVzWlc1bmRHZzlQVEVwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pHbHVjSFYwTG1adlkzVnpLQ2t1ZG1Gc0tDUnBibkIxZEM1MllXd29LU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxtWnZZM1Z6UTJGdGNHOG9KR2x1Y0hWMFd6QmRLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1IwYUdsekxtWnBibVFvWENKcGJuQjFkRnR1WVcxbFBTZGZjMlpmYzJWaGNtTm9KMTFjSWlrdWRISnBaMmRsY2lnblptOWpkWE1uS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MblJ5YVdkblpYSkZkbVZ1ZENoY0luTm1PbUZxWVhobWFXNXBjMmhjSWl3Z0lHUmhkR0VnS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNGdJQ0FnSUNBZ0lIMDdYRzVjYmlBZ0lDQWdJQ0FnZEdocGN5NW1iMk4xYzBOaGJYQnZJRDBnWm5WdVkzUnBiMjRvYVc1d2RYUkdhV1ZzWkNsN1hHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDNaaGNpQnBibkIxZEVacFpXeGtJRDBnWkc5amRXMWxiblF1WjJWMFJXeGxiV1Z1ZEVKNVNXUW9hV1FwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dsdWNIVjBSbWxsYkdRZ0lUMGdiblZzYkNBbUppQnBibkIxZEVacFpXeGtMblpoYkhWbExteGxibWQwYUNBaFBTQXdLWHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2FXNXdkWFJHYVdWc1pDNWpjbVZoZEdWVVpYaDBVbUZ1WjJVcGUxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ1JtbGxiR1JTWVc1blpTQTlJR2x1Y0hWMFJtbGxiR1F1WTNKbFlYUmxWR1Y0ZEZKaGJtZGxLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lFWnBaV3hrVW1GdVoyVXViVzkyWlZOMFlYSjBLQ2RqYUdGeVlXTjBaWEluTEdsdWNIVjBSbWxsYkdRdWRtRnNkV1V1YkdWdVozUm9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1JtbGxiR1JTWVc1blpTNWpiMnhzWVhCelpTZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCR2FXVnNaRkpoYm1kbExuTmxiR1ZqZENncE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWxiSE5sSUdsbUlDaHBibkIxZEVacFpXeGtMbk5sYkdWamRHbHZibE4wWVhKMElIeDhJR2x1Y0hWMFJtbGxiR1F1YzJWc1pXTjBhVzl1VTNSaGNuUWdQVDBnSnpBbktTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQmxiR1Z0VEdWdUlEMGdhVzV3ZFhSR2FXVnNaQzUyWVd4MVpTNXNaVzVuZEdnN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2x1Y0hWMFJtbGxiR1F1YzJWc1pXTjBhVzl1VTNSaGNuUWdQU0JsYkdWdFRHVnVPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcGJuQjFkRVpwWld4a0xuTmxiR1ZqZEdsdmJrVnVaQ0E5SUdWc1pXMU1aVzQ3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsdWNIVjBSbWxsYkdRdVlteDFjaWdwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsdWNIVjBSbWxsYkdRdVptOWpkWE1vS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBnWld4elpYdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvSUdsdWNIVjBSbWxsYkdRZ0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsdWNIVjBSbWxsYkdRdVptOWpkWE1vS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQjBhR2x6TG5SeWFXZG5aWEpGZG1WdWRDQTlJR1oxYm1OMGFXOXVLR1YyWlc1MGJtRnRaU3dnWkdGMFlTbGNiaUFnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1JsZG1WdWRGOWpiMjUwWVdsdVpYSWdQU0FrS0Z3aUxuTmxZWEpqYUdGdVpHWnBiSFJsY2x0a1lYUmhMWE5tTFdadmNtMHRhV1E5SjF3aUszTmxiR1l1YzJacFpDdGNJaWRkWENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSkdWMlpXNTBYMk52Ym5SaGFXNWxjaTUwY21sbloyVnlLR1YyWlc1MGJtRnRaU3dnV3lCa1lYUmhJRjBwTzF4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1bVpYUmphRUZxWVhoR2IzSnRJRDBnWm5WdVkzUnBiMjRvS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDNSeWFXZG5aWElnYzNSaGNuUWdaWFpsYm5SY2JpQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCbGRtVnVkRjlrWVhSaElEMGdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5tYVdRNklITmxiR1l1YzJacFpDeGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBZWEpuWlhSVFpXeGxZM1J2Y2pvZ2MyVnNaaTVoYW1GNFgzUmhjbWRsZEY5aGRIUnlMRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFI1Y0dVNklGd2labTl5YlZ3aUxGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHOWlhbVZqZERvZ2MyVnNabHh1SUNBZ0lDQWdJQ0FnSUNBZ2ZUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTUwY21sbloyVnlSWFpsYm5Rb1hDSnpaanBoYW1GNFptOXliWE4wWVhKMFhDSXNJRnNnWlhabGJuUmZaR0YwWVNCZEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0pIUm9hWE11WVdSa1EyeGhjM01vWENKelpXRnlZMmd0Wm1sc2RHVnlMV1JwYzJGaWJHVmtYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdjSEp2WTJWemMxOW1iM0p0TG1ScGMyRmliR1ZKYm5CMWRITW9jMlZzWmlrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhaaGNpQnhkV1Z5ZVY5d1lYSmhiWE1nUFNCelpXeG1MbWRsZEZWeWJGQmhjbUZ0Y3lncE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaHpaV3htTG14aGJtZGZZMjlrWlNFOVhDSmNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMM052SUdGa1pDQnBkRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEYxWlhKNVgzQmhjbUZ0Y3lBOUlITmxiR1l1YW05cGJsVnliRkJoY21GdEtIRjFaWEo1WDNCaGNtRnRjeXdnWENKc1lXNW5QVndpSzNObGJHWXViR0Z1WjE5amIyUmxLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR0ZxWVhoZmNISnZZMlZ6YzJsdVoxOTFjbXdnUFNCelpXeG1MbUZrWkZWeWJGQmhjbUZ0S0hObGJHWXVZV3BoZUY5bWIzSnRYM1Z5YkN3Z2NYVmxjbmxmY0dGeVlXMXpLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJrWVhSaFgzUjVjR1VnUFNCY0ltcHpiMjVjSWp0Y2JseHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkwyRmliM0owSUdGdWVTQndjbVYyYVc5MWN5QmhhbUY0SUhKbGNYVmxjM1J6WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZLbWxtS0hObGJHWXViR0Z6ZEY5aGFtRjRYM0psY1hWbGMzUXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXViR0Z6ZEY5aGFtRjRYM0psY1hWbGMzUXVZV0p2Y25Rb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNCOUtpOWNibHh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZMM05sYkdZdWJHRnpkRjloYW1GNFgzSmxjWFZsYzNRZ1BWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBa0xtZGxkQ2hoYW1GNFgzQnliMk5sYzNOcGJtZGZkWEpzTENCbWRXNWpkR2x2Ymloa1lYUmhMQ0J6ZEdGMGRYTXNJSEpsY1hWbGMzUXBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OXpaV3htTG14aGMzUmZZV3BoZUY5eVpYRjFaWE4wSUQwZ2JuVnNiRHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dmRYQmtZWFJsY3lCMGFHVWdjbVZ6ZFhSc2N5QW1JR1p2Y20wZ2FIUnRiRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdWRYQmtZWFJsUm05eWJTaGtZWFJoTENCa1lYUmhYM1I1Y0dVcE8xeHVYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHNJR1JoZEdGZmRIbHdaU2t1Wm1GcGJDaG1kVzVqZEdsdmJpaHFjVmhJVWl3Z2RHVjRkRk4wWVhSMWN5d2daWEp5YjNKVWFISnZkMjRwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUdSaGRHRWdQU0I3ZlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1lYUmhMbk5tYVdRZ1BTQnpaV3htTG5ObWFXUTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR0YwWVM1MFlYSm5aWFJUWld4bFkzUnZjaUE5SUhObGJHWXVZV3BoZUY5MFlYSm5aWFJmWVhSMGNqdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmtZWFJoTG05aWFtVmpkQ0E5SUhObGJHWTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR0YwWVM1aGFtRjRWVkpNSUQwZ1lXcGhlRjl3Y205alpYTnphVzVuWDNWeWJEdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmtZWFJoTG1weFdFaFNJRDBnYW5GWVNGSTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR0YwWVM1MFpYaDBVM1JoZEhWeklEMGdkR1Y0ZEZOMFlYUjFjenRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JrWVhSaExtVnljbTl5VkdoeWIzZHVJRDBnWlhKeWIzSlVhSEp2ZDI0N1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTUwY21sbloyVnlSWFpsYm5Rb1hDSnpaanBoYW1GNFpYSnliM0pjSWl3Z1d5QmtZWFJoSUYwcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCOUtTNWhiSGRoZVhNb1puVnVZM1JwYjI0b0tWeHVJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQmtZWFJoSUQwZ2UzMDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdaR0YwWVM1elptbGtJRDBnYzJWc1ppNXpabWxrTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdSaGRHRXVkR0Z5WjJWMFUyVnNaV04wYjNJZ1BTQnpaV3htTG1GcVlYaGZkR0Z5WjJWMFgyRjBkSEk3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWkdGMFlTNXZZbXBsWTNRZ1BTQnpaV3htTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pIUm9hWE11Y21WdGIzWmxRMnhoYzNNb1hDSnpaV0Z5WTJndFptbHNkR1Z5TFdScGMyRmliR1ZrWENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIQnliMk5sYzNOZlptOXliUzVsYm1GaWJHVkpibkIxZEhNb2MyVnNaaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MblJ5YVdkblpYSkZkbVZ1ZENoY0luTm1PbUZxWVhobWIzSnRabWx1YVhOb1hDSXNJRnNnWkdGMFlTQmRLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1SUNBZ0lDQWdJQ0I5TzF4dVhHNGdJQ0FnSUNBZ0lIUm9hWE11WTI5d2VVeHBjM1JKZEdWdGMwTnZiblJsYm5SeklEMGdablZ1WTNScGIyNG9KR3hwYzNSZlpuSnZiU3dnSkd4cGMzUmZkRzhwWEc0Z0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2WTI5d2VTQnZkbVZ5SUdOb2FXeGtJR3hwYzNRZ2FYUmxiWE5jYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJzYVY5amIyNTBaVzUwYzE5aGNuSmhlU0E5SUc1bGR5QkJjbkpoZVNncE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHWnliMjFmWVhSMGNtbGlkWFJsY3lBOUlHNWxkeUJCY25KaGVTZ3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnSkdaeWIyMWZabWxsYkdSeklEMGdKR3hwYzNSZlpuSnZiUzVtYVc1a0tGd2lQaUIxYkNBK0lHeHBYQ0lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FrWm5KdmJWOW1hV1ZzWkhNdVpXRmphQ2htZFc1amRHbHZiaWhwS1h0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHeHBYMk52Ym5SbGJuUnpYMkZ5Y21GNUxuQjFjMmdvSkNoMGFHbHpLUzVvZEcxc0tDa3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHRjBkSEpwWW5WMFpYTWdQU0FrS0hSb2FYTXBMbkJ5YjNBb1hDSmhkSFJ5YVdKMWRHVnpYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdaeWIyMWZZWFIwY21saWRYUmxjeTV3ZFhOb0tHRjBkSEpwWW5WMFpYTXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5MllYSWdabWxsYkdSZmJtRnRaU0E5SUNRb2RHaHBjeWt1WVhSMGNpaGNJbVJoZEdFdGMyWXRabWxsYkdRdGJtRnRaVndpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwzWmhjaUIwYjE5bWFXVnNaQ0E5SUNSc2FYTjBYM1J2TG1acGJtUW9YQ0krSUhWc0lENGdiR2xiWkdGMFlTMXpaaTFtYVdWc1pDMXVZVzFsUFNkY0lpdG1hV1ZzWkY5dVlXMWxLMXdpSjExY0lpazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDNObGJHWXVZMjl3ZVVGMGRISnBZblYwWlhNb0pDaDBhR2x6S1N3Z0pHeHBjM1JmZEc4c0lGd2laR0YwWVMxelppMWNJaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2JHbGZhWFFnUFNBd08xeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUjBiMTltYVdWc1pITWdQU0FrYkdsemRGOTBieTVtYVc1a0tGd2lQaUIxYkNBK0lHeHBYQ0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdKSFJ2WDJacFpXeGtjeTVsWVdOb0tHWjFibU4wYVc5dUtHa3BlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1FvZEdocGN5a3VhSFJ0YkNoc2FWOWpiMjUwWlc1MGMxOWhjbkpoZVZ0c2FWOXBkRjBwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1JtY205dFgyWnBaV3hrSUQwZ0pDZ2tabkp2YlY5bWFXVnNaSE11WjJWMEtHeHBYMmwwS1NrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnSkhSdlgyWnBaV3hrSUQwZ0pDaDBhR2x6S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa2RHOWZabWxsYkdRdWNtVnRiM1psUVhSMGNpaGNJbVJoZEdFdGMyWXRkR0Y0YjI1dmJYa3RZWEpqYUdsMlpWd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxtTnZjSGxCZEhSeWFXSjFkR1Z6S0NSbWNtOXRYMlpwWld4a0xDQWtkRzlmWm1sbGJHUXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYkdsZmFYUXJLenRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMHBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQXZLblpoY2lBa1puSnZiVjltYVdWc1pITWdQU0FrYkdsemRGOW1jbTl0TG1acGJtUW9YQ0lnZFd3Z1BpQnNhVndpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pIUnZYMlpwWld4a2N5QTlJQ1JzYVhOMFgzUnZMbVpwYm1Rb1hDSWdQaUJzYVZ3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBa1puSnZiVjltYVdWc1pITXVaV0ZqYUNobWRXNWpkR2x2YmlocGJtUmxlQ3dnZG1Gc0tYdGNiaUFnSUNBZ0lDQWdJQ0FnSUNCcFppZ2tLSFJvYVhNcExtaGhjMEYwZEhKcFluVjBaU2hjSW1SaGRHRXRjMll0ZEdGNGIyNXZiWGt0WVhKamFHbDJaVndpS1NsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0I3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0IwYUdsekxtTnZjSGxCZEhSeWFXSjFkR1Z6S0NSc2FYTjBYMlp5YjIwc0lDUnNhWE4wWDNSdktUc3FMMXh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2RHaHBjeTUxY0dSaGRHVkdiM0p0UVhSMGNtbGlkWFJsY3lBOUlHWjFibU4wYVc5dUtDUnNhWE4wWDJaeWIyMHNJQ1JzYVhOMFgzUnZLVnh1SUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnWm5KdmJWOWhkSFJ5YVdKMWRHVnpJRDBnSkd4cGMzUmZabkp2YlM1d2NtOXdLRndpWVhSMGNtbGlkWFJsYzF3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUM4dklHeHZiM0FnZEdoeWIzVm5hQ0E4YzJWc1pXTjBQaUJoZEhSeWFXSjFkR1Z6SUdGdVpDQmhjSEJzZVNCMGFHVnRJRzl1SUR4a2FYWStYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUIwYjE5aGRIUnlhV0oxZEdWeklEMGdKR3hwYzNSZmRHOHVjSEp2Y0NoY0ltRjBkSEpwWW5WMFpYTmNJaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWtMbVZoWTJnb2RHOWZZWFIwY21saWRYUmxjeXdnWm5WdVkzUnBiMjRvS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pHeHBjM1JmZEc4dWNtVnRiM1psUVhSMGNpaDBhR2x6TG01aGJXVXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDUXVaV0ZqYUNobWNtOXRYMkYwZEhKcFluVjBaWE1zSUdaMWJtTjBhVzl1S0NrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUnNhWE4wWDNSdkxtRjBkSElvZEdocGN5NXVZVzFsTENCMGFHbHpMblpoYkhWbEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgwcE8xeHVYRzRnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbU52Y0hsQmRIUnlhV0oxZEdWeklEMGdablZ1WTNScGIyNG9KR1p5YjIwc0lDUjBieXdnY0hKbFptbDRLVnh1SUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnBaaWgwZVhCbGIyWW9jSEpsWm1sNEtUMDlYQ0oxYm1SbFptbHVaV1JjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2NISmxabWw0SUQwZ1hDSmNJanRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR1p5YjIxZllYUjBjbWxpZFhSbGN5QTlJQ1JtY205dExuQnliM0FvWENKaGRIUnlhV0oxZEdWelhDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnZEc5ZllYUjBjbWxpZFhSbGN5QTlJQ1IwYnk1d2NtOXdLRndpWVhSMGNtbGlkWFJsYzF3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNRdVpXRmphQ2gwYjE5aGRIUnlhV0oxZEdWekxDQm1kVzVqZEdsdmJpZ3BJSHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtIQnlaV1pwZUNFOVhDSmNJaWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvZEdocGN5NXVZVzFsTG1sdVpHVjRUMllvY0hKbFptbDRLU0E5UFNBd0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtkRzh1Y21WdGIzWmxRWFIwY2loMGFHbHpMbTVoYldVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdWc2MyVmNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dkpIUnZMbkpsYlc5MlpVRjBkSElvZEdocGN5NXVZVzFsS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSkM1bFlXTm9LR1p5YjIxZllYUjBjbWxpZFhSbGN5d2dablZ1WTNScGIyNG9LU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkhSdkxtRjBkSElvZEdocGN5NXVZVzFsTENCMGFHbHpMblpoYkhWbEtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgwcE8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnZEdocGN5NWpiM0I1Um05eWJVRjBkSEpwWW5WMFpYTWdQU0JtZFc1amRHbHZiaWdrWm5KdmJTd2dKSFJ2S1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FrZEc4dWNtVnRiM1psUVhSMGNpaGNJbVJoZEdFdFkzVnljbVZ1ZEMxMFlYaHZibTl0ZVMxaGNtTm9hWFpsWENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NWpiM0I1UVhSMGNtbGlkWFJsY3lna1puSnZiU3dnSkhSdktUdGNibHh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2RHaHBjeTUxY0dSaGRHVkdiM0p0SUQwZ1puVnVZM1JwYjI0b1pHRjBZU3dnWkdGMFlWOTBlWEJsS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloa1lYUmhYM1I1Y0dVOVBWd2lhbk52Ymx3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZXk4dmRHaGxiaUIzWlNCa2FXUWdZU0J5WlhGMVpYTjBJSFJ2SUhSb1pTQmhhbUY0SUdWdVpIQnZhVzUwTENCemJ5QmxlSEJsWTNRZ1lXNGdiMkpxWldOMElHSmhZMnRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtIUjVjR1Z2Wmloa1lYUmhXeWRtYjNKdEoxMHBJVDA5WENKMWJtUmxabWx1WldSY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dmNtVnRiM1psSUdGc2JDQmxkbVZ1ZEhNZ1puSnZiU0JUSmtZZ1ptOXliVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa2RHaHBjeTV2Wm1Zb0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwzSmxabkpsYzJnZ2RHaGxJR1p2Y20wZ0tHRjFkRzhnWTI5MWJuUXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVZMjl3ZVV4cGMzUkpkR1Z0YzBOdmJuUmxiblJ6S0NRb1pHRjBZVnNuWm05eWJTZGRLU3dnSkhSb2FYTXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZjbVVnYVc1cGRDQlRKa1lnWTJ4aGMzTWdiMjRnZEdobElHWnZjbTFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGtkR2hwY3k1elpXRnlZMmhCYm1SR2FXeDBaWElvS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDJsbUlHRnFZWGdnYVhNZ1pXNWhZbXhsWkNCcGJtbDBJSFJvWlNCd1lXZHBibUYwYVc5dVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NXBibWwwS0hSeWRXVXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdWFYTmZZV3BoZUQwOU1TbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXpaWFIxY0VGcVlYaFFZV2RwYm1GMGFXOXVLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JseHVJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSFJvYVhNdVlXUmtVbVZ6ZFd4MGN5QTlJR1oxYm1OMGFXOXVLR1JoZEdFc0lHUmhkR0ZmZEhsd1pTbGNiaUFnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9aR0YwWVY5MGVYQmxQVDFjSW1wemIyNWNJaWxjYmlBZ0lDQWdJQ0FnSUNBZ0lIc3ZMM1JvWlc0Z2QyVWdaR2xrSUdFZ2NtVnhkV1Z6ZENCMGJ5QjBhR1VnWVdwaGVDQmxibVJ3YjJsdWRDd2djMjhnWlhod1pXTjBJR0Z1SUc5aWFtVmpkQ0JpWVdOclhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OW5jbUZpSUhSb1pTQnlaWE4xYkhSeklHRnVaQ0JzYjJGa0lHbHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTl6Wld4bUxpUmhhbUY0WDNKbGMzVnNkSE5mWTI5dWRHRnBibVZ5TG1Gd2NHVnVaQ2hrWVhSaFd5ZHlaWE4xYkhSekoxMHBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdWJHOWhaRjl0YjNKbFgyaDBiV3dnUFNCa1lYUmhXeWR5WlhOMWJIUnpKMTA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCbGJITmxJR2xtS0dSaGRHRmZkSGx3WlQwOVhDSm9kRzFzWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3THk5M1pTQmhjbVVnWlhod1pXTjBhVzVuSUhSb1pTQm9kRzFzSUc5bUlIUm9aU0J5WlhOMWJIUnpJSEJoWjJVZ1ltRmpheXdnYzI4Z1pYaDBjbUZqZENCMGFHVWdhSFJ0YkNCM1pTQnVaV1ZrWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUmtZWFJoWDI5aWFpQTlJQ1FvWkdGMFlTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1c2IyRmtYMjF2Y21WZmFIUnRiQ0E5SUNSa1lYUmhYMjlpYWk1bWFXNWtLSE5sYkdZdVlXcGhlRjkwWVhKblpYUmZZWFIwY2lrdWFIUnRiQ2dwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQmpiMjV6YjJ4bExteHZaeWhjSWs1RlZ5QklWRTFNWENJc0lITmxiR1l1Ykc5aFpGOXRiM0psWDJoMGJXd3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnYVc1bWFXNXBkR1ZmYzJOeWIyeHNYMlZ1WkNBOUlHWmhiSE5sTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlna0tGd2lQR1JwZGo1Y0lpdHpaV3htTG14dllXUmZiVzl5WlY5b2RHMXNLMXdpUEM5a2FYWStYQ0lwTG1acGJtUW9YQ0piWkdGMFlTMXpaV0Z5WTJndFptbHNkR1Z5TFdGamRHbHZiajBuYVc1bWFXNXBkR1V0YzJOeWIyeHNMV1Z1WkNkZFhDSXBMbXhsYm1kMGFENHdLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbHVabWx1YVhSbFgzTmpjbTlzYkY5bGJtUWdQU0IwY25WbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJsbUlIUm9aWEpsSUdseklHRnViM1JvWlhJZ2MyVnNaV04wYjNJZ1ptOXlJR2x1Wm1sdWFYUmxJSE5qY205c2JDd2dabWx1WkNCMGFHVWdZMjl1ZEdWdWRITWdiMllnZEdoaGRDQnBibk4wWldGa1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloelpXeG1MbWx1Wm1sdWFYUmxYM05qY205c2JGOWpiMjUwWVdsdVpYSWhQVndpWENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXNiMkZrWDIxdmNtVmZhSFJ0YkNBOUlDUW9YQ0k4WkdsMlBsd2lLM05sYkdZdWJHOWhaRjl0YjNKbFgyaDBiV3dyWENJOEwyUnBkajVjSWlrdVptbHVaQ2h6Wld4bUxtbHVabWx1YVhSbFgzTmpjbTlzYkY5amIyNTBZV2x1WlhJcExtaDBiV3dvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdWFXNW1hVzVwZEdWZmMyTnliMnhzWDNKbGMzVnNkRjlqYkdGemN5RTlYQ0pjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pISmxjM1ZzZEY5cGRHVnRjeUE5SUNRb1hDSThaR2wyUGx3aUszTmxiR1l1Ykc5aFpGOXRiM0psWDJoMGJXd3JYQ0k4TDJScGRqNWNJaWt1Wm1sdVpDaHpaV3htTG1sdVptbHVhWFJsWDNOamNtOXNiRjl5WlhOMWJIUmZZMnhoYzNNcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUFrY21WemRXeDBYMmwwWlcxelgyTnZiblJoYVc1bGNpQTlJQ1FvSnp4a2FYWXZQaWNzSUh0OUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtjbVZ6ZFd4MFgybDBaVzF6WDJOdmJuUmhhVzVsY2k1aGNIQmxibVFvSkhKbGMzVnNkRjlwZEdWdGN5azdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxteHZZV1JmYlc5eVpWOW9kRzFzSUQwZ0pISmxjM1ZzZEY5cGRHVnRjMTlqYjI1MFlXbHVaWEl1YUhSdGJDZ3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaHBibVpwYm1sMFpWOXpZM0p2Ykd4ZlpXNWtLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2V5OHZkMlVnWm05MWJtUWdZU0JrWVhSaElHRjBkSEpwWW5WMFpTQnphV2R1WVd4c2FXNW5JSFJvWlNCc1lYTjBJSEJoWjJVZ2MyOGdabWx1YVhOb0lHaGxjbVZjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVhWE5mYldGNFgzQmhaMlZrSUQwZ2RISjFaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxteGhjM1JmYkc5aFpGOXRiM0psWDJoMGJXd2dQU0J6Wld4bUxteHZZV1JmYlc5eVpWOW9kRzFzTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVwYm1acGJtbDBaVk5qY205c2JFRndjR1Z1WkNoelpXeG1MbXh2WVdSZmJXOXlaVjlvZEcxc0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnWld4elpTQnBaaWh6Wld4bUxteGhjM1JmYkc5aFpGOXRiM0psWDJoMGJXd2hQVDF6Wld4bUxteHZZV1JmYlc5eVpWOW9kRzFzS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2WTJobFkyc2dkRzhnYldGclpTQnpkWEpsSUhSb1pTQnVaWGNnYUhSdGJDQm1aWFJqYUdWa0lHbHpJR1JwWm1abGNtVnVkRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdWJHRnpkRjlzYjJGa1gyMXZjbVZmYUhSdGJDQTlJSE5sYkdZdWJHOWhaRjl0YjNKbFgyaDBiV3c3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXBibVpwYm1sMFpWTmpjbTlzYkVGd2NHVnVaQ2h6Wld4bUxteHZZV1JmYlc5eVpWOW9kRzFzS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlZ4dUlDQWdJQ0FnSUNBZ0lDQWdleTh2ZDJVZ2NtVmpaV2wyWldRZ2RHaGxJSE5oYldVZ2JXVnpjMkZuWlNCaFoyRnBiaUJ6YnlCa2IyNG5kQ0JoWkdRc0lHRnVaQ0IwWld4c0lGTW1SaUIwYUdGMElIZGxKM0psSUdGMElIUm9aU0JsYm1RdUxseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YVhOZmJXRjRYM0JoWjJWa0lEMGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlZ4dVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1cGJtWnBibWwwWlZOamNtOXNiRUZ3Y0dWdVpDQTlJR1oxYm1OMGFXOXVLQ1J2WW1wbFkzUXBYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdWFXNW1hVzVwZEdWZmMyTnliMnhzWDNKbGMzVnNkRjlqYkdGemN5RTlYQ0pjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxpUnBibVpwYm1sMFpWOXpZM0p2Ykd4ZlkyOXVkR0ZwYm1WeUxtWnBibVFvYzJWc1ppNXBibVpwYm1sMFpWOXpZM0p2Ykd4ZmNtVnpkV3gwWDJOc1lYTnpLUzVzWVhOMEtDa3VZV1owWlhJb0pHOWlhbVZqZENrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQmxiSE5sWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTGlScGJtWnBibWwwWlY5elkzSnZiR3hmWTI5dWRHRnBibVZ5TG1Gd2NHVnVaQ2drYjJKcVpXTjBLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVnh1WEc1Y2JpQWdJQ0FnSUNBZ2RHaHBjeTUxY0dSaGRHVlNaWE4xYkhSeklEMGdablZ1WTNScGIyNG9aR0YwWVN3Z1pHRjBZVjkwZVhCbEtWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaGtZWFJoWDNSNWNHVTlQVndpYW5OdmJsd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2V5OHZkR2hsYmlCM1pTQmthV1FnWVNCeVpYRjFaWE4wSUhSdklIUm9aU0JoYW1GNElHVnVaSEJ2YVc1MExDQnpieUJsZUhCbFkzUWdZVzRnYjJKcVpXTjBJR0poWTJ0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyZHlZV0lnZEdobElISmxjM1ZzZEhNZ1lXNWtJR3h2WVdRZ2FXNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG5KbGMzVnNkSE5mYUhSdGJDQTlJR1JoZEdGYkozSmxjM1ZzZEhNblhUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnZ2RHaHBjeTV5WlhCc1lXTmxYM0psYzNWc2RITWdLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1SkdGcVlYaGZjbVZ6ZFd4MGMxOWpiMjUwWVdsdVpYSXVhSFJ0YkNoMGFHbHpMbkpsYzNWc2RITmZhSFJ0YkNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvZEhsd1pXOW1LR1JoZEdGYkoyWnZjbTBuWFNraFBUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTl5WlcxdmRtVWdZV3hzSUdWMlpXNTBjeUJtY205dElGTW1SaUJtYjNKdFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1IwYUdsekxtOW1aaWdwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2Y21WdGIzWmxJSEJoWjJsdVlYUnBiMjVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTV5WlcxdmRtVkJhbUY0VUdGbmFXNWhkR2x2YmlncE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dmNtVm1jbVZ6YUNCMGFHVWdabTl5YlNBb1lYVjBieUJqYjNWdWRDbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNWpiM0I1VEdsemRFbDBaVzF6UTI5dWRHVnVkSE1vSkNoa1lYUmhXeWRtYjNKdEoxMHBMQ0FrZEdocGN5azdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OTFjR1JoZEdVZ1lYUjBjbWxpZFhSbGN5QnZiaUJtYjNKdFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdVkyOXdlVVp2Y20xQmRIUnlhV0oxZEdWektDUW9aR0YwWVZzblptOXliU2RkS1N3Z0pIUm9hWE1wTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2Y21VZ2FXNXBkQ0JUSmtZZ1kyeGhjM01nYjI0Z2RHaGxJR1p2Y20xY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKSFJvYVhNdWMyVmhjbU5vUVc1a1JtbHNkR1Z5S0hzbmFYTkpibWwwSnpvZ1ptRnNjMlY5S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXeHpaVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGtkR2hwY3k1bWFXNWtLRndpYVc1d2RYUmNJaWt1Y21WdGIzWmxRWFIwY2loY0ltUnBjMkZpYkdWa1hDSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUdWc2MyVWdhV1lvWkdGMFlWOTBlWEJsUFQxY0ltaDBiV3hjSWlrZ2V5OHZkMlVnWVhKbElHVjRjR1ZqZEdsdVp5QjBhR1VnYUhSdGJDQnZaaUIwYUdVZ2NtVnpkV3gwY3lCd1lXZGxJR0poWTJzc0lITnZJR1Y0ZEhKaFkzUWdkR2hsSUdoMGJXd2dkMlVnYm1WbFpGeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSa1lYUmhYMjlpYWlBOUlDUW9aR0YwWVNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjBhR2x6TG5KbGMzVnNkSE5mYUhSdGJDQTlJQ1JrWVhSaFgyOWlhaTVtYVc1a0tDQjBhR2x6TG1GcVlYaGZkR0Z5WjJWMFgyRjBkSElnS1M1b2RHMXNLQ2s3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JSFJvYVhNdWNtVndiR0ZqWlY5eVpYTjFiSFJ6SUNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxpUmhhbUY0WDNKbGMzVnNkSE5mWTI5dWRHRnBibVZ5TG1oMGJXd29kR2hwY3k1eVpYTjFiSFJ6WDJoMGJXd3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVkWEJrWVhSbFEyOXVkR1Z1ZEVGeVpXRnpLQ0FrWkdGMFlWOXZZbW9nS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2h6Wld4bUxpUmhhbUY0WDNKbGMzVnNkSE5mWTI5dWRHRnBibVZ5TG1acGJtUW9YQ0l1YzJWaGNtTm9ZVzVrWm1sc2RHVnlYQ0lwTG14bGJtZDBhQ0ErSURBcFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2V5OHZkR2hsYmlCMGFHVnlaU0JoY21VZ2MyVmhjbU5vSUdadmNtMG9jeWtnYVc1emFXUmxJSFJvWlNCeVpYTjFiSFJ6SUdOdmJuUmhhVzVsY2l3Z2MyOGdjbVV0YVc1cGRDQjBhR1Z0WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk0a1lXcGhlRjl5WlhOMWJIUnpYMk52Ym5SaGFXNWxjaTVtYVc1a0tGd2lMbk5sWVhKamFHRnVaR1pwYkhSbGNsd2lLUzV6WldGeVkyaEJibVJHYVd4MFpYSW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwybG1JSFJvWlNCamRYSnlaVzUwSUhObFlYSmphQ0JtYjNKdElHbHpJRzV2ZENCcGJuTnBaR1VnZEdobElISmxjM1ZzZEhNZ1kyOXVkR0ZwYm1WeUxDQjBhR1Z1SUhCeWIyTmxaV1FnWVhNZ2JtOXliV0ZzSUdGdVpDQjFjR1JoZEdVZ2RHaGxJR1p2Y20xY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppaHpaV3htTGlSaGFtRjRYM0psYzNWc2RITmZZMjl1ZEdGcGJtVnlMbVpwYm1Rb1hDSXVjMlZoY21Ob1lXNWtabWxzZEdWeVcyUmhkR0V0YzJZdFptOXliUzFwWkQwblhDSWdLeUJ6Wld4bUxuTm1hV1FnS3lCY0lpZGRYQ0lwTG14bGJtZDBhRDA5TUNrZ2UxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQWtibVYzWDNObFlYSmphRjltYjNKdElEMGdKR1JoZEdGZmIySnFMbVpwYm1Rb1hDSXVjMlZoY21Ob1lXNWtabWxzZEdWeVcyUmhkR0V0YzJZdFptOXliUzFwWkQwblhDSWdLeUJ6Wld4bUxuTm1hV1FnS3lCY0lpZGRYQ0lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNna2JtVjNYM05sWVhKamFGOW1iM0p0TG14bGJtZDBhQ0E5UFNBeEtTQjdMeTkwYUdWdUlISmxjR3hoWTJVZ2RHaGxJSE5sWVhKamFDQm1iM0p0SUhkcGRHZ2dkR2hsSUc1bGR5QnZibVZjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5eVpXMXZkbVVnWVd4c0lHVjJaVzUwY3lCbWNtOXRJRk1tUmlCbWIzSnRYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtkR2hwY3k1dlptWW9LVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5eVpXMXZkbVVnY0dGbmFXNWhkR2x2Ymx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXlaVzF2ZG1WQmFtRjRVR0ZuYVc1aGRHbHZiaWdwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwzSmxabkpsYzJnZ2RHaGxJR1p2Y20wZ0tHRjFkRzhnWTI5MWJuUXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1OdmNIbE1hWE4wU1hSbGJYTkRiMjUwWlc1MGN5Z2tibVYzWDNObFlYSmphRjltYjNKdExDQWtkR2hwY3lrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZkWEJrWVhSbElHRjBkSEpwWW5WMFpYTWdiMjRnWm05eWJWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVqYjNCNVJtOXliVUYwZEhKcFluVjBaWE1vSkc1bGQxOXpaV0Z5WTJoZlptOXliU3dnSkhSb2FYTXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDNKbElHbHVhWFFnVXlaR0lHTnNZWE56SUc5dUlIUm9aU0JtYjNKdFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa2RHaHBjeTV6WldGeVkyaEJibVJHYVd4MFpYSW9leWRwYzBsdWFYUW5PaUJtWVd4elpYMHBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWld4elpTQjdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SkhSb2FYTXVabWx1WkNoY0ltbHVjSFYwWENJcExuSmxiVzkyWlVGMGRISW9YQ0prYVhOaFlteGxaRndpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVwYzE5dFlYaGZjR0ZuWldRZ1BTQm1ZV3h6WlRzZ0x5OW1iM0lnYVc1bWFXNXBkR1VnYzJOeWIyeHNYRzRnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbU4xY25KbGJuUmZjR0ZuWldRZ1BTQXhPeUF2TDJadmNpQnBibVpwYm1sMFpTQnpZM0p2Ykd4Y2JpQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdWMyVjBTVzVtYVc1cGRHVlRZM0p2Ykd4RGIyNTBZV2x1WlhJb0tUdGNibHh1SUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2RHaHBjeTUxY0dSaGRHVkRiMjUwWlc1MFFYSmxZWE1nUFNCbWRXNWpkR2x2YmlnZ0pHaDBiV3hmWkdGMFlTQXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lGeHVJQ0FnSUNBZ0lDQWdJQ0FnTHk4Z1lXUmtJR0ZrWkdsMGFXOXVZV3dnWTI5dWRHVnVkQ0JoY21WaGMxeHVJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tDQjBhR2x6TG1GcVlYaGZkWEJrWVhSbFgzTmxZM1JwYjI1eklDWW1JSFJvYVhNdVlXcGhlRjkxY0dSaGRHVmZjMlZqZEdsdmJuTXViR1Z1WjNSb0lDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1p2Y2lBb2FXNWtaWGdnUFNBd095QnBibVJsZUNBOElIUm9hWE11WVdwaGVGOTFjR1JoZEdWZmMyVmpkR2x2Ym5NdWJHVnVaM1JvT3lBcksybHVaR1Y0S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCelpXeGxZM1J2Y2lBOUlIUm9hWE11WVdwaGVGOTFjR1JoZEdWZmMyVmpkR2x2Ym5OYmFXNWtaWGhkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtLQ0J6Wld4bFkzUnZjaUFwTG1oMGJXd29JQ1JvZEcxc1gyUmhkR0V1Wm1sdVpDZ2djMlZzWldOMGIzSWdLUzVvZEcxc0tDa2dLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZEdocGN5NW1ZV1JsUTI5dWRHVnVkRUZ5WldGeklEMGdablZ1WTNScGIyNG9JR1JwY21WamRHbHZiaUFwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJRnh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJRzl3WVdOcGRIa2dQU0F3TGpVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb0lHUnBjbVZqZEdsdmJpQTlQVDBnWENKcGJsd2lJQ2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUc5d1lXTnBkSGtnUFNBeE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb0lIUm9hWE11WVdwaGVGOTFjR1JoZEdWZmMyVmpkR2x2Ym5NZ0ppWWdkR2hwY3k1aGFtRjRYM1Z3WkdGMFpWOXpaV04wYVc5dWN5NXNaVzVuZEdnZ0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdabTl5SUNocGJtUmxlQ0E5SURBN0lHbHVaR1Y0SUR3Z2RHaHBjeTVoYW1GNFgzVndaR0YwWlY5elpXTjBhVzl1Y3k1c1pXNW5kR2c3SUNzcmFXNWtaWGdwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhObGJHVmpkRzl5SUQwZ2RHaHBjeTVoYW1GNFgzVndaR0YwWlY5elpXTjBhVzl1YzF0cGJtUmxlRjA3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUW9JSE5sYkdWamRHOXlJQ2t1YzNSdmNDaDBjblZsTEhSeWRXVXBMbUZ1YVcxaGRHVW9JSHNnYjNCaFkybDBlVG9nYjNCaFkybDBlWDBzSUZ3aVptRnpkRndpSUNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQmNiaUFnSUNBZ0lDQWdJQ0FnSUZ4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1eVpXMXZkbVZYYjI5RGIyMXRaWEpqWlVOdmJuUnliMnh6SUQwZ1puVnVZM1JwYjI0b0tYdGNiaUFnSUNBZ0lDQWdJQ0FnSUhaaGNpQWtkMjl2WDI5eVpHVnlZbmtnUFNBa0tDY3VkMjl2WTI5dGJXVnlZMlV0YjNKa1pYSnBibWNnTG05eVpHVnlZbmtuS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFpoY2lBa2QyOXZYMjl5WkdWeVlubGZabTl5YlNBOUlDUW9KeTUzYjI5amIyMXRaWEpqWlMxdmNtUmxjbWx1WnljcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBa2QyOXZYMjl5WkdWeVlubGZabTl5YlM1dlptWW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDUjNiMjlmYjNKa1pYSmllUzV2Wm1Zb0tUdGNiaUFnSUNBZ0lDQWdmVHRjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbUZrWkZGMVpYSjVVR0Z5WVcwZ1BTQm1kVzVqZEdsdmJpaHVZVzFsTENCMllXeDFaU3dnZFhKc1gzUjVjR1VwZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmloMGVYQmxiMllvZFhKc1gzUjVjR1VwUFQxY0luVnVaR1ZtYVc1bFpGd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUIxY214ZmRIbHdaU0E5SUZ3aVlXeHNYQ0k3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbVY0ZEhKaFgzRjFaWEo1WDNCaGNtRnRjMXQxY214ZmRIbHdaVjFiYm1GdFpWMGdQU0IyWVd4MVpUdGNibHh1SUNBZ0lDQWdJQ0I5TzF4dVhHNGdJQ0FnSUNBZ0lIUm9hWE11YVc1cGRGZHZiME52YlcxbGNtTmxRMjl1ZEhKdmJITWdQU0JtZFc1amRHbHZiaWdwZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0J6Wld4bUxuSmxiVzkyWlZkdmIwTnZiVzFsY21ObFEyOXVkSEp2YkhNb0tUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1IzYjI5ZmIzSmtaWEppZVNBOUlDUW9KeTUzYjI5amIyMXRaWEpqWlMxdmNtUmxjbWx1WnlBdWIzSmtaWEppZVNjcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUjNiMjlmYjNKa1pYSmllVjltYjNKdElEMGdKQ2duTG5kdmIyTnZiVzFsY21ObExXOXlaR1Z5YVc1bkp5azdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ2Y21SbGNsOTJZV3dnUFNCY0lsd2lPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9KSGR2YjE5dmNtUmxjbUo1TG14bGJtZDBhRDR3S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRzl5WkdWeVgzWmhiQ0E5SUNSM2IyOWZiM0prWlhKaWVTNTJZV3dvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lHVnNjMlZjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnZjbVJsY2w5MllXd2dQU0J6Wld4bUxtZGxkRkYxWlhKNVVHRnlZVzFHY205dFZWSk1LRndpYjNKa1pYSmllVndpTENCM2FXNWtiM2N1Ykc5allYUnBiMjR1YUhKbFppazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUtHOXlaR1Z5WDNaaGJEMDlYQ0p0Wlc1MVgyOXlaR1Z5WENJcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYjNKa1pYSmZkbUZzSUQwZ1hDSmNJanRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9LRzl5WkdWeVgzWmhiQ0U5WENKY0lpa21KaWdoSVc5eVpHVnlYM1poYkNrcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNWxlSFJ5WVY5eGRXVnllVjl3WVhKaGJYTXVZV3hzTG05eVpHVnlZbmtnUFNCdmNtUmxjbDkyWVd3N1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSkhkdmIxOXZjbVJsY21KNVgyWnZjbTB1YjI0b0ozTjFZbTFwZENjc0lHWjFibU4wYVc5dUtHVXBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pTNXdjbVYyWlc1MFJHVm1ZWFZzZENncE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZkbUZ5SUdadmNtMGdQU0JsTG5SaGNtZGxkRHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdabUZzYzJVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSkhkdmIxOXZjbVJsY21KNUxtOXVLRndpWTJoaGJtZGxYQ0lzSUdaMWJtTjBhVzl1S0dVcFhHNGdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWlM1d2NtVjJaVzUwUkdWbVlYVnNkQ2dwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSFpoYkNBOUlDUW9kR2hwY3lrdWRtRnNLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZb2RtRnNQVDFjSW0xbGJuVmZiM0prWlhKY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGJDQTlJRndpWENJN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1bGVIUnlZVjl4ZFdWeWVWOXdZWEpoYlhNdVlXeHNMbTl5WkdWeVlua2dQU0IyWVd3N1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtkR2hwY3k1MGNtbG5aMlZ5S0Z3aWMzVmliV2wwWENJcFhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z1ptRnNjMlU3WEc0Z0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dUlDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdkR2hwY3k1elkzSnZiR3hTWlhOMWJIUnpJRDBnWm5WdVkzUnBiMjRvS1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlnb2MyVnNaaTV6WTNKdmJHeGZiMjVmWVdOMGFXOXVQVDF6Wld4bUxtRnFZWGhmWVdOMGFXOXVLWHg4S0hObGJHWXVjMk55YjJ4c1gyOXVYMkZqZEdsdmJqMDlYQ0poYkd4Y0lpa3BYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTV6WTNKdmJHeFViMUJ2Y3lncE95QXZMM05qY205c2JDQjBhR1VnZDJsdVpHOTNJR2xtSUdsMElHaGhjeUJpWldWdUlITmxkRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YzJWc1ppNWhhbUY0WDJGamRHbHZiaUE5SUZ3aVhDSTdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0IwYUdsekxuVndaR0YwWlZWeWJFaHBjM1J2Y25rZ1BTQm1kVzVqZEdsdmJpaGhhbUY0WDNKbGMzVnNkSE5mZFhKc0tWeHVJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdkWE5sWDJocGMzUnZjbmxmWVhCcElEMGdNRHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2gzYVc1a2IzY3VhR2x6ZEc5eWVTQW1KaUIzYVc1a2IzY3VhR2x6ZEc5eWVTNXdkWE5vVTNSaGRHVXBYRzRnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RYTmxYMmhwYzNSdmNubGZZWEJwSUQwZ0pIUm9hWE11WVhSMGNpaGNJbVJoZEdFdGRYTmxMV2hwYzNSdmNua3RZWEJwWENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0JwWmlnb2MyVnNaaTUxY0dSaGRHVmZZV3BoZUY5MWNtdzlQVEVwSmlZb2RYTmxYMmhwYzNSdmNubGZZWEJwUFQweEtTbGNiaUFnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyNXZkeUJqYUdWamF5QnBaaUIwYUdVZ1luSnZkM05sY2lCemRYQndiM0owY3lCb2FYTjBiM0o1SUhOMFlYUmxJSEIxYzJnZ09pbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvZDJsdVpHOTNMbWhwYzNSdmNua2dKaVlnZDJsdVpHOTNMbWhwYzNSdmNua3VjSFZ6YUZOMFlYUmxLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FHbHpkRzl5ZVM1d2RYTm9VM1JoZEdVb2JuVnNiQ3dnYm5Wc2JDd2dZV3BoZUY5eVpYTjFiSFJ6WDNWeWJDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJSFJvYVhNdWNtVnRiM1psUVdwaGVGQmhaMmx1WVhScGIyNGdQU0JtZFc1amRHbHZiaWdwWEc0Z0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0hSNWNHVnZaaWh6Wld4bUxtRnFZWGhmYkdsdWEzTmZjMlZzWldOMGIzSXBJVDFjSW5WdVpHVm1hVzVsWkZ3aUtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQWtZV3BoZUY5c2FXNXJjMTl2WW1wbFkzUWdQU0JxVVhWbGNua29jMlZzWmk1aGFtRjRYMnhwYm10elgzTmxiR1ZqZEc5eUtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0NSaGFtRjRYMnhwYm10elgyOWlhbVZqZEM1c1pXNW5kR2crTUNsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1JoYW1GNFgyeHBibXR6WDI5aWFtVmpkQzV2Wm1Zb0tUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0IwYUdsekxtZGxkRUpoYzJWVmNtd2dQU0JtZFc1amRHbHZiaWdnZFhKc0lDa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OXViM2NnYzJWbElHbG1JSGRsSUdGeVpTQnZiaUIwYUdVZ1ZWSk1JSGRsSUhSb2FXNXJMaTR1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnZFhKc1gzQmhjblJ6SUQwZ2RYSnNMbk53YkdsMEtGd2lQMXdpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCMWNteGZZbUZ6WlNBOUlGd2lYQ0k3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0hWeWJGOXdZWEowY3k1c1pXNW5kR2crTUNsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IxY214ZlltRnpaU0E5SUhWeWJGOXdZWEowYzFzd1hUdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJVZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIVnliRjlpWVhObElEMGdkWEpzTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUhWeWJGOWlZWE5sTzF4dUlDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lIUm9hWE11WTJGdVJtVjBZMmhCYW1GNFVtVnpkV3gwY3lBOUlHWjFibU4wYVc5dUtHWmxkR05vWDNSNWNHVXBYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1LSFI1Y0dWdlppaG1aWFJqYUY5MGVYQmxLVDA5WENKMWJtUmxabWx1WldSY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdabVYwWTJoZmRIbHdaU0E5SUZ3aVhDSTdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhaaGNpQm1aWFJqYUY5aGFtRjRYM0psYzNWc2RITWdQU0JtWVd4elpUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9jMlZzWmk1cGMxOWhhbUY0UFQweEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnZXk4dmRHaGxiaUIzWlNCM2FXeHNJR0ZxWVhnZ2MzVmliV2wwSUhSb1pTQm1iM0p0WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyRnVaQ0JwWmlCM1pTQmpZVzRnWm1sdVpDQjBhR1VnY21WemRXeDBjeUJqYjI1MFlXbHVaWEpjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmloelpXeG1MaVJoYW1GNFgzSmxjM1ZzZEhOZlkyOXVkR0ZwYm1WeUxteGxibWQwYUQwOU1TbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdabGRHTm9YMkZxWVhoZmNtVnpkV3gwY3lBOUlIUnlkV1U3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSEpsYzNWc2RITmZkWEpzSUQwZ2MyVnNaaTV5WlhOMWJIUnpYM1Z5YkRzZ0lDOHZYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhKbGMzVnNkSE5mZFhKc1gyVnVZMjlrWldRZ1BTQW5KenNnSUM4dlhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR04xY25KbGJuUmZkWEpzSUQwZ2QybHVaRzkzTG14dlkyRjBhVzl1TG1oeVpXWTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDJsbmJtOXlaU0FqSUdGdVpDQmxkbVZ5ZVhSb2FXNW5JR0ZtZEdWeVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR2hoYzJoZmNHOXpJRDBnZDJsdVpHOTNMbXh2WTJGMGFXOXVMbWh5WldZdWFXNWtaWGhQWmlnbkl5Y3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0doaGMyaGZjRzl6SVQwOUxURXBlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCamRYSnlaVzUwWDNWeWJDQTlJSGRwYm1SdmR5NXNiMk5oZEdsdmJpNW9jbVZtTG5OMVluTjBjaWd3TENCM2FXNWtiM2N1Ykc5allYUnBiMjR1YUhKbFppNXBibVJsZUU5bUtDY2pKeWtwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LQ0FvSUNnZ2MyVnNaaTVrYVhOd2JHRjVYM0psYzNWc2RGOXRaWFJvYjJROVBWd2lZM1Z6ZEc5dFgzZHZiMk52YlcxbGNtTmxYM04wYjNKbFhDSWdLU0I4ZkNBb0lITmxiR1l1WkdsemNHeGhlVjl5WlhOMWJIUmZiV1YwYUc5a1BUMWNJbkJ2YzNSZmRIbHdaVjloY21Ob2FYWmxYQ0lnS1NBcElDWW1JQ2dnYzJWc1ppNWxibUZpYkdWZmRHRjRiMjV2YlhsZllYSmphR2wyWlhNZ1BUMGdNU0FwSUNsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0NCelpXeG1MbU4xY25KbGJuUmZkR0Y0YjI1dmJYbGZZWEpqYUdsMlpTQWhQVDFjSWx3aUlDbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWm1WMFkyaGZZV3BoZUY5eVpYTjFiSFJ6SUQwZ2RISjFaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSEpsZEhWeWJpQm1aWFJqYUY5aGFtRjRYM0psYzNWc2RITTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2S25aaGNpQnlaWE4xYkhSelgzVnliQ0E5SUhCeWIyTmxjM05mWm05eWJTNW5aWFJTWlhOMWJIUnpWWEpzS0hObGJHWXNJSE5sYkdZdWNtVnpkV3gwYzE5MWNtd3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR0ZqZEdsMlpWOTBZWGdnUFNCd2NtOWpaWE56WDJadmNtMHVaMlYwUVdOMGFYWmxWR0Y0S0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2NYVmxjbmxmY0dGeVlXMXpJRDBnYzJWc1ppNW5aWFJWY214UVlYSmhiWE1vZEhKMVpTd2dKeWNzSUdGamRHbDJaVjkwWVhncE95b3ZYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc1Y2JseHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTl1YjNjZ2MyVmxJR2xtSUhkbElHRnlaU0J2YmlCMGFHVWdWVkpNSUhkbElIUm9hVzVyTGk0dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSFZ5YkY5aVlYTmxJRDBnZEdocGN5NW5aWFJDWVhObFZYSnNLQ0JqZFhKeVpXNTBYM1Z5YkNBcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZkbUZ5SUhKbGMzVnNkSE5mZFhKc1gySmhjMlVnUFNCMGFHbHpMbWRsZEVKaGMyVlZjbXdvSUdOMWNuSmxiblJmZFhKc0lDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2JHRnVaeUE5SUhObGJHWXVaMlYwVVhWbGNubFFZWEpoYlVaeWIyMVZVa3dvWENKc1lXNW5YQ0lzSUhkcGJtUnZkeTVzYjJOaGRHbHZiaTVvY21WbUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWdvZEhsd1pXOW1LR3hoYm1jcElUMDlYQ0oxYm1SbFptbHVaV1JjSWlrbUppaHNZVzVuSVQwOWJuVnNiQ2twWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjFjbXhmWW1GelpTQTlJSE5sYkdZdVlXUmtWWEpzVUdGeVlXMG9kWEpzWDJKaGMyVXNJRndpYkdGdVp6MWNJaXRzWVc1bktUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2MyWnBaQ0E5SUhObGJHWXVaMlYwVVhWbGNubFFZWEpoYlVaeWIyMVZVa3dvWENKelptbGtYQ0lzSUhkcGJtUnZkeTVzYjJOaGRHbHZiaTVvY21WbUtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YVdZZ2MyWnBaQ0JwY3lCaElHNTFiV0psY2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtFNTFiV0psY2lod1lYSnpaVVpzYjJGMEtITm1hV1FwS1NBOVBTQnpabWxrS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkWEpzWDJKaGMyVWdQU0J6Wld4bUxtRmtaRlZ5YkZCaGNtRnRLSFZ5YkY5aVlYTmxMQ0JjSW5ObWFXUTlYQ0lyYzJacFpDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5cFppQmhibmtnYjJZZ2RHaGxJRE1nWTI5dVpHbDBhVzl1Y3lCaGNtVWdkSEoxWlN3Z2RHaGxiaUJwZEhNZ1oyOXZaQ0IwYnlCbmIxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZJQzBnTVNCOElHbG1JSFJvWlNCMWNtd2dZbUZ6WlNBOVBTQnlaWE4xYkhSelgzVnliRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2SUMwZ01pQjhJR2xtSUhWeWJDQmlZWE5sS3lCY0lpOWNJaUFnUFQwZ2NtVnpkV3gwYzE5MWNtd2dMU0JwYmlCallYTmxJRzltSUhWelpYSWdaWEp5YjNJZ2FXNGdkR2hsSUhKbGMzVnNkSE1nVlZKTVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OGdMU0F6SUh3Z2FXWWdkR2hsSUhKbGMzVnNkSE1nVlZKTUlHaGhjeUIxY213Z2NHRnlZVzF6TENCaGJtUWdkR2hsSUdOMWNuSmxiblFnZFhKc0lITjBZWEowY3lCM2FYUm9JSFJvWlNCeVpYTjFiSFJ6SUZWU1RDQmNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2ZEhKcGJTQmhibmtnZEhKaGFXeHBibWNnYzJ4aGMyZ2dabTl5SUdWaGMybGxjaUJqYjIxd1lYSnBjMjl1T2x4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhWeWJGOWlZWE5sSUQwZ2RYSnNYMkpoYzJVdWNtVndiR0ZqWlNndlhGd3ZKQzhzSUNjbktUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnlaWE4xYkhSelgzVnliQ0E5SUhKbGMzVnNkSE5mZFhKc0xuSmxjR3hoWTJVb0wxeGNMeVF2TENBbkp5azdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVZ6ZFd4MGMxOTFjbXhmWlc1amIyUmxaQ0E5SUdWdVkyOWtaVlZTU1NoeVpYTjFiSFJ6WDNWeWJDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ1kzVnljbVZ1ZEY5MWNteGZZMjl1ZEdGcGJuTmZjbVZ6ZFd4MGMxOTFjbXdnUFNBdE1UdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWdvZFhKc1gySmhjMlU5UFhKbGMzVnNkSE5mZFhKc0tYeDhLSFZ5YkY5aVlYTmxMblJ2VEc5M1pYSkRZWE5sS0NrOVBYSmxjM1ZzZEhOZmRYSnNYMlZ1WTI5a1pXUXVkRzlNYjNkbGNrTmhjMlVvS1NrZ0lDbDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdOMWNuSmxiblJmZFhKc1gyTnZiblJoYVc1elgzSmxjM1ZzZEhOZmRYSnNJRDBnTVR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUlHVnNjMlVnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvSUhKbGMzVnNkSE5mZFhKc0xtbHVaR1Y0VDJZb0lDYy9KeUFwSUNFOVBTQXRNU0FtSmlCamRYSnlaVzUwWDNWeWJDNXNZWE4wU1c1a1pYaFBaaWh5WlhOMWJIUnpYM1Z5YkN3Z01Da2dQVDA5SURBZ0tTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmpkWEp5Wlc1MFgzVnliRjlqYjI1MFlXbHVjMTl5WlhOMWJIUnpYM1Z5YkNBOUlERTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWh6Wld4bUxtOXViSGxmY21WemRXeDBjMTloYW1GNFBUMHhLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSHN2TDJsbUlHRWdkWE5sY2lCb1lYTWdZMmh2YzJWdUlIUnZJRzl1YkhrZ1lXeHNiM2NnWVdwaGVDQnZiaUJ5WlhOMWJIUnpJSEJoWjJWeklDaGtaV1poZFd4MElHSmxhR0YyYVc5MWNpbGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppZ2dZM1Z5Y21WdWRGOTFjbXhmWTI5dWRHRnBibk5mY21WemRXeDBjMTkxY213Z1BpQXRNU2xjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2V5OHZkR2hwY3lCdFpXRnVjeUIwYUdVZ1kzVnljbVZ1ZENCVlVrd2dZMjl1ZEdGcGJuTWdkR2hsSUhKbGMzVnNkSE1nZFhKc0xDQjNhR2xqYUNCdFpXRnVjeUIzWlNCallXNGdaRzhnWVdwaGVGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1ptVjBZMmhmWVdwaGVGOXlaWE4xYkhSeklEMGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGJITmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdabGRHTm9YMkZxWVhoZmNtVnpkV3gwY3lBOUlHWmhiSE5sTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJWY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0dabGRHTm9YM1I1Y0dVOVBWd2ljR0ZuYVc1aGRHbHZibHdpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWdnWTNWeWNtVnVkRjkxY214ZlkyOXVkR0ZwYm5OZmNtVnpkV3gwYzE5MWNtd2dQaUF0TVNsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhzdkwzUm9hWE1nYldWaGJuTWdkR2hsSUdOMWNuSmxiblFnVlZKTUlHTnZiblJoYVc1eklIUm9aU0J5WlhOMWJIUnpJSFZ5YkN3Z2QyaHBZMmdnYldWaGJuTWdkMlVnWTJGdUlHUnZJR0ZxWVhoY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXeHpaVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dlpHOXVKM1FnWVdwaGVDQndZV2RwYm1GMGFXOXVJSGRvWlc0Z2JtOTBJRzl1SUdFZ1V5WkdJSEJoWjJWY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQm1aWFJqYUY5aGFtRjRYM0psYzNWc2RITWdQU0JtWVd4elpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2NtVjBkWEp1SUdabGRHTm9YMkZxWVhoZmNtVnpkV3gwY3p0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJSFJvYVhNdWMyVjBkWEJCYW1GNFVHRm5hVzVoZEdsdmJpQTlJR1oxYm1OMGFXOXVLQ2xjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdMeTlwYm1acGJtbDBaU0J6WTNKdmJHeGNiaUFnSUNBZ0lDQWdJQ0FnSUdsbUtIUm9hWE11Y0dGbmFXNWhkR2x2Ymw5MGVYQmxQVDA5WENKcGJtWnBibWwwWlY5elkzSnZiR3hjSWlsY2JpQWdJQ0FnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2FXNW1hVzVwZEdWZmMyTnliMnhzWDJWdVpDQTlJR1poYkhObE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdUpHRnFZWGhmY21WemRXeDBjMTlqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJbHRrWVhSaExYTmxZWEpqYUMxbWFXeDBaWEl0WVdOMGFXOXVQU2RwYm1acGJtbDBaUzF6WTNKdmJHd3RaVzVrSjExY0lpa3ViR1Z1WjNSb1BqQXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcGJtWnBibWwwWlY5elkzSnZiR3hmWlc1a0lEMGdkSEoxWlR0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjMlZzWmk1cGMxOXRZWGhmY0dGblpXUWdQU0IwY25WbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0hCaGNuTmxTVzUwS0hSb2FYTXVhVzV6ZEdGdVkyVmZiblZ0WW1WeUtUMDlQVEVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKQ2gzYVc1a2IzY3BMbTltWmloY0luTmpjbTlzYkZ3aUxDQnpaV3htTG05dVYybHVaRzkzVTJOeWIyeHNLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvYzJWc1ppNWpZVzVHWlhSamFFRnFZWGhTWlhOMWJIUnpLRndpY0dGbmFXNWhkR2x2Ymx3aUtTa2dlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKQ2gzYVc1a2IzY3BMbTl1S0Z3aWMyTnliMnhzWENJc0lITmxiR1l1YjI1WGFXNWtiM2RUWTNKdmJHd3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlNCcFppaDBlWEJsYjJZb2MyVnNaaTVoYW1GNFgyeHBibXR6WDNObGJHVmpkRzl5S1QwOVhDSjFibVJsWm1sdVpXUmNJaWtnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhKbGRIVnlianRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUdWc2MyVWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1FvWkc5amRXMWxiblFwTG05bVppZ25ZMnhwWTJzbkxDQnpaV3htTG1GcVlYaGZiR2x1YTNOZmMyVnNaV04wYjNJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUW9aRzlqZFcxbGJuUXBMbTltWmloelpXeG1MbUZxWVhoZmJHbHVhM05mYzJWc1pXTjBiM0lwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNRb2MyVnNaaTVoYW1GNFgyeHBibXR6WDNObGJHVmpkRzl5S1M1dlptWW9LVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNRb1pHOWpkVzFsYm5RcExtOXVLQ2RqYkdsamF5Y3NJSE5sYkdZdVlXcGhlRjlzYVc1cmMxOXpaV3hsWTNSdmNpd2dablZ1WTNScGIyNG9aU2w3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvYzJWc1ppNWpZVzVHWlhSamFFRnFZWGhTWlhOMWJIUnpLRndpY0dGbmFXNWhkR2x2Ymx3aUtTbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnWlM1d2NtVjJaVzUwUkdWbVlYVnNkQ2dwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdiR2x1YXlBOUlHcFJkV1Z5ZVNoMGFHbHpLUzVoZEhSeUtDZG9jbVZtSnlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbUZxWVhoZllXTjBhVzl1SUQwZ1hDSndZV2RwYm1GMGFXOXVYQ0k3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQndZV2RsVG5WdFltVnlJRDBnYzJWc1ppNW5aWFJRWVdkbFpFWnliMjFWVWt3b2JHbHVheWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVKR0ZxWVhoZmNtVnpkV3gwYzE5amIyNTBZV2x1WlhJdVlYUjBjaWhjSW1SaGRHRXRjR0ZuWldSY0lpd2djR0ZuWlU1MWJXSmxjaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVabVYwWTJoQmFtRjRVbVZ6ZFd4MGN5Z3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdabUZzYzJVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlR0Y2JseHVJQ0FnSUNBZ0lDQjBhR2x6TG1kbGRGQmhaMlZrUm5KdmJWVlNUQ0E5SUdaMWJtTjBhVzl1S0ZWU1RDbDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ3WVdkbFpGWmhiQ0E5SURFN1hHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJacGNuTjBJSFJsYzNRZ2RHOGdjMlZsSUdsbUlIZGxJR2hoZG1VZ1hDSXZjR0ZuWlM4MEwxd2lJR2x1SUhSb1pTQlZVa3hjYmlBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUIwY0ZaaGJDQTlJSE5sYkdZdVoyVjBVWFZsY25sUVlYSmhiVVp5YjIxVlVrd29YQ0p6Wmw5d1lXZGxaRndpTENCVlVrd3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9LSFI1Y0dWdlppaDBjRlpoYkNrOVBWd2ljM1J5YVc1blhDSXBmSHdvZEhsd1pXOW1LSFJ3Vm1Gc0tUMDlYQ0p1ZFcxaVpYSmNJaWtwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjR0ZuWldSV1lXd2dQU0IwY0ZaaGJEdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJSEJoWjJWa1ZtRnNPMXh1SUNBZ0lDQWdJQ0I5TzF4dVhHNGdJQ0FnSUNBZ0lIUm9hWE11WjJWMFVYVmxjbmxRWVhKaGJVWnliMjFWVWt3Z1BTQm1kVzVqZEdsdmJpaHVZVzFsTENCVlVrd3BlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQjJZWElnY1hOMGNtbHVaeUE5SUZ3aVAxd2lLMVZTVEM1emNHeHBkQ2duUHljcFd6RmRPMXh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kSGx3Wlc5bUtIRnpkSEpwYm1jcElUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCMllXd2dQU0JrWldOdlpHVlZVa2xEYjIxd2IyNWxiblFvS0c1bGR5QlNaV2RGZUhBb0oxcy9mQ1pkSnlBcklHNWhiV1VnS3lBblBTY2dLeUFuS0Z0ZUpqdGRLejhwS0NaOEkzdzdmQ1FwSnlrdVpYaGxZeWh4YzNSeWFXNW5LWHg4V3l4Y0lsd2lYU2xiTVYwdWNtVndiR0ZqWlNndlhGd3JMMmNzSUNjbE1qQW5LU2w4Zkc1MWJHdzdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdUlIWmhiRHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJjSWx3aU8xeHVJQ0FnSUNBZ0lDQjlPMXh1WEc1Y2JseHVJQ0FnSUNBZ0lDQjBhR2x6TG1admNtMVZjR1JoZEdWa0lEMGdablZ1WTNScGIyNG9aU2w3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQzh2WlM1d2NtVjJaVzUwUkdWbVlYVnNkQ2dwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdhV1lvYzJWc1ppNWhkWFJ2WDNWd1pHRjBaVDA5TVNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YzNWaWJXbDBSbTl5YlNncE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlNCcFppZ29jMlZzWmk1aGRYUnZYM1Z3WkdGMFpUMDlNQ2ttSmloelpXeG1MbUYxZEc5ZlkyOTFiblJmY21WbWNtVnphRjl0YjJSbFBUMHhLU2xjYmlBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1admNtMVZjR1JoZEdWa1JtVjBZMmhCYW1GNEtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhKbGRIVnliaUJtWVd4elpUdGNiaUFnSUNBZ0lDQWdmVHRjYmx4dUlDQWdJQ0FnSUNCMGFHbHpMbVp2Y20xVmNHUmhkR1ZrUm1WMFkyaEJhbUY0SUQwZ1puVnVZM1JwYjI0b0tYdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0x5OXNiMjl3SUhSb2NtOTFaMmdnWVd4c0lIUm9aU0JtYVdWc1pITWdZVzVrSUdKMWFXeGtJSFJvWlNCVlVreGNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXVabVYwWTJoQmFtRjRSbTl5YlNncE8xeHVYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCbVlXeHpaVHRjYmlBZ0lDQWdJQ0FnZlR0Y2JseHVJQ0FnSUNBZ0lDQXZMMjFoYTJVZ1lXNTVJR052Y25KbFkzUnBiMjV6TDNWd1pHRjBaWE1nZEc4Z1ptbGxiR1J6SUdKbFptOXlaU0IwYUdVZ2MzVmliV2wwSUdOdmJYQnNaWFJsYzF4dUlDQWdJQ0FnSUNCMGFHbHpMbk5sZEVacFpXeGtjeUE5SUdaMWJtTjBhVzl1S0dVcGUxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBdkwybG1LSE5sYkdZdWFYTmZZV3BoZUQwOU1Da2dlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5emIyMWxkR2x0WlhNZ2RHaGxJR1p2Y20wZ2FYTWdjM1ZpYldsMGRHVmtJSGRwZEdodmRYUWdkR2hsSUhOc2FXUmxjaUI1WlhRZ2FHRjJhVzVuSUhWd1pHRjBaV1FzSUdGdVpDQmhjeUIzWlNCblpYUWdiM1Z5SUhaaGJIVmxjeUJtY205dFhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OTBhR1VnYzJ4cFpHVnlJR0Z1WkNCdWIzUWdhVzV3ZFhSekxDQjNaU0J1WldWa0lIUnZJR05vWldOcklHbDBJR2xtSUc1bFpXUnpJSFJ2SUdKbElITmxkRnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YjI1c2VTQnZZMk4xY25NZ2FXWWdZV3BoZUNCcGN5QnZabVlzSUdGdVpDQmhkWFJ2YzNWaWJXbDBJRzl1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNGtabWxsYkdSekxtVmhZMmdvWm5WdVkzUnBiMjRvS1NCN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUm1hV1ZzWkNBOUlDUW9kR2hwY3lrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlISmhibWRsWDJScGMzQnNZWGxmZG1Gc2RXVnpJRDBnSkdacFpXeGtMbVpwYm1Rb0p5NXpaaTF0WlhSaExYSmhibWRsTFhOc2FXUmxjaWNwTG1GMGRISW9YQ0prWVhSaExXUnBjM0JzWVhrdGRtRnNkV1Z6TFdGelhDSXBPeTh2WkdGMFlTMWthWE53YkdGNUxYWmhiSFZsY3kxaGN6MWNJblJsZUhSY0lseHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdsbUtISmhibWRsWDJScGMzQnNZWGxmZG1Gc2RXVnpQVDA5WENKMFpYaDBhVzV3ZFhSY0lpa2dlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlna1ptbGxiR1F1Wm1sdVpDaGNJaTV0WlhSaExYTnNhV1JsY2x3aUtTNXNaVzVuZEdnK01DbDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1JtYVdWc1pDNW1hVzVrS0Z3aUxtMWxkR0V0YzJ4cFpHVnlYQ0lwTG1WaFkyZ29ablZ1WTNScGIyNGdLR2x1WkdWNEtTQjdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdjMnhwWkdWeVgyOWlhbVZqZENBOUlDUW9kR2hwY3lsYk1GMDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUnpiR2xrWlhKZlpXd2dQU0FrS0hSb2FYTXBMbU5zYjNObGMzUW9YQ0l1YzJZdGJXVjBZUzF5WVc1blpTMXpiR2xrWlhKY0lpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5MllYSWdiV2x1Vm1Gc0lEMGdKSE5zYVdSbGNsOWxiQzVoZEhSeUtGd2laR0YwWVMxdGFXNWNJaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OTJZWElnYldGNFZtRnNJRDBnSkhOc2FXUmxjbDlsYkM1aGRIUnlLRndpWkdGMFlTMXRZWGhjSWlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUcxcGJsWmhiQ0E5SUNSemJHbGtaWEpmWld3dVptbHVaQ2hjSWk1elppMXlZVzVuWlMxdGFXNWNJaWt1ZG1Gc0tDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlHMWhlRlpoYkNBOUlDUnpiR2xrWlhKZlpXd3VabWx1WkNoY0lpNXpaaTF5WVc1blpTMXRZWGhjSWlrdWRtRnNLQ2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyeHBaR1Z5WDI5aWFtVmpkQzV1YjFWcFUyeHBaR1Z5TG5ObGRDaGJiV2x1Vm1Gc0xDQnRZWGhXWVd4ZEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDOHZmVnh1WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQXZMM04xWW0xcGRGeHVJQ0FnSUNBZ0lDQjBhR2x6TG5OMVltMXBkRVp2Y20wZ1BTQm1kVzVqZEdsdmJpaGxLWHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdMeTlzYjI5d0lIUm9jbTkxWjJnZ1lXeHNJSFJvWlNCbWFXVnNaSE1nWVc1a0lHSjFhV3hrSUhSb1pTQlZVa3hjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1LSE5sYkdZdWFYTlRkV0p0YVhSMGFXNW5JRDA5SUhSeWRXVXBJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0J5WlhSMWNtNGdabUZzYzJVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJSE5sYkdZdWMyVjBSbWxsYkdSektDazdYRzRnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbU5zWldGeVZHbHRaWElvS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnYzJWc1ppNXBjMU4xWW0xcGRIUnBibWNnUFNCMGNuVmxPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQndjbTlqWlhOelgyWnZjbTB1YzJWMFZHRjRRWEpqYUdsMlpWSmxjM1ZzZEhOVmNtd29jMlZzWml3Z2MyVnNaaTV5WlhOMWJIUnpYM1Z5YkNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXVKR0ZxWVhoZmNtVnpkV3gwYzE5amIyNTBZV2x1WlhJdVlYUjBjaWhjSW1SaGRHRXRjR0ZuWldSY0lpd2dNU2s3SUM4dmFXNXBkQ0J3WVdkbFpGeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaHpaV3htTG1OaGJrWmxkR05vUVdwaGVGSmxjM1ZzZEhNb0tTbGNiaUFnSUNBZ0lDQWdJQ0FnSUhzdkwzUm9aVzRnZDJVZ2QybHNiQ0JoYW1GNElITjFZbTFwZENCMGFHVWdabTl5YlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2MyVnNaaTVoYW1GNFgyRmpkR2x2YmlBOUlGd2ljM1ZpYldsMFhDSTdJQzh2YzI4Z2QyVWdhMjV2ZHlCcGRDQjNZWE51SjNRZ2NHRm5hVzVoZEdsdmJseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1Wm1WMFkyaEJhbUY0VW1WemRXeDBjeWdwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ1pXeHpaVnh1SUNBZ0lDQWdJQ0FnSUNBZ2V5OHZkR2hsYmlCM1pTQjNhV3hzSUhOcGJYQnNlU0J5WldScGNtVmpkQ0IwYnlCMGFHVWdVbVZ6ZFd4MGN5QlZVa3hjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQnlaWE4xYkhSelgzVnliQ0E5SUhCeWIyTmxjM05mWm05eWJTNW5aWFJTWlhOMWJIUnpWWEpzS0hObGJHWXNJSE5sYkdZdWNtVnpkV3gwYzE5MWNtd3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCeGRXVnllVjl3WVhKaGJYTWdQU0J6Wld4bUxtZGxkRlZ5YkZCaGNtRnRjeWgwY25WbExDQW5KeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WemRXeDBjMTkxY213Z1BTQnpaV3htTG1Ga1pGVnliRkJoY21GdEtISmxjM1ZzZEhOZmRYSnNMQ0J4ZFdWeWVWOXdZWEpoYlhNcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkMmx1Wkc5M0xteHZZMkYwYVc5dUxtaHlaV1lnUFNCeVpYTjFiSFJ6WDNWeWJEdGNiaUFnSUNBZ0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJR1poYkhObE8xeHVJQ0FnSUNBZ0lDQjlPMXh1SUNBZ0lDQWdJQ0IwYUdsekxuSmxjMlYwUm05eWJTQTlJR1oxYm1OMGFXOXVLSE4xWW0xcGRGOW1iM0p0S1Z4dUlDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDNWdWMyVjBJR0ZzYkNCbWFXVnNaSE5jYmlBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1SkdacFpXeGtjeTVsWVdOb0tHWjFibU4wYVc5dUtDbDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pHWnBaV3hrSUQwZ0pDaDBhR2x6S1R0Y2JseDBYSFJjZEZ4MFhHNWNkRngwWEhSY2RDUm1hV1ZzWkM1eVpXMXZkbVZCZEhSeUtGd2laR0YwWVMxelppMTBZWGh2Ym05dGVTMWhjbU5vYVhabFhDSXBPMXh1WEhSY2RGeDBYSFJjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDNOMFlXNWtZWEprSUdacFpXeGtJSFI1Y0dWelhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pHWnBaV3hrTG1acGJtUW9YQ0p6Wld4bFkzUTZibTkwS0Z0dGRXeDBhWEJzWlQwbmJYVnNkR2x3YkdVblhTa2dQaUJ2Y0hScGIyNDZabWx5YzNRdFkyaHBiR1JjSWlrdWNISnZjQ2hjSW5ObGJHVmpkR1ZrWENJc0lIUnlkV1VwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSbWFXVnNaQzVtYVc1a0tGd2ljMlZzWldOMFcyMTFiSFJwY0d4bFBTZHRkV3gwYVhCc1pTZGRJRDRnYjNCMGFXOXVYQ0lwTG5CeWIzQW9YQ0p6Wld4bFkzUmxaRndpTENCbVlXeHpaU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkdacFpXeGtMbVpwYm1Rb1hDSnBibkIxZEZ0MGVYQmxQU2RqYUdWamEySnZlQ2RkWENJcExuQnliM0FvWENKamFHVmphMlZrWENJc0lHWmhiSE5sS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa1ptbGxiR1F1Wm1sdVpDaGNJajRnZFd3Z1BpQnNhVHBtYVhKemRDMWphR2xzWkNCcGJuQjFkRnQwZVhCbFBTZHlZV1JwYnlkZFhDSXBMbkJ5YjNBb1hDSmphR1ZqYTJWa1hDSXNJSFJ5ZFdVcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUm1hV1ZzWkM1bWFXNWtLRndpYVc1d2RYUmJkSGx3WlQwbmRHVjRkQ2RkWENJcExuWmhiQ2hjSWx3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWtabWxsYkdRdVptbHVaQ2hjSWk1elppMXZjSFJwYjI0dFlXTjBhWFpsWENJcExuSmxiVzkyWlVOc1lYTnpLRndpYzJZdGIzQjBhVzl1TFdGamRHbDJaVndpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa1ptbGxiR1F1Wm1sdVpDaGNJajRnZFd3Z1BpQnNhVHBtYVhKemRDMWphR2xzWkNCcGJuQjFkRnQwZVhCbFBTZHlZV1JwYnlkZFhDSXBMbkJoY21WdWRDZ3BMbUZrWkVOc1lYTnpLRndpYzJZdGIzQjBhVzl1TFdGamRHbDJaVndpS1RzZ0x5OXlaU0JoWkdRZ1lXTjBhWFpsSUdOc1lYTnpJSFJ2SUdacGNuTjBJRndpWkdWbVlYVnNkRndpSUc5d2RHbHZibHh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5dWRXMWlaWElnY21GdVoyVWdMU0F5SUc1MWJXSmxjaUJwYm5CMWRDQm1hV1ZzWkhOY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa1ptbGxiR1F1Wm1sdVpDaGNJbWx1Y0hWMFczUjVjR1U5SjI1MWJXSmxjaWRkWENJcExtVmhZMmdvWm5WdVkzUnBiMjRvYVc1a1pYZ3BlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUFrZEdocGMwbHVjSFYwSUQwZ0pDaDBhR2x6S1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlna2RHaHBjMGx1Y0hWMExuQmhjbVZ1ZENncExuQmhjbVZ1ZENncExtaGhjME5zWVhOektGd2ljMll0YldWMFlTMXlZVzVuWlZ3aUtTa2dlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlocGJtUmxlRDA5TUNrZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1IwYUdselNXNXdkWFF1ZG1Gc0tDUjBhR2x6U1c1d2RYUXVZWFIwY2loY0ltMXBibHdpS1NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGJITmxJR2xtS0dsdVpHVjRQVDB4S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKSFJvYVhOSmJuQjFkQzUyWVd3b0pIUm9hWE5KYm5CMWRDNWhkSFJ5S0Z3aWJXRjRYQ0lwS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMMjFsZEdFZ0x5QnVkVzFpWlhKeklIZHBkR2dnTWlCcGJuQjFkSE1nS0daeWIyMGdMeUIwYnlCbWFXVnNaSE1wSUMwZ2MyVmpiMjVrSUdsdWNIVjBJRzExYzNRZ1ltVWdjbVZ6WlhRZ2RHOGdiV0Y0SUhaaGJIVmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUNSdFpYUmhYM05sYkdWamRGOW1jbTl0WDNSdklEMGdKR1pwWld4a0xtWnBibVFvWENJdWMyWXRiV1YwWVMxeVlXNW5aUzF6Wld4bFkzUXRabkp2YlhSdlhDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZb0pHMWxkR0ZmYzJWc1pXTjBYMlp5YjIxZmRHOHViR1Z1WjNSb1BqQXBJSHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnYzNSaGNuUmZiV2x1SUQwZ0pHMWxkR0ZmYzJWc1pXTjBYMlp5YjIxZmRHOHVZWFIwY2loY0ltUmhkR0V0YldsdVhDSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdjM1JoY25SZmJXRjRJRDBnSkcxbGRHRmZjMlZzWldOMFgyWnliMjFmZEc4dVlYUjBjaWhjSW1SaGRHRXRiV0Y0WENJcE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSdFpYUmhYM05sYkdWamRGOW1jbTl0WDNSdkxtWnBibVFvWENKelpXeGxZM1JjSWlrdVpXRmphQ2htZFc1amRHbHZiaWhwYm1SbGVDbDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lBa2RHaHBjMGx1Y0hWMElEMGdKQ2gwYUdsektUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvYVc1a1pYZzlQVEFwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1IwYUdselNXNXdkWFF1ZG1Gc0tITjBZWEowWDIxcGJpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxiSE5sSUdsbUtHbHVaR1Y0UFQweEtTQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkhSb2FYTkpibkIxZEM1MllXd29jM1JoY25SZmJXRjRLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCMllYSWdKRzFsZEdGZmNtRmthVzlmWm5KdmJWOTBieUE5SUNSbWFXVnNaQzVtYVc1a0tGd2lMbk5tTFcxbGRHRXRjbUZ1WjJVdGNtRmthVzh0Wm5KdmJYUnZYQ0lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2FXWW9KRzFsZEdGZmNtRmthVzlmWm5KdmJWOTBieTVzWlc1bmRHZytNQ2xjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJ6ZEdGeWRGOXRhVzRnUFNBa2JXVjBZVjl5WVdScGIxOW1jbTl0WDNSdkxtRjBkSElvWENKa1lYUmhMVzFwYmx3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlITjBZWEowWDIxaGVDQTlJQ1J0WlhSaFgzSmhaR2x2WDJaeWIyMWZkRzh1WVhSMGNpaGNJbVJoZEdFdGJXRjRYQ0lwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lBa2NtRmthVzlmWjNKdmRYQnpJRDBnSkcxbGRHRmZjbUZrYVc5ZlpuSnZiVjkwYnk1bWFXNWtLQ2N1YzJZdGFXNXdkWFF0Y21GdVoyVXRjbUZrYVc4bktUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa2NtRmthVzlmWjNKdmRYQnpMbVZoWTJnb1puVnVZM1JwYjI0b2FXNWtaWGdwZTF4dVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUFrY21Ga2FXOXpJRDBnSkNoMGFHbHpLUzVtYVc1a0tGd2lMbk5tTFdsdWNIVjBMWEpoWkdsdlhDSXBPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKSEpoWkdsdmN5NXdjbTl3S0Z3aVkyaGxZMnRsWkZ3aUxDQm1ZV3h6WlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1LR2x1WkdWNFBUMHdLVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSeVlXUnBiM011Wm1sc2RHVnlLQ2RiZG1Gc2RXVTlYQ0luSzNOMFlYSjBYMjFwYmlzblhDSmRKeWt1Y0hKdmNDaGNJbU5vWldOclpXUmNJaXdnZEhKMVpTazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQmxiSE5sSUdsbUtHbHVaR1Y0UFQweEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1J5WVdScGIzTXVabWxzZEdWeUtDZGJkbUZzZFdVOVhDSW5LM04wWVhKMFgyMWhlQ3NuWENKZEp5a3VjSEp2Y0NoY0ltTm9aV05yWldSY0lpd2dkSEoxWlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDI1MWJXSmxjaUJ6Ykdsa1pYSWdMU0J1YjFWcFUyeHBaR1Z5WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkdacFpXeGtMbVpwYm1Rb1hDSXViV1YwWVMxemJHbGtaWEpjSWlrdVpXRmphQ2htZFc1amRHbHZiaWhwYm1SbGVDbDdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSE5zYVdSbGNsOXZZbXBsWTNRZ1BTQWtLSFJvYVhNcFd6QmRPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdktuWmhjaUJ6Ykdsa1pYSmZiMkpxWldOMElEMGdKR052Ym5SaGFXNWxjaTVtYVc1a0tGd2lMbTFsZEdFdGMyeHBaR1Z5WENJcFd6QmRPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSE5zYVdSbGNsOTJZV3dnUFNCemJHbGtaWEpmYjJKcVpXTjBMbTV2VldsVGJHbGtaWEl1WjJWMEtDazdLaTljYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnSkhOc2FXUmxjbDlsYkNBOUlDUW9kR2hwY3lrdVkyeHZjMlZ6ZENoY0lpNXpaaTF0WlhSaExYSmhibWRsTFhOc2FXUmxjbHdpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUcxcGJsWmhiQ0E5SUNSemJHbGtaWEpmWld3dVlYUjBjaWhjSW1SaGRHRXRiV2x1WENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2JXRjRWbUZzSUQwZ0pITnNhV1JsY2w5bGJDNWhkSFJ5S0Z3aVpHRjBZUzF0WVhoY0lpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhOc2FXUmxjbDl2WW1wbFkzUXVibTlWYVZOc2FXUmxjaTV6WlhRb1cyMXBibFpoYkN3Z2JXRjRWbUZzWFNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlLVHRjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dmJtVmxaQ0IwYnlCelpXVWdhV1lnWVc1NUlHRnlaU0JqYjIxaWIySnZlQ0JoYm1RZ1lXTjBJR0ZqWTI5eVpHbHVaMng1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlDUmpiMjFpYjJKdmVDQTlJQ1JtYVdWc1pDNW1hVzVrS0Z3aWMyVnNaV04wVzJSaGRHRXRZMjl0WW05aWIzZzlKekVuWFZ3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaWdrWTI5dFltOWliM2d1YkdWdVozUm9QakFwWEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnBaaUFvZEhsd1pXOW1JQ1JqYjIxaWIySnZlQzVqYUc5elpXNGdJVDBnWENKMWJtUmxabWx1WldSY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkdOdmJXSnZZbTk0TG5SeWFXZG5aWElvWENKamFHOXpaVzQ2ZFhCa1lYUmxaRndpS1RzZ0x5OW1iM0lnWTJodmMyVnVJRzl1YkhsY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGJITmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSamIyMWliMkp2ZUM1MllXd29KeWNwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSkdOdmJXSnZZbTk0TG5SeWFXZG5aWElvSjJOb1lXNW5aUzV6Wld4bFkzUXlKeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzVjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdmU2s3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1Oc1pXRnlWR2x0WlhJb0tUdGNibHh1WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtS0hOMVltMXBkRjltYjNKdFBUMWNJbUZzZDJGNWMxd2lLVnh1SUNBZ0lDQWdJQ0FnSUNBZ2UxeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1YzNWaWJXbDBSbTl5YlNncE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlNCcFppaHpkV0p0YVhSZlptOXliVDA5WENKdVpYWmxjbHdpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtS0hSb2FYTXVZWFYwYjE5amIzVnVkRjl5WldaeVpYTm9YMjF2WkdVOVBURXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbVp2Y20xVmNHUmhkR1ZrUm1WMFkyaEJhbUY0S0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dUlDQWdJQ0FnSUNBZ0lDQWdaV3h6WlNCcFppaHpkV0p0YVhSZlptOXliVDA5WENKaGRYUnZYQ0lwWEc0Z0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvZEdocGN5NWhkWFJ2WDNWd1pHRjBaVDA5ZEhKMVpTbGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhObGJHWXVjM1ZpYldsMFJtOXliU2dwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbGJITmxYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppaDBhR2x6TG1GMWRHOWZZMjkxYm5SZmNtVm1jbVZ6YUY5dGIyUmxQVDB4S1Z4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQnpaV3htTG1admNtMVZjR1JoZEdWa1JtVjBZMmhCYW1GNEtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5WEc1Y2JpQWdJQ0FnSUNBZ2ZUdGNibHh1SUNBZ0lDQWdJQ0IwYUdsekxtbHVhWFFvS1R0Y2JseHVJQ0FnSUNBZ0lDQjJZWElnWlhabGJuUmZaR0YwWVNBOUlIdDlPMXh1SUNBZ0lDQWdJQ0JsZG1WdWRGOWtZWFJoTG5ObWFXUWdQU0J6Wld4bUxuTm1hV1E3WEc0Z0lDQWdJQ0FnSUdWMlpXNTBYMlJoZEdFdWRHRnlaMlYwVTJWc1pXTjBiM0lnUFNCelpXeG1MbUZxWVhoZmRHRnlaMlYwWDJGMGRISTdYRzRnSUNBZ0lDQWdJR1YyWlc1MFgyUmhkR0V1YjJKcVpXTjBJRDBnZEdocGN6dGNiaUFnSUNBZ0lDQWdhV1lvYjNCMGN5NXBjMGx1YVhRcFhHNGdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUhObGJHWXVkSEpwWjJkbGNrVjJaVzUwS0Z3aWMyWTZhVzVwZEZ3aUxDQmxkbVZ1ZEY5a1lYUmhLVHRjYmlBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnZlNrN1hHNTlPMXh1SWwxOSIsIihmdW5jdGlvbiAoZ2xvYmFsKXtcblxudmFyICQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHtcblxuXHR0YXhvbm9teV9hcmNoaXZlczogMCxcbiAgICB1cmxfcGFyYW1zOiB7fSxcbiAgICB0YXhfYXJjaGl2ZV9yZXN1bHRzX3VybDogXCJcIixcbiAgICBhY3RpdmVfdGF4OiBcIlwiLFxuICAgIGZpZWxkczoge30sXG5cdGluaXQ6IGZ1bmN0aW9uKHRheG9ub215X2FyY2hpdmVzLCBjdXJyZW50X3RheG9ub215X2FyY2hpdmUpe1xuXG4gICAgICAgIHRoaXMudGF4b25vbXlfYXJjaGl2ZXMgPSAwO1xuICAgICAgICB0aGlzLnVybF9wYXJhbXMgPSB7fTtcbiAgICAgICAgdGhpcy50YXhfYXJjaGl2ZV9yZXN1bHRzX3VybCA9IFwiXCI7XG4gICAgICAgIHRoaXMuYWN0aXZlX3RheCA9IFwiXCI7XG5cblx0XHQvL3RoaXMuJGZpZWxkcyA9ICRmaWVsZHM7XG4gICAgICAgIHRoaXMudGF4b25vbXlfYXJjaGl2ZXMgPSB0YXhvbm9teV9hcmNoaXZlcztcbiAgICAgICAgdGhpcy5jdXJyZW50X3RheG9ub215X2FyY2hpdmUgPSBjdXJyZW50X3RheG9ub215X2FyY2hpdmU7XG5cblx0XHR0aGlzLmNsZWFyVXJsQ29tcG9uZW50cygpO1xuXG5cdH0sXG4gICAgc2V0VGF4QXJjaGl2ZVJlc3VsdHNVcmw6IGZ1bmN0aW9uKCRmb3JtLCBjdXJyZW50X3Jlc3VsdHNfdXJsLCBnZXRfYWN0aXZlKSB7XG5cbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuXHRcdHRoaXMuY2xlYXJUYXhBcmNoaXZlUmVzdWx0c1VybCgpO1xuICAgICAgICAvL3ZhciBjdXJyZW50X3Jlc3VsdHNfdXJsID0gXCJcIjtcbiAgICAgICAgaWYodGhpcy50YXhvbm9teV9hcmNoaXZlcyE9MSlcbiAgICAgICAge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYodHlwZW9mKGdldF9hY3RpdmUpPT1cInVuZGVmaW5lZFwiKVxuXHRcdHtcblx0XHRcdHZhciBnZXRfYWN0aXZlID0gZmFsc2U7XG5cdFx0fVxuXG4gICAgICAgIC8vY2hlY2sgdG8gc2VlIGlmIHdlIGhhdmUgYW55IHRheG9ub21pZXMgc2VsZWN0ZWRcbiAgICAgICAgLy9pZiBzbywgY2hlY2sgdGhlaXIgcmV3cml0ZXMgYW5kIHVzZSB0aG9zZSBhcyB0aGUgcmVzdWx0cyB1cmxcbiAgICAgICAgdmFyICRmaWVsZCA9IGZhbHNlO1xuICAgICAgICB2YXIgZmllbGRfbmFtZSA9IFwiXCI7XG4gICAgICAgIHZhciBmaWVsZF92YWx1ZSA9IFwiXCI7XG5cbiAgICAgICAgdmFyICRhY3RpdmVfdGF4b25vbXkgPSAkZm9ybS4kZmllbGRzLnBhcmVudCgpLmZpbmQoXCJbZGF0YS1zZi10YXhvbm9teS1hcmNoaXZlPScxJ11cIik7XG4gICAgICAgIGlmKCRhY3RpdmVfdGF4b25vbXkubGVuZ3RoPT0xKVxuICAgICAgICB7XG4gICAgICAgICAgICAkZmllbGQgPSAkYWN0aXZlX3RheG9ub215O1xuXG4gICAgICAgICAgICB2YXIgZmllbGRUeXBlID0gJGZpZWxkLmF0dHIoXCJkYXRhLXNmLWZpZWxkLXR5cGVcIik7XG5cbiAgICAgICAgICAgIGlmICgoZmllbGRUeXBlID09IFwidGFnXCIpIHx8IChmaWVsZFR5cGUgPT0gXCJjYXRlZ29yeVwiKSB8fCAoZmllbGRUeXBlID09IFwidGF4b25vbXlcIikpIHtcbiAgICAgICAgICAgICAgICB2YXIgdGF4b25vbXlfdmFsdWUgPSBzZWxmLnByb2Nlc3NUYXhvbm9teSgkZmllbGQsIHRydWUpO1xuICAgICAgICAgICAgICAgIGZpZWxkX25hbWUgPSAkZmllbGQuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcbiAgICAgICAgICAgICAgICB2YXIgdGF4b25vbXlfbmFtZSA9IGZpZWxkX25hbWUucmVwbGFjZShcIl9zZnRfXCIsIFwiXCIpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRheG9ub215X3ZhbHVlKSB7XG4gICAgICAgICAgICAgICAgICAgIGZpZWxkX3ZhbHVlID0gdGF4b25vbXlfdmFsdWUudmFsdWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZihmaWVsZF92YWx1ZT09XCJcIilcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAkZmllbGQgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmKChzZWxmLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSE9XCJcIikmJihzZWxmLmN1cnJlbnRfdGF4b25vbXlfYXJjaGl2ZSE9dGF4b25vbXlfbmFtZSkpXG4gICAgICAgIHtcblxuICAgICAgICAgICAgdGhpcy50YXhfYXJjaGl2ZV9yZXN1bHRzX3VybCA9IGN1cnJlbnRfcmVzdWx0c191cmw7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZigoKGZpZWxkX3ZhbHVlPT1cIlwiKXx8KCEkZmllbGQpICkpXG4gICAgICAgIHtcbiAgICAgICAgICAgICRmb3JtLiRmaWVsZHMuZWFjaChmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAoISRmaWVsZCkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBmaWVsZFR5cGUgPSAkKHRoaXMpLmF0dHIoXCJkYXRhLXNmLWZpZWxkLXR5cGVcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKChmaWVsZFR5cGUgPT0gXCJ0YWdcIikgfHwgKGZpZWxkVHlwZSA9PSBcImNhdGVnb3J5XCIpIHx8IChmaWVsZFR5cGUgPT0gXCJ0YXhvbm9teVwiKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHRheG9ub215X3ZhbHVlID0gc2VsZi5wcm9jZXNzVGF4b25vbXkoJCh0aGlzKSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBmaWVsZF9uYW1lID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC1uYW1lXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGF4b25vbXlfdmFsdWUpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZpZWxkX3ZhbHVlID0gdGF4b25vbXlfdmFsdWUudmFsdWU7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoZmllbGRfdmFsdWUgIT0gXCJcIikge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRmaWVsZCA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmKCAoJGZpZWxkKSAmJiAoZmllbGRfdmFsdWUgIT0gXCJcIiApKSB7XG4gICAgICAgICAgICAvL2lmIHdlIGZvdW5kIGEgZmllbGRcblx0XHRcdHZhciByZXdyaXRlX2F0dHIgPSAoJGZpZWxkLmF0dHIoXCJkYXRhLXNmLXRlcm0tcmV3cml0ZVwiKSk7XG5cbiAgICAgICAgICAgIGlmKHJld3JpdGVfYXR0ciE9XCJcIikge1xuXG4gICAgICAgICAgICAgICAgdmFyIHJld3JpdGUgPSBKU09OLnBhcnNlKHJld3JpdGVfYXR0cik7XG4gICAgICAgICAgICAgICAgdmFyIGlucHV0X3R5cGUgPSAkZmllbGQuYXR0cihcImRhdGEtc2YtZmllbGQtaW5wdXQtdHlwZVwiKTtcbiAgICAgICAgICAgICAgICBzZWxmLmFjdGl2ZV90YXggPSBmaWVsZF9uYW1lO1xuXG4gICAgICAgICAgICAgICAgLy9maW5kIHRoZSBhY3RpdmUgZWxlbWVudFxuICAgICAgICAgICAgICAgIGlmICgoaW5wdXRfdHlwZSA9PSBcInJhZGlvXCIpIHx8IChpbnB1dF90eXBlID09IFwiY2hlY2tib3hcIikpIHtcblxuICAgICAgICAgICAgICAgICAgICAvL3ZhciAkYWN0aXZlID0gJGZpZWxkLmZpbmQoXCIuc2Ytb3B0aW9uLWFjdGl2ZVwiKTtcbiAgICAgICAgICAgICAgICAgICAgLy9leHBsb2RlIHRoZSB2YWx1ZXMgaWYgdGhlcmUgaXMgYSBkZWxpbVxuICAgICAgICAgICAgICAgICAgICAvL2ZpZWxkX3ZhbHVlXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGlzX3NpbmdsZV92YWx1ZSA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIHZhciBmaWVsZF92YWx1ZXMgPSBmaWVsZF92YWx1ZS5zcGxpdChcIixcIikuam9pbihcIitcIikuc3BsaXQoXCIrXCIpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoZmllbGRfdmFsdWVzLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlzX3NpbmdsZV92YWx1ZSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGlzX3NpbmdsZV92YWx1ZSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGlucHV0ID0gJGZpZWxkLmZpbmQoXCJpbnB1dFt2YWx1ZT0nXCIgKyBmaWVsZF92YWx1ZSArIFwiJ11cIik7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGFjdGl2ZSA9ICRpbnB1dC5wYXJlbnQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBkZXB0aCA9ICRhY3RpdmUuYXR0cihcImRhdGEtc2YtZGVwdGhcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vbm93IGxvb3AgdGhyb3VnaCBwYXJlbnRzIHRvIGdyYWIgdGhlaXIgbmFtZXNcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB2YWx1ZXMgPSBuZXcgQXJyYXkoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlcy5wdXNoKGZpZWxkX3ZhbHVlKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IGRlcHRoOyBpID4gMDsgaS0tKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGFjdGl2ZSA9ICRhY3RpdmUucGFyZW50KCkucGFyZW50KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWVzLnB1c2goJGFjdGl2ZS5maW5kKFwiaW5wdXRcIikudmFsKCkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZXMucmV2ZXJzZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2dyYWIgdGhlIHJld3JpdGUgZm9yIHRoaXMgZGVwdGhcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBhY3RpdmVfcmV3cml0ZSA9IHJld3JpdGVbZGVwdGhdO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHVybCA9IGFjdGl2ZV9yZXdyaXRlO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vdGhlbiBtYXAgZnJvbSB0aGUgcGFyZW50cyB0byB0aGUgZGVwdGhcbiAgICAgICAgICAgICAgICAgICAgICAgICQodmFsdWVzKS5lYWNoKGZ1bmN0aW9uIChpbmRleCwgdmFsdWUpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybCA9IHVybC5yZXBsYWNlKFwiW1wiICsgaW5kZXggKyBcIl1cIiwgdmFsdWUpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudGF4X2FyY2hpdmVfcmVzdWx0c191cmwgPSB1cmw7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vaWYgdGhlcmUgYXJlIG11bHRpcGxlIHZhbHVlcyxcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vdGhlbiB3ZSBuZWVkIHRvIGNoZWNrIGZvciAzIHRoaW5nczpcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pZiB0aGUgdmFsdWVzIHNlbGVjdGVkIGFyZSBhbGwgaW4gdGhlIHNhbWUgdHJlZSB0aGVuIHdlIGNhbiBkbyBzb21lIGNsZXZlciByZXdyaXRlIHN0dWZmXG4gICAgICAgICAgICAgICAgICAgICAgICAvL21lcmdlIGFsbCB2YWx1ZXMgaW4gc2FtZSBsZXZlbCwgdGhlbiBjb21iaW5lIHRoZSBsZXZlbHNcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pZiB0aGV5IGFyZSBmcm9tIGRpZmZlcmVudCB0cmVlcyB0aGVuIGp1c3QgY29tYmluZSB0aGVtIG9yIGp1c3QgdXNlIGBmaWVsZF92YWx1ZWBcbiAgICAgICAgICAgICAgICAgICAgICAgIC8qXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgZGVwdGhzID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgJChmaWVsZF92YWx1ZXMpLmVhY2goZnVuY3Rpb24gKGluZGV4LCB2YWwpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkaW5wdXQgPSAkZmllbGQuZmluZChcImlucHV0W3ZhbHVlPSdcIiArIGZpZWxkX3ZhbHVlICsgXCInXVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGFjdGl2ZSA9ICRpbnB1dC5wYXJlbnQoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBkZXB0aCA9ICRhY3RpdmUuYXR0cihcImRhdGEtc2YtZGVwdGhcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgLy9kZXB0aHMucHVzaChkZXB0aCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICB9KTsqL1xuXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZiAoKGlucHV0X3R5cGUgPT0gXCJzZWxlY3RcIikgfHwgKGlucHV0X3R5cGUgPT0gXCJtdWx0aXNlbGVjdFwiKSkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBpc19zaW5nbGVfdmFsdWUgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB2YXIgZmllbGRfdmFsdWVzID0gZmllbGRfdmFsdWUuc3BsaXQoXCIsXCIpLmpvaW4oXCIrXCIpLnNwbGl0KFwiK1wiKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGZpZWxkX3ZhbHVlcy5sZW5ndGggPiAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpc19zaW5nbGVfdmFsdWUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmIChpc19zaW5nbGVfdmFsdWUpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRhY3RpdmUgPSAkZmllbGQuZmluZChcIm9wdGlvblt2YWx1ZT0nXCIgKyBmaWVsZF92YWx1ZSArIFwiJ11cIik7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgZGVwdGggPSAkYWN0aXZlLmF0dHIoXCJkYXRhLXNmLWRlcHRoXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdmFsdWVzID0gbmV3IEFycmF5KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZXMucHVzaChmaWVsZF92YWx1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSBkZXB0aDsgaSA+IDA7IGktLSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRhY3RpdmUgPSAkYWN0aXZlLnByZXZBbGwoXCJvcHRpb25bZGF0YS1zZi1kZXB0aD0nXCIgKyAoaSAtIDEpICsgXCInXVwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZXMucHVzaCgkYWN0aXZlLnZhbCgpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWVzLnJldmVyc2UoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBhY3RpdmVfcmV3cml0ZSA9IHJld3JpdGVbZGVwdGhdO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHVybCA9IGFjdGl2ZV9yZXdyaXRlO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh2YWx1ZXMpLmVhY2goZnVuY3Rpb24gKGluZGV4LCB2YWx1ZSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsID0gdXJsLnJlcGxhY2UoXCJbXCIgKyBpbmRleCArIFwiXVwiLCB2YWx1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy50YXhfYXJjaGl2ZV9yZXN1bHRzX3VybCA9IHVybDtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cbiAgICAgICAgLy90aGlzLnRheF9hcmNoaXZlX3Jlc3VsdHNfdXJsID0gY3VycmVudF9yZXN1bHRzX3VybDtcbiAgICB9LFxuICAgIGdldFJlc3VsdHNVcmw6IGZ1bmN0aW9uKCRmb3JtLCBjdXJyZW50X3Jlc3VsdHNfdXJsKSB7XG5cbiAgICAgICAgLy90aGlzLnNldFRheEFyY2hpdmVSZXN1bHRzVXJsKCRmb3JtLCBjdXJyZW50X3Jlc3VsdHNfdXJsKTtcblxuICAgICAgICBpZih0aGlzLnRheF9hcmNoaXZlX3Jlc3VsdHNfdXJsPT1cIlwiKVxuICAgICAgICB7XG4gICAgICAgICAgICByZXR1cm4gY3VycmVudF9yZXN1bHRzX3VybDtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLnRheF9hcmNoaXZlX3Jlc3VsdHNfdXJsO1xuICAgIH0sXG5cdGdldFVybFBhcmFtczogZnVuY3Rpb24oJGZvcm0pe1xuXG5cdFx0dGhpcy5idWlsZFVybENvbXBvbmVudHMoJGZvcm0sIHRydWUpO1xuXG4gICAgICAgIGlmKHRoaXMudGF4X2FyY2hpdmVfcmVzdWx0c191cmwhPVwiXCIpXG4gICAgICAgIHtcblxuICAgICAgICAgICAgaWYodGhpcy5hY3RpdmVfdGF4IT1cIlwiKVxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHZhciBmaWVsZF9uYW1lID0gdGhpcy5hY3RpdmVfdGF4O1xuXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mKHRoaXMudXJsX3BhcmFtc1tmaWVsZF9uYW1lXSkhPVwidW5kZWZpbmVkXCIpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBkZWxldGUgdGhpcy51cmxfcGFyYW1zW2ZpZWxkX25hbWVdO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG5cdFx0cmV0dXJuIHRoaXMudXJsX3BhcmFtcztcblx0fSxcblx0Y2xlYXJVcmxDb21wb25lbnRzOiBmdW5jdGlvbigpe1xuXHRcdC8vdGhpcy51cmxfY29tcG9uZW50cyA9IFwiXCI7XG5cdFx0dGhpcy51cmxfcGFyYW1zID0ge307XG5cdH0sXG5cdGNsZWFyVGF4QXJjaGl2ZVJlc3VsdHNVcmw6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMudGF4X2FyY2hpdmVfcmVzdWx0c191cmwgPSAnJztcblx0fSxcblx0ZGlzYWJsZUlucHV0czogZnVuY3Rpb24oJGZvcm0pe1xuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XHRcblx0XHQkZm9ybS4kZmllbGRzLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdFxuXHRcdFx0dmFyICRpbnB1dHMgPSAkKHRoaXMpLmZpbmQoXCJpbnB1dCwgc2VsZWN0LCAubWV0YS1zbGlkZXJcIik7XG5cdFx0XHQkaW5wdXRzLmF0dHIoXCJkaXNhYmxlZFwiLCBcImRpc2FibGVkXCIpO1xuXHRcdFx0JGlucHV0cy5hdHRyKFwiZGlzYWJsZWRcIiwgdHJ1ZSk7XG5cdFx0XHQkaW5wdXRzLnByb3AoXCJkaXNhYmxlZFwiLCB0cnVlKTtcblx0XHRcdCRpbnB1dHMudHJpZ2dlcihcImNob3Nlbjp1cGRhdGVkXCIpO1xuXHRcdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0XG5cdH0sXG5cdGVuYWJsZUlucHV0czogZnVuY3Rpb24oJGZvcm0pe1xuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XHQkZm9ybS4kZmllbGRzLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdHZhciAkaW5wdXRzID0gJCh0aGlzKS5maW5kKFwiaW5wdXQsIHNlbGVjdCwgLm1ldGEtc2xpZGVyXCIpO1xuXHRcdFx0JGlucHV0cy5wcm9wKFwiZGlzYWJsZWRcIiwgZmFsc2UpO1xuXHRcdFx0JGlucHV0cy5hdHRyKFwiZGlzYWJsZWRcIiwgZmFsc2UpO1xuXHRcdFx0JGlucHV0cy50cmlnZ2VyKFwiY2hvc2VuOnVwZGF0ZWRcIik7XHRcdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0XG5cdH0sXG5cdGJ1aWxkVXJsQ29tcG9uZW50czogZnVuY3Rpb24oJGZvcm0sIGNsZWFyX2NvbXBvbmVudHMpe1xuXHRcdFxuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XHRcblx0XHRpZih0eXBlb2YoY2xlYXJfY29tcG9uZW50cykhPVwidW5kZWZpbmVkXCIpXG5cdFx0e1xuXHRcdFx0aWYoY2xlYXJfY29tcG9uZW50cz09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0dGhpcy5jbGVhclVybENvbXBvbmVudHMoKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0JGZvcm0uJGZpZWxkcy5lYWNoKGZ1bmN0aW9uKCl7XG5cdFx0XHRcblx0XHRcdHZhciBmaWVsZE5hbWUgPSAkKHRoaXMpLmF0dHIoXCJkYXRhLXNmLWZpZWxkLW5hbWVcIik7XG5cdFx0XHR2YXIgZmllbGRUeXBlID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC10eXBlXCIpO1xuXHRcdFx0XG5cdFx0XHRpZihmaWVsZFR5cGU9PVwic2VhcmNoXCIpXG5cdFx0XHR7XG5cdFx0XHRcdHNlbGYucHJvY2Vzc1NlYXJjaEZpZWxkKCQodGhpcykpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZSBpZigoZmllbGRUeXBlPT1cInRhZ1wiKXx8KGZpZWxkVHlwZT09XCJjYXRlZ29yeVwiKXx8KGZpZWxkVHlwZT09XCJ0YXhvbm9teVwiKSlcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzVGF4b25vbXkoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJzb3J0X29yZGVyXCIpXG5cdFx0XHR7XG5cdFx0XHRcdHNlbGYucHJvY2Vzc1NvcnRPcmRlckZpZWxkKCQodGhpcykpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZSBpZihmaWVsZFR5cGU9PVwicG9zdHNfcGVyX3BhZ2VcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUmVzdWx0c1BlclBhZ2VGaWVsZCgkKHRoaXMpKTtcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoZmllbGRUeXBlPT1cImF1dGhvclwiKVxuXHRcdFx0e1xuXHRcdFx0XHRzZWxmLnByb2Nlc3NBdXRob3IoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJwb3N0X3R5cGVcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUG9zdFR5cGUoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJwb3N0X2RhdGVcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUG9zdERhdGUoJCh0aGlzKSk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGZpZWxkVHlwZT09XCJwb3N0X21ldGFcIilcblx0XHRcdHtcblx0XHRcdFx0c2VsZi5wcm9jZXNzUG9zdE1ldGEoJCh0aGlzKSk7XG5cdFx0XHRcdFxuXHRcdFx0fVxuXHRcdFx0ZWxzZVxuXHRcdFx0e1xuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdFxuXHRcdH0pO1xuXHRcdFxuXHR9LFxuXHRwcm9jZXNzU2VhcmNoRmllbGQ6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cdFx0XG5cdFx0dmFyICRmaWVsZCA9ICRjb250YWluZXIuZmluZChcImlucHV0W25hbWVePSdfc2Zfc2VhcmNoJ11cIik7XG5cdFx0XG5cdFx0aWYoJGZpZWxkLmxlbmd0aD4wKVxuXHRcdHtcblx0XHRcdHZhciBmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHR2YXIgZmllbGRWYWwgPSAkZmllbGQudmFsKCk7XG5cdFx0XHRcblx0XHRcdGlmKGZpZWxkVmFsIT1cIlwiKVxuXHRcdFx0e1xuXHRcdFx0XHQvL3NlbGYudXJsX2NvbXBvbmVudHMgKz0gXCImX3NmX3M9XCIrZW5jb2RlVVJJQ29tcG9uZW50KGZpZWxkVmFsKTtcblx0XHRcdFx0c2VsZi51cmxfcGFyYW1zWydfc2ZfcyddID0gZW5jb2RlVVJJQ29tcG9uZW50KGZpZWxkVmFsKTtcblx0XHRcdH1cblx0XHR9XG5cdH0sXG5cdHByb2Nlc3NTb3J0T3JkZXJGaWVsZDogZnVuY3Rpb24oJGNvbnRhaW5lcilcblx0e1xuXHRcdHRoaXMucHJvY2Vzc0F1dGhvcigkY29udGFpbmVyKTtcblx0XHRcblx0fSxcblx0cHJvY2Vzc1Jlc3VsdHNQZXJQYWdlRmllbGQ6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR0aGlzLnByb2Nlc3NBdXRob3IoJGNvbnRhaW5lcik7XG5cdFx0XG5cdH0sXG5cdGdldEFjdGl2ZVRheDogZnVuY3Rpb24oJGZpZWxkKSB7XG5cdFx0cmV0dXJuIHRoaXMuYWN0aXZlX3RheDtcblx0fSxcblx0Z2V0U2VsZWN0VmFsOiBmdW5jdGlvbigkZmllbGQpe1xuXG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHRpZigkZmllbGQudmFsKCkhPTApXG5cdFx0e1xuXHRcdFx0ZmllbGRWYWwgPSAkZmllbGQudmFsKCk7XG5cdFx0fVxuXHRcdFxuXHRcdGlmKGZpZWxkVmFsPT1udWxsKVxuXHRcdHtcblx0XHRcdGZpZWxkVmFsID0gXCJcIjtcblx0XHR9XG5cdFx0XG5cdFx0cmV0dXJuIGZpZWxkVmFsO1xuXHR9LFxuXHRnZXRNZXRhU2VsZWN0VmFsOiBmdW5jdGlvbigkZmllbGQpe1xuXHRcdFxuXHRcdHZhciBmaWVsZFZhbCA9IFwiXCI7XG5cdFx0XG5cdFx0ZmllbGRWYWwgPSAkZmllbGQudmFsKCk7XG5cdFx0XHRcdFx0XHRcblx0XHRpZihmaWVsZFZhbD09bnVsbClcblx0XHR7XG5cdFx0XHRmaWVsZFZhbCA9IFwiXCI7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBmaWVsZFZhbDtcblx0fSxcblx0Z2V0TXVsdGlTZWxlY3RWYWw6IGZ1bmN0aW9uKCRmaWVsZCwgb3BlcmF0b3Ipe1xuXHRcdFxuXHRcdHZhciBkZWxpbSA9IFwiK1wiO1xuXHRcdGlmKG9wZXJhdG9yPT1cIm9yXCIpXG5cdFx0e1xuXHRcdFx0ZGVsaW0gPSBcIixcIjtcblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKCRmaWVsZC52YWwoKSk9PVwib2JqZWN0XCIpXG5cdFx0e1xuXHRcdFx0aWYoJGZpZWxkLnZhbCgpIT1udWxsKVxuXHRcdFx0e1xuXHRcdFx0XHRyZXR1cm4gJGZpZWxkLnZhbCgpLmpvaW4oZGVsaW0pO1xuXHRcdFx0fVxuXHRcdH1cblx0XHRcblx0fSxcblx0Z2V0TWV0YU11bHRpU2VsZWN0VmFsOiBmdW5jdGlvbigkZmllbGQsIG9wZXJhdG9yKXtcblx0XHRcblx0XHR2YXIgZGVsaW0gPSBcIi0rLVwiO1xuXHRcdGlmKG9wZXJhdG9yPT1cIm9yXCIpXG5cdFx0e1xuXHRcdFx0ZGVsaW0gPSBcIi0sLVwiO1xuXHRcdH1cblx0XHRcdFx0XG5cdFx0aWYodHlwZW9mKCRmaWVsZC52YWwoKSk9PVwib2JqZWN0XCIpXG5cdFx0e1xuXHRcdFx0aWYoJGZpZWxkLnZhbCgpIT1udWxsKVxuXHRcdFx0e1xuXHRcdFx0XHRcblx0XHRcdFx0dmFyIGZpZWxkdmFsID0gW107XG5cdFx0XHRcdFxuXHRcdFx0XHQkKCRmaWVsZC52YWwoKSkuZWFjaChmdW5jdGlvbihpbmRleCx2YWx1ZSl7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0ZmllbGR2YWwucHVzaCgodmFsdWUpKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRyZXR1cm4gZmllbGR2YWwuam9pbihkZWxpbSk7XG5cdFx0XHR9XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBcIlwiO1xuXHRcdFxuXHR9LFxuXHRnZXRDaGVja2JveFZhbDogZnVuY3Rpb24oJGZpZWxkLCBvcGVyYXRvcil7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIGZpZWxkVmFsID0gJGZpZWxkLm1hcChmdW5jdGlvbigpe1xuXHRcdFx0aWYoJCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiKT09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0cmV0dXJuICQodGhpcykudmFsKCk7XG5cdFx0XHR9XG5cdFx0fSkuZ2V0KCk7XG5cdFx0XG5cdFx0dmFyIGRlbGltID0gXCIrXCI7XG5cdFx0aWYob3BlcmF0b3I9PVwib3JcIilcblx0XHR7XG5cdFx0XHRkZWxpbSA9IFwiLFwiO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gZmllbGRWYWwuam9pbihkZWxpbSk7XG5cdH0sXG5cdGdldE1ldGFDaGVja2JveFZhbDogZnVuY3Rpb24oJGZpZWxkLCBvcGVyYXRvcil7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIGZpZWxkVmFsID0gJGZpZWxkLm1hcChmdW5jdGlvbigpe1xuXHRcdFx0aWYoJCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiKT09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0cmV0dXJuICgkKHRoaXMpLnZhbCgpKTtcblx0XHRcdH1cblx0XHR9KS5nZXQoKTtcblx0XHRcblx0XHR2YXIgZGVsaW0gPSBcIi0rLVwiO1xuXHRcdGlmKG9wZXJhdG9yPT1cIm9yXCIpXG5cdFx0e1xuXHRcdFx0ZGVsaW0gPSBcIi0sLVwiO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gZmllbGRWYWwuam9pbihkZWxpbSk7XG5cdH0sXG5cdGdldFJhZGlvVmFsOiBmdW5jdGlvbigkZmllbGQpe1xuXHRcdFx0XHRcdFx0XHRcblx0XHR2YXIgZmllbGRWYWwgPSAkZmllbGQubWFwKGZ1bmN0aW9uKClcblx0XHR7XG5cdFx0XHRpZigkKHRoaXMpLnByb3AoXCJjaGVja2VkXCIpPT10cnVlKVxuXHRcdFx0e1xuXHRcdFx0XHRyZXR1cm4gJCh0aGlzKS52YWwoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdH0pLmdldCgpO1xuXHRcdFxuXHRcdFxuXHRcdGlmKGZpZWxkVmFsWzBdIT0wKVxuXHRcdHtcblx0XHRcdHJldHVybiBmaWVsZFZhbFswXTtcblx0XHR9XG5cdH0sXG5cdGdldE1ldGFSYWRpb1ZhbDogZnVuY3Rpb24oJGZpZWxkKXtcblx0XHRcdFx0XHRcdFx0XG5cdFx0dmFyIGZpZWxkVmFsID0gJGZpZWxkLm1hcChmdW5jdGlvbigpXG5cdFx0e1xuXHRcdFx0aWYoJCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiKT09dHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0cmV0dXJuICQodGhpcykudmFsKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9KS5nZXQoKTtcblx0XHRcblx0XHRyZXR1cm4gZmllbGRWYWxbMF07XG5cdH0sXG5cdHByb2Nlc3NBdXRob3I6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIGZpZWxkVHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtdHlwZVwiKTtcblx0XHR2YXIgaW5wdXRUeXBlID0gJGNvbnRhaW5lci5hdHRyKFwiZGF0YS1zZi1maWVsZC1pbnB1dC10eXBlXCIpO1xuXHRcdFxuXHRcdHZhciAkZmllbGQ7XG5cdFx0dmFyIGZpZWxkTmFtZSA9IFwiXCI7XG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHRpZihpbnB1dFR5cGU9PVwic2VsZWN0XCIpXG5cdFx0e1xuXHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwic2VsZWN0XCIpO1xuXHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFx0XG5cdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0U2VsZWN0VmFsKCRmaWVsZCk7IFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJtdWx0aXNlbGVjdFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdHZhciBvcGVyYXRvciA9ICRmaWVsZC5hdHRyKFwiZGF0YS1vcGVyYXRvclwiKTtcblx0XHRcdFxuXHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldE11bHRpU2VsZWN0VmFsKCRmaWVsZCwgXCJvclwiKTtcblx0XHRcdFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJjaGVja2JveFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRcblx0XHRcdGlmKCRmaWVsZC5sZW5ndGg+MClcblx0XHRcdHtcblx0XHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0dmFyIG9wZXJhdG9yID0gJGNvbnRhaW5lci5maW5kKFwiPiB1bFwiKS5hdHRyKFwiZGF0YS1vcGVyYXRvclwiKTtcblx0XHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldENoZWNrYm94VmFsKCRmaWVsZCwgXCJvclwiKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJyYWRpb1wiKVxuXHRcdHtcblx0XHRcdFxuXHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwidWwgPiBsaSBpbnB1dDpyYWRpb1wiKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0aWYoJGZpZWxkLmxlbmd0aD4wKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0UmFkaW9WYWwoJGZpZWxkKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKGZpZWxkVmFsKSE9XCJ1bmRlZmluZWRcIilcblx0XHR7XG5cdFx0XHRpZihmaWVsZFZhbCE9XCJcIilcblx0XHRcdHtcblx0XHRcdFx0dmFyIGZpZWxkU2x1ZyA9IFwiXCI7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZihmaWVsZE5hbWU9PVwiX3NmX2F1dGhvclwiKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGRTbHVnID0gXCJhdXRob3JzXCI7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZSBpZihmaWVsZE5hbWU9PVwiX3NmX3NvcnRfb3JkZXJcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkU2x1ZyA9IFwic29ydF9vcmRlclwiO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2UgaWYoZmllbGROYW1lPT1cIl9zZl9wcHBcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkU2x1ZyA9IFwiX3NmX3BwcFwiO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2UgaWYoZmllbGROYW1lPT1cIl9zZl9wb3N0X3R5cGVcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkU2x1ZyA9IFwicG9zdF90eXBlc1wiO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Vcblx0XHRcdFx0e1xuXHRcdFx0XHRcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0aWYoZmllbGRTbHVnIT1cIlwiKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0Ly9zZWxmLnVybF9jb21wb25lbnRzICs9IFwiJlwiK2ZpZWxkU2x1ZytcIj1cIitmaWVsZFZhbDtcblx0XHRcdFx0XHRzZWxmLnVybF9wYXJhbXNbZmllbGRTbHVnXSA9IGZpZWxkVmFsO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fVxuXHRcdFxuXHR9LFxuXHRwcm9jZXNzUG9zdFR5cGUgOiBmdW5jdGlvbigkdGhpcyl7XG5cdFx0XG5cdFx0dGhpcy5wcm9jZXNzQXV0aG9yKCR0aGlzKTtcblx0XHRcblx0fSxcblx0cHJvY2Vzc1Bvc3RNZXRhOiBmdW5jdGlvbigkY29udGFpbmVyKVxuXHR7XG5cdFx0dmFyIHNlbGYgPSB0aGlzO1xuXHRcdFxuXHRcdHZhciBmaWVsZFR5cGUgPSAkY29udGFpbmVyLmF0dHIoXCJkYXRhLXNmLWZpZWxkLXR5cGVcIik7XG5cdFx0dmFyIGlucHV0VHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtaW5wdXQtdHlwZVwiKTtcblx0XHR2YXIgbWV0YVR5cGUgPSAkY29udGFpbmVyLmF0dHIoXCJkYXRhLXNmLW1ldGEtdHlwZVwiKTtcblxuXHRcdHZhciBmaWVsZFZhbCA9IFwiXCI7XG5cdFx0dmFyICRmaWVsZDtcblx0XHR2YXIgZmllbGROYW1lID0gXCJcIjtcblx0XHRcblx0XHRpZihtZXRhVHlwZT09XCJudW1iZXJcIilcblx0XHR7XG5cdFx0XHRpZihpbnB1dFR5cGU9PVwicmFuZ2UtbnVtYmVyXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcIi5zZi1tZXRhLXJhbmdlLW51bWJlciBpbnB1dFwiKTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciB2YWx1ZXMgPSBbXTtcblx0XHRcdFx0JGZpZWxkLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHR2YWx1ZXMucHVzaCgkKHRoaXMpLnZhbCgpKTtcblx0XHRcdFx0XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0ZmllbGRWYWwgPSB2YWx1ZXMuam9pbihcIitcIik7XG5cdFx0XHRcdFxuXHRcdFx0fVxuXHRcdFx0ZWxzZSBpZihpbnB1dFR5cGU9PVwicmFuZ2Utc2xpZGVyXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcIi5zZi1tZXRhLXJhbmdlLXNsaWRlciBpbnB1dFwiKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vZ2V0IGFueSBudW1iZXIgZm9ybWF0dGluZyBzdHVmZlxuXHRcdFx0XHR2YXIgJG1ldGFfcmFuZ2UgPSAkY29udGFpbmVyLmZpbmQoXCIuc2YtbWV0YS1yYW5nZS1zbGlkZXJcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHR2YXIgZGVjaW1hbF9wbGFjZXMgPSAkbWV0YV9yYW5nZS5hdHRyKFwiZGF0YS1kZWNpbWFsLXBsYWNlc1wiKTtcblx0XHRcdFx0dmFyIHRob3VzYW5kX3NlcGVyYXRvciA9ICRtZXRhX3JhbmdlLmF0dHIoXCJkYXRhLXRob3VzYW5kLXNlcGVyYXRvclwiKTtcblx0XHRcdFx0dmFyIGRlY2ltYWxfc2VwZXJhdG9yID0gJG1ldGFfcmFuZ2UuYXR0cihcImRhdGEtZGVjaW1hbC1zZXBlcmF0b3JcIik7XG5cblx0XHRcdFx0dmFyIGZpZWxkX2Zvcm1hdCA9IHdOdW1iKHtcblx0XHRcdFx0XHRtYXJrOiBkZWNpbWFsX3NlcGVyYXRvcixcblx0XHRcdFx0XHRkZWNpbWFsczogcGFyc2VGbG9hdChkZWNpbWFsX3BsYWNlcyksXG5cdFx0XHRcdFx0dGhvdXNhbmQ6IHRob3VzYW5kX3NlcGVyYXRvclxuXHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciB2YWx1ZXMgPSBbXTtcblxuXG5cdFx0XHRcdHZhciBzbGlkZXJfb2JqZWN0ID0gJGNvbnRhaW5lci5maW5kKFwiLm1ldGEtc2xpZGVyXCIpWzBdO1xuXHRcdFx0XHQvL3ZhbCBmcm9tIHNsaWRlciBvYmplY3Rcblx0XHRcdFx0dmFyIHNsaWRlcl92YWwgPSBzbGlkZXJfb2JqZWN0Lm5vVWlTbGlkZXIuZ2V0KCk7XG5cblx0XHRcdFx0dmFsdWVzLnB1c2goZmllbGRfZm9ybWF0LmZyb20oc2xpZGVyX3ZhbFswXSkpO1xuXHRcdFx0XHR2YWx1ZXMucHVzaChmaWVsZF9mb3JtYXQuZnJvbShzbGlkZXJfdmFsWzFdKSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRmaWVsZFZhbCA9IHZhbHVlcy5qb2luKFwiK1wiKTtcblx0XHRcdFx0XG5cdFx0XHRcdGZpZWxkTmFtZSA9ICRtZXRhX3JhbmdlLmF0dHIoXCJkYXRhLXNmLWZpZWxkLW5hbWVcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cInJhbmdlLXJhZGlvXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcIi5zZi1pbnB1dC1yYW5nZS1yYWRpb1wiKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg9PTApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQvL3RoZW4gdHJ5IGFnYWluLCB3ZSBtdXN0IGJlIHVzaW5nIGEgc2luZ2xlIGZpZWxkXG5cdFx0XHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwiPiB1bFwiKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHZhciAkbWV0YV9yYW5nZSA9ICRjb250YWluZXIuZmluZChcIi5zZi1tZXRhLXJhbmdlXCIpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly90aGVyZSBpcyBhbiBlbGVtZW50IHdpdGggYSBmcm9tL3RvIGNsYXNzIC0gc28gd2UgbmVlZCB0byBnZXQgdGhlIHZhbHVlcyBvZiB0aGUgZnJvbSAmIHRvIGlucHV0IGZpZWxkcyBzZXBlcmF0ZWx5XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg+MClcblx0XHRcdFx0e1x0XG5cdFx0XHRcdFx0dmFyIGZpZWxkX3ZhbHMgPSBbXTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkZmllbGQuZWFjaChmdW5jdGlvbigpe1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHR2YXIgJHJhZGlvcyA9ICQodGhpcykuZmluZChcIi5zZi1pbnB1dC1yYWRpb1wiKTtcblx0XHRcdFx0XHRcdGZpZWxkX3ZhbHMucHVzaChzZWxmLmdldE1ldGFSYWRpb1ZhbCgkcmFkaW9zKSk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvL3ByZXZlbnQgc2Vjb25kIG51bWJlciBmcm9tIGJlaW5nIGxvd2VyIHRoYW4gdGhlIGZpcnN0XG5cdFx0XHRcdFx0aWYoZmllbGRfdmFscy5sZW5ndGg9PTIpXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0aWYoTnVtYmVyKGZpZWxkX3ZhbHNbMV0pPE51bWJlcihmaWVsZF92YWxzWzBdKSlcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0ZmllbGRfdmFsc1sxXSA9IGZpZWxkX3ZhbHNbMF07XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGZpZWxkVmFsID0gZmllbGRfdmFscy5qb2luKFwiK1wiKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPT0xKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmZpbmQoXCIuc2YtaW5wdXQtcmFkaW9cIikuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGROYW1lID0gJG1ldGFfcmFuZ2UuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcblx0XHRcdFx0fVxuXG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJyYW5nZS1zZWxlY3RcIilcblx0XHRcdHtcblx0XHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwiLnNmLWlucHV0LXNlbGVjdFwiKTtcblx0XHRcdFx0dmFyICRtZXRhX3JhbmdlID0gJGNvbnRhaW5lci5maW5kKFwiLnNmLW1ldGEtcmFuZ2VcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHQvL3RoZXJlIGlzIGFuIGVsZW1lbnQgd2l0aCBhIGZyb20vdG8gY2xhc3MgLSBzbyB3ZSBuZWVkIHRvIGdldCB0aGUgdmFsdWVzIG9mIHRoZSBmcm9tICYgdG8gaW5wdXQgZmllbGRzIHNlcGVyYXRlbHlcblx0XHRcdFx0XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg+MClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHZhciBmaWVsZF92YWxzID0gW107XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JGZpZWxkLmVhY2goZnVuY3Rpb24oKXtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0dmFyICR0aGlzID0gJCh0aGlzKTtcblx0XHRcdFx0XHRcdGZpZWxkX3ZhbHMucHVzaChzZWxmLmdldE1ldGFTZWxlY3RWYWwoJHRoaXMpKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vcHJldmVudCBzZWNvbmQgbnVtYmVyIGZyb20gYmVpbmcgbG93ZXIgdGhhbiB0aGUgZmlyc3Rcblx0XHRcdFx0XHRpZihmaWVsZF92YWxzLmxlbmd0aD09Milcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRpZihOdW1iZXIoZmllbGRfdmFsc1sxXSk8TnVtYmVyKGZpZWxkX3ZhbHNbMF0pKVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRmaWVsZF92YWxzWzFdID0gZmllbGRfdmFsc1swXTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0ZmllbGRWYWwgPSBmaWVsZF92YWxzLmpvaW4oXCIrXCIpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdGlmKCRmaWVsZC5sZW5ndGg9PTEpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGROYW1lID0gJG1ldGFfcmFuZ2UuYXR0cihcImRhdGEtc2YtZmllbGQtbmFtZVwiKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cInJhbmdlLWNoZWNrYm94XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0Q2hlY2tib3hWYWwoJGZpZWxkLCBcImFuZFwiKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZihmaWVsZE5hbWU9PVwiXCIpXG5cdFx0XHR7XG5cdFx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0ZWxzZSBpZihtZXRhVHlwZT09XCJjaG9pY2VcIilcblx0XHR7XG5cdFx0XHRpZihpbnB1dFR5cGU9PVwic2VsZWN0XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdFx0XG5cdFx0XHRcdGZpZWxkVmFsID0gc2VsZi5nZXRNZXRhU2VsZWN0VmFsKCRmaWVsZCk7IFxuXHRcdFx0XHRcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cIm11bHRpc2VsZWN0XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdFx0dmFyIG9wZXJhdG9yID0gJGZpZWxkLmF0dHIoXCJkYXRhLW9wZXJhdG9yXCIpO1xuXHRcdFx0XHRcblx0XHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldE1ldGFNdWx0aVNlbGVjdFZhbCgkZmllbGQsIG9wZXJhdG9yKTtcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cImNoZWNrYm94XCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHR2YXIgb3BlcmF0b3IgPSAkY29udGFpbmVyLmZpbmQoXCI+IHVsXCIpLmF0dHIoXCJkYXRhLW9wZXJhdG9yXCIpO1xuXHRcdFx0XHRcdGZpZWxkVmFsID0gc2VsZi5nZXRNZXRhQ2hlY2tib3hWYWwoJGZpZWxkLCBvcGVyYXRvcik7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdGVsc2UgaWYoaW5wdXRUeXBlPT1cInJhZGlvXCIpXG5cdFx0XHR7XG5cdFx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6cmFkaW9cIik7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0TWV0YVJhZGlvVmFsKCRmaWVsZCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZmllbGRWYWwgPSBlbmNvZGVVUklDb21wb25lbnQoZmllbGRWYWwpO1xuXHRcdFx0aWYodHlwZW9mKCRmaWVsZCkhPT1cInVuZGVmaW5lZFwiKVxuXHRcdFx0e1xuXHRcdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly9mb3IgdGhvc2Ugd2hvIGluc2lzdCBvbiB1c2luZyAmIGFtcGVyc2FuZHMgaW4gdGhlIG5hbWUgb2YgdGhlIGN1c3RvbSBmaWVsZCAoISlcblx0XHRcdFx0XHRmaWVsZE5hbWUgPSAoZmllbGROYW1lKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0fVxuXHRcdGVsc2UgaWYobWV0YVR5cGU9PVwiZGF0ZVwiKVxuXHRcdHtcblx0XHRcdHNlbGYucHJvY2Vzc1Bvc3REYXRlKCRjb250YWluZXIpO1xuXHRcdH1cblx0XHRcblx0XHRpZih0eXBlb2YoZmllbGRWYWwpIT1cInVuZGVmaW5lZFwiKVxuXHRcdHtcblx0XHRcdGlmKGZpZWxkVmFsIT1cIlwiKVxuXHRcdFx0e1xuXHRcdFx0XHQvL3NlbGYudXJsX2NvbXBvbmVudHMgKz0gXCImXCIrZW5jb2RlVVJJQ29tcG9uZW50KGZpZWxkTmFtZSkrXCI9XCIrKGZpZWxkVmFsKTtcblx0XHRcdFx0c2VsZi51cmxfcGFyYW1zW2VuY29kZVVSSUNvbXBvbmVudChmaWVsZE5hbWUpXSA9IChmaWVsZFZhbCk7XG5cdFx0XHR9XG5cdFx0fVxuXHR9LFxuXHRwcm9jZXNzUG9zdERhdGU6IGZ1bmN0aW9uKCRjb250YWluZXIpXG5cdHtcblx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cdFx0XG5cdFx0dmFyIGZpZWxkVHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtdHlwZVwiKTtcblx0XHR2YXIgaW5wdXRUeXBlID0gJGNvbnRhaW5lci5hdHRyKFwiZGF0YS1zZi1maWVsZC1pbnB1dC10eXBlXCIpO1xuXHRcdFxuXHRcdHZhciAkZmllbGQ7XG5cdFx0dmFyIGZpZWxkTmFtZSA9IFwiXCI7XG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHQkZmllbGQgPSAkY29udGFpbmVyLmZpbmQoXCJ1bCA+IGxpIGlucHV0OnRleHRcIik7XG5cdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFxuXHRcdHZhciBkYXRlcyA9IFtdO1xuXHRcdCRmaWVsZC5lYWNoKGZ1bmN0aW9uKCl7XG5cdFx0XHRcblx0XHRcdGRhdGVzLnB1c2goJCh0aGlzKS52YWwoKSk7XG5cdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0aWYoJGZpZWxkLmxlbmd0aD09Milcblx0XHR7XG5cdFx0XHRpZigoZGF0ZXNbMF0hPVwiXCIpfHwoZGF0ZXNbMV0hPVwiXCIpKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZFZhbCA9IGRhdGVzLmpvaW4oXCIrXCIpO1xuXHRcdFx0XHRmaWVsZFZhbCA9IGZpZWxkVmFsLnJlcGxhY2UoL1xcLy9nLCcnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0ZWxzZSBpZigkZmllbGQubGVuZ3RoPT0xKVxuXHRcdHtcblx0XHRcdGlmKGRhdGVzWzBdIT1cIlwiKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZFZhbCA9IGRhdGVzLmpvaW4oXCIrXCIpO1xuXHRcdFx0XHRmaWVsZFZhbCA9IGZpZWxkVmFsLnJlcGxhY2UoL1xcLy9nLCcnKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKGZpZWxkVmFsKSE9XCJ1bmRlZmluZWRcIilcblx0XHR7XG5cdFx0XHRpZihmaWVsZFZhbCE9XCJcIilcblx0XHRcdHtcblx0XHRcdFx0dmFyIGZpZWxkU2x1ZyA9IFwiXCI7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZihmaWVsZE5hbWU9PVwiX3NmX3Bvc3RfZGF0ZVwiKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0ZmllbGRTbHVnID0gXCJwb3N0X2RhdGVcIjtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmaWVsZFNsdWcgPSBmaWVsZE5hbWU7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdGlmKGZpZWxkU2x1ZyE9XCJcIilcblx0XHRcdFx0e1xuXHRcdFx0XHRcdC8vc2VsZi51cmxfY29tcG9uZW50cyArPSBcIiZcIitmaWVsZFNsdWcrXCI9XCIrZmllbGRWYWw7XG5cdFx0XHRcdFx0c2VsZi51cmxfcGFyYW1zW2ZpZWxkU2x1Z10gPSBmaWVsZFZhbDtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH1cblx0XHRcblx0fSxcblx0cHJvY2Vzc1RheG9ub215OiBmdW5jdGlvbigkY29udGFpbmVyLCByZXR1cm5fb2JqZWN0KVxuXHR7XG4gICAgICAgIGlmKHR5cGVvZihyZXR1cm5fb2JqZWN0KT09XCJ1bmRlZmluZWRcIilcbiAgICAgICAge1xuICAgICAgICAgICAgcmV0dXJuX29iamVjdCA9IGZhbHNlO1xuICAgICAgICB9XG5cblx0XHQvL2lmKClcdFx0XHRcdFx0XG5cdFx0Ly92YXIgZmllbGROYW1lID0gJCh0aGlzKS5hdHRyKFwiZGF0YS1zZi1maWVsZC1uYW1lXCIpO1xuXHRcdHZhciBzZWxmID0gdGhpcztcblx0XG5cdFx0dmFyIGZpZWxkVHlwZSA9ICRjb250YWluZXIuYXR0cihcImRhdGEtc2YtZmllbGQtdHlwZVwiKTtcblx0XHR2YXIgaW5wdXRUeXBlID0gJGNvbnRhaW5lci5hdHRyKFwiZGF0YS1zZi1maWVsZC1pbnB1dC10eXBlXCIpO1xuXHRcdFxuXHRcdHZhciAkZmllbGQ7XG5cdFx0dmFyIGZpZWxkTmFtZSA9IFwiXCI7XG5cdFx0dmFyIGZpZWxkVmFsID0gXCJcIjtcblx0XHRcblx0XHRpZihpbnB1dFR5cGU9PVwic2VsZWN0XCIpXG5cdFx0e1xuXHRcdFx0JGZpZWxkID0gJGNvbnRhaW5lci5maW5kKFwic2VsZWN0XCIpO1xuXHRcdFx0ZmllbGROYW1lID0gJGZpZWxkLmF0dHIoXCJuYW1lXCIpLnJlcGxhY2UoJ1tdJywgJycpO1xuXHRcdFx0XG5cdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0U2VsZWN0VmFsKCRmaWVsZCk7IFxuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJtdWx0aXNlbGVjdFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInNlbGVjdFwiKTtcblx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdHZhciBvcGVyYXRvciA9ICRmaWVsZC5hdHRyKFwiZGF0YS1vcGVyYXRvclwiKTtcblx0XHRcdFxuXHRcdFx0ZmllbGRWYWwgPSBzZWxmLmdldE11bHRpU2VsZWN0VmFsKCRmaWVsZCwgb3BlcmF0b3IpO1xuXHRcdH1cblx0XHRlbHNlIGlmKGlucHV0VHlwZT09XCJjaGVja2JveFwiKVxuXHRcdHtcblx0XHRcdCRmaWVsZCA9ICRjb250YWluZXIuZmluZChcInVsID4gbGkgaW5wdXQ6Y2hlY2tib3hcIik7XG5cdFx0XHRpZigkZmllbGQubGVuZ3RoPjApXG5cdFx0XHR7XG5cdFx0XHRcdGZpZWxkTmFtZSA9ICRmaWVsZC5hdHRyKFwibmFtZVwiKS5yZXBsYWNlKCdbXScsICcnKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdHZhciBvcGVyYXRvciA9ICRjb250YWluZXIuZmluZChcIj4gdWxcIikuYXR0cihcImRhdGEtb3BlcmF0b3JcIik7XG5cdFx0XHRcdGZpZWxkVmFsID0gc2VsZi5nZXRDaGVja2JveFZhbCgkZmllbGQsIG9wZXJhdG9yKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0ZWxzZSBpZihpbnB1dFR5cGU9PVwicmFkaW9cIilcblx0XHR7XG5cdFx0XHQkZmllbGQgPSAkY29udGFpbmVyLmZpbmQoXCJ1bCA+IGxpIGlucHV0OnJhZGlvXCIpO1xuXHRcdFx0aWYoJGZpZWxkLmxlbmd0aD4wKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZE5hbWUgPSAkZmllbGQuYXR0cihcIm5hbWVcIikucmVwbGFjZSgnW10nLCAnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRmaWVsZFZhbCA9IHNlbGYuZ2V0UmFkaW9WYWwoJGZpZWxkKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYodHlwZW9mKGZpZWxkVmFsKSE9XCJ1bmRlZmluZWRcIilcblx0XHR7XG5cdFx0XHRpZihmaWVsZFZhbCE9XCJcIilcblx0XHRcdHtcbiAgICAgICAgICAgICAgICBpZihyZXR1cm5fb2JqZWN0PT10cnVlKVxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtuYW1lOiBmaWVsZE5hbWUsIHZhbHVlOiBmaWVsZFZhbH07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIC8vc2VsZi51cmxfY29tcG9uZW50cyArPSBcIiZcIitmaWVsZE5hbWUrXCI9XCIrZmllbGRWYWw7XG4gICAgICAgICAgICAgICAgICAgIHNlbGYudXJsX3BhcmFtc1tmaWVsZE5hbWVdID0gZmllbGRWYWw7XG4gICAgICAgICAgICAgICAgfVxuXG5cdFx0XHR9XG5cdFx0fVxuXG4gICAgICAgIGlmKHJldHVybl9vYmplY3Q9PXRydWUpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXHR9XG59O1xufSkuY2FsbCh0aGlzLHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWwgOiB0eXBlb2Ygc2VsZiAhPT0gXCJ1bmRlZmluZWRcIiA/IHNlbGYgOiB0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93IDoge30pXG4vLyMgc291cmNlTWFwcGluZ1VSTD1kYXRhOmFwcGxpY2F0aW9uL2pzb247Y2hhcnNldDp1dGYtODtiYXNlNjQsZXlKMlpYSnphVzl1SWpvekxDSnpiM1Z5WTJWeklqcGJJbk55WXk5d2RXSnNhV012WVhOelpYUnpMMnB6TDJsdVkyeDFaR1Z6TDNCeWIyTmxjM05mWm05eWJTNXFjeUpkTENKdVlXMWxjeUk2VzEwc0ltMWhjSEJwYm1keklqb2lPMEZCUVVFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRWlMQ0ptYVd4bElqb2laMlZ1WlhKaGRHVmtMbXB6SWl3aWMyOTFjbU5sVW05dmRDSTZJaUlzSW5OdmRYSmpaWE5EYjI1MFpXNTBJanBiSWx4dWRtRnlJQ1FnUFNBb2RIbHdaVzltSUhkcGJtUnZkeUFoUFQwZ1hDSjFibVJsWm1sdVpXUmNJaUEvSUhkcGJtUnZkMXNuYWxGMVpYSjVKMTBnT2lCMGVYQmxiMllnWjJ4dlltRnNJQ0U5UFNCY0luVnVaR1ZtYVc1bFpGd2lJRDhnWjJ4dlltRnNXeWRxVVhWbGNua25YU0E2SUc1MWJHd3BPMXh1WEc1dGIyUjFiR1V1Wlhod2IzSjBjeUE5SUh0Y2JseHVYSFIwWVhodmJtOXRlVjloY21Ob2FYWmxjem9nTUN4Y2JpQWdJQ0IxY214ZmNHRnlZVzF6T2lCN2ZTeGNiaUFnSUNCMFlYaGZZWEpqYUdsMlpWOXlaWE4xYkhSelgzVnliRG9nWENKY0lpeGNiaUFnSUNCaFkzUnBkbVZmZEdGNE9pQmNJbHdpTEZ4dUlDQWdJR1pwWld4a2N6b2dlMzBzWEc1Y2RHbHVhWFE2SUdaMWJtTjBhVzl1S0hSaGVHOXViMjE1WDJGeVkyaHBkbVZ6TENCamRYSnlaVzUwWDNSaGVHOXViMjE1WDJGeVkyaHBkbVVwZTF4dVhHNGdJQ0FnSUNBZ0lIUm9hWE11ZEdGNGIyNXZiWGxmWVhKamFHbDJaWE1nUFNBd08xeHVJQ0FnSUNBZ0lDQjBhR2x6TG5WeWJGOXdZWEpoYlhNZ1BTQjdmVHRjYmlBZ0lDQWdJQ0FnZEdocGN5NTBZWGhmWVhKamFHbDJaVjl5WlhOMWJIUnpYM1Z5YkNBOUlGd2lYQ0k3WEc0Z0lDQWdJQ0FnSUhSb2FYTXVZV04wYVhabFgzUmhlQ0E5SUZ3aVhDSTdYRzVjYmx4MFhIUXZMM1JvYVhNdUpHWnBaV3hrY3lBOUlDUm1hV1ZzWkhNN1hHNGdJQ0FnSUNBZ0lIUm9hWE11ZEdGNGIyNXZiWGxmWVhKamFHbDJaWE1nUFNCMFlYaHZibTl0ZVY5aGNtTm9hWFpsY3p0Y2JpQWdJQ0FnSUNBZ2RHaHBjeTVqZFhKeVpXNTBYM1JoZUc5dWIyMTVYMkZ5WTJocGRtVWdQU0JqZFhKeVpXNTBYM1JoZUc5dWIyMTVYMkZ5WTJocGRtVTdYRzVjYmx4MFhIUjBhR2x6TG1Oc1pXRnlWWEpzUTI5dGNHOXVaVzUwY3lncE8xeHVYRzVjZEgwc1hHNGdJQ0FnYzJWMFZHRjRRWEpqYUdsMlpWSmxjM1ZzZEhOVmNtdzZJR1oxYm1OMGFXOXVLQ1JtYjNKdExDQmpkWEp5Wlc1MFgzSmxjM1ZzZEhOZmRYSnNMQ0JuWlhSZllXTjBhWFpsS1NCN1hHNWNiaUFnSUNBZ0lDQWdkbUZ5SUhObGJHWWdQU0IwYUdsek8xeHVYSFJjZEhSb2FYTXVZMnhsWVhKVVlYaEJjbU5vYVhabFVtVnpkV3gwYzFWeWJDZ3BPMXh1SUNBZ0lDQWdJQ0F2TDNaaGNpQmpkWEp5Wlc1MFgzSmxjM1ZzZEhOZmRYSnNJRDBnWENKY0lqdGNiaUFnSUNBZ0lDQWdhV1lvZEdocGN5NTBZWGh2Ym05dGVWOWhjbU5vYVhabGN5RTlNU2xjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdU8xeHVJQ0FnSUNBZ0lDQjlYRzVjYmlBZ0lDQWdJQ0FnYVdZb2RIbHdaVzltS0dkbGRGOWhZM1JwZG1VcFBUMWNJblZ1WkdWbWFXNWxaRndpS1Z4dVhIUmNkSHRjYmx4MFhIUmNkSFpoY2lCblpYUmZZV04wYVhabElEMGdabUZzYzJVN1hHNWNkRngwZlZ4dVhHNGdJQ0FnSUNBZ0lDOHZZMmhsWTJzZ2RHOGdjMlZsSUdsbUlIZGxJR2hoZG1VZ1lXNTVJSFJoZUc5dWIyMXBaWE1nYzJWc1pXTjBaV1JjYmlBZ0lDQWdJQ0FnTHk5cFppQnpieXdnWTJobFkyc2dkR2hsYVhJZ2NtVjNjbWwwWlhNZ1lXNWtJSFZ6WlNCMGFHOXpaU0JoY3lCMGFHVWdjbVZ6ZFd4MGN5QjFjbXhjYmlBZ0lDQWdJQ0FnZG1GeUlDUm1hV1ZzWkNBOUlHWmhiSE5sTzF4dUlDQWdJQ0FnSUNCMllYSWdabWxsYkdSZmJtRnRaU0E5SUZ3aVhDSTdYRzRnSUNBZ0lDQWdJSFpoY2lCbWFXVnNaRjkyWVd4MVpTQTlJRndpWENJN1hHNWNiaUFnSUNBZ0lDQWdkbUZ5SUNSaFkzUnBkbVZmZEdGNGIyNXZiWGtnUFNBa1ptOXliUzRrWm1sbGJHUnpMbkJoY21WdWRDZ3BMbVpwYm1Rb1hDSmJaR0YwWVMxelppMTBZWGh2Ym05dGVTMWhjbU5vYVhabFBTY3hKMTFjSWlrN1hHNGdJQ0FnSUNBZ0lHbG1LQ1JoWTNScGRtVmZkR0Y0YjI1dmJYa3ViR1Z1WjNSb1BUMHhLVnh1SUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWtabWxsYkdRZ1BTQWtZV04wYVhabFgzUmhlRzl1YjIxNU8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCMllYSWdabWxsYkdSVWVYQmxJRDBnSkdacFpXeGtMbUYwZEhJb1hDSmtZWFJoTFhObUxXWnBaV3hrTFhSNWNHVmNJaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJR2xtSUNnb1ptbGxiR1JVZVhCbElEMDlJRndpZEdGblhDSXBJSHg4SUNobWFXVnNaRlI1Y0dVZ1BUMGdYQ0pqWVhSbFoyOXllVndpS1NCOGZDQW9abWxsYkdSVWVYQmxJRDA5SUZ3aWRHRjRiMjV2YlhsY0lpa3BJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2RHRjRiMjV2YlhsZmRtRnNkV1VnUFNCelpXeG1MbkJ5YjJObGMzTlVZWGh2Ym05dGVTZ2tabWxsYkdRc0lIUnlkV1VwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdacFpXeGtYMjVoYldVZ1BTQWtabWxsYkdRdVlYUjBjaWhjSW1SaGRHRXRjMll0Wm1sbGJHUXRibUZ0WlZ3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnZEdGNGIyNXZiWGxmYm1GdFpTQTlJR1pwWld4a1gyNWhiV1V1Y21Wd2JHRmpaU2hjSWw5elpuUmZYQ0lzSUZ3aVhDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tIUmhlRzl1YjIxNVgzWmhiSFZsS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1pwWld4a1gzWmhiSFZsSUQwZ2RHRjRiMjV2YlhsZmRtRnNkV1V1ZG1Gc2RXVTdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNCcFppaG1hV1ZzWkY5MllXeDFaVDA5WENKY0lpbGNiaUFnSUNBZ0lDQWdJQ0FnSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBa1ptbGxiR1FnUFNCbVlXeHpaVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lIMWNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtDaHpaV3htTG1OMWNuSmxiblJmZEdGNGIyNXZiWGxmWVhKamFHbDJaU0U5WENKY0lpa21KaWh6Wld4bUxtTjFjbkpsYm5SZmRHRjRiMjV2YlhsZllYSmphR2wyWlNFOWRHRjRiMjV2YlhsZmJtRnRaU2twWEc0Z0lDQWdJQ0FnSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NTBZWGhmWVhKamFHbDJaVjl5WlhOMWJIUnpYM1Z5YkNBOUlHTjFjbkpsYm5SZmNtVnpkV3gwYzE5MWNtdzdYRzRnSUNBZ0lDQWdJQ0FnSUNCeVpYUjFjbTQ3WEc0Z0lDQWdJQ0FnSUgxY2JseHVJQ0FnSUNBZ0lDQnBaaWdvS0dacFpXeGtYM1poYkhWbFBUMWNJbHdpS1h4OEtDRWtabWxsYkdRcElDa3BYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDUm1iM0p0TGlSbWFXVnNaSE11WldGamFDaG1kVzVqZEdsdmJpQW9LU0I3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9JU1JtYVdWc1pDa2dlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJtYVdWc1pGUjVjR1VnUFNBa0tIUm9hWE1wTG1GMGRISW9YQ0prWVhSaExYTm1MV1pwWld4a0xYUjVjR1ZjSWlrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tDaG1hV1ZzWkZSNWNHVWdQVDBnWENKMFlXZGNJaWtnZkh3Z0tHWnBaV3hrVkhsd1pTQTlQU0JjSW1OaGRHVm5iM0o1WENJcElIeDhJQ2htYVdWc1pGUjVjR1VnUFQwZ1hDSjBZWGh2Ym05dGVWd2lLU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1GeUlIUmhlRzl1YjIxNVgzWmhiSFZsSUQwZ2MyVnNaaTV3Y205alpYTnpWR0Y0YjI1dmJYa29KQ2gwYUdsektTd2dkSEoxWlNrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCbWFXVnNaRjl1WVcxbElEMGdKQ2gwYUdsektTNWhkSFJ5S0Z3aVpHRjBZUzF6WmkxbWFXVnNaQzF1WVcxbFhDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb2RHRjRiMjV2YlhsZmRtRnNkV1VwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1pwWld4a1gzWmhiSFZsSUQwZ2RHRjRiMjV2YlhsZmRtRnNkV1V1ZG1Gc2RXVTdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCcFppQW9abWxsYkdSZmRtRnNkV1VnSVQwZ1hDSmNJaWtnZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNSbWFXVnNaQ0E5SUNRb2RHaHBjeWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjlYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0I5S1R0Y2JpQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJR2xtS0NBb0pHWnBaV3hrS1NBbUppQW9abWxsYkdSZmRtRnNkV1VnSVQwZ1hDSmNJaUFwS1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0F2TDJsbUlIZGxJR1p2ZFc1a0lHRWdabWxsYkdSY2JseDBYSFJjZEhaaGNpQnlaWGR5YVhSbFgyRjBkSElnUFNBb0pHWnBaV3hrTG1GMGRISW9YQ0prWVhSaExYTm1MWFJsY20wdGNtVjNjbWwwWlZ3aUtTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lHbG1LSEpsZDNKcGRHVmZZWFIwY2lFOVhDSmNJaWtnZTF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSEpsZDNKcGRHVWdQU0JLVTA5T0xuQmhjbk5sS0hKbGQzSnBkR1ZmWVhSMGNpazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUdsdWNIVjBYM1I1Y0dVZ1BTQWtabWxsYkdRdVlYUjBjaWhjSW1SaGRHRXRjMll0Wm1sbGJHUXRhVzV3ZFhRdGRIbHdaVndpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCelpXeG1MbUZqZEdsMlpWOTBZWGdnUFNCbWFXVnNaRjl1WVcxbE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTltYVc1a0lIUm9aU0JoWTNScGRtVWdaV3hsYldWdWRGeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHbG1JQ2dvYVc1d2RYUmZkSGx3WlNBOVBTQmNJbkpoWkdsdlhDSXBJSHg4SUNocGJuQjFkRjkwZVhCbElEMDlJRndpWTJobFkydGliM2hjSWlrcElIdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwzWmhjaUFrWVdOMGFYWmxJRDBnSkdacFpXeGtMbVpwYm1Rb1hDSXVjMll0YjNCMGFXOXVMV0ZqZEdsMlpWd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OWxlSEJzYjJSbElIUm9aU0IyWVd4MVpYTWdhV1lnZEdobGNtVWdhWE1nWVNCa1pXeHBiVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBdkwyWnBaV3hrWDNaaGJIVmxYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJR2x6WDNOcGJtZHNaVjkyWVd4MVpTQTlJSFJ5ZFdVN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCbWFXVnNaRjkyWVd4MVpYTWdQU0JtYVdWc1pGOTJZV3gxWlM1emNHeHBkQ2hjSWl4Y0lpa3VhbTlwYmloY0lpdGNJaWt1YzNCc2FYUW9YQ0lyWENJcE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmlBb1ptbGxiR1JmZG1Gc2RXVnpMbXhsYm1kMGFDQStJREVwSUh0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUdselgzTnBibWRzWlY5MllXeDFaU0E5SUdaaGJITmxPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnYVdZZ0tHbHpYM05wYm1kc1pWOTJZV3gxWlNrZ2UxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnSkdsdWNIVjBJRDBnSkdacFpXeGtMbVpwYm1Rb1hDSnBibkIxZEZ0MllXeDFaVDBuWENJZ0t5Qm1hV1ZzWkY5MllXeDFaU0FySUZ3aUoxMWNJaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pHRmpkR2wyWlNBOUlDUnBibkIxZEM1d1lYSmxiblFvS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQmtaWEIwYUNBOUlDUmhZM1JwZG1VdVlYUjBjaWhjSW1SaGRHRXRjMll0WkdWd2RHaGNJaWs3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dmJtOTNJR3h2YjNBZ2RHaHliM1ZuYUNCd1lYSmxiblJ6SUhSdklHZHlZV0lnZEdobGFYSWdibUZ0WlhOY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQjJZV3gxWlhNZ1BTQnVaWGNnUVhKeVlYa29LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoYkhWbGN5NXdkWE5vS0dacFpXeGtYM1poYkhWbEtUdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdabTl5SUNoMllYSWdhU0E5SUdSbGNIUm9PeUJwSUQ0Z01Ec2dhUzB0S1NCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdKR0ZqZEdsMlpTQTlJQ1JoWTNScGRtVXVjR0Z5Wlc1MEtDa3VjR0Z5Wlc1MEtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1Gc2RXVnpMbkIxYzJnb0pHRmpkR2wyWlM1bWFXNWtLRndpYVc1d2RYUmNJaWt1ZG1Gc0tDa3BPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVd4MVpYTXVjbVYyWlhKelpTZ3BPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0F2TDJkeVlXSWdkR2hsSUhKbGQzSnBkR1VnWm05eUlIUm9hWE1nWkdWd2RHaGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJoWTNScGRtVmZjbVYzY21sMFpTQTlJSEpsZDNKcGRHVmJaR1Z3ZEdoZE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJSFZ5YkNBOUlHRmpkR2wyWlY5eVpYZHlhWFJsTzF4dVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZkR2hsYmlCdFlYQWdabkp2YlNCMGFHVWdjR0Z5Wlc1MGN5QjBieUIwYUdVZ1pHVndkR2hjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ1FvZG1Gc2RXVnpLUzVsWVdOb0tHWjFibU4wYVc5dUlDaHBibVJsZUN3Z2RtRnNkV1VwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFZ5YkNBOUlIVnliQzV5WlhCc1lXTmxLRndpVzF3aUlDc2dhVzVrWlhnZ0t5QmNJbDFjSWl3Z2RtRnNkV1VwTzF4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIUm9hWE11ZEdGNFgyRnlZMmhwZG1WZmNtVnpkV3gwYzE5MWNtd2dQU0IxY213N1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXeHpaU0I3WEc1Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4dmFXWWdkR2hsY21VZ1lYSmxJRzExYkhScGNHeGxJSFpoYkhWbGN5eGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDOHZkR2hsYmlCM1pTQnVaV1ZrSUhSdklHTm9aV05ySUdadmNpQXpJSFJvYVc1bmN6cGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdMeTlwWmlCMGFHVWdkbUZzZFdWeklITmxiR1ZqZEdWa0lHRnlaU0JoYkd3Z2FXNGdkR2hsSUhOaGJXVWdkSEpsWlNCMGFHVnVJSGRsSUdOaGJpQmtieUJ6YjIxbElHTnNaWFpsY2lCeVpYZHlhWFJsSUhOMGRXWm1YRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQXZMMjFsY21kbElHRnNiQ0IyWVd4MVpYTWdhVzRnYzJGdFpTQnNaWFpsYkN3Z2RHaGxiaUJqYjIxaWFXNWxJSFJvWlNCc1pYWmxiSE5jYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnTHk5cFppQjBhR1Y1SUdGeVpTQm1jbTl0SUdScFptWmxjbVZ1ZENCMGNtVmxjeUIwYUdWdUlHcDFjM1FnWTI5dFltbHVaU0IwYUdWdElHOXlJR3AxYzNRZ2RYTmxJR0JtYVdWc1pGOTJZV3gxWldCY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUM4cVhHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnWkdWd2RHaHpJRDBnYm1WM0lFRnljbUY1S0NrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pDaG1hV1ZzWkY5MllXeDFaWE1wTG1WaFkyZ29ablZ1WTNScGIyNGdLR2x1WkdWNExDQjJZV3dwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUFrYVc1d2RYUWdQU0FrWm1sbGJHUXVabWx1WkNoY0ltbHVjSFYwVzNaaGJIVmxQU2RjSWlBcklHWnBaV3hrWDNaaGJIVmxJQ3NnWENJblhWd2lLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ0pHRmpkR2wyWlNBOUlDUnBibkIxZEM1d1lYSmxiblFvS1R0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIWmhjaUJrWlhCMGFDQTlJQ1JoWTNScGRtVXVZWFIwY2loY0ltUmhkR0V0YzJZdFpHVndkR2hjSWlrN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0x5OWtaWEIwYUhNdWNIVnphQ2hrWlhCMGFDazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0I5S1RzcUwxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCOVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1pXeHpaU0JwWmlBb0tHbHVjSFYwWDNSNWNHVWdQVDBnWENKelpXeGxZM1JjSWlrZ2ZId2dLR2x1Y0hWMFgzUjVjR1VnUFQwZ1hDSnRkV3gwYVhObGJHVmpkRndpS1NrZ2UxeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUhaaGNpQnBjMTl6YVc1bmJHVmZkbUZzZFdVZ1BTQjBjblZsTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZWElnWm1sbGJHUmZkbUZzZFdWeklEMGdabWxsYkdSZmRtRnNkV1V1YzNCc2FYUW9YQ0lzWENJcExtcHZhVzRvWENJclhDSXBMbk53YkdsMEtGd2lLMXdpS1R0Y2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lnS0dacFpXeGtYM1poYkhWbGN5NXNaVzVuZEdnZ1BpQXhLU0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwYzE5emFXNW5iR1ZmZG1Gc2RXVWdQU0JtWVd4elpUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZlZ4dVhHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR2xtSUNocGMxOXphVzVuYkdWZmRtRnNkV1VwSUh0Y2JseHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RtRnlJQ1JoWTNScGRtVWdQU0FrWm1sbGJHUXVabWx1WkNoY0ltOXdkR2x2Ymx0MllXeDFaVDBuWENJZ0t5Qm1hV1ZzWkY5MllXeDFaU0FySUZ3aUoxMWNJaWs3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ1pHVndkR2dnUFNBa1lXTjBhWFpsTG1GMGRISW9YQ0prWVhSaExYTm1MV1JsY0hSb1hDSXBPMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVhJZ2RtRnNkV1Z6SUQwZ2JtVjNJRUZ5Y21GNUtDazdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQjJZV3gxWlhNdWNIVnphQ2htYVdWc1pGOTJZV3gxWlNrN1hHNWNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lHWnZjaUFvZG1GeUlHa2dQU0JrWlhCMGFEc2dhU0ErSURBN0lHa3RMU2tnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDUmhZM1JwZG1VZ1BTQWtZV04wYVhabExuQnlaWFpCYkd3b1hDSnZjSFJwYjI1YlpHRjBZUzF6Wmkxa1pYQjBhRDBuWENJZ0t5QW9hU0F0SURFcElDc2dYQ0luWFZ3aUtUdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0IyWVd4MVpYTXVjSFZ6YUNna1lXTjBhWFpsTG5aaGJDZ3BLVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDFjYmx4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZG1Gc2RXVnpMbkpsZG1WeWMyVW9LVHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCaFkzUnBkbVZmY21WM2NtbDBaU0E5SUhKbGQzSnBkR1ZiWkdWd2RHaGRPMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdkbUZ5SUhWeWJDQTlJR0ZqZEdsMlpWOXlaWGR5YVhSbE8xeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0pDaDJZV3gxWlhNcExtVmhZMmdvWm5WdVkzUnBiMjRnS0dsdVpHVjRMQ0IyWVd4MVpTa2dlMXh1WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2RYSnNJRDBnZFhKc0xuSmxjR3hoWTJVb1hDSmJYQ0lnS3lCcGJtUmxlQ0FySUZ3aVhWd2lMQ0IyWVd4MVpTazdYRzVjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSDBwTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnZEdocGN5NTBZWGhmWVhKamFHbDJaVjl5WlhOMWJIUnpYM1Z5YkNBOUlIVnliRHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ2ZWeHVYRzRnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnTHk5MGFHbHpMblJoZUY5aGNtTm9hWFpsWDNKbGMzVnNkSE5mZFhKc0lEMGdZM1Z5Y21WdWRGOXlaWE4xYkhSelgzVnliRHRjYmlBZ0lDQjlMRnh1SUNBZ0lHZGxkRkpsYzNWc2RITlZjbXc2SUdaMWJtTjBhVzl1S0NSbWIzSnRMQ0JqZFhKeVpXNTBYM0psYzNWc2RITmZkWEpzS1NCN1hHNWNiaUFnSUNBZ0lDQWdMeTkwYUdsekxuTmxkRlJoZUVGeVkyaHBkbVZTWlhOMWJIUnpWWEpzS0NSbWIzSnRMQ0JqZFhKeVpXNTBYM0psYzNWc2RITmZkWEpzS1R0Y2JseHVJQ0FnSUNBZ0lDQnBaaWgwYUdsekxuUmhlRjloY21Ob2FYWmxYM0psYzNWc2RITmZkWEpzUFQxY0lsd2lLVnh1SUNBZ0lDQWdJQ0I3WEc0Z0lDQWdJQ0FnSUNBZ0lDQnlaWFIxY200Z1kzVnljbVZ1ZEY5eVpYTjFiSFJ6WDNWeWJEdGNiaUFnSUNBZ0lDQWdmVnh1WEc0Z0lDQWdJQ0FnSUhKbGRIVnliaUIwYUdsekxuUmhlRjloY21Ob2FYWmxYM0psYzNWc2RITmZkWEpzTzF4dUlDQWdJSDBzWEc1Y2RHZGxkRlZ5YkZCaGNtRnRjem9nWm5WdVkzUnBiMjRvSkdadmNtMHBlMXh1WEc1Y2RGeDBkR2hwY3k1aWRXbHNaRlZ5YkVOdmJYQnZibVZ1ZEhNb0pHWnZjbTBzSUhSeWRXVXBPMXh1WEc0Z0lDQWdJQ0FnSUdsbUtIUm9hWE11ZEdGNFgyRnlZMmhwZG1WZmNtVnpkV3gwYzE5MWNtd2hQVndpWENJcFhHNGdJQ0FnSUNBZ0lIdGNibHh1SUNBZ0lDQWdJQ0FnSUNBZ2FXWW9kR2hwY3k1aFkzUnBkbVZmZEdGNElUMWNJbHdpS1Z4dUlDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJSFpoY2lCbWFXVnNaRjl1WVcxbElEMGdkR2hwY3k1aFkzUnBkbVZmZEdGNE8xeHVYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdhV1lvZEhsd1pXOW1LSFJvYVhNdWRYSnNYM0JoY21GdGMxdG1hV1ZzWkY5dVlXMWxYU2toUFZ3aWRXNWtaV1pwYm1Wa1hDSXBYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdlMXh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCa1pXeGxkR1VnZEdocGN5NTFjbXhmY0dGeVlXMXpXMlpwWld4a1gyNWhiV1ZkTzF4dUlDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUgxY2JpQWdJQ0FnSUNBZ0lDQWdJSDFjYmlBZ0lDQWdJQ0FnZlZ4dVhHNWNkRngwY21WMGRYSnVJSFJvYVhNdWRYSnNYM0JoY21GdGN6dGNibHgwZlN4Y2JseDBZMnhsWVhKVmNteERiMjF3YjI1bGJuUnpPaUJtZFc1amRHbHZiaWdwZTF4dVhIUmNkQzh2ZEdocGN5NTFjbXhmWTI5dGNHOXVaVzUwY3lBOUlGd2lYQ0k3WEc1Y2RGeDBkR2hwY3k1MWNteGZjR0Z5WVcxeklEMGdlMzA3WEc1Y2RIMHNYRzVjZEdOc1pXRnlWR0Y0UVhKamFHbDJaVkpsYzNWc2RITlZjbXc2SUdaMWJtTjBhVzl1S0NrZ2UxeHVYSFJjZEhSb2FYTXVkR0Y0WDJGeVkyaHBkbVZmY21WemRXeDBjMTkxY213Z1BTQW5KenRjYmx4MGZTeGNibHgwWkdsellXSnNaVWx1Y0hWMGN6b2dablZ1WTNScGIyNG9KR1p2Y20wcGUxeHVYSFJjZEhaaGNpQnpaV3htSUQwZ2RHaHBjenRjYmx4MFhIUmNibHgwWEhRa1ptOXliUzRrWm1sbGJHUnpMbVZoWTJnb1puVnVZM1JwYjI0b0tYdGNibHgwWEhSY2RGeHVYSFJjZEZ4MGRtRnlJQ1JwYm5CMWRITWdQU0FrS0hSb2FYTXBMbVpwYm1Rb1hDSnBibkIxZEN3Z2MyVnNaV04wTENBdWJXVjBZUzF6Ykdsa1pYSmNJaWs3WEc1Y2RGeDBYSFFrYVc1d2RYUnpMbUYwZEhJb1hDSmthWE5oWW14bFpGd2lMQ0JjSW1ScGMyRmliR1ZrWENJcE8xeHVYSFJjZEZ4MEpHbHVjSFYwY3k1aGRIUnlLRndpWkdsellXSnNaV1JjSWl3Z2RISjFaU2s3WEc1Y2RGeDBYSFFrYVc1d2RYUnpMbkJ5YjNBb1hDSmthWE5oWW14bFpGd2lMQ0IwY25WbEtUdGNibHgwWEhSY2RDUnBibkIxZEhNdWRISnBaMmRsY2loY0ltTm9iM05sYmpwMWNHUmhkR1ZrWENJcE8xeHVYSFJjZEZ4MFhHNWNkRngwZlNrN1hHNWNkRngwWEc1Y2RGeDBYRzVjZEgwc1hHNWNkR1Z1WVdKc1pVbHVjSFYwY3pvZ1puVnVZM1JwYjI0b0pHWnZjbTBwZTF4dVhIUmNkSFpoY2lCelpXeG1JRDBnZEdocGN6dGNibHgwWEhRa1ptOXliUzRrWm1sbGJHUnpMbVZoWTJnb1puVnVZM1JwYjI0b0tYdGNibHgwWEhSY2RIWmhjaUFrYVc1d2RYUnpJRDBnSkNoMGFHbHpLUzVtYVc1a0tGd2lhVzV3ZFhRc0lITmxiR1ZqZEN3Z0xtMWxkR0V0YzJ4cFpHVnlYQ0lwTzF4dVhIUmNkRngwSkdsdWNIVjBjeTV3Y205d0tGd2laR2x6WVdKc1pXUmNJaXdnWm1Gc2MyVXBPMXh1WEhSY2RGeDBKR2x1Y0hWMGN5NWhkSFJ5S0Z3aVpHbHpZV0pzWldSY0lpd2dabUZzYzJVcE8xeHVYSFJjZEZ4MEpHbHVjSFYwY3k1MGNtbG5aMlZ5S0Z3aVkyaHZjMlZ1T25Wd1pHRjBaV1JjSWlrN1hIUmNkRngwWEc1Y2RGeDBmU2s3WEc1Y2RGeDBYRzVjZEZ4MFhHNWNkSDBzWEc1Y2RHSjFhV3hrVlhKc1EyOXRjRzl1Wlc1MGN6b2dablZ1WTNScGIyNG9KR1p2Y20wc0lHTnNaV0Z5WDJOdmJYQnZibVZ1ZEhNcGUxeHVYSFJjZEZ4dVhIUmNkSFpoY2lCelpXeG1JRDBnZEdocGN6dGNibHgwWEhSY2JseDBYSFJwWmloMGVYQmxiMllvWTJ4bFlYSmZZMjl0Y0c5dVpXNTBjeWtoUFZ3aWRXNWtaV1pwYm1Wa1hDSXBYRzVjZEZ4MGUxeHVYSFJjZEZ4MGFXWW9ZMnhsWVhKZlkyOXRjRzl1Wlc1MGN6MDlkSEoxWlNsY2JseDBYSFJjZEh0Y2JseDBYSFJjZEZ4MGRHaHBjeTVqYkdWaGNsVnliRU52YlhCdmJtVnVkSE1vS1R0Y2JseDBYSFJjZEgxY2JseDBYSFI5WEc1Y2RGeDBYRzVjZEZ4MEpHWnZjbTB1SkdacFpXeGtjeTVsWVdOb0tHWjFibU4wYVc5dUtDbDdYRzVjZEZ4MFhIUmNibHgwWEhSY2RIWmhjaUJtYVdWc1pFNWhiV1VnUFNBa0tIUm9hWE1wTG1GMGRISW9YQ0prWVhSaExYTm1MV1pwWld4a0xXNWhiV1ZjSWlrN1hHNWNkRngwWEhSMllYSWdabWxsYkdSVWVYQmxJRDBnSkNoMGFHbHpLUzVoZEhSeUtGd2laR0YwWVMxelppMW1hV1ZzWkMxMGVYQmxYQ0lwTzF4dVhIUmNkRngwWEc1Y2RGeDBYSFJwWmlobWFXVnNaRlI1Y0dVOVBWd2ljMlZoY21Ob1hDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkSE5sYkdZdWNISnZZMlZ6YzFObFlYSmphRVpwWld4a0tDUW9kR2hwY3lrcE8xeHVYSFJjZEZ4MGZWeHVYSFJjZEZ4MFpXeHpaU0JwWmlnb1ptbGxiR1JVZVhCbFBUMWNJblJoWjF3aUtYeDhLR1pwWld4a1ZIbHdaVDA5WENKallYUmxaMjl5ZVZ3aUtYeDhLR1pwWld4a1ZIbHdaVDA5WENKMFlYaHZibTl0ZVZ3aUtTbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBjMlZzWmk1d2NtOWpaWE56VkdGNGIyNXZiWGtvSkNoMGFHbHpLU2s3WEc1Y2RGeDBYSFI5WEc1Y2RGeDBYSFJsYkhObElHbG1LR1pwWld4a1ZIbHdaVDA5WENKemIzSjBYMjl5WkdWeVhDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkSE5sYkdZdWNISnZZMlZ6YzFOdmNuUlBjbVJsY2tacFpXeGtLQ1FvZEdocGN5a3BPMXh1WEhSY2RGeDBmVnh1WEhSY2RGeDBaV3h6WlNCcFppaG1hV1ZzWkZSNWNHVTlQVndpY0c5emRITmZjR1Z5WDNCaFoyVmNJaWxjYmx4MFhIUmNkSHRjYmx4MFhIUmNkRngwYzJWc1ppNXdjbTlqWlhOelVtVnpkV3gwYzFCbGNsQmhaMlZHYVdWc1pDZ2tLSFJvYVhNcEtUdGNibHgwWEhSY2RIMWNibHgwWEhSY2RHVnNjMlVnYVdZb1ptbGxiR1JVZVhCbFBUMWNJbUYxZEdodmNsd2lLVnh1WEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJ6Wld4bUxuQnliMk5sYzNOQmRYUm9iM0lvSkNoMGFHbHpLU2s3WEc1Y2RGeDBYSFI5WEc1Y2RGeDBYSFJsYkhObElHbG1LR1pwWld4a1ZIbHdaVDA5WENKd2IzTjBYM1I1Y0dWY0lpbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBjMlZzWmk1d2NtOWpaWE56VUc5emRGUjVjR1VvSkNoMGFHbHpLU2s3WEc1Y2RGeDBYSFI5WEc1Y2RGeDBYSFJsYkhObElHbG1LR1pwWld4a1ZIbHdaVDA5WENKd2IzTjBYMlJoZEdWY0lpbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBjMlZzWmk1d2NtOWpaWE56VUc5emRFUmhkR1VvSkNoMGFHbHpLU2s3WEc1Y2RGeDBYSFI5WEc1Y2RGeDBYSFJsYkhObElHbG1LR1pwWld4a1ZIbHdaVDA5WENKd2IzTjBYMjFsZEdGY0lpbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBjMlZzWmk1d2NtOWpaWE56VUc5emRFMWxkR0VvSkNoMGFHbHpLU2s3WEc1Y2RGeDBYSFJjZEZ4dVhIUmNkRngwZlZ4dVhIUmNkRngwWld4elpWeHVYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUmNibHgwWEhSY2RIMWNibHgwWEhSY2RGeHVYSFJjZEgwcE8xeHVYSFJjZEZ4dVhIUjlMRnh1WEhSd2NtOWpaWE56VTJWaGNtTm9SbWxsYkdRNklHWjFibU4wYVc5dUtDUmpiMjUwWVdsdVpYSXBYRzVjZEh0Y2JseDBYSFIyWVhJZ2MyVnNaaUE5SUhSb2FYTTdYRzVjZEZ4MFhHNWNkRngwZG1GeUlDUm1hV1ZzWkNBOUlDUmpiMjUwWVdsdVpYSXVabWx1WkNoY0ltbHVjSFYwVzI1aGJXVmVQU2RmYzJaZmMyVmhjbU5vSjExY0lpazdYRzVjZEZ4MFhHNWNkRngwYVdZb0pHWnBaV3hrTG14bGJtZDBhRDR3S1Z4dVhIUmNkSHRjYmx4MFhIUmNkSFpoY2lCbWFXVnNaRTVoYldVZ1BTQWtabWxsYkdRdVlYUjBjaWhjSW01aGJXVmNJaWt1Y21Wd2JHRmpaU2duVzEwbkxDQW5KeWs3WEc1Y2RGeDBYSFIyWVhJZ1ptbGxiR1JXWVd3Z1BTQWtabWxsYkdRdWRtRnNLQ2s3WEc1Y2RGeDBYSFJjYmx4MFhIUmNkR2xtS0dacFpXeGtWbUZzSVQxY0lsd2lLVnh1WEhSY2RGeDBlMXh1WEhSY2RGeDBYSFF2TDNObGJHWXVkWEpzWDJOdmJYQnZibVZ1ZEhNZ0t6MGdYQ0ltWDNObVgzTTlYQ0lyWlc1amIyUmxWVkpKUTI5dGNHOXVaVzUwS0dacFpXeGtWbUZzS1R0Y2JseDBYSFJjZEZ4MGMyVnNaaTUxY214ZmNHRnlZVzF6V3lkZmMyWmZjeWRkSUQwZ1pXNWpiMlJsVlZKSlEyOXRjRzl1Wlc1MEtHWnBaV3hrVm1Gc0tUdGNibHgwWEhSY2RIMWNibHgwWEhSOVhHNWNkSDBzWEc1Y2RIQnliMk5sYzNOVGIzSjBUM0prWlhKR2FXVnNaRG9nWm5WdVkzUnBiMjRvSkdOdmJuUmhhVzVsY2lsY2JseDBlMXh1WEhSY2RIUm9hWE11Y0hKdlkyVnpjMEYxZEdodmNpZ2tZMjl1ZEdGcGJtVnlLVHRjYmx4MFhIUmNibHgwZlN4Y2JseDBjSEp2WTJWemMxSmxjM1ZzZEhOUVpYSlFZV2RsUm1sbGJHUTZJR1oxYm1OMGFXOXVLQ1JqYjI1MFlXbHVaWElwWEc1Y2RIdGNibHgwWEhSMGFHbHpMbkJ5YjJObGMzTkJkWFJvYjNJb0pHTnZiblJoYVc1bGNpazdYRzVjZEZ4MFhHNWNkSDBzWEc1Y2RHZGxkRUZqZEdsMlpWUmhlRG9nWm5WdVkzUnBiMjRvSkdacFpXeGtLU0I3WEc1Y2RGeDBjbVYwZFhKdUlIUm9hWE11WVdOMGFYWmxYM1JoZUR0Y2JseDBmU3hjYmx4MFoyVjBVMlZzWldOMFZtRnNPaUJtZFc1amRHbHZiaWdrWm1sbGJHUXBlMXh1WEc1Y2RGeDBkbUZ5SUdacFpXeGtWbUZzSUQwZ1hDSmNJanRjYmx4MFhIUmNibHgwWEhScFppZ2tabWxsYkdRdWRtRnNLQ2toUFRBcFhHNWNkRngwZTF4dVhIUmNkRngwWm1sbGJHUldZV3dnUFNBa1ptbGxiR1F1ZG1Gc0tDazdYRzVjZEZ4MGZWeHVYSFJjZEZ4dVhIUmNkR2xtS0dacFpXeGtWbUZzUFQxdWRXeHNLVnh1WEhSY2RIdGNibHgwWEhSY2RHWnBaV3hrVm1Gc0lEMGdYQ0pjSWp0Y2JseDBYSFI5WEc1Y2RGeDBYRzVjZEZ4MGNtVjBkWEp1SUdacFpXeGtWbUZzTzF4dVhIUjlMRnh1WEhSblpYUk5aWFJoVTJWc1pXTjBWbUZzT2lCbWRXNWpkR2x2Ymlna1ptbGxiR1FwZTF4dVhIUmNkRnh1WEhSY2RIWmhjaUJtYVdWc1pGWmhiQ0E5SUZ3aVhDSTdYRzVjZEZ4MFhHNWNkRngwWm1sbGJHUldZV3dnUFNBa1ptbGxiR1F1ZG1Gc0tDazdYRzVjZEZ4MFhIUmNkRngwWEhSY2JseDBYSFJwWmlobWFXVnNaRlpoYkQwOWJuVnNiQ2xjYmx4MFhIUjdYRzVjZEZ4MFhIUm1hV1ZzWkZaaGJDQTlJRndpWENJN1hHNWNkRngwZlZ4dVhIUmNkRnh1WEhSY2RISmxkSFZ5YmlCbWFXVnNaRlpoYkR0Y2JseDBmU3hjYmx4MFoyVjBUWFZzZEdsVFpXeGxZM1JXWVd3NklHWjFibU4wYVc5dUtDUm1hV1ZzWkN3Z2IzQmxjbUYwYjNJcGUxeHVYSFJjZEZ4dVhIUmNkSFpoY2lCa1pXeHBiU0E5SUZ3aUsxd2lPMXh1WEhSY2RHbG1LRzl3WlhKaGRHOXlQVDFjSW05eVhDSXBYRzVjZEZ4MGUxeHVYSFJjZEZ4MFpHVnNhVzBnUFNCY0lpeGNJanRjYmx4MFhIUjlYRzVjZEZ4MFhHNWNkRngwYVdZb2RIbHdaVzltS0NSbWFXVnNaQzUyWVd3b0tTazlQVndpYjJKcVpXTjBYQ0lwWEc1Y2RGeDBlMXh1WEhSY2RGeDBhV1lvSkdacFpXeGtMblpoYkNncElUMXVkV3hzS1Z4dVhIUmNkRngwZTF4dVhIUmNkRngwWEhSeVpYUjFjbTRnSkdacFpXeGtMblpoYkNncExtcHZhVzRvWkdWc2FXMHBPMXh1WEhSY2RGeDBmVnh1WEhSY2RIMWNibHgwWEhSY2JseDBmU3hjYmx4MFoyVjBUV1YwWVUxMWJIUnBVMlZzWldOMFZtRnNPaUJtZFc1amRHbHZiaWdrWm1sbGJHUXNJRzl3WlhKaGRHOXlLWHRjYmx4MFhIUmNibHgwWEhSMllYSWdaR1ZzYVcwZ1BTQmNJaTByTFZ3aU8xeHVYSFJjZEdsbUtHOXdaWEpoZEc5eVBUMWNJbTl5WENJcFhHNWNkRngwZTF4dVhIUmNkRngwWkdWc2FXMGdQU0JjSWkwc0xWd2lPMXh1WEhSY2RIMWNibHgwWEhSY2RGeDBYRzVjZEZ4MGFXWW9kSGx3Wlc5bUtDUm1hV1ZzWkM1MllXd29LU2s5UFZ3aWIySnFaV04wWENJcFhHNWNkRngwZTF4dVhIUmNkRngwYVdZb0pHWnBaV3hrTG5aaGJDZ3BJVDF1ZFd4c0tWeHVYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUmNibHgwWEhSY2RGeDBkbUZ5SUdacFpXeGtkbUZzSUQwZ1cxMDdYRzVjZEZ4MFhIUmNkRnh1WEhSY2RGeDBYSFFrS0NSbWFXVnNaQzUyWVd3b0tTa3VaV0ZqYUNobWRXNWpkR2x2YmlocGJtUmxlQ3gyWVd4MVpTbDdYRzVjZEZ4MFhIUmNkRngwWEc1Y2RGeDBYSFJjZEZ4MFptbGxiR1IyWVd3dWNIVnphQ2dvZG1Gc2RXVXBLVHRjYmx4MFhIUmNkRngwZlNrN1hHNWNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUnlaWFIxY200Z1ptbGxiR1IyWVd3dWFtOXBiaWhrWld4cGJTazdYRzVjZEZ4MFhIUjlYRzVjZEZ4MGZWeHVYSFJjZEZ4dVhIUmNkSEpsZEhWeWJpQmNJbHdpTzF4dVhIUmNkRnh1WEhSOUxGeHVYSFJuWlhSRGFHVmphMkp2ZUZaaGJEb2dablZ1WTNScGIyNG9KR1pwWld4a0xDQnZjR1Z5WVhSdmNpbDdYRzVjZEZ4MFhHNWNkRngwWEc1Y2RGeDBkbUZ5SUdacFpXeGtWbUZzSUQwZ0pHWnBaV3hrTG0xaGNDaG1kVzVqZEdsdmJpZ3BlMXh1WEhSY2RGeDBhV1lvSkNoMGFHbHpLUzV3Y205d0tGd2lZMmhsWTJ0bFpGd2lLVDA5ZEhKMVpTbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBjbVYwZFhKdUlDUW9kR2hwY3lrdWRtRnNLQ2s3WEc1Y2RGeDBYSFI5WEc1Y2RGeDBmU2t1WjJWMEtDazdYRzVjZEZ4MFhHNWNkRngwZG1GeUlHUmxiR2x0SUQwZ1hDSXJYQ0k3WEc1Y2RGeDBhV1lvYjNCbGNtRjBiM0k5UFZ3aWIzSmNJaWxjYmx4MFhIUjdYRzVjZEZ4MFhIUmtaV3hwYlNBOUlGd2lMRndpTzF4dVhIUmNkSDFjYmx4MFhIUmNibHgwWEhSeVpYUjFjbTRnWm1sbGJHUldZV3d1YW05cGJpaGtaV3hwYlNrN1hHNWNkSDBzWEc1Y2RHZGxkRTFsZEdGRGFHVmphMkp2ZUZaaGJEb2dablZ1WTNScGIyNG9KR1pwWld4a0xDQnZjR1Z5WVhSdmNpbDdYRzVjZEZ4MFhHNWNkRngwWEc1Y2RGeDBkbUZ5SUdacFpXeGtWbUZzSUQwZ0pHWnBaV3hrTG0xaGNDaG1kVzVqZEdsdmJpZ3BlMXh1WEhSY2RGeDBhV1lvSkNoMGFHbHpLUzV3Y205d0tGd2lZMmhsWTJ0bFpGd2lLVDA5ZEhKMVpTbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBjbVYwZFhKdUlDZ2tLSFJvYVhNcExuWmhiQ2dwS1R0Y2JseDBYSFJjZEgxY2JseDBYSFI5S1M1blpYUW9LVHRjYmx4MFhIUmNibHgwWEhSMllYSWdaR1ZzYVcwZ1BTQmNJaTByTFZ3aU8xeHVYSFJjZEdsbUtHOXdaWEpoZEc5eVBUMWNJbTl5WENJcFhHNWNkRngwZTF4dVhIUmNkRngwWkdWc2FXMGdQU0JjSWkwc0xWd2lPMXh1WEhSY2RIMWNibHgwWEhSY2JseDBYSFJ5WlhSMWNtNGdabWxsYkdSV1lXd3VhbTlwYmloa1pXeHBiU2s3WEc1Y2RIMHNYRzVjZEdkbGRGSmhaR2x2Vm1Gc09pQm1kVzVqZEdsdmJpZ2tabWxsYkdRcGUxeHVYSFJjZEZ4MFhIUmNkRngwWEhSY2JseDBYSFIyWVhJZ1ptbGxiR1JXWVd3Z1BTQWtabWxsYkdRdWJXRndLR1oxYm1OMGFXOXVLQ2xjYmx4MFhIUjdYRzVjZEZ4MFhIUnBaaWdrS0hSb2FYTXBMbkJ5YjNBb1hDSmphR1ZqYTJWa1hDSXBQVDEwY25WbEtWeHVYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUnlaWFIxY200Z0pDaDBhR2x6S1M1MllXd29LVHRjYmx4MFhIUmNkSDFjYmx4MFhIUmNkRnh1WEhSY2RIMHBMbWRsZENncE8xeHVYSFJjZEZ4dVhIUmNkRnh1WEhSY2RHbG1LR1pwWld4a1ZtRnNXekJkSVQwd0tWeHVYSFJjZEh0Y2JseDBYSFJjZEhKbGRIVnliaUJtYVdWc1pGWmhiRnN3WFR0Y2JseDBYSFI5WEc1Y2RIMHNYRzVjZEdkbGRFMWxkR0ZTWVdScGIxWmhiRG9nWm5WdVkzUnBiMjRvSkdacFpXeGtLWHRjYmx4MFhIUmNkRngwWEhSY2RGeDBYRzVjZEZ4MGRtRnlJR1pwWld4a1ZtRnNJRDBnSkdacFpXeGtMbTFoY0NobWRXNWpkR2x2YmlncFhHNWNkRngwZTF4dVhIUmNkRngwYVdZb0pDaDBhR2x6S1M1d2NtOXdLRndpWTJobFkydGxaRndpS1QwOWRISjFaU2xjYmx4MFhIUmNkSHRjYmx4MFhIUmNkRngwY21WMGRYSnVJQ1FvZEdocGN5a3VkbUZzS0NrN1hHNWNkRngwWEhSOVhHNWNkRngwWEhSY2JseDBYSFI5S1M1blpYUW9LVHRjYmx4MFhIUmNibHgwWEhSeVpYUjFjbTRnWm1sbGJHUldZV3hiTUYwN1hHNWNkSDBzWEc1Y2RIQnliMk5sYzNOQmRYUm9iM0k2SUdaMWJtTjBhVzl1S0NSamIyNTBZV2x1WlhJcFhHNWNkSHRjYmx4MFhIUjJZWElnYzJWc1ppQTlJSFJvYVhNN1hHNWNkRngwWEc1Y2RGeDBYRzVjZEZ4MGRtRnlJR1pwWld4a1ZIbHdaU0E5SUNSamIyNTBZV2x1WlhJdVlYUjBjaWhjSW1SaGRHRXRjMll0Wm1sbGJHUXRkSGx3WlZ3aUtUdGNibHgwWEhSMllYSWdhVzV3ZFhSVWVYQmxJRDBnSkdOdmJuUmhhVzVsY2k1aGRIUnlLRndpWkdGMFlTMXpaaTFtYVdWc1pDMXBibkIxZEMxMGVYQmxYQ0lwTzF4dVhIUmNkRnh1WEhSY2RIWmhjaUFrWm1sbGJHUTdYRzVjZEZ4MGRtRnlJR1pwWld4a1RtRnRaU0E5SUZ3aVhDSTdYRzVjZEZ4MGRtRnlJR1pwWld4a1ZtRnNJRDBnWENKY0lqdGNibHgwWEhSY2JseDBYSFJwWmlocGJuQjFkRlI1Y0dVOVBWd2ljMlZzWldOMFhDSXBYRzVjZEZ4MGUxeHVYSFJjZEZ4MEpHWnBaV3hrSUQwZ0pHTnZiblJoYVc1bGNpNW1hVzVrS0Z3aWMyVnNaV04wWENJcE8xeHVYSFJjZEZ4MFptbGxiR1JPWVcxbElEMGdKR1pwWld4a0xtRjBkSElvWENKdVlXMWxYQ0lwTG5KbGNHeGhZMlVvSjF0ZEp5d2dKeWNwTzF4dVhIUmNkRngwWEc1Y2RGeDBYSFJtYVdWc1pGWmhiQ0E5SUhObGJHWXVaMlYwVTJWc1pXTjBWbUZzS0NSbWFXVnNaQ2s3SUZ4dVhIUmNkSDFjYmx4MFhIUmxiSE5sSUdsbUtHbHVjSFYwVkhsd1pUMDlYQ0p0ZFd4MGFYTmxiR1ZqZEZ3aUtWeHVYSFJjZEh0Y2JseDBYSFJjZENSbWFXVnNaQ0E5SUNSamIyNTBZV2x1WlhJdVptbHVaQ2hjSW5ObGJHVmpkRndpS1R0Y2JseDBYSFJjZEdacFpXeGtUbUZ0WlNBOUlDUm1hV1ZzWkM1aGRIUnlLRndpYm1GdFpWd2lLUzV5WlhCc1lXTmxLQ2RiWFNjc0lDY25LVHRjYmx4MFhIUmNkSFpoY2lCdmNHVnlZWFJ2Y2lBOUlDUm1hV1ZzWkM1aGRIUnlLRndpWkdGMFlTMXZjR1Z5WVhSdmNsd2lLVHRjYmx4MFhIUmNkRnh1WEhSY2RGeDBabWxsYkdSV1lXd2dQU0J6Wld4bUxtZGxkRTExYkhScFUyVnNaV04wVm1Gc0tDUm1hV1ZzWkN3Z1hDSnZjbHdpS1R0Y2JseDBYSFJjZEZ4dVhIUmNkSDFjYmx4MFhIUmxiSE5sSUdsbUtHbHVjSFYwVkhsd1pUMDlYQ0pqYUdWamEySnZlRndpS1Z4dVhIUmNkSHRjYmx4MFhIUmNkQ1JtYVdWc1pDQTlJQ1JqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJblZzSUQ0Z2JHa2dhVzV3ZFhRNlkyaGxZMnRpYjNoY0lpazdYRzVjZEZ4MFhIUmNibHgwWEhSY2RHbG1LQ1JtYVdWc1pDNXNaVzVuZEdnK01DbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBabWxsYkdST1lXMWxJRDBnSkdacFpXeGtMbUYwZEhJb1hDSnVZVzFsWENJcExuSmxjR3hoWTJVb0oxdGRKeXdnSnljcE8xeHVYSFJjZEZ4MFhIUmNkRngwWEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwZG1GeUlHOXdaWEpoZEc5eUlEMGdKR052Ym5SaGFXNWxjaTVtYVc1a0tGd2lQaUIxYkZ3aUtTNWhkSFJ5S0Z3aVpHRjBZUzF2Y0dWeVlYUnZjbHdpS1R0Y2JseDBYSFJjZEZ4MFptbGxiR1JXWVd3Z1BTQnpaV3htTG1kbGRFTm9aV05yWW05NFZtRnNLQ1JtYVdWc1pDd2dYQ0p2Y2x3aUtUdGNibHgwWEhSY2RIMWNibHgwWEhSY2RGeHVYSFJjZEgxY2JseDBYSFJsYkhObElHbG1LR2x1Y0hWMFZIbHdaVDA5WENKeVlXUnBiMXdpS1Z4dVhIUmNkSHRjYmx4MFhIUmNkRnh1WEhSY2RGeDBKR1pwWld4a0lEMGdKR052Ym5SaGFXNWxjaTVtYVc1a0tGd2lkV3dnUGlCc2FTQnBibkIxZERweVlXUnBiMXdpS1R0Y2JseDBYSFJjZEZ4MFhIUmNkRnh1WEhSY2RGeDBhV1lvSkdacFpXeGtMbXhsYm1kMGFENHdLVnh1WEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJtYVdWc1pFNWhiV1VnUFNBa1ptbGxiR1F1WVhSMGNpaGNJbTVoYldWY0lpa3VjbVZ3YkdGalpTZ25XMTBuTENBbkp5azdYRzVjZEZ4MFhIUmNkRnh1WEhSY2RGeDBYSFJtYVdWc1pGWmhiQ0E5SUhObGJHWXVaMlYwVW1Ga2FXOVdZV3dvSkdacFpXeGtLVHRjYmx4MFhIUmNkSDFjYmx4MFhIUjlYRzVjZEZ4MFhHNWNkRngwYVdZb2RIbHdaVzltS0dacFpXeGtWbUZzS1NFOVhDSjFibVJsWm1sdVpXUmNJaWxjYmx4MFhIUjdYRzVjZEZ4MFhIUnBaaWhtYVdWc1pGWmhiQ0U5WENKY0lpbGNibHgwWEhSY2RIdGNibHgwWEhSY2RGeDBkbUZ5SUdacFpXeGtVMngxWnlBOUlGd2lYQ0k3WEc1Y2RGeDBYSFJjZEZ4dVhIUmNkRngwWEhScFppaG1hV1ZzWkU1aGJXVTlQVndpWDNObVgyRjFkR2h2Y2x3aUtWeHVYSFJjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkRngwWm1sbGJHUlRiSFZuSUQwZ1hDSmhkWFJvYjNKelhDSTdYRzVjZEZ4MFhIUmNkSDFjYmx4MFhIUmNkRngwWld4elpTQnBaaWhtYVdWc1pFNWhiV1U5UFZ3aVgzTm1YM052Y25SZmIzSmtaWEpjSWlsY2JseDBYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUmNkR1pwWld4a1UyeDFaeUE5SUZ3aWMyOXlkRjl2Y21SbGNsd2lPMXh1WEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFJjZEdWc2MyVWdhV1lvWm1sbGJHUk9ZVzFsUFQxY0lsOXpabDl3Y0hCY0lpbGNibHgwWEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJjZEdacFpXeGtVMngxWnlBOUlGd2lYM05tWDNCd2NGd2lPMXh1WEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFJjZEdWc2MyVWdhV1lvWm1sbGJHUk9ZVzFsUFQxY0lsOXpabDl3YjNOMFgzUjVjR1ZjSWlsY2JseDBYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUmNkR1pwWld4a1UyeDFaeUE5SUZ3aWNHOXpkRjkwZVhCbGMxd2lPMXh1WEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFJjZEdWc2MyVmNibHgwWEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwZlZ4dVhIUmNkRngwWEhSY2JseDBYSFJjZEZ4MGFXWW9abWxsYkdSVGJIVm5JVDFjSWx3aUtWeHVYSFJjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkRngwTHk5elpXeG1MblZ5YkY5amIyMXdiMjVsYm5SeklDczlJRndpSmx3aUsyWnBaV3hrVTJ4MVp5dGNJajFjSWl0bWFXVnNaRlpoYkR0Y2JseDBYSFJjZEZ4MFhIUnpaV3htTG5WeWJGOXdZWEpoYlhOYlptbGxiR1JUYkhWblhTQTlJR1pwWld4a1ZtRnNPMXh1WEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFI5WEc1Y2RGeDBmVnh1WEhSY2RGeHVYSFI5TEZ4dVhIUndjbTlqWlhOelVHOXpkRlI1Y0dVZ09pQm1kVzVqZEdsdmJpZ2tkR2hwY3lsN1hHNWNkRngwWEc1Y2RGeDBkR2hwY3k1d2NtOWpaWE56UVhWMGFHOXlLQ1IwYUdsektUdGNibHgwWEhSY2JseDBmU3hjYmx4MGNISnZZMlZ6YzFCdmMzUk5aWFJoT2lCbWRXNWpkR2x2Ymlna1kyOXVkR0ZwYm1WeUtWeHVYSFI3WEc1Y2RGeDBkbUZ5SUhObGJHWWdQU0IwYUdsek8xeHVYSFJjZEZ4dVhIUmNkSFpoY2lCbWFXVnNaRlI1Y0dVZ1BTQWtZMjl1ZEdGcGJtVnlMbUYwZEhJb1hDSmtZWFJoTFhObUxXWnBaV3hrTFhSNWNHVmNJaWs3WEc1Y2RGeDBkbUZ5SUdsdWNIVjBWSGx3WlNBOUlDUmpiMjUwWVdsdVpYSXVZWFIwY2loY0ltUmhkR0V0YzJZdFptbGxiR1F0YVc1d2RYUXRkSGx3WlZ3aUtUdGNibHgwWEhSMllYSWdiV1YwWVZSNWNHVWdQU0FrWTI5dWRHRnBibVZ5TG1GMGRISW9YQ0prWVhSaExYTm1MVzFsZEdFdGRIbHdaVndpS1R0Y2JseHVYSFJjZEhaaGNpQm1hV1ZzWkZaaGJDQTlJRndpWENJN1hHNWNkRngwZG1GeUlDUm1hV1ZzWkR0Y2JseDBYSFIyWVhJZ1ptbGxiR1JPWVcxbElEMGdYQ0pjSWp0Y2JseDBYSFJjYmx4MFhIUnBaaWh0WlhSaFZIbHdaVDA5WENKdWRXMWlaWEpjSWlsY2JseDBYSFI3WEc1Y2RGeDBYSFJwWmlocGJuQjFkRlI1Y0dVOVBWd2ljbUZ1WjJVdGJuVnRZbVZ5WENJcFhHNWNkRngwWEhSN1hHNWNkRngwWEhSY2RDUm1hV1ZzWkNBOUlDUmpiMjUwWVdsdVpYSXVabWx1WkNoY0lpNXpaaTF0WlhSaExYSmhibWRsTFc1MWJXSmxjaUJwYm5CMWRGd2lLVHRjYmx4MFhIUmNkRngwWEc1Y2RGeDBYSFJjZEhaaGNpQjJZV3gxWlhNZ1BTQmJYVHRjYmx4MFhIUmNkRngwSkdacFpXeGtMbVZoWTJnb1puVnVZM1JwYjI0b0tYdGNibHgwWEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwWEhSMllXeDFaWE11Y0hWemFDZ2tLSFJvYVhNcExuWmhiQ2dwS1R0Y2JseDBYSFJjZEZ4MFhHNWNkRngwWEhSY2RIMHBPMXh1WEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwWm1sbGJHUldZV3dnUFNCMllXeDFaWE11YW05cGJpaGNJaXRjSWlrN1hHNWNkRngwWEhSY2RGeHVYSFJjZEZ4MGZWeHVYSFJjZEZ4MFpXeHpaU0JwWmlocGJuQjFkRlI1Y0dVOVBWd2ljbUZ1WjJVdGMyeHBaR1Z5WENJcFhHNWNkRngwWEhSN1hHNWNkRngwWEhSY2RDUm1hV1ZzWkNBOUlDUmpiMjUwWVdsdVpYSXVabWx1WkNoY0lpNXpaaTF0WlhSaExYSmhibWRsTFhOc2FXUmxjaUJwYm5CMWRGd2lLVHRjYmx4MFhIUmNkRngwWEc1Y2RGeDBYSFJjZEM4dloyVjBJR0Z1ZVNCdWRXMWlaWElnWm05eWJXRjBkR2x1WnlCemRIVm1abHh1WEhSY2RGeDBYSFIyWVhJZ0pHMWxkR0ZmY21GdVoyVWdQU0FrWTI5dWRHRnBibVZ5TG1acGJtUW9YQ0l1YzJZdGJXVjBZUzF5WVc1blpTMXpiR2xrWlhKY0lpazdYRzVjZEZ4MFhIUmNkRnh1WEhSY2RGeDBYSFIyWVhJZ1pHVmphVzFoYkY5d2JHRmpaWE1nUFNBa2JXVjBZVjl5WVc1blpTNWhkSFJ5S0Z3aVpHRjBZUzFrWldOcGJXRnNMWEJzWVdObGMxd2lLVHRjYmx4MFhIUmNkRngwZG1GeUlIUm9iM1Z6WVc1a1gzTmxjR1Z5WVhSdmNpQTlJQ1J0WlhSaFgzSmhibWRsTG1GMGRISW9YQ0prWVhSaExYUm9iM1Z6WVc1a0xYTmxjR1Z5WVhSdmNsd2lLVHRjYmx4MFhIUmNkRngwZG1GeUlHUmxZMmx0WVd4ZmMyVndaWEpoZEc5eUlEMGdKRzFsZEdGZmNtRnVaMlV1WVhSMGNpaGNJbVJoZEdFdFpHVmphVzFoYkMxelpYQmxjbUYwYjNKY0lpazdYRzVjYmx4MFhIUmNkRngwZG1GeUlHWnBaV3hrWDJadmNtMWhkQ0E5SUhkT2RXMWlLSHRjYmx4MFhIUmNkRngwWEhSdFlYSnJPaUJrWldOcGJXRnNYM05sY0dWeVlYUnZjaXhjYmx4MFhIUmNkRngwWEhSa1pXTnBiV0ZzY3pvZ2NHRnljMlZHYkc5aGRDaGtaV05wYldGc1gzQnNZV05sY3lrc1hHNWNkRngwWEhSY2RGeDBkR2h2ZFhOaGJtUTZJSFJvYjNWellXNWtYM05sY0dWeVlYUnZjbHh1WEhSY2RGeDBYSFI5S1R0Y2JseDBYSFJjZEZ4MFhHNWNkRngwWEhSY2RIWmhjaUIyWVd4MVpYTWdQU0JiWFR0Y2JseHVYRzVjZEZ4MFhIUmNkSFpoY2lCemJHbGtaWEpmYjJKcVpXTjBJRDBnSkdOdmJuUmhhVzVsY2k1bWFXNWtLRndpTG0xbGRHRXRjMnhwWkdWeVhDSXBXekJkTzF4dVhIUmNkRngwWEhRdkwzWmhiQ0JtY205dElITnNhV1JsY2lCdlltcGxZM1JjYmx4MFhIUmNkRngwZG1GeUlITnNhV1JsY2w5MllXd2dQU0J6Ykdsa1pYSmZiMkpxWldOMExtNXZWV2xUYkdsa1pYSXVaMlYwS0NrN1hHNWNibHgwWEhSY2RGeDBkbUZzZFdWekxuQjFjMmdvWm1sbGJHUmZabTl5YldGMExtWnliMjBvYzJ4cFpHVnlYM1poYkZzd1hTa3BPMXh1WEhSY2RGeDBYSFIyWVd4MVpYTXVjSFZ6YUNobWFXVnNaRjltYjNKdFlYUXVabkp2YlNoemJHbGtaWEpmZG1Gc1d6RmRLU2s3WEc1Y2RGeDBYSFJjZEZ4dVhIUmNkRngwWEhSbWFXVnNaRlpoYkNBOUlIWmhiSFZsY3k1cWIybHVLRndpSzF3aUtUdGNibHgwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkR1pwWld4a1RtRnRaU0E5SUNSdFpYUmhYM0poYm1kbExtRjBkSElvWENKa1lYUmhMWE5tTFdacFpXeGtMVzVoYldWY0lpazdYRzVjZEZ4MFhIUmNkRnh1WEhSY2RGeDBYSFJjYmx4MFhIUmNkSDFjYmx4MFhIUmNkR1ZzYzJVZ2FXWW9hVzV3ZFhSVWVYQmxQVDFjSW5KaGJtZGxMWEpoWkdsdlhDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkQ1JtYVdWc1pDQTlJQ1JqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJaTV6WmkxcGJuQjFkQzF5WVc1blpTMXlZV1JwYjF3aUtUdGNibHgwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkR2xtS0NSbWFXVnNaQzVzWlc1bmRHZzlQVEFwWEc1Y2RGeDBYSFJjZEh0Y2JseDBYSFJjZEZ4MFhIUXZMM1JvWlc0Z2RISjVJR0ZuWVdsdUxDQjNaU0J0ZFhOMElHSmxJSFZ6YVc1bklHRWdjMmx1WjJ4bElHWnBaV3hrWEc1Y2RGeDBYSFJjZEZ4MEpHWnBaV3hrSUQwZ0pHTnZiblJoYVc1bGNpNW1hVzVrS0Z3aVBpQjFiRndpS1R0Y2JseDBYSFJjZEZ4MGZWeHVYRzVjZEZ4MFhIUmNkSFpoY2lBa2JXVjBZVjl5WVc1blpTQTlJQ1JqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJaTV6WmkxdFpYUmhMWEpoYm1kbFhDSXBPMXh1WEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwTHk5MGFHVnlaU0JwY3lCaGJpQmxiR1Z0Wlc1MElIZHBkR2dnWVNCbWNtOXRMM1J2SUdOc1lYTnpJQzBnYzI4Z2QyVWdibVZsWkNCMGJ5Qm5aWFFnZEdobElIWmhiSFZsY3lCdlppQjBhR1VnWm5KdmJTQW1JSFJ2SUdsdWNIVjBJR1pwWld4a2N5QnpaWEJsY21GMFpXeDVYRzVjZEZ4MFhIUmNkR2xtS0NSbWFXVnNaQzVzWlc1bmRHZytNQ2xjYmx4MFhIUmNkRngwZTF4MFhHNWNkRngwWEhSY2RGeDBkbUZ5SUdacFpXeGtYM1poYkhNZ1BTQmJYVHRjYmx4MFhIUmNkRngwWEhSY2JseDBYSFJjZEZ4MFhIUWtabWxsYkdRdVpXRmphQ2htZFc1amRHbHZiaWdwZTF4dVhIUmNkRngwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkRngwWEhSMllYSWdKSEpoWkdsdmN5QTlJQ1FvZEdocGN5a3VabWx1WkNoY0lpNXpaaTFwYm5CMWRDMXlZV1JwYjF3aUtUdGNibHgwWEhSY2RGeDBYSFJjZEdacFpXeGtYM1poYkhNdWNIVnphQ2h6Wld4bUxtZGxkRTFsZEdGU1lXUnBiMVpoYkNna2NtRmthVzl6S1NrN1hHNWNkRngwWEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwWEhSOUtUdGNibHgwWEhSY2RGeDBYSFJjYmx4MFhIUmNkRngwWEhRdkwzQnlaWFpsYm5RZ2MyVmpiMjVrSUc1MWJXSmxjaUJtY205dElHSmxhVzVuSUd4dmQyVnlJSFJvWVc0Z2RHaGxJR1pwY25OMFhHNWNkRngwWEhSY2RGeDBhV1lvWm1sbGJHUmZkbUZzY3k1c1pXNW5kR2c5UFRJcFhHNWNkRngwWEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJjZEZ4MGFXWW9UblZ0WW1WeUtHWnBaV3hrWDNaaGJITmJNVjBwUEU1MWJXSmxjaWhtYVdWc1pGOTJZV3h6V3pCZEtTbGNibHgwWEhSY2RGeDBYSFJjZEh0Y2JseDBYSFJjZEZ4MFhIUmNkRngwWm1sbGJHUmZkbUZzYzFzeFhTQTlJR1pwWld4a1gzWmhiSE5iTUYwN1hHNWNkRngwWEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFJjZEZ4MGZWeHVYSFJjZEZ4MFhIUmNkRnh1WEhSY2RGeDBYSFJjZEdacFpXeGtWbUZzSUQwZ1ptbGxiR1JmZG1Gc2N5NXFiMmx1S0Z3aUsxd2lLVHRjYmx4MFhIUmNkRngwZlZ4dVhIUmNkRngwWEhSY2RGeDBYSFJjZEZ4dVhIUmNkRngwWEhScFppZ2tabWxsYkdRdWJHVnVaM1JvUFQweEtWeHVYSFJjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkRngwWm1sbGJHUk9ZVzFsSUQwZ0pHWnBaV3hrTG1acGJtUW9YQ0l1YzJZdGFXNXdkWFF0Y21Ga2FXOWNJaWt1WVhSMGNpaGNJbTVoYldWY0lpa3VjbVZ3YkdGalpTZ25XMTBuTENBbkp5azdYRzVjZEZ4MFhIUmNkSDFjYmx4MFhIUmNkRngwWld4elpWeHVYSFJjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkRngwWm1sbGJHUk9ZVzFsSUQwZ0pHMWxkR0ZmY21GdVoyVXVZWFIwY2loY0ltUmhkR0V0YzJZdFptbGxiR1F0Ym1GdFpWd2lLVHRjYmx4MFhIUmNkRngwZlZ4dVhHNWNkRngwWEhSOVhHNWNkRngwWEhSbGJITmxJR2xtS0dsdWNIVjBWSGx3WlQwOVhDSnlZVzVuWlMxelpXeGxZM1JjSWlsY2JseDBYSFJjZEh0Y2JseDBYSFJjZEZ4MEpHWnBaV3hrSUQwZ0pHTnZiblJoYVc1bGNpNW1hVzVrS0Z3aUxuTm1MV2x1Y0hWMExYTmxiR1ZqZEZ3aUtUdGNibHgwWEhSY2RGeDBkbUZ5SUNSdFpYUmhYM0poYm1kbElEMGdKR052Ym5SaGFXNWxjaTVtYVc1a0tGd2lMbk5tTFcxbGRHRXRjbUZ1WjJWY0lpazdYRzVjZEZ4MFhIUmNkRnh1WEhSY2RGeDBYSFF2TDNSb1pYSmxJR2x6SUdGdUlHVnNaVzFsYm5RZ2QybDBhQ0JoSUdaeWIyMHZkRzhnWTJ4aGMzTWdMU0J6YnlCM1pTQnVaV1ZrSUhSdklHZGxkQ0IwYUdVZ2RtRnNkV1Z6SUc5bUlIUm9aU0JtY205dElDWWdkRzhnYVc1d2RYUWdabWxsYkdSeklITmxjR1Z5WVhSbGJIbGNibHgwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkR2xtS0NSbWFXVnNaQzVzWlc1bmRHZytNQ2xjYmx4MFhIUmNkRngwZTF4dVhIUmNkRngwWEhSY2RIWmhjaUJtYVdWc1pGOTJZV3h6SUQwZ1cxMDdYRzVjZEZ4MFhIUmNkRngwWEc1Y2RGeDBYSFJjZEZ4MEpHWnBaV3hrTG1WaFkyZ29ablZ1WTNScGIyNG9LWHRjYmx4MFhIUmNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUmNkRngwZG1GeUlDUjBhR2x6SUQwZ0pDaDBhR2x6S1R0Y2JseDBYSFJjZEZ4MFhIUmNkR1pwWld4a1gzWmhiSE11Y0hWemFDaHpaV3htTG1kbGRFMWxkR0ZUWld4bFkzUldZV3dvSkhSb2FYTXBLVHRjYmx4MFhIUmNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUmNkSDBwTzF4dVhIUmNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUmNkQzh2Y0hKbGRtVnVkQ0J6WldOdmJtUWdiblZ0WW1WeUlHWnliMjBnWW1WcGJtY2diRzkzWlhJZ2RHaGhiaUIwYUdVZ1ptbHljM1JjYmx4MFhIUmNkRngwWEhScFppaG1hV1ZzWkY5MllXeHpMbXhsYm1kMGFEMDlNaWxjYmx4MFhIUmNkRngwWEhSN1hHNWNkRngwWEhSY2RGeDBYSFJwWmloT2RXMWlaWElvWm1sbGJHUmZkbUZzYzFzeFhTazhUblZ0WW1WeUtHWnBaV3hrWDNaaGJITmJNRjBwS1Z4dVhIUmNkRngwWEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJjZEZ4MFhIUm1hV1ZzWkY5MllXeHpXekZkSUQwZ1ptbGxiR1JmZG1Gc2Mxc3dYVHRjYmx4MFhIUmNkRngwWEhSY2RIMWNibHgwWEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFJjZEZ4MFhHNWNkRngwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkRngwWm1sbGJHUldZV3dnUFNCbWFXVnNaRjkyWVd4ekxtcHZhVzRvWENJclhDSXBPMXh1WEhSY2RGeDBYSFI5WEc1Y2RGeDBYSFJjZEZ4MFhIUmNkRngwWEc1Y2RGeDBYSFJjZEdsbUtDUm1hV1ZzWkM1c1pXNW5kR2c5UFRFcFhHNWNkRngwWEhSY2RIdGNibHgwWEhSY2RGeDBYSFJtYVdWc1pFNWhiV1VnUFNBa1ptbGxiR1F1WVhSMGNpaGNJbTVoYldWY0lpa3VjbVZ3YkdGalpTZ25XMTBuTENBbkp5azdYRzVjZEZ4MFhIUmNkSDFjYmx4MFhIUmNkRngwWld4elpWeHVYSFJjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkRngwWm1sbGJHUk9ZVzFsSUQwZ0pHMWxkR0ZmY21GdVoyVXVZWFIwY2loY0ltUmhkR0V0YzJZdFptbGxiR1F0Ym1GdFpWd2lLVHRjYmx4MFhIUmNkRngwZlZ4dVhIUmNkRngwWEhSY2JseDBYSFJjZEgxY2JseDBYSFJjZEdWc2MyVWdhV1lvYVc1d2RYUlVlWEJsUFQxY0luSmhibWRsTFdOb1pXTnJZbTk0WENJcFhHNWNkRngwWEhSN1hHNWNkRngwWEhSY2RDUm1hV1ZzWkNBOUlDUmpiMjUwWVdsdVpYSXVabWx1WkNoY0luVnNJRDRnYkdrZ2FXNXdkWFE2WTJobFkydGliM2hjSWlrN1hHNWNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUnBaaWdrWm1sbGJHUXViR1Z1WjNSb1BqQXBYRzVjZEZ4MFhIUmNkSHRjYmx4MFhIUmNkRngwWEhSbWFXVnNaRlpoYkNBOUlITmxiR1l1WjJWMFEyaGxZMnRpYjNoV1lXd29KR1pwWld4a0xDQmNJbUZ1WkZ3aUtUdGNibHgwWEhSY2RGeDBmVnh1WEhSY2RGeDBmVnh1WEhSY2RGeDBYRzVjZEZ4MFhIUnBaaWhtYVdWc1pFNWhiV1U5UFZ3aVhDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkR1pwWld4a1RtRnRaU0E5SUNSbWFXVnNaQzVoZEhSeUtGd2libUZ0WlZ3aUtTNXlaWEJzWVdObEtDZGJYU2NzSUNjbktUdGNibHgwWEhSY2RIMWNibHgwWEhSOVhHNWNkRngwWld4elpTQnBaaWh0WlhSaFZIbHdaVDA5WENKamFHOXBZMlZjSWlsY2JseDBYSFI3WEc1Y2RGeDBYSFJwWmlocGJuQjFkRlI1Y0dVOVBWd2ljMlZzWldOMFhDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkQ1JtYVdWc1pDQTlJQ1JqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJbk5sYkdWamRGd2lLVHRjYmx4MFhIUmNkRngwWEc1Y2RGeDBYSFJjZEdacFpXeGtWbUZzSUQwZ2MyVnNaaTVuWlhSTlpYUmhVMlZzWldOMFZtRnNLQ1JtYVdWc1pDazdJRnh1WEhSY2RGeDBYSFJjYmx4MFhIUmNkSDFjYmx4MFhIUmNkR1ZzYzJVZ2FXWW9hVzV3ZFhSVWVYQmxQVDFjSW0xMWJIUnBjMlZzWldOMFhDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkQ1JtYVdWc1pDQTlJQ1JqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJbk5sYkdWamRGd2lLVHRjYmx4MFhIUmNkRngwZG1GeUlHOXdaWEpoZEc5eUlEMGdKR1pwWld4a0xtRjBkSElvWENKa1lYUmhMVzl3WlhKaGRHOXlYQ0lwTzF4dVhIUmNkRngwWEhSY2JseDBYSFJjZEZ4MFptbGxiR1JXWVd3Z1BTQnpaV3htTG1kbGRFMWxkR0ZOZFd4MGFWTmxiR1ZqZEZaaGJDZ2tabWxsYkdRc0lHOXdaWEpoZEc5eUtUdGNibHgwWEhSY2RIMWNibHgwWEhSY2RHVnNjMlVnYVdZb2FXNXdkWFJVZVhCbFBUMWNJbU5vWldOclltOTRYQ0lwWEc1Y2RGeDBYSFI3WEc1Y2RGeDBYSFJjZENSbWFXVnNaQ0E5SUNSamIyNTBZV2x1WlhJdVptbHVaQ2hjSW5Wc0lENGdiR2tnYVc1d2RYUTZZMmhsWTJ0aWIzaGNJaWs3WEc1Y2RGeDBYSFJjZEZ4dVhIUmNkRngwWEhScFppZ2tabWxsYkdRdWJHVnVaM1JvUGpBcFhHNWNkRngwWEhSY2RIdGNibHgwWEhSY2RGeDBYSFIyWVhJZ2IzQmxjbUYwYjNJZ1BTQWtZMjl1ZEdGcGJtVnlMbVpwYm1Rb1hDSStJSFZzWENJcExtRjBkSElvWENKa1lYUmhMVzl3WlhKaGRHOXlYQ0lwTzF4dVhIUmNkRngwWEhSY2RHWnBaV3hrVm1Gc0lEMGdjMlZzWmk1blpYUk5aWFJoUTJobFkydGliM2hXWVd3b0pHWnBaV3hrTENCdmNHVnlZWFJ2Y2lrN1hHNWNkRngwWEhSY2RIMWNibHgwWEhSY2RIMWNibHgwWEhSY2RHVnNjMlVnYVdZb2FXNXdkWFJVZVhCbFBUMWNJbkpoWkdsdlhDSXBYRzVjZEZ4MFhIUjdYRzVjZEZ4MFhIUmNkQ1JtYVdWc1pDQTlJQ1JqYjI1MFlXbHVaWEl1Wm1sdVpDaGNJblZzSUQ0Z2JHa2dhVzV3ZFhRNmNtRmthVzljSWlrN1hHNWNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUnBaaWdrWm1sbGJHUXViR1Z1WjNSb1BqQXBYRzVjZEZ4MFhIUmNkSHRjYmx4MFhIUmNkRngwWEhSbWFXVnNaRlpoYkNBOUlITmxiR1l1WjJWMFRXVjBZVkpoWkdsdlZtRnNLQ1JtYVdWc1pDazdYRzVjZEZ4MFhIUmNkSDFjYmx4MFhIUmNkSDFjYmx4MFhIUmNkRnh1WEhSY2RGeDBabWxsYkdSV1lXd2dQU0JsYm1OdlpHVlZVa2xEYjIxd2IyNWxiblFvWm1sbGJHUldZV3dwTzF4dVhIUmNkRngwYVdZb2RIbHdaVzltS0NSbWFXVnNaQ2toUFQxY0luVnVaR1ZtYVc1bFpGd2lLVnh1WEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJwWmlna1ptbGxiR1F1YkdWdVozUm9QakFwWEc1Y2RGeDBYSFJjZEh0Y2JseDBYSFJjZEZ4MFhIUm1hV1ZzWkU1aGJXVWdQU0FrWm1sbGJHUXVZWFIwY2loY0ltNWhiV1ZjSWlrdWNtVndiR0ZqWlNnblcxMG5MQ0FuSnlrN1hHNWNkRngwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkRngwTHk5bWIzSWdkR2h2YzJVZ2QyaHZJR2x1YzJsemRDQnZiaUIxYzJsdVp5QW1JR0Z0Y0dWeWMyRnVaSE1nYVc0Z2RHaGxJRzVoYldVZ2IyWWdkR2hsSUdOMWMzUnZiU0JtYVdWc1pDQW9JU2xjYmx4MFhIUmNkRngwWEhSbWFXVnNaRTVoYldVZ1BTQW9abWxsYkdST1lXMWxLVHRjYmx4MFhIUmNkRngwZlZ4dVhIUmNkRngwZlZ4dVhIUmNkRngwWEc1Y2RGeDBmVnh1WEhSY2RHVnNjMlVnYVdZb2JXVjBZVlI1Y0dVOVBWd2laR0YwWlZ3aUtWeHVYSFJjZEh0Y2JseDBYSFJjZEhObGJHWXVjSEp2WTJWemMxQnZjM1JFWVhSbEtDUmpiMjUwWVdsdVpYSXBPMXh1WEhSY2RIMWNibHgwWEhSY2JseDBYSFJwWmloMGVYQmxiMllvWm1sbGJHUldZV3dwSVQxY0luVnVaR1ZtYVc1bFpGd2lLVnh1WEhSY2RIdGNibHgwWEhSY2RHbG1LR1pwWld4a1ZtRnNJVDFjSWx3aUtWeHVYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUXZMM05sYkdZdWRYSnNYMk52YlhCdmJtVnVkSE1nS3owZ1hDSW1YQ0lyWlc1amIyUmxWVkpKUTI5dGNHOXVaVzUwS0dacFpXeGtUbUZ0WlNrclhDSTlYQ0lyS0dacFpXeGtWbUZzS1R0Y2JseDBYSFJjZEZ4MGMyVnNaaTUxY214ZmNHRnlZVzF6VzJWdVkyOWtaVlZTU1VOdmJYQnZibVZ1ZENobWFXVnNaRTVoYldVcFhTQTlJQ2htYVdWc1pGWmhiQ2s3WEc1Y2RGeDBYSFI5WEc1Y2RGeDBmVnh1WEhSOUxGeHVYSFJ3Y205alpYTnpVRzl6ZEVSaGRHVTZJR1oxYm1OMGFXOXVLQ1JqYjI1MFlXbHVaWElwWEc1Y2RIdGNibHgwWEhSMllYSWdjMlZzWmlBOUlIUm9hWE03WEc1Y2RGeDBYRzVjZEZ4MGRtRnlJR1pwWld4a1ZIbHdaU0E5SUNSamIyNTBZV2x1WlhJdVlYUjBjaWhjSW1SaGRHRXRjMll0Wm1sbGJHUXRkSGx3WlZ3aUtUdGNibHgwWEhSMllYSWdhVzV3ZFhSVWVYQmxJRDBnSkdOdmJuUmhhVzVsY2k1aGRIUnlLRndpWkdGMFlTMXpaaTFtYVdWc1pDMXBibkIxZEMxMGVYQmxYQ0lwTzF4dVhIUmNkRnh1WEhSY2RIWmhjaUFrWm1sbGJHUTdYRzVjZEZ4MGRtRnlJR1pwWld4a1RtRnRaU0E5SUZ3aVhDSTdYRzVjZEZ4MGRtRnlJR1pwWld4a1ZtRnNJRDBnWENKY0lqdGNibHgwWEhSY2JseDBYSFFrWm1sbGJHUWdQU0FrWTI5dWRHRnBibVZ5TG1acGJtUW9YQ0oxYkNBK0lHeHBJR2x1Y0hWME9uUmxlSFJjSWlrN1hHNWNkRngwWm1sbGJHUk9ZVzFsSUQwZ0pHWnBaV3hrTG1GMGRISW9YQ0p1WVcxbFhDSXBMbkpsY0d4aFkyVW9KMXRkSnl3Z0p5Y3BPMXh1WEhSY2RGeHVYSFJjZEhaaGNpQmtZWFJsY3lBOUlGdGRPMXh1WEhSY2RDUm1hV1ZzWkM1bFlXTm9LR1oxYm1OMGFXOXVLQ2w3WEc1Y2RGeDBYSFJjYmx4MFhIUmNkR1JoZEdWekxuQjFjMmdvSkNoMGFHbHpLUzUyWVd3b0tTazdYRzVjZEZ4MFhHNWNkRngwZlNrN1hHNWNkRngwWEc1Y2RGeDBhV1lvSkdacFpXeGtMbXhsYm1kMGFEMDlNaWxjYmx4MFhIUjdYRzVjZEZ4MFhIUnBaaWdvWkdGMFpYTmJNRjBoUFZ3aVhDSXBmSHdvWkdGMFpYTmJNVjBoUFZ3aVhDSXBLVnh1WEhSY2RGeDBlMXh1WEhSY2RGeDBYSFJtYVdWc1pGWmhiQ0E5SUdSaGRHVnpMbXB2YVc0b1hDSXJYQ0lwTzF4dVhIUmNkRngwWEhSbWFXVnNaRlpoYkNBOUlHWnBaV3hrVm1Gc0xuSmxjR3hoWTJVb0wxeGNMeTluTENjbktUdGNibHgwWEhSY2RIMWNibHgwWEhSOVhHNWNkRngwWld4elpTQnBaaWdrWm1sbGJHUXViR1Z1WjNSb1BUMHhLVnh1WEhSY2RIdGNibHgwWEhSY2RHbG1LR1JoZEdWeld6QmRJVDFjSWx3aUtWeHVYSFJjZEZ4MGUxeHVYSFJjZEZ4MFhIUm1hV1ZzWkZaaGJDQTlJR1JoZEdWekxtcHZhVzRvWENJclhDSXBPMXh1WEhSY2RGeDBYSFJtYVdWc1pGWmhiQ0E5SUdacFpXeGtWbUZzTG5KbGNHeGhZMlVvTDF4Y0x5OW5MQ2NuS1R0Y2JseDBYSFJjZEgxY2JseDBYSFI5WEc1Y2RGeDBYRzVjZEZ4MGFXWW9kSGx3Wlc5bUtHWnBaV3hrVm1Gc0tTRTlYQ0oxYm1SbFptbHVaV1JjSWlsY2JseDBYSFI3WEc1Y2RGeDBYSFJwWmlobWFXVnNaRlpoYkNFOVhDSmNJaWxjYmx4MFhIUmNkSHRjYmx4MFhIUmNkRngwZG1GeUlHWnBaV3hrVTJ4MVp5QTlJRndpWENJN1hHNWNkRngwWEhSY2RGeHVYSFJjZEZ4MFhIUnBaaWhtYVdWc1pFNWhiV1U5UFZ3aVgzTm1YM0J2YzNSZlpHRjBaVndpS1Z4dVhIUmNkRngwWEhSN1hHNWNkRngwWEhSY2RGeDBabWxsYkdSVGJIVm5JRDBnWENKd2IzTjBYMlJoZEdWY0lqdGNibHgwWEhSY2RGeDBmVnh1WEhSY2RGeDBYSFJsYkhObFhHNWNkRngwWEhSY2RIdGNibHgwWEhSY2RGeDBYSFJtYVdWc1pGTnNkV2NnUFNCbWFXVnNaRTVoYldVN1hHNWNkRngwWEhSY2RIMWNibHgwWEhSY2RGeDBYRzVjZEZ4MFhIUmNkR2xtS0dacFpXeGtVMngxWnlFOVhDSmNJaWxjYmx4MFhIUmNkRngwZTF4dVhIUmNkRngwWEhSY2RDOHZjMlZzWmk1MWNteGZZMjl0Y0c5dVpXNTBjeUFyUFNCY0lpWmNJaXRtYVdWc1pGTnNkV2NyWENJOVhDSXJabWxsYkdSV1lXdzdYRzVjZEZ4MFhIUmNkRngwYzJWc1ppNTFjbXhmY0dGeVlXMXpXMlpwWld4a1UyeDFaMTBnUFNCbWFXVnNaRlpoYkR0Y2JseDBYSFJjZEZ4MGZWeHVYSFJjZEZ4MGZWeHVYSFJjZEgxY2JseDBYSFJjYmx4MGZTeGNibHgwY0hKdlkyVnpjMVJoZUc5dWIyMTVPaUJtZFc1amRHbHZiaWdrWTI5dWRHRnBibVZ5TENCeVpYUjFjbTVmYjJKcVpXTjBLVnh1WEhSN1hHNGdJQ0FnSUNBZ0lHbG1LSFI1Y0dWdlppaHlaWFIxY201ZmIySnFaV04wS1QwOVhDSjFibVJsWm1sdVpXUmNJaWxjYmlBZ0lDQWdJQ0FnZTF4dUlDQWdJQ0FnSUNBZ0lDQWdjbVYwZFhKdVgyOWlhbVZqZENBOUlHWmhiSE5sTzF4dUlDQWdJQ0FnSUNCOVhHNWNibHgwWEhRdkwybG1LQ2xjZEZ4MFhIUmNkRngwWEc1Y2RGeDBMeTkyWVhJZ1ptbGxiR1JPWVcxbElEMGdKQ2gwYUdsektTNWhkSFJ5S0Z3aVpHRjBZUzF6WmkxbWFXVnNaQzF1WVcxbFhDSXBPMXh1WEhSY2RIWmhjaUJ6Wld4bUlEMGdkR2hwY3p0Y2JseDBYRzVjZEZ4MGRtRnlJR1pwWld4a1ZIbHdaU0E5SUNSamIyNTBZV2x1WlhJdVlYUjBjaWhjSW1SaGRHRXRjMll0Wm1sbGJHUXRkSGx3WlZ3aUtUdGNibHgwWEhSMllYSWdhVzV3ZFhSVWVYQmxJRDBnSkdOdmJuUmhhVzVsY2k1aGRIUnlLRndpWkdGMFlTMXpaaTFtYVdWc1pDMXBibkIxZEMxMGVYQmxYQ0lwTzF4dVhIUmNkRnh1WEhSY2RIWmhjaUFrWm1sbGJHUTdYRzVjZEZ4MGRtRnlJR1pwWld4a1RtRnRaU0E5SUZ3aVhDSTdYRzVjZEZ4MGRtRnlJR1pwWld4a1ZtRnNJRDBnWENKY0lqdGNibHgwWEhSY2JseDBYSFJwWmlocGJuQjFkRlI1Y0dVOVBWd2ljMlZzWldOMFhDSXBYRzVjZEZ4MGUxeHVYSFJjZEZ4MEpHWnBaV3hrSUQwZ0pHTnZiblJoYVc1bGNpNW1hVzVrS0Z3aWMyVnNaV04wWENJcE8xeHVYSFJjZEZ4MFptbGxiR1JPWVcxbElEMGdKR1pwWld4a0xtRjBkSElvWENKdVlXMWxYQ0lwTG5KbGNHeGhZMlVvSjF0ZEp5d2dKeWNwTzF4dVhIUmNkRngwWEc1Y2RGeDBYSFJtYVdWc1pGWmhiQ0E5SUhObGJHWXVaMlYwVTJWc1pXTjBWbUZzS0NSbWFXVnNaQ2s3SUZ4dVhIUmNkSDFjYmx4MFhIUmxiSE5sSUdsbUtHbHVjSFYwVkhsd1pUMDlYQ0p0ZFd4MGFYTmxiR1ZqZEZ3aUtWeHVYSFJjZEh0Y2JseDBYSFJjZENSbWFXVnNaQ0E5SUNSamIyNTBZV2x1WlhJdVptbHVaQ2hjSW5ObGJHVmpkRndpS1R0Y2JseDBYSFJjZEdacFpXeGtUbUZ0WlNBOUlDUm1hV1ZzWkM1aGRIUnlLRndpYm1GdFpWd2lLUzV5WlhCc1lXTmxLQ2RiWFNjc0lDY25LVHRjYmx4MFhIUmNkSFpoY2lCdmNHVnlZWFJ2Y2lBOUlDUm1hV1ZzWkM1aGRIUnlLRndpWkdGMFlTMXZjR1Z5WVhSdmNsd2lLVHRjYmx4MFhIUmNkRnh1WEhSY2RGeDBabWxsYkdSV1lXd2dQU0J6Wld4bUxtZGxkRTExYkhScFUyVnNaV04wVm1Gc0tDUm1hV1ZzWkN3Z2IzQmxjbUYwYjNJcE8xeHVYSFJjZEgxY2JseDBYSFJsYkhObElHbG1LR2x1Y0hWMFZIbHdaVDA5WENKamFHVmphMkp2ZUZ3aUtWeHVYSFJjZEh0Y2JseDBYSFJjZENSbWFXVnNaQ0E5SUNSamIyNTBZV2x1WlhJdVptbHVaQ2hjSW5Wc0lENGdiR2tnYVc1d2RYUTZZMmhsWTJ0aWIzaGNJaWs3WEc1Y2RGeDBYSFJwWmlna1ptbGxiR1F1YkdWdVozUm9QakFwWEc1Y2RGeDBYSFI3WEc1Y2RGeDBYSFJjZEdacFpXeGtUbUZ0WlNBOUlDUm1hV1ZzWkM1aGRIUnlLRndpYm1GdFpWd2lLUzV5WlhCc1lXTmxLQ2RiWFNjc0lDY25LVHRjYmx4MFhIUmNkRngwWEhSY2RGeDBYSFJjZEZ4MFhHNWNkRngwWEhSY2RIWmhjaUJ2Y0dWeVlYUnZjaUE5SUNSamIyNTBZV2x1WlhJdVptbHVaQ2hjSWo0Z2RXeGNJaWt1WVhSMGNpaGNJbVJoZEdFdGIzQmxjbUYwYjNKY0lpazdYRzVjZEZ4MFhIUmNkR1pwWld4a1ZtRnNJRDBnYzJWc1ppNW5aWFJEYUdWamEySnZlRlpoYkNna1ptbGxiR1FzSUc5d1pYSmhkRzl5S1R0Y2JseDBYSFJjZEgxY2JseDBYSFI5WEc1Y2RGeDBaV3h6WlNCcFppaHBibkIxZEZSNWNHVTlQVndpY21Ga2FXOWNJaWxjYmx4MFhIUjdYRzVjZEZ4MFhIUWtabWxsYkdRZ1BTQWtZMjl1ZEdGcGJtVnlMbVpwYm1Rb1hDSjFiQ0ErSUd4cElHbHVjSFYwT25KaFpHbHZYQ0lwTzF4dVhIUmNkRngwYVdZb0pHWnBaV3hrTG14bGJtZDBhRDR3S1Z4dVhIUmNkRngwZTF4dVhIUmNkRngwWEhSbWFXVnNaRTVoYldVZ1BTQWtabWxsYkdRdVlYUjBjaWhjSW01aGJXVmNJaWt1Y21Wd2JHRmpaU2duVzEwbkxDQW5KeWs3WEc1Y2RGeDBYSFJjZEZ4dVhIUmNkRngwWEhSbWFXVnNaRlpoYkNBOUlITmxiR1l1WjJWMFVtRmthVzlXWVd3b0pHWnBaV3hrS1R0Y2JseDBYSFJjZEgxY2JseDBYSFI5WEc1Y2RGeDBYRzVjZEZ4MGFXWW9kSGx3Wlc5bUtHWnBaV3hrVm1Gc0tTRTlYQ0oxYm1SbFptbHVaV1JjSWlsY2JseDBYSFI3WEc1Y2RGeDBYSFJwWmlobWFXVnNaRlpoYkNFOVhDSmNJaWxjYmx4MFhIUmNkSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0JwWmloeVpYUjFjbTVmYjJKcVpXTjBQVDEwY25WbEtWeHVJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lIdGNiaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnY21WMGRYSnVJSHR1WVcxbE9pQm1hV1ZzWkU1aGJXVXNJSFpoYkhWbE9pQm1hV1ZzWkZaaGJIMDdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJR1ZzYzJWY2JpQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCN1hHNGdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQzh2YzJWc1ppNTFjbXhmWTI5dGNHOXVaVzUwY3lBclBTQmNJaVpjSWl0bWFXVnNaRTVoYldVclhDSTlYQ0lyWm1sbGJHUldZV3c3WEc0Z0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lITmxiR1l1ZFhKc1gzQmhjbUZ0YzF0bWFXVnNaRTVoYldWZElEMGdabWxsYkdSV1lXdzdYRzRnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdmVnh1WEc1Y2RGeDBYSFI5WEc1Y2RGeDBmVnh1WEc0Z0lDQWdJQ0FnSUdsbUtISmxkSFZ5Ymw5dlltcGxZM1E5UFhSeWRXVXBYRzRnSUNBZ0lDQWdJSHRjYmlBZ0lDQWdJQ0FnSUNBZ0lISmxkSFZ5YmlCbVlXeHpaVHRjYmlBZ0lDQWdJQ0FnZlZ4dVhIUjlYRzU5T3lKZGZRPT0iLCJcbm1vZHVsZS5leHBvcnRzID0ge1xuXHRcblx0c2VhcmNoRm9ybXM6IHt9LFxuXHRcblx0aW5pdDogZnVuY3Rpb24oKXtcblx0XHRcblx0XHRcblx0fSxcblx0YWRkU2VhcmNoRm9ybTogZnVuY3Rpb24oaWQsIG9iamVjdCl7XG5cdFx0XG5cdFx0dGhpcy5zZWFyY2hGb3Jtc1tpZF0gPSBvYmplY3Q7XG5cdH0sXG5cdGdldFNlYXJjaEZvcm06IGZ1bmN0aW9uKGlkKVxuXHR7XG5cdFx0cmV0dXJuIHRoaXMuc2VhcmNoRm9ybXNbaWRdO1x0XG5cdH1cblx0XG59OyIsIihmdW5jdGlvbiAoZ2xvYmFsKXtcblxudmFyICQgXHRcdFx0XHQ9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCk7XG5cbm1vZHVsZS5leHBvcnRzID0ge1xuXHRcblx0aW5pdDogZnVuY3Rpb24oKXtcblx0XHQkKGRvY3VtZW50KS5vbihcInNmOmFqYXhmaW5pc2hcIiwgXCIuc2VhcmNoYW5kZmlsdGVyXCIsIGZ1bmN0aW9uKCBlLCBkYXRhICkge1xuXHRcdFx0dmFyIGRpc3BsYXlfbWV0aG9kID0gZGF0YS5vYmplY3QuZGlzcGxheV9yZXN1bHRfbWV0aG9kO1xuXHRcdFx0aWYgKCBkaXNwbGF5X21ldGhvZCA9PT0gJ2N1c3RvbV9lZGRfc3RvcmUnICkge1xuXHRcdFx0XHQkKCdpbnB1dC5lZGQtYWRkLXRvLWNhcnQnKS5jc3MoJ2Rpc3BsYXknLCBcIm5vbmVcIik7XG5cdFx0XHRcdCQoJ2EuZWRkLWFkZC10by1jYXJ0JykuYWRkQ2xhc3MoJ2VkZC1oYXMtanMnKTtcblx0XHRcdH0gZWxzZSBpZiAoIGRpc3BsYXlfbWV0aG9kID09PSAnY3VzdG9tX2xheW91dHMnICkge1xuXHRcdFx0XHRpZiAoICQoJy5jbC1sYXlvdXQnKS5oYXNDbGFzcyggJ2NsLWxheW91dC0tbWFzb25yeScgKSApIHtcblx0XHRcdFx0XHQvL3RoZW4gcmUtaW5pdCBtYXNvbnJ5XG5cdFx0XHRcdFx0Y29uc3QgbWFzb25yeUNvbnRhaW5lciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoICcuY2wtbGF5b3V0LS1tYXNvbnJ5JyApO1xuXHRcdFx0XHRcdGlmICggbWFzb25yeUNvbnRhaW5lci5sZW5ndGggPiAwICkge1xuXHRcdFx0XHRcdFx0Y29uc3QgY3VzdG9tTGF5b3V0R3JpZCA9IG5ldyBNYXNvbnJ5KCAnLmNsLWxheW91dC0tbWFzb25yeScsIHtcblx0XHRcdFx0XHRcdFx0Ly8gb3B0aW9ucy4uLlxuXHRcdFx0XHRcdFx0XHRpdGVtU2VsZWN0b3I6ICcuY2wtbGF5b3V0X19pdGVtJyxcblx0XHRcdFx0XHRcdFx0Ly9jb2x1bW5XaWR0aDogMzE5XG5cdFx0XHRcdFx0XHRcdHBlcmNlbnRQb3NpdGlvbjogdHJ1ZSxcblx0XHRcdFx0XHRcdFx0Ly9ndXR0ZXI6IDEwLFxuXHRcdFx0XHRcdFx0XHR0cmFuc2l0aW9uRHVyYXRpb246IDAsXG5cdFx0XHRcdFx0XHR9ICk7XG5cdFx0XHRcdFx0XHRpbWFnZXNMb2FkZWQoIG1hc29ucnlDb250YWluZXIgKS5vbiggJ3Byb2dyZXNzJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdGN1c3RvbUxheW91dEdyaWQubGF5b3V0KCk7XG5cdFx0XHRcdFx0XHR9ICk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH0sXG5cbn07XG59KS5jYWxsKHRoaXMsdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbCA6IHR5cGVvZiBzZWxmICE9PSBcInVuZGVmaW5lZFwiID8gc2VsZiA6IHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3cgOiB7fSlcbi8vIyBzb3VyY2VNYXBwaW5nVVJMPWRhdGE6YXBwbGljYXRpb24vanNvbjtjaGFyc2V0OnV0Zi04O2Jhc2U2NCxleUoyWlhKemFXOXVJam96TENKemIzVnlZMlZ6SWpwYkluTnlZeTl3ZFdKc2FXTXZZWE56WlhSekwycHpMMmx1WTJ4MVpHVnpMM1JvYVhKa2NHRnlkSGt1YW5NaVhTd2libUZ0WlhNaU9sdGRMQ0p0WVhCd2FXNW5jeUk2SWp0QlFVRkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CTzBGQlEwRTdRVUZEUVR0QlFVTkJPMEZCUTBFN1FVRkRRVHRCUVVOQk8wRkJRMEU3UVVGRFFUdEJRVU5CSWl3aVptbHNaU0k2SW1kbGJtVnlZWFJsWkM1cWN5SXNJbk52ZFhKalpWSnZiM1FpT2lJaUxDSnpiM1Z5WTJWelEyOXVkR1Z1ZENJNld5SmNiblpoY2lBa0lGeDBYSFJjZEZ4MFBTQW9kSGx3Wlc5bUlIZHBibVJ2ZHlBaFBUMGdYQ0oxYm1SbFptbHVaV1JjSWlBL0lIZHBibVJ2ZDFzbmFsRjFaWEo1SjEwZ09pQjBlWEJsYjJZZ1oyeHZZbUZzSUNFOVBTQmNJblZ1WkdWbWFXNWxaRndpSUQ4Z1oyeHZZbUZzV3lkcVVYVmxjbmtuWFNBNklHNTFiR3dwTzF4dVhHNXRiMlIxYkdVdVpYaHdiM0owY3lBOUlIdGNibHgwWEc1Y2RHbHVhWFE2SUdaMWJtTjBhVzl1S0NsN1hHNWNkRngwSkNoa2IyTjFiV1Z1ZENrdWIyNG9YQ0p6WmpwaGFtRjRabWx1YVhOb1hDSXNJRndpTG5ObFlYSmphR0Z1WkdacGJIUmxjbHdpTENCbWRXNWpkR2x2YmlnZ1pTd2daR0YwWVNBcElIdGNibHgwWEhSY2RIWmhjaUJrYVhOd2JHRjVYMjFsZEdodlpDQTlJR1JoZEdFdWIySnFaV04wTG1ScGMzQnNZWGxmY21WemRXeDBYMjFsZEdodlpEdGNibHgwWEhSY2RHbG1JQ2dnWkdsemNHeGhlVjl0WlhSb2IyUWdQVDA5SUNkamRYTjBiMjFmWldSa1gzTjBiM0psSnlBcElIdGNibHgwWEhSY2RGeDBKQ2duYVc1d2RYUXVaV1JrTFdGa1pDMTBieTFqWVhKMEp5a3VZM056S0Nka2FYTndiR0Y1Snl3Z1hDSnViMjVsWENJcE8xeHVYSFJjZEZ4MFhIUWtLQ2RoTG1Wa1pDMWhaR1F0ZEc4dFkyRnlkQ2NwTG1Ga1pFTnNZWE56S0NkbFpHUXRhR0Z6TFdwekp5azdYRzVjZEZ4MFhIUjlJR1ZzYzJVZ2FXWWdLQ0JrYVhOd2JHRjVYMjFsZEdodlpDQTlQVDBnSjJOMWMzUnZiVjlzWVhsdmRYUnpKeUFwSUh0Y2JseDBYSFJjZEZ4MGFXWWdLQ0FrS0NjdVkyd3RiR0Y1YjNWMEp5a3VhR0Z6UTJ4aGMzTW9JQ2RqYkMxc1lYbHZkWFF0TFcxaGMyOXVjbmtuSUNrZ0tTQjdYRzVjZEZ4MFhIUmNkRngwTHk5MGFHVnVJSEpsTFdsdWFYUWdiV0Z6YjI1eWVWeHVYSFJjZEZ4MFhIUmNkR052Ym5OMElHMWhjMjl1Y25sRGIyNTBZV2x1WlhJZ1BTQmtiMk4xYldWdWRDNXhkV1Z5ZVZObGJHVmpkRzl5UVd4c0tDQW5MbU5zTFd4aGVXOTFkQzB0YldGemIyNXllU2NnS1R0Y2JseDBYSFJjZEZ4MFhIUnBaaUFvSUcxaGMyOXVjbmxEYjI1MFlXbHVaWEl1YkdWdVozUm9JRDRnTUNBcElIdGNibHgwWEhSY2RGeDBYSFJjZEdOdmJuTjBJR04xYzNSdmJVeGhlVzkxZEVkeWFXUWdQU0J1WlhjZ1RXRnpiMjV5ZVNnZ0p5NWpiQzFzWVhsdmRYUXRMVzFoYzI5dWNua25MQ0I3WEc1Y2RGeDBYSFJjZEZ4MFhIUmNkQzh2SUc5d2RHbHZibk11TGk1Y2JseDBYSFJjZEZ4MFhIUmNkRngwYVhSbGJWTmxiR1ZqZEc5eU9pQW5MbU5zTFd4aGVXOTFkRjlmYVhSbGJTY3NYRzVjZEZ4MFhIUmNkRngwWEhSY2RDOHZZMjlzZFcxdVYybGtkR2c2SURNeE9WeHVYSFJjZEZ4MFhIUmNkRngwWEhSd1pYSmpaVzUwVUc5emFYUnBiMjQ2SUhSeWRXVXNYRzVjZEZ4MFhIUmNkRngwWEhSY2RDOHZaM1YwZEdWeU9pQXhNQ3hjYmx4MFhIUmNkRngwWEhSY2RGeDBkSEpoYm5OcGRHbHZia1IxY21GMGFXOXVPaUF3TEZ4dVhIUmNkRngwWEhSY2RGeDBmU0FwTzF4dVhIUmNkRngwWEhSY2RGeDBhVzFoWjJWelRHOWhaR1ZrS0NCdFlYTnZibko1UTI5dWRHRnBibVZ5SUNrdWIyNG9JQ2R3Y205bmNtVnpjeWNzSUdaMWJtTjBhVzl1S0NrZ2UxeHVYSFJjZEZ4MFhIUmNkRngwWEhSamRYTjBiMjFNWVhsdmRYUkhjbWxrTG14aGVXOTFkQ2dwTzF4dVhIUmNkRngwWEhSY2RGeDBmU0FwTzF4dVhIUmNkRngwWEhSY2RIMWNibHgwWEhSY2RGeDBmVnh1WEhSY2RGeDBmVnh1WEhSY2RIMHBPMXh1WEhSOUxGeHVYRzU5T3lKZGZRPT0iXX0=

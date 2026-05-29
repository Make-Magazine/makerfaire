/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/pro/license-api/src/api.ts":
/*!******************************************************!*\
  !*** ./packages/packages/pro/license-api/src/api.ts ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fetchLicenseStatus: function() { return /* binding */ fetchLicenseStatus; },
/* harmony export */   fetchTierFeatures: function() { return /* binding */ fetchTierFeatures; }
/* harmony export */ });
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__);

const TIER_FEATURES_URL = 'elementor-pro/v1/license/tier-features';
const LICENSE_STATUS_URL = 'elementor-pro/v1/license/get-license-status';
const CACHE_TTL_MS = 20_000;
const cache = new Map();
function cachedGet(url) {
  const now = Date.now();
  const cached = cache.get(url);
  if (cached && now < cached.expiry) {
    return cached.promise;
  }
  const promise = (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(url).catch(error => {
    cache.delete(url);
    throw error;
  });
  cache.set(url, {
    promise,
    expiry: now + CACHE_TTL_MS
  });
  return promise;
}
async function fetchTierFeatures() {
  const response = await cachedGet(TIER_FEATURES_URL);
  return response.data?.features || [];
}
async function fetchLicenseStatus() {
  const response = await cachedGet(LICENSE_STATUS_URL);
  return !!response.data?.isExpired;
}

/***/ }),

/***/ "./packages/packages/pro/license-api/src/hooks/use-license-status.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/pro/license-api/src/hooks/use-license-status.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useIsLicenseExpired: function() { return /* binding */ useIsLicenseExpired; },
/* harmony export */   useLicenseStatus: function() { return /* binding */ useLicenseStatus; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api */ "./packages/packages/pro/license-api/src/api.ts");


const QUERY_KEY = ['license', 'status'];
function useLicenseStatus() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: QUERY_KEY,
    queryFn: _api__WEBPACK_IMPORTED_MODULE_1__.fetchLicenseStatus,
    staleTime: Infinity
  });
}
function useIsLicenseExpired() {
  const {
    data: isExpired = false,
    ...rest
  } = useLicenseStatus();
  return {
    ...rest,
    data: isExpired
  };
}

/***/ }),

/***/ "./packages/packages/pro/license-api/src/hooks/use-tier-features.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/pro/license-api/src/hooks/use-tier-features.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useHasFeature: function() { return /* binding */ useHasFeature; },
/* harmony export */   useTierFeatures: function() { return /* binding */ useTierFeatures; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api */ "./packages/packages/pro/license-api/src/api.ts");


const QUERY_KEY = ['license', 'tier-features'];
function useTierFeatures() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: QUERY_KEY,
    queryFn: _api__WEBPACK_IMPORTED_MODULE_1__.fetchTierFeatures,
    staleTime: Infinity
  });
}
function useHasFeature(featureName) {
  const {
    data: features = [],
    ...rest
  } = useTierFeatures();
  return {
    ...rest,
    data: features.includes(featureName)
  };
}

/***/ }),

/***/ "@elementor/http-client":
/*!*********************************************!*\
  !*** external ["elementorV2","httpClient"] ***!
  \*********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["httpClient"];

/***/ }),

/***/ "@elementor/query":
/*!****************************************!*\
  !*** external ["elementorV2","query"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["query"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!********************************************************!*\
  !*** ./packages/packages/pro/license-api/src/index.ts ***!
  \********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fetchLicenseStatus: function() { return /* reexport safe */ _api__WEBPACK_IMPORTED_MODULE_0__.fetchLicenseStatus; },
/* harmony export */   fetchTierFeatures: function() { return /* reexport safe */ _api__WEBPACK_IMPORTED_MODULE_0__.fetchTierFeatures; },
/* harmony export */   useHasFeature: function() { return /* reexport safe */ _hooks_use_tier_features__WEBPACK_IMPORTED_MODULE_1__.useHasFeature; },
/* harmony export */   useIsLicenseExpired: function() { return /* reexport safe */ _hooks_use_license_status__WEBPACK_IMPORTED_MODULE_2__.useIsLicenseExpired; },
/* harmony export */   useLicenseStatus: function() { return /* reexport safe */ _hooks_use_license_status__WEBPACK_IMPORTED_MODULE_2__.useLicenseStatus; },
/* harmony export */   useTierFeatures: function() { return /* reexport safe */ _hooks_use_tier_features__WEBPACK_IMPORTED_MODULE_1__.useTierFeatures; }
/* harmony export */ });
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./api */ "./packages/packages/pro/license-api/src/api.ts");
/* harmony import */ var _hooks_use_tier_features__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-tier-features */ "./packages/packages/pro/license-api/src/hooks/use-tier-features.ts");
/* harmony import */ var _hooks_use_license_status__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks/use-license-status */ "./packages/packages/pro/license-api/src/hooks/use-license-status.ts");
// Core API


// Hooks


}();
(window.elementorV2 = window.elementorV2 || {}).licenseApi = __webpack_exports__;
/******/ })()
;
window.elementorV2.licenseApi?.init?.();
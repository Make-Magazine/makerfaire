/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/locations/src/components/error-boundary.tsx":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/locations/src/components/error-boundary.tsx ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ErrorBoundary; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

class ErrorBoundary extends react__WEBPACK_IMPORTED_MODULE_0__.Component {
  state = {
    hasError: false
  };
  static getDerivedStateFromError() {
    // Update state so the next render will show the fallback UI.
    return {
      hasError: true
    };
  }
  render() {
    if (this.state.hasError) {
      return this.props.fallback;
    }
    return this.props.children;
  }
}

/***/ }),

/***/ "./packages/packages/libs/locations/src/components/injected-component-wrapper.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/libs/locations/src/components/injected-component-wrapper.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ InjectedComponentWrapper; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _error_boundary__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./error-boundary */ "./packages/packages/libs/locations/src/components/error-boundary.tsx");



function InjectedComponentWrapper({
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_error_boundary__WEBPACK_IMPORTED_MODULE_1__["default"], {
    fallback: null
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Suspense, {
    fallback: null
  }, children));
}

/***/ }),

/***/ "./packages/packages/libs/locations/src/create-location.tsx":
/*!******************************************************************!*\
  !*** ./packages/packages/libs/locations/src/create-location.tsx ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createLocation: function() { return /* binding */ createLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _injections__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./injections */ "./packages/packages/libs/locations/src/injections.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function createLocation() {
  const injections = new Map();
  const getInjections = (0,_injections__WEBPACK_IMPORTED_MODULE_1__.createGetInjections)(injections);
  const useInjections = (0,_injections__WEBPACK_IMPORTED_MODULE_1__.createUseInjections)(getInjections);
  const Slot = createSlot(useInjections);
  const inject = createInject(injections);

  // Push the clear function to the flushInjectionsFns array, so we can flush all injections at once.
  _injections__WEBPACK_IMPORTED_MODULE_1__.flushInjectionsFns.push(() => injections.clear());
  return {
    inject,
    getInjections,
    useInjections,
    Slot
  };
}
function createSlot(useInjections) {
  return props => {
    const injections = useInjections();
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, injections.map(({
      id,
      component: Component
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Component, _extends({}, props, {
      key: id
    }))));
  };
}
function createInject(injections) {
  return ({
    component,
    id,
    options = {}
  }) => {
    if (injections.has(id) && !options?.overwrite) {
      // eslint-disable-next-line no-console
      console.warn(`An injection with the id "${id}" already exists. Did you mean to use "options.overwrite"?`);
      return;
    }
    injections.set(id, {
      id,
      component: (0,_injections__WEBPACK_IMPORTED_MODULE_1__.wrapInjectedComponent)(component),
      priority: options.priority ?? _injections__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_PRIORITY
    });
  };
}

/***/ }),

/***/ "./packages/packages/libs/locations/src/create-replaceable-location.tsx":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/locations/src/create-replaceable-location.tsx ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createReplaceableLocation: function() { return /* binding */ createReplaceableLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _injections__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./injections */ "./packages/packages/libs/locations/src/injections.tsx");


function createReplaceableLocation() {
  const injections = new Map();
  const getInjections = (0,_injections__WEBPACK_IMPORTED_MODULE_1__.createGetInjections)(injections);
  const useInjections = (0,_injections__WEBPACK_IMPORTED_MODULE_1__.createUseInjections)(getInjections);
  const Slot = createReplaceable(useInjections);
  const inject = createRegister(injections);

  // Push the clear function to the flushInjectionsFns array, so we can flush all injections at once.
  _injections__WEBPACK_IMPORTED_MODULE_1__.flushInjectionsFns.push(() => injections.clear());
  return {
    getInjections,
    useInjections,
    inject,
    Slot
  };
}
function createReplaceable(useInjections) {
  return props => {
    const injections = useInjections();
    const {
      component: Component
    } = injections.find(({
      condition
    }) => condition?.(props)) ?? {};
    if (!Component) {
      return props.children;
    }
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Component, props);
  };
}
function createRegister(injections) {
  return ({
    component,
    id,
    condition = () => true,
    options = {}
  }) => {
    injections.set(id, {
      id,
      component: (0,_injections__WEBPACK_IMPORTED_MODULE_1__.wrapInjectedComponent)(component),
      condition,
      priority: options.priority ?? _injections__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_PRIORITY
    });
  };
}

/***/ }),

/***/ "./packages/packages/libs/locations/src/injections.tsx":
/*!*************************************************************!*\
  !*** ./packages/packages/libs/locations/src/injections.tsx ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_PRIORITY: function() { return /* binding */ DEFAULT_PRIORITY; },
/* harmony export */   createGetInjections: function() { return /* binding */ createGetInjections; },
/* harmony export */   createUseInjections: function() { return /* binding */ createUseInjections; },
/* harmony export */   flushAllInjections: function() { return /* binding */ flushAllInjections; },
/* harmony export */   flushInjectionsFns: function() { return /* binding */ flushInjectionsFns; },
/* harmony export */   wrapInjectedComponent: function() { return /* binding */ wrapInjectedComponent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_injected_component_wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/injected-component-wrapper */ "./packages/packages/libs/locations/src/components/injected-component-wrapper.tsx");



const DEFAULT_PRIORITY = 10;

// Allow flushing all injections at once, for testing purposes.
const flushInjectionsFns = [];
function flushAllInjections() {
  flushInjectionsFns.forEach(flush => flush());
}
function createGetInjections(injections) {
  return () => [...injections.values()].sort((a, b) => a.priority - b.priority);
}
function createUseInjections(getInjections) {
  return () => (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => getInjections(), []);
}
function wrapInjectedComponent(Component) {
  return props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_injected_component_wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Component, props));
}

/***/ }),

/***/ "./packages/packages/libs/locations/src/types.ts":
/*!*******************************************************!*\
  !*** ./packages/packages/libs/locations/src/types.ts ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "react":
/*!**************************!*\
  !*** external ["React"] ***!
  \**************************/
/***/ (function(module) {

module.exports = window["React"];

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
/*!*******************************************************!*\
  !*** ./packages/packages/libs/locations/src/index.ts ***!
  \*******************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __flushAllInjections: function() { return /* reexport safe */ _injections__WEBPACK_IMPORTED_MODULE_3__.flushAllInjections; },
/* harmony export */   createLocation: function() { return /* reexport safe */ _create_location__WEBPACK_IMPORTED_MODULE_1__.createLocation; },
/* harmony export */   createReplaceableLocation: function() { return /* reexport safe */ _create_replaceable_location__WEBPACK_IMPORTED_MODULE_2__.createReplaceableLocation; }
/* harmony export */ });
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./types */ "./packages/packages/libs/locations/src/types.ts");
/* harmony import */ var _create_location__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./create-location */ "./packages/packages/libs/locations/src/create-location.tsx");
/* harmony import */ var _create_replaceable_location__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-replaceable-location */ "./packages/packages/libs/locations/src/create-replaceable-location.tsx");
/* harmony import */ var _injections__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./injections */ "./packages/packages/libs/locations/src/injections.tsx");




}();
(window.elementorV2 = window.elementorV2 || {}).locations = __webpack_exports__;
/******/ })()
;
window.elementorV2.locations?.init?.();
//# sourceMappingURL=locations.js.map
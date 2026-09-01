/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/editor-responsive/src/hooks/use-activate-breakpoint.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/hooks/use-activate-breakpoint.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useActivateBreakpoint: function() { return /* binding */ useActivateBreakpoint; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);


function useActivateBreakpoint() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(breakpoint => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommand)('panel/change-device-mode', {
      device: breakpoint
    });
  }, []);
}

/***/ }),

/***/ "./packages/packages/libs/editor-responsive/src/hooks/use-active-breakpoint.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/hooks/use-active-breakpoint.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useActiveBreakpoint: function() { return /* binding */ useActiveBreakpoint; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function useActiveBreakpoint() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/device-mode/change'), getActiveBreakpoint);
}
function getActiveBreakpoint() {
  const extendedWindow = window;
  return extendedWindow.elementor?.channels?.deviceMode?.request?.('currentMode') || null;
}

/***/ }),

/***/ "./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints-map.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints-map.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useBreakpointsMap: function() { return /* binding */ useBreakpointsMap; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _use_breakpoints__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-breakpoints */ "./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints.ts");


function useBreakpointsMap() {
  const breakpoints = (0,_use_breakpoints__WEBPACK_IMPORTED_MODULE_1__.useBreakpoints)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const entries = breakpoints.map(breakpoint => [breakpoint.id, breakpoint]);
    return Object.fromEntries(entries);
  }, [breakpoints]);
}

/***/ }),

/***/ "./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useBreakpoints: function() { return /* binding */ useBreakpoints; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _sync_get_breakpoints__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/get-breakpoints */ "./packages/packages/libs/editor-responsive/src/sync/get-breakpoints.ts");


function useBreakpoints() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), _sync_get_breakpoints__WEBPACK_IMPORTED_MODULE_1__.getBreakpoints);
}

/***/ }),

/***/ "./packages/packages/libs/editor-responsive/src/sync/get-breakpoints-tree.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/sync/get-breakpoints-tree.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBreakpointsTree: function() { return /* binding */ getBreakpointsTree; }
/* harmony export */ });
/* harmony import */ var _utils_get_breakpoints_by_widths__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/get-breakpoints-by-widths */ "./packages/packages/libs/editor-responsive/src/sync/utils/get-breakpoints-by-widths.ts");

function getBreakpointsTree() {
  const {
    minWidth,
    defaults,
    maxWidth
  } = (0,_utils_get_breakpoints_by_widths__WEBPACK_IMPORTED_MODULE_0__.getBreakpointsByWidths)();
  const [rootBreakpoint] = defaults;
  const rootNode = {
    ...rootBreakpoint,
    children: []
  };

  // gets an array of breakpoints and nest one in the prior breakpoint
  const buildBranch = breakpoints => {
    let last = rootNode;
    breakpoints.forEach(breakpoint => {
      const newNode = {
        ...breakpoint,
        children: []
      };
      last.children.push(newNode);
      last = newNode;
    });
  };
  buildBranch(minWidth);
  buildBranch(maxWidth);
  return rootNode;
}

/***/ }),

/***/ "./packages/packages/libs/editor-responsive/src/sync/get-breakpoints.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/sync/get-breakpoints.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBreakpoints: function() { return /* binding */ getBreakpoints; }
/* harmony export */ });
/* harmony import */ var _utils_get_breakpoints_by_widths__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/get-breakpoints-by-widths */ "./packages/packages/libs/editor-responsive/src/sync/utils/get-breakpoints-by-widths.ts");

function getBreakpoints() {
  const {
    minWidth,
    defaults,
    maxWidth
  } = (0,_utils_get_breakpoints_by_widths__WEBPACK_IMPORTED_MODULE_0__.getBreakpointsByWidths)();
  return [...minWidth, ...defaults, ...maxWidth];
}

/***/ }),

/***/ "./packages/packages/libs/editor-responsive/src/sync/utils/get-breakpoints-by-widths.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/sync/utils/get-breakpoints-by-widths.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBreakpointsByWidths: function() { return /* binding */ getBreakpointsByWidths; }
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);

function getBreakpointsByWidths() {
  const {
    breakpoints
  } = window.elementor?.config?.responsive || {};
  if (!breakpoints || Object.entries(breakpoints).length === 0) {
    return {
      minWidth: [],
      defaults: [],
      maxWidth: []
    };
  }
  const minWidth = [];
  const maxWidth = [];
  const defaults = [
  // Desktop breakpoint is not included in V1 config.
  {
    id: 'desktop',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Desktop', 'elementor')
  }];
  Object.entries(breakpoints).forEach(([id, v1Breakpoint]) => {
    if (!v1Breakpoint.is_enabled) {
      return;
    }
    const breakpoint = {
      id: id,
      label: v1Breakpoint.label,
      width: v1Breakpoint.value,
      type: v1Breakpoint.direction === 'min' ? 'min-width' : 'max-width'
    };
    if (!breakpoint.width) {
      defaults.push(breakpoint);
    } else if (breakpoint.type === 'min-width') {
      minWidth.push(breakpoint);
    } else if (breakpoint.type === 'max-width') {
      maxWidth.push(breakpoint);
    }
  });
  const byWidth = (a, b) => {
    return a.width && b.width ? b.width - a.width : 0;
  };
  return {
    minWidth: minWidth.sort(byWidth),
    defaults,
    maxWidth: maxWidth.sort(byWidth)
  };
}

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

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
/*!***************************************************************!*\
  !*** ./packages/packages/libs/editor-responsive/src/index.ts ***!
  \***************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBreakpoints: function() { return /* reexport safe */ _sync_get_breakpoints__WEBPACK_IMPORTED_MODULE_4__.getBreakpoints; },
/* harmony export */   getBreakpointsTree: function() { return /* reexport safe */ _sync_get_breakpoints_tree__WEBPACK_IMPORTED_MODULE_5__.getBreakpointsTree; },
/* harmony export */   useActivateBreakpoint: function() { return /* reexport safe */ _hooks_use_activate_breakpoint__WEBPACK_IMPORTED_MODULE_0__.useActivateBreakpoint; },
/* harmony export */   useActiveBreakpoint: function() { return /* reexport safe */ _hooks_use_active_breakpoint__WEBPACK_IMPORTED_MODULE_1__.useActiveBreakpoint; },
/* harmony export */   useBreakpoints: function() { return /* reexport safe */ _hooks_use_breakpoints__WEBPACK_IMPORTED_MODULE_2__.useBreakpoints; },
/* harmony export */   useBreakpointsMap: function() { return /* reexport safe */ _hooks_use_breakpoints_map__WEBPACK_IMPORTED_MODULE_3__.useBreakpointsMap; }
/* harmony export */ });
/* harmony import */ var _hooks_use_activate_breakpoint__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./hooks/use-activate-breakpoint */ "./packages/packages/libs/editor-responsive/src/hooks/use-activate-breakpoint.ts");
/* harmony import */ var _hooks_use_active_breakpoint__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-active-breakpoint */ "./packages/packages/libs/editor-responsive/src/hooks/use-active-breakpoint.ts");
/* harmony import */ var _hooks_use_breakpoints__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks/use-breakpoints */ "./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints.ts");
/* harmony import */ var _hooks_use_breakpoints_map__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./hooks/use-breakpoints-map */ "./packages/packages/libs/editor-responsive/src/hooks/use-breakpoints-map.ts");
/* harmony import */ var _sync_get_breakpoints__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./sync/get-breakpoints */ "./packages/packages/libs/editor-responsive/src/sync/get-breakpoints.ts");
/* harmony import */ var _sync_get_breakpoints_tree__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./sync/get-breakpoints-tree */ "./packages/packages/libs/editor-responsive/src/sync/get-breakpoints-tree.ts");






}();
(window.elementorV2 = window.elementorV2 || {}).editorResponsive = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorResponsive?.init?.();
//# sourceMappingURL=editor-responsive.js.map
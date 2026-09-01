/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/react-dom/client.js":
/*!******************************************!*\
  !*** ./node_modules/react-dom/client.js ***!
  \******************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {



var m = __webpack_require__(/*! react-dom */ "react-dom");
if (false) // removed by dead control flow
{} else {
  var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  exports.createRoot = function(c, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.createRoot(c, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
  exports.hydrateRoot = function(c, h, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.hydrateRoot(c, h, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
}


/***/ }),

/***/ "./packages/packages/core/editor/src/components/shell.tsx":
/*!****************************************************************!*\
  !*** ./packages/packages/core/editor/src/components/shell.tsx ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Shell; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../locations */ "./packages/packages/core/editor/src/locations.ts");


function Shell() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations__WEBPACK_IMPORTED_MODULE_1__.TopSlot, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", {
    style: {
      display: 'none'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations__WEBPACK_IMPORTED_MODULE_1__.LogicSlot, null)));
}

/***/ }),

/***/ "./packages/packages/core/editor/src/ensure-current-user.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor/src/ensure-current-user.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ensureCurrentUser: function() { return /* binding */ ensureCurrentUser; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);


async function ensureCurrentUser() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.registerDataHook)('after', 'editor/documents/attach-preview', async () => {
    try {
      await (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__.ensureUser)();
    } catch {}
  });
}

/***/ }),

/***/ "./packages/packages/core/editor/src/locations.ts":
/*!********************************************************!*\
  !*** ./packages/packages/core/editor/src/locations.ts ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LogicSlot: function() { return /* binding */ LogicSlot; },
/* harmony export */   TopSlot: function() { return /* binding */ TopSlot; },
/* harmony export */   injectIntoLogic: function() { return /* binding */ injectIntoLogic; },
/* harmony export */   injectIntoTop: function() { return /* binding */ injectIntoTop; }
/* harmony export */ });
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_0__);

const {
  Slot: TopSlot,
  inject: injectIntoTop
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();
const {
  Slot: LogicSlot,
  inject: injectIntoLogic
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();

/***/ }),

/***/ "./packages/packages/core/editor/src/start.tsx":
/*!*****************************************************!*\
  !*** ./packages/packages/core/editor/src/start.tsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   start: function() { return /* binding */ start; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react_dom_client__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react-dom/client */ "./node_modules/react-dom/client.js");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _components_shell__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/shell */ "./packages/packages/core/editor/src/components/shell.tsx");
/* harmony import */ var _ensure_current_user__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./ensure-current-user */ "./packages/packages/core/editor/src/ensure-current-user.ts");










function start(domElement) {
  const store = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_6__.__createStore)();
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_5__.createQueryClient)();
  (0,_ensure_current_user__WEBPACK_IMPORTED_MODULE_9__.ensureCurrentUser)();
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__.__privateDispatchReadyEvent)();
  render(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_store__WEBPACK_IMPORTED_MODULE_6__.__StoreProvider, {
    store: store
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_query__WEBPACK_IMPORTED_MODULE_5__.QueryClientProvider, {
    client: queryClient
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.DirectionProvider, {
    rtl: window.document.dir === 'rtl'
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.GlobalDialog, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_shell__WEBPACK_IMPORTED_MODULE_8__["default"], null))))), domElement);
}

// Support conditional rendering based on the React version.
// We use `createRoot` when available, but fallback to `ReactDOM.render` for older versions.
function render(app, domElement) {
  let renderFn;
  try {
    const root = (0,react_dom_client__WEBPACK_IMPORTED_MODULE_2__.createRoot)(domElement);
    renderFn = () => {
      root.render(app);
    };
  } catch {
    renderFn = () => {
      // eslint-disable-next-line react/no-deprecated
      react_dom__WEBPACK_IMPORTED_MODULE_1__.render(app, domElement);
    };
  }
  renderFn();
}

/***/ }),

/***/ "@elementor/editor-current-user":
/*!****************************************************!*\
  !*** external ["elementorV2","editorCurrentUser"] ***!
  \****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCurrentUser"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/locations":
/*!********************************************!*\
  !*** external ["elementorV2","locations"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["locations"];

/***/ }),

/***/ "@elementor/query":
/*!****************************************!*\
  !*** external ["elementorV2","query"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["query"];

/***/ }),

/***/ "@elementor/store":
/*!****************************************!*\
  !*** external ["elementorV2","store"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["store"];

/***/ }),

/***/ "@elementor/ui":
/*!*************************************!*\
  !*** external ["elementorV2","ui"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["ui"];

/***/ }),

/***/ "react":
/*!**************************!*\
  !*** external ["React"] ***!
  \**************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "react-dom":
/*!*****************************!*\
  !*** external ["ReactDOM"] ***!
  \*****************************/
/***/ (function(module) {

module.exports = window["ReactDOM"];

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
/*!****************************************************!*\
  !*** ./packages/packages/core/editor/src/index.ts ***!
  \****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   injectIntoLogic: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic; },
/* harmony export */   injectIntoTop: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop; },
/* harmony export */   start: function() { return /* reexport safe */ _start__WEBPACK_IMPORTED_MODULE_1__.start; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./locations */ "./packages/packages/core/editor/src/locations.ts");
/* harmony import */ var _start__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./start */ "./packages/packages/core/editor/src/start.tsx");


}();
(window.elementorV2 = window.elementorV2 || {}).editor = __webpack_exports__;
/******/ })()
;
window.elementorV2.editor?.init?.();
//# sourceMappingURL=editor.js.map
/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/editor-styles/src/types.ts":
/*!***********************************************************!*\
  !*** ./packages/packages/libs/editor-styles/src/types.ts ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "./packages/packages/libs/editor-styles/src/utils/generate-id.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-styles/src/utils/generate-id.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   generateId: function() { return /* binding */ generateId; }
/* harmony export */ });
function generateId(prefix = '', existingIds = []) {
  let id;
  do {
    id = prefix + Math.random().toString(16).slice(2, 9);
  } while (existingIds.includes(id));
  return id;
}

/***/ }),

/***/ "./packages/packages/libs/editor-styles/src/utils/get-styles-schema.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/libs/editor-styles/src/utils/get-styles-schema.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getStylesSchema: function() { return /* binding */ getStylesSchema; },
/* harmony export */   isExistingStyleProperty: function() { return /* binding */ isExistingStyleProperty; }
/* harmony export */ });
const getElementorConfig = () => {
  const extendedWindow = window;
  return extendedWindow.elementor?.config ?? {};
};
const getStylesSchema = () => {
  const config = getElementorConfig();
  const styleSchema = config?.atomic?.styles_schema ?? {};
  return styleSchema;
};
const isExistingStyleProperty = property => {
  const stylesSchema = getStylesSchema();
  return Object.keys(stylesSchema).includes(property);
};

/***/ }),

/***/ "./packages/packages/libs/editor-styles/src/utils/get-variant-by-meta.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-styles/src/utils/get-variant-by-meta.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getVariantByMeta: function() { return /* binding */ getVariantByMeta; }
/* harmony export */ });
function getVariantByMeta(style, meta) {
  return style.variants.find(variant => {
    return variant.meta.breakpoint === meta.breakpoint && variant.meta.state === meta.state;
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-styles/src/utils/state-utils.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-styles/src/utils/state-utils.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getSelectorWithState: function() { return /* binding */ getSelectorWithState; },
/* harmony export */   isClassState: function() { return /* binding */ isClassState; },
/* harmony export */   isPseudoState: function() { return /* binding */ isPseudoState; }
/* harmony export */ });
const PSEUDO_STATES = ['hover', 'focus', 'active', 'focus-visible'];
const CLASS_STATES = ['e--selected', 'e--disabled'];
function getAdditionalStates(state) {
  if (state === 'hover') {
    return ['focus-visible'];
  }
  return [];
}
function getStateSelector(state) {
  if (isClassState(state)) {
    return `.${state}`;
  }
  if (isPseudoState(state)) {
    return `:${state}`;
  }
  return state;
}
function isClassState(state) {
  return CLASS_STATES.includes(state);
}
function isPseudoState(state) {
  return PSEUDO_STATES.includes(state);
}
function getSelectorWithState(baseSelector, state) {
  if (!state) {
    return baseSelector;
  }
  return [state, ...getAdditionalStates(state)].map(currentState => `${baseSelector}${getStateSelector(currentState)}`).join(',');
}

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
/*!***********************************************************!*\
  !*** ./packages/packages/libs/editor-styles/src/index.ts ***!
  \***********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   generateId: function() { return /* reexport safe */ _utils_generate_id__WEBPACK_IMPORTED_MODULE_1__.generateId; },
/* harmony export */   getSelectorWithState: function() { return /* reexport safe */ _utils_state_utils__WEBPACK_IMPORTED_MODULE_4__.getSelectorWithState; },
/* harmony export */   getStylesSchema: function() { return /* reexport safe */ _utils_get_styles_schema__WEBPACK_IMPORTED_MODULE_2__.getStylesSchema; },
/* harmony export */   getVariantByMeta: function() { return /* reexport safe */ _utils_get_variant_by_meta__WEBPACK_IMPORTED_MODULE_3__.getVariantByMeta; },
/* harmony export */   isClassState: function() { return /* reexport safe */ _utils_state_utils__WEBPACK_IMPORTED_MODULE_4__.isClassState; },
/* harmony export */   isExistingStyleProperty: function() { return /* reexport safe */ _utils_get_styles_schema__WEBPACK_IMPORTED_MODULE_2__.isExistingStyleProperty; },
/* harmony export */   isPseudoState: function() { return /* reexport safe */ _utils_state_utils__WEBPACK_IMPORTED_MODULE_4__.isPseudoState; }
/* harmony export */ });
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./types */ "./packages/packages/libs/editor-styles/src/types.ts");
/* harmony import */ var _utils_generate_id__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/generate-id */ "./packages/packages/libs/editor-styles/src/utils/generate-id.ts");
/* harmony import */ var _utils_get_styles_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/get-styles-schema */ "./packages/packages/libs/editor-styles/src/utils/get-styles-schema.ts");
/* harmony import */ var _utils_get_variant_by_meta__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/get-variant-by-meta */ "./packages/packages/libs/editor-styles/src/utils/get-variant-by-meta.ts");
/* harmony import */ var _utils_state_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/state-utils */ "./packages/packages/libs/editor-styles/src/utils/state-utils.ts");
// types


// utils




}();
(window.elementorV2 = window.elementorV2 || {}).editorStyles = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorStyles?.init?.();
//# sourceMappingURL=editor-styles.js.map
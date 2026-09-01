/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-templates/src/init.ts":
/*!*************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/init.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _load_templates__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./load-templates */ "./packages/packages/core/editor-templates/src/load-templates.ts");
/* harmony import */ var _render_template_styles__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./render-template-styles */ "./packages/packages/core/editor-templates/src/render-template-styles.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-templates/src/store.ts");
/* harmony import */ var _templates_styles_provider__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./templates-styles-provider */ "./packages/packages/core/editor-templates/src/templates-styles-provider.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-templates/src/utils.ts");









function init() {
  if (!(0,_utils__WEBPACK_IMPORTED_MODULE_8__.isHandlingTemplateStyles)()) {
    return;
  }
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__registerSlice)(_store__WEBPACK_IMPORTED_MODULE_6__.slice);
  _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.register(_templates_styles_provider__WEBPACK_IMPORTED_MODULE_7__.templatesStylesProvider);
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.registerDataHook)('after', 'editor/documents/attach-preview', async () => {
    (0,_load_templates__WEBPACK_IMPORTED_MODULE_4__.unloadTemplates)();
    (0,_templates_styles_provider__WEBPACK_IMPORTED_MODULE_7__.clearTemplatesStyles)();
    await (0,_load_templates__WEBPACK_IMPORTED_MODULE_4__.loadTemplates)();
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'templates-styles',
    component: _render_template_styles__WEBPACK_IMPORTED_MODULE_5__.RenderTemplateStyles
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-templates/src/load-templates.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/load-templates.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   loadTemplates: function() { return /* binding */ loadTemplates; },
/* harmony export */   unloadTemplates: function() { return /* binding */ unloadTemplates; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-templates/src/store.ts");




const TEMPLATE_ATTRIBUTE = 'data-elementor-post-type="elementor_library"';
const DOCUMENT_WRAPPER_ATTR = 'data-elementor-id';
async function loadTemplates() {
  const iframeDocument = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.getCanvasIframeDocument)();
  if (!iframeDocument) {
    return;
  }
  const currentDocumentId = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.getV1CurrentDocument)()?.id;
  const templateIds = getTemplateIds(iframeDocument, currentDocumentId);
  if (!templateIds.length) {
    return;
  }
  const documents = await fetchDocuments(templateIds);
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.setTemplates(documents));
}
function unloadTemplates() {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.clearTemplates());
}
function getTemplateIds(iframeDocument, currentDocumentId) {
  const elements = [...iframeDocument.body.querySelectorAll(`[${TEMPLATE_ATTRIBUTE}]`)];
  const ids = elements.map(el => Number(el.getAttribute(DOCUMENT_WRAPPER_ATTR))).filter(id => !isNaN(id) && id !== currentDocumentId);
  return [...new Set(ids)];
}
async function fetchDocuments(ids) {
  const results = await Promise.all(ids.map(async id => {
    try {
      // using ajax.load instead of the document-manager as the latter causes an issue when trying to edit a template
      return await _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.ajax.load({
        data: {
          id
        },
        action: 'get_document_config',
        unique_id: `template-${id}`
      });
    } catch {
      return null;
    }
  }));
  return results.filter(doc => doc !== null);
}

/***/ }),

/***/ "./packages/packages/core/editor-templates/src/render-template-styles.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/render-template-styles.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RenderTemplateStyles: function() { return /* binding */ RenderTemplateStyles; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _templates_styles_provider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./templates-styles-provider */ "./packages/packages/core/editor-templates/src/templates-styles-provider.ts");
/* harmony import */ var _use_loaded_templates__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-loaded-templates */ "./packages/packages/core/editor-templates/src/use-loaded-templates.ts");



const RenderTemplateStyles = () => {
  const templates = (0,_use_loaded_templates__WEBPACK_IMPORTED_MODULE_2__.useLoadedTemplates)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const styles = templates.flatMap(extractStylesFromDocument);
    (0,_templates_styles_provider__WEBPACK_IMPORTED_MODULE_1__.addTemplateStyles)(styles);
  }, [templates]);
  return null;
};
function extractStylesFromDocument(elements) {
  if (!elements.length) {
    return [];
  }
  return elements.flatMap(extractStylesFromElement);
}
function extractStylesFromElement(element) {
  return [...Object.values(element.styles ?? {}), ...(element.elements ?? []).flatMap(extractStylesFromElement)];
}

/***/ }),

/***/ "./packages/packages/core/editor-templates/src/store.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/store.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   selectTemplates: function() { return /* binding */ selectTemplates; },
/* harmony export */   slice: function() { return /* binding */ slice; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);

const initialState = {
  entities: {}
};
const slice = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSlice)({
  name: 'templates',
  initialState,
  reducers: {
    setTemplates(state, action) {
      action.payload.forEach(doc => {
        state.entities[doc.id] = doc.elements ?? [];
      });
    },
    clearTemplates(state) {
      state.entities = {};
    }
  }
});
const selectEntities = state => state.templates.entities;
const selectTemplates = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectEntities], entities => Object.values(entities));

/***/ }),

/***/ "./packages/packages/core/editor-templates/src/templates-styles-provider.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/templates-styles-provider.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addTemplateStyles: function() { return /* binding */ addTemplateStyles; },
/* harmony export */   clearTemplatesStyles: function() { return /* binding */ clearTemplatesStyles; },
/* harmony export */   templatesStylesProvider: function() { return /* binding */ templatesStylesProvider; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);

let styles = [];
const listeners = new Set();
function addTemplateStyles(newStyles) {
  styles = [...styles, ...newStyles];
  listeners.forEach(cb => cb());
}
function clearTemplatesStyles() {
  styles = [];
  listeners.forEach(cb => cb());
}
const templatesStylesProvider = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.createStylesProvider)({
  key: 'templates-styles',
  priority: 50,
  subscribe: cb => {
    listeners.add(cb);
    return () => {
      listeners.delete(cb);
    };
  },
  actions: {
    all: () => styles,
    get: id => styles.find(style => style.id === id) ?? null
  }
});

/***/ }),

/***/ "./packages/packages/core/editor-templates/src/use-loaded-templates.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/use-loaded-templates.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useLoadedTemplates: function() { return /* binding */ useLoadedTemplates; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-templates/src/store.ts");


function useLoadedTemplates() {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_1__.selectTemplates);
}

/***/ }),

/***/ "./packages/packages/core/editor-templates/src/utils.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/utils.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isHandlingTemplateStyles: function() { return /* binding */ isHandlingTemplateStyles; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const MIN_PRO_VERSION_FOR_SELF_HANDLED_STYLES = '4.1';
const isHandlingTemplateStyles = () => (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.isProActive)() && !(0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.isProAtLeast)(MIN_PRO_VERSION_FOR_SELF_HANDLED_STYLES);

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-documents":
/*!**************************************************!*\
  !*** external ["elementorV2","editorDocuments"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorDocuments"];

/***/ }),

/***/ "@elementor/editor-styles-repository":
/*!*********************************************************!*\
  !*** external ["elementorV2","editorStylesRepository"] ***!
  \*********************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorStylesRepository"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/store":
/*!****************************************!*\
  !*** external ["elementorV2","store"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["store"];

/***/ }),

/***/ "@elementor/utils":
/*!****************************************!*\
  !*** external ["elementorV2","utils"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["utils"];

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
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-templates/src/index.ts ***!
  \**************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   isHandlingTemplateStyles: function() { return /* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_2__.isHandlingTemplateStyles; },
/* harmony export */   useLoadedTemplates: function() { return /* reexport safe */ _use_loaded_templates__WEBPACK_IMPORTED_MODULE_1__.useLoadedTemplates; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-templates/src/init.ts");
/* harmony import */ var _use_loaded_templates__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-loaded-templates */ "./packages/packages/core/editor-templates/src/use-loaded-templates.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-templates/src/utils.ts");



}();
(window.elementorV2 = window.elementorV2 || {}).editorTemplates = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorTemplates?.init?.();
//# sourceMappingURL=editor-templates.js.map
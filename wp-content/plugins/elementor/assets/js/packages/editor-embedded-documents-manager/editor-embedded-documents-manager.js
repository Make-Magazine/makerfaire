/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-embedded-documents-manager/src/init.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-embedded-documents-manager/src/init.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _manager__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./manager */ "./packages/packages/core/editor-embedded-documents-manager/src/manager.ts");
/* harmony import */ var _styles_provider__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./styles-provider */ "./packages/packages/core/editor-embedded-documents-manager/src/styles-provider.ts");





function init() {
  _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.register(_styles_provider__WEBPACK_IMPORTED_MODULE_4__.embeddedDocumentsStylesProvider);
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.registerDataHook)('after', 'editor/documents/attach-preview', () => {
    const {
      id
    } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.getV1CurrentDocument)() ?? {};
    (0,_manager__WEBPACK_IMPORTED_MODULE_3__.setCurrentDocumentId)(id ?? null);
    _manager__WEBPACK_IMPORTED_MODULE_3__.embeddedDocumentsManager.reset();
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-embedded-documents-manager/src/manager.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-embedded-documents-manager/src/manager.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   embeddedDocumentsManager: function() { return /* binding */ embeddedDocumentsManager; },
/* harmony export */   setCurrentDocumentId: function() { return /* binding */ setCurrentDocumentId; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _styles_provider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles-provider */ "./packages/packages/core/editor-embedded-documents-manager/src/styles-provider.ts");


const pendingIds = new Set();
const loadedDocuments = new Map();
const listeners = new Set();
let currentDocumentId = null;
function setCurrentDocumentId(id) {
  currentDocumentId = id;
}

/**
 * Adds post IDs to the manager. Any ID not yet loaded will be fetched;
 * already-loaded posts are skipped without notifying listeners.
 *
 * @param {number[]} ids Post IDs to load.
 */
function addDocuments(ids) {
  const newIds = ids.filter(id => !isCurrentDocument(id) && !pendingIds.has(id) && !loadedDocuments.has(id));
  newIds.forEach(id => pendingIds.add(id));
  if (!newIds.length) {
    return;
  }
  void fetchAndNotify(newIds);
}

/**
 * Registers a related post that was already fetched.
 *
 * @param {number}   documentId The document / post ID.
 * @param {Document} data       The document data to deliver.
 */
function setDocument(documentId, data) {
  if (isCurrentDocument(documentId)) {
    return;
  }
  const isNew = !loadedDocuments.has(documentId);
  loadedDocuments.set(documentId, data);
  if (isNew) {
    notifyListeners(documentId, data);
  } else {
    (0,_styles_provider__WEBPACK_IMPORTED_MODULE_1__.addEmbeddedDocumentStyles)(documentId, data);
  }
}

/**
 * Registers a callback that is invoked whenever a related (non-current) post
 * finishes loading. Already-loaded related posts are delivered to the callback
 * asynchronously on subscribe. Returns an unsubscribe function.
 *
 * @param {EmbeddedDocumentLoadCallback} callback Function called with (documentId, data) on each load.
 */
function onDocumentLoad(callback) {
  listeners.add(callback);
  void Promise.resolve().then(() => {
    if (!listeners.has(callback)) {
      return;
    }
    loadedDocuments.forEach((data, documentId) => {
      if (!isCurrentDocument(documentId)) {
        callback(documentId, data);
      }
    });
  });
  return () => {
    listeners.delete(callback);
  };
}
function reset() {
  pendingIds.clear();
  loadedDocuments.clear();
  (0,_styles_provider__WEBPACK_IMPORTED_MODULE_1__.clearEmbeddedDocumentsStyles)();
}
const embeddedDocumentsManager = {
  addDocuments,
  setDocument,
  onDocumentLoad,
  reset
};
function isCurrentDocument(documentId) {
  return currentDocumentId !== null && documentId === currentDocumentId;
}
async function fetchAndNotify(ids) {
  const results = await Promise.all(ids.map(fetchDocument));
  results.forEach((result, index) => {
    const id = ids[index];
    if (!pendingIds.has(id)) {
      return;
    }
    pendingIds.delete(id);
    if (!result || isCurrentDocument(id)) {
      return;
    }
    const {
      data
    } = result;
    loadedDocuments.set(id, data);
    notifyListeners(id, data);
  });
}
async function fetchDocument(id) {
  try {
    const data = await _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.ajax.load({
      data: {
        id
      },
      action: 'get_document_config',
      unique_id: `embedded-document-${id}`
    });
    return {
      id,
      data
    };
  } catch {
    return null;
  }
}
function notifyListeners(documentId, data) {
  if (isCurrentDocument(documentId)) {
    return;
  }
  listeners.forEach(cb => cb(documentId, data));
  (0,_styles_provider__WEBPACK_IMPORTED_MODULE_1__.addEmbeddedDocumentStyles)(documentId, data);
}

/***/ }),

/***/ "./packages/packages/core/editor-embedded-documents-manager/src/styles-provider.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-embedded-documents-manager/src/styles-provider.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addEmbeddedDocumentStyles: function() { return /* binding */ addEmbeddedDocumentStyles; },
/* harmony export */   clearEmbeddedDocumentsStyles: function() { return /* binding */ clearEmbeddedDocumentsStyles; },
/* harmony export */   embeddedDocumentsStylesProvider: function() { return /* binding */ embeddedDocumentsStylesProvider; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);

let styles = [];
let documentStyles = new Map();
const styleListeners = new Set();
const embeddedDocumentsStylesProvider = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.createStylesProvider)({
  key: 'embedded-documents-styles',
  priority: 75,
  subscribe: cb => {
    styleListeners.add(cb);
    return () => {
      styleListeners.delete(cb);
    };
  },
  actions: {
    all: () => styles,
    get: id => styles.find(style => style.id === id) ?? null
  }
});
function notifyStyleListeners() {
  styleListeners.forEach(cb => cb());
}
function addEmbeddedDocumentStyles(documentId, document) {
  const extracted = extractStylesFromDocument(document);
  if (!extracted.length) {
    if (!documentStyles.has(documentId)) {
      return;
    }
    documentStyles.delete(documentId);
  } else {
    documentStyles.set(documentId, extracted);
  }
  styles = [...documentStyles.values()].flat();
  notifyStyleListeners();
}
function clearEmbeddedDocumentsStyles() {
  documentStyles = new Map();
  styles = [];
  notifyStyleListeners();
}
function extractStylesFromDocument(document) {
  if (!document.elements?.length) {
    return [];
  }
  return document.elements.flatMap(extractStylesFromElement);
}
function extractStylesFromElement(element) {
  return [...Object.values(element.styles ?? {}), ...(element.elements ?? []).flatMap(extractStylesFromElement)];
}

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
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-embedded-documents-manager/src/index.ts ***!
  \*******************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   embeddedDocumentsManager: function() { return /* reexport safe */ _manager__WEBPACK_IMPORTED_MODULE_1__.embeddedDocumentsManager; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   setCurrentDocumentId: function() { return /* reexport safe */ _manager__WEBPACK_IMPORTED_MODULE_1__.setCurrentDocumentId; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-embedded-documents-manager/src/init.ts");
/* harmony import */ var _manager__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./manager */ "./packages/packages/core/editor-embedded-documents-manager/src/manager.ts");


}();
(window.elementorV2 = window.elementorV2 || {}).editorEmbeddedDocumentsManager = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorEmbeddedDocumentsManager?.init?.();
//# sourceMappingURL=editor-embedded-documents-manager.js.map
/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/session/src/session-storage-context.tsx":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/session/src/session-storage-context.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Context: function() { return /* binding */ Context; },
/* harmony export */   SessionStorageProvider: function() { return /* binding */ SessionStorageProvider; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const Context = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function SessionStorageProvider({
  children,
  prefix
}) {
  const contextPrefix = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context)?.prefix ?? '';
  const chainedPrefix = contextPrefix ? `${contextPrefix}/${prefix}` : prefix;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Context.Provider, {
    value: {
      prefix: chainedPrefix
    }
  }, children);
}

/***/ }),

/***/ "./packages/packages/libs/session/src/session-storage.ts":
/*!***************************************************************!*\
  !*** ./packages/packages/libs/session/src/session-storage.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getSessionStorageItem: function() { return /* binding */ getSessionStorageItem; },
/* harmony export */   removeSessionStorageItem: function() { return /* binding */ removeSessionStorageItem; },
/* harmony export */   setSessionStorageItem: function() { return /* binding */ setSessionStorageItem; }
/* harmony export */ });
const getSessionStorageItem = key => {
  return JSON.parse(sessionStorage.getItem(key) || '{}')?.item;
};
const setSessionStorageItem = (key, item) => {
  sessionStorage.setItem(key, JSON.stringify({
    item
  }));

  // The browser doesn't dispatch the `storage` event for the current tab,
  // so we need to dispatch it manually.
  //
  // https://developer.mozilla.org/en-US/docs/Web/API/Window/storage_event
  window.dispatchEvent(new StorageEvent('storage', {
    key,
    storageArea: sessionStorage
  }));
};
const removeSessionStorageItem = key => {
  sessionStorage.removeItem(key);

  // The browser doesn't dispatch the `storage` event for the current tab,
  // so we need to dispatch it manually.
  //
  // https://developer.mozilla.org/en-US/docs/Web/API/Window/storage_event
  window.dispatchEvent(new StorageEvent('storage', {
    key,
    storageArea: sessionStorage
  }));
};

/***/ }),

/***/ "./packages/packages/libs/session/src/use-session-storage.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/libs/session/src/use-session-storage.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSessionStorage: function() { return /* binding */ useSessionStorage; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _session_storage__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./session-storage */ "./packages/packages/libs/session/src/session-storage.ts");
/* harmony import */ var _session_storage_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./session-storage-context */ "./packages/packages/libs/session/src/session-storage-context.tsx");



const useSessionStorage = (key, customPrefix) => {
  const contextPrefix = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_session_storage_context__WEBPACK_IMPORTED_MODULE_2__.Context)?.prefix ?? '';
  const prefix = customPrefix ? customPrefix : contextPrefix;
  const prefixedKey = `${prefix}/${key}`;
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return subscribeToSessionStorage(prefixedKey, newValue => {
      setValue(newValue ?? null);
    });
  }, [prefixedKey]);
  const saveValue = newValue => {
    (0,_session_storage__WEBPACK_IMPORTED_MODULE_1__.setSessionStorageItem)(prefixedKey, newValue);
  };
  const removeValue = () => {
    (0,_session_storage__WEBPACK_IMPORTED_MODULE_1__.removeSessionStorageItem)(prefixedKey);
  };
  return [value, saveValue, removeValue];
};
const subscribeToSessionStorage = (key, subscriber) => {
  subscriber((0,_session_storage__WEBPACK_IMPORTED_MODULE_1__.getSessionStorageItem)(key));
  const abortController = new AbortController();
  window.addEventListener('storage', e => {
    if (e.key !== key || e.storageArea !== sessionStorage) {
      return;
    }
    subscriber((0,_session_storage__WEBPACK_IMPORTED_MODULE_1__.getSessionStorageItem)(key));
  }, {
    signal: abortController.signal
  });
  return () => {
    abortController.abort();
  };
};

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
/*!*****************************************************!*\
  !*** ./packages/packages/libs/session/src/index.ts ***!
  \*****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Context: function() { return /* reexport safe */ _session_storage_context__WEBPACK_IMPORTED_MODULE_2__.Context; },
/* harmony export */   SessionStorageProvider: function() { return /* reexport safe */ _session_storage_context__WEBPACK_IMPORTED_MODULE_2__.SessionStorageProvider; },
/* harmony export */   getSessionStorageItem: function() { return /* reexport safe */ _session_storage__WEBPACK_IMPORTED_MODULE_0__.getSessionStorageItem; },
/* harmony export */   removeSessionStorageItem: function() { return /* reexport safe */ _session_storage__WEBPACK_IMPORTED_MODULE_0__.removeSessionStorageItem; },
/* harmony export */   setSessionStorageItem: function() { return /* reexport safe */ _session_storage__WEBPACK_IMPORTED_MODULE_0__.setSessionStorageItem; },
/* harmony export */   useSessionStorage: function() { return /* reexport safe */ _use_session_storage__WEBPACK_IMPORTED_MODULE_1__.useSessionStorage; }
/* harmony export */ });
/* harmony import */ var _session_storage__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./session-storage */ "./packages/packages/libs/session/src/session-storage.ts");
/* harmony import */ var _use_session_storage__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-session-storage */ "./packages/packages/libs/session/src/use-session-storage.ts");
/* harmony import */ var _session_storage_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./session-storage-context */ "./packages/packages/libs/session/src/session-storage-context.tsx");



}();
(window.elementorV2 = window.elementorV2 || {}).session = __webpack_exports__;
/******/ })()
;
window.elementorV2.session?.init?.();
//# sourceMappingURL=session.js.map
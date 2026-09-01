/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/editor-current-user/src/api.ts":
/*!***************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/api.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   apiClient: function() { return /* binding */ apiClient; }
/* harmony export */ });
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__);

const RESOURCE_URL = 'elementor/v1/user-data/current-user';
const getUserPayload = {
  params: {
    context: 'edit'
  }
};
const apiClient = {
  get: () => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(RESOURCE_URL, getUserPayload).then(res => {
    const {
      capabilities = [],
      suppressedMessages = []
    } = res.data;
    return {
      capabilities,
      suppressedMessages
    };
  }),
  update: data => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().patch(RESOURCE_URL, {
    suppressedMessages: data.suppressedMessages
  })
};

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/ensure-current-user.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/ensure-current-user.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ensureUser: function() { return /* binding */ ensureUser; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/libs/editor-current-user/src/api.ts");
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");



async function ensureUser() {
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.getQueryClient)();
  return queryClient.ensureQueryData({
    queryKey: [_use_current_user__WEBPACK_IMPORTED_MODULE_2__.EDITOR_CURRENT_USER_QUERY_KEY],
    queryFn: _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.get,
    retry: false
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/get-current-user.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/get-current-user.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCurrentUser: function() { return /* binding */ getCurrentUser; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");


const getCurrentUser = () => {
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.getQueryClient)();
  return queryClient.getQueryData([_use_current_user__WEBPACK_IMPORTED_MODULE_1__.EDITOR_CURRENT_USER_QUERY_KEY]);
};

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/on-set-user.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/on-set-user.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   onSetUser: function() { return /* binding */ onSetUser; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");



function onSetUser(callback) {
  let unsubscribeQuery;
  const unsubscribeListener = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), () => {
    const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_1__.getQueryClient)();
    unsubscribeQuery = queryClient.getQueryCache().subscribe(event => {
      if (event.query.queryKey.includes(_use_current_user__WEBPACK_IMPORTED_MODULE_2__.EDITOR_CURRENT_USER_QUERY_KEY)) {
        callback(event.query.state.data);
      }
    });
  });
  return () => {
    unsubscribeQuery();
    unsubscribeListener();
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/use-current-user-capabilities.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/use-current-user-capabilities.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ADMIN_CAPABILITY: function() { return /* binding */ ADMIN_CAPABILITY; },
/* harmony export */   useCurrentUserCapabilities: function() { return /* binding */ useCurrentUserCapabilities; }
/* harmony export */ });
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");

const ADMIN_CAPABILITY = 'manage_options';
const useCurrentUserCapabilities = () => {
  const {
    data
  } = (0,_use_current_user__WEBPACK_IMPORTED_MODULE_0__.useCurrentUser)();
  const canUser = capability => {
    return Boolean(data?.capabilities.includes(capability));
  };
  const isAdmin = Boolean(data?.capabilities.includes(ADMIN_CAPABILITY));
  return {
    canUser,
    isAdmin,
    capabilities: data?.capabilities
  };
};

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/use-current-user.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/use-current-user.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDITOR_CURRENT_USER_QUERY_KEY: function() { return /* binding */ EDITOR_CURRENT_USER_QUERY_KEY; },
/* harmony export */   useCurrentUser: function() { return /* binding */ useCurrentUser; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/libs/editor-current-user/src/api.ts");


const EDITOR_CURRENT_USER_QUERY_KEY = 'editor-current-user';
const useCurrentUser = () => (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
  queryKey: [EDITOR_CURRENT_USER_QUERY_KEY],
  queryFn: _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.get
});

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/use-suppressed-message.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/use-suppressed-message.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSuppressedMessage: function() { return /* binding */ useSuppressedMessage; }
/* harmony export */ });
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");
/* harmony import */ var _use_update_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-update-current-user */ "./packages/packages/libs/editor-current-user/src/use-update-current-user.ts");


const useSuppressedMessage = messageKey => {
  const {
    data
  } = (0,_use_current_user__WEBPACK_IMPORTED_MODULE_0__.useCurrentUser)();
  const {
    mutate
  } = (0,_use_update_current_user__WEBPACK_IMPORTED_MODULE_1__.useUpdateCurrentUser)();
  const isMessageSuppressed = !!data?.suppressedMessages.includes(messageKey);
  const suppressMessage = () => {
    if (!isMessageSuppressed) {
      mutate({
        suppressedMessages: [...(data?.suppressedMessages ?? []), messageKey]
      });
    }
  };
  return [isMessageSuppressed, suppressMessage];
};

/***/ }),

/***/ "./packages/packages/libs/editor-current-user/src/use-update-current-user.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/use-update-current-user.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useUpdateCurrentUser: function() { return /* binding */ useUpdateCurrentUser; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/libs/editor-current-user/src/api.ts");
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");



const useUpdateCurrentUser = () => {
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQueryClient)();
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.update,
    onSuccess: () => queryClient.invalidateQueries({
      queryKey: [_use_current_user__WEBPACK_IMPORTED_MODULE_2__.EDITOR_CURRENT_USER_QUERY_KEY]
    })
  });
};

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

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
/*!*****************************************************************!*\
  !*** ./packages/packages/libs/editor-current-user/src/index.ts ***!
  \*****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ensureUser: function() { return /* reexport safe */ _ensure_current_user__WEBPACK_IMPORTED_MODULE_3__.ensureUser; },
/* harmony export */   getCurrentUser: function() { return /* reexport safe */ _get_current_user__WEBPACK_IMPORTED_MODULE_2__.getCurrentUser; },
/* harmony export */   onSetUser: function() { return /* reexport safe */ _on_set_user__WEBPACK_IMPORTED_MODULE_4__.onSetUser; },
/* harmony export */   useCurrentUser: function() { return /* reexport safe */ _use_current_user__WEBPACK_IMPORTED_MODULE_5__.useCurrentUser; },
/* harmony export */   useCurrentUserCapabilities: function() { return /* reexport safe */ _use_current_user_capabilities__WEBPACK_IMPORTED_MODULE_1__.useCurrentUserCapabilities; },
/* harmony export */   useSuppressedMessage: function() { return /* reexport safe */ _use_suppressed_message__WEBPACK_IMPORTED_MODULE_0__.useSuppressedMessage; },
/* harmony export */   useUpdateCurrentUser: function() { return /* reexport safe */ _use_update_current_user__WEBPACK_IMPORTED_MODULE_6__.useUpdateCurrentUser; }
/* harmony export */ });
/* harmony import */ var _use_suppressed_message__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-suppressed-message */ "./packages/packages/libs/editor-current-user/src/use-suppressed-message.ts");
/* harmony import */ var _use_current_user_capabilities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-current-user-capabilities */ "./packages/packages/libs/editor-current-user/src/use-current-user-capabilities.ts");
/* harmony import */ var _get_current_user__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get-current-user */ "./packages/packages/libs/editor-current-user/src/get-current-user.ts");
/* harmony import */ var _ensure_current_user__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ensure-current-user */ "./packages/packages/libs/editor-current-user/src/ensure-current-user.ts");
/* harmony import */ var _on_set_user__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./on-set-user */ "./packages/packages/libs/editor-current-user/src/on-set-user.ts");
/* harmony import */ var _use_current_user__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./use-current-user */ "./packages/packages/libs/editor-current-user/src/use-current-user.ts");
/* harmony import */ var _use_update_current_user__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./use-update-current-user */ "./packages/packages/libs/editor-current-user/src/use-update-current-user.ts");







}();
(window.elementorV2 = window.elementorV2 || {}).editorCurrentUser = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorCurrentUser?.init?.();
//# sourceMappingURL=editor-current-user.js.map
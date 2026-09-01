/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/apps/unlock-v4-promo/src/components/atomic-elements-promo.tsx":
/*!********************************************************************************!*\
  !*** ./packages/apps/unlock-v4-promo/src/components/atomic-elements-promo.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AtomicElementsPromo: function() { return /* binding */ AtomicElementsPromo; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _hooks_use_promo_suppressed_message__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-promo-suppressed-message */ "./packages/apps/unlock-v4-promo/src/hooks/use-promo-suppressed-message.ts");









const PROMO_IMAGE = 'https://assets.elementor.com/v4-promotion/v1/images/atomic_elements_section_260.png';
const LEARN_MORE_URL = 'https://go.elementor.com/wp-dash-opt-in-v4-help-center/';
function AtomicElementsPromo() {
  const [suppressed, toggleSuppressMessage] = (0,_hooks_use_promo_suppressed_message__WEBPACK_IMPORTED_MODULE_7__.usePromoSuppressedMessage)();
  const isAdmin = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.getCurrentUser)()?.capabilities?.includes('manage_options');
  const activateAtomicElements = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      const response = await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_4__.httpService)().post('elementor/v1/operations/opt-in-v4');
      if (response.data.success) {
        window.location.reload();
      }
    } catch {
      (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__.notify)({
        type: 'error',
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Failed to activate Atomic elements', 'elementor'),
        id: 'atomic-elements-promo-error'
      });
    }
  }, []);
  if (suppressed || !isAdmin) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      bgcolor: 'background.default',
      display: 'flex',
      flexDirection: 'column'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      display: 'flex',
      alignItems: 'center',
      gap: 1,
      pl: 2.5,
      pr: 1,
      py: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
    variant: "subtitle2",
    sx: {
      flexGrow: 1,
      gap: 1,
      display: 'flex',
      alignItems: 'center'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Atomic Elements', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Chip, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('New', 'elementor'),
    size: "tiny",
    variant: "standard",
    color: "secondary"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.CloseButton, {
    slotProps: {
      icon: {
        fontSize: 'small'
      }
    },
    onClick: toggleSuppressMessage
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      maxHeight: 205,
      mx: 2,
      overflow: 'hidden'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    component: "img",
    src: PROMO_IMAGE,
    alt: "",
    sx: {
      width: '100%',
      objectFit: 'cover',
      objectPosition: 'center',
      display: 'block'
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      pl: 2.5,
      pr: 4,
      pt: 2
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
    variant: "caption",
    color: "text.tertiary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Build with modern, flexible elements designed for reusable styles and cleaner layouts. Your existing site and content stay exactly the same.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      display: 'flex',
      justifyContent: 'flex-end',
      gap: 1,
      pb: 1.5,
      pl: 2,
      pr: 2.5,
      pt: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
    variant: "text",
    size: "small",
    color: "secondary",
    href: LEARN_MORE_URL,
    target: "_blank"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Learn more', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
    variant: "contained",
    size: "small",
    color: "inherit",
    sx: {
      bgcolor: 'text.primary',
      color: 'background.paper',
      '&:hover': {
        bgcolor: 'text.secondary',
        color: 'background.paper'
      }
    },
    onClick: activateAtomicElements
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Activate now', 'elementor')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Divider, null));
}

/***/ }),

/***/ "./packages/apps/unlock-v4-promo/src/hooks/use-promo-suppressed-message.ts":
/*!*********************************************************************************!*\
  !*** ./packages/apps/unlock-v4-promo/src/hooks/use-promo-suppressed-message.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   usePromoSuppressedMessage: function() { return /* binding */ usePromoSuppressedMessage; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const MESSAGE_KEY = 'atomic_elements_promo';
const usePromoSuppressedMessage = () => {
  const [suppressed, setSuppressMessage] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useSuppressedMessage)(MESSAGE_KEY);
  const toggleSuppressMessage = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (!suppressed) {
      setSuppressMessage();
      (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__.notify)({
        type: 'default',
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('You can enable the Atomic editor anytime from Editor Settings.', 'elementor'),
        id: MESSAGE_KEY
      });
    }
  }, [suppressed, setSuppressMessage]);
  return [suppressed, toggleSuppressMessage];
};

/***/ }),

/***/ "./packages/apps/unlock-v4-promo/src/init.ts":
/*!***************************************************!*\
  !*** ./packages/apps/unlock-v4-promo/src/init.ts ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements_panel_notice__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements-panel-notice */ "@elementor/editor-elements-panel-notice");
/* harmony import */ var _elementor_editor_elements_panel_notice__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements_panel_notice__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_atomic_elements_promo__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/atomic-elements-promo */ "./packages/apps/unlock-v4-promo/src/components/atomic-elements-promo.tsx");


function init() {
  const {
    experimentalFeatures = {}
  } = window.elementorCommon?.config || {};
  const currentAtomicElementsExperimentState = experimentalFeatures?.e_atomic_elements;
  const currentContainerExperimentState = experimentalFeatures?.container;

  // The logic is if the atomic elements experiment is not active and the container experiment is active, then show the promo.
  // Container experiment must be on as part of the container feature can break site (ask about moving sections to containers breaking changes)
  if (!currentAtomicElementsExperimentState && currentContainerExperimentState) {
    (0,_elementor_editor_elements_panel_notice__WEBPACK_IMPORTED_MODULE_0__.register)(_components_atomic_elements_promo__WEBPACK_IMPORTED_MODULE_1__.AtomicElementsPromo);
  }
}

/***/ }),

/***/ "@elementor/editor-current-user":
/*!****************************************************!*\
  !*** external ["elementorV2","editorCurrentUser"] ***!
  \****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCurrentUser"];

/***/ }),

/***/ "@elementor/editor-elements-panel-notice":
/*!************************************************************!*\
  !*** external ["elementorV2","editorElementsPanelNotice"] ***!
  \************************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorElementsPanelNotice"];

/***/ }),

/***/ "@elementor/editor-notifications":
/*!******************************************************!*\
  !*** external ["elementorV2","editorNotifications"] ***!
  \******************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorNotifications"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/http-client":
/*!*********************************************!*\
  !*** external ["elementorV2","httpClient"] ***!
  \*********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["httpClient"];

/***/ }),

/***/ "@elementor/ui":
/*!*************************************!*\
  !*** external ["elementorV2","ui"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["ui"];

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
/*!****************************************************!*\
  !*** ./packages/apps/unlock-v4-promo/src/index.ts ***!
  \****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AtomicElementsPromo: function() { return /* reexport safe */ _components_atomic_elements_promo__WEBPACK_IMPORTED_MODULE_1__.AtomicElementsPromo; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/apps/unlock-v4-promo/src/init.ts");
/* harmony import */ var _components_atomic_elements_promo__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/atomic-elements-promo */ "./packages/apps/unlock-v4-promo/src/components/atomic-elements-promo.tsx");


}();
(window.elementorV2 = window.elementorV2 || {}).unlockV4Promo = __webpack_exports__;
/******/ })()
;
window.elementorV2.unlockV4Promo?.init?.();
//# sourceMappingURL=unlock-v4-promo.js.map
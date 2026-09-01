/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-widget-creation/src/components/create-widget.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-widget-creation/src/components/create-widget.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CreateWidget: function() { return /* binding */ CreateWidget; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _interpolate_links__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../interpolate-links */ "./packages/packages/core/editor-widget-creation/src/interpolate-links.tsx");









const CREATE_WIDGET_EVENT = 'elementor/editor/create-widget';
const ANGIE_MODAL_PROMOTION_IMAGE_URL = 'https://assets.elementor.com/packages/v1/images/angie-modal-promotion.png';
const ANGIE_CTA_CLICKED_EVENT = 'ai_widget_cta_clicked';
const ANGIE_INSTALL_STARTED_EVENT = 'angie_install_started';
const ANGIE_INSTALL_COMPLETED_EVENT = 'angie_install_completed';
const ANGIE_INSTALL_ABANDONED_EVENT = 'angie_install_abandoned';
function CreateWidgetModal({
  prompt,
  entryPoint,
  onClose
}) {
  const [installState, setInstallState] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('idle');
  const [agreedToTerms, setAgreedToTerms] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const handleClose = () => {
    if (installState === 'installing') {
      return;
    }
    (0,_elementor_events__WEBPACK_IMPORTED_MODULE_3__.trackEvent)({
      eventName: ANGIE_INSTALL_ABANDONED_EVENT,
      abandon_step: installState === 'error' ? 'install_error' : 'install_modal',
      trigger_source: entryPoint
    });
    onClose();
  };
  const handleInstall = async () => {
    setInstallState('installing');
    (0,_elementor_events__WEBPACK_IMPORTED_MODULE_3__.trackEvent)({
      eventName: ANGIE_INSTALL_STARTED_EVENT,
      trigger_source: entryPoint
    });
    const [result] = await Promise.all([(0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.installAngiePlugin)(), (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.saveAngieConsent)()]);
    if (!result.success) {
      setInstallState('error');
      return;
    }
    (0,_elementor_events__WEBPACK_IMPORTED_MODULE_3__.trackEvent)({
      eventName: ANGIE_INSTALL_COMPLETED_EVENT,
      trigger_source: entryPoint
    });
    (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.redirectToAppAdmin)(prompt);
  };
  const handleFallbackInstall = () => {
    (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.redirectToInstallation)(prompt);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Dialog, {
    fullWidth: true,
    maxWidth: "md",
    open: true,
    onClose: handleClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.IconButton, {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Close', 'elementor'),
    onClick: handleClose,
    sx: {
      position: 'absolute',
      right: 8,
      top: 8,
      zIndex: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.XIcon, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.DialogContent, {
    sx: {
      p: 0,
      overflow: 'hidden'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "row",
    sx: {
      height: 400
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Image, {
    sx: {
      height: '100%',
      aspectRatio: '1 / 1',
      objectFit: 'cover',
      objectPosition: 'right center'
    },
    src: ANGIE_MODAL_PROMOTION_IMAGE_URL
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    justifyContent: "space-between",
    p: 4
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    gap: 2.5,
    justifyContent: "center",
    sx: {
      flex: 1,
      paddingInlineEnd: 2.5
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
    variant: "h4",
    fontWeight: 600,
    color: "text.secondary"
  }, installState === 'error' ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Installation failed', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Create custom widgets with Angie', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
    variant: "body2"
  }, installState === 'error' ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)("We couldn't install Angie automatically. Click below to install it manually.", 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Build custom widgets, sections, and code using simple instructions. Install once to start building directly from the editor.', 'elementor')), installState !== 'error' && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.FormControlLabel, {
    control: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Checkbox, {
      size: "small",
      checked: agreedToTerms,
      onChange: (_e, checked) => setAgreedToTerms(checked)
    }),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
      variant: "body2",
      color: "text.secondary"
    }, (0,_interpolate_links__WEBPACK_IMPORTED_MODULE_7__.interpolateLinks)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.sprintf)(
    // translators: %1$s is the Terms link, %2$s is the Privacy Policy link.
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('I agree to the %1$s & %2$s.', 'elementor'), '{{terms}}', '{{privacy}}'), {
      terms: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Terms', 'elementor'),
        href: 'https://elementor.com/terms/angie-terms-conditions/'
      },
      privacy: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Privacy Policy', 'elementor'),
        href: 'https://elementor.com/about/privacy/'
      }
    }))
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "row",
    justifyContent: "flex-end"
  }, installState === 'error' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
    variant: "contained",
    color: "accent",
    onClick: handleFallbackInstall
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Install Manually', 'elementor')) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
    variant: "contained",
    color: "accent",
    onClick: handleInstall,
    disabled: installState === 'installing' || !agreedToTerms,
    startIcon: installState === 'installing' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.CircularProgress, {
      size: 18,
      color: "inherit"
    }) : undefined
  }, installState === 'installing' ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Installing…', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Install & Activate', 'elementor'))))))));
}
function CreateWidget() {
  const [modalData, setModalData] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleShow = event => {
      const customEvent = event;
      const hasAngieInstalled = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.isAngieAvailable)();
      (0,_elementor_events__WEBPACK_IMPORTED_MODULE_3__.trackEvent)({
        eventName: ANGIE_CTA_CLICKED_EVENT,
        entry_point: customEvent.detail.entry_point,
        has_angie_installed: hasAngieInstalled
      });
      if (hasAngieInstalled) {
        (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.sendPromptToAngie)(customEvent.detail?.prompt);
        return;
      }
      setModalData(customEvent.detail);
    };
    window.addEventListener(CREATE_WIDGET_EVENT, handleShow);
    return () => {
      window.removeEventListener(CREATE_WIDGET_EVENT, handleShow);
    };
  }, []);
  if (!modalData) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CreateWidgetModal, {
    prompt: modalData.prompt,
    entryPoint: modalData.entry_point,
    onClose: () => setModalData(null)
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-widget-creation/src/init.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-widget-creation/src/init.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_create_widget__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/create-widget */ "./packages/packages/core/editor-widget-creation/src/components/create-widget.tsx");


function init() {
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'create-widget',
    component: _components_create_widget__WEBPACK_IMPORTED_MODULE_1__.CreateWidget
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-widget-creation/src/interpolate-links.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-widget-creation/src/interpolate-links.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   interpolateLinks: function() { return /* binding */ interpolateLinks; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const LINK_PLACEHOLDER_PATTERN = /\{\{(\w+)}}/g;
const interpolateLinks = (text, links) => {
  return text.split(LINK_PLACEHOLDER_PATTERN).map((part, i) => {
    const link = links[part];
    if (!link) {
      return part;
    }
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Link, {
      key: i,
      sx: {
        px: 0.5
      },
      href: link.href,
      target: "_blank",
      rel: "noopener noreferrer"
    }, link.label);
  });
};

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-mcp":
/*!********************************************!*\
  !*** external ["elementorV2","editorMcp"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorMcp"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/events":
/*!*****************************************!*\
  !*** external ["elementorV2","events"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["events"];

/***/ }),

/***/ "@elementor/icons":
/*!****************************************!*\
  !*** external ["elementorV2","icons"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["icons"];

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
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-widget-creation/src/index.ts ***!
  \********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-widget-creation/src/init.ts");

}();
(window.elementorV2 = window.elementorV2 || {}).editorWidgetCreation = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorWidgetCreation?.init?.();
//# sourceMappingURL=editor-widget-creation.js.map
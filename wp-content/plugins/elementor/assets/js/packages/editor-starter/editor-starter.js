/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-starter/src/components/starter-overlay.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-starter/src/components/starter-overlay.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ StarterOverlay; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_starter__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-starter */ "./packages/packages/core/editor-starter/src/hooks/use-starter.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-starter/src/utils.ts");






const StyledCard = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Card)(({
  theme
}) => ({
  width: theme.spacing(30),
  borderRadius: theme.spacing(1),
  boxShadow: 'none',
  transition: 'border-color 0.2s',
  border: `1px solid ${theme.palette.divider}`,
  paddingBlockEnd: theme.spacing(3),
  '&:hover': {
    borderColor: theme.palette.text.primary
  }
}));
const StyledCardMedia = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CardMedia)(({
  theme
}) => ({
  height: theme.spacing(14),
  objectFit: 'cover',
  backgroundColor: '#d5dadf'
}));
const StyledBlankCardThumbnail = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)('div')(({
  theme
}) => ({
  height: theme.spacing(14),
  backgroundColor: '#d5dadf',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  color: theme.palette.common.white
}));
const StyledCardContent = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CardContent)(({
  theme
}) => ({
  textAlign: 'center',
  paddingBlockStart: theme.spacing(2),
  paddingInline: 0,
  paddingBlockEnd: 0,
  gap: theme.spacing(1),
  display: 'flex',
  flexDirection: 'column'
}));
function StarterOverlay() {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useTheme)();
  const {
    config,
    isDismissing,
    portalContainer,
    dismiss,
    openAiPlanner,
    openTemplatesLibrary,
    onExited
  } = (0,_hooks_use_starter__WEBPACK_IMPORTED_MODULE_4__.useStarter)();
  if (!config || !portalContainer) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Dialog, {
    open: !isDismissing,
    onClose: dismiss,
    container: portalContainer,
    TransitionProps: {
      onExited
    },
    maxWidth: theme.spacing(104),
    BackdropProps: {
      sx: {
        backgroundColor: 'rgba(0, 0, 0, 0.6)'
      }
    },
    PaperProps: {
      sx: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: theme.spacing(3),
        p: 4,
        borderRadius: theme.spacing(2)
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CloseButton, {
    onClick: dismiss,
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Close', 'elementor'),
    sx: {
      position: 'absolute',
      insetBlockStart: theme.spacing(2),
      insetInlineEnd: theme.spacing(2)
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "h5",
    sx: {
      fontFamily: 'Poppins, sans-serif',
      fontWeight: 500,
      color: 'text.primary'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('How do you want to start?', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    spacing: 3,
    justifyContent: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCard, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CardActionArea, {
    onClick: openAiPlanner,
    sx: {
      '&:hover .MuiCardActionArea-focusHighlight': {
        opacity: 0
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCardMedia, {
    component: "img",
    image: (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getAssetUrl)('ai-site-planner.png'),
    alt: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('AI Site Planner', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCardContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle1",
    color: "text.primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('AI Site Planner', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Generate your wireframe with AI', 'elementor'))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCard, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CardActionArea, {
    onClick: openTemplatesLibrary,
    sx: {
      '&:hover .MuiCardActionArea-focusHighlight': {
        opacity: 0
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCardMedia, {
    component: "img",
    image: (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getAssetUrl)('website-templates.png'),
    alt: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Website templates', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCardContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle1",
    color: "text.primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Website templates', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Start with a ready-made design', 'elementor'))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCard, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CardActionArea, {
    onClick: dismiss,
    sx: {
      '&:hover .MuiCardActionArea-focusHighlight': {
        opacity: 0
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledBlankCardThumbnail, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CirclePlusIcon, {
    sx: {
      fontSize: theme.spacing(5)
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledCardContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle1",
    color: "text.primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Blank site', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Start from scratch', 'elementor')))))));
}

/***/ }),

/***/ "./packages/packages/core/editor-starter/src/hooks/use-starter.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-starter/src/hooks/use-starter.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStarter: function() { return /* binding */ useStarter; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-starter/src/utils.ts");



const EDITOR_WRAPPER_SELECTOR = '#elementor-editor-wrapper';
const STARTER_CONTAINER_ID = 'elementor-starter-container';
function markStarterDismissed(config) {
  _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
    path: config.restPath,
    method: 'POST',
    data: {
      starter_dismissed: true
    }
  });
}
function useStarter() {
  const [config, setConfig] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [isDismissing, setIsDismissing] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [portalContainer, setPortalContainer] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const dismissedRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  const dismiss = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (!config || dismissedRef.current) {
      return;
    }
    dismissedRef.current = true;
    setIsDismissing(true);
    (0,_utils__WEBPACK_IMPORTED_MODULE_2__.deleteStarterConfig)();
  }, [config]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const insertStarters = () => {
      const starterConfig = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getStarterConfig)();
      if (!starterConfig) {
        return;
      }
      const wrapper = document.querySelector(EDITOR_WRAPPER_SELECTOR);
      if (!wrapper) {
        return;
      }
      const container = document.createElement('div');
      container.id = STARTER_CONTAINER_ID;
      wrapper.prepend(container);
      setConfig(starterConfig);
      setPortalContainer(container);
      markStarterDismissed(starterConfig);
    };
    const onInsertStarters = event => {
      const detail = event?.detail;
      if (detail?.command === 'editor/documents/attach-preview') {
        insertStarters();
      }
    };
    window.addEventListener('elementor/commands/run/after', onInsertStarters);
    return () => window.removeEventListener('elementor/commands/run/after', onInsertStarters);
  }, []);
  function openTemplatesLibrary() {
    if (config?.kitLibraryUrl) {
      const url = new URL(config.kitLibraryUrl, window.location.origin);
      url.searchParams.set('referrer', 'onboarding');
      window.open(url.toString(), '_blank', 'noopener,noreferrer');
    }
  }
  function openAiPlanner() {
    if (config?.aiPlannerUrl) {
      window.open(config.aiPlannerUrl, '_blank', 'noopener,noreferrer');
    }
  }
  const onExited = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    portalContainer?.remove();
    setPortalContainer(null);
    setConfig(null);
  }, [portalContainer]);
  return {
    config,
    isDismissing,
    portalContainer,
    dismiss,
    openAiPlanner,
    openTemplatesLibrary,
    onExited
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-starter/src/init.tsx":
/*!************************************************************!*\
  !*** ./packages/packages/core/editor-starter/src/init.tsx ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_starter_overlay__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/starter-overlay */ "./packages/packages/core/editor-starter/src/components/starter-overlay.tsx");


function init() {
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'starter-overlay',
    component: _components_starter_overlay__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-starter/src/utils.ts":
/*!************************************************************!*\
  !*** ./packages/packages/core/editor-starter/src/utils.ts ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deleteStarterConfig: function() { return /* binding */ deleteStarterConfig; },
/* harmony export */   getAssetUrl: function() { return /* binding */ getAssetUrl; },
/* harmony export */   getStarterConfig: function() { return /* binding */ getStarterConfig; }
/* harmony export */ });
const ONBOARDING_ASSETS_PATH = 'images/app/onboarding/';
function getStarterConfig() {
  return window.elementor?.config?.starter ?? null;
}
function deleteStarterConfig() {
  const config = window.elementor?.config;
  if (config) {
    delete config.starter;
  }
}
function getAssetUrl(fileName) {
  const baseUrl = window.elementorCommon?.config?.urls?.assets ?? '';
  return baseUrl ? `${baseUrl}${ONBOARDING_ASSETS_PATH}${fileName}` : `${ONBOARDING_ASSETS_PATH}${fileName}`;
}

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

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

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["apiFetch"];

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
/*!************************************************************!*\
  !*** ./packages/packages/core/editor-starter/src/index.ts ***!
  \************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-starter/src/init.tsx");

}();
(window.elementorV2 = window.elementorV2 || {}).editorStarter = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorStarter?.init?.();
//# sourceMappingURL=editor-starter.js.map
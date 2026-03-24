/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay-expired.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/replay-expired.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReplayExpired: function() { return /* binding */ ReplayExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function ReplayExpired({
  value,
  disabled = true
}) {
  const options = [{
    value: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Yes', 'elementor-pro'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CheckIcon, {
      fontSize: size
    })
  }, {
    value: false,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No', 'elementor-pro'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MinusIcon, {
      fontSize: size
    })
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: () => {},
    value: value,
    disabled: disabled
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/replay.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Replay: function() { return /* binding */ Replay; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function Replay({
  value,
  onChange
}) {
  const options = [{
    value: false,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No', 'elementor-pro'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MinusIcon, {
      fontSize: size
    })
  }, {
    value: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Yes', 'elementor-pro'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CheckIcon, {
      fontSize: size
    })
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: onChange,
    value: value
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger-expired.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger-expired.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TriggerExpired: function() { return /* binding */ TriggerExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function TriggerExpired({
  value,
  onChange
}) {
  const availableTriggers = Object.entries({
    load: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Page load', 'elementor-pro'),
    scrollIn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Scroll into view', 'elementor-pro'),
    scrollOn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('While scrolling', 'elementor-pro')
  }).map(([key, label]) => ({
    key,
    label,
    disabled: key === 'scrollOn'
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: event => onChange(event.target.value),
    value: value
  }, availableTriggers.map(trigger => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuListItem, {
      key: trigger.key,
      value: trigger.key,
      disabled: trigger.disabled
    }, trigger.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Trigger: function() { return /* binding */ Trigger; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function Trigger({
  value,
  onChange
}) {
  const availableTriggers = Object.entries({
    load: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Page load', 'elementor-pro'),
    scrollIn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Scroll into view', 'elementor-pro'),
    scrollOn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('While scrolling', 'elementor-pro')
  }).map(([key, label]) => ({
    key,
    label
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: event => onChange(event.target.value),
    value: value
  }, availableTriggers.map(trigger => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuListItem, {
      key: trigger.key,
      value: trigger.key
    }, trigger.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/init.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/init.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls_extended__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls-extended */ "@elementor/editor-controls-extended");
/* harmony import */ var _elementor_editor_controls_extended__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls_extended__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_controls_replay__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/controls/replay */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay.tsx");
/* harmony import */ var _components_controls_replay_expired__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/controls/replay-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay-expired.tsx");
/* harmony import */ var _components_controls_trigger__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/controls/trigger */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger.tsx");
/* harmony import */ var _components_controls_trigger_expired__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/controls/trigger-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger-expired.tsx");






async function init() {
  const isLicenseExpired = await (0,_elementor_editor_controls_extended__WEBPACK_IMPORTED_MODULE_0__.getIsLicenseExpired)();
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.registerInteractionsControl)({
    type: 'replay',
    component: isLicenseExpired ? _components_controls_replay_expired__WEBPACK_IMPORTED_MODULE_3__.ReplayExpired : _components_controls_replay__WEBPACK_IMPORTED_MODULE_2__.Replay,
    options: ['yes', 'no']
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.registerInteractionsControl)({
    type: 'trigger',
    component: isLicenseExpired ? _components_controls_trigger_expired__WEBPACK_IMPORTED_MODULE_5__.TriggerExpired : _components_controls_trigger__WEBPACK_IMPORTED_MODULE_4__.Trigger,
    options: ['load', 'scrollIn', 'scrollOn']
  });
}

/***/ }),

/***/ "react":
/*!**************************!*\
  !*** external ["React"] ***!
  \**************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "@elementor/editor-controls":
/*!*************************************************!*\
  !*** external ["elementorV2","editorControls"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorControls"];

/***/ }),

/***/ "@elementor/editor-controls-extended":
/*!*********************************************************!*\
  !*** external ["elementorV2","editorControlsExtended"] ***!
  \*********************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorControlsExtended"];

/***/ }),

/***/ "@elementor/editor-interactions":
/*!*****************************************************!*\
  !*** external ["elementorV2","editorInteractions"] ***!
  \*****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorInteractions"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!*************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/index.ts ***!
  \*************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/pro/editor-interactions-extended/src/init.ts");

}();
(window.elementorV2 = window.elementorV2 || {}).editorInteractionsExtended = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorInteractionsExtended?.init?.();
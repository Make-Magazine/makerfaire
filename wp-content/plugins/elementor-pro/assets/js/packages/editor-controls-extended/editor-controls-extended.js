/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/pro/editor-controls-extended/src/controls/attributes-control.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/controls/attributes-control.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AttributesControl: function() { return /* binding */ AttributesControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const AttributesControl = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.createControl)(() => {
  const getHelperText = (key, value) => {
    if (value && !key) {
      return {
        keyHelper: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Empty attribute names aren't valid and won't render on the page.", 'elementor-pro')
      };
    }
    return {};
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.KeyValueControl, {
    keyName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Name', 'elementor-pro'),
    valueName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Value', 'elementor-pro'),
    regexKey: "^[a-zA-Z0-9_-]*$",
    validationErrorMessage: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Names can only use letters, numbers, dashes (-) and underscores (_).', 'elementor-pro'),
    getHelperText: getHelperText
  });
});

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/extend-transition-properties.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/extend-transition-properties.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extendTransitionProperties: function() { return /* binding */ extendTransitionProperties; },
/* harmony export */   transitionProperties: function() { return /* reexport safe */ _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties; },
/* harmony export */   transitionsItemsList: function() { return /* reexport safe */ _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionsItemsList; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);


function extendTransitionProperties() {
  // Core transition properties are limited to 'All Properties' option, so we need to extend it.
  if (_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties && _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.length === 1) {
    const commonProperties = _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.find(category => category.label === (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Common', 'elementor-pro'));
    if (commonProperties) {
      commonProperties.label = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Default', 'elementor-pro');
    }
    _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.push({
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Margin', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Margin (all)', 'elementor-pro'),
        value: 'margin'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Margin bottom', 'elementor-pro'),
        value: 'margin-block-end'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Margin left', 'elementor-pro'),
        value: 'margin-inline-start'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Margin right', 'elementor-pro'),
        value: 'margin-inline-end'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Margin top', 'elementor-pro'),
        value: 'margin-block-start'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Padding', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Padding (all)', 'elementor-pro'),
        value: 'padding'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Padding bottom', 'elementor-pro'),
        value: 'padding-block-end'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Padding left', 'elementor-pro'),
        value: 'padding-inline-start'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Padding right', 'elementor-pro'),
        value: 'padding-inline-end'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Padding top', 'elementor-pro'),
        value: 'padding-block-start'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Flex', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Flex (all)', 'elementor-pro'),
        value: 'flex'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Flex grow', 'elementor-pro'),
        value: 'flex-grow'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Flex shrink', 'elementor-pro'),
        value: 'flex-shrink'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Flex basis', 'elementor-pro'),
        value: 'flex-basis'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Size', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Width', 'elementor-pro'),
        value: 'width'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Height', 'elementor-pro'),
        value: 'height'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Max height', 'elementor-pro'),
        value: 'max-height'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Max width', 'elementor-pro'),
        value: 'max-width'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Min height', 'elementor-pro'),
        value: 'min-height'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Min width', 'elementor-pro'),
        value: 'min-width'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Position', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Top', 'elementor-pro'),
        value: 'top'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Left', 'elementor-pro'),
        value: 'left'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Bottom', 'elementor-pro'),
        value: 'bottom'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Right', 'elementor-pro'),
        value: 'right'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Z-Index', 'elementor-pro'),
        value: 'z-index'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Typography', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Font color', 'elementor-pro'),
        value: 'color'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Font size', 'elementor-pro'),
        value: 'font-size'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Line height', 'elementor-pro'),
        value: 'line-height'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Letter spacing', 'elementor-pro'),
        value: 'letter-spacing'
      },
      // { label: __('Text indent', 'elementor-pro'), value: 'text-indent' },
      // { label: __('Text shadow', 'elementor-pro'), value: 'text-shadow' },
      {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Word spacing', 'elementor-pro'),
        value: 'word-spacing'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Font variations', 'elementor-pro'),
        value: 'font-variation-settings'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Text stroke color', 'elementor-pro'),
        value: '-webkit-text-stroke-color'
      }
      // { label: __('Text underline offset', 'elementor-pro'), value: 'text-underline-offset' },
      // { label: __('Text decoration color', 'elementor-pro'), value: 'text-decoration-color'	 },
      ]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Background', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Background color', 'elementor-pro'),
        value: 'background-color'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Background position', 'elementor-pro'),
        value: 'background-position'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Box shadow', 'elementor-pro'),
        value: 'box-shadow'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Border', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Border (all)', 'elementor-pro'),
        value: 'border'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Border radius', 'elementor-pro'),
        value: 'border-radius'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Border color', 'elementor-pro'),
        value: 'border-color'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Border width', 'elementor-pro'),
        value: 'border-width'
      }]
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Effects', 'elementor-pro'),
      type: 'category',
      properties: [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Opacity', 'elementor-pro'),
        value: 'opacity'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Transform (all)', 'elementor-pro'),
        value: 'transform'
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Filter (all)', 'elementor-pro'),
        value: 'filter'
      }]
    });
    _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionsItemsList.splice(0, _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionsItemsList.length, ..._elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.map(category => ({
      label: category.label,
      items: category.properties.map(property => property.label)
    })));
  }
}


/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/init.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/init.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _extend_transition_properties__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./extend-transition-properties */ "./packages/packages/pro/editor-controls-extended/src/extend-transition-properties.ts");

function init() {
  (0,_extend_transition_properties__WEBPACK_IMPORTED_MODULE_0__.extendTransitionProperties)();
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
/*!*********************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/index.ts ***!
  \*********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AttributesControl: function() { return /* reexport safe */ _controls_attributes_control__WEBPACK_IMPORTED_MODULE_1__.AttributesControl; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/pro/editor-controls-extended/src/init.ts");
/* harmony import */ var _controls_attributes_control__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./controls/attributes-control */ "./packages/packages/pro/editor-controls-extended/src/controls/attributes-control.tsx");


(0,_init__WEBPACK_IMPORTED_MODULE_0__.init)();
}();
(window.elementorV2 = window.elementorV2 || {}).editorControlsExtended = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorControlsExtended?.init?.();
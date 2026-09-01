/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-elements-panel/src/components/elements-panel-tab.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/components/elements-panel-tab.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementsPanelTab: function() { return /* binding */ ElementsPanelTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _hooks_use_active_tab__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../hooks/use-active-tab */ "./packages/packages/core/editor-elements-panel/src/hooks/use-active-tab.ts");



const PANEL_WRAPPER_ID = 'elementor-panel-elements-wrapper';
function ElementsPanelTab() {
  const tab = (0,_hooks_use_active_tab__WEBPACK_IMPORTED_MODULE_2__.useActiveTab)();
  const TabComponent = tab?.component;
  const container = document.getElementById(PANEL_WRAPPER_ID);
  return TabComponent && container ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Portal, {
    container: container
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TabComponent, null)) : null;
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/consts.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/consts.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LEGACY_ELEMENTS_PANEL_COMPONENT_NAME: function() { return /* binding */ LEGACY_ELEMENTS_PANEL_COMPONENT_NAME; },
/* harmony export */   LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX: function() { return /* binding */ LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX; }
/* harmony export */ });
const LEGACY_ELEMENTS_PANEL_COMPONENT_NAME = 'panel/elements';
const LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX = `${LEGACY_ELEMENTS_PANEL_COMPONENT_NAME}/`;

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/hooks/use-active-tab.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/hooks/use-active-tab.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useActiveTab: function() { return /* binding */ useActiveTab; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../consts */ "./packages/packages/core/editor-elements-panel/src/consts.ts");
/* harmony import */ var _tabs__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../tabs */ "./packages/packages/core/editor-elements-panel/src/tabs.ts");
/* harmony import */ var _utils_get_window__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/get-window */ "./packages/packages/core/editor-elements-panel/src/utils/get-window.ts");




function useActiveTab() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent)(_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeCloseEvent)(_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX)], () => {
    const panelRoute = (0,_utils_get_window__WEBPACK_IMPORTED_MODULE_3__.getWindow)().$e.routes.getCurrent()?.panel;
    if (!panelRoute || !panelRoute.startsWith(_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX)) {
      return null;
    }
    const tab = panelRoute.replace(_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX, '');
    return (0,_tabs__WEBPACK_IMPORTED_MODULE_2__.getTab)(tab) ?? null;
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/init.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/init.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_elements_panel_tab__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/elements-panel-tab */ "./packages/packages/core/editor-elements-panel/src/components/elements-panel-tab.tsx");


function init() {
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'editor-elements-panel-tab',
    component: _components_elements_panel_tab__WEBPACK_IMPORTED_MODULE_1__.ElementsPanelTab
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/inject-tab.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/inject-tab.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   injectTab: function() { return /* binding */ injectTab; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./consts */ "./packages/packages/core/editor-elements-panel/src/consts.ts");
/* harmony import */ var _tabs__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./tabs */ "./packages/packages/core/editor-elements-panel/src/tabs.ts");
/* harmony import */ var _utils_create_legacy_view__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/create-legacy-view */ "./packages/packages/core/editor-elements-panel/src/utils/create-legacy-view.ts");
/* harmony import */ var _utils_create_tab_nav_item__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/create-tab-nav-item */ "./packages/packages/core/editor-elements-panel/src/utils/create-tab-nav-item.ts");
/* harmony import */ var _utils_get_legacy_elements_panel_component__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/get-legacy-elements-panel-component */ "./packages/packages/core/editor-elements-panel/src/utils/get-legacy-elements-panel-component.ts");






function injectTab({
  id,
  label,
  component,
  position
}) {
  (0,_tabs__WEBPACK_IMPORTED_MODULE_2__.registerTab)({
    id,
    label,
    component
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), () => {
    window.elementor?.hooks?.addFilter('panel/elements/regionViews', (regions, {
      elements
    }) => {
      // Creating a empty legacy view that will be replaced by react component.
      regions[id] = {
        region: elements,
        view: (0,_utils_create_legacy_view__WEBPACK_IMPORTED_MODULE_3__.createLegacyView)()
      };
      return regions;
    });
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/panel/init'), () => {
    // when adding a tab to the legacy elements panel, it will generate new route based on the id.
    (0,_utils_get_legacy_elements_panel_component__WEBPACK_IMPORTED_MODULE_5__.getLegacyElementsPanelComponent)().addTab(id, {
      title: label
    });
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent)(_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX), e => {
    const route = `${_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_ROUTE_PREFIX}${id}`;
    (0,_utils_create_tab_nav_item__WEBPACK_IMPORTED_MODULE_4__.createTabNavItem)({
      id,
      label,
      route,
      isActive: 'route' in e && e.route === route,
      position
    });
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/tabs.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/tabs.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getTab: function() { return /* binding */ getTab; },
/* harmony export */   registerTab: function() { return /* binding */ registerTab; }
/* harmony export */ });
const registry = new Map();
const DEFAULT_PRIORITY = 10;
function registerTab({
  id,
  priority = DEFAULT_PRIORITY,
  ...props
}) {
  const existing = registry.get(id);
  if (!existing || priority <= existing.priority) {
    registry.set(id, {
      ...props,
      priority
    });
  }
}
function getTab(id) {
  return registry.get(id) ?? null;
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/utils/create-legacy-view.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/utils/create-legacy-view.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createLegacyView: function() { return /* binding */ createLegacyView; }
/* harmony export */ });
/* harmony import */ var _get_window__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-window */ "./packages/packages/core/editor-elements-panel/src/utils/get-window.ts");

function createLegacyView() {
  return (0,_get_window__WEBPACK_IMPORTED_MODULE_0__.getWindow)().Marionette.CompositeView.extend({
    template: `<div></div>`,
    initialize() {
      (0,_get_window__WEBPACK_IMPORTED_MODULE_0__.getWindow)().elementor.getPanelView().getCurrentPageView().search.reset();
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/utils/create-tab-nav-item.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/utils/create-tab-nav-item.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createTabNavItem: function() { return /* binding */ createTabNavItem; }
/* harmony export */ });
/* harmony import */ var _get_navigation_wrapper_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-navigation-wrapper-element */ "./packages/packages/core/editor-elements-panel/src/utils/get-navigation-wrapper-element.ts");
/* harmony import */ var _get_window__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-window */ "./packages/packages/core/editor-elements-panel/src/utils/get-window.ts");


function createTabNavItem({
  id,
  label,
  route,
  isActive,
  position
}) {
  const wrapper = (0,_get_navigation_wrapper_element__WEBPACK_IMPORTED_MODULE_0__.getNavigationWrapperElement)();
  const btn = document.createElement('button');
  btn.className = ['elementor-component-tab', 'elementor-panel-navigation-tab', isActive ? 'elementor-active' : ''].filter(Boolean).join(' ');
  btn.setAttribute('data-tab', id);
  btn.textContent = label;
  btn.addEventListener('click', () => {
    (0,_get_window__WEBPACK_IMPORTED_MODULE_1__.getWindow)().$e.route(route);
  });
  if (position !== undefined && wrapper.children[position]) {
    wrapper.insertBefore(btn, wrapper.children[position]);
  } else {
    wrapper.appendChild(btn);
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/utils/get-legacy-elements-panel-component.ts":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/utils/get-legacy-elements-panel-component.ts ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getLegacyElementsPanelComponent: function() { return /* binding */ getLegacyElementsPanelComponent; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../consts */ "./packages/packages/core/editor-elements-panel/src/consts.ts");
/* harmony import */ var _get_window__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get-window */ "./packages/packages/core/editor-elements-panel/src/utils/get-window.ts");



const ComponentNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'e_component_not_found',
  message: 'Elementor component not found'
});
function getLegacyElementsPanelComponent() {
  const eComponent = (0,_get_window__WEBPACK_IMPORTED_MODULE_2__.getWindow)().$e.components.get(_consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_COMPONENT_NAME);
  if (!eComponent) {
    throw new ComponentNotFoundError({
      context: {
        componentId: _consts__WEBPACK_IMPORTED_MODULE_1__.LEGACY_ELEMENTS_PANEL_COMPONENT_NAME
      }
    });
  }
  return eComponent;
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/utils/get-navigation-wrapper-element.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/utils/get-navigation-wrapper-element.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getNavigationWrapperElement: function() { return /* binding */ getNavigationWrapperElement; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const NAVIGATION_WRAPPER_ID = 'elementor-panel-elements-navigation';
const ElementsPanelWrapperElementNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'elements_panel_wrapper_element_not_found',
  message: 'Elementor Elements Panel wrapper element not found'
});
function getNavigationWrapperElement() {
  const wrapper = document.getElementById(NAVIGATION_WRAPPER_ID);
  if (!wrapper) {
    throw new ElementsPanelWrapperElementNotFoundError();
  }
  return wrapper;
}

/***/ }),

/***/ "./packages/packages/core/editor-elements-panel/src/utils/get-window.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/utils/get-window.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getWindow: function() { return /* binding */ getWindow; }
/* harmony export */ });
function getWindow() {
  return window;
}

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/ui":
/*!*************************************!*\
  !*** external ["elementorV2","ui"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["ui"];

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
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-elements-panel/src/index.ts ***!
  \*******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   injectTab: function() { return /* reexport safe */ _inject_tab__WEBPACK_IMPORTED_MODULE_1__.injectTab; },
/* harmony export */   registerTab: function() { return /* reexport safe */ _tabs__WEBPACK_IMPORTED_MODULE_2__.registerTab; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-elements-panel/src/init.ts");
/* harmony import */ var _inject_tab__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./inject-tab */ "./packages/packages/core/editor-elements-panel/src/inject-tab.ts");
/* harmony import */ var _tabs__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./tabs */ "./packages/packages/core/editor-elements-panel/src/tabs.ts");



}();
(window.elementorV2 = window.elementorV2 || {}).editorElementsPanel = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorElementsPanel?.init?.();
//# sourceMappingURL=editor-elements-panel.js.map
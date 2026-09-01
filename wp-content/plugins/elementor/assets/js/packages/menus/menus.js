/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/menus/src/action.tsx":
/*!*****************************************************!*\
  !*** ./packages/packages/libs/menus/src/action.tsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Action; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const SIZE = 'tiny';
function Action({
  title,
  visible = true,
  icon: Icon,
  onClick
}) {
  if (!visible) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    placement: "top",
    title: title,
    arrow: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.IconButton, {
    "aria-label": title,
    size: SIZE,
    onClick: onClick
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    fontSize: SIZE
  })));
}

/***/ }),

/***/ "./packages/packages/libs/menus/src/controls-actions.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/libs/menus/src/controls-actions.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   controlActionsMenu: function() { return /* binding */ controlActionsMenu; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _action__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./action */ "./packages/packages/libs/menus/src/action.tsx");
/* harmony import */ var _create_menu__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-menu */ "./packages/packages/libs/menus/src/create-menu.ts");



const controlActionsMenu = (0,_create_menu__WEBPACK_IMPORTED_MODULE_2__.createMenu)({
  components: {
    Action: _action__WEBPACK_IMPORTED_MODULE_1__["default"],
    PopoverAction: _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_0__.PopoverAction
  }
});

/***/ }),

/***/ "./packages/packages/libs/menus/src/create-menu.ts":
/*!*********************************************************!*\
  !*** ./packages/packages/libs/menus/src/create-menu.ts ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createMenu: function() { return /* binding */ createMenu; }
/* harmony export */ });
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _create_register_item__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-register-item */ "./packages/packages/libs/menus/src/create-register-item.tsx");
/* harmony import */ var _create_use_menu_items__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./create-use-menu-items */ "./packages/packages/libs/menus/src/create-use-menu-items.ts");




function createSubscription() {
  const listeners = new Set();
  return {
    subscribe: listener => {
      listeners.add(listener);
      return () => listeners.delete(listener);
    },
    notify: () => listeners.forEach(listener => listener())
  };
}
function createMenu({
  groups = [],
  components
}) {
  const locations = createLocations([...groups, 'default']);
  const {
    subscribe,
    notify
  } = createSubscription();
  const registerFns = createRegisterFns(locations, components, notify);
  const useMenuItems = (0,_create_use_menu_items__WEBPACK_IMPORTED_MODULE_3__.createUseMenuItems)(locations, subscribe);
  return {
    useMenuItems,
    ...registerFns
  };
}
function createLocations(groups) {
  return groups.reduce((acc, group) => {
    acc[group] = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();
    return acc;
  }, {});
}
function createRegisterFns(locations, components, notify) {
  return Object.entries(components).reduce((acc, [key, component]) => {
    const name = `register${(0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.capitalize)(key)}`;
    return {
      ...acc,
      [name]: (0,_create_register_item__WEBPACK_IMPORTED_MODULE_2__.createRegisterItem)(locations, component, notify)
    };
  }, {});
}

/***/ }),

/***/ "./packages/packages/libs/menus/src/create-register-item.tsx":
/*!*******************************************************************!*\
  !*** ./packages/packages/libs/menus/src/create-register-item.tsx ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createRegisterItem: function() { return /* binding */ createRegisterItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }

function createRegisterItem(locations, component, notify) {
  return ({
    id,
    group = 'default',
    priority = 10,
    overwrite = false,
    props: _props,
    useProps: _useProps
  }) => {
    if (!(group in locations)) {
      return;
    }
    const Component = component;
    const useProps = _useProps || (() => _props);
    const InjectedComponent = props => {
      const componentProps = useProps();
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Component, _extends({}, props, componentProps));
    };
    locations[group].inject({
      id,
      component: InjectedComponent,
      options: {
        priority,
        overwrite
      }
    });
    notify();
  };
}

/***/ }),

/***/ "./packages/packages/libs/menus/src/create-use-menu-items.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/libs/menus/src/create-use-menu-items.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createUseMenuItems: function() { return /* binding */ createUseMenuItems; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function createUseMenuItems(locations, subscribe) {
  let snapshot = null;
  subscribe(() => {
    snapshot = null;
  });
  const getMenuItems = () => {
    if (snapshot) {
      return snapshot;
    }
    snapshot = Object.entries(locations).reduce((carry, [groupName, location]) => {
      const items = location.getInjections().map(injection => ({
        id: injection.id,
        MenuItem: injection.component
      }));
      return {
        ...carry,
        [groupName]: items
      };
    }, {});
    return snapshot;
  };
  return () => (0,react__WEBPACK_IMPORTED_MODULE_0__.useSyncExternalStore)(subscribe, getMenuItems);
}

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/locations":
/*!********************************************!*\
  !*** external ["elementorV2","locations"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["locations"];

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
/*!***************************************************!*\
  !*** ./packages/packages/libs/menus/src/index.ts ***!
  \***************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   controlActionsMenu: function() { return /* reexport safe */ _controls_actions__WEBPACK_IMPORTED_MODULE_1__.controlActionsMenu; },
/* harmony export */   createMenu: function() { return /* reexport safe */ _create_menu__WEBPACK_IMPORTED_MODULE_0__.createMenu; }
/* harmony export */ });
/* harmony import */ var _create_menu__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create-menu */ "./packages/packages/libs/menus/src/create-menu.ts");
/* harmony import */ var _controls_actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./controls-actions */ "./packages/packages/libs/menus/src/controls-actions.ts");


}();
(window.elementorV2 = window.elementorV2 || {}).menus = __webpack_exports__;
/******/ })()
;
window.elementorV2.menus?.init?.();
//# sourceMappingURL=menus.js.map
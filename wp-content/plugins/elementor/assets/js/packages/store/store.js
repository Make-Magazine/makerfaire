/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@reduxjs/toolkit":
/*!****************************************************!*\
  !*** external ["elementorVendors","reduxToolkit"] ***!
  \****************************************************/
/***/ (function(module) {

module.exports = window["elementorVendors"]["reduxToolkit"];

/***/ }),

/***/ "react-redux":
/*!**************************************************!*\
  !*** external ["elementorVendors","reactRedux"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorVendors"]["reactRedux"];

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
  !*** ./packages/packages/libs/store/src/index.ts ***!
  \***************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __StoreProvider: function() { return /* reexport safe */ react_redux__WEBPACK_IMPORTED_MODULE_1__.Provider; },
/* harmony export */   __addMiddleware: function() { return /* binding */ addMiddleware; },
/* harmony export */   __createAction: function() { return /* reexport safe */ _reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__.createAction; },
/* harmony export */   __createAsyncThunk: function() { return /* reexport safe */ _reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__.createAsyncThunk; },
/* harmony export */   __createSelector: function() { return /* reexport safe */ _reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__.createSelector; },
/* harmony export */   __createSlice: function() { return /* reexport safe */ _reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__.createSlice; },
/* harmony export */   __createStore: function() { return /* binding */ createStore; },
/* harmony export */   __deleteStore: function() { return /* binding */ deleteStore; },
/* harmony export */   __dispatch: function() { return /* binding */ dispatch; },
/* harmony export */   __getState: function() { return /* binding */ getState; },
/* harmony export */   __getStore: function() { return /* binding */ getStore; },
/* harmony export */   __registerSlice: function() { return /* binding */ registerSlice; },
/* harmony export */   __subscribe: function() { return /* binding */ subscribe; },
/* harmony export */   __subscribeWithSelector: function() { return /* binding */ subscribeWithSelector; },
/* harmony export */   __useDispatch: function() { return /* reexport safe */ react_redux__WEBPACK_IMPORTED_MODULE_1__.useDispatch; },
/* harmony export */   __useSelector: function() { return /* reexport safe */ react_redux__WEBPACK_IMPORTED_MODULE_1__.useSelector; }
/* harmony export */ });
/* harmony import */ var _reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @reduxjs/toolkit */ "@reduxjs/toolkit");
/* harmony import */ var _reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "react-redux");
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_redux__WEBPACK_IMPORTED_MODULE_1__);




/**
 * Usage:
 *
 * const mySlice = addSlice( ... );
 *
 * type MySliceState = SliceState<typeof mySlice>;
 *
 * const value = useSelector( ( state: MySliceState ) => state.mySlice.value );
 */

// The `configureStore` function from Redux Toolkit infers its actions from the `reducers`
// key of the initialization object. This is fine when creating the store statically, but
// breaks in our case since we create the store dynamically, which means that TypeScript
// can't infer the types. Therefore, we force the store to accept any actions using a
// generic store type.

// eslint-disable-next-line @typescript-eslint/no-explicit-any

let instance = null;
let slices = {};
const pendingActions = [];
const middlewares = new Set();
const getReducers = () => {
  const reducers = Object.entries(slices).reduce((reducersData, [name, slice]) => {
    reducersData[name] = slice.reducer;
    return reducersData;
  }, {});
  return (0,_reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__.combineReducers)(reducers);
};
function registerSlice(slice) {
  if (slices[slice.name]) {
    throw new Error(`Slice with name "${slice.name}" already exists.`);
  }
  slices[slice.name] = slice;
}
const addMiddleware = middleware => {
  middlewares.add(middleware);
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any -- See the comment above about `AnyStore`
const dispatch = action => {
  if (!instance) {
    pendingActions.push(action);
    return;
  }
  return instance.dispatch(action);
};
const getState = () => {
  if (!instance) {
    throw new Error('The store instance does not exist.');
  }
  return instance.getState();
};
const subscribe = listener => {
  if (!instance) {
    throw new Error('The store instance does not exist.');
  }
  return instance.subscribe(listener);
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const subscribeWithSelector = (selector, listener) => {
  let prevState = selector(getState());
  return subscribe(() => {
    const nextState = selector(getState());
    if (prevState === nextState) {
      return;
    }
    prevState = nextState;
    listener(nextState);
  });
};
const createStore = () => {
  if (instance) {
    throw new Error('The store instance already exists.');
  }
  instance = (0,_reduxjs_toolkit__WEBPACK_IMPORTED_MODULE_0__.configureStore)({
    reducer: getReducers(),
    middleware: getDefaultMiddleware => {
      return [...getDefaultMiddleware(), ...Array.from(middlewares)];
    }
  });
  if (pendingActions.length) {
    pendingActions.forEach(action => dispatch(action));
    pendingActions.length = 0;
  }
  return instance;
};
const getStore = () => {
  return instance;
};
const deleteStore = () => {
  instance = null;
  slices = {};
  pendingActions.length = 0;
  middlewares.clear();
};

}();
(window.elementorV2 = window.elementorV2 || {}).store = __webpack_exports__;
/******/ })()
;
window.elementorV2.store?.init?.();
//# sourceMappingURL=store.js.map
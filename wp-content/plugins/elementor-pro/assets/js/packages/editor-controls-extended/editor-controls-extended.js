/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@monaco-editor/loader/lib/es/_virtual/_rollupPluginBabelHelpers.js":
/*!*****************************************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/_virtual/_rollupPluginBabelHelpers.js ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   arrayLikeToArray: function() { return /* binding */ _arrayLikeToArray; },
/* harmony export */   arrayWithHoles: function() { return /* binding */ _arrayWithHoles; },
/* harmony export */   defineProperty: function() { return /* binding */ _defineProperty; },
/* harmony export */   iterableToArrayLimit: function() { return /* binding */ _iterableToArrayLimit; },
/* harmony export */   nonIterableRest: function() { return /* binding */ _nonIterableRest; },
/* harmony export */   objectSpread2: function() { return /* binding */ _objectSpread2; },
/* harmony export */   objectWithoutProperties: function() { return /* binding */ _objectWithoutProperties; },
/* harmony export */   objectWithoutPropertiesLoose: function() { return /* binding */ _objectWithoutPropertiesLoose; },
/* harmony export */   slicedToArray: function() { return /* binding */ _slicedToArray; },
/* harmony export */   toPrimitive: function() { return /* binding */ _toPrimitive; },
/* harmony export */   toPropertyKey: function() { return /* binding */ _toPropertyKey; },
/* harmony export */   unsupportedIterableToArray: function() { return /* binding */ _unsupportedIterableToArray; }
/* harmony export */ });
function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
function _defineProperty(e, r, t) {
  return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: true,
    configurable: true,
    writable: true
  }) : e[r] = t, e;
}
function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = true,
      o = false;
    try {
      if (i = (t = t.call(r)).next, 0 === l) ; else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = true, n = r;
    } finally {
      try {
        if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
function ownKeys(e, r) {
  var t = Object.keys(e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(e);
    r && (o = o.filter(function (r) {
      return Object.getOwnPropertyDescriptor(e, r).enumerable;
    })), t.push.apply(t, o);
  }
  return t;
}
function _objectSpread2(e) {
  for (var r = 1; r < arguments.length; r++) {
    var t = null != arguments[r] ? arguments[r] : {};
    r % 2 ? ownKeys(Object(t), true).forEach(function (r) {
      _defineProperty(e, r, t[r]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) {
      Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
    });
  }
  return e;
}
function _objectWithoutProperties(e, t) {
  if (null == e) return {};
  var o,
    r,
    i = _objectWithoutPropertiesLoose(e, t);
  if (Object.getOwnPropertySymbols) {
    var n = Object.getOwnPropertySymbols(e);
    for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
  }
  return i;
}
function _objectWithoutPropertiesLoose(r, e) {
  if (null == r) return {};
  var t = {};
  for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
    if (-1 !== e.indexOf(n)) continue;
    t[n] = r[n];
  }
  return t;
}
function _slicedToArray(r, e) {
  return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest();
}
function _toPrimitive(t, r) {
  if ("object" != typeof t || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r);
    if ("object" != typeof i) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
function _toPropertyKey(t) {
  var i = _toPrimitive(t, "string");
  return "symbol" == typeof i ? i : i + "";
}
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return _arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0;
  }
}




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/config/index.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/config/index.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ config; }
/* harmony export */ });
var config = {
  paths: {
    vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.55.1/min/vs'
  }
};




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/index.js":
/*!************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/index.js ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* reexport safe */ _loader_index_js__WEBPACK_IMPORTED_MODULE_0__["default"]; }
/* harmony export */ });
/* harmony import */ var _loader_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./loader/index.js */ "./node_modules/@monaco-editor/loader/lib/es/loader/index.js");







/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/loader/index.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/loader/index.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ loader; }
/* harmony export */ });
/* harmony import */ var _virtual_rollupPluginBabelHelpers_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../_virtual/_rollupPluginBabelHelpers.js */ "./node_modules/@monaco-editor/loader/lib/es/_virtual/_rollupPluginBabelHelpers.js");
/* harmony import */ var state_local__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! state-local */ "./node_modules/state-local/lib/es/state-local.js");
/* harmony import */ var _config_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../config/index.js */ "./node_modules/@monaco-editor/loader/lib/es/config/index.js");
/* harmony import */ var _validators_index_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../validators/index.js */ "./node_modules/@monaco-editor/loader/lib/es/validators/index.js");
/* harmony import */ var _utils_compose_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/compose.js */ "./node_modules/@monaco-editor/loader/lib/es/utils/compose.js");
/* harmony import */ var _utils_deepMerge_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/deepMerge.js */ "./node_modules/@monaco-editor/loader/lib/es/utils/deepMerge.js");
/* harmony import */ var _utils_makeCancelable_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/makeCancelable.js */ "./node_modules/@monaco-editor/loader/lib/es/utils/makeCancelable.js");








var _excluded = ["monaco"];

/** the local state of the module */
var _state$create = state_local__WEBPACK_IMPORTED_MODULE_1__["default"].create({
    config: _config_index_js__WEBPACK_IMPORTED_MODULE_2__["default"],
    isInitialized: false,
    resolve: null,
    reject: null,
    monaco: null
  }),
  _state$create2 = (0,_virtual_rollupPluginBabelHelpers_js__WEBPACK_IMPORTED_MODULE_0__.slicedToArray)(_state$create, 2),
  getState = _state$create2[0],
  setState = _state$create2[1];

/**
 * set the loader configuration
 * @param {Object} config - the configuration object
 */
function config(globalConfig) {
  var _validators$config = _validators_index_js__WEBPACK_IMPORTED_MODULE_3__["default"].config(globalConfig),
    monaco = _validators$config.monaco,
    config = (0,_virtual_rollupPluginBabelHelpers_js__WEBPACK_IMPORTED_MODULE_0__.objectWithoutProperties)(_validators$config, _excluded);
  setState(function (state) {
    return {
      config: (0,_utils_deepMerge_js__WEBPACK_IMPORTED_MODULE_5__["default"])(state.config, config),
      monaco: monaco
    };
  });
}

/**
 * handles the initialization of the monaco-editor
 * @return {Promise} - returns an instance of monaco (with a cancelable promise)
 */
function init() {
  var state = getState(function (_ref) {
    var monaco = _ref.monaco,
      isInitialized = _ref.isInitialized,
      resolve = _ref.resolve;
    return {
      monaco: monaco,
      isInitialized: isInitialized,
      resolve: resolve
    };
  });
  if (!state.isInitialized) {
    setState({
      isInitialized: true
    });
    if (state.monaco) {
      state.resolve(state.monaco);
      return (0,_utils_makeCancelable_js__WEBPACK_IMPORTED_MODULE_6__["default"])(wrapperPromise);
    }
    if (window.monaco && window.monaco.editor) {
      storeMonacoInstance(window.monaco);
      state.resolve(window.monaco);
      return (0,_utils_makeCancelable_js__WEBPACK_IMPORTED_MODULE_6__["default"])(wrapperPromise);
    }
    (0,_utils_compose_js__WEBPACK_IMPORTED_MODULE_4__["default"])(injectScripts, getMonacoLoaderScript)(configureLoader);
  }
  return (0,_utils_makeCancelable_js__WEBPACK_IMPORTED_MODULE_6__["default"])(wrapperPromise);
}

/**
 * injects provided scripts into the document.body
 * @param {Object} script - an HTML script element
 * @return {Object} - the injected HTML script element
 */
function injectScripts(script) {
  return document.body.appendChild(script);
}

/**
 * creates an HTML script element with/without provided src
 * @param {string} [src] - the source path of the script
 * @return {Object} - the created HTML script element
 */
function createScript(src) {
  var script = document.createElement('script');
  return src && (script.src = src), script;
}

/**
 * creates an HTML script element with the monaco loader src
 * @return {Object} - the created HTML script element
 */
function getMonacoLoaderScript(configureLoader) {
  var state = getState(function (_ref2) {
    var config = _ref2.config,
      reject = _ref2.reject;
    return {
      config: config,
      reject: reject
    };
  });
  var loaderScript = createScript("".concat(state.config.paths.vs, "/loader.js"));
  loaderScript.onload = function () {
    return configureLoader();
  };
  loaderScript.onerror = state.reject;
  return loaderScript;
}

/**
 * configures the monaco loader
 */
function configureLoader() {
  var state = getState(function (_ref3) {
    var config = _ref3.config,
      resolve = _ref3.resolve,
      reject = _ref3.reject;
    return {
      config: config,
      resolve: resolve,
      reject: reject
    };
  });
  var require = window.require;
  require.config(state.config);
  require(['vs/editor/editor.main'], function (loaded) {
    var monaco = loaded.m /* for 0.53 & 0.54 */ || loaded /* for other versions */;
    storeMonacoInstance(monaco);
    state.resolve(monaco);
  }, function (error) {
    state.reject(error);
  });
}

/**
 * store monaco instance in local state
 */
function storeMonacoInstance(monaco) {
  if (!getState().monaco) {
    setState({
      monaco: monaco
    });
  }
}

/**
 * internal helper function
 * extracts stored monaco instance
 * @return {Object|null} - the monaco instance
 */
function __getMonacoInstance() {
  return getState(function (_ref4) {
    var monaco = _ref4.monaco;
    return monaco;
  });
}
var wrapperPromise = new Promise(function (resolve, reject) {
  return setState({
    resolve: resolve,
    reject: reject
  });
});
var loader = {
  config: config,
  init: init,
  __getMonacoInstance: __getMonacoInstance
};




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/utils/compose.js":
/*!********************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/utils/compose.js ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ compose; }
/* harmony export */ });
var compose = function compose() {
  for (var _len = arguments.length, fns = new Array(_len), _key = 0; _key < _len; _key++) {
    fns[_key] = arguments[_key];
  }
  return function (x) {
    return fns.reduceRight(function (y, f) {
      return f(y);
    }, x);
  };
};




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/utils/curry.js":
/*!******************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/utils/curry.js ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ curry; }
/* harmony export */ });
function curry(fn) {
  return function curried() {
    var _this = this;
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    return args.length >= fn.length ? fn.apply(this, args) : function () {
      for (var _len2 = arguments.length, nextArgs = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        nextArgs[_key2] = arguments[_key2];
      }
      return curried.apply(_this, [].concat(args, nextArgs));
    };
  };
}




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/utils/deepMerge.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/utils/deepMerge.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ merge; }
/* harmony export */ });
/* harmony import */ var _virtual_rollupPluginBabelHelpers_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../_virtual/_rollupPluginBabelHelpers.js */ "./node_modules/@monaco-editor/loader/lib/es/_virtual/_rollupPluginBabelHelpers.js");


function merge(target, source) {
  Object.keys(source).forEach(function (key) {
    if (source[key] instanceof Object) {
      if (target[key]) {
        Object.assign(source[key], merge(target[key], source[key]));
      }
    }
  });
  return (0,_virtual_rollupPluginBabelHelpers_js__WEBPACK_IMPORTED_MODULE_0__.objectSpread2)((0,_virtual_rollupPluginBabelHelpers_js__WEBPACK_IMPORTED_MODULE_0__.objectSpread2)({}, target), source);
}




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/utils/isObject.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/utils/isObject.js ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ isObject; }
/* harmony export */ });
function isObject(value) {
  return {}.toString.call(value).includes('Object');
}




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/utils/makeCancelable.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/utils/makeCancelable.js ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CANCELATION_MESSAGE: function() { return /* binding */ CANCELATION_MESSAGE; },
/* harmony export */   "default": function() { return /* binding */ makeCancelable; }
/* harmony export */ });
// The source (has been changed) is https://github.com/facebook/react/issues/5465#issuecomment-157888325

var CANCELATION_MESSAGE = {
  type: 'cancelation',
  msg: 'operation is manually canceled'
};
function makeCancelable(promise) {
  var hasCanceled_ = false;
  var wrappedPromise = new Promise(function (resolve, reject) {
    promise.then(function (val) {
      return hasCanceled_ ? reject(CANCELATION_MESSAGE) : resolve(val);
    });
    promise["catch"](reject);
  });
  return wrappedPromise.cancel = function () {
    return hasCanceled_ = true;
  }, wrappedPromise;
}




/***/ }),

/***/ "./node_modules/@monaco-editor/loader/lib/es/validators/index.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@monaco-editor/loader/lib/es/validators/index.js ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ validators; },
/* harmony export */   errorHandler: function() { return /* binding */ errorHandler; },
/* harmony export */   errorMessages: function() { return /* binding */ errorMessages; }
/* harmony export */ });
/* harmony import */ var _utils_curry_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/curry.js */ "./node_modules/@monaco-editor/loader/lib/es/utils/curry.js");
/* harmony import */ var _utils_isObject_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/isObject.js */ "./node_modules/@monaco-editor/loader/lib/es/utils/isObject.js");



/**
 * validates the configuration object and informs about deprecation
 * @param {Object} config - the configuration object 
 * @return {Object} config - the validated configuration object
 */
function validateConfig(config) {
  if (!config) errorHandler('configIsRequired');
  if (!(0,_utils_isObject_js__WEBPACK_IMPORTED_MODULE_1__["default"])(config)) errorHandler('configType');
  if (config.urls) {
    informAboutDeprecation();
    return {
      paths: {
        vs: config.urls.monacoBase
      }
    };
  }
  return config;
}

/**
 * logs deprecation message
 */
function informAboutDeprecation() {
  console.warn(errorMessages.deprecation);
}
function throwError(errorMessages, type) {
  throw new Error(errorMessages[type] || errorMessages["default"]);
}
var errorMessages = {
  configIsRequired: 'the configuration object is required',
  configType: 'the configuration object should be an object',
  "default": 'an unknown error accured in `@monaco-editor/loader` package',
  deprecation: "Deprecation warning!\n    You are using deprecated way of configuration.\n\n    Instead of using\n      monaco.config({ urls: { monacoBase: '...' } })\n    use\n      monaco.config({ paths: { vs: '...' } })\n\n    For more please check the link https://github.com/suren-atoyan/monaco-loader#config\n  "
};
var errorHandler = (0,_utils_curry_js__WEBPACK_IMPORTED_MODULE_0__["default"])(throwError)(errorMessages);
var validators = {
  config: validateConfig
};




/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.styles.ts":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.styles.ts ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditorWrapper: function() { return /* binding */ EditorWrapper; },
/* harmony export */   ResizeHandle: function() { return /* binding */ ResizeHandle; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const EditorWrapper = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)((0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.Box))`
	/* @noflip */
	direction: ltr;
	border: 1px solid var( --e-a-border-color );
	border-radius: 8px;
	padding: 4px;
	position: relative;
	height: 200px;

	.monaco-editor .suggest-widget {
		width: 220px !important;
		max-width: 220px !important;
		z-index: 1001;
	}

	.visual-content-dimmed {
		opacity: 0.6;
		color: #aaa !important;
		pointer-events: none;
	}

	.monaco-editor {
		.margin-view-overlays > div:nth-of-type( 1 ) .cldr.codicon.codicon-folding-expanded {
			visibility: hidden;
		}

		.monaco-scrollable-element {
			> .scrollbar {
				width: 6px !important;

				> .slider {
					width: 6px !important;
				}
			}
		}
	}
`;
const ResizeHandle = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)((0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.Button))`
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	height: 6px;
	cursor: ns-resize;
	background: transparent;
	border: none;
	padding: 0;

	&:hover {
		background: rgba( 0, 0, 0, 0.05 );
	}

	&:active {
		background: rgba( 0, 0, 0, 0.1 );
	}

	&::after {
		content: '';
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate( -50%, -50% );
		width: 30px;
		height: 2px;
		background: var( --e-a-border-color );
		border-radius: 1px;
	}
`;

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssEditor: function() { return /* binding */ CssEditor; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _monaco_editor_react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @monaco-editor/react */ "./node_modules/@monaco-editor/react/dist/index.mjs");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _css_editor_styles__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./css-editor.styles */ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.styles.ts");
/* harmony import */ var _css_validation__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./css-validation */ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-validation.ts");
/* harmony import */ var _resize_handle__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./resize-handle */ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/resize-handle.tsx");
/* harmony import */ var _visual_content_change_protection__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./visual-content-change-protection */ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/visual-content-change-protection.ts");











const createEditorDidMountHandler = (editorRef, monacoRef, onUserContentChange, setIsValid, syntaxRuleOptions) => {
  return (editor, monaco) => {
    editorRef.current = editor;
    monacoRef.current = monaco;
    (0,_visual_content_change_protection__WEBPACK_IMPORTED_MODULE_9__.preventChangeOnVisualContent)(editor);
    (0,_css_validation__WEBPACK_IMPORTED_MODULE_7__.setCustomSyntaxRules)(editor, monaco, syntaxRuleOptions);
    const initialCode = editor.getModel()?.getValue() ?? '';
    const initialUserContent = (0,_visual_content_change_protection__WEBPACK_IMPORTED_MODULE_9__.getActual)(initialCode);
    onUserContentChange(initialUserContent);
    monaco.editor.onDidChangeMarkers(() => {
      (0,_css_validation__WEBPACK_IMPORTED_MODULE_7__.clearMarkersFromVisualContent)(editor, monaco);
      setIsValid((0,_css_validation__WEBPACK_IMPORTED_MODULE_7__.validate)(editor, monaco));
    });
    editor.setPosition({
      lineNumber: 2,
      column: (editor.getModel()?.getLineContent(2).length ?? 0) + 1
    });
    disableFoldingFirstRow(editor);

    // eslint-disable-next-line no-bitwise
    editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyA, () => {
      const editorModel = editor.getModel();
      if (!editorModel) {
        return;
      }
      const fullRange = editorModel.getFullModelRange();
      const contentEndLine = fullRange.endLineNumber - 1;
      let endColumn = editorModel.getLineLastNonWhitespaceColumn(contentEndLine);
      if (endColumn === 0) {
        endColumn = editorModel.getLineMaxColumn(contentEndLine);
      }
      editor.setSelection(new monaco.Selection(fullRange.startLineNumber + 1, fullRange.startColumn, contentEndLine, endColumn));
    });
  };
};
function disableFoldingFirstRow(editor) {
  if (!(typeof editor.getDomNode === 'function')) {
    return;
  }
  const marginViewOverlays = editor.getDomNode()?.querySelector('.margin-view-overlays');
  const handler = event => {
    const ev = event;
    if (ev.button !== 0) {
      return;
    }
    const target = ev.target;
    const firstElem = marginViewOverlays?.children[0];
    const isFirstRow = target === firstElem || target.parentElement === firstElem;
    if (isFirstRow) {
      event.preventDefault();
      event.stopPropagation();
    }
  };
  marginViewOverlays?.addEventListener('mousedown', handler);
  editor.onDidDispose(() => {
    marginViewOverlays?.removeEventListener('mousedown', handler);
  });
}
const CssEditor = ({
  value,
  onChange,
  syntaxRuleOptions,
  readOnly = false
}) => {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.useTheme)();
  const containerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const editorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const monacoRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const debounceTimer = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const [hasContent, setHasContent] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(value.trim() !== '');
  const [isValid, setIsValid] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [contentVersion, setContentVersion] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0);
  useOnUpdate(() => {
    const code = editorRef.current?.getModel()?.getValue() ?? '';
    const userContent = (0,_visual_content_change_protection__WEBPACK_IMPORTED_MODULE_9__.getActual)(code);
    setHasContent(!userContent.trim());
    onChange(userContent, isValid);
  }, [contentVersion, isValid]);
  const handleUserContentChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newValue => {
    setHasContent(newValue.trim() !== '');
  }, []);
  const handleResize = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    editorRef.current?.layout();
  }, []);
  const handleHeightChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(height => {
    if (containerRef.current) {
      containerRef.current.style.height = `${height}px`;
    }
  }, []);
  const handleEditorChange = () => {
    if (!editorRef.current || !monacoRef.current) {
      return;
    }
    (0,_css_validation__WEBPACK_IMPORTED_MODULE_7__.setCustomSyntaxRules)(editorRef?.current, monacoRef.current, syntaxRuleOptions);
    if (debounceTimer.current) {
      clearTimeout(debounceTimer.current);
    }
    debounceTimer.current = setTimeout(() => {
      setContentVersion(prev => prev + 1);
    }, 500);
  };
  const handleEditorDidMount = createEditorDidMountHandler(editorRef, monacoRef, handleUserContentChange, setIsValid, syntaxRuleOptions);
  const handleReset = () => editorRef.current?.getModel()?.setValue((0,_visual_content_change_protection__WEBPACK_IMPORTED_MODULE_9__.setVisualContent)(''));
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const timerRef = debounceTimer;
    return () => {
      const timer = timerRef.current;
      if (timer) {
        clearTimeout(timer);
      }
    };
  }, []);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.FloatingActionsBar, {
    actions: hasContent ? [/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ClearIconButton, {
      key: "clear",
      tooltipText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Clear', 'elementor-pro'),
      onClick: handleReset
    })] : []
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_editor_styles__WEBPACK_IMPORTED_MODULE_6__.EditorWrapper, {
    ref: containerRef,
    dir: "ltr"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_monaco_editor_react__WEBPACK_IMPORTED_MODULE_4__.Editor, {
    height: "100%",
    language: "css",
    theme: theme.palette.mode === 'dark' ? 'vs-dark' : 'vs',
    value: (0,_visual_content_change_protection__WEBPACK_IMPORTED_MODULE_9__.setVisualContent)(value),
    onMount: handleEditorDidMount,
    onChange: handleEditorChange,
    options: {
      lineNumbers: 'on',
      lineNumbersMinChars: 3,
      folding: true,
      minimap: {
        enabled: false
      },
      fontFamily: 'Roboto, Arial, Helvetica, Verdana, sans-serif',
      fontSize: 12,
      renderLineHighlight: 'none',
      hideCursorInOverviewRuler: true,
      overviewRulerBorder: false,
      fixedOverflowWidgets: true,
      suggestFontSize: 10,
      suggestLineHeight: 14,
      stickyScroll: {
        enabled: false
      },
      lineDecorationsWidth: 2,
      wordWrap: 'on',
      scrollBeyondLastLine: false,
      readOnly,
      editContext: false
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_resize_handle__WEBPACK_IMPORTED_MODULE_8__.ResizeHandleComponent, {
    onResize: handleResize,
    containerRef: containerRef,
    onHeightChange: handleHeightChange
  }))));
};
function useOnUpdate(callback, dependencies) {
  const hasMounted = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (hasMounted.current) {
      callback();
    } else {
      hasMounted.current = true;
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, dependencies);
}

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-validation.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-validation.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearMarkersFromVisualContent: function() { return /* binding */ clearMarkersFromVisualContent; },
/* harmony export */   setCustomSyntaxRules: function() { return /* binding */ setCustomSyntaxRules; },
/* harmony export */   validate: function() { return /* binding */ validate; }
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);

const syntaxRules = {
  pseudoState: {
    pattern: '^\\s*[&]{0,1}\\s*(?::hover|:active|:focus)',
    regex: true,
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The use of pseudo-states is not permitted. Instead, switch to the desired pseudo state and add your custom code there.', 'elementor-pro')
  },
  mediaQuery: {
    pattern: '@media\\s+[^{]*\\b(?:min-width|max-width|width)\\b',
    regex: true,
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The use of @media width queries is not permitted. Instead, switch to the desired breakpoint and add your custom code there.', 'elementor-pro')
  }
};
function setCustomSyntaxRules(editor, monaco, options) {
  const model = editor.getModel();
  if (!model) {
    return true;
  }
  const customMarkers = [];
  Object.entries(syntaxRules).forEach(([id, rule]) => {
    if (options?.rules?.[id] === false) {
      return;
    }
    const matches = model.findMatches(rule.pattern, true, rule.regex ?? false, true, null, true);
    matches.forEach(match => {
      customMarkers.push({
        severity: monaco.MarkerSeverity.Error,
        message: rule.message,
        startLineNumber: match.range.startLineNumber,
        startColumn: match.range.startColumn,
        endLineNumber: match.range.endLineNumber,
        endColumn: match.range.endColumn,
        source: 'custom-css-rules'
      });
    });
  });
  monaco.editor.setModelMarkers(model, 'custom-css-rules', customMarkers);
  return customMarkers.length === 0;
}
function validate(editor, monaco) {
  const model = editor.getModel();
  if (!model) {
    return true;
  }
  const allMarkers = monaco.editor.getModelMarkers({
    resource: model.uri
  });
  return allMarkers.filter(marker => marker.severity === monaco.MarkerSeverity.Error).length === 0;
}
function clearMarkersFromVisualContent(editor, monaco) {
  const model = editor.getModel();
  if (!model) {
    return;
  }
  const allMarkers = monaco.editor.getModelMarkers({
    resource: model.uri
  });
  const filteredMarkers = allMarkers.filter(marker => marker.startLineNumber !== 1);
  const nonCustomMarkers = filteredMarkers.filter(m => m.source !== 'custom-css-rules');
  if (nonCustomMarkers.length === allMarkers.length) {
    return;
  }
  monaco.editor.setModelMarkers(model, 'css', nonCustomMarkers);
}

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/resize-handle.tsx":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/resize-handle.tsx ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ResizeHandleComponent: function() { return /* binding */ ResizeHandleComponent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _css_editor_styles__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./css-editor.styles */ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.styles.ts");


const ResizeHandleComponent = ({
  onResize,
  containerRef,
  onHeightChange
}) => {
  const handleResizeMove = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(e => {
    const container = containerRef.current;
    if (!container) {
      return;
    }
    const containerRect = container.getBoundingClientRect();
    const newHeight = Math.max(100, e.clientY - containerRect.top);
    onHeightChange?.(newHeight);
    onResize(newHeight);
  }, [containerRef, onResize, onHeightChange]);
  const handleResizeEnd = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(() => {
    document.removeEventListener('mousemove', handleResizeMove);
    document.removeEventListener('mouseup', handleResizeEnd);
  }, [handleResizeMove]);
  const handleResizeStart = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(e => {
    e.preventDefault();
    e.stopPropagation();
    document.addEventListener('mousemove', handleResizeMove);
    document.addEventListener('mouseup', handleResizeEnd);
  }, [handleResizeMove, handleResizeEnd]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    return () => {
      document.removeEventListener('mousemove', handleResizeMove);
      document.removeEventListener('mouseup', handleResizeEnd);
    };
  }, [handleResizeMove, handleResizeEnd]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_editor_styles__WEBPACK_IMPORTED_MODULE_1__.ResizeHandle, {
    onMouseDown: handleResizeStart,
    "aria-label": "Resize editor height",
    title: "Drag to resize editor height"
  });
};

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/visual-content-change-protection.ts":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/visual-content-change-protection.ts ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getActual: function() { return /* binding */ getActual; },
/* harmony export */   preventChangeOnVisualContent: function() { return /* binding */ preventChangeOnVisualContent; },
/* harmony export */   setVisualContent: function() { return /* binding */ setVisualContent; }
/* harmony export */ });
const preventChangeOnVisualContent = editor => {
  const model = editor.getModel();
  if (!model) {
    return;
  }

  // On first load
  applyVisualContentStyling(editor, model);

  // On every change
  model.onDidChangeContent(() => {
    applyVisualContentStyling(editor, model);
  });
  disableCursorOnVisualContent(editor);
  overridePushEditOperations(model);
};
const applyVisualContentStyling = (editor, model) => {
  const decorationsCollection = editor.createDecorationsCollection();
  const lineCount = model.getLineCount();
  const decorations = [];
  decorations.push({
    range: {
      startLineNumber: 1,
      startColumn: 1,
      endLineNumber: 1,
      endColumn: model.getLineContent(1).length + 1
    },
    options: {
      inlineClassName: 'visual-content-dimmed',
      isWholeLine: false
    }
  });
  if (lineCount > 1) {
    decorations.push({
      range: {
        startLineNumber: lineCount,
        startColumn: 1,
        endLineNumber: lineCount,
        endColumn: model.getLineContent(lineCount).length + 1
      },
      options: {
        inlineClassName: 'visual-content-dimmed',
        isWholeLine: false
      }
    });
  }
  decorationsCollection.set(decorations);
};
const disableCursorOnVisualContent = editor => {
  const model = editor.getModel();
  if (!model) {
    return;
  }
  editor.onDidChangeCursorPosition(e => {
    const totalLines = model.getLineCount();
    const position = e.position;
    if (position.lineNumber === 1) {
      editor.setPosition({
        lineNumber: 2,
        column: 1
      });
    } else if (position.lineNumber === totalLines) {
      editor.setPosition({
        lineNumber: totalLines - 1,
        column: model.getLineContent(totalLines - 1).length + 1
      });
    }
  });
};
const overridePushEditOperations = model => {
  const originalPushEditOperations = model.pushEditOperations;
  model.pushEditOperations = (beforeCursorState, editOperations, cursorStateComputer) => {
    const totalLines = model.getLineCount();
    const modelRange = model.getFullModelRange();
    const filteredOperations = editOperations.filter(operation => {
      const range = operation.range;
      const affectsProtectedLine = range.startLineNumber === 1 || range.endLineNumber === 1 || range.startLineNumber === totalLines || range.endLineNumber === totalLines;
      if (affectsProtectedLine && isFullContentReplacement(range, modelRange) && hasVisualContent(operation.text)) {
        return true;
      }
      return !affectsProtectedLine;
    });
    return originalPushEditOperations.call(model, beforeCursorState, filteredOperations, cursorStateComputer);
  };
};
const isFullContentReplacement = (range, modelRange) => {
  return range.startLineNumber === modelRange.startLineNumber && range.endLineNumber === modelRange.endLineNumber && range.startColumn === modelRange.startColumn && range.endColumn === modelRange.endColumn;
};
const hasVisualContent = text => {
  if (!text) {
    return false;
  }
  return text.startsWith('element.style {') && text.endsWith('}');
};
const setVisualContent = value => {
  const trimmed = value.trim();
  return `element.style {\n${trimmed ? '  ' + trimmed.replace(/\n/g, '\n  ') + '\n' : '  \n'}}`;
};
const getActual = value => {
  const lines = value.split('\n');
  if (lines.length < 2) {
    return '';
  }
  return lines.slice(1, -1).map(line => line.replace(/^ {2}/, '')).join('\n');
};

/***/ }),

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

/***/ "./packages/packages/pro/editor-controls-extended/src/controls/display-conditions-control.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/controls/display-conditions-control.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DisplayConditionsControl: function() { return /* binding */ DisplayConditionsControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _prop_types_display_conditions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../prop-types/display-conditions */ "./packages/packages/pro/editor-controls-extended/src/prop-types/display-conditions.ts");
/* harmony import */ var _utils_display_conditions_utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/display-conditions-utils */ "./packages/packages/pro/editor-controls-extended/src/utils/display-conditions-utils.ts");








const OPEN_MODAL_EVENT = 'elementor/display-conditions/open';
const CLOSE_MODAL_EVENT = 'elementor/display-conditions/close';
const SET_CACHE_NOTICE_STATUS_EVENT = 'elementor/display-conditions/set-cache-notice-status';
const ariaLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Display Conditions', 'elementor-pro');
async function setCacheNoticeStatus() {
  return new Promise((resolve, reject) => {
    window.dispatchEvent(new CustomEvent(SET_CACHE_NOTICE_STATUS_EVENT, {
      detail: {
        resolve,
        reject
      }
    }));
  });
}
const DisplayConditionsControl = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.createControl)(({
  disabled = false
}) => {
  const {
    setValue,
    value: displayConditionsValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_prop_types_display_conditions__WEBPACK_IMPORTED_MODULE_5__.displayConditionsPropTypeUtil);
  const [isModalOpen, setIsModalOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const hasValue = !!displayConditionsValue?.length;
  const setControlValue = value => setValue((0,_utils_display_conditions_utils__WEBPACK_IMPORTED_MODULE_6__.transformV3ToV4)(JSON.parse(value ?? '[]')));
  const getControlValue = () => (0,_utils_display_conditions_utils__WEBPACK_IMPORTED_MODULE_6__.transformV4ToV3)(displayConditionsValue) ?? [];
  const onClose = () => {
    setIsModalOpen(false);
    window.dispatchEvent(new CustomEvent(CLOSE_MODAL_EVENT));
  };
  const conditions = getControlValue();
  const openConditionsModal = () => {
    setIsModalOpen(true);
    window.dispatchEvent(new CustomEvent(OPEN_MODAL_EVENT, {
      detail: {
        props: {
          getControlValue,
          setControlValue,
          onClose,
          setCacheNoticeStatus
        }
      }
    }));
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    spacing: 2,
    sx: {
      justifyContent: 'flex-end',
      alignItems: 'center'
    }
  }, disabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Chip, {
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CrownFilledIcon, {
      fontSize: "tiny"
    }),
    size: "tiny",
    color: "promotion",
    variant: "standard",
    sx: {
      width: '20px',
      '& .MuiChip-label': {
        display: 'none'
      }
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: ariaLabel,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    sx: {
      cursor: disabled ? 'not-allowed' : 'pointer',
      display: 'inline-flex'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ToggleButton, {
    value: JSON.stringify(conditions),
    size: "tiny",
    variant: "outline",
    "aria-pressed": hasValue,
    "aria-expanded": isModalOpen,
    selected: hasValue,
    "aria-haspopup": "dialog",
    "aria-label": ariaLabel,
    onClick: openConditionsModal,
    disabled: disabled,
    sx: {
      pointerEvents: disabled ? 'none' : 'auto'
    },
    "data-behavior": "display-conditions"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.SitemapIcon, {
    fontSize: "tiny"
  }))))));
});

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/controls/options-control.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/controls/options-control.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OptionsControl: function() { return /* binding */ OptionsControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const OptionsControl = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.createControl)(() => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.KeyValueControl, {
    keyName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Name', 'elementor-pro'),
    valueName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Value', 'elementor-pro')
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

function extendTransitionProperties(isDisabled = false) {
  if (!_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties || _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.length === 0) {
    return;
  }
  _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.forEach(category => {
    category.properties.forEach(property => {
      if (property.value !== 'all') {
        property.isDisabled = isDisabled;
      }
    });
  });
  _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionsItemsList.splice(0, _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionsItemsList.length, ..._elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.map(category => ({
    label: category.label,
    items: category.properties.map(property => property.label)
  })));
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
/* harmony import */ var _elementor_license_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/license-api */ "@elementor/license-api");
/* harmony import */ var _elementor_license_api__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_license_api__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _extend_transition_properties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./extend-transition-properties */ "./packages/packages/pro/editor-controls-extended/src/extend-transition-properties.ts");


async function init() {
  const [isExpired, features] = await Promise.all([(0,_elementor_license_api__WEBPACK_IMPORTED_MODULE_0__.fetchLicenseStatus)().catch(() => false), (0,_elementor_license_api__WEBPACK_IMPORTED_MODULE_0__.fetchTierFeatures)().catch(() => [])]);
  if (features.includes('transitions')) {
    (0,_extend_transition_properties__WEBPACK_IMPORTED_MODULE_1__.extendTransitionProperties)(isExpired);
  }
}

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/prop-types/condition-group.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/prop-types/condition-group.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   conditionGroupPropTypeUtil: function() { return /* binding */ conditionGroupPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const unknownChildrenSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.any().nullable();
const conditionGroupPropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('condition-group', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.array(unknownChildrenSchema));

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/prop-types/display-conditions.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/prop-types/display-conditions.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   displayConditionsPropTypeUtil: function() { return /* binding */ displayConditionsPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const unknownChildrenSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.any().nullable();
const displayConditionsPropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('display-conditions', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.array(unknownChildrenSchema));

/***/ }),

/***/ "./packages/packages/pro/editor-controls-extended/src/utils/display-conditions-utils.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/utils/display-conditions-utils.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformV3ToV4: function() { return /* binding */ transformV3ToV4; },
/* harmony export */   transformV4ToV3: function() { return /* binding */ transformV4ToV3; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);

// Value must be falsy for navigator indication to work properly
function transformV3ToV4(displayConditions) {
  if (!Array.isArray(displayConditions) || !displayConditions?.length) {
    return null;
  }
  const transformed = displayConditions.filter(conditionGroup => !!conditionGroup?.length).map(conditionGroup => ({
    $$type: 'condition-group',
    value: conditionGroup.map(condition => _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.create(JSON.stringify(condition)))
  }));
  return transformed.length ? transformed : null;
}
function transformV4ToV3(value) {
  const conditionGroups = value;
  const transformed = conditionGroups?.map(group => group.value.map(conditions => {
    return JSON.parse(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.extract(conditions) ?? '[]');
  })) ?? null;
  return getStructuredConditions(transformed);
}
function shouldConvertConditionsStructure(conditions) {
  return !!conditions?.length && !Array.isArray(conditions?.[0]);
}
function getStructuredConditions(conditions) {
  return shouldConvertConditionsStructure(conditions) ? [conditions] : conditions;
}

/***/ }),

/***/ "./node_modules/state-local/lib/es/state-local.js":
/*!********************************************************!*\
  !*** ./node_modules/state-local/lib/es/state-local.js ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    if (enumerableOnly) symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    });
    keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};

    if (i % 2) {
      ownKeys(Object(source), true).forEach(function (key) {
        _defineProperty(target, key, source[key]);
      });
    } else if (Object.getOwnPropertyDescriptors) {
      Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
    } else {
      ownKeys(Object(source)).forEach(function (key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }
  }

  return target;
}

function compose() {
  for (var _len = arguments.length, fns = new Array(_len), _key = 0; _key < _len; _key++) {
    fns[_key] = arguments[_key];
  }

  return function (x) {
    return fns.reduceRight(function (y, f) {
      return f(y);
    }, x);
  };
}

function curry(fn) {
  return function curried() {
    var _this = this;

    for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      args[_key2] = arguments[_key2];
    }

    return args.length >= fn.length ? fn.apply(this, args) : function () {
      for (var _len3 = arguments.length, nextArgs = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
        nextArgs[_key3] = arguments[_key3];
      }

      return curried.apply(_this, [].concat(args, nextArgs));
    };
  };
}

function isObject(value) {
  return {}.toString.call(value).includes('Object');
}

function isEmpty(obj) {
  return !Object.keys(obj).length;
}

function isFunction(value) {
  return typeof value === 'function';
}

function hasOwnProperty(object, property) {
  return Object.prototype.hasOwnProperty.call(object, property);
}

function validateChanges(initial, changes) {
  if (!isObject(changes)) errorHandler('changeType');
  if (Object.keys(changes).some(function (field) {
    return !hasOwnProperty(initial, field);
  })) errorHandler('changeField');
  return changes;
}

function validateSelector(selector) {
  if (!isFunction(selector)) errorHandler('selectorType');
}

function validateHandler(handler) {
  if (!(isFunction(handler) || isObject(handler))) errorHandler('handlerType');
  if (isObject(handler) && Object.values(handler).some(function (_handler) {
    return !isFunction(_handler);
  })) errorHandler('handlersType');
}

function validateInitial(initial) {
  if (!initial) errorHandler('initialIsRequired');
  if (!isObject(initial)) errorHandler('initialType');
  if (isEmpty(initial)) errorHandler('initialContent');
}

function throwError(errorMessages, type) {
  throw new Error(errorMessages[type] || errorMessages["default"]);
}

var errorMessages = {
  initialIsRequired: 'initial state is required',
  initialType: 'initial state should be an object',
  initialContent: 'initial state shouldn\'t be an empty object',
  handlerType: 'handler should be an object or a function',
  handlersType: 'all handlers should be a functions',
  selectorType: 'selector should be a function',
  changeType: 'provided value of changes should be an object',
  changeField: 'it seams you want to change a field in the state which is not specified in the "initial" state',
  "default": 'an unknown error accured in `state-local` package'
};
var errorHandler = curry(throwError)(errorMessages);
var validators = {
  changes: validateChanges,
  selector: validateSelector,
  handler: validateHandler,
  initial: validateInitial
};

function create(initial) {
  var handler = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  validators.initial(initial);
  validators.handler(handler);
  var state = {
    current: initial
  };
  var didUpdate = curry(didStateUpdate)(state, handler);
  var update = curry(updateState)(state);
  var validate = curry(validators.changes)(initial);
  var getChanges = curry(extractChanges)(state);

  function getState() {
    var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : function (state) {
      return state;
    };
    validators.selector(selector);
    return selector(state.current);
  }

  function setState(causedChanges) {
    compose(didUpdate, update, validate, getChanges)(causedChanges);
  }

  return [getState, setState];
}

function extractChanges(state, causedChanges) {
  return isFunction(causedChanges) ? causedChanges(state.current) : causedChanges;
}

function updateState(state, changes) {
  state.current = _objectSpread2(_objectSpread2({}, state.current), changes);
  return changes;
}

function didStateUpdate(state, handler, changes) {
  isFunction(handler) ? handler(state.current) : Object.keys(changes).forEach(function (field) {
    var _handler$field;

    return (_handler$field = handler[field]) === null || _handler$field === void 0 ? void 0 : _handler$field.call(handler, state.current[field]);
  });
  return changes;
}

var index = {
  create: create
};

/* harmony default export */ __webpack_exports__["default"] = (index);


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

/***/ "@elementor/editor-props":
/*!**********************************************!*\
  !*** external ["elementorV2","editorProps"] ***!
  \**********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorProps"];

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

/***/ "@elementor/license-api":
/*!*********************************************!*\
  !*** external ["elementorV2","licenseApi"] ***!
  \*********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["licenseApi"];

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

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

/***/ "./node_modules/@monaco-editor/react/dist/index.mjs":
/*!**********************************************************!*\
  !*** ./node_modules/@monaco-editor/react/dist/index.mjs ***!
  \**********************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DiffEditor: function() { return /* binding */ we; },
/* harmony export */   Editor: function() { return /* binding */ de; },
/* harmony export */   "default": function() { return /* binding */ Ft; },
/* harmony export */   loader: function() { return /* reexport safe */ _monaco_editor_loader__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   useMonaco: function() { return /* binding */ Le; }
/* harmony export */ });
/* harmony import */ var _monaco_editor_loader__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @monaco-editor/loader */ "./node_modules/@monaco-editor/loader/lib/es/index.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
var le={wrapper:{display:"flex",position:"relative",textAlign:"initial"},fullWidth:{width:"100%"},hide:{display:"none"}},v=le;var ae={container:{display:"flex",height:"100%",width:"100%",justifyContent:"center",alignItems:"center"}},Y=ae;function Me({children:e}){return react__WEBPACK_IMPORTED_MODULE_1__.createElement("div",{style:Y.container},e)}var Z=Me;var $=Z;function Ee({width:e,height:r,isEditorReady:n,loading:t,_ref:a,className:m,wrapperProps:E}){return react__WEBPACK_IMPORTED_MODULE_1__.createElement("section",{style:{...v.wrapper,width:e,height:r},...E},!n&&react__WEBPACK_IMPORTED_MODULE_1__.createElement($,null,t),react__WEBPACK_IMPORTED_MODULE_1__.createElement("div",{ref:a,style:{...v.fullWidth,...!n&&v.hide},className:m}))}var ee=Ee;var H=(0,react__WEBPACK_IMPORTED_MODULE_1__.memo)(ee);function Ce(e){(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(e,[])}var k=Ce;function he(e,r,n=!0){let t=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(!0);(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(t.current||!n?()=>{t.current=!1}:e,r)}var l=he;function D(){}function h(e,r,n,t){return De(e,t)||be(e,r,n,t)}function De(e,r){return e.editor.getModel(te(e,r))}function be(e,r,n,t){return e.editor.createModel(r,n,t?te(e,t):void 0)}function te(e,r){return e.Uri.parse(r)}function Oe({original:e,modified:r,language:n,originalLanguage:t,modifiedLanguage:a,originalModelPath:m,modifiedModelPath:E,keepCurrentOriginalModel:g=!1,keepCurrentModifiedModel:N=!1,theme:x="light",loading:P="Loading...",options:y={},height:V="100%",width:z="100%",className:F,wrapperProps:j={},beforeMount:A=D,onMount:q=D}){let[M,O]=(0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(!1),[T,s]=(0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(!0),u=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(null),c=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(null),w=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(null),d=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(q),o=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(A),b=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(!1);k(()=>{let i=_monaco_editor_loader__WEBPACK_IMPORTED_MODULE_0__["default"].init();return i.then(f=>(c.current=f)&&s(!1)).catch(f=>f?.type!=="cancelation"&&console.error("Monaco initialization: error:",f)),()=>u.current?I():i.cancel()}),l(()=>{if(u.current&&c.current){let i=u.current.getOriginalEditor(),f=h(c.current,e||"",t||n||"text",m||"");f!==i.getModel()&&i.setModel(f)}},[m],M),l(()=>{if(u.current&&c.current){let i=u.current.getModifiedEditor(),f=h(c.current,r||"",a||n||"text",E||"");f!==i.getModel()&&i.setModel(f)}},[E],M),l(()=>{let i=u.current.getModifiedEditor();i.getOption(c.current.editor.EditorOption.readOnly)?i.setValue(r||""):r!==i.getValue()&&(i.executeEdits("",[{range:i.getModel().getFullModelRange(),text:r||"",forceMoveMarkers:!0}]),i.pushUndoStop())},[r],M),l(()=>{u.current?.getModel()?.original.setValue(e||"")},[e],M),l(()=>{let{original:i,modified:f}=u.current.getModel();c.current.editor.setModelLanguage(i,t||n||"text"),c.current.editor.setModelLanguage(f,a||n||"text")},[n,t,a],M),l(()=>{c.current?.editor.setTheme(x)},[x],M),l(()=>{u.current?.updateOptions(y)},[y],M);let L=(0,react__WEBPACK_IMPORTED_MODULE_1__.useCallback)(()=>{if(!c.current)return;o.current(c.current);let i=h(c.current,e||"",t||n||"text",m||""),f=h(c.current,r||"",a||n||"text",E||"");u.current?.setModel({original:i,modified:f})},[n,r,a,e,t,m,E]),U=(0,react__WEBPACK_IMPORTED_MODULE_1__.useCallback)(()=>{!b.current&&w.current&&(u.current=c.current.editor.createDiffEditor(w.current,{automaticLayout:!0,...y}),L(),c.current?.editor.setTheme(x),O(!0),b.current=!0)},[y,x,L]);(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{M&&d.current(u.current,c.current)},[M]),(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{!T&&!M&&U()},[T,M,U]);function I(){let i=u.current?.getModel();g||i?.original?.dispose(),N||i?.modified?.dispose(),u.current?.dispose()}return react__WEBPACK_IMPORTED_MODULE_1__.createElement(H,{width:z,height:V,isEditorReady:M,loading:P,_ref:w,className:F,wrapperProps:j})}var ie=Oe;var we=(0,react__WEBPACK_IMPORTED_MODULE_1__.memo)(ie);function Pe(){let[e,r]=(0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(_monaco_editor_loader__WEBPACK_IMPORTED_MODULE_0__["default"].__getMonacoInstance());return k(()=>{let n;return e||(n=_monaco_editor_loader__WEBPACK_IMPORTED_MODULE_0__["default"].init(),n.then(t=>{r(t)})),()=>n?.cancel()}),e}var Le=Pe;function He(e){let r=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)();return (0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{r.current=e},[e]),r.current}var se=He;var _=new Map;function Ve({defaultValue:e,defaultLanguage:r,defaultPath:n,value:t,language:a,path:m,theme:E="light",line:g,loading:N="Loading...",options:x={},overrideServices:P={},saveViewState:y=!0,keepCurrentModel:V=!1,width:z="100%",height:F="100%",className:j,wrapperProps:A={},beforeMount:q=D,onMount:M=D,onChange:O,onValidate:T=D}){let[s,u]=(0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(!1),[c,w]=(0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(!0),d=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(null),o=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(null),b=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(null),L=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(M),U=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(q),I=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(),i=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(t),f=se(m),Q=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(!1),B=(0,react__WEBPACK_IMPORTED_MODULE_1__.useRef)(!1);k(()=>{let p=_monaco_editor_loader__WEBPACK_IMPORTED_MODULE_0__["default"].init();return p.then(R=>(d.current=R)&&w(!1)).catch(R=>R?.type!=="cancelation"&&console.error("Monaco initialization: error:",R)),()=>o.current?pe():p.cancel()}),l(()=>{let p=h(d.current,e||t||"",r||a||"",m||n||"");p!==o.current?.getModel()&&(y&&_.set(f,o.current?.saveViewState()),o.current?.setModel(p),y&&o.current?.restoreViewState(_.get(m)))},[m],s),l(()=>{o.current?.updateOptions(x)},[x],s),l(()=>{!o.current||t===void 0||(o.current.getOption(d.current.editor.EditorOption.readOnly)?o.current.setValue(t):t!==o.current.getValue()&&(B.current=!0,o.current.executeEdits("",[{range:o.current.getModel().getFullModelRange(),text:t,forceMoveMarkers:!0}]),o.current.pushUndoStop(),B.current=!1))},[t],s),l(()=>{let p=o.current?.getModel();p&&a&&d.current?.editor.setModelLanguage(p,a)},[a],s),l(()=>{g!==void 0&&o.current?.revealLine(g)},[g],s),l(()=>{d.current?.editor.setTheme(E)},[E],s);let X=(0,react__WEBPACK_IMPORTED_MODULE_1__.useCallback)(()=>{if(!(!b.current||!d.current)&&!Q.current){U.current(d.current);let p=m||n,R=h(d.current,t||e||"",r||a||"",p||"");o.current=d.current?.editor.create(b.current,{model:R,automaticLayout:!0,...x},P),y&&o.current.restoreViewState(_.get(p)),d.current.editor.setTheme(E),g!==void 0&&o.current.revealLine(g),u(!0),Q.current=!0}},[e,r,n,t,a,m,x,P,y,E,g]);(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{s&&L.current(o.current,d.current)},[s]),(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{!c&&!s&&X()},[c,s,X]),i.current=t,(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{s&&O&&(I.current?.dispose(),I.current=o.current?.onDidChangeModelContent(p=>{B.current||O(o.current.getValue(),p)}))},[s,O]),(0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(()=>{if(s){let p=d.current.editor.onDidChangeMarkers(R=>{let G=o.current.getModel()?.uri;if(G&&R.find(J=>J.path===G.path)){let J=d.current.editor.getModelMarkers({resource:G});T?.(J)}});return()=>{p?.dispose()}}return()=>{}},[s,T]);function pe(){I.current?.dispose(),V?y&&_.set(m,o.current.saveViewState()):o.current.getModel()?.dispose(),o.current.dispose()}return react__WEBPACK_IMPORTED_MODULE_1__.createElement(H,{width:z,height:F,isEditorReady:s,loading:N,_ref:b,className:j,wrapperProps:A})}var fe=Ve;var de=(0,react__WEBPACK_IMPORTED_MODULE_1__.memo)(fe);var Ft=de;
//# sourceMappingURL=index.mjs.map

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
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
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
/*!*********************************************************************!*\
  !*** ./packages/packages/pro/editor-controls-extended/src/index.ts ***!
  \*********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AttributesControl: function() { return /* reexport safe */ _controls_attributes_control__WEBPACK_IMPORTED_MODULE_1__.AttributesControl; },
/* harmony export */   CssEditor: function() { return /* reexport safe */ _components_css_code_editor_css_editor__WEBPACK_IMPORTED_MODULE_6__.CssEditor; },
/* harmony export */   DisplayConditionsControl: function() { return /* reexport safe */ _controls_display_conditions_control__WEBPACK_IMPORTED_MODULE_3__.DisplayConditionsControl; },
/* harmony export */   OptionsControl: function() { return /* reexport safe */ _controls_options_control__WEBPACK_IMPORTED_MODULE_2__.OptionsControl; },
/* harmony export */   conditionGroupPropTypeUtil: function() { return /* reexport safe */ _prop_types_condition_group__WEBPACK_IMPORTED_MODULE_5__.conditionGroupPropTypeUtil; },
/* harmony export */   displayConditionsPropTypeUtil: function() { return /* reexport safe */ _prop_types_display_conditions__WEBPACK_IMPORTED_MODULE_4__.displayConditionsPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/pro/editor-controls-extended/src/init.ts");
/* harmony import */ var _controls_attributes_control__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./controls/attributes-control */ "./packages/packages/pro/editor-controls-extended/src/controls/attributes-control.tsx");
/* harmony import */ var _controls_options_control__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./controls/options-control */ "./packages/packages/pro/editor-controls-extended/src/controls/options-control.tsx");
/* harmony import */ var _controls_display_conditions_control__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./controls/display-conditions-control */ "./packages/packages/pro/editor-controls-extended/src/controls/display-conditions-control.tsx");
/* harmony import */ var _prop_types_display_conditions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./prop-types/display-conditions */ "./packages/packages/pro/editor-controls-extended/src/prop-types/display-conditions.ts");
/* harmony import */ var _prop_types_condition_group__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./prop-types/condition-group */ "./packages/packages/pro/editor-controls-extended/src/prop-types/condition-group.ts");
/* harmony import */ var _components_css_code_editor_css_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/css-code-editor/css-editor */ "./packages/packages/pro/editor-controls-extended/src/components/css-code-editor/css-editor.tsx");







(0,_init__WEBPACK_IMPORTED_MODULE_0__.init)();
}();
(window.elementorV2 = window.elementorV2 || {}).editorControlsExtended = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorControlsExtended?.init?.();
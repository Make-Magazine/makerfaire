/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../modules/interactions/assets/js/interactions-breakpoints.js":
/*!*********************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-breakpoints.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.getActiveBreakpoint = getActiveBreakpoint;
exports.initBreakpoints = initBreakpoints;
var RESIZE_DEBOUNCE_TIMEOUT = 100;
var breakpoints = {
  list: {},
  active: {},
  onChange: function onChange() {}
};
function getActiveBreakpoint() {
  return breakpoints.active;
}
function matchBreakpoint(width) {
  for (var label in breakpoints.list) {
    var breakpoint = breakpoints.list[label];
    if ('min' === breakpoint.direction && width >= breakpoint.value) {
      return label;
    }
    if ('max' === breakpoint.direction && breakpoint.value >= width) {
      return label;
    }
  }
  return 'desktop';
}
function attachEventListeners() {
  var timeout = null;
  var onResize = function onResize() {
    if (timeout) {
      window.clearTimeout(timeout);
      timeout = null;
    }
    timeout = window.setTimeout(function () {
      var currentBreakpoint = matchBreakpoint(window.innerWidth);
      if (currentBreakpoint === breakpoints.active) {
        return;
      }
      breakpoints.active = currentBreakpoint;
      if ('function' === typeof breakpoints.onChange) {
        breakpoints.onChange(breakpoints.active);
      }
    }, RESIZE_DEBOUNCE_TIMEOUT);
  };
  window.addEventListener('resize', onResize);
}
function getBreakpointsList() {
  var _ElementorInteraction;
  return ((_ElementorInteraction = ElementorInteractionsConfig) === null || _ElementorInteraction === void 0 ? void 0 : _ElementorInteraction.breakpoints) || {};
}
function initBreakpoints() {
  var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
    onChange = _ref.onChange;
  breakpoints.list = getBreakpointsList();
  breakpoints.active = matchBreakpoint(window.innerWidth);
  if ('function' === typeof onChange) {
    breakpoints.onChange = onChange;
  }
  attachEventListeners();
}

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(e, r, t) {
  return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(r, e) {
  return arrayWithHoles(r) || iterableToArrayLimit(r, e) || unsupportedIterableToArray(r, e) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(o) {
  "@babel/helpers - typeof";

  return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? arrayLikeToArray(r, a) : void 0;
  }
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
var exports = __webpack_exports__;
/*!**********************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-shared-utils.js ***!
  \**********************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.config = config;
exports.extractInteractionId = extractInteractionId;
exports.getAnimateFunction = getAnimateFunction;
exports.getInViewFunction = getInViewFunction;
exports.getTransformBaselineFromComputedStyle = getTransformBaselineFromComputedStyle;
exports.parseInteractionsData = parseInteractionsData;
exports.preserveTransformKeyframes = preserveTransformKeyframes;
exports.resetElementStyles = resetElementStyles;
exports.skipInteraction = skipInteraction;
exports.timingValueToMs = timingValueToMs;
exports.unwrapInteractionValue = unwrapInteractionValue;
exports.waitForAnimateFunction = waitForAnimateFunction;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var _interactionsBreakpoints = __webpack_require__(/*! ./interactions-breakpoints.js */ "../modules/interactions/assets/js/interactions-breakpoints.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function config() {
  var _window$ElementorInte, _window$ElementorInte2;
  return (_window$ElementorInte = (_window$ElementorInte2 = window.ElementorInteractionsConfig) === null || _window$ElementorInte2 === void 0 ? void 0 : _window$ElementorInte2.constants) !== null && _window$ElementorInte !== void 0 ? _window$ElementorInte : {};
}
function skipInteraction(interaction) {
  var _interaction$breakpoi;
  var breakpoint = (0, _interactionsBreakpoints.getActiveBreakpoint)();
  return interaction === null || interaction === void 0 || (_interaction$breakpoi = interaction.breakpoints) === null || _interaction$breakpoi === void 0 || (_interaction$breakpoi = _interaction$breakpoi.excluded) === null || _interaction$breakpoi === void 0 ? void 0 : _interaction$breakpoi.includes(breakpoint);
}
function extractInteractionId(interaction) {
  if ('interaction-item' === (interaction === null || interaction === void 0 ? void 0 : interaction.$$type) && interaction !== null && interaction !== void 0 && interaction.value) {
    var _interaction$value$in;
    return ((_interaction$value$in = interaction.value.interaction_id) === null || _interaction$value$in === void 0 ? void 0 : _interaction$value$in.value) || null;
  }
  return null;
}
function motionFunc(name) {
  var _window, _window2;
  if ('function' !== typeof ((_window = window) === null || _window === void 0 || (_window = _window.Motion) === null || _window === void 0 ? void 0 : _window[name])) {
    return undefined;
  }
  return (_window2 = window) === null || _window2 === void 0 || (_window2 = _window2.Motion) === null || _window2 === void 0 ? void 0 : _window2[name];
}
function getAnimateFunction() {
  return motionFunc('animate');
}
function getInViewFunction() {
  return motionFunc('inView');
}
function waitForAnimateFunction(callback) {
  var maxAttempts = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 10;
  if (getAnimateFunction()) {
    callback();
    return;
  }
  if (maxAttempts > 0) {
    setTimeout(function () {
      return waitForAnimateFunction(callback, maxAttempts - 1);
    }, 100);
  }
}
function parseInteractionsData(data) {
  if ('string' === typeof data) {
    try {
      return JSON.parse(data);
    } catch (_unused) {
      return null;
    }
  }
  return data;
}
function unwrapInteractionValue(propValue) {
  var fallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  // Supports Elementor's typed wrapper shape: { $$type: '...', value: ... }.
  if (propValue && 'object' === (0, _typeof2.default)(propValue) && '$$type' in propValue) {
    return propValue.value;
  }
  return propValue !== null && propValue !== void 0 ? propValue : fallback;
}
function timingValueToMs(timingValue, fallbackMs) {
  if (null === timingValue || undefined === timingValue) {
    return fallbackMs;
  }
  var unwrapped = unwrapInteractionValue(timingValue);
  if ('number' === typeof unwrapped) {
    return unwrapped;
  }
  var sizeObj = unwrapInteractionValue(unwrapped);
  var size = sizeObj === null || sizeObj === void 0 ? void 0 : sizeObj.size;
  var unit = (sizeObj === null || sizeObj === void 0 ? void 0 : sizeObj.unit) || 'ms';
  if ('number' !== typeof size) {
    return fallbackMs;
  }
  if ('s' === unit) {
    return size * 1000;
  }
  return size;
}
function resetElementStyles(element) {
  if (!element) {
    return;
  }
  element.style.transition = '';
  element.style.transform = '';
  element.style.opacity = '';
}
var TRANSFORM_EPSILON = 0.001;
var radiansToDegrees = function radiansToDegrees(radians) {
  return radians * (180 / Math.PI);
};
var isNear = function isNear(value, expected) {
  return Math.abs(value - expected) <= TRANSFORM_EPSILON;
};
var isNearZero = function isNearZero(value) {
  return isNear(value, 0);
};
var isNearOne = function isNearOne(value) {
  return isNear(value, 1);
};
function parseMatrixValues(transformValue) {
  var match = transformValue.match(/^matrix(3d)?\((.+)\)$/);
  if (!match) {
    return null;
  }
  return match[2].split(',').map(function (token) {
    return Number.parseFloat(token.trim());
  }).filter(function (value) {
    return Number.isFinite(value);
  });
}
function createMatrixFromTransform(transformValue) {
  if (!transformValue || 'none' === transformValue) {
    return null;
  }
  var matrixFactories = [window.DOMMatrixReadOnly, window.DOMMatrix].filter(function (Factory) {
    return 'function' === typeof Factory;
  });
  var _iterator = _createForOfIteratorHelper(matrixFactories),
    _step;
  try {
    for (_iterator.s(); !(_step = _iterator.n()).done;) {
      var MatrixFactory = _step.value;
      try {
        var _ref, _matrix$a, _ref2, _matrix$b, _ref3, _matrix$c, _ref4, _matrix$d, _ref5, _matrix$e, _ref6, _matrix$f;
        var matrix = new MatrixFactory(transformValue);
        // CSS matrix(a,b,c,d,e,f): x' = ax + cy + e, y' = bx + dy + f
        // matrixXfromY = how much input Y contributes to output X (etc.)
        var compactMatrix = {
          matrixXfromX: (_ref = (_matrix$a = matrix.a) !== null && _matrix$a !== void 0 ? _matrix$a : matrix.m11) !== null && _ref !== void 0 ? _ref : 1,
          matrixYfromX: (_ref2 = (_matrix$b = matrix.b) !== null && _matrix$b !== void 0 ? _matrix$b : matrix.m12) !== null && _ref2 !== void 0 ? _ref2 : 0,
          matrixXfromY: (_ref3 = (_matrix$c = matrix.c) !== null && _matrix$c !== void 0 ? _matrix$c : matrix.m21) !== null && _ref3 !== void 0 ? _ref3 : 0,
          matrixYfromY: (_ref4 = (_matrix$d = matrix.d) !== null && _matrix$d !== void 0 ? _matrix$d : matrix.m22) !== null && _ref4 !== void 0 ? _ref4 : 1,
          matrixTranslateX: (_ref5 = (_matrix$e = matrix.e) !== null && _matrix$e !== void 0 ? _matrix$e : matrix.m41) !== null && _ref5 !== void 0 ? _ref5 : 0,
          matrixTranslateY: (_ref6 = (_matrix$f = matrix.f) !== null && _matrix$f !== void 0 ? _matrix$f : matrix.m42) !== null && _ref6 !== void 0 ? _ref6 : 0
        };
        if (Object.values(compactMatrix).every(Number.isFinite)) {
          return compactMatrix;
        }
      } catch (_unused2) {}
    }
  } catch (err) {
    _iterator.e(err);
  } finally {
    _iterator.f();
  }
  var parsedValues = parseMatrixValues(transformValue);
  if (!parsedValues) {
    return null;
  }
  if (6 === parsedValues.length) {
    var _parsedValues = (0, _slicedToArray2.default)(parsedValues, 6),
      matrixXfromX = _parsedValues[0],
      matrixYfromX = _parsedValues[1],
      matrixXfromY = _parsedValues[2],
      matrixYfromY = _parsedValues[3],
      matrixTranslateX = _parsedValues[4],
      matrixTranslateY = _parsedValues[5];
    return {
      matrixXfromX: matrixXfromX,
      matrixYfromX: matrixYfromX,
      matrixXfromY: matrixXfromY,
      matrixYfromY: matrixYfromY,
      matrixTranslateX: matrixTranslateX,
      matrixTranslateY: matrixTranslateY
    };
  }
  if (16 === parsedValues.length) {
    var _parsedValues2 = (0, _slicedToArray2.default)(parsedValues, 14),
      _matrixXfromX = _parsedValues2[0],
      _matrixYfromX = _parsedValues2[1],
      _matrixXfromY = _parsedValues2[4],
      _matrixYfromY = _parsedValues2[5],
      _matrixTranslateX = _parsedValues2[12],
      _matrixTranslateY = _parsedValues2[13];
    return {
      matrixXfromX: _matrixXfromX,
      matrixYfromX: _matrixYfromX,
      matrixXfromY: _matrixXfromY,
      matrixYfromY: _matrixYfromY,
      matrixTranslateX: _matrixTranslateX,
      matrixTranslateY: _matrixTranslateY
    };
  }
  return null;
}
function getTransformBaselineFromComputedStyle(element) {
  if (!element) {
    return null;
  }
  var computedStyle = window.getComputedStyle(element);
  var matrix = createMatrixFromTransform((computedStyle === null || computedStyle === void 0 ? void 0 : computedStyle.transform) || '');
  if (!matrix) {
    return null;
  }
  var matrixXfromX = matrix.matrixXfromX,
    matrixYfromX = matrix.matrixYfromX,
    matrixXfromY = matrix.matrixXfromY,
    matrixYfromY = matrix.matrixYfromY,
    matrixTranslateX = matrix.matrixTranslateX,
    matrixTranslateY = matrix.matrixTranslateY;
  var scaleX = Math.hypot(matrixXfromX, matrixYfromX);
  var determinant = matrixXfromX * matrixYfromY - matrixYfromX * matrixXfromY;
  var scaleY = scaleX ? determinant / scaleX : Math.hypot(matrixXfromY, matrixYfromY);
  var rotate = radiansToDegrees(Math.atan2(matrixYfromX, matrixXfromX));
  var shear = scaleX ? (matrixXfromX * matrixXfromY + matrixYfromX * matrixYfromY) / (scaleX * scaleX) : 0;
  var skewX = radiansToDegrees(Math.atan(shear));
  return {
    x: matrixTranslateX,
    y: matrixTranslateY,
    scaleX: Number.isFinite(scaleX) ? scaleX : 1,
    scaleY: Number.isFinite(scaleY) ? scaleY : 1,
    rotate: Number.isFinite(rotate) ? rotate : 0,
    skewX: Number.isFinite(skewX) ? skewX : 0
  };
}
function preserveTransformKeyframes(keyframes, baseline) {
  if (!baseline) {
    return keyframes;
  }
  var mergedKeyframes = _objectSpread({}, keyframes);
  var hasScaleShorthand = mergedKeyframes.scale !== undefined;
  var canSetScaleX = mergedKeyframes.scaleX === undefined && !isNearOne(baseline.scaleX);
  var canSetScaleY = mergedKeyframes.scaleY === undefined && !isNearOne(baseline.scaleY);
  if (mergedKeyframes.x === undefined && !isNearZero(baseline.x)) {
    mergedKeyframes.x = [baseline.x, baseline.x];
  }
  if (mergedKeyframes.y === undefined && !isNearZero(baseline.y)) {
    mergedKeyframes.y = [baseline.y, baseline.y];
  }
  if (!hasScaleShorthand) {
    if (canSetScaleX && canSetScaleY && isNear(baseline.scaleX, baseline.scaleY)) {
      mergedKeyframes.scale = [baseline.scaleX, baseline.scaleX];
    } else {
      if (canSetScaleX) {
        mergedKeyframes.scaleX = [baseline.scaleX, baseline.scaleX];
      }
      if (canSetScaleY) {
        mergedKeyframes.scaleY = [baseline.scaleY, baseline.scaleY];
      }
    }
  }
  if (mergedKeyframes.rotate === undefined && mergedKeyframes.rotateZ === undefined && !isNearZero(baseline.rotate)) {
    mergedKeyframes.rotate = [baseline.rotate, baseline.rotate];
  }
  if (mergedKeyframes.skew === undefined && mergedKeyframes.skewX === undefined && !isNearZero(baseline.skewX)) {
    mergedKeyframes.skewX = [baseline.skewX, baseline.skewX];
  }
  return mergedKeyframes;
}

// Expose on elementorModules for Pro and other consumers.
window.elementorModules = window.elementorModules || {};
window.elementorModules.interactions = {
  config: config,
  skipInteraction: skipInteraction,
  extractInteractionId: extractInteractionId,
  getAnimateFunction: getAnimateFunction,
  getInViewFunction: getInViewFunction,
  waitForAnimateFunction: waitForAnimateFunction,
  parseInteractionsData: parseInteractionsData,
  unwrapInteractionValue: unwrapInteractionValue,
  timingValueToMs: timingValueToMs,
  resetElementStyles: resetElementStyles,
  getTransformBaselineFromComputedStyle: getTransformBaselineFromComputedStyle,
  preserveTransformKeyframes: preserveTransformKeyframes
};
})();

/******/ })()
;
//# sourceMappingURL=interactions-shared-utils.js.map
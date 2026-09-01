/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/goober/dist/goober.modern.js":
/*!***************************************************!*\
  !*** ./node_modules/goober/dist/goober.modern.js ***!
  \***************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   css: function() { return /* binding */ u; },
/* harmony export */   extractCss: function() { return /* binding */ r; },
/* harmony export */   glob: function() { return /* binding */ b; },
/* harmony export */   keyframes: function() { return /* binding */ h; },
/* harmony export */   setup: function() { return /* binding */ m; },
/* harmony export */   styled: function() { return /* binding */ w; }
/* harmony export */ });
let e={data:""},t=t=>{if("object"==typeof window){let e=(t?t.querySelector("#_goober"):window._goober)||Object.assign(document.createElement("style"),{innerHTML:" ",id:"_goober"});return e.nonce=window.__nonce__,e.parentNode||(t||document.head).appendChild(e),e.firstChild}return t||e},r=e=>{let r=t(e),l=r.data;return r.data="",l},l=/(?:([\u0080-\uFFFF\w-%@]+) *:? *([^{;]+?);|([^;}{]*?) *{)|(}\s*)/g,a=/\/\*[^]*?\*\/|  +/g,n=/\n+/g,o=(e,t)=>{let r="",l="",a="";for(let n in e){let c=e[n];"@"==n[0]?"i"==n[1]?r=n+" "+c+";":l+="f"==n[1]?o(c,n):n+"{"+o(c,"k"==n[1]?"":t)+"}":"object"==typeof c?l+=o(c,t?t.replace(/([^,])+/g,e=>n.replace(/([^,]*:\S+\([^)]*\))|([^,])+/g,t=>/&/.test(t)?t.replace(/&/g,e):e?e+" "+t:t)):n):null!=c&&(n=/^--/.test(n)?n:n.replace(/[A-Z]/g,"-$&").toLowerCase(),a+=o.p?o.p(n,c):n+":"+c+";")}return r+(t&&a?t+"{"+a+"}":a)+l},c={},s=e=>{if("object"==typeof e){let t="";for(let r in e)t+=r+s(e[r]);return t}return e},i=(e,t,r,i,p)=>{let u=s(e),d=c[u]||(c[u]=(e=>{let t=0,r=11;for(;t<e.length;)r=101*r+e.charCodeAt(t++)>>>0;return"go"+r})(u));if(!c[d]){let t=u!==e?e:(e=>{let t,r,o=[{}];for(;t=l.exec(e.replace(a,""));)t[4]?o.shift():t[3]?(r=t[3].replace(n," ").trim(),o.unshift(o[0][r]=o[0][r]||{})):o[0][t[1]]=t[2].replace(n," ").trim();return o[0]})(e);c[d]=o(p?{["@keyframes "+d]:t}:t,r?"":"."+d)}let f=r&&c.g?c.g:null;return r&&(c.g=c[d]),((e,t,r,l)=>{l?t.data=t.data.replace(l,e):-1===t.data.indexOf(e)&&(t.data=r?e+t.data:t.data+e)})(c[d],t,i,f),d},p=(e,t,r)=>e.reduce((e,l,a)=>{let n=t[a];if(n&&n.call){let e=n(r),t=e&&e.props&&e.props.className||/^go/.test(e)&&e;n=t?"."+t:e&&"object"==typeof e?e.props?"":o(e,""):!1===e?"":e}return e+l+(null==n?"":n)},"");function u(e){let r=this||{},l=e.call?e(r.p):e;return i(l.unshift?l.raw?p(l,[].slice.call(arguments,1),r.p):l.reduce((e,t)=>Object.assign(e,t&&t.call?t(r.p):t),{}):l,t(r.target),r.g,r.o,r.k)}let d,f,g,b=u.bind({g:1}),h=u.bind({k:1});function m(e,t,r,l){o.p=t,d=e,f=r,g=l}function w(e,t){let r=this||{};return function(){let l=arguments;function a(n,o){let c=Object.assign({},n),s=c.className||a.className;r.p=Object.assign({theme:f&&f()},c),r.o=/ *go\d+/.test(s),c.className=u.apply(r,l)+(s?" "+s:""),t&&(c.ref=o);let i=e;return e[0]&&(i=c.as||e,delete c.as),g&&i[0]&&g(c),d(i,c)}return t?t(a):a}}


/***/ }),

/***/ "./node_modules/notistack/node_modules/clsx/dist/clsx.m.js":
/*!*****************************************************************!*\
  !*** ./node_modules/notistack/node_modules/clsx/dist/clsx.m.js ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clsx: function() { return /* binding */ clsx; }
/* harmony export */ });
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f);else for(t in e)e[t]&&(n&&(n+=" "),n+=t);return n}function clsx(){for(var e,t,f=0,n="";f<arguments.length;)(e=arguments[f++])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ __webpack_exports__["default"] = (clsx);

/***/ }),

/***/ "./node_modules/notistack/notistack.esm.js":
/*!*************************************************!*\
  !*** ./node_modules/notistack/notistack.esm.js ***!
  \*************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MaterialDesignContent: function() { return /* binding */ MaterialDesignContent$1; },
/* harmony export */   SnackbarContent: function() { return /* binding */ SnackbarContent; },
/* harmony export */   SnackbarProvider: function() { return /* binding */ SnackbarProvider; },
/* harmony export */   Transition: function() { return /* binding */ Transition; },
/* harmony export */   closeSnackbar: function() { return /* binding */ closeSnackbar; },
/* harmony export */   enqueueSnackbar: function() { return /* binding */ enqueueSnackbar; },
/* harmony export */   useSnackbar: function() { return /* binding */ useSnackbar; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var clsx__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! clsx */ "./node_modules/notistack/node_modules/clsx/dist/clsx.m.js");
/* harmony import */ var goober__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! goober */ "./node_modules/goober/dist/goober.modern.js");





function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

function _inheritsLoose(subClass, superClass) {
  subClass.prototype = Object.create(superClass.prototype);
  subClass.prototype.constructor = subClass;
  subClass.__proto__ = superClass;
}

function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

var noOp = function noOp() {
  return '';
};

var SnackbarContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createContext({
  enqueueSnackbar: noOp,
  closeSnackbar: noOp
});

var breakpoints = {
  downXs: '@media (max-width:599.95px)',
  upSm: '@media (min-width:600px)'
};

var capitalise = function capitalise(text) {
  return text.charAt(0).toUpperCase() + text.slice(1);
};

var originKeyExtractor = function originKeyExtractor(anchor) {
  return "" + capitalise(anchor.vertical) + capitalise(anchor.horizontal);
};
var isDefined = function isDefined(value) {
  return !!value || value === 0;
};

var UNMOUNTED = 'unmounted';
var EXITED = 'exited';
var ENTERING = 'entering';
var ENTERED = 'entered';
var EXITING = 'exiting';

var Transition = /*#__PURE__*/function (_React$Component) {
  _inheritsLoose(Transition, _React$Component);

  function Transition(props) {
    var _this;

    _this = _React$Component.call(this, props) || this;
    var appear = props.appear;
    var initialStatus;
    _this.appearStatus = null;

    if (props["in"]) {
      if (appear) {
        initialStatus = EXITED;
        _this.appearStatus = ENTERING;
      } else {
        initialStatus = ENTERED;
      }
    } else if (props.unmountOnExit || props.mountOnEnter) {
      initialStatus = UNMOUNTED;
    } else {
      initialStatus = EXITED;
    }

    _this.state = {
      status: initialStatus
    };
    _this.nextCallback = null;
    return _this;
  }

  Transition.getDerivedStateFromProps = function getDerivedStateFromProps(_ref, prevState) {
    var nextIn = _ref["in"];

    if (nextIn && prevState.status === UNMOUNTED) {
      return {
        status: EXITED
      };
    }

    return null;
  };

  var _proto = Transition.prototype;

  _proto.componentDidMount = function componentDidMount() {
    this.updateStatus(true, this.appearStatus);
  };

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var nextStatus = null;

    if (prevProps !== this.props) {
      var status = this.state.status;

      if (this.props["in"]) {
        if (status !== ENTERING && status !== ENTERED) {
          nextStatus = ENTERING;
        }
      } else if (status === ENTERING || status === ENTERED) {
        nextStatus = EXITING;
      }
    }

    this.updateStatus(false, nextStatus);
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    this.cancelNextCallback();
  };

  _proto.getTimeouts = function getTimeouts() {
    var timeout = this.props.timeout;
    var enter = timeout;
    var exit = timeout;

    if (timeout != null && typeof timeout !== 'number' && typeof timeout !== 'string') {
      exit = timeout.exit;
      enter = timeout.enter;
    }

    return {
      exit: exit,
      enter: enter
    };
  };

  _proto.updateStatus = function updateStatus(mounting, nextStatus) {
    if (mounting === void 0) {
      mounting = false;
    }

    if (nextStatus !== null) {
      this.cancelNextCallback();

      if (nextStatus === ENTERING) {
        this.performEnter(mounting);
      } else {
        this.performExit();
      }
    } else if (this.props.unmountOnExit && this.state.status === EXITED) {
      this.setState({
        status: UNMOUNTED
      });
    }
  };

  _proto.performEnter = function performEnter(mounting) {
    var _this2 = this;

    var enter = this.props.enter;
    var isAppearing = mounting;
    var timeouts = this.getTimeouts();

    if (!mounting && !enter) {
      this.safeSetState({
        status: ENTERED
      }, function () {
        if (_this2.props.onEntered) {
          _this2.props.onEntered(_this2.node, isAppearing);
        }
      });
      return;
    }

    if (this.props.onEnter) {
      this.props.onEnter(this.node, isAppearing);
    }

    this.safeSetState({
      status: ENTERING
    }, function () {
      if (_this2.props.onEntering) {
        _this2.props.onEntering(_this2.node, isAppearing);
      }

      _this2.onTransitionEnd(timeouts.enter, function () {
        _this2.safeSetState({
          status: ENTERED
        }, function () {
          if (_this2.props.onEntered) {
            _this2.props.onEntered(_this2.node, isAppearing);
          }
        });
      });
    });
  };

  _proto.performExit = function performExit() {
    var _this3 = this;

    var exit = this.props.exit;
    var timeouts = this.getTimeouts(); // no exit animation skip right to EXITED

    if (!exit) {
      this.safeSetState({
        status: EXITED
      }, function () {
        if (_this3.props.onExited) {
          _this3.props.onExited(_this3.node);
        }
      });
      return;
    }

    if (this.props.onExit) {
      this.props.onExit(this.node);
    }

    this.safeSetState({
      status: EXITING
    }, function () {
      if (_this3.props.onExiting) {
        _this3.props.onExiting(_this3.node);
      }

      _this3.onTransitionEnd(timeouts.exit, function () {
        _this3.safeSetState({
          status: EXITED
        }, function () {
          if (_this3.props.onExited) {
            _this3.props.onExited(_this3.node);
          }
        });
      });
    });
  };

  _proto.cancelNextCallback = function cancelNextCallback() {
    if (this.nextCallback !== null && this.nextCallback.cancel) {
      this.nextCallback.cancel();
      this.nextCallback = null;
    }
  };

  _proto.safeSetState = function safeSetState(nextState, callback) {
    callback = this.setNextCallback(callback);
    this.setState(nextState, callback);
  };

  _proto.setNextCallback = function setNextCallback(callback) {
    var _this4 = this;

    var active = true;

    this.nextCallback = function () {
      if (active) {
        active = false;
        _this4.nextCallback = null;
        callback();
      }
    };

    this.nextCallback.cancel = function () {
      active = false;
    };

    return this.nextCallback;
  };

  _proto.onTransitionEnd = function onTransitionEnd(timeout, handler) {
    this.setNextCallback(handler);
    var doesNotHaveTimeoutOrListener = timeout == null && !this.props.addEndListener;

    if (!this.node || doesNotHaveTimeoutOrListener) {
      setTimeout(this.nextCallback, 0);
      return;
    }

    if (this.props.addEndListener) {
      this.props.addEndListener(this.node, this.nextCallback);
    }

    if (timeout != null) {
      setTimeout(this.nextCallback, timeout);
    }
  };

  _proto.render = function render() {
    var status = this.state.status;

    if (status === UNMOUNTED) {
      return null;
    }

    var _this$props = this.props,
        children = _this$props.children,
        childProps = _objectWithoutPropertiesLoose(_this$props, ["children", "in", "mountOnEnter", "unmountOnExit", "appear", "enter", "exit", "timeout", "addEndListener", "onEnter", "onEntering", "onEntered", "onExit", "onExiting", "onExited", "nodeRef"]);

    return children(status, childProps);
  };

  _createClass(Transition, [{
    key: "node",
    get: function get() {
      var _this$props$nodeRef;

      var node = (_this$props$nodeRef = this.props.nodeRef) === null || _this$props$nodeRef === void 0 ? void 0 : _this$props$nodeRef.current;

      if (!node) {
        throw new Error('notistack - Custom snackbar is not refForwarding');
      }

      return node;
    }
  }]);

  return Transition;
}((react__WEBPACK_IMPORTED_MODULE_0___default().Component));

function noop() {//
}

Transition.defaultProps = {
  "in": false,
  mountOnEnter: false,
  unmountOnExit: false,
  appear: false,
  enter: true,
  exit: true,
  onEnter: noop,
  onEntering: noop,
  onEntered: noop,
  onExit: noop,
  onExiting: noop,
  onExited: noop
};

/**
 * Credit to MUI team @ https://mui.com
 */
/**
 * passes {value} to {ref}
 *
 * Useful if you want to expose the ref of an inner component to the public API
 * while still using it inside the component.
 * @param ref A ref callback or ref object. If anything falsy, this is a no-op.
 */

function setRef(ref, value) {
  if (typeof ref === 'function') {
    ref(value);
  } else if (ref) {
    ref.current = value;
  }
}

function useForkRef(refA, refB) {
  /**
   * This will create a new function if the ref props change and are defined.
   * This means react will call the old forkRef with `null` and the new forkRef
   * with the ref. Cleanup naturally emerges from this behavior.
   */
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
    if (refA == null && refB == null) {
      return null;
    }

    return function (refValue) {
      setRef(refA, refValue);
      setRef(refB, refValue);
    };
  }, [refA, refB]);
}

function getTransitionProps(props) {
  var timeout = props.timeout,
      _props$style = props.style,
      style = _props$style === void 0 ? {} : _props$style,
      mode = props.mode;
  return {
    duration: typeof timeout === 'object' ? timeout[mode] || 0 : timeout,
    easing: style.transitionTimingFunction,
    delay: style.transitionDelay
  };
}

/**
 * Credit to MUI team @ https://mui.com
 */
var defaultEasing = {
  // This is the most common easing curve.
  easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
  // Objects enter the screen at full velocity from off-screen and
  // slowly decelerate to a resting point.
  easeOut: 'cubic-bezier(0.0, 0, 0.2, 1)',
  // Objects leave the screen at full velocity. They do not decelerate when off-screen.
  easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
  // The sharp curve is used by objects that may return to the screen at any time.
  sharp: 'cubic-bezier(0.4, 0, 0.6, 1)'
};
/**
 * CSS hack to force a repaint
 */

var reflow = function reflow(node) {
  // We have to do something with node.scrollTop.
  // Otherwise it's removed from the compiled code by optimisers
  // eslint-disable-next-line no-self-assign
  node.scrollTop = node.scrollTop;
};

var formatMs = function formatMs(milliseconds) {
  return Math.round(milliseconds) + "ms";
};

function createTransition(props, options) {
  if (props === void 0) {
    props = ['all'];
  }

  var _ref = options || {},
      _ref$duration = _ref.duration,
      duration = _ref$duration === void 0 ? 300 : _ref$duration,
      _ref$easing = _ref.easing,
      easing = _ref$easing === void 0 ? defaultEasing.easeInOut : _ref$easing,
      _ref$delay = _ref.delay,
      delay = _ref$delay === void 0 ? 0 : _ref$delay;

  var properties = Array.isArray(props) ? props : [props];
  return properties.map(function (animatedProp) {
    var formattedDuration = typeof duration === 'string' ? duration : formatMs(duration);
    var formattedDelay = typeof delay === 'string' ? delay : formatMs(delay);
    return animatedProp + " " + formattedDuration + " " + easing + " " + formattedDelay;
  }).join(',');
}

function ownerDocument(node) {
  return node && node.ownerDocument || document;
}

function ownerWindow(node) {
  var doc = ownerDocument(node);
  return doc.defaultView || window;
}
/**
 * Corresponds to 10 frames at 60 Hz.
 * A few bytes payload overhead when lodash/debounce is ~3 kB and debounce ~300 B.
 */


function debounce(func, wait) {
  if (wait === void 0) {
    wait = 166;
  }

  var timeout;

  function debounced() {
    var _this = this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    var later = function later() {
      // @ts-ignore
      func.apply(_this, args);
    };

    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  }

  debounced.clear = function () {
    clearTimeout(timeout);
  };

  return debounced;
}
/**
 * Translate the node so it can't be seen on the screen.
 * Later, we're going to translate the node back to its original location with `none`.
 */


function getTranslateValue(direction, node) {
  var rect = node.getBoundingClientRect();
  var containerWindow = ownerWindow(node);
  var transform;

  if (node.fakeTransform) {
    transform = node.fakeTransform;
  } else {
    var computedStyle = containerWindow.getComputedStyle(node);
    transform = computedStyle.getPropertyValue('-webkit-transform') || computedStyle.getPropertyValue('transform');
  }

  var offsetX = 0;
  var offsetY = 0;

  if (transform && transform !== 'none' && typeof transform === 'string') {
    var transformValues = transform.split('(')[1].split(')')[0].split(',');
    offsetX = parseInt(transformValues[4], 10);
    offsetY = parseInt(transformValues[5], 10);
  }

  switch (direction) {
    case 'left':
      return "translateX(" + (containerWindow.innerWidth + offsetX - rect.left) + "px)";

    case 'right':
      return "translateX(-" + (rect.left + rect.width - offsetX) + "px)";

    case 'up':
      return "translateY(" + (containerWindow.innerHeight + offsetY - rect.top) + "px)";

    default:
      // down
      return "translateY(-" + (rect.top + rect.height - offsetY) + "px)";
  }
}

function setTranslateValue(direction, node) {
  if (!node) return;
  var transform = getTranslateValue(direction, node);

  if (transform) {
    node.style.webkitTransform = transform;
    node.style.transform = transform;
  }
}

var Slide = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(function (props, ref) {
  var children = props.children,
      _props$direction = props.direction,
      direction = _props$direction === void 0 ? 'down' : _props$direction,
      inProp = props["in"],
      style = props.style,
      _props$timeout = props.timeout,
      timeout = _props$timeout === void 0 ? 0 : _props$timeout,
      onEnter = props.onEnter,
      onEntered = props.onEntered,
      onExit = props.onExit,
      onExited = props.onExited,
      other = _objectWithoutPropertiesLoose(props, ["children", "direction", "in", "style", "timeout", "onEnter", "onEntered", "onExit", "onExited"]);

  var nodeRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  var handleRefIntermediary = useForkRef(children.ref, nodeRef);
  var handleRef = useForkRef(handleRefIntermediary, ref);

  var handleEnter = function handleEnter(node, isAppearing) {
    setTranslateValue(direction, node);
    reflow(node);

    if (onEnter) {
      onEnter(node, isAppearing);
    }
  };

  var handleEntering = function handleEntering(node) {
    var easing = (style === null || style === void 0 ? void 0 : style.transitionTimingFunction) || defaultEasing.easeOut;
    var transitionProps = getTransitionProps({
      timeout: timeout,
      mode: 'enter',
      style: _extends({}, style, {
        transitionTimingFunction: easing
      })
    });
    node.style.webkitTransition = createTransition('-webkit-transform', transitionProps);
    node.style.transition = createTransition('transform', transitionProps);
    node.style.webkitTransform = 'none';
    node.style.transform = 'none';
  };

  var handleExit = function handleExit(node) {
    var easing = (style === null || style === void 0 ? void 0 : style.transitionTimingFunction) || defaultEasing.sharp;
    var transitionProps = getTransitionProps({
      timeout: timeout,
      mode: 'exit',
      style: _extends({}, style, {
        transitionTimingFunction: easing
      })
    });
    node.style.webkitTransition = createTransition('-webkit-transform', transitionProps);
    node.style.transition = createTransition('transform', transitionProps);
    setTranslateValue(direction, node);

    if (onExit) {
      onExit(node);
    }
  };

  var handleExited = function handleExited(node) {
    // No need for transitions when the component is hidden
    node.style.webkitTransition = '';
    node.style.transition = '';

    if (onExited) {
      onExited(node);
    }
  };

  var updatePosition = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function () {
    if (nodeRef.current) {
      setTranslateValue(direction, nodeRef.current);
    }
  }, [direction]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    // Skip configuration where the position is screen size invariant.
    if (inProp || direction === 'down' || direction === 'right') {
      return undefined;
    }

    var handleResize = debounce(function () {
      if (nodeRef.current) {
        setTranslateValue(direction, nodeRef.current);
      }
    });
    var containerWindow = ownerWindow(nodeRef.current);
    containerWindow.addEventListener('resize', handleResize);
    return function () {
      handleResize.clear();
      containerWindow.removeEventListener('resize', handleResize);
    };
  }, [direction, inProp]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    if (!inProp) {
      // We need to update the position of the drawer when the direction change and
      // when it's hidden.
      updatePosition();
    }
  }, [inProp, updatePosition]);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Transition, Object.assign({
    appear: true,
    nodeRef: nodeRef,
    onEnter: handleEnter,
    onEntered: onEntered,
    onEntering: handleEntering,
    onExit: handleExit,
    onExited: handleExited,
    "in": inProp,
    timeout: timeout
  }, other), function (state, childProps) {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.cloneElement)(children, _extends({
      ref: handleRef,
      style: _extends({
        visibility: state === 'exited' && !inProp ? 'hidden' : undefined
      }, style, {}, children.props.style)
    }, childProps));
  });
});
Slide.displayName = 'Slide';

var SvgIcon = function SvgIcon(props) {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement("svg", Object.assign({
    viewBox: "0 0 24 24",
    focusable: "false",
    style: {
      fontSize: 20,
      marginInlineEnd: 8,
      userSelect: 'none',
      width: '1em',
      height: '1em',
      display: 'inline-block',
      fill: 'currentColor',
      flexShrink: 0
    }
  }, props));
};

var CheckIcon = function CheckIcon() {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SvgIcon, null, react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41\n        10.59L10 14.17L17.59 6.58L19 8L10 17Z"
  }));
};

var WarningIcon = function WarningIcon() {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SvgIcon, null, react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"
  }));
};

var ErrorIcon = function ErrorIcon() {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SvgIcon, null, react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,\n        6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,\n        13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"
  }));
};

var InfoIcon = function InfoIcon() {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SvgIcon, null, react__WEBPACK_IMPORTED_MODULE_0___default().createElement("path", {
    d: "M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,\n        0 22,12A10,10 0 0,0 12,2Z"
  }));
};

var defaultIconVariants = {
  "default": undefined,
  success: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(CheckIcon, null),
  warning: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(WarningIcon, null),
  error: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(ErrorIcon, null),
  info: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(InfoIcon, null)
};

var defaults = {
  maxSnack: 3,
  persist: false,
  hideIconVariant: false,
  disableWindowBlurListener: false,
  variant: 'default',
  autoHideDuration: 5000,
  iconVariant: defaultIconVariants,
  anchorOrigin: {
    vertical: 'bottom',
    horizontal: 'left'
  },
  TransitionComponent: Slide,
  transitionDuration: {
    enter: 225,
    exit: 195
  }
};
/**
 * Derives the right autoHideDuration taking into account the following
 * prority order: 1: Options, 2: Props, 3: default fallback
 */

var getAutoHideDuration = function getAutoHideDuration(optionsDuration, propsDuration) {
  var isNumberOrNull = function isNumberOrNull(numberish) {
    return typeof numberish === 'number' || numberish === null;
  };

  if (isNumberOrNull(optionsDuration)) return optionsDuration;
  if (isNumberOrNull(propsDuration)) return propsDuration;
  return defaults.autoHideDuration;
};
/**
 * Derives the right transitionDuration taking into account the following
 * prority order: 1: Options, 2: Props, 3: default fallback
 */


var getTransitionDuration = function getTransitionDuration(optionsDuration, propsDuration) {
  var is = function is(item, types) {
    return types.some(function (t) {
      return typeof item === t;
    });
  };

  if (is(optionsDuration, ['string', 'number'])) {
    return optionsDuration;
  }

  if (is(optionsDuration, ['object'])) {
    return _extends({}, defaults.transitionDuration, {}, is(propsDuration, ['object']) && propsDuration, {}, optionsDuration);
  }

  if (is(propsDuration, ['string', 'number'])) {
    return propsDuration;
  }

  if (is(propsDuration, ['object'])) {
    return _extends({}, defaults.transitionDuration, {}, propsDuration);
  }

  return defaults.transitionDuration;
};

var merge = function merge(options, props) {
  return function (name, shouldObjectMerge) {
    if (shouldObjectMerge === void 0) {
      shouldObjectMerge = false;
    }

    if (shouldObjectMerge) {
      return _extends({}, defaults[name], {}, props[name], {}, options[name]);
    }

    if (name === 'autoHideDuration') {
      return getAutoHideDuration(options.autoHideDuration, props.autoHideDuration);
    }

    if (name === 'transitionDuration') {
      return getTransitionDuration(options.transitionDuration, props.transitionDuration);
    }

    return options[name] || props[name] || defaults[name];
  };
};

function makeStyles(styles) {
  return Object.entries(styles).reduce(function (acc, _ref) {
    var _extends2;

    var key = _ref[0],
        value = _ref[1];
    return _extends({}, acc, (_extends2 = {}, _extends2[key] = (0,goober__WEBPACK_IMPORTED_MODULE_3__.css)(value), _extends2));
  }, {});
}
var ComponentClasses = {
  SnackbarContainer: 'notistack-SnackbarContainer',
  Snackbar: 'notistack-Snackbar',
  CollapseWrapper: 'notistack-CollapseWrapper',
  MuiContent: 'notistack-MuiContent',
  MuiContentVariant: function MuiContentVariant(variant) {
    return "notistack-MuiContent-" + variant;
  }
};

var classes = /*#__PURE__*/makeStyles({
  root: {
    height: 0
  },
  entered: {
    height: 'auto'
  }
});
var collapsedSize = '0px';
var timeout = 175;
var Collapse = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(function (props, ref) {
  var children = props.children,
      inProp = props["in"],
      onExited = props.onExited;
  var wrapperRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  var nodeRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  var handleRef = useForkRef(ref, nodeRef);

  var getWrapperSize = function getWrapperSize() {
    return wrapperRef.current ? wrapperRef.current.clientHeight : 0;
  };

  var handleEnter = function handleEnter(node) {
    node.style.height = collapsedSize;
  };

  var handleEntering = function handleEntering(node) {
    var wrapperSize = getWrapperSize();

    var _getTransitionProps = getTransitionProps({
      timeout: timeout,
      mode: 'enter'
    }),
        transitionDuration = _getTransitionProps.duration,
        easing = _getTransitionProps.easing;

    node.style.transitionDuration = typeof transitionDuration === 'string' ? transitionDuration : transitionDuration + "ms";
    node.style.height = wrapperSize + "px";
    node.style.transitionTimingFunction = easing || '';
  };

  var handleEntered = function handleEntered(node) {
    node.style.height = 'auto';
  };

  var handleExit = function handleExit(node) {
    node.style.height = getWrapperSize() + "px";
  };

  var handleExiting = function handleExiting(node) {
    reflow(node);

    var _getTransitionProps2 = getTransitionProps({
      timeout: timeout,
      mode: 'exit'
    }),
        transitionDuration = _getTransitionProps2.duration,
        easing = _getTransitionProps2.easing;

    node.style.transitionDuration = typeof transitionDuration === 'string' ? transitionDuration : transitionDuration + "ms";
    node.style.height = collapsedSize;
    node.style.transitionTimingFunction = easing || '';
  };

  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Transition, {
    "in": inProp,
    unmountOnExit: true,
    onEnter: handleEnter,
    onEntered: handleEntered,
    onEntering: handleEntering,
    onExit: handleExit,
    onExited: onExited,
    onExiting: handleExiting,
    nodeRef: nodeRef,
    timeout: timeout
  }, function (state, childProps) {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", Object.assign({
      ref: handleRef,
      className: (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(classes.root, state === 'entered' && classes.entered),
      style: _extends({
        pointerEvents: 'all',
        overflow: 'hidden',
        minHeight: collapsedSize,
        transition: createTransition('height')
      }, state === 'entered' && {
        overflow: 'visible'
      }, {}, state === 'exited' && !inProp && {
        visibility: 'hidden'
      })
    }, childProps), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ref: wrapperRef,
      className: ComponentClasses.CollapseWrapper,
      // Hack to get children with a negative margin to not falsify the height computation.
      style: {
        display: 'flex',
        width: '100%'
      }
    }, children));
  });
});
Collapse.displayName = 'Collapse';

var direction = {
  right: 'left',
  left: 'right',
  bottom: 'up',
  top: 'down'
};
var getSlideDirection = function getSlideDirection(anchorOrigin) {
  if (anchorOrigin.horizontal !== 'center') {
    return direction[anchorOrigin.horizontal];
  }

  return direction[anchorOrigin.vertical];
};
/** Tranforms classes name */

var toSnackbarAnchorOrigin = function toSnackbarAnchorOrigin(anchorOrigin) {
  return "anchorOrigin" + originKeyExtractor(anchorOrigin);
};
/**
 * Omit SnackbarContainer class keys that are not needed for SnackbarItem
 */

var keepSnackbarClassKeys = function keepSnackbarClassKeys(classes) {
  if (classes === void 0) {
    classes = {};
  }

  var containerClasses = {
    containerRoot: true,
    containerAnchorOriginTopCenter: true,
    containerAnchorOriginBottomCenter: true,
    containerAnchorOriginTopRight: true,
    containerAnchorOriginBottomRight: true,
    containerAnchorOriginTopLeft: true,
    containerAnchorOriginBottomLeft: true
  };
  return Object.keys(classes).filter(function (key) {
    return !containerClasses[key];
  }).reduce(function (obj, key) {
    var _extends2;

    return _extends({}, obj, (_extends2 = {}, _extends2[key] = classes[key], _extends2));
  }, {});
};

var noOp$1 = function noOp() {
  /* */
};
/**
 * Credit to MUI team @ https://mui.com
 * Safe chained function.
 *
 * Will only create a new function if needed,
 * otherwise will pass back existing functions or null.
 */


function createChainedFunction(funcs, snackbarId) {
  // @ts-ignore
  return funcs.reduce(function (acc, func) {
    if (func === null || func === undefined) {
      return acc;
    }

    return function chainedFunction() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      var argums = [].concat(args);

      if (snackbarId && argums.indexOf(snackbarId) === -1) {
        argums.push(snackbarId);
      } // @ts-ignore


      acc.apply(this, argums);
      func.apply(this, argums);
    };
  }, noOp$1);
}

/**
 * Credit to MUI team @ https://mui.com
 * https://github.com/facebook/react/issues/14099#issuecomment-440013892
 */
var useEnhancedEffect = typeof window !== 'undefined' ? react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect : react__WEBPACK_IMPORTED_MODULE_0__.useEffect;
function useEventCallback(fn) {
  var ref = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(fn);
  useEnhancedEffect(function () {
    ref.current = fn;
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function () {
    return (// @ts-expect-error hide `this`
      (ref.current).apply(void 0, arguments)
    );
  }, []);
}

/**
 * Credit to MUI team @ https://mui.com
 */
var Snackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(function (props, ref) {
  var children = props.children,
      className = props.className,
      autoHideDuration = props.autoHideDuration,
      _props$disableWindowB = props.disableWindowBlurListener,
      disableWindowBlurListener = _props$disableWindowB === void 0 ? false : _props$disableWindowB,
      onClose = props.onClose,
      id = props.id,
      open = props.open,
      _props$SnackbarProps = props.SnackbarProps,
      SnackbarProps = _props$SnackbarProps === void 0 ? {} : _props$SnackbarProps;
  var timerAutoHide = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();
  var handleClose = useEventCallback(function () {
    if (onClose) {
      onClose.apply(void 0, arguments);
    }
  });
  var setAutoHideTimer = useEventCallback(function (autoHideDurationParam) {
    if (!onClose || autoHideDurationParam == null) {
      return;
    }

    if (timerAutoHide.current) {
      clearTimeout(timerAutoHide.current);
    }

    timerAutoHide.current = setTimeout(function () {
      handleClose(null, 'timeout', id);
    }, autoHideDurationParam);
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    if (open) {
      setAutoHideTimer(autoHideDuration);
    }

    return function () {
      if (timerAutoHide.current) {
        clearTimeout(timerAutoHide.current);
      }
    };
  }, [open, autoHideDuration, setAutoHideTimer]);
  /**
   * Pause the timer when the user is interacting with the Snackbar
   * or when the user hide the window.
   */

  var handlePause = function handlePause() {
    if (timerAutoHide.current) {
      clearTimeout(timerAutoHide.current);
    }
  };
  /**
   * Restart the timer when the user is no longer interacting with the Snackbar
   * or when the window is shown back.
   */


  var handleResume = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function () {
    if (autoHideDuration != null) {
      setAutoHideTimer(autoHideDuration * 0.5);
    }
  }, [autoHideDuration, setAutoHideTimer]);

  var handleMouseEnter = function handleMouseEnter(event) {
    if (SnackbarProps.onMouseEnter) {
      SnackbarProps.onMouseEnter(event);
    }

    handlePause();
  };

  var handleMouseLeave = function handleMouseLeave(event) {
    if (SnackbarProps.onMouseLeave) {
      SnackbarProps.onMouseLeave(event);
    }

    handleResume();
  };

  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    if (!disableWindowBlurListener && open) {
      window.addEventListener('focus', handleResume);
      window.addEventListener('blur', handlePause);
      return function () {
        window.removeEventListener('focus', handleResume);
        window.removeEventListener('blur', handlePause);
      };
    }

    return undefined;
  }, [disableWindowBlurListener, handleResume, open]);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", Object.assign({
    ref: ref
  }, SnackbarProps, {
    className: (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(ComponentClasses.Snackbar, className),
    onMouseEnter: handleMouseEnter,
    onMouseLeave: handleMouseLeave
  }), children);
});
Snackbar.displayName = 'Snackbar';

var _root;
var classes$1 = /*#__PURE__*/makeStyles({
  root: (_root = {
    display: 'flex',
    flexWrap: 'wrap',
    flexGrow: 1
  }, _root[breakpoints.upSm] = {
    flexGrow: 'initial',
    minWidth: '288px'
  }, _root)
});
var SnackbarContent = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(function (_ref, ref) {
  var className = _ref.className,
      props = _objectWithoutPropertiesLoose(_ref, ["className"]);

  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", Object.assign({
    ref: ref,
    className: (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(classes$1.root, className)
  }, props));
});
SnackbarContent.displayName = 'SnackbarContent';

var classes$2 = /*#__PURE__*/makeStyles({
  root: {
    backgroundColor: '#313131',
    fontSize: '0.875rem',
    lineHeight: 1.43,
    letterSpacing: '0.01071em',
    color: '#fff',
    alignItems: 'center',
    padding: '6px 16px',
    borderRadius: '4px',
    boxShadow: '0px 3px 5px -1px rgba(0,0,0,0.2),0px 6px 10px 0px rgba(0,0,0,0.14),0px 1px 18px 0px rgba(0,0,0,0.12)'
  },
  lessPadding: {
    paddingLeft: 8 * 2.5 + "px"
  },
  "default": {
    backgroundColor: '#313131'
  },
  success: {
    backgroundColor: '#43a047'
  },
  error: {
    backgroundColor: '#d32f2f'
  },
  warning: {
    backgroundColor: '#ff9800'
  },
  info: {
    backgroundColor: '#2196f3'
  },
  message: {
    display: 'flex',
    alignItems: 'center',
    padding: '8px 0'
  },
  action: {
    display: 'flex',
    alignItems: 'center',
    marginLeft: 'auto',
    paddingLeft: '16px',
    marginRight: '-8px'
  }
});
var ariaDescribedby = 'notistack-snackbar';
var MaterialDesignContent = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(function (props, forwardedRef) {
  var id = props.id,
      message = props.message,
      componentOrFunctionAction = props.action,
      iconVariant = props.iconVariant,
      variant = props.variant,
      hideIconVariant = props.hideIconVariant,
      style = props.style,
      className = props.className;
  var icon = iconVariant[variant];
  var action = componentOrFunctionAction;

  if (typeof action === 'function') {
    action = action(id);
  }

  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SnackbarContent, {
    ref: forwardedRef,
    role: "alert",
    "aria-describedby": ariaDescribedby,
    style: style,
    className: (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(ComponentClasses.MuiContent, ComponentClasses.MuiContentVariant(variant), classes$2.root, classes$2[variant], className, !hideIconVariant && icon && classes$2.lessPadding)
  }, react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    id: ariaDescribedby,
    className: classes$2.message
  }, !hideIconVariant ? icon : null, message), action && react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: classes$2.action
  }, action));
});
MaterialDesignContent.displayName = 'MaterialDesignContent';
var MaterialDesignContent$1 = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.memo)(MaterialDesignContent);

var styles = /*#__PURE__*/makeStyles({
  wrappedRoot: {
    width: '100%',
    position: 'relative',
    transform: 'translateX(0)',
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
    minWidth: '288px'
  }
});

var SnackbarItem = function SnackbarItem(props) {
  var timeout = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();

  var _useState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true),
      collapsed = _useState[0],
      setCollapsed = _useState[1];

  var handleClose = createChainedFunction([props.snack.onClose, props.onClose]);

  var handleEntered = function handleEntered() {
    if (props.snack.requestClose) {
      handleClose(null, 'instructed', props.snack.id);
    }
  };

  var handleExitedScreen = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(function () {
    timeout.current = setTimeout(function () {
      setCollapsed(function (col) {
        return !col;
      });
    }, 125);
  }, []);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(function () {
    return function () {
      if (timeout.current) {
        clearTimeout(timeout.current);
      }
    };
  }, []);
  var snack = props.snack,
      allClasses = props.classes,
      _props$Component = props.Component,
      Component = _props$Component === void 0 ? MaterialDesignContent$1 : _props$Component;
  var classes = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(function () {
    return keepSnackbarClassKeys(allClasses);
  }, [allClasses]);

  var open = snack.open,
      SnackbarProps = snack.SnackbarProps,
      TransitionComponent = snack.TransitionComponent,
      TransitionProps = snack.TransitionProps,
      transitionDuration = snack.transitionDuration,
      disableWindowBlurListener = snack.disableWindowBlurListener,
      componentOrFunctionContent = snack.content,
      otherSnack = _objectWithoutPropertiesLoose(snack, ["open", "SnackbarProps", "TransitionComponent", "TransitionProps", "transitionDuration", "disableWindowBlurListener", "content", "entered", "requestClose", "onEnter", "onEntered", "onExit", "onExited"]);

  var transitionProps = _extends({
    direction: getSlideDirection(otherSnack.anchorOrigin),
    timeout: transitionDuration
  }, TransitionProps);

  var content = componentOrFunctionContent;

  if (typeof content === 'function') {
    content = content(otherSnack.id, otherSnack.message);
  }

  var callbacks = ['onEnter', 'onEntered', 'onExit', 'onExited'].reduce(function (acc, cbName) {
    var _extends2;

    return _extends({}, acc, (_extends2 = {}, _extends2[cbName] = createChainedFunction([props.snack[cbName], props[cbName]], otherSnack.id), _extends2));
  }, {});
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Collapse, {
    "in": collapsed,
    onExited: callbacks.onExited
  }, react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Snackbar, {
    open: open,
    id: otherSnack.id,
    disableWindowBlurListener: disableWindowBlurListener,
    autoHideDuration: otherSnack.autoHideDuration,
    className: (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(styles.wrappedRoot, classes.root, classes[toSnackbarAnchorOrigin(otherSnack.anchorOrigin)]),
    SnackbarProps: SnackbarProps,
    onClose: handleClose
  }, react__WEBPACK_IMPORTED_MODULE_0___default().createElement(TransitionComponent, Object.assign({}, transitionProps, {
    appear: true,
    "in": open,
    onExit: callbacks.onExit,
    onExited: handleExitedScreen,
    onEnter: callbacks.onEnter,
    // order matters. first callbacks.onEntered to set entered: true,
    // then handleEntered to check if there's a request for closing
    onEntered: createChainedFunction([callbacks.onEntered, handleEntered], otherSnack.id)
  }), content || react__WEBPACK_IMPORTED_MODULE_0___default().createElement(Component, Object.assign({}, otherSnack)))));
};

var _root$1, _rootDense, _left, _right, _center;
var indents = {
  view: {
    "default": 20,
    dense: 4
  },
  snackbar: {
    "default": 6,
    dense: 2
  }
};
var collapseWrapper = "." + ComponentClasses.CollapseWrapper;
var xsWidthMargin = 16;
var styles$1 = /*#__PURE__*/makeStyles({
  root: (_root$1 = {
    boxSizing: 'border-box',
    display: 'flex',
    maxHeight: '100%',
    position: 'fixed',
    zIndex: 1400,
    height: 'auto',
    width: 'auto',
    transition: /*#__PURE__*/createTransition(['top', 'right', 'bottom', 'left', 'max-width'], {
      duration: 300,
      easing: 'ease'
    }),
    // container itself is invisible and should not block clicks, clicks should be passed to its children
    // a pointerEvents: all is applied in the collapse component
    pointerEvents: 'none'
  }, _root$1[collapseWrapper] = {
    padding: indents.snackbar["default"] + "px 0px",
    transition: 'padding 300ms ease 0ms'
  }, _root$1.maxWidth = "calc(100% - " + indents.view["default"] * 2 + "px)", _root$1[breakpoints.downXs] = {
    width: '100%',
    maxWidth: "calc(100% - " + xsWidthMargin * 2 + "px)"
  }, _root$1),
  rootDense: (_rootDense = {}, _rootDense[collapseWrapper] = {
    padding: indents.snackbar.dense + "px 0px"
  }, _rootDense),
  top: {
    top: indents.view["default"] - indents.snackbar["default"] + "px",
    flexDirection: 'column'
  },
  bottom: {
    bottom: indents.view["default"] - indents.snackbar["default"] + "px",
    flexDirection: 'column-reverse'
  },
  left: (_left = {
    left: indents.view["default"] + "px"
  }, _left[breakpoints.upSm] = {
    alignItems: 'flex-start'
  }, _left[breakpoints.downXs] = {
    left: xsWidthMargin + "px"
  }, _left),
  right: (_right = {
    right: indents.view["default"] + "px"
  }, _right[breakpoints.upSm] = {
    alignItems: 'flex-end'
  }, _right[breakpoints.downXs] = {
    right: xsWidthMargin + "px"
  }, _right),
  center: (_center = {
    left: '50%',
    transform: 'translateX(-50%)'
  }, _center[breakpoints.upSm] = {
    alignItems: 'center'
  }, _center)
});

var SnackbarContainer = function SnackbarContainer(props) {
  var _props$classes = props.classes,
      classes = _props$classes === void 0 ? {} : _props$classes,
      anchorOrigin = props.anchorOrigin,
      dense = props.dense,
      children = props.children;
  var combinedClassname = (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(ComponentClasses.SnackbarContainer, styles$1[anchorOrigin.vertical], styles$1[anchorOrigin.horizontal], styles$1.root, // root should come after others to override maxWidth
  classes.containerRoot, classes["containerAnchorOrigin" + originKeyExtractor(anchorOrigin)], dense && styles$1.rootDense);
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement("div", {
    className: combinedClassname
  }, children);
};

var SnackbarContainer$1 = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.memo)(SnackbarContainer);

/* eslint-disable */
var __DEV__ = "development" !== 'production';

var messages = {
  NO_PERSIST_ALL: "Reached maxSnack while all enqueued snackbars have 'persist' flag. Notistack will dismiss the oldest snackbar anyway to allow other ones in the queue to be presented."
};
var warning = (function (messageKey) {
  if (!__DEV__) return;
  var message = messages[messageKey];

  if (typeof console !== 'undefined') {
    console.error("WARNING - notistack: " + message);
  }

  try {
    throw new Error(message);
  } catch (x) {}
});

var isOptions = function isOptions(messageOrOptions) {
  var isMessage = typeof messageOrOptions === 'string' || (0,react__WEBPACK_IMPORTED_MODULE_0__.isValidElement)(messageOrOptions);
  return !isMessage;
};

var enqueueSnackbar;
var closeSnackbar;

var SnackbarProvider = /*#__PURE__*/function (_Component) {
  _inheritsLoose(SnackbarProvider, _Component);

  function SnackbarProvider(props) {
    var _this;

    _this = _Component.call(this, props) || this;
    /**
     * Adds a new snackbar to the queue to be presented.
     * Returns generated or user defined key referencing the new snackbar or null
     */

    _this.enqueueSnackbar = function (messageOrOptions, optsOrUndefined) {
      if (optsOrUndefined === void 0) {
        optsOrUndefined = {};
      }

      if (messageOrOptions === undefined || messageOrOptions === null) {
        throw new Error('enqueueSnackbar called with invalid argument');
      }

      var opts = isOptions(messageOrOptions) ? messageOrOptions : optsOrUndefined;
      var message = isOptions(messageOrOptions) ? messageOrOptions.message : messageOrOptions;

      var key = opts.key,
          preventDuplicate = opts.preventDuplicate,
          options = _objectWithoutPropertiesLoose(opts, ["key", "preventDuplicate"]);

      var hasSpecifiedKey = isDefined(key);
      var id = hasSpecifiedKey ? key : new Date().getTime() + Math.random();
      var merger = merge(options, _this.props);

      var snack = _extends({
        id: id
      }, options, {
        message: message,
        open: true,
        entered: false,
        requestClose: false,
        persist: merger('persist'),
        action: merger('action'),
        content: merger('content'),
        variant: merger('variant'),
        anchorOrigin: merger('anchorOrigin'),
        disableWindowBlurListener: merger('disableWindowBlurListener'),
        autoHideDuration: merger('autoHideDuration'),
        hideIconVariant: merger('hideIconVariant'),
        TransitionComponent: merger('TransitionComponent'),
        transitionDuration: merger('transitionDuration'),
        TransitionProps: merger('TransitionProps', true),
        iconVariant: merger('iconVariant', true),
        style: merger('style', true),
        SnackbarProps: merger('SnackbarProps', true),
        className: (0,clsx__WEBPACK_IMPORTED_MODULE_2__["default"])(_this.props.className, options.className)
      });

      if (snack.persist) {
        snack.autoHideDuration = undefined;
      }

      _this.setState(function (state) {
        if (preventDuplicate === undefined && _this.props.preventDuplicate || preventDuplicate) {
          var compareFunction = function compareFunction(item) {
            return hasSpecifiedKey ? item.id === id : item.message === message;
          };

          var inQueue = state.queue.findIndex(compareFunction) > -1;
          var inView = state.snacks.findIndex(compareFunction) > -1;

          if (inQueue || inView) {
            return state;
          }
        }

        return _this.handleDisplaySnack(_extends({}, state, {
          queue: [].concat(state.queue, [snack])
        }));
      });

      return id;
    };
    /**
     * Reducer: Display snack if there's space for it. Otherwise, immediately
     * begin dismissing the oldest message to start showing the new one.
     */


    _this.handleDisplaySnack = function (state) {
      var snacks = state.snacks;

      if (snacks.length >= _this.maxSnack) {
        return _this.handleDismissOldest(state);
      }

      return _this.processQueue(state);
    };
    /**
     * Reducer: Display items (notifications) in the queue if there's space for them.
     */


    _this.processQueue = function (state) {
      var queue = state.queue,
          snacks = state.snacks;

      if (queue.length > 0) {
        return _extends({}, state, {
          snacks: [].concat(snacks, [queue[0]]),
          queue: queue.slice(1, queue.length)
        });
      }

      return state;
    };
    /**
     * Reducer: Hide oldest snackbar on the screen because there exists a new one which we have to display.
     * (ignoring the one with 'persist' flag. i.e. explicitly told by user not to get dismissed).
     *
     * Note 1: If there is already a message leaving the screen, no new messages are dismissed.
     * Note 2: If the oldest message has not yet entered the screen, only a request to close the
     *         snackbar is made. Once it entered the screen, it will be immediately dismissed.
     */


    _this.handleDismissOldest = function (state) {
      if (state.snacks.some(function (item) {
        return !item.open || item.requestClose;
      })) {
        return state;
      }

      var popped = false;
      var ignore = false;
      var persistentCount = state.snacks.reduce(function (acc, current) {
        return acc + (current.open && current.persist ? 1 : 0);
      }, 0);

      if (persistentCount === _this.maxSnack) {
         true ? warning('NO_PERSIST_ALL') : 0;
        ignore = true;
      }

      var snacks = state.snacks.map(function (item) {
        if (!popped && (!item.persist || ignore)) {
          popped = true;

          if (!item.entered) {
            return _extends({}, item, {
              requestClose: true
            });
          }

          if (item.onClose) {
            item.onClose(null, 'maxsnack', item.id);
          }

          if (_this.props.onClose) {
            _this.props.onClose(null, 'maxsnack', item.id);
          }

          return _extends({}, item, {
            open: false
          });
        }

        return _extends({}, item);
      });
      return _extends({}, state, {
        snacks: snacks
      });
    };
    /**
     * Set the entered state of the snackbar with the given key.
     */


    _this.handleEnteredSnack = function (node, isAppearing, key) {
      if (!isDefined(key)) {
        throw new Error('handleEnteredSnack Cannot be called with undefined key');
      }

      _this.setState(function (_ref) {
        var snacks = _ref.snacks;
        return {
          snacks: snacks.map(function (item) {
            return item.id === key ? _extends({}, item, {
              entered: true
            }) : _extends({}, item);
          })
        };
      });
    };
    /**
     * Hide a snackbar after its timeout.
     */


    _this.handleCloseSnack = function (event, reason, key) {
      // should not use createChainedFunction for onClose.
      // because this.closeSnackbar called this function
      if (_this.props.onClose) {
        _this.props.onClose(event, reason, key);
      }

      var shouldCloseAll = key === undefined;

      _this.setState(function (_ref2) {
        var snacks = _ref2.snacks,
            queue = _ref2.queue;
        return {
          snacks: snacks.map(function (item) {
            if (!shouldCloseAll && item.id !== key) {
              return _extends({}, item);
            }

            return item.entered ? _extends({}, item, {
              open: false
            }) : _extends({}, item, {
              requestClose: true
            });
          }),
          queue: queue.filter(function (item) {
            return item.id !== key;
          })
        };
      });
    };
    /**
     * Close snackbar with the given key
     */


    _this.closeSnackbar = function (key) {
      // call individual snackbar onClose callback passed through options parameter
      var toBeClosed = _this.state.snacks.find(function (item) {
        return item.id === key;
      });

      if (isDefined(key) && toBeClosed && toBeClosed.onClose) {
        toBeClosed.onClose(null, 'instructed', key);
      }

      _this.handleCloseSnack(null, 'instructed', key);
    };
    /**
     * When we set open attribute of a snackbar to false (i.e. after we hide a snackbar),
     * it leaves the screen and immediately after leaving animation is done, this method
     * gets called. We remove the hidden snackbar from state and then display notifications
     * waiting in the queue (if any). If after this process the queue is not empty, the
     * oldest message is dismissed.
     */


    _this.handleExitedSnack = function (node, key) {
      if (!isDefined(key)) {
        throw new Error('handleExitedSnack Cannot be called with undefined key');
      }

      _this.setState(function (state) {
        var newState = _this.processQueue(_extends({}, state, {
          snacks: state.snacks.filter(function (item) {
            return item.id !== key;
          })
        }));

        if (newState.queue.length === 0) {
          return newState;
        }

        return _this.handleDismissOldest(newState);
      });
    };

    enqueueSnackbar = _this.enqueueSnackbar;
    closeSnackbar = _this.closeSnackbar;
    _this.state = {
      snacks: [],
      queue: [],
      contextValue: {
        enqueueSnackbar: _this.enqueueSnackbar.bind(_assertThisInitialized(_this)),
        closeSnackbar: _this.closeSnackbar.bind(_assertThisInitialized(_this))
      }
    };
    return _this;
  }

  var _proto = SnackbarProvider.prototype;

  _proto.render = function render() {
    var _this2 = this;

    var contextValue = this.state.contextValue;
    var _this$props = this.props,
        domRoot = _this$props.domRoot,
        children = _this$props.children,
        _this$props$dense = _this$props.dense,
        dense = _this$props$dense === void 0 ? false : _this$props$dense,
        _this$props$Component = _this$props.Components,
        Components = _this$props$Component === void 0 ? {} : _this$props$Component,
        classes = _this$props.classes;
    var categ = this.state.snacks.reduce(function (acc, current) {
      var _extends2;

      var category = originKeyExtractor(current.anchorOrigin);
      var existingOfCategory = acc[category] || [];
      return _extends({}, acc, (_extends2 = {}, _extends2[category] = [].concat(existingOfCategory, [current]), _extends2));
    }, {});
    var snackbars = Object.keys(categ).map(function (origin) {
      var snacks = categ[origin];
      var nomineeSnack = snacks[0];
      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SnackbarContainer$1, {
        key: origin,
        dense: dense,
        anchorOrigin: nomineeSnack.anchorOrigin,
        classes: classes
      }, snacks.map(function (snack) {
        return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SnackbarItem, {
          key: snack.id,
          snack: snack,
          classes: classes,
          Component: Components[snack.variant],
          onClose: _this2.handleCloseSnack,
          onEnter: _this2.props.onEnter,
          onExit: _this2.props.onExit,
          onExited: createChainedFunction([_this2.handleExitedSnack, _this2.props.onExited], snack.id),
          onEntered: createChainedFunction([_this2.handleEnteredSnack, _this2.props.onEntered], snack.id)
        });
      }));
    });
    return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(SnackbarContext.Provider, {
      value: contextValue
    }, children, domRoot ? (0,react_dom__WEBPACK_IMPORTED_MODULE_1__.createPortal)(snackbars, domRoot) : snackbars);
  };

  _createClass(SnackbarProvider, [{
    key: "maxSnack",
    get: function get() {
      return this.props.maxSnack || defaults.maxSnack;
    }
  }]);

  return SnackbarProvider;
}(react__WEBPACK_IMPORTED_MODULE_0__.Component);

var useSnackbar = (function () {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(SnackbarContext);
});


//# sourceMappingURL=notistack.esm.js.map


/***/ }),

/***/ "./packages/packages/core/editor-notifications/src/components/notifications.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-notifications/src/components/notifications.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NotifyReact: function() { return /* binding */ NotifyReact; },
/* harmony export */   dismissNotification: function() { return /* binding */ dismissNotification; },
/* harmony export */   notify: function() { return /* binding */ notify; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var notistack__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! notistack */ "./node_modules/notistack/notistack.esm.js");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _hooks_use_enqueue_notifications__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-enqueue-notifications */ "./packages/packages/core/editor-notifications/src/hooks/use-enqueue-notifications.tsx");
/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../slice */ "./packages/packages/core/editor-notifications/src/slice.ts");
/* harmony import */ var _sync_get_editing_panel_width__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../sync/get-editing-panel-width */ "./packages/packages/core/editor-notifications/src/sync/get-editing-panel-width.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









// 8 seconds
const AUTO_HIDE_DURATION = 8000;
const DefaultCustomSnackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)((props, ref) => {
  const filteredProps = getFilteredSnackbarProps(props);
  const panelWidth = (0,_sync_get_editing_panel_width__WEBPACK_IMPORTED_MODULE_7__.getEditingPanelWidth)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.ThemeProvider, {
    palette: "unstable"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.SnackbarContent, _extends({
    ref: ref
  }, filteredProps, {
    sx: {
      '&.MuiPaper-root': {
        minWidth: 'max-content'
      },
      ml: panelWidth + 'px'
    }
  })));
});
const AlertSnackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  color,
  icon,
  ...props
}, ref) => {
  const panelWidth = (0,_sync_get_editing_panel_width__WEBPACK_IMPORTED_MODULE_7__.getEditingPanelWidth)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.ThemeProvider, {
    colorScheme: "light",
    palette: "unstable"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Alert, {
    ref: ref,
    variant: "standard",
    color: color,
    icon: icon,
    role: "alert",
    action: props.action,
    onClose: () => (0,notistack__WEBPACK_IMPORTED_MODULE_1__.closeSnackbar)(props.id),
    sx: {
      ml: panelWidth + 'px',
      '& .MuiAlert-message': {
        display: 'flex',
        flexWrap: 'nowrap',
        alignItems: 'center'
      },
      '& .MuiAlert-content': {
        whiteSpace: 'nowrap'
      }
    }
  }, props.message));
});
const PromotionSnackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)((props, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(AlertSnackbar, _extends({
  ref: ref,
  color: "promotion",
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CrownFilledIcon, null)
}, props)));
const InfoSnackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)((props, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(AlertSnackbar, _extends({
  ref: ref,
  color: "info",
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.InfoCircleFilledIcon, null)
}, props)));
const SuccessSnackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)((props, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(AlertSnackbar, _extends({
  ref: ref,
  color: "success",
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CircleCheckFilledIcon, null)
}, props)));
const ErrorSnackbar = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)((props, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(AlertSnackbar, _extends({
  ref: ref,
  color: "error",
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.AlertCircleFilled, null)
}, props)));
const muiToEuiMapper = {
  default: DefaultCustomSnackbar,
  promotion: PromotionSnackbar,
  info: InfoSnackbar,
  success: SuccessSnackbar,
  error: ErrorSnackbar
};
const Handler = () => {
  const notifications = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__useSelector)(state => state.notifications);
  (0,_hooks_use_enqueue_notifications__WEBPACK_IMPORTED_MODULE_5__.useEnqueueNotification)(notifications);
  return null;
};
const Wrapper = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(notistack__WEBPACK_IMPORTED_MODULE_1__.SnackbarProvider, {
    maxSnack: 3,
    autoHideDuration: AUTO_HIDE_DURATION,
    anchorOrigin: {
      horizontal: 'center',
      vertical: 'bottom'
    },
    Components: muiToEuiMapper
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Handler, null));
};

/*
 * This function can be used to trigger notifications from anywhere in the code.
 * even if you're running in a JS environment as opposed to a React environment.
 */
function notify(notification) {
  const store = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__getStore)();
  store?.dispatch((0,_slice__WEBPACK_IMPORTED_MODULE_6__.notifyAction)(notification));
}
function dismissNotification(id) {
  const store = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__getStore)();
  (0,notistack__WEBPACK_IMPORTED_MODULE_1__.closeSnackbar)(id);
  store?.dispatch((0,_slice__WEBPACK_IMPORTED_MODULE_6__.clearAction)({
    id
  }));
}

/*
 * This function can be used to trigger notifications from within a React component.
 * This is the preferred way to trigger notifications.
 */
function NotifyReact(notification) {
  const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__useDispatch)();
  dispatch((0,_slice__WEBPACK_IMPORTED_MODULE_6__.notifyAction)(notification));
}
function getFilteredSnackbarProps(props) {
  const forbiddenProps = ['autoHideDuration', 'persist', 'hideIconVariant', 'iconVariant', 'anchorOrigin'];
  return Object.entries(props).reduce((filteredProps, [key, value]) => {
    if (!forbiddenProps.includes(key)) {
      filteredProps[key] = value;
    }
    return filteredProps;
  }, {});
}
/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./packages/packages/core/editor-notifications/src/hooks/use-enqueue-notifications.tsx":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-notifications/src/hooks/use-enqueue-notifications.tsx ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useEnqueueNotification: function() { return /* binding */ useEnqueueNotification; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var notistack__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! notistack */ "./node_modules/notistack/notistack.esm.js");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../slice */ "./packages/packages/core/editor-notifications/src/slice.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }






const AUTO_HIDE_DURATION = 8000;
function createDefaultAction(notification, onDismiss) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: notification.id
  }, notification.additionalActionProps?.map((additionalAction, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, _extends({
    key: `${index}`
  }, additionalAction))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.CloseButton, {
    "aria-label": "close",
    color: "inherit",
    onClick: onDismiss
  }));
}
function createPromotionAction(notification) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: notification.id
  }, notification.additionalActionProps?.map((additionalAction, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, _extends({
    key: `${index}`
  }, additionalAction))));
}
const useEnqueueNotification = notifications => {
  const {
    enqueueSnackbar
  } = (0,notistack__WEBPACK_IMPORTED_MODULE_1__.useSnackbar)();
  const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__useDispatch)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    Object.values(notifications).forEach(notification => {
      const dismiss = () => {
        (0,notistack__WEBPACK_IMPORTED_MODULE_1__.closeSnackbar)(notification.id);
        dispatch((0,_slice__WEBPACK_IMPORTED_MODULE_4__.clearAction)({
          id: notification.id
        }));
      };
      const useAlertAction = ['promotion', 'info', 'success', 'error'].includes(notification.type);
      const action = useAlertAction ? createPromotionAction(notification) : createDefaultAction(notification, dismiss);
      enqueueSnackbar(notification.message, {
        variant: notification.type,
        key: notification.id,
        onClose: () => dispatch((0,_slice__WEBPACK_IMPORTED_MODULE_4__.clearAction)({
          id: notification.id
        })),
        preventDuplicate: true,
        action,
        autoHideDuration: notification.autoHideDuration ?? AUTO_HIDE_DURATION
      });
    });
  }, [notifications, enqueueSnackbar, dispatch]);
};

/***/ }),

/***/ "./packages/packages/core/editor-notifications/src/init.ts":
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-notifications/src/init.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_notifications__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/notifications */ "./packages/packages/core/editor-notifications/src/components/notifications.tsx");
/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./slice */ "./packages/packages/core/editor-notifications/src/slice.ts");




function init() {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__registerSlice)(_slice__WEBPACK_IMPORTED_MODULE_3__.notificationsSlice);
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'notifications',
    component: _components_notifications__WEBPACK_IMPORTED_MODULE_2__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-notifications/src/slice.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-notifications/src/slice.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearAction: function() { return /* binding */ clearAction; },
/* harmony export */   notificationsSlice: function() { return /* binding */ notificationsSlice; },
/* harmony export */   notifyAction: function() { return /* binding */ notifyAction; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);

const notificationsSlice = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSlice)({
  name: 'notifications',
  initialState: {},
  reducers: {
    notifyAction: (state, action) => {
      const newState = {
        ...state
      };
      if (!newState[action.payload.id]) {
        newState[action.payload.id] = action.payload;
      }
      return newState;
    },
    clearAction: (state, action) => {
      const newState = {
        ...state
      };
      if (newState[action.payload.id]) {
        // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
        delete newState[action.payload.id];
      }
      return newState;
    }
  }
});
const {
  notifyAction,
  clearAction
} = notificationsSlice.actions;

/***/ }),

/***/ "./packages/packages/core/editor-notifications/src/sync/get-editing-panel-width.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-notifications/src/sync/get-editing-panel-width.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getEditingPanelWidth: function() { return /* binding */ getEditingPanelWidth; }
/* harmony export */ });
function getEditingPanelWidth() {
  return document.querySelector('.elementor-panel')?.clientWidth || 0;
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

/***/ "@elementor/store":
/*!****************************************!*\
  !*** external ["elementorV2","store"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["store"];

/***/ }),

/***/ "@elementor/ui":
/*!*************************************!*\
  !*** external ["elementorV2","ui"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["ui"];

/***/ }),

/***/ "react":
/*!**************************!*\
  !*** external ["React"] ***!
  \**************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "react-dom":
/*!*****************************!*\
  !*** external ["ReactDOM"] ***!
  \*****************************/
/***/ (function(module) {

module.exports = window["ReactDOM"];

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
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-notifications/src/index.ts ***!
  \******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NotifyReact: function() { return /* reexport safe */ _components_notifications__WEBPACK_IMPORTED_MODULE_1__.NotifyReact; },
/* harmony export */   dismissNotification: function() { return /* reexport safe */ _components_notifications__WEBPACK_IMPORTED_MODULE_1__.dismissNotification; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   notify: function() { return /* reexport safe */ _components_notifications__WEBPACK_IMPORTED_MODULE_1__.notify; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-notifications/src/init.ts");
/* harmony import */ var _components_notifications__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/notifications */ "./packages/packages/core/editor-notifications/src/components/notifications.tsx");


}();
(window.elementorV2 = window.elementorV2 || {}).editorNotifications = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorNotifications?.init?.();
//# sourceMappingURL=editor-notifications.js.map
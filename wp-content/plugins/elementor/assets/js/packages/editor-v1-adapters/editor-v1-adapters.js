/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/editor-v1-adapters/src/ajax/index.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/ajax/index.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ajax: function() { return /* binding */ ajax; }
/* harmony export */ });
const ajax = {
  async load(data) {
    const extendedWindow = window;
    return new Promise((success, error) => {
      extendedWindow.elementorCommon?.ajax?.load({
        ...data,
        success,
        error
      });
    });
  },
  invalidateCache(data) {
    const extendedWindow = window;
    extendedWindow.elementorCommon?.ajax?.invalidateCache(data);
  }
};

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/canvas/get-canvas-iframe-document.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/canvas/get-canvas-iframe-document.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCanvasIframeDocument: function() { return /* binding */ getCanvasIframeDocument; }
/* harmony export */ });
function getCanvasIframeDocument() {
  return window.elementor?.$preview?.[0]?.contentDocument;
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/canvas/index.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/canvas/index.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCanvasIframeDocument: function() { return /* reexport safe */ _get_canvas_iframe_document__WEBPACK_IMPORTED_MODULE_0__.getCanvasIframeDocument; }
/* harmony export */ });
/* harmony import */ var _get_canvas_iframe_document__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-canvas-iframe-document */ "./packages/packages/libs/editor-v1-adapters/src/canvas/get-canvas-iframe-document.ts");


/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/config/enqueue-font.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/config/enqueue-font.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   enqueueFont: function() { return /* binding */ enqueueFont; }
/* harmony export */ });
const enqueueFont = (fontFamily, context = 'preview') => {
  return window.elementor?.helpers?.enqueueFont?.(fontFamily, context) ?? null;
};

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/config/get-elementor-globals.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/config/get-elementor-globals.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementorConfig: function() { return /* binding */ getElementorConfig; },
/* harmony export */   getElementorFrontendConfig: function() { return /* binding */ getElementorFrontendConfig; }
/* harmony export */ });
const getElementorConfig = () => {
  return window.elementor?.config ?? {};
};
const getElementorFrontendConfig = () => {
  return window.elementorFrontend?.config ?? {};
};

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/config/index.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/config/index.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   enqueueFont: function() { return /* reexport safe */ _enqueue_font__WEBPACK_IMPORTED_MODULE_1__.enqueueFont; },
/* harmony export */   getElementorConfig: function() { return /* reexport safe */ _get_elementor_globals__WEBPACK_IMPORTED_MODULE_0__.getElementorConfig; },
/* harmony export */   getElementorFrontendConfig: function() { return /* reexport safe */ _get_elementor_globals__WEBPACK_IMPORTED_MODULE_0__.getElementorFrontendConfig; }
/* harmony export */ });
/* harmony import */ var _get_elementor_globals__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-elementor-globals */ "./packages/packages/libs/editor-v1-adapters/src/config/get-elementor-globals.ts");
/* harmony import */ var _enqueue_font__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./enqueue-font */ "./packages/packages/libs/editor-v1-adapters/src/config/enqueue-font.ts");
/* harmony import */ var _supported_fonts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./supported-fonts */ "./packages/packages/libs/editor-v1-adapters/src/config/supported-fonts.ts");




/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/config/supported-fonts.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/config/supported-fonts.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/data-hooks/block-command.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/data-hooks/block-command.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   blockCommand: function() { return /* binding */ blockCommand; }
/* harmony export */ });
/* harmony import */ var _register_data_hook__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./register-data-hook */ "./packages/packages/libs/editor-v1-adapters/src/data-hooks/register-data-hook.ts");

function blockCommand({
  command,
  condition
}) {
  return (0,_register_data_hook__WEBPACK_IMPORTED_MODULE_0__.registerDataHook)('dependency', command, args => {
    const shouldBlock = condition(args);

    // Should return `false` to prevent the command from running.
    return !shouldBlock;
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/data-hooks/register-data-hook.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/data-hooks/register-data-hook.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   registerDataHook: function() { return /* binding */ registerDataHook; }
/* harmony export */ });
let hookId = 0;
function registerDataHook(type, command, callback) {
  const eWindow = window;
  const hooksClasses = eWindow.$e?.modules?.hookData;
  const hooksMap = {
    after: hooksClasses?.After,
    dependency: hooksClasses?.Dependency
  };
  const HookClass = hooksMap[type];
  if (!HookClass) {
    throw new Error(`Data hook '${type}' is not available`);
  }
  const currentHookId = ++hookId;
  const hook = new class extends HookClass {
    getCommand() {
      return command;
    }
    getId() {
      return `${command}--data--${currentHookId}`;
    }
    apply(args, result) {
      const hookOptions = {};
      const currentWindow = window;
      const commandsCurrentTrace = currentWindow.$e?.commands?.currentTrace;
      if (commandsCurrentTrace) {
        hookOptions.commandsCurrentTrace = commandsCurrentTrace;
      }
      const currentHistoryItemId = currentWindow.elementor?.documents?.getCurrent()?.history?.getCurrentId();
      if (currentHistoryItemId) {
        hookOptions.currentHistoryItemId = currentHistoryItemId;
      }
      if (type === 'dependency') {
        return callback(args, hookOptions);
      }
      return callback(args, result, hookOptions);
    }
  }();
  hook.register();
  return hook;
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/dispatchers/dispatchers.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/dispatchers/dispatchers.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   openRoute: function() { return /* binding */ openRoute; },
/* harmony export */   registerRoute: function() { return /* binding */ registerRoute; },
/* harmony export */   runCommand: function() { return /* binding */ runCommand; },
/* harmony export */   runCommandSync: function() { return /* binding */ runCommandSync; }
/* harmony export */ });
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-v1-adapters/src/dispatchers/utils.ts");

async function runCommand(command, args, {
  internal = false
} = {}) {
  const result = runCommandSync(command, args, {
    internal
  });
  if (result instanceof Promise) {
    return result;
  }
  if ((0,_utils__WEBPACK_IMPORTED_MODULE_0__.isJQueryDeferred)(result)) {
    return (0,_utils__WEBPACK_IMPORTED_MODULE_0__.promisifyJQueryDeferred)(result);
  }
  return Promise.resolve(result);
}
function runCommandSync(command, args, {
  internal = false
} = {}) {
  const extendedWindow = window;
  const run = internal ? extendedWindow.$e?.internal : extendedWindow.$e?.run;
  if (!run) {
    const runnerName = internal ? '$e.internal' : '$e.run';
    throw new Error(`\`${runnerName}()\` is not available`);
  }
  return run(command, args);
}
function openRoute(route) {
  const extendedWindow = window;
  if (!extendedWindow.$e?.route) {
    return Promise.reject('`$e.route()` is not available');
  }
  try {
    return Promise.resolve(extendedWindow.$e.route(route));
  } catch (e) {
    return Promise.reject(e);
  }
}
function registerRoute(route) {
  const extendedWindow = window;
  if (!extendedWindow.$e?.routes?.register) {
    return Promise.reject('`$e.routes.register()` is not available');
  }
  const routeParts = route.split('/');
  if (routeParts.length < 2) {
    return Promise.reject(`\`${route}\` is an invalid route`);
  }
  const componentRoute = routeParts.pop(); // routeParts.length must be >= 2
  const component = routeParts.join('/');
  try {
    return Promise.resolve(extendedWindow.$e.routes.register(component, componentRoute, () => null));
  } catch (e) {
    return Promise.reject(e);
  }
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/dispatchers/utils.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/dispatchers/utils.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isJQueryDeferred: function() { return /* binding */ isJQueryDeferred; },
/* harmony export */   promisifyJQueryDeferred: function() { return /* binding */ promisifyJQueryDeferred; }
/* harmony export */ });
function isJQueryDeferred(value) {
  // TODO: Copied from:
  //  https://github.com/elementor/elementor/blob/6a74fc9/modules/web-cli/assets/js/core/commands.js#L410

  return !!value && 'object' === typeof value && Object.hasOwn(value, 'promise') && Object.hasOwn(value, 'then') && Object.hasOwn(value, 'fail');
}
function promisifyJQueryDeferred(deferred) {
  return new Promise((resolve, reject) => {
    deferred.then(resolve, reject);
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/edit-mode.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/edit-mode.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   changeEditMode: function() { return /* binding */ changeEditMode; },
/* harmony export */   getCurrentEditMode: function() { return /* binding */ getCurrentEditMode; },
/* harmony export */   useEditMode: function() { return /* binding */ useEditMode; }
/* harmony export */ });
/* harmony import */ var _hooks_use_listen_to__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./hooks/use-listen-to */ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-listen-to.ts");
/* harmony import */ var _listeners__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./listeners */ "./packages/packages/libs/editor-v1-adapters/src/listeners/index.ts");


function useEditMode() {
  return (0,_hooks_use_listen_to__WEBPACK_IMPORTED_MODULE_0__["default"])((0,_listeners__WEBPACK_IMPORTED_MODULE_1__.windowEvent)('elementor/edit-mode/change'), getCurrentEditMode);
}
function getCurrentEditMode() {
  const extendedWindow = window;
  return extendedWindow.elementor.channels.dataEditMode.request('activeMode');
}
function changeEditMode(newMode) {
  const extendedWindow = window;
  return extendedWindow.elementor.changeEditMode(newMode);
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/hooks/index.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/hooks/index.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useIsRouteActive: function() { return /* reexport safe */ _use_is_route_active__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   useListenTo: function() { return /* reexport safe */ _use_listen_to__WEBPACK_IMPORTED_MODULE_1__["default"]; },
/* harmony export */   useRouteStatus: function() { return /* reexport safe */ _use_route_status__WEBPACK_IMPORTED_MODULE_2__["default"]; }
/* harmony export */ });
/* harmony import */ var _use_is_route_active__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-is-route-active */ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-is-route-active.ts");
/* harmony import */ var _use_listen_to__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-listen-to */ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-listen-to.ts");
/* harmony import */ var _use_route_status__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-route-status */ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-route-status.ts");




/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-is-route-active.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/hooks/use-is-route-active.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useIsRouteActive; }
/* harmony export */ });
/* harmony import */ var _listeners__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../listeners */ "./packages/packages/libs/editor-v1-adapters/src/listeners/index.ts");
/* harmony import */ var _readers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../readers */ "./packages/packages/libs/editor-v1-adapters/src/readers/index.ts");
/* harmony import */ var _use_listen_to__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-listen-to */ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-listen-to.ts");



function useIsRouteActive(route) {
  return (0,_use_listen_to__WEBPACK_IMPORTED_MODULE_2__["default"])([(0,_listeners__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent)(route), (0,_listeners__WEBPACK_IMPORTED_MODULE_0__.routeCloseEvent)(route)], () => (0,_readers__WEBPACK_IMPORTED_MODULE_1__.isRouteActive)(route), [route]);
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-listen-to.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/hooks/use-listen-to.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useListenTo; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _listeners__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../listeners */ "./packages/packages/libs/editor-v1-adapters/src/listeners/index.ts");


function useListenTo(event, getSnapshot, deps = []) {
  const [snapshot, setSnapshot] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => getSnapshot());
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const updateState = () => setSnapshot(getSnapshot());

    // Ensure the state is re-calculated when the dependencies have been changed.
    updateState();
    return (0,_listeners__WEBPACK_IMPORTED_MODULE_1__.listenTo)(event, updateState);
  }, deps); // eslint-disable-line react-hooks/exhaustive-deps

  return snapshot;
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-route-status.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/hooks/use-route-status.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useRouteStatus; }
/* harmony export */ });
/* harmony import */ var _edit_mode__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../edit-mode */ "./packages/packages/libs/editor-v1-adapters/src/edit-mode.ts");
/* harmony import */ var _use_is_route_active__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-is-route-active */ "./packages/packages/libs/editor-v1-adapters/src/hooks/use-is-route-active.ts");


function useRouteStatus(route, {
  blockOnKitRoutes = true,
  allowedEditModes = ['edit']
} = {}) {
  const isRouteActive = (0,_use_is_route_active__WEBPACK_IMPORTED_MODULE_1__["default"])(route);
  const isKitRouteActive = (0,_use_is_route_active__WEBPACK_IMPORTED_MODULE_1__["default"])('panel/global');
  const currentEditMode = (0,_edit_mode__WEBPACK_IMPORTED_MODULE_0__.useEditMode)();
  const isBlockedByEditMode = !allowedEditModes.includes(currentEditMode);
  const isBlockedByKit = blockOnKitRoutes && isKitRouteActive;
  const isActive = isRouteActive && !isBlockedByEditMode;
  const isBlocked = isBlockedByEditMode || isBlockedByKit;
  return {
    isActive,
    isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/listeners/event-creators.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/listeners/event-creators.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   commandEndEvent: function() { return /* binding */ commandEndEvent; },
/* harmony export */   commandStartEvent: function() { return /* binding */ commandStartEvent; },
/* harmony export */   routeCloseEvent: function() { return /* binding */ routeCloseEvent; },
/* harmony export */   routeOpenEvent: function() { return /* binding */ routeOpenEvent; },
/* harmony export */   v1ReadyEvent: function() { return /* binding */ v1ReadyEvent; },
/* harmony export */   windowEvent: function() { return /* binding */ windowEvent; }
/* harmony export */ });
const commandStartEvent = command => {
  return {
    type: 'command',
    name: command,
    state: 'before'
  };
};
const commandEndEvent = command => {
  return {
    type: 'command',
    name: command,
    state: 'after'
  };
};
const routeOpenEvent = route => {
  return {
    type: 'route',
    name: route,
    state: 'open'
  };
};
const routeCloseEvent = route => {
  return {
    type: 'route',
    name: route,
    state: 'close'
  };
};
const windowEvent = event => {
  return {
    type: 'window-event',
    name: event
  };
};
const v1ReadyEvent = () => {
  return windowEvent('elementor/initialized');
};

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/listeners/index.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/listeners/index.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   commandEndEvent: function() { return /* reexport safe */ _event_creators__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent; },
/* harmony export */   commandStartEvent: function() { return /* reexport safe */ _event_creators__WEBPACK_IMPORTED_MODULE_0__.commandStartEvent; },
/* harmony export */   dispatchReadyEvent: function() { return /* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_4__.dispatchReadyEvent; },
/* harmony export */   flushListeners: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_1__.flushListeners; },
/* harmony export */   isReady: function() { return /* reexport safe */ _is_ready__WEBPACK_IMPORTED_MODULE_3__.isReady; },
/* harmony export */   listenTo: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_1__.listenTo; },
/* harmony export */   routeCloseEvent: function() { return /* reexport safe */ _event_creators__WEBPACK_IMPORTED_MODULE_0__.routeCloseEvent; },
/* harmony export */   routeOpenEvent: function() { return /* reexport safe */ _event_creators__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent; },
/* harmony export */   setReady: function() { return /* reexport safe */ _is_ready__WEBPACK_IMPORTED_MODULE_3__.setReady; },
/* harmony export */   v1ReadyEvent: function() { return /* reexport safe */ _event_creators__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent; },
/* harmony export */   windowEvent: function() { return /* reexport safe */ _event_creators__WEBPACK_IMPORTED_MODULE_0__.windowEvent; }
/* harmony export */ });
/* harmony import */ var _event_creators__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./event-creators */ "./packages/packages/libs/editor-v1-adapters/src/listeners/event-creators.ts");
/* harmony import */ var _listeners__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./listeners */ "./packages/packages/libs/editor-v1-adapters/src/listeners/listeners.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./types */ "./packages/packages/libs/editor-v1-adapters/src/listeners/types.ts");
/* harmony import */ var _is_ready__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./is-ready */ "./packages/packages/libs/editor-v1-adapters/src/listeners/is-ready.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-v1-adapters/src/listeners/utils.ts");






/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/listeners/is-ready.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/listeners/is-ready.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isReady: function() { return /* binding */ isReady; },
/* harmony export */   setReady: function() { return /* binding */ setReady; }
/* harmony export */ });
/**
 * This file is used to store the state of the isReady variable, which is used to determine
 * if the adapter is ready to receive events (editor v1 and v2 are loaded).
 */

let ready = false;
function isReady() {
  return ready;
}
function setReady(value) {
  ready = value;
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/listeners/listeners.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/listeners/listeners.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   flushListeners: function() { return /* binding */ flushListeners; },
/* harmony export */   listenTo: function() { return /* binding */ listenTo; }
/* harmony export */ });
/* harmony import */ var _is_ready__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-ready */ "./packages/packages/libs/editor-v1-adapters/src/listeners/is-ready.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-v1-adapters/src/listeners/utils.ts");


const callbacksByEvent = new Map();
let abortController = new AbortController();
function listenTo(eventDescriptors, callback) {
  if (!Array.isArray(eventDescriptors)) {
    eventDescriptors = [eventDescriptors];
  }

  // @see https://github.com/typescript-eslint/typescript-eslint/issues/2841
  // eslint-disable-next-line array-callback-return -- Clashes with typescript.
  const cleanups = eventDescriptors.map(event => {
    const {
      type,
      name
    } = event;
    switch (type) {
      case 'command':
        return registerCommandListener(name, event.state, callback);
      case 'route':
        return registerRouteListener(name, event.state, callback);
      case 'window-event':
        return registerWindowEventListener(name, callback);
    }
  });
  return () => {
    cleanups.forEach(cleanup => cleanup());
  };
}
function flushListeners() {
  abortController.abort();
  callbacksByEvent.clear();
  (0,_is_ready__WEBPACK_IMPORTED_MODULE_0__.setReady)(false);
  abortController = new AbortController();
}
function registerCommandListener(command, state, callback) {
  return registerWindowEventListener(`elementor/commands/run/${state}`, e => {
    const shouldRunCallback = e.type === 'command' && e.command === command;
    if (shouldRunCallback) {
      callback(e);
    }
  });
}
function registerRouteListener(route, state, callback) {
  return registerWindowEventListener(`elementor/routes/${state}`, e => {
    const shouldRunCallback = e.type === 'route' && e.route.startsWith(route);
    if (shouldRunCallback) {
      callback(e);
    }
  });
}
const V1_READY_EVENT_NAME = 'elementor/initialized';
function registerWindowEventListener(event, callback) {
  const isFirstListener = !callbacksByEvent.has(event);
  if (isFirstListener) {
    callbacksByEvent.set(event, []);
    addListener(event);
  }
  callbacksByEvent.get(event)?.push(callback);
  if (event === V1_READY_EVENT_NAME && (0,_is_ready__WEBPACK_IMPORTED_MODULE_0__.isReady)()) {
    Promise.resolve().then(() => {
      const stillRegistered = callbacksByEvent.get(event)?.includes(callback);
      if (stillRegistered) {
        callback({
          type: 'window-event',
          event,
          originalEvent: new CustomEvent(event)
        });
      }
    });
  }
  return () => {
    const callbacks = callbacksByEvent.get(event);
    if (!callbacks?.length) {
      return;
    }
    const filtered = callbacks.filter(cb => cb !== callback);
    callbacksByEvent.set(event, filtered);
  };
}
function addListener(event) {
  window.addEventListener(event, makeEventHandler(event), {
    signal: abortController.signal
  });
}
function makeEventHandler(event) {
  return e => {
    if (!(0,_is_ready__WEBPACK_IMPORTED_MODULE_0__.isReady)()) {
      return;
    }
    const normalizedEvent = (0,_utils__WEBPACK_IMPORTED_MODULE_1__.normalizeEvent)(e);
    callbacksByEvent.get(event)?.forEach(callback => {
      callback(normalizedEvent);
    });
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/listeners/types.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/listeners/types.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/listeners/utils.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/listeners/utils.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dispatchReadyEvent: function() { return /* binding */ dispatchReadyEvent; },
/* harmony export */   normalizeEvent: function() { return /* binding */ normalizeEvent; }
/* harmony export */ });
/* harmony import */ var _is_ready__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-ready */ "./packages/packages/libs/editor-v1-adapters/src/listeners/is-ready.ts");

function dispatchReadyEvent() {
  return getV1LoadingPromise().then(() => {
    (0,_is_ready__WEBPACK_IMPORTED_MODULE_0__.setReady)(true);
    window.dispatchEvent(new CustomEvent('elementor/initialized'));
  });
}
function getV1LoadingPromise() {
  const v1LoadingPromise = window.__elementorEditorV1LoadingPromise;
  if (!v1LoadingPromise) {
    return Promise.reject('Elementor Editor V1 is not loaded');
  }
  return v1LoadingPromise;
}
function normalizeEvent(e) {
  if (e instanceof CustomEvent && e.detail?.command) {
    return {
      type: 'command',
      command: e.detail.command,
      args: e.detail.args,
      originalEvent: e
    };
  }
  if (e instanceof CustomEvent && e.detail?.route) {
    return {
      type: 'route',
      route: e.detail.route,
      originalEvent: e
    };
  }
  return {
    type: 'window-event',
    event: e.type,
    originalEvent: e
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/readers/index.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/readers/index.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EXPERIMENTAL_FEATURES: function() { return /* binding */ EXPERIMENTAL_FEATURES; },
/* harmony export */   isExperimentActive: function() { return /* binding */ isExperimentActive; },
/* harmony export */   isRouteActive: function() { return /* binding */ isRouteActive; }
/* harmony export */ });
const EXPERIMENTAL_FEATURES = {
  // Note: Add new experimental features here as needed
};
function isRouteActive(route) {
  const extendedWindow = window;
  return !!extendedWindow.$e?.routes?.isPartOf(route);
}
const isExperimentActive = experiment => {
  const extendedWindow = window;
  return !!extendedWindow.elementorCommon?.config?.experimentalFeatures?.[experiment];
};

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/undoable/get-history-manager.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/undoable/get-history-manager.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getHistoryManager: function() { return /* binding */ getHistoryManager; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const HistoryManagerNotAvailable = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'history_manager_not_available',
  message: 'Cannot access History manager.'
});
function getHistoryManager() {
  const extendedWindow = window;
  const historyManger = extendedWindow.elementor?.documents?.getCurrent?.()?.history;
  if (!historyManger) {
    throw new HistoryManagerNotAvailable();
  }
  return historyManger;
}

/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/undoable/index.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/undoable/index.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   undoable: function() { return /* reexport safe */ _undoable__WEBPACK_IMPORTED_MODULE_0__.undoable; }
/* harmony export */ });
/* harmony import */ var _undoable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./undoable */ "./packages/packages/libs/editor-v1-adapters/src/undoable/undoable.ts");


/***/ }),

/***/ "./packages/packages/libs/editor-v1-adapters/src/undoable/undoable.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/undoable/undoable.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   undoable: function() { return /* binding */ undoable; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_history_manager__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-history-manager */ "./packages/packages/libs/editor-v1-adapters/src/undoable/get-history-manager.ts");



// eslint-disable-next-line @typescript-eslint/no-explicit-any

// Action WITHOUT a payload.

// Action WITH a payload.

function undoable(actions, options) {
  actions.redo ??= actions.do;
  const _addHistoryItem = options.debounce ? (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.debounce)(addHistoryItem, options.debounce.wait) : addHistoryItem;
  return payload => {
    const _payload = payload;
    const _actions = actions;
    let doReturn = _actions.do(_payload);
    let undoReturn;
    _addHistoryItem({
      title: normalizeToGenerator(options.title)(_payload, doReturn),
      subTitle: normalizeToGenerator(options.subtitle)(_payload, doReturn),
      type: '',
      restore: (_, isRedo) => {
        if (isRedo) {
          doReturn = _actions.redo(_payload, doReturn, undoReturn);
          return;
        }
        undoReturn = _actions.undo(_payload, doReturn);
      }
    });
    return doReturn;
  };
}
function normalizeToGenerator(value) {
  return typeof value === 'function' ? value : () => value ?? '';
}
function addHistoryItem(item) {
  const history = (0,_get_history_manager__WEBPACK_IMPORTED_MODULE_1__.getHistoryManager)();
  history.addItem(item);
}

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
/*!****************************************************************!*\
  !*** ./packages/packages/libs/editor-v1-adapters/src/index.ts ***!
  \****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EXPERIMENTAL_FEATURES: function() { return /* reexport safe */ _readers__WEBPACK_IMPORTED_MODULE_3__.EXPERIMENTAL_FEATURES; },
/* harmony export */   __privateDispatchReadyEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.dispatchReadyEvent; },
/* harmony export */   __privateFlushListeners: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.flushListeners; },
/* harmony export */   __privateIsReady: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.isReady; },
/* harmony export */   __privateIsRouteActive: function() { return /* reexport safe */ _readers__WEBPACK_IMPORTED_MODULE_3__.isRouteActive; },
/* harmony export */   __privateListenTo: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.listenTo; },
/* harmony export */   __privateOpenRoute: function() { return /* reexport safe */ _dispatchers_dispatchers__WEBPACK_IMPORTED_MODULE_0__.openRoute; },
/* harmony export */   __privateRegisterRoute: function() { return /* reexport safe */ _dispatchers_dispatchers__WEBPACK_IMPORTED_MODULE_0__.registerRoute; },
/* harmony export */   __privateRunCommand: function() { return /* reexport safe */ _dispatchers_dispatchers__WEBPACK_IMPORTED_MODULE_0__.runCommand; },
/* harmony export */   __privateRunCommandSync: function() { return /* reexport safe */ _dispatchers_dispatchers__WEBPACK_IMPORTED_MODULE_0__.runCommandSync; },
/* harmony export */   __privateSetReady: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.setReady; },
/* harmony export */   __privateUseIsRouteActive: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useIsRouteActive; },
/* harmony export */   __privateUseListenTo: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useListenTo; },
/* harmony export */   __privateUseRouteStatus: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useRouteStatus; },
/* harmony export */   ajax: function() { return /* reexport safe */ _ajax__WEBPACK_IMPORTED_MODULE_4__.ajax; },
/* harmony export */   blockCommand: function() { return /* reexport safe */ _data_hooks_block_command__WEBPACK_IMPORTED_MODULE_8__.blockCommand; },
/* harmony export */   changeEditMode: function() { return /* reexport safe */ _edit_mode__WEBPACK_IMPORTED_MODULE_6__.changeEditMode; },
/* harmony export */   commandEndEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.commandEndEvent; },
/* harmony export */   commandStartEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.commandStartEvent; },
/* harmony export */   enqueueFont: function() { return /* reexport safe */ _config__WEBPACK_IMPORTED_MODULE_10__.enqueueFont; },
/* harmony export */   getCanvasIframeDocument: function() { return /* reexport safe */ _canvas__WEBPACK_IMPORTED_MODULE_9__.getCanvasIframeDocument; },
/* harmony export */   getCurrentEditMode: function() { return /* reexport safe */ _edit_mode__WEBPACK_IMPORTED_MODULE_6__.getCurrentEditMode; },
/* harmony export */   getElementorConfig: function() { return /* reexport safe */ _config__WEBPACK_IMPORTED_MODULE_10__.getElementorConfig; },
/* harmony export */   getElementorFrontendConfig: function() { return /* reexport safe */ _config__WEBPACK_IMPORTED_MODULE_10__.getElementorFrontendConfig; },
/* harmony export */   isExperimentActive: function() { return /* reexport safe */ _readers__WEBPACK_IMPORTED_MODULE_3__.isExperimentActive; },
/* harmony export */   registerDataHook: function() { return /* reexport safe */ _data_hooks_register_data_hook__WEBPACK_IMPORTED_MODULE_7__.registerDataHook; },
/* harmony export */   routeCloseEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.routeCloseEvent; },
/* harmony export */   routeOpenEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.routeOpenEvent; },
/* harmony export */   undoable: function() { return /* reexport safe */ _undoable__WEBPACK_IMPORTED_MODULE_5__.undoable; },
/* harmony export */   useEditMode: function() { return /* reexport safe */ _edit_mode__WEBPACK_IMPORTED_MODULE_6__.useEditMode; },
/* harmony export */   v1ReadyEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.v1ReadyEvent; },
/* harmony export */   windowEvent: function() { return /* reexport safe */ _listeners__WEBPACK_IMPORTED_MODULE_2__.windowEvent; }
/* harmony export */ });
/* harmony import */ var _dispatchers_dispatchers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dispatchers/dispatchers */ "./packages/packages/libs/editor-v1-adapters/src/dispatchers/dispatchers.ts");
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks */ "./packages/packages/libs/editor-v1-adapters/src/hooks/index.ts");
/* harmony import */ var _listeners__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./listeners */ "./packages/packages/libs/editor-v1-adapters/src/listeners/index.ts");
/* harmony import */ var _readers__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./readers */ "./packages/packages/libs/editor-v1-adapters/src/readers/index.ts");
/* harmony import */ var _ajax__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ajax */ "./packages/packages/libs/editor-v1-adapters/src/ajax/index.ts");
/* harmony import */ var _undoable__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./undoable */ "./packages/packages/libs/editor-v1-adapters/src/undoable/index.ts");
/* harmony import */ var _edit_mode__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./edit-mode */ "./packages/packages/libs/editor-v1-adapters/src/edit-mode.ts");
/* harmony import */ var _data_hooks_register_data_hook__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./data-hooks/register-data-hook */ "./packages/packages/libs/editor-v1-adapters/src/data-hooks/register-data-hook.ts");
/* harmony import */ var _data_hooks_block_command__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./data-hooks/block-command */ "./packages/packages/libs/editor-v1-adapters/src/data-hooks/block-command.ts");
/* harmony import */ var _canvas__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./canvas */ "./packages/packages/libs/editor-v1-adapters/src/canvas/index.ts");
/* harmony import */ var _config__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./config */ "./packages/packages/libs/editor-v1-adapters/src/config/index.ts");











}();
(window.elementorV2 = window.elementorV2 || {}).editorV1Adapters = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorV1Adapters?.init?.();
//# sourceMappingURL=editor-v1-adapters.js.map
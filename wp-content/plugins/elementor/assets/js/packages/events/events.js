/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/events/src/use-mixpanel.ts":
/*!***********************************************************!*\
  !*** ./packages/packages/libs/events/src/use-mixpanel.ts ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canSendEvents: function() { return /* binding */ canSendEvents; },
/* harmony export */   enableTracking: function() { return /* binding */ enableTracking; },
/* harmony export */   getMixpanel: function() { return /* binding */ getMixpanel; },
/* harmony export */   initializeAndEnableTracking: function() { return /* binding */ initializeAndEnableTracking; },
/* harmony export */   initializeMixpanel: function() { return /* binding */ initializeMixpanel; },
/* harmony export */   isMixpanelReady: function() { return /* binding */ isMixpanelReady; },
/* harmony export */   isTrackingEnabled: function() { return /* binding */ isTrackingEnabled; },
/* harmony export */   setCanSendEvents: function() { return /* binding */ setCanSendEvents; },
/* harmony export */   trackEvent: function() { return /* binding */ trackEvent; },
/* harmony export */   useMixpanel: function() { return /* binding */ useMixpanel; }
/* harmony export */ });
const getMixpanel = () => {
  const eventsManager = window.elementorCommon?.eventsManager || {};
  return {
    dispatchEvent: eventsManager.dispatchEvent?.bind(eventsManager),
    config: eventsManager.config,
    canSendEvents: eventsManager.canSendEvents?.bind(eventsManager),
    initializeMixpanel: eventsManager.initializeMixpanel?.bind(eventsManager),
    enableTracking: eventsManager.enableTracking?.bind(eventsManager),
    isMixpanelReady: eventsManager.isMixpanelReady?.bind(eventsManager),
    trackingEnabled: eventsManager.trackingEnabled ?? false,
    getMixpanelInstance: eventsManager.getMixpanelInstance?.bind(eventsManager)
  };
};
const useMixpanel = () => {
  const {
    dispatchEvent,
    config
  } = getMixpanel();
  return {
    dispatchEvent,
    config
  };
};
const trackEvent = event => {
  const {
    dispatchEvent
  } = getMixpanel();
  dispatchEvent?.(event.eventName, event);
};
const canSendEvents = () => getMixpanel().canSendEvents?.() ?? false;
const setCanSendEvents = value => {
  const editorEvents = window.elementorCommon?.config?.editor_events;
  if (editorEvents) {
    editorEvents.can_send_events = value;
  }
};
const isMixpanelReady = () => getMixpanel().isMixpanelReady?.() ?? false;
const isTrackingEnabled = () => getMixpanel().trackingEnabled;
const enableTracking = () => getMixpanel().enableTracking?.();
const initializeMixpanel = onLoaded => getMixpanel().initializeMixpanel?.(onLoaded ?? (() => {}));
function initializeAndEnableTracking(onReady) {
  const mixpanel = getMixpanel();
  if (!mixpanel.dispatchEvent) {
    return;
  }
  if (mixpanel.trackingEnabled) {
    onReady?.(mixpanel.getMixpanelInstance?.());
    return;
  }
  if (mixpanel.isMixpanelReady?.()) {
    mixpanel.enableTracking?.();
    onReady?.(mixpanel.getMixpanelInstance?.());
    return;
  }
  mixpanel.initializeMixpanel?.(mpInstance => {
    mixpanel.enableTracking?.();
    onReady?.(mpInstance);
  });
}

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
/*!****************************************************!*\
  !*** ./packages/packages/libs/events/src/index.ts ***!
  \****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canSendEvents: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.canSendEvents; },
/* harmony export */   enableTracking: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.enableTracking; },
/* harmony export */   getMixpanel: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.getMixpanel; },
/* harmony export */   initializeAndEnableTracking: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.initializeAndEnableTracking; },
/* harmony export */   initializeMixpanel: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.initializeMixpanel; },
/* harmony export */   isMixpanelReady: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.isMixpanelReady; },
/* harmony export */   isTrackingEnabled: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.isTrackingEnabled; },
/* harmony export */   setCanSendEvents: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.setCanSendEvents; },
/* harmony export */   trackEvent: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.trackEvent; },
/* harmony export */   useMixpanel: function() { return /* reexport safe */ _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__.useMixpanel; }
/* harmony export */ });
/* harmony import */ var _use_mixpanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-mixpanel */ "./packages/packages/libs/events/src/use-mixpanel.ts");

}();
(window.elementorV2 = window.elementorV2 || {}).events = __webpack_exports__;
/******/ })()
;
window.elementorV2.events?.init?.();
//# sourceMappingURL=events.js.map
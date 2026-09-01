/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/frontend-handlers/src/handlers-registry.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/frontend-handlers/src/handlers-registry.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   elementSelectorHandlers: function() { return /* binding */ elementSelectorHandlers; },
/* harmony export */   elementTypeHandlers: function() { return /* binding */ elementTypeHandlers; },
/* harmony export */   register: function() { return /* binding */ register; },
/* harmony export */   registerBySelector: function() { return /* binding */ registerBySelector; },
/* harmony export */   unregister: function() { return /* binding */ unregister; },
/* harmony export */   unregisterBySelector: function() { return /* binding */ unregisterBySelector; }
/* harmony export */ });
const elementTypeHandlers = new Map();
const elementSelectorHandlers = new Map();
const register = ({
  elementType,
  id,
  callback
}) => {
  if (!elementTypeHandlers.has(elementType)) {
    elementTypeHandlers.set(elementType, new Map());
  }
  if (!elementTypeHandlers.get(elementType)?.has(id)) {
    elementTypeHandlers.get(elementType)?.set(id, callback);
  }
};
Object.defineProperty(window, 'registerElementorElement', {
  value: register,
  enumerable: true
});
const unregister = ({
  elementType,
  id
}) => {
  if (!elementTypeHandlers.has(elementType)) {
    return;
  }
  if (id) {
    elementTypeHandlers.get(elementType)?.delete(id);
    if (elementTypeHandlers.get(elementType)?.size === 0) {
      elementTypeHandlers.delete(elementType);
    }
  } else {
    elementTypeHandlers.delete(elementType);
  }
};
const registerBySelector = ({
  id,
  selector,
  callback
}) => {
  if (!elementSelectorHandlers.has(selector)) {
    elementSelectorHandlers.set(selector, new Map());
  }
  if (!elementSelectorHandlers.get(selector)?.has(id)) {
    elementSelectorHandlers.get(selector)?.set(id, callback);
  }
};
const unregisterBySelector = ({
  selector,
  id
}) => {
  if (!elementSelectorHandlers.has(selector)) {
    return;
  }
  if (id) {
    elementSelectorHandlers.get(selector)?.delete(id);
    if (elementSelectorHandlers.get(selector)?.size === 0) {
      elementSelectorHandlers.delete(selector);
    }
  } else {
    elementSelectorHandlers.delete(selector);
  }
};

/***/ }),

/***/ "./packages/packages/core/frontend-handlers/src/init.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/frontend-handlers/src/init.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _lifecycle_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./lifecycle-events */ "./packages/packages/core/frontend-handlers/src/lifecycle-events.ts");

const ATOMIC_SELECTOR = '[data-e-type]';
let domMutationObserverStarted = false;
const pendingMutationNodes = new Set();
let pendingMutationsRafId = 0;
function init() {
  window.addEventListener('elementor/element/render', _event => {
    const event = _event;
    const {
      id,
      type,
      element
    } = event.detail;
    (0,_lifecycle_events__WEBPACK_IMPORTED_MODULE_0__.onElementRender)({
      element,
      elementType: type,
      elementId: id
    });
  });
  window.addEventListener('elementor/element/destroy', _event => {
    const event = _event;
    const {
      id,
      type,
      element
    } = event.detail;
    (0,_lifecycle_events__WEBPACK_IMPORTED_MODULE_0__.onElementDestroy)({
      elementType: type,
      elementId: id,
      element
    });
  });
  const bootDomHandlers = () => {
    scanDocumentForAtomicElements();
    startObservingDomForNewAtomicElements();
  };
  document.addEventListener('DOMContentLoaded', bootDomHandlers);
  if ('loading' !== document.readyState) {
    bootDomHandlers();
  }
}
function triggerAtomicRender(atom) {
  const eType = atom.dataset.eType;
  const id = atom.dataset.id;
  if (!eType || !id) {
    return;
  }
  (0,_lifecycle_events__WEBPACK_IMPORTED_MODULE_0__.onElementRender)({
    element: atom,
    elementType: eType,
    elementId: id
  });
}
function collectAtomicElementsInSubtree(root) {
  const found = [];
  if (root.matches(ATOMIC_SELECTOR)) {
    found.push(root);
  }
  root.querySelectorAll(ATOMIC_SELECTOR).forEach(el => {
    found.push(el);
  });
  return found;
}
function scanDocumentForAtomicElements() {
  document.querySelectorAll(ATOMIC_SELECTOR).forEach(el => {
    const atom = el;
    const {
      eType,
      id
    } = atom.dataset;
    if (!eType || !id) {
      return;
    }
    triggerAtomicRender(atom);
  });
}
function startObservingDomForNewAtomicElements() {
  if (domMutationObserverStarted || 'undefined' === typeof MutationObserver) {
    return;
  }
  domMutationObserverStarted = true;
  const observer = new MutationObserver(mutations => {
    for (const mutation of mutations) {
      mutation.addedNodes.forEach(node => {
        pendingMutationNodes.add(node);
      });
    }
    queueProcessPendingMutationNodes();
  });
  observer.observe(document.documentElement, {
    childList: true,
    subtree: true
  });
}
function queueProcessPendingMutationNodes() {
  if (pendingMutationsRafId || !pendingMutationNodes.size) {
    return;
  }
  pendingMutationsRafId = requestAnimationFrame(() => {
    pendingMutationsRafId = 0;
    const roots = Array.from(pendingMutationNodes);
    pendingMutationNodes.clear();
    const atoms = new Set();
    for (const node of roots) {
      if (Node.ELEMENT_NODE !== node.nodeType) {
        continue;
      }
      collectAtomicElementsInSubtree(node).forEach(atom => {
        atoms.add(atom);
      });
    }
    atoms.forEach(atom => triggerAtomicRender(atom));
  });
}

/***/ }),

/***/ "./packages/packages/core/frontend-handlers/src/lifecycle-events.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/frontend-handlers/src/lifecycle-events.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   onElementDestroy: function() { return /* binding */ onElementDestroy; },
/* harmony export */   onElementRender: function() { return /* binding */ onElementRender; },
/* harmony export */   onElementSelectorRender: function() { return /* binding */ onElementSelectorRender; }
/* harmony export */ });
/* harmony import */ var _handlers_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./handlers-registry */ "./packages/packages/core/frontend-handlers/src/handlers-registry.ts");

const unmountCallbacks = new WeakMap();
const ELEMENT_RENDERED_EVENT_NAME = 'elementor/element/rendered';
const ELEMENT_DESTROYED_EVENT_NAME = 'elementor/element/destroyed';
const dispatchDestroyedEvent = params => {
  params.element.dispatchEvent(new CustomEvent(ELEMENT_DESTROYED_EVENT_NAME, {
    bubbles: true,
    detail: params
  }));
};
const onElementRender = ({
  element,
  elementType,
  elementId
}) => {
  cleanupOnUnmount(element);
  const controller = new AbortController();
  const manualUnmount = [];
  const dispatchRenderedEvent = () => {
    onElementSelectorRender({
      element,
      controller
    });
    element.dispatchEvent(new CustomEvent(ELEMENT_RENDERED_EVENT_NAME, {
      bubbles: true,
      detail: {
        element,
        elementType,
        elementId
      }
    }));
  };

  // When the rendered event is dispatched, the element is not yet connected to the DOM (marionette view case)
  if (!element.isConnected) {
    requestAnimationFrame(() => {
      dispatchRenderedEvent();
    });
  } else {
    dispatchRenderedEvent();
  }
  if (!_handlers_registry__WEBPACK_IMPORTED_MODULE_0__.elementTypeHandlers.has(elementType)) {
    return;
  }
  setUnmountEntry({
    element,
    controller,
    manualUnmount
  });
  Array.from(_handlers_registry__WEBPACK_IMPORTED_MODULE_0__.elementTypeHandlers.get(elementType)?.values() ?? []).forEach(handler => {
    const settings = element.getAttribute('data-e-settings');
    const listenToChildren = elementTypes => ({
      render: callback => {
        const listener = event => {
          const {
            elementType: childType
          } = event.detail;
          if (!elementTypes.includes(childType)) {
            return;
          }
          callback(event);
        };
        element.addEventListener(ELEMENT_RENDERED_EVENT_NAME, listener, {
          signal: controller.signal
        });
        element.addEventListener(ELEMENT_DESTROYED_EVENT_NAME, listener, {
          signal: controller.signal
        });
      }
    });
    const unmount = handler({
      element,
      signal: controller.signal,
      settings: settings ? JSON.parse(settings) : {},
      listenToChildren
    });
    if (typeof unmount === 'function') {
      manualUnmount.push(unmount);
    }
  });
};
const onElementSelectorRender = ({
  element,
  controller
}) => {
  let requiresCleanup = false;
  const manualUnmount = [];
  Array.from(_handlers_registry__WEBPACK_IMPORTED_MODULE_0__.elementSelectorHandlers.entries() ?? []).forEach(([selector, handlers]) => {
    if (!element.matches(selector)) {
      return;
    }
    requiresCleanup = true;
    Array.from(handlers.values() ?? []).forEach(handler => {
      const settings = element.getAttribute('data-e-settings');
      const unmount = handler({
        element,
        signal: controller.signal,
        settings: settings ? JSON.parse(settings) : {}
      });
      if (typeof unmount === 'function') {
        manualUnmount.push(unmount);
      }
    });
  });
  if (requiresCleanup) {
    setUnmountEntry({
      element,
      controller,
      manualUnmount
    });
  }
};
const onElementDestroy = ({
  elementType,
  elementId,
  element
}) => {
  if (!element) {
    return;
  }
  cleanupOnUnmount(element);
  dispatchDestroyedEvent({
    element,
    elementType,
    elementId
  });
};
const setUnmountEntry = ({
  element,
  controller,
  manualUnmount
}) => {
  const existingEntry = unmountCallbacks.get(element);
  if (existingEntry) {
    existingEntry.manualUnmount.push(...manualUnmount);
  } else {
    unmountCallbacks.set(element, {
      controller,
      manualUnmount
    });
  }
};
const cleanupOnUnmount = element => {
  const entry = unmountCallbacks.get(element);
  if (entry) {
    entry.controller.abort();
    entry.manualUnmount.forEach(callback => callback());
    unmountCallbacks.delete(element);
  }
};

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
/*!***************************************************************!*\
  !*** ./packages/packages/core/frontend-handlers/src/index.ts ***!
  \***************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_1__.init; },
/* harmony export */   register: function() { return /* reexport safe */ _handlers_registry__WEBPACK_IMPORTED_MODULE_0__.register; },
/* harmony export */   registerBySelector: function() { return /* reexport safe */ _handlers_registry__WEBPACK_IMPORTED_MODULE_0__.registerBySelector; },
/* harmony export */   unregister: function() { return /* reexport safe */ _handlers_registry__WEBPACK_IMPORTED_MODULE_0__.unregister; },
/* harmony export */   unregisterBySelector: function() { return /* reexport safe */ _handlers_registry__WEBPACK_IMPORTED_MODULE_0__.unregisterBySelector; }
/* harmony export */ });
/* harmony import */ var _handlers_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./handlers-registry */ "./packages/packages/core/frontend-handlers/src/handlers-registry.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./init */ "./packages/packages/core/frontend-handlers/src/init.ts");


}();
(window.elementorV2 = window.elementorV2 || {}).frontendHandlers = __webpack_exports__;
/******/ })()
;
window.elementorV2.frontendHandlers?.init?.();
//# sourceMappingURL=frontend-handlers.js.map
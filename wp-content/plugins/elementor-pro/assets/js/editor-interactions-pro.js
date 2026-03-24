/*! elementor-pro - v3.35.0 - 11-02-2026 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "../modules/interactions/assets/js/interactions-utils.js":
/*!***************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-utils.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.config = void 0;
exports.extractAnimationId = extractAnimationId;
exports.extractInteractionId = extractInteractionId;
exports.getAnimateFunction = getAnimateFunction;
exports.getInViewFunction = getInViewFunction;
exports.getKeyframes = getKeyframes;
exports.getScrollFunction = getScrollFunction;
exports.parseAnimationName = parseAnimationName;
exports.parseInteractionsData = parseInteractionsData;
exports.waitForAnimateFunction = waitForAnimateFunction;
const config = exports.config = window.ElementorInteractionsConfig?.constants || {
  defaultDuration: 300,
  defaultDelay: 0,
  slideDistance: 100,
  scaleStart: 0,
  ease: 'easeIn',
  relativeTo: 'viewport',
  offsetTop: 0,
  offsetBottom: 1
};
function getKeyframes(effect, type, direction) {
  const isIn = 'in' === type;
  const keyframes = {};
  if ('fade' === effect) {
    keyframes.opacity = isIn ? [0, 1] : [1, 0];
  }
  if ('scale' === effect) {
    keyframes.scale = isIn ? [config.scaleStart, 1] : [1, config.scaleStart];
  }
  if (direction) {
    const distance = config.slideDistance;
    const movement = {
      left: {
        x: isIn ? [-distance, 0] : [0, -distance]
      },
      right: {
        x: isIn ? [distance, 0] : [0, distance]
      },
      top: {
        y: isIn ? [-distance, 0] : [0, -distance]
      },
      bottom: {
        y: isIn ? [distance, 0] : [0, distance]
      }
    };
    Object.assign(keyframes, movement[direction]);
  }
  return keyframes;
}
function parseAnimationName(name) {
  const [trigger, effect, type, direction, duration, delay, replay] = name.split('-');
  return {
    trigger,
    effect,
    type,
    direction: direction || null,
    duration: duration ? parseInt(duration, 10) : config.defaultDuration,
    delay: delay ? parseInt(delay, 10) : config.defaultDelay,
    replay
  };
}
function extractAnimationId(interaction) {
  if ('string' === typeof interaction) {
    return interaction;
  }
  if ('interaction-item' === interaction?.$$type && interaction?.value) {
    const {
      trigger,
      animation
    } = interaction.value;
    if ('animation-preset-props' === animation?.$$type && animation?.value) {
      const {
        effect,
        type,
        direction,
        timing_config: timingConfig
      } = animation.value;
      const triggerVal = trigger?.value || 'load';
      const effectVal = effect?.value || 'fade';
      const typeVal = type?.value || 'in';
      const directionVal = direction?.value || '';
      const duration = timingConfig?.value?.duration?.value ?? 300;
      const delay = timingConfig?.value?.delay?.value ?? 0;
      const replay = config?.replay ?? false;
      return `${triggerVal}-${effectVal}-${typeVal}-${directionVal}-${duration}-${delay}-${replay}`;
    }
  }
  if (interaction?.animation?.animation_id) {
    return interaction.animation.animation_id;
  }
  return null;
}
function extractInteractionId(interaction) {
  if ('interaction-item' === interaction?.$$type && interaction?.value) {
    return interaction.value.interaction_id?.value || null;
  }
  return null;
}
function getAnimateFunction() {
  return 'undefined' !== typeof animate ? animate : window.Motion?.animate;
}
function getInViewFunction() {
  return 'undefined' !== typeof inView ? inView : window.Motion?.inView;
}
function getScrollFunction() {
  if (window.Motion?.scroll && 'function' === typeof window.Motion.scroll) {
    return window.Motion.scroll;
  }
  return null;
}
function waitForAnimateFunction(callback, maxAttempts = 10) {
  if (getAnimateFunction()) {
    callback();
    return;
  }
  if (maxAttempts > 0) {
    setTimeout(() => waitForAnimateFunction(callback, maxAttempts - 1), 100);
  }
}
function parseInteractionsData(data) {
  if ('string' === typeof data) {
    try {
      return JSON.parse(data);
    } catch {
      return null;
    }
  }
  return data;
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
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!********************************************************************!*\
  !*** ../modules/interactions/assets/js/editor-interactions-pro.js ***!
  \********************************************************************/


var _interactionsUtils = __webpack_require__(/*! ./interactions-utils.js */ "../modules/interactions/assets/js/interactions-utils.js");
function applyAnimation(element, animConfig, animateFunc) {
  const keyframes = (0, _interactionsUtils.getKeyframes)(animConfig.effect, animConfig.type, animConfig.direction);
  const options = {
    duration: animConfig.duration / 1000,
    delay: animConfig.delay / 1000,
    ease: _interactionsUtils.config.ease
  };
  const initialKeyframes = {};
  Object.keys(keyframes).forEach(key => {
    initialKeyframes[key] = keyframes[key][0];
  });
  // WHY - Transition can be set on elements but once it sets it destroys all animations, so we basically put it aside.
  const transition = element.style.transition;
  element.style.transition = 'none';
  animateFunc(element, initialKeyframes, {
    duration: 0
  }).then(() => {
    animateFunc(element, keyframes, options).then(() => {
      if ('out' === animConfig.type) {
        const resetValues = {
          opacity: 1,
          scale: 1,
          x: 0,
          y: 0
        };
        const resetKeyframes = {};
        Object.keys(keyframes).forEach(key => {
          resetKeyframes[key] = resetValues[key];
        });
        element.style.transition = transition;
        animateFunc(element, resetKeyframes, {
          duration: 0
        });
      }
    });
  });
}
function getInteractionsData() {
  const scriptTag = document.querySelector('script[data-e-interactions="true"]');
  if (!scriptTag) {
    return [];
  }
  try {
    return JSON.parse(scriptTag.textContent || '[]');
  } catch {
    return [];
  }
}
function findElementByInteractionId(interactionId) {
  return document.querySelector('[data-interaction-id="' + interactionId + '"]');
}
function applyInteractionsToElement(element, interactionsData) {
  const animateFunc = (0, _interactionsUtils.getAnimateFunction)();
  if (!animateFunc) {
    return;
  }
  const parsedData = (0, _interactionsUtils.parseInteractionsData)(interactionsData);
  if (!parsedData) {
    return;
  }
  const interactions = Object.values(parsedData?.items || []);
  interactions.forEach(interaction => {
    const animationName = (0, _interactionsUtils.extractAnimationId)(interaction);
    const animConfig = animationName && (0, _interactionsUtils.parseAnimationName)(animationName);
    if (animConfig) {
      applyAnimation(element, animConfig, animateFunc);
    }
  });
}
let previousInteractionsData = [];
function handleInteractionsUpdate() {
  const currentInteractionsData = getInteractionsData();
  const changedItems = currentInteractionsData.filter(currentItem => {
    const previousItem = previousInteractionsData.find(prev => prev.dataId === currentItem.dataId);
    if (!previousItem) {
      return true;
    }
    const currentIds = (currentItem.interactions?.items || []).map(_interactionsUtils.extractInteractionId).filter(Boolean).sort().join(',');
    const prevIds = (previousItem.interactions?.items || []).map(_interactionsUtils.extractInteractionId).filter(Boolean).sort().join(',');
    return currentIds !== prevIds;
  });
  changedItems.forEach(item => {
    const element = findElementByInteractionId(item.dataId);
    const prevInteractions = previousInteractionsData.find(prev => prev.dataId === item.dataId)?.interactions;
    if (!element || !item.interactions?.items?.length) {
      return;
    }
    const prevIds = new Set((prevInteractions?.items || []).map(_interactionsUtils.extractInteractionId).filter(Boolean));
    const changedInteractions = item.interactions.items.filter(interaction => {
      const id = (0, _interactionsUtils.extractInteractionId)(interaction);
      return !id || !prevIds.has(id);
    });
    if (changedInteractions.length > 0) {
      applyInteractionsToElement(element, {
        ...item.interactions,
        items: changedInteractions
      });
    }
  });
  previousInteractionsData = currentInteractionsData;
}
function initEditorInteractionsHandler() {
  (0, _interactionsUtils.waitForAnimateFunction)(() => {
    const head = document.head;
    let scriptTag = null;
    let observer = null;
    function setupObserver(tag) {
      if (observer) {
        observer.disconnect();
      }
      observer = new MutationObserver(() => {
        handleInteractionsUpdate();
      });
      observer.observe(tag, {
        childList: true,
        characterData: true,
        subtree: true
      });
      handleInteractionsUpdate();
      registerWindowEvents();
    }
    const headObserver = new MutationObserver(() => {
      const foundScriptTag = document.querySelector('script[data-e-interactions="true"]');
      if (foundScriptTag && foundScriptTag !== scriptTag) {
        scriptTag = foundScriptTag;
        setupObserver(scriptTag);
        headObserver.disconnect();
      }
    });
    headObserver.observe(head, {
      childList: true,
      subtree: true
    });
    scriptTag = document.querySelector('script[data-e-interactions="true"]');
    if (scriptTag) {
      setupObserver(scriptTag);
      headObserver.disconnect();
    }
  });
}
function registerWindowEvents() {
  window.top.addEventListener('atomic/play_interactions', handlePlayInteractions);
}
function handlePlayInteractions(event) {
  const {
    elementId,
    interactionId
  } = event.detail;
  const interactionsData = getInteractionsData();
  const item = interactionsData.find(elementItemData => elementItemData.dataId === elementId);
  if (!item) {
    return;
  }
  const element = findElementByInteractionId(elementId);
  if (!element) {
    return;
  }
  const interactionsCopy = {
    ...item.interactions,
    items: item.interactions.items.filter(interactionItem => {
      const itemId = (0, _interactionsUtils.extractInteractionId)(interactionItem);
      return itemId === interactionId;
    })
  };
  applyInteractionsToElement(element, interactionsCopy);
}
if ('loading' === document.readyState) {
  document.addEventListener('DOMContentLoaded', initEditorInteractionsHandler);
} else {
  initEditorInteractionsHandler();
}
})();

/******/ })()
;
//# sourceMappingURL=editor-interactions-pro.js.map
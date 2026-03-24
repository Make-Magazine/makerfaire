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
/*!*************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-pro.js ***!
  \*************************************************************/


var _interactionsUtils = __webpack_require__(/*! ./interactions-utils.js */ "../modules/interactions/assets/js/interactions-utils.js");
function shouldStopAnimation(replay) {
  return [false, 0, '0', 'false', undefined].includes(replay);
}
function scrollOutAnimation(element, transition, animConfig, keyframes, options, animateFunc, inViewFunc) {
  const viewOptions = {
    amount: 0.85,
    root: null
  };
  const resetKeyframes = (0, _interactionsUtils.getKeyframes)(animConfig.effect, 'in', animConfig.direction);
  animateFunc(element, resetKeyframes, {
    duration: 0
  });
  const stop = inViewFunc(element, () => {
    return () => {
      animateFunc(element, keyframes, options).then(() => {
        element.style.transition = transition;
      });
      if (false === animConfig.replay) {
        stop();
      }
    };
  }, viewOptions);
}
function scrollInAnimation(element, transition, animConfig, keyframes, options, animateFunc, inViewFunc) {
  const viewOptions = {
    amount: 0,
    root: null
  };
  const initialKeyframes = {};
  Object.keys(keyframes).forEach(key => {
    initialKeyframes[key] = keyframes[key][0];
  });
  const shouldStop = shouldStopAnimation(animConfig.replay);
  const stop = inViewFunc(element, () => {
    animateFunc(element, keyframes, options).then(() => {
      element.style.transition = transition;
    });
    if (shouldStop) {
      stop();
    }
    return () => {
      if (!shouldStop) {
        animateFunc(element, initialKeyframes, {
          duration: 0
        });
      }
    };
  }, viewOptions);
}
function scrollOnAnimation(element, transition, animConfig, keyframes, options, animateFunc, scrollFunc) {
  const offsetTop = animConfig.offsetTop !== undefined ? animConfig.offsetTop : 0;
  const offsetBottom = animConfig.offsetBottom !== undefined ? animConfig.offsetBottom : 1;
  const offsetType = animConfig.offsetType || 'viewport';
  const scrollOptions = {};
  const initialKeyframes = {};
  Object.keys(keyframes).forEach(key => {
    initialKeyframes[key] = keyframes[key][0];
  });
  animateFunc(element, initialKeyframes, {
    duration: 0
  });
  const animation = animateFunc(element, keyframes, {
    ...options,
    autoplay: false
  });
  if ('viewport' === offsetType) {
    scrollOptions.target = element;
    scrollOptions.offset = [`${offsetTop} end`, `${offsetBottom} start`];
  } else {
    scrollOptions.offset = [offsetTop, offsetBottom];
  }
  const cancelScroll = scrollFunc(animation, scrollOptions);
  return cancelScroll;
}
function defaultAnimation(element, transition, keyframes, options, animateFunc) {
  animateFunc(element, keyframes, options).then(() => {
    element.style.transition = transition;
  });
}
function applyAnimation(element, animConfig, animateFunc, inViewFunc, scrollFunc) {
  const keyframes = (0, _interactionsUtils.getKeyframes)(animConfig.effect, animConfig.type, animConfig.direction);
  const options = {
    duration: animConfig.duration / 1000,
    delay: animConfig.delay / 1000,
    ease: _interactionsUtils.config.ease
  };

  // WHY - Transition can be set on elements but once it sets it destroys all animations, so we basically put it aside.
  const transition = element.style.transition;
  element.style.transition = 'none';
  if ('scrollOut' === animConfig.trigger) {
    scrollOutAnimation(element, transition, animConfig, keyframes, options, animateFunc, inViewFunc);
  } else if ('scrollIn' === animConfig.trigger) {
    scrollInAnimation(element, transition, animConfig, keyframes, options, animateFunc, inViewFunc);
  } else if ('scrollOn' === animConfig.trigger) {
    scrollOnAnimation(element, transition, animConfig, keyframes, options, animateFunc, scrollFunc);
  } else {
    defaultAnimation(element, transition, keyframes, options, animateFunc);
  }
}
function initInteractions() {
  (0, _interactionsUtils.waitForAnimateFunction)(() => {
    const animateFunc = (0, _interactionsUtils.getAnimateFunction)();
    const inViewFunc = (0, _interactionsUtils.getInViewFunction)();
    const scrollFunc = (0, _interactionsUtils.getScrollFunction)();
    if (!inViewFunc || !animateFunc || !scrollFunc) {
      return;
    }
    const elements = document.querySelectorAll('[data-interactions]');
    elements.forEach(element => {
      const interactionsData = element.getAttribute('data-interactions');
      const parsedData = (0, _interactionsUtils.parseInteractionsData)(interactionsData);
      if (!parsedData || !Array.isArray(parsedData)) {
        return;
      }
      parsedData.forEach(interaction => {
        const animationName = (0, _interactionsUtils.extractAnimationId)(interaction);
        const animConfig = animationName && (0, _interactionsUtils.parseAnimationName)(animationName);
        if (animConfig) {
          applyAnimation(element, animConfig, animateFunc, inViewFunc, scrollFunc);
        }
      });
    });
  });
}
if ('loading' === document.readyState) {
  document.addEventListener('DOMContentLoaded', initInteractions);
} else {
  initInteractions();
}
})();

/******/ })()
;
//# sourceMappingURL=interactions-pro.js.map
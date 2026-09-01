/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/node_modules/@tanstack/react-virtual/dist/esm/index.js":
/*!*************************************************************************!*\
  !*** ./packages/node_modules/@tanstack/react-virtual/dist/esm/index.js ***!
  \*************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Virtualizer: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.Virtualizer; },
/* harmony export */   approxEqual: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.approxEqual; },
/* harmony export */   debounce: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.debounce; },
/* harmony export */   defaultKeyExtractor: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.defaultKeyExtractor; },
/* harmony export */   defaultRangeExtractor: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.defaultRangeExtractor; },
/* harmony export */   elementScroll: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.elementScroll; },
/* harmony export */   measureElement: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.measureElement; },
/* harmony export */   memo: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.memo; },
/* harmony export */   notUndefined: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.notUndefined; },
/* harmony export */   observeElementOffset: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeElementOffset; },
/* harmony export */   observeElementRect: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeElementRect; },
/* harmony export */   observeWindowOffset: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeWindowOffset; },
/* harmony export */   observeWindowRect: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeWindowRect; },
/* harmony export */   useVirtualizer: function() { return /* binding */ useVirtualizer; },
/* harmony export */   useWindowVirtualizer: function() { return /* binding */ useWindowVirtualizer; },
/* harmony export */   windowScroll: function() { return /* reexport safe */ _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.windowScroll; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @tanstack/virtual-core */ "./packages/node_modules/@tanstack/virtual-core/dist/esm/index.js");




const useIsomorphicLayoutEffect = typeof document !== "undefined" ? react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect : react__WEBPACK_IMPORTED_MODULE_0__.useEffect;
function useVirtualizerBase({
  useFlushSync = true,
  ...options
}) {
  const rerender = react__WEBPACK_IMPORTED_MODULE_0__.useReducer(() => ({}), {})[1];
  const resolvedOptions = {
    ...options,
    onChange: (instance2, sync) => {
      var _a;
      if (useFlushSync && sync) {
        (0,react_dom__WEBPACK_IMPORTED_MODULE_1__.flushSync)(rerender);
      } else {
        rerender();
      }
      (_a = options.onChange) == null ? void 0 : _a.call(options, instance2, sync);
    }
  };
  const [instance] = react__WEBPACK_IMPORTED_MODULE_0__.useState(
    () => new _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.Virtualizer(resolvedOptions)
  );
  instance.setOptions(resolvedOptions);
  useIsomorphicLayoutEffect(() => {
    return instance._didMount();
  }, []);
  useIsomorphicLayoutEffect(() => {
    return instance._willUpdate();
  });
  return instance;
}
function useVirtualizer(options) {
  return useVirtualizerBase({
    observeElementRect: _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeElementRect,
    observeElementOffset: _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeElementOffset,
    scrollToFn: _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.elementScroll,
    ...options
  });
}
function useWindowVirtualizer(options) {
  return useVirtualizerBase({
    getScrollElement: () => typeof document !== "undefined" ? window : null,
    observeElementRect: _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeWindowRect,
    observeElementOffset: _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.observeWindowOffset,
    scrollToFn: _tanstack_virtual_core__WEBPACK_IMPORTED_MODULE_2__.windowScroll,
    initialOffset: () => typeof document !== "undefined" ? window.scrollY : 0,
    ...options
  });
}

//# sourceMappingURL=index.js.map


/***/ }),

/***/ "./packages/node_modules/@tanstack/virtual-core/dist/esm/index.js":
/*!************************************************************************!*\
  !*** ./packages/node_modules/@tanstack/virtual-core/dist/esm/index.js ***!
  \************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Virtualizer: function() { return /* binding */ Virtualizer; },
/* harmony export */   approxEqual: function() { return /* reexport safe */ _utils_js__WEBPACK_IMPORTED_MODULE_0__.approxEqual; },
/* harmony export */   debounce: function() { return /* reexport safe */ _utils_js__WEBPACK_IMPORTED_MODULE_0__.debounce; },
/* harmony export */   defaultKeyExtractor: function() { return /* binding */ defaultKeyExtractor; },
/* harmony export */   defaultRangeExtractor: function() { return /* binding */ defaultRangeExtractor; },
/* harmony export */   elementScroll: function() { return /* binding */ elementScroll; },
/* harmony export */   measureElement: function() { return /* binding */ measureElement; },
/* harmony export */   memo: function() { return /* reexport safe */ _utils_js__WEBPACK_IMPORTED_MODULE_0__.memo; },
/* harmony export */   notUndefined: function() { return /* reexport safe */ _utils_js__WEBPACK_IMPORTED_MODULE_0__.notUndefined; },
/* harmony export */   observeElementOffset: function() { return /* binding */ observeElementOffset; },
/* harmony export */   observeElementRect: function() { return /* binding */ observeElementRect; },
/* harmony export */   observeWindowOffset: function() { return /* binding */ observeWindowOffset; },
/* harmony export */   observeWindowRect: function() { return /* binding */ observeWindowRect; },
/* harmony export */   windowScroll: function() { return /* binding */ windowScroll; }
/* harmony export */ });
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./packages/node_modules/@tanstack/virtual-core/dist/esm/utils.js");

const getRect = (element) => {
  const { offsetWidth, offsetHeight } = element;
  return { width: offsetWidth, height: offsetHeight };
};
const defaultKeyExtractor = (index) => index;
const defaultRangeExtractor = (range) => {
  const start = Math.max(range.startIndex - range.overscan, 0);
  const end = Math.min(range.endIndex + range.overscan, range.count - 1);
  const arr = [];
  for (let i = start; i <= end; i++) {
    arr.push(i);
  }
  return arr;
};
const observeElementRect = (instance, cb) => {
  const element = instance.scrollElement;
  if (!element) {
    return;
  }
  const targetWindow = instance.targetWindow;
  if (!targetWindow) {
    return;
  }
  const handler = (rect) => {
    const { width, height } = rect;
    cb({ width: Math.round(width), height: Math.round(height) });
  };
  handler(getRect(element));
  if (!targetWindow.ResizeObserver) {
    return () => {
    };
  }
  const observer = new targetWindow.ResizeObserver((entries) => {
    const run = () => {
      const entry = entries[0];
      if (entry == null ? void 0 : entry.borderBoxSize) {
        const box = entry.borderBoxSize[0];
        if (box) {
          handler({ width: box.inlineSize, height: box.blockSize });
          return;
        }
      }
      handler(getRect(element));
    };
    instance.options.useAnimationFrameWithResizeObserver ? requestAnimationFrame(run) : run();
  });
  observer.observe(element, { box: "border-box" });
  return () => {
    observer.unobserve(element);
  };
};
const addEventListenerOptions = {
  passive: true
};
const observeWindowRect = (instance, cb) => {
  const element = instance.scrollElement;
  if (!element) {
    return;
  }
  const handler = () => {
    cb({ width: element.innerWidth, height: element.innerHeight });
  };
  handler();
  element.addEventListener("resize", handler, addEventListenerOptions);
  return () => {
    element.removeEventListener("resize", handler);
  };
};
const supportsScrollend = typeof window == "undefined" ? true : "onscrollend" in window;
const observeElementOffset = (instance, cb) => {
  const element = instance.scrollElement;
  if (!element) {
    return;
  }
  const targetWindow = instance.targetWindow;
  if (!targetWindow) {
    return;
  }
  let offset = 0;
  const fallback = instance.options.useScrollendEvent && supportsScrollend ? () => void 0 : (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.debounce)(
    targetWindow,
    () => {
      cb(offset, false);
    },
    instance.options.isScrollingResetDelay
  );
  const createHandler = (isScrolling) => () => {
    const { horizontal, isRtl } = instance.options;
    offset = horizontal ? element["scrollLeft"] * (isRtl && -1 || 1) : element["scrollTop"];
    fallback();
    cb(offset, isScrolling);
  };
  const handler = createHandler(true);
  const endHandler = createHandler(false);
  element.addEventListener("scroll", handler, addEventListenerOptions);
  const registerScrollendEvent = instance.options.useScrollendEvent && supportsScrollend;
  if (registerScrollendEvent) {
    element.addEventListener("scrollend", endHandler, addEventListenerOptions);
  }
  return () => {
    element.removeEventListener("scroll", handler);
    if (registerScrollendEvent) {
      element.removeEventListener("scrollend", endHandler);
    }
  };
};
const observeWindowOffset = (instance, cb) => {
  const element = instance.scrollElement;
  if (!element) {
    return;
  }
  const targetWindow = instance.targetWindow;
  if (!targetWindow) {
    return;
  }
  let offset = 0;
  const fallback = instance.options.useScrollendEvent && supportsScrollend ? () => void 0 : (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.debounce)(
    targetWindow,
    () => {
      cb(offset, false);
    },
    instance.options.isScrollingResetDelay
  );
  const createHandler = (isScrolling) => () => {
    offset = element[instance.options.horizontal ? "scrollX" : "scrollY"];
    fallback();
    cb(offset, isScrolling);
  };
  const handler = createHandler(true);
  const endHandler = createHandler(false);
  element.addEventListener("scroll", handler, addEventListenerOptions);
  const registerScrollendEvent = instance.options.useScrollendEvent && supportsScrollend;
  if (registerScrollendEvent) {
    element.addEventListener("scrollend", endHandler, addEventListenerOptions);
  }
  return () => {
    element.removeEventListener("scroll", handler);
    if (registerScrollendEvent) {
      element.removeEventListener("scrollend", endHandler);
    }
  };
};
const measureElement = (element, entry, instance) => {
  if (entry == null ? void 0 : entry.borderBoxSize) {
    const box = entry.borderBoxSize[0];
    if (box) {
      const size = Math.round(
        box[instance.options.horizontal ? "inlineSize" : "blockSize"]
      );
      return size;
    }
  }
  return element[instance.options.horizontal ? "offsetWidth" : "offsetHeight"];
};
const windowScroll = (offset, {
  adjustments = 0,
  behavior
}, instance) => {
  var _a, _b;
  const toOffset = offset + adjustments;
  (_b = (_a = instance.scrollElement) == null ? void 0 : _a.scrollTo) == null ? void 0 : _b.call(_a, {
    [instance.options.horizontal ? "left" : "top"]: toOffset,
    behavior
  });
};
const elementScroll = (offset, {
  adjustments = 0,
  behavior
}, instance) => {
  var _a, _b;
  const toOffset = offset + adjustments;
  (_b = (_a = instance.scrollElement) == null ? void 0 : _a.scrollTo) == null ? void 0 : _b.call(_a, {
    [instance.options.horizontal ? "left" : "top"]: toOffset,
    behavior
  });
};
class Virtualizer {
  constructor(opts) {
    this.unsubs = [];
    this.scrollElement = null;
    this.targetWindow = null;
    this.isScrolling = false;
    this.scrollState = null;
    this.measurementsCache = [];
    this.itemSizeCache = /* @__PURE__ */ new Map();
    this.laneAssignments = /* @__PURE__ */ new Map();
    this.pendingMeasuredCacheIndexes = [];
    this.prevLanes = void 0;
    this.lanesChangedFlag = false;
    this.lanesSettling = false;
    this.scrollRect = null;
    this.scrollOffset = null;
    this.scrollDirection = null;
    this.scrollAdjustments = 0;
    this.elementsCache = /* @__PURE__ */ new Map();
    this.now = () => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = this.targetWindow) == null ? void 0 : _a.performance) == null ? void 0 : _b.now) == null ? void 0 : _c.call(_b)) ?? Date.now();
    };
    this.observer = /* @__PURE__ */ (() => {
      let _ro = null;
      const get = () => {
        if (_ro) {
          return _ro;
        }
        if (!this.targetWindow || !this.targetWindow.ResizeObserver) {
          return null;
        }
        return _ro = new this.targetWindow.ResizeObserver((entries) => {
          entries.forEach((entry) => {
            const run = () => {
              const node = entry.target;
              const index = this.indexFromElement(node);
              if (!node.isConnected) {
                this.observer.unobserve(node);
                return;
              }
              if (this.shouldMeasureDuringScroll(index)) {
                this.resizeItem(
                  index,
                  this.options.measureElement(node, entry, this)
                );
              }
            };
            this.options.useAnimationFrameWithResizeObserver ? requestAnimationFrame(run) : run();
          });
        });
      };
      return {
        disconnect: () => {
          var _a;
          (_a = get()) == null ? void 0 : _a.disconnect();
          _ro = null;
        },
        observe: (target) => {
          var _a;
          return (_a = get()) == null ? void 0 : _a.observe(target, { box: "border-box" });
        },
        unobserve: (target) => {
          var _a;
          return (_a = get()) == null ? void 0 : _a.unobserve(target);
        }
      };
    })();
    this.range = null;
    this.setOptions = (opts2) => {
      Object.entries(opts2).forEach(([key, value]) => {
        if (typeof value === "undefined") delete opts2[key];
      });
      this.options = {
        debug: false,
        initialOffset: 0,
        overscan: 1,
        paddingStart: 0,
        paddingEnd: 0,
        scrollPaddingStart: 0,
        scrollPaddingEnd: 0,
        horizontal: false,
        getItemKey: defaultKeyExtractor,
        rangeExtractor: defaultRangeExtractor,
        onChange: () => {
        },
        measureElement,
        initialRect: { width: 0, height: 0 },
        scrollMargin: 0,
        gap: 0,
        indexAttribute: "data-index",
        initialMeasurementsCache: [],
        lanes: 1,
        isScrollingResetDelay: 150,
        enabled: true,
        isRtl: false,
        useScrollendEvent: false,
        useAnimationFrameWithResizeObserver: false,
        laneAssignmentMode: "estimate",
        ...opts2
      };
    };
    this.notify = (sync) => {
      var _a, _b;
      (_b = (_a = this.options).onChange) == null ? void 0 : _b.call(_a, this, sync);
    };
    this.maybeNotify = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.memo)(
      () => {
        this.calculateRange();
        return [
          this.isScrolling,
          this.range ? this.range.startIndex : null,
          this.range ? this.range.endIndex : null
        ];
      },
      (isScrolling) => {
        this.notify(isScrolling);
      },
      {
        key:  true && "maybeNotify",
        debug: () => this.options.debug,
        initialDeps: [
          this.isScrolling,
          this.range ? this.range.startIndex : null,
          this.range ? this.range.endIndex : null
        ]
      }
    );
    this.cleanup = () => {
      this.unsubs.filter(Boolean).forEach((d) => d());
      this.unsubs = [];
      this.observer.disconnect();
      if (this.rafId != null && this.targetWindow) {
        this.targetWindow.cancelAnimationFrame(this.rafId);
        this.rafId = null;
      }
      this.scrollState = null;
      this.scrollElement = null;
      this.targetWindow = null;
    };
    this._didMount = () => {
      return () => {
        this.cleanup();
      };
    };
    this._willUpdate = () => {
      var _a;
      const scrollElement = this.options.enabled ? this.options.getScrollElement() : null;
      if (this.scrollElement !== scrollElement) {
        this.cleanup();
        if (!scrollElement) {
          this.maybeNotify();
          return;
        }
        this.scrollElement = scrollElement;
        if (this.scrollElement && "ownerDocument" in this.scrollElement) {
          this.targetWindow = this.scrollElement.ownerDocument.defaultView;
        } else {
          this.targetWindow = ((_a = this.scrollElement) == null ? void 0 : _a.window) ?? null;
        }
        this.elementsCache.forEach((cached) => {
          this.observer.observe(cached);
        });
        this.unsubs.push(
          this.options.observeElementRect(this, (rect) => {
            this.scrollRect = rect;
            this.maybeNotify();
          })
        );
        this.unsubs.push(
          this.options.observeElementOffset(this, (offset, isScrolling) => {
            this.scrollAdjustments = 0;
            this.scrollDirection = isScrolling ? this.getScrollOffset() < offset ? "forward" : "backward" : null;
            this.scrollOffset = offset;
            this.isScrolling = isScrolling;
            if (this.scrollState) {
              this.scheduleScrollReconcile();
            }
            this.maybeNotify();
          })
        );
        this._scrollToOffset(this.getScrollOffset(), {
          adjustments: void 0,
          behavior: void 0
        });
      }
    };
    this.rafId = null;
    this.getSize = () => {
      if (!this.options.enabled) {
        this.scrollRect = null;
        return 0;
      }
      this.scrollRect = this.scrollRect ?? this.options.initialRect;
      return this.scrollRect[this.options.horizontal ? "width" : "height"];
    };
    this.getScrollOffset = () => {
      if (!this.options.enabled) {
        this.scrollOffset = null;
        return 0;
      }
      this.scrollOffset = this.scrollOffset ?? (typeof this.options.initialOffset === "function" ? this.options.initialOffset() : this.options.initialOffset);
      return this.scrollOffset;
    };
    this.getFurthestMeasurement = (measurements, index) => {
      const furthestMeasurementsFound = /* @__PURE__ */ new Map();
      const furthestMeasurements = /* @__PURE__ */ new Map();
      for (let m = index - 1; m >= 0; m--) {
        const measurement = measurements[m];
        if (furthestMeasurementsFound.has(measurement.lane)) {
          continue;
        }
        const previousFurthestMeasurement = furthestMeasurements.get(
          measurement.lane
        );
        if (previousFurthestMeasurement == null || measurement.end > previousFurthestMeasurement.end) {
          furthestMeasurements.set(measurement.lane, measurement);
        } else if (measurement.end < previousFurthestMeasurement.end) {
          furthestMeasurementsFound.set(measurement.lane, true);
        }
        if (furthestMeasurementsFound.size === this.options.lanes) {
          break;
        }
      }
      return furthestMeasurements.size === this.options.lanes ? Array.from(furthestMeasurements.values()).sort((a, b) => {
        if (a.end === b.end) {
          return a.index - b.index;
        }
        return a.end - b.end;
      })[0] : void 0;
    };
    this.getMeasurementOptions = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.memo)(
      () => [
        this.options.count,
        this.options.paddingStart,
        this.options.scrollMargin,
        this.options.getItemKey,
        this.options.enabled,
        this.options.lanes,
        this.options.laneAssignmentMode
      ],
      (count, paddingStart, scrollMargin, getItemKey, enabled, lanes, laneAssignmentMode) => {
        const lanesChanged = this.prevLanes !== void 0 && this.prevLanes !== lanes;
        if (lanesChanged) {
          this.lanesChangedFlag = true;
        }
        this.prevLanes = lanes;
        this.pendingMeasuredCacheIndexes = [];
        return {
          count,
          paddingStart,
          scrollMargin,
          getItemKey,
          enabled,
          lanes,
          laneAssignmentMode
        };
      },
      {
        key: false
      }
    );
    this.getMeasurements = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.memo)(
      () => [this.getMeasurementOptions(), this.itemSizeCache],
      ({
        count,
        paddingStart,
        scrollMargin,
        getItemKey,
        enabled,
        lanes,
        laneAssignmentMode
      }, itemSizeCache) => {
        if (!enabled) {
          this.measurementsCache = [];
          this.itemSizeCache.clear();
          this.laneAssignments.clear();
          return [];
        }
        if (this.laneAssignments.size > count) {
          for (const index of this.laneAssignments.keys()) {
            if (index >= count) {
              this.laneAssignments.delete(index);
            }
          }
        }
        if (this.lanesChangedFlag) {
          this.lanesChangedFlag = false;
          this.lanesSettling = true;
          this.measurementsCache = [];
          this.itemSizeCache.clear();
          this.laneAssignments.clear();
          this.pendingMeasuredCacheIndexes = [];
        }
        if (this.measurementsCache.length === 0 && !this.lanesSettling) {
          this.measurementsCache = this.options.initialMeasurementsCache;
          this.measurementsCache.forEach((item) => {
            this.itemSizeCache.set(item.key, item.size);
          });
        }
        const min = this.lanesSettling ? 0 : this.pendingMeasuredCacheIndexes.length > 0 ? Math.min(...this.pendingMeasuredCacheIndexes) : 0;
        this.pendingMeasuredCacheIndexes = [];
        if (this.lanesSettling && this.measurementsCache.length === count) {
          this.lanesSettling = false;
        }
        const measurements = this.measurementsCache.slice(0, min);
        const laneLastIndex = new Array(lanes).fill(
          void 0
        );
        for (let m = 0; m < min; m++) {
          const item = measurements[m];
          if (item) {
            laneLastIndex[item.lane] = m;
          }
        }
        for (let i = min; i < count; i++) {
          const key = getItemKey(i);
          const cachedLane = this.laneAssignments.get(i);
          let lane;
          let start;
          const shouldCacheLane = laneAssignmentMode === "estimate" || itemSizeCache.has(key);
          if (cachedLane !== void 0 && this.options.lanes > 1) {
            lane = cachedLane;
            const prevIndex = laneLastIndex[lane];
            const prevInLane = prevIndex !== void 0 ? measurements[prevIndex] : void 0;
            start = prevInLane ? prevInLane.end + this.options.gap : paddingStart + scrollMargin;
          } else {
            const furthestMeasurement = this.options.lanes === 1 ? measurements[i - 1] : this.getFurthestMeasurement(measurements, i);
            start = furthestMeasurement ? furthestMeasurement.end + this.options.gap : paddingStart + scrollMargin;
            lane = furthestMeasurement ? furthestMeasurement.lane : i % this.options.lanes;
            if (this.options.lanes > 1 && shouldCacheLane) {
              this.laneAssignments.set(i, lane);
            }
          }
          const measuredSize = itemSizeCache.get(key);
          const size = typeof measuredSize === "number" ? measuredSize : this.options.estimateSize(i);
          const end = start + size;
          measurements[i] = {
            index: i,
            start,
            size,
            end,
            key,
            lane
          };
          laneLastIndex[lane] = i;
        }
        this.measurementsCache = measurements;
        return measurements;
      },
      {
        key:  true && "getMeasurements",
        debug: () => this.options.debug
      }
    );
    this.calculateRange = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.memo)(
      () => [
        this.getMeasurements(),
        this.getSize(),
        this.getScrollOffset(),
        this.options.lanes
      ],
      (measurements, outerSize, scrollOffset, lanes) => {
        return this.range = measurements.length > 0 && outerSize > 0 ? calculateRange({
          measurements,
          outerSize,
          scrollOffset,
          lanes
        }) : null;
      },
      {
        key:  true && "calculateRange",
        debug: () => this.options.debug
      }
    );
    this.getVirtualIndexes = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.memo)(
      () => {
        let startIndex = null;
        let endIndex = null;
        const range = this.calculateRange();
        if (range) {
          startIndex = range.startIndex;
          endIndex = range.endIndex;
        }
        this.maybeNotify.updateDeps([this.isScrolling, startIndex, endIndex]);
        return [
          this.options.rangeExtractor,
          this.options.overscan,
          this.options.count,
          startIndex,
          endIndex
        ];
      },
      (rangeExtractor, overscan, count, startIndex, endIndex) => {
        return startIndex === null || endIndex === null ? [] : rangeExtractor({
          startIndex,
          endIndex,
          overscan,
          count
        });
      },
      {
        key:  true && "getVirtualIndexes",
        debug: () => this.options.debug
      }
    );
    this.indexFromElement = (node) => {
      const attributeName = this.options.indexAttribute;
      const indexStr = node.getAttribute(attributeName);
      if (!indexStr) {
        console.warn(
          `Missing attribute name '${attributeName}={index}' on measured element.`
        );
        return -1;
      }
      return parseInt(indexStr, 10);
    };
    this.shouldMeasureDuringScroll = (index) => {
      var _a;
      if (!this.scrollState || this.scrollState.behavior !== "smooth") {
        return true;
      }
      const scrollIndex = this.scrollState.index ?? ((_a = this.getVirtualItemForOffset(this.scrollState.lastTargetOffset)) == null ? void 0 : _a.index);
      if (scrollIndex !== void 0 && this.range) {
        const bufferSize = Math.max(
          this.options.overscan,
          Math.ceil((this.range.endIndex - this.range.startIndex) / 2)
        );
        const minIndex = Math.max(0, scrollIndex - bufferSize);
        const maxIndex = Math.min(
          this.options.count - 1,
          scrollIndex + bufferSize
        );
        return index >= minIndex && index <= maxIndex;
      }
      return true;
    };
    this.measureElement = (node) => {
      if (!node) {
        this.elementsCache.forEach((cached, key2) => {
          if (!cached.isConnected) {
            this.observer.unobserve(cached);
            this.elementsCache.delete(key2);
          }
        });
        return;
      }
      const index = this.indexFromElement(node);
      const key = this.options.getItemKey(index);
      const prevNode = this.elementsCache.get(key);
      if (prevNode !== node) {
        if (prevNode) {
          this.observer.unobserve(prevNode);
        }
        this.observer.observe(node);
        this.elementsCache.set(key, node);
      }
      if ((!this.isScrolling || this.scrollState) && this.shouldMeasureDuringScroll(index)) {
        this.resizeItem(index, this.options.measureElement(node, void 0, this));
      }
    };
    this.resizeItem = (index, size) => {
      var _a;
      const item = this.measurementsCache[index];
      if (!item) return;
      const itemSize = this.itemSizeCache.get(item.key) ?? item.size;
      const delta = size - itemSize;
      if (delta !== 0) {
        if (((_a = this.scrollState) == null ? void 0 : _a.behavior) !== "smooth" && (this.shouldAdjustScrollPositionOnItemSizeChange !== void 0 ? this.shouldAdjustScrollPositionOnItemSizeChange(item, delta, this) : item.start < this.getScrollOffset() + this.scrollAdjustments)) {
          if ( true && this.options.debug) {
            console.info("correction", delta);
          }
          this._scrollToOffset(this.getScrollOffset(), {
            adjustments: this.scrollAdjustments += delta,
            behavior: void 0
          });
        }
        this.pendingMeasuredCacheIndexes.push(item.index);
        this.itemSizeCache = new Map(this.itemSizeCache.set(item.key, size));
        this.notify(false);
      }
    };
    this.getVirtualItems = (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.memo)(
      () => [this.getVirtualIndexes(), this.getMeasurements()],
      (indexes, measurements) => {
        const virtualItems = [];
        for (let k = 0, len = indexes.length; k < len; k++) {
          const i = indexes[k];
          const measurement = measurements[i];
          virtualItems.push(measurement);
        }
        return virtualItems;
      },
      {
        key:  true && "getVirtualItems",
        debug: () => this.options.debug
      }
    );
    this.getVirtualItemForOffset = (offset) => {
      const measurements = this.getMeasurements();
      if (measurements.length === 0) {
        return void 0;
      }
      return (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.notUndefined)(
        measurements[findNearestBinarySearch(
          0,
          measurements.length - 1,
          (index) => (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.notUndefined)(measurements[index]).start,
          offset
        )]
      );
    };
    this.getMaxScrollOffset = () => {
      if (!this.scrollElement) return 0;
      if ("scrollHeight" in this.scrollElement) {
        return this.options.horizontal ? this.scrollElement.scrollWidth - this.scrollElement.clientWidth : this.scrollElement.scrollHeight - this.scrollElement.clientHeight;
      } else {
        const doc = this.scrollElement.document.documentElement;
        return this.options.horizontal ? doc.scrollWidth - this.scrollElement.innerWidth : doc.scrollHeight - this.scrollElement.innerHeight;
      }
    };
    this.getOffsetForAlignment = (toOffset, align, itemSize = 0) => {
      if (!this.scrollElement) return 0;
      const size = this.getSize();
      const scrollOffset = this.getScrollOffset();
      if (align === "auto") {
        align = toOffset >= scrollOffset + size ? "end" : "start";
      }
      if (align === "center") {
        toOffset += (itemSize - size) / 2;
      } else if (align === "end") {
        toOffset -= size;
      }
      const maxOffset = this.getMaxScrollOffset();
      return Math.max(Math.min(maxOffset, toOffset), 0);
    };
    this.getOffsetForIndex = (index, align = "auto") => {
      index = Math.max(0, Math.min(index, this.options.count - 1));
      const size = this.getSize();
      const scrollOffset = this.getScrollOffset();
      const item = this.measurementsCache[index];
      if (!item) return;
      if (align === "auto") {
        if (item.end >= scrollOffset + size - this.options.scrollPaddingEnd) {
          align = "end";
        } else if (item.start <= scrollOffset + this.options.scrollPaddingStart) {
          align = "start";
        } else {
          return [scrollOffset, align];
        }
      }
      if (align === "end" && index === this.options.count - 1) {
        return [this.getMaxScrollOffset(), align];
      }
      const toOffset = align === "end" ? item.end + this.options.scrollPaddingEnd : item.start - this.options.scrollPaddingStart;
      return [
        this.getOffsetForAlignment(toOffset, align, item.size),
        align
      ];
    };
    this.scrollToOffset = (toOffset, { align = "start", behavior = "auto" } = {}) => {
      const offset = this.getOffsetForAlignment(toOffset, align);
      const now = this.now();
      this.scrollState = {
        index: null,
        align,
        behavior,
        startedAt: now,
        lastTargetOffset: offset,
        stableFrames: 0
      };
      this._scrollToOffset(offset, { adjustments: void 0, behavior });
      this.scheduleScrollReconcile();
    };
    this.scrollToIndex = (index, {
      align: initialAlign = "auto",
      behavior = "auto"
    } = {}) => {
      index = Math.max(0, Math.min(index, this.options.count - 1));
      const offsetInfo = this.getOffsetForIndex(index, initialAlign);
      if (!offsetInfo) {
        return;
      }
      const [offset, align] = offsetInfo;
      const now = this.now();
      this.scrollState = {
        index,
        align,
        behavior,
        startedAt: now,
        lastTargetOffset: offset,
        stableFrames: 0
      };
      this._scrollToOffset(offset, { adjustments: void 0, behavior });
      this.scheduleScrollReconcile();
    };
    this.scrollBy = (delta, { behavior = "auto" } = {}) => {
      const offset = this.getScrollOffset() + delta;
      const now = this.now();
      this.scrollState = {
        index: null,
        align: "start",
        behavior,
        startedAt: now,
        lastTargetOffset: offset,
        stableFrames: 0
      };
      this._scrollToOffset(offset, { adjustments: void 0, behavior });
      this.scheduleScrollReconcile();
    };
    this.getTotalSize = () => {
      var _a;
      const measurements = this.getMeasurements();
      let end;
      if (measurements.length === 0) {
        end = this.options.paddingStart;
      } else if (this.options.lanes === 1) {
        end = ((_a = measurements[measurements.length - 1]) == null ? void 0 : _a.end) ?? 0;
      } else {
        const endByLane = Array(this.options.lanes).fill(null);
        let endIndex = measurements.length - 1;
        while (endIndex >= 0 && endByLane.some((val) => val === null)) {
          const item = measurements[endIndex];
          if (endByLane[item.lane] === null) {
            endByLane[item.lane] = item.end;
          }
          endIndex--;
        }
        end = Math.max(...endByLane.filter((val) => val !== null));
      }
      return Math.max(
        end - this.options.scrollMargin + this.options.paddingEnd,
        0
      );
    };
    this._scrollToOffset = (offset, {
      adjustments,
      behavior
    }) => {
      this.options.scrollToFn(offset, { behavior, adjustments }, this);
    };
    this.measure = () => {
      this.itemSizeCache = /* @__PURE__ */ new Map();
      this.laneAssignments = /* @__PURE__ */ new Map();
      this.notify(false);
    };
    this.setOptions(opts);
  }
  scheduleScrollReconcile() {
    if (!this.targetWindow) {
      this.scrollState = null;
      return;
    }
    if (this.rafId != null) return;
    this.rafId = this.targetWindow.requestAnimationFrame(() => {
      this.rafId = null;
      this.reconcileScroll();
    });
  }
  reconcileScroll() {
    if (!this.scrollState) return;
    const el = this.scrollElement;
    if (!el) return;
    const MAX_RECONCILE_MS = 5e3;
    if (this.now() - this.scrollState.startedAt > MAX_RECONCILE_MS) {
      this.scrollState = null;
      return;
    }
    const offsetInfo = this.scrollState.index != null ? this.getOffsetForIndex(this.scrollState.index, this.scrollState.align) : void 0;
    const targetOffset = offsetInfo ? offsetInfo[0] : this.scrollState.lastTargetOffset;
    const STABLE_FRAMES = 1;
    const targetChanged = targetOffset !== this.scrollState.lastTargetOffset;
    if (!targetChanged && (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.approxEqual)(targetOffset, this.getScrollOffset())) {
      this.scrollState.stableFrames++;
      if (this.scrollState.stableFrames >= STABLE_FRAMES) {
        this.scrollState = null;
        return;
      }
    } else {
      this.scrollState.stableFrames = 0;
      if (targetChanged) {
        this.scrollState.lastTargetOffset = targetOffset;
        this.scrollState.behavior = "auto";
        this._scrollToOffset(targetOffset, {
          adjustments: void 0,
          behavior: "auto"
        });
      }
    }
    this.scheduleScrollReconcile();
  }
}
const findNearestBinarySearch = (low, high, getCurrentValue, value) => {
  while (low <= high) {
    const middle = (low + high) / 2 | 0;
    const currentValue = getCurrentValue(middle);
    if (currentValue < value) {
      low = middle + 1;
    } else if (currentValue > value) {
      high = middle - 1;
    } else {
      return middle;
    }
  }
  if (low > 0) {
    return low - 1;
  } else {
    return 0;
  }
};
function calculateRange({
  measurements,
  outerSize,
  scrollOffset,
  lanes
}) {
  const lastIndex = measurements.length - 1;
  const getOffset = (index) => measurements[index].start;
  if (measurements.length <= lanes) {
    return {
      startIndex: 0,
      endIndex: lastIndex
    };
  }
  let startIndex = findNearestBinarySearch(
    0,
    lastIndex,
    getOffset,
    scrollOffset
  );
  let endIndex = startIndex;
  if (lanes === 1) {
    while (endIndex < lastIndex && measurements[endIndex].end < scrollOffset + outerSize) {
      endIndex++;
    }
  } else if (lanes > 1) {
    const endPerLane = Array(lanes).fill(0);
    while (endIndex < lastIndex && endPerLane.some((pos) => pos < scrollOffset + outerSize)) {
      const item = measurements[endIndex];
      endPerLane[item.lane] = item.end;
      endIndex++;
    }
    const startPerLane = Array(lanes).fill(scrollOffset + outerSize);
    while (startIndex >= 0 && startPerLane.some((pos) => pos >= scrollOffset)) {
      const item = measurements[startIndex];
      startPerLane[item.lane] = item.start;
      startIndex--;
    }
    startIndex = Math.max(0, startIndex - startIndex % lanes);
    endIndex = Math.min(lastIndex, endIndex + (lanes - 1 - endIndex % lanes));
  }
  return { startIndex, endIndex };
}

//# sourceMappingURL=index.js.map


/***/ }),

/***/ "./packages/node_modules/@tanstack/virtual-core/dist/esm/utils.js":
/*!************************************************************************!*\
  !*** ./packages/node_modules/@tanstack/virtual-core/dist/esm/utils.js ***!
  \************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   approxEqual: function() { return /* binding */ approxEqual; },
/* harmony export */   debounce: function() { return /* binding */ debounce; },
/* harmony export */   memo: function() { return /* binding */ memo; },
/* harmony export */   notUndefined: function() { return /* binding */ notUndefined; }
/* harmony export */ });
function memo(getDeps, fn, opts) {
  let deps = opts.initialDeps ?? [];
  let result;
  let isInitial = true;
  function memoizedFunction() {
    var _a, _b, _c;
    let depTime;
    if (opts.key && ((_a = opts.debug) == null ? void 0 : _a.call(opts))) depTime = Date.now();
    const newDeps = getDeps();
    const depsChanged = newDeps.length !== deps.length || newDeps.some((dep, index) => deps[index] !== dep);
    if (!depsChanged) {
      return result;
    }
    deps = newDeps;
    let resultTime;
    if (opts.key && ((_b = opts.debug) == null ? void 0 : _b.call(opts))) resultTime = Date.now();
    result = fn(...newDeps);
    if (opts.key && ((_c = opts.debug) == null ? void 0 : _c.call(opts))) {
      const depEndTime = Math.round((Date.now() - depTime) * 100) / 100;
      const resultEndTime = Math.round((Date.now() - resultTime) * 100) / 100;
      const resultFpsPercentage = resultEndTime / 16;
      const pad = (str, num) => {
        str = String(str);
        while (str.length < num) {
          str = " " + str;
        }
        return str;
      };
      console.info(
        `%c⏱ ${pad(resultEndTime, 5)} /${pad(depEndTime, 5)} ms`,
        `
            font-size: .6rem;
            font-weight: bold;
            color: hsl(${Math.max(
          0,
          Math.min(120 - 120 * resultFpsPercentage, 120)
        )}deg 100% 31%);`,
        opts == null ? void 0 : opts.key
      );
    }
    if ((opts == null ? void 0 : opts.onChange) && !(isInitial && opts.skipInitialOnChange)) {
      opts.onChange(result);
    }
    isInitial = false;
    return result;
  }
  memoizedFunction.updateDeps = (newDeps) => {
    deps = newDeps;
  };
  return memoizedFunction;
}
function notUndefined(value, msg) {
  if (value === void 0) {
    throw new Error(`Unexpected undefined${msg ? `: ${msg}` : ""}`);
  } else {
    return value;
  }
}
const approxEqual = (a, b) => Math.abs(a - b) < 1.01;
const debounce = (targetWindow, fn, ms) => {
  let timeoutId;
  return function(...args) {
    targetWindow.clearTimeout(timeoutId);
    timeoutId = targetWindow.setTimeout(() => fn.apply(this, args), ms);
  };
};

//# sourceMappingURL=utils.js.map


/***/ }),

/***/ "./packages/packages/core/editor-global-classes/service/css-class-usage-service.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/service/css-class-usage-service.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fetchCssClassUsage: function() { return /* binding */ fetchCssClassUsage; }
/* harmony export */ });
/* harmony import */ var _src_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../src/api */ "./packages/packages/core/editor-global-classes/src/api.ts");
/* harmony import */ var _src_components_css_class_usage_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../src/components/css-class-usage/utils */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/utils.ts");


const fetchCssClassUsage = async () => {
  const response = await _src_api__WEBPACK_IMPORTED_MODULE_0__.apiClient.usage();
  return (0,_src_components_css_class_usage_utils__WEBPACK_IMPORTED_MODULE_1__.transformData)(response?.data?.data || {});
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/api.ts":
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/api.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   API_ERROR_CODES: function() { return /* binding */ API_ERROR_CODES; },
/* harmony export */   apiClient: function() { return /* binding */ apiClient; }
/* harmony export */ });
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__);

const RESOURCE_URL = '/global-classes';
const BASE_URL = 'elementor/v1';
const RESOURCE_USAGE_URL = `${RESOURCE_URL}/usage`;
const RESOURCE_POST_URL = `${RESOURCE_URL}/post`;
const RESOURCE_STYLES_URL = `${RESOURCE_URL}/styles`;
function saveGlobalClasses(context, payload) {
  return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().put(`${BASE_URL}${RESOURCE_URL}`, payload, {
    params: {
      context
    }
  });
}
const apiClient = {
  usage: () => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(`${BASE_URL}${RESOURCE_USAGE_URL}`),
  all: (context = 'preview') => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(`${BASE_URL}${RESOURCE_URL}`, {
    params: {
      context
    }
  }),
  getStylesForPost: (postId, context = 'preview') => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(`${BASE_URL}${RESOURCE_POST_URL}`, {
    params: {
      context,
      post_id: postId
    }
  }),
  getStylesByIds: (ids, context = 'preview') => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(`${BASE_URL}${RESOURCE_STYLES_URL}`, {
    params: {
      context,
      ids: ids.join(',')
    }
  }),
  publish: payload => saveGlobalClasses('frontend', payload),
  saveDraft: payload => saveGlobalClasses('preview', payload)
};
const API_ERROR_CODES = {
  DUPLICATED_LABEL: 'DUPLICATED_LABEL'
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/capabilities.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/capabilities.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   UPDATE_CLASS_CAPABILITY_KEY: function() { return /* binding */ UPDATE_CLASS_CAPABILITY_KEY; },
/* harmony export */   getCapabilities: function() { return /* binding */ getCapabilities; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const EXPERIMENT_KEY = 'global_classes_should_enforce_capabilities';
const UPDATE_CLASS_CAPABILITY_KEY = 'elementor_global_classes_update_class';
const getCapabilities = () => {
  const shouldEnforceCapabilities = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.isExperimentActive)(EXPERIMENT_KEY);
  if (shouldEnforceCapabilities) {
    return {
      update: UPDATE_CLASS_CAPABILITY_KEY,
      create: UPDATE_CLASS_CAPABILITY_KEY,
      delete: UPDATE_CLASS_CAPABILITY_KEY,
      updateProps: UPDATE_CLASS_CAPABILITY_KEY
    };
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-item.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/class-item.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassItem: function() { return /* binding */ ClassItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _css_class_usage_components__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../css-class-usage/components */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/index.ts");
/* harmony import */ var _delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./delete-confirmation-dialog */ "./packages/packages/core/editor-global-classes/src/components/class-manager/delete-confirmation-dialog.tsx");
/* harmony import */ var _sortable__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./sortable */ "./packages/packages/core/editor-global-classes/src/components/class-manager/sortable.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }










const ClassItem = ({
  id,
  label,
  renameClass,
  selected,
  disabled,
  sortableTriggerProps,
  showSortIndicator,
  syncToV3,
  onToggleSync
}) => {
  const itemRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const {
    ref: editableRef,
    openEditMode,
    isEditing,
    error,
    getProps: getEditableProps
  } = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.useEditable)({
    value: label,
    onSubmit: renameClass,
    validation: validateLabel
  });
  const [selectedCssUsage, setSelectedCssUsage] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const {
    openDialog
  } = (0,_delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_7__.useDeleteConfirmation)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.usePopupState)({
    variant: 'popover',
    disableAutoFocus: true
  });
  const isSelected = (selectedCssUsage === id || selected || popupState.isOpen) && !disabled;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
    p: 0
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.WarningInfotip, {
    open: Boolean(error),
    text: error ?? '',
    placement: "bottom",
    width: itemRef.current?.getBoundingClientRect().width,
    offset: [0, -15]
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledListItemButton, {
    ref: itemRef,
    dense: true,
    disableGutters: true,
    showSortIndicator: showSortIndicator,
    showActions: isSelected || isEditing,
    shape: "rounded",
    onDoubleClick: openEditMode,
    selected: isSelected,
    disabled: disabled,
    focusVisibleClassName: "visible-class-item"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_sortable__WEBPACK_IMPORTED_MODULE_8__.SortableTrigger, sortableTriggerProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Indicator, {
    isActive: isEditing,
    isError: !!error
  }, isEditing ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.EditableField, _extends({
    ref: editableRef,
    as: _elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography,
    variant: "caption"
  }, getEditableProps())) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.EllipsisWithTooltip, {
    title: label,
    as: _elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography,
    variant: "caption"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, {
    className: 'class-item-locator'
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_class_usage_components__WEBPACK_IMPORTED_MODULE_6__.CssClassUsageTrigger, {
    id: id,
    onClick: setSelectedCssUsage
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
    placement: "top",
    className: 'class-item-more-actions',
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('More actions', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, _extends({
    size: "tiny"
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindTrigger)(popupState), {
    "aria-label": "More actions"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DotsVerticalIcon, {
    fontSize: "tiny"
  })))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Menu, _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindMenu)(popupState), {
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'right'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
    sx: {
      minWidth: '160px'
    },
    onClick: () => {
      popupState.close();
      openEditMode();
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
    variant: "caption",
    sx: {
      color: 'text.primary'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Rename', 'elementor'))), onToggleSync && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
    onClick: () => {
      popupState.close();
      onToggleSync(id, !syncToV3);
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
    direction: "row",
    alignItems: "center",
    gap: 1
  }, syncToV3 ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.RefreshOffIcon, {
    fontSize: "tiny"
  }) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.RefreshIcon, {
    fontSize: "tiny"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
    variant: "caption",
    sx: {
      color: 'text.primary'
    }
  }, syncToV3 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Stop syncing to Global Fonts', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Sync to Global Fonts', 'elementor')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
    onClick: () => {
      popupState.close();
      openDialog({
        id,
        label
      });
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
    variant: "caption",
    sx: {
      color: 'error.light'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Delete', 'elementor')))));
};
const StyledListItemButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.ListItemButton, {
  shouldForwardProp: prop => !['showActions', 'showSortIndicator'].includes(prop)
})(({
  showActions,
  showSortIndicator
}) => `
    min-height: 36px;

    &.visible-class-item {
      box-shadow: none !important;
    }

    .class-item-locator {
      visibility: hidden;
    }

    .class-item-sortable-trigger {
      visibility: ${showSortIndicator && showActions ? 'visible' : 'hidden'};
    }

    &:hover:not(:disabled) {
      .class-item-locator {
        visibility: visible;
      }

      .class-item-sortable-trigger {
        visibility: ${showSortIndicator ? 'visible' : 'hidden'};
      }
    }
  `);
const Indicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, {
  shouldForwardProp: prop => !['isActive', 'isError'].includes(prop)
})(({
  theme,
  isActive,
  isError
}) => ({
  display: 'flex',
  width: '100%',
  flexGrow: 1,
  borderRadius: theme.spacing(0.5),
  border: getIndicatorBorder({
    isActive,
    isError,
    theme
  }),
  padding: `0 ${theme.spacing(1)}`,
  marginLeft: isActive ? theme.spacing(1) : 0,
  minWidth: 0
}));
const getIndicatorBorder = ({
  isActive,
  isError,
  theme
}) => {
  if (isError) {
    return `2px solid ${theme.palette.error.main}`;
  }
  if (isActive) {
    return `2px solid ${theme.palette.secondary.main}`;
  }
  return 'none';
};
const validateLabel = newLabel => {
  const result = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.validateStyleLabel)(newLabel, 'rename');
  if (result.isValid) {
    return null;
  }
  return result.errorMessage;
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-button.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-button.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassManagerButton: function() { return /* binding */ ClassManagerButton; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../global-classes-styles-provider */ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts");
/* harmony import */ var _hooks_use_prefetch_css_class_usage__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../hooks/use-prefetch-css-class-usage */ "./packages/packages/core/editor-global-classes/src/hooks/use-prefetch-css-class-usage.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _flipped_color_swatch_icon__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./flipped-color-swatch-icon */ "./packages/packages/core/editor-global-classes/src/components/class-manager/flipped-color-swatch-icon.tsx");








const EVENT_TOGGLE_DESIGN_SYSTEM = 'elementor/toggle-design-system';
const ClassManagerButton = () => {
  const {
    prefetchClassesUsage
  } = (0,_hooks_use_prefetch_css_class_usage__WEBPACK_IMPORTED_MODULE_5__.usePrefetchCssClassUsage)();
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.useUserStylesCapability)();
  const isUserAllowedToUpdateClass = userCan(_global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_4__.globalClassesStylesProvider.getKey()).update;
  if (!isUserAllowedToUpdateClass) {
    return null;
  }
  const handleOpenPanel = () => {
    window.dispatchEvent(new CustomEvent(EVENT_TOGGLE_DESIGN_SYSTEM, {
      detail: {
        tab: 'classes'
      }
    }));
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_6__.trackGlobalClasses)({
      event: 'classManagerOpened',
      source: 'style-panel'
    });
    prefetchClassesUsage();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Class Manager', 'elementor'),
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    size: "tiny",
    onClick: handleOpenPanel,
    sx: {
      marginInlineEnd: -0.75
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_flipped_color_swatch_icon__WEBPACK_IMPORTED_MODULE_7__.FlippedColorSwatchIcon, {
    fontSize: "tiny"
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-introduction.tsx":
/*!******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-introduction.tsx ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassManagerIntroduction: function() { return /* binding */ ClassManagerIntroduction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);






const MESSAGE_KEY = 'global-class-manager';
const ClassManagerIntroduction = () => {
  const [isMessageSuppressed, suppressMessage] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useSuppressedMessage)(MESSAGE_KEY);
  const [shouldShowIntroduction, setShouldShowIntroduction] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(!isMessageSuppressed);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.IntroductionModal, {
    open: shouldShowIntroduction,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Class Manager', 'elementor'),
    handleClose: shouldShowAgain => {
      if (!shouldShowAgain) {
        suppressMessage();
      }
      setShouldShowIntroduction(false);
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Image, {
    sx: {
      width: '100%',
      aspectRatio: '16 / 9'
    },
    src: 'https://assets.elementor.com/packages/v1/images/class-manager-intro.svg',
    alt: ''
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(IntroductionContent, null));
};
const IntroductionContent = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    p: 3
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: 'body2'
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)("The Class Manager lets you see all the classes you've created, plus adjust their priority, rename them, and delete unused classes to keep your CSS structured.", 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: 'body2'
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Remember, when editing an item within a specific class, any changes you make will apply across all elements in that class.', 'elementor')));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-panel.tsx":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-panel.tsx ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassManagerPanelEmbedded: function() { return /* binding */ ClassManagerPanelEmbedded; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _hooks_use_classes_order__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../hooks/use-classes-order */ "./packages/packages/core/editor-global-classes/src/hooks/use-classes-order.ts");
/* harmony import */ var _hooks_use_dirty_state__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../hooks/use-dirty-state */ "./packages/packages/core/editor-global-classes/src/hooks/use-dirty-state.ts");
/* harmony import */ var _hooks_use_filters__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../hooks/use-filters */ "./packages/packages/core/editor-global-classes/src/hooks/use-filters.ts");
/* harmony import */ var _load_existing_classes__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../load-existing-classes */ "./packages/packages/core/editor-global-classes/src/load-existing-classes.ts");
/* harmony import */ var _save_global_classes__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../save-global-classes */ "./packages/packages/core/editor-global-classes/src/save-global-classes.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _search_and_filter_components_filter_active_filters__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../search-and-filter/components/filter/active-filters */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/active-filters.tsx");
/* harmony import */ var _search_and_filter_components_filter_css_class_filter__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../search-and-filter/components/filter/css-class-filter */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/css-class-filter.tsx");
/* harmony import */ var _search_and_filter_components_search_class_manager_search__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../search-and-filter/components/search/class-manager-search */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/search/class-manager-search.tsx");
/* harmony import */ var _search_and_filter_context__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ../search-and-filter/context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");
/* harmony import */ var _class_manager_introduction__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./class-manager-introduction */ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-introduction.tsx");
/* harmony import */ var _delete_class__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./delete-class */ "./packages/packages/core/editor-global-classes/src/components/class-manager/delete-class.ts");
/* harmony import */ var _flipped_color_swatch_icon__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./flipped-color-swatch-icon */ "./packages/packages/core/editor-global-classes/src/components/class-manager/flipped-color-swatch-icon.tsx");
/* harmony import */ var _global_classes_list__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./global-classes-list */ "./packages/packages/core/editor-global-classes/src/components/class-manager/global-classes-list.tsx");
/* harmony import */ var _panel_interactions__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./panel-interactions */ "./packages/packages/core/editor-global-classes/src/components/class-manager/panel-interactions.ts");
/* harmony import */ var _start_sync_to_v3_modal__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./start-sync-to-v3-modal */ "./packages/packages/core/editor-global-classes/src/components/class-manager/start-sync-to-v3-modal.tsx");



























const STOP_SYNC_MESSAGE_KEY = 'stop-sync-class';
function ClassManagerPanelEmbedded({
  onRequestClose,
  onExposeCloseAttempt,
  isActive
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ClassManagerPanelContent, {
    onRequestClose: onRequestClose,
    onExposeCloseAttempt: onExposeCloseAttempt,
    isActive: isActive
  });
}
function ClassManagerPanelContent({
  onRequestClose,
  onExposeCloseAttempt,
  isActive = true
}) {
  const isDirty = (0,_hooks_use_dirty_state__WEBPACK_IMPORTED_MODULE_10__.useDirtyState)();
  const {
    open: openSaveChangesDialog,
    close: closeSaveChangesDialog,
    isOpen: isSaveChangesDialogOpen
  } = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.useDialog)();
  const [stopSyncConfirmation, setStopSyncConfirmation] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [startSyncConfirmation, setStartSyncConfirmation] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [isStopSyncSuppressed] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useSuppressedMessage)(STOP_SYNC_MESSAGE_KEY);
  const [scrollElement, setScrollElement] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const {
    mutateAsync: publish,
    isPending: isPublishing
  } = usePublish();
  const resetAndClosePanel = () => {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_6__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_14__.slice.actions.resetToInitialState({
      context: 'frontend'
    }));
    closeSaveChangesDialog();
  };
  const handleClosePanel = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (isDirty) {
      openSaveChangesDialog();
      return;
    }
    void onRequestClose();
  }, [isDirty, openSaveChangesDialog, onRequestClose]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!onExposeCloseAttempt) {
      return;
    }
    onExposeCloseAttempt(() => handleClosePanel());
    return () => onExposeCloseAttempt(null);
  }, [onExposeCloseAttempt, handleClosePanel]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    (0,_panel_interactions__WEBPACK_IMPORTED_MODULE_24__.blockPanelInteractions)();
    return () => {
      (0,_panel_interactions__WEBPACK_IMPORTED_MODULE_24__.unblockPanelInteractions)();
    };
  }, []);
  const handleStopSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async classId => {
    await (0,_load_existing_classes__WEBPACK_IMPORTED_MODULE_12__.loadExistingClasses)([classId]);
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_6__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_14__.slice.actions.update({
      style: {
        id: classId,
        sync_to_v3: false
      }
    }));
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_15__.trackGlobalClasses)({
      event: 'classSyncToV3',
      classId,
      action: 'unsync'
    });
    setStopSyncConfirmation(null);
  }, []);
  const handleStartSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async classId => {
    await (0,_load_existing_classes__WEBPACK_IMPORTED_MODULE_12__.loadExistingClasses)([classId]);
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_6__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_14__.slice.actions.update({
      style: {
        id: classId,
        sync_to_v3: true
      }
    }));
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_15__.trackGlobalClasses)({
      event: 'classSyncToV3',
      classId,
      action: 'sync'
    });
    setStartSyncConfirmation(null);
  }, []);
  const handleStopSyncRequest = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(classId => {
    if (!isStopSyncSuppressed) {
      setStopSyncConfirmation(classId);
    } else {
      handleStopSync(classId);
    }
  }, [isStopSyncSuppressed, handleStopSync]);
  usePreventUnload();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.ErrorBoundary, {
    fallback: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ErrorBoundaryFallback, null)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_search_and_filter_context__WEBPACK_IMPORTED_MODULE_19__.SearchAndFilterProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Stack, {
    direction: "column",
    sx: {
      height: '100%',
      width: '100%',
      flex: 1,
      minHeight: 0,
      overflow: 'hidden'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Box, {
    px: 2,
    pb: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyContent: "space-between",
    gap: 0.5,
    sx: {
      pb: 0.5
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Box, {
    sx: {
      flexGrow: 1,
      minWidth: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_search_and_filter_components_search_class_manager_search__WEBPACK_IMPORTED_MODULE_18__.ClassManagerSearch, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_search_and_filter_components_filter_css_class_filter__WEBPACK_IMPORTED_MODULE_17__.CssClassFilter, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TotalCssClassCounter, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_search_and_filter_components_filter_active_filters__WEBPACK_IMPORTED_MODULE_16__.ActiveFilters, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Box, {
    ref: setScrollElement,
    px: 2,
    sx: {
      flexGrow: 1,
      overflowY: 'auto',
      minHeight: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_global_classes_list__WEBPACK_IMPORTED_MODULE_23__.GlobalClassesList, {
    disabled: isPublishing,
    scrollElement: scrollElement,
    onStopSyncRequest: handleStopSyncRequest,
    onStartSyncRequest: classId => setStartSyncConfirmation(classId)
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__.PanelFooter, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Button, {
    fullWidth: true,
    size: "small",
    color: "global",
    variant: "contained",
    onClick: publish,
    disabled: !isDirty,
    loading: isPublishing
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Save changes', 'elementor')))))), isActive && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_class_manager_introduction__WEBPACK_IMPORTED_MODULE_20__.ClassManagerIntroduction, null), startSyncConfirmation && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_start_sync_to_v3_modal__WEBPACK_IMPORTED_MODULE_25__.StartSyncToV3Modal, {
    externalOpen: true,
    classId: startSyncConfirmation,
    onExternalClose: () => setStartSyncConfirmation(null),
    onConfirm: () => handleStartSync(startSyncConfirmation)
  }), stopSyncConfirmation && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StopSyncConfirmationDialog, {
    open: true,
    onClose: () => setStopSyncConfirmation(null),
    onConfirm: () => handleStopSync(stopSyncConfirmation)
  }), isSaveChangesDialogOpen && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.SaveChangesDialog, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.DialogHeader, {
    onClose: closeSaveChangesDialog,
    logo: false
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.SaveChangesDialog.Title, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('You have unsaved changes', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.SaveChangesDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.SaveChangesDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('You have unsaved changes in the Class Manager.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.SaveChangesDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('To avoid losing your updates, save your changes before leaving.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.SaveChangesDialog.Actions, {
    actions: {
      discard: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Discard', 'elementor'),
        action: () => {
          resetAndClosePanel();
        }
      },
      confirm: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Save & Continue', 'elementor'),
        action: async () => {
          await publish();
          closeSaveChangesDialog();
          void onRequestClose();
        }
      }
    }
  })));
}
const ErrorBoundaryFallback = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Box, {
  role: "alert",
  sx: {
    minHeight: '100%',
    p: 2
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Alert, {
  severity: "error",
  sx: {
    mb: 2,
    maxWidth: 400,
    textAlign: 'center'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("strong", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Something went wrong', 'elementor'))));
const usePreventUnload = () => {
  const isDirty = (0,_hooks_use_dirty_state__WEBPACK_IMPORTED_MODULE_10__.useDirtyState)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleBeforeUnload = event => {
      if (isDirty) {
        event.preventDefault();
      }
    };
    window.addEventListener('beforeunload', handleBeforeUnload);
    return () => {
      window.removeEventListener('beforeunload', handleBeforeUnload);
    };
  }, [isDirty]);
};
const usePublish = () => {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_5__.useMutation)({
    mutationFn: () => (0,_save_global_classes__WEBPACK_IMPORTED_MODULE_13__.saveGlobalClasses)({
      context: 'frontend'
    }),
    onSuccess: async () => {
      (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__.setDocumentModifiedStatus)(false);
      if ((0,_delete_class__WEBPACK_IMPORTED_MODULE_21__.hasDeletedItems)()) {
        await (0,_delete_class__WEBPACK_IMPORTED_MODULE_21__.onDelete)();
      }
    }
  });
};
const TotalCssClassCounter = () => {
  const filters = (0,_hooks_use_filters__WEBPACK_IMPORTED_MODULE_11__.useFilters)();
  const cssClasses = (0,_hooks_use_classes_order__WEBPACK_IMPORTED_MODULE_9__.useClassesOrder)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_7__.Chip, {
    size: 'small',
    label: filters ? `${filters.length} / ${cssClasses?.length}` : cssClasses?.length
  });
};
const StopSyncConfirmationDialog = ({
  open,
  onClose,
  onConfirm
}) => {
  const [, suppressStopSyncMessage] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useSuppressedMessage)(STOP_SYNC_MESSAGE_KEY);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ConfirmationDialog, {
    open: open,
    onClose: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ConfirmationDialog.Title, {
    icon: _flipped_color_swatch_icon__WEBPACK_IMPORTED_MODULE_22__.FlippedColorSwatchIcon,
    iconColor: "primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Un-sync typography class', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ConfirmationDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ConfirmationDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)("You're about to stop syncing a typography class to Global Fonts.", 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ConfirmationDialog.ContentText, {
    sx: {
      mt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)("Note that if it's being used anywhere, the affected elements will inherit the default typography.", 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ConfirmationDialog.Actions, {
    onClose: onClose,
    onConfirm: onConfirm,
    cancelLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Cancel', 'elementor'),
    confirmLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Got it', 'elementor'),
    color: "primary",
    onSuppressMessage: suppressStopSyncMessage,
    suppressLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)("Don't show again", 'elementor')
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/delete-class.ts":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/delete-class.ts ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deleteClass: function() { return /* binding */ deleteClass; },
/* harmony export */   hasDeletedItems: function() { return /* binding */ hasDeletedItems; },
/* harmony export */   onDelete: function() { return /* binding */ onDelete; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");



let isDeleted = false;
const deleteClass = id => {
  (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_2__.trackGlobalClasses)({
    event: 'classDeleted',
    classId: id,
    runAction: () => {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.delete(id));
      isDeleted = true;
    }
  });
};
const onDelete = async () => {
  isDeleted = false;
};
const hasDeletedItems = () => isDeleted;

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/delete-confirmation-dialog.tsx":
/*!******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/delete-confirmation-dialog.tsx ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DeleteConfirmationProvider: function() { return /* binding */ DeleteConfirmationProvider; },
/* harmony export */   useDeleteConfirmation: function() { return /* binding */ useDeleteConfirmation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_css_class_usage_by_id__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-css-class-usage-by-id */ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage-by-id.ts");
/* harmony import */ var _delete_class__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./delete-class */ "./packages/packages/core/editor-global-classes/src/components/class-manager/delete-class.ts");







const context = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const DeleteConfirmationProvider = ({
  children
}) => {
  const [dialogProps, setDialogProps] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const openDialog = props => {
    setDialogProps(props);
  };
  const closeDialog = () => {
    setDialogProps(null);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(context.Provider, {
    value: {
      openDialog,
      closeDialog,
      dialogProps
    }
  }, children, !!dialogProps && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DeleteClassDialog, dialogProps));
};
const DeleteClassDialog = ({
  label,
  id
}) => {
  const {
    closeDialog
  } = useDeleteConfirmation();
  const {
    data: {
      total,
      content
    }
  } = (0,_hooks_use_css_class_usage_by_id__WEBPACK_IMPORTED_MODULE_4__.useCssClassUsageByID)(id);
  const handleConfirm = () => {
    closeDialog();
    (0,_delete_class__WEBPACK_IMPORTED_MODULE_5__.deleteClass)(id);
  };

  // translators: %1: total usage count, %2: number of pages
  const text = total && content.length ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Will permanently remove it from your project and may affect the design across all elements using it. Used %1 times across %2 pages. This action cannot be undone.', 'elementor').replace('%1', total.toString()).replace('%2', content.length.toString()) : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Will permanently remove it from your project and may affect the design across all elements using it. This action cannot be undone.', 'elementor');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog, {
    open: true,
    onClose: closeDialog
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Title, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Delete this class?', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Deleting', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle2",
    component: "span"
  }, "\xA0", label, "\xA0"), text)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Actions, {
    onClose: closeDialog,
    onConfirm: handleConfirm
  }));
};
const useDeleteConfirmation = () => {
  const contextValue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(context);
  if (!contextValue) {
    throw new Error('useDeleteConfirmation must be used within a DeleteConfirmationProvider');
  }
  return contextValue;
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/duplicate-label-dialog.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/duplicate-label-dialog.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DuplicateLabelDialog: function() { return /* binding */ DuplicateLabelDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);





const DUP_PREFIX = 'DUP_';
const DuplicateLabelDialog = ({
  modifiedLabels,
  onApprove
}) => {
  const handleButtonClick = () => {
    localStorage.setItem('elementor-global-classes-search', DUP_PREFIX);
    onApprove?.();
    (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.closeDialog)();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogHeader, {
    logo: false
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    display: "flex",
    alignItems: "center",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Icon, {
    color: "secondary"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.InfoCircleFilledIcon, {
    fontSize: "medium"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "subtitle1"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)("We've published your page and updated class names.", 'elementor')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    spacing: 2,
    direction: "column"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "body2"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Some new classes used the same names as existing ones. To prevent conflicts, we added the prefix', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("strong", null, " ", DUP_PREFIX)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    sx: {
      width: '100%',
      display: 'flex',
      gap: 2,
      alignItems: 'flex-start'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "subtitle2",
    sx: {
      fontWeight: 'bold',
      flex: 1,
      flexShrink: 1,
      flexGrow: 1,
      minWidth: 0
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Before', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "subtitle2",
    sx: {
      minWidth: '200px',
      fontWeight: 'bold',
      flexShrink: 0,
      flexGrow: 0,
      width: '200px',
      maxWidth: '200px'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('After', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, {
    sx: {
      mt: 0.5,
      mb: 0.5
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "column",
    gap: 0.5,
    sx: {
      pb: 2
    }
  }, Object.values(modifiedLabels).map(({
    original,
    modified
  }, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    key: index,
    sx: {
      width: '100%',
      display: 'flex',
      gap: 2,
      alignItems: 'flex-start'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    sx: {
      flex: 1,
      flexShrink: 1,
      flexGrow: 1,
      minWidth: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: original
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "body2",
    sx: {
      color: 'text.secondary'
    }
  }, original))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    sx: {
      minWidth: '200px',
      flexShrink: 0,
      flexGrow: 0,
      width: '200px',
      maxWidth: '200px'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: modified
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "body2",
    sx: {
      color: 'text.primary'
    }
  }, modified)))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Alert, {
    severity: "info",
    size: "small",
    color: "secondary"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("strong", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Your designs and classes are safe.', 'elementor')), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Only the prefixes were added. Find them in Class Manager by searching', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("strong", null, DUP_PREFIX)))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogActions, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    color: "secondary",
    variant: "text",
    onClick: handleButtonClick
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Go to Class Manager', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    color: "secondary",
    variant: "contained",
    onClick: _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.closeDialog
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Done', 'elementor'))));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/flipped-color-swatch-icon.tsx":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/flipped-color-swatch-icon.tsx ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FlippedColorSwatchIcon: function() { return /* binding */ FlippedColorSwatchIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const FlippedColorSwatchIcon = ({
  sx,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ColorSwatchIcon, _extends({
  sx: {
    transform: 'rotate(90deg)',
    ...sx
  }
}, props));

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/global-classes-list.tsx":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/global-classes-list.tsx ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GlobalClassesList: function() { return /* binding */ GlobalClassesList; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _tanstack_react_virtual__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @tanstack/react-virtual */ "./packages/node_modules/@tanstack/react-virtual/dist/esm/index.js");
/* harmony import */ var _tanstack_react_virtual__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @tanstack/react-virtual */ "./packages/node_modules/@tanstack/virtual-core/dist/esm/index.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _hooks_use_classes_order__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../hooks/use-classes-order */ "./packages/packages/core/editor-global-classes/src/hooks/use-classes-order.ts");
/* harmony import */ var _hooks_use_filters__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../hooks/use-filters */ "./packages/packages/core/editor-global-classes/src/hooks/use-filters.ts");
/* harmony import */ var _hooks_use_ordered_classes__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../hooks/use-ordered-classes */ "./packages/packages/core/editor-global-classes/src/hooks/use-ordered-classes.ts");
/* harmony import */ var _load_existing_classes__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../load-existing-classes */ "./packages/packages/core/editor-global-classes/src/load-existing-classes.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _search_and_filter_context__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../search-and-filter/context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");
/* harmony import */ var _class_item__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./class-item */ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-item.tsx");
/* harmony import */ var _delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./delete-confirmation-dialog */ "./packages/packages/core/editor-global-classes/src/components/class-manager/delete-confirmation-dialog.tsx");
/* harmony import */ var _flipped_color_swatch_icon__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./flipped-color-swatch-icon */ "./packages/packages/core/editor-global-classes/src/components/class-manager/flipped-color-swatch-icon.tsx");
/* harmony import */ var _not_found__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./not-found */ "./packages/packages/core/editor-global-classes/src/components/class-manager/not-found.tsx");
/* harmony import */ var _sortable__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./sortable */ "./packages/packages/core/editor-global-classes/src/components/class-manager/sortable.tsx");


















const ROW_HEIGHT = 40;
const OVERSCAN = 6;
const GlobalClassesList = ({
  disabled,
  scrollElement,
  onStopSyncRequest,
  onStartSyncRequest
}) => {
  const {
    search: {
      debouncedValue: searchValue
    }
  } = (0,_search_and_filter_context__WEBPACK_IMPORTED_MODULE_12__.useSearchAndFilters)();
  const cssClasses = (0,_hooks_use_ordered_classes__WEBPACK_IMPORTED_MODULE_8__.useOrderedClasses)();
  const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useDispatch)();
  const filters = (0,_hooks_use_filters__WEBPACK_IMPORTED_MODULE_7__.useFilters)();
  const [draggedItemId, setDraggedItemId] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [loading, setLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const addLoadingClass = classId => setLoading(prev => ({
    ...prev,
    [classId]: true
  }));
  const removeLoadingClass = classId => setLoading(prev => {
    const {
      [classId]: _,
      ...rest
    } = prev;
    return rest;
  });
  const draggedItemLabel = cssClasses.find(cssClass => cssClass.id === draggedItemId)?.label ?? '';
  const [classesOrder, reorderClasses] = useReorder(draggedItemId, setDraggedItemId, draggedItemLabel ?? '');
  const filteredCssClasses = useFilteredCssClasses();
  const virtualizer = (0,_tanstack_react_virtual__WEBPACK_IMPORTED_MODULE_3__.useVirtualizer)({
    count: filteredCssClasses.length,
    getScrollElement: () => scrollElement ?? null,
    estimateSize: () => ROW_HEIGHT,
    overscan: OVERSCAN,
    getItemKey: index => filteredCssClasses[index].id,
    // Keep the actively dragged row mounted even when scrolled out of view.
    // SortableItem unregisters its render on unmount, which would make the
    // DragOverlay clone disappear mid-drag.
    rangeExtractor: range => {
      const indices = new Set((0,_tanstack_react_virtual__WEBPACK_IMPORTED_MODULE_4__.defaultRangeExtractor)(range));
      if (draggedItemId) {
        const draggedItemIndex = filteredCssClasses.findIndex(cssClass => cssClass.id === draggedItemId);
        if (draggedItemIndex >= 0) {
          indices.add(draggedItemIndex);
        }
      }
      return [...indices].sort((a, b) => a - b);
    }
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handler = event => {
      if (event.key === 'z' && (event.ctrlKey || event.metaKey)) {
        event.stopImmediatePropagation();
        event.preventDefault();
        if (event.shiftKey) {
          dispatch(_store__WEBPACK_IMPORTED_MODULE_10__.slice.actions.redo());
          return;
        }
        dispatch(_store__WEBPACK_IMPORTED_MODULE_10__.slice.actions.undo());
      }
    };
    window.addEventListener('keydown', handler, {
      capture: true
    });
    return () => window.removeEventListener('keydown', handler);
  }, [dispatch]);
  if (!cssClasses?.length) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EmptyState, null);
  }
  const notFoundType = (0,_not_found__WEBPACK_IMPORTED_MODULE_16__.getNotFoundType)(searchValue, filters, filteredCssClasses);
  if (notFoundType) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_not_found__WEBPACK_IMPORTED_MODULE_16__.NotFound, {
      notFoundType: notFoundType
    });
  }
  const isFiltersApplied = filters?.length || searchValue;
  const allowSorting = filteredCssClasses.length > 1 && !isFiltersApplied;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_14__.DeleteConfirmationProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.List, {
    sx: {
      position: 'relative',
      display: 'block',
      height: virtualizer.getTotalSize(),
      padding: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_sortable__WEBPACK_IMPORTED_MODULE_17__.SortableProvider, {
    value: classesOrder,
    onChange: reorderClasses,
    onDragStart: event => setDraggedItemId(event.active.id),
    onDragEnd: () => setDraggedItemId(null),
    onDragCancel: () => setDraggedItemId(null),
    disableDragOverlay: !allowSorting
  }, virtualizer.getVirtualItems().map(virtualRow => {
    const cssClass = filteredCssClasses[virtualRow.index];
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_sortable__WEBPACK_IMPORTED_MODULE_17__.SortableItem, {
      key: virtualRow.key,
      id: cssClass.id,
      style: {
        position: 'absolute',
        top: virtualRow.start,
        left: 0,
        width: '100%'
      }
    }, ({
      isDragged,
      isDragPlaceholder,
      triggerProps,
      triggerStyle
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_class_item__WEBPACK_IMPORTED_MODULE_13__.ClassItem, {
      id: cssClass.id,
      label: cssClass.label,
      renameClass: async newLabel => {
        addLoadingClass(cssClass.id);
        try {
          (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_11__.trackGlobalClasses)({
            event: 'classRenamed',
            classId: cssClass.id,
            oldValue: cssClass.label,
            newValue: newLabel,
            source: 'class-manager'
          });
          void (await (0,_load_existing_classes__WEBPACK_IMPORTED_MODULE_9__.loadExistingClasses)([cssClass.id]));
          dispatch(_store__WEBPACK_IMPORTED_MODULE_10__.slice.actions.update({
            style: {
              id: cssClass.id,
              label: newLabel
            }
          }));
        } finally {
          removeLoadingClass(cssClass.id);
        }
      },
      selected: isDragged,
      disabled: disabled || isDragPlaceholder || loading[cssClass.id],
      sortableTriggerProps: {
        ...triggerProps,
        style: triggerStyle
      },
      showSortIndicator: allowSorting,
      syncToV3: cssClass.sync_to_v3,
      onToggleSync: (id, newValue) => {
        if (!newValue && onStopSyncRequest) {
          onStopSyncRequest(id);
        } else if (newValue && onStartSyncRequest) {
          onStartSyncRequest(id);
        } else {
          dispatch(_store__WEBPACK_IMPORTED_MODULE_10__.slice.actions.update({
            style: {
              id,
              sync_to_v3: newValue
            }
          }));
        }
      }
    }));
  }))));
};
const EmptyState = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
  alignItems: "center",
  gap: 1.5,
  pt: 10,
  px: 0.5,
  maxWidth: "260px",
  margin: "auto"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_flipped_color_swatch_icon__WEBPACK_IMPORTED_MODULE_15__.FlippedColorSwatchIcon, {
  fontSize: "large"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledHeader, {
  variant: "subtitle2",
  component: "h2",
  color: "text.secondary"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('There are no global classes yet.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
  align: "center",
  variant: "caption",
  color: "text.secondary"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('CSS classes created in the editor panel will appear here. Once they are available, you can arrange their hierarchy, rename them, or delete them as needed.', 'elementor')));

// Override panel reset styles.
const StyledHeader = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography)(({
  theme,
  variant
}) => ({
  '&.MuiTypography-root': {
    ...theme.typography[variant]
  }
}));
const useReorder = (draggedItemId, setDraggedItemId, draggedItemLabel) => {
  const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useDispatch)();
  const order = (0,_hooks_use_classes_order__WEBPACK_IMPORTED_MODULE_6__.useClassesOrder)();
  const reorder = newIds => {
    dispatch(_store__WEBPACK_IMPORTED_MODULE_10__.slice.actions.setOrder(newIds));
    if (draggedItemId) {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_11__.trackGlobalClasses)({
        event: 'classManagerReorder',
        classId: draggedItemId,
        classTitle: draggedItemLabel
      });
      setDraggedItemId(null);
    }
  };
  return [order, reorder];
};
const useFilteredCssClasses = () => {
  const cssClasses = (0,_hooks_use_ordered_classes__WEBPACK_IMPORTED_MODULE_8__.useOrderedClasses)();
  const {
    search: {
      debouncedValue: searchValue
    }
  } = (0,_search_and_filter_context__WEBPACK_IMPORTED_MODULE_12__.useSearchAndFilters)();
  const filters = (0,_hooks_use_filters__WEBPACK_IMPORTED_MODULE_7__.useFilters)();
  const lowercaseLabels = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => cssClasses.map(cssClass => ({
    ...cssClass,
    lowerLabel: cssClass.label.toLowerCase()
  })), [cssClasses]);
  const filteredClasses = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const normalizedSearch = searchValue.replace(/[^a-zA-Z0-9_-]/g, '').toLowerCase();
    if (normalizedSearch.length > 1) {
      return lowercaseLabels.filter(cssClass => cssClass.lowerLabel.includes(normalizedSearch));
    }
    return cssClasses;
  }, [searchValue, cssClasses, lowercaseLabels]);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (filters && filters.length > 0) {
      return filteredClasses.filter(cssClass => filters.includes(cssClass.id));
    }
    return filteredClasses;
  }, [filteredClasses, filters]);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/not-found.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/not-found.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NotFound: function() { return /* binding */ NotFound; },
/* harmony export */   NotFoundLayout: function() { return /* binding */ NotFoundLayout; },
/* harmony export */   getNotFoundType: function() { return /* binding */ getNotFoundType; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _search_and_filter_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../search-and-filter/context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





const getNotFoundType = (searchValue, filters, filteredClasses) => {
  const searchNotFound = filteredClasses.length <= 0 && searchValue.length > 1;
  const filterNotFound = filters && filters.length === 0;
  const filterAndSearchNotFound = searchNotFound && filterNotFound;
  if (filterAndSearchNotFound) {
    return 'filterAndSearch';
  }
  if (searchNotFound) {
    return 'search';
  }
  if (filterNotFound) {
    return 'filter';
  }
  return undefined;
};
const notFound = {
  filterAndSearch: {
    mainText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Sorry, nothing matched.', 'elementor'),
    sceneryText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Try something else.', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PhotoIcon, {
      color: "inherit",
      fontSize: "large"
    })
  },
  search: {
    mainText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Sorry, nothing matched', 'elementor'),
    sceneryText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Clear your input and try something else.', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PhotoIcon, {
      color: "inherit",
      fontSize: "large"
    })
  },
  filter: {
    mainText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Sorry, nothing matched that search.', 'elementor'),
    sceneryText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Clear the filters and try something else.', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ColorSwatchIcon, {
      color: "inherit",
      fontSize: "large"
    })
  }
};
const NotFound = ({
  notFoundType
}) => {
  const {
    search: {
      onClearSearch,
      inputValue
    },
    filters: {
      onClearFilter
    }
  } = (0,_search_and_filter_context__WEBPACK_IMPORTED_MODULE_4__.useSearchAndFilters)();
  switch (notFoundType) {
    case 'filter':
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(NotFoundLayout, _extends({}, notFound.filter, {
        onClear: onClearFilter
      }));
    case 'search':
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(NotFoundLayout, _extends({}, notFound.search, {
        searchValue: inputValue,
        onClear: onClearSearch
      }));
    case 'filterAndSearch':
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(NotFoundLayout, _extends({}, notFound.filterAndSearch, {
        onClear: () => {
          onClearFilter();
          onClearSearch();
        }
      }));
  }
};
const NotFoundLayout = ({
  onClear,
  searchValue,
  mainText,
  sceneryText,
  icon
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
  color: 'text.secondary',
  pt: 5,
  alignItems: "center",
  gap: 1,
  overflow: 'hidden',
  justifySelf: 'center'
}, icon, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
  sx: {
    width: '100%'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
  align: "center",
  variant: "subtitle2",
  color: "inherit"
}, mainText), searchValue && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
  variant: "subtitle2",
  color: "inherit",
  sx: {
    display: 'flex',
    width: '100%',
    justifyContent: 'center'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, "\u201C"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", {
  style: {
    maxWidth: '80%',
    overflow: 'hidden',
    textOverflow: 'ellipsis'
  }
}, searchValue), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, "\u201D."))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
  align: "center",
  variant: "caption",
  color: "inherit"
}, sceneryText), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
  align: "center",
  variant: "caption",
  color: "inherit"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Link, {
  color: "secondary",
  variant: "caption",
  component: "button",
  onClick: onClear
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Clear & try again', 'elementor'))));

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/panel-interactions.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/panel-interactions.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   blockPanelInteractions: function() { return /* binding */ blockPanelInteractions; },
/* harmony export */   unblockPanelInteractions: function() { return /* binding */ unblockPanelInteractions; }
/* harmony export */ });
function blockPanelInteractions() {
  const extendedWindow = window;
  extendedWindow.$e?.components?.get?.('panel')?.blockUserInteractions?.();
}
function unblockPanelInteractions() {
  const extendedWindow = window;
  extendedWindow.$e?.components?.get?.('panel')?.unblockUserInteractions?.();
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/sortable.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/sortable.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SortableItem: function() { return /* binding */ SortableItem; },
/* harmony export */   SortableProvider: function() { return /* binding */ SortableProvider; },
/* harmony export */   SortableTrigger: function() { return /* binding */ SortableTrigger; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const SortableProvider = props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.UnstableSortableProvider, _extends({
  restrictAxis: true,
  variant: "static",
  dragPlaceholderStyle: {
    visibility: 'hidden'
  }
}, props));
const SortableTrigger = props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledSortableTrigger, _extends({}, props, {
  role: "button",
  className: "class-item-sortable-trigger",
  "aria-label": "sort"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.GripVerticalIcon, {
  fontSize: "tiny"
}));
const SortableItem = ({
  children,
  id,
  style,
  ...props
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.UnstableSortableItem, _extends({}, props, {
    id: id,
    render: ({
      itemProps,
      isDragged,
      triggerProps,
      itemStyle,
      triggerStyle,
      dropIndicationStyle,
      showDropIndication,
      isDragOverlay,
      isDragPlaceholder
    }) => {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, _extends({}, itemProps, {
        style: {
          ...itemStyle,
          ...(!isDragOverlay ? style : null)
        },
        component: 'li',
        role: "listitem",
        sx: {
          backgroundColor: isDragOverlay ? 'background.paper' : undefined
        }
      }), children({
        itemProps,
        isDragged,
        triggerProps,
        itemStyle,
        triggerStyle,
        isDragPlaceholder
      }), showDropIndication && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SortableItemIndicator, {
        style: dropIndicationStyle
      }));
    }
  }));
};
const StyledSortableTrigger = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)('div')(({
  theme
}) => ({
  position: 'absolute',
  left: 0,
  top: '50%',
  transform: `translate( -${theme.spacing(1.5)}, -50% )`,
  color: theme.palette.action.active
}));
const SortableItemIndicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)((0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box))`
	width: 100%;
	height: 1px;
	background-color: ${({
  theme
}) => theme.palette.text.primary};
`;

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/class-manager/start-sync-to-v3-modal.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/class-manager/start-sync-to-v3-modal.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StartSyncToV3Modal: function() { return /* binding */ StartSyncToV3Modal; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");





const IMAGE_URL = 'https://assets.elementor.com/packages/v1/images/class-manager-sync-modal.png';
const StartSyncToV3Modal = ({
  externalOpen,
  classId,
  onExternalClose,
  onConfirm
} = {}) => {
  const [shouldShowAgain, setShouldShowAgain] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const hasTrackedExposure = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (externalOpen && classId && !hasTrackedExposure.current) {
      hasTrackedExposure.current = true;
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_3__.trackGlobalClasses)({
        event: 'classSyncToV3PopupShown',
        classId
      });
    }
    if (!externalOpen) {
      hasTrackedExposure.current = false;
    }
  }, [externalOpen, classId]);
  const handleClose = () => {
    if (classId) {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_3__.trackGlobalClasses)({
        event: 'classSyncToV3PopupClick',
        classId,
        action: 'cancel'
      });
    }
    onExternalClose?.();
  };
  const handleConfirm = () => {
    if (classId) {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_3__.trackGlobalClasses)({
        event: 'classSyncToV3PopupClick',
        classId,
        action: 'sync'
      });
    }
    onConfirm?.();
    onExternalClose?.();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Dialog, {
    open: !!externalOpen,
    onClose: handleClose,
    maxWidth: "sm",
    fullWidth: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.DialogContent, {
    sx: {
      p: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    component: "img",
    src: IMAGE_URL,
    alt: "",
    sx: {
      width: '100%',
      display: 'block'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    sx: {
      px: 3,
      pt: 4,
      pb: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "h6"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Sync class to Global Fonts', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    color: "secondary",
    sx: {
      mb: 2,
      pt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Only typography settings supported in Global Fonts will be applied, including: font family, responsive font sizes, weight, text transform, decoration, line height, letter spacing, and word spacing. Changes made in the class will automatically apply to Global Fonts.', 'elementor')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.DialogActions, {
    sx: {
      justifyContent: 'space-between',
      px: 3,
      pb: 2
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.FormControlLabel, {
    control: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
      checked: !shouldShowAgain,
      onChange: e => setShouldShowAgain(!e.target.checked)
    }),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
      variant: "body2",
      color: "secondary"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Don't show again", 'elementor'))
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    sx: {
      display: 'flex',
      gap: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    onClick: handleClose,
    color: "secondary",
    size: "small"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Cancel', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    onClick: handleConfirm,
    variant: "contained",
    size: "small"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Sync to Global Fonts', 'elementor')))));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/convert-local-class-to-global-class.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/convert-local-class-to-global-class.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConvertLocalClassToGlobalClass: function() { return /* binding */ ConvertLocalClassToGlobalClass; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../global-classes-styles-provider */ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");







const ConvertLocalClassToGlobalClass = props => {
  const localStyleData = props.styleDef;
  const handleConversion = () => {
    const newClassName = createClassName(`converted-class-`);
    if (!localStyleData) {
      throw new Error('Style definition is required for converting local class to global class.');
    }
    const newId = _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_5__.globalClassesStylesProvider.actions.create?.(newClassName, localStyleData.variants);
    if (newId) {
      props.successCallback(newId);
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_6__.trackGlobalClasses)({
        classId: newId,
        event: 'classCreated',
        source: 'converted',
        classTitle: newClassName
      });
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
    disabled: !props.canConvert,
    onClick: handleConversion,
    dense: true,
    sx: {
      '&.Mui-focusVisible': {
        border: 'none',
        boxShadow: 'none !important',
        backgroundColor: 'transparent'
      }
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Convert to global class', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null));
};
function createClassName(prefix) {
  let i = 1;
  let newClassName = `${prefix}${i}`;
  while (!(0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.validateStyleLabel)(newClassName, 'create').isValid) {
    newClassName = `${prefix}${++i}`;
  }
  return newClassName;
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-popover.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-popover.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassUsagePopover: function() { return /* binding */ CssClassUsagePopover; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _hooks_use_css_class_usage_by_id__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-css-class-usage-by-id */ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage-by-id.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");








const iconMapper = {
  'wp-post': {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Post', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PostTypeIcon, {
      fontSize: 'inherit'
    })
  },
  'wp-page': {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Page', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PagesIcon, {
      fontSize: 'inherit'
    })
  },
  popup: {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Popup', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PopupTemplateIcon, {
      fontSize: 'inherit'
    })
  },
  header: {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Header', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.HeaderTemplateIcon, {
      fontSize: 'inherit'
    })
  },
  footer: {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Footer', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.FooterTemplateIcon, {
      fontSize: 'inherit'
    })
  }
};
const CssClassUsagePopover = ({
  cssClassID,
  onClose
}) => {
  const {
    data: classUsage
  } = (0,_hooks_use_css_class_usage_by_id__WEBPACK_IMPORTED_MODULE_6__.useCssClassUsageByID)(cssClassID);
  const onNavigate = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useOpenDocumentInNewTab)();
  const cssClassUsageRecords = classUsage?.content.map(({
    title,
    elements,
    pageId,
    type
  }) => ({
    type: 'item',
    value: pageId,
    label: title,
    secondaryText: elements.length.toString(),
    docType: type
  })) ?? [];
  const handleSelect = value => {
    onNavigate(+value);
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackGlobalClasses)({
      event: 'classUsageLocate',
      classId: cssClassID
    });
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.CurrentLocationIcon, {
      fontSize: 'tiny'
    }),
    title: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
      flexDirection: 'row',
      gap: 1,
      alignItems: 'center'
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, {
      "aria-label": 'header-title'
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Locator', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Chip, {
      sx: {
        lineHeight: 1
      },
      size: 'tiny',
      label: classUsage.total
    }))),
    onClose: onClose
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverBody, {
    width: 300
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverMenuList, {
    onSelect: handleSelect,
    items: cssClassUsageRecords,
    onClose: () => {},
    menuListTemplate: StyledCssClassUsageItem,
    menuItemContentTemplate: cssClassUsageRecord => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
      flexDirection: 'row',
      flex: 1,
      alignItems: 'center'
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, {
      display: 'flex',
      sx: {
        pr: 1
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      disableInteractive: true,
      title: iconMapper?.[cssClassUsageRecord.docType]?.label ?? cssClassUsageRecord.docType,
      placement: "top"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Icon, {
      fontSize: 'small'
    }, iconMapper?.[cssClassUsageRecord.docType]?.icon || /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PagesIcon, {
      fontSize: 'inherit'
    })))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, {
      sx: {
        pr: 0.5,
        maxWidth: '173px'
      },
      display: 'flex'
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.EllipsisWithTooltip, {
      title: cssClassUsageRecord.label,
      as: _elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography,
      variant: "caption",
      maxWidth: "173px",
      sx: {
        lineHeight: 1
      }
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ExternalLinkIcon, {
      className: 'hover-only-icon',
      fontSize: 'tiny'
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Chip, {
      sx: {
        ml: 'auto'
      },
      size: 'tiny',
      label: cssClassUsageRecord.secondaryText
    }))
  })));
};
const StyledCssClassUsageItem = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.MenuList)(({
  theme
}) => ({
  '& > li': {
    display: 'flex',
    cursor: 'pointer',
    height: 32,
    width: '100%'
  },
  '& > [role="option"]': {
    ...theme.typography.caption,
    lineHeight: 'inherit',
    padding: theme.spacing(0.5, 1, 0.5, 2),
    textOverflow: 'ellipsis',
    position: 'absolute',
    top: 0,
    left: 0,
    opacity: 1,
    '.hover-only-icon': {
      color: theme.palette.text.disabled,
      opacity: 0
    },
    '&:hover': {
      borderRadius: theme.spacing(0.5),
      backgroundColor: theme.palette.action.hover,
      '.hover-only-icon': {
        color: theme.palette.text.disabled,
        opacity: 1
      }
    }
  },
  width: '100%',
  position: 'relative'
}));

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-trigger.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-trigger.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassUsageTrigger: function() { return /* binding */ CssClassUsageTrigger; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _hooks_use_css_class_usage_by_id__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../hooks/use-css-class-usage-by-id */ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage-by-id.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _css_class_usage_popover__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./css-class-usage-popover */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-popover.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }








const CssClassUsageTrigger = ({
  id,
  onClick
}) => {
  const {
    data: {
      total
    },
    isLoading
  } = (0,_hooks_use_css_class_usage_by_id__WEBPACK_IMPORTED_MODULE_5__.useCssClassUsageByID)(id);
  const cssClassUsagePopover = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.usePopupState)({
    variant: 'popover',
    popupId: 'css-class-usage-popover'
  });
  if (isLoading) {
    return null;
  }
  const WrapperComponent = total !== 0 ? TooltipWrapper : InfoAlertMessage;
  const handleMouseEnter = () => {
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_6__.trackGlobalClasses)({
      event: 'classUsageHovered',
      classId: id,
      usage: total
    });
  };
  const handleClick = e => {
    if (total !== 0) {
      (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindTrigger)(cssClassUsagePopover).onClick(e);
      onClick(id);
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_6__.trackGlobalClasses)({
        event: 'classUsageClicked',
        classId: id
      });
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    position: 'relative',
    onMouseEnter: handleMouseEnter
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(WrapperComponent, {
    total: total
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CustomIconButton, _extends({
    disabled: total === 0,
    size: 'tiny'
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindTrigger)(cssClassUsagePopover), {
    onClick: handleClick
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CurrentLocationIcon, {
    fontSize: 'tiny'
  })))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Popover, _extends({
    anchorOrigin: {
      vertical: 'center',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 15,
      horizontal: -50
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindPopover)(cssClassUsagePopover), {
    onClose: () => {
      (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindPopover)(cssClassUsagePopover).onClose();
      onClick('');
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_class_usage_popover__WEBPACK_IMPORTED_MODULE_7__.CssClassUsagePopover, {
    onClose: cssClassUsagePopover.close,
    "aria-label": "css-class-usage-popover",
    cssClassID: id
  }))));
};
const CustomIconButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.IconButton)(({
  theme
}) => ({
  '&.Mui-disabled': {
    pointerEvents: 'auto',
    // Enable hover
    '&:hover': {
      color: theme.palette.action.disabled // optional
    }
  },
  height: '22px',
  width: '22px'
}));
const TooltipWrapper = ({
  children,
  total
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
  disableInteractive: true,
  placement: 'top',
  title: `${(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Show {{number}} {{locations}}', 'elementor').replace('{{number}}', total.toString()).replace('{{locations}}', total === 1 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('location', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('locations', 'elementor'))}`
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, children));
const InfoAlertMessage = ({
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Infotip, {
  disableInteractive: true,
  placement: 'top',
  color: 'secondary',
  content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.InfoAlert, {
    sx: {
      mt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('This class isn’t being used yet.', 'elementor'))
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, children));

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/index.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/index.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassUsagePopover: function() { return /* reexport safe */ _css_class_usage_popover__WEBPACK_IMPORTED_MODULE_0__.CssClassUsagePopover; },
/* harmony export */   CssClassUsageTrigger: function() { return /* reexport safe */ _css_class_usage_trigger__WEBPACK_IMPORTED_MODULE_1__.CssClassUsageTrigger; }
/* harmony export */ });
/* harmony import */ var _css_class_usage_popover__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./css-class-usage-popover */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-popover.tsx");
/* harmony import */ var _css_class_usage_trigger__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./css-class-usage-trigger */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/components/css-class-usage-trigger.tsx");



/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/types.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/css-class-usage/types.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   QUERY_KEY: function() { return /* binding */ QUERY_KEY; }
/* harmony export */ });
const QUERY_KEY = 'css-classes-usage';

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/utils.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/css-class-usage/utils.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformData: function() { return /* binding */ transformData; }
/* harmony export */ });
const transformData = data => Object.entries(data).reduce((acc, [key, value]) => {
  acc[key] = {
    content: value || [],
    total: value.reduce((total, val) => total + (val?.total || 0), 0)
  };
  return acc;
}, {});

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/global-styles-import-listener.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/global-styles-import-listener.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GlobalStylesImportListener: function() { return /* binding */ GlobalStylesImportListener; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _load_document_classes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../load-document-classes */ "./packages/packages/core/editor-global-classes/src/load-document-classes.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_create_labels_for_classes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/create-labels-for-classes */ "./packages/packages/core/editor-global-classes/src/utils/create-labels-for-classes.ts");






function GlobalStylesImportListener() {
  const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__useDispatch)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleGlobalStylesImported = async event => {
      const customEvent = event;
      const globalClasses = customEvent.detail?.global_classes;
      if (!globalClasses?.added_items_order || !globalClasses?.added_items || globalClasses?.added_items_order?.length === 0) {
        (0,_load_document_classes__WEBPACK_IMPORTED_MODULE_3__.loadCurrentDocumentClasses)();
        return;
      }
      dispatch(_store__WEBPACK_IMPORTED_MODULE_4__.slice.actions.updateAfterTemplateImport({
        addedItems: globalClasses.added_items,
        addedIdsOrder: globalClasses.added_items_order,
        addedClassLabels: (0,_utils_create_labels_for_classes__WEBPACK_IMPORTED_MODULE_5__.createLabelsForClasses)(Object.values(globalClasses.added_items))
      }));
    };
    window.addEventListener(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.GLOBAL_STYLES_IMPORTED_EVENT, handleGlobalStylesImported);
    return () => {
      window.removeEventListener(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.GLOBAL_STYLES_IMPORTED_EVENT, handleGlobalStylesImported);
    };
  }, [dispatch]);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/populate-store.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/populate-store.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopulateStore: function() { return /* binding */ PopulateStore; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _load_document_classes__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../load-document-classes */ "./packages/packages/core/editor-global-classes/src/load-document-classes.ts");



function PopulateStore() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    // TODO - we run it early to have the labels mapping prior to the canvas rendering
    // but in fact we need a way to re-render any dependant twig-templated widgets/elements once we get the initial data
    // in case the canvas rendering has occurred prior to the resolving of this fetch
    (0,_load_document_classes__WEBPACK_IMPORTED_MODULE_2__.loadCurrentDocumentClasses)();
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.registerDataHook)('after', 'editor/documents/attach-preview', async () => {
      await (0,_load_document_classes__WEBPACK_IMPORTED_MODULE_2__.loadCurrentDocumentClasses)();
    });
  }, []);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/active-filters.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/active-filters.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ActiveFilters: function() { return /* binding */ ActiveFilters; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");
/* harmony import */ var _clear_icon_button__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./clear-icon-button */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/clear-icon-button.tsx");
/* harmony import */ var _filter_list__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./filter-list */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/filter-list.tsx");







const ActiveFilters = () => {
  const {
    filters: {
      filters,
      setFilters
    }
  } = (0,_context__WEBPACK_IMPORTED_MODULE_4__.useSearchAndFilters)();
  const handleRemove = key => {
    setFilters(prev => ({
      ...prev,
      [key]: false
    }));
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_3__.trackGlobalClasses)({
      event: 'classManagerFilterUsed',
      action: 'remove',
      type: key,
      trigger: 'header'
    });
  };
  const activeKeys = Object.keys(filters).filter(key => filters[key]);
  const showClearIcon = activeKeys.length > 0;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyContent: "space-between"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    gap: 0.5,
    alignItems: "center",
    flexWrap: "wrap"
  }, activeKeys.map(key => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Chip, {
    key: key,
    label: _filter_list__WEBPACK_IMPORTED_MODULE_6__.filterConfig[key],
    onDelete: () => handleRemove(key),
    sx: chipSx,
    size: "tiny"
  }))), showClearIcon && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_clear_icon_button__WEBPACK_IMPORTED_MODULE_5__.ClearIconButton, {
    trigger: "header",
    tooltipText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Clear Filters', 'elementor'),
    sx: {
      margin: '0 0 auto auto'
    }
  }));
};
const chipSx = {
  '& .MuiChip-deleteIcon': {
    display: 'none',
    transition: 'opacity 0.2s'
  },
  '&:hover .MuiChip-deleteIcon': {
    display: 'block'
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/clear-icon-button.tsx":
/*!*******************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/clear-icon-button.tsx ***!
  \*******************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClearIconButton: function() { return /* binding */ ClearIconButton; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");





const ClearIconButton = ({
  tooltipText,
  sx,
  trigger
}) => {
  const {
    filters: {
      onClearFilter
    }
  } = (0,_context__WEBPACK_IMPORTED_MODULE_4__.useSearchAndFilters)();
  const handleClearFilters = () => {
    onClearFilter(trigger);
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_3__.trackGlobalClasses)({
      event: 'classManagerFilterCleared',
      trigger
    });
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip, {
    title: tooltipText,
    placement: "top",
    disableInteractive: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CustomIconButton, {
    "aria-label": tooltipText,
    size: "tiny",
    onClick: handleClearFilters,
    sx: sx
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.BrushBigIcon, {
    fontSize: "tiny"
  }))));
};
const CustomIconButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton)(({
  theme
}) => ({
  '&.Mui-disabled': {
    pointerEvents: 'auto',
    '&:hover': {
      color: theme.palette.action.disabled
    }
  }
}));

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/css-class-filter.tsx":
/*!******************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/css-class-filter.tsx ***!
  \******************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassFilter: function() { return /* binding */ CssClassFilter; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");
/* harmony import */ var _clear_icon_button__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./clear-icon-button */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/clear-icon-button.tsx");
/* harmony import */ var _filter_list__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./filter-list */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/filter-list.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }










const CssClassFilter = () => {
  const {
    filters: {
      filters
    }
  } = (0,_context__WEBPACK_IMPORTED_MODULE_6__.useSearchAndFilters)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.usePopupState)({
    variant: 'popover',
    disableAutoFocus: true
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (popupState.isOpen) {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_5__.trackGlobalClasses)({
        event: 'classManagerFiltersOpened'
      });
    }
  }, [popupState.isOpen]);
  const showCleanIcon = Object.values(filters).some(value => value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Filters', 'elementor'),
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ToggleButton, _extends({
    value: "filter",
    size: 'tiny',
    selected: popupState.isOpen
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindToggle)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.FilterIcon, {
    fontSize: "tiny"
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Popover, _extends({
    sx: {
      maxWidth: '344px'
    },
    anchorOrigin: {
      vertical: 'top',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: -21
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindPopover)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.PopoverHeader, {
    actions: showCleanIcon ? [/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_clear_icon_button__WEBPACK_IMPORTED_MODULE_7__.ClearIconButton, {
      trigger: "menu",
      key: "clear-all-button",
      tooltipText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Clear all', 'elementor')
    })] : [],
    onClose: popupState.close,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Filters', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.FilterIcon, {
      fontSize: 'tiny'
    })
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, {
    sx: {
      borderWidth: '1px 0 0 0'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.PopoverBody, {
    width: 344,
    height: 125
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_filter_list__WEBPACK_IMPORTED_MODULE_8__.FilterList, null))));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/filter-list.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/filter/filter-list.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FilterList: function() { return /* binding */ FilterList; },
/* harmony export */   filterConfig: function() { return /* binding */ filterConfig; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_filtered_css_class_usage__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../hooks/use-filtered-css-class-usage */ "./packages/packages/core/editor-global-classes/src/hooks/use-filtered-css-class-usage.tsx");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");






const filterConfig = {
  unused: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Unused', 'elementor'),
  empty: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Empty', 'elementor'),
  onThisPage: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('On this page', 'elementor')
};
const FilterList = () => {
  const {
    filters: {
      filters,
      setFilters
    }
  } = (0,_context__WEBPACK_IMPORTED_MODULE_5__.useSearchAndFilters)();
  const filteredCssClass = (0,_hooks_use_filtered_css_class_usage__WEBPACK_IMPORTED_MODULE_3__.useFilteredCssClassUsage)();
  const handleOnClick = value => {
    setFilters(prev => ({
      ...prev,
      [value]: !prev[value]
    }));
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_4__.trackGlobalClasses)({
      event: 'classManagerFilterUsed',
      action: filters[value] ? 'remove' : 'apply',
      type: value,
      trigger: 'menu'
    });
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuList, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuItem, {
    onClick: () => handleOnClick('unused')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LabeledCheckbox, {
    label: filterConfig.unused,
    checked: filters.unused,
    suffix: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Chip, {
      size: 'tiny',
      sx: {
        ml: 'auto'
      },
      label: filteredCssClass.unused.length
    })
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuItem, {
    onClick: () => handleOnClick('empty')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LabeledCheckbox, {
    label: filterConfig.empty,
    checked: filters.empty,
    suffix: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Chip, {
      size: 'tiny',
      sx: {
        ml: 'auto'
      },
      label: filteredCssClass.empty.length
    })
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuItem, {
    onClick: () => handleOnClick('onThisPage')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LabeledCheckbox, {
    label: filterConfig.onThisPage,
    checked: filters.onThisPage,
    suffix: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Chip, {
      size: 'tiny',
      sx: {
        ml: 'auto'
      },
      label: filteredCssClass.onThisPage.length
    })
  })));
};
const LabeledCheckbox = ({
  label,
  suffix,
  checked
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
  direction: "row",
  alignItems: "center",
  gap: 0.5,
  flex: 1
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
  size: 'small',
  checked: checked,
  sx: {
    padding: 0,
    color: 'text.tertiary',
    '&.Mui-checked': {
      color: 'text.tertiary'
    }
  }
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
  variant: "caption",
  sx: {
    color: 'text.secondary'
  }
}, label), suffix);

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/search/class-manager-search.tsx":
/*!**********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/search-and-filter/components/search/class-manager-search.tsx ***!
  \**********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassManagerSearch: function() { return /* binding */ ClassManagerSearch; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");






const ClassManagerSearch = () => {
  const {
    search: {
      inputValue,
      handleChange
    }
  } = (0,_context__WEBPACK_IMPORTED_MODULE_5__.useSearchAndFilters)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    gap: 0.5,
    sx: {
      width: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      flexGrow: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.TextField, {
    role: 'search',
    fullWidth: true,
    size: 'tiny',
    value: inputValue,
    onFocus: () => {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_4__.trackGlobalClasses)({
        event: 'classManagerSearched'
      });
    },
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Search', 'elementor'),
    onChange: e => handleChange(e.target.value),
    InputProps: {
      startAdornment: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.InputAdornment, {
        position: "start"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.SearchIcon, {
        fontSize: 'tiny'
      }))
    }
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SearchAndFilterProvider: function() { return /* binding */ SearchAndFilterProvider; },
/* harmony export */   useSearchAndFilters: function() { return /* binding */ useSearchAndFilters; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);



const SearchAndFilterContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);
const INIT_CHECKED_FILTERS = {
  empty: false,
  onThisPage: false,
  unused: false
};
const SearchAndFilterProvider = ({
  children
}) => {
  const [filters, setFilters] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(INIT_CHECKED_FILTERS);
  const getInitialSearchValue = () => {
    const storedValue = localStorage.getItem('elementor-global-classes-search');
    if (storedValue) {
      localStorage.removeItem('elementor-global-classes-search');
      return storedValue;
    }
    return '';
  };
  const {
    debouncedValue,
    inputValue,
    handleChange
  } = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.useDebounceState)({
    delay: 300,
    initialValue: getInitialSearchValue()
  });
  const onClearSearch = () => {
    handleChange('');
  };
  const onClearFilter = () => {
    setFilters(INIT_CHECKED_FILTERS);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SearchAndFilterContext.Provider, {
    value: {
      search: {
        debouncedValue,
        inputValue,
        handleChange,
        onClearSearch
      },
      filters: {
        filters,
        setFilters,
        onClearFilter
      }
    }
  }, children);
};
const useSearchAndFilters = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(SearchAndFilterContext);
  if (!context) {
    throw new Error('useSearchContext must be used within a SearchContextProvider');
  }
  return context;
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/errors.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/errors.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GlobalClassLabelAlreadyExistsError: function() { return /* binding */ GlobalClassLabelAlreadyExistsError; },
/* harmony export */   GlobalClassNotFoundError: function() { return /* binding */ GlobalClassNotFoundError; },
/* harmony export */   GlobalClassTrackingError: function() { return /* binding */ GlobalClassTrackingError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const GlobalClassNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'global_class_not_found',
  message: 'Global class not found.'
});
const GlobalClassLabelAlreadyExistsError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'global_class_label_already_exists',
  message: 'Class with this name already exists.'
});
const GlobalClassTrackingError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'global_class_tracking_error',
  message: 'Error tracking global classes event.'
});

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GLOBAL_CLASSES_PROVIDER_KEY: function() { return /* binding */ GLOBAL_CLASSES_PROVIDER_KEY; },
/* harmony export */   globalClassesStylesProvider: function() { return /* binding */ globalClassesStylesProvider; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _capabilities__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./capabilities */ "./packages/packages/core/editor-global-classes/src/capabilities.ts");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./errors */ "./packages/packages/core/editor-global-classes/src/errors.ts");
/* harmony import */ var _load_existing_classes__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./load-existing-classes */ "./packages/packages/core/editor-global-classes/src/load-existing-classes.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");









const MAX_CLASSES = 1000;
const GLOBAL_CLASSES_PROVIDER_KEY = 'global-classes';
const PREGENERATED_LINK_PATTERN = /^global-([0-9]+-)?(preview|frontend)-[a-zA-Z_-]+-css$/;
const globalClassesStylesProvider = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.createStylesProvider)({
  key: GLOBAL_CLASSES_PROVIDER_KEY,
  priority: 30,
  limit: MAX_CLASSES,
  isPregeneratedLink: ({
    id
  }) => PREGENERATED_LINK_PATTERN.test(id),
  labels: {
    singular: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('class', 'elementor'),
    plural: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('classes', 'elementor')
  },
  subscribe: cb => subscribeWithStates(cb),
  capabilities: (0,_capabilities__WEBPACK_IMPORTED_MODULE_4__.getCapabilities)(),
  actions: {
    all: () => (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectOrderedClasses)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)()),
    get: id => {
      const state = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)();
      const isFetched = (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectIsClassFetched)(state, id);
      const style = (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectClass)(state, id);

      // the isFetched flag is based on the existence of the style in the initial data
      // so if the style is created during the same session - it won't be stored as part of the initial data
      if (isFetched || style) {
        return style;
      }
      (0,_load_existing_classes__WEBPACK_IMPORTED_MODULE_6__.loadExistingClasses)([id]);
      const label = (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectClassLabels)(state)[id] ?? id;
      return (0,_store__WEBPACK_IMPORTED_MODULE_7__.placeholderDefinition)(id, label);
    },
    resolveCssName: id => {
      const state = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)();
      const loaded = (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectClass)(state, id);
      if (loaded) {
        return loaded.label;
      }
      const fromIndex = (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectClassLabels)(state)[id];
      return fromIndex ?? id;
    },
    create: (label, variants = [], id) => {
      const existingClasses = Object.entries((0,_store__WEBPACK_IMPORTED_MODULE_7__.selectClassLabels)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)()));
      const existingLabels = existingClasses.map(([, classLabel]) => classLabel);
      if (existingLabels.includes(label)) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_5__.GlobalClassLabelAlreadyExistsError({
          context: {
            label
          }
        });
      }
      const existingIds = existingClasses.map(([existingId]) => existingId);
      if (!id) {
        id = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__.generateId)('g-', existingIds);
      }
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_7__.slice.actions.add({
        id,
        type: 'class',
        label,
        variants
      }));
      return id;
    },
    update: payload => {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_7__.slice.actions.update({
        style: payload
      }));
    },
    delete: id => {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_7__.slice.actions.delete(id));
    },
    updateProps: args => {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_7__.slice.actions.updateProps({
        id: args.id,
        meta: args.meta,
        props: args.props,
        mode: args.mode
      }));
    },
    updateCustomCss: args => {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_7__.slice.actions.updateProps({
        id: args.id,
        meta: args.meta,
        custom_css: args.custom_css,
        props: {}
      }));
    },
    tracking: data => {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_8__.trackGlobalClasses)(data).catch(error => {
        throw new _errors__WEBPACK_IMPORTED_MODULE_5__.GlobalClassTrackingError({
          cause: error
        });
      });
    }
  }
});
const subscribeWithStates = cb => {
  let previousState = (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectData)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)());
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__subscribeWithSelector)(state => (0,_store__WEBPACK_IMPORTED_MODULE_7__.selectData)(state), currentState => {
    cb(previousState.items, currentState.items);
    previousState = currentState;
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-classes-order.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-classes-order.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useClassesOrder: function() { return /* binding */ useClassesOrder; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");


const useClassesOrder = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_1__.selectOrder);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage-by-id.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage-by-id.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCssClassUsageByID: function() { return /* binding */ useCssClassUsageByID; }
/* harmony export */ });
/* harmony import */ var _use_css_class_usage__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-css-class-usage */ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage.ts");

const EMPTY_CLASS_USAGE = {
  total: 0,
  content: []
};
const useCssClassUsageByID = id => {
  const {
    data,
    ...rest
  } = (0,_use_css_class_usage__WEBPACK_IMPORTED_MODULE_0__.useCssClassUsage)();
  const classData = data?.[id] ?? EMPTY_CLASS_USAGE;
  return {
    ...rest,
    data: classData
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCssClassUsage: function() { return /* binding */ useCssClassUsage; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../service/css-class-usage-service */ "./packages/packages/core/editor-global-classes/service/css-class-usage-service.ts");
/* harmony import */ var _components_css_class_usage_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/css-class-usage/types */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/types.ts");



const useCssClassUsage = () => {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: [_components_css_class_usage_types__WEBPACK_IMPORTED_MODULE_2__.QUERY_KEY],
    queryFn: _service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_1__.fetchCssClassUsage,
    refetchOnMount: false,
    refetchOnWindowFocus: true
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-dirty-state.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-dirty-state.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useDirtyState: function() { return /* binding */ useDirtyState; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");


const useDirtyState = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_1__.selectIsDirty);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-empty-css-class.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-empty-css-class.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useAllCssClassesIDs: function() { return /* binding */ useAllCssClassesIDs; },
/* harmony export */   useEmptyCssClass: function() { return /* binding */ useEmptyCssClass; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");


const useEmptyCssClass = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_1__.selectEmptyCssClass);
};
const useAllCssClassesIDs = () => {
  const cssClasses = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_1__.selectGlobalClasses);
  return Object.keys(cssClasses);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-filtered-css-class-usage.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-filtered-css-class-usage.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useFilteredCssClassUsage: function() { return /* binding */ useFilteredCssClassUsage; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _use_css_class_usage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-css-class-usage */ "./packages/packages/core/editor-global-classes/src/hooks/use-css-class-usage.ts");
/* harmony import */ var _use_empty_css_class__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-empty-css-class */ "./packages/packages/core/editor-global-classes/src/hooks/use-empty-css-class.ts");




const findCssClassKeysByPageID = (data, pageId) => {
  const result = [];
  for (const key in data) {
    data[key].content.forEach(content => {
      if (+content.pageId === pageId) {
        result.push(key);
      }
    });
  }
  return result;
};
const getUnusedClasses = (usedCssClass, potentialUnused) => {
  const set = new Set(usedCssClass);
  return potentialUnused.filter(cssClass => !set.has(cssClass));
};
const EMPTY_FILTERED_CSS_CLASS_RESPONSE = {
  empty: [],
  onThisPage: [],
  unused: []
};
const useFilteredCssClassUsage = () => {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const emptyCssClasses = (0,_use_empty_css_class__WEBPACK_IMPORTED_MODULE_3__.useEmptyCssClass)();
  const {
    data,
    isLoading
  } = (0,_use_css_class_usage__WEBPACK_IMPORTED_MODULE_2__.useCssClassUsage)();
  const listOfCssClasses = (0,_use_empty_css_class__WEBPACK_IMPORTED_MODULE_3__.useAllCssClassesIDs)();
  const emptyCssClassesIDs = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => emptyCssClasses.map(({
    id
  }) => id), [emptyCssClasses]);
  const onThisPage = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!data || !document) {
      return [];
    }
    return findCssClassKeysByPageID(data, document.id);
  }, [data, document]);
  const unused = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!data) {
      return [];
    }
    return getUnusedClasses(Object.keys(data), listOfCssClasses);
  }, [data, listOfCssClasses]);
  if (isLoading || !data || !document) {
    return EMPTY_FILTERED_CSS_CLASS_RESPONSE;
  }
  return {
    onThisPage,
    unused,
    empty: emptyCssClassesIDs
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-filters.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-filters.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useFilters: function() { return /* binding */ useFilters; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_search_and_filter_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/search-and-filter/context */ "./packages/packages/core/editor-global-classes/src/components/search-and-filter/context.tsx");
/* harmony import */ var _use_filtered_css_class_usage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-filtered-css-class-usage */ "./packages/packages/core/editor-global-classes/src/hooks/use-filtered-css-class-usage.tsx");



const useFilters = () => {
  const {
    filters: {
      filters
    }
  } = (0,_components_search_and_filter_context__WEBPACK_IMPORTED_MODULE_1__.useSearchAndFilters)();
  const allFilters = (0,_use_filtered_css_class_usage__WEBPACK_IMPORTED_MODULE_2__.useFilteredCssClassUsage)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const activeEntries = Object.entries(filters).filter(([, isActive]) => isActive);
    if (activeEntries.length === 0) {
      return null;
    }
    return activeEntries.reduce((acc, [key], index) => {
      const current = allFilters[key] || [];
      if (index === 0) {
        return current;
      }
      return acc.filter(val => current.includes(val));
    }, []);
  }, [filters, allFilters]);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-ordered-classes.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-ordered-classes.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOrderedClasses: function() { return /* binding */ useOrderedClasses; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");


const useOrderedClasses = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_1__.selectOrderedClasses);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/hooks/use-prefetch-css-class-usage.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/hooks/use-prefetch-css-class-usage.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PrefetchCssClassUsage: function() { return /* binding */ PrefetchCssClassUsage; },
/* harmony export */   usePrefetchCssClassUsage: function() { return /* binding */ usePrefetchCssClassUsage; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../service/css-class-usage-service */ "./packages/packages/core/editor-global-classes/service/css-class-usage-service.ts");
/* harmony import */ var _components_css_class_usage_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/css-class-usage/types */ "./packages/packages/core/editor-global-classes/src/components/css-class-usage/types.ts");



function usePrefetchCssClassUsage() {
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQueryClient)();
  const prefetchClassesUsage = () => queryClient.prefetchQuery({
    queryKey: [_components_css_class_usage_types__WEBPACK_IMPORTED_MODULE_2__.QUERY_KEY],
    queryFn: _service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_1__.fetchCssClassUsage
  });
  return {
    prefetchClassesUsage
  };
}
const PrefetchCssClassUsage = () => {
  const {
    prefetchClassesUsage
  } = usePrefetchCssClassUsage();
  prefetchClassesUsage();
  return null;
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/init.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/init.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-embedded-documents-manager */ "@elementor/editor-embedded-documents-manager");
/* harmony import */ var _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _components_class_manager_class_manager_button__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/class-manager/class-manager-button */ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-button.tsx");
/* harmony import */ var _components_convert_local_class_to_global_class__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/convert-local-class-to-global-class */ "./packages/packages/core/editor-global-classes/src/components/convert-local-class-to-global-class.tsx");
/* harmony import */ var _components_global_styles_import_listener__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/global-styles-import-listener */ "./packages/packages/core/editor-global-classes/src/components/global-styles-import-listener.tsx");
/* harmony import */ var _components_populate_store__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/populate-store */ "./packages/packages/core/editor-global-classes/src/components/populate-store.tsx");
/* harmony import */ var _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./global-classes-styles-provider */ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts");
/* harmony import */ var _hooks_use_prefetch_css_class_usage__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./hooks/use-prefetch-css-class-usage */ "./packages/packages/core/editor-global-classes/src/hooks/use-prefetch-css-class-usage.ts");
/* harmony import */ var _load_document_classes__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./load-document-classes */ "./packages/packages/core/editor-global-classes/src/load-document-classes.ts");
/* harmony import */ var _mcp_integration__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./mcp-integration */ "./packages/packages/core/editor-global-classes/src/mcp-integration/index.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _sync_with_document__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./sync-with-document */ "./packages/packages/core/editor-global-classes/src/sync-with-document.tsx");
















function init() {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_5__.__registerSlice)(_store__WEBPACK_IMPORTED_MODULE_14__.slice);
  _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_2__.embeddedDocumentsManager.onDocumentLoad(documentId => {
    void (0,_load_document_classes__WEBPACK_IMPORTED_MODULE_12__.addDocumentClasses)(documentId);
  });
  _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4__.stylesRepository.register(_global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_10__.globalClassesStylesProvider);
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'global-classes-populate-store',
    component: _components_populate_store__WEBPACK_IMPORTED_MODULE_9__.PopulateStore
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'global-classes-sync-with-document',
    component: _sync_with_document__WEBPACK_IMPORTED_MODULE_15__.SyncWithDocumentSave
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'global-classes-import-listener',
    component: _components_global_styles_import_listener__WEBPACK_IMPORTED_MODULE_8__.GlobalStylesImportListener
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'global-classes-prefetch-css-class-usage',
    component: _hooks_use_prefetch_css_class_usage__WEBPACK_IMPORTED_MODULE_11__.PrefetchCssClassUsage
  });
  (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.injectIntoCssClassConvert)({
    id: 'global-classes-convert-from-local-class',
    component: _components_convert_local_class_to_global_class__WEBPACK_IMPORTED_MODULE_7__.ConvertLocalClassToGlobalClass
  });
  (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.injectIntoClassSelectorActions)({
    id: 'global-classes-manager-button',
    component: _components_class_manager_class_manager_button__WEBPACK_IMPORTED_MODULE_6__.ClassManagerButton
  });
  (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.registerStyleProviderToColors)(_global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_10__.GLOBAL_CLASSES_PROVIDER_KEY, {
    name: 'global',
    getThemeColor: theme => theme.palette.global.dark
  });
  (0,_mcp_integration__WEBPACK_IMPORTED_MODULE_13__.initMcpIntegration)((0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_3__.getMCPByDomain)('classes', {
    instructions: 'MCP server for management of Elementor global classes',
    docs: `Everything related to V4 ( Atomic ) global classes.
# Global classes
- Create/update/delete global classes
- Get list of global classes
- Get details of a global class
`
  }), (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_3__.getMCPByDomain)('canvas'));
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/load-document-classes.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/load-document-classes.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addDocumentClasses: function() { return /* binding */ addDocumentClasses; },
/* harmony export */   loadCurrentDocumentClasses: function() { return /* binding */ loadCurrentDocumentClasses; },
/* harmony export */   styleDefinitionsMapWithoutNull: function() { return /* binding */ styleDefinitionsMapWithoutNull; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-global-classes/src/api.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_create_labels_for_classes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/create-labels-for-classes */ "./packages/packages/core/editor-global-classes/src/utils/create-labels-for-classes.ts");





function styleDefinitionsMapWithoutNull(map) {
  return Object.fromEntries(Object.entries(map).filter(entry => entry[1] !== null));
}
function resetGlobalClassesState(globalOrder, classLabels) {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.load({
    preview: {
      items: {},
      order: globalOrder
    },
    frontend: {
      items: {},
      order: globalOrder
    },
    classLabels
  }));
}
async function loadCurrentDocumentClasses() {
  const [previewIndexRes, frontendIndexRes] = await Promise.all([_api__WEBPACK_IMPORTED_MODULE_2__.apiClient.all('preview'), _api__WEBPACK_IMPORTED_MODULE_2__.apiClient.all('frontend')]);
  const previewIndex = previewIndexRes.data.data;
  const frontendIndex = frontendIndexRes.data.data;
  const classLabels = (0,_utils_create_labels_for_classes__WEBPACK_IMPORTED_MODULE_4__.createLabelsForClasses)(previewIndex);
  const previewOrder = previewIndex.map(e => e.id);
  const frontendOrder = frontendIndex.map(e => e.id);

  // This is intended to establish the baseline with current labels and order
  // without it we won't be able to properly resolve the styles' class names
  resetGlobalClassesState(previewOrder, classLabels);
  const postId = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.getCurrentDocument)()?.id;
  if (!postId) {
    return;
  }
  const [previewPostRes, frontendPostRes] = await Promise.all([_api__WEBPACK_IMPORTED_MODULE_2__.apiClient.getStylesForPost(postId, 'preview'), _api__WEBPACK_IMPORTED_MODULE_2__.apiClient.getStylesForPost(postId, 'frontend')]);
  const previewItems = styleDefinitionsMapWithoutNull(previewPostRes.data.data);
  const frontendItems = styleDefinitionsMapWithoutNull(frontendPostRes.data.data);
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.load({
    preview: {
      items: previewItems,
      order: previewOrder
    },
    frontend: {
      items: frontendItems,
      order: frontendOrder
    },
    classLabels
  }));
}
async function addDocumentClasses(documentId) {
  const [previewPostRes, frontendPostRes] = await Promise.all([_api__WEBPACK_IMPORTED_MODULE_2__.apiClient.getStylesForPost(documentId, 'preview'), _api__WEBPACK_IMPORTED_MODULE_2__.apiClient.getStylesForPost(documentId, 'frontend')]);
  const previewItems = styleDefinitionsMapWithoutNull(previewPostRes.data.data);
  const frontendItems = styleDefinitionsMapWithoutNull(frontendPostRes.data.data);
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.mergeExistingClasses({
    preview: previewItems,
    frontend: frontendItems
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/load-existing-classes.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/load-existing-classes.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   loadExistingClasses: function() { return /* binding */ loadExistingClasses; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-global-classes/src/api.ts");
/* harmony import */ var _load_document_classes__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./load-document-classes */ "./packages/packages/core/editor-global-classes/src/load-document-classes.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-global-classes/src/store.ts");




let pendingLoad = null;
const pendingIds = new Set();
async function loadExistingClasses(classIds) {
  const existingClasses = (0,_store__WEBPACK_IMPORTED_MODULE_3__.selectGlobalClasses)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  const missingIds = classIds.filter(id => !(id in existingClasses));
  if (missingIds.length === 0) {
    return;
  }
  missingIds.forEach(id => pendingIds.add(id));
  if (pendingLoad) {
    await pendingLoad;
    return loadExistingClasses(classIds);
  }
  pendingLoad = fetchAndMergeClasses();
  try {
    await pendingLoad;
  } finally {
    pendingLoad = null;
  }
}
async function fetchAndMergeClasses() {
  const idsToFetch = Array.from(pendingIds);
  pendingIds.clear();
  if (idsToFetch.length === 0) {
    return;
  }
  const [previewResponse, frontendResponse] = await Promise.all([_api__WEBPACK_IMPORTED_MODULE_1__.apiClient.getStylesByIds(idsToFetch, 'preview'), _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.getStylesByIds(idsToFetch, 'frontend')]);
  const previewItems = (0,_load_document_classes__WEBPACK_IMPORTED_MODULE_2__.styleDefinitionsMapWithoutNull)(previewResponse.data.data);
  const frontendItems = (0,_load_document_classes__WEBPACK_IMPORTED_MODULE_2__.styleDefinitionsMapWithoutNull)(frontendResponse.data.data);
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.mergeExistingClasses({
    preview: previewItems,
    frontend: frontendItems
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/mcp-integration/apply-global-class-guide-prompt.ts":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/mcp-integration/apply-global-class-guide-prompt.ts ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   APPLY_GLOBAL_CLASS_GUIDE_URI: function() { return /* binding */ APPLY_GLOBAL_CLASS_GUIDE_URI; },
/* harmony export */   generateApplyGlobalClassGuidePrompt: function() { return /* binding */ generateApplyGlobalClassGuidePrompt; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);

const APPLY_GLOBAL_CLASS_GUIDE_URI = 'elementor://global-classes/tools/apply-global-class-guide';
const generateApplyGlobalClassGuidePrompt = () => {
  const prompt = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.toolPrompts)('apply-global-class');
  prompt.description('Apply a global class to an element, enabling consistent styling through your design system.');
  prompt.instruction(`## When to use this tool:
**ALWAYS use this IMMEDIATELY AFTER building compositions** to apply the global classes you created beforehand:
- After using "build-compositions" tool, apply semantic classes to the created elements
- When applying consistent typography styles (heading-primary, text-body, etc.)
- When applying theme colors or brand styles (bg-brand, button-cta, etc.)
- When ensuring spacing consistency (spacing-section-large, etc.)

**DO NOT use this tool** for:
- Elements that don't share styles with other elements (use inline styles instead)
- Layout-specific properties (those should remain inline in stylesConfig)`);
  prompt.instruction(`## Prerequisites:
- **REQUIRED**: Get the list of available global classes from 'elementor://global-classes' resource
- **REQUIRED**: Get element IDs from the composition XML returned by "build-compositions" tool
- Ensure you have the most up-to-date list of classes applied to the element to avoid duplicates
- Make sure you have the correct class ID that you want to apply`);
  prompt.instruction(`## Best Practices:
1. Apply multiple classes to a single element if needed (typography + color + spacing)
2. After applying, the tool will remind you to remove duplicate inline styles from elementConfig
3. Classes should describe purpose, not implementation (e.g., "heading-primary" not "big-red-text")`);
  return prompt.prompt();
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GLOBAL_CLASSES_URI: function() { return /* binding */ GLOBAL_CLASSES_URI; },
/* harmony export */   initClassesResource: function() { return /* binding */ initClassesResource; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../global-classes-styles-provider */ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");



const GLOBAL_CLASSES_URI = 'elementor://global-classes';
const STORAGE_KEY = 'elementor-global-classes';
const updateLocalStorageCache = () => {
  const classes = (0,_store__WEBPACK_IMPORTED_MODULE_2__.selectOrderedClasses)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  localStorage.setItem(STORAGE_KEY, JSON.stringify(classes));
};
const initClassesResource = (classesMcpEntry, canvasMcpEntry) => {
  [canvasMcpEntry, classesMcpEntry].forEach(entry => {
    const {
      sendResourceUpdated,
      resource,
      waitForReady
    } = entry;
    resource('global-classes', GLOBAL_CLASSES_URI, {
      description: 'Global classes list.'
    }, async () => {
      return {
        contents: [{
          uri: GLOBAL_CLASSES_URI,
          text: localStorage[STORAGE_KEY] ?? '[]'
        }]
      };
    });
    waitForReady().then(() => {
      updateLocalStorageCache();
      _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_1__.globalClassesStylesProvider.subscribe(() => {
        updateLocalStorageCache();
        sendResourceUpdated({
          uri: GLOBAL_CLASSES_URI
        });
      });
    });
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/mcp-integration/index.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/mcp-integration/index.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initMcpIntegration: function() { return /* binding */ initMcpIntegration; }
/* harmony export */ });
/* harmony import */ var _classes_resource__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./classes-resource */ "./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts");
/* harmony import */ var _mcp_apply_unapply_global_classes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./mcp-apply-unapply-global-classes */ "./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-apply-unapply-global-classes.ts");
/* harmony import */ var _mcp_get_global_class_usages__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./mcp-get-global-class-usages */ "./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-get-global-class-usages.ts");
/* harmony import */ var _mcp_manage_global_classes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./mcp-manage-global-classes */ "./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-manage-global-classes.ts");




const initMcpIntegration = (reg, canvasMcpEntry) => {
  (0,_mcp_apply_unapply_global_classes__WEBPACK_IMPORTED_MODULE_1__["default"])(reg);
  (0,_mcp_get_global_class_usages__WEBPACK_IMPORTED_MODULE_2__["default"])(reg);
  (0,_mcp_manage_global_classes__WEBPACK_IMPORTED_MODULE_3__.initManageGlobalClasses)(reg);
  (0,_classes_resource__WEBPACK_IMPORTED_MODULE_0__.initClassesResource)(reg, canvasMcpEntry);
};

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-apply-unapply-global-classes.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-apply-unapply-global-classes.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ initMcpApplyUnapplyGlobalClasses; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../global-classes-styles-provider */ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts");
/* harmony import */ var _apply_global_class_guide_prompt__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./apply-global-class-guide-prompt */ "./packages/packages/core/editor-global-classes/src/mcp-integration/apply-global-class-guide-prompt.ts");
/* harmony import */ var _classes_resource__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./classes-resource */ "./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts");





function initMcpApplyUnapplyGlobalClasses(server) {
  const {
    addTool,
    resource
  } = server;
  const applyGlobalClassGuideText = (0,_apply_global_class_guide_prompt__WEBPACK_IMPORTED_MODULE_3__.generateApplyGlobalClassGuidePrompt)();
  resource('apply-global-class-guide', _apply_global_class_guide_prompt__WEBPACK_IMPORTED_MODULE_3__.APPLY_GLOBAL_CLASS_GUIDE_URI, {
    description: 'Workflow, prerequisites, and best practices for apply-global-class',
    mimeType: 'text/plain',
    title: 'Apply global class tool guide'
  }, async uri => ({
    contents: [{
      mimeType: 'text/plain',
      text: applyGlobalClassGuideText,
      uri: uri.href
    }]
  }));
  addTool({
    schema: {
      classId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('The ID of the class to apply'),
      elementId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('The ID of the element to which the class will be applied')
    },
    outputSchema: {
      result: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('Result message indicating the success of the apply operation'),
      llm_instructions: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('Instructions what to do next, Important to follow these instructions!')
    },
    name: 'apply-global-class',
    description: `Apply a global class to an element for shared design-system styling. Read the full guide at [${_apply_global_class_guide_prompt__WEBPACK_IMPORTED_MODULE_3__.APPLY_GLOBAL_CLASS_GUIDE_URI}].`,
    requiredResources: [{
      description: 'Apply global class tool guide',
      uri: _apply_global_class_guide_prompt__WEBPACK_IMPORTED_MODULE_3__.APPLY_GLOBAL_CLASS_GUIDE_URI
    }, {
      description: 'Global classes list',
      uri: _classes_resource__WEBPACK_IMPORTED_MODULE_4__.GLOBAL_CLASSES_URI
    }],
    handler: async params => {
      const {
        classId,
        elementId
      } = params;
      const appliedClasses = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__.doGetAppliedClasses)(elementId);
      (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__.doApplyClasses)(elementId, [...appliedClasses, classId]);
      _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_2__.globalClassesStylesProvider.actions.tracking?.({
        event: 'classApplied',
        executedBy: 'mcp_tool',
        classId
      });
      return {
        llm_instructions: 'Please check the element configuration, find inline styles duplicated by the applied global class, and remove them',
        result: `Class ${classId} applied to element ${elementId} successfully.`
      };
    }
  });
  addTool({
    name: 'unapply-global-class',
    schema: {
      classId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('The ID of the class to unapply'),
      elementId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('The ID of the element from which the class will be unapplied')
    },
    outputSchema: {
      result: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('Result message indicating the success of the unapply operation')
    },
    description: `Unapply a global class from an element by class ID. Resolve class names to IDs via [${_classes_resource__WEBPACK_IMPORTED_MODULE_4__.GLOBAL_CLASSES_URI}].`,
    requiredResources: [{
      description: 'Global classes list',
      uri: _classes_resource__WEBPACK_IMPORTED_MODULE_4__.GLOBAL_CLASSES_URI
    }],
    handler: async params => {
      const {
        classId,
        elementId
      } = params;
      const ok = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__.doUnapplyClass)(elementId, classId);
      if (!ok) {
        throw new Error(`Class ${classId} is not applied to element ${elementId}, cannot unapply it.`);
      }
      return {
        result: `Class ${classId} unapplied from element ${elementId} successfully.`
      };
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-get-global-class-usages.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-get-global-class-usages.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ initMcpApplyGetGlobalClassUsages; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../service/css-class-usage-service */ "./packages/packages/core/editor-global-classes/service/css-class-usage-service.ts");
/* harmony import */ var _classes_resource__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./classes-resource */ "./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts");



function initMcpApplyGetGlobalClassUsages(reg) {
  const {
    addTool
  } = reg;
  const globalClassesUsageSchema = {
    usages: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
      classId: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The ID of the class, not visible to the user. To retrieve the name of the class, use the "list-global-classes" tool'),
      usages: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
        pageId: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The ID of the page where the class is used'),
        title: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The title of the page where the class is used'),
        total: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().describe('The number of times the class is used on this page'),
        elements: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string()).describe('List of element IDs using this class on the page')
      }))
    }))
  };
  addTool({
    name: 'get-global-class-usages',
    description: `Retrieve usages of global classes across all Elementor pages. Heavy operation — scans every page in the site.

## When to use:
- Before deleting or radically changing a class — to understand cross-page side effects and decide whether to consult the user.
- To identify unused global classes for cleanup.

## When NOT to use:
- To list global classes themselves — use the global-classes resource instead (this tool returns usages, not the class list).`,
    requiredResources: [{
      description: 'Global classes list',
      uri: _classes_resource__WEBPACK_IMPORTED_MODULE_2__.GLOBAL_CLASSES_URI
    }],
    outputSchema: globalClassesUsageSchema,
    handler: async () => {
      const data = await (0,_service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_1__.fetchCssClassUsage)();
      const result = {
        usages: []
      };
      Object.entries(data).forEach(([classId, usageDetails]) => {
        const newEntry = {
          classId,
          usages: []
        };
        if (typeof usageDetails !== 'number') {
          const {
            content
          } = usageDetails;
          content.forEach(detail => {
            newEntry.usages.push({
              pageId: String(detail.pageId),
              title: detail.title,
              total: detail.total,
              elements: detail.elements
            });
          });
          result.usages.push(newEntry);
        }
      });
      return result;
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-manage-global-classes.ts":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/mcp-integration/mcp-manage-global-classes.ts ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initManageGlobalClasses: function() { return /* binding */ initManageGlobalClasses; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../global-classes-styles-provider */ "./packages/packages/core/editor-global-classes/src/global-classes-styles-provider.ts");
/* harmony import */ var _load_existing_classes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../load-existing-classes */ "./packages/packages/core/editor-global-classes/src/load-existing-classes.ts");
/* harmony import */ var _save_global_classes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../save-global-classes */ "./packages/packages/core/editor-global-classes/src/save-global-classes.tsx");
/* harmony import */ var _classes_resource__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./classes-resource */ "./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts");







const schema = {
  action: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.enum(['create', 'modify', 'delete']).describe('Operation to perform'),
  classId: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().optional().describe('Global class ID (required for modify). Get from elementor://global-classes resource.'),
  globalClassName: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().optional().describe('Global class name (required for create)'),
  style: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
    default: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('Plaintext CSS for the default state. MUST be non-empty — blank strings are rejected.'),
    hover: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('Plaintext CSS for the :hover state. optional').optional(),
    focus: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('Plaintext CSS for the :focus state. optional').optional(),
    active: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('Plaintext CSS for the :active state. optional').optional()
  }).describe('Plaintext CSS per pseudo-state. All states are converted in one bulk request; unconvertible declarations are stored as custom CSS.'),
  breakpoint: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.nullable(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('Responsive breakpoint name for styles. Defaults to desktop (null).')).default(null).describe('Responsive breakpoint name for styles. Defaults to desktop (null).')
};
const outputSchema = {
  status: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.enum(['ok', 'error']).describe('Operation status'),
  classId: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().optional().describe('Class ID (returned on create success)'),
  message: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().optional().describe('Error details if status is error')
};
const handler = async input => {
  const {
    action,
    classId: rawClassId,
    globalClassName,
    style: rawStyle,
    breakpoint
  } = input;
  let classId = rawClassId;
  if (action === 'create' && !globalClassName) {
    return {
      status: 'error',
      message: 'Create requires globalClassName'
    };
  }
  if (action === 'modify' && !classId) {
    return {
      status: 'error',
      message: 'Modify requires classId'
    };
  }
  if (action === 'delete' && !classId) {
    return {
      status: 'error',
      message: 'Delete requires classId'
    };
  }
  const {
    create,
    update,
    delete: deleteClass
  } = _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_3__.globalClassesStylesProvider.actions;
  if (!create || !update || !deleteClass) {
    return {
      status: 'error',
      message: 'Required actions not available'
    };
  }
  const styleBlocks = collectNonEmptyStyleBlocks(rawStyle);
  if (action !== 'delete' && Object.keys(styleBlocks).length === 0) {
    throw new Error('Style must not be empty. Provide plaintext CSS per state.\n\nExample: style.default = "display: flex; flex-direction: column; gap: 1rem;"');
  }
  let convertedByState = {};
  if (action !== 'delete') {
    const conversionResults = await (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.convertStyleBlocksToAtomic)(styleBlocks);
    convertedByState = Object.fromEntries(Object.entries(conversionResults).map(([state, {
      props,
      customCss
    }]) => [state, {
      props: props,
      customCss: toStoredCustomCss(customCss)
    }]));
  }
  const breakpointValue = breakpoint ?? 'desktop';
  let result = {
    status: 'error',
    classId: '',
    message: 'unknown error'
  };
  try {
    if (action === 'delete') {
      const deleted = await attemptDelete({
        classId,
        stylesProvider: _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_3__.globalClassesStylesProvider
      });
      if (deleted) {
        return {
          status: 'ok',
          message: `deleted global class with ID ${classId}`
        };
      }
      throw new Error('error deleting class');
    }
    let currentAction = action;
    for await (const [state, {
      props,
      customCss
    }] of Object.entries(convertedByState)) {
      switch (currentAction) {
        case 'create':
          const newClassId = await attemptCreate({
            props,
            customCss,
            className: globalClassName,
            stylesProvider: _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_3__.globalClassesStylesProvider,
            breakpoint: breakpointValue,
            state: state
          });
          if (newClassId && currentAction === 'create') {
            currentAction = 'modify';
            classId = newClassId;
            result = {
              status: 'ok',
              message: `created global class with ID ${newClassId}`
            };
            _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_3__.globalClassesStylesProvider.actions.tracking?.({
              event: 'classCreated',
              executedBy: 'mcp_tool',
              classId: newClassId
            });
            (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.dispatchMcpStylesAppliedEvent)({
              styleValue: props
            });
          } else {
            throw new Error('error creating class');
          }
          break;
        case 'modify':
          const updated = await attemptUpdate({
            classId,
            props,
            customCss,
            stylesProvider: _global_classes_styles_provider__WEBPACK_IMPORTED_MODULE_3__.globalClassesStylesProvider,
            breakpoint: breakpointValue,
            state: state
          });
          if (updated) {
            result = {
              status: 'ok',
              classId
            };
            (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.dispatchMcpStylesAppliedEvent)({
              styleValue: props
            });
          } else {
            throw new Error('error modifying class');
          }
          break;
        default:
          throw new Error(`Unsupported action ${action}`);
      }
    }
  } catch (error) {
    return {
      status: 'error',
      message: `${action} failed: ${error.message || 'Unknown error'}`
    };
  }
  return result;
};
const initManageGlobalClasses = reg => {
  const {
    addTool
  } = reg;
  addTool({
    name: 'manage-global-classes',
    requiredResources: [{
      uri: _classes_resource__WEBPACK_IMPORTED_MODULE_6__.GLOBAL_CLASSES_URI,
      description: 'Global classes list'
    }, {
      uri: _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.BREAKPOINTS_SCHEMA_FULL_URI,
      description: 'Breakpoints list'
    }],
    description: `Create or modify global classes for reusable design-system styling. Class names must reflect purpose (e.g. heading-primary, button-cta). Create classes BEFORE applying them. Do NOT create classes for one-off styles.

IMPORTANT: style must contain plaintext CSS rules per state — never pass empty strings.
CSS is converted server-side in one bulk request; any declaration that cannot be converted is stored as the class custom CSS.

Example — creating a flex column class:
style.default = "display: flex; flex-direction: column; gap: 1rem;"

Example — hover state:
style.hover = "opacity: 0.85;"`,
    schema,
    outputSchema,
    handler
  });
};
async function attemptCreate(opts) {
  const {
    props,
    customCss,
    breakpoint,
    className,
    stylesProvider,
    state
  } = opts;
  const {
    create,
    delete: deleteClass
  } = stylesProvider.actions;
  if (!className) {
    throw new Error('Global class name is a required for creation');
  }
  if (!create || !deleteClass) {
    throw new Error('User is unable to create global classes');
  }
  const newClassId = create(className, [{
    meta: {
      breakpoint,
      state: state === 'default' ? null : state
    },
    custom_css: customCss,
    props
  }]);
  try {
    await (0,_save_global_classes__WEBPACK_IMPORTED_MODULE_5__.saveGlobalClasses)({
      context: 'frontend'
    });
    return newClassId;
  } catch {
    deleteClass(newClassId);
    throw new Error('error creating class');
  }
}
async function attemptUpdate(opts) {
  const {
    props,
    customCss,
    breakpoint,
    classId,
    stylesProvider,
    state
  } = opts;
  const {
    updateProps,
    update
  } = stylesProvider.actions;
  if (!classId) {
    throw new Error('Class ID is required for modification');
  }
  if (!updateProps || !update) {
    throw new Error('User is unable to update global classes');
  }
  await (0,_load_existing_classes__WEBPACK_IMPORTED_MODULE_4__.loadExistingClasses)([classId]);
  const snapshot = structuredClone(stylesProvider.actions.all());
  try {
    updateProps({
      id: classId,
      props,
      custom_css: customCss,
      meta: {
        breakpoint,
        state: state === 'default' ? null : state
      }
    });
    await (0,_save_global_classes__WEBPACK_IMPORTED_MODULE_5__.saveGlobalClasses)({
      context: 'frontend'
    });
    return true;
  } catch {
    snapshot.forEach(style => {
      update({
        id: style.id,
        variants: style.variants
      });
    });
    await (0,_save_global_classes__WEBPACK_IMPORTED_MODULE_5__.saveGlobalClasses)({
      context: 'frontend'
    });
    throw new Error('error updating class');
  }
}
async function attemptDelete(opts) {
  const {
    classId,
    stylesProvider
  } = opts;
  const {
    delete: deleteClass,
    create
  } = stylesProvider.actions;
  if (!classId) {
    throw new Error('Class ID is required for deletion');
  }
  if (!deleteClass || !create) {
    throw new Error('User is unable to delete global classes');
  }
  const snapshot = structuredClone(stylesProvider.actions.all());
  const targetClass = snapshot.find(style => style.id === classId);
  if (!targetClass) {
    throw new Error(`Class with ID "${classId}" not found`);
  }
  deleteClass(classId);
  await (0,_save_global_classes__WEBPACK_IMPORTED_MODULE_5__.saveGlobalClasses)({
    context: 'frontend'
  });
  return true;
}
function collectNonEmptyStyleBlocks(style) {
  const blocks = {};
  Object.entries(style).forEach(([state, cssText]) => {
    if (cssText?.trim()) {
      blocks[state] = cssText.trim();
    }
  });
  return blocks;
}
function toStoredCustomCss(customCss) {
  if (!customCss?.trim()) {
    return null;
  }
  return {
    raw: btoa(customCss)
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/save-global-classes.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/save-global-classes.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   saveGlobalClasses: function() { return /* binding */ saveGlobalClasses; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-global-classes/src/api.ts");
/* harmony import */ var _components_class_manager_duplicate_label_dialog__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/class-manager/duplicate-label-dialog */ "./packages/packages/core/editor-global-classes/src/components/class-manager/duplicate-label-dialog.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-global-classes/src/store.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");








async function saveGlobalClasses({
  context,
  onApprove
}) {
  const state = (0,_store__WEBPACK_IMPORTED_MODULE_6__.selectData)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)());
  const apiAction = context === 'preview' ? _api__WEBPACK_IMPORTED_MODULE_4__.apiClient.saveDraft : _api__WEBPACK_IMPORTED_MODULE_4__.apiClient.publish;
  const currentContext = context === 'preview' ? _store__WEBPACK_IMPORTED_MODULE_6__.selectPreviewInitialData : _store__WEBPACK_IMPORTED_MODULE_6__.selectFrontendInitialData;
  const changes = calculateChanges(state, currentContext((0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getState)()));
  const touchedIds = [...changes.added, ...changes.modified];
  const touchedItems = Object.fromEntries(touchedIds.map(id => [id, state.items[id]]).filter(([, v]) => v));
  const response = await apiAction({
    items: touchedItems,
    order: state.order,
    changes
  });
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_6__.slice.actions.reset({
    context
  }));
  window.dispatchEvent(new CustomEvent('classes:updated', {
    detail: {
      context
    }
  }));
  if (response?.data?.data?.code === _api__WEBPACK_IMPORTED_MODULE_4__.API_ERROR_CODES.DUPLICATED_LABEL) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_6__.slice.actions.updateMultiple(response.data.data.modifiedLabels));
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackGlobalClasses)({
      event: 'classPublishConflict',
      numOfConflicts: Object.keys(response.data.data.modifiedLabels).length
    });
    (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.openDialog)({
      component: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_class_manager_duplicate_label_dialog__WEBPACK_IMPORTED_MODULE_5__.DuplicateLabelDialog, {
        modifiedLabels: response.data.data.modifiedLabels || [],
        onApprove: onApprove
      })
    });
  }
}
function calculateChanges(state, initialData) {
  const stateIds = Object.keys(state.items);
  const initialDataIds = Object.keys(initialData.items);
  const {
    order: stateOrder
  } = state;
  const {
    order: initialDataOrder
  } = initialData;
  const stateOrderIdSet = new Set(stateOrder);
  const deleted = initialDataOrder.filter(id => !stateOrderIdSet.has(id));
  const order = stateOrder.join(';') !== initialDataOrder.join(';');
  return {
    added: stateIds.filter(id => !initialDataIds.includes(id)),
    deleted,
    modified: stateIds.filter(id => {
      return id in initialData.items && (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_3__.hash)(state.items[id]) !== (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_3__.hash)(initialData.items[id]);
    }),
    order
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/store.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/store.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   placeholderDefinition: function() { return /* binding */ placeholderDefinition; },
/* harmony export */   selectClass: function() { return /* binding */ selectClass; },
/* harmony export */   selectClassLabels: function() { return /* binding */ selectClassLabels; },
/* harmony export */   selectData: function() { return /* binding */ selectData; },
/* harmony export */   selectEmptyCssClass: function() { return /* binding */ selectEmptyCssClass; },
/* harmony export */   selectFrontendInitialData: function() { return /* binding */ selectFrontendInitialData; },
/* harmony export */   selectGlobalClasses: function() { return /* binding */ selectGlobalClasses; },
/* harmony export */   selectIsClassFetched: function() { return /* binding */ selectIsClassFetched; },
/* harmony export */   selectIsDirty: function() { return /* binding */ selectIsDirty; },
/* harmony export */   selectOrder: function() { return /* binding */ selectOrder; },
/* harmony export */   selectOrderedClasses: function() { return /* binding */ selectOrderedClasses; },
/* harmony export */   selectPreviewInitialData: function() { return /* binding */ selectPreviewInitialData; },
/* harmony export */   slice: function() { return /* binding */ slice; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./errors */ "./packages/packages/core/editor-global-classes/src/errors.ts");
/* harmony import */ var _utils_snapshot_history__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/snapshot-history */ "./packages/packages/core/editor-global-classes/src/utils/snapshot-history.ts");




const localHistory = _utils_snapshot_history__WEBPACK_IMPORTED_MODULE_3__.SnapshotHistory.get('global-classes');
const initialState = {
  data: {
    items: {},
    order: []
  },
  classLabels: {},
  initialData: {
    frontend: {
      items: {},
      order: []
    },
    preview: {
      items: {},
      order: []
    }
  },
  isDirty: false
};
// Slice
const SLICE_NAME = 'globalClasses';
const slice = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__createSlice)({
  name: SLICE_NAME,
  initialState,
  reducers: {
    load(state, {
      payload: {
        frontend,
        preview,
        classLabels
      }
    }) {
      state.initialData.frontend = frontend;
      state.initialData.preview = preview;
      state.data = preview;
      state.classLabels = classLabels;
      state.isDirty = false;
    },
    add(state, {
      payload
    }) {
      localHistory.next(state.data);
      state.data.items[payload.id] = payload;
      state.data.order.unshift(payload.id);
      state.classLabels[payload.id] = payload.label;
      state.isDirty = true;
    },
    delete(state, {
      payload
    }) {
      localHistory.next(state.data);
      state.data.items = Object.fromEntries(Object.entries(state.data.items).filter(([id]) => id !== payload));
      state.data.order = state.data.order.filter(id => id !== payload);
      // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
      delete state.classLabels[payload];
      state.isDirty = true;
    },
    setOrder(state, {
      payload
    }) {
      localHistory.next(state.data);
      state.data.order = payload;
      state.isDirty = true;
    },
    update(state, {
      payload
    }) {
      localHistory.next(state.data);
      const style = state.data.items[payload.style.id];
      const mergedData = {
        ...style,
        ...payload.style
      };
      state.data.items[payload.style.id] = mergedData;
      state.isDirty = true;
    },
    updateMultiple(state, {
      payload
    }) {
      localHistory.next(state.data);
      Object.entries(payload).forEach(([id, {
        modified
      }]) => {
        state.data.items[id].label = modified;
        state.classLabels[id] = modified;
      });
      state.isDirty = false;
    },
    updateProps(state, {
      payload
    }) {
      const style = state.data.items[payload.id];
      if (!style) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_2__.GlobalClassNotFoundError({
          context: {
            styleId: payload.id
          }
        });
      }
      localHistory.next(state.data);
      const variant = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__.getVariantByMeta)(style, payload.meta);
      let customCss = ('custom_css' in payload ? payload.custom_css : variant?.custom_css) ?? null;
      customCss = customCss?.raw ? customCss : null;
      if (variant) {
        const payloadProps = JSON.parse(JSON.stringify(payload.props));
        const mode = payload.mode ?? 'merge';
        if (mode === 'replace') {
          variant.props = payloadProps;
        } else {
          const variantProps = JSON.parse(JSON.stringify(variant.props));
          variant.props = mergeProps(variantProps, payloadProps);
        }
        variant.custom_css = customCss;
        style.variants = getNonEmptyVariants(style);
      } else {
        style.variants.push({
          meta: payload.meta,
          props: payload.props,
          custom_css: customCss
        });
      }
      state.isDirty = true;
    },
    reset(state, {
      payload: {
        context
      }
    }) {
      if (context === 'frontend') {
        localHistory.reset();
        state.initialData.frontend = state.data;
        state.isDirty = false;
      }
      state.initialData.preview = state.data;
    },
    undo(state) {
      if (localHistory.isLast()) {
        localHistory.next(state.data); // store current before undo
      }
      const data = localHistory.prev();
      if (data) {
        state.data = data;
        state.isDirty = true;
      } else {
        state.data = state.initialData.preview;
      }
    },
    resetToInitialState(state, {
      payload: {
        context
      }
    }) {
      localHistory.reset();
      state.data = state.initialData[context];
      state.isDirty = false;
    },
    redo(state) {
      const data = localHistory.next();
      if (localHistory.isLast()) {
        localHistory.prev();
      }
      if (data) {
        state.data = data;
        state.isDirty = true;
      }
    },
    mergeExistingClasses(state, {
      payload: {
        preview,
        frontend
      }
    }) {
      Object.entries(preview).forEach(([id, previewClassData]) => {
        const frontendClassData = frontend[id];
        if (previewClassData === null || previewClassData === undefined) {
          return;
        }
        if (!(id in state.data.items)) {
          state.data.items[id] = previewClassData;
        }
        if (!(id in state.initialData.frontend.items)) {
          state.initialData.frontend.items[id] = frontendClassData;
        }
        if (!(id in state.initialData.preview.items)) {
          state.initialData.preview.items[id] = previewClassData;
        }
        if (!(id in state.classLabels)) {
          state.classLabels[id] = previewClassData.label;
        }
      });
    },
    setOrderWithoutHistory(state, {
      payload
    }) {
      state.data.order = payload;
    },
    updateAfterTemplateImport(state, {
      payload
    }) {
      // Update initial data
      state.initialData.frontend.items = {
        ...state.initialData.frontend.items,
        ...payload.addedItems
      };
      state.initialData.frontend.order = [...state.initialData.frontend.order, ...payload.addedIdsOrder];
      state.initialData.preview.items = {
        ...state.initialData.preview.items,
        ...payload.addedItems
      };
      state.initialData.preview.order = [...state.initialData.preview.order, ...payload.addedIdsOrder];

      // Update current data
      state.data.items = {
        ...state.data.items,
        ...payload.addedItems
      };
      state.data.order = [...state.data.order, ...payload.addedIdsOrder];

      // Update class labels
      state.classLabels = {
        ...state.classLabels,
        ...payload.addedClassLabels
      };
    }
  }
});
const mergeProps = (current, updates) => {
  // edge case, the server returns an array instead of an object when empty props because of PHP array / object conversion
  const props = Array.isArray(current) ? {} : current;
  Object.entries(updates).forEach(([key, value]) => {
    if (value === null || value === undefined) {
      // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
      delete props[key];
    } else {
      props[key] = value;
    }
  });
  return props;
};
const getNonEmptyVariants = style => {
  return style.variants.filter(({
    props,
    custom_css: customCss
  }) => Object.keys(props).length || customCss?.raw);
};
const placeholderDefinition = (id, label) => ({
  id,
  type: 'class',
  label,
  variants: []
});

// Selectors
const selectData = state => state[SLICE_NAME].data;
const selectClassLabels = state => state[SLICE_NAME].classLabels;
const selectFrontendInitialData = state => state[SLICE_NAME].initialData.frontend;
const selectPreviewInitialData = state => state[SLICE_NAME].initialData.preview;
const selectOrder = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__createSelector)(selectData, ({
  order
}) => order);
const selectGlobalClasses = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__createSelector)(selectData, ({
  items
}) => items);
const selectIsDirty = state => state[SLICE_NAME].isDirty;
const selectOrderedClasses = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__createSelector)(selectData, selectClassLabels, ({
  items,
  order
}, classLabels) => order.map(id => {
  const loaded = items[id];
  if (loaded) {
    return loaded;
  }
  const label = classLabels[id];
  return label !== undefined ? placeholderDefinition(id, label) : null;
}).filter(s => s !== null));
const selectClass = (state, id) => state[SLICE_NAME].data.items[id] ?? null;
const selectEmptyCssClass = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__createSelector)(selectData, ({
  items
}) => Object.values(items).filter(cssClass => (cssClass.variants?.length ?? 0) === 0));
const selectIsClassFetched = (state, id) => !!state[SLICE_NAME].initialData.preview.items[id] || !!state[SLICE_NAME].initialData.frontend.items[id] || false;

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/sync-with-document-save.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/sync-with-document-save.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   syncWithDocumentSave: function() { return /* binding */ syncWithDocumentSave; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _capabilities__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./capabilities */ "./packages/packages/core/editor-global-classes/src/capabilities.ts");
/* harmony import */ var _save_global_classes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./save-global-classes */ "./packages/packages/core/editor-global-classes/src/save-global-classes.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-global-classes/src/store.ts");







let pendingSave = null;
function syncWithDocumentSave(panelActions) {
  const unsubscribe = syncDirtyState();
  bindSaveAction(panelActions);
  bindBeforeSaveTemplateAction();
  return unsubscribe;
}
function syncDirtyState() {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__subscribeWithSelector)(_store__WEBPACK_IMPORTED_MODULE_6__.selectIsDirty, () => {
    if (!isDirty()) {
      return;
    }
    (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.setDocumentModifiedStatus)(true);
  });
}
function triggerSave(panelActions, context = 'preview') {
  const user = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__.getCurrentUser)();
  const canEdit = user?.capabilities.includes(_capabilities__WEBPACK_IMPORTED_MODULE_4__.UPDATE_CLASS_CAPABILITY_KEY);
  if (!canEdit) {
    return null;
  }
  if (pendingSave) {
    return pendingSave;
  }
  const promise = (0,_save_global_classes__WEBPACK_IMPORTED_MODULE_5__.saveGlobalClasses)({
    context,
    onApprove: panelActions?.open
  });
  pendingSave = promise;
  promise.finally(() => {
    pendingSave = null;
  });
  return promise;
}
function bindSaveAction(panelActions) {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.registerDataHook)('dependency', 'document/save/save', args => {
    triggerSave(panelActions, args.status === 'publish' ? 'frontend' : 'preview');
    return true;
  });
}
function bindBeforeSaveTemplateAction() {
  window.addEventListener('elementor/global-styles/before-save', event => {
    if (!pendingSave && isDirty()) {
      triggerSave();
    }
    if (pendingSave) {
      event.detail.promises.push(pendingSave);
    }
  });
}
function isDirty() {
  return (0,_store__WEBPACK_IMPORTED_MODULE_6__.selectIsDirty)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__getState)());
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/sync-with-document.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/sync-with-document.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SyncWithDocumentSave: function() { return /* binding */ SyncWithDocumentSave; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _sync_with_document_save__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./sync-with-document-save */ "./packages/packages/core/editor-global-classes/src/sync-with-document-save.ts");



function SyncWithDocumentSave() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const unsubscribe = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.v1ReadyEvent)(), () => {
      const open = () => {
        window.dispatchEvent(new CustomEvent('elementor/open-global-classes-manager'));
      };
      (0,_sync_with_document_save__WEBPACK_IMPORTED_MODULE_2__.syncWithDocumentSave)({
        open
      });
    });
    return unsubscribe;
  }, []);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/utils/create-labels-for-classes.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/utils/create-labels-for-classes.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createLabelsForClasses: function() { return /* binding */ createLabelsForClasses; }
/* harmony export */ });
function createLabelsForClasses(entries) {
  return Object.fromEntries(entries.map(e => [e.id, e.label]));
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/utils/snapshot-history.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/utils/snapshot-history.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SnapshotHistory: function() { return /* binding */ SnapshotHistory; }
/* harmony export */ });
function createLink({
  value,
  next,
  prev
}) {
  return {
    value,
    prev: prev || null,
    next: next || null
  };
}
class SnapshotHistory {
  static registry = {};
  static get(namespace) {
    if (!SnapshotHistory.registry[namespace]) {
      SnapshotHistory.registry[namespace] = new SnapshotHistory(namespace);
    }
    return SnapshotHistory.registry[namespace];
  }
  first = null;
  current = null;
  constructor(namespace) {
    this.namespace = namespace;
  }
  transform(item) {
    return JSON.parse(JSON.stringify(item));
  }
  reset() {
    this.first = this.current = null;
  }
  prev() {
    if (!this.current || this.current === this.first) {
      return null;
    }
    this.current = this.current.prev;
    return this.current?.value || null;
  }
  isLast() {
    return !this.current || !this.current.next;
  }
  next(value) {
    if (value) {
      if (!this.current) {
        this.first = createLink({
          value: this.transform(value)
        });
        this.current = this.first;
        return this.current.value;
      }
      const nextLink = createLink({
        value: this.transform(value),
        prev: this.current
      });
      this.current.next = nextLink;
      this.current = nextLink;
      return this.current.value;
    }

    // No value skip to next without setting any
    if (!this.current || !this.current.next) {
      return null;
    }
    this.current = this.current.next;
    return this.current.value;
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/utils/tracking.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   trackGlobalClasses: function() { return /* binding */ trackGlobalClasses; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../service/css-class-usage-service */ "./packages/packages/core/editor-global-classes/service/css-class-usage-service.ts");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-global-classes/src/errors.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-global-classes/src/store.ts");





const trackGlobalClasses = async payload => {
  const {
    runAction
  } = payload;
  const data = await getSanitizedData(payload);
  if (data) {
    track(data);
    if (data.event === 'classCreated' && 'classId' in data) {
      fireClassApplied(data.classId);
    }
  }
  runAction?.();
};
const fireClassApplied = async classId => {
  const appliedInfo = await getAppliedInfo(classId);
  track({
    event: 'classApplied',
    classId,
    ...appliedInfo,
    totalInstancesAfterApply: 1
  });
};
const getSanitizedData = async payload => {
  switch (payload.event) {
    case 'classApplied':
      if ('classId' in payload && payload.classId) {
        const appliedInfo = await getAppliedInfo(payload.classId);
        return {
          ...payload,
          ...appliedInfo
        };
      }
      break;
    case 'classRemoved':
      if ('classId' in payload && payload.classId) {
        const deleteInfo = getRemovedInfo(payload.classId);
        return {
          ...payload,
          ...deleteInfo
        };
      }
      break;
    case 'classDeleted':
      if ('classId' in payload && payload.classId) {
        const deleteInfo = await trackDeleteClass(payload.classId);
        return {
          ...payload,
          ...deleteInfo
        };
      }
      break;
    case 'classCreated':
      if ('source' in payload && payload.source !== 'created') {
        if ('classId' in payload && payload.classId) {
          return {
            ...payload,
            classTitle: getCssClass(payload.classId).label
          };
        }
      }
      return payload;
    case 'classStateClicked':
      if ('classId' in payload && payload.classId) {
        return {
          ...payload,
          classTitle: getCssClass(payload.classId).label
        };
      }
      break;
    case 'classSyncToV3PopupShown':
      return {
        ...payload,
        interaction_type: 'popup_shown',
        target_type: 'popup',
        target_name: 'sync_to_v3_popup',
        interaction_result: 'popup_viewed',
        target_location: 'widget_panel',
        location_l1: 'class_manager'
      };
    case 'classSyncToV3':
      {
        const classLabel = getCssClass(payload.classId).label;
        const isSync = payload.action === 'sync';
        return {
          ...payload,
          interaction_type: 'click',
          target_type: classLabel,
          target_name: isSync ? 'sync_to_v3' : 'unsync_to_v3',
          interaction_result: isSync ? 'class_is_synced_to_V3' : 'class_is_unsynced_from_V3',
          target_location: 'widget_panel',
          location_l1: 'class_manager',
          interaction_description: isSync ? `user_synced_${classLabel}_to_v3` : `user_unsync_${classLabel}_from_v3`
        };
      }
    case 'classSyncToV3PopupClick':
      {
        const isSyncAction = payload.action === 'sync';
        return {
          ...payload,
          interaction_type: 'click',
          target_type: 'button',
          target_name: isSyncAction ? 'sync_to_v3' : 'cancel',
          interaction_result: isSyncAction ? 'class_is_synced' : 'cancel',
          target_location: 'sync_to_v3_popup'
        };
      }
    default:
      return payload;
  }
};
const track = data => {
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  if (!config?.names?.global_classes?.[data.event]) {
    // eslint-disable-next-line no-console
    console.error('Global class tracking event not found', {
      event: data.event
    });
    return;
  }
  const name = config.names.global_classes[data.event];
  const {
    event,
    ...eventData
  } = data;
  try {
    dispatchEvent?.(name, {
      event,
      ...eventData
    });
  } catch (error) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_3__.GlobalClassTrackingError({
      cause: error
    });
  }
};
const extractCssClassData = classId => {
  const cssClass = getCssClass(classId);
  const classTitle = cssClass.label;
  return {
    classTitle
  };
};
const getCssClass = classId => {
  const state = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__getState)();
  const cssClass = (0,_store__WEBPACK_IMPORTED_MODULE_4__.selectClass)(state, classId);
  if (cssClass) {
    return cssClass;
  }
  const label = (0,_store__WEBPACK_IMPORTED_MODULE_4__.selectClassLabels)(state)[classId];
  if (label !== undefined) {
    return (0,_store__WEBPACK_IMPORTED_MODULE_4__.placeholderDefinition)(classId, label);
  }
  throw new Error(`CSS class with ID ${classId} not found`);
};
const trackDeleteClass = async classId => {
  const classTitle = getCssClass(classId).label;
  const totalInstances = await getTotalInstancesByCssClassID(classId);
  return {
    totalInstances,
    classTitle
  };
};
const getTotalInstancesByCssClassID = async classId => {
  const cssClassUsage = await (0,_service_css_class_usage_service__WEBPACK_IMPORTED_MODULE_2__.fetchCssClassUsage)();
  return cssClassUsage[classId]?.total ?? 1;
};
const getAppliedInfo = async classId => {
  const {
    classTitle
  } = extractCssClassData(classId);
  const totalInstancesAfterApply = (await getTotalInstancesByCssClassID(classId)) + 1;
  return {
    classTitle,
    totalInstancesAfterApply
  };
};
const getRemovedInfo = classId => {
  const {
    classTitle
  } = extractCssClassData(classId);
  return {
    classTitle
  };
};

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-canvas":
/*!***********************************************!*\
  !*** external ["elementorV2","editorCanvas"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCanvas"];

/***/ }),

/***/ "@elementor/editor-current-user":
/*!****************************************************!*\
  !*** external ["elementorV2","editorCurrentUser"] ***!
  \****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCurrentUser"];

/***/ }),

/***/ "@elementor/editor-documents":
/*!**************************************************!*\
  !*** external ["elementorV2","editorDocuments"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorDocuments"];

/***/ }),

/***/ "@elementor/editor-editing-panel":
/*!*****************************************************!*\
  !*** external ["elementorV2","editorEditingPanel"] ***!
  \*****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorEditingPanel"];

/***/ }),

/***/ "@elementor/editor-embedded-documents-manager":
/*!*****************************************************************!*\
  !*** external ["elementorV2","editorEmbeddedDocumentsManager"] ***!
  \*****************************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorEmbeddedDocumentsManager"];

/***/ }),

/***/ "@elementor/editor-mcp":
/*!********************************************!*\
  !*** external ["elementorV2","editorMcp"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorMcp"];

/***/ }),

/***/ "@elementor/editor-panels":
/*!***********************************************!*\
  !*** external ["elementorV2","editorPanels"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorPanels"];

/***/ }),

/***/ "@elementor/editor-styles":
/*!***********************************************!*\
  !*** external ["elementorV2","editorStyles"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorStyles"];

/***/ }),

/***/ "@elementor/editor-styles-repository":
/*!*********************************************************!*\
  !*** external ["elementorV2","editorStylesRepository"] ***!
  \*********************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorStylesRepository"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/events":
/*!*****************************************!*\
  !*** external ["elementorV2","events"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["events"];

/***/ }),

/***/ "@elementor/http-client":
/*!*********************************************!*\
  !*** external ["elementorV2","httpClient"] ***!
  \*********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["httpClient"];

/***/ }),

/***/ "@elementor/icons":
/*!****************************************!*\
  !*** external ["elementorV2","icons"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["icons"];

/***/ }),

/***/ "@elementor/query":
/*!****************************************!*\
  !*** external ["elementorV2","query"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["query"];

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

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

/***/ "@elementor/utils":
/*!****************************************!*\
  !*** external ["elementorV2","utils"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["utils"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

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
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-global-classes/src/index.ts ***!
  \*******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassManagerPanelEmbedded: function() { return /* reexport safe */ _components_class_manager_class_manager_panel__WEBPACK_IMPORTED_MODULE_0__.ClassManagerPanelEmbedded; },
/* harmony export */   GLOBAL_CLASSES_URI: function() { return /* reexport safe */ _mcp_integration_classes_resource__WEBPACK_IMPORTED_MODULE_1__.GLOBAL_CLASSES_URI; },
/* harmony export */   addDocumentClasses: function() { return /* reexport safe */ _load_document_classes__WEBPACK_IMPORTED_MODULE_4__.addDocumentClasses; },
/* harmony export */   createLabelsForClasses: function() { return /* reexport safe */ _utils_create_labels_for_classes__WEBPACK_IMPORTED_MODULE_5__.createLabelsForClasses; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_2__.init; },
/* harmony export */   loadExistingClasses: function() { return /* reexport safe */ _load_existing_classes__WEBPACK_IMPORTED_MODULE_3__.loadExistingClasses; },
/* harmony export */   trackGlobalClasses: function() { return /* reexport safe */ _utils_tracking__WEBPACK_IMPORTED_MODULE_6__.trackGlobalClasses; }
/* harmony export */ });
/* harmony import */ var _components_class_manager_class_manager_panel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/class-manager/class-manager-panel */ "./packages/packages/core/editor-global-classes/src/components/class-manager/class-manager-panel.tsx");
/* harmony import */ var _mcp_integration_classes_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./mcp-integration/classes-resource */ "./packages/packages/core/editor-global-classes/src/mcp-integration/classes-resource.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-global-classes/src/init.ts");
/* harmony import */ var _load_existing_classes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./load-existing-classes */ "./packages/packages/core/editor-global-classes/src/load-existing-classes.ts");
/* harmony import */ var _load_document_classes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./load-document-classes */ "./packages/packages/core/editor-global-classes/src/load-document-classes.ts");
/* harmony import */ var _utils_create_labels_for_classes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/create-labels-for-classes */ "./packages/packages/core/editor-global-classes/src/utils/create-labels-for-classes.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-global-classes/src/utils/tracking.ts");







}();
(window.elementorV2 = window.elementorV2 || {}).editorGlobalClasses = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorGlobalClasses?.init?.();
//# sourceMappingURL=editor-global-classes.js.map
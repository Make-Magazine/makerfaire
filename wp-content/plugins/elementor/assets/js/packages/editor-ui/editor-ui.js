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

/***/ "./packages/packages/libs/editor-ui/src/components/collapse-icon.tsx":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/collapse-icon.tsx ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CollapseIcon: function() { return /* binding */ CollapseIcon; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);



// TODO: Replace this with future Rotate component that will be implemented in elementor-ui
const CollapseIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__.ChevronDownIcon, {
  shouldForwardProp: prop => prop !== 'open' && prop !== 'disabled'
})(({
  theme,
  open,
  disabled = false
}) => ({
  transform: open ? 'rotate(180deg)' : 'rotate(0deg)',
  transition: theme.transitions.create('transform', {
    duration: theme.transitions.duration.standard
  }),
  opacity: disabled ? 0.4 : 1
}));

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/collapsible-content.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/collapsible-content.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CollapsibleContent: function() { return /* binding */ CollapsibleContent; },
/* harmony export */   getCollapsibleValue: function() { return /* binding */ getCollapsibleValue; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _collapse_icon__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./collapse-icon */ "./packages/packages/libs/editor-ui/src/components/collapse-icon.tsx");





const IndicatorsWrapper = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)('div')`
	position: absolute;
	top: 0;
	right: ${({
  theme
}) => theme.spacing(3)};
	height: 100%;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
`;
const CollapsibleContent = ({
  children,
  defaultOpen = false,
  titleEnd = null
}) => {
  const [open, setOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(defaultOpen);
  const handleToggle = () => {
    setOpen(prevOpen => !prevOpen);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    sx: {
      position: 'relative'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    fullWidth: true,
    size: "small",
    color: "secondary",
    variant: "outlined",
    onClick: handleToggle,
    endIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_collapse_icon__WEBPACK_IMPORTED_MODULE_3__.CollapseIcon, {
      open: open
    }),
    sx: {
      my: 0.5
    },
    "aria-label": open ? 'Show less' : 'Show more'
  }, open ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Show less', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Show more', 'elementor')), titleEnd && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(IndicatorsWrapper, null, getCollapsibleValue(titleEnd, open))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Collapse, {
    in: open,
    timeout: "auto",
    unmountOnExit: true
  }, children));
};
function getCollapsibleValue(value, isOpen) {
  if (typeof value === 'function') {
    return value(isOpen);
  }
  return value;
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/confirmation-dialog.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/confirmation-dialog.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConfirmationDialog: function() { return /* binding */ ConfirmationDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





const TITLE_ID = 'confirmation-dialog';
const ConfirmationDialog = ({
  open,
  onClose,
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Dialog, {
  open: open,
  onClose: onClose,
  "aria-labelledby": TITLE_ID,
  maxWidth: "sm"
}, children);
const ConfirmationDialogTitle = ({
  children,
  icon: Icon = _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.AlertOctagonFilledIcon,
  iconColor = 'error'
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogTitle, {
  id: TITLE_ID,
  display: "flex",
  alignItems: "center",
  gap: 1,
  sx: {
    lineHeight: 1
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
  color: iconColor
}), children);
const ConfirmationDialogContent = ({
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogContent, {
  sx: {
    mt: 2
  }
}, children);
const ConfirmationDialogContentText = props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogContentText, _extends({
  variant: "body2",
  color: "secondary"
}, props));
const ConfirmationDialogActions = ({
  onClose,
  onConfirm,
  cancelLabel,
  confirmLabel,
  color = 'error',
  onSuppressMessage,
  suppressLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)("Don't show this again", 'elementor')
}) => {
  const [dontShowAgain, setDontShowAgain] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const handleConfirm = () => {
    if (dontShowAgain && onSuppressMessage) {
      onSuppressMessage();
    }
    onConfirm();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogActions, {
    sx: onSuppressMessage ? {
      justifyContent: 'space-between',
      alignItems: 'center'
    } : undefined
  }, onSuppressMessage && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.FormControlLabel, {
    control: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Checkbox, {
      checked: dontShowAgain,
      onChange: event => setDontShowAgain(event.target.checked),
      size: "medium",
      color: "secondary"
    }),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
      variant: "body2",
      color: "text.secondary"
    }, suppressLabel)
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    color: "secondary",
    onClick: onClose
  }, cancelLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Not now', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    autoFocus: true,
    variant: "contained",
    color: color,
    onClick: handleConfirm,
    sx: {
      ml: 1
    }
  }, confirmLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Delete', 'elementor'))));
};
ConfirmationDialog.Title = ConfirmationDialogTitle;
ConfirmationDialog.Content = ConfirmationDialogContent;
ConfirmationDialog.ContentText = ConfirmationDialogContentText;
ConfirmationDialog.Actions = ConfirmationDialogActions;

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/cta-button.tsx":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/cta-button.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CtaButton: function() { return /* binding */ CtaButton; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const CtaButton = ({
  href,
  children,
  showIcon = true,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, _extends({
  variant: "contained",
  color: "promotion",
  href: href,
  target: "_blank",
  startIcon: showIcon ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon, null) : undefined
}, props), children ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade Now', 'elementor'));

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/editable-field.tsx":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/editable-field.tsx ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditableField: function() { return /* binding */ EditableField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const EditableField = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  value,
  error,
  as = 'span',
  sx,
  ...props
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    title: error,
    open: !!error,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledField, _extends({
    ref: ref,
    component: as
  }, props), value));
});
const StyledField = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)((0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box))`
	width: 100%;
	&:focus {
		outline: none;
	}
`;

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/ellipsis-with-tooltip.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/ellipsis-with-tooltip.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EllipsisWithTooltip: function() { return /* binding */ EllipsisWithTooltip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const EllipsisWithTooltip = ({
  maxWidth,
  title,
  as,
  ...props
}) => {
  const [setRef, isOverflowing] = useIsOverflowing();
  if (isOverflowing) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
      title: title,
      placement: "top"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Content, _extends({
      maxWidth: maxWidth,
      ref: setRef,
      as: as
    }, props), title));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Content, _extends({
    maxWidth: maxWidth,
    ref: setRef,
    as: as
  }, props), title);
};
const Content = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  maxWidth,
  as: Component = _elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box,
  ...props
}, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Component, _extends({
  ref: ref,
  position: "relative"
}, props, {
  style: {
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap',
    maxWidth
  }
})));
const useIsOverflowing = () => {
  const [el, setEl] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [isOverflowing, setIsOverflown] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const observer = new ResizeObserver(([{
      target
    }]) => {
      setIsOverflown(target.scrollWidth > target.clientWidth);
    });
    if (el) {
      observer.observe(el);
    }
    return () => {
      observer.disconnect();
    };
  }, [el]);
  return [setEl, isOverflowing];
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/file-upload/file-upload-dropzone.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/file-upload/file-upload-dropzone.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FileUploadDropzone: function() { return /* binding */ FileUploadDropzone; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);





const cardSx = {
  minHeight: 152,
  border: '2px dashed',
  borderColor: 'divider',
  borderRadius: 1
};
const FileUploadDropzone = ({
  onFileSelected,
  allowedFileTypes,
  accept,
  regionLabel,
  primaryLabel,
  secondaryLabel,
  helperText
}) => {
  const fileInputRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const {
    getDropZoneProps
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useUnstableDropZone)({
    allowedFileTypes,
    onChange: ({
      valid
    }) => {
      if (valid[0]) {
        onFileSelected(valid[0]);
      }
    }
  });
  const dropZoneProps = getDropZoneProps();
  const handleBrowseClick = () => fileInputRef.current?.click();
  const handleFileInputChange = event => {
    const file = event.target.files?.[0];
    if (file) {
      onFileSelected(file);
    }
    event.target.value = '';
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Card, {
    variant: "outlined",
    role: "region",
    "aria-label": regionLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('File dropzone', 'elementor'),
    onDrop: dropZoneProps.onDrop,
    onDragEnter: dropZoneProps.onDragEnter,
    onDragLeave: dropZoneProps.onDragLeave,
    onDragOver: dropZoneProps.onDragOver,
    sx: cardSx
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    spacing: 1,
    padding: 3
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.UploadIcon, {
    fontSize: "medium"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    spacing: 0.5,
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Link, {
    component: "button",
    type: "button",
    underline: "always",
    onClick: handleBrowseClick
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body1",
    component: "span"
  }, primaryLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upload file', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body1"
  }, secondaryLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('or drag and drop', 'elementor'))), helperText ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    color: "text.secondary"
  }, helperText) : null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("input", {
    ref: fileInputRef,
    type: "file",
    accept: accept,
    hidden: true,
    onChange: handleFileInputChange
  }));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/file-upload/file-upload-row.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/file-upload/file-upload-row.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FileUploadRow: function() { return /* binding */ FileUploadRow; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const BYTES_PER_KILOBYTE = 1024;
const cardSx = {
  minHeight: 152,
  border: '2px dashed',
  borderColor: 'divider',
  borderRadius: 1
};
const formatFileSize = sizeInBytes => {
  const kilobytes = Math.max(1, Math.round(sizeInBytes / BYTES_PER_KILOBYTE));
  return `${kilobytes}kb`;
};
const FileUploadRow = ({
  file,
  onRemove,
  statusLabel
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Card, {
    variant: "outlined",
    sx: cardSx
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyContent: "space-between",
    padding: 2,
    spacing: 2,
    minHeight: 152
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "column",
    spacing: 0.5,
    minWidth: 0,
    flex: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle2",
    noWrap: true
  }, file.name), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    color: "text.secondary",
    noWrap: true
  }, formatFileSize(file.size), " \xB7 ", statusLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Complete', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    size: "small",
    onClick: onRemove,
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Remove file', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.XIcon, {
    fontSize: "inherit"
  }))));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/file-upload/index.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/file-upload/index.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FileUploadDropzone: function() { return /* reexport safe */ _file_upload_dropzone__WEBPACK_IMPORTED_MODULE_0__.FileUploadDropzone; },
/* harmony export */   FileUploadRow: function() { return /* reexport safe */ _file_upload_row__WEBPACK_IMPORTED_MODULE_1__.FileUploadRow; }
/* harmony export */ });
/* harmony import */ var _file_upload_dropzone__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./file-upload-dropzone */ "./packages/packages/libs/editor-ui/src/components/file-upload/file-upload-dropzone.tsx");
/* harmony import */ var _file_upload_row__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./file-upload-row */ "./packages/packages/libs/editor-ui/src/components/file-upload/file-upload-row.tsx");



/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/floating-bar.tsx":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/floating-bar.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FloatingActionsBar: function() { return /* binding */ FloatingActionsBar; },
/* harmony export */   useFloatingActionsBar: function() { return /* binding */ useFloatingActionsBar; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);




// CSS hack to hide empty floating bars.
const FloatingBarContainer = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)('span')`
	display: contents;

	.MuiFloatingActionBar-popper:has( .MuiFloatingActionBar-actions:empty ) {
		display: none;
	}

	.MuiFloatingActionBar-popper {
		z-index: 1000;
	}
`;
const FloatingActionsContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function FloatingActionsBar({
  actions,
  children
}) {
  const [open, setOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FloatingActionsContext.Provider, {
    value: {
      open,
      setOpen
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FloatingBarContainer, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.UnstableFloatingActionBar, {
    actions: actions,
    open: open || undefined
  }, children)));
}
function useFloatingActionsBar() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(FloatingActionsContext);
  if (!context) {
    throw new Error('useFloatingActions must be used within a FloatingActionsBar');
  }
  return context;
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/form.tsx":
/*!******************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/form.tsx ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Form: function() { return /* binding */ Form; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const Form = ({
  children,
  onSubmit,
  'data-testid': dataTestId
}) => {
  const formRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const handleSubmit = e => {
    e.preventDefault();
    onSubmit?.();
  };
  const handleKeyDown = e => {
    const {
      target
    } = e;
    if (e.key === 'Enter' && target instanceof HTMLInputElement && target.type !== 'submit') {
      e.preventDefault();
      formRef.current?.requestSubmit();
    }
  };
  return (
    /*#__PURE__*/
    // eslint-disable-next-line jsx-a11y/no-noninteractive-element-interactions
    react__WEBPACK_IMPORTED_MODULE_0__.createElement("form", _extends({
      onSubmit: handleSubmit,
      ref: formRef,
      onKeyDown: handleKeyDown
    }, dataTestId ? {
      'data-testid': dataTestId
    } : {}), children)
  );
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/global-dialog/components/global-dialog.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/global-dialog/components/global-dialog.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GlobalDialog: function() { return /* binding */ GlobalDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _theme_provider__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../theme-provider */ "./packages/packages/libs/editor-ui/src/components/theme-provider.tsx");
/* harmony import */ var _subscribers__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../subscribers */ "./packages/packages/libs/editor-ui/src/components/global-dialog/subscribers.ts");





const GlobalDialog = () => {
  const [content, setContent] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const unsubscribe = (0,_subscribers__WEBPACK_IMPORTED_MODULE_3__.subscribeToDialogState)(setContent);
    return () => {
      unsubscribe();
    };
  }, []);
  if (!content) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_theme_provider__WEBPACK_IMPORTED_MODULE_2__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Dialog, {
    role: "dialog",
    open: true,
    onClose: _subscribers__WEBPACK_IMPORTED_MODULE_3__.closeDialog,
    maxWidth: "sm",
    fullWidth: true
  }, content.component));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/global-dialog/index.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/global-dialog/index.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GlobalDialog: function() { return /* reexport safe */ _components_global_dialog__WEBPACK_IMPORTED_MODULE_0__.GlobalDialog; },
/* harmony export */   closeDialog: function() { return /* reexport safe */ _subscribers__WEBPACK_IMPORTED_MODULE_1__.closeDialog; },
/* harmony export */   openDialog: function() { return /* reexport safe */ _subscribers__WEBPACK_IMPORTED_MODULE_1__.openDialog; }
/* harmony export */ });
/* harmony import */ var _components_global_dialog__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/global-dialog */ "./packages/packages/libs/editor-ui/src/components/global-dialog/components/global-dialog.tsx");
/* harmony import */ var _subscribers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./subscribers */ "./packages/packages/libs/editor-ui/src/components/global-dialog/subscribers.ts");



/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/global-dialog/subscribers.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/global-dialog/subscribers.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   closeDialog: function() { return /* binding */ closeDialog; },
/* harmony export */   openDialog: function() { return /* binding */ openDialog; },
/* harmony export */   subscribeToDialogState: function() { return /* binding */ subscribeToDialogState; }
/* harmony export */ });
let currentDialogState = null;
const stateSubscribers = new Set();
const subscribeToDialogState = callback => {
  stateSubscribers.add(callback);
  callback(currentDialogState);
  return () => stateSubscribers.delete(callback);
};
const notifySubscribers = () => {
  stateSubscribers.forEach(callback => callback(currentDialogState));
};
const openDialog = ({
  component
}) => {
  currentDialogState = {
    component
  };
  notifySubscribers();
};
const closeDialog = () => {
  currentDialogState = null;
  notifySubscribers();
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/info-alert.tsx":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/info-alert.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InfoAlert: function() { return /* binding */ InfoAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const InfoAlert = props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Alert, _extends({
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.InfoCircleFilledIcon, {
    fontSize: "small",
    color: "secondary"
  }),
  variant: 'standard',
  color: "secondary",
  elevation: 0,
  size: "small"
}, props));

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/infotip-card.tsx":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/infotip-card.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InfoTipCard: function() { return /* binding */ InfoTipCard; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const InfoTipCard = ({
  content,
  svgIcon,
  learnMoreButton,
  ctaButton
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Card, {
    elevation: 0,
    sx: {
      width: 320
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardContent, {
    sx: {
      pb: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    display: "flex",
    alignItems: "start"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, {
    fontSize: "tiny",
    sx: {
      mr: 0.5
    }
  }, svgIcon), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2"
  }, content, learnMoreButton && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, "\xA0", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Link, {
    color: "info.main",
    href: learnMoreButton.href,
    target: "_blank"
  }, learnMoreButton.label))))), ctaButton && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardActions, {
    sx: {
      justifyContent: 'flex-start'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    size: "small",
    color: "secondary",
    variant: "contained",
    onClick: ctaButton.onClick,
    sx: {
      marginInlineStart: '1rem'
    }
  }, ctaButton.label)));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/introduction-modal.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/introduction-modal.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   IntroductionModal: function() { return /* binding */ IntroductionModal; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const IntroductionModal = ({
  open,
  handleClose,
  title,
  children
}) => {
  const [shouldShowAgain, setShouldShowAgain] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Dialog, {
    open: open,
    onClose: handleClose,
    maxWidth: 'sm',
    TransitionComponent: Transition
  }, title && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.DialogHeader, {
    logo: false
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.DialogTitle, null, title)), children, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.DialogActions, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.FormControlLabel, {
    sx: {
      marginRight: 'auto'
    },
    control: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
      checked: !shouldShowAgain,
      onChange: () => setShouldShowAgain(!shouldShowAgain)
    }),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
      variant: 'body2'
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Don't show this again", 'elementor'))
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    size: 'medium',
    variant: "contained",
    sx: {
      minWidth: '135px'
    },
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Got it introduction', 'elementor'),
    onClick: () => handleClose(shouldShowAgain)
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Got it', 'elementor'))));
};
const Transition = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Fade, _extends({
  ref: ref
}, props, {
  timeout: {
    enter: 1000,
    exit: 200
  }
})));

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/menu-item.tsx":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/menu-item.tsx ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MenuItemInfotip: function() { return /* binding */ MenuItemInfotip; },
/* harmony export */   MenuListItem: function() { return /* binding */ MenuListItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _info_alert__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./info-alert */ "./packages/packages/libs/editor-ui/src/components/info-alert.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const MenuListItem = ({
  children,
  menuItemTextProps,
  primaryTypographyProps = {
    variant: 'caption'
  },
  ...props
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuItem, _extends({
    dense: true
  }, props, {
    sx: {
      ...(props.sx ?? {})
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuItemText, _extends({
    primary: children,
    primaryTypographyProps: primaryTypographyProps
  }, menuItemTextProps)));
};
const MenuItemInfotip = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  showInfoTip = false,
  children,
  content
}, ref) => {
  if (!showInfoTip) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, children);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Infotip, {
    ref: ref,
    placement: 'right',
    arrow: false,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_info_alert__WEBPACK_IMPORTED_MODULE_2__.InfoAlert, {
      sx: {
        maxWidth: 325
      }
    }, content)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", {
    style: {
      pointerEvents: 'initial',
      width: '100%'
    },
    onClick: e => e.stopPropagation()
  }, children));
});

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/popover/body.tsx":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/popover/body.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopoverBody: function() { return /* binding */ PopoverBody; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const SECTION_PADDING_INLINE = 32;
const DEFAULT_POPOVER_HEIGHT = 348;
const FALLBACK_POPOVER_WIDTH = 220;
const PopoverBody = ({
  children,
  height = DEFAULT_POPOVER_HEIGHT,
  width,
  id
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    display: "flex",
    flexDirection: "column",
    sx: {
      height,
      overflow: 'hidden',
      width: `${width ? width - SECTION_PADDING_INLINE : FALLBACK_POPOVER_WIDTH}px`,
      maxWidth: 496
    },
    id: id
  }, children);
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/popover/header.tsx":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/popover/header.tsx ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopoverHeader: function() { return /* binding */ PopoverHeader; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const SIZE = 'tiny';
const PopoverHeader = ({
  title,
  onClose,
  icon,
  actions
}) => {
  const paddingAndSizing = {
    pl: 2,
    pr: 1,
    py: 1.5,
    maxHeight: 36
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, _extends({
    direction: "row",
    alignItems: "center"
  }, paddingAndSizing, {
    sx: {
      columnGap: 0.5
    }
  }), icon, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "subtitle2",
    sx: {
      fontSize: '12px',
      mt: 0.25
    }
  }, title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    sx: {
      ml: 'auto'
    }
  }, actions, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CloseButton, {
    slotProps: {
      icon: {
        fontSize: SIZE
      }
    },
    sx: {
      ml: 'auto'
    },
    onClick: onClose
  })));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/popover/index.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/popover/index.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ITEM_HEIGHT: function() { return /* reexport safe */ _menu_list__WEBPACK_IMPORTED_MODULE_3__.ITEM_HEIGHT; },
/* harmony export */   PopoverAction: function() { return /* reexport safe */ _popover_action__WEBPACK_IMPORTED_MODULE_4__.PopoverAction; },
/* harmony export */   PopoverBody: function() { return /* reexport safe */ _body__WEBPACK_IMPORTED_MODULE_0__.PopoverBody; },
/* harmony export */   PopoverHeader: function() { return /* reexport safe */ _header__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader; },
/* harmony export */   PopoverMenuList: function() { return /* reexport safe */ _menu_list__WEBPACK_IMPORTED_MODULE_3__.PopoverMenuList; },
/* harmony export */   SectionPopoverBody: function() { return /* reexport safe */ _section_popover_body__WEBPACK_IMPORTED_MODULE_1__.SectionPopoverBody; },
/* harmony export */   StyledMenuList: function() { return /* reexport safe */ _menu_list__WEBPACK_IMPORTED_MODULE_3__.StyledMenuList; }
/* harmony export */ });
/* harmony import */ var _body__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./body */ "./packages/packages/libs/editor-ui/src/components/popover/body.tsx");
/* harmony import */ var _section_popover_body__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./section-popover-body */ "./packages/packages/libs/editor-ui/src/components/popover/section-popover-body.tsx");
/* harmony import */ var _header__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./header */ "./packages/packages/libs/editor-ui/src/components/popover/header.tsx");
/* harmony import */ var _menu_list__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./menu-list */ "./packages/packages/libs/editor-ui/src/components/popover/menu-list.tsx");
/* harmony import */ var _popover_action__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./popover-action */ "./packages/packages/libs/editor-ui/src/components/popover/popover-action.tsx");






/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/popover/menu-list.tsx":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/popover/menu-list.tsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ITEM_HEIGHT: function() { return /* binding */ ITEM_HEIGHT; },
/* harmony export */   PopoverMenuList: function() { return /* binding */ PopoverMenuList; },
/* harmony export */   StyledMenuList: function() { return /* binding */ StyledMenuList; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _tanstack_react_virtual__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @tanstack/react-virtual */ "./packages/node_modules/@tanstack/react-virtual/dist/esm/index.js");
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../hooks */ "./packages/packages/libs/editor-ui/src/hooks/index.ts");





const ITEM_HEIGHT = 32;
const LIST_ITEMS_BUFFER = 6;
const MENU_LIST_PADDING_TOP = 8;
const menuSubHeaderAbsoluteStyling = start => ({
  position: 'absolute',
  transform: `translateY(${start + MENU_LIST_PADDING_TOP}px)`
});
const getAdjacentStickyIndices = (stickyIndices, range) => {
  const previousTwoStickyIndices = stickyIndices.filter(stickyIndex => stickyIndex < range.startIndex).slice(-2);
  const nextTwoStickyIndices = stickyIndices.filter(stickyIndex => stickyIndex > range.endIndex).slice(0, 2);
  return [...previousTwoStickyIndices, ...nextTwoStickyIndices];
};
const PopoverMenuList = ({
  items,
  onSelect,
  onClose,
  selectedValue,
  itemStyle,
  onChange,
  'data-testid': dataTestId,
  menuItemContentTemplate,
  categoryItemContentTemplate,
  noResultsComponent,
  menuListTemplate: CustomMenuList
}) => {
  const containerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const scrollTop = (0,_hooks__WEBPACK_IMPORTED_MODULE_3__.useScrollTop)({
    containerRef
  });
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)();
  const MenuListComponent = CustomMenuList || StyledMenuList;
  const stickyIndices = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => items.reduce((categoryIndices, item, index) => {
    if (item.type === 'category') {
      categoryIndices.push(index);
    }
    return categoryIndices;
  }, []), [items]);
  const getActiveItemIndices = range => {
    const visibleAndStickyIndexes = [];
    for (let i = range.startIndex; i <= range.endIndex; i++) {
      visibleAndStickyIndexes.push(i);
    }
    const stickyIndicesToShow = getAdjacentStickyIndices(stickyIndices, range);
    stickyIndicesToShow.forEach(stickyIndex => {
      if (!visibleAndStickyIndexes.includes(stickyIndex)) {
        visibleAndStickyIndexes.push(stickyIndex);
      }
    });
    return visibleAndStickyIndexes.sort((a, b) => a - b);
  };
  const onChangeCallback = ({
    getVirtualIndexes
  }) => {
    const visibleItems = getVirtualIndexes().map(index => items[index]);
    onChange?.(visibleItems);
  };
  const virtualizer = (0,_tanstack_react_virtual__WEBPACK_IMPORTED_MODULE_2__.useVirtualizer)({
    count: items.length,
    getScrollElement: () => containerRef.current,
    estimateSize: () => ITEM_HEIGHT,
    overscan: LIST_ITEMS_BUFFER,
    rangeExtractor: getActiveItemIndices,
    onChange: onChangeCallback
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    onChangeCallback(virtualizer);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [items]);
  (0,_hooks__WEBPACK_IMPORTED_MODULE_3__.useScrollToSelected)({
    selectedValue,
    items,
    virtualizer
  });
  const virtualItems = virtualizer.getVirtualItems();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    ref: containerRef,
    sx: {
      height: '100%',
      overflowY: 'auto'
    }
  }, items.length === 0 && noResultsComponent ? noResultsComponent : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuListComponent, {
    role: "listbox",
    style: {
      height: `${virtualizer.getTotalSize()}px`
    },
    "data-testid": dataTestId
  }, virtualItems.map(virtualRow => {
    const item = items[virtualRow.index];
    const isLast = virtualRow.index === items.length - 1;
    const isFirst = items[0]?.type === 'category' ? virtualRow.index === 1 : virtualRow.index === 0;
    const isSelected = selectedValue === item.value;
    const tabIndexFallback = !selectedValue ? 0 : -1;
    if (!item) {
      return null;
    }
    if (item.type === 'category') {
      const shouldStick = virtualRow.start + MENU_LIST_PADDING_TOP <= scrollTop;
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuSubheader, {
        key: virtualRow.key,
        style: shouldStick ? {} : menuSubHeaderAbsoluteStyling(virtualRow.start),
        sx: {
          fontWeight: '400',
          color: 'text.tertiary'
        }
      }, categoryItemContentTemplate ? categoryItemContentTemplate(item) : item.label || item.value);
    }
    const isDisabled = item.disabled;
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ListItem, {
      key: virtualRow.key,
      role: "option",
      "aria-selected": isSelected,
      "aria-disabled": isDisabled,
      onClick: isDisabled ? undefined : e => {
        if (e.target.closest('button')) {
          return;
        }
        onSelect(item.value);
        onClose();
      },
      onKeyDown: event => {
        if (event.key === 'Enter' && !isDisabled) {
          onSelect(item.value);
          onClose();
        }
        if (event.key === 'ArrowDown' && isLast) {
          event.preventDefault();
          event.stopPropagation();
        }
        if (event.key === 'ArrowUp' && isFirst) {
          event.preventDefault();
          event.stopPropagation();
        }
      },
      tabIndex: isSelected ? 0 : tabIndexFallback,
      sx: {
        transform: `translateY(${virtualRow.start + MENU_LIST_PADDING_TOP}px)`,
        ...theme.typography.caption,
        ...(itemStyle ? itemStyle(item) : {})
      }
    }, menuItemContentTemplate ? menuItemContentTemplate(item) : item.label || item.value);
  })));
};
const StyledMenuList = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuList)(({
  theme
}) => ({
  '& > li': {
    height: ITEM_HEIGHT,
    width: '100%',
    display: 'flex',
    alignItems: 'center'
  },
  '& > [role="option"]': {
    lineHeight: 'inherit',
    padding: theme.spacing(0.75, 2, 0.75, 4),
    '&:hover, &:focus': {
      backgroundColor: theme.palette.action.hover
    },
    '&[aria-selected="true"]': {
      backgroundColor: theme.palette.action.selected
    },
    '&[aria-disabled="true"]': {
      color: theme.palette.text.disabled
    },
    cursor: 'pointer',
    textOverflow: 'ellipsis',
    position: 'absolute',
    top: 0,
    left: 0
  },
  width: '100%',
  position: 'relative'
}));

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/popover/popover-action.tsx":
/*!************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/popover/popover-action.tsx ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopoverAction: function() { return /* binding */ PopoverAction; },
/* harmony export */   useFloatingActionsPopover: function() { return /* binding */ useFloatingActionsPopover; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _floating_bar__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../floating-bar */ "./packages/packages/libs/editor-ui/src/components/floating-bar.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const SIZE = 'tiny';
function PopoverAction({
  title,
  visible = true,
  icon: Icon,
  content: PopoverContent
}) {
  const {
    popupState,
    triggerProps,
    popoverProps
  } = useFloatingActionsPopover();
  if (!visible) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    placement: "top",
    title: title
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.IconButton, _extends({
    "aria-label": title,
    size: SIZE
  }, triggerProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    fontSize: SIZE
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Popover, _extends({
    disableScrollLock: true,
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'right'
    },
    PaperProps: {
      sx: {
        my: 2.5
      }
    }
  }, popoverProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PopoverContent, {
    close: popupState.close
  })));
}
function useFloatingActionsPopover() {
  const {
    setOpen
  } = (0,_floating_bar__WEBPACK_IMPORTED_MODULE_2__.useFloatingActionsBar)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.usePopupState)({
    variant: 'popover'
  });
  const triggerProps = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.bindTrigger)(popupState);
  const popoverProps = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.bindPopover)(popupState);
  const onClick = e => {
    triggerProps.onClick(e);
    setOpen(true);
  };
  const onClose = () => {
    popoverProps.onClose();
    setOpen(false);
  };
  const close = () => {
    popupState.close();
    setOpen(false);
  };
  return {
    popupState: {
      ...popupState,
      close
    },
    triggerProps: {
      ...triggerProps,
      onClick
    },
    popoverProps: {
      ...popoverProps,
      onClose
    }
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/popover/section-popover-body.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/popover/section-popover-body.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SectionPopoverBody: function() { return /* binding */ SectionPopoverBody; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_section_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../contexts/section-context */ "./packages/packages/libs/editor-ui/src/contexts/section-context.tsx");
/* harmony import */ var _body__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./body */ "./packages/packages/libs/editor-ui/src/components/popover/body.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const SectionPopoverBody = props => {
  const sectionWidth = (0,_contexts_section_context__WEBPACK_IMPORTED_MODULE_1__.useSectionWidth)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_body__WEBPACK_IMPORTED_MODULE_2__.PopoverBody, _extends({}, props, {
    width: sectionWidth
  }));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-alert.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/promotions/promotion-alert.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PromotionAlert: function() { return /* binding */ PromotionAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const PromotionAlert = ({
  message,
  upgradeUrl,
  onCtaClick
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Alert, {
  variant: "standard",
  color: "promotion",
  icon: false,
  role: "dialog",
  "aria-label": "promotion-alert",
  size: "small",
  sx: {
    m: 2,
    mt: 1,
    pt: 0.5,
    pb: 0.5
  }
}, message, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
  size: 'tiny',
  variant: 'text',
  color: 'promotion',
  target: "_blank",
  href: upgradeUrl,
  rel: "noopener noreferrer",
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon, {
    fontSize: "tiny"
  }),
  onClick: onCtaClick
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade now', 'elementor')));

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-chip.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/promotions/promotion-chip.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PromotionChip: function() { return /* binding */ PromotionChip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const PromotionChip = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  ...props
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Chip, _extends({
    "aria-label": "Promotion chip",
    ref: ref,
    size: "tiny",
    color: "promotion",
    variant: "standard",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon, null),
    sx: {
      ml: 1,
      width: '20px',
      '& .MuiChip-label': {
        display: 'none'
      }
    }
  }, props));
});

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-infotip.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/promotions/promotion-infotip.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PromotionInfotip: function() { return /* binding */ PromotionInfotip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../hooks */ "./packages/packages/libs/editor-ui/src/hooks/index.ts");
/* harmony import */ var _cta_button__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../cta-button */ "./packages/packages/libs/editor-ui/src/components/cta-button.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const PromotionInfotip = ({
  children,
  open,
  onClose,
  onCtaClick,
  ...cardProps
}) => {
  (0,_hooks__WEBPACK_IMPORTED_MODULE_2__.useCanvasClickHandler)(!!open, onClose);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Infotip, {
    placement: "right",
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InfotipCard, _extends({
      onClose: onClose,
      onCtaClick: onCtaClick
    }, cardProps)),
    open: open
  }, children);
};
function InfotipCard({
  title,
  content,
  assetUrl,
  ctaUrl,
  onClose,
  onCtaClick
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ClickAwayListener, {
    disableReactTree: true,
    mouseEvent: "onMouseDown",
    touchEvent: "onTouchStart",
    onClickAway: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Card, {
    elevation: 0,
    sx: {
      maxWidth: 296
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardHeader, {
    title: title,
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CloseButton, {
      slotProps: {
        icon: {
          fontSize: 'tiny'
        }
      },
      onClick: onClose
    })
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardMedia, {
    component: "img",
    image: assetUrl,
    alt: "",
    sx: {
      width: '100%',
      aspectRatio: '16 / 9'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, content)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardActions, {
    sx: {
      justifyContent: 'flex-start'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_cta_button__WEBPACK_IMPORTED_MODULE_3__.CtaButton, {
    href: ctaUrl,
    onClick: onCtaClick
  }))));
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-popover.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/promotions/promotion-popover.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PromotionPopover: function() { return /* binding */ PromotionPopover; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);



const PromotionPopover = ({
  children,
  open,
  placement = 'right',
  slotProps,
  anchorRef,
  ...cardProps
}) => {
  const anchorEl = anchorRef?.current;
  const defaultSlotProps = {
    popper: {
      ...(anchorEl && {
        anchorEl
      }),
      modifiers: [{
        name: 'offset',
        options: {
          offset: anchorRef ? [0, 4] : [0, 10]
        }
      }]
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Infotip, {
    placement: placement,
    arrow: false,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PopoverAlert, cardProps),
    open: open,
    slotProps: slotProps || defaultSlotProps
  }, children);
};
function PopoverAlert({
  title,
  content,
  ctaUrl,
  ctaText,
  onClose,
  onCtaClick
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ClickAwayListener, {
    disableReactTree: true,
    mouseEvent: "onMouseDown",
    touchEvent: "onTouchStart",
    onClickAway: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Alert, {
    variant: "standard",
    color: "promotion",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon, {
      fontSize: "tiny"
    }),
    onClose: onClose,
    onMouseDown: e => e.stopPropagation(),
    role: "dialog",
    "aria-label": "promotion-popover-title",
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertAction, {
      variant: "contained",
      color: "promotion",
      href: ctaUrl,
      target: "_blank",
      rel: "noopener noreferrer",
      onClick: onCtaClick
    }, ctaText),
    sx: {
      maxWidth: 296
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      gap: 0.5,
      display: 'flex',
      flexDirection: 'column'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertTitle, null, title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2"
  }, content))));
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/save-changes-dialog.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/save-changes-dialog.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SaveChangesDialog: function() { return /* binding */ SaveChangesDialog; },
/* harmony export */   useDialog: function() { return /* binding */ useDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const TITLE_ID = 'save-changes-dialog';
const SaveChangesDialog = ({
  children,
  onClose
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Dialog, {
  open: true,
  onClose: onClose,
  "aria-labelledby": TITLE_ID,
  maxWidth: "xs"
}, children);
const SaveChangesDialogTitle = ({
  children,
  onClose
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogTitle, {
  id: TITLE_ID,
  display: "flex",
  alignItems: "center",
  gap: 1,
  sx: {
    lineHeight: 1,
    justifyContent: 'space-between'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
  direction: "row",
  alignItems: "center",
  gap: 1
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.AlertTriangleFilledIcon, {
  color: "secondary"
}), children), onClose && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
  onClick: onClose,
  size: "small"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.XIcon, null)));
const SaveChangesDialogContent = ({
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogContent, null, children);
const SaveChangesDialogContentText = props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogContentText, _extends({
  variant: "body2",
  color: "textPrimary",
  display: "flex",
  flexDirection: "column"
}, props));
const SaveChangesDialogActions = ({
  actions
}) => {
  const [isConfirming, setIsConfirming] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const {
    cancel,
    confirm,
    discard
  } = actions;
  const onConfirm = async () => {
    setIsConfirming(true);
    await confirm.action();
    setIsConfirming(false);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogActions, null, cancel && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "text",
    color: "secondary",
    onClick: cancel.action
  }, cancel.label), discard && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "text",
    color: "secondary",
    onClick: discard.action
  }, discard.label), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "contained",
    color: "secondary",
    onClick: onConfirm,
    loading: isConfirming
  }, confirm.label));
};
SaveChangesDialog.Title = SaveChangesDialogTitle;
SaveChangesDialog.Content = SaveChangesDialogContent;
SaveChangesDialog.ContentText = SaveChangesDialogContentText;
SaveChangesDialog.Actions = SaveChangesDialogActions;
const useDialog = () => {
  const [isOpen, setIsOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const open = () => setIsOpen(true);
  const close = () => setIsOpen(false);
  return {
    isOpen,
    open,
    close
  };
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/search-field.tsx":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/search-field.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SearchField: function() { return /* binding */ SearchField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);





const SIZE = 'tiny';
const SearchField = ({
  value,
  onSearch,
  placeholder,
  id,
  sx
}) => {
  const inputRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const handleClear = () => {
    onSearch('');
    inputRef.current?.focus();
  };
  const handleInputChange = event => {
    onSearch(event.target.value);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      px: 2,
      pb: 1.5,
      ...sx
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.TextField
  // eslint-disable-next-line jsx-a11y/no-autofocus
  , {
    autoFocus: true,
    fullWidth: true,
    id: id,
    size: SIZE,
    value: value,
    inputRef: inputRef,
    onChange: handleInputChange,
    placeholder: placeholder,
    InputProps: {
      startAdornment: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.InputAdornment, {
        position: "start"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.SearchIcon, {
        fontSize: SIZE
      })),
      endAdornment: value && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
        size: SIZE,
        onClick: handleClear,
        "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Clear', 'elementor')
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.XIcon, {
        color: "action",
        fontSize: SIZE
      }))
    }
  }));
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/theme-provider.tsx":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/theme-provider.tsx ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ThemeProvider; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _hooks_use_color_scheme__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../hooks/use-color-scheme */ "./packages/packages/libs/editor-ui/src/hooks/use-color-scheme.ts");



const EDITOR_PALETTE = 'unstable';
function ThemeProvider({
  children
}) {
  const colorScheme = (0,_hooks_use_color_scheme__WEBPACK_IMPORTED_MODULE_2__.useColorScheme)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ThemeProvider, {
    colorScheme: colorScheme,
    palette: EDITOR_PALETTE
  }, children);
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/components/warning-infotip.tsx":
/*!*****************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/components/warning-infotip.tsx ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   WarningInfotip: function() { return /* binding */ WarningInfotip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);



const WarningInfotip = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  children,
  open,
  title,
  text,
  placement,
  width,
  offset,
  hasError = true
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Infotip, {
    ref: ref,
    open: open,
    placement: placement,
    PopperProps: {
      sx: {
        width: width ? width : 'initial',
        '.MuiTooltip-tooltip': {
          marginLeft: 0,
          marginRight: 0
        }
      },
      modifiers: offset ? [{
        name: 'offset',
        options: {
          offset
        }
      }] : []
    },
    arrow: false,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Alert, {
      color: hasError ? 'error' : 'secondary',
      severity: "warning",
      variant: "standard",
      size: "small"
    }, title ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertTitle, null, title) : null, text)
  }, children);
});

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/contexts/section-context.tsx":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/contexts/section-context.tsx ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SectionRefContext: function() { return /* binding */ SectionRefContext; },
/* harmony export */   useSectionWidth: function() { return /* binding */ useSectionWidth; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const FALLBACK_SECTION_WIDTH = 320;
const SectionRefContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const useSectionRef = () => (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(SectionRefContext);
const useSectionWidth = () => {
  const sectionRef = useSectionRef();
  return sectionRef?.current?.offsetWidth ?? FALLBACK_SECTION_WIDTH;
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/index.ts":
/*!*************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/index.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCanvasClickHandler: function() { return /* reexport safe */ _use_canvas_click_handler__WEBPACK_IMPORTED_MODULE_3__.useCanvasClickHandler; },
/* harmony export */   useScrollToSelected: function() { return /* reexport safe */ _use_scroll_to_selected__WEBPACK_IMPORTED_MODULE_0__.useScrollToSelected; },
/* harmony export */   useScrollTop: function() { return /* reexport safe */ _use_scroll_top__WEBPACK_IMPORTED_MODULE_1__.useScrollTop; },
/* harmony export */   useTextFieldAutoSelect: function() { return /* reexport safe */ _use_text_field_auto_select__WEBPACK_IMPORTED_MODULE_2__.useTextFieldAutoSelect; }
/* harmony export */ });
/* harmony import */ var _use_scroll_to_selected__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-scroll-to-selected */ "./packages/packages/libs/editor-ui/src/hooks/use-scroll-to-selected.ts");
/* harmony import */ var _use_scroll_top__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-scroll-top */ "./packages/packages/libs/editor-ui/src/hooks/use-scroll-top.ts");
/* harmony import */ var _use_text_field_auto_select__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-text-field-auto-select */ "./packages/packages/libs/editor-ui/src/hooks/use-text-field-auto-select.ts");
/* harmony import */ var _use_canvas_click_handler__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-canvas-click-handler */ "./packages/packages/libs/editor-ui/src/hooks/use-canvas-click-handler.tsx");





/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/use-canvas-click-handler.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/use-canvas-click-handler.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCanvasClickHandler: function() { return /* binding */ useCanvasClickHandler; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);


const useCanvasClickHandler = (isActive, onClickAway) => {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const canvasDocument = isActive ? (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.getCanvasIframeDocument)() : null;
    if (!canvasDocument) {
      return;
    }
    canvasDocument.addEventListener('mousedown', onClickAway);
    return () => canvasDocument.removeEventListener('mousedown', onClickAway);
  }, [isActive, onClickAway]);
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/use-color-scheme.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/use-color-scheme.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useColorScheme: function() { return /* binding */ useColorScheme; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);


function useColorScheme() {
  const [colorScheme, setColorScheme] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => getV1ColorScheme());
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.v1ReadyEvent)(), () => setColorScheme(getV1ColorScheme()));
  }, []);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/settings'), e => {
      const event = e;

      // The User-Preferences settings object has a key named `ui_theme` that controls the color scheme.
      const isColorScheme = event.args?.settings && 'ui_theme' in event.args.settings;
      if (isColorScheme) {
        setColorScheme(getV1ColorScheme());
      }
    });
  }, []);
  return colorScheme;
}
function getV1ColorScheme() {
  return window.elementor?.getPreferences?.('ui_theme') || 'auto';
}

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/use-editable.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/use-editable.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useEditable: function() { return /* binding */ useEditable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const useEditable = ({
  value,
  onSubmit,
  validation,
  onClick,
  onError
}) => {
  const [isEditing, setIsEditing] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [error, setError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const ref = useSelection(isEditing);
  const isDirty = newValue => newValue !== value;
  const openEditMode = () => {
    setIsEditing(true);
  };
  const closeEditMode = () => {
    setError(null);
    onError?.(null);
    setIsEditing(false);
  };
  const submit = newValue => {
    if (!isDirty(newValue)) {
      closeEditMode();
      return;
    }
    if (!error) {
      try {
        onSubmit(newValue);
      } finally {
        closeEditMode();
      }
    }
  };
  const onChange = event => {
    const {
      innerText: newValue
    } = event.target;
    if (validation) {
      const updatedError = isDirty(newValue) ? validation(newValue) : null;
      setError(updatedError);
      onError?.(updatedError);
    }
  };
  const handleKeyDown = event => {
    event.stopPropagation();
    if (['Escape'].includes(event.key)) {
      return closeEditMode();
    }
    if (['Enter'].includes(event.key)) {
      event.preventDefault();
      // submission is invoked only on blur, to avoid issues with double-submission in certain cases
      if (!error) {
        ref.current?.blur();
      }
    }
  };
  const handleClick = event => {
    if (isEditing) {
      event.stopPropagation();
    }
    onClick?.(event);
  };
  const handleBlur = () => {
    if (error) {
      closeEditMode();
      return;
    }
    submit(ref.current.innerText);
  };
  const listeners = {
    onClick: handleClick,
    onKeyDown: handleKeyDown,
    onInput: onChange,
    onBlur: handleBlur
  };
  const attributes = {
    value,
    role: 'textbox',
    contentEditable: isEditing,
    ...(isEditing && {
      suppressContentEditableWarning: true
    })
  };
  return {
    ref,
    isEditing,
    openEditMode,
    closeEditMode,
    value,
    error,
    getProps: () => ({
      ...listeners,
      ...attributes
    })
  };
};
const useSelection = isEditing => {
  const ref = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (isEditing) {
      selectAll(ref.current);
    }
  }, [isEditing]);
  return ref;
};
const selectAll = el => {
  const selection = getSelection();
  if (!selection || !el) {
    return;
  }
  const range = document.createRange();
  range.selectNodeContents(el);
  selection.removeAllRanges();
  selection.addRange(range);
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/use-scroll-to-selected.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/use-scroll-to-selected.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useScrollToSelected: function() { return /* binding */ useScrollToSelected; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const useScrollToSelected = ({
  selectedValue,
  items,
  virtualizer
}) => {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!selectedValue || items.length === 0) {
      return;
    }
    const selectedIndex = items.findIndex(item => item.value === selectedValue);
    if (selectedIndex !== -1) {
      virtualizer.scrollToIndex(selectedIndex, {
        align: 'center'
      });
    }
  }, [selectedValue, items, virtualizer]);
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/use-scroll-top.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/use-scroll-top.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useScrollTop: function() { return /* binding */ useScrollTop; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const useScrollTop = ({
  containerRef
}) => {
  const [scrollTop, setScrollTop] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const container = containerRef.current;
    if (!container) {
      return;
    }
    const handleScroll = () => {
      setScrollTop(container.scrollTop);
    };
    container.addEventListener('scroll', handleScroll);
    return () => container.removeEventListener('scroll', handleScroll);
  }, [containerRef]);
  return scrollTop;
};

/***/ }),

/***/ "./packages/packages/libs/editor-ui/src/hooks/use-text-field-auto-select.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/hooks/use-text-field-auto-select.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useTextFieldAutoSelect: function() { return /* binding */ useTextFieldAutoSelect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


// Automatically selects the default text in the text field when component mounts
const useTextFieldAutoSelect = () => {
  const inputRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (inputRef.current) {
      inputRef.current.focus();
      inputRef.current.select();
    }
  }, []);
  return inputRef;
};

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/icons":
/*!****************************************!*\
  !*** external ["elementorV2","icons"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["icons"];

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
/*!*******************************************************!*\
  !*** ./packages/packages/libs/editor-ui/src/index.ts ***!
  \*******************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CollapseIcon: function() { return /* reexport safe */ _components_collapse_icon__WEBPACK_IMPORTED_MODULE_0__.CollapseIcon; },
/* harmony export */   CollapsibleContent: function() { return /* reexport safe */ _components_collapsible_content__WEBPACK_IMPORTED_MODULE_1__.CollapsibleContent; },
/* harmony export */   ConfirmationDialog: function() { return /* reexport safe */ _components_confirmation_dialog__WEBPACK_IMPORTED_MODULE_22__.ConfirmationDialog; },
/* harmony export */   CtaButton: function() { return /* reexport safe */ _components_cta_button__WEBPACK_IMPORTED_MODULE_13__.CtaButton; },
/* harmony export */   EditableField: function() { return /* reexport safe */ _components_editable_field__WEBPACK_IMPORTED_MODULE_3__.EditableField; },
/* harmony export */   EllipsisWithTooltip: function() { return /* reexport safe */ _components_ellipsis_with_tooltip__WEBPACK_IMPORTED_MODULE_2__.EllipsisWithTooltip; },
/* harmony export */   FileUploadDropzone: function() { return /* reexport safe */ _components_file_upload__WEBPACK_IMPORTED_MODULE_19__.FileUploadDropzone; },
/* harmony export */   FileUploadRow: function() { return /* reexport safe */ _components_file_upload__WEBPACK_IMPORTED_MODULE_19__.FileUploadRow; },
/* harmony export */   FloatingActionsBar: function() { return /* reexport safe */ _components_floating_bar__WEBPACK_IMPORTED_MODULE_18__.FloatingActionsBar; },
/* harmony export */   Form: function() { return /* reexport safe */ _components_form__WEBPACK_IMPORTED_MODULE_12__.Form; },
/* harmony export */   GlobalDialog: function() { return /* reexport safe */ _components_global_dialog__WEBPACK_IMPORTED_MODULE_10__.GlobalDialog; },
/* harmony export */   ITEM_HEIGHT: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.ITEM_HEIGHT; },
/* harmony export */   InfoAlert: function() { return /* reexport safe */ _components_info_alert__WEBPACK_IMPORTED_MODULE_8__.InfoAlert; },
/* harmony export */   InfoTipCard: function() { return /* reexport safe */ _components_infotip_card__WEBPACK_IMPORTED_MODULE_7__.InfoTipCard; },
/* harmony export */   IntroductionModal: function() { return /* reexport safe */ _components_introduction_modal__WEBPACK_IMPORTED_MODULE_4__.IntroductionModal; },
/* harmony export */   MenuItemInfotip: function() { return /* reexport safe */ _components_menu_item__WEBPACK_IMPORTED_MODULE_6__.MenuItemInfotip; },
/* harmony export */   MenuListItem: function() { return /* reexport safe */ _components_menu_item__WEBPACK_IMPORTED_MODULE_6__.MenuListItem; },
/* harmony export */   PopoverAction: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.PopoverAction; },
/* harmony export */   PopoverBody: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.PopoverBody; },
/* harmony export */   PopoverHeader: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.PopoverHeader; },
/* harmony export */   PopoverMenuList: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.PopoverMenuList; },
/* harmony export */   PromotionAlert: function() { return /* reexport safe */ _components_promotions_promotion_alert__WEBPACK_IMPORTED_MODULE_17__.PromotionAlert; },
/* harmony export */   PromotionChip: function() { return /* reexport safe */ _components_promotions_promotion_chip__WEBPACK_IMPORTED_MODULE_16__.PromotionChip; },
/* harmony export */   PromotionInfotip: function() { return /* reexport safe */ _components_promotions_promotion_infotip__WEBPACK_IMPORTED_MODULE_14__.PromotionInfotip; },
/* harmony export */   PromotionPopover: function() { return /* reexport safe */ _components_promotions_promotion_popover__WEBPACK_IMPORTED_MODULE_15__.PromotionPopover; },
/* harmony export */   SaveChangesDialog: function() { return /* reexport safe */ _components_save_changes_dialog__WEBPACK_IMPORTED_MODULE_21__.SaveChangesDialog; },
/* harmony export */   SearchField: function() { return /* reexport safe */ _components_search_field__WEBPACK_IMPORTED_MODULE_11__.SearchField; },
/* harmony export */   SectionPopoverBody: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.SectionPopoverBody; },
/* harmony export */   SectionRefContext: function() { return /* reexport safe */ _contexts_section_context__WEBPACK_IMPORTED_MODULE_23__.SectionRefContext; },
/* harmony export */   StyledMenuList: function() { return /* reexport safe */ _components_popover__WEBPACK_IMPORTED_MODULE_20__.StyledMenuList; },
/* harmony export */   ThemeProvider: function() { return /* reexport safe */ _components_theme_provider__WEBPACK_IMPORTED_MODULE_5__["default"]; },
/* harmony export */   WarningInfotip: function() { return /* reexport safe */ _components_warning_infotip__WEBPACK_IMPORTED_MODULE_9__.WarningInfotip; },
/* harmony export */   closeDialog: function() { return /* reexport safe */ _components_global_dialog__WEBPACK_IMPORTED_MODULE_10__.closeDialog; },
/* harmony export */   getCollapsibleValue: function() { return /* reexport safe */ _components_collapsible_content__WEBPACK_IMPORTED_MODULE_1__.getCollapsibleValue; },
/* harmony export */   openDialog: function() { return /* reexport safe */ _components_global_dialog__WEBPACK_IMPORTED_MODULE_10__.openDialog; },
/* harmony export */   useCanvasClickHandler: function() { return /* reexport safe */ _hooks_use_canvas_click_handler__WEBPACK_IMPORTED_MODULE_26__.useCanvasClickHandler; },
/* harmony export */   useDialog: function() { return /* reexport safe */ _components_save_changes_dialog__WEBPACK_IMPORTED_MODULE_21__.useDialog; },
/* harmony export */   useEditable: function() { return /* reexport safe */ _hooks_use_editable__WEBPACK_IMPORTED_MODULE_24__.useEditable; },
/* harmony export */   useFloatingActionsBar: function() { return /* reexport safe */ _components_floating_bar__WEBPACK_IMPORTED_MODULE_18__.useFloatingActionsBar; },
/* harmony export */   useSectionWidth: function() { return /* reexport safe */ _contexts_section_context__WEBPACK_IMPORTED_MODULE_23__.useSectionWidth; },
/* harmony export */   useTextFieldAutoSelect: function() { return /* reexport safe */ _hooks_use_text_field_auto_select__WEBPACK_IMPORTED_MODULE_25__.useTextFieldAutoSelect; }
/* harmony export */ });
/* harmony import */ var _components_collapse_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/collapse-icon */ "./packages/packages/libs/editor-ui/src/components/collapse-icon.tsx");
/* harmony import */ var _components_collapsible_content__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/collapsible-content */ "./packages/packages/libs/editor-ui/src/components/collapsible-content.tsx");
/* harmony import */ var _components_ellipsis_with_tooltip__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/ellipsis-with-tooltip */ "./packages/packages/libs/editor-ui/src/components/ellipsis-with-tooltip.tsx");
/* harmony import */ var _components_editable_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/editable-field */ "./packages/packages/libs/editor-ui/src/components/editable-field.tsx");
/* harmony import */ var _components_introduction_modal__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/introduction-modal */ "./packages/packages/libs/editor-ui/src/components/introduction-modal.tsx");
/* harmony import */ var _components_theme_provider__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/theme-provider */ "./packages/packages/libs/editor-ui/src/components/theme-provider.tsx");
/* harmony import */ var _components_menu_item__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/menu-item */ "./packages/packages/libs/editor-ui/src/components/menu-item.tsx");
/* harmony import */ var _components_infotip_card__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/infotip-card */ "./packages/packages/libs/editor-ui/src/components/infotip-card.tsx");
/* harmony import */ var _components_info_alert__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/info-alert */ "./packages/packages/libs/editor-ui/src/components/info-alert.tsx");
/* harmony import */ var _components_warning_infotip__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/warning-infotip */ "./packages/packages/libs/editor-ui/src/components/warning-infotip.tsx");
/* harmony import */ var _components_global_dialog__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./components/global-dialog */ "./packages/packages/libs/editor-ui/src/components/global-dialog/index.ts");
/* harmony import */ var _components_search_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./components/search-field */ "./packages/packages/libs/editor-ui/src/components/search-field.tsx");
/* harmony import */ var _components_form__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/form */ "./packages/packages/libs/editor-ui/src/components/form.tsx");
/* harmony import */ var _components_cta_button__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/cta-button */ "./packages/packages/libs/editor-ui/src/components/cta-button.tsx");
/* harmony import */ var _components_promotions_promotion_infotip__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./components/promotions/promotion-infotip */ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-infotip.tsx");
/* harmony import */ var _components_promotions_promotion_popover__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./components/promotions/promotion-popover */ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-popover.tsx");
/* harmony import */ var _components_promotions_promotion_chip__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./components/promotions/promotion-chip */ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-chip.tsx");
/* harmony import */ var _components_promotions_promotion_alert__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./components/promotions/promotion-alert */ "./packages/packages/libs/editor-ui/src/components/promotions/promotion-alert.tsx");
/* harmony import */ var _components_floating_bar__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./components/floating-bar */ "./packages/packages/libs/editor-ui/src/components/floating-bar.tsx");
/* harmony import */ var _components_file_upload__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./components/file-upload */ "./packages/packages/libs/editor-ui/src/components/file-upload/index.ts");
/* harmony import */ var _components_popover__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./components/popover */ "./packages/packages/libs/editor-ui/src/components/popover/index.ts");
/* harmony import */ var _components_save_changes_dialog__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./components/save-changes-dialog */ "./packages/packages/libs/editor-ui/src/components/save-changes-dialog.tsx");
/* harmony import */ var _components_confirmation_dialog__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./components/confirmation-dialog */ "./packages/packages/libs/editor-ui/src/components/confirmation-dialog.tsx");
/* harmony import */ var _contexts_section_context__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./contexts/section-context */ "./packages/packages/libs/editor-ui/src/contexts/section-context.tsx");
/* harmony import */ var _hooks_use_editable__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./hooks/use-editable */ "./packages/packages/libs/editor-ui/src/hooks/use-editable.ts");
/* harmony import */ var _hooks_use_text_field_auto_select__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./hooks/use-text-field-auto-select */ "./packages/packages/libs/editor-ui/src/hooks/use-text-field-auto-select.ts");
/* harmony import */ var _hooks_use_canvas_click_handler__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./hooks/use-canvas-click-handler */ "./packages/packages/libs/editor-ui/src/hooks/use-canvas-click-handler.tsx");
// components
























// contexts


// hooks



}();
(window.elementorV2 = window.elementorV2 || {}).editorUi = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorUi?.init?.();
//# sourceMappingURL=editor-ui.js.map
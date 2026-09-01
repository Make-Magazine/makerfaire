/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@floating-ui/core/dist/floating-ui.core.mjs":
/*!******************************************************************!*\
  !*** ./node_modules/@floating-ui/core/dist/floating-ui.core.mjs ***!
  \******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   arrow: function() { return /* binding */ arrow; },
/* harmony export */   autoPlacement: function() { return /* binding */ autoPlacement; },
/* harmony export */   computePosition: function() { return /* binding */ computePosition; },
/* harmony export */   detectOverflow: function() { return /* binding */ detectOverflow; },
/* harmony export */   flip: function() { return /* binding */ flip; },
/* harmony export */   hide: function() { return /* binding */ hide; },
/* harmony export */   inline: function() { return /* binding */ inline; },
/* harmony export */   limitShift: function() { return /* binding */ limitShift; },
/* harmony export */   offset: function() { return /* binding */ offset; },
/* harmony export */   rectToClientRect: function() { return /* reexport safe */ _floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect; },
/* harmony export */   shift: function() { return /* binding */ shift; },
/* harmony export */   size: function() { return /* binding */ size; }
/* harmony export */ });
/* harmony import */ var _floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @floating-ui/utils */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs");



function computeCoordsFromPlacement(_ref, placement, rtl) {
  let {
    reference,
    floating
  } = _ref;
  const sideAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(placement);
  const alignmentAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignmentAxis)(placement);
  const alignLength = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAxisLength)(alignmentAxis);
  const side = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement);
  const isVertical = sideAxis === 'y';
  const commonX = reference.x + reference.width / 2 - floating.width / 2;
  const commonY = reference.y + reference.height / 2 - floating.height / 2;
  const commonAlign = reference[alignLength] / 2 - floating[alignLength] / 2;
  let coords;
  switch (side) {
    case 'top':
      coords = {
        x: commonX,
        y: reference.y - floating.height
      };
      break;
    case 'bottom':
      coords = {
        x: commonX,
        y: reference.y + reference.height
      };
      break;
    case 'right':
      coords = {
        x: reference.x + reference.width,
        y: commonY
      };
      break;
    case 'left':
      coords = {
        x: reference.x - floating.width,
        y: commonY
      };
      break;
    default:
      coords = {
        x: reference.x,
        y: reference.y
      };
  }
  switch ((0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement)) {
    case 'start':
      coords[alignmentAxis] -= commonAlign * (rtl && isVertical ? -1 : 1);
      break;
    case 'end':
      coords[alignmentAxis] += commonAlign * (rtl && isVertical ? -1 : 1);
      break;
  }
  return coords;
}

/**
 * Resolves with an object of overflow side offsets that determine how much the
 * element is overflowing a given clipping boundary on each side.
 * - positive = overflowing the boundary by that number of pixels
 * - negative = how many pixels left before it will overflow
 * - 0 = lies flush with the boundary
 * @see https://floating-ui.com/docs/detectOverflow
 */
async function detectOverflow(state, options) {
  var _await$platform$isEle;
  if (options === void 0) {
    options = {};
  }
  const {
    x,
    y,
    platform,
    rects,
    elements,
    strategy
  } = state;
  const {
    boundary = 'clippingAncestors',
    rootBoundary = 'viewport',
    elementContext = 'floating',
    altBoundary = false,
    padding = 0
  } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
  const paddingObject = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getPaddingObject)(padding);
  const altContext = elementContext === 'floating' ? 'reference' : 'floating';
  const element = elements[altBoundary ? altContext : elementContext];
  const clippingClientRect = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect)(await platform.getClippingRect({
    element: ((_await$platform$isEle = await (platform.isElement == null ? void 0 : platform.isElement(element))) != null ? _await$platform$isEle : true) ? element : element.contextElement || (await (platform.getDocumentElement == null ? void 0 : platform.getDocumentElement(elements.floating))),
    boundary,
    rootBoundary,
    strategy
  }));
  const rect = elementContext === 'floating' ? {
    x,
    y,
    width: rects.floating.width,
    height: rects.floating.height
  } : rects.reference;
  const offsetParent = await (platform.getOffsetParent == null ? void 0 : platform.getOffsetParent(elements.floating));
  const offsetScale = (await (platform.isElement == null ? void 0 : platform.isElement(offsetParent))) ? (await (platform.getScale == null ? void 0 : platform.getScale(offsetParent))) || {
    x: 1,
    y: 1
  } : {
    x: 1,
    y: 1
  };
  const elementClientRect = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect)(platform.convertOffsetParentRelativeRectToViewportRelativeRect ? await platform.convertOffsetParentRelativeRectToViewportRelativeRect({
    elements,
    rect,
    offsetParent,
    strategy
  }) : rect);
  return {
    top: (clippingClientRect.top - elementClientRect.top + paddingObject.top) / offsetScale.y,
    bottom: (elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom) / offsetScale.y,
    left: (clippingClientRect.left - elementClientRect.left + paddingObject.left) / offsetScale.x,
    right: (elementClientRect.right - clippingClientRect.right + paddingObject.right) / offsetScale.x
  };
}

// Maximum number of resets that can occur before bailing to avoid infinite reset loops.
const MAX_RESET_COUNT = 50;

/**
 * Computes the `x` and `y` coordinates that will place the floating element
 * next to a given reference element.
 *
 * This export does not have any `platform` interface logic. You will need to
 * write one for the platform you are using Floating UI with.
 */
const computePosition = async (reference, floating, config) => {
  const {
    placement = 'bottom',
    strategy = 'absolute',
    middleware = [],
    platform
  } = config;
  const platformWithDetectOverflow = platform.detectOverflow ? platform : {
    ...platform,
    detectOverflow
  };
  const rtl = await (platform.isRTL == null ? void 0 : platform.isRTL(floating));
  let rects = await platform.getElementRects({
    reference,
    floating,
    strategy
  });
  let {
    x,
    y
  } = computeCoordsFromPlacement(rects, placement, rtl);
  let statefulPlacement = placement;
  let resetCount = 0;
  const middlewareData = {};
  for (let i = 0; i < middleware.length; i++) {
    const currentMiddleware = middleware[i];
    if (!currentMiddleware) {
      continue;
    }
    const {
      name,
      fn
    } = currentMiddleware;
    const {
      x: nextX,
      y: nextY,
      data,
      reset
    } = await fn({
      x,
      y,
      initialPlacement: placement,
      placement: statefulPlacement,
      strategy,
      middlewareData,
      rects,
      platform: platformWithDetectOverflow,
      elements: {
        reference,
        floating
      }
    });
    x = nextX != null ? nextX : x;
    y = nextY != null ? nextY : y;
    middlewareData[name] = {
      ...middlewareData[name],
      ...data
    };
    if (reset && resetCount < MAX_RESET_COUNT) {
      resetCount++;
      if (typeof reset === 'object') {
        if (reset.placement) {
          statefulPlacement = reset.placement;
        }
        if (reset.rects) {
          rects = reset.rects === true ? await platform.getElementRects({
            reference,
            floating,
            strategy
          }) : reset.rects;
        }
        ({
          x,
          y
        } = computeCoordsFromPlacement(rects, statefulPlacement, rtl));
      }
      i = -1;
    }
  }
  return {
    x,
    y,
    placement: statefulPlacement,
    strategy,
    middlewareData
  };
};

/**
 * Provides data to position an inner element of the floating element so that it
 * appears centered to the reference element.
 * @see https://floating-ui.com/docs/arrow
 */
const arrow = options => ({
  name: 'arrow',
  options,
  async fn(state) {
    const {
      x,
      y,
      placement,
      rects,
      platform,
      elements,
      middlewareData
    } = state;
    // Since `element` is required, we don't Partial<> the type.
    const {
      element,
      padding = 0
    } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state) || {};
    if (element == null) {
      return {};
    }
    const paddingObject = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getPaddingObject)(padding);
    const coords = {
      x,
      y
    };
    const axis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignmentAxis)(placement);
    const length = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAxisLength)(axis);
    const arrowDimensions = await platform.getDimensions(element);
    const isYAxis = axis === 'y';
    const minProp = isYAxis ? 'top' : 'left';
    const maxProp = isYAxis ? 'bottom' : 'right';
    const clientProp = isYAxis ? 'clientHeight' : 'clientWidth';
    const endDiff = rects.reference[length] + rects.reference[axis] - coords[axis] - rects.floating[length];
    const startDiff = coords[axis] - rects.reference[axis];
    const arrowOffsetParent = await (platform.getOffsetParent == null ? void 0 : platform.getOffsetParent(element));
    let clientSize = arrowOffsetParent ? arrowOffsetParent[clientProp] : 0;

    // DOM platform can return `window` as the `offsetParent`.
    if (!clientSize || !(await (platform.isElement == null ? void 0 : platform.isElement(arrowOffsetParent)))) {
      clientSize = elements.floating[clientProp] || rects.floating[length];
    }
    const centerToReference = endDiff / 2 - startDiff / 2;

    // If the padding is large enough that it causes the arrow to no longer be
    // centered, modify the padding so that it is centered.
    const largestPossiblePadding = clientSize / 2 - arrowDimensions[length] / 2 - 1;
    const minPadding = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(paddingObject[minProp], largestPossiblePadding);
    const maxPadding = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(paddingObject[maxProp], largestPossiblePadding);

    // Make sure the arrow doesn't overflow the floating element if the center
    // point is outside the floating element's bounds.
    const min$1 = minPadding;
    const max = clientSize - arrowDimensions[length] - maxPadding;
    const center = clientSize / 2 - arrowDimensions[length] / 2 + centerToReference;
    const offset = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.clamp)(min$1, center, max);

    // If the reference is small enough that the arrow's padding causes it to
    // to point to nothing for an aligned placement, adjust the offset of the
    // floating element itself. To ensure `shift()` continues to take action,
    // a single reset is performed when this is true.
    const shouldAddOffset = !middlewareData.arrow && (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement) != null && center !== offset && rects.reference[length] / 2 - (center < min$1 ? minPadding : maxPadding) - arrowDimensions[length] / 2 < 0;
    const alignmentOffset = shouldAddOffset ? center < min$1 ? center - min$1 : center - max : 0;
    return {
      [axis]: coords[axis] + alignmentOffset,
      data: {
        [axis]: offset,
        centerOffset: center - offset - alignmentOffset,
        ...(shouldAddOffset && {
          alignmentOffset
        })
      },
      reset: shouldAddOffset
    };
  }
});

function getPlacementList(alignment, autoAlignment, allowedPlacements) {
  const allowedPlacementsSortedByAlignment = alignment ? [...allowedPlacements.filter(placement => (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement) === alignment), ...allowedPlacements.filter(placement => (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement) !== alignment)] : allowedPlacements.filter(placement => (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement) === placement);
  return allowedPlacementsSortedByAlignment.filter(placement => {
    if (alignment) {
      return (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement) === alignment || (autoAlignment ? (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getOppositeAlignmentPlacement)(placement) !== placement : false);
    }
    return true;
  });
}
/**
 * Optimizes the visibility of the floating element by choosing the placement
 * that has the most space available automatically, without needing to specify a
 * preferred placement. Alternative to `flip`.
 * @see https://floating-ui.com/docs/autoPlacement
 */
const autoPlacement = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: 'autoPlacement',
    options,
    async fn(state) {
      var _middlewareData$autoP, _middlewareData$autoP2, _placementsThatFitOnE;
      const {
        rects,
        middlewareData,
        placement,
        platform,
        elements
      } = state;
      const {
        crossAxis = false,
        alignment,
        allowedPlacements = _floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.placements,
        autoAlignment = true,
        ...detectOverflowOptions
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
      const placements$1 = alignment !== undefined || allowedPlacements === _floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.placements ? getPlacementList(alignment || null, autoAlignment, allowedPlacements) : allowedPlacements;
      const overflow = await platform.detectOverflow(state, detectOverflowOptions);
      const currentIndex = ((_middlewareData$autoP = middlewareData.autoPlacement) == null ? void 0 : _middlewareData$autoP.index) || 0;
      const currentPlacement = placements$1[currentIndex];
      if (currentPlacement == null) {
        return {};
      }
      const alignmentSides = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignmentSides)(currentPlacement, rects, await (platform.isRTL == null ? void 0 : platform.isRTL(elements.floating)));

      // Make `computeCoords` start from the right place.
      if (placement !== currentPlacement) {
        return {
          reset: {
            placement: placements$1[0]
          }
        };
      }
      const currentOverflows = [overflow[(0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(currentPlacement)], overflow[alignmentSides[0]], overflow[alignmentSides[1]]];
      const allOverflows = [...(((_middlewareData$autoP2 = middlewareData.autoPlacement) == null ? void 0 : _middlewareData$autoP2.overflows) || []), {
        placement: currentPlacement,
        overflows: currentOverflows
      }];
      const nextPlacement = placements$1[currentIndex + 1];

      // There are more placements to check.
      if (nextPlacement) {
        return {
          data: {
            index: currentIndex + 1,
            overflows: allOverflows
          },
          reset: {
            placement: nextPlacement
          }
        };
      }
      const placementsSortedByMostSpace = allOverflows.map(d => {
        const alignment = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(d.placement);
        return [d.placement, alignment && crossAxis ?
        // Check along the mainAxis and main crossAxis side.
        d.overflows.slice(0, 2).reduce((acc, v) => acc + v, 0) :
        // Check only the mainAxis.
        d.overflows[0], d.overflows];
      }).sort((a, b) => a[1] - b[1]);
      const placementsThatFitOnEachSide = placementsSortedByMostSpace.filter(d => d[2].slice(0,
      // Aligned placements should not check their opposite crossAxis
      // side.
      (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(d[0]) ? 2 : 3).every(v => v <= 0));
      const resetPlacement = ((_placementsThatFitOnE = placementsThatFitOnEachSide[0]) == null ? void 0 : _placementsThatFitOnE[0]) || placementsSortedByMostSpace[0][0];
      if (resetPlacement !== placement) {
        return {
          data: {
            index: currentIndex + 1,
            overflows: allOverflows
          },
          reset: {
            placement: resetPlacement
          }
        };
      }
      return {};
    }
  };
};

/**
 * Optimizes the visibility of the floating element by flipping the `placement`
 * in order to keep it in view when the preferred placement(s) will overflow the
 * clipping boundary. Alternative to `autoPlacement`.
 * @see https://floating-ui.com/docs/flip
 */
const flip = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: 'flip',
    options,
    async fn(state) {
      var _middlewareData$arrow, _middlewareData$flip;
      const {
        placement,
        middlewareData,
        rects,
        initialPlacement,
        platform,
        elements
      } = state;
      const {
        mainAxis: checkMainAxis = true,
        crossAxis: checkCrossAxis = true,
        fallbackPlacements: specifiedFallbackPlacements,
        fallbackStrategy = 'bestFit',
        fallbackAxisSideDirection = 'none',
        flipAlignment = true,
        ...detectOverflowOptions
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);

      // If a reset by the arrow was caused due to an alignment offset being
      // added, we should skip any logic now since `flip()` has already done its
      // work.
      // https://github.com/floating-ui/floating-ui/issues/2549#issuecomment-1719601643
      if ((_middlewareData$arrow = middlewareData.arrow) != null && _middlewareData$arrow.alignmentOffset) {
        return {};
      }
      const side = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement);
      const initialSideAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(initialPlacement);
      const isBasePlacement = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(initialPlacement) === initialPlacement;
      const rtl = await (platform.isRTL == null ? void 0 : platform.isRTL(elements.floating));
      const fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipAlignment ? [(0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getOppositePlacement)(initialPlacement)] : (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getExpandedPlacements)(initialPlacement));
      const hasFallbackAxisSideDirection = fallbackAxisSideDirection !== 'none';
      if (!specifiedFallbackPlacements && hasFallbackAxisSideDirection) {
        fallbackPlacements.push(...(0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getOppositeAxisPlacements)(initialPlacement, flipAlignment, fallbackAxisSideDirection, rtl));
      }
      const placements = [initialPlacement, ...fallbackPlacements];
      const overflow = await platform.detectOverflow(state, detectOverflowOptions);
      const overflows = [];
      let overflowsData = ((_middlewareData$flip = middlewareData.flip) == null ? void 0 : _middlewareData$flip.overflows) || [];
      if (checkMainAxis) {
        overflows.push(overflow[side]);
      }
      if (checkCrossAxis) {
        const sides = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignmentSides)(placement, rects, rtl);
        overflows.push(overflow[sides[0]], overflow[sides[1]]);
      }
      overflowsData = [...overflowsData, {
        placement,
        overflows
      }];

      // One or more sides is overflowing.
      if (!overflows.every(side => side <= 0)) {
        var _middlewareData$flip2, _overflowsData$filter;
        const nextIndex = (((_middlewareData$flip2 = middlewareData.flip) == null ? void 0 : _middlewareData$flip2.index) || 0) + 1;
        const nextPlacement = placements[nextIndex];
        if (nextPlacement) {
          const ignoreCrossAxisOverflow = checkCrossAxis === 'alignment' ? initialSideAxis !== (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(nextPlacement) : false;
          if (!ignoreCrossAxisOverflow ||
          // We leave the current main axis only if every placement on that axis
          // overflows the main axis.
          overflowsData.every(d => (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(d.placement) === initialSideAxis ? d.overflows[0] > 0 : true)) {
            // Try next placement and re-run the lifecycle.
            return {
              data: {
                index: nextIndex,
                overflows: overflowsData
              },
              reset: {
                placement: nextPlacement
              }
            };
          }
        }

        // First, find the candidates that fit on the mainAxis side of overflow,
        // then find the placement that fits the best on the main crossAxis side.
        let resetPlacement = (_overflowsData$filter = overflowsData.filter(d => d.overflows[0] <= 0).sort((a, b) => a.overflows[1] - b.overflows[1])[0]) == null ? void 0 : _overflowsData$filter.placement;

        // Otherwise fallback.
        if (!resetPlacement) {
          switch (fallbackStrategy) {
            case 'bestFit':
              {
                var _overflowsData$filter2;
                const placement = (_overflowsData$filter2 = overflowsData.filter(d => {
                  if (hasFallbackAxisSideDirection) {
                    const currentSideAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(d.placement);
                    return currentSideAxis === initialSideAxis ||
                    // Create a bias to the `y` side axis due to horizontal
                    // reading directions favoring greater width.
                    currentSideAxis === 'y';
                  }
                  return true;
                }).map(d => [d.placement, d.overflows.filter(overflow => overflow > 0).reduce((acc, overflow) => acc + overflow, 0)]).sort((a, b) => a[1] - b[1])[0]) == null ? void 0 : _overflowsData$filter2[0];
                if (placement) {
                  resetPlacement = placement;
                }
                break;
              }
            case 'initialPlacement':
              resetPlacement = initialPlacement;
              break;
          }
        }
        if (placement !== resetPlacement) {
          return {
            reset: {
              placement: resetPlacement
            }
          };
        }
      }
      return {};
    }
  };
};

function getSideOffsets(overflow, rect) {
  return {
    top: overflow.top - rect.height,
    right: overflow.right - rect.width,
    bottom: overflow.bottom - rect.height,
    left: overflow.left - rect.width
  };
}
function isAnySideFullyClipped(overflow) {
  return _floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.sides.some(side => overflow[side] >= 0);
}
/**
 * Provides data to hide the floating element in applicable situations, such as
 * when it is not in the same clipping context as the reference element.
 * @see https://floating-ui.com/docs/hide
 */
const hide = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: 'hide',
    options,
    async fn(state) {
      const {
        rects,
        platform
      } = state;
      const {
        strategy = 'referenceHidden',
        ...detectOverflowOptions
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
      switch (strategy) {
        case 'referenceHidden':
          {
            const overflow = await platform.detectOverflow(state, {
              ...detectOverflowOptions,
              elementContext: 'reference'
            });
            const offsets = getSideOffsets(overflow, rects.reference);
            return {
              data: {
                referenceHiddenOffsets: offsets,
                referenceHidden: isAnySideFullyClipped(offsets)
              }
            };
          }
        case 'escaped':
          {
            const overflow = await platform.detectOverflow(state, {
              ...detectOverflowOptions,
              altBoundary: true
            });
            const offsets = getSideOffsets(overflow, rects.floating);
            return {
              data: {
                escapedOffsets: offsets,
                escaped: isAnySideFullyClipped(offsets)
              }
            };
          }
        default:
          {
            return {};
          }
      }
    }
  };
};

function getBoundingRect(rects) {
  const minX = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(...rects.map(rect => rect.left));
  const minY = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(...rects.map(rect => rect.top));
  const maxX = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(...rects.map(rect => rect.right));
  const maxY = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(...rects.map(rect => rect.bottom));
  return {
    x: minX,
    y: minY,
    width: maxX - minX,
    height: maxY - minY
  };
}
function getRectsByLine(rects) {
  const sortedRects = rects.slice().sort((a, b) => a.y - b.y);
  const groups = [];
  let prevRect = null;
  for (let i = 0; i < sortedRects.length; i++) {
    const rect = sortedRects[i];
    if (!prevRect || rect.y - prevRect.y > prevRect.height / 2) {
      groups.push([rect]);
    } else {
      groups[groups.length - 1].push(rect);
    }
    prevRect = rect;
  }
  return groups.map(rect => (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect)(getBoundingRect(rect)));
}
/**
 * Provides improved positioning for inline reference elements that can span
 * over multiple lines, such as hyperlinks or range selections.
 * @see https://floating-ui.com/docs/inline
 */
const inline = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: 'inline',
    options,
    async fn(state) {
      const {
        placement,
        elements,
        rects,
        platform,
        strategy
      } = state;
      // A MouseEvent's client{X,Y} coords can be up to 2 pixels off a
      // ClientRect's bounds, despite the event listener being triggered. A
      // padding of 2 seems to handle this issue.
      const {
        padding = 2,
        x,
        y
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
      const nativeClientRects = Array.from((await (platform.getClientRects == null ? void 0 : platform.getClientRects(elements.reference))) || []);
      const clientRects = getRectsByLine(nativeClientRects);
      const fallback = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect)(getBoundingRect(nativeClientRects));
      const paddingObject = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getPaddingObject)(padding);
      function getBoundingClientRect() {
        // There are two rects and they are disjoined.
        if (clientRects.length === 2 && clientRects[0].left > clientRects[1].right && x != null && y != null) {
          // Find the first rect in which the point is fully inside.
          return clientRects.find(rect => x > rect.left - paddingObject.left && x < rect.right + paddingObject.right && y > rect.top - paddingObject.top && y < rect.bottom + paddingObject.bottom) || fallback;
        }

        // There are 2 or more connected rects.
        if (clientRects.length >= 2) {
          if ((0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(placement) === 'y') {
            const firstRect = clientRects[0];
            const lastRect = clientRects[clientRects.length - 1];
            const isTop = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement) === 'top';
            const top = firstRect.top;
            const bottom = lastRect.bottom;
            const left = isTop ? firstRect.left : lastRect.left;
            const right = isTop ? firstRect.right : lastRect.right;
            const width = right - left;
            const height = bottom - top;
            return {
              top,
              bottom,
              left,
              right,
              width,
              height,
              x: left,
              y: top
            };
          }
          const isLeftSide = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement) === 'left';
          const maxRight = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(...clientRects.map(rect => rect.right));
          const minLeft = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(...clientRects.map(rect => rect.left));
          const measureRects = clientRects.filter(rect => isLeftSide ? rect.left === minLeft : rect.right === maxRight);
          const top = measureRects[0].top;
          const bottom = measureRects[measureRects.length - 1].bottom;
          const left = minLeft;
          const right = maxRight;
          const width = right - left;
          const height = bottom - top;
          return {
            top,
            bottom,
            left,
            right,
            width,
            height,
            x: left,
            y: top
          };
        }
        return fallback;
      }
      const resetRects = await platform.getElementRects({
        reference: {
          getBoundingClientRect
        },
        floating: elements.floating,
        strategy
      });
      if (rects.reference.x !== resetRects.reference.x || rects.reference.y !== resetRects.reference.y || rects.reference.width !== resetRects.reference.width || rects.reference.height !== resetRects.reference.height) {
        return {
          reset: {
            rects: resetRects
          }
        };
      }
      return {};
    }
  };
};

const originSides = /*#__PURE__*/new Set(['left', 'top']);

// For type backwards-compatibility, the `OffsetOptions` type was also
// Derivable.

async function convertValueToCoords(state, options) {
  const {
    placement,
    platform,
    elements
  } = state;
  const rtl = await (platform.isRTL == null ? void 0 : platform.isRTL(elements.floating));
  const side = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement);
  const alignment = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement);
  const isVertical = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(placement) === 'y';
  const mainAxisMulti = originSides.has(side) ? -1 : 1;
  const crossAxisMulti = rtl && isVertical ? -1 : 1;
  const rawValue = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);

  // eslint-disable-next-line prefer-const
  let {
    mainAxis,
    crossAxis,
    alignmentAxis
  } = typeof rawValue === 'number' ? {
    mainAxis: rawValue,
    crossAxis: 0,
    alignmentAxis: null
  } : {
    mainAxis: rawValue.mainAxis || 0,
    crossAxis: rawValue.crossAxis || 0,
    alignmentAxis: rawValue.alignmentAxis
  };
  if (alignment && typeof alignmentAxis === 'number') {
    crossAxis = alignment === 'end' ? alignmentAxis * -1 : alignmentAxis;
  }
  return isVertical ? {
    x: crossAxis * crossAxisMulti,
    y: mainAxis * mainAxisMulti
  } : {
    x: mainAxis * mainAxisMulti,
    y: crossAxis * crossAxisMulti
  };
}

/**
 * Modifies the placement by translating the floating element along the
 * specified axes.
 * A number (shorthand for `mainAxis` or distance), or an axes configuration
 * object may be passed.
 * @see https://floating-ui.com/docs/offset
 */
const offset = function (options) {
  if (options === void 0) {
    options = 0;
  }
  return {
    name: 'offset',
    options,
    async fn(state) {
      var _middlewareData$offse, _middlewareData$arrow;
      const {
        x,
        y,
        placement,
        middlewareData
      } = state;
      const diffCoords = await convertValueToCoords(state, options);

      // If the placement is the same and the arrow caused an alignment offset
      // then we don't need to change the positioning coordinates.
      if (placement === ((_middlewareData$offse = middlewareData.offset) == null ? void 0 : _middlewareData$offse.placement) && (_middlewareData$arrow = middlewareData.arrow) != null && _middlewareData$arrow.alignmentOffset) {
        return {};
      }
      return {
        x: x + diffCoords.x,
        y: y + diffCoords.y,
        data: {
          ...diffCoords,
          placement
        }
      };
    }
  };
};

/**
 * Optimizes the visibility of the floating element by shifting it in order to
 * keep it in view when it will overflow the clipping boundary.
 * @see https://floating-ui.com/docs/shift
 */
const shift = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: 'shift',
    options,
    async fn(state) {
      const {
        x,
        y,
        placement,
        platform
      } = state;
      const {
        mainAxis: checkMainAxis = true,
        crossAxis: checkCrossAxis = false,
        limiter = {
          fn: _ref => {
            let {
              x,
              y
            } = _ref;
            return {
              x,
              y
            };
          }
        },
        ...detectOverflowOptions
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
      const coords = {
        x,
        y
      };
      const overflow = await platform.detectOverflow(state, detectOverflowOptions);
      const crossAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)((0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement));
      const mainAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getOppositeAxis)(crossAxis);
      let mainAxisCoord = coords[mainAxis];
      let crossAxisCoord = coords[crossAxis];
      if (checkMainAxis) {
        const minSide = mainAxis === 'y' ? 'top' : 'left';
        const maxSide = mainAxis === 'y' ? 'bottom' : 'right';
        const min = mainAxisCoord + overflow[minSide];
        const max = mainAxisCoord - overflow[maxSide];
        mainAxisCoord = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.clamp)(min, mainAxisCoord, max);
      }
      if (checkCrossAxis) {
        const minSide = crossAxis === 'y' ? 'top' : 'left';
        const maxSide = crossAxis === 'y' ? 'bottom' : 'right';
        const min = crossAxisCoord + overflow[minSide];
        const max = crossAxisCoord - overflow[maxSide];
        crossAxisCoord = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.clamp)(min, crossAxisCoord, max);
      }
      const limitedCoords = limiter.fn({
        ...state,
        [mainAxis]: mainAxisCoord,
        [crossAxis]: crossAxisCoord
      });
      return {
        ...limitedCoords,
        data: {
          x: limitedCoords.x - x,
          y: limitedCoords.y - y,
          enabled: {
            [mainAxis]: checkMainAxis,
            [crossAxis]: checkCrossAxis
          }
        }
      };
    }
  };
};
/**
 * Built-in `limiter` that will stop `shift()` at a certain point.
 */
const limitShift = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    options,
    fn(state) {
      const {
        x,
        y,
        placement,
        rects,
        middlewareData
      } = state;
      const {
        offset = 0,
        mainAxis: checkMainAxis = true,
        crossAxis: checkCrossAxis = true
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
      const coords = {
        x,
        y
      };
      const crossAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(placement);
      const mainAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getOppositeAxis)(crossAxis);
      let mainAxisCoord = coords[mainAxis];
      let crossAxisCoord = coords[crossAxis];
      const rawOffset = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(offset, state);
      const computedOffset = typeof rawOffset === 'number' ? {
        mainAxis: rawOffset,
        crossAxis: 0
      } : {
        mainAxis: 0,
        crossAxis: 0,
        ...rawOffset
      };
      if (checkMainAxis) {
        const len = mainAxis === 'y' ? 'height' : 'width';
        const limitMin = rects.reference[mainAxis] - rects.floating[len] + computedOffset.mainAxis;
        const limitMax = rects.reference[mainAxis] + rects.reference[len] - computedOffset.mainAxis;
        if (mainAxisCoord < limitMin) {
          mainAxisCoord = limitMin;
        } else if (mainAxisCoord > limitMax) {
          mainAxisCoord = limitMax;
        }
      }
      if (checkCrossAxis) {
        var _middlewareData$offse, _middlewareData$offse2;
        const len = mainAxis === 'y' ? 'width' : 'height';
        const isOriginSide = originSides.has((0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement));
        const limitMin = rects.reference[crossAxis] - rects.floating[len] + (isOriginSide ? ((_middlewareData$offse = middlewareData.offset) == null ? void 0 : _middlewareData$offse[crossAxis]) || 0 : 0) + (isOriginSide ? 0 : computedOffset.crossAxis);
        const limitMax = rects.reference[crossAxis] + rects.reference[len] + (isOriginSide ? 0 : ((_middlewareData$offse2 = middlewareData.offset) == null ? void 0 : _middlewareData$offse2[crossAxis]) || 0) - (isOriginSide ? computedOffset.crossAxis : 0);
        if (crossAxisCoord < limitMin) {
          crossAxisCoord = limitMin;
        } else if (crossAxisCoord > limitMax) {
          crossAxisCoord = limitMax;
        }
      }
      return {
        [mainAxis]: mainAxisCoord,
        [crossAxis]: crossAxisCoord
      };
    }
  };
};

/**
 * Provides data that allows you to change the size of the floating element —
 * for instance, prevent it from overflowing the clipping boundary or match the
 * width of the reference element.
 * @see https://floating-ui.com/docs/size
 */
const size = function (options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: 'size',
    options,
    async fn(state) {
      var _state$middlewareData, _state$middlewareData2;
      const {
        placement,
        rects,
        platform,
        elements
      } = state;
      const {
        apply = () => {},
        ...detectOverflowOptions
      } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.evaluate)(options, state);
      const overflow = await platform.detectOverflow(state, detectOverflowOptions);
      const side = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSide)(placement);
      const alignment = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getAlignment)(placement);
      const isYAxis = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.getSideAxis)(placement) === 'y';
      const {
        width,
        height
      } = rects.floating;
      let heightSide;
      let widthSide;
      if (side === 'top' || side === 'bottom') {
        heightSide = side;
        widthSide = alignment === ((await (platform.isRTL == null ? void 0 : platform.isRTL(elements.floating))) ? 'start' : 'end') ? 'left' : 'right';
      } else {
        widthSide = side;
        heightSide = alignment === 'end' ? 'top' : 'bottom';
      }
      const maximumClippingHeight = height - overflow.top - overflow.bottom;
      const maximumClippingWidth = width - overflow.left - overflow.right;
      const overflowAvailableHeight = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(height - overflow[heightSide], maximumClippingHeight);
      const overflowAvailableWidth = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.min)(width - overflow[widthSide], maximumClippingWidth);
      const noShift = !state.middlewareData.shift;
      let availableHeight = overflowAvailableHeight;
      let availableWidth = overflowAvailableWidth;
      if ((_state$middlewareData = state.middlewareData.shift) != null && _state$middlewareData.enabled.x) {
        availableWidth = maximumClippingWidth;
      }
      if ((_state$middlewareData2 = state.middlewareData.shift) != null && _state$middlewareData2.enabled.y) {
        availableHeight = maximumClippingHeight;
      }
      if (noShift && !alignment) {
        const xMin = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(overflow.left, 0);
        const xMax = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(overflow.right, 0);
        const yMin = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(overflow.top, 0);
        const yMax = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(overflow.bottom, 0);
        if (isYAxis) {
          availableWidth = width - 2 * (xMin !== 0 || xMax !== 0 ? xMin + xMax : (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(overflow.left, overflow.right));
        } else {
          availableHeight = height - 2 * (yMin !== 0 || yMax !== 0 ? yMin + yMax : (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_0__.max)(overflow.top, overflow.bottom));
        }
      }
      await apply({
        ...state,
        availableWidth,
        availableHeight
      });
      const nextDimensions = await platform.getDimensions(elements.floating);
      if (width !== nextDimensions.width || height !== nextDimensions.height) {
        return {
          reset: {
            rects: true
          }
        };
      }
      return {};
    }
  };
};




/***/ }),

/***/ "./node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs":
/*!****************************************************************!*\
  !*** ./node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs ***!
  \****************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   arrow: function() { return /* binding */ arrow; },
/* harmony export */   autoPlacement: function() { return /* binding */ autoPlacement; },
/* harmony export */   autoUpdate: function() { return /* binding */ autoUpdate; },
/* harmony export */   computePosition: function() { return /* binding */ computePosition; },
/* harmony export */   detectOverflow: function() { return /* binding */ detectOverflow; },
/* harmony export */   flip: function() { return /* binding */ flip; },
/* harmony export */   getOverflowAncestors: function() { return /* reexport safe */ _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getOverflowAncestors; },
/* harmony export */   hide: function() { return /* binding */ hide; },
/* harmony export */   inline: function() { return /* binding */ inline; },
/* harmony export */   limitShift: function() { return /* binding */ limitShift; },
/* harmony export */   offset: function() { return /* binding */ offset; },
/* harmony export */   platform: function() { return /* binding */ platform; },
/* harmony export */   shift: function() { return /* binding */ shift; },
/* harmony export */   size: function() { return /* binding */ size; }
/* harmony export */ });
/* harmony import */ var _floating_ui_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @floating-ui/utils */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs");
/* harmony import */ var _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @floating-ui/core */ "./node_modules/@floating-ui/core/dist/floating-ui.core.mjs");
/* harmony import */ var _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @floating-ui/utils/dom */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs");





function getCssDimensions(element) {
  const css = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(element);
  // In testing environments, the `width` and `height` properties are empty
  // strings for SVG elements, returning NaN. Fallback to `0` in this case.
  let width = parseFloat(css.width) || 0;
  let height = parseFloat(css.height) || 0;
  const hasOffset = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(element);
  const offsetWidth = hasOffset ? element.offsetWidth : width;
  const offsetHeight = hasOffset ? element.offsetHeight : height;
  const shouldFallback = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.round)(width) !== offsetWidth || (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.round)(height) !== offsetHeight;
  if (shouldFallback) {
    width = offsetWidth;
    height = offsetHeight;
  }
  return {
    width,
    height,
    $: shouldFallback
  };
}

function unwrapElement(element) {
  return !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(element) ? element.contextElement : element;
}

function getScale(element) {
  const domElement = unwrapElement(element);
  if (!(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(domElement)) {
    return (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(1);
  }
  const rect = domElement.getBoundingClientRect();
  const {
    width,
    height,
    $
  } = getCssDimensions(domElement);
  let x = ($ ? (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.round)(rect.width) : rect.width) / width;
  let y = ($ ? (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.round)(rect.height) : rect.height) / height;

  // 0, NaN, or Infinity should always fallback to 1.

  if (!x || !Number.isFinite(x)) {
    x = 1;
  }
  if (!y || !Number.isFinite(y)) {
    y = 1;
  }
  return {
    x,
    y
  };
}

const noOffsets = /*#__PURE__*/(0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(0);
function getVisualOffsets(element) {
  const win = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(element);
  if (!(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isWebKit)() || !win.visualViewport) {
    return noOffsets;
  }
  return {
    x: win.visualViewport.offsetLeft,
    y: win.visualViewport.offsetTop
  };
}
function shouldAddVisualOffsets(element, isFixed, floatingOffsetParent) {
  if (isFixed === void 0) {
    isFixed = false;
  }
  if (!floatingOffsetParent || isFixed && floatingOffsetParent !== (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(element)) {
    return false;
  }
  return isFixed;
}

function getBoundingClientRect(element, includeScale, isFixedStrategy, offsetParent) {
  if (includeScale === void 0) {
    includeScale = false;
  }
  if (isFixedStrategy === void 0) {
    isFixedStrategy = false;
  }
  const clientRect = element.getBoundingClientRect();
  const domElement = unwrapElement(element);
  let scale = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(1);
  if (includeScale) {
    if (offsetParent) {
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(offsetParent)) {
        scale = getScale(offsetParent);
      }
    } else {
      scale = getScale(element);
    }
  }
  const visualOffsets = shouldAddVisualOffsets(domElement, isFixedStrategy, offsetParent) ? getVisualOffsets(domElement) : (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(0);
  let x = (clientRect.left + visualOffsets.x) / scale.x;
  let y = (clientRect.top + visualOffsets.y) / scale.y;
  let width = clientRect.width / scale.x;
  let height = clientRect.height / scale.y;
  if (domElement) {
    const win = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(domElement);
    const offsetWin = offsetParent && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(offsetParent) ? (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(offsetParent) : offsetParent;
    let currentWin = win;
    let currentIFrame = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getFrameElement)(currentWin);
    while (currentIFrame && offsetParent && offsetWin !== currentWin) {
      const iframeScale = getScale(currentIFrame);
      const iframeRect = currentIFrame.getBoundingClientRect();
      const css = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(currentIFrame);
      const left = iframeRect.left + (currentIFrame.clientLeft + parseFloat(css.paddingLeft)) * iframeScale.x;
      const top = iframeRect.top + (currentIFrame.clientTop + parseFloat(css.paddingTop)) * iframeScale.y;
      x *= iframeScale.x;
      y *= iframeScale.y;
      width *= iframeScale.x;
      height *= iframeScale.y;
      x += left;
      y += top;
      currentWin = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(currentIFrame);
      currentIFrame = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getFrameElement)(currentWin);
    }
  }
  return (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect)({
    width,
    height,
    x,
    y
  });
}

// If <html> has a CSS width greater than the viewport, then this will be
// incorrect for RTL.
function getWindowScrollBarX(element, rect) {
  const leftScroll = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeScroll)(element).scrollLeft;
  if (!rect) {
    return getBoundingClientRect((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(element)).left + leftScroll;
  }
  return rect.left + leftScroll;
}

function getHTMLOffset(documentElement, scroll) {
  const htmlRect = documentElement.getBoundingClientRect();
  const x = htmlRect.left + scroll.scrollLeft - getWindowScrollBarX(documentElement, htmlRect);
  const y = htmlRect.top + scroll.scrollTop;
  return {
    x,
    y
  };
}

function convertOffsetParentRelativeRectToViewportRelativeRect(_ref) {
  let {
    elements,
    rect,
    offsetParent,
    strategy
  } = _ref;
  const isFixed = strategy === 'fixed';
  const documentElement = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(offsetParent);
  const topLayer = elements ? (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isTopLayer)(elements.floating) : false;
  if (offsetParent === documentElement || topLayer && isFixed) {
    return rect;
  }
  let scroll = {
    scrollLeft: 0,
    scrollTop: 0
  };
  let scale = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(1);
  const offsets = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(0);
  const isOffsetParentAnElement = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(offsetParent);
  if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
    if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeName)(offsetParent) !== 'body' || (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isOverflowElement)(documentElement)) {
      scroll = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeScroll)(offsetParent);
    }
    if (isOffsetParentAnElement) {
      const offsetRect = getBoundingClientRect(offsetParent);
      scale = getScale(offsetParent);
      offsets.x = offsetRect.x + offsetParent.clientLeft;
      offsets.y = offsetRect.y + offsetParent.clientTop;
    }
  }
  const htmlOffset = documentElement && !isOffsetParentAnElement && !isFixed ? getHTMLOffset(documentElement, scroll) : (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(0);
  return {
    width: rect.width * scale.x,
    height: rect.height * scale.y,
    x: rect.x * scale.x - scroll.scrollLeft * scale.x + offsets.x + htmlOffset.x,
    y: rect.y * scale.y - scroll.scrollTop * scale.y + offsets.y + htmlOffset.y
  };
}

function getClientRects(element) {
  return Array.from(element.getClientRects());
}

// Gets the entire size of the scrollable document area, even extending outside
// of the `<html>` and `<body>` rect bounds if horizontally scrollable.
function getDocumentRect(element) {
  const html = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(element);
  const scroll = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeScroll)(element);
  const body = element.ownerDocument.body;
  const width = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.max)(html.scrollWidth, html.clientWidth, body.scrollWidth, body.clientWidth);
  const height = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.max)(html.scrollHeight, html.clientHeight, body.scrollHeight, body.clientHeight);
  let x = -scroll.scrollLeft + getWindowScrollBarX(element);
  const y = -scroll.scrollTop;
  if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(body).direction === 'rtl') {
    x += (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.max)(html.clientWidth, body.clientWidth) - width;
  }
  return {
    width,
    height,
    x,
    y
  };
}

// Safety check: ensure the scrollbar space is reasonable in case this
// calculation is affected by unusual styles.
// Most scrollbars leave 15-18px of space.
const SCROLLBAR_MAX = 25;
function getViewportRect(element, strategy) {
  const win = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(element);
  const html = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(element);
  const visualViewport = win.visualViewport;
  let width = html.clientWidth;
  let height = html.clientHeight;
  let x = 0;
  let y = 0;
  if (visualViewport) {
    width = visualViewport.width;
    height = visualViewport.height;
    const visualViewportBased = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isWebKit)();
    if (!visualViewportBased || visualViewportBased && strategy === 'fixed') {
      x = visualViewport.offsetLeft;
      y = visualViewport.offsetTop;
    }
  }
  const windowScrollbarX = getWindowScrollBarX(html);
  // <html> `overflow: hidden` + `scrollbar-gutter: stable` reduces the
  // visual width of the <html> but this is not considered in the size
  // of `html.clientWidth`.
  if (windowScrollbarX <= 0) {
    const doc = html.ownerDocument;
    const body = doc.body;
    const bodyStyles = getComputedStyle(body);
    const bodyMarginInline = doc.compatMode === 'CSS1Compat' ? parseFloat(bodyStyles.marginLeft) + parseFloat(bodyStyles.marginRight) || 0 : 0;
    const clippingStableScrollbarWidth = Math.abs(html.clientWidth - body.clientWidth - bodyMarginInline);
    if (clippingStableScrollbarWidth <= SCROLLBAR_MAX) {
      width -= clippingStableScrollbarWidth;
    }
  } else if (windowScrollbarX <= SCROLLBAR_MAX) {
    // If the <body> scrollbar is on the left, the width needs to be extended
    // by the scrollbar amount so there isn't extra space on the right.
    width += windowScrollbarX;
  }
  return {
    width,
    height,
    x,
    y
  };
}

// Returns the inner client rect, subtracting scrollbars if present.
function getInnerBoundingClientRect(element, strategy) {
  const clientRect = getBoundingClientRect(element, true, strategy === 'fixed');
  const top = clientRect.top + element.clientTop;
  const left = clientRect.left + element.clientLeft;
  const scale = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(element) ? getScale(element) : (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(1);
  const width = element.clientWidth * scale.x;
  const height = element.clientHeight * scale.y;
  const x = left * scale.x;
  const y = top * scale.y;
  return {
    width,
    height,
    x,
    y
  };
}
function getClientRectFromClippingAncestor(element, clippingAncestor, strategy) {
  let rect;
  if (clippingAncestor === 'viewport') {
    rect = getViewportRect(element, strategy);
  } else if (clippingAncestor === 'document') {
    rect = getDocumentRect((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(element));
  } else if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(clippingAncestor)) {
    rect = getInnerBoundingClientRect(clippingAncestor, strategy);
  } else {
    const visualOffsets = getVisualOffsets(element);
    rect = {
      x: clippingAncestor.x - visualOffsets.x,
      y: clippingAncestor.y - visualOffsets.y,
      width: clippingAncestor.width,
      height: clippingAncestor.height
    };
  }
  return (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.rectToClientRect)(rect);
}
function hasFixedPositionAncestor(element, stopNode) {
  const parentNode = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getParentNode)(element);
  if (parentNode === stopNode || !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(parentNode) || (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isLastTraversableNode)(parentNode)) {
    return false;
  }
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(parentNode).position === 'fixed' || hasFixedPositionAncestor(parentNode, stopNode);
}

// A "clipping ancestor" is an `overflow` element with the characteristic of
// clipping (or hiding) child elements. This returns all clipping ancestors
// of the given element up the tree.
function getClippingElementAncestors(element, cache) {
  const cachedResult = cache.get(element);
  if (cachedResult) {
    return cachedResult;
  }
  let result = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getOverflowAncestors)(element, [], false).filter(el => (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(el) && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeName)(el) !== 'body');
  let currentContainingBlockComputedStyle = null;
  const elementIsFixed = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(element).position === 'fixed';
  let currentNode = elementIsFixed ? (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getParentNode)(element) : element;

  // https://developer.mozilla.org/en-US/docs/Web/CSS/Containing_block#identifying_the_containing_block
  while ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(currentNode) && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isLastTraversableNode)(currentNode)) {
    const computedStyle = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(currentNode);
    const currentNodeIsContaining = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isContainingBlock)(currentNode);
    if (!currentNodeIsContaining && computedStyle.position === 'fixed') {
      currentContainingBlockComputedStyle = null;
    }
    const shouldDropCurrentNode = elementIsFixed ? !currentNodeIsContaining && !currentContainingBlockComputedStyle : !currentNodeIsContaining && computedStyle.position === 'static' && !!currentContainingBlockComputedStyle && (currentContainingBlockComputedStyle.position === 'absolute' || currentContainingBlockComputedStyle.position === 'fixed') || (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isOverflowElement)(currentNode) && !currentNodeIsContaining && hasFixedPositionAncestor(element, currentNode);
    if (shouldDropCurrentNode) {
      // Drop non-containing blocks.
      result = result.filter(ancestor => ancestor !== currentNode);
    } else {
      // Record last containing block for next iteration.
      currentContainingBlockComputedStyle = computedStyle;
    }
    currentNode = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getParentNode)(currentNode);
  }
  cache.set(element, result);
  return result;
}

// Gets the maximum area that the element is visible in due to any number of
// clipping ancestors.
function getClippingRect(_ref) {
  let {
    element,
    boundary,
    rootBoundary,
    strategy
  } = _ref;
  const elementClippingAncestors = boundary === 'clippingAncestors' ? (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isTopLayer)(element) ? [] : getClippingElementAncestors(element, this._c) : [].concat(boundary);
  const clippingAncestors = [...elementClippingAncestors, rootBoundary];
  const firstRect = getClientRectFromClippingAncestor(element, clippingAncestors[0], strategy);
  let top = firstRect.top;
  let right = firstRect.right;
  let bottom = firstRect.bottom;
  let left = firstRect.left;
  for (let i = 1; i < clippingAncestors.length; i++) {
    const rect = getClientRectFromClippingAncestor(element, clippingAncestors[i], strategy);
    top = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.max)(rect.top, top);
    right = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.min)(rect.right, right);
    bottom = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.min)(rect.bottom, bottom);
    left = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.max)(rect.left, left);
  }
  return {
    width: right - left,
    height: bottom - top,
    x: left,
    y: top
  };
}

function getDimensions(element) {
  const {
    width,
    height
  } = getCssDimensions(element);
  return {
    width,
    height
  };
}

function getRectRelativeToOffsetParent(element, offsetParent, strategy) {
  const isOffsetParentAnElement = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(offsetParent);
  const documentElement = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(offsetParent);
  const isFixed = strategy === 'fixed';
  const rect = getBoundingClientRect(element, true, isFixed, offsetParent);
  let scroll = {
    scrollLeft: 0,
    scrollTop: 0
  };
  const offsets = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(0);

  // If the <body> scrollbar appears on the left (e.g. RTL systems). Use
  // Firefox with layout.scrollbar.side = 3 in about:config to test this.
  function setLeftRTLScrollbarOffset() {
    offsets.x = getWindowScrollBarX(documentElement);
  }
  if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
    if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeName)(offsetParent) !== 'body' || (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isOverflowElement)(documentElement)) {
      scroll = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getNodeScroll)(offsetParent);
    }
    if (isOffsetParentAnElement) {
      const offsetRect = getBoundingClientRect(offsetParent, true, isFixed, offsetParent);
      offsets.x = offsetRect.x + offsetParent.clientLeft;
      offsets.y = offsetRect.y + offsetParent.clientTop;
    } else if (documentElement) {
      setLeftRTLScrollbarOffset();
    }
  }
  if (isFixed && !isOffsetParentAnElement && documentElement) {
    setLeftRTLScrollbarOffset();
  }
  const htmlOffset = documentElement && !isOffsetParentAnElement && !isFixed ? getHTMLOffset(documentElement, scroll) : (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.createCoords)(0);
  const x = rect.left + scroll.scrollLeft - offsets.x - htmlOffset.x;
  const y = rect.top + scroll.scrollTop - offsets.y - htmlOffset.y;
  return {
    x,
    y,
    width: rect.width,
    height: rect.height
  };
}

function isStaticPositioned(element) {
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(element).position === 'static';
}

function getTrueOffsetParent(element, polyfill) {
  if (!(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(element) || (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(element).position === 'fixed') {
    return null;
  }
  if (polyfill) {
    return polyfill(element);
  }
  let rawOffsetParent = element.offsetParent;

  // Firefox returns the <html> element as the offsetParent if it's non-static,
  // while Chrome and Safari return the <body> element. The <body> element must
  // be used to perform the correct calculations even if the <html> element is
  // non-static.
  if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(element) === rawOffsetParent) {
    rawOffsetParent = rawOffsetParent.ownerDocument.body;
  }
  return rawOffsetParent;
}

// Gets the closest ancestor positioned element. Handles some edge cases,
// such as table ancestors and cross browser bugs.
function getOffsetParent(element, polyfill) {
  const win = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getWindow)(element);
  if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isTopLayer)(element)) {
    return win;
  }
  if (!(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isHTMLElement)(element)) {
    let svgOffsetParent = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getParentNode)(element);
    while (svgOffsetParent && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isLastTraversableNode)(svgOffsetParent)) {
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement)(svgOffsetParent) && !isStaticPositioned(svgOffsetParent)) {
        return svgOffsetParent;
      }
      svgOffsetParent = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getParentNode)(svgOffsetParent);
    }
    return win;
  }
  let offsetParent = getTrueOffsetParent(element, polyfill);
  while (offsetParent && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isTableElement)(offsetParent) && isStaticPositioned(offsetParent)) {
    offsetParent = getTrueOffsetParent(offsetParent, polyfill);
  }
  if (offsetParent && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isLastTraversableNode)(offsetParent) && isStaticPositioned(offsetParent) && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isContainingBlock)(offsetParent)) {
    return win;
  }
  return offsetParent || (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getContainingBlock)(element) || win;
}

const getElementRects = async function (data) {
  const getOffsetParentFn = this.getOffsetParent || getOffsetParent;
  const getDimensionsFn = this.getDimensions;
  const floatingDimensions = await getDimensionsFn(data.floating);
  return {
    reference: getRectRelativeToOffsetParent(data.reference, await getOffsetParentFn(data.floating), data.strategy),
    floating: {
      x: 0,
      y: 0,
      width: floatingDimensions.width,
      height: floatingDimensions.height
    }
  };
};

function isRTL(element) {
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getComputedStyle)(element).direction === 'rtl';
}

const platform = {
  convertOffsetParentRelativeRectToViewportRelativeRect,
  getDocumentElement: _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement,
  getClippingRect,
  getOffsetParent,
  getElementRects,
  getClientRects,
  getDimensions,
  getScale,
  isElement: _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.isElement,
  isRTL
};

function rectsAreEqual(a, b) {
  return a.x === b.x && a.y === b.y && a.width === b.width && a.height === b.height;
}

// https://samthor.au/2021/observing-dom/
function observeMove(element, onMove) {
  let io = null;
  let timeoutId;
  const root = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getDocumentElement)(element);
  function cleanup() {
    var _io;
    clearTimeout(timeoutId);
    (_io = io) == null || _io.disconnect();
    io = null;
  }
  function refresh(skip, threshold) {
    if (skip === void 0) {
      skip = false;
    }
    if (threshold === void 0) {
      threshold = 1;
    }
    cleanup();
    const elementRectForRootMargin = element.getBoundingClientRect();
    const {
      left,
      top,
      width,
      height
    } = elementRectForRootMargin;
    if (!skip) {
      onMove();
    }
    if (!width || !height) {
      return;
    }
    const insetTop = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.floor)(top);
    const insetRight = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.floor)(root.clientWidth - (left + width));
    const insetBottom = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.floor)(root.clientHeight - (top + height));
    const insetLeft = (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.floor)(left);
    const rootMargin = -insetTop + "px " + -insetRight + "px " + -insetBottom + "px " + -insetLeft + "px";
    const options = {
      rootMargin,
      threshold: (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.max)(0, (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_0__.min)(1, threshold)) || 1
    };
    let isFirstUpdate = true;
    function handleObserve(entries) {
      const ratio = entries[0].intersectionRatio;
      if (ratio !== threshold) {
        if (!isFirstUpdate) {
          return refresh();
        }
        if (!ratio) {
          // If the reference is clipped, the ratio is 0. Throttle the refresh
          // to prevent an infinite loop of updates.
          timeoutId = setTimeout(() => {
            refresh(false, 1e-7);
          }, 1000);
        } else {
          refresh(false, ratio);
        }
      }
      if (ratio === 1 && !rectsAreEqual(elementRectForRootMargin, element.getBoundingClientRect())) {
        // It's possible that even though the ratio is reported as 1, the
        // element is not actually fully within the IntersectionObserver's root
        // area anymore. This can happen under performance constraints. This may
        // be a bug in the browser's IntersectionObserver implementation. To
        // work around this, we compare the element's bounding rect now with
        // what it was at the time we created the IntersectionObserver. If they
        // are not equal then the element moved, so we refresh.
        refresh();
      }
      isFirstUpdate = false;
    }

    // Older browsers don't support a `document` as the root and will throw an
    // error.
    try {
      io = new IntersectionObserver(handleObserve, {
        ...options,
        // Handle <iframe>s
        root: root.ownerDocument
      });
    } catch (_e) {
      io = new IntersectionObserver(handleObserve, options);
    }
    io.observe(element);
  }
  refresh(true);
  return cleanup;
}

/**
 * Automatically updates the position of the floating element when necessary.
 * Should only be called when the floating element is mounted on the DOM or
 * visible on the screen.
 * @returns cleanup function that should be invoked when the floating element is
 * removed from the DOM or hidden from the screen.
 * @see https://floating-ui.com/docs/autoUpdate
 */
function autoUpdate(reference, floating, update, options) {
  if (options === void 0) {
    options = {};
  }
  const {
    ancestorScroll = true,
    ancestorResize = true,
    elementResize = typeof ResizeObserver === 'function',
    layoutShift = typeof IntersectionObserver === 'function',
    animationFrame = false
  } = options;
  const referenceEl = unwrapElement(reference);
  const ancestors = ancestorScroll || ancestorResize ? [...(referenceEl ? (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getOverflowAncestors)(referenceEl) : []), ...(floating ? (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_2__.getOverflowAncestors)(floating) : [])] : [];
  ancestors.forEach(ancestor => {
    ancestorScroll && ancestor.addEventListener('scroll', update, {
      passive: true
    });
    ancestorResize && ancestor.addEventListener('resize', update);
  });
  const cleanupIo = referenceEl && layoutShift ? observeMove(referenceEl, update) : null;
  let reobserveFrame = -1;
  let resizeObserver = null;
  if (elementResize) {
    resizeObserver = new ResizeObserver(_ref => {
      let [firstEntry] = _ref;
      if (firstEntry && firstEntry.target === referenceEl && resizeObserver && floating) {
        // Prevent update loops when using the `size` middleware.
        // https://github.com/floating-ui/floating-ui/issues/1740
        resizeObserver.unobserve(floating);
        cancelAnimationFrame(reobserveFrame);
        reobserveFrame = requestAnimationFrame(() => {
          var _resizeObserver;
          (_resizeObserver = resizeObserver) == null || _resizeObserver.observe(floating);
        });
      }
      update();
    });
    if (referenceEl && !animationFrame) {
      resizeObserver.observe(referenceEl);
    }
    if (floating) {
      resizeObserver.observe(floating);
    }
  }
  let frameId;
  let prevRefRect = animationFrame ? getBoundingClientRect(reference) : null;
  if (animationFrame) {
    frameLoop();
  }
  function frameLoop() {
    const nextRefRect = getBoundingClientRect(reference);
    if (prevRefRect && !rectsAreEqual(prevRefRect, nextRefRect)) {
      update();
    }
    prevRefRect = nextRefRect;
    frameId = requestAnimationFrame(frameLoop);
  }
  update();
  return () => {
    var _resizeObserver2;
    ancestors.forEach(ancestor => {
      ancestorScroll && ancestor.removeEventListener('scroll', update);
      ancestorResize && ancestor.removeEventListener('resize', update);
    });
    cleanupIo == null || cleanupIo();
    (_resizeObserver2 = resizeObserver) == null || _resizeObserver2.disconnect();
    resizeObserver = null;
    if (animationFrame) {
      cancelAnimationFrame(frameId);
    }
  };
}

/**
 * Resolves with an object of overflow side offsets that determine how much the
 * element is overflowing a given clipping boundary on each side.
 * - positive = overflowing the boundary by that number of pixels
 * - negative = how many pixels left before it will overflow
 * - 0 = lies flush with the boundary
 * @see https://floating-ui.com/docs/detectOverflow
 */
const detectOverflow = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.detectOverflow;

/**
 * Modifies the placement by translating the floating element along the
 * specified axes.
 * A number (shorthand for `mainAxis` or distance), or an axes configuration
 * object may be passed.
 * @see https://floating-ui.com/docs/offset
 */
const offset = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.offset;

/**
 * Optimizes the visibility of the floating element by choosing the placement
 * that has the most space available automatically, without needing to specify a
 * preferred placement. Alternative to `flip`.
 * @see https://floating-ui.com/docs/autoPlacement
 */
const autoPlacement = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.autoPlacement;

/**
 * Optimizes the visibility of the floating element by shifting it in order to
 * keep it in view when it will overflow the clipping boundary.
 * @see https://floating-ui.com/docs/shift
 */
const shift = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.shift;

/**
 * Optimizes the visibility of the floating element by flipping the `placement`
 * in order to keep it in view when the preferred placement(s) will overflow the
 * clipping boundary. Alternative to `autoPlacement`.
 * @see https://floating-ui.com/docs/flip
 */
const flip = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.flip;

/**
 * Provides data that allows you to change the size of the floating element —
 * for instance, prevent it from overflowing the clipping boundary or match the
 * width of the reference element.
 * @see https://floating-ui.com/docs/size
 */
const size = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.size;

/**
 * Provides data to hide the floating element in applicable situations, such as
 * when it is not in the same clipping context as the reference element.
 * @see https://floating-ui.com/docs/hide
 */
const hide = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.hide;

/**
 * Provides data to position an inner element of the floating element so that it
 * appears centered to the reference element.
 * @see https://floating-ui.com/docs/arrow
 */
const arrow = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.arrow;

/**
 * Provides improved positioning for inline reference elements that can span
 * over multiple lines, such as hyperlinks or range selections.
 * @see https://floating-ui.com/docs/inline
 */
const inline = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.inline;

/**
 * Built-in `limiter` that will stop `shift()` at a certain point.
 */
const limitShift = _floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.limitShift;

/**
 * Computes the `x` and `y` coordinates that will place the floating element
 * next to a given reference element.
 */
const computePosition = (reference, floating, options) => {
  // This caches the expensive `getClippingElementAncestors` function so that
  // multiple lifecycle resets re-use the same result. It only lives for a
  // single call. If other functions become expensive, we can add them as well.
  const cache = new Map();
  const mergedOptions = {
    platform,
    ...options
  };
  const platformWithCache = {
    ...mergedOptions.platform,
    _c: cache
  };
  return (0,_floating_ui_core__WEBPACK_IMPORTED_MODULE_1__.computePosition)(reference, floating, {
    ...mergedOptions,
    platform: platformWithCache
  });
};




/***/ }),

/***/ "./node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs":
/*!****************************************************************************!*\
  !*** ./node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs ***!
  \****************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   arrow: function() { return /* binding */ arrow; },
/* harmony export */   autoPlacement: function() { return /* binding */ autoPlacement; },
/* harmony export */   autoUpdate: function() { return /* reexport safe */ _floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.autoUpdate; },
/* harmony export */   computePosition: function() { return /* reexport safe */ _floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.computePosition; },
/* harmony export */   detectOverflow: function() { return /* reexport safe */ _floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.detectOverflow; },
/* harmony export */   flip: function() { return /* binding */ flip; },
/* harmony export */   getOverflowAncestors: function() { return /* reexport safe */ _floating_ui_dom__WEBPACK_IMPORTED_MODULE_1__.getOverflowAncestors; },
/* harmony export */   hide: function() { return /* binding */ hide; },
/* harmony export */   inline: function() { return /* binding */ inline; },
/* harmony export */   limitShift: function() { return /* binding */ limitShift; },
/* harmony export */   offset: function() { return /* binding */ offset; },
/* harmony export */   platform: function() { return /* reexport safe */ _floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.platform; },
/* harmony export */   shift: function() { return /* binding */ shift; },
/* harmony export */   size: function() { return /* binding */ size; },
/* harmony export */   useFloating: function() { return /* binding */ useFloating; }
/* harmony export */ });
/* harmony import */ var _floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @floating-ui/dom */ "./node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs");
/* harmony import */ var _floating_ui_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @floating-ui/dom */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react-dom */ "react-dom");






var isClient = typeof document !== 'undefined';

var noop = function noop() {};
var index = isClient ? react__WEBPACK_IMPORTED_MODULE_2__.useLayoutEffect : noop;

// Fork of `fast-deep-equal` that only does the comparisons we need and compares
// functions
function deepEqual(a, b) {
  if (a === b) {
    return true;
  }
  if (typeof a !== typeof b) {
    return false;
  }
  if (typeof a === 'function' && a.toString() === b.toString()) {
    return true;
  }
  let length;
  let i;
  let keys;
  if (a && b && typeof a === 'object') {
    if (Array.isArray(a)) {
      length = a.length;
      if (length !== b.length) return false;
      for (i = length; i-- !== 0;) {
        if (!deepEqual(a[i], b[i])) {
          return false;
        }
      }
      return true;
    }
    keys = Object.keys(a);
    length = keys.length;
    if (length !== Object.keys(b).length) {
      return false;
    }
    for (i = length; i-- !== 0;) {
      if (!{}.hasOwnProperty.call(b, keys[i])) {
        return false;
      }
    }
    for (i = length; i-- !== 0;) {
      const key = keys[i];
      if (key === '_owner' && a.$$typeof) {
        continue;
      }
      if (!deepEqual(a[key], b[key])) {
        return false;
      }
    }
    return true;
  }
  return a !== a && b !== b;
}

function getDPR(element) {
  if (typeof window === 'undefined') {
    return 1;
  }
  const win = element.ownerDocument.defaultView || window;
  return win.devicePixelRatio || 1;
}

function roundByDPR(element, value) {
  const dpr = getDPR(element);
  return Math.round(value * dpr) / dpr;
}

function useLatestRef(value) {
  const ref = react__WEBPACK_IMPORTED_MODULE_2__.useRef(value);
  index(() => {
    ref.current = value;
  });
  return ref;
}

/**
 * Provides data to position a floating element.
 * @see https://floating-ui.com/docs/useFloating
 */
function useFloating(options) {
  if (options === void 0) {
    options = {};
  }
  const {
    placement = 'bottom',
    strategy = 'absolute',
    middleware = [],
    platform,
    elements: {
      reference: externalReference,
      floating: externalFloating
    } = {},
    transform = true,
    whileElementsMounted,
    open
  } = options;
  const [data, setData] = react__WEBPACK_IMPORTED_MODULE_2__.useState({
    x: 0,
    y: 0,
    strategy,
    placement,
    middlewareData: {},
    isPositioned: false
  });
  const [latestMiddleware, setLatestMiddleware] = react__WEBPACK_IMPORTED_MODULE_2__.useState(middleware);
  if (!deepEqual(latestMiddleware, middleware)) {
    setLatestMiddleware(middleware);
  }
  const [_reference, _setReference] = react__WEBPACK_IMPORTED_MODULE_2__.useState(null);
  const [_floating, _setFloating] = react__WEBPACK_IMPORTED_MODULE_2__.useState(null);
  const setReference = react__WEBPACK_IMPORTED_MODULE_2__.useCallback(node => {
    if (node !== referenceRef.current) {
      referenceRef.current = node;
      _setReference(node);
    }
  }, []);
  const setFloating = react__WEBPACK_IMPORTED_MODULE_2__.useCallback(node => {
    if (node !== floatingRef.current) {
      floatingRef.current = node;
      _setFloating(node);
    }
  }, []);
  const referenceEl = externalReference || _reference;
  const floatingEl = externalFloating || _floating;
  const referenceRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef(null);
  const floatingRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef(null);
  const dataRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef(data);
  const hasWhileElementsMounted = whileElementsMounted != null;
  const whileElementsMountedRef = useLatestRef(whileElementsMounted);
  const platformRef = useLatestRef(platform);
  const openRef = useLatestRef(open);
  const update = react__WEBPACK_IMPORTED_MODULE_2__.useCallback(() => {
    if (!referenceRef.current || !floatingRef.current) {
      return;
    }
    const config = {
      placement,
      strategy,
      middleware: latestMiddleware
    };
    if (platformRef.current) {
      config.platform = platformRef.current;
    }
    (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.computePosition)(referenceRef.current, floatingRef.current, config).then(data => {
      const fullData = {
        ...data,
        // The floating element's position may be recomputed while it's closed
        // but still mounted (such as when transitioning out). To ensure
        // `isPositioned` will be `false` initially on the next open, avoid
        // setting it to `true` when `open === false` (must be specified).
        isPositioned: openRef.current !== false
      };
      if (isMountedRef.current && !deepEqual(dataRef.current, fullData)) {
        dataRef.current = fullData;
        react_dom__WEBPACK_IMPORTED_MODULE_3__.flushSync(() => {
          setData(fullData);
        });
      }
    });
  }, [latestMiddleware, placement, strategy, platformRef, openRef]);
  index(() => {
    if (open === false && dataRef.current.isPositioned) {
      dataRef.current.isPositioned = false;
      setData(data => ({
        ...data,
        isPositioned: false
      }));
    }
  }, [open]);
  const isMountedRef = react__WEBPACK_IMPORTED_MODULE_2__.useRef(false);
  index(() => {
    isMountedRef.current = true;
    return () => {
      isMountedRef.current = false;
    };
  }, []);
  index(() => {
    if (referenceEl) referenceRef.current = referenceEl;
    if (floatingEl) floatingRef.current = floatingEl;
    if (referenceEl && floatingEl) {
      if (whileElementsMountedRef.current) {
        return whileElementsMountedRef.current(referenceEl, floatingEl, update);
      }
      update();
    }
  }, [referenceEl, floatingEl, update, whileElementsMountedRef, hasWhileElementsMounted]);
  const refs = react__WEBPACK_IMPORTED_MODULE_2__.useMemo(() => ({
    reference: referenceRef,
    floating: floatingRef,
    setReference,
    setFloating
  }), [setReference, setFloating]);
  const elements = react__WEBPACK_IMPORTED_MODULE_2__.useMemo(() => ({
    reference: referenceEl,
    floating: floatingEl
  }), [referenceEl, floatingEl]);
  const floatingStyles = react__WEBPACK_IMPORTED_MODULE_2__.useMemo(() => {
    const initialStyles = {
      position: strategy,
      left: 0,
      top: 0
    };
    if (!elements.floating) {
      return initialStyles;
    }
    const x = roundByDPR(elements.floating, data.x);
    const y = roundByDPR(elements.floating, data.y);
    if (transform) {
      return {
        ...initialStyles,
        transform: "translate(" + x + "px, " + y + "px)",
        ...(getDPR(elements.floating) >= 1.5 && {
          willChange: 'transform'
        })
      };
    }
    return {
      position: strategy,
      left: x,
      top: y
    };
  }, [strategy, transform, elements.floating, data.x, data.y]);
  return react__WEBPACK_IMPORTED_MODULE_2__.useMemo(() => ({
    ...data,
    update,
    refs,
    elements,
    floatingStyles
  }), [data, update, refs, elements, floatingStyles]);
}

/**
 * Provides data to position an inner element of the floating element so that it
 * appears centered to the reference element.
 * This wraps the core `arrow` middleware to allow React refs as the element.
 * @see https://floating-ui.com/docs/arrow
 */
const arrow$1 = options => {
  function isRef(value) {
    return {}.hasOwnProperty.call(value, 'current');
  }
  return {
    name: 'arrow',
    options,
    fn(state) {
      const {
        element,
        padding
      } = typeof options === 'function' ? options(state) : options;
      if (element && isRef(element)) {
        if (element.current != null) {
          return (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.arrow)({
            element: element.current,
            padding
          }).fn(state);
        }
        return {};
      }
      if (element) {
        return (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.arrow)({
          element,
          padding
        }).fn(state);
      }
      return {};
    }
  };
};

/**
 * Modifies the placement by translating the floating element along the
 * specified axes.
 * A number (shorthand for `mainAxis` or distance), or an axes configuration
 * object may be passed.
 * @see https://floating-ui.com/docs/offset
 */
const offset = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.offset)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Optimizes the visibility of the floating element by shifting it in order to
 * keep it in view when it will overflow the clipping boundary.
 * @see https://floating-ui.com/docs/shift
 */
const shift = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.shift)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Built-in `limiter` that will stop `shift()` at a certain point.
 */
const limitShift = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.limitShift)(options);
  return {
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Optimizes the visibility of the floating element by flipping the `placement`
 * in order to keep it in view when the preferred placement(s) will overflow the
 * clipping boundary. Alternative to `autoPlacement`.
 * @see https://floating-ui.com/docs/flip
 */
const flip = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.flip)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Provides data that allows you to change the size of the floating element —
 * for instance, prevent it from overflowing the clipping boundary or match the
 * width of the reference element.
 * @see https://floating-ui.com/docs/size
 */
const size = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.size)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Optimizes the visibility of the floating element by choosing the placement
 * that has the most space available automatically, without needing to specify a
 * preferred placement. Alternative to `flip`.
 * @see https://floating-ui.com/docs/autoPlacement
 */
const autoPlacement = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.autoPlacement)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Provides data to hide the floating element in applicable situations, such as
 * when it is not in the same clipping context as the reference element.
 * @see https://floating-ui.com/docs/hide
 */
const hide = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.hide)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Provides improved positioning for inline reference elements that can span
 * over multiple lines, such as hyperlinks or range selections.
 * @see https://floating-ui.com/docs/inline
 */
const inline = (options, deps) => {
  const result = (0,_floating_ui_dom__WEBPACK_IMPORTED_MODULE_0__.inline)(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

/**
 * Provides data to position an inner element of the floating element so that it
 * appears centered to the reference element.
 * This wraps the core `arrow` middleware to allow React refs as the element.
 * @see https://floating-ui.com/docs/arrow
 */
const arrow = (options, deps) => {
  const result = arrow$1(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};




/***/ }),

/***/ "./node_modules/@floating-ui/react/dist/floating-ui.react.mjs":
/*!********************************************************************!*\
  !*** ./node_modules/@floating-ui/react/dist/floating-ui.react.mjs ***!
  \********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

var react__WEBPACK_IMPORTED_MODULE_0___namespace_cache;
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Composite: function() { return /* binding */ Composite; },
/* harmony export */   CompositeItem: function() { return /* binding */ CompositeItem; },
/* harmony export */   FloatingArrow: function() { return /* binding */ FloatingArrow; },
/* harmony export */   FloatingDelayGroup: function() { return /* binding */ FloatingDelayGroup; },
/* harmony export */   FloatingFocusManager: function() { return /* binding */ FloatingFocusManager; },
/* harmony export */   FloatingList: function() { return /* binding */ FloatingList; },
/* harmony export */   FloatingNode: function() { return /* binding */ FloatingNode; },
/* harmony export */   FloatingOverlay: function() { return /* binding */ FloatingOverlay; },
/* harmony export */   FloatingPortal: function() { return /* binding */ FloatingPortal; },
/* harmony export */   FloatingTree: function() { return /* binding */ FloatingTree; },
/* harmony export */   NextFloatingDelayGroup: function() { return /* binding */ NextFloatingDelayGroup; },
/* harmony export */   arrow: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.arrow; },
/* harmony export */   autoPlacement: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.autoPlacement; },
/* harmony export */   autoUpdate: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_7__.autoUpdate; },
/* harmony export */   computePosition: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_7__.computePosition; },
/* harmony export */   detectOverflow: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_7__.detectOverflow; },
/* harmony export */   flip: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.flip; },
/* harmony export */   getOverflowAncestors: function() { return /* reexport safe */ _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getOverflowAncestors; },
/* harmony export */   hide: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.hide; },
/* harmony export */   inline: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.inline; },
/* harmony export */   inner: function() { return /* binding */ inner; },
/* harmony export */   limitShift: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.limitShift; },
/* harmony export */   offset: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.offset; },
/* harmony export */   platform: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_7__.platform; },
/* harmony export */   safePolygon: function() { return /* binding */ safePolygon; },
/* harmony export */   shift: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.shift; },
/* harmony export */   size: function() { return /* reexport safe */ _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.size; },
/* harmony export */   useClick: function() { return /* binding */ useClick; },
/* harmony export */   useClientPoint: function() { return /* binding */ useClientPoint; },
/* harmony export */   useDelayGroup: function() { return /* binding */ useDelayGroup; },
/* harmony export */   useDelayGroupContext: function() { return /* binding */ useDelayGroupContext; },
/* harmony export */   useDismiss: function() { return /* binding */ useDismiss; },
/* harmony export */   useFloating: function() { return /* binding */ useFloating; },
/* harmony export */   useFloatingNodeId: function() { return /* binding */ useFloatingNodeId; },
/* harmony export */   useFloatingParentNodeId: function() { return /* binding */ useFloatingParentNodeId; },
/* harmony export */   useFloatingPortalNode: function() { return /* binding */ useFloatingPortalNode; },
/* harmony export */   useFloatingRootContext: function() { return /* binding */ useFloatingRootContext; },
/* harmony export */   useFloatingTree: function() { return /* binding */ useFloatingTree; },
/* harmony export */   useFocus: function() { return /* binding */ useFocus; },
/* harmony export */   useHover: function() { return /* binding */ useHover; },
/* harmony export */   useId: function() { return /* binding */ useId; },
/* harmony export */   useInnerOffset: function() { return /* binding */ useInnerOffset; },
/* harmony export */   useInteractions: function() { return /* binding */ useInteractions; },
/* harmony export */   useListItem: function() { return /* binding */ useListItem; },
/* harmony export */   useListNavigation: function() { return /* binding */ useListNavigation; },
/* harmony export */   useMergeRefs: function() { return /* binding */ useMergeRefs; },
/* harmony export */   useNextDelayGroup: function() { return /* binding */ useNextDelayGroup; },
/* harmony export */   useRole: function() { return /* binding */ useRole; },
/* harmony export */   useTransitionStatus: function() { return /* binding */ useTransitionStatus; },
/* harmony export */   useTransitionStyles: function() { return /* binding */ useTransitionStyles; },
/* harmony export */   useTypeahead: function() { return /* binding */ useTypeahead; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var _floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @floating-ui/react/utils */ "./node_modules/@floating-ui/react/dist/floating-ui.react.utils.mjs");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");
/* harmony import */ var _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @floating-ui/react-dom */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs");
/* harmony import */ var tabbable__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tabbable */ "./node_modules/tabbable/dist/index.esm.js");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @floating-ui/react-dom */ "./node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs");
/* harmony import */ var _floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @floating-ui/react-dom */ "./node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs");
/* harmony import */ var _floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @floating-ui/utils */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs");










/**
 * Merges an array of refs into a single memoized callback ref or `null`.
 * @see https://floating-ui.com/docs/react-utils#usemergerefs
 */
function useMergeRefs(refs) {
  const cleanupRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(undefined);
  const refEffect = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(instance => {
    const cleanups = refs.map(ref => {
      if (ref == null) {
        return;
      }
      if (typeof ref === 'function') {
        const refCallback = ref;
        const refCleanup = refCallback(instance);
        return typeof refCleanup === 'function' ? refCleanup : () => {
          refCallback(null);
        };
      }
      ref.current = instance;
      return () => {
        ref.current = null;
      };
    });
    return () => {
      cleanups.forEach(refCleanup => refCleanup == null ? void 0 : refCleanup());
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, refs);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    if (refs.every(ref => ref == null)) {
      return null;
    }
    return value => {
      if (cleanupRef.current) {
        cleanupRef.current();
        cleanupRef.current = undefined;
      }
      if (value != null) {
        cleanupRef.current = refEffect(value);
      }
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, refs);
}

function sortByDocumentPosition(a, b) {
  const position = a.compareDocumentPosition(b);
  if (position & Node.DOCUMENT_POSITION_FOLLOWING || position & Node.DOCUMENT_POSITION_CONTAINED_BY) {
    return -1;
  }
  if (position & Node.DOCUMENT_POSITION_PRECEDING || position & Node.DOCUMENT_POSITION_CONTAINS) {
    return 1;
  }
  return 0;
}
const FloatingListContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext({
  register: () => {},
  unregister: () => {},
  map: /*#__PURE__*/new Map(),
  elementsRef: {
    current: []
  }
});
/**
 * Provides context for a list of items within the floating element.
 * @see https://floating-ui.com/docs/FloatingList
 */
function FloatingList(props) {
  const {
    children,
    elementsRef,
    labelsRef
  } = props;
  const [nodes, setNodes] = react__WEBPACK_IMPORTED_MODULE_0__.useState(() => new Set());
  const register = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    setNodes(prevSet => new Set(prevSet).add(node));
  }, []);
  const unregister = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    setNodes(prevSet => {
      const set = new Set(prevSet);
      set.delete(node);
      return set;
    });
  }, []);
  const map = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    const newMap = new Map();
    const sortedNodes = Array.from(nodes.keys()).sort(sortByDocumentPosition);
    sortedNodes.forEach((node, index) => {
      newMap.set(node, index);
    });
    return newMap;
  }, [nodes]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FloatingListContext.Provider, {
    value: react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
      register,
      unregister,
      map,
      elementsRef,
      labelsRef
    }), [register, unregister, map, elementsRef, labelsRef]),
    children: children
  });
}
/**
 * Used to register a list item and its index (DOM position) in the
 * `FloatingList`.
 * @see https://floating-ui.com/docs/FloatingList#uselistitem
 */
function useListItem(props) {
  if (props === void 0) {
    props = {};
  }
  const {
    label
  } = props;
  const {
    register,
    unregister,
    map,
    elementsRef,
    labelsRef
  } = react__WEBPACK_IMPORTED_MODULE_0__.useContext(FloatingListContext);
  const [index, setIndex] = react__WEBPACK_IMPORTED_MODULE_0__.useState(null);
  const componentRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const ref = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    componentRef.current = node;
    if (index !== null) {
      elementsRef.current[index] = node;
      if (labelsRef) {
        var _node$textContent;
        const isLabelDefined = label !== undefined;
        labelsRef.current[index] = isLabelDefined ? label : (_node$textContent = node == null ? void 0 : node.textContent) != null ? _node$textContent : null;
      }
    }
  }, [index, elementsRef, labelsRef, label]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    const node = componentRef.current;
    if (node) {
      register(node);
      return () => {
        unregister(node);
      };
    }
  }, [register, unregister]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    const index = componentRef.current ? map.get(componentRef.current) : null;
    if (index != null) {
      setIndex(index);
    }
  }, [map]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    ref,
    index: index == null ? -1 : index
  }), [index, ref]);
}

const FOCUSABLE_ATTRIBUTE = 'data-floating-ui-focusable';
const ACTIVE_KEY = 'active';
const SELECTED_KEY = 'selected';
const ARROW_LEFT = 'ArrowLeft';
const ARROW_RIGHT = 'ArrowRight';
const ARROW_UP = 'ArrowUp';
const ARROW_DOWN = 'ArrowDown';

function renderJsx(render, computedProps) {
  if (typeof render === 'function') {
    return render(computedProps);
  }
  if (render) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.cloneElement(render, computedProps);
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("div", {
    ...computedProps
  });
}
const CompositeContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext({
  activeIndex: 0,
  onNavigate: () => {}
});
const horizontalKeys = [ARROW_LEFT, ARROW_RIGHT];
const verticalKeys = [ARROW_UP, ARROW_DOWN];
const allKeys = [...horizontalKeys, ...verticalKeys];

/**
 * Creates a single tab stop whose items are navigated by arrow keys, which
 * provides list navigation outside of floating element contexts.
 *
 * This is useful to enable navigation of a list of items that aren’t part of a
 * floating element. A menubar is an example of a composite, with each reference
 * element being an item.
 * @see https://floating-ui.com/docs/Composite
 */
const Composite = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(function Composite(props, forwardedRef) {
  const {
    render,
    orientation = 'both',
    loop = true,
    rtl = false,
    cols = 1,
    disabledIndices,
    activeIndex: externalActiveIndex,
    onNavigate: externalSetActiveIndex,
    itemSizes,
    dense = false,
    ...domProps
  } = props;
  const [internalActiveIndex, internalSetActiveIndex] = react__WEBPACK_IMPORTED_MODULE_0__.useState(0);
  const activeIndex = externalActiveIndex != null ? externalActiveIndex : internalActiveIndex;
  const onNavigate = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(externalSetActiveIndex != null ? externalSetActiveIndex : internalSetActiveIndex);
  const elementsRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef([]);
  const renderElementProps = render && typeof render !== 'function' ? render.props : {};
  const contextValue = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    activeIndex,
    onNavigate
  }), [activeIndex, onNavigate]);
  const isGrid = cols > 1;
  function handleKeyDown(event) {
    if (!allKeys.includes(event.key)) return;
    let nextIndex = activeIndex;
    const minIndex = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMinListIndex)(elementsRef, disabledIndices);
    const maxIndex = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMaxListIndex)(elementsRef, disabledIndices);
    const horizontalEndKey = rtl ? ARROW_LEFT : ARROW_RIGHT;
    const horizontalStartKey = rtl ? ARROW_RIGHT : ARROW_LEFT;
    if (isGrid) {
      const sizes = itemSizes || Array.from({
        length: elementsRef.current.length
      }, () => ({
        width: 1,
        height: 1
      }));
      // To calculate movements on the grid, we use hypothetical cell indices
      // as if every item was 1x1, then convert back to real indices.
      const cellMap = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.createGridCellMap)(sizes, cols, dense);
      const minGridIndex = cellMap.findIndex(index => index != null && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isListIndexDisabled)(elementsRef, index, disabledIndices));
      // last enabled index
      const maxGridIndex = cellMap.reduce((foundIndex, index, cellIndex) => index != null && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isListIndexDisabled)(elementsRef, index, disabledIndices) ? cellIndex : foundIndex, -1);
      const maybeNextIndex = cellMap[(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getGridNavigatedIndex)({
        current: cellMap.map(itemIndex => itemIndex ? elementsRef.current[itemIndex] : null)
      }, {
        event,
        orientation,
        loop,
        rtl,
        cols,
        // treat undefined (empty grid spaces) as disabled indices so we
        // don't end up in them
        disabledIndices: (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getGridCellIndices)([...((typeof disabledIndices !== 'function' ? disabledIndices : null) || elementsRef.current.map((_, index) => (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isListIndexDisabled)(elementsRef, index, disabledIndices) ? index : undefined)), undefined], cellMap),
        minIndex: minGridIndex,
        maxIndex: maxGridIndex,
        prevIndex: (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getGridCellIndexOfCorner)(activeIndex > maxIndex ? minIndex : activeIndex, sizes, cellMap, cols,
        // use a corner matching the edge closest to the direction we're
        // moving in so we don't end up in the same item. Prefer
        // top/left over bottom/right.
        event.key === ARROW_DOWN ? 'bl' : event.key === horizontalEndKey ? 'tr' : 'tl')
      })];
      if (maybeNextIndex != null) {
        nextIndex = maybeNextIndex;
      }
    }
    const toEndKeys = {
      horizontal: [horizontalEndKey],
      vertical: [ARROW_DOWN],
      both: [horizontalEndKey, ARROW_DOWN]
    }[orientation];
    const toStartKeys = {
      horizontal: [horizontalStartKey],
      vertical: [ARROW_UP],
      both: [horizontalStartKey, ARROW_UP]
    }[orientation];
    const preventedKeys = isGrid ? allKeys : {
      horizontal: horizontalKeys,
      vertical: verticalKeys,
      both: allKeys
    }[orientation];
    if (nextIndex === activeIndex && [...toEndKeys, ...toStartKeys].includes(event.key)) {
      if (loop && nextIndex === maxIndex && toEndKeys.includes(event.key)) {
        nextIndex = minIndex;
      } else if (loop && nextIndex === minIndex && toStartKeys.includes(event.key)) {
        nextIndex = maxIndex;
      } else {
        nextIndex = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.findNonDisabledListIndex)(elementsRef, {
          startingIndex: nextIndex,
          decrement: toStartKeys.includes(event.key),
          disabledIndices
        });
      }
    }
    if (nextIndex !== activeIndex && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isIndexOutOfListBounds)(elementsRef, nextIndex)) {
      var _elementsRef$current$;
      event.stopPropagation();
      if (preventedKeys.includes(event.key)) {
        event.preventDefault();
      }
      onNavigate(nextIndex);
      (_elementsRef$current$ = elementsRef.current[nextIndex]) == null || _elementsRef$current$.focus();
    }
  }
  const computedProps = {
    ...domProps,
    ...renderElementProps,
    ref: forwardedRef,
    'aria-orientation': orientation === 'both' ? undefined : orientation,
    onKeyDown(e) {
      domProps.onKeyDown == null || domProps.onKeyDown(e);
      renderElementProps.onKeyDown == null || renderElementProps.onKeyDown(e);
      handleKeyDown(e);
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(CompositeContext.Provider, {
    value: contextValue,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FloatingList, {
      elementsRef: elementsRef,
      children: renderJsx(render, computedProps)
    })
  });
});
/**
 * @see https://floating-ui.com/docs/Composite
 */
const CompositeItem = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(function CompositeItem(props, forwardedRef) {
  const {
    render,
    ...domProps
  } = props;
  const renderElementProps = render && typeof render !== 'function' ? render.props : {};
  const {
    activeIndex,
    onNavigate
  } = react__WEBPACK_IMPORTED_MODULE_0__.useContext(CompositeContext);
  const {
    ref,
    index
  } = useListItem();
  const mergedRef = useMergeRefs([ref, forwardedRef, renderElementProps.ref]);
  const isActive = activeIndex === index;
  const computedProps = {
    ...domProps,
    ...renderElementProps,
    ref: mergedRef,
    tabIndex: isActive ? 0 : -1,
    'data-active': isActive ? '' : undefined,
    onFocus(e) {
      domProps.onFocus == null || domProps.onFocus(e);
      renderElementProps.onFocus == null || renderElementProps.onFocus(e);
      onNavigate(index);
    }
  };
  return renderJsx(render, computedProps);
});

// https://github.com/mui/material-ui/issues/41190#issuecomment-2040873379
const SafeReact = {
  .../*#__PURE__*/ (react__WEBPACK_IMPORTED_MODULE_0___namespace_cache || (react__WEBPACK_IMPORTED_MODULE_0___namespace_cache = __webpack_require__.t(react__WEBPACK_IMPORTED_MODULE_0__, 2)))
};

let serverHandoffComplete = false;
let count = 0;
const genId = () => // Ensure the id is unique with multiple independent versions of Floating UI
// on <React 18
"floating-ui-" + Math.random().toString(36).slice(2, 6) + count++;
function useFloatingId() {
  const [id, setId] = react__WEBPACK_IMPORTED_MODULE_0__.useState(() => serverHandoffComplete ? genId() : undefined);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (id == null) {
      setId(genId());
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    serverHandoffComplete = true;
  }, []);
  return id;
}
const useReactId = SafeReact.useId;

/**
 * Uses React 18's built-in `useId()` when available, or falls back to a
 * slightly less performant (requiring a double render) implementation for
 * earlier React versions.
 * @see https://floating-ui.com/docs/react-utils#useid
 */
const useId = useReactId || useFloatingId;

let devMessageSet;
if (true) {
  devMessageSet = /*#__PURE__*/new Set();
}
function warn() {
  var _devMessageSet;
  for (var _len = arguments.length, messages = new Array(_len), _key = 0; _key < _len; _key++) {
    messages[_key] = arguments[_key];
  }
  const message = "Floating UI: " + messages.join(' ');
  if (!((_devMessageSet = devMessageSet) != null && _devMessageSet.has(message))) {
    var _devMessageSet2;
    (_devMessageSet2 = devMessageSet) == null || _devMessageSet2.add(message);
    console.warn(message);
  }
}
function error() {
  var _devMessageSet3;
  for (var _len2 = arguments.length, messages = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
    messages[_key2] = arguments[_key2];
  }
  const message = "Floating UI: " + messages.join(' ');
  if (!((_devMessageSet3 = devMessageSet) != null && _devMessageSet3.has(message))) {
    var _devMessageSet4;
    (_devMessageSet4 = devMessageSet) == null || _devMessageSet4.add(message);
    console.error(message);
  }
}

/**
 * Renders a pointing arrow triangle.
 * @see https://floating-ui.com/docs/FloatingArrow
 */
const FloatingArrow = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(function FloatingArrow(props, ref) {
  const {
    context: {
      placement,
      elements: {
        floating
      },
      middlewareData: {
        arrow,
        shift
      }
    },
    width = 14,
    height = 7,
    tipRadius = 0,
    strokeWidth = 0,
    staticOffset,
    stroke,
    d,
    style: {
      transform,
      ...restStyle
    } = {},
    ...rest
  } = props;
  if (true) {
    if (!ref) {
      warn('The `ref` prop is required for `FloatingArrow`.');
    }
  }
  const clipPathId = useId();
  const [isRTL, setIsRTL] = react__WEBPACK_IMPORTED_MODULE_0__.useState(false);

  // https://github.com/floating-ui/floating-ui/issues/2932
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!floating) return;
    const isRTL = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getComputedStyle)(floating).direction === 'rtl';
    if (isRTL) {
      setIsRTL(true);
    }
  }, [floating]);
  if (!floating) {
    return null;
  }
  const [side, alignment] = placement.split('-');
  const isVerticalSide = side === 'top' || side === 'bottom';
  let computedStaticOffset = staticOffset;
  if (isVerticalSide && shift != null && shift.x || !isVerticalSide && shift != null && shift.y) {
    computedStaticOffset = null;
  }

  // Strokes must be double the border width, this ensures the stroke's width
  // works as you'd expect.
  const computedStrokeWidth = strokeWidth * 2;
  const halfStrokeWidth = computedStrokeWidth / 2;
  const svgX = width / 2 * (tipRadius / -8 + 1);
  const svgY = height / 2 * tipRadius / 4;
  const isCustomShape = !!d;
  const yOffsetProp = computedStaticOffset && alignment === 'end' ? 'bottom' : 'top';
  let xOffsetProp = computedStaticOffset && alignment === 'end' ? 'right' : 'left';
  if (computedStaticOffset && isRTL) {
    xOffsetProp = alignment === 'end' ? 'left' : 'right';
  }
  const arrowX = (arrow == null ? void 0 : arrow.x) != null ? computedStaticOffset || arrow.x : '';
  const arrowY = (arrow == null ? void 0 : arrow.y) != null ? computedStaticOffset || arrow.y : '';
  const dValue = d || 'M0,0' + (" H" + width) + (" L" + (width - svgX) + "," + (height - svgY)) + (" Q" + width / 2 + "," + height + " " + svgX + "," + (height - svgY)) + ' Z';
  const rotation = {
    top: isCustomShape ? 'rotate(180deg)' : '',
    left: isCustomShape ? 'rotate(90deg)' : 'rotate(-90deg)',
    bottom: isCustomShape ? '' : 'rotate(180deg)',
    right: isCustomShape ? 'rotate(-90deg)' : 'rotate(90deg)'
  }[side];
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("svg", {
    ...rest,
    "aria-hidden": true,
    ref: ref,
    width: isCustomShape ? width : width + computedStrokeWidth,
    height: width,
    viewBox: "0 0 " + width + " " + (height > width ? height : width),
    style: {
      position: 'absolute',
      pointerEvents: 'none',
      [xOffsetProp]: arrowX,
      [yOffsetProp]: arrowY,
      [side]: isVerticalSide || isCustomShape ? '100%' : "calc(100% - " + computedStrokeWidth / 2 + "px)",
      transform: [rotation, transform].filter(t => !!t).join(' '),
      ...restStyle
    },
    children: [computedStrokeWidth > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("path", {
      clipPath: "url(#" + clipPathId + ")",
      fill: "none",
      stroke: stroke
      // Account for the stroke on the fill path rendered below.
      ,
      strokeWidth: computedStrokeWidth + (d ? 0 : 1),
      d: dValue
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("path", {
      stroke: computedStrokeWidth && !d ? rest.fill : 'none',
      d: dValue
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("clipPath", {
      id: clipPathId,
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("rect", {
        x: -halfStrokeWidth,
        y: halfStrokeWidth * (isCustomShape ? -1 : 1),
        width: width + computedStrokeWidth,
        height: width
      })
    })]
  });
});

function createEventEmitter() {
  const map = new Map();
  return {
    emit(event, data) {
      var _map$get;
      (_map$get = map.get(event)) == null || _map$get.forEach(listener => listener(data));
    },
    on(event, listener) {
      if (!map.has(event)) {
        map.set(event, new Set());
      }
      map.get(event).add(listener);
    },
    off(event, listener) {
      var _map$get2;
      (_map$get2 = map.get(event)) == null || _map$get2.delete(listener);
    }
  };
}

const FloatingNodeContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext(null);
const FloatingTreeContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext(null);

/**
 * Returns the parent node id for nested floating elements, if available.
 * Returns `null` for top-level floating elements.
 */
const useFloatingParentNodeId = () => {
  var _React$useContext;
  return ((_React$useContext = react__WEBPACK_IMPORTED_MODULE_0__.useContext(FloatingNodeContext)) == null ? void 0 : _React$useContext.id) || null;
};

/**
 * Returns the nearest floating tree context, if available.
 */
const useFloatingTree = () => react__WEBPACK_IMPORTED_MODULE_0__.useContext(FloatingTreeContext);

/**
 * Registers a node into the `FloatingTree`, returning its id.
 * @see https://floating-ui.com/docs/FloatingTree
 */
function useFloatingNodeId(customParentId) {
  const id = useId();
  const tree = useFloatingTree();
  const reactParentId = useFloatingParentNodeId();
  const parentId = customParentId || reactParentId;
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!id) return;
    const node = {
      id,
      parentId
    };
    tree == null || tree.addNode(node);
    return () => {
      tree == null || tree.removeNode(node);
    };
  }, [tree, id, parentId]);
  return id;
}
/**
 * Provides parent node context for nested floating elements.
 * @see https://floating-ui.com/docs/FloatingTree
 */
function FloatingNode(props) {
  const {
    children,
    id
  } = props;
  const parentId = useFloatingParentNodeId();
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FloatingNodeContext.Provider, {
    value: react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
      id,
      parentId
    }), [id, parentId]),
    children: children
  });
}
/**
 * Provides context for nested floating elements when they are not children of
 * each other on the DOM.
 * This is not necessary in all cases, except when there must be explicit communication between parent and child floating elements. It is necessary for:
 * - The `bubbles` option in the `useDismiss()` Hook
 * - Nested virtual list navigation
 * - Nested floating elements that each open on hover
 * - Custom communication between parent and child floating elements
 * @see https://floating-ui.com/docs/FloatingTree
 */
function FloatingTree(props) {
  const {
    children
  } = props;
  const nodesRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef([]);
  const addNode = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    nodesRef.current = [...nodesRef.current, node];
  }, []);
  const removeNode = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    nodesRef.current = nodesRef.current.filter(n => n !== node);
  }, []);
  const [events] = react__WEBPACK_IMPORTED_MODULE_0__.useState(() => createEventEmitter());
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FloatingTreeContext.Provider, {
    value: react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
      nodesRef,
      addNode,
      removeNode,
      events
    }), [addNode, removeNode, events]),
    children: children
  });
}

function createAttribute(name) {
  return "data-floating-ui-" + name;
}

function clearTimeoutIfSet(timeoutRef) {
  if (timeoutRef.current !== -1) {
    clearTimeout(timeoutRef.current);
    timeoutRef.current = -1;
  }
}

const safePolygonIdentifier = /*#__PURE__*/createAttribute('safe-polygon');
function getDelay(value, prop, pointerType) {
  if (pointerType && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMouseLikePointerType)(pointerType)) {
    return 0;
  }
  if (typeof value === 'number') {
    return value;
  }
  if (typeof value === 'function') {
    const result = value();
    if (typeof result === 'number') {
      return result;
    }
    return result == null ? void 0 : result[prop];
  }
  return value == null ? void 0 : value[prop];
}
function getRestMs(value) {
  if (typeof value === 'function') {
    return value();
  }
  return value;
}
/**
 * Opens the floating element while hovering over the reference element, like
 * CSS `:hover`.
 * @see https://floating-ui.com/docs/useHover
 */
function useHover(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    onOpenChange,
    dataRef,
    events,
    elements
  } = context;
  const {
    enabled = true,
    delay = 0,
    handleClose = null,
    mouseOnly = false,
    restMs = 0,
    move = true
  } = props;
  const tree = useFloatingTree();
  const parentId = useFloatingParentNodeId();
  const handleCloseRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(handleClose);
  const delayRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(delay);
  const openRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(open);
  const restMsRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(restMs);
  const pointerTypeRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef();
  const timeoutRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  const handlerRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef();
  const restTimeoutRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  const blockMouseMoveRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(true);
  const performedPointerEventsMutationRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const unbindMouseMoveRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(() => {});
  const restTimeoutPendingRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const isHoverOpen = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    var _dataRef$current$open;
    const type = (_dataRef$current$open = dataRef.current.openEvent) == null ? void 0 : _dataRef$current$open.type;
    return (type == null ? void 0 : type.includes('mouse')) && type !== 'mousedown';
  });

  // When closing before opening, clear the delay timeouts to cancel it
  // from showing.
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled) return;
    function onOpenChange(_ref) {
      let {
        open
      } = _ref;
      if (!open) {
        clearTimeoutIfSet(timeoutRef);
        clearTimeoutIfSet(restTimeoutRef);
        blockMouseMoveRef.current = true;
        restTimeoutPendingRef.current = false;
      }
    }
    events.on('openchange', onOpenChange);
    return () => {
      events.off('openchange', onOpenChange);
    };
  }, [enabled, events]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled) return;
    if (!handleCloseRef.current) return;
    if (!open) return;
    function onLeave(event) {
      if (isHoverOpen()) {
        onOpenChange(false, event, 'hover');
      }
    }
    const html = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating).documentElement;
    html.addEventListener('mouseleave', onLeave);
    return () => {
      html.removeEventListener('mouseleave', onLeave);
    };
  }, [elements.floating, open, onOpenChange, enabled, handleCloseRef, isHoverOpen]);
  const closeWithDelay = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(function (event, runElseBranch, reason) {
    if (runElseBranch === void 0) {
      runElseBranch = true;
    }
    if (reason === void 0) {
      reason = 'hover';
    }
    const closeDelay = getDelay(delayRef.current, 'close', pointerTypeRef.current);
    if (closeDelay && !handlerRef.current) {
      clearTimeoutIfSet(timeoutRef);
      timeoutRef.current = window.setTimeout(() => onOpenChange(false, event, reason), closeDelay);
    } else if (runElseBranch) {
      clearTimeoutIfSet(timeoutRef);
      onOpenChange(false, event, reason);
    }
  }, [delayRef, onOpenChange]);
  const cleanupMouseMoveHandler = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    unbindMouseMoveRef.current();
    handlerRef.current = undefined;
  });
  const clearPointerEvents = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    if (performedPointerEventsMutationRef.current) {
      const body = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating).body;
      body.style.pointerEvents = '';
      body.removeAttribute(safePolygonIdentifier);
      performedPointerEventsMutationRef.current = false;
    }
  });
  const isClickLikeOpenEvent = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    return dataRef.current.openEvent ? ['click', 'mousedown'].includes(dataRef.current.openEvent.type) : false;
  });

  // Registering the mouse events on the reference directly to bypass React's
  // delegation system. If the cursor was on a disabled element and then entered
  // the reference (no gap), `mouseenter` doesn't fire in the delegation system.
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled) return;
    function onReferenceMouseEnter(event) {
      clearTimeoutIfSet(timeoutRef);
      blockMouseMoveRef.current = false;
      if (mouseOnly && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMouseLikePointerType)(pointerTypeRef.current) || getRestMs(restMsRef.current) > 0 && !getDelay(delayRef.current, 'open')) {
        return;
      }
      const openDelay = getDelay(delayRef.current, 'open', pointerTypeRef.current);
      if (openDelay) {
        timeoutRef.current = window.setTimeout(() => {
          if (!openRef.current) {
            onOpenChange(true, event, 'hover');
          }
        }, openDelay);
      } else if (!open) {
        onOpenChange(true, event, 'hover');
      }
    }
    function onReferenceMouseLeave(event) {
      if (isClickLikeOpenEvent()) {
        clearPointerEvents();
        return;
      }
      unbindMouseMoveRef.current();
      const doc = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating);
      clearTimeoutIfSet(restTimeoutRef);
      restTimeoutPendingRef.current = false;
      if (handleCloseRef.current && dataRef.current.floatingContext) {
        // Prevent clearing `onScrollMouseLeave` timeout.
        if (!open) {
          clearTimeoutIfSet(timeoutRef);
        }
        handlerRef.current = handleCloseRef.current({
          ...dataRef.current.floatingContext,
          tree,
          x: event.clientX,
          y: event.clientY,
          onClose() {
            clearPointerEvents();
            cleanupMouseMoveHandler();
            if (!isClickLikeOpenEvent()) {
              closeWithDelay(event, true, 'safe-polygon');
            }
          }
        });
        const handler = handlerRef.current;
        doc.addEventListener('mousemove', handler);
        unbindMouseMoveRef.current = () => {
          doc.removeEventListener('mousemove', handler);
        };
        return;
      }

      // Allow interactivity without `safePolygon` on touch devices. With a
      // pointer, a short close delay is an alternative, so it should work
      // consistently.
      const shouldClose = pointerTypeRef.current === 'touch' ? !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(elements.floating, event.relatedTarget) : true;
      if (shouldClose) {
        closeWithDelay(event);
      }
    }

    // Ensure the floating element closes after scrolling even if the pointer
    // did not move.
    // https://github.com/floating-ui/floating-ui/discussions/1692
    function onScrollMouseLeave(event) {
      if (isClickLikeOpenEvent()) return;
      if (!dataRef.current.floatingContext) return;
      handleCloseRef.current == null || handleCloseRef.current({
        ...dataRef.current.floatingContext,
        tree,
        x: event.clientX,
        y: event.clientY,
        onClose() {
          clearPointerEvents();
          cleanupMouseMoveHandler();
          if (!isClickLikeOpenEvent()) {
            closeWithDelay(event);
          }
        }
      })(event);
    }
    function onFloatingMouseEnter() {
      clearTimeoutIfSet(timeoutRef);
    }
    function onFloatingMouseLeave(event) {
      if (!isClickLikeOpenEvent()) {
        closeWithDelay(event, false);
      }
    }
    if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(elements.domReference)) {
      const reference = elements.domReference;
      const floating = elements.floating;
      if (open) {
        reference.addEventListener('mouseleave', onScrollMouseLeave);
      }
      if (move) {
        reference.addEventListener('mousemove', onReferenceMouseEnter, {
          once: true
        });
      }
      reference.addEventListener('mouseenter', onReferenceMouseEnter);
      reference.addEventListener('mouseleave', onReferenceMouseLeave);
      if (floating) {
        floating.addEventListener('mouseleave', onScrollMouseLeave);
        floating.addEventListener('mouseenter', onFloatingMouseEnter);
        floating.addEventListener('mouseleave', onFloatingMouseLeave);
      }
      return () => {
        if (open) {
          reference.removeEventListener('mouseleave', onScrollMouseLeave);
        }
        if (move) {
          reference.removeEventListener('mousemove', onReferenceMouseEnter);
        }
        reference.removeEventListener('mouseenter', onReferenceMouseEnter);
        reference.removeEventListener('mouseleave', onReferenceMouseLeave);
        if (floating) {
          floating.removeEventListener('mouseleave', onScrollMouseLeave);
          floating.removeEventListener('mouseenter', onFloatingMouseEnter);
          floating.removeEventListener('mouseleave', onFloatingMouseLeave);
        }
      };
    }
  }, [elements, enabled, context, mouseOnly, move, closeWithDelay, cleanupMouseMoveHandler, clearPointerEvents, onOpenChange, open, openRef, tree, delayRef, handleCloseRef, dataRef, isClickLikeOpenEvent, restMsRef]);

  // Block pointer-events of every element other than the reference and floating
  // while the floating element is open and has a `handleClose` handler. Also
  // handles nested floating elements.
  // https://github.com/floating-ui/floating-ui/issues/1722
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    var _handleCloseRef$curre;
    if (!enabled) return;
    if (open && (_handleCloseRef$curre = handleCloseRef.current) != null && (_handleCloseRef$curre = _handleCloseRef$curre.__options) != null && _handleCloseRef$curre.blockPointerEvents && isHoverOpen()) {
      performedPointerEventsMutationRef.current = true;
      const floatingEl = elements.floating;
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(elements.domReference) && floatingEl) {
        var _tree$nodesRef$curren;
        const body = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating).body;
        body.setAttribute(safePolygonIdentifier, '');
        const ref = elements.domReference;
        const parentFloating = tree == null || (_tree$nodesRef$curren = tree.nodesRef.current.find(node => node.id === parentId)) == null || (_tree$nodesRef$curren = _tree$nodesRef$curren.context) == null ? void 0 : _tree$nodesRef$curren.elements.floating;
        if (parentFloating) {
          parentFloating.style.pointerEvents = '';
        }
        body.style.pointerEvents = 'none';
        ref.style.pointerEvents = 'auto';
        floatingEl.style.pointerEvents = 'auto';
        return () => {
          body.style.pointerEvents = '';
          ref.style.pointerEvents = '';
          floatingEl.style.pointerEvents = '';
        };
      }
    }
  }, [enabled, open, parentId, elements, tree, handleCloseRef, isHoverOpen]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!open) {
      pointerTypeRef.current = undefined;
      restTimeoutPendingRef.current = false;
      cleanupMouseMoveHandler();
      clearPointerEvents();
    }
  }, [open, cleanupMouseMoveHandler, clearPointerEvents]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    return () => {
      cleanupMouseMoveHandler();
      clearTimeoutIfSet(timeoutRef);
      clearTimeoutIfSet(restTimeoutRef);
      clearPointerEvents();
    };
  }, [enabled, elements.domReference, cleanupMouseMoveHandler, clearPointerEvents]);
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    function setPointerRef(event) {
      pointerTypeRef.current = event.pointerType;
    }
    return {
      onPointerDown: setPointerRef,
      onPointerEnter: setPointerRef,
      onMouseMove(event) {
        const {
          nativeEvent
        } = event;
        function handleMouseMove() {
          if (!blockMouseMoveRef.current && !openRef.current) {
            onOpenChange(true, nativeEvent, 'hover');
          }
        }
        if (mouseOnly && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMouseLikePointerType)(pointerTypeRef.current)) {
          return;
        }
        if (open || getRestMs(restMsRef.current) === 0) {
          return;
        }

        // Ignore insignificant movements to account for tremors.
        if (restTimeoutPendingRef.current && event.movementX ** 2 + event.movementY ** 2 < 2) {
          return;
        }
        clearTimeoutIfSet(restTimeoutRef);
        if (pointerTypeRef.current === 'touch') {
          handleMouseMove();
        } else {
          restTimeoutPendingRef.current = true;
          restTimeoutRef.current = window.setTimeout(handleMouseMove, getRestMs(restMsRef.current));
        }
      }
    };
  }, [mouseOnly, onOpenChange, open, openRef, restMsRef]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference
  } : {}, [enabled, reference]);
}

const NOOP = () => {};
const FloatingDelayGroupContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext({
  delay: 0,
  initialDelay: 0,
  timeoutMs: 0,
  currentId: null,
  setCurrentId: NOOP,
  setState: NOOP,
  isInstantPhase: false
});

/**
 * @deprecated
 * Use the return value of `useDelayGroup()` instead.
 */
const useDelayGroupContext = () => react__WEBPACK_IMPORTED_MODULE_0__.useContext(FloatingDelayGroupContext);
/**
 * Provides context for a group of floating elements that should share a
 * `delay`.
 * @see https://floating-ui.com/docs/FloatingDelayGroup
 */
function FloatingDelayGroup(props) {
  const {
    children,
    delay,
    timeoutMs = 0
  } = props;
  const [state, setState] = react__WEBPACK_IMPORTED_MODULE_0__.useReducer((prev, next) => ({
    ...prev,
    ...next
  }), {
    delay,
    timeoutMs,
    initialDelay: delay,
    currentId: null,
    isInstantPhase: false
  });
  const initialCurrentIdRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const setCurrentId = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(currentId => {
    setState({
      currentId
    });
  }, []);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (state.currentId) {
      if (initialCurrentIdRef.current === null) {
        initialCurrentIdRef.current = state.currentId;
      } else if (!state.isInstantPhase) {
        setState({
          isInstantPhase: true
        });
      }
    } else {
      if (state.isInstantPhase) {
        setState({
          isInstantPhase: false
        });
      }
      initialCurrentIdRef.current = null;
    }
  }, [state.currentId, state.isInstantPhase]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FloatingDelayGroupContext.Provider, {
    value: react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
      ...state,
      setState,
      setCurrentId
    }), [state, setCurrentId]),
    children: children
  });
}
/**
 * Enables grouping when called inside a component that's a child of a
 * `FloatingDelayGroup`.
 * @see https://floating-ui.com/docs/FloatingDelayGroup
 */
function useDelayGroup(context, options) {
  if (options === void 0) {
    options = {};
  }
  const {
    open,
    onOpenChange,
    floatingId
  } = context;
  const {
    id: optionId,
    enabled = true
  } = options;
  const id = optionId != null ? optionId : floatingId;
  const groupContext = useDelayGroupContext();
  const {
    currentId,
    setCurrentId,
    initialDelay,
    setState,
    timeoutMs
  } = groupContext;
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!enabled) return;
    if (!currentId) return;
    setState({
      delay: {
        open: 1,
        close: getDelay(initialDelay, 'close')
      }
    });
    if (currentId !== id) {
      onOpenChange(false);
    }
  }, [enabled, id, onOpenChange, setState, currentId, initialDelay]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    function unset() {
      onOpenChange(false);
      setState({
        delay: initialDelay,
        currentId: null
      });
    }
    if (!enabled) return;
    if (!currentId) return;
    if (!open && currentId === id) {
      if (timeoutMs) {
        const timeout = window.setTimeout(unset, timeoutMs);
        return () => {
          clearTimeout(timeout);
        };
      }
      unset();
    }
  }, [enabled, open, setState, currentId, id, onOpenChange, initialDelay, timeoutMs]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!enabled) return;
    if (setCurrentId === NOOP || !open) return;
    setCurrentId(id);
  }, [enabled, open, setCurrentId, id]);
  return groupContext;
}

const NextFloatingDelayGroupContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext({
  hasProvider: false,
  timeoutMs: 0,
  delayRef: {
    current: 0
  },
  initialDelayRef: {
    current: 0
  },
  timeoutIdRef: {
    current: -1
  },
  currentIdRef: {
    current: null
  },
  currentContextRef: {
    current: null
  }
});
/**
 * Experimental next version of `FloatingDelayGroup` to become the default
 * in the future. This component is not yet stable.
 * Provides context for a group of floating elements that should share a
 * `delay`. Unlike `FloatingDelayGroup`, `useNextDelayGroup` with this
 * component does not cause a re-render of unrelated consumers of the
 * context when the delay changes.
 * @see https://floating-ui.com/docs/FloatingDelayGroup
 */
function NextFloatingDelayGroup(props) {
  const {
    children,
    delay,
    timeoutMs = 0
  } = props;
  const delayRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(delay);
  const initialDelayRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(delay);
  const currentIdRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const currentContextRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const timeoutIdRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(NextFloatingDelayGroupContext.Provider, {
    value: react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
      hasProvider: true,
      delayRef,
      initialDelayRef,
      currentIdRef,
      timeoutMs,
      currentContextRef,
      timeoutIdRef
    }), [timeoutMs]),
    children: children
  });
}
/**
 * Enables grouping when called inside a component that's a child of a
 * `NextFloatingDelayGroup`.
 * @see https://floating-ui.com/docs/FloatingDelayGroup
 */
function useNextDelayGroup(context, options) {
  if (options === void 0) {
    options = {};
  }
  const {
    open,
    onOpenChange,
    floatingId
  } = context;
  const {
    enabled = true
  } = options;
  const groupContext = react__WEBPACK_IMPORTED_MODULE_0__.useContext(NextFloatingDelayGroupContext);
  const {
    currentIdRef,
    delayRef,
    timeoutMs,
    initialDelayRef,
    currentContextRef,
    hasProvider,
    timeoutIdRef
  } = groupContext;
  const [isInstantPhase, setIsInstantPhase] = react__WEBPACK_IMPORTED_MODULE_0__.useState(false);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    function unset() {
      var _currentContextRef$cu;
      setIsInstantPhase(false);
      (_currentContextRef$cu = currentContextRef.current) == null || _currentContextRef$cu.setIsInstantPhase(false);
      currentIdRef.current = null;
      currentContextRef.current = null;
      delayRef.current = initialDelayRef.current;
    }
    if (!enabled) return;
    if (!currentIdRef.current) return;
    if (!open && currentIdRef.current === floatingId) {
      setIsInstantPhase(false);
      if (timeoutMs) {
        timeoutIdRef.current = window.setTimeout(unset, timeoutMs);
        return () => {
          clearTimeout(timeoutIdRef.current);
        };
      }
      unset();
    }
  }, [enabled, open, floatingId, currentIdRef, delayRef, timeoutMs, initialDelayRef, currentContextRef, timeoutIdRef]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!enabled) return;
    if (!open) return;
    const prevContext = currentContextRef.current;
    const prevId = currentIdRef.current;
    currentContextRef.current = {
      onOpenChange,
      setIsInstantPhase
    };
    currentIdRef.current = floatingId;
    delayRef.current = {
      open: 0,
      close: getDelay(initialDelayRef.current, 'close')
    };
    if (prevId !== null && prevId !== floatingId) {
      clearTimeoutIfSet(timeoutIdRef);
      setIsInstantPhase(true);
      prevContext == null || prevContext.setIsInstantPhase(true);
      prevContext == null || prevContext.onOpenChange(false);
    } else {
      setIsInstantPhase(false);
      prevContext == null || prevContext.setIsInstantPhase(false);
    }
  }, [enabled, open, floatingId, onOpenChange, currentIdRef, delayRef, timeoutMs, initialDelayRef, currentContextRef, timeoutIdRef]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    return () => {
      currentContextRef.current = null;
    };
  }, [currentContextRef]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    hasProvider,
    delayRef,
    isInstantPhase
  }), [hasProvider, delayRef, isInstantPhase]);
}

let rafId = 0;
function enqueueFocus(el, options) {
  if (options === void 0) {
    options = {};
  }
  const {
    preventScroll = false,
    cancelPrevious = true,
    sync = false
  } = options;
  cancelPrevious && cancelAnimationFrame(rafId);
  const exec = () => el == null ? void 0 : el.focus({
    preventScroll
  });
  if (sync) {
    exec();
  } else {
    rafId = requestAnimationFrame(exec);
  }
}

function contains(parent, child) {
  if (!parent || !child) {
    return false;
  }
  const rootNode = child.getRootNode == null ? void 0 : child.getRootNode();

  // First, attempt with faster native method
  if (parent.contains(child)) {
    return true;
  }

  // then fallback to custom implementation with Shadow DOM support
  if (rootNode && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isShadowRoot)(rootNode)) {
    let next = child;
    while (next) {
      if (parent === next) {
        return true;
      }
      // @ts-ignore
      next = next.parentNode || next.host;
    }
  }

  // Give up, the result is false
  return false;
}
function getTarget(event) {
  if ('composedPath' in event) {
    return event.composedPath()[0];
  }

  // TS thinks `event` is of type never as it assumes all browsers support
  // `composedPath()`, but browsers without shadow DOM don't.
  return event.target;
}
function getDocument(node) {
  return (node == null ? void 0 : node.ownerDocument) || document;
}

// Modified to add conditional `aria-hidden` support:
// https://github.com/theKashey/aria-hidden/blob/9220c8f4a4fd35f63bee5510a9f41a37264382d4/src/index.ts
const counters = {
  inert: /*#__PURE__*/new WeakMap(),
  'aria-hidden': /*#__PURE__*/new WeakMap(),
  none: /*#__PURE__*/new WeakMap()
};
function getCounterMap(control) {
  if (control === 'inert') return counters.inert;
  if (control === 'aria-hidden') return counters['aria-hidden'];
  return counters.none;
}
let uncontrolledElementsSet = /*#__PURE__*/new WeakSet();
let markerMap = {};
let lockCount$1 = 0;
const supportsInert = () => typeof HTMLElement !== 'undefined' && 'inert' in HTMLElement.prototype;
function unwrapHost(node) {
  if (!node) {
    return null;
  }
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isShadowRoot)(node) ? node.host : unwrapHost(node.parentNode);
}
const correctElements = (parent, targets) => targets.map(target => {
  if (parent.contains(target)) {
    return target;
  }
  const correctedTarget = unwrapHost(target);
  if (parent.contains(correctedTarget)) {
    return correctedTarget;
  }
  return null;
}).filter(x => x != null);
function applyAttributeToOthers(uncorrectedAvoidElements, body, ariaHidden, inert) {
  const markerName = 'data-floating-ui-inert';
  const controlAttribute = inert ? 'inert' : ariaHidden ? 'aria-hidden' : null;
  const avoidElements = correctElements(body, uncorrectedAvoidElements);
  const elementsToKeep = new Set();
  const elementsToStop = new Set(avoidElements);
  const hiddenElements = [];
  if (!markerMap[markerName]) {
    markerMap[markerName] = new WeakMap();
  }
  const markerCounter = markerMap[markerName];
  avoidElements.forEach(keep);
  deep(body);
  elementsToKeep.clear();
  function keep(el) {
    if (!el || elementsToKeep.has(el)) {
      return;
    }
    elementsToKeep.add(el);
    el.parentNode && keep(el.parentNode);
  }
  function deep(parent) {
    if (!parent || elementsToStop.has(parent)) {
      return;
    }
    [].forEach.call(parent.children, node => {
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getNodeName)(node) === 'script') return;
      if (elementsToKeep.has(node)) {
        deep(node);
      } else {
        const attr = controlAttribute ? node.getAttribute(controlAttribute) : null;
        const alreadyHidden = attr !== null && attr !== 'false';
        const counterMap = getCounterMap(controlAttribute);
        const counterValue = (counterMap.get(node) || 0) + 1;
        const markerValue = (markerCounter.get(node) || 0) + 1;
        counterMap.set(node, counterValue);
        markerCounter.set(node, markerValue);
        hiddenElements.push(node);
        if (counterValue === 1 && alreadyHidden) {
          uncontrolledElementsSet.add(node);
        }
        if (markerValue === 1) {
          node.setAttribute(markerName, '');
        }
        if (!alreadyHidden && controlAttribute) {
          node.setAttribute(controlAttribute, controlAttribute === 'inert' ? '' : 'true');
        }
      }
    });
  }
  lockCount$1++;
  return () => {
    hiddenElements.forEach(element => {
      const counterMap = getCounterMap(controlAttribute);
      const currentCounterValue = counterMap.get(element) || 0;
      const counterValue = currentCounterValue - 1;
      const markerValue = (markerCounter.get(element) || 0) - 1;
      counterMap.set(element, counterValue);
      markerCounter.set(element, markerValue);
      if (!counterValue) {
        if (!uncontrolledElementsSet.has(element) && controlAttribute) {
          element.removeAttribute(controlAttribute);
        }
        uncontrolledElementsSet.delete(element);
      }
      if (!markerValue) {
        element.removeAttribute(markerName);
      }
    });
    lockCount$1--;
    if (!lockCount$1) {
      counters.inert = new WeakMap();
      counters['aria-hidden'] = new WeakMap();
      counters.none = new WeakMap();
      uncontrolledElementsSet = new WeakSet();
      markerMap = {};
    }
  };
}
function markOthers(avoidElements, ariaHidden, inert) {
  if (ariaHidden === void 0) {
    ariaHidden = false;
  }
  if (inert === void 0) {
    inert = false;
  }
  const body = getDocument(avoidElements[0]).body;
  return applyAttributeToOthers(avoidElements.concat(Array.from(body.querySelectorAll('[aria-live],[role="status"],output'))), body, ariaHidden, inert);
}

const HIDDEN_STYLES = {
  border: 0,
  clip: 'rect(0 0 0 0)',
  height: '1px',
  margin: '-1px',
  overflow: 'hidden',
  padding: 0,
  position: 'fixed',
  whiteSpace: 'nowrap',
  width: '1px',
  top: 0,
  left: 0
};
const FocusGuard = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(function FocusGuard(props, ref) {
  const [role, setRole] = react__WEBPACK_IMPORTED_MODULE_0__.useState();
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isSafari)()) {
      // Unlike other screen readers such as NVDA and JAWS, the virtual cursor
      // on VoiceOver does trigger the onFocus event, so we can use the focus
      // trap element. On Safari, only buttons trigger the onFocus event.
      // NB: "group" role in the Sandbox no longer appears to work, must be a
      // button role.
      setRole('button');
    }
  }, []);
  const restProps = {
    ref,
    tabIndex: 0,
    // Role is only for VoiceOver
    role,
    'aria-hidden': role ? undefined : true,
    [createAttribute('focus-guard')]: '',
    style: HIDDEN_STYLES
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("span", {
    ...props,
    ...restProps
  });
});

const HIDDEN_OWNER_STYLES = {
  clipPath: 'inset(50%)',
  position: 'fixed',
  top: 0,
  left: 0
};
const PortalContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createContext(null);
const attr = /*#__PURE__*/createAttribute('portal');
/**
 * @see https://floating-ui.com/docs/FloatingPortal#usefloatingportalnode
 */
function useFloatingPortalNode(props) {
  if (props === void 0) {
    props = {};
  }
  const {
    id,
    root
  } = props;
  const uniqueId = useId();
  const portalContext = usePortalContext();
  const [portalNode, setPortalNode] = react__WEBPACK_IMPORTED_MODULE_0__.useState(null);
  const portalNodeRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    return () => {
      portalNode == null || portalNode.remove();
      // Allow the subsequent layout effects to create a new node on updates.
      // The portal node will still be cleaned up on unmount.
      // https://github.com/floating-ui/floating-ui/issues/2454
      queueMicrotask(() => {
        portalNodeRef.current = null;
      });
    };
  }, [portalNode]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    // Wait for the uniqueId to be generated before creating the portal node in
    // React <18 (using `useFloatingId` instead of the native `useId`).
    // https://github.com/floating-ui/floating-ui/issues/2778
    if (!uniqueId) return;
    if (portalNodeRef.current) return;
    const existingIdRoot = id ? document.getElementById(id) : null;
    if (!existingIdRoot) return;
    const subRoot = document.createElement('div');
    subRoot.id = uniqueId;
    subRoot.setAttribute(attr, '');
    existingIdRoot.appendChild(subRoot);
    portalNodeRef.current = subRoot;
    setPortalNode(subRoot);
  }, [id, uniqueId]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    // Wait for the root to exist before creating the portal node. The root must
    // be stored in state, not a ref, for this to work reactively.
    if (root === null) return;
    if (!uniqueId) return;
    if (portalNodeRef.current) return;
    let container = root || (portalContext == null ? void 0 : portalContext.portalNode);
    if (container && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isNode)(container)) container = container.current;
    container = container || document.body;
    let idWrapper = null;
    if (id) {
      idWrapper = document.createElement('div');
      idWrapper.id = id;
      container.appendChild(idWrapper);
    }
    const subRoot = document.createElement('div');
    subRoot.id = uniqueId;
    subRoot.setAttribute(attr, '');
    container = idWrapper || container;
    container.appendChild(subRoot);
    portalNodeRef.current = subRoot;
    setPortalNode(subRoot);
  }, [id, root, uniqueId, portalContext]);
  return portalNode;
}
/**
 * Portals the floating element into a given container element — by default,
 * outside of the app root and into the body.
 * This is necessary to ensure the floating element can appear outside any
 * potential parent containers that cause clipping (such as `overflow: hidden`),
 * while retaining its location in the React tree.
 * @see https://floating-ui.com/docs/FloatingPortal
 */
function FloatingPortal(props) {
  const {
    children,
    id,
    root,
    preserveTabOrder = true
  } = props;
  const portalNode = useFloatingPortalNode({
    id,
    root
  });
  const [focusManagerState, setFocusManagerState] = react__WEBPACK_IMPORTED_MODULE_0__.useState(null);
  const beforeOutsideRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const afterOutsideRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const beforeInsideRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const afterInsideRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const modal = focusManagerState == null ? void 0 : focusManagerState.modal;
  const open = focusManagerState == null ? void 0 : focusManagerState.open;
  const shouldRenderGuards =
  // The FocusManager and therefore floating element are currently open/
  // rendered.
  !!focusManagerState &&
  // Guards are only for non-modal focus management.
  !focusManagerState.modal &&
  // Don't render if unmount is transitioning.
  focusManagerState.open && preserveTabOrder && !!(root || portalNode);

  // https://codesandbox.io/s/tabbable-portal-f4tng?file=/src/TabbablePortal.tsx
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!portalNode || !preserveTabOrder || modal) {
      return;
    }

    // Make sure elements inside the portal element are tabbable only when the
    // portal has already been focused, either by tabbing into a focus trap
    // element outside or using the mouse.
    function onFocus(event) {
      if (portalNode && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isOutsideEvent)(event)) {
        const focusing = event.type === 'focusin';
        const manageFocus = focusing ? _floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.enableFocusInside : _floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.disableFocusInside;
        manageFocus(portalNode);
      }
    }
    // Listen to the event on the capture phase so they run before the focus
    // trap elements onFocus prop is called.
    portalNode.addEventListener('focusin', onFocus, true);
    portalNode.addEventListener('focusout', onFocus, true);
    return () => {
      portalNode.removeEventListener('focusin', onFocus, true);
      portalNode.removeEventListener('focusout', onFocus, true);
    };
  }, [portalNode, preserveTabOrder, modal]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!portalNode) return;
    if (open) return;
    (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.enableFocusInside)(portalNode);
  }, [open, portalNode]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)(PortalContext.Provider, {
    value: react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
      preserveTabOrder,
      beforeOutsideRef,
      afterOutsideRef,
      beforeInsideRef,
      afterInsideRef,
      portalNode,
      setFocusManagerState
    }), [preserveTabOrder, portalNode]),
    children: [shouldRenderGuards && portalNode && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FocusGuard, {
      "data-type": "outside",
      ref: beforeOutsideRef,
      onFocus: event => {
        if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isOutsideEvent)(event, portalNode)) {
          var _beforeInsideRef$curr;
          (_beforeInsideRef$curr = beforeInsideRef.current) == null || _beforeInsideRef$curr.focus();
        } else {
          const domReference = focusManagerState ? focusManagerState.domReference : null;
          const prevTabbable = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getPreviousTabbable)(domReference);
          prevTabbable == null || prevTabbable.focus();
        }
      }
    }), shouldRenderGuards && portalNode && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("span", {
      "aria-owns": portalNode.id,
      style: HIDDEN_OWNER_STYLES
    }), portalNode && /*#__PURE__*/react_dom__WEBPACK_IMPORTED_MODULE_5__.createPortal(children, portalNode), shouldRenderGuards && portalNode && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FocusGuard, {
      "data-type": "outside",
      ref: afterOutsideRef,
      onFocus: event => {
        if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isOutsideEvent)(event, portalNode)) {
          var _afterInsideRef$curre;
          (_afterInsideRef$curre = afterInsideRef.current) == null || _afterInsideRef$curre.focus();
        } else {
          const domReference = focusManagerState ? focusManagerState.domReference : null;
          const nextTabbable = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNextTabbable)(domReference);
          nextTabbable == null || nextTabbable.focus();
          (focusManagerState == null ? void 0 : focusManagerState.closeOnFocusOut) && (focusManagerState == null ? void 0 : focusManagerState.onOpenChange(false, event.nativeEvent, 'focus-out'));
        }
      }
    })]
  });
}
const usePortalContext = () => react__WEBPACK_IMPORTED_MODULE_0__.useContext(PortalContext);

function useLiteMergeRefs(refs) {
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    return value => {
      refs.forEach(ref => {
        if (ref) {
          ref.current = value;
        }
      });
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, refs);
}

const LIST_LIMIT = 20;
let previouslyFocusedElements = [];
function clearDisconnectedPreviouslyFocusedElements() {
  previouslyFocusedElements = previouslyFocusedElements.filter(elementRef => {
    var _elementRef$deref;
    return (_elementRef$deref = elementRef.deref()) == null ? void 0 : _elementRef$deref.isConnected;
  });
}
function addPreviouslyFocusedElement(element) {
  clearDisconnectedPreviouslyFocusedElements();
  if (element && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getNodeName)(element) !== 'body') {
    previouslyFocusedElements.push(new WeakRef(element));
    if (previouslyFocusedElements.length > LIST_LIMIT) {
      previouslyFocusedElements = previouslyFocusedElements.slice(-LIST_LIMIT);
    }
  }
}
function getPreviouslyFocusedElement() {
  clearDisconnectedPreviouslyFocusedElements();
  const elementRef = previouslyFocusedElements[previouslyFocusedElements.length - 1];
  return elementRef == null ? void 0 : elementRef.deref();
}
function getFirstTabbableElement(container) {
  const tabbableOptions = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTabbableOptions)();
  if ((0,tabbable__WEBPACK_IMPORTED_MODULE_4__.isTabbable)(container, tabbableOptions)) {
    return container;
  }
  return (0,tabbable__WEBPACK_IMPORTED_MODULE_4__.tabbable)(container, tabbableOptions)[0] || container;
}
function handleTabIndex(floatingFocusElement, orderRef) {
  var _floatingFocusElement;
  if (!orderRef.current.includes('floating') && !((_floatingFocusElement = floatingFocusElement.getAttribute('role')) != null && _floatingFocusElement.includes('dialog'))) {
    return;
  }
  const options = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTabbableOptions)();
  const focusableElements = (0,tabbable__WEBPACK_IMPORTED_MODULE_4__.focusable)(floatingFocusElement, options);
  const tabbableContent = focusableElements.filter(element => {
    const dataTabIndex = element.getAttribute('data-tabindex') || '';
    return (0,tabbable__WEBPACK_IMPORTED_MODULE_4__.isTabbable)(element, options) || element.hasAttribute('data-tabindex') && !dataTabIndex.startsWith('-');
  });
  const tabIndex = floatingFocusElement.getAttribute('tabindex');
  if (orderRef.current.includes('floating') || tabbableContent.length === 0) {
    if (tabIndex !== '0') {
      floatingFocusElement.setAttribute('tabindex', '0');
    }
  } else if (tabIndex !== '-1' || floatingFocusElement.hasAttribute('data-tabindex') && floatingFocusElement.getAttribute('data-tabindex') !== '-1') {
    floatingFocusElement.setAttribute('tabindex', '-1');
    floatingFocusElement.setAttribute('data-tabindex', '-1');
  }
}
const VisuallyHiddenDismiss = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(function VisuallyHiddenDismiss(props, ref) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("button", {
    ...props,
    type: "button",
    ref: ref,
    tabIndex: -1,
    style: HIDDEN_STYLES
  });
});
/**
 * Provides focus management for the floating element.
 * @see https://floating-ui.com/docs/FloatingFocusManager
 */
function FloatingFocusManager(props) {
  const {
    context,
    children,
    disabled = false,
    order = ['content'],
    guards: _guards = true,
    initialFocus = 0,
    returnFocus = true,
    restoreFocus = false,
    modal = true,
    visuallyHiddenDismiss = false,
    closeOnFocusOut = true,
    outsideElementsInert = false,
    getInsideElements: _getInsideElements = () => []
  } = props;
  const {
    open,
    onOpenChange,
    events,
    dataRef,
    elements: {
      domReference,
      floating
    }
  } = context;
  const getNodeId = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    var _dataRef$current$floa;
    return (_dataRef$current$floa = dataRef.current.floatingContext) == null ? void 0 : _dataRef$current$floa.nodeId;
  });
  const getInsideElements = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(_getInsideElements);
  const ignoreInitialFocus = typeof initialFocus === 'number' && initialFocus < 0;
  // If the reference is a combobox and is typeable (e.g. input/textarea),
  // there are different focus semantics. The guards should not be rendered, but
  // aria-hidden should be applied to all nodes still. Further, the visually
  // hidden dismiss button should only appear at the end of the list, not the
  // start.
  const isUntrappedTypeableCombobox = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isTypeableCombobox)(domReference) && ignoreInitialFocus;

  // Force the guards to be rendered if the `inert` attribute is not supported.
  const inertSupported = supportsInert();
  const guards = inertSupported ? _guards : true;
  const useInert = !guards || inertSupported && outsideElementsInert;
  const orderRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(order);
  const initialFocusRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(initialFocus);
  const returnFocusRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(returnFocus);
  const tree = useFloatingTree();
  const portalContext = usePortalContext();
  const startDismissButtonRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const endDismissButtonRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const preventReturnFocusRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const isPointerDownRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const tabbableIndexRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  const blurTimeoutRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  const isInsidePortal = portalContext != null;
  const floatingFocusElement = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getFloatingFocusElement)(floating);
  const getTabbableContent = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(function (container) {
    if (container === void 0) {
      container = floatingFocusElement;
    }
    return container ? (0,tabbable__WEBPACK_IMPORTED_MODULE_4__.tabbable)(container, (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTabbableOptions)()) : [];
  });
  const getTabbableElements = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(container => {
    const content = getTabbableContent(container);
    return orderRef.current.map(type => {
      if (domReference && type === 'reference') {
        return domReference;
      }
      if (floatingFocusElement && type === 'floating') {
        return floatingFocusElement;
      }
      return content;
    }).filter(Boolean).flat();
  });
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (disabled) return;
    if (!modal) return;
    function onKeyDown(event) {
      if (event.key === 'Tab') {
        // The focus guards have nothing to focus, so we need to stop the event.
        if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(floatingFocusElement, (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(floatingFocusElement))) && getTabbableContent().length === 0 && !isUntrappedTypeableCombobox) {
          (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
        }
        const els = getTabbableElements();
        const target = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event);
        if (orderRef.current[0] === 'reference' && target === domReference) {
          (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
          if (event.shiftKey) {
            enqueueFocus(els[els.length - 1]);
          } else {
            enqueueFocus(els[1]);
          }
        }
        if (orderRef.current[1] === 'floating' && target === floatingFocusElement && event.shiftKey) {
          (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
          enqueueFocus(els[0]);
        }
      }
    }
    const doc = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(floatingFocusElement);
    doc.addEventListener('keydown', onKeyDown);
    return () => {
      doc.removeEventListener('keydown', onKeyDown);
    };
  }, [disabled, domReference, floatingFocusElement, modal, orderRef, isUntrappedTypeableCombobox, getTabbableContent, getTabbableElements]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (disabled) return;
    if (!floating) return;
    function handleFocusIn(event) {
      const target = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event);
      const tabbableContent = getTabbableContent();
      const tabbableIndex = tabbableContent.indexOf(target);
      if (tabbableIndex !== -1) {
        tabbableIndexRef.current = tabbableIndex;
      }
    }
    floating.addEventListener('focusin', handleFocusIn);
    return () => {
      floating.removeEventListener('focusin', handleFocusIn);
    };
  }, [disabled, floating, getTabbableContent]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (disabled) return;
    if (!closeOnFocusOut) return;

    // In Safari, buttons lose focus when pressing them.
    function handlePointerDown() {
      isPointerDownRef.current = true;
      setTimeout(() => {
        isPointerDownRef.current = false;
      });
    }
    function handleFocusOutside(event) {
      const relatedTarget = event.relatedTarget;
      const currentTarget = event.currentTarget;
      const target = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event);
      queueMicrotask(() => {
        const nodeId = getNodeId();
        const movedToUnrelatedNode = !((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(domReference, relatedTarget) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(floating, relatedTarget) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(relatedTarget, floating) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(portalContext == null ? void 0 : portalContext.portalNode, relatedTarget) || relatedTarget != null && relatedTarget.hasAttribute(createAttribute('focus-guard')) || tree && ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeChildren)(tree.nodesRef.current, nodeId).find(node => {
          var _node$context, _node$context2;
          return (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)((_node$context = node.context) == null ? void 0 : _node$context.elements.floating, relatedTarget) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)((_node$context2 = node.context) == null ? void 0 : _node$context2.elements.domReference, relatedTarget);
        }) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeAncestors)(tree.nodesRef.current, nodeId).find(node => {
          var _node$context3, _node$context4, _node$context5;
          return [(_node$context3 = node.context) == null ? void 0 : _node$context3.elements.floating, (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getFloatingFocusElement)((_node$context4 = node.context) == null ? void 0 : _node$context4.elements.floating)].includes(relatedTarget) || ((_node$context5 = node.context) == null ? void 0 : _node$context5.elements.domReference) === relatedTarget;
        })));
        if (currentTarget === domReference && floatingFocusElement) {
          handleTabIndex(floatingFocusElement, orderRef);
        }

        // Restore focus to the previous tabbable element index to prevent
        // focus from being lost outside the floating tree.
        if (restoreFocus && currentTarget !== domReference && !(target != null && target.isConnected) && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(floatingFocusElement)) === (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(floatingFocusElement).body) {
          // Let `FloatingPortal` effect knows that focus is still inside the
          // floating tree.
          if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(floatingFocusElement)) {
            floatingFocusElement.focus();
          }
          const prevTabbableIndex = tabbableIndexRef.current;
          const tabbableContent = getTabbableContent();
          const nodeToFocus = tabbableContent[prevTabbableIndex] || tabbableContent[tabbableContent.length - 1] || floatingFocusElement;
          if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(nodeToFocus)) {
            nodeToFocus.focus();
          }
        }

        // https://github.com/floating-ui/floating-ui/issues/3060
        if (dataRef.current.insideReactTree) {
          dataRef.current.insideReactTree = false;
          return;
        }

        // Focus did not move inside the floating tree, and there are no tabbable
        // portal guards to handle closing.
        if ((isUntrappedTypeableCombobox ? true : !modal) && relatedTarget && movedToUnrelatedNode && !isPointerDownRef.current &&
        // Fix React 18 Strict Mode returnFocus due to double rendering.
        relatedTarget !== getPreviouslyFocusedElement()) {
          preventReturnFocusRef.current = true;
          onOpenChange(false, event, 'focus-out');
        }
      });
    }
    const shouldHandleBlurCapture = Boolean(!tree && portalContext);
    function markInsideReactTree() {
      clearTimeoutIfSet(blurTimeoutRef);
      dataRef.current.insideReactTree = true;
      blurTimeoutRef.current = window.setTimeout(() => {
        dataRef.current.insideReactTree = false;
      });
    }
    if (floating && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(domReference)) {
      domReference.addEventListener('focusout', handleFocusOutside);
      domReference.addEventListener('pointerdown', handlePointerDown);
      floating.addEventListener('focusout', handleFocusOutside);
      if (shouldHandleBlurCapture) {
        floating.addEventListener('focusout', markInsideReactTree, true);
      }
      return () => {
        domReference.removeEventListener('focusout', handleFocusOutside);
        domReference.removeEventListener('pointerdown', handlePointerDown);
        floating.removeEventListener('focusout', handleFocusOutside);
        if (shouldHandleBlurCapture) {
          floating.removeEventListener('focusout', markInsideReactTree, true);
        }
      };
    }
  }, [disabled, domReference, floating, floatingFocusElement, modal, tree, portalContext, onOpenChange, closeOnFocusOut, restoreFocus, getTabbableContent, isUntrappedTypeableCombobox, getNodeId, orderRef, dataRef]);
  const beforeGuardRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const afterGuardRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const mergedBeforeGuardRef = useLiteMergeRefs([beforeGuardRef, portalContext == null ? void 0 : portalContext.beforeInsideRef]);
  const mergedAfterGuardRef = useLiteMergeRefs([afterGuardRef, portalContext == null ? void 0 : portalContext.afterInsideRef]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    var _portalContext$portal, _ancestors$find;
    if (disabled) return;
    if (!floating) return;

    // Don't hide portals nested within the parent portal.
    const portalNodes = Array.from((portalContext == null || (_portalContext$portal = portalContext.portalNode) == null ? void 0 : _portalContext$portal.querySelectorAll("[" + createAttribute('portal') + "]")) || []);
    const ancestors = tree ? (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeAncestors)(tree.nodesRef.current, getNodeId()) : [];
    const rootAncestorComboboxDomReference = (_ancestors$find = ancestors.find(node => {
      var _node$context6;
      return (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isTypeableCombobox)(((_node$context6 = node.context) == null ? void 0 : _node$context6.elements.domReference) || null);
    })) == null || (_ancestors$find = _ancestors$find.context) == null ? void 0 : _ancestors$find.elements.domReference;
    const insideElements = [floating, rootAncestorComboboxDomReference, ...portalNodes, ...getInsideElements(), startDismissButtonRef.current, endDismissButtonRef.current, beforeGuardRef.current, afterGuardRef.current, portalContext == null ? void 0 : portalContext.beforeOutsideRef.current, portalContext == null ? void 0 : portalContext.afterOutsideRef.current, orderRef.current.includes('reference') || isUntrappedTypeableCombobox ? domReference : null].filter(x => x != null);
    const cleanup = modal || isUntrappedTypeableCombobox ? markOthers(insideElements, !useInert, useInert) : markOthers(insideElements);
    return () => {
      cleanup();
    };
  }, [disabled, domReference, floating, modal, orderRef, portalContext, isUntrappedTypeableCombobox, guards, useInert, tree, getNodeId, getInsideElements]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (disabled || !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(floatingFocusElement)) return;
    const doc = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(floatingFocusElement);
    const previouslyFocusedElement = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)(doc);

    // Wait for any layout effect state setters to execute to set `tabIndex`.
    queueMicrotask(() => {
      const focusableElements = getTabbableElements(floatingFocusElement);
      const initialFocusValue = initialFocusRef.current;
      const elToFocus = (typeof initialFocusValue === 'number' ? focusableElements[initialFocusValue] : initialFocusValue.current) || floatingFocusElement;
      const focusAlreadyInsideFloatingEl = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(floatingFocusElement, previouslyFocusedElement);
      if (!ignoreInitialFocus && !focusAlreadyInsideFloatingEl && open) {
        enqueueFocus(elToFocus, {
          preventScroll: elToFocus === floatingFocusElement
        });
      }
    });
  }, [disabled, open, floatingFocusElement, ignoreInitialFocus, getTabbableElements, initialFocusRef]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (disabled || !floatingFocusElement) return;
    const doc = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(floatingFocusElement);
    const previouslyFocusedElement = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)(doc);
    addPreviouslyFocusedElement(previouslyFocusedElement);

    // Dismissing via outside press should always ignore `returnFocus` to
    // prevent unwanted scrolling.
    function onOpenChange(_ref) {
      let {
        reason,
        event,
        nested
      } = _ref;
      if (['hover', 'safe-polygon'].includes(reason) && event.type === 'mouseleave') {
        preventReturnFocusRef.current = true;
      }
      if (reason !== 'outside-press') return;
      if (nested) {
        preventReturnFocusRef.current = false;
      } else if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isVirtualClick)(event) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isVirtualPointerEvent)(event)) {
        preventReturnFocusRef.current = false;
      } else {
        let isPreventScrollSupported = false;
        document.createElement('div').focus({
          get preventScroll() {
            isPreventScrollSupported = true;
            return false;
          }
        });
        if (isPreventScrollSupported) {
          preventReturnFocusRef.current = false;
        } else {
          preventReturnFocusRef.current = true;
        }
      }
    }
    events.on('openchange', onOpenChange);
    const fallbackEl = doc.createElement('span');
    fallbackEl.setAttribute('tabindex', '-1');
    fallbackEl.setAttribute('aria-hidden', 'true');
    Object.assign(fallbackEl.style, HIDDEN_STYLES);
    if (isInsidePortal && domReference) {
      domReference.insertAdjacentElement('afterend', fallbackEl);
    }
    function getReturnElement() {
      if (typeof returnFocusRef.current === 'boolean') {
        const el = domReference || getPreviouslyFocusedElement();
        return el && el.isConnected ? el : fallbackEl;
      }
      return returnFocusRef.current.current || fallbackEl;
    }
    return () => {
      events.off('openchange', onOpenChange);
      const activeEl = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)(doc);
      const isFocusInsideFloatingTree = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(floating, activeEl) || tree && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeChildren)(tree.nodesRef.current, getNodeId(), false).some(node => {
        var _node$context7;
        return (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)((_node$context7 = node.context) == null ? void 0 : _node$context7.elements.floating, activeEl);
      });
      const returnElement = getReturnElement();
      queueMicrotask(() => {
        // This is `returnElement`, if it's tabbable, or its first tabbable child.
        const tabbableReturnElement = getFirstTabbableElement(returnElement);
        if (
        // eslint-disable-next-line react-hooks/exhaustive-deps
        returnFocusRef.current && !preventReturnFocusRef.current && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(tabbableReturnElement) && (
        // If the focus moved somewhere else after mount, avoid returning focus
        // since it likely entered a different element which should be
        // respected: https://github.com/floating-ui/floating-ui/issues/2607
        tabbableReturnElement !== activeEl && activeEl !== doc.body ? isFocusInsideFloatingTree : true)) {
          tabbableReturnElement.focus({
            preventScroll: true
          });
        }
        fallbackEl.remove();
      });
    };
  }, [disabled, floating, floatingFocusElement, returnFocusRef, dataRef, events, tree, isInsidePortal, domReference, getNodeId]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    // The `returnFocus` cleanup behavior is inside a microtask; ensure we
    // wait for it to complete before resetting the flag.
    queueMicrotask(() => {
      preventReturnFocusRef.current = false;
    });
    return () => {
      queueMicrotask(clearDisconnectedPreviouslyFocusedElements);
    };
  }, [disabled]);

  // Synchronize the `context` & `modal` value to the FloatingPortal context.
  // It will decide whether or not it needs to render its own guards.
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (disabled) return;
    if (!portalContext) return;
    portalContext.setFocusManagerState({
      modal,
      closeOnFocusOut,
      open,
      onOpenChange,
      domReference
    });
    return () => {
      portalContext.setFocusManagerState(null);
    };
  }, [disabled, portalContext, modal, open, onOpenChange, closeOnFocusOut, domReference]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (disabled) return;
    if (!floatingFocusElement) return;
    handleTabIndex(floatingFocusElement, orderRef);
  }, [disabled, floatingFocusElement, orderRef]);
  function renderDismissButton(location) {
    if (disabled || !visuallyHiddenDismiss || !modal) {
      return null;
    }
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(VisuallyHiddenDismiss, {
      ref: location === 'start' ? startDismissButtonRef : endDismissButtonRef,
      onClick: event => onOpenChange(false, event.nativeEvent),
      children: typeof visuallyHiddenDismiss === 'string' ? visuallyHiddenDismiss : 'Dismiss'
    });
  }
  const shouldRenderGuards = !disabled && guards && (modal ? !isUntrappedTypeableCombobox : true) && (isInsidePortal || modal);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.Fragment, {
    children: [shouldRenderGuards && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FocusGuard, {
      "data-type": "inside",
      ref: mergedBeforeGuardRef,
      onFocus: event => {
        if (modal) {
          const els = getTabbableElements();
          enqueueFocus(order[0] === 'reference' ? els[0] : els[els.length - 1]);
        } else if (portalContext != null && portalContext.preserveTabOrder && portalContext.portalNode) {
          preventReturnFocusRef.current = false;
          if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isOutsideEvent)(event, portalContext.portalNode)) {
            const nextTabbable = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNextTabbable)(domReference);
            nextTabbable == null || nextTabbable.focus();
          } else {
            var _portalContext$before;
            (_portalContext$before = portalContext.beforeOutsideRef.current) == null || _portalContext$before.focus();
          }
        }
      }
    }), !isUntrappedTypeableCombobox && renderDismissButton('start'), children, renderDismissButton('end'), shouldRenderGuards && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(FocusGuard, {
      "data-type": "inside",
      ref: mergedAfterGuardRef,
      onFocus: event => {
        if (modal) {
          enqueueFocus(getTabbableElements()[0]);
        } else if (portalContext != null && portalContext.preserveTabOrder && portalContext.portalNode) {
          if (closeOnFocusOut) {
            preventReturnFocusRef.current = true;
          }
          if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isOutsideEvent)(event, portalContext.portalNode)) {
            const prevTabbable = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getPreviousTabbable)(domReference);
            prevTabbable == null || prevTabbable.focus();
          } else {
            var _portalContext$afterO;
            (_portalContext$afterO = portalContext.afterOutsideRef.current) == null || _portalContext$afterO.focus();
          }
        }
      }
    })]
  });
}

let lockCount = 0;
const scrollbarProperty = '--floating-ui-scrollbar-width';
function enableScrollLock() {
  const platform = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getPlatform)();
  const isIOS = /iP(hone|ad|od)|iOS/.test(platform) ||
  // iPads can claim to be MacIntel
  platform === 'MacIntel' && navigator.maxTouchPoints > 1;
  const bodyStyle = document.body.style;
  // RTL <body> scrollbar
  const scrollbarX = Math.round(document.documentElement.getBoundingClientRect().left) + document.documentElement.scrollLeft;
  const paddingProp = scrollbarX ? 'paddingLeft' : 'paddingRight';
  const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
  const scrollX = bodyStyle.left ? parseFloat(bodyStyle.left) : window.scrollX;
  const scrollY = bodyStyle.top ? parseFloat(bodyStyle.top) : window.scrollY;
  bodyStyle.overflow = 'hidden';
  bodyStyle.setProperty(scrollbarProperty, scrollbarWidth + "px");
  if (scrollbarWidth) {
    bodyStyle[paddingProp] = scrollbarWidth + "px";
  }

  // Only iOS doesn't respect `overflow: hidden` on document.body, and this
  // technique has fewer side effects.
  if (isIOS) {
    var _window$visualViewpor, _window$visualViewpor2;
    // iOS 12 does not support `visualViewport`.
    const offsetLeft = ((_window$visualViewpor = window.visualViewport) == null ? void 0 : _window$visualViewpor.offsetLeft) || 0;
    const offsetTop = ((_window$visualViewpor2 = window.visualViewport) == null ? void 0 : _window$visualViewpor2.offsetTop) || 0;
    Object.assign(bodyStyle, {
      position: 'fixed',
      top: -(scrollY - Math.floor(offsetTop)) + "px",
      left: -(scrollX - Math.floor(offsetLeft)) + "px",
      right: '0'
    });
  }
  return () => {
    Object.assign(bodyStyle, {
      overflow: '',
      [paddingProp]: ''
    });
    bodyStyle.removeProperty(scrollbarProperty);
    if (isIOS) {
      Object.assign(bodyStyle, {
        position: '',
        top: '',
        left: '',
        right: ''
      });
      window.scrollTo(scrollX, scrollY);
    }
  };
}
let cleanup = () => {};

/**
 * Provides base styling for a fixed overlay element to dim content or block
 * pointer events behind a floating element.
 * It's a regular `<div>`, so it can be styled via any CSS solution you prefer.
 * @see https://floating-ui.com/docs/FloatingOverlay
 */
const FloatingOverlay = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(function FloatingOverlay(props, ref) {
  const {
    lockScroll = false,
    ...rest
  } = props;
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!lockScroll) return;
    lockCount++;
    if (lockCount === 1) {
      cleanup = enableScrollLock();
    }
    return () => {
      lockCount--;
      if (lockCount === 0) {
        cleanup();
      }
    };
  }, [lockScroll]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("div", {
    ref: ref,
    ...rest,
    style: {
      position: 'fixed',
      overflow: 'auto',
      top: 0,
      right: 0,
      bottom: 0,
      left: 0,
      ...rest.style
    }
  });
});

function isButtonTarget(event) {
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(event.target) && event.target.tagName === 'BUTTON';
}
function isAnchorTarget(event) {
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(event.target) && event.target.tagName === 'A';
}
function isSpaceIgnored(element) {
  return (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isTypeableElement)(element);
}
/**
 * Opens or closes the floating element when clicking the reference element.
 * @see https://floating-ui.com/docs/useClick
 */
function useClick(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    onOpenChange,
    dataRef,
    elements: {
      domReference
    }
  } = context;
  const {
    enabled = true,
    event: eventOption = 'click',
    toggle = true,
    ignoreMouse = false,
    keyboardHandlers = true,
    stickIfOpen = true
  } = props;
  const pointerTypeRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef();
  const didKeyDownRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    onPointerDown(event) {
      pointerTypeRef.current = event.pointerType;
    },
    onMouseDown(event) {
      const pointerType = pointerTypeRef.current;

      // Ignore all buttons except for the "main" button.
      // https://developer.mozilla.org/en-US/docs/Web/API/MouseEvent/button
      if (event.button !== 0) return;
      if (eventOption === 'click') return;
      if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMouseLikePointerType)(pointerType, true) && ignoreMouse) return;
      if (open && toggle && (dataRef.current.openEvent && stickIfOpen ? dataRef.current.openEvent.type === 'mousedown' : true)) {
        onOpenChange(false, event.nativeEvent, 'click');
      } else {
        // Prevent stealing focus from the floating element
        event.preventDefault();
        onOpenChange(true, event.nativeEvent, 'click');
      }
    },
    onClick(event) {
      const pointerType = pointerTypeRef.current;
      if (eventOption === 'mousedown' && pointerTypeRef.current) {
        pointerTypeRef.current = undefined;
        return;
      }
      if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMouseLikePointerType)(pointerType, true) && ignoreMouse) return;
      if (open && toggle && (dataRef.current.openEvent && stickIfOpen ? dataRef.current.openEvent.type === 'click' : true)) {
        onOpenChange(false, event.nativeEvent, 'click');
      } else {
        onOpenChange(true, event.nativeEvent, 'click');
      }
    },
    onKeyDown(event) {
      pointerTypeRef.current = undefined;
      if (event.defaultPrevented || !keyboardHandlers || isButtonTarget(event)) {
        return;
      }
      if (event.key === ' ' && !isSpaceIgnored(domReference)) {
        // Prevent scrolling
        event.preventDefault();
        didKeyDownRef.current = true;
      }
      if (isAnchorTarget(event)) {
        return;
      }
      if (event.key === 'Enter') {
        if (open && toggle) {
          onOpenChange(false, event.nativeEvent, 'click');
        } else {
          onOpenChange(true, event.nativeEvent, 'click');
        }
      }
    },
    onKeyUp(event) {
      if (event.defaultPrevented || !keyboardHandlers || isButtonTarget(event) || isSpaceIgnored(domReference)) {
        return;
      }
      if (event.key === ' ' && didKeyDownRef.current) {
        didKeyDownRef.current = false;
        if (open && toggle) {
          onOpenChange(false, event.nativeEvent, 'click');
        } else {
          onOpenChange(true, event.nativeEvent, 'click');
        }
      }
    }
  }), [dataRef, domReference, eventOption, ignoreMouse, keyboardHandlers, onOpenChange, open, stickIfOpen, toggle]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference
  } : {}, [enabled, reference]);
}

function createVirtualElement(domElement, data) {
  let offsetX = null;
  let offsetY = null;
  let isAutoUpdateEvent = false;
  return {
    contextElement: domElement || undefined,
    getBoundingClientRect() {
      var _data$dataRef$current;
      const domRect = (domElement == null ? void 0 : domElement.getBoundingClientRect()) || {
        width: 0,
        height: 0,
        x: 0,
        y: 0
      };
      const isXAxis = data.axis === 'x' || data.axis === 'both';
      const isYAxis = data.axis === 'y' || data.axis === 'both';
      const canTrackCursorOnAutoUpdate = ['mouseenter', 'mousemove'].includes(((_data$dataRef$current = data.dataRef.current.openEvent) == null ? void 0 : _data$dataRef$current.type) || '') && data.pointerType !== 'touch';
      let width = domRect.width;
      let height = domRect.height;
      let x = domRect.x;
      let y = domRect.y;
      if (offsetX == null && data.x && isXAxis) {
        offsetX = domRect.x - data.x;
      }
      if (offsetY == null && data.y && isYAxis) {
        offsetY = domRect.y - data.y;
      }
      x -= offsetX || 0;
      y -= offsetY || 0;
      width = 0;
      height = 0;
      if (!isAutoUpdateEvent || canTrackCursorOnAutoUpdate) {
        width = data.axis === 'y' ? domRect.width : 0;
        height = data.axis === 'x' ? domRect.height : 0;
        x = isXAxis && data.x != null ? data.x : x;
        y = isYAxis && data.y != null ? data.y : y;
      } else if (isAutoUpdateEvent && !canTrackCursorOnAutoUpdate) {
        height = data.axis === 'x' ? domRect.height : height;
        width = data.axis === 'y' ? domRect.width : width;
      }
      isAutoUpdateEvent = true;
      return {
        width,
        height,
        x,
        y,
        top: y,
        right: x + width,
        bottom: y + height,
        left: x
      };
    }
  };
}
function isMouseBasedEvent(event) {
  return event != null && event.clientX != null;
}
/**
 * Positions the floating element relative to a client point (in the viewport),
 * such as the mouse position. By default, it follows the mouse cursor.
 * @see https://floating-ui.com/docs/useClientPoint
 */
function useClientPoint(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    dataRef,
    elements: {
      floating,
      domReference
    },
    refs
  } = context;
  const {
    enabled = true,
    axis = 'both',
    x = null,
    y = null
  } = props;
  const initialRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const cleanupListenerRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const [pointerType, setPointerType] = react__WEBPACK_IMPORTED_MODULE_0__.useState();
  const [reactive, setReactive] = react__WEBPACK_IMPORTED_MODULE_0__.useState([]);
  const setReference = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)((x, y) => {
    if (initialRef.current) return;

    // Prevent setting if the open event was not a mouse-like one
    // (e.g. focus to open, then hover over the reference element).
    // Only apply if the event exists.
    if (dataRef.current.openEvent && !isMouseBasedEvent(dataRef.current.openEvent)) {
      return;
    }
    refs.setPositionReference(createVirtualElement(domReference, {
      x,
      y,
      axis,
      dataRef,
      pointerType
    }));
  });
  const handleReferenceEnterOrMove = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    if (x != null || y != null) return;
    if (!open) {
      setReference(event.clientX, event.clientY);
    } else if (!cleanupListenerRef.current) {
      // If there's no cleanup, there's no listener, but we want to ensure
      // we add the listener if the cursor landed on the floating element and
      // then back on the reference (i.e. it's interactive).
      setReactive([]);
    }
  });

  // If the pointer is a mouse-like pointer, we want to continue following the
  // mouse even if the floating element is transitioning out. On touch
  // devices, this is undesirable because the floating element will move to
  // the dismissal touch point.
  const openCheck = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMouseLikePointerType)(pointerType) ? floating : open;
  const addListener = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(() => {
    // Explicitly specified `x`/`y` coordinates shouldn't add a listener.
    if (!openCheck || !enabled || x != null || y != null) return;
    const win = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getWindow)(floating);
    function handleMouseMove(event) {
      const target = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event);
      if (!(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(floating, target)) {
        setReference(event.clientX, event.clientY);
      } else {
        win.removeEventListener('mousemove', handleMouseMove);
        cleanupListenerRef.current = null;
      }
    }
    if (!dataRef.current.openEvent || isMouseBasedEvent(dataRef.current.openEvent)) {
      win.addEventListener('mousemove', handleMouseMove);
      const cleanup = () => {
        win.removeEventListener('mousemove', handleMouseMove);
        cleanupListenerRef.current = null;
      };
      cleanupListenerRef.current = cleanup;
      return cleanup;
    }
    refs.setPositionReference(domReference);
  }, [openCheck, enabled, x, y, floating, dataRef, refs, domReference, setReference]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    return addListener();
  }, [addListener, reactive]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (enabled && !floating) {
      initialRef.current = false;
    }
  }, [enabled, floating]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled && open) {
      initialRef.current = true;
    }
  }, [enabled, open]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (enabled && (x != null || y != null)) {
      initialRef.current = false;
      setReference(x, y);
    }
  }, [enabled, x, y, setReference]);
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    function setPointerTypeRef(_ref) {
      let {
        pointerType
      } = _ref;
      setPointerType(pointerType);
    }
    return {
      onPointerDown: setPointerTypeRef,
      onPointerEnter: setPointerTypeRef,
      onMouseMove: handleReferenceEnterOrMove,
      onMouseEnter: handleReferenceEnterOrMove
    };
  }, [handleReferenceEnterOrMove]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference
  } : {}, [enabled, reference]);
}

const bubbleHandlerKeys = {
  pointerdown: 'onPointerDown',
  mousedown: 'onMouseDown',
  click: 'onClick'
};
const captureHandlerKeys = {
  pointerdown: 'onPointerDownCapture',
  mousedown: 'onMouseDownCapture',
  click: 'onClickCapture'
};
const normalizeProp = normalizable => {
  var _normalizable$escapeK, _normalizable$outside;
  return {
    escapeKey: typeof normalizable === 'boolean' ? normalizable : (_normalizable$escapeK = normalizable == null ? void 0 : normalizable.escapeKey) != null ? _normalizable$escapeK : false,
    outsidePress: typeof normalizable === 'boolean' ? normalizable : (_normalizable$outside = normalizable == null ? void 0 : normalizable.outsidePress) != null ? _normalizable$outside : true
  };
};
/**
 * Closes the floating element when a dismissal is requested — by default, when
 * the user presses the `escape` key or outside of the floating element.
 * @see https://floating-ui.com/docs/useDismiss
 */
function useDismiss(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    onOpenChange,
    elements,
    dataRef
  } = context;
  const {
    enabled = true,
    escapeKey = true,
    outsidePress: unstable_outsidePress = true,
    outsidePressEvent = 'pointerdown',
    referencePress = false,
    referencePressEvent = 'pointerdown',
    ancestorScroll = false,
    bubbles,
    capture
  } = props;
  const tree = useFloatingTree();
  const outsidePressFn = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(typeof unstable_outsidePress === 'function' ? unstable_outsidePress : () => false);
  const outsidePress = typeof unstable_outsidePress === 'function' ? outsidePressFn : unstable_outsidePress;
  const endedOrStartedInsideRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const {
    escapeKey: escapeKeyBubbles,
    outsidePress: outsidePressBubbles
  } = normalizeProp(bubbles);
  const {
    escapeKey: escapeKeyCapture,
    outsidePress: outsidePressCapture
  } = normalizeProp(capture);
  const isComposingRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const closeOnEscapeKeyDown = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    var _dataRef$current$floa;
    if (!open || !enabled || !escapeKey || event.key !== 'Escape') {
      return;
    }

    // Wait until IME is settled. Pressing `Escape` while composing should
    // close the compose menu, but not the floating element.
    if (isComposingRef.current) {
      return;
    }
    const nodeId = (_dataRef$current$floa = dataRef.current.floatingContext) == null ? void 0 : _dataRef$current$floa.nodeId;
    const children = tree ? (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeChildren)(tree.nodesRef.current, nodeId) : [];
    if (!escapeKeyBubbles) {
      event.stopPropagation();
      if (children.length > 0) {
        let shouldDismiss = true;
        children.forEach(child => {
          var _child$context;
          if ((_child$context = child.context) != null && _child$context.open && !child.context.dataRef.current.__escapeKeyBubbles) {
            shouldDismiss = false;
            return;
          }
        });
        if (!shouldDismiss) {
          return;
        }
      }
    }
    onOpenChange(false, (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isReactEvent)(event) ? event.nativeEvent : event, 'escape-key');
  });
  const closeOnEscapeKeyDownCapture = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    var _getTarget2;
    const callback = () => {
      var _getTarget;
      closeOnEscapeKeyDown(event);
      (_getTarget = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event)) == null || _getTarget.removeEventListener('keydown', callback);
    };
    (_getTarget2 = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event)) == null || _getTarget2.addEventListener('keydown', callback);
  });
  const closeOnPressOutside = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    var _dataRef$current$floa2;
    // Given developers can stop the propagation of the synthetic event,
    // we can only be confident with a positive value.
    const insideReactTree = dataRef.current.insideReactTree;
    dataRef.current.insideReactTree = false;

    // When click outside is lazy (`click` event), handle dragging.
    // Don't close if:
    // - The click started inside the floating element.
    // - The click ended inside the floating element.
    const endedOrStartedInside = endedOrStartedInsideRef.current;
    endedOrStartedInsideRef.current = false;
    if (outsidePressEvent === 'click' && endedOrStartedInside) {
      return;
    }
    if (insideReactTree) {
      return;
    }
    if (typeof outsidePress === 'function' && !outsidePress(event)) {
      return;
    }
    const target = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event);
    const inertSelector = "[" + createAttribute('inert') + "]";
    const markers = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating).querySelectorAll(inertSelector);
    let targetRootAncestor = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(target) ? target : null;
    while (targetRootAncestor && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isLastTraversableNode)(targetRootAncestor)) {
      const nextParent = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getParentNode)(targetRootAncestor);
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isLastTraversableNode)(nextParent) || !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(nextParent)) {
        break;
      }
      targetRootAncestor = nextParent;
    }

    // Check if the click occurred on a third-party element injected after the
    // floating element rendered.
    if (markers.length && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(target) && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isRootElement)(target) &&
    // Clicked on a direct ancestor (e.g. FloatingOverlay).
    !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(target, elements.floating) &&
    // If the target root element contains none of the markers, then the
    // element was injected after the floating element rendered.
    Array.from(markers).every(marker => !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(targetRootAncestor, marker))) {
      return;
    }

    // Check if the click occurred on the scrollbar
    if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(target) && floating) {
      const lastTraversableNode = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isLastTraversableNode)(target);
      const style = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getComputedStyle)(target);
      const scrollRe = /auto|scroll/;
      const isScrollableX = lastTraversableNode || scrollRe.test(style.overflowX);
      const isScrollableY = lastTraversableNode || scrollRe.test(style.overflowY);
      const canScrollX = isScrollableX && target.clientWidth > 0 && target.scrollWidth > target.clientWidth;
      const canScrollY = isScrollableY && target.clientHeight > 0 && target.scrollHeight > target.clientHeight;
      const isRTL = style.direction === 'rtl';

      // Check click position relative to scrollbar.
      // In some browsers it is possible to change the <body> (or window)
      // scrollbar to the left side, but is very rare and is difficult to
      // check for. Plus, for modal dialogs with backdrops, it is more
      // important that the backdrop is checked but not so much the window.
      const pressedVerticalScrollbar = canScrollY && (isRTL ? event.offsetX <= target.offsetWidth - target.clientWidth : event.offsetX > target.clientWidth);
      const pressedHorizontalScrollbar = canScrollX && event.offsetY > target.clientHeight;
      if (pressedVerticalScrollbar || pressedHorizontalScrollbar) {
        return;
      }
    }
    const nodeId = (_dataRef$current$floa2 = dataRef.current.floatingContext) == null ? void 0 : _dataRef$current$floa2.nodeId;
    const targetIsInsideChildren = tree && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeChildren)(tree.nodesRef.current, nodeId).some(node => {
      var _node$context;
      return (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isEventTargetWithin)(event, (_node$context = node.context) == null ? void 0 : _node$context.elements.floating);
    });
    if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isEventTargetWithin)(event, elements.floating) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isEventTargetWithin)(event, elements.domReference) || targetIsInsideChildren) {
      return;
    }
    const children = tree ? (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getNodeChildren)(tree.nodesRef.current, nodeId) : [];
    if (children.length > 0) {
      let shouldDismiss = true;
      children.forEach(child => {
        var _child$context2;
        if ((_child$context2 = child.context) != null && _child$context2.open && !child.context.dataRef.current.__outsidePressBubbles) {
          shouldDismiss = false;
          return;
        }
      });
      if (!shouldDismiss) {
        return;
      }
    }
    onOpenChange(false, event, 'outside-press');
  });
  const closeOnPressOutsideCapture = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    var _getTarget4;
    const callback = () => {
      var _getTarget3;
      closeOnPressOutside(event);
      (_getTarget3 = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event)) == null || _getTarget3.removeEventListener(outsidePressEvent, callback);
    };
    (_getTarget4 = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event)) == null || _getTarget4.addEventListener(outsidePressEvent, callback);
  });
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!open || !enabled) {
      return;
    }
    dataRef.current.__escapeKeyBubbles = escapeKeyBubbles;
    dataRef.current.__outsidePressBubbles = outsidePressBubbles;
    let compositionTimeout = -1;
    function onScroll(event) {
      onOpenChange(false, event, 'ancestor-scroll');
    }
    function handleCompositionStart() {
      window.clearTimeout(compositionTimeout);
      isComposingRef.current = true;
    }
    function handleCompositionEnd() {
      // Safari fires `compositionend` before `keydown`, so we need to wait
      // until the next tick to set `isComposing` to `false`.
      // https://bugs.webkit.org/show_bug.cgi?id=165004
      compositionTimeout = window.setTimeout(() => {
        isComposingRef.current = false;
      },
      // 0ms or 1ms don't work in Safari. 5ms appears to consistently work.
      // Only apply to WebKit for the test to remain 0ms.
      (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isWebKit)() ? 5 : 0);
    }
    const doc = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating);
    if (escapeKey) {
      doc.addEventListener('keydown', escapeKeyCapture ? closeOnEscapeKeyDownCapture : closeOnEscapeKeyDown, escapeKeyCapture);
      doc.addEventListener('compositionstart', handleCompositionStart);
      doc.addEventListener('compositionend', handleCompositionEnd);
    }
    outsidePress && doc.addEventListener(outsidePressEvent, outsidePressCapture ? closeOnPressOutsideCapture : closeOnPressOutside, outsidePressCapture);
    let ancestors = [];
    if (ancestorScroll) {
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(elements.domReference)) {
        ancestors = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getOverflowAncestors)(elements.domReference);
      }
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(elements.floating)) {
        ancestors = ancestors.concat((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getOverflowAncestors)(elements.floating));
      }
      if (!(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(elements.reference) && elements.reference && elements.reference.contextElement) {
        ancestors = ancestors.concat((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getOverflowAncestors)(elements.reference.contextElement));
      }
    }

    // Ignore the visual viewport for scrolling dismissal (allow pinch-zoom)
    ancestors = ancestors.filter(ancestor => {
      var _doc$defaultView;
      return ancestor !== ((_doc$defaultView = doc.defaultView) == null ? void 0 : _doc$defaultView.visualViewport);
    });
    ancestors.forEach(ancestor => {
      ancestor.addEventListener('scroll', onScroll, {
        passive: true
      });
    });
    return () => {
      if (escapeKey) {
        doc.removeEventListener('keydown', escapeKeyCapture ? closeOnEscapeKeyDownCapture : closeOnEscapeKeyDown, escapeKeyCapture);
        doc.removeEventListener('compositionstart', handleCompositionStart);
        doc.removeEventListener('compositionend', handleCompositionEnd);
      }
      outsidePress && doc.removeEventListener(outsidePressEvent, outsidePressCapture ? closeOnPressOutsideCapture : closeOnPressOutside, outsidePressCapture);
      ancestors.forEach(ancestor => {
        ancestor.removeEventListener('scroll', onScroll);
      });
      window.clearTimeout(compositionTimeout);
    };
  }, [dataRef, elements, escapeKey, outsidePress, outsidePressEvent, open, onOpenChange, ancestorScroll, enabled, escapeKeyBubbles, outsidePressBubbles, closeOnEscapeKeyDown, escapeKeyCapture, closeOnEscapeKeyDownCapture, closeOnPressOutside, outsidePressCapture, closeOnPressOutsideCapture]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    dataRef.current.insideReactTree = false;
  }, [dataRef, outsidePress, outsidePressEvent]);
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    onKeyDown: closeOnEscapeKeyDown,
    ...(referencePress && {
      [bubbleHandlerKeys[referencePressEvent]]: event => {
        onOpenChange(false, event.nativeEvent, 'reference-press');
      },
      ...(referencePressEvent !== 'click' && {
        onClick(event) {
          onOpenChange(false, event.nativeEvent, 'reference-press');
        }
      })
    })
  }), [closeOnEscapeKeyDown, onOpenChange, referencePress, referencePressEvent]);
  const floating = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    function setMouseDownOrUpInside(event) {
      if (event.button !== 0) {
        return;
      }
      endedOrStartedInsideRef.current = true;
    }
    return {
      onKeyDown: closeOnEscapeKeyDown,
      onMouseDown: setMouseDownOrUpInside,
      onMouseUp: setMouseDownOrUpInside,
      [captureHandlerKeys[outsidePressEvent]]: () => {
        dataRef.current.insideReactTree = true;
      }
    };
  }, [closeOnEscapeKeyDown, outsidePressEvent, dataRef]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference,
    floating
  } : {}, [enabled, reference, floating]);
}

function useFloatingRootContext(options) {
  const {
    open = false,
    onOpenChange: onOpenChangeProp,
    elements: elementsProp
  } = options;
  const floatingId = useId();
  const dataRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef({});
  const [events] = react__WEBPACK_IMPORTED_MODULE_0__.useState(() => createEventEmitter());
  const nested = useFloatingParentNodeId() != null;
  if (true) {
    const optionDomReference = elementsProp.reference;
    if (optionDomReference && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(optionDomReference)) {
      error('Cannot pass a virtual element to the `elements.reference` option,', 'as it must be a real DOM element. Use `refs.setPositionReference()`', 'instead.');
    }
  }
  const [positionReference, setPositionReference] = react__WEBPACK_IMPORTED_MODULE_0__.useState(elementsProp.reference);
  const onOpenChange = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)((open, event, reason) => {
    dataRef.current.openEvent = open ? event : undefined;
    events.emit('openchange', {
      open,
      event,
      reason,
      nested
    });
    onOpenChangeProp == null || onOpenChangeProp(open, event, reason);
  });
  const refs = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    setPositionReference
  }), []);
  const elements = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    reference: positionReference || elementsProp.reference || null,
    floating: elementsProp.floating || null,
    domReference: elementsProp.reference
  }), [positionReference, elementsProp.reference, elementsProp.floating]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    dataRef,
    open,
    onOpenChange,
    elements,
    events,
    floatingId,
    refs
  }), [open, onOpenChange, elements, events, floatingId, refs]);
}

/**
 * Provides data to position a floating element and context to add interactions.
 * @see https://floating-ui.com/docs/useFloating
 */
function useFloating(options) {
  if (options === void 0) {
    options = {};
  }
  const {
    nodeId
  } = options;
  const internalRootContext = useFloatingRootContext({
    ...options,
    elements: {
      reference: null,
      floating: null,
      ...options.elements
    }
  });
  const rootContext = options.rootContext || internalRootContext;
  const computedElements = rootContext.elements;
  const [_domReference, setDomReference] = react__WEBPACK_IMPORTED_MODULE_0__.useState(null);
  const [positionReference, _setPositionReference] = react__WEBPACK_IMPORTED_MODULE_0__.useState(null);
  const optionDomReference = computedElements == null ? void 0 : computedElements.domReference;
  const domReference = optionDomReference || _domReference;
  const domReferenceRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const tree = useFloatingTree();
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (domReference) {
      domReferenceRef.current = domReference;
    }
  }, [domReference]);
  const position = (0,_floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.useFloating)({
    ...options,
    elements: {
      ...computedElements,
      ...(positionReference && {
        reference: positionReference
      })
    }
  });
  const setPositionReference = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    const computedPositionReference = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(node) ? {
      getBoundingClientRect: () => node.getBoundingClientRect(),
      getClientRects: () => node.getClientRects(),
      contextElement: node
    } : node;
    // Store the positionReference in state if the DOM reference is specified externally via the
    // `elements.reference` option. This ensures that it won't be overridden on future renders.
    _setPositionReference(computedPositionReference);
    position.refs.setReference(computedPositionReference);
  }, [position.refs]);
  const setReference = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(node => {
    if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(node) || node === null) {
      domReferenceRef.current = node;
      setDomReference(node);
    }

    // Backwards-compatibility for passing a virtual element to `reference`
    // after it has set the DOM reference.
    if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(position.refs.reference.current) || position.refs.reference.current === null ||
    // Don't allow setting virtual elements using the old technique back to
    // `null` to support `positionReference` + an unstable `reference`
    // callback ref.
    node !== null && !(0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(node)) {
      position.refs.setReference(node);
    }
  }, [position.refs]);
  const refs = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    ...position.refs,
    setReference,
    setPositionReference,
    domReference: domReferenceRef
  }), [position.refs, setReference, setPositionReference]);
  const elements = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    ...position.elements,
    domReference: domReference
  }), [position.elements, domReference]);
  const context = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    ...position,
    ...rootContext,
    refs,
    elements,
    nodeId
  }), [position, refs, elements, nodeId, rootContext]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    rootContext.dataRef.current.floatingContext = context;
    const node = tree == null ? void 0 : tree.nodesRef.current.find(node => node.id === nodeId);
    if (node) {
      node.context = context;
    }
  });
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    ...position,
    context,
    refs,
    elements
  }), [position, refs, elements, context]);
}

function isMacSafari() {
  return (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isMac)() && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isSafari)();
}
/**
 * Opens the floating element while the reference element has focus, like CSS
 * `:focus`.
 * @see https://floating-ui.com/docs/useFocus
 */
function useFocus(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    onOpenChange,
    events,
    dataRef,
    elements
  } = context;
  const {
    enabled = true,
    visibleOnly = true
  } = props;
  const blockFocusRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const timeoutRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  const keyboardModalityRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(true);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled) return;
    const win = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.getWindow)(elements.domReference);

    // If the reference was focused and the user left the tab/window, and the
    // floating element was not open, the focus should be blocked when they
    // return to the tab/window.
    function onBlur() {
      if (!open && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(elements.domReference) && elements.domReference === (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.domReference))) {
        blockFocusRef.current = true;
      }
    }
    function onKeyDown() {
      keyboardModalityRef.current = true;
    }
    function onPointerDown() {
      keyboardModalityRef.current = false;
    }
    win.addEventListener('blur', onBlur);
    if (isMacSafari()) {
      win.addEventListener('keydown', onKeyDown, true);
      win.addEventListener('pointerdown', onPointerDown, true);
    }
    return () => {
      win.removeEventListener('blur', onBlur);
      if (isMacSafari()) {
        win.removeEventListener('keydown', onKeyDown, true);
        win.removeEventListener('pointerdown', onPointerDown, true);
      }
    };
  }, [elements.domReference, open, enabled]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled) return;
    function onOpenChange(_ref) {
      let {
        reason
      } = _ref;
      if (reason === 'reference-press' || reason === 'escape-key') {
        blockFocusRef.current = true;
      }
    }
    events.on('openchange', onOpenChange);
    return () => {
      events.off('openchange', onOpenChange);
    };
  }, [events, enabled]);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    return () => {
      clearTimeoutIfSet(timeoutRef);
    };
  }, []);
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    onMouseLeave() {
      blockFocusRef.current = false;
    },
    onFocus(event) {
      if (blockFocusRef.current) return;
      const target = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getTarget)(event.nativeEvent);
      if (visibleOnly && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(target)) {
        // Safari fails to match `:focus-visible` if focus was initially
        // outside the document.
        if (isMacSafari() && !event.relatedTarget) {
          if (!keyboardModalityRef.current && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isTypeableElement)(target)) {
            return;
          }
        } else if (!(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.matchesFocusVisible)(target)) {
          return;
        }
      }
      onOpenChange(true, event.nativeEvent, 'focus');
    },
    onBlur(event) {
      blockFocusRef.current = false;
      const relatedTarget = event.relatedTarget;
      const nativeEvent = event.nativeEvent;

      // Hit the non-modal focus management portal guard. Focus will be
      // moved into the floating element immediately after.
      const movedToFocusGuard = (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(relatedTarget) && relatedTarget.hasAttribute(createAttribute('focus-guard')) && relatedTarget.getAttribute('data-type') === 'outside';

      // Wait for the window blur listener to fire.
      timeoutRef.current = window.setTimeout(() => {
        var _dataRef$current$floa;
        const activeEl = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)(elements.domReference ? elements.domReference.ownerDocument : document);

        // Focus left the page, keep it open.
        if (!relatedTarget && activeEl === elements.domReference) return;

        // When focusing the reference element (e.g. regular click), then
        // clicking into the floating element, prevent it from hiding.
        // Note: it must be focusable, e.g. `tabindex="-1"`.
        // We can not rely on relatedTarget to point to the correct element
        // as it will only point to the shadow host of the newly focused element
        // and not the element that actually has received focus if it is located
        // inside a shadow root.
        if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)((_dataRef$current$floa = dataRef.current.floatingContext) == null ? void 0 : _dataRef$current$floa.refs.floating.current, activeEl) || (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(elements.domReference, activeEl) || movedToFocusGuard) {
          return;
        }
        onOpenChange(false, nativeEvent, 'focus');
      });
    }
  }), [dataRef, elements.domReference, onOpenChange, visibleOnly]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference
  } : {}, [enabled, reference]);
}

function mergeProps(userProps, propsList, elementKey) {
  const map = new Map();
  const isItem = elementKey === 'item';
  let domUserProps = userProps;
  if (isItem && userProps) {
    const {
      [ACTIVE_KEY]: _,
      [SELECTED_KEY]: __,
      ...validProps
    } = userProps;
    domUserProps = validProps;
  }
  return {
    ...(elementKey === 'floating' && {
      tabIndex: -1,
      [FOCUSABLE_ATTRIBUTE]: ''
    }),
    ...domUserProps,
    ...propsList.map(value => {
      const propsOrGetProps = value ? value[elementKey] : null;
      if (typeof propsOrGetProps === 'function') {
        return userProps ? propsOrGetProps(userProps) : null;
      }
      return propsOrGetProps;
    }).concat(userProps).reduce((acc, props) => {
      if (!props) {
        return acc;
      }
      Object.entries(props).forEach(_ref => {
        let [key, value] = _ref;
        if (isItem && [ACTIVE_KEY, SELECTED_KEY].includes(key)) {
          return;
        }
        if (key.indexOf('on') === 0) {
          if (!map.has(key)) {
            map.set(key, []);
          }
          if (typeof value === 'function') {
            var _map$get;
            (_map$get = map.get(key)) == null || _map$get.push(value);
            acc[key] = function () {
              var _map$get2;
              for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = arguments[_key];
              }
              return (_map$get2 = map.get(key)) == null ? void 0 : _map$get2.map(fn => fn(...args)).find(val => val !== undefined);
            };
          }
        } else {
          acc[key] = value;
        }
      });
      return acc;
    }, {})
  };
}
/**
 * Merges an array of interaction hooks' props into prop getters, allowing
 * event handler functions to be composed together without overwriting one
 * another.
 * @see https://floating-ui.com/docs/useInteractions
 */
function useInteractions(propsList) {
  if (propsList === void 0) {
    propsList = [];
  }
  const referenceDeps = propsList.map(key => key == null ? void 0 : key.reference);
  const floatingDeps = propsList.map(key => key == null ? void 0 : key.floating);
  const itemDeps = propsList.map(key => key == null ? void 0 : key.item);
  const getReferenceProps = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(userProps => mergeProps(userProps, propsList, 'reference'),
  // eslint-disable-next-line react-hooks/exhaustive-deps
  referenceDeps);
  const getFloatingProps = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(userProps => mergeProps(userProps, propsList, 'floating'),
  // eslint-disable-next-line react-hooks/exhaustive-deps
  floatingDeps);
  const getItemProps = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(userProps => mergeProps(userProps, propsList, 'item'),
  // eslint-disable-next-line react-hooks/exhaustive-deps
  itemDeps);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    getReferenceProps,
    getFloatingProps,
    getItemProps
  }), [getReferenceProps, getFloatingProps, getItemProps]);
}

const ESCAPE = 'Escape';
function doSwitch(orientation, vertical, horizontal) {
  switch (orientation) {
    case 'vertical':
      return vertical;
    case 'horizontal':
      return horizontal;
    default:
      return vertical || horizontal;
  }
}
function isMainOrientationKey(key, orientation) {
  const vertical = key === ARROW_UP || key === ARROW_DOWN;
  const horizontal = key === ARROW_LEFT || key === ARROW_RIGHT;
  return doSwitch(orientation, vertical, horizontal);
}
function isMainOrientationToEndKey(key, orientation, rtl) {
  const vertical = key === ARROW_DOWN;
  const horizontal = rtl ? key === ARROW_LEFT : key === ARROW_RIGHT;
  return doSwitch(orientation, vertical, horizontal) || key === 'Enter' || key === ' ' || key === '';
}
function isCrossOrientationOpenKey(key, orientation, rtl) {
  const vertical = rtl ? key === ARROW_LEFT : key === ARROW_RIGHT;
  const horizontal = key === ARROW_DOWN;
  return doSwitch(orientation, vertical, horizontal);
}
function isCrossOrientationCloseKey(key, orientation, rtl, cols) {
  const vertical = rtl ? key === ARROW_RIGHT : key === ARROW_LEFT;
  const horizontal = key === ARROW_UP;
  if (orientation === 'both' || orientation === 'horizontal' && cols && cols > 1) {
    return key === ESCAPE;
  }
  return doSwitch(orientation, vertical, horizontal);
}
/**
 * Adds arrow key-based navigation of a list of items, either using real DOM
 * focus or virtual focus.
 * @see https://floating-ui.com/docs/useListNavigation
 */
function useListNavigation(context, props) {
  const {
    open,
    onOpenChange,
    elements,
    floatingId
  } = context;
  const {
    listRef,
    activeIndex,
    onNavigate: unstable_onNavigate = () => {},
    enabled = true,
    selectedIndex = null,
    allowEscape = false,
    loop = false,
    nested = false,
    rtl = false,
    virtual = false,
    focusItemOnOpen = 'auto',
    focusItemOnHover = true,
    openOnArrowKeyDown = true,
    disabledIndices = undefined,
    orientation = 'vertical',
    parentOrientation,
    cols = 1,
    scrollItemIntoView = true,
    virtualItemRef,
    itemSizes,
    dense = false
  } = props;
  if (true) {
    if (allowEscape) {
      if (!loop) {
        warn('`useListNavigation` looping must be enabled to allow escaping.');
      }
      if (!virtual) {
        warn('`useListNavigation` must be virtual to allow escaping.');
      }
    }
    if (orientation === 'vertical' && cols > 1) {
      warn('In grid list navigation mode (`cols` > 1), the `orientation` should', 'be either "horizontal" or "both".');
    }
  }
  const floatingFocusElement = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getFloatingFocusElement)(elements.floating);
  const floatingFocusElementRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(floatingFocusElement);
  const parentId = useFloatingParentNodeId();
  const tree = useFloatingTree();
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    context.dataRef.current.orientation = orientation;
  }, [context, orientation]);
  const onNavigate = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    unstable_onNavigate(indexRef.current === -1 ? null : indexRef.current);
  });
  const typeableComboboxReference = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isTypeableCombobox)(elements.domReference);
  const focusItemOnOpenRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(focusItemOnOpen);
  const indexRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(selectedIndex != null ? selectedIndex : -1);
  const keyRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const isPointerModalityRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(true);
  const previousOnNavigateRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(onNavigate);
  const previousMountedRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(!!elements.floating);
  const previousOpenRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(open);
  const forceSyncFocusRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const forceScrollIntoViewRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const disabledIndicesRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(disabledIndices);
  const latestOpenRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(open);
  const scrollItemIntoViewRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(scrollItemIntoView);
  const selectedIndexRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(selectedIndex);
  const [activeId, setActiveId] = react__WEBPACK_IMPORTED_MODULE_0__.useState();
  const [virtualId, setVirtualId] = react__WEBPACK_IMPORTED_MODULE_0__.useState();
  const focusItem = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(() => {
    function runFocus(item) {
      if (virtual) {
        var _item$id;
        if ((_item$id = item.id) != null && _item$id.endsWith('-fui-option')) {
          item.id = floatingId + "-" + Math.random().toString(16).slice(2, 10);
        }
        setActiveId(item.id);
        tree == null || tree.events.emit('virtualfocus', item);
        if (virtualItemRef) {
          virtualItemRef.current = item;
        }
      } else {
        enqueueFocus(item, {
          sync: forceSyncFocusRef.current,
          preventScroll: true
        });
      }
    }
    const initialItem = listRef.current[indexRef.current];
    const forceScrollIntoView = forceScrollIntoViewRef.current;
    if (initialItem) {
      runFocus(initialItem);
    }
    const scheduler = forceSyncFocusRef.current ? v => v() : requestAnimationFrame;
    scheduler(() => {
      const waitedItem = listRef.current[indexRef.current] || initialItem;
      if (!waitedItem) return;
      if (!initialItem) {
        runFocus(waitedItem);
      }
      const scrollIntoViewOptions = scrollItemIntoViewRef.current;
      const shouldScrollIntoView = scrollIntoViewOptions && item && (forceScrollIntoView || !isPointerModalityRef.current);
      if (shouldScrollIntoView) {
        // JSDOM doesn't support `.scrollIntoView()` but it's widely supported
        // by all browsers.
        waitedItem.scrollIntoView == null || waitedItem.scrollIntoView(typeof scrollIntoViewOptions === 'boolean' ? {
          block: 'nearest',
          inline: 'nearest'
        } : scrollIntoViewOptions);
      }
    });
  });

  // Sync `selectedIndex` to be the `activeIndex` upon opening the floating
  // element. Also, reset `activeIndex` upon closing the floating element.
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!enabled) return;
    if (open && elements.floating) {
      if (focusItemOnOpenRef.current && selectedIndex != null) {
        // Regardless of the pointer modality, we want to ensure the selected
        // item comes into view when the floating element is opened.
        forceScrollIntoViewRef.current = true;
        indexRef.current = selectedIndex;
        onNavigate();
      }
    } else if (previousMountedRef.current) {
      // Since the user can specify `onNavigate` conditionally
      // (onNavigate: open ? setActiveIndex : setSelectedIndex),
      // we store and call the previous function.
      indexRef.current = -1;
      previousOnNavigateRef.current();
    }
  }, [enabled, open, elements.floating, selectedIndex, onNavigate]);

  // Sync `activeIndex` to be the focused item while the floating element is
  // open.
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!enabled) return;
    if (!open) return;
    if (!elements.floating) return;
    if (activeIndex == null) {
      forceSyncFocusRef.current = false;
      if (selectedIndexRef.current != null) {
        return;
      }

      // Reset while the floating element was open (e.g. the list changed).
      if (previousMountedRef.current) {
        indexRef.current = -1;
        focusItem();
      }

      // Initial sync.
      if ((!previousOpenRef.current || !previousMountedRef.current) && focusItemOnOpenRef.current && (keyRef.current != null || focusItemOnOpenRef.current === true && keyRef.current == null)) {
        let runs = 0;
        const waitForListPopulated = () => {
          if (listRef.current[0] == null) {
            // Avoid letting the browser paint if possible on the first try,
            // otherwise use rAF. Don't try more than twice, since something
            // is wrong otherwise.
            if (runs < 2) {
              const scheduler = runs ? requestAnimationFrame : queueMicrotask;
              scheduler(waitForListPopulated);
            }
            runs++;
          } else {
            indexRef.current = keyRef.current == null || isMainOrientationToEndKey(keyRef.current, orientation, rtl) || nested ? (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMinListIndex)(listRef, disabledIndicesRef.current) : (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMaxListIndex)(listRef, disabledIndicesRef.current);
            keyRef.current = null;
            onNavigate();
          }
        };
        waitForListPopulated();
      }
    } else if (!(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isIndexOutOfListBounds)(listRef, activeIndex)) {
      indexRef.current = activeIndex;
      focusItem();
      forceScrollIntoViewRef.current = false;
    }
  }, [enabled, open, elements.floating, activeIndex, selectedIndexRef, nested, listRef, orientation, rtl, onNavigate, focusItem, disabledIndicesRef]);

  // Ensure the parent floating element has focus when a nested child closes
  // to allow arrow key navigation to work after the pointer leaves the child.
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    var _nodes$find;
    if (!enabled || elements.floating || !tree || virtual || !previousMountedRef.current) {
      return;
    }
    const nodes = tree.nodesRef.current;
    const parent = (_nodes$find = nodes.find(node => node.id === parentId)) == null || (_nodes$find = _nodes$find.context) == null ? void 0 : _nodes$find.elements.floating;
    const activeEl = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDocument)(elements.floating));
    const treeContainsActiveEl = nodes.some(node => node.context && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.contains)(node.context.elements.floating, activeEl));
    if (parent && !treeContainsActiveEl && isPointerModalityRef.current) {
      parent.focus({
        preventScroll: true
      });
    }
  }, [enabled, elements.floating, tree, parentId, virtual]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!enabled) return;
    if (!tree) return;
    if (!virtual) return;
    if (parentId) return;
    function handleVirtualFocus(item) {
      setVirtualId(item.id);
      if (virtualItemRef) {
        virtualItemRef.current = item;
      }
    }
    tree.events.on('virtualfocus', handleVirtualFocus);
    return () => {
      tree.events.off('virtualfocus', handleVirtualFocus);
    };
  }, [enabled, tree, virtual, parentId, virtualItemRef]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    previousOnNavigateRef.current = onNavigate;
    previousOpenRef.current = open;
    previousMountedRef.current = !!elements.floating;
  });
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!open) {
      keyRef.current = null;
      focusItemOnOpenRef.current = focusItemOnOpen;
    }
  }, [open, focusItemOnOpen]);
  const hasActiveIndex = activeIndex != null;
  const item = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    function syncCurrentTarget(currentTarget) {
      if (!latestOpenRef.current) return;
      const index = listRef.current.indexOf(currentTarget);
      if (index !== -1 && indexRef.current !== index) {
        indexRef.current = index;
        onNavigate();
      }
    }
    const props = {
      onFocus(_ref) {
        let {
          currentTarget
        } = _ref;
        forceSyncFocusRef.current = true;
        syncCurrentTarget(currentTarget);
      },
      onClick: _ref2 => {
        let {
          currentTarget
        } = _ref2;
        return currentTarget.focus({
          preventScroll: true
        });
      },
      // Safari
      onMouseMove(_ref3) {
        let {
          currentTarget
        } = _ref3;
        forceSyncFocusRef.current = true;
        forceScrollIntoViewRef.current = false;
        if (focusItemOnHover) {
          syncCurrentTarget(currentTarget);
        }
      },
      onPointerLeave(_ref4) {
        let {
          pointerType
        } = _ref4;
        if (!isPointerModalityRef.current || pointerType === 'touch') {
          return;
        }
        forceSyncFocusRef.current = true;
        if (!focusItemOnHover) {
          return;
        }
        indexRef.current = -1;
        onNavigate();
        if (!virtual) {
          var _floatingFocusElement;
          (_floatingFocusElement = floatingFocusElementRef.current) == null || _floatingFocusElement.focus({
            preventScroll: true
          });
        }
      }
    };
    return props;
  }, [latestOpenRef, floatingFocusElementRef, focusItemOnHover, listRef, onNavigate, virtual]);
  const getParentOrientation = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(() => {
    var _tree$nodesRef$curren;
    return parentOrientation != null ? parentOrientation : tree == null || (_tree$nodesRef$curren = tree.nodesRef.current.find(node => node.id === parentId)) == null || (_tree$nodesRef$curren = _tree$nodesRef$curren.context) == null || (_tree$nodesRef$curren = _tree$nodesRef$curren.dataRef) == null ? void 0 : _tree$nodesRef$curren.current.orientation;
  }, [parentId, tree, parentOrientation]);
  const commonOnKeyDown = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    isPointerModalityRef.current = false;
    forceSyncFocusRef.current = true;

    // When composing a character, Chrome fires ArrowDown twice. Firefox/Safari
    // don't appear to suffer from this. `event.isComposing` is avoided due to
    // Safari not supporting it properly (although it's not needed in the first
    // place for Safari, just avoiding any possible issues).
    if (event.which === 229) {
      return;
    }

    // If the floating element is animating out, ignore navigation. Otherwise,
    // the `activeIndex` gets set to 0 despite not being open so the next time
    // the user ArrowDowns, the first item won't be focused.
    if (!latestOpenRef.current && event.currentTarget === floatingFocusElementRef.current) {
      return;
    }
    if (nested && isCrossOrientationCloseKey(event.key, orientation, rtl, cols)) {
      // If the nested list's close key is also the parent navigation key,
      // let the parent navigate. Otherwise, stop propagating the event.
      if (!isMainOrientationKey(event.key, getParentOrientation())) {
        (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
      }
      onOpenChange(false, event.nativeEvent, 'list-navigation');
      if ((0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isHTMLElement)(elements.domReference)) {
        if (virtual) {
          tree == null || tree.events.emit('virtualfocus', elements.domReference);
        } else {
          elements.domReference.focus();
        }
      }
      return;
    }
    const currentIndex = indexRef.current;
    const minIndex = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMinListIndex)(listRef, disabledIndices);
    const maxIndex = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMaxListIndex)(listRef, disabledIndices);
    if (!typeableComboboxReference) {
      if (event.key === 'Home') {
        (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
        indexRef.current = minIndex;
        onNavigate();
      }
      if (event.key === 'End') {
        (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
        indexRef.current = maxIndex;
        onNavigate();
      }
    }

    // Grid navigation.
    if (cols > 1) {
      const sizes = itemSizes || Array.from({
        length: listRef.current.length
      }, () => ({
        width: 1,
        height: 1
      }));
      // To calculate movements on the grid, we use hypothetical cell indices
      // as if every item was 1x1, then convert back to real indices.
      const cellMap = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.createGridCellMap)(sizes, cols, dense);
      const minGridIndex = cellMap.findIndex(index => index != null && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isListIndexDisabled)(listRef, index, disabledIndices));
      // last enabled index
      const maxGridIndex = cellMap.reduce((foundIndex, index, cellIndex) => index != null && !(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isListIndexDisabled)(listRef, index, disabledIndices) ? cellIndex : foundIndex, -1);
      const index = cellMap[(0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getGridNavigatedIndex)({
        current: cellMap.map(itemIndex => itemIndex != null ? listRef.current[itemIndex] : null)
      }, {
        event,
        orientation,
        loop,
        rtl,
        cols,
        // treat undefined (empty grid spaces) as disabled indices so we
        // don't end up in them
        disabledIndices: (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getGridCellIndices)([...((typeof disabledIndices !== 'function' ? disabledIndices : null) || listRef.current.map((_, index) => (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isListIndexDisabled)(listRef, index, disabledIndices) ? index : undefined)), undefined], cellMap),
        minIndex: minGridIndex,
        maxIndex: maxGridIndex,
        prevIndex: (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getGridCellIndexOfCorner)(indexRef.current > maxIndex ? minIndex : indexRef.current, sizes, cellMap, cols,
        // use a corner matching the edge closest to the direction
        // we're moving in so we don't end up in the same item. Prefer
        // top/left over bottom/right.
        event.key === ARROW_DOWN ? 'bl' : event.key === (rtl ? ARROW_LEFT : ARROW_RIGHT) ? 'tr' : 'tl'),
        stopEvent: true
      })];
      if (index != null) {
        indexRef.current = index;
        onNavigate();
      }
      if (orientation === 'both') {
        return;
      }
    }
    if (isMainOrientationKey(event.key, orientation)) {
      (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);

      // Reset the index if no item is focused.
      if (open && !virtual && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.activeElement)(event.currentTarget.ownerDocument) === event.currentTarget) {
        indexRef.current = isMainOrientationToEndKey(event.key, orientation, rtl) ? minIndex : maxIndex;
        onNavigate();
        return;
      }
      if (isMainOrientationToEndKey(event.key, orientation, rtl)) {
        if (loop) {
          indexRef.current = currentIndex >= maxIndex ? allowEscape && currentIndex !== listRef.current.length ? -1 : minIndex : (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.findNonDisabledListIndex)(listRef, {
            startingIndex: currentIndex,
            disabledIndices
          });
        } else {
          indexRef.current = Math.min(maxIndex, (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.findNonDisabledListIndex)(listRef, {
            startingIndex: currentIndex,
            disabledIndices
          }));
        }
      } else {
        if (loop) {
          indexRef.current = currentIndex <= minIndex ? allowEscape && currentIndex !== -1 ? listRef.current.length : maxIndex : (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.findNonDisabledListIndex)(listRef, {
            startingIndex: currentIndex,
            decrement: true,
            disabledIndices
          });
        } else {
          indexRef.current = Math.max(minIndex, (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.findNonDisabledListIndex)(listRef, {
            startingIndex: currentIndex,
            decrement: true,
            disabledIndices
          }));
        }
      }
      if ((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isIndexOutOfListBounds)(listRef, indexRef.current)) {
        indexRef.current = -1;
      }
      onNavigate();
    }
  });
  const ariaActiveDescendantProp = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    return virtual && open && hasActiveIndex && {
      'aria-activedescendant': virtualId || activeId
    };
  }, [virtual, open, hasActiveIndex, virtualId, activeId]);
  const floating = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    return {
      'aria-orientation': orientation === 'both' ? undefined : orientation,
      ...(!typeableComboboxReference ? ariaActiveDescendantProp : {}),
      onKeyDown: commonOnKeyDown,
      onPointerMove() {
        isPointerModalityRef.current = true;
      }
    };
  }, [ariaActiveDescendantProp, commonOnKeyDown, orientation, typeableComboboxReference]);
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    function checkVirtualMouse(event) {
      if (focusItemOnOpen === 'auto' && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isVirtualClick)(event.nativeEvent)) {
        focusItemOnOpenRef.current = true;
      }
    }
    function checkVirtualPointer(event) {
      // `pointerdown` fires first, reset the state then perform the checks.
      focusItemOnOpenRef.current = focusItemOnOpen;
      if (focusItemOnOpen === 'auto' && (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.isVirtualPointerEvent)(event.nativeEvent)) {
        focusItemOnOpenRef.current = true;
      }
    }
    return {
      ...ariaActiveDescendantProp,
      onKeyDown(event) {
        isPointerModalityRef.current = false;
        const isArrowKey = event.key.startsWith('Arrow');
        const isHomeOrEndKey = ['Home', 'End'].includes(event.key);
        const isMoveKey = isArrowKey || isHomeOrEndKey;
        const isCrossOpenKey = isCrossOrientationOpenKey(event.key, orientation, rtl);
        const isCrossCloseKey = isCrossOrientationCloseKey(event.key, orientation, rtl, cols);
        const isParentCrossOpenKey = isCrossOrientationOpenKey(event.key, getParentOrientation(), rtl);
        const isMainKey = isMainOrientationKey(event.key, orientation);
        const isNavigationKey = (nested ? isParentCrossOpenKey : isMainKey) || event.key === 'Enter' || event.key.trim() === '';
        if (virtual && open) {
          const rootNode = tree == null ? void 0 : tree.nodesRef.current.find(node => node.parentId == null);
          const deepestNode = tree && rootNode ? (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getDeepestNode)(tree.nodesRef.current, rootNode.id) : null;
          if (isMoveKey && deepestNode && virtualItemRef) {
            const eventObject = new KeyboardEvent('keydown', {
              key: event.key,
              bubbles: true
            });
            if (isCrossOpenKey || isCrossCloseKey) {
              var _deepestNode$context, _deepestNode$context2;
              const isCurrentTarget = ((_deepestNode$context = deepestNode.context) == null ? void 0 : _deepestNode$context.elements.domReference) === event.currentTarget;
              const dispatchItem = isCrossCloseKey && !isCurrentTarget ? (_deepestNode$context2 = deepestNode.context) == null ? void 0 : _deepestNode$context2.elements.domReference : isCrossOpenKey ? listRef.current.find(item => (item == null ? void 0 : item.id) === activeId) : null;
              if (dispatchItem) {
                (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
                dispatchItem.dispatchEvent(eventObject);
                setVirtualId(undefined);
              }
            }
            if ((isMainKey || isHomeOrEndKey) && deepestNode.context) {
              if (deepestNode.context.open && deepestNode.parentId && event.currentTarget !== deepestNode.context.elements.domReference) {
                var _deepestNode$context$;
                (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
                (_deepestNode$context$ = deepestNode.context.elements.domReference) == null || _deepestNode$context$.dispatchEvent(eventObject);
                return;
              }
            }
          }
          return commonOnKeyDown(event);
        }
        // If a floating element should not open on arrow key down, avoid
        // setting `activeIndex` while it's closed.
        if (!open && !openOnArrowKeyDown && isArrowKey) {
          return;
        }
        if (isNavigationKey) {
          const isParentMainKey = isMainOrientationKey(event.key, getParentOrientation());
          keyRef.current = nested && isParentMainKey ? null : event.key;
        }
        if (nested) {
          if (isParentCrossOpenKey) {
            (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
            if (open) {
              indexRef.current = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getMinListIndex)(listRef, disabledIndicesRef.current);
              onNavigate();
            } else {
              onOpenChange(true, event.nativeEvent, 'list-navigation');
            }
          }
          return;
        }
        if (isMainKey) {
          if (selectedIndex != null) {
            indexRef.current = selectedIndex;
          }
          (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
          if (!open && openOnArrowKeyDown) {
            onOpenChange(true, event.nativeEvent, 'list-navigation');
          } else {
            commonOnKeyDown(event);
          }
          if (open) {
            onNavigate();
          }
        }
      },
      onFocus() {
        if (open && !virtual) {
          indexRef.current = -1;
          onNavigate();
        }
      },
      onPointerDown: checkVirtualPointer,
      onPointerEnter: checkVirtualPointer,
      onMouseDown: checkVirtualMouse,
      onClick: checkVirtualMouse
    };
  }, [activeId, ariaActiveDescendantProp, cols, commonOnKeyDown, disabledIndicesRef, focusItemOnOpen, listRef, nested, onNavigate, onOpenChange, open, openOnArrowKeyDown, orientation, getParentOrientation, rtl, selectedIndex, tree, virtual, virtualItemRef]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference,
    floating,
    item
  } : {}, [enabled, reference, floating, item]);
}

const componentRoleToAriaRoleMap = /*#__PURE__*/new Map([['select', 'listbox'], ['combobox', 'listbox'], ['label', false]]);

/**
 * Adds base screen reader props to the reference and floating elements for a
 * given floating element `role`.
 * @see https://floating-ui.com/docs/useRole
 */
function useRole(context, props) {
  var _elements$domReferenc, _componentRoleToAriaR;
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    elements,
    floatingId: defaultFloatingId
  } = context;
  const {
    enabled = true,
    role = 'dialog'
  } = props;
  const defaultReferenceId = useId();
  const referenceId = ((_elements$domReferenc = elements.domReference) == null ? void 0 : _elements$domReferenc.id) || defaultReferenceId;
  const floatingId = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    var _getFloatingFocusElem;
    return ((_getFloatingFocusElem = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getFloatingFocusElement)(elements.floating)) == null ? void 0 : _getFloatingFocusElem.id) || defaultFloatingId;
  }, [elements.floating, defaultFloatingId]);
  const ariaRole = (_componentRoleToAriaR = componentRoleToAriaRoleMap.get(role)) != null ? _componentRoleToAriaR : role;
  const parentId = useFloatingParentNodeId();
  const isNested = parentId != null;
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    if (ariaRole === 'tooltip' || role === 'label') {
      return {
        ["aria-" + (role === 'label' ? 'labelledby' : 'describedby')]: open ? floatingId : undefined
      };
    }
    return {
      'aria-expanded': open ? 'true' : 'false',
      'aria-haspopup': ariaRole === 'alertdialog' ? 'dialog' : ariaRole,
      'aria-controls': open ? floatingId : undefined,
      ...(ariaRole === 'listbox' && {
        role: 'combobox'
      }),
      ...(ariaRole === 'menu' && {
        id: referenceId
      }),
      ...(ariaRole === 'menu' && isNested && {
        role: 'menuitem'
      }),
      ...(role === 'select' && {
        'aria-autocomplete': 'none'
      }),
      ...(role === 'combobox' && {
        'aria-autocomplete': 'list'
      })
    };
  }, [ariaRole, floatingId, isNested, open, referenceId, role]);
  const floating = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    const floatingProps = {
      id: floatingId,
      ...(ariaRole && {
        role: ariaRole
      })
    };
    if (ariaRole === 'tooltip' || role === 'label') {
      return floatingProps;
    }
    return {
      ...floatingProps,
      ...(ariaRole === 'menu' && {
        'aria-labelledby': referenceId
      })
    };
  }, [ariaRole, floatingId, referenceId, role]);
  const item = react__WEBPACK_IMPORTED_MODULE_0__.useCallback(_ref => {
    let {
      active,
      selected
    } = _ref;
    const commonProps = {
      role: 'option',
      ...(active && {
        id: floatingId + "-fui-option"
      })
    };

    // For `menu`, we are unable to tell if the item is a `menuitemradio`
    // or `menuitemcheckbox`. For backwards-compatibility reasons, also
    // avoid defaulting to `menuitem` as it may overwrite custom role props.
    switch (role) {
      case 'select':
      case 'combobox':
        return {
          ...commonProps,
          'aria-selected': selected
        };
    }
    return {};
  }, [floatingId, role]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference,
    floating,
    item
  } : {}, [enabled, reference, floating, item]);
}

// Converts a JS style key like `backgroundColor` to a CSS transition-property
// like `background-color`.
const camelCaseToKebabCase = str => str.replace(/[A-Z]+(?![a-z])|[A-Z]/g, ($, ofs) => (ofs ? '-' : '') + $.toLowerCase());
function execWithArgsOrReturn(valueOrFn, args) {
  return typeof valueOrFn === 'function' ? valueOrFn(args) : valueOrFn;
}
function useDelayUnmount(open, durationMs) {
  const [isMounted, setIsMounted] = react__WEBPACK_IMPORTED_MODULE_0__.useState(open);
  if (open && !isMounted) {
    setIsMounted(true);
  }
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!open && isMounted) {
      const timeout = setTimeout(() => setIsMounted(false), durationMs);
      return () => clearTimeout(timeout);
    }
  }, [open, isMounted, durationMs]);
  return isMounted;
}
/**
 * Provides a status string to apply CSS transitions to a floating element,
 * correctly handling placement-aware transitions.
 * @see https://floating-ui.com/docs/useTransition#usetransitionstatus
 */
function useTransitionStatus(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    open,
    elements: {
      floating
    }
  } = context;
  const {
    duration = 250
  } = props;
  const isNumberDuration = typeof duration === 'number';
  const closeDuration = (isNumberDuration ? duration : duration.close) || 0;
  const [status, setStatus] = react__WEBPACK_IMPORTED_MODULE_0__.useState('unmounted');
  const isMounted = useDelayUnmount(open, closeDuration);
  if (!isMounted && status === 'close') {
    setStatus('unmounted');
  }
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (!floating) return;
    if (open) {
      setStatus('initial');
      const frame = requestAnimationFrame(() => {
        // Ensure it opens before paint. With `FloatingDelayGroup`,
        // this avoids a flicker when moving between floating elements
        // to ensure one is always open with no missing frames.
        react_dom__WEBPACK_IMPORTED_MODULE_5__.flushSync(() => {
          setStatus('open');
        });
      });
      return () => {
        cancelAnimationFrame(frame);
      };
    }
    setStatus('close');
  }, [open, floating]);
  return {
    isMounted,
    status
  };
}
/**
 * Provides styles to apply CSS transitions to a floating element, correctly
 * handling placement-aware transitions. Wrapper around `useTransitionStatus`.
 * @see https://floating-ui.com/docs/useTransition#usetransitionstyles
 */
function useTransitionStyles(context, props) {
  if (props === void 0) {
    props = {};
  }
  const {
    initial: unstable_initial = {
      opacity: 0
    },
    open: unstable_open,
    close: unstable_close,
    common: unstable_common,
    duration = 250
  } = props;
  const placement = context.placement;
  const side = placement.split('-')[0];
  const fnArgs = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    side,
    placement
  }), [side, placement]);
  const isNumberDuration = typeof duration === 'number';
  const openDuration = (isNumberDuration ? duration : duration.open) || 0;
  const closeDuration = (isNumberDuration ? duration : duration.close) || 0;
  const [styles, setStyles] = react__WEBPACK_IMPORTED_MODULE_0__.useState(() => ({
    ...execWithArgsOrReturn(unstable_common, fnArgs),
    ...execWithArgsOrReturn(unstable_initial, fnArgs)
  }));
  const {
    isMounted,
    status
  } = useTransitionStatus(context, {
    duration
  });
  const initialRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(unstable_initial);
  const openRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(unstable_open);
  const closeRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(unstable_close);
  const commonRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(unstable_common);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    const initialStyles = execWithArgsOrReturn(initialRef.current, fnArgs);
    const closeStyles = execWithArgsOrReturn(closeRef.current, fnArgs);
    const commonStyles = execWithArgsOrReturn(commonRef.current, fnArgs);
    const openStyles = execWithArgsOrReturn(openRef.current, fnArgs) || Object.keys(initialStyles).reduce((acc, key) => {
      acc[key] = '';
      return acc;
    }, {});
    if (status === 'initial') {
      setStyles(styles => ({
        transitionProperty: styles.transitionProperty,
        ...commonStyles,
        ...initialStyles
      }));
    }
    if (status === 'open') {
      setStyles({
        transitionProperty: Object.keys(openStyles).map(camelCaseToKebabCase).join(','),
        transitionDuration: openDuration + "ms",
        ...commonStyles,
        ...openStyles
      });
    }
    if (status === 'close') {
      const styles = closeStyles || initialStyles;
      setStyles({
        transitionProperty: Object.keys(styles).map(camelCaseToKebabCase).join(','),
        transitionDuration: closeDuration + "ms",
        ...commonStyles,
        ...styles
      });
    }
  }, [closeDuration, closeRef, initialRef, openRef, commonRef, openDuration, status, fnArgs]);
  return {
    isMounted,
    styles
  };
}

/**
 * Provides a matching callback that can be used to focus an item as the user
 * types, often used in tandem with `useListNavigation()`.
 * @see https://floating-ui.com/docs/useTypeahead
 */
function useTypeahead(context, props) {
  var _ref;
  const {
    open,
    dataRef
  } = context;
  const {
    listRef,
    activeIndex,
    onMatch: unstable_onMatch,
    onTypingChange: unstable_onTypingChange,
    enabled = true,
    findMatch = null,
    resetMs = 750,
    ignoreKeys = [],
    selectedIndex = null
  } = props;
  const timeoutIdRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(-1);
  const stringRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef('');
  const prevIndexRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef((_ref = selectedIndex != null ? selectedIndex : activeIndex) != null ? _ref : -1);
  const matchIndexRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const onMatch = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(unstable_onMatch);
  const onTypingChange = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(unstable_onTypingChange);
  const findMatchRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(findMatch);
  const ignoreKeysRef = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useLatestRef)(ignoreKeys);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    if (open) {
      clearTimeoutIfSet(timeoutIdRef);
      matchIndexRef.current = null;
      stringRef.current = '';
    }
  }, [open]);
  (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useModernLayoutEffect)(() => {
    // Sync arrow key navigation but not typeahead navigation.
    if (open && stringRef.current === '') {
      var _ref2;
      prevIndexRef.current = (_ref2 = selectedIndex != null ? selectedIndex : activeIndex) != null ? _ref2 : -1;
    }
  }, [open, selectedIndex, activeIndex]);
  const setTypingChange = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(value => {
    if (value) {
      if (!dataRef.current.typing) {
        dataRef.current.typing = value;
        onTypingChange(value);
      }
    } else {
      if (dataRef.current.typing) {
        dataRef.current.typing = value;
        onTypingChange(value);
      }
    }
  });
  const onKeyDown = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(event => {
    function getMatchingIndex(list, orderedList, string) {
      const str = findMatchRef.current ? findMatchRef.current(orderedList, string) : orderedList.find(text => (text == null ? void 0 : text.toLocaleLowerCase().indexOf(string.toLocaleLowerCase())) === 0);
      return str ? list.indexOf(str) : -1;
    }
    const listContent = listRef.current;
    if (stringRef.current.length > 0 && stringRef.current[0] !== ' ') {
      if (getMatchingIndex(listContent, listContent, stringRef.current) === -1) {
        setTypingChange(false);
      } else if (event.key === ' ') {
        (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
      }
    }
    if (listContent == null || ignoreKeysRef.current.includes(event.key) ||
    // Character key.
    event.key.length !== 1 ||
    // Modifier key.
    event.ctrlKey || event.metaKey || event.altKey) {
      return;
    }
    if (open && event.key !== ' ') {
      (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.stopEvent)(event);
      setTypingChange(true);
    }

    // Bail out if the list contains a word like "llama" or "aaron". TODO:
    // allow it in this case, too.
    const allowRapidSuccessionOfFirstLetter = listContent.every(text => {
      var _text$, _text$2;
      return text ? ((_text$ = text[0]) == null ? void 0 : _text$.toLocaleLowerCase()) !== ((_text$2 = text[1]) == null ? void 0 : _text$2.toLocaleLowerCase()) : true;
    });

    // Allows the user to cycle through items that start with the same letter
    // in rapid succession.
    if (allowRapidSuccessionOfFirstLetter && stringRef.current === event.key) {
      stringRef.current = '';
      prevIndexRef.current = matchIndexRef.current;
    }
    stringRef.current += event.key;
    clearTimeoutIfSet(timeoutIdRef);
    timeoutIdRef.current = window.setTimeout(() => {
      stringRef.current = '';
      prevIndexRef.current = matchIndexRef.current;
      setTypingChange(false);
    }, resetMs);
    const prevIndex = prevIndexRef.current;
    const index = getMatchingIndex(listContent, [...listContent.slice((prevIndex || 0) + 1), ...listContent.slice(0, (prevIndex || 0) + 1)], stringRef.current);
    if (index !== -1) {
      onMatch(index);
      matchIndexRef.current = index;
    } else if (event.key !== ' ') {
      stringRef.current = '';
      setTypingChange(false);
    }
  });
  const reference = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    onKeyDown
  }), [onKeyDown]);
  const floating = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => {
    return {
      onKeyDown,
      onKeyUp(event) {
        if (event.key === ' ') {
          setTypingChange(false);
        }
      }
    };
  }, [onKeyDown, setTypingChange]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    reference,
    floating
  } : {}, [enabled, reference, floating]);
}

function getArgsWithCustomFloatingHeight(state, height) {
  return {
    ...state,
    rects: {
      ...state.rects,
      floating: {
        ...state.rects.floating,
        height
      }
    }
  };
}
/**
 * Positions the floating element such that an inner element inside of it is
 * anchored to the reference element.
 * @see https://floating-ui.com/docs/inner
 * @deprecated
 */
const inner = props => ({
  name: 'inner',
  options: props,
  async fn(state) {
    const {
      listRef,
      overflowRef,
      onFallbackChange,
      offset: innerOffset = 0,
      index = 0,
      minItemsVisible = 4,
      referenceOverflowThreshold = 0,
      scrollRef,
      ...detectOverflowOptions
    } = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__.evaluate)(props, state);
    const {
      rects,
      platform,
      elements: {
        floating
      }
    } = state;
    const item = listRef.current[index];
    const scrollEl = (scrollRef == null ? void 0 : scrollRef.current) || floating;

    // Valid combinations:
    // 1. Floating element is the scrollRef and has a border (default)
    // 2. Floating element is not the scrollRef, floating element has a border
    // 3. Floating element is not the scrollRef, scrollRef has a border
    // Floating > {...getFloatingProps()} wrapper > scrollRef > items is not
    // allowed as VoiceOver doesn't work.
    const clientTop = floating.clientTop || scrollEl.clientTop;
    const floatingIsBordered = floating.clientTop !== 0;
    const scrollElIsBordered = scrollEl.clientTop !== 0;
    const floatingIsScrollEl = floating === scrollEl;
    if (true) {
      if (!state.placement.startsWith('bottom')) {
        warn('`placement` side must be "bottom" when using the `inner`', 'middleware.');
      }
    }
    if (!item) {
      return {};
    }
    const nextArgs = {
      ...state,
      ...(await (0,_floating_ui_react_dom__WEBPACK_IMPORTED_MODULE_6__.offset)(-item.offsetTop - floating.clientTop - rects.reference.height / 2 - item.offsetHeight / 2 - innerOffset).fn(state))
    };
    const overflow = await platform.detectOverflow(getArgsWithCustomFloatingHeight(nextArgs, scrollEl.scrollHeight + clientTop + floating.clientTop), detectOverflowOptions);
    const refOverflow = await platform.detectOverflow(nextArgs, {
      ...detectOverflowOptions,
      elementContext: 'reference'
    });
    const diffY = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__.max)(0, overflow.top);
    const nextY = nextArgs.y + diffY;
    const isScrollable = scrollEl.scrollHeight > scrollEl.clientHeight;
    const rounder = isScrollable ? v => v : _floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__.round;
    const maxHeight = rounder((0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__.max)(0, scrollEl.scrollHeight + (floatingIsBordered && floatingIsScrollEl || scrollElIsBordered ? clientTop * 2 : 0) - diffY - (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__.max)(0, overflow.bottom)));
    scrollEl.style.maxHeight = maxHeight + "px";
    scrollEl.scrollTop = diffY;

    // There is not enough space, fallback to standard anchored positioning
    if (onFallbackChange) {
      const shouldFallback = scrollEl.offsetHeight < item.offsetHeight * (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_8__.min)(minItemsVisible, listRef.current.length) - 1 || refOverflow.top >= -referenceOverflowThreshold || refOverflow.bottom >= -referenceOverflowThreshold;
      react_dom__WEBPACK_IMPORTED_MODULE_5__.flushSync(() => onFallbackChange(shouldFallback));
    }
    if (overflowRef) {
      overflowRef.current = await platform.detectOverflow(getArgsWithCustomFloatingHeight({
        ...nextArgs,
        y: nextY
      }, scrollEl.offsetHeight + clientTop + floating.clientTop), detectOverflowOptions);
    }
    return {
      y: nextY
    };
  }
});
/**
 * Changes the `inner` middleware's `offset` upon a `wheel` event to
 * expand the floating element's height, revealing more list items.
 * @see https://floating-ui.com/docs/inner
 * @deprecated
 */
function useInnerOffset(context, props) {
  const {
    open,
    elements
  } = context;
  const {
    enabled = true,
    overflowRef,
    scrollRef,
    onChange: unstable_onChange
  } = props;
  const onChange = (0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.useEffectEvent)(unstable_onChange);
  const controlledScrollingRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(false);
  const prevScrollTopRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  const initialOverflowRef = react__WEBPACK_IMPORTED_MODULE_0__.useRef(null);
  react__WEBPACK_IMPORTED_MODULE_0__.useEffect(() => {
    if (!enabled) return;
    function onWheel(e) {
      if (e.ctrlKey || !el || overflowRef.current == null) {
        return;
      }
      const dY = e.deltaY;
      const isAtTop = overflowRef.current.top >= -0.5;
      const isAtBottom = overflowRef.current.bottom >= -0.5;
      const remainingScroll = el.scrollHeight - el.clientHeight;
      const sign = dY < 0 ? -1 : 1;
      const method = dY < 0 ? 'max' : 'min';
      if (el.scrollHeight <= el.clientHeight) {
        return;
      }
      if (!isAtTop && dY > 0 || !isAtBottom && dY < 0) {
        e.preventDefault();
        react_dom__WEBPACK_IMPORTED_MODULE_5__.flushSync(() => {
          onChange(d => d + Math[method](dY, remainingScroll * sign));
        });
      } else if (/firefox/i.test((0,_floating_ui_react_utils__WEBPACK_IMPORTED_MODULE_1__.getUserAgent)())) {
        // Needed to propagate scrolling during momentum scrolling phase once
        // it gets limited by the boundary. UX improvement, not critical.
        el.scrollTop += dY;
      }
    }
    const el = (scrollRef == null ? void 0 : scrollRef.current) || elements.floating;
    if (open && el) {
      el.addEventListener('wheel', onWheel);

      // Wait for the position to be ready.
      requestAnimationFrame(() => {
        prevScrollTopRef.current = el.scrollTop;
        if (overflowRef.current != null) {
          initialOverflowRef.current = {
            ...overflowRef.current
          };
        }
      });
      return () => {
        prevScrollTopRef.current = null;
        initialOverflowRef.current = null;
        el.removeEventListener('wheel', onWheel);
      };
    }
  }, [enabled, open, elements.floating, overflowRef, scrollRef, onChange]);
  const floating = react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => ({
    onKeyDown() {
      controlledScrollingRef.current = true;
    },
    onWheel() {
      controlledScrollingRef.current = false;
    },
    onPointerMove() {
      controlledScrollingRef.current = false;
    },
    onScroll() {
      const el = (scrollRef == null ? void 0 : scrollRef.current) || elements.floating;
      if (!overflowRef.current || !el || !controlledScrollingRef.current) {
        return;
      }
      if (prevScrollTopRef.current !== null) {
        const scrollDiff = el.scrollTop - prevScrollTopRef.current;
        if (overflowRef.current.bottom < -0.5 && scrollDiff < -1 || overflowRef.current.top < -0.5 && scrollDiff > 1) {
          react_dom__WEBPACK_IMPORTED_MODULE_5__.flushSync(() => onChange(d => d + scrollDiff));
        }
      }

      // [Firefox] Wait for the height change to have been applied.
      requestAnimationFrame(() => {
        prevScrollTopRef.current = el.scrollTop;
      });
    }
  }), [elements.floating, onChange, overflowRef, scrollRef]);
  return react__WEBPACK_IMPORTED_MODULE_0__.useMemo(() => enabled ? {
    floating
  } : {}, [enabled, floating]);
}

function getNodeChildren(nodes, id, onlyOpenChildren) {
  if (onlyOpenChildren === void 0) {
    onlyOpenChildren = true;
  }
  const directChildren = nodes.filter(node => {
    var _node$context;
    return node.parentId === id && (!onlyOpenChildren || ((_node$context = node.context) == null ? void 0 : _node$context.open));
  });
  return directChildren.flatMap(child => [child, ...getNodeChildren(nodes, child.id, onlyOpenChildren)]);
}

function isPointInPolygon(point, polygon) {
  const [x, y] = point;
  let isInside = false;
  const length = polygon.length;
  for (let i = 0, j = length - 1; i < length; j = i++) {
    const [xi, yi] = polygon[i] || [0, 0];
    const [xj, yj] = polygon[j] || [0, 0];
    const intersect = yi >= y !== yj >= y && x <= (xj - xi) * (y - yi) / (yj - yi) + xi;
    if (intersect) {
      isInside = !isInside;
    }
  }
  return isInside;
}
function isInside(point, rect) {
  return point[0] >= rect.x && point[0] <= rect.x + rect.width && point[1] >= rect.y && point[1] <= rect.y + rect.height;
}
/**
 * Generates a safe polygon area that the user can traverse without closing the
 * floating element once leaving the reference element.
 * @see https://floating-ui.com/docs/useHover#safepolygon
 */
function safePolygon(options) {
  if (options === void 0) {
    options = {};
  }
  const {
    buffer = 0.5,
    blockPointerEvents = false,
    requireIntent = true
  } = options;
  const timeoutRef = {
    current: -1
  };
  let hasLanded = false;
  let lastX = null;
  let lastY = null;
  let lastCursorTime = typeof performance !== 'undefined' ? performance.now() : 0;
  function getCursorSpeed(x, y) {
    const currentTime = performance.now();
    const elapsedTime = currentTime - lastCursorTime;
    if (lastX === null || lastY === null || elapsedTime === 0) {
      lastX = x;
      lastY = y;
      lastCursorTime = currentTime;
      return null;
    }
    const deltaX = x - lastX;
    const deltaY = y - lastY;
    const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
    const speed = distance / elapsedTime; // px / ms

    lastX = x;
    lastY = y;
    lastCursorTime = currentTime;
    return speed;
  }
  const fn = _ref => {
    let {
      x,
      y,
      placement,
      elements,
      onClose,
      nodeId,
      tree
    } = _ref;
    return function onMouseMove(event) {
      function close() {
        clearTimeoutIfSet(timeoutRef);
        onClose();
      }
      clearTimeoutIfSet(timeoutRef);
      if (!elements.domReference || !elements.floating || placement == null || x == null || y == null) {
        return;
      }
      const {
        clientX,
        clientY
      } = event;
      const clientPoint = [clientX, clientY];
      const target = getTarget(event);
      const isLeave = event.type === 'mouseleave';
      const isOverFloatingEl = contains(elements.floating, target);
      const isOverReferenceEl = contains(elements.domReference, target);
      const refRect = elements.domReference.getBoundingClientRect();
      const rect = elements.floating.getBoundingClientRect();
      const side = placement.split('-')[0];
      const cursorLeaveFromRight = x > rect.right - rect.width / 2;
      const cursorLeaveFromBottom = y > rect.bottom - rect.height / 2;
      const isOverReferenceRect = isInside(clientPoint, refRect);
      const isFloatingWider = rect.width > refRect.width;
      const isFloatingTaller = rect.height > refRect.height;
      const left = (isFloatingWider ? refRect : rect).left;
      const right = (isFloatingWider ? refRect : rect).right;
      const top = (isFloatingTaller ? refRect : rect).top;
      const bottom = (isFloatingTaller ? refRect : rect).bottom;
      if (isOverFloatingEl) {
        hasLanded = true;
        if (!isLeave) {
          return;
        }
      }
      if (isOverReferenceEl) {
        hasLanded = false;
      }
      if (isOverReferenceEl && !isLeave) {
        hasLanded = true;
        return;
      }

      // Prevent overlapping floating element from being stuck in an open-close
      // loop: https://github.com/floating-ui/floating-ui/issues/1910
      if (isLeave && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_3__.isElement)(event.relatedTarget) && contains(elements.floating, event.relatedTarget)) {
        return;
      }

      // If any nested child is open, abort.
      if (tree && getNodeChildren(tree.nodesRef.current, nodeId).length) {
        return;
      }

      // If the pointer is leaving from the opposite side, the "buffer" logic
      // creates a point where the floating element remains open, but should be
      // ignored.
      // A constant of 1 handles floating point rounding errors.
      if (side === 'top' && y >= refRect.bottom - 1 || side === 'bottom' && y <= refRect.top + 1 || side === 'left' && x >= refRect.right - 1 || side === 'right' && x <= refRect.left + 1) {
        return close();
      }

      // Ignore when the cursor is within the rectangular trough between the
      // two elements. Since the triangle is created from the cursor point,
      // which can start beyond the ref element's edge, traversing back and
      // forth from the ref to the floating element can cause it to close. This
      // ensures it always remains open in that case.
      let rectPoly = [];
      switch (side) {
        case 'top':
          rectPoly = [[left, refRect.top + 1], [left, rect.bottom - 1], [right, rect.bottom - 1], [right, refRect.top + 1]];
          break;
        case 'bottom':
          rectPoly = [[left, rect.top + 1], [left, refRect.bottom - 1], [right, refRect.bottom - 1], [right, rect.top + 1]];
          break;
        case 'left':
          rectPoly = [[rect.right - 1, bottom], [rect.right - 1, top], [refRect.left + 1, top], [refRect.left + 1, bottom]];
          break;
        case 'right':
          rectPoly = [[refRect.right - 1, bottom], [refRect.right - 1, top], [rect.left + 1, top], [rect.left + 1, bottom]];
          break;
      }
      function getPolygon(_ref2) {
        let [x, y] = _ref2;
        switch (side) {
          case 'top':
            {
              const cursorPointOne = [isFloatingWider ? x + buffer / 2 : cursorLeaveFromRight ? x + buffer * 4 : x - buffer * 4, y + buffer + 1];
              const cursorPointTwo = [isFloatingWider ? x - buffer / 2 : cursorLeaveFromRight ? x + buffer * 4 : x - buffer * 4, y + buffer + 1];
              const commonPoints = [[rect.left, cursorLeaveFromRight ? rect.bottom - buffer : isFloatingWider ? rect.bottom - buffer : rect.top], [rect.right, cursorLeaveFromRight ? isFloatingWider ? rect.bottom - buffer : rect.top : rect.bottom - buffer]];
              return [cursorPointOne, cursorPointTwo, ...commonPoints];
            }
          case 'bottom':
            {
              const cursorPointOne = [isFloatingWider ? x + buffer / 2 : cursorLeaveFromRight ? x + buffer * 4 : x - buffer * 4, y - buffer];
              const cursorPointTwo = [isFloatingWider ? x - buffer / 2 : cursorLeaveFromRight ? x + buffer * 4 : x - buffer * 4, y - buffer];
              const commonPoints = [[rect.left, cursorLeaveFromRight ? rect.top + buffer : isFloatingWider ? rect.top + buffer : rect.bottom], [rect.right, cursorLeaveFromRight ? isFloatingWider ? rect.top + buffer : rect.bottom : rect.top + buffer]];
              return [cursorPointOne, cursorPointTwo, ...commonPoints];
            }
          case 'left':
            {
              const cursorPointOne = [x + buffer + 1, isFloatingTaller ? y + buffer / 2 : cursorLeaveFromBottom ? y + buffer * 4 : y - buffer * 4];
              const cursorPointTwo = [x + buffer + 1, isFloatingTaller ? y - buffer / 2 : cursorLeaveFromBottom ? y + buffer * 4 : y - buffer * 4];
              const commonPoints = [[cursorLeaveFromBottom ? rect.right - buffer : isFloatingTaller ? rect.right - buffer : rect.left, rect.top], [cursorLeaveFromBottom ? isFloatingTaller ? rect.right - buffer : rect.left : rect.right - buffer, rect.bottom]];
              return [...commonPoints, cursorPointOne, cursorPointTwo];
            }
          case 'right':
            {
              const cursorPointOne = [x - buffer, isFloatingTaller ? y + buffer / 2 : cursorLeaveFromBottom ? y + buffer * 4 : y - buffer * 4];
              const cursorPointTwo = [x - buffer, isFloatingTaller ? y - buffer / 2 : cursorLeaveFromBottom ? y + buffer * 4 : y - buffer * 4];
              const commonPoints = [[cursorLeaveFromBottom ? rect.left + buffer : isFloatingTaller ? rect.left + buffer : rect.right, rect.top], [cursorLeaveFromBottom ? isFloatingTaller ? rect.left + buffer : rect.right : rect.left + buffer, rect.bottom]];
              return [cursorPointOne, cursorPointTwo, ...commonPoints];
            }
        }
      }
      if (isPointInPolygon([clientX, clientY], rectPoly)) {
        return;
      }
      if (hasLanded && !isOverReferenceRect) {
        return close();
      }
      if (!isLeave && requireIntent) {
        const cursorSpeed = getCursorSpeed(event.clientX, event.clientY);
        const cursorSpeedThreshold = 0.1;
        if (cursorSpeed !== null && cursorSpeed < cursorSpeedThreshold) {
          return close();
        }
      }
      if (!isPointInPolygon([clientX, clientY], getPolygon([x, y]))) {
        close();
      } else if (!hasLanded && requireIntent) {
        timeoutRef.current = window.setTimeout(close, 40);
      }
    };
  };
  fn.__options = {
    blockPointerEvents
  };
  return fn;
}




/***/ }),

/***/ "./node_modules/@floating-ui/react/dist/floating-ui.react.utils.mjs":
/*!**************************************************************************!*\
  !*** ./node_modules/@floating-ui/react/dist/floating-ui.react.utils.mjs ***!
  \**************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

var react__WEBPACK_IMPORTED_MODULE_1___namespace_cache;
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   activeElement: function() { return /* binding */ activeElement; },
/* harmony export */   contains: function() { return /* binding */ contains; },
/* harmony export */   createGridCellMap: function() { return /* binding */ createGridCellMap; },
/* harmony export */   disableFocusInside: function() { return /* binding */ disableFocusInside; },
/* harmony export */   enableFocusInside: function() { return /* binding */ enableFocusInside; },
/* harmony export */   findNonDisabledListIndex: function() { return /* binding */ findNonDisabledListIndex; },
/* harmony export */   getDeepestNode: function() { return /* binding */ getDeepestNode; },
/* harmony export */   getDocument: function() { return /* binding */ getDocument; },
/* harmony export */   getFloatingFocusElement: function() { return /* binding */ getFloatingFocusElement; },
/* harmony export */   getGridCellIndexOfCorner: function() { return /* binding */ getGridCellIndexOfCorner; },
/* harmony export */   getGridCellIndices: function() { return /* binding */ getGridCellIndices; },
/* harmony export */   getGridNavigatedIndex: function() { return /* binding */ getGridNavigatedIndex; },
/* harmony export */   getMaxListIndex: function() { return /* binding */ getMaxListIndex; },
/* harmony export */   getMinListIndex: function() { return /* binding */ getMinListIndex; },
/* harmony export */   getNextTabbable: function() { return /* binding */ getNextTabbable; },
/* harmony export */   getNodeAncestors: function() { return /* binding */ getNodeAncestors; },
/* harmony export */   getNodeChildren: function() { return /* binding */ getNodeChildren; },
/* harmony export */   getPlatform: function() { return /* binding */ getPlatform; },
/* harmony export */   getPreviousTabbable: function() { return /* binding */ getPreviousTabbable; },
/* harmony export */   getTabbableOptions: function() { return /* binding */ getTabbableOptions; },
/* harmony export */   getTarget: function() { return /* binding */ getTarget; },
/* harmony export */   getUserAgent: function() { return /* binding */ getUserAgent; },
/* harmony export */   isAndroid: function() { return /* binding */ isAndroid; },
/* harmony export */   isDifferentGridRow: function() { return /* binding */ isDifferentGridRow; },
/* harmony export */   isEventTargetWithin: function() { return /* binding */ isEventTargetWithin; },
/* harmony export */   isIndexOutOfListBounds: function() { return /* binding */ isIndexOutOfListBounds; },
/* harmony export */   isJSDOM: function() { return /* binding */ isJSDOM; },
/* harmony export */   isListIndexDisabled: function() { return /* binding */ isListIndexDisabled; },
/* harmony export */   isMac: function() { return /* binding */ isMac; },
/* harmony export */   isMouseLikePointerType: function() { return /* binding */ isMouseLikePointerType; },
/* harmony export */   isOutsideEvent: function() { return /* binding */ isOutsideEvent; },
/* harmony export */   isReactEvent: function() { return /* binding */ isReactEvent; },
/* harmony export */   isRootElement: function() { return /* binding */ isRootElement; },
/* harmony export */   isSafari: function() { return /* binding */ isSafari; },
/* harmony export */   isTypeableCombobox: function() { return /* binding */ isTypeableCombobox; },
/* harmony export */   isTypeableElement: function() { return /* binding */ isTypeableElement; },
/* harmony export */   isVirtualClick: function() { return /* binding */ isVirtualClick; },
/* harmony export */   isVirtualPointerEvent: function() { return /* binding */ isVirtualPointerEvent; },
/* harmony export */   matchesFocusVisible: function() { return /* binding */ matchesFocusVisible; },
/* harmony export */   stopEvent: function() { return /* binding */ stopEvent; },
/* harmony export */   useEffectEvent: function() { return /* binding */ useEffectEvent; },
/* harmony export */   useLatestRef: function() { return /* binding */ useLatestRef; },
/* harmony export */   useModernLayoutEffect: function() { return /* binding */ index; }
/* harmony export */ });
/* harmony import */ var _floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @floating-ui/utils/dom */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var _floating_ui_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @floating-ui/utils */ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs");
/* harmony import */ var tabbable__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tabbable */ "./node_modules/tabbable/dist/index.esm.js");






// Avoid Chrome DevTools blue warning.
function getPlatform() {
  const uaData = navigator.userAgentData;
  if (uaData != null && uaData.platform) {
    return uaData.platform;
  }
  return navigator.platform;
}
function getUserAgent() {
  const uaData = navigator.userAgentData;
  if (uaData && Array.isArray(uaData.brands)) {
    return uaData.brands.map(_ref => {
      let {
        brand,
        version
      } = _ref;
      return brand + "/" + version;
    }).join(' ');
  }
  return navigator.userAgent;
}
function isSafari() {
  // Chrome DevTools does not complain about navigator.vendor
  return /apple/i.test(navigator.vendor);
}
function isAndroid() {
  const re = /android/i;
  return re.test(getPlatform()) || re.test(getUserAgent());
}
function isMac() {
  return getPlatform().toLowerCase().startsWith('mac') && !navigator.maxTouchPoints;
}
function isJSDOM() {
  return getUserAgent().includes('jsdom/');
}

const FOCUSABLE_ATTRIBUTE = 'data-floating-ui-focusable';
const TYPEABLE_SELECTOR = "input:not([type='hidden']):not([disabled])," + "[contenteditable]:not([contenteditable='false']),textarea:not([disabled])";
const ARROW_LEFT = 'ArrowLeft';
const ARROW_RIGHT = 'ArrowRight';
const ARROW_UP = 'ArrowUp';
const ARROW_DOWN = 'ArrowDown';

function activeElement(doc) {
  let activeElement = doc.activeElement;
  while (((_activeElement = activeElement) == null || (_activeElement = _activeElement.shadowRoot) == null ? void 0 : _activeElement.activeElement) != null) {
    var _activeElement;
    activeElement = activeElement.shadowRoot.activeElement;
  }
  return activeElement;
}
function contains(parent, child) {
  if (!parent || !child) {
    return false;
  }
  const rootNode = child.getRootNode == null ? void 0 : child.getRootNode();

  // First, attempt with faster native method
  if (parent.contains(child)) {
    return true;
  }

  // then fallback to custom implementation with Shadow DOM support
  if (rootNode && (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_0__.isShadowRoot)(rootNode)) {
    let next = child;
    while (next) {
      if (parent === next) {
        return true;
      }
      // @ts-ignore
      next = next.parentNode || next.host;
    }
  }

  // Give up, the result is false
  return false;
}
function getTarget(event) {
  if ('composedPath' in event) {
    return event.composedPath()[0];
  }

  // TS thinks `event` is of type never as it assumes all browsers support
  // `composedPath()`, but browsers without shadow DOM don't.
  return event.target;
}
function isEventTargetWithin(event, node) {
  if (node == null) {
    return false;
  }
  if ('composedPath' in event) {
    return event.composedPath().includes(node);
  }

  // TS thinks `event` is of type never as it assumes all browsers support composedPath, but browsers without shadow dom don't
  const e = event;
  return e.target != null && node.contains(e.target);
}
function isRootElement(element) {
  return element.matches('html,body');
}
function getDocument(node) {
  return (node == null ? void 0 : node.ownerDocument) || document;
}
function isTypeableElement(element) {
  return (0,_floating_ui_utils_dom__WEBPACK_IMPORTED_MODULE_0__.isHTMLElement)(element) && element.matches(TYPEABLE_SELECTOR);
}
function isTypeableCombobox(element) {
  if (!element) return false;
  return element.getAttribute('role') === 'combobox' && isTypeableElement(element);
}
function matchesFocusVisible(element) {
  // We don't want to block focus from working with `visibleOnly`
  // (JSDOM doesn't match `:focus-visible` when the element has `:focus`)
  if (!element || isJSDOM()) return true;
  try {
    return element.matches(':focus-visible');
  } catch (_e) {
    return true;
  }
}
function getFloatingFocusElement(floatingElement) {
  if (!floatingElement) {
    return null;
  }
  // Try to find the element that has `{...getFloatingProps()}` spread on it.
  // This indicates the floating element is acting as a positioning wrapper, and
  // so focus should be managed on the child element with the event handlers and
  // aria props.
  return floatingElement.hasAttribute(FOCUSABLE_ATTRIBUTE) ? floatingElement : floatingElement.querySelector("[" + FOCUSABLE_ATTRIBUTE + "]") || floatingElement;
}

function getNodeChildren(nodes, id, onlyOpenChildren) {
  if (onlyOpenChildren === void 0) {
    onlyOpenChildren = true;
  }
  const directChildren = nodes.filter(node => {
    var _node$context;
    return node.parentId === id && (!onlyOpenChildren || ((_node$context = node.context) == null ? void 0 : _node$context.open));
  });
  return directChildren.flatMap(child => [child, ...getNodeChildren(nodes, child.id, onlyOpenChildren)]);
}
function getDeepestNode(nodes, id) {
  let deepestNodeId;
  let maxDepth = -1;
  function findDeepest(nodeId, depth) {
    if (depth > maxDepth) {
      deepestNodeId = nodeId;
      maxDepth = depth;
    }
    const children = getNodeChildren(nodes, nodeId);
    children.forEach(child => {
      findDeepest(child.id, depth + 1);
    });
  }
  findDeepest(id, 0);
  return nodes.find(node => node.id === deepestNodeId);
}
function getNodeAncestors(nodes, id) {
  var _nodes$find;
  let allAncestors = [];
  let currentParentId = (_nodes$find = nodes.find(node => node.id === id)) == null ? void 0 : _nodes$find.parentId;
  while (currentParentId) {
    const currentNode = nodes.find(node => node.id === currentParentId);
    currentParentId = currentNode == null ? void 0 : currentNode.parentId;
    if (currentNode) {
      allAncestors = allAncestors.concat(currentNode);
    }
  }
  return allAncestors;
}

function stopEvent(event) {
  event.preventDefault();
  event.stopPropagation();
}
function isReactEvent(event) {
  return 'nativeEvent' in event;
}

// License: https://github.com/adobe/react-spectrum/blob/b35d5c02fe900badccd0cf1a8f23bb593419f238/packages/@react-aria/utils/src/isVirtualEvent.ts
function isVirtualClick(event) {
  // FIXME: Firefox is now emitting a deprecation warning for `mozInputSource`.
  // Try to find a workaround for this. `react-aria` source still has the check.
  if (event.mozInputSource === 0 && event.isTrusted) {
    return true;
  }
  if (isAndroid() && event.pointerType) {
    return event.type === 'click' && event.buttons === 1;
  }
  return event.detail === 0 && !event.pointerType;
}
function isVirtualPointerEvent(event) {
  if (isJSDOM()) return false;
  return !isAndroid() && event.width === 0 && event.height === 0 || isAndroid() && event.width === 1 && event.height === 1 && event.pressure === 0 && event.detail === 0 && event.pointerType === 'mouse' ||
  // iOS VoiceOver returns 0.333• for width/height.
  event.width < 1 && event.height < 1 && event.pressure === 0 && event.detail === 0 && event.pointerType === 'touch';
}
function isMouseLikePointerType(pointerType, strict) {
  // On some Linux machines with Chromium, mouse inputs return a `pointerType`
  // of "pen": https://github.com/floating-ui/floating-ui/issues/2015
  const values = ['mouse', 'pen'];
  if (!strict) {
    values.push('', undefined);
  }
  return values.includes(pointerType);
}

var isClient = typeof document !== 'undefined';

var noop = function noop() {};
var index = isClient ? react__WEBPACK_IMPORTED_MODULE_1__.useLayoutEffect : noop;

// https://github.com/mui/material-ui/issues/41190#issuecomment-2040873379
const SafeReact = {
  .../*#__PURE__*/ (react__WEBPACK_IMPORTED_MODULE_1___namespace_cache || (react__WEBPACK_IMPORTED_MODULE_1___namespace_cache = __webpack_require__.t(react__WEBPACK_IMPORTED_MODULE_1__, 2)))
};

function useLatestRef(value) {
  const ref = react__WEBPACK_IMPORTED_MODULE_1__.useRef(value);
  index(() => {
    ref.current = value;
  });
  return ref;
}
const useInsertionEffect = SafeReact.useInsertionEffect;
const useSafeInsertionEffect = useInsertionEffect || (fn => fn());
function useEffectEvent(callback) {
  const ref = react__WEBPACK_IMPORTED_MODULE_1__.useRef(() => {
    if (true) {
      throw new Error('Cannot call an event handler while rendering.');
    }
  });
  useSafeInsertionEffect(() => {
    ref.current = callback;
  });
  return react__WEBPACK_IMPORTED_MODULE_1__.useCallback(function () {
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    return ref.current == null ? void 0 : ref.current(...args);
  }, []);
}

function isDifferentGridRow(index, cols, prevRow) {
  return Math.floor(index / cols) !== prevRow;
}
function isIndexOutOfListBounds(listRef, index) {
  return index < 0 || index >= listRef.current.length;
}
function getMinListIndex(listRef, disabledIndices) {
  return findNonDisabledListIndex(listRef, {
    disabledIndices
  });
}
function getMaxListIndex(listRef, disabledIndices) {
  return findNonDisabledListIndex(listRef, {
    decrement: true,
    startingIndex: listRef.current.length,
    disabledIndices
  });
}
function findNonDisabledListIndex(listRef, _temp) {
  let {
    startingIndex = -1,
    decrement = false,
    disabledIndices,
    amount = 1
  } = _temp === void 0 ? {} : _temp;
  let index = startingIndex;
  do {
    index += decrement ? -amount : amount;
  } while (index >= 0 && index <= listRef.current.length - 1 && isListIndexDisabled(listRef, index, disabledIndices));
  return index;
}
function getGridNavigatedIndex(listRef, _ref) {
  let {
    event,
    orientation,
    loop,
    rtl,
    cols,
    disabledIndices,
    minIndex,
    maxIndex,
    prevIndex,
    stopEvent: stop = false
  } = _ref;
  let nextIndex = prevIndex;
  if (event.key === ARROW_UP) {
    stop && stopEvent(event);
    if (prevIndex === -1) {
      nextIndex = maxIndex;
    } else {
      nextIndex = findNonDisabledListIndex(listRef, {
        startingIndex: nextIndex,
        amount: cols,
        decrement: true,
        disabledIndices
      });
      if (loop && (prevIndex - cols < minIndex || nextIndex < 0)) {
        const col = prevIndex % cols;
        const maxCol = maxIndex % cols;
        const offset = maxIndex - (maxCol - col);
        if (maxCol === col) {
          nextIndex = maxIndex;
        } else {
          nextIndex = maxCol > col ? offset : offset - cols;
        }
      }
    }
    if (isIndexOutOfListBounds(listRef, nextIndex)) {
      nextIndex = prevIndex;
    }
  }
  if (event.key === ARROW_DOWN) {
    stop && stopEvent(event);
    if (prevIndex === -1) {
      nextIndex = minIndex;
    } else {
      nextIndex = findNonDisabledListIndex(listRef, {
        startingIndex: prevIndex,
        amount: cols,
        disabledIndices
      });
      if (loop && prevIndex + cols > maxIndex) {
        nextIndex = findNonDisabledListIndex(listRef, {
          startingIndex: prevIndex % cols - cols,
          amount: cols,
          disabledIndices
        });
      }
    }
    if (isIndexOutOfListBounds(listRef, nextIndex)) {
      nextIndex = prevIndex;
    }
  }

  // Remains on the same row/column.
  if (orientation === 'both') {
    const prevRow = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_2__.floor)(prevIndex / cols);
    if (event.key === (rtl ? ARROW_LEFT : ARROW_RIGHT)) {
      stop && stopEvent(event);
      if (prevIndex % cols !== cols - 1) {
        nextIndex = findNonDisabledListIndex(listRef, {
          startingIndex: prevIndex,
          disabledIndices
        });
        if (loop && isDifferentGridRow(nextIndex, cols, prevRow)) {
          nextIndex = findNonDisabledListIndex(listRef, {
            startingIndex: prevIndex - prevIndex % cols - 1,
            disabledIndices
          });
        }
      } else if (loop) {
        nextIndex = findNonDisabledListIndex(listRef, {
          startingIndex: prevIndex - prevIndex % cols - 1,
          disabledIndices
        });
      }
      if (isDifferentGridRow(nextIndex, cols, prevRow)) {
        nextIndex = prevIndex;
      }
    }
    if (event.key === (rtl ? ARROW_RIGHT : ARROW_LEFT)) {
      stop && stopEvent(event);
      if (prevIndex % cols !== 0) {
        nextIndex = findNonDisabledListIndex(listRef, {
          startingIndex: prevIndex,
          decrement: true,
          disabledIndices
        });
        if (loop && isDifferentGridRow(nextIndex, cols, prevRow)) {
          nextIndex = findNonDisabledListIndex(listRef, {
            startingIndex: prevIndex + (cols - prevIndex % cols),
            decrement: true,
            disabledIndices
          });
        }
      } else if (loop) {
        nextIndex = findNonDisabledListIndex(listRef, {
          startingIndex: prevIndex + (cols - prevIndex % cols),
          decrement: true,
          disabledIndices
        });
      }
      if (isDifferentGridRow(nextIndex, cols, prevRow)) {
        nextIndex = prevIndex;
      }
    }
    const lastRow = (0,_floating_ui_utils__WEBPACK_IMPORTED_MODULE_2__.floor)(maxIndex / cols) === prevRow;
    if (isIndexOutOfListBounds(listRef, nextIndex)) {
      if (loop && lastRow) {
        nextIndex = event.key === (rtl ? ARROW_RIGHT : ARROW_LEFT) ? maxIndex : findNonDisabledListIndex(listRef, {
          startingIndex: prevIndex - prevIndex % cols - 1,
          disabledIndices
        });
      } else {
        nextIndex = prevIndex;
      }
    }
  }
  return nextIndex;
}

/** For each cell index, gets the item index that occupies that cell */
function createGridCellMap(sizes, cols, dense) {
  const cellMap = [];
  let startIndex = 0;
  sizes.forEach((_ref2, index) => {
    let {
      width,
      height
    } = _ref2;
    if (width > cols) {
      if (true) {
        throw new Error("[Floating UI]: Invalid grid - item width at index " + index + " is greater than grid columns");
      }
    }
    let itemPlaced = false;
    if (dense) {
      startIndex = 0;
    }
    while (!itemPlaced) {
      const targetCells = [];
      for (let i = 0; i < width; i++) {
        for (let j = 0; j < height; j++) {
          targetCells.push(startIndex + i + j * cols);
        }
      }
      if (startIndex % cols + width <= cols && targetCells.every(cell => cellMap[cell] == null)) {
        targetCells.forEach(cell => {
          cellMap[cell] = index;
        });
        itemPlaced = true;
      } else {
        startIndex++;
      }
    }
  });

  // convert into a non-sparse array
  return [...cellMap];
}

/** Gets cell index of an item's corner or -1 when index is -1. */
function getGridCellIndexOfCorner(index, sizes, cellMap, cols, corner) {
  if (index === -1) return -1;
  const firstCellIndex = cellMap.indexOf(index);
  const sizeItem = sizes[index];
  switch (corner) {
    case 'tl':
      return firstCellIndex;
    case 'tr':
      if (!sizeItem) {
        return firstCellIndex;
      }
      return firstCellIndex + sizeItem.width - 1;
    case 'bl':
      if (!sizeItem) {
        return firstCellIndex;
      }
      return firstCellIndex + (sizeItem.height - 1) * cols;
    case 'br':
      return cellMap.lastIndexOf(index);
  }
}

/** Gets all cell indices that correspond to the specified indices */
function getGridCellIndices(indices, cellMap) {
  return cellMap.flatMap((index, cellIndex) => indices.includes(index) ? [cellIndex] : []);
}
function isListIndexDisabled(listRef, index, disabledIndices) {
  if (typeof disabledIndices === 'function') {
    return disabledIndices(index);
  } else if (disabledIndices) {
    return disabledIndices.includes(index);
  }
  const element = listRef.current[index];
  return element == null || element.hasAttribute('disabled') || element.getAttribute('aria-disabled') === 'true';
}

const getTabbableOptions = () => ({
  getShadowRoot: true,
  displayCheck:
  // JSDOM does not support the `tabbable` library. To solve this we can
  // check if `ResizeObserver` is a real function (not polyfilled), which
  // determines if the current environment is JSDOM-like.
  typeof ResizeObserver === 'function' && ResizeObserver.toString().includes('[native code]') ? 'full' : 'none'
});
function getTabbableIn(container, dir) {
  const list = (0,tabbable__WEBPACK_IMPORTED_MODULE_3__.tabbable)(container, getTabbableOptions());
  const len = list.length;
  if (len === 0) return;
  const active = activeElement(getDocument(container));
  const index = list.indexOf(active);
  const nextIndex = index === -1 ? dir === 1 ? 0 : len - 1 : index + dir;
  return list[nextIndex];
}
function getNextTabbable(referenceElement) {
  return getTabbableIn(getDocument(referenceElement).body, 1) || referenceElement;
}
function getPreviousTabbable(referenceElement) {
  return getTabbableIn(getDocument(referenceElement).body, -1) || referenceElement;
}
function isOutsideEvent(event, container) {
  const containerElement = container || event.currentTarget;
  const relatedTarget = event.relatedTarget;
  return !relatedTarget || !contains(containerElement, relatedTarget);
}
function disableFocusInside(container) {
  const tabbableElements = (0,tabbable__WEBPACK_IMPORTED_MODULE_3__.tabbable)(container, getTabbableOptions());
  tabbableElements.forEach(element => {
    element.dataset.tabindex = element.getAttribute('tabindex') || '';
    element.setAttribute('tabindex', '-1');
  });
}
function enableFocusInside(container) {
  const elements = container.querySelectorAll('[data-tabindex]');
  elements.forEach(element => {
    const tabindex = element.dataset.tabindex;
    delete element.dataset.tabindex;
    if (tabindex) {
      element.setAttribute('tabindex', tabindex);
    } else {
      element.removeAttribute('tabindex');
    }
  });
}




/***/ }),

/***/ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs":
/*!************************************************************************!*\
  !*** ./node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs ***!
  \************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getComputedStyle: function() { return /* binding */ getComputedStyle; },
/* harmony export */   getContainingBlock: function() { return /* binding */ getContainingBlock; },
/* harmony export */   getDocumentElement: function() { return /* binding */ getDocumentElement; },
/* harmony export */   getFrameElement: function() { return /* binding */ getFrameElement; },
/* harmony export */   getNearestOverflowAncestor: function() { return /* binding */ getNearestOverflowAncestor; },
/* harmony export */   getNodeName: function() { return /* binding */ getNodeName; },
/* harmony export */   getNodeScroll: function() { return /* binding */ getNodeScroll; },
/* harmony export */   getOverflowAncestors: function() { return /* binding */ getOverflowAncestors; },
/* harmony export */   getParentNode: function() { return /* binding */ getParentNode; },
/* harmony export */   getWindow: function() { return /* binding */ getWindow; },
/* harmony export */   isContainingBlock: function() { return /* binding */ isContainingBlock; },
/* harmony export */   isElement: function() { return /* binding */ isElement; },
/* harmony export */   isHTMLElement: function() { return /* binding */ isHTMLElement; },
/* harmony export */   isLastTraversableNode: function() { return /* binding */ isLastTraversableNode; },
/* harmony export */   isNode: function() { return /* binding */ isNode; },
/* harmony export */   isOverflowElement: function() { return /* binding */ isOverflowElement; },
/* harmony export */   isShadowRoot: function() { return /* binding */ isShadowRoot; },
/* harmony export */   isTableElement: function() { return /* binding */ isTableElement; },
/* harmony export */   isTopLayer: function() { return /* binding */ isTopLayer; },
/* harmony export */   isWebKit: function() { return /* binding */ isWebKit; }
/* harmony export */ });
function hasWindow() {
  return typeof window !== 'undefined';
}
function getNodeName(node) {
  if (isNode(node)) {
    return (node.nodeName || '').toLowerCase();
  }
  // Mocked nodes in testing environments may not be instances of Node. By
  // returning `#document` an infinite loop won't occur.
  // https://github.com/floating-ui/floating-ui/issues/2317
  return '#document';
}
function getWindow(node) {
  var _node$ownerDocument;
  return (node == null || (_node$ownerDocument = node.ownerDocument) == null ? void 0 : _node$ownerDocument.defaultView) || window;
}
function getDocumentElement(node) {
  var _ref;
  return (_ref = (isNode(node) ? node.ownerDocument : node.document) || window.document) == null ? void 0 : _ref.documentElement;
}
function isNode(value) {
  if (!hasWindow()) {
    return false;
  }
  return value instanceof Node || value instanceof getWindow(value).Node;
}
function isElement(value) {
  if (!hasWindow()) {
    return false;
  }
  return value instanceof Element || value instanceof getWindow(value).Element;
}
function isHTMLElement(value) {
  if (!hasWindow()) {
    return false;
  }
  return value instanceof HTMLElement || value instanceof getWindow(value).HTMLElement;
}
function isShadowRoot(value) {
  if (!hasWindow() || typeof ShadowRoot === 'undefined') {
    return false;
  }
  return value instanceof ShadowRoot || value instanceof getWindow(value).ShadowRoot;
}
function isOverflowElement(element) {
  const {
    overflow,
    overflowX,
    overflowY,
    display
  } = getComputedStyle(element);
  return /auto|scroll|overlay|hidden|clip/.test(overflow + overflowY + overflowX) && display !== 'inline' && display !== 'contents';
}
function isTableElement(element) {
  return /^(table|td|th)$/.test(getNodeName(element));
}
function isTopLayer(element) {
  try {
    if (element.matches(':popover-open')) {
      return true;
    }
  } catch (_e) {
    // no-op
  }
  try {
    return element.matches(':modal');
  } catch (_e) {
    return false;
  }
}
const willChangeRe = /transform|translate|scale|rotate|perspective|filter/;
const containRe = /paint|layout|strict|content/;
const isNotNone = value => !!value && value !== 'none';
let isWebKitValue;
function isContainingBlock(elementOrCss) {
  const css = isElement(elementOrCss) ? getComputedStyle(elementOrCss) : elementOrCss;

  // https://developer.mozilla.org/en-US/docs/Web/CSS/Containing_block#identifying_the_containing_block
  // https://drafts.csswg.org/css-transforms-2/#individual-transforms
  return isNotNone(css.transform) || isNotNone(css.translate) || isNotNone(css.scale) || isNotNone(css.rotate) || isNotNone(css.perspective) || !isWebKit() && (isNotNone(css.backdropFilter) || isNotNone(css.filter)) || willChangeRe.test(css.willChange || '') || containRe.test(css.contain || '');
}
function getContainingBlock(element) {
  let currentNode = getParentNode(element);
  while (isHTMLElement(currentNode) && !isLastTraversableNode(currentNode)) {
    if (isContainingBlock(currentNode)) {
      return currentNode;
    } else if (isTopLayer(currentNode)) {
      return null;
    }
    currentNode = getParentNode(currentNode);
  }
  return null;
}
function isWebKit() {
  if (isWebKitValue == null) {
    isWebKitValue = typeof CSS !== 'undefined' && CSS.supports && CSS.supports('-webkit-backdrop-filter', 'none');
  }
  return isWebKitValue;
}
function isLastTraversableNode(node) {
  return /^(html|body|#document)$/.test(getNodeName(node));
}
function getComputedStyle(element) {
  return getWindow(element).getComputedStyle(element);
}
function getNodeScroll(element) {
  if (isElement(element)) {
    return {
      scrollLeft: element.scrollLeft,
      scrollTop: element.scrollTop
    };
  }
  return {
    scrollLeft: element.scrollX,
    scrollTop: element.scrollY
  };
}
function getParentNode(node) {
  if (getNodeName(node) === 'html') {
    return node;
  }
  const result =
  // Step into the shadow DOM of the parent of a slotted node.
  node.assignedSlot ||
  // DOM Element detected.
  node.parentNode ||
  // ShadowRoot detected.
  isShadowRoot(node) && node.host ||
  // Fallback.
  getDocumentElement(node);
  return isShadowRoot(result) ? result.host : result;
}
function getNearestOverflowAncestor(node) {
  const parentNode = getParentNode(node);
  if (isLastTraversableNode(parentNode)) {
    return node.ownerDocument ? node.ownerDocument.body : node.body;
  }
  if (isHTMLElement(parentNode) && isOverflowElement(parentNode)) {
    return parentNode;
  }
  return getNearestOverflowAncestor(parentNode);
}
function getOverflowAncestors(node, list, traverseIframes) {
  var _node$ownerDocument2;
  if (list === void 0) {
    list = [];
  }
  if (traverseIframes === void 0) {
    traverseIframes = true;
  }
  const scrollableAncestor = getNearestOverflowAncestor(node);
  const isBody = scrollableAncestor === ((_node$ownerDocument2 = node.ownerDocument) == null ? void 0 : _node$ownerDocument2.body);
  const win = getWindow(scrollableAncestor);
  if (isBody) {
    const frameElement = getFrameElement(win);
    return list.concat(win, win.visualViewport || [], isOverflowElement(scrollableAncestor) ? scrollableAncestor : [], frameElement && traverseIframes ? getOverflowAncestors(frameElement) : []);
  } else {
    return list.concat(scrollableAncestor, getOverflowAncestors(scrollableAncestor, [], traverseIframes));
  }
}
function getFrameElement(win) {
  return win.parent && Object.getPrototypeOf(win.parent) ? win.frameElement : null;
}




/***/ }),

/***/ "./node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs":
/*!********************************************************************!*\
  !*** ./node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs ***!
  \********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   alignments: function() { return /* binding */ alignments; },
/* harmony export */   clamp: function() { return /* binding */ clamp; },
/* harmony export */   createCoords: function() { return /* binding */ createCoords; },
/* harmony export */   evaluate: function() { return /* binding */ evaluate; },
/* harmony export */   expandPaddingObject: function() { return /* binding */ expandPaddingObject; },
/* harmony export */   floor: function() { return /* binding */ floor; },
/* harmony export */   getAlignment: function() { return /* binding */ getAlignment; },
/* harmony export */   getAlignmentAxis: function() { return /* binding */ getAlignmentAxis; },
/* harmony export */   getAlignmentSides: function() { return /* binding */ getAlignmentSides; },
/* harmony export */   getAxisLength: function() { return /* binding */ getAxisLength; },
/* harmony export */   getExpandedPlacements: function() { return /* binding */ getExpandedPlacements; },
/* harmony export */   getOppositeAlignmentPlacement: function() { return /* binding */ getOppositeAlignmentPlacement; },
/* harmony export */   getOppositeAxis: function() { return /* binding */ getOppositeAxis; },
/* harmony export */   getOppositeAxisPlacements: function() { return /* binding */ getOppositeAxisPlacements; },
/* harmony export */   getOppositePlacement: function() { return /* binding */ getOppositePlacement; },
/* harmony export */   getPaddingObject: function() { return /* binding */ getPaddingObject; },
/* harmony export */   getSide: function() { return /* binding */ getSide; },
/* harmony export */   getSideAxis: function() { return /* binding */ getSideAxis; },
/* harmony export */   max: function() { return /* binding */ max; },
/* harmony export */   min: function() { return /* binding */ min; },
/* harmony export */   placements: function() { return /* binding */ placements; },
/* harmony export */   rectToClientRect: function() { return /* binding */ rectToClientRect; },
/* harmony export */   round: function() { return /* binding */ round; },
/* harmony export */   sides: function() { return /* binding */ sides; }
/* harmony export */ });
/**
 * Custom positioning reference element.
 * @see https://floating-ui.com/docs/virtual-elements
 */

const sides = ['top', 'right', 'bottom', 'left'];
const alignments = ['start', 'end'];
const placements = /*#__PURE__*/sides.reduce((acc, side) => acc.concat(side, side + "-" + alignments[0], side + "-" + alignments[1]), []);
const min = Math.min;
const max = Math.max;
const round = Math.round;
const floor = Math.floor;
const createCoords = v => ({
  x: v,
  y: v
});
const oppositeSideMap = {
  left: 'right',
  right: 'left',
  bottom: 'top',
  top: 'bottom'
};
function clamp(start, value, end) {
  return max(start, min(value, end));
}
function evaluate(value, param) {
  return typeof value === 'function' ? value(param) : value;
}
function getSide(placement) {
  return placement.split('-')[0];
}
function getAlignment(placement) {
  return placement.split('-')[1];
}
function getOppositeAxis(axis) {
  return axis === 'x' ? 'y' : 'x';
}
function getAxisLength(axis) {
  return axis === 'y' ? 'height' : 'width';
}
function getSideAxis(placement) {
  const firstChar = placement[0];
  return firstChar === 't' || firstChar === 'b' ? 'y' : 'x';
}
function getAlignmentAxis(placement) {
  return getOppositeAxis(getSideAxis(placement));
}
function getAlignmentSides(placement, rects, rtl) {
  if (rtl === void 0) {
    rtl = false;
  }
  const alignment = getAlignment(placement);
  const alignmentAxis = getAlignmentAxis(placement);
  const length = getAxisLength(alignmentAxis);
  let mainAlignmentSide = alignmentAxis === 'x' ? alignment === (rtl ? 'end' : 'start') ? 'right' : 'left' : alignment === 'start' ? 'bottom' : 'top';
  if (rects.reference[length] > rects.floating[length]) {
    mainAlignmentSide = getOppositePlacement(mainAlignmentSide);
  }
  return [mainAlignmentSide, getOppositePlacement(mainAlignmentSide)];
}
function getExpandedPlacements(placement) {
  const oppositePlacement = getOppositePlacement(placement);
  return [getOppositeAlignmentPlacement(placement), oppositePlacement, getOppositeAlignmentPlacement(oppositePlacement)];
}
function getOppositeAlignmentPlacement(placement) {
  return placement.includes('start') ? placement.replace('start', 'end') : placement.replace('end', 'start');
}
const lrPlacement = ['left', 'right'];
const rlPlacement = ['right', 'left'];
const tbPlacement = ['top', 'bottom'];
const btPlacement = ['bottom', 'top'];
function getSideList(side, isStart, rtl) {
  switch (side) {
    case 'top':
    case 'bottom':
      if (rtl) return isStart ? rlPlacement : lrPlacement;
      return isStart ? lrPlacement : rlPlacement;
    case 'left':
    case 'right':
      return isStart ? tbPlacement : btPlacement;
    default:
      return [];
  }
}
function getOppositeAxisPlacements(placement, flipAlignment, direction, rtl) {
  const alignment = getAlignment(placement);
  let list = getSideList(getSide(placement), direction === 'start', rtl);
  if (alignment) {
    list = list.map(side => side + "-" + alignment);
    if (flipAlignment) {
      list = list.concat(list.map(getOppositeAlignmentPlacement));
    }
  }
  return list;
}
function getOppositePlacement(placement) {
  const side = getSide(placement);
  return oppositeSideMap[side] + placement.slice(side.length);
}
function expandPaddingObject(padding) {
  return {
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
    ...padding
  };
}
function getPaddingObject(padding) {
  return typeof padding !== 'number' ? expandPaddingObject(padding) : {
    top: padding,
    right: padding,
    bottom: padding,
    left: padding
  };
}
function rectToClientRect(rect) {
  const {
    x,
    y,
    width,
    height
  } = rect;
  return {
    width,
    height,
    top: y,
    left: x,
    right: x + width,
    bottom: y + height,
    x,
    y
  };
}




/***/ }),

/***/ "./node_modules/dompurify/dist/purify.es.mjs":
/*!***************************************************!*\
  !*** ./node_modules/dompurify/dist/purify.es.mjs ***!
  \***************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ purify; }
/* harmony export */ });
/*! @license DOMPurify 3.3.0 | (c) Cure53 and other contributors | Released under the Apache license 2.0 and Mozilla Public License 2.0 | github.com/cure53/DOMPurify/blob/3.3.0/LICENSE */

const {
  entries,
  setPrototypeOf,
  isFrozen,
  getPrototypeOf,
  getOwnPropertyDescriptor
} = Object;
let {
  freeze,
  seal,
  create
} = Object; // eslint-disable-line import/no-mutable-exports
let {
  apply,
  construct
} = typeof Reflect !== 'undefined' && Reflect;
if (!freeze) {
  freeze = function freeze(x) {
    return x;
  };
}
if (!seal) {
  seal = function seal(x) {
    return x;
  };
}
if (!apply) {
  apply = function apply(func, thisArg) {
    for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
      args[_key - 2] = arguments[_key];
    }
    return func.apply(thisArg, args);
  };
}
if (!construct) {
  construct = function construct(Func) {
    for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
      args[_key2 - 1] = arguments[_key2];
    }
    return new Func(...args);
  };
}
const arrayForEach = unapply(Array.prototype.forEach);
const arrayLastIndexOf = unapply(Array.prototype.lastIndexOf);
const arrayPop = unapply(Array.prototype.pop);
const arrayPush = unapply(Array.prototype.push);
const arraySplice = unapply(Array.prototype.splice);
const stringToLowerCase = unapply(String.prototype.toLowerCase);
const stringToString = unapply(String.prototype.toString);
const stringMatch = unapply(String.prototype.match);
const stringReplace = unapply(String.prototype.replace);
const stringIndexOf = unapply(String.prototype.indexOf);
const stringTrim = unapply(String.prototype.trim);
const objectHasOwnProperty = unapply(Object.prototype.hasOwnProperty);
const regExpTest = unapply(RegExp.prototype.test);
const typeErrorCreate = unconstruct(TypeError);
/**
 * Creates a new function that calls the given function with a specified thisArg and arguments.
 *
 * @param func - The function to be wrapped and called.
 * @returns A new function that calls the given function with a specified thisArg and arguments.
 */
function unapply(func) {
  return function (thisArg) {
    if (thisArg instanceof RegExp) {
      thisArg.lastIndex = 0;
    }
    for (var _len3 = arguments.length, args = new Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
      args[_key3 - 1] = arguments[_key3];
    }
    return apply(func, thisArg, args);
  };
}
/**
 * Creates a new function that constructs an instance of the given constructor function with the provided arguments.
 *
 * @param func - The constructor function to be wrapped and called.
 * @returns A new function that constructs an instance of the given constructor function with the provided arguments.
 */
function unconstruct(Func) {
  return function () {
    for (var _len4 = arguments.length, args = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
      args[_key4] = arguments[_key4];
    }
    return construct(Func, args);
  };
}
/**
 * Add properties to a lookup table
 *
 * @param set - The set to which elements will be added.
 * @param array - The array containing elements to be added to the set.
 * @param transformCaseFunc - An optional function to transform the case of each element before adding to the set.
 * @returns The modified set with added elements.
 */
function addToSet(set, array) {
  let transformCaseFunc = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : stringToLowerCase;
  if (setPrototypeOf) {
    // Make 'in' and truthy checks like Boolean(set.constructor)
    // independent of any properties defined on Object.prototype.
    // Prevent prototype setters from intercepting set as a this value.
    setPrototypeOf(set, null);
  }
  let l = array.length;
  while (l--) {
    let element = array[l];
    if (typeof element === 'string') {
      const lcElement = transformCaseFunc(element);
      if (lcElement !== element) {
        // Config presets (e.g. tags.js, attrs.js) are immutable.
        if (!isFrozen(array)) {
          array[l] = lcElement;
        }
        element = lcElement;
      }
    }
    set[element] = true;
  }
  return set;
}
/**
 * Clean up an array to harden against CSPP
 *
 * @param array - The array to be cleaned.
 * @returns The cleaned version of the array
 */
function cleanArray(array) {
  for (let index = 0; index < array.length; index++) {
    const isPropertyExist = objectHasOwnProperty(array, index);
    if (!isPropertyExist) {
      array[index] = null;
    }
  }
  return array;
}
/**
 * Shallow clone an object
 *
 * @param object - The object to be cloned.
 * @returns A new object that copies the original.
 */
function clone(object) {
  const newObject = create(null);
  for (const [property, value] of entries(object)) {
    const isPropertyExist = objectHasOwnProperty(object, property);
    if (isPropertyExist) {
      if (Array.isArray(value)) {
        newObject[property] = cleanArray(value);
      } else if (value && typeof value === 'object' && value.constructor === Object) {
        newObject[property] = clone(value);
      } else {
        newObject[property] = value;
      }
    }
  }
  return newObject;
}
/**
 * This method automatically checks if the prop is function or getter and behaves accordingly.
 *
 * @param object - The object to look up the getter function in its prototype chain.
 * @param prop - The property name for which to find the getter function.
 * @returns The getter function found in the prototype chain or a fallback function.
 */
function lookupGetter(object, prop) {
  while (object !== null) {
    const desc = getOwnPropertyDescriptor(object, prop);
    if (desc) {
      if (desc.get) {
        return unapply(desc.get);
      }
      if (typeof desc.value === 'function') {
        return unapply(desc.value);
      }
    }
    object = getPrototypeOf(object);
  }
  function fallbackValue() {
    return null;
  }
  return fallbackValue;
}

const html$1 = freeze(['a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'big', 'blink', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'content', 'data', 'datalist', 'dd', 'decorator', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'element', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meter', 'nav', 'nobr', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'search', 'section', 'select', 'shadow', 'slot', 'small', 'source', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr']);
const svg$1 = freeze(['svg', 'a', 'altglyph', 'altglyphdef', 'altglyphitem', 'animatecolor', 'animatemotion', 'animatetransform', 'circle', 'clippath', 'defs', 'desc', 'ellipse', 'enterkeyhint', 'exportparts', 'filter', 'font', 'g', 'glyph', 'glyphref', 'hkern', 'image', 'inputmode', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'mpath', 'part', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'style', 'switch', 'symbol', 'text', 'textpath', 'title', 'tref', 'tspan', 'view', 'vkern']);
const svgFilters = freeze(['feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix', 'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feDropShadow', 'feFlood', 'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feImage', 'feMerge', 'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting', 'feSpotLight', 'feTile', 'feTurbulence']);
// List of SVG elements that are disallowed by default.
// We still need to know them so that we can do namespace
// checks properly in case one wants to add them to
// allow-list.
const svgDisallowed = freeze(['animate', 'color-profile', 'cursor', 'discard', 'font-face', 'font-face-format', 'font-face-name', 'font-face-src', 'font-face-uri', 'foreignobject', 'hatch', 'hatchpath', 'mesh', 'meshgradient', 'meshpatch', 'meshrow', 'missing-glyph', 'script', 'set', 'solidcolor', 'unknown', 'use']);
const mathMl$1 = freeze(['math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover', 'mprescripts']);
// Similarly to SVG, we want to know all MathML elements,
// even those that we disallow by default.
const mathMlDisallowed = freeze(['maction', 'maligngroup', 'malignmark', 'mlongdiv', 'mscarries', 'mscarry', 'msgroup', 'mstack', 'msline', 'msrow', 'semantics', 'annotation', 'annotation-xml', 'mprescripts', 'none']);
const text = freeze(['#text']);

const html = freeze(['accept', 'action', 'align', 'alt', 'autocapitalize', 'autocomplete', 'autopictureinpicture', 'autoplay', 'background', 'bgcolor', 'border', 'capture', 'cellpadding', 'cellspacing', 'checked', 'cite', 'class', 'clear', 'color', 'cols', 'colspan', 'controls', 'controlslist', 'coords', 'crossorigin', 'datetime', 'decoding', 'default', 'dir', 'disabled', 'disablepictureinpicture', 'disableremoteplayback', 'download', 'draggable', 'enctype', 'enterkeyhint', 'exportparts', 'face', 'for', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'id', 'inert', 'inputmode', 'integrity', 'ismap', 'kind', 'label', 'lang', 'list', 'loading', 'loop', 'low', 'max', 'maxlength', 'media', 'method', 'min', 'minlength', 'multiple', 'muted', 'name', 'nonce', 'noshade', 'novalidate', 'nowrap', 'open', 'optimum', 'part', 'pattern', 'placeholder', 'playsinline', 'popover', 'popovertarget', 'popovertargetaction', 'poster', 'preload', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'rev', 'reversed', 'role', 'rows', 'rowspan', 'spellcheck', 'scope', 'selected', 'shape', 'size', 'sizes', 'slot', 'span', 'srclang', 'start', 'src', 'srcset', 'step', 'style', 'summary', 'tabindex', 'title', 'translate', 'type', 'usemap', 'valign', 'value', 'width', 'wrap', 'xmlns', 'slot']);
const svg = freeze(['accent-height', 'accumulate', 'additive', 'alignment-baseline', 'amplitude', 'ascent', 'attributename', 'attributetype', 'azimuth', 'basefrequency', 'baseline-shift', 'begin', 'bias', 'by', 'class', 'clip', 'clippathunits', 'clip-path', 'clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cx', 'cy', 'd', 'dx', 'dy', 'diffuseconstant', 'direction', 'display', 'divisor', 'dur', 'edgemode', 'elevation', 'end', 'exponent', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'filterunits', 'flood-color', 'flood-opacity', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'fx', 'fy', 'g1', 'g2', 'glyph-name', 'glyphref', 'gradientunits', 'gradienttransform', 'height', 'href', 'id', 'image-rendering', 'in', 'in2', 'intercept', 'k', 'k1', 'k2', 'k3', 'k4', 'kerning', 'keypoints', 'keysplines', 'keytimes', 'lang', 'lengthadjust', 'letter-spacing', 'kernelmatrix', 'kernelunitlength', 'lighting-color', 'local', 'marker-end', 'marker-mid', 'marker-start', 'markerheight', 'markerunits', 'markerwidth', 'maskcontentunits', 'maskunits', 'max', 'mask', 'mask-type', 'media', 'method', 'mode', 'min', 'name', 'numoctaves', 'offset', 'operator', 'opacity', 'order', 'orient', 'orientation', 'origin', 'overflow', 'paint-order', 'path', 'pathlength', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'preservealpha', 'preserveaspectratio', 'primitiveunits', 'r', 'rx', 'ry', 'radius', 'refx', 'refy', 'repeatcount', 'repeatdur', 'restart', 'result', 'rotate', 'scale', 'seed', 'shape-rendering', 'slope', 'specularconstant', 'specularexponent', 'spreadmethod', 'startoffset', 'stddeviation', 'stitchtiles', 'stop-color', 'stop-opacity', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke', 'stroke-width', 'style', 'surfacescale', 'systemlanguage', 'tabindex', 'tablevalues', 'targetx', 'targety', 'transform', 'transform-origin', 'text-anchor', 'text-decoration', 'text-rendering', 'textlength', 'type', 'u1', 'u2', 'unicode', 'values', 'viewbox', 'visibility', 'version', 'vert-adv-y', 'vert-origin-x', 'vert-origin-y', 'width', 'word-spacing', 'wrap', 'writing-mode', 'xchannelselector', 'ychannelselector', 'x', 'x1', 'x2', 'xmlns', 'y', 'y1', 'y2', 'z', 'zoomandpan']);
const mathMl = freeze(['accent', 'accentunder', 'align', 'bevelled', 'close', 'columnsalign', 'columnlines', 'columnspan', 'denomalign', 'depth', 'dir', 'display', 'displaystyle', 'encoding', 'fence', 'frame', 'height', 'href', 'id', 'largeop', 'length', 'linethickness', 'lspace', 'lquote', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'movablelimits', 'notation', 'numalign', 'open', 'rowalign', 'rowlines', 'rowspacing', 'rowspan', 'rspace', 'rquote', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier', 'selection', 'separator', 'separators', 'stretchy', 'subscriptshift', 'supscriptshift', 'symmetric', 'voffset', 'width', 'xmlns']);
const xml = freeze(['xlink:href', 'xml:id', 'xlink:title', 'xml:space', 'xmlns:xlink']);

// eslint-disable-next-line unicorn/better-regex
const MUSTACHE_EXPR = seal(/\{\{[\w\W]*|[\w\W]*\}\}/gm); // Specify template detection regex for SAFE_FOR_TEMPLATES mode
const ERB_EXPR = seal(/<%[\w\W]*|[\w\W]*%>/gm);
const TMPLIT_EXPR = seal(/\$\{[\w\W]*/gm); // eslint-disable-line unicorn/better-regex
const DATA_ATTR = seal(/^data-[\-\w.\u00B7-\uFFFF]+$/); // eslint-disable-line no-useless-escape
const ARIA_ATTR = seal(/^aria-[\-\w]+$/); // eslint-disable-line no-useless-escape
const IS_ALLOWED_URI = seal(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp|matrix):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i // eslint-disable-line no-useless-escape
);
const IS_SCRIPT_OR_DATA = seal(/^(?:\w+script|data):/i);
const ATTR_WHITESPACE = seal(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g // eslint-disable-line no-control-regex
);
const DOCTYPE_NAME = seal(/^html$/i);
const CUSTOM_ELEMENT = seal(/^[a-z][.\w]*(-[.\w]+)+$/i);

var EXPRESSIONS = /*#__PURE__*/Object.freeze({
  __proto__: null,
  ARIA_ATTR: ARIA_ATTR,
  ATTR_WHITESPACE: ATTR_WHITESPACE,
  CUSTOM_ELEMENT: CUSTOM_ELEMENT,
  DATA_ATTR: DATA_ATTR,
  DOCTYPE_NAME: DOCTYPE_NAME,
  ERB_EXPR: ERB_EXPR,
  IS_ALLOWED_URI: IS_ALLOWED_URI,
  IS_SCRIPT_OR_DATA: IS_SCRIPT_OR_DATA,
  MUSTACHE_EXPR: MUSTACHE_EXPR,
  TMPLIT_EXPR: TMPLIT_EXPR
});

/* eslint-disable @typescript-eslint/indent */
// https://developer.mozilla.org/en-US/docs/Web/API/Node/nodeType
const NODE_TYPE = {
  element: 1,
  attribute: 2,
  text: 3,
  cdataSection: 4,
  entityReference: 5,
  // Deprecated
  entityNode: 6,
  // Deprecated
  progressingInstruction: 7,
  comment: 8,
  document: 9,
  documentType: 10,
  documentFragment: 11,
  notation: 12 // Deprecated
};
const getGlobal = function getGlobal() {
  return typeof window === 'undefined' ? null : window;
};
/**
 * Creates a no-op policy for internal use only.
 * Don't export this function outside this module!
 * @param trustedTypes The policy factory.
 * @param purifyHostElement The Script element used to load DOMPurify (to determine policy name suffix).
 * @return The policy created (or null, if Trusted Types
 * are not supported or creating the policy failed).
 */
const _createTrustedTypesPolicy = function _createTrustedTypesPolicy(trustedTypes, purifyHostElement) {
  if (typeof trustedTypes !== 'object' || typeof trustedTypes.createPolicy !== 'function') {
    return null;
  }
  // Allow the callers to control the unique policy name
  // by adding a data-tt-policy-suffix to the script element with the DOMPurify.
  // Policy creation with duplicate names throws in Trusted Types.
  let suffix = null;
  const ATTR_NAME = 'data-tt-policy-suffix';
  if (purifyHostElement && purifyHostElement.hasAttribute(ATTR_NAME)) {
    suffix = purifyHostElement.getAttribute(ATTR_NAME);
  }
  const policyName = 'dompurify' + (suffix ? '#' + suffix : '');
  try {
    return trustedTypes.createPolicy(policyName, {
      createHTML(html) {
        return html;
      },
      createScriptURL(scriptUrl) {
        return scriptUrl;
      }
    });
  } catch (_) {
    // Policy creation failed (most likely another DOMPurify script has
    // already run). Skip creating the policy, as this will only cause errors
    // if TT are enforced.
    console.warn('TrustedTypes policy ' + policyName + ' could not be created.');
    return null;
  }
};
const _createHooksMap = function _createHooksMap() {
  return {
    afterSanitizeAttributes: [],
    afterSanitizeElements: [],
    afterSanitizeShadowDOM: [],
    beforeSanitizeAttributes: [],
    beforeSanitizeElements: [],
    beforeSanitizeShadowDOM: [],
    uponSanitizeAttribute: [],
    uponSanitizeElement: [],
    uponSanitizeShadowNode: []
  };
};
function createDOMPurify() {
  let window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : getGlobal();
  const DOMPurify = root => createDOMPurify(root);
  DOMPurify.version = '3.3.0';
  DOMPurify.removed = [];
  if (!window || !window.document || window.document.nodeType !== NODE_TYPE.document || !window.Element) {
    // Not running in a browser, provide a factory function
    // so that you can pass your own Window
    DOMPurify.isSupported = false;
    return DOMPurify;
  }
  let {
    document
  } = window;
  const originalDocument = document;
  const currentScript = originalDocument.currentScript;
  const {
    DocumentFragment,
    HTMLTemplateElement,
    Node,
    Element,
    NodeFilter,
    NamedNodeMap = window.NamedNodeMap || window.MozNamedAttrMap,
    HTMLFormElement,
    DOMParser,
    trustedTypes
  } = window;
  const ElementPrototype = Element.prototype;
  const cloneNode = lookupGetter(ElementPrototype, 'cloneNode');
  const remove = lookupGetter(ElementPrototype, 'remove');
  const getNextSibling = lookupGetter(ElementPrototype, 'nextSibling');
  const getChildNodes = lookupGetter(ElementPrototype, 'childNodes');
  const getParentNode = lookupGetter(ElementPrototype, 'parentNode');
  // As per issue #47, the web-components registry is inherited by a
  // new document created via createHTMLDocument. As per the spec
  // (http://w3c.github.io/webcomponents/spec/custom/#creating-and-passing-registries)
  // a new empty registry is used when creating a template contents owner
  // document, so we use that as our parent document to ensure nothing
  // is inherited.
  if (typeof HTMLTemplateElement === 'function') {
    const template = document.createElement('template');
    if (template.content && template.content.ownerDocument) {
      document = template.content.ownerDocument;
    }
  }
  let trustedTypesPolicy;
  let emptyHTML = '';
  const {
    implementation,
    createNodeIterator,
    createDocumentFragment,
    getElementsByTagName
  } = document;
  const {
    importNode
  } = originalDocument;
  let hooks = _createHooksMap();
  /**
   * Expose whether this browser supports running the full DOMPurify.
   */
  DOMPurify.isSupported = typeof entries === 'function' && typeof getParentNode === 'function' && implementation && implementation.createHTMLDocument !== undefined;
  const {
    MUSTACHE_EXPR,
    ERB_EXPR,
    TMPLIT_EXPR,
    DATA_ATTR,
    ARIA_ATTR,
    IS_SCRIPT_OR_DATA,
    ATTR_WHITESPACE,
    CUSTOM_ELEMENT
  } = EXPRESSIONS;
  let {
    IS_ALLOWED_URI: IS_ALLOWED_URI$1
  } = EXPRESSIONS;
  /**
   * We consider the elements and attributes below to be safe. Ideally
   * don't add any new ones but feel free to remove unwanted ones.
   */
  /* allowed element names */
  let ALLOWED_TAGS = null;
  const DEFAULT_ALLOWED_TAGS = addToSet({}, [...html$1, ...svg$1, ...svgFilters, ...mathMl$1, ...text]);
  /* Allowed attribute names */
  let ALLOWED_ATTR = null;
  const DEFAULT_ALLOWED_ATTR = addToSet({}, [...html, ...svg, ...mathMl, ...xml]);
  /*
   * Configure how DOMPurify should handle custom elements and their attributes as well as customized built-in elements.
   * @property {RegExp|Function|null} tagNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any custom elements)
   * @property {RegExp|Function|null} attributeNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any attributes not on the allow list)
   * @property {boolean} allowCustomizedBuiltInElements allow custom elements derived from built-ins if they pass CUSTOM_ELEMENT_HANDLING.tagNameCheck. Default: `false`.
   */
  let CUSTOM_ELEMENT_HANDLING = Object.seal(create(null, {
    tagNameCheck: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: null
    },
    attributeNameCheck: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: null
    },
    allowCustomizedBuiltInElements: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: false
    }
  }));
  /* Explicitly forbidden tags (overrides ALLOWED_TAGS/ADD_TAGS) */
  let FORBID_TAGS = null;
  /* Explicitly forbidden attributes (overrides ALLOWED_ATTR/ADD_ATTR) */
  let FORBID_ATTR = null;
  /* Config object to store ADD_TAGS/ADD_ATTR functions (when used as functions) */
  const EXTRA_ELEMENT_HANDLING = Object.seal(create(null, {
    tagCheck: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: null
    },
    attributeCheck: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: null
    }
  }));
  /* Decide if ARIA attributes are okay */
  let ALLOW_ARIA_ATTR = true;
  /* Decide if custom data attributes are okay */
  let ALLOW_DATA_ATTR = true;
  /* Decide if unknown protocols are okay */
  let ALLOW_UNKNOWN_PROTOCOLS = false;
  /* Decide if self-closing tags in attributes are allowed.
   * Usually removed due to a mXSS issue in jQuery 3.0 */
  let ALLOW_SELF_CLOSE_IN_ATTR = true;
  /* Output should be safe for common template engines.
   * This means, DOMPurify removes data attributes, mustaches and ERB
   */
  let SAFE_FOR_TEMPLATES = false;
  /* Output should be safe even for XML used within HTML and alike.
   * This means, DOMPurify removes comments when containing risky content.
   */
  let SAFE_FOR_XML = true;
  /* Decide if document with <html>... should be returned */
  let WHOLE_DOCUMENT = false;
  /* Track whether config is already set on this instance of DOMPurify. */
  let SET_CONFIG = false;
  /* Decide if all elements (e.g. style, script) must be children of
   * document.body. By default, browsers might move them to document.head */
  let FORCE_BODY = false;
  /* Decide if a DOM `HTMLBodyElement` should be returned, instead of a html
   * string (or a TrustedHTML object if Trusted Types are supported).
   * If `WHOLE_DOCUMENT` is enabled a `HTMLHtmlElement` will be returned instead
   */
  let RETURN_DOM = false;
  /* Decide if a DOM `DocumentFragment` should be returned, instead of a html
   * string  (or a TrustedHTML object if Trusted Types are supported) */
  let RETURN_DOM_FRAGMENT = false;
  /* Try to return a Trusted Type object instead of a string, return a string in
   * case Trusted Types are not supported  */
  let RETURN_TRUSTED_TYPE = false;
  /* Output should be free from DOM clobbering attacks?
   * This sanitizes markups named with colliding, clobberable built-in DOM APIs.
   */
  let SANITIZE_DOM = true;
  /* Achieve full DOM Clobbering protection by isolating the namespace of named
   * properties and JS variables, mitigating attacks that abuse the HTML/DOM spec rules.
   *
   * HTML/DOM spec rules that enable DOM Clobbering:
   *   - Named Access on Window (§7.3.3)
   *   - DOM Tree Accessors (§3.1.5)
   *   - Form Element Parent-Child Relations (§4.10.3)
   *   - Iframe srcdoc / Nested WindowProxies (§4.8.5)
   *   - HTMLCollection (§4.2.10.2)
   *
   * Namespace isolation is implemented by prefixing `id` and `name` attributes
   * with a constant string, i.e., `user-content-`
   */
  let SANITIZE_NAMED_PROPS = false;
  const SANITIZE_NAMED_PROPS_PREFIX = 'user-content-';
  /* Keep element content when removing element? */
  let KEEP_CONTENT = true;
  /* If a `Node` is passed to sanitize(), then performs sanitization in-place instead
   * of importing it into a new Document and returning a sanitized copy */
  let IN_PLACE = false;
  /* Allow usage of profiles like html, svg and mathMl */
  let USE_PROFILES = {};
  /* Tags to ignore content of when KEEP_CONTENT is true */
  let FORBID_CONTENTS = null;
  const DEFAULT_FORBID_CONTENTS = addToSet({}, ['annotation-xml', 'audio', 'colgroup', 'desc', 'foreignobject', 'head', 'iframe', 'math', 'mi', 'mn', 'mo', 'ms', 'mtext', 'noembed', 'noframes', 'noscript', 'plaintext', 'script', 'style', 'svg', 'template', 'thead', 'title', 'video', 'xmp']);
  /* Tags that are safe for data: URIs */
  let DATA_URI_TAGS = null;
  const DEFAULT_DATA_URI_TAGS = addToSet({}, ['audio', 'video', 'img', 'source', 'image', 'track']);
  /* Attributes safe for values like "javascript:" */
  let URI_SAFE_ATTRIBUTES = null;
  const DEFAULT_URI_SAFE_ATTRIBUTES = addToSet({}, ['alt', 'class', 'for', 'id', 'label', 'name', 'pattern', 'placeholder', 'role', 'summary', 'title', 'value', 'style', 'xmlns']);
  const MATHML_NAMESPACE = 'http://www.w3.org/1998/Math/MathML';
  const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
  const HTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';
  /* Document namespace */
  let NAMESPACE = HTML_NAMESPACE;
  let IS_EMPTY_INPUT = false;
  /* Allowed XHTML+XML namespaces */
  let ALLOWED_NAMESPACES = null;
  const DEFAULT_ALLOWED_NAMESPACES = addToSet({}, [MATHML_NAMESPACE, SVG_NAMESPACE, HTML_NAMESPACE], stringToString);
  let MATHML_TEXT_INTEGRATION_POINTS = addToSet({}, ['mi', 'mo', 'mn', 'ms', 'mtext']);
  let HTML_INTEGRATION_POINTS = addToSet({}, ['annotation-xml']);
  // Certain elements are allowed in both SVG and HTML
  // namespace. We need to specify them explicitly
  // so that they don't get erroneously deleted from
  // HTML namespace.
  const COMMON_SVG_AND_HTML_ELEMENTS = addToSet({}, ['title', 'style', 'font', 'a', 'script']);
  /* Parsing of strict XHTML documents */
  let PARSER_MEDIA_TYPE = null;
  const SUPPORTED_PARSER_MEDIA_TYPES = ['application/xhtml+xml', 'text/html'];
  const DEFAULT_PARSER_MEDIA_TYPE = 'text/html';
  let transformCaseFunc = null;
  /* Keep a reference to config to pass to hooks */
  let CONFIG = null;
  /* Ideally, do not touch anything below this line */
  /* ______________________________________________ */
  const formElement = document.createElement('form');
  const isRegexOrFunction = function isRegexOrFunction(testValue) {
    return testValue instanceof RegExp || testValue instanceof Function;
  };
  /**
   * _parseConfig
   *
   * @param cfg optional config literal
   */
  // eslint-disable-next-line complexity
  const _parseConfig = function _parseConfig() {
    let cfg = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    if (CONFIG && CONFIG === cfg) {
      return;
    }
    /* Shield configuration object from tampering */
    if (!cfg || typeof cfg !== 'object') {
      cfg = {};
    }
    /* Shield configuration object from prototype pollution */
    cfg = clone(cfg);
    PARSER_MEDIA_TYPE =
    // eslint-disable-next-line unicorn/prefer-includes
    SUPPORTED_PARSER_MEDIA_TYPES.indexOf(cfg.PARSER_MEDIA_TYPE) === -1 ? DEFAULT_PARSER_MEDIA_TYPE : cfg.PARSER_MEDIA_TYPE;
    // HTML tags and attributes are not case-sensitive, converting to lowercase. Keeping XHTML as is.
    transformCaseFunc = PARSER_MEDIA_TYPE === 'application/xhtml+xml' ? stringToString : stringToLowerCase;
    /* Set configuration parameters */
    ALLOWED_TAGS = objectHasOwnProperty(cfg, 'ALLOWED_TAGS') ? addToSet({}, cfg.ALLOWED_TAGS, transformCaseFunc) : DEFAULT_ALLOWED_TAGS;
    ALLOWED_ATTR = objectHasOwnProperty(cfg, 'ALLOWED_ATTR') ? addToSet({}, cfg.ALLOWED_ATTR, transformCaseFunc) : DEFAULT_ALLOWED_ATTR;
    ALLOWED_NAMESPACES = objectHasOwnProperty(cfg, 'ALLOWED_NAMESPACES') ? addToSet({}, cfg.ALLOWED_NAMESPACES, stringToString) : DEFAULT_ALLOWED_NAMESPACES;
    URI_SAFE_ATTRIBUTES = objectHasOwnProperty(cfg, 'ADD_URI_SAFE_ATTR') ? addToSet(clone(DEFAULT_URI_SAFE_ATTRIBUTES), cfg.ADD_URI_SAFE_ATTR, transformCaseFunc) : DEFAULT_URI_SAFE_ATTRIBUTES;
    DATA_URI_TAGS = objectHasOwnProperty(cfg, 'ADD_DATA_URI_TAGS') ? addToSet(clone(DEFAULT_DATA_URI_TAGS), cfg.ADD_DATA_URI_TAGS, transformCaseFunc) : DEFAULT_DATA_URI_TAGS;
    FORBID_CONTENTS = objectHasOwnProperty(cfg, 'FORBID_CONTENTS') ? addToSet({}, cfg.FORBID_CONTENTS, transformCaseFunc) : DEFAULT_FORBID_CONTENTS;
    FORBID_TAGS = objectHasOwnProperty(cfg, 'FORBID_TAGS') ? addToSet({}, cfg.FORBID_TAGS, transformCaseFunc) : clone({});
    FORBID_ATTR = objectHasOwnProperty(cfg, 'FORBID_ATTR') ? addToSet({}, cfg.FORBID_ATTR, transformCaseFunc) : clone({});
    USE_PROFILES = objectHasOwnProperty(cfg, 'USE_PROFILES') ? cfg.USE_PROFILES : false;
    ALLOW_ARIA_ATTR = cfg.ALLOW_ARIA_ATTR !== false; // Default true
    ALLOW_DATA_ATTR = cfg.ALLOW_DATA_ATTR !== false; // Default true
    ALLOW_UNKNOWN_PROTOCOLS = cfg.ALLOW_UNKNOWN_PROTOCOLS || false; // Default false
    ALLOW_SELF_CLOSE_IN_ATTR = cfg.ALLOW_SELF_CLOSE_IN_ATTR !== false; // Default true
    SAFE_FOR_TEMPLATES = cfg.SAFE_FOR_TEMPLATES || false; // Default false
    SAFE_FOR_XML = cfg.SAFE_FOR_XML !== false; // Default true
    WHOLE_DOCUMENT = cfg.WHOLE_DOCUMENT || false; // Default false
    RETURN_DOM = cfg.RETURN_DOM || false; // Default false
    RETURN_DOM_FRAGMENT = cfg.RETURN_DOM_FRAGMENT || false; // Default false
    RETURN_TRUSTED_TYPE = cfg.RETURN_TRUSTED_TYPE || false; // Default false
    FORCE_BODY = cfg.FORCE_BODY || false; // Default false
    SANITIZE_DOM = cfg.SANITIZE_DOM !== false; // Default true
    SANITIZE_NAMED_PROPS = cfg.SANITIZE_NAMED_PROPS || false; // Default false
    KEEP_CONTENT = cfg.KEEP_CONTENT !== false; // Default true
    IN_PLACE = cfg.IN_PLACE || false; // Default false
    IS_ALLOWED_URI$1 = cfg.ALLOWED_URI_REGEXP || IS_ALLOWED_URI;
    NAMESPACE = cfg.NAMESPACE || HTML_NAMESPACE;
    MATHML_TEXT_INTEGRATION_POINTS = cfg.MATHML_TEXT_INTEGRATION_POINTS || MATHML_TEXT_INTEGRATION_POINTS;
    HTML_INTEGRATION_POINTS = cfg.HTML_INTEGRATION_POINTS || HTML_INTEGRATION_POINTS;
    CUSTOM_ELEMENT_HANDLING = cfg.CUSTOM_ELEMENT_HANDLING || {};
    if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck)) {
      CUSTOM_ELEMENT_HANDLING.tagNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck;
    }
    if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck)) {
      CUSTOM_ELEMENT_HANDLING.attributeNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck;
    }
    if (cfg.CUSTOM_ELEMENT_HANDLING && typeof cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements === 'boolean') {
      CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements = cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements;
    }
    if (SAFE_FOR_TEMPLATES) {
      ALLOW_DATA_ATTR = false;
    }
    if (RETURN_DOM_FRAGMENT) {
      RETURN_DOM = true;
    }
    /* Parse profile info */
    if (USE_PROFILES) {
      ALLOWED_TAGS = addToSet({}, text);
      ALLOWED_ATTR = [];
      if (USE_PROFILES.html === true) {
        addToSet(ALLOWED_TAGS, html$1);
        addToSet(ALLOWED_ATTR, html);
      }
      if (USE_PROFILES.svg === true) {
        addToSet(ALLOWED_TAGS, svg$1);
        addToSet(ALLOWED_ATTR, svg);
        addToSet(ALLOWED_ATTR, xml);
      }
      if (USE_PROFILES.svgFilters === true) {
        addToSet(ALLOWED_TAGS, svgFilters);
        addToSet(ALLOWED_ATTR, svg);
        addToSet(ALLOWED_ATTR, xml);
      }
      if (USE_PROFILES.mathMl === true) {
        addToSet(ALLOWED_TAGS, mathMl$1);
        addToSet(ALLOWED_ATTR, mathMl);
        addToSet(ALLOWED_ATTR, xml);
      }
    }
    /* Merge configuration parameters */
    if (cfg.ADD_TAGS) {
      if (typeof cfg.ADD_TAGS === 'function') {
        EXTRA_ELEMENT_HANDLING.tagCheck = cfg.ADD_TAGS;
      } else {
        if (ALLOWED_TAGS === DEFAULT_ALLOWED_TAGS) {
          ALLOWED_TAGS = clone(ALLOWED_TAGS);
        }
        addToSet(ALLOWED_TAGS, cfg.ADD_TAGS, transformCaseFunc);
      }
    }
    if (cfg.ADD_ATTR) {
      if (typeof cfg.ADD_ATTR === 'function') {
        EXTRA_ELEMENT_HANDLING.attributeCheck = cfg.ADD_ATTR;
      } else {
        if (ALLOWED_ATTR === DEFAULT_ALLOWED_ATTR) {
          ALLOWED_ATTR = clone(ALLOWED_ATTR);
        }
        addToSet(ALLOWED_ATTR, cfg.ADD_ATTR, transformCaseFunc);
      }
    }
    if (cfg.ADD_URI_SAFE_ATTR) {
      addToSet(URI_SAFE_ATTRIBUTES, cfg.ADD_URI_SAFE_ATTR, transformCaseFunc);
    }
    if (cfg.FORBID_CONTENTS) {
      if (FORBID_CONTENTS === DEFAULT_FORBID_CONTENTS) {
        FORBID_CONTENTS = clone(FORBID_CONTENTS);
      }
      addToSet(FORBID_CONTENTS, cfg.FORBID_CONTENTS, transformCaseFunc);
    }
    /* Add #text in case KEEP_CONTENT is set to true */
    if (KEEP_CONTENT) {
      ALLOWED_TAGS['#text'] = true;
    }
    /* Add html, head and body to ALLOWED_TAGS in case WHOLE_DOCUMENT is true */
    if (WHOLE_DOCUMENT) {
      addToSet(ALLOWED_TAGS, ['html', 'head', 'body']);
    }
    /* Add tbody to ALLOWED_TAGS in case tables are permitted, see #286, #365 */
    if (ALLOWED_TAGS.table) {
      addToSet(ALLOWED_TAGS, ['tbody']);
      delete FORBID_TAGS.tbody;
    }
    if (cfg.TRUSTED_TYPES_POLICY) {
      if (typeof cfg.TRUSTED_TYPES_POLICY.createHTML !== 'function') {
        throw typeErrorCreate('TRUSTED_TYPES_POLICY configuration option must provide a "createHTML" hook.');
      }
      if (typeof cfg.TRUSTED_TYPES_POLICY.createScriptURL !== 'function') {
        throw typeErrorCreate('TRUSTED_TYPES_POLICY configuration option must provide a "createScriptURL" hook.');
      }
      // Overwrite existing TrustedTypes policy.
      trustedTypesPolicy = cfg.TRUSTED_TYPES_POLICY;
      // Sign local variables required by `sanitize`.
      emptyHTML = trustedTypesPolicy.createHTML('');
    } else {
      // Uninitialized policy, attempt to initialize the internal dompurify policy.
      if (trustedTypesPolicy === undefined) {
        trustedTypesPolicy = _createTrustedTypesPolicy(trustedTypes, currentScript);
      }
      // If creating the internal policy succeeded sign internal variables.
      if (trustedTypesPolicy !== null && typeof emptyHTML === 'string') {
        emptyHTML = trustedTypesPolicy.createHTML('');
      }
    }
    // Prevent further manipulation of configuration.
    // Not available in IE8, Safari 5, etc.
    if (freeze) {
      freeze(cfg);
    }
    CONFIG = cfg;
  };
  /* Keep track of all possible SVG and MathML tags
   * so that we can perform the namespace checks
   * correctly. */
  const ALL_SVG_TAGS = addToSet({}, [...svg$1, ...svgFilters, ...svgDisallowed]);
  const ALL_MATHML_TAGS = addToSet({}, [...mathMl$1, ...mathMlDisallowed]);
  /**
   * @param element a DOM element whose namespace is being checked
   * @returns Return false if the element has a
   *  namespace that a spec-compliant parser would never
   *  return. Return true otherwise.
   */
  const _checkValidNamespace = function _checkValidNamespace(element) {
    let parent = getParentNode(element);
    // In JSDOM, if we're inside shadow DOM, then parentNode
    // can be null. We just simulate parent in this case.
    if (!parent || !parent.tagName) {
      parent = {
        namespaceURI: NAMESPACE,
        tagName: 'template'
      };
    }
    const tagName = stringToLowerCase(element.tagName);
    const parentTagName = stringToLowerCase(parent.tagName);
    if (!ALLOWED_NAMESPACES[element.namespaceURI]) {
      return false;
    }
    if (element.namespaceURI === SVG_NAMESPACE) {
      // The only way to switch from HTML namespace to SVG
      // is via <svg>. If it happens via any other tag, then
      // it should be killed.
      if (parent.namespaceURI === HTML_NAMESPACE) {
        return tagName === 'svg';
      }
      // The only way to switch from MathML to SVG is via`
      // svg if parent is either <annotation-xml> or MathML
      // text integration points.
      if (parent.namespaceURI === MATHML_NAMESPACE) {
        return tagName === 'svg' && (parentTagName === 'annotation-xml' || MATHML_TEXT_INTEGRATION_POINTS[parentTagName]);
      }
      // We only allow elements that are defined in SVG
      // spec. All others are disallowed in SVG namespace.
      return Boolean(ALL_SVG_TAGS[tagName]);
    }
    if (element.namespaceURI === MATHML_NAMESPACE) {
      // The only way to switch from HTML namespace to MathML
      // is via <math>. If it happens via any other tag, then
      // it should be killed.
      if (parent.namespaceURI === HTML_NAMESPACE) {
        return tagName === 'math';
      }
      // The only way to switch from SVG to MathML is via
      // <math> and HTML integration points
      if (parent.namespaceURI === SVG_NAMESPACE) {
        return tagName === 'math' && HTML_INTEGRATION_POINTS[parentTagName];
      }
      // We only allow elements that are defined in MathML
      // spec. All others are disallowed in MathML namespace.
      return Boolean(ALL_MATHML_TAGS[tagName]);
    }
    if (element.namespaceURI === HTML_NAMESPACE) {
      // The only way to switch from SVG to HTML is via
      // HTML integration points, and from MathML to HTML
      // is via MathML text integration points
      if (parent.namespaceURI === SVG_NAMESPACE && !HTML_INTEGRATION_POINTS[parentTagName]) {
        return false;
      }
      if (parent.namespaceURI === MATHML_NAMESPACE && !MATHML_TEXT_INTEGRATION_POINTS[parentTagName]) {
        return false;
      }
      // We disallow tags that are specific for MathML
      // or SVG and should never appear in HTML namespace
      return !ALL_MATHML_TAGS[tagName] && (COMMON_SVG_AND_HTML_ELEMENTS[tagName] || !ALL_SVG_TAGS[tagName]);
    }
    // For XHTML and XML documents that support custom namespaces
    if (PARSER_MEDIA_TYPE === 'application/xhtml+xml' && ALLOWED_NAMESPACES[element.namespaceURI]) {
      return true;
    }
    // The code should never reach this place (this means
    // that the element somehow got namespace that is not
    // HTML, SVG, MathML or allowed via ALLOWED_NAMESPACES).
    // Return false just in case.
    return false;
  };
  /**
   * _forceRemove
   *
   * @param node a DOM node
   */
  const _forceRemove = function _forceRemove(node) {
    arrayPush(DOMPurify.removed, {
      element: node
    });
    try {
      // eslint-disable-next-line unicorn/prefer-dom-node-remove
      getParentNode(node).removeChild(node);
    } catch (_) {
      remove(node);
    }
  };
  /**
   * _removeAttribute
   *
   * @param name an Attribute name
   * @param element a DOM node
   */
  const _removeAttribute = function _removeAttribute(name, element) {
    try {
      arrayPush(DOMPurify.removed, {
        attribute: element.getAttributeNode(name),
        from: element
      });
    } catch (_) {
      arrayPush(DOMPurify.removed, {
        attribute: null,
        from: element
      });
    }
    element.removeAttribute(name);
    // We void attribute values for unremovable "is" attributes
    if (name === 'is') {
      if (RETURN_DOM || RETURN_DOM_FRAGMENT) {
        try {
          _forceRemove(element);
        } catch (_) {}
      } else {
        try {
          element.setAttribute(name, '');
        } catch (_) {}
      }
    }
  };
  /**
   * _initDocument
   *
   * @param dirty - a string of dirty markup
   * @return a DOM, filled with the dirty markup
   */
  const _initDocument = function _initDocument(dirty) {
    /* Create a HTML document */
    let doc = null;
    let leadingWhitespace = null;
    if (FORCE_BODY) {
      dirty = '<remove></remove>' + dirty;
    } else {
      /* If FORCE_BODY isn't used, leading whitespace needs to be preserved manually */
      const matches = stringMatch(dirty, /^[\r\n\t ]+/);
      leadingWhitespace = matches && matches[0];
    }
    if (PARSER_MEDIA_TYPE === 'application/xhtml+xml' && NAMESPACE === HTML_NAMESPACE) {
      // Root of XHTML doc must contain xmlns declaration (see https://www.w3.org/TR/xhtml1/normative.html#strict)
      dirty = '<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>' + dirty + '</body></html>';
    }
    const dirtyPayload = trustedTypesPolicy ? trustedTypesPolicy.createHTML(dirty) : dirty;
    /*
     * Use the DOMParser API by default, fallback later if needs be
     * DOMParser not work for svg when has multiple root element.
     */
    if (NAMESPACE === HTML_NAMESPACE) {
      try {
        doc = new DOMParser().parseFromString(dirtyPayload, PARSER_MEDIA_TYPE);
      } catch (_) {}
    }
    /* Use createHTMLDocument in case DOMParser is not available */
    if (!doc || !doc.documentElement) {
      doc = implementation.createDocument(NAMESPACE, 'template', null);
      try {
        doc.documentElement.innerHTML = IS_EMPTY_INPUT ? emptyHTML : dirtyPayload;
      } catch (_) {
        // Syntax error if dirtyPayload is invalid xml
      }
    }
    const body = doc.body || doc.documentElement;
    if (dirty && leadingWhitespace) {
      body.insertBefore(document.createTextNode(leadingWhitespace), body.childNodes[0] || null);
    }
    /* Work on whole document or just its body */
    if (NAMESPACE === HTML_NAMESPACE) {
      return getElementsByTagName.call(doc, WHOLE_DOCUMENT ? 'html' : 'body')[0];
    }
    return WHOLE_DOCUMENT ? doc.documentElement : body;
  };
  /**
   * Creates a NodeIterator object that you can use to traverse filtered lists of nodes or elements in a document.
   *
   * @param root The root element or node to start traversing on.
   * @return The created NodeIterator
   */
  const _createNodeIterator = function _createNodeIterator(root) {
    return createNodeIterator.call(root.ownerDocument || root, root,
    // eslint-disable-next-line no-bitwise
    NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT | NodeFilter.SHOW_PROCESSING_INSTRUCTION | NodeFilter.SHOW_CDATA_SECTION, null);
  };
  /**
   * _isClobbered
   *
   * @param element element to check for clobbering attacks
   * @return true if clobbered, false if safe
   */
  const _isClobbered = function _isClobbered(element) {
    return element instanceof HTMLFormElement && (typeof element.nodeName !== 'string' || typeof element.textContent !== 'string' || typeof element.removeChild !== 'function' || !(element.attributes instanceof NamedNodeMap) || typeof element.removeAttribute !== 'function' || typeof element.setAttribute !== 'function' || typeof element.namespaceURI !== 'string' || typeof element.insertBefore !== 'function' || typeof element.hasChildNodes !== 'function');
  };
  /**
   * Checks whether the given object is a DOM node.
   *
   * @param value object to check whether it's a DOM node
   * @return true is object is a DOM node
   */
  const _isNode = function _isNode(value) {
    return typeof Node === 'function' && value instanceof Node;
  };
  function _executeHooks(hooks, currentNode, data) {
    arrayForEach(hooks, hook => {
      hook.call(DOMPurify, currentNode, data, CONFIG);
    });
  }
  /**
   * _sanitizeElements
   *
   * @protect nodeName
   * @protect textContent
   * @protect removeChild
   * @param currentNode to check for permission to exist
   * @return true if node was killed, false if left alive
   */
  const _sanitizeElements = function _sanitizeElements(currentNode) {
    let content = null;
    /* Execute a hook if present */
    _executeHooks(hooks.beforeSanitizeElements, currentNode, null);
    /* Check if element is clobbered or can clobber */
    if (_isClobbered(currentNode)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Now let's check the element's type and name */
    const tagName = transformCaseFunc(currentNode.nodeName);
    /* Execute a hook if present */
    _executeHooks(hooks.uponSanitizeElement, currentNode, {
      tagName,
      allowedTags: ALLOWED_TAGS
    });
    /* Detect mXSS attempts abusing namespace confusion */
    if (SAFE_FOR_XML && currentNode.hasChildNodes() && !_isNode(currentNode.firstElementChild) && regExpTest(/<[/\w!]/g, currentNode.innerHTML) && regExpTest(/<[/\w!]/g, currentNode.textContent)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Remove any occurrence of processing instructions */
    if (currentNode.nodeType === NODE_TYPE.progressingInstruction) {
      _forceRemove(currentNode);
      return true;
    }
    /* Remove any kind of possibly harmful comments */
    if (SAFE_FOR_XML && currentNode.nodeType === NODE_TYPE.comment && regExpTest(/<[/\w]/g, currentNode.data)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Remove element if anything forbids its presence */
    if (!(EXTRA_ELEMENT_HANDLING.tagCheck instanceof Function && EXTRA_ELEMENT_HANDLING.tagCheck(tagName)) && (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName])) {
      /* Check if we have a custom element to handle */
      if (!FORBID_TAGS[tagName] && _isBasicCustomElement(tagName)) {
        if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, tagName)) {
          return false;
        }
        if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(tagName)) {
          return false;
        }
      }
      /* Keep content except for bad-listed elements */
      if (KEEP_CONTENT && !FORBID_CONTENTS[tagName]) {
        const parentNode = getParentNode(currentNode) || currentNode.parentNode;
        const childNodes = getChildNodes(currentNode) || currentNode.childNodes;
        if (childNodes && parentNode) {
          const childCount = childNodes.length;
          for (let i = childCount - 1; i >= 0; --i) {
            const childClone = cloneNode(childNodes[i], true);
            childClone.__removalCount = (currentNode.__removalCount || 0) + 1;
            parentNode.insertBefore(childClone, getNextSibling(currentNode));
          }
        }
      }
      _forceRemove(currentNode);
      return true;
    }
    /* Check whether element has a valid namespace */
    if (currentNode instanceof Element && !_checkValidNamespace(currentNode)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Make sure that older browsers don't get fallback-tag mXSS */
    if ((tagName === 'noscript' || tagName === 'noembed' || tagName === 'noframes') && regExpTest(/<\/no(script|embed|frames)/i, currentNode.innerHTML)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Sanitize element content to be template-safe */
    if (SAFE_FOR_TEMPLATES && currentNode.nodeType === NODE_TYPE.text) {
      /* Get the element's text content */
      content = currentNode.textContent;
      arrayForEach([MUSTACHE_EXPR, ERB_EXPR, TMPLIT_EXPR], expr => {
        content = stringReplace(content, expr, ' ');
      });
      if (currentNode.textContent !== content) {
        arrayPush(DOMPurify.removed, {
          element: currentNode.cloneNode()
        });
        currentNode.textContent = content;
      }
    }
    /* Execute a hook if present */
    _executeHooks(hooks.afterSanitizeElements, currentNode, null);
    return false;
  };
  /**
   * _isValidAttribute
   *
   * @param lcTag Lowercase tag name of containing element.
   * @param lcName Lowercase attribute name.
   * @param value Attribute value.
   * @return Returns true if `value` is valid, otherwise false.
   */
  // eslint-disable-next-line complexity
  const _isValidAttribute = function _isValidAttribute(lcTag, lcName, value) {
    /* Make sure attribute cannot clobber */
    if (SANITIZE_DOM && (lcName === 'id' || lcName === 'name') && (value in document || value in formElement)) {
      return false;
    }
    /* Allow valid data-* attributes: At least one character after "-"
        (https://html.spec.whatwg.org/multipage/dom.html#embedding-custom-non-visible-data-with-the-data-*-attributes)
        XML-compatible (https://html.spec.whatwg.org/multipage/infrastructure.html#xml-compatible and http://www.w3.org/TR/xml/#d0e804)
        We don't need to check the value; it's always URI safe. */
    if (ALLOW_DATA_ATTR && !FORBID_ATTR[lcName] && regExpTest(DATA_ATTR, lcName)) ; else if (ALLOW_ARIA_ATTR && regExpTest(ARIA_ATTR, lcName)) ; else if (EXTRA_ELEMENT_HANDLING.attributeCheck instanceof Function && EXTRA_ELEMENT_HANDLING.attributeCheck(lcName, lcTag)) ; else if (!ALLOWED_ATTR[lcName] || FORBID_ATTR[lcName]) {
      if (
      // First condition does a very basic check if a) it's basically a valid custom element tagname AND
      // b) if the tagName passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
      // and c) if the attribute name passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.attributeNameCheck
      _isBasicCustomElement(lcTag) && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, lcTag) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(lcTag)) && (CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.attributeNameCheck, lcName) || CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.attributeNameCheck(lcName, lcTag)) ||
      // Alternative, second condition checks if it's an `is`-attribute, AND
      // the value passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
      lcName === 'is' && CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, value) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(value))) ; else {
        return false;
      }
      /* Check value is safe. First, is attr inert? If so, is safe */
    } else if (URI_SAFE_ATTRIBUTES[lcName]) ; else if (regExpTest(IS_ALLOWED_URI$1, stringReplace(value, ATTR_WHITESPACE, ''))) ; else if ((lcName === 'src' || lcName === 'xlink:href' || lcName === 'href') && lcTag !== 'script' && stringIndexOf(value, 'data:') === 0 && DATA_URI_TAGS[lcTag]) ; else if (ALLOW_UNKNOWN_PROTOCOLS && !regExpTest(IS_SCRIPT_OR_DATA, stringReplace(value, ATTR_WHITESPACE, ''))) ; else if (value) {
      return false;
    } else ;
    return true;
  };
  /**
   * _isBasicCustomElement
   * checks if at least one dash is included in tagName, and it's not the first char
   * for more sophisticated checking see https://github.com/sindresorhus/validate-element-name
   *
   * @param tagName name of the tag of the node to sanitize
   * @returns Returns true if the tag name meets the basic criteria for a custom element, otherwise false.
   */
  const _isBasicCustomElement = function _isBasicCustomElement(tagName) {
    return tagName !== 'annotation-xml' && stringMatch(tagName, CUSTOM_ELEMENT);
  };
  /**
   * _sanitizeAttributes
   *
   * @protect attributes
   * @protect nodeName
   * @protect removeAttribute
   * @protect setAttribute
   *
   * @param currentNode to sanitize
   */
  const _sanitizeAttributes = function _sanitizeAttributes(currentNode) {
    /* Execute a hook if present */
    _executeHooks(hooks.beforeSanitizeAttributes, currentNode, null);
    const {
      attributes
    } = currentNode;
    /* Check if we have attributes; if not we might have a text node */
    if (!attributes || _isClobbered(currentNode)) {
      return;
    }
    const hookEvent = {
      attrName: '',
      attrValue: '',
      keepAttr: true,
      allowedAttributes: ALLOWED_ATTR,
      forceKeepAttr: undefined
    };
    let l = attributes.length;
    /* Go backwards over all attributes; safely remove bad ones */
    while (l--) {
      const attr = attributes[l];
      const {
        name,
        namespaceURI,
        value: attrValue
      } = attr;
      const lcName = transformCaseFunc(name);
      const initValue = attrValue;
      let value = name === 'value' ? initValue : stringTrim(initValue);
      /* Execute a hook if present */
      hookEvent.attrName = lcName;
      hookEvent.attrValue = value;
      hookEvent.keepAttr = true;
      hookEvent.forceKeepAttr = undefined; // Allows developers to see this is a property they can set
      _executeHooks(hooks.uponSanitizeAttribute, currentNode, hookEvent);
      value = hookEvent.attrValue;
      /* Full DOM Clobbering protection via namespace isolation,
       * Prefix id and name attributes with `user-content-`
       */
      if (SANITIZE_NAMED_PROPS && (lcName === 'id' || lcName === 'name')) {
        // Remove the attribute with this value
        _removeAttribute(name, currentNode);
        // Prefix the value and later re-create the attribute with the sanitized value
        value = SANITIZE_NAMED_PROPS_PREFIX + value;
      }
      /* Work around a security issue with comments inside attributes */
      if (SAFE_FOR_XML && regExpTest(/((--!?|])>)|<\/(style|title|textarea)/i, value)) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Make sure we cannot easily use animated hrefs, even if animations are allowed */
      if (lcName === 'attributename' && stringMatch(value, 'href')) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Did the hooks approve of the attribute? */
      if (hookEvent.forceKeepAttr) {
        continue;
      }
      /* Did the hooks approve of the attribute? */
      if (!hookEvent.keepAttr) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Work around a security issue in jQuery 3.0 */
      if (!ALLOW_SELF_CLOSE_IN_ATTR && regExpTest(/\/>/i, value)) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Sanitize attribute content to be template-safe */
      if (SAFE_FOR_TEMPLATES) {
        arrayForEach([MUSTACHE_EXPR, ERB_EXPR, TMPLIT_EXPR], expr => {
          value = stringReplace(value, expr, ' ');
        });
      }
      /* Is `value` valid for this attribute? */
      const lcTag = transformCaseFunc(currentNode.nodeName);
      if (!_isValidAttribute(lcTag, lcName, value)) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Handle attributes that require Trusted Types */
      if (trustedTypesPolicy && typeof trustedTypes === 'object' && typeof trustedTypes.getAttributeType === 'function') {
        if (namespaceURI) ; else {
          switch (trustedTypes.getAttributeType(lcTag, lcName)) {
            case 'TrustedHTML':
              {
                value = trustedTypesPolicy.createHTML(value);
                break;
              }
            case 'TrustedScriptURL':
              {
                value = trustedTypesPolicy.createScriptURL(value);
                break;
              }
          }
        }
      }
      /* Handle invalid data-* attribute set by try-catching it */
      if (value !== initValue) {
        try {
          if (namespaceURI) {
            currentNode.setAttributeNS(namespaceURI, name, value);
          } else {
            /* Fallback to setAttribute() for browser-unrecognized namespaces e.g. "x-schema". */
            currentNode.setAttribute(name, value);
          }
          if (_isClobbered(currentNode)) {
            _forceRemove(currentNode);
          } else {
            arrayPop(DOMPurify.removed);
          }
        } catch (_) {
          _removeAttribute(name, currentNode);
        }
      }
    }
    /* Execute a hook if present */
    _executeHooks(hooks.afterSanitizeAttributes, currentNode, null);
  };
  /**
   * _sanitizeShadowDOM
   *
   * @param fragment to iterate over recursively
   */
  const _sanitizeShadowDOM = function _sanitizeShadowDOM(fragment) {
    let shadowNode = null;
    const shadowIterator = _createNodeIterator(fragment);
    /* Execute a hook if present */
    _executeHooks(hooks.beforeSanitizeShadowDOM, fragment, null);
    while (shadowNode = shadowIterator.nextNode()) {
      /* Execute a hook if present */
      _executeHooks(hooks.uponSanitizeShadowNode, shadowNode, null);
      /* Sanitize tags and elements */
      _sanitizeElements(shadowNode);
      /* Check attributes next */
      _sanitizeAttributes(shadowNode);
      /* Deep shadow DOM detected */
      if (shadowNode.content instanceof DocumentFragment) {
        _sanitizeShadowDOM(shadowNode.content);
      }
    }
    /* Execute a hook if present */
    _executeHooks(hooks.afterSanitizeShadowDOM, fragment, null);
  };
  // eslint-disable-next-line complexity
  DOMPurify.sanitize = function (dirty) {
    let cfg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    let body = null;
    let importedNode = null;
    let currentNode = null;
    let returnNode = null;
    /* Make sure we have a string to sanitize.
      DO NOT return early, as this will return the wrong type if
      the user has requested a DOM object rather than a string */
    IS_EMPTY_INPUT = !dirty;
    if (IS_EMPTY_INPUT) {
      dirty = '<!-->';
    }
    /* Stringify, in case dirty is an object */
    if (typeof dirty !== 'string' && !_isNode(dirty)) {
      if (typeof dirty.toString === 'function') {
        dirty = dirty.toString();
        if (typeof dirty !== 'string') {
          throw typeErrorCreate('dirty is not a string, aborting');
        }
      } else {
        throw typeErrorCreate('toString is not a function');
      }
    }
    /* Return dirty HTML if DOMPurify cannot run */
    if (!DOMPurify.isSupported) {
      return dirty;
    }
    /* Assign config vars */
    if (!SET_CONFIG) {
      _parseConfig(cfg);
    }
    /* Clean up removed elements */
    DOMPurify.removed = [];
    /* Check if dirty is correctly typed for IN_PLACE */
    if (typeof dirty === 'string') {
      IN_PLACE = false;
    }
    if (IN_PLACE) {
      /* Do some early pre-sanitization to avoid unsafe root nodes */
      if (dirty.nodeName) {
        const tagName = transformCaseFunc(dirty.nodeName);
        if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
          throw typeErrorCreate('root node is forbidden and cannot be sanitized in-place');
        }
      }
    } else if (dirty instanceof Node) {
      /* If dirty is a DOM element, append to an empty document to avoid
         elements being stripped by the parser */
      body = _initDocument('<!---->');
      importedNode = body.ownerDocument.importNode(dirty, true);
      if (importedNode.nodeType === NODE_TYPE.element && importedNode.nodeName === 'BODY') {
        /* Node is already a body, use as is */
        body = importedNode;
      } else if (importedNode.nodeName === 'HTML') {
        body = importedNode;
      } else {
        // eslint-disable-next-line unicorn/prefer-dom-node-append
        body.appendChild(importedNode);
      }
    } else {
      /* Exit directly if we have nothing to do */
      if (!RETURN_DOM && !SAFE_FOR_TEMPLATES && !WHOLE_DOCUMENT &&
      // eslint-disable-next-line unicorn/prefer-includes
      dirty.indexOf('<') === -1) {
        return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(dirty) : dirty;
      }
      /* Initialize the document to work on */
      body = _initDocument(dirty);
      /* Check we have a DOM node from the data */
      if (!body) {
        return RETURN_DOM ? null : RETURN_TRUSTED_TYPE ? emptyHTML : '';
      }
    }
    /* Remove first element node (ours) if FORCE_BODY is set */
    if (body && FORCE_BODY) {
      _forceRemove(body.firstChild);
    }
    /* Get node iterator */
    const nodeIterator = _createNodeIterator(IN_PLACE ? dirty : body);
    /* Now start iterating over the created document */
    while (currentNode = nodeIterator.nextNode()) {
      /* Sanitize tags and elements */
      _sanitizeElements(currentNode);
      /* Check attributes next */
      _sanitizeAttributes(currentNode);
      /* Shadow DOM detected, sanitize it */
      if (currentNode.content instanceof DocumentFragment) {
        _sanitizeShadowDOM(currentNode.content);
      }
    }
    /* If we sanitized `dirty` in-place, return it. */
    if (IN_PLACE) {
      return dirty;
    }
    /* Return sanitized string or DOM */
    if (RETURN_DOM) {
      if (RETURN_DOM_FRAGMENT) {
        returnNode = createDocumentFragment.call(body.ownerDocument);
        while (body.firstChild) {
          // eslint-disable-next-line unicorn/prefer-dom-node-append
          returnNode.appendChild(body.firstChild);
        }
      } else {
        returnNode = body;
      }
      if (ALLOWED_ATTR.shadowroot || ALLOWED_ATTR.shadowrootmode) {
        /*
          AdoptNode() is not used because internal state is not reset
          (e.g. the past names map of a HTMLFormElement), this is safe
          in theory but we would rather not risk another attack vector.
          The state that is cloned by importNode() is explicitly defined
          by the specs.
        */
        returnNode = importNode.call(originalDocument, returnNode, true);
      }
      return returnNode;
    }
    let serializedHTML = WHOLE_DOCUMENT ? body.outerHTML : body.innerHTML;
    /* Serialize doctype if allowed */
    if (WHOLE_DOCUMENT && ALLOWED_TAGS['!doctype'] && body.ownerDocument && body.ownerDocument.doctype && body.ownerDocument.doctype.name && regExpTest(DOCTYPE_NAME, body.ownerDocument.doctype.name)) {
      serializedHTML = '<!DOCTYPE ' + body.ownerDocument.doctype.name + '>\n' + serializedHTML;
    }
    /* Sanitize final string template-safe */
    if (SAFE_FOR_TEMPLATES) {
      arrayForEach([MUSTACHE_EXPR, ERB_EXPR, TMPLIT_EXPR], expr => {
        serializedHTML = stringReplace(serializedHTML, expr, ' ');
      });
    }
    return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(serializedHTML) : serializedHTML;
  };
  DOMPurify.setConfig = function () {
    let cfg = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    _parseConfig(cfg);
    SET_CONFIG = true;
  };
  DOMPurify.clearConfig = function () {
    CONFIG = null;
    SET_CONFIG = false;
  };
  DOMPurify.isValidAttribute = function (tag, attr, value) {
    /* Initialize shared config vars if necessary. */
    if (!CONFIG) {
      _parseConfig({});
    }
    const lcTag = transformCaseFunc(tag);
    const lcName = transformCaseFunc(attr);
    return _isValidAttribute(lcTag, lcName, value);
  };
  DOMPurify.addHook = function (entryPoint, hookFunction) {
    if (typeof hookFunction !== 'function') {
      return;
    }
    arrayPush(hooks[entryPoint], hookFunction);
  };
  DOMPurify.removeHook = function (entryPoint, hookFunction) {
    if (hookFunction !== undefined) {
      const index = arrayLastIndexOf(hooks[entryPoint], hookFunction);
      return index === -1 ? undefined : arraySplice(hooks[entryPoint], index, 1)[0];
    }
    return arrayPop(hooks[entryPoint]);
  };
  DOMPurify.removeHooks = function (entryPoint) {
    hooks[entryPoint] = [];
  };
  DOMPurify.removeAllHooks = function () {
    hooks = _createHooksMap();
  };
  return DOMPurify;
}
var purify = createDOMPurify();


//# sourceMappingURL=purify.es.mjs.map


/***/ }),

/***/ "./node_modules/react-dom/client.js":
/*!******************************************!*\
  !*** ./node_modules/react-dom/client.js ***!
  \******************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {



var m = __webpack_require__(/*! react-dom */ "react-dom");
if (false) // removed by dead control flow
{} else {
  var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  exports.createRoot = function(c, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.createRoot(c, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
  exports.hydrateRoot = function(c, h, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.hydrateRoot(c, h, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
}


/***/ }),

/***/ "./node_modules/react/cjs/react-jsx-runtime.development.js":
/*!*****************************************************************!*\
  !*** ./node_modules/react/cjs/react-jsx-runtime.development.js ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



if (true) {
  (function() {
'use strict';

var React = __webpack_require__(/*! react */ "react");

// ATTENTION
// When adding new symbols to this file,
// Please consider also adding to 'react-devtools-shared/src/backend/ReactSymbols'
// The Symbol used to tag the ReactElement-like types.
var REACT_ELEMENT_TYPE = Symbol.for('react.element');
var REACT_PORTAL_TYPE = Symbol.for('react.portal');
var REACT_FRAGMENT_TYPE = Symbol.for('react.fragment');
var REACT_STRICT_MODE_TYPE = Symbol.for('react.strict_mode');
var REACT_PROFILER_TYPE = Symbol.for('react.profiler');
var REACT_PROVIDER_TYPE = Symbol.for('react.provider');
var REACT_CONTEXT_TYPE = Symbol.for('react.context');
var REACT_FORWARD_REF_TYPE = Symbol.for('react.forward_ref');
var REACT_SUSPENSE_TYPE = Symbol.for('react.suspense');
var REACT_SUSPENSE_LIST_TYPE = Symbol.for('react.suspense_list');
var REACT_MEMO_TYPE = Symbol.for('react.memo');
var REACT_LAZY_TYPE = Symbol.for('react.lazy');
var REACT_OFFSCREEN_TYPE = Symbol.for('react.offscreen');
var MAYBE_ITERATOR_SYMBOL = Symbol.iterator;
var FAUX_ITERATOR_SYMBOL = '@@iterator';
function getIteratorFn(maybeIterable) {
  if (maybeIterable === null || typeof maybeIterable !== 'object') {
    return null;
  }

  var maybeIterator = MAYBE_ITERATOR_SYMBOL && maybeIterable[MAYBE_ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL];

  if (typeof maybeIterator === 'function') {
    return maybeIterator;
  }

  return null;
}

var ReactSharedInternals = React.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;

function error(format) {
  {
    {
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }

      printWarning('error', format, args);
    }
  }
}

function printWarning(level, format, args) {
  // When changing this logic, you might want to also
  // update consoleWithStackDev.www.js as well.
  {
    var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;
    var stack = ReactDebugCurrentFrame.getStackAddendum();

    if (stack !== '') {
      format += '%s';
      args = args.concat([stack]);
    } // eslint-disable-next-line react-internal/safe-string-coercion


    var argsWithFormat = args.map(function (item) {
      return String(item);
    }); // Careful: RN currently depends on this prefix

    argsWithFormat.unshift('Warning: ' + format); // We intentionally don't use spread (or .apply) directly because it
    // breaks IE9: https://github.com/facebook/react/issues/13610
    // eslint-disable-next-line react-internal/no-production-logging

    Function.prototype.apply.call(console[level], console, argsWithFormat);
  }
}

// -----------------------------------------------------------------------------

var enableScopeAPI = false; // Experimental Create Event Handle API.
var enableCacheElement = false;
var enableTransitionTracing = false; // No known bugs, but needs performance testing

var enableLegacyHidden = false; // Enables unstable_avoidThisFallback feature in Fiber
// stuff. Intended to enable React core members to more easily debug scheduling
// issues in DEV builds.

var enableDebugTracing = false; // Track which Fiber(s) schedule render work.

var REACT_MODULE_REFERENCE;

{
  REACT_MODULE_REFERENCE = Symbol.for('react.module.reference');
}

function isValidElementType(type) {
  if (typeof type === 'string' || typeof type === 'function') {
    return true;
  } // Note: typeof might be other than 'symbol' or 'number' (e.g. if it's a polyfill).


  if (type === REACT_FRAGMENT_TYPE || type === REACT_PROFILER_TYPE || enableDebugTracing  || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || enableLegacyHidden  || type === REACT_OFFSCREEN_TYPE || enableScopeAPI  || enableCacheElement  || enableTransitionTracing ) {
    return true;
  }

  if (typeof type === 'object' && type !== null) {
    if (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || // This needs to include all possible module reference object
    // types supported by any Flight configuration anywhere since
    // we don't know which Flight build this will end up being used
    // with.
    type.$$typeof === REACT_MODULE_REFERENCE || type.getModuleId !== undefined) {
      return true;
    }
  }

  return false;
}

function getWrappedName(outerType, innerType, wrapperName) {
  var displayName = outerType.displayName;

  if (displayName) {
    return displayName;
  }

  var functionName = innerType.displayName || innerType.name || '';
  return functionName !== '' ? wrapperName + "(" + functionName + ")" : wrapperName;
} // Keep in sync with react-reconciler/getComponentNameFromFiber


function getContextName(type) {
  return type.displayName || 'Context';
} // Note that the reconciler package should generally prefer to use getComponentNameFromFiber() instead.


function getComponentNameFromType(type) {
  if (type == null) {
    // Host root, text node or just invalid type.
    return null;
  }

  {
    if (typeof type.tag === 'number') {
      error('Received an unexpected object in getComponentNameFromType(). ' + 'This is likely a bug in React. Please file an issue.');
    }
  }

  if (typeof type === 'function') {
    return type.displayName || type.name || null;
  }

  if (typeof type === 'string') {
    return type;
  }

  switch (type) {
    case REACT_FRAGMENT_TYPE:
      return 'Fragment';

    case REACT_PORTAL_TYPE:
      return 'Portal';

    case REACT_PROFILER_TYPE:
      return 'Profiler';

    case REACT_STRICT_MODE_TYPE:
      return 'StrictMode';

    case REACT_SUSPENSE_TYPE:
      return 'Suspense';

    case REACT_SUSPENSE_LIST_TYPE:
      return 'SuspenseList';

  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_CONTEXT_TYPE:
        var context = type;
        return getContextName(context) + '.Consumer';

      case REACT_PROVIDER_TYPE:
        var provider = type;
        return getContextName(provider._context) + '.Provider';

      case REACT_FORWARD_REF_TYPE:
        return getWrappedName(type, type.render, 'ForwardRef');

      case REACT_MEMO_TYPE:
        var outerName = type.displayName || null;

        if (outerName !== null) {
          return outerName;
        }

        return getComponentNameFromType(type.type) || 'Memo';

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            return getComponentNameFromType(init(payload));
          } catch (x) {
            return null;
          }
        }

      // eslint-disable-next-line no-fallthrough
    }
  }

  return null;
}

var assign = Object.assign;

// Helpers to patch console.logs to avoid logging during side-effect free
// replaying on render function. This currently only patches the object
// lazily which won't cover if the log function was extracted eagerly.
// We could also eagerly patch the method.
var disabledDepth = 0;
var prevLog;
var prevInfo;
var prevWarn;
var prevError;
var prevGroup;
var prevGroupCollapsed;
var prevGroupEnd;

function disabledLog() {}

disabledLog.__reactDisabledLog = true;
function disableLogs() {
  {
    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      prevLog = console.log;
      prevInfo = console.info;
      prevWarn = console.warn;
      prevError = console.error;
      prevGroup = console.group;
      prevGroupCollapsed = console.groupCollapsed;
      prevGroupEnd = console.groupEnd; // https://github.com/facebook/react/issues/19099

      var props = {
        configurable: true,
        enumerable: true,
        value: disabledLog,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        info: props,
        log: props,
        warn: props,
        error: props,
        group: props,
        groupCollapsed: props,
        groupEnd: props
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    disabledDepth++;
  }
}
function reenableLogs() {
  {
    disabledDepth--;

    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      var props = {
        configurable: true,
        enumerable: true,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        log: assign({}, props, {
          value: prevLog
        }),
        info: assign({}, props, {
          value: prevInfo
        }),
        warn: assign({}, props, {
          value: prevWarn
        }),
        error: assign({}, props, {
          value: prevError
        }),
        group: assign({}, props, {
          value: prevGroup
        }),
        groupCollapsed: assign({}, props, {
          value: prevGroupCollapsed
        }),
        groupEnd: assign({}, props, {
          value: prevGroupEnd
        })
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    if (disabledDepth < 0) {
      error('disabledDepth fell below zero. ' + 'This is a bug in React. Please file an issue.');
    }
  }
}

var ReactCurrentDispatcher = ReactSharedInternals.ReactCurrentDispatcher;
var prefix;
function describeBuiltInComponentFrame(name, source, ownerFn) {
  {
    if (prefix === undefined) {
      // Extract the VM specific prefix used by each line.
      try {
        throw Error();
      } catch (x) {
        var match = x.stack.trim().match(/\n( *(at )?)/);
        prefix = match && match[1] || '';
      }
    } // We use the prefix to ensure our stacks line up with native stack frames.


    return '\n' + prefix + name;
  }
}
var reentry = false;
var componentFrameCache;

{
  var PossiblyWeakMap = typeof WeakMap === 'function' ? WeakMap : Map;
  componentFrameCache = new PossiblyWeakMap();
}

function describeNativeComponentFrame(fn, construct) {
  // If something asked for a stack inside a fake render, it should get ignored.
  if ( !fn || reentry) {
    return '';
  }

  {
    var frame = componentFrameCache.get(fn);

    if (frame !== undefined) {
      return frame;
    }
  }

  var control;
  reentry = true;
  var previousPrepareStackTrace = Error.prepareStackTrace; // $FlowFixMe It does accept undefined.

  Error.prepareStackTrace = undefined;
  var previousDispatcher;

  {
    previousDispatcher = ReactCurrentDispatcher.current; // Set the dispatcher in DEV because this might be call in the render function
    // for warnings.

    ReactCurrentDispatcher.current = null;
    disableLogs();
  }

  try {
    // This should throw.
    if (construct) {
      // Something should be setting the props in the constructor.
      var Fake = function () {
        throw Error();
      }; // $FlowFixMe


      Object.defineProperty(Fake.prototype, 'props', {
        set: function () {
          // We use a throwing setter instead of frozen or non-writable props
          // because that won't throw in a non-strict mode function.
          throw Error();
        }
      });

      if (typeof Reflect === 'object' && Reflect.construct) {
        // We construct a different control for this case to include any extra
        // frames added by the construct call.
        try {
          Reflect.construct(Fake, []);
        } catch (x) {
          control = x;
        }

        Reflect.construct(fn, [], Fake);
      } else {
        try {
          Fake.call();
        } catch (x) {
          control = x;
        }

        fn.call(Fake.prototype);
      }
    } else {
      try {
        throw Error();
      } catch (x) {
        control = x;
      }

      fn();
    }
  } catch (sample) {
    // This is inlined manually because closure doesn't do it for us.
    if (sample && control && typeof sample.stack === 'string') {
      // This extracts the first frame from the sample that isn't also in the control.
      // Skipping one frame that we assume is the frame that calls the two.
      var sampleLines = sample.stack.split('\n');
      var controlLines = control.stack.split('\n');
      var s = sampleLines.length - 1;
      var c = controlLines.length - 1;

      while (s >= 1 && c >= 0 && sampleLines[s] !== controlLines[c]) {
        // We expect at least one stack frame to be shared.
        // Typically this will be the root most one. However, stack frames may be
        // cut off due to maximum stack limits. In this case, one maybe cut off
        // earlier than the other. We assume that the sample is longer or the same
        // and there for cut off earlier. So we should find the root most frame in
        // the sample somewhere in the control.
        c--;
      }

      for (; s >= 1 && c >= 0; s--, c--) {
        // Next we find the first one that isn't the same which should be the
        // frame that called our sample function and the control.
        if (sampleLines[s] !== controlLines[c]) {
          // In V8, the first line is describing the message but other VMs don't.
          // If we're about to return the first line, and the control is also on the same
          // line, that's a pretty good indicator that our sample threw at same line as
          // the control. I.e. before we entered the sample frame. So we ignore this result.
          // This can happen if you passed a class to function component, or non-function.
          if (s !== 1 || c !== 1) {
            do {
              s--;
              c--; // We may still have similar intermediate frames from the construct call.
              // The next one that isn't the same should be our match though.

              if (c < 0 || sampleLines[s] !== controlLines[c]) {
                // V8 adds a "new" prefix for native classes. Let's remove it to make it prettier.
                var _frame = '\n' + sampleLines[s].replace(' at new ', ' at '); // If our component frame is labeled "<anonymous>"
                // but we have a user-provided "displayName"
                // splice it in to make the stack more readable.


                if (fn.displayName && _frame.includes('<anonymous>')) {
                  _frame = _frame.replace('<anonymous>', fn.displayName);
                }

                {
                  if (typeof fn === 'function') {
                    componentFrameCache.set(fn, _frame);
                  }
                } // Return the line we found.


                return _frame;
              }
            } while (s >= 1 && c >= 0);
          }

          break;
        }
      }
    }
  } finally {
    reentry = false;

    {
      ReactCurrentDispatcher.current = previousDispatcher;
      reenableLogs();
    }

    Error.prepareStackTrace = previousPrepareStackTrace;
  } // Fallback to just using the name if we couldn't make it throw.


  var name = fn ? fn.displayName || fn.name : '';
  var syntheticFrame = name ? describeBuiltInComponentFrame(name) : '';

  {
    if (typeof fn === 'function') {
      componentFrameCache.set(fn, syntheticFrame);
    }
  }

  return syntheticFrame;
}
function describeFunctionComponentFrame(fn, source, ownerFn) {
  {
    return describeNativeComponentFrame(fn, false);
  }
}

function shouldConstruct(Component) {
  var prototype = Component.prototype;
  return !!(prototype && prototype.isReactComponent);
}

function describeUnknownElementTypeFrameInDEV(type, source, ownerFn) {

  if (type == null) {
    return '';
  }

  if (typeof type === 'function') {
    {
      return describeNativeComponentFrame(type, shouldConstruct(type));
    }
  }

  if (typeof type === 'string') {
    return describeBuiltInComponentFrame(type);
  }

  switch (type) {
    case REACT_SUSPENSE_TYPE:
      return describeBuiltInComponentFrame('Suspense');

    case REACT_SUSPENSE_LIST_TYPE:
      return describeBuiltInComponentFrame('SuspenseList');
  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_FORWARD_REF_TYPE:
        return describeFunctionComponentFrame(type.render);

      case REACT_MEMO_TYPE:
        // Memo may contain any component type so we recursively resolve it.
        return describeUnknownElementTypeFrameInDEV(type.type, source, ownerFn);

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            // Lazy may contain any component type so we recursively resolve it.
            return describeUnknownElementTypeFrameInDEV(init(payload), source, ownerFn);
          } catch (x) {}
        }
    }
  }

  return '';
}

var hasOwnProperty = Object.prototype.hasOwnProperty;

var loggedTypeFailures = {};
var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame.setExtraStackFrame(null);
    }
  }
}

function checkPropTypes(typeSpecs, values, location, componentName, element) {
  {
    // $FlowFixMe This is okay but Flow doesn't know it.
    var has = Function.call.bind(hasOwnProperty);

    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error$1 = void 0; // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.

        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            // eslint-disable-next-line react-internal/prod-error-codes
            var err = Error((componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' + 'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' + 'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.');
            err.name = 'Invariant Violation';
            throw err;
          }

          error$1 = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED');
        } catch (ex) {
          error$1 = ex;
        }

        if (error$1 && !(error$1 instanceof Error)) {
          setCurrentlyValidatingElement(element);

          error('%s: type specification of %s' + ' `%s` is invalid; the type checker ' + 'function must return `null` or an `Error` but returned a %s. ' + 'You may have forgotten to pass an argument to the type checker ' + 'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' + 'shape all require an argument).', componentName || 'React class', location, typeSpecName, typeof error$1);

          setCurrentlyValidatingElement(null);
        }

        if (error$1 instanceof Error && !(error$1.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error$1.message] = true;
          setCurrentlyValidatingElement(element);

          error('Failed %s type: %s', location, error$1.message);

          setCurrentlyValidatingElement(null);
        }
      }
    }
  }
}

var isArrayImpl = Array.isArray; // eslint-disable-next-line no-redeclare

function isArray(a) {
  return isArrayImpl(a);
}

/*
 * The `'' + value` pattern (used in in perf-sensitive code) throws for Symbol
 * and Temporal.* types. See https://github.com/facebook/react/pull/22064.
 *
 * The functions in this module will throw an easier-to-understand,
 * easier-to-debug exception with a clear errors message message explaining the
 * problem. (Instead of a confusing exception thrown inside the implementation
 * of the `value` object).
 */
// $FlowFixMe only called in DEV, so void return is not possible.
function typeName(value) {
  {
    // toStringTag is needed for namespaced types like Temporal.Instant
    var hasToStringTag = typeof Symbol === 'function' && Symbol.toStringTag;
    var type = hasToStringTag && value[Symbol.toStringTag] || value.constructor.name || 'Object';
    return type;
  }
} // $FlowFixMe only called in DEV, so void return is not possible.


function willCoercionThrow(value) {
  {
    try {
      testStringCoercion(value);
      return false;
    } catch (e) {
      return true;
    }
  }
}

function testStringCoercion(value) {
  // If you ended up here by following an exception call stack, here's what's
  // happened: you supplied an object or symbol value to React (as a prop, key,
  // DOM attribute, CSS property, string ref, etc.) and when React tried to
  // coerce it to a string using `'' + value`, an exception was thrown.
  //
  // The most common types that will cause this exception are `Symbol` instances
  // and Temporal objects like `Temporal.Instant`. But any object that has a
  // `valueOf` or `[Symbol.toPrimitive]` method that throws will also cause this
  // exception. (Library authors do this to prevent users from using built-in
  // numeric operators like `+` or comparison operators like `>=` because custom
  // methods are needed to perform accurate arithmetic or comparison.)
  //
  // To fix the problem, coerce this object or symbol value to a string before
  // passing it to React. The most reliable way is usually `String(value)`.
  //
  // To find which value is throwing, check the browser or debugger console.
  // Before this exception was thrown, there should be `console.error` output
  // that shows the type (Symbol, Temporal.PlainDate, etc.) that caused the
  // problem and how that type was used: key, atrribute, input value prop, etc.
  // In most cases, this console output also shows the component and its
  // ancestor components where the exception happened.
  //
  // eslint-disable-next-line react-internal/safe-string-coercion
  return '' + value;
}
function checkKeyStringCoercion(value) {
  {
    if (willCoercionThrow(value)) {
      error('The provided key is an unsupported type %s.' + ' This value must be coerced to a string before before using it here.', typeName(value));

      return testStringCoercion(value); // throw (to help callers find troubleshooting comments)
    }
  }
}

var ReactCurrentOwner = ReactSharedInternals.ReactCurrentOwner;
var RESERVED_PROPS = {
  key: true,
  ref: true,
  __self: true,
  __source: true
};
var specialPropKeyWarningShown;
var specialPropRefWarningShown;
var didWarnAboutStringRefs;

{
  didWarnAboutStringRefs = {};
}

function hasValidRef(config) {
  {
    if (hasOwnProperty.call(config, 'ref')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'ref').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.ref !== undefined;
}

function hasValidKey(config) {
  {
    if (hasOwnProperty.call(config, 'key')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'key').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.key !== undefined;
}

function warnIfStringRefCannotBeAutoConverted(config, self) {
  {
    if (typeof config.ref === 'string' && ReactCurrentOwner.current && self && ReactCurrentOwner.current.stateNode !== self) {
      var componentName = getComponentNameFromType(ReactCurrentOwner.current.type);

      if (!didWarnAboutStringRefs[componentName]) {
        error('Component "%s" contains the string ref "%s". ' + 'Support for string refs will be removed in a future major release. ' + 'This case cannot be automatically converted to an arrow function. ' + 'We ask you to manually fix this case by using useRef() or createRef() instead. ' + 'Learn more about using refs safely here: ' + 'https://reactjs.org/link/strict-mode-string-ref', getComponentNameFromType(ReactCurrentOwner.current.type), config.ref);

        didWarnAboutStringRefs[componentName] = true;
      }
    }
  }
}

function defineKeyPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingKey = function () {
      if (!specialPropKeyWarningShown) {
        specialPropKeyWarningShown = true;

        error('%s: `key` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingKey.isReactWarning = true;
    Object.defineProperty(props, 'key', {
      get: warnAboutAccessingKey,
      configurable: true
    });
  }
}

function defineRefPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingRef = function () {
      if (!specialPropRefWarningShown) {
        specialPropRefWarningShown = true;

        error('%s: `ref` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingRef.isReactWarning = true;
    Object.defineProperty(props, 'ref', {
      get: warnAboutAccessingRef,
      configurable: true
    });
  }
}
/**
 * Factory method to create a new React element. This no longer adheres to
 * the class pattern, so do not use new to call it. Also, instanceof check
 * will not work. Instead test $$typeof field against Symbol.for('react.element') to check
 * if something is a React Element.
 *
 * @param {*} type
 * @param {*} props
 * @param {*} key
 * @param {string|object} ref
 * @param {*} owner
 * @param {*} self A *temporary* helper to detect places where `this` is
 * different from the `owner` when React.createElement is called, so that we
 * can warn. We want to get rid of owner and replace string `ref`s with arrow
 * functions, and as long as `this` and owner are the same, there will be no
 * change in behavior.
 * @param {*} source An annotation object (added by a transpiler or otherwise)
 * indicating filename, line number, and/or other information.
 * @internal
 */


var ReactElement = function (type, key, ref, self, source, owner, props) {
  var element = {
    // This tag allows us to uniquely identify this as a React Element
    $$typeof: REACT_ELEMENT_TYPE,
    // Built-in properties that belong on the element
    type: type,
    key: key,
    ref: ref,
    props: props,
    // Record the component responsible for creating this element.
    _owner: owner
  };

  {
    // The validation flag is currently mutative. We put it on
    // an external backing store so that we can freeze the whole object.
    // This can be replaced with a WeakMap once they are implemented in
    // commonly used development environments.
    element._store = {}; // To make comparing ReactElements easier for testing purposes, we make
    // the validation flag non-enumerable (where possible, which should
    // include every environment we run tests in), so the test framework
    // ignores it.

    Object.defineProperty(element._store, 'validated', {
      configurable: false,
      enumerable: false,
      writable: true,
      value: false
    }); // self and source are DEV only properties.

    Object.defineProperty(element, '_self', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: self
    }); // Two elements created in two different places should be considered
    // equal for testing purposes and therefore we hide it from enumeration.

    Object.defineProperty(element, '_source', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: source
    });

    if (Object.freeze) {
      Object.freeze(element.props);
      Object.freeze(element);
    }
  }

  return element;
};
/**
 * https://github.com/reactjs/rfcs/pull/107
 * @param {*} type
 * @param {object} props
 * @param {string} key
 */

function jsxDEV(type, config, maybeKey, source, self) {
  {
    var propName; // Reserved names are extracted

    var props = {};
    var key = null;
    var ref = null; // Currently, key can be spread in as a prop. This causes a potential
    // issue if key is also explicitly declared (ie. <div {...props} key="Hi" />
    // or <div key="Hi" {...props} /> ). We want to deprecate key spread,
    // but as an intermediary step, we will use jsxDEV for everything except
    // <div {...props} key="Hi" />, because we aren't currently able to tell if
    // key is explicitly declared to be undefined or not.

    if (maybeKey !== undefined) {
      {
        checkKeyStringCoercion(maybeKey);
      }

      key = '' + maybeKey;
    }

    if (hasValidKey(config)) {
      {
        checkKeyStringCoercion(config.key);
      }

      key = '' + config.key;
    }

    if (hasValidRef(config)) {
      ref = config.ref;
      warnIfStringRefCannotBeAutoConverted(config, self);
    } // Remaining properties are added to a new props object


    for (propName in config) {
      if (hasOwnProperty.call(config, propName) && !RESERVED_PROPS.hasOwnProperty(propName)) {
        props[propName] = config[propName];
      }
    } // Resolve default props


    if (type && type.defaultProps) {
      var defaultProps = type.defaultProps;

      for (propName in defaultProps) {
        if (props[propName] === undefined) {
          props[propName] = defaultProps[propName];
        }
      }
    }

    if (key || ref) {
      var displayName = typeof type === 'function' ? type.displayName || type.name || 'Unknown' : type;

      if (key) {
        defineKeyPropWarningGetter(props, displayName);
      }

      if (ref) {
        defineRefPropWarningGetter(props, displayName);
      }
    }

    return ReactElement(type, key, ref, self, source, ReactCurrentOwner.current, props);
  }
}

var ReactCurrentOwner$1 = ReactSharedInternals.ReactCurrentOwner;
var ReactDebugCurrentFrame$1 = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement$1(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame$1.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame$1.setExtraStackFrame(null);
    }
  }
}

var propTypesMisspellWarningShown;

{
  propTypesMisspellWarningShown = false;
}
/**
 * Verifies the object is a ReactElement.
 * See https://reactjs.org/docs/react-api.html#isvalidelement
 * @param {?object} object
 * @return {boolean} True if `object` is a ReactElement.
 * @final
 */


function isValidElement(object) {
  {
    return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
  }
}

function getDeclarationErrorAddendum() {
  {
    if (ReactCurrentOwner$1.current) {
      var name = getComponentNameFromType(ReactCurrentOwner$1.current.type);

      if (name) {
        return '\n\nCheck the render method of `' + name + '`.';
      }
    }

    return '';
  }
}

function getSourceInfoErrorAddendum(source) {
  {
    if (source !== undefined) {
      var fileName = source.fileName.replace(/^.*[\\\/]/, '');
      var lineNumber = source.lineNumber;
      return '\n\nCheck your code at ' + fileName + ':' + lineNumber + '.';
    }

    return '';
  }
}
/**
 * Warn if there's no key explicitly set on dynamic arrays of children or
 * object keys are not valid. This allows us to keep track of children between
 * updates.
 */


var ownerHasKeyUseWarning = {};

function getCurrentComponentErrorInfo(parentType) {
  {
    var info = getDeclarationErrorAddendum();

    if (!info) {
      var parentName = typeof parentType === 'string' ? parentType : parentType.displayName || parentType.name;

      if (parentName) {
        info = "\n\nCheck the top-level render call using <" + parentName + ">.";
      }
    }

    return info;
  }
}
/**
 * Warn if the element doesn't have an explicit key assigned to it.
 * This element is in an array. The array could grow and shrink or be
 * reordered. All children that haven't already been validated are required to
 * have a "key" property assigned to it. Error statuses are cached so a warning
 * will only be shown once.
 *
 * @internal
 * @param {ReactElement} element Element that requires a key.
 * @param {*} parentType element's parent's type.
 */


function validateExplicitKey(element, parentType) {
  {
    if (!element._store || element._store.validated || element.key != null) {
      return;
    }

    element._store.validated = true;
    var currentComponentErrorInfo = getCurrentComponentErrorInfo(parentType);

    if (ownerHasKeyUseWarning[currentComponentErrorInfo]) {
      return;
    }

    ownerHasKeyUseWarning[currentComponentErrorInfo] = true; // Usually the current owner is the offender, but if it accepts children as a
    // property, it may be the creator of the child that's responsible for
    // assigning it a key.

    var childOwner = '';

    if (element && element._owner && element._owner !== ReactCurrentOwner$1.current) {
      // Give the component that originally created this child.
      childOwner = " It was passed a child from " + getComponentNameFromType(element._owner.type) + ".";
    }

    setCurrentlyValidatingElement$1(element);

    error('Each child in a list should have a unique "key" prop.' + '%s%s See https://reactjs.org/link/warning-keys for more information.', currentComponentErrorInfo, childOwner);

    setCurrentlyValidatingElement$1(null);
  }
}
/**
 * Ensure that every element either is passed in a static location, in an
 * array with an explicit keys property defined, or in an object literal
 * with valid key property.
 *
 * @internal
 * @param {ReactNode} node Statically passed child of any type.
 * @param {*} parentType node's parent's type.
 */


function validateChildKeys(node, parentType) {
  {
    if (typeof node !== 'object') {
      return;
    }

    if (isArray(node)) {
      for (var i = 0; i < node.length; i++) {
        var child = node[i];

        if (isValidElement(child)) {
          validateExplicitKey(child, parentType);
        }
      }
    } else if (isValidElement(node)) {
      // This element was passed in a valid location.
      if (node._store) {
        node._store.validated = true;
      }
    } else if (node) {
      var iteratorFn = getIteratorFn(node);

      if (typeof iteratorFn === 'function') {
        // Entry iterators used to provide implicit keys,
        // but now we print a separate warning for them later.
        if (iteratorFn !== node.entries) {
          var iterator = iteratorFn.call(node);
          var step;

          while (!(step = iterator.next()).done) {
            if (isValidElement(step.value)) {
              validateExplicitKey(step.value, parentType);
            }
          }
        }
      }
    }
  }
}
/**
 * Given an element, validate that its props follow the propTypes definition,
 * provided by the type.
 *
 * @param {ReactElement} element
 */


function validatePropTypes(element) {
  {
    var type = element.type;

    if (type === null || type === undefined || typeof type === 'string') {
      return;
    }

    var propTypes;

    if (typeof type === 'function') {
      propTypes = type.propTypes;
    } else if (typeof type === 'object' && (type.$$typeof === REACT_FORWARD_REF_TYPE || // Note: Memo only checks outer props here.
    // Inner props are checked in the reconciler.
    type.$$typeof === REACT_MEMO_TYPE)) {
      propTypes = type.propTypes;
    } else {
      return;
    }

    if (propTypes) {
      // Intentionally inside to avoid triggering lazy initializers:
      var name = getComponentNameFromType(type);
      checkPropTypes(propTypes, element.props, 'prop', name, element);
    } else if (type.PropTypes !== undefined && !propTypesMisspellWarningShown) {
      propTypesMisspellWarningShown = true; // Intentionally inside to avoid triggering lazy initializers:

      var _name = getComponentNameFromType(type);

      error('Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?', _name || 'Unknown');
    }

    if (typeof type.getDefaultProps === 'function' && !type.getDefaultProps.isReactClassApproved) {
      error('getDefaultProps is only used on classic React.createClass ' + 'definitions. Use a static property named `defaultProps` instead.');
    }
  }
}
/**
 * Given a fragment, validate that it can only be provided with fragment props
 * @param {ReactElement} fragment
 */


function validateFragmentProps(fragment) {
  {
    var keys = Object.keys(fragment.props);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];

      if (key !== 'children' && key !== 'key') {
        setCurrentlyValidatingElement$1(fragment);

        error('Invalid prop `%s` supplied to `React.Fragment`. ' + 'React.Fragment can only have `key` and `children` props.', key);

        setCurrentlyValidatingElement$1(null);
        break;
      }
    }

    if (fragment.ref !== null) {
      setCurrentlyValidatingElement$1(fragment);

      error('Invalid attribute `ref` supplied to `React.Fragment`.');

      setCurrentlyValidatingElement$1(null);
    }
  }
}

var didWarnAboutKeySpread = {};
function jsxWithValidation(type, props, key, isStaticChildren, source, self) {
  {
    var validType = isValidElementType(type); // We warn in this case but don't throw. We expect the element creation to
    // succeed and there will likely be errors in render.

    if (!validType) {
      var info = '';

      if (type === undefined || typeof type === 'object' && type !== null && Object.keys(type).length === 0) {
        info += ' You likely forgot to export your component from the file ' + "it's defined in, or you might have mixed up default and named imports.";
      }

      var sourceInfo = getSourceInfoErrorAddendum(source);

      if (sourceInfo) {
        info += sourceInfo;
      } else {
        info += getDeclarationErrorAddendum();
      }

      var typeString;

      if (type === null) {
        typeString = 'null';
      } else if (isArray(type)) {
        typeString = 'array';
      } else if (type !== undefined && type.$$typeof === REACT_ELEMENT_TYPE) {
        typeString = "<" + (getComponentNameFromType(type.type) || 'Unknown') + " />";
        info = ' Did you accidentally export a JSX literal instead of a component?';
      } else {
        typeString = typeof type;
      }

      error('React.jsx: type is invalid -- expected a string (for ' + 'built-in components) or a class/function (for composite ' + 'components) but got: %s.%s', typeString, info);
    }

    var element = jsxDEV(type, props, key, source, self); // The result can be nullish if a mock or a custom function is used.
    // TODO: Drop this when these are no longer allowed as the type argument.

    if (element == null) {
      return element;
    } // Skip key warning if the type isn't valid since our key validation logic
    // doesn't expect a non-string/function type and can throw confusing errors.
    // We don't want exception behavior to differ between dev and prod.
    // (Rendering will throw with a helpful message and as soon as the type is
    // fixed, the key warnings will appear.)


    if (validType) {
      var children = props.children;

      if (children !== undefined) {
        if (isStaticChildren) {
          if (isArray(children)) {
            for (var i = 0; i < children.length; i++) {
              validateChildKeys(children[i], type);
            }

            if (Object.freeze) {
              Object.freeze(children);
            }
          } else {
            error('React.jsx: Static children should always be an array. ' + 'You are likely explicitly calling React.jsxs or React.jsxDEV. ' + 'Use the Babel transform instead.');
          }
        } else {
          validateChildKeys(children, type);
        }
      }
    }

    {
      if (hasOwnProperty.call(props, 'key')) {
        var componentName = getComponentNameFromType(type);
        var keys = Object.keys(props).filter(function (k) {
          return k !== 'key';
        });
        var beforeExample = keys.length > 0 ? '{key: someKey, ' + keys.join(': ..., ') + ': ...}' : '{key: someKey}';

        if (!didWarnAboutKeySpread[componentName + beforeExample]) {
          var afterExample = keys.length > 0 ? '{' + keys.join(': ..., ') + ': ...}' : '{}';

          error('A props object containing a "key" prop is being spread into JSX:\n' + '  let props = %s;\n' + '  <%s {...props} />\n' + 'React keys must be passed directly to JSX without using spread:\n' + '  let props = %s;\n' + '  <%s key={someKey} {...props} />', beforeExample, componentName, afterExample, componentName);

          didWarnAboutKeySpread[componentName + beforeExample] = true;
        }
      }
    }

    if (type === REACT_FRAGMENT_TYPE) {
      validateFragmentProps(element);
    } else {
      validatePropTypes(element);
    }

    return element;
  }
} // These two functions exist to still get child warnings in dev
// even with the prod transform. This means that jsxDEV is purely
// opt-in behavior for better messages but that we won't stop
// giving you warnings if you use production apis.

function jsxWithValidationStatic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, true);
  }
}
function jsxWithValidationDynamic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, false);
  }
}

var jsx =  jsxWithValidationDynamic ; // we may want to special case jsxs internally to take advantage of static children.
// for now we can ship identical prod functions

var jsxs =  jsxWithValidationStatic ;

exports.Fragment = REACT_FRAGMENT_TYPE;
exports.jsx = jsx;
exports.jsxs = jsxs;
  })();
}


/***/ }),

/***/ "./node_modules/react/jsx-runtime.js":
/*!*******************************************!*\
  !*** ./node_modules/react/jsx-runtime.js ***!
  \*******************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./cjs/react-jsx-runtime.development.js */ "./node_modules/react/cjs/react-jsx-runtime.development.js");
}


/***/ }),

/***/ "./node_modules/tabbable/dist/index.esm.js":
/*!*************************************************!*\
  !*** ./node_modules/tabbable/dist/index.esm.js ***!
  \*************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   focusable: function() { return /* binding */ focusable; },
/* harmony export */   getTabIndex: function() { return /* binding */ getTabIndex; },
/* harmony export */   isFocusable: function() { return /* binding */ isFocusable; },
/* harmony export */   isTabbable: function() { return /* binding */ isTabbable; },
/* harmony export */   tabbable: function() { return /* binding */ tabbable; }
/* harmony export */ });
/*!
* tabbable 6.4.0
* @license MIT, https://github.com/focus-trap/tabbable/blob/master/LICENSE
*/
// NOTE: separate `:not()` selectors has broader browser support than the newer
//  `:not([inert], [inert] *)` (Feb 2023)
var candidateSelectors = ['input:not([inert]):not([inert] *)', 'select:not([inert]):not([inert] *)', 'textarea:not([inert]):not([inert] *)', 'a[href]:not([inert]):not([inert] *)', 'button:not([inert]):not([inert] *)', '[tabindex]:not(slot):not([inert]):not([inert] *)', 'audio[controls]:not([inert]):not([inert] *)', 'video[controls]:not([inert]):not([inert] *)', '[contenteditable]:not([contenteditable="false"]):not([inert]):not([inert] *)', 'details>summary:first-of-type:not([inert]):not([inert] *)', 'details:not([inert]):not([inert] *)'];
var candidateSelector = /* #__PURE__ */candidateSelectors.join(',');
var NoElement = typeof Element === 'undefined';
var matches = NoElement ? function () {} : Element.prototype.matches || Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
var getRootNode = !NoElement && Element.prototype.getRootNode ? function (element) {
  var _element$getRootNode;
  return element === null || element === void 0 ? void 0 : (_element$getRootNode = element.getRootNode) === null || _element$getRootNode === void 0 ? void 0 : _element$getRootNode.call(element);
} : function (element) {
  return element === null || element === void 0 ? void 0 : element.ownerDocument;
};

/**
 * Determines if a node is inert or in an inert ancestor.
 * @param {Node} [node]
 * @param {boolean} [lookUp] If true and `node` is not inert, looks up at ancestors to
 *  see if any of them are inert. If false, only `node` itself is considered.
 * @returns {boolean} True if inert itself or by way of being in an inert ancestor.
 *  False if `node` is falsy.
 */
var _isInert = function isInert(node, lookUp) {
  var _node$getAttribute;
  if (lookUp === void 0) {
    lookUp = true;
  }
  // CAREFUL: JSDom does not support inert at all, so we can't use the `HTMLElement.inert`
  //  JS API property; we have to check the attribute, which can either be empty or 'true';
  //  if it's `null` (not specified) or 'false', it's an active element
  var inertAtt = node === null || node === void 0 ? void 0 : (_node$getAttribute = node.getAttribute) === null || _node$getAttribute === void 0 ? void 0 : _node$getAttribute.call(node, 'inert');
  var inert = inertAtt === '' || inertAtt === 'true';

  // NOTE: this could also be handled with `node.matches('[inert], :is([inert] *)')`
  //  if it weren't for `matches()` not being a function on shadow roots; the following
  //  code works for any kind of node
  var result = inert || lookUp && node && (
  // closest does not exist on shadow roots, so we fall back to a manual
  // lookup upward, in case it is not defined.
  typeof node.closest === 'function' ? node.closest('[inert]') : _isInert(node.parentNode));
  return result;
};

/**
 * Determines if a node's content is editable.
 * @param {Element} [node]
 * @returns True if it's content-editable; false if it's not or `node` is falsy.
 */
var isContentEditable = function isContentEditable(node) {
  var _node$getAttribute2;
  // CAREFUL: JSDom does not support the `HTMLElement.isContentEditable` API so we have
  //  to use the attribute directly to check for this, which can either be empty or 'true';
  //  if it's `null` (not specified) or 'false', it's a non-editable element
  var attValue = node === null || node === void 0 ? void 0 : (_node$getAttribute2 = node.getAttribute) === null || _node$getAttribute2 === void 0 ? void 0 : _node$getAttribute2.call(node, 'contenteditable');
  return attValue === '' || attValue === 'true';
};

/**
 * @param {Element} el container to check in
 * @param {boolean} includeContainer add container to check
 * @param {(node: Element) => boolean} filter filter candidates
 * @returns {Element[]}
 */
var getCandidates = function getCandidates(el, includeContainer, filter) {
  // even if `includeContainer=false`, we still have to check it for inertness because
  //  if it's inert (either by itself or via its parent), then all its children are inert
  if (_isInert(el)) {
    return [];
  }
  var candidates = Array.prototype.slice.apply(el.querySelectorAll(candidateSelector));
  if (includeContainer && matches.call(el, candidateSelector)) {
    candidates.unshift(el);
  }
  candidates = candidates.filter(filter);
  return candidates;
};

/**
 * @callback GetShadowRoot
 * @param {Element} element to check for shadow root
 * @returns {ShadowRoot|boolean} ShadowRoot if available or boolean indicating if a shadowRoot is attached but not available.
 */

/**
 * @callback ShadowRootFilter
 * @param {Element} shadowHostNode the element which contains shadow content
 * @returns {boolean} true if a shadow root could potentially contain valid candidates.
 */

/**
 * @typedef {Object} CandidateScope
 * @property {Element} scopeParent contains inner candidates
 * @property {Element[]} candidates list of candidates found in the scope parent
 */

/**
 * @typedef {Object} IterativeOptions
 * @property {GetShadowRoot|boolean} getShadowRoot true if shadow support is enabled; falsy if not;
 *  if a function, implies shadow support is enabled and either returns the shadow root of an element
 *  or a boolean stating if it has an undisclosed shadow root
 * @property {(node: Element) => boolean} filter filter candidates
 * @property {boolean} flatten if true then result will flatten any CandidateScope into the returned list
 * @property {ShadowRootFilter} shadowRootFilter filter shadow roots;
 */

/**
 * @param {Element[]} elements list of element containers to match candidates from
 * @param {boolean} includeContainer add container list to check
 * @param {IterativeOptions} options
 * @returns {Array.<Element|CandidateScope>}
 */
var _getCandidatesIteratively = function getCandidatesIteratively(elements, includeContainer, options) {
  var candidates = [];
  var elementsToCheck = Array.from(elements);
  while (elementsToCheck.length) {
    var element = elementsToCheck.shift();
    if (_isInert(element, false)) {
      // no need to look up since we're drilling down
      // anything inside this container will also be inert
      continue;
    }
    if (element.tagName === 'SLOT') {
      // add shadow dom slot scope (slot itself cannot be focusable)
      var assigned = element.assignedElements();
      var content = assigned.length ? assigned : element.children;
      var nestedCandidates = _getCandidatesIteratively(content, true, options);
      if (options.flatten) {
        candidates.push.apply(candidates, nestedCandidates);
      } else {
        candidates.push({
          scopeParent: element,
          candidates: nestedCandidates
        });
      }
    } else {
      // check candidate element
      var validCandidate = matches.call(element, candidateSelector);
      if (validCandidate && options.filter(element) && (includeContainer || !elements.includes(element))) {
        candidates.push(element);
      }

      // iterate over shadow content if possible
      var shadowRoot = element.shadowRoot ||
      // check for an undisclosed shadow
      typeof options.getShadowRoot === 'function' && options.getShadowRoot(element);

      // no inert look up because we're already drilling down and checking for inertness
      //  on the way down, so all containers to this root node should have already been
      //  vetted as non-inert
      var validShadowRoot = !_isInert(shadowRoot, false) && (!options.shadowRootFilter || options.shadowRootFilter(element));
      if (shadowRoot && validShadowRoot) {
        // add shadow dom scope IIF a shadow root node was given; otherwise, an undisclosed
        //  shadow exists, so look at light dom children as fallback BUT create a scope for any
        //  child candidates found because they're likely slotted elements (elements that are
        //  children of the web component element (which has the shadow), in the light dom, but
        //  slotted somewhere _inside_ the undisclosed shadow) -- the scope is created below,
        //  _after_ we return from this recursive call
        var _nestedCandidates = _getCandidatesIteratively(shadowRoot === true ? element.children : shadowRoot.children, true, options);
        if (options.flatten) {
          candidates.push.apply(candidates, _nestedCandidates);
        } else {
          candidates.push({
            scopeParent: element,
            candidates: _nestedCandidates
          });
        }
      } else {
        // there's not shadow so just dig into the element's (light dom) children
        //  __without__ giving the element special scope treatment
        elementsToCheck.unshift.apply(elementsToCheck, element.children);
      }
    }
  }
  return candidates;
};

/**
 * @private
 * Determines if the node has an explicitly specified `tabindex` attribute.
 * @param {HTMLElement} node
 * @returns {boolean} True if so; false if not.
 */
var hasTabIndex = function hasTabIndex(node) {
  return !isNaN(parseInt(node.getAttribute('tabindex'), 10));
};

/**
 * Determine the tab index of a given node.
 * @param {HTMLElement} node
 * @returns {number} Tab order (negative, 0, or positive number).
 * @throws {Error} If `node` is falsy.
 */
var getTabIndex = function getTabIndex(node) {
  if (!node) {
    throw new Error('No node provided');
  }
  if (node.tabIndex < 0) {
    // in Chrome, <details/>, <audio controls/> and <video controls/> elements get a default
    // `tabIndex` of -1 when the 'tabindex' attribute isn't specified in the DOM,
    // yet they are still part of the regular tab order; in FF, they get a default
    // `tabIndex` of 0; since Chrome still puts those elements in the regular tab
    // order, consider their tab index to be 0.
    // Also browsers do not return `tabIndex` correctly for contentEditable nodes;
    // so if they don't have a tabindex attribute specifically set, assume it's 0.
    if ((/^(AUDIO|VIDEO|DETAILS)$/.test(node.tagName) || isContentEditable(node)) && !hasTabIndex(node)) {
      return 0;
    }
  }
  return node.tabIndex;
};

/**
 * Determine the tab index of a given node __for sort order purposes__.
 * @param {HTMLElement} node
 * @param {boolean} [isScope] True for a custom element with shadow root or slot that, by default,
 *  has tabIndex -1, but needs to be sorted by document order in order for its content to be
 *  inserted into the correct sort position.
 * @returns {number} Tab order (negative, 0, or positive number).
 */
var getSortOrderTabIndex = function getSortOrderTabIndex(node, isScope) {
  var tabIndex = getTabIndex(node);
  if (tabIndex < 0 && isScope && !hasTabIndex(node)) {
    return 0;
  }
  return tabIndex;
};
var sortOrderedTabbables = function sortOrderedTabbables(a, b) {
  return a.tabIndex === b.tabIndex ? a.documentOrder - b.documentOrder : a.tabIndex - b.tabIndex;
};
var isInput = function isInput(node) {
  return node.tagName === 'INPUT';
};
var isHiddenInput = function isHiddenInput(node) {
  return isInput(node) && node.type === 'hidden';
};
var isDetailsWithSummary = function isDetailsWithSummary(node) {
  var r = node.tagName === 'DETAILS' && Array.prototype.slice.apply(node.children).some(function (child) {
    return child.tagName === 'SUMMARY';
  });
  return r;
};
var getCheckedRadio = function getCheckedRadio(nodes, form) {
  for (var i = 0; i < nodes.length; i++) {
    if (nodes[i].checked && nodes[i].form === form) {
      return nodes[i];
    }
  }
};
var isTabbableRadio = function isTabbableRadio(node) {
  if (!node.name) {
    return true;
  }
  var radioScope = node.form || getRootNode(node);
  var queryRadios = function queryRadios(name) {
    return radioScope.querySelectorAll('input[type="radio"][name="' + name + '"]');
  };
  var radioSet;
  if (typeof window !== 'undefined' && typeof window.CSS !== 'undefined' && typeof window.CSS.escape === 'function') {
    radioSet = queryRadios(window.CSS.escape(node.name));
  } else {
    try {
      radioSet = queryRadios(node.name);
    } catch (err) {
      // eslint-disable-next-line no-console
      console.error('Looks like you have a radio button with a name attribute containing invalid CSS selector characters and need the CSS.escape polyfill: %s', err.message);
      return false;
    }
  }
  var checked = getCheckedRadio(radioSet, node.form);
  return !checked || checked === node;
};
var isRadio = function isRadio(node) {
  return isInput(node) && node.type === 'radio';
};
var isNonTabbableRadio = function isNonTabbableRadio(node) {
  return isRadio(node) && !isTabbableRadio(node);
};

// determines if a node is ultimately attached to the window's document
var isNodeAttached = function isNodeAttached(node) {
  var _nodeRoot;
  // The root node is the shadow root if the node is in a shadow DOM; some document otherwise
  //  (but NOT _the_ document; see second 'If' comment below for more).
  // If rootNode is shadow root, it'll have a host, which is the element to which the shadow
  //  is attached, and the one we need to check if it's in the document or not (because the
  //  shadow, and all nodes it contains, is never considered in the document since shadows
  //  behave like self-contained DOMs; but if the shadow's HOST, which is part of the document,
  //  is hidden, or is not in the document itself but is detached, it will affect the shadow's
  //  visibility, including all the nodes it contains). The host could be any normal node,
  //  or a custom element (i.e. web component). Either way, that's the one that is considered
  //  part of the document, not the shadow root, nor any of its children (i.e. the node being
  //  tested).
  // To further complicate things, we have to look all the way up until we find a shadow HOST
  //  that is attached (or find none) because the node might be in nested shadows...
  // If rootNode is not a shadow root, it won't have a host, and so rootNode should be the
  //  document (per the docs) and while it's a Document-type object, that document does not
  //  appear to be the same as the node's `ownerDocument` for some reason, so it's safer
  //  to ignore the rootNode at this point, and use `node.ownerDocument`. Otherwise,
  //  using `rootNode.contains(node)` will _always_ be true we'll get false-positives when
  //  node is actually detached.
  // NOTE: If `nodeRootHost` or `node` happens to be the `document` itself (which is possible
  //  if a tabbable/focusable node was quickly added to the DOM, focused, and then removed
  //  from the DOM as in https://github.com/focus-trap/focus-trap-react/issues/905), then
  //  `ownerDocument` will be `null`, hence the optional chaining on it.
  var nodeRoot = node && getRootNode(node);
  var nodeRootHost = (_nodeRoot = nodeRoot) === null || _nodeRoot === void 0 ? void 0 : _nodeRoot.host;

  // in some cases, a detached node will return itself as the root instead of a document or
  //  shadow root object, in which case, we shouldn't try to look further up the host chain
  var attached = false;
  if (nodeRoot && nodeRoot !== node) {
    var _nodeRootHost, _nodeRootHost$ownerDo, _node$ownerDocument;
    attached = !!((_nodeRootHost = nodeRootHost) !== null && _nodeRootHost !== void 0 && (_nodeRootHost$ownerDo = _nodeRootHost.ownerDocument) !== null && _nodeRootHost$ownerDo !== void 0 && _nodeRootHost$ownerDo.contains(nodeRootHost) || node !== null && node !== void 0 && (_node$ownerDocument = node.ownerDocument) !== null && _node$ownerDocument !== void 0 && _node$ownerDocument.contains(node));
    while (!attached && nodeRootHost) {
      var _nodeRoot2, _nodeRootHost2, _nodeRootHost2$ownerD;
      // since it's not attached and we have a root host, the node MUST be in a nested shadow DOM,
      //  which means we need to get the host's host and check if that parent host is contained
      //  in (i.e. attached to) the document
      nodeRoot = getRootNode(nodeRootHost);
      nodeRootHost = (_nodeRoot2 = nodeRoot) === null || _nodeRoot2 === void 0 ? void 0 : _nodeRoot2.host;
      attached = !!((_nodeRootHost2 = nodeRootHost) !== null && _nodeRootHost2 !== void 0 && (_nodeRootHost2$ownerD = _nodeRootHost2.ownerDocument) !== null && _nodeRootHost2$ownerD !== void 0 && _nodeRootHost2$ownerD.contains(nodeRootHost));
    }
  }
  return attached;
};
var isZeroArea = function isZeroArea(node) {
  var _node$getBoundingClie = node.getBoundingClientRect(),
    width = _node$getBoundingClie.width,
    height = _node$getBoundingClie.height;
  return width === 0 && height === 0;
};
var isHidden = function isHidden(node, _ref) {
  var displayCheck = _ref.displayCheck,
    getShadowRoot = _ref.getShadowRoot;
  if (displayCheck === 'full-native') {
    if ('checkVisibility' in node) {
      // Chrome >= 105, Edge >= 105, Firefox >= 106, Safari >= 17.4
      // @see https://developer.mozilla.org/en-US/docs/Web/API/Element/checkVisibility#browser_compatibility
      var visible = node.checkVisibility({
        // Checking opacity might be desirable for some use cases, but natively,
        // opacity zero elements _are_ focusable and tabbable.
        checkOpacity: false,
        opacityProperty: false,
        contentVisibilityAuto: true,
        visibilityProperty: true,
        // This is an alias for `visibilityProperty`. Contemporary browsers
        // support both. However, this alias has wider browser support (Chrome
        // >= 105 and Firefox >= 106, vs. Chrome >= 121 and Firefox >= 122), so
        // we include it anyway.
        checkVisibilityCSS: true
      });
      return !visible;
    }
    // Fall through to manual visibility checks
  }

  // NOTE: visibility will be `undefined` if node is detached from the document
  //  (see notes about this further down), which means we will consider it visible
  //  (this is legacy behavior from a very long way back)
  // NOTE: we check this regardless of `displayCheck="none"` because this is a
  //  _visibility_ check, not a _display_ check
  if (getComputedStyle(node).visibility === 'hidden') {
    return true;
  }
  var isDirectSummary = matches.call(node, 'details>summary:first-of-type');
  var nodeUnderDetails = isDirectSummary ? node.parentElement : node;
  if (matches.call(nodeUnderDetails, 'details:not([open]) *')) {
    return true;
  }
  if (!displayCheck || displayCheck === 'full' ||
  // full-native can run this branch when it falls through in case
  // Element#checkVisibility is unsupported
  displayCheck === 'full-native' || displayCheck === 'legacy-full') {
    if (typeof getShadowRoot === 'function') {
      // figure out if we should consider the node to be in an undisclosed shadow and use the
      //  'non-zero-area' fallback
      var originalNode = node;
      while (node) {
        var parentElement = node.parentElement;
        var rootNode = getRootNode(node);
        if (parentElement && !parentElement.shadowRoot && getShadowRoot(parentElement) === true // check if there's an undisclosed shadow
        ) {
          // node has an undisclosed shadow which means we can only treat it as a black box, so we
          //  fall back to a non-zero-area test
          return isZeroArea(node);
        } else if (node.assignedSlot) {
          // iterate up slot
          node = node.assignedSlot;
        } else if (!parentElement && rootNode !== node.ownerDocument) {
          // cross shadow boundary
          node = rootNode.host;
        } else {
          // iterate up normal dom
          node = parentElement;
        }
      }
      node = originalNode;
    }
    // else, `getShadowRoot` might be true, but all that does is enable shadow DOM support
    //  (i.e. it does not also presume that all nodes might have undisclosed shadows); or
    //  it might be a falsy value, which means shadow DOM support is disabled

    // Since we didn't find it sitting in an undisclosed shadow (or shadows are disabled)
    //  now we can just test to see if it would normally be visible or not, provided it's
    //  attached to the main document.
    // NOTE: We must consider case where node is inside a shadow DOM and given directly to
    //  `isTabbable()` or `isFocusable()` -- regardless of `getShadowRoot` option setting.

    if (isNodeAttached(node)) {
      // this works wherever the node is: if there's at least one client rect, it's
      //  somehow displayed; it also covers the CSS 'display: contents' case where the
      //  node itself is hidden in place of its contents; and there's no need to search
      //  up the hierarchy either
      return !node.getClientRects().length;
    }

    // Else, the node isn't attached to the document, which means the `getClientRects()`
    //  API will __always__ return zero rects (this can happen, for example, if React
    //  is used to render nodes onto a detached tree, as confirmed in this thread:
    //  https://github.com/facebook/react/issues/9117#issuecomment-284228870)
    //
    // It also means that even window.getComputedStyle(node).display will return `undefined`
    //  because styles are only computed for nodes that are in the document.
    //
    // NOTE: THIS HAS BEEN THE CASE FOR YEARS. It is not new, nor is it caused by tabbable
    //  somehow. Though it was never stated officially, anyone who has ever used tabbable
    //  APIs on nodes in detached containers has actually implicitly used tabbable in what
    //  was later (as of v5.2.0 on Apr 9, 2021) called `displayCheck="none"` mode -- essentially
    //  considering __everything__ to be visible because of the innability to determine styles.
    //
    // v6.0.0: As of this major release, the default 'full' option __no longer treats detached
    //  nodes as visible with the 'none' fallback.__
    if (displayCheck !== 'legacy-full') {
      return true; // hidden
    }
    // else, fallback to 'none' mode and consider the node visible
  } else if (displayCheck === 'non-zero-area') {
    // NOTE: Even though this tests that the node's client rect is non-zero to determine
    //  whether it's displayed, and that a detached node will __always__ have a zero-area
    //  client rect, we don't special-case for whether the node is attached or not. In
    //  this mode, we do want to consider nodes that have a zero area to be hidden at all
    //  times, and that includes attached or not.
    return isZeroArea(node);
  }

  // visible, as far as we can tell, or per current `displayCheck=none` mode, we assume
  //  it's visible
  return false;
};

// form fields (nested) inside a disabled fieldset are not focusable/tabbable
//  unless they are in the _first_ <legend> element of the top-most disabled
//  fieldset
var isDisabledFromFieldset = function isDisabledFromFieldset(node) {
  if (/^(INPUT|BUTTON|SELECT|TEXTAREA)$/.test(node.tagName)) {
    var parentNode = node.parentElement;
    // check if `node` is contained in a disabled <fieldset>
    while (parentNode) {
      if (parentNode.tagName === 'FIELDSET' && parentNode.disabled) {
        // look for the first <legend> among the children of the disabled <fieldset>
        for (var i = 0; i < parentNode.children.length; i++) {
          var child = parentNode.children.item(i);
          // when the first <legend> (in document order) is found
          if (child.tagName === 'LEGEND') {
            // if its parent <fieldset> is not nested in another disabled <fieldset>,
            // return whether `node` is a descendant of its first <legend>
            return matches.call(parentNode, 'fieldset[disabled] *') ? true : !child.contains(node);
          }
        }
        // the disabled <fieldset> containing `node` has no <legend>
        return true;
      }
      parentNode = parentNode.parentElement;
    }
  }

  // else, node's tabbable/focusable state should not be affected by a fieldset's
  //  enabled/disabled state
  return false;
};
var isNodeMatchingSelectorFocusable = function isNodeMatchingSelectorFocusable(options, node) {
  if (node.disabled || isHiddenInput(node) || isHidden(node, options) ||
  // For a details element with a summary, the summary element gets the focus
  isDetailsWithSummary(node) || isDisabledFromFieldset(node)) {
    return false;
  }
  return true;
};
var isNodeMatchingSelectorTabbable = function isNodeMatchingSelectorTabbable(options, node) {
  if (isNonTabbableRadio(node) || getTabIndex(node) < 0 || !isNodeMatchingSelectorFocusable(options, node)) {
    return false;
  }
  return true;
};
var isShadowRootTabbable = function isShadowRootTabbable(shadowHostNode) {
  var tabIndex = parseInt(shadowHostNode.getAttribute('tabindex'), 10);
  if (isNaN(tabIndex) || tabIndex >= 0) {
    return true;
  }
  // If a custom element has an explicit negative tabindex,
  // browsers will not allow tab targeting said element's children.
  return false;
};

/**
 * @param {Array.<Element|CandidateScope>} candidates
 * @returns Element[]
 */
var _sortByOrder = function sortByOrder(candidates) {
  var regularTabbables = [];
  var orderedTabbables = [];
  candidates.forEach(function (item, i) {
    var isScope = !!item.scopeParent;
    var element = isScope ? item.scopeParent : item;
    var candidateTabindex = getSortOrderTabIndex(element, isScope);
    var elements = isScope ? _sortByOrder(item.candidates) : element;
    if (candidateTabindex === 0) {
      isScope ? regularTabbables.push.apply(regularTabbables, elements) : regularTabbables.push(element);
    } else {
      orderedTabbables.push({
        documentOrder: i,
        tabIndex: candidateTabindex,
        item: item,
        isScope: isScope,
        content: elements
      });
    }
  });
  return orderedTabbables.sort(sortOrderedTabbables).reduce(function (acc, sortable) {
    sortable.isScope ? acc.push.apply(acc, sortable.content) : acc.push(sortable.content);
    return acc;
  }, []).concat(regularTabbables);
};
var tabbable = function tabbable(container, options) {
  options = options || {};
  var candidates;
  if (options.getShadowRoot) {
    candidates = _getCandidatesIteratively([container], options.includeContainer, {
      filter: isNodeMatchingSelectorTabbable.bind(null, options),
      flatten: false,
      getShadowRoot: options.getShadowRoot,
      shadowRootFilter: isShadowRootTabbable
    });
  } else {
    candidates = getCandidates(container, options.includeContainer, isNodeMatchingSelectorTabbable.bind(null, options));
  }
  return _sortByOrder(candidates);
};
var focusable = function focusable(container, options) {
  options = options || {};
  var candidates;
  if (options.getShadowRoot) {
    candidates = _getCandidatesIteratively([container], options.includeContainer, {
      filter: isNodeMatchingSelectorFocusable.bind(null, options),
      flatten: true,
      getShadowRoot: options.getShadowRoot
    });
  } else {
    candidates = getCandidates(container, options.includeContainer, isNodeMatchingSelectorFocusable.bind(null, options));
  }
  return candidates;
};
var isTabbable = function isTabbable(node, options) {
  options = options || {};
  if (!node) {
    throw new Error('No node provided');
  }
  if (matches.call(node, candidateSelector) === false) {
    return false;
  }
  return isNodeMatchingSelectorTabbable(options, node);
};
var focusableCandidateSelector = /* #__PURE__ */candidateSelectors.concat('iframe:not([inert]):not([inert] *)').join(',');
var isFocusable = function isFocusable(node, options) {
  options = options || {};
  if (!node) {
    throw new Error('No node provided');
  }
  if (matches.call(node, focusableCandidateSelector) === false) {
    return false;
  }
  return isNodeMatchingSelectorFocusable(options, node);
};


//# sourceMappingURL=index.esm.js.map


/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/classes-rename.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/classes-rename.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassesRename: function() { return /* binding */ ClassesRename; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_3__);




const ClassesRename = () => {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const unsubscribe = subscribeToStylesRepository();
    return () => {
      unsubscribe();
    };
  }, []);
  return null;
};
const subscribeToStylesRepository = () => {
  return _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.stylesRepository.subscribe((previous, current) => {
    if (!previous || !current) {
      return;
    }
    const currentIds = Object.keys(current);
    currentIds.forEach(id => {
      const isStyleChanged = previous[id] && (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_3__.hash)(previous[id]) !== (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_3__.hash)(current[id]);
      if (!isStyleChanged) {
        return;
      }
      const previousStyle = previous[id];
      const currentStyle = current[id];
      if (previousStyle.label !== currentStyle.label) {
        renameClass(previousStyle.label, currentStyle.label);
      }
    });
  });
};
const renameClass = (oldClassName, newClassName) => {
  Object.values((0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.getV1DocumentsManager)().documents).forEach(document => {
    const container = document.container;
    container.view?.el?.querySelectorAll(`.elementor .${oldClassName}`).forEach(element => {
      element.classList.replace(oldClassName, newClassName);
    });
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/elements-overlays.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/elements-overlays.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementsOverlays: function() { return /* binding */ ElementsOverlays; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _grid_outline__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./grid-outline */ "./packages/packages/core/editor-canvas/src/components/grid-outline/index.ts");
/* harmony import */ var _outline_overlay__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./outline-overlay */ "./packages/packages/core/editor-canvas/src/components/outline-overlay.tsx");





const hasGridStyleDisplay = element => {
  return element.computedStyleMap().get('display')?.toString() === 'grid';
};
const ELEMENTS_DATA_ATTR = 'atomic';
const overlayRegistry = [{
  component: _outline_overlay__WEBPACK_IMPORTED_MODULE_4__.OutlineOverlay,
  shouldRender: () => true
}, {
  component: _grid_outline__WEBPACK_IMPORTED_MODULE_3__.GridEmptyCellPositioner,
  shouldRender: ({
    element
  }) => hasGridStyleDisplay(element)
}, {
  component: _grid_outline__WEBPACK_IMPORTED_MODULE_3__.GridOutlineOverlay,
  shouldRender: ({
    isSelected,
    element
  }) => isSelected && hasGridStyleDisplay(element)
}];
function ElementsOverlays() {
  const selected = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.useSelectedElement)();
  const elements = useElementsDom();
  const currentEditMode = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.useEditMode)();
  const isEditMode = currentEditMode === 'edit';
  const isKitRouteActive = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.__privateUseIsRouteActive)('panel/global');
  const isActive = isEditMode && !isKitRouteActive;
  if (!isActive) {
    return null;
  }
  return elements.map(({
    id,
    domElement,
    isGlobal,
    widgetType
  }) => {
    const isSelected = selected.element?.id === id;
    return overlayRegistry.map(({
      shouldRender,
      component: Overlay
    }, index) => shouldRender({
      id,
      element: domElement,
      isSelected,
      widgetType
    }) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Overlay, {
      key: `${id}-${index}`,
      id: id,
      element: domElement,
      isSelected: isSelected,
      isGlobal: isGlobal,
      widgetType: widgetType
    }));
  });
}
function useElementsDom() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.windowEvent)('elementor/editor/element-rendered'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.windowEvent)('elementor/editor/element-destroyed')], () => {
    return (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElements)().filter(el => isV4Element(el.view?.el?.dataset)).map(element => ({
      id: element.id,
      domElement: element.view?.getDomElement?.()?.get?.(0),
      isGlobal: element.model.get('isGlobal') ?? false,
      widgetType: element.model.get('widgetType')
    })).filter(item => !!item.domElement);
  });
}
function isV4Element(dataset) {
  if (!dataset) {
    return false;
  }
  return ELEMENTS_DATA_ATTR in dataset || 'eType' in dataset;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/cell.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/cell.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Cell: function() { return /* binding */ Cell; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const FALLBACK_COLOR = 'rgba(0, 0, 0, 0.12)';
function Cell({
  x,
  y,
  width,
  height,
  color
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("rect", {
    x: x,
    y: y,
    width: width,
    height: height,
    fill: "none",
    stroke: color || FALLBACK_COLOR,
    strokeWidth: 1,
    strokeDasharray: "2 2",
    vectorEffect: "non-scaling-stroke"
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/first-empty-cell.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/first-empty-cell.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FirstEmptyCell: function() { return /* binding */ FirstEmptyCell; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const GLYPH_SIZE = 19;
function FirstEmptyCell({
  rect,
  color
}) {
  const size = Math.min(GLYPH_SIZE, rect.width, rect.height);
  if (size <= 0) {
    return null;
  }
  const centerX = rect.x + rect.width / 2;
  const centerY = rect.y + rect.height / 2;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("i", {
    className: "eicon-plus",
    "aria-hidden": "true",
    style: {
      position: 'absolute',
      left: centerX,
      top: centerY,
      transform: 'translate(-50%, -50%)',
      fontSize: size,
      color,
      lineHeight: 1,
      pointerEvents: 'none'
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/grid-empty-cell-positioner.tsx":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/grid-empty-cell-positioner.tsx ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridEmptyCellPositioner: function() { return /* binding */ GridEmptyCellPositioner; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_element_rect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../hooks/use-element-rect */ "./packages/packages/core/editor-canvas/src/hooks/use-element-rect.ts");
/* harmony import */ var _hooks_use_grid_tracks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../hooks/use-grid-tracks */ "./packages/packages/core/editor-canvas/src/hooks/use-grid-tracks.ts");
/* harmony import */ var _utils_find_first_empty_cell__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/find-first-empty-cell */ "./packages/packages/core/editor-canvas/src/utils/find-first-empty-cell.ts");




const CSS_VAR_ROW = '--e-grid-empty-cell-row';
const CSS_VAR_COL = '--e-grid-empty-cell-col';
const CSS_VAR_VISIBILITY = '--e-grid-empty-cell-visibility';
const clearGridEmptyCellStyles = target => {
  target.style.removeProperty(CSS_VAR_ROW);
  target.style.removeProperty(CSS_VAR_COL);
  target.style.removeProperty(CSS_VAR_VISIBILITY);
};
const GridEmptyCellPositioner = ({
  element
}) => {
  const rect = (0,_hooks_use_element_rect__WEBPACK_IMPORTED_MODULE_1__.useElementRect)(element);
  const tracks = (0,_hooks_use_grid_tracks__WEBPACK_IMPORTED_MODULE_2__.useGridTracks)(element, rect);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!element) {
      return;
    }
    const firstEmpty = (0,_utils_find_first_empty_cell__WEBPACK_IMPORTED_MODULE_3__.findFirstEmptyCell)(element, tracks.columns.length, tracks.rows.length);
    if (!firstEmpty) {
      element.style.removeProperty(CSS_VAR_ROW);
      element.style.removeProperty(CSS_VAR_COL);
      element.style.setProperty(CSS_VAR_VISIBILITY, 'hidden');
      return () => clearGridEmptyCellStyles(element);
    }
    element.style.setProperty(CSS_VAR_ROW, String(firstEmpty.row + 1));
    element.style.setProperty(CSS_VAR_COL, String(firstEmpty.col + 1));
    element.style.setProperty(CSS_VAR_VISIBILITY, 'visible');
    return () => {
      clearGridEmptyCellStyles(element);
    };
  }, [element, tracks]);
  return null;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/grid-outline-overlay.tsx":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/grid-outline-overlay.tsx ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridOutlineOverlay: function() { return /* binding */ GridOutlineOverlay; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/react/dist/floating-ui.react.mjs");
/* harmony import */ var _hooks_use_element_rect__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-element-rect */ "./packages/packages/core/editor-canvas/src/hooks/use-element-rect.ts");
/* harmony import */ var _hooks_use_floating_on_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../hooks/use-floating-on-element */ "./packages/packages/core/editor-canvas/src/hooks/use-floating-on-element.ts");
/* harmony import */ var _hooks_use_grid_tracks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../hooks/use-grid-tracks */ "./packages/packages/core/editor-canvas/src/hooks/use-grid-tracks.ts");
/* harmony import */ var _outline_overlay__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../outline-overlay */ "./packages/packages/core/editor-canvas/src/components/outline-overlay.tsx");
/* harmony import */ var _grid_outline__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./grid-outline */ "./packages/packages/core/editor-canvas/src/components/grid-outline/grid-outline.tsx");









const GridOutlineOverlay = ({
  element,
  id,
  isSelected
}) => {
  const settings = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.useElementEditorSettings)(id);
  const enabled = settings?.grid_outline;
  const rect = (0,_hooks_use_element_rect__WEBPACK_IMPORTED_MODULE_4__.useElementRect)(element);
  const tracks = (0,_hooks_use_grid_tracks__WEBPACK_IMPORTED_MODULE_6__.useGridTracks)(element, rect);
  const {
    floating
  } = (0,_hooks_use_floating_on_element__WEBPACK_IMPORTED_MODULE_5__.useFloatingOnElement)({
    element,
    isSelected
  });
  if (enabled === false) {
    return null;
  }
  if (tracks.columns.length === 0 && tracks.rows.length === 0) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_floating_ui_react__WEBPACK_IMPORTED_MODULE_3__.FloatingPortal, {
    id: _outline_overlay__WEBPACK_IMPORTED_MODULE_7__.CANVAS_WRAPPER_ID
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    ref: floating.setRef,
    style: {
      ...floating.styles,
      pointerEvents: 'none'
    },
    "data-grid-outline": id,
    role: "presentation"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_outline__WEBPACK_IMPORTED_MODULE_8__.GridOutline, {
    element: element,
    tracks: tracks,
    width: rect.width,
    height: rect.height
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/grid-outline.tsx":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/grid-outline.tsx ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridOutline: function() { return /* binding */ GridOutline; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_find_first_empty_cell__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/find-first-empty-cell */ "./packages/packages/core/editor-canvas/src/utils/find-first-empty-cell.ts");
/* harmony import */ var _utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/grid-outline-utils */ "./packages/packages/core/editor-canvas/src/utils/grid-outline-utils.ts");
/* harmony import */ var _cell__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./cell */ "./packages/packages/core/editor-canvas/src/components/grid-outline/cell.tsx");
/* harmony import */ var _first_empty_cell__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./first-empty-cell */ "./packages/packages/core/editor-canvas/src/components/grid-outline/first-empty-cell.tsx");
/* harmony import */ var _line__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./line */ "./packages/packages/core/editor-canvas/src/components/grid-outline/line.tsx");







const renderCells = (cells, color) => cells.map((cell, i) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_cell__WEBPACK_IMPORTED_MODULE_3__.Cell, {
  key: i,
  x: (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.snapToHalfPixel)(cell.x),
  y: (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.snapToHalfPixel)(cell.y),
  width: Math.round(cell.width),
  height: Math.round(cell.height),
  color: color
}));
const renderLines = (tracks, width, height) => {
  const {
    vertical,
    horizontal
  } = (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.computeGridLines)(tracks, width, height);
  return [...vertical.map((line, i) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_line__WEBPACK_IMPORTED_MODULE_5__.Line, {
    key: `v${i}`,
    x1: (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.snapToHalfPixel)(line.x1),
    y1: Math.round(line.y1),
    x2: (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.snapToHalfPixel)(line.x2),
    y2: Math.round(line.y2),
    color: tracks.borderColor
  })), ...horizontal.map((line, i) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_line__WEBPACK_IMPORTED_MODULE_5__.Line, {
    key: `h${i}`,
    x1: Math.round(line.x1),
    y1: (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.snapToHalfPixel)(line.y1),
    x2: Math.round(line.x2),
    y2: (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.snapToHalfPixel)(line.y2),
    color: tracks.borderColor
  }))];
};
const isDragActiveFromDom = element => {
  return Boolean(element?.querySelector('.e-dragging-over, .elementor-dragging-on-child, .elementor-draggable-over, .elementor-widget-placeholder, .elementor-sortable-placeholder'));
};
function GridOutline({
  element,
  tracks,
  width,
  height
}) {
  const cells = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_2__.computeCellRects)(tracks, width, height), [tracks, width, height]);
  const hasGap = tracks.columnGap > 0 || tracks.rowGap > 0;
  const firstEmpty = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_utils_find_first_empty_cell__WEBPACK_IMPORTED_MODULE_1__.findFirstEmptyCell)(element, tracks.columns.length, tracks.rows.length), [element, tracks]);
  const emptyCellRect = firstEmpty && tracks.columns.length > 0 ? cells[firstEmpty.row * tracks.columns.length + firstEmpty.col] : null;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("svg", {
    width: width,
    height: height,
    style: {
      position: 'absolute',
      inset: 0,
      overflow: 'visible'
    },
    xmlns: "http://www.w3.org/2000/svg"
  }, hasGap ? renderCells(cells, tracks.borderColor) : renderLines(tracks, width, height)), emptyCellRect && !isDragActiveFromDom(element) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_first_empty_cell__WEBPACK_IMPORTED_MODULE_4__.FirstEmptyCell, {
    rect: emptyCellRect,
    color: tracks.borderColor
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/index.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/index.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridEmptyCellPositioner: function() { return /* reexport safe */ _grid_empty_cell_positioner__WEBPACK_IMPORTED_MODULE_0__.GridEmptyCellPositioner; },
/* harmony export */   GridOutlineOverlay: function() { return /* reexport safe */ _grid_outline_overlay__WEBPACK_IMPORTED_MODULE_1__.GridOutlineOverlay; }
/* harmony export */ });
/* harmony import */ var _grid_empty_cell_positioner__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid-empty-cell-positioner */ "./packages/packages/core/editor-canvas/src/components/grid-outline/grid-empty-cell-positioner.tsx");
/* harmony import */ var _grid_outline_overlay__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid-outline-overlay */ "./packages/packages/core/editor-canvas/src/components/grid-outline/grid-outline-overlay.tsx");



/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/grid-outline/line.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/grid-outline/line.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Line: function() { return /* binding */ Line; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const FALLBACK_COLOR = 'rgba(0, 0, 0, 0.12)';
function Line({
  x1,
  y1,
  x2,
  y2,
  color
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("line", {
    x1: x1,
    y1: y1,
    x2: x2,
    y2: y2,
    stroke: color || FALLBACK_COLOR,
    strokeWidth: 1,
    strokeDasharray: "2 2",
    vectorEffect: "non-scaling-stroke"
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/interactions-renderer.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/interactions-renderer.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsRenderer: function() { return /* binding */ InteractionsRenderer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_interactions_items__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../hooks/use-interactions-items */ "./packages/packages/core/editor-canvas/src/hooks/use-interactions-items.ts");




function InteractionsRenderer() {
  const container = usePortalContainer();
  const interactionItems = (0,_hooks_use_interactions_items__WEBPACK_IMPORTED_MODULE_3__.useInteractionsItems)();
  if (!container) {
    return null;
  }
  const interactionsData = JSON.stringify(Array.isArray(interactionItems) ? interactionItems : []);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Portal, {
    container: container
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("script", {
    type: "application/json",
    "data-e-interactions": "true",
    dangerouslySetInnerHTML: {
      __html: interactionsData
    }
  }));
}
function usePortalContainer() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('editor/documents/attach-preview'), () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.getCanvasIframeDocument)()?.head);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/outline-overlay.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/outline-overlay.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CANVAS_WRAPPER_ID: function() { return /* binding */ CANVAS_WRAPPER_ID; },
/* harmony export */   OutlineOverlay: function() { return /* binding */ OutlineOverlay; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/react/dist/floating-ui.react.mjs");
/* harmony import */ var _hooks_use_bind_react_props_to_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../hooks/use-bind-react-props-to-element */ "./packages/packages/core/editor-canvas/src/hooks/use-bind-react-props-to-element.ts");
/* harmony import */ var _hooks_use_floating_on_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-floating-on-element */ "./packages/packages/core/editor-canvas/src/hooks/use-floating-on-element.ts");
/* harmony import */ var _hooks_use_has_overlapping__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-has-overlapping */ "./packages/packages/core/editor-canvas/src/hooks/use-has-overlapping.ts");
/* harmony import */ var _utils_outline_offset_utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/outline-offset-utils */ "./packages/packages/core/editor-canvas/src/utils/outline-offset-utils.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }







const CANVAS_WRAPPER_ID = 'elementor-preview-responsive-wrapper';
const OverlayBox = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => prop !== 'isSelected' && prop !== 'isSmallerOffset' && prop !== 'isGlobal'
})(({
  theme,
  isSelected,
  isSmallerOffset,
  isGlobal
}) => ({
  outline: `${isSelected ? '2px' : '1px'} solid ${isGlobal ? theme.palette.global.main : theme.palette.primary.light}`,
  outlineOffset: isSelected && !isSmallerOffset ? '-2px' : '-1px',
  pointerEvents: 'none'
}));
const OutlineOverlay = ({
  element,
  isSelected,
  id,
  isGlobal = false,
  widgetType
}) => {
  const {
    context,
    floating,
    isVisible
  } = (0,_hooks_use_floating_on_element__WEBPACK_IMPORTED_MODULE_4__.useFloatingOnElement)({
    element,
    isSelected
  });
  const {
    getFloatingProps,
    getReferenceProps
  } = (0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_2__.useInteractions)([(0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_2__.useHover)(context)]);
  const hasOverlapping = (0,_hooks_use_has_overlapping__WEBPACK_IMPORTED_MODULE_5__.useHasOverlapping)();
  (0,_hooks_use_bind_react_props_to_element__WEBPACK_IMPORTED_MODULE_3__.useBindReactPropsToElement)(element, getReferenceProps);
  const isSmallerOffset = (0,_utils_outline_offset_utils__WEBPACK_IMPORTED_MODULE_6__.shouldUseSmallerOutlineOffset)(element, widgetType);
  return isVisible && !hasOverlapping && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_floating_ui_react__WEBPACK_IMPORTED_MODULE_2__.FloatingPortal, {
    id: CANVAS_WRAPPER_ID
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(OverlayBox, _extends({
    ref: floating.setRef,
    isSelected: isSelected,
    isGlobal: isGlobal,
    style: floating.styles,
    "data-element-overlay": id,
    role: "presentation",
    isSmallerOffset: isSmallerOffset
  }, getFloatingProps())));
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/spotlight-backdrop.tsx":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/spotlight-backdrop.tsx ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SpotlightBackdrop: function() { return /* binding */ SpotlightBackdrop; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_element_rect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../hooks/use-element-rect */ "./packages/packages/core/editor-canvas/src/hooks/use-element-rect.ts");


function SpotlightBackdrop({
  canvas,
  element,
  onExit,
  ariaLabel
}) {
  const rect = (0,_hooks_use_element_rect__WEBPACK_IMPORTED_MODULE_1__.useElementRect)(element);
  const clipPath = element ? getRectClipPath(rect, canvas.defaultView) : undefined;
  const backdropStyle = {
    position: 'fixed',
    top: 0,
    left: 0,
    width: '100vw',
    height: '100vh',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    zIndex: 999,
    pointerEvents: 'painted',
    cursor: 'pointer',
    clipPath
  };
  const handleKeyDown = event => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      onExit();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", {
    style: backdropStyle,
    onClick: onExit,
    onKeyDown: handleKeyDown,
    role: "button",
    tabIndex: 0,
    "aria-label": ariaLabel
  });
}
function getRectClipPath(rect, viewport) {
  const {
    x,
    y,
    width,
    height
  } = rect;
  const {
    innerWidth: vw,
    innerHeight: vh
  } = viewport;
  return `path(evenodd, 'M 0 0 L ${vw} 0 L ${vw} ${vh} L 0 ${vh} Z M ${x} ${y} L ${x + width} ${y} L ${x + width} ${y + height} L ${x} ${y + height} L ${x} ${y} Z')`;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/components/style-renderer.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/components/style-renderer.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleRenderer: function() { return /* binding */ StyleRenderer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_documents_css_links__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../hooks/use-documents-css-links */ "./packages/packages/core/editor-canvas/src/hooks/use-documents-css-links.ts");
/* harmony import */ var _hooks_use_style_items__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-style-items */ "./packages/packages/core/editor-canvas/src/hooks/use-style-items.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





function StyleRenderer() {
  const container = usePortalContainer();
  const styleItems = (0,_hooks_use_style_items__WEBPACK_IMPORTED_MODULE_4__.useStyleItems)();
  const linksAttrs = (0,_hooks_use_documents_css_links__WEBPACK_IMPORTED_MODULE_3__.useDocumentsCssLinks)();
  if (!container) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Portal, {
    container: container
  }, filterUniqueStyleDefinitions(styleItems).map(item => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("style", {
    key: `${item.id}-${item.breakpoint}-${item.state ?? 'normal'}`
  }, item.value)), linksAttrs.map(attrs => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("link", _extends({}, attrs, {
    key: attrs.id
  }))));
}
function usePortalContainer() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('editor/documents/attach-preview'), () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.getCanvasIframeDocument)()?.head);
}

// we load local styles also from components, which are handled differently
// to avoid having "Encountered two children with the same key" - adding this filtering to avoid rendering the same style twice
function filterUniqueStyleDefinitions(styleItems) {
  const seen = new Map();
  return styleItems.filter(style => {
    const existingStyle = seen.get(style.id);
    if (existingStyle) {
      const existingStyleVariant = existingStyle.find(s => s.breakpoint === style.breakpoint && s.state === style.state);
      if (existingStyleVariant) {
        return false;
      }
      existingStyle.push(style);
      return true;
    }
    seen.set(style.id, [style]);
    return true;
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/composition-builder/composition-builder.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/composition-builder/composition-builder.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CompositionBuilder: function() { return /* binding */ CompositionBuilder; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _form_structure_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../form-structure/utils */ "./packages/packages/core/editor-canvas/src/form-structure/utils.ts");
/* harmony import */ var _mcp_utils_do_update_element_property__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../mcp/utils/do-update-element-property */ "./packages/packages/core/editor-canvas/src/mcp/utils/do-update-element-property.ts");
/* harmony import */ var _mcp_utils_merge_custom_css__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../mcp/utils/merge-custom-css */ "./packages/packages/core/editor-canvas/src/mcp/utils/merge-custom-css.ts");
/* harmony import */ var _utils_required_children_enforcer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/required-children-enforcer */ "./packages/packages/core/editor-canvas/src/composition-builder/utils/required-children-enforcer.ts");
/* harmony import */ var _utils_required_default_child_tags__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/required-default-child-tags */ "./packages/packages/core/editor-canvas/src/composition-builder/utils/required-default-child-tags.ts");






const CREATE_ELEMENT_INVALID_CONTAINER_MESSAGE = 'createElement did not return an element container with a model.';
class CompositionBuilder {
  elementConfig = {};
  elementStylesConfig = {};
  elementCustomCSS = {};
  rootContainers = [];
  api = {
    createElement: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.createElement,
    deleteElement: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.deleteElement,
    getWidgetsCache: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache,
    generateElementId: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.generateElementId,
    getContainer: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer,
    doUpdateElementProperty: _mcp_utils_do_update_element_property__WEBPACK_IMPORTED_MODULE_2__.doUpdateElementProperty
  };
  static fromXMLString(xmlString, api = {}) {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(xmlString, 'application/xml');
    const errorNode = xmlDoc.querySelector('parsererror');
    if (errorNode) {
      throw new Error('Failed to parse XML string: ' + errorNode.textContent);
    }
    return new CompositionBuilder({
      xml: xmlDoc,
      api
    });
  }
  constructor(opts) {
    const {
      api = {},
      elementConfig = {},
      stylesConfig = {},
      customCSS = {},
      xml
    } = opts;
    this.xml = xml;
    Object.assign(this.api, api);
    this.setElementConfig(elementConfig);
    this.setStylesConfig(stylesConfig);
    this.setCustomCSS(customCSS);
  }
  setElementConfig(config) {
    this.elementConfig = config;
  }
  setStylesConfig(config) {
    this.elementStylesConfig = config;
  }
  setCustomCSS(config) {
    this.elementCustomCSS = config;
  }
  getXML() {
    return this.xml;
  }
  buildModelTree(node, widgetsCache) {
    const elementTag = node.tagName;
    const isWidget = widgetsCache[elementTag]?.elType === 'widget';
    const id = this.api.generateElementId();
    const children = Array.from(node.children).map(child => this.buildModelTree(child, widgetsCache));
    node.setAttribute('id', id);
    const base = {
      id,
      skipDefaultChildren: true,
      elements: children,
      editor_settings: {
        title: node.getAttribute('configuration-id') ?? undefined
      },
      elType: 'widget'
    };

    // TODO: Restore this code once components are working in compositions
    // if ( elementTag === 'e-component' ) {
    // 	// apply component id before applying values
    // 	const elementConfig = this.elementConfig[ String( node.getAttribute( 'configuration-id' ) ) ];
    // 	if ( elementConfig ) {
    // 		base.settings = base.settings || {};
    // 		base.settings.component_instance = elementConfig.component_instance;
    // 	}
    // }

    if (isWidget) {
      return {
        ...base,
        elType: 'widget',
        widgetType: elementTag
      };
    }
    return {
      ...base,
      elType: elementTag
    };
  }
  async awaitViewRender(element) {
    const view = element.view;
    if (view?._currentRenderPromise instanceof Promise) {
      await view._currentRenderPromise;
    } else {
      await Promise.resolve();
    }
  }
  validateChildTypes(node, widgetsCache) {
    const errors = [];
    const allowedChildTypes = widgetsCache[node.tagName]?.allowed_child_types;
    if (allowedChildTypes?.length) {
      for (const child of Array.from(node.children)) {
        if (!allowedChildTypes.includes(child.tagName)) {
          errors.push(`"${child.tagName}" is not allowed as a child of "${node.tagName}". Allowed: ${allowedChildTypes.join(', ')}`);
        }
      }
    }
    for (const child of Array.from(node.children)) {
      errors.push(...this.validateChildTypes(child, widgetsCache));
    }
    return errors;
  }
  matchNodeByConfigId(configId) {
    const node = this.xml.querySelector(`[configuration-id="${configId}"]`);
    if (!node) {
      throw new Error(`Configuration id "${configId}" does not have target node.`);
    }
    const id = node.getAttribute('id');
    if (!id) {
      throw new Error(`Node with configuration id "${configId}" does not have element id.`);
    }
    const element = this.api.getContainer(id);
    if (!element) {
      throw new Error(`Element with id "${id}" not found but should exist.`);
    }
    return {
      element,
      node
    };
  }
  async applyProperties() {
    const configErrors = [];
    const styleErrors = [];
    const allConfigIds = new Set([...Object.keys(this.elementConfig), ...Object.keys(this.elementStylesConfig), ...Object.keys(this.elementCustomCSS)]);
    for (const configId of allConfigIds) {
      let element, node;
      try {
        ({
          element,
          node
        } = this.matchNodeByConfigId(configId));
      } catch (matchErr) {
        const msg = matchErr.message;
        if (this.elementConfig[configId]) {
          configErrors.push(msg);
        }
        if (this.elementStylesConfig[configId] || this.elementCustomCSS[configId]) {
          styleErrors.push(msg);
        }
        continue;
      }
      const config = this.elementConfig[configId];
      if (config) {
        for (const [propertyName, propertyValue] of Object.entries(config)) {
          try {
            this.api.doUpdateElementProperty({
              elementId: element.id,
              propertyName,
              propertyValue,
              elementType: node.tagName
            });
          } catch (error) {
            configErrors.push(error.message);
          }
        }
      }
      const styleConfig = this.elementStylesConfig[configId];
      const hasInvalidStyles = false;
      if (styleConfig) {
        const validStylesPropValues = {};
        for (const [styleName, stylePropValue] of Object.entries(styleConfig)) {
          if (styleName === '$intention') {
            continue;
          } else {
            // skipping actual validation - properies comes from the server
            validStylesPropValues[styleName] = stylePropValue;
          }
        }
        if (Object.keys(validStylesPropValues).length > 0) {
          try {
            this.api.doUpdateElementProperty({
              elementId: element.id,
              propertyName: '_styles',
              propertyValue: validStylesPropValues,
              elementType: node.tagName
            });
          } catch (error) {
            styleErrors.push(String(error));
          }
        }
      }
      const intentionCss = typeof styleConfig?.$intention === 'string' ? styleConfig.$intention.trim() : '';
      const fallbackCss = hasInvalidStyles && intentionCss ? intentionCss : '';
      const mergedCustomCss = (0,_mcp_utils_merge_custom_css__WEBPACK_IMPORTED_MODULE_3__.mergeCustomCssText)(this.elementCustomCSS[configId], fallbackCss);
      if (mergedCustomCss) {
        try {
          this.api.doUpdateElementProperty({
            elementId: element.id,
            propertyName: '_styles',
            propertyValue: {
              custom_css: mergedCustomCss
            },
            elementType: node.tagName
          });
        } catch (cssErr) {
          styleErrors.push(String(cssErr));
        }
      }
      await this.awaitViewRender(element);
    }
    return {
      configErrors,
      styleErrors
    };
  }
  async build(rootContainer) {
    const widgetsCache = this.api.getWidgetsCache() || {};
    new Set(this.xml.querySelectorAll('*')).forEach(node => {
      if (!widgetsCache[node.tagName]) {
        throw new Error(`Unknown widget type: ${node.tagName}`);
      }
    });
    const typesWithRequiredChildren = Object.keys(widgetsCache).filter(elementType => (0,_utils_required_default_child_tags__WEBPACK_IMPORTED_MODULE_5__.getRequiredDefaultChildTemplates)(widgetsCache[elementType]).length > 0);
    typesWithRequiredChildren.forEach(elementType => {
      new _utils_required_children_enforcer__WEBPACK_IMPORTED_MODULE_4__.RequiredChildrenEnforcer(elementType, widgetsCache).enforce(this.xml);
    });
    const childTypeErrors = [];
    for (const rootChild of Array.from(this.xml.children)) {
      childTypeErrors.push(...this.validateChildTypes(rootChild, widgetsCache));
    }
    if (childTypeErrors.length) {
      throw new Error(`Invalid element structure:\n${childTypeErrors.join('\n')}`);
    }
    const formErrors = [...(0,_form_structure_utils__WEBPACK_IMPORTED_MODULE_1__.collectFormAncestorErrors)(this.xml), ...(0,_form_structure_utils__WEBPACK_IMPORTED_MODULE_1__.collectSubmitButtonErrors)(this.xml), ...(0,_form_structure_utils__WEBPACK_IMPORTED_MODULE_1__.collectEmptyMessageErrors)(this.xml)];
    const children = Array.from(this.xml.children);
    for (const childNode of children) {
      const modelTree = this.buildModelTree(childNode, widgetsCache);
      try {
        const newElement = this.api.createElement({
          container: rootContainer,
          model: modelTree,
          options: {
            useHistory: false
          }
        });
        if (!newElement?.model) {
          throw new Error(CREATE_ELEMENT_INVALID_CONTAINER_MESSAGE);
        }
        this.rootContainers.push(newElement);
        await this.awaitViewRender(newElement);
      } catch (e) {
        const attempToRestoreInvalidContainer = this.api.getContainer(modelTree.id);
        if (attempToRestoreInvalidContainer) {
          this.api.deleteElement({
            container: attempToRestoreInvalidContainer
          });
        }
        throw e;
      }
    }
    const {
      configErrors,
      styleErrors
    } = await this.applyProperties();
    if (typeof window !== 'undefined') {
      const targetWindow = window.top || window;
      targetWindow.dispatchEvent(new CustomEvent('elementor/composition/built', {
        detail: {
          rootContainers: this.rootContainers.map(c => c.id)
        }
      }));
    }
    return {
      configErrors,
      styleErrors,
      formErrors,
      rootContainers: [...this.rootContainers]
    };
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/composition-builder/utils/required-children-enforcer.ts":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/composition-builder/utils/required-children-enforcer.ts ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RequiredChildrenEnforcer: function() { return /* binding */ RequiredChildrenEnforcer; }
/* harmony export */ });
/* harmony import */ var _required_default_child_tags__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./required-default-child-tags */ "./packages/packages/core/editor-canvas/src/composition-builder/utils/required-default-child-tags.ts");

const REQUIRED_CHILD_SCHEMA_HINT = 'Use the widget schema resource; under llm_guidance.required_direct_children for V4 widgets.';
class RequiredChildrenEnforcer {
  constructor(elementType, widgetsCache) {
    this.elementType = elementType;
    this.requiredTemplates = (0,_required_default_child_tags__WEBPACK_IMPORTED_MODULE_0__.getRequiredDefaultChildTemplates)(widgetsCache[elementType]);
  }
  enforce(xml) {
    if (this.requiredTemplates.length === 0) {
      return;
    }
    const errors = [];
    for (const rootNode of Array.from(xml.children)) {
      this.collectMissingRequiredErrors(rootNode, errors);
    }
    if (errors.length) {
      throw new Error(`${errors.join('\n')}\n${REQUIRED_CHILD_SCHEMA_HINT}`);
    }
  }
  collectMissingRequiredErrors(node, errors) {
    if (node.tagName === this.elementType) {
      const existingChildTags = new Set(Array.from(node.children).map(child => child.tagName));
      const missingTags = this.requiredTemplates.map(child => child.widgetType ?? child.elType ?? '').filter(type => type && !existingChildTags.has(type));
      if (missingTags.length) {
        const configurationId = node.getAttribute('configuration-id');
        const location = configurationId ? `<${node.tagName} configuration-id="${configurationId}">` : `<${node.tagName}>`;
        errors.push(`${location} Missing required direct child element tag(s): ${missingTags.join(', ')}.`);
      }
    }
    for (const childNode of Array.from(node.children)) {
      this.collectMissingRequiredErrors(childNode, errors);
    }
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/composition-builder/utils/required-default-child-tags.ts":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/composition-builder/utils/required-default-child-tags.ts ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getRequiredDefaultChildTemplates: function() { return /* binding */ getRequiredDefaultChildTemplates; },
/* harmony export */   getRequiredDefaultChildTypes: function() { return /* binding */ getRequiredDefaultChildTypes; }
/* harmony export */ });
function getRequiredDefaultChildTemplates(elementConfig) {
  const defaultChildren = elementConfig?.default_children;
  if (!Array.isArray(defaultChildren)) {
    return [];
  }
  return defaultChildren.filter(child => child?.meta?.required ?? false);
}
function getRequiredDefaultChildTypes(elementConfig) {
  return getRequiredDefaultChildTemplates(elementConfig).map(child => child.widgetType ?? child.elType ?? '').filter(type => Boolean(type));
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/form-structure/enforce-form-ancestor-commands.ts":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/form-structure/enforce-form-ancestor-commands.ts ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initFormAncestorEnforcement: function() { return /* binding */ initFormAncestorEnforcement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-canvas/src/form-structure/utils.ts");




const FORM_FIELDS_OUTSIDE_ALERT = {
  type: 'default',
  message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Form elements must be placed inside a form.', 'elementor'),
  id: 'form-fields-outside-form-blocked'
};
function initFormAncestorEnforcement() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.blockCommand)({
    command: 'document/elements/create',
    condition: blockFormFieldCreate
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.blockCommand)({
    command: 'document/elements/move',
    condition: blockFormFieldMove
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.blockCommand)({
    command: 'document/elements/paste',
    condition: blockFormFieldPaste
  });
}
function blockFormFieldCreate(args) {
  const elementType = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.getArgsElementType)(args);
  if (!elementType || !_utils__WEBPACK_IMPORTED_MODULE_3__.FORM_FIELD_ELEMENT_TYPES.has(elementType)) {
    return false;
  }
  const containers = args.containers ?? [args.container];
  if (containers.some(container => !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.isWithinForm)(container))) {
    handleBlockedFormField();
    return true;
  }
  return false;
}
function blockFormFieldMove(args) {
  const {
    containers = [args.container],
    target
  } = args;

  // Form fields must not be outside a form, but can be moved while inside a form or the container IS a form [ED-23858]
  const hasLooseFormFields = containers.some(container => container ? !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.hasElementType)(container, _utils__WEBPACK_IMPORTED_MODULE_3__.FORM_ELEMENT_TYPE) && (0,_utils__WEBPACK_IMPORTED_MODULE_3__.hasElementTypes)(container, _utils__WEBPACK_IMPORTED_MODULE_3__.FORM_FIELD_ELEMENT_TYPES) : false);
  if (hasLooseFormFields && !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.isWithinForm)(target) && !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.movedContainersIncludeAtomicFormRoot)(containers)) {
    handleBlockedFormField();
    return true;
  }
  return false;
}
function blockFormFieldPaste(args) {
  const {
    storageType
  } = args;
  if (storageType !== 'localstorage') {
    return false;
  }
  const data = window?.elementorCommon?.storage?.get();
  if (!data?.clipboard?.elements) {
    return false;
  }
  const hasFormFieldElement = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.hasClipboardElementTypes)(data.clipboard.elements, _utils__WEBPACK_IMPORTED_MODULE_3__.FORM_FIELD_ELEMENT_TYPES);
  if (hasFormFieldElement && !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.isWithinForm)(args.container) && !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.clipboardRootsAreAtomicForms)(data.clipboard.elements)) {
    handleBlockedFormField();
    return true;
  }
  return false;
}
function handleBlockedFormField() {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)(FORM_FIELDS_OUTSIDE_ALERT);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/form-structure/prevent-form-nesting-commands.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/form-structure/prevent-form-nesting-commands.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initFormNestingPrevention: function() { return /* binding */ initFormNestingPrevention; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-canvas/src/form-structure/utils.ts");




const FORM_NESTING_ALERT = {
  type: 'default',
  message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Forms can't be nested. Create separate forms instead.", 'elementor'),
  id: 'form-nesting-blocked'
};
function initFormNestingPrevention() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.blockCommand)({
    command: 'document/elements/create',
    condition: blockFormCreate
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.blockCommand)({
    command: 'document/elements/move',
    condition: blockFormMove
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.blockCommand)({
    command: 'document/elements/paste',
    condition: blockFormPaste
  });
}
function blockFormCreate(args) {
  const elementType = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.getArgsElementType)(args);
  if (!elementType) {
    return false;
  }
  if (elementType === _utils__WEBPACK_IMPORTED_MODULE_3__.FORM_ELEMENT_TYPE && (0,_utils__WEBPACK_IMPORTED_MODULE_3__.isWithinForm)(args.container)) {
    handleBlockedFormField();
    return true;
  }
  return false;
}
function blockFormMove(args) {
  const {
    containers = [args.container],
    target
  } = args;
  const hasFormElement = containers.some(container => container ? (0,_utils__WEBPACK_IMPORTED_MODULE_3__.hasElementType)(container, _utils__WEBPACK_IMPORTED_MODULE_3__.FORM_ELEMENT_TYPE) : false);
  if (hasFormElement && (0,_utils__WEBPACK_IMPORTED_MODULE_3__.isWithinForm)(target)) {
    handleBlockedFormField();
    return true;
  }
  return false;
}
function blockFormPaste(args) {
  const {
    storageType
  } = args;
  if (storageType !== 'localstorage') {
    return false;
  }
  const data = window?.elementorCommon?.storage?.get();
  if (!data?.clipboard?.elements) {
    return false;
  }
  const hasFormElement = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.hasClipboardElementType)(data.clipboard.elements, _utils__WEBPACK_IMPORTED_MODULE_3__.FORM_ELEMENT_TYPE);
  if (hasFormElement && (0,_utils__WEBPACK_IMPORTED_MODULE_3__.isWithinForm)(args.container)) {
    handleBlockedFormField();
    return true;
  }
  return false;
}
function handleBlockedFormField() {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)(FORM_NESTING_ALERT);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/form-structure/utils.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/form-structure/utils.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FORM_ELEMENT_TYPE: function() { return /* binding */ FORM_ELEMENT_TYPE; },
/* harmony export */   FORM_FIELD_ELEMENT_TYPES: function() { return /* binding */ FORM_FIELD_ELEMENT_TYPES; },
/* harmony export */   clipboardRootsAreAtomicForms: function() { return /* binding */ clipboardRootsAreAtomicForms; },
/* harmony export */   collectEmptyMessageErrors: function() { return /* binding */ collectEmptyMessageErrors; },
/* harmony export */   collectFormAncestorErrors: function() { return /* binding */ collectFormAncestorErrors; },
/* harmony export */   collectSubmitButtonErrors: function() { return /* binding */ collectSubmitButtonErrors; },
/* harmony export */   getArgsElementType: function() { return /* binding */ getArgsElementType; },
/* harmony export */   getClipboardElementType: function() { return /* binding */ getClipboardElementType; },
/* harmony export */   getElementType: function() { return /* binding */ getElementType; },
/* harmony export */   hasClipboardElementType: function() { return /* binding */ hasClipboardElementType; },
/* harmony export */   hasClipboardElementTypes: function() { return /* binding */ hasClipboardElementTypes; },
/* harmony export */   hasElementType: function() { return /* binding */ hasElementType; },
/* harmony export */   hasElementTypes: function() { return /* binding */ hasElementTypes; },
/* harmony export */   hasFormAncestor: function() { return /* binding */ hasFormAncestor; },
/* harmony export */   isElementWithinFormSelector: function() { return /* binding */ isElementWithinFormSelector; },
/* harmony export */   isWithinForm: function() { return /* binding */ isWithinForm; },
/* harmony export */   movedContainersIncludeAtomicFormRoot: function() { return /* binding */ movedContainersIncludeAtomicFormRoot; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

const FORM_ELEMENT_TYPE = 'e-form';
const FORM_FIELD_ELEMENT_TYPES = new Set(['e-form-input', 'e-form-textarea', 'e-form-label', 'e-form-checkbox', 'e-form-submit-button', 'e-form-select', 'e-form-radio-button', 'e-form-file-upload', 'e-form-date-picker', 'e-form-time-picker']);
function getArgsElementType(args) {
  return args.model?.widgetType || args.model?.elType;
}
function getElementType(element) {
  return element?.model.get('widgetType') || element?.model.get('elType');
}
function getClipboardElementType(element) {
  return element?.widgetType || element?.elType;
}
function isElementWithinFormSelector(element) {
  return !!element?.view?.el?.closest('form,[data-element_type="e-form"]');
}
function isWithinForm(element) {
  return isElementWithinFormSelector(element);
}
function hasElementType(element, type) {
  return (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getAllDescendants)(element).some(item => getElementType(item) === type);
}
function hasElementTypes(element, types) {
  return (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getAllDescendants)(element).some(item => {
    const itemType = getElementType(item);
    return itemType ? types.has(itemType) : false;
  });
}
function hasClipboardElementType(elements, type) {
  return elements.some(element => {
    const elementType = getClipboardElementType(element);
    if (elementType === type) {
      return true;
    }
    return element.elements ? hasClipboardElementType(element.elements, type) : false;
  });
}
function hasClipboardElementTypes(elements, types) {
  return elements.some(element => {
    const elementType = getClipboardElementType(element);
    if (elementType && types.has(elementType)) {
      return true;
    }
    return element.elements ? hasClipboardElementTypes(element.elements, types) : false;
  });
}
function movedContainersIncludeAtomicFormRoot(containers) {
  return containers.some(container => getElementType(container) === FORM_ELEMENT_TYPE);
}
function clipboardRootsAreAtomicForms(elements) {
  if (!elements.length) {
    return false;
  }
  return elements.every(el => getClipboardElementType(el) === FORM_ELEMENT_TYPE);
}
function hasFormAncestor(node) {
  return node.closest(FORM_ELEMENT_TYPE) !== null;
}
function collectFormAncestorErrors(xml) {
  const errors = [];
  for (const node of xml.querySelectorAll('*')) {
    if (!FORM_FIELD_ELEMENT_TYPES.has(node.tagName.toLowerCase())) {
      continue;
    }
    if (hasFormAncestor(node)) {
      continue;
    }
    const id = node.getAttribute('configuration-id');
    errors.push(`<${node.tagName}${id ? ` configuration-id="${id}"` : ''}> must be nested inside <e-form> (any ancestor depth is allowed).`);
  }
  return errors;
}
function collectSubmitButtonErrors(xml) {
  const errors = [];
  for (const form of xml.querySelectorAll('e-form')) {
    const submitButtons = form.querySelectorAll('e-form-submit-button');
    if (submitButtons.length === 0) {
      errors.push(`<e-form> has no <e-form-submit-button>.`);
    } else if (submitButtons.length > 1) {
      errors.push(`<e-form> has ${submitButtons.length} submit buttons — only 1 is allowed.`);
    }
  }
  return errors;
}
function collectEmptyMessageErrors(xml) {
  const errors = [];
  for (const node of xml.querySelectorAll('e-form-success-message, e-form-error-message')) {
    if (node.children.length === 0) {
      errors.push(`<${node.tagName}> must have at least one child element (e.g. <e-atomic-paragraph>).`);
    }
  }
  return errors;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-bind-react-props-to-element.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-bind-react-props-to-element.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useBindReactPropsToElement: function() { return /* binding */ useBindReactPropsToElement; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useBindReactPropsToElement(element, getProps) {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const el = element;
    const {
      events,
      attrs
    } = groupProps(getProps());
    events.forEach(([eventName, listener]) => el.addEventListener(eventName, listener));
    attrs.forEach(([attrName, attrValue]) => el.setAttribute(attrName, attrValue));
    return () => {
      events.forEach(([eventName, listener]) => el.removeEventListener(eventName, listener));
      attrs.forEach(([attrName]) => el.removeAttribute(attrName));
    };
  }, [getProps, element]);
}
function groupProps(props) {
  const eventRegex = /^on(?=[A-Z])/;
  return Object.entries(props).reduce((acc, [propName, propValue]) => {
    if (!eventRegex.test(propName)) {
      acc.attrs.push([propName, propValue]);
      return acc;
    }
    const eventName = propName.replace(eventRegex, '').toLowerCase();
    const listener = propValue;
    acc.events.push([eventName, listener]);
    return acc;
  }, {
    events: [],
    attrs: []
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-canvas-document.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-canvas-document.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCanvasDocument: function() { return /* binding */ useCanvasDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function useCanvasDocument() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/attach-preview'), () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.getCanvasIframeDocument)());
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-documents-css-links.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-documents-css-links.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useDocumentsCssLinks: function() { return /* binding */ useDocumentsCssLinks; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const REMOVED_ATTR = 'data-e-removed';
const DOCUMENT_WRAPPER_ATTR = 'data-elementor-id';
const CSS_LINK_ID_PREFIX = 'elementor-post-';
const CSS_LINK_ID_SUFFIX = '-css';
function useDocumentsCssLinks() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/attach-preview'), () => {
    const iframeDocument = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.getCanvasIframeDocument)();
    if (!iframeDocument) {
      return [];
    }
    const relevantLinkIds = getDocumentsIdsInCanvas(iframeDocument).map(id => `${CSS_LINK_ID_PREFIX}${id}${CSS_LINK_ID_SUFFIX}`);
    const links = getDocumentsCssLinks(iframeDocument).filter(link => relevantLinkIds.includes(link.getAttribute('id') ?? ''));
    links.forEach(link => {
      if (!link.hasAttribute(REMOVED_ATTR)) {
        link.remove();
      }
    });
    return links.map(link => ({
      ...getLinkAttrs(link),
      id: link.getAttribute('id') ?? '',
      [REMOVED_ATTR]: true
    }));
  });
}
function getDocumentsIdsInCanvas(document) {
  return [...(document.body.querySelectorAll(`[${DOCUMENT_WRAPPER_ATTR}]`) ?? [])].map(el => el.getAttribute(DOCUMENT_WRAPPER_ATTR) || '');
}
function getDocumentsCssLinks(document) {
  return [...(document.head.querySelectorAll(`link[rel="stylesheet"][id^=${CSS_LINK_ID_PREFIX}][id$=${CSS_LINK_ID_SUFFIX}]`) ?? [])];
}
function getLinkAttrs(el) {
  const entries = [...el.attributes].map(attr => [attr.name, attr.value]);
  return Object.fromEntries(entries);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-element-rect.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-element-rect.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useElementRect: function() { return /* binding */ useElementRect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);


function useElementRect(element) {
  const [rect, setRect] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(new DOMRect(0, 0, 0, 0));
  const onChange = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.throttle)(() => {
    setRect(element?.getBoundingClientRect() ?? new DOMRect(0, 0, 0, 0));
  }, 20, true);
  useScrollListener({
    element,
    onChange
  });
  useResizeListener({
    element,
    onChange
  });
  useMutationsListener({
    element,
    onChange
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => () => {
    onChange.cancel();
  }, [onChange]);
  return rect;
}
function useScrollListener({
  element,
  onChange
}) {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!element) {
      return;
    }
    const win = element.ownerDocument?.defaultView;
    win?.addEventListener('scroll', onChange, {
      passive: true
    });
    return () => {
      win?.removeEventListener('scroll', onChange);
    };
  }, [element, onChange]);
}
function useResizeListener({
  element,
  onChange
}) {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!element) {
      return;
    }
    const resizeObserver = new ResizeObserver(onChange);
    resizeObserver.observe(element);
    const win = element.ownerDocument?.defaultView;
    win?.addEventListener('resize', onChange, {
      passive: true
    });
    return () => {
      resizeObserver.disconnect();
      win?.removeEventListener('resize', onChange);
    };
  }, [element, onChange]);
}
function useMutationsListener({
  element,
  onChange
}) {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!element) {
      return;
    }
    const mutationObserver = new MutationObserver(onChange);
    mutationObserver.observe(element, {
      childList: true,
      subtree: true
    });
    return () => {
      mutationObserver.disconnect();
    };
  }, [element, onChange]);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-escape-on-canvas.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-escape-on-canvas.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useEscapeOnCanvas: function() { return /* binding */ useEscapeOnCanvas; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useEscapeOnCanvas(canvasDocument, onEscape) {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!canvasDocument) {
      return;
    }
    const handleEsc = event => {
      if (event.key === 'Escape') {
        onEscape();
      }
    };
    canvasDocument.body.addEventListener('keydown', handleEsc);
    return () => {
      canvasDocument.body.removeEventListener('keydown', handleEsc);
    };
  }, [canvasDocument, onEscape]);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-floating-on-element.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-floating-on-element.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useFloatingOnElement: function() { return /* binding */ useFloatingOnElement; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/react/dist/floating-ui.react.mjs");
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs");
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs");


function useFloatingOnElement({
  element,
  isSelected
}) {
  const [isOpen, setIsOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const sizeModifier = 2;
  const {
    refs,
    floatingStyles,
    context
  } = (0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_1__.useFloating)({
    // Must be controlled for interactions (like hover) to work.
    open: isOpen || isSelected,
    onOpenChange: setIsOpen,
    whileElementsMounted: _floating_ui_react__WEBPACK_IMPORTED_MODULE_2__.autoUpdate,
    middleware: [
    // Match the floating element's size to the reference element.

    (0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_3__.size)(() => {
      return {
        apply({
          elements,
          rects
        }) {
          Object.assign(elements.floating.style, {
            width: `${rects.reference.width + sizeModifier}px`,
            height: `${rects.reference.height + sizeModifier}px`
          });
        }
      };
    }),
    // Center the floating element on the reference element.
    (0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_3__.offset)(({
      rects
    }) => -rects.reference.height / 2 - rects.floating.height / 2)]
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    // Update the reference manually because Floating UI does not recalculate
    // the reference element when it is being used in `option.elements.reference`.
    // @link https://github.com/floating-ui/floating-ui/blob/master/packages/react/src/hooks/useFloatingRootContext.ts
    refs.setReference(element);
  }, [element, refs]);
  return {
    isVisible: isOpen || isSelected,
    context,
    floating: {
      setRef: refs.setFloating,
      ref: refs.floating,
      styles: floatingStyles
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-grid-children.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-grid-children.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useGridChildren: function() { return /* binding */ useGridChildren; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useGridChildren(element) {
  const [signal, setSignal] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!element) {
      return;
    }
    const bump = () => setSignal(previous => previous + 1);
    const resizeObserver = new ResizeObserver(bump);
    const observed = new Set();
    const syncChildren = () => {
      for (const child of Array.from(element.children)) {
        if (!observed.has(child)) {
          resizeObserver.observe(child);
          observed.add(child);
        }
      }
      for (const child of observed) {
        if (child.parentElement !== element) {
          resizeObserver.unobserve(child);
          observed.delete(child);
        }
      }
    };
    syncChildren();
    const mutationObserver = new MutationObserver(() => {
      syncChildren();
      bump();
    });
    mutationObserver.observe(element, {
      childList: true
    });
    return () => {
      mutationObserver.disconnect();
      resizeObserver.disconnect();
      observed.clear();
    };
  }, [element]);
  return signal;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-grid-tracks.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-grid-tracks.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useGridTracks: function() { return /* binding */ useGridTracks; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/grid-outline-utils */ "./packages/packages/core/editor-canvas/src/utils/grid-outline-utils.ts");
/* harmony import */ var _use_grid_children__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./use-grid-children */ "./packages/packages/core/editor-canvas/src/hooks/use-grid-children.ts");





const EMPTY = {
  columns: [],
  rows: [],
  columnGap: 0,
  rowGap: 0,
  padding: {
    top: 0,
    right: 0,
    bottom: 0,
    left: 0
  },
  borderColor: ''
};
const DEVICE_MODE_CHANGE_EVENT = 'elementor/device-mode/change';
function useGridTracks(element, rect) {
  const [tracks, setTracks] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(EMPTY);
  const trigger = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.windowEvent)(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.ELEMENT_STYLE_CHANGE_EVENT), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.windowEvent)(DEVICE_MODE_CHANGE_EVENT)], () => ({}));
  const childrenTrigger = (0,_use_grid_children__WEBPACK_IMPORTED_MODULE_4__.useGridChildren)(element);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const previewWindow = element?.ownerDocument?.defaultView;
    if (!element || !previewWindow) {
      setTracks(EMPTY);
      return;
    }
    const frame = previewWindow.requestAnimationFrame(() => {
      setTracks((0,_utils_grid_outline_utils__WEBPACK_IMPORTED_MODULE_3__.toGridTracks)(previewWindow.getComputedStyle(element)));
    });
    return () => {
      previewWindow.cancelAnimationFrame(frame);
    };
  }, [element, rect.width, rect.height, trigger, childrenTrigger]);
  return tracks;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-has-overlapping.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-has-overlapping.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useHasOverlapping: function() { return /* binding */ useHasOverlapping; }
/* harmony export */ });
const possibleOverlappingSelectors = ['.e-off-canvas']; // can add more selectors here if needed, make sure to loop through them to check classList

const useHasOverlapping = () => {
  const preview = window.elementor?.$preview?.[0];
  if (!preview) {
    return false;
  }
  const hasOverlapping = possibleOverlappingSelectors.map(selector => Array.from(preview?.contentWindow?.document.body.querySelectorAll(selector) ?? [])).flat().some(elem => elem.checkVisibility({
    opacityProperty: true,
    visibilityProperty: true,
    contentVisibilityAuto: true
  }));
  return hasOverlapping;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-interactions-items.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-interactions-items.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useInteractionsItems: function() { return /* binding */ useInteractionsItems; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _use_on_mount__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-on-mount */ "./packages/packages/core/editor-canvas/src/hooks/use-on-mount.ts");




function useInteractionsItems() {
  const [interactionItems, setInteractionItems] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const providerAndSubscribers = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    try {
      const providers = _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.interactionsRepository.getProviders();
      const mapped = providers.map(provider => {
        return {
          provider,
          subscriber: createProviderSubscriber({
            provider,
            setInteractionItems
          })
        };
      });
      return mapped;
    } catch {
      return [];
    }
  }, []);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (providerAndSubscribers.length === 0) {
      return;
    }
    const unsubscribes = providerAndSubscribers.map(({
      provider,
      subscriber
    }) => {
      const safeSubscriber = () => {
        try {
          subscriber();
        } catch {}
      };
      const unsubscribe = provider.subscribe(safeSubscriber);
      return unsubscribe;
    });
    return () => {
      unsubscribes.forEach(unsubscribe => unsubscribe());
    };
  }, [providerAndSubscribers]);
  (0,_use_on_mount__WEBPACK_IMPORTED_MODULE_3__.useOnMount)(() => {
    if (providerAndSubscribers.length === 0) {
      return;
    }
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.registerDataHook)('after', 'editor/documents/attach-preview', async () => {
      providerAndSubscribers.forEach(({
        subscriber
      }) => {
        try {
          subscriber();
        } catch {}
      });
    });
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const result = Object.values(interactionItems).sort(sortByProviderPriority).flatMap(({
      items
    }) => items);
    return result;
  }, [interactionItems]);
}
function sortByProviderPriority({
  provider: providerA
}, {
  provider: providerB
}) {
  return providerA.priority - providerB.priority;
}
function createProviderSubscriber({
  provider,
  setInteractionItems
}) {
  return () => {
    try {
      const items = provider.actions.all();
      const providerKey = provider.getKey();
      setInteractionItems(prev => ({
        ...prev,
        [providerKey]: {
          provider,
          items: items
        }
      }));
    } catch {}
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-on-mount.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-on-mount.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOnMount: function() { return /* binding */ useOnMount; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useOnMount(cb) {
  const mounted = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!mounted.current) {
      mounted.current = true;
      cb();
    }
  }, []); // eslint-disable-line react-hooks/exhaustive-deps
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-style-items.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-style-items.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStyleItems: function() { return /* binding */ useStyleItems; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _utils_abort_previous_runs__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/abort-previous-runs */ "./packages/packages/core/editor-canvas/src/utils/abort-previous-runs.ts");
/* harmony import */ var _utils_pregenerated_links_removal__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/pregenerated-links-removal */ "./packages/packages/core/editor-canvas/src/utils/pregenerated-links-removal.ts");
/* harmony import */ var _utils_signalized_process__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../utils/signalized-process */ "./packages/packages/core/editor-canvas/src/utils/signalized-process.ts");
/* harmony import */ var _use_on_mount__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./use-on-mount */ "./packages/packages/core/editor-canvas/src/hooks/use-on-mount.ts");
/* harmony import */ var _use_style_prop_resolver__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./use-style-prop-resolver */ "./packages/packages/core/editor-canvas/src/hooks/use-style-prop-resolver.ts");
/* harmony import */ var _use_style_renderer__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./use-style-renderer */ "./packages/packages/core/editor-canvas/src/hooks/use-style-renderer.ts");











function useStyleItems() {
  const resolve = (0,_use_style_prop_resolver__WEBPACK_IMPORTED_MODULE_9__.useStylePropResolver)();
  const renderStyles = (0,_use_style_renderer__WEBPACK_IMPORTED_MODULE_10__.useStyleRenderer)(resolve);
  const breakpoints = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.useBreakpoints)();
  const [styleItems, setStyleItems] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const styleItemsCacheRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(new Map());
  const providerAndSubscribers = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const createEmptyCache = () => {
      return {
        orderedIds: [],
        itemsById: new Map()
      };
    };
    const getCache = provider => {
      const providerKey = safeGetKey(provider);
      if (!providerKey) {
        return createEmptyCache();
      }
      if (!styleItemsCacheRef.current.has(providerKey)) {
        styleItemsCacheRef.current.set(providerKey, createEmptyCache());
      }
      return styleItemsCacheRef.current.get(providerKey);
    };
    return _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.stylesRepository.getProviders().map(provider => ({
      provider,
      subscriber: createProviderSubscriber({
        provider,
        renderStyles,
        setStyleItems,
        getCache: () => getCache(provider)
      })
    }));
  }, [renderStyles]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const unsubscribes = providerAndSubscribers.map(({
      provider,
      subscriber
    }) => provider.subscribe(subscriber));
    return () => {
      unsubscribes.forEach(unsubscribe => unsubscribe());
    };
  }, [providerAndSubscribers]);
  (0,_use_on_mount__WEBPACK_IMPORTED_MODULE_8__.useOnMount)(() => {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__.registerDataHook)('after', 'editor/documents/attach-preview', async () => {
      (0,_utils_pregenerated_links_removal__WEBPACK_IMPORTED_MODULE_6__.resetRemovedProviders)();
      const promises = providerAndSubscribers.map(async ({
        subscriber
      }) => subscriber());
      await Promise.all(promises);
    });
  });
  const breakpointSorter = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => createBreakpointSorter(breakpoints.map(breakpoint => breakpoint.id)), [breakpoints]);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => Object.values(styleItems).sort(prioritySorter).flatMap(({
    items
  }) => items).sort(stateSorter).sort(breakpointSorter), [styleItems, breakpointSorter]);
}
function prioritySorter({
  provider: providerA
}, {
  provider: providerB
}) {
  return providerA.priority - providerB.priority;
}
function stateSorter({
  state: stateA
}, {
  state: stateB
}) {
  if ((0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.isClassState)(stateA) && !(0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.isClassState)(stateB)) {
    return -1;
  }
  if (!(0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.isClassState)(stateA) && (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.isClassState)(stateB)) {
    return 1;
  }
  return 0;
}
function createBreakpointSorter(breakpointsOrder) {
  return ({
    breakpoint: breakpointA
  }, {
    breakpoint: breakpointB
  }) => breakpointsOrder.indexOf(breakpointA) - breakpointsOrder.indexOf(breakpointB);
}
function safeGetKey(provider) {
  try {
    return provider.getKey();
  } catch {
    return null;
  }
}
function createProviderSubscriber({
  provider,
  renderStyles,
  setStyleItems,
  getCache
}) {
  return (0,_utils_abort_previous_runs__WEBPACK_IMPORTED_MODULE_5__.abortPreviousRuns)((abortController, previous, current) => (0,_utils_signalized_process__WEBPACK_IMPORTED_MODULE_7__.signalizedProcess)(abortController.signal).then((_, signal) => {
    const cache = getCache();
    const hasDiffInfo = current !== undefined && previous !== undefined;
    const hasCache = cache.orderedIds.length > 0;
    if (hasCache && provider.isPregeneratedLink) {
      // if styles were rendered already (i.e. hasCache = true), we can safely remove the pregenerated css rules imported via <link /> tags
      (0,_utils_pregenerated_links_removal__WEBPACK_IMPORTED_MODULE_6__.removeProviderPregeneratedLinks)(provider.getKey(), provider.isPregeneratedLink);
    }
    if (hasDiffInfo && hasCache) {
      return updateItems(cache, previous, current, signal);
    }
    return createItems(cache, signal);
  }).then(items => {
    setStyleItems(prev => ({
      ...prev,
      [provider.getKey()]: {
        provider,
        items
      }
    }));
  }).execute());
  async function updateItems(cache, previous, current, signal) {
    const changedIds = getChangedStyleIds(previous, current);
    cache.orderedIds = provider.actions.all().map(style => style.id).reverse();
    if (changedIds.length > 0) {
      const changedStyles = changedIds.map(id => provider.actions.get(id)).filter(style => !!style).map(style => ({
        ...style,
        cssName: provider.actions.resolveCssName(style.id)
      }));
      const breakpointSplit = breakToBreakpoints(changedStyles);
      return renderStyles({
        styles: breakpointSplit,
        signal
      }).then(rendered => {
        updateCacheItems(cache, changedIds, rendered);
        return getOrderedItems(cache);
      });
    }
    return getOrderedItems(cache);
  }
  async function createItems(cache, signal) {
    const allStyles = provider.actions.all();
    const styles = [...allStyles].reverse().map(style => {
      return {
        ...style,
        cssName: provider.actions.resolveCssName(style.id)
      };
    });
    return renderStyles({
      styles: breakToBreakpoints(styles),
      signal
    }).then(rendered => {
      rebuildCache(cache, allStyles, rendered);
      return getOrderedItems(cache);
    });
  }
  function breakToBreakpoints(styles) {
    return Object.values(styles.reduce((acc, style) => {
      style.variants.forEach(variant => {
        const breakpoint = variant.meta.breakpoint || 'desktop';
        if (!acc[style.id]) {
          acc[style.id] = {};
        }
        if (!acc[style.id][breakpoint]) {
          acc[style.id][breakpoint] = {
            ...style,
            variants: []
          };
        }
        acc[style.id][breakpoint].variants.push(variant);
      });
      return acc;
    }, {})).flatMap(breakpointMap => Object.values(breakpointMap));
  }
}
function getChangedStyleIds(previous, current) {
  const changedIds = [];
  for (const id of Object.keys(current)) {
    const currentStyle = current[id];
    const previousStyle = previous[id];
    if (!previousStyle || currentStyle !== previousStyle) {
      changedIds.push(id);
    }
  }
  return changedIds;
}
function getOrderedItems(cache) {
  return cache.orderedIds.map(id => cache.itemsById.get(id)).filter(items => items !== undefined).flat();
}
function updateCacheItems(cache, changedIds, changedItems) {
  for (const id of changedIds) {
    cache.itemsById.delete(id);
  }
  for (const item of changedItems) {
    const existing = cache.itemsById.get(item.id) || [];
    existing.push(item);
    cache.itemsById.set(item.id, existing);
  }
}
function rebuildCache(cache, allStyles, items) {
  cache.orderedIds = allStyles.map(style => style.id).reverse();
  cache.itemsById.clear();
  for (const item of items) {
    const existing = cache.itemsById.get(item.id) || [];
    existing.push(item);
    cache.itemsById.set(item.id, existing);
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-style-prop-resolver.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-style-prop-resolver.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStylePropResolver: function() { return /* binding */ useStylePropResolver; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _renderers_create_props_resolver__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../renderers/create-props-resolver */ "./packages/packages/core/editor-canvas/src/renderers/create-props-resolver.ts");
/* harmony import */ var _renderers_enqueue_font_from_style_prop__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../renderers/enqueue-font-from-style-prop */ "./packages/packages/core/editor-canvas/src/renderers/enqueue-font-from-style-prop.ts");
/* harmony import */ var _style_transformers_registry__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../style-transformers-registry */ "./packages/packages/core/editor-canvas/src/style-transformers-registry.ts");






function useStylePropResolver() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return (0,_renderers_create_props_resolver__WEBPACK_IMPORTED_MODULE_3__.createPropsResolver)({
      transformers: _style_transformers_registry__WEBPACK_IMPORTED_MODULE_5__.styleTransformersRegistry,
      schema: (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__.getStylesSchema)(),
      onPropResolve: ({
        propValue,
        propType
      }) => {
        (0,_renderers_enqueue_font_from_style_prop__WEBPACK_IMPORTED_MODULE_4__.maybeEnqueueFontFromStyleProp)(propType, propValue, _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.enqueueFont);
      }
    });
  }, []);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/hooks/use-style-renderer.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/hooks/use-style-renderer.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStyleRenderer: function() { return /* binding */ useStyleRenderer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _renderers_create_styles_renderer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../renderers/create-styles-renderer */ "./packages/packages/core/editor-canvas/src/renderers/create-styles-renderer.ts");



const SELECTOR_PREFIX = '.elementor';
function useStyleRenderer(resolve) {
  const breakpoints = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.useBreakpointsMap)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return (0,_renderers_create_styles_renderer__WEBPACK_IMPORTED_MODULE_2__.createStylesRenderer)({
      selectorPrefix: SELECTOR_PREFIX,
      breakpoints,
      resolve
    });
  }, [resolve, breakpoints]);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/init-settings-transformers.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/init-settings-transformers.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initSettingsTransformers: function() { return /* binding */ initSettingsTransformers; }
/* harmony export */ });
/* harmony import */ var _settings_transformers_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings-transformers-registry */ "./packages/packages/core/editor-canvas/src/settings-transformers-registry.ts");
/* harmony import */ var _transformers_settings_attributes_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./transformers/settings/attributes-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/attributes-transformer.ts");
/* harmony import */ var _transformers_settings_classes_transformer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./transformers/settings/classes-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/classes-transformer.ts");
/* harmony import */ var _transformers_settings_date_range_transformer__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./transformers/settings/date-range-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/date-range-transformer.ts");
/* harmony import */ var _transformers_settings_date_time_transformer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./transformers/settings/date-time-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/date-time-transformer.ts");
/* harmony import */ var _transformers_settings_html_v2_transformer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./transformers/settings/html-v2-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/html-v2-transformer.ts");
/* harmony import */ var _transformers_settings_html_v3_transformer__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./transformers/settings/html-v3-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/html-v3-transformer.ts");
/* harmony import */ var _transformers_settings_link_transformer__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./transformers/settings/link-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/link-transformer.ts");
/* harmony import */ var _transformers_settings_query_transformer__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./transformers/settings/query-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/query-transformer.ts");
/* harmony import */ var _transformers_settings_time_range_transformer__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./transformers/settings/time-range-transformer */ "./packages/packages/core/editor-canvas/src/transformers/settings/time-range-transformer.ts");
/* harmony import */ var _transformers_shared_image_src_transformer__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./transformers/shared/image-src-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/image-src-transformer.ts");
/* harmony import */ var _transformers_shared_image_transformer__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./transformers/shared/image-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/image-transformer.ts");
/* harmony import */ var _transformers_shared_plain_transformer__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./transformers/shared/plain-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/plain-transformer.ts");
/* harmony import */ var _transformers_shared_svg_src_transformer__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./transformers/shared/svg-src-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/svg-src-transformer.ts");
/* harmony import */ var _transformers_shared_video_src_transformer__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./transformers/shared/video-src-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/video-src-transformer.ts");















function initSettingsTransformers() {
  _settings_transformers_registry__WEBPACK_IMPORTED_MODULE_0__.settingsTransformersRegistry.register('classes', (0,_transformers_settings_classes_transformer__WEBPACK_IMPORTED_MODULE_2__.createClassesTransformer)()).register('link', _transformers_settings_link_transformer__WEBPACK_IMPORTED_MODULE_7__.linkTransformer).register('query', _transformers_settings_query_transformer__WEBPACK_IMPORTED_MODULE_8__.queryTransformer).register('image', _transformers_shared_image_transformer__WEBPACK_IMPORTED_MODULE_11__.imageTransformer).register('image-src', _transformers_shared_image_src_transformer__WEBPACK_IMPORTED_MODULE_10__.imageSrcTransformer).register('svg-src', _transformers_shared_svg_src_transformer__WEBPACK_IMPORTED_MODULE_13__.svgSrcTransformer).register('video-src', _transformers_shared_video_src_transformer__WEBPACK_IMPORTED_MODULE_14__.videoSrcTransformer).register('attributes', _transformers_settings_attributes_transformer__WEBPACK_IMPORTED_MODULE_1__.attributesTransformer).register('date-time', _transformers_settings_date_time_transformer__WEBPACK_IMPORTED_MODULE_4__.dateTimeTransformer).register('html-v2', _transformers_settings_html_v2_transformer__WEBPACK_IMPORTED_MODULE_5__.htmlV2Transformer).register('html-v3', _transformers_settings_html_v3_transformer__WEBPACK_IMPORTED_MODULE_6__.htmlV3Transformer).register('date-range', _transformers_settings_date_range_transformer__WEBPACK_IMPORTED_MODULE_3__.dateRangeTransformer).register('time-range', _transformers_settings_time_range_transformer__WEBPACK_IMPORTED_MODULE_9__.timeRangeTransformer).registerFallback(_transformers_shared_plain_transformer__WEBPACK_IMPORTED_MODULE_12__.plainTransformer);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/init-style-transformers.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/init-style-transformers.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initStyleTransformers: function() { return /* binding */ initStyleTransformers; }
/* harmony export */ });
/* harmony import */ var _style_transformers_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style-transformers-registry */ "./packages/packages/core/editor-canvas/src/style-transformers-registry.ts");
/* harmony import */ var _transformers_shared_image_src_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./transformers/shared/image-src-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/image-src-transformer.ts");
/* harmony import */ var _transformers_shared_image_transformer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./transformers/shared/image-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/image-transformer.ts");
/* harmony import */ var _transformers_shared_plain_transformer__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./transformers/shared/plain-transformer */ "./packages/packages/core/editor-canvas/src/transformers/shared/plain-transformer.ts");
/* harmony import */ var _transformers_styles_background_color_overlay_transformer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./transformers/styles/background-color-overlay-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/background-color-overlay-transformer.ts");
/* harmony import */ var _transformers_styles_background_gradient_overlay_transformer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./transformers/styles/background-gradient-overlay-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/background-gradient-overlay-transformer.ts");
/* harmony import */ var _transformers_styles_background_image_overlay_transformer__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./transformers/styles/background-image-overlay-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/background-image-overlay-transformer.ts");
/* harmony import */ var _transformers_styles_background_image_size_scale_transformer__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./transformers/styles/background-image-size-scale-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/background-image-size-scale-transformer.ts");
/* harmony import */ var _transformers_styles_background_overlay_transformer__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./transformers/styles/background-overlay-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/background-overlay-transformer.ts");
/* harmony import */ var _transformers_styles_background_transformer__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./transformers/styles/background-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/background-transformer.ts");
/* harmony import */ var _transformers_styles_color_stop_transformer__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./transformers/styles/color-stop-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/color-stop-transformer.ts");
/* harmony import */ var _transformers_styles_create_combine_array_transformer__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./transformers/styles/create-combine-array-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/create-combine-array-transformer.ts");
/* harmony import */ var _transformers_styles_create_multi_props_transformer__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./transformers/styles/create-multi-props-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/create-multi-props-transformer.ts");
/* harmony import */ var _transformers_styles_filter_transformer__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./transformers/styles/filter-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/filter-transformer.ts");
/* harmony import */ var _transformers_styles_flex_transformer__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./transformers/styles/flex-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/flex-transformer.ts");
/* harmony import */ var _transformers_styles_font_family_transformer__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./transformers/styles/font-family-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/font-family-transformer.ts");
/* harmony import */ var _transformers_styles_grid_track_size_transformer__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./transformers/styles/grid-track-size-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-size-transformer.ts");
/* harmony import */ var _transformers_styles_perspective_origin_transformer__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./transformers/styles/perspective-origin-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/perspective-origin-transformer.ts");
/* harmony import */ var _transformers_styles_position_transformer__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./transformers/styles/position-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/position-transformer.ts");
/* harmony import */ var _transformers_styles_shadow_transformer__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./transformers/styles/shadow-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/shadow-transformer.ts");
/* harmony import */ var _transformers_styles_size_transformer__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./transformers/styles/size-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/size-transformer.ts");
/* harmony import */ var _transformers_styles_span_transformer__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./transformers/styles/span-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/span-transformer.ts");
/* harmony import */ var _transformers_styles_stroke_transformer__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./transformers/styles/stroke-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/stroke-transformer.ts");
/* harmony import */ var _transformers_styles_transform_functions_transformer__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./transformers/styles/transform-functions-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-functions-transformer.ts");
/* harmony import */ var _transformers_styles_transform_move_transformer__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./transformers/styles/transform-move-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-move-transformer.ts");
/* harmony import */ var _transformers_styles_transform_origin_transformer__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./transformers/styles/transform-origin-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-origin-transformer.ts");
/* harmony import */ var _transformers_styles_transform_rotate_transformer__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./transformers/styles/transform-rotate-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-rotate-transformer.ts");
/* harmony import */ var _transformers_styles_transform_scale_transformer__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./transformers/styles/transform-scale-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-scale-transformer.ts");
/* harmony import */ var _transformers_styles_transform_skew_transformer__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./transformers/styles/transform-skew-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-skew-transformer.ts");
/* harmony import */ var _transformers_styles_transition_transformer__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./transformers/styles/transition-transformer */ "./packages/packages/core/editor-canvas/src/transformers/styles/transition-transformer.ts");






























function initStyleTransformers() {
  _style_transformers_registry__WEBPACK_IMPORTED_MODULE_0__.styleTransformersRegistry.register('font-family', _transformers_styles_font_family_transformer__WEBPACK_IMPORTED_MODULE_15__.fontFamilyTransformer).register('size', _transformers_styles_size_transformer__WEBPACK_IMPORTED_MODULE_20__.sizeTransformer).register('grid-track-size', _transformers_styles_grid_track_size_transformer__WEBPACK_IMPORTED_MODULE_16__.gridTrackSizeTransformer).register('shadow', _transformers_styles_shadow_transformer__WEBPACK_IMPORTED_MODULE_19__.shadowTransformer).register('stroke', _transformers_styles_stroke_transformer__WEBPACK_IMPORTED_MODULE_22__.strokeTransformer).register('dimensions', (0,_transformers_styles_create_multi_props_transformer__WEBPACK_IMPORTED_MODULE_12__.createMultiPropsTransformer)(['block-start', 'block-end', 'inline-start', 'inline-end'], ({
    propKey,
    key
  }) => `${propKey}-${key}`)).register('filter', _transformers_styles_filter_transformer__WEBPACK_IMPORTED_MODULE_13__.filterTransformer).register('backdrop-filter', _transformers_styles_filter_transformer__WEBPACK_IMPORTED_MODULE_13__.filterTransformer).register('box-shadow', (0,_transformers_styles_create_combine_array_transformer__WEBPACK_IMPORTED_MODULE_11__.createCombineArrayTransformer)(',')).register('background', _transformers_styles_background_transformer__WEBPACK_IMPORTED_MODULE_9__.backgroundTransformer).register('background-overlay', _transformers_styles_background_overlay_transformer__WEBPACK_IMPORTED_MODULE_8__.backgroundOverlayTransformer).register('background-color-overlay', _transformers_styles_background_color_overlay_transformer__WEBPACK_IMPORTED_MODULE_4__.backgroundColorOverlayTransformer).register('background-image-overlay', _transformers_styles_background_image_overlay_transformer__WEBPACK_IMPORTED_MODULE_6__.backgroundImageOverlayTransformer).register('background-gradient-overlay', _transformers_styles_background_gradient_overlay_transformer__WEBPACK_IMPORTED_MODULE_5__.backgroundGradientOverlayTransformer).register('gradient-color-stop', (0,_transformers_styles_create_combine_array_transformer__WEBPACK_IMPORTED_MODULE_11__.createCombineArrayTransformer)(',')).register('color-stop', _transformers_styles_color_stop_transformer__WEBPACK_IMPORTED_MODULE_10__.colorStopTransformer).register('background-image-position-offset', _transformers_styles_position_transformer__WEBPACK_IMPORTED_MODULE_18__.positionTransformer).register('background-image-size-scale', _transformers_styles_background_image_size_scale_transformer__WEBPACK_IMPORTED_MODULE_7__.backgroundImageSizeScaleTransformer).register('image-src', _transformers_shared_image_src_transformer__WEBPACK_IMPORTED_MODULE_1__.imageSrcTransformer).register('image', _transformers_shared_image_transformer__WEBPACK_IMPORTED_MODULE_2__.imageTransformer).register('object-position', _transformers_styles_position_transformer__WEBPACK_IMPORTED_MODULE_18__.positionTransformer).register('span', _transformers_styles_span_transformer__WEBPACK_IMPORTED_MODULE_21__.spanTransformer).register('transform-origin', _transformers_styles_transform_origin_transformer__WEBPACK_IMPORTED_MODULE_25__.transformOriginTransformer).register('perspective-origin', _transformers_styles_perspective_origin_transformer__WEBPACK_IMPORTED_MODULE_17__.perspectiveOriginTransformer).register('transform-move', _transformers_styles_transform_move_transformer__WEBPACK_IMPORTED_MODULE_24__.transformMoveTransformer).register('transform-scale', _transformers_styles_transform_scale_transformer__WEBPACK_IMPORTED_MODULE_27__.transformScaleTransformer).register('transform-rotate', _transformers_styles_transform_rotate_transformer__WEBPACK_IMPORTED_MODULE_26__.transformRotateTransformer).register('transform-skew', _transformers_styles_transform_skew_transformer__WEBPACK_IMPORTED_MODULE_28__.transformSkewTransformer).register('transform-functions', _transformers_styles_transform_functions_transformer__WEBPACK_IMPORTED_MODULE_23__.transformFunctionsTransformer).register('transform', (0,_transformers_styles_create_multi_props_transformer__WEBPACK_IMPORTED_MODULE_12__.createMultiPropsTransformer)(['transform-functions', 'transform-origin', 'perspective', 'perspective-origin'], ({
    key
  }) => key === 'transform-functions' ? 'transform' : key)).register('transition', _transformers_styles_transition_transformer__WEBPACK_IMPORTED_MODULE_29__.transitionTransformer).register('layout-direction', (0,_transformers_styles_create_multi_props_transformer__WEBPACK_IMPORTED_MODULE_12__.createMultiPropsTransformer)(['row', 'column'], ({
    propKey,
    key
  }) => `${key}-${propKey}`)).register('flex', _transformers_styles_flex_transformer__WEBPACK_IMPORTED_MODULE_14__.flexTransformer).register('border-width-v2', (0,_transformers_styles_create_multi_props_transformer__WEBPACK_IMPORTED_MODULE_12__.createMultiPropsTransformer)(['block-start', 'block-end', 'inline-start', 'inline-end'], ({
    key
  }) => `border-${key}-width`)).register('border-radius-v2', (0,_transformers_styles_create_multi_props_transformer__WEBPACK_IMPORTED_MODULE_12__.createMultiPropsTransformer)(['start-start', 'start-end', 'end-start', 'end-end'], ({
    key
  }) => `border-${key}-radius`)).registerFallback(_transformers_shared_plain_transformer__WEBPACK_IMPORTED_MODULE_3__.plainTransformer);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/init.tsx":
/*!***********************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/init.tsx ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_classes_rename__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/classes-rename */ "./packages/packages/core/editor-canvas/src/components/classes-rename.tsx");
/* harmony import */ var _components_elements_overlays__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/elements-overlays */ "./packages/packages/core/editor-canvas/src/components/elements-overlays.tsx");
/* harmony import */ var _components_interactions_renderer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/interactions-renderer */ "./packages/packages/core/editor-canvas/src/components/interactions-renderer.tsx");
/* harmony import */ var _components_style_renderer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/style-renderer */ "./packages/packages/core/editor-canvas/src/components/style-renderer.tsx");
/* harmony import */ var _form_structure_enforce_form_ancestor_commands__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./form-structure/enforce-form-ancestor-commands */ "./packages/packages/core/editor-canvas/src/form-structure/enforce-form-ancestor-commands.ts");
/* harmony import */ var _form_structure_prevent_form_nesting_commands__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./form-structure/prevent-form-nesting-commands */ "./packages/packages/core/editor-canvas/src/form-structure/prevent-form-nesting-commands.ts");
/* harmony import */ var _init_settings_transformers__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./init-settings-transformers */ "./packages/packages/core/editor-canvas/src/init-settings-transformers.ts");
/* harmony import */ var _init_style_transformers__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./init-style-transformers */ "./packages/packages/core/editor-canvas/src/init-style-transformers.ts");
/* harmony import */ var _legacy_init_legacy_views__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./legacy/init-legacy-views */ "./packages/packages/core/editor-canvas/src/legacy/init-legacy-views.ts");
/* harmony import */ var _legacy_replacements_manager__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./legacy/replacements/manager */ "./packages/packages/core/editor-canvas/src/legacy/replacements/manager.ts");
/* harmony import */ var _legacy_tabs_model_extensions__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./legacy/tabs-model-extensions */ "./packages/packages/core/editor-canvas/src/legacy/tabs-model-extensions.ts");
/* harmony import */ var _mcp_canvas_mcp__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./mcp/canvas-mcp */ "./packages/packages/core/editor-canvas/src/mcp/canvas-mcp.ts");
/* harmony import */ var _mcp_mcp_description__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./mcp/mcp-description */ "./packages/packages/core/editor-canvas/src/mcp/mcp-description.ts");
/* harmony import */ var _prevent_link_in_link_commands__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./prevent-link-in-link-commands */ "./packages/packages/core/editor-canvas/src/prevent-link-in-link-commands.ts");
/* harmony import */ var _style_commands_init_style_commands__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./style-commands/init-style-commands */ "./packages/packages/core/editor-canvas/src/style-commands/init-style-commands.ts");

















function init() {
  (0,_init_style_transformers__WEBPACK_IMPORTED_MODULE_9__.initStyleTransformers)();
  (0,_style_commands_init_style_commands__WEBPACK_IMPORTED_MODULE_16__.initStyleCommands)();
  (0,_prevent_link_in_link_commands__WEBPACK_IMPORTED_MODULE_15__.initLinkInLinkPrevention)();
  (0,_form_structure_prevent_form_nesting_commands__WEBPACK_IMPORTED_MODULE_7__.initFormNestingPrevention)();
  (0,_form_structure_enforce_form_ancestor_commands__WEBPACK_IMPORTED_MODULE_6__.initFormAncestorEnforcement)();
  (0,_legacy_replacements_manager__WEBPACK_IMPORTED_MODULE_11__.initViewReplacements)();
  (0,_legacy_init_legacy_views__WEBPACK_IMPORTED_MODULE_10__.initLegacyViews)();
  (0,_init_settings_transformers__WEBPACK_IMPORTED_MODULE_8__.initSettingsTransformers)();
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'elements-overlays',
    component: _components_elements_overlays__WEBPACK_IMPORTED_MODULE_3__.ElementsOverlays
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'canvas-style-render',
    component: _components_style_renderer__WEBPACK_IMPORTED_MODULE_5__.StyleRenderer
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'canvas-interactions-render',
    component: _components_interactions_renderer__WEBPACK_IMPORTED_MODULE_4__.InteractionsRenderer
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'classes-rename',
    component: _components_classes_rename__WEBPACK_IMPORTED_MODULE_2__.ClassesRename
  });
  (0,_mcp_canvas_mcp__WEBPACK_IMPORTED_MODULE_13__.initCanvasMcp)((0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.getMCPByDomain)('canvas', {
    instructions: `Everything related to V4 ( Atomic ) canvas.
# Canvas workflow for new compositions
- Configure elements settings and styles
- Build compositions/sections out of V4 atomic elements using context aware designs using the website resources
- Get and retrieve element configuration values
`,
    docs: _mcp_mcp_description__WEBPACK_IMPORTED_MODULE_14__.mcpDescription
  }));
  (0,_legacy_tabs_model_extensions__WEBPACK_IMPORTED_MODULE_12__.initTabsModelExtensions)();
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/create-element-type.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/create-element-type.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementType: function() { return /* binding */ createElementType; },
/* harmony export */   createElementViewClassDeclaration: function() { return /* binding */ createElementViewClassDeclaration; }
/* harmony export */ });
// Technically it shouldn't have a return type annotation, but for some
// reason TypeScript can't infer the types properly when emitting DTS.
//
// See: https://github.com/microsoft/TypeScript/issues/9944#issuecomment-244448079
function createElementType(type) {
  const legacyWindow = window;
  return class extends legacyWindow.elementor.modules.elements.types.Widget {
    getType() {
      return type;
    }
    getView() {
      return createElementViewClassDeclaration();
    }
  };
}
function createElementViewClassDeclaration() {
  const legacyWindow = window;
  return class extends legacyWindow.elementor.modules.elements.views.Widget {
    // Dispatch `render` event so the overlay layer will be updated
    onRender(...args) {
      super.onRender(...args);
      this.#dispatchEvent('elementor/preview/atomic-widget/render');
      this.#dispatchPreviewEvent('elementor/element/render');
    }

    // Dispatch `destroy` event so the overlay layer will be updated
    onDestroy(...args) {
      super.onDestroy(...args);
      this.#dispatchEvent('elementor/preview/atomic-widget/destroy');
      this.#dispatchPreviewEvent('elementor/element/destroy');
    }
    attributes() {
      return {
        ...super.attributes(),
        // Mark the widget as atomic, so external APIs (such as the overlay layer) can reference it.
        'data-atomic': '',
        // Make the wrapper is non-existent in terms of CSS to mimic the frontend DOM tree.
        style: 'display: contents !important;'
      };
    }

    // Removes behaviors that are not needed for atomic widgets (that are implemented in the overlay layer).
    behaviors() {
      const disabledBehaviors = ['InlineEditing', 'Draggable', 'Resizable'];
      const behaviorsAsEntries = Object.entries(super.behaviors()).filter(([key]) => !disabledBehaviors.includes(key));
      return Object.fromEntries(behaviorsAsEntries);
    }

    // Change the drag handle because the $el is not the draggable element (`display: contents`).
    getDomElement() {
      return this.$el.find(':first-child');
    }

    // Remove the overlay, so we can use the new overlay layer.
    getHandlesOverlay() {
      return null;
    }
    #dispatchEvent(eventType) {
      window.top?.dispatchEvent(new CustomEvent(eventType, {
        detail: {
          id: this.model.get('id')
        }
      }));
    }
    #dispatchPreviewEvent(eventType) {
      const element = this.getDomElement().get(0);
      if (!element) {
        return;
      }
      legacyWindow.elementor?.$preview?.[0]?.contentWindow.dispatchEvent(new CustomEvent(eventType, {
        detail: {
          id: this.model.get('id'),
          type: this.model.get('widgetType'),
          element
        }
      }));
    }
    getContextMenuGroups() {
      return super.getContextMenuGroups().filter(group => group.name !== 'save');
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/create-nested-templated-element-type.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/create-nested-templated-element-type.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canBeNestedTemplated: function() { return /* binding */ canBeNestedTemplated; },
/* harmony export */   createNestedTemplatedElementType: function() { return /* binding */ createNestedTemplatedElementType; },
/* harmony export */   createNestedTemplatedElementView: function() { return /* binding */ createNestedTemplatedElementView; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_signalized_process__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/signalized-process */ "./packages/packages/core/editor-canvas/src/utils/signalized-process.ts");
/* harmony import */ var _create_pending_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-pending-element */ "./packages/packages/core/editor-canvas/src/legacy/create-pending-element.ts");
/* harmony import */ var _create_templated_element_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./create-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-templated-element-type.ts");
/* harmony import */ var _twig_rendering_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./twig-rendering-utils */ "./packages/packages/core/editor-canvas/src/legacy/twig-rendering-utils.ts");





function canBeNestedTemplated(element) {
  return (0,_create_templated_element_type__WEBPACK_IMPORTED_MODULE_3__.canBeTemplated)(element) && 'support_nesting' in element && !!element.support_nesting;
}
function createNestedTemplatedElementType({
  type,
  renderer,
  element,
  modelExtensions
}) {
  const legacyWindow = window;
  return class extends legacyWindow.elementor.modules.elements.types.Base {
    getType() {
      return type;
    }
    getView() {
      return createNestedTemplatedElementView({
        type,
        renderer,
        element
      });
    }
    getModel() {
      const BaseModel = legacyWindow.elementor.modules.elements.models.AtomicElementBase;
      if (modelExtensions && Object.keys(modelExtensions).length > 0) {
        return BaseModel.extend(modelExtensions);
      }
      return BaseModel;
    }
  };
}
function buildEditorAttributes(model) {
  const id = model.get('id');
  const originId = model.get('originId');
  const cid = model.cid ?? '';
  const attrs = {
    'data-model-cid': cid,
    'data-interaction-id': originId ?? id,
    'x-ignore': 'true'
  };
  return Object.entries(attrs).map(([key, value]) => `${key}="${value}"`).join(' ');
}
function buildEditorClasses(model) {
  const id = model.get('id');
  return ['elementor-element', 'elementor-element-edit-mode', `elementor-element-${id}`].join(' ');
}
function createNestedTemplatedElementView({
  type,
  renderer,
  element
}) {
  const legacyWindow = window;
  const {
    templateKey,
    baseStylesDictionary,
    resolveProps
  } = (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_4__.setupTwigRenderer)({
    type,
    renderer,
    element
  });
  const AtomicElementBaseView = legacyWindow.elementor.modules.elements.views.createAtomicElementBase(type);
  const parentRenderChildren = AtomicElementBaseView.prototype._renderChildren;
  const parentOpenEditingPanel = AtomicElementBaseView.prototype._openEditingPanel;
  const parentAddElement = AtomicElementBaseView.prototype.addElement;
  return AtomicElementBaseView.extend({
    _abortController: null,
    _lastResolvedSettingsHash: null,
    _domUpdateWasSkipped: false,
    template: false,
    attributes() {
      return {
        'data-model-cid': this.model.cid
      };
    },
    getTemplateType() {
      return 'twig';
    },
    invalidateRenderCache() {
      this._lastResolvedSettingsHash = null;
    },
    renderOnChange() {
      this.render();
    },
    render() {
      this._abortController?.abort();
      this._abortController = new AbortController();
      const process = (0,_utils_signalized_process__WEBPACK_IMPORTED_MODULE_1__.signalizedProcess)(this._abortController.signal).then(() => this._beforeRender()).then(() => this._renderTemplate())
      // Dispatch the render event after the template is ready
      .then(() => this._onTemplateReady()).then(() => this._renderChildren()).then(() => this._afterRender());
      this._currentRenderPromise = process.execute();
      return this._currentRenderPromise;
    },
    _beforeRender() {
      (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_4__.createBeforeRender)(this);
    },
    _onTemplateReady() {
      this.dispatchPreviewEvent('elementor/element/render');
    },
    _afterRender() {
      (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_4__.createAfterRender)(this);
      this.dispatchPreviewEvent('elementor/element/rendered');
      requestAnimationFrame(() => {
        this._initAlpine();
      });
      this.model.trigger('render:complete');
      window.dispatchEvent(new CustomEvent(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.ELEMENT_STYLE_CHANGE_EVENT));
    },
    async _renderTemplate() {
      const model = this.model;
      this.triggerMethod('before:render:template');
      const process = (0,_utils_signalized_process__WEBPACK_IMPORTED_MODULE_1__.signalizedProcess)(this._abortController?.signal).then((_, signal) => {
        const settings = model.get('settings').toJSON();
        return resolveProps({
          props: settings,
          signal,
          renderContext: this.getResolverRenderContext?.()
        });
      }).then(async settings => {
        const resolvedSettings = this.afterSettingsResolve(settings);
        const settingsHash = JSON.stringify(resolvedSettings);
        const settingsChanged = settingsHash !== this._lastResolvedSettingsHash;
        if (!settingsChanged && this.isRendered) {
          this._domUpdateWasSkipped = true;
          return null;
        }
        this._domUpdateWasSkipped = false;
        this._lastResolvedSettingsHash = settingsHash;
        const context = {
          id: model.get('id'),
          interaction_id: this.getInteractionId(),
          type,
          settings: resolvedSettings,
          base_styles: baseStylesDictionary,
          editor_attributes: buildEditorAttributes(model),
          editor_classes: buildEditorClasses(model),
          ...(this.getResolverRenderContext?.() ?? {})
        };
        return renderer.render(templateKey, context);
      }).then(html => {
        if (html === null) {
          return;
        }
        this._attachTwigContent(html);
      });
      await process.execute();
      this.bindUIElements();
      this.triggerMethod('render:template');
    },
    afterSettingsResolve(settings) {
      return settings;
    },
    getRenderContext() {
      return this._parent?.getRenderContext?.();
    },
    getResolverRenderContext() {
      return this._parent?.getResolverRenderContext?.();
    },
    getChildType() {
      const allowedTypes = element.allowed_child_types ?? [];
      if (allowedTypes && allowedTypes.length > 0) {
        return allowedTypes;
      }
      return AtomicElementBaseView.prototype.getChildType.call(this);
    },
    _attachTwigContent(html) {
      const $newContent = legacyWindow.jQuery(html);
      const oldEl = this.$el.get(0);
      const newEl = $newContent.get(0);
      if (!oldEl || !newEl) {
        return;
      }
      this._destroyAlpine();
      const overlayHTML = this.getHandlesOverlay()?.get(0)?.outerHTML ?? '';
      const needsTagSwap = oldEl.tagName !== newEl.tagName;
      const targetEl = needsTagSwap ? (oldEl.ownerDocument ?? document).createElement(newEl.tagName) : oldEl;
      Array.from(newEl.attributes).forEach(attr => {
        targetEl.setAttribute(attr.name, attr.value);
      });
      targetEl.innerHTML = overlayHTML + newEl.innerHTML;
      if (needsTagSwap) {
        oldEl.replaceWith(targetEl);
        this.setElement(legacyWindow.jQuery(targetEl));
      }
    },
    async _renderChildren() {
      if (this._shouldReuseChildren()) {
        (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_4__.rerenderExistingChildren)(this);
      } else {
        parentRenderChildren.call(this);
      }
      await (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_4__.waitForChildrenToComplete)(this);
      this._removeChildrenPlaceholder();
    },
    _shouldReuseChildren() {
      return this._domUpdateWasSkipped && this.children?.length > 0;
    },
    _removeChildrenPlaceholder() {
      const el = this.$el.get(0);
      if (!el) {
        return;
      }
      const placeholderComment = Array.from(el.childNodes).find(node => node.nodeType === Node.COMMENT_NODE && node.nodeValue?.trim() === 'elementor-children-placeholder');
      placeholderComment?.remove();
    },
    getChildViewContainer() {
      this.childViewContainer = '';
      return this.$el;
    },
    attachBuffer(_collectionView, buffer) {
      const el = this.$el.get(0);
      if (!el) {
        return;
      }

      // Find the placeholder comment
      const placeholderComment = Array.from(el.childNodes).find(node => node.nodeType === Node.COMMENT_NODE && node.nodeValue?.trim() === 'elementor-children-placeholder');
      if (placeholderComment) {
        // Insert children at the placeholder location and remove the placeholder
        placeholderComment.parentNode?.insertBefore(buffer, placeholderComment);
        placeholderComment.remove();
      } else {
        // Fallback: append to root
        el.append(buffer);
      }
    },
    getDomElement() {
      return this.$el;
    },
    onBeforeDestroy() {
      this._abortController?.abort();
    },
    onDestroy() {
      this.dispatchPreviewEvent('elementor/element/destroy');
    },
    _destroyAlpine() {
      const el = this.$el.get(0);
      if (!el) {
        return;
      }
      const xDataValue = el.getAttribute('x-data');
      if (!xDataValue) {
        return;
      }
      const previewWindow = el.ownerDocument?.defaultView;
      previewWindow?.Alpine?.destroyTree(el);
    },
    _initAlpine() {
      const el = this.$el.get(0);
      if (!el) {
        return;
      }
      el.removeAttribute('x-ignore');
      const xDataValue = el.getAttribute('x-data');
      if (!xDataValue) {
        return;
      }
      const previewWindow = el.ownerDocument?.defaultView;
      previewWindow?.Alpine?.initTree(el);
    },
    _doAfterRender(callback) {
      if (this.isRendered) {
        callback();
      } else {
        this.once('render', callback);
      }
    },
    _openEditingPanel(options) {
      this._doAfterRender(() => parentOpenEditingPanel.call(this, options));
    },
    addElement(data, options) {
      if (this.isRendered) {
        return parentAddElement.call(this, data, options);
      }
      return (0,_create_pending_element__WEBPACK_IMPORTED_MODULE_2__.createPendingElement)(this, data, options);
    },
    getInteractionId() {
      const originId = this.model.get('originId');
      const id = this.model.get('id');
      return originId ?? id;
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/create-pending-element.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/create-pending-element.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createPendingElement: function() { return /* binding */ createPendingElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

function createPendingElement(wrapperView, data, options = {}) {
  const parentContainer = wrapperView.getContainer();
  const model = {
    ...data
  };
  if (!model.id) {
    model.id = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.generateElementId)();
  }
  if (!model.elements) {
    model.elements = [];
  }
  const added = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.addModelToParent)(parentContainer.id, model, options);
  if (!added) {
    return undefined;
  }
  const childId = model.id;
  const childModel = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.findModelInDocument)(childId);
  if (!childModel) {
    return undefined;
  }
  const pendingContainer = {
    id: childId,
    settings: {
      get: () => ({}),
      set: () => ({}),
      toJSON: () => ({})
    },
    parent: parentContainer,
    model: childModel,
    view: undefined,
    lookup() {
      return (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(childId) ?? pendingContainer;
    }
  };
  wrapperView.once('render', () => {
    wrapperView.model?.trigger?.('navigator:add', childModel, options);
  });
  if (options.edit !== false) {
    selectChildWhenWrapperRenders(wrapperView, childId);
  }
  return {
    getContainer: () => pendingContainer
  };
}
function selectChildWhenWrapperRenders(wrapperView, childId) {
  wrapperView.once('render', () => {
    const childContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(childId);
    if (childContainer?.model?.trigger) {
      childContainer.model.trigger('request:edit');
      return;
    }
    wrapperView.model?.trigger?.('request:edit');
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/create-pro-promotion-nested-type.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/create-pro-promotion-nested-type.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createProPromotionNestedType: function() { return /* binding */ createProPromotionNestedType; }
/* harmony export */ });
/* harmony import */ var _create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create-nested-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-nested-templated-element-type.ts");

function createProPromotionNestedType({
  type,
  renderer,
  element
}) {
  if (!(0,_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_0__.canBeNestedTemplated)(element)) {
    throw new Error(`Element "${type}" is not a valid nested templated element.`);
  }
  const BaseType = (0,_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_0__.createNestedTemplatedElementType)({
    type,
    renderer,
    element: element
  });
  let PromotionView = null;
  return class extends BaseType {
    getView() {
      if (!PromotionView) {
        const BaseView = new BaseType().getView();
        PromotionView = createPromotionView(BaseView);
      }
      return PromotionView;
    }
  };
}
function createPromotionView(BaseView) {
  return class extends BaseView {
    _afterRender() {
      super._afterRender();
      const removeBtnSelector = '.e-pro-promotion-placeholder__remove-btn';
      const unlockBtnSelector = '.e-pro-promotion-placeholder__unlock-btn';
      this.$el.off('click', removeBtnSelector);
      this.$el.on('click', removeBtnSelector, e => {
        e.preventDefault();
        e.stopPropagation();
        window.$e.run('document/elements/delete', {
          container: this.container
        });
      });
      this.$el.off('click', unlockBtnSelector);
      this.$el.on('click', unlockBtnSelector, e => {
        e.stopPropagation();
      });
    }
    async _renderChildren() {}
    behaviors() {
      const disabledBehaviors = ['InlineEditing', 'Draggable', 'Resizable'];
      const behaviorsAsEntries = Object.entries(super.behaviors()).filter(([key]) => !disabledBehaviors.includes(key));
      return Object.fromEntries(behaviorsAsEntries);
    }
    getContextMenuGroups() {
      return super.getContextMenuGroups().filter(group => group.name !== 'save');
    }
    onDestroy(...args) {
      super.onDestroy(...args);
      this.$el.off('click', '.e-pro-promotion-placeholder__remove-btn');
      this.$el.off('click', '.e-pro-promotion-placeholder__unlock-btn');
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/create-templated-element-type.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/create-templated-element-type.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canBeTemplated: function() { return /* binding */ canBeTemplated; },
/* harmony export */   createTemplatedElementType: function() { return /* binding */ createTemplatedElementType; },
/* harmony export */   createTemplatedElementView: function() { return /* binding */ createTemplatedElementView; }
/* harmony export */ });
/* harmony import */ var _utils_signalized_process__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/signalized-process */ "./packages/packages/core/editor-canvas/src/utils/signalized-process.ts");
/* harmony import */ var _create_element_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./create-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-element-type.ts");
/* harmony import */ var _twig_rendering_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./twig-rendering-utils */ "./packages/packages/core/editor-canvas/src/legacy/twig-rendering-utils.ts");



function createTemplatedElementType({
  type,
  renderer,
  element
}) {
  const legacyWindow = window;
  const view = createTemplatedElementView({
    type,
    renderer,
    element
  });
  return class extends legacyWindow.elementor.modules.elements.types.Widget {
    getType() {
      return type;
    }
    getView() {
      return view;
    }
  };
}
function canBeTemplated(element) {
  return !!(element.atomic_props_schema && element.twig_templates && element.twig_main_template && element.base_styles_dictionary);
}
function createTemplatedElementView({
  type,
  renderer,
  element
}) {
  const BaseView = (0,_create_element_type__WEBPACK_IMPORTED_MODULE_1__.createElementViewClassDeclaration)();
  const {
    templateKey,
    baseStylesDictionary,
    resolveProps
  } = (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_2__.setupTwigRenderer)({
    type,
    renderer,
    element
  });
  return class extends BaseView {
    _abortController = null;
    _lastResolvedSettingsHash = null;
    _domUpdateWasSkipped = false;
    getTemplateType() {
      return 'twig';
    }
    getNamespaceKey() {
      return type;
    }
    renderOnChange() {
      this.render();
    }
    getRenderContext() {
      return this._parent?.getRenderContext?.();
    }
    getResolverRenderContext() {
      return this._parent?.getResolverRenderContext?.();
    }
    invalidateRenderCache() {
      this._lastResolvedSettingsHash = null;
    }
    render() {
      this._abortController?.abort();
      this._abortController = new AbortController();
      const process = (0,_utils_signalized_process__WEBPACK_IMPORTED_MODULE_0__.signalizedProcess)(this._abortController.signal).then(() => this._beforeRender()).then(() => this._renderTemplate()).then(() => this._renderChildren()).then(() => this._afterRender());
      this._currentRenderPromise = process.execute();
      return this._currentRenderPromise;
    }
    async _renderChildren() {
      if (this._shouldReuseChildren()) {
        (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_2__.rerenderExistingChildren)(this);
      } else {
        super._renderChildren();
      }
      await (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_2__.waitForChildrenToComplete)(this);
    }
    _shouldReuseChildren() {
      return this._domUpdateWasSkipped && this.children?.length > 0;
    }
    async _renderTemplate() {
      this.triggerMethod('before:render:template');
      const process = (0,_utils_signalized_process__WEBPACK_IMPORTED_MODULE_0__.signalizedProcess)(this._abortController?.signal).then((_, signal) => {
        const settings = this.model.get('settings').toJSON();
        return resolveProps({
          props: settings,
          signal,
          renderContext: this.getResolverRenderContext()
        });
      }).then(settings => {
        return this.afterSettingsResolve(settings);
      }).then(async settings => {
        const settingsHash = JSON.stringify(settings);
        const settingsChanged = settingsHash !== this._lastResolvedSettingsHash;
        if (!settingsChanged && this.isRendered) {
          this._domUpdateWasSkipped = true;
          return null;
        }
        this._domUpdateWasSkipped = false;
        this._lastResolvedSettingsHash = settingsHash;
        const context = {
          id: this.model.get('id'),
          interaction_id: this.getInteractionId(),
          type,
          settings,
          base_styles: baseStylesDictionary,
          ...(this.getResolverRenderContext?.() ?? {})
        };
        return renderer.render(templateKey, context);
      }).then(html => {
        if (html === null) {
          return;
        }
        this.$el.html(html);
      });
      await process.execute();
      this.bindUIElements();
      this.triggerMethod('render:template');
    }
    afterSettingsResolve(settings) {
      return settings;
    }
    _beforeRender() {
      (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_2__.createBeforeRender)(this);
    }
    _afterRender() {
      (0,_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_2__.createAfterRender)(this);
    }
    _doAfterRender(callback) {
      if (this.isRendered) {
        callback();
      } else {
        this.once('render', callback);
      }
    }
    _openEditingPanel(options) {
      this._doAfterRender(() => super._openEditingPanel(options));
    }
    getInteractionId() {
      const originId = this.model.get('originId');
      const id = this.model.get('id');
      return originId ?? id;
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/init-legacy-views.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/init-legacy-views.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   elementsLegacyTypes: function() { return /* binding */ elementsLegacyTypes; },
/* harmony export */   initLegacyViews: function() { return /* binding */ initLegacyViews; },
/* harmony export */   registerElementType: function() { return /* binding */ registerElementType; },
/* harmony export */   registerModelExtensions: function() { return /* binding */ registerModelExtensions; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _renderers_create_dom_renderer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../renderers/create-dom-renderer */ "./packages/packages/core/editor-canvas/src/renderers/create-dom-renderer.ts");
/* harmony import */ var _create_element_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./create-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-element-type.ts");
/* harmony import */ var _create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./create-nested-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-nested-templated-element-type.ts");
/* harmony import */ var _create_pro_promotion_nested_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./create-pro-promotion-nested-type */ "./packages/packages/core/editor-canvas/src/legacy/create-pro-promotion-nested-type.ts");
/* harmony import */ var _create_templated_element_type__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./create-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-templated-element-type.ts");
/* harmony import */ var _replacements_manager__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./replacements/manager */ "./packages/packages/core/editor-canvas/src/legacy/replacements/manager.ts");








const elementsLegacyTypes = {};
const modelExtensionsRegistry = {};
function registerModelExtensions(type, extensions) {
  modelExtensionsRegistry[type] = extensions;
}
function registerElementType(type, elementTypeGenerator) {
  elementsLegacyTypes[type] = elementTypeGenerator;
  if ((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateIsReady)()) {
    registerElementInLegacyManager(type, (0,_renderers_create_dom_renderer__WEBPACK_IMPORTED_MODULE_2__.createDomRenderer)());
  }
}
function initLegacyViews() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.v1ReadyEvent)(), () => {
    const widgetsCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)() ?? {};
    const renderer = (0,_renderers_create_dom_renderer__WEBPACK_IMPORTED_MODULE_2__.createDomRenderer)();
    registerProPromotionTypes(widgetsCache);
    Object.keys(widgetsCache).forEach(type => {
      registerElementInLegacyManager(type, renderer);
    });
  });
}
function registerElementInLegacyManager(type, renderer) {
  const element = ((0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)() ?? {})[type];
  if (!element?.atomic) {
    return;
  }
  const legacyWindow = window;
  const ResolvedElementType = resolveElementType(type, renderer, element);
  tryRegisterElement(legacyWindow, type, element, ResolvedElementType);
}
function registerProPromotionTypes(widgetsCache) {
  Object.entries(widgetsCache).forEach(([type, element]) => {
    if (element.meta?.is_pro_promotion) {
      registerElementType(type, options => (0,_create_pro_promotion_nested_type__WEBPACK_IMPORTED_MODULE_5__.createProPromotionNestedType)(options));
    }
  });
}
function resolveElementType(type, renderer, element) {
  if ((0,_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_4__.canBeNestedTemplated)(element)) {
    const customGenerator = elementsLegacyTypes[type];
    return customGenerator ? customGenerator({
      type,
      renderer,
      element
    }) : createNestedTemplatedType(type, renderer, element);
  }
  if (!(0,_create_templated_element_type__WEBPACK_IMPORTED_MODULE_6__.canBeTemplated)(element)) {
    return (0,_create_element_type__WEBPACK_IMPORTED_MODULE_3__.createElementType)(type);
  }
  const customGenerator = elementsLegacyTypes[type];
  return customGenerator ? customGenerator({
    type,
    renderer,
    element
  }) : (0,_replacements_manager__WEBPACK_IMPORTED_MODULE_7__.createTemplatedElementTypeWithReplacements)({
    type,
    renderer,
    element
  });
}
function tryRegisterElement(legacyWindow, type, element, ResolvedElementType) {
  const shouldBeRegistered = (0,_create_templated_element_type__WEBPACK_IMPORTED_MODULE_6__.canBeTemplated)(element) || (0,_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_4__.canBeNestedTemplated)(element);
  if (!shouldBeRegistered) {
    return;
  }
  const elementsManager = legacyWindow.elementor.elementsManager;
  const isAlreadyRegistered = Boolean(elementsManager.getElementTypeClass(type));
  try {
    elementsManager.registerElementType(new ResolvedElementType());
  } catch {
    const canOverrideExisting = (0,_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_4__.canBeNestedTemplated)(element) && isAlreadyRegistered;
    if (canOverrideExisting) {
      elementsManager.elementTypes[type] = new ResolvedElementType();
    }
  }
}
function createNestedTemplatedType(type, renderer, element) {
  return (0,_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_4__.createNestedTemplatedElementType)({
    type,
    renderer,
    element,
    modelExtensions: modelExtensionsRegistry[type]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/replacements/base.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/replacements/base.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReplacementBase: function() { return /* binding */ ReplacementBase; },
/* harmony export */   TRIGGER_TIMING: function() { return /* binding */ TRIGGER_TIMING; }
/* harmony export */ });
const TRIGGER_TIMING = {
  before: 'before',
  after: 'after',
  never: 'never'
};
class ReplacementBase {
  constructor(settings) {
    this.getSetting = settings.getSetting;
    this.setSetting = settings.setSetting;
    this.element = settings.element;
    this.type = settings.type;
    this.id = settings.id;
    this.refreshView = settings.refreshView;
    this.reactRoot = settings.reactRoot;
    this.reactContainer = settings.reactContainer;
  }
  static getTypes() {
    return null;
  }
  shouldRenderReplacement() {
    return true;
  }
  originalMethodsToTrigger() {
    return {
      _beforeRender: TRIGGER_TIMING.before,
      _afterRender: TRIGGER_TIMING.after,
      renderOnChange: TRIGGER_TIMING.never,
      onDestroy: TRIGGER_TIMING.never,
      render: TRIGGER_TIMING.never
    };
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/canvas-inline-editor.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/canvas-inline-editor.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CanvasInlineEditor: function() { return /* binding */ CanvasInlineEditor; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/react/dist/floating-ui.react.mjs");
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs");
/* harmony import */ var _floating_ui_react__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @floating-ui/react */ "./node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs");
/* harmony import */ var _components_outline_overlay__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../components/outline-overlay */ "./packages/packages/core/editor-canvas/src/components/outline-overlay.tsx");
/* harmony import */ var _inline_editing_utils__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./inline-editing-utils */ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-utils.ts");







const CanvasInlineEditor = ({
  elementClasses,
  initialValue,
  expectedTag,
  rootElement,
  contentElement,
  id,
  setValue,
  requestDestroy
}) => {
  const [active, setActive] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [editor, setEditor] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const {
    onSelectionEnd,
    anchor: toolbarAnchor,
    clearAnchor
  } = (0,_inline_editing_utils__WEBPACK_IMPORTED_MODULE_7__.useRenderToolbar)(rootElement.ownerDocument, id);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!active) {
      clearAnchor();
      requestDestroy();
    }
  }, [active, clearAnchor, requestDestroy]);
  const dismiss = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setEditor(null);
    setActive(false);
  }, []);
  (0,_inline_editing_utils__WEBPACK_IMPORTED_MODULE_7__.useOnClickOutsideIframe)(dismiss);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const ownerDocument = contentElement.ownerDocument;
    const handleClickAway = event => {
      if (contentElement.contains(event.target)) {
        return;
      }
      dismiss();
    };
    ownerDocument.addEventListener('mousedown', handleClickAway);
    return () => ownerDocument.removeEventListener('mousedown', handleClickAway);
  }, [contentElement, dismiss]);
  if (!active) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InlineEditingOverlay, {
    expectedTag: expectedTag,
    rootElement: rootElement,
    id: id
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.InlineEditor, {
    onEditorCreate: setEditor,
    mountElement: contentElement,
    editorProps: {
      attributes: {
        style: 'outline: none; display: inherit; justify-content: inherit; align-items: inherit; flex-direction: inherit; text-align: inherit;'
      }
    },
    elementClasses: elementClasses,
    value: initialValue,
    setValue: setValue,
    onBlur: dismiss,
    autofocus: true,
    onSelectionEnd: onSelectionEnd
  }), toolbarAnchor && editor && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InlineEditingToolbar, {
    anchor: toolbarAnchor,
    editor: editor,
    id: id
  }));
};
const InlineEditingOverlay = ({
  expectedTag,
  rootElement,
  id
}) => {
  const inlineEditedElement = (0,_inline_editing_utils__WEBPACK_IMPORTED_MODULE_7__.getInlineEditorElement)(rootElement, expectedTag);
  const [overlayRefElement, setOverlayElement] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(inlineEditedElement);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setOverlayElement((0,_inline_editing_utils__WEBPACK_IMPORTED_MODULE_7__.getInlineEditorElement)(rootElement, expectedTag));
  }, [expectedTag, rootElement]);
  return overlayRefElement ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_outline_overlay__WEBPACK_IMPORTED_MODULE_6__.OutlineOverlay, {
    element: overlayRefElement,
    id: id,
    isSelected: true
  }) : null;
};
const InlineEditingToolbar = ({
  anchor,
  editor,
  id
}) => {
  const {
    refs,
    floatingStyles
  } = (0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_3__.useFloating)({
    placement: 'top',
    strategy: 'fixed',
    transform: false,
    whileElementsMounted: _floating_ui_react__WEBPACK_IMPORTED_MODULE_4__.autoUpdate,
    middleware: [_inline_editing_utils__WEBPACK_IMPORTED_MODULE_7__.horizontalShifterMiddleware, (0,_floating_ui_react__WEBPACK_IMPORTED_MODULE_5__.flip)()]
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect)(() => {
    refs.setReference(anchor);
    return () => refs.setReference(null);
  }, [anchor, refs]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_floating_ui_react__WEBPACK_IMPORTED_MODULE_3__.FloatingPortal, {
    id: _components_outline_overlay__WEBPACK_IMPORTED_MODULE_6__.CANVAS_WRAPPER_ID
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    ref: refs.setFloating,
    role: "presentation",
    style: {
      ...floatingStyles,
      pointerEvents: 'none'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.InlineEditorToolbar, {
    editor: editor,
    elementId: id
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-elements.tsx":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-elements.tsx ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ InlineEditingReplacement; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../base */ "./packages/packages/core/editor-canvas/src/legacy/replacements/base.ts");
/* harmony import */ var _canvas_inline_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./canvas-inline-editor */ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/canvas-inline-editor.tsx");
/* harmony import */ var _inline_editing_eligibility__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./inline-editing-eligibility */ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-eligibility.ts");
/* harmony import */ var _inline_editing_utils__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./inline-editing-utils */ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-utils.ts");









const HISTORY_DEBOUNCE_WAIT = 800;
class InlineEditingReplacement extends _base__WEBPACK_IMPORTED_MODULE_5__.ReplacementBase {
  handlerAttached = false;
  editing = false;
  getReplacementKey() {
    return 'inline-editing';
  }
  static getTypes() {
    return Object.keys(_inline_editing_utils__WEBPACK_IMPORTED_MODULE_8__.INLINE_EDITING_PROPERTY_PER_TYPE);
  }
  isEditingModeActive() {
    return this.editing;
  }
  shouldRenderReplacement() {
    return this.isInlineEditingEligible() && (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.getCurrentEditMode)() === 'edit';
  }
  handleRenderInlineEditor = () => {
    if (this.isEditingModeActive() || !this.isInlineEditingEligible()) {
      return;
    }
    this.renderInlineEditor();
  };
  renderOnChange() {
    if (this.isEditingModeActive()) {
      return;
    }
    this.refreshView();
  }
  onDestroy() {
    this.resetInlineEditorRoot();
  }
  _beforeRender() {
    this.resetInlineEditorRoot();
  }
  _afterRender() {
    if (this.isInlineEditingEligible() && !this.handlerAttached) {
      this.element.addEventListener('click', this.handleRenderInlineEditor);
      this.handlerAttached = true;
    }
  }
  originalMethodsToTrigger() {
    const before = this.isEditingModeActive() ? _base__WEBPACK_IMPORTED_MODULE_5__.TRIGGER_TIMING.never : _base__WEBPACK_IMPORTED_MODULE_5__.TRIGGER_TIMING.before;
    const after = this.isEditingModeActive() ? _base__WEBPACK_IMPORTED_MODULE_5__.TRIGGER_TIMING.never : _base__WEBPACK_IMPORTED_MODULE_5__.TRIGGER_TIMING.after;
    return {
      _beforeRender: before,
      _afterRender: after,
      renderOnChange: after,
      onDestroy: _base__WEBPACK_IMPORTED_MODULE_5__.TRIGGER_TIMING.after,
      render: before
    };
  }
  resetInlineEditorRoot() {
    this.element.removeEventListener('click', this.handleRenderInlineEditor);
    this.handlerAttached = false;
    this.reactRoot.render(null);
    this.editing = false;
  }
  unmountInlineEditor() {
    this.resetInlineEditorRoot();
    this.refreshView();
  }
  isInlineEditingEligible() {
    const settingKey = this.getInlineEditablePropertyName();
    const rawValue = this.getSetting(settingKey);
    return (0,_inline_editing_eligibility__WEBPACK_IMPORTED_MODULE_7__.isInlineEditingAllowed)({
      rawValue,
      propTypeFromSchema: this.getInlineEditablePropType()
    });
  }
  getInlineEditablePropertyName() {
    return _inline_editing_utils__WEBPACK_IMPORTED_MODULE_8__.INLINE_EDITING_PROPERTY_PER_TYPE[this.type] ?? '';
  }
  getInlineEditablePropType() {
    const propSchema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementType)(this.type)?.propsSchema;
    const propertyName = this.getInlineEditablePropertyName();
    return propSchema?.[propertyName] ?? null;
  }
  getInlineEditablePropValue() {
    const prop = this.getInlineEditablePropType();
    const settingKey = this.getInlineEditablePropertyName();
    return this.getSetting(settingKey) ?? prop?.default ?? null;
  }
  getExtractedContentValue() {
    const propValue = this.getInlineEditablePropValue();
    const extracted = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.htmlV3PropTypeUtil.extract(propValue);
    return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.extract(extracted?.content ?? null) ?? '';
  }
  setContentValue(value) {
    const settingKey = this.getInlineEditablePropertyName();
    const html = value || '';
    const parsed = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.parseHtmlChildren)(html);
    const valueToSave = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.htmlV3PropTypeUtil.create({
      content: parsed.content ? _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.create(parsed.content) : null,
      children: parsed.children
    });
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.undoable)({
      do: () => {
        const prevValue = this.getInlineEditablePropValue();
        this.runCommand(settingKey, valueToSave);
        return prevValue;
      },
      undo: (_, prevValue) => {
        this.runCommand(settingKey, prevValue ?? null);
      }
    }, {
      title: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementLabel)(this.id),
      // translators: %s is the name of the property that was edited.
      subtitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('%s edited', 'elementor').replace('%s', this.getInlineEditablePropTypeKey() ?? 'Inline editing'),
      debounce: {
        wait: HISTORY_DEBOUNCE_WAIT
      }
    })();
  }
  getInlineEditablePropTypeKey() {
    const propType = this.getInlineEditablePropType();
    if (!propType) {
      return null;
    }
    if (propType.kind === 'union') {
      const textKeys = [_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.htmlV3PropTypeUtil.key, _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.key];
      for (const key of textKeys) {
        if (propType.prop_types[key]) {
          return key;
        }
      }
      return null;
    }
    if ('key' in propType && typeof propType.key === 'string') {
      return propType.key;
    }
    return null;
  }
  runCommand(key, value) {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.__privateRunCommandSync)('document/elements/set-settings', {
      container: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(this.id),
      settings: {
        [key]: value
      }
    }, {
      internal: true
    });
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.__privateRunCommandSync)('document/save/set-is-modified', {
      status: true
    }, {
      internal: true
    });
  }
  getExpectedTag() {
    const tagPropType = this.getTagPropType();
    const tagSettingKey = 'tag';
    return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.extract(this.getSetting(tagSettingKey) ?? null) ?? _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.extract(tagPropType?.default ?? null) ?? null;
  }
  getTagPropType() {
    const propsSchema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementType)(this.type)?.propsSchema;
    if (!propsSchema?.tag) {
      return null;
    }
    const tagPropType = propsSchema.tag ?? null;
    if (tagPropType.kind === 'union') {
      return tagPropType.prop_types.string ?? null;
    }
    return tagPropType;
  }
  renderInlineEditor() {
    if (this.isEditingModeActive()) {
      this.resetInlineEditorRoot();
    }
    const contentElement = this.element.children?.[0];
    if (!contentElement) {
      return;
    }
    const elementClasses = contentElement.classList.toString();
    const propValue = this.getExtractedContentValue();
    const expectedTag = this.getExpectedTag();
    contentElement.innerHTML = '';
    this.editing = true;
    this.reactRoot.render(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_canvas_inline_editor__WEBPACK_IMPORTED_MODULE_6__.CanvasInlineEditor, {
      elementClasses: elementClasses,
      initialValue: propValue,
      expectedTag: expectedTag,
      rootElement: this.element,
      contentElement: contentElement,
      id: this.id,
      setValue: this.setContentValue.bind(this),
      requestDestroy: this.unmountInlineEditor.bind(this)
    }));
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-eligibility.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-eligibility.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isInlineEditingAllowed: function() { return /* binding */ isInlineEditingAllowed; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);

const hasKey = propType => {
  return 'key' in propType;
};
const TEXT_PROP_TYPE_KEYS = new Set([_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.htmlV3PropTypeUtil.key, _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.key]);
const isCoreTextPropTypeKey = key => {
  return TEXT_PROP_TYPE_KEYS.has(key);
};
const isAllowedBySchema = propTypeFromSchema => {
  if (!propTypeFromSchema) {
    return false;
  }
  if (hasKey(propTypeFromSchema) && isCoreTextPropTypeKey(propTypeFromSchema.key)) {
    return true;
  }
  if (propTypeFromSchema.kind !== 'union') {
    return false;
  }
  return [...TEXT_PROP_TYPE_KEYS].some(key => propTypeFromSchema.prop_types[key]);
};
const isInlineEditingAllowed = ({
  rawValue,
  propTypeFromSchema
}) => {
  if (rawValue === null || rawValue === undefined) {
    return isAllowedBySchema(propTypeFromSchema);
  }
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.htmlV3PropTypeUtil.isValid(rawValue) || _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.isValid(rawValue);
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-utils.ts":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-utils.ts ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDITOR_WRAPPER_SELECTOR: function() { return /* binding */ EDITOR_WRAPPER_SELECTOR; },
/* harmony export */   INLINE_EDITING_PROPERTY_PER_TYPE: function() { return /* binding */ INLINE_EDITING_PROPERTY_PER_TYPE; },
/* harmony export */   getInlineEditorElement: function() { return /* binding */ getInlineEditorElement; },
/* harmony export */   getToolbarAnchor: function() { return /* binding */ getToolbarAnchor; },
/* harmony export */   getWidgetType: function() { return /* binding */ getWidgetType; },
/* harmony export */   horizontalShifterMiddleware: function() { return /* binding */ horizontalShifterMiddleware; },
/* harmony export */   legacyWindow: function() { return /* binding */ legacyWindow; },
/* harmony export */   removeToolbarAnchor: function() { return /* binding */ removeToolbarAnchor; },
/* harmony export */   useOnClickOutsideIframe: function() { return /* binding */ useOnClickOutsideIframe; },
/* harmony export */   useRenderToolbar: function() { return /* binding */ useRenderToolbar; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const TOP_BAR_SELECTOR = '#elementor-editor-wrapper-v2';
const NAVIGATOR_SELECTOR = '#elementor-navigator';
const EDITING_PANEL = '#elementor-panel';
const EDITOR_ELEMENTS_OUT_OF_IFRAME = [TOP_BAR_SELECTOR, NAVIGATOR_SELECTOR, EDITING_PANEL];
const EDITOR_WRAPPER_SELECTOR = 'inline-editor-wrapper';
const TOOLBAR_ANCHOR_ID_PREFIX = 'inline-editing-toolbar-anchor';
const TOOLBAR_ANCHOR_STATIC_STYLES = {
  backgroundColor: 'transparent',
  border: 'none',
  outline: 'none',
  boxShadow: 'none',
  padding: '0',
  margin: '0',
  borderRadius: '0',
  overflow: 'hidden',
  opacity: '0',
  pointerEvents: 'none',
  position: 'absolute',
  display: 'block'
};
const INLINE_EDITING_PROPERTY_PER_TYPE = {
  'e-button': 'text',
  'e-form-label': 'text',
  'e-heading': 'title',
  'e-paragraph': 'paragraph',
  'e-form-submit-button': 'text'
};
const legacyWindow = window;
const getWidgetType = container => {
  return container?.model?.get('widgetType') ?? container?.model?.get('elType') ?? null;
};
const getInlineEditorElement = (elementWrapper, expectedTag) => {
  return !expectedTag ? null : elementWrapper.querySelector(expectedTag);
};

// Elements out of iframe and canvas don't trigger "onClickAway" which unmounts the editor
// since they are not part of the iframes owner document.
// We need to manually add listeners to these elements to unmount the editor when they are clicked.
const useOnClickOutsideIframe = handleUnmount => {
  const asyncUnmountInlineEditor = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => queueMicrotask(handleUnmount), [handleUnmount]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    EDITOR_ELEMENTS_OUT_OF_IFRAME.forEach(selector => document?.querySelector(selector)?.addEventListener('mousedown', asyncUnmountInlineEditor));
    return () => EDITOR_ELEMENTS_OUT_OF_IFRAME.forEach(selector => document?.querySelector(selector)?.removeEventListener('mousedown', asyncUnmountInlineEditor));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
};
const useRenderToolbar = (ownerDocument, id) => {
  const [anchor, setAnchor] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!anchor) {
      removeToolbarAnchor(ownerDocument, id);
    }
  }, [anchor, ownerDocument, id]);
  const onSelectionEnd = view => {
    const hasSelection = !view.state.selection.empty;
    removeToolbarAnchor(ownerDocument, id);
    if (hasSelection) {
      setAnchor(createAnchorBasedOnSelection(ownerDocument, id));
    } else {
      setAnchor(null);
    }
  };
  const clearAnchor = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setAnchor(null);
  }, []);
  return {
    onSelectionEnd,
    anchor,
    clearAnchor
  };
};
const createAnchorBasedOnSelection = (ownerDocument, id) => {
  const frameWindow = ownerDocument.defaultView;
  const selection = frameWindow?.getSelection();
  if (!selection) {
    return null;
  }
  const range = selection.getRangeAt(0);
  const selectionRect = range.getBoundingClientRect();
  const bodyRect = ownerDocument.body.getBoundingClientRect();
  const toolbarAnchor = ownerDocument.createElement('span');
  styleToolbarAnchor(toolbarAnchor, selectionRect, bodyRect);
  toolbarAnchor.setAttribute('id', getToolbarAnchorId(id));
  ownerDocument.body.appendChild(toolbarAnchor);
  return toolbarAnchor;
};
const removeToolbarAnchor = (ownerDocument, id) => {
  const toolbarAnchor = getToolbarAnchor(ownerDocument, id);
  if (toolbarAnchor) {
    ownerDocument.body.removeChild(toolbarAnchor);
  }
};
const getToolbarAnchorId = id => `${TOOLBAR_ANCHOR_ID_PREFIX}-${id}`;
const getToolbarAnchor = (ownerDocument, id) => ownerDocument.getElementById(getToolbarAnchorId(id));
const styleToolbarAnchor = (anchor, selectionRect, bodyRect) => {
  const {
    width,
    height
  } = selectionRect;
  Object.assign(anchor.style, {
    ...TOOLBAR_ANCHOR_STATIC_STYLES,
    top: `${selectionRect.top - bodyRect.top}px`,
    left: `${selectionRect.left - bodyRect.left}px`,
    width: `${width}px`,
    height: `${height}px`
  });
};
const horizontalShifterMiddleware = {
  name: 'horizontalShifter',
  fn(state) {
    const {
      x: left,
      y: top,
      elements: {
        reference: anchor,
        floating
      }
    } = state;
    const newState = {
      ...state,
      x: left,
      y: top
    };
    const isLeftOverflown = left < 0;
    if (isLeftOverflown) {
      newState.x = 0;
      return newState;
    }
    const anchorRect = anchor.getBoundingClientRect();
    const right = left + floating.offsetWidth;
    const documentWidth = anchor.ownerDocument.body.offsetWidth;
    const isRightOverflown = right > documentWidth && anchorRect.right < right;
    if (isRightOverflown) {
      const diff = right - documentWidth;
      newState.x = left - diff;
      return newState;
    }
    return newState;
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/replacements/manager.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/replacements/manager.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createTemplatedElementTypeWithReplacements: function() { return /* binding */ createTemplatedElementTypeWithReplacements; },
/* harmony export */   createViewWithReplacements: function() { return /* binding */ createViewWithReplacements; },
/* harmony export */   getReplacement: function() { return /* binding */ getReplacement; },
/* harmony export */   initViewReplacements: function() { return /* binding */ initViewReplacements; },
/* harmony export */   registerReplacement: function() { return /* binding */ registerReplacement; }
/* harmony export */ });
/* harmony import */ var react_dom_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-dom/client */ "./node_modules/react-dom/client.js");
/* harmony import */ var _create_templated_element_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-templated-element-type.ts");
/* harmony import */ var _inline_editing_inline_editing_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./inline-editing/inline-editing-elements */ "./packages/packages/core/editor-canvas/src/legacy/replacements/inline-editing/inline-editing-elements.tsx");



const replacements = new Map();
const initViewReplacements = () => {
  registerReplacement(_inline_editing_inline_editing_elements__WEBPACK_IMPORTED_MODULE_2__["default"]);
};
const registerReplacement = replacement => {
  const types = replacement.getTypes();
  if (!types) {
    return;
  }
  types.forEach(type => {
    replacements.set(type, replacement);
  });
};
const getReplacement = type => {
  return replacements.get(type) ?? null;
};
const createViewWithReplacements = options => {
  const TemplatedView = (0,_create_templated_element_type__WEBPACK_IMPORTED_MODULE_1__.createTemplatedElementView)(options);
  return class extends TemplatedView {
    #replacement = null;
    #config;
    #reactContainer;
    #reactRoot;
    constructor(...args) {
      super(...args);
      const settings = this.model.get('settings');
      this.#reactContainer = this.el.ownerDocument.createElement('div');
      this.#reactContainer.style.display = 'none';
      this.el.ownerDocument.body.appendChild(this.#reactContainer);
      this.#reactRoot = (0,react_dom_client__WEBPACK_IMPORTED_MODULE_0__.createRoot)(this.#reactContainer);
      this.#config = {
        getSetting: settings.get.bind(settings),
        setSetting: settings.set.bind(settings),
        element: this.el,
        type: this?.model?.get('widgetType') ?? this.container?.model?.get('elType') ?? null,
        id: this?.model?.get('id') ?? null,
        refreshView: this.refreshView.bind(this),
        reactRoot: this.#reactRoot,
        reactContainer: this.#reactContainer
      };
    }
    refreshView() {
      this.invalidateRenderCache?.();
      this.render();
    }
    renderOnChange() {
      this.#triggerAltMethod('renderOnChange');
    }
    render() {
      const config = this.#config;
      const widgetType = config.type;
      const ReplacementClass = widgetType ? getReplacement(widgetType) : null;
      if (ReplacementClass && !this.#replacement) {
        this.#replacement = new ReplacementClass(config);
      }
      this.#triggerAltMethod('render');
    }
    onDestroy() {
      this.#triggerAltMethod('onDestroy');
      this.#reactRoot.unmount();
      this.#reactContainer.remove();
    }
    _afterRender() {
      this.#triggerAltMethod('_afterRender');
    }
    _beforeRender() {
      this.#triggerAltMethod('_beforeRender');
    }
    #triggerAltMethod(methodKey) {
      const baseMethod = TemplatedView.prototype[methodKey].bind(this);
      const shouldReplace = this.#replacement?.shouldRenderReplacement();
      const altMethod = shouldReplace && this.#replacement?.[methodKey]?.bind(this.#replacement);
      if (!altMethod || !shouldReplace) {
        return baseMethod();
      }
      const renderTiming = this.#replacement?.originalMethodsToTrigger()[methodKey] ?? 'never';
      if (renderTiming === 'before') {
        baseMethod();
      }
      altMethod();
      if (renderTiming === 'after') {
        baseMethod();
      }
    }
  };
};
const createTemplatedElementTypeWithReplacements = ({
  type,
  renderer,
  element
}) => {
  const legacyWindow = window;
  const view = createViewWithReplacements({
    type,
    renderer,
    element
  });
  return class extends legacyWindow.elementor.modules.elements.types.Widget {
    getType() {
      return type;
    }
    getView() {
      return view;
    }
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/tabs-model-extensions.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/tabs-model-extensions.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initTabsModelExtensions: function() { return /* binding */ initTabsModelExtensions; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _init_legacy_views__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./init-legacy-views */ "./packages/packages/core/editor-canvas/src/legacy/init-legacy-views.ts");


const tabModelExtensions = {
  modifyDefaultChildren(elements) {
    if (!Array.isArray(elements) || elements.length === 0) {
      return elements;
    }
    const [paragraph] = elements;
    const position = this.get('editor_settings')?.initial_position;
    if (!position || !paragraph || typeof paragraph !== 'object') {
      return elements;
    }
    const paragraphElement = paragraph;
    const updatedParagraph = {
      ...paragraphElement,
      settings: {
        ...paragraphElement.settings,
        paragraph: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.htmlV3PropTypeUtil.create({
          content: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.create(`Tab ${position}`),
          children: []
        })
      }
    };
    return [updatedParagraph, ...elements.slice(1)];
  }
};
function initTabsModelExtensions() {
  (0,_init_legacy_views__WEBPACK_IMPORTED_MODULE_1__.registerModelExtensions)('e-tab', tabModelExtensions);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/twig-rendering-utils.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/twig-rendering-utils.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createAfterRender: function() { return /* binding */ createAfterRender; },
/* harmony export */   createBeforeRender: function() { return /* binding */ createBeforeRender; },
/* harmony export */   rerenderExistingChildren: function() { return /* binding */ rerenderExistingChildren; },
/* harmony export */   setupTwigRenderer: function() { return /* binding */ setupTwigRenderer; },
/* harmony export */   waitForChildrenToComplete: function() { return /* binding */ waitForChildrenToComplete; }
/* harmony export */ });
/* harmony import */ var _renderers_create_props_resolver__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../renderers/create-props-resolver */ "./packages/packages/core/editor-canvas/src/renderers/create-props-resolver.ts");
/* harmony import */ var _settings_transformers_registry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../settings-transformers-registry */ "./packages/packages/core/editor-canvas/src/settings-transformers-registry.ts");


function setupTwigRenderer({
  renderer,
  element
}) {
  const templateKey = element.twig_main_template;
  const baseStylesDictionary = element.base_styles_dictionary;
  Object.entries(element.twig_templates).forEach(([key, template]) => {
    renderer.register(key, template);
  });
  const resolveProps = (0,_renderers_create_props_resolver__WEBPACK_IMPORTED_MODULE_0__.createPropsResolver)({
    transformers: _settings_transformers_registry__WEBPACK_IMPORTED_MODULE_1__.settingsTransformersRegistry,
    schema: element.atomic_props_schema
  });
  return {
    templateKey,
    baseStylesDictionary,
    resolveProps
  };
}
function createBeforeRender(view) {
  view._ensureViewIsIntact();
  view._isRendering = true;
  view.resetChildViewContainer();
  view.triggerMethod('before:render', view);
}
function createAfterRender(view) {
  view._isRendering = false;
  view.isRendered = true;
  view.triggerMethod('render', view);
}
function rerenderExistingChildren(view) {
  view.children?.each(childView => {
    childView.render();
  });
}
async function waitForChildrenToComplete(view) {
  const promises = [];
  view.children?.each(childView => {
    if (childView._currentRenderPromise) {
      promises.push(childView._currentRenderPromise);
    }
  });
  if (promises.length > 0) {
    await Promise.all(promises);
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/legacy/types.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/legacy/types.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/canvas-mcp.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/canvas-mcp.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initCanvasMcp: function() { return /* binding */ initCanvasMcp; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _resources_available_widgets_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./resources/available-widgets-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/available-widgets-resource.ts");
/* harmony import */ var _resources_breakpoints_resource__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./resources/breakpoints-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/breakpoints-resource.ts");
/* harmony import */ var _resources_document_structure_resource__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./resources/document-structure-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/document-structure-resource.ts");
/* harmony import */ var _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./resources/dynamic-tags-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts");
/* harmony import */ var _resources_editor_state_resource__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./resources/editor-state-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/editor-state-resource.ts");
/* harmony import */ var _resources_general_context_resource__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./resources/general-context-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/general-context-resource.ts");
/* harmony import */ var _resources_selected_element_resource__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./resources/selected-element-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/selected-element-resource.ts");
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");
/* harmony import */ var _tools_build_composition_tool__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./tools/build-composition/tool */ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/tool.ts");
/* harmony import */ var _tools_configure_element_tool__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./tools/configure-element/tool */ "./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/tool.ts");
/* harmony import */ var _tools_get_element_config_tool__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./tools/get-element-config/tool */ "./packages/packages/core/editor-canvas/src/mcp/tools/get-element-config/tool.ts");
/* harmony import */ var _utils_resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./utils/resolve-dynamic-tag */ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-dynamic-tag.ts");













const initCanvasMcp = reg => {
  // TODO: Remove this comment once 4.2 released
  // NOTE: Style schema removed in favor of css-to-schema functionality [ED-24441]
  // Reference code can be found at any commit prior to `d338e816f0c97b90b52fe2f1ef0bfe2aad816ab0`
  _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.Schema.setDynamicTagNamesResolver(_utils_resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_12__.getDynamicTagNamesByCategories);
  (0,_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_8__.initWidgetsSchemaResource)(reg);
  (0,_resources_available_widgets_resource__WEBPACK_IMPORTED_MODULE_1__.initAvailableWidgetsResource)(reg);
  (0,_resources_document_structure_resource__WEBPACK_IMPORTED_MODULE_3__.initDocumentStructureResource)(reg);
  (0,_resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_4__.initDynamicTagsResource)(reg);
  (0,_resources_selected_element_resource__WEBPACK_IMPORTED_MODULE_7__.initSelectedElementResource)(reg);
  (0,_resources_editor_state_resource__WEBPACK_IMPORTED_MODULE_5__.initEditorStateResource)(reg);
  (0,_resources_general_context_resource__WEBPACK_IMPORTED_MODULE_6__.initGeneralContextResource)(reg);
  (0,_tools_build_composition_tool__WEBPACK_IMPORTED_MODULE_9__.initBuildCompositionsTool)(reg);
  (0,_tools_get_element_config_tool__WEBPACK_IMPORTED_MODULE_11__.initGetElementConfigTool)(reg);
  (0,_tools_configure_element_tool__WEBPACK_IMPORTED_MODULE_10__.initConfigureElementTool)(reg);
  (0,_resources_breakpoints_resource__WEBPACK_IMPORTED_MODULE_2__.initBreakpointsResource)(reg);
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/mcp-description.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/mcp-description.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   mcpDescription: function() { return /* binding */ mcpDescription; }
/* harmony export */ });
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");

const ELEMENT_SCHEMA_URI = _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_0__.WIDGET_SCHEMA_URI.replace('{widgetType}', 'element-schema');
const mcpDescription = `Elementor Canvas MCP
This MCP enables creation, configuration, and styling of elements on the Elementor canvas using the build_composition tool.

# Core Concepts

## PropValues Structure
All data in Elementor uses PropValues - a typed wrapper for values:
\`\`\`json
{
  "$$type": "the-prop-type-schema-kind",
  "value": "the-actual-value-as-defined-for-the-propType"
}
\`\`\`
The \`$$type\` defines how Elementor interprets the value. Providing the correct \`$$type\` is critical - incorrect types will be rejected.

## Design System Resources
- **Global Variables**: Reusable colors, sizes, and fonts (\`elementor://global-variables\`)
- **Global Classes**: Reusable style sets that can be applied to elements (\`elementor://global-classes\`)
- **Widget Schemas**: Configuration options for each widget type (\`${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_0__.WIDGET_SCHEMA_URI}\`)

# Building Compositions with build_composition

The \`build_composition\` tool is the primary way to create elements. It accepts structure (XML), configuration, and styling in a single operation.

## Complete Workflow

### 1. Parse User Requirements
Understand what needs to be built: structure, content, and styling.

### 2. Check Global Resources FIRST
Always check existing resources before building:
- List \`elementor://global-variables\` for available variables (colors, sizes, fonts)
- List \`elementor://global-classes\` for available style sets
- **Always prefer using existing global resources over creating inline styles**

### 3. Retrieve Widget Schemas
For each widget you'll use:
- List \`${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_0__.WIDGET_SCHEMA_URI}\` to see available widgets
- Retrieve configuration schema from \`${ELEMENT_SCHEMA_URI}\` for each widget
- Check the \`llm_guidance\` property for container nesting, \`default_styles\`, and \`default_settings\` (omit default_settings from elementConfig unless the user asks to change them)

### 4. Build XML Structure
Create valid XML with configuration-ids:
- Each element must have a unique \`configuration-id\` attribute
- No text nodes, classes, or IDs in XML - structure only
- Example:
\`\`\`xml
<e-container configuration-id="container-1">
  <e-heading configuration-id="heading-1" />
  <e-text configuration-id="text-1" />
</e-container>
\`\`\`

### 5. Create elementConfig
Map each configuration-id to its widget properties using PropValues:
- Use correct \`$$type\` matching the widget's schema
- Use global variables in PropValues where applicable
- Example:
\`\`\`json
{
  "heading-1": {
    "text": { "$$type": "string", "value": "Welcome" },
    "tag": { "$$type": "string", "value": "h1" }
  }
}
\`\`\`

### 6. Create style
Map each configuration-id to raw CSS declarations (property → value strings). The server converts them to native styles and stores any unconvertible declarations as the element custom CSS.
- Example:
\`\`\`json
{
  "heading-1": "color: #1a1a1a; font-size: 2rem;"
  }
}
\`\`\`

### 7. Execute build_composition
Call the tool with your XML structure, elementConfig, and style. The response will contain the created element IDs.
At the response you will also find llm_instructions for you to do afterwards, read and follow them!

## Key Points

- **PropValue Types**: Arrays that accept union types are typed as mixed arrays
- **Visual Sizing**: Widget sizes MUST be defined via the style parameter (raw CSS). Widget properties like image "size" control resolution, not visual appearance
- **Global Variables**: Reference by label/name: (e.g. var(--card-background-color)
- **Naming Conventions**: Use meaningful, purpose-based names (e.g., "primary-button", "heading-large"), not value-based names (e.g., "blue-style", "20px-padding")

## Example: e-image PropValue Structure
\`\`\`json
{
  "$$type": "image",
  "value": {
    "src": {
      "$$type": "image-src",
      "value": {
        "url": { "$$type": "url", "value": "https://example.com/image.jpg" }
      }
    },
    "size": { "$$type": "string", "value": "full" }
  }
}
\`\`\`
Note: The "size" property controls image resolution/loading, not visual size. Set visual dimensions via the style parameter (raw CSS).
`;

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/available-widgets-resource.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/available-widgets-resource.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AVAILABLE_WIDGETS_URI: function() { return /* binding */ AVAILABLE_WIDGETS_URI; },
/* harmony export */   AVAILABLE_WIDGETS_URI_V4: function() { return /* binding */ AVAILABLE_WIDGETS_URI_V4; },
/* harmony export */   initAvailableWidgetsResource: function() { return /* binding */ initAvailableWidgetsResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_element_data_util__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/element-data-util */ "./packages/packages/core/editor-canvas/src/mcp/utils/element-data-util.ts");


const AVAILABLE_WIDGETS_URI = 'elementor://context/available-widgets';
const AVAILABLE_WIDGETS_URI_V4 = 'elementor://context/available-widgets/v4';
const initAvailableWidgetsResource = reg => {
  const {
    resource,
    sendResourceUpdated
  } = reg;
  const buildContents = (uri, filterFunction = () => true) => {
    const widgets = (0,_utils_element_data_util__WEBPACK_IMPORTED_MODULE_1__.getAvailableWidgets)().filter(filterFunction);
    return {
      contents: [{
        uri,
        mimeType: 'application/json',
        text: JSON.stringify(widgets, null, 2)
      }]
    };
  };
  const notifyResourcesUpdated = () => {
    sendResourceUpdated({
      uri: AVAILABLE_WIDGETS_URI,
      ...buildContents(AVAILABLE_WIDGETS_URI)
    });
    sendResourceUpdated({
      uri: AVAILABLE_WIDGETS_URI_V4,
      ...buildContents(AVAILABLE_WIDGETS_URI_V4, w => w.version === 'v4')
    });
  };
  resource('available-widgets-v4', AVAILABLE_WIDGETS_URI_V4, {
    description: 'All registered v4 version widgets'
  }, async () => buildContents(AVAILABLE_WIDGETS_URI_V4, w => w.version === 'v4'));
  resource('available-widgets', AVAILABLE_WIDGETS_URI, {
    description: 'All registered widget types with v3/v4 version metadata and description.'
  }, async () => buildContents(AVAILABLE_WIDGETS_URI));
  const eventName = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)().name;
  const onV1Ready = () => {
    const widgets = (0,_utils_element_data_util__WEBPACK_IMPORTED_MODULE_1__.getAvailableWidgets)();
    if (widgets.length === 0) {
      return;
    }
    window.removeEventListener(eventName, onV1Ready);
    notifyResourcesUpdated();
  };
  window.addEventListener(eventName, onV1Ready);
  onV1Ready();
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/breakpoints-resource.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/breakpoints-resource.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BREAKPOINTS_SCHEMA_FULL_URI: function() { return /* binding */ BREAKPOINTS_SCHEMA_FULL_URI; },
/* harmony export */   BREAKPOINTS_SCHEMA_URI: function() { return /* binding */ BREAKPOINTS_SCHEMA_URI; },
/* harmony export */   initBreakpointsResource: function() { return /* binding */ initBreakpointsResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _widgets_schema_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");


const BREAKPOINTS_SCHEMA_URI = 'elementor://breakpoints/list';
const BREAKPOINTS_SCHEMA_FULL_URI = `${_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_1__.CANVAS_SERVER_NAME}_${BREAKPOINTS_SCHEMA_URI}`;
const initBreakpointsResource = reg => {
  const {
    resource,
    sendResourceUpdated
  } = reg;
  const getBreakpointsList = () => {
    const {
      breakpoints
    } = window.elementor?.config?.responsive || {};
    if (!breakpoints) {
      return [];
    }
    return Object.values(breakpoints).filter(bp => bp.is_enabled).map(bp => {
      const {
        direction: constraint,
        label,
        value
      } = bp;
      return {
        label,
        constraint,
        value
      };
    });
  };
  const buildResourceResponse = () => ({
    contents: [{
      uri: BREAKPOINTS_SCHEMA_URI,
      mimeType: 'application/json',
      text: JSON.stringify(getBreakpointsList())
    }]
  });
  resource('breakpoints ', BREAKPOINTS_SCHEMA_URI, {
    description: 'Breakpoints list.'
  }, () => {
    return buildResourceResponse();
  });
  window.addEventListener((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)().name, () => {
    sendResourceUpdated({
      uri: BREAKPOINTS_SCHEMA_URI,
      ...buildResourceResponse()
    });
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/build-llm-guidance.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/build-llm-guidance.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildLlmGuidance: function() { return /* binding */ buildLlmGuidance; },
/* harmony export */   enrichPropertiesWithBaseSettingsHints: function() { return /* binding */ enrichPropertiesWithBaseSettingsHints; },
/* harmony export */   mergeInstructions: function() { return /* binding */ mergeInstructions; }
/* harmony export */ });
/* harmony import */ var _composition_builder_utils_required_default_child_tags__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../composition-builder/utils/required-default-child-tags */ "./packages/packages/core/editor-canvas/src/composition-builder/utils/required-default-child-tags.ts");

const DEFAULT_STYLES_INSTRUCTION = 'These are the default styles applied to the widget. Override only when necessary.';
const DEFAULT_SETTINGS_INSTRUCTION = 'These are the default settings applied to the widget. Omit them from elementConfig unless the user explicitly asks to change them.';
const BASE_SETTING_PROP_HINT = 'Has a widget default — omit unless user explicitly requests a change. See llm_guidance.default_settings.';
function mergeInstructions(existing, additional) {
  if (typeof existing === 'string' && existing.length > 0) {
    return `${existing} ${additional}`;
  }
  return additional;
}
function enrichPropertiesWithBaseSettingsHints(properties, baseSettingsKeys) {
  if (!baseSettingsKeys.length) {
    return properties;
  }
  const enriched = {
    ...properties
  };
  for (const key of baseSettingsKeys) {
    const propSchema = enriched[key];
    if (!propSchema) {
      continue;
    }
    enriched[key] = {
      ...propSchema,
      description: propSchema.description ? `${propSchema.description} ${BASE_SETTING_PROP_HINT}` : BASE_SETTING_PROP_HINT
    };
  }
  return enriched;
}
function buildLlmGuidance(widgetData, widgetType, allWidgets) {
  const defaultStyles = {};
  const baseStyleSchema = widgetData?.base_styles;
  if (baseStyleSchema) {
    Object.values(baseStyleSchema).forEach(stylePropType => {
      stylePropType.variants.forEach(variant => {
        Object.assign(defaultStyles, variant.props);
      });
    });
  }
  const baseSettings = widgetData?.base_settings ?? {};
  const hasDefaultStyles = Object.keys(defaultStyles).length > 0;
  const hasDefaultSettings = Object.keys(baseSettings).length > 0;
  const llmGuidance = {
    can_have_children: !!widgetData?.meta?.is_container
  };
  if (hasDefaultStyles) {
    llmGuidance.instructions = DEFAULT_STYLES_INSTRUCTION;
    llmGuidance.default_styles = defaultStyles;
  }
  if (hasDefaultSettings) {
    llmGuidance.instructions = mergeInstructions(llmGuidance.instructions, DEFAULT_SETTINGS_INSTRUCTION);
    llmGuidance.default_settings = baseSettings;
  }
  const allowedChildTypes = widgetData.allowed_child_types;
  const allowedParents = Object.entries(allWidgets).filter(([, parentConfig]) => parentConfig.allowed_child_types?.includes(widgetType)).map(([parentType]) => parentType);
  if (allowedChildTypes?.length || allowedParents.length) {
    llmGuidance.nesting = {
      ...(allowedChildTypes?.length ? {
        allowed_child_types: allowedChildTypes
      } : {}),
      ...(allowedParents.length ? {
        allowed_parents: allowedParents
      } : {})
    };
  }
  const requiredDirectChildTags = (0,_composition_builder_utils_required_default_child_tags__WEBPACK_IMPORTED_MODULE_0__.getRequiredDefaultChildTypes)(widgetData);
  if (requiredDirectChildTags.length) {
    llmGuidance.required_direct_children = requiredDirectChildTags;
  }
  return llmGuidance;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/document-structure-resource.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/document-structure-resource.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DOCUMENT_STRUCTURE_URI: function() { return /* binding */ DOCUMENT_STRUCTURE_URI; },
/* harmony export */   initDocumentStructureResource: function() { return /* binding */ initDocumentStructureResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);


const DOCUMENT_STRUCTURE_URI = 'elementor://document/structure';
const initDocumentStructureResource = reg => {
  const {
    resource,
    sendResourceUpdated
  } = reg;
  let currentDocumentStructure = null;
  const updateDocumentStructure = () => {
    const structure = getDocumentStructure();
    const newStructure = JSON.stringify(structure, null, 2);
    if (newStructure !== currentDocumentStructure) {
      currentDocumentStructure = newStructure;
      sendResourceUpdated({
        uri: DOCUMENT_STRUCTURE_URI
      });
    }
  };

  // Listen to document changes
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/create'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/delete'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/move'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/copy'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/paste'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('editor/documents/attach-preview'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('editor/documents/switch')], updateDocumentStructure);

  // Initialize on load
  updateDocumentStructure();
  resource('document-structure', DOCUMENT_STRUCTURE_URI, {
    description: 'Document structure.'
  }, async () => {
    return {
      contents: [{
        uri: DOCUMENT_STRUCTURE_URI,
        text: JSON.stringify(getDocumentStructure(), null, 2)
      }]
    };
  });
};
function getDocumentStructure() {
  const extendedWindow = window;
  const document = extendedWindow.elementor?.documents?.getCurrent?.();
  if (!document) {
    return {
      error: 'No active document found'
    };
  }
  const containers = document.container?.children || [];
  const elements = containers.map(container => extractElementData(container));
  return {
    documentId: document.id,
    documentType: document.config.type,
    title: document.config.settings?.post_title || 'Untitled',
    elements: elements.filter(el => el !== null)
  };
}
function resolveElementVersion(element) {
  if (element.model?.config?.atomic) {
    return 'v4';
  }
  const widgetType = element.model?.attributes?.widgetType;
  if (widgetType && (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[widgetType]?.atomic_props_schema) {
    return 'v4';
  }
  return 'v3';
}
function extractElementData(element) {
  if (!element || !element.model) {
    return null;
  }
  const model = element.model.attributes;
  const result = {
    id: model.id,
    elType: model.elType,
    widgetType: model.widgetType || undefined,
    version: resolveElementVersion(element)
  };
  const title = model.title || element.model?.editor_settings?.title || element.model.getTitle?.();
  if (title) {
    result.title = title;
  }
  if (element.children && element.children.length > 0) {
    result.children = element.children.map(child => extractElementData(child)).filter(child => child !== null);
  }
  return result;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DYNAMIC_TAGS_URI: function() { return /* binding */ DYNAMIC_TAGS_URI; },
/* harmony export */   initDynamicTagsResource: function() { return /* binding */ initDynamicTagsResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/resolve-dynamic-tag */ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-dynamic-tag.ts");


const DYNAMIC_TAGS_URI = 'elementor://dynamic-tags';
const settingsSchema = propsSchema => {
  return Object.fromEntries(Object.entries(propsSchema ?? {}).filter(([key]) => !_utils_resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_1__.OMITTED_DYNAMIC_SETTING_KEYS.includes(key)).map(([key, propType]) => [key, _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.Schema.propTypeToJsonSchema(propType)]));
};
const buildDynamicTagsList = () => {
  return Object.values((0,_utils_resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_1__.getAtomicDynamicTags)()).map(tag => ({
    name: tag.name,
    label: tag.label,
    categories: tag.categories,
    settings: settingsSchema(tag.props_schema)
  }));
};
const initDynamicTagsResource = reg => {
  const {
    resource
  } = reg;
  resource('dynamic-tags', DYNAMIC_TAGS_URI, {
    description: 'List of available dynamic tags. To bind a property to a dynamic source, set its value to ' + '{ "$$type": "dynamic", "value": { "name": <tag name>, "settings": { ... } } } using a tag whose ' + 'name appears in that property\'s allowed list, and populate "settings" per the tag entry here.',
    mimeType: 'application/json'
  }, async uri => ({
    contents: [{
      uri: uri.href,
      mimeType: 'application/json',
      text: JSON.stringify(buildDynamicTagsList())
    }]
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/editor-state-resource.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/editor-state-resource.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDITOR_STATE_URI: function() { return /* binding */ EDITOR_STATE_URI; },
/* harmony export */   initEditorStateResource: function() { return /* binding */ initEditorStateResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const CURRENTLY_VIEWED_SCREEN = 'The user is currently viewing the Elementor editor';
const PAGE_CONTENT_CHARACTER_LIMIT = 500;
const PREVIEW_TEXT_NODE_MIN_LENGTH = 2;
const EDITOR_STATE_URI = 'elementor://context/editor-state';
const initEditorStateResource = reg => {
  const {
    resource,
    sendResourceUpdated
  } = reg;
  let lastSerializedState = '';
  const buildState = () => ({
    currentlyViewedScreen: CURRENTLY_VIEWED_SCREEN,
    pageContent: getPageContentFromPreview(),
    pageTitle: getPageTitle()
  });
  const notifyIfChanged = () => {
    const serialized = JSON.stringify(buildState());
    if (serialized === lastSerializedState) {
      return;
    }
    lastSerializedState = serialized;
    sendResourceUpdated({
      uri: EDITOR_STATE_URI
    });
  };
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/switch'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/attach-preview')], notifyIfChanged);
  lastSerializedState = JSON.stringify(buildState());
  resource('editor-state', EDITOR_STATE_URI, {
    description: 'Editor page title, preview text snapshot, and viewed screen label.'
  }, async () => {
    return {
      contents: [{
        uri: EDITOR_STATE_URI,
        text: JSON.stringify(buildState(), null, 2)
      }]
    };
  });
};
function getPageContentFromPreview() {
  try {
    const root = window.elementor?.$previewContents?.[0];
    if (!root) {
      return null;
    }
    const content = [];
    const clone = root.cloneNode(true);
    clone.querySelectorAll('.elementor-editor-element-settings, #elementor-add-new-section').forEach(el => {
      el.remove();
    });
    const walk = (node, insideElementorElement = false) => {
      const isInside = node.classList?.contains('elementor-element') || insideElementorElement;
      if (node.nodeType === Node.TEXT_NODE && isInside) {
        const text = node.textContent?.trim().replace(/\s+/g, ' ');
        if (text && text.length > PREVIEW_TEXT_NODE_MIN_LENGTH) {
          content.push(text);
        }
      } else {
        node.childNodes.forEach(child => {
          walk(child, isInside);
        });
      }
    };
    walk(clone);
    const text = content.join(' ');
    if (text.length > PAGE_CONTENT_CHARACTER_LIMIT) {
      return text.slice(0, PAGE_CONTENT_CHARACTER_LIMIT) + '...';
    }
    return text;
  } catch {
    return null;
  }
}
function getPageTitle() {
  try {
    const extendedWindow = window;
    const currentDocument = extendedWindow.elementor?.documents?.getCurrent?.();
    const postTitle = currentDocument?.config?.settings?.post_title;
    if (postTitle) {
      return postTitle;
    }
    let title = document.title || 'Page';
    title = title.split(/\s*[‹»|–—-]\s*/)[0];
    const trimmed = title.trim();
    return trimmed || 'Page';
  } catch {
    return 'Page';
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/general-context-resource.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/general-context-resource.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GENERAL_CONTEXT_URI: function() { return /* binding */ GENERAL_CONTEXT_URI; },
/* harmony export */   initGeneralContextResource: function() { return /* binding */ initGeneralContextResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const GENERAL_CONTEXT_URI = 'elementor://context/general';
const initGeneralContextResource = reg => {
  const {
    resource,
    sendResourceUpdated
  } = reg;
  let lastSerializedPayload = null;
  const getPageTitle = () => {
    const extendedWindow = window;
    const title = extendedWindow.elementor?.documents?.getCurrent?.()?.config?.settings?.post_title;
    if (!title?.trim()) {
      return null;
    }
    return title;
  };
  const buildPayload = () => {
    const extendedWindow = window;
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const postParam = new URLSearchParams(location.search).get('post');
    const parsedPostId = postParam ? Number(postParam) : null;
    const postId = parsedPostId !== null && Number.isFinite(parsedPostId) ? parsedPostId : null;
    const pageTitle = getPageTitle();
    const urlObject = new URL(window.location.href);
    const pageUrl = urlObject.pathname + urlObject.search;
    const pageName = pageTitle || 'Elementor Editor';
    const plugins = extendedWindow.angieConfig?.plugins;
    return {
      timezone,
      postId,
      currentPage: {
        pageName,
        pageTitle,
        pageUrl
      },
      ...(plugins && {
        plugins
      })
    };
  };
  const pushUpdateIfChanged = () => {
    const serialized = JSON.stringify(buildPayload());
    if (serialized === lastSerializedPayload) {
      return;
    }
    lastSerializedPayload = serialized;
    sendResourceUpdated({
      uri: GENERAL_CONTEXT_URI
    });
  };
  resource('general-context', GENERAL_CONTEXT_URI, {
    description: 'General context: timezone, post id, and current page.'
  }, async () => {
    return {
      contents: [{
        uri: GENERAL_CONTEXT_URI,
        mimeType: 'application/json',
        text: JSON.stringify(buildPayload(), null, 2)
      }]
    };
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/switch'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/attach-preview'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/settings')], pushUpdateIfChanged);
  pushUpdateIfChanged();
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/selected-element-resource.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/selected-element-resource.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SELECTED_ELEMENT_URI: function() { return /* binding */ SELECTED_ELEMENT_URI; },
/* harmony export */   initSelectedElementResource: function() { return /* binding */ initSelectedElementResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);


const SELECTED_ELEMENT_URI = 'elementor://context/selected-element';
const initSelectedElementResource = reg => {
  const {
    resource,
    sendResourceUpdated
  } = reg;
  let currentPayloadText = null;
  const publishIfChanged = payload => {
    const nextText = JSON.stringify(payload);
    if (nextText !== currentPayloadText) {
      currentPayloadText = nextText;
      sendResourceUpdated({
        uri: SELECTED_ELEMENT_URI
      });
    }
  };
  const onCommand = e => {
    if (e.type !== 'command') {
      return;
    }
    const commandEvent = e;
    if (commandEvent.command === 'document/elements/deselect-all') {
      publishIfChanged(createEmptySelectedElementPayload());
      return;
    }
    if (commandEvent.command !== 'document/elements/select' && commandEvent.command !== 'document/elements/settings') {
      return;
    }
    const {
      container
    } = commandEvent.args || {};
    if (container?.id) {
      publishIfChanged(buildPayloadFromContainer(container));
      return;
    }
    publishIfChanged(readSelectionFromEditor());
  };
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/select'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/deselect-all'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('document/elements/settings')], onCommand);
  publishIfChanged(readSelectionFromEditor());
  resource('selected-element', SELECTED_ELEMENT_URI, {
    description: 'Currently selected Elementor element context.'
  }, async () => {
    return {
      contents: [{
        uri: SELECTED_ELEMENT_URI,
        text: JSON.stringify(readSelectionFromEditor(), null, 2)
      }]
    };
  });
};
function createEmptySelectedElementPayload() {
  return {
    elementDisplayName: null,
    elementType: null,
    properties: null,
    selectedElementId: null,
    selectedParentId: null,
    version: null,
    widgetType: null
  };
}
function readSelectionFromEditor() {
  const elements = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getSelectedElements)();
  if (elements.length !== 1) {
    return createEmptySelectedElementPayload();
  }
  const container = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elements[0].id);
  return buildPayloadFromContainer(container);
}
function buildPayloadFromContainer(container) {
  if (!container?.id) {
    return createEmptySelectedElementPayload();
  }
  const widgetType = container.model.get('widgetType') ?? null;
  const elementType = container.type ?? 'widget';
  return {
    elementDisplayName: getElementDisplayName(container),
    elementType,
    properties: getElementProperties(container, widgetType),
    selectedElementId: container.id,
    selectedParentId: container.parent?.id ?? null,
    version: resolveElementVersion(container, widgetType),
    widgetType
  };
}
function resolveElementVersion(container, widgetType) {
  if (container.model?.config?.atomic) {
    return 'v4';
  }
  if (widgetType && (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[widgetType]?.atomic_props_schema) {
    return 'v4';
  }
  return 'v3';
}
function getElementProperties(container, widgetType) {
  const settings = container.settings?.toJSON?.();
  if (!settings || typeof settings !== 'object') {
    return null;
  }
  const widgetConfig = widgetType ? (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[widgetType] : null;
  const controls = widgetConfig?.controls;
  const filtered = {};
  for (const [key, value] of Object.entries(settings)) {
    if (value === undefined || value === null || value === '') {
      continue;
    }
    const controlDefault = controls?.[key]?.default;
    if (controlDefault !== undefined && JSON.stringify(value) === JSON.stringify(controlDefault)) {
      continue;
    }
    filtered[key] = value;
  }
  return Object.keys(filtered).length > 0 ? filtered : null;
}
function getElementDisplayName(container) {
  try {
    if (container.label) {
      return container.label;
    }
    const widgetType = container.model?.get?.('widgetType');
    if (widgetType) {
      const capitalizedType = widgetType.charAt(0).toUpperCase() + widgetType.slice(1);
      return capitalizedType.replace(/-/g, ' ');
    }
    if (container.type === 'container') {
      return 'Container';
    }
    if (container.type === 'section') {
      return 'Section';
    }
    return `Element ${container.id}`;
  } catch {
    return `Element ${container.id}`;
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BEST_PRACTICES_FULL_URI: function() { return /* binding */ BEST_PRACTICES_FULL_URI; },
/* harmony export */   BEST_PRACTICES_URI: function() { return /* binding */ BEST_PRACTICES_URI; },
/* harmony export */   CANVAS_SERVER_NAME: function() { return /* binding */ CANVAS_SERVER_NAME; },
/* harmony export */   STYLE_SCHEMA_URI: function() { return /* binding */ STYLE_SCHEMA_URI; },
/* harmony export */   WIDGET_SCHEMA_FULL_URI: function() { return /* binding */ WIDGET_SCHEMA_FULL_URI; },
/* harmony export */   WIDGET_SCHEMA_URI: function() { return /* binding */ WIDGET_SCHEMA_URI; },
/* harmony export */   initWidgetsSchemaResource: function() { return /* binding */ initWidgetsSchemaResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_element_data_util__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/element-data-util */ "./packages/packages/core/editor-canvas/src/mcp/utils/element-data-util.ts");
/* harmony import */ var _build_llm_guidance__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./build-llm-guidance */ "./packages/packages/core/editor-canvas/src/mcp/resources/build-llm-guidance.ts");





const V3_LAYOUT_CONTROL_TYPES = new Set(['section', 'tab', 'tabs']);
function extractV3ControlsMetadata(controls) {
  if (!(0,_utils_element_data_util__WEBPACK_IMPORTED_MODULE_3__.hasV3Controls)(controls)) {
    return {};
  }
  const result = {};
  for (const [controlKey, raw] of Object.entries(controls)) {
    if (!raw || typeof raw !== 'object') {
      continue;
    }
    const control = raw;
    const controlType = typeof control.type === 'string' ? control.type : undefined;
    if (controlType && V3_LAYOUT_CONTROL_TYPES.has(controlType)) {
      continue;
    }
    const entry = {};
    if (Object.prototype.hasOwnProperty.call(control, 'default')) {
      entry.default = control.default;
    }
    if (controlType) {
      entry.type = controlType;
    }
    if (Object.prototype.hasOwnProperty.call(control, 'options') && control.options !== undefined) {
      const options = control.options;
      if (options && typeof options === 'object' && !Array.isArray(options)) {
        entry.options = Object.keys(options);
      } else {
        entry.options = options;
      }
    }
    result[controlKey] = entry;
  }
  return result;
}
const CANVAS_SERVER_NAME = 'editor-canvas';
const WIDGET_SCHEMA_URI = 'elementor://widgets/schema/{widgetType}';
const WIDGET_SCHEMA_FULL_URI = `${CANVAS_SERVER_NAME}_${WIDGET_SCHEMA_URI}`;
const STYLE_SCHEMA_URI = 'elementor://styles/schema/{category}';
const BEST_PRACTICES_URI = 'elementor://styles/best-practices';
const BEST_PRACTICES_FULL_URI = `${CANVAS_SERVER_NAME}_${BEST_PRACTICES_URI}`;
const initWidgetsSchemaResource = reg => {
  const {
    resource
  } = reg;
  resource('styles-best-practices', BEST_PRACTICES_URI, {
    description: 'Styling best practices'
  }, async () => {
    return {
      contents: [{
        uri: BEST_PRACTICES_URI,
        text: `# Styling best practices
Prefer using "em" and "rem" values for text-related sizes, padding and spacing. Use percentages for dynamic sizing relative to parent containers.
This flexboxes are by default "flex" with "stretch" alignment. To ensure proper layout, define the "justify-content" and "align-items" as in the schema.

Styling is provided as raw CSS. The css string must follow standard CSS syntax, with properties and values separated by semicolons, no selectors, or nesting rules allowed.

** CRITICAL - VARIABLES **
When using global variables, ensure that the variables are defined in the ${'elementor://global-variables'} resource.
Variables from the user context ARE NOT SUPPORTED AND WILL RESOLVE IN ERROR.

`
      }]
    };
  });
  resource('widget-schema-by-type', new _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.ResourceTemplate(WIDGET_SCHEMA_URI, {
    list: () => {
      const cache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)() || {};
      const availableWidgets = Object.keys(cache).filter(widgetType => (0,_utils_element_data_util__WEBPACK_IMPORTED_MODULE_3__.isWidgetAvailableForLLM)(cache[widgetType]));
      return {
        resources: availableWidgets.map(widgetType => ({
          uri: `elementor://widgets/schema/${widgetType}`,
          name: 'Widget schema for ' + widgetType
        }))
      };
    }
  }), {
    description: 'PropType schema for the specified widget type'
  }, async (uri, variables) => {
    const widgetType = typeof variables.widgetType === 'string' ? variables.widgetType : variables.widgetType?.[0];
    const widgetData = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[widgetType];
    if (!widgetData) {
      throw new Error(`No prop schema found for element type: ${widgetType}`);
    }
    const propSchema = widgetData.atomic_props_schema;
    if (!propSchema) {
      if (!(0,_utils_element_data_util__WEBPACK_IMPORTED_MODULE_3__.hasV3Controls)(widgetData.controls)) {
        throw new Error(`No prop schema found for element type: ${widgetType}`);
      }
      const controlMetadata = extractV3ControlsMetadata(widgetData.controls);
      return {
        contents: [{
          uri: uri.toString(),
          mimeType: 'application/json',
          text: JSON.stringify({
            widget_version: 'v3',
            message: 'This widget exists in the editor but has no atomic props schema (V4). Use control_metadata as non-authoritative hints from legacy controls.',
            fields_note: 'All settings are optional; there is no JSON schema for this widget type.',
            properties: controlMetadata
          })
        }]
      };
    }
    const baseSettingsKeys = Object.keys(widgetData?.base_settings ?? {});
    const asJson = (0,_build_llm_guidance__WEBPACK_IMPORTED_MODULE_4__.enrichPropertiesWithBaseSettingsHints)(Object.fromEntries(Object.entries(propSchema).filter(([key, propType]) => _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.Schema.isPropKeyConfigurable(key, propType)).map(([key, propType]) => [key, _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.Schema.propTypeToJsonSchema(propType)])), baseSettingsKeys);
    const description = typeof widgetData?.meta?.description === 'string' ? widgetData.meta.description : undefined;
    const allWidgets = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)() || {};
    const llmGuidance = (0,_build_llm_guidance__WEBPACK_IMPORTED_MODULE_4__.buildLlmGuidance)(widgetData, widgetType, allWidgets);
    return {
      contents: [{
        uri: uri.toString(),
        mimeType: 'application/json',
        text: JSON.stringify({
          type: 'object',
          properties: asJson,
          description,
          llm_guidance: llmGuidance
        })
      }]
    };
  });
};
function cleanupPropSchema(propSchema) {
  const result = {};
  Object.keys(propSchema).forEach(propName => {
    result[propName] = cleanupPropType(propSchema[propName]);
  });
  return result;
}
function cleanupPropType(propType) {
  const result = {};
  Object.keys(propType).forEach(property => {
    switch (property) {
      case 'key':
      case 'kind':
        result[property] = propType[property];
        break;
      case 'meta':
      case 'settings':
        {
          if (Object.keys(propType[property] || {}).length > 0) {
            result[property] = propType[property];
          }
        }
        break;
    }
  });
  if (result.kind === 'plain') {
    Object.defineProperty(result, 'kind', {
      value: 'string'
    });
  } else if (result.kind === 'array') {
    result.item_prop_type = cleanupPropType(propType.item_prop_type);
  } else if (result.kind === 'object') {
    const shape = propType.shape;
    const cleanedShape = cleanupPropSchema(shape);
    result.shape = cleanedShape;
  } else if (result.kind === 'union') {
    const propTypes = propType.prop_types;
    const cleanedPropTypes = {};
    Object.keys(propTypes).forEach(key => {
      cleanedPropTypes[key] = cleanupPropType(propTypes[key]);
    });
    result.prop_types = cleanedPropTypes;
  }
  return result;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/prompt.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/prompt.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BUILD_COMPOSITIONS_GUIDE_URI: function() { return /* binding */ BUILD_COMPOSITIONS_GUIDE_URI; },
/* harmony export */   generatePrompt: function() { return /* binding */ generatePrompt; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _resources_available_widgets_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../resources/available-widgets-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/available-widgets-resource.ts");
/* harmony import */ var _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../resources/dynamic-tags-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts");



const BUILD_COMPOSITIONS_GUIDE_URI = 'elementor://canvas/tools/build-compositions-guide';
const generatePrompt = () => {
  const buildCompositionsToolPrompt = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.toolPrompts)('build-compositions');
  buildCompositionsToolPrompt.description(`
# RESOURCES (Read before use)
- [elementor://global-classes] - Check FIRST for reusable classes
- [elementor://global-variables] - ONLY use variables defined here
- [${_resources_available_widgets_resource__WEBPACK_IMPORTED_MODULE_1__.AVAILABLE_WIDGETS_URI}/v4]

# TOOL SUPPORT
This tool support v4 elements only

# WORKFLOW
1. Check/create global classes via "manage-global-classes" tool
2. Build composition (THIS TOOL) - minimal inline styles
3. Apply classes via "apply-global-class" tool

# XML STRUCTURE
- Use widget tags: \`<e-button configuration-id="btn1"></e-button>\`
- Containers: "e-flexbox", "e-div-block", "e-tabs"
- Every element needs unique "configuration-id"
- No attributes, classes, IDs, or text nodes in XML

## NESTED ELEMENTS
Some elements have internal tree structures (nesting). When using these elements, you MUST build the FULL tree in XML.
- Check \`llm_guidance.nesting\` in widget schemas for structure requirements
- \`llm_guidance.required_direct_children\` lists element types that must appear as direct child tags in XML (from widget defaults)
- \`allowed_child_types\` lists which element types can be nested inside
- \`allowed_parents\` lists which element types this element can be placed inside

# CONFIGURATION
- Map configuration-id → elementConfig (props) + style (raw CSS declarations)
- elementConfig PropValues require \`$$type\` matching schema
- style is raw CSS (property → value strings); the server converts it to native styles and stores any unconvertible declarations as the element custom CSS
- NO LINKS in configuration
- Retry on errors up to 10x
- Check \`llm_guidance.default_settings\` in widget schemas — omit only keys listed there from elementConfig unless the user explicitly asks to change them

# DYNAMIC TAGS
- A value can be made dynamic wherever its schema exposes a \`"$$type": "dynamic"\` variant. This may be the property root OR a NESTED field (e.g. an image's \`src\`, not the whole \`image\`).
- Put the dynamic object EXACTLY at that node, in place of the static variant. The variant's \`name\` lists the allowed tags; read [${_resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_2__.DYNAMIC_TAGS_URI}] for each tag's settings schema.
- Provide at that node: \`{ "$$type": "dynamic", "value": { "name": "<allowed tag>", "settings": { ... } } }\`
- Example (image): \`{ "$$type": "image", "value": { "src": { "$$type": "dynamic", "value": { "name": "<image tag>", "settings": { ... } } } } }\`
- Do NOT send \`group\` (it is resolved automatically). Populate \`settings\` strictly per the tag's schema; use \`{}\` only when it has none.

Note about configuration ids: These names are visible to the end-user, make sure they make sense, related and relevant.

# DESIGN PHILOSOPHY: CONTEXT-DRIVEN CREATIVITY

**Use the user's context aggressively.** Business type, brand personality, target audience, and purpose should drive every design decision. A law firm needs gravitas; a children's app needs playfulness. Don't default to generic.

## SIZING: DEFAULT IS NO SIZE (CRITICAL)

**DO NOT specify height or width unless you have a specific visual reason.**

Flexbox and CSS already handle sizing automatically:
- Containers grow to fit their content
- Flex children distribute space via flex properties, not width/height
- Text elements size to their content

WHEN TO SPECIFY SIZE:
- min-height on ROOT section for viewport-spanning hero (use min-height, NOT height)
- max-width for contained content areas (e.g., max-width: 60rem)
- Explicit aspect ratios for media containers

NEVER SPECIFY:
- height on nested containers (causes overflow)
- width on flex children (use flex-basis or gap instead)
- 100vh on anything except root-level sections
- Any size "just to be safe" - if unsure, OMIT IT

vh units are VIEWPORT-relative. Nested 100vh inside 100vh = 200vh overflow.

GOOD: \`<e-flexbox>content naturally sizes</e-flexbox>\`
BAD: \`<e-flexbox style="height:100vh"><e-div-block style="height:100vh">overflow</e-div-block></e-flexbox>\`

## Layout Variety (Break the Template)
- AVOID: Full-width 100vh hero → three columns → testimonials → CTA (every AI does this)
- VARY heights: Use auto-height sections with generous padding (6rem+). Let content breathe
- VARY widths: Not everything spans full width. Use contained sections (max-width: 960px) mixed with edge-to-edge
- ASYMMETRIC grids: 2:1, 1:3, offset layouts. Avoid equal column widths
- Negative space as design element: Large margins create focus and sophistication
- Break alignment intentionally: Offset headings, overlapping elements, broken grids

## Visual Depth & Effects
- Layer elements: Overlapping cards, text over images, floating elements
- Subtle shadows with color tint (not pure black): \`box-shadow: 0 20px 60px rgba(<brand-color-here>, 0.15)\`
- Gradient overlays on images for text readability
- Border radius variation: Mix sharp (0) and soft (1rem+) corners purposefully
- Backdrop blur for glassmorphism where appropriate
- Micro-interactions via CSS: hover transforms, transitions (0.3s ease)

## Typography with Character
- Display fonts for headlines (from user's brand or contextually appropriate)
- Size contrast: 4rem+ headlines vs 1rem body. Make hierarchy unmistakable
- Letter-spacing: Tight for large headlines (-0.02em), loose for small caps (0.1em)
- Line-height: Tight for headlines (1.1), generous for body (1.6-1.8)
- Text decoration: Underlines, highlights, gradient text for emphasis

## Color with Purpose
- Extract palette from user context (brand colors, industry norms, mood)
- 60-30-10 rule: dominant, secondary, accent
- Tinted neutrals over pure grays: warm (#faf8f5, #2d2a26) or cool (#f5f7fa, #1e2430)
- Color blocking: Large colored sections create visual rhythm
- Gradient directions: Diagonal (135deg, 225deg) feel more dynamic than vertical

## Spacing Strategy
- Section padding: 6rem-10rem vertical, creating breathing room
- Rhythm variation: Tight groups (2rem) with generous gaps between (6rem)
- Use rem/em exclusively for responsive scaling
- Generous padding on CTAs: min 1rem 2.5rem

# HARD CONSTRAINTS
- Variables ONLY from [elementor://global-variables] (others throw errors)
- Avoid SVG widgets unless assets are pre-uploaded
- Check \`llm_guidance\` in widget schemas (\`default_styles\`, nesting, required children)

# PARAMETERS
- **xmlStructure**: Valid XML with configuration-id attributes
- **elementConfig**: configuration-id → widget PropValues
- **style**: configuration-id → raw CSS declarations (property → value strings; no selectors)
  `);
  buildCompositionsToolPrompt.example(`
Section with heading + button (NO explicit heights - content sizes naturally):
{
  xmlStructure: "<e-flexbox configuration-id="Main Section"><e-heading configuration-id="Section Title"></e-heading><e-button configuration-id="Call to Action"></e-button></e-flexbox>",
  elementConfig: {
    "section1": { "tag": { "$$type": "string", "value": "section" } }
  },
  style: {
    "Section Title": {
      "padding": "6rem 4rem",
      "background": "linear-gradient(135deg, #faf8f5 0%, #f0ebe4 100%)",
      "font-size": "3.5rem",
      "color": "#2d2a26"
    }
  }
}
Note: No height/width specified on any element - flexbox handles layout automatically.
`);
  buildCompositionsToolPrompt.parameter('xmlStructure', `Valid XML structure with custom elementor tags and configuration-id attributes.`);
  buildCompositionsToolPrompt.parameter('elementConfig', `Record mapping configuration IDs to widget PropValues.`);
  buildCompositionsToolPrompt.parameter('style', `Record mapping configuration IDs to raw CSS declarations (property → value strings).`);
  buildCompositionsToolPrompt.instruction(`Element IDs in the returned XML represent actual widgets. Use these IDs for subsequent styling or configuration changes.`);
  return buildCompositionsToolPrompt.prompt();
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/schema.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/schema.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   inputSchema: function() { return /* binding */ inputSchema; },
/* harmony export */   outputSchema: function() { return /* binding */ outputSchema; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");


const inputSchema = {
  xmlStructure: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The XML structure representing the composition to be built'),
  elementConfig: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The configuration id'), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('property name'), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any().describe(`The PropValue for the property, refer to ${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_1__.WIDGET_SCHEMA_URI}`))).describe('A record mapping element IDs to their configuration objects. REQUIRED'),
  style: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The configuration id'), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('A CSS property name, e.g. "color", "padding".'), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('A CSS value, e.g. "6rem 4rem", "#2d2a26".'))).describe('A record mapping element configuration IDs to their raw CSS declarations (property→value). Converted to native styles server-side; any declaration that cannot be converted is stored as the element custom CSS.').default({})
};
const outputSchema = {
  errors: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('Error message if the composition building failed').optional(),
  xmlStructure: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The built XML structure as a string. Must use this XML after completion of building the composition, it contains real IDs.').optional(),
  llm_instructions: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('Instructions what to do next, Important to follow these instructions!').optional()
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/tool.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/tool.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENT_ADDED_EVENT: function() { return /* binding */ ELEMENT_ADDED_EVENT; },
/* harmony export */   initBuildCompositionsTool: function() { return /* binding */ initBuildCompositionsTool; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _composition_builder_composition_builder__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../composition-builder/composition-builder */ "./packages/packages/core/editor-canvas/src/composition-builder/composition-builder.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../utils/tracking */ "./packages/packages/core/editor-canvas/src/utils/tracking.ts");
/* harmony import */ var _resources_available_widgets_resource__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../resources/available-widgets-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/available-widgets-resource.ts");
/* harmony import */ var _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../resources/dynamic-tags-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts");
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");
/* harmony import */ var _utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../utils/convert-css-to-atomic */ "./packages/packages/core/editor-canvas/src/mcp/utils/convert-css-to-atomic.ts");
/* harmony import */ var _utils_element_data_util__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../utils/element-data-util */ "./packages/packages/core/editor-canvas/src/mcp/utils/element-data-util.ts");
/* harmony import */ var _utils_get_composition_target_container__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../utils/get-composition-target-container */ "./packages/packages/core/editor-canvas/src/mcp/utils/get-composition-target-container.ts");
/* harmony import */ var _prompt__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./prompt */ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/prompt.ts");
/* harmony import */ var _schema__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./schema */ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/schema.ts");
/* harmony import */ var _xml_leaf_wrapper__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./xml-leaf-wrapper */ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/xml-leaf-wrapper.ts");














const ELEMENT_ADDED_EVENT = 'elementor/canvas/element-added';
const initBuildCompositionsTool = reg => {
  const {
    addTool,
    resource
  } = reg;
  resource('build-compositions-guide', _prompt__WEBPACK_IMPORTED_MODULE_11__.BUILD_COMPOSITIONS_GUIDE_URI, {
    title: 'Build Compositions Guide',
    description: 'Detailed guide for using the build-compositions tool',
    mimeType: 'text/plain'
  }, async uri => ({
    contents: [{
      uri: uri.href,
      mimeType: 'text/plain',
      text: (0,_prompt__WEBPACK_IMPORTED_MODULE_11__.generatePrompt)()
    }]
  }));
  addTool({
    name: 'build-compositions',
    description: 'Build V4 element compositions on the Elementor canvas. Read the guide resource before use.',
    schema: _schema__WEBPACK_IMPORTED_MODULE_12__.inputSchema,
    requiredResources: [{
      description: 'Build compositions guide',
      uri: _prompt__WEBPACK_IMPORTED_MODULE_11__.BUILD_COMPOSITIONS_GUIDE_URI
    }, {
      description: 'Widgets schema',
      uri: _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_7__.WIDGET_SCHEMA_URI
    }, {
      description: 'Global Classes',
      uri: 'elementor://global-classes'
    }, {
      description: 'Global Variables',
      uri: 'elementor://global-variables'
    }, {
      description: 'Styles best practices',
      uri: _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_7__.BEST_PRACTICES_URI
    }, {
      description: 'Available widgets for this tool',
      uri: _resources_available_widgets_resource__WEBPACK_IMPORTED_MODULE_5__.AVAILABLE_WIDGETS_URI_V4
    }, {
      description: 'Dynamic tags catalog',
      uri: _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_6__.DYNAMIC_TAGS_URI
    }],
    outputSchema: _schema__WEBPACK_IMPORTED_MODULE_12__.outputSchema,
    handler: async rawParams => {
      assertCompositionXmlUsesV4WidgetsOnly(rawParams.xmlStructure);
      const {
        stylesConfig: convertedStyles,
        customCSS
      } = await convertCompositionStyles(rawParams.style);
      const {
        xmlStructure,
        elementConfig,
        stylesConfig
      } = (0,_xml_leaf_wrapper__WEBPACK_IMPORTED_MODULE_13__.adaptLeafRootParams)({
        ...rawParams,
        stylesConfig: convertedStyles,
        widgetsCache: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getWidgetsCache)() ?? {}
      });
      let generatedXML = '';
      const errors = [];
      const rootContainers = [];
      const documentContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)('document');
      const currentDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.getCurrentDocument)();
      const targetContainer = (0,_utils_get_composition_target_container__WEBPACK_IMPORTED_MODULE_10__.getCompositionTargetContainer)(documentContainer, currentDocument?.type.value);
      try {
        const compositionBuilder = _composition_builder_composition_builder__WEBPACK_IMPORTED_MODULE_3__.CompositionBuilder.fromXMLString(xmlStructure, {
          createElement: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.createElement,
          deleteElement: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.deleteElement,
          getWidgetsCache: _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getWidgetsCache
        });
        compositionBuilder.setElementConfig(elementConfig);
        compositionBuilder.setStylesConfig(stylesConfig);
        compositionBuilder.setCustomCSS(customCSS);
        const {
          configErrors,
          formErrors,
          rootContainers: generatedRootContainers
        } = await compositionBuilder.build(targetContainer);
        rootContainers.push(...generatedRootContainers);
        generatedXML = new XMLSerializer().serializeToString(compositionBuilder.getXML());
        rootContainers.forEach(container => {
          const elementData = container.model?.toJSON();
          if (elementData) {
            onElementAdded(elementData);
          }
        });
        Object.values(stylesConfig).forEach(styleValue => {
          (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__.dispatchMcpStylesAppliedEvent)({
            styleValue
          });
        });
        if (configErrors.length) {
          errors.push(...configErrors.map(msg => new Error(msg)));
        }
        if (formErrors.length) {
          errors.push(...formErrors.map(msg => new Error(msg)));
        }
      } catch (error) {
        errors.push(error);
      }
      if (errors.length) {
        rootContainers.forEach(rootContainer => {
          (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.deleteElement)({
            container: rootContainer,
            options: {
              useHistory: false
            }
          });
        });
        const errorMessages = errors.map(e => {
          if (typeof e === 'string') {
            return e;
          }
          if (e instanceof Error) {
            return e.message || String(e);
          }
          if (typeof e === 'object' && e !== null) {
            return JSON.stringify(e);
          }
          return String(e);
        }).filter(msg => msg && msg.trim() !== '' && msg !== '{}' && msg !== 'null' && msg !== 'undefined');
        if (errorMessages.length === 0) {
          throw new Error('Failed to build composition: Unknown error occurred. No error details available.');
        }
        const errorText = `Failed to build composition with the following errors:\n\n${errorMessages.join('\n\n')}`;
        throw new Error(errorText);
      }
      return {
        xmlStructure: generatedXML,
        errors: errors?.length ? errors.map(e => typeof e === 'string' ? e : e.message).join('\n\n') : undefined,
        llm_instructions: `The composition was built successfully with element IDs embedded in the XML.

**CRITICAL NEXT STEPS** (Follow in order):
1. **Apply Global Classes**: Use "apply-global-class" tool to apply the global classes you created BEFORE building this composition
   - Check the created element IDs in the returned XML
   - Apply semantic classes (heading-primary, button-cta, etc.) to appropriate elements

2. **Fine-tune if needed**: Use "configure-element" tool only for element-specific adjustments that don't warrant global classes

Remember: Global classes ensure design consistency and reusability. Don't skip applying them!
`
      };
    }
  });
};
async function convertCompositionStyles(style) {
  const stylesConfig = {};
  const customCSS = {};
  if (!style || Object.keys(style).length === 0) {
    return {
      stylesConfig,
      customCSS
    };
  }
  const results = await (0,_utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_8__.convertStyleBlocksToAtomic)(style);
  for (const [configId, {
    props,
    customCss
  }] of Object.entries(results)) {
    stylesConfig[configId] = props;
    if (customCss) {
      customCSS[configId] = customCss;
    }
  }
  return {
    stylesConfig,
    customCSS
  };
}
function assertCompositionXmlUsesV4WidgetsOnly(xmlStructure) {
  const doc = new DOMParser().parseFromString(xmlStructure, 'application/xml');
  if (doc.querySelector('parsererror')) {
    throw new Error('Failed to parse XML string: ' + doc);
  }
  const widgetsCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getWidgetsCache)() ?? {};
  for (const node of doc.querySelectorAll('*')) {
    const type = node.tagName;
    const widgetData = widgetsCache[type];
    if (!widgetData) {
      continue;
    }
    if (widgetData.elType !== 'widget') {
      continue;
    }
    if (!(0,_utils_element_data_util__WEBPACK_IMPORTED_MODULE_9__.isWidgetAvailableForLLM)(widgetData) || !widgetData.atomic_props_schema) {
      throw new Error(`This tool does not support element type: ${type}`);
    }
  }
}
function onElementAdded(element) {
  const elType = element.elType ?? '';
  const widgetType = element.widgetType ?? '';
  const elementName = elType === 'widget' ? widgetType : elType;
  (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_4__.trackCanvasEvent)({
    eventName: 'add_element',
    executed_by: 'mcp_tool',
    element_name: elementName,
    element_type: elType,
    widget_type: widgetType
  });
  const event = {
    element,
    executedBy: 'mcp_tool'
  };
  window.dispatchEvent(new CustomEvent(ELEMENT_ADDED_EVENT, {
    detail: event
  }));
  if (element.elements?.length) {
    element.elements?.forEach(childElement => {
      onElementAdded(childElement);
    });
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/xml-leaf-wrapper.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/xml-leaf-wrapper.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DIV_BLOCK_TAG: function() { return /* binding */ DIV_BLOCK_TAG; },
/* harmony export */   ZERO_SPACING: function() { return /* binding */ ZERO_SPACING; },
/* harmony export */   adaptLeafRootParams: function() { return /* binding */ adaptLeafRootParams; }
/* harmony export */ });
const DIV_BLOCK_TAG = 'e-div-block';
const ZERO_SPACING = {
  $$type: 'size',
  value: {
    size: {
      $$type: 'number',
      value: 0
    },
    unit: {
      $$type: 'string',
      value: 'px'
    }
  }
};
function adaptLeafRootParams(params) {
  const doc = new DOMParser().parseFromString(params.xmlStructure, 'application/xml');
  const rootElement = doc.documentElement;
  if (!isLeafWidget(rootElement.tagName, params.widgetsCache)) {
    return params;
  }
  const wrapperConfigId = getDivBlockWrapperConfigId(params.widgetsCache);
  return {
    ...params,
    xmlStructure: serializeWrapped(doc, rootElement, wrapperConfigId),
    stylesConfig: {
      ...params.stylesConfig,
      [wrapperConfigId]: {
        margin: ZERO_SPACING,
        padding: ZERO_SPACING,
        ...params.stylesConfig[wrapperConfigId]
      }
    }
  };
}
function getDivBlockWrapperConfigId(widgetsCache) {
  return widgetsCache[DIV_BLOCK_TAG]?.title ?? DIV_BLOCK_TAG;
}
function isLeafWidget(tagName, widgetsCache) {
  return widgetsCache[tagName]?.elType === 'widget';
}
function serializeWrapped(doc, rootElement, wrapperConfigId) {
  const wrapper = doc.createElement(DIV_BLOCK_TAG);
  wrapper.setAttribute('configuration-id', wrapperConfigId);
  wrapper.appendChild(rootElement.cloneNode(true));
  const wrappedDoc = new DOMParser().parseFromString(`<${DIV_BLOCK_TAG} />`, 'application/xml');
  wrappedDoc.replaceChild(wrapper, wrappedDoc.documentElement);
  return new XMLSerializer().serializeToString(wrappedDoc);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/prompt.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/prompt.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CONFIGURE_ELEMENT_GUIDE_TEXT: function() { return /* binding */ CONFIGURE_ELEMENT_GUIDE_TEXT; },
/* harmony export */   CONFIGURE_ELEMENT_GUIDE_URI: function() { return /* binding */ CONFIGURE_ELEMENT_GUIDE_URI; },
/* harmony export */   generatePrompt: function() { return /* binding */ generatePrompt; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../resources/dynamic-tags-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts");
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");



const CONFIGURE_ELEMENT_GUIDE_URI = 'elementor://canvas/tools/configure-element-guide';
const generatePrompt = () => {
  const configureElementToolPrompt = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.toolPrompts)('configure-element');
  configureElementToolPrompt.description(`
Configure an existing element on the page.

# **CRITICAL - REQUIRED INFORMATION (Must read before using this tool)**
1. [${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_2__.WIDGET_SCHEMA_URI}] — **Widget properties** (\`propertiesToChange\`): each widgetType (e.g. e-heading, e-button) has its own PropType schema; values must be PropValues with \`$$type\`.
2. [elementor://global-variables] — **Design tokens for styling**: use labels in CSS as \`var(--label)\` or \`var(--label, fallback)\`; only variables listed here are valid.
3. **Styling** (\`style\` parameter): flat map of CSS property → value strings — **not** PropValues. The server converts to native styles; unconvertible declarations become custom CSS.
4. **Current state**: \`get-element-configuration-values\` returns \`properties\` as PropValues and \`style\` in stored form; when writing, send raw CSS in \`style\`, not copied PropValues.

Before using this tool, read the widget PropType schema at editor-canvas__elementor://widgets/schema/{widgetType}

# When to use this tool
When a user requires to change anything in an element, such as updating text, colors, sizes, or other configurable properties.
This tool handles elements of type "widget".
This tool handles styling elements, using the "style" parameter (raw CSS as a property → value map).

To CLEAR a property (i.e., set it to default or none), provide null as a value - example: \`background-color: null\`.

The element's schema must be known before using this tool.

**PropValue structure (for \`propertiesToChange\` only — not for \`style\`):**
{
    "$$type": string, // MANDATORY as defined in the PropType schema under the "key" property
    value: unknown // The value according to the PropType schema for kinds of "array", use array with PropValues items inside. For "object", read the shape property of the PropType schema. For "plain", use strings.
}

<IMPORTANT>
ALWAYS MAKE SURE you have the PropType schemas for the element you are configuring. If you are not sure, retrieve the schema from the resources mentioned above.
</IMPORTANT>

You can use multiple property changes at once by providing multiple entries in the propertiesToChange object.
Some properties are nested, use the root property name, then objects with nested values inside, as the complete schema suggests.

Make sure you have the "widget-schema-by-type" resource available to retrieve the PropType schema for the element type you are configuring.

# How to configure elements
We use a dedicated PropType Schema for configuring element properties (propertiesToChange). When you configure an element property, you must use the EXACT PropType Value as defined in the schema.
For styling, use the "style" parameter with raw CSS declarations (property → value strings) - e.g. \`color: var(--primary-text, #000); height: 4rem;\`;
For all non-primitive entries in \`propertiesToChange\`, provide the schema \`key\` as \`$$type\` in the generated object, as it is MANDATORY for parsing.

Use the EXACT PropType schema given, and ALWAYS include the \`key\` from the schema for every property you are changing in \`propertiesToChange\`.

Check \`llm_guidance.default_settings\` in the widget schema — include a key in \`propertiesToChange\` only when the user explicitly asks to change it.

# Dynamic tags
A value can be made dynamic wherever its schema exposes a variant with "$$type": "dynamic". This may be the property root OR a NESTED field: for example an image is made dynamic on its "src" (the root stays "image"), NOT on the whole "image" value.
Put the dynamic object EXACTLY at the node whose schema offers the "dynamic" variant, in place of the static variant. The variant's "name" enumerates the tags allowed at that node.
1. Read the [${_resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_1__.DYNAMIC_TAGS_URI}] resource for each allowed tag's settings schema.
2. Provide, at that node:
{
  "$$type": "dynamic",
  "value": {
    "name": "<allowed tag name>",
    "settings": { /* strictly per the tag's settings schema */ }
  }
}
Image example: { "$$type": "image", "value": { "src": { "$$type": "dynamic", "value": { "name": "<image tag>", "settings": { ... } } } } }
Do NOT send "group" (it is resolved automatically). Use { "settings": {} } only when the tag has no settings.
`);
  configureElementToolPrompt.parameter('elementId', 'The ID of the element to configure. MANDATORY.');
  configureElementToolPrompt.parameter('elementType', 'The type of the element to configure (i.e. e-heading, e-button). MANDATORY.');
  configureElementToolPrompt.parameter('propertiesToChange', 'An object containing the properties to change, with their new values. MANDATORY. When updating a style only, provide an empty object.');
  configureElementToolPrompt.parameter('style', 'A flat map of raw CSS declarations (property → value), e.g. { "line-height": "1.25rem", "color": "var(--primary-text, #000)" }. Set a value to null to reset that property to its default. OPTIONAL.');
  configureElementToolPrompt.example(`
\`\`\`json
{
  propertiesToChange: {
    // List of properties TO CHANGE, following the PropType schema for the element as defined in the resource [${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_2__.WIDGET_SCHEMA_URI}]
    title: {
      $$type: 'string',
      value: 'New Title Text'
    },
    border: {
      $$type: 'boolean',
      value: false
    },
  },
  style: {
    'line-height': '1.25rem',
    'color': 'var(--primary-text, #000)'
  },
  elementId: 'element-id',
  elementType: 'element-type'
};
\`\`\`
`);
  configureElementToolPrompt.instruction('The $$type property is MANDATORY for every value in propertiesToChange; it is not used in the style parameter (raw CSS only).');
  configureElementToolPrompt.instruction(`
V4 only: If MCP fails, give manual steps using V4 UI.

V4 Editor structure:
Panel tabs: General (→ Settings section: ID, Tag, Link), Style, Interactions.
NO Advanced tab. Never mention Advanced tab.
`);
  return configureElementToolPrompt.prompt();
};
const CONFIGURE_ELEMENT_GUIDE_TEXT = generatePrompt();

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/schema.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/schema.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   inputSchema: function() { return /* binding */ inputSchema; },
/* harmony export */   outputSchema: function() { return /* binding */ outputSchema; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");


const inputSchema = {
  propertiesToChange: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The property name.'), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any().describe(`PropValue, refer to [${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_1__.WIDGET_SCHEMA_URI}] by correct type, as appears in elementType`), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any()).describe('An object record containing property names and their new values to be set on the element'),
  style: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('A CSS property name, e.g. "color", "margin-top".'), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable().describe('A CSS value, e.g. "red", "10px", "1px solid #000". Use null to reset the property to its default.')).describe('Raw CSS declarations as a flat property→value map. Converted to native styles server-side; any declaration that cannot be converted is stored as the element custom CSS. A null value resets that property to its default.').default({}),
  elementType: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The type of the element to retrieve the schema'),
  elementId: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('The unique id of the element to configure')
};
const outputSchema = {
  success: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.boolean().describe('Whether the configuration change was successful, only if propertyName and propertyValue are provided')
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/tool.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/tool.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initConfigureElementTool: function() { return /* binding */ initConfigureElementTool; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../resources/dynamic-tags-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/dynamic-tags-resource.ts");
/* harmony import */ var _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");
/* harmony import */ var _utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../utils/convert-css-to-atomic */ "./packages/packages/core/editor-canvas/src/mcp/utils/convert-css-to-atomic.ts");
/* harmony import */ var _utils_do_update_element_property__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../utils/do-update-element-property */ "./packages/packages/core/editor-canvas/src/mcp/utils/do-update-element-property.ts");
/* harmony import */ var _utils_resolve_canonical_prop_name__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../utils/resolve-canonical-prop-name */ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-canonical-prop-name.ts");
/* harmony import */ var _prompt__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./prompt */ "./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/prompt.ts");
/* harmony import */ var _schema__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./schema */ "./packages/packages/core/editor-canvas/src/mcp/tools/configure-element/schema.ts");










const initConfigureElementTool = reg => {
  const {
    addTool,
    resource
  } = reg;
  resource('configure-element-guide', _prompt__WEBPACK_IMPORTED_MODULE_8__.CONFIGURE_ELEMENT_GUIDE_URI, {
    title: 'Configure Element Guide',
    description: 'Detailed guide for using the configure-element tool',
    mimeType: 'text/plain'
  }, async uri => ({
    contents: [{
      uri: uri.href,
      mimeType: 'text/plain',
      text: (0,_prompt__WEBPACK_IMPORTED_MODULE_8__.generatePrompt)()
    }]
  }));
  addTool({
    name: 'configure-element',
    description: "Configure an existing V4 element's properties and styles. Read the guide resource before use.",
    schema: _schema__WEBPACK_IMPORTED_MODULE_9__.inputSchema,
    outputSchema: _schema__WEBPACK_IMPORTED_MODULE_9__.outputSchema,
    requiredResources: [{
      description: 'Widgets schema',
      uri: _resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_4__.WIDGET_SCHEMA_URI
    }, {
      description: 'Configure element guide',
      uri: _prompt__WEBPACK_IMPORTED_MODULE_8__.CONFIGURE_ELEMENT_GUIDE_URI
    }, {
      description: 'Dynamic tags catalog',
      uri: _resources_dynamic_tags_resource__WEBPACK_IMPORTED_MODULE_3__.DYNAMIC_TAGS_URI
    }],
    handler: async ({
      elementId,
      propertiesToChange,
      elementType,
      style
    }) => {
      const widgetData = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[elementType];
      if (!widgetData) {
        throw new Error(`Unknown element type: ${elementType}. Check the available-widgets resource for valid types.`);
      }
      const container = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
      if (!container) {
        throw new Error(`Element with id ${elementId} not found`);
      }
      const isElementTypeMatchingId = container.settings.get('widgetType') === elementType || container.type === elementType;
      if (!isElementTypeMatchingId) {
        throw new Error(`Element with ID ${elementId} is not of type ${elementType}`);
      }
      if (!widgetData.atomic_props_schema) {
        throw new Error(`This tool does not support V3 elements. Please use the elementor-v3-mcp tools instead for element type: ${elementType}`);
      }
      const propertiesToUpdate = (0,_utils_resolve_canonical_prop_name__WEBPACK_IMPORTED_MODULE_7__.resolveCanonicalPropKeys)(elementType, propertiesToChange);
      const toUpdate = Object.entries(propertiesToUpdate);
      for (const [propertyName, propertyValue] of toUpdate) {
        if (!_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.Schema.isPropKeyConfigurable(propertyName)) {
          throw new Error(`Not allowed to update ${propertyName}`);
        }
        try {
          (0,_utils_do_update_element_property__WEBPACK_IMPORTED_MODULE_6__.doUpdateElementProperty)({
            elementId,
            elementType,
            propertyName,
            propertyValue
          });
        } catch (error) {
          const errorMessage = createUpdateErrorMessage({
            propertyName,
            elementId,
            elementType,
            error: error,
            propertyType: 'prop'
          });
          throw new Error(errorMessage);
        }
      }
      await applyStyleFromCss({
        elementId,
        elementType,
        style
      });
      return {
        success: true
      };
    }
  });
};
async function applyStyleFromCss(opts) {
  const {
    elementId,
    elementType,
    style
  } = opts;
  if (!style || Object.keys(style).length === 0) {
    return;
  }
  const {
    props,
    customCss
  } = await (0,_utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_5__.convertCssToAtomic)(style);
  const styleValue = {
    ...props
  };
  if (customCss) {
    styleValue.custom_css = customCss;
  }
  if (Object.keys(styleValue).length === 0) {
    return;
  }
  try {
    (0,_utils_do_update_element_property__WEBPACK_IMPORTED_MODULE_6__.doUpdateElementProperty)({
      elementId,
      elementType,
      propertyName: '_styles',
      propertyValue: styleValue,
      customCssWriteMode: 'merge-with-stored'
    });
    (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.dispatchMcpStylesAppliedEvent)({
      styleValue
    });
  } catch (error) {
    throw new Error(createUpdateErrorMessage({
      propertyName: '(style)',
      elementId,
      elementType,
      propertyType: 'style',
      error: error
    }));
  }
}
function createUpdateErrorMessage(opts) {
  const {
    propertyName,
    elementId,
    elementType,
    error,
    propertyType
  } = opts;
  return `Failed to update property "${propertyName}" on element "${elementId}": ${error.message}.
${propertyType === 'prop' ? `
Check the element's PropType schema at the resource [${_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_4__.WIDGET_SCHEMA_URI.replace('{widgetType}', elementType)}] for type "${elementType}" to ensure the property exists and the value matches the expected PropType.
Now that you have this information, ensure you have the schema and try again.` : `
Provide styling as raw CSS via the "style" parameter (a flat map of CSS property → value). Declarations that cannot be converted are stored as the element custom CSS.`};
}`;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/tools/get-element-config/tool.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/tools/get-element-config/tool.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initGetElementConfigTool: function() { return /* binding */ initGetElementConfigTool; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__);



const schema = {
  elementId: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string()
};
const outputSchema = {
  properties: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(), _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.any()).describe('A record mapping PropTypes to their corresponding PropValues'),
  style: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(), _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.any()).describe('A record mapping StyleSchema properties to their corresponding PropValues'),
  childElements: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
    id: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
    elementType: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
    childElements: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.any()).describe('An array of child element IDs, when applicable, same structure recursively')
  })).describe('An array of child element IDs, when applicable, with recursive structure')
};
const structuredElements = element => {
  const children = element.children || [];
  return children.map(child => {
    return {
      id: child.id,
      elementType: child.model.get('elType') || child.model.get('widgetType') || 'unknown',
      childElements: structuredElements(child)
    };
  });
};
const initGetElementConfigTool = reg => {
  const {
    addTool
  } = reg;
  addTool({
    name: 'get-element-configuration-values',
    description: "Retrieve the element's configuration PropValues for a specific element by unique ID.",
    schema,
    outputSchema,
    handler: async ({
      elementId
    }) => {
      const element = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
      if (!element) {
        throw new Error(`Element with ID ${elementId} not found.`);
      }
      const elementType = element.model.get('widgetType') || element.model.get('elType') || '';
      const widgetData = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[elementType];
      if (!widgetData) {
        throw new Error(`Unknown element type: ${elementType}. Check the available-widgets resource for valid types.`);
      }
      if (!widgetData.atomic_props_schema) {
        throw new Error(`This tool does not support V3 elements. Please use the elementor-v3-mcp tools instead for element type: ${elementType}`);
      }
      const elementRawSettings = element.settings;
      const propSchema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[elementType]?.atomic_props_schema;
      if (!elementRawSettings || !propSchema) {
        throw new Error(`No settings or prop schema found for element ID: ${elementId}`);
      }
      const propValues = {};
      const stylePropValues = {};
      _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.Schema.configurableKeys(propSchema).forEach(key => {
        propValues[key] = structuredClone(elementRawSettings.get(key));
      });
      const elementStyles = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementStyles)(elementId) || {};
      const localStyle = Object.values(elementStyles).find(style => style.label === 'local');
      if (localStyle) {
        const defaultVariant = localStyle.variants.find(variant => variant.meta.breakpoint === 'desktop' && !variant.meta.state);
        if (defaultVariant) {
          const styleProps = defaultVariant.props || {};
          Object.keys(styleProps).forEach(stylePropName => {
            if (typeof styleProps[stylePropName] !== 'undefined') {
              stylePropValues[stylePropName] = structuredClone(styleProps[stylePropName]);
            }
          });
          if (defaultVariant.custom_css) {
            stylePropValues.custom_css = atob(defaultVariant.custom_css.raw);
          }
        }
      }
      return {
        properties: {
          ...propValues
        },
        style: {
          ...stylePropValues
        },
        childElements: structuredElements(element)
      };
    }
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/convert-css-to-atomic.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/convert-css-to-atomic.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   convertCssToAtomic: function() { return /* binding */ convertCssToAtomic; },
/* harmony export */   convertStyleBlocksToAtomic: function() { return /* binding */ convertStyleBlocksToAtomic; }
/* harmony export */ });
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__);

const CSS_TO_ATOMIC_URL = 'elementor/v1/css-to-atomic';
const SINGLE_BLOCK_KEY = 'default';

/**
 * A flat CSS property→value map. A null value is an explicit reset: the server emits it as a null
 * prop so the editor restores the property to its default.
 */

const convertBlocks = async blocks => {
  const {
    data
  } = await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().post(CSS_TO_ATOMIC_URL, {
    blocks
  });
  return data.data;
};
const convertStyleBlocksToAtomic = async styleByName => convertBlocks(styleByName);
const convertCssToAtomic = async style => {
  const results = await convertStyleBlocksToAtomic({
    [SINGLE_BLOCK_KEY]: style
  });
  return results[SINGLE_BLOCK_KEY];
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/do-update-element-property.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/do-update-element-property.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   doUpdateElementProperty: function() { return /* binding */ doUpdateElementProperty; },
/* harmony export */   resolvePropValue: function() { return /* binding */ resolvePropValue; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _merge_custom_css__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./merge-custom-css */ "./packages/packages/core/editor-canvas/src/mcp/utils/merge-custom-css.ts");
/* harmony import */ var _resolve_canonical_prop_name__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./resolve-canonical-prop-name */ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-canonical-prop-name.ts");
/* harmony import */ var _resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./resolve-dynamic-tag */ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-dynamic-tag.ts");








// TODO: see https://elementor.atlassian.net/browse/ED-22513 for better cross-module access

const LOCAL_STYLE_META = {
  breakpoint: 'desktop',
  state: null
};
function resolvePropValue(value, forceKey) {
  // TODO: see https://elementor.atlassian.net/browse/ED-22513 for better cross-module access
  const Utils = window.elementorV2.editorVariables.Utils;
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.Schema.adjustLlmPropValueSchema(value, {
    forceKey,
    transformers: {
      ...Utils.globalVariablesLLMResolvers,
      [_resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_6__.DYNAMIC_PROP_TYPE_KEY]: _resolve_dynamic_tag__WEBPACK_IMPORTED_MODULE_6__.dynamicTagLLMResolver
    }
  });
}

/*
 * This function expects a PropValue bag for updating an element.
 * Also, it supports updating styles "on-the-way" by checking for "_styles" property with PropValue bag that fits the common style schema.
 */
const doUpdateElementProperty = params => {
  const {
    elementId,
    propertyValue,
    elementType,
    customCssWriteMode = 'replace'
  } = params;
  const propertyName = params.propertyName === '_styles' ? params.propertyName : (0,_resolve_canonical_prop_name__WEBPACK_IMPORTED_MODULE_5__.resolveCanonicalPropName)(elementType, params.propertyName);
  if (propertyName === '_styles') {
    const elementStyles = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementStyles)(elementId) || {};
    const propertyMapValue = propertyValue;
    const styleSchema = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getStylesSchema)();
    const transformedStyleValues = Object.fromEntries(Object.entries(propertyMapValue).map(([key, val]) => {
      if (key === 'custom_css') {
        return [key, val];
      }
      const {
        key: propKey,
        kind
      } = styleSchema?.[key] || {};
      if (!propKey && kind !== 'union') {
        throw new Error(`_styles property ${key} is not supported.`);
      }
      if (val === null) {
        return [key, null];
      }
      return [key, resolvePropValue(val, propKey)];
    }));
    const localStyle = Object.values(elementStyles).find(style => style.label === 'local');
    const existingCustomCssText = localStyle ? (0,_merge_custom_css__WEBPACK_IMPORTED_MODULE_4__.readStoredCustomCssText)((0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getVariantByMeta)(localStyle, LOCAL_STYLE_META)?.custom_css?.raw) : '';
    let customCss;
    Object.keys(propertyMapValue).forEach(stylePropName => {
      const propertyRawSchema = styleSchema[stylePropName];
      if (stylePropName === 'custom_css') {
        let customCssValue = propertyMapValue[stylePropName];
        if (typeof customCssValue === 'object' && customCssValue && customCssValue.value) {
          customCssValue = String(customCssValue.value);
        }
        if (!customCssValue) {
          customCssValue = '';
        }
        const customCssText = customCssWriteMode === 'merge-with-stored' ? (0,_merge_custom_css__WEBPACK_IMPORTED_MODULE_4__.mergeCustomCssText)(existingCustomCssText, customCssValue) : String(customCssValue);
        if (customCssText) {
          customCss = {
            raw: btoa(customCssText)
          };
        } else {
          customCss = {
            raw: btoa('')
          };
        }
        return;
      }
      const isSupported = !!propertyRawSchema;
      if (!isSupported) {
        throw new Error(`Style property ${stylePropName} is not supported.`);
      }
      if (propertyRawSchema.kind === 'plain') {
        if (typeof propertyMapValue[stylePropName] !== 'object') {
          const propUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.getPropSchemaFromCache)(propertyRawSchema.key);
          if (propUtil) {
            const plainValue = propUtil.create(propertyMapValue[stylePropName]);
            propertyMapValue[stylePropName] = plainValue;
          }
        }
      }
    });
    delete transformedStyleValues.custom_css;
    if (!localStyle) {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.createElementStyle)({
        elementId,
        ...(typeof customCss !== 'undefined' ? {
          custom_css: customCss
        } : {}),
        classesProp: 'classes',
        label: 'local',
        meta: {
          breakpoint: 'desktop',
          state: null
        },
        props: {
          ...transformedStyleValues
        }
      });
    } else {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementStyle)({
        elementId,
        styleId: localStyle.id,
        meta: {
          breakpoint: 'desktop',
          state: null
        },
        ...(typeof customCss !== 'undefined' ? {
          custom_css: customCss
        } : {}),
        props: {
          ...transformedStyleValues
        }
      });
    }
    return;
  }
  const elementPropSchema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[elementType]?.atomic_props_schema;
  if (!elementPropSchema) {
    throw new Error(`No prop schema found for element type: ${elementType}`);
  }
  if (!elementPropSchema[propertyName]) {
    const propertyNames = Object.keys(elementPropSchema);
    throw new Error(`Property "${propertyName}" does not exist on element type "${elementType}". Available properties are: ${propertyNames.join(', ')}`);
  }
  const propKey = elementPropSchema[propertyName].key;
  const value = resolvePropValue(propertyValue, propKey);
  const {
    valid,
    jsonSchema
  } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.Schema.validatePropValue(elementPropSchema[propertyName], propertyValue);
  if (!valid) {
    throw new Error(`Invalid PropValue for elementId: ${elementId}. PropKey: ${propKey}, PropValue: ${JSON.stringify(propertyValue)}\nExpected Schema: ${jsonSchema}`);
  }
  (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementSettings)({
    id: elementId,
    props: {
      [propertyName]: value
    },
    withHistory: false
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.__privateRunCommandSync)('document/save/set-is-modified', {
    status: true
  }, {
    internal: true
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/element-data-util.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/element-data-util.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getAvailableWidgets: function() { return /* binding */ getAvailableWidgets; },
/* harmony export */   getWidgetVersion: function() { return /* binding */ getWidgetVersion; },
/* harmony export */   hasV3Controls: function() { return /* binding */ hasV3Controls; },
/* harmony export */   isWidgetAvailableForLLM: function() { return /* binding */ isWidgetAvailableForLLM; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

function hasV3Controls(controls) {
  return typeof controls === 'object' && controls !== null && Object.keys(controls).length > 0;
}
function isWidgetAvailableForLLM(config) {
  if (!config) {
    return false;
  }
  if (config.meta?.llm_support === false) {
    return false;
  }
  // TODO: Restore component once working in compositions
  if (config.title === 'Component') {
    return false;
  }
  if (config.atomic_props_schema) {
    return true;
  }
  return hasV3Controls(config.controls);
}
function getWidgetVersion(config) {
  return config?.atomic_props_schema ? 'v4' : 'v3';
}
function getAvailableWidgets() {
  const cache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)() ?? {};
  return Object.keys(cache).filter(widgetType => isWidgetAvailableForLLM(cache[widgetType])).sort().map(widgetType => {
    const config = cache[widgetType];
    const description = typeof config?.meta?.description === 'string' ? config.meta.description : undefined;
    return {
      type: widgetType,
      version: getWidgetVersion(config),
      ...(description && {
        description
      })
    };
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/get-composition-target-container.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/get-composition-target-container.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCompositionTargetContainer: function() { return /* binding */ getCompositionTargetContainer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);

function getCompositionTargetContainer(documentContainer, documentType) {
  const firstChild = documentContainer.children?.[0];
  if (documentType === _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.COMPONENT_DOCUMENT_TYPE && firstChild) {
    return firstChild;
  }
  return documentContainer;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/merge-custom-css.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/merge-custom-css.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   mergeCustomCssText: function() { return /* binding */ mergeCustomCssText; },
/* harmony export */   readStoredCustomCssText: function() { return /* binding */ readStoredCustomCssText; }
/* harmony export */ });
const CUSTOM_CSS_SEPARATOR = '\n';
const mergeCustomCssText = (...cssParts) => cssParts.map(cssPart => cssPart?.trim()).filter(cssPart => !!cssPart).join(CUSTOM_CSS_SEPARATOR);
const readStoredCustomCssText = raw => {
  if (!raw) {
    return '';
  }
  try {
    return atob(raw);
  } catch {
    return '';
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-canonical-prop-name.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/resolve-canonical-prop-name.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   resolveCanonicalPropKeys: function() { return /* binding */ resolveCanonicalPropKeys; },
/* harmony export */   resolveCanonicalPropName: function() { return /* binding */ resolveCanonicalPropName; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

function buildAliasToCanonicalMap(schema) {
  const aliasToCanonical = {};
  for (const [canonical, propType] of Object.entries(schema)) {
    const aliases = propType.meta?.aliases;
    if (!Array.isArray(aliases)) {
      continue;
    }
    for (const alias of aliases) {
      if (typeof alias === 'string' && alias) {
        aliasToCanonical[alias] = canonical;
      }
    }
  }
  return aliasToCanonical;
}
function resolveCanonicalPropName(elementType, propertyName) {
  const schema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[elementType]?.atomic_props_schema;
  if (!schema || schema[propertyName]) {
    return propertyName;
  }
  return buildAliasToCanonicalMap(schema)[propertyName] ?? propertyName;
}
function resolveCanonicalPropKeys(elementType, props) {
  const schema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[elementType]?.atomic_props_schema;
  if (!schema) {
    return {
      ...props
    };
  }
  const aliasToCanonical = buildAliasToCanonicalMap(schema);
  const resolved = {};
  for (const [key, value] of Object.entries(props)) {
    if (schema[key]) {
      resolved[key] = value;
    }
  }
  for (const [key, value] of Object.entries(props)) {
    if (schema[key]) {
      continue;
    }
    const canonical = aliasToCanonical[key];
    if (!canonical) {
      resolved[key] = value;
      continue;
    }
    if (!Object.prototype.hasOwnProperty.call(resolved, canonical)) {
      resolved[canonical] = value;
    }
  }
  return resolved;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/mcp/utils/resolve-dynamic-tag.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/mcp/utils/resolve-dynamic-tag.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DYNAMIC_PROP_TYPE_KEY: function() { return /* binding */ DYNAMIC_PROP_TYPE_KEY; },
/* harmony export */   OMITTED_DYNAMIC_SETTING_KEYS: function() { return /* binding */ OMITTED_DYNAMIC_SETTING_KEYS; },
/* harmony export */   dynamicTagLLMResolver: function() { return /* binding */ dynamicTagLLMResolver; },
/* harmony export */   getAtomicDynamicTags: function() { return /* binding */ getAtomicDynamicTags; },
/* harmony export */   getDynamicTagNamesByCategories: function() { return /* binding */ getDynamicTagNamesByCategories; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const DYNAMIC_PROP_TYPE_KEY = 'dynamic';

// `fallback` is a generic render-time default added to every tag; it is noise for configuration.
const OMITTED_DYNAMIC_SETTING_KEYS = ['fallback'];
const getAtomicDynamicTags = () => {
  const config = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.getElementorConfig)();
  return config.atomicDynamicTags?.tags ?? {};
};
const getDynamicTagNamesByCategories = categories => {
  if (!categories.length) {
    return [];
  }
  const wanted = new Set(categories);
  return Object.values(getAtomicDynamicTags()).filter(tag => tag.categories?.some(category => wanted.has(category))).map(tag => tag.name);
};

// Reconstructs an intact dynamic PropValue from whatever the LLM produced: the authoritative `group`
// is taken from the registry (never trusted from the model) and `settings` are rebuilt strictly from
// the tag's props schema (provided values are wrapped, omitted ones fall back to their defaults).
const dynamicTagLLMResolver = value => {
  const input = value ?? {};
  const tag = input.name ? getAtomicDynamicTags()[input.name] : undefined;
  if (!tag) {
    return {
      $$type: DYNAMIC_PROP_TYPE_KEY,
      value: {
        name: input.name ?? '',
        group: '',
        settings: {}
      }
    };
  }
  return {
    $$type: DYNAMIC_PROP_TYPE_KEY,
    value: {
      name: tag.name,
      group: tag.group,
      settings: buildStrictSettings(tag.props_schema ?? {}, input.settings ?? {})
    }
  };
};
const buildStrictSettings = (schema, provided) => {
  const settings = {};
  for (const [key, propType] of Object.entries(schema)) {
    if (OMITTED_DYNAMIC_SETTING_KEYS.includes(key)) {
      continue;
    }
    const resolved = provided[key] !== undefined ? wrapSettingValue(provided[key], propType) : defaultSettingValue(propType);
    if (resolved !== undefined && resolved !== null) {
      settings[key] = resolved;
    }
  }
  return settings;
};
const wrapSettingValue = (raw, propType) => {
  if (raw !== null && typeof raw === 'object') {
    return raw;
  }
  return propType.key ? {
    $$type: propType.key,
    value: raw
  } : raw;
};
const defaultSettingValue = propType => {
  if (propType.initial_value !== null && propType.initial_value !== undefined) {
    return propType.initial_value;
  }
  if (propType.default !== null && propType.default !== undefined) {
    return wrapSettingValue(propType.default, propType);
  }
  return undefined;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/prevent-link-in-link-commands.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/prevent-link-in-link-commands.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initLinkInLinkPrevention: function() { return /* binding */ initLinkInLinkPrevention; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function initLinkInLinkPrevention() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'document/elements/paste',
    condition: blockLinkInLinkPaste
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'document/elements/move',
    condition: blockLinkInLinkMove
  });
}
const learnMoreActionProps = {
  href: 'https://go.elementor.com/element-link-inside-link-infotip',
  target: '_blank',
  color: 'inherit',
  variant: 'text',
  sx: {
    marginInlineStart: '20px'
  },
  children: 'Learn more'
};
function blockLinkInLinkPaste(args) {
  const {
    containers = [args.container],
    storageType
  } = args;
  const targetElements = containers;
  if (storageType !== 'localstorage') {
    return false;
  }
  const data = window?.elementorCommon?.storage?.get();
  if (!data?.clipboard?.elements) {
    return false;
  }
  const sourceElements = data.clipboard.elements;
  const notification = {
    type: 'default',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)("To paste a link to this element, first remove the link from it's parent container.", 'elementor'),
    id: 'paste-in-link-blocked',
    additionalActionProps: [learnMoreActionProps]
  };
  const blocked = shouldBlock(sourceElements, targetElements);
  if (blocked) {
    (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)(notification);
  }
  return blocked;
}
function blockLinkInLinkMove(args) {
  const {
    containers = [args.container],
    target
  } = args;
  const sourceElements = containers;
  const targetElement = target;
  const notification = {
    type: 'default',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)("To drag a link to this element, first remove the link from it's parent container.", 'elementor'),
    id: 'move-in-link-blocked',
    additionalActionProps: [learnMoreActionProps]
  };
  const isBlocked = shouldBlock(sourceElements, [targetElement]);
  if (isBlocked) {
    (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)(notification);
  }
  return isBlocked;
}
function shouldBlock(sourceElements, targetElements) {
  if (!sourceElements?.length || !targetElements?.length) {
    return false;
  }
  const isSourceContainsAnAnchor = sourceElements.some(src => {
    return src?.id ? (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.isElementAnchored)(src.id) || !!(0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getAnchoredDescendantId)(src.id) : false;
  });
  if (!isSourceContainsAnAnchor) {
    return false;
  }
  const isTargetContainsAnAnchor = targetElements.some(target => {
    return target?.id ? (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.isElementAnchored)(target.id) || !!(0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getAnchoredAncestorId)(target.id) : false;
  });
  return isTargetContainsAnAnchor;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/renderers/create-dom-renderer.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/renderers/create-dom-renderer.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createDomRenderer: function() { return /* binding */ createDomRenderer; }
/* harmony export */ });
/* harmony import */ var _elementor_twing__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/twing */ "@elementor/twing");
/* harmony import */ var _elementor_twing__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_twing__WEBPACK_IMPORTED_MODULE_0__);

function createDomRenderer() {
  const loader = (0,_elementor_twing__WEBPACK_IMPORTED_MODULE_0__.createArrayLoader)({});
  const environment = (0,_elementor_twing__WEBPACK_IMPORTED_MODULE_0__.createEnvironment)(loader);
  environment.registerEscapingStrategy(escapeHtmlTag, 'html_tag');
  environment.registerEscapingStrategy(escapeURL, 'full_url');
  return {
    register: loader.setTemplate,
    render: environment.render
  };
}
function escapeHtmlTag(value) {
  const allowedTags = ['a', 'article', 'aside', 'button', 'div', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'main', 'nav', 'p', 'section', 'span'];
  return allowedTags.includes(value) ? value : 'div';
}
function escapeURL(value) {
  const allowedProtocols = ['http:', 'https:', 'mailto:', 'tel:'];
  try {
    const parsed = new URL(value);
    return allowedProtocols.includes(parsed.protocol) ? value : '';
  } catch {
    return '';
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/renderers/create-props-resolver.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/renderers/create-props-resolver.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createPropsResolver: function() { return /* binding */ createPropsResolver; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _multi_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./multi-props */ "./packages/packages/core/editor-canvas/src/renderers/multi-props.ts");


const TRANSFORM_DEPTH_LIMIT = 3;
function createPropsResolver({
  transformers,
  schema: initialSchema,
  onPropResolve
}) {
  async function resolve({
    props,
    schema,
    signal,
    renderContext
  }) {
    schema = schema ?? initialSchema;
    const promises = Promise.all(Object.entries(schema).map(async ([key, type]) => {
      const value = props[key] ?? type.default;
      const transformed = await transform({
        value,
        key,
        type,
        signal,
        renderContext
      });
      onPropResolve?.({
        key,
        value: transformed,
        propValue: value,
        propType: type
      });
      if ((0,_multi_props__WEBPACK_IMPORTED_MODULE_1__.isMultiProps)(transformed)) {
        return (0,_multi_props__WEBPACK_IMPORTED_MODULE_1__.getMultiPropsValue)(transformed);
      }
      return {
        [key]: transformed
      };
    }));
    return Object.assign({}, ...(await promises).filter(Boolean));
  }
  async function transform({
    value,
    key,
    type,
    signal,
    depth = 0,
    renderContext
  }) {
    if (value === null || value === undefined) {
      return null;
    }
    if (!(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(value)) {
      return value;
    }
    if (depth > TRANSFORM_DEPTH_LIMIT) {
      return null;
    }
    if (value.disabled === true) {
      return null;
    }
    let transformablePropType = type;
    if (type.kind === 'union') {
      transformablePropType = type.prop_types[value.$$type];
      if (!transformablePropType) {
        return null;
      }
    }
    transformablePropType = transformablePropType;
    if (value.$$type !== transformablePropType.key) {
      return null;
    }

    // Warning: This variable is loosely-typed - use with caution.
    let resolvedValue = value.value;
    if (transformablePropType.kind === 'object') {
      resolvedValue = await resolve({
        props: resolvedValue,
        schema: transformablePropType.shape,
        signal,
        renderContext
      });
    }
    if (transformablePropType.kind === 'array') {
      resolvedValue = await Promise.all(resolvedValue.map(item => transform({
        value: item,
        key,
        type: transformablePropType.item_prop_type,
        depth,
        signal,
        renderContext
      })));
    }
    const transformer = transformers.get(value.$$type);
    if (!transformer) {
      return null;
    }
    try {
      const transformed = await transformer(resolvedValue, {
        key,
        signal,
        renderContext,
        propType: type
      });
      return transform({
        value: transformed,
        key,
        type,
        signal,
        depth: depth + 1,
        renderContext
      });
    } catch {
      return null;
    }
  }
  return resolve;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/renderers/create-styles-renderer.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/renderers/create-styles-renderer.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createStylesRenderer: function() { return /* binding */ createStylesRenderer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./errors */ "./packages/packages/core/editor-canvas/src/renderers/errors.ts");



const SELECTORS_MAP = {
  class: '.'
};
const DEFAULT_BREAKPOINT = 'desktop';
const DEFAULT_STATE = 'normal';
function getStyleUniqueKey(style) {
  const breakpoint = style.variants[0]?.meta?.breakpoint ?? DEFAULT_BREAKPOINT;
  const state = style.variants[0]?.meta?.state ?? DEFAULT_STATE;
  return `${style.id}-${breakpoint}-${state}`;
}
function createStylesRenderer({
  resolve,
  breakpoints,
  selectorPrefix = ''
}) {
  return async ({
    styles,
    signal
  }) => {
    const seenKeys = new Set();
    const uniqueStyles = styles.filter(style => {
      const key = getStyleUniqueKey(style);
      if (seenKeys.has(key)) {
        return false;
      }
      seenKeys.add(key);
      return true;
    });
    const stylesCssPromises = uniqueStyles.map(async style => {
      const variantCssPromises = Object.values(style.variants).map(async variant => {
        const css = await propsToCss({
          props: variant.props,
          resolve,
          signal
        });
        const customCss = customCssToString(variant.custom_css);
        return createStyleWrapper().for(style.cssName, style.type).withPrefix(selectorPrefix).withState(variant.meta.state).withMediaQuery(variant.meta.breakpoint ? breakpoints[variant.meta.breakpoint] : null).wrap(css + customCss);
      });
      const variantsCss = await Promise.all(variantCssPromises);
      return {
        id: style.id,
        breakpoint: style?.variants[0]?.meta?.breakpoint || 'desktop',
        value: variantsCss.join(''),
        state: style?.variants[0]?.meta?.state || null
      };
    });
    return await Promise.all(stylesCssPromises);
  };
}
function createStyleWrapper(value = '', wrapper) {
  return {
    for: (cssName, type) => {
      const symbol = SELECTORS_MAP[type];
      if (!symbol) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_2__.UnknownStyleTypeError({
          context: {
            type
          }
        });
      }
      return createStyleWrapper(`${value}${symbol}${cssName}`, wrapper);
    },
    withPrefix: prefix => createStyleWrapper([prefix, value].filter(Boolean).join(' '), wrapper),
    withState: state => {
      const selector = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_0__.getSelectorWithState)(value, state);
      return createStyleWrapper(selector, wrapper);
    },
    withMediaQuery: breakpoint => {
      if (!breakpoint?.type) {
        return createStyleWrapper(value, wrapper);
      }
      const size = `${breakpoint.type}:${breakpoint.width}px`;
      return createStyleWrapper(value, css => `@media(${size}){${css}}`);
    },
    wrap: css => {
      const res = `${value}{${css}}`;
      if (!wrapper) {
        return res;
      }
      return wrapper(res);
    }
  };
}
async function propsToCss({
  props,
  resolve,
  signal
}) {
  const transformed = await resolve({
    props,
    signal
  });
  return Object.entries(transformed).reduce((acc, [propName, propValue]) => {
    if (propValue === null) {
      return acc;
    }
    acc.push(propName + ':' + propValue + ';');
    return acc;
  }, []).join('');
}
function customCssToString(customCss) {
  const decoded = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.decodeString)(customCss?.raw || '');
  if (!decoded.trim()) {
    return '';
  }
  return decoded + '\n';
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/renderers/enqueue-font-from-style-prop.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/renderers/enqueue-font-from-style-prop.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   maybeEnqueueFontFromStyleProp: function() { return /* binding */ maybeEnqueueFontFromStyleProp; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);

const maybeEnqueueFontFromStyleProp = (propType, propValue, enqueue) => {
  if (!(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(propValue) || propValue.disabled) {
    return;
  }
  const typeKey = propType.kind === 'union' ? propValue.$$type : propType.key;
  const propTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.getPropSchemaFromCache)(typeKey);
  if (!propTypeUtil || !('getEnqueueFontFamily' in propTypeUtil) || typeof propTypeUtil.getEnqueueFontFamily !== 'function') {
    return;
  }
  const stored = propValue.value;
  if (typeof stored !== 'string') {
    return;
  }
  const font = propTypeUtil.getEnqueueFontFamily(stored);
  if (font) {
    enqueue(font);
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/renderers/errors.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/renderers/errors.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   UnknownStyleStateError: function() { return /* binding */ UnknownStyleStateError; },
/* harmony export */   UnknownStyleTypeError: function() { return /* binding */ UnknownStyleTypeError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const UnknownStyleTypeError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'unknown_style_type',
  message: 'Unknown style type'
});
const UnknownStyleStateError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'unknown_style_state',
  message: 'Unknown style state'
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/renderers/multi-props.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/renderers/multi-props.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createMultiPropsValue: function() { return /* binding */ createMultiPropsValue; },
/* harmony export */   getMultiPropsValue: function() { return /* binding */ getMultiPropsValue; },
/* harmony export */   isMultiProps: function() { return /* binding */ isMultiProps; }
/* harmony export */ });
const isMultiProps = propValue => {
  return !!propValue && typeof propValue === 'object' && '$$multi-props' in propValue && propValue['$$multi-props'] === true;
};
const createMultiPropsValue = props => {
  return {
    '$$multi-props': true,
    value: props
  };
};
const getMultiPropsValue = multiProps => {
  return multiProps.value;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/settings-transformers-registry.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/settings-transformers-registry.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   settingsTransformersRegistry: function() { return /* binding */ settingsTransformersRegistry; }
/* harmony export */ });
/* harmony import */ var _transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./transformers/create-transformers-registry */ "./packages/packages/core/editor-canvas/src/transformers/create-transformers-registry.ts");

const settingsTransformersRegistry = (0,_transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_0__.createTransformersRegistry)();

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/style-commands/init-style-commands.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/style-commands/init-style-commands.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initStyleCommands: function() { return /* binding */ initStyleCommands; }
/* harmony export */ });
/* harmony import */ var _paste_style__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./paste-style */ "./packages/packages/core/editor-canvas/src/style-commands/paste-style.ts");
/* harmony import */ var _reset_style__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reset-style */ "./packages/packages/core/editor-canvas/src/style-commands/reset-style.ts");


function initStyleCommands() {
  (0,_paste_style__WEBPACK_IMPORTED_MODULE_0__.initPasteStyleCommand)();
  (0,_reset_style__WEBPACK_IMPORTED_MODULE_1__.initResetStyleCommand)();
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/style-commands/paste-style.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/style-commands/paste-style.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initPasteStyleCommand: function() { return /* binding */ initPasteStyleCommand; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_command_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/command-utils */ "./packages/packages/core/editor-canvas/src/utils/command-utils.ts");
/* harmony import */ var _undoable_actions_paste_element_style__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./undoable-actions/paste-element-style */ "./packages/packages/core/editor-canvas/src/style-commands/undoable-actions/paste-element-style.ts");





function initPasteStyleCommand() {
  const pasteElementStyleCommand = (0,_undoable_actions_paste_element_style__WEBPACK_IMPORTED_MODULE_4__.undoablePasteElementStyle)();
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'document/elements/paste-style',
    condition: _utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.hasAtomicWidgets
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.commandStartEvent)('document/elements/paste-style'), e => pasteStyles(e.args, pasteElementStyleCommand));
}
function pasteStyles(args, pasteLocalStyle) {
  const {
    containers = [args.container],
    storageKey
  } = args;
  const atomicContainers = containers.filter(_utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.isAtomicWidget);
  if (!atomicContainers.length) {
    return;
  }
  const clipboardElements = (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.getClipboardElements)(storageKey);
  const [clipboardElement] = clipboardElements ?? [];
  const clipboardContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(clipboardElement.id);
  if (!clipboardElement || !clipboardContainer || !(0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.isAtomicWidget)(clipboardContainer)) {
    return;
  }
  const elementStyles = clipboardElement.styles;
  const elementStyle = Object.values(elementStyles ?? {})[0]; // we currently support only one local style

  const classesSetting = getClassesWithoutLocalStyle(clipboardContainer, elementStyle);
  if (classesSetting.length) {
    pasteClasses(atomicContainers, classesSetting);
  }
  if (elementStyle) {
    pasteLocalStyle({
      containers: atomicContainers,
      newStyle: elementStyle
    });
  }
}
function getClassesWithoutLocalStyle(clipboardContainer, style) {
  const classesProp = (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.getClassesProp)(clipboardContainer);
  if (!classesProp) {
    return [];
  }
  const classesSetting = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementSetting)(clipboardContainer.id, classesProp);
  return classesSetting?.value.filter(styleId => styleId !== style?.id) ?? [];
}
function pasteClasses(containers, classes) {
  containers.forEach(container => {
    const classesProp = (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.getClassesProp)(container);
    if (!classesProp) {
      return;
    }
    const classesSetting = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementSetting)(container.id, classesProp);
    const currentClasses = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.classesPropTypeUtil.extract(classesSetting) ?? [];
    const newClasses = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.classesPropTypeUtil.create(Array.from(new Set([...classes, ...currentClasses])));
    (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementSettings)({
      id: container.id,
      props: {
        [classesProp]: newClasses
      }
    });
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/style-commands/reset-style.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/style-commands/reset-style.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initResetStyleCommand: function() { return /* binding */ initResetStyleCommand; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_command_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/command-utils */ "./packages/packages/core/editor-canvas/src/utils/command-utils.ts");
/* harmony import */ var _undoable_actions_reset_element_style__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./undoable-actions/reset-element-style */ "./packages/packages/core/editor-canvas/src/style-commands/undoable-actions/reset-element-style.ts");



function initResetStyleCommand() {
  const resetElementStyles = (0,_undoable_actions_reset_element_style__WEBPACK_IMPORTED_MODULE_2__.undoableResetElementStyle)();
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.blockCommand)({
    command: 'document/elements/reset-style',
    condition: _utils_command_utils__WEBPACK_IMPORTED_MODULE_1__.hasAtomicWidgets
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandStartEvent)('document/elements/reset-style'), e => resetStyles(e.args, resetElementStyles));
}
function resetStyles(args, resetElementStyles) {
  const {
    containers = [args.container]
  } = args;
  const atomicContainers = containers.filter(_utils_command_utils__WEBPACK_IMPORTED_MODULE_1__.isAtomicWidget);
  if (!atomicContainers.length) {
    return;
  }
  resetElementStyles({
    containers: atomicContainers
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/style-commands/undoable-actions/paste-element-style.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/style-commands/undoable-actions/paste-element-style.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   undoablePasteElementStyle: function() { return /* binding */ undoablePasteElementStyle; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _utils_command_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/command-utils */ "./packages/packages/core/editor-canvas/src/utils/command-utils.ts");





const undoablePasteElementStyle = () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.undoable)({
  do: ({
    containers,
    newStyle
  }) => {
    return containers.map(container => {
      const elementId = container.id;
      const classesProp = (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_4__.getClassesProp)(container);
      if (!classesProp) {
        return null;
      }
      const originalStyles = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementStyles)(container.id);
      const [styleId, styleDef] = Object.entries(originalStyles ?? {})[0] ?? []; // we currently support only one local style
      const originalStyle = Object.keys(styleDef ?? {}).length ? styleDef : null;
      const revertData = {
        styleId,
        originalStyle
      };
      if (styleId) {
        newStyle.variants.forEach(({
          meta,
          props,
          custom_css: customCss
        }) => {
          (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementStyle)({
            elementId,
            styleId,
            meta,
            props,
            custom_css: customCss
          });
        });
      } else {
        const [firstVariant] = newStyle.variants;
        const additionalVariants = newStyle.variants.slice(1);
        revertData.styleId = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.createElementStyle)({
          elementId,
          classesProp,
          label: _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.ELEMENTS_STYLES_RESERVED_LABEL,
          ...firstVariant,
          additionalVariants
        });
      }
      return revertData;
    });
  },
  undo: ({
    containers
  }, revertDataItems) => {
    containers.forEach((container, index) => {
      const revertData = revertDataItems[index];
      if (!revertData) {
        return;
      }
      if (!revertData.originalStyle) {
        // the container didn't have a style before pasting the new style
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.deleteElementStyle)(container.id, revertData.styleId);
        return;
      }
      const classesProp = (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_4__.getClassesProp)(container);
      if (!classesProp) {
        return;
      }
      const [firstVariant] = revertData.originalStyle.variants;
      const additionalVariants = revertData.originalStyle.variants.slice(1);
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.createElementStyle)({
        elementId: container.id,
        classesProp,
        label: _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.ELEMENTS_STYLES_RESERVED_LABEL,
        styleId: revertData.styleId,
        ...firstVariant,
        additionalVariants
      });
    });
  }
}, {
  title: ({
    containers
  }) => (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_4__.getTitleForContainers)(containers),
  subtitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Style Pasted', 'elementor')
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/style-commands/undoable-actions/reset-element-style.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/style-commands/undoable-actions/reset-element-style.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   undoableResetElementStyle: function() { return /* binding */ undoableResetElementStyle; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _utils_command_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/command-utils */ "./packages/packages/core/editor-canvas/src/utils/command-utils.ts");





const undoableResetElementStyle = () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.undoable)({
  do: ({
    containers
  }) => {
    return containers.map(container => {
      const elementId = container.model.get('id');
      const containerStyles = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementStyles)(elementId);
      Object.keys(containerStyles ?? {}).forEach(styleId => (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.deleteElementStyle)(elementId, styleId));
      return containerStyles;
    });
  },
  undo: ({
    containers
  }, revertDataItems) => {
    containers.forEach((container, index) => {
      const classesProp = (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_4__.getClassesProp)(container);
      if (!classesProp) {
        return;
      }
      const elementId = container.model.get('id');
      const containerStyles = revertDataItems[index];
      Object.entries(containerStyles ?? {}).forEach(([styleId, style]) => {
        const [firstVariant] = style.variants;
        const additionalVariants = style.variants.slice(1);
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.createElementStyle)({
          elementId,
          classesProp,
          styleId,
          label: _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.ELEMENTS_STYLES_RESERVED_LABEL,
          ...firstVariant,
          additionalVariants
        });
      });
    });
  }
}, {
  title: ({
    containers
  }) => (0,_utils_command_utils__WEBPACK_IMPORTED_MODULE_4__.getTitleForContainers)(containers),
  subtitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Style Reset', 'elementor')
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/style-transformers-registry.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/style-transformers-registry.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   styleTransformersRegistry: function() { return /* binding */ styleTransformersRegistry; }
/* harmony export */ });
/* harmony import */ var _transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./transformers/create-transformers-registry */ "./packages/packages/core/editor-canvas/src/transformers/create-transformers-registry.ts");

const styleTransformersRegistry = (0,_transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_0__.createTransformersRegistry)();

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/sync/drag-element-from-panel.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/sync/drag-element-from-panel.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   endDragElementFromPanel: function() { return /* binding */ endDragElementFromPanel; },
/* harmony export */   startDragElementFromPanel: function() { return /* binding */ startDragElementFromPanel; }
/* harmony export */ });
const DRAG_GROUPS = ['elementor-element'];
const endDragElementFromPanel = () => {
  getElementorChannels()?.panelElements?.trigger('element:drag:end');
};
const startDragElementFromPanel = (props, event) => {
  setDragGroups(event);
  const channels = getElementorChannels();
  channels?.editor.reply('element:dragged', null);
  channels?.panelElements.reply('element:selected', getLegacyPanelElementView(props)).trigger('element:drag:start');
};
const setDragGroups = event => {
  const dataContainer = {
    groups: getDragGroups(event)
  };
  event.dataTransfer?.setData(JSON.stringify(dataContainer), 'true');
};
const getDragGroups = event => {
  const dataContainer = event.dataTransfer?.getData('text/plain');
  return dataContainer ? JSON.parse(dataContainer).groups : DRAG_GROUPS;
};
const getElementorChannels = () => {
  const extendedWindow = window;
  const channels = extendedWindow.elementor?.channels;
  if (!channels) {
    throw new Error('Elementor channels not found: Elementor editor is not initialized or channels are unavailable.');
  }
  return channels;
};
const getLegacyPanelElementView = ({
  settings,
  ...rest
}) => {
  const extendedWindow = window;
  const LegacyElementModel = extendedWindow.elementor?.modules?.elements?.models?.Element;
  if (!LegacyElementModel) {
    throw new Error('Elementor legacy Element model not found in editor modules');
  }
  const elementModel = new LegacyElementModel({
    ...rest,
    custom: {
      isPreset: !!settings,
      preset_settings: settings
    }
  });
  return {
    model: elementModel
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/sync/global-styles-imported-event.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/sync/global-styles-imported-event.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GLOBAL_STYLES_IMPORTED_EVENT: function() { return /* binding */ GLOBAL_STYLES_IMPORTED_EVENT; }
/* harmony export */ });
const GLOBAL_STYLES_IMPORTED_EVENT = 'elementor/global-styles/imported';

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createTransformer: function() { return /* binding */ createTransformer; }
/* harmony export */ });
// Wrap transformer for better DX (types).
// Inspired by: https://tkdodo.eu/blog/the-query-options-api
function createTransformer(cb) {
  return cb;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/create-transformers-registry.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/create-transformers-registry.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createTransformersRegistry: function() { return /* binding */ createTransformersRegistry; },
/* harmony export */   stylesInheritanceTransformersRegistry: function() { return /* binding */ stylesInheritanceTransformersRegistry; }
/* harmony export */ });
function createTransformersRegistry() {
  const transformers = {};
  let fallbackTransformer = null;
  return {
    register(type, transformer) {
      transformers[type] = transformer;
      return this;
    },
    registerFallback(transformer) {
      fallbackTransformer = transformer;
      return this;
    },
    get(type) {
      return transformers[type] ?? fallbackTransformer;
    },
    all() {
      return {
        ...transformers
      };
    }
  };
}
const stylesInheritanceTransformersRegistry = createTransformersRegistry();

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/attributes-transformer.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/attributes-transformer.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   attributesTransformer: function() { return /* binding */ attributesTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const attributesTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(() => '');

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/classes-transformer.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/classes-transformer.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createClassesTransformer: function() { return /* binding */ createClassesTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


function transformClassId(id, cache) {
  if (!cache.has(id)) {
    const provider = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.stylesRepository.getProviders().find(p => {
      return p.actions.all().find(style => style.id === id);
    });
    if (!provider) {
      return id;
    }
    cache.set(id, provider.getKey());
  }
  const providerKey = cache.get(id);
  const provider = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.stylesRepository.getProviderByKey(providerKey);
  return provider?.actions.resolveCssName(id) ?? id;
}
function createClassesTransformer() {
  const cache = new Map();
  return (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => {
    return value.map(id => transformClassId(id, cache)).filter(Boolean);
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/date-range-transformer.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/date-range-transformer.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dateRangeTransformer: function() { return /* binding */ dateRangeTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const dateRangeTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (!value || Object.keys(value).length === 0) {
    return null;
  }
  return {
    min: value.min || null,
    max: value.max || null
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/date-time-transformer.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/date-time-transformer.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dateTimeTransformer: function() { return /* binding */ dateTimeTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const dateTimeTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(values => {
  return values.map(value => {
    const date = (value.date || '').trim();
    const time = (value.time || '').trim();
    return !date && !time ? '' : `${date} ${time}`.trim();
  }).join(' ');
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/html-v2-transformer.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/html-v2-transformer.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   htmlV2Transformer: function() { return /* binding */ htmlV2Transformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const htmlV2Transformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return value?.content ?? '';
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/html-v3-transformer.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/html-v3-transformer.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   htmlV3Transformer: function() { return /* binding */ htmlV3Transformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const htmlV3Transformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return value?.content ?? '';
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/link-transformer.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/link-transformer.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   linkTransformer: function() { return /* binding */ linkTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const linkTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(({
  destination,
  isTargetBlank,
  tag
}) => {
  return {
    // The real post URL is not relevant in the Editor.
    href: typeof destination === 'number' ? '#post-id-' + destination : destination,
    target: isTargetBlank ? '_blank' : '_self',
    tag: tag ?? 'a'
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/query-transformer.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/query-transformer.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   queryTransformer: function() { return /* binding */ queryTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const queryTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(({
  id
}) => {
  return id ?? null;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/settings/time-range-transformer.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/settings/time-range-transformer.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   timeRangeTransformer: function() { return /* binding */ timeRangeTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const timeRangeTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (!value || Object.keys(value).length === 0) {
    return null;
  }
  return {
    min: value.min || null,
    max: value.max || null
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/shared/image-src-transformer.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/shared/image-src-transformer.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   imageSrcTransformer: function() { return /* binding */ imageSrcTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const imageSrcTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => ({
  id: value.id ?? null,
  url: value.url ?? null,
  alt: value.alt ?? null
}));

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/shared/image-transformer.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/shared/image-transformer.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   imageTransformer: function() { return /* binding */ imageTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/wp-media */ "@elementor/wp-media");
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_wp_media__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


const imageTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(async value => {
  const {
    src,
    size
  } = value;
  if (!src?.id) {
    return src?.url ? {
      src: src.url,
      alt: src.alt ?? ''
    } : null;
  }
  const attachment = await (0,_elementor_wp_media__WEBPACK_IMPORTED_MODULE_0__.getMediaAttachment)({
    id: src.id
  });
  const sizedAttachment = attachment?.sizes?.[size ?? ''];
  if (sizedAttachment) {
    return {
      src: sizedAttachment.url,
      height: sizedAttachment.height,
      width: sizedAttachment.width,
      alt: attachment.alt
    };
  }
  if (attachment) {
    return {
      src: attachment.url,
      height: attachment.height,
      width: attachment.width,
      alt: attachment.alt
    };
  }
  return null;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/shared/plain-transformer.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/shared/plain-transformer.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   plainTransformer: function() { return /* binding */ plainTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const plainTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return value;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/shared/svg-src-transformer.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/shared/svg-src-transformer.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   svgSrcTransformer: function() { return /* binding */ svgSrcTransformer; }
/* harmony export */ });
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! dompurify */ "./node_modules/dompurify/dist/purify.es.mjs");
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/wp-media */ "@elementor/wp-media");
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_wp_media__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");



const SVG_INLINE_STYLES = 'width: 100%; height: 100%; overflow: unset;';
function processSvgContent(svgText) {
  const sanitized = dompurify__WEBPACK_IMPORTED_MODULE_0__["default"].sanitize(svgText, {
    USE_PROFILES: {
      svg: true,
      svgFilters: true
    }
  });
  const parser = new DOMParser();
  const doc = parser.parseFromString(sanitized, 'image/svg+xml');
  const svgElement = doc.querySelector('svg');
  if (!svgElement) {
    return null;
  }
  svgElement.setAttribute('fill', 'currentColor');
  const existingStyle = svgElement.getAttribute('style') ?? '';
  const trimmed = existingStyle.trim();
  const merged = trimmed ? `${trimmed.replace(/;$/, '')}; ${SVG_INLINE_STYLES}` : SVG_INLINE_STYLES;
  svgElement.setAttribute('style', merged);
  return svgElement.outerHTML;
}
async function fetchSvgContent(url, signal) {
  try {
    const response = await fetch(url, {
      signal
    });
    if (!response.ok) {
      return null;
    }
    const contentType = response.headers.get('content-type') ?? '';
    const isSvg = contentType.includes('svg') || contentType.includes('xml') || url.endsWith('.svg');
    if (!isSvg) {
      return null;
    }
    return await response.text();
  } catch {
    return null;
  }
}
function resolveSvgSrcId(id) {
  if (typeof id !== 'number' || id <= 0) {
    return null;
  }
  return id;
}
const svgSrcTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_2__.createTransformer)(async (value, {
  signal
}) => {
  const id = resolveSvgSrcId(value.id);
  const urlFromValue = typeof value.url === 'string' ? value.url : null;
  let url = urlFromValue;
  if (id && !urlFromValue) {
    const attachment = await (0,_elementor_wp_media__WEBPACK_IMPORTED_MODULE_1__.getMediaAttachment)({
      id
    });
    url = attachment?.url ?? null;
  }
  const resolvedUrl = typeof url === 'string' ? url : null;
  if (!resolvedUrl) {
    return {
      html: null,
      url: null
    };
  }
  const svgText = await fetchSvgContent(resolvedUrl, signal);
  const html = svgText ? processSvgContent(svgText) : null;
  return {
    html,
    url: resolvedUrl
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/shared/video-src-transformer.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/shared/video-src-transformer.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   videoSrcTransformer: function() { return /* binding */ videoSrcTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/wp-media */ "@elementor/wp-media");
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_wp_media__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


const videoSrcTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(async value => {
  const {
    id,
    url
  } = value;
  if (!id) {
    return {
      id: null,
      url
    };
  }
  const attachment = await (0,_elementor_wp_media__WEBPACK_IMPORTED_MODULE_0__.getMediaAttachment)({
    id
  });
  return {
    id,
    url: attachment?.url ?? url
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/background-color-overlay-transformer.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/background-color-overlay-transformer.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundColorOverlayTransformer: function() { return /* binding */ backgroundColorOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const backgroundColorOverlayTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  const {
    color = null
  } = value;
  if (!color) {
    return null;
  }
  return `linear-gradient(${color}, ${color})`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/background-gradient-overlay-transformer.ts":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/background-gradient-overlay-transformer.ts ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundGradientOverlayTransformer: function() { return /* binding */ backgroundGradientOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const backgroundGradientOverlayTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (value.type === 'radial') {
    return `radial-gradient(circle at ${value.positions}, ${value.stops})`;
  }
  return `linear-gradient(${value.angle}deg, ${value.stops})`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/background-image-overlay-transformer.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/background-image-overlay-transformer.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundImageOverlayTransformer: function() { return /* binding */ backgroundImageOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const backgroundImageOverlayTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  const {
    image,
    size = null,
    position = null,
    repeat = null,
    attachment = null
  } = value;
  if (!image) {
    return null;
  }
  const src = image.src ? `url(${image.src})` : null;
  const backgroundStyles = {
    src,
    repeat,
    attachment,
    size,
    position
  };
  return backgroundStyles;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/background-image-size-scale-transformer.ts":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/background-image-size-scale-transformer.ts ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundImageSizeScaleTransformer: function() { return /* binding */ backgroundImageSizeScaleTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const backgroundImageSizeScaleTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(({
  width,
  height
}) => `${width ?? 'auto'} ${height ?? 'auto'}`);

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/background-overlay-transformer.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/background-overlay-transformer.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundOverlayTransformer: function() { return /* binding */ backgroundOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const backgroundOverlayTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (!value || value.length === 0) {
    return null;
  }
  const normalizedValues = normalizeOverlayValues(value);
  if (normalizedValues.length === 0) {
    return null;
  }
  const images = getValuesString(normalizedValues, 'src', 'none', true);
  const repeats = getValuesString(normalizedValues, 'repeat', 'repeat');
  const attachments = getValuesString(normalizedValues, 'attachment', 'scroll');
  const sizes = getValuesString(normalizedValues, 'size', 'auto auto');
  const positions = getValuesString(normalizedValues, 'position', '0% 0%');
  return {
    'background-image': images,
    'background-repeat': repeats,
    'background-attachment': attachments,
    'background-size': sizes,
    'background-position': positions
  };
});
function normalizeOverlayValues(overlays) {
  const mappedValues = overlays.map(item => {
    if (typeof item === 'string') {
      return {
        src: item,
        repeat: null,
        attachment: null,
        size: null,
        position: null
      };
    }
    return item;
  });
  return mappedValues.filter(item => item && !!item.src);
}
function getValuesString(items, prop, defaultValue, preventUnification = false) {
  const isEmpty = items.filter(item => item?.[prop]).length === 0;
  if (isEmpty) {
    return defaultValue;
  }
  const formattedValues = items.map(item => item[prop] ?? defaultValue);
  if (!preventUnification) {
    const allSame = formattedValues.every(value => value === formattedValues[0]);
    if (allSame) {
      return formattedValues[0];
    }
  }
  return formattedValues.join(',');
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/background-transformer.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/background-transformer.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundTransformer: function() { return /* binding */ backgroundTransformer; }
/* harmony export */ });
/* harmony import */ var _renderers_multi_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../renderers/multi-props */ "./packages/packages/core/editor-canvas/src/renderers/multi-props.ts");
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


const backgroundTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => {
  const {
    color = null,
    'background-overlay': overlays = null,
    clip = null
  } = value;
  return (0,_renderers_multi_props__WEBPACK_IMPORTED_MODULE_0__.createMultiPropsValue)({
    ...overlays,
    'background-color': color,
    'background-clip': clip
  });
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/color-stop-transformer.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/color-stop-transformer.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorStopTransformer: function() { return /* binding */ colorStopTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const colorStopTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => `${value?.color} ${value?.offset ?? 0}%`);

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/create-combine-array-transformer.ts":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/create-combine-array-transformer.ts ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createCombineArrayTransformer: function() { return /* binding */ createCombineArrayTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const createCombineArrayTransformer = delimiter => {
  return (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => value?.length ? value.filter(Boolean).join(delimiter) : null);
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/create-multi-props-transformer.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/create-multi-props-transformer.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createMultiPropsTransformer: function() { return /* binding */ createMultiPropsTransformer; }
/* harmony export */ });
/* harmony import */ var _renderers_multi_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../renderers/multi-props */ "./packages/packages/core/editor-canvas/src/renderers/multi-props.ts");
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


const createMultiPropsTransformer = (keys, keyGenerator) => {
  return (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)((value, {
    key: propKey
  }) => {
    const entries = keys.filter(key => value[key]).map(key => [keyGenerator({
      propKey,
      key
    }), value[key]]);
    return (0,_renderers_multi_props__WEBPACK_IMPORTED_MODULE_0__.createMultiPropsValue)(Object.fromEntries(entries));
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/filter-transformer.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/filter-transformer.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   filterTransformer: function() { return /* binding */ filterTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const filterTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(filterValues => {
  if (filterValues?.length < 1) {
    return null;
  }
  return filterValues.filter(Boolean).map(mapToFilterFunctionString).join(' ');
});
const mapToFilterFunctionString = value => {
  if (value.func === 'drop-shadow') {
    const {
      xAxis,
      yAxis,
      blur,
      color
    } = value.args;
    return `drop-shadow(${xAxis || '0px'} ${yAxis || '0px'} ${blur || '10px'} ${color || 'transparent'})`;
  }
  const size = value.args?.size;
  if (!value.func || !size) {
    return '';
  }
  return `${value.func}(${size})`;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/flex-transformer.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/flex-transformer.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   flexTransformer: function() { return /* binding */ flexTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const flexTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  const grow = value.flexGrow;
  const shrink = value.flexShrink;
  const basis = value.flexBasis;
  const hasGrow = grow !== undefined && grow !== null;
  const hasShrink = shrink !== undefined && shrink !== null;
  const hasBasis = basis !== undefined && basis !== null;
  if (!hasGrow && !hasShrink && !hasBasis) {
    return null;
  }
  if (hasGrow && hasShrink && hasBasis) {
    return `${grow} ${shrink} ${typeof basis === 'object' && basis.size !== undefined ? `${basis.size}${basis.unit || ''}` : basis}`;
  }
  if (hasGrow && hasShrink && !hasBasis) {
    return `${grow} ${shrink}`;
  }
  if (hasGrow && !hasShrink && hasBasis) {
    return `${grow} 1 ${typeof basis === 'object' && basis.size !== undefined ? `${basis.size}${basis.unit || ''}` : basis}`;
  }
  if (!hasGrow && hasShrink && hasBasis) {
    return `0 ${shrink} ${typeof basis === 'object' && basis.size !== undefined ? `${basis.size}${basis.unit || ''}` : basis}`;
  }
  if (hasGrow && !hasShrink && !hasBasis) {
    return `${grow}`;
  }
  if (!hasGrow && hasShrink && !hasBasis) {
    return `0 ${shrink}`;
  }
  if (!hasGrow && !hasShrink && hasBasis) {
    return `0 1 ${typeof basis === 'object' && basis.size !== undefined ? `${basis.size}${basis.unit || ''}` : basis}`;
  }
  return null;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/font-family-transformer.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/font-family-transformer.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fontFamilyTransformer: function() { return /* binding */ fontFamilyTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const fontFamilyTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (typeof value !== 'string' || !value.trim()) {
    return null;
  }
  const trimmed = value.trim();
  if (trimmed.startsWith('"') && trimmed.endsWith('"') || trimmed.startsWith("'") && trimmed.endsWith("'")) {
    return trimmed;
  }
  return `"${trimmed}"`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-renderer.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-renderer.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   formatGridTrackRepeat: function() { return /* binding */ formatGridTrackRepeat; },
/* harmony export */   isGridTrackProperty: function() { return /* binding */ isGridTrackProperty; }
/* harmony export */ });
const GRID_TRACK_PROPERTIES = new Set(['grid-template-columns', 'grid-template-rows']);
const isGridTrackProperty = cssProperty => GRID_TRACK_PROPERTIES.has(cssProperty);
const formatGridTrackRepeat = count => {
  if (!Number.isFinite(count) || count < 1) {
    return null;
  }
  return `repeat(${count}, 1fr)`;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-size-transformer.ts":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-size-transformer.ts ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   gridTrackSizeTransformer: function() { return /* binding */ gridTrackSizeTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");
/* harmony import */ var _grid_track_renderer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid-track-renderer */ "./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-renderer.ts");


const gridTrackSizeTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (value.unit === 'custom') {
    return value.size;
  }
  if (value.unit === 'fr') {
    return (0,_grid_track_renderer__WEBPACK_IMPORTED_MODULE_1__.formatGridTrackRepeat)(Math.trunc(Number(value.size)));
  }
  return `${value.size}${value.unit}`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/perspective-origin-transformer.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/perspective-origin-transformer.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   perspectiveOriginTransformer: function() { return /* binding */ perspectiveOriginTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const FALLBACK = '0px';
function getVal(val) {
  return `${val ?? FALLBACK}`;
}
const perspectiveOriginTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => `${getVal(value?.x)} ${getVal(value?.y)}`);

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/position-transformer.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/position-transformer.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   positionTransformer: function() { return /* binding */ positionTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const positionTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(({
  x,
  y
}) => `${x ?? '0px'} ${y ?? '0px'}`);

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/shadow-transformer.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/shadow-transformer.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   shadowTransformer: function() { return /* binding */ shadowTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const shadowTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return [value.hOffset, value.vOffset, value.blur, value.spread, value.color, value.position].filter(Boolean).join(' ');
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/size-transformer.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/size-transformer.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   sizeTransformer: function() { return /* binding */ sizeTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const sizeTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  if (value.unit === 'auto') {
    return 'auto';
  }
  return value.unit === 'custom' ? value.size : `${value.size}${value.unit}`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/span-transformer.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/span-transformer.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   spanTransformer: function() { return /* binding */ spanTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const spanTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return value?.trim() || null;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/stroke-transformer.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/stroke-transformer.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   strokeTransformer: function() { return /* binding */ strokeTransformer; }
/* harmony export */ });
/* harmony import */ var _renderers_multi_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../renderers/multi-props */ "./packages/packages/core/editor-canvas/src/renderers/multi-props.ts");
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


const strokeTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => {
  const parsed = {
    '-webkit-text-stroke': `${value.width} ${value.color}`,
    stroke: `${value.color}`,
    'stroke-width': `${value.width}`
  };
  return (0,_renderers_multi_props__WEBPACK_IMPORTED_MODULE_0__.createMultiPropsValue)(parsed);
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-functions-transformer.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transform-functions-transformer.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformFunctionsTransformer: function() { return /* binding */ transformFunctionsTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const transformFunctionsTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(values => {
  if (values?.length < 1) {
    return null;
  }
  return values.join(' ');
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-move-transformer.ts":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transform-move-transformer.ts ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformMoveTransformer: function() { return /* binding */ transformMoveTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const defaultMove = '0px';
const transformMoveTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return `translate3d(${value.x ?? defaultMove}, ${value.y ?? defaultMove}, ${value.z ?? defaultMove})`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-origin-transformer.ts":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transform-origin-transformer.ts ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformOriginTransformer: function() { return /* binding */ transformOriginTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const EMPTY_VALUE = '0px';
const DEFAULT_XY = '50%';
const DEFAULT_Z = EMPTY_VALUE;
function getVal(val) {
  return `${val ?? EMPTY_VALUE}`;
}
const transformOriginTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  const x = getVal(value.x);
  const y = getVal(value.y);
  const z = getVal(value.z);
  if (x === DEFAULT_XY && y === DEFAULT_XY && z === DEFAULT_Z) {
    return null;
  }
  return `${x} ${y} ${z}`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-rotate-transformer.ts":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transform-rotate-transformer.ts ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformRotateTransformer: function() { return /* binding */ transformRotateTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const defaultRotate = '0deg';
const transformRotateTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  const transforms = [`rotateX(${value?.x ?? defaultRotate})`, `rotateY(${value?.y ?? defaultRotate})`, `rotateZ(${value?.z ?? defaultRotate})`];
  return transforms.join(' ');
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-scale-transformer.ts":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transform-scale-transformer.ts ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformScaleTransformer: function() { return /* binding */ transformScaleTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const transformScaleTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  return `scale3d(${value.x ?? 1}, ${value.y ?? 1}, ${value.z ?? 1})`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transform-skew-transformer.ts":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transform-skew-transformer.ts ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformSkewTransformer: function() { return /* binding */ transformSkewTransformer; }
/* harmony export */ });
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");

const defaultSkew = '0deg';
const transformSkewTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
  const x = value?.x ?? defaultSkew;
  const y = value?.y ?? defaultSkew;
  return `skew(${x}, ${y})`;
});

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/transformers/styles/transition-transformer.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/transformers/styles/transition-transformer.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transitionTransformer: function() { return /* binding */ transitionTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _create_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");


const getAllowedProperties = () => {
  const allowedProperties = new Set();
  _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.transitionProperties.forEach(category => {
    category.properties.forEach(property => {
      allowedProperties.add(property.value);
    });
  });
  return allowedProperties;
};
const transitionTransformer = (0,_create_transformer__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(transitionValues => {
  if (transitionValues?.length < 1) {
    return null;
  }
  const allowedProperties = getAllowedProperties();
  const validTransitions = transitionValues.map(value => mapToTransitionString(value, allowedProperties)).filter(Boolean);
  if (validTransitions.length === 0) {
    return null;
  }
  return validTransitions.join(', ');
});
const mapToTransitionString = (value, allowedProperties) => {
  if (!value.selection || !value.size) {
    return '';
  }
  const property = value.selection.value;
  if (!allowedProperties.has(property)) {
    return '';
  }
  return `${property} ${value.size}`;
};

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/abort-previous-runs.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/abort-previous-runs.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   abortPreviousRuns: function() { return /* binding */ abortPreviousRuns; }
/* harmony export */ });
// eslint-disable-next-line @typescript-eslint/no-explicit-any
function abortPreviousRuns(cb) {
  let abortController = null;
  return (...args) => {
    if (abortController) {
      abortController.abort();
    }
    abortController = new AbortController();
    return cb(abortController, ...args);
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/after-render.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/after-render.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   doAfterRender: function() { return /* binding */ doAfterRender; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

function doAfterRender(elementIds, callback) {
  const pending = elementIds.map(elementId => {
    const view = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId)?.view;
    if (!view || !hasDoAfterRender(view)) {
      return undefined;
    }
    return new Promise(resolve => view._doAfterRender(resolve));
  }).filter(Boolean);
  if (pending.length > 0) {
    Promise.all(pending).then(() => callback(elementIds));
  } else {
    callback(elementIds);
  }
}
function hasDoAfterRender(view) {
  return typeof view?._doAfterRender === 'function';
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/command-utils.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/command-utils.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getClassesProp: function() { return /* binding */ getClassesProp; },
/* harmony export */   getClipboardElements: function() { return /* binding */ getClipboardElements; },
/* harmony export */   getTitleForContainers: function() { return /* binding */ getTitleForContainers; },
/* harmony export */   hasAtomicWidgets: function() { return /* binding */ hasAtomicWidgets; },
/* harmony export */   isAtomicWidget: function() { return /* binding */ isAtomicWidget; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function hasAtomicWidgets(args) {
  const {
    containers = [args.container]
  } = args;
  return containers.some(isAtomicWidget);
}
function isAtomicWidget(container) {
  if (!container) {
    return false;
  }
  return Boolean(getContainerSchema(container));
}
function getClassesProp(container) {
  const propsSchema = getContainerSchema(container);
  if (!propsSchema) {
    return null;
  }
  const [propKey] = Object.entries(propsSchema).find(([, propType]) => propType.kind === 'plain' && propType.key === _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.CLASSES_PROP_KEY) ?? [];
  return propKey ?? null;
}
function getContainerSchema(container) {
  const type = container?.model.get('widgetType') || container?.model.get('elType');
  const widgetsCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)();
  const elementType = widgetsCache?.[type];
  return elementType?.atomic_props_schema ?? null;
}
function getClipboardElements(storageKey = 'clipboard') {
  try {
    const storedData = JSON.parse(localStorage.getItem('elementor') ?? '{}');
    return storedData[storageKey]?.elements;
  } catch {
    return undefined;
  }
}
function getTitleForContainers(containers) {
  return containers.length > 1 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Elements', 'elementor') : (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementLabel)(containers[0].id);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/find-first-empty-cell.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/find-first-empty-cell.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   findFirstEmptyCell: function() { return /* binding */ findFirstEmptyCell; }
/* harmony export */ });
function findFirstEmptyCell(element, columnCount, rowCount) {
  if (!element || columnCount === 0 || rowCount === 0) {
    return null;
  }
  const previewWindow = element.ownerDocument?.defaultView;
  if (!previewWindow) {
    return null;
  }
  const containerStyle = previewWindow.getComputedStyle(element);
  const flowsByColumn = containerStyle.gridAutoFlow.trim().startsWith('column');
  const matrix = Array.from({
    length: rowCount
  }, () => new Array(columnCount).fill(false));
  const explicit = [];
  const autoPlaced = [];
  for (const child of Array.from(element.children)) {
    if (!child.classList.contains('elementor-element')) {
      continue;
    }
    const style = previewWindow.getComputedStyle(child);
    if (style.display === 'none') {
      continue;
    }
    const col = resolvePlacement(style.gridColumnStart, style.gridColumnEnd);
    const row = resolvePlacement(style.gridRowStart, style.gridRowEnd);
    if (col.start !== null || row.start !== null) {
      explicit.push({
        col: col.start,
        colSpan: col.span,
        row: row.start,
        rowSpan: row.span
      });
    } else {
      autoPlaced.push({
        colSpan: col.span,
        rowSpan: row.span
      });
    }
  }
  for (const child of explicit) {
    fillMatrix(matrix, child.col ?? 0, child.row ?? 0, child.colSpan, child.rowSpan);
  }
  for (const child of autoPlaced) {
    const slot = findNextFreeSlot(matrix, child.colSpan, child.rowSpan, flowsByColumn);
    if (slot) {
      fillMatrix(matrix, slot.col, slot.row, child.colSpan, child.rowSpan);
    }
  }
  return scanFirstEmpty(matrix, flowsByColumn);
}
function resolvePlacement(startRaw, endRaw) {
  const start = parseLineValue(startRaw);
  const end = parseLineValue(endRaw);
  if (typeof start === 'number') {
    const zeroIndexedStart = start - 1;
    if (typeof end === 'number') {
      return {
        start: zeroIndexedStart,
        span: Math.max(1, end - start)
      };
    }
    if (isSpan(end)) {
      return {
        start: zeroIndexedStart,
        span: end.n
      };
    }
    return {
      start: zeroIndexedStart,
      span: 1
    };
  }
  if (isSpan(start)) {
    if (typeof end === 'number') {
      const zeroIndexedStart = end - 1 - start.n;
      return {
        start: zeroIndexedStart >= 0 ? zeroIndexedStart : null,
        span: start.n
      };
    }
    return {
      start: null,
      span: start.n
    };
  }
  if (typeof end === 'number') {
    const zeroIndexedStart = end - 2;
    return {
      start: zeroIndexedStart >= 0 ? zeroIndexedStart : null,
      span: 1
    };
  }
  if (isSpan(end)) {
    return {
      start: null,
      span: end.n
    };
  }
  return {
    start: null,
    span: 1
  };
}
function parseLineValue(raw) {
  const trimmed = raw.trim();
  if (trimmed === '' || trimmed === 'auto') {
    return 'auto';
  }
  const spanMatch = trimmed.match(/^span\s+(\d+)$/);
  if (spanMatch) {
    const n = parseInt(spanMatch[1], 10);
    return {
      kind: 'span',
      n: Math.max(1, n)
    };
  }
  const parsed = parseInt(trimmed, 10);
  if (Number.isFinite(parsed) && parsed > 0) {
    return parsed;
  }
  return 'auto';
}
function isSpan(value) {
  return typeof value === 'object' && value !== null && 'kind' in value && value.kind === 'span';
}
function fillMatrix(matrix, col, row, colSpan, rowSpan) {
  const rows = matrix.length;
  const cols = rows > 0 ? matrix[0].length : 0;
  const startRow = Math.max(0, row);
  const startCol = Math.max(0, col);
  const endRow = Math.min(rows, row + rowSpan);
  const endCol = Math.min(cols, col + colSpan);
  for (let r = startRow; r < endRow; r++) {
    for (let c = startCol; c < endCol; c++) {
      matrix[r][c] = true;
    }
  }
}
function findNextFreeSlot(matrix, colSpan, rowSpan, flowsByColumn) {
  const rows = matrix.length;
  const cols = rows > 0 ? matrix[0].length : 0;
  const maxCol = cols - colSpan;
  const maxRow = rows - rowSpan;
  if (maxCol < 0 || maxRow < 0) {
    return null;
  }
  if (flowsByColumn) {
    for (let col = 0; col <= maxCol; col++) {
      for (let row = 0; row <= maxRow; row++) {
        if (canFit(matrix, col, row, colSpan, rowSpan)) {
          return {
            row,
            col
          };
        }
      }
    }
  } else {
    for (let row = 0; row <= maxRow; row++) {
      for (let col = 0; col <= maxCol; col++) {
        if (canFit(matrix, col, row, colSpan, rowSpan)) {
          return {
            row,
            col
          };
        }
      }
    }
  }
  return null;
}
function canFit(matrix, col, row, colSpan, rowSpan) {
  for (let r = row; r < row + rowSpan; r++) {
    for (let c = col; c < col + colSpan; c++) {
      if (matrix[r][c]) {
        return false;
      }
    }
  }
  return true;
}
function scanFirstEmpty(matrix, flowsByColumn) {
  const rows = matrix.length;
  const cols = rows > 0 ? matrix[0].length : 0;
  if (flowsByColumn) {
    for (let col = 0; col < cols; col++) {
      for (let row = 0; row < rows; row++) {
        if (!matrix[row][col]) {
          return {
            row,
            col
          };
        }
      }
    }
  } else {
    for (let row = 0; row < rows; row++) {
      for (let col = 0; col < cols; col++) {
        if (!matrix[row][col]) {
          return {
            row,
            col
          };
        }
      }
    }
  }
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/grid-outline-utils.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/grid-outline-utils.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   computeCellRects: function() { return /* binding */ computeCellRects; },
/* harmony export */   computeGridLines: function() { return /* binding */ computeGridLines; },
/* harmony export */   parseTrackList: function() { return /* binding */ parseTrackList; },
/* harmony export */   resolveGapPx: function() { return /* binding */ resolveGapPx; },
/* harmony export */   snapToHalfPixel: function() { return /* binding */ snapToHalfPixel; },
/* harmony export */   toGridTracks: function() { return /* binding */ toGridTracks; },
/* harmony export */   toPx: function() { return /* binding */ toPx; }
/* harmony export */ });
function toGridTracks(computedStyle) {
  return {
    columns: parseTrackList(computedStyle.gridTemplateColumns),
    rows: parseTrackList(computedStyle.gridTemplateRows),
    columnGap: resolveGapPx(computedStyle.columnGap, computedStyle.width),
    rowGap: resolveGapPx(computedStyle.rowGap, computedStyle.height),
    padding: {
      top: toPx(computedStyle.paddingTop),
      right: toPx(computedStyle.paddingRight),
      bottom: toPx(computedStyle.paddingBottom),
      left: toPx(computedStyle.paddingLeft)
    },
    borderColor: computedStyle.getPropertyValue('--e-a-border-color-bold').trim()
  };
}
function computeCellRects(tracks, width, height) {
  const {
    columns,
    rows,
    columnGap,
    rowGap,
    padding
  } = tracks;
  const hasColumns = columns.length > 0;
  const hasRows = rows.length > 0;
  if (!hasColumns && !hasRows) {
    return [];
  }
  const columnSegments = hasColumns ? computeTrackSegments(columns, columnGap, padding.left) : [{
    start: padding.left,
    size: width - padding.left - padding.right
  }];
  const rowSegments = hasRows ? computeTrackSegments(rows, rowGap, padding.top) : [{
    start: padding.top,
    size: height - padding.top - padding.bottom
  }];
  const cells = [];
  for (const row of rowSegments) {
    for (const column of columnSegments) {
      cells.push({
        x: column.start,
        y: row.start,
        width: column.size,
        height: row.size
      });
    }
  }
  return cells;
}
function computeGridLines(tracks, width, height) {
  const {
    columns,
    rows,
    columnGap,
    rowGap,
    padding
  } = tracks;
  const hasColumns = columns.length > 0;
  const hasRows = rows.length > 0;
  if (!hasColumns && !hasRows) {
    return {
      vertical: [],
      horizontal: []
    };
  }
  const columnSegments = hasColumns ? computeTrackSegments(columns, columnGap, padding.left) : [{
    start: padding.left,
    size: width - padding.left - padding.right
  }];
  const rowSegments = hasRows ? computeTrackSegments(rows, rowGap, padding.top) : [{
    start: padding.top,
    size: height - padding.top - padding.bottom
  }];
  const xs = uniqueSorted(columnSegments.flatMap(s => [s.start, s.start + s.size]));
  const ys = uniqueSorted(rowSegments.flatMap(s => [s.start, s.start + s.size]));
  const yTop = ys[0];
  const yBottom = ys[ys.length - 1];
  const xLeft = xs[0];
  const xRight = xs[xs.length - 1];
  return {
    vertical: xs.map(x => ({
      x1: x,
      y1: yTop,
      x2: x,
      y2: yBottom
    })),
    horizontal: ys.map(y => ({
      x1: xLeft,
      y1: y,
      x2: xRight,
      y2: y
    }))
  };
}
function uniqueSorted(values) {
  return Array.from(new Set(values)).sort((a, b) => a - b);
}
function computeTrackSegments(sizes, gap, offset) {
  const segments = [];
  let cursor = offset;
  for (let i = 0; i < sizes.length; i++) {
    segments.push({
      start: cursor,
      size: sizes[i]
    });
    cursor += sizes[i];
    if (i < sizes.length - 1) {
      cursor += gap;
    }
  }
  return segments;
}
function snapToHalfPixel(value) {
  return Math.round(value) + 0.5;
}
function parseTrackList(value) {
  if (!value || value === 'none') {
    return [];
  }
  return value.trim().split(/\s+/).map(toPx).filter(n => n > 0);
}
function toPx(value) {
  const parsed = parseFloat(value);
  return Number.isFinite(parsed) ? parsed : 0;
}
function resolveGapPx(value, referenceSize) {
  if (value.trim().endsWith('%')) {
    const percent = parseFloat(value);
    const reference = parseFloat(referenceSize);
    return Number.isFinite(percent) && Number.isFinite(reference) ? percent / 100 * reference : 0;
  }
  return toPx(value);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/outline-offset-utils.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/outline-offset-utils.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SMALLER_OUTLINE_OFFSET_WIDGET_TYPES: function() { return /* binding */ SMALLER_OUTLINE_OFFSET_WIDGET_TYPES; },
/* harmony export */   THIN_ELEMENT_MAX_HEIGHT_PX: function() { return /* binding */ THIN_ELEMENT_MAX_HEIGHT_PX; },
/* harmony export */   shouldUseSmallerOutlineOffset: function() { return /* binding */ shouldUseSmallerOutlineOffset; }
/* harmony export */ });
const THIN_ELEMENT_MAX_HEIGHT_PX = 1;
const SMALLER_OUTLINE_OFFSET_WIDGET_TYPES = new Set(['e-form-input']);
function shouldUseSmallerOutlineOffset(element, widgetType) {
  if (element.offsetHeight <= THIN_ELEMENT_MAX_HEIGHT_PX) {
    return true;
  }
  return widgetType !== undefined && SMALLER_OUTLINE_OFFSET_WIDGET_TYPES.has(widgetType);
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/pregenerated-links-removal.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/pregenerated-links-removal.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   removeProviderPregeneratedLinks: function() { return /* binding */ removeProviderPregeneratedLinks; },
/* harmony export */   resetRemovedProviders: function() { return /* binding */ resetRemovedProviders; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const removedProviderKeys = new Set();
function removeProviderPregeneratedLinks(providerKey, removePregeneratedLink) {
  if (removedProviderKeys.has(providerKey)) {
    return;
  }
  const iframeDocument = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.getCanvasIframeDocument)();
  if (!iframeDocument) {
    return;
  }
  const links = iframeDocument.head.querySelectorAll('link[rel="stylesheet"]');
  links.forEach(link => {
    const {
      id,
      href,
      media
    } = link;
    if (removePregeneratedLink({
      id,
      href,
      media
    })) {
      link.remove();
    }
  });
  removedProviderKeys.add(providerKey);
}
function resetRemovedProviders() {
  removedProviderKeys.clear();
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/signalized-process.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/signalized-process.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   signalizedProcess: function() { return /* binding */ signalizedProcess; }
/* harmony export */ });
// eslint-disable-next-line @typescript-eslint/no-explicit-any

function signalizedProcess(signal, steps = []) {
  return {
    then: cb => {
      steps.push(cb);
      return signalizedProcess(signal, steps);
    },
    execute: async () => {
      let lastResult;
      for (const step of steps) {
        if (signal.aborted) {
          break;
        }
        lastResult = await step(lastResult, signal);
      }
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-canvas/src/utils/tracking.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/utils/tracking.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   trackCanvasEvent: function() { return /* binding */ trackCanvasEvent; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);

const trackCanvasEvent = data => {
  (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.trackEvent)(data);
};

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-controls":
/*!*************************************************!*\
  !*** external ["elementorV2","editorControls"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorControls"];

/***/ }),

/***/ "@elementor/editor-documents":
/*!**************************************************!*\
  !*** external ["elementorV2","editorDocuments"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorDocuments"];

/***/ }),

/***/ "@elementor/editor-elements":
/*!*************************************************!*\
  !*** external ["elementorV2","editorElements"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorElements"];

/***/ }),

/***/ "@elementor/editor-interactions":
/*!*****************************************************!*\
  !*** external ["elementorV2","editorInteractions"] ***!
  \*****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorInteractions"];

/***/ }),

/***/ "@elementor/editor-mcp":
/*!********************************************!*\
  !*** external ["elementorV2","editorMcp"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorMcp"];

/***/ }),

/***/ "@elementor/editor-notifications":
/*!******************************************************!*\
  !*** external ["elementorV2","editorNotifications"] ***!
  \******************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorNotifications"];

/***/ }),

/***/ "@elementor/editor-props":
/*!**********************************************!*\
  !*** external ["elementorV2","editorProps"] ***!
  \**********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorProps"];

/***/ }),

/***/ "@elementor/editor-responsive":
/*!***************************************************!*\
  !*** external ["elementorV2","editorResponsive"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorResponsive"];

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

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

/***/ }),

/***/ "@elementor/twing":
/*!****************************************!*\
  !*** external ["elementorV2","twing"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["twing"];

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

/***/ "@elementor/wp-media":
/*!******************************************!*\
  !*** external ["elementorV2","wpMedia"] ***!
  \******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["wpMedia"];

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
/******/ 	/* webpack/runtime/create fake namespace object */
/******/ 	!function() {
/******/ 		var getProto = Object.getPrototypeOf ? function(obj) { return Object.getPrototypeOf(obj); } : function(obj) { return obj.__proto__; };
/******/ 		var leafPrototypes;
/******/ 		// create a fake namespace object
/******/ 		// mode & 1: value is a module id, require it
/******/ 		// mode & 2: merge all properties of value into the ns
/******/ 		// mode & 4: return value when already ns object
/******/ 		// mode & 16: return value when it's Promise-like
/******/ 		// mode & 8|1: behave like require
/******/ 		__webpack_require__.t = function(value, mode) {
/******/ 			if(mode & 1) value = this(value);
/******/ 			if(mode & 8) return value;
/******/ 			if(typeof value === 'object' && value) {
/******/ 				if((mode & 4) && value.__esModule) return value;
/******/ 				if((mode & 16) && typeof value.then === 'function') return value;
/******/ 			}
/******/ 			var ns = Object.create(null);
/******/ 			__webpack_require__.r(ns);
/******/ 			var def = {};
/******/ 			leafPrototypes = leafPrototypes || [null, getProto({}), getProto([]), getProto(getProto)];
/******/ 			for(var current = mode & 2 && value; (typeof current == 'object' || typeof current == 'function') && !~leafPrototypes.indexOf(current); current = getProto(current)) {
/******/ 				Object.getOwnPropertyNames(current).forEach(function(key) { def[key] = function() { return value[key]; }; });
/******/ 			}
/******/ 			def['default'] = function() { return value; };
/******/ 			__webpack_require__.d(ns, def);
/******/ 			return ns;
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
/*!***********************************************************!*\
  !*** ./packages/packages/core/editor-canvas/src/index.ts ***!
  \***********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BREAKPOINTS_SCHEMA_FULL_URI: function() { return /* reexport safe */ _mcp_resources_breakpoints_resource__WEBPACK_IMPORTED_MODULE_0__.BREAKPOINTS_SCHEMA_FULL_URI; },
/* harmony export */   BREAKPOINTS_SCHEMA_URI: function() { return /* reexport safe */ _mcp_resources_breakpoints_resource__WEBPACK_IMPORTED_MODULE_0__.BREAKPOINTS_SCHEMA_URI; },
/* harmony export */   DOCUMENT_STRUCTURE_URI: function() { return /* reexport safe */ _mcp_resources_document_structure_resource__WEBPACK_IMPORTED_MODULE_14__.DOCUMENT_STRUCTURE_URI; },
/* harmony export */   ELEMENT_ADDED_EVENT: function() { return /* reexport safe */ _mcp_tools_build_composition_tool__WEBPACK_IMPORTED_MODULE_24__.ELEMENT_ADDED_EVENT; },
/* harmony export */   GLOBAL_STYLES_IMPORTED_EVENT: function() { return /* reexport safe */ _sync_global_styles_imported_event__WEBPACK_IMPORTED_MODULE_13__.GLOBAL_STYLES_IMPORTED_EVENT; },
/* harmony export */   SpotlightBackdrop: function() { return /* reexport safe */ _components_spotlight_backdrop__WEBPACK_IMPORTED_MODULE_16__.SpotlightBackdrop; },
/* harmony export */   UnknownStyleStateError: function() { return /* reexport safe */ _renderers_errors__WEBPACK_IMPORTED_MODULE_20__.UnknownStyleStateError; },
/* harmony export */   UnknownStyleTypeError: function() { return /* reexport safe */ _renderers_errors__WEBPACK_IMPORTED_MODULE_20__.UnknownStyleTypeError; },
/* harmony export */   WIDGET_SCHEMA_FULL_URI: function() { return /* reexport safe */ _mcp_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_15__.WIDGET_SCHEMA_FULL_URI; },
/* harmony export */   WIDGET_SCHEMA_URI: function() { return /* reexport safe */ _mcp_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_15__.WIDGET_SCHEMA_URI; },
/* harmony export */   canBeNestedTemplated: function() { return /* reexport safe */ _legacy_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_5__.canBeNestedTemplated; },
/* harmony export */   convertCssToAtomic: function() { return /* reexport safe */ _mcp_utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_1__.convertCssToAtomic; },
/* harmony export */   convertStyleBlocksToAtomic: function() { return /* reexport safe */ _mcp_utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_1__.convertStyleBlocksToAtomic; },
/* harmony export */   createNestedTemplatedElementType: function() { return /* reexport safe */ _legacy_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_5__.createNestedTemplatedElementType; },
/* harmony export */   createNestedTemplatedElementView: function() { return /* reexport safe */ _legacy_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_5__.createNestedTemplatedElementView; },
/* harmony export */   createPropsResolver: function() { return /* reexport safe */ _renderers_create_props_resolver__WEBPACK_IMPORTED_MODULE_9__.createPropsResolver; },
/* harmony export */   createTemplatedElementView: function() { return /* reexport safe */ _legacy_create_templated_element_type__WEBPACK_IMPORTED_MODULE_4__.createTemplatedElementView; },
/* harmony export */   createTransformer: function() { return /* reexport safe */ _transformers_create_transformer__WEBPACK_IMPORTED_MODULE_17__.createTransformer; },
/* harmony export */   createTransformersRegistry: function() { return /* reexport safe */ _transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_19__.createTransformersRegistry; },
/* harmony export */   doAfterRender: function() { return /* reexport safe */ _utils_after_render__WEBPACK_IMPORTED_MODULE_23__.doAfterRender; },
/* harmony export */   endDragElementFromPanel: function() { return /* reexport safe */ _sync_drag_element_from_panel__WEBPACK_IMPORTED_MODULE_12__.endDragElementFromPanel; },
/* harmony export */   formatGridTrackRepeat: function() { return /* reexport safe */ _transformers_styles_grid_track_renderer__WEBPACK_IMPORTED_MODULE_18__.formatGridTrackRepeat; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_2__.init; },
/* harmony export */   isAtomicWidget: function() { return /* reexport safe */ _utils_command_utils__WEBPACK_IMPORTED_MODULE_3__.isAtomicWidget; },
/* harmony export */   isGridTrackProperty: function() { return /* reexport safe */ _transformers_styles_grid_track_renderer__WEBPACK_IMPORTED_MODULE_18__.isGridTrackProperty; },
/* harmony export */   registerElementType: function() { return /* reexport safe */ _legacy_init_legacy_views__WEBPACK_IMPORTED_MODULE_6__.registerElementType; },
/* harmony export */   registerModelExtensions: function() { return /* reexport safe */ _legacy_init_legacy_views__WEBPACK_IMPORTED_MODULE_6__.registerModelExtensions; },
/* harmony export */   settingsTransformersRegistry: function() { return /* reexport safe */ _settings_transformers_registry__WEBPACK_IMPORTED_MODULE_10__.settingsTransformersRegistry; },
/* harmony export */   startDragElementFromPanel: function() { return /* reexport safe */ _sync_drag_element_from_panel__WEBPACK_IMPORTED_MODULE_12__.startDragElementFromPanel; },
/* harmony export */   styleTransformersRegistry: function() { return /* reexport safe */ _style_transformers_registry__WEBPACK_IMPORTED_MODULE_11__.styleTransformersRegistry; },
/* harmony export */   stylesInheritanceTransformersRegistry: function() { return /* reexport safe */ _transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_19__.stylesInheritanceTransformersRegistry; },
/* harmony export */   useCanvasDocument: function() { return /* reexport safe */ _hooks_use_canvas_document__WEBPACK_IMPORTED_MODULE_21__.useCanvasDocument; },
/* harmony export */   useEscapeOnCanvas: function() { return /* reexport safe */ _hooks_use_escape_on_canvas__WEBPACK_IMPORTED_MODULE_22__.useEscapeOnCanvas; },
/* harmony export */   waitForChildrenToComplete: function() { return /* reexport safe */ _legacy_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_7__.waitForChildrenToComplete; }
/* harmony export */ });
/* harmony import */ var _mcp_resources_breakpoints_resource__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./mcp/resources/breakpoints-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/breakpoints-resource.ts");
/* harmony import */ var _mcp_utils_convert_css_to_atomic__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./mcp/utils/convert-css-to-atomic */ "./packages/packages/core/editor-canvas/src/mcp/utils/convert-css-to-atomic.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-canvas/src/init.tsx");
/* harmony import */ var _utils_command_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/command-utils */ "./packages/packages/core/editor-canvas/src/utils/command-utils.ts");
/* harmony import */ var _legacy_create_templated_element_type__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./legacy/create-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-templated-element-type.ts");
/* harmony import */ var _legacy_create_nested_templated_element_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./legacy/create-nested-templated-element-type */ "./packages/packages/core/editor-canvas/src/legacy/create-nested-templated-element-type.ts");
/* harmony import */ var _legacy_init_legacy_views__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./legacy/init-legacy-views */ "./packages/packages/core/editor-canvas/src/legacy/init-legacy-views.ts");
/* harmony import */ var _legacy_twig_rendering_utils__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./legacy/twig-rendering-utils */ "./packages/packages/core/editor-canvas/src/legacy/twig-rendering-utils.ts");
/* harmony import */ var _legacy_types__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./legacy/types */ "./packages/packages/core/editor-canvas/src/legacy/types.ts");
/* harmony import */ var _renderers_create_props_resolver__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./renderers/create-props-resolver */ "./packages/packages/core/editor-canvas/src/renderers/create-props-resolver.ts");
/* harmony import */ var _settings_transformers_registry__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./settings-transformers-registry */ "./packages/packages/core/editor-canvas/src/settings-transformers-registry.ts");
/* harmony import */ var _style_transformers_registry__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./style-transformers-registry */ "./packages/packages/core/editor-canvas/src/style-transformers-registry.ts");
/* harmony import */ var _sync_drag_element_from_panel__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./sync/drag-element-from-panel */ "./packages/packages/core/editor-canvas/src/sync/drag-element-from-panel.ts");
/* harmony import */ var _sync_global_styles_imported_event__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./sync/global-styles-imported-event */ "./packages/packages/core/editor-canvas/src/sync/global-styles-imported-event.ts");
/* harmony import */ var _mcp_resources_document_structure_resource__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./mcp/resources/document-structure-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/document-structure-resource.ts");
/* harmony import */ var _mcp_resources_widgets_schema_resource__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./mcp/resources/widgets-schema-resource */ "./packages/packages/core/editor-canvas/src/mcp/resources/widgets-schema-resource.ts");
/* harmony import */ var _components_spotlight_backdrop__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./components/spotlight-backdrop */ "./packages/packages/core/editor-canvas/src/components/spotlight-backdrop.tsx");
/* harmony import */ var _transformers_create_transformer__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./transformers/create-transformer */ "./packages/packages/core/editor-canvas/src/transformers/create-transformer.ts");
/* harmony import */ var _transformers_styles_grid_track_renderer__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./transformers/styles/grid-track-renderer */ "./packages/packages/core/editor-canvas/src/transformers/styles/grid-track-renderer.ts");
/* harmony import */ var _transformers_create_transformers_registry__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./transformers/create-transformers-registry */ "./packages/packages/core/editor-canvas/src/transformers/create-transformers-registry.ts");
/* harmony import */ var _renderers_errors__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./renderers/errors */ "./packages/packages/core/editor-canvas/src/renderers/errors.ts");
/* harmony import */ var _hooks_use_canvas_document__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./hooks/use-canvas-document */ "./packages/packages/core/editor-canvas/src/hooks/use-canvas-document.ts");
/* harmony import */ var _hooks_use_escape_on_canvas__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./hooks/use-escape-on-canvas */ "./packages/packages/core/editor-canvas/src/hooks/use-escape-on-canvas.ts");
/* harmony import */ var _utils_after_render__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./utils/after-render */ "./packages/packages/core/editor-canvas/src/utils/after-render.ts");
/* harmony import */ var _mcp_tools_build_composition_tool__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./mcp/tools/build-composition/tool */ "./packages/packages/core/editor-canvas/src/mcp/tools/build-composition/tool.ts");


























}();
(window.elementorV2 = window.elementorV2 || {}).editorCanvas = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorCanvas?.init?.();
//# sourceMappingURL=editor-canvas.js.map
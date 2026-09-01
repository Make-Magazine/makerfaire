/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-interactions/src/commands/get-clipboard-elements.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/commands/get-clipboard-elements.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getClipboardElements: function() { return /* binding */ getClipboardElements; }
/* harmony export */ });
function getClipboardElements(storageKey = 'clipboard') {
  try {
    const storedData = JSON.parse(localStorage.getItem('elementor') ?? '{}');
    return storedData[storageKey]?.elements;
  } catch {
    return undefined;
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/commands/paste-interactions.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/commands/paste-interactions.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initPasteInteractionsCommand: function() { return /* binding */ initPasteInteractionsCommand; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_is_supported_interaction_item__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/is-supported-interaction-item */ "./packages/packages/core/editor-interactions/src/utils/is-supported-interaction-item.ts");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/temp-id-utils */ "./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts");
/* harmony import */ var _get_clipboard_elements__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./get-clipboard-elements */ "./packages/packages/core/editor-interactions/src/commands/get-clipboard-elements.ts");







function isAtomicContainer(container) {
  const type = container?.model.get('widgetType') || container?.model.get('elType');
  const widgetsCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)();
  const elementConfig = widgetsCache?.[type];
  return Boolean(elementConfig?.atomic_props_schema);
}
function getTitleForContainers(containers) {
  return containers.length > 1 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Elements', 'elementor') : (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementLabel)(containers[0].id);
}
function normalizeClipboardInteractions(raw) {
  if (!raw) {
    return null;
  }
  const parsed = typeof raw === 'string' ? JSON.parse(raw) : raw;
  const payload = {
    version: parsed?.version ?? 1,
    items: parsed?.items?.filter(item => (0,_utils_is_supported_interaction_item__WEBPACK_IMPORTED_MODULE_3__.isSupportedInteractionItem)(item)) ?? []
  };
  if (!payload.items.length) {
    return null;
  }
  return payload;
}
function regenerateInteractionIds(interactions) {
  const cloned = structuredClone(interactions);
  cloned.items?.forEach(item => {
    if (item.$$type === 'interaction-item' && item.value) {
      item.value.interaction_id = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.createString)((0,_utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_5__.generateTempInteractionId)());
    }
  });
  return cloned;
}
function initPasteInteractionsCommand() {
  const undoablePasteInteractions = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.undoable)({
    do: ({
      containers,
      newInteractions
    }) => {
      const pasted = regenerateInteractionIds(newInteractions);
      return containers.map(container => {
        const elementId = container.id;
        const previous = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementInteractions)(elementId);
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementInteractions)({
          elementId,
          interactions: pasted
        });
        return {
          elementId,
          previous: previous ?? {
            version: 1,
            items: []
          }
        };
      });
    },
    undo: (_, revertData) => {
      revertData.forEach(({
        elementId,
        previous
      }) => {
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementInteractions)({
          elementId,
          interactions: previous.items?.length ? previous : undefined
        });
      });
    }
  }, {
    title: ({
      containers
    }) => getTitleForContainers(containers),
    subtitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Interactions Pasted', 'elementor')
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandStartEvent)('document/elements/paste-interactions'), e => {
    const args = e.args;
    const containers = args.containers ?? (args.container ? [args.container] : []);
    const storageKey = args.storageKey ?? 'clipboard';
    if (!containers.length) {
      return;
    }
    const clipboardElements = (0,_get_clipboard_elements__WEBPACK_IMPORTED_MODULE_6__.getClipboardElements)(storageKey);
    const [clipboardElement] = clipboardElements ?? [];
    if (!clipboardElement) {
      return;
    }
    const newInteractions = normalizeClipboardInteractions(clipboardElement.interactions);
    if (!newInteractions) {
      return;
    }
    const existingContainers = containers.filter(c => (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(c.id));
    const validContainers = existingContainers.filter(isAtomicContainer);
    if (!validContainers.length) {
      return;
    }
    undoablePasteInteractions({
      containers: validContainers,
      newInteractions
    });
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/direction.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/direction.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Direction: function() { return /* binding */ Direction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);






const AXIS = {
  top: 'vertical',
  bottom: 'vertical',
  left: 'horizontal',
  right: 'horizontal'
};
function parseValue(value) {
  return value.split('-').filter(Boolean);
}
function serializeValue(directions) {
  const vertical = directions.find(d => d === 'top' || d === 'bottom');
  const horizontal = directions.find(d => d === 'left' || d === 'right');
  if (vertical && horizontal) {
    return `${vertical}-${horizontal}`;
  }
  return directions[0] ?? '';
}
function toggleDirection(current, clicked) {
  if (current.includes(clicked)) {
    return current.filter(d => d !== clicked);
  }
  const filtered = current.filter(d => AXIS[d] !== AXIS[clicked]);
  return [...filtered, clicked];
}
function Direction({
  value,
  onChange,
  interactionType
}) {
  const isIn = interactionType === 'in';
  const options = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => [{
    dir: 'top',
    label: isIn ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('From top', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('To top', 'elementor'),
    Icon: isIn ? _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownSmallIcon : _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowUpSmallIcon
  }, {
    dir: 'bottom',
    label: isIn ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('From bottom', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('To bottom', 'elementor'),
    Icon: isIn ? _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowUpSmallIcon : _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownSmallIcon
  }, {
    dir: 'left',
    label: isIn ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('From left', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('To left', 'elementor'),
    Icon: isIn ? _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon : _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowLeftIcon
  }, {
    dir: 'right',
    label: isIn ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('From right', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('To right', 'elementor'),
    Icon: isIn ? _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowLeftIcon : _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon
  }], [isIn]);
  const selectedDirections = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => parseValue(value), [value]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.StyledToggleButtonGroup, {
    size: "tiny",
    justify: "end",
    sx: {
      display: 'grid',
      gridTemplateColumns: 'repeat(4, minmax(0, 25%))',
      width: '100%'
    }
  }, options.map(({
    dir,
    label,
    Icon
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    key: dir,
    title: label,
    disableFocusListener: true,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.StyledToggleButton, {
    value: dir,
    selected: selectedDirections.includes(dir),
    "aria-label": label,
    size: "tiny",
    isPlaceholder: false,
    onChange: () => {
      const next = toggleDirection(selectedDirections, dir);
      onChange(serializeValue(next));
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    fontSize: "tiny"
  })))));
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/easing.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/easing.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BASE_EASINGS: function() { return /* binding */ BASE_EASINGS; },
/* harmony export */   EASING_OPTIONS: function() { return /* binding */ EASING_OPTIONS; },
/* harmony export */   Easing: function() { return /* binding */ Easing; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _ui_promotion_select__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../ui/promotion-select */ "./packages/packages/core/editor-interactions/src/ui/promotion-select.tsx");
/* harmony import */ var _interaction_details__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../interaction-details */ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx");




const TRACKING_DATA = {
  target_name: 'interactions_easing',
  location_l2: 'interactions'
};
const EASING_OPTIONS = {
  easeIn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Ease In', 'elementor'),
  easeInOut: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Ease In Out', 'elementor'),
  easeOut: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Ease Out', 'elementor'),
  backIn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Back In', 'elementor'),
  backInOut: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Back In Out', 'elementor'),
  backOut: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Back Out', 'elementor'),
  linear: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Linear', 'elementor')
};
const BASE_EASINGS = ['easeIn'];
function Easing({}) {
  const baseOptions = Object.fromEntries(Object.entries(EASING_OPTIONS).filter(([key]) => BASE_EASINGS.includes(key)));
  const disabledOptions = Object.fromEntries(Object.entries(EASING_OPTIONS).filter(([key]) => !BASE_EASINGS.includes(key)));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_promotion_select__WEBPACK_IMPORTED_MODULE_2__.PromotionSelect, {
    value: _interaction_details__WEBPACK_IMPORTED_MODULE_3__.DEFAULT_VALUES.easing,
    baseOptions: baseOptions,
    disabledOptions: disabledOptions,
    promotionContent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Upgrade to control the smoothness of the interaction.', 'elementor'),
    upgradeUrl: "https://go.elementor.com/go-pro-interactions-easing-modal/",
    trackingData: TRACKING_DATA
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/effect-type.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/effect-type.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EffectType: function() { return /* binding */ EffectType; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function EffectType({
  value,
  onChange
}) {
  const options = [{
    value: 'in',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('In', 'elementor'),
    renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('In', 'elementor'),
    showTooltip: true
  }, {
    value: 'out',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Out', 'elementor'),
    renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Out', 'elementor'),
    showTooltip: true
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: onChange,
    value: value
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/effect.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/effect.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BASE_EFFECTS: function() { return /* binding */ BASE_EFFECTS; },
/* harmony export */   EFFECT_OPTIONS: function() { return /* binding */ EFFECT_OPTIONS; },
/* harmony export */   Effect: function() { return /* binding */ Effect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _ui_promotion_select__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../ui/promotion-select */ "./packages/packages/core/editor-interactions/src/ui/promotion-select.tsx");
/* harmony import */ var _interaction_details__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../interaction-details */ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx");




const TRACKING_DATA = {
  target_name: 'interactions_effect',
  location_l2: 'interactions'
};
const EFFECT_OPTIONS = {
  fade: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Fade', 'elementor'),
  slide: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Slide', 'elementor'),
  scale: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Scale', 'elementor'),
  custom: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Custom', 'elementor')
};
const BASE_EFFECTS = ['fade', 'slide', 'scale'];
function Effect({
  value,
  onChange
}) {
  const baseOptions = Object.fromEntries(Object.entries(EFFECT_OPTIONS).filter(([key]) => BASE_EFFECTS.includes(key)));
  const disabledOptions = Object.fromEntries(Object.entries(EFFECT_OPTIONS).filter(([key]) => !BASE_EFFECTS.includes(key)));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_promotion_select__WEBPACK_IMPORTED_MODULE_2__.PromotionSelect, {
    value: value in baseOptions ? value : _interaction_details__WEBPACK_IMPORTED_MODULE_3__.DEFAULT_VALUES.effect,
    onChange: onChange,
    baseOptions: baseOptions,
    disabledOptions: disabledOptions,
    promotionLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('PRO effects', 'elementor'),
    promotionContent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Upgrade to further customize your animation with opacity, scale, move, rotate and more.', 'elementor'),
    upgradeUrl: "https://go.elementor.com/go-pro-interactions-custom-effect-modal/",
    trackingData: TRACKING_DATA
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/repeat.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/repeat.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   REPEAT_OPTIONS: function() { return /* binding */ REPEAT_OPTIONS; },
/* harmony export */   REPEAT_TOOLTIPS: function() { return /* binding */ REPEAT_TOOLTIPS; },
/* harmony export */   Repeat: function() { return /* binding */ Repeat; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _ui_interactions_promotion_chip__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../ui/interactions-promotion-chip */ "./packages/packages/core/editor-interactions/src/ui/interactions-promotion-chip.tsx");
/* harmony import */ var _ui_promotion_overlay_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../ui/promotion-overlay-layout */ "./packages/packages/core/editor-interactions/src/ui/promotion-overlay-layout.tsx");







const TRACKING_DATA = {
  target_name: 'interactions_repeat',
  location_l2: 'interactions'
};
const REPEAT_OPTIONS = {
  times: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('times', 'elementor'),
  loop: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('loop', 'elementor')
};
const REPEAT_TOOLTIPS = {
  times: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Enable number', 'elementor'),
  loop: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Infinite repeat', 'elementor')
};
function Repeat() {
  const repeatContainerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const options = [{
    value: REPEAT_OPTIONS.times,
    disabled: true,
    label: REPEAT_TOOLTIPS.times,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.Number123Icon, {
      fontSize: size
    }),
    showTooltip: true
  }, {
    value: REPEAT_OPTIONS.loop,
    disabled: true,
    label: REPEAT_TOOLTIPS.loop,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.RepeatIcon, {
      fontSize: size
    }),
    showTooltip: true
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_promotion_overlay_layout__WEBPACK_IMPORTED_MODULE_5__.PromotionOverlayLayout, {
    ref: repeatContainerRef,
    promotionChip: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_interactions_promotion_chip__WEBPACK_IMPORTED_MODULE_4__.InteractionsPromotionChip, {
      content: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade to control how many times the animation repeats.', 'elementor'),
      upgradeUrl: 'https://go.elementor.com/go-pro-interactions-repeat-modal/',
      anchorRef: repeatContainerRef,
      trackingData: TRACKING_DATA
    })
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: () => {},
    value: ''
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/replay.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/replay.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BASE_REPLAY: function() { return /* binding */ BASE_REPLAY; },
/* harmony export */   REPLAY_OPTIONS: function() { return /* binding */ REPLAY_OPTIONS; },
/* harmony export */   Replay: function() { return /* binding */ Replay; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _ui_interactions_promotion_chip__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../ui/interactions-promotion-chip */ "./packages/packages/core/editor-interactions/src/ui/interactions-promotion-chip.tsx");
/* harmony import */ var _ui_promotion_overlay_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../ui/promotion-overlay-layout */ "./packages/packages/core/editor-interactions/src/ui/promotion-overlay-layout.tsx");







const TRACKING_DATA = {
  target_name: 'interactions_replay',
  location_l2: 'interactions'
};
const REPLAY_OPTIONS = {
  no: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No', 'elementor'),
  yes: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Yes', 'elementor')
};
const BASE_REPLAY = ['no'];
function Replay({
  onChange
}) {
  const replayContainerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const options = [{
    value: false,
    disabled: false,
    label: REPLAY_OPTIONS.no,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MinusIcon, {
      fontSize: size
    }),
    showTooltip: true
  }, {
    value: true,
    disabled: true,
    label: REPLAY_OPTIONS.yes,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.CheckIcon, {
      fontSize: size
    }),
    showTooltip: true
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_promotion_overlay_layout__WEBPACK_IMPORTED_MODULE_5__.PromotionOverlayLayout, {
    ref: replayContainerRef,
    promotionChip: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_interactions_promotion_chip__WEBPACK_IMPORTED_MODULE_4__.InteractionsPromotionChip, {
      content: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade to run the animation every time its trigger occurs.', 'elementor'),
      upgradeUrl: 'https://go.elementor.com/go-pro-interactions-replay-modal/',
      anchorRef: replayContainerRef,
      trackingData: TRACKING_DATA
    })
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: onChange,
    value: false
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/time-frame-indicator.tsx":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/time-frame-indicator.tsx ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TimeFrameIndicator: function() { return /* binding */ TimeFrameIndicator; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _configs_time_constants__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../configs/time-constants */ "./packages/packages/core/editor-interactions/src/configs/time-constants.ts");
/* harmony import */ var _utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/size-transform-utils */ "./packages/packages/core/editor-interactions/src/utils/size-transform-utils.ts");





function TimeFrameIndicator({
  value,
  onChange,
  defaultValue
}) {
  const sizeValue = (0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_3__.parseSizeValue)(value, _configs_time_constants__WEBPACK_IMPORTED_MODULE_2__.TIME_UNITS, defaultValue, _configs_time_constants__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_TIME_UNIT);
  const handleChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(size => {
    onChange((0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_3__.formatSizeValue)(size));
  }, [onChange]);
  const handleBlur = event => {
    if (!event.target.value) {
      handleChange((0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_3__.parseSizeValue)(defaultValue, _configs_time_constants__WEBPACK_IMPORTED_MODULE_2__.TIME_UNITS, undefined, _configs_time_constants__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_TIME_UNIT));
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeComponent, {
    units: _configs_time_constants__WEBPACK_IMPORTED_MODULE_2__.TIME_UNITS,
    value: sizeValue,
    setValue: handleChange,
    onBlur: handleBlur,
    InputProps: {
      inputProps: {
        min: 0
      }
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/controls/trigger.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/controls/trigger.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BASE_TRIGGERS: function() { return /* binding */ BASE_TRIGGERS; },
/* harmony export */   TRIGGER_OPTIONS: function() { return /* binding */ TRIGGER_OPTIONS; },
/* harmony export */   Trigger: function() { return /* binding */ Trigger; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _ui_promotion_select__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../ui/promotion-select */ "./packages/packages/core/editor-interactions/src/ui/promotion-select.tsx");
/* harmony import */ var _interaction_details__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../interaction-details */ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx");




const TRACKING_DATA = {
  target_name: 'interactions_trigger',
  location_l2: 'interactions'
};
const TRIGGER_OPTIONS = {
  load: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Page load', 'elementor'),
  scrollIn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Scroll into view', 'elementor'),
  scrollOn: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('While scrolling', 'elementor'),
  hover: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('On hover', 'elementor'),
  click: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('On click', 'elementor')
};
const BASE_TRIGGERS = ['load', 'scrollIn'];
function Trigger({
  value,
  onChange
}) {
  const baseOptions = Object.fromEntries(Object.entries(TRIGGER_OPTIONS).filter(([key]) => BASE_TRIGGERS.includes(key)));
  const disabledOptions = Object.fromEntries(Object.entries(TRIGGER_OPTIONS).filter(([key]) => !BASE_TRIGGERS.includes(key)));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_promotion_select__WEBPACK_IMPORTED_MODULE_2__.PromotionSelect, {
    value: value in baseOptions ? value : _interaction_details__WEBPACK_IMPORTED_MODULE_3__.DEFAULT_VALUES.trigger,
    onChange: onChange,
    baseOptions: baseOptions,
    disabledOptions: disabledOptions,
    promotionLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('PRO triggers', 'elementor'),
    promotionContent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Upgrade to unlock more interactions triggers.', 'elementor'),
    upgradeUrl: "https://go.elementor.com/go-pro-interactions-triggers-modal/",
    trackingData: TRACKING_DATA
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/empty-state.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/empty-state.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EmptyState: function() { return /* binding */ EmptyState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const EmptyState = ({
  onCreateInteraction
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    justifyContent: "center",
    height: "100%",
    color: "text.secondary",
    sx: {
      p: 2.5,
      pt: 8,
      pb: 5.5
    },
    gap: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.SwipeIcon, {
    fontSize: "large"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Animate elements with Interactions', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    maxWidth: "170px"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Add entrance animations and effects triggered by user interactions such as page load or scroll.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "outlined",
    color: "secondary",
    size: "small",
    sx: {
      mt: 1
    },
    onClick: onCreateInteraction
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create an interaction', 'elementor')));
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/field.tsx":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/field.tsx ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Field: function() { return /* binding */ Field; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);



const Field = ({
  label,
  children
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 12,
    "aria-label": `${label} control`
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverGridContainer, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 6
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 6
  }, children)));
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/interaction-details.tsx ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_VALUES: function() { return /* binding */ DEFAULT_VALUES; },
/* harmony export */   InteractionDetails: function() { return /* binding */ InteractionDetails; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _interactions_controls_registry__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../interactions-controls-registry */ "./packages/packages/core/editor-interactions/src/interactions-controls-registry.ts");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _utils_resolve_direction__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/resolve-direction */ "./packages/packages/core/editor-interactions/src/utils/resolve-direction.ts");
/* harmony import */ var _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../utils/scroll-interaction-event */ "./packages/packages/core/editor-interactions/src/utils/scroll-interaction-event.ts");
/* harmony import */ var _utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../utils/size-transform-utils */ "./packages/packages/core/editor-interactions/src/utils/size-transform-utils.ts");
/* harmony import */ var _controls_time_frame_indicator__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./controls/time-frame-indicator */ "./packages/packages/core/editor-interactions/src/components/controls/time-frame-indicator.tsx");
/* harmony import */ var _field__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./field */ "./packages/packages/core/editor-interactions/src/components/field.tsx");












const DEFAULT_VALUES = {
  trigger: 'load',
  effect: 'fade',
  type: 'in',
  direction: '',
  duration: 600,
  delay: 0,
  replay: false,
  easing: 'easeIn',
  relativeTo: 'viewport',
  repeat: '',
  times: 1,
  start: 85,
  end: 15
};
const TRIGGERS_WITHOUT_REPLAY = ['load', 'scrollOn', 'hover', 'click'];
const controlVisibilityConfig = {
  replay: values => !TRIGGERS_WITHOUT_REPLAY.includes(values.trigger),
  custom: values => values.effect === 'custom',
  effectType: values => values.effect !== 'custom',
  direction: values => values.effect !== 'custom',
  relativeTo: values => values.trigger === 'scrollOn',
  start: values => values.trigger === 'scrollOn',
  end: values => values.trigger === 'scrollOn',
  repeat: values => values.trigger !== 'scrollOn',
  times: values => values.trigger !== 'scrollOn' && values.repeat === 'times',
  duration: values => {
    const isRelativeToVisible = values.trigger === 'scrollOn';
    return !isRelativeToVisible;
  },
  delay: values => {
    const isRelativeToVisible = values.trigger === 'scrollOn';
    return !isRelativeToVisible;
  }
};
function normalizeTimesValue(value, fallback) {
  const numericValue = Number(value);
  if (!Number.isFinite(numericValue)) {
    return fallback;
  }
  return Math.max(1, Math.floor(numericValue));
}
function useControlComponent(controlName, isVisible = true) {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!isVisible) {
      return null;
    }
    return (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_4__.getInteractionsControl)(controlName)?.component ?? null;
  }, [controlName, isVisible]);
}
const InteractionDetails = ({
  interaction,
  onChange,
  onPlayInteraction
}) => {
  const trigger = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(interaction.trigger, DEFAULT_VALUES.trigger);
  const effect = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(interaction.animation.value.effect, DEFAULT_VALUES.effect);
  const customEffects = interaction.animation.value.custom_effect;
  const type = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(interaction.animation.value.type, DEFAULT_VALUES.type);
  const direction = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(interaction.animation.value.direction, DEFAULT_VALUES.direction);
  const duration = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractSize)(interaction.animation.value.timing_config.value.duration);
  const delay = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractSize)(interaction.animation.value.timing_config.value.delay);
  const replay = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractBoolean)(interaction.animation.value.config?.value.replay, DEFAULT_VALUES.replay);
  const easing = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(interaction.animation.value.config?.value.easing, DEFAULT_VALUES.easing);
  const relativeTo = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(interaction.animation.value.config?.value.relativeTo, DEFAULT_VALUES.relativeTo);
  const configValue = interaction.animation.value.config?.value;
  const repeat = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(configValue?.repeat, DEFAULT_VALUES.repeat);
  const times = normalizeTimesValue(configValue?.times?.value, DEFAULT_VALUES.times);
  const start = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractSize)(interaction.animation.value.config?.value.start, DEFAULT_VALUES.start);
  const end = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractSize)(interaction.animation.value.config?.value.end, DEFAULT_VALUES.end);
  const interactionValues = {
    trigger,
    effect,
    type,
    direction,
    duration,
    delay,
    easing,
    replay,
    relativeTo,
    repeat,
    times,
    start,
    end,
    customEffects
  };
  const TriggerControl = useControlComponent('trigger', true);
  const EffectControl = useControlComponent('effect');
  const ReplayControl = useControlComponent('replay', controlVisibilityConfig.replay(interactionValues));
  const RelativeToControl = useControlComponent('relativeTo', controlVisibilityConfig.relativeTo(interactionValues));
  const StartControl = useControlComponent('start', controlVisibilityConfig.start(interactionValues));
  const EndControl = useControlComponent('end', controlVisibilityConfig.end(interactionValues));
  const CustomEffectControl = useControlComponent('customEffects', controlVisibilityConfig.custom(interactionValues));
  const EffectTypeControl = useControlComponent('effectType', controlVisibilityConfig.effectType(interactionValues));
  const DirectionControl = useControlComponent('direction', controlVisibilityConfig.direction(interactionValues));
  const RepeatControl = useControlComponent('repeat', controlVisibilityConfig.repeat(interactionValues));
  const TimesControl = useControlComponent('times', controlVisibilityConfig.times(interactionValues));
  const EasingControl = useControlComponent('easing');
  const updateInteraction = updates => {
    const resolvedDirectionValue = (0,_utils_resolve_direction__WEBPACK_IMPORTED_MODULE_6__.resolveDirection)('direction' in updates, updates.effect, updates.direction, direction, effect);
    const updatedInteraction = {
      ...interaction,
      interaction_id: interaction.interaction_id,
      trigger: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.createString)(updates.trigger ?? trigger),
      animation: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.createAnimationPreset)({
        effect: updates.effect ?? effect,
        type: updates.type ?? type,
        direction: resolvedDirectionValue,
        duration: updates.duration ?? duration,
        delay: updates.delay ?? delay,
        replay: updates.replay ?? replay,
        easing: updates.easing ?? easing,
        relativeTo: updates.relativeTo ?? relativeTo,
        repeat: updates.repeat ?? repeat,
        times: updates.times ?? times,
        start: updates.start ?? start,
        end: updates.end ?? end,
        customEffects: updates.customEffects ?? customEffects
      })
    };
    onChange(updatedInteraction);
    (0,_utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_7__.syncGridOverlay)(updates.trigger ?? trigger, updates.start ?? start, updates.end ?? end, updates.relativeTo ?? relativeTo);
    const interactionId = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractString)(updatedInteraction.interaction_id);
    setTimeout(() => {
      onPlayInteraction(interactionId);
    }, 0);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverContent, {
    p: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    spacing: 1.5
  }, TriggerControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Trigger', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TriggerControl, {
    value: trigger,
    onChange: v => updateInteraction({
      trigger: v
    })
  })), ReplayControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Replay', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ReplayControl, {
    value: replay,
    onChange: v => updateInteraction({
      replay: v
    }),
    disabled: true
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    spacing: 1.5
  }, EffectControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Effect', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EffectControl, {
    value: effect,
    onChange: v => updateInteraction({
      effect: v
    })
  })), CustomEffectControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Custom Effect', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CustomEffectControl, {
    value: customEffects,
    onChange: v => updateInteraction({
      customEffects: v
    })
  })), EffectTypeControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Type', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EffectTypeControl, {
    value: type,
    onChange: v => updateInteraction({
      type: v
    })
  })), DirectionControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Direction', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DirectionControl, {
    value: direction,
    onChange: v => updateInteraction({
      direction: v
    }),
    interactionType: type
  })), RepeatControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Repeat', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RepeatControl, {
    value: repeat,
    onChange: v => updateInteraction({
      repeat: v
    })
  })), TimesControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Times', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TimesControl, {
    value: times,
    onChange: v => updateInteraction({
      times: normalizeTimesValue(v, DEFAULT_VALUES.times)
    })
  })), controlVisibilityConfig.duration(interactionValues) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Duration', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_time_frame_indicator__WEBPACK_IMPORTED_MODULE_9__.TimeFrameIndicator, {
    value: String(duration),
    onChange: v => updateInteraction({
      duration: v
    }),
    defaultValue: DEFAULT_VALUES.duration
  })), controlVisibilityConfig.delay(interactionValues) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Delay', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_time_frame_indicator__WEBPACK_IMPORTED_MODULE_9__.TimeFrameIndicator, {
    value: String(delay),
    onChange: v => updateInteraction({
      delay: v
    }),
    defaultValue: DEFAULT_VALUES.delay
  }))), controlVisibilityConfig.relativeTo(interactionValues) && RelativeToControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    spacing: 1.5
  }, StartControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Start', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartControl, {
    value: (0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_8__.parseSizeValue)(start, ['%']).size?.toString() ?? '',
    onChange: v => updateInteraction({
      start: v
    })
  })), EndControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('End', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EndControl, {
    value: (0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_8__.parseSizeValue)(end, ['%']).size?.toString() ?? '',
    onChange: v => updateInteraction({
      end: v
    })
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Relative To', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RelativeToControl, {
    value: relativeTo,
    onChange: v => updateInteraction({
      relativeTo: v
    })
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, null)), EasingControl && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    spacing: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field__WEBPACK_IMPORTED_MODULE_10__.Field, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Easing', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EasingControl, {
    value: easing,
    onChange: v => {
      updateInteraction({
        easing: v
      });
    }
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/interaction-settings.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/interaction-settings.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionSettings: function() { return /* binding */ InteractionSettings; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }







const SIZE = 'tiny';
const InteractionSettings = ({
  interaction,
  onChange
}) => {
  const breakpoints = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2__.useBreakpoints)();
  const availableBreakpoints = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => breakpoints.map(breakpoint => ({
    label: breakpoint.label,
    value: String(breakpoint.id)
  })), [breakpoints]);
  const [selectedBreakpoints, setSelectedBreakpoints] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    const excluded = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.extractExcludedBreakpoints)(interaction.breakpoints).filter(excludedBreakpoint => {
      return availableBreakpoints.some(({
        value
      }) => value === excludedBreakpoint);
    });
    return availableBreakpoints.filter(({
      value
    }) => {
      return !excluded.includes(value);
    });
  });
  const handleBreakpointChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((_, newValue) => {
    setSelectedBreakpoints(newValue);
    const selectedValues = newValue.map(option => option.value);
    const newExcluded = availableBreakpoints.filter(breakpoint => !selectedValues.includes(breakpoint.value)).map(breakpoint => breakpoint.value);
    const updatedInteraction = {
      ...interaction,
      ...(newExcluded.length > 0 && {
        breakpoints: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_5__.createInteractionBreakpoints)(newExcluded)
      })
    };
    if (newExcluded.length === 0) {
      delete updatedInteraction.breakpoints;
    }
    onChange(updatedInteraction);
  }, [interaction, availableBreakpoints, onChange]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverContent, {
    p: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    container: true,
    spacing: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    item: true,
    xs: 12
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "column",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, {
    sx: {
      width: '100%'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Trigger on', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Autocomplete, {
    fullWidth: true,
    multiple: true,
    value: selectedBreakpoints,
    onChange: handleBreakpointChange,
    size: SIZE,
    options: availableBreakpoints,
    isOptionEqualToValue: (option, value) => option.value === value.value,
    renderInput: params => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.TextField, params),
    renderTags: (values, getTagProps) => values.map((option, index) => {
      const {
        key,
        ...chipProps
      } = getTagProps({
        index
      });
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Chip, _extends({
        key: key,
        size: SIZE,
        label: option.label
      }, chipProps));
    })
  })))));
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/interactions-list-item.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/interactions-list-item.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsListItem: function() { return /* binding */ InteractionsListItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_interactions_item_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../contexts/interactions-item-context */ "./packages/packages/core/editor-interactions/src/contexts/interactions-item-context.tsx");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _interaction_details__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./interaction-details */ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx");
/* harmony import */ var _interaction_settings__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./interaction-settings */ "./packages/packages/core/editor-interactions/src/components/interaction-settings.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }








const InteractionsListItem = ({
  index,
  value: interaction
}) => {
  const {
    getTabsProps,
    getTabProps,
    getTabPanelProps
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTabs)('details');
  const context = (0,_contexts_interactions_item_context__WEBPACK_IMPORTED_MODULE_3__.useInteractionItemContext)();
  const handleChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newInteractionValue => {
    context?.onInteractionChange(index, newInteractionValue);
  }, [context, index]);
  const handlePlayInteraction = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(interactionId => {
    context?.onPlayInteraction(interactionId);
  }, [context]);
  const interactionId = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(interaction.value.interaction_id);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tabs, _extends({
    size: "small",
    variant: "fullWidth",
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Interaction', 'elementor')
  }, getTabsProps()), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Details', 'elementor')
  }, getTabProps('details'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Settings', 'elementor')
  }, getTabProps('settings')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TabPanel, _extends({
    sx: {
      p: 0
    }
  }, getTabPanelProps('details')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_interaction_details__WEBPACK_IMPORTED_MODULE_5__.InteractionDetails, {
    key: interactionId,
    interaction: interaction.value,
    onChange: handleChange,
    onPlayInteraction: handlePlayInteraction
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TabPanel, _extends({
    sx: {
      p: 0
    }
  }, getTabPanelProps('settings')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_interaction_settings__WEBPACK_IMPORTED_MODULE_6__.InteractionSettings, {
    key: interactionId,
    interaction: interaction.value,
    onChange: handleChange
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/interactions-list.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/interactions-list.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsList: function() { return /* binding */ InteractionsList; },
/* harmony export */   MAX_NUMBER_OF_INTERACTIONS: function() { return /* binding */ MAX_NUMBER_OF_INTERACTIONS; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _contexts_interactions_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../contexts/interactions-context */ "./packages/packages/core/editor-interactions/src/contexts/interactions-context.tsx");
/* harmony import */ var _contexts_interactions_item_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../contexts/interactions-item-context */ "./packages/packages/core/editor-interactions/src/contexts/interactions-item-context.tsx");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../utils/scroll-interaction-event */ "./packages/packages/core/editor-interactions/src/utils/scroll-interaction-event.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils/tracking */ "./packages/packages/core/editor-interactions/src/utils/tracking.ts");
/* harmony import */ var _interaction_details__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./interaction-details */ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx");
/* harmony import */ var _interactions_list_item__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./interactions-list-item */ "./packages/packages/core/editor-interactions/src/components/interactions-list-item.tsx");













const MAX_NUMBER_OF_INTERACTIONS = 5;
function InteractionsList(props) {
  const {
    interactions,
    onSelectInteractions,
    onPlayInteraction,
    triggerCreateOnShowEmpty
  } = props;
  const {
    elementId
  } = (0,_contexts_interactions_context__WEBPACK_IMPORTED_MODULE_5__.useInteractionsContext)();
  const hasInitializedRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  const newlyCreatedIdsRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(new Set());
  const handleUpdateInteractions = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newInteractions => {
    onSelectInteractions(newInteractions);
  }, [onSelectInteractions]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (triggerCreateOnShowEmpty && !hasInitializedRef.current && (!interactions.items || interactions.items?.length === 0)) {
      hasInitializedRef.current = true;
      const newItem = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.createDefaultInteractionItem)();
      newlyCreatedIdsRef.current.add((0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.extractString)(newItem.value.interaction_id));
      const newState = {
        version: 1,
        items: [newItem]
      };
      handleUpdateInteractions(newState);
    }
  }, [triggerCreateOnShowEmpty, interactions.items, handleUpdateInteractions]);
  const isMaxNumberOfInteractionsReached = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return interactions.items?.length >= MAX_NUMBER_OF_INTERACTIONS;
  }, [interactions.items?.length]);
  const infotipContent = isMaxNumberOfInteractionsReached ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Alert, {
    color: "secondary",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.InfoCircleFilledIcon, null),
    size: "small"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.AlertTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Interactions', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    component: "span"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)("You've reached the limit of 5 interactions for this element. Please remove an interaction before creating a new one.", 'elementor'))) : undefined;
  const handleRepeaterChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((newItems, _, meta) => {
    handleUpdateInteractions({
      ...interactions,
      items: newItems
    });
    if (meta?.action?.type === 'add') {
      const addedItem = meta.action.payload[0]?.item;
      if (addedItem) {
        newlyCreatedIdsRef.current.add((0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.extractString)(addedItem.value.interaction_id));
      }
    }
  }, [interactions, handleUpdateInteractions]);
  const handleInteractionChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((index, newInteractionValue) => {
    const newItems = structuredClone(interactions.items);
    newItems[index] = {
      $$type: 'interaction-item',
      value: newInteractionValue
    };
    handleUpdateInteractions({
      ...interactions,
      items: newItems
    });
  }, [interactions, handleUpdateInteractions]);
  const contextValue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => ({
    onInteractionChange: handleInteractionChange,
    onPlayInteraction
  }), [handleInteractionChange, onPlayInteraction]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_interactions_item_context__WEBPACK_IMPORTED_MODULE_6__.InteractionItemContextProvider, {
    value: contextValue
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.Repeater, {
    openOnAdd: true,
    openItem: triggerCreateOnShowEmpty ? 0 : undefined,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Interactions', 'elementor'),
    values: interactions.items,
    setValues: handleRepeaterChange,
    showDuplicate: false,
    showToggle: false,
    isSortable: false,
    disableAddItemButton: isMaxNumberOfInteractionsReached,
    addButtonInfotipContent: infotipContent,
    itemSettings: {
      initialValues: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.createDefaultInteractionItem)(),
      Label: ({
        value
      }) => (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.buildDisplayLabel)(value.value),
      Icon: () => null,
      Content: _interactions_list_item__WEBPACK_IMPORTED_MODULE_11__.InteractionsListItem,
      onPopoverOpen: value => {
        const {
          trigger,
          start,
          end,
          relativeTo
        } = (0,_utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_8__.extractScrollOverlayParams)(value.value, _interaction_details__WEBPACK_IMPORTED_MODULE_10__.DEFAULT_VALUES);
        (0,_utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_8__.syncGridOverlay)(trigger, start, end, relativeTo);
      },
      onPopoverClose: value => {
        (0,_utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_8__.dispatchScrollInteraction)(null);
        const id = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.extractString)(value.value.interaction_id);
        if (newlyCreatedIdsRef.current.has(id)) {
          newlyCreatedIdsRef.current.delete(id);
          (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_9__.trackInteractionCreated)(elementId, value);
        }
      },
      actions: value => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
        key: "preview",
        placement: "top",
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Preview', 'elementor')
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.IconButton, {
        "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Play interaction', 'elementor'),
        size: "tiny",
        onClick: () => onPlayInteraction((0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_7__.extractString)(value.value.interaction_id))
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.PlayerPlayIcon, {
        fontSize: "tiny"
      })))
    }
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/components/interactions-tab.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/components/interactions-tab.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsTab: function() { return /* binding */ InteractionsTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_interactions_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../contexts/interactions-context */ "./packages/packages/core/editor-interactions/src/contexts/interactions-context.tsx");
/* harmony import */ var _contexts_popup_state_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../contexts/popup-state-context */ "./packages/packages/core/editor-interactions/src/contexts/popup-state-context.tsx");
/* harmony import */ var _hooks_use_element_interactions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-element-interactions */ "./packages/packages/core/editor-interactions/src/hooks/use-element-interactions.ts");
/* harmony import */ var _empty_state__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./empty-state */ "./packages/packages/core/editor-interactions/src/components/empty-state.tsx");
/* harmony import */ var _interactions_list__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./interactions-list */ "./packages/packages/core/editor-interactions/src/components/interactions-list.tsx");









const InteractionsTab = ({
  elementId
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_popup_state_context__WEBPACK_IMPORTED_MODULE_4__.PopupStateProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InteractionsTabContent, {
    elementId: elementId
  }));
};
function InteractionsTabContent({
  elementId
}) {
  const existingInteractions = (0,_hooks_use_element_interactions__WEBPACK_IMPORTED_MODULE_5__.useElementInteractions)(elementId);
  const firstInteractionState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const hasInteractions = existingInteractions?.items?.length || firstInteractionState[0];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_session__WEBPACK_IMPORTED_MODULE_1__.SessionStorageProvider, {
    prefix: elementId
  }, hasInteractions ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_interactions_context__WEBPACK_IMPORTED_MODULE_3__.InteractionsProvider, {
    elementId: elementId
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InteractionsContent, {
    firstInteractionState: firstInteractionState
  })) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_empty_state__WEBPACK_IMPORTED_MODULE_6__.EmptyState, {
    onCreateInteraction: () => {
      firstInteractionState[1](true);
    }
  }));
}
function InteractionsContent({
  firstInteractionState
}) {
  const {
    interactions,
    setInteractions,
    playInteractions
  } = (0,_contexts_interactions_context__WEBPACK_IMPORTED_MODULE_3__.useInteractionsContext)();
  const applyInteraction = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newInteractions => {
    firstInteractionState[1](false);
    if (!newInteractions) {
      setInteractions(undefined);
      return;
    }
    setInteractions(newInteractions);
  }, [setInteractions, firstInteractionState]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    sx: {
      m: 1,
      p: 1.5
    },
    gap: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_interactions_list__WEBPACK_IMPORTED_MODULE_7__.InteractionsList, {
    triggerCreateOnShowEmpty: firstInteractionState[0],
    interactions: interactions,
    onSelectInteractions: applyInteraction,
    onPlayInteraction: playInteractions
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/configs/time-constants.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/configs/time-constants.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_TIME_UNIT: function() { return /* binding */ DEFAULT_TIME_UNIT; },
/* harmony export */   TIME_UNITS: function() { return /* binding */ TIME_UNITS; }
/* harmony export */ });
const TIME_UNITS = ['s', 'ms'];
const DEFAULT_TIME_UNIT = 'ms';

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/contexts/interactions-context.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/contexts/interactions-context.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsProvider: function() { return /* binding */ InteractionsProvider; },
/* harmony export */   useInteractionsContext: function() { return /* binding */ useInteractionsContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_element_interactions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-element-interactions */ "./packages/packages/core/editor-interactions/src/hooks/use-element-interactions.ts");






const InteractionsContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const DEFAULT_INTERACTIONS = {
  version: 1,
  items: []
};
const InteractionsProvider = ({
  children,
  elementId
}) => {
  const rawInteractions = (0,_hooks_use_element_interactions__WEBPACK_IMPORTED_MODULE_4__.useElementInteractions)(elementId);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    window.dispatchEvent(new CustomEvent('elementor/element/update_interactions'));
  }, []);
  const interactions = rawInteractions ?? DEFAULT_INTERACTIONS;
  const undoableSetInteractions = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.undoable)({
    do: ({
      interactions: newInteractions
    }) => {
      const previous = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementInteractions)(elementId);
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.updateElementInteractions)({
        elementId,
        interactions: newInteractions
      });
      return previous;
    },
    undo: (_, previous) => {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.updateElementInteractions)({
        elementId,
        interactions: previous?.items?.length ? previous : undefined
      });
    }
  }, {
    title: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementLabel)(elementId),
    subtitle: ({
      operationType
    }) => operationType === 'apply' ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Interaction Applied', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Interaction Deleted', 'elementor')
  }), [elementId]);
  const setInteractions = value => {
    const normalizedValue = value && value.items?.length === 0 ? undefined : value;
    const prevItemCount = interactions.items?.length ?? 0;
    const newItemCount = normalizedValue?.items?.length ?? 0;
    if (newItemCount > prevItemCount) {
      undoableSetInteractions({
        interactions: normalizedValue,
        operationType: 'apply'
      });
    } else if (newItemCount < prevItemCount) {
      undoableSetInteractions({
        interactions: normalizedValue,
        operationType: 'delete'
      });
    } else {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.updateElementInteractions)({
        elementId,
        interactions: normalizedValue
      });
    }
  };
  const playInteractions = interactionId => {
    (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.playElementInteractions)(elementId, interactionId);
  };
  const contextValue = {
    elementId,
    interactions,
    setInteractions,
    playInteractions
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InteractionsContext.Provider, {
    value: contextValue
  }, children);
};
const useInteractionsContext = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(InteractionsContext);
  if (!context) {
    throw new Error('useInteractionsContext must be used within InteractionsProvider');
  }
  return context;
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/contexts/interactions-item-context.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/contexts/interactions-item-context.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionItemContextProvider: function() { return /* binding */ InteractionItemContextProvider; },
/* harmony export */   useInteractionItemContext: function() { return /* binding */ useInteractionItemContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const InteractionItemContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function InteractionItemContextProvider({
  value,
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InteractionItemContext.Provider, {
    value: value
  }, children);
}
function useInteractionItemContext() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(InteractionItemContext);
  if (!context) {
    throw new Error('useInteractionItemContext must be used within InteractionItemContextProvider');
  }
  return context;
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/contexts/popup-state-context.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/contexts/popup-state-context.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopupStateProvider: function() { return /* binding */ PopupStateProvider; },
/* harmony export */   usePopupStateContext: function() { return /* binding */ usePopupStateContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const PopupStateContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);
const PopupStateProvider = ({
  children
}) => {
  const [openByDefault, setOpenByDefault] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const triggerDefaultOpen = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setOpenByDefault(true);
  }, []);
  const resetDefaultOpen = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setOpenByDefault(false);
  }, []);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PopupStateContext.Provider, {
    value: {
      openByDefault,
      triggerDefaultOpen,
      resetDefaultOpen
    }
  }, children);
};
const usePopupStateContext = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(PopupStateContext);
  if (!context) {
    throw new Error('usePopupStateContext must be used within PopupStateProvider');
  }
  return context;
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/hooks/on-duplicate.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/hooks/on-duplicate.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initCleanInteractionIdsOnDuplicate: function() { return /* binding */ initCleanInteractionIdsOnDuplicate; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/temp-id-utils */ "./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts");




function initCleanInteractionIdsOnDuplicate() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.registerDataHook)('after', 'document/elements/duplicate', (_args, result) => {
    if (!result || typeof result === 'boolean' && result === false) {
      return;
    }
    const containers = Array.isArray(result) ? result : [result];
    containers.forEach(container => {
      cleanInteractionIdsRecursive(container.id);
    });
    window.dispatchEvent(new CustomEvent('elementor/element/update_interactions'));
  });
}
function cleanInteractionIdsRecursive(elementId) {
  const container = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  if (!container) {
    return;
  }
  (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getAllDescendants)(container).forEach(element => {
    cleanInteractionIds(element.id);
  });
}
function cleanInteractionIds(elementId) {
  const container = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  if (!container) {
    return;
  }
  const interactions = container.model.get('interactions');
  if (!interactions || !interactions.items) {
    return;
  }
  const updatedInteractions = structuredClone(interactions);
  updatedInteractions?.items?.forEach(interaction => {
    if (interaction.$$type === 'interaction-item' && interaction.value) {
      interaction.value.interaction_id = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_2__.createString)((0,_utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_3__.generateTempInteractionId)());
    }
  });
  container.model.set('interactions', updatedInteractions);
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/hooks/use-element-interactions.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/hooks/use-element-interactions.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useElementInteractions: function() { return /* binding */ useElementInteractions; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_filter_interactions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/filter-interactions */ "./packages/packages/core/editor-interactions/src/utils/filter-interactions.ts");




const useElementInteractions = elementId => {
  const [interactions, setInteractions] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    const initial = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementInteractions)(elementId);
    const filteredInteractions = (0,_utils_filter_interactions__WEBPACK_IMPORTED_MODULE_3__.filterInteractions)(initial?.items ?? []);
    return {
      version: initial?.version ?? 1,
      items: filteredInteractions
    };
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.windowEvent)('elementor/element/update_interactions'), () => {
    const newInteractions = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementInteractions)(elementId);
    const filteredInteractions = (0,_utils_filter_interactions__WEBPACK_IMPORTED_MODULE_3__.filterInteractions)(newInteractions?.items ?? []);
    setInteractions({
      version: newInteractions?.version ?? 1,
      items: filteredInteractions
    });
  }, [elementId]);
  return interactions;
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/init.ts":
/*!****************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/init.ts ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _commands_paste_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./commands/paste-interactions */ "./packages/packages/core/editor-interactions/src/commands/paste-interactions.ts");
/* harmony import */ var _components_controls_direction__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/controls/direction */ "./packages/packages/core/editor-interactions/src/components/controls/direction.tsx");
/* harmony import */ var _components_controls_easing__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/controls/easing */ "./packages/packages/core/editor-interactions/src/components/controls/easing.tsx");
/* harmony import */ var _components_controls_effect__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/controls/effect */ "./packages/packages/core/editor-interactions/src/components/controls/effect.tsx");
/* harmony import */ var _components_controls_effect_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/controls/effect-type */ "./packages/packages/core/editor-interactions/src/components/controls/effect-type.tsx");
/* harmony import */ var _components_controls_repeat__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/controls/repeat */ "./packages/packages/core/editor-interactions/src/components/controls/repeat.tsx");
/* harmony import */ var _components_controls_replay__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/controls/replay */ "./packages/packages/core/editor-interactions/src/components/controls/replay.tsx");
/* harmony import */ var _components_controls_trigger__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/controls/trigger */ "./packages/packages/core/editor-interactions/src/components/controls/trigger.tsx");
/* harmony import */ var _hooks_on_duplicate__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./hooks/on-duplicate */ "./packages/packages/core/editor-interactions/src/hooks/on-duplicate.ts");
/* harmony import */ var _interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./interactions-controls-registry */ "./packages/packages/core/editor-interactions/src/interactions-controls-registry.ts");
/* harmony import */ var _interactions_repository__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./interactions-repository */ "./packages/packages/core/editor-interactions/src/interactions-repository.ts");
/* harmony import */ var _mcp__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./mcp */ "./packages/packages/core/editor-interactions/src/mcp/index.ts");
/* harmony import */ var _providers_document_elements_interactions_provider__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./providers/document-elements-interactions-provider */ "./packages/packages/core/editor-interactions/src/providers/document-elements-interactions-provider.ts");














function init() {
  try {
    _interactions_repository__WEBPACK_IMPORTED_MODULE_11__.interactionsRepository.register(_providers_document_elements_interactions_provider__WEBPACK_IMPORTED_MODULE_13__.documentElementsInteractionsProvider);
    (0,_hooks_on_duplicate__WEBPACK_IMPORTED_MODULE_9__.initCleanInteractionIdsOnDuplicate)();
    (0,_commands_paste_interactions__WEBPACK_IMPORTED_MODULE_1__.initPasteInteractionsCommand)();
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'trigger',
      component: _components_controls_trigger__WEBPACK_IMPORTED_MODULE_8__.Trigger,
      options: ['load', 'scrollIn']
    });
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'easing',
      component: _components_controls_easing__WEBPACK_IMPORTED_MODULE_3__.Easing,
      options: ['easeIn']
    });
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'replay',
      component: _components_controls_replay__WEBPACK_IMPORTED_MODULE_7__.Replay,
      options: ['no']
    });
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'effectType',
      component: _components_controls_effect_type__WEBPACK_IMPORTED_MODULE_5__.EffectType,
      options: ['in', 'out']
    });
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'direction',
      component: _components_controls_direction__WEBPACK_IMPORTED_MODULE_2__.Direction,
      options: ['top', 'bottom', 'left', 'right']
    });
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'effect',
      component: _components_controls_effect__WEBPACK_IMPORTED_MODULE_4__.Effect,
      options: ['fade', 'slide', 'scale']
    });
    (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_10__.registerInteractionsControl)({
      type: 'repeat',
      component: _components_controls_repeat__WEBPACK_IMPORTED_MODULE_6__.Repeat
    });
    (0,_mcp__WEBPACK_IMPORTED_MODULE_12__.initMcpInteractions)((0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.getMCPByDomain)('interactions', {
      docs: _mcp__WEBPACK_IMPORTED_MODULE_12__.EDITOR_INTERACTIONS_MCP_DESCRIPTION,
      instructions: _mcp__WEBPACK_IMPORTED_MODULE_12__.EDITOR_INTERACTIONS_MCP_SHORT_DESCRIPTION
    }));
  } catch (error) {
    throw error;
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/interactions-controls-registry.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/interactions-controls-registry.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getInteractionsControl: function() { return /* binding */ getInteractionsControl; },
/* harmony export */   getInteractionsControlOptions: function() { return /* binding */ getInteractionsControlOptions; },
/* harmony export */   registerInteractionsControl: function() { return /* binding */ registerInteractionsControl; }
/* harmony export */ });
const controlsRegistry = new Map();
function registerInteractionsControl({
  type,
  component,
  options
}) {
  controlsRegistry.set(type, {
    type,
    component: component,
    options
  });
}
function getInteractionsControl(type) {
  return controlsRegistry.get(type);
}
function getInteractionsControlOptions(type) {
  return controlsRegistry.get(type)?.options ?? [];
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/interactions-repository.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/interactions-repository.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   interactionsRepository: function() { return /* binding */ interactionsRepository; }
/* harmony export */ });
/* harmony import */ var _utils_create_interactions_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/create-interactions-repository */ "./packages/packages/core/editor-interactions/src/utils/create-interactions-repository.ts");

const interactionsRepository = (0,_utils_create_interactions_repository__WEBPACK_IMPORTED_MODULE_0__.createInteractionsRepository)();

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/mcp/constants.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/mcp/constants.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDITOR_INTERACTIONS_MCP_DESCRIPTION: function() { return /* binding */ EDITOR_INTERACTIONS_MCP_DESCRIPTION; },
/* harmony export */   EDITOR_INTERACTIONS_MCP_SHORT_DESCRIPTION: function() { return /* binding */ EDITOR_INTERACTIONS_MCP_SHORT_DESCRIPTION; },
/* harmony export */   MAX_INTERACTIONS_PER_ELEMENT: function() { return /* binding */ MAX_INTERACTIONS_PER_ELEMENT; }
/* harmony export */ });
const MAX_INTERACTIONS_PER_ELEMENT = 5;
const EDITOR_INTERACTIONS_MCP_SHORT_DESCRIPTION = `Everything related to V4 ( Atomic ) interactions.
# Interactions
- Create/update/delete interactions
- Get list of interactions
- Get details of an interaction
`;
const EDITOR_INTERACTIONS_MCP_DESCRIPTION = `MCP server for managing element interactions and animations. Use this to add, modify, or remove animations and motion effects triggered by user events such as page load or scroll-into-view.
		** IMPORTANT **
		Use the "interactions-schema" resource to get the schema of the interactions.
		Actions:
		- get: Read the current interactions on the element.
		- add: Add a new interaction (max ${MAX_INTERACTIONS_PER_ELEMENT} per element).
		- update: Update an existing interaction by its interactionId.
		- delete: Remove a specific interaction by its interactionId.
		- clear: Remove all interactions from the element.

		For add/update, provide: trigger, effect, effectType, direction (required for slide effect), duration, delay, easing.
		Use excludedBreakpoints to disable the animation on specific responsive breakpoints (e.g. ["mobile", "tablet"]).
		Example Get Request:
		{
			"elementId": "123",
			"action": "get",
			"interactionId": "123",
			"animationData": {
				"trigger": "click",
				"effect": "fade",
			}
		}
		Example Add Request:
		{
			"elementId": "123",
			"action": "add",
			"animationData": {
				"effectType": "in",
				"direction": "top",
				"trigger": "click",
				"effect": "fade",
				"duration": 1000,
				"delay": 0,
				"easing": "easeIn",
				"excludedBreakpoints": ["mobile", "tablet"],
			}
		}
		Example Update Request:
		{
			"elementId": "123",
			"action": "update",
			"interactionId": "123",
			"animationData": {
				"trigger": "click",
				"effect": "fade",
			}
		}
		Example Delete Request:
		{
			"elementId": "123",
			"action": "delete",
			"interactionId": "123",
		}
		Example Clear Request:
		{
			"elementId": "123",
			"action": "clear",
		}`;

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/mcp/index.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/mcp/index.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDITOR_INTERACTIONS_MCP_DESCRIPTION: function() { return /* reexport safe */ _constants__WEBPACK_IMPORTED_MODULE_2__.EDITOR_INTERACTIONS_MCP_DESCRIPTION; },
/* harmony export */   EDITOR_INTERACTIONS_MCP_SHORT_DESCRIPTION: function() { return /* reexport safe */ _constants__WEBPACK_IMPORTED_MODULE_2__.EDITOR_INTERACTIONS_MCP_SHORT_DESCRIPTION; },
/* harmony export */   MAX_INTERACTIONS_PER_ELEMENT: function() { return /* reexport safe */ _constants__WEBPACK_IMPORTED_MODULE_2__.MAX_INTERACTIONS_PER_ELEMENT; },
/* harmony export */   initMcpInteractions: function() { return /* binding */ initMcpInteractions; }
/* harmony export */ });
/* harmony import */ var _resources_interactions_schema_resource__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./resources/interactions-schema-resource */ "./packages/packages/core/editor-interactions/src/mcp/resources/interactions-schema-resource.ts");
/* harmony import */ var _tools_manage_element_interaction_tool__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./tools/manage-element-interaction-tool */ "./packages/packages/core/editor-interactions/src/mcp/tools/manage-element-interaction-tool.ts");
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./constants */ "./packages/packages/core/editor-interactions/src/mcp/constants.ts");



const initMcpInteractions = reg => {
  const {
    setMCPDescription
  } = reg;
  setMCPDescription(`Everything related to V4 ( Atomic ) interactions.
# Interactions
- Create/update/delete interactions
- Get list of interactions
- Get details of an interaction
`);
  (0,_resources_interactions_schema_resource__WEBPACK_IMPORTED_MODULE_0__.initInteractionsSchemaResource)(reg);
  (0,_tools_manage_element_interaction_tool__WEBPACK_IMPORTED_MODULE_1__.initManageElementInteractionTool)(reg);
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/mcp/resources/interactions-schema-resource.ts":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/mcp/resources/interactions-schema-resource.ts ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   INTERACTIONS_SCHEMA_URI: function() { return /* binding */ INTERACTIONS_SCHEMA_URI; },
/* harmony export */   initInteractionsSchemaResource: function() { return /* binding */ initInteractionsSchemaResource; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _tools_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../tools/schema */ "./packages/packages/core/editor-interactions/src/mcp/tools/schema.ts");


const INTERACTIONS_SCHEMA_URI = 'elementor://interactions/schema';
const initInteractionsSchemaResource = reg => {
  const {
    resource
  } = reg;
  const schema = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.isProActive)() ? {
    ..._tools_schema__WEBPACK_IMPORTED_MODULE_1__.baseSchema,
    ..._tools_schema__WEBPACK_IMPORTED_MODULE_1__.proSchema
  } : _tools_schema__WEBPACK_IMPORTED_MODULE_1__.baseSchema;
  resource('interactions-schema', INTERACTIONS_SCHEMA_URI, {
    description: 'Schema describing all available options for element interactions.'
  }, async () => {
    return {
      contents: [{
        uri: INTERACTIONS_SCHEMA_URI,
        mimeType: 'application/json',
        text: JSON.stringify(schema)
      }]
    };
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/mcp/tools/manage-element-interaction-tool.ts":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/mcp/tools/manage-element-interaction-tool.ts ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initManageElementInteractionTool: function() { return /* binding */ initManageElementInteractionTool; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _interactions_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../interactions-repository */ "./packages/packages/core/editor-interactions/src/interactions-repository.ts");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../utils/temp-id-utils */ "./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts");
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../constants */ "./packages/packages/core/editor-interactions/src/mcp/constants.ts");
/* harmony import */ var _resources_interactions_schema_resource__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../resources/interactions-schema-resource */ "./packages/packages/core/editor-interactions/src/mcp/resources/interactions-schema-resource.ts");
/* harmony import */ var _schema__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./schema */ "./packages/packages/core/editor-interactions/src/mcp/tools/schema.ts");









const EMPTY_INTERACTIONS = {
  version: 1,
  items: []
};
const EFFECTS_WITHOUT_TYPE = ['custom'];
const BREAKPOINTS_SCHEMA_URI = 'elementor://breakpoints/list';
const initManageElementInteractionTool = reg => {
  const {
    addTool
  } = reg;
  const extendedSchema = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_2__.isProActive)() ? {
    ..._schema__WEBPACK_IMPORTED_MODULE_8__.baseSchema,
    ..._schema__WEBPACK_IMPORTED_MODULE_8__.proSchema
  } : _schema__WEBPACK_IMPORTED_MODULE_8__.baseSchema;
  const schema = {
    elementId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().describe('The ID of the element to read or modify interactions on'),
    action: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.enum(['get', 'add', 'update', 'delete', 'clear']).describe('Operation to perform. Use "get" first to inspect existing interactions.'),
    interactionId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().optional().describe('Interaction ID — required for update and delete. Obtain from a prior "get" call.'),
    ...extendedSchema
  };
  addTool({
    name: 'manage-element-interaction',
    description: `Manage the element interaction.`,
    schema,
    requiredResources: [{
      uri: _resources_interactions_schema_resource__WEBPACK_IMPORTED_MODULE_7__.INTERACTIONS_SCHEMA_URI,
      description: 'Interactions schema with all available options'
    }, {
      uri: BREAKPOINTS_SCHEMA_URI,
      description: 'Available breakpoint IDs for excludedBreakpoints'
    }],
    isDestructive: true,
    outputSchema: {
      success: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.boolean().describe('Whether the action was successful'),
      action: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.enum(['get', 'add', 'update', 'delete', 'clear']).describe('Operation to perform. Use "get" first to inspect existing interactions.'),
      elementId: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string().optional().describe('The ID of the element to read or modify interactions on'),
      interactions: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.any()).optional().describe('The interactions on the element'),
      count: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.number().optional().describe('The number of interactions on the element')
    },
    handler: input => {
      const {
        elementId,
        action,
        interactionId,
        ...animationData
      } = input;
      const {
        effectType,
        ...restAnimationData
      } = animationData;
      const effect = restAnimationData.effect;
      const resolvedType = effectType ?? (effect && !EFFECTS_WITHOUT_TYPE.includes(effect) ? 'in' : undefined);
      const allInteractions = _interactions_repository__WEBPACK_IMPORTED_MODULE_3__.interactionsRepository.all();
      const elementData = allInteractions.find(data => data.elementId === elementId);
      const currentInteractions = elementData?.interactions ?? EMPTY_INTERACTIONS;
      if (action === 'get') {
        const summary = currentInteractions.items.map(item => {
          const {
            value
          } = item;
          const animValue = value.animation.value;
          const timingValue = animValue.timing_config.value;
          const configValue = animValue.config.value;
          return {
            id: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(value.interaction_id),
            trigger: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(value.trigger),
            effect: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(animValue.effect),
            effectType: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(animValue.type),
            direction: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(animValue.direction),
            duration: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractSize)(timingValue.duration),
            delay: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractSize)(timingValue.delay),
            easing: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(configValue.easing),
            excludedBreakpoints: (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractExcludedBreakpoints)(value.breakpoints)
          };
        });
        return {
          success: true,
          elementId,
          action,
          interactions: summary,
          count: summary.length
        };
      }
      let updatedItems = [...currentInteractions.items];
      switch (action) {
        case 'add':
          {
            if (updatedItems.length >= _constants__WEBPACK_IMPORTED_MODULE_6__.MAX_INTERACTIONS_PER_ELEMENT) {
              throw new Error(`Cannot add more than ${_constants__WEBPACK_IMPORTED_MODULE_6__.MAX_INTERACTIONS_PER_ELEMENT} interactions per element. Current count: ${updatedItems.length}. Delete an existing interaction first.`);
            }
            const newItem = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.createInteractionItem)({
              interactionId: (0,_utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_5__.generateTempInteractionId)(),
              ...restAnimationData,
              type: resolvedType
            });
            updatedItems = [...updatedItems, newItem];
            break;
          }
        case 'update':
          {
            if (!interactionId) {
              throw new Error('interactionId is required for the update action.');
            }
            const itemIndex = updatedItems.findIndex(item => (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(item.value.interaction_id) === interactionId);
            if (itemIndex === -1) {
              throw new Error(`Interaction with ID "${interactionId}" not found on element "${elementId}".`);
            }
            const updatedItem = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.createInteractionItem)({
              interactionId,
              ...restAnimationData,
              type: resolvedType
            });
            updatedItems = [...updatedItems.slice(0, itemIndex), updatedItem, ...updatedItems.slice(itemIndex + 1)];
            break;
          }
        case 'delete':
          {
            if (!interactionId) {
              throw new Error('interactionId is required for the delete action.');
            }
            const beforeCount = updatedItems.length;
            updatedItems = updatedItems.filter(item => (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_4__.extractString)(item.value.interaction_id) !== interactionId);
            if (updatedItems.length === beforeCount) {
              throw new Error(`Interaction with ID "${interactionId}" not found on element "${elementId}".`);
            }
            break;
          }
        case 'clear':
          {
            updatedItems = [];
            break;
          }
      }
      const updatedInteractions = {
        ...currentInteractions,
        items: updatedItems
      };
      try {
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementInteractions)({
          elementId,
          interactions: updatedInteractions
        });
      } catch (error) {
        throw new Error(`Failed to update interactions for element "${elementId}": ${error instanceof Error ? error.message : 'Unknown error'}`);
      }
      return {
        success: true,
        action,
        elementId,
        count: updatedItems.length
      };
    }
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/mcp/tools/schema.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/mcp/tools/schema.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   baseSchema: function() { return /* binding */ baseSchema; },
/* harmony export */   proSchema: function() { return /* binding */ proSchema; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);

const baseSchema = {
  trigger: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['load', 'scrollIn']).optional().describe('Event that triggers the animation'),
  effect: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['fade', 'slide', 'scale']).optional().describe('Animation effect type'),
  effectType: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['in', 'out']).optional().describe('Whether the animation plays in or out'),
  direction: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['top', 'bottom', 'left', 'right', 'top-left', 'top-right', 'bottom-left', 'bottom-right']).optional().describe('Direction of the Animation. Can be one of the following or empty if not needed. At slide effect, this is required field.'),
  duration: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(10000).optional().describe('Animation duration in milliseconds'),
  delay: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(10000).optional().describe('Animation delay in milliseconds'),
  easing: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['easeIn']).optional().describe('Easing function for the animation. Use "easeIn" for free tier.'),
  excludedBreakpoints: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['widescreen', 'desktop', 'laptop', 'tablet_extra', 'tablet', 'mobile_extra', 'mobile'])).optional().describe('Breakpoint IDs on which this interaction is disabled (e.g. ["mobile", "tablet"]). Fetch the "elementor://breakpoints/list" resource to get the valid IDs for the current site. Omit to enable on all breakpoints.')
};
const proSchema = {
  trigger: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['load', 'scrollIn', 'scrollOut', 'scrollOn', 'hover', 'click']).optional().describe('Event that triggers the animation'),
  effect: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['fade', 'slide', 'scale', 'custom']).optional().describe('Animation effect type'),
  easing: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['easeIn', 'easeInOut', 'easeOut', 'backIn', 'backInOut', 'backOut', 'linear']).optional().describe('Easing function for the animation.'),
  customEffects: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
    keyframes: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
      stop: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().describe('The stop of the keyframe in percent, can be either 0 or 100'),
      value: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
        opacity: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(1).describe('The opacity of the keyframe'),
        scale: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
          x: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(1).describe('The x scale of the keyframe'),
          y: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(1).describe('The y scale of the keyframe')
        }).optional().describe('The scale of the keyframe'),
        rotate: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
          x: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(360).describe('The x rotate of the keyframe'),
          y: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(360).describe('The y rotate of the keyframe'),
          z: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(360).describe('The z rotate of the keyframe')
        }).optional().describe('The rotate of the keyframe'),
        move: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
          x: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(1).describe('The x move of the keyframe'),
          y: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(1).describe('The y move of the keyframe'),
          z: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(1).describe('The z move of the keyframe')
        }).optional().describe('The move of the keyframe'),
        skew: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
          x: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(360).describe('The x skew of the keyframe'),
          y: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().min(0).max(360).describe('The y skew of the keyframe')
        }).optional().describe('The skew of the keyframe')
      })
    })).describe('The keyframes of the custom effect')
  }).optional().describe('The custom effect to use for the animation')
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/providers/document-elements-interactions-provider.ts":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/providers/document-elements-interactions-provider.ts ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX: function() { return /* binding */ ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX; },
/* harmony export */   documentElementsInteractionsProvider: function() { return /* binding */ documentElementsInteractionsProvider; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_create_interactions_provider__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/create-interactions-provider */ "./packages/packages/core/editor-interactions/src/utils/create-interactions-provider.ts");



const ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX = 'document-elements-interactions-';
const documentElementsInteractionsProvider = (0,_utils_create_interactions_provider__WEBPACK_IMPORTED_MODULE_2__.createInteractionsProvider)({
  key: () => {
    const documentId = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getCurrentDocumentId)();
    if (!documentId) {
      const pendingKey = `${ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX}pending`;
      return pendingKey;
    }
    const key = `${ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX}${documentId}`;
    return key;
  },
  priority: 50,
  subscribe: cb => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.windowEvent)('elementor/element/update_interactions')], () => cb());
  },
  actions: {
    all: () => {
      const elements = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElements)();
      const filtered = elements.filter(element => {
        const interactions = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementInteractions)(element.id);
        if (!interactions) {
          return false;
        }
        return interactions?.items?.length > 0;
      });
      return filtered.map(element => {
        const interactions = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementInteractions)(element.id);
        return {
          elementId: element.id,
          dataId: element.id,
          interactions: interactions || {
            version: 1,
            items: []
          }
        };
      });
    }
  }
});

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/ui/interactions-promotion-chip.tsx":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/ui/interactions-promotion-chip.tsx ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsPromotionChip: function() { return /* binding */ InteractionsPromotionChip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);






const InteractionsPromotionChip = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  content,
  upgradeUrl,
  anchorRef,
  trackingData
}, ref) => {
  const [isOpen, setIsOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.useCanvasClickHandler)(isOpen, () => setIsOpen(false));
  const toggle = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setIsOpen(prev => {
      if (!prev) {
        (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackViewPromotion)(trackingData);
      }
      return !prev;
    });
  }, [trackingData]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useImperativeHandle)(ref, () => ({
    toggle
  }), [toggle]);
  const handleToggle = e => {
    e.stopPropagation();
    toggle();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PromotionPopover, {
    open: isOpen,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Interactions', 'elementor'),
    content: content,
    ctaText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Upgrade now', 'elementor'),
    ctaUrl: upgradeUrl,
    anchorRef: anchorRef,
    placement: anchorRef ? 'right-start' : undefined,
    onClose: e => {
      e.stopPropagation();
      setIsOpen(false);
    },
    onCtaClick: () => (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackUpgradePromotionClick)(trackingData)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    onMouseDown: e => e.stopPropagation(),
    onClick: handleToggle,
    sx: {
      cursor: 'pointer',
      display: 'inline-flex',
      mr: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PromotionChip, null)));
});

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/ui/promotion-overlay-layout.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/ui/promotion-overlay-layout.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PromotionOverlayLayout: function() { return /* binding */ PromotionOverlayLayout; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);



const OVERLAY_GRID = '1 / 1';
const CHIP_OFFSET = '50%';
const PromotionOverlayLayout = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  children,
  promotionChip
}, ref) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  ref: ref,
  sx: {
    display: 'grid',
    alignItems: 'center'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  sx: {
    gridArea: OVERLAY_GRID
  }
}, children), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  sx: {
    gridArea: OVERLAY_GRID,
    marginInlineEnd: CHIP_OFFSET,
    justifySelf: 'end'
  }
}, promotionChip)));

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/ui/promotion-select.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/ui/promotion-select.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PromotionSelect: function() { return /* binding */ PromotionSelect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _interactions_promotion_chip__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./interactions-promotion-chip */ "./packages/packages/core/editor-interactions/src/ui/interactions-promotion-chip.tsx");






function PromotionSelect({
  value,
  onChange,
  baseOptions,
  disabledOptions,
  promotionLabel,
  promotionContent,
  upgradeUrl,
  trackingData
}) {
  const promotionRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const anchorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Select, {
    value: value,
    onChange: e => onChange?.(e.target.value),
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    MenuProps: {
      disablePortal: true
    }
  }, Object.entries(baseOptions).map(([key, label]) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuListItem, {
    key: key,
    value: key
  }, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuSubheader, {
    ref: anchorRef,
    sx: {
      cursor: 'pointer',
      color: 'text.tertiary',
      fontWeight: '400',
      display: 'flex',
      alignItems: 'center'
    },
    onMouseDown: e => {
      e.stopPropagation();
      promotionRef.current?.toggle();
    }
  }, promotionLabel ?? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('PRO features', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_interactions_promotion_chip__WEBPACK_IMPORTED_MODULE_4__.InteractionsPromotionChip, {
    content: promotionContent,
    upgradeUrl: upgradeUrl,
    ref: promotionRef,
    anchorRef: anchorRef,
    trackingData: trackingData
  })), Object.entries(disabledOptions).map(([key, label]) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuListItem, {
    key: key,
    value: key,
    disabled: true,
    sx: {
      pl: 3
    }
  }, label)));
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/create-interactions-provider.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/create-interactions-provider.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createInteractionsProvider: function() { return /* binding */ createInteractionsProvider; }
/* harmony export */ });
const DEFAULT_PRIORITY = 10;
function createInteractionsProvider({
  key,
  priority = DEFAULT_PRIORITY,
  subscribe = () => () => {},
  actions
}) {
  return {
    getKey: typeof key === 'string' ? () => key : key,
    priority,
    subscribe,
    actions: {
      all: actions.all
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/create-interactions-repository.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/create-interactions-repository.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createInteractionsRepository: function() { return /* binding */ createInteractionsRepository; }
/* harmony export */ });
const createInteractionsRepository = () => {
  const providers = [];
  const getProviders = () => {
    const sorted = providers.slice(0).sort((a, b) => a.priority > b.priority ? -1 : 1);
    return sorted;
  };
  const register = provider => {
    providers.push(provider);
  };
  const all = () => {
    return getProviders().flatMap(provider => provider.actions.all());
  };
  const subscribe = cb => {
    const unsubscribes = providers.map(provider => provider.subscribe(cb));
    return () => {
      unsubscribes.forEach(unsubscribe => unsubscribe());
    };
  };
  const getProviderByKey = key => {
    return providers.find(provider => {
      try {
        return provider.getKey() === key;
      } catch {
        // Provider might not be ready yet (e.g., no document loaded)
        return false;
      }
    });
  };
  return {
    all,
    register,
    subscribe,
    getProviders,
    getProviderByKey
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/custom-effect-to-prop-value.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/custom-effect-to-prop-value.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   toCustomEffectPropValue: function() { return /* binding */ toCustomEffectPropValue; }
/* harmony export */ });
const CUSTOM_EFFECT_TYPE = 'custom-effect';
const KEYFRAMES_TYPE = 'keyframes';
const KEYFRAME_STOP_TYPE = 'keyframe-stop';
const KEYFRAME_STOP_SETTINGS_TYPE = 'keyframe-stop-settings';
const SIZE_TYPE = 'size';
const NUMBER_TYPE = 'number';
const TRANSFORM_SCALE_TYPE = 'transform-scale';
const TRANSFORM_ROTATE_TYPE = 'transform-rotate';
const TRANSFORM_MOVE_TYPE = 'transform-move';
const TRANSFORM_SKEW_TYPE = 'transform-skew';
const UNIT_PERCENT = '%';
const UNIT_DEG = 'deg';
const UNIT_PX = 'px';
const isPlainCustomEffect = v => typeof v === 'object' && v !== null && 'keyframes' in v && Array.isArray(v.keyframes) && !('$$type' in v);
const toSizePropValue = (size, unit = UNIT_PERCENT) => ({
  $$type: SIZE_TYPE,
  value: {
    size,
    unit
  }
});
const toNumberPropValue = n => ({
  $$type: NUMBER_TYPE,
  value: n
});
const toDimensionalNumberPropValue = (type, plain, defaults) => ({
  $$type: type,
  value: {
    x: toNumberPropValue(plain.x ?? defaults.x),
    y: toNumberPropValue(plain.y ?? defaults.y),
    z: toNumberPropValue(plain.z ?? defaults.z)
  }
});
const toDimensionalSizePropValue = (type, plain, defaults, unit) => ({
  $$type: type,
  value: {
    x: toSizePropValue(plain.x ?? defaults.x, unit),
    y: toSizePropValue(plain.y ?? defaults.y, unit),
    z: toSizePropValue(plain.z ?? defaults.z, unit)
  }
});
const toSkewPropValue = plain => ({
  $$type: TRANSFORM_SKEW_TYPE,
  value: {
    x: toSizePropValue(plain.x ?? 0, UNIT_DEG),
    y: toSizePropValue(plain.y ?? 0, UNIT_DEG)
  }
});
const toKeyframeStopSettingsPropValue = plain => {
  const value = {};
  if (plain.opacity !== undefined) {
    const percent = plain.opacity <= 1 ? Math.round(plain.opacity * 100) : plain.opacity;
    value.opacity = toSizePropValue(percent);
  }
  if (plain.scale !== undefined) {
    value.scale = toDimensionalNumberPropValue(TRANSFORM_SCALE_TYPE, plain.scale, {
      x: 1,
      y: 1,
      z: 1
    });
  }
  if (plain.rotate !== undefined) {
    value.rotate = toDimensionalSizePropValue(TRANSFORM_ROTATE_TYPE, plain.rotate, {
      x: 0,
      y: 0,
      z: 0
    }, UNIT_DEG);
  }
  if (plain.move !== undefined) {
    value.move = toDimensionalSizePropValue(TRANSFORM_MOVE_TYPE, plain.move, {
      x: 0,
      y: 0,
      z: 0
    }, UNIT_PX);
  }
  if (plain.skew !== undefined) {
    value.skew = toSkewPropValue(plain.skew);
  }
  return {
    $$type: KEYFRAME_STOP_SETTINGS_TYPE,
    value
  };
};
const isPlainKeyframe = v => typeof v === 'object' && v !== null && 'stop' in v && 'value' in v && !('$$type' in v);
const toKeyframeStopPropValue = item => {
  if (!isPlainKeyframe(item)) {
    return item;
  }
  return {
    $$type: KEYFRAME_STOP_TYPE,
    value: {
      stop: toSizePropValue(item.stop),
      settings: toKeyframeStopSettingsPropValue(item.value)
    }
  };
};
const toKeyframesPropValue = keyframes => ({
  $$type: KEYFRAMES_TYPE,
  value: keyframes.map(toKeyframeStopPropValue)
});
const plainCustomEffectToPropValue = plain => ({
  $$type: CUSTOM_EFFECT_TYPE,
  value: {
    keyframes: toKeyframesPropValue(plain.keyframes)
  }
});
const toCustomEffectPropValue = customEffects => {
  if (customEffects === undefined) {
    return undefined;
  }
  if (isPlainCustomEffect(customEffects)) {
    return plainCustomEffectToPropValue(customEffects);
  }
  return customEffects;
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/filter-interactions.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/filter-interactions.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   filterInteractions: function() { return /* binding */ filterInteractions; }
/* harmony export */ });
/* harmony import */ var _is_supported_interaction_item__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-supported-interaction-item */ "./packages/packages/core/editor-interactions/src/utils/is-supported-interaction-item.ts");

const filterInteractions = interactions => {
  return interactions.filter(interaction => {
    return (0,_is_supported_interaction_item__WEBPACK_IMPORTED_MODULE_0__.isSupportedInteractionItem)(interaction);
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/get-interactions-config.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/get-interactions-config.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getInteractionsConfig: function() { return /* binding */ getInteractionsConfig; }
/* harmony export */ });
function getInteractionsConfig() {
  return window.ElementorInteractionsConfig ?? {};
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/is-supported-interaction-item.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/is-supported-interaction-item.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isSupportedInteractionItem: function() { return /* binding */ isSupportedInteractionItem; }
/* harmony export */ });
/* harmony import */ var _interactions_controls_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../interactions-controls-registry */ "./packages/packages/core/editor-interactions/src/interactions-controls-registry.ts");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");


function isSupportedInteractionItem(interaction) {
  const value = interaction.value;
  const replay = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_1__.extractBoolean)(value.animation.value.config?.value.replay);
  if (true === replay) {
    return hasSupport('replay', 'yes');
  }
  const trigger = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_1__.extractString)(value.trigger);
  const easing = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_1__.extractString)(value.animation.value.config?.value.easing);
  const effect = (0,_utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_1__.extractString)(value.animation.value.effect);
  const checks = [['trigger', trigger], ['easing', easing], ['effect', effect]];
  return checks.every(([controlType, controlValue]) => {
    if (controlValue === '' || controlValue === null) {
      return true;
    }
    return hasSupport(controlType, controlValue);
  });
}
function hasSupport(controlType, controlValue) {
  const supportedOptions = (0,_interactions_controls_registry__WEBPACK_IMPORTED_MODULE_0__.getInteractionsControlOptions)(controlType);
  if (1 > supportedOptions.length) {
    return true;
  }
  return supportedOptions.includes(controlValue);
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildDisplayLabel: function() { return /* binding */ buildDisplayLabel; },
/* harmony export */   createAnimationPreset: function() { return /* binding */ createAnimationPreset; },
/* harmony export */   createBoolean: function() { return /* binding */ createBoolean; },
/* harmony export */   createConfig: function() { return /* binding */ createConfig; },
/* harmony export */   createDefaultInteractionItem: function() { return /* binding */ createDefaultInteractionItem; },
/* harmony export */   createDefaultInteractions: function() { return /* binding */ createDefaultInteractions; },
/* harmony export */   createExcludedBreakpoints: function() { return /* binding */ createExcludedBreakpoints; },
/* harmony export */   createInteractionBreakpoints: function() { return /* binding */ createInteractionBreakpoints; },
/* harmony export */   createInteractionItem: function() { return /* binding */ createInteractionItem; },
/* harmony export */   createNumber: function() { return /* binding */ createNumber; },
/* harmony export */   createString: function() { return /* binding */ createString; },
/* harmony export */   createTimingConfig: function() { return /* binding */ createTimingConfig; },
/* harmony export */   extractBoolean: function() { return /* binding */ extractBoolean; },
/* harmony export */   extractExcludedBreakpoints: function() { return /* binding */ extractExcludedBreakpoints; },
/* harmony export */   extractSize: function() { return /* binding */ extractSize; },
/* harmony export */   extractString: function() { return /* binding */ extractString; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _configs_time_constants__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../configs/time-constants */ "./packages/packages/core/editor-interactions/src/configs/time-constants.ts");
/* harmony import */ var _utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/size-transform-utils */ "./packages/packages/core/editor-interactions/src/utils/size-transform-utils.ts");
/* harmony import */ var _custom_effect_to_prop_value__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./custom-effect-to-prop-value */ "./packages/packages/core/editor-interactions/src/utils/custom-effect-to-prop-value.ts");
/* harmony import */ var _get_interactions_config__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./get-interactions-config */ "./packages/packages/core/editor-interactions/src/utils/get-interactions-config.ts");
/* harmony import */ var _temp_id_utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./temp-id-utils */ "./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts");






const createString = value => ({
  $$type: 'string',
  value
});
const createNumber = value => ({
  $$type: 'number',
  value
});
const createTimingConfig = (duration, delay) => ({
  $$type: 'timing-config',
  value: {
    duration: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.sizePropTypeUtil.create((0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_2__.parseSizeValue)(duration, _configs_time_constants__WEBPACK_IMPORTED_MODULE_1__.TIME_UNITS, undefined, _configs_time_constants__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_TIME_UNIT)),
    delay: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.sizePropTypeUtil.create((0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_2__.parseSizeValue)(delay, _configs_time_constants__WEBPACK_IMPORTED_MODULE_1__.TIME_UNITS, undefined, _configs_time_constants__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_TIME_UNIT))
  }
});
const createBoolean = value => ({
  $$type: 'boolean',
  value
});
const createConfig = ({
  replay,
  easing = 'easeIn',
  relativeTo = 'viewport',
  repeat = '',
  times = 1,
  start = 85,
  end = 15
}) => ({
  $$type: 'config',
  value: {
    replay: createBoolean(replay),
    easing: createString(easing),
    relativeTo: createString(relativeTo),
    repeat: createString(repeat),
    times: createNumber(times),
    start: createSize(start, '%'),
    end: createSize(end, '%')
  }
});
const createSize = (value, defaultUnit, defaultValue) => {
  if (!value) {
    return;
  }
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.sizePropTypeUtil.create((0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_2__.parseSizeValue)(value, ['%'], defaultValue, defaultUnit));
};
const extractBoolean = (prop, fallback = false) => {
  return prop?.value ?? fallback;
};
const createExcludedBreakpoints = breakpoints => ({
  $$type: 'excluded-breakpoints',
  value: breakpoints.map(createString)
});
const createInteractionBreakpoints = excluded => ({
  $$type: 'interaction-breakpoints',
  value: {
    excluded: createExcludedBreakpoints(excluded)
  }
});
const extractExcludedBreakpoints = breakpoints => {
  return breakpoints?.value.excluded.value.map(bp => bp.value) ?? [];
};
const createAnimationPreset = ({
  effect,
  type,
  direction,
  duration,
  delay,
  replay = false,
  easing = 'easeIn',
  relativeTo,
  repeat,
  times,
  start,
  end,
  customEffects
}) => {
  const customEffectProp = (0,_custom_effect_to_prop_value__WEBPACK_IMPORTED_MODULE_3__.toCustomEffectPropValue)(customEffects);
  return {
    $$type: 'animation-preset-props',
    value: {
      effect: createString(effect),
      ...(customEffectProp !== undefined && {
        custom_effect: customEffectProp
      }),
      type: createString(type),
      direction: createString(direction ?? ''),
      timing_config: createTimingConfig(duration, delay),
      config: createConfig({
        replay,
        easing,
        relativeTo,
        repeat,
        times,
        start,
        end
      })
    }
  };
};
const createInteractionItem = ({
  trigger,
  effect,
  type,
  direction,
  duration,
  delay,
  interactionId,
  replay = false,
  easing = 'easeIn',
  relativeTo,
  repeat,
  times,
  start,
  end,
  excludedBreakpoints,
  customEffects
}) => ({
  $$type: 'interaction-item',
  value: {
    ...(interactionId && {
      interaction_id: createString(interactionId)
    }),
    trigger: createString(trigger ?? ''),
    animation: createAnimationPreset({
      effect: effect ?? '',
      type: type ?? '',
      direction,
      duration: duration ?? 0,
      delay: delay ?? 0,
      replay,
      easing,
      relativeTo,
      repeat,
      times,
      start,
      end,
      customEffects
    }),
    ...(excludedBreakpoints && excludedBreakpoints.length > 0 && {
      breakpoints: createInteractionBreakpoints(excludedBreakpoints)
    })
  }
});
const createDefaultInteractionItem = () => {
  const {
    constants
  } = (0,_get_interactions_config__WEBPACK_IMPORTED_MODULE_4__.getInteractionsConfig)();
  return createInteractionItem({
    trigger: 'load',
    effect: 'fade',
    type: 'in',
    duration: constants.defaultDuration,
    delay: constants.defaultDelay,
    replay: false,
    easing: constants.defaultEasing,
    interactionId: (0,_temp_id_utils__WEBPACK_IMPORTED_MODULE_5__.generateTempInteractionId)()
  });
};
const createDefaultInteractions = () => ({
  version: 1,
  items: [createDefaultInteractionItem()]
});
const extractString = (prop, fallback = '') => {
  return prop?.value ?? fallback;
};
const extractSize = (prop, defaultValue) => {
  if (!prop?.value) {
    return defaultValue;
  }
  return (0,_utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_2__.formatSizeValue)(prop.value);
};
const TRIGGER_LABELS = {
  load: 'On page load',
  scrollIn: 'Scroll into view',
  scrollOut: 'Scroll out of view',
  scrollOn: 'While scrolling'
};
const capitalize = str => {
  return str.charAt(0).toUpperCase() + str.slice(1);
};
const buildDisplayLabel = item => {
  const trigger = extractString(item.trigger);
  const effect = extractString(item.animation.value.effect);
  const type = extractString(item.animation.value.type);
  const triggerLabel = TRIGGER_LABELS[trigger] || capitalize(trigger);
  const effectLabel = capitalize(effect);
  const typeLabel = 'custom' === effect ? '' : capitalize(type);
  return `${triggerLabel}: ${effectLabel} ${typeLabel}`;
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/resolve-direction.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/resolve-direction.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   resolveDirection: function() { return /* binding */ resolveDirection; }
/* harmony export */ });
const resolveDirection = (hasDirection, newEffect, newDirection, currentDirection, currentEffect) => {
  if (newEffect === 'slide' && !newDirection) {
    return 'top';
  }
  if (currentEffect === 'slide' && hasDirection) {
    return newDirection || 'top';
  }
  if (hasDirection) {
    return newDirection;
  }
  return currentDirection;
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/scroll-interaction-event.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/scroll-interaction-event.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SCROLL_INTERACTION_EVENT: function() { return /* binding */ SCROLL_INTERACTION_EVENT; },
/* harmony export */   dispatchScrollInteraction: function() { return /* binding */ dispatchScrollInteraction; },
/* harmony export */   extractScrollOverlayParams: function() { return /* binding */ extractScrollOverlayParams; },
/* harmony export */   syncGridOverlay: function() { return /* binding */ syncGridOverlay; }
/* harmony export */ });
/* harmony import */ var _prop_value_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");

const SCROLL_INTERACTION_EVENT = 'elementor/interactions/scroll-change';
function dispatchScrollInteraction(data) {
  window.dispatchEvent(new CustomEvent(SCROLL_INTERACTION_EVENT, {
    detail: data
  }));
}
function extractScrollOverlayParams(interaction, defaults) {
  return {
    trigger: (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_0__.extractString)(interaction.trigger, defaults.trigger),
    start: (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_0__.extractSize)(interaction.animation.value.config?.value.start, defaults.start),
    end: (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_0__.extractSize)(interaction.animation.value.config?.value.end, defaults.end),
    relativeTo: (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_0__.extractString)(interaction.animation.value.config?.value.relativeTo, defaults.relativeTo)
  };
}
function syncGridOverlay(trigger, start, end, relativeTo) {
  if (trigger === 'scrollOn') {
    dispatchScrollInteraction({
      start,
      end,
      relativeTo
    });
  } else {
    dispatchScrollInteraction(null);
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/size-transform-utils.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/size-transform-utils.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   formatSizeValue: function() { return /* binding */ formatSizeValue; },
/* harmony export */   parseSizeValue: function() { return /* binding */ parseSizeValue; }
/* harmony export */ });
const SIZE_REGEX = /^(?:(-?\d*\.?\d+)([a-z%]+)|([a-z%]+))$/i;
const parseSizeValue = (value, allowedUnits, defaultValue, defaultUnit) => {
  if (typeof value === 'number') {
    return {
      size: value,
      unit: defaultUnit
    };
  }
  const sizeValue = tryParse(value, allowedUnits, defaultUnit);
  if (sizeValue) {
    return sizeValue;
  }
  if (defaultValue) {
    const fallbackSize = tryParse(defaultValue, allowedUnits, defaultUnit);
    if (fallbackSize) {
      return fallbackSize;
    }
  }
  return createSizeValue(null, defaultUnit);
};
const tryParse = (value, allowedUnits, defaultUnit) => {
  if (typeof value === 'number') {
    return createSizeValue(value, defaultUnit);
  }
  const match = value && value.match(SIZE_REGEX);
  if (!match) {
    if (value) {
      return {
        size: Number(value),
        unit: defaultUnit
      };
    }
    return null;
  }
  const size = match[1] ? parseFloat(match[1]) : null;
  const unit = match[2] || match[3];
  if (!allowedUnits.includes(unit)) {
    return null;
  }
  return createSizeValue(size, unit);
};
const formatSizeValue = ({
  size,
  unit
}) => {
  return `${size ?? ''}${unit}`;
};
const createSizeValue = (size, unit) => {
  return {
    size,
    unit
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   generateTempInteractionId: function() { return /* binding */ generateTempInteractionId; },
/* harmony export */   isTempId: function() { return /* binding */ isTempId; }
/* harmony export */ });
const TEMP_ID_PREFIX = 'temp-';
const TEMP_ID_REGEX = /^temp-[a-z0-9]+$/i;
function generateTempInteractionId() {
  return `${TEMP_ID_PREFIX}${Math.random().toString(36).substring(2, 11)}`;
}
function isTempId(id) {
  return !!id && TEMP_ID_REGEX.test(id);
}

/***/ }),

/***/ "./packages/packages/core/editor-interactions/src/utils/tracking.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/utils/tracking.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   trackInteractionCreated: function() { return /* binding */ trackInteractionCreated; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _prop_value_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");



const TRIGGER_LABELS = {
  load: 'On page load',
  scrollIn: 'Scroll into view',
  scrollOut: 'Scroll out of view',
  scrollOn: 'While scrolling',
  hover: 'Hover',
  click: 'Click'
};
const capitalize = s => s.charAt(0).toUpperCase() + s.slice(1);
const trackInteractionCreated = (elementId, item) => {
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_1__.getMixpanel)();
  if (!config?.names?.interactions?.created) {
    return;
  }
  const trigger = (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_2__.extractString)(item.value.trigger);
  const effect = (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_2__.extractString)(item.value.animation.value.effect);
  const type = (0,_prop_value_utils__WEBPACK_IMPORTED_MODULE_2__.extractString)(item.value.animation.value.type);
  dispatchEvent?.(config.names.interactions.created, {
    app_type: config?.appTypes?.editor,
    window_name: config?.appTypes?.editor,
    interaction_type: config?.triggers?.click,
    target_name: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementLabel)(elementId),
    interaction_result: 'interaction_created',
    target_location: config?.locations?.widgetPanel,
    location_l1: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementLabel)(elementId),
    location_l2: 'interactions',
    interaction_description: 'interaction_created',
    interaction_trigger: TRIGGER_LABELS[trigger] ?? capitalize(trigger),
    interaction_effect: effect === 'custom' ? capitalize(effect) : `${capitalize(effect)} ${capitalize(type)}`
  });
};

/***/ }),

/***/ "@elementor/editor-controls":
/*!*************************************************!*\
  !*** external ["elementorV2","editorControls"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorControls"];

/***/ }),

/***/ "@elementor/editor-elements":
/*!*************************************************!*\
  !*** external ["elementorV2","editorElements"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorElements"];

/***/ }),

/***/ "@elementor/editor-mcp":
/*!********************************************!*\
  !*** external ["elementorV2","editorMcp"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorMcp"];

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

/***/ "@elementor/icons":
/*!****************************************!*\
  !*** external ["elementorV2","icons"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["icons"];

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

/***/ }),

/***/ "@elementor/session":
/*!******************************************!*\
  !*** external ["elementorV2","session"] ***!
  \******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["session"];

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
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-interactions/src/index.ts ***!
  \*****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BASE_EASINGS: function() { return /* reexport safe */ _components_controls_easing__WEBPACK_IMPORTED_MODULE_9__.BASE_EASINGS; },
/* harmony export */   BASE_EFFECTS: function() { return /* reexport safe */ _components_controls_effect__WEBPACK_IMPORTED_MODULE_11__.BASE_EFFECTS; },
/* harmony export */   BASE_REPLAY: function() { return /* reexport safe */ _components_controls_replay__WEBPACK_IMPORTED_MODULE_10__.BASE_REPLAY; },
/* harmony export */   BASE_TRIGGERS: function() { return /* reexport safe */ _components_controls_trigger__WEBPACK_IMPORTED_MODULE_8__.BASE_TRIGGERS; },
/* harmony export */   DEFAULT_VALUES: function() { return /* reexport safe */ _components_interaction_details__WEBPACK_IMPORTED_MODULE_13__.DEFAULT_VALUES; },
/* harmony export */   EASING_OPTIONS: function() { return /* reexport safe */ _components_controls_easing__WEBPACK_IMPORTED_MODULE_9__.EASING_OPTIONS; },
/* harmony export */   EFFECT_OPTIONS: function() { return /* reexport safe */ _components_controls_effect__WEBPACK_IMPORTED_MODULE_11__.EFFECT_OPTIONS; },
/* harmony export */   ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX: function() { return /* reexport safe */ _providers_document_elements_interactions_provider__WEBPACK_IMPORTED_MODULE_5__.ELEMENTS_INTERACTIONS_PROVIDER_KEY_PREFIX; },
/* harmony export */   EmptyState: function() { return /* reexport safe */ _components_empty_state__WEBPACK_IMPORTED_MODULE_0__.EmptyState; },
/* harmony export */   InteractionsTab: function() { return /* reexport safe */ _components_interactions_tab__WEBPACK_IMPORTED_MODULE_1__.InteractionsTab; },
/* harmony export */   REPEAT_OPTIONS: function() { return /* reexport safe */ _components_controls_repeat__WEBPACK_IMPORTED_MODULE_12__.REPEAT_OPTIONS; },
/* harmony export */   REPEAT_TOOLTIPS: function() { return /* reexport safe */ _components_controls_repeat__WEBPACK_IMPORTED_MODULE_12__.REPEAT_TOOLTIPS; },
/* harmony export */   REPLAY_OPTIONS: function() { return /* reexport safe */ _components_controls_replay__WEBPACK_IMPORTED_MODULE_10__.REPLAY_OPTIONS; },
/* harmony export */   SCROLL_INTERACTION_EVENT: function() { return /* reexport safe */ _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_19__.SCROLL_INTERACTION_EVENT; },
/* harmony export */   TRIGGER_OPTIONS: function() { return /* reexport safe */ _components_controls_trigger__WEBPACK_IMPORTED_MODULE_8__.TRIGGER_OPTIONS; },
/* harmony export */   buildDisplayLabel: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.buildDisplayLabel; },
/* harmony export */   createAnimationPreset: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createAnimationPreset; },
/* harmony export */   createBoolean: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createBoolean; },
/* harmony export */   createConfig: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createConfig; },
/* harmony export */   createDefaultInteractionItem: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createDefaultInteractionItem; },
/* harmony export */   createDefaultInteractions: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createDefaultInteractions; },
/* harmony export */   createExcludedBreakpoints: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createExcludedBreakpoints; },
/* harmony export */   createInteractionBreakpoints: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createInteractionBreakpoints; },
/* harmony export */   createInteractionItem: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createInteractionItem; },
/* harmony export */   createInteractionsProvider: function() { return /* reexport safe */ _utils_create_interactions_provider__WEBPACK_IMPORTED_MODULE_4__.createInteractionsProvider; },
/* harmony export */   createNumber: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createNumber; },
/* harmony export */   createString: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createString; },
/* harmony export */   createTimingConfig: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.createTimingConfig; },
/* harmony export */   dispatchScrollInteraction: function() { return /* reexport safe */ _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_19__.dispatchScrollInteraction; },
/* harmony export */   extractBoolean: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.extractBoolean; },
/* harmony export */   extractExcludedBreakpoints: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.extractExcludedBreakpoints; },
/* harmony export */   extractScrollOverlayParams: function() { return /* reexport safe */ _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_19__.extractScrollOverlayParams; },
/* harmony export */   extractSize: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.extractSize; },
/* harmony export */   extractString: function() { return /* reexport safe */ _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__.extractString; },
/* harmony export */   formatSizeValue: function() { return /* reexport safe */ _utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_17__.formatSizeValue; },
/* harmony export */   generateTempInteractionId: function() { return /* reexport safe */ _utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_15__.generateTempInteractionId; },
/* harmony export */   getInteractionsConfig: function() { return /* reexport safe */ _utils_get_interactions_config__WEBPACK_IMPORTED_MODULE_2__.getInteractionsConfig; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_6__.init; },
/* harmony export */   interactionsRepository: function() { return /* reexport safe */ _interactions_repository__WEBPACK_IMPORTED_MODULE_3__.interactionsRepository; },
/* harmony export */   isTempId: function() { return /* reexport safe */ _utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_15__.isTempId; },
/* harmony export */   parseSizeValue: function() { return /* reexport safe */ _utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_17__.parseSizeValue; },
/* harmony export */   registerInteractionsControl: function() { return /* reexport safe */ _interactions_controls_registry__WEBPACK_IMPORTED_MODULE_7__.registerInteractionsControl; },
/* harmony export */   resolveDirection: function() { return /* reexport safe */ _utils_resolve_direction__WEBPACK_IMPORTED_MODULE_16__.resolveDirection; },
/* harmony export */   syncGridOverlay: function() { return /* reexport safe */ _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_19__.syncGridOverlay; },
/* harmony export */   useElementInteractions: function() { return /* reexport safe */ _hooks_use_element_interactions__WEBPACK_IMPORTED_MODULE_18__.useElementInteractions; }
/* harmony export */ });
/* harmony import */ var _components_empty_state__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/empty-state */ "./packages/packages/core/editor-interactions/src/components/empty-state.tsx");
/* harmony import */ var _components_interactions_tab__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/interactions-tab */ "./packages/packages/core/editor-interactions/src/components/interactions-tab.tsx");
/* harmony import */ var _utils_get_interactions_config__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/get-interactions-config */ "./packages/packages/core/editor-interactions/src/utils/get-interactions-config.ts");
/* harmony import */ var _interactions_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./interactions-repository */ "./packages/packages/core/editor-interactions/src/interactions-repository.ts");
/* harmony import */ var _utils_create_interactions_provider__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/create-interactions-provider */ "./packages/packages/core/editor-interactions/src/utils/create-interactions-provider.ts");
/* harmony import */ var _providers_document_elements_interactions_provider__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./providers/document-elements-interactions-provider */ "./packages/packages/core/editor-interactions/src/providers/document-elements-interactions-provider.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-interactions/src/init.ts");
/* harmony import */ var _interactions_controls_registry__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./interactions-controls-registry */ "./packages/packages/core/editor-interactions/src/interactions-controls-registry.ts");
/* harmony import */ var _components_controls_trigger__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/controls/trigger */ "./packages/packages/core/editor-interactions/src/components/controls/trigger.tsx");
/* harmony import */ var _components_controls_easing__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/controls/easing */ "./packages/packages/core/editor-interactions/src/components/controls/easing.tsx");
/* harmony import */ var _components_controls_replay__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./components/controls/replay */ "./packages/packages/core/editor-interactions/src/components/controls/replay.tsx");
/* harmony import */ var _components_controls_effect__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./components/controls/effect */ "./packages/packages/core/editor-interactions/src/components/controls/effect.tsx");
/* harmony import */ var _components_controls_repeat__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/controls/repeat */ "./packages/packages/core/editor-interactions/src/components/controls/repeat.tsx");
/* harmony import */ var _components_interaction_details__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/interaction-details */ "./packages/packages/core/editor-interactions/src/components/interaction-details.tsx");
/* harmony import */ var _utils_prop_value_utils__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./utils/prop-value-utils */ "./packages/packages/core/editor-interactions/src/utils/prop-value-utils.ts");
/* harmony import */ var _utils_temp_id_utils__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./utils/temp-id-utils */ "./packages/packages/core/editor-interactions/src/utils/temp-id-utils.ts");
/* harmony import */ var _utils_resolve_direction__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./utils/resolve-direction */ "./packages/packages/core/editor-interactions/src/utils/resolve-direction.ts");
/* harmony import */ var _utils_size_transform_utils__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./utils/size-transform-utils */ "./packages/packages/core/editor-interactions/src/utils/size-transform-utils.ts");
/* harmony import */ var _hooks_use_element_interactions__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./hooks/use-element-interactions */ "./packages/packages/core/editor-interactions/src/hooks/use-element-interactions.ts");
/* harmony import */ var _utils_scroll_interaction_event__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./utils/scroll-interaction-event */ "./packages/packages/core/editor-interactions/src/utils/scroll-interaction-event.ts");




















}();
(window.elementorV2 = window.elementorV2 || {}).editorInteractions = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorInteractions?.init?.();
//# sourceMappingURL=editor-interactions.js.map
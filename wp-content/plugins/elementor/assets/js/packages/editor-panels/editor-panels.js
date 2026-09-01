/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-panels/src/api.ts":
/*!*********************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/api.ts ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createPanel: function() { return /* binding */ createPanel; },
/* harmony export */   registerPanel: function() { return /* binding */ registerPanel; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _location__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./location */ "./packages/packages/core/editor-panels/src/location.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-panels/src/store/index.ts");
/* harmony import */ var _sync__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./sync */ "./packages/packages/core/editor-panels/src/sync.ts");





function createPanel({
  id,
  component,
  onOpen,
  onClose,
  allowedEditModes,
  blockOnKitRoutes,
  isOpenPreviousElement = false
}) {
  const usePanelStatus = createUseStatus(id, {
    allowedEditModes,
    blockOnKitRoutes
  });
  const usePanelActions = createUseActions(id, usePanelStatus, {
    onOpen,
    onClose
  }, isOpenPreviousElement);
  return {
    panel: {
      id,
      component
    },
    usePanelStatus,
    usePanelActions
  };
}
function registerPanel({
  id,
  component
}) {
  (0,_location__WEBPACK_IMPORTED_MODULE_2__.injectIntoPanels)({
    id,
    component
  });
}
function createUseStatus(id, options = {}) {
  return () => {
    const openPanelId = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_3__.selectOpenId);
    const v1PanelStatus = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseRouteStatus)(_sync__WEBPACK_IMPORTED_MODULE_4__.V2_PANEL, options);
    return {
      isOpen: openPanelId === id && v1PanelStatus.isActive,
      isBlocked: v1PanelStatus.isBlocked
    };
  };
}
function createUseActions(id, useStatus, options = {}, isOpenPreviousElement) {
  let stateSnapshot = null;
  let previousSelectedElement = null;
  return () => {
    const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useDispatch)();
    const {
      isBlocked
    } = useStatus();
    return {
      open: async () => {
        if (isBlocked) {
          return;
        }
        if (isOpenPreviousElement) {
          previousSelectedElement = window.elementor?.selection?.getElements?.()[0]?.model.get('id') ?? null;
        }
        dispatch(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.open(id));
        stateSnapshot = options.onOpen?.() ?? null;
      },
      close: async () => {
        if (isBlocked) {
          return;
        }
        dispatch(_store__WEBPACK_IMPORTED_MODULE_3__.slice.actions.close(id));
        await options.onClose?.(stateSnapshot);
        if (previousSelectedElement) {
          try {
            const container = window.elementor?.getContainer?.(previousSelectedElement);
            (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('document/elements/select', {
              container
            });
          } catch {}
          previousSelectedElement = null;
        }
      }
    };
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/external/index.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/external/index.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Panel: function() { return /* reexport safe */ _panel__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   PanelBody: function() { return /* reexport safe */ _panel_body__WEBPACK_IMPORTED_MODULE_3__["default"]; },
/* harmony export */   PanelFooter: function() { return /* reexport safe */ _panel_footer__WEBPACK_IMPORTED_MODULE_4__["default"]; },
/* harmony export */   PanelHeader: function() { return /* reexport safe */ _panel_header__WEBPACK_IMPORTED_MODULE_1__["default"]; },
/* harmony export */   PanelHeaderTitle: function() { return /* reexport safe */ _panel_header_title__WEBPACK_IMPORTED_MODULE_2__["default"]; }
/* harmony export */ });
/* harmony import */ var _panel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./panel */ "./packages/packages/core/editor-panels/src/components/external/panel.tsx");
/* harmony import */ var _panel_header__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./panel-header */ "./packages/packages/core/editor-panels/src/components/external/panel-header.tsx");
/* harmony import */ var _panel_header_title__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./panel-header-title */ "./packages/packages/core/editor-panels/src/components/external/panel-header-title.tsx");
/* harmony import */ var _panel_body__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./panel-body */ "./packages/packages/core/editor-panels/src/components/external/panel-body.tsx");
/* harmony import */ var _panel_footer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./panel-footer */ "./packages/packages/core/editor-panels/src/components/external/panel-footer.tsx");






/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/external/panel-body.tsx":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/external/panel-body.tsx ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PanelBody; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function PanelBody({
  children,
  sx,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, _extends({
    component: "main",
    sx: {
      overflowY: 'auto',
      height: '100%',
      ...sx
    }
  }, props), children);
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/external/panel-footer.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/external/panel-footer.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PanelFooter; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function PanelFooter({
  children,
  sx,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, _extends({
    component: "footer",
    sx: {
      display: 'flex',
      position: 'sticky',
      bottom: 0,
      px: 2,
      py: 1.5
    }
  }, props), children));
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/external/panel-header-title.tsx":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/external/panel-header-title.tsx ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




// This is to override Editor reset.scss that overrides eui styles
const Typography = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography)(({
  theme,
  variant = 'body1'
}) => {
  if (variant === 'inherit') {
    return {};
  }
  return {
    '&.MuiTypography-root': {
      ...theme.typography[variant]
    }
  };
});
const PanelHeaderTitle = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  children,
  ...props
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Typography, _extends({
    ref: ref,
    component: "h2",
    variant: "subtitle1"
  }, props), children);
});
/* harmony default export */ __webpack_exports__["default"] = (PanelHeaderTitle);

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/external/panel-header.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/external/panel-header.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PanelHeader; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const Header = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  height: theme?.spacing(6) || '48px',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  gap: theme?.spacing(0.5) || '4px'
}));
function PanelHeader({
  children,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Header, _extends({
    component: "header"
  }, props), children));
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/external/panel.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/external/panel.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Panel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function Panel({
  children,
  sx,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Drawer, _extends({
    open: true,
    variant: "persistent",
    anchor: "left",
    PaperProps: {
      sx: {
        position: 'relative',
        width: '100%',
        bgcolor: 'background.default',
        border: 'none'
      }
    },
    sx: {
      height: '100%',
      ...sx
    }
  }, props), children);
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/internal/panels.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/internal/panels.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Panels; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_open_panel_injection__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../hooks/use-open-panel-injection */ "./packages/packages/core/editor-panels/src/hooks/use-open-panel-injection.ts");
/* harmony import */ var _portal__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./portal */ "./packages/packages/core/editor-panels/src/components/internal/portal.tsx");



function Panels() {
  const openPanel = (0,_hooks_use_open_panel_injection__WEBPACK_IMPORTED_MODULE_1__["default"])();
  const Component = openPanel?.component ?? null;
  if (!Component) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_portal__WEBPACK_IMPORTED_MODULE_2__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Component, null));
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/components/internal/portal.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/components/internal/portal.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Portal; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _sync__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../sync */ "./packages/packages/core/editor-panels/src/sync.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




function Portal(props) {
  const containerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(_sync__WEBPACK_IMPORTED_MODULE_2__.getPortalContainer);
  if (!containerRef.current) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Portal, _extends({
    container: containerRef.current
  }, props));
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/hooks/use-open-panel-injection.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/hooks/use-open-panel-injection.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useOpenPanelInjection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _location__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../location */ "./packages/packages/core/editor-panels/src/location.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-panels/src/store/index.ts");




function useOpenPanelInjection() {
  const injections = (0,_location__WEBPACK_IMPORTED_MODULE_2__.usePanelsInjections)();
  const openId = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_3__.selectOpenId);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => injections.find(injection => openId === injection.id), [injections, openId]);
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/init.ts":
/*!**********************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/init.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_internal_panels__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/internal/panels */ "./packages/packages/core/editor-panels/src/components/internal/panels.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-panels/src/store/index.ts");
/* harmony import */ var _sync__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./sync */ "./packages/packages/core/editor-panels/src/sync.ts");





function init() {
  (0,_sync__WEBPACK_IMPORTED_MODULE_4__.sync)();
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__registerSlice)(_store__WEBPACK_IMPORTED_MODULE_3__.slice);
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'panels',
    component: _components_internal_panels__WEBPACK_IMPORTED_MODULE_2__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/location.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/location.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   injectIntoPanels: function() { return /* binding */ injectIntoPanels; },
/* harmony export */   usePanelsInjections: function() { return /* binding */ usePanelsInjections; }
/* harmony export */ });
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_0__);

const {
  inject: injectIntoPanels,
  useInjections: usePanelsInjections
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/store/index.ts":
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/store/index.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   selectOpenId: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_0__.selectOpenId; },
/* harmony export */   slice: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_1__["default"]; }
/* harmony export */ });
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./selectors */ "./packages/packages/core/editor-panels/src/store/selectors.ts");
/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./slice */ "./packages/packages/core/editor-panels/src/store/slice.ts");



/***/ }),

/***/ "./packages/packages/core/editor-panels/src/store/selectors.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/store/selectors.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   selectOpenId: function() { return /* binding */ selectOpenId; }
/* harmony export */ });
const selectOpenId = state => state.panels.openId;

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/store/slice.ts":
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/store/slice.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);

const initialState = {
  openId: null
};
/* harmony default export */ __webpack_exports__["default"] = ((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSlice)({
  name: 'panels',
  initialState,
  reducers: {
    open(state, action) {
      state.openId = action.payload;
    },
    close(state, action) {
      if (!action.payload || state.openId === action.payload) {
        state.openId = null;
      }
    }
  }
}));

/***/ }),

/***/ "./packages/packages/core/editor-panels/src/sync.ts":
/*!**********************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/sync.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   V2_PANEL: function() { return /* binding */ V2_PANEL; },
/* harmony export */   getPortalContainer: function() { return /* binding */ getPortalContainer; },
/* harmony export */   sync: function() { return /* binding */ sync; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-panels/src/store/index.ts");



const V2_PANEL = 'panel/v2';
function getPortalContainer() {
  return document.querySelector('#elementor-panel-inner');
}
function sync() {
  // Register the V2 panel route on panel init.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/panel/init'), () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRegisterRoute)(V2_PANEL));

  // On empty route open, hide V1 panel elements.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent)(V2_PANEL), () => {
    getV1PanelElements().forEach(el => {
      el.setAttribute('hidden', 'hidden');
      el.setAttribute('inert', 'true');
    });
  });

  // On empty route close, close the V2 panel.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeCloseEvent)(V2_PANEL), () => (0,_store__WEBPACK_IMPORTED_MODULE_2__.selectOpenId)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__getState)()) && (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_2__.slice.actions.close()));

  // On empty route close, show V1 panel elements.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeCloseEvent)(V2_PANEL), () => {
    getV1PanelElements().forEach(el => {
      el.removeAttribute('hidden');
      el.removeAttribute('inert');
    });
  });

  // On V2 panel open, open the V2 panel route.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/panel/init'), () => subscribe({
    on: state => (0,_store__WEBPACK_IMPORTED_MODULE_2__.selectOpenId)(state),
    when: ({
      prev,
      current
    }) => !!(!prev && current),
    // is panel opened
    callback: () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateOpenRoute)(V2_PANEL)
  }));

  // On V2 panel close, close the V2 panel route.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/panel/init'), () => subscribe({
    on: state => (0,_store__WEBPACK_IMPORTED_MODULE_2__.selectOpenId)(state),
    when: ({
      prev,
      current
    }) => !!(!current && prev),
    // is panel closed
    callback: () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateIsRouteActive)(V2_PANEL) && (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateOpenRoute)(getDefaultRoute())
  }));
}
function getV1PanelElements() {
  const v1ElementsSelector = ['#elementor-panel-header-wrapper', '#elementor-panel-content-wrapper', '#elementor-panel-state-loading', '#elementor-panel-footer'].join(', ');
  return document.querySelectorAll(v1ElementsSelector);
}
function getDefaultRoute() {
  const defaultRoute = window?.elementor?.documents?.getCurrent?.()?.config?.panel?.default_route;
  return defaultRoute || 'panel/elements/categories';
}
function subscribe({
  on,
  when,
  callback
}) {
  let prev;
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__subscribe)(() => {
    const current = on((0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__getState)());
    if (when({
      prev,
      current
    })) {
      callback({
        prev,
        current
      });
    }
    prev = current;
  });
}

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/locations":
/*!********************************************!*\
  !*** external ["elementorV2","locations"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["locations"];

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
/*!***********************************************************!*\
  !*** ./packages/packages/core/editor-panels/src/index.ts ***!
  \***********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Panel: function() { return /* reexport safe */ _components_external__WEBPACK_IMPORTED_MODULE_2__.Panel; },
/* harmony export */   PanelBody: function() { return /* reexport safe */ _components_external__WEBPACK_IMPORTED_MODULE_2__.PanelBody; },
/* harmony export */   PanelFooter: function() { return /* reexport safe */ _components_external__WEBPACK_IMPORTED_MODULE_2__.PanelFooter; },
/* harmony export */   PanelHeader: function() { return /* reexport safe */ _components_external__WEBPACK_IMPORTED_MODULE_2__.PanelHeader; },
/* harmony export */   PanelHeaderTitle: function() { return /* reexport safe */ _components_external__WEBPACK_IMPORTED_MODULE_2__.PanelHeaderTitle; },
/* harmony export */   __createPanel: function() { return /* reexport safe */ _api__WEBPACK_IMPORTED_MODULE_1__.createPanel; },
/* harmony export */   __registerPanel: function() { return /* reexport safe */ _api__WEBPACK_IMPORTED_MODULE_1__.registerPanel; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-panels/src/init.ts");
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-panels/src/api.ts");
/* harmony import */ var _components_external__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/external */ "./packages/packages/core/editor-panels/src/components/external/index.ts");



}();
(window.elementorV2 = window.elementorV2 || {}).editorPanels = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorPanels?.init?.();
//# sourceMappingURL=editor-panels.js.map
/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-app-bar/src/components/actions/action.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/actions/action.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Action; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_menu_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../contexts/menu-context */ "./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx");
/* harmony import */ var _ui_popover_menu_item__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../ui/popover-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu-item.tsx");
/* harmony import */ var _ui_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ui/toolbar-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-item.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




function Action({
  icon: Icon,
  title,
  visible = true,
  ...props
}) {
  const {
    type
  } = (0,_contexts_menu_context__WEBPACK_IMPORTED_MODULE_1__.useMenuContext)();
  if (!visible) {
    return null;
  }
  return type === 'toolbar' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_3__["default"], _extends({
    title: title
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_popover_menu_item__WEBPACK_IMPORTED_MODULE_2__["default"], _extends({}, props, {
    text: title,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/actions/link.tsx":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/actions/link.tsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Link; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_menu_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../contexts/menu-context */ "./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx");
/* harmony import */ var _ui_popover_menu_item__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../ui/popover-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu-item.tsx");
/* harmony import */ var _ui_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ui/toolbar-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-item.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




function Link({
  icon: Icon,
  title,
  visible = true,
  showExternalLinkIcon = false,
  ...props
}) {
  const {
    type
  } = (0,_contexts_menu_context__WEBPACK_IMPORTED_MODULE_1__.useMenuContext)();
  if (!visible) {
    return null;
  }
  return type === 'toolbar' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_3__["default"], _extends({
    title: title
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_popover_menu_item__WEBPACK_IMPORTED_MODULE_2__["default"], _extends({}, props, {
    text: title,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null),
    showExternalLinkIcon: showExternalLinkIcon
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/actions/toggle-action.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/actions/toggle-action.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToggleAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_menu_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../contexts/menu-context */ "./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx");
/* harmony import */ var _ui_popover_menu_item__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../ui/popover-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu-item.tsx");
/* harmony import */ var _ui_toolbar_menu_toggle_item__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ui/toolbar-menu-toggle-item */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-toggle-item.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




function ToggleAction({
  icon: Icon,
  title,
  value,
  visible = true,
  ...props
}) {
  const {
    type
  } = (0,_contexts_menu_context__WEBPACK_IMPORTED_MODULE_1__.useMenuContext)();
  if (!visible) {
    return null;
  }
  return type === 'toolbar' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu_toggle_item__WEBPACK_IMPORTED_MODULE_3__["default"], _extends({
    value: value || title,
    title: title
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_popover_menu_item__WEBPACK_IMPORTED_MODULE_2__["default"], _extends({}, props, {
    text: title,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/app-bar.tsx":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/app-bar.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ AppBar; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _locations_main_menu_location__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./locations/main-menu-location */ "./packages/packages/core/editor-app-bar/src/components/locations/main-menu-location.tsx");
/* harmony import */ var _locations_page_indication_location__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./locations/page-indication-location */ "./packages/packages/core/editor-app-bar/src/components/locations/page-indication-location.tsx");
/* harmony import */ var _locations_primary_action_location__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./locations/primary-action-location */ "./packages/packages/core/editor-app-bar/src/components/locations/primary-action-location.tsx");
/* harmony import */ var _locations_responsive_location__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./locations/responsive-location */ "./packages/packages/core/editor-app-bar/src/components/locations/responsive-location.tsx");
/* harmony import */ var _locations_tools_menu_location__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./locations/tools-menu-location */ "./packages/packages/core/editor-app-bar/src/components/locations/tools-menu-location.tsx");
/* harmony import */ var _locations_utilities_menu_location__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./locations/utilities-menu-location */ "./packages/packages/core/editor-app-bar/src/components/locations/utilities-menu-location.tsx");
/* harmony import */ var _ui_toolbar_menu__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./ui/toolbar-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu.tsx");










function AppBar() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ThemeProvider, {
    colorScheme: "dark"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AppBar, {
    position: "sticky"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Toolbar, {
    disableGutters: true,
    variant: "dense"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    display: "grid",
    gridTemplateColumns: "repeat(3, 1fr)",
    flexGrow: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    flexWrap: "nowrap"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations_main_menu_location__WEBPACK_IMPORTED_MODULE_3__["default"], null), document?.permissions?.allowAddingWidgets && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations_tools_menu_location__WEBPACK_IMPORTED_MODULE_7__["default"], null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    justifyContent: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu__WEBPACK_IMPORTED_MODULE_9__["default"], {
    spacing: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, {
    orientation: "vertical"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations_page_indication_location__WEBPACK_IMPORTED_MODULE_4__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, {
    orientation: "vertical"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations_responsive_location__WEBPACK_IMPORTED_MODULE_6__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, {
    orientation: "vertical"
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    justifyContent: "flex-end",
    flexWrap: "nowrap"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations_utilities_menu_location__WEBPACK_IMPORTED_MODULE_8__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations_primary_action_location__WEBPACK_IMPORTED_MODULE_5__["default"], null))))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/integrations-menu-location.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/integrations-menu-location.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ IntegrationsMenuLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _ui_popover_menu__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../ui/popover-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu.tsx");
/* harmony import */ var _ui_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../ui/toolbar-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-item.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }







const {
  useMenuItems
} = _locations__WEBPACK_IMPORTED_MODULE_4__.integrationsMenu;
function IntegrationsMenuLocation() {
  const menuItems = useMenuItems();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.usePopupState)({
    variant: 'popover',
    popupId: 'elementor-v2-app-bar-integrations'
  });
  if (menuItems.default.length === 0) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_6__["default"], _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindTrigger)(popupState), {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Integrations', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PlugIcon, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_popover_menu__WEBPACK_IMPORTED_MODULE_5__["default"], _extends({
    onClick: popupState.close
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindMenu)(popupState), {
    marginThreshold: 8,
    open: popupState.isOpen
  }), menuItems.default.map(({
    MenuItem: IntegrationsMenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(IntegrationsMenuItem, {
    key: id
  }))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/main-menu-location.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/main-menu-location.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ MainMenuLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _ui_popover_menu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ui/popover-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu.tsx");
/* harmony import */ var _ui_toolbar_logo__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../ui/toolbar-logo */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-logo.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





const {
  useMenuItems
} = _locations__WEBPACK_IMPORTED_MODULE_2__.mainMenu;
function MainMenuLocation() {
  const menuItems = useMenuItems();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.usePopupState)({
    variant: 'popover',
    popupId: 'elementor-v2-app-bar-main-menu'
  });
  const toolbarLogoProps = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.bindTrigger)(popupState);
  const onToolbarClick = e => {
    const extendedWindow = window;
    const config = extendedWindow?.elementorCommon?.eventsManager?.config;
    if (config) {
      extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.elementorLogoDropdown, {
        location: config.locations.topBar,
        secondaryLocation: config.secondaryLocations.eLogoMenu,
        trigger: config.triggers.dropdownClick,
        element: config.elements.buttonIcon
      });
    }
    toolbarLogoProps.onClick(e);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    sx: {
      paddingInlineStart: 3
    },
    direction: "row",
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_logo__WEBPACK_IMPORTED_MODULE_4__["default"], _extends({}, toolbarLogoProps, {
    onClick: onToolbarClick,
    selected: popupState.isOpen
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_popover_menu__WEBPACK_IMPORTED_MODULE_3__["default"], _extends({
    onClick: popupState.close
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.bindMenu)(popupState), {
    marginThreshold: 8
  }), menuItems.default.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, null), menuItems.help.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  })), menuItems.exits.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  }))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/page-indication-location.tsx":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/page-indication-location.tsx ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PageIndicationLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");


function PageIndicationLocation() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations__WEBPACK_IMPORTED_MODULE_1__.PageIndicationSlot, null);
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/primary-action-location.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/primary-action-location.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PrimaryActionLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");


function PrimaryActionLocation() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations__WEBPACK_IMPORTED_MODULE_1__.PrimaryActionSlot, null);
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/responsive-location.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/responsive-location.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ResponsiveLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");


function ResponsiveLocation() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_locations__WEBPACK_IMPORTED_MODULE_1__.ResponsiveSlot, null);
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/send-feedback-popup-location.tsx":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/send-feedback-popup-location.tsx ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ SendFeedbackPopupLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _extensions_feedback_feedback_consts__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../extensions/feedback/feedback-consts */ "./packages/packages/core/editor-app-bar/src/extensions/feedback/feedback-consts.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }










const checkIfUserIsConnected = () => {
  const extendedWindow = window;
  return extendedWindow?.elementorCommon?.config.library_connect.is_connected || extendedWindow?.elementorPro?.config.isActive;
};
function SendFeedbackPopupLocation() {
  const isActive = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.isExperimentActive)(_extensions_feedback_feedback_consts__WEBPACK_IMPORTED_MODULE_8__.EXPERIMENT_NAME);
  const extendedWindow = window;
  const [isUserConnected, setIsUserConnected] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(checkIfUserIsConnected());
  const connectUrl = extendedWindow?.elementor?.config.user.top_bar.connect_url;
  const [feedbackContent, setFeedbackContent] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [feedbackResult, setFeedbackResult] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [submitDisabled, setSubmitDisabled] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const {
    dispatchEvent: trackEvent = (...args) => void args
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_3__.useMixpanel)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.usePopupState)({
    variant: 'dialog',
    popupId: _extensions_feedback_feedback_consts__WEBPACK_IMPORTED_MODULE_8__.FEEDBACK_TOGGLE_EVENT
  });
  const [isFetching, setIsFetching] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handler = () => {
      popupState.toggle();
      // reason to re-check: clicking "connect to elementor" closes the dialog. At this time the user can perform connect, and the state might change externally.
      setIsUserConnected(checkIfUserIsConnected());
      setFeedbackResult(null);
      trackEvent('feedback_modal_opened', {
        source: 'top_bar',
        context: 'v4_beta'
      });
    };
    window.addEventListener(_extensions_feedback_feedback_consts__WEBPACK_IMPORTED_MODULE_8__.FEEDBACK_TOGGLE_EVENT, handler);
    return () => {
      window.removeEventListener(_extensions_feedback_feedback_consts__WEBPACK_IMPORTED_MODULE_8__.FEEDBACK_TOGGLE_EVENT, handler);
    };
  }, [popupState, trackEvent]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setSubmitDisabled(feedbackContent.trim().length < 10 || !isUserConnected || isFetching);
  }, [feedbackContent, feedbackResult, isUserConnected, isFetching]);
  const handleClose = () => {
    popupState.close();
    trackEvent('feedback_modal_closed', {
      feedback_text: feedbackContent
    });
  };
  const handleStartAnother = () => {
    setFeedbackContent('');
    setFeedbackResult(null);
  };
  const submitFeedback = () => {
    setIsFetching(true);
    (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_4__.httpService)().post('elementor/v1/feedback/submit', {
      description: feedbackContent.trim()
    }).then(response => {
      setFeedbackResult({
        message: response.data.message,
        success: response.data.success
      });
      // check if unauthorized - not signed in or expired, needs to reconnect to my-elementor account
      if (!response.data.success && response.data.code.toString() === '401' || response.data.code.toString() === '403') {
        setIsUserConnected(false);
      }
      trackEvent(response.data.success ? 'feedback_submitted' : 'feedback_error', {
        feedback_length: feedbackContent.length,
        error_type: response.data.success ? undefined : 'server',
        error_message: response.data.success ? undefined : response.data.message
      });
    }).finally(() => setIsFetching(false));
  };
  if (!isActive) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Popover, _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.bindDialog)(popupState), {
    onClose: () => handleClose()
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Dialog, {
    open: popupState.isOpen
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.DialogHeader, {
    style: {
      width: '100%',
      minWidth: '35rem'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.DialogTitle, {
    style: {
      width: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
    display: "flex",
    direction: "row",
    alignItems: "center",
    justifyContent: "space-between",
    width: "100%"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Submit Feedback', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.CloseButton, {
    onClick: popupState.close
  })))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.DialogContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
    direction: "column",
    gap: 2
  }, isUserConnected ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.TextField, {
    autofocus: true,
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('E.g. Can you add ABC features? I want to do ABC and it’s important because …', 'elementor'),
    fullwith: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Your Feedback', 'elementor'),
    multiline: true,
    id: "elementor-feedback-usercontent",
    rows: 6,
    cols: 80,
    disabled: isFetching || feedbackResult?.success,
    onChange: event => setFeedbackContent(event.target.value),
    value: feedbackContent
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
    direction: "row",
    justifyContent: "flex-end",
    alignItems: "center",
    gap: 2
  }, feedbackResult && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, feedbackResult.success ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__.CheckIcon, {
    color: "success"
  }) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__.AlertCircleIcon, {
    color: "error"
  }), feedbackResult.message), feedbackResult?.success ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Button, {
    variant: "text",
    onClick: () => handleStartAnother()
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Submit Another Feedback', 'elementor')) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Button, {
    disabled: submitDisabled,
    onClick: submitFeedback,
    variant: "contained",
    color: "primary",
    size: "small"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Submit', 'elementor')))) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Button, {
    variant: "contained",
    color: "primary",
    size: "large",
    href: connectUrl,
    target: "_blank",
    rel: "noopener",
    onClick: popupState.close
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Connect to Elementor', 'elementor'))))))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/tools-menu-location.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/tools-menu-location.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToolsMenuLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _extensions_angie_components_angie_guide_location__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../extensions/angie/components/angie-guide-location */ "./packages/packages/core/editor-app-bar/src/extensions/angie/components/angie-guide-location.tsx");
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _ui_toolbar_menu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ui/toolbar-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu.tsx");
/* harmony import */ var _ui_toolbar_menu_more__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../ui/toolbar-menu-more */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-more.tsx");
/* harmony import */ var _integrations_menu_location__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./integrations-menu-location */ "./packages/packages/core/editor-app-bar/src/components/locations/integrations-menu-location.tsx");
/* harmony import */ var _send_feedback_popup_location__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./send-feedback-popup-location */ "./packages/packages/core/editor-app-bar/src/components/locations/send-feedback-popup-location.tsx");







const MAX_TOOLBAR_ACTIONS = 5;
const {
  useMenuItems
} = _locations__WEBPACK_IMPORTED_MODULE_2__.toolsMenu;
function ToolsMenuLocation() {
  const menuItems = useMenuItems();
  const toolbarMenuItems = menuItems.default.slice(0, MAX_TOOLBAR_ACTIONS);
  const popoverMenuItems = menuItems.default.slice(MAX_TOOLBAR_ACTIONS);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu__WEBPACK_IMPORTED_MODULE_3__["default"], null, toolbarMenuItems.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_extensions_angie_components_angie_guide_location__WEBPACK_IMPORTED_MODULE_1__.AngieGuideLocation, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_send_feedback_popup_location__WEBPACK_IMPORTED_MODULE_6__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_integrations_menu_location__WEBPACK_IMPORTED_MODULE_5__["default"], null), popoverMenuItems.length > 0 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu_more__WEBPACK_IMPORTED_MODULE_4__["default"], {
    id: "elementor-editor-app-bar-tools-more"
  }, popoverMenuItems.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  }))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/locations/utilities-menu-location.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/locations/utilities-menu-location.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ UtilitiesMenuLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _ui_toolbar_menu__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../ui/toolbar-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu.tsx");
/* harmony import */ var _ui_toolbar_menu_more__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../ui/toolbar-menu-more */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-more.tsx");





const MAX_TOOLBAR_ACTIONS = 4;
const {
  useMenuItems
} = _locations__WEBPACK_IMPORTED_MODULE_1__.utilitiesMenu;
function UtilitiesMenuLocation() {
  const menuItems = useMenuItems();

  // If there are more than 5 items, show the first 4 inline and the rest in the popover.
  // Otherwise, display all items inline.
  const shouldUsePopover = menuItems.default.length > MAX_TOOLBAR_ACTIONS + 1;
  const toolbarMenuItems = shouldUsePopover ? menuItems.default.slice(0, MAX_TOOLBAR_ACTIONS) : menuItems.default;
  const popoverMenuItems = shouldUsePopover ? menuItems.default.slice(MAX_TOOLBAR_ACTIONS) : [];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu__WEBPACK_IMPORTED_MODULE_2__["default"], null, toolbarMenuItems.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: id
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, null))), popoverMenuItems.length > 0 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_toolbar_menu_more__WEBPACK_IMPORTED_MODULE_3__["default"], {
    id: "elementor-editor-app-bar-utilities-more"
  }, popoverMenuItems.map(({
    MenuItem,
    id
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  }))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu-item.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/popover-menu-item.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PopoverMenuItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const DirectionalArrowIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ArrowUpRightIcon);
const DirectionalChevronIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ChevronRightIcon);
function PopoverMenuItem({
  text,
  icon,
  onClick,
  href,
  target,
  disabled,
  isGroupParent,
  showExternalLinkIcon,
  ...props
}) {
  const isExternalLink = href && target === '_blank' && showExternalLinkIcon;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuItem, _extends({}, props, {
    disabled: disabled,
    onClick: onClick,
    component: href ? 'a' : 'div',
    href: href,
    target: target,
    sx: {
      '&:hover': {
        color: 'text.primary' // Overriding global CSS from the editor.
      }
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemIcon, null, icon), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemText, {
    primary: text
  }), isExternalLink && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DirectionalArrowIcon, null), isGroupParent && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DirectionalChevronIcon, null));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/popover-menu.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PopoverMenu; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_menu_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts/menu-context */ "./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



function PopoverMenu({
  children,
  popupState,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_menu_context__WEBPACK_IMPORTED_MODULE_2__.MenuContextProvider, {
    type: 'popover',
    popupState: popupState
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Menu, _extends({
    PaperProps: {
      sx: {
        mt: 1.5
      }
    }
  }, props, {
    MenuListProps: {
      component: 'div',
      dense: true
    }
  }), children));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-logo.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/toolbar-logo.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToolbarLogo; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const ElementorLogo = props => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("g", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "16",
    cy: "16",
    r: "16"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M11.7 9H9V22.3H11.7V9Z"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22.4 9H9V11.7H22.4V9Z"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22.4 14.4004H9V17.1004H22.4V14.4004Z"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22.4 19.6992H9V22.3992H22.4V19.6992Z"
  })));
};
const StyledToggleButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ToggleButton)(({
  theme
}) => ({
  padding: 0,
  border: 0,
  color: theme.palette.text.primary,
  '&.MuiToggleButton-root:hover': {
    backgroundColor: 'initial'
  },
  '&.MuiToggleButton-root.Mui-selected': {
    backgroundColor: 'initial'
  }
}));
const StyledElementorLogo = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(ElementorLogo, {
  shouldForwardProp: prop => prop !== 'showMenuIcon'
})(({
  theme,
  showMenuIcon
}) => ({
  '& path': {
    fill: theme.palette.background.default,
    transition: 'all 0.2s linear',
    transformOrigin: 'bottom left',
    '&:first-of-type': {
      transitionDelay: !showMenuIcon && '0.2s',
      transform: showMenuIcon && 'translateY(-9px) scaleY(0)'
    },
    '&:not(:first-of-type)': {
      // Emotion automatically change 4 to -4 in RTL mode.
      transform: !showMenuIcon && `translateX(${theme.direction === 'rtl' ? '4' : '9'}px) scaleX(0.6)`
    },
    '&:nth-of-type(2)': {
      transitionDelay: showMenuIcon ? '0' : '0.2s'
    },
    '&:nth-of-type(3)': {
      transitionDelay: '0.1s'
    },
    '&:nth-of-type(4)': {
      transitionDelay: showMenuIcon ? '0.2s' : '0'
    }
  }
}));
function ToolbarLogo(props) {
  const [isHoverState, setIsHoverState] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const showMenuIcon = props.selected || isHoverState;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledToggleButton, _extends({}, props, {
    value: "selected",
    size: "large",
    onMouseEnter: () => setIsHoverState(true),
    onMouseLeave: () => setIsHoverState(false)
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledElementorLogo, {
    fontSize: "large",
    showMenuIcon: showMenuIcon,
    titleAccess: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Elementor Logo', 'elementor')
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-item.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-item.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToolbarMenuItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function ToolbarMenuItem({
  title,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Tooltip, {
    title: title
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    component: "span",
    "aria-label": undefined
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.IconButton, _extends({}, props, {
    "aria-label": title,
    size: "medium",
    sx: {
      '& svg': {
        fontSize: '1.25rem',
        height: '1em',
        width: '1em'
      },
      '&:hover': {
        color: 'text.primary'
      }
    }
  }))));
}
function Tooltip(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, _extends({
    PopperProps: {
      sx: {
        '&.MuiTooltip-popper .MuiTooltip-tooltip.MuiTooltip-tooltipPlacementBottom': {
          mt: 2
        }
      }
    }
  }, props));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-more.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-more.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToolbarMenuMore; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _popover_menu__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./popover-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu.tsx");
/* harmony import */ var _toolbar_menu_item__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./toolbar-menu-item */ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-item.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }






function ToolbarMenuMore({
  children,
  id
}) {
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.usePopupState)({
    variant: 'popover',
    popupId: id
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_toolbar_menu_item__WEBPACK_IMPORTED_MODULE_5__["default"], _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindTrigger)(popupState), {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('More', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.DotsVerticalIcon, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_popover_menu__WEBPACK_IMPORTED_MODULE_4__["default"], _extends({
    onClick: popupState.close
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindMenu)(popupState)), children));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-toggle-item.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu-toggle-item.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToolbarMenuToggleItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function ToolbarMenuToggleItem({
  title,
  onClick,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    title: title
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    component: "span",
    "aria-label": undefined
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ToggleButton, _extends({}, props, {
    onChange: onClick,
    "aria-label": title,
    size: "small",
    sx: {
      border: 0,
      // Temp fix until the style of the ToggleButton component will be decided.
      '&.Mui-disabled': {
        border: 0 // Temp fix until the style of the ToggleButton component will be decided.
      },
      '& svg': {
        fontSize: '1.25rem',
        height: '1em',
        width: '1em'
      }
    }
  }))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/components/ui/toolbar-menu.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ToolbarMenu; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_menu_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts/menu-context */ "./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



function ToolbarMenu({
  children,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_menu_context__WEBPACK_IMPORTED_MODULE_2__.MenuContextProvider, {
    type: 'toolbar'
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, _extends({
    sx: {
      px: 1.5
    },
    spacing: 1.5,
    direction: "row",
    alignItems: "center"
  }, props), children));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/contexts/menu-context.tsx ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MenuContextProvider: function() { return /* binding */ MenuContextProvider; },
/* harmony export */   useMenuContext: function() { return /* binding */ useMenuContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const MenuContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)({
  type: 'toolbar'
});
function MenuContextProvider({
  type,
  popupState,
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuContext.Provider, {
    value: {
      type,
      popupState
    }
  }, children);
}
function useMenuContext() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(MenuContext);
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/angie/angie-consts.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/angie/angie-consts.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AI_WIDGET_CTA_VIEWED_EVENT: function() { return /* binding */ AI_WIDGET_CTA_VIEWED_EVENT; },
/* harmony export */   ANGIE_BUTTON_ARIA_LABEL: function() { return /* binding */ ANGIE_BUTTON_ARIA_LABEL; },
/* harmony export */   ANGIE_DESCRIPTION: function() { return /* binding */ ANGIE_DESCRIPTION; },
/* harmony export */   ANGIE_GUIDE_TOGGLE_EVENT: function() { return /* binding */ ANGIE_GUIDE_TOGGLE_EVENT; },
/* harmony export */   ANGIE_LEARN_MORE_URL: function() { return /* binding */ ANGIE_LEARN_MORE_URL; },
/* harmony export */   ANGIE_PROMOTION_IMAGE_URL: function() { return /* binding */ ANGIE_PROMOTION_IMAGE_URL; },
/* harmony export */   ANGIE_TOP_BAR_DESCRIPTION: function() { return /* binding */ ANGIE_TOP_BAR_DESCRIPTION; },
/* harmony export */   ANGIE_TOP_BAR_PROMOTION_IMAGE_URL: function() { return /* binding */ ANGIE_TOP_BAR_PROMOTION_IMAGE_URL; },
/* harmony export */   CREATE_WIDGET_EVENT: function() { return /* binding */ CREATE_WIDGET_EVENT; }
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);

const ANGIE_GUIDE_TOGGLE_EVENT = 'elementor/editor/toggle-angie-guide';
const CREATE_WIDGET_EVENT = 'elementor/editor/create-widget';
const ANGIE_BUTTON_ARIA_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Angie', 'elementor');
const ANGIE_PROMOTION_IMAGE_URL = 'https://assets.elementor.com/packages/v1/images/angie-promotion.svg';
const ANGIE_LEARN_MORE_URL = 'https://go.elementor.com/angie-learn-more';
const ANGIE_DESCRIPTION = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Angie lets you generate custom widgets, sections, and code using simple instructions.', 'elementor');
const AI_WIDGET_CTA_VIEWED_EVENT = 'ai_widget_cta_viewed';
const ANGIE_TOP_BAR_PROMOTION_IMAGE_URL = 'https://assets.elementor.com/packages/v1/images/angie-top-bar-promotion.svg';
const ANGIE_TOP_BAR_DESCRIPTION = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Build custom widgets using simple instructions.', 'elementor');

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/angie/components/angie-guide-card.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/angie/components/angie-guide-card.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AngieGuideCard: function() { return /* binding */ AngieGuideCard; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function AngieGuideCard({
  imageUrl,
  description,
  learnMoreUrl,
  onInstall,
  onClose
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ClickAwayListener, {
    onClickAway: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    sx: {
      width: 296
    },
    "data-testid": "e-angie-guide-card"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    alignItems: "center",
    gap: 1,
    py: 1,
    px: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "subtitle2"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Meet Angie', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Chip, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('New', 'elementor'),
    size: "small",
    color: "info",
    variant: "standard"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CloseButton, {
    edge: "end",
    sx: {
      ml: 'auto'
    },
    slotProps: {
      icon: {
        fontSize: 'small'
      }
    },
    onClick: onClose
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Image, {
    src: imageUrl,
    alt: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Angie', 'elementor'),
    sx: {
      height: 150,
      width: '100%'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    px: 2,
    pt: 1.5,
    pb: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    color: "secondary"
  }, description)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    justifyContent: "flex-end",
    gap: 1,
    pt: 1,
    pb: 1.5,
    px: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "text",
    size: "small",
    color: "secondary",
    onClick: () => {
      window.open(learnMoreUrl, '_blank', 'noopener,noreferrer');
      onClose();
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Learn More', 'elementor')), onInstall && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "contained",
    size: "small",
    color: "accent",
    onClick: onInstall
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Try for free', 'elementor')))));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/angie/components/angie-guide-location.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/angie/components/angie-guide-location.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AngieGuideLocation: function() { return /* binding */ AngieGuideLocation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _angie_consts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../angie-consts */ "./packages/packages/core/editor-app-bar/src/extensions/angie/angie-consts.ts");
/* harmony import */ var _components_angie_guide_card__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../components/angie-guide-card */ "./packages/packages/core/editor-app-bar/src/extensions/angie/components/angie-guide-card.tsx");
/* harmony import */ var _hooks_use_auto_show__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-auto-show */ "./packages/packages/core/editor-app-bar/src/extensions/angie/hooks/use-auto-show.ts");









function AngieGuideLocation() {
  (0,_hooks_use_auto_show__WEBPACK_IMPORTED_MODULE_7__.useAutoShow)();
  const [anchorEl, setAnchorEl] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const {
    dispatchEvent
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_3__.useMixpanel)();
  const {
    isAdmin
  } = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useCurrentUserCapabilities)();
  const isOpen = Boolean(anchorEl);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleToggle = () => {
      setAnchorEl(prev => {
        if (prev) {
          return null;
        }
        return document.querySelector(`[aria-label="${_angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_BUTTON_ARIA_LABEL}"]`);
      });
    };
    window.addEventListener(_angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_GUIDE_TOGGLE_EVENT, handleToggle);
    return () => {
      window.removeEventListener(_angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_GUIDE_TOGGLE_EVENT, handleToggle);
    };
  }, []);
  const handleClose = () => setAnchorEl(null);
  const handleInstall = async () => {
    dispatchEvent?.(_angie_consts__WEBPACK_IMPORTED_MODULE_5__.AI_WIDGET_CTA_VIEWED_EVENT, {
      entry_point: 'top_bar_icon'
    });
    window.dispatchEvent(new CustomEvent(_angie_consts__WEBPACK_IMPORTED_MODULE_5__.CREATE_WIDGET_EVENT, {
      detail: {
        entry_point: 'top_bar_icon'
      }
    }));
    handleClose();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Infotip, {
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_angie_guide_card__WEBPACK_IMPORTED_MODULE_6__.AngieGuideCard, {
      imageUrl: _angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_TOP_BAR_PROMOTION_IMAGE_URL,
      description: _angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_TOP_BAR_DESCRIPTION,
      learnMoreUrl: _angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_LEARN_MORE_URL,
      onInstall: isAdmin ? handleInstall : undefined,
      onClose: handleClose
    }),
    placement: "bottom-start",
    open: isOpen,
    disableHoverListener: true,
    PopperProps: {
      anchorEl,
      modifiers: [{
        name: 'offset',
        options: {
          offset: [-4, -4]
        }
      }]
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null)));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/angie/hooks/use-action-props.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/angie/hooks/use-action-props.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useActionProps: function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _angie_consts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../angie-consts */ "./packages/packages/core/editor-app-bar/src/extensions/angie/angie-consts.ts");






function useActionProps() {
  const hasAngieInstalled = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.isAngieAvailable)();
  const visible = !hasAngieInstalled;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!visible) {
      return;
    }
    (0,_elementor_events__WEBPACK_IMPORTED_MODULE_2__.trackEvent)({
      eventName: _angie_consts__WEBPACK_IMPORTED_MODULE_5__.AI_WIDGET_CTA_VIEWED_EVENT,
      entry_point: 'top_bar_icon',
      has_angie_installed: false
    });
  }, [visible]);
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Angie', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_3__.AngieIcon,
    onClick: () => {
      window.dispatchEvent(new CustomEvent(_angie_consts__WEBPACK_IMPORTED_MODULE_5__.ANGIE_GUIDE_TOGGLE_EVENT));
    },
    selected: false,
    visible
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/angie/hooks/use-auto-show.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/angie/hooks/use-auto-show.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useAutoShow: function() { return /* binding */ useAutoShow; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _angie_consts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../angie-consts */ "./packages/packages/core/editor-app-bar/src/extensions/angie/angie-consts.ts");


function useAutoShow() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!window.elementor?.config?.angie?.autoShow) {
      return;
    }
    const id = setTimeout(() => {
      window.dispatchEvent(new CustomEvent(_angie_consts__WEBPACK_IMPORTED_MODULE_1__.ANGIE_GUIDE_TOGGLE_EVENT));
    }, 0);
    return () => clearTimeout(id);
  }, []);
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/angie/index.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/angie/index.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/angie/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.toolsMenu.registerToggleAction({
    id: 'toggle-angie',
    priority: 2,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__.useActionProps
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/connect/hooks/use-connect-link-config.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/connect/hooks/use-connect-link-config.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useConnectLinkConfig; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const dispatchConnectClickEvent = eventName => {
  try {
    const extendedWindow = window;
    const config = extendedWindow?.elementorCommon?.eventsManager?.config;
    if (config) {
      extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar[eventName], {
        location: config.locations.topBar,
        secondaryLocation: config.secondaryLocations.eLogoMenu,
        trigger: config.triggers.dropdownClick,
        element: config.elements.buttonIcon
      });
    }
  } catch (error) {
    // eslint-disable-next-line no-console
    console.warn(error);
  }
};
function useConnectLinkConfig() {
  const extendedWindow = window;
  let isUserConnected = false;
  const isPro = extendedWindow?.elementor?.helpers.hasPro();
  let target = '_blank';
  if (isPro) {
    isUserConnected = extendedWindow?.elementorPro?.config.isActive ?? false;
  } else {
    isUserConnected = extendedWindow?.elementorCommon?.config.library_connect.is_connected ?? false;
    target = '_self';
  }
  const handleConnectClick = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(event => {
    event.preventDefault();
    if (extendedWindow.jQuery && extendedWindow.jQuery.fn?.elementorConnect) {
      const connectUrl = extendedWindow?.elementor?.config.user.top_bar.connect_url;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const $tempButton = extendedWindow.jQuery('<a>');
      $tempButton?.attr('href', connectUrl)?.attr('target', '_blank')?.attr('rel', 'opener')?.css('display', 'none')?.appendTo('body');
      $tempButton.elementorConnect({
        success: () => {
          dispatchConnectClickEvent('accountConnected');
          setTimeout(() => {
            extendedWindow.location.reload();
          }, 200);
        }
      });
      $tempButton[0].click();
      dispatchConnectClickEvent('connectAccount');
      setTimeout(() => {
        $tempButton.remove();
      }, 1000);
    }
  }, [extendedWindow]);
  return isUserConnected ? {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('My Elementor', 'elementor'),
    href: extendedWindow?.elementor?.config.user.top_bar.my_elementor_url,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.UserIcon,
    target: '_blank'
  } : {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Connect my account', 'elementor'),
    href: extendedWindow?.elementor?.config.user.top_bar.connect_url,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.UserIcon,
    target,
    onClick: handleConnectClick
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/connect/index.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/connect/index.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_connect_link_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-connect-link-config */ "./packages/packages/core/editor-app-bar/src/extensions/connect/hooks/use-connect-link-config.tsx");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.mainMenu.registerLink({
    id: 'app-bar-connect',
    group: 'exits',
    priority: 10,
    useProps: _hooks_use_connect_link_config__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-preview/hooks/use-action-props.ts":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-preview/hooks/use-action-props.ts ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function useActionProps() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocument)();
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.EyeIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Preview Changes', 'elementor'),
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.previewPage, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations['preview-page'],
          trigger: config.triggers.click,
          element: config.elements.buttonIcon
        });
      }
      if (document) {
        (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommand)('editor/documents/preview', {
          id: document.id,
          force: true
        });
      }
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-preview/index.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-preview/index.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/documents-preview/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.utilitiesMenu.registerAction({
    id: 'document-preview-button',
    priority: 30,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/components/primary-action-menu.tsx":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/components/primary-action-menu.tsx ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PrimaryActionMenu; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_ui_popover_menu__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../components/ui/popover-menu */ "./packages/packages/core/editor-app-bar/src/components/ui/popover-menu.tsx");
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../locations */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/locations.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const {
  useMenuItems
} = _locations__WEBPACK_IMPORTED_MODULE_3__.documentOptionsMenu;

// CSS hack to hide dividers for empty menu items, due to a limitation in the locations' mechanism.
const StyledPopoverMenu = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)((0,_components_ui_popover_menu__WEBPACK_IMPORTED_MODULE_2__["default"]))`
	& > .MuiPopover-paper > .MuiList-root {
		& > .MuiDivider-root {
			display: none;
		}

		& > *:not( .MuiDivider-root ):not( :last-of-type ) + .MuiDivider-root {
			display: block;
		}
	}
`;
function PrimaryActionMenu(props) {
  const {
    save: saveActions,
    default: defaultActions
  } = useMenuItems();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledPopoverMenu, _extends({}, props, {
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'right'
    },
    marginThreshold: 4,
    PaperProps: {
      sx: {
        mt: 0.5
      }
    }
  }), saveActions.map(({
    MenuItem,
    id
  }, index) => [index > 0 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, {
    key: `${id}-divider`
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  })]), saveActions.length > 0 && defaultActions.length > 0 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, null), defaultActions.map(({
    MenuItem,
    id
  }, index) => [index > 0 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, {
    key: `${id}-divider`
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuItem, {
    key: id
  })]));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/components/primary-action.tsx":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/components/primary-action.tsx ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PrimaryAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _primary_action_menu__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./primary-action-menu */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/components/primary-action-menu.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }







function PrimaryAction() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const {
    save
  } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocumentActions)();
  const editMode = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.useEditMode)();
  const isEditMode = editMode === 'edit';
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.usePopupState)({
    variant: 'popover',
    popupId: 'document-save-options'
  });
  if (!document) {
    return null;
  }
  const isPublishDisabled = !isEditMode || !isPublishEnabled(document);
  const isSaveOptionsDisabled = !isEditMode || document.type.value === 'kit';

  // When the document is being saved, the spinner should not appear.
  // Usually happens when the Kit is being saved.
  const shouldShowSpinner = document.isSaving && !isPublishDisabled;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.ButtonGroup, {
    size: "large",
    variant: "contained"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Button, {
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.publishButton, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations['publish-button'],
          trigger: config.triggers.click,
          element: config.elements.mainCta
        });
      }
      if (!document.isSaving) {
        save();
      }
    },
    sx: {
      height: '100%',
      borderRadius: 0,
      maxWidth: '158px',
      '&.MuiButtonBase-root.MuiButtonGroup-grouped': {
        minWidth: '110px'
      }
    },
    disabled: isPublishDisabled
  }, shouldShowSpinner ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.CircularProgress, {
    color: "inherit",
    size: "1.5em"
  }) : getLabel(document)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Save Options', 'elementor'),
    PopperProps: {
      sx: {
        '&.MuiTooltip-popper .MuiTooltip-tooltip.MuiTooltip-tooltipPlacementBottom': {
          mt: 1,
          mr: 0.25
        }
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, {
    component: "span",
    "aria-label": undefined
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Button, _extends({
    size: "small"
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindTrigger)(popupState), {
    sx: {
      px: 0,
      height: '100%',
      borderRadius: 0
    },
    disabled: isSaveOptionsDisabled,
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Save Options', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ChevronDownIcon, null))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_primary_action_menu__WEBPACK_IMPORTED_MODULE_6__["default"], _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindMenu)(popupState), {
    onClick: popupState.close
  })));
}
function getLabel(document) {
  return document.userCan.publish ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Publish', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Submit', 'elementor');
}
function isPublishEnabled(document) {
  if (document.type.value === 'kit') {
    return false;
  }
  return document.isDirty || document.status.value === 'draft';
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-copy-and-share-props.ts":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-copy-and-share-props.ts ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useDocumentCopyAndShareProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function useDocumentCopyAndShareProps() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocument)();
  const {
    copyAndShare
  } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocumentActions)();
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_1__.useMixpanel)();
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LinkIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Copy and Share', 'elementor'),
    onClick: () => {
      const eventName = config?.names?.editorOne?.topBarPublishDropdown;
      if (eventName) {
        dispatchEvent?.(eventName, {
          app_type: config?.appTypes?.editor,
          window_name: config?.appTypes?.editor,
          interaction_type: config?.triggers?.click?.toLowerCase(),
          target_type: config?.targetTypes?.dropdownItem,
          target_name: config?.targetNames?.publishDropdown?.copyAndShare,
          interaction_result: config?.interactionResults?.actionSelected,
          target_location: config?.locations?.topBar?.replace(/\s+/g, '_').toLowerCase(),
          location_l1: config?.secondaryLocations?.publishDropdown?.replace(/\s+/g, '_').toLowerCase(),
          location_l2: config?.targetTypes?.dropdownItem
        });
      }
      copyAndShare();
    },
    disabled: !document || document.isSaving || document.isSavingDraft || !('publish' === document.status.value),
    visible: document?.permissions?.showCopyAndShare
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-save-draft-props.ts":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-save-draft-props.ts ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useDocumentSaveDraftProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function useDocumentSaveDraftProps() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocument)();
  const {
    saveDraft
  } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocumentActions)();
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_1__.useMixpanel)();
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.FileReportIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Save Draft', 'elementor'),
    onClick: () => {
      const eventName = config?.names?.editorOne?.topBarPublishDropdown;
      if (eventName) {
        dispatchEvent?.(eventName, {
          app_type: config?.appTypes?.editor,
          window_name: config?.appTypes?.editor,
          interaction_type: config?.triggers?.click?.toLowerCase(),
          target_type: config?.targetTypes?.dropdownItem,
          target_name: config?.targetNames?.publishDropdown?.saveDraft,
          interaction_result: config?.interactionResults?.actionSelected,
          target_location: config?.locations?.topBar?.replace(/\s+/g, '_').toLowerCase(),
          location_l1: config?.secondaryLocations?.publishDropdown?.replace(/\s+/g, '_').toLowerCase(),
          location_l2: config?.targetTypes?.dropdownItem
        });
      }
      saveDraft();
    },
    disabled: !document || document.isSaving || document.isSavingDraft || !document.isDirty
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-save-template-props.ts":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-save-template-props.ts ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useDocumentSaveTemplateProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function useDocumentSaveTemplateProps() {
  const {
    saveTemplate
  } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocumentActions)();
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_1__.useMixpanel)();
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.FolderIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Save as Template', 'elementor'),
    onClick: () => {
      const eventName = config?.names?.editorOne?.topBarPublishDropdown;
      if (eventName) {
        dispatchEvent?.(eventName, {
          app_type: config?.appTypes?.editor,
          window_name: config?.appTypes?.editor,
          interaction_type: config?.triggers?.click?.toLowerCase(),
          target_type: config?.targetTypes?.dropdownItem,
          target_name: config?.targetNames?.publishDropdown?.saveAsTemplate,
          interaction_result: config?.interactionResults?.actionSelected,
          target_location: config?.locations?.topBar?.replace(/\s+/g, '_').toLowerCase(),
          location_l1: config?.secondaryLocations?.publishDropdown?.replace(/\s+/g, '_').toLowerCase(),
          location_l2: config?.targetTypes?.dropdownItem
        });
      }
      saveTemplate();
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-view-page-props.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-view-page-props.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useDocumentViewPageProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);





function useDocumentViewPageProps() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocument)();
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_2__.useMixpanel)();
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_3__.EyeIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('View Page', 'elementor'),
    onClick: () => {
      const eventName = config?.names?.editorOne?.topBarPublishDropdown;
      if (eventName) {
        dispatchEvent?.(eventName, {
          app_type: config?.appTypes?.editor,
          window_name: config?.appTypes?.editor,
          interaction_type: config?.triggers?.click?.toLowerCase(),
          target_type: config?.targetTypes?.dropdownItem,
          target_name: config?.targetNames?.publishDropdown?.viewPage,
          interaction_result: config?.interactionResults?.actionSelected,
          target_location: config?.locations?.topBar?.replace(/\s+/g, '_').toLowerCase(),
          location_l1: config?.secondaryLocations?.publishDropdown?.replace(/\s+/g, '_').toLowerCase(),
          location_l2: config?.targetTypes?.dropdownItem
        });
      }
      if (document?.id) {
        (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommand)('editor/documents/view', {
          id: document.id
        });
      }
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/index.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/index.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _components_primary_action__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/primary-action */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/components/primary-action.tsx");
/* harmony import */ var _hooks_use_document_copy_and_share_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks/use-document-copy-and-share-props */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-copy-and-share-props.ts");
/* harmony import */ var _hooks_use_document_save_draft_props__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./hooks/use-document-save-draft-props */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-save-draft-props.ts");
/* harmony import */ var _hooks_use_document_save_template_props__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./hooks/use-document-save-template-props */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-save-template-props.ts");
/* harmony import */ var _hooks_use_document_view_page_props__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./hooks/use-document-view-page-props */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/hooks/use-document-view-page-props.ts");
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./locations */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/locations.ts");







function init() {
  (0,_locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoPrimaryAction)({
    id: 'document-primary-action',
    component: _components_primary_action__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
  _locations__WEBPACK_IMPORTED_MODULE_6__.documentOptionsMenu.registerAction({
    group: 'save',
    id: 'document-save-draft',
    priority: 10,
    useProps: _hooks_use_document_save_draft_props__WEBPACK_IMPORTED_MODULE_3__["default"]
  });
  _locations__WEBPACK_IMPORTED_MODULE_6__.documentOptionsMenu.registerAction({
    group: 'save',
    id: 'document-save-as-template',
    priority: 20,
    useProps: _hooks_use_document_save_template_props__WEBPACK_IMPORTED_MODULE_4__["default"]
  });
  _locations__WEBPACK_IMPORTED_MODULE_6__.documentOptionsMenu.registerAction({
    id: 'document-copy-and-share',
    priority: 10,
    useProps: _hooks_use_document_copy_and_share_props__WEBPACK_IMPORTED_MODULE_2__["default"]
  });
  _locations__WEBPACK_IMPORTED_MODULE_6__.documentOptionsMenu.registerAction({
    id: 'document-view-page',
    priority: 50,
    useProps: _hooks_use_document_view_page_props__WEBPACK_IMPORTED_MODULE_5__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/locations.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-save/locations.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   documentOptionsMenu: function() { return /* binding */ documentOptionsMenu; }
/* harmony export */ });
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/menus */ "@elementor/menus");
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_menus__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_actions_action__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/actions/action */ "./packages/packages/core/editor-app-bar/src/components/actions/action.tsx");
/* harmony import */ var _components_actions_link__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/actions/link */ "./packages/packages/core/editor-app-bar/src/components/actions/link.tsx");
/* harmony import */ var _components_actions_toggle_action__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/actions/toggle-action */ "./packages/packages/core/editor-app-bar/src/components/actions/toggle-action.tsx");




const documentOptionsMenu = (0,_elementor_menus__WEBPACK_IMPORTED_MODULE_0__.createMenu)({
  groups: ['save'],
  components: {
    Action: _components_actions_action__WEBPACK_IMPORTED_MODULE_1__["default"],
    ToggleAction: _components_actions_toggle_action__WEBPACK_IMPORTED_MODULE_3__["default"],
    Link: _components_actions_link__WEBPACK_IMPORTED_MODULE_2__["default"]
  }
});

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-settings/hooks/use-action-props.ts":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-settings/hooks/use-action-props.ts ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function useActionProps() {
  const activeDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocument)();
  const hostDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useHostDocument)();
  const {
    isActive,
    isBlocked
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateUseRouteStatus)('panel/page-settings');
  const document = activeDocument && activeDocument.type.value !== 'kit' ? activeDocument : hostDocument;
  const ButtonTitle = document ? /* translators: %s: Post type label. */
  (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('%s Settings', 'elementor').replace('%s', document.type.label) : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Document Settings', 'elementor');
  return {
    title: ButtonTitle,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.FileSettingsIcon,
    onClick: () => {
      if (!document) {
        return;
      }
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.documentSettings, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations['document-settings'],
          trigger: config.triggers.click,
          element: config.elements.buttonIcon
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateOpenRoute)('panel/page-settings/settings');
    },
    selected: isActive,
    disabled: isBlocked || !document
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/documents-settings/index.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/documents-settings/index.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/documents-settings/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.toolsMenu.registerToggleAction({
    id: 'document-settings-button',
    priority: 3,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/elements/hooks/use-action-props.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/elements/hooks/use-action-props.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  const {
    isActive,
    isBlocked
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseRouteStatus)('panel/elements');
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Add Element', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PlusIcon,
    id: 'ele-add-element',
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.widgetPanel, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations['widget-panel'],
          trigger: config.triggers.toggleClick,
          element: config.elements.buttonIcon
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateOpenRoute)('panel/elements/categories');
    },
    selected: isActive,
    disabled: isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/elements/index.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/elements/index.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/elements/hooks/use-action-props.ts");
/* harmony import */ var _sync_sync_panel_title__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./sync/sync-panel-title */ "./packages/packages/core/editor-app-bar/src/extensions/elements/sync/sync-panel-title.ts");



function init() {
  (0,_sync_sync_panel_title__WEBPACK_IMPORTED_MODULE_2__["default"])();
  _locations__WEBPACK_IMPORTED_MODULE_0__.toolsMenu.registerToggleAction({
    id: 'open-elements-panel',
    priority: 1,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/elements/sync/sync-panel-title.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/elements/sync/sync-panel-title.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ syncPanelTitle; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);


function syncPanelTitle() {
  const panelTitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Elements', 'elementor');
  const tabTitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Widgets', 'elementor');
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent)('panel/elements'), () => {
    setPanelTitle(panelTitle);
    setTabTitle(tabTitle);
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), () => {
    if ((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateIsRouteActive)('panel/elements')) {
      setPanelTitle(panelTitle);
      setTabTitle(tabTitle);
    }
  });
}
function setPanelTitle(title) {
  window.elementor?.getPanelView?.()?.getHeaderView?.()?.setTitle?.(title);
}
function setTabTitle(title) {
  const tab = document.querySelector('.elementor-component-tab[data-tab="categories"]');
  if (tab) {
    tab.textContent = title;
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/feedback/feedback-consts.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/feedback/feedback-consts.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EXPERIMENT_NAME: function() { return /* binding */ EXPERIMENT_NAME; },
/* harmony export */   FEEDBACK_TOGGLE_EVENT: function() { return /* binding */ FEEDBACK_TOGGLE_EVENT; }
/* harmony export */ });
const EXPERIMENT_NAME = 'in_editor_feedback';
const FEEDBACK_TOGGLE_EVENT = 'elementor/open-feedback';

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/feedback/index.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/feedback/index.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _feedback_consts__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./feedback-consts */ "./packages/packages/core/editor-app-bar/src/extensions/feedback/feedback-consts.ts");





function init() {
  const isActive = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.isExperimentActive)(_feedback_consts__WEBPACK_IMPORTED_MODULE_4__.EXPERIMENT_NAME);
  if (!isActive) {
    return;
  }
  _locations__WEBPACK_IMPORTED_MODULE_3__.mainMenu.registerAction({
    id: 'open-send-feedback',
    group: 'help',
    priority: 20,
    useProps: () => {
      return {
        icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.MessageLinesIcon,
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Send Feedback', 'elementor'),
        onClick: () => {
          dispatchEvent(new CustomEvent(_feedback_consts__WEBPACK_IMPORTED_MODULE_4__.FEEDBACK_TOGGLE_EVENT));
        }
      };
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/finder/hooks/use-action-props.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/finder/hooks/use-action-props.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Finder', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.SearchIcon,
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.finder, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.finder,
          trigger: config.triggers.toggleClick,
          element: config.elements.buttonIcon
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('finder/toggle');
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/finder/index.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/finder/index.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/finder/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.utilitiesMenu.registerAction({
    id: 'toggle-finder',
    priority: 15,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/help/hooks/use-action-props.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/help/hooks/use-action-props.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);


function useActionProps() {
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Help Center', 'elementor'),
    href: 'https://go.elementor.com/editor-top-bar-learn/',
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.HelpIcon,
    target: '_blank',
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.help, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.help,
          trigger: config.triggers.click,
          element: config.elements.buttonIcon
        });
      }
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/help/index.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/help/index.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/help/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.mainMenu.registerLink({
    id: 'open-help-center',
    group: 'help',
    priority: 10,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/history/hooks/use-action-props.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/history/hooks/use-action-props.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  const {
    isActive,
    isBlocked
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseRouteStatus)('panel/history');
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('History', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.HistoryIcon,
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.history, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.elementorLogo,
          trigger: config.triggers.click,
          element: config.elements.link
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateOpenRoute)('panel/history/actions');
    },
    selected: isActive,
    disabled: isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/history/index.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/history/index.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/history/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.toolsMenu.registerToggleAction({
    id: 'open-history',
    priority: 15,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/index.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/index.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _angie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./angie */ "./packages/packages/core/editor-app-bar/src/extensions/angie/index.ts");
/* harmony import */ var _connect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./connect */ "./packages/packages/core/editor-app-bar/src/extensions/connect/index.ts");
/* harmony import */ var _documents_preview__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./documents-preview */ "./packages/packages/core/editor-app-bar/src/extensions/documents-preview/index.ts");
/* harmony import */ var _documents_save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./documents-save */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/index.ts");
/* harmony import */ var _documents_settings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./documents-settings */ "./packages/packages/core/editor-app-bar/src/extensions/documents-settings/index.ts");
/* harmony import */ var _elements__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./elements */ "./packages/packages/core/editor-app-bar/src/extensions/elements/index.ts");
/* harmony import */ var _feedback__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./feedback */ "./packages/packages/core/editor-app-bar/src/extensions/feedback/index.ts");
/* harmony import */ var _finder__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./finder */ "./packages/packages/core/editor-app-bar/src/extensions/finder/index.ts");
/* harmony import */ var _help__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./help */ "./packages/packages/core/editor-app-bar/src/extensions/help/index.ts");
/* harmony import */ var _history__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./history */ "./packages/packages/core/editor-app-bar/src/extensions/history/index.ts");
/* harmony import */ var _keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./keyboard-shortcuts */ "./packages/packages/core/editor-app-bar/src/extensions/keyboard-shortcuts/index.ts");
/* harmony import */ var _responsive__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./responsive */ "./packages/packages/core/editor-app-bar/src/extensions/responsive/index.ts");
/* harmony import */ var _site_settings__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./site-settings */ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/index.ts");
/* harmony import */ var _structure__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./structure */ "./packages/packages/core/editor-app-bar/src/extensions/structure/index.ts");
/* harmony import */ var _theme_builder__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./theme-builder */ "./packages/packages/core/editor-app-bar/src/extensions/theme-builder/index.ts");
/* harmony import */ var _user_preferences__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./user-preferences */ "./packages/packages/core/editor-app-bar/src/extensions/user-preferences/index.ts");
/* harmony import */ var _wordpress__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./wordpress */ "./packages/packages/core/editor-app-bar/src/extensions/wordpress/index.ts");
/**
 * All the code in this directory is a temporary solution.
 * The code should be moved to the appropriate packages.
 */


















function init() {
  (0,_angie__WEBPACK_IMPORTED_MODULE_0__.init)();
  (0,_documents_preview__WEBPACK_IMPORTED_MODULE_2__.init)();
  (0,_documents_save__WEBPACK_IMPORTED_MODULE_3__.init)();
  (0,_documents_settings__WEBPACK_IMPORTED_MODULE_4__.init)();
  (0,_elements__WEBPACK_IMPORTED_MODULE_5__.init)();
  (0,_finder__WEBPACK_IMPORTED_MODULE_7__.init)();
  (0,_help__WEBPACK_IMPORTED_MODULE_8__.init)();
  (0,_history__WEBPACK_IMPORTED_MODULE_9__.init)();
  (0,_keyboard_shortcuts__WEBPACK_IMPORTED_MODULE_10__.init)();
  (0,_responsive__WEBPACK_IMPORTED_MODULE_11__.init)();
  (0,_site_settings__WEBPACK_IMPORTED_MODULE_12__.init)();
  (0,_feedback__WEBPACK_IMPORTED_MODULE_6__.init)();
  (0,_structure__WEBPACK_IMPORTED_MODULE_13__.init)();
  (0,_theme_builder__WEBPACK_IMPORTED_MODULE_14__.init)();
  (0,_user_preferences__WEBPACK_IMPORTED_MODULE_15__.init)();
  (0,_wordpress__WEBPACK_IMPORTED_MODULE_16__.init)();
  (0,_connect__WEBPACK_IMPORTED_MODULE_1__.init)();
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/keyboard-shortcuts/hooks/use-action-props.ts":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/keyboard-shortcuts/hooks/use-action-props.ts ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.KeyboardIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Keyboard Shortcuts', 'elementor'),
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.keyboardShortcuts, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.elementorLogo,
          trigger: config.triggers.click,
          element: config.elements.link
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('shortcuts/open');
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/keyboard-shortcuts/index.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/keyboard-shortcuts/index.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/keyboard-shortcuts/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.mainMenu.registerAction({
    id: 'open-keyboard-shortcuts',
    group: 'default',
    priority: 40,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/responsive/components/breakpoints-switcher.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/responsive/components/breakpoints-switcher.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ BreakpointsSwitcher; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





function BreakpointsSwitcher() {
  const breakpoints = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.useBreakpoints)();
  const activeBreakpoint = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.useActiveBreakpoint)();
  const activateBreakpoint = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.useActivateBreakpoint)();
  if (!breakpoints.length || !activeBreakpoint) {
    return null;
  }
  const onChange = (_, value) => {
    const extendedWindow = window;
    const config = extendedWindow?.elementorCommon?.eventsManager?.config;
    if (config) {
      extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.responsiveControls, {
        location: config.locations.topBar,
        secondaryLocation: config.secondaryLocations.responsiveControls,
        trigger: config.triggers.click,
        element: config.elements.buttonIcon,
        mode: value
      });
    }
    activateBreakpoint(value);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tabs, {
    textColor: "inherit",
    indicatorColor: "secondary",
    value: activeBreakpoint,
    onChange: onChange,
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Switch Device', 'elementor'),
    sx: {
      '& .MuiTabs-indicator': {
        backgroundColor: 'text.primary'
      }
    }
  }, breakpoints.map(({
    id,
    label,
    type,
    width
  }) => {
    const Icon = iconsMap[id];
    const title = labelsMap[type || 'default'].replace('%s', label).replace('%d', width?.toString() || '');
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tab, {
      value: id,
      key: id,
      "aria-label": title,
      icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Tooltip, {
        title: title
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)),
      sx: {
        minWidth: 'auto'
      },
      "data-testid": `switch-device-to-${id}`
    });
  }));
}
function Tooltip(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, _extends({
    PopperProps: {
      sx: {
        '&.MuiTooltip-popper .MuiTooltip-tooltip.MuiTooltip-tooltipPlacementBottom': {
          mt: 2.5
        }
      }
    }
  }, props));
}
const iconsMap = {
  widescreen: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.WidescreenIcon,
  desktop: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.DesktopIcon,
  laptop: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LaptopIcon,
  tablet_extra: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TabletLandscapeIcon,
  tablet: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TabletPortraitIcon,
  mobile_extra: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MobileLandscapeIcon,
  mobile: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MobilePortraitIcon
};
const labelsMap = {
  default: '%s',
  // translators: %s: Breakpoint label, %d: Breakpoint size.
  'min-width': (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('%s (%dpx and up)', 'elementor'),
  // translators: %s: Breakpoint label, %d: Breakpoint size.
  'max-width': (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('%s (up to %dpx)', 'elementor')
};

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/responsive/index.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/responsive/index.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _components_breakpoints_switcher__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/breakpoints-switcher */ "./packages/packages/core/editor-app-bar/src/extensions/responsive/components/breakpoints-switcher.tsx");


function init() {
  (0,_locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoResponsive)({
    id: 'responsive-breakpoints-switcher',
    component: _components_breakpoints_switcher__WEBPACK_IMPORTED_MODULE_1__["default"],
    options: {
      priority: 20 // After document indication.
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/portal.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/portal.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Portal; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



function Portal(props) {
  const containerRef = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.routeOpenEvent)('panel/global'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.routeCloseEvent)('panel/global')], getContainerRef);
  if (!containerRef.current) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Portal, _extends({
    container: containerRef.current
  }, props));
}
function getContainerRef() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateIsRouteActive)('panel/global') ? {
    current: document.querySelector('#elementor-panel-inner')
  } : {
    current: null
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/portalled-primary-action.tsx":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/portalled-primary-action.tsx ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PortalledPrimaryAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _portal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./portal */ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/portal.tsx");
/* harmony import */ var _primary_action__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./primary-action */ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/primary-action.tsx");



function PortalledPrimaryAction() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_portal__WEBPACK_IMPORTED_MODULE_1__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_primary_action__WEBPACK_IMPORTED_MODULE_2__["default"], null));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/primary-action.tsx":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/primary-action.tsx ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PrimaryAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function PrimaryAction() {
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const {
    save
  } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocumentActions)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Paper, {
    sx: {
      px: 5,
      py: 4,
      borderTop: 1,
      borderColor: 'divider'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "contained",
    disabled: !document || !document.isDirty,
    size: "medium",
    sx: {
      width: '100%'
    },
    onClick: () => document && !document.isSaving ? save() : null
  }, document?.isSaving ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CircularProgress, null) : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Save Changes', 'elementor')));
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/hooks/use-action-props.ts":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/site-settings/hooks/use-action-props.ts ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  const {
    isActive,
    isBlocked
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseRouteStatus)('panel/global', {
    blockOnKitRoutes: false
  });
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Site Settings', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.SettingsIcon,
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.siteSettings, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.siteSettings,
          trigger: config.triggers.toggleClick,
          element: config.elements.buttonIcon
        });
      }
      if (isActive) {
        (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('panel/global/close');
      } else {
        (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('panel/global/open');
      }
    },
    selected: isActive,
    disabled: isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/index.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/site-settings/index.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _components_portalled_primary_action__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/portalled-primary-action */ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/components/portalled-primary-action.tsx");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/site-settings/hooks/use-action-props.ts");




function init() {
  // This is portal, so it injected into the top of the editor, but renders inside the site-settings panel.
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'site-settings-primary-action-portal',
    component: _components_portalled_primary_action__WEBPACK_IMPORTED_MODULE_2__["default"]
  });
  _locations__WEBPACK_IMPORTED_MODULE_1__.mainMenu.registerToggleAction({
    id: 'toggle-site-settings',
    group: 'default',
    priority: 1,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_3__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/structure/hooks/use-action-props.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/structure/hooks/use-action-props.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  const {
    isActive,
    isBlocked
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseRouteStatus)('navigator');
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Structure', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.StructureIcon,
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.structure, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.structure,
          trigger: config.triggers.toggleClick,
          element: config.elements.buttonIcon
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('navigator/toggle');
    },
    selected: isActive,
    disabled: isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/structure/index.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/structure/index.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/structure/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.utilitiesMenu.registerToggleAction({
    id: 'toggle-structure-view',
    priority: 25,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/theme-builder/hooks/use-action-props.ts":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/theme-builder/hooks/use-action-props.ts ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ThemeBuilderIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Theme Builder', 'elementor'),
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.themeBuilder, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.elementorLogo,
          trigger: config.triggers.click,
          element: config.elements.link
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('app/open');
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/theme-builder/index.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/theme-builder/index.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/theme-builder/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.mainMenu.registerAction({
    id: 'open-theme-builder',
    group: 'default',
    priority: 10,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/user-preferences/hooks/use-action-props.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/user-preferences/hooks/use-action-props.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActionProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function useActionProps() {
  const {
    isActive,
    isBlocked
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseRouteStatus)('panel/editor-preferences');
  return {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ToggleRightIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('User Preferences', 'elementor'),
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.userPreferences, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.elementorLogo,
          trigger: config.triggers.click,
          element: config.elements.link
        });
      }
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateOpenRoute)('panel/editor-preferences');
    },
    selected: isActive,
    disabled: isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/user-preferences/index.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/user-preferences/index.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-action-props */ "./packages/packages/core/editor-app-bar/src/extensions/user-preferences/hooks/use-action-props.ts");


function init() {
  _locations__WEBPACK_IMPORTED_MODULE_0__.mainMenu.registerToggleAction({
    id: 'open-user-preferences',
    group: 'default',
    priority: 30,
    useProps: _hooks_use_action_props__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/extensions/wordpress/index.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/extensions/wordpress/index.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");




function init() {
  _locations__WEBPACK_IMPORTED_MODULE_3__.mainMenu.registerLink({
    id: 'exit-to-wordpress',
    group: 'exits',
    priority: 20,
    useProps: () => {
      const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.__useActiveDocument)();
      return {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Exit to WordPress', 'elementor'),
        href: document?.links?.platformEdit,
        icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.WordpressIcon,
        onClick: () => {
          const extendedWindow = window;
          const config = extendedWindow?.elementorCommon?.eventsManager?.config;
          if (config) {
            extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.exitToWordpress, {
              location: config.locations.topBar,
              secondaryLocation: config.secondaryLocations.elementorLogo,
              trigger: config.triggers.click,
              element: config.elements.link
            });
          }
        }
      };
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/init.ts":
/*!***********************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/init.ts ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_app_bar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/app-bar */ "./packages/packages/core/editor-app-bar/src/components/app-bar.tsx");
/* harmony import */ var _extensions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./extensions */ "./packages/packages/core/editor-app-bar/src/extensions/index.ts");
/* harmony import */ var _sync_redirect_old_menus__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./sync/redirect-old-menus */ "./packages/packages/core/editor-app-bar/src/sync/redirect-old-menus.ts");




function init() {
  (0,_sync_redirect_old_menus__WEBPACK_IMPORTED_MODULE_3__["default"])();
  (0,_extensions__WEBPACK_IMPORTED_MODULE_2__.init)();
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'app-bar',
    component: _components_app_bar__WEBPACK_IMPORTED_MODULE_1__["default"]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/locations.ts":
/*!****************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/locations.ts ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PageIndicationSlot: function() { return /* binding */ PageIndicationSlot; },
/* harmony export */   PrimaryActionSlot: function() { return /* binding */ PrimaryActionSlot; },
/* harmony export */   ResponsiveSlot: function() { return /* binding */ ResponsiveSlot; },
/* harmony export */   injectIntoPageIndication: function() { return /* binding */ injectIntoPageIndication; },
/* harmony export */   injectIntoPrimaryAction: function() { return /* binding */ injectIntoPrimaryAction; },
/* harmony export */   injectIntoResponsive: function() { return /* binding */ injectIntoResponsive; },
/* harmony export */   integrationsMenu: function() { return /* binding */ integrationsMenu; },
/* harmony export */   mainMenu: function() { return /* binding */ mainMenu; },
/* harmony export */   toolsMenu: function() { return /* binding */ toolsMenu; },
/* harmony export */   utilitiesMenu: function() { return /* binding */ utilitiesMenu; }
/* harmony export */ });
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/menus */ "@elementor/menus");
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_menus__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_actions_action__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/actions/action */ "./packages/packages/core/editor-app-bar/src/components/actions/action.tsx");
/* harmony import */ var _components_actions_link__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/actions/link */ "./packages/packages/core/editor-app-bar/src/components/actions/link.tsx");
/* harmony import */ var _components_actions_toggle_action__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/actions/toggle-action */ "./packages/packages/core/editor-app-bar/src/components/actions/toggle-action.tsx");





const {
  inject: injectIntoPageIndication,
  Slot: PageIndicationSlot
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();
const {
  inject: injectIntoResponsive,
  Slot: ResponsiveSlot
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();
const {
  inject: injectIntoPrimaryAction,
  Slot: PrimaryActionSlot
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_0__.createLocation)();
const components = {
  Action: _components_actions_action__WEBPACK_IMPORTED_MODULE_2__["default"],
  ToggleAction: _components_actions_toggle_action__WEBPACK_IMPORTED_MODULE_4__["default"],
  Link: _components_actions_link__WEBPACK_IMPORTED_MODULE_3__["default"]
};
const mainMenu = (0,_elementor_menus__WEBPACK_IMPORTED_MODULE_1__.createMenu)({
  groups: ['help', 'exits'],
  components
});
const toolsMenu = (0,_elementor_menus__WEBPACK_IMPORTED_MODULE_1__.createMenu)({
  components
});
const utilitiesMenu = (0,_elementor_menus__WEBPACK_IMPORTED_MODULE_1__.createMenu)({
  components
});
const integrationsMenu = (0,_elementor_menus__WEBPACK_IMPORTED_MODULE_1__.createMenu)({
  components
});

/***/ }),

/***/ "./packages/packages/core/editor-app-bar/src/sync/redirect-old-menus.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/sync/redirect-old-menus.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ redirectOldMenus; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function redirectOldMenus() {
  // Currently, in V1, when you click `esc` it opens the hamburger menu in the panel.
  // In V2, we don't have this panel, so we redirect the user to the elements panel instead.
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.routeOpenEvent)('panel/menu'), () => {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateOpenRoute)('panel/elements/categories');
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

/***/ "@elementor/editor-mcp":
/*!********************************************!*\
  !*** external ["elementorV2","editorMcp"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorMcp"];

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

/***/ "@elementor/locations":
/*!********************************************!*\
  !*** external ["elementorV2","locations"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["locations"];

/***/ }),

/***/ "@elementor/menus":
/*!****************************************!*\
  !*** external ["elementorV2","menus"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["menus"];

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
/*!************************************************************!*\
  !*** ./packages/packages/core/editor-app-bar/src/index.ts ***!
  \************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   documentOptionsMenu: function() { return /* reexport safe */ _extensions_documents_save_locations__WEBPACK_IMPORTED_MODULE_1__.documentOptionsMenu; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_2__.init; },
/* harmony export */   injectIntoPageIndication: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoPageIndication; },
/* harmony export */   injectIntoPrimaryAction: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoPrimaryAction; },
/* harmony export */   injectIntoResponsive: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.injectIntoResponsive; },
/* harmony export */   integrationsMenu: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.integrationsMenu; },
/* harmony export */   mainMenu: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.mainMenu; },
/* harmony export */   toolsMenu: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.toolsMenu; },
/* harmony export */   utilitiesMenu: function() { return /* reexport safe */ _locations__WEBPACK_IMPORTED_MODULE_0__.utilitiesMenu; }
/* harmony export */ });
/* harmony import */ var _locations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./locations */ "./packages/packages/core/editor-app-bar/src/locations.ts");
/* harmony import */ var _extensions_documents_save_locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./extensions/documents-save/locations */ "./packages/packages/core/editor-app-bar/src/extensions/documents-save/locations.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-app-bar/src/init.ts");



}();
(window.elementorV2 = window.elementorV2 || {}).editorAppBar = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorAppBar?.init?.();
//# sourceMappingURL=editor-app-bar.js.map
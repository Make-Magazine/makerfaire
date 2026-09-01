/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-components/src/api.ts":
/*!*************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/api.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   apiClient: function() { return /* binding */ apiClient; },
/* harmony export */   getParams: function() { return /* binding */ getParams; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__);


const BASE_URL = 'elementor/v1/components';
const getParams = id => ({
  action: 'get_document_config',
  unique_id: `document-config-${id}`,
  data: {
    id
  }
});
const apiClient = {
  get: () => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().get(`${BASE_URL}`).then(res => res.data.data),
  create: payload => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().post(`${BASE_URL}`, payload).then(res => res.data.data),
  updateStatuses: (ids, status) => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().put(`${BASE_URL}/status`, {
    ids,
    status
  }),
  getComponentConfig: id => _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.ajax.load(getParams(id)),
  invalidateComponentConfigCache: id => _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.ajax.invalidateCache(getParams(id)),
  getComponentLockStatus: async componentId => await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().get(`${BASE_URL}/lock-status`, {
    params: {
      componentId
    }
  }).then(res => {
    const {
      is_current_user_allow_to_edit: isAllowedToSwitchDocument,
      locked_by: lockedBy
    } = res.data.data;
    return {
      isAllowedToSwitchDocument,
      lockedBy: lockedBy || ''
    };
  }),
  lockComponent: async componentId => await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().post(`${BASE_URL}/lock`, {
    componentId
  }).then(res => res.data),
  unlockComponent: async componentId => await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().post(`${BASE_URL}/unlock`, {
    componentId
  }).then(res => res.data),
  getOverridableProps: async componentIds => await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().get(`${BASE_URL}/overridable-props`, {
    params: {
      'componentIds[]': componentIds
    }
  }).then(res => res.data),
  updateArchivedComponents: async (componentIds, status) => await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().post(`${BASE_URL}/archive`, {
    componentIds,
    status
  }).then(res => res.data.data),
  updateComponentTitle: (updatedComponentNames, status) => (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().post(`${BASE_URL}/update-titles`, {
    components: updatedComponentNames,
    status
  }).then(res => res.data.data),
  validate: async payload => await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_1__.httpService)().post(`${BASE_URL}/create-validate`, payload).then(res => res.data)
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/component-instance-transformer.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/component-instance-transformer.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentInstanceTransformer: function() { return /* binding */ componentInstanceTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _utils_component_document_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/component-document-data */ "./packages/packages/core/editor-components/src/utils/component-document-data.ts");




const componentInstanceTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(async ({
  component_id: id,
  overrides: overridesValue
}) => {
  const unpublishedComponents = (0,_store_store__WEBPACK_IMPORTED_MODULE_2__.selectUnpublishedComponents)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__getState)());
  const unpublishedComponent = unpublishedComponents.find(({
    uid
  }) => uid === id);
  const overrides = overridesValue?.reduce((acc, override) => ({
    ...acc,
    ...override
  }), {});
  if (unpublishedComponent) {
    return {
      elements: structuredClone(unpublishedComponent.elements),
      overrides
    };
  }
  if (typeof id !== 'number') {
    throw new Error(`Component ID "${id}" not valid.`);
  }
  const data = await (0,_utils_component_document_data__WEBPACK_IMPORTED_MODULE_3__.getComponentDocumentData)(id);
  return {
    elements: data?.elements ?? [],
    overrides
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-components/src/component-overridable-transformer.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/component-overridable-transformer.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentOverridableTransformer: function() { return /* binding */ componentOverridableTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);

const componentOverridableTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)((value, options) => {
  const {
    overrides
  } = options.renderContext ?? {};
  const overrideValue = overrides?.[value.override_key];
  if (overrideValue) {
    const isOverride = isOriginValueOverride(value.origin_value);
    if (isOverride) {
      return transformOverride(value, options, overrideValue);
    }
    return overrideValue;
  }
  return value.origin_value;
});
function transformOverride(value, options, overrideValue) {
  const transformer = _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.settingsTransformersRegistry.get('override');
  if (!transformer) {
    return null;
  }
  const transformedValue = transformer(value.origin_value.value, options);
  if (!transformedValue) {
    return null;
  }
  const [key] = Object.keys(transformedValue);
  return {
    [key]: overrideValue
  };
}
function isOriginValueOverride(originValue) {
  return originValue.$$type === 'override';
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/component-override-transformer.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/component-override-transformer.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentOverrideTransformer: function() { return /* binding */ componentOverrideTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);

const componentOverrideTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(override => {
  const {
    override_key: key,
    override_value: overrideValue
  } = override;
  return {
    [key]: overrideValue
  };
});

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/component-search.tsx":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/component-search.tsx ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentSearch: function() { return /* binding */ ComponentSearch; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _search_provider__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./search-provider */ "./packages/packages/core/editor-components/src/components/components-tab/search-provider.tsx");





const ComponentSearch = () => {
  const {
    inputValue,
    handleChange
  } = (0,_search_provider__WEBPACK_IMPORTED_MODULE_4__.useSearch)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    gap: 0.5,
    sx: {
      width: '100%',
      px: 2,
      py: 1.5
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

/***/ "./packages/packages/core/editor-components/src/components/components-tab/components-item.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/components-item.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentItem: function() { return /* binding */ ComponentItem; },
/* harmony export */   ComponentName: function() { return /* binding */ ComponentName; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





const ComponentItem = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  component,
  disabled = true,
  draggable,
  onDragStart,
  onDragEnd,
  onClick,
  isEditing = false,
  error = null,
  nameSlot,
  endSlot,
  ...props
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemButton, _extends({
    disabled: disabled,
    draggable: draggable,
    onDragStart: onDragStart,
    onDragEnd: onDragEnd,
    shape: "rounded",
    ref: ref,
    sx: {
      border: 'solid 1px',
      borderColor: 'divider',
      py: 0.5,
      px: 1,
      display: 'flex',
      width: '100%',
      alignItems: 'center',
      gap: 1
    }
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    display: "flex",
    alignItems: "center",
    gap: 1,
    minWidth: 0,
    flexGrow: 1,
    onClick: onClick
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemIcon, {
    size: "tiny"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ComponentsIcon, {
    fontSize: "tiny"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Indicator, {
    isActive: isEditing,
    isError: !!error
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    display: "flex",
    flex: 1,
    minWidth: 0,
    flexGrow: 1
  }, nameSlot ?? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ComponentName, {
    name: component.name
  })))), endSlot);
});
const Indicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
  shouldForwardProp: prop => prop !== 'isActive' && prop !== 'isError'
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
function ComponentName({
  name,
  editable
}) {
  if (editable?.isEditing) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EditableField, _extends({
      ref: editable.ref,
      as: _elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography,
      variant: "caption"
    }, editable.getProps()));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: name,
    as: _elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography,
    variant: "caption",
    color: "text.primary"
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/components-list.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/components-list.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentsList: function() { return /* binding */ ComponentsList; },
/* harmony export */   EmptySearchResult: function() { return /* binding */ EmptySearchResult; },
/* harmony export */   useFilteredComponents: function() { return /* binding */ useFilteredComponents; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-components */ "./packages/packages/core/editor-components/src/hooks/use-components.ts");
/* harmony import */ var _hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../hooks/use-components-permissions */ "./packages/packages/core/editor-components/src/hooks/use-components-permissions.ts");
/* harmony import */ var _utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../utils/is-pro-components-supported */ "./packages/packages/core/editor-components/src/utils/is-pro-components-supported.ts");
/* harmony import */ var _components_item__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components-item */ "./packages/packages/core/editor-components/src/components/components-tab/components-item.tsx");
/* harmony import */ var _loading_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./loading-components */ "./packages/packages/core/editor-components/src/components/components-tab/loading-components.tsx");
/* harmony import */ var _search_provider__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./search-provider */ "./packages/packages/core/editor-components/src/components/components-tab/search-provider.tsx");










const LEARN_MORE_URL = 'http://go.elementor.com/components-guide-article';
const UPGRADE_URL = 'https://go.elementor.com/go-pro-components/';
const UPDATE_PLUGINS_URL = '/wp-admin/plugins.php';

// Override legacy panel CSS reset that sets h1-h6 to font-size:100% and font-weight:normal.
// See: assets/dev/scss/editor/panel/_reset.scss (applied via :where() selector in panel.scss).
const SUBTITLE_OVERRIDE_SX = {
  fontSize: '0.875rem !important',
  fontWeight: '500 !important'
};
function ComponentsList() {
  const {
    components,
    isLoading,
    searchValue
  } = useFilteredComponents();
  if (isLoading) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_loading_components__WEBPACK_IMPORTED_MODULE_8__.LoadingComponents, null);
  }
  const isEmpty = !components?.length;
  if (isEmpty) {
    if (searchValue.length) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EmptySearchResult, null);
    }
    if ((0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_6__.isProOutdatedForComponents)()) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ProOutdatedEmptyState, null);
    }
    return (0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_6__.isProComponentsSupported)() ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EmptyState, null) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ProUpgradeEmptyState, null);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.List, {
    sx: {
      display: 'flex',
      flexDirection: 'column',
      gap: 1,
      px: 2
    }
  }, components.map(component => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_item__WEBPACK_IMPORTED_MODULE_7__.ComponentItem, {
    key: component.uid,
    component: component
  })));
}
const ProUpgradeEmptyState = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    justifyContent: "start",
    height: "100%",
    sx: {
      px: 2,
      py: 4
    },
    gap: 2,
    overflow: "hidden"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ComponentsIcon, {
    fontSize: "large",
    sx: {
      color: 'text.secondary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2",
    color: "text.secondary",
    sx: SUBTITLE_OVERRIDE_SX
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create Reusable Components', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    color: "secondary",
    sx: {
      maxWidth: 200
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create design elements that sync across your entire site.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "contained",
    color: "promotion",
    size: "small",
    startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon, null),
    href: UPGRADE_URL,
    target: "_blank",
    rel: "noopener noreferrer"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade now', 'elementor')));
};
const ProOutdatedEmptyState = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    justifyContent: "start",
    height: "100%",
    sx: {
      px: 2,
      py: 4,
      maxWidth: 268,
      m: 'auto'
    },
    gap: 2,
    overflow: "hidden"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ComponentsIcon, {
    fontSize: "large",
    sx: {
      color: 'text.secondary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2",
    color: "text.secondary",
    sx: SUBTITLE_OVERRIDE_SX
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create Reusable Components', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    color: "secondary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create design elements that sync across your entire site.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    color: "secondary",
    sx: {
      mt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('To create components, update Elementor Pro to the latest version.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "text",
    color: "info",
    size: "small",
    href: UPDATE_PLUGINS_URL,
    target: "_blank",
    rel: "noopener noreferrer"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Update Elementor Pro', 'elementor')));
};
const EmptyState = () => {
  const {
    canCreate
  } = (0,_hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_5__.useComponentsPermissions)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    justifyContent: "start",
    height: "100%",
    sx: {
      px: 2,
      py: 4
    },
    gap: 2,
    overflow: "hidden"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ComponentsIcon, {
    fontSize: "large",
    sx: {
      color: 'text.secondary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2",
    color: "text.secondary",
    sx: SUBTITLE_OVERRIDE_SX
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No components yet', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    color: "secondary",
    sx: {
      maxWidth: 200
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Components are reusable blocks that sync across your site.', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), canCreate ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create once, use everywhere.', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('With your current role, you cannot create components. Contact an administrator to create one.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Link, {
    href: LEARN_MORE_URL,
    target: "_blank",
    rel: "noopener noreferrer",
    variant: "caption",
    color: "info.main"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Learn more about components', 'elementor'))), canCreate && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, {
    sx: {
      width: '100%'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    gap: 1,
    width: "100%"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2",
    color: "text.secondary",
    sx: SUBTITLE_OVERRIDE_SX
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Create your first one:', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    color: "secondary",
    sx: {
      maxWidth: 228
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Right-click any div-block or flexbox on your canvas or structure and select "Create component"', 'elementor')))));
};
const EmptySearchResult = () => {
  const {
    searchValue,
    clearSearch
  } = (0,_search_provider__WEBPACK_IMPORTED_MODULE_9__.useSearch)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    color: 'text.secondary',
    pt: 5,
    alignItems: "center",
    gap: 1,
    overflow: 'hidden',
    justifySelf: 'center'
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ComponentsIcon, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      width: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2",
    color: "inherit",
    sx: SUBTITLE_OVERRIDE_SX
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Sorry, nothing matched', 'elementor')), searchValue && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle2",
    color: "inherit",
    sx: {
      ...SUBTITLE_OVERRIDE_SX,
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
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Try something else.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    color: "inherit"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Link, {
    color: "secondary",
    variant: "caption",
    component: "button",
    onClick: clearSearch
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Clear & try again', 'elementor'))));
};
const useFilteredComponents = () => {
  const {
    components,
    isLoading
  } = (0,_hooks_use_components__WEBPACK_IMPORTED_MODULE_4__.useComponents)();
  const {
    searchValue
  } = (0,_search_provider__WEBPACK_IMPORTED_MODULE_9__.useSearch)();
  return {
    components: components.filter(component => component.name.toLowerCase().includes(searchValue.toLowerCase())),
    isLoading,
    searchValue
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/components-pro-notification.tsx":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/components-pro-notification.tsx ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentsProNotification: function() { return /* binding */ ComponentsProNotification; },
/* harmony export */   UPGRADE_URL: function() { return /* binding */ UPGRADE_URL; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_upgrade_alert__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components-upgrade-alert */ "./packages/packages/core/editor-components/src/components/components-upgrade-alert.tsx");



const UPGRADE_URL = 'https://go.elementor.com/go-pro-components-exist-footer/';
function ComponentsProNotification() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_upgrade_alert__WEBPACK_IMPORTED_MODULE_2__.ComponentsUpgradeAlert, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Create new components', 'elementor'),
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Creating new components requires an active Pro subscription.', 'elementor'),
    upgradeUrl: UPGRADE_URL
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/components-update-notification.tsx":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/components-update-notification.tsx ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentsUpdateNotification: function() { return /* binding */ ComponentsUpdateNotification; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_update_alert__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components-update-alert */ "./packages/packages/core/editor-components/src/components/components-update-alert.tsx");



function ComponentsUpdateNotification() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_update_alert__WEBPACK_IMPORTED_MODULE_2__.ComponentsUpdateAlert, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Create new Components', 'elementor'),
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('To create new components, update Elementor Pro to the latest version.', 'elementor')
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/components.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/components.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Components: function() { return /* binding */ Components; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../hooks/use-components */ "./packages/packages/core/editor-components/src/hooks/use-components.ts");
/* harmony import */ var _utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/is-pro-components-supported */ "./packages/packages/core/editor-components/src/utils/is-pro-components-supported.ts");
/* harmony import */ var _component_search__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./component-search */ "./packages/packages/core/editor-components/src/components/components-tab/component-search.tsx");
/* harmony import */ var _components_list__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components-list */ "./packages/packages/core/editor-components/src/components/components-tab/components-list.tsx");
/* harmony import */ var _components_pro_notification__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components-pro-notification */ "./packages/packages/core/editor-components/src/components/components-tab/components-pro-notification.tsx");
/* harmony import */ var _components_update_notification__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components-update-notification */ "./packages/packages/core/editor-components/src/components/components-tab/components-update-notification.tsx");
/* harmony import */ var _search_provider__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./search-provider */ "./packages/packages/core/editor-components/src/components/components-tab/search-provider.tsx");











const FULL_HEIGHT_STYLE_ID = 'components-full-height-panel';
const FULL_HEIGHT_CSS = `
#elementor-panel-page-elements {
	display: flex;
	flex-direction: column;
	height: 100%;
}

#elementor-panel-elements {
	display: flex;
	flex-direction: column;
	flex: 1;
	min-height: 0;
}

#elementor-panel-elements-wrapper {
	display: flex;
	flex-direction: column;
	flex: 1;
	min-height: 0;
}
`;
const useFullHeightPanel = () => {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect)(() => {
    let style = document.getElementById(FULL_HEIGHT_STYLE_ID);
    if (!style) {
      style = document.createElement('style');
      style.id = FULL_HEIGHT_STYLE_ID;
      style.textContent = FULL_HEIGHT_CSS;
      document.head.appendChild(style);
    }
    return () => {
      document.getElementById(FULL_HEIGHT_STYLE_ID)?.remove();
    };
  }, []);
};
const ComponentsContent = () => {
  const {
    components,
    isLoading
  } = (0,_hooks_use_components__WEBPACK_IMPORTED_MODULE_3__.useComponents)();
  const hasComponents = !isLoading && components.length > 0;
  const showProNotification = !(0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_4__.isProComponentsSupported)() && hasComponents;
  const isOutdated = (0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_4__.isProOutdatedForComponents)();
  useFullHeightPanel();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    justifyContent: "space-between",
    sx: {
      flex: 1,
      minHeight: 0
    }
  }, hasComponents && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_component_search__WEBPACK_IMPORTED_MODULE_5__.ComponentSearch, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_list__WEBPACK_IMPORTED_MODULE_6__.ComponentsList, null), showProNotification && (isOutdated ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_update_notification__WEBPACK_IMPORTED_MODULE_8__.ComponentsUpdateNotification, null) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_pro_notification__WEBPACK_IMPORTED_MODULE_7__.ComponentsProNotification, null)));
};
const Components = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_search_provider__WEBPACK_IMPORTED_MODULE_9__.SearchProvider, {
    localStorageKey: "elementor-components-search"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ComponentsContent, null)));
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/loading-components.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/loading-components.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LoadingComponents: function() { return /* binding */ LoadingComponents; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const ROWS = Array.from({
  length: 3
}, (_, index) => index);
const STAGGER_DELAY_MS = 80;
const LoadingComponents = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    "aria-label": "Loading components",
    gap: 1.5,
    sx: {
      pointerEvents: 'none',
      position: 'relative',
      maxHeight: '300px',
      overflow: 'hidden',
      px: 1,
      '&:after': {
        position: 'absolute',
        bottom: 0,
        content: '""',
        left: 0,
        width: '100%',
        height: '40%',
        pointerEvents: 'none',
        zIndex: 1
      }
    }
  }, ROWS.map(row => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    key: row,
    display: "flex",
    alignItems: "center",
    gap: 1.5,
    sx: {
      py: 0.75,
      px: 1.5,
      opacity: 0,
      animation: `e-loading-fade-in 0.4s ease-out ${row * STAGGER_DELAY_MS}ms forwards`,
      '@keyframes e-loading-fade-in': {
        from: {
          opacity: 0,
          transform: 'translateY(4px)'
        },
        to: {
          opacity: 1,
          transform: 'translateY(0)'
        }
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Skeleton, {
    animation: "wave",
    variant: "rounded",
    width: 24,
    height: 24
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Skeleton, {
    animation: "wave",
    variant: "rounded",
    width: "60%",
    height: 14
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-tab/search-provider.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-tab/search-provider.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SearchProvider: function() { return /* binding */ SearchProvider; },
/* harmony export */   useSearch: function() { return /* binding */ useSearch; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);



const SearchContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);
const SearchProvider = ({
  children,
  localStorageKey
}) => {
  const {
    debouncedValue,
    handleChange,
    inputValue
  } = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.useSearchState)({
    localStorageKey
  });
  const clearSearch = () => {
    handleChange('');
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SearchContext.Provider, {
    value: {
      handleChange,
      clearSearch,
      searchValue: debouncedValue,
      inputValue
    }
  }, children);
};
const useSearch = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(SearchContext);
  if (!context) {
    throw new Error('useSearch must be used within a SearchProvider');
  }
  return context;
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-update-alert.tsx":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-update-alert.tsx ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentsUpdateAlert: function() { return /* binding */ ComponentsUpdateAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const UPDATE_PLUGINS_URL = '/wp-admin/plugins.php';
function ComponentsUpdateAlert({
  title,
  description
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      mt: 'auto',
      position: 'sticky',
      bottom: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Alert, {
    variant: "standard",
    color: "info",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.InfoCircleFilledIcon, {
      fontSize: "tiny"
    }),
    role: "status",
    size: "small",
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertAction, {
      variant: "contained",
      color: "info",
      href: UPDATE_PLUGINS_URL,
      target: "_blank",
      rel: "noopener noreferrer"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade Now', 'elementor')),
    sx: {
      m: 2,
      mt: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertTitle, null, title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption"
  }, description)));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/components-upgrade-alert.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/components-upgrade-alert.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentsUpgradeAlert: function() { return /* binding */ ComponentsUpgradeAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function ComponentsUpgradeAlert({
  title,
  description,
  upgradeUrl
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      mt: 'auto',
      position: 'sticky',
      bottom: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Alert, {
    variant: "standard",
    color: "promotion",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon, {
      fontSize: "tiny"
    }),
    role: "status",
    size: "small",
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertAction, {
      variant: "contained",
      color: "promotion",
      href: upgradeUrl,
      target: "_blank",
      rel: "noopener noreferrer"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade now', 'elementor')),
    sx: {
      m: 2,
      mt: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertTitle, null, title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption"
  }, description)));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/control-label.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/control-label.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ControlLabel: function() { return /* binding */ ControlLabel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);



const ControlLabel = ({
  children,
  ...props
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyItems: "start",
    gap: 0.25
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, props, children), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlAdornments, null));
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/detach-instance-confirmation-dialog.tsx":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/detach-instance-confirmation-dialog.tsx ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DetachInstanceConfirmationDialog: function() { return /* binding */ DetachInstanceConfirmationDialog; },
/* harmony export */   openDetachConfirmDialog: function() { return /* binding */ openDetachConfirmDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function DetachInstanceConfirmationDialog({
  open,
  onClose,
  onConfirm
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog, {
    open: open,
    onClose: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Title, {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.AlertTriangleFilledIcon,
    iconColor: "secondary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Detach from Component?', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Detaching this instance will break its link to the Component. Changes to the Component will no longer apply. Continue?', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Actions, {
    onClose: onClose,
    onConfirm: onConfirm,
    confirmLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Detach', 'elementor'),
    color: "primary"
  }));
}

// Used imperatively from the context menu (Marionette view).
function openDetachConfirmDialog(onConfirm) {
  const handleConfirm = () => {
    (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.closeDialog)();
    onConfirm();
  };
  (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.openDialog)({
    component: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DetachInstanceConfirmationDialog, {
      open: true,
      onClose: _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.closeDialog,
      onConfirm: handleConfirm
    })
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/errors.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/errors.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OverrideControlInnerElementNotFoundError: function() { return /* binding */ OverrideControlInnerElementNotFoundError; },
/* harmony export */   OverrideControlPropTypeNotFoundError: function() { return /* binding */ OverrideControlPropTypeNotFoundError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const OverrideControlInnerElementNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'override_control_inner_element_not_found',
  message: `Component inner element not found for override control. The element may have been deleted without updating the overridable props, or the component has not finished rendering yet.`
});
const OverrideControlPropTypeNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'override_control_prop_type_not_found',
  message: 'Prop type not found for override control.'
});

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/in-edit-mode.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/in-edit-mode.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   openEditModeDialog: function() { return /* binding */ openEditModeDialog; }
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





const openEditModeDialog = lockedBy => {
  (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.openDialog)({
    component: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EditModeDialog, {
      lockedBy: lockedBy
    })
  });
};
const EditModeDialog = ({
  lockedBy
}) => {
  /* translators: %s is the name of the user who is currently editing the document */
  const content = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('%s is currently editing this document', 'elementor').replace('%s', lockedBy);
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
  }, content))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    spacing: 2,
    direction: "column"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "body2"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('You can wait for them to finish or reach out to coordinate your changes together.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogActions, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    color: "secondary",
    variant: "contained",
    onClick: _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.closeDialog
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Close', 'elementor'))))));
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/detach-action.tsx":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/detach-action.tsx ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DetachAction: function() { return /* binding */ DetachAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _utils_detach_component_instance__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/detach-component-instance */ "./packages/packages/core/editor-components/src/utils/detach-component-instance/index.ts");
/* harmony import */ var _detach_instance_confirmation_dialog__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../detach-instance-confirmation-dialog */ "./packages/packages/core/editor-components/src/components/detach-instance-confirmation-dialog.tsx");
/* harmony import */ var _instance_panel_header__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./instance-panel-header */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-header.tsx");








const DetachAction = ({
  componentInstanceId,
  componentId
}) => {
  const [isDetachDialogOpen, setIsDetachDialogOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const handleDetachConfirm = async () => {
    setIsDetachDialogOpen(false);
    try {
      await (0,_utils_detach_component_instance__WEBPACK_IMPORTED_MODULE_4__.detachComponentInstance)({
        instanceId: componentInstanceId,
        componentId,
        trackingInfo: getDetachTrackingInfo()
      });
    } catch {
      (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)({
        type: 'error',
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Failed to detach component instance.', 'elementor'),
        id: 'detach-component-instance-failed'
      });
    }
  };
  const handleDetachCancel = () => {
    setIsDetachDialogOpen(false);
  };
  const handleDetachClick = () => {
    setIsDetachDialogOpen(true);
  };
  const detachLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Detach from Component', 'elementor');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_instance_panel_header__WEBPACK_IMPORTED_MODULE_6__.EditComponentAction, {
    label: detachLabel,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.DetachIcon,
    onClick: handleDetachClick
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_detach_instance_confirmation_dialog__WEBPACK_IMPORTED_MODULE_5__.DetachInstanceConfirmationDialog, {
    open: isDetachDialogOpen,
    onClose: handleDetachCancel,
    onConfirm: handleDetachConfirm
  }));
};
function getDetachTrackingInfo() {
  const extendedWindow = window;
  const config = extendedWindow?.elementorCommon?.eventsManager?.config;
  if (!config) {
    return {
      location: '',
      trigger: ''
    };
  }
  return {
    location: config.locations.components.instanceEditingPanel,
    trigger: config.triggers.click
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/empty-state.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/empty-state.tsx ***!
  \********************************************************************************************************/
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
/* harmony import */ var _hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-components-permissions */ "./packages/packages/core/editor-components/src/hooks/use-components-permissions.ts");





const EmptyState = ({
  onEditComponent
}) => {
  const {
    canEdit
  } = (0,_hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_4__.useComponentsPermissions)();
  const message = canEdit ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit the component to add properties, manage them or update the design across all instances.', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('With your current role, you cannot edit this component. Contact an administrator to add properties.', 'elementor');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    justifyContent: "start",
    height: "100%",
    color: "text.secondary",
    sx: {
      p: 2.5,
      pt: 8,
      pb: 5.5,
      mt: 1
    },
    gap: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ComponentPropListIcon, {
    fontSize: "large"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "subtitle2"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No properties yet', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    align: "center",
    variant: "caption",
    maxWidth: "170px"
  }, message), canEdit && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "outlined",
    color: "secondary",
    size: "small",
    sx: {
      mt: 1
    },
    disabled: !onEditComponent,
    onClick: onEditComponent
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PencilIcon, {
    fontSize: "small"
  }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit component', 'elementor')));
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-editing-panel.tsx":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-editing-panel.tsx ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InstanceEditingPanel: function() { return /* binding */ InstanceEditingPanel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-components-permissions */ "./packages/packages/core/editor-components/src/hooks/use-components-permissions.ts");
/* harmony import */ var _provider_component_instance_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../provider/component-instance-context */ "./packages/packages/core/editor-components/src/provider/component-instance-context.tsx");
/* harmony import */ var _utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../utils/is-pro-components-supported */ "./packages/packages/core/editor-components/src/utils/is-pro-components-supported.ts");
/* harmony import */ var _components_update_alert__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../components-update-alert */ "./packages/packages/core/editor-components/src/components/components-update-alert.tsx");
/* harmony import */ var _components_upgrade_alert__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../components-upgrade-alert */ "./packages/packages/core/editor-components/src/components/components-upgrade-alert.tsx");
/* harmony import */ var _detach_action__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./detach-action */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/detach-action.tsx");
/* harmony import */ var _empty_state__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./empty-state */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/empty-state.tsx");
/* harmony import */ var _instance_panel_body__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./instance-panel-body */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-body.tsx");
/* harmony import */ var _instance_panel_header__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./instance-panel-header */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-header.tsx");
/* harmony import */ var _use_instance_panel_data__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./use-instance-panel-data */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/use-instance-panel-data.ts");














const EDIT_UPGRADE_URL = 'https://go.elementor.com/go-pro-components-Instance-edit-footer/';
function InstanceEditingPanel() {
  const {
    canEdit
  } = (0,_hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_4__.useComponentsPermissions)();
  const data = (0,_use_instance_panel_data__WEBPACK_IMPORTED_MODULE_13__.useInstancePanelData)();
  if (!data) {
    return null;
  }
  const {
    componentId,
    component,
    overrides,
    overridableProps,
    groups,
    isEmpty,
    componentInstanceId
  } = data;

  /* translators: %s: component name. */
  const panelTitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit %s', 'elementor').replace('%s', component.name);
  const actions = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    gap: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_detach_action__WEBPACK_IMPORTED_MODULE_9__.DetachAction, {
    componentInstanceId: componentInstanceId,
    componentId: componentId
  }), canEdit && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_instance_panel_header__WEBPACK_IMPORTED_MODULE_12__.EditComponentAction, {
    disabled: true,
    label: panelTitle,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PencilIcon
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    "data-testid": "instance-editing-panel",
    sx: {
      display: 'flex',
      flexDirection: 'column',
      height: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_5__.ComponentInstanceProvider, {
    componentId: componentId,
    overrides: overrides,
    overridableProps: overridableProps
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_instance_panel_header__WEBPACK_IMPORTED_MODULE_12__.InstancePanelHeader, {
    componentName: component.name,
    actions: actions
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_instance_panel_body__WEBPACK_IMPORTED_MODULE_11__.InstancePanelBody, {
    groups: groups,
    isEmpty: isEmpty,
    emptyState: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_empty_state__WEBPACK_IMPORTED_MODULE_10__.EmptyState, null),
    componentInstanceId: componentInstanceId
  })), !(0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_6__.isProComponentsSupported)() && ((0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_6__.isProOutdatedForComponents)() ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_update_alert__WEBPACK_IMPORTED_MODULE_7__.ComponentsUpdateAlert, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit Component', 'elementor'),
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('To edit components, update Elementor Pro to the latest version.', 'elementor')
  }) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_upgrade_alert__WEBPACK_IMPORTED_MODULE_8__.ComponentsUpgradeAlert, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit components', 'elementor'),
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Editing components requires an active Pro subscription.', 'elementor'),
    upgradeUrl: EDIT_UPGRADE_URL
  })));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-body.tsx":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-body.tsx ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InstancePanelBody: function() { return /* binding */ InstancePanelBody; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _override_props_group__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./override-props-group */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/override-props-group.tsx");






function InstancePanelBody({
  groups,
  isEmpty,
  emptyState,
  componentInstanceId
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__.PanelBody, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlAdornmentsProvider, {
    items: (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.getFieldIndicators)('settings')
  }, isEmpty ? emptyState : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
    direction: "column",
    alignItems: "stretch"
  }, groups.map(group => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: group.id + componentInstanceId
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_override_props_group__WEBPACK_IMPORTED_MODULE_5__.OverridePropsGroup, {
    group: group
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null))))));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-header.tsx":
/*!******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-header.tsx ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditComponentAction: function() { return /* binding */ EditComponentAction; },
/* harmony export */   InstancePanelHeader: function() { return /* binding */ InstancePanelHeader; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);





function InstancePanelHeader({
  componentName,
  actions
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.PanelHeader, {
    sx: {
      justifyContent: 'start',
      px: 2
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
    direction: "row",
    alignItems: "center",
    flexGrow: 1,
    gap: 1,
    maxWidth: "100%"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ComponentsIcon, {
    fontSize: "small",
    sx: {
      color: 'text.tertiary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.EllipsisWithTooltip, {
    title: componentName,
    as: _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.PanelHeaderTitle,
    sx: {
      flexGrow: 1
    }
  }), actions));
}
function EditComponentAction({
  label,
  onClick,
  disabled = false,
  icon: Icon
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
    title: label
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, {
    size: "tiny",
    onClick: onClick,
    "aria-label": label,
    disabled: disabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    fontSize: "tiny"
  })));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/override-prop-control.tsx":
/*!******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/override-prop-control.tsx ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OverridePropControl: function() { return /* binding */ OverridePropControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_controls_by_widget_type__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-controls-by-widget-type */ "./packages/packages/core/editor-components/src/hooks/use-controls-by-widget-type.ts");
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../prop-types/component-instance-overrides-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../prop-types/component-instance-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");
/* harmony import */ var _provider_component_instance_context__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../provider/component-instance-context */ "./packages/packages/core/editor-components/src/provider/component-instance-context.tsx");
/* harmony import */ var _provider_overridable_prop_context__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../provider/overridable-prop-context */ "./packages/packages/core/editor-components/src/provider/overridable-prop-context.tsx");
/* harmony import */ var _store_actions_update_overridable_prop__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../store/actions/update-overridable-prop */ "./packages/packages/core/editor-components/src/store/actions/update-overridable-prop.ts");
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _utils_get_prop_type_for_component_override__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../utils/get-prop-type-for-component-override */ "./packages/packages/core/editor-components/src/utils/get-prop-type-for-component-override.ts");
/* harmony import */ var _utils_overridable_props_utils__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../utils/overridable-props-utils */ "./packages/packages/core/editor-components/src/utils/overridable-props-utils.ts");
/* harmony import */ var _utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../../utils/resolve-override-prop-value */ "./packages/packages/core/editor-components/src/utils/resolve-override-prop-value.ts");
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../control-label */ "./packages/packages/core/editor-components/src/components/control-label.tsx");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-components/src/components/errors.ts");
/* harmony import */ var _utils_correct_exposed_empty_override__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./utils/correct-exposed-empty-override */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/correct-exposed-empty-override.ts");
/* harmony import */ var _utils_resolve_element_settings__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./utils/resolve-element-settings */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/resolve-element-settings.ts");
/* harmony import */ var _utils_use_override_dependencies__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./utils/use-override-dependencies */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/use-override-dependencies.ts");
/* harmony import */ var _utils_use_resolved_inner_element__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./utils/use-resolved-inner-element */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/use-resolved-inner-element.ts");






















function OverridePropControl({
  overrideKey
}) {
  const overridableProps = (0,_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_9__.useComponentOverridableProps)();
  const overridableProp = overridableProps.props[overrideKey];
  if (!overridableProp) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.SettingsField, {
    bind: "component_instance",
    propDisplayName: overridableProp.label
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(OverrideControl, {
    overridableProp: overridableProp
  }));
}
function OverrideControl({
  overridableProp
}) {
  const componentInstanceElement = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.useElement)();
  const {
    value: instanceValue,
    setValue: setInstanceValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_7__.componentInstancePropTypeUtil);
  const wrappingComponentId = (0,_store_store__WEBPACK_IMPORTED_MODULE_12__.useCurrentComponentId)();
  const componentId = (0,_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_9__.useComponentId)();
  const overridableProps = (0,_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_9__.useComponentOverridableProps)();
  const overrides = (0,_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_9__.useComponentInstanceOverrides)();
  const controls = (0,_hooks_use_controls_by_widget_type__WEBPACK_IMPORTED_MODULE_4__.useControlsByWidgetType)(overridableProp.originPropFields?.widgetType ?? overridableProp.widgetType);
  const controlReplacements = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.getControlReplacements)();
  const matchingOverride = (0,_utils_overridable_props_utils__WEBPACK_IMPORTED_MODULE_14__.getMatchingOverride)(overrides, overridableProp.overrideKey);
  const {
    propKey
  } = overridableProp.originPropFields ?? overridableProp;
  const propType = (0,_utils_get_prop_type_for_component_override__WEBPACK_IMPORTED_MODULE_13__.getPropTypeForComponentOverride)(overridableProp);
  if (!propType) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_17__.OverrideControlPropTypeNotFoundError({
      context: {
        overridableProp
      }
    });
  }
  const {
    elementId,
    elementType,
    resolvedElementSettings,
    resolvedOriginValues
  } = (0,_utils_use_resolved_inner_element__WEBPACK_IMPORTED_MODULE_21__.useResolvedInnerElement)(overridableProp);
  const {
    overrideValue,
    isDisabled,
    isHidden
  } = (0,_utils_use_override_dependencies__WEBPACK_IMPORTED_MODULE_20__.useOverrideControlDependencies)({
    existingOverride: matchingOverride,
    resolvedElementSettings,
    elementType,
    elementId,
    propKey
  });
  if (isHidden) {
    return null;
  }
  const {
    propValue,
    baseValue: resolvedBaseValue
  } = resolveOverrideValues(overrideValue, resolvedOriginValues, propKey);
  const value = {
    [overridableProp.overrideKey]: propValue
  };
  const baseValue = {
    [overridableProp.overrideKey]: resolvedBaseValue
  };
  const {
    control,
    controlProps,
    layout
  } = getControlParams(controls, overridableProp.originPropFields ?? overridableProp, overridableProp.label);
  const propTypeSchema = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.createTopLevelObjectType)({
    schema: {
      [overridableProp.overrideKey]: propType
    }
  });
  const setValue = (newValue, options, meta) => {
    let newPropValue = getTempNewValueForDynamicProp(propType, propValue, newValue[overridableProp.overrideKey]);
    newPropValue = (0,_utils_correct_exposed_empty_override__WEBPACK_IMPORTED_MODULE_18__.correctExposedEmptyOverride)(newPropValue, matchingOverride);
    const newOverrideValue = createOverrideValue({
      matchingOverride,
      overrideKey: overridableProp.overrideKey,
      overrideValue: newPropValue,
      componentId
    });
    let newOverrides = (overrides ?? []).filter(override => isValidOverride(overridableProps, override)).map(override => override === matchingOverride ? newOverrideValue : override);
    if (!matchingOverride) {
      newOverrides = [...newOverrides, newOverrideValue];
    }
    setInstanceValue({
      ...instanceValue,
      overrides: _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_6__.componentInstanceOverridesPropTypeUtil.create(newOverrides)
    }, options, meta);
    const overridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__.componentOverridablePropTypeUtil.extract(newOverrideValue);
    if (overridableValue && wrappingComponentId) {
      if (overridableProp.originPropFields) {
        (0,_store_actions_update_overridable_prop__WEBPACK_IMPORTED_MODULE_11__.updateOverridableProp)(wrappingComponentId, overridableValue, overridableProp.originPropFields);
        return;
      }
      const originPropFields = {
        elType: overridableProp.elType,
        widgetType: overridableProp.widgetType,
        propKey: overridableProp.propKey,
        elementId: overridableProp.elementId
      };
      (0,_store_actions_update_overridable_prop__WEBPACK_IMPORTED_MODULE_11__.updateOverridableProp)(wrappingComponentId, overridableValue, originPropFields);
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_provider_overridable_prop_context__WEBPACK_IMPORTED_MODULE_10__.OverridablePropProvider, {
    value: _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__.componentOverridablePropTypeUtil.extract(matchingOverride) ?? undefined,
    componentInstanceElement: componentInstanceElement
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.ElementProvider, {
    element: {
      id: elementId,
      type: elementType.key
    },
    elementType: elementType,
    settings: resolvedElementSettings
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, {
    propType: propTypeSchema,
    value: value,
    setValue: setValue,
    baseValue: baseValue,
    isDisabled: isDisabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: overridableProp.overrideKey
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlReplacementsProvider, {
    replacements: controlReplacements
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    mb: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.ControlTypeContainer, {
    layout: layout
  }, layout !== 'custom' && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_16__.ControlLabel, null, overridableProp.label), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(OriginalControl, {
    control: control,
    controlProps: controlProps
  }))))))));
}
function resolveOverrideValues(overrideValue, resolvedOriginValues, propKey) {
  const unwrappedSettings = (0,_utils_resolve_element_settings__WEBPACK_IMPORTED_MODULE_19__.unwrapOverridableSettings)(resolvedOriginValues);
  const inheritedValue = unwrappedSettings[propKey] ?? null;
  const isInheritedDynamic = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.isDynamicPropValue)(inheritedValue);
  const shouldUseInheritedAsValue = isInheritedDynamic && !overrideValue;
  const propValue = shouldUseInheritedAsValue ? inheritedValue : overrideValue;
  const baseValue = overrideValue || isInheritedDynamic ? null : inheritedValue;
  return {
    propValue,
    baseValue
  };
}

// Temp solution: when removing an override on a dynamic value, fall back to propType.default
// instead of null, since we don't have placeholder support for dynamics yet.
function getTempNewValueForDynamicProp(propType, propValue, newPropValue) {
  const isRemovingOverride = newPropValue === null;
  if (isRemovingOverride && (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.isDynamicPropValue)(propValue)) {
    return propType.default ?? null;
  }
  return newPropValue;
}
function createOverrideValue({
  matchingOverride,
  overrideKey,
  overrideValue,
  componentId
}) {
  const overridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__.componentOverridablePropTypeUtil.extract(matchingOverride);
  const newOverridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__.componentOverridablePropTypeUtil.extract(overrideValue);
  const anyOverridable = newOverridableValue ?? overridableValue;
  if (anyOverridable) {
    const innerOverride = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_5__.componentInstanceOverridePropTypeUtil.create({
      override_key: overrideKey,
      override_value: (0,_utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_15__.resolveOverridePropValue)(overrideValue),
      schema_source: {
        type: 'component',
        id: componentId
      }
    });
    return _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__.componentOverridablePropTypeUtil.create({
      override_key: anyOverridable.override_key,
      origin_value: innerOverride
    });
  }
  return _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_5__.componentInstanceOverridePropTypeUtil.create({
    override_key: overrideKey,
    override_value: overrideValue,
    schema_source: {
      type: 'component',
      id: componentId
    }
  });
}
function getControlParams(controls, originPropFields, label) {
  const control = controls[originPropFields.propKey];
  const {
    value
  } = control;
  const layout = getControlLayout(control);
  const controlProps = populateChildControlProps(value.props);
  if (layout === 'custom') {
    controlProps.label = label ?? value.label;
  }
  return {
    control,
    controlProps,
    layout
  };
}
function OriginalControl({
  control,
  controlProps
}) {
  const {
    value
  } = control;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.BaseControl, {
    type: value.type,
    props: controlProps
  });
}
function getControlLayout(control) {
  return control.value.meta?.layout || _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.controlsRegistry.getLayout(control.value.type);
}
function populateChildControlProps(props) {
  if (props.childControlType) {
    const childComponent = _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.controlsRegistry.get(props.childControlType);
    const childPropType = _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.controlsRegistry.getPropTypeUtil(props.childControlType);
    props = {
      ...props,
      childControlConfig: {
        component: childComponent,
        props: props.childControlProps || {},
        propTypeUtil: childPropType
      }
    };
  }
  return props;
}
function isValidOverride(overridableProps, override) {
  const overridableKey = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_8__.componentOverridablePropTypeUtil.isValid(override) ? override.value.origin_value?.value.override_key : override.value.override_key;
  return !!overridableProps.props[overridableKey];
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/override-props-group.tsx":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/override-props-group.tsx ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OverridePropsGroup: function() { return /* binding */ OverridePropsGroup; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _override_prop_control__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./override-prop-control */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/override-prop-control.tsx");






function OverridePropsGroup({
  group
}) {
  const [isOpen, setIsOpen] = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.useStateByElement)(group.id, true);
  const handleClick = () => {
    setIsOpen(!isOpen);
  };
  const id = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  const labelId = `label-${id}`;
  const contentId = `content-${id}`;
  const title = group.label;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    "aria-label": `${title} section`
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemButton, {
    id: labelId,
    "aria-controls": contentId,
    "aria-label": `${title} section`,
    onClick: handleClick,
    p: 0,
    sx: {
      '&:hover': {
        backgroundColor: 'transparent'
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyItems: "start",
    flexGrow: 1,
    gap: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemText, {
    secondary: title,
    secondaryTypographyProps: {
      color: 'text.primary',
      variant: 'caption',
      fontWeight: 'bold'
    },
    sx: {
      flexGrow: 0,
      flexShrink: 1,
      marginInlineEnd: 1
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.CollapseIcon, {
    open: isOpen,
    color: "secondary",
    fontSize: "tiny"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Collapse, {
    id: contentId,
    "aria-labelledby": labelId,
    in: isOpen,
    timeout: "auto"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "column",
    gap: 1,
    p: 2
  }, group.props.map(overrideKey => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_override_prop_control__WEBPACK_IMPORTED_MODULE_4__.OverridePropControl, {
    key: overrideKey,
    overrideKey: overrideKey
  })))));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/use-instance-panel-data.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/use-instance-panel-data.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useInstancePanelData: function() { return /* binding */ useInstancePanelData; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_sanitize_overridable_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../hooks/use-sanitize-overridable-props */ "./packages/packages/core/editor-components/src/hooks/use-sanitize-overridable-props.ts");
/* harmony import */ var _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../prop-types/component-instance-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts");
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");




function useInstancePanelData() {
  const {
    element,
    settings
  } = useComponentInstanceSettings();
  const componentId = settings?.component_id?.value;
  const overrides = settings?.overrides?.value;
  const component = (0,_store_store__WEBPACK_IMPORTED_MODULE_3__.useComponent)(componentId ?? null);
  const componentInstanceId = element?.id;
  const overridableProps = (0,_hooks_use_sanitize_overridable_props__WEBPACK_IMPORTED_MODULE_1__.useSanitizeOverridableProps)(componentId ?? null, componentInstanceId);
  if (!componentId || !overridableProps || !component || !componentInstanceId) {
    return null;
  }
  const isNonEmptyGroup = group => group !== null && group.props.length > 0;
  const groups = overridableProps.groups.order.map(groupId => overridableProps.groups.items[groupId] ?? null).filter(isNonEmptyGroup);
  const isEmpty = groups.length === 0 || Object.keys(overridableProps.props).length === 0;
  return {
    componentId,
    component,
    overrides,
    overridableProps,
    groups,
    isEmpty,
    componentInstanceId
  };
}
function useComponentInstanceSettings() {
  const {
    element,
    settings
  } = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_0__.useElement)();
  return {
    element,
    settings: _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentInstancePropTypeUtil.extract(settings.component_instance)
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/correct-exposed-empty-override.ts":
/*!********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/correct-exposed-empty-override.ts ***!
  \********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   correctExposedEmptyOverride: function() { return /* binding */ correctExposedEmptyOverride; }
/* harmony export */ });
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");

// The control receives the resolved value, so when exposing a prop that was never overridden,
// origin_value will be the resolved value instead of null.
// So here, we correct this by resetting origin_value to null.
function correctExposedEmptyOverride(newPropValue, matchingOverride) {
  const newOverridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentOverridablePropTypeUtil.extract(newPropValue);
  const isExposingEmptyOverride = newOverridableValue && matchingOverride === null;
  if (!isExposingEmptyOverride) {
    return newPropValue;
  }
  return _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentOverridablePropTypeUtil.create({
    override_key: newOverridableValue.override_key,
    origin_value: null
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/resolve-element-settings.ts":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/resolve-element-settings.ts ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   applyOverridesToSettings: function() { return /* binding */ applyOverridesToSettings; },
/* harmony export */   unwrapOverridableSettings: function() { return /* binding */ unwrapOverridableSettings; }
/* harmony export */ });
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");

function applyOverridesToSettings(elementSettings, overrides) {
  const result = {};
  for (const [propKey, propValue] of Object.entries(elementSettings)) {
    const overridable = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentOverridablePropTypeUtil.extract(propValue);
    if (!overridable) {
      result[propKey] = propValue;
      continue;
    }
    const override = overrides[overridable.override_key];
    if (!override) {
      result[propKey] = propValue;
      continue;
    }
    if (override.outermostKey && override.outermostKey !== overridable.override_key) {
      const originValue = overridable.origin_value;
      result[propKey] = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentOverridablePropTypeUtil.create({
        override_key: override.outermostKey,
        origin_value: override.value ?? originValue
      });
    } else {
      result[propKey] = override.value ?? propValue;
    }
  }
  return result;
}
function unwrapOverridableSettings(elementSettings) {
  const result = {};
  for (const [propKey, propValue] of Object.entries(elementSettings)) {
    const overridable = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentOverridablePropTypeUtil.extract(propValue);
    if (!overridable) {
      result[propKey] = propValue;
      continue;
    }
    result[propKey] = overridable.origin_value;
  }
  return result;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/use-override-dependencies.ts":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/use-override-dependencies.ts ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOverrideControlDependencies: function() { return /* binding */ useOverrideControlDependencies; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../utils/resolve-override-prop-value */ "./packages/packages/core/editor-components/src/utils/resolve-override-prop-value.ts");



function useOverrideControlDependencies({
  existingOverride,
  resolvedElementSettings,
  elementId,
  elementType,
  propKey
}) {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const {
      isDisabled,
      isHidden
    } = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.extractDependencyEffect)(propKey, elementType.propsSchema, resolvedElementSettings);
    const existingOverrideValue = existingOverride ? (0,_utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_2__.resolveOverridePropValue)(existingOverride) : null;
    const settingsForDepsNewValuesCalculation = {
      ...resolvedElementSettings,
      [propKey]: existingOverrideValue
    };
    const resolvedSettingsWithDefaults = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.getElementSettingsWithDefaults)(elementType.propsSchema, settingsForDepsNewValuesCalculation);
    const dependents = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.extractOrderedDependencies)(elementType.dependenciesPerTargetMapping ?? {});
    const settingsWithDepsNewValues = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.getUpdatedValues)({
      [propKey]: existingOverrideValue
    }, dependents, elementType.propsSchema, resolvedSettingsWithDefaults, elementId);
    const overrideValue = settingsWithDepsNewValues[propKey];
    return {
      overrideValue,
      isDisabled,
      isHidden
    };
  }, [existingOverride, resolvedElementSettings, propKey, elementType.propsSchema, elementType.dependenciesPerTargetMapping, elementId]);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/use-resolved-inner-element.ts":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/use-resolved-inner-element.ts ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useResolvedInnerElement: function() { return /* binding */ useResolvedInnerElement; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");
/* harmony import */ var _provider_component_instance_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../provider/component-instance-context */ "./packages/packages/core/editor-components/src/provider/component-instance-context.tsx");
/* harmony import */ var _utils_resolve_overrides_chain__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../utils/resolve-overrides-chain */ "./packages/packages/core/editor-components/src/utils/resolve-overrides-chain.ts");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../errors */ "./packages/packages/core/editor-components/src/components/errors.ts");
/* harmony import */ var _resolve_element_settings__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./resolve-element-settings */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/utils/resolve-element-settings.ts");









function useResolvedInnerElement(overridableProp) {
  const componentInstanceElement = (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.useElement)();
  const componentId = (0,_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_5__.useComponentId)();
  const overrides = (0,_provider_component_instance_context__WEBPACK_IMPORTED_MODULE_5__.useComponentInstanceOverrides)();
  const {
    elementId: originElementId,
    widgetType,
    elType
  } = overridableProp.originPropFields ?? overridableProp;
  const type = elType === 'widget' ? widgetType : elType;
  const elementType = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.getElementType)(type);
  if (!elementType) {
    throw new Error(`Element type not found for ${type}`);
  }
  const {
    elementId,
    overridesMapping
  } = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const overridesChainResult = (0,_utils_resolve_overrides_chain__WEBPACK_IMPORTED_MODULE_6__.resolveOverridesChain)({
      outerOverridableProp: overridableProp,
      outerInstanceId: componentInstanceElement.element.id
    });
    if (overridesChainResult.isChainBroken) {
      throw new _errors__WEBPACK_IMPORTED_MODULE_7__.OverrideControlInnerElementNotFoundError({
        context: {
          componentId,
          elementId: originElementId
        }
      });
    }
    return {
      elementId: overridesChainResult.innerElement.id,
      overridesMapping: overridesChainResult.overridesMapping
    };
  }, [overridableProp, componentInstanceElement.element.id, componentId, originElementId]);

  // Not reactive to inner element store changes — intentional.
  // Inner element settings can only change in component edit mode, which unmounts this component.
  const settingsWithInnerOverrides = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const settings = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.getElementSettings)(elementId, Object.keys(elementType?.propsSchema ?? {}));
    return (0,_resolve_element_settings__WEBPACK_IMPORTED_MODULE_8__.applyOverridesToSettings)(settings, overridesMapping);
  }, [elementId, elementType?.propsSchema, overridesMapping]);
  const resolvedElementSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const withAllOverrides = (0,_resolve_element_settings__WEBPACK_IMPORTED_MODULE_8__.applyOverridesToSettings)(settingsWithInnerOverrides, formatOverridesToApply(overrides));
    return (0,_resolve_element_settings__WEBPACK_IMPORTED_MODULE_8__.unwrapOverridableSettings)(withAllOverrides);
  }, [settingsWithInnerOverrides, overrides]);
  return {
    elementId,
    elementType,
    resolvedOriginValues: settingsWithInnerOverrides,
    resolvedElementSettings
  };
}
function formatOverridesToApply(overrides) {
  if (!overrides) {
    return {};
  }
  const result = {};
  for (const item of overrides) {
    const overridable = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_4__.componentOverridablePropTypeUtil.extract(item);
    let override = item;
    if (overridable) {
      override = overridable.origin_value;
    }
    const extractedOverride = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_3__.componentInstanceOverridePropTypeUtil.extract(override);
    if (!extractedOverride) {
      continue;
    }
    result[extractedOverride.override_key] = {
      value: extractedOverride.override_value
    };
  }
  return result;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/components/load-template-components.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/components/load-template-components.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LoadTemplateComponents: function() { return /* binding */ LoadTemplateComponents; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_templates__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-templates */ "@elementor/editor-templates");
/* harmony import */ var _elementor_editor_templates__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_templates__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../store/actions/load-components-assets */ "./packages/packages/core/editor-components/src/store/actions/load-components-assets.ts");




const LoadTemplateComponents = () => {
  if ((0,_elementor_editor_templates__WEBPACK_IMPORTED_MODULE_1__.isHandlingTemplateStyles)()) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LoadTemplateComponentsInternal, null);
  }
  return null;
};
function LoadTemplateComponentsInternal() {
  const templates = (0,_elementor_editor_templates__WEBPACK_IMPORTED_MODULE_1__.useLoadedTemplates)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    (0,_store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_2__.loadComponentsAssets)(templates.flatMap(elements => elements ?? []));
  }, [templates]);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/consts.ts":
/*!****************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/consts.ts ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COMPONENT_WIDGET_TYPE: function() { return /* binding */ COMPONENT_WIDGET_TYPE; }
/* harmony export */ });
const COMPONENT_WIDGET_TYPE = 'e-component';

/***/ }),

/***/ "./packages/packages/core/editor-components/src/create-component-type.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/create-component-type.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COMPONENT_WIDGET_TYPE: function() { return /* binding */ COMPONENT_WIDGET_TYPE; },
/* harmony export */   createComponentType: function() { return /* binding */ createComponentType; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-components/src/api.ts");
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _utils_detach_component_instance__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/detach-component-instance */ "./packages/packages/core/editor-components/src/utils/detach-component-instance/index.ts");
/* harmony import */ var _utils_format_component_elements_id__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils/format-component-elements-id */ "./packages/packages/core/editor-components/src/utils/format-component-elements-id.ts");
/* harmony import */ var _utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./utils/is-pro-components-supported */ "./packages/packages/core/editor-components/src/utils/is-pro-components-supported.ts");
/* harmony import */ var _utils_switch_to_component__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./utils/switch-to-component */ "./packages/packages/core/editor-components/src/utils/switch-to-component.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-components/src/utils/tracking.ts");













const COMPONENT_WIDGET_TYPE = 'e-component';
const EDIT_COMPONENT_DB_CLICK_UPGRADE_URL = 'https://go.elementor.com/go-pro-components-Instance-edit-canvas-double-click/';
const EDIT_COMPONENT_CONTEXT_MENU_UPGRADE_URL = 'https://go.elementor.com/go-pro-components-Instance-edit-context-menu/';
const UPDATE_PLUGINS_URL = '/wp-admin/plugins.php';
const COMPONENT_EDIT_UPGRADE_NOTIFICATION_ID = 'component-edit-upgrade';
const COMPONENT_EDIT_UPDATE_NOTIFICATION_ID = 'component-edit-update';
const COMPONENT_EDIT_UPGRADE_AUTO_HIDE_DURATION = 2000;
function notifyComponentEditUpgrade() {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__.notify)({
    type: 'promotion',
    id: COMPONENT_EDIT_UPGRADE_NOTIFICATION_ID,
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Editing components requires an active Pro subscription.', 'elementor'),
    autoHideDuration: COMPONENT_EDIT_UPGRADE_AUTO_HIDE_DURATION,
    additionalActionProps: [{
      size: 'small',
      variant: 'contained',
      color: 'promotion',
      href: EDIT_COMPONENT_DB_CLICK_UPGRADE_URL,
      target: '_blank',
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Upgrade Now', 'elementor')
    }]
  });
}
function notifyComponentEditUpdate() {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__.notify)({
    type: 'info',
    id: COMPONENT_EDIT_UPDATE_NOTIFICATION_ID,
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('To edit components, update Elementor Pro to the latest version.', 'elementor'),
    additionalActionProps: [{
      size: 'small',
      variant: 'contained',
      color: 'info',
      href: UPDATE_PLUGINS_URL,
      target: '_blank',
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Update Now', 'elementor')
    }]
  });
}
const updateGroups = (groups, config) => {
  const disableMap = new Map(Object.entries(config.disable ?? {}));
  const addMap = new Map(Object.entries(config.add ?? {}));
  return groups.map(group => {
    const disabledActions = disableMap.get(group.name) ?? [];
    const addConfig = addMap.get(group.name);

    // Update disabled actions
    const updatedActions = group.actions.map(action => disabledActions.includes(action.name) ? {
      ...action,
      isEnabled: () => false
    } : action);

    // Insert additional actions if needed
    if (addConfig) {
      updatedActions.splice(addConfig.index, 0, ...addConfig.actions);
    }
    return {
      ...group,
      actions: updatedActions
    };
  });
};
function createComponentType(options) {
  const legacyWindow = window;
  const WidgetType = legacyWindow.elementor.modules.elements.types.Widget;
  const view = createComponentView({
    ...options
  });
  return class extends WidgetType {
    getType() {
      return options.type;
    }
    getView() {
      return view;
    }
    getModel() {
      return createComponentModel();
    }
  };
}
function createComponentView(options) {
  const legacyWindow = window;
  return class extends (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTemplatedElementView)(options) {
    eventsManagerConfig = legacyWindow.elementorCommon.eventsManager.config;
    #componentRenderContext;
    isComponentCurrentlyEdited() {
      const currentDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.getCurrentDocument)();
      return currentDocument?.id === this.getComponentId();
    }
    getRenderContext() {
      const namespaceKey = this.getNamespaceKey();
      const parentContext = this._parent?.getRenderContext?.();
      const parentComponentContext = parentContext?.[namespaceKey];
      if (!this.#componentRenderContext) {
        return parentContext;
      }
      const ownOverrides = this.#componentRenderContext.overrides ?? {};
      const parentOverrides = parentComponentContext?.overrides ?? {};
      return {
        ...parentContext,
        [namespaceKey]: {
          overrides: {
            ...parentOverrides,
            ...ownOverrides
          }
        }
      };
    }
    getResolverRenderContext() {
      const namespaceKey = this.getNamespaceKey();
      const context = this.getRenderContext();
      const ownContext = context?.[namespaceKey];
      if (!ownContext) {
        return this._parent?.getResolverRenderContext?.();
      }
      const parentResolverContext = this._parent?.getResolverRenderContext?.();
      return {
        ...parentResolverContext,
        ...ownContext
      };
    }
    afterSettingsResolve(settings) {
      const componentInstance = settings.component_instance;
      if (componentInstance) {
        this.#componentRenderContext = {
          overrides: componentInstance.overrides ?? {}
        };
        const instanceId = this.model.get('id');
        const elements = componentInstance.elements ?? [];
        const formattedElements = (0,_utils_format_component_elements_id__WEBPACK_IMPORTED_MODULE_9__.formatComponentElementsId)(elements, [instanceId]);
        this.collection = legacyWindow.elementor.createBackboneElementsCollection(formattedElements);
        this.collection.models.forEach(setInactiveRecursively);
        settings.component_instance = '<template data-children-placeholder></template>';
      }
      return settings;
    }
    getDomElement() {
      // Component does not have a DOM element, so we return the first child's DOM element.
      return this.children.findByIndex(0)?.getDomElement() ?? this.$el;
    }
    attachBuffer(collectionView, buffer) {
      const childrenPlaceholder = collectionView.$el.find('[data-children-placeholder]').get(0);
      if (!childrenPlaceholder) {
        super.attachBuffer(collectionView, buffer);
        return;
      }
      childrenPlaceholder.replaceWith(buffer);
    }
    getComponentId() {
      const componentInstance = this.options?.model?.get('settings')?.get('component_instance')?.value;
      return componentInstance.component_id.value;
    }
    getContextMenuGroups() {
      const filteredGroups = super.getContextMenuGroups().filter(group => group.name !== 'save');
      const componentId = this.getComponentId();
      if (!componentId) {
        return filteredGroups;
      }
      const newGroups = updateGroups(filteredGroups, this._getContextMenuConfig());
      return newGroups;
    }
    _getContextMenuConfig() {
      const isAdministrator = isUserAdministrator();
      const hasPro = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_4__.hasProInstalled)();
      const isOutdated = (0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_10__.isProOutdatedForComponents)();
      const showPromoBadge = !hasPro && !isOutdated;
      const badgeClass = 'elementor-context-menu-list__item__shortcut__promotion-badge';
      const proBadge = `<a href="${EDIT_COMPONENT_CONTEXT_MENU_UPGRADE_URL}" target="_blank" onclick="event.stopPropagation()" class="${badgeClass}"><i class="eicon-upgrade-crown"></i></a>`;
      const editComponentAction = {
        name: 'edit component',
        icon: 'eicon-edit',
        title: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Edit Component', 'elementor'),
        ...(showPromoBadge && {
          shortcut: proBadge,
          hasShortcutAction: true
        }),
        isEnabled: () => (0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_10__.isProComponentsSupported)() || isOutdated,
        callback: (_, eventData) => this.editComponent(eventData)
      };
      const detachInstanceAction = {
        name: 'detach instance',
        icon: 'eicon-chain-broken',
        title: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Detach from Component', 'elementor'),
        isEnabled: () => true,
        callback: (_, eventData) => this.detachInstance(eventData)
      };
      const actions = isAdministrator ? [editComponentAction, detachInstanceAction] : [detachInstanceAction];
      const addedGroup = {
        general: {
          index: 1,
          actions
        }
      };
      const disabledGroup = {
        clipboard: ['pasteStyle', 'resetStyle']
      };
      return {
        add: addedGroup,
        disable: disabledGroup
      };
    }
    async switchDocument() {
      //todo: handle unpublished
      const {
        isAllowedToSwitchDocument,
        lockedBy
      } = await _api__WEBPACK_IMPORTED_MODULE_6__.apiClient.getComponentLockStatus(this.getComponentId());
      if (!isAllowedToSwitchDocument) {
        options.showLockedByModal?.(lockedBy || '');
      } else {
        (0,_utils_switch_to_component__WEBPACK_IMPORTED_MODULE_11__.switchToComponent)(this.getComponentId(), this.model.get('id'), this.el);
      }
    }
    editComponent({
      trigger,
      location,
      secondaryLocation
    }) {
      if ((0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_10__.isProOutdatedForComponents)()) {
        notifyComponentEditUpdate();
        return;
      }
      if (!(0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_10__.isProComponentsSupported)() || this.isComponentCurrentlyEdited()) {
        return;
      }
      this.switchDocument();
      const editorSettings = this.model.get('editor_settings');
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_12__.trackComponentEvent)({
        action: 'edited',
        executedBy: 'user',
        component_uid: editorSettings?.component_uid,
        component_name: editorSettings?.title,
        location,
        secondary_location: secondaryLocation,
        trigger
      });
    }
    detachInstance({
      trigger,
      location,
      secondaryLocation
    }) {
      const componentId = this.getComponentId();
      const instanceId = this.model.get('id');
      if (!componentId || !instanceId) {
        return;
      }
      const handleConfirm = async () => {
        try {
          await (0,_utils_detach_component_instance__WEBPACK_IMPORTED_MODULE_8__.detachComponentInstance)({
            instanceId,
            componentId,
            trackingInfo: {
              location,
              secondaryLocation,
              trigger
            }
          });
        } catch {
          (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_2__.notify)({
            type: 'error',
            message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Failed to detach component instance.', 'elementor'),
            id: 'detach-component-instance-failed'
          });
        }
      };
      options.showDetachConfirmDialog?.(handleConfirm);
    }
    handleDblClick(e) {
      e.stopPropagation();
      if (!isUserAdministrator()) {
        return;
      }
      if ((0,_utils_is_pro_components_supported__WEBPACK_IMPORTED_MODULE_10__.isProOutdatedForComponents)()) {
        notifyComponentEditUpdate();
        return;
      }
      if (!(0,_elementor_utils__WEBPACK_IMPORTED_MODULE_4__.hasProInstalled)()) {
        notifyComponentEditUpgrade();
        return;
      }
      const {
        triggers,
        locations,
        secondaryLocations
      } = this.eventsManagerConfig;
      this.editComponent({
        trigger: triggers.doubleClick,
        location: locations.canvas,
        secondaryLocation: secondaryLocations.canvasElement
      });
    }
    events() {
      return {
        ...super.events(),
        dblclick: this.handleDblClick
      };
    }
    attributes() {
      return {
        ...super.attributes(),
        'data-elementor-id': this.getComponentId()
      };
    }
  };
}
function setInactiveRecursively(model) {
  const editSettings = model.get('editSettings');
  if (editSettings) {
    editSettings.set('inactive', true);
  }
  const elements = model.get('elements');
  if (elements) {
    elements.forEach(childModel => {
      setInactiveRecursively(childModel);
    });
  }
}
function isUserAdministrator() {
  const legacyWindow = window;
  return legacyWindow.elementor.config?.user?.is_administrator ?? false;
}
function createComponentModel() {
  const legacyWindow = window;
  const WidgetType = legacyWindow.elementor.modules.elements.types.Widget;
  const widgetTypeInstance = new WidgetType();
  const BaseWidgetModel = widgetTypeInstance.getModel();
  return BaseWidgetModel.extend({
    initialize(attributes, options) {
      BaseWidgetModel.prototype.initialize.call(this, attributes, options);
      const componentInstance = this.get('settings')?.get('component_instance');
      if (componentInstance?.value) {
        const componentId = componentInstance.value.component_id?.value;
        if (componentId && typeof componentId === 'number') {
          this.set('componentId', componentId);
        }
      }
      this.set('isGlobal', true);
    },
    getTitle() {
      const editorSettings = this.get('editor_settings');
      const instanceTitle = editorSettings?.title;
      if (instanceTitle) {
        return instanceTitle;
      }
      const componentUid = editorSettings?.component_uid;
      if (componentUid) {
        const component = (0,_store_store__WEBPACK_IMPORTED_MODULE_7__.selectComponentByUid)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__getState)(), componentUid);
        if (component?.name) {
          return component.name;
        }
      }
      return window.elementor.getElementData(this).title;
    },
    getComponentId() {
      return this.get('componentId') || null;
    },
    getComponentName() {
      return this.getTitle();
    },
    getComponentUid() {
      const editorSettings = this.get('editor_settings');
      return editorSettings?.component_uid || null;
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/hooks/use-components-permissions.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/hooks/use-components-permissions.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useComponentsPermissions: function() { return /* binding */ useComponentsPermissions; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__);

const useComponentsPermissions = () => {
  const {
    isAdmin
  } = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__.useCurrentUserCapabilities)();
  return {
    canCreate: isAdmin,
    canEdit: isAdmin,
    canDelete: isAdmin,
    canRename: isAdmin
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/hooks/use-components.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/hooks/use-components.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useComponents: function() { return /* binding */ useComponents; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");


const useComponents = () => {
  const components = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store_store__WEBPACK_IMPORTED_MODULE_1__.selectComponents);
  const isLoading = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store_store__WEBPACK_IMPORTED_MODULE_1__.selectLoadIsPending);
  return {
    components,
    isLoading
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/hooks/use-controls-by-widget-type.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/hooks/use-controls-by-widget-type.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useControlsByWidgetType: function() { return /* binding */ useControlsByWidgetType; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

function useControlsByWidgetType(type) {
  const elementType = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementType)(type);
  if (!elementType) {
    return {};
  }
  const controls = iterateControls(elementType.controls);
  return getControlsByBind(controls);
}
function iterateControls(controls) {
  return controls.map(control => {
    if (control.type === 'control' && 'bind' in control.value) {
      return control;
    }
    if (control.type === 'section') {
      return iterateControls(control.value.items);
    }
    return null;
  }).filter(Boolean).flat();
}
function getControlsByBind(controls) {
  return controls.reduce((controlsByBind, control) => ({
    ...controlsByBind,
    [control.value.bind]: control
  }), {});
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/hooks/use-sanitize-overridable-props.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/hooks/use-sanitize-overridable-props.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSanitizeOverridableProps: function() { return /* binding */ useSanitizeOverridableProps; }
/* harmony export */ });
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _utils_filter_valid_overridable_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/filter-valid-overridable-props */ "./packages/packages/core/editor-components/src/utils/filter-valid-overridable-props.ts");


function useSanitizeOverridableProps(componentId, instanceElementId) {
  const overridableProps = (0,_store_store__WEBPACK_IMPORTED_MODULE_0__.useOverridableProps)(componentId);
  const isSanitized = (0,_store_store__WEBPACK_IMPORTED_MODULE_0__.useIsSanitizedComponent)(componentId, 'overridableProps');
  if (!overridableProps || !componentId) {
    return undefined;
  }
  if (isSanitized) {
    return overridableProps;
  }
  return (0,_utils_filter_valid_overridable_props__WEBPACK_IMPORTED_MODULE_1__.filterValidOverridableProps)(overridableProps, instanceElementId);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/init.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/init.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_elements_panel__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-elements-panel */ "@elementor/editor-elements-panel");
/* harmony import */ var _elementor_editor_elements_panel__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements_panel__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/editor-embedded-documents-manager */ "@elementor/editor-embedded-documents-manager");
/* harmony import */ var _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _component_instance_transformer__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./component-instance-transformer */ "./packages/packages/core/editor-components/src/component-instance-transformer.ts");
/* harmony import */ var _component_overridable_transformer__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./component-overridable-transformer */ "./packages/packages/core/editor-components/src/component-overridable-transformer.ts");
/* harmony import */ var _component_override_transformer__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./component-override-transformer */ "./packages/packages/core/editor-components/src/component-override-transformer.ts");
/* harmony import */ var _components_components_tab_components__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/components-tab/components */ "./packages/packages/core/editor-components/src/components/components-tab/components.tsx");
/* harmony import */ var _components_detach_instance_confirmation_dialog__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/detach-instance-confirmation-dialog */ "./packages/packages/core/editor-components/src/components/detach-instance-confirmation-dialog.tsx");
/* harmony import */ var _components_in_edit_mode__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./components/in-edit-mode */ "./packages/packages/core/editor-components/src/components/in-edit-mode.tsx");
/* harmony import */ var _components_instance_editing_panel_instance_editing_panel__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./components/instance-editing-panel/instance-editing-panel */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-editing-panel.tsx");
/* harmony import */ var _components_load_template_components__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./components/load-template-components */ "./packages/packages/core/editor-components/src/components/load-template-components.tsx");
/* harmony import */ var _create_component_type__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./create-component-type */ "./packages/packages/core/editor-components/src/create-component-type.ts");
/* harmony import */ var _populate_store__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./populate-store */ "./packages/packages/core/editor-components/src/populate-store.ts");
/* harmony import */ var _prevent_circular_nesting__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./prevent-circular-nesting */ "./packages/packages/core/editor-components/src/prevent-circular-nesting.ts");
/* harmony import */ var _store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./store/actions/load-components-assets */ "./packages/packages/core/editor-components/src/store/actions/load-components-assets.ts");
/* harmony import */ var _store_actions_remove_component_styles__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./store/actions/remove-component-styles */ "./packages/packages/core/editor-components/src/store/actions/remove-component-styles.ts");
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _sync_before_save__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./sync/before-save */ "./packages/packages/core/editor-components/src/sync/before-save.ts");
/* harmony import */ var _sync_load_component_data_after_instance_added__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./sync/load-component-data-after-instance-added */ "./packages/packages/core/editor-components/src/sync/load-component-data-after-instance-added.ts");

























function init() {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_7__.__registerSlice)(_store_store__WEBPACK_IMPORTED_MODULE_22__.slice);
  (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.registerElementType)(_create_component_type__WEBPACK_IMPORTED_MODULE_17__.COMPONENT_WIDGET_TYPE, options => (0,_create_component_type__WEBPACK_IMPORTED_MODULE_17__.createComponentType)({
    ...options,
    showLockedByModal: _components_in_edit_mode__WEBPACK_IMPORTED_MODULE_14__.openEditModeDialog,
    showDetachConfirmDialog: _components_detach_instance_confirmation_dialog__WEBPACK_IMPORTED_MODULE_13__.openDetachConfirmDialog
  }));
  window.elementorCommon.__beforeSave = _sync_before_save__WEBPACK_IMPORTED_MODULE_23__.beforeSave;
  (0,_elementor_editor_elements_panel__WEBPACK_IMPORTED_MODULE_4__.injectTab)({
    id: 'components',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__.__)('Components', 'elementor'),
    component: _components_components_tab_components__WEBPACK_IMPORTED_MODULE_12__.Components,
    position: 1
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'components-populate-store',
    component: _populate_store__WEBPACK_IMPORTED_MODULE_18__.PopulateStore
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_6__.registerDataHook)('after', 'editor/documents/attach-preview', () => {
    const {
      id,
      config
    } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__.getV1CurrentDocument)() ?? {};
    if (!id) {
      return;
    }
    (0,_store_actions_remove_component_styles__WEBPACK_IMPORTED_MODULE_21__.removeComponentStyles)(id);
    void (0,_store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_20__.loadComponentsAssets)(config?.elements ?? []);
  });
  _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_5__.embeddedDocumentsManager.onDocumentLoad((_documentId, data) => {
    void (0,_store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_20__.loadComponentsAssets)(data.elements ?? []);
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'templates',
    component: _components_load_template_components__WEBPACK_IMPORTED_MODULE_16__.LoadTemplateComponents
  });
  (0,_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_3__.registerEditingPanelReplacement)({
    id: 'component-instance-edit-panel',
    condition: (_, elementType) => elementType.key === 'e-component',
    component: _components_instance_editing_panel_instance_editing_panel__WEBPACK_IMPORTED_MODULE_15__.InstanceEditingPanel
  });
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.settingsTransformersRegistry.register('component-instance', _component_instance_transformer__WEBPACK_IMPORTED_MODULE_9__.componentInstanceTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.settingsTransformersRegistry.register('overridable', _component_overridable_transformer__WEBPACK_IMPORTED_MODULE_10__.componentOverridableTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.settingsTransformersRegistry.register('override', _component_override_transformer__WEBPACK_IMPORTED_MODULE_11__.componentOverrideTransformer);
  (0,_prevent_circular_nesting__WEBPACK_IMPORTED_MODULE_19__.initCircularNestingPrevention)();
  (0,_sync_load_component_data_after_instance_added__WEBPACK_IMPORTED_MODULE_24__.initLoadComponentDataAfterInstanceAdded)();
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/populate-store.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/populate-store.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopulateStore: function() { return /* binding */ PopulateStore; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store_thunks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store/thunks */ "./packages/packages/core/editor-components/src/store/thunks.ts");



function PopulateStore() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)((0,_store_thunks__WEBPACK_IMPORTED_MODULE_2__.loadComponents)());
  }, []);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/prevent-circular-nesting.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/prevent-circular-nesting.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extractComponentIdsFromElements: function() { return /* binding */ extractComponentIdsFromElements; },
/* harmony export */   initCircularNestingPrevention: function() { return /* binding */ initCircularNestingPrevention; },
/* harmony export */   wouldCreateCircularNesting: function() { return /* binding */ wouldCreateCircularNesting; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./store/store */ "./packages/packages/core/editor-components/src/store/store.ts");






const COMPONENT_TYPE = 'e-component';
const COMPONENT_CIRCULAR_NESTING_ALERT = {
  type: 'default',
  message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)("Can't add this component - components that contain each other can't be nested.", 'elementor'),
  id: 'circular-component-nesting-blocked'
};
function initCircularNestingPrevention() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'document/elements/create',
    condition: blockCircularCreate
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'document/elements/move',
    condition: blockCircularMove
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'document/elements/paste',
    condition: blockCircularPaste
  });
}

// Note that this function only checks for direct circular nesting, not indirect nesting
function wouldCreateCircularNesting(componentIdToAdd) {
  if (componentIdToAdd === undefined) {
    return false;
  }
  const state = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__getState)();
  const currentComponentId = (0,_store_store__WEBPACK_IMPORTED_MODULE_5__.selectCurrentComponentId)(state);
  const path = (0,_store_store__WEBPACK_IMPORTED_MODULE_5__.selectPath)(state);
  if (currentComponentId === null) {
    return false;
  }
  if (componentIdToAdd === currentComponentId) {
    return true;
  }
  return path.some(item => item.componentId === componentIdToAdd);
}
function extractComponentIdFromModel(model) {
  if (!model) {
    return null;
  }
  const isComponent = model.widgetType === COMPONENT_TYPE;
  if (!isComponent) {
    return null;
  }
  return model.settings?.component_instance?.value?.component_id?.value ?? null;
}
function extractComponentIdFromElement(element) {
  if (element.widgetType !== COMPONENT_TYPE) {
    return null;
  }
  return element.settings?.component_instance?.value?.component_id?.value ?? null;
}
function extractComponentIdsFromElements(elements) {
  const ids = [];
  for (const element of elements) {
    const componentId = extractComponentIdFromElement(element);
    if (componentId !== null) {
      ids.push(componentId);
    }
    if (element.elements?.length) {
      ids.push(...extractComponentIdsFromElements(element.elements));
    }
  }
  return ids;
}
function extractComponentIdFromContainer(container) {
  const widgetType = container.model?.get?.('widgetType');
  if (widgetType !== COMPONENT_TYPE) {
    return null;
  }
  const settings = container.model?.get?.('settings');
  const componentInstance = settings?.get?.('component_instance');
  return componentInstance?.value?.component_id?.value ?? null;
}
function blockCircularCreate(args) {
  const componentId = extractComponentIdFromModel(args.model);
  if (componentId === null) {
    return false;
  }
  const isBlocked = wouldCreateCircularNesting(componentId);
  if (isBlocked) {
    (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)(COMPONENT_CIRCULAR_NESTING_ALERT);
  }
  return isBlocked;
}
function blockCircularMove(args) {
  const {
    containers = [args.container]
  } = args;
  const hasCircularComponent = containers.some(container => {
    if (!container) {
      return false;
    }
    const allElements = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getAllDescendants)(container);
    return allElements.some(element => {
      const componentId = extractComponentIdFromContainer(element);
      if (componentId === null) {
        return false;
      }
      return wouldCreateCircularNesting(componentId);
    });
  });
  if (hasCircularComponent) {
    (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)(COMPONENT_CIRCULAR_NESTING_ALERT);
  }
  return hasCircularComponent;
}
function blockCircularPaste(args) {
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
  const allComponentIds = extractComponentIdsFromElements(data.clipboard.elements);
  const hasCircularComponent = allComponentIds.some(wouldCreateCircularNesting);
  if (hasCircularComponent) {
    (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)(COMPONENT_CIRCULAR_NESTING_ALERT);
  }
  return hasCircularComponent;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentInstanceOverridePropTypeUtil: function() { return /* binding */ componentInstanceOverridePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const componentInstanceOverridePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('override', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.object({
  override_key: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string(),
  override_value: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.unknown(),
  schema_source: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.object({
    type: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.literal('component'),
    id: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.number()
  })
}));

/***/ }),

/***/ "./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentInstanceOverridesPropTypeUtil: function() { return /* binding */ componentInstanceOverridesPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");




const componentInstanceOverridesPropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('overrides', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.union([_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentInstanceOverridePropTypeUtil.schema, _component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_3__.componentOverridablePropTypeUtil.schema])).optional().default([]));

/***/ }),

/***/ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentInstancePropTypeUtil: function() { return /* binding */ componentInstancePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./component-instance-overrides-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts");



const componentInstancePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('component-instance', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.object({
  component_id: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.numberPropTypeUtil.schema,
  overrides: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.optional(_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentInstanceOverridesPropTypeUtil.schema)
}));

/***/ }),

/***/ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentOverridablePropTypeUtil: function() { return /* binding */ componentOverridablePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const componentOverridablePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('overridable', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.object({
  override_key: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string(),
  origin_value: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.object({
    $$type: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string(),
    value: _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.unknown()
  }).nullable()
}));

/***/ }),

/***/ "./packages/packages/core/editor-components/src/provider/component-instance-context.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/provider/component-instance-context.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ComponentInstanceProvider: function() { return /* binding */ ComponentInstanceProvider; },
/* harmony export */   useComponentId: function() { return /* binding */ useComponentId; },
/* harmony export */   useComponentInstanceOverrides: function() { return /* binding */ useComponentInstanceOverrides; },
/* harmony export */   useComponentOverridableProps: function() { return /* binding */ useComponentOverridableProps; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const ComponentInstanceContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function ComponentInstanceProvider({
  children,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ComponentInstanceContext.Provider, {
    value: props
  }, children);
}
function useComponentInstanceContext() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(ComponentInstanceContext);
  if (!context) {
    throw new Error('useComponentInstanceContext must be used within a ComponentInstanceProvider');
  }
  return context;
}
const useComponentId = () => useComponentInstanceContext().componentId;
const useComponentInstanceOverrides = () => useComponentInstanceContext().overrides;
const useComponentOverridableProps = () => useComponentInstanceContext().overridableProps;

/***/ }),

/***/ "./packages/packages/core/editor-components/src/provider/overridable-prop-context.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/provider/overridable-prop-context.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OverridablePropProvider: function() { return /* binding */ OverridablePropProvider; },
/* harmony export */   useComponentInstanceElement: function() { return /* binding */ useComponentInstanceElement; },
/* harmony export */   useOverridablePropValue: function() { return /* binding */ useOverridablePropValue; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const OverridablePropContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function OverridablePropProvider({
  children,
  ...props
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(OverridablePropContext.Provider, {
    value: props
  }, children);
}
const useOverridablePropValue = () => (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(OverridablePropContext)?.value;
const useComponentInstanceElement = () => (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(OverridablePropContext)?.componentInstanceElement;

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/actions/load-components-assets.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/actions/load-components-assets.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   loadComponentsAssets: function() { return /* binding */ loadComponentsAssets; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-embedded-documents-manager */ "@elementor/editor-embedded-documents-manager");
/* harmony import */ var _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_get_component_documents__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/get-component-documents */ "./packages/packages/core/editor-components/src/utils/get-component-documents.ts");
/* harmony import */ var _load_components_overridable_props__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./load-components-overridable-props */ "./packages/packages/core/editor-components/src/store/actions/load-components-overridable-props.ts");




async function loadComponentsAssets(elements) {
  if (!elements.length) {
    return;
  }
  const documents = await (0,_utils_get_component_documents__WEBPACK_IMPORTED_MODULE_2__.getComponentDocuments)({
    elements,
    isRecursive: false
  });
  updateDocumentState(documents);
  documents.forEach((document, id) => {
    _elementor_editor_embedded_documents_manager__WEBPACK_IMPORTED_MODULE_1__.embeddedDocumentsManager.setDocument(id, document);
  });
  await (0,_load_components_overridable_props__WEBPACK_IMPORTED_MODULE_3__.loadComponentsOverridableProps)([...documents.keys()]);
}
function updateDocumentState(documents) {
  const isDrafted = [...documents.values()].some(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.isDocumentDirty);
  if (isDrafted) {
    (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.setDocumentModifiedStatus)(true);
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/actions/load-components-overridable-props.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/actions/load-components-overridable-props.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   loadComponentsOverridableProps: function() { return /* binding */ loadComponentsOverridableProps; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../api */ "./packages/packages/core/editor-components/src/api.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-components/src/store/store.ts");



async function loadComponentsOverridableProps(componentIds) {
  const unloadedIds = componentIds.filter(id => !(0,_store__WEBPACK_IMPORTED_MODULE_2__.selectIsOverridablePropsLoaded)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)(), id));
  if (!unloadedIds.length) {
    return;
  }
  const {
    data
  } = await _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.getOverridableProps(unloadedIds);
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_2__.slice.actions.loadOverridableProps(data));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/actions/remove-component-styles.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/actions/remove-component-styles.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   removeComponentStyles: function() { return /* binding */ removeComponentStyles; }
/* harmony export */ });
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../api */ "./packages/packages/core/editor-components/src/api.ts");

function removeComponentStyles(id) {
  _api__WEBPACK_IMPORTED_MODULE_0__.apiClient.invalidateComponentConfigCache(id);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/actions/update-overridable-prop.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/actions/update-overridable-prop.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   updateOverridableProp: function() { return /* binding */ updateOverridableProp; }
/* harmony export */ });
/* harmony import */ var _utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/resolve-override-prop-value */ "./packages/packages/core/editor-components/src/utils/resolve-override-prop-value.ts");
/* harmony import */ var _dispatchers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../dispatchers */ "./packages/packages/core/editor-components/src/store/dispatchers.ts");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../selectors */ "./packages/packages/core/editor-components/src/store/selectors.ts");



function updateOverridableProp(componentId, propValue, originPropFields) {
  const overridableProps = _selectors__WEBPACK_IMPORTED_MODULE_2__.componentsSelectors.getOverridableProps(componentId);
  if (!overridableProps) {
    return;
  }
  const existingOverridableProp = overridableProps.props[propValue.override_key];
  if (!existingOverridableProp) {
    return;
  }
  const originValue = (0,_utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_0__.resolveOverridePropValue)(propValue.origin_value);
  const newOverridableProp = originPropFields ? {
    originValue,
    originPropFields
  } : {
    originValue
  };
  const newOverridableProps = {
    ...overridableProps,
    props: {
      ...overridableProps.props,
      [existingOverridableProp.overrideKey]: {
        ...existingOverridableProp,
        ...newOverridableProp
      }
    }
  };
  _dispatchers__WEBPACK_IMPORTED_MODULE_1__.componentsActions.setOverridableProps(componentId, newOverridableProps);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/dispatchers.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/dispatchers.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentsActions: function() { return /* binding */ componentsActions; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-components/src/store/store.ts");


function safeDispatch() {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getStore)()?.dispatch;
}
const componentsActions = {
  add(components) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.add(components));
  },
  load(components) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.load(components));
  },
  addUnpublished(component) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.addUnpublished(component));
  },
  removeUnpublished(uids) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.removeUnpublished(uids));
  },
  resetUnpublished() {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.resetUnpublished());
  },
  addCreatedThisSession(uid) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.addCreatedThisSession(uid));
  },
  removeCreatedThisSession(uid) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.removeCreatedThisSession(uid));
  },
  archive(componentId) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.archive(componentId));
  },
  setCurrentComponentId(id) {
    safeDispatch()?.(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.setCurrentComponentId(id));
  },
  setPath(path) {
    safeDispatch()?.(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.setPath(path));
  },
  setOverridableProps(componentId, overridableProps) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.setOverridableProps({
      componentId,
      overridableProps
    }));
  },
  rename(componentUid, name) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.rename({
      componentUid,
      name
    }));
  },
  cleanUpdatedComponentNames() {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.cleanUpdatedComponentNames());
  },
  updateComponentSanitizedAttribute(componentId, attribute) {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.updateComponentSanitizedAttribute({
      componentId,
      attribute
    }));
  },
  resetSanitizedComponents() {
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(_store__WEBPACK_IMPORTED_MODULE_1__.slice.actions.resetSanitizedComponents());
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/extensible-slice.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/extensible-slice.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __resetExtraReducers: function() { return /* binding */ __resetExtraReducers; },
/* harmony export */   createComponentsAction: function() { return /* binding */ createComponentsAction; },
/* harmony export */   registerComponentsReducer: function() { return /* binding */ registerComponentsReducer; },
/* harmony export */   slice: function() { return /* binding */ slice; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store-types */ "./packages/packages/core/editor-components/src/store/store-types.ts");
/* harmony import */ var _thunks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./thunks */ "./packages/packages/core/editor-components/src/store/thunks.ts");



const extraReducersMap = new Map();
function registerComponentsReducer(name, reducer) {
  extraReducersMap.set(`${_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME}/${name}`, reducer);
}
function createComponentsAction(name) {
  const action = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createAction)(`${_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME}/${name}`);
  return {
    action,
    register(reducer) {
      registerComponentsReducer(name, reducer);
    },
    dispatch(payload) {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__dispatch)(action(payload));
    }
  };
}
function __resetExtraReducers() {
  extraReducersMap.clear();
}
const baseSlice = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSlice)({
  name: _store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME,
  initialState: _store_types__WEBPACK_IMPORTED_MODULE_1__.initialState,
  reducers: {
    add: (state, {
      payload
    }) => {
      if (Array.isArray(payload)) {
        state.data = [...payload, ...state.data];
      } else {
        state.data.unshift(payload);
      }
    },
    load: (state, {
      payload
    }) => {
      state.data = payload;
    },
    addUnpublished: (state, {
      payload
    }) => {
      state.unpublishedData.unshift(payload);
    },
    removeUnpublished: (state, {
      payload
    }) => {
      const uidsToRemove = Array.isArray(payload) ? payload : [payload];
      state.unpublishedData = state.unpublishedData.filter(component => !uidsToRemove.includes(component.uid));
    },
    resetUnpublished: state => {
      state.unpublishedData = [];
    },
    addCreatedThisSession: (state, {
      payload
    }) => {
      state.createdThisSession.push(payload);
    },
    removeCreatedThisSession: (state, {
      payload
    }) => {
      state.createdThisSession = state.createdThisSession.filter(uid => uid !== payload);
    },
    archive: (state, {
      payload
    }) => {
      const component = state.data.find(comp => comp.id === payload);
      if (component) {
        component.isArchived = true;
        state.archivedThisSession.push(payload);
      }
    },
    setCurrentComponentId: (state, {
      payload
    }) => {
      state.currentComponentId = payload;
    },
    setPath: (state, {
      payload
    }) => {
      state.path = payload;
    },
    setOverridableProps: (state, {
      payload
    }) => {
      const component = state.data.find(comp => comp.id === payload.componentId);
      if (!component) {
        return;
      }
      component.overridableProps = payload.overridableProps;
    },
    loadOverridableProps: (state, {
      payload
    }) => {
      const componentIds = Object.keys(payload);
      componentIds.forEach(id => {
        const componentId = Number(id);
        const overridableProps = payload[componentId];
        const component = state.data.find(comp => comp.id === componentId);
        if (!component || !overridableProps) {
          return;
        }
        component.overridableProps = overridableProps;
      });
    },
    rename: (state, {
      payload
    }) => {
      const component = state.data.find(comp => comp.uid === payload.componentUid);
      if (!component) {
        return;
      }
      if (component.id) {
        state.updatedComponentNames[component.id] = payload.name;
      }
      component.name = payload.name;
    },
    cleanUpdatedComponentNames: state => {
      state.updatedComponentNames = {};
    },
    updateComponentSanitizedAttribute: (state, {
      payload: {
        componentId,
        attribute
      }
    }) => {
      if (!state.sanitized[componentId]) {
        state.sanitized[componentId] = {};
      }
      state.sanitized[componentId][attribute] = true;
    },
    resetSanitizedComponents: state => {
      state.sanitized = {};
    }
  },
  extraReducers: builder => {
    builder.addCase(_thunks__WEBPACK_IMPORTED_MODULE_2__.loadComponents.fulfilled, (state, {
      payload
    }) => {
      state.data = payload;
      state.loadStatus = 'idle';
    });
    builder.addCase(_thunks__WEBPACK_IMPORTED_MODULE_2__.loadComponents.pending, state => {
      state.loadStatus = 'pending';
    });
    builder.addCase(_thunks__WEBPACK_IMPORTED_MODULE_2__.loadComponents.rejected, state => {
      state.loadStatus = 'error';
    });
  }
});
const slice = {
  ...baseSlice,
  reducer(state, action) {
    const nextState = baseSlice.reducer(state, action);
    const extraReducer = extraReducersMap.get(action.type);
    if (!extraReducer || !nextState) {
      return nextState;
    }
    const clonedState = structuredClone(nextState);
    extraReducer(clonedState, action);
    return clonedState;
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/selectors.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/selectors.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   componentsSelectors: function() { return /* binding */ componentsSelectors; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-components/src/store/store.ts");


function safeGetState() {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getStore)()?.getState();
}
const componentsSelectors = {
  getOverridableProps(componentId) {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectOverridableProps)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)(), componentId);
  },
  getCurrentComponent() {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectCurrentComponent)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  },
  getCurrentComponentId() {
    const state = safeGetState();
    if (!state) {
      return null;
    }
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectCurrentComponentId)(state);
  },
  getUnpublishedComponents() {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectUnpublishedComponents)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  },
  getUpdatedComponentNames() {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectUpdatedComponentNames)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  },
  getArchivedThisSession() {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectArchivedThisSession)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  },
  getCreatedThisSession() {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectCreatedThisSession)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  },
  getComponents() {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectComponents)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
  },
  getComponentByUid(componentUid) {
    return (0,_store__WEBPACK_IMPORTED_MODULE_1__.selectComponentByUid)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)(), componentUid);
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/store-types.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/store-types.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SLICE_NAME: function() { return /* binding */ SLICE_NAME; },
/* harmony export */   initialState: function() { return /* binding */ initialState; }
/* harmony export */ });
const initialState = {
  data: [],
  unpublishedData: [],
  loadStatus: 'idle',
  createdThisSession: [],
  archivedThisSession: [],
  path: [],
  currentComponentId: null,
  updatedComponentNames: {},
  sanitized: {}
};
const SLICE_NAME = 'components';

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/store.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/store.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SLICE_NAME: function() { return /* reexport safe */ _store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME; },
/* harmony export */   __resetExtraReducers: function() { return /* reexport safe */ _extensible_slice__WEBPACK_IMPORTED_MODULE_2__.__resetExtraReducers; },
/* harmony export */   createComponentsAction: function() { return /* reexport safe */ _extensible_slice__WEBPACK_IMPORTED_MODULE_2__.createComponentsAction; },
/* harmony export */   initialState: function() { return /* reexport safe */ _store_types__WEBPACK_IMPORTED_MODULE_1__.initialState; },
/* harmony export */   registerComponentsReducer: function() { return /* reexport safe */ _extensible_slice__WEBPACK_IMPORTED_MODULE_2__.registerComponentsReducer; },
/* harmony export */   selectArchivedThisSession: function() { return /* binding */ selectArchivedThisSession; },
/* harmony export */   selectComponent: function() { return /* binding */ selectComponent; },
/* harmony export */   selectComponentByUid: function() { return /* binding */ selectComponentByUid; },
/* harmony export */   selectComponents: function() { return /* binding */ selectComponents; },
/* harmony export */   selectCreatedThisSession: function() { return /* binding */ selectCreatedThisSession; },
/* harmony export */   selectCurrentComponent: function() { return /* binding */ selectCurrentComponent; },
/* harmony export */   selectCurrentComponentId: function() { return /* binding */ selectCurrentComponentId; },
/* harmony export */   selectData: function() { return /* binding */ selectData; },
/* harmony export */   selectIsOverridablePropsLoaded: function() { return /* binding */ selectIsOverridablePropsLoaded; },
/* harmony export */   selectLoadIsError: function() { return /* binding */ selectLoadIsError; },
/* harmony export */   selectLoadIsPending: function() { return /* binding */ selectLoadIsPending; },
/* harmony export */   selectOverridableProps: function() { return /* binding */ selectOverridableProps; },
/* harmony export */   selectPath: function() { return /* binding */ selectPath; },
/* harmony export */   selectUnpublishedComponents: function() { return /* binding */ selectUnpublishedComponents; },
/* harmony export */   selectUpdatedComponentNames: function() { return /* binding */ selectUpdatedComponentNames; },
/* harmony export */   slice: function() { return /* reexport safe */ _extensible_slice__WEBPACK_IMPORTED_MODULE_2__.slice; },
/* harmony export */   useComponent: function() { return /* binding */ useComponent; },
/* harmony export */   useCurrentComponent: function() { return /* binding */ useCurrentComponent; },
/* harmony export */   useCurrentComponentId: function() { return /* binding */ useCurrentComponentId; },
/* harmony export */   useIsSanitizedComponent: function() { return /* binding */ useIsSanitizedComponent; },
/* harmony export */   useOverridableProps: function() { return /* binding */ useOverridableProps; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store-types */ "./packages/packages/core/editor-components/src/store/store-types.ts");
/* harmony import */ var _extensible_slice__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./extensible-slice */ "./packages/packages/core/editor-components/src/store/extensible-slice.ts");




const selectData = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].data;
const selectArchivedThisSession = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].archivedThisSession;
const selectLoadStatus = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].loadStatus;
const selectUnpublishedData = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].unpublishedData;
const getCreatedThisSession = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].createdThisSession;
const getPath = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].path;
const getCurrentComponentId = state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].currentComponentId;
const selectComponent = (state, componentId) => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].data.find(component => component.id === componentId);
const useComponent = componentId => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(state => componentId ? selectComponent(state, componentId) : null);
};
const selectComponentByUid = (state, componentUid) => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].data.find(component => component.uid === componentUid) ?? state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].unpublishedData.find(component => component.uid === componentUid);
const selectComponents = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectData, selectUnpublishedData, (data, unpublishedData) => [...unpublishedData.map(item => ({
  uid: item.uid,
  name: item.name,
  overridableProps: item.overridableProps
})), ...data.filter(component => !component.isArchived)]);
const selectUnpublishedComponents = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectUnpublishedData, unpublishedData => unpublishedData);
const selectLoadIsPending = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectLoadStatus, status => status === 'pending');
const selectLoadIsError = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectLoadStatus, status => status === 'error');
const selectCreatedThisSession = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(getCreatedThisSession, createdThisSession => createdThisSession);
const DEFAULT_OVERRIDABLE_PROPS = {
  props: {},
  groups: {
    items: {},
    order: []
  }
};
const selectOverridableProps = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectComponent, component => {
  if (!component) {
    return undefined;
  }
  return component.overridableProps ?? DEFAULT_OVERRIDABLE_PROPS;
});
const useOverridableProps = componentId => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(state => componentId ? selectOverridableProps(state, componentId) : null);
};
const selectIsOverridablePropsLoaded = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectComponent, component => {
  return !!component?.overridableProps;
});
const selectPath = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(getPath, path => path);
const selectCurrentComponentId = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(getCurrentComponentId, currentComponentId => currentComponentId);
const selectCurrentComponent = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectData, getCurrentComponentId, (data, currentComponentId) => data.find(component => component.id === currentComponentId));
const useCurrentComponentId = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(selectCurrentComponentId);
};
const useCurrentComponent = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(selectCurrentComponent);
};
const selectUpdatedComponentNames = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].updatedComponentNames, updatedComponentNames => Object.entries(updatedComponentNames).map(([componentId, title]) => ({
  componentId: Number(componentId),
  title
})));
const useSanitizedComponents = () => {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(state => state[_store_types__WEBPACK_IMPORTED_MODULE_1__.SLICE_NAME].sanitized);
};
const useIsSanitizedComponent = (componentId, key) => {
  const sanitizedComponents = useSanitizedComponents();
  if (!componentId) {
    return false;
  }
  return !!sanitizedComponents[componentId]?.[key];
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/store/thunks.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/store/thunks.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   loadComponents: function() { return /* binding */ loadComponents; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api */ "./packages/packages/core/editor-components/src/api.ts");


const loadComponents = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createAsyncThunk)('components/load', async () => {
  const response = await _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.get();
  return response;
});


/***/ }),

/***/ "./packages/packages/core/editor-components/src/sync/before-save.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/sync/before-save.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   beforeSave: function() { return /* binding */ beforeSave; }
/* harmony export */ });
/* harmony import */ var _publish_draft_components_in_page_before_save__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./publish-draft-components-in-page-before-save */ "./packages/packages/core/editor-components/src/sync/publish-draft-components-in-page-before-save.ts");

const beforeSave = ({
  container,
  status
}) => {
  const elements = container?.model.get('elements').toJSON?.() ?? [];
  return (0,_publish_draft_components_in_page_before_save__WEBPACK_IMPORTED_MODULE_0__.publishDraftComponentsInPageBeforeSave)({
    elements,
    status
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/sync/load-component-data-after-instance-added.ts":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/sync/load-component-data-after-instance-added.ts ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initLoadComponentDataAfterInstanceAdded: function() { return /* binding */ initLoadComponentDataAfterInstanceAdded; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/actions/load-components-assets */ "./packages/packages/core/editor-components/src/store/actions/load-components-assets.ts");


function initLoadComponentDataAfterInstanceAdded() {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.registerDataHook)('after', 'document/elements/paste', (_args, result) => {
    load(result);
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.registerDataHook)('after', 'document/elements/import', (_args, result) => {
    load(result);
  });
}
function load(result) {
  if (!result) {
    return;
  }
  const containers = Array.isArray(result) ? result : [result];
  const elements = containers.map(container => container.model?.toJSON()).filter(element => !!element);
  (0,_store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_1__.loadComponentsAssets)(elements);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/sync/publish-draft-components-in-page-before-save.ts":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/sync/publish-draft-components-in-page-before-save.ts ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   publishDraftComponentsInPageBeforeSave: function() { return /* binding */ publishDraftComponentsInPageBeforeSave; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../api */ "./packages/packages/core/editor-components/src/api.ts");
/* harmony import */ var _utils_get_component_documents__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/get-component-documents */ "./packages/packages/core/editor-components/src/utils/get-component-documents.ts");






const INSUFFICIENT_PERMISSIONS_ERROR_CODE = 'insufficient_permissions';
const PUBLISH_UPGRADE_URL = 'https://go.elementor.com/go-pro-components-Instance-draft-failure/';
const PUBLISH_UPGRADE_NOTIFICATION_ID = 'component-publish-upgrade';
async function publishDraftComponentsInPageBeforeSave({
  status,
  elements
}) {
  if (status !== 'publish') {
    return;
  }
  const documents = await (0,_utils_get_component_documents__WEBPACK_IMPORTED_MODULE_5__.getComponentDocuments)({
    elements
  });
  const draftIds = [...documents.values()].filter(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.isDocumentDirty).map(document => document.id);
  if (draftIds.length === 0) {
    return;
  }
  try {
    await _api__WEBPACK_IMPORTED_MODULE_4__.apiClient.updateStatuses(draftIds, 'publish');
  } catch (error) {
    if (isInsufficientPermissionsError(error)) {
      notifyPublishUpgrade();
      return;
    }
    throw error;
  }
  draftIds.forEach(id => (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.invalidateDocumentData)(id));
}
function isInsufficientPermissionsError(error) {
  return error instanceof _elementor_http_client__WEBPACK_IMPORTED_MODULE_2__.AxiosError && error.response?.data?.code === INSUFFICIENT_PERMISSIONS_ERROR_CODE;
}
function notifyPublishUpgrade() {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_1__.notify)({
    type: 'promotion',
    id: PUBLISH_UPGRADE_NOTIFICATION_ID,
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('You have unpublished component on this page. You need a pro version to publish it.', 'elementor'),
    additionalActionProps: [{
      size: 'small',
      variant: 'contained',
      color: 'promotion',
      href: PUBLISH_UPGRADE_URL,
      target: '_blank',
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Upgrade Now', 'elementor')
    }]
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/component-document-data.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/component-document-data.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getComponentDocumentData: function() { return /* binding */ getComponentDocumentData; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);

const getComponentDocumentData = async id => {
  const documentManager = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.getV1DocumentsManager)();
  try {
    return await documentManager.request(id);
  } catch {
    return null;
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/detach-component-instance/detach-component-instance.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/detach-component-instance/detach-component-instance.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   detachComponentInstance: function() { return /* binding */ detachComponentInstance; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../prop-types/component-instance-overrides-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../prop-types/component-instance-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts");
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _component_document_data__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../component-document-data */ "./packages/packages/core/editor-components/src/utils/component-document-data.ts");
/* harmony import */ var _tracking__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../tracking */ "./packages/packages/core/editor-components/src/utils/tracking.ts");
/* harmony import */ var _resolve_detached_instance__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./resolve-detached-instance */ "./packages/packages/core/editor-components/src/utils/detach-component-instance/resolve-detached-instance.ts");











const DETACH_EVENT = 'elementor/components/detach-instance';
const DETACH_UNDO_EVENT = 'elementor/components/undo-detach-instance';
const DETACH_REDO_EVENT = 'elementor/components/redo-detach-instance';
async function detachComponentInstance({
  instanceId,
  componentId,
  trackingInfo
}) {
  const instanceContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(instanceId);
  if (!instanceContainer) {
    throw new Error(`Instance container with ID "${instanceId}" not found.`);
  }
  const componentData = await (0,_component_document_data__WEBPACK_IMPORTED_MODULE_8__.getComponentDocumentData)(componentId);
  if (!componentData) {
    throw new Error(`Component with ID "${componentId}" not found.`);
  }
  const rootElement = componentData.elements?.[0];
  if (!rootElement) {
    throw new Error(`Component with ID "${componentId}" has no root element.`);
  }
  const undoableDetach = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.undoable)({
    do: () => {
      const overrides = extractInstanceOverrides(instanceContainer);
      const detachedInstanceElementData = (0,_resolve_detached_instance__WEBPACK_IMPORTED_MODULE_10__.resolveDetachedInstance)(rootElement, overrides);
      const originalInstanceModel = instanceContainer.model.toJSON();
      const actionId = new Date().getTime();

      // We should fire the event *before* replacing the element, so it will happen before delete event is fired.
      // As we do overridable props cleanup for both events.
      window.dispatchEvent(new CustomEvent(DETACH_EVENT, {
        detail: {
          detachedInstanceId: instanceId,
          detachActionId: actionId
        }
      }));
      const detachedElement = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.replaceElement)({
        currentElementId: instanceId,
        newElement: detachedInstanceElementData,
        withHistory: false
      });
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.selectElement)(detachedElement.id);
      const componentUid = (0,_store_store__WEBPACK_IMPORTED_MODULE_7__.selectComponent)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_3__.__getState)(), componentId)?.uid;
      (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackComponentEvent)({
        action: 'detached',
        executedBy: 'user',
        component_uid: componentUid,
        instance_id: instanceId,
        location: trackingInfo.location,
        secondary_location: trackingInfo.secondaryLocation,
        trigger: trackingInfo.trigger
      });
      return {
        detachedElement,
        detachedInstanceElementData,
        originalInstanceModel,
        actionId
      };
    },
    undo: (_, {
      detachedElement,
      originalInstanceModel,
      actionId
    }) => {
      const restoredInstance = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.replaceElement)({
        currentElementId: detachedElement.id,
        newElement: originalInstanceModel,
        withHistory: false
      });
      window.dispatchEvent(new CustomEvent(DETACH_UNDO_EVENT, {
        detail: {
          restoredInstanceId: restoredInstance.id,
          detachActionId: actionId
        }
      }));

      // Wait for the instance to be restored
      (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.doAfterRender)([restoredInstance.id], () => {
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.selectElement)(restoredInstance.id);
      });
      return restoredInstance;
    },
    redo: (_, doReturn, restoredInstance) => {
      const {
        detachedInstanceElementData,
        actionId
      } = doReturn;
      const detachedElement = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.replaceElement)({
        currentElementId: restoredInstance.id,
        newElement: detachedInstanceElementData,
        withHistory: false
      });
      window.dispatchEvent(new CustomEvent(DETACH_REDO_EVENT, {
        detail: {
          detachedInstanceId: detachedElement.id,
          detachActionId: actionId
        }
      }));
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.selectElement)(detachedElement.id);
      return {
        ...doReturn,
        detachedElement
      };
    }
  }, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Detach from Component', 'elementor'),
    subtitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Instance detached', 'elementor')
  });
  return undoableDetach();
}
function extractInstanceOverrides(instanceContainer) {
  const settings = instanceContainer.model.toJSON().settings;
  const componentInstance = _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_6__.componentInstancePropTypeUtil.extract(settings?.component_instance);
  const overrides = _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_5__.componentInstanceOverridesPropTypeUtil.extract(componentInstance?.overrides);
  return overrides ?? [];
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/detach-component-instance/index.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/detach-component-instance/index.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   detachComponentInstance: function() { return /* reexport safe */ _detach_component_instance__WEBPACK_IMPORTED_MODULE_0__.detachComponentInstance; }
/* harmony export */ });
/* harmony import */ var _detach_component_instance__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./detach-component-instance */ "./packages/packages/core/editor-components/src/utils/detach-component-instance/detach-component-instance.ts");


/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/detach-component-instance/regenerate-local-style-ids.ts":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/detach-component-instance/regenerate-local-style-ids.ts ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   regenerateLocalStyleIds: function() { return /* binding */ regenerateLocalStyleIds; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);


// Ts version for atomic-widgets/assets/js/editor/utils/regenerate-local-style-ids.js
function regenerateLocalStyleIds(element) {
  const originalStyles = element.styles;
  if (!originalStyles || Object.keys(originalStyles).length === 0) {
    return {
      styles: undefined,
      settings: undefined
    };
  }
  const newStyles = {};
  const styleIdMapping = {};
  for (const [originalStyleId, style] of Object.entries(originalStyles)) {
    const newStyleId = generateLocalStyleId(element.id);
    newStyles[newStyleId] = {
      ...style,
      id: newStyleId
    };
    styleIdMapping[originalStyleId] = newStyleId;
  }
  const settings = element.settings;
  if (!settings || Object.keys(settings).length === 0) {
    return {
      styles: newStyles,
      settings: undefined
    };
  }
  const updatedSettings = {
    ...settings
  };
  for (const [propKey, propValue] of Object.entries(updatedSettings)) {
    if (isClassesProp(propValue) && propValue.value.length > 0) {
      const updatedClasses = propValue.value.map(classId => styleIdMapping[classId] ?? classId);
      updatedSettings[propKey] = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.classesPropTypeUtil.create(updatedClasses);
    }
  }
  return {
    styles: newStyles,
    settings: updatedSettings
  };
}
function isClassesProp(prop) {
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.classesPropTypeUtil.isValid(prop);
}
function generateLocalStyleId(elementId) {
  return `e-${elementId}-${(0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.generateElementId)()}`;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/detach-component-instance/resolve-detached-instance.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/detach-component-instance/resolve-detached-instance.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getOverridableOverride: function() { return /* binding */ getOverridableOverride; },
/* harmony export */   resolveDetachedInstance: function() { return /* binding */ resolveDetachedInstance; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");
/* harmony import */ var _regenerate_local_style_ids__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./regenerate-local-style-ids */ "./packages/packages/core/editor-components/src/utils/detach-component-instance/regenerate-local-style-ids.ts");
/* harmony import */ var _resolve_overridable_settings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./resolve-overridable-settings */ "./packages/packages/core/editor-components/src/utils/detach-component-instance/resolve-overridable-settings.ts");






/**
 * Creates detached element from component instance
 * by applying overrides, reverting overridables, and regenerating IDs.
 * This is used when detaching a component instance from its origin component.
 *
 * The function goes through all nested elements recursively and:
 * - Regenerates element IDs and local style IDs
 * - Resolves overridable settings - applies all instance overrides to element settings,
 * or replaces overridable with origin_value when no matching override exists.
 *
 * @param element   - The component's root element
 * @param overrides - Array of overrides from the component instance
 * @return A new element data with all overrides applied and new IDs
 */
function resolveDetachedInstance(element, overrides) {
  const overrideMap = createOverrideMap(overrides);
  return resolveElementRecursive(structuredClone(element), overrideMap);
}
function resolveElementRecursive(element, overrideMap) {
  element.id = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.generateElementId)();
  if (element.styles) {
    const {
      styles,
      settings
    } = (0,_regenerate_local_style_ids__WEBPACK_IMPORTED_MODULE_3__.regenerateLocalStyleIds)(element);
    element.styles = styles;
    if (settings) {
      element.settings = {
        ...element.settings,
        ...settings
      };
    }
  }
  if (element.settings) {
    element.settings = (0,_resolve_overridable_settings__WEBPACK_IMPORTED_MODULE_4__.resolveOverridableSettings)(element, overrideMap);
  }
  if (element.elements?.length) {
    element.elements = element.elements.map(child => resolveElementRecursive(child, overrideMap));
  }
  return element;
}
function createOverrideMap(overrides) {
  const map = new Map();
  overrides.forEach(item => {
    let override = null;
    if (_prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentInstanceOverridePropTypeUtil.isValid(item)) {
      override = item;
    } else if (_prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentOverridablePropTypeUtil.isValid(item)) {
      override = getOverridableOverride(item);
    }
    if (override) {
      const overrideKey = override.value.override_key;
      map.set(overrideKey, override);
    }
  });
  return map;
}
function getOverridableOverride(propValue) {
  if (!_prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentOverridablePropTypeUtil.isValid(propValue)) {
    return null;
  }
  const originValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentOverridablePropTypeUtil.extract(propValue)?.origin_value;
  if (!_prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentInstanceOverridePropTypeUtil.isValid(originValue)) {
    return null;
  }
  return originValue;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/detach-component-instance/resolve-overridable-settings.ts":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/detach-component-instance/resolve-overridable-settings.ts ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   resolveOverridableSettings: function() { return /* binding */ resolveOverridableSettings; }
/* harmony export */ });
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../prop-types/component-instance-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");
/* harmony import */ var _is_component_instance__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../is-component-instance */ "./packages/packages/core/editor-components/src/utils/is-component-instance.ts");




function resolveOverridableSettings(element, overrideMap) {
  if ((0,_is_component_instance__WEBPACK_IMPORTED_MODULE_3__.isComponentInstance)({
    widgetType: element.widgetType,
    elType: element.elType
  })) {
    return resolveOverridableSettingsForComponentInstance(element, overrideMap);
  }
  return resolveOverridableSettingsForElement(element, overrideMap);
}
function resolveOverridableSettingsForElement(element, overrideMap) {
  const updatedSettings = element.settings ? {
    ...element.settings
  } : {};
  for (const [settingKey, settingValue] of Object.entries(element.settings ?? {})) {
    updatedSettings[settingKey] = resolvePropValue(settingValue, overrideMap);
  }
  return updatedSettings;
}
function resolveOverridableSettingsForComponentInstance(element, overrideMap) {
  const componentInstance = element.settings?.component_instance;
  if (!_prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentInstancePropTypeUtil.isValid(componentInstance)) {
    return element.settings ?? {};
  }
  const instanceOverrides = componentInstance.value.overrides?.value;
  if (!instanceOverrides?.length) {
    return element.settings ?? {};
  }
  const updatedOverrides = instanceOverrides.map(item => resolvePropValue(item, overrideMap, {
    isOverridableOverride: true
  }));
  return {
    ...element.settings,
    component_instance: {
      ...componentInstance,
      value: {
        ...componentInstance.value,
        overrides: {
          ...componentInstance.value.overrides,
          value: updatedOverrides
        }
      }
    }
  };
}
function resolvePropValue(propValue, overrideMap, options) {
  const {
    isOverridableOverride = false
  } = options ?? {};

  // if it's not an overridable, return the prop value as is
  if (!_prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentOverridablePropTypeUtil.isValid(propValue)) {
    return propValue;
  }
  const overridableKey = propValue.value.override_key;
  const matchingOverride = overrideMap.get(overridableKey);
  const originValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentOverridablePropTypeUtil.extract(propValue)?.origin_value;

  // if no matching override, return the overridable's origin value
  if (!matchingOverride) {
    return originValue;
  }
  if (isOverridableOverride) {
    return resolveOverridableOverride(matchingOverride, originValue);
  }

  // for regular props, when there's a non null matching override, return the matching override value,
  // otherwise return the origin value
  const matchingOverrideValue = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.extract(matchingOverride)?.override_value;
  return matchingOverrideValue ?? originValue;
}
function resolveOverridableOverride(matchingOverride, originValue) {
  if (!originValue || !_prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.isValid(originValue)) {
    return null;
  }

  // for overridable overrides, we should create a new override with the matching override value
  // but keep the origin value's override key and schema source, so they'll match the inner component.
  return _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.create({
    override_value: matchingOverride.value.override_value,
    override_key: originValue.value.override_key,
    schema_source: originValue.value.schema_source
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/filter-valid-overridable-props.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/filter-valid-overridable-props.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   filterValidOverridableProps: function() { return /* binding */ filterValidOverridableProps; },
/* harmony export */   isExposedPropValid: function() { return /* binding */ isExposedPropValid; }
/* harmony export */ });
/* harmony import */ var _resolve_overrides_chain__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./resolve-overrides-chain */ "./packages/packages/core/editor-components/src/utils/resolve-overrides-chain.ts");

function filterValidOverridableProps(overridableProps,
// instanceElementId is used to find the component inner elements,
// and should be passed when editing component instance (not in component edit mode)
instanceElementId) {
  const validProps = {};
  for (const [key, prop] of Object.entries(overridableProps.props)) {
    if (isExposedPropValid(prop, instanceElementId)) {
      validProps[key] = prop;
    }
  }
  const validPropKeys = new Set(Object.keys(validProps));
  const filteredGroups = {
    items: Object.fromEntries(Object.entries(overridableProps.groups.items).map(([groupId, group]) => [groupId, {
      ...group,
      props: group.props.filter(propKey => validPropKeys.has(propKey))
    }])),
    order: overridableProps.groups.order
  };
  return {
    props: validProps,
    groups: filteredGroups
  };
}
function isExposedPropValid(prop, instanceElementId) {
  const {
    isChainBroken
  } = (0,_resolve_overrides_chain__WEBPACK_IMPORTED_MODULE_0__.resolveOverridesChain)({
    outerOverridableProp: prop,
    outerInstanceId: instanceElementId
  });
  return !isChainBroken;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/format-component-elements-id.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/format-component-elements-id.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   formatComponentElementsId: function() { return /* binding */ formatComponentElementsId; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const ELEMENT_ID_LENGTH = 7;
// The purpose of this function is to solve the issue of component inner elements having the same ID
// when there are multiple instances of the same component on a page.
// We change the ID of the inner elements to a hash of the nesting path of the element and the element's originalID.
function formatComponentElementsId(elements, path) {
  return elements.map(element => {
    const nestingPath = [...path, element.id];
    const id = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.hashString)(nestingPath.join('_'), ELEMENT_ID_LENGTH);
    return {
      ...element,
      id,
      originId: element.id,
      elements: element.elements ? formatComponentElementsId(element.elements, nestingPath) : undefined
    };
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/get-component-documents.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/get-component-documents.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getComponentDocuments: function() { return /* binding */ getComponentDocuments; }
/* harmony export */ });
/* harmony import */ var _component_document_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./component-document-data */ "./packages/packages/core/editor-components/src/utils/component-document-data.ts");
/* harmony import */ var _is_component_instance__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./is-component-instance */ "./packages/packages/core/editor-components/src/utils/is-component-instance.ts");


async function getComponentDocuments({
  elements,
  cache = new Map(),
  isRecursive = true
}) {
  const componentIds = await getComponentIds(elements, cache, isRecursive);
  return getDocumentsMap(componentIds, cache);
}
async function getComponentIds(elements, cache, isRecursive) {
  const results = await Promise.all(elements.filter(Boolean).map(async ({
    widgetType,
    elType,
    elements: childElements,
    settings
  }) => {
    const ids = [];
    if ((0,_is_component_instance__WEBPACK_IMPORTED_MODULE_1__.isComponentInstance)({
      widgetType,
      elType
    })) {
      const componentId = settings?.component_instance?.value?.component_id.value;
      if (!componentId) {
        return ids;
      }
      ids.push(componentId);
      if (!cache.has(componentId)) {
        cache.set(componentId, (0,_component_document_data__WEBPACK_IMPORTED_MODULE_0__.getComponentDocumentData)(componentId));
      }
      if (isRecursive) {
        const doc = await cache.get(componentId);
        childElements = doc?.elements;
      }
    }
    if (childElements?.length) {
      const childIds = await getComponentIds(childElements, cache, isRecursive);
      ids.push(...childIds);
    }
    return ids;
  }));
  return [...new Set(results.flat())];
}
async function getDocumentsMap(ids, cache) {
  const documents = await Promise.all(ids.map(async id => {
    const document = await cache.get(id);
    if (!document) {
      return null;
    }
    return [id, document];
  }));
  return new Map(documents.filter(document => document !== null));
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/get-container-by-origin-id.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/get-container-by-origin-id.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getContainerByOriginId: function() { return /* binding */ getContainerByOriginId; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

/**
 * Resolves a container by its origin ID, with optional scoping to a component instance.
 *
 * When component instances are rendered, their inner elements get prefixed IDs
 * (e.g., `{instanceId}_{originalId}`), but store data uses original IDs.
 * This function bridges between the two by searching for elements with a matching `originId`.
 *
 * @param originElementId   - The original (unprefixed) element ID from store data
 * @param instanceElementId - Optional instance widget ID to scope the search
 *
 * @return The container with prefixed runtime ID, or null if not found
 *
 * @example
 * // Component editing mode (no prefixing)
 * getContainerByOriginId('element-1') // returns container with id='element-1'
 *
 * @example
 * // Instance editing mode (with hash prefixing)
 * getContainerByOriginId('element-1', 'instance-abc')
 * // returns container with id='{hash}_element-1' and originId='element-1'
 */
function getContainerByOriginId(originElementId, instanceElementId) {
  if (!instanceElementId) {
    return (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(originElementId);
  }
  const instanceContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(instanceElementId);
  if (!instanceContainer) {
    return null;
  }
  const legacyWindow = window;
  return legacyWindow.elementor?.getContainerByKeyValue?.({
    key: 'originId',
    value: originElementId,
    parent: instanceContainer.view
  }) ?? null;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/get-overridable-prop.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/get-overridable-prop.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getOverridableProp: function() { return /* binding */ getOverridableProp; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");


function getOverridableProp({
  componentId,
  overrideKey
}) {
  const overridableProps = (0,_store_store__WEBPACK_IMPORTED_MODULE_1__.selectOverridableProps)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)(), componentId);
  if (!overridableProps) {
    return undefined;
  }
  return overridableProps.props[overrideKey];
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/get-prop-type-for-component-override.ts":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/get-prop-type-for-component-override.ts ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getPropTypeForComponentOverride: function() { return /* binding */ getPropTypeForComponentOverride; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

const getPropTypeForComponentOverride = overridableProp => {
  if (overridableProp.originPropFields) {
    return getPropType(overridableProp.originPropFields);
  }
  const {
    widgetType,
    propKey
  } = overridableProp;
  return getPropType({
    widgetType,
    propKey
  });
};
function getPropType({
  widgetType,
  propKey
}) {
  const widgetPropsSchema = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)()?.[widgetType]?.atomic_props_schema;
  return widgetPropsSchema?.[propKey];
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/is-component-instance.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/is-component-instance.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isComponentInstance: function() { return /* binding */ isComponentInstance; }
/* harmony export */ });
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../consts */ "./packages/packages/core/editor-components/src/consts.ts");

function isComponentInstance(elementModel) {
  return [elementModel.widgetType, elementModel.elType].includes(_consts__WEBPACK_IMPORTED_MODULE_0__.COMPONENT_WIDGET_TYPE);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/is-pro-components-supported.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/is-pro-components-supported.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isProComponentsSupported: function() { return /* binding */ isProComponentsSupported; },
/* harmony export */   isProOutdatedForComponents: function() { return /* binding */ isProOutdatedForComponents; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const MIN_PRO_VERSION_FOR_COMPONENTS = '4.0';
function isProComponentsSupported() {
  return (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.hasProInstalled)() && (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.isProAtLeast)(MIN_PRO_VERSION_FOR_COMPONENTS);
}
function isProOutdatedForComponents() {
  return (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.hasProInstalled)() && !(0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.isProAtLeast)(MIN_PRO_VERSION_FOR_COMPONENTS);
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/overridable-props-utils.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/overridable-props-utils.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getMatchingOverride: function() { return /* binding */ getMatchingOverride; }
/* harmony export */ });
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");


function getMatchingOverride(overrides, overrideKey) {
  return overrides?.find(override => {
    const overridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentOverridablePropTypeUtil.extract(override);
    if (overridableValue) {
      const overrideValue = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.extract(overridableValue.origin_value);
      return overrideValue?.override_key === overrideKey;
    }
    return override.value.override_key === overrideKey;
  }) ?? null;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/resolve-override-prop-value.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/resolve-override-prop-value.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   resolveOverridePropValue: function() { return /* binding */ resolveOverridePropValue; }
/* harmony export */ });
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");


const resolveOverridePropValue = originalPropValue => {
  const isOverridable = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentOverridablePropTypeUtil.isValid(originalPropValue);
  if (isOverridable) {
    return getOverridableValue(originalPropValue);
  }
  const isOverride = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.isValid(originalPropValue);
  if (isOverride) {
    return getOverrideValue(originalPropValue);
  }
  return originalPropValue;
};
function getOverridableValue(overridableProp) {
  const overridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentOverridablePropTypeUtil.extract(overridableProp);
  if (!overridableValue) {
    return null;
  }
  const isOverride = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.isValid(overridableValue.origin_value);
  if (isOverride) {
    return getOverrideValue(overridableValue.origin_value);
  }
  return overridableValue.origin_value;
}
function getOverrideValue(overrideProp) {
  const overrideValue = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.extract(overrideProp);
  if (!overrideValue) {
    return null;
  }
  return overrideValue.override_value;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/resolve-overrides-chain.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/resolve-overrides-chain.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildOverridesMap: function() { return /* binding */ buildOverridesMap; },
/* harmony export */   resolveOverridesChain: function() { return /* binding */ resolveOverridesChain; }
/* harmony export */ });
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../prop-types/component-instance-overrides-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../prop-types/component-instance-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");
/* harmony import */ var _get_container_by_origin_id__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./get-container-by-origin-id */ "./packages/packages/core/editor-components/src/utils/get-container-by-origin-id.ts");
/* harmony import */ var _get_overridable_prop__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./get-overridable-prop */ "./packages/packages/core/editor-components/src/utils/get-overridable-prop.ts");






// Recursively walks down a chain of nested component instances to find the innermost element
// and collect the overrides mapping for it.
// Returns the resolved inner element with its overrides mapping,
// or { isChainBroken: true } if any level in the chain is no longer overridable.
function resolveOverridesChain({
  outerOverridableProp,
  outerInstanceId,
  overridesMapping = {}
}) {
  // Stop condition: no originPropFields means we've reached the most inner component instance
  if (!outerOverridableProp.originPropFields) {
    const innerElement = (0,_get_container_by_origin_id__WEBPACK_IMPORTED_MODULE_4__.getContainerByOriginId)(outerOverridableProp.elementId, outerInstanceId);
    if (!innerElement) {
      throw new Error(`Inner element not found inside instance. elementId: ${outerOverridableProp.elementId}, instanceId: ${outerInstanceId}`);
    }
    return {
      isChainBroken: false,
      innerElement,
      overridesMapping
    };
  }

  // Step 1: Find the intermediate component instance and read its settings.
  const currentInstance = (0,_get_container_by_origin_id__WEBPACK_IMPORTED_MODULE_4__.getContainerByOriginId)(outerOverridableProp.elementId, outerInstanceId);
  if (!currentInstance) {
    // One of the instances in the chain was deleted.
    return {
      isChainBroken: true
    };
  }
  const {
    componentId,
    overrides
  } = extractComponentInstanceSettings(currentInstance);
  if (!componentId) {
    throw new Error(`Component ID not found for current instance. currentInstanceId: ${currentInstance.id}. outerInstanceId: ${outerInstanceId}`);
  }

  // Collect overrides from this level, translating keys for exposed-further props.
  const mergedOverrides = buildOverridesMap(overridesMapping, overrides ?? []);

  // Find the overridable-override that matches the outer overridable prop's key,
  // to get the next level's overridable prop.
  const override = findOverrideByOuterKey(overrides, outerOverridableProp.overrideKey);
  const overrideKey = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.extract(override)?.override_key;
  if (!override || !overrideKey) {
    // No matching override found for the current level - it means it's no longer overridable.
    return {
      isChainBroken: true
    };
  }
  const overridableProp = (0,_get_overridable_prop__WEBPACK_IMPORTED_MODULE_5__.getOverridableProp)({
    componentId,
    overrideKey
  });
  if (!overridableProp) {
    throw new Error(`Overridable prop not found. componentId: ${componentId}, overrideKey: ${overrideKey}`);
  }

  // Step 4: Recurse into the next nesting level.
  return resolveOverridesChain({
    outerOverridableProp: overridableProp,
    outerInstanceId: currentInstance.id,
    overridesMapping: mergedOverrides
  });
}

/**
 * Builds overrides map from instances chain:
 * At each level, we collect overrides from the current instance and merge them with the overrides from the outer levels.
 *
 * For exposed-further overrides (overridable wrapping an override), we have outer key (overridable's) and inner key (override's).
 * If a higher level already set a value for the outer key, that value is carried forward to the inner key
 * — same logic as the componentOverridableTransformer in the render pipeline.
 *
 * For simple overrides, that are not exposed further, we just use the override's key and value.
 *
 * @param existing       - Previously accumulated overrides from outer levels.
 * @param levelOverrides - The overrides array from the current level instance.
 */
function buildOverridesMap(existing, levelOverrides) {
  const result = {
    ...existing
  };
  for (const item of levelOverrides) {
    const overridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_3__.componentOverridablePropTypeUtil.extract(item);
    if (overridableValue) {
      // Exposed-further: overridable wraps an inner override with a different key.
      const override = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.extract(overridableValue.origin_value);
      if (!override) {
        continue;
      }
      const outerKey = overridableValue.override_key;
      const innerKey = override.override_key;
      const innerValue = override.override_value;

      // If an upper level already set a value for the outer key, carry it forward to the inner key.
      const higherLevelOverride = existing[outerKey];
      if (higherLevelOverride) {
        const outerValue = higherLevelOverride.value;
        result[innerKey] = {
          value: outerValue ?? innerValue,
          outermostKey: higherLevelOverride.outermostKey ?? outerKey
        };
        continue;
      }
      result[innerKey] = {
        value: innerValue,
        outermostKey: outerKey
      };
    } else {
      // Simple override: not exposed further, we just store the override's key and value.
      const override = _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.extract(item);
      if (!override) {
        continue;
      }
      const key = override.override_key;
      const value = override.override_value;
      result[key] = {
        value
      };
    }
  }
  return result;
}
function extractComponentInstanceSettings(element) {
  const instanceSetting = element.settings?.get('component_instance');
  const instanceValue = _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_2__.componentInstancePropTypeUtil.extract(instanceSetting);
  const componentId = instanceValue?.component_id?.value;
  const overrides = _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_1__.componentInstanceOverridesPropTypeUtil.extract(instanceValue?.overrides);
  return {
    componentId,
    overrides
  };
}

// Finds the inner override prop whose wrapping overridable matches the given outer key.
function findOverrideByOuterKey(overrides, outerKey) {
  if (!overrides) {
    return null;
  }
  const overridableOverride = overrides.find(item => {
    const overridableValue = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_3__.componentOverridablePropTypeUtil.extract(item);
    if (!overridableValue) {
      return false;
    }
    return overridableValue.override_key === outerKey;
  });
  const override = _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_3__.componentOverridablePropTypeUtil.extract(overridableOverride)?.origin_value;
  if (!override || !_prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_0__.componentInstanceOverridePropTypeUtil.isValid(override)) {
    return null;
  }
  return override;
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/switch-to-component.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/switch-to-component.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildUniqueSelector: function() { return /* binding */ buildUniqueSelector; },
/* harmony export */   expandNavigator: function() { return /* binding */ expandNavigator; },
/* harmony export */   switchToComponent: function() { return /* binding */ switchToComponent; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);



async function switchToComponent(componentId, componentInstanceId, element) {
  const selector = getSelector(element, componentInstanceId);
  (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.invalidateDocumentData)(componentId);
  await (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.switchToDocument)(componentId, {
    selector,
    mode: 'autosave',
    setAsInitial: false,
    shouldScroll: false
  });
  const currentDocumentContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getCurrentDocumentContainer)();
  const topLevelElement = currentDocumentContainer?.children?.[0];
  if (topLevelElement) {
    (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.selectElement)(topLevelElement.id);
    expandNavigator();
  }
}
async function expandNavigator() {
  await (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.__privateRunCommand)('navigator/expand-all');
}
function getSelector(element, componentInstanceId) {
  if (element) {
    return buildUniqueSelector(element);
  }
  if (componentInstanceId) {
    return `[data-id="${componentInstanceId}"]`;
  }
  return undefined;
}
function buildUniqueSelector(element) {
  const selectors = [];
  let current = element.closest('[data-id]');
  while (current) {
    const dataId = current.dataset.id;
    const isComponentInstance = current.hasAttribute('data-elementor-id');
    if (isComponentInstance) {
      selectors.unshift(`[data-id="${dataId}"]`);
    }
    current = current.parentElement?.closest('[data-id]') ?? null;
  }
  if (selectors.length === 0) {
    const closestElement = element.closest('[data-id]');
    if (closestElement?.dataset?.id) {
      return `[data-id="${closestElement.dataset.id}"]`;
    }
  }
  return selectors.join(' ');
}

/***/ }),

/***/ "./packages/packages/core/editor-components/src/utils/tracking.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/utils/tracking.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   onElementDrop: function() { return /* binding */ onElementDrop; },
/* harmony export */   trackComponentEvent: function() { return /* binding */ trackComponentEvent; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../store/store */ "./packages/packages/core/editor-components/src/store/store.ts");




// TODO: Remove this type in version 4.4.0
/** @deprecated since 4.2.1 - use `ExecutedBy` instead */

const FEATURE_NAME = 'Components';
const trackComponentEvent = ({
  action,
  source,
  executedBy,
  ...data
}) => {
  if (source === 'system' || executedBy === 'system') {
    return;
  }
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  if (!config?.names?.components?.[action]) {
    return;
  }
  const name = config.names.components[action];
  dispatchEvent?.(name, {
    ...data,
    executed_by: executedBy ?? source,
    'Feature name': FEATURE_NAME
  });
};

// TODO: Remove this function in version 4.4.0 - moved to pro
const onElementDrop = (_args, element) => {
  if (!(element?.model?.get('widgetType') === 'e-component')) {
    return;
  }
  const editorSettings = element.model.get('editor_settings');
  const componentName = editorSettings?.title;
  const componentUID = editorSettings?.component_uid;
  const instanceId = element.id;
  const createdThisSession = (0,_store_store__WEBPACK_IMPORTED_MODULE_2__.selectCreatedThisSession)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__getState)());
  const isSameSessionReuse = componentUID && createdThisSession.includes(componentUID);
  const eventsManagerConfig = window.elementorCommon.eventsManager.config;
  const {
    locations,
    secondaryLocations
  } = eventsManagerConfig;
  trackComponentEvent({
    action: 'instanceAdded',
    executedBy: 'user',
    instance_id: instanceId,
    component_uid: componentUID,
    component_name: componentName,
    is_same_session_reuse: isSameSessionReuse,
    location: locations.widgetPanel,
    secondary_location: secondaryLocations.componentsTab
  });
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

/***/ "@elementor/editor-controls":
/*!*************************************************!*\
  !*** external ["elementorV2","editorControls"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorControls"];

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

/***/ "@elementor/editor-elements":
/*!*************************************************!*\
  !*** external ["elementorV2","editorElements"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorElements"];

/***/ }),

/***/ "@elementor/editor-elements-panel":
/*!******************************************************!*\
  !*** external ["elementorV2","editorElementsPanel"] ***!
  \******************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorElementsPanel"];

/***/ }),

/***/ "@elementor/editor-embedded-documents-manager":
/*!*****************************************************************!*\
  !*** external ["elementorV2","editorEmbeddedDocumentsManager"] ***!
  \*****************************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorEmbeddedDocumentsManager"];

/***/ }),

/***/ "@elementor/editor-notifications":
/*!******************************************************!*\
  !*** external ["elementorV2","editorNotifications"] ***!
  \******************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorNotifications"];

/***/ }),

/***/ "@elementor/editor-panels":
/*!***********************************************!*\
  !*** external ["elementorV2","editorPanels"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorPanels"];

/***/ }),

/***/ "@elementor/editor-props":
/*!**********************************************!*\
  !*** external ["elementorV2","editorProps"] ***!
  \**********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorProps"];

/***/ }),

/***/ "@elementor/editor-templates":
/*!**************************************************!*\
  !*** external ["elementorV2","editorTemplates"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorTemplates"];

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
/*!***************************************************************!*\
  !*** ./packages/packages/core/editor-components/src/index.ts ***!
  \***************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COMPONENT_WIDGET_TYPE: function() { return /* reexport safe */ _create_component_type__WEBPACK_IMPORTED_MODULE_12__.COMPONENT_WIDGET_TYPE; },
/* harmony export */   ComponentInstanceProvider: function() { return /* reexport safe */ _provider_component_instance_context__WEBPACK_IMPORTED_MODULE_20__.ComponentInstanceProvider; },
/* harmony export */   ComponentItem: function() { return /* reexport safe */ _components_components_tab_components_item__WEBPACK_IMPORTED_MODULE_3__.ComponentItem; },
/* harmony export */   ComponentName: function() { return /* reexport safe */ _components_components_tab_components_item__WEBPACK_IMPORTED_MODULE_3__.ComponentName; },
/* harmony export */   ComponentSearch: function() { return /* reexport safe */ _components_components_tab_component_search__WEBPACK_IMPORTED_MODULE_2__.ComponentSearch; },
/* harmony export */   ComponentsList: function() { return /* reexport safe */ _components_components_tab_components_list__WEBPACK_IMPORTED_MODULE_4__.ComponentsList; },
/* harmony export */   DetachAction: function() { return /* reexport safe */ _components_instance_editing_panel_detach_action__WEBPACK_IMPORTED_MODULE_11__.DetachAction; },
/* harmony export */   EditComponentAction: function() { return /* reexport safe */ _components_instance_editing_panel_instance_panel_header__WEBPACK_IMPORTED_MODULE_9__.EditComponentAction; },
/* harmony export */   EmptySearchResult: function() { return /* reexport safe */ _components_components_tab_components_list__WEBPACK_IMPORTED_MODULE_4__.EmptySearchResult; },
/* harmony export */   InstanceEmptyState: function() { return /* reexport safe */ _components_instance_editing_panel_empty_state__WEBPACK_IMPORTED_MODULE_7__.EmptyState; },
/* harmony export */   InstancePanelBody: function() { return /* reexport safe */ _components_instance_editing_panel_instance_panel_body__WEBPACK_IMPORTED_MODULE_8__.InstancePanelBody; },
/* harmony export */   InstancePanelHeader: function() { return /* reexport safe */ _components_instance_editing_panel_instance_panel_header__WEBPACK_IMPORTED_MODULE_9__.InstancePanelHeader; },
/* harmony export */   LoadingComponents: function() { return /* reexport safe */ _components_components_tab_loading_components__WEBPACK_IMPORTED_MODULE_5__.LoadingComponents; },
/* harmony export */   OverridablePropProvider: function() { return /* reexport safe */ _provider_overridable_prop_context__WEBPACK_IMPORTED_MODULE_21__.OverridablePropProvider; },
/* harmony export */   SLICE_NAME: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.SLICE_NAME; },
/* harmony export */   SearchProvider: function() { return /* reexport safe */ _components_components_tab_search_provider__WEBPACK_IMPORTED_MODULE_6__.SearchProvider; },
/* harmony export */   apiClient: function() { return /* reexport safe */ _api__WEBPACK_IMPORTED_MODULE_1__.apiClient; },
/* harmony export */   componentInstanceOverridePropTypeUtil: function() { return /* reexport safe */ _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_16__.componentInstanceOverridePropTypeUtil; },
/* harmony export */   componentInstanceOverridesPropTypeUtil: function() { return /* reexport safe */ _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_17__.componentInstanceOverridesPropTypeUtil; },
/* harmony export */   componentInstancePropTypeUtil: function() { return /* reexport safe */ _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_18__.componentInstancePropTypeUtil; },
/* harmony export */   componentOverridablePropTypeUtil: function() { return /* reexport safe */ _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_19__.componentOverridablePropTypeUtil; },
/* harmony export */   componentsActions: function() { return /* reexport safe */ _store_dispatchers__WEBPACK_IMPORTED_MODULE_24__.componentsActions; },
/* harmony export */   componentsSelectors: function() { return /* reexport safe */ _store_selectors__WEBPACK_IMPORTED_MODULE_25__.componentsSelectors; },
/* harmony export */   createComponentsAction: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.createComponentsAction; },
/* harmony export */   filterValidOverridableProps: function() { return /* reexport safe */ _utils_filter_valid_overridable_props__WEBPACK_IMPORTED_MODULE_28__.filterValidOverridableProps; },
/* harmony export */   getComponentDocumentData: function() { return /* reexport safe */ _utils_component_document_data__WEBPACK_IMPORTED_MODULE_36__.getComponentDocumentData; },
/* harmony export */   getContainerByOriginId: function() { return /* reexport safe */ _utils_get_container_by_origin_id__WEBPACK_IMPORTED_MODULE_29__.getContainerByOriginId; },
/* harmony export */   getOverridableProp: function() { return /* reexport safe */ _utils_get_overridable_prop__WEBPACK_IMPORTED_MODULE_30__.getOverridableProp; },
/* harmony export */   getPropTypeForComponentOverride: function() { return /* reexport safe */ _utils_get_prop_type_for_component_override__WEBPACK_IMPORTED_MODULE_31__.getPropTypeForComponentOverride; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   isComponentInstance: function() { return /* reexport safe */ _utils_is_component_instance__WEBPACK_IMPORTED_MODULE_32__.isComponentInstance; },
/* harmony export */   loadComponentsAssets: function() { return /* reexport safe */ _store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_22__.loadComponentsAssets; },
/* harmony export */   onElementDrop: function() { return /* reexport safe */ _utils_tracking__WEBPACK_IMPORTED_MODULE_35__.onElementDrop; },
/* harmony export */   publishDraftComponentsInPageBeforeSave: function() { return /* reexport safe */ _sync_publish_draft_components_in_page_before_save__WEBPACK_IMPORTED_MODULE_27__.publishDraftComponentsInPageBeforeSave; },
/* harmony export */   registerComponentsReducer: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.registerComponentsReducer; },
/* harmony export */   resolveOverridePropValue: function() { return /* reexport safe */ _utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_33__.resolveOverridePropValue; },
/* harmony export */   selectComponentByUid: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.selectComponentByUid; },
/* harmony export */   selectCreatedThisSession: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.selectCreatedThisSession; },
/* harmony export */   selectOverridableProps: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.selectOverridableProps; },
/* harmony export */   selectPath: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.selectPath; },
/* harmony export */   slice: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.slice; },
/* harmony export */   switchToComponent: function() { return /* reexport safe */ _utils_switch_to_component__WEBPACK_IMPORTED_MODULE_34__.switchToComponent; },
/* harmony export */   trackComponentEvent: function() { return /* reexport safe */ _utils_tracking__WEBPACK_IMPORTED_MODULE_35__.trackComponentEvent; },
/* harmony export */   updateOverridableProp: function() { return /* reexport safe */ _store_actions_update_overridable_prop__WEBPACK_IMPORTED_MODULE_23__.updateOverridableProp; },
/* harmony export */   useComponentInstanceElement: function() { return /* reexport safe */ _provider_overridable_prop_context__WEBPACK_IMPORTED_MODULE_21__.useComponentInstanceElement; },
/* harmony export */   useComponents: function() { return /* reexport safe */ _hooks_use_components__WEBPACK_IMPORTED_MODULE_13__.useComponents; },
/* harmony export */   useComponentsPermissions: function() { return /* reexport safe */ _hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_14__.useComponentsPermissions; },
/* harmony export */   useCurrentComponent: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.useCurrentComponent; },
/* harmony export */   useCurrentComponentId: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.useCurrentComponentId; },
/* harmony export */   useFilteredComponents: function() { return /* reexport safe */ _components_components_tab_components_list__WEBPACK_IMPORTED_MODULE_4__.useFilteredComponents; },
/* harmony export */   useInstancePanelData: function() { return /* reexport safe */ _components_instance_editing_panel_use_instance_panel_data__WEBPACK_IMPORTED_MODULE_10__.useInstancePanelData; },
/* harmony export */   useIsSanitizedComponent: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.useIsSanitizedComponent; },
/* harmony export */   useOverridablePropValue: function() { return /* reexport safe */ _provider_overridable_prop_context__WEBPACK_IMPORTED_MODULE_21__.useOverridablePropValue; },
/* harmony export */   useOverridableProps: function() { return /* reexport safe */ _store_store__WEBPACK_IMPORTED_MODULE_26__.useOverridableProps; },
/* harmony export */   useSanitizeOverridableProps: function() { return /* reexport safe */ _hooks_use_sanitize_overridable_props__WEBPACK_IMPORTED_MODULE_15__.useSanitizeOverridableProps; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-components/src/init.ts");
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-components/src/api.ts");
/* harmony import */ var _components_components_tab_component_search__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/components-tab/component-search */ "./packages/packages/core/editor-components/src/components/components-tab/component-search.tsx");
/* harmony import */ var _components_components_tab_components_item__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/components-tab/components-item */ "./packages/packages/core/editor-components/src/components/components-tab/components-item.tsx");
/* harmony import */ var _components_components_tab_components_list__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/components-tab/components-list */ "./packages/packages/core/editor-components/src/components/components-tab/components-list.tsx");
/* harmony import */ var _components_components_tab_loading_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/components-tab/loading-components */ "./packages/packages/core/editor-components/src/components/components-tab/loading-components.tsx");
/* harmony import */ var _components_components_tab_search_provider__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/components-tab/search-provider */ "./packages/packages/core/editor-components/src/components/components-tab/search-provider.tsx");
/* harmony import */ var _components_instance_editing_panel_empty_state__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/instance-editing-panel/empty-state */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/empty-state.tsx");
/* harmony import */ var _components_instance_editing_panel_instance_panel_body__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/instance-editing-panel/instance-panel-body */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-body.tsx");
/* harmony import */ var _components_instance_editing_panel_instance_panel_header__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/instance-editing-panel/instance-panel-header */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/instance-panel-header.tsx");
/* harmony import */ var _components_instance_editing_panel_use_instance_panel_data__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./components/instance-editing-panel/use-instance-panel-data */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/use-instance-panel-data.ts");
/* harmony import */ var _components_instance_editing_panel_detach_action__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./components/instance-editing-panel/detach-action */ "./packages/packages/core/editor-components/src/components/instance-editing-panel/detach-action.tsx");
/* harmony import */ var _create_component_type__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./create-component-type */ "./packages/packages/core/editor-components/src/create-component-type.ts");
/* harmony import */ var _hooks_use_components__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./hooks/use-components */ "./packages/packages/core/editor-components/src/hooks/use-components.ts");
/* harmony import */ var _hooks_use_components_permissions__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./hooks/use-components-permissions */ "./packages/packages/core/editor-components/src/hooks/use-components-permissions.ts");
/* harmony import */ var _hooks_use_sanitize_overridable_props__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./hooks/use-sanitize-overridable-props */ "./packages/packages/core/editor-components/src/hooks/use-sanitize-overridable-props.ts");
/* harmony import */ var _prop_types_component_instance_override_prop_type__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./prop-types/component-instance-override-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-override-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_overrides_prop_type__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./prop-types/component-instance-overrides-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-overrides-prop-type.ts");
/* harmony import */ var _prop_types_component_instance_prop_type__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./prop-types/component-instance-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-instance-prop-type.ts");
/* harmony import */ var _prop_types_component_overridable_prop_type__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./prop-types/component-overridable-prop-type */ "./packages/packages/core/editor-components/src/prop-types/component-overridable-prop-type.ts");
/* harmony import */ var _provider_component_instance_context__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./provider/component-instance-context */ "./packages/packages/core/editor-components/src/provider/component-instance-context.tsx");
/* harmony import */ var _provider_overridable_prop_context__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./provider/overridable-prop-context */ "./packages/packages/core/editor-components/src/provider/overridable-prop-context.tsx");
/* harmony import */ var _store_actions_load_components_assets__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./store/actions/load-components-assets */ "./packages/packages/core/editor-components/src/store/actions/load-components-assets.ts");
/* harmony import */ var _store_actions_update_overridable_prop__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./store/actions/update-overridable-prop */ "./packages/packages/core/editor-components/src/store/actions/update-overridable-prop.ts");
/* harmony import */ var _store_dispatchers__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./store/dispatchers */ "./packages/packages/core/editor-components/src/store/dispatchers.ts");
/* harmony import */ var _store_selectors__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./store/selectors */ "./packages/packages/core/editor-components/src/store/selectors.ts");
/* harmony import */ var _store_store__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./store/store */ "./packages/packages/core/editor-components/src/store/store.ts");
/* harmony import */ var _sync_publish_draft_components_in_page_before_save__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./sync/publish-draft-components-in-page-before-save */ "./packages/packages/core/editor-components/src/sync/publish-draft-components-in-page-before-save.ts");
/* harmony import */ var _utils_filter_valid_overridable_props__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./utils/filter-valid-overridable-props */ "./packages/packages/core/editor-components/src/utils/filter-valid-overridable-props.ts");
/* harmony import */ var _utils_get_container_by_origin_id__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./utils/get-container-by-origin-id */ "./packages/packages/core/editor-components/src/utils/get-container-by-origin-id.ts");
/* harmony import */ var _utils_get_overridable_prop__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./utils/get-overridable-prop */ "./packages/packages/core/editor-components/src/utils/get-overridable-prop.ts");
/* harmony import */ var _utils_get_prop_type_for_component_override__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./utils/get-prop-type-for-component-override */ "./packages/packages/core/editor-components/src/utils/get-prop-type-for-component-override.ts");
/* harmony import */ var _utils_is_component_instance__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./utils/is-component-instance */ "./packages/packages/core/editor-components/src/utils/is-component-instance.ts");
/* harmony import */ var _utils_resolve_override_prop_value__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./utils/resolve-override-prop-value */ "./packages/packages/core/editor-components/src/utils/resolve-override-prop-value.ts");
/* harmony import */ var _utils_switch_to_component__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./utils/switch-to-component */ "./packages/packages/core/editor-components/src/utils/switch-to-component.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-components/src/utils/tracking.ts");
/* harmony import */ var _utils_component_document_data__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./utils/component-document-data */ "./packages/packages/core/editor-components/src/utils/component-document-data.ts");





































}();
(window.elementorV2 = window.elementorV2 || {}).editorComponents = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorComponents?.init?.();
//# sourceMappingURL=editor-components.js.map
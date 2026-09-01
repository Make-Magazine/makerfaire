/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-variables/src/api.ts":
/*!************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/api.ts ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   apiClient: function() { return /* binding */ apiClient; }
/* harmony export */ });
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__);

const BASE_PATH = 'elementor/v1/variables';
const apiClient = {
  list: () => {
    return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().get(BASE_PATH + '/list');
  },
  create: (type, label, value) => {
    return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().post(BASE_PATH + '/create', {
      type,
      label,
      value
    });
  },
  update: (id, label, value, type) => {
    return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().put(BASE_PATH + '/update', {
      id,
      label,
      value,
      type
    });
  },
  delete: id => {
    return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().post(BASE_PATH + '/delete', {
      id
    });
  },
  restore: (id, label, value, type) => {
    const payload = {
      id
    };
    if (label) {
      payload.label = label;
    }
    if (value) {
      payload.value = value;
    }
    if (type) {
      payload.type = type;
    }
    return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().post(BASE_PATH + '/restore', payload);
  },
  batch: payload => {
    return (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().post(BASE_PATH + '/batch', payload);
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/batch-operations.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/batch-operations.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildOperationsArray: function() { return /* binding */ buildOperationsArray; },
/* harmony export */   generateTempId: function() { return /* binding */ generateTempId; },
/* harmony export */   isTempId: function() { return /* binding */ isTempId; }
/* harmony export */ });
const generateTempId = () => {
  const timestamp = Date.now().toString(36);
  const random = Math.random().toString(36).substring(2, 8);
  return `tmp-${timestamp}-${random}`;
};
const isTempId = id => {
  return id.startsWith('tmp-');
};
const buildOperationsArray = (originalVariables, currentVariables, deletedVariables) => {
  const operations = [];
  Object.entries(currentVariables).forEach(([id, variable]) => {
    if (isTempId(id)) {
      operations.push({
        type: 'create',
        variable: {
          ...variable,
          id
        }
      });
    } else if (originalVariables[id]) {
      const original = originalVariables[id];
      const syncChanged = original.sync_to_v3 !== variable.sync_to_v3;
      if (original.deleted && !variable.deleted) {
        operations.push({
          type: 'restore',
          id,
          ...(original.label !== variable.label && {
            label: variable.label
          }),
          ...(original.value !== variable.value && {
            value: variable.value
          })
        });
      } else if (!variable.deleted && (original.label !== variable.label || original.value !== variable.value || original.order !== variable.order || original.type !== variable.type || syncChanged)) {
        operations.push({
          type: 'update',
          id,
          variable: {
            ...(original.label !== variable.label && {
              label: variable.label
            }),
            ...(original.value !== variable.value && {
              value: variable.value
            }),
            ...(original.order !== variable.order && {
              order: variable.order
            }),
            ...(original.type !== variable.type && {
              type: variable.type
            }),
            ...(syncChanged && {
              sync_to_v3: variable.sync_to_v3
            })
          }
        });
      }
    }
  });
  deletedVariables.forEach(id => {
    operations.push({
      type: 'delete',
      id
    });
  });
  return operations.filter(op => {
    const id = op.id || op.variable?.id;
    return id && !(isTempId(id) && currentVariables[id]?.deleted);
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/fields/color-field.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/fields/color-field.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ColorField: function() { return /* binding */ ColorField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../context/variable-selection-popover.context */ "./packages/packages/core/editor-variables/src/context/variable-selection-popover.context.tsx");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");





const ColorField = ({
  value,
  onChange,
  onValidationChange
}) => {
  const [color, setColor] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(value);
  const [errorMessage, setErrorMessage] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const defaultRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const anchorRef = (0,_context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_2__.usePopoverContentRef)() ?? defaultRef.current;
  const handleChange = newValue => {
    setColor(newValue);
    const errorMsg = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_3__.validateValue)(newValue);
    setErrorMessage(errorMsg);
    onValidationChange?.(errorMsg);
    onChange(errorMsg ? '' : newValue);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.UnstableColorField, {
    id: "color-variable-field",
    size: "tiny",
    fullWidth: true,
    value: color,
    onChange: handleChange,
    error: errorMessage || undefined,
    slotProps: {
      colorPicker: {
        anchorEl: anchorRef,
        anchorOrigin: {
          vertical: 'top',
          horizontal: 'right'
        },
        transformOrigin: {
          vertical: 'top',
          horizontal: -10
        },
        slotProps: {
          colorIndicator: {
            size: 'inherit',
            sx: {
              borderRadius: 0.5
            }
          }
        }
      }
    }
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/fields/font-field.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/fields/font-field.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FontField: function() { return /* binding */ FontField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../context/variable-selection-popover.context */ "./packages/packages/core/editor-variables/src/context/variable-selection-popover.context.tsx");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









const FontField = ({
  value,
  onChange,
  onValidationChange
}) => {
  const [fontFamily, setFontFamily] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(value);
  const defaultRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const anchorRef = (0,_context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_6__.usePopoverContentRef)() ?? defaultRef.current;
  const fontPopoverState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.usePopupState)({
    variant: 'popover'
  });
  const fontFamilies = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useFontFamilies)();
  const sectionWidth = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.useSectionWidth)();
  const mapFontSubs = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return fontFamilies.map(({
      label,
      fonts
    }) => ({
      label,
      items: fonts
    }));
  }, [fontFamilies]);
  const handleChange = newValue => {
    setFontFamily(newValue);
    const errorMsg = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_7__.validateValue)(newValue);
    onValidationChange?.(errorMsg);
    onChange(errorMsg ? '' : newValue);
  };
  const handleFontFamilyChange = newFontFamily => {
    handleChange(newFontFamily);
    fontPopoverState.close();
  };
  const id = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.UnstableTag, _extends({
    id: id,
    variant: "outlined",
    label: fontFamily,
    endIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ChevronDownIcon, {
      fontSize: "tiny"
    })
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindTrigger)(fontPopoverState), {
    fullWidth: true
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Popover, _extends({
    disablePortal: true,
    disableScrollLock: true,
    anchorEl: anchorRef,
    anchorOrigin: {
      vertical: 'top',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: -28
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindPopover)(fontPopoverState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ItemSelector, {
    id: "font-family-variables-selector",
    itemsList: mapFontSubs,
    selectedItem: fontFamily,
    onItemChange: handleFontFamilyChange,
    onClose: fontPopoverState.close,
    sectionWidth: sectionWidth,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Font family', 'elementor'),
    itemStyle: item => ({
      fontFamily: item.value
    }),
    onDebounce: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.enqueueFont,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_3__.TextIcon
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/fields/label-field.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/fields/label-field.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LabelField: function() { return /* binding */ LabelField; },
/* harmony export */   useLabelError: function() { return /* binding */ useLabelError; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





function isLabelEqual(a, b) {
  return a.trim().toLowerCase() === b.trim().toLowerCase();
}
const useLabelError = initialError => {
  const [error, setError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialError ?? {
    value: '',
    message: ''
  });
  return {
    labelFieldError: error,
    setLabelFieldError: setError
  };
};
const LabelField = ({
  value,
  error,
  onChange,
  id,
  onErrorChange,
  size = 'tiny',
  focusOnShow = false,
  selectOnShow = false,
  showWarningInfotip = false,
  variables,
  onKeyDown
}) => {
  const [label, setLabel] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(value);
  const [errorMessage, setErrorMessage] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const fieldRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const handleChange = newValue => {
    setLabel(newValue);
    const errorMsg = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_3__.validateLabel)(newValue, variables);
    setErrorMessage(errorMsg);
    onErrorChange?.(errorMsg);
    onChange(isLabelEqual(newValue, error?.value ?? '') || errorMsg ? '' : newValue);
  };
  let errorMsg = errorMessage;
  if (isLabelEqual(label, error?.value ?? '') && error?.message) {
    errorMsg = error.message;
  }
  const hintMsg = !errorMsg ? (0,_utils_validations__WEBPACK_IMPORTED_MODULE_3__.labelHint)(label) : '';
  const textField = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.TextField, {
    ref: fieldRef,
    id: id,
    size: size,
    fullWidth: true,
    value: label,
    error: !!errorMsg,
    onChange: e => handleChange(e.target.value),
    inputProps: {
      maxLength: _utils_validations__WEBPACK_IMPORTED_MODULE_3__.VARIABLE_LABEL_MAX_LENGTH,
      ...(selectOnShow && {
        onFocus: e => e.target.select()
      }),
      'aria-label': 'Name',
      onKeyDown
    }
    // eslint-disable-next-line jsx-a11y/no-autofocus
    ,
    autoFocus: focusOnShow
  });
  if (showWarningInfotip) {
    const tooltipWidth = Math.max(240, fieldRef.current?.getBoundingClientRect().width ?? 240);
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.WarningInfotip, _extends({
      open: Boolean(errorMsg || hintMsg),
      text: errorMsg || hintMsg,
      placement: "bottom-start",
      width: tooltipWidth,
      offset: [0, -15]
    }, hintMsg && {
      hasError: false
    }), textField);
  }
  return textField;
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/global-styles-import-listener.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/global-styles-import-listener.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GlobalStylesImportListener: function() { return /* binding */ GlobalStylesImportListener; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");



function GlobalStylesImportListener() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleGlobalStylesImported = () => {
      _service__WEBPACK_IMPORTED_MODULE_2__.service.load();
    };
    window.addEventListener(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.GLOBAL_STYLES_IMPORTED_EVENT, handleGlobalStylesImported);
    return () => {
      window.removeEventListener(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.GLOBAL_STYLES_IMPORTED_EVENT, handleGlobalStylesImported);
    };
  }, []);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/mcp-variable-connect-listener.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/mcp-variable-connect-listener.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   McpVariableConnectListener: function() { return /* binding */ McpVariableConnectListener; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_extract_variables_from_style_value__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/extract-variables-from-style-value */ "./packages/packages/core/editor-variables/src/utils/extract-variables-from-style-value.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");




function McpVariableConnectListener() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleMcpStylesApplied = event => {
      const {
        styleValue
      } = event.detail;
      const variables = (0,_utils_extract_variables_from_style_value__WEBPACK_IMPORTED_MODULE_2__.extractVariablesFromStyleValue)(styleValue);
      variables.forEach(({
        type,
        controlPath
      }) => {
        (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_3__.trackVariableEvent)({
          varType: type,
          controlPath,
          action: 'connect',
          executedBy: 'mcp_tool'
        });
      });
    };
    window.addEventListener(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.MCP_STYLES_APPLIED_EVENT, handleMcpStylesApplied);
    return () => {
      window.removeEventListener(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_1__.MCP_STYLES_APPLIED_EVENT, handleMcpStylesApplied);
    };
  }, []);
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/color-indicator.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/color-indicator.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ColorIndicator: function() { return /* binding */ ColorIndicator; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const ColorIndicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.UnstableColorIndicator)(({
  theme
}) => ({
  borderRadius: `${theme.shape.borderRadius / 2}px`,
  marginRight: theme.spacing(0.25)
}));

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/delete-confirmation-dialog.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/delete-confirmation-dialog.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DeleteConfirmationDialog: function() { return /* binding */ DeleteConfirmationDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const DeleteConfirmationDialog = ({
  open,
  label,
  closeDialog,
  onConfirm
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog, {
    open: open,
    onClose: closeDialog
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Title, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Delete this variable?', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('All elements using', 'elementor'), "\xA0", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "subtitle2",
    component: "span",
    sx: {
      lineBreak: 'anywhere'
    }
  }, label), "\xA0", (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('will keep their current values, but the variable itself will be removed.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.ConfirmationDialog.Actions, {
    onClose: closeDialog,
    onConfirm: onConfirm
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/deleted-variable-alert.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/deleted-variable-alert.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DeletedVariableAlert: function() { return /* binding */ DeletedVariableAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const DeletedVariableAlert = ({
  onClose,
  onUnlink,
  onRestore,
  label
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ClickAwayListener, {
    onClickAway: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Alert, {
    variant: "standard",
    severity: "warning",
    onClose: onClose,
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, onUnlink && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertAction, {
      variant: "contained",
      onClick: onUnlink
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Unlink', 'elementor')), onRestore && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertAction, {
      variant: "outlined",
      onClick: onRestore
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Restore', 'elementor'))),
    sx: {
      maxWidth: 300
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Deleted variable', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    color: "textPrimary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('The variable', 'elementor'), "\xA0'", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    component: "span",
    sx: {
      lineBreak: 'anywhere'
    }
  }, label), "'\xA0", (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('has been deleted, but it is still referenced in this location. You may restore the variable or unlink it to assign a different value.', 'elementor'))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/edit-confirmation-dialog.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/edit-confirmation-dialog.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDIT_CONFIRMATION_DIALOG_ID: function() { return /* binding */ EDIT_CONFIRMATION_DIALOG_ID; },
/* harmony export */   EditConfirmationDialog: function() { return /* binding */ EditConfirmationDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);





const EDIT_CONFIRMATION_DIALOG_ID = 'edit-confirmation-dialog';
const EditConfirmationDialog = ({
  closeDialog,
  onConfirm,
  onSuppressMessage
}) => {
  const [dontShowAgain, setDontShowAgain] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const handleSave = () => {
    if (dontShowAgain) {
      onSuppressMessage?.();
    }
    onConfirm?.();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Dialog, {
    open: true,
    onClose: closeDialog,
    maxWidth: "xs"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogTitle, {
    display: "flex",
    alignItems: "center",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.AlertTriangleFilledIcon, {
    color: "secondary"
  }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Changes to variables go live right away.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogContentText, {
    variant: "body2",
    color: "textPrimary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)("Don't worry - all other changes you make will wait until you publish your site.", 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.DialogActions, {
    sx: {
      justifyContent: 'space-between',
      alignItems: 'center'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.FormControlLabel, {
    control: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Checkbox, {
      checked: dontShowAgain,
      onChange: event => setDontShowAgain(event.target.checked),
      size: "small"
    }),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
      variant: "body2"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)("Don't show me again", 'elementor'))
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    color: "secondary",
    onClick: closeDialog
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Keep editing', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    variant: "contained",
    color: "secondary",
    onClick: handleSave,
    sx: {
      ml: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Save', 'elementor')))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/empty-state.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/empty-state.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EmptyState: function() { return /* binding */ EmptyState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_permissions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../hooks/use-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-permissions.ts");




const EmptyState = ({
  icon,
  title,
  message,
  onAdd,
  children
}) => {
  const canAdd = (0,_hooks_use_permissions__WEBPACK_IMPORTED_MODULE_3__.usePermissions)().canAdd();
  const displayTitle = canAdd ? title : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('There are no variables', 'elementor');
  const displayMessage = canAdd ? message : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('With your current role, you can only connect and detach variables.', 'elementor');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Content, {
    title: displayTitle,
    message: displayMessage,
    icon: icon
  }, children || onAdd && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "outlined",
    color: "secondary",
    size: "small",
    onClick: onAdd
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Create a variable', 'elementor')));
};
function Content({
  title,
  message,
  icon,
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    gap: 1,
    alignItems: "center",
    justifyContent: "flex-start",
    height: "100%",
    color: "text.secondary",
    sx: {
      p: 2.5,
      pt: 8,
      pb: 5.5
    }
  }, icon, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    align: "center",
    variant: "subtitle2"
  }, title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    align: "center",
    variant: "caption",
    maxWidth: "180px"
  }, message), children);
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/form-field.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/form-field.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormField: function() { return /* binding */ FormField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const FormField = ({
  id,
  label,
  errorMsg,
  noticeMsg,
  children
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Grid, {
    container: true,
    gap: 0.75,
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Grid, {
    item: true,
    xs: 12
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.FormLabel, {
    htmlFor: id,
    size: "tiny"
  }, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Grid, {
    item: true,
    xs: 12
  }, children, errorMsg && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.FormHelperText, {
    error: true
  }, errorMsg), noticeMsg && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.FormHelperText, null, noticeMsg)));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/menu-item-content.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/menu-item-content.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MenuItemContent: function() { return /* binding */ MenuItemContent; }
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





const SIZE = 'tiny';
const EDIT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Edit variable', 'elementor');
const MenuItemContent = ({
  item,
  disabled = false
}) => {
  const onEdit = item.onEdit;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemIcon, {
    sx: {
      color: disabled ? 'text.disabled' : 'inherit'
    }
  }, item.icon), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    sx: {
      flex: 1,
      minWidth: 0,
      display: 'flex',
      alignItems: 'center',
      gap: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: item.label || item.value,
    as: _elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography,
    variant: "caption",
    color: disabled ? 'text.disabled' : 'text.primary',
    sx: {
      marginTop: '1px',
      lineHeight: '2'
    },
    maxWidth: "50%"
  }), item.secondaryText && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: item.secondaryText,
    as: _elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography,
    variant: "caption",
    color: disabled ? 'text.disabled' : 'text.tertiary',
    sx: {
      marginTop: '1px',
      lineHeight: '1'
    },
    maxWidth: "50%"
  })), !!onEdit && !disabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    placement: "top",
    title: EDIT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.IconButton, {
    sx: {
      mx: 1,
      opacity: '0'
    },
    onClick: e => {
      e.stopPropagation();
      onEdit(item.value);
    },
    "aria-label": EDIT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.EditIcon, {
    color: "action",
    fontSize: SIZE
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/mismatch-variable-alert.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/mismatch-variable-alert.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MismatchVariableAlert: function() { return /* binding */ MismatchVariableAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const i18n = {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Variable has changed', 'elementor'),
  message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)(`This variable is no longer compatible with this property. You can clear it or select a different one.`, 'elementor'),
  buttons: {
    clear: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Clear', 'elementor'),
    select: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Select variable', 'elementor')
  }
};
const MismatchVariableAlert = ({
  onClose,
  onClear,
  triggerSelect
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ClickAwayListener, {
    onClickAway: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Alert, {
    variant: "standard",
    severity: "warning",
    onClose: onClose,
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, onClear && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertAction, {
      variant: "contained",
      onClick: onClear
    }, i18n.buttons.clear), triggerSelect && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertAction, {
      variant: "outlined",
      onClick: triggerSelect
    }, i18n.buttons.select)),
    sx: {
      maxWidth: 300
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertTitle, null, i18n.title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    color: "textPrimary"
  }, i18n.message)));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/missing-variable-alert.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/missing-variable-alert.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MissingVariableAlert: function() { return /* binding */ MissingVariableAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const MissingVariableAlert = ({
  onClose,
  onClear
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ClickAwayListener, {
    onClickAway: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Alert, {
    variant: "standard",
    severity: "warning",
    onClose: onClose,
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, onClear && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertAction, {
      variant: "contained",
      onClick: onClear
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Clear', 'elementor'))),
    sx: {
      maxWidth: 300
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.AlertTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('This variable is missing', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    color: "textPrimary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('It may have been deleted. Try clearing this field and select a different value or variable.', 'elementor'))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/no-search-results.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/no-search-results.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NoSearchResults: function() { return /* binding */ NoSearchResults; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const NoSearchResults = ({
  searchValue,
  onClear,
  icon
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    gap: 1,
    alignItems: "center",
    justifyContent: "center",
    p: 2.5,
    color: "text.secondary",
    sx: {
      pb: 3.5,
      pt: 8
    }
  }, icon, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    align: "center",
    variant: "subtitle2"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Sorry, nothing matched', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), "\u201C", searchValue, "\u201D."), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    align: "center",
    variant: "caption",
    sx: {
      display: 'flex',
      flexDirection: 'column'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Try something else.', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Link, {
    color: "text.secondary",
    variant: "caption",
    component: "button",
    onClick: onClear
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Clear & try again', 'elementor'))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/styled-menu-list.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/styled-menu-list.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariablesStyledMenuList: function() { return /* binding */ VariablesStyledMenuList; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const VariablesStyledMenuList = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.MenuList)(({
  theme,
  disabled
}) => ({
  '& > li': {
    height: 32,
    width: '100%',
    display: 'flex',
    alignItems: 'center'
  },
  '& > [role="option"]': {
    ...theme.typography.caption,
    lineHeight: 'inherit',
    padding: theme.spacing(0.5, 1, 0.5, 2),
    ...(!disabled && {
      '&:hover, &:focus': {
        backgroundColor: theme.palette.action.hover
      },
      cursor: 'pointer'
    }),
    '&[aria-selected="true"]': {
      backgroundColor: theme.palette.action.selected
    },
    textOverflow: 'ellipsis',
    position: 'absolute',
    top: 0,
    left: 0,
    '&:hover .MuiIconButton-root, .MuiIconButton-root:focus': {
      opacity: 1
    }
  },
  width: '100%',
  position: 'relative'
}));

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/tags/assigned-tag.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/tags/assigned-tag.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AssignedTag: function() { return /* binding */ AssignedTag; },
/* harmony export */   SIZE: function() { return /* binding */ SIZE; }
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




const SIZE = 'tiny';
const UNLINK_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Unlink variable', 'elementor');
const AssignedTag = ({
  startIcon,
  label,
  onUnlink,
  ...props
}) => {
  const actions = [];
  if (onUnlink) {
    actions.push(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip, {
      key: "unlink",
      title: UNLINK_LABEL,
      placement: "bottom"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
      size: SIZE,
      onClick: onUnlink,
      "aria-label": UNLINK_LABEL
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.DetachIcon, {
      fontSize: SIZE
    }))));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip, {
    title: label,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.UnstableTag, _extends({
    fullWidth: true,
    showActionsOnHover: true,
    startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
      gap: 0.5,
      direction: "row",
      alignItems: "center"
    }, startIcon),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
      sx: {
        display: 'inline-grid',
        minWidth: 0
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
      sx: {
        lineHeight: 1.34
      },
      variant: "caption",
      noWrap: true
    }, label)),
    actions: actions
  }, props)));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/tags/warning-variable-tag.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/tags/warning-variable-tag.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   WarningVariableTag: function() { return /* binding */ WarningVariableTag; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



const WarningVariableTag = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  label,
  suffix,
  onClick,
  icon,
  ...props
}, ref) => {
  const displayText = suffix ? `${label} (${suffix})` : label;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Chip, _extends({
    ref: ref,
    size: "tiny",
    color: "warning",
    shape: "rounded",
    variant: "standard",
    onClick: onClick,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.AlertTriangleFilledIcon, null),
    label: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip, {
      title: displayText,
      placement: "top"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
      sx: {
        display: 'inline-grid',
        minWidth: 0
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
      variant: "caption",
      noWrap: true,
      sx: {
        lineHeight: 1.34
      }
    }, displayText))),
    sx: {
      height: theme => theme.spacing(3.5),
      borderRadius: theme => theme.spacing(1),
      justifyContent: 'flex-start',
      width: '100%'
    }
  }, props));
});
WarningVariableTag.displayName = 'WarningVariableTag';

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/variable-promotion-chip.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/variable-promotion-chip.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariablePromotionChip: function() { return /* binding */ VariablePromotionChip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);







const VariablePromotionChip = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  variableType,
  upgradeUrl,
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
  const title = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.sprintf)(/* translators: %s: Variable Type. */
  (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('%s variables', 'elementor'), (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_4__.capitalize)(variableType));
  const content = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.sprintf)(/* translators: %s: Variable Type. */
  (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Upgrade to continue creating and editing %s variables.', 'elementor'), variableType);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PromotionPopover, {
    open: isOpen,
    title: title,
    content: content,
    ctaText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Upgrade now', 'elementor'),
    ctaUrl: upgradeUrl,
    onClose: e => {
      e.stopPropagation();
      setIsOpen(false);
    },
    onCtaClick: () => (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackUpgradePromotionClick)(trackingData)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    onClick: e => {
      e.stopPropagation();
      toggle();
    },
    sx: {
      cursor: 'pointer',
      display: 'inline-flex'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PromotionChip, null)));
});

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/variable/assigned-variable.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/variable/assigned-variable.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AssignedVariable: function() { return /* binding */ AssignedVariable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _utils_unlink_variable__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../utils/unlink-variable */ "./packages/packages/core/editor-variables/src/utils/unlink-variable.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _variable_selection_popover__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../variable-selection-popover */ "./packages/packages/core/editor-variables/src/components/variable-selection-popover.tsx");
/* harmony import */ var _tags_assigned_tag__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../tags/assigned-tag */ "./packages/packages/core/editor-variables/src/components/ui/tags/assigned-tag.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









const AssignedVariable = ({
  variable,
  propTypeKey
}) => {
  const {
    startIcon,
    propTypeUtil
  } = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_5__.getVariableType)(propTypeKey);
  const {
    setValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const anchorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const popupId = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.usePopupState)({
    variant: 'popover',
    popupId: `elementor-variables-list-${popupId}`
  });
  const unlinkVariable = (0,_utils_unlink_variable__WEBPACK_IMPORTED_MODULE_4__.createUnlinkHandler)(variable, propTypeKey, setValue);
  const StartIcon = startIcon || (() => null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    ref: anchorRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_tags_assigned_tag__WEBPACK_IMPORTED_MODULE_7__.AssignedTag, _extends({
    label: variable.label,
    startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ColorFilterIcon, {
      fontSize: _tags_assigned_tag__WEBPACK_IMPORTED_MODULE_7__.SIZE
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartIcon, {
      value: variable.value
    })),
    onUnlink: unlinkVariable
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindTrigger)(popupState))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Popover, _extends({
    disableScrollLock: true,
    anchorEl: anchorRef.current,
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
        my: 1
      }
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindPopover)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_selection_popover__WEBPACK_IMPORTED_MODULE_6__.VariableSelectionPopover, {
    selectedVariable: variable,
    closePopover: popupState.close,
    propTypeKey: propTypeUtil.key
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/variable/deleted-variable.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/variable/deleted-variable.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DeletedVariable: function() { return /* binding */ DeletedVariable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _hooks_use_permissions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../hooks/use-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-permissions.ts");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../hooks/use-variable-bound-prop */ "./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts");
/* harmony import */ var _utils_unlink_variable__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../../utils/unlink-variable */ "./packages/packages/core/editor-variables/src/utils/unlink-variable.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _variable_restore__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../variable-restore */ "./packages/packages/core/editor-variables/src/components/variable-restore.tsx");
/* harmony import */ var _deleted_variable_alert__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../deleted-variable-alert */ "./packages/packages/core/editor-variables/src/components/ui/deleted-variable-alert.tsx");
/* harmony import */ var _tags_warning_variable_tag__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../tags/warning-variable-tag */ "./packages/packages/core/editor-variables/src/components/ui/tags/warning-variable-tag.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }














const DeletedVariable = ({
  variable,
  propTypeKey
}) => {
  const {
    propTypeUtil
  } = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_9__.getVariableType)(propTypeKey);
  const boundProp = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const userPermissions = (0,_hooks_use_permissions__WEBPACK_IMPORTED_MODULE_5__.usePermissions)();
  const [showInfotip, setShowInfotip] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const toggleInfotip = () => setShowInfotip(prev => !prev);
  const closeInfotip = () => setShowInfotip(false);
  const deletedChipAnchorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const popupId = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.usePopupState)({
    variant: 'popover',
    popupId: `elementor-variables-restore-${popupId}`
  });
  const handlers = {};
  if (userPermissions.canUnlink()) {
    handlers.onUnlink = (0,_utils_unlink_variable__WEBPACK_IMPORTED_MODULE_8__.createUnlinkHandler)(variable, propTypeKey, boundProp.setValue);
  }
  if (userPermissions.canRestore()) {
    handlers.onRestore = () => {
      if (!variable.key) {
        return;
      }
      (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_6__.restoreVariable)(variable.key).then(id => {
        (0,_hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_7__.resolveBoundPropAndSetValue)(propTypeUtil.create(id), boundProp);
        closeInfotip();
      }).catch(() => {
        closeInfotip();
        popupState.setAnchorEl(deletedChipAnchorRef.current);
        popupState.open();
      });
    };
  }
  const handleRestoreWithOverrides = () => {
    popupState.close();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    ref: deletedChipAnchorRef
  }, showInfotip && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Backdrop, {
    open: true,
    onClick: closeInfotip,
    invisible: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Infotip, {
    color: "warning",
    placement: "right-start",
    open: showInfotip,
    disableHoverListener: true,
    onClose: closeInfotip,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_deleted_variable_alert__WEBPACK_IMPORTED_MODULE_11__.DeletedVariableAlert, {
      onClose: closeInfotip,
      onUnlink: handlers.onUnlink,
      onRestore: handlers.onRestore,
      label: variable.label
    }),
    slotProps: {
      popper: {
        modifiers: [{
          name: 'offset',
          options: {
            offset: [0, 24]
          }
        }]
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_tags_warning_variable_tag__WEBPACK_IMPORTED_MODULE_12__.WarningVariableTag, {
    label: variable.label,
    onClick: toggleInfotip,
    suffix: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('deleted', 'elementor')
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Popover, _extends({
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
        my: 1
      }
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindPopover)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_context_variable_type_context__WEBPACK_IMPORTED_MODULE_4__.VariableTypeProvider, {
    propTypeKey: propTypeKey
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_restore__WEBPACK_IMPORTED_MODULE_10__.VariableRestore, {
    variableId: variable.key ?? '',
    onClose: popupState.close,
    onSubmit: handleRestoreWithOverrides
  })))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/variable/mismatch-variable.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/variable/mismatch-variable.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MismatchVariable: function() { return /* binding */ MismatchVariable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _variable_selection_popover__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../variable-selection-popover */ "./packages/packages/core/editor-variables/src/components/variable-selection-popover.tsx");
/* harmony import */ var _mismatch_variable_alert__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../mismatch-variable-alert */ "./packages/packages/core/editor-variables/src/components/ui/mismatch-variable-alert.tsx");
/* harmony import */ var _tags_warning_variable_tag__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../tags/warning-variable-tag */ "./packages/packages/core/editor-variables/src/components/ui/tags/warning-variable-tag.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }








const MismatchVariable = ({
  variable
}) => {
  const {
    setValue,
    value
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const anchorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const popupId = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.usePopupState)({
    variant: 'popover',
    popupId: `elementor-variables-list-${popupId}`
  });
  const [infotipVisible, setInfotipVisible] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const toggleInfotip = () => setInfotipVisible(prev => !prev);
  const closeInfotip = () => setInfotipVisible(false);
  const triggerSelect = () => {
    closeInfotip();
    popupState.setAnchorEl(anchorRef.current);
    popupState.open();
  };
  const clearValue = () => {
    closeInfotip();
    setValue(null);
  };
  const showClearButton = !!value;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    ref: anchorRef
  }, infotipVisible && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Backdrop, {
    open: true,
    onClick: closeInfotip,
    invisible: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Infotip, {
    color: "warning",
    placement: "right-start",
    open: infotipVisible,
    disableHoverListener: true,
    onClose: closeInfotip,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_mismatch_variable_alert__WEBPACK_IMPORTED_MODULE_5__.MismatchVariableAlert, {
      onClose: closeInfotip,
      onClear: showClearButton ? clearValue : undefined,
      triggerSelect: triggerSelect
    }),
    slotProps: {
      popper: {
        modifiers: [{
          name: 'offset',
          options: {
            offset: [0, 24]
          }
        }]
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_tags_warning_variable_tag__WEBPACK_IMPORTED_MODULE_6__.WarningVariableTag, {
    label: variable.label,
    onClick: toggleInfotip,
    suffix: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('changed', 'elementor')
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Popover, _extends({
    disableScrollLock: true,
    anchorEl: anchorRef.current,
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
        my: 1
      }
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindPopover)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_selection_popover__WEBPACK_IMPORTED_MODULE_4__.VariableSelectionPopover, {
    selectedVariable: variable,
    closePopover: popupState.close,
    propTypeKey: variable.type
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/ui/variable/missing-variable.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/ui/variable/missing-variable.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MissingVariable: function() { return /* binding */ MissingVariable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _missing_variable_alert__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../missing-variable-alert */ "./packages/packages/core/editor-variables/src/components/ui/missing-variable-alert.tsx");
/* harmony import */ var _tags_warning_variable_tag__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../tags/warning-variable-tag */ "./packages/packages/core/editor-variables/src/components/ui/tags/warning-variable-tag.tsx");







const MissingVariable = () => {
  const {
    setValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const [infotipVisible, setInfotipVisible] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const toggleInfotip = () => setInfotipVisible(prev => !prev);
  const closeInfotip = () => setInfotipVisible(false);
  const clearValue = () => setValue(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, infotipVisible && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Backdrop, {
    open: true,
    onClick: closeInfotip,
    invisible: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Infotip, {
    color: "warning",
    placement: "right-start",
    open: infotipVisible,
    disableHoverListener: true,
    onClose: closeInfotip,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_missing_variable_alert__WEBPACK_IMPORTED_MODULE_4__.MissingVariableAlert, {
      onClose: closeInfotip,
      onClear: clearValue
    }),
    slotProps: {
      popper: {
        modifiers: [{
          name: 'offset',
          options: {
            offset: [0, 24]
          }
        }]
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_tags_warning_variable_tag__WEBPACK_IMPORTED_MODULE_5__.WarningVariableTag, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Missing variable', 'elementor'),
    onClick: toggleInfotip
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variable-creation.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variable-creation.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableCreation: function() { return /* binding */ VariableCreation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _hooks_use_initial_value__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-initial-value */ "./packages/packages/core/editor-variables/src/hooks/use-initial-value.ts");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../hooks/use-variable-bound-prop */ "./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
/* harmony import */ var _fields_label_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./fields/label-field */ "./packages/packages/core/editor-variables/src/components/fields/label-field.tsx");
/* harmony import */ var _ui_form_field__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./ui/form-field */ "./packages/packages/core/editor-variables/src/components/ui/form-field.tsx");














const SIZE = 'tiny';
const VariableCreation = ({
  onGoBack,
  onClose
}) => {
  const {
    icon: VariableIcon,
    valueField: ValueField,
    propTypeUtil
  } = (0,_context_variable_type_context__WEBPACK_IMPORTED_MODULE_6__.useVariableType)();
  const {
    setVariableValue: setVariable,
    path
  } = (0,_hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_9__.useVariableBoundProp)();
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const initialValue = (0,_hooks_use_initial_value__WEBPACK_IMPORTED_MODULE_7__.useInitialValue)();
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialValue);
  const [label, setLabel] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [errorMessage, setErrorMessage] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [valueFieldError, setValueFieldError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [propTypeKey, setPropTypeKey] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(propTypeUtil.key);
  const {
    labelFieldError,
    setLabelFieldError
  } = (0,_fields_label_field__WEBPACK_IMPORTED_MODULE_11__.useLabelError)();
  const resetFields = () => {
    setValue('');
    setLabel('');
    setErrorMessage('');
    setValueFieldError('');
  };
  const closePopover = () => {
    resetFields();
    onClose();
  };
  const handleCreateAndTrack = () => {
    (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_8__.createVariable)({
      value,
      label,
      type: propTypeKey
    }, {
      eventData: {
        controlPath: path.join('.')
      }
    }).then(key => {
      setVariable(key);
      closePopover();
    }).catch(error => {
      const mappedError = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_10__.mapServerError)(error);
      if (mappedError && 'label' === mappedError.field) {
        setLabel('');
        setLabelFieldError({
          value: label,
          message: mappedError.message
        });
        return;
      }
      setErrorMessage(_utils_validations__WEBPACK_IMPORTED_MODULE_10__.ERROR_MESSAGES.UNEXPECTED_ERROR);
    });
  };
  const hasEmptyFields = () => {
    if ('' === label.trim()) {
      return true;
    }
    if ('string' === typeof value) {
      return '' === value.trim();
    }
    return false === Boolean(value);
  };
  const hasErrors = () => {
    return !!errorMessage;
  };
  const isSubmitDisabled = hasEmptyFields() || hasErrors();
  const handleKeyDown = event => {
    if (event.key === 'Enter' && !isSubmitDisabled) {
      event.preventDefault();
      handleCreateAndTrack();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SectionPopoverBody, {
    height: "auto"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, onGoBack && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, {
      size: SIZE,
      "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Go Back', 'elementor'),
      onClick: onGoBack
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ArrowLeftIcon, {
      fontSize: SIZE
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: SIZE
    })),
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Create variable', 'elementor'),
    onClose: closePopover
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverContent, {
    p: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_form_field__WEBPACK_IMPORTED_MODULE_12__.FormField, {
    id: "variable-label",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Name', 'elementor'),
    errorMsg: labelFieldError?.message,
    noticeMsg: (0,_utils_validations__WEBPACK_IMPORTED_MODULE_10__.labelHint)(label)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_fields_label_field__WEBPACK_IMPORTED_MODULE_11__.LabelField, {
    id: "variable-label",
    value: label,
    error: labelFieldError,
    onChange: newValue => {
      setLabel(newValue);
      setErrorMessage('');
    },
    onErrorChange: errorMsg => {
      setLabelFieldError({
        value: '',
        message: errorMsg
      });
    },
    onKeyDown: handleKeyDown,
    focusOnShow: true
  })), ValueField && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_form_field__WEBPACK_IMPORTED_MODULE_12__.FormField, {
    errorMsg: valueFieldError,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Value', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
    variant: "h5",
    id: "variable-value-wrapper"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ValueField, {
    value: value,
    onPropTypeKeyChange: key => setPropTypeKey(key),
    onChange: newValue => {
      setValue(newValue);
      setErrorMessage('');
      setValueFieldError('');
    },
    onValidationChange: setValueFieldError,
    propType: propType,
    onKeyDown: handleKeyDown
  }))), errorMessage && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.FormHelperText, {
    error: true
  }, errorMessage)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.CardActions, {
    sx: {
      pt: 0.5,
      pb: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Button, {
    id: "create-variable-button",
    size: "small",
    variant: "contained",
    disabled: isSubmitDisabled,
    onClick: handleCreateAndTrack
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Create', 'elementor'))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variable-edit.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variable-edit.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableEdit: function() { return /* binding */ VariableEdit; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _hooks_use_permissions__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../hooks/use-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-permissions.ts");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../hooks/use-variable-bound-prop */ "./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts");
/* harmony import */ var _style_variables_repository__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../style-variables-repository */ "./packages/packages/core/editor-variables/src/style-variables-repository.ts");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
/* harmony import */ var _fields_label_field__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./fields/label-field */ "./packages/packages/core/editor-variables/src/components/fields/label-field.tsx");
/* harmony import */ var _ui_delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./ui/delete-confirmation-dialog */ "./packages/packages/core/editor-variables/src/components/ui/delete-confirmation-dialog.tsx");
/* harmony import */ var _ui_edit_confirmation_dialog__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./ui/edit-confirmation-dialog */ "./packages/packages/core/editor-variables/src/components/ui/edit-confirmation-dialog.tsx");
/* harmony import */ var _ui_form_field__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./ui/form-field */ "./packages/packages/core/editor-variables/src/components/ui/form-field.tsx");


















const SIZE = 'tiny';
const DELETE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Delete variable', 'elementor');
const VariableEdit = ({
  onClose,
  onGoBack,
  onSubmit,
  editId
}) => {
  const {
    icon: VariableIcon,
    valueField: ValueField,
    variableType,
    propTypeUtil
  } = (0,_context_variable_type_context__WEBPACK_IMPORTED_MODULE_7__.useVariableType)();
  const {
    setVariableValue: notifyBoundPropChange,
    variableId,
    path
  } = (0,_hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_10__.useVariableBoundProp)();
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const [isMessageSuppressed, suppressMessage] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_2__.useSuppressedMessage)(_ui_edit_confirmation_dialog__WEBPACK_IMPORTED_MODULE_15__.EDIT_CONFIRMATION_DIALOG_ID);
  const [deleteConfirmation, setDeleteConfirmation] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [editConfirmation, setEditConfirmation] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [errorMessage, setErrorMessage] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [valueFieldError, setValueFieldError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const {
    labelFieldError,
    setLabelFieldError
  } = (0,_fields_label_field__WEBPACK_IMPORTED_MODULE_13__.useLabelError)();
  const variable = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_9__.useVariable)(editId);
  const [propTypeKey, setPropTypeKey] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(variable?.type ?? propTypeUtil.key);
  if (!variable) {
    throw new Error(`Global ${variableType} variable not found`);
  }
  const userPermissions = (0,_hooks_use_permissions__WEBPACK_IMPORTED_MODULE_8__.usePermissions)();
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => variable.value);
  const [label, setLabel] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => variable.label);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    _style_variables_repository__WEBPACK_IMPORTED_MODULE_11__.styleVariablesRepository.update({
      [editId]: {
        ...variable,
        value
      }
    });
    return () => {
      _style_variables_repository__WEBPACK_IMPORTED_MODULE_11__.styleVariablesRepository.update({
        [editId]: {
          ...variable
        }
      });
    };
  }, [editId, value, variable]);
  const handleUpdate = () => {
    if (isMessageSuppressed) {
      handleSaveVariable();
    } else {
      setEditConfirmation(true);
    }
  };
  const handleSaveVariable = () => {
    const typeChanged = propTypeKey !== variable.type;
    const updatePayload = typeChanged ? {
      value,
      label,
      type: propTypeKey
    } : {
      value,
      label
    };
    (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_9__.updateVariable)(editId, updatePayload, {
      eventData: {
        controlPath: path.join('.')
      }
    }).then(() => {
      maybeTriggerBoundPropChange();
      onSubmit?.();
    }).catch(error => {
      const mappedError = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_12__.mapServerError)(error);
      if (mappedError && 'label' === mappedError.field) {
        setLabel('');
        setLabelFieldError({
          value: label,
          message: mappedError.message
        });
        return;
      }
      setErrorMessage(_utils_validations__WEBPACK_IMPORTED_MODULE_12__.ERROR_MESSAGES.UNEXPECTED_ERROR);
    });
  };
  const handleDelete = () => {
    (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_9__.deleteVariable)(editId).then(() => {
      maybeTriggerBoundPropChange();
      onSubmit?.();
    });
  };
  const maybeTriggerBoundPropChange = () => {
    if (editId === variableId) {
      notifyBoundPropChange(editId);
    }
  };
  const handleDeleteConfirmation = () => {
    setDeleteConfirmation(true);
  };
  const closeDeleteDialog = () => () => {
    setDeleteConfirmation(false);
  };
  const closeEditDialog = () => () => {
    setEditConfirmation(false);
  };
  const actions = [];
  if (userPermissions.canDelete()) {
    actions.push(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Tooltip, {
      key: "delete",
      placement: "top",
      title: DELETE_LABEL
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.IconButton, {
      size: SIZE,
      onClick: handleDeleteConfirmation,
      "aria-label": DELETE_LABEL
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.TrashIcon, {
      fontSize: SIZE
    }))));
  }
  const hasEmptyFields = () => {
    if ('' === label.trim()) {
      return true;
    }
    if ('string' === typeof value) {
      return '' === value.trim();
    }
    return false === Boolean(value);
  };
  const noValueChanged = () => {
    return value === variable.value && label === variable.label;
  };
  const hasErrors = () => {
    return !!errorMessage;
  };
  const isSubmitDisabled = noValueChanged() || hasEmptyFields() || hasErrors();
  const handleKeyDown = event => {
    if (event.key === 'Enter' && !isSubmitDisabled) {
      event.preventDefault();
      handleUpdate();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SectionPopoverBody, {
    height: "auto"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.PopoverHeader, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Edit variable', 'elementor'),
    onClose: onClose,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, onGoBack && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.IconButton, {
      size: SIZE,
      "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Go Back', 'elementor'),
      onClick: onGoBack
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.ArrowLeftIcon, {
      fontSize: SIZE
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: SIZE
    })),
    actions: actions
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverContent, {
    p: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_form_field__WEBPACK_IMPORTED_MODULE_16__.FormField, {
    id: "variable-label",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Name', 'elementor'),
    errorMsg: labelFieldError?.message,
    noticeMsg: (0,_utils_validations__WEBPACK_IMPORTED_MODULE_12__.labelHint)(label)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_fields_label_field__WEBPACK_IMPORTED_MODULE_13__.LabelField, {
    id: "variable-label",
    value: label,
    error: labelFieldError,
    onChange: newValue => {
      setLabel(newValue);
      setErrorMessage('');
    },
    onErrorChange: errorMsg => {
      setLabelFieldError({
        value: '',
        message: errorMsg
      });
    },
    onKeyDown: handleKeyDown,
    focusOnShow: true
  })), ValueField && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_form_field__WEBPACK_IMPORTED_MODULE_16__.FormField, {
    errorMsg: valueFieldError,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Value', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
    variant: "h5"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ValueField, {
    propTypeKey: variable.type,
    onPropTypeKeyChange: key => setPropTypeKey(key),
    value: value,
    onChange: newValue => {
      setValue(newValue);
      setErrorMessage('');
      setValueFieldError('');
    },
    onKeyDown: handleKeyDown,
    onValidationChange: setValueFieldError,
    propType: propType
  }))), errorMessage && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.FormHelperText, {
    error: true
  }, errorMessage)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.CardActions, {
    sx: {
      pt: 0.5,
      pb: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
    size: "small",
    variant: "contained",
    disabled: isSubmitDisabled,
    onClick: handleUpdate
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Save', 'elementor')))), deleteConfirmation && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_14__.DeleteConfirmationDialog, {
    open: true,
    label: label,
    onConfirm: handleDelete,
    closeDialog: closeDeleteDialog()
  }), editConfirmation && !isMessageSuppressed && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_edit_confirmation_dialog__WEBPACK_IMPORTED_MODULE_15__.EditConfirmationDialog, {
    closeDialog: closeEditDialog(),
    onConfirm: handleSaveVariable,
    onSuppressMessage: suppressMessage
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variable-restore.tsx":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variable-restore.tsx ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableRestore: function() { return /* binding */ VariableRestore; }
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
/* harmony import */ var _context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../context/variable-selection-popover.context */ "./packages/packages/core/editor-variables/src/context/variable-selection-popover.context.tsx");
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../hooks/use-variable-bound-prop */ "./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
/* harmony import */ var _fields_label_field__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./fields/label-field */ "./packages/packages/core/editor-variables/src/components/fields/label-field.tsx");
/* harmony import */ var _ui_form_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./ui/form-field */ "./packages/packages/core/editor-variables/src/components/ui/form-field.tsx");













const SIZE = 'tiny';
const VariableRestore = ({
  variableId,
  onClose,
  onSubmit
}) => {
  const {
    icon: VariableIcon,
    valueField: ValueField,
    variableType,
    propTypeUtil
  } = (0,_context_variable_type_context__WEBPACK_IMPORTED_MODULE_6__.useVariableType)();
  const {
    setVariableValue: notifyBoundPropChange
  } = (0,_hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_8__.useVariableBoundProp)();
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const variable = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__.useVariable)(variableId);
  if (!variable) {
    throw new Error(`Global ${variableType} variable not found`);
  }
  const [errorMessage, setErrorMessage] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [valueFieldError, setValueFieldError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [label, setLabel] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(variable.label);
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(variable.value);
  const [propTypeKey, setPropTypeKey] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(variable?.type ?? propTypeUtil.key);
  const {
    labelFieldError,
    setLabelFieldError
  } = (0,_fields_label_field__WEBPACK_IMPORTED_MODULE_10__.useLabelError)({
    value: variable.label,
    message: _utils_validations__WEBPACK_IMPORTED_MODULE_9__.ERROR_MESSAGES.DUPLICATED_LABEL
  });
  const handleRestore = () => {
    const typeChanged = propTypeKey !== variable.type;
    const restorePromise = typeChanged ? (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__.restoreVariable)(variableId, label, value, propTypeKey) : (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__.restoreVariable)(variableId, label, value);
    restorePromise.then(() => {
      notifyBoundPropChange(variableId);
      onSubmit?.();
    }).catch(error => {
      const mappedError = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_9__.mapServerError)(error);
      if (mappedError && 'label' === mappedError.field) {
        setLabel('');
        setLabelFieldError({
          value: label,
          message: mappedError.message
        });
        return;
      }
      setErrorMessage(_utils_validations__WEBPACK_IMPORTED_MODULE_9__.ERROR_MESSAGES.UNEXPECTED_ERROR);
    });
  };
  const hasEmptyFields = () => {
    if ('' === label.trim()) {
      return true;
    }
    if ('string' === typeof value) {
      return '' === value.trim();
    }
    return false === Boolean(value);
  };
  const noValueChanged = () => {
    return value === variable.value && label === variable.label;
  };
  const hasErrors = () => {
    return !!errorMessage;
  };
  const isSubmitDisabled = noValueChanged() || hasEmptyFields() || hasErrors();
  const handleKeyDown = event => {
    if (event.key === 'Enter' && !isSubmitDisabled) {
      event.preventDefault();
      handleRestore();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_5__.PopoverContentRefContextProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SectionPopoverBody, {
    height: "auto"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: SIZE
    }),
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Restore variable', 'elementor'),
    onClose: onClose
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverContent, {
    p: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_form_field__WEBPACK_IMPORTED_MODULE_11__.FormField, {
    id: "variable-label",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Name', 'elementor'),
    errorMsg: labelFieldError?.message,
    noticeMsg: (0,_utils_validations__WEBPACK_IMPORTED_MODULE_9__.labelHint)(label)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_fields_label_field__WEBPACK_IMPORTED_MODULE_10__.LabelField, {
    id: "variable-label",
    value: label,
    error: labelFieldError,
    onChange: newValue => {
      setLabel(newValue);
      setErrorMessage('');
    },
    onErrorChange: errorMsg => {
      setLabelFieldError({
        value: '',
        message: errorMsg
      });
    },
    onKeyDown: handleKeyDown,
    focusOnShow: true
  })), ValueField && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_form_field__WEBPACK_IMPORTED_MODULE_11__.FormField, {
    errorMsg: valueFieldError,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Value', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
    variant: "h5"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ValueField, {
    propTypeKey: propTypeKey,
    onPropTypeKeyChange: key => setPropTypeKey(key),
    value: value,
    onChange: newValue => {
      setValue(newValue);
      setErrorMessage('');
      setValueFieldError('');
    },
    onValidationChange: setValueFieldError,
    propType: propType,
    onKeyDown: handleKeyDown
  }))), errorMessage && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.FormHelperText, {
    error: true
  }, errorMessage)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.CardActions, {
    sx: {
      pt: 0.5,
      pb: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    size: "small",
    variant: "contained",
    disabled: isSubmitDisabled,
    onClick: handleRestore
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Restore', 'elementor')))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variable-selection-popover.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variable-selection-popover.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableSelectionPopover: function() { return /* binding */ VariableSelectionPopover; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../context/variable-selection-popover.context */ "./packages/packages/core/editor-variables/src/context/variable-selection-popover.context.tsx");
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _hooks_use_permissions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-permissions.ts");
/* harmony import */ var _hooks_use_quota_permissions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-quota-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-quota-permissions.ts");
/* harmony import */ var _variable_creation__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./variable-creation */ "./packages/packages/core/editor-variables/src/components/variable-creation.tsx");
/* harmony import */ var _variable_edit__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./variable-edit */ "./packages/packages/core/editor-variables/src/components/variable-edit.tsx");
/* harmony import */ var _variables_selection__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./variables-selection */ "./packages/packages/core/editor-variables/src/components/variables-selection.tsx");










const VIEW_LIST = 'list';
const VIEW_ADD = 'add';
const VIEW_EDIT = 'edit';
const VariableSelectionPopover = ({
  closePopover,
  propTypeKey,
  selectedVariable
}) => {
  const [currentView, setCurrentView] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(VIEW_LIST);
  const [editId, setEditId] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const onSettingsAvailable = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.isExperimentActive)('e_variables_manager') ? () => {
    window.dispatchEvent(new CustomEvent('elementor/toggle-design-system', {
      detail: {
        tab: 'variables'
      }
    }));
  } : undefined;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_context_variable_type_context__WEBPACK_IMPORTED_MODULE_3__.VariableTypeProvider, {
    propTypeKey: propTypeKey
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_context_variable_selection_popover_context__WEBPACK_IMPORTED_MODULE_2__.PopoverContentRefContextProvider, null, RenderView({
    propTypeKey,
    currentView,
    selectedVariable,
    editId,
    setEditId,
    setCurrentView,
    closePopover,
    onSettings: onSettingsAvailable
  })));
};
function RenderView(props) {
  const userPermissions = (0,_hooks_use_permissions__WEBPACK_IMPORTED_MODULE_4__.usePermissions)();
  const userQuotaPermissions = (0,_hooks_use_quota_permissions__WEBPACK_IMPORTED_MODULE_5__.useQuotaPermissions)(props.propTypeKey);
  const handlers = {
    onClose: () => {
      props.closePopover();
    },
    onGoBack: () => {
      props.setCurrentView(VIEW_LIST);
    }
  };
  if (userPermissions.canAdd()) {
    handlers.onAdd = () => {
      props.setCurrentView(VIEW_ADD);
    };
  }
  if (userPermissions.canEdit()) {
    handlers.onEdit = key => {
      props.setEditId(key);
      props.setCurrentView(VIEW_EDIT);
    };
  }
  if (userPermissions.canManageSettings() && props.onSettings) {
    handlers.onSettings = () => {
      props.closePopover();
      props.onSettings?.();
    };
  }
  const handleSubmitOnEdit = () => {
    if (props?.selectedVariable?.key === props.editId) {
      handlers.onClose();
    } else {
      handlers.onGoBack?.();
    }
  };
  if (VIEW_LIST === props.currentView) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variables_selection__WEBPACK_IMPORTED_MODULE_8__.VariablesSelection, {
      closePopover: handlers.onClose,
      onAdd: handlers.onAdd,
      onEdit: handlers.onEdit,
      onSettings: handlers.onSettings,
      disabled: !userQuotaPermissions.canAdd()
    });
  }
  if (VIEW_ADD === props.currentView) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_creation__WEBPACK_IMPORTED_MODULE_6__.VariableCreation, {
      onGoBack: handlers.onGoBack,
      onClose: handlers.onClose
    });
  }
  if (VIEW_EDIT === props.currentView) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_edit__WEBPACK_IMPORTED_MODULE_7__.VariableEdit, {
      editId: props.editId,
      onGoBack: handlers.onGoBack,
      onClose: handlers.onClose,
      onSubmit: handleSubmitOnEdit
    });
  }
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-auto-edit.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-auto-edit.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useAutoEdit: function() { return /* binding */ useAutoEdit; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const useAutoEdit = () => {
  const [autoEditVariableId, setAutoEditVariableId] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(undefined);
  const startAutoEdit = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(variableId => {
    setAutoEditVariableId(variableId);
  }, []);
  const handleAutoEditComplete = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setTimeout(() => {
      setAutoEditVariableId(undefined);
    }, 100);
  }, []);
  return {
    autoEditVariableId,
    startAutoEdit,
    handleAutoEditComplete
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-error-navigation.ts":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-error-navigation.ts ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useErrorNavigation: function() { return /* binding */ useErrorNavigation; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const useErrorNavigation = () => {
  const currentIndexRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(0);
  const createNavigationCallback = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((ids, onNavigate, onComplete) => {
    return () => {
      if (!ids?.length) {
        return;
      }
      const currentIndex = currentIndexRef.current;
      const currentId = ids[currentIndex];
      if (currentId) {
        onNavigate(currentId);
        const nextIndex = currentIndex + 1;
        if (nextIndex >= ids.length) {
          onComplete();
          currentIndexRef.current = 0;
        } else {
          currentIndexRef.current = nextIndex;
        }
      }
    };
  }, []);
  const resetNavigation = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    currentIndexRef.current = 0;
  }, []);
  return {
    createNavigationCallback,
    resetNavigation
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-variables-manager-state.ts":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-variables-manager-state.ts ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useVariablesManagerState: function() { return /* binding */ useVariablesManagerState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _batch_operations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../batch-operations */ "./packages/packages/core/editor-variables/src/batch-operations.ts");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _storage__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../storage */ "./packages/packages/core/editor-variables/src/storage.ts");
/* harmony import */ var _utils_duplicate_label__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../utils/duplicate-label */ "./packages/packages/core/editor-variables/src/utils/duplicate-label.ts");
/* harmony import */ var _utils_filter_by_search__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../utils/filter-by-search */ "./packages/packages/core/editor-variables/src/utils/filter-by-search.ts");
/* harmony import */ var _utils_variables_to_list__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../utils/variables-to-list */ "./packages/packages/core/editor-variables/src/utils/variables-to-list.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");









const useVariablesManagerState = () => {
  const [variables, setVariables] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_2__.getVariables)(false));
  const [deletedVariables, setDeletedVariables] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [isSaveDisabled, setIsSaveDisabled] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isDirty, setIsDirty] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isSaving, setIsSaving] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [searchValue, setSearchValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleStorageUpdated = () => {
      setVariables((0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_2__.getVariables)(false));
      setDeletedVariables([]);
      setIsDirty(false);
    };
    window.addEventListener(_storage__WEBPACK_IMPORTED_MODULE_4__.STORAGE_UPDATED_EVENT, handleStorageUpdated);
    return () => {
      window.removeEventListener(_storage__WEBPACK_IMPORTED_MODULE_4__.STORAGE_UPDATED_EVENT, handleStorageUpdated);
    };
  }, []);
  const handleOnChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newVariables => {
    const hasChanges = Object.entries(newVariables).some(([id, newVar]) => {
      const existingVar = variables[id];
      if (!existingVar) {
        return true;
      }
      return existingVar.label !== newVar.label || existingVar.value !== newVar.value || existingVar.order !== newVar.order || existingVar.type !== newVar.type || (existingVar.sync_to_v3 ?? false) !== (newVar.sync_to_v3 ?? false);
    });
    if (hasChanges) {
      setVariables({
        ...variables,
        ...newVariables
      });
      setIsDirty(true);
    }
  }, [variables]);
  const createVariable = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((type, defaultName, defaultValue) => {
    const newId = (0,_batch_operations__WEBPACK_IMPORTED_MODULE_1__.generateTempId)();
    const newVariable = {
      id: newId,
      label: defaultName.trim(),
      value: defaultValue.trim(),
      type
    };
    setVariables(prev => ({
      ...prev,
      [newId]: newVariable
    }));
    setIsDirty(true);
    return newId;
  }, []);
  const duplicateVariable = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(sourceId => {
    const newId = (0,_batch_operations__WEBPACK_IMPORTED_MODULE_1__.generateTempId)();
    setVariables(prev => {
      const source = prev[sourceId];
      if (!source || source.deleted) {
        return prev;
      }
      const existingLabels = Object.values(prev).filter(v => !v.deleted).map(v => v.label);
      return {
        ...prev,
        [newId]: {
          label: (0,_utils_duplicate_label__WEBPACK_IMPORTED_MODULE_5__.generateDuplicateLabel)(source.label, existingLabels),
          value: source.value,
          type: source.type
        }
      };
    });
    setIsDirty(true);
    return newId;
  }, []);
  const handleDeleteVariable = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    setDeletedVariables(prev => [...prev, itemId]);
    setVariables(prev => ({
      ...prev,
      [itemId]: {
        ...prev[itemId],
        deleted: true
      }
    }));
    setIsDirty(true);
  }, []);
  const handleStartSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    setVariables(prev => ({
      ...prev,
      [itemId]: {
        ...prev[itemId],
        sync_to_v3: true
      }
    }));
    setIsDirty(true);
  }, []);
  const handleStopSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    setVariables(prev => ({
      ...prev,
      [itemId]: {
        ...prev[itemId],
        sync_to_v3: false
      }
    }));
    setIsDirty(true);
  }, []);
  const handleSearch = searchTerm => {
    setSearchValue(searchTerm);
  };
  const handleSave = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    const originalVariables = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_2__.getVariables)(false);
    setIsSaving(true);
    const result = await _service__WEBPACK_IMPORTED_MODULE_3__.service.batchSave(originalVariables, variables, deletedVariables);
    if (result.success) {
      await _service__WEBPACK_IMPORTED_MODULE_3__.service.load();
      const updatedVariables = _service__WEBPACK_IMPORTED_MODULE_3__.service.variables();
      setVariables(updatedVariables);
      setDeletedVariables([]);
      setIsDirty(false);
    }
    return {
      success: result.success
    };
  }, [variables, deletedVariables]);
  const filteredVariables = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    const list = (0,_utils_variables_to_list__WEBPACK_IMPORTED_MODULE_7__.variablesToList)(variables).filter(v => !v.deleted);
    const typeFiltered = (0,_utils_variables_to_list__WEBPACK_IMPORTED_MODULE_7__.applySelectionFilters)(list, (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_8__.getVariableTypes)());
    const searchFiltered = (0,_utils_filter_by_search__WEBPACK_IMPORTED_MODULE_6__.filterBySearch)(typeFiltered, searchValue);
    return Object.fromEntries(searchFiltered.map(({
      key,
      ...rest
    }) => [key, rest]));
  }, [variables, searchValue]);
  return {
    variables: filteredVariables(),
    deletedVariables,
    isDirty,
    isSaveDisabled,
    handleOnChange,
    createVariable,
    duplicateVariable,
    handleDeleteVariable,
    handleStartSync,
    handleStopSync,
    handleSave,
    isSaving,
    handleSearch,
    searchValue,
    setIsSaving,
    setIsSaveDisabled
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-edit-menu.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-edit-menu.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableEditMenu: function() { return /* binding */ VariableEditMenu; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const VariableEditMenu = ({
  menuActions,
  disabled,
  itemId
}) => {
  const menuState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.usePopupState)({
    variant: 'popover'
  });
  const triggerProps = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindTrigger)(menuState);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, _extends({}, triggerProps, {
    disabled: disabled,
    size: "tiny",
    onClick: e => {
      e.stopPropagation();
      triggerProps.onClick?.(e);
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.DotsVerticalIcon, {
    fontSize: "tiny"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Menu, _extends({
    disablePortal: true,
    MenuListProps: {
      dense: true
    },
    PaperProps: {
      elevation: 6
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindMenu)(menuState), {
    anchorEl: menuState.anchorEl,
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'right'
    },
    open: menuState.isOpen,
    onClose: menuState.close
  }), menuActions.map(action => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuItem, {
    key: action.name,
    onClick: e => {
      e.stopPropagation();
      action.onClick?.(itemId);
      menuState.close();
    },
    sx: {
      color: action.color,
      gap: 1
    }
  }, action.icon && /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(action.icon, {
    fontSize: 'inherit'
  }), ' ', action.name))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-cell.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-cell.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableTableCell: function() { return /* binding */ VariableTableCell; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const VariableTableCell = ({
  children,
  isHeader,
  width,
  maxWidth,
  align,
  noPadding,
  sx
}) => {
  const baseSx = {
    maxWidth: maxWidth ?? 150,
    cursor: 'initial',
    typography: 'caption',
    ...(isHeader && {
      color: 'text.primary',
      fontWeight: 'bold'
    }),
    ...(isHeader && !noPadding && {
      padding: '10px 16px'
    }),
    ...(width && {
      width
    }),
    ...sx
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TableCell, {
    size: "small",
    padding: noPadding ? 'none' : undefined,
    align: align,
    sx: baseSx
  }, children);
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-row.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-row.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableRow: function() { return /* binding */ VariableRow; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_quota_permissions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../hooks/use-quota-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-quota-permissions.ts");
/* harmony import */ var _fields_label_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../fields/label-field */ "./packages/packages/core/editor-variables/src/components/fields/label-field.tsx");
/* harmony import */ var _ui_variable_promotion_chip__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../ui/variable-promotion-chip */ "./packages/packages/core/editor-variables/src/components/ui/variable-promotion-chip.tsx");
/* harmony import */ var _variable_editable_cell__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../variable-editable-cell */ "./packages/packages/core/editor-variables/src/components/variables-manager/variable-editable-cell.tsx");
/* harmony import */ var _variable_edit_menu__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./variable-edit-menu */ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-edit-menu.tsx");
/* harmony import */ var _variable_table_cell__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./variable-table-cell */ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-cell.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }











const TRACKING_DATA = {
  target_name: 'variables_manager',
  target_location: 'variables_manager'
};
const VariableRow = props => {
  const {
    row,
    variables,
    handleOnChange,
    autoEditVariableId,
    onAutoEditComplete,
    onFieldError,
    menuActions,
    handleRowRef,
    itemProps,
    showDropIndication,
    triggerProps,
    itemStyle,
    triggerStyle,
    isDragged,
    dropPosition,
    setTriggerRef,
    isSorting
  } = props;
  const promotionRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const isDisabled = !(0,_hooks_use_quota_permissions__WEBPACK_IMPORTED_MODULE_4__.useQuotaPermissions)(row.type).canEdit();
  const showIndicationBefore = showDropIndication && dropPosition === 'before';
  const showIndicationAfter = showDropIndication && dropPosition === 'after';
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.TableRow, _extends({}, itemProps, {
    ref: itemProps.ref,
    selected: isDragged,
    sx: {
      ...(isDisabled && {
        '& td, & th': {
          color: 'text.disabled'
        }
      }),
      ...(showIndicationBefore && {
        '& td, & th': {
          borderTop: '2px solid',
          borderTopColor: 'primary.main'
        }
      }),
      ...(showIndicationAfter && {
        '& td, & th': {
          borderBottom: '2px solid',
          borderBottomColor: 'primary.main'
        }
      }),
      '&:hover, &:focus-within': {
        backgroundColor: 'action.hover',
        '& [role="toolbar"], & [draggable]': {
          opacity: 1
        }
      },
      '& [role="toolbar"], & [draggable]': {
        opacity: 0
      }
    },
    style: {
      ...itemStyle,
      ...triggerStyle
    },
    onClick: () => {
      if (isDisabled) {
        promotionRef.current?.toggle();
      }
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_table_cell__WEBPACK_IMPORTED_MODULE_9__.VariableTableCell, {
    noPadding: true,
    width: 10,
    maxWidth: 10
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.IconButton, _extends({
    size: "small",
    ref: setTriggerRef
  }, triggerProps, {
    disabled: isSorting,
    draggable: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.GripVerticalIcon, {
    fontSize: "inherit"
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_table_cell__WEBPACK_IMPORTED_MODULE_9__.VariableTableCell, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_editable_cell__WEBPACK_IMPORTED_MODULE_7__.VariableEditableCell, {
    initialValue: row.name,
    onChange: value => {
      if (value !== row.name && !isDisabled) {
        handleOnChange({
          ...variables,
          [row.id]: {
            ...variables[row.id],
            label: value
          }
        });
      }
    },
    prefixElement: /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(row.icon, {
      fontSize: 'inherit',
      color: isDisabled ? 'disabled' : 'inherit'
    }),
    editableElement: ({
      value,
      onChange,
      onValidationChange,
      error
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_fields_label_field__WEBPACK_IMPORTED_MODULE_5__.LabelField, {
      id: 'variable-label-' + row.id,
      size: "tiny",
      value: value,
      onChange: onChange,
      onErrorChange: errorMsg => {
        onValidationChange?.(errorMsg);
        onFieldError?.(!!errorMsg);
      },
      error: error,
      focusOnShow: true,
      selectOnShow: autoEditVariableId === row.id,
      showWarningInfotip: true,
      variables: variables
    }),
    autoEdit: autoEditVariableId === row.id && !isDisabled,
    onRowRef: handleRowRef(row.id),
    onAutoEditComplete: autoEditVariableId === row.id ? onAutoEditComplete : undefined,
    fieldType: "label",
    disabled: isDisabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: row.name,
    sx: {
      border: '4px solid transparent'
    }
  }, row.name))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_table_cell__WEBPACK_IMPORTED_MODULE_9__.VariableTableCell, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_editable_cell__WEBPACK_IMPORTED_MODULE_7__.VariableEditableCell, {
    initialValue: row.value,
    onChange: value => {
      if (value !== row.value && !isDisabled) {
        handleOnChange({
          ...variables,
          [row.id]: {
            ...variables[row.id],
            value
          }
        });
      }
    },
    editableElement: ({
      value,
      onChange,
      onValidationChange,
      error
    }) => row.valueField?.({
      value,
      onChange,
      onPropTypeKeyChange: type => {
        if (!isDisabled && type !== row.type) {
          handleOnChange({
            ...variables,
            [row.id]: {
              ...variables[row.id],
              type
            }
          });
        }
      },
      propTypeKey: row.type,
      onValidationChange: errorMsg => {
        onValidationChange?.(errorMsg);
        onFieldError?.(!!errorMsg);
      },
      error
    }) ?? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null),
    onRowRef: handleRowRef(row.id),
    gap: 0.25,
    fieldType: "value",
    disabled: isDisabled
  }, row.startIcon && row.startIcon({
    value: row.value
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.EllipsisWithTooltip, {
    title: row.value,
    sx: {
      border: '4px solid transparent',
      lineHeight: '1',
      pt: 0.25
    }
  }, row.value))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_table_cell__WEBPACK_IMPORTED_MODULE_9__.VariableTableCell, {
    align: "right",
    noPadding: true,
    width: 16,
    maxWidth: 16,
    sx: {
      paddingInlineEnd: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    role: "toolbar",
    direction: "row",
    justifyContent: "flex-end",
    alignItems: "center"
  }, isDisabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_promotion_chip__WEBPACK_IMPORTED_MODULE_6__.VariablePromotionChip, {
    variableType: row.variableType,
    upgradeUrl: `https://go.elementor.com/renew-license-manager-${row.variableType}-variable`,
    ref: promotionRef,
    trackingData: TRACKING_DATA
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variable_edit_menu__WEBPACK_IMPORTED_MODULE_8__.VariableEditMenu, {
    menuActions: menuActions(row.id),
    disabled: isSorting,
    itemId: row.id
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/variable-editable-cell.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/variable-editable-cell.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableEditableCell: function() { return /* binding */ VariableEditableCell; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _fields_label_field__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../fields/label-field */ "./packages/packages/core/editor-variables/src/components/fields/label-field.tsx");




const VariableEditableCell = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.memo(({
  initialValue,
  children,
  editableElement,
  onChange,
  prefixElement,
  autoEdit = false,
  onRowRef,
  onAutoEditComplete,
  gap = 1,
  fieldType,
  disabled = false
}) => {
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialValue);
  const [isEditing, setIsEditing] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const {
    labelFieldError,
    setLabelFieldError
  } = (0,_fields_label_field__WEBPACK_IMPORTED_MODULE_2__.useLabelError)();
  const [valueFieldError, setValueFieldError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const handleSave = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    const hasError = fieldType === 'label' && labelFieldError?.message || fieldType === 'value' && valueFieldError;
    if (!hasError) {
      onChange(value);
    }
    setIsEditing(false);
  }, [value, onChange, fieldType, labelFieldError, valueFieldError]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    onRowRef?.(rowRef?.current);
  }, [onRowRef]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (autoEdit && !isEditing && !disabled) {
      setIsEditing(true);
      onAutoEditComplete?.();
    }
  }, [autoEdit, isEditing, onAutoEditComplete, disabled]);
  const handleDoubleClick = () => {
    if (disabled) {
      return;
    }
    setIsEditing(true);
  };
  const handleKeyDown = event => {
    if (disabled) {
      return;
    }
    if (event.key === 'Enter') {
      handleSave();
    } else if (event.key === 'Escape') {
      setIsEditing(false);
    }
    if (event.key === ' ' && !isEditing) {
      event.preventDefault();
      setIsEditing(true);
    }
  };
  const handleChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newValue => {
    setValue(newValue);
  }, []);
  const handleValidationChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(errorMsg => {
    if (fieldType === 'label') {
      setLabelFieldError({
        value,
        message: errorMsg
      });
    } else {
      setValueFieldError(errorMsg);
    }
  }, [fieldType, value, setLabelFieldError, setValueFieldError]);
  let currentError;
  if (fieldType === 'label') {
    currentError = labelFieldError;
  } else if (fieldType === 'value') {
    currentError = {
      value,
      message: valueFieldError
    };
  }
  const editableContent = editableElement({
    value,
    onChange: handleChange,
    onValidationChange: handleValidationChange,
    error: currentError
  });
  if (isEditing) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ClickAwayListener, {
      onClickAway: handleSave
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
      ref: rowRef,
      direction: "row",
      alignItems: "center",
      gap: gap,
      onDoubleClick: handleDoubleClick,
      onKeyDown: handleKeyDown,
      tabIndex: 0,
      role: "button",
      "aria-label": "Double click or press Space to edit"
    }, prefixElement, editableContent));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    ref: rowRef,
    direction: "row",
    alignItems: "center",
    gap: gap,
    onDoubleClick: handleDoubleClick,
    onKeyDown: handleKeyDown,
    tabIndex: disabled ? -1 : 0,
    role: "button",
    "aria-label": disabled ? '' : 'Double click or press Space to edit'
  }, prefixElement, children);
});

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-create-menu.tsx":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-create-menu.tsx ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SIZE: function() { return /* binding */ SIZE; },
/* harmony export */   VariableManagerCreateMenu: function() { return /* binding */ VariableManagerCreateMenu; },
/* harmony export */   getDefaultName: function() { return /* binding */ getDefaultName; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _hooks_use_quota_permissions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../hooks/use-quota-permissions */ "./packages/packages/core/editor-variables/src/hooks/use-quota-permissions.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _ui_variable_promotion_chip__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../ui/variable-promotion-chip */ "./packages/packages/core/editor-variables/src/components/ui/variable-promotion-chip.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }










const TRACKING_DATA = {
  target_name: 'variables_manager',
  target_location: 'variables_manager',
  location_l1: 'create variable menu'
};
const SIZE = 'tiny';
const VariableManagerCreateMenu = ({
  variables,
  onCreate,
  menuState,
  outlinedTrigger = false
}) => {
  const buttonRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const variableTypes = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_7__.getVariableTypes)();
  const menuOptionConfigs = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => Object.entries(variableTypes).filter(([, variable]) => !!variable.defaultValue).map(([key, variable]) => ({
    key,
    propTypeKey: variable.propTypeUtil.key,
    variableType: variable.variableType,
    defaultValue: variable.defaultValue || '',
    icon: variable.icon
  })), [variableTypes]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindTrigger)(menuState), {
    ref: buttonRef,
    size: SIZE,
    variant: outlinedTrigger ? 'outlined' : undefined,
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Add variable', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PlusIcon, {
    fontSize: SIZE
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Menu, _extends({
    disablePortal: true,
    MenuListProps: {
      dense: true
    },
    PaperProps: {
      elevation: 6
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindMenu)(menuState), {
    anchorEl: buttonRef.current,
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'right'
    },
    "data-testid": "variable-manager-create-menu"
  }), menuOptionConfigs.map(config => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(MenuOption, {
    key: config.key,
    config: config,
    variables: variables,
    onCreate: onCreate,
    onClose: menuState.close
  }))));
};
const MenuOption = ({
  config,
  variables,
  onCreate,
  onClose
}) => {
  const promotionRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const userQuotaPermissions = (0,_hooks_use_quota_permissions__WEBPACK_IMPORTED_MODULE_5__.useQuotaPermissions)(config.propTypeKey);
  const displayName = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_3__.capitalize)(config.variableType);
  const isDisabled = !userQuotaPermissions.canAdd();
  const handleClick = () => {
    if (isDisabled) {
      promotionRef.current?.toggle();
      return;
    }
    const defaultName = getDefaultName(variables, config.variableType);
    onCreate(config.key, defaultName, config.defaultValue);
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_6__.trackVariablesManagerEvent)({
      action: 'add',
      varType: config.variableType
    });
    onClose();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuItem, {
    onClick: handleClick,
    sx: {
      gap: 1.5,
      cursor: 'pointer'
    }
  }, /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(config.icon, {
    fontSize: SIZE,
    color: isDisabled ? 'disabled' : 'action'
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    color: isDisabled ? 'text.disabled' : 'text.primary'
  }, displayName), isDisabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_promotion_chip__WEBPACK_IMPORTED_MODULE_8__.VariablePromotionChip, {
    variableType: config.variableType,
    upgradeUrl: `https://go.elementor.com/go-pro-manager-${config.variableType}-variable/`,
    ref: promotionRef,
    trackingData: TRACKING_DATA
  }));
};
const getDefaultName = (variables, baseName) => {
  const pattern = new RegExp(`^${baseName}-(\\d+)$`, 'i');
  const takenNumbers = new Set();
  Object.values(variables).forEach(variable => {
    const match = variable.label.match(pattern);
    if (match) {
      takenNumbers.add(parseInt(match[1], 10));
    }
  });
  let counter = 1;
  while (takenNumbers.has(counter)) {
    counter++;
  }
  return `${baseName}-${counter}`;
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-panel.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-panel.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariablesManagerPanelEmbedded: function() { return /* binding */ VariablesManagerPanelEmbedded; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _ui_delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../ui/delete-confirmation-dialog */ "./packages/packages/core/editor-variables/src/components/ui/delete-confirmation-dialog.tsx");
/* harmony import */ var _ui_empty_state__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../ui/empty-state */ "./packages/packages/core/editor-variables/src/components/ui/empty-state.tsx");
/* harmony import */ var _ui_no_search_results__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../ui/no-search-results */ "./packages/packages/core/editor-variables/src/components/ui/no-search-results.tsx");
/* harmony import */ var _hooks_use_auto_edit__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./hooks/use-auto-edit */ "./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-auto-edit.ts");
/* harmony import */ var _hooks_use_error_navigation__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./hooks/use-error-navigation */ "./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-error-navigation.ts");
/* harmony import */ var _hooks_use_variables_manager_state__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./hooks/use-variables-manager-state */ "./packages/packages/core/editor-variables/src/components/variables-manager/hooks/use-variables-manager-state.ts");
/* harmony import */ var _variables_manager_create_menu__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./variables-manager-create-menu */ "./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-create-menu.tsx");
/* harmony import */ var _variables_manager_table__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./variables-manager-table */ "./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-table.tsx");



















const STOP_SYNC_MESSAGE_KEY = 'stop-sync-variable';
function VariablesManagerPanelEmbedded({
  onRequestClose,
  onExposeCloseAttempt
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariablesManagerPanelContent, {
    onRequestClose: onRequestClose,
    onExposeCloseAttempt: onExposeCloseAttempt
  });
}
function VariablesManagerPanelContent({
  onRequestClose,
  onExposeCloseAttempt
}) {
  const {
    open: openSaveChangesDialog,
    close: closeSaveChangesDialog,
    isOpen: isSaveChangesDialogOpen
  } = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.useDialog)();
  const [isStopSyncSuppressed] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useSuppressedMessage)(STOP_SYNC_MESSAGE_KEY);
  const createMenuState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.usePopupState)({
    variant: 'popover'
  });
  const {
    variables,
    isDirty,
    searchValue,
    isSaveDisabled,
    handleOnChange,
    createVariable,
    duplicateVariable,
    handleDeleteVariable,
    handleStartSync: startSyncFromState,
    handleStopSync: stopSyncFromState,
    handleSave,
    isSaving,
    handleSearch,
    setIsSaving,
    setIsSaveDisabled
  } = (0,_hooks_use_variables_manager_state__WEBPACK_IMPORTED_MODULE_15__.useVariablesManagerState)();
  const {
    autoEditVariableId,
    startAutoEdit,
    handleAutoEditComplete
  } = (0,_hooks_use_auto_edit__WEBPACK_IMPORTED_MODULE_13__.useAutoEdit)();
  const {
    createNavigationCallback,
    resetNavigation
  } = (0,_hooks_use_error_navigation__WEBPACK_IMPORTED_MODULE_14__.useErrorNavigation)();
  const [deleteConfirmation, setDeleteConfirmation] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [stopSyncConfirmation, setStopSyncConfirmation] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [serverError, setServerError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  usePreventUnload(isDirty);
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
  const handleCreateVariable = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((type, defaultName, defaultValue) => {
    const newId = createVariable(type, defaultName, defaultValue);
    if (newId) {
      startAutoEdit(newId);
    }
  }, [createVariable, startAutoEdit]);
  const handleSaveClick = async () => {
    try {
      setServerError(null);
      resetNavigation();
      const result = await handleSave();
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackVariablesManagerEvent)({
        action: 'saveChanges'
      });
      return result;
    } catch (error) {
      const mappedError = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_8__.mapServerError)(error);
      const duplicatedIds = mappedError?.action?.data?.duplicatedIds;
      if (mappedError && 'label' === mappedError.field) {
        if (duplicatedIds && mappedError.action) {
          mappedError.action.callback = createNavigationCallback(duplicatedIds, startAutoEdit, () => {
            setIsSaveDisabled(false);
          });
        }
        setServerError(mappedError);
        setIsSaveDisabled(true);
        resetNavigation();
      }
      return {
        success: false,
        error: mappedError
      };
    } finally {
      setIsSaving(false);
    }
  };
  const handleDeleteVariableWithConfirmation = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    handleDeleteVariable(itemId);
    setDeleteConfirmation(null);
  }, [handleDeleteVariable]);
  const commitStopSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    stopSyncFromState(itemId);
    const variable = variables[itemId];
    if (variable) {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackVariableSyncToV3)({
        variableLabel: variable.label,
        action: 'unsync'
      });
    }
  }, [stopSyncFromState, variables]);
  const handleStartSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    startSyncFromState(itemId);
    const variable = variables[itemId];
    if (variable) {
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackVariableSyncToV3)({
        variableLabel: variable.label,
        action: 'sync'
      });
    }
  }, [startSyncFromState, variables]);
  const handleStopSync = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(itemId => {
    if (!isStopSyncSuppressed) {
      setStopSyncConfirmation(itemId);
    } else {
      commitStopSync(itemId);
    }
  }, [isStopSyncSuppressed, commitStopSync]);
  const buildMenuActions = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(variableId => {
    const variable = variables[variableId];
    if (!variable) {
      return [];
    }
    const typeActions = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_9__.getMenuActionsForVariable)(variable.type, {
      variable,
      variableId,
      handlers: {
        onStartSync: handleStartSync,
        onStopSync: handleStopSync
      }
    });
    const duplicateAction = {
      name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Duplicate', 'elementor'),
      icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.CopyIcon,
      color: 'text.primary',
      onClick: itemId => {
        const newId = duplicateVariable(itemId);
        startAutoEdit(newId);
        const variableTypeOptions = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_9__.getVariableType)(variable.type);
        (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackVariablesManagerEvent)({
          action: 'duplicate',
          varType: variableTypeOptions?.variableType
        });
      }
    };
    const deleteAction = {
      name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Delete', 'elementor'),
      icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.TrashIcon,
      color: 'error.main',
      onClick: itemId => {
        const v = variables[itemId];
        if (v) {
          setDeleteConfirmation({
            id: itemId,
            label: v.label
          });
          const variableTypeOptions = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_9__.getVariableType)(v.type);
          (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_7__.trackVariablesManagerEvent)({
            action: 'delete',
            varType: variableTypeOptions?.variableType
          });
        }
      }
    };
    return [...typeActions, duplicateAction, deleteAction];
  }, [variables, handleStartSync, handleStopSync, duplicateVariable, startAutoEdit]);
  const hasVariables = Object.keys(variables).length > 0;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "column",
    sx: {
      height: '100%',
      width: '100%',
      flex: 1,
      minHeight: 0,
      overflow: 'hidden'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "row",
    alignItems: "center",
    spacing: 1,
    width: "100%",
    sx: {
      flexShrink: 0,
      px: 2,
      pb: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SearchField, {
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Search', 'elementor'),
    value: searchValue,
    onSearch: handleSearch,
    sx: {
      flex: 1,
      minWidth: 0,
      px: 0,
      py: 0,
      display: 'flex',
      alignItems: 'center',
      alignSelf: 'stretch'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      display: 'flex',
      flexShrink: 0,
      alignItems: 'center'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variables_manager_create_menu__WEBPACK_IMPORTED_MODULE_16__.VariableManagerCreateMenu, {
    outlinedTrigger: true,
    onCreate: handleCreateVariable,
    variables: variables,
    menuState: createMenuState
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Divider, {
    sx: {
      width: '100%'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    sx: {
      display: 'flex',
      flexDirection: 'column',
      flex: 1,
      minHeight: 0
    }
  }, hasVariables && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_variables_manager_table__WEBPACK_IMPORTED_MODULE_17__.VariablesManagerTable, {
    menuActions: buildMenuActions,
    variables: variables,
    onChange: handleOnChange,
    autoEditVariableId: autoEditVariableId,
    onAutoEditComplete: handleAutoEditComplete,
    onFieldError: setIsSaveDisabled
  }), !hasVariables && searchValue && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_no_search_results__WEBPACK_IMPORTED_MODULE_12__.NoSearchResults, {
    searchValue: searchValue,
    onClear: () => handleSearch(''),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.ColorFilterIcon, {
      fontSize: "large"
    })
  }), !hasVariables && !searchValue && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_empty_state__WEBPACK_IMPORTED_MODULE_11__.EmptyState, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Create your first variable', 'elementor'),
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Variables are saved attributes that you can apply anywhere on your site.', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.ColorFilterIcon, {
      fontSize: "large"
    }),
    onAdd: createMenuState.open
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.PanelFooter, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Infotip, {
    placement: "right",
    open: !!serverError,
    content: serverError ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Alert, {
      severity: serverError.severity ?? 'error',
      action: serverError.action?.label ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.AlertAction, {
        onClick: serverError.action.callback
      }, serverError.action.label) : undefined,
      onClose: !serverError.action?.label ? () => {
        setServerError(null);
        setIsSaveDisabled(false);
      } : undefined,
      icon: serverError.IconComponent ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(serverError.IconComponent, null) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.AlertTriangleFilledIcon, null)
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.AlertTitle, null, serverError.message), serverError.action?.message) : null,
    arrow: false,
    slotProps: {
      popper: {
        modifiers: [{
          name: 'offset',
          options: {
            offset: [-10, 10]
          }
        }]
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
    fullWidth: true,
    size: "small",
    color: "global",
    variant: "contained",
    disabled: isSaveDisabled || !isDirty || isSaving,
    onClick: handleSaveClick,
    loading: isSaving
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Save changes', 'elementor'))))), deleteConfirmation && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_delete_confirmation_dialog__WEBPACK_IMPORTED_MODULE_10__.DeleteConfirmationDialog, {
    open: true,
    label: deleteConfirmation.label,
    onConfirm: () => handleDeleteVariableWithConfirmation(deleteConfirmation.id),
    closeDialog: () => setDeleteConfirmation(null)
  }), stopSyncConfirmation && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StopSyncConfirmationDialog, {
    open: true,
    onClose: () => setStopSyncConfirmation(null),
    onConfirm: () => {
      commitStopSync(stopSyncConfirmation);
      setStopSyncConfirmation(null);
    }
  }), isSaveChangesDialogOpen && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SaveChangesDialog, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SaveChangesDialog.Title, {
    onClose: closeSaveChangesDialog
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('You have unsaved changes', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SaveChangesDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SaveChangesDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('You have unsaved changes in the Variables Manager.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SaveChangesDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('To avoid losing your updates, save your changes before leaving.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.SaveChangesDialog.Actions, {
    actions: {
      discard: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Discard', 'elementor'),
        action: () => {
          closeSaveChangesDialog();
          void onRequestClose();
        }
      },
      confirm: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Save', 'elementor'),
        action: async () => {
          const result = await handleSaveClick();
          closeSaveChangesDialog();
          if (result?.success) {
            void onRequestClose();
          }
        }
      }
    }
  })));
}
const usePreventUnload = isDirty => {
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
const StopSyncConfirmationDialog = ({
  open,
  onClose,
  onConfirm
}) => {
  const [, suppressStopSyncMessage] = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useSuppressedMessage)(STOP_SYNC_MESSAGE_KEY);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ConfirmationDialog, {
    open: open,
    onClose: onClose
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ConfirmationDialog.Title, {
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.ColorFilterIcon,
    iconColor: "primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Stop syncing variable color', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ConfirmationDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ConfirmationDialog.ContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('This will disconnect the variable color from Global Colors. Existing uses on your site will automatically switch to a default color.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ConfirmationDialog.Actions, {
    onClose: onClose,
    onConfirm: onConfirm,
    cancelLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Cancel', 'elementor'),
    confirmLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Got it', 'elementor'),
    color: "primary",
    onSuppressMessage: suppressStopSyncMessage,
    suppressLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)("Don't show again", 'elementor')
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-table.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-table.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariablesManagerTable: function() { return /* binding */ VariablesManagerTable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _ui_variable_table_cell__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ui/variable-table-cell */ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-cell.tsx");
/* harmony import */ var _ui_variable_table_row__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./ui/variable-table-row */ "./packages/packages/core/editor-variables/src/components/variables-manager/ui/variable-table-row.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }







const VariablesManagerTable = ({
  menuActions,
  variables,
  onChange: handleOnChange,
  autoEditVariableId,
  onAutoEditComplete,
  onFieldError
}) => {
  const tableContainerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const variableRowRefs = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(new Map());
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (autoEditVariableId && tableContainerRef.current) {
      const rowElement = variableRowRefs.current.get(autoEditVariableId);
      if (rowElement) {
        setTimeout(() => {
          rowElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
            inline: 'nearest'
          });
        }, 100);
      }
    }
  }, [autoEditVariableId]);
  const handleRowRef = id => ref => {
    if (ref) {
      variableRowRefs.current.set(id, ref);
    } else {
      variableRowRefs.current.delete(id);
    }
  };
  const ids = Object.keys(variables).sort(sortVariablesOrder(variables));
  const rows = ids.map(id => {
    const variable = variables[id];
    const variableType = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_3__.getVariableType)(variable.type);
    if (!variableType) {
      return null;
    }
    return {
      id,
      type: variable.type,
      name: variable.label,
      value: variable.value,
      ...variableType
    };
  }).filter(Boolean);
  const tableSX = {
    minWidth: 250,
    tableLayout: 'fixed'
  };
  const handleReorder = newIds => {
    const updatedVariables = {
      ...variables
    };
    newIds.forEach((id, index) => {
      const current = updatedVariables[id];
      if (!current) {
        return;
      }
      updatedVariables[id] = Object.assign({}, current, {
        order: index + 1
      });
    });
    handleOnChange(updatedVariables);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TableContainer, {
    ref: tableContainerRef,
    sx: {
      overflow: 'initial'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Table, {
    sx: tableSX,
    "aria-label": "Variables manager list with drag and drop reordering",
    stickyHeader: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TableHead, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TableRow, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_table_cell__WEBPACK_IMPORTED_MODULE_4__.VariableTableCell, {
    isHeader: true,
    noPadding: true,
    width: 10,
    maxWidth: 10
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_table_cell__WEBPACK_IMPORTED_MODULE_4__.VariableTableCell, {
    isHeader: true
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Name', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_table_cell__WEBPACK_IMPORTED_MODULE_4__.VariableTableCell, {
    isHeader: true
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Value', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_table_cell__WEBPACK_IMPORTED_MODULE_4__.VariableTableCell, {
    isHeader: true,
    noPadding: true,
    width: 16,
    maxWidth: 16
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TableBody, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.UnstableSortableProvider, {
    value: ids,
    onChange: handleReorder,
    variant: "static",
    restrictAxis: true,
    dragOverlay: ({
      children: dragOverlayChildren,
      ...dragOverlayProps
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Table, _extends({
      sx: tableSX
    }, dragOverlayProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TableBody, null, dragOverlayChildren))
  }, rows.map(row => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.UnstableSortableItem, {
    key: row.id,
    id: row.id,
    render: props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_variable_table_row__WEBPACK_IMPORTED_MODULE_5__.VariableRow, _extends({}, props, {
      row: row,
      variables: variables,
      handleOnChange: handleOnChange,
      autoEditVariableId: autoEditVariableId,
      onAutoEditComplete: onAutoEditComplete,
      onFieldError: onFieldError,
      menuActions: menuActions,
      handleRowRef: handleRowRef
    }))
  }))))));
};
function sortVariablesOrder(variables) {
  return (a, b) => {
    const orderA = variables[a]?.order ?? Number.MAX_SAFE_INTEGER;
    const orderB = variables[b]?.order ?? Number.MAX_SAFE_INTEGER;
    return orderA - orderB;
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-repeater-item-slot.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-repeater-item-slot.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BackgroundRepeaterColorIndicator: function() { return /* binding */ BackgroundRepeaterColorIndicator; },
/* harmony export */   BackgroundRepeaterLabel: function() { return /* binding */ BackgroundRepeaterLabel; },
/* harmony export */   BoxShadowRepeaterColorIndicator: function() { return /* binding */ BoxShadowRepeaterColorIndicator; },
/* harmony export */   BoxShadowRepeaterLabel: function() { return /* binding */ BoxShadowRepeaterLabel; },
/* harmony export */   FilterDropShadowIconIndicator: function() { return /* binding */ FilterDropShadowIconIndicator; },
/* harmony export */   FilterDropShadowRepeaterLabel: function() { return /* binding */ FilterDropShadowRepeaterLabel; },
/* harmony export */   FilterSingleSizeRepeaterLabel: function() { return /* binding */ FilterSingleSizeRepeaterLabel; },
/* harmony export */   TransformRepeaterLabel: function() { return /* binding */ TransformRepeaterLabel; },
/* harmony export */   TransitionsSizeVariableLabel: function() { return /* binding */ TransitionsSizeVariableLabel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _utils_size_value__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/size-value */ "./packages/packages/core/editor-variables/src/utils/size-value.ts");
/* harmony import */ var _ui_color_indicator__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./ui/color-indicator */ "./packages/packages/core/editor-variables/src/components/ui/color-indicator.tsx");







const useColorVariable = value => {
  const variableId = value?.value?.color?.value;
  return (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_4__.getVariable)(variableId || '');
};
const BackgroundRepeaterColorIndicator = ({
  value
}) => {
  const colorVariable = useColorVariable(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_color_indicator__WEBPACK_IMPORTED_MODULE_6__.ColorIndicator, {
    component: "span",
    size: "inherit",
    value: colorVariable?.value
  });
};
const BackgroundRepeaterLabel = ({
  value
}) => {
  const colorVariable = useColorVariable(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span"
  }, colorVariable?.label);
};
const BoxShadowRepeaterColorIndicator = ({
  value
}) => {
  const colorVariable = useColorVariable(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_color_indicator__WEBPACK_IMPORTED_MODULE_6__.ColorIndicator, {
    component: "span",
    size: "inherit",
    value: colorVariable?.value
  });
};
const FilterDropShadowIconIndicator = ({
  value
}) => {
  const {
    args
  } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.extract(value) || {};
  const {
    color
  } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.extract(args) || {};
  const colorVariable = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_4__.getVariable)(color?.value || '');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_color_indicator__WEBPACK_IMPORTED_MODULE_6__.ColorIndicator, {
    component: "span",
    size: "inherit",
    value: colorVariable?.value
  });
};
const FilterSingleSizeRepeaterLabel = ({
  value
}) => {
  const cssFilterFunction = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.extract(value);
  if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.isValid(cssFilterFunction?.args)) {
    return null;
  }
  const args = cssFilterFunction?.args;
  const func = cssFilterFunction?.func?.value ?? '';
  const rendered = (0,_utils_size_value__WEBPACK_IMPORTED_MODULE_5__.sizeValue)(args?.value?.size);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span",
    style: {
      textTransform: 'capitalize'
    }
  }, `${func}: `), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span"
  }, rendered));
};
const FilterDropShadowRepeaterLabel = ({
  value
}) => {
  const {
    args
  } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.extract(value) || {};
  const {
    xAxis,
    yAxis,
    blur
  } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.extract(args) || {};
  const labels = [];
  for (const val of [xAxis, yAxis, blur]) {
    const rendered = (0,_utils_size_value__WEBPACK_IMPORTED_MODULE_5__.sizeValue)(val);
    if (rendered) {
      labels.push(rendered);
    }
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Drop shadow:', 'elementor'), " ", labels.join(' '));
};
const BoxShadowRepeaterLabel = ({
  value
}) => {
  const {
    position,
    hOffset,
    vOffset,
    blur,
    spread
  } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.shadowPropTypeUtil.extract(value) || {};
  const labels = [];
  for (const val of [hOffset, vOffset, blur, spread]) {
    const rendered = (0,_utils_size_value__WEBPACK_IMPORTED_MODULE_5__.sizeValue)(val);
    if (rendered) {
      labels.push(rendered);
    }
  }
  const positionLabel = position?.value || 'outset';
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span",
    style: {
      textTransform: 'capitalize'
    }
  }, positionLabel, ": ", labels.join(' '));
};
const TransformRepeaterLabel = ({
  value
}) => {
  const labels = [];
  if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.moveTransformPropTypeUtil.isValid(value)) {
    labels.push((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Move:', 'elementor'));
    const {
      x,
      y,
      z
    } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.moveTransformPropTypeUtil.extract(value) || {};
    for (const val of [x, y, z]) {
      const rendered = (0,_utils_size_value__WEBPACK_IMPORTED_MODULE_5__.sizeValue)(val);
      if (rendered) {
        labels.push(rendered);
      }
    }
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span"
  }, labels.join(' '));
};
const TransitionsSizeVariableLabel = ({
  value: prop
}) => {
  let label = '';
  const variableId = prop?.value?.size?.value || '';
  const variable = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_4__.getVariable)(variableId);
  if (variable && _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.selectionSizePropTypeUtil.isValid(prop)) {
    const selection = prop.value?.selection?.value?.key?.value;
    if (selection) {
      label += `${selection}: `;
    }
    label += variable?.value;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    component: "span"
  }, label);
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/components/variables-selection.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/components/variables-selection.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariablesSelection: function() { return /* binding */ VariablesSelection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../hooks/use-variable-bound-prop */ "./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");
/* harmony import */ var _ui_empty_state__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./ui/empty-state */ "./packages/packages/core/editor-variables/src/components/ui/empty-state.tsx");
/* harmony import */ var _ui_menu_item_content__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./ui/menu-item-content */ "./packages/packages/core/editor-variables/src/components/ui/menu-item-content.tsx");
/* harmony import */ var _ui_no_search_results__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./ui/no-search-results */ "./packages/packages/core/editor-variables/src/components/ui/no-search-results.tsx");
/* harmony import */ var _ui_styled_menu_list__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./ui/styled-menu-list */ "./packages/packages/core/editor-variables/src/components/ui/styled-menu-list.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
















const SIZE = 'tiny';
const CREATE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Create variable', 'elementor');
const MANAGER_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Variables Manager', 'elementor');
const getProUpgradeUrl = variableType => `https://go.elementor.com/renew-license-panel-${variableType}-variable`;
const VariablesSelection = ({
  closePopover,
  onAdd,
  onEdit,
  onSettings,
  disabled = false
}) => {
  const {
    icon: VariableIcon,
    startIcon,
    variableType,
    propTypeUtil,
    emptyState
  } = (0,_context_variable_type_context__WEBPACK_IMPORTED_MODULE_6__.useVariableType)();
  const {
    value: variable,
    setValue: setVariable,
    path
  } = (0,_hooks_use_variable_bound_prop__WEBPACK_IMPORTED_MODULE_8__.useVariableBoundProp)();
  const [searchValue, setSearchValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const {
    list: variables,
    hasMatches: hasSearchResults,
    isSourceNotEmpty: hasVariables,
    hasNoCompatibleVariables
  } = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__.useFilteredVariables)(searchValue, propTypeUtil.key);
  const handleSetVariable = key => {
    setVariable(key);
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_9__.trackVariableEvent)({
      varType: variableType,
      controlPath: path.join('.'),
      action: 'connect'
    });
    closePopover();
  };
  const onAddAndTrack = () => {
    onAdd?.();
    (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_9__.trackVariableEvent)({
      varType: variableType,
      controlPath: path.join('.'),
      action: 'add'
    });
  };
  const actions = [];
  if (onAdd) {
    actions.push(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      key: "add",
      placement: "top",
      title: CREATE_LABEL
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, {
      id: "add-variable-button",
      size: SIZE,
      onClick: onAddAndTrack,
      "aria-label": CREATE_LABEL,
      disabled: disabled
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PlusIcon, {
      fontSize: SIZE
    })))));
  }
  if (onSettings) {
    const handleOpenManager = () => {
      onSettings();
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_9__.trackVariablesManagerEvent)({
        action: 'openManager',
        source: 'vars-popover',
        varType: variableType,
        controlPath: path.join('.')
      });
    };
    actions.push(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tooltip, {
      key: "settings",
      placement: "top",
      title: MANAGER_LABEL
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, {
      id: "variables-manager-button",
      size: SIZE,
      onClick: handleOpenManager,
      "aria-label": MANAGER_LABEL
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SettingsIcon, {
      fontSize: SIZE
    }))));
  }
  const StartIcon = startIcon || (() => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
    fontSize: SIZE
  }));
  const items = variables.map(({
    value,
    label,
    key
  }) => ({
    type: 'item',
    value: key,
    label,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartIcon, {
      value: value
    }),
    secondaryText: value,
    onEdit: onEdit ? () => onEdit?.(key) : undefined
  }));
  const handleSearch = search => {
    setSearchValue(search);
  };
  const handleClearSearch = () => {
    setSearchValue('');
  };
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (disabled) {
      (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackViewPromotion)({
        target_name: 'variables_popover',
        target_location: 'widget_panel',
        location_l1: 'variables_list'
      });
    }
  }, [disabled]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SectionPopoverBody, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Variables', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ColorFilterIcon, {
      fontSize: SIZE
    }),
    onClose: closePopover,
    actions: actions
  }), hasVariables && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SearchField, {
    value: searchValue,
    onSearch: handleSearch,
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Search', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), hasVariables && hasSearchResults && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverMenuList, {
    items: items,
    onSelect: disabled ? () => {} : handleSetVariable,
    onClose: () => {},
    selectedValue: variable,
    "data-testid": `${variableType}-variables-list`,
    menuListTemplate: props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_styled_menu_list__WEBPACK_IMPORTED_MODULE_13__.VariablesStyledMenuList, _extends({}, props, {
      disabled: disabled
    })),
    menuItemContentTemplate: item => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_menu_item_content__WEBPACK_IMPORTED_MODULE_11__.MenuItemContent, {
      item: item,
      disabled: disabled
    })
  }), disabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PromotionAlert, {
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.sprintf)(/* translators: %s: Variable Type. */
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Upgrade to continue creating and editing %s variables.', 'elementor'), variableType),
    upgradeUrl: getProUpgradeUrl(variableType),
    onCtaClick: () => (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackUpgradePromotionClick)({
      target_name: 'variables_popover',
      location_l1: 'variables_list'
    })
  })), !hasSearchResults && hasVariables && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_no_search_results__WEBPACK_IMPORTED_MODULE_12__.NoSearchResults, {
    searchValue: searchValue,
    onClear: handleClearSearch,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: "large"
    })
  }), disabled && !hasVariables && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_empty_state__WEBPACK_IMPORTED_MODULE_10__.EmptyState, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.sprintf)(/* translators: %s: Variable Type. */
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('No %s variables yet', 'elementor'), variableType),
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.sprintf)(/* translators: %s: Variable Type. */
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Upgrade to create %s variables and maintain consistent element sizing.', 'elementor'), variableType),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: "large"
    })
  }, emptyState), !hasVariables && !hasNoCompatibleVariables && !disabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_empty_state__WEBPACK_IMPORTED_MODULE_10__.EmptyState, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.sprintf)(/* translators: %s: Variable Type. */
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Create your first %s variable', 'elementor'), variableType),
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Variables are saved attributes that you can apply anywhere on your site.', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: "large"
    }),
    onAdd: onAdd
  }), hasNoCompatibleVariables && !disabled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_empty_state__WEBPACK_IMPORTED_MODULE_10__.EmptyState, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('No compatible variables', 'elementor'),
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Looks like none of your variables work with this control. Create a new variable to use it here.', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableIcon, {
      fontSize: "large"
    }),
    onAdd: onAdd
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/context/variable-selection-popover.context.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/context/variable-selection-popover.context.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PopoverContentRefContextProvider: function() { return /* binding */ PopoverContentRefContextProvider; },
/* harmony export */   usePopoverContentRef: function() { return /* binding */ usePopoverContentRef; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);



const PopoverContentRefContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const PopoverContentRefContextProvider = ({
  children
}) => {
  const [anchorRef, setAnchorRef] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PopoverContentRefContext.Provider, {
    value: anchorRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    ref: setAnchorRef
  }, children));
};
const usePopoverContentRef = () => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(PopoverContentRefContext);
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/context/variable-type-context.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableTypeProvider: function() { return /* binding */ VariableTypeProvider; },
/* harmony export */   useVariableType: function() { return /* binding */ useVariableType; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");



const VariableTypeContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function VariableTypeProvider({
  children,
  propTypeKey
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VariableTypeContext.Provider, {
    value: propTypeKey
  }, children);
}
function useVariableType() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(VariableTypeContext);
  if (context === null) {
    throw new Error('useVariableType must be used within a VariableTypeProvider');
  }
  return (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_1__.getVariableType)(context);
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/controls/variable-control.tsx":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/controls/variable-control.tsx ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   VariableControl: function() { return /* binding */ VariableControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_ui_variable_assigned_variable__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/ui/variable/assigned-variable */ "./packages/packages/core/editor-variables/src/components/ui/variable/assigned-variable.tsx");
/* harmony import */ var _components_ui_variable_deleted_variable__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../components/ui/variable/deleted-variable */ "./packages/packages/core/editor-variables/src/components/ui/variable/deleted-variable.tsx");
/* harmony import */ var _components_ui_variable_mismatch_variable__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/ui/variable/mismatch-variable */ "./packages/packages/core/editor-variables/src/components/ui/variable/mismatch-variable.tsx");
/* harmony import */ var _components_ui_variable_missing_variable__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../components/ui/variable/missing-variable */ "./packages/packages/core/editor-variables/src/components/ui/variable/missing-variable.tsx");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");








const VariableControl = () => {
  const boundProp = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const boundPropValue = boundProp.value ?? boundProp.placeholder;
  const assignedVariable = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_6__.useVariable)(boundPropValue?.value);
  if (!assignedVariable) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_variable_missing_variable__WEBPACK_IMPORTED_MODULE_5__.MissingVariable, null);
  }
  const {
    $$type: propTypeKey
  } = boundPropValue;
  if (assignedVariable?.deleted) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_variable_deleted_variable__WEBPACK_IMPORTED_MODULE_3__.DeletedVariable, {
      variable: assignedVariable,
      propTypeKey: propTypeKey
    });
  }
  const {
    isCompatible
  } = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_7__.getVariableType)(assignedVariable.type);
  if (isCompatible && !isCompatible(boundProp?.propType, assignedVariable)) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_variable_mismatch_variable__WEBPACK_IMPORTED_MODULE_4__.MismatchVariable, {
      variable: assignedVariable
    });
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_variable_assigned_variable__WEBPACK_IMPORTED_MODULE_2__.AssignedVariable, {
    variable: assignedVariable,
    propTypeKey: propTypeKey
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/create-style-variables-repository.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/create-style-variables-repository.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createStyleVariablesRepository: function() { return /* binding */ createStyleVariablesRepository; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _prop_types_font_variable_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./prop-types/font-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/font-variable-prop-type.ts");


const createStyleVariablesRepository = () => {
  const variables = {};
  let subscription;
  const subscribe = cb => {
    subscription = cb;
    return () => {
      subscription = () => {};
    };
  };
  const notify = () => {
    if (typeof subscription === 'function') {
      subscription({
        ...variables
      });
    }
  };
  const shouldUpdate = (key, maybeUpdated) => {
    if (!(key in variables)) {
      return true;
    }
    if (variables[key].label !== maybeUpdated.label) {
      return true;
    }
    if (variables[key].value !== maybeUpdated.value) {
      return true;
    }
    if (!variables[key]?.deleted && maybeUpdated?.deleted) {
      return true;
    }
    if (variables[key]?.deleted && !maybeUpdated?.deleted) {
      return true;
    }
    return false;
  };
  const applyUpdates = updatedVars => {
    let hasChanges = false;
    for (const [key, variable] of Object.entries(updatedVars)) {
      if (shouldUpdate(key, variable)) {
        variables[key] = variable;
        if (variable.type === _prop_types_font_variable_prop_type__WEBPACK_IMPORTED_MODULE_1__.fontVariablePropTypeUtil.key) {
          fontEnqueue(variable.value);
        }
        hasChanges = true;
      }
    }
    return hasChanges;
  };
  const fontEnqueue = value => {
    if (!value) {
      return;
    }
    try {
      (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.enqueueFont)(value);
    } catch {
      // This prevents font enqueueing failures from breaking variable updates
    }
  };
  const update = updatedVars => {
    if (applyUpdates(updatedVars)) {
      notify();
    }
  };
  return {
    subscribe,
    update
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/hooks/use-initial-value.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/hooks/use-initial-value.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useInitialValue: function() { return /* binding */ useInitialValue; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _use_prop_variables__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");



const useInitialValue = () => {
  const {
    value: initial
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.useBoundProp)();
  const hasAssignedVariable = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_1__.hasVariableType)(initial?.$$type) && Boolean(initial?.value);
  const variable = (0,_use_prop_variables__WEBPACK_IMPORTED_MODULE_2__.useVariable)(hasAssignedVariable ? initial.value : '');
  if (hasAssignedVariable) {
    return variable ? variable.value : '';
  }
  return initial?.value ?? '';
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/hooks/use-permissions.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/hooks/use-permissions.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   usePermissions: function() { return /* binding */ usePermissions; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__);

const usePermissions = () => {
  const {
    canUser,
    isAdmin
  } = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__.useCurrentUserCapabilities)();
  return {
    canAssign: () => canUser('edit_posts'),
    canUnlink: () => canUser('edit_posts'),
    canAdd: () => isAdmin,
    canDelete: () => isAdmin,
    canEdit: () => isAdmin,
    canRestore: () => isAdmin,
    canManageSettings: () => isAdmin
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/hooks/use-prop-variable-action.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/hooks/use-prop-variable-action.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   usePropVariableAction: function() { return /* binding */ usePropVariableAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_variable_selection_popover__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/variable-selection-popover */ "./packages/packages/core/editor-variables/src/components/variable-selection-popover.tsx");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");







const usePropVariableAction = () => {
  const {
    propType,
    path
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const variable = resolveVariableFromPropType(propType);
  return {
    visible: Boolean(variable),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ColorFilterIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Variables', 'elementor'),
    content: ({
      close: closePopover
    }) => {
      if (!variable) {
        return null;
      }
      trackOpenVariablePopover(path, variable.variableType);
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_variable_selection_popover__WEBPACK_IMPORTED_MODULE_4__.VariableSelectionPopover, {
        closePopover: closePopover,
        propTypeKey: variable.propTypeUtil.key
      });
    }
  };
};
const resolveVariableFromPropType = propType => {
  if (propType.kind !== 'union') {
    return undefined;
  }
  for (const key of Object.keys(propType.prop_types)) {
    const variable = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__.getVariableType)(key);
    if (variable) {
      return variable;
    }
  }
  return undefined;
};
const trackOpenVariablePopover = (path, variableType) => {
  (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_5__.trackVariableEvent)({
    varType: variableType,
    controlPath: path.join('.'),
    action: 'open'
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createVariable: function() { return /* binding */ createVariable; },
/* harmony export */   deleteVariable: function() { return /* binding */ deleteVariable; },
/* harmony export */   getVariable: function() { return /* binding */ getVariable; },
/* harmony export */   getVariables: function() { return /* binding */ getVariables; },
/* harmony export */   hasVariable: function() { return /* binding */ hasVariable; },
/* harmony export */   restoreVariable: function() { return /* binding */ restoreVariable; },
/* harmony export */   updateVariable: function() { return /* binding */ updateVariable; },
/* harmony export */   useFilteredVariables: function() { return /* binding */ useFilteredVariables; },
/* harmony export */   useVariable: function() { return /* binding */ useVariable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _utils_filter_by_search__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/filter-by-search */ "./packages/packages/core/editor-variables/src/utils/filter-by-search.ts");
/* harmony import */ var _utils_variables_to_list__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils/variables-to-list */ "./packages/packages/core/editor-variables/src/utils/variables-to-list.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");







const getVariables = (includeDeleted = true) => {
  const variables = _service__WEBPACK_IMPORTED_MODULE_3__.service.variables();
  if (includeDeleted) {
    return variables;
  }
  return Object.fromEntries(Object.entries(variables).filter(([, variable]) => !variable.deleted));
};
const hasVariable = key => {
  return getVariables()[key] !== undefined;
};

/**
 * @param      key
 * @deprecated Use getVariable instead
 */
const useVariable = key => {
  return getVariable(key);
};
function getVariable(key) {
  const variables = getVariables();
  if (!variables?.[key]) {
    return null;
  }
  return {
    ...variables[key],
    key
  };
}
const useFilteredVariables = (searchValue, propTypeKey) => {
  const baseVariables = usePropVariables(propTypeKey);
  const typeFilteredVariables = useVariableSelectionFilter(baseVariables);
  const searchFilteredVariables = (0,_utils_filter_by_search__WEBPACK_IMPORTED_MODULE_4__.filterBySearch)(typeFilteredVariables, searchValue);
  const sortedVariables = searchFilteredVariables.sort((a, b) => {
    const orderA = a.order ?? Number.MAX_SAFE_INTEGER;
    const orderB = b.order ?? Number.MAX_SAFE_INTEGER;
    return orderA - orderB;
  });
  return {
    list: sortedVariables,
    hasMatches: searchFilteredVariables.length > 0,
    isSourceNotEmpty: typeFilteredVariables.length > 0,
    hasNoCompatibleVariables: baseVariables.length > 0 && typeFilteredVariables.length === 0
  };
};
const useVariableSelectionFilter = variables => {
  const {
    selectionFilter
  } = (0,_context_variable_type_context__WEBPACK_IMPORTED_MODULE_2__.useVariableType)();
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  return selectionFilter ? selectionFilter(variables, propType) : variables;
};
const usePropVariables = propKey => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => normalizeVariables(propKey), [propKey]);
};
const getMatchingTypes = propKey => {
  const matchingTypes = [];
  const allTypes = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__.getVariableTypes)();
  const variableType = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__.getVariableType)(propKey);
  Object.entries(allTypes).forEach(([key, typeOptions]) => {
    if (variableType.variableType === typeOptions.variableType) {
      matchingTypes.push(key);
    }
  });
  return matchingTypes;
};
const normalizeVariables = propKey => {
  const variables = getVariables(false);
  const matchingTypes = getMatchingTypes(propKey);
  return (0,_utils_variables_to_list__WEBPACK_IMPORTED_MODULE_5__.variablesToList)(variables).filter(variable => matchingTypes.includes(variable.type)).map(_utils_variables_to_list__WEBPACK_IMPORTED_MODULE_5__.toNormalizedVariable);
};
const extractId = ({
  id
}) => id;
const createVariable = (newVariable, options) => {
  return _service__WEBPACK_IMPORTED_MODULE_3__.service.create(newVariable, options).then(extractId);
};
const updateVariable = (updateId, {
  value,
  label,
  type
}, options) => {
  return _service__WEBPACK_IMPORTED_MODULE_3__.service.update(updateId, {
    value,
    label,
    type
  }, options).then(extractId);
};
const deleteVariable = deleteId => {
  return _service__WEBPACK_IMPORTED_MODULE_3__.service.delete(deleteId).then(extractId);
};
const restoreVariable = (restoreId, label, value, type) => {
  return _service__WEBPACK_IMPORTED_MODULE_3__.service.restore(restoreId, label, value, type).then(extractId);
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/hooks/use-quota-permissions.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/hooks/use-quota-permissions.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useQuotaPermissions: function() { return /* binding */ useQuotaPermissions; }
/* harmony export */ });
const useQuotaPermissions = variableType => {
  const quotaConfig = {
    ...(window.ElementorVariablesQuotaConfig ?? {}),
    ...(window.ElementorVariablesQuotaConfigExtended ?? {})
  };

  // BC: Remove when 4.01 is released
  const hasLegacySupport = quotaConfig[variableType] === undefined && window.elementorPro;
  const limit = quotaConfig[variableType] || 0;
  const hasPermission = hasLegacySupport || limit > 0;
  return {
    canAdd: () => hasPermission,
    canEdit: () => hasPermission
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/hooks/use-variable-bound-prop.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   resolveBoundPropAndSetValue: function() { return /* binding */ resolveBoundPropAndSetValue; },
/* harmony export */   useVariableBoundProp: function() { return /* binding */ useVariableBoundProp; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _context_variable_type_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../context/variable-type-context */ "./packages/packages/core/editor-variables/src/context/variable-type-context.tsx");



const useVariableBoundProp = () => {
  const {
    propTypeUtil
  } = (0,_context_variable_type_context__WEBPACK_IMPORTED_MODULE_2__.useVariableType)();
  const boundProp = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.useBoundProp)(propTypeUtil);
  return {
    ...boundProp,
    setVariableValue: value => resolveBoundPropAndSetValue(value, boundProp),
    variableId: boundProp.value ?? boundProp.placeholder
  };
};
const resolveBoundPropAndSetValue = (value, boundProp) => {
  const propValue = unwrapValue(boundProp.value);
  const placeholder = unwrapValue(boundProp.placeholder);
  const newValue = unwrapValue(value);
  if (!propValue && placeholder === newValue) {
    return boundProp.setValue(null);
  }
  return boundProp.setValue(value);
};
const unwrapValue = input => {
  if ((0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.isTransformable)(input)) {
    return input.value;
  }
  return input;
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/init.ts":
/*!*************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/init.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/menus */ "@elementor/menus");
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_menus__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_global_styles_import_listener__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/global-styles-import-listener */ "./packages/packages/core/editor-variables/src/components/global-styles-import-listener.tsx");
/* harmony import */ var _components_mcp_variable_connect_listener__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/mcp-variable-connect-listener */ "./packages/packages/core/editor-variables/src/components/mcp-variable-connect-listener.tsx");
/* harmony import */ var _controls_variable_control__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./controls/variable-control */ "./packages/packages/core/editor-variables/src/controls/variable-control.tsx");
/* harmony import */ var _hooks_use_prop_variable_action__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./hooks/use-prop-variable-action */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variable-action.tsx");
/* harmony import */ var _mcp__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./mcp */ "./packages/packages/core/editor-variables/src/mcp/index.ts");
/* harmony import */ var _register_variable_types__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./register-variable-types */ "./packages/packages/core/editor-variables/src/register-variable-types.tsx");
/* harmony import */ var _renderers_style_variables_renderer__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./renderers/style-variables-renderer */ "./packages/packages/core/editor-variables/src/renderers/style-variables-renderer.tsx");
/* harmony import */ var _repeater_injections__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./repeater-injections */ "./packages/packages/core/editor-variables/src/repeater-injections.ts");
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");















const {
  registerPopoverAction
} = _elementor_menus__WEBPACK_IMPORTED_MODULE_4__.controlActionsMenu;
function init() {
  (0,_register_variable_types__WEBPACK_IMPORTED_MODULE_10__.registerVariableTypes)();
  (0,_repeater_injections__WEBPACK_IMPORTED_MODULE_12__.registerRepeaterInjections)();
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.registerControlReplacement)({
    component: _controls_variable_control__WEBPACK_IMPORTED_MODULE_7__.VariableControl,
    condition: ({
      value,
      placeholder
    }) => {
      if (hasVariableAssigned(value)) {
        return true;
      }
      if (value) {
        return false;
      }
      return hasVariableAssigned(placeholder);
    }
  });
  registerPopoverAction({
    id: 'variables',
    priority: 40,
    useProps: _hooks_use_prop_variable_action__WEBPACK_IMPORTED_MODULE_8__.usePropVariableAction
  });
  _service__WEBPACK_IMPORTED_MODULE_13__.service.init();
  const variablesMcpRegistry = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__.getMCPByDomain)('variables', {
    instructions: `Everything related to V4 ( Atomic ) variables.
# Global variables
- Create/update/delete global variables
- Get list of global variables
- Get details of a global variable
`
  });
  (0,_mcp__WEBPACK_IMPORTED_MODULE_9__.initMcp)(variablesMcpRegistry, (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_2__.getMCPByDomain)('canvas'));
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoTop)({
    id: 'canvas-style-variables-render',
    component: _renderers_style_variables_renderer__WEBPACK_IMPORTED_MODULE_11__.StyleVariablesRenderer
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'variables-import-listener',
    component: _components_global_styles_import_listener__WEBPACK_IMPORTED_MODULE_5__.GlobalStylesImportListener
  });
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'mcp-variable-connect-listener',
    component: _components_mcp_variable_connect_listener__WEBPACK_IMPORTED_MODULE_6__.McpVariableConnectListener
  });
}
function hasVariableAssigned(value) {
  if ((0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__.isTransformable)(value)) {
    return (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_14__.hasVariableType)(value.$$type);
  }
  return false;
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/mcp/index.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/mcp/index.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initMcp: function() { return /* binding */ initMcp; }
/* harmony export */ });
/* harmony import */ var _manage_variable_tool__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./manage-variable-tool */ "./packages/packages/core/editor-variables/src/mcp/manage-variable-tool.ts");
/* harmony import */ var _variables_resource__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./variables-resource */ "./packages/packages/core/editor-variables/src/mcp/variables-resource.ts");


function initMcp(reg, canvasMcpEntry) {
  window.addEventListener('elementor/init', () => {
    (0,_manage_variable_tool__WEBPACK_IMPORTED_MODULE_0__.initManageVariableTool)(reg);
    (0,_variables_resource__WEBPACK_IMPORTED_MODULE_1__.initVariablesResource)(reg, canvasMcpEntry);
  }, {
    once: true
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/mcp/manage-variable-tool.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/mcp/manage-variable-tool.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initManageVariableTool: function() { return /* binding */ initManageVariableTool; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _sync_get_font_configs__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../sync/get-font-configs */ "./packages/packages/core/editor-variables/src/sync/get-font-configs.ts");
/* harmony import */ var _utils_validations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");
/* harmony import */ var _variable_tool_prompt__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./variable-tool-prompt */ "./packages/packages/core/editor-variables/src/mcp/variable-tool-prompt.ts");
/* harmony import */ var _variables_resource__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./variables-resource */ "./packages/packages/core/editor-variables/src/mcp/variables-resource.ts");







const VARIABLE_TYPES = {
  COLOR: 'global-color-variable',
  FONT: 'global-font-variable',
  SIZE: 'global-size-variable',
  CUSTOM_SIZE: 'global-custom-size-variable'
};
const LENGTH_UNIT_PATTERN = /^(auto|\d+(\.\d+)?(px|rem|em|vh|vw|%|ch|s|ms))$/i;
const COLOR_PATTERN = /^(#[0-9a-f]{3,8}|rgba?\(|hsl)/i;
function validateValueForType(type, value) {
  if (type === VARIABLE_TYPES.FONT && LENGTH_UNIT_PATTERN.test(value.trim())) {
    return `Font variable value must be a font family name (e.g. "Roboto"), not a size value like "${value}". Use "global-size-variable" or "global-custom-size-variable" for spacing/size values.`;
  }
  if (type === VARIABLE_TYPES.COLOR && !COLOR_PATTERN.test(value.trim())) {
    return `Color variable value should be a CSS color (e.g. "#FF0000"), got "${value}".`;
  }
  if (type === VARIABLE_TYPES.SIZE && !LENGTH_UNIT_PATTERN.test(value.trim())) {
    return `Size variable value should include a CSS unit (e.g. "16px") or be "auto", got "${value}".`;
  }
  if (type === VARIABLE_TYPES.FONT && !isFontAvailable(value)) {
    return `Font "${value}" is not supported in WordPress. Please choose one of the available font families.`;
  }
  return null;
}
function isFontAvailable(font) {
  const fonts = (0,_sync_get_font_configs__WEBPACK_IMPORTED_MODULE_3__.getFontConfigs)();
  const key = font.trim();
  return !!fonts?.[key];
}
const initManageVariableTool = reg => {
  const {
    addTool,
    resource
  } = reg;
  const RUNTIME_ALLOWED_VARIABLE_TYPES = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.isProActive)() ? [VARIABLE_TYPES.COLOR, VARIABLE_TYPES.FONT, VARIABLE_TYPES.SIZE, VARIABLE_TYPES.CUSTOM_SIZE] : [VARIABLE_TYPES.COLOR, VARIABLE_TYPES.FONT];
  resource('manage-global-variable-guide', _variable_tool_prompt__WEBPACK_IMPORTED_MODULE_5__.MANAGE_VARIABLES_GUIDE_URI, {
    title: 'Manage Global Variable Guide',
    description: 'Detailed guide for using the manage-global-variable tool',
    mimeType: 'text/plain'
  }, async uri => ({
    contents: [{
      uri: uri.href,
      mimeType: 'text/plain',
      text: (0,_variable_tool_prompt__WEBPACK_IMPORTED_MODULE_5__.generateVariablesPrompt)()
    }]
  }));
  addTool({
    name: 'manage-global-variable',
    description: 'Manage V4 global variables (color, font, size, custom-size). Read the guide resource before use. font = font-famliy, size = measured unit, custom-size = calculated values',
    schema: {
      action: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['create', 'update', 'delete']),
      id: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().optional().describe('Variable id — required for update/delete. Get from the global-variables resource.'),
      type: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(RUNTIME_ALLOWED_VARIABLE_TYPES),
      label: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().describe('Variable label (lowercase, dash-separated) — required for create/update.'),
      value: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().optional().describe('Plain CSS value — required for create/update. Color: hex/rgba/hsl. Font: family name only. Size: value with unit e.g. "16px", or "auto" (Pro). Do NOT pass JSON.')
    },
    outputSchema: {
      status: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['ok']).describe('Operation status'),
      message: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().optional().describe('Error details if status is error')
    },
    requiredResources: [{
      uri: _variable_tool_prompt__WEBPACK_IMPORTED_MODULE_5__.MANAGE_VARIABLES_GUIDE_URI,
      description: 'Full guide for variable types, naming rules, and usage'
    }, {
      uri: _variables_resource__WEBPACK_IMPORTED_MODULE_6__.GLOBAL_VARIABLES_URI,
      description: 'Current global variables — check before creating to avoid duplicates'
    }],
    isDestructive: true,
    handler: async params => {
      const operations = getServiceActions(_service__WEBPACK_IMPORTED_MODULE_2__.service);
      const op = operations[params.action];
      if (op) {
        await op(params);
        return {
          status: 'ok'
        };
      }
      throw new Error(`Unknown action ${params.action}`);
    }
  });
};
function getServiceActions(svc) {
  return {
    create({
      type,
      label,
      value
    }) {
      if (!type || !label || !value) {
        throw new Error('Create requires type, label, and value');
      }
      if ((type === VARIABLE_TYPES.SIZE || type === VARIABLE_TYPES.CUSTOM_SIZE) && !(0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.isProActive)()) {
        throw new Error('Creating size variables requires Elementor Pro.');
      }
      const labelError = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_4__.validateLabel)(label);
      if (labelError) {
        throw new Error(labelError);
      }
      const valueError = validateValueForType(type, value);
      if (valueError) {
        throw new Error(valueError);
      }
      return svc.create({
        type,
        label,
        value
      }, {
        eventData: {
          executedBy: 'mcp_tool'
        }
      });
    },
    update({
      id,
      label,
      value
    }) {
      if (!id || !label || !value) {
        throw new Error('Update requires id, label, and value');
      }
      const labelError = (0,_utils_validations__WEBPACK_IMPORTED_MODULE_4__.validateLabel)(label);
      if (labelError) {
        throw new Error(labelError);
      }
      const existingVariable = svc.variables()[id];
      if (existingVariable) {
        const valueError = validateValueForType(existingVariable.type, value);
        if (valueError) {
          throw new Error(valueError);
        }
      }
      return svc.update(id, {
        label,
        value
      }, {
        eventData: {
          executedBy: 'mcp_tool'
        }
      });
    },
    delete({
      id
    }) {
      if (!id) {
        throw new Error('delete requires id');
      }
      return svc.delete(id);
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/mcp/variable-tool-prompt.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/mcp/variable-tool-prompt.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MANAGE_VARIABLES_GUIDE_URI: function() { return /* binding */ MANAGE_VARIABLES_GUIDE_URI; },
/* harmony export */   generateVariablesPrompt: function() { return /* binding */ generateVariablesPrompt; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_1__);


const MANAGE_VARIABLES_GUIDE_URI = 'elementor://variables/tools/manage-global-variable-guide';
const generateVariablesPrompt = () => {
  const prompt = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.toolPrompts)('manage-global-variable');
  const proIsActive = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_1__.isProActive)();
  const sizeVariableSection = proIsActive ? `- **global-size-variable** — A simple CSS length with a unit (Elementor Pro). Use this for fixed spacing, font sizes, or layout values. Example: \`16px\`, \`1.5rem\`, \`2em\`, \`10vh\`
- **global-custom-size-variable** — Any CSS size expression that goes beyond a simple number + unit (Elementor Pro). Use this when the value is a CSS function, a keyword, or a combination of units that \`global-size-variable\` cannot represent. Example: \`auto\`, \`clamp(1rem, 2vw, 2rem)\`, \`calc(100% - 32px)\`, \`min(50vw, 600px)\`, \`300ms\`, \`2ch\`. When in doubt: if the value contains a function call or a keyword, use \`global-custom-size-variable\`.` : `- ~~global-size-variable~~ — requires Elementor Pro (not available on this site)
- ~~global-custom-size-variable~~ — requires Elementor Pro (not available on this site)`;
  prompt.description(`
# Purpose
Create, update, or delete V4 global CSS variables. These are distinct from legacy v3 globals and map 1:1 to \`--css-var: VALUE\`.

# Available Types
- **global-color-variable** — CSS color value. Example: \`#FF0000\`, \`rgba(255,0,0,1)\`, \`hsl(0,100%,50%)\`
- **global-font-variable** — Font family name ONLY — NOT a size or px value. Example: \`Roboto\`, \`Open Sans\`. NEVER pass px/rem here.
${sizeVariableSection}

# Naming Rules
- Labels must be **lowercase**, using only letters (a-z), numbers, digits (0-9), dashes (-), or underscores (_)
- No spaces, no special characters
- Example: "Headline Primary" → \`headline-primary\`
- Labels must be unique — always check [elementor://global-variables] first

# Value Rules
- Provide a **plain CSS value** only — do NOT pass JSON, legacy-globals object structures, or variable references
- Values are inserted as-is: \`--css-var: <value>\`
- NEVER store a px/rem value inside a \`global-font-variable\` — use \`global-size-variable\` (Pro) instead

# Operations
- **create** — requires \`type\`, \`label\`, \`value\`. Label must be unique.
- **update** — requires \`id\`, \`label\`, \`value\`. Get \`id\` from [elementor://global-variables]. When renaming: keep existing value. When changing value: keep exact existing label.
- **delete** — requires \`id\`. DESTRUCTIVE — always confirm with user before executing.
`);
  prompt.parameter('action', '"create", "update", or "delete".');
  prompt.parameter('type', 'Variable type. Required for create. See Available Types above.');
  prompt.parameter('label', 'Variable name (lowercase, dash-separated). Required for create/update.');
  prompt.parameter('value', 'Plain CSS value matching the variable type. Required for create/update. Do NOT pass JSON.');
  prompt.parameter('id', 'Variable ID. Required for update/delete. Obtain from [elementor://global-variables].');
  prompt.example(`
Create a brand color:
{ "action": "create", "type": "global-color-variable", "label": "brand-primary", "value": "#1A73E8" }

Create a heading font:
{ "action": "create", "type": "global-font-variable", "label": "font-heading", "value": "Playfair Display" }

Create a simple spacing size:
{ "action": "create", "type": "global-size-variable", "label": "spacing-md", "value": "16px" }

Create a fluid/responsive size using a CSS function (use global-custom-size-variable, NOT global-size-variable):
{ "action": "create", "type": "global-custom-size-variable", "label": "spacing-fluid", "value": "clamp(1rem, 2vw, 2rem)" }

Create a size that is a keyword:
{ "action": "create", "type": "global-custom-size-variable", "label": "width-auto", "value": "auto" }

Create a size using calc():
{ "action": "create", "type": "global-custom-size-variable", "label": "sidebar-width", "value": "calc(100% - 32px)" }

Update a variable's value (keep exact label):
{ "action": "update", "id": "abc123", "label": "brand-primary", "value": "#0D47A1" }

Rename a variable (keep existing value):
{ "action": "update", "id": "abc123", "label": "brand-secondary", "value": "#1A73E8" }

Delete a variable:
{ "action": "delete", "id": "abc123" }
`);
  prompt.instruction('Always read [elementor://global-variables] before creating to check existing variables and avoid duplicate labels.');
  return prompt.prompt();
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/mcp/variables-resource.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/mcp/variables-resource.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GLOBAL_VARIABLES_URI: function() { return /* binding */ GLOBAL_VARIABLES_URI; },
/* harmony export */   initVariablesResource: function() { return /* binding */ initVariablesResource; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _storage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../storage */ "./packages/packages/core/editor-variables/src/storage.ts");



const GLOBAL_VARIABLES_URI = 'elementor://global-variables';
const buildGlobalVariablesPayload = async () => {
  const merged = {};
  Object.entries(_service__WEBPACK_IMPORTED_MODULE_1__.service.variables()).forEach(([id, variable]) => {
    if (!variable.deleted) {
      merged[id] = {
        ...variable,
        version: 'v4'
      };
    }
  });
  return merged;
};
const initVariablesResource = (variablesMcpEntry, canvasMcpEntry) => {
  [canvasMcpEntry, variablesMcpEntry].forEach(entry => {
    const {
      resource,
      sendResourceUpdated
    } = entry;
    const notifyGlobalVariablesUpdated = () => {
      sendResourceUpdated({
        uri: GLOBAL_VARIABLES_URI
      });
    };
    resource('global-variables', GLOBAL_VARIABLES_URI, {
      description: 'Global variables available (v4)'
    }, async () => {
      const variables = await buildGlobalVariablesPayload();
      return {
        contents: [{
          uri: GLOBAL_VARIABLES_URI,
          mimeType: 'application/json',
          text: JSON.stringify(variables)
        }]
      };
    });
    window.addEventListener(_storage__WEBPACK_IMPORTED_MODULE_2__.STORAGE_UPDATED_EVENT, notifyGlobalVariablesUpdated);
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/save/update'), notifyGlobalVariablesUpdated);
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/prop-types/color-variable-prop-type.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/prop-types/color-variable-prop-type.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorVariablePropTypeUtil: function() { return /* binding */ colorVariablePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const colorVariablePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('global-color-variable', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string());

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/prop-types/custom-size-variable-prop-type.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/prop-types/custom-size-variable-prop-type.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   customSizeVariablePropTypeUtil: function() { return /* binding */ customSizeVariablePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const customSizeVariablePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('global-custom-size-variable', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string());

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/prop-types/font-variable-prop-type.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/prop-types/font-variable-prop-type.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fontVariablePropTypeUtil: function() { return /* binding */ fontVariablePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const fontVariablePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('global-font-variable', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string());

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/prop-types/index.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/prop-types/index.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorVariablePropTypeUtil: function() { return /* reexport safe */ _color_variable_prop_type__WEBPACK_IMPORTED_MODULE_0__.colorVariablePropTypeUtil; },
/* harmony export */   customSizeVariablePropTypeUtil: function() { return /* reexport safe */ _custom_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_1__.customSizeVariablePropTypeUtil; },
/* harmony export */   fontVariablePropTypeUtil: function() { return /* reexport safe */ _font_variable_prop_type__WEBPACK_IMPORTED_MODULE_2__.fontVariablePropTypeUtil; },
/* harmony export */   sizeVariablePropTypeUtil: function() { return /* reexport safe */ _size_variable_prop_type__WEBPACK_IMPORTED_MODULE_3__.sizeVariablePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _color_variable_prop_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./color-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/color-variable-prop-type.ts");
/* harmony import */ var _custom_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./custom-size-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/custom-size-variable-prop-type.ts");
/* harmony import */ var _font_variable_prop_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./font-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/font-variable-prop-type.ts");
/* harmony import */ var _size_variable_prop_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./size-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/size-variable-prop-type.ts");





/***/ }),

/***/ "./packages/packages/core/editor-variables/src/prop-types/size-variable-prop-type.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/prop-types/size-variable-prop-type.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   sizeVariablePropTypeUtil: function() { return /* binding */ sizeVariablePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_1__);


const sizeVariablePropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('global-size-variable', _elementor_schema__WEBPACK_IMPORTED_MODULE_1__.z.string());

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/register-variable-types.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/register-variable-types.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   registerVariableTypes: function() { return /* binding */ registerVariableTypes; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _components_fields_color_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/fields/color-field */ "./packages/packages/core/editor-variables/src/components/fields/color-field.tsx");
/* harmony import */ var _components_fields_font_field__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/fields/font-field */ "./packages/packages/core/editor-variables/src/components/fields/font-field.tsx");
/* harmony import */ var _components_ui_color_indicator__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/ui/color-indicator */ "./packages/packages/core/editor-variables/src/components/ui/color-indicator.tsx");
/* harmony import */ var _prop_types_color_variable_prop_type__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./prop-types/color-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/color-variable-prop-type.ts");
/* harmony import */ var _prop_types_font_variable_prop_type__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./prop-types/font-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/font-variable-prop-type.ts");
/* harmony import */ var _prop_types_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./prop-types/size-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/size-variable-prop-type.ts");
/* harmony import */ var _transformers_empty_transformer__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./transformers/empty-transformer */ "./packages/packages/core/editor-variables/src/transformers/empty-transformer.tsx");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");














function registerVariableTypes() {
  (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_13__.registerVariableType)({
    key: _prop_types_color_variable_prop_type__WEBPACK_IMPORTED_MODULE_9__.colorVariablePropTypeUtil.key,
    valueField: _components_fields_color_field__WEBPACK_IMPORTED_MODULE_6__.ColorField,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.BrushIcon,
    propTypeUtil: _prop_types_color_variable_prop_type__WEBPACK_IMPORTED_MODULE_9__.colorVariablePropTypeUtil,
    fallbackPropTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.colorPropTypeUtil,
    variableType: 'color',
    startIcon: ({
      value
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_color_indicator__WEBPACK_IMPORTED_MODULE_8__.ColorIndicator, {
      size: "inherit",
      component: "span",
      value: value
    }),
    defaultValue: '#ffffff',
    menuActionsFactory: ({
      variable,
      variableId,
      handlers
    }) => {
      const actions = [];
      if (variable.sync_to_v3) {
        actions.push({
          name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Stop syncing to Global Colors', 'elementor'),
          icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.RefreshOffIcon,
          color: 'text.primary',
          onClick: () => handlers.onStopSync(variableId)
        });
      } else {
        actions.push({
          name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Sync to Global Colors', 'elementor'),
          icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.RefreshIcon,
          color: 'text.primary',
          onClick: () => handlers.onStartSync(variableId)
        });
      }
      return actions;
    }
  });
  (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_13__.registerVariableType)({
    key: _prop_types_font_variable_prop_type__WEBPACK_IMPORTED_MODULE_10__.fontVariablePropTypeUtil.key,
    valueField: _components_fields_font_field__WEBPACK_IMPORTED_MODULE_7__.FontField,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.TextIcon,
    propTypeUtil: _prop_types_font_variable_prop_type__WEBPACK_IMPORTED_MODULE_10__.fontVariablePropTypeUtil,
    fallbackPropTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil,
    variableType: 'font',
    defaultValue: 'Roboto'
  });
  const sizePromotions = {
    isActive: false,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_4__.ExpandDiagonalIcon,
    propTypeUtil: _prop_types_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_11__.sizeVariablePropTypeUtil,
    fallbackPropTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.sizePropTypeUtil,
    styleTransformer: _transformers_empty_transformer__WEBPACK_IMPORTED_MODULE_12__.EmptyTransformer,
    variableType: 'size',
    selectionFilter: () => [],
    emptyState: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.CtaButton, {
      size: "small",
      href: 'https://go.elementor.com/go-pro-panel-size-variable/',
      onClick: () => (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackUpgradePromotionClick)({
        target_name: 'variables_popover',
        location_l1: 'variables_list'
      })
    })
  };
  (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_13__.registerVariableType)({
    ...sizePromotions,
    key: _prop_types_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_11__.sizeVariablePropTypeUtil.key,
    defaultValue: '0px'
  });
  (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_13__.registerVariableType)({
    ...sizePromotions,
    key: 'global-custom-size-variable'
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/renderers/style-variables-renderer.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/renderers/style-variables-renderer.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleVariablesRenderer: function() { return /* binding */ StyleVariablesRenderer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _style_variables_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../style-variables-repository */ "./packages/packages/core/editor-variables/src/style-variables-repository.ts");





const VARIABLES_WRAPPER = ':root';
function StyleVariablesRenderer() {
  const container = usePortalContainer();
  const styleVariables = useStyleVariables();
  const hasVariables = Object.keys(styleVariables).length > 0;
  if (!container || !hasVariables) {
    return null;
  }
  const cssVariables = convertToCssVariables(styleVariables);
  const wrappedCss = `${VARIABLES_WRAPPER}{${cssVariables}}`;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Portal, {
    container: container
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("style", {
    "data-e-style-id": "e-variables",
    key: wrappedCss
  }, wrappedCss));
}
function usePortalContainer() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandEndEvent)('editor/documents/attach-preview'), () => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.getCanvasIframeDocument)()?.head);
}
function useStyleVariables() {
  const [variables, setVariables] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const unsubscribe = _style_variables_repository__WEBPACK_IMPORTED_MODULE_3__.styleVariablesRepository.subscribe(setVariables);
    return () => {
      unsubscribe();
    };
  }, []);
  return variables;
}
function cssVariableDeclaration(key, variable) {
  const variableName = variable?.deleted ? key : variable.label;
  const value = variable.value;
  return `--${variableName}:${value};`;
}
function convertToCssVariables(variables) {
  const listOfVariables = Object.entries(variables);
  return listOfVariables.map(([key, variable]) => cssVariableDeclaration(key, variable)).join('');
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/repeater-injections.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/repeater-injections.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   registerRepeaterInjections: function() { return /* binding */ registerRepeaterInjections; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/variables-repeater-item-slot */ "./packages/packages/core/editor-variables/src/components/variables-repeater-item-slot.tsx");
/* harmony import */ var _prop_types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./prop-types */ "./packages/packages/core/editor-variables/src/prop-types/index.ts");




function registerRepeaterInjections() {
  backgroundOverlayRepeaterInjections();
  boxShadowRepeaterInjections();
  transitionsRepeaterInjections();
  transformRepeaterInjections();
  filterRepeaterInjections();
}
function backgroundOverlayRepeaterInjections() {
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemIcon)({
    id: 'background-color-variables-icon',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.BackgroundRepeaterColorIndicator,
    condition: ({
      value
    }) => {
      return hasAssignedColorVariable(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.backgroundColorOverlayPropTypeUtil.extract(value)?.color);
    }
  });
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemLabel)({
    id: 'background-color-variables-label',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.BackgroundRepeaterLabel,
    condition: ({
      value
    }) => {
      return hasAssignedColorVariable(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.backgroundColorOverlayPropTypeUtil.extract(value)?.color);
    }
  });
}
function boxShadowRepeaterInjections() {
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemIcon)({
    id: 'box-shadow-color-variables-icon',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.BoxShadowRepeaterColorIndicator,
    condition: ({
      value
    }) => {
      const {
        color
      } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.shadowPropTypeUtil.extract(value) || {};
      return hasAssignedColorVariable(color);
    }
  });
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemLabel)({
    id: 'color-variables-box-shadow-label',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.BoxShadowRepeaterLabel,
    condition: ({
      value
    }) => {
      const {
        hOffset,
        vOffset,
        blur,
        spread
      } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.shadowPropTypeUtil.extract(value) || {};
      return hasAssignedSizeVariable(hOffset) || hasAssignedSizeVariable(vOffset) || hasAssignedSizeVariable(blur) || hasAssignedSizeVariable(spread);
    }
  });
}
function transformRepeaterInjections() {
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemLabel)({
    id: 'transform-size-variables-label',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.TransformRepeaterLabel,
    condition: ({
      value
    }) => {
      if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.moveTransformPropTypeUtil.isValid(value)) {
        const {
          x: xAxis,
          y: yAxis,
          z: zAxis
        } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.moveTransformPropTypeUtil.extract(value) || {};
        return hasAssignedSizeVariable(xAxis) || hasAssignedSizeVariable(yAxis) || hasAssignedSizeVariable(zAxis);
      }
      return false;
    }
  });
}
function transitionsRepeaterInjections() {
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemLabel)({
    id: 'transition-size-variables-label',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.TransitionsSizeVariableLabel,
    condition: ({
      value
    }) => {
      return hasAssignedSizeVariable(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.selectionSizePropTypeUtil.extract(value)?.size);
    }
  });
}
function filterRepeaterInjections() {
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemIcon)({
    id: 'filters-color-variables-icon',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.FilterDropShadowIconIndicator,
    condition: ({
      value
    }) => {
      if (!_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.isValid(value)) {
        return false;
      }
      const args = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.extract(value)?.args;
      if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.isValid(args)) {
        return hasAssignedColorVariable(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.extract(args)?.color);
      }
      return false;
    }
  });
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemLabel)({
    id: 'filters-drop-shadow-size-variables-label',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.FilterDropShadowRepeaterLabel,
    condition: ({
      value
    }) => {
      if (!_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.isValid(value)) {
        return false;
      }
      const args = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.extract(value)?.args;
      if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.isValid(args)) {
        const {
          xAxis,
          yAxis,
          blur
        } = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dropShadowFilterPropTypeUtil.extract(args) || {};
        return hasAssignedSizeVariable(xAxis) || hasAssignedSizeVariable(yAxis) || hasAssignedSizeVariable(blur);
      }
      return false;
    }
  });
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.injectIntoRepeaterItemLabel)({
    id: 'filters-size-variables-label',
    component: _components_variables_repeater_item_slot__WEBPACK_IMPORTED_MODULE_2__.FilterSingleSizeRepeaterLabel,
    condition: ({
      value
    }) => {
      if (!_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.isValid(value)) {
        return false;
      }
      const args = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.cssFilterFunctionPropUtil.extract(value)?.args;
      return hasAssignedSizeVariable(args?.value?.size);
    }
  });
}
function hasAssignedSizeVariable(value) {
  if (_prop_types__WEBPACK_IMPORTED_MODULE_3__.sizeVariablePropTypeUtil.isValid(value)) {
    return true;
  }
  if (_prop_types__WEBPACK_IMPORTED_MODULE_3__.customSizeVariablePropTypeUtil.isValid(value)) {
    return true;
  }
  return false;
}
function hasAssignedColorVariable(value) {
  return !!_prop_types__WEBPACK_IMPORTED_MODULE_3__.colorVariablePropTypeUtil.isValid(value);
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/service.ts":
/*!****************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/service.ts ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   service: function() { return /* binding */ service; }
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ "./packages/packages/core/editor-variables/src/api.ts");
/* harmony import */ var _batch_operations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./batch-operations */ "./packages/packages/core/editor-variables/src/batch-operations.ts");
/* harmony import */ var _storage__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./storage */ "./packages/packages/core/editor-variables/src/storage.ts");
/* harmony import */ var _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./style-variables-repository */ "./packages/packages/core/editor-variables/src/style-variables-repository.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");






const storage = new _storage__WEBPACK_IMPORTED_MODULE_3__.Storage();
const service = {
  variables: () => {
    return storage.load();
  },
  findIdByLabel(needle) {
    const variableId = Object.entries(this.variables()).find(([, variable]) => variable.label === needle);
    if (!variableId) {
      throw new Error(`Variable with label ${needle} not found`);
    }
    return variableId[0];
  },
  findVariableByLabel(needle) {
    return Object.values(this.variables()).find(variable => variable.label === needle) || null;
  },
  getWatermark: () => {
    return storage.state.watermark;
  },
  init: () => {
    return service.load();
  },
  load: () => {
    return _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.list().then(response => {
      const {
        success,
        data: payload
      } = response.data;
      if (!success) {
        throw new Error('Unexpected response from server');
      }
      return payload;
    }).then(data => {
      const {
        variables,
        watermark
      } = data;
      storage.fill(variables, watermark);
      _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__.styleVariablesRepository.update(variables);
      return variables;
    });
  },
  create: ({
    type,
    label,
    value
  }, options = {}) => {
    return _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.create(type, label, value).then(response => {
      const {
        success,
        data: payload
      } = response.data;
      if (!success) {
        const errorMessage = payload?.message || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Unexpected response from server', 'elementor');
        throw new Error(errorMessage);
      }
      return payload;
    }).then(data => {
      const {
        variable,
        watermark
      } = data;
      handleWatermark(_storage__WEBPACK_IMPORTED_MODULE_3__.OP_RW, watermark);
      const {
        id: variableId,
        ...createdVariable
      } = variable;
      storage.add(variableId, createdVariable);
      _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__.styleVariablesRepository.update({
        [variableId]: createdVariable
      });
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_5__.trackVariableEvent)({
        varType: type,
        action: 'save',
        ...options.eventData
      });
      return {
        id: variableId,
        variable: createdVariable
      };
    });
  },
  update: (id, {
    label,
    value,
    type
  }, options = {}) => {
    return _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.update(id, label, value, type).then(response => {
      const {
        success,
        data: payload
      } = response.data;
      if (!success) {
        const errorMessage = payload?.message || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Unexpected response from server', 'elementor');
        throw new Error(errorMessage);
      }
      return payload;
    }).then(data => {
      const {
        variable,
        watermark
      } = data;
      handleWatermark(_storage__WEBPACK_IMPORTED_MODULE_3__.OP_RW, watermark);
      const {
        id: variableId,
        ...updatedVariable
      } = variable;
      storage.update(variableId, updatedVariable);
      _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__.styleVariablesRepository.update({
        [variableId]: updatedVariable
      });
      (0,_utils_tracking__WEBPACK_IMPORTED_MODULE_5__.trackVariableEvent)({
        varType: updatedVariable.type,
        action: 'update',
        ...options.eventData
      });
      return {
        id: variableId,
        variable: updatedVariable
      };
    });
  },
  delete: id => {
    return _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.delete(id).then(response => {
      const {
        success,
        data: payload
      } = response.data;
      if (!success) {
        throw new Error('Unexpected response from server');
      }
      return payload;
    }).then(data => {
      const {
        variable,
        watermark
      } = data;
      handleWatermark(_storage__WEBPACK_IMPORTED_MODULE_3__.OP_RW, watermark);
      const {
        id: variableId,
        ...deletedVariable
      } = variable;
      storage.update(variableId, deletedVariable);
      _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__.styleVariablesRepository.update({
        [variableId]: deletedVariable
      });
      return {
        id: variableId,
        variable: deletedVariable
      };
    });
  },
  restore: (id, label, value, type) => {
    return _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.restore(id, label, value, type).then(response => {
      const {
        success,
        data: payload
      } = response.data;
      if (!success) {
        throw new Error('Unexpected response from server');
      }
      return payload;
    }).then(data => {
      const {
        variable,
        watermark
      } = data;
      handleWatermark(_storage__WEBPACK_IMPORTED_MODULE_3__.OP_RW, watermark);
      const {
        id: variableId,
        ...restoredVariable
      } = variable;
      storage.update(variableId, restoredVariable);
      _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__.styleVariablesRepository.update({
        [variableId]: restoredVariable
      });
      return {
        id: variableId,
        variable: restoredVariable
      };
    });
  },
  batchSave: (originalVariables, currentVariables, deletedVariables) => {
    const operations = (0,_batch_operations__WEBPACK_IMPORTED_MODULE_2__.buildOperationsArray)(originalVariables, currentVariables, deletedVariables);
    const batchPayload = {
      operations,
      watermark: storage.state.watermark
    };
    if (operations.length === 0) {
      return Promise.resolve({
        success: true,
        watermark: storage.state.watermark,
        operations: 0
      });
    }
    return _api__WEBPACK_IMPORTED_MODULE_1__.apiClient.batch(batchPayload).then(response => {
      const {
        success,
        data: payload
      } = response.data;
      if (!success) {
        throw new Error('Unexpected response from server');
      }
      return payload;
    }).then(data => {
      const {
        results,
        watermark
      } = data;
      handleWatermark(_storage__WEBPACK_IMPORTED_MODULE_3__.OP_RW, watermark);
      if (results) {
        results.forEach(result => {
          const variableId = result.id;
          if (result.variable) {
            if (result.type === 'create') {
              storage.add(variableId, result.variable);
            } else {
              storage.update(variableId, result.variable);
            }
            _style_variables_repository__WEBPACK_IMPORTED_MODULE_4__.styleVariablesRepository.update({
              [variableId]: result.variable
            });
          }
        });
      }
      return {
        success: true,
        watermark,
        operations: operations.length
      };
    });
  }
};
const handleWatermark = (operation, newWatermark) => {
  if (storage.watermarkDiff(operation, newWatermark)) {
    setTimeout(() => service.load(), 500);
  }
  storage.watermark(newWatermark);
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/storage.ts":
/*!****************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/storage.ts ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OP_RW: function() { return /* binding */ OP_RW; },
/* harmony export */   STORAGE_UPDATED_EVENT: function() { return /* binding */ STORAGE_UPDATED_EVENT; },
/* harmony export */   Storage: function() { return /* binding */ Storage; }
/* harmony export */ });
const STORAGE_KEY = 'elementor-global-variables';
const STORAGE_WATERMARK_KEY = 'elementor-global-variables-watermark';
const STORAGE_UPDATED_EVENT = 'variables:updated';
const OP_RW = 'RW';
const OP_RO = 'RO';
class Storage {
  notifyChange() {
    window.dispatchEvent(new Event(STORAGE_UPDATED_EVENT));
  }
  constructor() {
    this.state = {
      watermark: -1,
      variables: {}
    };
  }
  load() {
    this.state.watermark = parseInt(localStorage.getItem(STORAGE_WATERMARK_KEY) || '-1');
    this.state.variables = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    return this.state.variables;
  }
  fill(variables, watermark) {
    this.state.variables = {};
    if (variables && Object.keys(variables).length) {
      this.state.variables = variables;
    }
    this.state.watermark = watermark;
    localStorage.setItem(STORAGE_WATERMARK_KEY, this.state.watermark.toString());
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state.variables));
    this.notifyChange();
  }
  add(id, variable) {
    this.load();
    this.state.variables[id] = variable;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state.variables));
    this.notifyChange();
  }
  update(id, variable) {
    this.load();
    this.state.variables[id] = variable;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state.variables));
    this.notifyChange();
  }
  watermark(watermark) {
    this.state.watermark = watermark;
    localStorage.setItem(STORAGE_WATERMARK_KEY, this.state.watermark.toString());
  }
  watermarkDiff(operation, newWatermark) {
    const diff = newWatermark - this.state.watermark;
    if (OP_RW === operation) {
      return 1 !== diff;
    }
    if (OP_RO === operation) {
      return 0 !== diff;
    }
    return false;
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/style-variables-repository.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/style-variables-repository.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   styleVariablesRepository: function() { return /* binding */ styleVariablesRepository; }
/* harmony export */ });
/* harmony import */ var _create_style_variables_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create-style-variables-repository */ "./packages/packages/core/editor-variables/src/create-style-variables-repository.ts");

const styleVariablesRepository = (0,_create_style_variables_repository__WEBPACK_IMPORTED_MODULE_0__.createStyleVariablesRepository)();

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/sync/get-font-configs.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/sync/get-font-configs.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getFontConfigs: function() { return /* binding */ getFontConfigs; }
/* harmony export */ });
const getFontConfigs = () => {
  return window.elementor?.config?.controls?.font?.options ?? {};
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/transformers/empty-transformer.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/transformers/empty-transformer.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EmptyTransformer: function() { return /* binding */ EmptyTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);


// eslint-disable-next-line @typescript-eslint/no-unused-vars
const EmptyTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(_value => {
  return null;
});

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/transformers/inheritance-transformer.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/transformers/inheritance-transformer.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   inheritanceTransformer: function() { return /* binding */ inheritanceTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_ui_color_indicator__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/ui/color-indicator */ "./packages/packages/core/editor-variables/src/components/ui/color-indicator.tsx");
/* harmony import */ var _prop_types_color_variable_prop_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../prop-types/color-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/color-variable-prop-type.ts");
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _utils_resolve_css_variable__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/resolve-css-variable */ "./packages/packages/core/editor-variables/src/transformers/utils/resolve-css-variable.ts");








const inheritanceTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(id => {
  const variables = _service__WEBPACK_IMPORTED_MODULE_6__.service.variables();
  const variable = variables[id];
  if (!variable) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Missing variable', 'elementor'));
  }
  const showColorIndicator = variable.type === _prop_types_color_variable_prop_type__WEBPACK_IMPORTED_MODULE_5__.colorVariablePropTypeUtil.key;
  const css = (0,_utils_resolve_css_variable__WEBPACK_IMPORTED_MODULE_7__.resolveCssVariable)(id, variable);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    spacing: 0.5,
    sx: {
      paddingInline: '1px'
    },
    alignItems: "center"
  }, showColorIndicator && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_color_indicator__WEBPACK_IMPORTED_MODULE_4__.ColorIndicator, {
    size: "inherit",
    value: variable.value
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    overflow: "hidden",
    whiteSpace: "nowrap",
    textOverflow: "ellipsis"
  }, css));
});

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/transformers/utils/resolve-css-variable.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/transformers/utils/resolve-css-variable.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   resolveCssVariable: function() { return /* binding */ resolveCssVariable; }
/* harmony export */ });
const resolveCssVariable = (id, variable) => {
  let name = id;
  let fallbackValue = '';
  if (variable) {
    fallbackValue = variable.value;
  }
  if (variable && !variable.deleted) {
    name = variable.label;
  }
  if (!name.trim()) {
    return null;
  }
  const validCssVariableName = `--${name}`;
  if (!fallbackValue.trim()) {
    return `var(${validCssVariableName})`;
  }
  return `var(${validCssVariableName}, ${fallbackValue})`;
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/transformers/variable-transformer.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/transformers/variable-transformer.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   variableTransformer: function() { return /* binding */ variableTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _utils_resolve_css_variable__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/resolve-css-variable */ "./packages/packages/core/editor-variables/src/transformers/utils/resolve-css-variable.ts");



const variableTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)((idOrLabel, {
  key
}) => {
  const variables = _service__WEBPACK_IMPORTED_MODULE_1__.service.variables();
  const targetVariable = variables[idOrLabel] || _service__WEBPACK_IMPORTED_MODULE_1__.service.findVariableByLabel(idOrLabel);
  if (!targetVariable) {
    return null;
  }
  if ((0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.isGridTrackProperty)(key)) {
    const count = parseInt((targetVariable.value ?? '').trim(), 10);
    return (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.formatGridTrackRepeat)(count);
  }
  const id = _service__WEBPACK_IMPORTED_MODULE_1__.service.findIdByLabel(targetVariable.label);
  return (0,_utils_resolve_css_variable__WEBPACK_IMPORTED_MODULE_2__.resolveCssVariable)(id, targetVariable);
});

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/duplicate-label.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/duplicate-label.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COPY_SUFFIX: function() { return /* binding */ COPY_SUFFIX; },
/* harmony export */   generateDuplicateLabel: function() { return /* binding */ generateDuplicateLabel; }
/* harmony export */ });
/* harmony import */ var _validations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./validations */ "./packages/packages/core/editor-variables/src/utils/validations.ts");

const COPY_SUFFIX = '-Copy';
const trimToFit = (base, suffix) => {
  const combined = base + suffix;
  if (combined.length <= _validations__WEBPACK_IMPORTED_MODULE_0__.VARIABLE_LABEL_MAX_LENGTH) {
    return combined;
  }
  return base.slice(0, _validations__WEBPACK_IMPORTED_MODULE_0__.VARIABLE_LABEL_MAX_LENGTH - suffix.length) + suffix;
};
const generateDuplicateLabel = (originalLabel, existingLabels) => {
  const labelsSet = new Set(existingLabels);
  const firstCandidate = trimToFit(originalLabel, COPY_SUFFIX);
  if (!labelsSet.has(firstCandidate)) {
    return firstCandidate;
  }
  for (let i = 2; i <= labelsSet.size + 1; i++) {
    const candidate = trimToFit(originalLabel, `${COPY_SUFFIX}-${i}`);
    if (!labelsSet.has(candidate)) {
      return candidate;
    }
  }
  return firstCandidate;
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/extract-variables-from-style-value.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/extract-variables-from-style-value.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extractVariablesFromStyleValue: function() { return /* binding */ extractVariablesFromStyleValue; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);

const VARIABLE_TYPE_KEYS = ['global-color-variable', 'global-font-variable', 'global-size-variable', 'global-custom-size-variable'];
function tryExtractVariable(value) {
  for (const key of VARIABLE_TYPE_KEYS) {
    const propUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.getPropSchemaFromCache)(key);
    if (propUtil?.isValid(value)) {
      return {
        type: key,
        variableId: propUtil.extract(value)
      };
    }
  }
  return null;
}
function traverse(value, path, result) {
  const extracted = tryExtractVariable(value);
  if (extracted) {
    result.push({
      ...extracted,
      controlPath: path.join('.')
    });
    return;
  }
  if ((0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(value)) {
    traverse(value.value, path, result);
    return;
  }
  if (value && typeof value === 'object') {
    for (const [key, val] of Object.entries(value)) {
      traverse(val, [...path, key], result);
    }
  }
}
function extractVariablesFromStyleValue(styleValue) {
  const result = [];
  traverse(styleValue, [], result);
  return result;
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/filter-by-search.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/filter-by-search.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   filterBySearch: function() { return /* binding */ filterBySearch; }
/* harmony export */ });
function filterBySearch(variables, searchValue) {
  const lowerSearchValue = searchValue.toLowerCase();
  return variables.filter(variable => variable.label.toLowerCase().includes(lowerSearchValue));
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/llm-propvalue-label-resolver.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/llm-propvalue-label-resolver.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   globalVariablesLLMResolvers: function() { return /* binding */ globalVariablesLLMResolvers; }
/* harmony export */ });
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../service */ "./packages/packages/core/editor-variables/src/service.ts");

const defaultResolver = key => value => {
  const idOrLabel = String(value);
  return {
    $$type: key,
    value: _service__WEBPACK_IMPORTED_MODULE_0__.service.variables()[idOrLabel] ? idOrLabel : _service__WEBPACK_IMPORTED_MODULE_0__.service.findIdByLabel(idOrLabel)
  };
};
const globalVariablesLLMResolvers = {
  'global-color-variable': defaultResolver('global-color-variable'),
  'global-font-variable': defaultResolver('global-font-variable'),
  'global-size-variable': defaultResolver('global-size-variable')
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/size-value.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/size-value.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   sizeValue: function() { return /* binding */ sizeValue; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _prop_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../prop-types */ "./packages/packages/core/editor-variables/src/prop-types/index.ts");



const DEFAULT_UNIT = 'px';
const CUSTOM_SIZE_LABEL = 'fx';
function sizeValue(value) {
  if (_prop_types__WEBPACK_IMPORTED_MODULE_2__.sizeVariablePropTypeUtil.isValid(value) || _prop_types__WEBPACK_IMPORTED_MODULE_2__.customSizeVariablePropTypeUtil.isValid(value)) {
    const variable = (0,_hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_1__.getVariable)(value?.value);
    return variable?.value;
  }
  if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.sizePropTypeUtil.isValid(value)) {
    const {
      size,
      unit
    } = value.value;
    if ('custom' !== unit) {
      return `${size ?? 0}${unit ?? DEFAULT_UNIT}`;
    }
    if (!size) {
      return CUSTOM_SIZE_LABEL;
    }
    return size;
  }
  return '';
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/tracking.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/tracking.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   trackVariableEvent: function() { return /* binding */ trackVariableEvent; },
/* harmony export */   trackVariableSyncToV3: function() { return /* binding */ trackVariableSyncToV3; },
/* harmony export */   trackVariablesManagerEvent: function() { return /* binding */ trackVariablesManagerEvent; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);

const trackVariableEvent = ({
  varType,
  controlPath,
  action,
  executedBy
}) => {
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  if (!config?.names?.variables?.[action]) {
    return;
  }
  const name = config.names.variables[action];
  let eventData = {
    var_type: varType,
    action_type: name
  };
  if (executedBy) {
    eventData.executed_by = executedBy;
  }
  const defaultLocationInfo = {
    location: config?.locations?.variables || '',
    secondaryLocation: config?.secondaryLocations?.variablesPopover || '',
    trigger: config?.triggers?.click || ''
  };
  if (!executedBy || executedBy !== 'mcp_tool') {
    eventData = {
      ...defaultLocationInfo,
      ...eventData
    };
  }
  if (controlPath) {
    eventData.control_path = controlPath;
  }
  dispatchEvent?.(name, eventData);
};
const trackVariablesManagerEvent = ({
  action,
  source,
  varType,
  controlPath
}) => {
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  if (!config?.names?.variables?.[action]) {
    return;
  }
  const name = config.names.variables[action];
  const eventData = {
    location: config?.locations?.variablesManager || '',
    trigger: config?.triggers?.click || '',
    action_type: name
  };
  if (source) {
    eventData.source = source;
  }
  if (varType) {
    eventData.var_type = varType;
  }
  if (controlPath) {
    eventData.style_control_path = controlPath;
  }
  dispatchEvent?.(name, eventData);
};
const trackVariableSyncToV3 = ({
  variableLabel,
  action
}) => {
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  if (!config?.names?.variables?.variableSyncToV3) {
    return;
  }
  const name = config.names.variables.variableSyncToV3;
  const isSync = action === 'sync';
  dispatchEvent?.(name, {
    interaction_type: 'click',
    target_type: variableLabel,
    target_name: isSync ? 'sync_to_v3' : 'unsync_to_v3',
    interaction_result: isSync ? 'var_is_synced_to_V3' : 'var_is_unsynced_from_V3',
    target_location: 'widget_panel',
    location_l1: 'var_manager',
    interaction_description: isSync ? `user_synced_${variableLabel}_to_v3` : `user_unsync_${variableLabel}_from_v3`
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/unlink-variable.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/unlink-variable.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createUnlinkHandler: function() { return /* binding */ createUnlinkHandler; },
/* harmony export */   transformValueBeforeUnlink: function() { return /* binding */ transformValueBeforeUnlink; }
/* harmony export */ });
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");

function transformValueBeforeUnlink(variable, propTypeKey) {
  const {
    valueTransformer
  } = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_0__.getVariableType)(propTypeKey);
  if (valueTransformer) {
    return valueTransformer(variable.value, variable.type);
  }
  return variable.value;
}
function createUnlinkHandler(variable, propTypeKey, setValue) {
  return () => {
    const {
      fallbackPropTypeUtil
    } = (0,_variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_0__.getVariableType)(propTypeKey);
    const transformedValue = transformValueBeforeUnlink(variable, propTypeKey);
    setValue(fallbackPropTypeUtil.create(transformedValue));
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/validations.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/validations.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ERROR_MESSAGES: function() { return /* binding */ ERROR_MESSAGES; },
/* harmony export */   VARIABLE_LABEL_MAX_LENGTH: function() { return /* binding */ VARIABLE_LABEL_MAX_LENGTH; },
/* harmony export */   labelHint: function() { return /* binding */ labelHint; },
/* harmony export */   mapServerError: function() { return /* binding */ mapServerError; },
/* harmony export */   validateLabel: function() { return /* binding */ validateLabel; },
/* harmony export */   validateValue: function() { return /* binding */ validateValue; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);


const ERROR_MESSAGES = {
  MISSING_VARIABLE_NAME: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Give your variable a name.', 'elementor'),
  MISSING_VARIABLE_VALUE: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Add a value to complete your variable.', 'elementor'),
  INVALID_CHARACTERS: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Use letters, numbers, dashes (-), or underscores (_) for the name.', 'elementor'),
  NO_NON_SPECIAL_CHARACTER: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Names have to include at least one non-special character.', 'elementor'),
  VARIABLE_LABEL_MAX_LENGTH: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Keep names up to 50 characters.', 'elementor'),
  DUPLICATED_LABEL: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('This variable name already exists. Please choose a unique name.', 'elementor'),
  UNEXPECTED_ERROR: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('There was a glitch. Try saving your variable again.', 'elementor'),
  BATCH: {
    DUPLICATED_LABELS: (count, name) =>
    // eslint-disable-next-line @wordpress/i18n-translator-comments
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('We found %1$d duplicated %2$s.', 'elementor'), count, name),
    UNEXPECTED_ERROR: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('There was a glitch.', 'elementor'),
    DUPLICATED_LABEL_ACTION: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Take me there', 'elementor'),
    DUPLICATED_LABEL_ACTION_MESSAGE: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Please rename the variables.', 'elementor'),
    UNEXPECTED_ERROR_ACTION_MESSAGE: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Try saving your variables again.', 'elementor')
  }
};
const VARIABLE_LABEL_MAX_LENGTH = 50;
const mapServerError = error => {
  if (error?.response?.data?.code === 'duplicated_label') {
    return {
      field: 'label',
      message: ERROR_MESSAGES.DUPLICATED_LABEL
    };
  }
  if (error?.response?.data?.code === 'batch_duplicated_label') {
    const errorData = error?.response?.data?.data ?? {};
    const count = Object.keys(errorData).length;
    const name = count === 1 ? 'name' : 'names';
    const duplicatedIds = Object.keys(errorData);
    return {
      field: 'label',
      message: ERROR_MESSAGES.BATCH.DUPLICATED_LABELS(count, name),
      severity: 'error',
      IconComponent: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.AlertTriangleFilledIcon,
      action: {
        label: ERROR_MESSAGES.BATCH.DUPLICATED_LABEL_ACTION,
        message: ERROR_MESSAGES.BATCH.DUPLICATED_LABEL_ACTION_MESSAGE,
        data: {
          duplicatedIds
        }
      }
    };
  }
  if (error?.response?.data?.code === 'batch_operation_failed') {
    return {
      field: 'label',
      message: ERROR_MESSAGES.BATCH.UNEXPECTED_ERROR,
      severity: 'secondary',
      IconComponent: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.InfoCircleFilledIcon,
      action: {
        message: ERROR_MESSAGES.BATCH.UNEXPECTED_ERROR_ACTION_MESSAGE
      }
    };
  }
  return undefined;
};
const validateLabel = (name, variables) => {
  if (!name.trim()) {
    return ERROR_MESSAGES.MISSING_VARIABLE_NAME;
  }
  const allowedChars = /^[a-zA-Z0-9_-]+$/;
  if (!allowedChars.test(name)) {
    return ERROR_MESSAGES.INVALID_CHARACTERS;
  }
  const hasAlphanumeric = /[a-zA-Z0-9]/;
  if (!hasAlphanumeric.test(name)) {
    return ERROR_MESSAGES.NO_NON_SPECIAL_CHARACTER;
  }
  if (VARIABLE_LABEL_MAX_LENGTH < name.length) {
    return ERROR_MESSAGES.VARIABLE_LABEL_MAX_LENGTH;
  }
  if (Object.values(variables ?? {}).some(variable => variable.label === name)) {
    return ERROR_MESSAGES.DUPLICATED_LABEL;
  }
  return '';
};
const labelHint = name => {
  const hintThreshold = VARIABLE_LABEL_MAX_LENGTH * 0.8 - 1;
  if (hintThreshold < name.length) {
    return ERROR_MESSAGES.VARIABLE_LABEL_MAX_LENGTH;
  }
  return '';
};
const validateValue = value => {
  if (!value.trim()) {
    return ERROR_MESSAGES.MISSING_VARIABLE_VALUE;
  }
  return '';
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/utils/variables-to-list.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/utils/variables-to-list.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   applySelectionFilters: function() { return /* binding */ applySelectionFilters; },
/* harmony export */   toNormalizedVariable: function() { return /* binding */ toNormalizedVariable; },
/* harmony export */   variablesToList: function() { return /* binding */ variablesToList; }
/* harmony export */ });
const variablesToList = variables => {
  return Object.entries(variables).map(([key, variable]) => ({
    key,
    ...variable
  }));
};
const toNormalizedVariable = ({
  key,
  label,
  value,
  order,
  sync_to_v3: syncToV3
}) => ({
  key,
  label,
  value,
  order,
  sync_to_v3: syncToV3
});
const applySelectionFilters = (variables, variableTypes) => {
  const grouped = {};
  variables.forEach(item => (grouped[item.type] ??= []).push(item));
  return Object.entries(grouped).flatMap(([type, vars]) => {
    const filter = variableTypes[type]?.selectionFilter;
    const normalized = vars.map(toNormalizedVariable);
    return (filter?.(normalized) ?? normalized).map(v => ({
      ...v,
      type
    }));
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/variables-registry/create-variable-type-registry.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/variables-registry/create-variable-type-registry.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createVariableTypeRegistry: function() { return /* binding */ createVariableTypeRegistry; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _transformers_inheritance_transformer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../transformers/inheritance-transformer */ "./packages/packages/core/editor-variables/src/transformers/inheritance-transformer.tsx");
/* harmony import */ var _transformers_variable_transformer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../transformers/variable-transformer */ "./packages/packages/core/editor-variables/src/transformers/variable-transformer.ts");



function createVariableTypeRegistry() {
  const variableTypes = {};
  const registerVariableType = ({
    key,
    icon,
    startIcon,
    valueField,
    propTypeUtil,
    variableType,
    defaultValue,
    selectionFilter,
    valueTransformer,
    styleTransformer,
    fallbackPropTypeUtil,
    isCompatible,
    emptyState,
    isActive = true,
    menuActionsFactory
  }) => {
    const variableTypeKey = key ?? propTypeUtil.key;
    if (!isCompatible) {
      isCompatible = (propType, variable) => {
        if ('union' === propType.kind) {
          if (variable.type in propType.prop_types) {
            return true;
          }
        }
        return false;
      };
    }
    variableTypes[variableTypeKey] = {
      icon,
      startIcon,
      valueField,
      propTypeUtil,
      variableType,
      defaultValue,
      selectionFilter,
      valueTransformer,
      fallbackPropTypeUtil,
      isCompatible,
      emptyState,
      isActive,
      menuActionsFactory
    };
    registerTransformer(propTypeUtil.key, styleTransformer);
    registerInheritanceTransformer(propTypeUtil.key);
  };
  const registerTransformer = (key, transformer) => {
    _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.styleTransformersRegistry.register(key, transformer ?? _transformers_variable_transformer__WEBPACK_IMPORTED_MODULE_2__.variableTransformer);
  };
  const registerInheritanceTransformer = key => {
    _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register(key, _transformers_inheritance_transformer__WEBPACK_IMPORTED_MODULE_1__.inheritanceTransformer);
  };
  const getVariableType = key => {
    return variableTypes[key];
  };
  const getVariableTypes = () => {
    return variableTypes;
  };
  const hasVariableType = key => {
    return key in variableTypes && !!variableTypes[key].isActive;
  };
  return {
    registerVariableType,
    getVariableType,
    getVariableTypes,
    hasVariableType
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getMenuActionsForVariable: function() { return /* binding */ getMenuActionsForVariable; },
/* harmony export */   getVariableType: function() { return /* binding */ getVariableType; },
/* harmony export */   getVariableTypes: function() { return /* binding */ getVariableTypes; },
/* harmony export */   hasVariableType: function() { return /* binding */ hasVariableType; },
/* harmony export */   registerVariableType: function() { return /* binding */ registerVariableType; }
/* harmony export */ });
/* harmony import */ var _create_variable_type_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create-variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/create-variable-type-registry.ts");

const {
  registerVariableType,
  getVariableType,
  getVariableTypes,
  hasVariableType
} = (0,_create_variable_type_registry__WEBPACK_IMPORTED_MODULE_0__.createVariableTypeRegistry)();
function getMenuActionsForVariable(variableType, context) {
  const typeOptions = getVariableType(variableType);
  if (typeOptions?.menuActionsFactory) {
    return typeOptions.menuActionsFactory(context);
  }
  return [];
}

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

/***/ "@elementor/editor-props":
/*!**********************************************!*\
  !*** external ["elementorV2","editorProps"] ***!
  \**********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorProps"];

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

/***/ "@elementor/menus":
/*!****************************************!*\
  !*** external ["elementorV2","menus"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["menus"];

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

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
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-variables/src/index.ts ***!
  \**************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GLOBAL_VARIABLES_URI: function() { return /* reexport safe */ _mcp_variables_resource__WEBPACK_IMPORTED_MODULE_2__.GLOBAL_VARIABLES_URI; },
/* harmony export */   Utils: function() { return /* binding */ Utils; },
/* harmony export */   VariablesManagerPanelEmbedded: function() { return /* reexport safe */ _components_variables_manager_variables_manager_panel__WEBPACK_IMPORTED_MODULE_0__.VariablesManagerPanelEmbedded; },
/* harmony export */   getMenuActionsForVariable: function() { return /* reexport safe */ _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__.getMenuActionsForVariable; },
/* harmony export */   hasVariable: function() { return /* reexport safe */ _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__.hasVariable; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_1__.init; },
/* harmony export */   registerVariableType: function() { return /* reexport safe */ _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__.registerVariableType; },
/* harmony export */   registerVariableTypes: function() { return /* reexport safe */ _register_variable_types__WEBPACK_IMPORTED_MODULE_4__.registerVariableTypes; },
/* harmony export */   service: function() { return /* reexport safe */ _service__WEBPACK_IMPORTED_MODULE_5__.service; },
/* harmony export */   sizeVariablePropTypeUtil: function() { return /* reexport safe */ _prop_types_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_3__.sizeVariablePropTypeUtil; },
/* harmony export */   trackVariablesManagerEvent: function() { return /* reexport safe */ _utils_tracking__WEBPACK_IMPORTED_MODULE_8__.trackVariablesManagerEvent; }
/* harmony export */ });
/* harmony import */ var _components_variables_manager_variables_manager_panel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/variables-manager/variables-manager-panel */ "./packages/packages/core/editor-variables/src/components/variables-manager/variables-manager-panel.tsx");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-variables/src/init.ts");
/* harmony import */ var _mcp_variables_resource__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./mcp/variables-resource */ "./packages/packages/core/editor-variables/src/mcp/variables-resource.ts");
/* harmony import */ var _prop_types_size_variable_prop_type__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./prop-types/size-variable-prop-type */ "./packages/packages/core/editor-variables/src/prop-types/size-variable-prop-type.ts");
/* harmony import */ var _register_variable_types__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./register-variable-types */ "./packages/packages/core/editor-variables/src/register-variable-types.tsx");
/* harmony import */ var _service__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./service */ "./packages/packages/core/editor-variables/src/service.ts");
/* harmony import */ var _variables_registry_variable_type_registry__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./variables-registry/variable-type-registry */ "./packages/packages/core/editor-variables/src/variables-registry/variable-type-registry.ts");
/* harmony import */ var _hooks_use_prop_variables__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./hooks/use-prop-variables */ "./packages/packages/core/editor-variables/src/hooks/use-prop-variables.ts");
/* harmony import */ var _utils_tracking__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/tracking */ "./packages/packages/core/editor-variables/src/utils/tracking.ts");
/* harmony import */ var _utils_llm_propvalue_label_resolver__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils/llm-propvalue-label-resolver */ "./packages/packages/core/editor-variables/src/utils/llm-propvalue-label-resolver.ts");










const Utils = {
  globalVariablesLLMResolvers: _utils_llm_propvalue_label_resolver__WEBPACK_IMPORTED_MODULE_9__.globalVariablesLLMResolvers
};
}();
(window.elementorV2 = window.elementorV2 || {}).editorVariables = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorVariables?.init?.();
//# sourceMappingURL=editor-variables.js.map
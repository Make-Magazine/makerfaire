/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-editing-panel/src/apply-unapply-actions.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/apply-unapply-actions.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   doApplyClasses: function() { return /* binding */ doApplyClasses; },
/* harmony export */   doGetAppliedClasses: function() { return /* binding */ doGetAppliedClasses; },
/* harmony export */   doUnapplyClass: function() { return /* binding */ doUnapplyClass; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__);





// Externalized for use outside of Hooks

function doGetAppliedClasses(elementId, classesPropType = 'classes') {
  return (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementSetting)(elementId, classesPropType)?.value || [];
}
function doApplyClasses(elementId, classIds, classesPropType = 'classes') {
  (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.updateElementSettings)({
    id: elementId,
    props: {
      [classesPropType]: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.classesPropTypeUtil.create(classIds)
    },
    withHistory: false
  });
  (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_0__.setDocumentModifiedStatus)(true);
  ensureClassesAreLoaded(classIds);
}
function ensureClassesAreLoaded(classIds) {
  const providers = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.stylesRepository.getProviders();
  classIds.forEach(classId => {
    _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.stylesRepository.getProviderByKey(classId)?.actions.get(classId);
    const owningProvider = providers.find(provider => provider.actions.all().some(style => style.id === classId));
    try {
      // this is essentially to enforce the loading of a class if we have async lazy-loading style providers, e.g. global classes
      owningProvider?.actions.get(classId);
    } catch {}
  });
}
function doUnapplyClass(elementId, classId, classesPropType = 'classes') {
  const appliedClasses = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementSetting)(elementId, classesPropType)?.value || [];
  if (!appliedClasses.includes(classId)) {
    return false;
  }
  const updatedClassIds = appliedClasses.filter(id => id !== classId);
  doApplyClasses(elementId, updatedClassIds, classesPropType);
  return true;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/add-or-remove-content.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/add-or-remove-content.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AddOrRemoveContent: function() { return /* binding */ AddOrRemoveContent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");




const SIZE = 'tiny';
const AddOrRemoveContent = ({
  isAdded,
  onAdd,
  onRemove,
  children,
  disabled,
  renderLabel
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_3__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    sx: {
      justifyContent: 'space-between',
      alignItems: 'center',
      marginInlineEnd: -0.75
    }
  }, renderLabel(), isAdded ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    size: SIZE,
    onClick: onRemove,
    "aria-label": "Remove",
    disabled: disabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.MinusIcon, {
    fontSize: SIZE
  })) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    size: SIZE,
    onClick: onAdd,
    "aria-label": "Add",
    disabled: disabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PlusIcon, {
    fontSize: SIZE
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Collapse, {
    in: isAdded,
    unmountOnExit: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_3__.SectionContent, null, children)));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/control-label.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ControlLabel: function() { return /* binding */ ControlLabel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




const ControlLabel = ({
  children,
  infoTooltip
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyItems: "start",
    gap: 0.25
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, children), infoTooltip && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: infoTooltip,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.InfoCircleIcon, {
    fontSize: "tiny"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlAdornments, null));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/autocomplete-option-internal-properties.ts":
/*!**************************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/autocomplete-option-internal-properties.ts ***!
  \**************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addGroupToOptions: function() { return /* binding */ addGroupToOptions; },
/* harmony export */   removeInternalKeys: function() { return /* binding */ removeInternalKeys; }
/* harmony export */ });
function addGroupToOptions(options, pluralEntityName) {
  return options.map(option => {
    return {
      ...option,
      _group: `Existing ${pluralEntityName ?? 'options'}`
    };
  });
}
function removeInternalKeys(option) {
  const {
    _group,
    _action,
    ...rest
  } = option;
  return rest;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/creatable-autocomplete.tsx":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/creatable-autocomplete.tsx ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CreatableAutocomplete: function() { return /* binding */ CreatableAutocomplete; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _autocomplete_option_internal_properties__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./autocomplete-option-internal-properties */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/autocomplete-option-internal-properties.ts");
/* harmony import */ var _use_autocomplete_change__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-autocomplete-change */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-autocomplete-change.ts");
/* harmony import */ var _use_autocomplete_states__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./use-autocomplete-states */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-autocomplete-states.ts");
/* harmony import */ var _use_create_option__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./use-create-option */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-create-option.ts");
/* harmony import */ var _use_filter_options__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./use-filter-options */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-filter-options.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }








const MIN_INPUT_LENGTH = 2;
const CreatableAutocomplete = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(CreatableAutocompleteInner);
function CreatableAutocompleteInner({
  selected,
  options,
  entityName,
  onSelect,
  placeholder,
  onCreate,
  validate,
  renderEmptyState,
  ...props
}, ref) {
  const {
    inputValue,
    setInputValue,
    error,
    setError,
    inputHandlers
  } = (0,_use_autocomplete_states__WEBPACK_IMPORTED_MODULE_4__.useInputState)(validate);
  const {
    open,
    openDropdown,
    closeDropdown
  } = (0,_use_autocomplete_states__WEBPACK_IMPORTED_MODULE_4__.useOpenState)(props.open);
  const {
    createOption,
    loading
  } = (0,_use_create_option__WEBPACK_IMPORTED_MODULE_5__.useCreateOption)({
    onCreate,
    validate,
    setInputValue,
    setError,
    closeDropdown
  });
  const [internalOptions, internalSelected] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => [options, selected].map(optionsArr => (0,_autocomplete_option_internal_properties__WEBPACK_IMPORTED_MODULE_2__.addGroupToOptions)(optionsArr, entityName?.plural)), [options, selected, entityName?.plural]);
  const handleChange = (0,_use_autocomplete_change__WEBPACK_IMPORTED_MODULE_3__.useAutocompleteChange)({
    options: internalOptions,
    onSelect,
    createOption,
    setInputValue,
    closeDropdown
  });
  const filterOptions = (0,_use_filter_options__WEBPACK_IMPORTED_MODULE_6__.useFilterOptions)({
    options,
    selected,
    onCreate,
    entityName
  });
  const isCreatable = Boolean(onCreate);
  const freeSolo = isCreatable || inputValue.length < MIN_INPUT_LENGTH || undefined;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Autocomplete, _extends({
    renderTags: (tagValue, getTagProps) => {
      return tagValue.map((option, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Chip, _extends({
        size: "tiny"
      }, getTagProps({
        index
      }), {
        key: option.key ?? option.value ?? option.label,
        label: option.label
      })));
    }
  }, props, {
    ref: ref,
    freeSolo: freeSolo,
    forcePopupIcon: false,
    multiple: true,
    clearOnBlur: true,
    selectOnFocus: true,
    disableClearable: true,
    handleHomeEndKeys: true,
    disabled: loading,
    open: open,
    onOpen: openDropdown,
    onClose: closeDropdown,
    disableCloseOnSelect: true,
    value: internalSelected,
    options: internalOptions,
    ListboxComponent: error ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((_, errorTextRef) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ErrorText, {
      ref: errorTextRef,
      error: error
    })) : undefined,
    renderGroup: params => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Group, params),
    inputValue: inputValue,
    renderInput: params => {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TextField, _extends({}, params, {
        error: Boolean(error),
        placeholder: placeholder
      }, inputHandlers, {
        sx: theme => ({
          '.MuiAutocomplete-inputRoot.MuiInputBase-adornedStart': {
            paddingLeft: theme.spacing(0.25),
            paddingRight: theme.spacing(0.25)
          }
        })
      }));
    },
    onChange: handleChange,
    getOptionLabel: option => typeof option === 'string' ? option : option.label,
    getOptionKey: option => {
      if (typeof option === 'string') {
        return option;
      }
      return option.key ?? option.value ?? option.label;
    },
    filterOptions: filterOptions,
    groupBy: option => option._group ?? '',
    renderOption: (optionProps, option) => {
      const {
        _group,
        label
      } = option;
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("li", _extends({}, optionProps, {
        style: {
          display: 'block',
          textOverflow: 'ellipsis'
        },
        "data-group": _group
      }), label);
    },
    noOptionsText: renderEmptyState?.({
      searchValue: inputValue,
      onClear: () => {
        setInputValue('');
        closeDropdown();
      }
    }),
    isOptionEqualToValue: (option, value) => {
      if (typeof option === 'string') {
        return option === value;
      }
      return option.value === value.value;
    }
  }));
}
const Group = params => {
  const id = `combobox-group-${(0,react__WEBPACK_IMPORTED_MODULE_0__.useId)().replace(/:/g, '_')}`;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledGroup, {
    role: "group",
    "aria-labelledby": id
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledGroupHeader, {
    id: id
  }, " ", params.group), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledGroupItems, {
    role: "listbox"
  }, params.children));
};
const ErrorText = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  error = 'error'
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    ref: ref,
    sx: theme => ({
      padding: theme.spacing(2)
    })
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "caption",
    sx: {
      color: 'error.main',
      display: 'inline-block'
    }
  }, error));
});
const StyledGroup = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)('li')`
	&:not( :last-of-type ) {
		border-bottom: 1px solid ${({
  theme
}) => theme.palette.divider};
	}
`;
const StyledGroupHeader = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  position: 'sticky',
  top: '-8px',
  padding: theme.spacing(1, 2),
  color: theme.palette.text.tertiary,
  backgroundColor: theme.palette.primary.contrastText
}));
const StyledGroupItems = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)('ul')`
	padding: 0;
`;

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/index.ts":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/index.ts ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CreatableAutocomplete: function() { return /* reexport safe */ _creatable_autocomplete__WEBPACK_IMPORTED_MODULE_0__.CreatableAutocomplete; }
/* harmony export */ });
/* harmony import */ var _creatable_autocomplete__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./creatable-autocomplete */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/creatable-autocomplete.tsx");



/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-autocomplete-change.ts":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-autocomplete-change.ts ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useAutocompleteChange: function() { return /* binding */ useAutocompleteChange; }
/* harmony export */ });
/* harmony import */ var _autocomplete_option_internal_properties__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./autocomplete-option-internal-properties */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/autocomplete-option-internal-properties.ts");

function useAutocompleteChange(params) {
  const {
    options,
    onSelect,
    createOption,
    setInputValue,
    closeDropdown
  } = params;
  if (!onSelect && !createOption) {
    return;
  }
  const handleChange = async (_, selectedOrInputValue, reason, details) => {
    const changedOption = details?.option;
    if (!changedOption || typeof changedOption === 'object' && changedOption.fixed) {
      // If `changedOption` is nullish it means no option was selected, created or removed.
      // The reason is either "blur" which we don't support (can't be "clear" since we disabled it).
      // If the option is fixed, it can't be selected, created or removed.
      return;
    }
    const selectedOptions = selectedOrInputValue.filter(option => typeof option !== 'string');
    switch (reason) {
      case 'removeOption':
        const removedOption = changedOption;
        updateSelectedOptions(selectedOptions, 'removeOption', removedOption);
        break;

      // User clicked an option. It's either an existing option, or "Create <new option>".
      case 'selectOption':
        {
          const selectedOption = changedOption;
          if (selectedOption._action === 'create') {
            const newOption = selectedOption.value;
            return createOption?.(newOption);
          }
          updateSelectedOptions(selectedOptions, 'selectOption', selectedOption);
          break;
        }

      // User pressed "Enter" after typing input. The input is either matching existing option or a new option to create.
      case 'createOption':
        {
          const inputValue = changedOption;
          const matchingOption = options.find(option => option.label.toLocaleLowerCase() === inputValue.toLocaleLowerCase());
          if (matchingOption) {
            selectedOptions.push(matchingOption);
            updateSelectedOptions(selectedOptions, 'selectOption', matchingOption);
          } else {
            return createOption?.(inputValue);
          }
          break;
        }
    }
    setInputValue('');
    closeDropdown();
  };
  return handleChange;
  function updateSelectedOptions(selectedOptions, reason, changedOption) {
    onSelect?.(selectedOptions.map(option => (0,_autocomplete_option_internal_properties__WEBPACK_IMPORTED_MODULE_0__.removeInternalKeys)(option)), reason, (0,_autocomplete_option_internal_properties__WEBPACK_IMPORTED_MODULE_0__.removeInternalKeys)(changedOption));
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-autocomplete-states.ts":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-autocomplete-states.ts ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useInputState: function() { return /* binding */ useInputState; },
/* harmony export */   useOpenState: function() { return /* binding */ useOpenState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useInputState(validate) {
  const [inputValue, setInputValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [error, setError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const handleInputChange = event => {
    const {
      value
    } = event.target;
    setInputValue(value);
    if (!validate) {
      return;
    }
    if (!value) {
      setError(null);
      return;
    }
    const {
      isValid,
      errorMessage
    } = validate(value, 'inputChange');
    if (isValid) {
      setError(null);
    } else {
      setError(errorMessage);
    }
  };
  const handleInputBlur = () => {
    setInputValue('');
    setError(null);
  };
  return {
    inputValue,
    setInputValue,
    error,
    setError,
    inputHandlers: {
      onChange: handleInputChange,
      onBlur: handleInputBlur
    }
  };
}
function useOpenState(initialOpen = false) {
  const [open, setOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialOpen);
  const openDropdown = () => setOpen(true);
  const closeDropdown = () => setOpen(false);
  return {
    open,
    openDropdown,
    closeDropdown
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-create-option.ts":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-create-option.ts ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCreateOption: function() { return /* binding */ useCreateOption; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useCreateOption(params) {
  const {
    onCreate,
    validate,
    setInputValue,
    setError,
    closeDropdown
  } = params;
  const [loading, setLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  if (!onCreate) {
    return {
      createOption: null,
      loading: false
    };
  }
  const createOption = async value => {
    setLoading(true);
    if (validate) {
      const {
        isValid,
        errorMessage
      } = validate(value, 'create');
      if (!isValid) {
        setError(errorMessage);
        setLoading(false);
        return;
      }
    }
    try {
      setInputValue('');
      closeDropdown();
      await onCreate(value);
    } catch {
      // TODO: Do something with the error.
    } finally {
      setLoading(false);
    }
  };
  return {
    createOption,
    loading
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-filter-options.ts":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/use-filter-options.ts ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useFilterOptions: function() { return /* binding */ useFilterOptions; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const STRIP_NON_CLASS_CHARS = /[^a-zA-Z0-9_-]/g;
function normalizeClassSearch(value) {
  return value.replace(STRIP_NON_CLASS_CHARS, '').toLowerCase();
}
function useFilterOptions(parameters) {
  const {
    options,
    selected,
    onCreate,
    entityName
  } = parameters;
  const filter = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.createFilterOptions)({
    matchFrom: 'any'
  });
  const filterOptions = (optionList, params) => {
    const selectedValues = selected.map(option => option.value);
    const filteredOptions = filter(optionList.filter(option => !selectedValues.includes(option.value)), {
      ...params,
      inputValue: normalizeClassSearch(params.inputValue)
    });
    const isExisting = options.some(option => params.inputValue === option.label);
    const allowCreate = Boolean(onCreate) && params.inputValue !== '' && !selectedValues.includes(params.inputValue) && !isExisting;
    if (allowCreate) {
      filteredOptions.unshift({
        label: `Create "${params.inputValue}"`,
        value: params.inputValue,
        _group: `Create a new ${entityName?.singular ?? 'option'}`,
        key: `create-${params.inputValue}`,
        _action: 'create'
      });
    }
    return filteredOptions;
  };
  return filterOptions;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/consts.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/consts.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PENDING_CLASS_RENAME_SESSION_KEY: function() { return /* binding */ PENDING_CLASS_RENAME_SESSION_KEY; }
/* harmony export */ });
const PENDING_CLASS_RENAME_SESSION_KEY = 'pending-class-rename-id';

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-context.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-context.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassProvider: function() { return /* binding */ CssClassProvider; },
/* harmony export */   useCssClass: function() { return /* binding */ useCssClass; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const CssClassContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const useCssClass = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(CssClassContext);
  if (!context) {
    throw new Error('useCssClass must be used within a CssClassProvider');
  }
  return context;
};
function CssClassProvider({
  children,
  ...contextValue
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CssClassContext.Provider, {
    value: contextValue
  }, children);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-convert-local.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-convert-local.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassConvert: function() { return /* binding */ CssClassConvert; },
/* harmony export */   CssClassConvertSlot: function() { return /* binding */ CssClassConvertSlot; },
/* harmony export */   injectIntoCssClassConvert: function() { return /* binding */ injectIntoCssClassConvert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./consts */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/consts.ts");









const {
  Slot: CssClassConvertSlot,
  inject: injectIntoCssClassConvert
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_3__.createLocation)();
/**
 * Convert a local class to a global class injection point
 * @param props
 */
const CssClassConvert = props => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_6__.useElement)();
  const elementId = element.id;
  const currentClassesProp = (0,_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_5__.useClassesProp)();
  const {
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_7__.useStyle)();
  const [, saveValue] = (0,_elementor_session__WEBPACK_IMPORTED_MODULE_4__.useSessionStorage)(_consts__WEBPACK_IMPORTED_MODULE_8__.PENDING_CLASS_RENAME_SESSION_KEY, 'app');
  const successCallback = newId => {
    if (!props.styleDef) {
      throw new Error('Style definition is required for converting local class to global class.');
    }
    onConvert({
      newId,
      elementId,
      classesProp: currentClassesProp,
      styleDef: props.styleDef
    });
    saveValue(newId);
    setActiveId(newId);
    props.closeMenu();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CssClassConvertSlot, {
    canConvert: !!props.canConvert,
    styleDef: props.styleDef,
    successCallback: successCallback
  });
};
const onConvert = opts => {
  const {
    newId,
    elementId,
    classesProp
  } = opts;
  (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.deleteElementStyle)(elementId, opts.styleDef.id);
  const currentUsedClasses = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementSetting)(elementId, classesProp) || {
    value: []
  };
  (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.updateElementSettings)({
    id: elementId,
    props: {
      [classesProp]: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.classesPropTypeUtil.create([newId, ...currentUsedClasses.value])
    },
    withHistory: false
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-item.tsx":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-item.tsx ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassItem: function() { return /* binding */ CssClassItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./consts */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/consts.ts");
/* harmony import */ var _css_class_context__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./css-class-context */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-context.tsx");
/* harmony import */ var _css_class_menu__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./css-class-menu */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-menu.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }













const CHIP_SIZE = 'tiny';
function CssClassItem(props) {
  const {
    chipProps,
    icon,
    color: colorProp,
    fixed,
    ...classProps
  } = props;
  const {
    id,
    provider,
    label,
    isActive,
    onClickActive,
    renameLabel,
    setError
  } = classProps;
  const {
    elementStates
  } = (0,_css_class_menu__WEBPACK_IMPORTED_MODULE_11__.useElementStates)();
  const {
    meta,
    setMetaState
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.usePopupState)({
    variant: 'popover'
  });
  const [chipRef, setChipRef] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const {
    onDelete,
    ...chipGroupProps
  } = chipProps;
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.useUserStylesCapability)();
  const [convertedFromLocalId,, clearConvertedFromLocalId] = (0,_elementor_session__WEBPACK_IMPORTED_MODULE_5__.useSessionStorage)(_consts__WEBPACK_IMPORTED_MODULE_9__.PENDING_CLASS_RENAME_SESSION_KEY, 'app');
  const {
    ref,
    isEditing,
    openEditMode,
    error,
    getProps: getEditableProps
  } = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.useEditable)({
    value: label,
    onSubmit: renameLabel,
    validation: validateLabel,
    onError: setError
  });
  const color = error ? 'error' : colorProp;
  const providerActions = provider ? _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.stylesRepository.getProviderByKey(provider)?.actions : null;
  const allowRename = Boolean(providerActions?.update) && userCan(provider ?? '')?.update;
  const isShowingState = isActive && meta.state;
  const stateLabel = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (meta.state && (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__.isClassState)(meta.state)) {
      return elementStates.find(state => state.value === meta.state)?.label;
    }
    return meta.state;
  }, [meta.state, elementStates]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (convertedFromLocalId && id === convertedFromLocalId) {
      clearConvertedFromLocalId();
      openEditMode();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id, convertedFromLocalId]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.ThemeProvider, {
    palette: "default"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.UnstableChipGroup, _extends({
    ref: setChipRef
  }, chipGroupProps, {
    "aria-label": `Edit ${label}`,
    role: "group",
    sx: theme => ({
      '&.MuiChipGroup-root.MuiAutocomplete-tag': {
        margin: theme.spacing(0.125)
      }
    })
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Chip, {
    size: CHIP_SIZE,
    label: isEditing ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.EditableField, _extends({
      ref: ref
    }, getEditableProps())) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.EllipsisWithTooltip, {
      maxWidth: "10ch",
      title: label,
      as: "div"
    }),
    variant: isActive && !meta.state && !isEditing ? 'filled' : 'standard',
    shape: "rounded",
    icon: icon,
    color: color,
    onClick: () => {
      if (isShowingState) {
        setMetaState(null);
        return;
      }
      if (allowRename && isActive) {
        openEditMode();
        return;
      }
      onClickActive(id);
    },
    "aria-pressed": isActive,
    sx: theme => ({
      lineHeight: 1,
      cursor: isActive && allowRename && !isShowingState ? 'text' : 'pointer',
      borderRadius: `${theme.shape.borderRadius * 0.75}px`,
      '&.Mui-focusVisible': {
        boxShadow: 'none !important'
      }
    })
  }), !isEditing && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Chip, _extends({
    icon: isShowingState ? undefined : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.DotsVerticalIcon, {
      fontSize: "tiny"
    }),
    size: CHIP_SIZE,
    label: isShowingState ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
      direction: "row",
      gap: 0.5,
      alignItems: "center"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Typography, {
      variant: "inherit"
    }, stateLabel), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.DotsVerticalIcon, {
      fontSize: "tiny"
    })) : undefined,
    variant: "filled",
    shape: "rounded",
    color: color
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.bindTrigger)(popupState), {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Open CSS Class Menu', 'elementor'),
    sx: theme => ({
      borderRadius: `${theme.shape.borderRadius * 0.75}px`,
      paddingRight: 0,
      ...(!isShowingState ? {
        paddingLeft: 0
      } : {}),
      '.MuiChip-label': isShowingState ? {
        paddingRight: 0
      } : {
        padding: 0
      }
    })
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_class_context__WEBPACK_IMPORTED_MODULE_10__.CssClassProvider, _extends({}, classProps, {
    handleRename: openEditMode
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_class_menu__WEBPACK_IMPORTED_MODULE_11__.CssClassMenu, {
    popupState: popupState,
    anchorEl: chipRef,
    fixed: fixed
  })));
}
const validateLabel = newLabel => {
  const result = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.validateStyleLabel)(newLabel, 'rename');
  if (result.isValid) {
    return null;
  }
  return result.errorMessage;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-menu.tsx":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-menu.tsx ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssClassMenu: function() { return /* binding */ CssClassMenu; },
/* harmony export */   useElementStates: function() { return /* binding */ useElementStates; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../utils/get-styles-provider-color */ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts");
/* harmony import */ var _utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../utils/tracking/subscribe */ "./packages/packages/core/editor-editing-panel/src/utils/tracking/subscribe.ts");
/* harmony import */ var _style_indicator__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../style-indicator */ "./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx");
/* harmony import */ var _css_class_context__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./css-class-context */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-context.tsx");
/* harmony import */ var _duplicate_class_menu_item__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./duplicate-class-menu-item */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/duplicate-class-menu-item.tsx");
/* harmony import */ var _local_class_sub_menu__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./local-class-sub-menu */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/local-class-sub-menu.tsx");
/* harmony import */ var _use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./use-apply-and-unapply-class */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-apply-and-unapply-class.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }















const DEFAULT_PSEUDO_STATES = [{
  key: 'normal',
  value: null,
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('normal', 'elementor')
}, {
  key: 'hover',
  value: 'hover',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('hover', 'elementor')
}, {
  key: 'focus',
  value: 'focus',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('focus', 'elementor')
}, {
  key: 'active',
  value: 'active',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('active', 'elementor')
}];
function usePseudoStates() {
  const {
    elementType
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_6__.useElement)();
  const {
    pseudoStates = []
  } = elementType;
  const additionalStates = pseudoStates.map(({
    name,
    value
  }) => ({
    key: value,
    value: value,
    label: name
  }));
  return [...DEFAULT_PSEUDO_STATES, ...additionalStates];
}
function CssClassMenu({
  popupState,
  anchorEl,
  fixed
}) {
  const {
    provider
  } = (0,_css_class_context__WEBPACK_IMPORTED_MODULE_11__.useCssClass)();
  const isLocalStyle = provider ? (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.isElementsStylesProvider)(provider) : true;
  const pseudoStates = usePseudoStates();
  const handleKeyDown = e => {
    e.stopPropagation();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Menu, _extends({
    MenuListProps: {
      dense: true,
      sx: {
        minWidth: '160px'
      }
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindMenu)(popupState), {
    anchorEl: anchorEl,
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'left'
    },
    transformOrigin: {
      horizontal: 'left',
      vertical: -4
    },
    onKeyDown: handleKeyDown
    // Workaround for focus-visible issue.
    ,
    disableAutoFocusItem: true
  }), isLocalStyle && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_local_class_sub_menu__WEBPACK_IMPORTED_MODULE_13__.LocalClassSubMenu, {
    popupState: popupState
  }), getMenuItemsByProvider({
    provider,
    closeMenu: popupState.close,
    fixed
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.MenuSubheader, {
    sx: {
      typography: 'caption',
      color: 'text.secondary',
      pb: 0.5,
      pt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('States', 'elementor')), pseudoStates.map(state => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StateMenuItem, {
      key: state.key,
      state: state.value,
      label: state.label,
      closeMenu: popupState.close
    });
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ClassStatesMenu, {
    closeMenu: popupState.close
  }));
}
function ClassStatesMenu({
  closeMenu
}) {
  const {
    elementStates,
    elementTitle
  } = useElementStates();
  if (!elementStates.length) {
    return null;
  }

  /* translators: %s: Element type title. */
  const customTitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('%s States', 'elementor').replace('%s', elementTitle);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.MenuSubheader, {
    sx: {
      typography: 'caption',
      color: 'text.secondary',
      pb: 0.5,
      pt: 1
    }
  }, customTitle), elementStates.map(state => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StateMenuItem, {
      key: state.key,
      state: state.value,
      label: state.label,
      closeMenu: closeMenu
    });
  }));
}
const CLASS_STATES_MAP = {
  selected: {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('selected', 'elementor')
  },
  disabled: {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('disabled', 'elementor')
  }
};
function useElementStates() {
  const {
    elementType
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_6__.useElement)();
  const {
    styleStates = []
  } = elementType;
  const elementStates = styleStates.map(({
    value,
    name
  }) => ({
    key: value,
    value,
    label: CLASS_STATES_MAP[value]?.label ?? name
  }));
  return {
    elementStates,
    elementTitle: elementType.title
  };
}
function useModifiedStates(styleId) {
  const {
    meta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_7__.useStyle)();
  const styleDef = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.stylesRepository.all().find(style => style.id === styleId);
  return Object.fromEntries(styleDef?.variants.filter(variant => meta.breakpoint === variant.meta.breakpoint && (!(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.isEmpty)(variant.props) || Boolean(variant.custom_css?.raw?.trim()))).map(variant => [variant.meta.state ?? 'normal', true]) ?? []);
}
function getMenuItemsByProvider({
  provider,
  closeMenu,
  fixed
}) {
  if (!provider) {
    return [];
  }
  const providerInstance = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.stylesRepository.getProviderByKey(provider);
  const providerActions = providerInstance?.actions;
  const canUpdate = providerActions?.update;
  const canDuplicate = providerActions?.create && providerActions?.get;
  const canUnapply = !fixed;
  const actions = [canUpdate && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RenameClassMenuItem, {
    key: "rename-class",
    closeMenu: closeMenu
  }), canDuplicate && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_duplicate_class_menu_item__WEBPACK_IMPORTED_MODULE_12__.DuplicateClassMenuItem, {
    key: "duplicate-class",
    closeMenu: closeMenu
  }), canUnapply && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(UnapplyClassMenuItem, {
    key: "unapply-class",
    closeMenu: closeMenu
  })].filter(Boolean);
  if (actions.length) {
    actions.unshift(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.MenuSubheader, {
      key: "provider-label",
      sx: {
        typography: 'caption',
        color: 'text.secondary',
        pb: 0.5,
        pt: 1,
        textTransform: 'capitalize'
      }
    }, providerInstance?.labels?.singular));
    actions.push(/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, {
      key: "provider-actions-divider"
    }));
  }
  return actions;
}
function StateMenuItem({
  state,
  label,
  closeMenu,
  ...props
}) {
  const {
    id: styleId,
    provider
  } = (0,_css_class_context__WEBPACK_IMPORTED_MODULE_11__.useCssClass)();
  const {
    id: activeId,
    setId: setActiveId,
    setMetaState: setActiveMetaState,
    meta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_7__.useStyle)();
  const {
    state: activeState
  } = meta;
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.useUserStylesCapability)();
  const modifiedStates = useModifiedStates(styleId);
  const isUpdateAllowed = !state || userCan(provider ?? '').updateProps;
  const isStyled = modifiedStates[state ?? 'normal'] ?? false;
  const disabled = !isUpdateAllowed && !isStyled;
  const isActive = styleId === activeId;
  const isSelected = state === activeState && isActive;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuListItem, _extends({}, props, {
    selected: isSelected,
    disabled: disabled,
    sx: {
      textTransform: 'capitalize'
    },
    onClick: () => {
      if (!isActive) {
        setActiveId(styleId);
      }
      (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_9__.trackStyles)(provider ?? '', 'classStateClicked', {
        classId: styleId,
        type: label,
        source: styleId ? 'global' : 'local'
      });
      setActiveMetaState(state);
      closeMenu();
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuItemInfotip, {
    showInfoTip: disabled,
    content: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('With your current role, you can only use existing states.', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
    gap: 0.75,
    direction: "row",
    alignItems: "center"
  }, isStyled && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_indicator__WEBPACK_IMPORTED_MODULE_10__.StyleIndicator, {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Has style', 'elementor'),
    getColor: (0,_utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_8__.getTempStylesProviderThemeColor)(provider ?? '')
  }), label)));
}
function UnapplyClassMenuItem({
  closeMenu,
  ...props
}) {
  const {
    id: classId,
    label: classLabel,
    provider
  } = (0,_css_class_context__WEBPACK_IMPORTED_MODULE_11__.useCssClass)();
  const unapplyClass = (0,_use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_14__.useUndoableUnapplyClass)();
  return classId ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuListItem, _extends({}, props, {
    onClick: () => {
      unapplyClass({
        classId,
        classLabel
      });
      (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_9__.trackStyles)(provider ?? '', 'classRemoved', {
        classId,
        classTitle: classLabel,
        source: 'style-tab'
      });
      closeMenu();
    }
  }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Remove', 'elementor')) : null;
}
function RenameClassMenuItem({
  closeMenu
}) {
  const {
    handleRename,
    provider
  } = (0,_css_class_context__WEBPACK_IMPORTED_MODULE_11__.useCssClass)();
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.useUserStylesCapability)();
  if (!provider) {
    return null;
  }
  const isAllowed = userCan(provider).update;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuListItem, {
    disabled: !isAllowed,
    onClick: () => {
      closeMenu();
      handleRename();
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuItemInfotip, {
    showInfoTip: !isAllowed,
    content: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)("With your current role, you can use existing classes but can't modify them.", 'elementor')
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Rename', 'elementor')));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-selector.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-selector.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassSelectorActionsSlot: function() { return /* binding */ ClassSelectorActionsSlot; },
/* harmony export */   CssClassSelector: function() { return /* binding */ CssClassSelector; },
/* harmony export */   injectIntoClassSelectorActions: function() { return /* binding */ injectIntoClassSelectorActions; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../utils/get-styles-provider-color */ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts");
/* harmony import */ var _utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../utils/tracking/subscribe */ "./packages/packages/core/editor-editing-panel/src/utils/tracking/subscribe.ts");
/* harmony import */ var _creatable_autocomplete__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../creatable-autocomplete */ "./packages/packages/core/editor-editing-panel/src/components/creatable-autocomplete/index.ts");
/* harmony import */ var _css_class_item__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./css-class-item */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-item.tsx");
/* harmony import */ var _missing_classes_alert__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./missing-classes-alert */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/missing-classes-alert.tsx");
/* harmony import */ var _use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./use-apply-and-unapply-class */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-apply-and-unapply-class.ts");
/* harmony import */ var _use_missing_classes__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./use-missing-classes */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-missing-classes.ts");


















const ID = 'elementor-css-class-selector';
const TAGS_LIMIT = 50;
function openClassManagerPanel() {
  window.dispatchEvent(new CustomEvent('elementor/toggle-design-system', {
    detail: {
      tab: 'classes'
    }
  }));
}
const EMPTY_OPTION = {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('local', 'elementor'),
  value: null,
  fixed: true,
  color: getTempStylesProviderColorName('accent'),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.MapPinIcon, null),
  provider: null
};
const {
  Slot: ClassSelectorActionsSlot,
  inject: injectIntoClassSelectorActions
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_4__.createLocation)();

/**
 * Applied - Classes applied to an element.
 * Active - Class that is currently on edit mode.
 */

function CssClassSelector() {
  const options = useOptions();
  const {
    id: activeId,
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_9__.useStyle)();
  const autocompleteRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const [renameError, setRenameError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const handleSelect = useHandleSelect();
  const {
    create,
    validate,
    entityName,
    isAtLimit,
    limitCount
  } = useCreateAction();
  const appliedOptions = useAppliedOptions(options);
  const active = appliedOptions.find(option => option.value === activeId) ?? EMPTY_OPTION;
  const showPlaceholder = appliedOptions.every(({
    fixed
  }) => fixed);
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.useUserStylesCapability)();
  const canEdit = active.provider ? userCan(active.provider).updateProps : true;
  const missingClassesIds = (0,_use_missing_classes__WEBPACK_IMPORTED_MODULE_16__.useMissingClassesIds)();
  const hasMissingClasses = missingClassesIds.length > 0;
  const unapplyClasses = (0,_use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_15__.useUnapplyClasses)();
  const clearMissingClasses = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    unapplyClasses(missingClassesIds);
  }, [missingClassesIds, unapplyClasses]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    p: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "row",
    gap: 1,
    alignItems: "center",
    justifyContent: "space-between"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.FormLabel, {
    htmlFor: ID,
    size: "small"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Classes', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "row",
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ClassSelectorActionsSlot, null))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.WarningInfotip, {
    open: Boolean(renameError),
    text: renameError ?? '',
    placement: "bottom",
    width: autocompleteRef.current?.getBoundingClientRect().width,
    offset: [0, -15]
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_creatable_autocomplete__WEBPACK_IMPORTED_MODULE_12__.CreatableAutocomplete, {
    id: ID,
    ref: autocompleteRef,
    size: "tiny",
    placeholder: showPlaceholder ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Type class name', 'elementor') : undefined,
    options: options,
    selected: appliedOptions,
    entityName: entityName,
    onSelect: handleSelect,
    onCreate: create ?? undefined,
    validate: validate ?? undefined,
    limitTags: TAGS_LIMIT,
    renderEmptyState: isAtLimit && typeof limitCount === 'number' ? props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LimitReachedEmptyState, {
      limitCount: limitCount,
      onClear: props.onClear
    }) : EmptyState,
    getLimitTagsText: more => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Chip, {
      size: "tiny",
      variant: "standard",
      label: `+${more}`,
      clickable: true
    }),
    renderTags: (values, getTagProps) => values.map((value, index) => {
      const chipProps = getTagProps({
        index
      });
      const isActive = value.value === active?.value;
      const renameLabel = newLabel => {
        if (!value.value) {
          throw new Error(`Cannot rename a class without style id`);
        }
        (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_11__.trackStyles)(value.provider ?? '', 'classRenamed', {
          classId: value.value,
          newValue: newLabel,
          oldValue: value.label,
          source: 'style-tab'
        });
        return updateClassByProvider(value.provider, {
          label: newLabel,
          id: value.value
        });
      };
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_class_item__WEBPACK_IMPORTED_MODULE_13__.CssClassItem, {
        key: chipProps.key,
        fixed: value.fixed,
        label: value.label,
        provider: value.provider,
        id: value.value,
        isActive: isActive,
        color: isActive && value.color ? value.color : 'default',
        icon: value.icon,
        chipProps: chipProps,
        onClickActive: () => setActiveId(value.value),
        renameLabel: renameLabel,
        setError: setRenameError
      });
    })
  })), hasMissingClasses && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_missing_classes_alert__WEBPACK_IMPORTED_MODULE_14__.MissingClassesAlert, {
    onDismiss: clearMissingClasses
  }), !canEdit && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.InfoAlert, {
    sx: {
      mt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('With your current role, you can use existing classes but can’t modify them.', 'elementor')));
}
const EmptyStateLayout = ({
  searchValue,
  onClear,
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
  sx: {
    py: 4
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
  gap: 1,
  alignItems: "center",
  color: "text.secondary",
  justifyContent: "center",
  sx: {
    px: 2,
    m: 'auto',
    maxWidth: '236px'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ColorSwatchIcon, {
  sx: {
    transform: 'rotate(90deg)'
  },
  fontSize: "large"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
  align: "center",
  variant: "subtitle2"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Sorry, nothing matched', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), "\u201C", searchValue, "\u201D."), children, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Link, {
  color: "text.secondary",
  variant: "caption",
  component: "button",
  onClick: onClear
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Clear & try again', 'elementor'))));
const EmptyState = props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EmptyStateLayout, props, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
  align: "center",
  variant: "caption",
  sx: {
    mb: 2
  }
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('With your current role,', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('you can only use existing classes.', 'elementor')));
const LimitReachedEmptyState = ({
  limitCount,
  onClear
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
  sx: {
    py: 4
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
  gap: 1,
  alignItems: "center",
  color: "text.secondary",
  justifyContent: "center",
  sx: {
    px: 1,
    m: 'auto',
    maxWidth: '260px'
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ColorSwatchIcon, {
  sx: {
    transform: 'rotate(90deg)'
  },
  fontSize: "large"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
  align: "center",
  variant: "subtitle2"
}, /* translators: %s is the maximum number of classes */
(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Limit of %s classes reached', 'elementor').replace('%s', String(limitCount))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
  align: "center",
  variant: "caption",
  component: "div"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Remove a class to create a new one.', 'elementor'), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Link, {
  color: "inherit",
  variant: "caption",
  component: "button",
  onClick: onClear,
  sx: {
    verticalAlign: 'baseline'
  }
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Clear', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Button, {
  variant: "outlined",
  color: "secondary",
  size: "small",
  onClick: () => {
    openClassManagerPanel();
    onClear();
  }
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Class Manager', 'elementor'))));
const updateClassByProvider = (provider, data) => {
  if (!provider) {
    return;
  }
  const providerInstance = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.getProviderByKey(provider);
  if (!providerInstance) {
    return;
  }
  return providerInstance.actions.update?.(data);
};
function useOptions() {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_8__.useElement)();
  const isProviderEditable = provider => !!provider.actions.updateProps;
  return (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.useProviders)().filter(isProviderEditable).flatMap(provider => {
    const isElements = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.isElementsStylesProvider)(provider.getKey());
    const styleDefs = provider.actions.all({
      elementId: element.id
    });

    // Add an empty local option for elements, as fallback.
    if (isElements && styleDefs.length === 0) {
      return [EMPTY_OPTION];
    }
    return styleDefs.map(styleDef => {
      return {
        label: styleDef.label,
        value: styleDef.id,
        fixed: isElements,
        color: getTempStylesProviderColorName((0,_utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_10__.getStylesProviderColorName)(provider.getKey())),
        icon: isElements ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.MapPinIcon, null) : null,
        provider: provider.getKey()
      };
    });
  });
}
function getTempStylesProviderColorName(color) {
  if (color === 'accent') {
    return 'primary';
  }
  return color;
}
function useCreateAction() {
  const [provider, createAction] = (0,_use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_15__.useCreateAndApplyClass)();
  if (!provider || !createAction) {
    return {};
  }
  const entityName = provider.labels.singular && provider.labels.plural ? provider.labels : undefined;
  const validate = (newClassLabel, event) => (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.validateStyleLabel)(newClassLabel, event);
  if (hasReachedLimit(provider)) {
    return {
      entityName,
      isAtLimit: true,
      limitCount: provider.limit,
      validate
    };
  }
  const create = classLabel => {
    const {
      createdId
    } = createAction({
      classLabel
    });
    (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_11__.trackStyles)(provider.getKey() ?? '', 'classCreated', {
      source: 'created',
      classTitle: classLabel,
      classId: createdId
    });
  };
  return {
    create,
    validate,
    entityName,
    isAtLimit: false
  };
}
function hasReachedLimit(provider) {
  return provider.actions.all().length >= provider.limit;
}
function useAppliedOptions(options) {
  const currentClassesProp = (0,_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_7__.useClassesProp)();
  const appliedIds = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_8__.usePanelElementSetting)(currentClassesProp)?.value ?? [];
  const appliedOptions = options.filter(option => option.value && appliedIds.includes(option.value));
  const hasElementsProviderStyleApplied = appliedOptions.some(option => option.provider && (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.isElementsStylesProvider)(option.provider));
  if (!hasElementsProviderStyleApplied) {
    appliedOptions.unshift(EMPTY_OPTION);
  }
  return appliedOptions;
}
function useHandleSelect() {
  const apply = (0,_use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_15__.useUndoableApplyClass)();
  const unapply = (0,_use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_15__.useUndoableUnapplyClass)();
  return (_selectedOptions, reason, option) => {
    if (!option.value) {
      return;
    }
    switch (reason) {
      case 'selectOption':
        apply({
          classId: option.value,
          classLabel: option.label
        });
        (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_11__.trackStyles)(option.provider ?? '', 'classApplied', {
          classId: option.value,
          source: 'style-tab'
        });
        break;
      case 'removeOption':
        unapply({
          classId: option.value,
          classLabel: option.label
        });
        (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_11__.trackStyles)(option.provider ?? '', 'classRemoved', {
          classId: option.value,
          source: 'style-tab'
        });
        break;
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/duplicate-class-menu-item.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/duplicate-class-menu-item.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DuplicateClassMenuItem: function() { return /* binding */ DuplicateClassMenuItem; },
/* harmony export */   getUniqueDuplicateLabel: function() { return /* binding */ getUniqueDuplicateLabel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../utils/tracking/subscribe */ "./packages/packages/core/editor-editing-panel/src/utils/tracking/subscribe.ts");
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./consts */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/consts.ts");
/* harmony import */ var _css_class_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./css-class-context */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-context.tsx");
/* harmony import */ var _use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./use-apply-and-unapply-class */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-apply-and-unapply-class.ts");









const DUPLICATE_LABEL_PREFIX = 'copy-of';
function getUniqueDuplicateLabel(originalLabel, existingLabels) {
  let newLabel = `${DUPLICATE_LABEL_PREFIX}-${originalLabel}`;
  let counter = 2;
  while (existingLabels.includes(newLabel)) {
    newLabel = `${DUPLICATE_LABEL_PREFIX}-${originalLabel}-${counter}`;
    counter++;
  }
  return newLabel;
}
function DuplicateClassMenuItem({
  closeMenu
}) {
  const {
    id: classId,
    provider
  } = (0,_css_class_context__WEBPACK_IMPORTED_MODULE_7__.useCssClass)();
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.useUserStylesCapability)();
  const applyClass = (0,_use_apply_and_unapply_class__WEBPACK_IMPORTED_MODULE_8__.useUndoableApplyClass)();
  const [, setPendingEditId] = (0,_elementor_session__WEBPACK_IMPORTED_MODULE_3__.useSessionStorage)(_consts__WEBPACK_IMPORTED_MODULE_6__.PENDING_CLASS_RENAME_SESSION_KEY, 'app');
  if (!provider || !classId) {
    return null;
  }
  const providerInstance = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.getProviderByKey(provider);
  const createAction = providerInstance?.actions.create;
  const getAction = providerInstance?.actions.get;
  if (!createAction || !getAction) {
    return null;
  }
  if (!userCan(provider).create) {
    return null;
  }
  const handleDuplicate = () => {
    const styleDef = getAction(classId);
    if (!styleDef) {
      closeMenu();
      return;
    }
    const existingLabels = providerInstance.actions.all().map(style => style.label);
    const newLabel = getUniqueDuplicateLabel(styleDef.label, existingLabels);
    const newId = createAction(newLabel, styleDef.variants);
    if (newId) {
      applyClass({
        classId: newId,
        classLabel: newLabel
      });
      setPendingEditId(newId);
      (0,_utils_tracking_subscribe__WEBPACK_IMPORTED_MODULE_5__.trackStyles)(provider, 'classCreated', {
        classId: newId,
        source: 'duplicated',
        classTitle: newLabel
      });
    }
    closeMenu();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
    onClick: handleDuplicate
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Duplicate', 'elementor'));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/local-class-sub-menu.tsx":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/local-class-sub-menu.tsx ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LocalClassSubMenu: function() { return /* binding */ LocalClassSubMenu; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _css_class_convert_local__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./css-class-convert-local */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-convert-local.tsx");
/* harmony import */ var _use_can_convert_local_class_to_global__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./use-can-convert-local-class-to-global */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-can-convert-local-class-to-global.ts");





const LocalClassSubMenu = props => {
  const {
    canConvert,
    styleDef
  } = (0,_use_can_convert_local_class_to_global__WEBPACK_IMPORTED_MODULE_4__.useCanConvertLocalClassToGlobal)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuSubheader, {
    sx: {
      typography: 'caption',
      color: 'text.secondary',
      pb: 0.5,
      pt: 1
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Local Class', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_class_convert_local__WEBPACK_IMPORTED_MODULE_3__.CssClassConvert, {
    canConvert: canConvert,
    styleDef: styleDef,
    closeMenu: props.popupState.close
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/missing-classes-alert.tsx":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/missing-classes-alert.tsx ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MissingClassesAlert: function() { return /* binding */ MissingClassesAlert; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function MissingClassesAlert({
  onDismiss
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Alert, {
    severity: "warning",
    onClose: onDismiss,
    size: "small",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.AlertTriangleFilledIcon, {
      fontSize: "tiny"
    }),
    sx: {
      mt: 1
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.AlertTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Some classes are missing', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    textColor: "primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('A class was removed from your site and is no longer active on this element', 'elementor')));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-apply-and-unapply-class.ts":
/*!***************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/use-apply-and-unapply-class.ts ***!
  \***************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCreateAndApplyClass: function() { return /* binding */ useCreateAndApplyClass; },
/* harmony export */   useUnapplyClasses: function() { return /* binding */ useUnapplyClasses; },
/* harmony export */   useUndoableApplyClass: function() { return /* binding */ useUndoableApplyClass; },
/* harmony export */   useUndoableUnapplyClass: function() { return /* binding */ useUndoableUnapplyClass; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _apply_unapply_actions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../apply-unapply-actions */ "./packages/packages/core/editor-editing-panel/src/apply-unapply-actions.ts");
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");









function useUndoableApplyClass() {
  const {
    id: activeId,
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const applyClass = useApplyClass();
  const unapplyClasses = useUnapplyClasses();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.undoable)({
      do: ({
        classId
      }) => {
        const prevActiveId = activeId;
        applyClass(classId);
        return prevActiveId;
      },
      undo: ({
        classId
      }, prevActiveId) => {
        unapplyClasses([classId]);
        setActiveId(prevActiveId);
      }
    }, {
      title: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementLabel)(element.id),
      subtitle: ({
        classLabel
      }) => {
        /* translators: %s is the class name. */
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)(`class %s applied`, 'elementor').replace('%s', classLabel);
      }
    });
  }, [activeId, applyClass, element.id, unapplyClasses, setActiveId]);
}
function useUndoableUnapplyClass() {
  const {
    id: activeId,
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const applyClass = useApplyClass();
  const unapplyClasses = useUnapplyClasses();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.undoable)({
      do: ({
        classId
      }) => {
        const prevActiveId = activeId;
        unapplyClasses([classId]);
        return prevActiveId;
      },
      undo: ({
        classId
      }, prevActiveId) => {
        applyClass(classId);
        setActiveId(prevActiveId);
      }
    }, {
      title: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementLabel)(element.id),
      subtitle: ({
        classLabel
      }) => {
        /* translators: %s is the class name. */
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)(`class %s removed`, 'elementor').replace('%s', classLabel);
      }
    });
  }, [activeId, applyClass, element.id, unapplyClasses, setActiveId]);
}
function useCreateAndApplyClass() {
  const {
    id: activeId,
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const [provider, createAction] = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_2__.useGetStylesRepositoryCreateAction)() ?? [null, null];
  const deleteAction = provider?.actions.delete;
  const applyClass = useApplyClass();
  const unapplyClasses = useUnapplyClasses();
  const undoableCreateAndApply = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!provider || !createAction) {
      return;
    }
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.undoable)({
      do: ({
        classLabel
      }) => {
        const prevActiveId = activeId;
        const createdId = createAction(classLabel);
        applyClass(createdId);
        return {
          prevActiveId,
          createdId
        };
      },
      undo: (_, {
        prevActiveId,
        createdId
      }) => {
        unapplyClasses([createdId]);
        deleteAction?.(createdId);
        setActiveId(prevActiveId);
      },
      redo: ({
        classLabel
      }, {
        createdId
      }) => {
        const prevActiveId = activeId;
        createAction(classLabel, [], createdId);
        applyClass(createdId);
        return {
          prevActiveId,
          createdId
        };
      }
    }, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Class', 'elementor'),
      subtitle: ({
        classLabel
      }) => {
        /* translators: %s is the class name. */
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)(`%s created`, 'elementor').replace('%s', classLabel);
      }
    });
  }, [activeId, applyClass, createAction, deleteAction, provider, setActiveId, unapplyClasses]);
  if (!provider || !undoableCreateAndApply) {
    return [null, null];
  }
  return [provider, undoableCreateAndApply];
}
function useApplyClass() {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const {
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const {
    setClasses,
    getAppliedClasses
  } = useClasses();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(classIDToApply => {
    const appliedClasses = getAppliedClasses();
    if (appliedClasses.includes(classIDToApply)) {
      throw new Error(`Class ${classIDToApply} is already applied to element ${element.id}, cannot re-apply.`);
    }
    const updatedClassesIds = [...appliedClasses, classIDToApply];
    setClasses(updatedClassesIds);
    setActiveId(classIDToApply);
  }, [element.id, getAppliedClasses, setActiveId, setClasses]);
}
function useUnapplyClasses() {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const {
    id: activeId,
    setId: setActiveId
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const {
    setClasses,
    getAppliedClasses
  } = useClasses();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(classIDsToUnapply => {
    const appliedClasses = getAppliedClasses();
    if (!classIDsToUnapply.every(classID => appliedClasses.includes(classID))) {
      const missingClasses = classIDsToUnapply.filter(classID => !appliedClasses.includes(classID));
      throw new Error(`Classes ${missingClasses.join(', ')} are not applied to element ${element.id}, cannot unapply them.`);
    }
    const updatedClassesIds = appliedClasses.filter(id => !classIDsToUnapply.includes(id));
    setClasses(updatedClassesIds);
    if (activeId && classIDsToUnapply.includes(activeId)) {
      setActiveId(updatedClassesIds[0] ?? null);
    }
  }, [activeId, element.id, getAppliedClasses, setActiveId, setClasses]);
}
function useClasses() {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const currentClassesProp = (0,_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_6__.useClassesProp)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const setClasses = ids => {
      (0,_apply_unapply_actions__WEBPACK_IMPORTED_MODULE_5__.doApplyClasses)(element.id, ids, currentClassesProp);
    };
    const getAppliedClasses = () => (0,_apply_unapply_actions__WEBPACK_IMPORTED_MODULE_5__.doGetAppliedClasses)(element.id, currentClassesProp) || [];
    return {
      setClasses,
      getAppliedClasses
    };
  }, [currentClassesProp, element.id]);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-can-convert-local-class-to-global.ts":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/use-can-convert-local-class-to-global.ts ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCanConvertLocalClassToGlobal: function() { return /* binding */ useCanConvertLocalClassToGlobal; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");



const useCanConvertLocalClassToGlobal = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_1__.useElement)();
  const {
    provider,
    id,
    meta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_2__.useStyle)();
  const styleDef = provider?.actions.get(id, {
    elementId: element.id,
    ...meta
  });
  const isLocalStylesProvider = provider && (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.isElementsStylesProvider)(provider?.getKey());
  const variants = styleDef?.variants || [];
  const canConvert = !!(isLocalStylesProvider && variants.length);
  return {
    canConvert,
    isLocalStylesProvider,
    id,
    styleDef: styleDef || null
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/css-classes/use-missing-classes.ts":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/css-classes/use-missing-classes.ts ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useMissingClassesIds: function() { return /* binding */ useMissingClassesIds; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");



function useMissingClassesIds() {
  const providers = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.useProviders)();
  const currentClassesProp = (0,_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_1__.useClassesProp)();
  const appliedIds = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_2__.usePanelElementSetting)(currentClassesProp)?.value ?? [];
  const allKnownIds = new Set(providers.flatMap(provider => provider.actions.all().map(style => style.id)));
  return appliedIds.filter(id => !allKnownIds.has(id));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/custom-css-indicator.tsx":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/custom-css-indicator.tsx ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomCssIndicator: function() { return /* binding */ CustomCssIndicator; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _hooks_use_custom_css__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-custom-css */ "./packages/packages/core/editor-editing-panel/src/hooks/use-custom-css.ts");
/* harmony import */ var _utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../utils/get-styles-provider-color */ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts");
/* harmony import */ var _style_indicator__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./style-indicator */ "./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx");









const CustomCssIndicator = () => {
  const {
    customCss
  } = (0,_hooks_use_custom_css__WEBPACK_IMPORTED_MODULE_5__.useCustomCss)();
  const {
    id: styleId,
    provider,
    meta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_4__.useStyle)();
  const {
    element: {
      id: elementId
    }
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_3__.useElement)();
  const style = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => styleId && provider ? provider.actions.get(styleId, {
    elementId
  }) : null, [styleId, provider, elementId]);
  const hasContent = Boolean(customCss?.raw?.trim());
  const hasInheritedContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (hasContent) {
      return false;
    }
    return hasInheritedCustomCss(style, meta);
  }, [hasContent, style, meta]);
  if (!hasContent) {
    if (hasInheritedContent) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_indicator__WEBPACK_IMPORTED_MODULE_7__.StyleIndicator, null);
    }
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_indicator__WEBPACK_IMPORTED_MODULE_7__.StyleIndicator, {
    getColor: provider ? (0,_utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_6__.getStylesProviderThemeColor)(provider.getKey()) : undefined
  });
};
const hasInheritedCustomCss = (style, meta) => {
  if (!style || !meta) {
    return false;
  }
  const target = meta.breakpoint ?? 'desktop';
  const root = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.getBreakpointsTree)();
  const state = meta.state;
  function search(node, ancestorHasCss) {
    if (!style) {
      return undefined;
    }
    const hasHere = Boolean((0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getVariantByMeta)(style, {
      breakpoint: node.id,
      state
    })?.custom_css?.raw?.trim());
    if (node.id === target) {
      return ancestorHasCss;
    }
    for (const child of node.children ?? []) {
      const res = search(child, ancestorHasCss || hasHere);
      if (res !== undefined) {
        return res;
      }
    }
    return undefined;
  }
  return Boolean(search(root, false));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-error-fallback.tsx":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/editing-panel-error-fallback.tsx ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditorPanelErrorFallback: function() { return /* binding */ EditorPanelErrorFallback; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


function EditorPanelErrorFallback() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    role: "alert",
    sx: {
      minHeight: '100%',
      p: 2
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Alert, {
    severity: "error",
    sx: {
      mb: 2,
      maxWidth: 400,
      textAlign: 'center'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("strong", null, "Something went wrong")));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-hooks.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/editing-panel-hooks.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditingPanelHooks: function() { return /* binding */ EditingPanelHooks; }
/* harmony export */ });
/* harmony import */ var _hooks_use_open_editor_panel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../hooks/use-open-editor-panel */ "./packages/packages/core/editor-editing-panel/src/hooks/use-open-editor-panel.ts");

const EditingPanelHooks = () => {
  (0,_hooks_use_open_editor_panel__WEBPACK_IMPORTED_MODULE_0__.useOpenEditorPanel)();
  return null;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-sticky-promotion.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/editing-panel-sticky-promotion.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditingPanelStickyPromotion: function() { return /* binding */ EditingPanelStickyPromotion; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function getStickyPromotion() {
  return window.elementor?.config?.editingPanelStickyPromotion;
}
const EditingPanelStickyPromotion = () => {
  const promotion = getStickyPromotion();
  if (!promotion) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", {
    className: "elementor-panel-editor-sticky-promotion"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", {
    className: "elementor-get-pro-sticky-message"
  }, promotion.message, ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("a", {
    target: "_blank",
    rel: "noreferrer",
    href: promotion.url
  }, promotion.button_text)));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-tabs.tsx":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/editing-panel-tabs.tsx ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditingPanelTabs: function() { return /* binding */ EditingPanelTabs; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_scroll_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../contexts/scroll-context */ "./packages/packages/core/editor-editing-panel/src/contexts/scroll-context.tsx");
/* harmony import */ var _hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-default-panel-settings */ "./packages/packages/core/editor-editing-panel/src/hooks/use-default-panel-settings.ts");
/* harmony import */ var _hooks_use_state_by_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../hooks/use-state-by-element */ "./packages/packages/core/editor-editing-panel/src/hooks/use-state-by-element.ts");
/* harmony import */ var _interactions_tab__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./interactions-tab */ "./packages/packages/core/editor-editing-panel/src/components/interactions-tab.tsx");
/* harmony import */ var _settings_tab__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./settings-tab */ "./packages/packages/core/editor-editing-panel/src/components/settings-tab.tsx");
/* harmony import */ var _style_tab__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./style-tab */ "./packages/packages/core/editor-editing-panel/src/components/style-tab.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }













const EditingPanelTabs = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_5__.useElement)();
  return (
    /*#__PURE__*/
    // When switching between elements, the local states should be reset. We are using key to rerender the tabs.
    // Reference: https://react.dev/learn/preserving-and-resetting-state#resetting-a-form-with-a-key
    react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
      key: element.id
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PanelTabContent, null))
  );
};
const PanelTabContent = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_5__.useElement)();
  const editorDefaults = (0,_hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_7__.useDefaultPanelSettings)();
  const defaultComponentTab = editorDefaults.defaultTab;
  const isInteractionsActive = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.isExperimentActive)('e_interactions');
  const isPromotedElement = !!(0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getWidgetsCache)()?.[element.type]?.meta?.is_pro_promotion;
  const [storedTab, setCurrentTab] = (0,_hooks_use_state_by_element__WEBPACK_IMPORTED_MODULE_8__.useStateByElement)('tab', defaultComponentTab);
  const currentTab = isPromotedElement && storedTab === 'settings' ? 'style' : storedTab;
  const {
    getTabProps,
    getTabPanelProps,
    getTabsProps
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.useTabs)(currentTab);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_scroll_context__WEBPACK_IMPORTED_MODULE_6__.ScrollProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "column",
    sx: {
      width: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    sx: {
      ..._style_tab__WEBPACK_IMPORTED_MODULE_11__.stickyHeaderStyles,
      top: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tabs, _extends({
    variant: "fullWidth",
    size: "small",
    sx: {
      mt: 0.5
    }
  }, getTabsProps(), {
    onChange: (_, newValue) => {
      getTabsProps().onChange(_, newValue);
      setCurrentTab(newValue);
    }
  }), !isPromotedElement && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('General', 'elementor')
  }, getTabProps('settings'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Style', 'elementor')
  }, getTabProps('style'))), isInteractionsActive && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Interactions', 'elementor')
  }, getTabProps('interactions')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null)), !isPromotedElement && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.TabPanel, _extends({}, getTabPanelProps('settings'), {
    disablePadding: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_settings_tab__WEBPACK_IMPORTED_MODULE_10__.SettingsTab, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.TabPanel, _extends({}, getTabPanelProps('style'), {
    disablePadding: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab__WEBPACK_IMPORTED_MODULE_11__.StyleTab, null)), isInteractionsActive && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.TabPanel, _extends({}, getTabPanelProps('interactions'), {
    disablePadding: true
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_interactions_tab__WEBPACK_IMPORTED_MODULE_9__.InteractionsTab, null))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/editing-panel.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/editing-panel.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditingPanel: function() { return /* binding */ EditingPanel; },
/* harmony export */   PanelHeaderTopSlot: function() { return /* binding */ PanelHeaderTopSlot; },
/* harmony export */   injectIntoPanelHeaderTop: function() { return /* binding */ injectIntoPanelHeaderTop; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @elementor/menus */ "@elementor/menus");
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_elementor_menus__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _editing_panel_replacement_registry__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../editing-panel-replacement-registry */ "./packages/packages/core/editor-editing-panel/src/editing-panel-replacement-registry.tsx");
/* harmony import */ var _editing_panel_error_fallback__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./editing-panel-error-fallback */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-error-fallback.tsx");
/* harmony import */ var _editing_panel_sticky_promotion__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./editing-panel-sticky-promotion */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-sticky-promotion.tsx");
/* harmony import */ var _editing_panel_tabs__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./editing-panel-tabs */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-tabs.tsx");
















const {
  Slot: PanelHeaderTopSlot,
  inject: injectIntoPanelHeaderTop
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_6__.createLocation)();
const {
  useMenuItems
} = _elementor_menus__WEBPACK_IMPORTED_MODULE_7__.controlActionsMenu;
const EditingPanel = () => {
  const {
    element,
    elementType,
    settings
  } = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.useSelectedElementSettings)();
  const controlReplacements = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.getControlReplacements)();
  const menuItems = useMenuItems().default;
  if (!element || !elementType) {
    return null;
  }

  /* translators: %s: Element type title. */
  const panelTitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__.__)('Edit %s', 'elementor').replace('%s', elementType.title);
  const {
    component: ReplacementComponent
  } = (0,_editing_panel_replacement_registry__WEBPACK_IMPORTED_MODULE_12__.getEditingPanelReplacement)(element, elementType) ?? {};
  let panelContent = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__.PanelHeader, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__.PanelHeaderTitle, null, panelTitle), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__.AtomIcon, {
    fontSize: "small",
    sx: {
      color: 'text.tertiary'
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    sx: {
      height: 'auto',
      flex: 1,
      minHeight: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_editing_panel_tabs__WEBPACK_IMPORTED_MODULE_15__.EditingPanelTabs, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_editing_panel_sticky_promotion__WEBPACK_IMPORTED_MODULE_14__.EditingPanelStickyPromotion, null));
  if (ReplacementComponent) {
    panelContent = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ReplacementComponent, null);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_9__.ErrorBoundary, {
    fallback: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_editing_panel_error_fallback__WEBPACK_IMPORTED_MODULE_13__.EditorPanelErrorFallback, null)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_session__WEBPACK_IMPORTED_MODULE_8__.SessionStorageProvider, {
    prefix: 'elementor'
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_4__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlActionsProvider, {
    items: menuItems
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlReplacementsProvider, {
    replacements: controlReplacements
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_element_context__WEBPACK_IMPORTED_MODULE_11__.ElementProvider, {
    element: element,
    elementType: elementType,
    settings: settings
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_3__.Panel, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PanelHeaderTopSlot, null), panelContent)))))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/interactions-tab.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/interactions-tab.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InteractionsTab: function() { return /* binding */ InteractionsTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _sections_list__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./sections-list */ "./packages/packages/core/editor-editing-panel/src/components/sections-list.tsx");




const InteractionsTab = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_2__.useElement)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_sections_list__WEBPACK_IMPORTED_MODULE_3__.SectionsList, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.InteractionsTab, {
    elementId: element.id
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PanelDivider: function() { return /* binding */ PanelDivider; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const PanelDivider = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, {
  sx: {
    my: 0.5
  }
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/promotions/custom-css.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/promotions/custom-css.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomCssSection: function() { return /* binding */ CustomCssSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _style_tab_section__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../style-tab-section */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-section.tsx");





const TRACKING_DATA = {
  target_name: 'custom_css',
  location_l2: 'style'
};
const CustomCssSection = () => {
  const triggerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_3__.StyleTabSection, {
    section: {
      name: 'Custom CSS',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Custom CSS', 'elementor'),
      action: {
        component: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PromotionTrigger, {
          ref: triggerRef,
          promotionKey: "customCss",
          trackingData: TRACKING_DATA
        }),
        onClick: () => triggerRef.current?.toggle()
      }
    }
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/promotions/init.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/promotions/init.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../controls-registry/controls-registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx");
/* harmony import */ var _style_tab__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../style-tab */ "./packages/packages/core/editor-editing-panel/src/components/style-tab.tsx");
/* harmony import */ var _custom_css__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./custom-css */ "./packages/packages/core/editor-editing-panel/src/components/promotions/custom-css.tsx");




const init = () => {
  (0,_style_tab__WEBPACK_IMPORTED_MODULE_2__.injectIntoStyleTab)({
    id: 'custom-css',
    component: _custom_css__WEBPACK_IMPORTED_MODULE_3__.CustomCssSection,
    options: {
      overwrite: true
    }
  });
  if (!window.elementorPro) {
    _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_1__.controlsRegistry.register('attributes', _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.AttributesControl, 'two-columns');
    _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_1__.controlsRegistry.register('display-conditions', _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.DisplayConditionsControl, 'two-columns');
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/section-content.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SectionContent: function() { return /* binding */ SectionContent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const SectionContent = ({
  gap = 2,
  sx,
  children,
  'aria-label': ariaLabel,
  className
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
  gap: gap,
  sx: {
    ...sx
  },
  "aria-label": ariaLabel,
  className: className
}, children);

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/section.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/section.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Section: function() { return /* binding */ Section; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_state_by_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../hooks/use-state-by-element */ "./packages/packages/core/editor-editing-panel/src/hooks/use-state-by-element.ts");





function Section({
  title,
  children,
  defaultExpanded = false,
  titleEnd,
  unmountOnExit = true,
  action
}) {
  const [isOpen, setIsOpen] = (0,_hooks_use_state_by_element__WEBPACK_IMPORTED_MODULE_3__.useStateByElement)(title, !!defaultExpanded);
  const ref = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const isDisabled = !!action;
  const handleClick = () => {
    if (isDisabled) {
      action?.onClick();
    } else {
      setIsOpen(!isOpen);
    }
  };
  const id = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  const labelId = `label-${id}`;
  const contentId = `content-${id}`;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemButton, {
    id: labelId,
    "aria-controls": contentId,
    "aria-label": `${title} section`,
    onClick: handleClick,
    sx: {
      '&:hover': {
        backgroundColor: 'transparent'
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyItems: "start",
    flexGrow: 1,
    gap: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemText, {
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
  }), (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.getCollapsibleValue)(titleEnd, isOpen)), action?.component, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.CollapseIcon, {
    open: isOpen,
    color: "secondary",
    fontSize: "tiny",
    disabled: isDisabled,
    sx: {
      ml: 1
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Collapse, {
    id: contentId,
    "aria-labelledby": labelId,
    in: isOpen,
    timeout: "auto",
    unmountOnExit: unmountOnExit
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.SectionRefContext.Provider, {
    value: ref
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    ref: ref,
    gap: 2.5,
    p: 2,
    "aria-label": `${title} section content`
  }, children))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, null));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/sections-list.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/sections-list.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SectionsList: function() { return /* binding */ SectionsList; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function SectionsList(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.List, _extends({
    disablePadding: true,
    component: "div"
  }, props));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/settings-control.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/settings-control.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SettingsControl: function() { return /* binding */ SettingsControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_control__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../controls-registry/control */ "./packages/packages/core/editor-editing-panel/src/controls-registry/control.tsx");
/* harmony import */ var _controls_registry_control_type_container__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../controls-registry/control-type-container */ "./packages/packages/core/editor-editing-panel/src/controls-registry/control-type-container.tsx");
/* harmony import */ var _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../controls-registry/controls-registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx");
/* harmony import */ var _controls_registry_settings_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../controls-registry/settings-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/settings-field.tsx");
/* harmony import */ var _field_indicators_registry__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../field-indicators-registry */ "./packages/packages/core/editor-editing-panel/src/field-indicators-registry.ts");
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");









const Wrapper = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)('span')`
	display: contents;
`;
const SettingsControl = ({
  control: {
    value,
    type
  }
}) => {
  if (!_controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_5__.controlsRegistry.get(value.type)) {
    return null;
  }
  const layout = value.meta?.layout || _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_5__.controlsRegistry.getLayout(value.type);
  const controlProps = populateChildControlProps(value.props);
  if (layout === 'custom') {
    controlProps.label = value.label;
  }
  if (type === 'element-control') {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ControlLayout, {
      control: value,
      layout: layout,
      controlProps: controlProps
    });
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_settings_field__WEBPACK_IMPORTED_MODULE_6__.SettingsField, {
    bind: value.bind,
    propDisplayName: value.label || value.bind
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ControlLayout, {
    control: value,
    layout: layout,
    controlProps: controlProps
  }));
};
const ControlLayout = ({
  control,
  layout,
  controlProps
}) => {
  const controlType = control.type;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlAdornmentsProvider, {
    items: (0,_field_indicators_registry__WEBPACK_IMPORTED_MODULE_7__.getFieldIndicators)('settings')
  }, control.meta?.topDivider && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Wrapper, {
    "data-type": "settings-field"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_control_type_container__WEBPACK_IMPORTED_MODULE_4__.ControlTypeContainer, {
    layout: layout
  }, control.label && layout !== 'custom' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_8__.ControlLabel, null, control.label) : null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_control__WEBPACK_IMPORTED_MODULE_3__.Control, {
    type: controlType,
    props: controlProps
  }))));
};
function populateChildControlProps(props) {
  if (props.childControlType) {
    const childComponent = _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_5__.controlsRegistry.get(props.childControlType);
    const childPropType = _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_5__.controlsRegistry.getPropTypeUtil(props.childControlType);
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

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/settings-tab.tsx":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/settings-tab.tsx ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SettingsTab: function() { return /* binding */ SettingsTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../hooks/use-default-panel-settings */ "./packages/packages/core/editor-editing-panel/src/hooks/use-default-panel-settings.ts");
/* harmony import */ var _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/prop-dependency-utils */ "./packages/packages/core/editor-editing-panel/src/utils/prop-dependency-utils.ts");
/* harmony import */ var _section__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./section */ "./packages/packages/core/editor-editing-panel/src/components/section.tsx");
/* harmony import */ var _sections_list__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./sections-list */ "./packages/packages/core/editor-editing-panel/src/components/sections-list.tsx");
/* harmony import */ var _settings_control__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./settings-control */ "./packages/packages/core/editor-editing-panel/src/components/settings-control.tsx");








const SettingsTab = () => {
  const {
    elementType,
    element,
    settings
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_2__.useElement)();
  const settingsDefault = (0,_hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_3__.useDefaultPanelSettings)();
  const currentSettings = settings;
  const isDefaultExpanded = sectionId => !!sectionId && settingsDefault.defaultSectionsExpanded.settings?.includes(sectionId);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_session__WEBPACK_IMPORTED_MODULE_1__.SessionStorageProvider, {
    prefix: element.id
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_sections_list__WEBPACK_IMPORTED_MODULE_6__.SectionsList, null, elementType.controls.map((control, index) => {
    if (isControl(control)) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_settings_control__WEBPACK_IMPORTED_MODULE_7__.SettingsControl, {
        key: getKey(control, element),
        control: control
      });
    }
    const {
      type,
      value
    } = control;
    if (type === 'section') {
      const sectionItems = renderSectionItems({
        items: value.items,
        element,
        propsSchema: elementType.propsSchema,
        settings: currentSettings
      });
      if (!sectionItems.length) {
        return null;
      }
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section__WEBPACK_IMPORTED_MODULE_5__.Section, {
        title: value.label,
        key: type + '.' + index,
        defaultExpanded: isDefaultExpanded(value.id)
      }, sectionItems);
    }
    return null;
  })));
};
function getKey(control, element) {
  if (control.type === 'control') {
    return control.value.bind + '.' + element.id;
  }
  return control.value.type + '.' + element.id;
}
function isControl(control) {
  return control.type === 'control' || control.type === 'element-control';
}
function renderSectionItems({
  items,
  element,
  propsSchema,
  settings
}) {
  return items?.flatMap(item => {
    if (!isControl(item)) {
      // TODO: Handle 2nd level sections
      return [];
    }
    if (item.type === 'control' && isControlHiddenByDependencies(item, propsSchema, settings)) {
      return [];
    }
    return [/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_settings_control__WEBPACK_IMPORTED_MODULE_7__.SettingsControl, {
      key: getKey(item, element),
      control: item
    })];
  }) ?? [];
}
function isControlHiddenByDependencies(control, propsSchema, settings) {
  const {
    isHidden
  } = (0,_utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_4__.extractDependencyEffect)(control.value.bind, propsSchema, settings);
  return isHidden;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleIndicator: function() { return /* binding */ StyleIndicator; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const StyleIndicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)('div', {
  shouldForwardProp: prop => !['isOverridden', 'getColor'].includes(prop)
})`
	width: 5px;
	height: 5px;
	border-radius: 50%;
	background-color: ${({
  theme,
  isOverridden,
  getColor
}) => {
  if (isOverridden) {
    return theme.palette.warning.light;
  }
  const providerColor = getColor?.(theme);
  return providerColor ?? theme.palette.text.disabled;
}};
`;

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/background-section/background-section.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/background-section/background-section.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BackgroundSection: function() { return /* binding */ BackgroundSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");





const BACKGROUND_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Background', 'elementor');
const BackgroundSection = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_4__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "background",
    propDisplayName: BACKGROUND_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.BackgroundControl, null)));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-color-field.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-color-field.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BorderColorField: function() { return /* binding */ BorderColorField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const BORDER_COLOR_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Border color', 'elementor');
const BorderColorField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
  bind: "border-color",
  propDisplayName: BORDER_COLOR_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
  label: BORDER_COLOR_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ColorControl, null)));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-radius-field.tsx":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-radius-field.tsx ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BorderRadiusField: function() { return /* binding */ BorderRadiusField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_direction__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../hooks/use-direction */ "./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");









const BORDER_RADIUS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border radius', 'elementor');
const StartStartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.RadiusTopLeftIcon);
const StartEndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.RadiusTopRightIcon);
const EndStartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.RadiusBottomLeftIcon);
const EndEndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.RadiusBottomRightIcon);
const getStartStartLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Top right', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Top left', 'elementor');
const getStartStartAriaLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border top right radius', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border top left radius', 'elementor');
const getStartEndLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Top left', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Top right', 'elementor');
const getStartEndAriaLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border top left radius', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border top right radius', 'elementor');
const getEndStartLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Bottom right', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Bottom left', 'elementor');
const getEndStartAriaLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border bottom right radius', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border bottom left radius', 'elementor');
const getEndEndLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Bottom left', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Bottom right', 'elementor');
const getEndEndAriaLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border bottom left radius', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border bottom right radius', 'elementor');
const getCorners = isSiteRtl => [{
  label: getStartStartLabel(isSiteRtl),
  ariaLabel: getStartStartAriaLabel(isSiteRtl),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartStartIcon, {
    fontSize: 'tiny'
  }),
  bind: 'start-start'
}, {
  label: getStartEndLabel(isSiteRtl),
  ariaLabel: getStartEndAriaLabel(isSiteRtl),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartEndIcon, {
    fontSize: 'tiny'
  }),
  bind: 'start-end'
}, {
  label: getEndStartLabel(isSiteRtl),
  ariaLabel: getEndStartAriaLabel(isSiteRtl),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EndStartIcon, {
    fontSize: 'tiny'
  }),
  bind: 'end-start'
}, {
  label: getEndEndLabel(isSiteRtl),
  ariaLabel: getEndEndAriaLabel(isSiteRtl),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EndEndIcon, {
    fontSize: 'tiny'
  }),
  bind: 'end-end'
}];
const BorderRadiusField = () => {
  const {
    isSiteRtl
  } = (0,_hooks_use_direction__WEBPACK_IMPORTED_MODULE_7__.useDirection)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_8__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_6__.StylesField, {
    bind: 'border-radius',
    propDisplayName: BORDER_RADIUS_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.EqualUnequalSizesControl, {
    items: getCorners(isSiteRtl),
    label: BORDER_RADIUS_LABEL,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.BorderCornersIcon, {
      fontSize: 'tiny'
    }),
    tooltipLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Adjust corners', 'elementor'),
    multiSizePropTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.borderRadiusPropTypeUtil
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-section.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-section.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BorderSection: function() { return /* binding */ BorderSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _border_color_field__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./border-color-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-color-field.tsx");
/* harmony import */ var _border_radius_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./border-radius-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-radius-field.tsx");
/* harmony import */ var _border_style_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./border-style-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-style-field.tsx");
/* harmony import */ var _border_width_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./border-width-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-width-field.tsx");






const BorderSection = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_1__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_border_width_field__WEBPACK_IMPORTED_MODULE_5__.BorderWidthField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_border_color_field__WEBPACK_IMPORTED_MODULE_2__.BorderColorField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_border_style_field__WEBPACK_IMPORTED_MODULE_4__.BorderStyleField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_border_radius_field__WEBPACK_IMPORTED_MODULE_3__.BorderRadiusField, null));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-style-field.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-style-field.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BorderStyleField: function() { return /* binding */ BorderStyleField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const BORDER_TYPE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Border type', 'elementor');
const borderStyles = [{
  value: 'none',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('None', 'elementor')
}, {
  value: 'solid',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Solid', 'elementor')
}, {
  value: 'dashed',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Dashed', 'elementor')
}, {
  value: 'dotted',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Dotted', 'elementor')
}, {
  value: 'double',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Double', 'elementor')
}, {
  value: 'groove',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Groove', 'elementor')
}, {
  value: 'ridge',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Ridge', 'elementor')
}, {
  value: 'inset',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Inset', 'elementor')
}, {
  value: 'outset',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Outset', 'elementor')
}];
const BorderStyleField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
  bind: "border-style",
  propDisplayName: BORDER_TYPE_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
  label: BORDER_TYPE_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
  options: borderStyles
})));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-width-field.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-width-field.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BorderWidthField: function() { return /* binding */ BorderWidthField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_direction__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../hooks/use-direction */ "./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts");








const BORDER_WIDTH_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border width', 'elementor');
const InlineStartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SideRightIcon);
const InlineEndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SideLeftIcon);
const getEdges = isSiteRtl => [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Top', 'elementor'),
  ariaLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border top width', 'elementor'),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SideTopIcon, {
    fontSize: 'tiny'
  }),
  bind: 'block-start'
}, {
  label: isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Left', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Right', 'elementor'),
  ariaLabel: isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border left width', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border right width', 'elementor'),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InlineStartIcon, {
    fontSize: 'tiny'
  }),
  bind: 'inline-end'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Bottom', 'elementor'),
  ariaLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border bottom width', 'elementor'),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SideBottomIcon, {
    fontSize: 'tiny'
  }),
  bind: 'block-end'
}, {
  label: isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Right', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Left', 'elementor'),
  ariaLabel: isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border right width', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Border left width', 'elementor'),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(InlineEndIcon, {
    fontSize: 'tiny'
  }),
  bind: 'inline-start'
}];
const BorderWidthField = () => {
  const {
    isSiteRtl
  } = (0,_hooks_use_direction__WEBPACK_IMPORTED_MODULE_7__.useDirection)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_6__.StylesField, {
    bind: 'border-width',
    propDisplayName: BORDER_WIDTH_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.EqualUnequalSizesControl, {
    items: getEdges(isSiteRtl),
    label: BORDER_WIDTH_LABEL,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SideAllIcon, {
      fontSize: 'tiny'
    }),
    tooltipLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Adjust borders', 'elementor'),
    multiSizePropTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.borderWidthPropTypeUtil
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/blend-mode-field.tsx":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/blend-mode-field.tsx ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BlendModeField: function() { return /* binding */ BlendModeField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const BLEND_MODE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Blend mode', 'elementor');
const blendModeOptions = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Normal', 'elementor'),
  value: 'normal'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Multiply', 'elementor'),
  value: 'multiply'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Screen', 'elementor'),
  value: 'screen'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Overlay', 'elementor'),
  value: 'overlay'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Darken', 'elementor'),
  value: 'darken'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Lighten', 'elementor'),
  value: 'lighten'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Color dodge', 'elementor'),
  value: 'color-dodge'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Color burn', 'elementor'),
  value: 'color-burn'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Saturation', 'elementor'),
  value: 'saturation'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Color', 'elementor'),
  value: 'color'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Difference', 'elementor'),
  value: 'difference'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Exclusion', 'elementor'),
  value: 'exclusion'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Hue', 'elementor'),
  value: 'hue'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Luminosity', 'elementor'),
  value: 'luminosity'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Soft light', 'elementor'),
  value: 'soft-light'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Hard light', 'elementor'),
  value: 'hard-light'
}];
const BlendModeField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "mix-blend-mode",
    propDisplayName: BLEND_MODE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: BLEND_MODE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    options: blendModeOptions
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/effects-section.tsx":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/effects-section.tsx ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EffectsSection: function() { return /* binding */ EffectsSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _utils_can_element_have_children__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../utils/can-element-have-children */ "./packages/packages/core/editor-editing-panel/src/utils/can-element-have-children.ts");
/* harmony import */ var _utils_get_recently_used_styles__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../utils/get-recently-used-styles */ "./packages/packages/core/editor-editing-panel/src/utils/get-recently-used-styles.ts");
/* harmony import */ var _panel_divider__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../panel-divider */ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _blend_mode_field__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./blend-mode-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/blend-mode-field.tsx");
/* harmony import */ var _opacity_control_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./opacity-control-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/opacity-control-field.tsx");












const BOX_SHADOW_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Box shadow', 'elementor');
const FILTER_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Filters', 'elementor');
const TRANSFORM_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Transform', 'elementor');
const BACKDROP_FILTER_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Backdrop filters', 'elementor');
const TRANSITIONS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Transitions', 'elementor');
const EffectsSection = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_3__.useElement)();
  const {
    meta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_4__.useStyle)();
  const canHaveChildren = (0,_utils_can_element_have_children__WEBPACK_IMPORTED_MODULE_6__.canElementHaveChildren)(element?.id ?? '');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_9__.SectionContent, {
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_blend_mode_field__WEBPACK_IMPORTED_MODULE_10__.BlendModeField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_opacity_control_field__WEBPACK_IMPORTED_MODULE_11__.OpacityControlField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "box-shadow",
    propDisplayName: BOX_SHADOW_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.BoxShadowRepeaterControl, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "transform",
    propDisplayName: TRANSFORM_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.TransformRepeaterControl, {
    showChildrenPerspective: canHaveChildren
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "transition",
    propDisplayName: TRANSITIONS_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.TransitionRepeaterControl, {
    currentStyleState: meta.state,
    recentlyUsedListGetter: () => (0,_utils_get_recently_used_styles__WEBPACK_IMPORTED_MODULE_7__.getRecentlyUsedList)(element?.id ?? '')
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "filter",
    propDisplayName: FILTER_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.FilterRepeaterControl, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "backdrop-filter",
    propDisplayName: BACKDROP_FILTER_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.FilterRepeaterControl, {
    filterPropName: "backdrop-filter"
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/opacity-control-field.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/opacity-control-field.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OpacityControlField: function() { return /* binding */ OpacityControlField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const OPACITY_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Opacity', 'elementor');
const OpacityControlField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: 'opacity',
    propDisplayName: OPACITY_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    ref: rowRef,
    label: OPACITY_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    units: ['%'],
    anchorRef: rowRef,
    defaultUnit: "%"
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-content-field.tsx":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-content-field.tsx ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AlignContentField: function() { return /* binding */ AlignContentField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");
/* harmony import */ var _utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/rotated-icon */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









const ALIGN_CONTENT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Align content', 'elementor');
const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyTopIcon);
const EndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyBottomIcon);
const iconProps = {
  isClockwise: false,
  offset: 0,
  disableRotationForReversed: true
};
const options = [{
  value: 'start',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Start', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: StartIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'center',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Center', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyCenterIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'end',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('End', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: EndIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'space-between',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Space between', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifySpaceBetweenVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'space-around',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Space around', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifySpaceAroundVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'space-evenly',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Space evenly', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyDistributeVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}];
const AlignContentField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
  bind: "align-content",
  propDisplayName: ALIGN_CONTENT_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
  label: ALIGN_CONTENT_LABEL,
  direction: "column"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: options,
  fullWidth: true
}))));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-items-field.tsx":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-items-field.tsx ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AlignItemsField: function() { return /* binding */ AlignItemsField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");
/* harmony import */ var _utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/rotated-icon */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









const ALIGN_ITEMS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Align items', 'elementor');
const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignLeftIcon);
const EndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignRightIcon);
const iconProps = {
  isClockwise: false,
  offset: 90
};
const options = [{
  value: 'start',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Start', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: StartIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'center',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Center', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignCenterIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'end',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('End', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: EndIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'stretch',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Stretch', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutDistributeVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}];
const AlignItemsField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "align-items",
    propDisplayName: ALIGN_ITEMS_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
    label: ALIGN_ITEMS_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-self-child-field.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-self-child-field.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AlignSelfChild: function() { return /* binding */ AlignSelfChild; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");
/* harmony import */ var _utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/rotated-icon */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









const ALIGN_SELF_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Align self', 'elementor');
const ALIGN_SELF_CHILD_OFFSET_MAP = {
  row: 90,
  'row-reverse': 90,
  column: 0,
  'column-reverse': 0
};
const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignLeftIcon);
const EndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignRightIcon);
const iconProps = {
  isClockwise: false
};
const getOptions = parentStyleDirection => [{
  value: 'start',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Start', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: StartIcon,
    size: size,
    offset: ALIGN_SELF_CHILD_OFFSET_MAP[parentStyleDirection]
  }, iconProps)),
  showTooltip: true
}, {
  value: 'center',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Center', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignCenterIcon,
    size: size,
    offset: ALIGN_SELF_CHILD_OFFSET_MAP[parentStyleDirection]
  }, iconProps)),
  showTooltip: true
}, {
  value: 'end',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('End', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: EndIcon,
    size: size,
    offset: ALIGN_SELF_CHILD_OFFSET_MAP[parentStyleDirection]
  }, iconProps)),
  showTooltip: true
}, {
  value: 'stretch',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Stretch', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutDistributeVerticalIcon,
    size: size,
    offset: ALIGN_SELF_CHILD_OFFSET_MAP[parentStyleDirection]
  }, iconProps)),
  showTooltip: true
}];
const AlignSelfChild = ({
  parentStyleDirection
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
  bind: "align-self",
  propDisplayName: ALIGN_SELF_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
  label: ALIGN_SELF_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: getOptions(parentStyleDirection)
}))));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-self-grid-child-field.tsx":
/*!**********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-self-grid-child-field.tsx ***!
  \**********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AlignSelfGridChild: function() { return /* binding */ AlignSelfGridChild; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");







const ALIGN_SELF_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Align self', 'elementor');
const ALIGN_SELF_CHILD_OFFSET_MAP = {
  row: 0,
  column: -90
};
const AlignSelfGridChild = ({
  parentStyleDirection
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
  bind: "align-self",
  propDisplayName: ALIGN_SELF_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_5__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_6__.StylesFieldLayout, {
  label: ALIGN_SELF_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: getOptions(parentStyleDirection ?? 'row')
}))));
const RotatedIcon = ({
  icon: Icon,
  size,
  offset
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
  fontSize: size,
  sx: {
    rotate: `${offset}deg`
  }
});
const getOptions = parentStyleDirection => {
  const offset = ALIGN_SELF_CHILD_OFFSET_MAP[parentStyleDirection.replace('dense', '').trim()];
  return [{
    value: 'start',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Start', 'elementor'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RotatedIcon, {
      icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyTopIcon,
      size: size,
      offset: offset
    }),
    showTooltip: true
  }, {
    value: 'center',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Center', 'elementor'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RotatedIcon, {
      icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyCenterIcon,
      size: size,
      offset: offset
    }),
    showTooltip: true
  }, {
    value: 'end',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('End', 'elementor'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RotatedIcon, {
      icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyBottomIcon,
      size: size,
      offset: offset
    }),
    showTooltip: true
  }, {
    value: 'stretch',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Stretch', 'elementor'),
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RotatedIcon, {
      icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutDistributeVerticalIcon,
      size: size,
      offset: offset
    }),
    showTooltip: true
  }];
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/display-field.tsx":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/display-field.tsx ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DisplayField: function() { return /* binding */ DisplayField; },
/* harmony export */   useDisplayPlaceholderValue: function() { return /* binding */ useDisplayPlaceholderValue; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../contexts/styles-inheritance-context */ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx");
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const DISPLAY_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Display', 'elementor');
const displayFieldItems = [{
  value: 'block',
  renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Block', 'elementor'),
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Block', 'elementor'),
  showTooltip: true
}, {
  value: 'flex',
  renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Flex', 'elementor'),
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Flex', 'elementor'),
  showTooltip: true
}, {
  value: 'grid',
  renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Grid', 'elementor'),
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Grid', 'elementor'),
  showTooltip: true
}, {
  value: 'none',
  renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('None', 'elementor'),
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('None', 'elementor'),
  showTooltip: true
}, {
  value: 'inline-block',
  renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('In-blk', 'elementor'),
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Inline-block', 'elementor'),
  showTooltip: true
}, {
  value: 'inline-flex',
  renderContent: () => (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('In-flx', 'elementor'),
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Inline-flex', 'elementor'),
  showTooltip: true
}];
const DisplayField = () => {
  const placeholder = useDisplayPlaceholderValue();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "display",
    propDisplayName: DISPLAY_LABEL,
    placeholder: placeholder
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
    label: DISPLAY_LABEL,
    direction: "column"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: displayFieldItems,
    maxItems: 4,
    fullWidth: true
  })));
};

// TODO - placing this logic deliberately here, and will be removed once applied automatically to all style fields as part of ED-18491
const useDisplayPlaceholderValue = () => (0,_contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_3__.useStylesInheritanceChain)(['display'])[0]?.value ?? undefined;

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-direction-field.tsx":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-direction-field.tsx ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FlexDirectionField: function() { return /* binding */ FlexDirectionField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");








const FLEX_DIRECTION_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Direction', 'elementor');
const options = [{
  value: 'row',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Row', 'elementor'),
  renderContent: ({
    size
  }) => {
    const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon);
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartIcon, {
      fontSize: size
    });
  },
  showTooltip: true
}, {
  value: 'column',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Column', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownSmallIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'row-reverse',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Reversed row', 'elementor'),
  renderContent: ({
    size
  }) => {
    const EndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowLeftIcon);
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EndIcon, {
      fontSize: size
    });
  },
  showTooltip: true
}, {
  value: 'column-reverse',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Reversed column', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowUpSmallIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const FlexDirectionField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "flex-direction",
    propDisplayName: FLEX_DIRECTION_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
    label: FLEX_DIRECTION_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-order-field.tsx":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-order-field.tsx ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FIRST_DEFAULT_VALUE: function() { return /* binding */ FIRST_DEFAULT_VALUE; },
/* harmony export */   FlexOrderField: function() { return /* binding */ FlexOrderField; },
/* harmony export */   LAST_DEFAULT_VALUE: function() { return /* binding */ LAST_DEFAULT_VALUE; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");












const ORDER_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Order', 'elementor');
const FIRST_DEFAULT_VALUE = -99999;
const LAST_DEFAULT_VALUE = 99999;
const FIRST = 'first';
const LAST = 'last';
const CUSTOM = 'custom';
const orderValueMap = {
  [FIRST]: FIRST_DEFAULT_VALUE,
  [LAST]: LAST_DEFAULT_VALUE
};
const items = [{
  value: FIRST,
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('First', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowUpSmallIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: LAST,
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Last', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownSmallIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: CUSTOM,
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Custom', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.PencilIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const FlexOrderField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "order",
    propDisplayName: ORDER_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_9__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FlexOrderFieldContent, null))));
};
function FlexOrderFieldContent() {
  const {
    value: order,
    setValue: setOrder,
    canEdit
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__.useStylesField)('order', {
    history: {
      propDisplayName: ORDER_LABEL
    }
  });
  const {
    placeholder
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const placeholderValue = placeholder;
  const currentGroup = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => getGroupControlValue(order?.value ?? null), [order]);
  const [activeGroup, setActiveGroup] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(currentGroup);
  const [customLocked, setCustomLocked] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!customLocked) {
      setActiveGroup(currentGroup);
    }
  }, [currentGroup, customLocked]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (order === null) {
      setCustomLocked(false);
    }
  }, [order]);
  const groupPlaceholder = getGroupControlValue(placeholderValue?.value ?? null);
  const handleToggleButtonChange = group => {
    setActiveGroup(group);
    setCustomLocked(group === CUSTOM);
    if (CUSTOM === group) {
      setOrder({
        $$type: 'number',
        value: null
      });
      return;
    }
    if (FIRST === group) {
      setOrder({
        $$type: 'number',
        value: orderValueMap[group]
      });
      return;
    }
    if (LAST === group) {
      setOrder({
        $$type: 'number',
        value: orderValueMap[group]
      });
      return;
    }
    setOrder(null);
  };
  const isCustomVisible = CUSTOM === activeGroup || CUSTOM === groupPlaceholder;
  const orderPlaceholder = CUSTOM === groupPlaceholder ? String(placeholderValue?.value ?? null) : '';
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_10__.StylesFieldLayout, {
    label: ORDER_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlToggleButtonGroup, {
    items: items,
    value: activeGroup,
    onChange: handleToggleButtonChange,
    exclusive: true,
    placeholder: groupPlaceholder,
    disabled: !canEdit
  })), isCustomVisible && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    container: true,
    gap: 2,
    alignItems: "center",
    flexWrap: "nowrap"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    item: true,
    xs: 6
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_8__.ControlLabel, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Custom order', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    item: true,
    xs: 6,
    sx: {
      display: 'flex',
      justifyContent: 'end'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberControl, {
    min: FIRST_DEFAULT_VALUE + 1,
    max: LAST_DEFAULT_VALUE - 1,
    shouldForceInt: true,
    placeholder: orderPlaceholder
  }))));
}
const getGroupControlValue = order => {
  if (LAST_DEFAULT_VALUE === order) {
    return LAST;
  }
  if (FIRST_DEFAULT_VALUE === order) {
    return FIRST;
  }
  if (null !== order) {
    return CUSTOM;
  }
  return null;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-size-field.tsx":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-size-field.tsx ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FlexSizeField: function() { return /* binding */ FlexSizeField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");











const FLEX_SIZE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Flex Size', 'elementor');
const DEFAULT = 1;
const items = [{
  value: 'flex-grow',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Grow', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ExpandIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'flex-shrink',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Shrink', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ShrinkIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'custom',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Custom', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PencilIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const FlexSizeField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_8__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "flex",
    propDisplayName: FLEX_SIZE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FlexSizeFieldContent, null))));
};
const FlexSizeFieldContent = () => {
  const {
    value,
    setValue,
    canEdit
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__.useStylesField)('flex', {
    history: {
      propDisplayName: FLEX_SIZE_LABEL
    }
  });
  const {
    placeholder
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const flexValues = extractFlexValues(value);
  const currentGroup = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => getActiveGroup(flexValues), [flexValues]);
  const [activeGroup, setActiveGroup] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(currentGroup);
  const [customLocked, setCustomLocked] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!customLocked) {
      setActiveGroup(currentGroup);
    }
  }, [currentGroup, customLocked]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (value === null) {
      setCustomLocked(false);
    }
  }, [value]);
  const onChangeGroup = (group = null) => {
    setActiveGroup(group);
    setCustomLocked(group === 'custom');
    const newFlexValue = createFlexValueForGroup(group, value);
    setValue(newFlexValue);
  };
  const groupPlaceholder = getActiveGroup(extractFlexValues(placeholder));
  const isCustomVisible = 'custom' === activeGroup || 'custom' === groupPlaceholder;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_9__.StylesFieldLayout, {
    label: FLEX_SIZE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlToggleButtonGroup, {
    value: activeGroup ?? null,
    placeholder: groupPlaceholder ?? null,
    onChange: onChangeGroup,
    disabled: !canEdit,
    items: items,
    exclusive: true
  })), isCustomVisible && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FlexCustomField, null));
};
function extractFlexValues(source) {
  return {
    grow: source?.value?.flexGrow?.value ?? null,
    shrink: source?.value?.flexShrink?.value ?? null,
    basis: source?.value?.flexBasis?.value ?? null
  };
}
const createFlexValueForGroup = (group, flexValue) => {
  if (!group) {
    return null;
  }
  if (group === 'flex-grow') {
    return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.flexPropTypeUtil.create({
      flexGrow: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.create(DEFAULT),
      flexShrink: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.create(0),
      flexBasis: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.sizePropTypeUtil.create({
        unit: 'auto',
        size: ''
      })
    });
  }
  if (group === 'flex-shrink') {
    return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.flexPropTypeUtil.create({
      flexGrow: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.create(0),
      flexShrink: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.create(DEFAULT),
      flexBasis: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.sizePropTypeUtil.create({
        unit: 'auto',
        size: ''
      })
    });
  }
  if (group === 'custom') {
    if (flexValue) {
      return flexValue;
    }
    return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.flexPropTypeUtil.create({
      flexGrow: null,
      flexShrink: null,
      flexBasis: null
    });
  }
  return null;
};
const FlexCustomField = () => {
  const flexBasisRowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const context = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.flexPropTypeUtil);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, context, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_9__.StylesFieldLayout, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Grow', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: "flexGrow"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberControl, {
    min: 0,
    shouldForceInt: true
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_9__.StylesFieldLayout, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Shrink', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: "flexShrink"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberControl, {
    min: 0,
    shouldForceInt: true
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_9__.StylesFieldLayout, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Basis', 'elementor'),
    ref: flexBasisRowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: "flexBasis"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    extendedOptions: ['auto'],
    anchorRef: flexBasisRowRef
  })))));
};
const getActiveGroup = ({
  grow,
  shrink,
  basis
}) => {
  if (null === grow && null === shrink && !basis) {
    return null;
  }
  const isAutoBasis = basis === null || typeof basis === 'object' && basis.unit === 'auto';
  if (basis && !isAutoBasis) {
    return 'custom';
  }
  if (grow === DEFAULT && (shrink === null || shrink === 0) && isAutoBasis) {
    return 'flex-grow';
  }
  if (shrink === DEFAULT && (grow === null || grow === 0) && isAutoBasis) {
    return 'flex-shrink';
  }
  return 'custom';
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/gap-control-field.tsx":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/gap-control-field.tsx ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GapControlField: function() { return /* binding */ GapControlField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");




const GAPS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Gaps', 'elementor');
const GapControlField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: 'gap',
    propDisplayName: GAPS_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.GapControl, {
    label: GAPS_LABEL
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-auto-flow-field.tsx":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-auto-flow-field.tsx ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridAutoFlowField: function() { return /* binding */ GridAutoFlowField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");









const AUTO_FLOW_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Auto flow', 'elementor');
const DENSE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Dense', 'elementor');
const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon);
const directionOptions = [{
  value: 'row',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Row', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'column',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Column', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownSmallIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const parseAutoFlow = value => {
  if (!value) {
    return {
      direction: null,
      dense: false
    };
  }
  const dense = value.includes('dense');
  const direction = value.replace(/\s*dense\s*/, '').trim();
  return {
    direction: direction || 'row',
    dense
  };
};
const composeAutoFlow = (direction, dense) => {
  return dense ? `${direction} dense` : direction;
};
const GridAutoFlowFieldContent = () => {
  const {
    value,
    setValue,
    canEdit
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__.useStylesField)('grid-auto-flow', {
    history: {
      propDisplayName: AUTO_FLOW_LABEL
    }
  });
  const {
    placeholder
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const {
    direction,
    dense
  } = parseAutoFlow(value?.value ?? null);
  const directionPlaceholder = parseAutoFlow(placeholder?.value ?? null).direction;
  const handleDirectionChange = newDirection => {
    if (!newDirection) {
      setValue(null);
      return;
    }
    setValue({
      $$type: 'string',
      value: composeAutoFlow(newDirection, dense)
    });
  };
  const handleDenseToggle = () => {
    const nextDirection = direction ?? 'row';
    setValue({
      $$type: 'string',
      value: composeAutoFlow(nextDirection, !dense)
    });
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_8__.StylesFieldLayout, {
    label: AUTO_FLOW_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    container: true,
    gap: 1,
    flexWrap: "nowrap",
    alignItems: "center",
    justifyContent: "flex-end"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    item: true,
    sx: {
      width: 64,
      maxWidth: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlToggleButtonGroup, {
    items: directionOptions,
    value: direction,
    placeholder: directionPlaceholder,
    onChange: handleDirectionChange,
    exclusive: true,
    fullWidth: true,
    disabled: !canEdit
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
    item: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: DENSE_LABEL,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ToggleButton, {
    value: "dense",
    selected: dense,
    onChange: handleDenseToggle,
    size: "tiny",
    "aria-label": DENSE_LABEL,
    disabled: !canEdit
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutDashboardIcon, {
    fontSize: "tiny"
  }))))));
};
const GridAutoFlowField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
  bind: "grid-auto-flow",
  propDisplayName: AUTO_FLOW_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridAutoFlowFieldContent, null)));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-auto-track-fields.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-auto-track-fields.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridAutoTrackFields: function() { return /* binding */ GridAutoTrackFields; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");







const DEFAULT_UNIT = 'fr';
const AUTO_ROWS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Auto rows', 'elementor');
const AUTO_COLUMNS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Auto columns', 'elementor');
const AUTO_ROWS_TOOLTIP = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Set the size for new rows created automatically when content exceeds the defined grid.', 'elementor');
const AUTO_COLUMNS_TOOLTIP = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Set the size for new columns created automatically when content exceeds the defined grid.', 'elementor');
const GridAutoTrackField = ({
  bind,
  infoTooltip,
  label,
  rowRef
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
  bind: bind,
  propDisplayName: label
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
  infoTooltip: infoTooltip,
  label: label,
  ref: rowRef
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
  enablePropTypeUnits: true,
  defaultUnit: DEFAULT_UNIT,
  anchorRef: rowRef
})));
const GridAutoTrackFields = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    gap: 2,
    pt: 2,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridAutoTrackField, {
    bind: "grid-auto-rows",
    infoTooltip: AUTO_ROWS_TOOLTIP,
    label: AUTO_ROWS_LABEL,
    rowRef: rowRef
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridAutoTrackField, {
    bind: "grid-auto-columns",
    infoTooltip: AUTO_COLUMNS_TOOLTIP,
    label: AUTO_COLUMNS_LABEL,
    rowRef: rowRef
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-justify-items-field.tsx":
/*!*******************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-justify-items-field.tsx ***!
  \*******************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridJustifyItemsField: function() { return /* binding */ GridJustifyItemsField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");








const JUSTIFY_ITEMS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Justify items', 'elementor');
const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignLeftIcon);
const EndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignRightIcon);
const options = [{
  value: 'start',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Start', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StartIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'center',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Center', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutAlignCenterIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'end',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('End', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(EndIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'stretch',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Stretch', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LayoutDistributeVerticalIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const GridJustifyItemsField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
  bind: "justify-items",
  propDisplayName: JUSTIFY_ITEMS_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
  label: JUSTIFY_ITEMS_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: options
}))));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-outline-field.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-outline-field.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridOutlineField: function() { return /* binding */ GridOutlineField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const GRID_OUTLINE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Show Grid Outline', 'elementor');
const GridOutlineField = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_4__.useElement)();
  const settings = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.useElementEditorSettings)(element.id);
  const value = settings?.grid_outline ?? true;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
    label: GRID_OUTLINE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      display: 'flex',
      justifyContent: 'flex-end'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Switch, {
    "aria-label": GRID_OUTLINE_LABEL,
    checked: value,
    onChange: event => {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.updateElementEditorSettings)({
        elementId: element.id,
        settings: {
          grid_outline: event.target.checked
        }
      });
    },
    size: "small"
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-size-field.tsx":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-size-field.tsx ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridSizeFields: function() { return /* binding */ GridSizeFields; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");
/* harmony import */ var _utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/grid-track-value */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/grid-track-value.ts");










const SizeFieldWrapper = ({
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlActions, null, children);
const GridTrackSizeInput = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.createControl)(props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeComponent, {
  units: _utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.UNITS,
  value: props.value,
  placeholder: props.placeholder,
  defaultUnit: _utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.FR,
  setValue: props.setValue,
  onBlur: () => {},
  min: 1,
  anchorRef: props.anchorRef,
  SizeFieldWrapper: SizeFieldWrapper
}));
const GridTrackFieldContent = ({
  cssProp,
  label
}) => {
  const {
    value,
    setValue
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_5__.useStylesField)(cssProp, {
    history: {
      propDisplayName: label
    }
  });
  const {
    placeholder: inheritedPlaceholder
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const anchorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const local = (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.parseValue)(value);
  const inherited = (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.parseValue)(inheritedPlaceholder);
  const displayValue = local.kind !== 'empty' ? (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.toSizeInput)(local) : (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.toSizeInput)(_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.EMPTY, (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.unitOf)(inherited));
  const placeholder = (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.toPlaceholder)(inherited);
  const handleChange = raw => {
    const next = (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.fromSizeInput)(raw);
    if (next.kind === 'empty' && local.kind !== 'empty' && raw.unit !== (0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.unitOf)(local)) {
      return;
    }
    setValue((0,_utils_grid_track_value__WEBPACK_IMPORTED_MODULE_8__.toPropValue)(next));
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
    label: label,
    direction: "column"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("div", {
    ref: anchorRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridTrackSizeInput, {
    value: displayValue,
    placeholder: placeholder,
    setValue: handleChange,
    anchorRef: anchorRef
  })));
};
const GridTrackField = ({
  cssProp,
  label
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
  bind: cssProp,
  propDisplayName: label
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridTrackFieldContent, {
  cssProp: cssProp,
  label: label
})));
const GridSizeFields = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
  container: true,
  gap: 2,
  flexWrap: "nowrap"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
  item: true,
  xs: 6
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridTrackField, {
  cssProp: "grid-template-columns",
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Columns', 'elementor')
})), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
  item: true,
  xs: 6
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridTrackField, {
  cssProp: "grid-template-rows",
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Rows', 'elementor')
})));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-span-field.tsx":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-span-field.tsx ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridSpanFields: function() { return /* binding */ GridSpanFields; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");







const GridSpanFieldContent = ({
  label
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_6__.StylesFieldLayout, {
    label: label,
    direction: "column"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.GridSpanControl, null));
};
const GridSpanField = ({
  cssProp,
  label
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
  bind: cssProp,
  propDisplayName: label
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_5__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridSpanFieldContent, {
  cssProp: cssProp,
  label: label
})));
const GridSpanFields = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
  container: true,
  gap: 2,
  flexWrap: "nowrap"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
  item: true,
  xs: 6
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridSpanField, {
  cssProp: "grid-column",
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Grid column', 'elementor')
})), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
  item: true,
  xs: 6
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridSpanField, {
  cssProp: "grid-row",
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Grid row', 'elementor')
})));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/justify-content-field.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/justify-content-field.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   JustifyContentField: function() { return /* binding */ JustifyContentField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");
/* harmony import */ var _utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/rotated-icon */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }









const JUSTIFY_CONTENT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Justify content', 'elementor');
const StartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyTopIcon);
const EndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyBottomIcon);
const iconProps = {
  isClockwise: true,
  offset: -90
};
const options = [{
  value: 'flex-start',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Start', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: StartIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'center',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Center', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyCenterIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'flex-end',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('End', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: EndIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'space-between',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Space between', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifySpaceBetweenVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'space-around',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Space around', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifySpaceAroundVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}, {
  value: 'space-evenly',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Space evenly', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_8__.RotatedIcon, _extends({
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.JustifyDistributeVerticalIcon,
    size: size
  }, iconProps)),
  showTooltip: true
}];
const JustifyContentField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
  bind: "justify-content",
  propDisplayName: JUSTIFY_CONTENT_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
  label: JUSTIFY_CONTENT_LABEL,
  direction: "column"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: options,
  fullWidth: true
}))));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/layout-section.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/layout-section.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GridFieldsSlot: function() { return /* binding */ GridFieldsSlot; },
/* harmony export */   LayoutSection: function() { return /* binding */ LayoutSection; },
/* harmony export */   injectIntoGridFields: function() { return /* binding */ injectIntoGridFields; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _hooks_use_computed_style__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-computed-style */ "./packages/packages/core/editor-editing-panel/src/hooks/use-computed-style.ts");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _panel_divider__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../panel-divider */ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../style-tab-collapsible-content */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-collapsible-content.tsx");
/* harmony import */ var _align_content_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./align-content-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-content-field.tsx");
/* harmony import */ var _align_items_field__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./align-items-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-items-field.tsx");
/* harmony import */ var _align_self_child_field__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./align-self-child-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-self-child-field.tsx");
/* harmony import */ var _align_self_grid_child_field__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./align-self-grid-child-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/align-self-grid-child-field.tsx");
/* harmony import */ var _display_field__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./display-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/display-field.tsx");
/* harmony import */ var _flex_direction_field__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./flex-direction-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-direction-field.tsx");
/* harmony import */ var _flex_order_field__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./flex-order-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-order-field.tsx");
/* harmony import */ var _flex_size_field__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./flex-size-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/flex-size-field.tsx");
/* harmony import */ var _gap_control_field__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./gap-control-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/gap-control-field.tsx");
/* harmony import */ var _grid_auto_flow_field__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./grid-auto-flow-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-auto-flow-field.tsx");
/* harmony import */ var _grid_auto_track_fields__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./grid-auto-track-fields */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-auto-track-fields.tsx");
/* harmony import */ var _grid_justify_items_field__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./grid-justify-items-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-justify-items-field.tsx");
/* harmony import */ var _grid_outline_field__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./grid-outline-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-outline-field.tsx");
/* harmony import */ var _grid_size_field__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./grid-size-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-size-field.tsx");
/* harmony import */ var _grid_span_field__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./grid-span-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/grid-span-field.tsx");
/* harmony import */ var _justify_content_field__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./justify-content-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/justify-content-field.tsx");
/* harmony import */ var _wrap_field__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./wrap-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/wrap-field.tsx");




























const DISPLAY_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Display', 'elementor');
const FLEX_WRAP_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Flex wrap', 'elementor');
const DEFAULT_PARENT_FLOW_DIRECTION = 'row';
const {
  Slot: GridFieldsSlot,
  inject: injectIntoGridFields
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_3__.createLocation)();
const LayoutSection = () => {
  const {
    value: display
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_7__.useStylesField)('display', {
    history: {
      propDisplayName: DISPLAY_LABEL
    }
  });
  const displayPlaceholder = (0,_display_field__WEBPACK_IMPORTED_MODULE_15__.useDisplayPlaceholderValue)();
  const isDisplayFlex = shouldDisplayFlexFields(display, displayPlaceholder);
  const isDisplayGrid = 'grid' === (display?.value ?? displayPlaceholder?.value);
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_5__.useElement)();
  const parent = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.useParentElement)(element.id);
  const parentStyle = (0,_hooks_use_computed_style__WEBPACK_IMPORTED_MODULE_6__.useComputedStyle)(parent?.id || null);
  const getParentStyleDirection = () => {
    if ('flex' === parentStyle?.display) {
      return parentStyle?.flexDirection ?? DEFAULT_PARENT_FLOW_DIRECTION;
    }
    if ('grid' === parentStyle?.display) {
      return parentStyle?.gridAutoFlow ?? DEFAULT_PARENT_FLOW_DIRECTION;
    }
    return DEFAULT_PARENT_FLOW_DIRECTION;
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_9__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_display_field__WEBPACK_IMPORTED_MODULE_15__.DisplayField, null), isDisplayFlex && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FlexFields, null), 'flex' === parentStyle?.display && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FlexChildFields, {
    parentStyleDirection: getParentStyleDirection()
  }), isDisplayGrid && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridFields, null), 'grid' === parentStyle?.display && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridChildFields, {
    parentStyleDirection: getParentStyleDirection()
  }));
};
const FlexFields = () => {
  const {
    value: flexWrap
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_7__.useStylesField)('flex-wrap', {
    history: {
      propDisplayName: FLEX_WRAP_LABEL
    }
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_flex_direction_field__WEBPACK_IMPORTED_MODULE_16__.FlexDirectionField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_justify_content_field__WEBPACK_IMPORTED_MODULE_26__.JustifyContentField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_align_items_field__WEBPACK_IMPORTED_MODULE_12__.AlignItemsField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_gap_control_field__WEBPACK_IMPORTED_MODULE_19__.GapControlField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_wrap_field__WEBPACK_IMPORTED_MODULE_27__.WrapField, null), ['wrap', 'wrap-reverse'].includes(flexWrap?.value) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_align_content_field__WEBPACK_IMPORTED_MODULE_11__.AlignContentField, null));
};
const GridFields = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_outline_field__WEBPACK_IMPORTED_MODULE_23__.GridOutlineField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_size_field__WEBPACK_IMPORTED_MODULE_24__.GridSizeFields, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_auto_flow_field__WEBPACK_IMPORTED_MODULE_20__.GridAutoFlowField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(GridFieldsSlot, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_10__.StyleTabCollapsibleContent, {
  fields: ['grid-auto-rows', 'grid-auto-columns']
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_auto_track_fields__WEBPACK_IMPORTED_MODULE_21__.GridAutoTrackFields, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_gap_control_field__WEBPACK_IMPORTED_MODULE_19__.GapControlField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_justify_items_field__WEBPACK_IMPORTED_MODULE_22__.GridJustifyItemsField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_align_items_field__WEBPACK_IMPORTED_MODULE_12__.AlignItemsField, null));
const FlexChildFields = ({
  parentStyleDirection
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Flex child', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_align_self_child_field__WEBPACK_IMPORTED_MODULE_13__.AlignSelfChild, {
  parentStyleDirection: parentStyleDirection
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_flex_order_field__WEBPACK_IMPORTED_MODULE_17__.FlexOrderField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_flex_size_field__WEBPACK_IMPORTED_MODULE_18__.FlexSizeField, null));
const GridChildFields = ({
  parentStyleDirection
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_8__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Grid child', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_grid_span_field__WEBPACK_IMPORTED_MODULE_25__.GridSpanFields, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_align_self_grid_child_field__WEBPACK_IMPORTED_MODULE_14__.AlignSelfGridChild, {
  parentStyleDirection: parentStyleDirection
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_flex_order_field__WEBPACK_IMPORTED_MODULE_17__.FlexOrderField, null));
const shouldDisplayFlexFields = (display, local) => {
  const value = display?.value ?? local?.value;
  if (!value) {
    return false;
  }
  return 'flex' === value || 'inline-flex' === value;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/grid-track-value.ts":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/grid-track-value.ts ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CUSTOM: function() { return /* binding */ CUSTOM; },
/* harmony export */   EMPTY: function() { return /* binding */ EMPTY; },
/* harmony export */   FR: function() { return /* binding */ FR; },
/* harmony export */   UNITS: function() { return /* binding */ UNITS; },
/* harmony export */   fromSizeInput: function() { return /* binding */ fromSizeInput; },
/* harmony export */   parseValue: function() { return /* binding */ parseValue; },
/* harmony export */   toPlaceholder: function() { return /* binding */ toPlaceholder; },
/* harmony export */   toPropValue: function() { return /* binding */ toPropValue; },
/* harmony export */   toSizeInput: function() { return /* binding */ toSizeInput; },
/* harmony export */   unitOf: function() { return /* binding */ unitOf; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);

const FR = 'fr';
const CUSTOM = 'custom';
const UNITS = [FR, CUSTOM];
const EMPTY = {
  kind: 'empty'
};

// Backward-compat: legacy values were stored as `{$$type:'string', value:'repeat(N, 1fr)'}`.
const REPEAT_FR_PATTERN = /^repeat\(\s*(\d+)\s*,\s*1fr\s*\)$/;
const parseString = css => {
  if (!css) {
    return EMPTY;
  }
  const match = css.match(REPEAT_FR_PATTERN);
  if (match) {
    const count = parseInt(match[1], 10);
    return count >= 1 ? {
      kind: 'fr',
      count
    } : EMPTY;
  }
  return {
    kind: 'custom',
    raw: css
  };
};
const parseGridTrackSize = size => {
  if (!size) {
    return EMPTY;
  }
  if (size.unit === FR) {
    const n = Number(size.size);
    return Number.isFinite(n) && n >= 1 ? {
      kind: 'fr',
      count: Math.trunc(n)
    } : EMPTY;
  }
  const raw = String(size.size ?? '');
  return raw === '' ? EMPTY : {
    kind: 'custom',
    raw
  };
};
const parseValue = value => {
  if (!value) {
    return EMPTY;
  }
  if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.gridTrackSizePropTypeUtil.isValid(value)) {
    return parseGridTrackSize(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.gridTrackSizePropTypeUtil.extract(value));
  }
  if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.isValid(value)) {
    return parseString(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.extract(value));
  }
  return EMPTY;
};
const fromSizeInput = v => {
  if (v.size === '' || Number.isNaN(v.size)) {
    return EMPTY;
  }
  if (v.unit === FR) {
    const n = Number(v.size);
    return Number.isFinite(n) && n >= 1 ? {
      kind: 'fr',
      count: Math.trunc(n)
    } : EMPTY;
  }
  return {
    kind: 'custom',
    raw: String(v.size)
  };
};
const toPropValue = v => {
  switch (v.kind) {
    case 'empty':
      return null;
    case 'fr':
      return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.gridTrackSizePropTypeUtil.create({
        size: v.count,
        unit: FR
      });
    case 'custom':
      return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.gridTrackSizePropTypeUtil.create({
        size: v.raw,
        unit: CUSTOM
      });
  }
};
const toSizeInput = (v, fallbackUnit = FR) => {
  switch (v.kind) {
    case 'empty':
      return {
        size: '',
        unit: fallbackUnit
      };
    case 'fr':
      return {
        size: v.count,
        unit: FR
      };
    case 'custom':
      return {
        size: v.raw,
        unit: CUSTOM
      };
  }
};
const toPlaceholder = v => {
  switch (v.kind) {
    case 'empty':
      return undefined;
    case 'fr':
      return String(v.count);
    case 'custom':
      return v.raw;
  }
};
const unitOf = (v, fallback = FR) => {
  if (v.kind === 'fr') {
    return FR;
  }
  if (v.kind === 'custom') {
    return CUSTOM;
  }
  return fallback;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RotatedIcon: function() { return /* binding */ RotatedIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");





const FLEX_DIRECTION_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Flex direction', 'elementor');
const CLOCKWISE_ANGLES = {
  row: 0,
  column: 90,
  'row-reverse': 180,
  'column-reverse': 270
};
const COUNTER_CLOCKWISE_ANGLES = {
  row: 0,
  column: -90,
  'row-reverse': -180,
  'column-reverse': -270
};
const RotatedIcon = ({
  icon: Icon,
  size,
  isClockwise = true,
  offset = 0,
  disableRotationForReversed = false
}) => {
  const rotate = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(useGetTargetAngle(isClockwise, offset, disableRotationForReversed));
  rotate.current = useGetTargetAngle(isClockwise, offset, disableRotationForReversed, rotate);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    fontSize: size,
    sx: {
      transition: '.3s',
      rotate: `${rotate.current}deg`
    }
  });
};
const useGetTargetAngle = (isClockwise, offset, disableRotationForReversed, existingRef) => {
  const {
    value: direction
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_3__.useStylesField)('flex-direction', {
    history: {
      propDisplayName: FLEX_DIRECTION_LABEL
    }
  });
  const isRtl = 'rtl' === (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)().direction;
  const rotationMultiplier = isRtl ? -1 : 1;
  const angleMap = isClockwise ? CLOCKWISE_ANGLES : COUNTER_CLOCKWISE_ANGLES;
  const currentDirection = direction?.value || 'row';
  const currentAngle = existingRef ? existingRef.current * rotationMultiplier : angleMap[currentDirection] + offset;
  const targetAngle = angleMap[currentDirection] + offset;
  const diffToTargetAngle = (targetAngle - currentAngle + 360) % 360;
  const formattedDiff = (diffToTargetAngle + 180) % 360 - 180;
  if (disableRotationForReversed && ['row-reverse', 'column-reverse'].includes(currentDirection)) {
    return 0;
  }
  return (currentAngle + formattedDiff) * rotationMultiplier;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/wrap-field.tsx":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/wrap-field.tsx ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   WrapField: function() { return /* binding */ WrapField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");







const FLEX_WRAP_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Wrap', 'elementor');
const options = [{
  value: 'nowrap',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No wrap', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'wrap',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Wrap', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowBackIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'wrap-reverse',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Reversed wrap', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowForwardIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const WrapField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "flex-wrap",
    propDisplayName: FLEX_WRAP_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_5__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_6__.StylesFieldLayout, {
    label: FLEX_WRAP_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/dimensions-field.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/dimensions-field.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DimensionsField: function() { return /* binding */ DimensionsField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_direction__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-direction */ "./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");
/* harmony import */ var _layout_section_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../layout-section/utils/rotated-icon */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/utils/rotated-icon.tsx");











const InlineStartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.SideLeftIcon);
const InlineEndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.SideRightIcon);
const sideIcons = {
  'inset-block-start': /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.SideTopIcon, {
    fontSize: 'tiny'
  }),
  'inset-block-end': /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.SideBottomIcon, {
    fontSize: 'tiny'
  }),
  'inset-inline-start': /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_layout_section_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_9__.RotatedIcon, {
    icon: InlineStartIcon,
    size: "tiny"
  }),
  'inset-inline-end': /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_layout_section_utils_rotated_icon__WEBPACK_IMPORTED_MODULE_9__.RotatedIcon, {
    icon: InlineEndIcon,
    size: "tiny"
  })
};
const getInlineStartLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Right', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Left', 'elementor');
const getInlineEndLabel = isSiteRtl => isSiteRtl ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Left', 'elementor') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Right', 'elementor');
const DimensionsField = () => {
  const {
    isSiteRtl
  } = (0,_hooks_use_direction__WEBPACK_IMPORTED_MODULE_6__.useDirection)();
  const rowRefs = [(0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null), (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null)];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_7__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    gap: 2,
    flexWrap: "nowrap",
    ref: rowRefs[0]
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DimensionField, {
    side: "inset-block-start",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Top', 'elementor'),
    rowRef: rowRefs[0]
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DimensionField, {
    side: "inset-inline-end",
    label: getInlineEndLabel(isSiteRtl),
    rowRef: rowRefs[0]
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    gap: 2,
    flexWrap: "nowrap",
    ref: rowRefs[1]
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DimensionField, {
    side: "inset-block-end",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Bottom', 'elementor'),
    rowRef: rowRefs[1]
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DimensionField, {
    side: "inset-inline-start",
    label: getInlineStartLabel(isSiteRtl),
    rowRef: rowRefs[1]
  })));
};
const DimensionField = ({
  side,
  label,
  rowRef
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
  bind: side,
  propDisplayName: label
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
  container: true,
  gap: 0.75,
  alignItems: "center"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
  item: true,
  xs: 12
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_8__.ControlLabel, null, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Grid, {
  item: true,
  xs: 12
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
  startIcon: sideIcons[side],
  extendedOptions: ['auto'],
  anchorRef: rowRef,
  min: -Number.MAX_SAFE_INTEGER
}))));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/offset-field.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/offset-field.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OffsetField: function() { return /* binding */ OffsetField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const OFFSET_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Anchor offset', 'elementor');
const UNITS = ['px', 'em', 'rem', 'vw', 'vh'];
const OffsetField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "scroll-margin-top",
    propDisplayName: OFFSET_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: OFFSET_LABEL,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    units: UNITS,
    anchorRef: rowRef
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/position-field.tsx":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/position-field.tsx ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PositionField: function() { return /* binding */ PositionField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const POSITION_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Position', 'elementor');
const positionOptions = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Static', 'elementor'),
  value: 'static'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Relative', 'elementor'),
  value: 'relative'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Absolute', 'elementor'),
  value: 'absolute'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Fixed', 'elementor'),
  value: 'fixed'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Sticky', 'elementor'),
  value: 'sticky'
}];
const PositionField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "position",
    propDisplayName: POSITION_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: POSITION_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    options: positionOptions
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/position-section.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/position-section.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PositionSection: function() { return /* binding */ PositionSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-styles-fields */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts");
/* harmony import */ var _panel_divider__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../panel-divider */ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _dimensions_field__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./dimensions-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/dimensions-field.tsx");
/* harmony import */ var _offset_field__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./offset-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/offset-field.tsx");
/* harmony import */ var _position_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./position-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/position-field.tsx");
/* harmony import */ var _z_index_field__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./z-index-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/z-index-field.tsx");














const POSITION_STATIC = 'static';
const POSITION_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Position', 'elementor');
const DIMENSIONS_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Dimensions', 'elementor');
const DEPENDENT_PROP_NAMES = ['inset-block-start', 'inset-block-end', 'inset-inline-start', 'inset-inline-end', 'z-index'];
const PositionSection = () => {
  const {
    value: position
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_5__.useStylesField)('position', withHistoryLabel(POSITION_LABEL));
  const positionPrevRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(position);
  const {
    values: dependentValues,
    setValues: setDependentValues
  } = (0,_hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_6__.useStylesFields)(DEPENDENT_PROP_NAMES);
  const [savedDependentValues, saveToHistory, clearHistory] = usePersistDimensions();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (position && position?.value === POSITION_STATIC && hasDependentValues(dependentValues)) {
      saveToHistory(extractDimensions(dependentValues));
    }
    if (positionPrevRef.current?.value === POSITION_STATIC) {
      setDependentValues({
        ...savedDependentValues
      }, withHistoryLabel(DIMENSIONS_LABEL));
      clearHistory();
    }
    if ((!position || position?.value === POSITION_STATIC) && dependentValues?.['z-index']) {
      setDependentValues({
        'z-index': null
      }, withHistoryLabel(DIMENSIONS_LABEL));
    }
    positionPrevRef.current = position;
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [position?.value]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledSectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_position_field__WEBPACK_IMPORTED_MODULE_11__.PositionField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_dimensions_field__WEBPACK_IMPORTED_MODULE_9__.DimensionsField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_z_index_field__WEBPACK_IMPORTED_MODULE_12__.ZIndexField, {
    disabled: !position || position?.value === POSITION_STATIC
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_7__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_offset_field__WEBPACK_IMPORTED_MODULE_10__.OffsetField, null));
};
const usePersistDimensions = () => {
  const {
    id: styleDefID,
    meta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_4__.useStyle)();
  const styleVariantPath = `styles/${styleDefID}/${meta.breakpoint || 'desktop'}/${meta.state || 'null'}`;
  const dimensionsPath = `${styleVariantPath}/dimensions`;
  return (0,_elementor_session__WEBPACK_IMPORTED_MODULE_1__.useSessionStorage)(dimensionsPath);
};
const withHistoryLabel = name => {
  return {
    history: {
      propDisplayName: name
    }
  };
};
const hasDependentValues = values => {
  if (!values) {
    return false;
  }
  const dimensions = extractDimensions(values);
  return Object.values(dimensions).some(v => v !== null);
};
const extractDimensions = values => {
  return DEPENDENT_PROP_NAMES.reduce((acc, key) => {
    return {
      ...acc,
      [key]: values?.[key] ?? null
    };
  }, {});
};
const StyledSectionContent = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_section_content__WEBPACK_IMPORTED_MODULE_8__.SectionContent, {
  shouldForwardProp: prop => prop !== 'gap'
})(({
  gap = 2,
  theme
}) => ({
  gap: 0,
  '& > *': {
    marginBottom: theme.spacing(gap)
  },
  '& > *:last-child': {
    marginBottom: 0
  },
  '& > .MuiStack-root': {
    marginBottom: 0
  },
  '& > .MuiStack-root:has(> *)': {
    marginBottom: theme.spacing(gap)
  },
  '& > .MuiDivider-root': {
    marginBottom: theme.spacing(gap)
  }
}));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/z-index-field.tsx":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/z-index-field.tsx ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ZIndexField: function() { return /* binding */ ZIndexField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");







const Z_INDEX_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Z-index', 'elementor');
const ZIndexField = ({
  disabled
}) => {
  const StyleField = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "z-index",
    propDisplayName: Z_INDEX_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_6__.StylesFieldLayout, {
    label: Z_INDEX_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberControl, {
    disabled: disabled
  })));
  const content = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Alert, {
    color: "secondary",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.InfoCircleFilledIcon, null),
    size: "small"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.AlertTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Z-index', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    component: "span"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('z-index only works on positioned elements. Change position to relative, absolute, or fixed to enable layering.', 'elementor')));
  return disabled ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Infotip, {
    placement: "right",
    content: content,
    color: "secondary",
    slotProps: {
      popper: {
        sx: {
          width: 300
        }
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, null, StyleField)) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, StyleField);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/object-fit-field.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/object-fit-field.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ObjectFitField: function() { return /* binding */ ObjectFitField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const OBJECT_FIT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Object fit', 'elementor');
const positionOptions = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Fill', 'elementor'),
  value: 'fill'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Cover', 'elementor'),
  value: 'cover'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Contain', 'elementor'),
  value: 'contain'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('None', 'elementor'),
  value: 'none'
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Scale down', 'elementor'),
  value: 'scale-down'
}];
const ObjectFitField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "object-fit",
    propDisplayName: OBJECT_FIT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: OBJECT_FIT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    options: positionOptions
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/overflow-field.tsx":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/overflow-field.tsx ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OverflowField: function() { return /* binding */ OverflowField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const OVERFLOW_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Overflow', 'elementor');
const options = [{
  value: 'visible',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Visible', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.EyeIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'hidden',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Hidden', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.EyeOffIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'auto',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Auto', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LetterAIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const OverflowField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "overflow",
    propDisplayName: OVERFLOW_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
    label: OVERFLOW_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/size-section.tsx":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/size-section.tsx ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SizeSection: function() { return /* binding */ SizeSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");
/* harmony import */ var _panel_divider__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../panel-divider */ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../style-tab-collapsible-content */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-collapsible-content.tsx");
/* harmony import */ var _object_fit_field__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./object-fit-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/object-fit-field.tsx");
/* harmony import */ var _overflow_field__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./overflow-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/overflow-field.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }












const CssSizeProps = [[{
  bind: 'width',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Width', 'elementor')
}, {
  bind: 'height',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Height', 'elementor')
}], [{
  bind: 'min-width',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Min width', 'elementor')
}, {
  bind: 'min-height',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Min height', 'elementor')
}], [{
  bind: 'max-width',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Max width', 'elementor')
}, {
  bind: 'max-height',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Max height', 'elementor')
}]];
const ASPECT_RATIO_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Aspect Ratio', 'elementor');
const SizeSection = () => {
  const gridRowRefs = [(0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null), (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null), (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null)];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_7__.SectionContent, null, CssSizeProps.map((row, rowIndex) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    key: rowIndex,
    container: true,
    gap: 2,
    flexWrap: "nowrap",
    ref: gridRowRefs[rowIndex]
  }, row.map(props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 6,
    key: props.bind
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SizeField, _extends({}, props, {
    rowRef: gridRowRefs[rowIndex],
    extendedOptions: ['auto']
  })))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_6__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_overflow_field__WEBPACK_IMPORTED_MODULE_10__.OverflowField, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_8__.StyleTabCollapsibleContent, {
    fields: ['aspect-ratio', 'object-fit']
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    gap: 2,
    pt: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "aspect-ratio",
    propDisplayName: ASPECT_RATIO_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.AspectRatioControl, {
    label: ASPECT_RATIO_LABEL
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_6__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_object_fit_field__WEBPACK_IMPORTED_MODULE_9__.ObjectFitField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "object-position",
    propDisplayName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Object position', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 6
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PositionControl, null))))));
};
const SizeField = ({
  label,
  bind,
  rowRef,
  extendedOptions
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: bind,
    propDisplayName: label
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    container: true,
    gap: 0.75,
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 12
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_5__.ControlLabel, null, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 12
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    extendedOptions: extendedOptions,
    anchorRef: rowRef
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/spacing-section/spacing-section.tsx":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/spacing-section/spacing-section.tsx ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SpacingSection: function() { return /* binding */ SpacingSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_direction__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../hooks/use-direction */ "./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts");
/* harmony import */ var _panel_divider__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../panel-divider */ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");







const MARGIN_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Margin', 'elementor');
const PADDING_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Padding', 'elementor');
const SpacingSection = () => {
  const {
    isSiteRtl
  } = (0,_hooks_use_direction__WEBPACK_IMPORTED_MODULE_4__.useDirection)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_6__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: 'margin',
    propDisplayName: MARGIN_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.LinkedDimensionsControl, {
    label: MARGIN_LABEL,
    isSiteRtl: isSiteRtl,
    min: -Number.MAX_SAFE_INTEGER
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_5__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: 'padding',
    propDisplayName: PADDING_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.LinkedDimensionsControl, {
    label: PADDING_LABEL,
    isSiteRtl: isSiteRtl
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/column-count-field.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/column-count-field.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ColumnCountField: function() { return /* binding */ ColumnCountField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const COLUMN_COUNT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Columns', 'elementor');
const ColumnCountField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "column-count",
    propDisplayName: COLUMN_COUNT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: COLUMN_COUNT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberControl, {
    shouldForceInt: true,
    min: 0,
    step: 1
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/column-gap-field.tsx":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/column-gap-field.tsx ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ColumnGapField: function() { return /* binding */ ColumnGapField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const COLUMN_GAP_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Column gap', 'elementor');
const ColumnGapField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "column-gap",
    propDisplayName: COLUMN_GAP_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: COLUMN_GAP_LABEL,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    anchorRef: rowRef
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-family-field.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-family-field.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FontFamilyField: function() { return /* binding */ FontFamilyField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const FONT_FAMILY_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Font family', 'elementor');
const FontFamilyField = () => {
  const fontFamilies = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useFontFamilies)();
  const sectionWidth = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.useSectionWidth)();
  if (fontFamilies.length === 0) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "font-family",
    propDisplayName: FONT_FAMILY_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
    label: FONT_FAMILY_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.FontFamilyControl, {
    fontFamilies: fontFamilies,
    sectionWidth: sectionWidth,
    ariaLabel: FONT_FAMILY_LABEL
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-size-field.tsx":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-size-field.tsx ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FontSizeField: function() { return /* binding */ FontSizeField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const FONT_SIZE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Font size', 'elementor');
const FontSizeField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "font-size",
    propDisplayName: FONT_SIZE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: FONT_SIZE_LABEL,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    anchorRef: rowRef,
    ariaLabel: FONT_SIZE_LABEL
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-style-field.tsx":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-style-field.tsx ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FontStyleField: function() { return /* binding */ FontStyleField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const FONT_STYLE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Font style', 'elementor');
const options = [{
  value: 'normal',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Normal', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MinusIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'italic',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Italic', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ItalicIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const FontStyleField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "font-style",
    propDisplayName: FONT_STYLE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
    label: FONT_STYLE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-weight-field.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-weight-field.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FontWeightField: function() { return /* binding */ FontWeightField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const FONT_WEIGHT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Font weight', 'elementor');
const fontWeightOptions = [{
  value: '100',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('100 - Thin', 'elementor')
}, {
  value: '200',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('200 - Extra light', 'elementor')
}, {
  value: '300',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('300 - Light', 'elementor')
}, {
  value: '400',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('400 - Normal', 'elementor')
}, {
  value: '500',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('500 - Medium', 'elementor')
}, {
  value: '600',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('600 - Semi bold', 'elementor')
}, {
  value: '700',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('700 - Bold', 'elementor')
}, {
  value: '800',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('800 - Extra bold', 'elementor')
}, {
  value: '900',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('900 - Black', 'elementor')
}];
const FontWeightField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "font-weight",
    propDisplayName: FONT_WEIGHT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: FONT_WEIGHT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    options: fontWeightOptions
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/letter-spacing-field.tsx":
/*!*******************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/letter-spacing-field.tsx ***!
  \*******************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LetterSpacingField: function() { return /* binding */ LetterSpacingField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const LETTER_SPACING_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Letter spacing', 'elementor');
const LetterSpacingField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "letter-spacing",
    propDisplayName: LETTER_SPACING_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: LETTER_SPACING_LABEL,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    anchorRef: rowRef,
    min: -Number.MAX_SAFE_INTEGER
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/line-height-field.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/line-height-field.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LineHeightField: function() { return /* binding */ LineHeightField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const LINE_HEIGHT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Line height', 'elementor');
const LineHeightField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "line-height",
    propDisplayName: LINE_HEIGHT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: LINE_HEIGHT_LABEL,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    anchorRef: rowRef
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-alignment-field.tsx":
/*!*******************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-alignment-field.tsx ***!
  \*******************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TextAlignmentField: function() { return /* binding */ TextAlignmentField; }
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
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../styles-inheritance/components/ui-providers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");








const TEXT_ALIGNMENT_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Text align', 'elementor');
const AlignStartIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.AlignLeftIcon);
const AlignEndIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.AlignRightIcon);
const options = [{
  value: 'start',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Start', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(AlignStartIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'center',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Center', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.AlignCenterIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'end',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('End', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(AlignEndIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'justify',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Justify', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.AlignJustifiedIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const TextAlignmentField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_5__.StylesField, {
    bind: "text-align",
    propDisplayName: TEXT_ALIGNMENT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_ui_providers__WEBPACK_IMPORTED_MODULE_6__.UiProviders, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_7__.StylesFieldLayout, {
    label: TEXT_ALIGNMENT_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  }))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-color-field.tsx":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-color-field.tsx ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TextColorField: function() { return /* binding */ TextColorField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");





const TEXT_COLOR_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Text color', 'elementor');
const TextColorField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "color",
    propDisplayName: TEXT_COLOR_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: TEXT_COLOR_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ColorControl, {
    id: "text-color-control"
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-decoration-field.tsx":
/*!********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-decoration-field.tsx ***!
  \********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TextDecorationField: function() { return /* binding */ TextDecorationField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const TEXT_DECORATION_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Line decoration', 'elementor');
const options = [{
  value: 'none',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('None', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MinusIcon, {
    fontSize: size
  }),
  showTooltip: true,
  exclusive: true
}, {
  value: 'underline',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Underline', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.UnderlineIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'line-through',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Line-through', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.StrikethroughIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'overline',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Overline', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.OverlineIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const TextDecorationField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
  bind: "text-decoration",
  propDisplayName: TEXT_DECORATION_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
  label: TEXT_DECORATION_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: options,
  exclusive: false
})));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-direction-field.tsx":
/*!*******************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-direction-field.tsx ***!
  \*******************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TextDirectionField: function() { return /* binding */ TextDirectionField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const TEXT_DIRECTION_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Direction', 'elementor');
const options = [{
  value: 'ltr',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Left to right', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TextDirectionLtrIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'rtl',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Right to left', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TextDirectionRtlIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const TextDirectionField = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
    bind: "direction",
    propDisplayName: TEXT_DIRECTION_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
    label: TEXT_DIRECTION_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    options: options
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-stroke-field.tsx":
/*!****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-stroke-field.tsx ***!
  \****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TextStrokeField: function() { return /* binding */ TextStrokeField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _add_or_remove_content__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../add-or-remove-content */ "./packages/packages/core/editor-editing-panel/src/components/add-or-remove-content.tsx");
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");







const initTextStroke = {
  $$type: 'stroke',
  value: {
    color: {
      $$type: 'color',
      value: '#000000'
    },
    width: {
      $$type: 'size',
      value: {
        unit: 'px',
        size: 1
      }
    }
  }
};
const TEXT_STROKE_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Text stroke', 'elementor');
const TextStrokeField = () => {
  const {
    value,
    setValue,
    canEdit
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_4__.useStylesField)('stroke', {
    history: {
      propDisplayName: TEXT_STROKE_LABEL
    }
  });
  const addTextStroke = () => {
    setValue(initTextStroke);
  };
  const removeTextStroke = () => {
    setValue(null);
  };
  const hasTextStroke = Boolean(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: 'stroke',
    propDisplayName: TEXT_STROKE_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_add_or_remove_content__WEBPACK_IMPORTED_MODULE_5__.AddOrRemoveContent, {
    isAdded: hasTextStroke,
    onAdd: addTextStroke,
    onRemove: removeTextStroke,
    disabled: !canEdit,
    renderLabel: () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_6__.ControlLabel, null, TEXT_STROKE_LABEL)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.StrokeControl, null)));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/transform-field.tsx":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/transform-field.tsx ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TransformField: function() { return /* binding */ TransformField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const TEXT_TRANSFORM_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Text transform', 'elementor');
const options = [{
  value: 'none',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('None', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MinusIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'capitalize',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Capitalize', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LetterCaseIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'uppercase',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Uppercase', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LetterCaseUpperIcon, {
    fontSize: size
  }),
  showTooltip: true
}, {
  value: 'lowercase',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Lowercase', 'elementor'),
  renderContent: ({
    size
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LetterCaseLowerIcon, {
    fontSize: size
  }),
  showTooltip: true
}];
const TransformField = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_4__.StylesField, {
  bind: "text-transform",
  propDisplayName: TEXT_TRANSFORM_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_5__.StylesFieldLayout, {
  label: TEXT_TRANSFORM_LABEL
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  options: options
})));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/typography-section.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/typography-section.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TypographySection: function() { return /* binding */ TypographySection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _panel_divider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../panel-divider */ "./packages/packages/core/editor-editing-panel/src/components/panel-divider.tsx");
/* harmony import */ var _section_content__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../style-tab-collapsible-content */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-collapsible-content.tsx");
/* harmony import */ var _column_count_field__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./column-count-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/column-count-field.tsx");
/* harmony import */ var _column_gap_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./column-gap-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/column-gap-field.tsx");
/* harmony import */ var _font_family_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./font-family-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-family-field.tsx");
/* harmony import */ var _font_size_field__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./font-size-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-size-field.tsx");
/* harmony import */ var _font_style_field__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./font-style-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-style-field.tsx");
/* harmony import */ var _font_weight_field__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./font-weight-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/font-weight-field.tsx");
/* harmony import */ var _letter_spacing_field__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./letter-spacing-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/letter-spacing-field.tsx");
/* harmony import */ var _line_height_field__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./line-height-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/line-height-field.tsx");
/* harmony import */ var _text_alignment_field__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./text-alignment-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-alignment-field.tsx");
/* harmony import */ var _text_color_field__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./text-color-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-color-field.tsx");
/* harmony import */ var _text_decoration_field__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./text-decoration-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-decoration-field.tsx");
/* harmony import */ var _text_direction_field__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./text-direction-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-direction-field.tsx");
/* harmony import */ var _text_stroke_field__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./text-stroke-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/text-stroke-field.tsx");
/* harmony import */ var _transform_field__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./transform-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/transform-field.tsx");
/* harmony import */ var _word_spacing_field__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./word-spacing-field */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/word-spacing-field.tsx");



















const TypographySection = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_2__.SectionContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_font_family_field__WEBPACK_IMPORTED_MODULE_6__.FontFamilyField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_font_weight_field__WEBPACK_IMPORTED_MODULE_9__.FontWeightField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_font_size_field__WEBPACK_IMPORTED_MODULE_7__.FontSizeField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_1__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_text_alignment_field__WEBPACK_IMPORTED_MODULE_12__.TextAlignmentField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_text_color_field__WEBPACK_IMPORTED_MODULE_13__.TextColorField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_3__.StyleTabCollapsibleContent, {
    fields: ['line-height', 'letter-spacing', 'word-spacing', 'column-count', 'text-decoration', 'text-transform', 'direction', 'font-style', 'stroke']
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section_content__WEBPACK_IMPORTED_MODULE_2__.SectionContent, {
    sx: {
      pt: 2
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_line_height_field__WEBPACK_IMPORTED_MODULE_11__.LineHeightField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_letter_spacing_field__WEBPACK_IMPORTED_MODULE_10__.LetterSpacingField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_word_spacing_field__WEBPACK_IMPORTED_MODULE_18__.WordSpacingField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_column_count_field__WEBPACK_IMPORTED_MODULE_4__.ColumnCountField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_column_gap_field__WEBPACK_IMPORTED_MODULE_5__.ColumnGapField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_panel_divider__WEBPACK_IMPORTED_MODULE_1__.PanelDivider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_text_decoration_field__WEBPACK_IMPORTED_MODULE_14__.TextDecorationField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_transform_field__WEBPACK_IMPORTED_MODULE_17__.TransformField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_text_direction_field__WEBPACK_IMPORTED_MODULE_15__.TextDirectionField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_font_style_field__WEBPACK_IMPORTED_MODULE_8__.FontStyleField, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_text_stroke_field__WEBPACK_IMPORTED_MODULE_16__.TextStrokeField, null))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/word-spacing-field.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/word-spacing-field.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   WordSpacingField: function() { return /* binding */ WordSpacingField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../controls-registry/styles-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx");
/* harmony import */ var _styles_field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../styles-field-layout */ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx");






const WORD_SPACING_LABEL = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Word spacing', 'elementor');
const WordSpacingField = () => {
  const rowRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_styles_field__WEBPACK_IMPORTED_MODULE_3__.StylesField, {
    bind: "word-spacing",
    propDisplayName: WORD_SPACING_LABEL
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_field_layout__WEBPACK_IMPORTED_MODULE_4__.StylesFieldLayout, {
    label: WORD_SPACING_LABEL,
    ref: rowRef
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.SizeControl, {
    anchorRef: rowRef,
    min: -Number.MAX_SAFE_INTEGER
  })));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-tab-collapsible-content.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-tab-collapsible-content.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleTabCollapsibleContent: function() { return /* binding */ StyleTabCollapsibleContent; },
/* harmony export */   getStylesInheritanceIndicators: function() { return /* binding */ getStylesInheritanceIndicators; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _styles_inheritance_components_styles_inheritance_section_indicators__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../styles-inheritance/components/styles-inheritance-section-indicators */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-section-indicators.tsx");



const StyleTabCollapsibleContent = ({
  fields = [],
  children
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.CollapsibleContent, {
    titleEnd: getStylesInheritanceIndicators(fields)
  }, children);
};
function getStylesInheritanceIndicators(fields) {
  if (fields.length === 0) {
    return null;
  }
  return isOpen => !isOpen ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_components_styles_inheritance_section_indicators__WEBPACK_IMPORTED_MODULE_2__.StylesInheritanceSectionIndicators, {
    fields: fields
  }) : null;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-tab-section.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-tab-section.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleTabSection: function() { return /* binding */ StyleTabSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../hooks/use-default-panel-settings */ "./packages/packages/core/editor-editing-panel/src/hooks/use-default-panel-settings.ts");
/* harmony import */ var _section__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./section */ "./packages/packages/core/editor-editing-panel/src/components/section.tsx");
/* harmony import */ var _style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./style-tab-collapsible-content */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-collapsible-content.tsx");




const StyleTabSection = ({
  section,
  fields = [],
  unmountOnExit = true
}) => {
  const {
    component,
    name,
    title,
    action
  } = section;
  const tabDefaults = (0,_hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_1__.useDefaultPanelSettings)();
  const SectionComponent = component || (() => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null));
  const isExpanded = tabDefaults.defaultSectionsExpanded.style?.includes(name);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_section__WEBPACK_IMPORTED_MODULE_2__.Section, {
    title: title,
    defaultExpanded: isExpanded,
    titleEnd: (0,_style_tab_collapsible_content__WEBPACK_IMPORTED_MODULE_3__.getStylesInheritanceIndicators)(fields),
    unmountOnExit: unmountOnExit,
    action: action
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SectionComponent, null));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/style-tab.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/style-tab.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleTab: function() { return /* binding */ StyleTab; },
/* harmony export */   StyleTabSlot: function() { return /* binding */ StyleTabSlot; },
/* harmony export */   injectIntoStyleTab: function() { return /* binding */ injectIntoStyleTab; },
/* harmony export */   stickyHeaderStyles: function() { return /* binding */ stickyHeaderStyles; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/locations */ "@elementor/locations");
/* harmony import */ var _elementor_locations__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_locations__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_scroll_context__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../contexts/scroll-context */ "./packages/packages/core/editor-editing-panel/src/contexts/scroll-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../contexts/styles-inheritance-context */ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx");
/* harmony import */ var _hooks_use_active_style_def_id__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../hooks/use-active-style-def-id */ "./packages/packages/core/editor-editing-panel/src/hooks/use-active-style-def-id.ts");
/* harmony import */ var _css_classes_css_class_selector__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./css-classes/css-class-selector */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-selector.tsx");
/* harmony import */ var _sections_list__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./sections-list */ "./packages/packages/core/editor-editing-panel/src/components/sections-list.tsx");
/* harmony import */ var _style_sections_background_section_background_section__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./style-sections/background-section/background-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/background-section/background-section.tsx");
/* harmony import */ var _style_sections_border_section_border_section__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./style-sections/border-section/border-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/border-section/border-section.tsx");
/* harmony import */ var _style_sections_effects_section_effects_section__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./style-sections/effects-section/effects-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/effects-section/effects-section.tsx");
/* harmony import */ var _style_sections_layout_section_layout_section__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./style-sections/layout-section/layout-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/layout-section.tsx");
/* harmony import */ var _style_sections_position_section_position_section__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./style-sections/position-section/position-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/position-section/position-section.tsx");
/* harmony import */ var _style_sections_size_section_size_section__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./style-sections/size-section/size-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/size-section/size-section.tsx");
/* harmony import */ var _style_sections_spacing_section_spacing_section__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./style-sections/spacing-section/spacing-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/spacing-section/spacing-section.tsx");
/* harmony import */ var _style_sections_typography_section_typography_section__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./style-sections/typography-section/typography-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/typography-section/typography-section.tsx");
/* harmony import */ var _style_tab_section__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./style-tab-section */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-section.tsx");

























const TABS_HEADER_HEIGHT = '37px';
const {
  Slot: StyleTabSlot,
  inject: injectIntoStyleTab
} = (0,_elementor_locations__WEBPACK_IMPORTED_MODULE_3__.createLocation)();
const stickyHeaderStyles = {
  position: 'sticky',
  zIndex: 1100,
  opacity: 1,
  backgroundColor: 'background.default',
  transition: 'top 300ms ease'
};
const StyleTab = () => {
  const currentClassesProp = useCurrentClassesProp();
  const [activeStyleDefId, setActiveStyleDefId] = (0,_hooks_use_active_style_def_id__WEBPACK_IMPORTED_MODULE_12__.useActiveStyleDefId)(currentClassesProp ?? '');
  const [activeStyleState, setActiveStyleState] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const breakpoint = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_2__.useActiveBreakpoint)();
  if (!currentClassesProp) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_7__.ClassesPropProvider, {
    prop: currentClassesProp
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_style_context__WEBPACK_IMPORTED_MODULE_10__.StyleProvider, {
    meta: {
      breakpoint,
      state: activeStyleState
    },
    id: activeStyleDefId,
    setId: id => {
      setActiveStyleDefId(id);
      setActiveStyleState(null);
    },
    setMetaState: setActiveStyleState
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_session__WEBPACK_IMPORTED_MODULE_4__.SessionStorageProvider, {
    prefix: activeStyleDefId ?? ''
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_11__.StyleInheritanceProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ClassesHeader, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_css_classes_css_class_selector__WEBPACK_IMPORTED_MODULE_13__.CssClassSelector, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Divider, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_sections_list__WEBPACK_IMPORTED_MODULE_14__.SectionsList, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_layout_section_layout_section__WEBPACK_IMPORTED_MODULE_18__.LayoutSection,
      name: 'Layout',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Layout', 'elementor')
    },
    fields: ['display', 'flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'align-self', 'gap', 'order', 'grid-column', 'grid-row', 'grid-auto-rows', 'grid-auto-columns']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_spacing_section_spacing_section__WEBPACK_IMPORTED_MODULE_21__.SpacingSection,
      name: 'Spacing',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Spacing', 'elementor')
    },
    fields: ['margin', 'padding']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_size_section_size_section__WEBPACK_IMPORTED_MODULE_20__.SizeSection,
      name: 'Size',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Size', 'elementor')
    },
    fields: ['width', 'min-width', 'max-width', 'height', 'min-height', 'max-height', 'overflow', 'aspect-ratio', 'object-fit']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_position_section_position_section__WEBPACK_IMPORTED_MODULE_19__.PositionSection,
      name: 'Position',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Position', 'elementor')
    },
    fields: ['position', 'z-index', 'scroll-margin-top']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_typography_section_typography_section__WEBPACK_IMPORTED_MODULE_22__.TypographySection,
      name: 'Typography',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Typography', 'elementor')
    },
    fields: ['font-family', 'font-weight', 'font-size', 'text-align', 'color', 'line-height', 'letter-spacing', 'word-spacing', 'column-count', 'text-decoration', 'text-transform', 'direction', 'font-style', 'stroke']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_background_section_background_section__WEBPACK_IMPORTED_MODULE_15__.BackgroundSection,
      name: 'Background',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Background', 'elementor')
    },
    fields: ['background']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_border_section_border_section__WEBPACK_IMPORTED_MODULE_16__.BorderSection,
      name: 'Border',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Border', 'elementor')
    },
    fields: ['border-radius', 'border-width', 'border-color', 'border-style']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_style_tab_section__WEBPACK_IMPORTED_MODULE_23__.StyleTabSection, {
    section: {
      component: _style_sections_effects_section_effects_section__WEBPACK_IMPORTED_MODULE_17__.EffectsSection,
      name: 'Effects',
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Effects', 'elementor')
    },
    fields: ['mix-blend-mode', 'box-shadow', 'opacity', 'transform', 'filter', 'backdrop-filter', 'transform-origin', 'transition']
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyleTabSlot, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Box, {
    sx: {
      height: '150px'
    }
  })))));
};
function ClassesHeader({
  children
}) {
  const scrollDirection = (0,_contexts_scroll_context__WEBPACK_IMPORTED_MODULE_9__.useScrollDirection)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    sx: {
      ...stickyHeaderStyles,
      top: scrollDirection === 'up' ? TABS_HEADER_HEIGHT : 0
    }
  }, children);
}
function useCurrentClassesProp() {
  const {
    elementType
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_8__.useElement)();
  const prop = Object.entries(elementType.propsSchema).find(([, propType]) => propType.kind === 'plain' && propType.key === _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.CLASSES_PROP_KEY);
  if (!prop) {
    return null;
  }
  return prop[0];
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/components/styles-field-layout.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StylesFieldLayout: function() { return /* binding */ StylesFieldLayout; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _control_label__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");



const StylesFieldLayout = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  const {
    direction = 'row',
    children,
    label,
    infoTooltip
  } = props;
  const LayoutComponent = direction === 'row' ? Row : Column;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LayoutComponent, {
    label: label,
    infoTooltip: infoTooltip,
    ref: ref,
    children: children
  });
});
const Row = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  label,
  children,
  infoTooltip
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Grid, {
    container: true,
    gap: 2,
    alignItems: "center",
    flexWrap: "nowrap",
    ref: ref,
    "aria-label": `${label} control`
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Grid, {
    item: true,
    xs: 6
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_2__.ControlLabel, {
    infoTooltip: infoTooltip
  }, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Grid, {
    item: true,
    xs: 6,
    sx: theme => ({
      width: `calc(50% - ${theme.spacing(2)})`
    })
  }, children));
});
const Column = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  label,
  children,
  infoTooltip
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    gap: 0.75,
    ref: ref
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_control_label__WEBPACK_IMPORTED_MODULE_2__.ControlLabel, {
    infoTooltip: infoTooltip
  }, label), children);
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ClassesPropProvider: function() { return /* binding */ ClassesPropProvider; },
/* harmony export */   useClassesProp: function() { return /* binding */ useClassesProp; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const Context = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function ClassesPropProvider({
  children,
  prop
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Context.Provider, {
    value: {
      prop
    }
  }, children);
}
function useClassesProp() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
  if (!context) {
    throw new Error('useClassesProp must be used within a ClassesPropProvider');
  }
  return context.prop;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementProvider: function() { return /* binding */ ElementProvider; },
/* harmony export */   useElement: function() { return /* binding */ useElement; },
/* harmony export */   usePanelElementSetting: function() { return /* binding */ usePanelElementSetting; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const Context = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function ElementProvider({
  children,
  element,
  elementType,
  settings
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Context.Provider, {
    value: {
      element,
      elementType,
      settings
    }
  }, children);
}
function useElement() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
  if (!context) {
    throw new Error('useElement must be used within a ElementProvider');
  }
  return context;
}
function usePanelElementSetting(propKey) {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
  if (!context) {
    throw new Error('usePanelElementSetting must be used within a ElementProvider');
  }
  return context.settings[propKey] ?? null;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/contexts/scroll-context.tsx":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/contexts/scroll-context.tsx ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ScrollProvider: function() { return /* binding */ ScrollProvider; },
/* harmony export */   useScrollDirection: function() { return /* binding */ useScrollDirection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);



const ScrollContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);
const ScrollPanel = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)('div')`
	height: 100%;
	overflow-y: auto;
`;
const DEFAULT_SCROLL_DIRECTION = 'up';
function ScrollProvider({
  children
}) {
  const [direction, setDirection] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(DEFAULT_SCROLL_DIRECTION);
  const ref = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const scrollPos = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(0);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const scrollElement = ref.current;
    if (!scrollElement) {
      return;
    }
    const handleScroll = () => {
      const {
        scrollTop
      } = scrollElement;
      if (scrollTop > scrollPos.current) {
        setDirection('down');
      } else if (scrollTop < scrollPos.current) {
        setDirection('up');
      }
      scrollPos.current = scrollTop;
    };
    scrollElement.addEventListener('scroll', handleScroll);
    return () => {
      scrollElement.removeEventListener('scroll', handleScroll);
    };
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ScrollContext.Provider, {
    value: {
      direction
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ScrollPanel, {
    ref: ref
  }, children));
}
function useScrollDirection() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(ScrollContext)?.direction ?? DEFAULT_SCROLL_DIRECTION;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleProvider: function() { return /* binding */ StyleProvider; },
/* harmony export */   getProviderByStyleId: function() { return /* binding */ getProviderByStyleId; },
/* harmony export */   useIsStyle: function() { return /* binding */ useIsStyle; },
/* harmony export */   useStyle: function() { return /* binding */ useStyle; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-editing-panel/src/errors.ts");




const Context = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function StyleProvider({
  children,
  ...props
}) {
  const provider = props.id === null ? null : getProviderByStyleId(props.id);
  const {
    userCan
  } = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.useUserStylesCapability)();
  if (props.id && !provider) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_2__.StylesProviderNotFoundError({
      context: {
        styleId: props.id
      }
    });
  }
  const canEdit = userCan(provider?.getKey() ?? '').updateProps;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Context.Provider, {
    value: {
      ...props,
      provider,
      canEdit
    }
  }, children);
}
function useStyle() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
  if (!context) {
    throw new Error('useStyle must be used within a StyleProvider');
  }
  return context;
}
function getProviderByStyleId(styleId) {
  const styleProvider = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.getProviders().find(provider => {
    return provider.actions.all().find(style => style.id === styleId);
  });
  return styleProvider ?? null;
}
function useIsStyle() {
  return !!(0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyleInheritanceProvider: function() { return /* binding */ StyleInheritanceProvider; },
/* harmony export */   useInheritedValues: function() { return /* binding */ useInheritedValues; },
/* harmony export */   useStylesInheritanceChain: function() { return /* binding */ useStylesInheritanceChain; },
/* harmony export */   useStylesInheritanceSnapshot: function() { return /* binding */ useStylesInheritanceSnapshot; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _hooks_use_styles_rerender__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../hooks/use-styles-rerender */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-rerender.ts");
/* harmony import */ var _styles_inheritance_create_styles_inheritance__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../styles-inheritance/create-styles-inheritance */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/create-styles-inheritance.ts");
/* harmony import */ var _classes_prop_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _element_context__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _style_context__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");












const Context = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function StyleInheritanceProvider({
  children
}) {
  const styleDefs = useAppliedStyles();
  const breakpointsTree = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_3__.getBreakpointsTree)();
  const {
    getSnapshot,
    getInheritanceChain
  } = (0,_styles_inheritance_create_styles_inheritance__WEBPACK_IMPORTED_MODULE_7__.createStylesInheritance)(styleDefs, breakpointsTree);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Context.Provider, {
    value: {
      getSnapshot,
      getInheritanceChain
    }
  }, children);
}
function useStylesInheritanceSnapshot() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
  const {
    meta
  } = (0,_style_context__WEBPACK_IMPORTED_MODULE_10__.useStyle)();
  if (!context) {
    throw new Error('useStylesInheritanceSnapshot must be used within a StyleInheritanceProvider');
  }
  if (!meta) {
    return null;
  }
  return context.getSnapshot(meta) ?? null;
}
function useStylesInheritanceChain(path) {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(Context);
  if (!context) {
    throw new Error('useStylesInheritanceChain must be used within a StyleInheritanceProvider');
  }
  const schema = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_4__.getStylesSchema)();
  const topLevelPropType = schema?.[path[0]];
  const snapshot = useStylesInheritanceSnapshot();
  if (!snapshot) {
    return [];
  }
  return context.getInheritanceChain(snapshot, path, topLevelPropType);
}
const EMPTY_INHERITED_VALUES = {};
function useInheritedValues(propKeys) {
  const snapshot = useStylesInheritanceSnapshot();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!snapshot || propKeys.length === 0) {
      return EMPTY_INHERITED_VALUES;
    }
    return Object.fromEntries(propKeys.map(key => [key, snapshot[key]?.[0]?.value ?? null]));
  }, [snapshot, propKeys]);
}
const useAppliedStyles = () => {
  const currentClassesProp = (0,_classes_prop_context__WEBPACK_IMPORTED_MODULE_8__.useClassesProp)();
  const baseStyles = useBaseStyles();
  (0,_hooks_use_styles_rerender__WEBPACK_IMPORTED_MODULE_6__.useStylesRerender)();
  const classesProp = (0,_element_context__WEBPACK_IMPORTED_MODULE_9__.usePanelElementSetting)(currentClassesProp);
  const appliedStyles = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.classesPropTypeUtil.extract(classesProp) ?? [];
  return _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_5__.stylesRepository.all().filter(style => [...baseStyles, ...appliedStyles].includes(style.id));
};
const useBaseStyles = () => {
  const {
    elementType
  } = (0,_element_context__WEBPACK_IMPORTED_MODULE_9__.useElement)();
  const widgetsCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getWidgetsCache)();
  const widgetCache = widgetsCache?.[elementType.key];
  return Object.keys(widgetCache?.base_styles ?? {});
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/conditional-field.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/conditional-field.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConditionalField: function() { return /* binding */ ConditionalField; },
/* harmony export */   getDependencies: function() { return /* binding */ getDependencies; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../contexts/styles-inheritance-context */ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx");
/* harmony import */ var _hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-styles-fields */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts");





const ConditionalField = ({
  children
}) => {
  const {
    propType,
    value,
    resetValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const depList = getDependencies(propType);
  const {
    values: depValues,
    setValues: setDepValues
  } = (0,_hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_4__.useStylesFields)(depList);
  const inheritedValues = (0,_contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_3__.useInheritedValues)(depList);
  const resolvedValues = resolveWithInherited(depValues, inheritedValues);
  const isHidden = !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.isDependencyMet)(propType?.dependencies, resolvedValues).isMet;
  useSyncDepsWithInherited({
    isHidden,
    depValues,
    value,
    inheritedValues,
    setDepValues,
    resetValue
  });
  return isHidden ? null : children;
};
function wasDepsCleared(prevDepValues, depValues) {
  if (!prevDepValues) {
    return false;
  }
  return Object.keys(prevDepValues).some(key => prevDepValues[key] && (!depValues || !depValues[key]));
}
function useSyncDepsWithInherited({
  isHidden,
  depValues,
  value,
  inheritedValues,
  setDepValues,
  resetValue
}) {
  const syncRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)({
    hasSynced: false,
    prevDepValues: depValues
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const {
      hasSynced,
      prevDepValues
    } = syncRef.current;
    if (hasSynced && value && wasDepsCleared(prevDepValues, depValues) || isHidden && depValues && value) {
      resetValue();
    }
    if (isHidden || !value || !depValues) {
      syncRef.current = {
        hasSynced: false,
        prevDepValues: depValues
      };
      return;
    }
    if (hasSynced) {
      syncRef.current.prevDepValues = depValues;
      return;
    }
    syncRef.current = {
      hasSynced: true,
      prevDepValues: depValues
    };
    Object.entries(depValues).forEach(([key, depValue]) => {
      const inherited = inheritedValues[key];
      if (!depValue && inherited) {
        setDepValues({
          [key]: inherited
        }, {
          history: {
            propDisplayName: key
          }
        });
      }
    });
  }, [isHidden, depValues, value, inheritedValues, setDepValues, resetValue]);
}
function resolveWithInherited(localValues, inheritedValues) {
  if (!localValues) {
    const hasInherited = Object.keys(inheritedValues).length > 0;
    return hasInherited ? {
      ...inheritedValues
    } : null;
  }
  return Object.fromEntries(Object.entries(localValues).map(([key, val]) => [key, val ?? inheritedValues[key] ?? null]));
}
function getDependencies(propType) {
  if (!propType?.dependencies?.terms.length) {
    return [];
  }
  return propType.dependencies.terms.flatMap(term => !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.isDependency)(term) ? term.path : []);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/control-type-container.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/control-type-container.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ControlTypeContainer: function() { return /* binding */ ControlTypeContainer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const ControlTypeContainer = ({
  children,
  layout
}) => {
  if (layout === 'custom') {
    return children;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledContainer, {
    layout: layout
  }, children);
};
const StyledContainer = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => !['layout'].includes(prop)
})(({
  layout,
  theme
}) => ({
  display: 'grid',
  gridGap: theme.spacing(1),
  ...getGridLayout(layout)
}));
const getGridLayout = layout => ({
  justifyContent: 'space-between',
  ...getStyleByLayout(layout)
});
const getStyleByLayout = layout => {
  if (layout === 'full') {
    return {
      gridTemplateColumns: 'minmax(0, 1fr)'
    };
  }
  if (layout === 'two-columns') {
    return {
      alignItems: 'center',
      gridTemplateColumns: 'repeat(2, minmax(0, 1fr))'
    };
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/control.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/control.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Control: function() { return /* binding */ Control; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-editing-panel/src/errors.ts");
/* harmony import */ var _controls_registry__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./controls-registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const Control = ({
  props,
  type
}) => {
  const ControlByType = _controls_registry__WEBPACK_IMPORTED_MODULE_3__.controlsRegistry.get(type);
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_1__.useElement)();
  if (!ControlByType) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_2__.ControlTypeNotFoundError({
      context: {
        controlType: type
      }
    });
  }

  // @ts-expect-error ControlComponent props are inferred from the type (T).
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ControlByType, _extends({}, props, {
    context: {
      elementId: element.id
    }
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   controlsRegistry: function() { return /* binding */ controlsRegistry; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-editing-panel/src/errors.ts");



const queryArrayPropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.createArrayPropUtils)(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.queryPropTypeUtil.key, _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.queryPropTypeUtil.schema);
const controlTypes = {
  image: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.ImageControl,
    layout: 'custom',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.imagePropTypeUtil
  },
  'svg-media': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.SvgMediaControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.svgSrcPropTypeUtil
  },
  text: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.TextControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  },
  textarea: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.TextAreaControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  },
  size: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.SizeControl,
    layout: 'two-columns',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.sizePropTypeUtil
  },
  select: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.SelectControlWrapper,
    layout: 'two-columns',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  },
  chips: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.ChipsControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringArrayPropTypeUtil
  },
  link: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.LinkControl,
    layout: 'custom',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.linkPropTypeUtil
  },
  query: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.QueryControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.queryPropTypeUtil
  },
  'query-chips': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.QueryChipsControl,
    layout: 'full',
    propTypeUtil: queryArrayPropTypeUtil
  },
  'query-filter-repeater': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.QueryFilterRepeaterControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.queryFilterArrayPropTypeUtil
  },
  url: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.UrlControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  },
  switch: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.SwitchControl,
    layout: 'two-columns',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.booleanPropTypeUtil
  },
  number: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.NumberControl,
    layout: 'two-columns',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.numberPropTypeUtil
  },
  repeatable: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.RepeatableControl,
    layout: 'full',
    propTypeUtil: undefined
  },
  'key-value': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.KeyValueControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.keyValuePropTypeUtil
  },
  'html-tag': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.HtmlTagControl,
    layout: 'two-columns',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  },
  toggle: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.ToggleControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  },
  'date-time': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.DateTimeControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.DateTimePropTypeUtil
  },
  video: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.VideoMediaControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.videoSrcPropTypeUtil
  },
  'inline-editing': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.InlineEditingControl,
    layout: 'full',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.htmlV3PropTypeUtil
  },
  email: {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.EmailFormActionControl,
    layout: 'custom',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.emailsPropTypeUtil
  },
  'date-range': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.DateRangeControl,
    layout: 'custom',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.dateRangePropTypeUtil
  },
  'time-range': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.TimeRangeControl,
    layout: 'custom',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.timeRangePropTypeUtil
  },
  'attachment-type': {
    component: _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.AttachmentTypeControl,
    layout: 'custom',
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil
  }
};
class ControlsRegistry {
  constructor(controlsRegistry) {
    this.controlsRegistry = controlsRegistry;
    this.controlsRegistry = controlsRegistry;
  }
  get(type) {
    return this.controlsRegistry[type]?.component;
  }
  getLayout(type) {
    return this.controlsRegistry[type]?.layout;
  }
  getPropTypeUtil(type) {
    return this.controlsRegistry[type]?.propTypeUtil;
  }
  registry() {
    return this.controlsRegistry;
  }
  register(type, component, layout,
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  propTypeUtil) {
    if (this.controlsRegistry[type]) {
      throw new _errors__WEBPACK_IMPORTED_MODULE_2__.ControlTypeAlreadyRegisteredError({
        context: {
          controlType: type
        }
      });
    }
    this.controlsRegistry[type] = {
      component,
      layout,
      propTypeUtil
    };
  }
  unregister(type) {
    if (!this.controlsRegistry[type]) {
      throw new _errors__WEBPACK_IMPORTED_MODULE_2__.ControlTypeNotRegisteredError({
        context: {
          controlType: type
        }
      });
    }
    // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
    delete this.controlsRegistry[type];
  }
}
const controlsRegistry = new ControlsRegistry(controlTypes);

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createTopLevelObjectType: function() { return /* binding */ createTopLevelObjectType; }
/* harmony export */ });
const createTopLevelObjectType = ({
  schema
}) => {
  const schemaPropType = {
    key: '',
    kind: 'object',
    meta: {},
    settings: {},
    default: null,
    shape: schema
  };
  return schemaPropType;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/get-element-by-type.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/get-element-by-type.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementByType: function() { return /* binding */ getElementByType; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

const getElementByType = (elementId, type) => {
  const currentElement = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  if (!currentElement) {
    return null;
  }
  if (currentElement.model.get('elType') === type) {
    return currentElement;
  }
  return currentElement.children?.findRecursive?.(child => child.model.get('elType') === type) ?? null;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/registry.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/registry.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   registerElementControls: function() { return /* binding */ registerElementControls; }
/* harmony export */ });
/* harmony import */ var _controls_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../controls-registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx");
/* harmony import */ var _tabs_control_tabs_control__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./tabs-control/tabs-control */ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/tabs-control/tabs-control.tsx");


const controlTypes = {
  tabs: {
    component: _tabs_control_tabs_control__WEBPACK_IMPORTED_MODULE_1__.TabsControl,
    layout: 'full'
  }
};
const registerElementControls = () => {
  Object.entries(controlTypes).forEach(([type, {
    component,
    layout
  }]) => {
    _controls_registry__WEBPACK_IMPORTED_MODULE_0__.controlsRegistry.register(type, component, layout);
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/tabs-control/tabs-control.tsx":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/tabs-control/tabs-control.tsx ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConditionalTooltip: function() { return /* binding */ ConditionalTooltip; },
/* harmony export */   TabsControl: function() { return /* binding */ TabsControl; },
/* harmony export */   TabsControlContent: function() { return /* binding */ TabsControlContent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _settings_field__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../settings-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/settings-field.tsx");
/* harmony import */ var _get_element_by_type__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../get-element-by-type */ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/get-element-by-type.ts");
/* harmony import */ var _use_actions__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./use-actions */ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/tabs-control/use-actions.ts");











const TAB_MENU_ELEMENT_TYPE = 'e-tabs-menu';
const TAB_CONTENT_AREA_ELEMENT_TYPE = 'e-tabs-content-area';
const TabsControl = ({
  label
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_settings_field__WEBPACK_IMPORTED_MODULE_8__.SettingsField, {
    bind: "default-active-tab",
    propDisplayName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Tabs', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TabsControlContent, {
    label: label
  }));
};
const TabsControlContent = ({
  label
}) => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const {
    addItem,
    duplicateItem,
    moveItem,
    removeItem
  } = (0,_use_actions__WEBPACK_IMPORTED_MODULE_10__.useActions)();
  const {
    [_use_actions__WEBPACK_IMPORTED_MODULE_10__.TAB_ELEMENT_TYPE]: tabLinks
  } = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.useElementChildren)(element.id, {
    [TAB_MENU_ELEMENT_TYPE]: _use_actions__WEBPACK_IMPORTED_MODULE_10__.TAB_ELEMENT_TYPE
  });
  const tabList = (0,_get_element_by_type__WEBPACK_IMPORTED_MODULE_9__.getElementByType)(element.id, TAB_MENU_ELEMENT_TYPE);
  const tabContentArea = (0,_get_element_by_type__WEBPACK_IMPORTED_MODULE_9__.getElementByType)(element.id, TAB_CONTENT_AREA_ELEMENT_TYPE);
  const repeaterValues = tabLinks.map((tabLink, index) => {
    return {
      id: tabLink.id,
      title: tabLink.editorSettings?.title,
      index
    };
  });
  const setValue = (_newValues, _options, meta) => {
    if (meta?.action?.type === 'add') {
      const items = meta.action.payload;
      return addItem({
        tabContentAreaId: tabContentArea.id,
        items,
        tabsMenuId: tabList.id
      });
    }
    if (meta?.action?.type === 'remove') {
      const items = meta.action.payload;
      return removeItem({
        items,
        tabContentAreaId: tabContentArea.id
      });
    }
    if (meta?.action?.type === 'duplicate') {
      const items = meta.action.payload;
      return duplicateItem({
        items,
        tabContentAreaId: tabContentArea.id
      });
    }
    if (meta?.action?.type === 'reorder') {
      const {
        from,
        to
      } = meta.action.payload;
      return moveItem({
        toIndex: to,
        tabsMenuId: tabList.id,
        tabContentAreaId: tabContentArea.id,
        movedElementId: tabLinks[from].id,
        movedElementIndex: from
      });
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.Repeater, {
    showToggle: false,
    values: repeaterValues,
    setValues: setValue,
    showRemove: repeaterValues.length > 1,
    label: label,
    itemSettings: {
      getId: ({
        item
      }) => item.id,
      initialValues: {
        id: '',
        title: 'Tab'
      },
      Label: ItemLabel,
      Content: ItemContent,
      Icon: () => null
    }
  });
};
const ItemLabel = ({
  value,
  index
}) => {
  const elementTitle = value?.title;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    sx: {
      minHeight: 20
    },
    direction: "row",
    alignItems: "center",
    gap: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, elementTitle), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ItemDefaultTab, {
    index: index
  }));
};
const ItemDefaultTab = ({
  index
}) => {
  const {
    value: defaultItem
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__.numberPropTypeUtil);
  const isDefault = defaultItem === index;
  if (!isDefault) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Chip, {
    size: "tiny",
    shape: "rounded",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Default', 'elementor')
  });
};
const ItemContent = ({
  value,
  index
}) => {
  if (!value.id) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    p: 2,
    gap: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TabLabelControl, {
    elementId: value.id
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_settings_field__WEBPACK_IMPORTED_MODULE_8__.SettingsField, {
    bind: "default-active-tab",
    propDisplayName: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Tabs', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DefaultTabControl, {
    tabIndex: index
  })));
};
const DefaultTabControl = ({
  tabIndex
}) => {
  const {
    value,
    setValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__.numberPropTypeUtil);
  const isDefault = value === tabIndex;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyContent: "space-between",
    gap: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Set as default tab', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ConditionalTooltip, {
    showTooltip: isDefault,
    placement: "right"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Switch, {
    size: "small",
    checked: isDefault,
    disabled: isDefault,
    onChange: ({
      target
    }) => {
      setValue(target.checked ? tabIndex : null);
    },
    inputProps: {
      ...(isDefault ? {
        style: {
          opacity: 0,
          cursor: 'not-allowed'
        }
      } : {})
    }
  })));
};
const TabLabelControl = ({
  elementId
}) => {
  const editorSettings = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.useElementEditorSettings)(elementId);
  const label = editorSettings?.title ?? '';
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Stack, {
    gap: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Tab name', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.TextField, {
    size: "tiny",
    value: label,
    onChange: ({
      target
    }) => {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_2__.updateElementEditorSettings)({
        elementId,
        settings: {
          title: target.value
        }
      });
    }
  }));
};
const ConditionalTooltip = ({
  showTooltip,
  children
}) => {
  if (!showTooltip) {
    return children;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Infotip, {
    arrow: false,
    content: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Alert, {
      color: "secondary",
      icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_4__.InfoCircleFilledIcon, {
        fontSize: "tiny"
      }),
      size: "small",
      sx: {
        width: 288
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Typography, {
      variant: "body2"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('To change the default tab, simply set another tab as default.', 'elementor')))
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, children));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/tabs-control/use-actions.ts":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/tabs-control/use-actions.ts ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TAB_CONTENT_ELEMENT_TYPE: function() { return /* binding */ TAB_CONTENT_ELEMENT_TYPE; },
/* harmony export */   TAB_ELEMENT_TYPE: function() { return /* binding */ TAB_ELEMENT_TYPE; },
/* harmony export */   useActions: function() { return /* binding */ useActions; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const TAB_ELEMENT_TYPE = 'e-tab';
const TAB_CONTENT_ELEMENT_TYPE = 'e-tab-content';
const useActions = () => {
  const {
    value,
    setValue: setDefaultActiveTab
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.useBoundProp)(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil);
  const defaultActiveTab = value ?? 0;
  const duplicateItem = ({
    items,
    tabContentAreaId
  }) => {
    const newDefault = calculateDefaultOnDuplicate({
      items,
      defaultActiveTab
    });
    items.forEach(({
      item,
      index
    }) => {
      const tabId = item.id;
      const tabContentAreaContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(tabContentAreaId);
      const tabContentId = tabContentAreaContainer?.children?.[index]?.id;
      if (!tabContentId) {
        throw new Error('Original content ID is required for duplication');
      }
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.duplicateElements)({
        elementIds: [tabId, tabContentId],
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Duplicate Tab', 'elementor'),
        onDuplicateElements: () => {
          if (newDefault !== defaultActiveTab) {
            setDefaultActiveTab(newDefault, {}, {
              withHistory: false
            });
          }
        },
        onRestoreElements: () => {
          if (newDefault !== defaultActiveTab) {
            setDefaultActiveTab(defaultActiveTab, {}, {
              withHistory: false
            });
          }
        }
      });
    });
  };
  const moveItem = ({
    toIndex,
    tabsMenuId,
    tabContentAreaId,
    movedElementId,
    movedElementIndex
  }) => {
    const tabContentContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(tabContentAreaId);
    const tabContent = tabContentContainer?.children?.[movedElementIndex];
    const movedElement = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(movedElementId);
    const tabsMenu = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(tabsMenuId);
    if (!tabContent) {
      throw new Error('Content element is required');
    }
    if (!movedElement || !tabsMenu) {
      throw new Error('Tab element or menu not found');
    }
    const newDefault = calculateDefaultOnMove({
      from: movedElementIndex,
      to: toIndex,
      defaultActiveTab
    });
    (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.moveElements)({
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Reorder Tabs', 'elementor'),
      moves: [{
        element: movedElement,
        targetContainer: tabsMenu,
        options: {
          at: toIndex
        }
      }, {
        element: tabContent,
        targetContainer: tabContentContainer,
        options: {
          at: toIndex
        }
      }],
      onMoveElements: () => {
        if (newDefault !== defaultActiveTab) {
          setDefaultActiveTab(newDefault, {}, {
            withHistory: false
          });
        }
      },
      onRestoreElements: () => {
        if (newDefault !== defaultActiveTab) {
          setDefaultActiveTab(defaultActiveTab, {}, {
            withHistory: false
          });
        }
      }
    });
  };
  const removeItem = ({
    items,
    tabContentAreaId
  }) => {
    const newDefault = calculateDefaultOnRemove({
      items,
      defaultActiveTab
    });
    (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.removeElements)({
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Tabs', 'elementor'),
      elementIds: items.flatMap(({
        item,
        index
      }) => {
        const tabId = item.id;
        const tabContentContainer = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(tabContentAreaId);
        const tabContentId = tabContentContainer?.children?.[index]?.id;
        if (!tabContentId) {
          throw new Error('Content ID is required');
        }
        return [tabId, tabContentId];
      }),
      onRemoveElements: () => {
        if (newDefault !== defaultActiveTab) {
          setDefaultActiveTab(newDefault, {}, {
            withHistory: false
          });
        }
      },
      onRestoreElements: () => {
        if (newDefault !== defaultActiveTab) {
          setDefaultActiveTab(defaultActiveTab, {}, {
            withHistory: false
          });
        }
      }
    });
  };
  const addItem = ({
    tabContentAreaId,
    tabsMenuId,
    items
  }) => {
    const tabContentArea = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(tabContentAreaId);
    const tabsMenu = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getContainer)(tabsMenuId);
    if (!tabContentArea || !tabsMenu) {
      throw new Error('Tab containers not found');
    }
    items.forEach(({
      index
    }) => {
      const position = index + 1;
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.createElements)({
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Tabs', 'elementor'),
        elements: [{
          container: tabContentArea,
          model: {
            elType: TAB_CONTENT_ELEMENT_TYPE,
            editor_settings: {
              title: `Tab ${position} content`,
              initial_position: position
            }
          }
        }, {
          container: tabsMenu,
          model: {
            elType: TAB_ELEMENT_TYPE,
            editor_settings: {
              title: `Tab ${position} trigger`,
              initial_position: position
            }
          }
        }]
      });
    });
  };
  return {
    duplicateItem,
    moveItem,
    removeItem,
    addItem
  };
};
const calculateDefaultOnMove = ({
  from,
  to,
  defaultActiveTab
}) => {
  if (from === defaultActiveTab) {
    return to;
  }
  if (from < defaultActiveTab && to >= defaultActiveTab) {
    return defaultActiveTab - 1;
  }
  if (from > defaultActiveTab && to <= defaultActiveTab) {
    return defaultActiveTab + 1;
  }
  return defaultActiveTab;
};
const calculateDefaultOnRemove = ({
  items,
  defaultActiveTab
}) => {
  const isDefault = items.some(({
    index
  }) => index === defaultActiveTab);
  if (isDefault) {
    return 0;
  }
  const defaultGap = items.reduce((acc, {
    index
  }) => index < defaultActiveTab ? acc + 1 : acc, 0);
  return defaultActiveTab - defaultGap;
};
const calculateDefaultOnDuplicate = ({
  items,
  defaultActiveTab
}) => {
  const duplicatesBefore = items.reduce((acc, {
    index
  }) => {
    const isDuplicatedBeforeDefault = index < defaultActiveTab;
    return isDuplicatedBeforeDefault ? acc + 1 : acc;
  }, 0);
  return defaultActiveTab + duplicatesBefore;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/settings-field.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/settings-field.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SettingsField: function() { return /* binding */ SettingsField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../utils/prop-dependency-utils */ "./packages/packages/core/editor-editing-panel/src/utils/prop-dependency-utils.ts");
/* harmony import */ var _create_top_level_object_type__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./create-top-level-object-type */ "./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts");










const HISTORY_DEBOUNCE_WAIT = 800;
const SettingsField = ({
  bind,
  children,
  propDisplayName
}) => {
  const {
    element: {
      id: elementId
    },
    elementType: {
      propsSchema,
      dependenciesPerTargetMapping = {}
    },
    settings: currentElementSettings
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_6__.useElement)();
  const value = {
    [bind]: currentElementSettings?.[bind] ?? null
  };
  const propType = (0,_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_8__.createTopLevelObjectType)({
    schema: propsSchema
  });
  const undoableUpdateElementProp = useUndoableUpdateElementProp({
    elementId,
    propDisplayName
  });
  const {
    isDisabled,
    isHidden
  } = (0,_utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_7__.extractDependencyEffect)(bind, propsSchema, currentElementSettings);
  if (isHidden) {
    return null;
  }

  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const setValue = (newValue, _ = {}, meta) => {
    const {
      withHistory = true
    } = meta ?? {};
    const dependents = (0,_utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_7__.extractOrderedDependencies)(dependenciesPerTargetMapping);
    const settingsWithDefaults = (0,_utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_7__.getElementSettingsWithDefaults)(propsSchema, currentElementSettings);
    const settings = (0,_utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_7__.getUpdatedValues)(newValue, dependents, propsSchema, settingsWithDefaults, elementId);
    if (withHistory) {
      undoableUpdateElementProp(settings);
    } else {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__.updateElementSettings)({
        id: elementId,
        props: settings,
        withHistory: false
      });
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, {
    propType: propType,
    value: value,
    setValue: setValue,
    isDisabled: isDisabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: bind
  }, children));
};
function useUndoableUpdateElementProp({
  elementId,
  propDisplayName
}) {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__.undoable)({
      do: newSettings => {
        const prevPropValue = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__.getElementSettings)(elementId, Object.keys(newSettings));
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__.updateElementSettings)({
          id: elementId,
          props: newSettings,
          withHistory: false
        });
        (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_2__.setDocumentModifiedStatus)(true);
        return prevPropValue;
      },
      undo: ({}, prevProps) => {
        (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__.updateElementSettings)({
          id: elementId,
          props: prevProps,
          withHistory: false
        });
      }
    }, {
      title: (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_3__.getElementLabel)(elementId),
      // translators: %s is the name of the property that was edited.
      subtitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('%s edited', 'elementor').replace('%s', propDisplayName),
      debounce: {
        wait: HISTORY_DEBOUNCE_WAIT
      }
    });
  }, [elementId, propDisplayName]);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/controls-registry/styles-field.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StylesField: function() { return /* binding */ StylesField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../contexts/styles-inheritance-context */ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx");
/* harmony import */ var _field_indicators_registry__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../field-indicators-registry */ "./packages/packages/core/editor-editing-panel/src/field-indicators-registry.ts");
/* harmony import */ var _hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../hooks/use-styles-field */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts");
/* harmony import */ var _conditional_field__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./conditional-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/conditional-field.tsx");
/* harmony import */ var _create_top_level_object_type__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./create-top-level-object-type */ "./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts");









const DIMENSION_SIDES = ['block-start', 'block-end', 'inline-start', 'inline-end'];

/**
 * When a breakpoint inherits from a parent that only set *some* dimension sides, the remaining
 * sides should still show the next ancestor's value as a placeholder — not go blank.
 *
 * Example: Desktop = 10px (uniform). Tablet = inline-start 5px only. Mobile = no local value.
 * Without merging, mobile's Right / Bottom / Top placeholders would be empty because the closest
 * inherited value (tablet's partial dims) has no value for those sides.
 * With merging, we walk further up the chain and fill the gaps: inline-start gets 5px from tablet,
 * the other three sides get 10px from desktop's uniform size.
 *
 * Non-dimensions values (color, font-size, …) are returned as-is — no merging needed there.
 * @param chain
 * @param startIndex
 */
function buildResolvedPlaceholder(chain, startIndex) {
  const firstEntry = chain[startIndex];
  if (!firstEntry) {
    return undefined;
  }
  const firstValue = firstEntry.value;
  if (!_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.dimensionsPropTypeUtil.isValid(firstValue)) {
    return firstValue;
  }
  const firstDims = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.dimensionsPropTypeUtil.extract(firstValue);
  if (DIMENSION_SIDES.every(side => firstDims?.[side] !== null && firstDims?.[side] !== undefined)) {
    return firstValue;
  }
  const merged = {};
  DIMENSION_SIDES.forEach(side => {
    if (firstDims?.[side] !== null && firstDims?.[side] !== undefined) {
      merged[side] = firstDims[side];
    }
  });
  for (let i = startIndex + 1; i < chain.length; i++) {
    const val = chain[i].value;
    if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.sizePropTypeUtil.isValid(val)) {
      DIMENSION_SIDES.forEach(side => {
        if (merged[side] === null || merged[side] === undefined) {
          merged[side] = val;
        }
      });
      break;
    } else if (_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.dimensionsPropTypeUtil.isValid(val)) {
      const dims = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.dimensionsPropTypeUtil.extract(val);
      DIMENSION_SIDES.forEach(side => {
        if ((merged[side] === null || merged[side] === undefined) && dims?.[side] !== null && dims?.[side] !== undefined) {
          merged[side] = dims[side];
        }
      });
    }
    if (DIMENSION_SIDES.every(side => merged[side] !== null && merged[side] !== undefined)) {
      break;
    }
  }
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.dimensionsPropTypeUtil.create({
    'block-start': merged['block-start'] ?? null,
    'block-end': merged['block-end'] ?? null,
    'inline-start': merged['inline-start'] ?? null,
    'inline-end': merged['inline-end'] ?? null
  });
}
const StylesField = ({
  bind,
  propDisplayName,
  children
}) => {
  const stylesSchema = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__.getStylesSchema)();
  const stylesInheritanceChain = (0,_contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_4__.useStylesInheritanceChain)([bind]);
  const {
    value,
    canEdit,
    ...fields
  } = (0,_hooks_use_styles_field__WEBPACK_IMPORTED_MODULE_6__.useStylesField)(bind, {
    history: {
      propDisplayName
    }
  });
  const propType = (0,_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_8__.createTopLevelObjectType)({
    schema: stylesSchema
  });

  // Placeholders represent the inherited value the user would see if they cleared their local
  // override. The chain is ordered most-specific-first, so when the current breakpoint has its
  // own value, chain[0] IS that value — showing it as a placeholder would be meaningless.
  // We skip to chain[1] so controls display what's truly inherited from parent breakpoints.
  //
  // buildResolvedPlaceholder also handles the case where the inherited value itself is a partial
  // dimensions (e.g. tablet only overrides inline-start): it merges subsequent chain entries to
  // fill the missing sides, so "Right / Bottom" inputs don't lose their inherited placeholder.
  const placeholderStartIndex = value ? 1 : 0;
  const placeholderValues = {
    [bind]: buildResolvedPlaceholder(stylesInheritanceChain, placeholderStartIndex)
  };
  const setValue = newValue => {
    fields.setValue(newValue[bind]);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlAdornmentsProvider, {
    items: (0,_field_indicators_registry__WEBPACK_IMPORTED_MODULE_5__.getFieldIndicators)('styles')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, {
    propType: propType,
    value: {
      [bind]: value
    },
    setValue: setValue,
    placeholder: placeholderValues,
    isDisabled: () => !canEdit
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: bind
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_conditional_field__WEBPACK_IMPORTED_MODULE_7__.ConditionalField, null, children))));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/components/background-control-dynamic-tag.tsx":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/components/background-control-dynamic-tag.tsx ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BackgroundControlDynamicTagIcon: function() { return /* binding */ BackgroundControlDynamicTagIcon; },
/* harmony export */   BackgroundControlDynamicTagLabel: function() { return /* binding */ BackgroundControlDynamicTagLabel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_dynamic_tag__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-dynamic-tag */ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-dynamic-tag.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }






// Since this is injected, the initial prop provider does not dig into the nested structure of the value.
// We need to synthetically create a type that matches the expected structure of the value.

const BackgroundControlDynamicTagIcon = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
  fontSize: "tiny"
});
const BackgroundControlDynamicTagLabel = ({
  value
}) => {
  const context = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.backgroundImageOverlayPropTypeUtil);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, _extends({}, context, {
    value: value.value
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: "image"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Wrapper, {
    rawValue: value.value
  })));
};
const Wrapper = ({
  rawValue
}) => {
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const imageOverlayPropType = propType.prop_types['background-image-overlay'];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, {
    propType: imageOverlayPropType.shape.image,
    value: rawValue,
    setValue: () => void 0
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: "src"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Content, {
    rawValue: rawValue.image
  })));
};
const Content = ({
  rawValue
}) => {
  const src = rawValue.value.src;
  const dynamicTag = (0,_hooks_use_dynamic_tag__WEBPACK_IMPORTED_MODULE_4__.useDynamicTag)(src.value.name || '');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, dynamicTag?.label);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-conditional-control.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-conditional-control.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DynamicConditionalControl: function() { return /* binding */ DynamicConditionalControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);



const DynamicConditionalControl = ({
  children,
  propType,
  propsSchema,
  dynamicSettings
}) => {
  const defaults = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!propsSchema) {
      return {};
    }
    const entries = Object.entries(propsSchema);
    return entries.reduce((result, [key, prop]) => {
      result[key] = prop?.default ?? null;
      return result;
    }, {});
  }, [propsSchema]);
  const convertedSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    if (!dynamicSettings) {
      return {};
    }
    return Object.entries(dynamicSettings).reduce((result, [key, dynamicValue]) => {
      if (dynamicValue && typeof dynamicValue === 'object' && '$$type' in dynamicValue) {
        result[key] = dynamicValue;
      } else {
        result[key] = {
          $$type: 'plain',
          value: dynamicValue
        };
      }
      return result;
    }, {});
  }, [dynamicSettings]);
  const effectiveSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return {
      ...defaults,
      ...convertedSettings
    };
  }, [defaults, convertedSettings]);
  if (!propType?.dependencies?.terms.length) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, children);
  }
  const isHidden = !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.isDependencyMet)(propType?.dependencies, effectiveSettings).isMet;
  return isHidden ? null : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, children);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection-control.tsx":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection-control.tsx ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DynamicSelectionControl: function() { return /* binding */ DynamicSelectionControl; },
/* harmony export */   DynamicSettingsPopover: function() { return /* binding */ DynamicSettingsPopover; }
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
/* harmony import */ var _controls_registry_control__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../controls-registry/control */ "./packages/packages/core/editor-editing-panel/src/controls-registry/control.tsx");
/* harmony import */ var _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../controls-registry/controls-registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx");
/* harmony import */ var _controls_registry_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../controls-registry/create-top-level-object-type */ "./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts");
/* harmony import */ var _hooks_use_license_config__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../hooks/use-license-config */ "./packages/packages/core/editor-editing-panel/src/hooks/use-license-config.ts");
/* harmony import */ var _hooks_use_persist_dynamic_value__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../hooks/use-persist-dynamic-value */ "./packages/packages/core/editor-editing-panel/src/hooks/use-persist-dynamic-value.ts");
/* harmony import */ var _dynamic_control__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../dynamic-control */ "./packages/packages/core/editor-editing-panel/src/dynamics/dynamic-control.tsx");
/* harmony import */ var _hooks_use_dynamic_tag__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../hooks/use-dynamic-tag */ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-dynamic-tag.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");
/* harmony import */ var _dynamic_selection__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./dynamic-selection */ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }















const SIZE = 'tiny';
const tagsWithoutTabs = ['popup'];
const DynamicSelectionControl = ({
  OriginalControl,
  ...props
}) => {
  const {
    setValue: setAnyValue,
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const {
    bind,
    value
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_utils__WEBPACK_IMPORTED_MODULE_13__.dynamicPropTypeUtil);
  const {
    expired: readonly
  } = (0,_hooks_use_license_config__WEBPACK_IMPORTED_MODULE_9__.useLicenseConfig)();
  const originalPropType = (0,_controls_registry_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_8__.createTopLevelObjectType)({
    schema: {
      [bind]: propType
    }
  });
  const [propValueFromHistory] = (0,_hooks_use_persist_dynamic_value__WEBPACK_IMPORTED_MODULE_10__.usePersistDynamicValue)(bind);
  const selectionPopoverState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.usePopupState)({
    variant: 'popover'
  });
  const {
    name: tagName = ''
  } = value;
  const dynamicTag = (0,_hooks_use_dynamic_tag__WEBPACK_IMPORTED_MODULE_12__.useDynamicTag)(tagName);
  if (!(0,_utils__WEBPACK_IMPORTED_MODULE_13__.isDynamicTagSupported)(tagName) && OriginalControl) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, {
      propType: originalPropType,
      value: {
        [bind]: null
      },
      setValue: setAnyValue
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
      bind: bind
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(OriginalControl, props)));
  }
  const removeDynamicTag = () => {
    setAnyValue(propValueFromHistory ?? null);
  };
  if (!dynamicTag) {
    throw new Error(`Dynamic tag ${tagName} not found`);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.UnstableTag, _extends({
    fullWidth: true,
    showActionsOnHover: true,
    label: dynamicTag.label,
    startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
      fontSize: SIZE
    })
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindTrigger)(selectionPopoverState), {
    actions: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DynamicSettingsPopover, {
      dynamicTag: dynamicTag,
      disabled: readonly
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, {
      size: SIZE,
      onClick: removeDynamicTag,
      "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Remove dynamic value', 'elementor')
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.XIcon, {
      fontSize: SIZE
    })))
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Popover, _extends({
    disablePortal: true,
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
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindPopover)(selectionPopoverState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SectionPopoverBody, {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Dynamic tags', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_dynamic_selection__WEBPACK_IMPORTED_MODULE_14__.DynamicSelection, {
    close: selectionPopoverState.close,
    expired: readonly
  }))));
};
const DynamicSettingsPopover = ({
  dynamicTag,
  disabled = false
}) => {
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.usePopupState)({
    variant: 'popover'
  });
  const hasDynamicSettings = !!dynamicTag.atomic_controls.length;
  if (!hasDynamicSettings) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.IconButton, _extends({
    size: SIZE,
    disabled: disabled
  }, !disabled && (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindTrigger)(popupState), {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Dynamic settings', 'elementor')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.SettingsIcon, {
    fontSize: SIZE
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Popover, _extends({
    disablePortal: true,
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
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.bindPopover)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SectionPopoverBody, {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Dynamic settings', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    title: dynamicTag.label,
    onClose: popupState.close,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
      fontSize: SIZE
    })
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DynamicSettings, {
    controls: dynamicTag.atomic_controls,
    tagName: dynamicTag.name
  }))));
};
const DynamicSettings = ({
  controls,
  tagName
}) => {
  const tabs = controls.filter(({
    type
  }) => type === 'section');
  const {
    getTabsProps,
    getTabProps,
    getTabPanelProps
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.useTabs)(0);
  if (!tabs.length) {
    // Dynamic must have hierarchical controls.
    return null;
  }
  if (tagsWithoutTabs.includes(tagName)) {
    const singleTab = tabs[0];
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ControlsItemsStack, {
      items: singleTab.value.items
    }));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, tabs.length > 1 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tabs, _extends({
    size: "small",
    variant: "fullWidth"
  }, getTabsProps()), tabs.map(({
    value
  }, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Tab, _extends({
    key: index,
    label: value.label,
    sx: {
      px: 1,
      py: 0.5
    }
  }, getTabProps(index))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), tabs.map(({
    value
  }, index) => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.TabPanel, _extends({
      key: index,
      sx: {
        flexGrow: 1,
        py: 0,
        overflowY: 'auto'
      }
    }, getTabPanelProps(index)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ControlsItemsStack, {
      items: value.items
    }));
  }));
};
const LAYOUT_OVERRIDE_FIELDS = {
  separator: 'two-columns',
  action: 'full',
  off_canvas: 'full',
  type: 'two-columns'
};
const DYNAMIC_TAG_LAYOUT_OVERRIDES = {
  select: 'full'
};
const getLayout = control => {
  const dynamicOverride = DYNAMIC_TAG_LAYOUT_OVERRIDES[control.type];
  if (dynamicOverride) {
    return dynamicOverride;
  }
  return LAYOUT_OVERRIDE_FIELDS[control.bind] ?? _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_7__.controlsRegistry.getLayout(control.type);
};
const Control = ({
  control
}) => {
  if (!_controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_7__.controlsRegistry.get(control.type)) {
    return null;
  }
  const layout = getLayout(control);
  const shouldDisablePortal = control.type === 'select';
  const baseControlProps = shouldDisablePortal ? {
    ...control.props,
    MenuProps: {
      ...(control.props?.MenuProps ?? {}),
      disablePortal: true
    }
  } : {
    ...control.props
  };
  const controlProps = {
    ...baseControlProps,
    ariaLabel: control.label
  };
  const isSwitchControl = control.type === 'switch';
  const layoutStyleProps = layout === 'two-columns' ? {
    display: 'grid',
    gridTemplateColumns: isSwitchControl ? 'minmax(0, 1fr) max-content' : '1fr 1fr'
  } : {};
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_dynamic_control__WEBPACK_IMPORTED_MODULE_11__.DynamicControl, {
    bind: control.bind
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Grid, {
    container: true,
    gap: 0.75,
    sx: layoutStyleProps
  }, control.label ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Grid, {
    item: true,
    xs: 12
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, control.label)) : null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Grid, {
    item: true,
    xs: 12
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_registry_control__WEBPACK_IMPORTED_MODULE_6__.Control, {
    type: control.type,
    props: controlProps
  }))));
};
function ControlsItemsStack({
  items
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
    p: 2,
    gap: 2,
    sx: {
      overflowY: 'auto'
    }
  }, items.map(item => item.type === 'control' ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Control, {
    key: item.value.bind,
    control: item.value
  }) : null));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection.tsx":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection.tsx ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DynamicSelection: function() { return /* binding */ DynamicSelection; }
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
/* harmony import */ var _hooks_use_persist_dynamic_value__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../hooks/use-persist-dynamic-value */ "./packages/packages/core/editor-editing-panel/src/hooks/use-persist-dynamic-value.ts");
/* harmony import */ var _hooks_use_prop_dynamic_tags__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-prop-dynamic-tags */ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-tags.ts");
/* harmony import */ var _sync_get_atomic_dynamic_tags__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../sync/get-atomic-dynamic-tags */ "./packages/packages/core/editor-editing-panel/src/dynamics/sync/get-atomic-dynamic-tags.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");











const SIZE = 'tiny';
const PROMO_TEXT_WIDTH = 170;
const PRO_DYNAMIC_TAGS_URL = 'https://go.elementor.com/go-pro-dynamic-tags-modal/';
const RENEW_DYNAMIC_TAGS_URL = 'https://go.elementor.com/go-pro-dynamic-tags-renew-modal/';
const DynamicSelection = ({
  close: closePopover,
  expired = false
}) => {
  const [searchValue, setSearchValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const {
    groups: dynamicGroups
  } = (0,_sync_get_atomic_dynamic_tags__WEBPACK_IMPORTED_MODULE_8__.getAtomicDynamicTags)() || {};
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.useTheme)();
  const {
    value: anyValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const {
    bind,
    value: dynamicValue,
    setValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_utils__WEBPACK_IMPORTED_MODULE_9__.dynamicPropTypeUtil);
  const [, updatePropValueHistory] = (0,_hooks_use_persist_dynamic_value__WEBPACK_IMPORTED_MODULE_6__.usePersistDynamicValue)(bind);
  const isCurrentValueDynamic = !!dynamicValue;
  const options = useFilteredOptions(searchValue);
  const hasNoDynamicTags = !options.length && !searchValue.trim();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (hasNoDynamicTags) {
      (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackViewPromotion)({
        target_name: 'dynamic_tags'
      });
    } else if (expired) {
      (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackViewPromotion)({
        target_name: 'dynamic_tags'
      });
    }
  }, [hasNoDynamicTags, expired]);
  const handleSearch = value => {
    setSearchValue(value);
  };
  const handleSetDynamicTag = value => {
    if (!isCurrentValueDynamic) {
      updatePropValueHistory(anyValue);
    }
    const selectedOption = options.flatMap(([, items]) => items).find(item => item.value === value);
    setValue({
      name: value,
      group: selectedOption?.group ?? '',
      settings: {
        label: selectedOption?.label
      }
    });
    closePopover();
  };
  const virtualizedItems = options.flatMap(([category, items]) => [{
    type: 'category',
    value: category,
    label: dynamicGroups?.[category]?.title || category
  }, ...items.map(item => ({
    type: 'item',
    value: item.value,
    label: item.label
  }))]);
  const getPopOverContent = () => {
    if (hasNoDynamicTags) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(NoDynamicTags, null);
    }
    if (expired) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ExpiredDynamicTags, null);
    }
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SearchField, {
      value: searchValue,
      onSearch: handleSearch,
      placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Search dynamic tags…', 'elementor')
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverMenuList, {
      items: virtualizedItems,
      onSelect: handleSetDynamicTag,
      onClose: closePopover,
      selectedValue: dynamicValue?.name,
      itemStyle: item => item.type === 'item' ? {
        paddingInlineStart: theme.spacing(3.5)
      } : {},
      noResultsComponent: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(NoResults, {
        searchValue: searchValue,
        onClear: () => setSearchValue('')
      })
    }));
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SectionPopoverBody, {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Dynamic tags', 'elementor')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Dynamic tags', 'elementor'),
    onClose: closePopover,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
      fontSize: SIZE
    })
  }), getPopOverContent());
};
const NoResults = ({
  searchValue,
  onClear
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
  gap: 1,
  alignItems: "center",
  justifyContent: "center",
  height: "100%",
  p: 2.5,
  color: "text.secondary",
  sx: {
    pb: 3.5
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
  fontSize: "large"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
  align: "center",
  variant: "subtitle2"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Sorry, nothing matched', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), "\u201C", searchValue, "\u201D."), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
  align: "center",
  variant: "caption",
  sx: {
    display: 'flex',
    flexDirection: 'column'
  }
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Try something else.', 'elementor'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Link, {
  color: "text.secondary",
  variant: "caption",
  component: "button",
  onClick: onClear
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Clear & try again', 'elementor'))));
const NoDynamicTags = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
  gap: 1,
  alignItems: "center",
  justifyContent: "center",
  height: "100%",
  p: 2.5,
  color: "text.secondary",
  sx: {
    pb: 3.5
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
  fontSize: "large"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
  align: "center",
  variant: "subtitle2"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Streamline your workflow with dynamic tags', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
  align: "center",
  variant: "caption",
  width: PROMO_TEXT_WIDTH
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Upgrade now to display your content dynamically.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.CtaButton, {
  size: "small",
  href: PRO_DYNAMIC_TAGS_URL,
  onClick: () => (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackUpgradePromotionClick)({
    target_name: 'dynamic_tags'
  })
})));
const ExpiredDynamicTags = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Stack, {
  gap: 1,
  alignItems: "center",
  justifyContent: "center",
  height: "100%",
  p: 2.5,
  color: "text.secondary",
  sx: {
    pb: 3.5
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DatabaseIcon, {
  fontSize: "large"
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
  align: "center",
  variant: "subtitle2"
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Unlock your Dynamic tags again', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.Typography, {
  align: "center",
  variant: "caption",
  width: PROMO_TEXT_WIDTH
}, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Dynamic tags need Elementor Pro. Renew now to keep them active.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.CtaButton, {
  size: "small",
  href: RENEW_DYNAMIC_TAGS_URL,
  onClick: () => (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.trackUpgradePromotionClick)({
    target_name: 'dynamic_tags'
  }),
  children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Renew Now', 'elementor')
})));
const useFilteredOptions = searchValue => {
  const dynamicTags = (0,_hooks_use_prop_dynamic_tags__WEBPACK_IMPORTED_MODULE_7__.usePropDynamicTags)();
  const options = dynamicTags.reduce((categories, {
    name,
    label,
    group
  }) => {
    const isVisible = label.toLowerCase().includes(searchValue.trim().toLowerCase());
    if (!isVisible) {
      return categories;
    }
    if (!categories.has(group)) {
      categories.set(group, []);
    }
    categories.get(group)?.push({
      label,
      group,
      value: name
    });
    return categories;
  }, new Map());
  return [...options];
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/dynamic-control.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/dynamic-control.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DynamicControl: function() { return /* binding */ DynamicControl; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _controls_registry_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../controls-registry/create-top-level-object-type */ "./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts");
/* harmony import */ var _components_dynamic_conditional_control__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/dynamic-conditional-control */ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-conditional-control.tsx");
/* harmony import */ var _hooks_use_dynamic_tag__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./hooks/use-dynamic-tag */ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-dynamic-tag.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");






const DynamicControl = ({
  bind,
  children
}) => {
  const {
    value,
    setValue
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)(_utils__WEBPACK_IMPORTED_MODULE_5__.dynamicPropTypeUtil);
  const {
    name = '',
    group = '',
    settings
  } = value ?? {};
  const dynamicTag = (0,_hooks_use_dynamic_tag__WEBPACK_IMPORTED_MODULE_4__.useDynamicTag)(name);
  if (!dynamicTag) {
    throw new Error(`Dynamic tag ${name} not found`);
  }
  const dynamicPropType = dynamicTag.props_schema[bind];
  const defaultValue = dynamicPropType?.default;
  const dynamicValue = settings?.[bind] ?? defaultValue;
  const setDynamicValue = newValues => {
    setValue({
      name,
      group,
      settings: {
        ...settings,
        ...newValues
      }
    });
  };
  const propType = (0,_controls_registry_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_2__.createTopLevelObjectType)({
    schema: dynamicTag.props_schema
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropProvider, {
    propType: propType,
    setValue: setDynamicValue,
    value: {
      [bind]: dynamicValue
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PropKeyProvider, {
    bind: bind
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_dynamic_conditional_control__WEBPACK_IMPORTED_MODULE_3__.DynamicConditionalControl, {
    propType: dynamicPropType,
    propsSchema: dynamicTag.props_schema,
    dynamicSettings: settings
  }, children)));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/dynamic-transformer.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/dynamic-transformer.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dynamicTransformer: function() { return /* binding */ dynamicTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./errors */ "./packages/packages/core/editor-editing-panel/src/dynamics/errors.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");




const dynamicTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)((value, {
  propType,
  renderContext
}) => {
  if (!value?.name || !(0,_utils__WEBPACK_IMPORTED_MODULE_3__.isDynamicTagSupported)(value.name)) {
    return propType?.default ?? null;
  }
  const renderPostId = renderContext?.currentPostId;
  return getDynamicValue(value.name, simpleTransform(value?.settings ?? {}), renderPostId);
});

// Temporary naive transformation until we'll have a `backendTransformer` that
// will replace the `dynamicTransformer` client implementation.
function simpleTransform(props) {
  const transformed = Object.entries(props).map(([settingKey, settingValue]) => {
    const value = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.isTransformable)(settingValue) ? settingValue.value : settingValue;
    return [settingKey, value];
  });
  return Object.fromEntries(transformed);
}
function getDynamicValue(name, settings, renderPostId) {
  const {
    dynamicTags
  } = window.elementor ?? {};
  if (!dynamicTags) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_2__.DynamicTagsManagerNotFoundError();
  }
  const getTagValue = () => {
    const tag = dynamicTags.createTag('v4-dynamic-tag', name, settings);
    if (!tag) {
      return null;
    }
    if (renderPostId) {
      tag.editorRenderPostId = renderPostId;
    }
    return dynamicTags.loadTagDataFromCache(tag) ?? null;
  };
  const tagValue = getTagValue();
  if (tagValue !== null) {
    return tagValue;
  }
  return new Promise(resolve => {
    dynamicTags.refreshCacheFromServer(() => {
      resolve(getTagValue());
    });
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/errors.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/errors.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DynamicTagsManagerNotFoundError: function() { return /* binding */ DynamicTagsManagerNotFoundError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const DynamicTagsManagerNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'dynamic_tags_manager_not_found',
  message: 'Dynamic tags manager not found'
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-dynamic-tag.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-dynamic-tag.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useDynamicTag: function() { return /* binding */ useDynamicTag; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _use_prop_dynamic_tags__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-prop-dynamic-tags */ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-tags.ts");


const useDynamicTag = tagName => {
  const dynamicTags = (0,_use_prop_dynamic_tags__WEBPACK_IMPORTED_MODULE_1__.useAllPropDynamicTags)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => dynamicTags.find(tag => tag.name === tagName) ?? null, [dynamicTags, tagName]);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-action.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-action.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   usePropDynamicAction: function() { return /* binding */ usePropDynamicAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_dynamic_selection__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/dynamic-selection */ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection.tsx");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");






const usePropDynamicAction = () => {
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const visible = !!propType && (0,_utils__WEBPACK_IMPORTED_MODULE_5__.supportsDynamic)(propType);
  return {
    visible,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.DatabaseIcon,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Dynamic tags', 'elementor'),
    content: ({
      close
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_dynamic_selection__WEBPACK_IMPORTED_MODULE_4__.DynamicSelection, {
      close: close
    })
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-tags.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-tags.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useAllPropDynamicTags: function() { return /* binding */ useAllPropDynamicTags; },
/* harmony export */   usePropDynamicTags: function() { return /* binding */ usePropDynamicTags; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _sync_get_atomic_dynamic_tags__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../sync/get-atomic-dynamic-tags */ "./packages/packages/core/editor-editing-panel/src/dynamics/sync/get-atomic-dynamic-tags.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");




const usePropDynamicTags = () => {
  return usePropDynamicTagsInternal(true);
};
const useAllPropDynamicTags = () => {
  return usePropDynamicTagsInternal(false);
};
const usePropDynamicTagsInternal = filterByLicense => {
  let categories = [];
  const {
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  if (propType) {
    const propDynamicType = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.getDynamicPropType)(propType);
    categories = propDynamicType?.settings.categories || [];
  }
  const categoriesKey = categories.join();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => getDynamicTagsByCategories(categories, filterByLicense),
  // eslint-disable-next-line react-hooks/exhaustive-deps
  [categoriesKey, filterByLicense]);
};
const getDynamicTagsByCategories = (categories, filterByLicense) => {
  const {
    tags,
    groups
  } = (0,_sync_get_atomic_dynamic_tags__WEBPACK_IMPORTED_MODULE_2__.getAtomicDynamicTags)(filterByLicense) || {};
  if (!categories.length || !tags || !groups) {
    return [];
  }
  const _categories = new Set(categories);
  const dynamicTags = [];
  const groupedFilteredTags = {};
  for (const tag of Object.values(tags)) {
    if (!tag.categories.some(category => _categories.has(category))) {
      continue;
    }
    if (!groupedFilteredTags[tag.group]) {
      groupedFilteredTags[tag.group] = [];
    }
    groupedFilteredTags[tag.group].push(tag);
  }
  for (const group in groups) {
    if (groupedFilteredTags[group]) {
      dynamicTags.push(...groupedFilteredTags[group]);
    }
  }
  return dynamicTags;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/init.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/init.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/menus */ "@elementor/menus");
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_menus__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_background_control_dynamic_tag__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/background-control-dynamic-tag */ "./packages/packages/core/editor-editing-panel/src/dynamics/components/background-control-dynamic-tag.tsx");
/* harmony import */ var _components_dynamic_selection_control__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/dynamic-selection-control */ "./packages/packages/core/editor-editing-panel/src/dynamics/components/dynamic-selection-control.tsx");
/* harmony import */ var _dynamic_transformer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./dynamic-transformer */ "./packages/packages/core/editor-editing-panel/src/dynamics/dynamic-transformer.ts");
/* harmony import */ var _hooks_use_prop_dynamic_action__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./hooks/use-prop-dynamic-action */ "./packages/packages/core/editor-editing-panel/src/dynamics/hooks/use-prop-dynamic-action.tsx");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");








const {
  registerPopoverAction
} = _elementor_menus__WEBPACK_IMPORTED_MODULE_2__.controlActionsMenu;
const init = () => {
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.registerControlReplacement)({
    component: _components_dynamic_selection_control__WEBPACK_IMPORTED_MODULE_4__.DynamicSelectionControl,
    condition: ({
      value
    }) => (0,_utils__WEBPACK_IMPORTED_MODULE_7__.isDynamicPropValue)(value)
  });
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.injectIntoRepeaterItemLabel)({
    id: 'dynamic-background-image',
    condition: ({
      value
    }) => (0,_utils__WEBPACK_IMPORTED_MODULE_7__.isDynamicPropValue)(value.value?.image?.value?.src),
    component: _components_background_control_dynamic_tag__WEBPACK_IMPORTED_MODULE_3__.BackgroundControlDynamicTagLabel
  });
  (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.injectIntoRepeaterItemIcon)({
    id: 'dynamic-background-image',
    condition: ({
      value
    }) => (0,_utils__WEBPACK_IMPORTED_MODULE_7__.isDynamicPropValue)(value.value?.image?.value?.src),
    component: _components_background_control_dynamic_tag__WEBPACK_IMPORTED_MODULE_3__.BackgroundControlDynamicTagIcon
  });
  registerPopoverAction({
    id: 'dynamic-tags',
    priority: 20,
    useProps: _hooks_use_prop_dynamic_action__WEBPACK_IMPORTED_MODULE_6__.usePropDynamicAction
  });
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.styleTransformersRegistry.register('dynamic', _dynamic_transformer__WEBPACK_IMPORTED_MODULE_5__.dynamicTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.settingsTransformersRegistry.register('dynamic', _dynamic_transformer__WEBPACK_IMPORTED_MODULE_5__.dynamicTransformer);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/sync/get-atomic-dynamic-tags.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/sync/get-atomic-dynamic-tags.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getAtomicDynamicTags: function() { return /* binding */ getAtomicDynamicTags; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_use_license_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../hooks/use-license-config */ "./packages/packages/core/editor-editing-panel/src/hooks/use-license-config.ts");


const getAtomicDynamicTags = (shouldFilterByLicense = true) => {
  const {
    atomicDynamicTags
  } = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.getElementorConfig)();
  if (!atomicDynamicTags) {
    return null;
  }
  return {
    tags: shouldFilterByLicense ? filterByLicense(atomicDynamicTags.tags) : atomicDynamicTags.tags,
    groups: atomicDynamicTags.groups
  };
};
const filterByLicense = tags => {
  const {
    expired
  } = (0,_hooks_use_license_config__WEBPACK_IMPORTED_MODULE_1__.getLicenseConfig)();
  if (expired) {
    return Object.fromEntries(Object.entries(tags).filter(([, tag]) => !(tag?.meta?.origin === 'elementor' && tag?.meta?.required_license)));
  }
  return tags;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dynamicPropTypeUtil: function() { return /* binding */ dynamicPropTypeUtil; },
/* harmony export */   getDynamicPropType: function() { return /* binding */ getDynamicPropType; },
/* harmony export */   isDynamicPropValue: function() { return /* binding */ isDynamicPropValue; },
/* harmony export */   isDynamicTagSupported: function() { return /* binding */ isDynamicTagSupported; },
/* harmony export */   supportsDynamic: function() { return /* binding */ supportsDynamic; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__);



const DYNAMIC_PROP_TYPE_KEY = 'dynamic';
const dynamicPropTypeUtil = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)(DYNAMIC_PROP_TYPE_KEY, _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.strictObject({
  name: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
  group: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
  settings: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.any().optional()
}));
const isDynamicTagSupported = tagName => {
  return !!(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.getElementorConfig)()?.atomicDynamicTags?.tags?.[tagName];
};
const isDynamicPropType = prop => prop.key === DYNAMIC_PROP_TYPE_KEY;
const getDynamicPropType = propType => {
  const dynamicPropType = propType.kind === 'union' && propType.prop_types[DYNAMIC_PROP_TYPE_KEY];
  return dynamicPropType && isDynamicPropType(dynamicPropType) ? dynamicPropType : null;
};
const isDynamicPropValue = prop => {
  return (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(prop) && prop.$$type === DYNAMIC_PROP_TYPE_KEY;
};
const supportsDynamic = propType => {
  return !!getDynamicPropType(propType);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/editing-panel-replacement-registry.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/editing-panel-replacement-registry.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getEditingPanelReplacement: function() { return /* binding */ getEditingPanelReplacement; },
/* harmony export */   registerEditingPanelReplacement: function() { return /* binding */ registerEditingPanelReplacement; }
/* harmony export */ });
const registry = new Map();
const DEFAULT_PRIORITY = 10;
const registerEditingPanelReplacement = ({
  id,
  priority = DEFAULT_PRIORITY,
  ...props
}) => {
  registry.set(id, {
    ...props,
    priority
  });
};
const getEditingPanelReplacement = (element, elementType) => Array.from(registry.values()).filter(({
  condition
}) => condition(element, elementType)).sort((a, b) => a.priority - b.priority)?.[0] ?? null;

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/errors.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/errors.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ControlTypeAlreadyRegisteredError: function() { return /* binding */ ControlTypeAlreadyRegisteredError; },
/* harmony export */   ControlTypeNotFoundError: function() { return /* binding */ ControlTypeNotFoundError; },
/* harmony export */   ControlTypeNotRegisteredError: function() { return /* binding */ ControlTypeNotRegisteredError; },
/* harmony export */   StyleNotFoundUnderProviderError: function() { return /* binding */ StyleNotFoundUnderProviderError; },
/* harmony export */   StylesProviderCannotUpdatePropsError: function() { return /* binding */ StylesProviderCannotUpdatePropsError; },
/* harmony export */   StylesProviderNotFoundError: function() { return /* binding */ StylesProviderNotFoundError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const ControlTypeNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'control_type_not_found',
  message: 'Control type not found.'
});
const ControlTypeAlreadyRegisteredError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'control_type_already_registered',
  message: 'Control type is already registered.'
});
const ControlTypeNotRegisteredError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'control_type_not_registered',
  message: 'Control type is not registered.'
});
const StylesProviderNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'provider_not_found',
  message: 'Styles provider not found.'
});
const StylesProviderCannotUpdatePropsError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'provider_cannot_update_props',
  message: "Styles provider doesn't support updating props."
});
const StyleNotFoundUnderProviderError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'style_not_found_under_provider',
  message: 'Style not found under the provider.'
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/field-indicators-registry.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/field-indicators-registry.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FIELD_TYPE: function() { return /* binding */ FIELD_TYPE; },
/* harmony export */   getFieldIndicators: function() { return /* binding */ getFieldIndicators; },
/* harmony export */   registerFieldIndicator: function() { return /* binding */ registerFieldIndicator; }
/* harmony export */ });
const indicatorsRegistry = {
  settings: new Map(),
  styles: new Map()
};
const DEFAULT_PRIORITY = 10;
const FIELD_TYPE = {
  SETTINGS: 'settings',
  STYLES: 'styles'
};
const registerFieldIndicator = ({
  fieldType,
  id,
  indicator,
  priority = DEFAULT_PRIORITY
}) => {
  indicatorsRegistry[fieldType].set(id, {
    id,
    indicator,
    priority
  });
};
const getFieldIndicators = fieldType => Array.from(indicatorsRegistry[fieldType].values()).sort((a, b) => a.priority - b.priority).map(({
  id,
  indicator: Adornment
}) => ({
  id,
  Adornment
}));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-active-style-def-id.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-active-style-def-id.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useActiveStyleDefId: function() { return /* binding */ useActiveStyleDefId; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _use_state_by_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-state-by-element */ "./packages/packages/core/editor-editing-panel/src/hooks/use-state-by-element.ts");




function useActiveStyleDefId(classProp) {
  const [activeStyledDefId, setActiveStyledDefId] = (0,_use_state_by_element__WEBPACK_IMPORTED_MODULE_3__.useStateByElement)('active-style-id', null);
  const appliedClassesIds = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_2__.usePanelElementSetting)(classProp)?.value || [];
  const validAppliedClassesIds = useValidClassIds(appliedClassesIds);
  const fallback = useFirstAppliedClass(appliedClassesIds);
  const activeAndAppliedClassId = useActiveAndAppliedClassId(activeStyledDefId, validAppliedClassesIds);
  return [activeAndAppliedClassId || fallback?.id || null, setActiveStyledDefId];
}
function useValidClassIds(appliedClassesIds) {
  const providers = (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.useProviders)();
  const allKnownIds = new Set(providers.flatMap(provider => provider.actions.all().map(style => style.id)));
  return appliedClassesIds.filter(id => allKnownIds.has(id));
}
function useFirstAppliedClass(appliedClassesIds) {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_2__.useElement)();
  const stylesDefs = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementStyles)(element.id) ?? {};
  return Object.values(stylesDefs).find(styleDef => appliedClassesIds.includes(styleDef.id));
}
function useActiveAndAppliedClassId(id, appliedClassesIds) {
  const isClassApplied = !!id && appliedClassesIds.includes(id);
  return isClassApplied ? id : null;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-computed-style.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-computed-style.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useComputedStyle: function() { return /* binding */ useComputedStyle; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function useComputedStyle(elementId) {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/device-mode/change'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/reset-style'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/settings'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/paste-style')], () => {
    if (!elementId) {
      return null;
    }
    const element = window.elementor?.getContainer?.(elementId);
    if (!element?.view?.el) {
      return null;
    }
    const resp = window.getComputedStyle(element.view.el);
    return resp;
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-custom-css.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-custom-css.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCustomCss: function() { return /* binding */ useCustomCss; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-editing-panel/src/errors.ts");
/* harmony import */ var _use_styles_fields__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./use-styles-fields */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts");
/* harmony import */ var _use_styles_rerender__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./use-styles-rerender */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-rerender.ts");













const useCustomCss = () => {
  const {
    element: {
      id: elementId
    }
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_8__.useElement)();
  const {
    id: styleId,
    meta,
    provider
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_9__.useStyle)();
  const style = provider?.actions.get(styleId, {
    elementId
  });
  const undoableUpdateStyle = useUndoableActions({
    elementId,
    meta
  });
  const currentStyleId = styleId ? styleId : null;
  const currentProvider = styleId ? provider : null;
  (0,_use_styles_rerender__WEBPACK_IMPORTED_MODULE_12__.useStylesRerender)();
  const variant = style ? (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__.getVariantByMeta)(style, meta) : null;
  const setCustomCss = (raw, {
    history: {
      propDisplayName
    }
  }) => {
    const newValue = {
      raw: (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_6__.encodeString)(sanitize(raw))
    };
    undoableUpdateStyle({
      styleId: currentStyleId,
      provider: currentProvider,
      customCss: newValue,
      propDisplayName
    });
  };
  const customCss = variant?.custom_css?.raw ? {
    raw: (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_6__.decodeString)(variant.custom_css.raw)
  } : null;
  return {
    customCss,
    setCustomCss
  };
};
function useUndoableActions({
  elementId,
  meta: {
    breakpoint,
    state
  }
}) {
  const classesProp = (0,_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_7__.useClassesProp)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const meta = {
      breakpoint,
      state
    };
    const createStyleArgs = {
      elementId,
      classesProp,
      meta,
      label: _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_4__.ELEMENTS_STYLES_RESERVED_LABEL
    };
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_5__.undoable)({
      do: payload => {
        if ((0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.shouldCreateNewLocalStyle)(payload)) {
          return create(payload);
        }
        return update(payload);
      },
      undo: (payload, doReturn) => {
        if ((0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.shouldCreateNewLocalStyle)(payload)) {
          return undoCreate(payload, doReturn);
        }
        return undoUpdate(payload, doReturn);
      },
      redo: (payload, doReturn) => {
        if ((0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.shouldCreateNewLocalStyle)(payload)) {
          return create(payload, doReturn);
        }
        return update(payload);
      }
    }, {
      title: ({
        provider,
        styleId
      }) => (0,_use_styles_fields__WEBPACK_IMPORTED_MODULE_11__.getTitle)({
        provider,
        styleId,
        elementId
      }),
      subtitle: ({
        provider,
        styleId,
        propDisplayName
      }) => (0,_use_styles_fields__WEBPACK_IMPORTED_MODULE_11__.getSubtitle)({
        provider,
        styleId,
        elementId,
        propDisplayName
      }),
      debounce: {
        wait: _use_styles_fields__WEBPACK_IMPORTED_MODULE_11__.HISTORY_DEBOUNCE_WAIT
      }
    });
    function create({
      customCss
    }, redoArgs) {
      const createdStyle = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.createElementStyle)({
        ...createStyleArgs,
        props: {},
        custom_css: customCss ?? null,
        styleId: redoArgs?.createdStyleId
      });
      return {
        createdStyleId: createdStyle
      };
    }
    function undoCreate(_, {
      createdStyleId
    }) {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.deleteElementStyle)(elementId, createdStyleId);
    }
    function update({
      provider,
      styleId,
      customCss
    }) {
      if (!provider.actions.updateCustomCss) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_10__.StylesProviderCannotUpdatePropsError({
          context: {
            providerKey: provider.getKey()
          }
        });
      }
      const style = provider.actions.get(styleId, {
        elementId
      });
      const prevCustomCss = getCurrentCustomCss(style, meta);
      provider.actions.updateCustomCss({
        id: styleId,
        meta,
        custom_css: customCss
      }, {
        elementId
      });
      return {
        styleId,
        provider,
        prevCustomCss
      };
    }
    function undoUpdate(_, {
      styleId,
      provider,
      prevCustomCss
    }) {
      provider.actions.updateCustomCss?.({
        id: styleId,
        meta,
        custom_css: prevCustomCss ?? {
          raw: ''
        }
      }, {
        elementId
      });
    }
  }, [elementId, breakpoint, state, classesProp]);
}
function getCurrentCustomCss(style, meta) {
  if (!style) {
    return null;
  }
  const variant = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_3__.getVariantByMeta)(style, meta);
  return variant?.custom_css ?? null;
}
function sanitize(raw) {
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.schema.safeParse(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.create(raw)).data?.value?.trim() ?? '';
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-default-panel-settings.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-default-panel-settings.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   registerElementPanelDefaults: function() { return /* binding */ registerElementPanelDefaults; },
/* harmony export */   useDefaultPanelSettings: function() { return /* binding */ useDefaultPanelSettings; }
/* harmony export */ });
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");

const fallbackEditorSettings = {
  defaultSectionsExpanded: {
    settings: ['content', 'settings'],
    style: []
  },
  defaultTab: 'settings'
};
const elementPanelDefaults = {
  'e-div-block': {
    defaultSectionsExpanded: fallbackEditorSettings.defaultSectionsExpanded,
    defaultTab: 'style'
  },
  'e-flexbox': {
    defaultSectionsExpanded: fallbackEditorSettings.defaultSectionsExpanded,
    defaultTab: 'style'
  },
  'e-grid': {
    defaultSectionsExpanded: fallbackEditorSettings.defaultSectionsExpanded,
    defaultTab: 'style'
  },
  'e-divider': {
    defaultSectionsExpanded: fallbackEditorSettings.defaultSectionsExpanded,
    defaultTab: 'style'
  }
};
function registerElementPanelDefaults(type, defaults) {
  elementPanelDefaults[type] = defaults;
}
const useDefaultPanelSettings = () => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_0__.useElement)();
  return elementPanelDefaults[element.type] ?? fallbackEditorSettings;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useDirection: function() { return /* binding */ useDirection; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


function useDirection() {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)();
  const isUiRtl = 'rtl' === theme.direction,
    isSiteRtl = !!(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.getElementorFrontendConfig)()?.is_rtl;
  return {
    isSiteRtl,
    isUiRtl
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-license-config.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-license-config.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getLicenseConfig: function() { return /* binding */ getLicenseConfig; },
/* harmony export */   setLicenseConfig: function() { return /* binding */ setLicenseConfig; },
/* harmony export */   useLicenseConfig: function() { return /* binding */ useLicenseConfig; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

let config = {
  expired: false
};
const listeners = new Set();
function setLicenseConfig(newConfig) {
  config = {
    ...config,
    ...newConfig
  };
  listeners.forEach(listener => listener());
}
function getLicenseConfig() {
  return config;
}
function subscribe(listener) {
  listeners.add(listener);
  return () => listeners.delete(listener);
}
function useLicenseConfig() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useSyncExternalStore)(subscribe, getLicenseConfig, getLicenseConfig);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-open-editor-panel.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-open-editor-panel.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOpenEditorPanel: function() { return /* binding */ useOpenEditorPanel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../panel */ "./packages/packages/core/editor-editing-panel/src/panel.ts");
/* harmony import */ var _sync_is_atomic_widget_selected__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../sync/is-atomic-widget-selected */ "./packages/packages/core/editor-editing-panel/src/sync/is-atomic-widget-selected.ts");




const useOpenEditorPanel = () => {
  const {
    open
  } = (0,_panel__WEBPACK_IMPORTED_MODULE_2__.usePanelActions)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.commandStartEvent)('panel/editor/open'), () => {
      if ((0,_sync_is_atomic_widget_selected__WEBPACK_IMPORTED_MODULE_3__.isAtomicWidgetSelected)()) {
        open();
      }
    });
  }, []); // eslint-disable-line react-hooks/exhaustive-deps
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-persist-dynamic-value.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-persist-dynamic-value.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   usePersistDynamicValue: function() { return /* binding */ usePersistDynamicValue; }
/* harmony export */ });
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");


const usePersistDynamicValue = propKey => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_1__.useElement)();
  const prefixedKey = `dynamic/non-dynamic-values-history/${element.id}/${propKey}`;
  return (0,_elementor_session__WEBPACK_IMPORTED_MODULE_0__.useSessionStorage)(prefixedKey);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-state-by-element.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-state-by-element.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStateByElement: function() { return /* binding */ useStateByElement; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");



const useStateByElement = (key, initialValue) => {
  const {
    element
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_2__.useElement)();
  const lookup = `elementor/editor-state/${element.id}/${key}`;
  const storedValue = (0,_elementor_session__WEBPACK_IMPORTED_MODULE_1__.getSessionStorageItem)(lookup);
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(storedValue ?? initialValue);
  const doUpdate = newValue => {
    (0,_elementor_session__WEBPACK_IMPORTED_MODULE_1__.setSessionStorageItem)(lookup, newValue);
    setValue(newValue);
  };
  return [value, doUpdate];
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-styles-field.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStylesField: function() { return /* binding */ useStylesField; }
/* harmony export */ });
/* harmony import */ var _use_styles_fields__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-styles-fields */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts");

function useStylesField(propName, meta) {
  const {
    values,
    setValues,
    canEdit
  } = (0,_use_styles_fields__WEBPACK_IMPORTED_MODULE_0__.useStylesFields)([propName]);
  const value = values?.[propName] ?? null;
  const setValue = newValue => {
    setValues({
      [propName]: newValue
    }, meta);
  };
  return {
    value: value,
    setValue,
    canEdit
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HISTORY_DEBOUNCE_WAIT: function() { return /* binding */ HISTORY_DEBOUNCE_WAIT; },
/* harmony export */   getSubtitle: function() { return /* binding */ getSubtitle; },
/* harmony export */   getTitle: function() { return /* binding */ getTitle; },
/* harmony export */   useStylesFields: function() { return /* binding */ useStylesFields; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-editing-panel/src/errors.ts");
/* harmony import */ var _use_styles_rerender__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./use-styles-rerender */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-rerender.ts");












const HISTORY_DEBOUNCE_WAIT = 800;
function useStylesFields(propNames) {
  const {
    element: {
      id: elementId
    }
  } = (0,_contexts_element_context__WEBPACK_IMPORTED_MODULE_7__.useElement)();
  const {
    id: styleId,
    meta,
    provider,
    canEdit
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_8__.useStyle)();
  const undoableUpdateStyle = useUndoableActions({
    elementId,
    meta
  });
  (0,_use_styles_rerender__WEBPACK_IMPORTED_MODULE_10__.useStylesRerender)();
  const values = getProps({
    elementId,
    styleId,
    provider,
    meta,
    propNames
  });
  const setValues = (props, {
    history: {
      propDisplayName
    }
  }) => {
    if (!styleId) {
      undoableUpdateStyle({
        styleId: null,
        provider: null,
        props,
        propDisplayName
      });
    } else {
      undoableUpdateStyle({
        styleId,
        provider,
        props,
        propDisplayName
      });
    }
  };
  return {
    values,
    setValues,
    canEdit
  };
}
function getProps({
  styleId,
  elementId,
  provider,
  meta,
  propNames
}) {
  if (!provider || !styleId) {
    return null;
  }
  const style = provider.actions.get(styleId, {
    elementId
  });
  if (!style) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_9__.StyleNotFoundUnderProviderError({
      context: {
        styleId,
        providerKey: provider.getKey()
      }
    });
  }
  const variant = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getVariantByMeta)(style, meta);
  return Object.fromEntries(propNames.map(key => [key, variant?.props[key] ?? null]));
}
function useUndoableActions({
  elementId,
  meta: {
    breakpoint,
    state
  }
}) {
  const classesProp = (0,_contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_6__.useClassesProp)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const meta = {
      breakpoint,
      state
    };
    const createStyleArgs = {
      elementId,
      classesProp,
      meta,
      label: _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.ELEMENTS_STYLES_RESERVED_LABEL
    };
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_4__.undoable)({
      do: payload => {
        if ((0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.shouldCreateNewLocalStyle)(payload)) {
          return create(payload);
        }
        return update(payload);
      },
      undo: (payload, doReturn) => {
        const wasLocalStyleCreated = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.shouldCreateNewLocalStyle)(payload);
        if (wasLocalStyleCreated) {
          return undoCreate(payload, doReturn);
        }
        return undo(payload, doReturn);
      },
      redo: (payload, doReturn) => {
        const wasLocalStyleCreated = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.shouldCreateNewLocalStyle)(payload);
        if (wasLocalStyleCreated) {
          return create(payload, doReturn);
        }
        return update(payload);
      }
    }, {
      title: ({
        provider,
        styleId
      }) => getTitle({
        provider,
        styleId,
        elementId
      }),
      subtitle: ({
        provider,
        styleId,
        propDisplayName
      }) => getSubtitle({
        provider,
        styleId,
        elementId,
        propDisplayName
      }),
      debounce: {
        wait: HISTORY_DEBOUNCE_WAIT
      }
    });
    function create({
      props
    }, redoArgs) {
      const createdStyle = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.createElementStyle)({
        ...createStyleArgs,
        props,
        styleId: redoArgs?.createdStyleId
      });
      return {
        createdStyleId: createdStyle
      };
    }
    function undoCreate(_, {
      createdStyleId
    }) {
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.deleteElementStyle)(elementId, createdStyleId);
    }
    function update({
      provider,
      styleId,
      props
    }) {
      if (!provider.actions.updateProps) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_9__.StylesProviderCannotUpdatePropsError({
          context: {
            providerKey: provider.getKey()
          }
        });
      }
      const style = provider.actions.get(styleId, {
        elementId
      });
      const prevProps = getCurrentProps(style, meta);
      provider.actions.updateProps({
        id: styleId,
        meta,
        props
      }, {
        elementId
      });
      return {
        styleId,
        provider,
        prevProps
      };
    }
    function undo(_, {
      styleId,
      provider,
      prevProps
    }) {
      provider.actions.updateProps?.({
        id: styleId,
        meta,
        props: prevProps,
        mode: 'replace'
      }, {
        elementId
      });
    }
  }, [elementId, breakpoint, state, classesProp]);
}
function getCurrentProps(style, meta) {
  if (!style) {
    return {};
  }
  const variant = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getVariantByMeta)(style, meta);
  const props = variant?.props ?? {};
  return structuredClone(props);
}
const defaultHistoryTitles = {
  title: ({
    provider
  }) => {
    const providerLabel = provider.labels?.singular;
    return providerLabel ? capitalize(providerLabel) : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Style', 'elementor');
  },
  subtitle: ({
    provider,
    styleId,
    elementId,
    propDisplayName
  }) => {
    const styleLabel = provider.actions.get(styleId, {
      elementId
    })?.label;
    if (!styleLabel) {
      throw new Error(`Style ${styleId} not found`);
    }

    // translators: %s$1 is the style label, %s$2 is the name of the style property being edited
    return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)(`%s$1 %s$2 edited`, 'elementor').replace('%s$1', styleLabel).replace('%s$2', propDisplayName);
  }
};
const localStyleHistoryTitles = {
  title: ({
    elementId
  }) => (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementLabel)(elementId),
  subtitle: ({
    propDisplayName
  }) =>
  // translators: %s is the name of the style property being edited
  (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)(`%s edited`, 'elementor').replace('%s', propDisplayName)
};
function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
const isLocalStyle = (provider, styleId) => !provider || !styleId || (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.isElementsStylesProvider)(provider.getKey());
const getTitle = ({
  provider,
  styleId,
  elementId
}) => {
  const isLocal = isLocalStyle(provider, styleId);
  if (isLocal) {
    return localStyleHistoryTitles.title({
      elementId
    });
  }
  return defaultHistoryTitles.title({
    provider: provider
  });
};
const getSubtitle = ({
  provider,
  styleId,
  propDisplayName,
  elementId
}) => {
  const isLocal = isLocalStyle(provider, styleId);
  if (isLocal) {
    return localStyleHistoryTitles.subtitle({
      propDisplayName
    });
  }
  return defaultHistoryTitles.subtitle({
    provider: provider,
    styleId: styleId,
    elementId,
    propDisplayName
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-rerender.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/hooks/use-styles-rerender.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useStylesRerender: function() { return /* binding */ useStylesRerender; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");


const useStylesRerender = () => {
  const {
    provider
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_1__.useStyle)();
  const [, reRender] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useReducer)(p => !p, false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => provider?.subscribe(reRender), [provider]);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/init.ts":
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/init.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_editing_panel_hooks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/editing-panel-hooks */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-hooks.tsx");
/* harmony import */ var _components_promotions_init__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/promotions/init */ "./packages/packages/core/editor-editing-panel/src/components/promotions/init.tsx");
/* harmony import */ var _controls_registry_element_controls_registry__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./controls-registry/element-controls/registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/element-controls/registry.ts");
/* harmony import */ var _dynamics_init__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./dynamics/init */ "./packages/packages/core/editor-editing-panel/src/dynamics/init.ts");
/* harmony import */ var _panel__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./panel */ "./packages/packages/core/editor-editing-panel/src/panel.ts");
/* harmony import */ var _reset_style_props__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./reset-style-props */ "./packages/packages/core/editor-editing-panel/src/reset-style-props.tsx");
/* harmony import */ var _styles_inheritance_init__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./styles-inheritance/init */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/init.ts");
/* harmony import */ var _sync_is_atomic_widget_selected__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./sync/is-atomic-widget-selected */ "./packages/packages/core/editor-editing-panel/src/sync/is-atomic-widget-selected.ts");











function init() {
  (0,_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.__registerPanel)(_panel__WEBPACK_IMPORTED_MODULE_7__.panel);
  blockV1Panel();
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'editing-panel-hooks',
    component: _components_editing_panel_hooks__WEBPACK_IMPORTED_MODULE_3__.EditingPanelHooks
  });

  // TODO: Move it from here once we have dynamic package.
  (0,_dynamics_init__WEBPACK_IMPORTED_MODULE_6__.init)();

  // TODO: Move it from here once we have styles-inheritance package.
  (0,_styles_inheritance_init__WEBPACK_IMPORTED_MODULE_9__.init)();

  // TODO: Move it from here once we have element-controls package.
  (0,_controls_registry_element_controls_registry__WEBPACK_IMPORTED_MODULE_5__.registerElementControls)();
  (0,_reset_style_props__WEBPACK_IMPORTED_MODULE_8__.initResetStyleProps)();
  (0,_components_promotions_init__WEBPACK_IMPORTED_MODULE_4__.init)();
}
const blockV1Panel = () => {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_2__.blockCommand)({
    command: 'panel/editor/open',
    condition: _sync_is_atomic_widget_selected__WEBPACK_IMPORTED_MODULE_10__.isAtomicWidgetSelected
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/panel.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/panel.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   panel: function() { return /* binding */ panel; },
/* harmony export */   usePanelActions: function() { return /* binding */ usePanelActions; },
/* harmony export */   usePanelStatus: function() { return /* binding */ usePanelStatus; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/editing-panel */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel.tsx");


const {
  panel,
  usePanelActions,
  usePanelStatus
} = (0,_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0__.__createPanel)({
  id: 'editing-panel',
  component: _components_editing_panel__WEBPACK_IMPORTED_MODULE_1__.EditingPanel
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/provider-colors-registry.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/provider-colors-registry.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getStyleProviderColors: function() { return /* binding */ getStyleProviderColors; },
/* harmony export */   registerStyleProviderToColors: function() { return /* binding */ registerStyleProviderToColors; }
/* harmony export */ });
const DEFAULT_COLORS = {
  name: 'default',
  getThemeColor: null
};
const providerColorsRegistry = new Map();
const registerStyleProviderToColors = (provider, colors) => {
  providerColorsRegistry.set(provider, colors);
};
const getStyleProviderColors = provider => providerColorsRegistry.get(provider) ?? DEFAULT_COLORS;

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/reset-style-props.tsx":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/reset-style-props.tsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initResetStyleProps: function() { return /* binding */ initResetStyleProps; },
/* harmony export */   useResetStyleValueProps: function() { return /* binding */ useResetStyleValueProps; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-variables */ "@elementor/editor-variables");
/* harmony import */ var _elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/menus */ "@elementor/menus");
/* harmony import */ var _elementor_menus__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_menus__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _utils_is_equal__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/is-equal */ "./packages/packages/core/editor-editing-panel/src/utils/is-equal.ts");







const {
  registerAction
} = _elementor_menus__WEBPACK_IMPORTED_MODULE_3__.controlActionsMenu;
function initResetStyleProps() {
  registerAction({
    id: 'reset-style-value',
    priority: 10,
    useProps: useResetStyleValueProps
  });
}
function useResetStyleValueProps() {
  const isStyle = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_5__.useIsStyle)();
  const {
    value,
    resetValue,
    propType
  } = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_0__.useBoundProp)();
  const hasValue = value !== null && value !== undefined;
  const hasInitial = propType.initial_value !== undefined && propType.initial_value !== null;
  const isRequired = !!propType.settings?.required;
  const shouldHide = !!propType.settings?.hide_reset;
  const isPropTypeValue = value;
  const isVariable = isPropTypeValue?.$$type?.includes('variable');
  const variableExists = isVariable && (0,_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1__.hasVariable)(isPropTypeValue?.value);
  function calculateVisibility() {
    if (!isStyle || !hasValue || shouldHide || isVariable && !variableExists) {
      return false;
    }
    if (hasInitial) {
      return !(0,_utils_is_equal__WEBPACK_IMPORTED_MODULE_6__.isEqual)(value, propType.initial_value);
    }
    return !isRequired;
  }
  const visible = calculateVisibility();
  return {
    visible,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Clear', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.BrushBigIcon,
    onClick: () => resetValue()
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/action-icons.tsx":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/action-icons.tsx ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ActionIcons: function() { return /* binding */ ActionIcons; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const ActionIcons = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  display: "flex",
  gap: 0.5,
  alignItems: "center"
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/breakpoint-icon.tsx":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/breakpoint-icon.tsx ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BreakpointIcon: function() { return /* binding */ BreakpointIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-responsive */ "@elementor/editor-responsive");
/* harmony import */ var _elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




const SIZE = 'tiny';
const DEFAULT_BREAKPOINT = 'desktop';
const breakpointIconMap = {
  widescreen: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.WidescreenIcon,
  desktop: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.DesktopIcon,
  laptop: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.LaptopIcon,
  tablet_extra: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TabletLandscapeIcon,
  tablet: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TabletPortraitIcon,
  mobile_extra: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MobileLandscapeIcon,
  mobile: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.MobilePortraitIcon
};
const BreakpointIcon = ({
  breakpoint
}) => {
  const breakpoints = (0,_elementor_editor_responsive__WEBPACK_IMPORTED_MODULE_1__.useBreakpoints)();
  const currentBreakpoint = breakpoint || DEFAULT_BREAKPOINT;
  const IconComponent = breakpointIconMap[currentBreakpoint];
  if (!IconComponent) {
    return null;
  }
  const breakpointLabel = breakpoints.find(breakpointItem => breakpointItem.id === currentBreakpoint)?.label;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: breakpointLabel,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(IconComponent, {
    fontSize: SIZE,
    sx: {
      mt: '2px'
    }
  }));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/index.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/index.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ActionIcons: function() { return /* reexport safe */ _action_icons__WEBPACK_IMPORTED_MODULE_3__.ActionIcons; },
/* harmony export */   BreakpointIcon: function() { return /* reexport safe */ _breakpoint_icon__WEBPACK_IMPORTED_MODULE_0__.BreakpointIcon; },
/* harmony export */   LabelChip: function() { return /* reexport safe */ _label_chip__WEBPACK_IMPORTED_MODULE_1__.LabelChip; },
/* harmony export */   ValueComponent: function() { return /* reexport safe */ _value_component__WEBPACK_IMPORTED_MODULE_2__.ValueComponent; }
/* harmony export */ });
/* harmony import */ var _breakpoint_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./breakpoint-icon */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/breakpoint-icon.tsx");
/* harmony import */ var _label_chip__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./label-chip */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/label-chip.tsx");
/* harmony import */ var _value_component__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./value-component */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/value-component.tsx");
/* harmony import */ var _action_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./action-icons */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/action-icons.tsx");





/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/label-chip.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/label-chip.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LabelChip: function() { return /* binding */ LabelChip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../utils/get-styles-provider-color */ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts");






const SIZE = 'tiny';
const LabelChip = ({
  displayLabel,
  provider
}) => {
  const isBaseStyle = provider === _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.ELEMENTS_BASE_STYLES_PROVIDER_KEY;
  const chipIcon = isBaseStyle ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Inherited from base styles', 'elementor'),
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.InfoCircleIcon, {
    fontSize: SIZE
  })) : undefined;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Chip, {
    label: displayLabel,
    size: SIZE,
    color: (0,_utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_5__.getStylesProviderColorName)(provider),
    variant: "standard",
    state: "enabled",
    icon: chipIcon,
    sx: theme => ({
      lineHeight: 1,
      flexWrap: 'nowrap',
      alignItems: 'center',
      borderRadius: `${theme.shape.borderRadius * 0.75}px`,
      flexDirection: 'row-reverse',
      '.MuiChip-label': {
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        whiteSpace: 'nowrap'
      }
    })
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/value-component.tsx":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/value-component.tsx ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ValueComponent: function() { return /* binding */ ValueComponent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const ValueComponent = ({
  index,
  value
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    title: value,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "caption",
    color: "text.tertiary",
    sx: {
      mt: '1px',
      textDecoration: index === 0 ? 'none' : 'line-through',
      overflow: 'hidden',
      display: '-webkit-box',
      WebkitLineClamp: 1,
      WebkitBoxOrient: 'vertical',
      pl: 2.5,
      minWidth: 0,
      maxWidth: '100%'
    }
  }, value));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-indicator.tsx":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-indicator.tsx ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StylesInheritanceIndicator: function() { return /* binding */ StylesInheritanceIndicator; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_style_indicator__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/style-indicator */ "./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../contexts/styles-inheritance-context */ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx");
/* harmony import */ var _utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../utils/get-styles-provider-color */ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/utils.ts");
/* harmony import */ var _styles_inheritance_infotip__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./styles-inheritance-infotip */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-infotip.tsx");











const StylesInheritanceIndicator = ({
  customContext
}) => {
  const context = (0,_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.useBoundProp)();
  const {
    path,
    propType
  } = customContext || context;
  const inheritanceChain = (0,_contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_7__.useStylesInheritanceChain)(path);
  if (!path || !inheritanceChain.length) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Indicator, {
    inheritanceChain: inheritanceChain,
    path: path,
    propType: propType
  });
};
const Indicator = ({
  inheritanceChain,
  path,
  propType,
  isDisabled
}) => {
  const {
    id: currentStyleId,
    provider: currentStyleProvider,
    meta: currentStyleMeta
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_6__.useStyle)();
  const currentItem = currentStyleId ? (0,_utils__WEBPACK_IMPORTED_MODULE_9__.getValueFromInheritanceChain)(inheritanceChain, currentStyleId, currentStyleMeta) : null;
  const hasValue = !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.isEmpty)(currentItem?.value);
  const [actualStyle] = inheritanceChain;
  if (actualStyle.provider === _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.ELEMENTS_BASE_STYLES_PROVIDER_KEY) {
    return null;
  }
  const isFinalValue = currentItem === actualStyle;
  const label = getLabel({
    isFinalValue,
    hasValue
  });
  const styleIndicatorProps = {
    getColor: isFinalValue && currentStyleProvider ? (0,_utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_8__.getStylesProviderThemeColor)(currentStyleProvider.getKey()) : undefined,
    isOverridden: hasValue && !isFinalValue ? true : undefined
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styles_inheritance_infotip__WEBPACK_IMPORTED_MODULE_10__.StylesInheritanceInfotip, {
    inheritanceChain: inheritanceChain,
    path: path,
    propType: propType,
    label: label,
    isDisabled: isDisabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_style_indicator__WEBPACK_IMPORTED_MODULE_5__.StyleIndicator, styleIndicatorProps));
};
const getLabel = ({
  isFinalValue,
  hasValue
}) => {
  if (isFinalValue) {
    return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('This is the final value', 'elementor');
  }
  if (hasValue) {
    return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('This value is overridden by another style', 'elementor');
  }
  return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('This has value from another style', 'elementor');
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-infotip.tsx":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-infotip.tsx ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StylesInheritanceInfotip: function() { return /* binding */ StylesInheritanceInfotip; },
/* harmony export */   calculatePopoverOffset: function() { return /* binding */ calculatePopoverOffset; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _hooks_use_normalized_inheritance_chain_items__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-normalized-inheritance-chain-items */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/hooks/use-normalized-inheritance-chain-items.tsx");
/* harmony import */ var _infotip__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./infotip */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/infotip/index.ts");








const SECTION_PADDING_INLINE = 32;
const INFOTIP_MAX_WIDTH = 496;
const calculatePopoverOffset = (triggerRect, cardWidth, isSiteRtl) => {
  if (!triggerRect) {
    return 0;
  }
  const triggerWidth = triggerRect.width;
  return isSiteRtl ? triggerWidth - cardWidth : -(cardWidth / 2) + triggerWidth / 2;
};
const StylesInheritanceInfotip = ({
  inheritanceChain,
  propType,
  path,
  label,
  children,
  isDisabled
}) => {
  const [showInfotip, setShowInfotip] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const triggerRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const toggleInfotip = () => {
    if (isDisabled) {
      return;
    }
    setShowInfotip(prev => !prev);
  };
  const closeInfotip = () => {
    if (isDisabled) {
      return;
    }
    setShowInfotip(false);
  };
  const key = path.join('.');
  const sectionWidth = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.useSectionWidth)();
  const resolve = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    return (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createPropsResolver)({
      transformers: _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.stylesInheritanceTransformersRegistry,
      schema: {
        [key]: propType
      }
    });
  }, [key, propType]);
  const items = (0,_hooks_use_normalized_inheritance_chain_items__WEBPACK_IMPORTED_MODULE_5__.useNormalizedInheritanceChainItems)(inheritanceChain, key, resolve);
  const infotipContent = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ClickAwayListener, {
    onClickAway: closeInfotip
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Card, {
    elevation: 0,
    sx: {
      width: `${sectionWidth - SECTION_PADDING_INLINE}px`,
      maxWidth: INFOTIP_MAX_WIDTH,
      maxHeight: 268,
      overflowX: 'hidden',
      display: 'flex',
      flexDirection: 'column'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    sx: {
      position: 'sticky',
      top: 0,
      zIndex: 1,
      backgroundColor: 'background.paper'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.PopoverHeader, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Style origin', 'elementor'),
    onClose: closeInfotip
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.CardContent, {
    sx: {
      display: 'flex',
      flexDirection: 'column',
      p: 0,
      flex: 1,
      overflow: 'auto',
      '&:last-child': {
        pb: 0
      }
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    gap: 1.5,
    sx: {
      pl: 2,
      pr: 1,
      pt: 1.5,
      pb: 1.5
    },
    role: "list"
  }, items.map((item, index) => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
      key: item.id,
      display: "flex",
      gap: 0.5,
      role: "listitem"
      // translators: %s is the display label of the inheritance item
      ,
      "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Inheritance item: %s', 'elementor').replace('%s', item.displayLabel)
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
      display: "flex",
      gap: 0.5,
      sx: {
        flexWrap: 'wrap',
        width: '100%',
        alignItems: 'flex-start'
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_infotip__WEBPACK_IMPORTED_MODULE_6__.BreakpointIcon, {
      breakpoint: item.breakpoint
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_infotip__WEBPACK_IMPORTED_MODULE_6__.LabelChip, {
      displayLabel: item.displayLabel,
      provider: item.provider
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_infotip__WEBPACK_IMPORTED_MODULE_6__.ValueComponent, {
      index: index,
      value: item.value
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_infotip__WEBPACK_IMPORTED_MODULE_6__.ActionIcons, null));
  })))));
  if (isDisabled) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
      sx: {
        display: 'inline-flex'
      }
    }, children);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
    ref: triggerRef,
    sx: {
      display: 'inline-flex'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TooltipOrInfotip, {
    showInfotip: showInfotip,
    onClose: closeInfotip,
    infotipContent: infotipContent,
    isDisabled: isDisabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.IconButton, {
    onClick: toggleInfotip,
    "aria-label": label,
    sx: {
      my: '-1px'
    },
    disabled: isDisabled
  }, children)));
};
function TooltipOrInfotip({
  children,
  showInfotip,
  onClose,
  infotipContent,
  isDisabled
}) {
  if (isDisabled) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Box, {
      sx: {
        display: 'inline-flex'
      }
    }, children);
  }
  if (showInfotip) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Backdrop, {
      open: showInfotip,
      onClick: onClose,
      sx: {
        backgroundColor: 'transparent',
        zIndex: theme => theme.zIndex.modal - 1
      }
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Infotip, {
      placement: "top-end",
      content: infotipContent,
      open: showInfotip,
      onClose: onClose,
      disableHoverListener: true
    }, children));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Style origin', 'elementor'),
    placement: "top"
  }, children);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-section-indicators.tsx":
/*!*********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-section-indicators.tsx ***!
  \*********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StylesInheritanceSectionIndicators: function() { return /* binding */ StylesInheritanceSectionIndicators; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_custom_css_indicator__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/custom-css-indicator */ "./packages/packages/core/editor-editing-panel/src/components/custom-css-indicator.tsx");
/* harmony import */ var _components_style_indicator__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/style-indicator */ "./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../contexts/styles-inheritance-context */ "./packages/packages/core/editor-editing-panel/src/contexts/styles-inheritance-context.tsx");
/* harmony import */ var _utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../utils/get-styles-provider-color */ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts");









const StylesInheritanceSectionIndicators = ({
  fields
}) => {
  const {
    id,
    meta,
    provider
  } = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_6__.useStyle)();
  const snapshot = (0,_contexts_styles_inheritance_context__WEBPACK_IMPORTED_MODULE_7__.useStylesInheritanceSnapshot)();
  if (fields.includes('custom_css')) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_custom_css_indicator__WEBPACK_IMPORTED_MODULE_4__.CustomCssIndicator, null);
  }
  const snapshotFields = Object.fromEntries(Object.entries(snapshot ?? {}).filter(([key]) => fields.includes(key)));
  const {
    hasValues,
    hasOverrides
  } = getIndicators(snapshotFields, id ?? '', meta);
  if (!hasValues && !hasOverrides) {
    return null;
  }
  const hasValueLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Has effective styles', 'elementor');
  const hasOverridesLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Has overridden styles', 'elementor');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Has styles', 'elementor'),
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    sx: {
      '& > *': {
        marginInlineStart: -0.25
      }
    },
    role: "list"
  }, hasValues && provider && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_style_indicator__WEBPACK_IMPORTED_MODULE_5__.StyleIndicator, {
    getColor: (0,_utils_get_styles_provider_color__WEBPACK_IMPORTED_MODULE_8__.getStylesProviderThemeColor)(provider.getKey()),
    "data-variant": (0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_1__.isElementsStylesProvider)(provider.getKey()) ? 'local' : 'global',
    role: "listitem",
    "aria-label": hasValueLabel
  }), hasOverrides && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_style_indicator__WEBPACK_IMPORTED_MODULE_5__.StyleIndicator, {
    isOverridden: true,
    "data-variant": "overridden",
    role: "listitem",
    "aria-label": hasOverridesLabel
  })));
};
function getIndicators(snapshotFields, styleId, meta) {
  let hasValues = false;
  let hasOverrides = false;
  Object.values(snapshotFields).forEach(inheritanceChain => {
    const currentStyle = getCurrentStyleFromChain(inheritanceChain, styleId, meta);
    if (!currentStyle) {
      return;
    }
    const [actualStyle] = inheritanceChain;
    if (currentStyle === actualStyle) {
      hasValues = true;
    } else {
      hasOverrides = true;
    }
  });
  return {
    hasValues,
    hasOverrides
  };
}
function getCurrentStyleFromChain(chain, styleId, meta) {
  return chain.find(({
    style: {
      id
    },
    variant: {
      meta: {
        breakpoint,
        state
      }
    }
  }) => id === styleId && breakpoint === meta.breakpoint && state === meta.state);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/ui-providers.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   UiProviders: function() { return /* binding */ UiProviders; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _hooks_use_direction__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../hooks/use-direction */ "./packages/packages/core/editor-editing-panel/src/hooks/use-direction.ts");



const UiProviders = ({
  children
}) => {
  const {
    isSiteRtl
  } = (0,_hooks_use_direction__WEBPACK_IMPORTED_MODULE_2__.useDirection)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.DirectionProvider, {
    rtl: isSiteRtl
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ThemeProvider, null, children));
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/consts.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/consts.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   excludePropTypeTransformers: function() { return /* binding */ excludePropTypeTransformers; }
/* harmony export */ });
// the following prop types' style transformers would be ignored to provide alternative transformers for the styles inheritance popover
const excludePropTypeTransformers = new Set(['background-color-overlay', 'background-image-overlay', 'background-gradient-overlay', 'gradient-color-stop', 'color-stop', 'background-image-position-offset', 'background-image-size-scale', 'image-src', 'image', 'background-overlay']);

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/create-snapshots-manager.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/create-snapshots-manager.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createSnapshotsManager: function() { return /* binding */ createSnapshotsManager; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-variables */ "@elementor/editor-variables");
/* harmony import */ var _elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/utils.ts");



function createSnapshotsManager(getStylesByMeta, breakpointsRoot) {
  const breakpointsInheritancePaths = makeBreakpointsInheritancePaths(breakpointsRoot);
  const allBreakpointStatesSnapshots = {};
  const buildMissingSnapshotsForBreakpoint = (currentBreakpointId, parentBreakpoint, state) => {
    const currentBreakpointKey = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getBreakpointKey)(currentBreakpointId);
    const stateKey = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getStateKey)(state);
    if (!allBreakpointStatesSnapshots[currentBreakpointKey]) {
      allBreakpointStatesSnapshots[currentBreakpointKey] = {
        [_utils__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_STATE]: buildStateSnapshotSlot(getStylesByMeta({
          breakpoint: currentBreakpointId,
          state: null
        }), parentBreakpoint, {}, null)
      };
    }
    if (state && !allBreakpointStatesSnapshots[currentBreakpointKey]?.[stateKey]) {
      allBreakpointStatesSnapshots[currentBreakpointKey][stateKey] = buildStateSnapshotSlot(getStylesByMeta({
        breakpoint: currentBreakpointId,
        state
      }), parentBreakpoint, allBreakpointStatesSnapshots[currentBreakpointKey] ?? {}, state);
    }
  };
  return meta => {
    const {
      breakpoint,
      state
    } = meta;
    const stateKey = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getStateKey)(state);
    const breakpointKey = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getBreakpointKey)(breakpoint);
    if (allBreakpointStatesSnapshots[breakpointKey]?.[stateKey]) {
      // snapshot was already made for this breakpoint+state
      return allBreakpointStatesSnapshots[breakpointKey]?.[stateKey]?.snapshot;
    }
    const breakpointsChain = [...breakpointsInheritancePaths[breakpointKey], breakpoint];
    breakpointsChain.forEach((breakpointId, index) => {
      const parentBreakpointId = index > 0 ? breakpointsChain[index - 1] : null;
      buildMissingSnapshotsForBreakpoint(breakpointId, parentBreakpointId ? allBreakpointStatesSnapshots[parentBreakpointId] : undefined, state);
    });
    return allBreakpointStatesSnapshots[breakpointKey]?.[stateKey]?.snapshot;
  };
}

/**
 * builds a mapping of each breakpoint to its inheritance chain, e.g. -
 * 	desktop: [],
 * 	tablet: [ 'desktop' ],
 * 	mobile: [ 'desktop', 'tablet' ]
 * @param root
 */
function makeBreakpointsInheritancePaths(root) {
  const breakpoints = {};
  const traverse = (node, parent) => {
    const {
      id,
      children
    } = node;
    breakpoints[id] = parent ? [...parent] : [];
    children?.forEach(child => {
      traverse(child, [...(breakpoints[id] ?? []), id]);
    });
  };
  traverse(root);
  return breakpoints;
}

// creates a snapshot slot for a specific breakpoint and state
function buildStateSnapshotSlot(styles, parentBreakpoint, currentBreakpoint, state) {
  const initialSlot = buildInitialSnapshotFromStyles(styles);
  if (!state) {
    return {
      snapshot: mergeSnapshots([initialSlot.snapshot, parentBreakpoint?.[_utils__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_STATE]?.snapshot]),
      stateSpecificSnapshot: undefined
    };
  }
  return {
    snapshot: mergeSnapshots([initialSlot.snapshot, parentBreakpoint?.[state]?.stateSpecificSnapshot, currentBreakpoint[_utils__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_STATE]?.snapshot]),
    stateSpecificSnapshot: mergeSnapshots([initialSlot.stateSpecificSnapshot, parentBreakpoint?.[state]?.stateSpecificSnapshot])
  };
}

// creates an initial snapshot based on the passed style variants only
function buildInitialSnapshotFromStyles(styles) {
  const snapshot = {};
  styles.forEach(styleData => {
    const {
      variant: {
        props
      }
    } = styleData;
    Object.entries(props).forEach(([key, value]) => {
      const filteredValue = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.filterEmptyValues)(value);
      const filteredVariableValue = filteredValue?.$$type?.includes('variable') && !(0,_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_1__.hasVariable)(filteredValue?.value) ? null : filteredValue;
      if (filteredVariableValue === null) {
        return;
      }
      if (!snapshot[key]) {
        snapshot[key] = [];
      }
      const snapshotPropValue = {
        ...styleData,
        value: filteredVariableValue
      };
      snapshot[key].push(snapshotPropValue);
    });
  });
  return {
    snapshot,
    stateSpecificSnapshot: snapshot
  };
}

// merge previous snapshot into the current one - first value of each prop is the strongest
function mergeSnapshots(snapshots) {
  const snapshot = {};
  snapshots.filter(Boolean).forEach(currentSnapshot => Object.entries(currentSnapshot).forEach(([key, values]) => {
    if (!snapshot[key]) {
      snapshot[key] = [];
    }

    // concatenate the previous snapshot's prop values to the current ones
    snapshot[key] = snapshot[key].concat(values);
  }));
  return snapshot;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/create-styles-inheritance.ts":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/create-styles-inheritance.ts ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createStylesInheritance: function() { return /* binding */ createStylesInheritance; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _create_snapshots_manager__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-snapshots-manager */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/create-snapshots-manager.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/utils.ts");




function createStylesInheritance(styleDefs, breakpointsRoot) {
  const styleVariantsByMeta = buildStyleVariantsByMetaMapping(styleDefs);
  const getStyles = ({
    breakpoint,
    state
  }) => styleVariantsByMeta?.[(0,_utils__WEBPACK_IMPORTED_MODULE_3__.getBreakpointKey)(breakpoint)]?.[(0,_utils__WEBPACK_IMPORTED_MODULE_3__.getStateKey)(state)] ?? [];
  return {
    getSnapshot: (0,_create_snapshots_manager__WEBPACK_IMPORTED_MODULE_2__.createSnapshotsManager)(getStyles, breakpointsRoot),
    getInheritanceChain: (snapshot, path, topLevelPropType) => {
      const [field, ...nextFields] = path;
      let inheritanceChain = snapshot[field] ?? [];
      if (nextFields.length > 0) {
        const filterPropType = getFilterPropType(topLevelPropType, nextFields);
        inheritanceChain = inheritanceChain.map(({
          value: styleValue,
          ...rest
        }) => ({
          ...rest,
          value: getValueByPath(styleValue, nextFields, filterPropType)
        })).filter(({
          value: styleValue
        }) => !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isEmpty)(styleValue));
      }
      return inheritanceChain;
    }
  };
}
function buildStyleVariantsByMetaMapping(styleDefs) {
  const breakpointStateSlots = {};
  styleDefs.forEach(styleDef => {
    const provider = (0,_contexts_style_context__WEBPACK_IMPORTED_MODULE_1__.getProviderByStyleId)(styleDef.id)?.getKey() ?? null;

    // iterate over each style definition's variants and place them in the corresponding breakpoint's base or state styles
    styleDef.variants.forEach(variant => {
      const {
        meta
      } = variant;
      const {
        state,
        breakpoint
      } = meta;
      const breakpointKey = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.getBreakpointKey)(breakpoint);
      const stateKey = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.getStateKey)(state);
      if (!breakpointStateSlots[breakpointKey]) {
        breakpointStateSlots[breakpointKey] = {};
      }
      const breakpointNode = breakpointStateSlots[breakpointKey];
      if (!breakpointNode[stateKey]) {
        breakpointNode[stateKey] = [];
      }
      breakpointNode[stateKey].push({
        style: styleDef,
        variant,
        provider
      });
    });
  });
  return breakpointStateSlots;
}
function getValueByPath(value, path, filterPropType) {
  if (!value || typeof value !== 'object') {
    return null;
  }
  if (shouldUseOriginalValue(filterPropType, value)) {
    return value;
  }
  return path.reduce((currentScope, key) => {
    if (!currentScope) {
      return null;
    }
    if ((0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(currentScope)) {
      return currentScope.value?.[key] ?? null;
    }
    if (typeof currentScope === 'object') {
      return currentScope[key] ?? null;
    }
    return null;
  }, value);
}
function shouldUseOriginalValue(filterPropType, value) {
  return !!filterPropType && (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(value) && filterPropType.key !== value.$$type;
}
const getFilterPropType = (propType, path) => {
  if (!propType || propType.kind !== 'union') {
    return null;
  }
  return Object.values(propType.prop_types).find(type => {
    return !!path.reduce((currentScope, key) => {
      if (currentScope?.kind !== 'object') {
        return null;
      }
      const {
        shape
      } = currentScope;
      if (shape[key]) {
        return shape[key];
      }
      return null;
    }, type);
  }) ?? null;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/hooks/use-normalized-inheritance-chain-items.tsx":
/*!*****************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/hooks/use-normalized-inheritance-chain-items.tsx ***!
  \*****************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   normalizeInheritanceItem: function() { return /* binding */ normalizeInheritanceItem; },
/* harmony export */   useNormalizedInheritanceChainItems: function() { return /* binding */ useNormalizedInheritanceChainItems; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);





const MAXIMUM_ITEMS = 2;
const useNormalizedInheritanceChainItems = (inheritanceChain, bind, resolve) => {
  const [items, setItems] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    (async () => {
      const normalizedItems = await Promise.all(inheritanceChain.filter(({
        style
      }) => style).map((item, index) => normalizeInheritanceItem(item, index, bind, resolve)));
      const validItems = normalizedItems.map(item => ({
        ...item,
        displayLabel: _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_3__.ELEMENTS_BASE_STYLES_PROVIDER_KEY !== item.provider ? item.displayLabel : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Base', 'elementor')
      })).filter(item => !item.value || item.displayLabel !== '').slice(0, MAXIMUM_ITEMS);
      setItems(validItems);
    })();
  }, [inheritanceChain, bind, resolve]);
  return items;
};
const DEFAULT_BREAKPOINT = 'desktop';
const normalizeInheritanceItem = async (item, index, bind, resolve) => {
  const {
    variant: {
      meta: {
        state,
        breakpoint
      }
    },
    style: {
      label,
      id
    }
  } = item;
  const displayLabel = getDisplayLabel({
    label,
    state
  });
  return {
    id: id ? id + (state ?? '') : index,
    provider: item.provider || '',
    breakpoint: breakpoint ?? DEFAULT_BREAKPOINT,
    displayLabel,
    value: await getTransformedValue(item, bind, resolve)
  };
};
function getDisplayLabel({
  label,
  state
}) {
  if (!state) {
    return label;
  }
  if ((0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.isClassState)(state)) {
    return `${label}.${state}`;
  }
  if ((0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.isPseudoState)(state)) {
    return `${label}:${state}`;
  }
  throw new _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.UnknownStyleStateError({
    context: {
      state
    }
  });
}
const getTransformedValue = async (item, bind, resolve) => {
  try {
    const result = await resolve({
      props: {
        [bind]: item.value
      }
    });
    const value = result?.[bind] ?? result;
    if (/*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.isValidElement)(value)) {
      return value;
    }
    if (typeof value === 'object') {
      return JSON.stringify(value);
    }
    return String(value);
  } catch {
    return '';
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/init-styles-inheritance-transformers.ts":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/init-styles-inheritance-transformers.ts ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initStylesInheritanceTransformers: function() { return /* binding */ initStylesInheritanceTransformers; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./consts */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/consts.ts");
/* harmony import */ var _transformers_array_transformer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./transformers/array-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/array-transformer.tsx");
/* harmony import */ var _transformers_background_color_overlay_transformer__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./transformers/background-color-overlay-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-color-overlay-transformer.tsx");
/* harmony import */ var _transformers_background_gradient_overlay_transformer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./transformers/background-gradient-overlay-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-gradient-overlay-transformer.tsx");
/* harmony import */ var _transformers_background_image_overlay_transformer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./transformers/background-image-overlay-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-image-overlay-transformer.tsx");
/* harmony import */ var _transformers_box_shadow_transformer__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./transformers/box-shadow-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/box-shadow-transformer.tsx");
/* harmony import */ var _transformers_color_transformer__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./transformers/color-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/color-transformer.tsx");
/* harmony import */ var _transformers_repeater_to_items_transformer__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./transformers/repeater-to-items-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/repeater-to-items-transformer.tsx");









function initStylesInheritanceTransformers() {
  const originalStyleTransformers = _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.styleTransformersRegistry.all();
  Object.entries(originalStyleTransformers).forEach(([propType, transformer]) => {
    if (_consts__WEBPACK_IMPORTED_MODULE_1__.excludePropTypeTransformers.has(propType)) {
      return;
    }
    _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register(propType, transformer);
  });
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.registerFallback((0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)(value => {
    return value;
  }));
  registerCustomTransformers(originalStyleTransformers);
}
function registerCustomTransformers(originalStyleTransformers) {
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('color', _transformers_color_transformer__WEBPACK_IMPORTED_MODULE_7__.colorTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('background-color-overlay', _transformers_background_color_overlay_transformer__WEBPACK_IMPORTED_MODULE_3__.backgroundColorOverlayTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('background-gradient-overlay', _transformers_background_gradient_overlay_transformer__WEBPACK_IMPORTED_MODULE_4__.backgroundGradientOverlayTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('background-image-overlay', _transformers_background_image_overlay_transformer__WEBPACK_IMPORTED_MODULE_5__.backgroundImageOverlayTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('shadow', _transformers_box_shadow_transformer__WEBPACK_IMPORTED_MODULE_6__.boxShadowTransformer);
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('filter', (0,_transformers_repeater_to_items_transformer__WEBPACK_IMPORTED_MODULE_8__.createRepeaterToItemsTransformer)(originalStyleTransformers.filter));
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('backdrop-filter', (0,_transformers_repeater_to_items_transformer__WEBPACK_IMPORTED_MODULE_8__.createRepeaterToItemsTransformer)(originalStyleTransformers['backdrop-filter']));
  _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register('transition', (0,_transformers_repeater_to_items_transformer__WEBPACK_IMPORTED_MODULE_8__.createRepeaterToItemsTransformer)(originalStyleTransformers.transition));
  ['background-overlay', 'box-shadow', 'transform-functions'].forEach(propType => _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.stylesInheritanceTransformersRegistry.register(propType, _transformers_array_transformer__WEBPACK_IMPORTED_MODULE_2__.arrayTransformer));
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/init.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/init.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _field_indicators_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../field-indicators-registry */ "./packages/packages/core/editor-editing-panel/src/field-indicators-registry.ts");
/* harmony import */ var _components_styles_inheritance_indicator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/styles-inheritance-indicator */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/components/styles-inheritance-indicator.tsx");
/* harmony import */ var _init_styles_inheritance_transformers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./init-styles-inheritance-transformers */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/init-styles-inheritance-transformers.ts");



const init = () => {
  (0,_init_styles_inheritance_transformers__WEBPACK_IMPORTED_MODULE_2__.initStylesInheritanceTransformers)();
  (0,_field_indicators_registry__WEBPACK_IMPORTED_MODULE_0__.registerFieldIndicator)({
    fieldType: _field_indicators_registry__WEBPACK_IMPORTED_MODULE_0__.FIELD_TYPE.STYLES,
    id: 'styles-inheritance',
    priority: 1,
    indicator: _components_styles_inheritance_indicator__WEBPACK_IMPORTED_MODULE_1__.StylesInheritanceIndicator
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/array-transformer.tsx":
/*!***************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/array-transformer.tsx ***!
  \***************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   arrayTransformer: function() { return /* binding */ arrayTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);


const arrayTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(values => {
  if (!values || values.length === 0) {
    return null;
  }
  const allStrings = values.every(item => typeof item === 'string' || typeof item === 'number');
  if (allStrings) {
    return values.join(' ');
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, values.map((item, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: index
  }, index > 0 && ' ', item)));
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-color-overlay-transformer.tsx":
/*!**********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-color-overlay-transformer.tsx ***!
  \**********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StyledUnstableColorIndicator: function() { return /* binding */ StyledUnstableColorIndicator; },
/* harmony export */   backgroundColorOverlayTransformer: function() { return /* binding */ backgroundColorOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);



const backgroundColorOverlayTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
  direction: "row",
  gap: 1,
  alignItems: "center"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ItemLabelColor, {
  value: value
})));
const ItemLabelColor = ({
  value: {
    color
  }
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, color);
};
const StyledUnstableColorIndicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.UnstableColorIndicator)(({
  theme
}) => ({
  width: '1em',
  height: '1em',
  borderRadius: `${theme.shape.borderRadius / 2}px`,
  outline: `1px solid ${theme.palette.action.disabled}`,
  flexShrink: 0
}));

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-gradient-overlay-transformer.tsx":
/*!*************************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-gradient-overlay-transformer.tsx ***!
  \*************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundGradientOverlayTransformer: function() { return /* binding */ backgroundGradientOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _background_color_overlay_transformer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./background-color-overlay-transformer */ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-color-overlay-transformer.tsx");





const backgroundGradientOverlayTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
  direction: "row",
  gap: 1,
  alignItems: "center"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ItemIconGradient, {
  value: value
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ItemLabelGradient, {
  value: value
})));
const ItemIconGradient = ({
  value
}) => {
  const gradient = getGradientValue(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_background_color_overlay_transformer__WEBPACK_IMPORTED_MODULE_4__.StyledUnstableColorIndicator, {
    size: "inherit",
    component: "span",
    value: gradient
  });
};
const ItemLabelGradient = ({
  value
}) => {
  if (value.type === 'linear') {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Linear gradient', 'elementor'));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Radial gradient', 'elementor'));
};
const getGradientValue = gradient => {
  const stops = gradient.stops?.map(({
    color,
    offset
  }) => `${color} ${offset ?? 0}%`)?.join(',');
  if (gradient.type === 'linear') {
    return `linear-gradient(${gradient.angle}deg, ${stops})`;
  }
  return `radial-gradient(circle at ${gradient.positions}, ${stops})`;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-image-overlay-transformer.tsx":
/*!**********************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/background-image-overlay-transformer.tsx ***!
  \**********************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundImageOverlayTransformer: function() { return /* binding */ backgroundImageOverlayTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/wp-media */ "@elementor/wp-media");
/* harmony import */ var _elementor_wp_media__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_wp_media__WEBPACK_IMPORTED_MODULE_4__);





const backgroundImageOverlayTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
  direction: "row",
  gap: 1,
  alignItems: "center"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ItemIconImage, {
  value: value
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ItemLabelImage, {
  value: value
})));
const ItemIconImage = ({
  value
}) => {
  const {
    imageUrl
  } = useImage(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.CardMedia, {
    image: imageUrl,
    sx: theme => ({
      height: '1em',
      width: '1em',
      borderRadius: `${theme.shape.borderRadius / 2}px`,
      outline: `1px solid ${theme.palette.action.disabled}`,
      flexShrink: 0
    })
  });
};
const ItemLabelImage = ({
  value
}) => {
  const {
    imageTitle
  } = useImage(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.EllipsisWithTooltip, {
    title: imageTitle
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, imageTitle));
};
const useImage = image => {
  let imageTitle,
    imageUrl = null;
  const imageSrc = image?.image.src;
  const {
    data: attachment
  } = (0,_elementor_wp_media__WEBPACK_IMPORTED_MODULE_4__.useWpMediaAttachment)(imageSrc.id || null);
  if (imageSrc.id) {
    const imageFileTypeExtension = getFileExtensionFromFilename(attachment?.filename);
    imageTitle = `${attachment?.title}${imageFileTypeExtension}` || null;
    imageUrl = attachment?.url || null;
  } else if (imageSrc.url) {
    imageUrl = imageSrc.url;
    imageTitle = imageUrl?.substring(imageUrl.lastIndexOf('/') + 1) || null;
  }
  return {
    imageTitle,
    imageUrl
  };
};
const getFileExtensionFromFilename = filename => {
  if (!filename) {
    return '';
  }

  // get the substring after the last . in the filename
  const extension = filename.substring(filename.lastIndexOf('.') + 1);
  return `.${extension}`;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/box-shadow-transformer.tsx":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/box-shadow-transformer.tsx ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   boxShadowTransformer: function() { return /* binding */ boxShadowTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);


const boxShadowTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => {
  if (!value) {
    return null;
  }
  const {
    color,
    hOffset,
    vOffset,
    blur,
    spread,
    position
  } = value;
  const colorValue = color || '#000000';
  const sizes = [hOffset || '0px', vOffset || '0px', blur || '10px', spread || '0px'].join(' ');
  const positionValue = position || 'outset';
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, colorValue, " ", positionValue, ", ", sizes);
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/color-transformer.tsx":
/*!***************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/color-transformer.tsx ***!
  \***************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorTransformer: function() { return /* binding */ colorTransformer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);



function isValidCSSColor(value) {
  if (!value.trim()) {
    return false;
  }
  return CSS.supports('color', value.trim());
}
const StyledColorIndicator = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.UnstableColorIndicator)(({
  theme
}) => ({
  width: '1em',
  height: '1em',
  borderRadius: `${theme.shape.borderRadius / 2}px`,
  outline: `1px solid ${theme.palette.action.disabled}`,
  flexShrink: 0
}));
const colorTransformer = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_1__.createTransformer)(value => {
  if (!isValidCSSColor(value)) {
    return value;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    gap: 1,
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledColorIndicator, {
    size: "inherit",
    component: "span",
    value: value
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, value));
});

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/repeater-to-items-transformer.tsx":
/*!***************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/transformers/repeater-to-items-transformer.tsx ***!
  \***************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createRepeaterToItemsTransformer: function() { return /* binding */ createRepeaterToItemsTransformer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);

const createRepeaterToItemsTransformer = originalTransformer => {
  return (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createTransformer)((value, options) => {
    const stringResult = originalTransformer(value, options);
    if (!stringResult || typeof stringResult !== 'string') {
      return stringResult;
    }
    return stringResult;
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/styles-inheritance/utils.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/styles-inheritance/utils.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_STATE: function() { return /* binding */ DEFAULT_STATE; },
/* harmony export */   getBreakpointKey: function() { return /* binding */ getBreakpointKey; },
/* harmony export */   getStateKey: function() { return /* binding */ getStateKey; },
/* harmony export */   getValueFromInheritanceChain: function() { return /* binding */ getValueFromInheritanceChain; }
/* harmony export */ });
const DEFAULT_STATE = 'normal';
const DEFAULT_BREAKPOINT = 'desktop';
const getStateKey = state => state ?? DEFAULT_STATE;
const getBreakpointKey = breakpoint => breakpoint ?? DEFAULT_BREAKPOINT;
const getValueFromInheritanceChain = (inheritanceChain, styleId, meta) => inheritanceChain.find(({
  style,
  variant: {
    meta: {
      breakpoint,
      state
    }
  }
}) => style.id === styleId && breakpoint === meta.breakpoint && state === meta.state);

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/sync/is-atomic-widget-selected.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/sync/is-atomic-widget-selected.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isAtomicWidgetSelected: function() { return /* binding */ isAtomicWidgetSelected; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

const isAtomicWidgetSelected = () => {
  const selectedElements = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getSelectedElements)();
  const widgetCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)();
  if (selectedElements.length !== 1) {
    return false;
  }

  // Check if the selected element has atomic controls, meaning it's a V2 element.
  return !!widgetCache?.[selectedElements[0].type]?.atomic_controls;
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/utils/can-element-have-children.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/utils/can-element-have-children.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canElementHaveChildren: function() { return /* binding */ canElementHaveChildren; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);

const canElementHaveChildren = elementId => {
  const container = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  if (!container) {
    return false;
  }
  return container.model.get('elType') !== 'widget';
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/utils/get-recently-used-styles.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/utils/get-recently-used-styles.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getRecentlyUsedList: function() { return /* binding */ getRecentlyUsedList; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__);



const getRecentlyUsedList = async elementId => {
  if (!elementId) {
    return [];
  }
  const resolver = (0,_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.createPropsResolver)({
    transformers: _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.styleTransformersRegistry,
    schema: (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getStylesSchema)()
  });
  const styles = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_1__.getElementStyles)(elementId) ?? {};
  const styleKeys = Object.keys(styles ?? {});
  const variants = styleKeys.map(key => styles?.[key]?.variants ?? []);
  const resolved = await Promise.all(variants.flat().map(async variant => {
    const result = await resolver({
      props: variant.props ?? {},
      schema: (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_2__.getStylesSchema)()
    });
    return Object.entries(result).filter(([, value]) => value !== null).map(([key]) => key);
  }));
  const propSet = new Set(resolved.flat());
  return Array.from(propSet);
};

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/utils/get-styles-provider-color.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getStylesProviderColorName: function() { return /* binding */ getStylesProviderColorName; },
/* harmony export */   getStylesProviderThemeColor: function() { return /* binding */ getStylesProviderThemeColor; },
/* harmony export */   getTempStylesProviderThemeColor: function() { return /* binding */ getTempStylesProviderThemeColor; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _provider_colors_registry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../provider-colors-registry */ "./packages/packages/core/editor-editing-panel/src/provider-colors-registry.ts");


const getStylesProviderColorName = provider => {
  if (!provider || provider === _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.ELEMENTS_BASE_STYLES_PROVIDER_KEY) {
    return 'default';
  }
  if ((0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.isElementsStylesProvider)(provider)) {
    return 'accent';
  }
  return (0,_provider_colors_registry__WEBPACK_IMPORTED_MODULE_1__.getStyleProviderColors)(provider).name;
};
const getStylesProviderThemeColor = provider => {
  if (!provider || provider === _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.ELEMENTS_BASE_STYLES_PROVIDER_KEY) {
    return null;
  }
  if ((0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.isElementsStylesProvider)(provider)) {
    return theme => theme.palette.accent.main;
  }
  return (0,_provider_colors_registry__WEBPACK_IMPORTED_MODULE_1__.getStyleProviderColors)(provider).getThemeColor;
};
function getTempStylesProviderThemeColor(provider) {
  if ((0,_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.isElementsStylesProvider)(provider)) {
    return theme => theme.palette.primary.main;
  }
  return getStylesProviderThemeColor(provider);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/utils/is-equal.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/utils/is-equal.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isEqual: function() { return /* binding */ isEqual; }
/* harmony export */ });
function isEqual(a, b) {
  if (a === b) {
    return true;
  }
  if (a === null || b === null) {
    return false;
  }
  if (typeof a !== typeof b) {
    return false;
  }
  if (Array.isArray(a) && Array.isArray(b)) {
    if (a.length !== b.length) {
      return false;
    }
    for (let i = 0; i < a.length; i++) {
      if (!isEqual(a[i], b[i])) {
        return false;
      }
    }
    return true;
  }
  if (typeof a === 'object' && typeof b === 'object') {
    const objA = a;
    const objB = b;
    const keysA = Object.keys(objA);
    const keysB = Object.keys(objB);
    if (keysA.length !== keysB.length) {
      return false;
    }
    for (const key of keysA) {
      if (!(key in objB)) {
        return false;
      }
      if (!isEqual(objA[key], objB[key])) {
        return false;
      }
    }
    return true;
  }
  return false;
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/utils/prop-dependency-utils.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/utils/prop-dependency-utils.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extractDependencyEffect: function() { return /* binding */ extractDependencyEffect; },
/* harmony export */   extractOrderedDependencies: function() { return /* binding */ extractOrderedDependencies; },
/* harmony export */   getElementSettingsWithDefaults: function() { return /* binding */ getElementSettingsWithDefaults; },
/* harmony export */   getUpdatedValues: function() { return /* binding */ getUpdatedValues; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/session */ "@elementor/session");
/* harmony import */ var _elementor_session__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_session__WEBPACK_IMPORTED_MODULE_1__);


function getElementSettingsWithDefaults(propsSchema, elementSettings) {
  const elementSettingsWithDefaults = {
    ...elementSettings
  };
  Object.keys(propsSchema).forEach(key => {
    if (elementSettingsWithDefaults[key] === null && propsSchema[key].default !== null) {
      elementSettingsWithDefaults[key] = propsSchema[key].default;
    }
  });
  return elementSettingsWithDefaults;
}
function extractDependencyEffect(bind, propsSchema, settings) {
  const settingsWithDefaults = getElementSettingsWithDefaults(propsSchema, settings);
  const propType = propsSchema[bind];
  const depCheck = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isDependencyMet)(propType?.dependencies, settingsWithDefaults);
  const failingTerm = !depCheck.isMet ? depCheck.failingDependencies[0] : undefined;
  const isHidden = !!failingTerm && !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isDependency)(failingTerm) && failingTerm?.effect === 'hide';
  return {
    isHidden,
    isDisabled: prop => !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isDependencyMet)(prop?.dependencies, settingsWithDefaults).isMet
  };
}
function extractOrderedDependencies(dependenciesPerTargetMapping) {
  return Object.values(dependenciesPerTargetMapping).flat().filter((dependent, index, self) => self.indexOf(dependent) === index);
}
function getUpdatedValues(values, dependencies, propsSchema, elementValues, elementId) {
  if (!dependencies.length) {
    return values;
  }
  return dependencies.reduce((newValues, dependency) => {
    const path = dependency.split('.');
    const combinedValues = {
      ...elementValues,
      ...newValues
    };
    const propType = getPropType(propsSchema, combinedValues, path);
    if (!propType) {
      return newValues;
    }
    const testDependencies = {
      previousValues: (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isDependencyMet)(propType.dependencies, elementValues),
      newValues: (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isDependencyMet)(propType.dependencies, combinedValues)
    };
    if (!testDependencies.newValues.isMet) {
      const newValue = handleUnmetCondition({
        failingDependencies: testDependencies.newValues.failingDependencies,
        dependency,
        elementValues: combinedValues,
        defaultValue: propType.default,
        elementId
      });
      return {
        ...newValues,
        ...updateValue(path, newValue, combinedValues)
      };
    }
    if (!testDependencies.previousValues.isMet) {
      const savedValue = retrievePreviousValueFromStorage({
        path: dependency,
        elementId
      });
      const currentValue = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.extractValue)(path, combinedValues, [], {
        unwrapOverridableLeaf: false
      });
      removePreviousValueFromStorage({
        path: dependency,
        elementId
      });
      const restored = isCompatibleSavedValue(savedValue, currentValue) ? savedValue : propType.default;
      return {
        ...newValues,
        ...updateValue(path, restored, combinedValues)
      };
    }
    return newValues;
  }, {
    ...values
  });
}
function getPropType(schema, elementValues, path) {
  if (!path.length) {
    return null;
  }
  const [basePropKey, ...keys] = path;
  const baseProp = schema[basePropKey];
  if (!baseProp) {
    return null;
  }
  return keys.reduce((prop, key, index) => evaluatePropType({
    prop,
    key,
    index,
    path,
    elementValues,
    basePropKey
  }), baseProp);
}
function evaluatePropType(props) {
  const {
    prop
  } = props;
  if (!prop?.kind) {
    return null;
  }
  const {
    key,
    index,
    path,
    elementValues,
    basePropKey
  } = props;
  switch (prop.kind) {
    case 'union':
      const value = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.extractValue)(path.slice(0, index + 1), elementValues);
      const type = value?.$$type ?? null;
      return getPropType({
        [basePropKey]: prop.prop_types?.[type]
      }, elementValues, path.slice(0, index + 2));
    case 'array':
      return prop.item_prop_type;
    case 'object':
      return prop.shape[key];
  }
  return prop[key];
}
function updateValue(path, value, values) {
  const topPropKey = path[0];
  const root = {
    ...values
  };
  let carry = root;
  for (let index = 0; index < path.length; index++) {
    const key = path[index];
    const isLeaf = index === path.length - 1;
    if (isLeaf) {
      carry[key] = mergeLeafValue(carry[key], value);
      break;
    }
    const next = cloneDescent(carry[key]);
    if (!next) {
      break;
    }
    carry[key] = next.replacement;
    carry = next.descended;
  }
  return {
    [topPropKey]: root[topPropKey] ?? null
  };
}
function cloneDescent(child) {
  if (!child) {
    return null;
  }
  if ((0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(child)) {
    const origin = child.value.origin_value;
    if (!origin || !(0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(origin)) {
      return null;
    }
    const descended = {
      ...origin.value
    };
    const replacement = {
      ...child,
      value: {
        ...child.value,
        origin_value: {
          ...origin,
          value: descended
        }
      }
    };
    return {
      replacement,
      descended
    };
  }
  if ((0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(child)) {
    const descended = {
      ...child.value
    };
    const replacement = {
      ...child,
      value: descended
    };
    return {
      replacement,
      descended
    };
  }
  return null;
}
function isCompatibleSavedValue(saved, current) {
  if (!saved) {
    return false;
  }
  return (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(saved) === (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(current);
}
function mergeLeafValue(existing, incoming) {
  if (incoming === null) {
    return null;
  }
  if (incoming && (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(incoming)) {
    return incoming;
  }
  if (existing && (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(existing) && incoming) {
    return (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.rewrapOverridableValue)(existing, incoming);
  }
  return incoming;
}
function handleUnmetCondition(props) {
  const {
    failingDependencies,
    dependency,
    elementValues,
    defaultValue,
    elementId
  } = props;
  const termWithNewValue = failingDependencies.find(term => 'newValue' in term && !!term.newValue);
  const newValue = termWithNewValue?.newValue ?? null;
  const currentValue = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.extractValue)(dependency.split('.'), elementValues, [], {
    unwrapOverridableLeaf: false
  }) ?? defaultValue;
  savePreviousValueToStorage({
    path: dependency,
    elementId,
    value: currentValue
  });
  return newValue;
}
function savePreviousValueToStorage({
  path,
  elementId,
  value
}) {
  const prefix = `elementor/${elementId}`;
  const savedValue = retrievePreviousValueFromStorage({
    path,
    elementId
  });
  if (savedValue) {
    return;
  }
  const key = `${prefix}:${path}`;
  (0,_elementor_session__WEBPACK_IMPORTED_MODULE_1__.setSessionStorageItem)(key, value);
}
function retrievePreviousValueFromStorage({
  path,
  elementId
}) {
  const prefix = `elementor/${elementId}`;
  const key = `${prefix}:${path}`;
  return (0,_elementor_session__WEBPACK_IMPORTED_MODULE_1__.getSessionStorageItem)(key) ?? null;
}
function removePreviousValueFromStorage({
  path,
  elementId
}) {
  const prefix = `elementor/${elementId}`;
  const key = `${prefix}:${path}`;
  (0,_elementor_session__WEBPACK_IMPORTED_MODULE_1__.removeSessionStorageItem)(key);
}

/***/ }),

/***/ "./packages/packages/core/editor-editing-panel/src/utils/tracking/subscribe.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/utils/tracking/subscribe.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   trackStyles: function() { return /* binding */ trackStyles; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-styles-repository */ "@elementor/editor-styles-repository");
/* harmony import */ var _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__);

const trackStyles = (provider, event, data) => {
  const providerInstance = _elementor_editor_styles_repository__WEBPACK_IMPORTED_MODULE_0__.stylesRepository.getProviderByKey(provider);
  providerInstance?.actions.tracking?.({
    event,
    ...data
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

/***/ "@elementor/editor-variables":
/*!**************************************************!*\
  !*** external ["elementorV2","editorVariables"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorVariables"];

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
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-editing-panel/src/index.ts ***!
  \******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BaseControl: function() { return /* reexport safe */ _controls_registry_control__WEBPACK_IMPORTED_MODULE_16__.Control; },
/* harmony export */   ControlLabel: function() { return /* reexport safe */ _components_control_label__WEBPACK_IMPORTED_MODULE_1__.ControlLabel; },
/* harmony export */   ControlTypeContainer: function() { return /* reexport safe */ _controls_registry_control_type_container__WEBPACK_IMPORTED_MODULE_17__.ControlTypeContainer; },
/* harmony export */   CustomCssIndicator: function() { return /* reexport safe */ _components_custom_css_indicator__WEBPACK_IMPORTED_MODULE_3__.CustomCssIndicator; },
/* harmony export */   EditingPanelTabs: function() { return /* reexport safe */ _components_editing_panel_tabs__WEBPACK_IMPORTED_MODULE_5__.EditingPanelTabs; },
/* harmony export */   ElementProvider: function() { return /* reexport safe */ _contexts_element_context__WEBPACK_IMPORTED_MODULE_14__.ElementProvider; },
/* harmony export */   FIELD_TYPE: function() { return /* reexport safe */ _field_indicators_registry__WEBPACK_IMPORTED_MODULE_28__.FIELD_TYPE; },
/* harmony export */   HISTORY_DEBOUNCE_WAIT: function() { return /* reexport safe */ _hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_23__.HISTORY_DEBOUNCE_WAIT; },
/* harmony export */   SectionContent: function() { return /* reexport safe */ _components_section_content__WEBPACK_IMPORTED_MODULE_6__.SectionContent; },
/* harmony export */   SettingsControl: function() { return /* reexport safe */ _components_settings_control__WEBPACK_IMPORTED_MODULE_7__.SettingsControl; },
/* harmony export */   SettingsField: function() { return /* reexport safe */ _controls_registry_settings_field__WEBPACK_IMPORTED_MODULE_8__.SettingsField; },
/* harmony export */   StyleIndicator: function() { return /* reexport safe */ _components_style_indicator__WEBPACK_IMPORTED_MODULE_9__.StyleIndicator; },
/* harmony export */   StyleTabSection: function() { return /* reexport safe */ _components_style_tab_section__WEBPACK_IMPORTED_MODULE_12__.StyleTabSection; },
/* harmony export */   StylesProviderCannotUpdatePropsError: function() { return /* reexport safe */ _errors__WEBPACK_IMPORTED_MODULE_19__.StylesProviderCannotUpdatePropsError; },
/* harmony export */   controlsRegistry: function() { return /* reexport safe */ _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_18__.controlsRegistry; },
/* harmony export */   createTopLevelObjectType: function() { return /* reexport safe */ _controls_registry_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_20__.createTopLevelObjectType; },
/* harmony export */   doApplyClasses: function() { return /* reexport safe */ _apply_unapply_actions__WEBPACK_IMPORTED_MODULE_31__.doApplyClasses; },
/* harmony export */   doGetAppliedClasses: function() { return /* reexport safe */ _apply_unapply_actions__WEBPACK_IMPORTED_MODULE_31__.doGetAppliedClasses; },
/* harmony export */   doUnapplyClass: function() { return /* reexport safe */ _apply_unapply_actions__WEBPACK_IMPORTED_MODULE_31__.doUnapplyClass; },
/* harmony export */   extractDependencyEffect: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_34__.extractDependencyEffect; },
/* harmony export */   extractOrderedDependencies: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_34__.extractOrderedDependencies; },
/* harmony export */   getElementSettingsWithDefaults: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_34__.getElementSettingsWithDefaults; },
/* harmony export */   getFieldIndicators: function() { return /* reexport safe */ _field_indicators_registry__WEBPACK_IMPORTED_MODULE_28__.getFieldIndicators; },
/* harmony export */   getSubtitle: function() { return /* reexport safe */ _hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_23__.getSubtitle; },
/* harmony export */   getTitle: function() { return /* reexport safe */ _hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_23__.getTitle; },
/* harmony export */   getUpdatedValues: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_34__.getUpdatedValues; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_25__.init; },
/* harmony export */   injectIntoClassSelectorActions: function() { return /* reexport safe */ _components_css_classes_css_class_selector__WEBPACK_IMPORTED_MODULE_2__.injectIntoClassSelectorActions; },
/* harmony export */   injectIntoCssClassConvert: function() { return /* reexport safe */ _components_css_classes_css_class_convert_local__WEBPACK_IMPORTED_MODULE_0__.injectIntoCssClassConvert; },
/* harmony export */   injectIntoGridFields: function() { return /* reexport safe */ _components_style_sections_layout_section_layout_section__WEBPACK_IMPORTED_MODULE_11__.injectIntoGridFields; },
/* harmony export */   injectIntoPanelHeaderTop: function() { return /* reexport safe */ _components_editing_panel__WEBPACK_IMPORTED_MODULE_4__.injectIntoPanelHeaderTop; },
/* harmony export */   injectIntoStyleTab: function() { return /* reexport safe */ _components_style_tab__WEBPACK_IMPORTED_MODULE_10__.injectIntoStyleTab; },
/* harmony export */   isDynamicPropValue: function() { return /* reexport safe */ _dynamics_utils__WEBPACK_IMPORTED_MODULE_33__.isDynamicPropValue; },
/* harmony export */   registerEditingPanelReplacement: function() { return /* reexport safe */ _editing_panel_replacement_registry__WEBPACK_IMPORTED_MODULE_29__.registerEditingPanelReplacement; },
/* harmony export */   registerElementPanelDefaults: function() { return /* reexport safe */ _hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_30__.registerElementPanelDefaults; },
/* harmony export */   registerFieldIndicator: function() { return /* reexport safe */ _field_indicators_registry__WEBPACK_IMPORTED_MODULE_28__.registerFieldIndicator; },
/* harmony export */   registerStyleProviderToColors: function() { return /* reexport safe */ _provider_colors_registry__WEBPACK_IMPORTED_MODULE_27__.registerStyleProviderToColors; },
/* harmony export */   setLicenseConfig: function() { return /* reexport safe */ _hooks_use_license_config__WEBPACK_IMPORTED_MODULE_32__.setLicenseConfig; },
/* harmony export */   useClassesProp: function() { return /* reexport safe */ _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_13__.useClassesProp; },
/* harmony export */   useCustomCss: function() { return /* reexport safe */ _hooks_use_custom_css__WEBPACK_IMPORTED_MODULE_21__.useCustomCss; },
/* harmony export */   useElement: function() { return /* reexport safe */ _contexts_element_context__WEBPACK_IMPORTED_MODULE_14__.useElement; },
/* harmony export */   usePanelActions: function() { return /* reexport safe */ _panel__WEBPACK_IMPORTED_MODULE_26__.usePanelActions; },
/* harmony export */   usePanelStatus: function() { return /* reexport safe */ _panel__WEBPACK_IMPORTED_MODULE_26__.usePanelStatus; },
/* harmony export */   useStateByElement: function() { return /* reexport safe */ _hooks_use_state_by_element__WEBPACK_IMPORTED_MODULE_22__.useStateByElement; },
/* harmony export */   useStyle: function() { return /* reexport safe */ _contexts_style_context__WEBPACK_IMPORTED_MODULE_15__.useStyle; },
/* harmony export */   useStylesRerender: function() { return /* reexport safe */ _hooks_use_styles_rerender__WEBPACK_IMPORTED_MODULE_24__.useStylesRerender; }
/* harmony export */ });
/* harmony import */ var _components_css_classes_css_class_convert_local__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/css-classes/css-class-convert-local */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-convert-local.tsx");
/* harmony import */ var _components_control_label__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/control-label */ "./packages/packages/core/editor-editing-panel/src/components/control-label.tsx");
/* harmony import */ var _components_css_classes_css_class_selector__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/css-classes/css-class-selector */ "./packages/packages/core/editor-editing-panel/src/components/css-classes/css-class-selector.tsx");
/* harmony import */ var _components_custom_css_indicator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/custom-css-indicator */ "./packages/packages/core/editor-editing-panel/src/components/custom-css-indicator.tsx");
/* harmony import */ var _components_editing_panel__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/editing-panel */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel.tsx");
/* harmony import */ var _components_editing_panel_tabs__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/editing-panel-tabs */ "./packages/packages/core/editor-editing-panel/src/components/editing-panel-tabs.tsx");
/* harmony import */ var _components_section_content__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/section-content */ "./packages/packages/core/editor-editing-panel/src/components/section-content.tsx");
/* harmony import */ var _components_settings_control__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/settings-control */ "./packages/packages/core/editor-editing-panel/src/components/settings-control.tsx");
/* harmony import */ var _controls_registry_settings_field__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./controls-registry/settings-field */ "./packages/packages/core/editor-editing-panel/src/controls-registry/settings-field.tsx");
/* harmony import */ var _components_style_indicator__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/style-indicator */ "./packages/packages/core/editor-editing-panel/src/components/style-indicator.tsx");
/* harmony import */ var _components_style_tab__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./components/style-tab */ "./packages/packages/core/editor-editing-panel/src/components/style-tab.tsx");
/* harmony import */ var _components_style_sections_layout_section_layout_section__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./components/style-sections/layout-section/layout-section */ "./packages/packages/core/editor-editing-panel/src/components/style-sections/layout-section/layout-section.tsx");
/* harmony import */ var _components_style_tab_section__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/style-tab-section */ "./packages/packages/core/editor-editing-panel/src/components/style-tab-section.tsx");
/* harmony import */ var _contexts_classes_prop_context__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./contexts/classes-prop-context */ "./packages/packages/core/editor-editing-panel/src/contexts/classes-prop-context.tsx");
/* harmony import */ var _contexts_element_context__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./contexts/element-context */ "./packages/packages/core/editor-editing-panel/src/contexts/element-context.tsx");
/* harmony import */ var _contexts_style_context__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./contexts/style-context */ "./packages/packages/core/editor-editing-panel/src/contexts/style-context.tsx");
/* harmony import */ var _controls_registry_control__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./controls-registry/control */ "./packages/packages/core/editor-editing-panel/src/controls-registry/control.tsx");
/* harmony import */ var _controls_registry_control_type_container__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./controls-registry/control-type-container */ "./packages/packages/core/editor-editing-panel/src/controls-registry/control-type-container.tsx");
/* harmony import */ var _controls_registry_controls_registry__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./controls-registry/controls-registry */ "./packages/packages/core/editor-editing-panel/src/controls-registry/controls-registry.tsx");
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./errors */ "./packages/packages/core/editor-editing-panel/src/errors.ts");
/* harmony import */ var _controls_registry_create_top_level_object_type__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./controls-registry/create-top-level-object-type */ "./packages/packages/core/editor-editing-panel/src/controls-registry/create-top-level-object-type.ts");
/* harmony import */ var _hooks_use_custom_css__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./hooks/use-custom-css */ "./packages/packages/core/editor-editing-panel/src/hooks/use-custom-css.ts");
/* harmony import */ var _hooks_use_state_by_element__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./hooks/use-state-by-element */ "./packages/packages/core/editor-editing-panel/src/hooks/use-state-by-element.ts");
/* harmony import */ var _hooks_use_styles_fields__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./hooks/use-styles-fields */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-fields.ts");
/* harmony import */ var _hooks_use_styles_rerender__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./hooks/use-styles-rerender */ "./packages/packages/core/editor-editing-panel/src/hooks/use-styles-rerender.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-editing-panel/src/init.ts");
/* harmony import */ var _panel__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./panel */ "./packages/packages/core/editor-editing-panel/src/panel.ts");
/* harmony import */ var _provider_colors_registry__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./provider-colors-registry */ "./packages/packages/core/editor-editing-panel/src/provider-colors-registry.ts");
/* harmony import */ var _field_indicators_registry__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./field-indicators-registry */ "./packages/packages/core/editor-editing-panel/src/field-indicators-registry.ts");
/* harmony import */ var _editing_panel_replacement_registry__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./editing-panel-replacement-registry */ "./packages/packages/core/editor-editing-panel/src/editing-panel-replacement-registry.tsx");
/* harmony import */ var _hooks_use_default_panel_settings__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./hooks/use-default-panel-settings */ "./packages/packages/core/editor-editing-panel/src/hooks/use-default-panel-settings.ts");
/* harmony import */ var _apply_unapply_actions__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./apply-unapply-actions */ "./packages/packages/core/editor-editing-panel/src/apply-unapply-actions.ts");
/* harmony import */ var _hooks_use_license_config__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./hooks/use-license-config */ "./packages/packages/core/editor-editing-panel/src/hooks/use-license-config.ts");
/* harmony import */ var _dynamics_utils__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./dynamics/utils */ "./packages/packages/core/editor-editing-panel/src/dynamics/utils.ts");
/* harmony import */ var _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./utils/prop-dependency-utils */ "./packages/packages/core/editor-editing-panel/src/utils/prop-dependency-utils.ts");



































}();
(window.elementorV2 = window.elementorV2 || {}).editorEditingPanel = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorEditingPanel?.init?.();
//# sourceMappingURL=editor-editing-panel.js.map
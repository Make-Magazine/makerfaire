/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-styles-repository/src/errors.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/errors.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ActiveDocumentMustExistError: function() { return /* binding */ ActiveDocumentMustExistError; },
/* harmony export */   InvalidElementsStyleProviderMetaError: function() { return /* binding */ InvalidElementsStyleProviderMetaError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const InvalidElementsStyleProviderMetaError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'invalid_elements_style_provider_meta',
  message: 'Invalid elements style provider meta.'
});
const ActiveDocumentMustExistError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'active_document_must_exist',
  message: 'Active document must exist.'
});

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/hooks/use-get-styles-repository-create-action.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/hooks/use-get-styles-repository-create-action.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useGetStylesRepositoryCreateAction: function() { return /* binding */ useGetStylesRepositoryCreateAction; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../styles-repository */ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts");
/* harmony import */ var _use_user_styles_capability__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-user-styles-capability */ "./packages/packages/core/editor-styles-repository/src/hooks/use-user-styles-capability.ts");



function useGetStylesRepositoryCreateAction() {
  const {
    userCan
  } = (0,_use_user_styles_capability__WEBPACK_IMPORTED_MODULE_2__.useUserStylesCapability)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const createActions = _styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.getProviders().map(provider => {
      if (!provider.actions.create || !userCan(provider.getKey()).create) {
        return null;
      }
      return [provider, provider.actions.create];
    }).filter(Boolean);
    if (createActions.length === 1) {
      return createActions[0];
    } else if (createActions.length === 0) {
      return null;
    }
    throw new Error('Multiple providers with create action found in styles repository.');
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
}

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/hooks/use-providers.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/hooks/use-providers.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useProviders: function() { return /* binding */ useProviders; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../styles-repository */ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts");


function useProviders() {
  const [, rerender] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useReducer)(prev => !prev, false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => _styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.subscribe(rerender), []);
  return _styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.getProviders();
}

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/hooks/use-user-styles-capability.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/hooks/use-user-styles-capability.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useUserStylesCapability: function() { return /* binding */ useUserStylesCapability; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../styles-repository */ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts");


const DEFAULT_CAPABILITIES = {
  create: true,
  delete: true,
  update: true,
  updateProps: true
};
const useUserStylesCapability = () => {
  const {
    capabilities
  } = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_0__.useCurrentUserCapabilities)();
  const userCan = providerKey => {
    const provider = _styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository.getProviderByKey(providerKey);
    if (!provider?.capabilities) {
      return DEFAULT_CAPABILITIES;
    }
    return Object.entries(provider.capabilities).reduce((acc, [key, capability]) => ({
      ...acc,
      [key]: capabilities?.includes(capability) ?? true
    }), DEFAULT_CAPABILITIES);
  };
  return {
    userCan
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/init.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/init.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./providers/document-elements-styles-provider */ "./packages/packages/core/editor-styles-repository/src/providers/document-elements-styles-provider.ts");
/* harmony import */ var _providers_element_base_styles_provider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./providers/element-base-styles-provider */ "./packages/packages/core/editor-styles-repository/src/providers/element-base-styles-provider.ts");
/* harmony import */ var _styles_repository__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./styles-repository */ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts");



function init() {
  _styles_repository__WEBPACK_IMPORTED_MODULE_2__.stylesRepository.register(_providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_0__.documentElementsStylesProvider);
  _styles_repository__WEBPACK_IMPORTED_MODULE_2__.stylesRepository.register(_providers_element_base_styles_provider__WEBPACK_IMPORTED_MODULE_1__.elementBaseStylesProvider);
}

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/providers/document-elements-styles-provider.ts":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/providers/document-elements-styles-provider.ts ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENTS_STYLES_PROVIDER_KEY_PREFIX: function() { return /* binding */ ELEMENTS_STYLES_PROVIDER_KEY_PREFIX; },
/* harmony export */   ELEMENTS_STYLES_RESERVED_LABEL: function() { return /* binding */ ELEMENTS_STYLES_RESERVED_LABEL; },
/* harmony export */   documentElementsStylesProvider: function() { return /* binding */ documentElementsStylesProvider; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../errors */ "./packages/packages/core/editor-styles-repository/src/errors.ts");
/* harmony import */ var _utils_create_styles_provider__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/create-styles-provider */ "./packages/packages/core/editor-styles-repository/src/utils/create-styles-provider.ts");




const ELEMENTS_STYLES_PROVIDER_KEY_PREFIX = 'document-elements-';
const ELEMENTS_STYLES_RESERVED_LABEL = 'local';
const PREGENERATED_LINK_PATTERN = /^local-\d+-(preview|frontend)-[a-zA-Z_-]+-css$/;
const documentElementsStylesProvider = (0,_utils_create_styles_provider__WEBPACK_IMPORTED_MODULE_3__.createStylesProvider)({
  key: () => {
    const documentId = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getCurrentDocumentId)();
    if (!documentId) {
      throw new _errors__WEBPACK_IMPORTED_MODULE_2__.ActiveDocumentMustExistError();
    }
    return `${ELEMENTS_STYLES_PROVIDER_KEY_PREFIX}${documentId}`;
  },
  priority: 50,
  isPregeneratedLink: ({
    id
  }) => PREGENERATED_LINK_PATTERN.test(id),
  subscribe: cb => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateListenTo)(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.styleRerenderEvents, () => cb()),
  actions: {
    all: (meta = {}) => {
      let elements = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElements)();
      if (isValidElementsMeta(meta)) {
        elements = elements.filter(element => element.id === meta.elementId);
      }
      return elements.flatMap(element => Object.values(element.model.get('styles') ?? {}));
    },
    get: (id, meta = {}) => {
      if (!isValidElementsMeta(meta)) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_2__.InvalidElementsStyleProviderMetaError({
          context: {
            meta
          }
        });
      }
      const styles = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getElementStyles)(meta.elementId) ?? {};
      return styles[id] ?? null;
    },
    updateProps: (args, meta = {}) => {
      if (!isValidElementsMeta(meta)) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_2__.InvalidElementsStyleProviderMetaError({
          context: {
            meta
          }
        });
      }
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementStyle)({
        elementId: meta.elementId,
        styleId: args.id,
        meta: args.meta,
        props: args.props
      });
    },
    updateCustomCss: (args, meta = {}) => {
      if (!isValidElementsMeta(meta)) {
        throw new _errors__WEBPACK_IMPORTED_MODULE_2__.InvalidElementsStyleProviderMetaError({
          context: {
            meta
          }
        });
      }
      (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.updateElementStyle)({
        elementId: meta.elementId,
        styleId: args.id,
        meta: args.meta,
        custom_css: args.custom_css.raw ? args.custom_css : null,
        props: {}
      });
    }
  }
});
function isValidElementsMeta(meta) {
  return 'elementId' in meta && typeof meta.elementId === 'string' && !!meta.elementId;
}

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/providers/element-base-styles-provider.ts":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/providers/element-base-styles-provider.ts ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENTS_BASE_STYLES_PROVIDER_KEY: function() { return /* binding */ ELEMENTS_BASE_STYLES_PROVIDER_KEY; },
/* harmony export */   elementBaseStylesProvider: function() { return /* binding */ elementBaseStylesProvider; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-elements */ "@elementor/editor-elements");
/* harmony import */ var _elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_styles_provider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-styles-provider */ "./packages/packages/core/editor-styles-repository/src/utils/create-styles-provider.ts");


const ELEMENTS_BASE_STYLES_PROVIDER_KEY = 'element-base-styles';
const elementBaseStylesProvider = (0,_utils_create_styles_provider__WEBPACK_IMPORTED_MODULE_1__.createStylesProvider)({
  key: ELEMENTS_BASE_STYLES_PROVIDER_KEY,
  actions: {
    all() {
      const widgetsCache = (0,_elementor_editor_elements__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)();
      return Object.values(widgetsCache ?? {}).flatMap(widget => Object.values(widget.base_styles ?? {}));
    },
    get(id) {
      return this.all().find(style => style.id === id) ?? null;
    }
  }
});

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/styles-repository.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   stylesRepository: function() { return /* binding */ stylesRepository; }
/* harmony export */ });
/* harmony import */ var _utils_create_styles_repository__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/create-styles-repository */ "./packages/packages/core/editor-styles-repository/src/utils/create-styles-repository.ts");

const stylesRepository = (0,_utils_create_styles_repository__WEBPACK_IMPORTED_MODULE_0__.createStylesRepository)();

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/types.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/types.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/utils/create-styles-provider.ts":
/*!*********************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/utils/create-styles-provider.ts ***!
  \*********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createStylesProvider: function() { return /* binding */ createStylesProvider; }
/* harmony export */ });
const DEFAULT_LIMIT = 10000;
const DEFAULT_PRIORITY = 10;
function createStylesProvider({
  key,
  priority = DEFAULT_PRIORITY,
  limit = DEFAULT_LIMIT,
  subscribe = () => () => {},
  labels,
  actions,
  capabilities,
  isPregeneratedLink
}) {
  return {
    getKey: typeof key === 'string' ? () => key : key,
    priority,
    limit,
    capabilities,
    subscribe,
    labels: {
      singular: labels?.singular ?? null,
      plural: labels?.plural ?? null
    },
    actions: {
      all: actions.all,
      get: actions.get,
      resolveCssName: actions.resolveCssName ?? (id => id),
      create: actions.create,
      delete: actions.delete,
      update: actions.update,
      updateProps: actions.updateProps,
      updateCustomCss: actions.updateCustomCss,
      tracking: actions.tracking
    },
    isPregeneratedLink
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/utils/create-styles-repository.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/utils/create-styles-repository.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createStylesRepository: function() { return /* binding */ createStylesRepository; }
/* harmony export */ });
const createStylesRepository = () => {
  const providers = [];
  const getProviders = () => {
    return providers.slice(0).sort((a, b) => a.priority > b.priority ? -1 : 1);
  };
  const register = provider => {
    providers.push(provider);
  };
  const all = (meta = {}) => {
    return getProviders().flatMap(provider => provider.actions.all(meta));
  };
  const subscribe = cb => {
    const unsubscribes = providers.map(provider => {
      return provider.subscribe(cb);
    });
    return () => {
      unsubscribes.forEach(unsubscribe => unsubscribe());
    };
  };
  const getProviderByKey = key => {
    return providers.find(provider => provider.getKey() === key);
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

/***/ "./packages/packages/core/editor-styles-repository/src/utils/is-elements-styles-provider.ts":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/utils/is-elements-styles-provider.ts ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isElementsStylesProvider: function() { return /* binding */ isElementsStylesProvider; }
/* harmony export */ });
/* harmony import */ var _providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../providers/document-elements-styles-provider */ "./packages/packages/core/editor-styles-repository/src/providers/document-elements-styles-provider.ts");

function isElementsStylesProvider(key) {
  return new RegExp(`^${_providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_0__.ELEMENTS_STYLES_PROVIDER_KEY_PREFIX}\\d+$`).test(key);
}

/***/ }),

/***/ "./packages/packages/core/editor-styles-repository/src/utils/validate-style-label.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/utils/validate-style-label.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   validateStyleLabel: function() { return /* binding */ validateStyleLabel; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../providers/document-elements-styles-provider */ "./packages/packages/core/editor-styles-repository/src/providers/document-elements-styles-provider.ts");
/* harmony import */ var _styles_repository__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../styles-repository */ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts");




const NO_START_DIGIT_REGEX = /^(|[^0-9].*)$/;
const NO_SPACES_REGEX = /^\S*$/;
const NO_SPECIAL_CHARS_REGEX = /^(|[a-zA-Z0-9_-]+)$/;
const NO_DOUBLE_HYPHEN_START_REGEX = /^(?!--).*/;
const NO_HYPHEN_DIGIT_START_REGEX = /^(?!-[0-9])/;
const RESERVED_CLASS_NAMES = ['container'];
const schema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().max(50, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Class name is too long. Please keep it under 50 characters.', 'elementor')).regex(NO_START_DIGIT_REGEX, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Class names must start with a letter.', 'elementor')).regex(NO_SPACES_REGEX, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Class names can’t contain spaces.', 'elementor')).regex(NO_SPECIAL_CHARS_REGEX, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Class names can only use letters, numbers, dashes (-), and underscores (_).', 'elementor')).regex(NO_DOUBLE_HYPHEN_START_REGEX, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Double hyphens are reserved for custom properties.', 'elementor')).regex(NO_HYPHEN_DIGIT_START_REGEX, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Class names can’t start with a hyphen followed by a number.', 'elementor')).refine(value => !RESERVED_CLASS_NAMES.includes(value), {
  message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('This name is reserved and can’t be used. Try something more specific.', 'elementor')
});
function validateStyleLabel(label, event) {
  const existingLabels = new Set([_providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_2__.ELEMENTS_STYLES_RESERVED_LABEL, ..._styles_repository__WEBPACK_IMPORTED_MODULE_3__.stylesRepository.all().map(styleDef => styleDef.label.toLowerCase())]);
  const fullValidationEvent = ['create', 'rename'].includes(event);
  const result = schema.refine(value => !(fullValidationEvent && value.length < 2), {
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Class name is too short. Use at least 2 characters.', 'elementor')
  }).refine(value => !(fullValidationEvent && existingLabels.has(value)), {
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('This class name already exists. Please choose a unique name.', 'elementor')
  }).safeParse(label.toLowerCase());
  if (result.success) {
    return {
      isValid: true,
      errorMessage: null
    };
  }
  return {
    isValid: false,
    errorMessage: result.error.format()._errors[0]
  };
}

/***/ }),

/***/ "@elementor/editor-current-user":
/*!****************************************************!*\
  !*** external ["elementorV2","editorCurrentUser"] ***!
  \****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCurrentUser"];

/***/ }),

/***/ "@elementor/editor-elements":
/*!*************************************************!*\
  !*** external ["elementorV2","editorElements"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorElements"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

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
/*!**********************************************************************!*\
  !*** ./packages/packages/core/editor-styles-repository/src/index.ts ***!
  \**********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENTS_BASE_STYLES_PROVIDER_KEY: function() { return /* reexport safe */ _providers_element_base_styles_provider__WEBPACK_IMPORTED_MODULE_8__.ELEMENTS_BASE_STYLES_PROVIDER_KEY; },
/* harmony export */   ELEMENTS_STYLES_PROVIDER_KEY_PREFIX: function() { return /* reexport safe */ _providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_9__.ELEMENTS_STYLES_PROVIDER_KEY_PREFIX; },
/* harmony export */   ELEMENTS_STYLES_RESERVED_LABEL: function() { return /* reexport safe */ _providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_9__.ELEMENTS_STYLES_RESERVED_LABEL; },
/* harmony export */   createStylesProvider: function() { return /* reexport safe */ _utils_create_styles_provider__WEBPACK_IMPORTED_MODULE_6__.createStylesProvider; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_10__.init; },
/* harmony export */   isElementsStylesProvider: function() { return /* reexport safe */ _utils_is_elements_styles_provider__WEBPACK_IMPORTED_MODULE_7__.isElementsStylesProvider; },
/* harmony export */   stylesRepository: function() { return /* reexport safe */ _styles_repository__WEBPACK_IMPORTED_MODULE_1__.stylesRepository; },
/* harmony export */   useGetStylesRepositoryCreateAction: function() { return /* reexport safe */ _hooks_use_get_styles_repository_create_action__WEBPACK_IMPORTED_MODULE_3__.useGetStylesRepositoryCreateAction; },
/* harmony export */   useProviders: function() { return /* reexport safe */ _hooks_use_providers__WEBPACK_IMPORTED_MODULE_2__.useProviders; },
/* harmony export */   useUserStylesCapability: function() { return /* reexport safe */ _hooks_use_user_styles_capability__WEBPACK_IMPORTED_MODULE_4__.useUserStylesCapability; },
/* harmony export */   validateStyleLabel: function() { return /* reexport safe */ _utils_validate_style_label__WEBPACK_IMPORTED_MODULE_5__.validateStyleLabel; }
/* harmony export */ });
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./types */ "./packages/packages/core/editor-styles-repository/src/types.ts");
/* harmony import */ var _styles_repository__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles-repository */ "./packages/packages/core/editor-styles-repository/src/styles-repository.ts");
/* harmony import */ var _hooks_use_providers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks/use-providers */ "./packages/packages/core/editor-styles-repository/src/hooks/use-providers.ts");
/* harmony import */ var _hooks_use_get_styles_repository_create_action__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./hooks/use-get-styles-repository-create-action */ "./packages/packages/core/editor-styles-repository/src/hooks/use-get-styles-repository-create-action.ts");
/* harmony import */ var _hooks_use_user_styles_capability__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./hooks/use-user-styles-capability */ "./packages/packages/core/editor-styles-repository/src/hooks/use-user-styles-capability.ts");
/* harmony import */ var _utils_validate_style_label__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/validate-style-label */ "./packages/packages/core/editor-styles-repository/src/utils/validate-style-label.ts");
/* harmony import */ var _utils_create_styles_provider__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/create-styles-provider */ "./packages/packages/core/editor-styles-repository/src/utils/create-styles-provider.ts");
/* harmony import */ var _utils_is_elements_styles_provider__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/is-elements-styles-provider */ "./packages/packages/core/editor-styles-repository/src/utils/is-elements-styles-provider.ts");
/* harmony import */ var _providers_element_base_styles_provider__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./providers/element-base-styles-provider */ "./packages/packages/core/editor-styles-repository/src/providers/element-base-styles-provider.ts");
/* harmony import */ var _providers_document_elements_styles_provider__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./providers/document-elements-styles-provider */ "./packages/packages/core/editor-styles-repository/src/providers/document-elements-styles-provider.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-styles-repository/src/init.ts");











}();
(window.elementorV2 = window.elementorV2 || {}).editorStylesRepository = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorStylesRepository?.init?.();
//# sourceMappingURL=editor-styles-repository.js.map
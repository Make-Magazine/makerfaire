"use strict";
(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["ai-form-code"],{

/***/ "../modules/ai/assets/js/editor/hooks/use-code-prompt.js":
/*!***************************************************************!*\
  !*** ../modules/ai/assets/js/editor/hooks/use-code-prompt.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _api = __webpack_require__(/*! ../api */ "../modules/ai/assets/js/editor/api/index.js");
var _usePrompt = _interopRequireDefault(__webpack_require__(/*! ./use-prompt */ "../modules/ai/assets/js/editor/hooks/use-prompt.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var getCodeResult = /*#__PURE__*/function () {
  var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(payload, _ref) {
    var codeLanguage, htmlMarkup, elementId;
    return _regenerator.default.wrap(function (_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          codeLanguage = _ref.codeLanguage, htmlMarkup = _ref.htmlMarkup, elementId = _ref.elementId;
          if (!('css' === codeLanguage)) {
            _context.next = 1;
            break;
          }
          return _context.abrupt("return", (0, _api.getCustomCSS)(_objectSpread(_objectSpread({}, payload), {}, {
            html_markup: htmlMarkup,
            element_id: elementId
          })));
        case 1:
          return _context.abrupt("return", (0, _api.getCustomCode)(_objectSpread(_objectSpread({}, payload), {}, {
            language: codeLanguage
          })));
        case 2:
        case "end":
          return _context.stop();
      }
    }, _callee);
  }));
  return function getCodeResult(_x, _x2) {
    return _ref2.apply(this, arguments);
  };
}();
var useCodePrompt = function useCodePrompt(_ref3) {
  var codeLanguage = _ref3.codeLanguage,
    htmlMarkup = _ref3.htmlMarkup,
    elementId = _ref3.elementId,
    credits = _ref3.initialCredits;
  var promptData = (0, _usePrompt.default)(function (payload) {
    return getCodeResult(payload, {
      codeLanguage: codeLanguage,
      htmlMarkup: htmlMarkup,
      elementId: elementId
    });
  }, {
    credits: credits
  });
  return promptData;
};
var _default = exports["default"] = useCodePrompt;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-code/code-block.js":
/*!********************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-code/code-block.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _textarea = _interopRequireDefault(__webpack_require__(/*! ../../components/textarea */ "../modules/ai/assets/js/editor/components/textarea.js"));
var _excluded = ["node", "inline", "children", "defaultValue", "onInsert"];
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var CodeBlock = function CodeBlock(_ref) {
  var node = _ref.node,
    inline = _ref.inline,
    children = _ref.children,
    defaultValue = _ref.defaultValue,
    onInsert = _ref.onInsert,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  var codeBlockInput = (0, _react.useRef)(null);
  if (inline) {
    return /*#__PURE__*/_react.default.createElement("code", props);
  }
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      position: 'relative'
    },
    dir: "ltr"
  }, /*#__PURE__*/_react.default.createElement(_textarea.default, (0, _extends2.default)({
    fullWidth: true,
    ref: codeBlockInput,
    defaultValue: children[0],
    sx: {
      mb: 1
    },
    helperText: (0, _i18n.__)('Code generated by AI may be inaccurate.', 'elementor')
  }, props)), /*#__PURE__*/_react.default.createElement(_ui.Button, {
    size: "small",
    variant: "contained",
    onClick: function onClick() {
      return onInsert(defaultValue + '\n' + codeBlockInput.current.value);
    },
    sx: {
      position: 'absolute',
      right: '11px /* @noflip */',
      bottom: '44px'
    }
  }, (0, _i18n.__)('Insert', 'elementor')));
};
CodeBlock.propTypes = {
  node: _propTypes.default.object,
  inline: _propTypes.default.bool,
  children: _propTypes.default.arrayOf(_propTypes.default.node).isRequired,
  defaultValue: _propTypes.default.string,
  onInsert: _propTypes.default.func.isRequired
};
var _default = exports["default"] = CodeBlock;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-code/index.js":
/*!***************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-code/index.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _actionsData = __webpack_require__(/*! ../../actions-data */ "../modules/ai/assets/js/editor/actions-data.js");
var _loader = _interopRequireDefault(__webpack_require__(/*! ../../components/loader */ "../modules/ai/assets/js/editor/components/loader.js"));
var _promptSearch = _interopRequireDefault(__webpack_require__(/*! ../../components/prompt-search */ "../modules/ai/assets/js/editor/components/prompt-search.js"));
var _promptSuggestions = _interopRequireDefault(__webpack_require__(/*! ../../components/prompt-suggestions */ "../modules/ai/assets/js/editor/components/prompt-suggestions.js"));
var _generateButton = _interopRequireDefault(__webpack_require__(/*! ../../components/generate-button */ "../modules/ai/assets/js/editor/components/generate-button.js"));
var _promptErrorMessage = _interopRequireDefault(__webpack_require__(/*! ../../components/prompt-error-message */ "../modules/ai/assets/js/editor/components/prompt-error-message.js"));
var _codeBlock = _interopRequireDefault(__webpack_require__(/*! ./code-block */ "../modules/ai/assets/js/editor/pages/form-code/code-block.js"));
var _useCodePrompt2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-code-prompt */ "../modules/ai/assets/js/editor/hooks/use-code-prompt.js"));
var _promptHistoryActionContext = __webpack_require__(/*! ../../components/prompt-history/context/prompt-history-action-context */ "../modules/ai/assets/js/editor/components/prompt-history/context/prompt-history-action-context.js");
var _promptLibraryLink = _interopRequireDefault(__webpack_require__(/*! ../../components/prompt-library-link */ "../modules/ai/assets/js/editor/components/prompt-library-link.js"));
var _requestsIds = __webpack_require__(/*! ../../context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
var _voicePromotionAlert = __webpack_require__(/*! ../../components/voice-promotion-alert */ "../modules/ai/assets/js/editor/components/voice-promotion-alert.js");
var _splitTextResult = __webpack_require__(/*! ./splitTextResult */ "../modules/ai/assets/js/editor/pages/form-code/splitTextResult.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var ReactMarkdown = (0, _react.lazy)(function () {
  return __webpack_require__.e(/*! import() | ai-react-markdown */ "ai-react-markdown").then(__webpack_require__.bind(__webpack_require__, /*! react-markdown */ "../node_modules/react-markdown/index.js"));
});
var generateUniqueId = function generateUniqueId() {
  return "custom-css-".concat(Math.random().toString(36).substr(2, 9));
};
var CodeDisplayWrapper = (0, _ui.styled)(_ui.Box)(function () {
  return {
    '& p': {
      mb: '10px',
      fontSize: '13px',
      lineHeight: '1.5'
    },
    '& pre': {
      position: 'relative'
    },
    '& textarea': {
      fontSize: '13px',
      lineHeight: '1.7'
    }
  };
});
var FormCode = function FormCode(_ref) {
  var onClose = _ref.onClose,
    getControlValue = _ref.getControlValue,
    setControlValue = _ref.setControlValue,
    additionalOptions = _ref.additionalOptions,
    credits = _ref.credits,
    children = _ref.children;
  var _useCodePrompt = (0, _useCodePrompt2.default)(_objectSpread(_objectSpread({}, additionalOptions), {}, {
      credits: credits
    })),
    data = _useCodePrompt.data,
    isLoading = _useCodePrompt.isLoading,
    error = _useCodePrompt.error,
    reset = _useCodePrompt.reset,
    send = _useCodePrompt.send,
    sendUsageData = _useCodePrompt.sendUsageData;
  var _splitText = (0, _splitTextResult.splitText)(data.result),
    code = _splitText.code,
    details = _splitText.details;
  var _useState = (0, _react.useState)(''),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    prompt = _useState2[0],
    setPrompt = _useState2[1];
  var _useRequestIds = (0, _requestsIds.useRequestIds)(),
    setGenerate = _useRequestIds.setGenerate;
  var styleTagId = (0, _react.useRef)(generateUniqueId());
  (0, _promptHistoryActionContext.useSubscribeOnPromptHistoryAction)([{
    type: _promptHistoryActionContext.ACTION_TYPES.REUSE,
    handler: function handler(action) {
      reset();
      setPrompt(action.data);
    }
  }]);
  var lastRun = (0, _react.useRef)(function () {});
  var autocompleteItems = _actionsData.codeHtmlAutocomplete;
  var promptLibraryLink = '';
  if ('css' === (additionalOptions === null || additionalOptions === void 0 ? void 0 : additionalOptions.codeLanguage)) {
    autocompleteItems = _actionsData.codeCssAutocomplete;
    promptLibraryLink = 'https://go.elementor.com/ai-prompt-library-css/';
  } else if (additionalOptions !== null && additionalOptions !== void 0 && additionalOptions.htmlMarkup) {
    promptLibraryLink = 'https://go.elementor.com/ai-prompt-library-html/';
  } else {
    promptLibraryLink = 'https://go.elementor.com/ai-prompt-library-custom-code/';
  }
  var showSuggestions = !prompt;
  var handleSubmit = /*#__PURE__*/function () {
    var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(event) {
      var response;
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            event.preventDefault();
            setGenerate();
            lastRun.current = function () {
              return send({
                prompt: prompt
              });
            };
            _context.next = 1;
            return lastRun.current();
          case 1:
            response = _context.sent;
            if ('css' === (additionalOptions === null || additionalOptions === void 0 ? void 0 : additionalOptions.codeLanguage) && response.result) {
              showCssPreview((0, _splitTextResult.splitText)(response.result).code);
            }
          case 2:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function handleSubmit(_x) {
      return _ref2.apply(this, arguments);
    };
  }();
  (0, _react.useEffect)(function () {
    return function () {
      removeStyleTag();
    };
  }, []);
  var showCssPreview = function showCssPreview(cssCode) {
    var parsedCssCode = parseCSS(cssCode);
    insertStyleTag(parsedCssCode);
  };
  var parseCSS = function parseCSS(cssCode) {
    var elementId = additionalOptions === null || additionalOptions === void 0 ? void 0 : additionalOptions.elementId;
    var selector = 'document' === elementId ? elementor.config.document.settings.cssWrapperSelector : ".elementor-element.elementor-element-".concat(elementId);
    return cssCode && cssCode.replace(/`/g, '') // Remove backticks if any
    .replace(/^css\s*/i, '') // Remove "css" prefix if any, case-insensitive
    .replace(/selector/g, selector); // Replace `selector` with the actual selector
  };
  var isElementorEditor = function isElementorEditor() {
    return window.elementorFrontend;
  };
  var insertStyleTag = function insertStyleTag(cssCode) {
    if (!isElementorEditor()) {
      return;
    }
    var style = document.createElement('style');
    style.id = styleTagId.current;
    style.appendChild(document.createTextNode(cssCode));
    elementorFrontend.elements.$body[0].appendChild(style);
  };
  var removeStyleTag = function removeStyleTag() {
    if (!isElementorEditor()) {
      return;
    }
    var styleTag = elementorFrontend.elements.$body[0].querySelector("#".concat(styleTagId.current));
    if (styleTag) {
      styleTag.remove();
    }
  };
  var applyPrompt = function applyPrompt(inputText) {
    sendUsageData();
    setControlValue(inputText);
    onClose();
  };
  if (isLoading) {
    return /*#__PURE__*/_react.default.createElement(_loader.default, null);
  }
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, error && /*#__PURE__*/_react.default.createElement(_promptErrorMessage.default, {
    error: error,
    onRetry: lastRun.current,
    sx: {
      mb: 2.5
    }
  }), children, !data.result && /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: "form",
    onSubmit: handleSubmit
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      pb: 1.5
    }
  }, /*#__PURE__*/_react.default.createElement(_promptSearch.default, {
    placeholder: (0, _i18n.__)('Describe the code you want to use...', 'elementor'),
    name: "prompt",
    value: prompt,
    color: "secondary",
    onChange: function onChange(event) {
      return setPrompt(event.target.value);
    }
  })), showSuggestions && /*#__PURE__*/_react.default.createElement(_promptSuggestions.default, {
    suggestions: autocompleteItems,
    onSelect: setPrompt
  }, /*#__PURE__*/_react.default.createElement(_promptLibraryLink.default, {
    libraryLink: promptLibraryLink
  })), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    alignItems: "center",
    sx: {
      py: 1.5,
      mt: 4
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    justifyContent: "flex-end",
    flexGrow: 1
  }, /*#__PURE__*/_react.default.createElement(_generateButton.default, null, (0, _i18n.__)('Generate code', 'elementor'))))), data.result && /*#__PURE__*/_react.default.createElement(CodeDisplayWrapper, null, /*#__PURE__*/_react.default.createElement(_react.Suspense, {
    fallback: /*#__PURE__*/_react.default.createElement(_loader.default, null)
  }, /*#__PURE__*/_react.default.createElement(ReactMarkdown, {
    components: {
      code: function code(props) {
        return /*#__PURE__*/_react.default.createElement(_codeBlock.default, (0, _extends2.default)({}, props, {
          defaultValue: getControlValue(),
          onInsert: applyPrompt
        }));
      }
    }
  }, code)), details, /*#__PURE__*/_react.default.createElement(_voicePromotionAlert.VoicePromotionAlert, {
    introductionKey: "ai-context-code-promotion"
  }), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    alignItems: "center",
    sx: {
      mt: 4
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    gap: 1,
    justifyContent: "flex-end",
    flexGrow: 1
  }, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    size: "small",
    color: "secondary",
    variant: "text",
    onClick: function onClick() {
      removeStyleTag();
      reset();
    }
  }, (0, _i18n.__)('New prompt', 'elementor'))))));
};
FormCode.propTypes = {
  onClose: _propTypes.default.func.isRequired,
  getControlValue: _propTypes.default.func.isRequired,
  setControlValue: _propTypes.default.func.isRequired,
  additionalOptions: _propTypes.default.shape({
    codeLanguage: _propTypes.default.string,
    htmlMarkup: _propTypes.default.string,
    elementId: _propTypes.default.string,
    initialCredits: _propTypes.default.number
  }),
  credits: _propTypes.default.number,
  children: _propTypes.default.node
};
var _default = exports["default"] = FormCode;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-code/splitTextResult.js":
/*!*************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-code/splitTextResult.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.splitText = splitText;
function splitText(inputText) {
  if (!inputText) {
    return {};
  }
  var codeMatch = inputText.match(/```([\s\S]*?)```/);
  var code = codeMatch ? "```".concat(codeMatch[1], "```").trim() : '';
  var detailsMatch = inputText.match(/```[\s\S]*?```([\s\S]*)/);
  var details = detailsMatch === null || detailsMatch === void 0 ? void 0 : detailsMatch[1].trim();
  return {
    code: code,
    details: details
  };
}

/***/ })

}]);
//# sourceMappingURL=ai-form-code.f1fc9ab9d55b03c0a95d.bundle.js.map
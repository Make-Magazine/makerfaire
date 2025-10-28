/*! elementor - v3.32.0 - 21-10-2025 */
"use strict";
(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["onboarding"],{

/***/ "../app/modules/onboarding/assets/js/app.js":
/*!**************************************************!*\
  !*** ../app/modules/onboarding/assets/js/app.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = App;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _router2 = _interopRequireDefault(__webpack_require__(/*! @elementor/router */ "@elementor/router"));
var _context = __webpack_require__(/*! ./context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _account = _interopRequireDefault(__webpack_require__(/*! ./pages/account */ "../app/modules/onboarding/assets/js/pages/account.js"));
var _helloTheme = _interopRequireDefault(__webpack_require__(/*! ./pages/hello-theme */ "../app/modules/onboarding/assets/js/pages/hello-theme.js"));
var _siteName = _interopRequireDefault(__webpack_require__(/*! ./pages/site-name */ "../app/modules/onboarding/assets/js/pages/site-name.js"));
var _siteLogo = _interopRequireDefault(__webpack_require__(/*! ./pages/site-logo */ "../app/modules/onboarding/assets/js/pages/site-logo.js"));
var _goodToGo = _interopRequireDefault(__webpack_require__(/*! ./pages/good-to-go */ "../app/modules/onboarding/assets/js/pages/good-to-go.js"));
var _uploadAndInstallPro = _interopRequireDefault(__webpack_require__(/*! ./pages/upload-and-install-pro */ "../app/modules/onboarding/assets/js/pages/upload-and-install-pro.js"));
var _chooseFeatures = _interopRequireDefault(__webpack_require__(/*! ./pages/choose-features */ "../app/modules/onboarding/assets/js/pages/choose-features.js"));
var _onboardingEventTracking = __webpack_require__(/*! ./utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function App() {
  // Send an AJAX request to update the database option which makes sure the Onboarding process only runs once,
  // for new Elementor sites.
  (0, _react.useEffect)(function () {
    var _elementorAppConfig;
    _onboardingEventTracking.OnboardingEventTracking.initiateCoreOnboarding();

    // This is to prevent dark theme in onboarding app from the frontend and not backend
    var darkThemeClassName = 'eps-theme-dark';
    var hasDarkMode = document.body.classList.contains(darkThemeClassName);
    if (hasDarkMode) {
      document.body.classList.remove(darkThemeClassName);
    }
    if (!((_elementorAppConfig = elementorAppConfig) !== null && _elementorAppConfig !== void 0 && (_elementorAppConfig = _elementorAppConfig.onboarding) !== null && _elementorAppConfig !== void 0 && _elementorAppConfig.onboardingAlreadyRan)) {
      var formData = new FormData();
      formData.append('_nonce', elementorCommon.config.ajax.nonce);
      formData.append('action', 'elementor_update_onboarding_option');
      fetch(elementorCommon.config.ajax.url, {
        method: 'POST',
        body: formData
      });
    }
    elementorAppConfig.return_url = elementorAppConfig.admin_url;
    return function () {
      if (hasDarkMode) {
        document.body.classList.add(darkThemeClassName);
      }
    };
  }, []);
  return /*#__PURE__*/_react.default.createElement(_context.ContextProvider, null, /*#__PURE__*/_react.default.createElement(_router.LocationProvider, {
    history: _router2.default.appHistory
  }, /*#__PURE__*/_react.default.createElement(_router.Router, null, /*#__PURE__*/_react.default.createElement(_account.default, {
    default: true
  }), /*#__PURE__*/_react.default.createElement(_helloTheme.default, {
    path: "hello"
  }), /*#__PURE__*/_react.default.createElement(_chooseFeatures.default, {
    path: "chooseFeatures"
  }), /*#__PURE__*/_react.default.createElement(_siteName.default, {
    path: "siteName"
  }), /*#__PURE__*/_react.default.createElement(_siteLogo.default, {
    path: "siteLogo"
  }), /*#__PURE__*/_react.default.createElement(_goodToGo.default, {
    path: "goodToGo"
  }), /*#__PURE__*/_react.default.createElement(_uploadAndInstallPro.default, {
    path: "uploadAndInstallPro"
  }))));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/button.js":
/*!****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/button.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Button;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _excluded = ["elRef"];
function Button(props) {
  var buttonSettings = props.buttonSettings,
    type = props.type;
  var buttonClasses = 'e-onboarding__button';
  if (type) {
    buttonClasses += " e-onboarding__button-".concat(type);
  }
  if (buttonSettings.className) {
    buttonSettings.className += ' ' + buttonClasses;
  } else {
    buttonSettings.className = buttonClasses;
  }
  var elRef = buttonSettings.elRef,
    buttonProps = (0, _objectWithoutProperties2.default)(buttonSettings, _excluded);
  if (buttonSettings.href) {
    return /*#__PURE__*/_react.default.createElement("a", (0, _extends2.default)({
      ref: elRef
    }, buttonProps), buttonSettings.text);
  }
  return /*#__PURE__*/_react.default.createElement("div", (0, _extends2.default)({
    ref: elRef
  }, buttonProps), buttonSettings.text);
}
Button.propTypes = {
  buttonSettings: PropTypes.object.isRequired,
  type: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/card.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/card.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Card;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Card(_ref) {
  var image = _ref.image,
    imageAlt = _ref.imageAlt,
    text = _ref.text,
    link = _ref.link,
    name = _ref.name,
    clickAction = _ref.clickAction,
    _ref$target = _ref.target,
    target = _ref$target === void 0 ? '_self' : _ref$target;
  var onClick = function onClick() {
    elementorCommon.events.dispatchEvent({
      event: 'starting canvas click',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        selection: name
      }
    });
    if (clickAction) {
      clickAction();
    }
  };
  return /*#__PURE__*/_react.default.createElement("a", {
    target: target,
    className: "e-onboarding__card",
    href: link,
    onClick: onClick
  }, /*#__PURE__*/_react.default.createElement("img", {
    className: "e-onboarding__card-image",
    src: image,
    alt: imageAlt
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__card-text"
  }, text));
}
Card.propTypes = {
  image: PropTypes.string.isRequired,
  imageAlt: PropTypes.string.isRequired,
  text: PropTypes.string.isRequired,
  link: PropTypes.string.isRequired,
  name: PropTypes.string.isRequired,
  clickAction: PropTypes.func,
  target: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/checklist-item.js":
/*!************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/checklist-item.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ChecklistItem;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function ChecklistItem(props) {
  return /*#__PURE__*/_react.default.createElement("li", {
    className: "e-onboarding__checklist-item"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-check-circle"
  }), props.children);
}
ChecklistItem.propTypes = {
  children: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/checklist.js":
/*!*******************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/checklist.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Checklist;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Checklist(props) {
  return /*#__PURE__*/_react.default.createElement("ul", {
    className: "e-onboarding__checklist"
  }, props.children);
}
Checklist.propTypes = {
  children: PropTypes.any.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/go-pro-popover.js":
/*!************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/go-pro-popover.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoProPopover;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _popoverDialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/popover-dialog/popover-dialog */ "../app/assets/js/ui/popover-dialog/popover-dialog.js"));
var _checklist = _interopRequireDefault(__webpack_require__(/*! ./checklist */ "../app/modules/onboarding/assets/js/components/checklist.js"));
var _checklistItem = _interopRequireDefault(__webpack_require__(/*! ./checklist-item */ "../app/modules/onboarding/assets/js/components/checklist-item.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ./button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function GoProPopover(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState;
  var upgradeButtonRef = (0, _react.useRef)(null);

  // Handle the Pro Upload popup window.
  var alreadyHaveProButtonRef = (0, _react.useCallback)(function (alreadyHaveProButton) {
    if (!alreadyHaveProButton) {
      return;
    }
    if (!state.currentStep || '' === state.currentStep) {
      return;
    }
    var existingHandler = alreadyHaveProButton._elementorProHandler;
    if (existingHandler) {
      alreadyHaveProButton.removeEventListener('click', existingHandler);
    }
    var clickHandler = function clickHandler(event) {
      event.preventDefault();
      if (!state.currentStep || '' === state.currentStep) {
        return;
      }
      var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(stepNumber, 'upgrade_already_pro');
      _onboardingEventTracking.OnboardingEventTracking.cancelDelayedNoClickEvent();
      if (stepNumber) {
        _onboardingEventTracking.OnboardingEventTracking.sendTopUpgrade(stepNumber, 'already_pro_user');
      }
      elementorCommon.events.dispatchEvent({
        event: 'already have pro',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });

      // Open the Pro Upload screen in a popup.
      window.open(alreadyHaveProButton.href + '&mode=popup', 'elementorUploadPro', "toolbar=no, menubar=no, width=728, height=531, top=100, left=100");

      // Run the callback for when the upload succeeds.
      elementorCommon.elements.$body.on('elementor/upload-and-install-pro/success', function () {
        updateState({
          hasPro: true,
          proNotice: {
            color: 'success',
            children: __('Pro is now active! You can continue.', 'elementor')
          }
        });
      });
    };
    alreadyHaveProButton._elementorProHandler = clickHandler;
    alreadyHaveProButton.addEventListener('click', clickHandler);
  }, [state.currentStep, updateState]);
  var getElProButton = {
    text: elementorAppConfig.onboarding.experiment ? __('Upgrade now', 'elementor') : __('Upgrade Now', 'elementor'),
    className: 'e-onboarding__go-pro-cta',
    target: '_blank',
    href: 'https://elementor.com/pro/?utm_source=onboarding-wizard&utm_campaign=gopro&utm_medium=wp-dash&utm_content=top-bar-dropdown&utm_term=' + elementorAppConfig.onboarding.onboardingVersion,
    tabIndex: 0,
    elRef: function elRef(buttonElement) {
      if (!buttonElement) {
        return;
      }
      upgradeButtonRef.current = buttonElement;
    },
    onClick: function onClick() {
      if (!state.currentStep || '' === state.currentStep) {
        return;
      }
      var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(stepNumber, 'upgrade_now');
      _onboardingEventTracking.OnboardingEventTracking.cancelDelayedNoClickEvent();
      if (stepNumber) {
        _onboardingEventTracking.OnboardingEventTracking.sendTopUpgrade(stepNumber, 'on_tooltip');
      }
      elementorCommon.events.dispatchEvent({
        event: 'get elementor pro',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });
    }
  };
  var targetRef = props.goProButtonRef || upgradeButtonRef;
  return /*#__PURE__*/_react.default.createElement(_popoverDialog.default, {
    targetRef: targetRef,
    wrapperClass: "e-onboarding__go-pro"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-content"
  }, /*#__PURE__*/_react.default.createElement("h2", {
    className: "e-onboarding__go-pro-title"
  }, __('Ready to Get Elementor Pro?', 'elementor')), /*#__PURE__*/_react.default.createElement(_checklist.default, null, /*#__PURE__*/_react.default.createElement(_checklistItem.default, null, __('90+ Basic & Pro widgets', 'elementor')), /*#__PURE__*/_react.default.createElement(_checklistItem.default, null, __('300+ Basic & Pro templates', 'elementor')), /*#__PURE__*/_react.default.createElement(_checklistItem.default, null, __('Premium Support', 'elementor'))), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-paragraph"
  }, __('And so much more!', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-paragraph"
  }, /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: getElProButton
  })), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-paragraph"
  }, /*#__PURE__*/_react.default.createElement("a", {
    tabIndex: "0",
    className: "e-onboarding__go-pro-already-have",
    ref: alreadyHaveProButtonRef,
    href: elementorAppConfig.onboarding.urls.uploadPro,
    rel: "opener"
  }, __('Already have Elementor Pro?', 'elementor')))));
}
GoProPopover.propTypes = {
  buttonsConfig: _propTypes.default.array.isRequired,
  goProButtonRef: _propTypes.default.object
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/footer-buttons.js":
/*!*******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/footer-buttons.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = FooterButtons;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ../button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _skipButton = _interopRequireDefault(__webpack_require__(/*! ../skip-button */ "../app/modules/onboarding/assets/js/components/skip-button.js"));
function FooterButtons(_ref) {
  var actionButton = _ref.actionButton,
    skipButton = _ref.skipButton,
    className = _ref.className;
  var classNames = 'e-onboarding__footer';
  if (className) {
    classNames += ' ' + className;
  }
  return /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: classNames
  }, actionButton && /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: actionButton,
    type: "action"
  }), skipButton && /*#__PURE__*/_react.default.createElement(_skipButton.default, {
    button: skipButton
  }));
}
FooterButtons.propTypes = {
  actionButton: PropTypes.object,
  skipButton: PropTypes.object,
  className: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/header.js":
/*!***********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/header.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Header;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _goProPopover = _interopRequireDefault(__webpack_require__(/*! ../go-pro-popover */ "../app/modules/onboarding/assets/js/components/go-pro-popover.js"));
var _headerButtons = _interopRequireDefault(__webpack_require__(/*! elementor-app/layout/header-buttons */ "../app/assets/js/layout/header-buttons.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function Header(props) {
  (0, _usePageTitle.default)({
    title: props.title
  });
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state;
  var trackXButtonExit = function trackXButtonExit() {
    var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.sendExitButtonEvent(stepNumber || state.currentStep);
  };
  var onClose = function onClose() {
    trackXButtonExit();
    elementorCommon.events.dispatchEvent({
      event: 'close modal',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
    setTimeout(function () {
      window.top.location = elementorAppConfig.admin_url;
    }, 100);
  };
  return /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "eps-app__header e-onboarding__header"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__logo-title-wrapper e-onboarding__header-logo"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eps-app__logo eicon-elementor"
  }), /*#__PURE__*/_react.default.createElement("img", {
    src: elementorCommon.config.urls.assets + 'images/logo-platform.svg',
    alt: __('Elementor Logo', 'elementor')
  })), /*#__PURE__*/_react.default.createElement(_headerButtons.default, {
    buttons: props.buttons,
    onClose: onClose
  }), !state.hasPro && /*#__PURE__*/_react.default.createElement(_goProPopover.default, {
    buttonsConfig: props.buttons,
    goProButtonRef: props.goProButtonRef
  }));
}
Header.propTypes = {
  title: _propTypes.default.string,
  buttons: _propTypes.default.arrayOf(_propTypes.default.object),
  goProButtonRef: _propTypes.default.object
};
Header.defaultProps = {
  buttons: []
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/layout.js":
/*!***********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/layout.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Layout;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _header = _interopRequireDefault(__webpack_require__(/*! ./header */ "../app/modules/onboarding/assets/js/components/layout/header.js"));
var _progressBar = _interopRequireDefault(__webpack_require__(/*! ../progress-bar/progress-bar */ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _connect = _interopRequireDefault(__webpack_require__(/*! ../../utils/connect */ "../app/modules/onboarding/assets/js/utils/connect.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function getCurrentStepForTracking(pageId, currentStep) {
  return pageId || currentStep || 'account';
}
function shouldResetupButtonTracking(buttonRef, pageId, currentStep) {
  if (!buttonRef) {
    return false;
  }
  var currentStepForTracking = getCurrentStepForTracking(pageId, currentStep);
  var currentTrackedStep = buttonRef.dataset.onboardingStep;
  return currentTrackedStep !== currentStepForTracking;
}
function Layout(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState;
  var stepNumber = (0, _react.useMemo)(function () {
    return _onboardingEventTracking.OnboardingEventTracking.getStepNumber(props.pageId);
  }, [props.pageId]);
  var goProButtonRef = (0, _react.useRef)();
  var setupTopbarUpgradeTracking = (0, _react.useCallback)(function (buttonElement) {
    if (!buttonElement) {
      return;
    }
    var currentStep = getCurrentStepForTracking(props.pageId, state.currentStep);
    goProButtonRef.current = buttonElement;
    return _onboardingEventTracking.OnboardingEventTracking.setupSingleUpgradeButton(buttonElement, currentStep);
  }, [state.currentStep, props.pageId]);
  var handleTopbarConnectSuccess = (0, _react.useCallback)(function () {
    updateState({
      isLibraryConnected: true
    });
  }, [updateState]);
  (0, _react.useEffect)(function () {
    // Send modal load event for current step.
    elementorCommon.events.dispatchEvent({
      event: 'modal load',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: props.pageId,
        user_state: elementorCommon.config.library_connect.is_connected ? 'logged' : 'anon'
      }
    });
    if (goProButtonRef.current) {
      setupTopbarUpgradeTracking(goProButtonRef.current);
    }
    updateState({
      currentStep: props.pageId,
      nextStep: props.nextStep || '',
      proNotice: null
    });
  }, [setupTopbarUpgradeTracking, stepNumber, props.pageId, props.nextStep, updateState]);
  (0, _react.useEffect)(function () {
    if (shouldResetupButtonTracking(goProButtonRef.current, props.pageId, state.currentStep)) {
      setupTopbarUpgradeTracking(goProButtonRef.current);
    }
  }, [state.currentStep, props.pageId, setupTopbarUpgradeTracking]);
  var headerButtons = [],
    createAccountButton = {
      id: 'create-account',
      text: __('Create Account', 'elementor'),
      hideText: false,
      elRef: (0, _react.useRef)(),
      url: elementorAppConfig.onboarding.urls.signUp + elementorAppConfig.onboarding.utms.connectTopBar,
      target: '_blank',
      rel: 'opener',
      onClick: function onClick() {
        _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('CREATE_MY_ACCOUNT', {
          currentStep: stepNumber,
          createAccountClicked: 'topbar'
        });
        elementorCommon.events.dispatchEvent({
          event: 'create account',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            source: 'header'
          }
        });
      }
    };
  if (state.isLibraryConnected) {
    headerButtons.push({
      id: 'my-elementor',
      text: __('My Elementor', 'elementor'),
      hideText: false,
      icon: 'eicon-user-circle-o',
      url: 'https://my.elementor.com/websites/?utm_source=onboarding-wizard&utm_medium=wp-dash&utm_campaign=my-account&utm_content=top-bar&utm_term=' + elementorAppConfig.onboarding.onboardingVersion,
      target: '_blank',
      onClick: function onClick() {
        elementorCommon.events.dispatchEvent({
          event: 'my elementor click',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            source: 'header'
          }
        });
      }
    });
  } else {
    headerButtons.push(createAccountButton);
  }
  if (!state.hasPro) {
    headerButtons.push({
      id: 'go-pro',
      text: __('Upgrade', 'elementor'),
      hideText: false,
      className: 'eps-button__go-pro-btn',
      url: 'https://elementor.com/pro/?utm_source=onboarding-wizard&utm_campaign=gopro&utm_medium=wp-dash&utm_content=top-bar&utm_term=' + elementorAppConfig.onboarding.onboardingVersion,
      target: '_blank',
      elRef: setupTopbarUpgradeTracking,
      onClick: function onClick() {
        var currentStep = getCurrentStepForTracking(props.pageId, state.currentStep);
        var currentStepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(currentStep);
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(currentStepNumber, 'upgrade_topbar');
        elementorCommon.events.dispatchEvent({
          event: 'go pro',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: currentStep
          }
        });
      }
    });
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__lightbox"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app e-onboarding"
  }, !state.isLibraryConnected && /*#__PURE__*/_react.default.createElement(_connect.default, {
    buttonRef: createAccountButton.elRef,
    successCallback: handleTopbarConnectSuccess
  }), /*#__PURE__*/_react.default.createElement(_header.default, {
    title: __('Getting Started', 'elementor'),
    buttons: headerButtons,
    goProButtonRef: goProButtonRef
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: 'eps-app__main e-onboarding__page-' + props.pageId
  }, /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-onboarding__content"
  }, /*#__PURE__*/_react.default.createElement(_progressBar.default, null), props.children))));
}
Layout.propTypes = {
  pageId: _propTypes.default.string.isRequired,
  nextStep: _propTypes.default.string,
  className: _propTypes.default.string,
  children: _propTypes.default.any.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js":
/*!************************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/page-content-layout.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = PageContentLayout;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _notice = _interopRequireDefault(__webpack_require__(/*! ../notice */ "../app/modules/onboarding/assets/js/components/notice.js"));
var _footerButtons = _interopRequireDefault(__webpack_require__(/*! ./footer-buttons */ "../app/modules/onboarding/assets/js/components/layout/footer-buttons.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function PageContentLayout(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state;
  var printNotices = function printNotices() {
    return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, props.noticeState && /*#__PURE__*/_react.default.createElement(_notice.default, {
      noticeState: props.noticeState
    }), state.proNotice && /*#__PURE__*/_react.default.createElement(_notice.default, {
      noticeState: state.proNotice
    }));
  };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "e-onboarding__page-content"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-start"
  }, /*#__PURE__*/_react.default.createElement("h1", {
    className: "e-onboarding__page-content-section-title"
  }, props.title, props.secondLineTitle && /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("br", null), props.secondLineTitle)), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-section-text"
  }, props.children)), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-end"
  }, /*#__PURE__*/_react.default.createElement("img", {
    src: props.image,
    alt: "Information"
  }))), props.noticeState && /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__notice-container"
  }, props.noticeState || state.proNotice ? printNotices() : /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__notice-empty-spacer"
  })), /*#__PURE__*/_react.default.createElement(_footerButtons.default, {
    actionButton: props.actionButton,
    skipButton: props.skipButton
  }));
}
PageContentLayout.propTypes = {
  title: PropTypes.string,
  secondLineTitle: PropTypes.string,
  children: PropTypes.any,
  image: PropTypes.string,
  actionButton: PropTypes.object,
  skipButton: PropTypes.object,
  noticeState: PropTypes.any
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/message.js":
/*!*****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/message.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Message;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function Message(_ref) {
  var tier = _ref.tier;
  /* Translators: %s: Plan name */
  var translatedString = __('Based on the features you chose, we recommend the %s plan, or higher', 'elementor');
  var _translatedString$spl = translatedString.split('%s'),
    _translatedString$spl2 = (0, _slicedToArray2.default)(_translatedString$spl, 2),
    messageFirstPart = _translatedString$spl2[0],
    messageSecondPart = _translatedString$spl2[1];
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, messageFirstPart, /*#__PURE__*/_react.default.createElement("strong", null, tier), messageSecondPart);
}
Message.propTypes = {
  tier: PropTypes.string.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/notice.js":
/*!****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/notice.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Notice;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Notice(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__notice e-onboarding__notice--".concat(props.noticeState.type)
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: props.noticeState.icon
  }), /*#__PURE__*/_react.default.createElement("span", {
    className: "e-onboarding__notice-text"
  }, props.noticeState.message));
}
Notice.propTypes = {
  noticeState: PropTypes.object
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar-item.js":
/*!****************************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/progress-bar/progress-bar-item.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ProgressBarItem;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ProgressBarItem(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    stepCompleted = 'completed' === state.steps[props.id],
    stepSkipped = 'skipped' === state.steps[props.id];
  var itemClasses = 'e-onboarding__progress-bar-item';
  if (props.id === state.currentStep) {
    itemClasses += ' e-onboarding__progress-bar-item--active';
  } else if (stepCompleted) {
    itemClasses += ' e-onboarding__progress-bar-item--completed';
  } else if (stepSkipped) {
    itemClasses += ' e-onboarding__progress-bar-item--skipped';
  }
  return (
    /*#__PURE__*/
    // eslint-disable-next-line jsx-a11y/click-events-have-key-events, jsx-a11y/no-static-element-interactions
    _react.default.createElement("div", {
      onClick: props.onClick,
      className: itemClasses
    }, /*#__PURE__*/_react.default.createElement("div", {
      className: "e-onboarding__progress-bar-item-icon"
    }, stepCompleted ? /*#__PURE__*/_react.default.createElement("i", {
      className: "eicon-check"
    }) : props.index + 1), props.title)
  );
}
ProgressBarItem.propTypes = {
  index: PropTypes.number.isRequired,
  id: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,
  route: PropTypes.string,
  onClick: PropTypes.func
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar.js":
/*!***********************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/progress-bar/progress-bar.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ProgressBar;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _progressBarItem = _interopRequireDefault(__webpack_require__(/*! ./progress-bar-item */ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar-item.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ProgressBar() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    navigate = (0, _router.useNavigate)(),
    progressBarItemsConfig = [{
      id: 'account',
      title: __('Elementor Account', 'elementor'),
      route: 'account'
    }];

  // If hello theme is already activated when onboarding starts, don't show this step in the onboarding.
  if (!elementorAppConfig.onboarding.helloActivated) {
    progressBarItemsConfig.push({
      id: 'hello',
      title: __('Hello Biz Theme', 'elementor'),
      route: 'hello'
    });
  }
  if (elementorAppConfig.onboarding.experiment) {
    progressBarItemsConfig.push({
      id: 'chooseFeatures',
      title: __('Choose Features', 'elementor'),
      route: 'chooseFeatures'
    });
  } else {
    progressBarItemsConfig.push({
      id: 'siteName',
      title: __('Site Name', 'elementor'),
      route: 'site-name'
    }, {
      id: 'siteLogo',
      title: __('Site Logo', 'elementor'),
      route: 'site-logo'
    });
  }
  progressBarItemsConfig.push({
    id: 'goodToGo',
    title: __('Good to Go', 'elementor'),
    route: 'good-to-go'
  });
  var progressBarItems = progressBarItemsConfig.map(function (itemConfig, index) {
    itemConfig.index = index;
    if (state.steps[itemConfig.id]) {
      itemConfig.onClick = function () {
        var currentStepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
        var nextStepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(itemConfig.id);
        if (4 === currentStepNumber) {
          _onboardingEventTracking.OnboardingEventTracking.trackStepAction(4, 'stepper_clicks', {
            from_step: currentStepNumber,
            to_step: nextStepNumber
          });
          _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(4);
        }
        elementorCommon.events.dispatchEvent({
          event: 'step click',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            next_step: itemConfig.id
          }
        });
        navigate('/onboarding/' + itemConfig.id);
      };
    }
    return /*#__PURE__*/_react.default.createElement(_progressBarItem.default, (0, _extends2.default)({
      key: itemConfig.id
    }, itemConfig));
  });
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__progress-bar"
  }, progressBarItems);
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/skip-button.js":
/*!*********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/skip-button.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SkipButton;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _button = _interopRequireDefault(__webpack_require__(/*! ./button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function SkipButton(props) {
  var button = props.button,
    className = props.className,
    _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    navigate = (0, _router.useNavigate)(),
    skipStep = function skipStep() {
      var mutatedState = JSON.parse(JSON.stringify(state));
      mutatedState.steps[state.currentStep] = 'skipped';
      updateState(mutatedState);
      if (state.nextStep) {
        navigate('onboarding/' + state.nextStep);
      }
    },
    action = button.action || skipStep;

  // Make sure the 'action' prop doesn't get printed on the button markup which causes an error.
  delete button.action;

  // Handle both href and non-href skip buttons properly
  button.onClick = function (event) {
    var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.trackStepAction(stepNumber, 'skipped');
    _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(stepNumber);
    _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('SKIP', {
      currentStep: stepNumber
    });
    if (4 === stepNumber) {
      _onboardingEventTracking.OnboardingEventTracking.storeExitEventForLater('step4_skip_button', stepNumber);
    }
    elementorCommon.events.dispatchEvent({
      event: 'skip',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
    if (button.href) {
      event.preventDefault();
      setTimeout(function () {
        window.location.href = button.href;
      }, 100);
    } else {
      action();
    }
  };
  return /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: button,
    className: className,
    type: "skip"
  });
}
SkipButton.propTypes = {
  button: PropTypes.object.isRequired,
  className: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/context/context.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/context/context.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ContextProvider = ContextProvider;
exports.OnboardingContext = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var OnboardingContext = exports.OnboardingContext = (0, _react.createContext)({});
function ContextProvider(props) {
  var onboardingConfig = elementorAppConfig.onboarding,
    initialState = {
      // eslint-disable-next-line camelcase
      hasPro: elementorAppConfig.hasPro,
      isLibraryConnected: onboardingConfig.isLibraryConnected,
      isHelloThemeInstalled: onboardingConfig.helloInstalled,
      isHelloThemeActivated: onboardingConfig.helloActivated,
      siteName: onboardingConfig.siteName,
      siteLogo: onboardingConfig.siteLogo,
      proNotice: '',
      currentStep: '',
      nextStep: '',
      steps: {
        account: false,
        hello: false,
        chooseFeatures: false,
        siteName: false,
        siteLogo: false,
        goodToGo: false
      }
    },
    _useState = (0, _react.useState)(initialState),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    state = _useState2[0],
    setState = _useState2[1],
    updateState = (0, _react.useCallback)(function (newState) {
      setState(function (prev) {
        return _objectSpread(_objectSpread({}, prev), newState);
      });
    }, [setState]),
    getStateObjectToUpdate = function getStateObjectToUpdate(stateObject, mainChangedPropertyKey, subChangedPropertyKey, subChangedPropertyValue) {
      var mutatedStateCopy = JSON.parse(JSON.stringify(stateObject));
      mutatedStateCopy[mainChangedPropertyKey][subChangedPropertyKey] = subChangedPropertyValue;
      return mutatedStateCopy;
    };
  return /*#__PURE__*/_react.default.createElement(OnboardingContext.Provider, {
    value: {
      state: state,
      setState: setState,
      updateState: updateState,
      getStateObjectToUpdate: getStateObjectToUpdate
    }
  }, props.children);
}
ContextProvider.propTypes = {
  children: PropTypes.any
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/account.js":
/*!************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/account.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Account;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _connect = _interopRequireDefault(__webpack_require__(/*! ../utils/connect */ "../app/modules/onboarding/assets/js/utils/connect.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
var _utils = __webpack_require__(/*! ../utils/utils */ "../app/modules/onboarding/assets/js/utils/utils.js");
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function Account() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    noticeState = _useState2[0],
    setNoticeState = _useState2[1],
    nextStep = getNextStep(),
    navigate = (0, _router.useNavigate)(),
    pageId = 'account',
    actionButtonRef = (0, _react.useRef)(),
    alreadyHaveAccountLinkRef = (0, _react.useRef)();
  (0, _react.useEffect)(function () {
    if (!state.isLibraryConnected) {
      var _elementorCommon$even;
      (0, _utils.safeDispatchEvent)('view_account_setup', {
        location: 'plugin_onboarding',
        trigger: ((_elementorCommon$even = elementorCommon.eventsManager) === null || _elementorCommon$even === void 0 || (_elementorCommon$even = _elementorCommon$even.config) === null || _elementorCommon$even === void 0 || (_elementorCommon$even = _elementorCommon$even.triggers) === null || _elementorCommon$even === void 0 ? void 0 : _elementorCommon$even.pageLoaded) || 'page_loaded',
        step_number: 1,
        step_name: 'account_setup',
        is_library_connected: (state === null || state === void 0 ? void 0 : state.isLibraryConnected) || false
      });
    }
    _onboardingEventTracking.OnboardingEventTracking.setupAllUpgradeButtons(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(1);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip setup', 'elementor'),
      action: function action() {
        var _elementorCommon$even2;
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(1, 'skip');
        _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('SKIP', {
          currentStep: 1
        });
        _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(1);
        (0, _utils.safeDispatchEvent)('skip_setup', {
          location: 'plugin_onboarding',
          trigger: ((_elementorCommon$even2 = elementorCommon.eventsManager) === null || _elementorCommon$even2 === void 0 || (_elementorCommon$even2 = _elementorCommon$even2.config) === null || _elementorCommon$even2 === void 0 || (_elementorCommon$even2 = _elementorCommon$even2.triggers) === null || _elementorCommon$even2 === void 0 ? void 0 : _elementorCommon$even2.click) || 'click',
          step_number: 1,
          step_name: 'account_setup'
        });
        updateState(getStateObjectToUpdate(state, 'steps', pageId, 'skipped'));
        navigate('onboarding/' + nextStep);
      }
    };
  }
  var pageTexts = {};
  if (state.isLibraryConnected) {
    pageTexts = {
      firstLine: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('To get the most out of Elementor, we\'ll help you take your', 'elementor'), " ", /*#__PURE__*/_react.default.createElement("br", null), " ", __('first steps:', 'elementor')),
      listItems: elementorAppConfig.onboarding.experiment ? [__('Set your site\'s theme', 'elementor'), __('Choose additional features', 'elementor'), __('Choose how to start creating', 'elementor')] : [__('Set your site\'s theme', 'elementor'), __('Give your site a name & logo', 'elementor'), __('Choose how to start creating', 'elementor')]
    };
  } else {
    pageTexts = elementorAppConfig.onboarding.experiment ? {
      firstLine: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('To get the most of Elementor, we\'ll connect your account.', 'elementor'), "  ", /*#__PURE__*/_react.default.createElement("br", null), " ", __('Then you can:', 'elementor')),
      listItems: [__('Access dozens of professionally designed templates', 'elementor'), __('Manage all your sites from the My Elementor dashboard', 'elementor'), __('Unlock tools that streamline your workflow and site setup', 'elementor')]
    } : {
      firstLine: __('To get the most out of Elementor, we’ll connect your account.', 'elementor') + ' ' + __('Then you can:', 'elementor'),
      listItems: [__('Choose from countless professional templates', 'elementor'), __('Manage your site with our handy dashboard', 'elementor'), __('Take part in the community forum, share & grow together', 'elementor')]
    };
  }

  // If the user is not connected, the on-click action is handled by the <Connect> component, so there is no onclick
  // property.
  var actionButton = {
    role: 'button'
  };
  if (state.isLibraryConnected) {
    actionButton.text = __('Let’s do it', 'elementor');
    actionButton.onClick = function () {
      elementorCommon.events.dispatchEvent({
        event: 'next',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });
      updateState(getStateObjectToUpdate(state, 'steps', pageId, 'completed'));
      navigate('onboarding/' + nextStep);
    };
  } else {
    actionButton.text = __('Start setup', 'elementor');
    actionButton.href = elementorAppConfig.onboarding.urls.signUp + elementorAppConfig.onboarding.utms.connectCta;
    actionButton.ref = actionButtonRef;
    actionButton.onClick = function () {
      var _elementorCommon$even3;
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(1, 'create');
      _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('CREATE_MY_ACCOUNT', {
        currentStep: 1,
        createAccountClicked: 'main_cta'
      });
      (0, _utils.safeDispatchEvent)('new_account_connect', {
        location: 'plugin_onboarding',
        trigger: ((_elementorCommon$even3 = elementorCommon.eventsManager) === null || _elementorCommon$even3 === void 0 || (_elementorCommon$even3 = _elementorCommon$even3.config) === null || _elementorCommon$even3 === void 0 || (_elementorCommon$even3 = _elementorCommon$even3.triggers) === null || _elementorCommon$even3 === void 0 ? void 0 : _elementorCommon$even3.click) || 'click',
        step_number: 1,
        step_name: 'account_setup',
        button_text: 'Start setup'
      });
    };
  }
  var connectSuccessCallback = function connectSuccessCallback() {
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
    stateToUpdate.isLibraryConnected = true;
    updateState(stateToUpdate);
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'success',
        action: 'connect account'
      }
    });
    setNoticeState({
      type: 'success',
      icon: 'eicon-check-circle-o',
      message: 'Alrighty - your account is connected.'
    });
    _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(1);
    navigate('onboarding/' + nextStep);
  };
  function getNextStep() {
    if (!state.isHelloThemeActivated) {
      return 'hello';
    }
    return elementorAppConfig.onboarding.experiment ? 'chooseFeatures' : 'siteName';
  }
  var connectFailureCallback = function connectFailureCallback() {
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'failure',
        action: 'connect account'
      }
    });
    _onboardingEventTracking.OnboardingEventTracking.sendConnectionFailureEvents();
    setNoticeState({
      type: 'error',
      icon: 'eicon-warning',
      message: __('Oops, the connection failed. Try again.', 'elementor')
    });
    navigate('onboarding/' + nextStep);
  };
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Account.svg',
    title: elementorAppConfig.onboarding.experiment ? __('You\'re here!', 'elementor') : __('You\'re here! Let\'s set things up.', 'elementor'),
    secondLineTitle: elementorAppConfig.onboarding.experiment ? __(' Let\'s get connected.', 'elementor') : '',
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, actionButton.ref && !state.isLibraryConnected && /*#__PURE__*/_react.default.createElement(_connect.default, {
    buttonRef: actionButton.ref,
    successCallback: function successCallback(event, data) {
      return connectSuccessCallback(event, data);
    },
    errorCallback: connectFailureCallback
  }), /*#__PURE__*/_react.default.createElement("span", null, pageTexts.firstLine), /*#__PURE__*/_react.default.createElement("ul", null, pageTexts.listItems.map(function (listItem, index) {
    return /*#__PURE__*/_react.default.createElement("li", {
      key: 'listItem' + index
    }, listItem);
  }))), !state.isLibraryConnected && /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__footnote"
  }, /*#__PURE__*/_react.default.createElement("p", null, __('Already have an account?', 'elementor') + ' ', /*#__PURE__*/_react.default.createElement("a", {
    ref: alreadyHaveAccountLinkRef,
    href: elementorAppConfig.onboarding.urls.connect + elementorAppConfig.onboarding.utms.connectCtaLink,
    onClick: function onClick() {
      var _elementorCommon$even4;
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(1, 'connect');
      _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('STEP1_CLICKED_CONNECT', {
        currentStep: state.currentStep
      });
      (0, _utils.safeDispatchEvent)('existing_account_connect', {
        location: 'plugin_onboarding',
        trigger: ((_elementorCommon$even4 = elementorCommon.eventsManager) === null || _elementorCommon$even4 === void 0 || (_elementorCommon$even4 = _elementorCommon$even4.config) === null || _elementorCommon$even4 === void 0 || (_elementorCommon$even4 = _elementorCommon$even4.triggers) === null || _elementorCommon$even4 === void 0 ? void 0 : _elementorCommon$even4.click) || 'click',
        step_number: 1,
        step_name: 'account_setup',
        button_text: 'Click here to connect'
      });
    }
  }, __('Click here to connect', 'elementor'))), /*#__PURE__*/_react.default.createElement(_connect.default, {
    buttonRef: alreadyHaveAccountLinkRef,
    successCallback: connectSuccessCallback,
    errorCallback: connectFailureCallback
  })));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/choose-features.js":
/*!********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/choose-features.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ChooseFeatures;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _message = _interopRequireDefault(__webpack_require__(/*! ../components/message */ "../app/modules/onboarding/assets/js/components/message.js"));
var _utils = __webpack_require__(/*! ../utils/utils */ "../app/modules/onboarding/assets/js/utils/utils.js");
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
var _useButtonAction2 = _interopRequireDefault(__webpack_require__(/*! ../utils/use-button-action */ "../app/modules/onboarding/assets/js/utils/use-button-action.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ChooseFeatures() {
  var _useAjax = (0, _useAjax2.default)(),
    setAjax = _useAjax.setAjax,
    tiers = {
      advanced: __('Advanced', 'elementor'),
      essential: __('Essential', 'elementor')
    },
    _useState = (0, _react.useState)({
      essential: [],
      advanced: []
    }),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    selectedFeatures = _useState2[0],
    setSelectedFeatures = _useState2[1],
    _useState3 = (0, _react.useState)(tiers.essential),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    tierName = _useState4[0],
    setTierName = _useState4[1],
    pageId = 'chooseFeatures',
    nextStep = 'goodToGo',
    _useButtonAction = (0, _useButtonAction2.default)(pageId, nextStep),
    state = _useButtonAction.state,
    handleAction = _useButtonAction.handleAction,
    actionButton = {
      text: __('Upgrade Now', 'elementor'),
      href: elementorAppConfig.onboarding.urls.upgrade,
      target: '_blank',
      onClick: function onClick() {
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'pro_features_checked', {
          features: _onboardingEventTracking.OnboardingEventTracking.extractSelectedFeatureKeys(selectedFeatures)
        });
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'upgrade_now', {
          pro_features_checked: _onboardingEventTracking.OnboardingEventTracking.extractSelectedFeatureKeys(selectedFeatures)
        });
        elementorCommon.events.dispatchEvent({
          event: 'next',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep
          }
        });
        _onboardingEventTracking.OnboardingEventTracking.sendUpgradeNowStep3(selectedFeatures, state.currentStep);
        _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(3);
        setAjax({
          data: {
            action: 'elementor_save_onboarding_features',
            data: JSON.stringify({
              features: selectedFeatures
            })
          }
        });
        handleAction('completed');
      }
    };
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip', 'elementor'),
      action: function action() {
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'pro_features_checked', {
          features: _onboardingEventTracking.OnboardingEventTracking.extractSelectedFeatureKeys(selectedFeatures)
        });
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'skipped');
        setAjax({
          data: {
            action: 'elementor_save_onboarding_features',
            data: JSON.stringify({
              features: selectedFeatures
            })
          }
        });
        handleAction('skipped');
      }
    };
  }
  if (!isFeatureSelected(selectedFeatures)) {
    actionButton.className = 'e-onboarding__button--disabled';
  }
  (0, _react.useEffect)(function () {
    if (selectedFeatures.advanced.length > 0) {
      setTierName(tiers.advanced);
    } else {
      setTierName(tiers.essential);
    }
  }, [selectedFeatures, tiers.advanced, tiers.essential]);
  (0, _react.useEffect)(function () {
    _onboardingEventTracking.OnboardingEventTracking.setupAllUpgradeButtons(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(3);
  }, [state.currentStep]);
  function isFeatureSelected(features) {
    return !!features.advanced.length || !!features.essential.length;
  }
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Setup.svg',
    title: __('Elevate your website with additional Pro features.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton
  }, /*#__PURE__*/_react.default.createElement("p", null, __('Which Elementor Pro features do you need to bring your creative vision to life?', 'elementor')), /*#__PURE__*/_react.default.createElement("form", {
    className: "e-onboarding__choose-features-section"
  }, _utils.options.map(function (option, index) {
    var itemId = "".concat(option.plan, "-").concat(index);
    return /*#__PURE__*/_react.default.createElement("label", {
      key: itemId,
      className: "e-onboarding__choose-features-section__label",
      htmlFor: itemId
    }, /*#__PURE__*/_react.default.createElement("input", {
      className: "e-onboarding__choose-features-section__checkbox",
      type: "checkbox",
      onChange: function onChange(event) {
        return (0, _utils.setSelectedFeatureList)({
          checked: event.currentTarget.checked,
          id: event.target.value,
          text: option.text,
          selectedFeatures: selectedFeatures,
          setSelectedFeatures: setSelectedFeatures
        });
      },
      id: itemId,
      value: itemId
    }), option.text);
  })), /*#__PURE__*/_react.default.createElement("p", {
    className: "e-onboarding__choose-features-section__message"
  }, isFeatureSelected(selectedFeatures) && /*#__PURE__*/_react.default.createElement(_message.default, {
    tier: tierName
  }))));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/good-to-go.js":
/*!***************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/good-to-go.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoodToGo;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _card = _interopRequireDefault(__webpack_require__(/*! ../components/card */ "../app/modules/onboarding/assets/js/components/card.js"));
var _footerButtons = _interopRequireDefault(__webpack_require__(/*! ../components/layout/footer-buttons */ "../app/modules/onboarding/assets/js/components/layout/footer-buttons.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function GoodToGo() {
  var pageId = 'goodToGo',
    skipButton = {
      text: __('Skip', 'elementor'),
      href: elementorAppConfig.onboarding.urls.createNewPage
    },
    kitLibraryLink = elementorAppConfig.onboarding.urls.kitLibrary + '&referrer=onboarding';
  (0, _react.useEffect)(function () {
    _onboardingEventTracking.OnboardingEventTracking.checkAndSendReturnToStep4();
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(4);
  }, []);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId
  }, /*#__PURE__*/_react.default.createElement("h1", {
    className: "e-onboarding__page-content-section-title"
  }, elementorAppConfig.onboarding.experiment ? __('Welcome aboard! What\'s next?', 'elementor') : __('That\'s a wrap! What\'s next?', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-section-text"
  }, __('There are three ways to get started with Elementor:', 'elementor')), /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "e-onboarding__cards-grid e-onboarding__page-content"
  }, /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "blank",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Blank_Canvas.svg',
    imageAlt: __('Click here to create a new page and open it in Elementor Editor', 'elementor'),
    text: __('Edit a blank canvas with the Elementor Editor', 'elementor'),
    link: elementorAppConfig.onboarding.urls.createNewPage,
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('blank_canvas');
    }
  }), /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "template",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Library.svg',
    imageAlt: __('Click here to go to Elementor\'s Website Templates', 'elementor'),
    text: __('Choose a professionally-designed template or import your own', 'elementor'),
    link: kitLibraryLink,
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('kit_library');

      // The location is reloaded to make sure the Kit Library's state is re-created.
      location.href = kitLibraryLink;
      location.reload();
    }
  }), /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "site-planner",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Site_Planner.svg',
    imageAlt: __('Click here to go to Elementor\'s Site Planner', 'elementor'),
    text: __('Create a professional site in minutes using AI', 'elementor'),
    link: elementorAppConfig.onboarding.urls.sitePlanner,
    target: "_blank",
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('site_planner');
    }
  })), /*#__PURE__*/_react.default.createElement(_footerButtons.default, {
    skipButton: _objectSpread(_objectSpread({}, skipButton), {}, {
      target: '_self'
    }),
    className: "e-onboarding__good-to-go-footer"
  }));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/hello-theme.js":
/*!****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/hello-theme.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = HelloTheme;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* eslint-disable @wordpress/i18n-ellipsis */

function HelloTheme() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useAjax = (0, _useAjax2.default)(),
    activateHelloThemeAjaxState = _useAjax.ajaxState,
    setActivateHelloThemeAjaxState = _useAjax.setAjax,
    _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    helloInstalledInOnboarding = _useState2[0],
    setHelloInstalledInOnboarding = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isInstalling = _useState4[0],
    setIsInstalling = _useState4[1],
    noticeStateSuccess = {
      type: 'success',
      icon: 'eicon-check-circle-o',
      message: __('Your site’s got Hello theme. High-five!', 'elementor')
    },
    _useState5 = (0, _react.useState)(state.isHelloThemeActivated ? noticeStateSuccess : null),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    noticeState = _useState6[0],
    setNoticeState = _useState6[1],
    _useState7 = (0, _react.useState)([]),
    _useState8 = (0, _slicedToArray2.default)(_useState7, 2),
    activeTimeouts = _useState8[0],
    setActiveTimeouts = _useState8[1],
    continueWithHelloThemeText = state.isHelloThemeActivated ? __('Next', 'elementor') : __('Continue with Hello Biz Theme', 'elementor'),
    _useState9 = (0, _react.useState)(continueWithHelloThemeText),
    _useState0 = (0, _slicedToArray2.default)(_useState9, 2),
    actionButtonText = _useState0[0],
    setActionButtonText = _useState0[1],
    navigate = (0, _router.useNavigate)(),
    pageId = 'hello',
    nextStep = elementorAppConfig.onboarding.experiment ? 'chooseFeatures' : 'siteName',
    goToNextScreen = function goToNextScreen() {
      return navigate('onboarding/' + nextStep);
    };

  /**
   * Setup
   *
   * If Hello Theme is already activated when onboarding starts, This screen is unneeded and is marked as 'completed'
   * and skipped.
   */
  (0, _react.useEffect)(function () {
    if (!helloInstalledInOnboarding && state.isHelloThemeActivated) {
      var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
      updateState(stateToUpdate);
      goToNextScreen();
    }
    _onboardingEventTracking.OnboardingEventTracking.setupAllUpgradeButtons(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(2);
  }, []);
  var resetScreenContent = function resetScreenContent() {
    // Clear any active timeouts for changing the action button text during installation.
    activeTimeouts.forEach(function (timeoutID) {
      return clearTimeout(timeoutID);
    });
    setActiveTimeouts([]);
    setIsInstalling(false);
    setActionButtonText(continueWithHelloThemeText);
  };

  /**
   * Callbacks
   */
  var onHelloThemeActivationSuccess = (0, _react.useCallback)(function () {
    setIsInstalling(false);
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'success',
        action: 'hello theme activation'
      }
    });
    setNoticeState(noticeStateSuccess);
    setActionButtonText(__('Next', 'elementor'));
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
    stateToUpdate.isHelloThemeActivated = true;
    updateState(stateToUpdate);
    setHelloInstalledInOnboarding(true);
    _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(2);
    goToNextScreen();
  }, []);
  var onErrorInstallHelloTheme = function onErrorInstallHelloTheme() {
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'failure',
        action: 'hello theme install'
      }
    });
    setNoticeState({
      type: 'error',
      icon: 'eicon-warning',
      message: __('There was a problem installing Hello Biz Theme.', 'elementor')
    });
    resetScreenContent();
  };
  var activateHelloTheme = function activateHelloTheme() {
    setIsInstalling(true);
    updateState({
      isHelloThemeInstalled: true
    });
    setActivateHelloThemeAjaxState({
      data: {
        action: 'elementor_activate_hello_theme'
      }
    });
  };
  var installHelloTheme = function installHelloTheme() {
    if (!isInstalling) {
      setIsInstalling(true);
    }
    wp.updates.ajax('install-theme', {
      slug: 'hello-biz',
      success: function success() {
        return activateHelloTheme();
      },
      error: function error() {
        return onErrorInstallHelloTheme();
      }
    });
  };
  var sendNextButtonEvent = function sendNextButtonEvent() {
    elementorCommon.events.dispatchEvent({
      event: 'next',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
  };

  /**
   * Action Button
   */
  var actionButton = {
    text: actionButtonText,
    role: 'button'
  };
  if (isInstalling) {
    actionButton.className = 'e-onboarding__button--processing';
  }
  if (state.isHelloThemeActivated) {
    actionButton.onClick = function () {
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(2, 'continue_hello_biz');
      sendNextButtonEvent();
      _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(2);
      goToNextScreen();
    };
  } else {
    actionButton.onClick = function () {
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(2, 'continue_hello_biz');
      _onboardingEventTracking.OnboardingEventTracking.sendHelloBizContinue(state.currentStep);
      sendNextButtonEvent();
      if (state.isHelloThemeInstalled && !state.isHelloThemeActivated) {
        activateHelloTheme();
      } else if (!state.isHelloThemeInstalled) {
        installHelloTheme();
      } else {
        _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(2);
        goToNextScreen();
      }
    };
  }

  /**
   * Skip Button
   */
  var skipButton = {};
  if (isInstalling) {
    skipButton.className = 'e-onboarding__button-skip--disabled';
  }
  if ('completed' !== state.steps[pageId]) {
    skipButton.text = __('Skip', 'elementor');
  }

  /**
   * Set timeouts for updating the 'Next' button text if the Hello Theme installation is taking too long.
   */
  (0, _react.useEffect)(function () {
    if (isInstalling) {
      setActionButtonText(/*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
        className: "eicon-loading eicon-animation-spin",
        "aria-hidden": "true"
      })));
    }
    var actionTextTimeouts = [];
    var timeout4 = setTimeout(function () {
      if (!isInstalling) {
        return;
      }
      setActionButtonText(/*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
        className: "eicon-loading eicon-animation-spin",
        "aria-hidden": "true"
      }), /*#__PURE__*/_react.default.createElement("span", {
        className: "e-onboarding__action-button-text"
      }, __('Hold on, this can take a minute...', 'elementor'))));
    }, 4000);
    actionTextTimeouts.push(timeout4);
    var timeout30 = setTimeout(function () {
      if (!isInstalling) {
        return;
      }
      setActionButtonText(/*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
        className: "eicon-loading eicon-animation-spin",
        "aria-hidden": "true"
      }), /*#__PURE__*/_react.default.createElement("span", {
        className: "e-onboarding__action-button-text"
      }, __('Okay, now we\'re really close...', 'elementor'))));
    }, 30000);
    actionTextTimeouts.push(timeout30);
    setActiveTimeouts(actionTextTimeouts);
  }, [isInstalling]);
  (0, _react.useEffect)(function () {
    if ('initial' !== activateHelloThemeAjaxState.status) {
      var _activateHelloThemeAj;
      if ('success' === activateHelloThemeAjaxState.status && (_activateHelloThemeAj = activateHelloThemeAjaxState.response) !== null && _activateHelloThemeAj !== void 0 && _activateHelloThemeAj.helloThemeActivated) {
        onHelloThemeActivationSuccess();
      } else if ('error' === activateHelloThemeAjaxState.status) {
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'hello theme activation'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: __('There was a problem activating Hello Biz Theme.', 'elementor')
        });

        // Clear any active timeouts for changing the action button text during installation.
        resetScreenContent();
      }
    }
  }, [activateHelloThemeAjaxState.status]);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Hello_Biz.svg',
    title: __('Every site starts with a theme.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("p", null, __('Hello Biz by Elementor helps you launch your professional business website - fast.', 'elementor')), !elementorAppConfig.onboarding.experiment && /*#__PURE__*/_react.default.createElement("p", null, __('Here\'s why:', 'elementor')), /*#__PURE__*/_react.default.createElement("ul", {
    className: "e-onboarding__feature-list"
  }, /*#__PURE__*/_react.default.createElement("li", null, __('Get online faster', 'elementor')), /*#__PURE__*/_react.default.createElement("li", null, __('Lightweight and fast loading', 'elementor')), /*#__PURE__*/_react.default.createElement("li", null, __('Great for SEO', 'elementor')))), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__footnote"
  }, '* ' + __('You can switch your theme later on', 'elementor')));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/site-logo.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/site-logo.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SiteLogo;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _useAjax3 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! elementor-app/organisms/drop-zone */ "../app/assets/js/organisms/drop-zone.js"));
var _unfilteredFilesDialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/organisms/unfiltered-files-dialog */ "../app/assets/js/organisms/unfiltered-files-dialog.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */

function SiteLogo() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useState = (0, _react.useState)(state.siteLogo.id ? state.siteLogo : null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    file = _useState2[0],
    setFile = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isUploading = _useState4[0],
    setIsUploading = _useState4[1],
    _useState5 = (0, _react.useState)(false),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    showUnfilteredFilesDialog = _useState6[0],
    setShowUnfilteredFilesDialog = _useState6[1],
    _useState7 = (0, _react.useState)(),
    _useState8 = (0, _slicedToArray2.default)(_useState7, 2),
    fileSource = _useState8[0],
    setFileSource = _useState8[1],
    _useState9 = (0, _react.useState)(null),
    _useState0 = (0, _slicedToArray2.default)(_useState9, 2),
    noticeState = _useState0[0],
    setNoticeState = _useState0[1],
    _useAjax = (0, _useAjax3.default)(),
    updateLogoAjaxState = _useAjax.ajaxState,
    setUpdateLogoAjax = _useAjax.setAjax,
    _useAjax2 = (0, _useAjax3.default)(),
    uploadImageAjaxState = _useAjax2.ajaxState,
    setUploadImageAjax = _useAjax2.setAjax,
    pageId = 'siteLogo',
    nextStep = 'goodToGo',
    navigate = (0, _router.useNavigate)(),
    actionButton = {
      role: 'button',
      onClick: function onClick() {
        elementorCommon.events.dispatchEvent({
          event: 'next',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep
          }
        });
        if (file.id) {
          if (file.id !== state.siteLogo.id) {
            updateSiteLogo();
          } else {
            // If the currently displayed logo is already set as the site logo, just go to the next screen.
            var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
            updateState(stateToUpdate);
            navigate('onboarding/' + nextStep);
          }
        }
      }
    };
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip', 'elementor')
    };
  }
  if (isUploading) {
    actionButton.text = /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
      className: "eicon-loading eicon-animation-spin",
      "aria-hidden": "true"
    }));
  } else {
    actionButton.text = __('Next', 'elementor');
  }
  if (!file) {
    actionButton.className = 'e-onboarding__button--disabled';
  }
  var updateSiteLogo = (0, _react.useCallback)(function () {
    setIsUploading(true);
    setUpdateLogoAjax({
      data: {
        action: 'elementor_update_site_logo',
        data: JSON.stringify({
          attachmentId: file.id
        })
      }
    });
  }, [file]);
  var uploadSiteLogo = function uploadSiteLogo(fileToUpload) {
    setIsUploading(true);
    setUploadImageAjax({
      data: {
        action: 'elementor_upload_site_logo',
        fileToUpload: fileToUpload
      }
    });
  };
  var dismissUnfilteredFilesCallback = function dismissUnfilteredFilesCallback() {
    setIsUploading(false);
    setFile(null);
    setShowUnfilteredFilesDialog(false);
  };
  var _onFileSelect = function onFileSelect(selectedFile) {
    setFileSource('drop');
    if ('image/svg+xml' === selectedFile.type && !elementorAppConfig.onboarding.isUnfilteredFilesEnabled) {
      setFile(selectedFile);
      setIsUploading(true);
      setShowUnfilteredFilesDialog(true);
    } else {
      setFile(selectedFile);
      setNoticeState(null);
      uploadSiteLogo(selectedFile);
    }
  };
  var onImageRemoveClick = function onImageRemoveClick() {
    elementorCommon.events.dispatchEvent({
      event: 'remove selected logo',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement
      }
    });
    setFile(null);
  };

  /**
   * Ajax Callbacks
   */
  // Run the callback for the new image upload AJAX request.
  (0, _react.useEffect)(function () {
    if ('initial' !== uploadImageAjaxState.status) {
      var _uploadImageAjaxState;
      if ('success' === uploadImageAjaxState.status && (_uploadImageAjaxState = uploadImageAjaxState.response) !== null && _uploadImageAjaxState !== void 0 && (_uploadImageAjaxState = _uploadImageAjaxState.imageAttachment) !== null && _uploadImageAjaxState !== void 0 && _uploadImageAjaxState.id) {
        elementorCommon.events.dispatchEvent({
          event: 'logo image uploaded',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            source: fileSource
          }
        });
        setIsUploading(false);
        setFile(uploadImageAjaxState.response.imageAttachment);
        if (noticeState) {
          setNoticeState(null);
        }
      } else if ('error' === uploadImageAjaxState.status) {
        setIsUploading(false);
        setFile(null);
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            action_state: 'failure',
            action: 'logo image upload'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: 'That didn\'t work. Try uploading your file again.'
        });
      }
    }
  }, [uploadImageAjaxState.status]);

  // Run the callback for the site logo update AJAX request.
  (0, _react.useEffect)(function () {
    if ('initial' !== updateLogoAjaxState.status) {
      var _updateLogoAjaxState$;
      if ('success' === updateLogoAjaxState.status && (_updateLogoAjaxState$ = updateLogoAjaxState.response) !== null && _updateLogoAjaxState$ !== void 0 && _updateLogoAjaxState$.siteLogoUpdated) {
        elementorCommon.events.dispatchEvent({
          event: 'logo image updated',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            source: fileSource
          }
        });
        setIsUploading(false);
        if (noticeState) {
          setNoticeState(null);
        }
        var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
        stateToUpdate.siteLogo = {
          id: file.id,
          url: file.url
        };
        updateState(stateToUpdate);
        navigate('onboarding/' + nextStep);
      } else if ('error' === updateLogoAjaxState.status) {
        setIsUploading(false);
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'update site logo'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: 'That didn\'t work. Try uploading your file again.'
        });
      }
    }
  }, [updateLogoAjaxState.status]);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Setup.svg',
    title: __('Have a logo? Add it here.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("span", null, __('Otherwise, you can skip this and add one later.', 'elementor')), file && !showUnfilteredFilesDialog ? /*#__PURE__*/_react.default.createElement("div", {
    className: 'e-onboarding__logo-container' + (isUploading ? ' e-onboarding__is-uploading' : '')
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__logo-remove",
    onClick: function onClick() {
      return onImageRemoveClick();
    }
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-trash-o"
  })), /*#__PURE__*/_react.default.createElement("img", {
    src: file.url,
    alt: __('Potential Site Logo', 'elementor')
  })) : /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_dropZone.default, {
    className: "e-onboarding__drop-zone",
    heading: __('Drop image here', 'elementor'),
    secondaryText: __('or', 'elementor'),
    buttonText: __('Open Media Library', 'elementor'),
    buttonVariant: "outlined",
    buttonColor: "cta",
    icon: '',
    type: "wp-media",
    filetypes: ['jpg', 'jpeg', 'png', 'svg'],
    onFileSelect: function onFileSelect(selectedFile) {
      return _onFileSelect(selectedFile);
    },
    onWpMediaSelect: function onWpMediaSelect(frame) {
      // Get media attachment details from the frame state
      var attachment = frame.state().get('selection').first().toJSON();
      setFileSource('browse');
      setFile(attachment);
      setNoticeState(null);
    },
    onButtonClick: function onButtonClick() {
      elementorCommon.events.dispatchEvent({
        event: 'browse file click',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });
    }
    // TODO: DEAL WITH ERROR
    ,
    onError: function onError(error) {
      if ('file_not_allowed' === error.id) {
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'logo upload format'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: __('This file type is not supported. Try a different type of file', 'elementor')
        });
      }
    }
  })), /*#__PURE__*/_react.default.createElement(_unfilteredFilesDialog.default, {
    show: showUnfilteredFilesDialog,
    setShow: setShowUnfilteredFilesDialog,
    confirmModalText: __('This allows Elementor to scan your SVGs for malicious content. If you do not wish to allow this, use a different image format.', 'elementor'),
    errorModalText: __('There was a problem with enabling SVG uploads. Try again, or use another image format.', 'elementor'),
    onReady: function onReady() {
      setShowUnfilteredFilesDialog(false);
      elementorAppConfig.onboarding.isUnfilteredFilesEnabled = true;
      uploadSiteLogo(file);
    },
    onDismiss: function onDismiss() {
      return dismissUnfilteredFilesCallback();
    },
    onCancel: function onCancel() {
      return dismissUnfilteredFilesCallback();
    }
  })));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/site-name.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/site-name.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SiteName;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function SiteName() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useAjax = (0, _useAjax2.default)(),
    ajaxState = _useAjax.ajaxState,
    setAjax = _useAjax.setAjax,
    _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    noticeState = _useState2[0],
    setNoticeState = _useState2[1],
    _useState3 = (0, _react.useState)(state.siteName),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    siteNameInputValue = _useState4[0],
    setSiteNameInputValue = _useState4[1],
    pageId = 'siteName',
    nextStep = 'siteLogo',
    navigate = (0, _router.useNavigate)(),
    nameInputRef = (0, _react.useRef)(),
    actionButton = {
      text: __('Next', 'elementor'),
      onClick: function onClick() {
        elementorCommon.events.dispatchEvent({
          event: 'next',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep
          }
        });

        // Only run the site name update AJAX if the new name is different than the existing one and it isn't empty.
        if (nameInputRef.current.value !== state.siteName && '' !== nameInputRef.current.value) {
          setAjax({
            data: {
              action: 'elementor_update_site_name',
              data: JSON.stringify({
                siteName: nameInputRef.current.value
              })
            }
          });
        } else if (nameInputRef.current.value === state.siteName) {
          var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
          updateState(stateToUpdate);
          navigate('onboarding/' + nextStep);
        } else {
          var _stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'skipped');
          updateState(_stateToUpdate);
          navigate('onboarding/' + nextStep);
        }
      }
    };
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip', 'elementor')
    };
  }
  if (!siteNameInputValue) {
    actionButton.className = 'e-onboarding__button--disabled';
  }

  // Run the callback for the site name update AJAX request.
  (0, _react.useEffect)(function () {
    if ('initial' !== ajaxState.status) {
      var _ajaxState$response;
      if ('success' === ajaxState.status && (_ajaxState$response = ajaxState.response) !== null && _ajaxState$response !== void 0 && _ajaxState$response.siteNameUpdated) {
        var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
        stateToUpdate.siteName = nameInputRef.current.value;
        updateState(stateToUpdate);
        navigate('onboarding/' + nextStep);
      } else if ('error' === ajaxState.status) {
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'site name update'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: __('Sorry, the name wasn\'t saved. Try again, or skip for now.', 'elementor')
        });
      }
    }
  }, [ajaxState.status]);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Setup.svg',
    title: __('Now, let\'s give your site a name.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("p", null, __('This is what your site is called on the WP dashboard, and can be changed later from the general settings - it\'s not your website\'s URL.', 'elementor')), /*#__PURE__*/_react.default.createElement("input", {
    className: "e-onboarding__text-input e-onboarding__site-name-input",
    type: "text",
    placeholder: "e.g. Eric's Space Shuttles",
    defaultValue: state.siteName || '',
    ref: nameInputRef,
    onChange: function onChange(event) {
      return setSiteNameInputValue(event.target.value);
    }
  })));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/upload-and-install-pro.js":
/*!***************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/upload-and-install-pro.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = UploadAndInstallPro;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! ../../../../../assets/js/organisms/drop-zone */ "../app/assets/js/organisms/drop-zone.js"));
var _notice = _interopRequireDefault(__webpack_require__(/*! ../components/notice */ "../app/modules/onboarding/assets/js/components/notice.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _elementorLoading = _interopRequireDefault(__webpack_require__(/*! elementor-app/molecules/elementor-loading */ "../app/assets/js/molecules/elementor-loading.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function UploadAndInstallPro() {
  (0, _usePageTitle.default)({
    title: __('Upload and Install Elementor Pro', 'elementor')
  });
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    _useAjax = (0, _useAjax2.default)(),
    installProZipAjaxState = _useAjax.ajaxState,
    setInstallProZipAjaxState = _useAjax.setAjax,
    _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    noticeState = _useState2[0],
    setNoticeState = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isLoading = _useState4[0],
    setIsLoading = _useState4[1],
    _useState5 = (0, _react.useState)(),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    fileSource = _useState6[0],
    setFileSource = _useState6[1];
  var uploadProZip = (0, _react.useCallback)(function (file) {
    setIsLoading(true);
    setInstallProZipAjaxState({
      data: {
        action: 'elementor_upload_and_install_pro',
        fileToUpload: file
      }
    });
  }, []);
  var setErrorNotice = function setErrorNotice() {
    var error = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var step = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'upload';
    var errorMessage = (error === null || error === void 0 ? void 0 : error.message) || 'That didn\'t work. Try uploading your file again.';
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'failure',
        action: step + ' pro',
        source: fileSource
      }
    });
    setNoticeState({
      type: 'error',
      icon: 'eicon-warning',
      message: errorMessage
    });
  };

  /**
   * Ajax Callbacks
   */
  // Run the callback that runs when the Pro Upload Ajax returns a response.
  (0, _react.useEffect)(function () {
    if ('initial' !== installProZipAjaxState.status) {
      var _installProZipAjaxSta;
      setIsLoading(false);
      if ('success' === installProZipAjaxState.status && (_installProZipAjaxSta = installProZipAjaxState.response) !== null && _installProZipAjaxSta !== void 0 && _installProZipAjaxSta.elementorProInstalled) {
        elementorCommon.events.dispatchEvent({
          event: 'pro uploaded',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            source: fileSource
          }
        });
        if (opener && opener !== window) {
          opener.jQuery('body').trigger('elementor/upload-and-install-pro/success');
          window.close();
          opener.focus();
        }
      } else if ('error' === installProZipAjaxState.status) {
        setErrorNotice('install');
      }
    }
  }, [installProZipAjaxState.status]);
  var onProUploadHelpLinkClick = function onProUploadHelpLinkClick() {
    elementorCommon.events.dispatchEvent({
      event: 'pro plugin upload help',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
  };
  if (isLoading) {
    return /*#__PURE__*/_react.default.createElement(_elementorLoading.default, {
      loadingText: __('Uploading', 'elementor')
    });
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app e-onboarding__upload-pro"
  }, /*#__PURE__*/_react.default.createElement(_content.default, null, /*#__PURE__*/_react.default.createElement(_dropZone.default, {
    className: "e-onboarding__upload-pro-drop-zone",
    onFileSelect: function onFileSelect(file, event, source) {
      setFileSource(source);
      uploadProZip(file);
    },
    onError: function onError(error) {
      return setErrorNotice(error, 'upload');
    },
    filetypes: ['zip'],
    buttonColor: "cta",
    buttonVariant: "contained",
    heading: __('Import your Elementor Pro plugin file', 'elementor'),
    text: __('Drag & Drop your .zip file here', 'elementor'),
    secondaryText: __('or', 'elementor'),
    buttonText: __('Browse', 'elementor')
  }), noticeState && /*#__PURE__*/_react.default.createElement(_notice.default, {
    noticeState: noticeState
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__upload-pro-get-file"
  }, __('Don\'t know where to get the file from?', 'elementor') + ' ', /*#__PURE__*/_react.default.createElement("a", {
    onClick: function onClick() {
      return onProUploadHelpLinkClick();
    },
    href: 'https://my.elementor.com/subscriptions/' + elementorAppConfig.onboarding.utms.downloadPro,
    target: "_blank"
  }, __('Click here', 'elementor')))));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/connect.js":
/*!************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/connect.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Connect;
var _react = __webpack_require__(/*! react */ "react");
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _onboardingEventTracking = __webpack_require__(/*! ./onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function Connect(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate;
  var buttonRef = props.buttonRef,
    successCallback = props.successCallback,
    errorCallback = props.errorCallback;
  var handleCoreConnectionLogic = (0, _react.useCallback)(function (event, data) {
    var isTrackingOptedInConnect = data.tracking_opted_in && elementorCommon.config.editor_events;
    _onboardingEventTracking.OnboardingEventTracking.updateLibraryConnectConfig(data);
    if (isTrackingOptedInConnect) {
      elementorCommon.config.editor_events.can_send_events = true;
      _onboardingEventTracking.OnboardingEventTracking.sendConnectionSuccessEvents(data);
    }
  }, []);
  var defaultConnectSuccessCallback = (0, _react.useCallback)(function () {
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', 'account', 'completed');
    stateToUpdate.isLibraryConnected = true;
    updateState(stateToUpdate);
  }, [state, getStateObjectToUpdate, updateState]);
  (0, _react.useEffect)(function () {
    jQuery(buttonRef.current).elementorConnect({
      success: function success(event, data) {
        handleCoreConnectionLogic(event, data);
        if (successCallback) {
          successCallback(event, data);
        } else {
          defaultConnectSuccessCallback();
        }
      },
      error: function error() {
        if (errorCallback) {
          errorCallback();
        }
      },
      popup: {
        width: 726,
        height: 534
      }
    });
  }, [buttonRef, successCallback, errorCallback, handleCoreConnectionLogic, defaultConnectSuccessCallback]);
  return null;
}
Connect.propTypes = {
  buttonRef: PropTypes.object.isRequired,
  successCallback: PropTypes.func,
  errorCallback: PropTypes.func
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/modules/onboarding-tracker.js":
/*!*******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/modules/onboarding-tracker.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "ONBOARDING_STEP_NAMES", ({
  enumerable: true,
  get: function get() {
    return _eventDispatcher.ONBOARDING_STEP_NAMES;
  }
}));
Object.defineProperty(exports, "ONBOARDING_STORAGE_KEYS", ({
  enumerable: true,
  get: function get() {
    return _storageManager.ONBOARDING_STORAGE_KEYS;
  }
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _eventsConfig = _interopRequireDefault(__webpack_require__(/*! ../../../../../../../core/common/modules/events-manager/assets/js/events-config */ "../core/common/modules/events-manager/assets/js/events-config.js"));
var _storageManager = _interopRequireWildcard(__webpack_require__(/*! ./storage-manager.js */ "../app/modules/onboarding/assets/js/utils/modules/storage-manager.js"));
var _eventDispatcher = _interopRequireWildcard(__webpack_require__(/*! ./event-dispatcher.js */ "../app/modules/onboarding/assets/js/utils/modules/event-dispatcher.js"));
var _timingManager = _interopRequireDefault(__webpack_require__(/*! ./timing-manager.js */ "../app/modules/onboarding/assets/js/utils/modules/timing-manager.js"));
var _postOnboardingTracker = _interopRequireDefault(__webpack_require__(/*! ./post-onboarding-tracker.js */ "../app/modules/onboarding/assets/js/utils/modules/post-onboarding-tracker.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var OnboardingTracker = /*#__PURE__*/function () {
  function OnboardingTracker() {
    (0, _classCallCheck2.default)(this, OnboardingTracker);
    this.initializeEventConfigs();
    this.initializeEventListeners();
  }
  return (0, _createClass2.default)(OnboardingTracker, [{
    key: "initializeEventConfigs",
    value: function initializeEventConfigs() {
      this.EVENT_CONFIGS = {
        SKIP: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.SKIP,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_SKIP,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'skip_clicked'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep,
              skip_timestamp: eventData.timestamp
            };
          }
        },
        TOP_UPGRADE: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.TOP_UPGRADE,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE,
          isArray: true,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'upgrade_interaction'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep,
              upgrade_clicked: eventData.upgradeClicked
            };
          }
        },
        CREATE_MY_ACCOUNT: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.CREATE_MY_ACCOUNT,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_MY_ACCOUNT,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'upgrade_interaction'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep,
              create_account_clicked: eventData.createAccountClicked
            };
          }
        },
        CREATE_ACCOUNT_STATUS: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.CREATE_ACCOUNT_STATUS,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_ACCOUNT_STATUS,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'create_flow_returns_status'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              onboarding_create_account_status: eventData.status
            };
          }
        },
        CONNECT_STATUS: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.CONNECT_STATUS,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CONNECT_STATUS,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'connect_flow_returns_status'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              onboarding_connect_status: eventData.status,
              tracking_opted_in: eventData.trackingOptedIn,
              user_tier: eventData.userTier
            };
          },
          stepOverride: 1,
          stepNameOverride: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT
        },
        STEP1_CLICKED_CONNECT: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP1_CLICKED_CONNECT,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_CLICKED_CONNECT,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: _eventsConfig.default.triggers.click
          },
          payloadBuilder: function payloadBuilder() {
            return {};
          },
          stepOverride: 1,
          stepNameOverride: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT
        },
        STEP1_END_STATE: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP1_END_STATE,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_END_STATE,
          isRawPayload: true,
          payloadBuilder: function payloadBuilder(eventData) {
            return eventData;
          }
        },
        EXIT_BUTTON: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.EXIT_BUTTON,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_EXIT_BUTTON,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'exit_button_clicked'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep
            };
          }
        }
      };
    }
  }, {
    key: "initializeEventListeners",
    value: function initializeEventListeners() {
      var _this = this;
      if ('undefined' === typeof document) {
        return;
      }
      document.addEventListener('click', function (event) {
        var cardGridElement = event.target.closest('.e-onboarding__cards-grid');
        if (cardGridElement) {
          _this.handleStep4CardClick(event);
        }
      }, true);
      this.setupUrlChangeDetection();
    }
  }, {
    key: "setupUrlChangeDetection",
    value: function setupUrlChangeDetection() {
      var _this2 = this;
      var lastUrl = window.location.href;
      var urlChangeDetector = function urlChangeDetector() {
        var currentUrl = window.location.href;
        if (currentUrl !== lastUrl) {
          var isStep4 = currentUrl.includes('goodToGo') || currentUrl.includes('step4') || currentUrl.includes('site_starter');
          if (isStep4) {
            setTimeout(function () {
              _timingManager.default.trackStepStartTime(4);
              _this2.checkAndSendReturnToStep4();
            }, 100);
          }
          lastUrl = currentUrl;
        }
      };
      setInterval(urlChangeDetector, 500);
      window.addEventListener('popstate', function () {
        setTimeout(urlChangeDetector, 100);
      });
    }
  }, {
    key: "dispatchEvent",
    value: function dispatchEvent(eventName, payload) {
      return _eventDispatcher.default.dispatch(eventName, payload);
    }
  }, {
    key: "sendEventOrStore",
    value: function sendEventOrStore(eventType, eventData) {
      if ('TOP_UPGRADE' === eventType && 'no_click' !== eventData.upgradeClicked) {
        var stepNumber = this.getStepNumber(eventData.currentStep);
        this.markUpgradeClickSent(stepNumber);
      }
      if (_eventDispatcher.default.canSendEvents()) {
        return this.sendEventDirect(eventType, eventData);
      }
      this.storeEventForLater(eventType, eventData);
    }
  }, {
    key: "sendEventDirect",
    value: function sendEventDirect(eventType, eventData) {
      var config = this.EVENT_CONFIGS[eventType];
      if (!config) {
        return;
      }
      if (config.isRawPayload) {
        return this.dispatchEvent(config.eventName, eventData);
      }
      var stepNumber = config.stepOverride || this.getStepNumber(eventData.currentStep);
      var stepName = config.stepNameOverride || this.getStepName(stepNumber);
      var eventPayload = _eventDispatcher.default.createStepEventPayload(stepNumber, stepName, _objectSpread(_objectSpread({}, config.basePayload), config.payloadBuilder(eventData)));
      return this.dispatchEvent(config.eventName, eventPayload);
    }
  }, {
    key: "storeEventForLater",
    value: function storeEventForLater(eventType, eventData) {
      var config = this.EVENT_CONFIGS[eventType];
      if (!config) {
        return;
      }
      var dataWithTimestamp = _objectSpread(_objectSpread({}, eventData), {}, {
        timestamp: _timingManager.default.getCurrentTime()
      });
      if (config.isArray) {
        var existingEvents = _storageManager.default.getArray(config.storageKey);
        existingEvents.push(dataWithTimestamp);
        _storageManager.default.setObject(config.storageKey, existingEvents);
      } else {
        _storageManager.default.setObject(config.storageKey, dataWithTimestamp);
      }
    }
  }, {
    key: "sendStoredEvent",
    value: function sendStoredEvent(eventType) {
      var _this3 = this;
      var config = this.EVENT_CONFIGS[eventType];
      if (!config) {
        return;
      }
      var storedData = config.isArray ? _storageManager.default.getArray(config.storageKey) : _storageManager.default.getObject(config.storageKey);
      if (!storedData || config.isArray && 0 === storedData.length) {
        return;
      }
      var processEvent = function processEvent(eventData) {
        if (config.isRawPayload) {
          _this3.dispatchEvent(config.eventName, eventData);
          return;
        }
        var stepNumber = config.stepOverride || _this3.getStepNumber(eventData.currentStep);
        var stepName = config.stepNameOverride || _this3.getStepName(stepNumber);
        var eventPayload = _eventDispatcher.default.createStepEventPayload(stepNumber, stepName, _objectSpread(_objectSpread({}, config.basePayload), config.payloadBuilder(eventData)));
        _this3.dispatchEvent(config.eventName, eventPayload);
      };
      if (config.isArray) {
        storedData.forEach(processEvent);
      } else {
        processEvent(storedData);
      }
      _storageManager.default.remove(config.storageKey);
    }
  }, {
    key: "updateLibraryConnectConfig",
    value: function updateLibraryConnectConfig(data) {
      if (!elementorCommon.config.library_connect) {
        return;
      }
      elementorCommon.config.library_connect.is_connected = true;
      elementorCommon.config.library_connect.current_access_level = data.kits_access_level || data.access_level || 0;
      elementorCommon.config.library_connect.current_access_tier = data.access_tier;
      elementorCommon.config.library_connect.plan_type = data.plan_type;
      elementorCommon.config.library_connect.user_id = data.user_id || null;
    }
  }, {
    key: "sendUpgradeNowStep3",
    value: function sendUpgradeNowStep3(selectedFeatures, currentStep) {
      var proFeaturesChecked = this.extractSelectedFeatureKeys(selectedFeatures);
      return _eventDispatcher.default.dispatchStepEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.UPGRADE_NOW_S3, currentStep, _eventDispatcher.ONBOARDING_STEP_NAMES.PRO_FEATURES, {
        location: 'plugin_onboarding',
        trigger: _eventsConfig.default.triggers.click,
        pro_features_checked: proFeaturesChecked
      });
    }
  }, {
    key: "extractSelectedFeatureKeys",
    value: function extractSelectedFeatureKeys(selectedFeatures) {
      if (!selectedFeatures || !Array.isArray(selectedFeatures)) {
        return [];
      }
      return selectedFeatures.filter(function (feature) {
        return feature && feature.is_checked;
      }).map(function (feature) {
        return feature.key;
      }).filter(function (key) {
        return key;
      });
    }
  }, {
    key: "sendHelloBizContinue",
    value: function sendHelloBizContinue(stepNumber) {
      if (_eventDispatcher.default.canSendEvents()) {
        return _eventDispatcher.default.dispatchStepEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.HELLO_BIZ_CONTINUE, stepNumber, _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ, {
          location: 'plugin_onboarding',
          trigger: _eventsConfig.default.triggers.click
        });
      }
    }
  }, {
    key: "sendTopUpgrade",
    value: function sendTopUpgrade(currentStep, upgradeClicked) {
      return this.sendEventOrStore('TOP_UPGRADE', {
        currentStep: currentStep,
        upgradeClicked: upgradeClicked
      });
    }
  }, {
    key: "cancelDelayedNoClickEvent",
    value: function cancelDelayedNoClickEvent() {
      _storageManager.default.remove(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE_NO_CLICK);
    }
  }, {
    key: "initiateCoreOnboarding",
    value: function initiateCoreOnboarding() {
      _timingManager.default.clearStaleSessionData();
      _timingManager.default.initializeOnboardingStartTime();
    }
  }, {
    key: "sendCoreOnboardingInitiated",
    value: function sendCoreOnboardingInitiated() {
      var startTime = _timingManager.default.initializeOnboardingStartTime();
      var currentTime = _timingManager.default.getCurrentTime();
      var totalOnboardingTime = Math.round((currentTime - startTime) / 1000);
      var eventData = _timingManager.default.addTimingToEventData({
        location: 'plugin_onboarding',
        trigger: 'core_onboarding_initiated',
        step_number: 1,
        step_name: _eventDispatcher.ONBOARDING_STEP_NAMES.ONBOARDING_START,
        onboarding_start_time: startTime,
        total_onboarding_time_seconds: totalOnboardingTime
      });
      this.dispatchEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.CORE_ONBOARDING, eventData);
      _storageManager.default.remove(_storageManager.ONBOARDING_STORAGE_KEYS.INITIATED);
    }
  }, {
    key: "storeSiteStarterChoice",
    value: function storeSiteStarterChoice(siteStarter) {
      var choiceData = {
        site_starter: siteStarter,
        timestamp: _timingManager.default.getCurrentTime(),
        return_event_sent: false
      };
      _storageManager.default.setObject(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE, choiceData);
    }
  }, {
    key: "checkAndSendReturnToStep4",
    value: function checkAndSendReturnToStep4() {
      var choiceData = _storageManager.default.getObject(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE);
      if (!choiceData) {
        return;
      }
      if (!choiceData.return_event_sent) {
        var returnEventPayload = _eventDispatcher.default.createStepEventPayload(4, _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_STARTER, {
          location: 'plugin_onboarding',
          trigger: 'user_returns_to_onboarding',
          return_to_onboarding: choiceData.site_starter,
          original_choice_timestamp: choiceData.timestamp
        });
        this.dispatchEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.STEP4_RETURN_STEP4, returnEventPayload);
        choiceData.return_event_sent = true;
        _storageManager.default.setObject(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE, choiceData);
      }
    }
  }, {
    key: "handleSiteStarterChoice",
    value: function handleSiteStarterChoice(siteStarter) {
      _timingManager.default.trackStepStartTime(4);
      this.storeSiteStarterChoice(siteStarter);
      this.trackStepAction(4, 'site_starter', {
        source_type: siteStarter
      });
      this.sendStepEndState(4);
    }
  }, {
    key: "storeExitEventForLater",
    value: function storeExitEventForLater(exitType, currentStep) {
      var exitData = {
        exitType: exitType,
        currentStep: currentStep,
        timestamp: _timingManager.default.getCurrentTime()
      };
      _storageManager.default.setObject(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_EXIT, exitData);
    }
  }, {
    key: "checkAndSendEditorLoadedFromOnboarding",
    value: function checkAndSendEditorLoadedFromOnboarding() {
      return _postOnboardingTracker.default.checkAndSendEditorLoadedFromOnboarding();
    }
  }, {
    key: "sendExitButtonEvent",
    value: function sendExitButtonEvent(currentStep) {
      var stepNumber = this.getStepNumber(currentStep);
      this.trackStepAction(stepNumber, 'exit_button');
      this.sendStepEndState(stepNumber);
      return this.sendEventOrStore('EXIT_BUTTON', {
        currentStep: currentStep
      });
    }
  }, {
    key: "trackStepAction",
    value: function trackStepAction(stepNumber, action) {
      var additionalData = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var stepConfig = this.getStepConfig(stepNumber);
      if (stepConfig) {
        this.trackStepActionInternal(stepNumber, action, stepConfig.storageKey, additionalData);
      }
    }
  }, {
    key: "sendStepEndState",
    value: function sendStepEndState(stepNumber) {
      var stepConfig = this.getStepConfig(stepNumber);
      if (stepConfig) {
        this.sendStepEndStateInternal(stepNumber, stepConfig.storageKey, stepConfig.eventName, stepConfig.stepName, stepConfig.endStateProperty);
      }
    }
  }, {
    key: "trackStepActionInternal",
    value: function trackStepActionInternal(stepNumber, action, storageKey) {
      var additionalData = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
      // Always store the action, regardless of global timing availability
      var existingActions = _storageManager.default.getArray(storageKey);
      var actionData = _objectSpread({
        action: action,
        timestamp: _timingManager.default.getCurrentTime()
      }, additionalData);
      existingActions.push(actionData);
      _storageManager.default.setObject(storageKey, existingActions);
    }
  }, {
    key: "sendStepEndStateInternal",
    value: function sendStepEndStateInternal(stepNumber, storageKey, eventName, stepName, endStateProperty) {
      var actions = _storageManager.default.getArray(storageKey);
      if (0 === actions.length) {
        return;
      }
      var eventData = _eventDispatcher.default.createStepEventPayload(stepNumber, stepName, {
        location: 'plugin_onboarding',
        trigger: 'user_redirects_out_of_step'
      });
      eventData = _timingManager.default.addTimingToEventData(eventData, stepNumber);
      var filteredActions = actions.filter(function (action) {
        return 'upgrade_hover' !== action.action && 'upgrade_topbar' !== action.action && 'upgrade_now' !== action.action && 'upgrade_already_pro' !== action.action;
      });
      eventData[endStateProperty] = filteredActions;
      if (_eventDispatcher.default.canSendEvents()) {
        this.sendHoverEventsFromStepActions(actions, stepNumber);
        this.dispatchEvent(eventName, eventData);
        _storageManager.default.remove(storageKey);
        _timingManager.default.clearStepStartTime(stepNumber);
      } else if (1 === stepNumber) {
        this.storeStep1EndStateForLater(eventData, storageKey);
      } else {
        this.sendHoverEventsFromStepActions(actions, stepNumber);
        this.dispatchEvent(eventName, eventData);
        _storageManager.default.remove(storageKey);
        _timingManager.default.clearStepStartTime(stepNumber);
      }
    }
  }, {
    key: "getStepNumber",
    value: function getStepNumber(pageId) {
      if (this.isNumericPageId(pageId)) {
        return pageId;
      }
      if (this.isStringNumericPageId(pageId)) {
        return this.convertStringToNumber(pageId);
      }
      return this.mapPageIdToStepNumber(pageId);
    }
  }, {
    key: "isNumericPageId",
    value: function isNumericPageId(pageId) {
      return 'number' === typeof pageId;
    }
  }, {
    key: "isStringNumericPageId",
    value: function isStringNumericPageId(pageId) {
      return 'string' === typeof pageId && !isNaN(pageId);
    }
  }, {
    key: "convertStringToNumber",
    value: function convertStringToNumber(pageId) {
      return parseInt(pageId, 10);
    }
  }, {
    key: "mapPageIdToStepNumber",
    value: function mapPageIdToStepNumber(pageId) {
      var stepMappings = this.getStepMappings();
      var mappedStep = stepMappings[pageId];
      return mappedStep || null;
    }
  }, {
    key: "getStepMappings",
    value: function getStepMappings() {
      return {
        account: 1,
        connect: 1,
        hello: 2,
        hello_biz: 2,
        chooseFeatures: 3,
        pro_features: 3,
        site_starter: 4,
        goodToGo: 4,
        siteName: 5,
        siteLogo: 6
      };
    }
  }, {
    key: "getStepName",
    value: function getStepName(stepNumber) {
      var stepNames = {
        1: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT,
        2: _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ,
        3: _eventDispatcher.ONBOARDING_STEP_NAMES.PRO_FEATURES,
        4: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_STARTER,
        5: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_NAME,
        6: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_LOGO
      };
      return stepNames[stepNumber] || 'unknown';
    }
  }, {
    key: "getStepConfig",
    value: function getStepConfig(stepNumber) {
      var stepConfigs = {
        1: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP1_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP1_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT,
          endStateProperty: 'step1_actions'
        },
        2: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP2_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ,
          endStateProperty: 'step2_actions'
        },
        3: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP3_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.PRO_FEATURES,
          endStateProperty: 'step3_actions'
        },
        4: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP4_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_STARTER,
          endStateProperty: 'step4_actions'
        }
      };
      return stepConfigs[stepNumber] || null;
    }
  }, {
    key: "sendConnectionSuccessEvents",
    value: function sendConnectionSuccessEvents(data) {
      this.sendCoreOnboardingInitiated();
      this.sendAppropriateStatusEvent('success', data);
      this.sendAllStoredEvents();
    }
  }, {
    key: "sendConnectionFailureEvents",
    value: function sendConnectionFailureEvents() {
      this.sendAppropriateStatusEvent('fail');
    }
  }, {
    key: "sendAppropriateStatusEvent",
    value: function sendAppropriateStatusEvent(status) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var hasCreateAccountAction = _storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_MY_ACCOUNT);
      var hasConnectAction = _storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_CLICKED_CONNECT);
      if (hasCreateAccountAction) {
        this.sendEventDirect('CREATE_ACCOUNT_STATUS', {
          status: status,
          currentStep: 1
        });
      } else if (hasConnectAction) {
        if (data) {
          this.sendEventDirect('CONNECT_STATUS', {
            status: status,
            trackingOptedIn: data.tracking_opted_in,
            userTier: data.access_tier
          });
        } else {
          this.sendEventDirect('CONNECT_STATUS', {
            status: status,
            trackingOptedIn: false,
            userTier: null
          });
        }
      } else if (data) {
        this.sendEventDirect('CONNECT_STATUS', {
          status: status,
          trackingOptedIn: data.tracking_opted_in,
          userTier: data.access_tier
        });
      } else {
        this.sendEventDirect('CONNECT_STATUS', {
          status: status,
          trackingOptedIn: false,
          userTier: null
        });
      }
    }
  }, {
    key: "sendAllStoredEvents",
    value: function sendAllStoredEvents() {
      this.sendStoredEvent('SKIP');
      this.sendStoredEvent('TOP_UPGRADE');
      this.sendStoredEvent('CREATE_MY_ACCOUNT');
      this.sendStoredEvent('CREATE_ACCOUNT_STATUS');
      this.sendStoredEvent('CONNECT_STATUS');
      this.sendStoredEvent('STEP1_CLICKED_CONNECT');
      this.sendStoredEvent('STEP1_END_STATE');
      this.sendStoredEvent('EXIT_BUTTON');
    }
  }, {
    key: "handleStep4CardClick",
    value: function handleStep4CardClick() {
      var hasPreviousClick = _storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_HAS_PREVIOUS_CLICK);
      if (hasPreviousClick) {
        this.checkAndSendReturnToStep4();
      } else {
        _storageManager.default.setString(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_HAS_PREVIOUS_CLICK, 'true');
      }
    }
  }, {
    key: "setupAllUpgradeButtons",
    value: function setupAllUpgradeButtons(currentStep) {
      var _this4 = this;
      var upgradeButtons = document.querySelectorAll('.elementor-button[href*="upgrade"], .e-btn[href*="upgrade"], .eps-button[href*="upgrade"]');
      upgradeButtons.forEach(function (button) {
        _this4.setupSingleUpgradeButton(button, currentStep);
      });
      return upgradeButtons.length;
    }
  }, {
    key: "setupSingleUpgradeButton",
    value: function setupSingleUpgradeButton(buttonElement, currentStep) {
      var _this5 = this;
      if (!this.isValidButtonElement(buttonElement)) {
        return null;
      }
      this.cleanupButtonTracking(buttonElement);
      if (this.isButtonAlreadyTrackedForStep(buttonElement, currentStep)) {
        return null;
      }
      this.markButtonAsTracked(buttonElement, currentStep);
      var eventHandlers = this.createUpgradeButtonEventHandlers(buttonElement, currentStep);
      this.attachEventHandlersToButton(buttonElement, eventHandlers);
      return function () {
        _this5.cleanupButtonTracking(buttonElement);
      };
    }
  }, {
    key: "isValidButtonElement",
    value: function isValidButtonElement(buttonElement) {
      return !!buttonElement;
    }
  }, {
    key: "isButtonAlreadyTrackedForStep",
    value: function isButtonAlreadyTrackedForStep(buttonElement, currentStep) {
      var existingStep = buttonElement.dataset.onboardingStep;
      return buttonElement.dataset.onboardingTracked && existingStep === currentStep;
    }
  }, {
    key: "markButtonAsTracked",
    value: function markButtonAsTracked(buttonElement, currentStep) {
      buttonElement.dataset.onboardingTracked = 'true';
      buttonElement.dataset.onboardingStep = currentStep;
    }
  }, {
    key: "createUpgradeButtonEventHandlers",
    value: function createUpgradeButtonEventHandlers(buttonElement, currentStep) {
      var _this6 = this;
      var hasClicked = false;
      var hasHovered = false;
      var handleMouseEnter = function handleMouseEnter() {
        if (!hasHovered) {
          hasHovered = true;
          _this6.trackUpgradeHoverAction(currentStep, buttonElement);
        }
      };
      var handleMouseLeave = function handleMouseLeave() {};
      var handleClick = function handleClick() {
        if (_this6.preventDuplicateClick(hasClicked)) {
          return;
        }
        hasClicked = true;
        _this6.sendUpgradeClickEvent(buttonElement, currentStep);
      };
      return {
        handleMouseEnter: handleMouseEnter,
        handleMouseLeave: handleMouseLeave,
        handleClick: handleClick
      };
    }
  }, {
    key: "preventDuplicateClick",
    value: function preventDuplicateClick(hasClicked) {
      return hasClicked;
    }
  }, {
    key: "sendUpgradeClickEvent",
    value: function sendUpgradeClickEvent(buttonElement, currentStep) {
      var upgradeClickedValue = this.determineUpgradeClickedValue(buttonElement);
      this.sendEventOrStore('TOP_UPGRADE', {
        currentStep: currentStep,
        upgradeClicked: upgradeClickedValue
      });
    }
  }, {
    key: "trackUpgradeHoverAction",
    value: function trackUpgradeHoverAction(currentStep, buttonElement) {
      var stepNumber = this.getStepNumber(currentStep);
      if (!stepNumber) {
        return;
      }
      var upgradeHoverValue = this.determineUpgradeClickedValue(buttonElement);
      this.trackStepAction(stepNumber, 'upgrade_hover', {
        upgrade_hovered: upgradeHoverValue,
        hover_timestamp: _timingManager.default.getCurrentTime()
      });
    }
  }, {
    key: "sendHoverEventsFromStepActions",
    value: function sendHoverEventsFromStepActions(actions, stepNumber) {
      var _this7 = this;
      var hoverActions = actions.filter(function (action) {
        return 'upgrade_hover' === action.action;
      });
      if (0 === hoverActions.length) {
        return;
      }
      var hasUpgradeClickInActions = actions.some(function (action) {
        return 'upgrade_topbar' === action.action || 'upgrade_tooltip' === action.action || 'upgrade_now' === action.action || 'upgrade_already_pro' === action.action;
      });
      var hasStoredClickEvent = this.hasExistingUpgradeClickEvent(stepNumber);
      var hasClickBeenSent = this.hasUpgradeClickBeenSent(stepNumber);
      if (hasUpgradeClickInActions || hasStoredClickEvent || hasClickBeenSent) {
        return;
      }
      hoverActions.forEach(function (hoverAction) {
        _this7.sendEventOrStore('TOP_UPGRADE', {
          currentStep: stepNumber,
          upgradeClicked: 'no_click',
          upgradeHovered: hoverAction.upgrade_hovered,
          hoverTimestamp: hoverAction.hover_timestamp
        });
      });
    }
  }, {
    key: "markUpgradeClickSent",
    value: function markUpgradeClickSent(stepNumber) {
      if (!this.sentUpgradeClicks) {
        this.sentUpgradeClicks = new Set();
      }
      this.sentUpgradeClicks.add(stepNumber);
    }
  }, {
    key: "hasUpgradeClickBeenSent",
    value: function hasUpgradeClickBeenSent(stepNumber) {
      return this.sentUpgradeClicks && this.sentUpgradeClicks.has(stepNumber);
    }
  }, {
    key: "hasExistingUpgradeClickEvent",
    value: function hasExistingUpgradeClickEvent(stepNumber) {
      var _this8 = this;
      var config = this.EVENT_CONFIGS.TOP_UPGRADE;
      var storedEvents = _storageManager.default.getArray(config.storageKey);
      return storedEvents.some(function (event) {
        var eventStepNumber = _this8.getStepNumber(event.currentStep);
        return eventStepNumber === stepNumber && event.upgradeClicked && 'no_click' !== event.upgradeClicked;
      });
    }
  }, {
    key: "attachEventHandlersToButton",
    value: function attachEventHandlersToButton(buttonElement, eventHandlers) {
      var handleMouseEnter = eventHandlers.handleMouseEnter,
        handleMouseLeave = eventHandlers.handleMouseLeave,
        handleClick = eventHandlers.handleClick;
      buttonElement._onboardingHandlers = {
        mouseenter: handleMouseEnter,
        mouseleave: handleMouseLeave,
        click: handleClick
      };
      buttonElement.addEventListener('mouseenter', handleMouseEnter);
      buttonElement.addEventListener('mouseleave', handleMouseLeave);
      buttonElement.addEventListener('click', handleClick);
    }
  }, {
    key: "cleanupButtonTracking",
    value: function cleanupButtonTracking(buttonElement) {
      if (!buttonElement) {
        return;
      }
      this.removeExistingEventHandlers(buttonElement);
      this.clearTrackingDataAttributes(buttonElement);
    }
  }, {
    key: "removeExistingEventHandlers",
    value: function removeExistingEventHandlers(buttonElement) {
      if (buttonElement._onboardingHandlers) {
        var handlers = buttonElement._onboardingHandlers;
        buttonElement.removeEventListener('mouseenter', handlers.mouseenter);
        buttonElement.removeEventListener('mouseleave', handlers.mouseleave);
        buttonElement.removeEventListener('click', handlers.click);
        delete buttonElement._onboardingHandlers;
      }
    }
  }, {
    key: "clearTrackingDataAttributes",
    value: function clearTrackingDataAttributes(buttonElement) {
      delete buttonElement.dataset.onboardingTracked;
      delete buttonElement.dataset.onboardingStep;
    }
  }, {
    key: "determineUpgradeClickedValue",
    value: function determineUpgradeClickedValue(buttonElement) {
      var _elementorCommon$conf, _elementorCommon$conf2;
      if ((_elementorCommon$conf = elementorCommon.config.library_connect) !== null && _elementorCommon$conf !== void 0 && _elementorCommon$conf.is_connected && 'pro' === ((_elementorCommon$conf2 = elementorCommon.config.library_connect) === null || _elementorCommon$conf2 === void 0 ? void 0 : _elementorCommon$conf2.current_access_tier)) {
        return 'already_pro_user';
      }
      if (buttonElement.closest('.e-app__popover') || buttonElement.closest('.elementor-tooltip') || buttonElement.closest('.e-onboarding__go-pro-content')) {
        return 'on_tooltip';
      }
      if (buttonElement.closest('.eps-app__header')) {
        return 'on_topbar';
      }
      return 'on_topbar';
    }
  }, {
    key: "trackExitAndSendEndState",
    value: function trackExitAndSendEndState(currentStep) {
      this.trackStepAction(currentStep, 'exit');
      this.sendStepEndState(currentStep);
    }
  }, {
    key: "storeStep1EndStateForLater",
    value: function storeStep1EndStateForLater(eventData, storageKey) {
      this.storeEventForLater('STEP1_END_STATE', eventData);
      _storageManager.default.remove(storageKey);
    }
  }, {
    key: "onStepLoad",
    value: function onStepLoad(currentStep) {
      _timingManager.default.trackStepStartTime(this.getStepNumber(currentStep));
      if (2 === this.getStepNumber(currentStep) || 'hello' === currentStep || 'hello_biz' === currentStep) {
        this.sendStoredStep1EventsOnStep2();
      }
      if (4 === this.getStepNumber(currentStep) || 'goodToGo' === currentStep) {
        this.checkAndSendReturnToStep4();
      }
    }
  }, {
    key: "sendStoredStep1EventsOnStep2",
    value: function sendStoredStep1EventsOnStep2() {
      this.sendStoredEvent('STEP1_CLICKED_CONNECT');
      var step1Actions = _storageManager.default.getArray(_storageManager.ONBOARDING_STORAGE_KEYS.STEP1_ACTIONS);
      if (step1Actions.length > 0) {
        this.sendHoverEventsFromStepActions(step1Actions, 1);
      }
      this.sendStoredEvent('STEP1_END_STATE');
    }
  }, {
    key: "setupPostOnboardingClickTracking",
    value: function setupPostOnboardingClickTracking() {
      return _postOnboardingTracker.default.setupPostOnboardingClickTracking();
    }
  }, {
    key: "cleanupPostOnboardingTracking",
    value: function cleanupPostOnboardingTracking() {
      return _postOnboardingTracker.default.cleanupPostOnboardingTracking();
    }
  }, {
    key: "clearAllOnboardingStorage",
    value: function clearAllOnboardingStorage() {
      return _postOnboardingTracker.default.clearAllOnboardingStorage();
    }
  }]);
}();
var onboardingTracker = new OnboardingTracker();
var _default = exports["default"] = onboardingTracker;

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/modules/timing-manager.js":
/*!***************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/modules/timing-manager.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.addTimingToEventData = addTimingToEventData;
exports.calculateStepTimeSpent = calculateStepTimeSpent;
exports.calculateTotalTimeSpent = calculateTotalTimeSpent;
exports.clearStaleSessionData = clearStaleSessionData;
exports.clearStepStartTime = clearStepStartTime;
exports.createTimeSpentData = createTimeSpentData;
exports["default"] = void 0;
exports.formatTimeForEvent = formatTimeForEvent;
exports.getCurrentTime = getCurrentTime;
exports.getOnboardingStartTime = getOnboardingStartTime;
exports.hasOnboardingStarted = hasOnboardingStarted;
exports.initializeOnboardingStartTime = initializeOnboardingStartTime;
exports.isWithinTimeThreshold = isWithinTimeThreshold;
exports.trackStepStartTime = trackStepStartTime;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _storageManager = _interopRequireWildcard(__webpack_require__(/*! ./storage-manager.js */ "../app/modules/onboarding/assets/js/utils/modules/storage-manager.js"));
var StorageManager = _storageManager;
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function getCurrentTime() {
  return Date.now();
}
function initializeOnboardingStartTime() {
  var startTime = getCurrentTime();
  StorageManager.setNumber(_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME, startTime);
  StorageManager.setString(_storageManager.ONBOARDING_STORAGE_KEYS.INITIATED, 'true');
  return startTime;
}
function getOnboardingStartTime() {
  return StorageManager.getNumber(_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME);
}
function hasOnboardingStarted() {
  return StorageManager.exists(_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME);
}
function trackStepStartTime(stepNumber) {
  var existingStartTime = StorageManager.getStepStartTime(stepNumber);
  if (existingStartTime) {
    return existingStartTime;
  }
  var currentTime = getCurrentTime();
  StorageManager.setStepStartTime(stepNumber, currentTime);
  return currentTime;
}
function calculateStepTimeSpent(stepNumber) {
  var stepStartTime = StorageManager.getStepStartTime(stepNumber);
  if (!stepStartTime) {
    return null;
  }
  var currentTime = getCurrentTime();
  var stepTimeSpent = Math.round((currentTime - stepStartTime) / 1000);
  return stepTimeSpent;
}
function clearStepStartTime(stepNumber) {
  StorageManager.clearStepStartTime(stepNumber);
}
function calculateTotalTimeSpent() {
  var startTime = getOnboardingStartTime();
  if (!startTime) {
    return null;
  }
  var currentTime = getCurrentTime();
  var timeSpent = Math.round((currentTime - startTime) / 1000);
  return {
    startTime: startTime,
    currentTime: currentTime,
    timeSpent: timeSpent
  };
}
function formatTimeForEvent(timeInSeconds) {
  if (null === timeInSeconds || timeInSeconds === undefined) {
    return null;
  }
  return "".concat(timeInSeconds, "s");
}
function createTimeSpentData() {
  var stepNumber = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var totalTimeData = calculateTotalTimeSpent();
  var result = {};
  if (totalTimeData) {
    result.time_spent = formatTimeForEvent(totalTimeData.timeSpent);
    result.total_onboarding_time_seconds = totalTimeData.timeSpent;
    result.onboarding_start_time = totalTimeData.startTime;
  }
  if (stepNumber) {
    var stepTimeSpent = calculateStepTimeSpent(stepNumber);
    if (stepTimeSpent !== null) {
      result.step_time_spent = formatTimeForEvent(stepTimeSpent);
    }
  }
  return Object.keys(result).length > 0 ? result : null;
}
function addTimingToEventData(eventData) {
  var stepNumber = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var timingData = createTimeSpentData(stepNumber);
  if (timingData) {
    return _objectSpread(_objectSpread({}, eventData), timingData);
  }
  return eventData;
}
function clearStaleSessionData() {
  var recentStepStartTimes = [];
  var currentTime = getCurrentTime();
  var recentStepStartTimeThresholdMs = 5000;
  [_storageManager.ONBOARDING_STORAGE_KEYS.STEP1_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_START_TIME].forEach(function (key) {
    var value = StorageManager.getString(key);
    if (value) {
      var timestamp = parseInt(value, 10);
      var age = currentTime - timestamp;
      if (age < recentStepStartTimeThresholdMs) {
        recentStepStartTimes.push(key);
      }
    }
  });
  var keysToRemove = [_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.INITIATED, _storageManager.ONBOARDING_STORAGE_KEYS.STEP1_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_HAS_PREVIOUS_CLICK, _storageManager.ONBOARDING_STORAGE_KEYS.EDITOR_LOAD_TRACKED, _storageManager.ONBOARDING_STORAGE_KEYS.POST_ONBOARDING_CLICK_COUNT, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_SKIP, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CONNECT_STATUS, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_ACCOUNT_STATUS, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_MY_ACCOUNT, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE_NO_CLICK, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_CLICKED_CONNECT, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_END_STATE, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_EXIT_BUTTON, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE_MOUSEOVER];
  keysToRemove.forEach(function (key) {
    if (!recentStepStartTimes.includes(key)) {
      StorageManager.remove(key);
    }
  });
  [_storageManager.ONBOARDING_STORAGE_KEYS.STEP1_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_START_TIME].forEach(function (key) {
    if (!recentStepStartTimes.includes(key)) {
      StorageManager.remove(key);
    }
  });
}
function isWithinTimeThreshold(timestamp) {
  var thresholdMs = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 5000;
  var currentTime = getCurrentTime();
  return currentTime - timestamp < thresholdMs;
}
var TimingManager = {
  getCurrentTime: getCurrentTime,
  initializeOnboardingStartTime: initializeOnboardingStartTime,
  getOnboardingStartTime: getOnboardingStartTime,
  hasOnboardingStarted: hasOnboardingStarted,
  trackStepStartTime: trackStepStartTime,
  calculateStepTimeSpent: calculateStepTimeSpent,
  clearStepStartTime: clearStepStartTime,
  calculateTotalTimeSpent: calculateTotalTimeSpent,
  formatTimeForEvent: formatTimeForEvent,
  createTimeSpentData: createTimeSpentData,
  addTimingToEventData: addTimingToEventData,
  clearStaleSessionData: clearStaleSessionData,
  isWithinTimeThreshold: isWithinTimeThreshold
};
var _default = exports["default"] = TimingManager;

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js":
/*!******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "ONBOARDING_STEP_NAMES", ({
  enumerable: true,
  get: function get() {
    return _onboardingTracker.ONBOARDING_STEP_NAMES;
  }
}));
Object.defineProperty(exports, "ONBOARDING_STORAGE_KEYS", ({
  enumerable: true,
  get: function get() {
    return _onboardingTracker.ONBOARDING_STORAGE_KEYS;
  }
}));
exports.OnboardingEventTracking = void 0;
var _onboardingTracker = _interopRequireWildcard(__webpack_require__(/*! ./modules/onboarding-tracker.js */ "../app/modules/onboarding/assets/js/utils/modules/onboarding-tracker.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var OnboardingEventTracking = exports.OnboardingEventTracking = _onboardingTracker.default;

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/use-button-action.js":
/*!**********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/use-button-action.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useButtonAction;
var _react = __webpack_require__(/*! react */ "react");
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
function useButtonAction(pageId, nextPage) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate;
  var navigate = (0, _router.useNavigate)();
  var handleAction = function handleAction(action) {
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, action);
    updateState(stateToUpdate);
    navigate('onboarding/' + nextPage);
  };
  return {
    state: state,
    handleAction: handleAction
  };
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/utils.js":
/*!**********************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/utils.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.setSelectedFeatureList = exports.safeDispatchEvent = exports.options = void 0;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * Checkboxes data.
 */
var options = exports.options = [{
  plan: 'essential',
  text: __('Templates & Theme Builder', 'elementor')
}, {
  plan: 'advanced',
  text: __('WooCommerce Builder', 'elementor')
}, {
  plan: 'essential',
  text: __('Lead Collection & Form Builder', 'elementor')
}, {
  plan: 'essential',
  text: __('Dynamic Content', 'elementor')
}, {
  plan: 'advanced',
  text: __('Popup Builder', 'elementor')
}, {
  plan: 'advanced',
  text: __('Custom Code & CSS', 'elementor')
}, {
  plan: 'essential',
  text: __('Motion Effects & Animations', 'elementor')
}, {
  plan: 'advanced',
  text: __('Notes & Collaboration', 'elementor')
}];

/**
 * Set the selected feature list.
 * @param {Object}   param0
 * @param {boolean}  param0.checked
 * @param {string}   param0.id
 * @param {string}   param0.text
 * @param {Object}   param0.selectedFeatures
 * @param {Function} param0.setSelectedFeatures
 */
var setSelectedFeatureList = exports.setSelectedFeatureList = function setSelectedFeatureList(_ref) {
  var checked = _ref.checked,
    id = _ref.id,
    text = _ref.text,
    selectedFeatures = _ref.selectedFeatures,
    setSelectedFeatures = _ref.setSelectedFeatures;
  var tier = id.split('-')[0];
  if (checked) {
    setSelectedFeatures(_objectSpread(_objectSpread({}, selectedFeatures), {}, (0, _defineProperty2.default)({}, tier, [].concat((0, _toConsumableArray2.default)(selectedFeatures[tier]), [text]))));
  } else {
    setSelectedFeatures(_objectSpread(_objectSpread({}, selectedFeatures), {}, (0, _defineProperty2.default)({}, tier, selectedFeatures[tier].filter(function (item) {
      return item !== text;
    }))));
  }
};
var safeDispatchEvent = exports.safeDispatchEvent = function safeDispatchEvent(eventName, eventData) {
  try {
    var _elementorCommon, _elementorCommon$disp;
    (_elementorCommon = elementorCommon) === null || _elementorCommon === void 0 || (_elementorCommon = _elementorCommon.eventsManager) === null || _elementorCommon === void 0 || (_elementorCommon$disp = _elementorCommon.dispatchEvent) === null || _elementorCommon$disp === void 0 || _elementorCommon$disp.call(_elementorCommon, eventName, eventData);
  } catch (error) {
    // Silently fail - don't let tracking break the user experience
  }
};

/***/ })

}]);
//# sourceMappingURL=onboarding.50bce36b17131b8d21b7.bundle.js.map
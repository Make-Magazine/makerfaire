/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/apps/onboarding/src/analytics/core-feature-ids.ts":
/*!********************************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/core-feature-ids.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CORE_FEATURE_IDS: function() { return /* reexport safe */ _steps_components_site_features_feature_options__WEBPACK_IMPORTED_MODULE_0__.CORE_FEATURE_IDS; }
/* harmony export */ });
/* harmony import */ var _steps_components_site_features_feature_options__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../steps/components/site-features/feature-options */ "./packages/apps/onboarding/src/steps/components/site-features/feature-options.ts");


/***/ }),

/***/ "./packages/apps/onboarding/src/analytics/event-queue.ts":
/*!***************************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/event-queue.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearEventQueue: function() { return /* binding */ clearEventQueue; },
/* harmony export */   enqueueEvent: function() { return /* binding */ enqueueEvent; },
/* harmony export */   getEventQueue: function() { return /* binding */ getEventQueue; }
/* harmony export */ });
const STORAGE_KEY = 'elementor_ob_event_queue';
function enqueueEvent(name, payload) {
  try {
    const queue = getEventQueue();
    queue.push({
      name,
      payload,
      timestamp: Date.now()
    });
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
  } catch {}
}
function getEventQueue() {
  try {
    const raw = sessionStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}
function clearEventQueue() {
  try {
    sessionStorage.removeItem(STORAGE_KEY);
  } catch {}
}

/***/ }),

/***/ "./packages/apps/onboarding/src/analytics/events.ts":
/*!**********************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/events.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EXPERIENCE_VALUE_MAP: function() { return /* binding */ EXPERIENCE_VALUE_MAP; },
/* harmony export */   OnboardingEventName: function() { return /* binding */ OnboardingEventName; },
/* harmony export */   PERSONA_VALUE_MAP: function() { return /* binding */ PERSONA_VALUE_MAP; },
/* harmony export */   STEP_NUMBERS: function() { return /* binding */ STEP_NUMBERS; },
/* harmony export */   STEP_SPEC_NAMES: function() { return /* binding */ STEP_SPEC_NAMES; },
/* harmony export */   TARGET_NAME_PERSONA: function() { return /* binding */ TARGET_NAME_PERSONA; },
/* harmony export */   THEME_VALUE_MAP: function() { return /* binding */ THEME_VALUE_MAP; }
/* harmony export */ });
const OnboardingEventName = {
  INITIALIZED: 'ob_onboarding_initialized',
  LOGIN_TYPE: 'ob_login_type',
  CONNECT: 'ob_connect',
  PRO_INSTALL: 'ob_pro_install',
  STEP_VIEWED: 'ob_step_viewed',
  PERSONA_SELECTED: 'ob_persona_selected',
  SITE_TOPIC_SELECTED: 'ob_site_topic_selected',
  EXPERIENCE_SELECTED: 'ob_experience_selected',
  THEME_SELECTED: 'ob_theme_selected',
  THEME_UNSELECTED: 'ob_theme_unselected',
  PRO_FEATURES_SELECTED: 'ob_pro_features_selected',
  BACK_CLICKED: 'ob_back_clicked',
  SKIP_CLICKED: 'ob_skip_clicked',
  UPGRADE_CLICKED: 'ob_upgrade_clicked',
  RESUME_ONBOARDING: 'ob_resume_onboarding',
  SITE_STARTER_SELECTED: 'ob_site_starter_selected',
  SUMMARY: 'ob_summary',
  ERROR_REPORTED: 'ob_error_reported'
};
const STEP_NUMBERS = {
  login: '0',
  pro_install: '0',
  theme_selection: '1',
  site_features: '1'
};
const TARGET_NAME_PERSONA = 'who_are_you_building_for';
const PERSONA_VALUE_MAP = {
  myself: 'myself_or_someone_i_know',
  business: 'my_business_or_workplace',
  client: 'a_client',
  exploring: 'just_exploring'
};
const EXPERIENCE_VALUE_MAP = {
  beginner: 'beginner',
  intermediate: 'intermediate',
  advanced: 'expert'
};
const THEME_VALUE_MAP = {
  'hello-elementor': 'hello'
};
const STEP_SPEC_NAMES = {
  login: 'login',
  pro_install: 'pro_install',
  building_for: 'who_are_you_building_for',
  site_about: 'what_is_your_site_about',
  experience_level: 'how_experienced_are_you',
  theme_selection: 'theme_install',
  site_features: 'pro_features'
};

/** Snapshot of onboarding state for ob_summary. */

/** Enforces target_type/target_name pairing for ob_error_reported. */

/***/ }),

/***/ "./packages/apps/onboarding/src/analytics/index.ts":
/*!*********************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/index.ts ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EXPERIENCE_VALUE_MAP: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.EXPERIENCE_VALUE_MAP; },
/* harmony export */   OnboardingEventName: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.OnboardingEventName; },
/* harmony export */   PERSONA_VALUE_MAP: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.PERSONA_VALUE_MAP; },
/* harmony export */   STEP_NUMBERS: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.STEP_NUMBERS; },
/* harmony export */   STEP_SPEC_NAMES: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.STEP_SPEC_NAMES; },
/* harmony export */   TARGET_NAME_PERSONA: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.TARGET_NAME_PERSONA; },
/* harmony export */   THEME_VALUE_MAP: function() { return /* reexport safe */ _events__WEBPACK_IMPORTED_MODULE_0__.THEME_VALUE_MAP; },
/* harmony export */   canSendEvents: function() { return /* reexport safe */ _init_tracking__WEBPACK_IMPORTED_MODULE_2__.canSendEvents; },
/* harmony export */   clearEventQueue: function() { return /* reexport safe */ _event_queue__WEBPACK_IMPORTED_MODULE_1__.clearEventQueue; },
/* harmony export */   enqueueEvent: function() { return /* reexport safe */ _event_queue__WEBPACK_IMPORTED_MODULE_1__.enqueueEvent; },
/* harmony export */   getEventQueue: function() { return /* reexport safe */ _event_queue__WEBPACK_IMPORTED_MODULE_1__.getEventQueue; },
/* harmony export */   initializeAndEnableTracking: function() { return /* reexport safe */ _init_tracking__WEBPACK_IMPORTED_MODULE_2__.initializeAndEnableTracking; },
/* harmony export */   setCanSendEvents: function() { return /* reexport safe */ _init_tracking__WEBPACK_IMPORTED_MODULE_2__.setCanSendEvents; },
/* harmony export */   updateLibraryConnectConfig: function() { return /* reexport safe */ _init_tracking__WEBPACK_IMPORTED_MODULE_2__.updateLibraryConnectConfig; }
/* harmony export */ });
/* harmony import */ var _events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./events */ "./packages/apps/onboarding/src/analytics/events.ts");
/* harmony import */ var _event_queue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./event-queue */ "./packages/apps/onboarding/src/analytics/event-queue.ts");
/* harmony import */ var _init_tracking__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./init-tracking */ "./packages/apps/onboarding/src/analytics/init-tracking.ts");




/***/ }),

/***/ "./packages/apps/onboarding/src/analytics/init-tracking.ts":
/*!*****************************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/init-tracking.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canSendEvents: function() { return /* reexport safe */ _elementor_events__WEBPACK_IMPORTED_MODULE_0__.canSendEvents; },
/* harmony export */   initializeAndEnableTracking: function() { return /* reexport safe */ _elementor_events__WEBPACK_IMPORTED_MODULE_0__.initializeAndEnableTracking; },
/* harmony export */   setCanSendEvents: function() { return /* reexport safe */ _elementor_events__WEBPACK_IMPORTED_MODULE_0__.setCanSendEvents; },
/* harmony export */   updateLibraryConnectConfig: function() { return /* binding */ updateLibraryConnectConfig; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);

function updateLibraryConnectConfig(data) {
  const extendedWindow = window;
  const config = extendedWindow.elementorCommon?.config;
  if (!config?.library_connect) {
    return;
  }
  const libraryConnectConfig = config.library_connect;
  libraryConnectConfig.is_connected = true;
  libraryConnectConfig.current_access_level = data.kits_access_level ?? data.access_level ?? 0;
  libraryConnectConfig.current_access_tier = data.access_tier;
  libraryConnectConfig.plan_type = data.plan_type;
  libraryConnectConfig.user_id = data.user_id ? String(data.user_id) : null;
}


/***/ }),

/***/ "./packages/apps/onboarding/src/analytics/onboarding-tracking.ts":
/*!***********************************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/onboarding-tracking.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   flushQueue: function() { return /* binding */ flushQueue; },
/* harmony export */   trackBackClicked: function() { return /* binding */ trackBackClicked; },
/* harmony export */   trackConnect: function() { return /* binding */ trackConnect; },
/* harmony export */   trackErrorReported: function() { return /* binding */ trackErrorReported; },
/* harmony export */   trackLoginType: function() { return /* binding */ trackLoginType; },
/* harmony export */   trackOnboardingInitialized: function() { return /* binding */ trackOnboardingInitialized; },
/* harmony export */   trackProFeaturesSelected: function() { return /* binding */ trackProFeaturesSelected; },
/* harmony export */   trackProInstall: function() { return /* binding */ trackProInstall; },
/* harmony export */   trackResumeOnboarding: function() { return /* binding */ trackResumeOnboarding; },
/* harmony export */   trackSiteStarterSelected: function() { return /* binding */ trackSiteStarterSelected; },
/* harmony export */   trackSkipClicked: function() { return /* binding */ trackSkipClicked; },
/* harmony export */   trackStepViewed: function() { return /* binding */ trackStepViewed; },
/* harmony export */   trackSummary: function() { return /* binding */ trackSummary; },
/* harmony export */   trackThemeSelected: function() { return /* binding */ trackThemeSelected; },
/* harmony export */   trackThemeUnselected: function() { return /* binding */ trackThemeUnselected; },
/* harmony export */   trackUpgradeClicked: function() { return /* binding */ trackUpgradeClicked; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _core_feature_ids__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./core-feature-ids */ "./packages/apps/onboarding/src/analytics/core-feature-ids.ts");
/* harmony import */ var _event_queue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./event-queue */ "./packages/apps/onboarding/src/analytics/event-queue.ts");
/* harmony import */ var _events__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./events */ "./packages/apps/onboarding/src/analytics/events.ts");




function dispatchDirectly(eventName, payload) {
  const {
    dispatchEvent
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  dispatchEvent?.(eventName, payload);
}
function trackEvent(isActive, eventName, payload) {
  const fullPayload = {
    app_type: 'editor',
    window_name: 'core_onboarding',
    ...payload
  };
  if (isActive && (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.canSendEvents)()) {
    dispatchDirectly(eventName, fullPayload);
  } else {
    (0,_event_queue__WEBPACK_IMPORTED_MODULE_2__.enqueueEvent)(eventName, fullPayload);
  }
}
function flushQueue() {
  const queued = (0,_event_queue__WEBPACK_IMPORTED_MODULE_2__.getEventQueue)();
  queued.sort((a, b) => a.timestamp - b.timestamp).forEach(event => {
    if (event.name && event.payload) {
      dispatchDirectly(event.name, event.payload);
    }
  });
  (0,_event_queue__WEBPACK_IMPORTED_MODULE_2__.clearEventQueue)();
}
function trackOnboardingInitialized(isActive) {
  (0,_event_queue__WEBPACK_IMPORTED_MODULE_2__.clearEventQueue)();
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.INITIALIZED, {
    interaction_type: 'load',
    target_type: 'loaded',
    target_name: 'onboarding_first_load',
    interaction_result: 'onboarding_loaded',
    target_location: 'onboarding',
    interaction_description: 'first step of the onboarding funnel'
  });
}
function trackLoginType(isActive, loginType) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.LOGIN_TYPE, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: 'login',
    interaction_result: loginType === 'guest' ? 'skip_and_onboarding_initialization' : 'login_option selected',
    target_value: loginType,
    target_location: 'onboarding',
    location_l1: 'login_step',
    interaction_description: 'user connect process loaded from onboarding'
  });
}
function trackConnect(isActive, success, error) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.CONNECT, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: 'connect',
    interaction_result: 'user_connect',
    target_value: success,
    target_location: 'onboarding',
    location_l1: 'connect_flow',
    interaction_description: 'user connect process loaded from onboarding',
    metadata: !success && error ? {
      error
    } : undefined
  });
}
function trackProInstall(isActive, action) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.PRO_INSTALL, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: action === 'install' ? 'install_pro_on_this_site' : "i'll_do_it_later",
    interaction_result: action === 'install' ? 'pro_installed_onboarding_initialization' : 'skip_and_onboarding_initialization',
    target_location: 'onboarding',
    location_l1: 'install_pro_step',
    state: action === 'install'
  });
}
function trackStepViewed(isActive, viewedStepId) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.STEP_VIEWED, {
    interaction_type: 'step_load',
    target_type: 'loaded',
    target_name: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_SPEC_NAMES[viewedStepId] ?? viewedStepId,
    interaction_result: 'step_load',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[viewedStepId],
    target_location: 'onboarding',
    location_l1: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[viewedStepId],
    interaction_description: 'onboarding step loaded'
  });
}
function trackThemeSelected(isActive, theme, source) {
  const isSiteFeatures = source === 'site_features';
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.THEME_SELECTED, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: isSiteFeatures ? 'continue_with_free' : 'continue_with_hello',
    interaction_result: 'theme_installed',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.THEME_VALUE_MAP[theme] ?? theme,
    target_location: 'onboarding',
    location_l1: isSiteFeatures ? 'pro_features' : 'select_theme',
    location_l2: isSiteFeatures ? _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS.site_features : _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS.theme_selection,
    interaction_description: isSiteFeatures ? 'user installed hello theme on pro features step' : 'user installed a certain theme'
  });
}
function trackThemeUnselected(isActive) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.THEME_UNSELECTED, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: 'hello_theme',
    interaction_result: 'theme_install_skipped',
    target_value: 'hello_theme',
    target_location: 'onboarding',
    location_l1: 'pro_features',
    location_l2: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS.site_features,
    interaction_description: 'user unselected hello theme box and continued'
  });
}
function trackProFeaturesSelected(isActive, params) {
  const featuresWithoutCore = params.features.filter(id => !_core_feature_ids__WEBPACK_IMPORTED_MODULE_1__.CORE_FEATURE_IDS.has(id));
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.PRO_FEATURES_SELECTED, {
    interaction_type: 'click',
    target_type: 'cards',
    target_name: params.targetName,
    interaction_result: params.targetName === 'continue_with_free' ? 'finish_onboarding' : 'pricing_page',
    target_value: featuresWithoutCore,
    target_location: 'onboarding',
    location_l1: 'pro_features',
    location_l2: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS.site_features,
    interaction_description: 'user selected pro features and continued'
  });
}
function trackBackClicked(isActive, currentStepId) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.BACK_CLICKED, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: 'back',
    interaction_result: 'redirect_back',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[currentStepId],
    target_location: 'onboarding_navigation',
    location_l1: 'footer',
    location_l2: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[currentStepId]
  });
}
function trackSkipClicked(isActive, currentStepId) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.SKIP_CLICKED, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: 'skip',
    interaction_result: 'skip_step',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[currentStepId],
    target_location: 'onboarding_navigation',
    location_l1: 'footer',
    location_l2: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[currentStepId]
  });
}
function trackUpgradeClicked(isActive, currentStepId) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.UPGRADE_CLICKED, {
    interaction_type: 'click',
    target_type: 'button',
    target_name: 'upgrade',
    interaction_result: 'pricing_page_opened',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[currentStepId],
    target_location: 'onboarding_navigation',
    location_l1: 'header',
    location_l2: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[currentStepId]
  });
}
function trackResumeOnboarding(isActive, resumeStepId) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.RESUME_ONBOARDING, {
    interaction_type: 'onboarding_load',
    target_type: 'reloaded',
    target_name: 'reloaded',
    interaction_result: 'onboarding_load',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[resumeStepId],
    target_location: 'onboarding',
    location_l1: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[resumeStepId],
    interaction_description: 'onboarding step loaded'
  });
}
function trackSiteStarterSelected(isActive, params) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.SITE_STARTER_SELECTED, {
    window_name: 'editor',
    interaction_type: 'click',
    target_type: 'card',
    target_name: params.targetName,
    interaction_result: params.interactionResult,
    target_location: 'start_building',
    location_l1: '',
    interaction_description: 'user selected or ignored site starters on first load of canvas'
  });
}
function toSummaryValue(v) {
  if (v === null || v === undefined || v === '') {
    return 'skip';
  }
  if (Array.isArray(v) && v.length === 0) {
    return 'skip';
  }
  return v;
}
function trackSummary(isActive, snapshot) {
  const proFeaturesOnly = (snapshot.choices.site_features ?? []).filter(id => !_core_feature_ids__WEBPACK_IMPORTED_MODULE_1__.CORE_FEATURE_IDS.has(id));
  const metadata = [{
    key: 'login_type',
    value: toSummaryValue(snapshot.isGuest ? 'guest' : 'elementor_login')
  }, {
    key: 'connect',
    value: snapshot.isConnected
  }, {
    key: 'pro_install',
    value: toSummaryValue(snapshot.proInstall ?? false)
  }, {
    key: 'persona',
    value: toSummaryValue(snapshot.choices.building_for !== null && snapshot.choices.building_for !== undefined ? _events__WEBPACK_IMPORTED_MODULE_3__.PERSONA_VALUE_MAP[snapshot.choices.building_for] ?? snapshot.choices.building_for : null)
  }, {
    key: 'site_topic',
    value: toSummaryValue(snapshot.choices.site_about ?? [])
  }, {
    key: 'experience_level',
    value: toSummaryValue(snapshot.choices.experience_level !== null && snapshot.choices.experience_level !== undefined ? _events__WEBPACK_IMPORTED_MODULE_3__.EXPERIENCE_VALUE_MAP[snapshot.choices.experience_level] ?? snapshot.choices.experience_level : null)
  }, {
    key: 'theme_installed',
    value: (() => {
      if (snapshot.choices.theme_selection !== null && snapshot.choices.theme_selection !== undefined) {
        return _events__WEBPACK_IMPORTED_MODULE_3__.THEME_VALUE_MAP[snapshot.choices.theme_selection] ?? snapshot.choices.theme_selection;
      }
      if ((snapshot.choices.site_features ?? []).includes('hello_theme')) {
        return 'hello';
      }
      return 'none';
    })()
  }, {
    key: 'pro_features',
    value: toSummaryValue(proFeaturesOnly.length ? proFeaturesOnly : 'skip')
  }, {
    key: 'steps_completed',
    value: snapshot.completedSteps.length
  }];
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.SUMMARY, {
    interaction_type: 'onboarding_complete',
    target_type: 'summary',
    target_name: 'ob_summary',
    interaction_result: 'onboarding_final_choices',
    target_location: 'onboarding',
    location_l1: 'summary',
    interaction_description: 'trigger event upon onboarding completion or when flow is closed abruptly',
    metadata: {
      summary: metadata
    }
  });
}
function trackErrorReported(isActive, params) {
  trackEvent(isActive, _events__WEBPACK_IMPORTED_MODULE_3__.OnboardingEventName.ERROR_REPORTED, {
    interaction_type: 'action_failed',
    target_type: params.targetType,
    target_name: params.targetName,
    interaction_result: 'error_reported',
    target_value: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[params.stepId] ?? params.stepId,
    target_location: 'onboarding',
    location_l1: _events__WEBPACK_IMPORTED_MODULE_3__.STEP_NUMBERS[params.stepId] ?? params.stepId,
    interaction_description: 'onboarding step loaded',
    metadata: {
      error_title: params.errorBody
    }
  });
}

/***/ }),

/***/ "./packages/apps/onboarding/src/analytics/tracking-context.tsx":
/*!*********************************************************************!*\
  !*** ./packages/apps/onboarding/src/analytics/tracking-context.tsx ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TrackingProvider: function() { return /* binding */ TrackingProvider; },
/* harmony export */   useTrackingState: function() { return /* binding */ useTrackingState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store_selectors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../store/selectors */ "./packages/apps/onboarding/src/store/selectors.ts");
/* harmony import */ var _init_tracking__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./init-tracking */ "./packages/apps/onboarding/src/analytics/init-tracking.ts");
/* harmony import */ var _onboarding_tracking__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./onboarding-tracking */ "./packages/apps/onboarding/src/analytics/onboarding-tracking.ts");






const TrackingContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function TrackingProvider({
  children
}) {
  const [isActive, setIsActive] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const activate = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => setIsActive(true), []);
  const isConnected = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store_selectors__WEBPACK_IMPORTED_MODULE_2__.selectIsConnected);
  const isGuest = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store_selectors__WEBPACK_IMPORTED_MODULE_2__.selectIsGuest);
  const hasActivated = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (hasActivated.current || isActive || !isConnected || isGuest) {
      return;
    }
    if ((0,_init_tracking__WEBPACK_IMPORTED_MODULE_3__.canSendEvents)()) {
      hasActivated.current = true;
      (0,_init_tracking__WEBPACK_IMPORTED_MODULE_3__.initializeAndEnableTracking)(mp => {
        mp?.set_config?.({
          api_transport: 'sendbeacon'
        });
        setIsActive(true);
        (0,_onboarding_tracking__WEBPACK_IMPORTED_MODULE_4__.flushQueue)();
      });
    }
  }, [isConnected, isGuest, isActive]);
  const value = {
    isActive,
    activate
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TrackingContext.Provider, {
    value: value
  }, children);
}
function useTrackingState() {
  const ctx = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(TrackingContext);
  if (!ctx) {
    throw new Error('useTrackingState must be used within TrackingProvider');
  }
  return ctx;
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/app-content.tsx":
/*!*****************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/app-content.tsx ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AppContent: function() { return /* binding */ AppContent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _analytics__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../analytics */ "./packages/apps/onboarding/src/analytics/index.ts");
/* harmony import */ var _hooks_use_check_pro_install_screen__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-check-pro-install-screen */ "./packages/apps/onboarding/src/hooks/use-check-pro-install-screen.ts");
/* harmony import */ var _hooks_use_elementor_connect__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/use-elementor-connect */ "./packages/apps/onboarding/src/hooks/use-elementor-connect.ts");
/* harmony import */ var _hooks_use_install_theme__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../hooks/use-install-theme */ "./packages/apps/onboarding/src/hooks/use-install-theme.ts");
/* harmony import */ var _hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../hooks/use-onboarding */ "./packages/apps/onboarding/src/hooks/use-onboarding.ts");
/* harmony import */ var _hooks_use_onboarding_event__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../hooks/use-onboarding-event */ "./packages/apps/onboarding/src/hooks/use-onboarding-event.ts");
/* harmony import */ var _hooks_use_update_choices__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../hooks/use-update-choices */ "./packages/apps/onboarding/src/hooks/use-update-choices.ts");
/* harmony import */ var _hooks_use_update_progress__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../hooks/use-update-progress */ "./packages/apps/onboarding/src/hooks/use-update-progress.ts");
/* harmony import */ var _hooks_use_video_preload__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../hooks/use-video-preload */ "./packages/apps/onboarding/src/hooks/use-video-preload.ts");
/* harmony import */ var _steps_screens_login__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../steps/screens/login */ "./packages/apps/onboarding/src/steps/screens/login.tsx");
/* harmony import */ var _steps_screens_pro_install__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../steps/screens/pro-install */ "./packages/apps/onboarding/src/steps/screens/pro-install.tsx");
/* harmony import */ var _steps_screens_site_features__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../steps/screens/site-features */ "./packages/apps/onboarding/src/steps/screens/site-features.tsx");
/* harmony import */ var _steps_screens_theme_selection__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../steps/screens/theme-selection */ "./packages/apps/onboarding/src/steps/screens/theme-selection.tsx");
/* harmony import */ var _steps_step_visuals__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../steps/step-visuals */ "./packages/apps/onboarding/src/steps/step-visuals.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../types */ "./packages/apps/onboarding/src/types.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _toast_toast_context__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./toast/toast-context */ "./packages/apps/onboarding/src/components/toast/toast-context.tsx");
/* harmony import */ var _ui_base_layout__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./ui/base-layout */ "./packages/apps/onboarding/src/components/ui/base-layout.tsx");
/* harmony import */ var _ui_completion_screen__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./ui/completion-screen */ "./packages/apps/onboarding/src/components/ui/completion-screen.tsx");
/* harmony import */ var _ui_footer__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./ui/footer */ "./packages/apps/onboarding/src/components/ui/footer.tsx");
/* harmony import */ var _ui_footer_actions__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./ui/footer-actions */ "./packages/apps/onboarding/src/components/ui/footer-actions.tsx");
/* harmony import */ var _ui_split_layout__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./ui/split-layout */ "./packages/apps/onboarding/src/components/ui/split-layout.tsx");
/* harmony import */ var _ui_top_bar__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./ui/top-bar */ "./packages/apps/onboarding/src/components/ui/top-bar.tsx");
/* harmony import */ var _ui_top_bar_content__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./ui/top-bar-content */ "./packages/apps/onboarding/src/components/ui/top-bar-content.tsx");




























const isChoiceEmpty = choice => {
  return choice === null || choice === undefined || Array.isArray(choice) && choice.length === 0;
};
const isContinueDisabled = (stepId, isLast, choiceForStep) => {
  if (stepId === _types__WEBPACK_IMPORTED_MODULE_17__.StepId.THEME_SELECTION) {
    return false;
  }
  if (isLast) {
    return false;
  }
  return isChoiceEmpty(choiceForStep);
};
function AppContent({
  onClose
}) {
  const {
    stepId,
    stepIndex,
    isFirst,
    isLast,
    totalSteps,
    resumeStepIdForTracking,
    isLoading,
    isConnected,
    hasPassedLogin,
    shouldShowProInstall,
    choices,
    completedSteps,
    urls,
    actions,
    isGuest
  } = (0,_hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_7__.useOnboarding)();
  const [isCompleting, setIsCompleting] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const isCompletingRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  const {
    showToast
  } = (0,_toast_toast_context__WEBPACK_IMPORTED_MODULE_19__.useToast)();
  (0,_hooks_use_video_preload__WEBPACK_IMPORTED_MODULE_11__.useVideoPreload)();
  const updateProgress = (0,_hooks_use_update_progress__WEBPACK_IMPORTED_MODULE_10__.useUpdateProgress)();
  const updateChoices = (0,_hooks_use_update_choices__WEBPACK_IMPORTED_MODULE_9__.useUpdateChoices)();
  const installTheme = (0,_hooks_use_install_theme__WEBPACK_IMPORTED_MODULE_6__.useInstallTheme)();
  const {
    trackOnboardingInitialized,
    trackLoginType,
    trackConnect,
    trackStepViewed,
    trackProFeaturesSelected,
    trackBackClicked,
    trackSkipClicked,
    trackUpgradeClicked,
    trackResumeOnboarding,
    trackSummary,
    trackThemeSelected,
    trackErrorReported,
    activateTracking,
    flushQueue
  } = (0,_hooks_use_onboarding_event__WEBPACK_IMPORTED_MODULE_8__.useOnboardingEvent)();
  const hasTrackedInit = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!hasTrackedInit.current) {
      hasTrackedInit.current = true;
      trackOnboardingInitialized();
      if (resumeStepIdForTracking) {
        trackResumeOnboarding(resumeStepIdForTracking);
        actions.clearResumeStepIdForTracking();
      } else {
        trackStepViewed('login');
      }
      return;
    }
    if (hasPassedLogin && stepId && !isCompletingRef.current) {
      trackStepViewed(stepId);
    }
  }, [stepId, resumeStepIdForTracking, hasPassedLogin, actions, trackOnboardingInitialized, trackResumeOnboarding, trackStepViewed]);
  const checkProInstallScreen = (0,_hooks_use_check_pro_install_screen__WEBPACK_IMPORTED_MODULE_4__.useCheckProInstallScreen)();
  const handleConnectSuccess = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async (data, loginType) => {
    trackConnect(true);
    trackLoginType(loginType);
    const shouldEnableTracking = data.tracking_opted_in || (0,_analytics__WEBPACK_IMPORTED_MODULE_3__.canSendEvents)();
    if (data.tracking_opted_in) {
      (0,_analytics__WEBPACK_IMPORTED_MODULE_3__.setCanSendEvents)(true);
    }
    (0,_analytics__WEBPACK_IMPORTED_MODULE_3__.updateLibraryConnectConfig)(data);
    if (shouldEnableTracking) {
      (0,_analytics__WEBPACK_IMPORTED_MODULE_3__.initializeAndEnableTracking)(mp => {
        mp?.set_config?.({
          api_transport: 'sendbeacon'
        });
        activateTracking();
        flushQueue();
      });
    }
    const result = await checkProInstallScreen();
    actions.setShouldShowProInstallScreen(result.shouldShowProInstallScreen);
    actions.setConnected(true);
  }, [actions, checkProInstallScreen, trackConnect, trackLoginType, activateTracking, flushQueue]);
  const handleConnect = (0,_hooks_use_elementor_connect__WEBPACK_IMPORTED_MODULE_5__.useElementorConnect)({
    connectUrl: urls.connect,
    onSuccess: data => handleConnectSuccess(data, 'elementor_login')
  });
  const handleSignUp = (0,_hooks_use_elementor_connect__WEBPACK_IMPORTED_MODULE_5__.useElementorConnect)({
    connectUrl: urls.signUp,
    onSuccess: data => handleConnectSuccess(data, 'social_login')
  });
  function handleContinueAsGuest(event) {
    event.preventDefault();
    trackLoginType('guest');
    actions.setGuest(true);
  }
  const handleClose = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    trackSummary({
      choices,
      completedSteps: [...completedSteps],
      isConnected,
      isGuest
    });
    window.dispatchEvent(new CustomEvent('onboarding-user-exit'));
    updateProgress.mutate({
      user_exit: true
    }, {
      onSuccess: () => {
        actions.setExitType('user_exit');
        onClose?.();
      },
      onError: () => {
        actions.setExitType('user_exit');
        onClose?.();
      }
    });
  }, [actions, choices, completedSteps, isConnected, isGuest, onClose, trackSummary, updateProgress]);
  function handleBack() {
    trackBackClicked(stepId);
    if (isFirst) {
      actions.setGuest(false);
    } else {
      actions.prevStep();
    }
  }
  const redirectToNewPage = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    const redirectUrl = urls.createNewPage || urls.editor || urls.dashboard;
    const mp = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_1__.getMixpanel)().getMixpanelInstance?.();
    mp?.request_batchers?.events?.flush?.();
    window.location.href = redirectUrl;
  }, [urls]);
  const completeAndRedirect = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    updateProgress.mutate({
      complete_step: stepId,
      complete: true,
      step_index: stepIndex,
      total_steps: totalSteps
    }, {
      onSuccess: redirectToNewPage,
      onError: () => {
        redirectToNewPage();
      }
    });
  }, [updateProgress, stepId, stepIndex, totalSteps, redirectToNewPage]);
  const handleSkip = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    trackSkipClicked(stepId);
    if (isLast) {
      trackSummary({
        choices,
        completedSteps: [...completedSteps, stepId],
        isConnected,
        isGuest
      });
      isCompletingRef.current = true;
      setIsCompleting(true);
      updateProgress.mutate({
        skip_step: true,
        complete: true,
        step_index: stepIndex,
        total_steps: totalSteps
      }, {
        onSuccess: redirectToNewPage,
        onError: () => {
          redirectToNewPage();
        }
      });
      return;
    }
    updateProgress.mutate({
      skip_step: true,
      step_index: stepIndex,
      total_steps: totalSteps
    }, {
      onSuccess: () => {
        actions.nextStep();
      },
      onError: () => {
        actions.nextStep();
      }
    });
  }, [actions, choices, completedSteps, isConnected, isGuest, isLast, stepId, stepIndex, totalSteps, trackSkipClicked, trackSummary, updateProgress, redirectToNewPage]);
  const saveChoicesFireAndForget = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(choiceData => {
    updateChoices.mutate(choiceData);
  }, [updateChoices]);
  const handleContinue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(directChoice => {
    if (stepId === _types__WEBPACK_IMPORTED_MODULE_17__.StepId.SITE_FEATURES) {
      trackProFeaturesSelected({
        targetName: 'continue_with_free',
        features: choices.site_features || []
      });
    }
    let effectiveDirectChoice = directChoice;
    if (stepId === _types__WEBPACK_IMPORTED_MODULE_17__.StepId.THEME_SELECTION && !effectiveDirectChoice) {
      effectiveDirectChoice = {
        theme_selection: 'hello-elementor'
      };
    }
    const storedChoice = choices[stepId];
    const choiceData = effectiveDirectChoice ?? (isChoiceEmpty(storedChoice) ? null : {
      [stepId]: storedChoice
    });
    if (choiceData) {
      saveChoicesFireAndForget(choiceData);
    }
    if (stepId === _types__WEBPACK_IMPORTED_MODULE_17__.StepId.THEME_SELECTION) {
      const themeSlug = choiceData?.theme_selection ?? choices.theme_selection ?? 'hello-elementor';
      if (themeSlug && isLast) {
        trackThemeSelected(themeSlug, 'theme_selection');
        isCompletingRef.current = true;
        setIsCompleting(true);
        installTheme.mutate(themeSlug, {
          onSuccess: completeAndRedirect,
          onError: error => {
            trackErrorReported({
              targetType: 'install',
              targetName: 'continue_with_hello',
              stepId: 'theme_selection',
              errorBody: error instanceof Error ? error.message : 'Failed to install theme'
            });
            showToast((0,_utils_translations__WEBPACK_IMPORTED_MODULE_18__.t)('error.theme_install_failed'));
            completeAndRedirect();
          }
        });
        return;
      }
      if (themeSlug) {
        trackThemeSelected(themeSlug, 'theme_selection');
        installTheme.mutate(themeSlug, {
          onError: error => {
            trackErrorReported({
              targetType: 'install',
              targetName: 'continue_with_hello',
              stepId: 'theme_selection',
              errorBody: error instanceof Error ? error.message : 'Failed to install theme'
            });
            showToast((0,_utils_translations__WEBPACK_IMPORTED_MODULE_18__.t)('error.theme_install_failed'));
          }
        });
      }
    }
    if (isLast) {
      trackSummary({
        choices,
        completedSteps: [...completedSteps, stepId],
        isConnected,
        isGuest
      });
      isCompletingRef.current = true;
      setIsCompleting(true);
      completeAndRedirect();
      return;
    }
    updateProgress.mutate({
      complete_step: stepId,
      step_index: stepIndex,
      total_steps: totalSteps
    }, {
      onSuccess: () => {
        actions.completeStep(stepId);
        actions.nextStep();
      },
      onError: () => {
        actions.completeStep(stepId);
        actions.nextStep();
      }
    });
  }, [actions, choices, completedSteps, isConnected, isGuest, isLast, stepId, stepIndex, totalSteps, updateProgress, saveChoicesFireAndForget, installTheme, showToast, completeAndRedirect, trackErrorReported, trackProFeaturesSelected, trackSummary, trackThemeSelected]);
  const rightPanelConfig = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_steps_step_visuals__WEBPACK_IMPORTED_MODULE_16__.getStepVisualConfig)(stepId), [stepId]);
  const isPending = updateProgress.isPending || isLoading;
  const choiceForStep = choices[stepId];
  const continueDisabled = isContinueDisabled(stepId, isLast, choiceForStep);
  const isBackDisabled = isFirst && isConnected;
  const getContinueLabel = () => {
    if (stepId === _types__WEBPACK_IMPORTED_MODULE_17__.StepId.THEME_SELECTION && !completedSteps.includes(_types__WEBPACK_IMPORTED_MODULE_17__.StepId.THEME_SELECTION)) {
      return (0,_utils_translations__WEBPACK_IMPORTED_MODULE_18__.t)('steps.theme_selection.v2.continue_with_theme');
    }
    if (stepId === _types__WEBPACK_IMPORTED_MODULE_17__.StepId.SITE_FEATURES && !completedSteps.includes(_types__WEBPACK_IMPORTED_MODULE_17__.StepId.SITE_FEATURES)) {
      return (0,_utils_translations__WEBPACK_IMPORTED_MODULE_18__.t)('steps.site_features.continue_with_free');
    }
    if (isLast) {
      return (0,_utils_translations__WEBPACK_IMPORTED_MODULE_18__.t)('common.finish');
    }
    return (0,_utils_translations__WEBPACK_IMPORTED_MODULE_18__.t)('common.continue');
  };
  const renderStepContent = () => {
    switch (stepId) {
      case _types__WEBPACK_IMPORTED_MODULE_17__.StepId.THEME_SELECTION:
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_steps_screens_theme_selection__WEBPACK_IMPORTED_MODULE_15__.ThemeSelection, null);
      case _types__WEBPACK_IMPORTED_MODULE_17__.StepId.SITE_FEATURES:
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_steps_screens_site_features__WEBPACK_IMPORTED_MODULE_14__.SiteFeatures, null);
      default:
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
          sx: {
            flex: 1,
            width: '100%'
          }
        });
    }
  };
  if (isCompleting) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_completion_screen__WEBPACK_IMPORTED_MODULE_21__.CompletionScreen, null);
  }
  if (!hasPassedLogin) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_base_layout__WEBPACK_IMPORTED_MODULE_20__.BaseLayout, {
      topBar: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_top_bar__WEBPACK_IMPORTED_MODULE_25__.TopBar, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_top_bar_content__WEBPACK_IMPORTED_MODULE_26__.TopBarContent, {
        showUpgrade: true,
        showClose: false,
        onUpgrade: () => {
          trackUpgradeClicked('login');
          window.open(urls.upgradeUrl, '_blank');
        }
      }))
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_steps_screens_login__WEBPACK_IMPORTED_MODULE_12__.Login, {
      onConnect: handleConnect,
      onSignUp: handleSignUp,
      onContinueAsGuest: handleContinueAsGuest
    }));
  }
  if (shouldShowProInstall) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_base_layout__WEBPACK_IMPORTED_MODULE_20__.BaseLayout, {
      topBar: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_top_bar__WEBPACK_IMPORTED_MODULE_25__.TopBar, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_top_bar_content__WEBPACK_IMPORTED_MODULE_26__.TopBarContent, {
        showUpgrade: false,
        showClose: false
      }))
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_steps_screens_pro_install__WEBPACK_IMPORTED_MODULE_13__.ProInstall, null));
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_base_layout__WEBPACK_IMPORTED_MODULE_20__.BaseLayout, {
    testId: "onboarding-steps",
    topBar: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_top_bar__WEBPACK_IMPORTED_MODULE_25__.TopBar, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_top_bar_content__WEBPACK_IMPORTED_MODULE_26__.TopBarContent, {
      showClose: false,
      onClose: handleClose,
      onUpgrade: () => {
        trackUpgradeClicked(stepId);
        window.open(urls.upgradeUrl, '_blank');
      }
    })),
    footer: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_footer__WEBPACK_IMPORTED_MODULE_22__.Footer, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_footer_actions__WEBPACK_IMPORTED_MODULE_23__.FooterActions, {
      showBack: true,
      showSkip: true,
      showContinue: true,
      isBackDisabled: isBackDisabled,
      continueLabel: getContinueLabel(),
      continueDisabled: continueDisabled,
      continueLoading: isPending,
      onBack: handleBack,
      onSkip: handleSkip,
      onContinue: () => handleContinue()
    }))
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_split_layout__WEBPACK_IMPORTED_MODULE_24__.SplitLayout, {
    left: renderStepContent(),
    rightConfig: rightPanelConfig
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/app.tsx":
/*!*********************************************************!*\
  !*** ./packages/apps/onboarding/src/components/app.tsx ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* binding */ App; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _analytics_tracking_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../analytics/tracking-context */ "./packages/apps/onboarding/src/analytics/tracking-context.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../store */ "./packages/apps/onboarding/src/store/index.ts");
/* harmony import */ var _app_content__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./app-content */ "./packages/apps/onboarding/src/components/app-content.tsx");
/* harmony import */ var _toast_toast_context__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./toast/toast-context */ "./packages/apps/onboarding/src/components/toast/toast-context.tsx");









function resolveColorScheme(preference) {
  if (preference === 'dark') {
    return 'dark';
  }
  if (preference === 'light') {
    return 'light';
  }
  const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches;
  return prefersDark ? 'dark' : 'light';
}
function App(props) {
  const store = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    (0,_store__WEBPACK_IMPORTED_MODULE_5__.registerOnboardingSlice)();
    let existingStore = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__getStore)();
    if (!existingStore) {
      existingStore = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__createStore)();
    }
    existingStore.dispatch((0,_store__WEBPACK_IMPORTED_MODULE_5__.initFromConfig)());
    return existingStore;
  }, []);
  const queryClient = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_elementor_query__WEBPACK_IMPORTED_MODULE_1__.createQueryClient)(), []);
  const uiTheme = window.elementorAppConfig?.onboarding?.uiTheme ?? 'auto';
  const colorScheme = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => resolveColorScheme(uiTheme), [uiTheme]);
  const isRtl = window.elementorCommon?.config?.isRTL ?? false;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_store__WEBPACK_IMPORTED_MODULE_2__.__StoreProvider, {
    store: store
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_query__WEBPACK_IMPORTED_MODULE_1__.QueryClientProvider, {
    client: queryClient
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DirectionProvider, {
    rtl: isRtl
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ThemeProvider, {
    colorScheme: colorScheme,
    palette: "argon-beta"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_toast_toast_context__WEBPACK_IMPORTED_MODULE_7__.ToastProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_analytics_tracking_context__WEBPACK_IMPORTED_MODULE_4__.TrackingProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_app_content__WEBPACK_IMPORTED_MODULE_6__.AppContent, props)))))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/fullscreen-card.tsx":
/*!*********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/fullscreen-card.tsx ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Card: function() { return /* binding */ Card; },
/* harmony export */   FullscreenCard: function() { return /* binding */ FullscreenCard; },
/* harmony export */   PrimaryButton: function() { return /* binding */ PrimaryButton; },
/* harmony export */   SecondaryButton: function() { return /* binding */ SecondaryButton; },
/* harmony export */   SocialIconWrapper: function() { return /* binding */ SocialIconWrapper; },
/* harmony export */   TextButton: function() { return /* binding */ TextButton; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _steps_step_visuals__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../steps/step-visuals */ "./packages/apps/onboarding/src/steps/step-visuals.ts");



const BACKDROP_OPACITY = 0.6;
const FullscreenCardRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => prop !== 'background'
})(({
  theme,
  background
}) => ({
  position: 'relative',
  minHeight: '100%',
  width: '100%',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  padding: theme.spacing(4),
  background
}));
const Backdrop = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  position: 'absolute',
  inset: 0,
  backgroundColor: theme.palette.mode === 'dark' ? theme.palette.common.black : theme.palette.text.primary,
  opacity: BACKDROP_OPACITY
}));
const Card = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Paper)(({
  theme
}) => ({
  width: 512,
  maxWidth: '90%',
  padding: theme.spacing(6, 6, 5),
  borderRadius: theme.shape.borderRadius * 2,
  boxShadow: theme.shadows[24],
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  gap: theme.spacing(3),
  position: 'relative',
  zIndex: 1
}));
const PrimaryButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button)(({
  theme
}) => ({
  borderRadius: theme.shape.borderRadius,
  textTransform: 'none',
  fontSize: theme.typography.pxToRem(15),
  fontWeight: 500,
  letterSpacing: '0.46px',
  lineHeight: theme.typography.pxToRem(26),
  padding: theme.spacing(1, 2.75),
  minHeight: theme.spacing(6)
}));
const SecondaryButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Button)(({
  theme
}) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  flexWrap: 'wrap',
  gap: theme.spacing(1),
  border: `1px solid ${theme.palette.divider}`,
  borderRadius: theme.shape.borderRadius,
  textTransform: 'none',
  fontSize: theme.typography.pxToRem(15),
  fontWeight: 500,
  letterSpacing: '0.46px',
  lineHeight: theme.typography.pxToRem(26),
  padding: theme.spacing(1, 3)
}));
const SocialIconWrapper = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Paper)(({
  theme
}) => ({
  backgroundColor: theme.palette.background.paper,
  backgroundImage: theme.palette.mode === 'dark' ? 'linear-gradient(rgba(255, 255, 255, 0.165), rgba(255, 255, 255, 0.165))' : 'none',
  width: theme.spacing(3.5),
  height: theme.spacing(3.5),
  borderRadius: '50%',
  border: `1px solid ${theme.palette.divider}`,
  boxShadow: 'none',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  marginLeft: theme.spacing(-1),
  '&:first-of-type': {
    marginLeft: 0,
    zIndex: 1
  },
  '&:nth-of-type(2)': {
    zIndex: 2
  },
  '&:nth-of-type(3)': {
    zIndex: 3
  },
  '& svg': {
    width: theme.spacing(2.5),
    height: theme.spacing(2.5)
  }
}));
const TextButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Link)(({
  theme
}) => ({
  textTransform: 'none',
  fontSize: theme.typography.pxToRem(13),
  fontWeight: 500,
  letterSpacing: '0.46px',
  lineHeight: theme.typography.pxToRem(22)
}));
function FullscreenCard({
  children,
  'data-testid': testId
}) {
  const {
    background
  } = (0,_steps_step_visuals__WEBPACK_IMPORTED_MODULE_2__.getLoginVisualConfig)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FullscreenCardRoot, {
    background: background,
    "data-testid": testId
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Backdrop, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Card, {
    elevation: 24
  }, children));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/index.ts":
/*!**********************************************************!*\
  !*** ./packages/apps/onboarding/src/components/index.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* reexport safe */ _app__WEBPACK_IMPORTED_MODULE_0__.App; }
/* harmony export */ });
/* harmony import */ var _app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./app */ "./packages/apps/onboarding/src/components/app.tsx");


/***/ }),

/***/ "./packages/apps/onboarding/src/components/theme-selection/constants.ts":
/*!******************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/theme-selection/constants.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HELLO_THEME: function() { return /* binding */ HELLO_THEME; },
/* harmony export */   getGreetingText: function() { return /* binding */ getGreetingText; }
/* harmony export */ });
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");

const HELLO_THEME = {
  slug: 'hello-elementor',
  labelKey: 'steps.theme_selection.theme_hello_label',
  descriptionKey: 'steps.theme_selection.theme_hello_description',
  previewBgColor: '#f6f6f6'
};
function getGreetingText(experienceLevel) {
  if (experienceLevel === 'beginner') {
    return (0,_utils_translations__WEBPACK_IMPORTED_MODULE_0__.t)('steps.theme_selection.greeting_beginner');
  }
  return (0,_utils_translations__WEBPACK_IMPORTED_MODULE_0__.t)('steps.theme_selection.greeting_default');
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/theme-selection/hello-theme-preview.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/theme-selection/hello-theme-preview.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HelloThemePreview: function() { return /* binding */ HelloThemePreview; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../icons */ "./packages/apps/onboarding/src/icons/index.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _styled_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./styled-components */ "./packages/apps/onboarding/src/components/theme-selection/styled-components.ts");






const HELLO_BADGE_BG_COLOR = '#ED01EE';
const LEFT_CARD_SIZE = 120;
const RIGHT_CARD_SIZE = 144;
const CARDS_OVERLAP = 15;
const PLUS_SIZE = 32;
const PreviewRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)({
  position: 'relative',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center'
});
const ElementorPlaceholderCard = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)(({
  theme
}) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  inlineSize: LEFT_CARD_SIZE,
  blockSize: LEFT_CARD_SIZE,
  borderRadius: theme.spacing(1.25),
  border: `2px dashed ${theme.palette.divider}`,
  backgroundColor: '#F4F4F4',
  flexShrink: 0
}));
const HelloCard = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)(({
  theme
}) => ({
  position: 'relative',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'flex-start',
  gap: theme.spacing(1.5),
  inlineSize: RIGHT_CARD_SIZE,
  blockSize: RIGHT_CARD_SIZE,
  paddingBlockStart: theme.spacing(5.5),
  paddingBlockEnd: theme.spacing(2.5),
  borderRadius: theme.spacing(1.25),
  border: `2px solid ${theme.palette.text.primary}`,
  backgroundColor: theme.palette.background.paper,
  marginInlineStart: `-${CARDS_OVERLAP}px`,
  flexShrink: 0,
  zIndex: 1
}));
const HelloBadge = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)(({
  theme
}) => ({
  position: 'absolute',
  insetBlockStart: theme.spacing(-1.5),
  insetInlineEnd: theme.spacing(-1.5),
  paddingBlock: theme.spacing(0.375),
  paddingInline: theme.spacing(1),
  borderRadius: theme.spacing(2.5),
  backgroundColor: HELLO_BADGE_BG_COLOR,
  color: theme.palette.common.white,
  zIndex: 2
}));
const PlusOverlay = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)({
  position: 'absolute',
  insetBlockStart: '50%',
  insetInlineStart: `${LEFT_CARD_SIZE - CARDS_OVERLAP}px`,
  transform: 'translate(-50%, -50%)',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  zIndex: 3,
  pointerEvents: 'none'
});
function HelloThemePreview({
  isInstalled = false
}) {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useTheme)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    alignItems: "center",
    width: "100%",
    "data-testid": "hello-theme-preview"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PreviewRoot, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ElementorPlaceholderCard, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_icons__WEBPACK_IMPORTED_MODULE_3__.ElementorLogoIcon, {
    sx: {
      fontSize: theme.spacing(5.5)
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(HelloCard, null, isInstalled ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styled_components__WEBPACK_IMPORTED_MODULE_5__.InstalledChip, {
    label: (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)('common.installed'),
    size: "small",
    color: "success",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CheckedCircleIcon, null),
    sx: {
      position: 'absolute',
      insetBlockStart: theme.spacing(1),
      insetInlineStart: theme.spacing(1),
      zIndex: 2
    }
  }) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(HelloBadge, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    color: "inherit",
    sx: {
      fontWeight: 400,
      lineHeight: 1.5
    }
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)('steps.theme_selection.theme_hello_label'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_icons__WEBPACK_IMPORTED_MODULE_3__.HelloLayoutIcon, {
    sx: {
      fontSize: theme.spacing(6),
      color: 'text.primary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "caption",
    color: "text.secondary"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)('steps.theme_selection.by_elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PlusOverlay, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_icons__WEBPACK_IMPORTED_MODULE_3__.PlusIcon, {
    sx: {
      width: PLUS_SIZE,
      height: PLUS_SIZE,
      color: 'text.primary'
    },
    strokeColor: theme.palette.mode === 'dark' ? theme.palette.common.black : theme.palette.common.white
  }))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/theme-selection/index.ts":
/*!**************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/theme-selection/index.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HELLO_THEME: function() { return /* reexport safe */ _constants__WEBPACK_IMPORTED_MODULE_0__.HELLO_THEME; },
/* harmony export */   HelloThemePreview: function() { return /* reexport safe */ _hello_theme_preview__WEBPACK_IMPORTED_MODULE_1__.HelloThemePreview; },
/* harmony export */   getGreetingText: function() { return /* reexport safe */ _constants__WEBPACK_IMPORTED_MODULE_0__.getGreetingText; }
/* harmony export */ });
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./constants */ "./packages/apps/onboarding/src/components/theme-selection/constants.ts");
/* harmony import */ var _hello_theme_preview__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hello-theme-preview */ "./packages/apps/onboarding/src/components/theme-selection/hello-theme-preview.tsx");



/***/ }),

/***/ "./packages/apps/onboarding/src/components/theme-selection/styled-components.ts":
/*!**************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/theme-selection/styled-components.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   InstalledChip: function() { return /* binding */ InstalledChip; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const InstalledChip = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.Chip)(({
  theme
}) => ({
  position: 'absolute',
  insetBlockStart: theme.spacing(1),
  insetInlineStart: theme.spacing(1),
  zIndex: 1,
  backgroundColor: theme.palette.success.main,
  color: theme.palette.success.contrastText,
  '& .MuiChip-icon': {
    color: 'inherit'
  }
}));

/***/ }),

/***/ "./packages/apps/onboarding/src/components/toast/toast-context.tsx":
/*!*************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/toast/toast-context.tsx ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ToastProvider: function() { return /* binding */ ToastProvider; },
/* harmony export */   useToast: function() { return /* binding */ useToast; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);




const AUTO_HIDE_DURATION = 8000;
const ToastContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
function ToastProvider({
  children
}) {
  const [toasts, setToasts] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const idCounter = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(0);
  const showToast = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(message => {
    const id = String(++idCounter.current);
    setToasts(prev => [...prev, {
      id,
      message
    }]);
  }, []);
  const dismissToast = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(id => {
    setToasts(prev => prev.filter(t => t.id !== id));
  }, []);
  const value = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => ({
    showToast,
    dismissToast
  }), [showToast, dismissToast]);
  const currentToast = toasts[0] ?? null;
  const handleClose = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((_event, reason) => {
    if (reason === 'clickaway') {
      return;
    }
    if (currentToast) {
      dismissToast(currentToast.id);
    }
  }, [currentToast, dismissToast]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ToastContext.Provider, {
    value: value
  }, children, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Snackbar, {
    open: !!currentToast,
    autoHideDuration: AUTO_HIDE_DURATION,
    onClose: handleClose,
    anchorOrigin: {
      vertical: 'top',
      horizontal: 'center'
    },
    ContentProps: {
      elevation: 6,
      sx: {
        borderRadius: '4px',
        '& .MuiSnackbarContent-action': {
          gap: '4px',
          marginInlineStart: '4px'
        }
      }
    },
    message: currentToast?.message,
    action: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
      size: "small",
      "aria-label": "close",
      color: "inherit",
      onClick: handleClose
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.XIcon, {
      fontSize: "small"
    }))
  }));
}
function useToast() {
  const ctx = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(ToastContext);
  if (!ctx) {
    throw new Error('useToast must be used within a ToastProvider');
  }
  return ctx;
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/base-layout.tsx":
/*!********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/base-layout.tsx ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BaseLayout: function() { return /* binding */ BaseLayout; },
/* harmony export */   FOOTER_HEIGHT: function() { return /* binding */ FOOTER_HEIGHT; },
/* harmony export */   LAYOUT_PADDING: function() { return /* binding */ LAYOUT_PADDING; },
/* harmony export */   TOPBAR_HEIGHT: function() { return /* binding */ TOPBAR_HEIGHT; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const TOPBAR_HEIGHT = 48;
const FOOTER_HEIGHT = 68;
const LayoutRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  position: 'fixed',
  inset: 0,
  display: 'flex',
  flexDirection: 'column',
  background: theme.palette.background.default,
  zIndex: theme.zIndex?.modal || 1300
}));
const ContentArea = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => !['topBarHeight', 'footerHeight'].includes(prop)
})(({
  topBarHeight,
  footerHeight
}) => ({
  flex: 1,
  display: 'flex',
  flexDirection: 'column',
  overflow: 'auto',
  paddingTop: topBarHeight,
  paddingBottom: footerHeight
}));
function BaseLayout({
  children,
  topBar,
  footer,
  testId
}) {
  const topBarHeight = topBar ? TOPBAR_HEIGHT : 0;
  const footerHeight = footer ? FOOTER_HEIGHT : 0;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LayoutRoot, {
    "data-module": "onboarding",
    "data-testid": testId
  }, topBar, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ContentArea, {
    topBarHeight: topBarHeight,
    footerHeight: footerHeight
  }, children), footer);
}
const LAYOUT_PADDING = 4;


/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/completion-screen.tsx":
/*!**************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/completion-screen.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CompletionScreen: function() { return /* binding */ CompletionScreen; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");



const PROGRESS_BAR_WIDTH = 192;
const ProgressTrack = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  width: PROGRESS_BAR_WIDTH,
  height: 4,
  borderRadius: 22,
  backgroundColor: theme.palette.action.hover,
  position: 'relative',
  overflow: 'hidden'
}));
const FAKE_PROGRESS_KEYFRAMES = {
  '0%': {
    width: '0%'
  },
  '30%': {
    width: '35%'
  },
  '60%': {
    width: '55%'
  },
  '80%': {
    width: '68%'
  },
  '100%': {
    width: '75%'
  }
};
const ProgressFill = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  position: 'absolute',
  left: 0,
  top: 0,
  height: '100%',
  borderRadius: 22,
  backgroundColor: theme.palette.text.primary,
  animation: 'onboarding-fake-progress 3s ease-out forwards',
  '@keyframes onboarding-fake-progress': FAKE_PROGRESS_KEYFRAMES
}));
function CompletionScreen() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    sx: {
      width: '100%',
      height: '100vh',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      bgcolor: 'background.paper'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 4,
    alignItems: "center",
    sx: {
      maxWidth: 463,
      width: '100%',
      px: 3
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ProgressTrack, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ProgressFill, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 1,
    textAlign: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "h5",
    fontWeight: 500,
    color: "text.primary"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_2__.t)('completion.title')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body1",
    color: "text.secondary"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_2__.t)('completion.subtitle')))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/elementor-icon.tsx":
/*!***********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/elementor-icon.tsx ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementorIcon: function() { return /* binding */ ElementorIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



/**
 * Elementor "E" logo icon.
 * Uses currentColor by default, so it inherits color from parent or theme.
 * @param props
 */
function ElementorIcon(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M15.93 0C7.13 0 0 7.16 0 16s7.13 16 15.93 16c8.8 0 15.93-7.16 15.93-16S24.73 0 15.93 0zM11.15 24H7.97V8h3.18v16zm12.74 0h-9.56v-3.2h9.56V24zm0-6.4h-9.56v-3.2h9.56v3.2zm0-6.4h-9.56V8h9.56v3.2z",
    fill: "currentColor"
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/elementor-logo.tsx":
/*!***********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/elementor-logo.tsx ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementorLogo: function() { return /* binding */ ElementorLogo; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./elementor-icon */ "./packages/apps/onboarding/src/components/ui/elementor-icon.tsx");
/* harmony import */ var _elementor_wordmark__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./elementor-wordmark */ "./packages/apps/onboarding/src/components/ui/elementor-wordmark.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




function ElementorLogo({
  height = 20,
  sx,
  ...props
}) {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)();

  // Icon is square
  const iconSize = height;

  // Wordmark viewBox is 90x15, aspect ratio 6
  const wordmarkHeight = height * 0.8;
  const wordmarkWidth = wordmarkHeight * 6;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, _extends({
    direction: "row",
    alignItems: "center",
    spacing: 0.5,
    sx: {
      color: theme.palette.text.primary,
      ...sx
    }
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icon__WEBPACK_IMPORTED_MODULE_2__.ElementorIcon, {
    sx: {
      width: iconSize,
      height: iconSize
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_wordmark__WEBPACK_IMPORTED_MODULE_3__.ElementorWordmark, {
    sx: {
      width: wordmarkWidth,
      height: wordmarkHeight
    }
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/elementor-wordmark.tsx":
/*!***************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/elementor-wordmark.tsx ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementorWordmark: function() { return /* binding */ ElementorWordmark; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



/**
 * Elementor wordmark (text "elementor").
 * Uses currentColor by default, so it inherits color from parent or theme.
 * @param props
 */
function ElementorWordmark(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 90 15"
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M2.96457 11.7721C3.50632 12.322 4.19354 12.5969 5.01619 12.5969C5.70843 12.5969 6.27526 12.4519 6.71668 12.162C7.16313 11.8721 7.44905 11.4622 7.58449 10.9323H9.69631C9.51071 11.977 8.99404 12.8119 8.14129 13.4417C7.29355 14.0716 6.25018 14.3815 5.01619 14.3815C4.02299 14.3815 3.15017 14.1666 2.39774 13.7367C1.65033 13.3068 1.06343 12.7269 0.64709 12.002C0.230745 11.2772 0.0150486 10.4874 0 9.64254H2.05162C2.11683 10.5174 2.42282 11.2272 2.96959 11.7771L2.96457 11.7721ZM2.01149 8.98768C2.01149 8.76273 2.03657 8.46779 2.09175 8.09287H7.48416C7.42898 7.37802 7.16814 6.80314 6.69662 6.36823C6.2251 5.93333 5.59807 5.71337 4.81555 5.71337C4.08318 5.71337 3.48625 5.92333 3.01473 6.34824C2.54321 6.77315 2.23722 7.35303 2.08674 8.09287H0.0351132C0.155502 7.33803 0.42136 6.65317 0.832688 6.0383C1.24402 5.42343 1.79078 4.93854 2.47298 4.58861C3.15519 4.23868 3.93771 4.06372 4.81053 4.06372C5.83383 4.06372 6.70665 4.28367 7.42898 4.72858C8.15131 5.17349 8.69808 5.75336 9.05925 6.47321C9.42543 7.19306 9.60601 7.98289 9.60601 8.84271C9.60601 9.08266 9.59096 9.3476 9.56588 9.63754H2.04159C2.01651 9.32261 2.00146 9.10266 2.00146 8.98268L2.01149 8.98768Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M11.0857 1.82461H9.49561V0H13.2176V14.222H11.0857V1.82461Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M17.3762 11.7721C17.9179 12.322 18.6052 12.5969 19.4278 12.5969C20.12 12.5969 20.6869 12.4519 21.1283 12.162C21.5747 11.8721 21.8607 11.4622 21.9961 10.9323H24.1079C23.9223 11.977 23.4057 12.8119 22.5529 13.4417C21.7052 14.0716 20.6618 14.3815 19.4278 14.3815C18.4346 14.3815 17.5618 14.1666 16.8094 13.7367C16.0619 13.3068 15.4751 12.7269 15.0587 12.002C14.6424 11.2772 14.4267 10.4874 14.4116 9.64254H16.4632C16.5285 10.5174 16.8344 11.2272 17.3812 11.7771L17.3762 11.7721ZM16.4231 8.98768C16.4231 8.76273 16.4482 8.46779 16.5034 8.09287H21.8958C21.8406 7.37802 21.5798 6.80314 21.1082 6.36823C20.6367 5.93333 20.0097 5.71337 19.2272 5.71337C18.4948 5.71337 17.8979 5.92333 17.4264 6.34824C16.9548 6.77315 16.6488 7.35303 16.4984 8.09287H14.4467C14.5671 7.33803 14.833 6.65317 15.2443 6.0383C15.6556 5.42343 16.2024 4.93854 16.8846 4.58861C17.5668 4.23868 18.3493 4.06372 19.2221 4.06372C20.2455 4.06372 21.1183 4.28367 21.8406 4.72858C22.5629 5.17349 23.1097 5.75336 23.4709 6.47321C23.837 7.19306 24.0176 7.98289 24.0176 8.84271C24.0176 9.08266 24.0026 9.3476 23.9775 9.63754H16.4532C16.4281 9.32261 16.4131 9.10266 16.4131 8.98268L16.4231 8.98768Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M26.1243 6.03319H24.5342V4.22857H28.2361V5.91321C28.4769 5.34333 28.8531 4.89343 29.3698 4.5635C29.8865 4.23357 30.5034 4.0686 31.2208 4.0686C31.9782 4.0686 32.6454 4.24857 33.2222 4.60349C33.7991 4.95842 34.1853 5.44831 34.386 6.07318C34.6117 5.4933 35.0331 5.0134 35.6501 4.63348C36.2671 4.25856 36.9392 4.0686 37.6716 4.0686C38.7601 4.0686 39.6229 4.41353 40.2599 5.09839C40.897 5.78824 41.213 6.69805 41.213 7.83781V14.2265H39.1012V8.43268C39.1012 7.66784 38.9156 7.05297 38.5444 6.58807C38.1732 6.12317 37.6766 5.89322 37.0496 5.89322C36.3473 5.89322 35.7805 6.17316 35.3591 6.72804C34.9327 7.28292 34.7221 7.95778 34.7221 8.75261V14.2265H32.5902V8.43268C32.5902 7.66784 32.4096 7.05297 32.0434 6.58807C31.6772 6.12317 31.1857 5.89322 30.5586 5.89322C29.8664 5.89322 29.3096 6.17316 28.8782 6.73804C28.4468 7.29792 28.2311 7.97278 28.2311 8.75261V14.2265H26.1193V6.03319H26.1243Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M45.3518 11.7721C45.8935 12.322 46.5807 12.5969 47.4034 12.5969C48.0956 12.5969 48.6625 12.4519 49.1039 12.162C49.5503 11.8721 49.8363 11.4622 49.9717 10.9323H52.0835C51.8979 11.977 51.3812 12.8119 50.5285 13.4417C49.6808 14.0716 48.6374 14.3815 47.4034 14.3815C46.4102 14.3815 45.5374 14.1666 44.7849 13.7367C44.0375 13.3068 43.4506 12.7269 43.0343 12.002C42.6179 11.2772 42.4023 10.4874 42.3872 9.64254H44.4388C44.504 10.5174 44.81 11.2272 45.3568 11.7771L45.3518 11.7721ZM44.3987 8.98768C44.3987 8.76273 44.4238 8.46779 44.479 8.09287H49.8714C49.8162 7.37802 49.5554 6.80314 49.0838 6.36823C48.6123 5.93333 47.9853 5.71337 47.2028 5.71337C46.4704 5.71337 45.8735 5.92333 45.4019 6.34824C44.9304 6.77315 44.6244 7.35303 44.4739 8.09287H42.4223C42.5427 7.33803 42.8086 6.65317 43.2199 6.0383C43.6312 5.42343 44.178 4.93854 44.8602 4.58861C45.5424 4.23868 46.3249 4.06372 47.1977 4.06372C48.221 4.06372 49.0939 4.28367 49.8162 4.72858C50.5385 5.17349 51.0853 5.75336 51.4465 6.47321C51.8126 7.19306 51.9932 7.98289 51.9932 8.84271C51.9932 9.08266 51.9782 9.3476 51.9531 9.63754H44.4288C44.4037 9.32261 44.3887 9.10266 44.3887 8.98268L44.3987 8.98768Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M54.0144 6.03319H52.4243V4.22857H56.1263V5.91321C56.3921 5.31834 56.8085 4.86344 57.3703 4.5435C57.9321 4.22857 58.5892 4.0686 59.3316 4.0686C60.4603 4.0686 61.3632 4.43353 62.0504 5.16837C62.7326 5.90322 63.0737 6.86301 63.0737 8.05276V14.2215H60.9418V8.48767C60.9418 7.73283 60.7362 7.12796 60.3248 6.66305C59.9135 6.19815 59.3717 5.9682 58.6946 5.9682C57.9522 5.9682 57.3352 6.23315 56.8536 6.76303C56.367 7.29292 56.1263 7.96778 56.1263 8.78761V14.2215H54.0144V6.03319Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M66.8009 13.3318C66.1839 12.7369 65.8729 11.9171 65.8729 10.8723V6.03336H63.8413V4.22875H65.8729V1.1344H67.9847V4.22875H70.8891V6.03336H67.9847V10.7924C67.9847 11.3072 68.1201 11.7072 68.391 11.9921C68.6619 12.277 69.0381 12.417 69.5146 12.417H70.8891V14.2216H69.2989C68.2505 14.2216 67.4179 13.9217 66.8009 13.3268V13.3318Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M74.024 13.7167C73.2364 13.2718 72.6195 12.6619 72.173 11.8821C71.7266 11.1022 71.5059 10.2174 71.5059 9.22263C71.5059 8.22784 71.7266 7.34303 72.173 6.56319C72.6195 5.78336 73.2364 5.17349 74.024 4.72858C74.8115 4.28367 75.7044 4.06372 76.7026 4.06372C77.7009 4.06372 78.5887 4.28367 79.3813 4.72858C80.1688 5.17349 80.7858 5.78336 81.2323 6.56319C81.6787 7.34303 81.8994 8.22784 81.8994 9.22263C81.8994 10.2174 81.6787 11.1022 81.2323 11.8821C80.7858 12.6619 80.1688 13.2718 79.3813 13.7167C78.5937 14.1616 77.7009 14.3815 76.7026 14.3815C75.7044 14.3815 74.8165 14.1616 74.024 13.7167ZM78.8997 11.6171C79.4615 11.0023 79.7475 10.2074 79.7475 9.22763C79.7475 8.24784 79.4666 7.453 78.8997 6.83813C78.3379 6.22326 77.6005 5.91333 76.7026 5.91333C75.8047 5.91333 75.0724 6.21827 74.5106 6.83813C73.9538 7.453 73.6729 8.24784 73.6729 9.22763C73.6729 10.2074 73.9538 11.0023 74.5106 11.6171C75.0674 12.232 75.7997 12.5419 76.7026 12.5419C77.6056 12.5419 78.3379 12.237 78.8997 11.6171Z",
    fill: "currentColor"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M83.8411 6.03362H82.251V4.229H85.9529V6.09361C86.1937 5.44375 86.5198 4.97385 86.9361 4.67391C87.3524 4.37397 87.9092 4.229 88.5965 4.229H89.7903V6.03362H88.2955C87.4728 6.03362 86.8759 6.35355 86.5047 6.99342C86.1335 7.63328 85.9479 8.5131 85.9479 9.62286V14.2269H83.8361V6.03362H83.8411Z",
    fill: "currentColor"
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/footer-actions.tsx":
/*!***********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/footer-actions.tsx ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FooterActions: function() { return /* binding */ FooterActions; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");




const LeftActions = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)({
  display: 'flex',
  alignItems: 'center',
  gap: 8
});
const RightActions = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)({
  display: 'flex',
  alignItems: 'center',
  gap: 8
});
const DirectionalArrowLeftIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.withDirection)(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ArrowLeftIcon);
const BackButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button)(({
  theme
}) => ({
  color: theme.palette.text.primary,
  padding: theme.spacing(0.75, 1),
  minHeight: 0,
  borderRadius: theme.shape.borderRadius,
  textTransform: 'none',
  fontSize: theme.typography.pxToRem(14),
  fontWeight: 500,
  lineHeight: theme.typography.pxToRem(24),
  letterSpacing: '0.4px'
}));
const SkipButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button)(({
  theme
}) => {
  const outlinedBorderColor = theme.palette.primary?.states?.outlinedBorder ?? theme.palette.divider;
  return {
    color: theme.palette.text.primary,
    borderColor: outlinedBorderColor,
    padding: theme.spacing(0.75, 2),
    minHeight: 0,
    borderRadius: theme.shape.borderRadius,
    textTransform: 'none',
    fontSize: theme.typography.pxToRem(14),
    fontWeight: 500,
    lineHeight: theme.typography.pxToRem(24),
    letterSpacing: '0.4px',
    '&:hover': {
      borderColor: outlinedBorderColor
    }
  };
});
const ContinueButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button)(({
  theme
}) => ({
  padding: theme.spacing(0.75, 2),
  minHeight: 0,
  borderRadius: theme.shape.borderRadius,
  textTransform: 'none',
  fontSize: theme.typography.pxToRem(14),
  fontWeight: 500,
  lineHeight: theme.typography.pxToRem(24),
  letterSpacing: '0.4px',
  '&:focus-visible': {
    backgroundColor: theme.palette.promotion.main
  }
}));
function FooterActions({
  showBack = true,
  showSkip = true,
  showContinue = true,
  backLabel = (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)('common.back'),
  skipLabel = (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)('common.skip'),
  continueLabel = (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)('common.continue'),
  isBackDisabled = false,
  continueDisabled = false,
  continueLoading = false,
  onBack,
  onSkip,
  onContinue
}) {
  const {
    direction
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useTheme)();
  const isRtl = direction === 'rtl';
  const backIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DirectionalArrowLeftIcon, {
    fontSize: "tiny"
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LeftActions, null, showBack && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(BackButton, {
    variant: "text",
    onClick: onBack,
    disabled: isBackDisabled,
    startIcon: isRtl ? undefined : backIcon,
    endIcon: isRtl ? backIcon : undefined
  }, backLabel)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RightActions, null, showSkip && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SkipButton, {
    variant: "outlined",
    onClick: onSkip
  }, skipLabel), showContinue && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ContinueButton, {
    color: "primary",
    variant: "contained",
    onClick: onContinue,
    disabled: continueDisabled || continueLoading
  }, continueLoading ? (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)('common.loading') : continueLabel)));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/footer-highlights.tsx":
/*!**************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/footer-highlights.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FooterHighlights: function() { return /* binding */ FooterHighlights; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");




function FooterHighlights({
  items,
  testId = 'footer-highlights',
  sx
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 3,
    "data-testid": testId,
    sx: sx
  }, items.map(item => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    key: item.labelKey,
    direction: "row",
    alignItems: "center",
    gap: 0.75
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CircleCheckFilledIcon, {
    fontSize: "tiny",
    sx: {
      color: 'text.secondary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    sx: {
      color: 'text.secondary'
    }
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)(item.labelKey)))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/footer.tsx":
/*!***************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/footer.tsx ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Footer: function() { return /* binding */ Footer; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _base_layout__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./base-layout */ "./packages/apps/onboarding/src/components/ui/base-layout.tsx");



const FooterRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  position: 'fixed',
  bottom: 0,
  left: 0,
  right: 0,
  height: _base_layout__WEBPACK_IMPORTED_MODULE_2__.FOOTER_HEIGHT,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'space-between',
  padding: theme.spacing(2, 3),
  background: theme.palette.background.paper,
  boxShadow: theme.shadows[4],
  zIndex: theme.zIndex?.appBar || 1100
}));
function Footer({
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FooterRoot, {
    component: "footer"
  }, children);
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/greeting-banner.tsx":
/*!************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/greeting-banner.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GreetingBanner: function() { return /* binding */ GreetingBanner; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _styled_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./styled-components */ "./packages/apps/onboarding/src/components/ui/styled-components.ts");



function GreetingBanner({
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_styled_components__WEBPACK_IMPORTED_MODULE_2__.GreetingBannerRoot, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body1",
    color: "text.primary",
    align: "center"
  }, children));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/right-panel.tsx":
/*!********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/right-panel.tsx ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RightPanel: function() { return /* binding */ RightPanel; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _steps_step_visuals__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../steps/step-visuals */ "./packages/apps/onboarding/src/steps/step-visuals.ts");
/* harmony import */ var _base_layout__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./base-layout */ "./packages/apps/onboarding/src/components/ui/base-layout.tsx");





const PANEL_RADIUS_MULTIPLIER = 2;
const PANEL_MIN_HEIGHT = 36;
const VIDEO_TRANSITION_MS = 400;
const ALL_VIDEO_URLS = (0,_steps_step_visuals__WEBPACK_IMPORTED_MODULE_2__.getVideoUrls)();
const RightPanelRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => prop !== 'background'
})(({
  theme,
  background
}) => {
  const height = `calc(100vh - ${_base_layout__WEBPACK_IMPORTED_MODULE_3__.TOPBAR_HEIGHT}px - ${_base_layout__WEBPACK_IMPORTED_MODULE_3__.FOOTER_HEIGHT}px - ${theme.spacing(_base_layout__WEBPACK_IMPORTED_MODULE_3__.LAYOUT_PADDING * 2)})`;
  return {
    position: 'relative',
    width: '100%',
    height,
    minHeight: theme.spacing(PANEL_MIN_HEIGHT),
    borderRadius: theme.shape.borderRadius * PANEL_RADIUS_MULTIPLIER,
    overflow: 'hidden',
    background
  };
});
const VideoStack = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.memo(function VideoStack({
  activeUrl
}) {
  const videoRefs = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(new Map());
  const [readyUrls, setReadyUrls] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => new Set());

  // Reset the incoming video to its first frame *before* the browser paints it,
  // otherwise the element is revealed still showing the last frame from its
  // previous play and then visibly jumps back to the start. useLayoutEffect
  // runs after the DOM mutation but before paint, so the seek is never seen.
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect)(() => {
    videoRefs.current.forEach((element, videoUrl) => {
      if (videoUrl === activeUrl) {
        element.currentTime = 0;
        element.play()?.catch(() => {});
      } else {
        element.pause();
      }
    });
  }, [activeUrl]);
  const markReady = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(videoUrl => {
    setReadyUrls(prev => {
      if (prev.has(videoUrl)) {
        return prev;
      }
      const next = new Set(prev);
      next.add(videoUrl);
      return next;
    });
  }, []);

  // Only reveal the active video once its own element has a decoded frame.
  const visibleUrl = activeUrl && readyUrls.has(activeUrl) ? activeUrl : undefined;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, ALL_VIDEO_URLS.map(videoUrl => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    key: videoUrl,
    component: "video",
    src: videoUrl,
    preload: "auto",
    muted: true,
    playsInline: true,
    onLoadedData: () => markReady(videoUrl),
    ref: element => {
      if (element) {
        videoRefs.current.set(videoUrl, element);
      }
    },
    sx: {
      position: 'absolute',
      inset: 0,
      width: '100%',
      height: '100%',
      objectFit: 'cover',
      opacity: videoUrl === visibleUrl ? 1 : 0,
      transition: `opacity ${VIDEO_TRANSITION_MS}ms ease`
    }
  })));
});
const RightPanel = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.memo(function RightPanel({
  config
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RightPanelRoot, {
    background: config.background
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(VideoStack, {
    activeUrl: config.video
  }));
});

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/selection-badge.tsx":
/*!************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/selection-badge.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SelectionBadge: function() { return /* binding */ SelectionBadge; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const SelectionBadgeRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => 'variant' !== prop
})(({
  theme,
  variant
}) => ({
  position: 'absolute',
  top: theme.spacing(-1),
  insetInlineEnd: theme.spacing(-1),
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  width: theme.spacing(2.25),
  height: theme.spacing(2.25),
  borderRadius: '50%',
  backgroundColor: variant === 'paid' ? theme.palette.promotion.main : theme.palette.text.primary,
  color: theme.palette.common.white,
  '& .MuiSvgIcon-root': {
    fontSize: theme.typography.pxToRem(14)
  }
}));
function SelectionBadge({
  icon: Icon,
  variant = 'free'
}) {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SelectionBadgeRoot, {
    variant: variant
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    sx: {
      fill: variant === 'paid' ? theme.palette.common.white : theme.palette.secondary.contrastText
    }
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/split-layout.tsx":
/*!*********************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/split-layout.tsx ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LAYOUT_PADDING: function() { return /* reexport safe */ _base_layout__WEBPACK_IMPORTED_MODULE_2__.LAYOUT_PADDING; },
/* harmony export */   LAYOUT_TRANSITION_MS: function() { return /* binding */ LAYOUT_TRANSITION_MS; },
/* harmony export */   SplitLayout: function() { return /* binding */ SplitLayout; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _base_layout__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./base-layout */ "./packages/apps/onboarding/src/components/ui/base-layout.tsx");
/* harmony import */ var _right_panel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./right-panel */ "./packages/apps/onboarding/src/components/ui/right-panel.tsx");




const LAYOUT_GAP = 4;

const LAYOUT_TRANSITION_MS = 300;
const LEFT_PANEL_CONTENT_WIDTH = 386;
const LEFT_PANEL_PADDING_X = 80;
const LEFT_PANEL_PADDING_TOP = 40;
const LEFT_PANEL_GAP = 32;
const IMAGE_MIN_WIDTH = 464;
const CONTENT_IMAGE_MIN_GAP = 80;
const SplitLayoutRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => {
  const hideImageBreakpoint = LEFT_PANEL_CONTENT_WIDTH + LEFT_PANEL_PADDING_X * 2 + CONTENT_IMAGE_MIN_GAP + IMAGE_MIN_WIDTH + LAYOUT_GAP * 8;
  return {
    flex: 1,
    display: 'grid',
    gridTemplateColumns: `2fr 1fr`,
    gap: theme.spacing(LAYOUT_GAP),
    padding: theme.spacing(_base_layout__WEBPACK_IMPORTED_MODULE_2__.LAYOUT_PADDING),
    transition: `grid-template-columns ${LAYOUT_TRANSITION_MS}ms ease`,
    [theme.breakpoints.up('md')]: {
      minHeight: 0
    },
    [`@media (max-width: ${hideImageBreakpoint}px)`]: {
      gridTemplateColumns: '1fr',
      '& > *:last-child': {
        display: 'none'
      }
    }
  };
});
const LeftPanel = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
  shouldForwardProp: prop => 'contentMaxWidth' !== prop
})(({
  theme,
  contentMaxWidth
}) => ({
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  gap: LEFT_PANEL_GAP,
  minHeight: 0,
  paddingBlockStart: LEFT_PANEL_PADDING_TOP,
  paddingInline: LEFT_PANEL_PADDING_X,
  '& > *': {
    width: '100%'
  },
  '& > *:last-of-type': {
    maxWidth: contentMaxWidth,
    flex: 1,
    display: 'flex',
    flexDirection: 'column',
    minHeight: 0
  },
  [theme.breakpoints.down('sm')]: {
    padding: 0,
    gap: LEFT_PANEL_GAP / 2,
    '& > *': {
      maxWidth: 'none'
    }
  }
}));
function SplitLayout({
  left,
  rightConfig
}) {
  const contentMaxWidth = rightConfig.contentMaxWidth ?? LEFT_PANEL_CONTENT_WIDTH;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(SplitLayoutRoot, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(LeftPanel, {
    contentMaxWidth: contentMaxWidth
  }, left), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_right_panel__WEBPACK_IMPORTED_MODULE_3__.RightPanel, {
    config: rightConfig
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/styled-components.ts":
/*!*************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/styled-components.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GreetingBannerRoot: function() { return /* binding */ GreetingBannerRoot; },
/* harmony export */   StepTitle: function() { return /* binding */ StepTitle; }
/* harmony export */ });
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__);

const GREETING_BANNER_BG_COLOR_LIGHT = '#fae4fa';
const GREETING_BANNER_BG_COLOR_DARK = '#491146';
const StepTitle = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.Typography)({
  fontWeight: 500,
  fontFamily: 'Poppins'
});
const GreetingBannerRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_0__.Box)(({
  theme
}) => ({
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  paddingInline: theme.spacing(3),
  paddingBlock: theme.spacing(1.5),
  borderRadius: 16,
  backgroundColor: theme.palette.mode === 'dark' ? GREETING_BANNER_BG_COLOR_DARK : GREETING_BANNER_BG_COLOR_LIGHT,
  alignSelf: 'flex-start'
}));

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/top-bar-content.tsx":
/*!************************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/top-bar-content.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TopBarContent: function() { return /* binding */ TopBarContent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _elementor_logo__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./elementor-logo */ "./packages/apps/onboarding/src/components/ui/elementor-logo.tsx");





const UpgradeButton = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button)(({
  theme
}) => ({
  backgroundColor: theme.palette.promotion.main,
  color: theme.palette.promotion.contrastText,
  padding: theme.spacing(0.5, 1.25),
  minHeight: 0,
  borderRadius: 8,
  textTransform: 'none',
  fontSize: theme.typography.pxToRem(13),
  fontWeight: 500,
  lineHeight: theme.typography.pxToRem(22),
  letterSpacing: '0.46px'
}));
const Divider = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)('div')(({
  theme
}) => ({
  width: 2,
  height: 20,
  backgroundColor: theme.palette.divider
}));
function TopBarContent({
  showUpgrade = true,
  showClose = true,
  onUpgrade,
  onClose
}) {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useTheme)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_logo__WEBPACK_IMPORTED_MODULE_4__.ElementorLogo, {
    height: 20
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    alignItems: "center",
    spacing: 2
  }, showUpgrade && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(UpgradeButton, {
    color: "promotion",
    variant: "contained",
    onClick: onUpgrade
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)('common.upgrade')), showClose && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    alignItems: "center",
    spacing: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    "aria-label": (0,_utils_translations__WEBPACK_IMPORTED_MODULE_3__.t)('common.close_onboarding'),
    onClick: onClose,
    size: "small",
    sx: {
      color: theme.palette.text.secondary,
      padding: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.XIcon, {
    fontSize: "tiny"
  })))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/components/ui/top-bar.tsx":
/*!****************************************************************!*\
  !*** ./packages/apps/onboarding/src/components/ui/top-bar.tsx ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TopBar: function() { return /* binding */ TopBar; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _base_layout__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./base-layout */ "./packages/apps/onboarding/src/components/ui/base-layout.tsx");



const TopBarRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box)(({
  theme
}) => ({
  position: 'fixed',
  top: 0,
  left: 0,
  right: 0,
  height: _base_layout__WEBPACK_IMPORTED_MODULE_2__.TOPBAR_HEIGHT,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'space-between',
  paddingInlineStart: 41,
  paddingInlineEnd: 16,
  background: theme.palette.background.paper,
  zIndex: theme.zIndex?.appBar || 1100
}));
function TopBar({
  children
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(TopBarRoot, {
    component: "header"
  }, children, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, {
    absolute: true
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-check-pro-install-screen.ts":
/*!****************************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-check-pro-install-screen.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useCheckProInstallScreen: function() { return /* binding */ useCheckProInstallScreen; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_get_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/get-config */ "./packages/apps/onboarding/src/utils/get-config.ts");


function useCheckProInstallScreen() {
  const checkProInstallScreen = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    const config = (0,_utils_get_config__WEBPACK_IMPORTED_MODULE_1__.getConfig)();
    if (!config) {
      return {
        shouldShowProInstallScreen: false
      };
    }
    const response = await fetch(`${config.restUrl}pro-install-screen`, {
      method: 'GET',
      headers: {
        'X-WP-Nonce': config.nonce
      }
    });
    if (!response.ok) {
      return {
        shouldShowProInstallScreen: false
      };
    }
    const json = await response.json();
    return {
      shouldShowProInstallScreen: json.data?.shouldShowProInstallScreen ?? false
    };
  }, []);
  return checkProInstallScreen;
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-elementor-connect.ts":
/*!*********************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-elementor-connect.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useElementorConnect: function() { return /* binding */ useElementorConnect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const POPUP_WIDTH = 600;
const POPUP_HEIGHT = 700;
const POPUP_TOP = 200;
const POPUP_LEFT = 0;
const CONNECT_SUCCESS_EVENT = 'elementor/connect/success';
function useElementorConnect({
  connectUrl,
  onSuccess
}) {
  const onSuccessRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(onSuccess);
  onSuccessRef.current = onSuccess;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handleNativeSuccess = event => {
      const detail = event.detail;
      const data = detail ?? {};
      onSuccessRef.current?.(data);
    };
    window.addEventListener(CONNECT_SUCCESS_EVENT, handleNativeSuccess);
    return () => {
      window.removeEventListener(CONNECT_SUCCESS_EVENT, handleNativeSuccess);
    };
  }, []);
  const triggerConnect = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (!connectUrl) {
      return;
    }
    const separator = connectUrl.includes('?') ? '&' : '?';
    const popupUrl = `${connectUrl}${separator}mode=popup`;
    const features = `toolbar=no,menubar=no,width=${POPUP_WIDTH},height=${POPUP_HEIGHT},top=${POPUP_TOP},left=${POPUP_LEFT}`;
    const popup = window.open(popupUrl, 'elementorConnect', features);
    if (!popup) {
      window.location.href = connectUrl;
    }
  }, [connectUrl]);
  return triggerConnect;
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-install-pro.ts":
/*!***************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-install-pro.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useInstallPro: function() { return /* binding */ useInstallPro; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_get_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/get-config */ "./packages/apps/onboarding/src/utils/get-config.ts");
/* harmony import */ var _utils_retry__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/retry */ "./packages/apps/onboarding/src/utils/retry.ts");



async function installProRequest() {
  const config = (0,_utils_get_config__WEBPACK_IMPORTED_MODULE_1__.getConfig)();
  if (!config) {
    throw new Error('Onboarding config not found');
  }
  const response = await fetch(`${config.restUrl}install-pro`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': config.nonce
    }
  });
  if (!response.ok) {
    const error = await response.json().catch(() => null);
    throw new Error(error?.message || 'Failed to install Elementor Pro');
  }
  const json = await response.json();
  return json.data;
}
function useInstallPro() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: () => (0,_utils_retry__WEBPACK_IMPORTED_MODULE_2__.withRetry)(installProRequest)
  });
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-install-theme.ts":
/*!*****************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-install-theme.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useInstallTheme: function() { return /* binding */ useInstallTheme; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_get_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/get-config */ "./packages/apps/onboarding/src/utils/get-config.ts");
/* harmony import */ var _utils_retry__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/retry */ "./packages/apps/onboarding/src/utils/retry.ts");



async function installThemeRequest(themeSlug) {
  const config = (0,_utils_get_config__WEBPACK_IMPORTED_MODULE_1__.getConfig)();
  if (!config) {
    throw new Error('Onboarding config not found');
  }
  const response = await fetch(`${config.restUrl}install-theme`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': config.nonce
    },
    body: JSON.stringify({
      theme_slug: themeSlug
    })
  });
  if (!response.ok) {
    const error = await response.json().catch(() => null);
    throw new Error(error?.message || 'Failed to install theme');
  }
  const json = await response.json();
  return json.data;
}
function useInstallTheme() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: themeSlug => (0,_utils_retry__WEBPACK_IMPORTED_MODULE_2__.withRetry)(() => installThemeRequest(themeSlug))
  });
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-onboarding-event.ts":
/*!********************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-onboarding-event.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOnboardingEvent: function() { return /* binding */ useOnboardingEvent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../analytics/onboarding-tracking */ "./packages/apps/onboarding/src/analytics/onboarding-tracking.ts");
/* harmony import */ var _analytics_tracking_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../analytics/tracking-context */ "./packages/apps/onboarding/src/analytics/tracking-context.tsx");



function useOnboardingEvent() {
  const {
    isActive,
    activate
  } = (0,_analytics_tracking_context__WEBPACK_IMPORTED_MODULE_2__.useTrackingState)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => ({
    trackOnboardingInitialized: () => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackOnboardingInitialized)(isActive),
    trackLoginType: loginType => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackLoginType)(isActive, loginType),
    trackConnect: (success, error) => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackConnect)(isActive, success, error),
    trackProInstall: action => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackProInstall)(isActive, action),
    trackStepViewed: viewedStepId => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackStepViewed)(isActive, viewedStepId),
    trackThemeSelected: (theme, source) => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackThemeSelected)(isActive, theme, source),
    trackThemeUnselected: () => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackThemeUnselected)(isActive),
    trackProFeaturesSelected: params => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackProFeaturesSelected)(isActive, params),
    trackBackClicked: currentStepId => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackBackClicked)(isActive, currentStepId),
    trackSkipClicked: currentStepId => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackSkipClicked)(isActive, currentStepId),
    trackUpgradeClicked: currentStepId => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackUpgradeClicked)(isActive, currentStepId),
    trackResumeOnboarding: resumeStepId => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackResumeOnboarding)(isActive, resumeStepId),
    trackSiteStarterSelected: params => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackSiteStarterSelected)(isActive, params),
    trackSummary: snapshot => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackSummary)(isActive, snapshot),
    trackErrorReported: params => (0,_analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.trackErrorReported)(isActive, params),
    activateTracking: activate,
    flushQueue: _analytics_onboarding_tracking__WEBPACK_IMPORTED_MODULE_1__.flushQueue
  }), [isActive, activate]);
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-onboarding.ts":
/*!**************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-onboarding.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOnboarding: function() { return /* binding */ useOnboarding; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../store */ "./packages/apps/onboarding/src/store/index.ts");



function useOnboarding() {
  const dispatch = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useDispatch)();
  const stepId = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectCurrentStepId);
  const stepIndex = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectCurrentStepIndex);
  const step = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectCurrentStep);
  const steps = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectSteps);
  const isFirst = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectIsFirstStep);
  const isLast = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectIsLastStep);
  const totalSteps = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectTotalSteps);
  const completedSteps = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectCompletedSteps);
  const choices = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectChoices);
  const isLoading = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectIsLoading);
  const error = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectError);
  const hadUnexpectedExit = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectHadUnexpectedExit);
  const resumeStepIdForTracking = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectResumeStepIdForTracking);
  const isConnected = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectIsConnected);
  const isGuest = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectIsGuest);
  const hasPassedLogin = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectHasPassedLogin);
  const shouldShowProInstall = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectShouldShowProInstall);
  const userName = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectUserName);
  const urls = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__useSelector)(_store__WEBPACK_IMPORTED_MODULE_2__.selectUrls);
  const actions = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => ({
    goToStep: id => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.goToStep)(id)),
    goToStepIndex: index => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.goToStepIndex)(index)),
    nextStep: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.nextStep)()),
    prevStep: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.prevStep)()),
    completeStep: id => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.completeStep)(id)),
    setUserChoice: (key, value) => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setUserChoice)({
      key,
      value
    })),
    setUserChoices: data => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setUserChoices)(data)),
    setExitType: type => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setExitType)(type)),
    startOnboarding: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.startOnboarding)()),
    completeOnboarding: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.completeOnboarding)()),
    setLoading: loading => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setLoading)(loading)),
    setError: err => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setError)(err)),
    clearUnexpectedExit: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.clearUnexpectedExit)()),
    clearResumeStepIdForTracking: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.clearResumeStepIdForTracking)()),
    setConnected: connected => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setConnected)(connected)),
    setGuest: guest => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setGuest)(guest)),
    setShouldShowProInstallScreen: value => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.setShouldShowProInstallScreen)(value)),
    dismissProInstallScreen: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.dismissProInstallScreen)()),
    markProInstalled: () => dispatch((0,_store__WEBPACK_IMPORTED_MODULE_2__.markProInstalled)())
  }), [dispatch]);
  return {
    stepId,
    stepIndex,
    step,
    steps,
    isFirst,
    isLast,
    totalSteps,
    completedSteps,
    choices,
    isLoading,
    error,
    hadUnexpectedExit,
    resumeStepIdForTracking,
    isConnected,
    isGuest,
    hasPassedLogin,
    shouldShowProInstall,
    userName,
    urls,
    actions
  };
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-update-choices.ts":
/*!******************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-update-choices.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useUpdateChoices: function() { return /* binding */ useUpdateChoices; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_get_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/get-config */ "./packages/apps/onboarding/src/utils/get-config.ts");


async function updateChoices(params) {
  const config = (0,_utils_get_config__WEBPACK_IMPORTED_MODULE_1__.getConfig)();
  if (!config) {
    throw new Error('Onboarding config not found');
  }
  const response = await fetch(`${config.restUrl}user-choices`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': config.nonce
    },
    body: JSON.stringify(params)
  });
  if (!response.ok) {
    throw new Error('Failed to update choices');
  }
}
function useUpdateChoices() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: updateChoices
  });
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-update-progress.ts":
/*!*******************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-update-progress.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useUpdateProgress: function() { return /* binding */ useUpdateProgress; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_get_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/get-config */ "./packages/apps/onboarding/src/utils/get-config.ts");


async function updateProgress(params) {
  const config = (0,_utils_get_config__WEBPACK_IMPORTED_MODULE_1__.getConfig)();
  if (!config) {
    throw new Error('Onboarding config not found');
  }
  const response = await fetch(`${config.restUrl}user-progress`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': config.nonce
    },
    body: JSON.stringify(params)
  });
  if (!response.ok) {
    throw new Error('Failed to update progress');
  }
}
function useUpdateProgress() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: updateProgress
  });
}

/***/ }),

/***/ "./packages/apps/onboarding/src/hooks/use-video-preload.ts":
/*!*****************************************************************!*\
  !*** ./packages/apps/onboarding/src/hooks/use-video-preload.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isVideoPreloaded: function() { return /* binding */ isVideoPreloaded; },
/* harmony export */   useVideoPreload: function() { return /* binding */ useVideoPreload; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _steps_step_visuals__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../steps/step-visuals */ "./packages/apps/onboarding/src/steps/step-visuals.ts");


const preloadedUrls = new Set();
function useVideoPreload() {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    let isMounted = true;
    const preloadSequentially = async () => {
      for (const url of (0,_steps_step_visuals__WEBPACK_IMPORTED_MODULE_1__.getVideoUrls)()) {
        if (!isMounted) {
          break;
        }
        if (preloadedUrls.has(url)) {
          continue;
        }
        await waitForVideo(url);
      }
    };
    preloadSequentially();
    return () => {
      isMounted = false;
    };
  }, []);
}
function isVideoPreloaded(url) {
  return preloadedUrls.has(url);
}
const waitForVideo = url => new Promise(resolve => {
  const video = document.createElement('video');
  const cleanup = () => {
    video.removeEventListener('canplaythrough', onCanPlayThrough);
    video.removeEventListener('error', onError);
    video.src = '';
    video.load();
  };
  const onCanPlayThrough = () => {
    preloadedUrls.add(url);
    cleanup();
    resolve();
  };
  const onError = () => {
    cleanup();
    resolve();
  };
  video.addEventListener('canplaythrough', onCanPlayThrough);
  video.addEventListener('error', onError);
  video.preload = 'auto';
  video.src = url;
  video.load();
});

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/index.ts":
/*!*****************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/index.ts ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AIGeneratorIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.AIGeneratorIcon; },
/* harmony export */   AccessibilityToolsIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.AccessibilityToolsIcon; },
/* harmony export */   AppleIcon: function() { return /* reexport safe */ _login_apple_icon__WEBPACK_IMPORTED_MODULE_0__.AppleIcon; },
/* harmony export */   ClassesVariablesIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.ClassesVariablesIcon; },
/* harmony export */   CookieConsentIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.CookieConsentIcon; },
/* harmony export */   CorePlaceholderIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.CorePlaceholderIcon; },
/* harmony export */   CustomCodeIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.CustomCodeIcon; },
/* harmony export */   ElementorLogoIcon: function() { return /* reexport safe */ _theme_selection__WEBPACK_IMPORTED_MODULE_4__.ElementorLogoIcon; },
/* harmony export */   EmailDeliverabilityIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.EmailDeliverabilityIcon; },
/* harmony export */   ExploreMoreIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.ExploreMoreIcon; },
/* harmony export */   FacebookIcon: function() { return /* reexport safe */ _login_facebook_icon__WEBPACK_IMPORTED_MODULE_1__.FacebookIcon; },
/* harmony export */   GoogleIcon: function() { return /* reexport safe */ _login_google_icon__WEBPACK_IMPORTED_MODULE_2__.GoogleIcon; },
/* harmony export */   HelloLayoutIcon: function() { return /* reexport safe */ _theme_selection__WEBPACK_IMPORTED_MODULE_4__.HelloLayoutIcon; },
/* harmony export */   HelloThemeIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.HelloThemeIcon; },
/* harmony export */   ImageOptimizationIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.ImageOptimizationIcon; },
/* harmony export */   InteractionsIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.InteractionsIcon; },
/* harmony export */   PlusIcon: function() { return /* reexport safe */ _theme_selection__WEBPACK_IMPORTED_MODULE_4__.PlusIcon; },
/* harmony export */   ThemeBuilderIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.ThemeBuilderIcon; },
/* harmony export */   WoocommerceIcon: function() { return /* reexport safe */ _site_features__WEBPACK_IMPORTED_MODULE_3__.WoocommerceIcon; }
/* harmony export */ });
/* harmony import */ var _login_apple_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./login/apple-icon */ "./packages/apps/onboarding/src/icons/login/apple-icon.tsx");
/* harmony import */ var _login_facebook_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./login/facebook-icon */ "./packages/apps/onboarding/src/icons/login/facebook-icon.tsx");
/* harmony import */ var _login_google_icon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./login/google-icon */ "./packages/apps/onboarding/src/icons/login/google-icon.tsx");
/* harmony import */ var _site_features__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./site-features */ "./packages/apps/onboarding/src/icons/site-features/index.ts");
/* harmony import */ var _theme_selection__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./theme-selection */ "./packages/apps/onboarding/src/icons/theme-selection/index.ts");






/***/ }),

/***/ "./packages/apps/onboarding/src/icons/login/apple-icon.tsx":
/*!*****************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/login/apple-icon.tsx ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AppleIcon: function() { return /* binding */ AppleIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


function AppleIcon() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, {
    viewBox: "0 0 24 24",
    "aria-label": "Apple"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M18.8456 19.5C18.0156 20.74 17.1356 21.95 15.7956 21.97C14.4556 22 14.0256 21.18 12.5056 21.18C10.9756 21.18 10.5056 21.95 9.23559 22C7.92559 22.05 6.93559 20.68 6.09559 19.47C4.38559 17 3.07559 12.45 4.83559 9.39C5.70559 7.87 7.26559 6.91 8.95559 6.88C10.2356 6.86 11.4556 7.75 12.2456 7.75C13.0256 7.75 14.5056 6.68 16.0556 6.84C16.7056 6.87 18.5256 7.1 19.6956 8.82C19.6056 8.88 17.5256 10.1 17.5456 12.63C17.5756 15.65 20.1956 16.66 20.2256 16.67C20.1956 16.74 19.8056 18.11 18.8456 19.5ZM13.1356 3.5C13.8656 2.67 15.0756 2.04 16.0756 2C16.2056 3.17 15.7356 4.35 15.0356 5.19C14.3456 6.04 13.2056 6.7 12.0856 6.61C11.9356 5.46 12.4956 4.26 13.1356 3.5Z",
    fill: "currentColor"
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/login/facebook-icon.tsx":
/*!********************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/login/facebook-icon.tsx ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FacebookIcon: function() { return /* binding */ FacebookIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


function FacebookIcon() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, {
    viewBox: "0 0 24 24",
    "aria-label": "Facebook"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("g", {
    clipPath: "url(#fb-clip)"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M21 12C21 7.02947 16.9705 3 12 3C7.02947 3 3 7.02947 3 12C3 16.4921 6.29119 20.2155 10.5938 20.8907V14.6016H8.30859V12H10.5938V10.0172C10.5938 7.76156 11.9374 6.51562 13.9932 6.51562C14.9779 6.51562 16.0078 6.69141 16.0078 6.69141V8.90625H14.873C13.7549 8.90625 13.4062 9.60002 13.4062 10.3118V12H15.9023L15.5033 14.6016H13.4062V20.8907C17.7088 20.2155 21 16.4922 21 12Z",
    fill: "#3975EA"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M15.5033 14.6016L15.9023 12H13.4062V10.3118C13.4062 9.59995 13.7549 8.90625 14.873 8.90625H16.0078V6.69141C16.0078 6.69141 14.9779 6.51562 13.9931 6.51562C11.9374 6.51562 10.5938 7.76156 10.5938 10.0172V12H8.30859V14.6016H10.5938V20.8907C11.059 20.9636 11.5291 21.0001 12 21C12.4709 21.0001 12.941 20.9636 13.4062 20.8907V14.6016H15.5033Z",
    fill: "white"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("defs", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("clipPath", {
    id: "fb-clip"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("rect", {
    width: "18",
    height: "18",
    fill: "white",
    transform: "translate(3 3)"
  }))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/login/google-icon.tsx":
/*!******************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/login/google-icon.tsx ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GoogleIcon: function() { return /* binding */ GoogleIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


function GoogleIcon() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, {
    viewBox: "0 0 24 24",
    "aria-label": "Google"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M9.32142 4.43335C7.72278 4.98794 6.3441 6.04056 5.38791 7.4366C4.43172 8.83265 3.94841 10.4985 4.00898 12.1896C4.06954 13.8806 4.67078 15.5076 5.72439 16.8317C6.77801 18.1558 8.22845 19.1071 9.86267 19.5459C11.1876 19.8877 12.5757 19.9027 13.9077 19.5896C15.1143 19.3186 16.2299 18.7388 17.1452 17.9071C18.0978 17.015 18.7892 15.8802 19.1452 14.6246C19.5321 13.2592 19.6009 11.8233 19.3464 10.4271H11.9064V13.5134H16.2152C16.1291 14.0056 15.9445 14.4754 15.6726 14.8946C15.4007 15.3139 15.047 15.674 14.6327 15.9534C14.1065 16.3014 13.5134 16.5356 12.8914 16.6409C12.2676 16.7568 11.6277 16.7568 11.0039 16.6409C10.3717 16.5101 9.77355 16.2492 9.24767 15.8746C8.40286 15.2766 7.76852 14.427 7.43517 13.4471C7.09619 12.4488 7.09619 11.3666 7.43517 10.3684C7.67246 9.66862 8.06472 9.03152 8.58267 8.5046C9.17541 7.89054 9.92584 7.4516 10.7516 7.23595C11.5774 7.0203 12.4466 7.03627 13.2639 7.2821C13.9024 7.47809 14.4862 7.82052 14.9689 8.2821C15.4548 7.79877 15.9398 7.31419 16.4239 6.82835C16.6739 6.5671 16.9464 6.31835 17.1927 6.05085C16.4559 5.3652 15.591 4.83167 14.6477 4.48085C12.9298 3.85709 11.0501 3.84033 9.32142 4.43335Z",
    fill: "#515962"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M9.32128 4.43347C11.0499 3.84004 12.9295 3.85636 14.6475 4.47972C15.591 4.83293 16.4555 5.36902 17.1913 6.05722C16.9413 6.32472 16.6775 6.57472 16.4225 6.83472C15.9375 7.31889 15.4529 7.80139 14.9688 8.28222C14.4861 7.82064 13.9022 7.47821 13.2638 7.28222C12.4467 7.03552 11.5776 7.01863 10.7516 7.2334C9.92555 7.44817 9.17467 7.8863 8.58128 8.49972C8.06332 9.02664 7.67106 9.66374 7.43378 10.3635L4.84253 8.35722C5.77004 6.51792 7.37597 5.111 9.32128 4.43347Z",
    fill: "#E33629"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M4.155 10.3449C4.29427 9.65466 4.5255 8.9862 4.8425 8.35742L7.43375 10.3687C7.09476 11.3669 7.09476 12.4492 7.43375 13.4474C6.57041 14.1141 5.70666 14.7841 4.8425 15.4574C4.04893 13.8778 3.80691 12.078 4.155 10.3449Z",
    fill: "#F8BD00"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M11.9063 10.4258H19.3463C19.6008 11.8219 19.532 13.2579 19.1451 14.6233C18.7891 15.8789 18.0977 17.0137 17.1451 17.9058C16.3088 17.2533 15.4688 16.6058 14.6326 15.9533C15.0472 15.6736 15.401 15.3131 15.673 14.8934C15.9449 14.4737 16.1293 14.0035 16.2151 13.5108H11.9063C11.9051 12.4833 11.9063 11.4545 11.9063 10.4258Z",
    fill: "#587DBD"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M4.84131 15.4573C5.70548 14.7906 6.56923 14.1206 7.43256 13.4473C7.76657 14.4275 8.40181 15.2771 9.24756 15.8748C9.77507 16.2476 10.3745 16.5064 11.0076 16.6348C11.6314 16.7508 12.2712 16.7508 12.8951 16.6348C13.5171 16.5295 14.1102 16.2953 14.6363 15.9473C15.4726 16.5998 16.3126 17.2473 17.1488 17.8998C16.2337 18.7319 15.1181 19.3121 13.9113 19.5835C12.5793 19.8966 11.1912 19.8816 9.86631 19.5398C8.81844 19.26 7.83965 18.7668 6.99131 18.091C6.09339 17.3781 5.36 16.4797 4.84131 15.4573Z",
    fill: "#319F43"
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/accessibility-tools-icon.tsx":
/*!***************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/accessibility-tools-icon.tsx ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const AccessibilityToolsIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "16.4814",
    cy: "5.64844",
    r: "2.25",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M6.3341 10.6687L10.8605 11.3515C14.2811 11.8671 17.7598 11.8666 21.18 11.3492L25.6649 10.6698C26.6404 10.5223 27.5187 11.2782 27.5187 12.2649V12.9296C27.5186 13.7277 26.9344 14.4061 26.1452 14.5247L21.7875 15.1786C21.1891 15.2684 20.5856 15.3434 19.986 15.4357C19.5122 15.5086 19.1722 15.9214 19.1722 16.4007V17.9094C19.1722 18.3051 19.2445 18.7152 19.5101 19.0086L24.5906 24.6215C25.1883 25.2818 25.1373 26.3017 24.477 26.8993L23.7211 27.5832C23.0608 28.1807 22.042 28.1298 21.4444 27.4696L16.8529 22.3973C16.3956 21.8921 15.6022 21.8921 15.145 22.3973L10.5535 27.4696C9.95579 28.1299 8.93591 28.1809 8.27561 27.5832L7.5197 26.8993C6.85956 26.3018 6.80874 25.2818 7.40609 24.6215L12.497 18.9966C12.7597 18.7064 12.8324 18.3009 12.8324 17.9094V16.4085C12.8324 15.9237 12.4847 15.5082 12.0054 15.4354C11.4147 15.3457 10.8218 15.2669 10.2316 15.1786L5.8549 14.5236C5.06549 14.4052 4.48145 13.7268 4.48145 12.9285V12.2637C4.4815 11.2775 5.35894 10.5219 6.3341 10.6687Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (AccessibilityToolsIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/ai-generator-icon.tsx":
/*!********************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/ai-generator-icon.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const AIGeneratorIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M23.2273 4.83321C23.4152 4.47883 23.9229 4.47883 24.1108 4.83321L24.4249 5.42564C24.8747 6.27415 25.5688 6.9682 26.4173 7.41807L27.0097 7.73216C27.3641 7.92005 27.3641 8.42778 27.0097 8.61567L26.4173 8.92976C25.5688 9.37962 24.8747 10.0737 24.4249 10.9222L24.1108 11.5146C23.9229 11.869 23.4152 11.869 23.2273 11.5146L22.9132 10.9222C22.4633 10.0737 21.7693 9.37962 20.9208 8.92976L20.3283 8.61567C19.9739 8.42778 19.9739 7.92005 20.3283 7.73216L20.9208 7.41807C21.7693 6.9682 22.4633 6.27415 22.9132 5.42564L23.2273 4.83321Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.25"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M13.7075 8.79729C14.0833 8.08851 15.0988 8.08851 15.4745 8.79729L17.3436 12.3226C17.9059 13.3832 18.7735 14.2508 19.8341 14.8131L23.3594 16.6821C24.0682 17.0579 24.0682 18.0734 23.3594 18.4491L19.8341 20.3182C18.7735 20.8805 17.9059 21.7481 17.3436 22.8087L15.4745 26.334C15.0988 27.0428 14.0833 27.0428 13.7075 26.334L11.8385 22.8087C11.2762 21.7481 10.4086 20.8805 9.34796 20.3182L5.82268 18.4492C5.1139 18.0734 5.1139 17.0579 5.82268 16.6821L9.34796 14.8131C10.4086 14.2508 11.2762 13.3832 11.8385 12.3226L13.7075 8.79729Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (AIGeneratorIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/classes-variables-icon.tsx":
/*!*************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/classes-variables-icon.tsx ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const ClassesVariablesIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M6.66797 4H12.0013C12.7085 4 13.3868 4.28095 13.8869 4.78105C14.387 5.28115 14.668 5.95942 14.668 6.66667V22.6667C14.668 24.0812 14.1061 25.4377 13.1059 26.4379C12.1057 27.4381 10.7491 28 9.33464 28C7.92015 28 6.56359 27.4381 5.5634 26.4379C4.56321 25.4377 4.0013 24.0812 4.0013 22.6667V6.66667C4.0013 5.95942 4.28225 5.28115 4.78235 4.78105C5.28245 4.28095 5.96072 4 6.66797 4Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M14.6685 9.8029L17.3351 7.13623C17.8352 6.63631 18.5134 6.35547 19.2205 6.35547C19.9276 6.35547 20.6057 6.63631 21.1058 7.13623L24.8765 10.9069C25.3764 11.407 25.6572 12.0851 25.6572 12.7922C25.6572 13.4993 25.3764 14.1775 24.8765 14.6776L12.8765 26.6776",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22.2686 17.332H25.3353C26.0425 17.332 26.7208 17.613 27.2209 18.1131C27.721 18.6132 28.002 19.2915 28.002 19.9987V25.332C28.002 26.0393 27.721 26.7176 27.2209 27.2176C26.7208 27.7177 26.0425 27.9987 25.3353 27.9987H9.33529",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M9.33496 22.668V22.6813",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (ClassesVariablesIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/cookie-consent-icon.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/cookie-consent-icon.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const CookieConsentIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "14.56",
    cy: "12.317",
    r: "2.16",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "11.801",
    cy: "19.158",
    r: "1.8",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "19.96",
    cy: "19.96",
    r: "1.56",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M16 3.999C16.19 3.999 16.378 4.004 16.566 4.013C17.055 4.037 17.446 4.395 17.654 4.838C18.421 6.47 20.078 7.599 22 7.599C22.609 7.599 23.2 8.19 23.2 8.799C23.2 11.049 24.748 12.938 26.837 13.458C27.369 13.59 27.84 13.969 27.908 14.513C27.969 15 28 15.496 28 16C28 22.627 22.628 28 16 28C9.372 28 4 22.627 4 16C4.001 9.372 9.373 3.999 16 3.999Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (CookieConsentIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/core-placeholder-icon.tsx":
/*!************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/core-placeholder-icon.tsx ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const CorePlaceholderIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M15.333 15.9997C15.333 16.1765 15.4032 16.3461 15.5283 16.4711C15.6533 16.5961 15.8229 16.6663 15.9997 16.6663C16.1765 16.6663 16.3461 16.5961 16.4711 16.4711C16.5961 16.3461 16.6663 16.1765 16.6663 15.9997C16.6663 15.8229 16.5961 15.6533 16.4711 15.5283C16.3461 15.4032 16.1765 15.333 15.9997 15.333C15.8229 15.333 15.6533 15.4032 15.5283 15.5283C15.4032 15.6533 15.333 15.8229 15.333 15.9997Z",
    fill: "currentColor",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M6.66699 16.0003C6.66699 17.226 6.90841 18.4397 7.37745 19.572C7.84649 20.7044 8.53398 21.7333 9.40066 22.6C10.2673 23.4667 11.2962 24.1542 12.4286 24.6232C13.561 25.0922 14.7747 25.3337 16.0003 25.3337C17.226 25.3337 18.4397 25.0922 19.572 24.6232C20.7044 24.1542 21.7333 23.4667 22.6 22.6C23.4667 21.7333 24.1542 20.7044 24.6232 19.572C25.0922 18.4397 25.3337 17.226 25.3337 16.0003C25.3337 14.7747 25.0922 13.561 24.6232 12.4286C24.1542 11.2962 23.4667 10.2673 22.6 9.40066C21.7333 8.53398 20.7044 7.84649 19.572 7.37745C18.4397 6.90841 17.226 6.66699 16.0003 6.66699C14.7747 6.66699 13.561 6.90841 12.4286 7.37745C11.2962 7.84649 10.2673 8.53398 9.40066 9.40066C8.53398 10.2673 7.84649 11.2962 7.37745 12.4286C6.90841 13.561 6.66699 14.7747 6.66699 16.0003Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M16 4V6.66667",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M4 16H6.66667",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M16 25.333V27.9997",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M25.333 16H27.9997",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (CorePlaceholderIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/custom-code-icon.tsx":
/*!*******************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/custom-code-icon.tsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const CustomCodeIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M9.33333 10.666L4 15.9993L9.33333 21.3327",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22.667 10.666L28.0003 15.9993L22.667 21.3327",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M18.6663 5.33301L13.333 26.6663",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (CustomCodeIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/email-deliverability-icon.tsx":
/*!****************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/email-deliverability-icon.tsx ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const EmailDeliverabilityIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M13.8998 26.8185H8.91711C6.32659 26.8185 4.22656 24.7185 4.22656 22.128V9.15636C4.22656 7.17638 5.82548 5.56771 7.80544 5.55962C13.2372 5.53744 18.6688 5.5374 24.1005 5.5596C26.0804 5.56769 27.6793 7.17636 27.6793 9.15632V16.6031",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M5.35254 6.66406L13.4831 11.7007C14.9964 12.6381 16.91 12.6381 18.4233 11.7007L26.5538 6.66406",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M28.2246 21.62L19.5512 16.2156C18.5501 15.5918 17.344 16.6355 17.8176 17.7158L19.6501 21.8962C19.7847 22.2033 19.7847 22.5527 19.6501 22.8598L17.7337 27.2315C17.268 28.2939 18.4308 29.3331 19.4344 28.7515L28.1917 23.6767C28.9747 23.223 28.9927 22.0987 28.2246 21.62Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M12.7002 22.6016L15.1002 22.6016",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M10 18.3984L14.2 18.3984",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (EmailDeliverabilityIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/explore-more-icon.tsx":
/*!********************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/explore-more-icon.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const ExploreMoreIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref,
    width: "24",
    height: "24"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M6.74805 3.75L12.748 9.75L6.74805 15.75",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (ExploreMoreIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/hello-theme-icon.tsx":
/*!*******************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/hello-theme-icon.tsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const HelloThemeIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("rect", {
    x: "4",
    y: "4",
    width: "24",
    height: "24",
    rx: "1.25",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M12 11.75V21.36M20 11.75V21.36M4 11.22H28M4 20.82H28",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (HelloThemeIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/image-optimization-icon.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/image-optimization-icon.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const ImageOptimizationIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("rect", {
    x: "4.59961",
    y: "4.60156",
    width: "22.8",
    height: "22.8",
    rx: "4.56",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M5.74023 26.2611L11.8306 18.6481C12.2446 18.1306 12.9998 18.0467 13.5173 18.4607L17.3432 21.5215C17.8607 21.9355 18.6159 21.8516 19.0299 21.3341L27.4002 10.8711",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "10.4802",
    cy: "10.4792",
    r: "2.28",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (ImageOptimizationIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/index.ts":
/*!*******************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/index.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AIGeneratorIcon: function() { return /* reexport safe */ _ai_generator_icon__WEBPACK_IMPORTED_MODULE_1__["default"]; },
/* harmony export */   AccessibilityToolsIcon: function() { return /* reexport safe */ _accessibility_tools_icon__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   ClassesVariablesIcon: function() { return /* reexport safe */ _classes_variables_icon__WEBPACK_IMPORTED_MODULE_2__["default"]; },
/* harmony export */   CookieConsentIcon: function() { return /* reexport safe */ _cookie_consent_icon__WEBPACK_IMPORTED_MODULE_3__["default"]; },
/* harmony export */   CorePlaceholderIcon: function() { return /* reexport safe */ _core_placeholder_icon__WEBPACK_IMPORTED_MODULE_4__["default"]; },
/* harmony export */   CustomCodeIcon: function() { return /* reexport safe */ _custom_code_icon__WEBPACK_IMPORTED_MODULE_5__["default"]; },
/* harmony export */   EmailDeliverabilityIcon: function() { return /* reexport safe */ _email_deliverability_icon__WEBPACK_IMPORTED_MODULE_7__["default"]; },
/* harmony export */   ExploreMoreIcon: function() { return /* reexport safe */ _explore_more_icon__WEBPACK_IMPORTED_MODULE_8__["default"]; },
/* harmony export */   HelloThemeIcon: function() { return /* reexport safe */ _hello_theme_icon__WEBPACK_IMPORTED_MODULE_9__["default"]; },
/* harmony export */   ImageOptimizationIcon: function() { return /* reexport safe */ _image_optimization_icon__WEBPACK_IMPORTED_MODULE_10__["default"]; },
/* harmony export */   InteractionsIcon: function() { return /* reexport safe */ _interactions_icon__WEBPACK_IMPORTED_MODULE_6__["default"]; },
/* harmony export */   ThemeBuilderIcon: function() { return /* reexport safe */ _theme_builder_icon__WEBPACK_IMPORTED_MODULE_11__["default"]; },
/* harmony export */   WoocommerceIcon: function() { return /* reexport safe */ _woocommerce_icon__WEBPACK_IMPORTED_MODULE_12__["default"]; }
/* harmony export */ });
/* harmony import */ var _accessibility_tools_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./accessibility-tools-icon */ "./packages/apps/onboarding/src/icons/site-features/accessibility-tools-icon.tsx");
/* harmony import */ var _ai_generator_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ai-generator-icon */ "./packages/apps/onboarding/src/icons/site-features/ai-generator-icon.tsx");
/* harmony import */ var _classes_variables_icon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./classes-variables-icon */ "./packages/apps/onboarding/src/icons/site-features/classes-variables-icon.tsx");
/* harmony import */ var _cookie_consent_icon__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./cookie-consent-icon */ "./packages/apps/onboarding/src/icons/site-features/cookie-consent-icon.tsx");
/* harmony import */ var _core_placeholder_icon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./core-placeholder-icon */ "./packages/apps/onboarding/src/icons/site-features/core-placeholder-icon.tsx");
/* harmony import */ var _custom_code_icon__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./custom-code-icon */ "./packages/apps/onboarding/src/icons/site-features/custom-code-icon.tsx");
/* harmony import */ var _interactions_icon__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./interactions-icon */ "./packages/apps/onboarding/src/icons/site-features/interactions-icon.tsx");
/* harmony import */ var _email_deliverability_icon__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./email-deliverability-icon */ "./packages/apps/onboarding/src/icons/site-features/email-deliverability-icon.tsx");
/* harmony import */ var _explore_more_icon__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./explore-more-icon */ "./packages/apps/onboarding/src/icons/site-features/explore-more-icon.tsx");
/* harmony import */ var _hello_theme_icon__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./hello-theme-icon */ "./packages/apps/onboarding/src/icons/site-features/hello-theme-icon.tsx");
/* harmony import */ var _image_optimization_icon__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./image-optimization-icon */ "./packages/apps/onboarding/src/icons/site-features/image-optimization-icon.tsx");
/* harmony import */ var _theme_builder_icon__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./theme-builder-icon */ "./packages/apps/onboarding/src/icons/site-features/theme-builder-icon.tsx");
/* harmony import */ var _woocommerce_icon__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./woocommerce-icon */ "./packages/apps/onboarding/src/icons/site-features/woocommerce-icon.tsx");














/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/interactions-icon.tsx":
/*!********************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/interactions-icon.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const InteractionsIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M12.3001 24.5497C12.1024 24.7925 11.8534 24.9888 11.5711 25.1243C11.2887 25.2598 10.9799 25.3313 10.6667 25.3337C10.0427 25.3337 9.44807 25.0483 9.03341 24.5497L3.21874 17.5523C2.86185 17.1138 2.66699 16.5657 2.66699 16.0003C2.66699 15.4349 2.86185 14.8868 3.21874 14.4483L9.03341 7.45099C9.23113 7.2081 9.48006 7.01189 9.76241 6.87636C10.0448 6.74083 10.3536 6.66934 10.6667 6.66699C11.2907 6.66699 11.8854 6.95233 12.3001 7.45099L18.1147 14.4483C18.4716 14.8868 18.6665 15.4349 18.6665 16.0003C18.6665 16.5657 18.4716 17.1138 18.1147 17.5523L12.3001 24.5497Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22.667 6.66699L28.7817 14.4483C29.1386 14.8868 29.3334 15.4349 29.3334 16.0003C29.3334 16.5657 29.1386 17.1138 28.7817 17.5523L22.667 25.3337",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M17.333 6.66699L23.4477 14.4483C23.8046 14.8868 23.9994 15.4349 23.9994 16.0003C23.9994 16.5657 23.8046 17.1138 23.4477 17.5523L17.333 25.3337",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (InteractionsIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/theme-builder-icon.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/theme-builder-icon.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const ThemeBuilderIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M4.5 5.9375C4.5 5.55625 4.65145 5.19062 4.92103 4.92103C5.19062 4.65145 5.55625 4.5 5.9375 4.5H26.0625C26.4437 4.5 26.8094 4.65145 27.079 4.92103C27.3486 5.19062 27.5 5.55625 27.5 5.9375V8.8125C27.5 9.19375 27.3486 9.55938 27.079 9.82897C26.8094 10.0985 26.4437 10.25 26.0625 10.25H5.9375C5.55625 10.25 5.19062 10.0985 4.92103 9.82897C4.65145 9.55938 4.5 9.19375 4.5 8.8125V5.9375Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M4.5 17.4375C4.5 17.0563 4.65145 16.6906 4.92103 16.421C5.19062 16.1515 5.55625 16 5.9375 16H11.6875C12.0687 16 12.4344 16.1515 12.704 16.421C12.9735 16.6906 13.125 17.0563 13.125 17.4375V26.0625C13.125 26.4437 12.9735 26.8094 12.704 27.079C12.4344 27.3486 12.0687 27.5 11.6875 27.5H5.9375C5.55625 27.5 5.19062 27.3486 4.92103 27.079C4.65145 26.8094 4.5 26.4437 4.5 26.0625V17.4375Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M18.875 16H27.5",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M18.875 21.75H27.5",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M18.875 27.5H27.5",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (ThemeBuilderIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/site-features/woocommerce-icon.tsx":
/*!*******************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/site-features/woocommerce-icon.tsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const WoocommerceIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 32 32"
  }, props, {
    ref: ref,
    width: "32",
    height: "32"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M17.9998 28V18C17.9998 17.7348 18.1052 17.4804 18.2927 17.2929C18.4803 17.1054 18.7346 17 18.9998 17H22.9998C23.265 17 23.5194 17.1054 23.7069 17.2929C23.8945 17.4804 23.9998 17.7348 23.9998 18V28M17.9998 28H3.14649M17.9998 28H23.9998M23.9998 28H28.8532M26.9998 28V12.4653M26.9998 12.4653C26.1999 12.9268 25.2651 13.0974 24.3537 12.9482C23.4424 12.799 22.6108 12.3391 21.9998 11.6467C21.2665 12.476 20.1945 13 18.9998 13C18.4322 13.0005 17.871 12.88 17.3536 12.6464C16.8363 12.4128 16.3748 12.0715 15.9998 11.6453C15.2665 12.476 14.1945 13 12.9998 13C12.4322 13.0005 11.871 12.88 11.3536 12.6464C10.8363 12.4128 10.3748 12.0715 9.99982 11.6453C9.38905 12.338 8.55748 12.7981 7.64615 12.9476C6.73482 13.097 5.79985 12.9266 4.99982 12.4653M26.9998 12.4653C27.5313 12.1585 27.985 11.7336 28.326 11.2234C28.6669 10.7131 28.8858 10.1313 28.9659 9.52287C29.0459 8.91446 28.9849 8.29579 28.7876 7.71473C28.5902 7.13368 28.2618 6.60582 27.8278 6.172L26.2412 4.58667C25.8664 4.21149 25.3581 4.00047 24.8278 4H7.17049C6.64043 4.00011 6.13209 4.21064 5.75716 4.58533L4.17182 6.172C3.73881 6.60632 3.41131 7.13427 3.21458 7.71516C3.01786 8.29605 2.95715 8.91436 3.03715 9.52242C3.11715 10.1305 3.33572 10.712 3.67601 11.2223C4.01629 11.7325 4.4692 12.1578 4.99982 12.4653M4.99982 28V12.4653M8.99982 24H13.9998C14.265 24 14.5194 23.8946 14.7069 23.7071C14.8945 23.5196 14.9998 23.2652 14.9998 23V18C14.9998 17.7348 14.8945 17.4804 14.7069 17.2929C14.5194 17.1054 14.265 17 13.9998 17H8.99982C8.73461 17 8.48025 17.1054 8.29272 17.2929C8.10518 17.4804 7.99982 17.7348 7.99982 18V23C7.99982 23.552 8.44782 24 8.99982 24Z",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (WoocommerceIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/theme-selection/elementor-logo-icon.tsx":
/*!************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/theme-selection/elementor-logo-icon.tsx ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const ElementorLogoIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 44 44"
  }, props, {
    ref: ref
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("circle", {
    cx: "22",
    cy: "22",
    r: "22",
    fill: "#ffffff"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M22 0C9.85 0 0 9.85 0 22s9.85 22 22 22 22-9.85 22-22S34.15 0 22 0ZM15.4 33H11V11h4.4v22Zm17.6 0H19.8v-4.4H33V33Zm0-8.8H19.8v-4.4H33v4.4Zm0-8.8H19.8V11H33v4.4Z",
    fill: "#515962",
    fillOpacity: "0.38"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (ElementorLogoIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/theme-selection/hello-layout-icon.tsx":
/*!**********************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/theme-selection/hello-layout-icon.tsx ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const HelloLayoutIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef((props, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 48 48"
  }, props, {
    ref: ref
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("rect", {
    x: "1.66",
    y: "1.66",
    width: "44.68",
    height: "44.68",
    rx: "3.88",
    ry: "3.88",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "3.33"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M3.33 17.11H44.67M3.33 30.89H44.67",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "3.33"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: "M17.11 17.11V30.89M30.89 17.11V30.89",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "3.33"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (HelloLayoutIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/icons/theme-selection/index.ts":
/*!*********************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/theme-selection/index.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementorLogoIcon: function() { return /* reexport safe */ _elementor_logo_icon__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   HelloLayoutIcon: function() { return /* reexport safe */ _hello_layout_icon__WEBPACK_IMPORTED_MODULE_1__["default"]; },
/* harmony export */   PlusIcon: function() { return /* reexport safe */ _plus_icon__WEBPACK_IMPORTED_MODULE_2__["default"]; }
/* harmony export */ });
/* harmony import */ var _elementor_logo_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./elementor-logo-icon */ "./packages/apps/onboarding/src/icons/theme-selection/elementor-logo-icon.tsx");
/* harmony import */ var _hello_layout_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hello-layout-icon */ "./packages/apps/onboarding/src/icons/theme-selection/hello-layout-icon.tsx");
/* harmony import */ var _plus_icon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./plus-icon */ "./packages/apps/onboarding/src/icons/theme-selection/plus-icon.tsx");




/***/ }),

/***/ "./packages/apps/onboarding/src/icons/theme-selection/plus-icon.tsx":
/*!**************************************************************************!*\
  !*** ./packages/apps/onboarding/src/icons/theme-selection/plus-icon.tsx ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


const PLUS_PATH = 'M20.7273 2.72725C21.8317 2.72726 22.7271 3.62279 22.7273 4.72725V11.8171H29.8181C30.9226 11.8171 31.818 12.7126 31.8181 13.8171V20.7263C31.818 21.8308 30.9226 22.7263 29.8181 22.7263H22.7273V29.8181C22.7273 30.9226 21.8318 31.8181 20.7273 31.8181H13.8181C12.7136 31.818 11.8181 30.9226 11.8181 29.8181V22.7263H4.72725C3.62282 22.7261 2.72729 21.8307 2.72725 20.7263V13.8171C2.72731 12.7127 3.62284 11.8172 4.72725 11.8171H11.8181V4.72725C11.8182 3.62283 12.7136 2.72733 13.8181 2.72725H20.7273Z';
const PlusIcon = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.forwardRef(({
  sx,
  strokeColor = '#ffffff',
  ...props
}, ref) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.SvgIcon, _extends({
    viewBox: "0 0 34.545 34.545"
  }, props, {
    ref: ref,
    sx: {
      overflow: 'visible',
      ...sx
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: PLUS_PATH,
    fill: "none",
    stroke: strokeColor,
    strokeWidth: "5.454",
    strokeLinejoin: "round"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    d: PLUS_PATH,
    fill: "currentColor"
  }));
});
/* harmony default export */ __webpack_exports__["default"] = (PlusIcon);

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/components/site-features/feature-grid.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/components/site-features/feature-grid.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FeatureGrid: function() { return /* binding */ FeatureGrid; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_ui_selection_badge__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../components/ui/selection-badge */ "./packages/apps/onboarding/src/components/ui/selection-badge.tsx");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _pro_plan_notice__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./pro-plan-notice */ "./packages/apps/onboarding/src/steps/components/site-features/pro-plan-notice.tsx");






const CornerChip = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Chip)(({
  theme
}) => ({
  position: 'absolute',
  insetBlockStart: theme.spacing(0.75),
  insetInlineStart: theme.spacing(0.75),
  height: theme.spacing(2.25),
  '& .MuiChip-label': {
    fontSize: theme.spacing(1.5),
    padding: `${theme.spacing(0.375)} ${theme.spacing(0.625)}`
  }
}));
const FeatureCard = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
  shouldForwardProp: prop => !['isSelected', 'isCore'].includes(prop)
})(({
  theme,
  isSelected,
  isCore
}) => ({
  position: 'relative',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  aspectRatio: '1',
  minHeight: theme.spacing(12),
  padding: theme.spacing(2),
  borderRadius: theme.spacing(1),
  border: isSelected ? `2px solid ${theme.palette.text.primary}` : `1px solid ${theme.palette.divider}`,
  cursor: isCore ? 'default' : 'pointer',
  transition: 'border-color 0.2s ease, background-color 0.2s ease',
  ...(!isCore && {
    '&:hover': {
      backgroundColor: theme.palette.action.hover
    }
  })
}));
function FeatureGrid({
  options,
  selectedValues,
  onFeatureClick
}) {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useTheme)();
  const isPaid = licenseType => licenseType === 'pro' || licenseType === 'one';
  const selectedPaidFeatures = selectedValues.filter(id => {
    const featureOption = options.find(item => item.id === id);
    return featureOption && isPaid(featureOption.licenseType);
  });
  const shouldDisplayProPlanNotice = selectedPaidFeatures.length > 0;
  const planName = selectedPaidFeatures.some(id => {
    const featureOption = options.find(item => item.id === id);
    return featureOption?.licenseType === 'one';
  }) ? 'One' : 'Pro';
  const handleKeyDown = (event, handler) => {
    if (['Enter', ' '].includes(event.key)) {
      event.preventDefault();
      handler();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    justifyContent: "center",
    sx: {
      display: 'grid',
      gridTemplateColumns: {
        xs: 'repeat(auto-fit, minmax(100px, 135px))',
        sm: 'repeat(4, 140px)',
        md: 'repeat(5, 140px)'
      },
      gap: 2,
      width: '100%'
    }
  }, options.map(option => {
    const isSelected = selectedValues.includes(option.id);
    const Icon = option.Icon;
    const isOptionPaid = isPaid(option.licenseType);
    const BadgeIcon = isOptionPaid ? _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CrownFilledIcon : _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CheckIcon;
    const isCore = option.licenseType === 'core';
    const handleClick = () => onFeatureClick(option.id);
    const handleKeyDownEvent = isCore ? undefined : event => handleKeyDown(event, handleClick);
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(FeatureCard, {
      key: option.id,
      "data-testid": `feature-card-${option.id}`,
      isSelected: isSelected,
      isCore: isCore,
      onClick: isCore ? undefined : handleClick,
      role: isCore ? undefined : 'button',
      tabIndex: isCore ? undefined : 0,
      onKeyDown: handleKeyDownEvent,
      "aria-pressed": isCore ? undefined : isSelected,
      "aria-label": isCore ? undefined : (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)(option.labelKey)
    }, isCore && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CornerChip, {
      label: (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)('steps.site_features.included'),
      size: "small"
    }), isSelected && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_selection_badge__WEBPACK_IMPORTED_MODULE_3__.SelectionBadge, {
      icon: BadgeIcon,
      variant: isOptionPaid ? 'paid' : 'free'
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
      className: "feature-icon",
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      color: "primary.dark",
      width: theme.spacing(4),
      height: theme.spacing(4),
      sx: {
        mt: 2,
        mb: 1
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
      sx: {
        width: theme.spacing(4),
        height: theme.spacing(4),
        fontSize: theme.spacing(4)
      }
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
      variant: "body2",
      color: "text.secondary",
      display: "flex",
      alignItems: "center",
      align: "center",
      sx: {
        minHeight: theme.spacing(5)
      }
    }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)(option.labelKey)));
  }), shouldDisplayProPlanNotice && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_pro_plan_notice__WEBPACK_IMPORTED_MODULE_5__.ProPlanNotice, {
    planName: planName
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/components/site-features/feature-options.ts":
/*!****************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/components/site-features/feature-options.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COOKIE_CONSENT_FEATURE_ID: function() { return /* binding */ COOKIE_CONSENT_FEATURE_ID; },
/* harmony export */   CORE_FEATURE_IDS: function() { return /* binding */ CORE_FEATURE_IDS; },
/* harmony export */   FEATURE_OPTIONS: function() { return /* binding */ FEATURE_OPTIONS; }
/* harmony export */ });
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../icons */ "./packages/apps/onboarding/src/icons/index.ts");

const COOKIE_CONSENT_FEATURE_ID = 'cookie_consent';
const FEATURE_OPTIONS = [{
  id: 'classes_variables',
  labelKey: 'steps.site_features.option_classes_variables',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.ClassesVariablesIcon,
  licenseType: 'core'
}, {
  id: 'interactions',
  labelKey: 'steps.site_features.option_interactions',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.InteractionsIcon,
  licenseType: 'core'
}, {
  id: 'theme_builder',
  labelKey: 'steps.site_features.option_theme_builder',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.ThemeBuilderIcon,
  licenseType: 'pro'
}, {
  id: 'lead_collection',
  labelKey: 'steps.site_features.option_lead_collection',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.CorePlaceholderIcon,
  licenseType: 'pro'
}, {
  id: 'custom_code_css',
  labelKey: 'steps.site_features.option_custom_code',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.CustomCodeIcon,
  licenseType: 'pro'
}, {
  id: 'email_deliverability',
  labelKey: 'steps.site_features.option_email_deliverability',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.EmailDeliverabilityIcon,
  licenseType: 'one'
}, {
  id: COOKIE_CONSENT_FEATURE_ID,
  labelKey: 'steps.site_features.option_cookie_consent',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.CookieConsentIcon,
  licenseType: 'one'
}, {
  id: 'ai_features',
  labelKey: 'steps.site_features.option_ai_generator',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.AIGeneratorIcon,
  licenseType: 'one'
}, {
  id: 'image_optimization',
  labelKey: 'steps.site_features.option_image_optimization',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.ImageOptimizationIcon,
  licenseType: 'one'
}, {
  id: 'accessibility',
  labelKey: 'steps.site_features.option_accessibility_tools',
  Icon: _icons__WEBPACK_IMPORTED_MODULE_0__.AccessibilityToolsIcon,
  licenseType: 'one'
}];
const CORE_FEATURE_IDS = new Set(FEATURE_OPTIONS.flatMap(option => option.licenseType === 'core' ? [option.id] : []));

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/components/site-features/index.ts":
/*!******************************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/components/site-features/index.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COOKIE_CONSENT_FEATURE_ID: function() { return /* reexport safe */ _feature_options__WEBPACK_IMPORTED_MODULE_1__.COOKIE_CONSENT_FEATURE_ID; },
/* harmony export */   CORE_FEATURE_IDS: function() { return /* reexport safe */ _feature_options__WEBPACK_IMPORTED_MODULE_1__.CORE_FEATURE_IDS; },
/* harmony export */   FEATURE_OPTIONS: function() { return /* reexport safe */ _feature_options__WEBPACK_IMPORTED_MODULE_1__.FEATURE_OPTIONS; },
/* harmony export */   FeatureGrid: function() { return /* reexport safe */ _feature_grid__WEBPACK_IMPORTED_MODULE_0__.FeatureGrid; },
/* harmony export */   ProPlanNotice: function() { return /* reexport safe */ _pro_plan_notice__WEBPACK_IMPORTED_MODULE_2__.ProPlanNotice; }
/* harmony export */ });
/* harmony import */ var _feature_grid__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./feature-grid */ "./packages/apps/onboarding/src/steps/components/site-features/feature-grid.tsx");
/* harmony import */ var _feature_options__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./feature-options */ "./packages/apps/onboarding/src/steps/components/site-features/feature-options.ts");
/* harmony import */ var _pro_plan_notice__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./pro-plan-notice */ "./packages/apps/onboarding/src/steps/components/site-features/pro-plan-notice.tsx");




/***/ }),

/***/ "./packages/apps/onboarding/src/steps/components/site-features/pro-plan-notice.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/components/site-features/pro-plan-notice.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ProPlanNotice: function() { return /* binding */ ProPlanNotice; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../hooks/use-onboarding */ "./packages/apps/onboarding/src/hooks/use-onboarding.ts");
/* harmony import */ var _hooks_use_onboarding_event__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../hooks/use-onboarding-event */ "./packages/apps/onboarding/src/hooks/use-onboarding-event.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");







const PRO_PLAN_NOTICE_BG_LIGHT = '#FAE4FA';
const PRO_PLAN_NOTICE_BG_DARK = '#491146';
const ProPlanNoticeRoot = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box)(({
  theme
}) => ({
  display: 'flex',
  alignItems: 'center',
  gap: theme.spacing(1),
  padding: theme.spacing(1.5, 3),
  borderRadius: theme.spacing(2),
  backgroundColor: theme.palette.mode === 'dark' ? PRO_PLAN_NOTICE_BG_DARK : PRO_PLAN_NOTICE_BG_LIGHT,
  width: 'max-content',
  gridColumn: '1 / -1',
  marginBlockStart: theme.spacing(2),
  [theme.breakpoints.down('sm')]: {
    flexDirection: 'column',
    justifyContent: 'center',
    justifySelf: 'center'
  }
}));
function ProPlanNotice({
  planName
}) {
  const {
    urls,
    choices
  } = (0,_hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_3__.useOnboarding)();
  const {
    trackProFeaturesSelected
  } = (0,_hooks_use_onboarding_event__WEBPACK_IMPORTED_MODULE_4__.useOnboardingEvent)();
  const comparePlansUrl = urls.comparePlans;
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.useTheme)();
  const isOne = 'One' === planName;
  const handleComparePlansClick = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    trackProFeaturesSelected({
      targetName: 'compare plans',
      features: choices.site_features || []
    });
  }, [trackProFeaturesSelected, choices.site_features]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ProPlanNoticeRoot, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, {
    direction: "row",
    spacing: 1.5,
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    color: "text.primary",
    fontSize: theme.spacing(2)
  }, isOne ? (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('steps.site_features.plan_recommendation_one') : (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('steps.site_features.plan_recommendation_pro'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Link, {
    href: comparePlansUrl,
    target: "_blank",
    color: theme.palette.mode === 'dark' ? 'common.white' : 'promotion.main',
    onClick: handleComparePlansClick,
    sx: {
      display: 'flex',
      alignItems: 'center',
      gap: theme.spacing(0.25),
      fontSize: theme.spacing(2),
      '&:hover': {
        textDecoration: 'underline',
        textDecorationColor: 'rgba(147, 0, 63, 0.4)'
      },
      '& > svg': {
        fontSize: 'inherit'
      }
    }
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('steps.site_features.compare_plans'), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ArrowUpRightIcon, null)));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/screens/login.tsx":
/*!**************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/screens/login.tsx ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Login: function() { return /* binding */ Login; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/fullscreen-card */ "./packages/apps/onboarding/src/components/fullscreen-card.tsx");
/* harmony import */ var _components_ui_elementor_icon__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/ui/elementor-icon */ "./packages/apps/onboarding/src/components/ui/elementor-icon.tsx");
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../icons */ "./packages/apps/onboarding/src/icons/index.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");






function Login({
  onConnect,
  onSignUp,
  onContinueAsGuest
}) {
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.FullscreenCard, {
    "data-testid": "login-screen"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    display: "flex",
    alignItems: "center",
    marginBottom: -1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_elementor_icon__WEBPACK_IMPORTED_MODULE_3__.ElementorIcon, {
    sx: {
      width: 32,
      height: 32
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "h5",
    color: "text.primary",
    align: "center",
    fontWeight: 500,
    fontFamily: "Poppins"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('login.title')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    width: "100%",
    gap: theme.spacing(2)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.PrimaryButton, {
    variant: "contained",
    color: "primary",
    fullWidth: true,
    size: "large",
    onClick: onConnect
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('login.sign_in')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 6
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 2,
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.SecondaryButton, {
    onClick: onSignUp,
    variant: "outlined",
    color: "primary",
    fullWidth: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    display: "flex",
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.SocialIconWrapper, {
    elevation: 24
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_icons__WEBPACK_IMPORTED_MODULE_4__.GoogleIcon, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.SocialIconWrapper, {
    elevation: 24
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_icons__WEBPACK_IMPORTED_MODULE_4__.FacebookIcon, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.SocialIconWrapper, {
    elevation: 24
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_icons__WEBPACK_IMPORTED_MODULE_4__.AppleIcon, null))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "button",
    fontWeight: "500"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('login.continue_another_way')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.TextButton, {
    href: '#',
    color: "info.main",
    align: "center",
    variant: "body2",
    onClick: event => onContinueAsGuest?.(event),
    sx: {
      textDecoration: 'none'
    }
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_5__.t)('common.skip')))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/screens/pro-install.tsx":
/*!********************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/screens/pro-install.tsx ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ProInstall: function() { return /* binding */ ProInstall; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/fullscreen-card */ "./packages/apps/onboarding/src/components/fullscreen-card.tsx");
/* harmony import */ var _components_toast_toast_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/toast/toast-context */ "./packages/apps/onboarding/src/components/toast/toast-context.tsx");
/* harmony import */ var _hooks_use_install_pro__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-install-pro */ "./packages/apps/onboarding/src/hooks/use-install-pro.ts");
/* harmony import */ var _hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../hooks/use-onboarding */ "./packages/apps/onboarding/src/hooks/use-onboarding.ts");
/* harmony import */ var _hooks_use_onboarding_event__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../hooks/use-onboarding-event */ "./packages/apps/onboarding/src/hooks/use-onboarding-event.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _step_visuals__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../step-visuals */ "./packages/apps/onboarding/src/steps/step-visuals.ts");










const ProLogo = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.styled)('img')(({
  theme
}) => ({
  maxWidth: 200,
  height: 'auto',
  margin: theme.spacing(1, 0)
}));
function ProInstall() {
  const {
    actions
  } = (0,_hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_5__.useOnboarding)();
  const installPro = (0,_hooks_use_install_pro__WEBPACK_IMPORTED_MODULE_4__.useInstallPro)();
  const {
    showToast
  } = (0,_components_toast_toast_context__WEBPACK_IMPORTED_MODULE_3__.useToast)();
  const {
    trackProInstall,
    trackStepViewed,
    trackErrorReported
  } = (0,_hooks_use_onboarding_event__WEBPACK_IMPORTED_MODULE_6__.useOnboardingEvent)();
  const hasTrackedView = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!hasTrackedView.current) {
      hasTrackedView.current = true;
      trackStepViewed('pro_install');
    }
  }, [trackStepViewed]);
  function handleInstall() {
    trackProInstall('install');
    installPro.mutate(undefined, {
      onSuccess: () => {
        actions.markProInstalled();
      },
      onError: error => {
        trackErrorReported({
          targetType: 'install',
          targetName: 'install_pro_on_this_site',
          stepId: 'pro_install',
          errorBody: error instanceof Error ? error.message : 'Failed to install Elementor Pro'
        });
        showToast((0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('error.pro_install_failed'));
        actions.dismissProInstallScreen();
      }
    });
  }
  function handleDismiss(event) {
    event.preventDefault();
    trackProInstall('later');
    actions.dismissProInstallScreen();
  }
  const isInstalling = installPro.isPending;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.FullscreenCard, {
    "data-testid": "pro-install-screen"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "h5",
    color: "text.primary",
    align: "center",
    fontWeight: 500,
    fontFamily: "Poppins",
    marginBottom: -2
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.title')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body2",
    align: "center",
    color: "text.secondary"
  }, isInstalling ? (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.installing') : (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.subtitle')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ProLogo, {
    src: (0,_step_visuals__WEBPACK_IMPORTED_MODULE_8__.getOnboardingAssetUrl)('install-pro-logo.png'),
    alt: (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.logo_alt')
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 2,
    width: "100%",
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.PrimaryButton, {
    variant: "contained",
    color: "primary",
    fullWidth: true,
    size: "large",
    onClick: handleInstall,
    disabled: isInstalling,
    startIcon: isInstalling ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CircularProgress, {
      size: 18,
      color: "inherit"
    }) : undefined
  }, isInstalling ? (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.installing_short') : (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.install_button')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_fullscreen_card__WEBPACK_IMPORTED_MODULE_2__.TextButton, {
    href: '#',
    align: "center",
    onClick: event => handleDismiss(event),
    disabled: isInstalling,
    sx: {
      textDecoration: 'none'
    }
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_7__.t)('pro_install.do_it_later'))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/screens/site-features.tsx":
/*!**********************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/screens/site-features.tsx ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SiteFeatures: function() { return /* binding */ SiteFeatures; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_ui_styled_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/ui/styled-components */ "./packages/apps/onboarding/src/components/ui/styled-components.ts");
/* harmony import */ var _hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../hooks/use-onboarding */ "./packages/apps/onboarding/src/hooks/use-onboarding.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _components_site_features__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../components/site-features */ "./packages/apps/onboarding/src/steps/components/site-features/index.ts");







const FEATURE_OPTION_IDS = new Set(_components_site_features__WEBPACK_IMPORTED_MODULE_5__.FEATURE_OPTIONS.map(featureOption => featureOption.id));
function SiteFeatures() {
  const {
    choices,
    actions
  } = (0,_hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_3__.useOnboarding)();
  const theme = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTheme)();
  const rawSiteFeatures = choices.site_features;
  const storedSelectableFeatures = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (rawSiteFeatures || []).filter(id => FEATURE_OPTION_IDS.has(id) && !_components_site_features__WEBPACK_IMPORTED_MODULE_5__.CORE_FEATURE_IDS.has(id)), [rawSiteFeatures]);
  const selectedValues = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const combined = [..._components_site_features__WEBPACK_IMPORTED_MODULE_5__.CORE_FEATURE_IDS, ...storedSelectableFeatures];
    return combined.filter((id, index) => combined.indexOf(id) === index);
  }, [storedSelectableFeatures]);
  function handleFeatureClick(id) {
    if (_components_site_features__WEBPACK_IMPORTED_MODULE_5__.CORE_FEATURE_IDS.has(id)) {
      return;
    }
    const isCurrentlySelected = storedSelectableFeatures.includes(id);
    const updatedSelectableFeatures = isCurrentlySelected ? storedSelectableFeatures.filter(featureId => featureId !== id) : [...storedSelectableFeatures, id];
    actions.setUserChoice('site_features', updatedSelectableFeatures);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 4,
    width: "100%",
    "data-testid": "site-features-step"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 1,
    textAlign: "center",
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_styled_components__WEBPACK_IMPORTED_MODULE_2__.StepTitle, {
    color: "text.primary",
    variant: "h5",
    align: "center",
    paddingBlockStart: theme.spacing(2.5)
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)('steps.site_features.title')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body1",
    color: "text.secondary"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_4__.t)('steps.site_features.subtitle'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_site_features__WEBPACK_IMPORTED_MODULE_5__.FeatureGrid, {
    options: _components_site_features__WEBPACK_IMPORTED_MODULE_5__.FEATURE_OPTIONS,
    selectedValues: selectedValues,
    onFeatureClick: handleFeatureClick
  }));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/screens/theme-selection.tsx":
/*!************************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/screens/theme-selection.tsx ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ThemeSelection: function() { return /* binding */ ThemeSelection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_theme_selection__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/theme-selection */ "./packages/apps/onboarding/src/components/theme-selection/index.ts");
/* harmony import */ var _components_theme_selection_hello_theme_preview__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/theme-selection/hello-theme-preview */ "./packages/apps/onboarding/src/components/theme-selection/hello-theme-preview.tsx");
/* harmony import */ var _components_ui_footer_highlights__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/ui/footer-highlights */ "./packages/apps/onboarding/src/components/ui/footer-highlights.tsx");
/* harmony import */ var _components_ui_greeting_banner__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/ui/greeting-banner */ "./packages/apps/onboarding/src/components/ui/greeting-banner.tsx");
/* harmony import */ var _components_ui_styled_components__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/ui/styled-components */ "./packages/apps/onboarding/src/components/ui/styled-components.ts");
/* harmony import */ var _hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../hooks/use-onboarding */ "./packages/apps/onboarding/src/hooks/use-onboarding.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../types */ "./packages/apps/onboarding/src/types.ts");
/* harmony import */ var _utils_get_config__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../utils/get-config */ "./packages/apps/onboarding/src/utils/get-config.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");
/* harmony import */ var _theme_selection_footer__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../theme-selection-footer */ "./packages/apps/onboarding/src/steps/theme-selection-footer.ts");













function ThemeSelection() {
  const {
    choices,
    completedSteps
  } = (0,_hooks_use_onboarding__WEBPACK_IMPORTED_MODULE_7__.useOnboarding)();
  const isStepCompleted = completedSteps.includes(_types__WEBPACK_IMPORTED_MODULE_8__.StepId.THEME_SELECTION);
  const isHelloThemeActive = (0,_utils_get_config__WEBPACK_IMPORTED_MODULE_9__.getConfig)()?.isHelloThemeActive ?? false;
  const isInstalled = isHelloThemeActive || isStepCompleted && choices.theme_selection === _components_theme_selection__WEBPACK_IMPORTED_MODULE_2__.HELLO_THEME.slug;
  const greetingText = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_components_theme_selection__WEBPACK_IMPORTED_MODULE_2__.getGreetingText)(null), []);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 7.5,
    width: "100%",
    sx: {
      flex: 1
    },
    "data-testid": "theme-selection-step"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    width: "100%",
    maxWidth: 386,
    alignSelf: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_greeting_banner__WEBPACK_IMPORTED_MODULE_5__.GreetingBanner, null, greetingText)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    useFlexGap: true,
    spacing: 4,
    alignItems: "center",
    width: "100%"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 1,
    textAlign: "center",
    alignItems: "center"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_styled_components__WEBPACK_IMPORTED_MODULE_6__.StepTitle, {
    color: "text.primary",
    variant: "h5",
    align: "center"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_10__.t)('steps.theme_selection.v2.title')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body1",
    color: "text.secondary"
  }, (0,_utils_translations__WEBPACK_IMPORTED_MODULE_10__.t)('steps.theme_selection.v2.subtitle'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    useFlexGap: true,
    alignItems: "center",
    width: "100%",
    sx: {
      gap: '60px'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_theme_selection_hello_theme_preview__WEBPACK_IMPORTED_MODULE_3__.HelloThemePreview, {
    isInstalled: isInstalled
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_ui_footer_highlights__WEBPACK_IMPORTED_MODULE_4__.FooterHighlights, {
    items: _theme_selection_footer__WEBPACK_IMPORTED_MODULE_11__.THEME_SELECTION_FOOTER_HIGHLIGHTS,
    testId: "theme-selection-highlights"
  }))));
}

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/step-visuals.ts":
/*!************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/step-visuals.ts ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LOGIN_CONFIG: function() { return /* binding */ LOGIN_CONFIG; },
/* harmony export */   getLoginVisualConfig: function() { return /* binding */ getLoginVisualConfig; },
/* harmony export */   getOnboardingAssetUrl: function() { return /* binding */ getOnboardingAssetUrl; },
/* harmony export */   getOnboardingVideoUrl: function() { return /* binding */ getOnboardingVideoUrl; },
/* harmony export */   getStepVisualConfig: function() { return /* binding */ getStepVisualConfig; },
/* harmony export */   getVideoUrls: function() { return /* binding */ getVideoUrls; }
/* harmony export */ });
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../types */ "./packages/apps/onboarding/src/types.ts");

const ONBOARDING_ASSETS_PATH = 'images/app/onboarding/';
const VIDEOS_BASE_URL = 'https://assets.elementor.com/onboarding/v1/videos/';
const CONTENT_MAX_WIDTH_WIDE_ELEMENT = 724;
const getAssetsBaseUrl = () => window.elementorCommon?.config?.urls?.assets ?? '';
const getOnboardingAssetUrl = fileName => {
  const baseUrl = getAssetsBaseUrl();
  const path = `${ONBOARDING_ASSETS_PATH}${fileName}`;
  return baseUrl ? `${baseUrl}${path}` : path;
};
const getOnboardingVideoUrl = fileName => `${VIDEOS_BASE_URL}${fileName}`;
const buildBackground = fileName => {
  const imageUrl = getOnboardingAssetUrl(fileName);
  return `url(${imageUrl}) center / cover no-repeat`;
};
const DEFAULT_CONFIG = {
  background: buildBackground('step-1.webp')
};
const LOGIN_CONFIG = {
  background: buildBackground('login.webp')
};
const stepVisuals = {
  [_types__WEBPACK_IMPORTED_MODULE_0__.StepId.THEME_SELECTION]: {
    background: '',
    video: getOnboardingVideoUrl('step-4-v2.webm'),
    contentMaxWidth: CONTENT_MAX_WIDTH_WIDE_ELEMENT
  },
  [_types__WEBPACK_IMPORTED_MODULE_0__.StepId.SITE_FEATURES]: {
    background: '',
    video: getOnboardingVideoUrl('step-4-v2.webm'),
    contentMaxWidth: CONTENT_MAX_WIDTH_WIDE_ELEMENT
  }
};
const getLoginVisualConfig = () => LOGIN_CONFIG;
const getStepVisualConfig = stepId => stepVisuals[stepId] ?? DEFAULT_CONFIG;

// Deduplicated so shared videos (e.g. steps 4 and 5 use the same clip) yield a
// single <video> element rather than two with a colliding React key.
const getVideoUrls = () => {
  const urls = Object.values(stepVisuals).flatMap(config => config.video ? [config.video] : []);
  return [...new Set(urls)];
};

/***/ }),

/***/ "./packages/apps/onboarding/src/steps/theme-selection-footer.ts":
/*!**********************************************************************!*\
  !*** ./packages/apps/onboarding/src/steps/theme-selection-footer.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   THEME_SELECTION_FOOTER_HIGHLIGHTS: function() { return /* binding */ THEME_SELECTION_FOOTER_HIGHLIGHTS; }
/* harmony export */ });
const THEME_SELECTION_FOOTER_HIGHLIGHTS = [{
  labelKey: 'steps.theme_selection.highlight_fast'
}, {
  labelKey: 'steps.theme_selection.highlight_responsive'
}, {
  labelKey: 'steps.theme_selection.highlight_built_for_elementor'
}];

/***/ }),

/***/ "./packages/apps/onboarding/src/store/index.ts":
/*!*****************************************************!*\
  !*** ./packages/apps/onboarding/src/store/index.ts ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearResumeStepIdForTracking: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.clearResumeStepIdForTracking; },
/* harmony export */   clearUnexpectedExit: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.clearUnexpectedExit; },
/* harmony export */   completeOnboarding: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.completeOnboarding; },
/* harmony export */   completeStep: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.completeStep; },
/* harmony export */   dismissProInstallScreen: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.dismissProInstallScreen; },
/* harmony export */   goToStep: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.goToStep; },
/* harmony export */   goToStepIndex: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.goToStepIndex; },
/* harmony export */   initFromConfig: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.initFromConfig; },
/* harmony export */   markProInstalled: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.markProInstalled; },
/* harmony export */   nextStep: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.nextStep; },
/* harmony export */   prevStep: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.prevStep; },
/* harmony export */   registerOnboardingSlice: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.registerOnboardingSlice; },
/* harmony export */   selectChoices: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectChoices; },
/* harmony export */   selectCompletedSteps: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectCompletedSteps; },
/* harmony export */   selectCurrentStep: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectCurrentStep; },
/* harmony export */   selectCurrentStepId: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectCurrentStepId; },
/* harmony export */   selectCurrentStepIndex: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectCurrentStepIndex; },
/* harmony export */   selectError: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectError; },
/* harmony export */   selectHadUnexpectedExit: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectHadUnexpectedExit; },
/* harmony export */   selectHasPassedLogin: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectHasPassedLogin; },
/* harmony export */   selectHasProInstallScreenDismissed: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectHasProInstallScreenDismissed; },
/* harmony export */   selectIsConnected: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectIsConnected; },
/* harmony export */   selectIsFirstStep: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectIsFirstStep; },
/* harmony export */   selectIsGuest: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectIsGuest; },
/* harmony export */   selectIsLastStep: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectIsLastStep; },
/* harmony export */   selectIsLoading: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectIsLoading; },
/* harmony export */   selectIsStepCompleted: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectIsStepCompleted; },
/* harmony export */   selectOnboarding: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectOnboarding; },
/* harmony export */   selectResumeStepIdForTracking: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectResumeStepIdForTracking; },
/* harmony export */   selectShouldShowProInstall: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectShouldShowProInstall; },
/* harmony export */   selectSteps: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectSteps; },
/* harmony export */   selectTotalSteps: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectTotalSteps; },
/* harmony export */   selectUrls: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectUrls; },
/* harmony export */   selectUserName: function() { return /* reexport safe */ _selectors__WEBPACK_IMPORTED_MODULE_1__.selectUserName; },
/* harmony export */   setConnected: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setConnected; },
/* harmony export */   setError: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setError; },
/* harmony export */   setExitType: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setExitType; },
/* harmony export */   setGuest: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setGuest; },
/* harmony export */   setLoading: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setLoading; },
/* harmony export */   setShouldShowProInstallScreen: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setShouldShowProInstallScreen; },
/* harmony export */   setUserChoice: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setUserChoice; },
/* harmony export */   setUserChoices: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.setUserChoices; },
/* harmony export */   slice: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.slice; },
/* harmony export */   startOnboarding: function() { return /* reexport safe */ _slice__WEBPACK_IMPORTED_MODULE_0__.startOnboarding; }
/* harmony export */ });
/* harmony import */ var _slice__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./slice */ "./packages/apps/onboarding/src/store/slice.ts");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./selectors */ "./packages/apps/onboarding/src/store/selectors.ts");



/***/ }),

/***/ "./packages/apps/onboarding/src/store/selectors.ts":
/*!*********************************************************!*\
  !*** ./packages/apps/onboarding/src/store/selectors.ts ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   selectChoices: function() { return /* binding */ selectChoices; },
/* harmony export */   selectCompletedSteps: function() { return /* binding */ selectCompletedSteps; },
/* harmony export */   selectCurrentStep: function() { return /* binding */ selectCurrentStep; },
/* harmony export */   selectCurrentStepId: function() { return /* binding */ selectCurrentStepId; },
/* harmony export */   selectCurrentStepIndex: function() { return /* binding */ selectCurrentStepIndex; },
/* harmony export */   selectError: function() { return /* binding */ selectError; },
/* harmony export */   selectHadUnexpectedExit: function() { return /* binding */ selectHadUnexpectedExit; },
/* harmony export */   selectHasPassedLogin: function() { return /* binding */ selectHasPassedLogin; },
/* harmony export */   selectHasProInstallScreenDismissed: function() { return /* binding */ selectHasProInstallScreenDismissed; },
/* harmony export */   selectIsConnected: function() { return /* binding */ selectIsConnected; },
/* harmony export */   selectIsFirstStep: function() { return /* binding */ selectIsFirstStep; },
/* harmony export */   selectIsGuest: function() { return /* binding */ selectIsGuest; },
/* harmony export */   selectIsLastStep: function() { return /* binding */ selectIsLastStep; },
/* harmony export */   selectIsLoading: function() { return /* binding */ selectIsLoading; },
/* harmony export */   selectIsStepCompleted: function() { return /* binding */ selectIsStepCompleted; },
/* harmony export */   selectOnboarding: function() { return /* binding */ selectOnboarding; },
/* harmony export */   selectResumeStepIdForTracking: function() { return /* binding */ selectResumeStepIdForTracking; },
/* harmony export */   selectShouldShowProInstall: function() { return /* binding */ selectShouldShowProInstall; },
/* harmony export */   selectSteps: function() { return /* binding */ selectSteps; },
/* harmony export */   selectTotalSteps: function() { return /* binding */ selectTotalSteps; },
/* harmony export */   selectUrls: function() { return /* binding */ selectUrls; },
/* harmony export */   selectUserName: function() { return /* binding */ selectUserName; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);

const selectOnboarding = state => state.onboarding;
const selectSteps = state => state.onboarding.steps;
const selectCurrentStepId = state => state.onboarding.currentStepId;
const selectCurrentStepIndex = state => state.onboarding.currentStepIndex;
const selectCompletedSteps = state => state.onboarding.completedSteps;
const selectChoices = state => state.onboarding.choices;
const selectIsLoading = state => state.onboarding.isLoading;
const selectError = state => state.onboarding.error;
const selectHadUnexpectedExit = state => state.onboarding.hadUnexpectedExit;
const selectResumeStepIdForTracking = state => state.onboarding.resumeStepIdForTracking;
const selectIsConnected = state => state.onboarding.isConnected;
const selectIsGuest = state => state.onboarding.isGuest;
const selectUserName = state => state.onboarding.userName;
const selectUrls = state => state.onboarding.urls;
const selectShouldShowProInstallScreen = state => state.onboarding.shouldShowProInstallScreen;
const selectHasProInstallScreenDismissed = state => state.onboarding.hasProInstallScreenDismissed;
const selectCurrentStep = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectSteps, selectCurrentStepIndex], (steps, index) => steps[index] ?? null);
const selectIsFirstStep = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectCurrentStepIndex], index => index === 0);
const selectIsLastStep = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectSteps, selectCurrentStepIndex], (steps, index) => index === steps.length - 1);
const selectTotalSteps = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectSteps], steps => steps.length);
const selectIsStepCompleted = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectCompletedSteps, (_state, stepId) => stepId], (completedSteps, stepId) => completedSteps.includes(stepId));
const selectHasPassedLogin = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectIsConnected, selectIsGuest], (isConnected, isGuest) => isConnected || isGuest);
const selectShouldShowProInstall = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)([selectIsConnected, selectShouldShowProInstallScreen, selectHasProInstallScreenDismissed], (isConnected, shouldShowProInstallScreen, isDismissed) => isConnected && shouldShowProInstallScreen && !isDismissed);

/***/ }),

/***/ "./packages/apps/onboarding/src/store/slice.ts":
/*!*****************************************************!*\
  !*** ./packages/apps/onboarding/src/store/slice.ts ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearResumeStepIdForTracking: function() { return /* binding */ clearResumeStepIdForTracking; },
/* harmony export */   clearUnexpectedExit: function() { return /* binding */ clearUnexpectedExit; },
/* harmony export */   completeOnboarding: function() { return /* binding */ completeOnboarding; },
/* harmony export */   completeStep: function() { return /* binding */ completeStep; },
/* harmony export */   dismissProInstallScreen: function() { return /* binding */ dismissProInstallScreen; },
/* harmony export */   goToStep: function() { return /* binding */ goToStep; },
/* harmony export */   goToStepIndex: function() { return /* binding */ goToStepIndex; },
/* harmony export */   initFromConfig: function() { return /* binding */ initFromConfig; },
/* harmony export */   markProInstalled: function() { return /* binding */ markProInstalled; },
/* harmony export */   nextStep: function() { return /* binding */ nextStep; },
/* harmony export */   prevStep: function() { return /* binding */ prevStep; },
/* harmony export */   registerOnboardingSlice: function() { return /* binding */ registerOnboardingSlice; },
/* harmony export */   setConnected: function() { return /* binding */ setConnected; },
/* harmony export */   setError: function() { return /* binding */ setError; },
/* harmony export */   setExitType: function() { return /* binding */ setExitType; },
/* harmony export */   setGuest: function() { return /* binding */ setGuest; },
/* harmony export */   setLoading: function() { return /* binding */ setLoading; },
/* harmony export */   setShouldShowProInstallScreen: function() { return /* binding */ setShouldShowProInstallScreen; },
/* harmony export */   setUserChoice: function() { return /* binding */ setUserChoice; },
/* harmony export */   setUserChoices: function() { return /* binding */ setUserChoices; },
/* harmony export */   slice: function() { return /* binding */ slice; },
/* harmony export */   startOnboarding: function() { return /* binding */ startOnboarding; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../types */ "./packages/apps/onboarding/src/types.ts");
/* harmony import */ var _utils_translations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/translations */ "./packages/apps/onboarding/src/utils/translations.ts");



function createThemeSelectionStep() {
  return {
    id: _types__WEBPACK_IMPORTED_MODULE_1__.StepId.THEME_SELECTION,
    label: (0,_utils_translations__WEBPACK_IMPORTED_MODULE_2__.t)('steps.theme_selection.title'),
    type: 'single'
  };
}
function getDefaultSteps() {
  return [{
    id: _types__WEBPACK_IMPORTED_MODULE_1__.StepId.SITE_FEATURES,
    label: (0,_utils_translations__WEBPACK_IMPORTED_MODULE_2__.t)('steps.site_features.title'),
    type: 'multiple'
  }];
}
function applyProInstalledSteps(steps) {
  const withoutSiteFeatures = steps.filter(step => step.id !== _types__WEBPACK_IMPORTED_MODULE_1__.StepId.SITE_FEATURES);
  if (withoutSiteFeatures.some(step => step.id === _types__WEBPACK_IMPORTED_MODULE_1__.StepId.THEME_SELECTION)) {
    return withoutSiteFeatures;
  }
  return [...withoutSiteFeatures, createThemeSelectionStep()];
}
function parseStepsFromConfig(configSteps) {
  if (!configSteps || configSteps.length === 0) {
    return getDefaultSteps();
  }
  return configSteps.map(step => ({
    id: step.id,
    label: step.label,
    type: step.type || 'single'
  }));
}
function parseCompletedSteps(completedSteps) {
  if (!completedSteps) {
    return [];
  }
  return completedSteps;
}
function getDefaultChoices() {
  return {
    building_for: null,
    site_about: [],
    experience_level: null,
    theme_selection: null,
    site_features: []
  };
}
function getEmptyState() {
  const steps = getDefaultSteps();
  return {
    steps,
    currentStepId: steps[0]?.id ?? _types__WEBPACK_IMPORTED_MODULE_1__.StepId.SITE_FEATURES,
    currentStepIndex: 0,
    completedSteps: [],
    exitType: null,
    lastActiveTimestamp: null,
    startedAt: null,
    choices: getDefaultChoices(),
    isLoading: false,
    error: null,
    hadUnexpectedExit: false,
    resumeStepIdForTracking: null,
    isConnected: false,
    isGuest: false,
    userName: '',
    urls: {
      dashboard: '',
      editor: '',
      connect: '',
      signUp: '',
      comparePlans: '',
      upgradeUrl: ''
    },
    shouldShowProInstallScreen: false,
    hasProInstallScreenDismissed: false
  };
}
function buildStateFromConfig(config) {
  if (!config) {
    return getEmptyState();
  }
  const steps = parseStepsFromConfig(config.steps);
  const firstStepId = steps[0]?.id ?? _types__WEBPACK_IMPORTED_MODULE_1__.StepId.SITE_FEATURES;
  const progress = config.progress ?? {};
  let currentStepIndex = progress.current_step_index ?? 0;
  const progressStepId = progress.current_step_id;
  const isInvalidStepIndex = currentStepIndex < 0 || currentStepIndex >= steps.length;
  const isInvalidStepId = Boolean(progressStepId) && !steps.some(step => step.id === progressStepId);
  if (isInvalidStepIndex || isInvalidStepId) {
    currentStepIndex = 0;
  }
  const currentStepId = steps[currentStepIndex]?.id ?? firstStepId;
  return {
    steps,
    currentStepId,
    currentStepIndex,
    completedSteps: parseCompletedSteps(progress.completed_steps),
    exitType: progress.exit_type ?? null,
    lastActiveTimestamp: progress.last_active_timestamp ?? null,
    startedAt: progress.started_at ?? null,
    choices: {
      ...getDefaultChoices(),
      ...config.choices
    },
    isLoading: false,
    error: null,
    hadUnexpectedExit: false,
    resumeStepIdForTracking: config.hadUnexpectedExit ? currentStepId : null,
    isConnected: config.isConnected ?? false,
    isGuest: false,
    userName: config.userName ?? '',
    urls: config.urls ?? {
      dashboard: '',
      editor: '',
      connect: '',
      signUp: '',
      comparePlans: '',
      upgradeUrl: ''
    },
    shouldShowProInstallScreen: config.shouldShowProInstallScreen ?? false,
    hasProInstallScreenDismissed: false
  };
}
const slice = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSlice)({
  name: 'onboarding',
  initialState: getEmptyState(),
  reducers: {
    initFromConfig: state => {
      const config = window.elementorAppConfig?.onboarding;
      if (config) {
        return buildStateFromConfig(config);
      }
      return state;
    },
    goToStep: (state, action) => {
      const stepId = action.payload;
      const stepIndex = state.steps.findIndex(s => s.id === stepId);
      if (stepIndex !== -1) {
        state.currentStepId = stepId;
        state.currentStepIndex = stepIndex;
      }
    },
    goToStepIndex: (state, action) => {
      const index = action.payload;
      if (index >= 0 && index < state.steps.length) {
        state.currentStepId = state.steps[index].id;
        state.currentStepIndex = index;
      }
    },
    nextStep: state => {
      const nextIndex = state.currentStepIndex + 1;
      if (nextIndex < state.steps.length) {
        state.currentStepId = state.steps[nextIndex].id;
        state.currentStepIndex = nextIndex;
      }
    },
    prevStep: state => {
      const prevIndex = state.currentStepIndex - 1;
      if (prevIndex >= 0) {
        state.currentStepId = state.steps[prevIndex].id;
        state.currentStepIndex = prevIndex;
      }
    },
    completeStep: (state, action) => {
      const stepId = action.payload;
      if (!state.completedSteps.includes(stepId)) {
        state.completedSteps.push(stepId);
      }
    },
    setUserChoice: (state, action) => {
      const {
        key,
        value
      } = action.payload;
      state.choices[key] = value;
    },
    setUserChoices: (state, action) => {
      state.choices = {
        ...state.choices,
        ...action.payload
      };
    },
    setExitType: (state, action) => {
      state.exitType = action.payload;
    },
    startOnboarding: state => {
      state.startedAt = Date.now();
      state.exitType = null;
      state.hadUnexpectedExit = false;
    },
    completeOnboarding: state => {
      state.exitType = 'user_exit';
    },
    setLoading: (state, action) => {
      state.isLoading = action.payload;
    },
    setError: (state, action) => {
      state.error = action.payload;
    },
    clearUnexpectedExit: state => {
      state.hadUnexpectedExit = false;
    },
    clearResumeStepIdForTracking: state => {
      state.resumeStepIdForTracking = null;
    },
    setConnected: (state, action) => {
      state.isConnected = action.payload;
    },
    setGuest: (state, action) => {
      state.isGuest = action.payload;
    },
    setShouldShowProInstallScreen: (state, action) => {
      state.shouldShowProInstallScreen = action.payload;
    },
    dismissProInstallScreen: state => {
      state.hasProInstallScreenDismissed = true;
    },
    markProInstalled: state => {
      state.hasProInstallScreenDismissed = true;
      state.steps = applyProInstalledSteps(state.steps);
      state.currentStepIndex = 0;
      state.currentStepId = state.steps[0]?.id ?? _types__WEBPACK_IMPORTED_MODULE_1__.StepId.THEME_SELECTION;
    }
  }
});
const {
  initFromConfig,
  goToStep,
  goToStepIndex,
  nextStep,
  prevStep,
  completeStep,
  setUserChoice,
  setUserChoices,
  setExitType,
  startOnboarding,
  completeOnboarding,
  setLoading,
  setError,
  clearUnexpectedExit,
  clearResumeStepIdForTracking,
  setConnected,
  setGuest,
  setShouldShowProInstallScreen,
  dismissProInstallScreen,
  markProInstalled
} = slice.actions;
function registerOnboardingSlice() {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__registerSlice)(slice);
}

/***/ }),

/***/ "./packages/apps/onboarding/src/types.ts":
/*!***********************************************!*\
  !*** ./packages/apps/onboarding/src/types.ts ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StepId: function() { return /* binding */ StepId; }
/* harmony export */ });
const StepId = {
  THEME_SELECTION: 'theme_selection',
  SITE_FEATURES: 'site_features'
};

/***/ }),

/***/ "./packages/apps/onboarding/src/utils/default-strings.ts":
/*!***************************************************************!*\
  !*** ./packages/apps/onboarding/src/utils/default-strings.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_STRINGS: function() { return /* binding */ DEFAULT_STRINGS; }
/* harmony export */ });
const DEFAULT_STRINGS = {
  'common.continue': 'Continue',
  'common.skip': 'Skip',
  'common.back': 'Back',
  'common.finish': 'Finish',
  'common.loading': 'Loading\u2026',
  'common.upgrade': 'Upgrade',
  'common.close_onboarding': 'Close onboarding',
  'common.installed': 'Installed',
  'common.recommended': 'Recommended',
  'error.failed_mark_exit': 'Failed to mark user exit.',
  'error.failed_complete_step': 'Failed to complete step.',
  'error.pro_install_failed': "Couldn't install Elementor Pro. Please try again later",
  'error.theme_install_failed': "Theme installation didn't complete. Please try again later",
  'login.title': "Let's get to work.",
  'login.sign_in': 'Sign in to Elementor',
  'login.continue_another_way': 'Continue another way',
  'login.continue_as_guest': 'Continue as a guest',
  'steps.building_for.title': 'Who are you building for?',
  'steps.building_for.greeting_with_name': "Hey %1$s %2$s Let's get your site set up.",
  'steps.building_for.greeting_without_name': "Hey%s Let's get your site set up.",
  'steps.building_for.option_myself': 'Myself or someone I know',
  'steps.building_for.option_business': 'My business or workplace',
  'steps.building_for.option_client': 'A client',
  'steps.building_for.option_exploring': 'Just exploring',
  'steps.site_about.title': 'What is your site about?',
  'steps.site_about.subtitle': 'Choose anything that applies.',
  'steps.site_about.option_small_med_business': 'Small-Med Business',
  'steps.site_about.option_online_store': 'Online store',
  'steps.site_about.option_company_site': 'Company site',
  'steps.site_about.option_blog': 'Blog',
  'steps.site_about.option_landing_page': 'Landing page',
  'steps.site_about.option_booking': 'Booking',
  'steps.site_about.option_organization': 'Organization',
  'steps.site_about.option_other': 'Other',
  'steps.site_about.greeting_myself': "Got it! We'll keep things simple.",
  'steps.site_about.greeting_business': "Great! Let's set up your business site.",
  'steps.site_about.greeting_client': "Nice! Let's create something for your client.",
  'steps.site_about.greeting_fallback': "Let's get started!",
  'steps.experience_level.title': 'How experienced are you with Elementor?',
  'steps.experience_level.subtitle': 'This helps us adjust the editor to your workflow.',
  'steps.experience_level.option_beginner': "I'm just getting started",
  'steps.experience_level.option_intermediate': 'I have some experience',
  'steps.experience_level.option_advanced': "I'm very comfortable with Elementor",
  'steps.theme_selection.title': 'Start with a theme that fits your needs',
  'steps.theme_selection.subtitle': 'Hello themes are built to work seamlessly with Elementor.',
  'steps.theme_selection.v2.title': 'Build faster with Hello Theme',
  'steps.theme_selection.v2.subtitle': "Use Elementor's lightweight theme for faster setup and full design control.",
  'steps.theme_selection.aria_label': 'Theme selection',
  'steps.theme_selection.theme_hello_label': 'Hello',
  'steps.theme_selection.theme_hello_description': 'A flexible canvas theme you can shape from the ground up',
  'steps.theme_selection.by_elementor': 'by Elementor',
  'steps.theme_selection.greeting_beginner': "Glad you're here!",
  'steps.theme_selection.greeting_default': "Great. Let's take it to the next step",
  'steps.theme_selection.continue_with_theme': 'Continue with this theme',
  'steps.theme_selection.v2.continue_with_theme': 'Continue with Hello',
  'steps.theme_selection.highlight_fast': 'Fast by design',
  'steps.theme_selection.highlight_responsive': 'Responsive from the start',
  'steps.theme_selection.highlight_built_for_elementor': 'Built for Elementor',
  'steps.site_features.title': 'What do you want to include in your site?',
  'steps.site_features.subtitle': "We'll use this to tailor suggestions for you.",
  'steps.site_features.continue_with_free': 'Continue with Free',
  'steps.site_features.option_classes_variables': 'Design system',
  'steps.site_features.option_interactions': 'Interactions',
  'steps.site_features.option_cookie_consent': 'Cookie Consent',
  'steps.site_features.option_theme_builder': 'Theme builder',
  'steps.site_features.option_lead_collection': 'Forms & leads',
  'steps.site_features.option_custom_code': 'Custom Code',
  'steps.site_features.option_email_deliverability': 'Email deliverability',
  'steps.site_features.option_ai_generator': 'AI tools',
  'steps.site_features.option_image_optimization': 'Image optimization',
  'steps.site_features.option_accessibility_tools': 'Accessibility',
  'steps.site_features.included': 'Included',
  'steps.site_features.plan_recommendation_one': 'Nice picks 🙂 Elementor One has you covered.',
  'steps.site_features.plan_recommendation_pro': 'Advanced tools? Elementor Pro includes them.',
  'steps.site_features.compare_plans': 'View plans',
  'pro_install.title': 'You already have a Pro subscription',
  'pro_install.subtitle': 'Would you like to install it on this site now?',
  'pro_install.installing': 'Installing Elementor Pro\u2026',
  'pro_install.installing_short': 'Installing\u2026',
  'pro_install.install_button': 'Install Pro on this site',
  'pro_install.logo_alt': 'Elementor + Elementor Pro',
  'pro_install.do_it_later': "I'll do it later",
  'completion.title': 'Getting things ready',
  'completion.subtitle': 'Tailoring the editor to your goals and workflow\u2026'
};

/***/ }),

/***/ "./packages/apps/onboarding/src/utils/get-config.ts":
/*!**********************************************************!*\
  !*** ./packages/apps/onboarding/src/utils/get-config.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getConfig: function() { return /* binding */ getConfig; }
/* harmony export */ });
function getConfig() {
  return window.elementorAppConfig?.onboarding ?? null;
}

/***/ }),

/***/ "./packages/apps/onboarding/src/utils/retry.ts":
/*!*****************************************************!*\
  !*** ./packages/apps/onboarding/src/utils/retry.ts ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   withRetry: function() { return /* binding */ withRetry; }
/* harmony export */ });
async function withRetry(fn, maxRetries = 1, delayMs = 1000) {
  let lastError = new Error('withRetry: all attempts failed');
  for (let attempt = 0; attempt <= maxRetries; attempt++) {
    try {
      return await fn();
    } catch (error) {
      lastError = error instanceof Error ? error : new Error(String(error));
      if (attempt < maxRetries) {
        await new Promise(r => setTimeout(r, delayMs));
      }
    }
  }
  throw lastError;
}

/***/ }),

/***/ "./packages/apps/onboarding/src/utils/translations.ts":
/*!************************************************************!*\
  !*** ./packages/apps/onboarding/src/utils/translations.ts ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   t: function() { return /* binding */ t; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _default_strings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./default-strings */ "./packages/apps/onboarding/src/utils/default-strings.ts");


const t = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createTranslate)({
  configKey: 'onboarding',
  defaultStrings: _default_strings__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_STRINGS
});

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

/***/ "@elementor/query":
/*!****************************************!*\
  !*** external ["elementorV2","query"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["query"];

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
/*!***********************************************!*\
  !*** ./packages/apps/onboarding/src/index.ts ***!
  \***********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* reexport safe */ _components__WEBPACK_IMPORTED_MODULE_0__.App; }
/* harmony export */ });
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ "./packages/apps/onboarding/src/components/index.ts");

}();
(window.elementorV2 = window.elementorV2 || {}).onboarding = __webpack_exports__;
/******/ })()
;
window.elementorV2.onboarding?.init?.();
//# sourceMappingURL=onboarding.js.map
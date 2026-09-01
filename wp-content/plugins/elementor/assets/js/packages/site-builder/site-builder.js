/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/apps/site-builder/src/components/app.tsx":
/*!***********************************************************!*\
  !*** ./packages/apps/site-builder/src/components/app.tsx ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* binding */ App; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _connect_auth_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../connect-auth-schema */ "./packages/apps/site-builder/src/connect-auth-schema.ts");
/* harmony import */ var _hooks_use_site_builder_iframe_messaging__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../hooks/use-site-builder-iframe-messaging */ "./packages/apps/site-builder/src/hooks/use-site-builder-iframe-messaging.ts");
/* harmony import */ var _site_builder_config__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../site-builder-config */ "./packages/apps/site-builder/src/site-builder-config.ts");






const iframeStyle = {
  position: 'fixed',
  top: 0,
  left: 0,
  width: '100vw',
  height: '100vh',
  border: 'none',
  zIndex: 10000
};
function App() {
  const iframeRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const [siteBuilderParams, setSiteBuilderParams] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const [connectAuth, setConnectAuth] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const iframeUrl = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => (0,_site_builder_config__WEBPACK_IMPORTED_MODULE_4__.getSiteBuilderConfig)()?.iframeUrl ?? '', []);
  (0,_hooks_use_site_builder_iframe_messaging__WEBPACK_IMPORTED_MODULE_3__.useSiteBuilderIframeMessaging)({
    iframeRef,
    iframeUrl,
    siteBuilderParams,
    connectAuth
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const fetchConnectAuth = async () => {
      try {
        const json = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
          path: '/elementor/v1/site-builder/auth'
        });
        if (json.success && json.data && (0,_connect_auth_schema__WEBPACK_IMPORTED_MODULE_2__.isValidConnectAuth)(json.data)) {
          setConnectAuth(json.data);
        } else {
          throw new Error('Invalid auth response: missing required Connect fields');
        }
      } catch (err) {
        // eslint-disable-next-line no-console
        console.error('Failed to fetch connectAuth:', err);
      }
    };
    fetchConnectAuth();
  }, []);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!window.opener) {
      return;
    }
    const onInit = event => {
      if (event.source !== window.opener) {
        return;
      }
      if (event.origin !== window.location.origin) {
        return;
      }
      if (event.data?.type !== 'site-builder/init') {
        return;
      }
      setSiteBuilderParams(event.data.payload ?? {});
      window.removeEventListener('message', onInit);
    };
    window.addEventListener('message', onInit);
    window.opener.postMessage({
      type: 'site-builder/ready'
    }, window.location.origin);
    return () => window.removeEventListener('message', onInit);
  }, []);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const wpApiSettings = window.wpApiSettings;
    const nonce = wpApiSettings?.nonce || '';
    if (!nonce) {
      return;
    }
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
      path: '/elementor/v1/site-builder/snapshot',
      method: 'POST',
      data: {
        value: {}
      }
    }).catch(() => {});
  }, []);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("iframe", {
    ref: iframeRef,
    src: iframeUrl,
    style: iframeStyle,
    title: "Website Planner",
    allow: "clipboard-read; clipboard-write"
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/components/index.ts":
/*!************************************************************!*\
  !*** ./packages/apps/site-builder/src/components/index.ts ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* reexport safe */ _app__WEBPACK_IMPORTED_MODULE_0__.App; }
/* harmony export */ });
/* harmony import */ var _app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./app */ "./packages/apps/site-builder/src/components/app.tsx");


/***/ }),

/***/ "./packages/apps/site-builder/src/connect-auth-schema.ts":
/*!***************************************************************!*\
  !*** ./packages/apps/site-builder/src/connect-auth-schema.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   connectAuthSchema: function() { return /* binding */ connectAuthSchema; },
/* harmony export */   isValidConnectAuth: function() { return /* binding */ isValidConnectAuth; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);

const connectAuthSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  signature: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().min(1),
  accessToken: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().min(1),
  clientId: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().min(1),
  homeUrl: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().min(1),
  siteKey: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().min(1)
});
function isValidConnectAuth(data) {
  return connectAuthSchema.safeParse(data).success;
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/can-redirect-after-deploy.ts":
/*!****************************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/can-redirect-after-deploy.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canRedirectToEditorAfterDeploy: function() { return /* binding */ canRedirectToEditorAfterDeploy; },
/* harmony export */   resolveEditorRedirectPageId: function() { return /* binding */ resolveEditorRedirectPageId; }
/* harmony export */ });
const blockingErrorPrefixes = ['pages:', 'home_page:'];
const hasBlockingDeployErrors = errors => errors?.some(err => blockingErrorPrefixes.some(prefix => String(err).startsWith(prefix))) ?? false;
function resolveEditorRedirectPageId(args) {
  if (hasBlockingDeployErrors(args.errors)) {
    return null;
  }
  if (args.isIncremental) {
    const lastPlannerPage = args.pages?.[args.pages.length - 1];
    if (!lastPlannerPage || !args.pageIdMap) {
      return null;
    }
    return args.pageIdMap[lastPlannerPage.id] ?? null;
  }
  return args.homePageId ?? null;
}
function canRedirectToEditorAfterDeploy(args) {
  return resolveEditorRedirectPageId(args) !== null;
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/deploy-editor-redirect.ts":
/*!*************************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/deploy-editor-redirect.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EDITOR_REDIRECT_ACKNOWLEDGE_FALLBACK_MS: function() { return /* binding */ EDITOR_REDIRECT_ACKNOWLEDGE_FALLBACK_MS; },
/* harmony export */   clearPendingEditorRedirect: function() { return /* binding */ clearPendingEditorRedirect; },
/* harmony export */   completeEditorRedirectOnDeployAcknowledge: function() { return /* binding */ completeEditorRedirectOnDeployAcknowledge; },
/* harmony export */   scheduleEditorRedirectAfterDeploy: function() { return /* binding */ scheduleEditorRedirectAfterDeploy; }
/* harmony export */ });
const EDITOR_REDIRECT_ACKNOWLEDGE_FALLBACK_MS = 3000;
function scheduleEditorRedirectAfterDeploy(redirectUrl, options = {}) {
  const fallbackMs = options.fallbackMs ?? EDITOR_REDIRECT_ACKNOWLEDGE_FALLBACK_MS;
  const navigate = options.navigate ?? (url => {
    window.location.href = url;
  });
  const pending = {
    redirectUrl,
    fallbackTimerId: null
  };
  pending.fallbackTimerId = setTimeout(() => {
    if (pending.redirectUrl !== redirectUrl) {
      return;
    }
    pending.redirectUrl = null;
    pending.fallbackTimerId = null;
    navigate(redirectUrl);
  }, fallbackMs);
  return pending;
}
function clearPendingEditorRedirect(pending) {
  if (!pending) {
    return;
  }
  if (pending.fallbackTimerId) {
    clearTimeout(pending.fallbackTimerId);
    pending.fallbackTimerId = null;
  }
  pending.redirectUrl = null;
}
function completeEditorRedirectOnDeployAcknowledge(pending, navigate = url => {
  window.location.href = url;
}) {
  const redirectUrl = pending?.redirectUrl;
  clearPendingEditorRedirect(pending);
  if (redirectUrl) {
    navigate(redirectUrl);
  }
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/index.ts":
/*!********************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/index.ts ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deployWebsite: function() { return /* binding */ deployWebsite; }
/* harmony export */ });
/* harmony import */ var _steps_global_classes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./steps/global-classes */ "./packages/apps/site-builder/src/deploy/steps/global-classes.ts");
/* harmony import */ var _steps_global_variables__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./steps/global-variables */ "./packages/apps/site-builder/src/deploy/steps/global-variables.ts");
/* harmony import */ var _steps_kit_settings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./steps/kit-settings */ "./packages/apps/site-builder/src/deploy/steps/kit-settings.ts");
/* harmony import */ var _steps_logo__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./steps/logo */ "./packages/apps/site-builder/src/deploy/steps/logo.ts");
/* harmony import */ var _steps_menus__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./steps/menus */ "./packages/apps/site-builder/src/deploy/steps/menus.ts");
/* harmony import */ var _steps_pages__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./steps/pages */ "./packages/apps/site-builder/src/deploy/steps/pages.ts");
/* harmony import */ var _steps_sample_posts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./steps/sample-posts */ "./packages/apps/site-builder/src/deploy/steps/sample-posts.ts");
/* harmony import */ var _steps_site_metadata__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./steps/site-metadata */ "./packages/apps/site-builder/src/deploy/steps/site-metadata.ts");
/* harmony import */ var _steps_theme_parts__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./steps/theme-parts */ "./packages/apps/site-builder/src/deploy/steps/theme-parts.ts");
/* harmony import */ var _steps_wire_menu_widgets__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./steps/wire-menu-widgets */ "./packages/apps/site-builder/src/deploy/steps/wire-menu-widgets.ts");










async function deployWebsite(payload) {
  const errors = [];
  const mode = payload.mode === 'incremental' ? 'incremental' : 'full';
  if (mode === 'incremental') {
    let pageIdMap = {};
    try {
      ({
        pageIdMap
      } = await (0,_steps_pages__WEBPACK_IMPORTED_MODULE_5__.createPages)(payload.pages));
    } catch (e) {
      errors.push(`pages: ${e.message}`);
    }
    return {
      status: errors.length ? 'error' : 'success',
      homeUrl: window.location.origin,
      pageIdMap,
      ...(errors.length ? {
        errors,
        error: errors[0]
      } : {})
    };
  }
  if (payload.siteMeta) {
    try {
      await (0,_steps_site_metadata__WEBPACK_IMPORTED_MODULE_7__.setSiteMetadata)(payload.siteMeta);
    } catch (e) {
      errors.push(`site_metadata: ${e.message}`);
    }
  }
  if (payload.logo) {
    try {
      await (0,_steps_logo__WEBPACK_IMPORTED_MODULE_3__.uploadLogo)(payload.logo);
    } catch (e) {
      errors.push(`logo: ${e.message}`);
    }
  }
  if (payload.kitSettings) {
    try {
      await (0,_steps_kit_settings__WEBPACK_IMPORTED_MODULE_2__.updateKitSettings)(payload.kitSettings);
    } catch (e) {
      errors.push(`kit_settings: ${e.message}`);
    }
  }
  if (payload.globalVariables) {
    try {
      await (0,_steps_global_variables__WEBPACK_IMPORTED_MODULE_1__.deployGlobalVariables)(payload.globalVariables);
    } catch (e) {
      errors.push(`global_variables: ${e.message}`);
    }
  }
  if (payload.globalClasses) {
    try {
      await (0,_steps_global_classes__WEBPACK_IMPORTED_MODULE_0__.deployGlobalClasses)(payload.globalClasses);
    } catch (e) {
      errors.push(`global_classes: ${e.message}`);
    }
  }
  let pageIdMap = {};
  let pageUrlMap = {};
  try {
    ({
      pageIdMap,
      pageUrlMap
    } = await (0,_steps_pages__WEBPACK_IMPORTED_MODULE_5__.createPages)(payload.pages));
  } catch (e) {
    errors.push(`pages: ${e.message}`);
  }
  const homeWpId = resolveHomePageId(payload.pages, pageIdMap);
  if (homeWpId) {
    try {
      await (0,_steps_pages__WEBPACK_IMPORTED_MODULE_5__.setHomePage)(homeWpId);
    } catch (e) {
      errors.push(`home_page: ${e.message}`);
    }
  }
  let createdMenus = {};
  try {
    createdMenus = await (0,_steps_menus__WEBPACK_IMPORTED_MODULE_4__.createMenus)(payload.menus, pageIdMap);
  } catch (e) {
    errors.push(`menus: ${e.message}`);
  }
  if (payload.header) {
    (0,_steps_wire_menu_widgets__WEBPACK_IMPORTED_MODULE_9__.wireMenuWidgets)(payload.header.content, {
      items: payload.menus?.header ?? [],
      pageUrlMap,
      menuSlug: createdMenus.header?.slug
    });
  }
  if (payload.footer) {
    (0,_steps_wire_menu_widgets__WEBPACK_IMPORTED_MODULE_9__.wireMenuWidgets)(payload.footer.content, {
      items: payload.menus?.footer ?? [],
      pageUrlMap,
      menuSlug: createdMenus.footer?.slug
    });
  }
  const themeParts = [];
  if (payload.header) {
    themeParts.push({
      key: 'header',
      part: payload.header
    });
  }
  if (payload.footer) {
    themeParts.push({
      key: 'footer',
      part: payload.footer
    });
  }
  if (payload.error404) {
    themeParts.push({
      key: 'error404',
      part: payload.error404
    });
  }
  if (payload.singlePost) {
    themeParts.push({
      key: 'singlePost',
      part: payload.singlePost
    });
  }
  if (themeParts.length) {
    try {
      await (0,_steps_theme_parts__WEBPACK_IMPORTED_MODULE_8__.createThemeParts)(themeParts);
    } catch (e) {
      errors.push(`theme_parts: ${e.message}`);
    }
  }
  if (payload.samplePosts?.length) {
    try {
      await (0,_steps_sample_posts__WEBPACK_IMPORTED_MODULE_6__.createSamplePosts)(payload.samplePosts);
    } catch (e) {
      errors.push(`sample_posts: ${e.message}`);
    }
  }
  const result = {
    status: errors.length ? 'error' : 'success',
    homeUrl: window.location.origin,
    homePageId: homeWpId || 0,
    pageIdMap,
    ...(errors.length ? {
      errors,
      error: errors[0]
    } : {})
  };
  return result;
}
function resolveHomePageId(pages, pageIdMap) {
  if (pageIdMap.home) {
    return pageIdMap.home;
  }
  const homePage = pages[0];
  if (homePage && pageIdMap[homePage.id]) {
    return pageIdMap[homePage.id];
  }
  return undefined;
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/global-classes.ts":
/*!***********************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/global-classes.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deployGlobalClasses: function() { return /* binding */ deployGlobalClasses; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../types */ "./packages/apps/site-builder/src/deploy/types.ts");


async function deployGlobalClasses(globalClasses) {
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: _types__WEBPACK_IMPORTED_MODULE_1__.DEPLOY_DESIGN_SYSTEM_PATH,
    method: 'POST',
    data: {
      globalClasses
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/global-variables.ts":
/*!*************************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/global-variables.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deployGlobalVariables: function() { return /* binding */ deployGlobalVariables; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../types */ "./packages/apps/site-builder/src/deploy/types.ts");


async function deployGlobalVariables(globalVariables) {
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: _types__WEBPACK_IMPORTED_MODULE_1__.DEPLOY_DESIGN_SYSTEM_PATH,
    method: 'POST',
    data: {
      globalVariables
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/kit-settings.ts":
/*!*********************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/kit-settings.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   updateKitSettings: function() { return /* binding */ updateKitSettings; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function updateKitSettings(kitSettings) {
  const res = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/elementor/v1/settings/elementor_active_kit'
  });
  const kitId = res?.data?.value;
  if (!kitId) {
    throw new Error('Could not resolve active kit ID');
  }
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: `/wp/v2/elementor_library/${kitId}`,
    method: 'POST',
    data: {
      meta: {
        _elementor_page_settings: kitSettings
      }
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/logo.ts":
/*!*************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/logo.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   uploadLogo: function() { return /* binding */ uploadLogo; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function uploadLogo(logo) {
  const imageResponse = await fetch(logo.url);
  if (!imageResponse.ok) {
    throw new Error(`Failed to download logo: ${imageResponse.status}`);
  }
  const imageBlob = await imageResponse.blob();
  const media = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/media',
    method: 'POST',
    body: imageBlob,
    headers: {
      'Content-Disposition': `attachment; filename="${logo.filename}"`,
      'Content-Type': imageBlob.type || 'image/png'
    }
  });
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/settings',
    method: 'POST',
    data: {
      site_logo: media.id
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/menu-locations.ts":
/*!***********************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/menu-locations.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fetchMenuLocationSlugs: function() { return /* binding */ fetchMenuLocationSlugs; },
/* harmony export */   isInvalidMenuLocationError: function() { return /* binding */ isInvalidMenuLocationError; },
/* harmony export */   resolveMenuLocation: function() { return /* binding */ resolveMenuLocation; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function fetchMenuLocationSlugs() {
  const locations = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/menu-locations'
  });
  if (!locations) {
    return [];
  }
  if (Array.isArray(locations)) {
    return locations.map(location => location.name).filter(Boolean);
  }
  return Object.keys(locations);
}
function resolveMenuLocation(availableSlugs, candidates, fallbackPattern) {
  for (const candidate of candidates) {
    if (availableSlugs.includes(candidate)) {
      return candidate;
    }
  }
  if (fallbackPattern) {
    const matched = availableSlugs.find(slug => fallbackPattern.test(slug));
    if (matched) {
      return matched;
    }
  }
  return undefined;
}
function isInvalidMenuLocationError(error) {
  if (!error || typeof error !== 'object') {
    return false;
  }
  const err = error;
  if (err.code === 'rest_invalid_param' && err.data?.details?.locations?.code === 'rest_invalid_menu_location') {
    return true;
  }
  return err.code === 'rest_invalid_menu_location';
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/menus.ts":
/*!**************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/menus.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createMenus: function() { return /* binding */ createMenus; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _menu_locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./menu-locations */ "./packages/apps/site-builder/src/deploy/steps/menu-locations.ts");


async function postMenu(data) {
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/menus',
    method: 'POST',
    data
  });
}
async function createMenu(args) {
  const {
    name,
    items,
    pageIdMap,
    locationCandidates,
    fallbackPattern
  } = args;
  const availableSlugs = await (0,_menu_locations__WEBPACK_IMPORTED_MODULE_1__.fetchMenuLocationSlugs)();
  const location = (0,_menu_locations__WEBPACK_IMPORTED_MODULE_1__.resolveMenuLocation)(availableSlugs, locationCandidates, fallbackPattern);
  const baseData = {
    name,
    auto_add: false
  };
  let menu;
  try {
    menu = await postMenu(location ? {
      ...baseData,
      locations: [location]
    } : baseData);
  } catch (error) {
    if (!location || !(0,_menu_locations__WEBPACK_IMPORTED_MODULE_1__.isInvalidMenuLocationError)(error)) {
      throw error;
    }
    menu = await postMenu(baseData);
  }
  const menuItemPromises = items.map((item, index) => {
    const objectId = pageIdMap[item.pageId];
    if (!objectId) {
      return Promise.resolve();
    }
    return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: '/wp/v2/menu-items',
      method: 'POST',
      data: {
        title: item.title,
        object_id: objectId,
        menus: menu.id,
        object: 'page',
        type: 'post_type',
        status: 'publish',
        menu_order: index + 1,
        parent: 0
      }
    });
  });
  await Promise.all(menuItemPromises);
  return menu;
}
async function createMenus(menus, pageIdMap) {
  const created = {};
  if (menus?.header?.length) {
    created.header = await createMenu({
      name: `Header-${Date.now()}`,
      items: menus.header,
      pageIdMap,
      locationCandidates: ['header', 'menu-1', 'main', 'navigation'],
      fallbackPattern: /header|main|navigation/i
    });
  }
  if (menus?.footer?.length) {
    created.footer = await createMenu({
      name: `Footer-${Date.now()}`,
      items: menus.footer,
      pageIdMap,
      locationCandidates: ['footer', 'footer-menu', 'secondary'],
      fallbackPattern: /footer/i
    });
  }
  return created;
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/pages.ts":
/*!**************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/pages.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createPages: function() { return /* binding */ createPages; },
/* harmony export */   setHomePage: function() { return /* binding */ setHomePage; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function createPages(pages) {
  const pageIdMap = {};
  const pageUrlMap = {};
  for (const page of pages) {
    const created = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: '/wp/v2/pages',
      method: 'POST',
      data: {
        title: page.title,
        status: 'publish',
        meta: {
          _elementor_edit_mode: 'builder',
          _elementor_template_type: 'wp-page',
          _elementor_data: JSON.stringify(page.content)
        }
      }
    });
    pageIdMap[page.id] = created.id;
    if (created.link) {
      pageUrlMap[page.id] = created.link;
    }
  }
  return {
    pageIdMap,
    pageUrlMap
  };
}
async function setHomePage(homePageWpId) {
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/settings',
    method: 'POST',
    data: {
      page_on_front: homePageWpId,
      show_on_front: 'page'
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/sample-posts.ts":
/*!*********************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/sample-posts.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createSamplePosts: function() { return /* binding */ createSamplePosts; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function createSamplePosts(posts) {
  for (const post of posts) {
    await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: '/wp/v2/posts',
      method: 'POST',
      data: {
        title: post.title,
        content: post.content,
        status: 'publish'
      }
    });
  }
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/site-metadata.ts":
/*!**********************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/site-metadata.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   setSiteMetadata: function() { return /* binding */ setSiteMetadata; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function setSiteMetadata(siteMeta) {
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/settings',
    method: 'POST',
    data: {
      title: siteMeta.title,
      description: siteMeta.tagline
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/theme-parts.ts":
/*!********************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/theme-parts.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createThemeParts: function() { return /* binding */ createThemeParts; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

async function getSupportedDocumentTypes() {
  try {
    const schema = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: '/wp/v2/elementor_library',
      method: 'OPTIONS'
    });
    return schema?.schema?.properties?.meta?.properties?._elementor_template_type?.enum || [];
  } catch {
    return [];
  }
}
function getConditionBucket(type) {
  if (type === 'header') {
    return 'header';
  }
  if (type === 'footer') {
    return 'footer';
  }
  return 'single';
}
async function createThemeParts(parts) {
  const supportedTypes = await getSupportedDocumentTypes();
  const supported = parts.filter(({
    part
  }) => supportedTypes.includes(part.type));
  if (!supported.length) {
    return;
  }
  const templateIds = {};
  for (const {
    key,
    part
  } of supported) {
    const created = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: '/wp/v2/elementor_library',
      method: 'POST',
      data: {
        title: part.title,
        status: 'publish',
        meta: {
          _elementor_edit_mode: 'builder',
          _elementor_template_type: part.type,
          _elementor_data: JSON.stringify(part.content),
          _elementor_conditions: part.themeBuilderCondition || 'include/general'
        }
      }
    });
    templateIds[key] = created.id;
  }
  const conditions = {};
  for (const {
    key,
    part
  } of supported) {
    const tid = templateIds[key];
    if (!tid) {
      continue;
    }
    const conditionValue = part.themeBuilderCondition || 'include/general';
    const bucket = getConditionBucket(part.type);
    if (!conditions[bucket]) {
      conditions[bucket] = {};
    }
    conditions[bucket][tid] = [conditionValue];
  }
  await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/elementor/v1/settings/elementor_pro_theme_builder_conditions',
    method: 'POST',
    data: {
      value: conditions
    }
  });
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/steps/wire-menu-widgets.ts":
/*!**************************************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/steps/wire-menu-widgets.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   wireMenuWidgets: function() { return /* binding */ wireMenuWidgets; }
/* harmony export */ });
const MEGA_MENU_WIDGET_TYPE = 'mega-menu';
const NAV_MENU_WIDGET_TYPE = 'nav-menu';
function buildItemLink(url) {
  return {
    url,
    is_external: '',
    nofollow: ''
  };
}
function wireMegaMenu(node, items, pageUrlMap) {
  const menuItems = node.settings?.menu_items;
  if (!Array.isArray(menuItems)) {
    return 0;
  }
  let patched = 0;
  menuItems.forEach((menuItem, index) => {
    const item = items[index];
    if (!item || typeof menuItem !== 'object' || menuItem === null) {
      return;
    }
    const url = pageUrlMap[item.pageId];
    if (!url) {
      return;
    }
    menuItem.item_link = buildItemLink(url);
    patched += 1;
  });
  return patched;
}
function wireNavMenu(node, menuSlug) {
  if (!menuSlug || !node.settings) {
    return 0;
  }
  node.settings.menu = menuSlug;
  return 1;
}
function wireMenuWidgets(content, options) {
  const walk = nodes => {
    for (const node of nodes) {
      if (node.widgetType === MEGA_MENU_WIDGET_TYPE) {
        wireMegaMenu(node, options.items, options.pageUrlMap);
      } else if (node.widgetType === NAV_MENU_WIDGET_TYPE) {
        wireNavMenu(node, options.menuSlug);
      }
      if (Array.isArray(node.elements)) {
        walk(node.elements);
      }
    }
  };
  walk(content);
}

/***/ }),

/***/ "./packages/apps/site-builder/src/deploy/types.ts":
/*!********************************************************!*\
  !*** ./packages/apps/site-builder/src/deploy/types.ts ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEPLOY_DESIGN_SYSTEM_PATH: function() { return /* binding */ DEPLOY_DESIGN_SYSTEM_PATH; }
/* harmony export */ });
const DEPLOY_DESIGN_SYSTEM_PATH = '/elementor/v1/site-builder/deploy-design-system';

/***/ }),

/***/ "./packages/apps/site-builder/src/hooks/use-site-builder-iframe-messaging.ts":
/*!***********************************************************************************!*\
  !*** ./packages/apps/site-builder/src/hooks/use-site-builder-iframe-messaging.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSiteBuilderIframeMessaging: function() { return /* binding */ useSiteBuilderIframeMessaging; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _deploy__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../deploy */ "./packages/apps/site-builder/src/deploy/index.ts");
/* harmony import */ var _deploy_can_redirect_after_deploy__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../deploy/can-redirect-after-deploy */ "./packages/apps/site-builder/src/deploy/can-redirect-after-deploy.ts");
/* harmony import */ var _deploy_deploy_editor_redirect__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../deploy/deploy-editor-redirect */ "./packages/apps/site-builder/src/deploy/deploy-editor-redirect.ts");
/* harmony import */ var _site_builder_config__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../site-builder-config */ "./packages/apps/site-builder/src/site-builder-config.ts");





function sendReferrerInfo(iframe, event, targetOrigin, siteBuilderParams, connectAuth) {
  const config = (0,_site_builder_config__WEBPACK_IMPORTED_MODULE_4__.getSiteBuilderConfig)();
  iframe.contentWindow?.postMessage({
    type: 'referrer/info',
    instanceId: event.data?.payload?.instanceId ?? '',
    info: {
      connectAuth,
      exitTo: config?.exitTo,
      page: {
        url: window.location.href,
        elementorAiCurrentContext: (0,_site_builder_config__WEBPACK_IMPORTED_MODULE_4__.getElementorAiCurrentContext)()
      },
      user: {
        isAdmin: config?.isAdmin ?? false
      },
      siteBuilderParams
    }
  }, targetOrigin);
}
async function handleDeploy(iframe, event) {
  const origin = event.origin || '*';
  const payload = event.data?.payload;
  const isIncremental = payload?.mode === 'incremental';
  if (!payload) {
    iframe?.contentWindow?.postMessage({
      type: 'site-planner/deploy-website/result',
      payload: {
        status: 'error',
        error: 'Missing deploy payload'
      }
    }, origin);
    return null;
  }
  try {
    const result = await (0,_deploy__WEBPACK_IMPORTED_MODULE_1__.deployWebsite)(payload);
    iframe?.contentWindow?.postMessage({
      type: 'site-planner/deploy-website/result',
      payload: result
    }, origin);
    const editorPageId = (0,_deploy_can_redirect_after_deploy__WEBPACK_IMPORTED_MODULE_2__.resolveEditorRedirectPageId)({
      isIncremental,
      homePageId: result.homePageId,
      pageIdMap: result.pageIdMap,
      pages: payload.pages,
      errors: result.errors
    });
    if (editorPageId) {
      return `/wp-admin/post.php?post=${editorPageId}&action=elementor`;
    }
    return null;
  } catch (err) {
    iframe?.contentWindow?.postMessage({
      type: 'site-planner/deploy-website/result',
      payload: {
        status: 'error',
        error: err instanceof Error ? err.message : 'Deploy failed'
      }
    }, origin);
    return null;
  }
}
function useSiteBuilderIframeMessaging({
  iframeRef,
  iframeUrl,
  siteBuilderParams,
  connectAuth
}) {
  const pendingRedirectRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const allowedOrigin = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    try {
      return new URL(iframeUrl).origin;
    } catch {
      return '';
    }
  }, [iframeUrl]);
  const handleMessage = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async event => {
    if (!allowedOrigin) {
      return;
    }
    if (event.origin !== allowedOrigin) {
      return;
    }
    if (event.source !== iframeRef.current?.contentWindow) {
      return;
    }
    const {
      type
    } = event.data ?? {};
    if (type === 'get/referrer/info') {
      const iframe = iframeRef.current;
      if (iframe?.contentWindow) {
        sendReferrerInfo(iframe, event, allowedOrigin, siteBuilderParams, connectAuth);
      }
      return;
    }
    if (type === 'site-planner/deploy-website') {
      (0,_deploy_deploy_editor_redirect__WEBPACK_IMPORTED_MODULE_3__.clearPendingEditorRedirect)(pendingRedirectRef.current);
      pendingRedirectRef.current = null;
      const redirectUrl = await handleDeploy(iframeRef.current, event);
      if (redirectUrl) {
        pendingRedirectRef.current = (0,_deploy_deploy_editor_redirect__WEBPACK_IMPORTED_MODULE_3__.scheduleEditorRedirectAfterDeploy)(redirectUrl);
      }
      return;
    }
    if (type === 'site-planner/deploy-website/acknowledge') {
      (0,_deploy_deploy_editor_redirect__WEBPACK_IMPORTED_MODULE_3__.completeEditorRedirectOnDeployAcknowledge)(pendingRedirectRef.current);
      pendingRedirectRef.current = null;
      return;
    }
    if (type === 'element-selector/close') {
      const exitTo = (0,_site_builder_config__WEBPACK_IMPORTED_MODULE_4__.getSiteBuilderConfig)()?.exitTo;
      if (window.top && exitTo && typeof exitTo === 'string') {
        window.top.location.href = exitTo;
      }
    }
  }, [allowedOrigin, siteBuilderParams, connectAuth, iframeRef]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, [handleMessage]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return () => (0,_deploy_deploy_editor_redirect__WEBPACK_IMPORTED_MODULE_3__.clearPendingEditorRedirect)(pendingRedirectRef.current);
  }, []);
}

/***/ }),

/***/ "./packages/apps/site-builder/src/site-builder-config.ts":
/*!***************************************************************!*\
  !*** ./packages/apps/site-builder/src/site-builder-config.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementorAiCurrentContext: function() { return /* binding */ getElementorAiCurrentContext; },
/* harmony export */   getSiteBuilderConfig: function() { return /* binding */ getSiteBuilderConfig; }
/* harmony export */ });
function getSiteBuilderConfig() {
  return window.elementorAppConfig?.['site-builder'];
}
function getElementorAiCurrentContext() {
  return getSiteBuilderConfig()?.elementorAiCurrentContext || {};
}

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["apiFetch"];

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
/*!*************************************************!*\
  !*** ./packages/apps/site-builder/src/index.ts ***!
  \*************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* reexport safe */ _components__WEBPACK_IMPORTED_MODULE_0__.App; }
/* harmony export */ });
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ "./packages/apps/site-builder/src/components/index.ts");

}();
(window.elementorV2 = window.elementorV2 || {}).siteBuilder = __webpack_exports__;
/******/ })()
;
window.elementorV2.siteBuilder?.init?.();
//# sourceMappingURL=site-builder.js.map
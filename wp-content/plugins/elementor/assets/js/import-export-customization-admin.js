/*! elementor - v3.32.0 - 21-10-2025 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../app/assets/js/event-track/apps-event-tracking.js":
/*!***********************************************************!*\
  !*** ../app/assets/js/event-track/apps-event-tracking.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.appsEventTrackingDispatch = exports.AppsEventTracking = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _eventsConfig = _interopRequireDefault(__webpack_require__(/*! ../../../../core/common/modules/events-manager/assets/js/events-config */ "../core/common/modules/events-manager/assets/js/events-config.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var EVENTS_MAP = {
  PAGE_VIEWS_WEBSITE_TEMPLATES: 'page_views_website_templates',
  KITS_CLOUD_UPGRADE_CLICKED: 'kits_cloud_upgrade_clicked',
  EXPORT_KIT_CUSTOMIZATION: 'export_kit_customization',
  IMPORT_KIT_CUSTOMIZATION: 'import_kit_customization',
  KIT_IMPORT_STATUS: 'kit_import_status',
  KIT_CLOUD_LIBRARY_APPLY: 'kit_cloud_library_apply',
  KIT_CLOUD_LIBRARY_DELETE: 'kit_cloud_library_delete',
  IMPORT_EXPORT_ADMIN_ACTION: 'ie_admin_action',
  KIT_IMPORT_UPLOAD_FILE: 'kit_import_upload_file'
};
var appsEventTrackingDispatch = exports.appsEventTrackingDispatch = function appsEventTrackingDispatch(command, eventParams) {
  // Add existing eventParams key value pair to the data/details object.
  var objectCreator = function objectCreator(array, obj) {
    var _iterator = _createForOfIteratorHelper(array),
      _step;
    try {
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        var key = _step.value;
        if (eventParams.hasOwnProperty(key) && eventParams[key] !== null) {
          obj[key] = eventParams[key];
        }
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
    return obj;
  };
  var dataKeys = [];
  var detailsKeys = ['layout', 'site_part', 'error', 'document_name', 'document_type', 'view_type_clicked', 'tag', 'sort_direction', 'sort_type', 'action', 'grid_location', 'kit_name', 'page_source', 'element_position', 'element', 'event_type', 'modal_type', 'method', 'status', 'step', 'item', 'category', 'element_location', 'search_term', 'section', 'site_area'];
  var data = {};
  var details = {};
  var init = function init() {
    objectCreator(detailsKeys, details);
    objectCreator(dataKeys, data);
    var commandSplit = command.split('/');
    data.placement = commandSplit[0];
    data.event = commandSplit[1];

    // If 'details' is not empty, add the details object to the data object.
    if (Object.keys(details).length) {
      data.details = details;
    }
  };
  init();
  $e.run(command, data);
};
var AppsEventTracking = exports.AppsEventTracking = /*#__PURE__*/function () {
  function AppsEventTracking() {
    (0, _classCallCheck2.default)(this, AppsEventTracking);
  }
  return (0, _createClass2.default)(AppsEventTracking, null, [{
    key: "dispatchEvent",
    value: function dispatchEvent(eventName, payload) {
      return elementorCommon.eventsManager.dispatchEvent(eventName, payload);
    }
  }, {
    key: "sendPageViewsWebsiteTemplates",
    value: function sendPageViewsWebsiteTemplates(page) {
      return this.dispatchEvent(EVENTS_MAP.PAGE_VIEWS_WEBSITE_TEMPLATES, {
        trigger: _eventsConfig.default.triggers.pageLoaded,
        page_loaded: page,
        secondary_location: page
      });
    }
  }, {
    key: "sendKitsCloudUpgradeClicked",
    value: function sendKitsCloudUpgradeClicked(upgradeLocation) {
      return this.dispatchEvent(EVENTS_MAP.KITS_CLOUD_UPGRADE_CLICKED, {
        trigger: _eventsConfig.default.triggers.click,
        secondary_location: upgradeLocation,
        upgrade_location: upgradeLocation
      });
    }
  }, {
    key: "sendExportKitCustomization",
    value: function sendExportKitCustomization(payload) {
      return this.dispatchEvent(EVENTS_MAP.EXPORT_KIT_CUSTOMIZATION, _objectSpread({
        trigger: _eventsConfig.default.triggers.click
      }, payload));
    }
  }, {
    key: "sendImportKitCustomization",
    value: function sendImportKitCustomization(payload) {
      return this.dispatchEvent(EVENTS_MAP.IMPORT_KIT_CUSTOMIZATION, _objectSpread({
        trigger: _eventsConfig.default.triggers.click
      }, payload));
    }
  }, {
    key: "sendKitImportStatus",
    value: function sendKitImportStatus() {
      var error = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      return this.dispatchEvent(EVENTS_MAP.KIT_IMPORT_STATUS, _objectSpread({
        kit_import_status: !error
      }, error && {
        kit_import_error: error.message
      }));
    }
  }, {
    key: "sendKitCloudLibraryApply",
    value: function sendKitCloudLibraryApply(kitId, kitApplyUrl) {
      return this.dispatchEvent(EVENTS_MAP.KIT_CLOUD_LIBRARY_APPLY, _objectSpread({
        trigger: _eventsConfig.default.triggers.click,
        kit_cloud_id: kitId
      }, kitApplyUrl && {
        kit_apply_url: kitApplyUrl
      }));
    }
  }, {
    key: "sendKitCloudLibraryDelete",
    value: function sendKitCloudLibraryDelete() {
      return this.dispatchEvent(EVENTS_MAP.KIT_CLOUD_LIBRARY_DELETE, {
        trigger: _eventsConfig.default.triggers.click
      });
    }
  }, {
    key: "sendImportExportAdminAction",
    value: function sendImportExportAdminAction(actionType) {
      return this.dispatchEvent(EVENTS_MAP.IMPORT_EXPORT_ADMIN_ACTION, {
        trigger: _eventsConfig.default.triggers.click,
        action_type: actionType
      });
    }
  }, {
    key: "sendKitImportUploadFile",
    value: function sendKitImportUploadFile(status) {
      return this.dispatchEvent(EVENTS_MAP.KIT_IMPORT_UPLOAD_FILE, {
        kit_import_upload_file_status: status
      });
    }
  }]);
}();

/***/ }),

/***/ "../core/common/modules/events-manager/assets/js/events-config.js":
/*!************************************************************************!*\
  !*** ../core/common/modules/events-manager/assets/js/events-config.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var eventsConfig = {
  triggers: {
    click: 'Click',
    accordionClick: 'Accordion Click',
    toggleClick: 'Toggle Click',
    dropdownClick: 'Click Dropdown',
    editorLoaded: 'Editor Loaded',
    visible: 'Visible',
    pageLoaded: 'Page Loaded'
  },
  locations: {
    widgetPanel: 'Widget Panel',
    topBar: 'Top Bar',
    elementorEditor: 'Elementor Editor',
    templatesLibrary: {
      library: 'Templates Library'
    },
    app: {
      import: 'Import Kit',
      export: 'Export Kit',
      kitLibrary: 'Kit Library',
      cloudKitLibrary: 'Cloud Kit Library'
    },
    variables: 'Variables Panel',
    admin: 'WP admin'
  },
  secondaryLocations: {
    layout: 'Layout Section',
    basic: 'Basic Section',
    'pro-elements': 'Pro Section',
    general: 'General Section',
    'theme-elements': 'Site Section',
    'theme-elements-single': 'Single Section',
    'woocommerce-elements': 'WooCommerce Section',
    wordpress: 'WordPress Section',
    categories: 'Widgets Tab',
    global: 'Globals Tab',
    'whats-new': 'What\'s New',
    'document-settings': 'Document Settings icon',
    'preview-page': 'Preview Page',
    'publish-button': 'Publish Button',
    'widget-panel': 'Widget Panel Icon',
    finder: 'Finder',
    help: 'Help',
    elementorLogoDropdown: 'top_bar_elementor_logo_dropdown',
    elementorLogo: 'Elementor Logo',
    eLogoMenu: 'E-logo Menu',
    notes: 'Notes',
    siteSettings: 'Site Settings',
    structure: 'Structure',
    documentNameDropdown: 'Document Name dropdown',
    responsiveControls: 'Responsive controls',
    launchpad: 'launchpad',
    checklistHeader: 'Checklist Header',
    checklistSteps: 'Checklist Steps',
    userPreferences: 'User Preferences',
    contextMenu: 'Context Menu',
    templateLibrary: {
      saveModal: 'Save to Modal',
      moveModal: 'Move to Modal',
      bulkMoveModal: 'Bulk Move to Modal',
      copyModal: 'Copy to Modal',
      bulkCopyModal: 'Bulk Copy to Modal',
      saveModalSelectFolder: 'Save to Modal - select folder',
      saveModalSelectConnect: 'Save to Modal - connect',
      saveModalSelectUpgrade: 'Save to Modal - upgrade',
      importModal: 'Import Modal',
      newFolderModal: 'New Folder Modal',
      deleteDialog: 'Delete Dialog',
      deleteFolderDialog: 'Delete Folder Dialog',
      renameDialog: 'Rename Dialog',
      createFolderDialog: 'Create Folder Dialog',
      applySettingsDialog: 'Apply Settings Dialog',
      cloudTab: 'Cloud Tab',
      siteTab: 'Site Tab',
      cloudTabFolder: 'Cloud Tab - Folder',
      cloudTabConnect: 'Cloud Tab - Connect',
      cloudTabUpgrade: 'Cloud Tab - Upgrade',
      morePopup: 'Context Menu',
      quotaBar: 'Quota Bar'
    },
    kitLibrary: {
      cloudKitLibrary: 'kits_cloud_library',
      cloudKitLibraryConnect: 'kits_cloud_library_connect',
      cloudKitLibraryUpgrade: 'kits_cloud_library_upgrade',
      kitExportCustomization: 'kit_export_customization',
      kitExport: 'kit_export',
      kitExportCustomizationEdit: 'kit_export_customization_edit',
      kitExportSummary: 'kit_export_summary',
      kitImportUploadBox: 'kit_import_upload_box',
      kitImportCustomization: 'kit_import_customization',
      kitImportSummary: 'kit_import_summary'
    },
    variablesPopover: 'Variables Popover',
    admin: {
      pluginToolsTab: 'plugin_tools_tab',
      pluginWebsiteTemplatesTab: 'plugin_website_templates_tab'
    }
  },
  elements: {
    accordionSection: 'Accordion section',
    buttonIcon: 'Button Icon',
    mainCta: 'Main CTA',
    button: 'Button',
    link: 'Link',
    dropdown: 'Dropdown',
    toggle: 'Toggle',
    launchpadChecklist: 'Checklist popup'
  },
  names: {
    v1: {
      layout: 'v1_widgets_tab_layout_section',
      basic: 'v1_widgets_tab_basic_section',
      'pro-elements': 'v1_widgets_tab_pro_section',
      general: 'v1_widgets_tab_general_section',
      'theme-elements': 'v1_widgets_tab_site_section',
      'theme-elements-single': 'v1_widgets_tab_single_section',
      'woocommerce-elements': 'v1_widgets_tab_woocommerce_section',
      wordpress: 'v1_widgets_tab_wordpress_section',
      categories: 'v1_widgets_tab',
      global: 'v1_globals_tab'
    },
    topBar: {
      whatsNew: 'top_bar_whats_new',
      documentSettings: 'top_bar_document_settings_icon',
      previewPage: 'top_bar_preview_page',
      publishButton: 'top_bar_publish_button',
      widgetPanel: 'top_bar_widget_panel_icon',
      finder: 'top_bar_finder',
      help: 'top_bar_help',
      history: 'top_bar_elementor_logo_dropdown_history',
      userPreferences: 'top_bar_elementor_logo_dropdown_user_preferences',
      keyboardShortcuts: 'top_bar_elementor_logo_dropdown_keyboard_shortcuts',
      exitToWordpress: 'top_bar_elementor_logo_dropdown_exit_to_wordpress',
      themeBuilder: 'top_bar_elementor_logo_dropdown_theme_builder',
      notes: 'top_bar_notes',
      siteSettings: 'top_bar_site_setting',
      structure: 'top_bar_structure',
      documentNameDropdown: 'top_bar_document_name_dropdown',
      responsiveControls: 'top_bar_responsive_controls',
      launchpadOn: 'top_bar_checklist_icon_show',
      launchpadOff: 'top_bar_checklist_icon_hide',
      elementorLogoDropdown: 'open_e_menu',
      connectAccount: 'connect_account',
      accountConnected: 'account_connected'
    },
    // ChecklistSteps event names are generated dynamically, based on stepId and action type taken: title, action, done, undone, upgrade
    elementorEditor: {
      checklist: {
        checklistHeaderClose: 'checklist_header_close_icon',
        checklistFirstPopup: 'checklist popup triggered'
      },
      userPreferences: {
        checklistShow: 'checklist_userpreferences_toggle_show',
        checklistHide: 'checklist_userpreferences_toggle_hide'
      }
    },
    variables: {
      open: 'open_variables_popover',
      add: 'add_new_variable',
      connect: 'connect_variable',
      save: 'save_new_variable'
    }
  }
};
var _default = exports["default"] = eventsConfig;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \****************************************************************/
/***/ ((module) => {

function _classCallCheck(a, n) {
  if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function");
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/createClass.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/createClass.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperties(e, r) {
  for (var t = 0; t < r.length; t++) {
    var o = r[t];
    o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, toPropertyKey(o.key), o);
  }
}
function _createClass(e, r, t) {
  return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", {
    writable: !1
  }), e;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(e, r, t) {
  return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(o) {
  "@babel/helpers - typeof";

  return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*********************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/admin.js ***!
  \*********************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _appsEventTracking = __webpack_require__(/*! ../../../../assets/js/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var Admin = /*#__PURE__*/function () {
  function Admin() {
    var _this = this;
    (0, _classCallCheck2.default)(this, Admin);
    var urlParams = new URLSearchParams(window.location.search);
    if ('elementor-tools' === urlParams.get('page')) {
      this.sendPageToolsViewedEvent();
      elementorAdmin.elements.$settingsTabs.on('focus', function () {
        var location = window.location.hash.slice(1);
        _this.maybeSendImportExportLocationEvent(location);
      });
      this.maybeSendImportExportLocationEvent(window.location.hash.slice(1));
    }
    this.revertButton = document.getElementById('elementor-import-export__revert_kit');
    this.importFroLibraryButton = document.getElementById('elementor-import-export__import_from_library');
    this.importButton = document.getElementById('elementor-import-export__import');
    this.exportButton = document.getElementById('elementor-import-export__export');
    if (this.revertButton) {
      this.revertButton.addEventListener('click', this.onRevertButtonClick.bind(this));
    }
    if (this.importFroLibraryButton) {
      this.importFroLibraryButton.addEventListener('click', this.onImportFromLibraryButtonClick.bind(this));
    }
    if (this.importButton) {
      this.importButton.addEventListener('click', this.onImportButtonClick.bind(this));
    }
    if (this.exportButton) {
      this.exportButton.addEventListener('click', this.onExportButtonClick.bind(this));
    }
  }
  return (0, _createClass2.default)(Admin, [{
    key: "sendPageToolsViewedEvent",
    value: function sendPageToolsViewedEvent() {
      _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.admin.pluginToolsTab);
    }
  }, {
    key: "maybeSendImportExportLocationEvent",
    value: function maybeSendImportExportLocationEvent(location) {
      if ('tab-import-export-kit' === location) {
        _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.admin.pluginWebsiteTemplatesTab);
      }
    }
  }, {
    key: "onRevertButtonClick",
    value: function onRevertButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Revert');
    }
  }, {
    key: "onExportButtonClick",
    value: function onExportButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Export');
    }
  }, {
    key: "onImportButtonClick",
    value: function onImportButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Import');
    }
  }, {
    key: "onImportFromLibraryButtonClick",
    value: function onImportFromLibraryButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Import from Library');
    }
  }]);
}();
window.addEventListener('load', function () {
  new Admin();
});
})();

/******/ })()
;
//# sourceMappingURL=import-export-customization-admin.js.map
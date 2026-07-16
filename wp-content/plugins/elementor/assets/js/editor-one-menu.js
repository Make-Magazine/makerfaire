/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/editor/utils/editor-one-events.js":
/*!**********************************************************!*\
  !*** ../assets/dev/js/editor/utils/editor-one-events.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.createDebouncedWidgetPanelSearch = exports.createDebouncedFinderSearch = exports.EditorOneEventManager = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var EditorOneEventManager = exports.EditorOneEventManager = /*#__PURE__*/function () {
  function EditorOneEventManager() {
    (0, _classCallCheck2.default)(this, EditorOneEventManager);
  }
  return (0, _createClass2.default)(EditorOneEventManager, null, [{
    key: "getEventsManager",
    value: function getEventsManager() {
      var _elementorCommon;
      return (_elementorCommon = elementorCommon) === null || _elementorCommon === void 0 ? void 0 : _elementorCommon.eventsManager;
    }
  }, {
    key: "getConfig",
    value: function getConfig() {
      var _this$getEventsManage;
      return (_this$getEventsManage = this.getEventsManager()) === null || _this$getEventsManage === void 0 ? void 0 : _this$getEventsManage.config;
    }
  }, {
    key: "canSendEvents",
    value: function canSendEvents() {
      var _elementorCommon2;
      return ((_elementorCommon2 = elementorCommon) === null || _elementorCommon2 === void 0 || (_elementorCommon2 = _elementorCommon2.config) === null || _elementorCommon2 === void 0 || (_elementorCommon2 = _elementorCommon2.editor_events) === null || _elementorCommon2 === void 0 ? void 0 : _elementorCommon2.can_send_events) || false;
    }
  }, {
    key: "isEventsManagerAvailable",
    value: function isEventsManagerAvailable() {
      var eventsManager = this.getEventsManager();
      return eventsManager && 'function' === typeof eventsManager.dispatchEvent;
    }
  }, {
    key: "dispatchEvent",
    value: function dispatchEvent(eventName, payload) {
      if (!this.isEventsManagerAvailable() || !this.canSendEvents()) {
        return false;
      }
      try {
        return this.getEventsManager().dispatchEvent(eventName, payload);
      } catch (error) {
        return false;
      }
    }
  }, {
    key: "toLowerSnake",
    value: function toLowerSnake(value) {
      if (!value || 'string' !== typeof value) {
        return value;
      }
      return value.replace(/\s+/g, '_').toLowerCase();
    }
  }, {
    key: "decodeHtmlEntities",
    value: function decodeHtmlEntities(text) {
      if (!text || 'string' !== typeof text) {
        return text;
      }
      var doc = new DOMParser().parseFromString(text, 'text/html');
      return doc.body.textContent || text;
    }
  }, {
    key: "isInEditorContext",
    value: function isInEditorContext() {
      var _window$elementor;
      return 'undefined' !== typeof window.elementor && !!((_window$elementor = window.elementor) !== null && _window$elementor !== void 0 && _window$elementor.documents);
    }
  }, {
    key: "getFinderContext",
    value: function getFinderContext() {
      var _config$appTypes, _config$appTypes2, _config$locations, _config$locations2;
      var config = this.getConfig();
      var isEditor = this.isInEditorContext();
      return {
        windowName: isEditor ? config === null || config === void 0 || (_config$appTypes = config.appTypes) === null || _config$appTypes === void 0 ? void 0 : _config$appTypes.editor : config === null || config === void 0 || (_config$appTypes2 = config.appTypes) === null || _config$appTypes2 === void 0 ? void 0 : _config$appTypes2.wpAdmin,
        targetLocation: this.toLowerSnake(isEditor ? config === null || config === void 0 || (_config$locations = config.locations) === null || _config$locations === void 0 ? void 0 : _config$locations.topBar : config === null || config === void 0 || (_config$locations2 = config.locations) === null || _config$locations2 === void 0 ? void 0 : _config$locations2.sidebar)
      };
    }
  }, {
    key: "createBasePayload",
    value: function createBasePayload() {
      var _config$appTypes$edit, _config$appTypes3, _config$appTypes$edit2, _config$appTypes4;
      var overrides = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var config = this.getConfig();
      return _objectSpread({
        app_type: (_config$appTypes$edit = config === null || config === void 0 || (_config$appTypes3 = config.appTypes) === null || _config$appTypes3 === void 0 ? void 0 : _config$appTypes3.editor) !== null && _config$appTypes$edit !== void 0 ? _config$appTypes$edit : 'editor',
        window_name: (_config$appTypes$edit2 = config === null || config === void 0 || (_config$appTypes4 = config.appTypes) === null || _config$appTypes4 === void 0 ? void 0 : _config$appTypes4.editor) !== null && _config$appTypes$edit2 !== void 0 ? _config$appTypes$edit2 : 'editor'
      }, overrides);
    }
  }, {
    key: "sendTopBarPublishDropdown",
    value: function sendTopBarPublishDropdown(targetName) {
      var _config$names, _config$triggers, _config$targetTypes, _config$interactionRe, _config$locations3, _config$secondaryLoca, _config$targetTypes2;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names = config.names) === null || _config$names === void 0 || (_config$names = _config$names.editorOne) === null || _config$names === void 0 ? void 0 : _config$names.topBarPublishDropdown, this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers = config.triggers) === null || _config$triggers === void 0 ? void 0 : _config$triggers.click),
        target_type: config === null || config === void 0 || (_config$targetTypes = config.targetTypes) === null || _config$targetTypes === void 0 ? void 0 : _config$targetTypes.dropdownItem,
        target_name: targetName,
        interaction_result: config === null || config === void 0 || (_config$interactionRe = config.interactionResults) === null || _config$interactionRe === void 0 ? void 0 : _config$interactionRe.actionSelected,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations3 = config.locations) === null || _config$locations3 === void 0 ? void 0 : _config$locations3.topBar),
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca = config.secondaryLocations) === null || _config$secondaryLoca === void 0 ? void 0 : _config$secondaryLoca.publishDropdown),
        location_l2: config === null || config === void 0 || (_config$targetTypes2 = config.targetTypes) === null || _config$targetTypes2 === void 0 ? void 0 : _config$targetTypes2.dropdownItem,
        interaction_description: 'User selected an action from the publish dropdown'
      }));
    }
  }, {
    key: "sendTopBarPageList",
    value: function sendTopBarPageList(targetName) {
      var _config$names2, _config$triggers2, _config$targetTypes3, _config$interactionRe2, _config$interactionRe3, _config$locations4, _config$secondaryLoca2, _config$targetTypes4;
      var isCreate = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names2 = config.names) === null || _config$names2 === void 0 || (_config$names2 = _config$names2.editorOne) === null || _config$names2 === void 0 ? void 0 : _config$names2.topBarPageList, this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers2 = config.triggers) === null || _config$triggers2 === void 0 ? void 0 : _config$triggers2.click),
        target_type: config === null || config === void 0 || (_config$targetTypes3 = config.targetTypes) === null || _config$targetTypes3 === void 0 ? void 0 : _config$targetTypes3.dropdownItem,
        target_name: targetName,
        interaction_result: isCreate ? config === null || config === void 0 || (_config$interactionRe2 = config.interactionResults) === null || _config$interactionRe2 === void 0 ? void 0 : _config$interactionRe2.create : config === null || config === void 0 || (_config$interactionRe3 = config.interactionResults) === null || _config$interactionRe3 === void 0 ? void 0 : _config$interactionRe3.navigate,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations4 = config.locations) === null || _config$locations4 === void 0 ? void 0 : _config$locations4.topBar),
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca2 = config.secondaryLocations) === null || _config$secondaryLoca2 === void 0 ? void 0 : _config$secondaryLoca2.pageListDropdown),
        location_l2: config === null || config === void 0 || (_config$targetTypes4 = config.targetTypes) === null || _config$targetTypes4 === void 0 ? void 0 : _config$targetTypes4.dropdownItem,
        interaction_description: 'User selected an action from the page list dropdown'
      }));
    }
  }, {
    key: "sendSiteSettingsSession",
    value: function sendSiteSettingsSession(_ref) {
      var _config$names3, _config$triggers3, _config$interactionRe4, _config$locations5, _config$secondaryLoca3;
      var targetType = _ref.targetType,
        _ref$visitedItems = _ref.visitedItems,
        visitedItems = _ref$visitedItems === void 0 ? [] : _ref$visitedItems,
        _ref$savedItems = _ref.savedItems,
        savedItems = _ref$savedItems === void 0 ? [] : _ref$savedItems,
        state = _ref.state;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names3 = config.names) === null || _config$names3 === void 0 || (_config$names3 = _config$names3.editorOne) === null || _config$names3 === void 0 ? void 0 : _config$names3.siteSettingsSession, this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers3 = config.triggers) === null || _config$triggers3 === void 0 ? void 0 : _config$triggers3.click),
        target_type: targetType,
        target_name: 'site_settings',
        interaction_result: config === null || config === void 0 || (_config$interactionRe4 = config.interactionResults) === null || _config$interactionRe4 === void 0 ? void 0 : _config$interactionRe4.sessionEnd,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations5 = config.locations) === null || _config$locations5 === void 0 ? void 0 : _config$locations5.leftPanel),
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca3 = config.secondaryLocations) === null || _config$secondaryLoca3 === void 0 ? void 0 : _config$secondaryLoca3.siteSettings),
        interaction_description: 'Records areas visited as part of the site setting session',
        metadata: {
          visited_items: visitedItems,
          saved_items: savedItems
        },
        state: state
      }));
    }
  }, {
    key: "sendELibraryNav",
    value: function sendELibraryNav(tabName) {
      var _config$names4, _config$triggers4, _config$targetTypes5, _config$interactionRe5, _config$locations6, _config$secondaryLoca4;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names4 = config.names) === null || _config$names4 === void 0 || (_config$names4 = _config$names4.editorOne) === null || _config$names4 === void 0 ? void 0 : _config$names4.eLibraryNav, this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers4 = config.triggers) === null || _config$triggers4 === void 0 ? void 0 : _config$triggers4.tabSelect),
        target_type: config === null || config === void 0 || (_config$targetTypes5 = config.targetTypes) === null || _config$targetTypes5 === void 0 ? void 0 : _config$targetTypes5.tab,
        target_name: this.toLowerSnake(tabName),
        interaction_result: config === null || config === void 0 || (_config$interactionRe5 = config.interactionResults) === null || _config$interactionRe5 === void 0 ? void 0 : _config$interactionRe5.tabChanged,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations6 = config.locations) === null || _config$locations6 === void 0 ? void 0 : _config$locations6.elementorLibrary),
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca4 = config.secondaryLocations) === null || _config$secondaryLoca4 === void 0 ? void 0 : _config$secondaryLoca4.libraryTabs),
        interaction_description: 'User navigates within elementor library'
      }));
    }
  }, {
    key: "sendELibraryInsert",
    value: function sendELibraryInsert(_ref2) {
      var _config$triggers5, _config$targetTypes6, _config$interactionRe6, _config$locations7, _config$secondaryLoca5, _config$names5;
      var assetId = _ref2.assetId,
        assetName = _ref2.assetName,
        libraryType = _ref2.libraryType,
        _ref2$proRequired = _ref2.proRequired,
        proRequired = _ref2$proRequired === void 0 ? false : _ref2$proRequired;
      var config = this.getConfig();
      var payload = this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers5 = config.triggers) === null || _config$triggers5 === void 0 ? void 0 : _config$triggers5.insert),
        target_type: config === null || config === void 0 || (_config$targetTypes6 = config.targetTypes) === null || _config$targetTypes6 === void 0 ? void 0 : _config$targetTypes6.button,
        target_name: String(assetId),
        interaction_result: config === null || config === void 0 || (_config$interactionRe6 = config.interactionResults) === null || _config$interactionRe6 === void 0 ? void 0 : _config$interactionRe6.assetInserted,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations7 = config.locations) === null || _config$locations7 === void 0 ? void 0 : _config$locations7.elementorLibrary),
        location_l1: this.toLowerSnake(libraryType),
        location_l2: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca5 = config.secondaryLocations) === null || _config$secondaryLoca5 === void 0 ? void 0 : _config$secondaryLoca5.assetCard),
        interaction_description: 'User inserts block/pages from elementor library',
        metadata: {
          template_id: String(assetId),
          template_name: this.decodeHtmlEntities(assetName) || ''
        }
      });
      if (proRequired) {
        payload.state = 'pro_plan_required';
      }
      return this.dispatchEvent(config === null || config === void 0 || (_config$names5 = config.names) === null || _config$names5 === void 0 || (_config$names5 = _config$names5.editorOne) === null || _config$names5 === void 0 ? void 0 : _config$names5.eLibraryInsert, payload);
    }
  }, {
    key: "sendELibraryFavorite",
    value: function sendELibraryFavorite(_ref3) {
      var _config$triggers6, _config$targetTypes7, _config$interactionRe7, _config$locations8, _config$secondaryLoca6, _config$names6;
      var assetId = _ref3.assetId,
        assetName = _ref3.assetName,
        libraryType = _ref3.libraryType,
        isFavorite = _ref3.isFavorite,
        _ref3$proRequired = _ref3.proRequired,
        proRequired = _ref3$proRequired === void 0 ? false : _ref3$proRequired;
      var config = this.getConfig();
      var payload = this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers6 = config.triggers) === null || _config$triggers6 === void 0 ? void 0 : _config$triggers6.click),
        target_type: config === null || config === void 0 || (_config$targetTypes7 = config.targetTypes) === null || _config$targetTypes7 === void 0 ? void 0 : _config$targetTypes7.toggle,
        target_name: String(assetId),
        interaction_result: config === null || config === void 0 || (_config$interactionRe7 = config.interactionResults) === null || _config$interactionRe7 === void 0 ? void 0 : _config$interactionRe7.assetFavorite,
        target_value: Boolean(isFavorite),
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations8 = config.locations) === null || _config$locations8 === void 0 ? void 0 : _config$locations8.elementorLibrary),
        location_l1: this.toLowerSnake(libraryType),
        location_l2: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca6 = config.secondaryLocations) === null || _config$secondaryLoca6 === void 0 ? void 0 : _config$secondaryLoca6.assetCard),
        interaction_description: 'User favorite block/pages from elementor library',
        metadata: {
          template_id: String(assetId),
          template_name: this.decodeHtmlEntities(assetName) || ''
        }
      });
      if (proRequired) {
        payload.state = 'pro_plan_required';
      }
      return this.dispatchEvent(config === null || config === void 0 || (_config$names6 = config.names) === null || _config$names6 === void 0 || (_config$names6 = _config$names6.editorOne) === null || _config$names6 === void 0 ? void 0 : _config$names6.eLibraryFavorite, payload);
    }
  }, {
    key: "sendELibraryGenerateAi",
    value: function sendELibraryGenerateAi(_ref4) {
      var _config$names7, _config$triggers7, _config$targetTypes8, _config$interactionRe8, _config$locations9, _config$secondaryLoca7;
      var assetId = _ref4.assetId,
        assetName = _ref4.assetName,
        libraryType = _ref4.libraryType;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names7 = config.names) === null || _config$names7 === void 0 || (_config$names7 = _config$names7.editorOne) === null || _config$names7 === void 0 ? void 0 : _config$names7.eLibraryGenerateAi, this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers7 = config.triggers) === null || _config$triggers7 === void 0 ? void 0 : _config$triggers7.click),
        target_type: config === null || config === void 0 || (_config$targetTypes8 = config.targetTypes) === null || _config$targetTypes8 === void 0 ? void 0 : _config$targetTypes8.button,
        target_name: String(assetId),
        interaction_result: config === null || config === void 0 || (_config$interactionRe8 = config.interactionResults) === null || _config$interactionRe8 === void 0 ? void 0 : _config$interactionRe8.aiGenerate,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations9 = config.locations) === null || _config$locations9 === void 0 ? void 0 : _config$locations9.elementorLibrary),
        location_l1: this.toLowerSnake(libraryType),
        location_l2: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca7 = config.secondaryLocations) === null || _config$secondaryLoca7 === void 0 ? void 0 : _config$secondaryLoca7.assetCard),
        interaction_description: 'User generated block/page based on a library asset',
        metadata: {
          template_id: String(assetId),
          template_name: this.decodeHtmlEntities(assetName) || ''
        }
      }));
    }
  }, {
    key: "sendFinderSearchInput",
    value: function sendFinderSearchInput(_ref5) {
      var _config$triggers8, _config$targetTypes9, _config$interactionRe9, _config$interactionRe0, _config$secondaryLoca8, _config$names8;
      var resultsCount = _ref5.resultsCount,
        _ref5$searchTerm = _ref5.searchTerm,
        searchTerm = _ref5$searchTerm === void 0 ? null : _ref5$searchTerm;
      var config = this.getConfig();
      var hasResults = resultsCount > 0;
      var finderContext = this.getFinderContext();
      var payload = this.createBasePayload({
        window_name: finderContext.windowName,
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers8 = config.triggers) === null || _config$triggers8 === void 0 ? void 0 : _config$triggers8.typing),
        target_type: config === null || config === void 0 || (_config$targetTypes9 = config.targetTypes) === null || _config$targetTypes9 === void 0 ? void 0 : _config$targetTypes9.searchInput,
        target_name: 'finder',
        interaction_result: hasResults ? config === null || config === void 0 || (_config$interactionRe9 = config.interactionResults) === null || _config$interactionRe9 === void 0 ? void 0 : _config$interactionRe9.resultsUpdated : config === null || config === void 0 || (_config$interactionRe0 = config.interactionResults) === null || _config$interactionRe0 === void 0 ? void 0 : _config$interactionRe0.noResults,
        target_location: finderContext.targetLocation,
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca8 = config.secondaryLocations) === null || _config$secondaryLoca8 === void 0 ? void 0 : _config$secondaryLoca8.finder),
        interaction_description: 'Finder search input, follows debounce behavior',
        metadata: {
          results_count: resultsCount
        }
      });
      if (!hasResults && searchTerm) {
        payload.metadata.search_term = searchTerm;
      }
      return this.dispatchEvent(config === null || config === void 0 || (_config$names8 = config.names) === null || _config$names8 === void 0 || (_config$names8 = _config$names8.editorOne) === null || _config$names8 === void 0 ? void 0 : _config$names8.finderSearchInput, payload);
    }
  }, {
    key: "sendFinderResultSelect",
    value: function sendFinderResultSelect(choice) {
      var _config$names9, _config$triggers9, _config$targetTypes0, _config$interactionRe1, _config$secondaryLoca9, _config$secondaryLoca0;
      var config = this.getConfig();
      var finderContext = this.getFinderContext();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names9 = config.names) === null || _config$names9 === void 0 || (_config$names9 = _config$names9.editorOne) === null || _config$names9 === void 0 ? void 0 : _config$names9.finderResultSelect, this.createBasePayload({
        window_name: finderContext.windowName,
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers9 = config.triggers) === null || _config$triggers9 === void 0 ? void 0 : _config$triggers9.click),
        target_type: config === null || config === void 0 || (_config$targetTypes0 = config.targetTypes) === null || _config$targetTypes0 === void 0 ? void 0 : _config$targetTypes0.searchResult,
        target_name: choice,
        interaction_result: config === null || config === void 0 || (_config$interactionRe1 = config.interactionResults) === null || _config$interactionRe1 === void 0 ? void 0 : _config$interactionRe1.selected,
        target_location: finderContext.targetLocation,
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca9 = config.secondaryLocations) === null || _config$secondaryLoca9 === void 0 ? void 0 : _config$secondaryLoca9.finder),
        location_l2: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca0 = config.secondaryLocations) === null || _config$secondaryLoca0 === void 0 ? void 0 : _config$secondaryLoca0.finderResults),
        interaction_description: 'Finder search results was selected'
      }));
    }
  }, {
    key: "sendCanvasEmptyBoxAction",
    value: function sendCanvasEmptyBoxAction(_ref6) {
      var _config$triggers0, _config$targetTypes1, _config$interactionRe10, _config$locations0, _config$secondaryLoca1, _config$names0;
      var targetName = _ref6.targetName,
        _ref6$metadata = _ref6.metadata,
        metadata = _ref6$metadata === void 0 ? {} : _ref6$metadata,
        _ref6$containerCreate = _ref6.containerCreated,
        containerCreated = _ref6$containerCreate === void 0 ? null : _ref6$containerCreate;
      var config = this.getConfig();
      var payload = this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers0 = config.triggers) === null || _config$triggers0 === void 0 ? void 0 : _config$triggers0.click),
        target_type: config === null || config === void 0 || (_config$targetTypes1 = config.targetTypes) === null || _config$targetTypes1 === void 0 ? void 0 : _config$targetTypes1.buttons,
        target_name: targetName,
        interaction_result: config === null || config === void 0 || (_config$interactionRe10 = config.interactionResults) === null || _config$interactionRe10 === void 0 ? void 0 : _config$interactionRe10.selected,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations0 = config.locations) === null || _config$locations0 === void 0 ? void 0 : _config$locations0.canvas),
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca1 = config.secondaryLocations) === null || _config$secondaryLoca1 === void 0 ? void 0 : _config$secondaryLoca1.emptyBox),
        interaction_description: 'Empty box on canvas actions'
      });
      if (Object.keys(metadata).length > 0) {
        payload.metadata = metadata;
      }
      if (containerCreated !== null) {
        payload.state = containerCreated;
      }
      return this.dispatchEvent(config === null || config === void 0 || (_config$names0 = config.names) === null || _config$names0 === void 0 || (_config$names0 = _config$names0.editorOne) === null || _config$names0 === void 0 ? void 0 : _config$names0.canvasEmptyBoxAction, payload);
    }
  }, {
    key: "sendWidgetPanelSearch",
    value: function sendWidgetPanelSearch(_ref7) {
      var _config$triggers1, _config$targetTypes10, _config$interactionRe11, _config$interactionRe12, _config$locations1, _config$locations10, _config$secondaryLoca10, _config$names1;
      var resultsCount = _ref7.resultsCount,
        _ref7$userInput = _ref7.userInput,
        userInput = _ref7$userInput === void 0 ? null : _ref7$userInput;
      var config = this.getConfig();
      var hasResults = resultsCount > 0;
      var payload = this.createBasePayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers1 = config.triggers) === null || _config$triggers1 === void 0 ? void 0 : _config$triggers1.typing),
        target_type: config === null || config === void 0 || (_config$targetTypes10 = config.targetTypes) === null || _config$targetTypes10 === void 0 ? void 0 : _config$targetTypes10.searchWidget,
        target_name: 'search_widget',
        interaction_result: hasResults ? config === null || config === void 0 || (_config$interactionRe11 = config.interactionResults) === null || _config$interactionRe11 === void 0 ? void 0 : _config$interactionRe11.resultsUpdated : config === null || config === void 0 || (_config$interactionRe12 = config.interactionResults) === null || _config$interactionRe12 === void 0 ? void 0 : _config$interactionRe12.noResults,
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations1 = config.locations) === null || _config$locations1 === void 0 ? void 0 : _config$locations1.leftPanel),
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$locations10 = config.locations) === null || _config$locations10 === void 0 ? void 0 : _config$locations10.widgetPanel),
        location_l2: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca10 = config.secondaryLocations) === null || _config$secondaryLoca10 === void 0 ? void 0 : _config$secondaryLoca10.searchBar),
        interaction_description: 'Widget search input, follows debounce behavior'
      });
      if (!hasResults && userInput) {
        payload.metadata = {
          user_input: userInput
        };
      }
      return this.dispatchEvent(config === null || config === void 0 || (_config$names1 = config.names) === null || _config$names1 === void 0 || (_config$names1 = _config$names1.editorOne) === null || _config$names1 === void 0 ? void 0 : _config$names1.widgetPanelSearch, payload);
    }
  }, {
    key: "createWpDashPayload",
    value: function createWpDashPayload() {
      var _config$appTypes$wpDa, _config$appTypes5, _config$locations11;
      var overrides = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var config = this.getConfig();
      return this.createBasePayload(_objectSpread({
        window_name: (_config$appTypes$wpDa = config === null || config === void 0 || (_config$appTypes5 = config.appTypes) === null || _config$appTypes5 === void 0 ? void 0 : _config$appTypes5.wpDash) !== null && _config$appTypes$wpDa !== void 0 ? _config$appTypes$wpDa : 'wpdash',
        target_location: this.toLowerSnake(config === null || config === void 0 || (_config$locations11 = config.locations) === null || _config$locations11 === void 0 ? void 0 : _config$locations11.wpDashAdmin),
        location_l2: ''
      }, overrides));
    }
  }, {
    key: "sendWpDashElementorMenuClick",
    value: function sendWpDashElementorMenuClick() {
      var _config$names10, _config$triggers10, _config$targetTypes11, _config$interactionRe13, _config$secondaryLoca11;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names10 = config.names) === null || _config$names10 === void 0 || (_config$names10 = _config$names10.editorOne) === null || _config$names10 === void 0 ? void 0 : _config$names10.wpDashElementorMenuClick, this.createWpDashPayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers10 = config.triggers) === null || _config$triggers10 === void 0 ? void 0 : _config$triggers10.click),
        target_type: config === null || config === void 0 || (_config$targetTypes11 = config.targetTypes) === null || _config$targetTypes11 === void 0 ? void 0 : _config$targetTypes11.wpDashAdminMenuItem,
        target_name: 'elementor_menu_item',
        interaction_result: config === null || config === void 0 || (_config$interactionRe13 = config.interactionResults) === null || _config$interactionRe13 === void 0 ? void 0 : _config$interactionRe13.elementorSideMenuOpened,
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca11 = config.secondaryLocations) === null || _config$secondaryLoca11 === void 0 ? void 0 : _config$secondaryLoca11.wpDashElementorCoreMenu),
        interaction_description: 'core_user_clicked_elementor_menu_item'
      }));
    }
  }, {
    key: "sendWpDashEditorSubMenuHover",
    value: function sendWpDashEditorSubMenuHover() {
      var _config$names11, _config$triggers11, _config$targetTypes12, _config$interactionRe14, _config$secondaryLoca12;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names11 = config.names) === null || _config$names11 === void 0 || (_config$names11 = _config$names11.editorOne) === null || _config$names11 === void 0 ? void 0 : _config$names11.wpDashEditorSubMenuHover, this.createWpDashPayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers11 = config.triggers) === null || _config$triggers11 === void 0 ? void 0 : _config$triggers11.hover),
        target_type: config === null || config === void 0 || (_config$targetTypes12 = config.targetTypes) === null || _config$targetTypes12 === void 0 ? void 0 : _config$targetTypes12.wpDashEditorMenu,
        target_name: 'wpdash_editor_sub_menu',
        interaction_result: config === null || config === void 0 || (_config$interactionRe14 = config.interactionResults) === null || _config$interactionRe14 === void 0 ? void 0 : _config$interactionRe14.editorSubMenuOpened,
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca12 = config.secondaryLocations) === null || _config$secondaryLoca12 === void 0 ? void 0 : _config$secondaryLoca12.wpDashElementorCoreSubMenu),
        interaction_description: 'core_user_hovered_sub_menu'
      }));
    }
  }, {
    key: "sendWpDashThemeBuilderClick",
    value: function sendWpDashThemeBuilderClick() {
      var _config$names12, _config$triggers12, _config$targetTypes13, _config$interactionRe15, _config$secondaryLoca13;
      var config = this.getConfig();
      return this.dispatchEvent(config === null || config === void 0 || (_config$names12 = config.names) === null || _config$names12 === void 0 || (_config$names12 = _config$names12.editorOne) === null || _config$names12 === void 0 ? void 0 : _config$names12.wpDashThemeBuilderClick, this.createWpDashPayload({
        interaction_type: this.toLowerSnake(config === null || config === void 0 || (_config$triggers12 = config.triggers) === null || _config$triggers12 === void 0 ? void 0 : _config$triggers12.click),
        target_type: config === null || config === void 0 || (_config$targetTypes13 = config.targetTypes) === null || _config$targetTypes13 === void 0 ? void 0 : _config$targetTypes13.wpDashSubMenuItem,
        target_name: 'theme_builder_menu_item',
        interaction_result: config === null || config === void 0 || (_config$interactionRe15 = config.interactionResults) === null || _config$interactionRe15 === void 0 ? void 0 : _config$interactionRe15.themeBuilderPromotionWindow,
        location_l1: this.toLowerSnake(config === null || config === void 0 || (_config$secondaryLoca13 = config.secondaryLocations) === null || _config$secondaryLoca13 === void 0 ? void 0 : _config$secondaryLoca13.wpDashThemeBuilder),
        interaction_description: 'core_user_clicked_theme_builder_menu_item'
      }));
    }
  }]);
}();
var createDebouncedFinderSearch = exports.createDebouncedFinderSearch = function createDebouncedFinderSearch() {
  var delay = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 300;
  return _.debounce(function (resultsCount, searchTerm) {
    EditorOneEventManager.sendFinderSearchInput({
      resultsCount: resultsCount,
      searchTerm: searchTerm
    });
  }, delay);
};
var createDebouncedWidgetPanelSearch = exports.createDebouncedWidgetPanelSearch = function createDebouncedWidgetPanelSearch() {
  var delay = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 2000;
  return _.debounce(function (resultsCount, userInput) {
    EditorOneEventManager.sendWidgetPanelSearch({
      resultsCount: resultsCount,
      userInput: userInput
    });
  }, delay);
};
var _default = exports["default"] = EditorOneEventManager;

/***/ }),

/***/ "../modules/editor-one/assets/js/admin-menu/classes/flyout-interaction-handler.js":
/*!****************************************************************************************!*\
  !*** ../modules/editor-one/assets/js/admin-menu/classes/flyout-interaction-handler.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.FlyoutInteractionHandler = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _menuEventsTracker = __webpack_require__(/*! ./menu-events-tracker */ "../modules/editor-one/assets/js/admin-menu/classes/menu-events-tracker.js");
var FlyoutInteractionHandler = exports.FlyoutInteractionHandler = /*#__PURE__*/function () {
  function FlyoutInteractionHandler() {
    (0, _classCallCheck2.default)(this, FlyoutInteractionHandler);
    this.activeMenu = null;
    this.activeParent = null;
    this.closeTimeout = null;
    this.lastMousePos = null;
    this.exitPoint = null;
    this.mouseMoveHandler = null;
  }
  return (0, _createClass2.default)(FlyoutInteractionHandler, [{
    key: "handle",
    value: function handle() {
      this.setupFlyoutMenus();
      this.setupMobileSupport();
    }
  }, {
    key: "setupFlyoutMenus",
    value: function setupFlyoutMenus() {
      var _this = this;
      var menuItems = document.querySelectorAll('#adminmenu li.elementor-has-flyout');
      menuItems.forEach(function (parentLi) {
        var flyoutMenu = parentLi.querySelector('.elementor-submenu-flyout');
        if (!flyoutMenu) {
          return;
        }
        _this.attachHoverEvents(parentLi, flyoutMenu);
        _this.attachFocusEvents(parentLi, flyoutMenu);
        _this.attachKeyboardEvents(parentLi, flyoutMenu);
      });
    }
  }, {
    key: "attachHoverEvents",
    value: function attachHoverEvents(parentLi, flyoutMenu) {
      var _this2 = this;
      parentLi.addEventListener('mouseenter', function () {
        // If moving to a new parent that is NOT part of the current active tree
        if (_this2.activeMenu && !_this2.activeMenu.contains(parentLi) && _this2.activeMenu !== flyoutMenu) {
          // If we are moving to a sibling or unrelated menu, close the current one immediately
          // UNLESS we are in the safe zone triangle of the parent
          if (!_this2.isCursorInSafeZone()) {
            _this2.hideFlyout(_this2.activeMenu);
          }
        }
        _this2.clearCloseTimeout();
        _this2.showFlyout(parentLi, flyoutMenu);
      });
      parentLi.addEventListener('mouseleave', function (event) {
        _this2.exitPoint = {
          x: event.clientX,
          y: event.clientY
        };
        _this2.scheduleClose(parentLi, flyoutMenu);
      });
      flyoutMenu.addEventListener('mouseenter', function () {
        _this2.clearCloseTimeout();
        _this2.stopMouseTracking();
      });
      flyoutMenu.addEventListener('mouseleave', function (event) {
        _this2.exitPoint = {
          x: event.clientX,
          y: event.clientY
        };
        _this2.scheduleClose(parentLi, flyoutMenu);
      });
    }
  }, {
    key: "attachFocusEvents",
    value: function attachFocusEvents(parentLi, flyoutMenu) {
      var _this3 = this;
      var parentLink = parentLi.querySelector(':scope > a');
      if (parentLink) {
        parentLink.addEventListener('focus', function () {
          _this3.showFlyout(parentLi, flyoutMenu);
        });
      }
      flyoutMenu.addEventListener('focusout', function (event) {
        if (!parentLi.contains(event.relatedTarget)) {
          _this3.hideFlyout(flyoutMenu);
        }
      });
    }
  }, {
    key: "attachKeyboardEvents",
    value: function attachKeyboardEvents(parentLi, flyoutMenu) {
      var _this4 = this;
      parentLi.addEventListener('keydown', function (event) {
        _this4.handleKeyNavigation(event, parentLi, flyoutMenu);
      });
    }
  }, {
    key: "showFlyout",
    value: function showFlyout(parentLi, flyoutMenu) {
      var wasAlreadyVisible = flyoutMenu.classList.contains('elementor-submenu-flyout-visible');
      if (this.activeMenu && this.activeMenu !== flyoutMenu) {
        this.hideFlyout(this.activeMenu);
      }
      this.exitPoint = null;
      this.positionFlyout(parentLi, flyoutMenu);
      flyoutMenu.classList.add('elementor-submenu-flyout-visible');
      this.activeMenu = flyoutMenu;
      this.activeParent = parentLi;
      if (!wasAlreadyVisible) {
        _menuEventsTracker.MenuEventsTracker.trackEditorSubMenuOpened();
      }
    }
  }, {
    key: "hideFlyout",
    value: function hideFlyout(flyoutMenu) {
      flyoutMenu.classList.remove('elementor-submenu-flyout-visible');
      if (this.activeMenu === flyoutMenu) {
        this.activeMenu = null;
        this.activeParent = null;
        this.exitPoint = null;
        this.stopMouseTracking();
      }
    }
  }, {
    key: "scheduleClose",
    value: function scheduleClose(parentLi, flyoutMenu) {
      var _this5 = this;
      this.clearCloseTimeout();
      this.startMouseTracking(parentLi, flyoutMenu);
      this.closeTimeout = setTimeout(function () {
        _this5.checkAndClose(flyoutMenu);
      }, 300);
    }
  }, {
    key: "checkAndClose",
    value: function checkAndClose(flyoutMenu) {
      var _this6 = this;
      if (!this.activeMenu) {
        return;
      }
      if (!this.isCursorInSafeZone()) {
        this.hideFlyout(flyoutMenu);
      } else {
        this.closeTimeout = setTimeout(function () {
          _this6.checkAndClose(flyoutMenu);
        }, 300);
      }
    }
  }, {
    key: "clearCloseTimeout",
    value: function clearCloseTimeout() {
      if (this.closeTimeout) {
        clearTimeout(this.closeTimeout);
        this.closeTimeout = null;
      }
    }
  }, {
    key: "startMouseTracking",
    value: function startMouseTracking() {
      var _this7 = this;
      this.stopMouseTracking();
      this.mouseMoveHandler = function (event) {
        _this7.lastMousePos = {
          x: event.clientX,
          y: event.clientY
        };
      };
      document.addEventListener('mousemove', this.mouseMoveHandler);
    }
  }, {
    key: "stopMouseTracking",
    value: function stopMouseTracking() {
      if (this.mouseMoveHandler) {
        document.removeEventListener('mousemove', this.mouseMoveHandler);
        this.mouseMoveHandler = null;
      }
      this.lastMousePos = null;
    }
  }, {
    key: "isCursorInSafeZone",
    value: function isCursorInSafeZone() {
      if (!this.lastMousePos || !this.activeMenu || !this.activeParent) {
        return false;
      }
      var cursor = this.lastMousePos;
      var parentRect = this.activeParent.getBoundingClientRect();
      if (this.isPointInRect(cursor, parentRect)) {
        return true;
      }
      var flyoutRect = this.activeMenu.getBoundingClientRect();
      if (this.isPointInRect(cursor, flyoutRect)) {
        return true;
      }
      return this.isPointInTriangle(cursor, parentRect, flyoutRect);
    }
  }, {
    key: "isPointInRect",
    value: function isPointInRect(point, rect) {
      return point.x >= rect.left && point.x <= rect.right && point.y >= rect.top && point.y <= rect.bottom;
    }
  }, {
    key: "isPointInTriangle",
    value: function isPointInTriangle(cursor, parentRect, flyoutRect) {
      var exitX = this.exitPoint ? this.exitPoint.x : parentRect.right;
      var distParent = Math.abs(exitX - parentRect.right);
      var distFlyout = Math.abs(exitX - flyoutRect.left);
      var triangleApex, baseTop, baseBottom;

      // Determine direction: Moving towards Flyout (default) or towards Parent (backwards)
      if (distParent < distFlyout) {
        // Moving towards Flyout
        triangleApex = this.exitPoint || {
          x: parentRect.right,
          y: parentRect.top + parentRect.height / 2
        };
        baseTop = {
          x: flyoutRect.left,
          y: flyoutRect.top - 100
        };
        baseBottom = {
          x: flyoutRect.left,
          y: flyoutRect.bottom + 100
        };
      } else {
        // Moving towards Parent
        triangleApex = this.exitPoint || {
          x: flyoutRect.left,
          y: flyoutRect.top + flyoutRect.height / 2
        };
        baseTop = {
          x: parentRect.right,
          y: parentRect.top - 100
        };
        baseBottom = {
          x: parentRect.right,
          y: parentRect.bottom + 100
        };
      }
      return this.pointInTriangle(cursor, triangleApex, baseTop, baseBottom);
    }
  }, {
    key: "pointInTriangle",
    value: function pointInTriangle(p, v1, v2, v3) {
      var sign = function sign(p1, p2, p3) {
        return (p1.x - p3.x) * (p2.y - p3.y) - (p2.x - p3.x) * (p1.y - p3.y);
      };
      var d1 = sign(p, v1, v2);
      var d2 = sign(p, v2, v3);
      var d3 = sign(p, v3, v1);
      var hasNeg = 0 > d1 || 0 > d2 || 0 > d3;
      var hasPos = 0 < d1 || 0 < d2 || 0 < d3;
      return !(hasNeg && hasPos);
    }
  }, {
    key: "positionFlyout",
    value: function positionFlyout(parentLi, flyoutMenu) {
      var windowHeight = window.innerHeight;
      var flyoutHeight = flyoutMenu.offsetHeight;
      var parentRect = parentLi.getBoundingClientRect();
      var relativeTop = parentRect.top;
      if (relativeTop + flyoutHeight > windowHeight) {
        var newTop = windowHeight - flyoutHeight - relativeTop;
        if (newTop < -relativeTop) {
          newTop = -relativeTop + 10;
        }
        flyoutMenu.style.top = newTop + 'px';
      } else {
        delete flyoutMenu.style.top;
      }
    }
  }, {
    key: "handleKeyNavigation",
    value: function handleKeyNavigation(event, parentLi, flyoutMenu) {
      var allLinks = flyoutMenu.querySelectorAll('a');
      var focusedLink = flyoutMenu.querySelector('a:focus');
      var currentIndex = Array.from(allLinks).indexOf(focusedLink);
      var isVisible = flyoutMenu.classList.contains('elementor-submenu-flyout-visible');
      switch (event.key) {
        case 'ArrowRight':
          if (!isVisible) {
            var _allLinks$;
            event.preventDefault();
            this.showFlyout(parentLi, flyoutMenu);
            (_allLinks$ = allLinks[0]) === null || _allLinks$ === void 0 || _allLinks$.focus();
          }
          break;
        case 'ArrowLeft':
          if (isVisible) {
            var _parentLi$querySelect;
            event.preventDefault();
            this.hideFlyout(flyoutMenu);
            (_parentLi$querySelect = parentLi.querySelector(':scope > a')) === null || _parentLi$querySelect === void 0 || _parentLi$querySelect.focus();
          }
          break;
        case 'ArrowDown':
          if (isVisible && currentIndex >= 0) {
            var _allLinks$nextIndex;
            event.preventDefault();
            var nextIndex = (currentIndex + 1) % allLinks.length;
            (_allLinks$nextIndex = allLinks[nextIndex]) === null || _allLinks$nextIndex === void 0 || _allLinks$nextIndex.focus();
          }
          break;
        case 'ArrowUp':
          if (isVisible && currentIndex >= 0) {
            var _allLinks$prevIndex;
            event.preventDefault();
            var prevIndex = (currentIndex - 1 + allLinks.length) % allLinks.length;
            (_allLinks$prevIndex = allLinks[prevIndex]) === null || _allLinks$prevIndex === void 0 || _allLinks$prevIndex.focus();
          }
          break;
        case 'Escape':
          if (isVisible) {
            var _parentLi$querySelect2;
            event.preventDefault();
            this.hideFlyout(flyoutMenu);
            (_parentLi$querySelect2 = parentLi.querySelector(':scope > a')) === null || _parentLi$querySelect2 === void 0 || _parentLi$querySelect2.focus();
          }
          break;
      }
    }
  }, {
    key: "setupMobileSupport",
    value: function setupMobileSupport() {
      var _this8 = this;
      if (window.innerWidth > 782) {
        return;
      }
      var menuLinks = document.querySelectorAll('#adminmenu li.elementor-has-flyout > a');
      menuLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
          _this8.handleMobileClick(event, link);
        });
      });
      document.addEventListener('click', function (event) {
        _this8.handleDocumentClick(event);
      });
    }
  }, {
    key: "handleMobileClick",
    value: function handleMobileClick(event, link) {
      var parentLi = link.parentElement;
      var flyoutMenu = parentLi.querySelector('.elementor-submenu-flyout');
      if (!flyoutMenu) {
        return;
      }
      if (parentLi.classList.contains('elementor-flyout-open')) {
        return;
      }
      event.preventDefault();
      document.querySelectorAll('#adminmenu li.elementor-has-flyout').forEach(function (item) {
        item.classList.remove('elementor-flyout-open');
      });
      parentLi.classList.add('elementor-flyout-open');
    }
  }, {
    key: "handleDocumentClick",
    value: function handleDocumentClick(event) {
      if (!event.target.closest('#adminmenu li.elementor-has-flyout')) {
        document.querySelectorAll('#adminmenu li.elementor-has-flyout').forEach(function (item) {
          item.classList.remove('elementor-flyout-open');
        });
      }
    }
  }]);
}();

/***/ }),

/***/ "../modules/editor-one/assets/js/admin-menu/classes/flyout-menu-renderer.js":
/*!**********************************************************************************!*\
  !*** ../modules/editor-one/assets/js/admin-menu/classes/flyout-menu-renderer.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.FlyoutMenuRenderer = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var FlyoutMenuRenderer = exports.FlyoutMenuRenderer = /*#__PURE__*/function () {
  function FlyoutMenuRenderer(config) {
    (0, _classCallCheck2.default)(this, FlyoutMenuRenderer);
    this.config = config;
  }
  return (0, _createClass2.default)(FlyoutMenuRenderer, [{
    key: "render",
    value: function render() {
      var editorFlyout = this.config.editorFlyout;
      if (!editorFlyout || !editorFlyout.items || !editorFlyout.items.length) {
        return false;
      }
      var editorLi = this.findEditorInMenu("#toplevel_page_elementor-home");
      if (!editorLi) {
        return false;
      }
      editorLi.classList.add('elementor-has-flyout');
      var editorFlyoutUl = document.createElement('ul');
      editorFlyoutUl.className = 'elementor-submenu-flyout elementor-level-3';
      editorFlyout.items.forEach(function (item) {
        if (item.has_divider_before) {
          var dividerLi = document.createElement('li');
          dividerLi.className = 'elementor-flyout-divider';
          dividerLi.setAttribute('role', 'separator');
          editorFlyoutUl.appendChild(dividerLi);
        }
        var li = document.createElement('li');
        li.setAttribute('data-group-id', item.group_id || '');
        li.setAttribute('data-slug', item.slug || '');
        var a = document.createElement('a');
        a.href = item.url;
        a.textContent = item.label;
        li.appendChild(a);
        editorFlyoutUl.appendChild(li);
      });
      editorLi.appendChild(editorFlyoutUl);
      return true;
    }
  }, {
    key: "findEditorInMenu",
    value: function findEditorInMenu(menuSelector) {
      var menuItem = document.querySelector(menuSelector);
      if (!menuItem) {
        return null;
      }
      var submenu = menuItem.querySelector('.wp-submenu');
      if (!submenu) {
        return null;
      }
      var editorItem = submenu.querySelector('a[href$="page=elementor"]');
      if (!editorItem) {
        return null;
      }
      return editorItem.closest('li');
    }
  }]);
}();

/***/ }),

/***/ "../modules/editor-one/assets/js/admin-menu/classes/menu-events-tracker.js":
/*!*********************************************************************************!*\
  !*** ../modules/editor-one/assets/js/admin-menu/classes/menu-events-tracker.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.MenuEventsTracker = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _editorOneEvents = __webpack_require__(/*! elementor-editor-utils/editor-one-events */ "../assets/dev/js/editor/utils/editor-one-events.js");
var SELECTORS = {
  elementorMenuLink: '#toplevel_page_elementor-home > a.menu-top',
  themeBuilderItem: '[data-slug="elementor-theme-builder"] a, .wp-submenu a[href*="page=elementor-theme-builder"]'
};
var MenuEventsTracker = exports.MenuEventsTracker = /*#__PURE__*/function () {
  function MenuEventsTracker() {
    (0, _classCallCheck2.default)(this, MenuEventsTracker);
  }
  return (0, _createClass2.default)(MenuEventsTracker, null, [{
    key: "attach",
    value: function attach() {
      this.attachElementorMenuClick();
      this.attachThemeBuilderClick();
    }
  }, {
    key: "attachElementorMenuClick",
    value: function attachElementorMenuClick() {
      var menuLink = document.querySelector(SELECTORS.elementorMenuLink);
      if (!menuLink) {
        return;
      }
      menuLink.addEventListener('click', function () {
        _editorOneEvents.EditorOneEventManager.sendWpDashElementorMenuClick();
      });
    }
  }, {
    key: "attachThemeBuilderClick",
    value: function attachThemeBuilderClick() {
      document.addEventListener('click', function (event) {
        var link = event.target.closest(SELECTORS.themeBuilderItem);
        if (link) {
          _editorOneEvents.EditorOneEventManager.sendWpDashThemeBuilderClick();
        }
      }, true);
    }
  }, {
    key: "trackEditorSubMenuOpened",
    value: function trackEditorSubMenuOpened() {
      _editorOneEvents.EditorOneEventManager.sendWpDashEditorSubMenuHover();
    }
  }]);
}();

/***/ }),

/***/ "../modules/editor-one/assets/js/admin-menu/classes/sidebar-menu-handler.js":
/*!**********************************************************************************!*\
  !*** ../modules/editor-one/assets/js/admin-menu/classes/sidebar-menu-handler.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.SidebarMenuHandler = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var SidebarMenuHandler = exports.SidebarMenuHandler = /*#__PURE__*/function () {
  function SidebarMenuHandler() {
    (0, _classCallCheck2.default)(this, SidebarMenuHandler);
    this.elementorHomeMenu = this.findElementorHomeMenu();
    if (this.elementorHomeMenu) {
      this.deactivateOtherMenus();
      this.activateElementorMenu();
      this.highlightSubmenu();
    }
  }
  return (0, _createClass2.default)(SidebarMenuHandler, [{
    key: "findElementorHomeMenu",
    value: function findElementorHomeMenu() {
      return document.querySelector('#toplevel_page_elementor-home');
    }
  }, {
    key: "deactivateOtherMenus",
    value: function deactivateOtherMenus() {
      var _this = this;
      document.querySelectorAll('#adminmenu li.wp-has-current-submenu').forEach(function (item) {
        if (item !== _this.elementorHomeMenu) {
          item.classList.remove('wp-has-current-submenu', 'wp-menu-open', 'selected');
          item.classList.add('wp-not-current-submenu');
          var link = item.querySelector(':scope > a');
          if (link) {
            link.classList.remove('wp-has-current-submenu', 'wp-menu-open', 'current');
          }
        }
      });
    }
  }, {
    key: "activateElementorMenu",
    value: function activateElementorMenu() {
      this.elementorHomeMenu.classList.remove('wp-not-current-submenu');
      this.elementorHomeMenu.classList.add('wp-has-current-submenu', 'wp-menu-open', 'selected');
      var elementorLink = this.elementorHomeMenu.querySelector(':scope > a.menu-top');
      if (elementorLink) {
        elementorLink.classList.add('wp-has-current-submenu', 'wp-menu-open');
      }
    }
  }, {
    key: "highlightSubmenu",
    value: function highlightSubmenu() {
      var currentUrl = new URL(window.location.href);
      var searchParams = currentUrl.searchParams;
      var page = searchParams.get('page');
      var targetSlug = 'elementor';
      if ('elementor' === page || 'elementor-home' === page) {
        targetSlug = 'elementor';
      } else if ('e-form-submissions' === page) {
        targetSlug = 'e-form-submissions';
      } else if ('elementor-theme-builder' === page) {
        targetSlug = 'elementor-theme-builder';
      }
      var submenuItems = this.elementorHomeMenu.querySelectorAll('.wp-submenu li');
      submenuItems.forEach(function (item) {
        var link = item.querySelector('a');
        if (!link) {
          return;
        }
        item.classList.remove('current');
        link.classList.remove('current');
        link.setAttribute('aria-current', '');
        var linkUrl = new URL(link.href, window.location.origin);
        var linkPage = linkUrl.searchParams.get('page');
        if (linkPage === targetSlug) {
          item.classList.add('current');
          link.classList.add('current');
          link.setAttribute('aria-current', 'page');
        }
      });
    }
  }]);
}();

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
/*!*********************************************************!*\
  !*** ../modules/editor-one/assets/js/admin-menu/app.js ***!
  \*********************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _flyoutMenuRenderer = __webpack_require__(/*! ./classes/flyout-menu-renderer */ "../modules/editor-one/assets/js/admin-menu/classes/flyout-menu-renderer.js");
var _sidebarMenuHandler = __webpack_require__(/*! ./classes/sidebar-menu-handler */ "../modules/editor-one/assets/js/admin-menu/classes/sidebar-menu-handler.js");
var _flyoutInteractionHandler = __webpack_require__(/*! ./classes/flyout-interaction-handler */ "../modules/editor-one/assets/js/admin-menu/classes/flyout-interaction-handler.js");
var _menuEventsTracker = __webpack_require__(/*! ./classes/menu-events-tracker */ "../modules/editor-one/assets/js/admin-menu/classes/menu-events-tracker.js");
var EditorOneMenu = /*#__PURE__*/function () {
  function EditorOneMenu() {
    var _window;
    (0, _classCallCheck2.default)(this, EditorOneMenu);
    this.config = ((_window = window) === null || _window === void 0 ? void 0 : _window.editorOneMenuConfig) || {};
  }
  return (0, _createClass2.default)(EditorOneMenu, [{
    key: "init",
    value: function init() {
      _menuEventsTracker.MenuEventsTracker.attach();
      if (this.isSidebarNavigationActive()) {
        new _sidebarMenuHandler.SidebarMenuHandler();
        return;
      }
      this.buildFlyoutMenus();
    }
  }, {
    key: "isSidebarNavigationActive",
    value: function isSidebarNavigationActive() {
      return document.body.classList.contains('e-has-sidebar-navigation');
    }
  }, {
    key: "buildFlyoutMenus",
    value: function buildFlyoutMenus() {
      var renderer = new _flyoutMenuRenderer.FlyoutMenuRenderer(this.config);
      if (renderer.render()) {
        new _flyoutInteractionHandler.FlyoutInteractionHandler().handle();
      }
    }
  }]);
}();
var initEditorOneMenu = function initEditorOneMenu() {
  var editorOneMenu = new EditorOneMenu();
  editorOneMenu.init();
};
if ('loading' === document.readyState) {
  document.addEventListener('DOMContentLoaded', initEditorOneMenu);
} else {
  initEditorOneMenu();
}
})();

/******/ })()
;
//# sourceMappingURL=editor-one-menu.js.map
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../app/modules/import-export-customization/assets/js/shared/registry/base.js":
/*!************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/registry/base.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.BaseRegistry = void 0;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _excluded = ["children"];
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var BaseRegistry = exports.BaseRegistry = /*#__PURE__*/function () {
  function BaseRegistry() {
    (0, _classCallCheck2.default)(this, BaseRegistry);
    this.sections = new Map();
  }
  return (0, _createClass2.default)(BaseRegistry, [{
    key: "register",
    value: function register(section) {
      var _this = this;
      if (!section.key || !section.title) {
        throw new Error('Template type must have key and title');
      }
      var existingSection = this.get(section.key);
      var formattedSection = existingSection || this.formatSection(section);
      if (section.children) {
        // If existing section has children, merge them with new children
        if (formattedSection.children) {
          var existingChildrenMap = new Map(formattedSection.children.map(function (child) {
            return [child.key, child];
          }));

          // Override existing children with new ones and add new children
          section.children.forEach(function (childSection) {
            var formattedChild = _this.formatSection(childSection);
            existingChildrenMap.set(childSection.key, formattedChild);
          });
          formattedSection.children = Array.from(existingChildrenMap.values());
        } else {
          formattedSection.children = section.children.map(function (childSection) {
            return _this.formatSection(childSection);
          });
        }
      }
      this.sections.set(section.key, formattedSection);
    }
  }, {
    key: "formatSection",
    value: function formatSection(_ref) {
      var children = _ref.children,
        section = (0, _objectWithoutProperties2.default)(_ref, _excluded);
      return _objectSpread({
        key: section.key,
        title: section.title,
        description: section.description || '',
        useParentDefault: section.useParentDefault !== false,
        getInitialState: section.getInitialState || null,
        component: section.component || null,
        order: section.order || 10,
        isAvailable: section.isAvailable || function () {
          return true;
        }
      }, section);
    }
  }, {
    key: "getAll",
    value: function getAll() {
      return Array.from(this.sections.values()).filter(function (type) {
        return type.isAvailable();
      }).map(function (type) {
        if (type.children) {
          return _objectSpread(_objectSpread({}, type), {}, {
            children: (0, _toConsumableArray2.default)(type.children).sort(function (a, b) {
              return a.order - b.order;
            })
          });
        }
        return type;
      }).sort(function (a, b) {
        return a.order - b.order;
      });
    }
  }, {
    key: "get",
    value: function get(key) {
      return this.sections.get(key);
    }
  }]);
}();

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs.js":
/*!*****************************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs.js ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.customizationDialogsRegistry = void 0;
var _base = __webpack_require__(/*! ./base */ "../app/modules/import-export-customization/assets/js/shared/registry/base.js");
var customizationDialogsRegistry = exports.customizationDialogsRegistry = new _base.BaseRegistry();

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers.js":
/*!******************************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers.js ***!
  \******************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.createGetInitialState = createGetInitialState;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function createGetInitialState(exportGroup) {
  var additionalProps = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return function (data, parentInitialState) {
    var isEnabled = parentInitialState;
    var isImport = data.hasOwnProperty('uploadedData');
    if (isImport) {
      var _elementorAppConfig;
      isEnabled = false;
      var templates = data.uploadedData.manifest.templates;
      var exportGroups = ((_elementorAppConfig = elementorAppConfig) === null || _elementorAppConfig === void 0 || (_elementorAppConfig = _elementorAppConfig['import-export-customization']) === null || _elementorAppConfig === void 0 ? void 0 : _elementorAppConfig.exportGroups) || {};
      for (var templateId in templates) {
        var template = templates[templateId];
        var templateExportGroup = exportGroups[template.doc_type];
        if (templateExportGroup === exportGroup) {
          isEnabled = true;
          break;
        }
      }
    }
    return _objectSpread({
      enabled: isEnabled
    }, additionalProps);
  };
}

/***/ }),

/***/ "../assets/dev/js/editor/utils/is-instanceof.js":
/*!******************************************************!*\
  !*** ../assets/dev/js/editor/utils/is-instanceof.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
/**
 * Some FileAPI objects such as FileList, DataTransferItem and DataTransferItemList has inconsistency with the retrieved
 * object (from events, etc.) and the actual JavaScript object so a regular instanceof doesn't work. This function can
 * check whether it's instanceof by using the objects constructor and prototype names.
 *
 * @param  object
 * @param  constructors
 * @return {boolean}
 */
var _default = exports["default"] = function _default(object, constructors) {
  constructors = Array.isArray(constructors) ? constructors : [constructors];
  var _iterator = _createForOfIteratorHelper(constructors),
    _step;
  try {
    for (_iterator.s(); !(_step = _iterator.n()).done;) {
      var constructor = _step.value;
      if (object.constructor.name === constructor.prototype[Symbol.toStringTag]) {
        return true;
      }
    }
  } catch (err) {
    _iterator.e(err);
  } finally {
    _iterator.f();
  }
  return false;
};

/***/ }),

/***/ "../assets/dev/js/modules/imports/args-object.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/modules/imports/args-object.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _instanceType = _interopRequireDefault(__webpack_require__(/*! ./instance-type */ "../assets/dev/js/modules/imports/instance-type.js"));
var _isInstanceof = _interopRequireDefault(__webpack_require__(/*! ../../editor/utils/is-instanceof */ "../assets/dev/js/editor/utils/is-instanceof.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ArgsObject = exports["default"] = /*#__PURE__*/function (_InstanceType) {
  /**
   * Function constructor().
   *
   * Create ArgsObject.
   *
   * @param {{}} args
   */
  function ArgsObject(args) {
    var _this;
    (0, _classCallCheck2.default)(this, ArgsObject);
    _this = _callSuper(this, ArgsObject);
    _this.args = args;
    return _this;
  }

  /**
   * Function requireArgument().
   *
   * Validate property in args.
   *
   * @param {string} property
   * @param {{}}     args
   *
   * @throws {Error}
   */
  (0, _inherits2.default)(ArgsObject, _InstanceType);
  return (0, _createClass2.default)(ArgsObject, [{
    key: "requireArgument",
    value: function requireArgument(property) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.args;
      if (!Object.prototype.hasOwnProperty.call(args, property)) {
        throw Error("".concat(property, " is required."));
      }
    }

    /**
     * Function requireArgumentType().
     *
     * Validate property in args using `type === typeof(args.whatever)`.
     *
     * @param {string} property
     * @param {string} type
     * @param {{}}     args
     *
     * @throws {Error}
     */
  }, {
    key: "requireArgumentType",
    value: function requireArgumentType(property, type) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.args;
      this.requireArgument(property, args);
      if ((0, _typeof2.default)(args[property]) !== type) {
        throw Error("".concat(property, " invalid type: ").concat(type, "."));
      }
    }

    /**
     * Function requireArgumentInstance().
     *
     * Validate property in args using `args.whatever instanceof instance`.
     *
     * @param {string} property
     * @param {*}      instance
     * @param {{}}     args
     *
     * @throws {Error}
     */
  }, {
    key: "requireArgumentInstance",
    value: function requireArgumentInstance(property, instance) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.args;
      this.requireArgument(property, args);
      if (!(args[property] instanceof instance) && !(0, _isInstanceof.default)(args[property], instance)) {
        throw Error("".concat(property, " invalid instance."));
      }
    }

    /**
     * Function requireArgumentConstructor().
     *
     * Validate property in args using `type === args.whatever.constructor`.
     *
     * @param {string} property
     * @param {*}      type
     * @param {{}}     args
     *
     * @throws {Error}
     */
  }, {
    key: "requireArgumentConstructor",
    value: function requireArgumentConstructor(property, type) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.args;
      this.requireArgument(property, args);

      // Note: Converting the constructor to string in order to avoid equation issues
      // due to different memory addresses between iframes (window.Object !== window.top.Object).
      if (args[property].constructor.toString() !== type.prototype.constructor.toString()) {
        throw Error("".concat(property, " invalid constructor type."));
      }
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'ArgsObject';
    }
  }]);
}(_instanceType.default);

/***/ }),

/***/ "../assets/dev/js/modules/imports/force-method-implementation.js":
/*!***********************************************************************!*\
  !*** ../assets/dev/js/modules/imports/force-method-implementation.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ForceMethodImplementation = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
// TODO: Wrong location used as `elementorModules.ForceMethodImplementation(); should be` `elementorUtils.forceMethodImplementation()`;
var ForceMethodImplementation = exports.ForceMethodImplementation = /*#__PURE__*/function (_Error) {
  function ForceMethodImplementation() {
    var _this;
    var info = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    (0, _classCallCheck2.default)(this, ForceMethodImplementation);
    _this = _callSuper(this, ForceMethodImplementation, ["".concat(info.isStatic ? 'static ' : '').concat(info.fullName, "() should be implemented, please provide '").concat(info.functionName || info.fullName, "' functionality."), args]);

    // Allow to pass custom properties to the error.
    if (Object.keys(args).length) {
      // eslint-disable-next-line no-console
      console.error(args);
    }
    Error.captureStackTrace(_this, ForceMethodImplementation);
    return _this;
  }
  (0, _inherits2.default)(ForceMethodImplementation, _Error);
  return (0, _createClass2.default)(ForceMethodImplementation);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));
var _default = exports["default"] = function _default(args) {
  var stack = Error().stack,
    caller = stack.split('\n')[2].trim(),
    callerName = caller.startsWith('at new') ? 'constructor' : caller.split(' ')[1],
    info = {};
  info.functionName = callerName;
  info.fullName = callerName;
  if (info.functionName.includes('.')) {
    var parts = info.functionName.split('.');
    info.className = parts[0];
    info.functionName = parts[1];
  } else {
    info.isStatic = true;
  }
  throw new ForceMethodImplementation(info, args);
};

/***/ }),

/***/ "../assets/dev/js/modules/imports/instance-type.js":
/*!*********************************************************!*\
  !*** ../assets/dev/js/modules/imports/instance-type.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var InstanceType = exports["default"] = /*#__PURE__*/function () {
  function InstanceType() {
    var _this = this;
    (0, _classCallCheck2.default)(this, InstanceType);
    // Since anonymous classes sometimes do not get validated by babel, do it manually.
    var target = this instanceof InstanceType ? this.constructor : void 0;
    var prototypes = [];
    while (target.__proto__ && target.__proto__.name) {
      prototypes.push(target.__proto__);
      target = target.__proto__;
    }
    prototypes.reverse().forEach(function (proto) {
      return _this instanceof proto;
    });
  }
  return (0, _createClass2.default)(InstanceType, null, [{
    key: Symbol.hasInstance,
    value: function value(target) {
      /**
       * This is function extending being called each time JS uses instanceOf, since babel use it each time it create new class
       * its give's opportunity to mange capabilities of instanceOf operator.
       * saving current class each time will give option later to handle instanceOf manually.
       */
      var result = _superPropGet(InstanceType, Symbol.hasInstance, this, 2)([target]);

      // Act normal when validate a class, which does not have instance type.
      if (target && !target.constructor.getInstanceType) {
        return result;
      }
      if (target) {
        if (!target.instanceTypes) {
          target.instanceTypes = [];
        }
        if (!result) {
          if (this.getInstanceType() === target.constructor.getInstanceType()) {
            result = true;
          }
        }
        if (result) {
          var name = this.getInstanceType === InstanceType.getInstanceType ? 'BaseInstanceType' : this.getInstanceType();
          if (-1 === target.instanceTypes.indexOf(name)) {
            target.instanceTypes.push(name);
          }
        }
      }
      if (!result && target) {
        // Check if the given 'target', is instance of known types.
        result = target.instanceTypes && Array.isArray(target.instanceTypes) && -1 !== target.instanceTypes.indexOf(this.getInstanceType());
      }
      return result;
    }
  }, {
    key: "getInstanceType",
    value: function getInstanceType() {
      elementorModules.ForceMethodImplementation();
    }
  }]);
}();

/***/ }),

/***/ "../assets/dev/js/modules/imports/module.js":
/*!**************************************************!*\
  !*** ../assets/dev/js/modules/imports/module.js ***!
  \**************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var Module = function Module() {
  var $ = jQuery,
    instanceParams = arguments,
    self = this,
    events = {};
  var settings;
  var ensureClosureMethods = function ensureClosureMethods() {
    $.each(self, function (methodName) {
      var oldMethod = self[methodName];
      if ('function' !== typeof oldMethod) {
        return;
      }
      self[methodName] = function () {
        return oldMethod.apply(self, arguments);
      };
    });
  };
  var initSettings = function initSettings() {
    settings = self.getDefaultSettings();
    var instanceSettings = instanceParams[0];
    if (instanceSettings) {
      $.extend(true, settings, instanceSettings);
    }
  };
  var init = function init() {
    self.__construct.apply(self, instanceParams);
    ensureClosureMethods();
    initSettings();
    self.trigger('init');
  };
  this.getItems = function (items, itemKey) {
    if (itemKey) {
      var keyStack = itemKey.split('.'),
        currentKey = keyStack.splice(0, 1);
      if (!keyStack.length) {
        return items[currentKey];
      }
      if (!items[currentKey]) {
        return;
      }
      return this.getItems(items[currentKey], keyStack.join('.'));
    }
    return items;
  };
  this.getSettings = function (setting) {
    return this.getItems(settings, setting);
  };
  this.setSettings = function (settingKey, value, settingsContainer) {
    if (!settingsContainer) {
      settingsContainer = settings;
    }
    if ('object' === (0, _typeof2.default)(settingKey)) {
      $.extend(settingsContainer, settingKey);
      return self;
    }
    var keyStack = settingKey.split('.'),
      currentKey = keyStack.splice(0, 1);
    if (!keyStack.length) {
      settingsContainer[currentKey] = value;
      return self;
    }
    if (!settingsContainer[currentKey]) {
      settingsContainer[currentKey] = {};
    }
    return self.setSettings(keyStack.join('.'), value, settingsContainer[currentKey]);
  };
  this.getErrorMessage = function (type, functionName) {
    var message;
    switch (type) {
      case 'forceMethodImplementation':
        message = "The method '".concat(functionName, "' must to be implemented in the inheritor child.");
        break;
      default:
        message = 'An error occurs';
    }
    return message;
  };

  // TODO: This function should be deleted ?.
  this.forceMethodImplementation = function (functionName) {
    throw new Error(this.getErrorMessage('forceMethodImplementation', functionName));
  };
  this.on = function (eventName, callback) {
    if ('object' === (0, _typeof2.default)(eventName)) {
      $.each(eventName, function (singleEventName) {
        self.on(singleEventName, this);
      });
      return self;
    }
    var eventNames = eventName.split(' ');
    eventNames.forEach(function (singleEventName) {
      if (!events[singleEventName]) {
        events[singleEventName] = [];
      }
      events[singleEventName].push(callback);
    });
    return self;
  };
  this.off = function (eventName, callback) {
    if (!events[eventName]) {
      return self;
    }
    if (!callback) {
      delete events[eventName];
      return self;
    }
    var callbackIndex = events[eventName].indexOf(callback);
    if (-1 !== callbackIndex) {
      delete events[eventName][callbackIndex];

      // Reset array index (for next off on same event).
      events[eventName] = events[eventName].filter(function (val) {
        return val;
      });
    }
    return self;
  };
  this.trigger = function (eventName) {
    var methodName = 'on' + eventName[0].toUpperCase() + eventName.slice(1),
      params = Array.prototype.slice.call(arguments, 1);
    if (self[methodName]) {
      self[methodName].apply(self, params);
    }
    var callbacks = events[eventName];
    if (!callbacks) {
      return self;
    }
    $.each(callbacks, function (index, callback) {
      callback.apply(self, params);
    });
    return self;
  };
  init();
};
Module.prototype.__construct = function () {};
Module.prototype.getDefaultSettings = function () {
  return {};
};
Module.prototype.getConstructorID = function () {
  return this.constructor.name;
};
Module.extend = function (properties) {
  var $ = jQuery,
    parent = this;
  var child = function child() {
    return parent.apply(this, arguments);
  };
  $.extend(child, parent);
  child.prototype = Object.create($.extend({}, parent.prototype, properties));
  child.prototype.constructor = child;
  child.__super__ = parent.prototype;
  return child;
};
module.exports = Module;

/***/ }),

/***/ "../assets/dev/js/modules/imports/utils/masonry.js":
/*!*********************************************************!*\
  !*** ../assets/dev/js/modules/imports/utils/masonry.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _viewModule = _interopRequireDefault(__webpack_require__(/*! ../view-module */ "../assets/dev/js/modules/imports/view-module.js"));
var _default = exports["default"] = _viewModule.default.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      container: null,
      items: null,
      columnsCount: 3,
      verticalSpaceBetween: 30
    };
  },
  getDefaultElements: function getDefaultElements() {
    return {
      $container: jQuery(this.getSettings('container')),
      $items: jQuery(this.getSettings('items'))
    };
  },
  run: function run() {
    var heights = [],
      distanceFromTop = this.elements.$container.position().top,
      settings = this.getSettings(),
      columnsCount = settings.columnsCount;
    distanceFromTop += parseInt(this.elements.$container.css('margin-top'), 10);
    this.elements.$items.each(function (index) {
      var row = Math.floor(index / columnsCount),
        $item = jQuery(this),
        itemHeight = $item[0].getBoundingClientRect().height + settings.verticalSpaceBetween;
      if (row) {
        var itemPosition = $item.position(),
          indexAtRow = index % columnsCount,
          pullHeight = itemPosition.top - distanceFromTop - heights[indexAtRow];
        pullHeight -= parseInt($item.css('margin-top'), 10);
        pullHeight *= -1;
        $item.css('margin-top', pullHeight + 'px');
        heights[indexAtRow] += itemHeight;
      } else {
        heights.push(itemHeight);
      }
    });
  }
});

/***/ }),

/***/ "../assets/dev/js/modules/imports/utils/scroll.js":
/*!********************************************************!*\
  !*** ../assets/dev/js/modules/imports/utils/scroll.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
// Moved from elementor pro: 'assets/dev/js/frontend/utils'
var Scroll = exports["default"] = /*#__PURE__*/function () {
  function Scroll() {
    (0, _classCallCheck2.default)(this, Scroll);
  }
  return (0, _createClass2.default)(Scroll, null, [{
    key: "scrollObserver",
    value:
    /**
     * @param {Object}      obj
     * @param {number}      obj.sensitivity - Value between 0-100 - Will determine the intersection trigger points on the element
     * @param {Function}    obj.callback    - Will be triggered on each intersection point between the element and the viewport top/bottom
     * @param {string}      obj.offset      - Offset between the element intersection points and the viewport, written like in CSS: '-50% 0 -25%'
     * @param {HTMLElement} obj.root        - The element that the events will be relative to, if 'null' will be relative to the viewport
     */
    function scrollObserver(obj) {
      var lastScrollY = 0;

      // Generating thresholds points along the animation height
      // More thresholds points = more trigger points of the callback
      var buildThresholds = function buildThresholds() {
        var sensitivityPercentage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
        var thresholds = [];
        if (sensitivityPercentage > 0 && sensitivityPercentage <= 100) {
          var increment = 100 / sensitivityPercentage;
          for (var i = 0; i <= 100; i += increment) {
            thresholds.push(i / 100);
          }
        } else {
          thresholds.push(0);
        }
        return thresholds;
      };
      var options = {
        root: obj.root || null,
        rootMargin: obj.offset || '0px',
        threshold: buildThresholds(obj.sensitivity)
      };
      function handleIntersect(entries) {
        var currentScrollY = entries[0].boundingClientRect.y,
          isInViewport = entries[0].isIntersecting,
          intersectionScrollDirection = currentScrollY < lastScrollY ? 'down' : 'up',
          scrollPercentage = Math.abs(parseFloat((entries[0].intersectionRatio * 100).toFixed(2)));
        obj.callback({
          sensitivity: obj.sensitivity,
          isInViewport: isInViewport,
          scrollPercentage: scrollPercentage,
          intersectionScrollDirection: intersectionScrollDirection
        });
        lastScrollY = currentScrollY;
      }
      return new IntersectionObserver(handleIntersect, options);
    }

    /**
     * @param {jQuery.Element} $element
     * @param {Object}         offsetObj
     * @param {number}         offsetObj.start - Offset start value in percentages
     * @param {number}         offsetObj.end   - Offset end value in percentages
     */
  }, {
    key: "getElementViewportPercentage",
    value: function getElementViewportPercentage($element) {
      var offsetObj = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var elementOffset = $element[0].getBoundingClientRect(),
        offsetStart = offsetObj.start || 0,
        offsetEnd = offsetObj.end || 0,
        windowStartOffset = window.innerHeight * offsetStart / 100,
        windowEndOffset = window.innerHeight * offsetEnd / 100,
        y1 = elementOffset.top - window.innerHeight,
        y2 = elementOffset.top + windowStartOffset + $element.height(),
        startPosition = 0 - y1 + windowStartOffset,
        endPosition = y2 - y1 + windowEndOffset,
        percent = Math.max(0, Math.min(startPosition / endPosition, 1));
      return parseFloat((percent * 100).toFixed(2));
    }

    /**
     * @param {Object} offsetObj
     * @param {number} offsetObj.start - Offset start value in percentages
     * @param {number} offsetObj.end   - Offset end value in percentages
     * @param {number} limitPageHeight - Will limit the page height calculation
     */
  }, {
    key: "getPageScrollPercentage",
    value: function getPageScrollPercentage() {
      var offsetObj = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var limitPageHeight = arguments.length > 1 ? arguments[1] : undefined;
      var offsetStart = offsetObj.start || 0,
        offsetEnd = offsetObj.end || 0,
        initialPageHeight = limitPageHeight || document.documentElement.scrollHeight - document.documentElement.clientHeight,
        heightOffset = initialPageHeight * offsetStart / 100,
        pageRange = initialPageHeight + heightOffset + initialPageHeight * offsetEnd / 100,
        scrollPos = document.documentElement.scrollTop + document.body.scrollTop + heightOffset;
      return scrollPos / pageRange * 100;
    }
  }]);
}();

/***/ }),

/***/ "../assets/dev/js/modules/imports/view-module.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/modules/imports/view-module.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _module = _interopRequireDefault(__webpack_require__(/*! ./module */ "../assets/dev/js/modules/imports/module.js"));
var _default = exports["default"] = _module.default.extend({
  elements: null,
  getDefaultElements: function getDefaultElements() {
    return {};
  },
  bindEvents: function bindEvents() {},
  onInit: function onInit() {
    this.initElements();
    this.bindEvents();
  },
  initElements: function initElements() {
    this.elements = this.getDefaultElements();
  }
});

/***/ }),

/***/ "../assets/dev/js/modules/modules.js":
/*!*******************************************!*\
  !*** ../assets/dev/js/modules/modules.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _module = _interopRequireDefault(__webpack_require__(/*! ./imports/module */ "../assets/dev/js/modules/imports/module.js"));
var _viewModule = _interopRequireDefault(__webpack_require__(/*! ./imports/view-module */ "../assets/dev/js/modules/imports/view-module.js"));
var _argsObject = _interopRequireDefault(__webpack_require__(/*! ./imports/args-object */ "../assets/dev/js/modules/imports/args-object.js"));
var _masonry = _interopRequireDefault(__webpack_require__(/*! ./imports/utils/masonry */ "../assets/dev/js/modules/imports/utils/masonry.js"));
var _scroll = _interopRequireDefault(__webpack_require__(/*! ./imports/utils/scroll */ "../assets/dev/js/modules/imports/utils/scroll.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ./imports/force-method-implementation */ "../assets/dev/js/modules/imports/force-method-implementation.js"));
var _templateRegistryHelpers = __webpack_require__(/*! ../../../../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers */ "../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers.js");
var _customizationDialogs = __webpack_require__(/*! ../../../../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs */ "../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs.js");
var baseModules = {
  Module: _module.default,
  ViewModule: _viewModule.default,
  ArgsObject: _argsObject.default,
  ForceMethodImplementation: _forceMethodImplementation.default,
  utils: {
    Masonry: _masonry.default,
    Scroll: _scroll.default
  },
  importExport: {
    createGetInitialState: _templateRegistryHelpers.createGetInitialState,
    customizationDialogsRegistry: _customizationDialogs.customizationDialogsRegistry
  }
};
if (!window.elementorModules) {
  window.elementorModules = baseModules;
} else {
  Object.assign(window.elementorModules, baseModules);
}
var _default = exports["default"] = window.elementorModules;

/***/ }),

/***/ "../core/common/assets/js/views/modal/header.js":
/*!******************************************************!*\
  !*** ../core/common/assets/js/views/modal/header.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$LayoutVie) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$LayoutVie);
  return (0, _createClass2.default)(_default, [{
    key: "tagName",
    value: function tagName() {
      return 'header';
    }
  }, {
    key: "className",
    value: function className() {
      return 'elementor-templates-modal__header';
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-templates-modal__header';
    }
  }, {
    key: "regions",
    value: function regions() {
      return {
        logoArea: '.elementor-templates-modal__header__logo-area',
        tools: '#elementor-template-library-header-tools',
        menuArea: '.elementor-templates-modal__header__menu-area'
      };
    }
  }, {
    key: "ui",
    value: function ui() {
      return {
        closeModal: '.elementor-templates-modal__header__close'
      };
    }
  }, {
    key: "events",
    value: function events() {
      return {
        'click @ui.closeModal': 'onCloseModalClick'
      };
    }
  }, {
    key: "onRender",
    value: function onRender() {
      this.bindEscapeKey();
    }
  }, {
    key: "bindEscapeKey",
    value: function bindEscapeKey() {
      var _this = this;
      this.onDocumentKeyDown = function (event) {
        if ('Escape' === event.key) {
          _this.onCloseModalClick();
        }
      };
      document.addEventListener('keydown', this.onDocumentKeyDown);
    }
  }, {
    key: "onDestroy",
    value: function onDestroy() {
      if (this.onDocumentKeyDown) {
        document.removeEventListener('keydown', this.onDocumentKeyDown);
      }
    }
  }, {
    key: "templateHelpers",
    value: function templateHelpers() {
      return {
        closeType: this.getOption('closeType')
      };
    }
  }, {
    key: "onCloseModalClick",
    value: function onCloseModalClick() {
      this._parent._parent._parent.hideModal();
      var documentType = this.getDocumentType();
      var customEvent = new CustomEvent("core/modal/close/".concat(documentType));
      window.dispatchEvent(customEvent);
      if (this.isFloatingButtonLibraryClose()) {
        $e.internal('document/save/set-is-modified', {
          status: false
        });
        window.location.href = elementor.config.admin_floating_button_admin_url;
      }
    }
  }, {
    key: "getDocumentType",
    value: function getDocumentType() {
      var _elementor$config$doc, _elementor;
      var DEFAULT_TYPE = 'default';
      if ('undefined' === typeof window.elementor) {
        return DEFAULT_TYPE;
      }
      return (_elementor$config$doc = (_elementor = elementor) === null || _elementor === void 0 || (_elementor = _elementor.config) === null || _elementor === void 0 || (_elementor = _elementor.document) === null || _elementor === void 0 ? void 0 : _elementor.type) !== null && _elementor$config$doc !== void 0 ? _elementor$config$doc : DEFAULT_TYPE;
    }
  }, {
    key: "isFloatingButtonLibraryClose",
    value: function isFloatingButtonLibraryClose() {
      var _elementor$config, _elementor$config2;
      return window.elementor && ((_elementor$config = elementor.config) === null || _elementor$config === void 0 ? void 0 : _elementor$config.admin_floating_button_admin_url) && 'floating-buttons' === ((_elementor$config2 = elementor.config) === null || _elementor$config2 === void 0 || (_elementor$config2 = _elementor$config2.document) === null || _elementor$config2 === void 0 ? void 0 : _elementor$config2.type) && (this.$el.closest('.dialog-lightbox-widget-content').find('.elementor-template-library-template-floating_button').length || this.$el.closest('.dialog-lightbox-widget-content').find('#elementor-template-library-preview').length || this.$el.closest('.dialog-lightbox-widget-content').find('#elementor-template-library-templates-empty').length);
    }
  }]);
}(Marionette.LayoutView);

/***/ }),

/***/ "../core/common/assets/js/views/modal/layout.js":
/*!******************************************************!*\
  !*** ../core/common/assets/js/views/modal/layout.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _header = _interopRequireDefault(__webpack_require__(/*! ./header */ "../core/common/assets/js/views/modal/header.js"));
var _logo = _interopRequireDefault(__webpack_require__(/*! ./logo */ "../core/common/assets/js/views/modal/logo.js"));
var _loading = _interopRequireDefault(__webpack_require__(/*! ./loading */ "../core/common/assets/js/views/modal/loading.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$LayoutVie) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$LayoutVie);
  return (0, _createClass2.default)(_default, [{
    key: "el",
    value: function el() {
      return this.getModal().getElements('widget');
    }
  }, {
    key: "regions",
    value: function regions() {
      return {
        modalHeader: '.dialog-header',
        modalContent: '.dialog-lightbox-content',
        modalLoading: '.dialog-lightbox-loading'
      };
    }
  }, {
    key: "initialize",
    value: function initialize() {
      this.modalHeader.show(new _header.default(this.getHeaderOptions()));
    }
  }, {
    key: "getModal",
    value: function getModal() {
      if (!this.modal) {
        this.initModal();
      }
      return this.modal;
    }
  }, {
    key: "initModal",
    value: function initModal() {
      var modalOptions = {
        className: 'elementor-templates-modal',
        closeButton: false,
        draggable: false,
        hide: {
          onOutsideClick: false,
          onEscKeyPress: false
        }
      };
      jQuery.extend(true, modalOptions, this.getModalOptions());
      this.modal = elementorCommon.dialogsManager.createWidget('lightbox', modalOptions);
      this.modal.getElements('message').append(this.modal.addElement('content'), this.modal.addElement('loading'));
      if (modalOptions.draggable) {
        this.draggableModal();
      }
    }
  }, {
    key: "showModal",
    value: function showModal() {
      this.getModal().show();
    }
  }, {
    key: "hideModal",
    value: function hideModal() {
      this.getModal().hide();
    }
  }, {
    key: "draggableModal",
    value: function draggableModal() {
      var $modalWidgetContent = this.getModal().getElements('widgetContent');
      $modalWidgetContent.draggable({
        containment: 'parent',
        stop: function stop() {
          $modalWidgetContent.height('');
        }
      });
      $modalWidgetContent.css('position', 'absolute');
    }
  }, {
    key: "getModalOptions",
    value: function getModalOptions() {
      return {};
    }
  }, {
    key: "getLogoOptions",
    value: function getLogoOptions() {
      return {};
    }
  }, {
    key: "getHeaderOptions",
    value: function getHeaderOptions() {
      return {
        closeType: 'normal'
      };
    }
  }, {
    key: "getHeaderView",
    value: function getHeaderView() {
      return this.modalHeader.currentView;
    }
  }, {
    key: "showLoadingView",
    value: function showLoadingView() {
      this.modalLoading.show(new _loading.default());
      this.modalLoading.$el.show();
      this.modalContent.$el.hide();
    }
  }, {
    key: "hideLoadingView",
    value: function hideLoadingView() {
      this.modalContent.$el.show();
      this.modalLoading.$el.hide();
    }
  }, {
    key: "showLogo",
    value: function showLogo() {
      this.getHeaderView().logoArea.show(new _logo.default(this.getLogoOptions()));
    }
  }]);
}(Marionette.LayoutView);

/***/ }),

/***/ "../core/common/assets/js/views/modal/loading.js":
/*!*******************************************************!*\
  !*** ../core/common/assets/js/views/modal/loading.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$ItemView) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$ItemView);
  return (0, _createClass2.default)(_default, [{
    key: "id",
    value: function id() {
      return 'elementor-template-library-loading';
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-template-library-loading';
    }
  }]);
}(Marionette.ItemView);

/***/ }),

/***/ "../core/common/assets/js/views/modal/logo.js":
/*!****************************************************!*\
  !*** ../core/common/assets/js/views/modal/logo.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$ItemView) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$ItemView);
  return (0, _createClass2.default)(_default, [{
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-templates-modal__header__logo';
    }
  }, {
    key: "className",
    value: function className() {
      return 'elementor-templates-modal__header__logo';
    }
  }, {
    key: "events",
    value: function events() {
      return {
        click: 'onClick'
      };
    }
  }, {
    key: "templateHelpers",
    value: function templateHelpers() {
      return {
        title: this.getOption('title')
      };
    }
  }, {
    key: "onClick",
    value: function onClick() {
      var clickCallback = this.getOption('click');
      if (clickCallback) {
        clickCallback();
      }
    }
  }]);
}(Marionette.ItemView);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-base.js":
/*!************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-base.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandInfra = _interopRequireDefault(__webpack_require__(/*! ./command-infra */ "../modules/web-cli/assets/js/modules/command-infra.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @name $e.modules.CommandBase
 */
var CommandBase = exports["default"] = /*#__PURE__*/function (_CommandInfra) {
  function CommandBase() {
    (0, _classCallCheck2.default)(this, CommandBase);
    return _callSuper(this, CommandBase, arguments);
  }
  (0, _inherits2.default)(CommandBase, _CommandInfra);
  return (0, _createClass2.default)(CommandBase, [{
    key: "onBeforeRun",
    value: function onBeforeRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      $e.hooks.runUIBefore(this.command, args);
    }
  }, {
    key: "onAfterRun",
    value: function onAfterRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
      $e.hooks.runUIAfter(this.command, args, result);
    }
  }, {
    key: "onBeforeApply",
    value: function onBeforeApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      $e.hooks.runDataDependency(this.command, args);
    }
  }, {
    key: "onAfterApply",
    value: function onAfterApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
      return $e.hooks.runDataAfter(this.command, args, result);
    }
  }, {
    key: "onCatchApply",
    value: function onCatchApply(e) {
      this.runCatchHooks(e);
    }

    /**
     * Run all the catch hooks.
     *
     * @param {Error} e
     */
  }, {
    key: "runCatchHooks",
    value: function runCatchHooks(e) {
      $e.hooks.runDataCatch(this.command, this.args, e);
      $e.hooks.runUICatch(this.command, this.args, e);
    }

    /**
     * TODO - Remove - Backwards compatibility.
     *
     * Function requireContainer().
     *
     * Validate `arg.container` & `arg.containers`.
     *
     * @param {{}} args
     * @deprecated since 3.7.0, extend `$e.modules.editor.CommandContainerBase` or `$e.modules.editor.CommandContainerInternalBase` instead.
     *
     * @throws {Error}
     */
  }, {
    key: "requireContainer",
    value: function requireContainer() {
      var _this = this;
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.args;
      _deprecation.default.deprecated('requireContainer()', '3.7.0', 'Extend `$e.modules.editor.CommandContainerBase` or `$e.modules.editor.CommandContainerInternalBase`');
      if (!args.container && !args.containers) {
        throw Error('container or containers are required.');
      }
      if (args.container && args.containers) {
        throw Error('container and containers cannot go together please select one of them.');
      }
      var containers = args.containers || [args.container];
      containers.forEach(function (container) {
        _this.requireArgumentInstance('container', elementorModules.editor.Container, {
          container: container
        });
      });
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandBase';
    }
  }]);
}(_commandInfra.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-callback-base.js":
/*!*********************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-callback-base.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! ./command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * To support pure callbacks in the API(commands.js), to ensure they have registered with the proper context.
 */
var CommandCallbackBase = exports["default"] = /*#__PURE__*/function (_CommandBase) {
  function CommandCallbackBase() {
    (0, _classCallCheck2.default)(this, CommandCallbackBase);
    return _callSuper(this, CommandCallbackBase, arguments);
  }
  (0, _inherits2.default)(CommandCallbackBase, _CommandBase);
  return (0, _createClass2.default)(CommandCallbackBase, [{
    key: "apply",
    value: function apply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return this.constructor.getCallback()(args);
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandCallbackBase';
    }

    /**
     * Get original callback of the command.
     *
     * Support pure callbacks ( Non command-base ).
     *
     * @return {()=>{}} Command Results.
     */
  }, {
    key: "getCallback",
    value: function getCallback() {
      return this.registerConfig.callback;
    }
  }]);
}(_commandBase.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-infra.js":
/*!*************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-infra.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _argsObject = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/imports/args-object */ "../assets/dev/js/modules/imports/args-object.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('../modules/component-base')} ComponentBase
 */
var CommandInfra = exports["default"] = /*#__PURE__*/function (_ArgsObject) {
  /**
   * Function constructor().
   *
   * Create Commands Base.
   *
   * @param {{}} args
   */
  function CommandInfra() {
    var _this;
    var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    (0, _classCallCheck2.default)(this, CommandInfra);
    _this = _callSuper(this, CommandInfra, [args]);
    if (!_this.constructor.registerConfig) {
      throw RangeError('Doing it wrong: Each command type should have `registerConfig`.');
    }

    // Acknowledge self about which command it run.
    _this.command = _this.constructor.getCommand();

    // Assign instance of current component.
    _this.component = _this.constructor.getComponent();

    // Who ever need do something before without `super` the constructor can use `initialize` method.
    _this.initialize(args);

    // Refresh args, maybe the changed via `initialize`.
    args = _this.args;

    // Validate args before run.
    _this.validateArgs(args);
    return _this;
  }

  /**
   * Function initialize().
   *
   * Initialize command, called after construction.
   *
   * @param {{}} args
   */
  (0, _inherits2.default)(CommandInfra, _ArgsObject);
  return (0, _createClass2.default)(CommandInfra, [{
    key: "currentCommand",
    get:
    /**
     * @deprecated since 3.7.0, use `this.command` instead.
     */
    function get() {
      _deprecation.default.deprecated('this.currentCommand', '3.7.0', 'this.command');
      return this.command;
    }
  }, {
    key: "initialize",
    value: function initialize() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    /**
     * Function validateArgs().
     *
     * Validate command arguments.
     *
     * @param {{}} args
     */
  }, {
    key: "validateArgs",
    value: function validateArgs() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function apply().
     *
     * Do the actual command.
     *
     * @param {{}} args
     *
     * @return {*} Command results.
     */
  }, {
    key: "apply",
    value: function apply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      // eslint-disable-line no-unused-vars
      elementorModules.ForceMethodImplementation();
    }

    /**
     * Function run().
     *
     * Run command with history & hooks.
     *
     * @return {*} Command results.
     */
  }, {
    key: "run",
    value: function run() {
      return this.apply(this.args);
    }

    /**
     * Function onBeforeRun.
     *
     * Called before run().
     *
     * @param {{}} args
     */
  }, {
    key: "onBeforeRun",
    value: function onBeforeRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    /**
     * Function onAfterRun.
     *
     * Called after run().
     *
     * @param {{}} args
     * @param {*}  result
     */
  }, {
    key: "onAfterRun",
    value: function onAfterRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
    } // eslint-disable-line no-unused-vars

    /**
     * Function onBeforeApply.
     *
     * Called before apply().
     *
     * @param {{}} args
     */
  }, {
    key: "onBeforeApply",
    value: function onBeforeApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    /**
     * Function onAfterApply.
     *
     * Called after apply().
     *
     * @param {{}} args
     * @param {*}  result
     */
  }, {
    key: "onAfterApply",
    value: function onAfterApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
    } // eslint-disable-line no-unused-vars

    /**
     * Function onCatchApply.
     *
     * Called after apply() failed.
     *
     * @param {Error} e
     */
  }, {
    key: "onCatchApply",
    value: function onCatchApply(e) {} // eslint-disable-line no-unused-vars
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandInfra';
    }

    /**
     * Get info of command.
     *
     * @return {Object} Extra information about the command.
     */
  }, {
    key: "getInfo",
    value: function getInfo() {
      return {};
    }

    /**
     * @return {string} Self command name.
     */
  }, {
    key: "getCommand",
    value: function getCommand() {
      return this.registerConfig.command;
    }

    /**
     * @return {ComponentBase} Self component
     */
  }, {
    key: "getComponent",
    value: function getComponent() {
      return this.registerConfig.component;
    }
  }, {
    key: "setRegisterConfig",
    value: function setRegisterConfig(config) {
      this.registerConfig = Object.freeze(config);
    }
  }]);
}(_argsObject.default);
/**
 * @type {Object}
 */
(0, _defineProperty2.default)(CommandInfra, "registerConfig", null);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/close.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/close.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Close = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Close = exports.Close = /*#__PURE__*/function (_CommandBase) {
  function Close() {
    (0, _classCallCheck2.default)(this, Close);
    return _callSuper(this, Close, arguments);
  }
  (0, _inherits2.default)(Close, _CommandBase);
  return (0, _createClass2.default)(Close, [{
    key: "apply",
    value: function apply() {
      this.component.close();
    }
  }]);
}(_commandBase.default);
var _default = exports["default"] = Close;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/index.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/index.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "Close", ({
  enumerable: true,
  get: function get() {
    return _close.Close;
  }
}));
Object.defineProperty(exports, "Open", ({
  enumerable: true,
  get: function get() {
    return _open.Open;
  }
}));
Object.defineProperty(exports, "Toggle", ({
  enumerable: true,
  get: function get() {
    return _toggle.Toggle;
  }
}));
var _close = __webpack_require__(/*! ./close */ "../modules/web-cli/assets/js/modules/commands/close.js");
var _open = __webpack_require__(/*! ./open */ "../modules/web-cli/assets/js/modules/commands/open.js");
var _toggle = __webpack_require__(/*! ./toggle */ "../modules/web-cli/assets/js/modules/commands/toggle.js");

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/open.js":
/*!*************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/open.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Open = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Open = exports.Open = /*#__PURE__*/function (_CommandBase) {
  function Open() {
    (0, _classCallCheck2.default)(this, Open);
    return _callSuper(this, Open, arguments);
  }
  (0, _inherits2.default)(Open, _CommandBase);
  return (0, _createClass2.default)(Open, [{
    key: "apply",
    value: function apply() {
      $e.route(this.component.getNamespace());
    }
  }]);
}(_commandBase.default);
var _default = exports["default"] = Open;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/toggle.js":
/*!***************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/toggle.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Toggle = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Toggle = exports.Toggle = /*#__PURE__*/function (_CommandBase) {
  function Toggle() {
    (0, _classCallCheck2.default)(this, Toggle);
    return _callSuper(this, Toggle, arguments);
  }
  (0, _inherits2.default)(Toggle, _CommandBase);
  return (0, _createClass2.default)(Toggle, [{
    key: "apply",
    value: function apply() {
      if (this.component.isOpen) {
        this.component.close();
      } else {
        $e.route(this.component.getNamespace());
      }
    }
  }]);
}(_commandBase.default);
var _default = exports["default"] = Toggle;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/component-base.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/component-base.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandCallbackBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-callback-base */ "../modules/web-cli/assets/js/modules/command-callback-base.js"));
var _toolkit = __webpack_require__(/*! @reduxjs/toolkit */ "@reduxjs/toolkit");
var _module = _interopRequireDefault(__webpack_require__(/*! elementor/assets/dev/js/modules/imports/module.js */ "../assets/dev/js/modules/imports/module.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('./command-infra')} CommandInfra
 * @typedef {import('./hook-base')} HookBase
 * @typedef {import('../core/states/ui-state-base')} UiStateBase
 */
var ComponentBase = exports["default"] = /*#__PURE__*/function (_Module) {
  function ComponentBase() {
    (0, _classCallCheck2.default)(this, ComponentBase);
    return _callSuper(this, ComponentBase, arguments);
  }
  (0, _inherits2.default)(ComponentBase, _Module);
  return (0, _createClass2.default)(ComponentBase, [{
    key: "__construct",
    value: function __construct() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      if (args.manager) {
        this.manager = args.manager;
      }
      this.commands = this.defaultCommands();
      this.commandsInternal = this.defaultCommandsInternal();
      this.hooks = this.defaultHooks();
      this.routes = this.defaultRoutes();
      this.tabs = this.defaultTabs();
      this.shortcuts = this.defaultShortcuts();
      this.utils = this.defaultUtils();
      this.data = this.defaultData();
      this.uiStates = this.defaultUiStates();
      this.states = this.defaultStates();
      this.defaultRoute = '';
      this.currentTab = '';
    }
  }, {
    key: "registerAPI",
    value: function registerAPI() {
      var _this = this;
      Object.entries(this.getTabs()).forEach(function (tab) {
        return _this.registerTabRoute(tab[0]);
      });
      Object.entries(this.getRoutes()).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          route = _ref2[0],
          callback = _ref2[1];
        return _this.registerRoute(route, callback);
      });
      Object.entries(this.getCommands()).forEach(function (_ref3) {
        var _ref4 = (0, _slicedToArray2.default)(_ref3, 2),
          command = _ref4[0],
          callback = _ref4[1];
        return _this.registerCommand(command, callback);
      });
      Object.entries(this.getCommandsInternal()).forEach(function (_ref5) {
        var _ref6 = (0, _slicedToArray2.default)(_ref5, 2),
          command = _ref6[0],
          callback = _ref6[1];
        return _this.registerCommandInternal(command, callback);
      });
      Object.values(this.getHooks()).forEach(function (instance) {
        return _this.registerHook(instance);
      });
      Object.entries(this.getData()).forEach(function (_ref7) {
        var _ref8 = (0, _slicedToArray2.default)(_ref7, 2),
          command = _ref8[0],
          callback = _ref8[1];
        return _this.registerData(command, callback);
      });
      Object.values(this.getUiStates()).forEach(function (instance) {
        return _this.registerUiState(instance);
      });
      Object.entries(this.getStates()).forEach(function (_ref9) {
        var _ref0 = (0, _slicedToArray2.default)(_ref9, 2),
          id = _ref0[0],
          state = _ref0[1];
        return _this.registerState(id, state);
      });
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * @return {string} namespace
     */
  }, {
    key: "getNamespace",
    value: function getNamespace() {
      (0, _forceMethodImplementation.default)();
    }

    /**
     * @deprecated since 3.7.0, use `getServiceName()` instead.
     */
  }, {
    key: "getRootContainer",
    value: function getRootContainer() {
      _deprecation.default.deprecated('getRootContainer()', '3.7.0', 'getServiceName()');
      return this.getServiceName();
    }
  }, {
    key: "getServiceName",
    value: function getServiceName() {
      return this.getNamespace().split('/')[0];
    }
  }, {
    key: "store",
    get: function get() {
      return $e.store.get(this.getNamespace());
    }
  }, {
    key: "defaultTabs",
    value: function defaultTabs() {
      return {};
    }
  }, {
    key: "defaultRoutes",
    value: function defaultRoutes() {
      return {};
    }
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      return {};
    }
  }, {
    key: "defaultCommandsInternal",
    value: function defaultCommandsInternal() {
      return {};
    }
  }, {
    key: "defaultHooks",
    value: function defaultHooks() {
      return {};
    }

    /**
     * Get the component's default UI states.
     *
     * @return {Object} default UI states
     */
  }, {
    key: "defaultUiStates",
    value: function defaultUiStates() {
      return {};
    }

    /**
     * Get the component's Redux slice settings.
     *
     * @return {Object} Redux slice settings
     */
  }, {
    key: "defaultStates",
    value: function defaultStates() {
      return {};
    }
  }, {
    key: "defaultShortcuts",
    value: function defaultShortcuts() {
      return {};
    }
  }, {
    key: "defaultUtils",
    value: function defaultUtils() {
      return {};
    }
  }, {
    key: "defaultData",
    value: function defaultData() {
      return {};
    }
  }, {
    key: "getCommands",
    value: function getCommands() {
      return this.commands;
    }
  }, {
    key: "getCommandsInternal",
    value: function getCommandsInternal() {
      return this.commandsInternal;
    }
  }, {
    key: "getHooks",
    value: function getHooks() {
      return this.hooks;
    }

    /**
     * Retrieve the component's UI states.
     *
     * @return {Object} UI states
     */
  }, {
    key: "getUiStates",
    value: function getUiStates() {
      return this.uiStates;
    }

    /**
     * Retrieve the component's Redux Slice.
     *
     * @return {Object} Redux Slice
     */
  }, {
    key: "getStates",
    value: function getStates() {
      return this.states;
    }
  }, {
    key: "getRoutes",
    value: function getRoutes() {
      return this.routes;
    }
  }, {
    key: "getTabs",
    value: function getTabs() {
      return this.tabs;
    }
  }, {
    key: "getShortcuts",
    value: function getShortcuts() {
      return this.shortcuts;
    }
  }, {
    key: "getData",
    value: function getData() {
      return this.data;
    }

    /**
     * @param {string}                      command
     * @param {(()=>{}|CommandInfra)}       context
     * @param {'default'|'internal'|'data'} commandsType
     */
  }, {
    key: "registerCommand",
    value: function registerCommand(command, context) {
      var commandsType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
      var commandsManager;
      switch (commandsType) {
        case 'default':
          commandsManager = $e.commands;
          break;
        case 'internal':
          commandsManager = $e.commandsInternal;
          break;
        case 'data':
          commandsManager = $e.data;
          break;
        default:
          throw new Error("Invalid commands type: '".concat(command, "'"));
      }
      var fullCommand = this.getNamespace() + '/' + command,
        instanceType = context.getInstanceType ? context.getInstanceType() : false,
        registerConfig = {
          command: fullCommand,
          component: this
        };

      // Support pure callback.
      if (!instanceType) {
        if ($e.devTools) {
          $e.devTools.log.warn("Attach command-callback-base, on command: '".concat(fullCommand, "', context is unknown type."));
        }
        registerConfig.callback = context;

        // Unique class.
        context = /*#__PURE__*/function (_CommandCallbackBase) {
          function context() {
            (0, _classCallCheck2.default)(this, context);
            return _callSuper(this, context, arguments);
          }
          (0, _inherits2.default)(context, _CommandCallbackBase);
          return (0, _createClass2.default)(context);
        }(_commandCallbackBase.default);
      }
      context.setRegisterConfig(registerConfig);
      commandsManager.register(this, command, context);
    }

    /**
     * @param {HookBase} instance
     */
  }, {
    key: "registerHook",
    value: function registerHook(instance) {
      return instance.register();
    }
  }, {
    key: "registerCommandInternal",
    value: function registerCommandInternal(command, context) {
      this.registerCommand(command, context, 'internal');
    }

    /**
     * Register a UI state.
     *
     * @param {UiStateBase} instance - UI state instance.
     *
     * @return {void}
     */
  }, {
    key: "registerUiState",
    value: function registerUiState(instance) {
      $e.uiStates.register(instance);
    }

    /**
     * Register a Redux Slice.
     *
     * @param {string} id          - State id.
     * @param {Object} stateConfig - The state config.
     *
     * @return {void}
     */
  }, {
    key: "registerState",
    value: function registerState(id, stateConfig) {
      id = this.getNamespace() + (id ? "/".concat(id) : '');
      var slice = (0, _toolkit.createSlice)(_objectSpread(_objectSpread({}, stateConfig), {}, {
        name: id
      }));
      $e.store.register(id, slice);
    }
  }, {
    key: "registerRoute",
    value: function registerRoute(route, callback) {
      $e.routes.register(this, route, callback);
    }
  }, {
    key: "registerData",
    value: function registerData(command, context) {
      this.registerCommand(command, context, 'data');
    }
  }, {
    key: "unregisterRoute",
    value: function unregisterRoute(route) {
      $e.routes.unregister(this, route);
    }
  }, {
    key: "registerTabRoute",
    value: function registerTabRoute(tab) {
      var _this2 = this;
      this.registerRoute(tab, function (args) {
        return _this2.activateTab(tab, args);
      });
    }
  }, {
    key: "dependency",
    value: function dependency() {
      return true;
    }
  }, {
    key: "open",
    value: function open() {
      return true;
    }
  }, {
    key: "close",
    value: function close() {
      if (!this.isOpen) {
        return false;
      }
      this.isOpen = false;
      this.inactivate();
      $e.routes.clearCurrent(this.getNamespace());
      $e.routes.clearHistory(this.getServiceName());
      return true;
    }
  }, {
    key: "activate",
    value: function activate() {
      $e.components.activate(this.getNamespace());
    }
  }, {
    key: "inactivate",
    value: function inactivate() {
      $e.components.inactivate(this.getNamespace());
    }
  }, {
    key: "isActive",
    value: function isActive() {
      return $e.components.isActive(this.getNamespace());
    }
  }, {
    key: "onRoute",
    value: function onRoute(route) {
      this.toggleRouteClass(route, true);
      this.toggleHistoryClass();
      this.activate();
      this.trigger('route/open', route);
    }
  }, {
    key: "onCloseRoute",
    value: function onCloseRoute(route) {
      this.toggleRouteClass(route, false);
      this.inactivate();
      this.trigger('route/close', route);
    }
  }, {
    key: "setDefaultRoute",
    value: function setDefaultRoute(route) {
      this.defaultRoute = this.getNamespace() + '/' + route;
    }
  }, {
    key: "getDefaultRoute",
    value: function getDefaultRoute() {
      return this.defaultRoute;
    }
  }, {
    key: "removeTab",
    value: function removeTab(tab) {
      delete this.tabs[tab];
      this.unregisterRoute(tab);
    }
  }, {
    key: "hasTab",
    value: function hasTab(tab) {
      return !!this.tabs[tab];
    }
  }, {
    key: "addTab",
    value: function addTab(tab, args, position) {
      var _this3 = this;
      this.tabs[tab] = args;
      // It can be 0.
      if ('undefined' !== typeof position) {
        var newTabs = {};
        var ids = Object.keys(this.tabs);
        // Remove new tab
        ids.pop();

        // Add it to position.
        ids.splice(position, 0, tab);
        ids.forEach(function (id) {
          newTabs[id] = _this3.tabs[id];
        });
        this.tabs = newTabs;
      }
      this.registerTabRoute(tab);
    }
  }, {
    key: "getTabsWrapperSelector",
    value: function getTabsWrapperSelector() {
      return '';
    }
  }, {
    key: "getTabRoute",
    value: function getTabRoute(tab) {
      return this.getNamespace() + '/' + tab;
    }
  }, {
    key: "renderTab",
    value: function renderTab(tab) {} // eslint-disable-line
  }, {
    key: "activateTab",
    value: function activateTab(tab, args) {
      var _this4 = this;
      this.renderTab(tab, args);
      jQuery(this.getTabsWrapperSelector() + ' .elementor-component-tab').off('click').on('click', function (event) {
        $e.route(_this4.getTabRoute(event.currentTarget.dataset.tab), args);
      }).removeClass('elementor-active').filter('[data-tab="' + tab + '"]').addClass('elementor-active');
    }
  }, {
    key: "getActiveTabConfig",
    value: function getActiveTabConfig() {
      return this.tabs[this.currentTab] || {};
    }
  }, {
    key: "getBodyClass",
    value: function getBodyClass(route) {
      return 'e-route-' + route.replace(/\//g, '-');
    }

    /**
     * If command includes uppercase character convert it to lowercase and add `-`.
     * e.g: `CopyAll` is converted to `copy-all`.
     *
     * @param {string} commandName
     */
  }, {
    key: "normalizeCommandName",
    value: function normalizeCommandName(commandName) {
      return commandName.replace(/[A-Z]/g, function (match, offset) {
        return (offset > 0 ? '-' : '') + match.toLowerCase();
      });
    }

    /**
     * @param {{}} commandsFromImport
     * @return {{}} imported commands
     */
  }, {
    key: "importCommands",
    value: function importCommands(commandsFromImport) {
      var _this5 = this;
      var commands = {};

      // Convert `Commands` to `ComponentBase` workable format.
      Object.entries(commandsFromImport).forEach(function (_ref1) {
        var _ref10 = (0, _slicedToArray2.default)(_ref1, 2),
          className = _ref10[0],
          Class = _ref10[1];
        var command = _this5.normalizeCommandName(className);
        commands[command] = Class;
      });
      return commands;
    }
  }, {
    key: "importHooks",
    value: function importHooks(hooksFromImport) {
      var hooks = {};
      for (var key in hooksFromImport) {
        var hook = new hooksFromImport[key]();
        hooks[hook.getId()] = hook;
      }
      return hooks;
    }

    /**
     * Import & initialize the component's UI states.
     * Should be used inside `defaultUiState()`.
     *
     * @param {Object} statesFromImport - UI states from import.
     *
     * @return {Object} UI States
     */
  }, {
    key: "importUiStates",
    value: function importUiStates(statesFromImport) {
      var _this6 = this;
      var uiStates = {};
      Object.values(statesFromImport).forEach(function (className) {
        var uiState = new className(_this6);
        uiStates[uiState.getId()] = uiState;
      });
      return uiStates;
    }

    /**
     * Set a UI state value.
     * TODO: Should we provide such function? Maybe the developer should implicitly pass the full state ID?
     *
     * @param {string} state - Non-prefixed state ID.
     * @param {*}      value - New state value.
     *
     * @return {void}
     */
  }, {
    key: "setUiState",
    value: function setUiState(state, value) {
      $e.uiStates.set("".concat(this.getNamespace(), "/").concat(state), value);
    }
  }, {
    key: "toggleRouteClass",
    value: function toggleRouteClass(route, state) {
      document.body.classList.toggle(this.getBodyClass(route), state);
    }
  }, {
    key: "toggleHistoryClass",
    value: function toggleHistoryClass() {
      document.body.classList.toggle('e-routes-has-history', !!$e.routes.getHistory(this.getServiceName()).length);
    }
  }]);
}(_module.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/component-modal-base.js":
/*!********************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/component-modal-base.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _readOnlyError2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/readOnlyError */ "../node_modules/@babel/runtime/helpers/readOnlyError.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _componentBase = _interopRequireDefault(__webpack_require__(/*! ./component-base */ "../modules/web-cli/assets/js/modules/component-base.js"));
var commands = _interopRequireWildcard(__webpack_require__(/*! ./commands/ */ "../modules/web-cli/assets/js/modules/commands/index.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var ComponentModalBase = exports["default"] = /*#__PURE__*/function (_ComponentBase) {
  function ComponentModalBase() {
    (0, _classCallCheck2.default)(this, ComponentModalBase);
    return _callSuper(this, ComponentModalBase, arguments);
  }
  (0, _inherits2.default)(ComponentModalBase, _ComponentBase);
  return (0, _createClass2.default)(ComponentModalBase, [{
    key: "registerAPI",
    value: function registerAPI() {
      var _this = this;
      _superPropGet(ComponentModalBase, "registerAPI", this, 3)([]);
      $e.shortcuts.register('esc', {
        scopes: [this.getNamespace()],
        callback: function callback() {
          return _this.close();
        }
      });
    }
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      return this.importCommands(commands);
    }
  }, {
    key: "defaultRoutes",
    value: function defaultRoutes() {
      return {
        '': function _() {/* Nothing to do, it's already rendered. */}
      };
    }
  }, {
    key: "open",
    value: function open() {
      var _this2 = this;
      if (!this.layout) {
        var layout = this.getModalLayout();
        this.layout = new layout({
          component: this
        });
        this.layout.getModal().on('hide', function () {
          return _this2.close();
        });
      }
      this.layout.showModal();
      return true;
    }
  }, {
    key: "close",
    value: function close() {
      if (!_superPropGet(ComponentModalBase, "close", this, 3)([])) {
        return false;
      }
      var close = elementor.hooks.applyFilters('component/modal/close', this.layout.getModal().hide.bind(this.layout.getModal()), this);
      close();
      return true;
    }
  }, {
    key: "getModalLayout",
    value: function getModalLayout() {
      (0, _forceMethodImplementation.default)();
    }
  }]);
}(_componentBase.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hook-break.js":
/*!**********************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hook-break.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var HookBreak = exports["default"] = /*#__PURE__*/function (_Error) {
  function HookBreak() {
    (0, _classCallCheck2.default)(this, HookBreak);
    return _callSuper(this, HookBreak, ['HookBreak']);
  }
  (0, _inherits2.default)(HookBreak, _Error);
  return (0, _createClass2.default)(HookBreak);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/console.js":
/*!*****************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/console.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var Console = exports["default"] = /*#__PURE__*/function () {
  function Console() {
    (0, _classCallCheck2.default)(this, Console);
  }
  return (0, _createClass2.default)(Console, null, [{
    key: "error",
    value: function error(message) {
      // Show an error if devTools is available.
      if ($e.devTools) {
        $e.devTools.log.error(message);
      }

      // If not a 'Hook-Break' then show error.
      if (!(message instanceof $e.modules.HookBreak)) {
        // eslint-disable-next-line no-console
        console.error(message);
      }
    }
  }, {
    key: "warn",
    value: function warn() {
      var _console;
      var style = "font-size: 12px; background-image: url(\"".concat(elementorWebCliConfig.urls.assets, "images/logo-icon.png\"); background-repeat: no-repeat; background-size: contain;");
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      args.unshift('%c  %c', style, '');
      (_console = console).warn.apply(_console, args); // eslint-disable-line no-console
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/deprecation.js":
/*!*********************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/deprecation.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
// Copied from `modules/dev-tools/assets/js/deprecation.js`
/**
 * @typedef {Object} Version
 * @property {number} major1 The first number
 * @property {number} major2 The second number
 * @property {number} minor  The third number
 * @property {string} build  The fourth number
 */

var softDeprecated = function softDeprecated(name, version, replacement) {
  if (elementorWebCliConfig.isDebug) {
    deprecatedMessage('soft', name, version, replacement);
  }
};
var hardDeprecated = function hardDeprecated(name, version, replacement) {
  deprecatedMessage('hard', name, version, replacement);
};
var deprecatedMessage = function deprecatedMessage(type, name, version, replacement) {
  var message = "`".concat(name, "` is ").concat(type, " deprecated since ").concat(version);
  if (replacement) {
    message += " - Use `".concat(replacement, "` instead");
  }
  _console.default.warn(message);
};
var Deprecation = exports["default"] = /*#__PURE__*/function () {
  function Deprecation() {
    (0, _classCallCheck2.default)(this, Deprecation);
  }
  return (0, _createClass2.default)(Deprecation, null, [{
    key: "deprecated",
    value: function deprecated(name, version, replacement) {
      if (this.isHardDeprecated(version)) {
        hardDeprecated(name, version, replacement);
      } else {
        softDeprecated(name, version, replacement);
      }
    }

    /**
     * @param {string} version
     *
     * @return {Version}
     */
  }, {
    key: "parseVersion",
    value: function parseVersion(version) {
      var versionParts = version.split('.');
      if (versionParts.length < 3 || versionParts.length > 4) {
        throw new RangeError('Invalid Semantic Version string provided');
      }
      var _versionParts = (0, _slicedToArray2.default)(versionParts, 4),
        major1 = _versionParts[0],
        major2 = _versionParts[1],
        minor = _versionParts[2],
        _versionParts$ = _versionParts[3],
        build = _versionParts$ === void 0 ? '' : _versionParts$;
      return {
        major1: parseInt(major1),
        major2: parseInt(major2),
        minor: parseInt(minor),
        build: build
      };
    }

    /**
     * Get total of major.
     *
     * Since `get_total_major` cannot determine how much really versions between 2.9.0 and 3.3.0 if there is 2.10.0 version for example,
     * versions with major2 more then 9 will be added to total.
     *
     * @param {Version} versionObj
     *
     * @return {number}
     */
  }, {
    key: "getTotalMajor",
    value: function getTotalMajor(versionObj) {
      var total = parseInt("".concat(versionObj.major1).concat(versionObj.major2, "0"));
      total = Number((total / 10).toFixed(0));
      if (versionObj.major2 > 9) {
        total = versionObj.major2 - 9;
      }
      return total;
    }

    /**
     * @param {string} version1
     * @param {string} version2
     *
     * @return {number}
     */
  }, {
    key: "compareVersion",
    value: function compareVersion(version1, version2) {
      var _this = this;
      return [this.parseVersion(version1), this.parseVersion(version2)].map(function (versionObj) {
        return _this.getTotalMajor(versionObj);
      }).reduce(function (acc, major) {
        return acc - major;
      });
    }

    /**
     * @param {string} version
     *
     * @return {boolean}
     */
  }, {
    key: "isSoftDeprecated",
    value: function isSoftDeprecated(version) {
      var total = this.compareVersion(version, elementorWebCliConfig.version);
      return total <= 4;
    }

    /**
     * @param {string} version
     * @return {boolean}
     */
  }, {
    key: "isHardDeprecated",
    value: function isHardDeprecated(version) {
      var total = this.compareVersion(version, elementorWebCliConfig.version);
      return total < 0 || total >= 8;
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/force-method-implementation.js":
/*!*************************************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/force-method-implementation.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ForceMethodImplementation = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
// TODO: Copied from `assets/dev/js/modules/imports/force-method-implementation.js`;
var ForceMethodImplementation = exports.ForceMethodImplementation = /*#__PURE__*/function (_Error) {
  function ForceMethodImplementation() {
    var _this;
    var info = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    (0, _classCallCheck2.default)(this, ForceMethodImplementation);
    _this = _callSuper(this, ForceMethodImplementation, ["".concat(info.isStatic ? 'static ' : '').concat(info.fullName, "() should be implemented, please provide '").concat(info.functionName || info.fullName, "' functionality.")]);
    Error.captureStackTrace(_this, ForceMethodImplementation);
    return _this;
  }
  (0, _inherits2.default)(ForceMethodImplementation, _Error);
  return (0, _createClass2.default)(ForceMethodImplementation);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));
var _default = exports["default"] = function _default() {
  var stack = Error().stack,
    caller = stack.split('\n')[2].trim(),
    callerName = caller.startsWith('at new') ? 'constructor' : caller.split(' ')[1],
    info = {};
  info.functionName = callerName;
  info.fullName = callerName;
  if (info.functionName.includes('.')) {
    var parts = info.functionName.split('.');
    info.className = parts[0];
    info.functionName = parts[1];
  } else {
    info.isStatic = true;
  }
  throw new ForceMethodImplementation(info);
};

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return arrayLikeToArray(r);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _assertThisInitialized(e) {
  if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  return e;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/construct.js":
/*!***********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/construct.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var isNativeReflectConstruct = __webpack_require__(/*! ./isNativeReflectConstruct.js */ "../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js");
var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _construct(t, e, r) {
  if (isNativeReflectConstruct()) return Reflect.construct.apply(null, arguments);
  var o = [null];
  o.push.apply(o, e);
  var p = new (t.bind.apply(t, o))();
  return r && setPrototypeOf(p, r.prototype), p;
}
module.exports = _construct, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/get.js":
/*!*****************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/get.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var superPropBase = __webpack_require__(/*! ./superPropBase.js */ "../node_modules/@babel/runtime/helpers/superPropBase.js");
function _get() {
  return module.exports = _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) {
    var p = superPropBase(e, t);
    if (p) {
      var n = Object.getOwnPropertyDescriptor(p, t);
      return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value;
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _get.apply(null, arguments);
}
module.exports = _get, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _getPrototypeOf(t) {
  return module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) {
    return t.__proto__ || Object.getPrototypeOf(t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _getPrototypeOf(t);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/inherits.js":
/*!**********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/inherits.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(t, e) {
  if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
  t.prototype = Object.create(e && e.prototype, {
    constructor: {
      value: t,
      writable: !0,
      configurable: !0
    }
  }), Object.defineProperty(t, "prototype", {
    writable: !1
  }), e && setPrototypeOf(t, e);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/isNativeFunction.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/isNativeFunction.js ***!
  \******************************************************************/
/***/ ((module) => {

function _isNativeFunction(t) {
  try {
    return -1 !== Function.toString.call(t).indexOf("[native code]");
  } catch (n) {
    return "function" == typeof t;
  }
}
module.exports = _isNativeFunction, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js ***!
  \**************************************************************************/
/***/ ((module) => {

function _isNativeReflectConstruct() {
  try {
    var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
  } catch (t) {}
  return (module.exports = _isNativeReflectConstruct = function _isNativeReflectConstruct() {
    return !!t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _isNativeReflectConstruct, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js":
/*!*************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectWithoutProperties.js ***!
  \*************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var objectWithoutPropertiesLoose = __webpack_require__(/*! ./objectWithoutPropertiesLoose.js */ "../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js");
function _objectWithoutProperties(e, t) {
  if (null == e) return {};
  var o,
    r,
    i = objectWithoutPropertiesLoose(e, t);
  if (Object.getOwnPropertySymbols) {
    var n = Object.getOwnPropertySymbols(e);
    for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
  }
  return i;
}
module.exports = _objectWithoutProperties, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js":
/*!******************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js ***!
  \******************************************************************************/
/***/ ((module) => {

function _objectWithoutPropertiesLoose(r, e) {
  if (null == r) return {};
  var t = {};
  for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
    if (-1 !== e.indexOf(n)) continue;
    t[n] = r[n];
  }
  return t;
}
module.exports = _objectWithoutPropertiesLoose, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!***************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(t, e) {
  if (e && ("object" == _typeof(e) || "function" == typeof e)) return e;
  if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined");
  return assertThisInitialized(t);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/readOnlyError.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/readOnlyError.js ***!
  \***************************************************************/
/***/ ((module) => {

function _readOnlyError(r) {
  throw new TypeError('"' + r + '" is read-only');
}
module.exports = _readOnlyError, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _setPrototypeOf(t, e) {
  return module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) {
    return t.__proto__ = e, t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _setPrototypeOf(t, e);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(r, e) {
  return arrayWithHoles(r) || iterableToArrayLimit(r, e) || unsupportedIterableToArray(r, e) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/superPropBase.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/superPropBase.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
function _superPropBase(t, o) {
  for (; !{}.hasOwnProperty.call(t, o) && null !== (t = getPrototypeOf(t)););
  return t;
}
module.exports = _superPropBase, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "../node_modules/@babel/runtime/helpers/iterableToArray.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js");
function _toConsumableArray(r) {
  return arrayWithoutHoles(r) || iterableToArray(r) || unsupportedIterableToArray(r) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? arrayLikeToArray(r, a) : void 0;
  }
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/wrapNativeSuper.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
var isNativeFunction = __webpack_require__(/*! ./isNativeFunction.js */ "../node_modules/@babel/runtime/helpers/isNativeFunction.js");
var construct = __webpack_require__(/*! ./construct.js */ "../node_modules/@babel/runtime/helpers/construct.js");
function _wrapNativeSuper(t) {
  var r = "function" == typeof Map ? new Map() : void 0;
  return module.exports = _wrapNativeSuper = function _wrapNativeSuper(t) {
    if (null === t || !isNativeFunction(t)) return t;
    if ("function" != typeof t) throw new TypeError("Super expression must either be null or a function");
    if (void 0 !== r) {
      if (r.has(t)) return r.get(t);
      r.set(t, Wrapper);
    }
    function Wrapper() {
      return construct(t, arguments, getPrototypeOf(this).constructor);
    }
    return Wrapper.prototype = Object.create(t.prototype, {
      constructor: {
        value: Wrapper,
        enumerable: !1,
        writable: !0,
        configurable: !0
      }
    }), setPrototypeOf(Wrapper, t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _wrapNativeSuper(t);
}
module.exports = _wrapNativeSuper, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "@reduxjs/toolkit":
/*!************************************************!*\
  !*** external "elementorVendors.reduxToolkit" ***!
  \************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorVendors.reduxToolkit;

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
/*!*******************************************!*\
  !*** ../core/common/assets/js/modules.js ***!
  \*******************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _modules = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/modules */ "../assets/dev/js/modules/modules.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ./views/modal/layout */ "../core/common/assets/js/views/modal/layout.js"));
var _componentBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/component-base */ "../modules/web-cli/assets/js/modules/component-base.js"));
var _componentModalBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/component-modal-base */ "../modules/web-cli/assets/js/modules/component-modal-base.js"));
var _hookBreak = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/hook-break */ "../modules/web-cli/assets/js/modules/hook-break.js"));
_modules.default.common = {
  /**
   * @deprecated since 2.9.0, use `$e.modules.ComponentBase` instead.
   */
  get Component() {
    // `elementorCommon` isn't available during it self initialize.
    setTimeout(function () {
      elementorDevTools.deprecation.deprecated('elementorModules.common.Component', '2.9.0', '$e.modules.ComponentBase');
    }, 2000);
    return _componentBase.default;
  },
  /**
   * @deprecated since 2.9.0, use `$e.modules.ComponentModalBase` instead.
   */
  get ComponentModal() {
    // `elementorCommon` isn't available during it self initialize.
    setTimeout(function () {
      elementorDevTools.deprecation.deprecated('elementorModules.common.ComponentModal', '2.9.0', '$e.modules.ComponentModalBase');
    }, 2000);
    return _componentModalBase.default;
  },
  /**
   * @deprecated since 2.9.0, use `$e.modules.HookBreak` instead.
   */
  get HookBreak() {
    // `elementorCommon` isn't available during it self initialize.
    setTimeout(function () {
      elementorDevTools.deprecation.deprecated('elementorModules.common.HookBreak', '2.9.0', '$e.modules.HookBreak');
    }, 2000);
    return _hookBreak.default;
  },
  views: {
    modal: {
      Layout: _layout.default
    }
  }
};
})();

/******/ })()
;
//# sourceMappingURL=common-modules.js.map
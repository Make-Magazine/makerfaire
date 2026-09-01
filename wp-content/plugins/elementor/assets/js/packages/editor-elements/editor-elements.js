/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/editor-elements/src/errors.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/errors.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ElementIndexNotFoundError: function() { return /* binding */ ElementIndexNotFoundError; },
/* harmony export */   ElementLabelNotExistsError: function() { return /* binding */ ElementLabelNotExistsError; },
/* harmony export */   ElementNotFoundError: function() { return /* binding */ ElementNotFoundError; },
/* harmony export */   ElementParentNotFoundError: function() { return /* binding */ ElementParentNotFoundError; },
/* harmony export */   ElementTypeNotExistsError: function() { return /* binding */ ElementTypeNotExistsError; },
/* harmony export */   StyleNotFoundError: function() { return /* binding */ StyleNotFoundError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const ElementNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'element_not_found',
  message: 'Element not found.'
});
const StyleNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'style_not_found',
  message: 'Style not found.'
});
const ElementTypeNotExistsError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'element_type_not_exists',
  message: 'Element type does not exist.'
});
const ElementLabelNotExistsError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'element_label_not_exists',
  message: 'Element label does not exist.'
});
const ElementParentNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'element_parent_not_found',
  message: 'Element parent not found.'
});
const ElementIndexNotFoundError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'element_index_not_found',
  message: 'Element index not found.'
});

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/hooks/use-element-children.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/hooks/use-element-children.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useElementChildren: function() { return /* binding */ useElementChildren; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _sync_get_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _sync_model_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../sync/model-utils */ "./packages/packages/libs/editor-elements/src/sync/model-utils.ts");



function toElementModel({
  model
}) {
  return {
    id: model.get('id'),
    editorSettings: model.get('editor_settings') ?? {}
  };
}
function useElementChildren(elementId, childrenTypes) {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/create'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/delete'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/update'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/set-settings')], () => {
    const container = (0,_sync_get_container__WEBPACK_IMPORTED_MODULE_1__.getContainer)(elementId);
    const model = container?.model;
    if (!model) {
      return {};
    }
    const elementChildren = Object.entries(childrenTypes).reduce((acc, [parentType, childType]) => {
      const parent = (0,_sync_model_utils__WEBPACK_IMPORTED_MODULE_2__.findChildRecursive)(model, m => m.get('elType') === parentType);
      if (!parent) {
        acc[childType] = [];
        return acc;
      }
      const children = (0,_sync_model_utils__WEBPACK_IMPORTED_MODULE_2__.getElementChildren)(parent.model, m => m.get('elType') === childType);
      acc[childType] = children.map(toElementModel);
      return acc;
    }, {});
    return elementChildren;
  }, [elementId]);
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/hooks/use-element-editor-settings.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/hooks/use-element-editor-settings.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useElementEditorSettings: function() { return /* binding */ useElementEditorSettings; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _sync_get_element_editor_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/get-element-editor-settings */ "./packages/packages/libs/editor-elements/src/sync/get-element-editor-settings.ts");


const useElementEditorSettings = elementId => {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)('elementor/element/update_editor_settings'), () => (0,_sync_get_element_editor_settings__WEBPACK_IMPORTED_MODULE_1__.getElementEditorSettings)(elementId), [elementId]);
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/hooks/use-parent-element.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/hooks/use-parent-element.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useParentElement: function() { return /* binding */ useParentElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function useParentElement(elementId) {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/create')], () => {
    if (!elementId) {
      return null;
    }
    const extendedWindow = window;
    const element = extendedWindow?.elementor?.getContainer?.(elementId);
    if (!element) {
      return null;
    }
    return element.parent;
  }, [elementId]);
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/hooks/use-selected-element-settings.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/hooks/use-selected-element-settings.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSelectedElementSettings: function() { return /* binding */ useSelectedElementSettings; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _sync_get_element_setting__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/get-element-setting */ "./packages/packages/libs/editor-elements/src/sync/get-element-setting.ts");
/* harmony import */ var _sync_get_selected_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../sync/get-selected-elements */ "./packages/packages/libs/editor-elements/src/sync/get-selected-elements.ts");



// Use the selected element and it's settings,
// and subscribe to changes on both the selected element and its settings.
// This ensures the element and its settings are synced.
function useSelectedElementSettings() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/select'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/deselect'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/select-all'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/deselect-all'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/set-settings')], () => {
    const {
      element,
      elementType
    } = (0,_sync_get_selected_elements__WEBPACK_IMPORTED_MODULE_2__.getSelectedElement)();
    if (!element || !elementType) {
      return {
        element: null,
        elementType: null,
        settings: null
      };
    }
    const settings = (0,_sync_get_element_setting__WEBPACK_IMPORTED_MODULE_1__.getElementSettings)(element.id, Object.keys(elementType.propsSchema));
    return {
      element,
      elementType,
      settings
    };
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/hooks/use-selected-element.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/hooks/use-selected-element.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSelectedElement: function() { return /* binding */ useSelectedElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _sync_get_selected_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/get-selected-elements */ "./packages/packages/libs/editor-elements/src/sync/get-selected-elements.ts");


function useSelectedElement() {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateUseListenTo)([(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/select'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/deselect'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/select-all'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/deselect-all')], _sync_get_selected_elements__WEBPACK_IMPORTED_MODULE_1__.getSelectedElement);
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/link-restriction.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/link-restriction.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getAnchoredAncestorId: function() { return /* binding */ getAnchoredAncestorId; },
/* harmony export */   getAnchoredDescendantId: function() { return /* binding */ getAnchoredDescendantId; },
/* harmony export */   getLinkInLinkRestriction: function() { return /* binding */ getLinkInLinkRestriction; },
/* harmony export */   isElementAnchored: function() { return /* binding */ isElementAnchored; }
/* harmony export */ });
/* harmony import */ var _sync_get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./sync/get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _sync_get_element_setting__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./sync/get-element-setting */ "./packages/packages/libs/editor-elements/src/sync/get-element-setting.ts");


const ANCHOR_SELECTOR = 'a, [data-action-link]';
function getLinkInLinkRestriction(elementId, resolvedValue) {
  const anchoredDescendantId = getAnchoredDescendantId(elementId);
  if (anchoredDescendantId) {
    return {
      shouldRestrict: true,
      reason: 'descendant',
      elementId: anchoredDescendantId
    };
  }
  const hasInlineLink = checkForInlineLink(elementId, resolvedValue);
  if (hasInlineLink) {
    return {
      shouldRestrict: true,
      reason: 'descendant',
      elementId
    };
  }
  const ancestor = getAnchoredAncestorId(elementId);
  if (ancestor) {
    return {
      shouldRestrict: true,
      reason: 'ancestor',
      elementId: ancestor
    };
  }
  return {
    shouldRestrict: false
  };
}
function getAnchoredDescendantId(elementId) {
  const element = getElementDOM(elementId);
  if (!element) {
    return null;
  }
  for (const childAnchorElement of Array.from(element.querySelectorAll(ANCHOR_SELECTOR))) {
    // Ensure the child is not in the current element's scope
    const childElementId = findElementIdOf(childAnchorElement);
    if (childElementId !== elementId) {
      return childElementId;
    }
  }
  return null;
}
function getAnchoredAncestorId(elementId) {
  const element = getElementDOM(elementId);
  if (!element || element.parentElement === null) {
    return null;
  }
  const parentAnchor = element.parentElement.closest(ANCHOR_SELECTOR);
  return parentAnchor ? findElementIdOf(parentAnchor) : null;
}
function isElementAnchored(elementId) {
  const element = getElementDOM(elementId);
  if (!element) {
    return false;
  }
  if (element.matches(ANCHOR_SELECTOR)) {
    return true;
  }
  return doesElementContainAnchor(element);
}
function doesElementContainAnchor(element) {
  for (const child of Array.from(element.children)) {
    if (isElementorElement(child)) {
      continue;
    }
    if (child.matches(ANCHOR_SELECTOR)) {
      return true;
    }
    if (doesElementContainAnchor(child)) {
      return true;
    }
  }
  return false;
}
function findElementIdOf(element) {
  return element.closest('[data-id]')?.dataset.id || null;
}
function checkForInlineLink(elementId, resolvedValue) {
  const element = getElementDOM(elementId);
  if (!element) {
    return false;
  }
  if (element.matches(ANCHOR_SELECTOR)) {
    return false;
  }
  const linkSetting = resolvedValue ?? (0,_sync_get_element_setting__WEBPACK_IMPORTED_MODULE_1__.getElementSetting)(elementId, 'link')?.value;
  if (linkSetting?.destination) {
    return false;
  }
  return element.querySelector(ANCHOR_SELECTOR) !== null;
}
function getElementDOM(id) {
  try {
    const fromContainer = (0,_sync_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(id)?.view?.el;
    if (fromContainer) {
      return fromContainer;
    }

    // Inner elements of component instances are rendered from Twig and have
    // no V1 Backbone view, so getContainer(id) returns null. Fall back to
    // querying the preview iframe document directly so link-in-link
    // restriction still works for those elements.
    return queryPreviewDOMByElementId(id);
  } catch {
    return null;
  }
}
function queryPreviewDOMByElementId(id) {
  const previewDocument = window.elementor?.getPreviewContainer?.()?.view?.el?.ownerDocument;
  if (!previewDocument) {
    return null;
  }
  return previewDocument.querySelector(`[data-id="${id}"]`);
}
function isElementorElement(element) {
  return element.hasAttribute('data-id');
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/styles/consts.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/styles/consts.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENT_STYLE_CHANGE_EVENT: function() { return /* binding */ ELEMENT_STYLE_CHANGE_EVENT; },
/* harmony export */   styleRerenderEvents: function() { return /* binding */ styleRerenderEvents; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

const ELEMENT_STYLE_CHANGE_EVENT = 'elementor/editor-v2/editor-elements/style';
const styleRerenderEvents = [(0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/create'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/duplicate'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/import'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/paste'), (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.windowEvent)(ELEMENT_STYLE_CHANGE_EVENT)];

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/styles/create-element-style.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/styles/create-element-style.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementStyle: function() { return /* binding */ createElementStyle; },
/* harmony export */   shouldCreateNewLocalStyle: function() { return /* binding */ shouldCreateNewLocalStyle; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _sync_get_element_setting__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../sync/get-element-setting */ "./packages/packages/libs/editor-elements/src/sync/get-element-setting.ts");
/* harmony import */ var _sync_update_element_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../sync/update-element-settings */ "./packages/packages/libs/editor-elements/src/sync/update-element-settings.ts");
/* harmony import */ var _mutate_element_styles__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./mutate-element-styles */ "./packages/packages/libs/editor-elements/src/styles/mutate-element-styles.ts");





function createElementStyle({
  styleId,
  elementId,
  classesProp,
  label,
  meta,
  props,
  custom_css: customCss = null,
  additionalVariants = []
}) {
  let id = styleId;
  (0,_mutate_element_styles__WEBPACK_IMPORTED_MODULE_4__.mutateElementStyles)(elementId, styles => {
    id ??= (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__.generateId)(`e-${elementId}-`, Object.keys(styles));
    const variants = [{
      meta,
      props,
      custom_css: customCss
    }, ...additionalVariants];
    styles[id] = {
      id,
      label,
      type: 'class',
      variants
    };
    addStyleToClassesProp(elementId, classesProp, id);
    return styles;
  });
  return id;
}
function addStyleToClassesProp(elementId, classesProp, styleId) {
  const base = (0,_sync_get_element_setting__WEBPACK_IMPORTED_MODULE_2__.getElementSetting)(elementId, classesProp);
  const classesPropValue = _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.classesPropTypeUtil.create(prev => {
    return [...(prev ?? []), styleId];
  }, {
    base
  });
  (0,_sync_update_element_settings__WEBPACK_IMPORTED_MODULE_3__.updateElementSettings)({
    id: elementId,
    props: {
      [classesProp]: classesPropValue
    },
    withHistory: false
  });
}
function shouldCreateNewLocalStyle(payload) {
  return !payload?.styleId && !payload?.provider;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/styles/delete-element-style.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/styles/delete-element-style.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deleteElementStyle: function() { return /* binding */ deleteElementStyle; }
/* harmony export */ });
/* harmony import */ var _mutate_element_styles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./mutate-element-styles */ "./packages/packages/libs/editor-elements/src/styles/mutate-element-styles.ts");

function deleteElementStyle(elementId, styleId) {
  (0,_mutate_element_styles__WEBPACK_IMPORTED_MODULE_0__.mutateElementStyles)(elementId, styles => {
    // The object is deep cloned so mutating it is fine.
    // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
    delete styles[styleId];
    return styles;
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/styles/mutate-element-styles.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/styles/mutate-element-styles.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   mutateElementStyles: function() { return /* binding */ mutateElementStyles; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../errors */ "./packages/packages/libs/editor-elements/src/errors.ts");
/* harmony import */ var _sync_get_container__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../sync/get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _sync_update_element_settings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../sync/update-element-settings */ "./packages/packages/libs/editor-elements/src/sync/update-element-settings.ts");
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./consts */ "./packages/packages/libs/editor-elements/src/styles/consts.ts");






function mutateElementStyles(elementId, mutator) {
  const container = (0,_sync_get_container__WEBPACK_IMPORTED_MODULE_3__.getContainer)(elementId);
  if (!container) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_2__.ElementNotFoundError({
      context: {
        elementId
      }
    });
  }
  const oldIds = Object.keys(container.model.get('styles') ?? {});
  const styles = mutateStyles(container, mutator);
  const newIds = Object.keys(styles);
  clearRemovedClasses(container, {
    oldIds,
    newIds
  });
  notifyChanges();
  return styles;
}
function mutateStyles(container, mutator) {
  const styles = structuredClone(container.model.get('styles')) ?? {};
  const entries = Object.entries(mutator(styles)).map(([styleId, style]) => {
    style.variants = removeEmptyVariants(style);
    return [styleId, style];
  }).filter(([, style]) => {
    return !isStyleEmpty(style);
  });
  const mutatedStyles = Object.fromEntries(entries);
  container.model.set('styles', mutatedStyles);
  return mutatedStyles;
}
function removeEmptyVariants(style) {
  return style.variants.filter(({
    props,
    custom_css: customCss
  }) => Object.keys(props).length > 0 || customCss?.raw);
}
function isStyleEmpty(style) {
  return style.variants.length === 0;
}
function clearRemovedClasses(container, {
  oldIds,
  newIds
}) {
  const removedIds = oldIds.filter(id => !newIds.includes(id));
  const classesProps = structuredClone(getClassesProps(container));
  classesProps.forEach(([, prop]) => {
    prop.value = prop.value.filter(value => !removedIds.includes(value));
  });
  (0,_sync_update_element_settings__WEBPACK_IMPORTED_MODULE_4__.updateElementSettings)({
    id: container.id,
    props: Object.fromEntries(classesProps),
    withHistory: false
  });
}
function getClassesProps(container) {
  return Object.entries(container.settings.toJSON()).filter(prop => {
    const [, value] = prop;
    return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.classesPropTypeUtil.isValid(value);
  });
}
function notifyChanges() {
  dispatchChangeEvent();
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommandSync)('document/save/set-is-modified', {
    status: true
  }, {
    internal: true
  });
}
function dispatchChangeEvent() {
  window.dispatchEvent(new CustomEvent(_consts__WEBPACK_IMPORTED_MODULE_5__.ELEMENT_STYLE_CHANGE_EVENT));
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/styles/update-element-style.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/styles/update-element-style.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   updateElementStyle: function() { return /* binding */ updateElementStyle; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-styles */ "@elementor/editor-styles");
/* harmony import */ var _elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../errors */ "./packages/packages/libs/editor-elements/src/errors.ts");
/* harmony import */ var _mutate_element_styles__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./mutate-element-styles */ "./packages/packages/libs/editor-elements/src/styles/mutate-element-styles.ts");




function updateElementStyle(args) {
  (0,_mutate_element_styles__WEBPACK_IMPORTED_MODULE_3__.mutateElementStyles)(args.elementId, styles => {
    const style = styles[args.styleId];
    if (!style) {
      throw new _errors__WEBPACK_IMPORTED_MODULE_2__.StyleNotFoundError({
        context: {
          styleId: args.styleId
        }
      });
    }
    const variant = (0,_elementor_editor_styles__WEBPACK_IMPORTED_MODULE_1__.getVariantByMeta)(style, args.meta);
    const customCss = ('custom_css' in args ? args.custom_css : variant?.custom_css) ?? null;
    if (variant) {
      variant.props = (0,_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.mergeProps)(variant.props, args.props);
      variant.custom_css = customCss?.raw ? customCss : null;
    } else {
      style.variants.push({
        meta: args.meta,
        props: args.props,
        custom_css: customCss
      });
    }
    return styles;
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/create-element.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/create-element.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElement: function() { return /* binding */ createElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function createElement({
  container,
  model,
  options
}) {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/elements/create', {
    container,
    model,
    options: {
      edit: false,
      ...options
    }
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/create-elements.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/create-elements.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElements: function() { return /* binding */ createElements; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-element */ "./packages/packages/libs/editor-elements/src/sync/create-element.ts");
/* harmony import */ var _delete_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./delete-element */ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts");
/* harmony import */ var _resolve_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./resolve-element */ "./packages/packages/libs/editor-elements/src/sync/resolve-element.ts");





const createElements = ({
  elements,
  title,
  subtitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Item added', 'elementor')
}) => {
  const undoableCreate = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.undoable)({
    do: ({
      elements: elementsParam
    }) => {
      const createdElements = [];
      elementsParam.forEach(({
        container,
        options,
        ...elementParams
      }) => {
        const parentContainer = container.lookup?.() ?? container;
        if (!parentContainer) {
          throw new Error('Parent container not found');
        }
        const element = (0,_create_element__WEBPACK_IMPORTED_MODULE_2__.createElement)({
          container: parentContainer,
          ...elementParams,
          options: {
            ...options,
            useHistory: false
          }
        });
        createdElements.push({
          container: element,
          parentContainer,
          model: element.model?.toJSON() || {},
          options,
          containerId: element.id,
          parentContainerId: parentContainer.id
        });
      });
      return {
        createdElements
      };
    },
    undo: (_, {
      createdElements
    }) => {
      [...createdElements].reverse().forEach(({
        container,
        containerId,
        parentContainerId
      }) => {
        const freshContainer = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_4__.resolveContainer)(container, containerId);
        if (freshContainer) {
          (0,_delete_element__WEBPACK_IMPORTED_MODULE_3__.deleteElement)({
            container: freshContainer,
            options: {
              useHistory: false
            }
          });
          return;
        }
        (0,_resolve_element__WEBPACK_IMPORTED_MODULE_4__.removeModelFromParent)(parentContainerId, containerId);
      });
    },
    redo: (_, {
      createdElements
    }) => {
      const newElements = [];
      createdElements.forEach(({
        parentContainer,
        parentContainerId,
        model,
        options
      }) => {
        const freshParent = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_4__.resolveContainer)(parentContainer, parentContainerId);
        if (freshParent) {
          const element = (0,_create_element__WEBPACK_IMPORTED_MODULE_2__.createElement)({
            container: freshParent,
            model,
            options: {
              ...options,
              useHistory: false
            }
          });
          newElements.push({
            container: element,
            parentContainer: freshParent,
            model: element.model.toJSON(),
            options,
            containerId: element.id,
            parentContainerId: freshParent.id
          });
          return;
        }
        (0,_resolve_element__WEBPACK_IMPORTED_MODULE_4__.addModelToParent)(parentContainerId, model);
        newElements.push({
          container: parentContainer,
          parentContainer,
          model,
          options,
          containerId: model.id ?? '',
          parentContainerId
        });
      });
      return {
        createdElements: newElements
      };
    }
  }, {
    title,
    subtitle
  });
  return undoableCreate({
    elements
  });
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/delete-element.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deleteElement: function() { return /* binding */ deleteElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function deleteElement({
  container,
  options = {}
}) {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/elements/delete', {
    container,
    options
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/drop-element.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/drop-element.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dropElement: function() { return /* binding */ dropElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");


function dropElement({
  containerId,
  model,
  options
}) {
  const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_1__.getContainer)(containerId);
  if (!container) {
    throw new Error(`Container with ID "${containerId}" not found`);
  }
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('preview/drop', {
    container,
    model,
    options
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/duplicate-element.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/duplicate-element.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   duplicateElement: function() { return /* binding */ duplicateElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function duplicateElement({
  element,
  options = {}
}) {
  const currentIndex = element.view?._index ?? 0;
  const insertPosition = options.clone !== false ? currentIndex + 1 : undefined;
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/elements/duplicate', {
    container: element,
    options: {
      at: insertPosition,
      edit: false,
      ...options
    }
  });
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/duplicate-elements.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/duplicate-elements.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   duplicateElements: function() { return /* binding */ duplicateElements; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-element */ "./packages/packages/libs/editor-elements/src/sync/create-element.ts");
/* harmony import */ var _delete_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./delete-element */ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts");
/* harmony import */ var _duplicate_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./duplicate-element */ "./packages/packages/libs/editor-elements/src/sync/duplicate-element.ts");
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _resolve_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./resolve-element */ "./packages/packages/libs/editor-elements/src/sync/resolve-element.ts");







const duplicateElements = ({
  elementIds,
  title,
  subtitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Item duplicated', 'elementor'),
  onDuplicateElements,
  onRestoreElements
}) => {
  const undoableDuplicate = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.undoable)({
    do: ({
      elementIds: elementIdsToDuplicate
    }) => {
      onDuplicateElements?.();
      const duplicatedElements = [];
      elementIdsToDuplicate.forEach(elementId => {
        const originalContainer = (0,_get_container__WEBPACK_IMPORTED_MODULE_5__.getContainer)(elementId);
        if (!originalContainer?.parent) {
          return;
        }
        const duplicatedElement = (0,_duplicate_element__WEBPACK_IMPORTED_MODULE_4__.duplicateElement)({
          element: originalContainer,
          options: {
            useHistory: false
          }
        });
        if (!duplicatedElement.parent) {
          return;
        }
        duplicatedElements.push({
          container: duplicatedElement,
          parentContainer: duplicatedElement.parent,
          model: duplicatedElement.model.toJSON(),
          at: duplicatedElement.view?._index,
          containerId: duplicatedElement.id,
          parentContainerId: duplicatedElement.parent.id
        });
      });
      return {
        duplicatedElements
      };
    },
    undo: (_, {
      duplicatedElements
    }) => {
      onRestoreElements?.();
      [...duplicatedElements].reverse().forEach(({
        container,
        containerId,
        parentContainerId
      }) => {
        const freshContainer = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_6__.resolveContainer)(container, containerId);
        if (freshContainer) {
          (0,_delete_element__WEBPACK_IMPORTED_MODULE_3__.deleteElement)({
            container: freshContainer,
            options: {
              useHistory: false
            }
          });
          return;
        }
        (0,_resolve_element__WEBPACK_IMPORTED_MODULE_6__.removeModelFromParent)(parentContainerId, containerId);
      });
    },
    redo: (_, {
      duplicatedElements: previousElements
    }) => {
      onDuplicateElements?.();
      const duplicatedElements = [];
      previousElements.forEach(({
        parentContainer,
        parentContainerId,
        model,
        at
      }) => {
        const freshParent = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_6__.resolveContainer)(parentContainer, parentContainerId);
        if (freshParent) {
          const createdElement = (0,_create_element__WEBPACK_IMPORTED_MODULE_2__.createElement)({
            container: freshParent,
            model,
            options: {
              useHistory: false,
              clone: false,
              at
            }
          });
          duplicatedElements.push({
            container: createdElement,
            parentContainer: freshParent,
            model,
            at,
            containerId: createdElement.id,
            parentContainerId: freshParent.id
          });
          return;
        }
        (0,_resolve_element__WEBPACK_IMPORTED_MODULE_6__.addModelToParent)(parentContainerId, model, {
          at
        });
        duplicatedElements.push({
          container: parentContainer,
          parentContainer,
          model,
          at,
          containerId: model.id ?? '',
          parentContainerId
        });
      });
      return {
        duplicatedElements
      };
    }
  }, {
    title,
    subtitle
  });
  return undoableDuplicate({
    elementIds
  });
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/generate-element-id.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/generate-element-id.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   generateElementId: function() { return /* binding */ generateElementId; }
/* harmony export */ });
const generateElementId = () => {
  const extendedWindow = window;
  return extendedWindow.elementorCommon?.helpers?.getUniqueId?.() ?? `el-${Date.now()}-${Math.random().toString(36).substring(2, 9)}`;
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-all-descendants.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-all-descendants.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getAllDescendants: function() { return /* binding */ getAllDescendants; }
/* harmony export */ });
function getAllDescendants(container) {
  const children = (container.children ?? []).flatMap(child => getAllDescendants(child));
  return [container, ...children];
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-container.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-container.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getContainer: function() { return /* binding */ getContainer; },
/* harmony export */   selectElement: function() { return /* binding */ selectElement; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function getContainer(id) {
  const extendedWindow = window;
  const container = extendedWindow.elementor?.getContainer?.(id);
  return container ?? null;
}
const selectElement = elementId => {
  try {
    const container = getContainer(elementId);
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('document/elements/select', {
      container
    });
  } catch {}
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-current-document-container.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-current-document-container.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCurrentDocumentContainer: function() { return /* binding */ getCurrentDocumentContainer; }
/* harmony export */ });
function getCurrentDocumentContainer() {
  const extendedWindow = window;
  return extendedWindow.elementor?.documents?.getCurrent?.()?.container ?? null;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-current-document-id.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-current-document-id.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCurrentDocumentId: function() { return /* binding */ getCurrentDocumentId; }
/* harmony export */ });
function getCurrentDocumentId() {
  const extendedWindow = window;
  try {
    return extendedWindow.elementor?.documents?.getCurrentId?.() ?? null;
  } catch {
    return null;
  }
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-element-editor-settings.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-element-editor-settings.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementEditorSettings: function() { return /* binding */ getElementEditorSettings; }
/* harmony export */ });
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");

function getElementEditorSettings(elementId) {
  const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  return container?.model.get('editor_settings') ?? {};
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-element-interactions.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-element-interactions.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementInteractions: function() { return /* binding */ getElementInteractions; }
/* harmony export */ });
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");

function getElementInteractions(elementId) {
  const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  const interactions = container?.model?.get('interactions');
  if (typeof interactions === 'string') {
    return JSON.parse(interactions);
  }
  return interactions;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-element-label.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-element-label.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementLabel: function() { return /* binding */ getElementLabel; }
/* harmony export */ });
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../errors */ "./packages/packages/libs/editor-elements/src/errors.ts");
/* harmony import */ var _sync_get_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _sync_get_widgets_cache__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../sync/get-widgets-cache */ "./packages/packages/libs/editor-elements/src/sync/get-widgets-cache.ts");
/* harmony import */ var _get_selected_elements__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./get-selected-elements */ "./packages/packages/libs/editor-elements/src/sync/get-selected-elements.ts");




function getElementLabel(elementId) {
  if (!elementId) {
    elementId = (0,_get_selected_elements__WEBPACK_IMPORTED_MODULE_3__.getSelectedElements)()?.[0]?.id;
  }
  const container = (0,_sync_get_container__WEBPACK_IMPORTED_MODULE_1__.getContainer)(elementId);
  const type = container?.model.get('widgetType') || container?.model.get('elType');
  if (!type) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.ElementTypeNotExistsError({
      context: {
        elementId
      }
    });
  }
  const label = (0,_sync_get_widgets_cache__WEBPACK_IMPORTED_MODULE_2__.getWidgetsCache)()?.[type]?.title;
  if (!label) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.ElementLabelNotExistsError({
      context: {
        elementType: type
      }
    });
  }
  return label;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-element-setting.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-element-setting.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementSetting: function() { return /* binding */ getElementSetting; },
/* harmony export */   getElementSettings: function() { return /* binding */ getElementSettings; }
/* harmony export */ });
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");

const getElementSetting = (elementId, settingKey) => {
  const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementId);
  return container?.settings?.get(settingKey) ?? null;
};
const getElementSettings = (elementId, settingKey) => {
  return Object.fromEntries(settingKey.map(key => [key, getElementSetting(elementId, key)]));
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-element-styles.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-element-styles.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementStyles: function() { return /* binding */ getElementStyles; }
/* harmony export */ });
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");

const getElementStyles = elementID => {
  const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(elementID);
  return container?.model.get('styles') || null;
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-element-type.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-element-type.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElementType: function() { return /* binding */ getElementType; }
/* harmony export */ });
/* harmony import */ var _get_widgets_cache__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-widgets-cache */ "./packages/packages/libs/editor-elements/src/sync/get-widgets-cache.ts");

function getElementType(type) {
  if (!type) {
    return null;
  }
  const widgetsCache = (0,_get_widgets_cache__WEBPACK_IMPORTED_MODULE_0__.getWidgetsCache)();
  const elementType = widgetsCache?.[type];
  if (!elementType?.atomic_controls) {
    return null;
  }
  if (!elementType?.atomic_props_schema) {
    return null;
  }
  return {
    key: type,
    controls: elementType.atomic_controls,
    propsSchema: elementType.atomic_props_schema,
    dependenciesPerTargetMapping: elementType.dependencies_per_target_mapping ?? {},
    title: elementType.title,
    styleStates: elementType.atomic_style_states ?? [],
    pseudoStates: elementType.atomic_pseudo_states ?? []
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-elements.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-elements.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getElements: function() { return /* binding */ getElements; }
/* harmony export */ });
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _get_current_document_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-current-document-container */ "./packages/packages/libs/editor-elements/src/sync/get-current-document-container.ts");


function getElements(root) {
  const container = root ? (0,_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(root) : (0,_get_current_document_container__WEBPACK_IMPORTED_MODULE_1__.getCurrentDocumentContainer)();
  if (!container) {
    return [];
  }
  const children = [...(container.model.get('elements') ?? [])].flatMap(childModel => getElements(childModel.get('id')));
  return [container, ...children];
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-selected-elements.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-selected-elements.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getSelectedElement: function() { return /* binding */ getSelectedElement; },
/* harmony export */   getSelectedElements: function() { return /* binding */ getSelectedElements; }
/* harmony export */ });
/* harmony import */ var _get_element_type__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-element-type */ "./packages/packages/libs/editor-elements/src/sync/get-element-type.ts");

function getSelectedElements() {
  const extendedWindow = window;
  const selectedElements = extendedWindow.elementor?.selection?.getElements?.() ?? [];
  return selectedElements.reduce((acc, el) => {
    const type = el.model.get('widgetType') || el.model.get('elType');
    if (type) {
      acc.push({
        id: el.model.get('id'),
        type
      });
    }
    return acc;
  }, []);
}
function getSelectedElement() {
  const elements = getSelectedElements();
  const [element] = elements;
  const elementType = (0,_get_element_type__WEBPACK_IMPORTED_MODULE_0__.getElementType)(element?.type);
  if (elements.length !== 1 || !elementType || !element) {
    return {
      element: null,
      elementType: null
    };
  }
  return {
    element,
    elementType
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/get-widgets-cache.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/get-widgets-cache.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getWidgetsCache: function() { return /* binding */ getWidgetsCache; }
/* harmony export */ });
function getWidgetsCache() {
  const extendedWindow = window;
  return extendedWindow?.elementor?.widgetsCache || null;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/model-utils.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/model-utils.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   findChildRecursive: function() { return /* binding */ findChildRecursive; },
/* harmony export */   getElementChildren: function() { return /* binding */ getElementChildren; }
/* harmony export */ });
function findChildRecursive(model, predicate) {
  const childModels = model.get('elements') ?? [];
  for (const childModel of childModels) {
    if (predicate(childModel)) {
      return {
        model: childModel
      };
    }
    const found = findChildRecursive(childModel, predicate);
    if (found) {
      return found;
    }
  }
  return null;
}
function getElementChildren(model, predicate) {
  const childModels = model.get('elements') ?? [];
  return childModels.filter(childModel => !predicate || predicate(childModel)).map(childModel => ({
    model: childModel
  }));
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/move-element.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/move-element.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   moveElement: function() { return /* binding */ moveElement; }
/* harmony export */ });
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./create-element */ "./packages/packages/libs/editor-elements/src/sync/create-element.ts");
/* harmony import */ var _delete_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./delete-element */ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts");


function moveElement({
  element,
  targetContainer,
  options = {}
}) {
  const resolvedElement = element.lookup?.();
  const resolvedTarget = targetContainer.lookup?.();
  if (!resolvedElement) {
    throw new Error(`Element not found: ${element.id}`);
  }
  if (!resolvedTarget) {
    throw new Error(`Target container not found: ${targetContainer.id}`);
  }
  const modelToRecreate = resolvedElement.model.toJSON();
  (0,_delete_element__WEBPACK_IMPORTED_MODULE_1__.deleteElement)({
    container: resolvedElement,
    options: {
      ...options,
      useHistory: false
    }
  });
  const newContainer = (0,_create_element__WEBPACK_IMPORTED_MODULE_0__.createElement)({
    container: resolvedTarget,
    model: modelToRecreate,
    options: {
      edit: false,
      ...options,
      useHistory: false
    }
  });
  return newContainer;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/move-elements.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/move-elements.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   moveElements: function() { return /* binding */ moveElements; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _move_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./move-element */ "./packages/packages/libs/editor-elements/src/sync/move-element.ts");
/* harmony import */ var _resolve_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./resolve-element */ "./packages/packages/libs/editor-elements/src/sync/resolve-element.ts");




const moveElements = ({
  moves: movesToMake,
  title,
  subtitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Elements moved', 'elementor'),
  onMoveElements,
  onRestoreElements
}) => {
  const undoableMove = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.undoable)({
    do: ({
      moves
    }) => {
      const movedElements = [];
      onMoveElements?.();
      moves.forEach(({
        element,
        targetContainer,
        options
      }) => {
        const sourceElement = element.lookup?.() ?? element;
        const target = targetContainer.lookup?.() ?? targetContainer;
        if (!sourceElement) {
          throw new Error('Element not found');
        }
        if (!target) {
          throw new Error('Target container not found');
        }
        if (!sourceElement.parent) {
          throw new Error('Element has no parent container');
        }
        const originalContainer = sourceElement.parent;
        const originalIndex = originalContainer.children?.indexOf(sourceElement) ?? -1;
        const newElement = (0,_move_element__WEBPACK_IMPORTED_MODULE_2__.moveElement)({
          element: sourceElement,
          targetContainer: target,
          options: {
            ...options,
            useHistory: false
          }
        });
        movedElements.push({
          element: newElement,
          originalContainer,
          originalIndex,
          targetContainer: target,
          options,
          elementId: newElement.id,
          originalContainerId: originalContainer.id,
          targetContainerId: target.id
        });
      });
      return {
        movedElements
      };
    },
    undo: (_, {
      movedElements
    }) => {
      onRestoreElements?.();

      // Fallback for async-rendered nested elements whose views may not exist yet (ED-22825).
      [...movedElements].reverse().forEach(({
        element,
        elementId,
        originalContainer,
        originalContainerId,
        originalIndex
      }) => {
        const freshElement = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_3__.resolveContainer)(element, elementId);
        const freshOriginalContainer = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_3__.resolveContainer)(originalContainer, originalContainerId);
        if (!freshElement || !freshOriginalContainer) {
          return;
        }
        (0,_move_element__WEBPACK_IMPORTED_MODULE_2__.moveElement)({
          element: freshElement,
          targetContainer: freshOriginalContainer,
          options: {
            useHistory: false,
            at: originalIndex >= 0 ? originalIndex : undefined
          }
        });
      });
    },
    redo: (_, {
      movedElements
    }) => {
      const newMovedElements = [];
      onMoveElements?.();
      movedElements.forEach(({
        element,
        elementId,
        originalContainer,
        originalContainerId,
        originalIndex,
        targetContainer,
        targetContainerId,
        options
      }) => {
        const freshElement = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_3__.resolveContainer)(element, elementId);
        const freshOriginalContainer = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_3__.resolveContainer)(originalContainer, originalContainerId);
        const freshTarget = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_3__.resolveContainer)(targetContainer, targetContainerId);
        if (!freshElement || !freshOriginalContainer || !freshTarget) {
          return;
        }
        const newElement = (0,_move_element__WEBPACK_IMPORTED_MODULE_2__.moveElement)({
          element: freshElement,
          targetContainer: freshTarget,
          options: {
            ...options,
            useHistory: false
          }
        });
        newMovedElements.push({
          element: newElement,
          originalContainer: freshOriginalContainer,
          originalIndex,
          targetContainer: freshTarget,
          options,
          elementId: newElement.id,
          originalContainerId: freshOriginalContainer.id,
          targetContainerId: freshTarget.id
        });
      });
      return {
        movedElements: newMovedElements
      };
    }
  }, {
    title,
    subtitle
  });
  return undoableMove({
    moves: movesToMake
  });
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/remove-elements.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/remove-elements.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   removeElements: function() { return /* binding */ removeElements; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./create-element */ "./packages/packages/libs/editor-elements/src/sync/create-element.ts");
/* harmony import */ var _delete_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./delete-element */ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts");
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _resolve_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./resolve-element */ "./packages/packages/libs/editor-elements/src/sync/resolve-element.ts");






const removeElements = ({
  elementIds,
  title,
  subtitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Item removed', 'elementor'),
  onRemoveElements,
  onRestoreElements
}) => {
  const undoableRemove = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.undoable)({
    do: ({
      elementIds: elementIdsParam
    }) => {
      const removedElements = [];
      elementIdsParam.forEach(elementId => {
        const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_4__.getContainer)(elementId);
        if (container?.parent) {
          removedElements.push({
            container,
            parent: container.parent,
            model: container.model.toJSON(),
            at: container.view?._index ?? 0,
            containerId: container.id,
            parentId: container.parent.id
          });
        }
      });
      onRemoveElements?.();
      removedElements.forEach(({
        container
      }) => {
        (0,_delete_element__WEBPACK_IMPORTED_MODULE_3__.deleteElement)({
          container,
          options: {
            useHistory: false
          }
        });
      });
      return {
        removedElements
      };
    },
    undo: (_, {
      removedElements
    }) => {
      onRestoreElements?.();
      [...removedElements].reverse().forEach(({
        parent,
        parentId,
        model,
        at
      }) => {
        const freshParent = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_5__.resolveContainer)(parent, parentId);
        if (freshParent) {
          (0,_create_element__WEBPACK_IMPORTED_MODULE_2__.createElement)({
            container: freshParent,
            model,
            options: {
              useHistory: false,
              at
            }
          });
          return;
        }
        (0,_resolve_element__WEBPACK_IMPORTED_MODULE_5__.addModelToParent)(parentId, model, {
          at
        });
      });
    },
    redo: (_, {
      removedElements
    }) => {
      onRemoveElements?.();
      const newRemovedElements = [];
      removedElements.forEach(({
        container,
        parent,
        model,
        at,
        containerId,
        parentId
      }) => {
        const freshContainer = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_5__.resolveContainer)(container, containerId);
        const freshParent = (0,_resolve_element__WEBPACK_IMPORTED_MODULE_5__.resolveContainer)(parent, parentId);
        if (freshContainer && freshParent) {
          (0,_delete_element__WEBPACK_IMPORTED_MODULE_3__.deleteElement)({
            container: freshContainer,
            options: {
              useHistory: false
            }
          });
          newRemovedElements.push({
            container: freshContainer,
            parent: freshParent,
            model,
            at,
            containerId,
            parentId
          });
          return;
        }
        (0,_resolve_element__WEBPACK_IMPORTED_MODULE_5__.removeModelFromParent)(parentId, containerId);
        newRemovedElements.push({
          container,
          parent,
          model,
          at,
          containerId,
          parentId
        });
      });
      return {
        removedElements: newRemovedElements
      };
    }
  }, {
    title,
    subtitle
  });
  return undoableRemove({
    elementIds
  });
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/replace-element.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/replace-element.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   replaceElement: function() { return /* binding */ replaceElement; }
/* harmony export */ });
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../errors */ "./packages/packages/libs/editor-elements/src/errors.ts");
/* harmony import */ var _create_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./create-element */ "./packages/packages/libs/editor-elements/src/sync/create-element.ts");
/* harmony import */ var _delete_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./delete-element */ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts");
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");




const replaceElement = ({
  currentElementId,
  newElement,
  withHistory = true
}) => {
  const currentElementContainer = (0,_get_container__WEBPACK_IMPORTED_MODULE_3__.getContainer)(currentElementId);
  if (!currentElementContainer) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.ElementNotFoundError({
      context: {
        elementId: currentElementId
      }
    });
  }
  const {
    container,
    index
  } = getNewElementContainer(currentElementContainer, newElement);
  const newElementInstance = (0,_create_element__WEBPACK_IMPORTED_MODULE_1__.createElement)({
    container,
    model: newElement,
    options: {
      at: index,
      useHistory: withHistory
    }
  });
  (0,_delete_element__WEBPACK_IMPORTED_MODULE_2__.deleteElement)({
    container: currentElementContainer,
    options: {
      useHistory: withHistory
    }
  });
  return newElementInstance;
};
function getNewElementContainer(currentElementContainer, newElement) {
  const {
    parent
  } = currentElementContainer;
  if (!parent) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.ElementParentNotFoundError({
      context: {
        elementId: currentElementContainer.id
      }
    });
  }
  const elementIndex = currentElementContainer.view?._index ?? 0;
  if (elementIndex === -1) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.ElementIndexNotFoundError({
      context: {
        elementId: currentElementContainer.id
      }
    });
  }
  let location = {
    container: parent,
    index: elementIndex
  };
  if (parent.id === 'document' && newElement.elType === 'widget') {
    location = createWrapperForWidget(parent, elementIndex);
  }
  return location;
}
const DEFAULT_CONTAINER_TYPE = 'e-flexbox';
function createWrapperForWidget(parent, elementIndex) {
  const container = (0,_create_element__WEBPACK_IMPORTED_MODULE_1__.createElement)({
    container: parent,
    model: {
      elType: DEFAULT_CONTAINER_TYPE
    },
    options: {
      at: elementIndex,
      useHistory: false
    }
  });
  return {
    container,
    index: 0
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/resolve-element.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/resolve-element.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addModelToParent: function() { return /* binding */ addModelToParent; },
/* harmony export */   findModelInDocument: function() { return /* binding */ findModelInDocument; },
/* harmony export */   removeModelFromParent: function() { return /* binding */ removeModelFromParent; },
/* harmony export */   resolveContainer: function() { return /* binding */ resolveContainer; }
/* harmony export */ });
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");

function isConnected(container) {
  if (!container) {
    return false;
  }
  if (!container.view?.el) {
    return true;
  }
  return container.view.el.isConnected;
}
function resolveContainer(container, id) {
  const looked = container.lookup?.();
  if (isConnected(looked)) {
    return looked;
  }
  const byId = (0,_get_container__WEBPACK_IMPORTED_MODULE_0__.getContainer)(id);
  if (isConnected(byId)) {
    return byId;
  }
  return null;
}
function getDocumentUtils() {
  return window.$e?.components?.get?.('document')?.utils;
}
function findModelInDocument(id) {
  return getDocumentUtils()?.findModelById?.(id) ?? null;
}
function addModelToParent(parentId, childData, options) {
  return getDocumentUtils()?.addModelToParent?.(parentId, childData, options) ?? false;
}
function removeModelFromParent(parentId, childId) {
  return getDocumentUtils()?.removeModelFromParent?.(parentId, childId) ?? false;
}

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/update-element-editor-settings.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/update-element-editor-settings.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   updateElementEditorSettings: function() { return /* binding */ updateElementEditorSettings; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");


const updateElementEditorSettings = ({
  elementId,
  settings
}) => {
  const element = (0,_get_container__WEBPACK_IMPORTED_MODULE_1__.getContainer)(elementId);
  if (!element) {
    throw new Error(`Element with id ${elementId} not found`);
  }
  const editorSettings = element.model.get('editor_settings') ?? {};
  element.model.set('editor_settings', {
    ...editorSettings,
    ...settings
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/save/set-is-modified', {
    status: true
  }, {
    internal: true
  });
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/update-element-interactions.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/update-element-interactions.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   playElementInteractions: function() { return /* binding */ playElementInteractions; },
/* harmony export */   updateElementInteractions: function() { return /* binding */ updateElementInteractions; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");


const updateElementInteractions = ({
  elementId,
  interactions
}) => {
  const element = (0,_get_container__WEBPACK_IMPORTED_MODULE_1__.getContainer)(elementId);
  if (!element) {
    throw new Error(`Element with id ${elementId} not found`);
  }
  element.model.set('interactions', interactions);
  window.dispatchEvent(new CustomEvent('elementor/element/update_interactions'));
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/save/set-is-modified', {
    status: true
  }, {
    internal: true
  });
};
const playElementInteractions = (elementId, interactionId) => {
  window.top?.dispatchEvent(new CustomEvent('atomic/play_interactions', {
    detail: {
      elementId,
      interactionId
    }
  }));
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/sync/update-element-settings.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/sync/update-element-settings.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   updateElementSettings: function() { return /* binding */ updateElementSettings; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_container__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");


const updateElementSettings = ({
  id,
  props,
  withHistory = true
}) => {
  const container = (0,_get_container__WEBPACK_IMPORTED_MODULE_1__.getContainer)(id);
  if (!container) {
    return;
  }
  const args = {
    container,
    settings: {
      ...props
    }
  };
  if (withHistory) {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/elements/settings', args);
  } else {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/elements/set-settings', args, {
      internal: true
    });
  }
};

/***/ }),

/***/ "./packages/packages/libs/editor-elements/src/types.ts":
/*!*************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/types.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "@elementor/editor-props":
/*!**********************************************!*\
  !*** external ["elementorV2","editorProps"] ***!
  \**********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorProps"];

/***/ }),

/***/ "@elementor/editor-styles":
/*!***********************************************!*\
  !*** external ["elementorV2","editorStyles"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorStyles"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

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
/*!*************************************************************!*\
  !*** ./packages/packages/libs/editor-elements/src/index.ts ***!
  \*************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ELEMENT_STYLE_CHANGE_EVENT: function() { return /* reexport safe */ _styles_consts__WEBPACK_IMPORTED_MODULE_34__.ELEMENT_STYLE_CHANGE_EVENT; },
/* harmony export */   addModelToParent: function() { return /* reexport safe */ _sync_resolve_element__WEBPACK_IMPORTED_MODULE_14__.addModelToParent; },
/* harmony export */   createElement: function() { return /* reexport safe */ _sync_create_element__WEBPACK_IMPORTED_MODULE_6__.createElement; },
/* harmony export */   createElementStyle: function() { return /* reexport safe */ _styles_create_element_style__WEBPACK_IMPORTED_MODULE_35__.createElementStyle; },
/* harmony export */   createElements: function() { return /* reexport safe */ _sync_create_elements__WEBPACK_IMPORTED_MODULE_7__.createElements; },
/* harmony export */   deleteElement: function() { return /* reexport safe */ _sync_delete_element__WEBPACK_IMPORTED_MODULE_8__.deleteElement; },
/* harmony export */   deleteElementStyle: function() { return /* reexport safe */ _styles_delete_element_style__WEBPACK_IMPORTED_MODULE_36__.deleteElementStyle; },
/* harmony export */   dropElement: function() { return /* reexport safe */ _sync_drop_element__WEBPACK_IMPORTED_MODULE_9__.dropElement; },
/* harmony export */   duplicateElement: function() { return /* reexport safe */ _sync_duplicate_element__WEBPACK_IMPORTED_MODULE_10__.duplicateElement; },
/* harmony export */   duplicateElements: function() { return /* reexport safe */ _sync_duplicate_elements__WEBPACK_IMPORTED_MODULE_11__.duplicateElements; },
/* harmony export */   findChildRecursive: function() { return /* reexport safe */ _sync_model_utils__WEBPACK_IMPORTED_MODULE_23__.findChildRecursive; },
/* harmony export */   findModelInDocument: function() { return /* reexport safe */ _sync_resolve_element__WEBPACK_IMPORTED_MODULE_14__.findModelInDocument; },
/* harmony export */   generateElementId: function() { return /* reexport safe */ _sync_generate_element_id__WEBPACK_IMPORTED_MODULE_12__.generateElementId; },
/* harmony export */   getAllDescendants: function() { return /* reexport safe */ _sync_get_all_descendants__WEBPACK_IMPORTED_MODULE_22__.getAllDescendants; },
/* harmony export */   getAnchoredAncestorId: function() { return /* reexport safe */ _link_restriction__WEBPACK_IMPORTED_MODULE_33__.getAnchoredAncestorId; },
/* harmony export */   getAnchoredDescendantId: function() { return /* reexport safe */ _link_restriction__WEBPACK_IMPORTED_MODULE_33__.getAnchoredDescendantId; },
/* harmony export */   getContainer: function() { return /* reexport safe */ _sync_get_container__WEBPACK_IMPORTED_MODULE_13__.getContainer; },
/* harmony export */   getCurrentDocumentContainer: function() { return /* reexport safe */ _sync_get_current_document_container__WEBPACK_IMPORTED_MODULE_15__.getCurrentDocumentContainer; },
/* harmony export */   getCurrentDocumentId: function() { return /* reexport safe */ _sync_get_current_document_id__WEBPACK_IMPORTED_MODULE_16__.getCurrentDocumentId; },
/* harmony export */   getElementChildrenWithFallback: function() { return /* reexport safe */ _sync_model_utils__WEBPACK_IMPORTED_MODULE_23__.getElementChildren; },
/* harmony export */   getElementEditorSettings: function() { return /* reexport safe */ _sync_get_element_editor_settings__WEBPACK_IMPORTED_MODULE_17__.getElementEditorSettings; },
/* harmony export */   getElementInteractions: function() { return /* reexport safe */ _sync_get_element_interactions__WEBPACK_IMPORTED_MODULE_38__.getElementInteractions; },
/* harmony export */   getElementLabel: function() { return /* reexport safe */ _sync_get_element_label__WEBPACK_IMPORTED_MODULE_18__.getElementLabel; },
/* harmony export */   getElementSetting: function() { return /* reexport safe */ _sync_get_element_setting__WEBPACK_IMPORTED_MODULE_19__.getElementSetting; },
/* harmony export */   getElementSettings: function() { return /* reexport safe */ _sync_get_element_setting__WEBPACK_IMPORTED_MODULE_19__.getElementSettings; },
/* harmony export */   getElementStyles: function() { return /* reexport safe */ _sync_get_element_styles__WEBPACK_IMPORTED_MODULE_20__.getElementStyles; },
/* harmony export */   getElementType: function() { return /* reexport safe */ _sync_get_element_type__WEBPACK_IMPORTED_MODULE_21__.getElementType; },
/* harmony export */   getElements: function() { return /* reexport safe */ _sync_get_elements__WEBPACK_IMPORTED_MODULE_24__.getElements; },
/* harmony export */   getLinkInLinkRestriction: function() { return /* reexport safe */ _link_restriction__WEBPACK_IMPORTED_MODULE_33__.getLinkInLinkRestriction; },
/* harmony export */   getSelectedElements: function() { return /* reexport safe */ _sync_get_selected_elements__WEBPACK_IMPORTED_MODULE_25__.getSelectedElements; },
/* harmony export */   getWidgetsCache: function() { return /* reexport safe */ _sync_get_widgets_cache__WEBPACK_IMPORTED_MODULE_26__.getWidgetsCache; },
/* harmony export */   isElementAnchored: function() { return /* reexport safe */ _link_restriction__WEBPACK_IMPORTED_MODULE_33__.isElementAnchored; },
/* harmony export */   moveElement: function() { return /* reexport safe */ _sync_move_element__WEBPACK_IMPORTED_MODULE_27__.moveElement; },
/* harmony export */   moveElements: function() { return /* reexport safe */ _sync_move_elements__WEBPACK_IMPORTED_MODULE_28__.moveElements; },
/* harmony export */   playElementInteractions: function() { return /* reexport safe */ _sync_update_element_interactions__WEBPACK_IMPORTED_MODULE_39__.playElementInteractions; },
/* harmony export */   removeElements: function() { return /* reexport safe */ _sync_remove_elements__WEBPACK_IMPORTED_MODULE_29__.removeElements; },
/* harmony export */   removeModelFromParent: function() { return /* reexport safe */ _sync_resolve_element__WEBPACK_IMPORTED_MODULE_14__.removeModelFromParent; },
/* harmony export */   replaceElement: function() { return /* reexport safe */ _sync_replace_element__WEBPACK_IMPORTED_MODULE_30__.replaceElement; },
/* harmony export */   resolveContainer: function() { return /* reexport safe */ _sync_resolve_element__WEBPACK_IMPORTED_MODULE_14__.resolveContainer; },
/* harmony export */   selectElement: function() { return /* reexport safe */ _sync_get_container__WEBPACK_IMPORTED_MODULE_13__.selectElement; },
/* harmony export */   shouldCreateNewLocalStyle: function() { return /* reexport safe */ _styles_create_element_style__WEBPACK_IMPORTED_MODULE_35__.shouldCreateNewLocalStyle; },
/* harmony export */   styleRerenderEvents: function() { return /* reexport safe */ _styles_consts__WEBPACK_IMPORTED_MODULE_34__.styleRerenderEvents; },
/* harmony export */   updateElementEditorSettings: function() { return /* reexport safe */ _sync_update_element_editor_settings__WEBPACK_IMPORTED_MODULE_31__.updateElementEditorSettings; },
/* harmony export */   updateElementInteractions: function() { return /* reexport safe */ _sync_update_element_interactions__WEBPACK_IMPORTED_MODULE_39__.updateElementInteractions; },
/* harmony export */   updateElementSettings: function() { return /* reexport safe */ _sync_update_element_settings__WEBPACK_IMPORTED_MODULE_32__.updateElementSettings; },
/* harmony export */   updateElementStyle: function() { return /* reexport safe */ _styles_update_element_style__WEBPACK_IMPORTED_MODULE_37__.updateElementStyle; },
/* harmony export */   useElementChildren: function() { return /* reexport safe */ _hooks_use_element_children__WEBPACK_IMPORTED_MODULE_1__.useElementChildren; },
/* harmony export */   useElementEditorSettings: function() { return /* reexport safe */ _hooks_use_element_editor_settings__WEBPACK_IMPORTED_MODULE_2__.useElementEditorSettings; },
/* harmony export */   useParentElement: function() { return /* reexport safe */ _hooks_use_parent_element__WEBPACK_IMPORTED_MODULE_3__.useParentElement; },
/* harmony export */   useSelectedElement: function() { return /* reexport safe */ _hooks_use_selected_element__WEBPACK_IMPORTED_MODULE_4__.useSelectedElement; },
/* harmony export */   useSelectedElementSettings: function() { return /* reexport safe */ _hooks_use_selected_element_settings__WEBPACK_IMPORTED_MODULE_5__.useSelectedElementSettings; }
/* harmony export */ });
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./types */ "./packages/packages/libs/editor-elements/src/types.ts");
/* harmony import */ var _hooks_use_element_children__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-element-children */ "./packages/packages/libs/editor-elements/src/hooks/use-element-children.ts");
/* harmony import */ var _hooks_use_element_editor_settings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks/use-element-editor-settings */ "./packages/packages/libs/editor-elements/src/hooks/use-element-editor-settings.ts");
/* harmony import */ var _hooks_use_parent_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./hooks/use-parent-element */ "./packages/packages/libs/editor-elements/src/hooks/use-parent-element.ts");
/* harmony import */ var _hooks_use_selected_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./hooks/use-selected-element */ "./packages/packages/libs/editor-elements/src/hooks/use-selected-element.ts");
/* harmony import */ var _hooks_use_selected_element_settings__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./hooks/use-selected-element-settings */ "./packages/packages/libs/editor-elements/src/hooks/use-selected-element-settings.ts");
/* harmony import */ var _sync_create_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./sync/create-element */ "./packages/packages/libs/editor-elements/src/sync/create-element.ts");
/* harmony import */ var _sync_create_elements__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./sync/create-elements */ "./packages/packages/libs/editor-elements/src/sync/create-elements.ts");
/* harmony import */ var _sync_delete_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./sync/delete-element */ "./packages/packages/libs/editor-elements/src/sync/delete-element.ts");
/* harmony import */ var _sync_drop_element__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./sync/drop-element */ "./packages/packages/libs/editor-elements/src/sync/drop-element.ts");
/* harmony import */ var _sync_duplicate_element__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./sync/duplicate-element */ "./packages/packages/libs/editor-elements/src/sync/duplicate-element.ts");
/* harmony import */ var _sync_duplicate_elements__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./sync/duplicate-elements */ "./packages/packages/libs/editor-elements/src/sync/duplicate-elements.ts");
/* harmony import */ var _sync_generate_element_id__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./sync/generate-element-id */ "./packages/packages/libs/editor-elements/src/sync/generate-element-id.ts");
/* harmony import */ var _sync_get_container__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./sync/get-container */ "./packages/packages/libs/editor-elements/src/sync/get-container.ts");
/* harmony import */ var _sync_resolve_element__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./sync/resolve-element */ "./packages/packages/libs/editor-elements/src/sync/resolve-element.ts");
/* harmony import */ var _sync_get_current_document_container__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./sync/get-current-document-container */ "./packages/packages/libs/editor-elements/src/sync/get-current-document-container.ts");
/* harmony import */ var _sync_get_current_document_id__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./sync/get-current-document-id */ "./packages/packages/libs/editor-elements/src/sync/get-current-document-id.ts");
/* harmony import */ var _sync_get_element_editor_settings__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./sync/get-element-editor-settings */ "./packages/packages/libs/editor-elements/src/sync/get-element-editor-settings.ts");
/* harmony import */ var _sync_get_element_label__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./sync/get-element-label */ "./packages/packages/libs/editor-elements/src/sync/get-element-label.ts");
/* harmony import */ var _sync_get_element_setting__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./sync/get-element-setting */ "./packages/packages/libs/editor-elements/src/sync/get-element-setting.ts");
/* harmony import */ var _sync_get_element_styles__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./sync/get-element-styles */ "./packages/packages/libs/editor-elements/src/sync/get-element-styles.ts");
/* harmony import */ var _sync_get_element_type__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./sync/get-element-type */ "./packages/packages/libs/editor-elements/src/sync/get-element-type.ts");
/* harmony import */ var _sync_get_all_descendants__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./sync/get-all-descendants */ "./packages/packages/libs/editor-elements/src/sync/get-all-descendants.ts");
/* harmony import */ var _sync_model_utils__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./sync/model-utils */ "./packages/packages/libs/editor-elements/src/sync/model-utils.ts");
/* harmony import */ var _sync_get_elements__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./sync/get-elements */ "./packages/packages/libs/editor-elements/src/sync/get-elements.ts");
/* harmony import */ var _sync_get_selected_elements__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./sync/get-selected-elements */ "./packages/packages/libs/editor-elements/src/sync/get-selected-elements.ts");
/* harmony import */ var _sync_get_widgets_cache__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./sync/get-widgets-cache */ "./packages/packages/libs/editor-elements/src/sync/get-widgets-cache.ts");
/* harmony import */ var _sync_move_element__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./sync/move-element */ "./packages/packages/libs/editor-elements/src/sync/move-element.ts");
/* harmony import */ var _sync_move_elements__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./sync/move-elements */ "./packages/packages/libs/editor-elements/src/sync/move-elements.ts");
/* harmony import */ var _sync_remove_elements__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./sync/remove-elements */ "./packages/packages/libs/editor-elements/src/sync/remove-elements.ts");
/* harmony import */ var _sync_replace_element__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./sync/replace-element */ "./packages/packages/libs/editor-elements/src/sync/replace-element.ts");
/* harmony import */ var _sync_update_element_editor_settings__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./sync/update-element-editor-settings */ "./packages/packages/libs/editor-elements/src/sync/update-element-editor-settings.ts");
/* harmony import */ var _sync_update_element_settings__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./sync/update-element-settings */ "./packages/packages/libs/editor-elements/src/sync/update-element-settings.ts");
/* harmony import */ var _link_restriction__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./link-restriction */ "./packages/packages/libs/editor-elements/src/link-restriction.ts");
/* harmony import */ var _styles_consts__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./styles/consts */ "./packages/packages/libs/editor-elements/src/styles/consts.ts");
/* harmony import */ var _styles_create_element_style__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./styles/create-element-style */ "./packages/packages/libs/editor-elements/src/styles/create-element-style.ts");
/* harmony import */ var _styles_delete_element_style__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./styles/delete-element-style */ "./packages/packages/libs/editor-elements/src/styles/delete-element-style.ts");
/* harmony import */ var _styles_update_element_style__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./styles/update-element-style */ "./packages/packages/libs/editor-elements/src/styles/update-element-style.ts");
/* harmony import */ var _sync_get_element_interactions__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./sync/get-element-interactions */ "./packages/packages/libs/editor-elements/src/sync/get-element-interactions.ts");
/* harmony import */ var _sync_update_element_interactions__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./sync/update-element-interactions */ "./packages/packages/libs/editor-elements/src/sync/update-element-interactions.ts");
// types

// hooks






// utils


































}();
(window.elementorV2 = window.elementorV2 || {}).editorElements = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorElements?.init?.();
//# sourceMappingURL=editor-elements.js.map
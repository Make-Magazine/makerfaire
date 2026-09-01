/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-documents/src/components/logic-hooks.tsx":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/components/logic-hooks.tsx ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LogicHooks: function() { return /* binding */ LogicHooks; }
/* harmony export */ });
/* harmony import */ var _hooks_use_sync_document_title__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../hooks/use-sync-document-title */ "./packages/packages/core/editor-documents/src/hooks/use-sync-document-title.ts");

function LogicHooks() {
  (0,_hooks_use_sync_document_title__WEBPACK_IMPORTED_MODULE_0__["default"])();
  return null;
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/consts.ts":
/*!***************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/consts.ts ***!
  \***************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COMPONENT_DOCUMENT_TYPE: function() { return /* binding */ COMPONENT_DOCUMENT_TYPE; }
/* harmony export */ });
const COMPONENT_DOCUMENT_TYPE = 'elementor_component';

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/index.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/index.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useActiveDocument: function() { return /* reexport safe */ _use_active_document__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   useActiveDocumentActions: function() { return /* reexport safe */ _use_active_document_actions__WEBPACK_IMPORTED_MODULE_1__["default"]; },
/* harmony export */   useHostDocument: function() { return /* reexport safe */ _use_host_document__WEBPACK_IMPORTED_MODULE_2__["default"]; },
/* harmony export */   useNavigateToDocument: function() { return /* reexport safe */ _use_navigate_to_document__WEBPACK_IMPORTED_MODULE_3__["default"]; },
/* harmony export */   useOpenDocumentInNewTab: function() { return /* reexport safe */ _use_open_document_in_new_tab__WEBPACK_IMPORTED_MODULE_4__["default"]; }
/* harmony export */ });
/* harmony import */ var _use_active_document__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./use-active-document */ "./packages/packages/core/editor-documents/src/hooks/use-active-document.ts");
/* harmony import */ var _use_active_document_actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./use-active-document-actions */ "./packages/packages/core/editor-documents/src/hooks/use-active-document-actions.ts");
/* harmony import */ var _use_host_document__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-host-document */ "./packages/packages/core/editor-documents/src/hooks/use-host-document.ts");
/* harmony import */ var _use_navigate_to_document__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-navigate-to-document */ "./packages/packages/core/editor-documents/src/hooks/use-navigate-to-document.ts");
/* harmony import */ var _use_open_document_in_new_tab__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./use-open-document-in-new-tab */ "./packages/packages/core/editor-documents/src/hooks/use-open-document-in-new-tab.ts");






/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/use-active-document-actions.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/use-active-document-actions.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActiveDocumentActions; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _use_active_document__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-active-document */ "./packages/packages/core/editor-documents/src/hooks/use-active-document.ts");



function useActiveDocumentActions() {
  const document = (0,_use_active_document__WEBPACK_IMPORTED_MODULE_2__["default"])();
  const permalink = document?.links?.permalink ?? '';
  const save = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommand)('document/save/default'), []);
  const saveDraft = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommand)('document/save/draft'), []);
  const saveTemplate = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateOpenRoute)('library/save-template'), []);
  const copyAndShare = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    navigator.clipboard.writeText(permalink);
  }, [permalink]);
  return {
    save,
    saveDraft,
    saveTemplate,
    copyAndShare
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/use-active-document.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/use-active-document.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useActiveDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_selectors__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/selectors */ "./packages/packages/core/editor-documents/src/store/selectors.ts");


function useActiveDocument() {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store_selectors__WEBPACK_IMPORTED_MODULE_1__.selectActiveDocument);
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/use-host-document.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/use-host-document.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useHostDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_selectors__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/selectors */ "./packages/packages/core/editor-documents/src/store/selectors.ts");


function useHostDocument() {
  return (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__useSelector)(_store_selectors__WEBPACK_IMPORTED_MODULE_1__.selectHostDocument);
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/use-navigate-to-document.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/use-navigate-to-document.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useNavigateToDocument; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _sync_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../sync/utils */ "./packages/packages/core/editor-documents/src/sync/utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-documents/src/hooks/utils.ts");



function useNavigateToDocument() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async id => {
    const url = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getUpdateUrl)(id);
    await (0,_sync_utils__WEBPACK_IMPORTED_MODULE_1__.switchToDocument)(id, {
      setAsInitial: true
    });
    history.replaceState({}, '', url);
  }, []);
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/use-open-document-in-new-tab.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/use-open-document-in-new-tab.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useOpenDocumentInNewTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-documents/src/hooks/utils.ts");


function useOpenDocumentInNewTab() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(id => {
    const url = (0,_utils__WEBPACK_IMPORTED_MODULE_1__.getUpdateUrl)(id);
    window.open(url.href);
  }, []);
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/use-sync-document-title.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/use-sync-document-title.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useSyncDocumentTitle; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _use_active_document__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-active-document */ "./packages/packages/core/editor-documents/src/hooks/use-active-document.ts");
/* harmony import */ var _use_host_document__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-host-document */ "./packages/packages/core/editor-documents/src/hooks/use-host-document.ts");




function useSyncDocumentTitle() {
  const activeDocument = (0,_use_active_document__WEBPACK_IMPORTED_MODULE_2__["default"])();
  const hostDocument = (0,_use_host_document__WEBPACK_IMPORTED_MODULE_3__["default"])();
  const document = activeDocument && activeDocument.type.value !== 'kit' ? activeDocument : hostDocument;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    // Allow empty string as title.
    if (document?.title === undefined) {
      return;
    }

    // translators: %s: Document title.
    const title = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Edit "%s" with Elementor', 'elementor').replace('%s', document.title);
    window.document.title = title;
  }, [document?.title]);
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/hooks/utils.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/hooks/utils.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getUpdateUrl: function() { return /* binding */ getUpdateUrl; }
/* harmony export */ });
const getUpdateUrl = id => {
  const url = new URL(window.location.href);
  url.searchParams.set('post', id.toString());
  url.searchParams.delete('active-document');
  return url;
};

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/init.ts":
/*!*************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/init.ts ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_logic_hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/logic-hooks */ "./packages/packages/core/editor-documents/src/components/logic-hooks.tsx");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-documents/src/store/index.ts");
/* harmony import */ var _sync__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./sync */ "./packages/packages/core/editor-documents/src/sync/index.ts");





function init() {
  initStore();
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'documents-hooks',
    component: _components_logic_hooks__WEBPACK_IMPORTED_MODULE_2__.LogicHooks
  });
}
function initStore() {
  (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__registerSlice)(_store__WEBPACK_IMPORTED_MODULE_3__.slice);
  (0,_sync__WEBPACK_IMPORTED_MODULE_4__.syncStore)();
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/store/get-current-document.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/store/get-current-document.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getCurrentDocument: function() { return /* binding */ getCurrentDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_selectors__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/selectors */ "./packages/packages/core/editor-documents/src/store/selectors.ts");


function getCurrentDocument() {
  return (0,_store_selectors__WEBPACK_IMPORTED_MODULE_1__.selectActiveDocument)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__getState)());
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/store/index.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/store/index.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   slice: function() { return /* binding */ slice; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);

const initialState = {
  entities: {},
  activeId: null,
  hostId: null
};
function hasActiveEntity(state) {
  return !!(state.activeId && state.entities[state.activeId]);
}
const slice = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSlice)({
  name: 'documents',
  initialState,
  reducers: {
    init(state, {
      payload
    }) {
      state.entities = payload.entities;
      state.hostId = payload.hostId;
      state.activeId = payload.activeId;
    },
    activateDocument(state, action) {
      state.entities[action.payload.id] = action.payload;
      state.activeId = action.payload.id;
    },
    setAsHost(state, action) {
      state.hostId = action.payload;
    },
    updateActiveDocument(state, action) {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId] = {
          ...state.entities[state.activeId],
          ...action.payload
        };
      }
    },
    startSaving(state) {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId].isSaving = true;
      }
    },
    endSaving(state, action) {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId] = {
          ...action.payload,
          isSaving: false
        };
      }
    },
    startSavingDraft: state => {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId].isSavingDraft = true;
      }
    },
    endSavingDraft(state, action) {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId] = {
          ...action.payload,
          isSavingDraft: false
        };
      }
    },
    markAsDirty(state) {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId].isDirty = true;
      }
    },
    markAsPristine(state) {
      if (hasActiveEntity(state)) {
        state.entities[state.activeId].isDirty = false;
      }
    }
  }
});

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/store/selectors.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/store/selectors.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   selectActiveDocument: function() { return /* binding */ selectActiveDocument; },
/* harmony export */   selectHostDocument: function() { return /* binding */ selectHostDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_0__);

const selectEntities = state => state.documents.entities;
const selectActiveId = state => state.documents.activeId;
const selectHostId = state => state.documents.hostId;
const selectActiveDocument = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectEntities, selectActiveId, (entities, activeId) => activeId && entities[activeId] ? entities[activeId] : null);
const selectHostDocument = (0,_elementor_store__WEBPACK_IMPORTED_MODULE_0__.__createSelector)(selectEntities, selectHostId, (entities, hostId) => hostId && entities[hostId] ? entities[hostId] : null);

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/sync/index.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/sync/index.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   syncStore: function() { return /* reexport safe */ _sync_store__WEBPACK_IMPORTED_MODULE_0__.syncStore; }
/* harmony export */ });
/* harmony import */ var _sync_store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./sync-store */ "./packages/packages/core/editor-documents/src/sync/sync-store.ts");


/***/ }),

/***/ "./packages/packages/core/editor-documents/src/sync/sync-store.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/sync/sync-store.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   syncStore: function() { return /* binding */ syncStore; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/store */ "@elementor/store");
/* harmony import */ var _elementor_store__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_store__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../store */ "./packages/packages/core/editor-documents/src/store/index.ts");
/* harmony import */ var _store_selectors__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../store/selectors */ "./packages/packages/core/editor-documents/src/store/selectors.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils */ "./packages/packages/core/editor-documents/src/sync/utils.ts");






function syncStore() {
  syncInitialization();
  syncActiveDocument();
  syncOnDocumentSave();
  syncOnTitleChange();
  syncOnDocumentChange();
  syncOnExitToChange();
}
function syncInitialization() {
  const {
    init
  } = _store__WEBPACK_IMPORTED_MODULE_3__.slice.actions;
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.v1ReadyEvent)(), () => {
    const documentsManager = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsManager)();
    const entities = Object.entries(documentsManager.documents).reduce((acc, [id, document]) => {
      acc[id] = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.normalizeV1Document)(document);
      return acc;
    }, {});
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(init({
      entities,
      hostId: documentsManager.getInitialId(),
      activeId: documentsManager.getCurrentId()
    }));
  });
}
function syncActiveDocument() {
  const {
    activateDocument,
    setAsHost
  } = _store__WEBPACK_IMPORTED_MODULE_3__.slice.actions;
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('editor/documents/open'), () => {
    const documentsManager = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsManager)();
    const currentDocument = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.normalizeV1Document)(documentsManager.getCurrent());
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(activateDocument(currentDocument));
    if (documentsManager.getInitialId() === currentDocument.id) {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(setAsHost(currentDocument.id));
    }
  });
}
function syncOnDocumentSave() {
  const {
    startSaving,
    endSaving,
    startSavingDraft,
    endSavingDraft
  } = _store__WEBPACK_IMPORTED_MODULE_3__.slice.actions;
  const isDraft = e => {
    const event = e;

    /**
     * @see https://github.com/elementor/elementor/blob/5f815d40a/assets/dev/js/editor/document/save/hooks/ui/save/before.js#L18-L22
     */
    return event.args?.status === 'autosave';
  };
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandStartEvent)('document/save/save'), e => {
    if (isDraft(e)) {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(startSavingDraft());
      return;
    }
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(startSaving());
  });
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/save/save'), e => {
    const activeDocument = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.normalizeV1Document)((0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsManager)().getCurrent());
    if (isDraft(e)) {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(endSavingDraft(activeDocument));
    } else {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(endSaving(activeDocument));
    }
  });
}
function syncOnTitleChange() {
  const {
    updateActiveDocument
  } = _store__WEBPACK_IMPORTED_MODULE_3__.slice.actions;
  const updateTitle = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_2__.debounce)(e => {
    const event = e;
    if (!('post_title' in event.args?.settings)) {
      return;
    }
    const currentDocument = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsManager)().getCurrent();
    const newTitle = currentDocument.container.settings.get('post_title');
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(updateActiveDocument({
      title: newTitle
    }));
  }, 400);
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/settings'), updateTitle);
}
function syncOnExitToChange() {
  const {
    updateActiveDocument
  } = _store__WEBPACK_IMPORTED_MODULE_3__.slice.actions;
  const updateExitTo = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_2__.debounce)(e => {
    const event = e;
    if (!('exit_to' in event.args?.settings)) {
      return;
    }
    const currentDocument = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsManager)().getCurrent();
    const newExitTo = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsExitTo)(currentDocument);
    const permalink = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentPermalink)(currentDocument);
    const wpPreview = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentWpPreview)(currentDocument);
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(updateActiveDocument({
      links: {
        platformEdit: newExitTo,
        permalink,
        wpPreview
      }
    }));
  }, 400);
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/elements/settings'), updateExitTo);
}
function syncOnDocumentChange() {
  const {
    markAsDirty,
    markAsPristine
  } = _store__WEBPACK_IMPORTED_MODULE_3__.slice.actions;
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.commandEndEvent)('document/save/set-is-modified'), () => {
    // Skip the dirtiness check if the document is currently being saved, to prevent the UI
    // from showing a disabled publish button while there is a save process. The state will
    // be updated when the save process ends and the whole document state it normalized
    // (see `syncOnDocumentSave`)
    const isSaving = (0,_store_selectors__WEBPACK_IMPORTED_MODULE_4__.selectActiveDocument)((0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__getState)())?.isSaving;
    if (isSaving) {
      return;
    }
    const currentDocument = (0,_utils__WEBPACK_IMPORTED_MODULE_5__.getV1DocumentsManager)().getCurrent();
    if (currentDocument.editor.isChanged) {
      (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(markAsDirty());
      return;
    }
    (0,_elementor_store__WEBPACK_IMPORTED_MODULE_1__.__dispatch)(markAsPristine());
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/sync/utils.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/sync/utils.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getV1CurrentDocument: function() { return /* binding */ getV1CurrentDocument; },
/* harmony export */   getV1DocumentPermalink: function() { return /* binding */ getV1DocumentPermalink; },
/* harmony export */   getV1DocumentWpPreview: function() { return /* binding */ getV1DocumentWpPreview; },
/* harmony export */   getV1DocumentsExitTo: function() { return /* binding */ getV1DocumentsExitTo; },
/* harmony export */   getV1DocumentsManager: function() { return /* binding */ getV1DocumentsManager; },
/* harmony export */   invalidateDocumentData: function() { return /* binding */ invalidateDocumentData; },
/* harmony export */   isDocumentDirty: function() { return /* binding */ isDocumentDirty; },
/* harmony export */   normalizeV1Document: function() { return /* binding */ normalizeV1Document; },
/* harmony export */   reloadCurrentDocument: function() { return /* binding */ reloadCurrentDocument; },
/* harmony export */   setDocumentModifiedStatus: function() { return /* binding */ setDocumentModifiedStatus; },
/* harmony export */   switchToDocument: function() { return /* binding */ switchToDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_get_current_document__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../store/get-current-document */ "./packages/packages/core/editor-documents/src/store/get-current-document.ts");


function getV1DocumentsManager() {
  const documentsManager = window.elementor?.documents;
  if (!documentsManager) {
    throw new Error('Elementor Editor V1 documents manager not found');
  }
  return documentsManager;
}
function getV1DocumentsExitTo(documentData) {
  const exitPreference = window.elementor?.getPreferences?.('exit_to') || 'this_post';
  switch (exitPreference) {
    case 'dashboard':
      return documentData.config.urls.main_dashboard;
    case 'all_posts':
      return documentData.config.urls.all_post_type;
    case 'this_post':
    default:
      return documentData.config.urls.exit_to_dashboard;
  }
}
function getV1DocumentShowCopyAndShare(documentData) {
  return documentData?.config?.panel?.show_copy_and_share ?? false;
}
function getV1DocumentPermalink(documentData) {
  return documentData.config.urls.permalink ?? '';
}
function getV1DocumentWpPreview(documentData) {
  return documentData.config.urls.wp_preview ?? '';
}
function normalizeV1Document(documentData) {
  // Draft or autosave.
  const isUnpublishedRevision = documentData.config.revisions.current_id !== documentData.id;
  const exitToUrl = getV1DocumentsExitTo(documentData);
  return {
    id: documentData.id,
    title: documentData.container.settings.get('post_title'),
    type: {
      value: documentData.config.type,
      label: documentData.config.panel.title
    },
    status: {
      value: documentData.config.status.value,
      label: documentData.config.status.label
    },
    links: {
      permalink: getV1DocumentPermalink(documentData),
      wpPreview: getV1DocumentWpPreview(documentData),
      platformEdit: exitToUrl
    },
    isDirty: documentData.editor.isChanged || isUnpublishedRevision,
    isSaving: documentData.editor.isSaving,
    isSavingDraft: false,
    permissions: {
      allowAddingWidgets: documentData.config.panel?.allow_adding_widgets ?? true,
      showCopyAndShare: getV1DocumentShowCopyAndShare(documentData)
    },
    userCan: {
      publish: documentData.config.user.can_publish
    }
  };
}
function setDocumentModifiedStatus(status) {
  (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommandSync)('document/save/set-is-modified', {
    status
  }, {
    internal: true
  });
}
function getV1CurrentDocument() {
  return window.elementor?.documents?.getCurrent();
}
function isDocumentDirty(document) {
  const isDraft = document.status.value === 'draft';

  // When the document is published, but have draft version.
  const hasAutosave = document.revisions?.current_id !== document.id;
  return isDraft || hasAutosave;
}
function invalidateDocumentData(documentId) {
  const documentsManager = getV1DocumentsManager();
  documentsManager.invalidateCache(documentId);
}
function reloadCurrentDocument() {
  const currentDocument = (0,_store_get_current_document__WEBPACK_IMPORTED_MODULE_1__.getCurrentDocument)();
  if (!currentDocument?.id) {
    return Promise.resolve();
  }
  getV1DocumentsManager().invalidateCache();
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('editor/documents/switch', {
    id: currentDocument.id,
    shouldScroll: false,
    shouldNavigateToDefaultRoute: false
  });
}
function switchToDocument(documentId, options) {
  return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('editor/documents/switch', {
    id: documentId,
    ...options
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-documents/src/types.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/types.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/store":
/*!****************************************!*\
  !*** external ["elementorV2","store"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["store"];

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
/*!**************************************************************!*\
  !*** ./packages/packages/core/editor-documents/src/index.ts ***!
  \**************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   COMPONENT_DOCUMENT_TYPE: function() { return /* reexport safe */ _consts__WEBPACK_IMPORTED_MODULE_5__.COMPONENT_DOCUMENT_TYPE; },
/* harmony export */   __useActiveDocument: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useActiveDocument; },
/* harmony export */   __useActiveDocumentActions: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useActiveDocumentActions; },
/* harmony export */   __useHostDocument: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useHostDocument; },
/* harmony export */   __useNavigateToDocument: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useNavigateToDocument; },
/* harmony export */   __useOpenDocumentInNewTab: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_1__.useOpenDocumentInNewTab; },
/* harmony export */   getCurrentDocument: function() { return /* reexport safe */ _store_get_current_document__WEBPACK_IMPORTED_MODULE_3__.getCurrentDocument; },
/* harmony export */   getV1CurrentDocument: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.getV1CurrentDocument; },
/* harmony export */   getV1DocumentsManager: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.getV1DocumentsManager; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   invalidateDocumentData: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.invalidateDocumentData; },
/* harmony export */   isDocumentDirty: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.isDocumentDirty; },
/* harmony export */   reloadCurrentDocument: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.reloadCurrentDocument; },
/* harmony export */   setDocumentModifiedStatus: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.setDocumentModifiedStatus; },
/* harmony export */   slice: function() { return /* reexport safe */ _store__WEBPACK_IMPORTED_MODULE_2__.slice; },
/* harmony export */   switchToDocument: function() { return /* reexport safe */ _sync_utils__WEBPACK_IMPORTED_MODULE_4__.switchToDocument; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-documents/src/init.ts");
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks */ "./packages/packages/core/editor-documents/src/hooks/index.ts");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store */ "./packages/packages/core/editor-documents/src/store/index.ts");
/* harmony import */ var _store_get_current_document__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store/get-current-document */ "./packages/packages/core/editor-documents/src/store/get-current-document.ts");
/* harmony import */ var _sync_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./sync/utils */ "./packages/packages/core/editor-documents/src/sync/utils.ts");
/* harmony import */ var _consts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./consts */ "./packages/packages/core/editor-documents/src/consts.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./types */ "./packages/packages/core/editor-documents/src/types.ts");







}();
(window.elementorV2 = window.elementorV2 || {}).editorDocuments = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorDocuments?.init?.();
//# sourceMappingURL=editor-documents.js.map
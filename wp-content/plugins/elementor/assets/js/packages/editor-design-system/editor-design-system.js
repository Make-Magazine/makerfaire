/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-design-system/src/components/design-system-entrypoints.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/components/design-system-entrypoints.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DesignSystemEntrypoints: function() { return /* binding */ DesignSystemEntrypoints; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _design_system_panel__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../design-system-panel */ "./packages/packages/core/editor-design-system/src/design-system-panel.tsx");
/* harmony import */ var _initial_tab__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../initial-tab */ "./packages/packages/core/editor-design-system/src/initial-tab.ts");








const V1_ELEMENTS_PANEL_ROUTE = 'panel/elements/categories';
const EVENT_OPEN_VARIABLES = 'elementor/open-variables-manager';
const EVENT_OPEN_CLASSES = 'elementor/open-global-classes-manager';
const EVENT_TOGGLE = 'elementor/toggle-design-system';
const EVENT_SET_TAB = 'elementor/design-system/set-tab';
const ACTIVE_PANEL_PARAM = 'active-panel';
const PANEL_ID = 'design-system';
const LEGACY_GLOBAL_CLASSES_PANEL = 'global-classes-manager';
const LEGACY_VARIABLES_PANEL = 'variables-manager';
function DesignSystemEntrypoints() {
  const {
    open,
    close
  } = (0,_design_system_panel__WEBPACK_IMPORTED_MODULE_5__.usePanelActions)();
  const {
    isOpen
  } = (0,_design_system_panel__WEBPACK_IMPORTED_MODULE_5__.usePanelStatus)();
  const document = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const {
    save: saveDocument
  } = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocumentActions)();
  const {
    open: openSaveDialog,
    close: closeSaveDialog,
    isOpen: isSaveDialogOpen
  } = (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.useDialog)();
  const documentRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(document);
  documentRef.current = document;
  const pendingOpenRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const gatedOpen = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(onClean => {
    if (documentRef.current?.isDirty) {
      pendingOpenRef.current = onClean;
      openSaveDialog();
      return;
    }
    onClean();
  }, [openSaveDialog]);
  const handleSaveAndContinue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      await saveDocument();
      closeSaveDialog();
      pendingOpenRef.current?.();
      pendingOpenRef.current = null;
    } catch {
      // Keep dialog open;
    }
  }, [saveDocument, closeSaveDialog]);
  const handleStayHere = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    closeSaveDialog();
    pendingOpenRef.current = null;
  }, [closeSaveDialog]);
  const isOpenRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(isOpen);
  isOpenRef.current = isOpen;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handler = event => {
      const tab = event.detail?.tab;
      if (tab !== 'variables' && tab !== 'classes') {
        return;
      }
      if (isOpenRef.current && (0,_initial_tab__WEBPACK_IMPORTED_MODULE_6__.getActiveDesignSystemTab)() === tab) {
        void close();
        return;
      }
      if (isOpenRef.current) {
        window.dispatchEvent(new CustomEvent(EVENT_SET_TAB, {
          detail: {
            tab
          }
        }));
        return;
      }
      gatedOpen(() => {
        window.dispatchEvent(new CustomEvent(tab === 'variables' ? EVENT_OPEN_VARIABLES : EVENT_OPEN_CLASSES));
      });
    };
    window.addEventListener(EVENT_TOGGLE, handler);
    return () => {
      window.removeEventListener(EVENT_TOGGLE, handler);
    };
  }, [close, gatedOpen]);
  const pendingTabRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const [readyToOpenFromEvent, setReadyToOpenFromEvent] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (readyToOpenFromEvent) {
      setReadyToOpenFromEvent(false);
      void open();
    }
  }, [readyToOpenFromEvent, open]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.routeOpenEvent)(V1_ELEMENTS_PANEL_ROUTE), () => {
      const tab = pendingTabRef.current;
      if (tab) {
        pendingTabRef.current = null;
        (0,_initial_tab__WEBPACK_IMPORTED_MODULE_6__.setPendingDesignSystemTab)(tab);
        setReadyToOpenFromEvent(true);
      }
    });
  }, []);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const bind = (eventName, tab) => {
      const handler = () => {
        pendingTabRef.current = tab;
        (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.__privateOpenRoute)(V1_ELEMENTS_PANEL_ROUTE);
      };
      window.addEventListener(eventName, handler);
      return () => window.removeEventListener(eventName, handler);
    };
    const unlistenVariables = bind(EVENT_OPEN_VARIABLES, 'variables');
    const unlistenClasses = bind(EVENT_OPEN_CLASSES, 'classes');
    return () => {
      unlistenVariables();
      unlistenClasses();
    };
  }, []);
  const hasOpenedFromUrl = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const activePanel = urlParams.get(ACTIVE_PANEL_PARAM);
    if (!activePanel) {
      return;
    }
    let targetTab = null;
    if (activePanel === PANEL_ID) {
      const tab = urlParams.get('design-system-tab');
      targetTab = tab === 'classes' ? 'classes' : 'variables';
    } else if (activePanel === LEGACY_GLOBAL_CLASSES_PANEL) {
      targetTab = 'classes';
    } else if (activePanel === LEGACY_VARIABLES_PANEL) {
      targetTab = 'variables';
    } else {
      return;
    }
    const cleanup = (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.__privateListenTo)((0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.routeOpenEvent)(V1_ELEMENTS_PANEL_ROUTE), () => {
      if (hasOpenedFromUrl.current) {
        return;
      }
      hasOpenedFromUrl.current = true;
      requestAnimationFrame(() => {
        if (targetTab) {
          (0,_initial_tab__WEBPACK_IMPORTED_MODULE_6__.setPendingDesignSystemTab)(targetTab);
        }
        gatedOpen(() => void open());
      });
    });
    return cleanup;
  }, [open, gatedOpen]);
  return isSaveDialogOpen ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SaveChangesDialog, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SaveChangesDialog.Title, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('You have unsaved changes', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SaveChangesDialog.Content, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SaveChangesDialog.ContentText, {
    sx: {
      mb: 2
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)("To open the Design System, save your page first. You can't continue without saving.", 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.SaveChangesDialog.Actions, {
    actions: {
      cancel: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Stay here', 'elementor'),
        action: handleStayHere
      },
      confirm: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Save & Continue', 'elementor'),
        action: handleSaveAndContinue
      }
    }
  }))) : null;
}

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/components/design-system-header-menu.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/components/design-system-header-menu.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DesignSystemHeaderMenu: function() { return /* binding */ DesignSystemHeaderMenu; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-current-user */ "@elementor/editor-current-user");
/* harmony import */ var _elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _export_download__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../export/download */ "./packages/packages/core/editor-design-system/src/export/download.ts");
/* harmony import */ var _export_export_notifications__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../export/export-notifications */ "./packages/packages/core/editor-design-system/src/export/export-notifications.ts");
/* harmony import */ var _export_hooks_use_export_request__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../export/hooks/use-export-request */ "./packages/packages/core/editor-design-system/src/export/hooks/use-export-request.ts");
/* harmony import */ var _import_hooks_use_import_request__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../import/hooks/use-import-request */ "./packages/packages/core/editor-design-system/src/import/hooks/use-import-request.ts");
/* harmony import */ var _import_import_design_system_dialog__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../import/import-design-system-dialog */ "./packages/packages/core/editor-design-system/src/import/import-design-system-dialog.tsx");
/* harmony import */ var _import_tracking__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../import/tracking */ "./packages/packages/core/editor-design-system/src/import/tracking.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }













const POPUP_STATE_ID = 'design-system-header-menu';
const DesignSystemHeaderMenu = () => {
  const {
    isAdmin
  } = (0,_elementor_editor_current_user__WEBPACK_IMPORTED_MODULE_1__.useCurrentUserCapabilities)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.usePopupState)({
    variant: 'popover',
    popupId: POPUP_STATE_ID
  });
  const exportMutation = (0,_export_hooks_use_export_request__WEBPACK_IMPORTED_MODULE_9__.useExportRequest)();
  const isImporting = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_4__.useIsMutating)({
    mutationKey: [_import_hooks_use_import_request__WEBPACK_IMPORTED_MODULE_10__.IMPORT_DESIGN_SYSTEM_MUTATION_KEY]
  }) > 0;
  const isExporting = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_4__.useIsMutating)({
    mutationKey: [_export_hooks_use_export_request__WEBPACK_IMPORTED_MODULE_9__.EXPORT_DESIGN_SYSTEM_MUTATION_KEY]
  }) > 0;
  const isInProgress = isImporting || isExporting;
  const triggerProps = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.bindTrigger)(popupState);
  const handleImport = () => {
    popupState.close();
    (0,_import_tracking__WEBPACK_IMPORTED_MODULE_12__.trackDesignSystem)({
      event: 'importOpened'
    });
    (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.openDialog)({
      component: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_import_import_design_system_dialog__WEBPACK_IMPORTED_MODULE_11__.ImportDesignSystemDialog, {
        onClose: _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.closeDialog
      })
    });
  };
  const runExport = async () => {
    (0,_export_export_notifications__WEBPACK_IMPORTED_MODULE_8__.notifyExportInProgress)();
    try {
      const {
        blob,
        fileName
      } = await exportMutation.mutateAsync();
      (0,_export_download__WEBPACK_IMPORTED_MODULE_7__.downloadBlob)(blob, fileName);
      (0,_export_export_notifications__WEBPACK_IMPORTED_MODULE_8__.notifyExportSuccess)();
    } catch {
      (0,_export_export_notifications__WEBPACK_IMPORTED_MODULE_8__.notifyExportFailure)(runExport);
    }
  };
  const handleExport = () => {
    popupState.close();
    (0,_import_tracking__WEBPACK_IMPORTED_MODULE_12__.trackDesignSystem)({
      event: 'export'
    });
    void runExport();
  };
  const triggerLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Design system actions', 'elementor');
  const currentlyExportingLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)(`Export is in progress. The file will be downloaded when it's complete.`, 'elementor');
  const currentlyImportingLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)(`Import is in progress. You will receive a notification when it's complete.`, 'elementor');
  let tooltipLabel = triggerLabel;
  if (isInProgress) {
    tooltipLabel = isExporting ? currentlyExportingLabel : currentlyImportingLabel;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, isAdmin && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Tooltip, {
    title: tooltipLabel,
    placement: "top"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("span", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.IconButton, _extends({}, triggerProps, {
    size: "small",
    "aria-label": triggerLabel,
    disabled: isInProgress
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DotsVerticalIcon, {
    fontSize: "small"
  })))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.Menu, _extends({}, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.bindMenu)(popupState), {
    MenuListProps: {
      dense: true
    },
    PaperProps: {
      elevation: 6
    },
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'right'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.MenuItem, {
    onClick: handleImport,
    disabled: isImporting
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.ListItemIcon, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.UploadIcon, {
    fontSize: "tiny"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.ListItemText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Import', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.MenuItem, {
    onClick: handleExport,
    disabled: isExporting
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.ListItemIcon, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.DownloadIcon, {
    fontSize: "tiny"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_5__.ListItemText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Export', 'elementor')))));
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/components/design-system-panel-content.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/components/design-system-panel-content.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DesignSystemPanelContent: function() { return /* binding */ DesignSystemPanelContent; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_global_classes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-global-classes */ "@elementor/editor-global-classes");
/* harmony import */ var _elementor_editor_global_classes__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_global_classes__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_editor_variables__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/editor-variables */ "@elementor/editor-variables");
/* harmony import */ var _elementor_editor_variables__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _initial_tab__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../initial-tab */ "./packages/packages/core/editor-design-system/src/initial-tab.ts");
/* harmony import */ var _design_system_header_menu__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./design-system-header-menu */ "./packages/packages/core/editor-design-system/src/components/design-system-header-menu.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }











const stickyTabRowStyles = {
  position: 'sticky',
  zIndex: 1100,
  opacity: 1,
  backgroundColor: 'background.default',
  transition: 'top 300ms ease'
};
const EVENT_SET_TAB = 'elementor/design-system/set-tab';
const trackDesignSystemTabOpened = tab => {
  switch (tab) {
    case 'classes':
      (0,_elementor_editor_global_classes__WEBPACK_IMPORTED_MODULE_1__.trackGlobalClasses)({
        event: 'classManagerOpened',
        source: 'system-panel'
      });
      break;
    case 'variables':
      (0,_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_4__.trackVariablesManagerEvent)({
        action: 'openManager',
        source: 'system-panel'
      });
      break;
  }
};
function DesignSystemPanelContent({
  onRequestClose
}) {
  const [currentTab, setCurrentTab] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => (0,_initial_tab__WEBPACK_IMPORTED_MODULE_8__.getInitialDesignSystemTab)());
  const variablesCloseAttemptRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const classesCloseAttemptRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const isChainingRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  const {
    getTabProps,
    getTabPanelProps,
    getTabsProps
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.useTabs)(currentTab);
  const chainedThroughClasses = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (!isChainingRef.current && classesCloseAttemptRef.current) {
      isChainingRef.current = true;
      classesCloseAttemptRef.current();
      isChainingRef.current = false;
      return;
    }
    void onRequestClose();
  }, [onRequestClose]);
  const chainedThroughVariables = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (!isChainingRef.current && variablesCloseAttemptRef.current) {
      isChainingRef.current = true;
      variablesCloseAttemptRef.current();
      isChainingRef.current = false;
      return;
    }
    void onRequestClose();
  }, [onRequestClose]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    (0,_initial_tab__WEBPACK_IMPORTED_MODULE_8__.notifyDesignSystemTabChange)(currentTab);
  }, [currentTab]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const handler = event => {
      const tab = event.detail?.tab;
      if (!tab) {
        return;
      }
      setCurrentTab(tab);
      (0,_initial_tab__WEBPACK_IMPORTED_MODULE_8__.persistDesignSystemTab)(tab);
      (0,_initial_tab__WEBPACK_IMPORTED_MODULE_8__.notifyDesignSystemTabChange)(tab);
      trackDesignSystemTabOpened(tab);
    };
    window.addEventListener(EVENT_SET_TAB, handler);
    return () => {
      window.removeEventListener(EVENT_SET_TAB, handler);
    };
  }, []);
  const handleHeaderClose = () => {
    if (currentTab === 'variables' && variablesCloseAttemptRef.current) {
      variablesCloseAttemptRef.current();
      return;
    }
    if (currentTab === 'classes' && classesCloseAttemptRef.current) {
      classesCloseAttemptRef.current();
      return;
    }
    void onRequestClose();
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_3__.ThemeProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.Panel, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.PanelHeader, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
    p: 1,
    pl: 2,
    width: "100%",
    direction: "row",
    alignItems: "center",
    spacing: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.PanelHeaderTitle, {
    sx: {
      flex: 1,
      minWidth: 0
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Design system', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_design_system_header_menu__WEBPACK_IMPORTED_MODULE_9__.DesignSystemHeaderMenu, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.CloseButton, {
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Close', 'elementor'),
    sx: {
      flexShrink: 0
    },
    onClick: () => void handleHeaderClose()
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    sx: {
      display: 'flex',
      flexDirection: 'column',
      height: '100%',
      overflow: 'hidden',
      minHeight: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
    direction: "column",
    sx: {
      width: '100%',
      flex: 1,
      minHeight: 0,
      overflow: 'hidden'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Stack, {
    sx: {
      ...stickyTabRowStyles,
      top: 0,
      flexShrink: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Tabs, _extends({
    variant: "fullWidth",
    size: "small",
    sx: {
      mt: 0.5
    }
  }, getTabsProps(), {
    onChange: (e, newValue) => {
      getTabsProps().onChange(e, newValue);
      setCurrentTab(newValue);
      (0,_initial_tab__WEBPACK_IMPORTED_MODULE_8__.persistDesignSystemTab)(newValue);
      (0,_initial_tab__WEBPACK_IMPORTED_MODULE_8__.notifyDesignSystemTabChange)(newValue);
      trackDesignSystemTabOpened(newValue);
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Variables', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__.ColorFilterIcon, {
      fontSize: "small"
    }),
    iconPosition: "start"
  }, getTabProps('variables'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Classes', 'elementor'),
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_5__.ColorSwatchIcon, {
      fontSize: "small"
    }),
    iconPosition: "start"
  }, getTabProps('classes')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Divider, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Box, _extends({
    role: "tabpanel"
  }, getTabPanelProps('variables'), {
    sx: {
      flex: 1,
      minHeight: 0,
      display: currentTab === 'variables' ? 'flex' : 'none',
      flexDirection: 'column',
      overflow: 'hidden',
      pt: 1
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_variables__WEBPACK_IMPORTED_MODULE_4__.VariablesManagerPanelEmbedded, {
    onRequestClose: chainedThroughClasses,
    onExposeCloseAttempt: fn => {
      variablesCloseAttemptRef.current = fn;
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_6__.Box, _extends({
    role: "tabpanel"
  }, getTabPanelProps('classes'), {
    sx: {
      flex: 1,
      minHeight: 0,
      display: currentTab === 'classes' ? 'flex' : 'none',
      flexDirection: 'column',
      overflow: 'hidden',
      pt: 1
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_global_classes__WEBPACK_IMPORTED_MODULE_1__.ClassManagerPanelEmbedded, {
    onRequestClose: chainedThroughVariables,
    onExposeCloseAttempt: fn => {
      classesCloseAttemptRef.current = fn;
    },
    isActive: currentTab === 'classes'
  }))))));
}

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/design-system-panel.tsx":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/design-system-panel.tsx ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   panel: function() { return /* binding */ panel; },
/* harmony export */   usePanelActions: function() { return /* binding */ usePanelActions; },
/* harmony export */   usePanelStatus: function() { return /* binding */ usePanelStatus; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_design_system_panel_content__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/design-system-panel-content */ "./packages/packages/core/editor-design-system/src/components/design-system-panel-content.tsx");





const PANEL_ID = 'design-system';
const {
  panel,
  usePanelStatus,
  usePanelActions
} = (0,_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.__createPanel)({
  id: PANEL_ID,
  component: DesignSystemPanelRoot,
  allowedEditModes: ['edit', PANEL_ID],
  onOpen: () => {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.changeEditMode)(PANEL_ID);
  },
  onClose: async () => {
    (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_3__.changeEditMode)('edit');
    await (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.reloadCurrentDocument)();
  },
  isOpenPreviousElement: true
});
function DesignSystemPanelRoot() {
  const {
    close: closePanel
  } = usePanelActions();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_design_system_panel_content__WEBPACK_IMPORTED_MODULE_4__.DesignSystemPanelContent, {
    onRequestClose: closePanel
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/export/download.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/export/download.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   downloadBlob: function() { return /* binding */ downloadBlob; }
/* harmony export */ });
const downloadBlob = (blob, fileName) => {
  const url = URL.createObjectURL(blob);
  const anchor = document.createElement('a');
  anchor.href = url;
  anchor.download = fileName;
  anchor.rel = 'noopener';
  document.body.appendChild(anchor);
  anchor.click();
  document.body.removeChild(anchor);
  URL.revokeObjectURL(url);
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/export/export-notifications.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/export/export-notifications.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   notifyExportFailure: function() { return /* binding */ notifyExportFailure; },
/* harmony export */   notifyExportInProgress: function() { return /* binding */ notifyExportInProgress; },
/* harmony export */   notifyExportSuccess: function() { return /* binding */ notifyExportSuccess; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);


const EXPORT_STARTED_NOTIFICATION_ID = 'design-system-export-started';
const SUCCESS_NOTIFICATION_ID = 'design-system-export-succeeded';
const FAILURE_NOTIFICATION_ID = 'design-system-export-failed';
const notifyExportInProgress = () => {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)({
    id: EXPORT_STARTED_NOTIFICATION_ID,
    type: 'info',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Export in progress. Your file will download when it’s ready.', 'elementor')
  });
};
const notifyExportSuccess = () => {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.dismissNotification)(EXPORT_STARTED_NOTIFICATION_ID);
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)({
    id: SUCCESS_NOTIFICATION_ID,
    type: 'success',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Design system exported', 'elementor')
  });
};
const notifyExportFailure = onRetry => {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.dismissNotification)(EXPORT_STARTED_NOTIFICATION_ID);
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)({
    id: FAILURE_NOTIFICATION_ID,
    type: 'error',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Your design system export failed', 'elementor'),
    additionalActionProps: [{
      size: 'small',
      variant: 'outlined',
      color: 'error',
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Try again', 'elementor'),
      onClick: () => {
        (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.dismissNotification)(FAILURE_NOTIFICATION_ID);
        onRetry();
      }
    }]
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/export/hooks/use-export-request.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/export/hooks/use-export-request.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_EXPORT_FILE_NAME: function() { return /* binding */ DEFAULT_EXPORT_FILE_NAME; },
/* harmony export */   DesignSystemExportError: function() { return /* binding */ DesignSystemExportError; },
/* harmony export */   EXPORT_DESIGN_SYSTEM_MUTATION_KEY: function() { return /* binding */ EXPORT_DESIGN_SYSTEM_MUTATION_KEY; },
/* harmony export */   useExportRequest: function() { return /* binding */ useExportRequest; }
/* harmony export */ });
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_1__);


const EXPORT_BASE_PATH = 'elementor/v1/import-export-customization';
const EXPORT_DESIGN_SYSTEM_MUTATION_KEY = 'design-system-export';
const DEFAULT_EXPORT_FILE_NAME = 'design-system-export.zip';

// 2 minutes
const EXPORT_REQUEST_TIMEOUT_MS = 120_000;
const EXPORT_REQUEST_BODY = {
  include: ['settings'],
  kitInfo: {
    title: 'design-system',
    description: '',
    source: 'local'
  },
  customization: {
    settings: {
      theme: false,
      classes: true,
      variables: true
    }
  }
};
class DesignSystemExportError extends Error {
  constructor(cause) {
    super('Design system export failed');
    this.name = 'DesignSystemExportError';
    this.cause = cause;
  }
}
const useExportRequest = () => {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_1__.useMutation)({
    mutationKey: [EXPORT_DESIGN_SYSTEM_MUTATION_KEY],
    mutationFn: async () => {
      try {
        const {
          data
        } = await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_0__.httpService)().post(`${EXPORT_BASE_PATH}/export`, EXPORT_REQUEST_BODY, {
          timeout: EXPORT_REQUEST_TIMEOUT_MS
        });
        return {
          fileName: DEFAULT_EXPORT_FILE_NAME,
          blob: base64ToZipBlob(data.data.file)
        };
      } catch (error) {
        throw new DesignSystemExportError(error);
      }
    }
  });
};
const base64ToZipBlob = base64 => {
  const binary = atob(base64);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  return new Blob([bytes], {
    type: 'application/zip'
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/import/components/conflict-options.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/import/components/conflict-options.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConflictOptions: function() { return /* binding */ ConflictOptions; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



const getOptions = () => [{
  value: 'replace',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Replace existing values', 'elementor'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Imported design system values will overwrite existing variables and classes.', 'elementor')
}, {
  value: 'keep',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Keep existing values', 'elementor'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Existing variables and classes will not change.', 'elementor')
}];
const ConflictOptions = ({
  value,
  onChange
}) => {
  const options = getOptions();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 1
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "body1"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('How to handle conflicts with existing variables or classes?', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.RadioGroup, {
    value: value ?? '',
    onChange: (_, next) => onChange(next)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    spacing: 1
  }, options.map(option => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Card, {
    key: option.value,
    variant: "outlined"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.CardActionArea, {
    onClick: () => onChange(option.value)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    alignItems: "center",
    spacing: 2,
    padding: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Radio, {
    value: option.value,
    checked: value === option.value,
    inputProps: {
      'aria-label': option.title
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "column",
    spacing: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "subtitle2"
  }, option.title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    variant: "caption",
    color: "text.secondary"
  }, option.description)))))))));
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/import/hooks/use-dialog-state.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/import/hooks/use-dialog-state.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useDialogState: function() { return /* binding */ useDialogState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const initialState = {
  file: null,
  conflictStrategy: null
};
const useDialogState = () => {
  const [state, setState] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialState);
  const setFile = file => setState(prev => ({
    ...prev,
    file
  }));
  const setConflictStrategy = conflictStrategy => setState(prev => ({
    ...prev,
    conflictStrategy
  }));
  return {
    ...state,
    setFile,
    setConflictStrategy
  };
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/import/hooks/use-import-request.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/import/hooks/use-import-request.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DesignSystemUploadValidationError: function() { return /* binding */ DesignSystemUploadValidationError; },
/* harmony export */   IMPORT_DESIGN_SYSTEM_MUTATION_KEY: function() { return /* binding */ IMPORT_DESIGN_SYSTEM_MUTATION_KEY; },
/* harmony export */   useImportRequest: function() { return /* binding */ useImportRequest; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-canvas */ "@elementor/editor-canvas");
/* harmony import */ var _elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/http-client */ "@elementor/http-client");
/* harmony import */ var _elementor_http_client__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_http_client__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_3__);




const IMPORT_BASE_PATH = 'elementor/v1/import-export-customization';
const IMPORT_REQUEST_TIMEOUT_MS = 120_000;
const GLOBAL_CLASSES_RUNNER = 'global-classes';
const GLOBAL_VARIABLES_RUNNER = 'global-variables';
const SUPPORTED_RUNNERS = [GLOBAL_CLASSES_RUNNER, GLOBAL_VARIABLES_RUNNER];
const IMPORT_DESIGN_SYSTEM_MUTATION_KEY = 'design-system-import';
const useImportRequest = () => {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_3__.useMutation)({
    mutationKey: [IMPORT_DESIGN_SYSTEM_MUTATION_KEY],
    mutationFn: async ({
      file,
      conflictStrategy
    }) => {
      await (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_1__.__privateRunCommand)('document/save/auto', {
        force: true
      });
      const session = await uploadKit(file);
      const runners = await startImport(session, conflictStrategy);
      await runRunners(session, runners);
      window.dispatchEvent(new CustomEvent(_elementor_editor_canvas__WEBPACK_IMPORTED_MODULE_0__.GLOBAL_STYLES_IMPORTED_EVENT));
    }
  });
};
class DesignSystemUploadValidationError extends Error {
  constructor(cause) {
    super('Design system upload validation failed');
    this.name = 'DesignSystemUploadValidationError';
    this.cause = cause;
  }
}
const uploadKit = async file => {
  const formData = new FormData();
  formData.append('e_import_file', file);
  try {
    const {
      data
    } = await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_2__.httpService)().post(`${IMPORT_BASE_PATH}/upload`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      timeout: IMPORT_REQUEST_TIMEOUT_MS
    });
    return data.data.session;
  } catch (error) {
    throw new DesignSystemUploadValidationError(error);
  }
};
const startImport = async (session, conflictStrategy) => {
  const customization = {
    'design-system': {
      conflict_resolution: conflictStrategy === 'keep' ? 'skip' : 'replace'
    }
  };
  const {
    data
  } = await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_2__.httpService)().post(`${IMPORT_BASE_PATH}/import`, {
    session,
    include: ['design-system'],
    customization
  }, {
    timeout: IMPORT_REQUEST_TIMEOUT_MS
  });
  return (data.data.runners ?? []).filter(runner => SUPPORTED_RUNNERS.includes(runner));
};
const runRunners = async (session, runners) => {
  for (const runner of runners) {
    await (0,_elementor_http_client__WEBPACK_IMPORTED_MODULE_2__.httpService)().post(`${IMPORT_BASE_PATH}/import-runner`, {
      session,
      runner
    }, {
      timeout: IMPORT_REQUEST_TIMEOUT_MS
    });
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/import/import-design-system-dialog.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/import/import-design-system-dialog.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ImportDesignSystemDialog: function() { return /* binding */ ImportDesignSystemDialog; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_conflict_options__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/conflict-options */ "./packages/packages/core/editor-design-system/src/import/components/conflict-options.tsx");
/* harmony import */ var _hooks_use_dialog_state__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./hooks/use-dialog-state */ "./packages/packages/core/editor-design-system/src/import/hooks/use-dialog-state.ts");
/* harmony import */ var _hooks_use_import_request__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./hooks/use-import-request */ "./packages/packages/core/editor-design-system/src/import/hooks/use-import-request.ts");
/* harmony import */ var _import_notifications__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./import-notifications */ "./packages/packages/core/editor-design-system/src/import/import-notifications.tsx");
/* harmony import */ var _tracking__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./tracking */ "./packages/packages/core/editor-design-system/src/import/tracking.ts");










const ALLOWED_FILE_TYPES = ['application/zip'];
const FILE_INPUT_ACCEPT = 'application/zip,.zip';
// TODO: Replace with the actual server-enforced limit once finalized.
const MAX_FILE_SIZE_MB = 3;
const LEARN_MORE_URL = 'https://go.elementor.com/wp-dash-import-export-design-system/';
const reopenSelf = () => {
  (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
    event: 'importOpened'
  });
  (0,_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.openDialog)({
    component: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(ImportDesignSystemDialog, {
      onClose: _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.closeDialog
    })
  });
};
const ImportDesignSystemDialog = ({
  onClose
}) => {
  const {
    file,
    conflictStrategy,
    setFile,
    setConflictStrategy
  } = (0,_hooks_use_dialog_state__WEBPACK_IMPORTED_MODULE_6__.useDialogState)();
  const importMutation = (0,_hooks_use_import_request__WEBPACK_IMPORTED_MODULE_7__.useImportRequest)();
  const isImportEnabled = Boolean(file && conflictStrategy);
  const handleFileSelected = selected => {
    setFile(selected);
    (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
      event: 'fileSelected',
      file_type: _tracking__WEBPACK_IMPORTED_MODULE_9__.FILE_TYPE_DESIGN_SYSTEM
    });
  };
  const handleConflictChange = choice => {
    setConflictStrategy(choice);
    (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
      event: 'conflictChoice',
      choice
    });
  };
  const handleImport = async () => {
    if (!file || !conflictStrategy) {
      return;
    }
    (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
      event: 'confirmed',
      conflict_choice: conflictStrategy
    });
    (0,_import_notifications__WEBPACK_IMPORTED_MODULE_8__.notifyImportInProgress)();
    onClose();
    try {
      await importMutation.mutateAsync({
        file,
        conflictStrategy
      });
      (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
        event: 'imported'
      });
      (0,_import_notifications__WEBPACK_IMPORTED_MODULE_8__.notifyImportSuccess)();
    } catch (error) {
      if (error instanceof _hooks_use_import_request__WEBPACK_IMPORTED_MODULE_7__.DesignSystemUploadValidationError) {
        (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
          event: 'validationFailed',
          file_type: _tracking__WEBPACK_IMPORTED_MODULE_9__.FILE_TYPE_DESIGN_SYSTEM
        });
      } else {
        (0,_tracking__WEBPACK_IMPORTED_MODULE_9__.trackDesignSystem)({
          event: 'importFailed'
        });
      }
      (0,_import_notifications__WEBPACK_IMPORTED_MODULE_8__.notifyImportFailure)(reopenSelf);
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogHeader, {
    logo: false
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Import Design System', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    spacing: 3
  }, file ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.FileUploadRow, {
    file: file,
    onRemove: () => setFile(null)
  }) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.FileUploadDropzone, {
    onFileSelected: handleFileSelected,
    allowedFileTypes: ALLOWED_FILE_TYPES,
    accept: FILE_INPUT_ACCEPT,
    regionLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Design system file dropzone', 'elementor'),
    helperText: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.sprintf)(
    // translators: %d is the maximum file size in megabytes.
    (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('zip (max. %dMB)', 'elementor'), MAX_FILE_SIZE_MB)
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_components_conflict_options__WEBPACK_IMPORTED_MODULE_5__.ConflictOptions, {
    value: conflictStrategy,
    onChange: handleConflictChange
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    spacing: 0.5,
    alignItems: "center",
    justifyContent: "flex-start"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.HelpIcon, {
    sx: {
      fontSize: 16,
      color: 'text.tertiary'
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Link, {
    href: LEARN_MORE_URL,
    target: "_blank",
    rel: "noopener noreferrer",
    underline: "always",
    variant: "caption",
    color: "text.tertiary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Learn how design system imports work', 'elementor'))))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogActions, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    size: "medium",
    color: "secondary",
    onClick: onClose
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Cancel', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    size: "medium",
    variant: "contained",
    color: "primary",
    disabled: !isImportEnabled,
    onClick: handleImport
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Import', 'elementor'))));
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/import/import-notifications.tsx":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/import/import-notifications.tsx ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   notifyImportFailure: function() { return /* binding */ notifyImportFailure; },
/* harmony export */   notifyImportInProgress: function() { return /* binding */ notifyImportInProgress; },
/* harmony export */   notifyImportSuccess: function() { return /* binding */ notifyImportSuccess; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-notifications */ "@elementor/editor-notifications");
/* harmony import */ var _elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _hooks_use_import_request__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./hooks/use-import-request */ "./packages/packages/core/editor-design-system/src/import/hooks/use-import-request.ts");




const IMPORT_STARTED_NOTIFICATION_ID = 'design-system-import-started';
const SUCCESS_NOTIFICATION_ID = 'design-system-import-succeeded';
const FAILURE_NOTIFICATION_ID = 'design-system-import-failed';
const notifyImportInProgress = () => {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)({
    id: IMPORT_STARTED_NOTIFICATION_ID,
    type: 'info',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Import in Progress. You will be notified when the import is complete.', 'elementor')
  });
};
const notifyImportSuccess = () => {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.dismissNotification)(IMPORT_STARTED_NOTIFICATION_ID);
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)({
    id: SUCCESS_NOTIFICATION_ID,
    type: 'success',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Design system imported', 'elementor')
  });
};
const notifyImportFailure = onRetry => {
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.dismissNotification)(IMPORT_STARTED_NOTIFICATION_ID);
  (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.notify)({
    id: FAILURE_NOTIFICATION_ID,
    type: 'error',
    message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Your design system import failed', 'elementor'),
    additionalActionProps: [{
      size: 'small',
      variant: 'outlined',
      color: 'error',
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Try again', 'elementor'),
      onClick: () => {
        (0,_elementor_editor_notifications__WEBPACK_IMPORTED_MODULE_0__.dismissNotification)(FAILURE_NOTIFICATION_ID);
        const isImporting = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_1__.getQueryClient)().isMutating({
          mutationKey: [_hooks_use_import_request__WEBPACK_IMPORTED_MODULE_3__.IMPORT_DESIGN_SYSTEM_MUTATION_KEY]
        }) > 0;
        if (isImporting) {
          return;
        }
        onRetry();
      }
    }]
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/import/tracking.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/import/tracking.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FILE_TYPE_DESIGN_SYSTEM: function() { return /* binding */ FILE_TYPE_DESIGN_SYSTEM; },
/* harmony export */   trackDesignSystem: function() { return /* binding */ trackDesignSystem; }
/* harmony export */ });
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_0__);

const FILE_TYPE_DESIGN_SYSTEM = 'design_system';
const trackDesignSystem = payload => {
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_0__.getMixpanel)();
  const name = config?.names?.design_system?.[payload.event];
  if (!name) {
    return;
  }
  const {
    event,
    ...eventData
  } = payload;
  try {
    dispatchEvent?.(name, {
      event,
      ...eventData
    });
  } catch {
    // Silently ignore tracking errors so they don't break the user flow.
  }
};

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/init.ts":
/*!*****************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/init.ts ***!
  \*****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor */ "@elementor/editor");
/* harmony import */ var _elementor_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-app-bar */ "@elementor/editor-app-bar");
/* harmony import */ var _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_design_system_entrypoints__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/design-system-entrypoints */ "./packages/packages/core/editor-design-system/src/components/design-system-entrypoints.tsx");
/* harmony import */ var _design_system_panel__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./design-system-panel */ "./packages/packages/core/editor-design-system/src/design-system-panel.tsx");
/* harmony import */ var _use_open_design_system_toolbar__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./use-open-design-system-toolbar */ "./packages/packages/core/editor-design-system/src/use-open-design-system-toolbar.ts");






function init() {
  (0,_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_2__.__registerPanel)(_design_system_panel__WEBPACK_IMPORTED_MODULE_4__.panel);
  (0,_elementor_editor__WEBPACK_IMPORTED_MODULE_0__.injectIntoLogic)({
    id: 'design-system-entrypoints',
    component: _components_design_system_entrypoints__WEBPACK_IMPORTED_MODULE_3__.DesignSystemEntrypoints
  });
  _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_1__.toolsMenu.registerToggleAction({
    id: 'open-design-system-toolbar',
    priority: 21,
    useProps: _use_open_design_system_toolbar__WEBPACK_IMPORTED_MODULE_5__.useOpenDesignSystemToolbar
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/initial-tab.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/initial-tab.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getActiveDesignSystemTab: function() { return /* binding */ getActiveDesignSystemTab; },
/* harmony export */   getInitialDesignSystemTab: function() { return /* binding */ getInitialDesignSystemTab; },
/* harmony export */   notifyDesignSystemTabChange: function() { return /* binding */ notifyDesignSystemTabChange; },
/* harmony export */   persistDesignSystemTab: function() { return /* binding */ persistDesignSystemTab; },
/* harmony export */   setPendingDesignSystemTab: function() { return /* binding */ setPendingDesignSystemTab; }
/* harmony export */ });
const STORAGE_KEY = 'elementor_editor_design_system_active_tab';
function readStoredTab() {
  if (typeof window === 'undefined') {
    return 'variables';
  }
  try {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (raw === 'classes' || raw === 'variables') {
      return raw;
    }
  } catch {
    // Storage may be unavailable (private mode, quota, etc.).
  }
  return 'variables';
}
let pendingTabForOpen = null;
let activeTabInMemory = readStoredTab();
function setPendingDesignSystemTab(tab) {
  pendingTabForOpen = tab;
}
function getInitialDesignSystemTab() {
  if (pendingTabForOpen) {
    const t = pendingTabForOpen;
    pendingTabForOpen = null;
    activeTabInMemory = t;
    persistDesignSystemTab(t);
    return t;
  }
  const t = readStoredTab();
  activeTabInMemory = t;
  return t;
}
function notifyDesignSystemTabChange(tab) {
  activeTabInMemory = tab;
}
function getActiveDesignSystemTab() {
  return activeTabInMemory;
}
function persistDesignSystemTab(tab) {
  if (typeof window === 'undefined') {
    return;
  }
  try {
    window.localStorage.setItem(STORAGE_KEY, tab);
  } catch {
    // Ignore persistence failures.
  }
}

/***/ }),

/***/ "./packages/packages/core/editor-design-system/src/use-open-design-system-toolbar.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/use-open-design-system-toolbar.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useOpenDesignSystemToolbar: function() { return /* binding */ useOpenDesignSystemToolbar; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _design_system_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./design-system-panel */ "./packages/packages/core/editor-design-system/src/design-system-panel.tsx");
/* harmony import */ var _import_tracking__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./import/tracking */ "./packages/packages/core/editor-design-system/src/import/tracking.ts");
/* harmony import */ var _initial_tab__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./initial-tab */ "./packages/packages/core/editor-design-system/src/initial-tab.ts");


const EVENT_TOGGLE = 'elementor/toggle-design-system';



function useOpenDesignSystemToolbar() {
  const {
    isOpen
  } = (0,_design_system_panel__WEBPACK_IMPORTED_MODULE_2__.usePanelStatus)();
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Design System', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.DropletHalfFilledIcon,
    onClick: () => {
      if (!isOpen) {
        (0,_import_tracking__WEBPACK_IMPORTED_MODULE_3__.trackDesignSystem)({
          event: 'opened'
        });
      }
      const tab = (0,_initial_tab__WEBPACK_IMPORTED_MODULE_4__.getActiveDesignSystemTab)() ?? 'variables';
      window.dispatchEvent(new CustomEvent(EVENT_TOGGLE, {
        detail: {
          tab
        }
      }));
    },
    selected: isOpen
  };
}

/***/ }),

/***/ "@elementor/editor":
/*!*****************************************!*\
  !*** external ["elementorV2","editor"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editor"];

/***/ }),

/***/ "@elementor/editor-app-bar":
/*!***********************************************!*\
  !*** external ["elementorV2","editorAppBar"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorAppBar"];

/***/ }),

/***/ "@elementor/editor-canvas":
/*!***********************************************!*\
  !*** external ["elementorV2","editorCanvas"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCanvas"];

/***/ }),

/***/ "@elementor/editor-current-user":
/*!****************************************************!*\
  !*** external ["elementorV2","editorCurrentUser"] ***!
  \****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorCurrentUser"];

/***/ }),

/***/ "@elementor/editor-documents":
/*!**************************************************!*\
  !*** external ["elementorV2","editorDocuments"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorDocuments"];

/***/ }),

/***/ "@elementor/editor-global-classes":
/*!******************************************************!*\
  !*** external ["elementorV2","editorGlobalClasses"] ***!
  \******************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorGlobalClasses"];

/***/ }),

/***/ "@elementor/editor-notifications":
/*!******************************************************!*\
  !*** external ["elementorV2","editorNotifications"] ***!
  \******************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorNotifications"];

/***/ }),

/***/ "@elementor/editor-panels":
/*!***********************************************!*\
  !*** external ["elementorV2","editorPanels"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorPanels"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/editor-variables":
/*!**************************************************!*\
  !*** external ["elementorV2","editorVariables"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorVariables"];

/***/ }),

/***/ "@elementor/events":
/*!*****************************************!*\
  !*** external ["elementorV2","events"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["events"];

/***/ }),

/***/ "@elementor/http-client":
/*!*********************************************!*\
  !*** external ["elementorV2","httpClient"] ***!
  \*********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["httpClient"];

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

/***/ "@elementor/ui":
/*!*************************************!*\
  !*** external ["elementorV2","ui"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["ui"];

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
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-design-system/src/index.ts ***!
  \******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getInitialDesignSystemTab: function() { return /* reexport safe */ _initial_tab__WEBPACK_IMPORTED_MODULE_1__.getInitialDesignSystemTab; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; },
/* harmony export */   panel: function() { return /* reexport safe */ _design_system_panel__WEBPACK_IMPORTED_MODULE_2__.panel; },
/* harmony export */   persistDesignSystemTab: function() { return /* reexport safe */ _initial_tab__WEBPACK_IMPORTED_MODULE_1__.persistDesignSystemTab; },
/* harmony export */   usePanelActions: function() { return /* reexport safe */ _design_system_panel__WEBPACK_IMPORTED_MODULE_2__.usePanelActions; },
/* harmony export */   usePanelStatus: function() { return /* reexport safe */ _design_system_panel__WEBPACK_IMPORTED_MODULE_2__.usePanelStatus; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-design-system/src/init.ts");
/* harmony import */ var _initial_tab__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./initial-tab */ "./packages/packages/core/editor-design-system/src/initial-tab.ts");
/* harmony import */ var _design_system_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./design-system-panel */ "./packages/packages/core/editor-design-system/src/design-system-panel.tsx");



}();
(window.elementorV2 = window.elementorV2 || {}).editorDesignSystem = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorDesignSystem?.init?.();
//# sourceMappingURL=editor-design-system.js.map
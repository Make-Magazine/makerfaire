/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/libs/wp-media/src/errors.ts":
/*!*******************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/errors.ts ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   WpMediaNotAvailableError: function() { return /* binding */ WpMediaNotAvailableError; },
/* harmony export */   WpPluploadSettingsNotAvailableError: function() { return /* binding */ WpPluploadSettingsNotAvailableError; }
/* harmony export */ });
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/utils */ "@elementor/utils");
/* harmony import */ var _elementor_utils__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_utils__WEBPACK_IMPORTED_MODULE_0__);

const WpMediaNotAvailableError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'wp_media_not_available',
  message: '`wp.media` is not available, make sure the `media-models` handle is set in the dependencies array'
});
const WpPluploadSettingsNotAvailableError = (0,_elementor_utils__WEBPACK_IMPORTED_MODULE_0__.createError)({
  code: 'wp_plupload_settings_not_available',
  message: '`_wpPluploadSettings` is not available, make sure a wp media uploader is open'
});

/***/ }),

/***/ "./packages/packages/libs/wp-media/src/get-media-attachment.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/get-media-attachment.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fetchAttachmentFromWP: function() { return /* binding */ fetchAttachmentFromWP; },
/* harmony export */   getMediaAttachment: function() { return /* binding */ getMediaAttachment; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _media__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./media */ "./packages/packages/libs/wp-media/src/media.ts");
/* harmony import */ var _normalize__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./normalize */ "./packages/packages/libs/wp-media/src/normalize.ts");



async function fetchAttachmentFromWP(id) {
  const model = (0,_media__WEBPACK_IMPORTED_MODULE_1__["default"])().attachment(id);
  const wpAttachment = model.toJSON();
  const isFetched = 'url' in wpAttachment;
  if (isFetched) {
    return (0,_normalize__WEBPACK_IMPORTED_MODULE_2__["default"])(wpAttachment);
  }
  try {
    return (0,_normalize__WEBPACK_IMPORTED_MODULE_2__["default"])(await model.fetch());
  } catch {
    return null;
  }
}
async function getMediaAttachment({
  id
}) {
  if (!id) {
    return null;
  }
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.getQueryClient)();
  return queryClient.ensureQueryData({
    queryKey: ['wp-attachment', id],
    queryFn: () => fetchAttachmentFromWP(id)
  });
}

/***/ }),

/***/ "./packages/packages/libs/wp-media/src/hooks/use-wp-media-attachment.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/hooks/use-wp-media-attachment.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useWpMediaAttachment; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_media_attachment__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../get-media-attachment */ "./packages/packages/libs/wp-media/src/get-media-attachment.ts");


function useWpMediaAttachment(id) {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: ['wp-attachment', id],
    queryFn: () => (0,_get_media_attachment__WEBPACK_IMPORTED_MODULE_1__.fetchAttachmentFromWP)(id),
    enabled: !!id
  });
}

/***/ }),

/***/ "./packages/packages/libs/wp-media/src/hooks/use-wp-media-frame.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/hooks/use-wp-media-frame.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useWpMediaFrame; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _media__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../media */ "./packages/packages/libs/wp-media/src/media.ts");
/* harmony import */ var _normalize__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../normalize */ "./packages/packages/libs/wp-media/src/normalize.ts");
/* harmony import */ var _wp_plupload_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../wp-plupload-settings */ "./packages/packages/libs/wp-media/src/wp-plupload-settings.ts");




function useWpMediaFrame(options) {
  const frame = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();
  const open = (openOptions = {}) => {
    cleanupFrame(frame.current);
    frame.current = createFrame({
      ...options,
      ...openOptions
    });
    frame.current?.open();
  };
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    return () => {
      cleanupFrame(frame.current);
    };
  }, []);
  return {
    open
  };
}
function createFrame({
  onSelect,
  onSelectUrl,
  allowUrlImport,
  multiple,
  mediaTypes,
  selected,
  title,
  mode = 'browse',
  currentUrl,
  currentAlt
}) {
  const frame = (0,_media__WEBPACK_IMPORTED_MODULE_1__["default"])()({
    title,
    multiple,
    library: {
      type: getMimeTypes(mediaTypes)
    },
    ...(allowUrlImport ? {
      frame: 'post'
    } : {})
  }).on('open', () => {
    setTypeCaller(frame);
    applyMode(frame, mode, currentUrl, currentAlt);
    if (mode !== 'url') {
      applySelection(frame, selected);
    }
  }).on('insert select', () => select(frame, multiple, onSelect, onSelectUrl));
  if (allowUrlImport) {
    frame.on('ready open', () => restrictFrameMenu(frame));
  }
  handleExtensions(frame, mediaTypes);
  return frame;
}
function cleanupFrame(frame) {
  frame?.detach();
  frame?.remove();
}
function applyMode(frame, mode = 'browse', currentUrl, currentAlt) {
  if (mode === 'url') {
    frame.setState('embed');
    if (currentUrl || currentAlt) {
      // Defer so the toolbar region is initialized before change:url triggers its refresh callback.
      setTimeout(() => {
        if (currentUrl) {
          frame.state()?.props?.set('url', currentUrl);
        }
        if (currentAlt) {
          frame.state()?.props?.set('alt', currentAlt);
        }
      }, 0);
    }
  } else {
    frame.content.mode(mode);
  }
}
function applySelection(frame, selected) {
  const selectedAttachments = (typeof selected === 'number' ? [selected] : selected)?.filter(id => !!id).map(id => (0,_media__WEBPACK_IMPORTED_MODULE_1__["default"])().attachment(id));
  frame.state().get('selection').set(selectedAttachments || []);
}
function select(frame, multiple, onSelect, onSelectUrl) {
  const state = frame.state();
  if (state.get('id') === 'embed') {
    if (onSelectUrl) {
      const url = state.props?.get('url');
      const alt = state.props?.get('alt');
      if (url) {
        onSelectUrl(url, alt);
      }
    }
    return;
  }
  const attachments = state.get('selection').toJSON().map(_normalize__WEBPACK_IMPORTED_MODULE_2__["default"]);
  const onSelectFn = onSelect;
  onSelectFn(multiple ? attachments : attachments[0]);
}
const FRAME_MENU_ITEMS_TO_REMOVE = ['#menu-item-gallery', '#menu-item-featured-image', '#menu-item-playlist', '#menu-item-video-playlist'].join(',');
function restrictFrameMenu(frame) {
  frame.$el?.find(FRAME_MENU_ITEMS_TO_REMOVE)?.remove();
}
function setTypeCaller(frame) {
  frame.uploader.uploader.param('uploadTypeCaller', 'elementor-wp-media-upload');
}
function handleExtensions(frame, mediaTypes) {
  const defaultExtensions = (0,_wp_plupload_settings__WEBPACK_IMPORTED_MODULE_3__["default"])().defaults.filters.mime_types?.[0]?.extensions;

  // Set extensions by media types
  frame.on('ready', () => {
    (0,_wp_plupload_settings__WEBPACK_IMPORTED_MODULE_3__["default"])().defaults.filters.mime_types = [{
      extensions: getExtensions(mediaTypes)
    }];
  });

  // Restore default upload extensions
  frame.on('close', () => {
    (0,_wp_plupload_settings__WEBPACK_IMPORTED_MODULE_3__["default"])().defaults.filters.mime_types = defaultExtensions ? [{
      extensions: defaultExtensions
    }] : [];
  });
}
const imageExtensions = ['avif', 'bmp', 'gif', 'ico', 'jpe', 'jpeg', 'jpg', 'png', 'webp'];
const videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'avi', 'wmv', 'mpg', 'mpeg', '3gp', '3g2'];
function getMimeTypes(mediaTypes) {
  const mimeTypesPerType = {
    image: imageExtensions.map(extension => `image/${extension}`),
    svg: ['image/svg+xml'],
    video: ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-m4v', 'video/avi', 'video/x-ms-wmv', 'video/mpeg', 'video/3gpp', 'video/3gpp2']
  };
  return mediaTypes.reduce((prev, currentType) => {
    return prev.concat(mimeTypesPerType[currentType]);
  }, []);
}
function getExtensions(mediaTypes) {
  const extensionsPerType = {
    image: imageExtensions,
    svg: ['svg'],
    video: videoExtensions
  };
  const extensions = mediaTypes.reduce((prev, currentType) => {
    return prev.concat(extensionsPerType[currentType]);
  }, []);
  return extensions.join(',');
}

/***/ }),

/***/ "./packages/packages/libs/wp-media/src/media.ts":
/*!******************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/media.ts ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./errors */ "./packages/packages/libs/wp-media/src/errors.ts");

const wpMediaWindow = window;
/* harmony default export */ __webpack_exports__["default"] = (() => {
  if (!wpMediaWindow.wp?.media) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.WpMediaNotAvailableError();
  }
  return wpMediaWindow.wp.media;
});

/***/ }),

/***/ "./packages/packages/libs/wp-media/src/normalize.ts":
/*!**********************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/normalize.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ normalize; }
/* harmony export */ });
function normalize(attachment) {
  const {
    filesizeInBytes,
    filesizeHumanReadable,
    author,
    authorName,
    ...rest
  } = attachment;
  return {
    ...rest,
    filesize: {
      inBytes: filesizeInBytes,
      humanReadable: filesizeHumanReadable
    },
    author: {
      id: parseInt(author),
      name: authorName
    }
  };
}

/***/ }),

/***/ "./packages/packages/libs/wp-media/src/wp-plupload-settings.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/wp-plupload-settings.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _errors__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./errors */ "./packages/packages/libs/wp-media/src/errors.ts");

const wpPluploadSettingsWindow = window;
/* harmony default export */ __webpack_exports__["default"] = (() => {
  if (!wpPluploadSettingsWindow._wpPluploadSettings) {
    throw new _errors__WEBPACK_IMPORTED_MODULE_0__.WpPluploadSettingsNotAvailableError();
  }
  return wpPluploadSettingsWindow._wpPluploadSettings;
});

/***/ }),

/***/ "@elementor/query":
/*!****************************************!*\
  !*** external ["elementorV2","query"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["query"];

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
/*!******************************************************!*\
  !*** ./packages/packages/libs/wp-media/src/index.ts ***!
  \******************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getMediaAttachment: function() { return /* reexport safe */ _get_media_attachment__WEBPACK_IMPORTED_MODULE_2__.getMediaAttachment; },
/* harmony export */   useWpMediaAttachment: function() { return /* reexport safe */ _hooks_use_wp_media_attachment__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   useWpMediaFrame: function() { return /* reexport safe */ _hooks_use_wp_media_frame__WEBPACK_IMPORTED_MODULE_1__["default"]; }
/* harmony export */ });
/* harmony import */ var _hooks_use_wp_media_attachment__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./hooks/use-wp-media-attachment */ "./packages/packages/libs/wp-media/src/hooks/use-wp-media-attachment.ts");
/* harmony import */ var _hooks_use_wp_media_frame__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./hooks/use-wp-media-frame */ "./packages/packages/libs/wp-media/src/hooks/use-wp-media-frame.ts");
/* harmony import */ var _get_media_attachment__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get-media-attachment */ "./packages/packages/libs/wp-media/src/get-media-attachment.ts");



}();
(window.elementorV2 = window.elementorV2 || {}).wpMedia = __webpack_exports__;
/******/ })()
;
window.elementorV2.wpMedia?.init?.();
//# sourceMappingURL=wp-media.js.map
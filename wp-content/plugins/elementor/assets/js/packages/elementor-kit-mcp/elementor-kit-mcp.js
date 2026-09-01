/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/elementor-kit-mcp/src/elementor-kit-mcp-server.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/elementor-kit-mcp/src/elementor-kit-mcp-server.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementorKitServer: function() { return /* binding */ createElementorKitServer; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/elementor-mcp-common */ "@elementor/elementor-mcp-common");
/* harmony import */ var _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _mcp_description_resource__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./mcp-description-resource */ "./packages/packages/core/elementor-kit-mcp/src/mcp-description-resource.ts");




const RESOURCE_NAME_KIT_FONTS = 'elementor-kit-fonts';
const RESOURCE_URI_KIT_FONTS = 'elementor://kit/fonts';
const RESOURCE_NAME_KIT_SCHEMA = 'elementor-kit-schema';
const RESOURCE_URI_KIT_SCHEMA_TEMPLATE = 'elementor://kit/schema/{tab}';
const RESOURCE_NAME_KIT_SETTINGS = 'elementor-kit-settings';
const RESOURCE_URI_KIT_SETTINGS = 'elementor://kit/settings';
const VERSION = '2.0.0';
async function fetchKitFonts() {
  return (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.callWpApi)(`/angie/v1/elementor-kit/fonts`, 'GET');
}
async function fetchKitSchema() {
  return (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.callWpApi)(`/angie/v1/elementor-kit/schema`, 'GET');
}
async function fetchKitSettings() {
  return (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.callWpApi)(`/angie/v1/elementor-kit`, 'GET');
}
async function validateFonts(systemTypography, customTypography) {
  const fontsResponse = await fetchKitFonts();
  const availableFonts = Object.keys(fontsResponse.data.fonts || {});
  const invalidFonts = [];
  [systemTypography, customTypography].forEach(typography => {
    if (typography) {
      typography.forEach(item => {
        if (item.typography_font_family && !availableFonts.includes(item.typography_font_family)) {
          invalidFonts.push(item.typography_font_family);
        }
      });
    }
  });
  if (invalidFonts.length > 0) {
    return `Please use available fonts only. Invalid fonts: ${invalidFonts.join(', ')}. Available fonts: ${availableFonts.join(', ')}`;
  }
  return '';
}
function sanitizeMediaFields(obj) {
  const mediaFields = ['site_logo', 'site_icon'];
  const sanitized = {
    ...obj
  };
  for (const field of mediaFields) {
    if (field in sanitized && sanitized[field] === '') {
      sanitized[field] = 0;
    }
  }
  return sanitized;
}
const baseColorSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
  title: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
  color: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string()
});
const baseTypographySchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
  title: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
  typography_typography: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
  typography_font_family: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('Font family name that must be from the available Elementor fonts list. Use the get-fonts endpoint (/angie/v1/elementor-kit/fonts) to get the complete list of available fonts. Only use fonts that exist in this list to avoid validation errors.'),
  typography_font_weight: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
  typography_font_size: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
    unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
    size: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.number()
  }).optional(),
  typography_font_size_tablet: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
    unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
    size: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.number()
  }).optional(),
  typography_font_size_mobile: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
    unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
    size: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.number()
  }).optional(),
  typography_line_height: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.object({
    unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string(),
    size: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.number()
  }).optional()
}).passthrough();
const systemColorItemSchema = baseColorSchema.extend({
  _id: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.enum(['primary', 'secondary', 'text', 'accent'])
});
const systemTypographyItemSchema = baseTypographySchema.extend({
  _id: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.enum(['primary', 'secondary', 'text', 'accent'])
});
const customColorItemSchema = baseColorSchema.extend({
  _id: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string()
});
const customTypographyItemSchema = baseTypographySchema.extend({
  _id: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string()
});
const systemColorsSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.array(systemColorItemSchema).length(4);
const systemTypographySchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.array(systemTypographyItemSchema).length(4);
const customColorsSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.array(customColorItemSchema);
const customTypographySchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.array(customTypographyItemSchema);
const generalPatchSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.record(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.unknown()).describe('General patch object for any other Elementor kit settings like spacing, buttons, forms, layout settings, etc.');
const SERVER_INSTRUCTIONS = 'Manages Elementor global design system: colors, typography, and site identity.';
async function createElementorKitServer() {
  await (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.waitForElementorEditor)();
  const server = new _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.McpServer({
    name: 'elementor-kit-server',
    version: VERSION,
    title: 'Elementor Kit'
  }, {
    instructions: SERVER_INSTRUCTIONS,
    capabilities: {
      resources: {
        subscribe: true
      }
    }
  });
  (0,_mcp_description_resource__WEBPACK_IMPORTED_MODULE_3__.addKitDescriptionResource)(server);
  const getAvailableTabs = async () => {
    const kitSchema = await fetchKitSchema();
    return Object.keys(kitSchema.data || {});
  };
  server.registerResource(RESOURCE_NAME_KIT_FONTS, RESOURCE_URI_KIT_FONTS, {
    title: 'Elementor Kit Available Fonts',
    description: 'Complete list of all available font families that can be used in Elementor, including system fonts, Google fonts, and custom uploaded fonts'
  }, async uri => {
    const fontsResponse = await fetchKitFonts();
    return {
      contents: [{
        uri: uri.href,
        mimeType: 'application/json',
        text: JSON.stringify(fontsResponse, null, 2)
      }]
    };
  });
  server.registerResource(RESOURCE_NAME_KIT_SETTINGS, RESOURCE_URI_KIT_SETTINGS, {
    title: 'Elementor Kit Current Settings',
    description: 'Complete current Elementor global kit configuration including all system and custom colors, typography settings, spacing, buttons, forms, and other site-wide design settings'
  }, async uri => {
    const currentSettings = await fetchKitSettings();
    return {
      contents: [{
        uri: uri.href,
        mimeType: 'application/json',
        text: JSON.stringify(currentSettings, null, 2)
      }]
    };
  });
  server.registerResource(RESOURCE_NAME_KIT_SCHEMA, new _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.ResourceTemplate(RESOURCE_URI_KIT_SCHEMA_TEMPLATE, {
    list: async () => {
      const availableTabs = await getAvailableTabs();
      return {
        resources: availableTabs.map(tab => {
          return {
            uri: `elementor://kit/schema/${tab}`,
            name: `${RESOURCE_NAME_KIT_SCHEMA}-${tab}`,
            title: `Elementor Kit Schema - ${tab}`,
            description: `Schema definition for Elementor kit ${tab} settings tab`,
            mimeType: 'application/json'
          };
        })
      };
    }
  }), {
    title: 'Elementor Kit Schema',
    description: 'Complete schema definition for a specific Elementor kit settings tab, showing all available fields, their types, valid values, and configuration options'
  }, async (uri, variables) => {
    const tab = Array.isArray(variables.tab) ? variables.tab[0] : variables.tab;
    if (!tab) {
      throw new Error('Tab parameter is required');
    }
    const kitSchema = await fetchKitSchema();
    const tabSchema = kitSchema.data?.[tab];
    if (!tabSchema) {
      throw new Error(`No schema found for tab '${tab}'. Available tabs: ${Object.keys(kitSchema.data).join(', ')}`);
    }
    return {
      contents: [{
        uri: uri.toString(),
        mimeType: 'application/json',
        text: JSON.stringify(tabSchema, null, 2)
      }]
    };
  });
  server.registerTool('update-elementor-kit-settings-colors-and-fonts', {
    description: `This tool applies configuration changes to Elementor global kit settings that control site-wide design elements. Use this when you need to modify global colors, typography, spacing, buttons, form fields, or other theme-wide settings that affect the entire website appearance.

The tool will permanently update the site's global design settings and return a success confirmation with the updated configuration data.`,
    inputSchema: {
      systemColors: systemColorsSchema.optional().nullable().describe('System colors array with exactly 4 items having IDs: primary, secondary, text, accent'),
      systemTypography: systemTypographySchema.optional().nullable().describe('System typography array with exactly 4 items having IDs: primary, secondary, text, accent'),
      customColors: customColorsSchema.optional().nullable().describe('Custom colors array - flexible structure for additional color definitions'),
      customTypography: customTypographySchema.optional().nullable().describe('Custom typography array - flexible structure for additional font definitions (no color field needed)'),
      patchObject: generalPatchSchema.optional().nullable().describe('General patch object for any other Elementor kit settings like spacing, buttons, forms, layout settings, etc.'),
      confirmationMessage: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().describe('REQUIRED: Provide a clear explanation in **markdown format** of what Elementor kit settings will be changed and their potential impact on the site\'s global design. This message will be shown to the user before proceeding. Be specific about which design elements are being modified (colors, typography, spacing, etc.) and how they will affect the entire website. Examples: "You\'re about to update the global **primary color** from `blue` to `green`. This will change **buttons**, **links**, and **accent colors** throughout your entire site." or "You\'re about to change the **primary font** from `Roboto` to `Open Sans`. This will affect **headings** across all pages."')
    },
    annotations: {
      title: 'Update Elementor Kit Settings',
      destructiveHint: true
    },
    _meta: {
      'angie/requiredResources': [{
        uri: _mcp_description_resource__WEBPACK_IMPORTED_MODULE_3__.KIT_DESCRIPTION_URI,
        whenToUse: 'Read first for kit capabilities and limitations'
      }]
    }
  }, async ({
    systemColors,
    systemTypography,
    customColors,
    customTypography,
    patchObject,
    confirmationMessage
  }) => {
    (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.requireConfirmationMessage)(confirmationMessage, 'Elementor kit settings');
    const invalidFonts = await validateFonts(systemTypography, customTypography);
    if (invalidFonts) {
      throw new Error(invalidFonts);
    }
    const completePatchObject = {
      ...(systemColors && {
        system_colors: systemColors
      }),
      ...(systemTypography && {
        system_typography: systemTypography
      }),
      ...(customColors && {
        custom_colors: customColors
      }),
      ...(customTypography && {
        custom_typography: customTypography
      }),
      ...sanitizeMediaFields(patchObject || {})
    };
    if (Object.keys(completePatchObject).length === 0) {
      throw new Error('At least one update must be provided (systemColors, systemTypography, customColors, customTypography, or patchObject)');
    }
    const response = await (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.callWpApi)(`/angie/v1/elementor-kit`, 'POST', completePatchObject);
    return {
      content: [{
        type: 'text',
        text: JSON.stringify({
          success: true,
          message: 'Settings updated successfully',
          data: response,
          appliedSettings: completePatchObject
        }, null, 2)
      }]
    };
  });
  const sdk = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.getAngieSdk)();
  await sdk.waitForReady();
  sdk.registerLocalServer({
    server,
    version: VERSION,
    description: SERVER_INSTRUCTIONS,
    name: 'elementor-kit-server'
  });
  return server;
}

/***/ }),

/***/ "./packages/packages/core/elementor-kit-mcp/src/init.ts":
/*!**************************************************************!*\
  !*** ./packages/packages/core/elementor-kit-mcp/src/init.ts ***!
  \**************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_kit_mcp_server__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./elementor-kit-mcp-server */ "./packages/packages/core/elementor-kit-mcp/src/elementor-kit-mcp-server.ts");

function init() {
  (0,_elementor_kit_mcp_server__WEBPACK_IMPORTED_MODULE_0__.createElementorKitServer)();
}

/***/ }),

/***/ "./packages/packages/core/elementor-kit-mcp/src/mcp-description-resource.ts":
/*!**********************************************************************************!*\
  !*** ./packages/packages/core/elementor-kit-mcp/src/mcp-description-resource.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   KIT_DESCRIPTION: function() { return /* binding */ KIT_DESCRIPTION; },
/* harmony export */   KIT_DESCRIPTION_URI: function() { return /* binding */ KIT_DESCRIPTION_URI; },
/* harmony export */   addKitDescriptionResource: function() { return /* binding */ addKitDescriptionResource; }
/* harmony export */ });
const KIT_DESCRIPTION_URI = 'elementor://kit/server-description';
const KIT_DESCRIPTION = `## Elementor Kit Settings Management

### Capabilities:
**Global Design System:**
- Create, update, name, and delete global colors (both system and custom)
- Create, update, name, and delete global fonts (both system and custom)

**Site Identity:**
- Insert site logo
- Set site favicon
- Update site name
- Modify site description

### Limitations:
**Theme Style Settings:**
- Cannot set or update Elementor Theme Style settings including typography, buttons, images, form fields, Hello Theme header, or Hello Theme footer that affect the entire website appearance

**Site-Wide Settings:**
- Cannot set site-wide background (color or image)
- Cannot configure mobile browser background
- Cannot modify global layout settings such as content width, container padding, and widget gaps (the default space between widgets)

**Note**: Angie can adjust all of these layout properties at the container or page level - just not site-wide.`;
function addKitDescriptionResource(server) {
  server.registerResource('elementor-kit-server-description', KIT_DESCRIPTION_URI, {
    title: 'Elementor Kit Server Description',
    description: 'Elementor Kit capabilities and limitations',
    mimeType: 'text/plain'
  }, async uri => ({
    contents: [{
      uri: uri.href,
      mimeType: 'text/plain',
      text: KIT_DESCRIPTION
    }]
  }));
}

/***/ }),

/***/ "@elementor/editor-mcp":
/*!********************************************!*\
  !*** external ["elementorV2","editorMcp"] ***!
  \********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorMcp"];

/***/ }),

/***/ "@elementor/elementor-mcp-common":
/*!*****************************************************!*\
  !*** external ["elementorV2","elementorMcpCommon"] ***!
  \*****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["elementorMcpCommon"];

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

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
/*!***************************************************************!*\
  !*** ./packages/packages/core/elementor-kit-mcp/src/index.ts ***!
  \***************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementorKitServer: function() { return /* reexport safe */ _elementor_kit_mcp_server__WEBPACK_IMPORTED_MODULE_0__.createElementorKitServer; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_1__.init; }
/* harmony export */ });
/* harmony import */ var _elementor_kit_mcp_server__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./elementor-kit-mcp-server */ "./packages/packages/core/elementor-kit-mcp/src/elementor-kit-mcp-server.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./init */ "./packages/packages/core/elementor-kit-mcp/src/init.ts");


}();
(window.elementorV2 = window.elementorV2 || {}).elementorKitMcp = __webpack_exports__;
/******/ })()
;
window.elementorV2.elementorKitMcp?.init?.();
//# sourceMappingURL=elementor-kit-mcp.js.map
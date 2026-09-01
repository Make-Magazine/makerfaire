/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/apps/elementor-capabilities-mcp/src/elementor-capabilities-mcp-server.ts":
/*!*******************************************************************************************!*\
  !*** ./packages/apps/elementor-capabilities-mcp/src/elementor-capabilities-mcp-server.ts ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createElementorCapabilitiesServer: function() { return /* binding */ createElementorCapabilitiesServer; },
/* harmony export */   getSafeOrigin: function() { return /* binding */ getSafeOrigin; },
/* harmony export */   safeNavigateAfterResponse: function() { return /* binding */ safeNavigateAfterResponse; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/elementor-mcp-common */ "@elementor/elementor-mcp-common");
/* harmony import */ var _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _mcp_description_resource__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./mcp-description-resource */ "./packages/apps/elementor-capabilities-mcp/src/mcp-description-resource.ts");
/* harmony import */ var _pages_list_resource__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./pages-list-resource */ "./packages/apps/elementor-capabilities-mcp/src/pages-list-resource.ts");





// Constants
const ANGIE_REQUIRED_RESOURCES = 'angie/requiredResources';
const WP_PAGES_ENDPOINT = '/wp/v2/pages';
const ELEMENTOR_EDIT_MODE = 'builder';
const DEFAULT_PAGE_TITLE = 'New Page';
const ELEMENTOR_EDIT_ACTION = 'elementor';
const getSafeOrigin = () => {
  if (typeof window === 'undefined') {
    return '';
  }
  const url = new URL(window.location.href);
  const originParam = `${url.protocol}://${url.host}`;
  return originParam || '';
};
function safeNavigateAfterResponse(url) {
  try {
    const angieIframe = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.getAngieIframe)();

    // @ts-ignore: It's not null
    angieIframe.contentWindow.postMessage({
      type: _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.AngieMessageEvenetType.ANGIE_NAVIGATE_AFTER_RESPONSE,
      payload: {
        url
      }
    }, getSafeOrigin());
  } finally {
    setTimeout(() => {
      window.location.replace(url);
    }, 50);
  }
}

/**
 * Creates an MCP server that exposes Elementor capabilities when NOT in the editor
 * This helps Angie understand what Elementor can do and navigate users to the editor
 */
function createElementorCapabilitiesServer() {
  const server = new _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.McpServer({
    name: 'elementor-capabilities-server',
    version: '1.0.0',
    title: 'Elementor Capabilities'
  }, {
    instructions: `Provides Elementor page-building capabilities and navigates users to the editor.`,
    capabilities: {
      resources: {
        subscribe: true
      }
    }
  });
  (0,_pages_list_resource__WEBPACK_IMPORTED_MODULE_4__.addPagesListResource)(server);
  (0,_mcp_description_resource__WEBPACK_IMPORTED_MODULE_3__.addCapabilitiesDescriptionResource)(server);
  server.registerTool('navigate-to-elementor-editor', {
    description: `Navigate the user to the Elementor editor. Causes a full page reload — editor-related tools become available afterward.

Workflow: choose from the pages list resource → set pageId (edit) OR createNew=true (new page) with confirmationMessage.`,
    inputSchema: {
      pageId: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.number().optional().describe('The ID of the page to navigate to. Only provide after user has explicitly selected a page from the available pages list. When provided, confirmationMessage is REQUIRED.'),
      createNew: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.boolean().optional().describe('Set to true ONLY when user explicitly requests to create a new page. When true, confirmationMessage is REQUIRED. Default is false.'),
      newPageTitle: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().optional().describe('Title for the new page when createNew is true. Use what the user specified or default to "New Page".'),
      confirmationMessage: _elementor_schema__WEBPACK_IMPORTED_MODULE_2__.z.string().optional().describe("REQUIRED when pageId or createNew is provided. A clear message shown to user before navigation. Example: \"I'll open the 'Homepage' in Elementor editor so you can add a form widget. Ready to proceed?\" Omit when just fetching available pages.")
    },
    annotations: {
      title: 'Navigate to Elementor Editor',
      destructiveHint: true
    },
    _meta: {
      [ANGIE_REQUIRED_RESOURCES]: [{
        uri: _mcp_description_resource__WEBPACK_IMPORTED_MODULE_3__.CAPABILITIES_DESCRIPTION_URI,
        whenToUse: 'Read first for full capabilities and limitations guide'
      }, {
        uri: _pages_list_resource__WEBPACK_IMPORTED_MODULE_4__.PAGES_LIST_RESOURCE_URI,
        whenToUse: 'Always use this resource first to understand what pages are available before asking the user which page to edit'
      }]
    }
  }, async params => {
    try {
      if ((params.pageId || params.createNew) && !params.confirmationMessage) {
        throw new Error('confirmationMessage is required when navigating to a page (pageId or createNew provided)');
      }
      if (params.createNew) {
        return await handleCreateAndNavigate(params);
      }
      if (params.pageId) {
        return await handleNavigateToPage(params.pageId);
      }
      throw new Error('Either pageId or createNew must be provided. The pages list is available in the wp-pages-list resource.');
    } catch (error) {
      // eslint-disable-next-line no-console
      console.error('[Elementor Capabilities Server] navigation error:', error);
      throw new Error(`Error navigating to Elementor editor: ${error.message}`);
    }
  });
  return server;
}
async function handleCreateAndNavigate(params) {
  const title = params.newPageTitle || DEFAULT_PAGE_TITLE;
  const response = await (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.callWpApi)(WP_PAGES_ENDPOINT, 'POST', {
    title,
    status: 'draft',
    meta: {
      _elementor_edit_mode: ELEMENTOR_EDIT_MODE
    }
  });
  const newPage = response.data;
  if (!newPage || typeof newPage !== 'object' || !('id' in newPage)) {
    throw new Error('Invalid response from page creation API');
  }
  return await handleNavigateToPage(newPage.id);
}
async function handleNavigateToPage(pageId) {
  if (!pageId || pageId <= 0) {
    throw new Error('Invalid page ID');
  }
  const response = await (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_1__.callWpApi)(`${WP_PAGES_ENDPOINT}/${pageId}`, 'GET');
  const page = response.data;
  if (!page || typeof page !== 'object' || !('id' in page) || !('title' in page)) {
    throw new Error(`Invalid page data received for page ID ${pageId}`);
  }
  const editUrl = generateElementorEditUrl(pageId);
  if (!editUrl.startsWith(window.location.origin)) {
    throw new Error('Invalid navigation URL');
  }
  safeNavigateAfterResponse(editUrl);
  return {
    content: [{
      type: 'text',
      text: JSON.stringify({
        success: true,
        message: `Navigating to Elementor editor for page: ${page.title.rendered}`,
        pageId: page.id,
        pageTitle: page.title.rendered,
        editUrl,
        nextSteps: 'Once in the Elementor editor, you can use Elementor MCP tools for page settings, UI navigation, AI content generation, custom styling, and dynamic content.'
      }, null, 2)
    }]
  };
}
function generateElementorEditUrl(pageId) {
  const baseUrl = window.location.origin;
  return `${baseUrl}/wp-admin/post.php?post=${pageId}&action=${ELEMENTOR_EDIT_ACTION}`;
}

/***/ }),

/***/ "./packages/apps/elementor-capabilities-mcp/src/mcp-description-resource.ts":
/*!**********************************************************************************!*\
  !*** ./packages/apps/elementor-capabilities-mcp/src/mcp-description-resource.ts ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CAPABILITIES_DESCRIPTION: function() { return /* binding */ CAPABILITIES_DESCRIPTION; },
/* harmony export */   CAPABILITIES_DESCRIPTION_URI: function() { return /* binding */ CAPABILITIES_DESCRIPTION_URI; },
/* harmony export */   addCapabilitiesDescriptionResource: function() { return /* binding */ addCapabilitiesDescriptionResource; }
/* harmony export */ });
/* harmony import */ var _pages_list_resource__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./pages-list-resource */ "./packages/apps/elementor-capabilities-mcp/src/pages-list-resource.ts");

const CAPABILITIES_DESCRIPTION_URI = 'elementor://capabilities/server-description';
const CAPABILITIES_DESCRIPTION = `## Elementor Page Builder - Available When In Editor

This MCP provides information about Elementor's page building capabilities. When users ask about Elementor features, you should navigate them to the Elementor editor where full capabilities are available.

### Available Resources:
**Pages List Resource** (\`${_pages_list_resource__WEBPACK_IMPORTED_MODULE_0__.PAGES_LIST_RESOURCE_URI}\`):
- Lists all WordPress pages with IDs, titles, status, and metadata
- Use this to see what pages are available before navigation
- Updated automatically when pages are created or modified
- Useful for suggesting pages to edit or checking if a page exists

### Elementor Capabilities (Available In Editor):

**Page Management:**
- Manage page settings, saving and routing pages
- Control the editor UI, including switching between desktop, tablet, and mobile views

**Global Styles:**
- Work with global styles, helping manage shared design settings like colors and fonts across the site

**Element Management:**
- Create, edit, delete, duplicate, and move individual Elementor widgets and containers
- Update widget and container settings
- Insert text content with AI-powered text generation
- Create image widgets and assign images to elements

**AI-Powered Content Creation:**
- Generate and edit text and insert it into the page
- Generate images and place them on the canvas

**Custom Styling & Code:**
- Apply custom CSS to elements
- Generate supported code snippets

### Elementor Limitations (What Cannot Be Done):

**Element Management Limitations:**
- Cannot apply motion effects
- Cannot create fully designed or polished pages in a single step
- Cannot fully resolve responsiveness issues
- Layout generation (Copilot) is not currently available

**Theme Builder:**
- Cannot create or manage Theme Builder templates, including headers, footers, single posts, archives, products, loop items, or 404 pages
- Cannot set display conditions for templates
- Cannot configure popup triggers and advanced rules

**System Settings:**
- Cannot change Elementor system-level settings
- Cannot activate or work with Editor V4
- Cannot manage form submissions
- Cannot add custom fonts or icons
- Cannot manage user roles
- Cannot roll back Elementor versions
- Cannot place the site in maintenance mode
- Cannot export the website
- Cannot apply full website templates

**Code & Widgets:**
- Cannot register PHP code or create new custom widgets, though Angie may provide guidance, code snippets, or plugin suggestions where helpful

**Note**: While page names can include terms like "header" or "footer", these won't function as actual theme parts without Theme Builder access.

### How to Help Users:

**When users ask about Elementor features:**
1. Confirm what they want to accomplish
2. Check if it's a supported capability (see lists above)
3. **Get the page to edit:**
   - If user says "create new page" → Use createNew=true
   - Otherwise → Call tool with no parameters to get page list
   - Show pages to user: "Which page? Homepage, About, Contact..."
   - User chooses → Call tool again with that pageId
4. Once in editor, full Elementor MCP tools will be available

**Examples:**
- User: "Edit homepage" → Get page list → Find "Homepage" → Ask to confirm → Navigate
- User: "Create new page" → createNew=true → Navigate

**Examples of when to navigate to editor:**
- "Edit my homepage with Elementor"
- "Apply custom CSS to my page"
- "Generate text content for my page"
- "Work with dynamic content"

**Examples of when to navigate to editor (element management now supported):**
- "Add a heading widget" (Navigate to editor - element management supported)
- "Create a new section with containers" (Navigate to editor - container creation supported)
- "Change the color of a button" (Navigate to editor - widget settings supported)

**Examples of what Elementor CANNOT do (don't navigate):**
- "Add motion effects to my page" (Motion effects not supported)
- "Create a header template" (Theme Builder not available)
- "Set up a popup trigger" (Popup triggers and advanced rules not supported)
- "Change Elementor settings" (System-level settings restricted)
- "Add custom fonts" (Custom fonts not supported)
- "Create a custom widget" (Cannot register PHP code or custom widgets)

### Important Notes:
- Elementor tools are ONLY available when inside the Elementor editor
- This MCP server is for navigation and capability awareness
- Once in editor, the full 'elementor' MCP server with all tools becomes available
- Always verify if the requested feature is supported before navigating

**Important**: When users ask "What can Angie do?" or similar questions about Angie's general capabilities, use the \`what-can-angie-do\` tool from the knowledge MCP server instead of generating your own response.`;
function addCapabilitiesDescriptionResource(server) {
  server.registerResource('elementor-capabilities-server-description', CAPABILITIES_DESCRIPTION_URI, {
    title: 'Elementor capabilities and limitations',
    description: 'Full guide to Elementor MCP capabilities, limitations, workflows, and when to navigate to the editor.'
  }, async uri => ({
    contents: [{
      uri: uri.href,
      mimeType: 'text/plain',
      text: CAPABILITIES_DESCRIPTION
    }]
  }));
}

/***/ }),

/***/ "./packages/apps/elementor-capabilities-mcp/src/pages-list-resource.ts":
/*!*****************************************************************************!*\
  !*** ./packages/apps/elementor-capabilities-mcp/src/pages-list-resource.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PAGES_LIST_RESOURCE_URI: function() { return /* binding */ PAGES_LIST_RESOURCE_URI; },
/* harmony export */   addPagesListResource: function() { return /* binding */ addPagesListResource; }
/* harmony export */ });
/* harmony import */ var _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/elementor-mcp-common */ "@elementor/elementor-mcp-common");
/* harmony import */ var _elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_0__);

const PAGES_LIST_RESOURCE_URI = 'wp://pages/list';
function addPagesListResource(server) {
  server.registerResource('wp-pages-list', PAGES_LIST_RESOURCE_URI, {
    title: 'List of wordpress pages',
    description: 'List of all WordPress pages with their IDs, titles, status, and metadata. Use this to get available pages before navigation or editing.'
  }, async uri => {
    try {
      const response = await (0,_elementor_elementor_mcp_common__WEBPACK_IMPORTED_MODULE_0__.callWpApi)('/wp/v2/pages?per_page=100&orderby=modified&order=desc', 'GET');
      const pages = response.data;
      if (!Array.isArray(pages)) {
        throw new Error('Invalid response format from wordpress pages API');
      }
      const pagesList = pages.map(page => ({
        id: page.id,
        title: page.title.rendered,
        status: page.status,
        link: page.link,
        modified: page.modified,
        type: page.type
      }));
      return {
        contents: [{
          uri: uri.href,
          mimeType: 'application/json',
          text: JSON.stringify({
            pages: pagesList,
            total: pagesList.length,
            message: 'List of all available wordpress pages'
          }, null, 2)
        }]
      };
    } catch (error) {
      throw new Error(`Failed to fetch pages list: ${error.message}`);
    }
  });
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
  !*** ./packages/apps/elementor-capabilities-mcp/src/index.ts ***!
  \***************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-mcp */ "@elementor/editor-mcp");
/* harmony import */ var _elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_capabilities_mcp_server__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./elementor-capabilities-mcp-server */ "./packages/apps/elementor-capabilities-mcp/src/elementor-capabilities-mcp-server.ts");


function init() {
  const sdk = (0,_elementor_editor_mcp__WEBPACK_IMPORTED_MODULE_0__.getAngieSdk)();
  sdk.waitForReady().then(() => {
    const capabilitiesServer = (0,_elementor_capabilities_mcp_server__WEBPACK_IMPORTED_MODULE_1__.createElementorCapabilitiesServer)();
    sdk.registerServer({
      name: 'elementor-capabilities',
      version: '2.0.0',
      description: 'Elementor Capabilities Gateway',
      server: capabilitiesServer,
      capabilities: {
        tools: {}
      }
    });
    // eslint-disable-next-line no-console
    console.log('[Elementor Capabilities MCP] Module initialized');
  });
}
}();
(window.elementorV2 = window.elementorV2 || {}).elementorCapabilitiesMcp = __webpack_exports__;
/******/ })()
;
window.elementorV2.elementorCapabilitiesMcp?.init?.();
//# sourceMappingURL=elementor-capabilities-mcp.js.map
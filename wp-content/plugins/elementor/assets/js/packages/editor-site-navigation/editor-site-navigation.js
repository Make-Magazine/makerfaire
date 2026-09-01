/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/core/editor-site-navigation/src/api/post.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/api/post.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   POST_PER_PAGE: function() { return /* binding */ POST_PER_PAGE; },
/* harmony export */   createRequest: function() { return /* binding */ createRequest; },
/* harmony export */   deleteRequest: function() { return /* binding */ deleteRequest; },
/* harmony export */   duplicateRequest: function() { return /* binding */ duplicateRequest; },
/* harmony export */   getRequest: function() { return /* binding */ getRequest; },
/* harmony export */   postTypesMap: function() { return /* binding */ postTypesMap; },
/* harmony export */   updateRequest: function() { return /* binding */ updateRequest; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);


const postTypesMap = {
  page: {
    labels: {
      singular_name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Page', 'elementor'),
      plural_name: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Pages', 'elementor')
    },
    rest_base: 'pages'
  }
};
const POST_PER_PAGE = 10;
const getRequest = async (postTypeSlug, page) => {
  const baseUri = `/wp/v2/${postTypesMap[postTypeSlug].rest_base}`;
  const keys = ['id', 'type', 'title', 'link', 'status', 'user_can'];
  const queryParams = new URLSearchParams({
    status: 'any',
    order: 'asc',
    page: page.toString(),
    per_page: POST_PER_PAGE.toString(),
    _fields: keys.join(',')
  });
  const uri = baseUri + '?' + queryParams.toString();
  const result = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: uri,
    parse: false
  });
  const data = await result.json();
  const totalPages = Number(result.headers.get('x-wp-totalpages'));
  const totalPosts = Number(result.headers.get('x-wp-total'));
  return {
    data,
    totalPages,
    totalPosts,
    currentPage: page
  };
};
const createRequest = (postTypeSlug, newPost) => {
  const path = `/wp/v2/${postTypesMap[postTypeSlug].rest_base}`;
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path,
    method: 'POST',
    data: newPost
  });
};
const updateRequest = (postTypeSlug, updatedPost) => {
  const path = `/wp/v2/${postTypesMap[postTypeSlug].rest_base}`;
  const {
    id,
    ...data
  } = updatedPost;
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: `${path}/${id}`,
    method: 'POST',
    data
  });
};
const deleteRequest = (postTypeSlug, postId) => {
  const path = `/wp/v2/${postTypesMap[postTypeSlug].rest_base}`;
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: `${path}/${postId}`,
    method: 'DELETE'
  });
};
const duplicateRequest = originalPost => {
  const path = `/elementor/v1/site-navigation/duplicate-post`;
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path,
    method: 'POST',
    data: {
      post_id: originalPost.id,
      title: originalPost.title
    }
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/api/recent-posts.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/api/recent-posts.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NUMBER_OF_RECENT_POSTS: function() { return /* binding */ NUMBER_OF_RECENT_POSTS; },
/* harmony export */   baseUrl: function() { return /* binding */ baseUrl; },
/* harmony export */   getRequest: function() { return /* binding */ getRequest; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

const baseUrl = '/elementor/v1/site-navigation/recent-posts';
const NUMBER_OF_RECENT_POSTS = 6;
const getRequest = () => {
  const queryParams = new URLSearchParams({
    posts_per_page: `${NUMBER_OF_RECENT_POSTS}`
  });
  const path = `${baseUrl}?${queryParams.toString()}`;
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/api/settings.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/api/settings.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getSettings: function() { return /* binding */ getSettings; },
/* harmony export */   updateSettings: function() { return /* binding */ updateSettings; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

const getSettings = () => {
  const baseUri = '/elementor/v1/site-navigation/homepage';
  const uri = baseUri;
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: uri
  });
};
const updateSettings = settings => {
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/wp/v2/settings',
    method: 'POST',
    data: settings
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/api/user.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/api/user.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getUser: function() { return /* binding */ getUser; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

const getUser = () => {
  const baseUri = '/wp/v2/users/me';
  const keys = ['capabilities'];
  const queryParams = new URLSearchParams({
    _fields: keys.join(','),
    context: 'edit'
  });
  const uri = baseUri + '?' + queryParams.toString();
  return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: uri
  });
};

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ActionMenuItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


function ActionMenuItem({
  title,
  icon: Icon,
  MenuItemProps
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuItem, MenuItemProps, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ListItemIcon, {
    sx: {
      color: 'inherit'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.ListItemText, {
    primary: title
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/delete.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/delete.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Delete; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../../hooks/use-posts-actions */ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts-actions.ts");
/* harmony import */ var _action_menu_item__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../action-menu-item */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx");









function Delete({
  post
}) {
  const [isDialogOpen, setIsDialogOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const activeDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const isPostActive = activeDocument?.id === post.id;
  const userCanDelete = post.user_can.delete;
  const isDisabled = !userCanDelete || post.isHome || isPostActive;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_action_menu_item__WEBPACK_IMPORTED_MODULE_7__["default"], {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Delete', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_2__.TrashIcon,
    MenuItemProps: {
      disabled: isDisabled,
      onClick: () => setIsDialogOpen(true),
      sx: {
        '&:hover': {
          color: 'error.main'
        }
      }
    }
  }), isDialogOpen && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DeleteDialog, {
    post: post,
    setIsDialogOpen: setIsDialogOpen
  }));
}
function DeleteDialog({
  post,
  setIsDialogOpen
}) {
  const {
    type
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_5__.usePostListContext)();
  const {
    deletePost
  } = (0,_hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_6__.usePostActions)(type);
  const {
    setError
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_5__.usePostListContext)();

  /* translators: %s: Post title. */
  const dialogTitle = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Delete "%s"?', 'elementor'), post.title.rendered);
  const deletePage = async () => {
    try {
      await deletePost.mutateAsync(post.id);
    } catch {
      setError();
      setIsDialogOpen(false);
    }
  };
  const handleCancel = () => {
    if (deletePost.isPending) {
      return;
    }
    setIsDialogOpen(false);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Dialog, {
    open: true,
    onClose: handleCancel,
    "aria-labelledby": "delete-dialog"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogTitle, {
    noWrap: true
  }, dialogTitle), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogContent, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogContentText, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('The page and its content will be deleted forever and we won’t be able to recover them.', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.DialogActions, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    variant: "contained",
    color: "secondary",
    onClick: handleCancel,
    disabled: deletePost.isPending
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Cancel', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, {
    variant: "contained",
    color: "error",
    onClick: deletePage,
    disabled: deletePost.isPending
  }, !deletePost.isPending ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Delete', 'elementor') : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.CircularProgress, null))));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/duplicate.tsx":
/*!***************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/duplicate.tsx ***!
  \***************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Duplicate; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_user__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../hooks/use-user */ "./packages/packages/core/editor-site-navigation/src/hooks/use-user.ts");
/* harmony import */ var _action_menu_item__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../action-menu-item */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx");






function Duplicate({
  post,
  popupState
}) {
  const {
    setEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.usePostListContext)();
  const {
    data: user
  } = (0,_hooks_use_user__WEBPACK_IMPORTED_MODULE_4__["default"])();
  const onClick = () => {
    popupState.close();
    setEditMode({
      mode: 'duplicate',
      details: {
        postId: post.id,
        title: post.title.rendered
      }
    });
  };
  const isDisabled = !user?.capabilities?.edit_pages;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_action_menu_item__WEBPACK_IMPORTED_MODULE_5__["default"], {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Duplicate', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.CopyIcon,
    MenuItemProps: {
      disabled: isDisabled,
      onClick
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/rename.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/rename.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Rename; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _action_menu_item__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../action-menu-item */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx");





function Rename({
  post
}) {
  const {
    setEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.usePostListContext)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_action_menu_item__WEBPACK_IMPORTED_MODULE_4__["default"], {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Rename', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.EraseIcon,
    MenuItemProps: {
      disabled: !post.user_can.edit,
      onClick: () => {
        setEditMode({
          mode: 'rename',
          details: {
            postId: post.id
          }
        });
      }
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/set-home.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/set-home.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ SetHome; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_homepage_actions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../../hooks/use-homepage-actions */ "./packages/packages/core/editor-site-navigation/src/hooks/use-homepage-actions.ts");
/* harmony import */ var _hooks_use_user__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../../hooks/use-user */ "./packages/packages/core/editor-site-navigation/src/hooks/use-user.ts");
/* harmony import */ var _action_menu_item__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../action-menu-item */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx");








function SetHome({
  post,
  closeMenu
}) {
  const {
    updateSettingsMutation
  } = (0,_hooks_use_homepage_actions__WEBPACK_IMPORTED_MODULE_5__.useHomepageActions)();
  const {
    setError
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__.usePostListContext)();
  const {
    data: user
  } = (0,_hooks_use_user__WEBPACK_IMPORTED_MODULE_6__["default"])();
  const handleClick = async () => {
    try {
      await updateSettingsMutation.mutateAsync({
        show_on_front: 'page',
        page_on_front: post.id
      });
    } catch {
      setError();
    } finally {
      closeMenu();
    }
  };
  const canManageOptions = !!user?.capabilities?.manage_options;
  const isPostPublished = post.status === 'publish';
  const isPostHomepage = !!post.isHome;
  const isDisabled = !canManageOptions || isPostHomepage || !isPostPublished || updateSettingsMutation.isPending;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_action_menu_item__WEBPACK_IMPORTED_MODULE_7__["default"], {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Set as homepage', 'elementor'),
    icon: !updateSettingsMutation.isPending ? _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.HomeIcon : _elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CircularProgress,
    MenuItemProps: {
      disabled: isDisabled,
      onClick: handleClick
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/view.tsx":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/view.tsx ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ View; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _api_post__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../api/post */ "./packages/packages/core/editor-site-navigation/src/api/post.ts");
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _action_menu_item__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../action-menu-item */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/action-menu-item.tsx");






function View({
  post
}) {
  const {
    type
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__.usePostListContext)();

  // translators: %s: Post type (e.g. Page, Post, etc.)
  const title = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('View %s', 'elementor').replace('%s', _api_post__WEBPACK_IMPORTED_MODULE_3__.postTypesMap[type].labels.singular_name);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_action_menu_item__WEBPACK_IMPORTED_MODULE_5__["default"], {
    title: title,
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.EyeIcon,
    MenuItemProps: {
      onClick: () => window.open(post.link, '_blank')
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/add-new-button.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/add-new-button.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ AddNewButton; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_user__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../hooks/use-user */ "./packages/packages/core/editor-site-navigation/src/hooks/use-user.ts");






function AddNewButton() {
  const {
    setEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__.usePostListContext)();
  const {
    data: user
  } = (0,_hooks_use_user__WEBPACK_IMPORTED_MODULE_5__["default"])();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    size: 'small',
    startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PlusIcon, null),
    disabled: !user?.capabilities?.edit_pages,
    onClick: () => {
      setEditMode({
        mode: 'create',
        details: {}
      });
    },
    sx: {
      px: 1.5
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Add New', 'elementor'));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/error-snackbar.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/error-snackbar.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const ErrorSnackbar = ({
  open,
  onClose
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Snackbar, {
    open: open,
    onClose: onClose,
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'left'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Alert, {
    onClose: onClose,
    severity: "error",
    sx: {
      width: '100%'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    component: "span",
    sx: {
      fontWeight: 'bold'
    }
  }, "We couldn\u2019t complete the action."), ' ', "Please try again"));
};
/* harmony default export */ __webpack_exports__["default"] = (ErrorSnackbar);

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/panel.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/panel.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   panel: function() { return /* binding */ panel; },
/* harmony export */   usePanelActions: function() { return /* binding */ usePanelActions; },
/* harmony export */   usePanelStatus: function() { return /* binding */ usePanelStatus; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _shell__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./shell */ "./packages/packages/core/editor-site-navigation/src/components/panel/shell.tsx");


const {
  panel,
  usePanelStatus,
  usePanelActions
} = (0,_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_0__.__createPanel)({
  id: 'site-navigation-panel',
  component: _shell__WEBPACK_IMPORTED_MODULE_1__["default"]
});

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/collapsible-list.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/collapsible-list.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ CollapsibleList; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);




// TODO 21/06/2023 : Should replace this with future Rotate component that will be implemented in elementor-ui
const RotateIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.ChevronDownIcon, {
  shouldForwardProp: prop => prop !== 'isOpen'
})(({
  theme,
  isOpen
}) => ({
  transform: isOpen ? 'rotate(0deg)' : 'rotate(-90deg)',
  transition: theme.transitions.create('transform', {
    duration: theme.transitions.duration.standard
  })
}));
const StyledListItemIcon = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.styled)(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemIcon)(({
  theme
}) => ({
  minWidth: theme.spacing(4)
}));
function CollapsibleList({
  label,
  Icon,
  isOpenByDefault = false,
  children
}) {
  const [isOpen, setIsOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(isOpenByDefault);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItem, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledListItemIcon, {
    sx: {
      color: 'text.secondary'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    onClick: () => setIsOpen(prev => !prev),
    size: "small",
    sx: {
      color: 'inherit'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(RotateIcon, {
    fontSize: "small",
    isOpen: isOpen
  }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(StyledListItemIcon, {
    size: "small",
    sx: {
      color: 'inherit'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, {
    fontSize: "small"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemText, {
    primaryTypographyProps: {
      variant: 'subtitle2',
      component: 'span'
    },
    primary: label
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Collapse, {
    in: isOpen,
    timeout: "auto",
    unmountOnExit: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.List, {
    dense: true
  }, children)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, {
    sx: {
      mt: 1
    }
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/error-state.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/error-state.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ErrorState; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




function ErrorState() {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      display: 'flex',
      flexDirection: 'column',
      justifyContent: 'center',
      alignItems: 'center',
      pt: '40px',
      gap: '16px'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.Error404TemplateIcon, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      display: 'flex',
      flexDirection: 'column',
      justifyContent: 'center',
      alignItems: 'center',
      gap: '8px'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body1",
    color: "text.primary"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('We couldn’t display your pages.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    color: "text.primary",
    sx: {
      textAlign: 'center'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('It’s probably a temporary issue.', 'elementor')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, {
    variant: "body2",
    color: "text.primary",
    sx: {
      textAlign: 'center'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('If the problem persists,', 'elementor'), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Link, {
    target: "_blank",
    href: "https://go.elementor.com/wp-editor-support-open-ticket/"
  }, "Notify support")))));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/edit-mode-template.tsx":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/edit-mode-template.tsx ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ EditModeTemplate; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");






function EditModeTemplate({
  postTitle,
  isLoading,
  callback
}) {
  const [title, setTitle] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(postTitle);
  const [touched, setTouched] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [inputError, setInputError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const closeButton = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();
  const onBlur = e => {
    if (closeButton.current === e.relatedTarget) {
      return;
    }
    runCallback();
  };
  const onFormSubmit = e => {
    e.preventDefault();
    runCallback();
  };
  const validateInput = input => {
    return input.trim() !== '';
  };
  const runCallback = () => {
    if (!validateInput(title)) {
      return;
    }
    callback(title);
  };
  const onChange = e => {
    if (!touched) {
      setTouched(true);
    }
    const value = e.target.value;
    if (!validateInput(value)) {
      setInputError((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Name is required', 'elementor'));
    } else {
      setInputError(null);
    }
    setTitle(value);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItem, {
    secondaryAction: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CloseButton, {
      isLoading: isLoading,
      closeButton: closeButton
    })
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    width: "100%",
    component: "form",
    onSubmit: onFormSubmit
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.TextField, {
    autoFocus: true // eslint-disable-line jsx-a11y/no-autofocus
    ,
    fullWidth: true,
    value: title,
    onChange: onChange,
    disabled: isLoading,
    error: !!inputError,
    onBlur: onBlur,
    variant: "outlined",
    color: "secondary",
    size: "small"
  }))), inputError && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItem, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemText, {
    sx: {
      color: 'error.main'
    }
  }, inputError)));
}
function CloseButton({
  isLoading,
  closeButton
}) {
  const {
    resetEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__.usePostListContext)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.IconButton, {
    size: "small",
    color: "secondary",
    onClick: resetEditMode,
    ref: closeButton,
    disabled: isLoading
  }, isLoading ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CircularProgress, null) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.XIcon, {
    fontSize: "small"
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-create.tsx":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-create.tsx ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ListItemCreate; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../hooks/use-posts-actions */ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts-actions.ts");
/* harmony import */ var _edit_mode_template__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./edit-mode-template */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/edit-mode-template.tsx");






function ListItemCreate() {
  const {
    type,
    resetEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.usePostListContext)();
  const {
    createPost
  } = (0,_hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_4__.usePostActions)(type);
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useNavigateToDocument)();
  const {
    setError
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.usePostListContext)();
  const createPostCallback = async inputValue => {
    try {
      const {
        id
      } = await createPost.mutateAsync({
        title: inputValue,
        status: 'draft'
      });
      navigateToDocument(id);
    } catch {
      setError();
    } finally {
      resetEditMode();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_edit_mode_template__WEBPACK_IMPORTED_MODULE_5__["default"], {
    postTitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('New Page', 'elementor'),
    isLoading: createPost.isPending,
    callback: createPostCallback
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-duplicate.tsx":
/*!**************************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-duplicate.tsx ***!
  \**************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ListItemDuplicate; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../hooks/use-posts-actions */ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts-actions.ts");
/* harmony import */ var _edit_mode_template__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./edit-mode-template */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/edit-mode-template.tsx");






function ListItemDuplicate() {
  const {
    type,
    editMode,
    resetEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.usePostListContext)();
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useNavigateToDocument)();
  const {
    duplicatePost
  } = (0,_hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_4__.usePostActions)(type);
  const {
    setError
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.usePostListContext)();
  if ('duplicate' !== editMode.mode) {
    return null;
  }
  const duplicatePostCallback = async inputValue => {
    try {
      const {
        post_id: postId
      } = await duplicatePost.mutateAsync({
        id: editMode.details.postId,
        title: inputValue
      });
      navigateToDocument(postId);
    } catch {
      setError();
    } finally {
      resetEditMode();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_edit_mode_template__WEBPACK_IMPORTED_MODULE_5__["default"], {
    postTitle: `${editMode.details.title} ${(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('copy', 'elementor')}`,
    isLoading: duplicatePost.isPending,
    callback: duplicatePostCallback
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-rename.tsx":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-rename.tsx ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ListItemRename; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../hooks/use-posts-actions */ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts-actions.ts");
/* harmony import */ var _hooks_use_rename_active_document__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../hooks/use-rename-active-document */ "./packages/packages/core/editor-site-navigation/src/hooks/use-rename-active-document.ts");
/* harmony import */ var _edit_mode_template__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./edit-mode-template */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/edit-mode-template.tsx");






function ListItemRename({
  post
}) {
  const {
    type,
    resetEditMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_2__.usePostListContext)();
  const {
    updatePost
  } = (0,_hooks_use_posts_actions__WEBPACK_IMPORTED_MODULE_3__.usePostActions)(type);
  const {
    setError
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_2__.usePostListContext)();
  const activeDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const rename = (0,_hooks_use_rename_active_document__WEBPACK_IMPORTED_MODULE_4__["default"])();
  const isActive = activeDocument?.id === post.id;
  const title = isActive ? activeDocument?.title : post.title.rendered;
  const renamePostCallback = async inputValue => {
    if (inputValue === title) {
      resetEditMode();
    }
    try {
      if (isActive) {
        await rename(inputValue);
      } else {
        await updatePost.mutateAsync({
          id: post.id,
          title: inputValue
        });
      }
    } catch {
      setError();
    } finally {
      resetEditMode();
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_edit_mode_template__WEBPACK_IMPORTED_MODULE_5__["default"], {
    postTitle: title,
    isLoading: updatePost.isPending,
    callback: renamePostCallback
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-view.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-view.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ ListItemView; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _shared_page_title_and_status__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../shared/page-title-and-status */ "./packages/packages/core/editor-site-navigation/src/components/shared/page-title-and-status.tsx");
/* harmony import */ var _actions_menu_actions_delete__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../actions-menu/actions/delete */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/delete.tsx");
/* harmony import */ var _actions_menu_actions_duplicate__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../actions-menu/actions/duplicate */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/duplicate.tsx");
/* harmony import */ var _actions_menu_actions_rename__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../actions-menu/actions/rename */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/rename.tsx");
/* harmony import */ var _actions_menu_actions_set_home__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../actions-menu/actions/set-home */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/set-home.tsx");
/* harmony import */ var _actions_menu_actions_view__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../actions-menu/actions/view */ "./packages/packages/core/editor-site-navigation/src/components/panel/actions-menu/actions/view.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }











const DisabledPostTooltip = ({
  children,
  isDisabled
}) => {
  if (isDisabled) {
    const title = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Typography, {
      variant: "caption"
    }, "You cannot edit this page.", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("br", null), "To edit it directly, contact the site owner");
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Tooltip, {
      title: title,
      placement: "bottom",
      arrow: false
    }, children);
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, children);
};
function ListItemView({
  post
}) {
  const activeDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useNavigateToDocument)();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.usePopupState)({
    variant: 'popover',
    popupId: 'post-actions',
    disableAutoFocus: true
  });
  const isActive = activeDocument?.id === post.id;
  const status = isActive ? activeDocument?.status.value : post.status;
  const title = isActive ? activeDocument?.title : post.title.rendered;
  const isDisabled = !post.user_can.edit;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(DisabledPostTooltip, {
    isDisabled: isDisabled
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItem, {
    disablePadding: true,
    secondaryAction: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.IconButton, _extends({
      value: true,
      size: "small"
    }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindTrigger)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.DotsVerticalIcon, {
      fontSize: "small"
    }))
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemButton, {
    selected: isActive,
    disabled: isDisabled,
    onClick: () => {
      if (!isActive) {
        navigateToDocument(post.id);
      }
    },
    dense: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemText, {
    disableTypography: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_shared_page_title_and_status__WEBPACK_IMPORTED_MODULE_5__["default"], {
    title: title,
    status: status
  })), post.isHome && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.HomeIcon, {
    titleAccess: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Homepage', 'elementor'),
    color: "disabled"
  })))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Menu, _extends({
    PaperProps: {
      sx: {
        mt: 2,
        width: 200
      }
    },
    MenuListProps: {
      dense: true
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindMenu)(popupState)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_actions_menu_actions_rename__WEBPACK_IMPORTED_MODULE_8__["default"], {
    post: post
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_actions_menu_actions_duplicate__WEBPACK_IMPORTED_MODULE_7__["default"], {
    post: post,
    popupState: popupState
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_actions_menu_actions_delete__WEBPACK_IMPORTED_MODULE_6__["default"], {
    post: post
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_actions_menu_actions_view__WEBPACK_IMPORTED_MODULE_10__["default"], {
    post: post
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_actions_menu_actions_set_home__WEBPACK_IMPORTED_MODULE_9__["default"], {
    post: post,
    closeMenu: () => popupState.close()
  })));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/post-list-item.tsx":
/*!**********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/post-list-item.tsx ***!
  \**********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PostListItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _list_items_list_item_create__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./list-items/list-item-create */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-create.tsx");
/* harmony import */ var _list_items_list_item_duplicate__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./list-items/list-item-duplicate */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-duplicate.tsx");
/* harmony import */ var _list_items_list_item_rename__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./list-items/list-item-rename */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-rename.tsx");
/* harmony import */ var _list_items_list_item_view__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./list-items/list-item-view */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/list-items/list-item-view.tsx");






function PostListItem({
  post
}) {
  const {
    editMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_1__.usePostListContext)();
  if ('rename' === editMode.mode && post?.id && post?.id === editMode.details.postId) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_list_items_list_item_rename__WEBPACK_IMPORTED_MODULE_4__["default"], {
      post: post
    });
  }
  if ('create' === editMode.mode && !post) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_list_items_list_item_create__WEBPACK_IMPORTED_MODULE_2__["default"], null);
  }
  if ('duplicate' === editMode.mode && !post) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_list_items_list_item_duplicate__WEBPACK_IMPORTED_MODULE_3__["default"], null);
  }
  if (!post) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_list_items_list_item_view__WEBPACK_IMPORTED_MODULE_5__["default"], {
    post: post
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/posts-collapsible-list.tsx":
/*!******************************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/posts-collapsible-list.tsx ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PostsCollapsibleList; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _api_post__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../api/post */ "./packages/packages/core/editor-site-navigation/src/api/post.ts");
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _hooks_use_homepage__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../hooks/use-homepage */ "./packages/packages/core/editor-site-navigation/src/hooks/use-homepage.ts");
/* harmony import */ var _hooks_use_posts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../hooks/use-posts */ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts.ts");
/* harmony import */ var _add_new_button__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../add-new-button */ "./packages/packages/core/editor-site-navigation/src/components/panel/add-new-button.tsx");
/* harmony import */ var _collapsible_list__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./collapsible-list */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/collapsible-list.tsx");
/* harmony import */ var _error_state__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./error-state */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/error-state.tsx");
/* harmony import */ var _post_list_item__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./post-list-item */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/post-list-item.tsx");











function PostsCollapsibleList({
  isOpenByDefault = false
}) {
  const {
    type,
    editMode
  } = (0,_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_4__.usePostListContext)();
  const {
    data: {
      posts,
      total
    },
    isLoading: postsLoading,
    isError: postsError,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage
  } = (0,_hooks_use_posts__WEBPACK_IMPORTED_MODULE_6__.usePosts)(type);
  const {
    data: homepageId
  } = (0,_hooks_use_homepage__WEBPACK_IMPORTED_MODULE_5__.useHomepage)();
  if (postsError) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_error_state__WEBPACK_IMPORTED_MODULE_9__["default"], null);
  }
  if (!posts || postsLoading) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
      sx: {
        px: 5
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
      display: "flex",
      justifyContent: "flex-end",
      alignItems: "center"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Skeleton, {
      sx: {
        my: 4
      },
      animation: "wave",
      variant: "rounded",
      width: "110px",
      height: "28px"
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Skeleton, {
      sx: {
        my: 3
      },
      animation: "wave",
      variant: "rounded",
      width: "100%",
      height: "24px"
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Skeleton, {
      sx: {
        my: 3
      },
      animation: "wave",
      variant: "rounded",
      width: "70%",
      height: "24px"
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Skeleton, {
      sx: {
        my: 3
      },
      animation: "wave",
      variant: "rounded",
      width: "70%",
      height: "24px"
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Skeleton, {
      sx: {
        my: 3
      },
      animation: "wave",
      variant: "rounded",
      width: "70%",
      height: "24px"
    })));
  }
  const label = `${_api_post__WEBPACK_IMPORTED_MODULE_3__.postTypesMap[type].labels.plural_name} (${total.toString()})`;
  const mappedPosts = posts.map(post => {
    if (post.id === homepageId) {
      return {
        ...post,
        isHome: true
      };
    }
    return post;
  });
  const sortedPosts = mappedPosts.sort((a, b) => {
    if (a.id === homepageId) {
      return -1;
    }
    if (b.id === homepageId) {
      return 1;
    }
    return 0;
  });
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    display: "flex",
    justifyContent: "flex-end",
    alignItems: "center",
    sx: {
      py: 1,
      px: 2
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_add_new_button__WEBPACK_IMPORTED_MODULE_7__["default"], null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.List, {
    dense: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_collapsible_list__WEBPACK_IMPORTED_MODULE_8__["default"], {
    label: label,
    Icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PageTypeIcon,
    isOpenByDefault: isOpenByDefault || false
  }, sortedPosts.map(post => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_post_list_item__WEBPACK_IMPORTED_MODULE_10__["default"], {
      key: post.id,
      post: post
    });
  }), ['duplicate', 'create'].includes(editMode.mode) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_post_list_item__WEBPACK_IMPORTED_MODULE_10__["default"], null), hasNextPage && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, {
    sx: {
      display: 'flex',
      justifyContent: 'center'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button, {
    onClick: fetchNextPage,
    color: "secondary"
  }, isFetchingNextPage ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CircularProgress, null) : 'Load More')))));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/panel/shell.tsx":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/panel/shell.tsx ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../contexts/post-list-context */ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx");
/* harmony import */ var _error_snackbar__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./error-snackbar */ "./packages/packages/core/editor-site-navigation/src/components/panel/error-snackbar.tsx");
/* harmony import */ var _posts_list_posts_collapsible_list__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./posts-list/posts-collapsible-list */ "./packages/packages/core/editor-site-navigation/src/components/panel/posts-list/posts-collapsible-list.tsx");







const Shell = () => {
  const [isErrorSnackbarOpen, setIsErrorSnackbarOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.Panel, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.PanelHeader, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.PanelHeaderTitle, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Pages', 'elementor'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.PanelBody, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_contexts_post_list_context__WEBPACK_IMPORTED_MODULE_3__.PostListContextProvider, {
    type: 'page',
    setError: () => setIsErrorSnackbarOpen(true)
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_posts_list_posts_collapsible_list__WEBPACK_IMPORTED_MODULE_5__["default"], {
    isOpenByDefault: true
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_error_snackbar__WEBPACK_IMPORTED_MODULE_4__["default"], {
    open: isErrorSnackbarOpen,
    onClose: () => setIsErrorSnackbarOpen(false)
  })));
};
/* harmony default export */ __webpack_exports__["default"] = (Shell);

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/shared/page-title-and-status.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/shared/page-title-and-status.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PageTitleAndStatus; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _hooks_use_reverse_html_entities__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../hooks/use-reverse-html-entities */ "./packages/packages/core/editor-site-navigation/src/hooks/use-reverse-html-entities.ts");



const PageStatus = ({
  status
}) => {
  if ('publish' === status) {
    return null;
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    component: "span",
    variant: "body2",
    color: "text.secondary",
    sx: {
      textTransform: 'capitalize',
      fontStyle: 'italic',
      whiteSpace: 'nowrap',
      flexBasis: 'content'
    }
  }, "(", status, ")");
};
const PageTitle = ({
  title
}) => {
  const modifiedTitle = (0,_hooks_use_reverse_html_entities__WEBPACK_IMPORTED_MODULE_2__.useReverseHtmlEntities)(title);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    component: "span",
    variant: "body2",
    color: "text.secondary",
    noWrap: true,
    sx: {
      flexBasis: 'auto'
    }
  }, modifiedTitle);
};
function PageTitleAndStatus({
  title,
  status
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Box, {
    display: "flex"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PageTitle, {
    title: title
  }), "\xA0", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PageStatus, {
    status: status
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/top-bar/chip-doc-type.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/top-bar/chip-doc-type.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ DocTypeChip; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _icons_map__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../icons-map */ "./packages/packages/core/editor-site-navigation/src/icons-map.ts");




const iconsMap = (0,_icons_map__WEBPACK_IMPORTED_MODULE_3__.getIconsMap)();
function DocTypeChip({
  postType,
  docType,
  label
}) {
  const color = 'elementor_library' === postType ? 'global' : 'primary';
  const Icon = iconsMap[docType] || _elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PostTypeIcon;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Chip, {
    component: "span",
    size: "small",
    variant: "outlined",
    label: label,
    "data-value": docType,
    color: color,
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Icon, null),
    sx: {
      ml: 1,
      cursor: 'inherit'
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/top-bar/create-post-list-item.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/top-bar/create-post-list-item.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CreatePostListItem: function() { return /* binding */ CreatePostListItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _hooks_use_create_page__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../hooks/use-create-page */ "./packages/packages/core/editor-site-navigation/src/hooks/use-create-page.ts");
/* harmony import */ var _hooks_use_user__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../hooks/use-user */ "./packages/packages/core/editor-site-navigation/src/hooks/use-user.ts");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }








function CreatePostListItem({
  closePopup,
  ...props
}) {
  const {
    create,
    isLoading
  } = (0,_hooks_use_create_page__WEBPACK_IMPORTED_MODULE_6__["default"])();
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useNavigateToDocument)();
  const {
    data: user
  } = (0,_hooks_use_user__WEBPACK_IMPORTED_MODULE_7__["default"])();
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_2__.useMixpanel)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.MenuItem, _extends({
    disabled: isLoading || !user?.capabilities?.edit_pages,
    onClick: async () => {
      const eventName = config?.names?.editorOne?.topBarPageList;
      if (eventName) {
        dispatchEvent?.(eventName, {
          app_type: config?.appTypes?.editor,
          window_name: config?.appTypes?.editor,
          interaction_type: config?.triggers?.click?.toLowerCase(),
          target_type: config?.targetTypes?.dropdownItem,
          target_name: config?.targetNames?.pageList?.addNewPage,
          interaction_result: config?.interactionResults?.create,
          target_location: config?.locations?.topBar?.replace(/\s+/g, '_').toLowerCase(),
          location_l1: config?.secondaryLocations?.pageListDropdown?.replace(/\s+/g, '_').toLowerCase(),
          location_l2: config?.targetTypes?.dropdownItem
        });
      }
      const {
        id
      } = await create();
      closePopup();
      await navigateToDocument(id);
    }
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.ListItemIcon, null, isLoading ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.CircularProgress, {
    size: "1.25rem"
  }) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.PlusIcon, {
    fontSize: "small"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_4__.ListItemText, {
    primaryTypographyProps: {
      variant: 'body2'
    },
    primary: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Add new page', 'elementor')
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/top-bar/indicator.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/top-bar/indicator.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Indicator; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function Indicator({
  title,
  status
}) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(Tooltip, {
    title: title
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    component: "span",
    direction: "row",
    alignItems: "center",
    spacing: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    component: "span",
    variant: "body2",
    sx: {
      maxWidth: '120px'
    },
    noWrap: true
  }, title), status.value !== 'publish' && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Typography, {
    component: "span",
    variant: "body2",
    sx: {
      fontStyle: 'italic'
    }
  }, "(", status.label, ")")));
}
function Tooltip(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tooltip, _extends({
    PopperProps: {
      sx: {
        '&.MuiTooltip-popper .MuiTooltip-tooltip.MuiTooltip-tooltipPlacementBottom': {
          mt: 2.7
        }
      }
    }
  }, props));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/top-bar/post-list-item.tsx":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/top-bar/post-list-item.tsx ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PostListItem: function() { return /* binding */ PostListItem; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/events */ "@elementor/events");
/* harmony import */ var _elementor_events__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_events__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _hooks_use_reverse_html_entities__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../hooks/use-reverse-html-entities */ "./packages/packages/core/editor-site-navigation/src/hooks/use-reverse-html-entities.ts");
/* harmony import */ var _chip_doc_type__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./chip-doc-type */ "./packages/packages/core/editor-site-navigation/src/components/top-bar/chip-doc-type.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }






function PostListItem({
  post,
  closePopup,
  ...props
}) {
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useNavigateToDocument)();
  const postTitle = (0,_hooks_use_reverse_html_entities__WEBPACK_IMPORTED_MODULE_4__.useReverseHtmlEntities)(post.title);
  const {
    dispatchEvent,
    config
  } = (0,_elementor_events__WEBPACK_IMPORTED_MODULE_2__.useMixpanel)();
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuItem, _extends({
    disabled: !post.user_can.edit,
    onClick: async () => {
      const eventName = config?.names?.editorOne?.topBarPageList;
      if (eventName) {
        dispatchEvent?.(eventName, {
          app_type: config?.appTypes?.editor,
          window_name: config?.appTypes?.editor,
          interaction_type: config?.triggers?.click?.toLowerCase(),
          target_type: config?.targetTypes?.dropdownItem,
          target_name: postTitle,
          interaction_result: config?.interactionResults?.navigate,
          target_location: config?.locations?.topBar?.replace(/\s+/g, '_').toLowerCase(),
          location_l1: config?.secondaryLocations?.pageListDropdown?.replace(/\s+/g, '_').toLowerCase(),
          location_l2: config?.targetTypes?.dropdownItem
        });
      }
      closePopup();
      await navigateToDocument(post.id);
    }
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemText, {
    sx: {
      flexGrow: 0
    },
    primaryTypographyProps: {
      variant: 'body2',
      noWrap: true
    },
    primary: postTitle
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_chip_doc_type__WEBPACK_IMPORTED_MODULE_5__["default"], {
    postType: post.type.post_type,
    docType: post.type.doc_type,
    label: post.type.label
  }));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/components/top-bar/recently-edited.tsx":
/*!**************************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/components/top-bar/recently-edited.tsx ***!
  \**************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ RecentlyEdited; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _api_recent_posts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../api/recent-posts */ "./packages/packages/core/editor-site-navigation/src/api/recent-posts.ts");
/* harmony import */ var _hooks_use_recent_posts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../hooks/use-recent-posts */ "./packages/packages/core/editor-site-navigation/src/hooks/use-recent-posts.ts");
/* harmony import */ var _hooks_use_reverse_html_entities__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../hooks/use-reverse-html-entities */ "./packages/packages/core/editor-site-navigation/src/hooks/use-reverse-html-entities.ts");
/* harmony import */ var _create_post_list_item__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./create-post-list-item */ "./packages/packages/core/editor-site-navigation/src/components/top-bar/create-post-list-item.tsx");
/* harmony import */ var _indicator__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./indicator */ "./packages/packages/core/editor-site-navigation/src/components/top-bar/indicator.tsx");
/* harmony import */ var _post_list_item__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./post-list-item */ "./packages/packages/core/editor-site-navigation/src/components/top-bar/post-list-item.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }











function RecentlyEdited() {
  const activeDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useActiveDocument)();
  const hostDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_1__.__useHostDocument)();
  const document = activeDocument && activeDocument.type.value !== 'kit' ? activeDocument : hostDocument;
  const {
    data
  } = (0,_hooks_use_recent_posts__WEBPACK_IMPORTED_MODULE_6__["default"])();
  const getRecentPosts = () => {
    if (!data) {
      return [];
    }
    return data.filter(post => post.id !== document?.id).splice(0, _api_recent_posts__WEBPACK_IMPORTED_MODULE_5__.NUMBER_OF_RECENT_POSTS - 1);
  };
  const recentPosts = getRecentPosts();
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.usePopupState)({
    variant: 'popover',
    popupId: 'elementor-v2-top-bar-recently-edited'
  });
  const documentTitle = (0,_hooks_use_reverse_html_entities__WEBPACK_IMPORTED_MODULE_7__.useReverseHtmlEntities)(document?.title);
  if (!document) {
    return null;
  }
  const buttonProps = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindTrigger)(popupState);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Button, _extends({
    color: "inherit",
    size: "small",
    endIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ChevronDownIcon, {
      fontSize: "small"
    })
  }, buttonProps, {
    onClick: e => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent(config.names.topBar.documentNameDropdown, {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.documentNameDropdown,
          trigger: config.triggers.dropdownClick,
          element: config.elements.dropdown
        });
      }
      buttonProps.onClick(e);
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_indicator__WEBPACK_IMPORTED_MODULE_9__["default"], {
    title: documentTitle,
    status: document.status
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Menu, _extends({
    MenuListProps: {
      subheader: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListSubheader, {
        color: "primary",
        sx: {
          fontStyle: 'italic',
          fontWeight: '300'
        }
      }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Recent', 'elementor'))
    },
    PaperProps: {
      sx: {
        mt: 2.5,
        width: 320
      }
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindMenu)(popupState)), recentPosts.map(post => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_post_list_item__WEBPACK_IMPORTED_MODULE_10__.PostListItem, {
    key: post.id,
    post: post,
    closePopup: popupState.close
  })), recentPosts.length === 0 && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.MenuItem, {
    disabled: true
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemText, {
    primaryTypographyProps: {
      variant: 'caption',
      fontStyle: 'italic'
    },
    primary: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('There are no other pages or templates on this site yet.', 'elementor')
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, {
    disabled: recentPosts.length === 0
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_create_post_list_item__WEBPACK_IMPORTED_MODULE_8__.CreatePostListItem, {
    closePopup: popupState.close
  })));
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx":
/*!******************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/contexts/post-list-context.tsx ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PostListContextProvider: function() { return /* binding */ PostListContextProvider; },
/* harmony export */   usePostListContext: function() { return /* binding */ usePostListContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const defaultValues = {
  type: 'page',
  editMode: {
    mode: 'none',
    details: {}
  },
  setEditMode: () => null,
  resetEditMode: () => null,
  setError: () => null
};
const PostListContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(defaultValues);
const PostListContextProvider = ({
  type,
  setError,
  children
}) => {
  const [editMode, setEditMode] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(defaultValues.editMode);
  const resetEditMode = () => {
    setEditMode(defaultValues.editMode);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(PostListContext.Provider, {
    value: {
      type,
      editMode,
      setEditMode,
      resetEditMode,
      setError
    }
  }, children);
};
function usePostListContext() {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(PostListContext);
  if (!context) {
    throw new Error('The `usePostListContext()` hook must be used within an `<PostListContextProvider />`');
  }
  return context;
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/env.ts":
/*!******************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/env.ts ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   env: function() { return /* binding */ env; }
/* harmony export */ });
/* harmony import */ var _elementor_env__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/env */ "@elementor/env");
/* harmony import */ var _elementor_env__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_env__WEBPACK_IMPORTED_MODULE_0__);

const {
  env
} = (0,_elementor_env__WEBPACK_IMPORTED_MODULE_0__.parseEnv)('@elementor/editor-site-navigation', envData => {
  return envData;
});

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-create-page.ts":
/*!************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-create-page.ts ***!
  \************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useCreatePage; },
/* harmony export */   endpointPath: function() { return /* binding */ endpointPath; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__);


const endpointPath = '/elementor/v1/site-navigation/add-new-post';
function useCreatePage() {
  const [isLoading, setIsLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  return {
    create: () => {
      setIsLoading(true);
      return addNewPage().then(newPost => newPost).finally(() => setIsLoading(false));
    },
    isLoading
  };
}
async function addNewPage() {
  return await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
    path: endpointPath,
    method: 'POST',
    data: {
      post_type: 'page'
    }
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-homepage-actions.ts":
/*!*****************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-homepage-actions.ts ***!
  \*****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useHomepageActions: function() { return /* binding */ useHomepageActions; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api/settings */ "./packages/packages/core/editor-site-navigation/src/api/settings.ts");
/* harmony import */ var _use_homepage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-homepage */ "./packages/packages/core/editor-site-navigation/src/hooks/use-homepage.ts");



function useHomepageActions() {
  const invalidateSettings = useInvalidateSettings();
  const onSuccess = async () => invalidateSettings({
    exact: true
  });
  const updateSettingsMutation = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: settings => (0,_api_settings__WEBPACK_IMPORTED_MODULE_1__.updateSettings)(settings),
    onSuccess
  });
  return {
    updateSettingsMutation
  };
}
function useInvalidateSettings() {
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQueryClient)();
  return (options = {}) => {
    const queryKey = (0,_use_homepage__WEBPACK_IMPORTED_MODULE_2__.settingsQueryKey)();
    return queryClient.invalidateQueries({
      queryKey
    }, options);
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-homepage.ts":
/*!*********************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-homepage.ts ***!
  \*********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   settingsQueryKey: function() { return /* binding */ settingsQueryKey; },
/* harmony export */   useHomepage: function() { return /* binding */ useHomepage; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api/settings */ "./packages/packages/core/editor-site-navigation/src/api/settings.ts");


const settingsQueryKey = () => ['site-navigation', 'homepage'];
function useHomepage() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: settingsQueryKey(),
    queryFn: () => (0,_api_settings__WEBPACK_IMPORTED_MODULE_1__.getSettings)()
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts-actions.ts":
/*!**************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-posts-actions.ts ***!
  \**************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   usePostActions: function() { return /* binding */ usePostActions; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api_post__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api/post */ "./packages/packages/core/editor-site-navigation/src/api/post.ts");
/* harmony import */ var _use_posts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./use-posts */ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts.ts");
/* harmony import */ var _use_recent_posts__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./use-recent-posts */ "./packages/packages/core/editor-site-navigation/src/hooks/use-recent-posts.ts");




function usePostActions(postTypeSlug) {
  const invalidatePosts = useInvalidatePosts(postTypeSlug);
  const onSuccess = () => invalidatePosts({
    exact: true
  });
  const createPost = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: newPost => (0,_api_post__WEBPACK_IMPORTED_MODULE_1__.createRequest)(postTypeSlug, newPost),
    onSuccess
  });
  const updatePost = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: updatedPost => (0,_api_post__WEBPACK_IMPORTED_MODULE_1__.updateRequest)(postTypeSlug, updatedPost),
    onSuccess
  });
  const deletePost = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: postId => (0,_api_post__WEBPACK_IMPORTED_MODULE_1__.deleteRequest)(postTypeSlug, postId),
    onSuccess
  });
  const duplicatePost = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useMutation)({
    mutationFn: originalPost => (0,_api_post__WEBPACK_IMPORTED_MODULE_1__.duplicateRequest)(originalPost),
    onSuccess
  });
  return {
    createPost,
    updatePost,
    deletePost,
    duplicatePost
  };
}
function useInvalidatePosts(postTypeSlug) {
  const queryClient = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQueryClient)();
  return (options = {}) => {
    const queryKey = (0,_use_posts__WEBPACK_IMPORTED_MODULE_2__.postsQueryKey)(postTypeSlug);
    queryClient.invalidateQueries({
      queryKey: _use_recent_posts__WEBPACK_IMPORTED_MODULE_3__.recentPostsQueryKey
    }, options);
    return queryClient.invalidateQueries({
      queryKey
    }, options);
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-posts.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-posts.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   postsQueryKey: function() { return /* binding */ postsQueryKey; },
/* harmony export */   usePosts: function() { return /* binding */ usePosts; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api_post__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api/post */ "./packages/packages/core/editor-site-navigation/src/api/post.ts");


const postsQueryKey = postTypeSlug => ['site-navigation', 'posts', postTypeSlug];
const flattenData = data => {
  if (!data) {
    return data;
  }
  const flattened = [];
  data.pages.forEach(page => {
    flattened.push(...page.data);
  });
  return flattened;
};
function usePosts(postTypeSlug) {
  const query = (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useInfiniteQuery)({
    queryKey: postsQueryKey(postTypeSlug),
    queryFn: ({
      pageParam = 1
    }) => (0,_api_post__WEBPACK_IMPORTED_MODULE_1__.getRequest)(postTypeSlug, pageParam),
    initialPageParam: 1,
    getNextPageParam: lastPage => {
      return lastPage.currentPage < lastPage.totalPages ? lastPage.currentPage + 1 : undefined;
    }
  });
  return {
    ...query,
    data: {
      posts: flattenData(query.data),
      total: query.data?.pages[0]?.totalPosts ?? 0
    }
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-recent-posts.ts":
/*!*************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-recent-posts.ts ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useRecentPosts; },
/* harmony export */   recentPostsQueryKey: function() { return /* binding */ recentPostsQueryKey; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api_recent_posts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api/recent-posts */ "./packages/packages/core/editor-site-navigation/src/api/recent-posts.ts");


const recentPostsQueryKey = ['site-navigation', 'recent-posts'];
function useRecentPosts() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: recentPostsQueryKey,
    queryFn: () => (0,_api_recent_posts__WEBPACK_IMPORTED_MODULE_1__.getRequest)()
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-rename-active-document.ts":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-rename-active-document.ts ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useRenameActiveDocument; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
/* harmony import */ var _elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__);

function getV1DocumentsManager() {
  const documentsManager = window.elementor?.documents;
  if (!documentsManager) {
    throw new Error('Elementor Editor V1 documents manager not found');
  }
  return documentsManager;
}
function useRenameActiveDocument() {
  return async title => {
    const currentDocument = getV1DocumentsManager().getCurrent();
    const container = currentDocument.container;
    await (0,_elementor_editor_v1_adapters__WEBPACK_IMPORTED_MODULE_0__.__privateRunCommand)('document/elements/settings', {
      container,
      settings: {
        post_title: title
      }
    });
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-reverse-html-entities.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-reverse-html-entities.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useReverseHtmlEntities: function() { return /* binding */ useReverseHtmlEntities; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useReverseHtmlEntities(escapedHTML = '') {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = escapedHTML;
    const {
      value
    } = textarea;
    textarea.remove();
    return value;
  }, [escapedHTML]);
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-toggle-button-props.ts":
/*!********************************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-toggle-button-props.ts ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useToggleButtonProps: function() { return /* binding */ useToggleButtonProps; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_panel_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/panel/panel */ "./packages/packages/core/editor-site-navigation/src/components/panel/panel.ts");



function useToggleButtonProps() {
  const {
    isOpen: selectedState,
    isBlocked
  } = (0,_components_panel_panel__WEBPACK_IMPORTED_MODULE_2__.usePanelStatus)();
  const {
    open,
    close
  } = (0,_components_panel_panel__WEBPACK_IMPORTED_MODULE_2__.usePanelActions)();
  return {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Pages', 'elementor'),
    icon: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PagesIcon,
    onClick: () => {
      const extendedWindow = window;
      const config = extendedWindow?.elementorCommon?.eventsManager?.config;
      if (config) {
        extendedWindow.elementorCommon.eventsManager.dispatchEvent('top_bar_pages', {
          location: config.locations.topBar,
          secondaryLocation: config.secondaryLocations.elementorLogo,
          trigger: config.triggers.click,
          element: config.elements.buttonIcon
        });
      }
      return selectedState ? close() : open();
    },
    selected: selectedState,
    disabled: isBlocked
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/hooks/use-user.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/hooks/use-user.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ useUser; }
/* harmony export */ });
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/query */ "@elementor/query");
/* harmony import */ var _elementor_query__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_query__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _api_user__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../api/user */ "./packages/packages/core/editor-site-navigation/src/api/user.ts");


const userQueryKey = () => ['site-navigation', 'user'];
function useUser() {
  return (0,_elementor_query__WEBPACK_IMPORTED_MODULE_0__.useQuery)({
    queryKey: userQueryKey(),
    queryFn: _api_user__WEBPACK_IMPORTED_MODULE_1__.getUser,
    staleTime: 30 * 60 * 1000
  });
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/icons-map.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/icons-map.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extendIconsMap: function() { return /* binding */ extendIconsMap; },
/* harmony export */   getIconsMap: function() { return /* binding */ getIconsMap; },
/* harmony export */   resetIconsMap: function() { return /* binding */ resetIconsMap; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__);

const initialIconsMap = {
  page: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PageTemplateIcon,
  section: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.SectionTemplateIcon,
  container: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.ContainerTemplateIcon,
  'wp-page': _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PageTypeIcon,
  'wp-post': _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PostTypeIcon
};
let iconsMap = {
  ...initialIconsMap
};
function extendIconsMap(additionalIcons) {
  Object.assign(iconsMap, additionalIcons);
}
function getIconsMap() {
  return iconsMap;
}
function resetIconsMap() {
  iconsMap = {
    ...initialIconsMap
  };
}

/***/ }),

/***/ "./packages/packages/core/editor-site-navigation/src/init.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/init.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-app-bar */ "@elementor/editor-app-bar");
/* harmony import */ var _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-panels */ "@elementor/editor-panels");
/* harmony import */ var _elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_panel_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/panel/panel */ "./packages/packages/core/editor-site-navigation/src/components/panel/panel.ts");
/* harmony import */ var _components_top_bar_recently_edited__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/top-bar/recently-edited */ "./packages/packages/core/editor-site-navigation/src/components/top-bar/recently-edited.tsx");
/* harmony import */ var _env__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./env */ "./packages/packages/core/editor-site-navigation/src/env.ts");
/* harmony import */ var _hooks_use_toggle_button_props__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./hooks/use-toggle-button-props */ "./packages/packages/core/editor-site-navigation/src/hooks/use-toggle-button-props.ts");






function init() {
  registerTopBarMenuItems();
  // TODO 06/06/2023 :  remove if when we are production ready
  if (_env__WEBPACK_IMPORTED_MODULE_4__.env.is_pages_panel_active) {
    (0,_elementor_editor_panels__WEBPACK_IMPORTED_MODULE_1__.__registerPanel)(_components_panel_panel__WEBPACK_IMPORTED_MODULE_2__.panel);
    registerButton();
  }
}
function registerTopBarMenuItems() {
  (0,_elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_0__.injectIntoPageIndication)({
    id: 'document-recently-edited',
    component: _components_top_bar_recently_edited__WEBPACK_IMPORTED_MODULE_3__["default"]
  });
}
function registerButton() {
  _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_0__.toolsMenu.registerToggleAction({
    id: 'toggle-site-navigation-panel',
    priority: 20,
    useProps: _hooks_use_toggle_button_props__WEBPACK_IMPORTED_MODULE_5__.useToggleButtonProps
  });
}

/***/ }),

/***/ "@elementor/editor-app-bar":
/*!***********************************************!*\
  !*** external ["elementorV2","editorAppBar"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorAppBar"];

/***/ }),

/***/ "@elementor/editor-documents":
/*!**************************************************!*\
  !*** external ["elementorV2","editorDocuments"] ***!
  \**************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorDocuments"];

/***/ }),

/***/ "@elementor/editor-panels":
/*!***********************************************!*\
  !*** external ["elementorV2","editorPanels"] ***!
  \***********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorPanels"];

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***************************************************!*\
  !*** external ["elementorV2","editorV1Adapters"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorV1Adapters"];

/***/ }),

/***/ "@elementor/env":
/*!**************************************!*\
  !*** external ["elementorV2","env"] ***!
  \**************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["env"];

/***/ }),

/***/ "@elementor/events":
/*!*****************************************!*\
  !*** external ["elementorV2","events"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["events"];

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

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["apiFetch"];

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
/*!********************************************************************!*\
  !*** ./packages/packages/core/editor-site-navigation/src/index.ts ***!
  \********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extendIconsMap: function() { return /* reexport safe */ _icons_map__WEBPACK_IMPORTED_MODULE_0__.extendIconsMap; },
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_1__.init; }
/* harmony export */ });
/* harmony import */ var _icons_map__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./icons-map */ "./packages/packages/core/editor-site-navigation/src/icons-map.ts");
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./init */ "./packages/packages/core/editor-site-navigation/src/init.ts");


}();
(window.elementorV2 = window.elementorV2 || {}).editorSiteNavigation = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorSiteNavigation?.init?.();
//# sourceMappingURL=editor-site-navigation.js.map
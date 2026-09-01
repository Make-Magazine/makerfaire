# Notices Framework

The Notices framework enables GravityKit products to display WP admin notices in a unified interface. By default, all notices appear on the WP admin dashboard screen unless otherwise specified. 

## Table of Contents

- [Notice Types](#notice-types)
  - [Runtime Notices](#runtime-notices)
  - [Stored Notices – Global](#stored-notices--global)
  - [Stored Notices – User](#stored-notices--user)
- [Live Notices](#live-notices)
  - [Implementation Details (backend)](#implementation-details-backend)
  - [Implementation Details (frontend)](#implementation-details-frontend)
- [Notice Parameters](#notice-parameters)
  - [Required Parameters](#required-parameters)
    - [`namespace` (**required**)](#namespace-required)
    - [`slug` (**required**)](#slug-required)
  - [`message` (**required**)](#message-required)
  - [Optional Parameters](#optional-parameters)
    - [`order`](#order)
    - [`severity`](#severity)
    - [`dismissible`](#dismissible)
    - [`globally_dismissible` (for stored notices only)](#globally_dismissible-for-stored-notices-only)
    - [`global_dismiss_capability` (for stored notices only)](#global_dismiss_capability-for-stored-notices-only)
    - [`sticky`](#sticky)
    - [`snooze`](#snooze)
    - [`condition`](#condition)
    - [`extra`](#extra)
    - [`product_name` & `product_icon`](#product_name--product_icon)
    - [`screens`](#screens)
    - [`capabilities`](#capabilities)
    - [`context`](#context)
    - [`scope` (for stored notices only)](#scope-for-stored-notices-only)
    - [`users` (for stored notices only using `scope` = `user`)](#users-for-stored-notices-only-using-scope--user)
    - [`flash` (for stored notices only)](#flash-for-stored-notices-only)
    - [`starts`](#starts)
    - [`expires`](#expires)
  - [Notice Cleanup and Expiration](#notice-cleanup-and-expiration)
    - [`live` (for live notices)](#live-for-live-notices)
- [Storage Keys Reference](#storage-keys-reference)
- [Examples](#examples)
  - [Runtime Notice](#runtime-notice)
  - [Stored Notice – Global](#stored-notice--global)
  - [Stored Notice – User](#stored-notice--user)
  - [Live Notice](#live-notice)
  - [Flash Notice](#flash-notice)
  - [Globally Dismissible Notice](#globally-dismissible-notice)
  - [Notice with Capabilities and Custom Screen](#notice-with-capabilities-and-custom-screen)
  - [Notice with All Parameters](#notice-with-all-parameters)
  - [Using `not:` Prefix for Exclusions](#using-not-prefix-for-exclusions)
    - [Exclude Super Admins from Site-Specific Notice](#exclude-super-admins-from-site-specific-notice)
    - [Show on All Screens Except Sensitive Areas](#show-on-all-screens-except-sensitive-areas)
    - [Exclude Specific Users from Announcement](#exclude-specific-users-from-announcement)
- [Network/Multisite Notices](#networkmultisite-notices)
  - [Helper Methods](#helper-methods)
  - [Usage Examples](#usage-examples)
  - [Considerations](#considerations)
- [Hooks](#hooks)
  - [Filters](#filters)
  - [Actions](#actions)
- [UI](#ui)
  - [Display Modes](#display-modes)
  - [Product-Based Grouping](#product-based-grouping)
  - [Visibility & Collapse Behavior](#visibility--collapse-behavior)
  - [Accessibility & UX](#accessibility--ux)

## Notice Types

### Runtime Notices

These notices are stored in-memory only and are not persisted to the database. They are registered on each request and will be shown if the `condition` callback (optional) returns `true` (default), and all other visibility guards pass. When a user dismisses a runtime notice, this action is tracked per user in their user meta data.

**Example:**
```php
NoticeManager::get_instance()->add_runtime([
    'namespace' => 'gk-gravityview',
    'slug' => 'debug-mode-on',
    'message' => 'Debug mode is enabled!',
    'severity' => 'warning',
    'screens' => [ 'plugins.php' ],
    'condition' => static fn() => defined( 'WP_DEBUG' ) && WP_DEBUG, // Optional; if not set, the notice is always shown.
]);
```

### Stored Notices – Global

These notices are persisted in the database (`wp_options:gk_notices`) and are visible to all users (subject to guards). Dismissal/snooze is tracked per user in user meta.

**Example:**
```php
NoticeManager::get_instance()->add_stored([
    'namespace' => 'gk-gravityview',
    'slug' => 'release-3-0',
    'message' => 'Version 3.0 is here!',
    'severity' => 'success',
    'dismissible' => true,
    'snooze' => [
        '1 hour' => HOUR_IN_SECONDS,
        '1 day' => DAY_IN_SECONDS,
    ],
    'starts' => strtotime('+1 hour'),
    'expires' => strtotime('+30 days'),
]);
```

### Stored Notices – User

These notices are persisted in user meta (`wp_usermeta.gk_notices.defs`) for each target user. They are only visible to specified users. Dismissal removes the notice definition for that user.

**Example:**
```php
NoticeManager::get_instance()->add_stored([
    'namespace' => 'gk-gravityview',
    'slug' => 'onboarding-done',
    'message' => '🎉 Setup complete – enjoy!',
    'scope' => 'user',
    'users' => [ 1, 2, 3 ], // Optional; if not set, the notice is visible to the current user only.
    'flash' => true,
]);
```

## Live Notices

Live notices update their content dynamically via a backend callback and frontend polling, making them useful for progress bars, real-time status updates, etc.

**Example:**
```php
NoticeManager::get_instance()->add_stored([
    'namespace' => 'gk-gravityview',
    'slug' => 'import-progress',
    'message' => 'Importing data…',
    'severity' => 'info',
    'live' => [
        'callback' => 'my_live_notice_callback',
        'refresh_interval' => 5,
        'show_progress' => true,
        'auto_dismiss' => true, // If true, the notice is automatically dismissed when the progress reaches 100.
    ],
]);
```

### Implementation Details (backend)

**Backend Callback Requirements:**
The callback function should return an array with updated notice data:
```php
function my_live_notice_callback( $notice ) {
    return [
        'message' => 'Processing... 50% complete',
        'progress' => 50, // Optional: 0-100 for progress bar
        'extra' => [ 'foo' => 'bar' ], // Optional: additional data included here will be persisted to the database.
    ];
}
```

**Error Handling:**
- If the callback returns `false` or throws an exception, an error is displayed in the UI and polling stops for that notice.

**Interval Constraints:**
- In production (when WP_DEBUG is false), minimum polling interval is enforced as 5 seconds.
- When WP_DEBUG is true, any interval down to 0 seconds is allowed for testing.
- All live polling constants are configurable via `StoredNotice` class constants.

**Performance Optimization:**
- When a stored notice is registered with `add_stored()`, its live callback is NOT executed in the same request.
- The initial data provided during registration is used as-is for the first render.
- Live updates only occur on subsequent page loads or via Ajax polling.

### Implementation Details (frontend)

The Svelte UI implements live notice polling in [livePolling.js](../../UI/Notices/src/utils/livePolling.js). Key details:

- **Registration/Unregistration:**
  - Notices with live updates are registered for polling via `registerLiveNotice(config)` on mount.
  - Each notice provides its ID, refresh interval, and callbacks for update and error handling.
  - Notices can be unregistered individually; polling stops automatically when no notices remain.

- **Polling Interval & Backoff:**
  - First poll happens after the configured interval (no immediate requests for newly registered notices).
  - On each tick, the UI batches all registered notice IDs and requests updates from the backend.
  - If polling fails, exponential backoff is applied (interval doubles up to `LIVE_MAX_REFRESH`).
  - After consecutive errors reach `LIVE_MAX_CONSECUTIVE_ERRORS` (default 3), polling stops and all notices are notified of the error.
  - On recovery from errors, the interval returns to the base refresh rate.

- **Update & Error Delivery:**
  - On successful response, each notice’s `onUpdate` callback is called with the new data, and the progress/notice message is updated.
  - If a notice signals completion (e.g., `result.progress === 100`), it is automatically unregistered.
  - On error, each notice’s `onError` callback is called and a notice error message is displayed.

- **Batching:**
  - All live notices are polled together in a single API request.

- **Polling Interval Selection:**
  - The polling interval is set to the **minimum `refresh_interval`** among all registered live notices.
  - If a notice doesn't specify `refresh_interval`, the server defaults it to 5 seconds.
  - On initial load, polling never runs faster than `LIVE_DEFAULT_REFRESH` (5 seconds) to prevent overwhelming the server.
  - As notices complete and are unregistered, the interval is recalculated based on remaining notices.
  - Example: Notices with intervals [20s, 5s (default), 8s] = polls every 5s. After 5s notice completes, polls every 8s. After 8s notice completes, polls every 20s.


## Notice Parameters

### Required Parameters

These parameters are required for all notice types.

#### `namespace` (**required**)

Groups notices together under a common namespace (unique string). If a text domain is used, the notice will appear associated with the GravityKit product.

**Example:**
```php
'namespace' => 'gk-gravityview'
```

#### `slug` (**required**)

Unique identifier for the notice.

**Example:**
```php
'slug' => 'debug-mode-on'
```

### `message` (**required**)

The message to display in the notice. HTML is allowed.

### Optional Parameters

These parameters are optional for all notice types.

#### `order`

Controls the display order of notices. Lower numbers appear first. Default: `10`.

**Example:**
```php
'order' => 5 // This notice will appear before notices with order 10
```

**NOTE**: The `order` parameter is **not** used in the current sorting logic implemented in `NoticeEvaluator.php`. Notices are sorted in the following order:

1. **Sticky status** (sticky notices first)
2. **Severity priority** (error → warning → success → info)
3. **Namespace** (alphabetical)
4. **Notice ID** (alphabetical for stability)

The `order` parameter is currently reserved for future use but does not affect notice display order.

#### `severity`

Sets the notice severity level. Affects styling and visual appearance. Default: `'info'`.

**Valid values:** `'info'`, `'success'`, `'warning'`, `'error'`

**Example:**
```php
'severity' => 'warning'
```

#### `dismissible`

Controls whether users can dismiss the notice. Default: `true`.

**Example:**
```php
'dismissible' => false // Notice cannot be dismissed.
```

#### `globally_dismissible` (for stored notices only)

Controls whether the notice can be dismissed globally (removed from the database entirely) instead of just per-user dismissal. When enabled, users with the required capability will see an option to dismiss the notice for everyone. Default: `false`.

This is useful for notices about issues that have been resolved where the first admin to fix the issue can dismiss the notice for all users.

**Example:**
```php
'globally_dismissible' => true // Notice can be dismissed for all users
```

#### `global_dismiss_capability` (for stored notices only)

Specifies the capability or capabilities required to dismiss a notice globally. Only applies when `globally_dismissible` is `true`. Default: `'manage_options'`.

Accepts:
- **String**: A single capability
- **Array**: Multiple capabilities (user needs at least one)

**Examples:**
```php
// Single capability
'globally_dismissible' => true,
'global_dismiss_capability' => 'manage_network' // Only super admins can dismiss globally

// Multiple capabilities
'globally_dismissible' => true,
'global_dismiss_capability' => ['manage_network', 'manage_options'] // Super admins OR site admins
```

#### `sticky`

Controls whether the notice is always visible and never collapsed. Default: `false`.

**Example:**
```php
'sticky' => true // Notice remains prominently visible
```

#### `snooze`

Defines available snooze options for users. Associative array of label => duration in seconds. If provided, makes the notice snoozable. Flash notices cannot be snoozed.

**Example:**
```php
'snooze' => [
    '1 hour' => HOUR_IN_SECONDS,
    '1 day' => DAY_IN_SECONDS,
    '1 week' => WEEK_IN_SECONDS,
]
```

#### `condition`

A callable that determines whether the notice should be shown. For runtime notices, this is evaluated on each request. For stored notices, must be a callable string reference. Default: always show notice.

**Example:**
```php
'condition' => static function() { return defined('WP_DEBUG') && WP_DEBUG; }
// For stored notices, use string reference:
'condition' => 'my_condition_function'
```

#### `extra`

Associative array of additional data attached to the notice. This data is persisted to the database for stored notices.

**Example:**
```php
'extra' => [
    'foo' => 'bar',
    'baz' => 'qux',
]
```

#### `product_name` & `product_icon`

Override the default product name and icon for this notice. This is useful for notices that are not associated with a GravityKit product.

When using GravityKit's product text domain as the namespace, the product name and icon will be automatically set based on the product information from Foundation's `ProductManager`. If not provided, notices will be displayed with the default GravityKit icon and name.

**Example:**
```php
'product_name' => 'Custom Product Name',
'product_icon' => 'https://example.com/custom-icon.png'
```

#### `screens`

Controls which admin screens the notice appears on. String, array of strings, callable, or array mixing both. Default: `['dashboard']` - notices appear only on the WP admin dashboard unless otherwise specified.

**Supports `not:` prefix for exclusions**:
- Use `not:` prefix to exclude specific screens
- Exclusions take precedence over inclusions

**Examples:**
```php
// Show only on specific screens (overrides default).
'screens' => [ 'plugins', 'dashboard' ]

// Show on all screens (override default).
'screens' => []

// Show on all screens EXCEPT plugins and themes.
'screens' => [ 'not:plugins', 'not:themes' ]

// Callable for complex logic.
'screens' => function( $notice, $screen ) { return $screen && $screen->id === 'my_custom_screen'; }
```

#### `capabilities`

Restricts notice to users with specific capabilities. String or array of strings. If not set, the notice is visible to all users.

**Supports `not:` prefix for exclusions**:
- Use `not:` prefix to exclude users with specific capabilities
- Exclusions take precedence over inclusions
- If only exclusions are specified, notice shows to all users except those with excluded capabilities

**Examples:**
```php
// Show only to users with these capabilities
'capabilities' => [ 'manage_options', 'gravityview_edit_entries' ]

// Show to everyone EXCEPT super admins
'capabilities' => [ 'not:manage_network' ]

// Show to editors but NOT super admins
'capabilities' => [ 'edit_posts', 'not:manage_network' ]

// Show to all admins EXCEPT network admins
'capabilities' => [ 'manage_options', 'not:manage_network' ]
```

#### `context`

Controls which WP admin contexts the notice appears in. String or array of strings. Default: `['site', 'ms_subsite']`.

**Valid values:** `'ms_network'`, `'ms_main'`, `'ms_subsite'`, `'site'`, `'user'`, or `'all'`

- `'ms_network'` - Only shows in Network Admin (`/wp-admin/network/`)
- `'ms_main'` - Only shows in the main site of a multisite network
- `'ms_subsite'` - Only shows in subsites of a multisite network (not the main site)
- `'site'` - Only shows in single-site WP installations
- `'user'` - Only shows in User Admin (multisite user dashboard)
- `'all'` - Shows in all admin contexts

**Examples:**
```php
// Single context
'context' => 'ms_network'

// Multiple contexts
'context' => [ 'ms_network', 'ms_main' ]

// Default (shows in single-site and multisite subsites)
'context' => [ 'site', 'ms_subsite' ]

// All contexts
'context' => 'all'
```

**Important:** By default, notices appear in single-site installations and multisite subsites, but NOT in network admin or the main site of a multisite network. This prevents super admin notification overload while ensuring notices are visible in the most common contexts.

#### `scope` (for stored notices only)

Controls the scope of the notice. If `user`, the notice is visible to the current user only. If `global`, the notice is visible to all users. If not set, the notice is visible to all users. 

#### `users` (for stored notices only using `scope` = `user`)

Controls the users the notice is visible to. Array of user IDs. If not set, the notice is visible to the current user only.

**Supports `not:` prefix for exclusions**:
- Use `not:` prefix to exclude specific user IDs
- Exclusions take precedence over inclusions
- If only exclusions are specified, notice is stored globally with user exclusions

**Examples:**
```php
// Show only to specific users
'users' => [ 1, 2, 3 ]

// Show to all users EXCEPT user 1 and 5
'scope' => 'user',
'users' => [ 'not:1', 'not:5' ]

// Show to users 2, 3, 4 but never to user 1
'users' => [ 2, 3, 4, 'not:1' ]
```

**Note:** When only exclusions are provided, the notice is stored as a global notice with an exclusion list, making it more efficient than storing for each individual user.

#### `flash` (for stored notices only)

If `true`, the notice is displayed once and then automatically dismissed. For global flash notices, dismissal is tracked per-user (not removed from storage). For user-scoped flash notices, the notice is removed from that user's storage after display.

**Example:**
```php
'flash' => true
```

#### `starts`

Delays display until a specific time (Unix timestamp). If set, notice is not shown until after this time. `0` or unset = active immediately.

**Example:**
```php
'starts' => strtotime('+2 days')
```

#### `expires`

Hides and purges the notice after a specific time (Unix timestamp). If set, notice is deleted from storage after this time. `0` or unset = never expires automatically.

**Example:**
```php
'expires' => strtotime('+1 week')
```

### Notice Cleanup and Expiration

The framework automatically handles cleanup of expired notices:

- **Automatic Cleanup**: Expired notices are automatically removed from storage before evaluation
- **Global Notices**: Removed from `wp_options` when expired
- **User Notices**: Removed from user meta when expired
- **Performance**: Cleanup happens transparently during normal notice retrieval

Note: Flash notices have special cleanup behavior - see the `flash` parameter documentation.

#### `live` (for live notices)

Configuration object for live notices that update dynamically via polling. All live notice parameters:

- **`callback`** (required): Callable string that returns updated notice data
- **`refresh_interval`** (optional): Polling interval in seconds (default: 5, min: 5, max: 60)
- **`show_progress`** (optional): Whether to display a progress bar (default: false)
- **`progress`** (optional): Initial progress value (0-100)
- **`auto_dismiss`** (optional): Auto-dismiss when progress reaches 100% (default: false)
- **`auto_hide_progress`** (optional): Auto-hide progress bar when progress reaches 100% (default: false)

**Note:** `auto_dismiss` removes the entire notice when complete, while `auto_hide_progress` only hides the progress bar but keeps the notice visible. Use `auto_hide_progress` when you want to show a completion message without the visual clutter of a full progress bar.

**Polling behavior:**
- Frontend polls at the fastest interval among all active live notices
- Intervals are constrained between 5-60 seconds (unless WP_DEBUG is enabled)
- After 3 consecutive errors, polling stops
- Exponential backoff applied on errors

**Example:**
```php
'live' => [
    'callback' => 'my_progress_callback',
    'refresh_interval' => 10,
    'show_progress' => true,
    'progress' => 25,
    'auto_dismiss' => true,
]
```

## Storage Keys Reference

| Key                       | Storage        | Description |
|---------------------------|----------------|-------------|
| `gk_notices`              | `wp_options`     | Global stored definitions |
| `gk_notices.defs`         | `wp_usermeta`      | User stored definitions |
| `gk_notices.actions`      | `wp_usermeta`      | Per-user state (dismissed, snoozed) |

# Examples

## Runtime Notice

```php
NoticeManager::get_instance()->add_runtime([
    'namespace'    => 'gk-gravityview',
    'slug'         => 'ai-search-enabled',
    'message'      => 'AI-powered search bar is enabled for GravityView!',
    'severity'     => 'info',
    'screens'      => [ 'plugins' ],
    'capabilities' => [ 'manage_options' ],
    'condition'    => static function () { return defined('GV_AI_SEARCH') && GV_AI_SEARCH; },
    'extra'        => [ 'docs_url' => 'https://www.gravitykit.com' ],
]);
```

## Stored Notice – Global

```php
NoticeManager::get_instance()->add_stored([
    'namespace'    => 'gk-gravityexport',
    'slug'         => 'gravityexport-2-0',
    'message'      => 'GravityExport 2.0 is here! Check out what’s new.',
    'severity'     => 'success',
    'dismissible'  => true,
    'snooze'       => [
        '1 day'  => DAY_IN_SECONDS,
        '1 week' => WEEK_IN_SECONDS,
    ],
    'screens'      => [ 'dashboard', 'gravityexport_page' ],
    'starts'       => strtotime('+1 day'),
    'expires'      => strtotime('+14 days'),
    'extra'        => [ 'changelog_url' => 'https://www.gravitykit.com' ],
]);
```

## Stored Notice – User

```php
NoticeManager::get_instance()->add_stored([
    'namespace'    => 'gk-gravityimport',
    'slug'         => 'onboarding-complete',
    'message'      => '🎉 You’ve completed onboarding for GravityImport!',
    'scope'        => 'user',
    'users'        => [ 5 ], // Only user ID 5.
    'flash'        => true,
    'screens'      => [ 'gravityimport_page' ],
    'extra'        => [ 'help_url' => 'https://www.gravitykit.com' ],
]);
```

## Live Notice

```php
NoticeManager::get_instance()->add_stored([
    'namespace'    => 'gk-gravityimport',
    'slug'         => 'import-progress',
    'message'      => 'Importing entries…',
    'severity'     => 'info',
    'live'         => [
        'callback'           => 'gk_gravityimport_import_progress',
        'refresh_interval'   => 5,
        'show_progress'      => true,
        'auto_hide_progress' => true, // Hide progress bar when complete but keep notice.
    ],
    'screens'      => [ 'gravityimport_page' ],
    'capabilities' => [ 'manage_options' ],
]);
```

## Flash Notice

```php
NoticeManager::get_instance()->add_stored([
    'namespace'    => 'gk-gravitycalendar',
    'slug'         => 'summer-promo',
    'message'      => '🌞 Summer Sale! Get 20% off GravityCalendar.',
    'severity'     => 'success',
    'flash'        => true,
    'screens'      => [ 'dashboard' ],
    'expires'      => strtotime('+7 days'),
    'extra'        => [ 'promo_url' => 'https://www.gravitykit.com' ],
]);
```

**Note:** Global flash notices are dismissed per-user rather than removed entirely, allowing other users to see them. User-scoped flash notices are removed from the specific user's storage after display.

## Globally Dismissible Notice

This example shows a notice about a configuration issue that can be dismissed for all users once the problem is resolved:

```php
NoticeManager::get_instance()->add_stored([
    'namespace'             => 'gk-gravityview',
    'slug'                  => 'misconfigured-page-warning',
    'message'               => 'Page X is misconfigured. Please review <a href="/wp-admin/post.php?post=123&action=edit">page settings</a>.',
    'severity'              => 'error',
    'dismissible'           => true,
    'globally_dismissible'  => true,  // Allow admins to dismiss for everyone
    'global_dismiss_capability' => 'manage_options', // Only admins can dismiss globally
    'scope'                 => 'global',
    'capabilities'          => [ 'manage_options' ], // Only show to admins
]);
```

When an admin with `manage_options` capability dismisses this notice, they'll see a confirmation dialog asking whether to dismiss it for everyone (removing it from the database) or just for themselves. This is useful for notices about issues that, once resolved by one admin, don't need to be shown to others.

## Notice with Capabilities and Custom Screen

```php
NoticeManager::get_instance()->add_stored([
    'namespace'    => 'gk-gravityview-magic-links',
    'slug'         => 'magic-links-admin',
    'message'      => 'Manage Magic Links settings here.',
    'capabilities' => [ 'manage_options', 'edit_posts' ],
    'screens'      => [ 'magic_links_settings' ],
    'extra'        => [ 'settings_url' => 'https://www.gravitykit.com' ],
]);
```

## Notice with All Parameters

```php
NoticeManager::get_instance()->add_stored([
    'namespace'      => 'gk-gravitycharts',
    'slug'           => 'charts-advanced-feature',
    'message'        => 'Try the new advanced charting features in GravityCharts!',
    'order'          => 5,
    'severity'       => 'info',
    'dismissible'    => true,
    'sticky'         => false,
    'snooze'         => [ 
        '1 hour' => HOUR_IN_SECONDS, 
        '1 day' => DAY_IN_SECONDS,
        '1 week' => WEEK_IN_SECONDS 
    ],
    'condition'      => 'gravitycharts_advanced_features_enabled',
    'extra'          => [ 
        'feature_url' => 'https://www.gravitykit.com/extensions/gravitycharts/',
        'docs_url' => 'https://docs.gravitykit.com/article/gravitycharts/',
        'cta_text' => 'Learn More'
    ],
    'product_name'   => 'GravityCharts Pro',
    'product_icon'   => 'https://www.gravitykit.com/wp-content/uploads/gravitycharts-icon.png',
    'screens'        => [ 'gravitycharts_page', 'dashboard' ],
    'capabilities'   => [ 'manage_options', 'gravitycharts_edit' ],
    'scope'          => 'global',
    'flash'          => false,
    'starts'         => strtotime('+2 days'),
    'expires'        => strtotime('+10 days'),
    'live'           => [
        'callback'           => 'gravitycharts_feature_progress',
        'refresh_interval'   => 10,
        'show_progress'      => true,
        'progress'           => 0,
        'auto_hide_progress' => true,
    ],
]);
```

## Using `not:` Prefix for Exclusions

### Exclude Super Admins from Site-Specific Notice

```php
NoticeManager::get_instance()->add_stored([
    'namespace'    => 'gk-gravityview',
    'slug'         => 'site-maintenance',
    'message'      => 'Site maintenance scheduled for this weekend.',
    'severity'     => 'warning',
    'capabilities' => [ 'manage_options', 'not:manage_network' ], // Site admins but NOT super admins
]);
```

### Show on All Screens Except Sensitive Areas

```php
NoticeManager::get_instance()->add_stored([
    'namespace' => 'gk-gravityexport',
    'slug'      => 'new-feature',
    'message'   => 'New export format available!',
    'screens'   => [ 'not:users', 'not:tools', 'not:options-general' ], // Everywhere except these screens
]);
```

### Exclude Specific Users from Announcement

```php
NoticeManager::get_instance()->add_stored([
    'namespace' => 'gk-gravityimport',
    'slug'      => 'beta-feature',
    'message'   => 'Try our new beta import feature!',
    'scope'     => 'user',
    'users'     => [ 'not:1', 'not:5' ], // All users except IDs 1 and 5
]);
```

## Network/Multisite Notices

The Notices framework provides helper methods through the `NoticeHelpers` class to easily target notices to specific user types in WP multisite environments.

### Helper Methods

The `NoticeHelpers` class provides the following static methods:

- **`network_only()`** - Shows notices only in network admin area to super admins
- **`sites_only()`** - Shows in all site admin areas including single-site, main site, and subsites (excludes network admin)
- **`sites_exclude_super()`** - Shows to site admins, excluding network super admins
- **`non_admins_only()`** - Shows only to non-admin users
- **`specific_sites($site_ids)`** - Shows only on specific multisite site(s), accepts array or 'main'
- **`main_site_only()`** - Shows on single-site installations and the main site of multisite networks
- **`subsites_only()`** - Shows only on multisite subsites (never shows in single-site)
- **`all_contexts()`** - Shows in all admin contexts
- **`with_capabilities($capabilities, $context)`** - Shows to users with specific capabilities
- **`on_screens($screens, $capabilities, $context)`** - Shows on specific screens
- **`exclude_super_admin($base_config)`** - Wraps any config to exclude super admins

### Usage Examples

```php
use GravityKit\GravityView\Foundation\Notices\NoticeManager;
use GravityKit\GravityView\Foundation\Notices\NoticeHelpers;

// 1. Network Admin Only - Shows only in network admin area
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-network',
    'slug' => 'network-maintenance',
    'message' => 'Network-wide maintenance scheduled for midnight',
    'severity' => 'warning',
], NoticeHelpers::network_only()));

// 2. All Sites Admin Areas - Shows in site admin areas but NOT network admin
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-site',
    'slug' => 'site-update',
    'message' => 'New features available for your site',
    'severity' => 'info',
], NoticeHelpers::sites_only()));

// 3. Site Admins Excluding Super Admins - Prevents super admin overload
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-site',
    'slug' => 'plugin-update',
    'message' => 'Plugin update required',
    'severity' => 'warning',
], NoticeHelpers::sites_exclude_super()));

// 4. Non-Admin Users Only - Shows to regular users
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-users',
    'slug' => 'profile-incomplete',
    'message' => 'Please complete your profile information',
    'severity' => 'info',
], NoticeHelpers::non_admins_only()));

// 5. Specific Sites - Shows only on sites 2 and 3
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-site',
    'slug' => 'site-specific-notice',
    'message' => 'This notice only appears on sites 2 and 3',
], NoticeHelpers::specific_sites([2, 3])));

// 6. Custom Capability with Context - Shows to users with custom capability in all site contexts
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-custom',
    'slug' => 'editor-notice',
    'message' => 'Content review needed',
], NoticeHelpers::with_capabilities('edit_others_posts')));

// 7. Main Site Only - Shows only on the main site
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-main',
    'slug' => 'main-site-notice',
    'message' => 'This notice only appears on the main site',
], NoticeHelpers::main_site_only()));

// 8. Subsites Only - Shows on all subsites except main
NoticeManager::get_instance()->add_stored(array_merge([
    'namespace' => 'gk-subsites',
    'slug' => 'subsites-notice',
    'message' => 'This notice appears on all subsites but not the main site',
], NoticeHelpers::subsites_only()));

// 9. Exclude Super Admin from Any Notice - Wraps existing config to exclude super admins
$notice_config = [
    'namespace' => 'gk-general',
    'slug' => 'general-alert',
    'message' => 'Important: This alert is for site admins only',
    'capabilities' => ['manage_options'],
];
NoticeManager::get_instance()->add_stored(
    NoticeHelpers::exclude_super_admin($notice_config)
);

// 10. Using not: prefix directly (alternative to helper)
NoticeManager::get_instance()->add_stored([
    'namespace' => 'gk-general',
    'slug' => 'general-alert-v2',
    'message' => 'Important: This alert is for site admins only',
    'capabilities' => ['manage_options', 'not:manage_network'], // Same effect as exclude_super_admin
]);
```

### Considerations

**Distinguishing Between Sites in Multisite:**

The context system automatically distinguishes between different site types during page render:

- `'site'` - Single-site WP installations only
- `'ms_main'` - Main site of a multisite network only (detected via `Core::is_main_network_site()`)
- `'ms_subsite'` - Subsites in a multisite network (detected via `Core::is_not_main_network_site()`)
- `'ms_network'` - Network admin area only (detected via `is_network_admin()`)
- `'user'` - User admin area (detected via `is_user_admin()`)

Context detection happens during initial page render to determine which notices to display. Ajax operations (dismiss, snooze, live updates) work with notice IDs directly and don't require context detection.

Helper methods handle these contexts automatically:
- Use `NoticeHelpers::main_site_only()` for single-site + multisite main site
- Use `NoticeHelpers::subsites_only()` for multisite subsites only
- Use `NoticeHelpers::specific_sites([2, 3])` for specific multisite site IDs
- Use `NoticeHelpers::sites_only()` for all site contexts (not network admin)

**WP Capability Reference:**

- `manage_network` - Only network super admins have this capability
- `manage_options` - Site admins (and super admins) have this capability
- `delete_sites` - Network admins who can delete sites
- `create_sites` - Network admins who can create sites
- `read` - All authenticated users have this capability

## Hooks

### Filters

#### Notice Lifecycle Filters

##### `gk/foundation/notices/add`
Filters notice data before a notice is added. This runs for both runtime and stored notices.

```php
apply_filters( 'gk/foundation/notices/add', array $data, string $type );
```

**Parameters:**
- `$data` (array) - The notice data array
- `$type` (string) - Notice type: 'runtime' or 'stored'

##### `gk/foundation/notices/update`
Filters notice data when updating a stored notice.

```php
apply_filters( 'gk/foundation/notices/update', array $updated_def, array $changes, string $notice_id );
```

**Parameters:**
- `$updated_def` (array) - The updated notice definition
- `$changes` (array) - The changes being applied
- `$notice_id` (string) - The notice ID being updated

#### Notice Evaluation Filters

##### `gk/foundation/notices/evaluation/before`
Filters all notices before evaluation begins. Useful for adding or modifying notices based on context.

```php
apply_filters( 'gk/foundation/notices/evaluation/before', array $notices, string $context, array $user_state );
```

**Parameters:**
- `$notices` (array) - Array of NoticeInterface objects
- `$context` (string) - Current admin context
- `$user_state` (array) - User state data

##### `gk/foundation/notices/evaluation/notice`
Filters whether a specific notice should be displayed during evaluation.

```php
apply_filters( 'gk/foundation/notices/evaluation/notice', null|bool $should_display, NoticeInterface $notice, string $context, array $user_state );
```

**Parameters:**
- `$should_display` (null|bool) - Whether to display the notice (null for default evaluation)
- `$notice` (NoticeInterface) - The notice being evaluated
- `$context` (string) - Current admin context
- `$user_state` (array) - User state data
    
##### `gk/foundation/notices/evaluation/after`
Filters the evaluated notices after all evaluation is complete.

```php
apply_filters( 'gk/foundation/notices/evaluation/after', array $evaluated_notices, array $original_notices, string $context, array $user_state );
```

**Parameters:**
- `$evaluated_notices` (array) - Notices that passed evaluation
- `$original_notices` (array) - Original notices before evaluation
- `$context` (string) - Current admin context
- `$user_state` (array) - User state data

##### `gk/foundation/notices/active`
Filters the list of active notices returned by `get_active()`.

```php
apply_filters( 'gk/foundation/notices/active', array $notices );
```

**Parameters:**
- `$notices` (array) - Array of active NoticeInterface objects

#### Rendering Filters

##### `gk/foundation/notices/render/payload`
Filters the JavaScript payload data before rendering notices in the frontend.

```php
apply_filters( 'gk/foundation/notices/render/payload', array $data );
```

**Parameters:**
- `$data` (array) - Payload data containing notices and parameters

**Structure:**
```php
$data = [
    'notices' => [...],      // Array of notice data
    'apiRoute' => '...',     // REST API route
    'nonce' => '...',        // Security nonce
    'isDebug' => true/false, // Debug mode flag
];
```

##### `gk/foundation/notices/render/container`
Filters the HTML container for notices.

```php
apply_filters( 'gk/foundation/notices/render/container', string $html );
```

**Parameters:**
- `$html` (string) - HTML container markup

#### Content Filters

##### `gk/foundation/notices/content/allowed-tags`
Filters the allowed HTML tags and attributes in notice messages.

```php
apply_filters( 'gk/foundation/notices/content/allowed-tags', array $tags );
```

**Parameters:**
- `$tags` (array) - Allowed HTML tags and attributes (wp_kses format)

#### Ajax Response Filters

##### `gk/foundation/notices/ajax/live-update`
Filters live notice update responses from the callback.

```php
apply_filters( 'gk/foundation/notices/ajax/live-update', array|BaseException $response, string $context, StoredNoticeInterface $notice );
```

**Parameters:**
- `$response` (array|BaseException) - Response from the live callback
- `$context` (string) - Current admin context
- `$notice` (StoredNoticeInterface) - The notice being updated

##### `gk/foundation/notices/ajax/live-response`
Filters the final Ajax response for live notice updates.

```php
apply_filters( 'gk/foundation/notices/ajax/live-response', array $response, StoredNoticeInterface $notice );
```

**Parameters:**
- `$response` (array) - The Ajax response data
- `$notice` (StoredNoticeInterface) - The notice being updated

#### User State Filters

##### `gk/foundation/notices/user-state`
Filters user state changes before they are saved.

```php
apply_filters( 'gk/foundation/notices/user-state', array $changes, int $user_id );
```

**Parameters:**
- `$changes` (array) - State changes to be applied
- `$user_id` (int) - User ID

### Actions

#### Notice Lifecycle Actions

##### `gk/foundation/notices/added`
Fired after a notice is successfully added.

```php
do_action( 'gk/foundation/notices/added', NoticeInterface $notice, string $type );
```

**Parameters:**
- `$notice` (NoticeInterface) - The notice that was added
- `$type` (string) - Notice type: 'runtime' or 'stored'

##### `gk/foundation/notices/saved`
Fired after a stored notice is persisted to the database.

```php
do_action( 'gk/foundation/notices/saved', StoredNoticeInterface $notice );
```

**Parameters:**
- `$notice` (StoredNoticeInterface) - The notice that was saved

##### `gk/foundation/notices/removed`
Fired after a notice is removed from storage.

```php
do_action( 'gk/foundation/notices/removed', string $notice_id );
```

**Parameters:**
- `$notice_id` (string) - The ID of the removed notice

#### Rendering Actions

##### `gk/foundation/notices/render/before`
Fired before notices are rendered. Notices array is passed by reference for modification.

```php
do_action_ref_array( 'gk/foundation/notices/render/before', array &$notices, string $context );
```

**Parameters:**
- `$notices` (array) - Array of notices (passed by reference)
- `$context` (string) - Current admin context

#### User Interaction Actions

##### `gk/foundation/notices/ajax/dismissed`
Fired when a user dismisses a notice via Ajax.

```php
do_action( 'gk/foundation/notices/ajax/dismissed', string $notice_id, int $user_id );
```

**Parameters:**
- `$notice_id` (string) - ID of the dismissed notice
- `$user_id` (int) - ID of the user who dismissed the notice

##### `gk/foundation/notices/ajax/snoozed`
Fired when a user snoozes a notice via Ajax.

```php
do_action( 'gk/foundation/notices/ajax/snoozed', string $notice_id, int $user_id, int $snooze_until );
```

**Parameters:**
- `$notice_id` (string) - ID of the snoozed notice
- `$user_id` (int) - ID of the user who snoozed the notice
- `$snooze_until` (int) - Unix timestamp when the snooze expires

## UI

The UI is implemented using Svelte+Tailwind (~18 KB gzipped + ~4 KB CSS) that groups and displays notices in an intelligent, user-friendly manner.

### Display Modes

**Single Notice:**
- When only one notice is active, it displays as a standalone card without grouping headers
- Shows the product icon directly on the card

**Multiple Notices (Notice Group):**
- Multiple notices are grouped together under a single product header
- Shows product name, icon, and total notice count in the header
- Provides bulk actions like "Dismiss all" and expand/collapse functionality

### Product-Based Grouping

Notices are grouped by **product**.

- **Same Product**: When all notices share the same `namespace`, they're grouped under that product's name and icon in the header; individual notices are displayed with their own product icon.
- **Mixed Products**: When notices have different namespaces, it becomes a "mixed product group" using the default GravityKit branding
- **Individual Icons**: In mixed product groups, each notice card shows its own product icon for identification
- **No Product**: When product can't be inferred from the namespace (text domain), the default GravityKit branding is used.

### Visibility & Collapse Behavior

**Sticky Notices:**
- Always visible regardless of collapse state
- Cannot be hidden by the "Show less" feature
- Displayed first in the list

**Non-Sticky Notices:**
- Limited to 3 visible when collapsed
- Additional notices are hidden behind "Show all" button

**Collapse Controls:**
- "Show all"/"Show less" button appears when there are more than 3 non-sticky notices
- Defaults to collapsed when there are collapsible notices
- State is persisted in browser's localStorage

### Accessibility & UX

- Full RTL support
- ARIA live regions for screen readers
- Keyboard navigation with proper focus management
- Error handling with inline error messages
- Animations and transitions respect the `prefers-reduced-motion` user preference
- Proper semantic HTML with roles and labels

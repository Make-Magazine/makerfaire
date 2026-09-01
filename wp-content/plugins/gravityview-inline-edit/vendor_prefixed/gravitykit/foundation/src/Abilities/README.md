# GravityKit Abilities (Foundation)

WordPress 6.9 shipped the Abilities API: a registry of typed,
schema-validated server actions discoverable through REST, MCP, and (in
WP 7.0) the admin Command Palette. Foundation does not reinvent any of
that. It adds the small set of things WordPress does not provide: a
GravityKit-shaped registration facade, a permission wrapper that supports
per-ability enable/disable plus rate-limiting, a cross-product metadata
contract, batch contract primitives, REST catalog endpoints, an admin
Settings UI, and the WP 7.0 Command Palette opt-in.

**Foundation hosts no abilities of its own.** It is plumbing. Consuming
plugins bring their own ability surfaces.

## Design Philosophy

> **Do not abstract what WordPress already provides. Add only what's missing.**

- Native `wp_register_ability()` does the registration. Foundation does not
  wrap it in a competing primitive.
- Native annotations (`readonly`, `destructive`, `idempotent`) drive method
  routing in the core run controller. Foundation does not invent a parallel
  taxonomy.
- Native `meta` storage carries everything. Foundation stamps GravityKit
  cross-product keys into the same bag.
- Foundation adds only what WordPress does not have: a GravityKit
  registration hook, an enable/disable layer with rate-limit short-circuit,
  cross-product metadata, batch primitives, a settings UI, and a discovery
  catalog endpoint.

## Requirements

| Layer | Minimum |
| --- | --- |
| WordPress | 6.9 (ships the Abilities API and `/wp-abilities/v1/`) |
| WordPress (Command Palette opt-in) | 7.0 (`@wordpress/core-abilities` client) |
| GravityKit Foundation | 1.23.0 |
| PHP | 7.4 |

If `wp_register_ability()` and `wp_register_ability_category()` are not
defined, `Framework::init()` returns silently — the early-return guard is
at `Framework.php:91-93`; the registration work that's skipped runs
through `Framework.php:94-110`.

## What One Ability Config Produces

A GravityKit plugin describes one action as a single ability config and
hands it to Foundation. Foundation forwards it to `wp_register_ability()`
after wrapping the permission callback, normalizing the category, and
stamping cross-product metadata. From one config you get:

- a WordPress-registered ability at
  `/wp-json/wp-abilities/v1/abilities/{name}/run`
- a row in the Foundation catalog at `/wp-json/gravitykit/v1/abilities`
- a checkbox on the GravityKit settings page that toggles it on or off
- a Command Palette entry (if opted in) in WordPress 7.0+
- an MCP tool name derivable from the ability name

There is no parallel registry. `wp_get_abilities()` returns the same
`WP_Ability` objects Foundation registered.

## Registering an Ability

Two registration shapes; both end at `Manager::register()` and the runtime
is identical (`Framework.php:207-254`). Pick one shape per
ability file. The register/before action runs first, the register filter
runs after.

### Path A: array config on `gk/foundation/abilities/register/before`

```php
<?php

use GravityKit\Foundation\Abilities\Manager;
use WP_Error;

add_action(
    'gk/foundation/abilities/register/before',
    static function ( Manager $manager ): void {
        $manager->register(
            [
                'name'                => 'gk-myplugin/items-list',
                'label'               => __( 'List Items', 'gk-myplugin' ),
                'description'         => __( 'Return items visible to the current user.', 'gk-myplugin' ),
                'category'            => 'gk-myplugin-discovery',
                'input_schema'        => [
                    'type'                 => 'object',
                    'properties'           => [
                        'limit' => [ 'type' => 'integer', 'default' => 20, 'minimum' => 1, 'maximum' => 100 ],
                    ],
                    'additionalProperties' => false,
                ],
                'output_schema'       => [
                    'type'       => 'object',
                    'properties' => [
                        'items' => [
                            'type'  => 'array',
                            'items' => [
                                'type'       => 'object',
                                'properties' => [
                                    'id'    => [ 'type' => 'integer' ],
                                    'title' => [ 'type' => 'string' ],
                                ],
                                'required'   => [ 'id', 'title' ],
                            ],
                        ],
                    ],
                    'required'   => [ 'items' ],
                ],
                'execute_callback'    => static function ( array $input ): array {
                    return [ 'items' => fetch_items( (int) ( $input['limit'] ?? 20 ) ) ];
                },
                'permission_callback' => static function () {
                    if ( current_user_can( 'edit_posts' ) ) {
                        return true;
                    }

                    return new WP_Error(
                        'rest_forbidden',
                        __( 'You are not allowed to list items.', 'gk-myplugin' ),
                        [ 'status' => 403 ]
                    );
                },
                'meta'                => [
                    'show_in_rest' => true,
                    'annotations'  => [
                        'readonly'    => true,
                        'destructive' => false,
                        'idempotent'  => true,
                    ],
                ],
            ]
        );
    }
);
```

Required keys after normalization: `name`, `label`, `description`,
`category`, `execute_callback`, `permission_callback`
(`Manager.php:201-237`). Legacy top-level `annotations` and
`show_in_rest` are merged into `meta` and unset before the config reaches
`wp_register_ability()` (`Manager.php:290, :627-664`).

### Path B: fluent builder on `gk/foundation/abilities/register`

Both paths are first-class. In-tree GravityView abilities use **Path A**
(array config on `gk/foundation/abilities/register/before`), so Path A is the
dominant canonical example to grep when writing a new ability. Path B is
exercised by `AbilityBuilderTest` and remains a published API contract —
the snake_case-only method surface is the load-bearing constraint.


```php
<?php

use GravityKit\Foundation\Abilities\Manager;

add_filter(
    'gk/foundation/abilities/register',
    static function ( array $abilities, Manager $manager ): array {
        $manager
            ->builder( 'gk-myplugin/items-count' )
            ->label( __( 'Count Items', 'gk-myplugin' ) )
            ->description( __( 'Return the number of visible items.', 'gk-myplugin' ) )
            ->category( 'gk-myplugin-discovery' )
            ->capability( 'edit_posts' )
            ->input( [ 'type' => 'object', 'properties' => [], 'additionalProperties' => false ] )
            ->output(
                [
                    'type'       => 'object',
                    'properties' => [ 'count' => [ 'type' => 'integer' ] ],
                    'required'   => [ 'count' ],
                ]
            )
            ->readonly()
            ->idempotent()
            ->show_in_rest()
            ->callback( static function (): array {
                return [ 'count' => 0 ];
            } )
            ->register();

        return $abilities;
    },
    10,
    2
);
```

### Builder methods

All methods are **snake_case only** — no camelCase aliases
(`AbilityBuilder.php`).

| Method | Sets |
| --- | --- |
| `label( string )` | `label` |
| `description( string )` | `description` |
| `category( string )` | adds one slug; first added is the primary |
| `categories( array )` | bulk add of category slugs |
| `capability( string )` | `permission_callback` of `current_user_can( $cap )` |
| `permission( callable )` | custom `permission_callback` |
| `input( array )` | `input_schema` |
| `output( array )` | `output_schema` |
| `callback( callable )` | `execute_callback` |
| `meta( string, mixed )` | arbitrary `meta[$key]` |
| `readonly( bool = true )` | `meta.annotations.readonly` |
| `destructive( bool = true )` | `meta.annotations.destructive` |
| `idempotent( bool = true )` | `meta.annotations.idempotent` |
| `command_palette( bool = true )` | `meta.command_palette` |
| `show_in_rest( bool = true )` | `meta.show_in_rest` |
| `user_description( string )` | `meta.user_description` (plain-language text for human surfaces) |
| `user_visible( bool = true )` | `meta.user_visible` (opt into the Settings UI / human surfaces) |
| `to_array()` | returns accumulated config without registering |
| `register()` | calls `Manager::register()`; returns `true\|WP_Error` |

`Manager::builder( $name )` and `Manager::ability( $name )` both return a
fresh `AbilityBuilder` (`Manager.php:348-365`).

## Name Format

The regex is `/^gk-[a-z0-9-]+\/[a-z0-9-]+$/`
(`Manager.php`). Names that fail return
`gk_abilities_invalid_name` and never reach `wp_register_ability()`.
Every config-validation failure (invalid name, missing field, wrong
type, non-callable callback) also fires `_doing_it_wrong()` so the
failure surfaces in `WP_DEBUG` even when the caller discards the
returned `WP_Error` — which is the in-tree default.

| Input | Valid? | Why |
| --- | --- | --- |
| `gk-myplugin/items-list` | yes | matches |
| `gk-some-plugin/items-bulk-delete` | yes | dashes allowed in slug + action |
| `myplugin/items-list` | no | missing `gk-` |
| `gk-myplugin/items_list` | no | underscore |
| `gk-myplugin/items/list` | no | second slash |
| `gk-MyPlugin/items-list` | no | uppercase |

## Categories

Foundation contributes **no categories of its own**. Each product
declares its product-level data — MCP tool-name prefix plus its
**product category** (`gk-{product}`) and **scope subcategories** —
in one payload on the `gk/foundation/abilities/products` filter.
Foundation registers the declared categories when the Abilities API
category registry is ready (`Framework.php:141-198`):

```php
<?php

add_filter(
    'gk/foundation/abilities/products',
    static function ( array $products ): array {
        $products['myplugin'] = [
            'mcp_prefix' => 'mp',
            'categories' => [
                'gk-myplugin'           => [
                    'label'       => __( 'MyPlugin', 'gk-myplugin' ),
                    'description' => __( 'MyPlugin operations.', 'gk-myplugin' ),
                ],
                'gk-myplugin-discovery' => [
                    'label'       => __( 'MyPlugin Discovery', 'gk-myplugin' ),
                    'description' => __( 'Readonly discovery endpoints.', 'gk-myplugin' ),
                ],
            ],
        ];

        return $products;
    }
);
```

Malformed declarations (non-array product or category definitions,
empty slugs) are skipped with a `_doing_it_wrong()` notice.

A scope subcategory's slug must start with `gk-{product}-` for scope
derivation to fire. The primary category drives the `gk_scope` metadata
stamp: an ability whose category is `gk-myplugin-discovery` gets
`gk_scope = discovery` (`Manager.php:950-961`).

### Recommended scope vocabulary

Cross-product catalog slices (`?scope=discovery`) only work when
products converge on the same scope suffixes. Reuse these before
inventing a new one:

- `discovery` — readonly list/get endpoints (use with
  `annotations.readonly = true`)
- `configuration` — settings reads and writes

GravityView's scope set (`views`, `fields`, `widgets`, `search-fields`,
`grid`, `preview`) is the precedent for entity-shaped scopes. For
read-vs-write slicing, don't add a scope — that's what the
`readonly`/`destructive` annotations are for.

## Cross-Product Metadata (Auto-Stamped)

Foundation stamps four metadata keys on every registered ability during
normalization (`Manager.php:670-687`). **Consuming plugins
do not set these manually** — overriding `gk_registered_by` `gk_product`
/ `gk_scope` would break catalog filters in other products. Cross-product
tooling should filter on these keys rather than parsing names or product
classes (`Framework.php:7-17`).

| Meta key | Source | Example |
| --- | --- | --- |
| `gk_registered_by` | constant `'gravitykit'` | `gravitykit` |
| `gk_product` | the slug between `gk-` and the first `/` in the ability name | `gk-myplugin/items-list` → `myplugin` |
| `gk_scope` | suffix of the primary category when it matches `gk-{product}-{scope}` | `gk-myplugin-discovery` → `discovery` |
| `gk_contract_version` | `meta.gk_contract_version` if a string, else `1.0.0` | `1.0.0` |

```php
<?php

use WP_Ability;

$myplugin_discovery = array_filter(
    GravityKitFoundation::abilities()->all(),
    static function ( WP_Ability $ability ): bool {
        return 'myplugin'  === $ability->get_meta_item( 'gk_product' )
            && 'discovery' === $ability->get_meta_item( 'gk_scope' );
    }
);
```

`Manager::by_product( 'myplugin' )` and `Manager::by_scope( 'discovery' )`
are convenience wrappers (`Manager.php`). They (and the matching
`GravityKitFoundation::abilities()->by_product()` / `->by_scope()` /
`->categories()` facade methods on `Framework.php`) are the documented
cross-product contract surface — other GravityKit products will use them
once they wire through Foundation. Keep them even when GravityView is the
only in-tree caller.

**Why `gk_contract_version` and `gk_registered_by` exist.** Both carry
information not derivable from name or category. `gk_contract_version` is
a semver compatibility tag a client can pin against (`1.x` clients refuse
to call once a server stamps `2.0.0`). `gk_registered_by` distinguishes
abilities Foundation registered (with the metadata contract) from
abilities a third party called `wp_register_ability()` for directly, so
cross-product tooling can filter to the GK family even when both coexist.

## Annotations

Annotations live in `meta.annotations`. Foundation carries WordPress's
native vocabulary verbatim and adds two domain conventions.

| Annotation | Meaning |
| --- | --- |
| `readonly` | the ability never writes |
| `destructive` | the ability irreversibly deletes or destroys state |
| `idempotent` | N identical calls have the same effect as one |
| `batch` | the ability accepts the [batch input shape](#batch-contract) |
| `expensive` | optional marker for slow / broad scans (advisory) |

`readonly` `destructive` `idempotent` drive the core run controller's
HTTP-method enforcement (see [REST surface](#rest-surface)). Always set
them explicitly on any ability that opts into the Command Palette.

## The Wrapped Permission Pipeline

`Manager::register()` wraps the consumer's `permission_callback` before
handing it to `wp_register_ability()` (`Manager.php:577-615`).
On every invocation the wrapped callback runs three steps in order:

1. **Rate-limit filter** — `gk/foundation/abilities/rate-limit` is applied
   with three args (`null`, ability name, validated input array).
   Returning a `WP_Error` short-circuits the chain. Returning `null` (the
   default) passes through.
2. **Disabled-state check** — if the ability is in the
   `gk_abilities_disabled` option, return `ability_disabled` (HTTP 403).
3. **Consumer's `permission_callback`** — the original callback. If the
   ability has no `input_schema`, the wrapper omits the input argument.

### Permission callback return values

| Return | Result |
| --- | --- |
| `true` | allow |
| `false` | deny with WordPress's generic `rest_forbidden` (403) |
| `WP_Error` | deny with that specific code, message, and `data.status` |

### The WP_Error truthy-guard pitfall

`WP_Error` is **truthy** in PHP. The naive `if ( ! $permission )` lets
denials slip through:

```php
<?php

// Wrong: WP_Error is truthy, the guard never trips, the denial is
// silently treated as a pass.
$permission = $authz->can_edit( $id );

if ( ! $permission ) {
    return new WP_Error( 'rest_forbidden', 'No.', [ 'status' => 403 ] );
}
```

Always check `is_wp_error()` first, then compare against exact `true`:

```php
<?php

use WP_Error;

$permission = $authz->can_edit( $id );

if ( is_wp_error( $permission ) ) {
    return $permission;
}

if ( true !== $permission ) {
    return new WP_Error(
        'rest_forbidden',
        __( 'Sorry, you are not allowed to do that.', 'gk-myplugin' ),
        [ 'status' => 403 ]
    );
}
```

Never write `if ( ! $permission )` when the callee can return `WP_Error`.

### Rate-limit filter

```php
<?php

add_filter(
    'gk/foundation/abilities/rate-limit',
    static function ( $decision, string $name, array $input ) {
        if ( null !== $decision ) {
            return $decision;
        }

        if ( 'gk-myplugin/items-scan' !== $name ) {
            return null;
        }

        $per_page = isset( $input['per_page'] ) ? (int) $input['per_page'] : 0;
        if ( $per_page > 50 ) {
            return new WP_Error(
                'gk_abilities_rate_limited',
                __( 'Reduce per_page and try again.', 'gk-myplugin' ),
                [ 'status' => 429 ]
            );
        }

        return null;
    },
    10,
    3
);
```

The filter runs for *every* registered ability on *every* invocation, so
guard with `null === $decision` and a name match before doing real work.
The third argument is the validated input (`[]` for input-less
abilities) (`Manager.php:592`).

## Batch Contract

A batch-capable ability accepts a `batch` array of N similar operations
and applies them atomically. Foundation ships the shared contract;
consuming plugins ship the validation pass and the repository writes.
Opt in by setting `meta.annotations.batch = true`.

### Input shape

When using batch mode, only four top-level keys are allowed:

| Key | Required | Notes |
| --- | --- | --- |
| `id` | yes | identifier of the parent resource the batch targets |
| `version` | yes | optimistic-concurrency token from a prior read |
| `dry_run` | no | bool; when `true`, validate and plan but do not persist |
| `batch` | yes | array of 1–50 items |

Any other top-level key is **mixed mode** and must be rejected with HTTP
400 `gk_batch_mixed_mode` before any item is attempted.

Cap `batch` at `maxItems: 50` in the JSON Schema so the WP request
validator rejects oversize batches before the callback runs. Repositories
should enforce the same cap defensively. 50 items is the recommended
upper bound so validation and persistence fit inside a typical 5-second
per-resource lock window.

### Detecting batch input

```php
<?php

use GravityKit\Foundation\Abilities\Support\BatchContract;

$config['execute_callback'] = static function ( array $input ) use ( $repository ) {
    if ( BatchContract::is_batch_input( $input ) ) {
        return run_batch( $input, $repository );
    }

    return run_singleton( $input, $repository );
};
```

`BatchContract::is_batch_input( $input )` returns true when `$input` is
an array whose `batch` key holds an array — including an empty one.
Detection is shape-only; enforce minimum batch size via JSON Schema
(`minItems`) so an empty batch fails with a schema error instead of a
misleading singleton missing-field error
(`Support/BatchContract.php:41-49`).

### Apply semantics

A batch executes in two passes inside the same lock:

1. **Validate the whole plan first.** Walk every item, validate against
   the rolling in-memory plan, append every failure as
   `{index, code, message, data}`. **Do not stop at the first invalid
   item.**
2. **If any item failed:** return one HTTP 400
   `gk_batch_validation_failed` with `data.failures` containing every
   per-item error. Nothing persists.
3. **If all items validated:** check the version token. Stale → HTTP 409
   `gk_batch_version_mismatch` with the current version in
   `data.current_version`. Nothing persists.
4. **Apply pass (live):** load the underlying state once, mutate it in
   memory, persist once, bump the version once. Persist failure → HTTP
   500 `gk_batch_persist_failed`.
5. **Apply pass (dry-run):** same validation, no persist, no version
   bump. Generated ids must be plausible-live (real UUIDs, not
   `dry-run-1` placeholders) so client preview rendering matches the
   eventual write.

### Validation-pass aggregated error

```json
{
  "code": "gk_batch_validation_failed",
  "message": "2 of 4 batch items failed validation.",
  "data": {
    "status": 400,
    "failures": [
      { "index": 1, "code": "myplugin_invalid_input", "message": "Item id is required.", "data": { "status": 400 } },
      { "index": 3, "code": "myplugin_duplicate",     "message": "Item already exists.", "data": { "status": 400 } }
    ]
  }
}
```

### Version-token concurrency

Every batch carries a `version` token from a prior read. The token shape
is owned by the consuming plugin — Foundation does not assume ETag,
`post_modified`, or any specific format. The read endpoint and the batch
endpoint MUST emit the token identically because clients round-trip it
byte-for-byte. Stale tokens return HTTP 409 `gk_batch_version_mismatch`.

### Response envelope

`BatchContract::result_envelope( array $items, bool $dry_run, array $context = [] )`
normalizes every batch response (`Support/BatchContract.php`):

```json
{
  "batch_results": [
    { "index": 0, "result": { "id": 42, "version": "2026-05-20T12:35:10Z:4" } },
    { "index": 1, "result": { "id": 43, "version": "2026-05-20T12:35:10Z:4" } }
  ],
  "dry_run": false,
  "version": "2026-05-20T12:35:10Z:4"
}
```

The v1 contract is **all-or-nothing**: every row that makes it into the
envelope is by definition successful. Validation failures return HTTP 400
`gk_batch_validation_failed` BEFORE the envelope is built (the WP_Error's
`data.failures` carries the per-item rejection list). Persist failures
return HTTP 500 `gk_batch_persist_failed` and write nothing. The envelope
therefore omits per-row `ok` / `error` and the redundant `would_apply`
top-level. The shape is identical for live and dry-run; only `dry_run`
flips.

### Adding batch to any ability

1. Ship the singleton path first. Batch layers on top.
2. Add `version` (string), `dry_run` (boolean), `batch` (array,
   `maxItems: 50`) to the input schema.
3. Set `meta.annotations.batch = true`.
4. In `execute_callback`, branch on `BatchContract::is_batch_input()`.
5. Implement the product-side handler per the apply semantics above.
6. Wrap the response with `BatchContract::result_envelope( $rows,
   $dry_run, [ 'version' => $new_version ] )`.

For a worked reference implementation see GravityView's
`gravityview/src/Abilities/Support/BatchHelpers.php::handle_repository_batch()`,
which threads validation → version-check → plan → atomic persist for all
eight GV batch-capable abilities.

## Support Primitives

`Support/` ships three helpers consuming plugins can use
directly. `BatchContract` is covered above.

### `DryRun`

For abilities whose live path calls `wp_insert_post()`, `wp_update_post()`,
`wp_trash_post()`, or `wp_delete_post()`. Those functions do not have a
`pre_update_post` short-circuit filter, so they cannot be intercepted the
way `update_post_metadata` can.

```php
<?php

use GravityKit\Foundation\Abilities\Support\DryRun;

$result = DryRun::with_dry_run(
    (bool) ( $input['dry_run'] ?? false ),
    static function () use ( $input ) {
        // Validation work runs in both live and dry-run modes.

        return DryRun::with_post_write_guard(
            static function () use ( $input ) {
                $post_id = wp_insert_post( $input['post'] );
                return [ 'post_id' => $post_id ];
            },
            [ 'post_id' => 0 ]
        );
    }
);
```

`DryRun::is_active()` returns true while a `with_dry_run( true, ... )`
frame is on the stack. Inside that frame, `with_post_write_guard()` skips
the live closure and returns the planned-outcome array stamped with the
`dry_run: true` flag (`Support/DryRun.php`). The depth counter is
re-entrant. Metadata-only writes are typically short-circuited via
product-specific metadata filters instead.

### `Projection`

Shared `include` parser for response shaping.

```php
<?php

use GravityKit\Foundation\Abilities\Support\Projection;

$allowed_keys = [ 'title', 'author', 'created' ];
$defaults     = [ 'title' ];
$include      = Projection::normalize_include( $input['include'] ?? [], $allowed_keys );

$response = [];

if ( Projection::should_include( 'title', $include, $defaults ) ) {
    $response['title'] = $entity->title();
}

if ( Projection::should_include( 'author', $include, $defaults ) ) {
    $response['author'] = $entity->author();
}
```

Unknown keys are discarded. Returned keys preserve the allowed-list order
so response shape stays stable regardless of caller input order. An empty
include array means "use defaults" (`Support/Projection.php:30-71`).

## REST Surface

Two distinct REST namespaces are in play. Knowing which is which is
load-bearing.

### 1. WordPress core `wp-abilities/v1` (NOT registered by Foundation)

WordPress 6.9+ registers this for every ability with
`meta.show_in_rest = true`:

```text
/wp-json/wp-abilities/v1/abilities/{ability-name}/run
```

HTTP method enforcement comes from the annotations:

| Annotation combo | Required method |
| --- | --- |
| `readonly: true` | `GET` |
| `destructive: true` + `idempotent: true` | `DELETE` |
| writes (default) | `POST` |

`POST` `DELETE` body wraps input under `input`:

```json
{ "input": { "id": 42, "limit": 20 } }
```

`GET` passes each input field as its own bracketed query parameter (the
controller does **not** JSON-decode an `input=<json>` query string):

```text
GET /wp-json/wp-abilities/v1/abilities/gk-myplugin/items-list/run?input[limit]=20&input[include][]=meta
```

WordPress core also exposes its own list endpoint at
`/wp-json/wp-abilities/v1/abilities`, which returns a **top-level JSON
array** (no `abilities` wrapper) of every ability with
`show_in_rest = true` across all plugins.

### 2. Foundation `gravitykit/v1` (the GK-only catalog)

`REST.php` registers two routes scoped to GravityKit
abilities only (filtered by `gk_registered_by === 'gravitykit'`):

| Route | Method | Default permission | Filter |
| --- | --- | --- | --- |
| `/wp-json/gravitykit/v1/abilities` | `GET` | `manage_options` | `gk/foundation/abilities/rest/catalog/permission` |
| `/wp-json/gravitykit/v1/abilities/{name}` | `GET` | `manage_options` | `gk/foundation/abilities/rest/catalog/permission` |

### Catalog query parameters

The list endpoint (`/wp-json/gravitykit/v1/abilities`) accepts:

| Param | Type | Default | Notes |
| --- | --- | --- | --- |
| `include_disabled` | bool | `false` | Include abilities currently disabled via Settings. Filtered out by default so AI clients only see what they can invoke. |
| `product` | slug | — | Filter by `gk_product` (e.g. `myplugin`). |
| `scope` | slug | — | Filter by `gk_scope` (derived from category). |
| `category` | slug | — | Filter by category slug. |
| `page` | int ≥ 1 | `1` | Page number. |
| `per_page` | int 1–100 | `50` | Items per page. |

Pagination headers `X-WP-Total` and `X-WP-TotalPages` are emitted on every list response, matching WP core's `/wp-abilities/v1/abilities` shape.

The catalog item shape from `Manager::to_rest_item()`
(`Manager.php:539-563`):

```json
{
  "name": "gk-myplugin/items-list",
  "label": "List Items",
  "description": "Return items visible to the current user.",
  "category": "gk-myplugin-discovery",
  "categories": [
    { "slug": "gk-myplugin-discovery", "label": "MyPlugin Discovery", "description": "..." }
  ],
  "input_schema": { "...": "..." },
  "output_schema": { "...": "..." },
  "annotations": { "readonly": true, "destructive": false, "idempotent": true },
  "enabled": true,
  "gk_product": "myplugin",
  "gk_scope": "discovery",
  "gk_contract_version": "1.0.0",
  "rest_run_url": "https://example.test/wp-json/wp-abilities/v1/abilities/gk-myplugin/items-list/run",
  "mcp_tool_name": "gk_items_list",
  "context": "catalog"
}
```

**The permission filter is restrict-only and enforced.** `can_view_abilities()`
short-circuits to `false` when `current_user_can( 'manage_options' )` returns
false, then runs the filter on the resulting `true`. A filter returning `true`
for an unauthorized user has no effect — only restricting filters narrow
access (`REST.php`).

```php
<?php

add_filter(
    'gk/foundation/abilities/rest/catalog/permission',
    static function ( bool $allowed ): bool {
        return $allowed && is_super_admin();
    }
);
```

**Permission divergence from WP core.** Foundation's catalog at
`/wp-json/gravitykit/v1/abilities` defaults to `manage_options`; WP core's
`/wp-json/wp-abilities/v1/abilities` defaults to `read`. Any authenticated
user can enumerate the same set of GravityKit abilities through core's
endpoint as long as `show_in_rest=true` is set on each ability. If you
need a tighter gate on discovery, also filter WP core's permission via
`rest_pre_dispatch` or the `wp_abilities_*` filter the core controller
exposes — Foundation's filter only governs the `gravitykit/v1` namespace.

## MCP Tool Naming

`Manager::get_mcp_tool_name()` derives an MCP-friendly tool name from
each ability name. The prefix is the **required** `mcp_prefix` in the
product's `gk/foundation/abilities/products` declaration (e.g.
GravityView declares `gv`). A missing prefix fires `_doing_it_wrong()`
and falls back to the full product slug, which stays collision-free by
construction. The whole name converts from kebab-case to snake_case.

| Ability name | Declared prefix | MCP tool name |
| --- | --- | --- |
| `gk-gravityview/views-list` | `gv` | `gv_views_list` |
| `gk-myplugin/items-bulk-delete` | missing (warns) | `myplugin_items_bulk_delete` |

The derived name appears as `mcp_tool_name` in the Foundation catalog
row.

## Settings UI and Enable/Disable

The Foundation Settings page (Settings → GravityKit → Abilities) renders
an Abilities section (`SettingsUI::add_settings_section()`), gated by two
things:

1. The **`gk/foundation/abilities/render-ui`** filter must return `true`.
   It **defaults to `false`**, so the tab stays hidden until a product opts
   in. Abilities are still registered and served over REST regardless.
2. At least one registered ability must be **user-visible** (see below).

Visible abilities are grouped by their primary product category and
rendered as on/off checkboxes, each described by its `user_description`
(falling back to the technical `description`). Saving runs through
`SettingsUI::sync_disabled_abilities()`, which reconciles only the visible
abilities and writes the `gk_abilities_disabled` option (a flat array of
ability names); abilities hidden from the UI keep their existing state.

### User-facing surfaces

Abilities are **AI/MCP-facing by default**. The `description` is the
machine contract (consumed by the REST/Abilities API and MCP), so it stays
technical. Two meta keys curate the human-facing surfaces:

- **`user_visible`** (`->user_visible()`, opt-in — default `false`) — the
  parent toggle. Marks an ability for human surfaces: it then renders in
  the Settings UI and is eligible for the Command Palette. REST/API
  exposure is independent (`show_in_rest`).
- **`user_description`** (`->user_description( '…' )`) — plain-language
  text for the Settings UI (and a Command Palette hint where supported),
  falling back to `description` when unset.

| Surface | Inclusion gate | Body text |
| --- | --- | --- |
| Abilities API / MCP | `show_in_rest` | `description` (technical) |
| Settings UI | `render-ui` filter + `user_visible` | `user_description` ?? `description` |
| Command Palette | `user_visible` + `command_palette` | command `label` |

```php
<?php

// Read.
if ( GravityKitFoundation::abilities()->is_enabled( 'gk-myplugin/items-list' ) ) {
    // Ability is active.
}

// Write.
GravityKitFoundation::abilities()->disable( 'gk-myplugin/item-delete' );
GravityKitFoundation::abilities()->enable( 'gk-myplugin/item-delete' );

// Inspect.
$disabled = GravityKitFoundation::abilities()->disabled();

// Replace the whole list (used by the Settings save flow).
GravityKitFoundation::abilities()->set_disabled(
    [ 'gk-myplugin/item-delete', 'gk-myplugin/cache-flush' ]
);
```

`GravityKitFoundation::abilities()` and `Core::abilities()` both return
the same `Framework` instance (`src/Core.php:47`,
`Framework.php:376-425`). Prefer the static facade.

Disabled abilities are **always registered with WP core** and remain
visible in `wp_get_abilities()` and discovery surfaces. Invocation
short-circuits through the wrapped permission callback with
`ability_disabled` (HTTP 403) (`Manager.php`, `wrap_permission_callback`).
This is the option-backed permission-denial model: state changes never
require re-registration outside the `wp_abilities_api_init` window.

`disable()` intentionally does **not** call `wp_unregister_ability()`,
because `wp_register_ability()` is gated by
`doing_action('wp_abilities_api_init')` (`wp-includes/abilities-api.php`)
and the registry rejects already-registered names
(`WP_Abilities_Registry::register`, `registry.php:92-99`).
`SettingsUI::sync_disabled_abilities()` follows the same model — saving
the Abilities section in Settings produces the same observable state as
calling `Manager::disable()` directly.

The Foundation catalog endpoint omits disabled abilities by default;
pass `?include_disabled=1` to surface them with `enabled: false` so
admin tooling can render an enable/disable toggle.

A `gk/foundation/abilities/changed` action fires on toggle with the
ability name and the new state (`disabled` or `enabled`)
(`Manager.php:750, :788`).

## Command Palette (WP 7.0)

WordPress 7.0 ships `@wordpress/core-abilities`, which auto-fetches
server-registered abilities from `/wp-abilities/v1/` and registers them
into the admin client abilities store. That store powers the admin
Command Palette.

**Foundation does not enqueue a parallel JavaScript bridge.** Core
handles the client side. On WP 6.9 the server
endpoints exist but the client integration does not — `command_palette =
true` is harmless but won't surface anywhere.

### Opting in

```php
<?php

$manager
    ->builder( 'gk-myplugin/items-list' )
    ->label( __( 'List Items', 'gk-myplugin' ) )
    ->description( __( 'List visible items.', 'gk-myplugin' ) )
    ->category( 'gk-myplugin-discovery' )
    ->permission( static function (): bool {
        return current_user_can( 'read' );
    } )
    ->callback( static function (): array {
        return [ 'items' => [] ];
    } )
    ->readonly()
    ->idempotent()
    ->show_in_rest()
    ->command_palette()
    ->register();
```

### Mandatory companions

Do not set `command_palette = true` alone. All of these must also be set:

- `meta.show_in_rest = true` — core discovers REST-exposed abilities.
- `meta.annotations.readonly` (explicit bool)
- `meta.annotations.destructive` (explicit bool)
- `meta.annotations.idempotent` (explicit bool)

**Do not opt destructive abilities in by default.** The palette invokes
abilities in one click; that's the wrong UX for a delete. Leave
`command_palette` unset for any ability annotated `destructive` unless
the surrounding UI flow has explicit confirmation and recovery semantics.

## Hooks Reference

| Hook | Kind | When | Args |
| --- | --- | --- | --- |
| `gk/foundation/abilities/products` | filter | Register your product: MCP tool-name prefix + ability categories to register, keyed by product slug | `array $products` |
| `gk/foundation/abilities/register/before` | action | Preferred registration hook | `Manager $manager` |
| `gk/foundation/abilities/register` | filter | Alternative array-return registration | `array $abilities, Manager $manager` |
| `gk/foundation/abilities/registered` | action | After all abilities registered | `Manager $manager` |
| `gk/foundation/abilities/rate-limit` | filter | Inside every wrapped permission callback, before the disabled gate | `null\|WP_Error $decision, string $name, array $input` |
| `gk/foundation/abilities/changed` | action | After `enable()` `disable()` | `string $name, string $state` |
| `gk/foundation/abilities/rest/catalog/permission` | filter | Restrict catalog endpoint visibility | `bool $allowed` |

## Error Code Reference

| Code | HTTP | Source | Meaning |
| --- | --- | --- | --- |
| `gk_abilities_not_supported` | n/a | `Manager::register()` `register_category()` | WP < 6.9 |
| `gk_abilities_invalid_name` | n/a | `Manager::register()` | Name failed the regex |
| `gk_abilities_missing_field` | n/a | `Manager::register()` | Required config key missing |
| `gk_abilities_invalid_type` | n/a | `Manager::register()` | `label`/`description`/`category` not a string |
| `gk_abilities_invalid_callback` | n/a | `Manager::register()` | Callback not callable |
| `gk_abilities_registration_failed` | n/a | `Manager::register()` | `wp_register_ability()` rejected the config |
| `gk_abilities_option_update_failed` | n/a | `Manager::disable()` `enable()` | `update_option()` failed |
| `ability_disabled` | 403 | wrapped permission callback | Ability is in the disabled list |
| `gk_abilities_rate_limited` | 429 (suggested) | rate-limit filter | Consumer denied the call |
| `gk_batch_mixed_mode` | 400 | consuming plugin's batch handler | Singleton field sent alongside `batch` |
| `gk_batch_validation_failed` | 400 | consuming plugin's batch handler | One or more items failed validation |
| `gk_batch_version_mismatch` | 409 | consuming plugin's batch handler | Stale `version` token |
| `gk_batch_persist_failed` | 500 | consuming plugin's batch handler | Persist step failed after validation |

For consumer-plugin-specific codes, see that plugin's abilities docs.

## Source Map

| Concern | File |
| --- | --- |
| Framework entry point + hooks | `Framework.php` |
| Registration + normalization + wrapping | `Manager.php` |
| Fluent builder | `AbilityBuilder.php` |
| Admin Settings UI | `SettingsUI.php` |
| REST catalog | `REST.php` |
| Batch contract primitives | `Support/BatchContract.php` |
| Dry-run helper for post-column writes | `Support/DryRun.php` |
| Projection / include helper | `Support/Projection.php` |
| Public facade resolution | `src/Core.php` |

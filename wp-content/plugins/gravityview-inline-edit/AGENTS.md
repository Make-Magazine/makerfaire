# AGENTS.md: GravityEdit

> Inline editing of Gravity Forms entry values, on the Gravity Forms entries page and on the GravityView frontend.

## Quick Start

**What this is:** A WordPress plugin that makes entry values editable in place. It wraps rendered field output in markup that the vendored [x-editable](https://vitalets.github.io/x-editable/) library turns into a popup editor, then saves the change over AJAX and hands the browser a payload describing what to re-render. It supports ~25 Gravity Forms field types plus entry meta.

**Main entry point:** `gravityview-inline-edit.php` (plugin header says `GravityEdit`; the file and slug keep the original name)

**Architecture style:** Hook-driven WordPress plugin. No PSR-4 autoloading of its own code. Classes are `require`d and self-register by instantiating themselves at the bottom of their file.

**Key namespace:** The plugin's own classes are **global** and prefixed `GravityView_Inline_Edit_*`. Only the vendored Foundation is namespaced, as `GravityKit\GravityEdit\Foundation` (Strauss prefix from `composer.json` `extra.strauss.namespace_prefix`).

## Repository Map

```text
gravityview-inline-edit.php          Plugin header, constants, Foundation registration, boot
class-gravityview-inline-edit.php    Core singleton: field allow/deny lists, caps, edit mode/style

includes/
  class-gravityview-inline-edit-render-abstract.php   Abstract render base; wraps field output
  class-gravityview-inline-edit-gravity-forms.php     Render impl: GF entries page
  class-gravityview-inline-edit-gravityview.php       Render impl: GravityView frontend
  class-gravityview-inline-edit-ajax.php              Save + upload endpoints (the hot path)
  class-gravityview-inline-edit-scripts.php           Script/style registration, JS settings
  class-gravityview-inline-edit-settings.php          GFAddOn subclass (settings, logging)
  class-gravityview-inline-edit-user-registration.php User Registration Add-On integration
  fields/                                             27 field handlers; see "Extension Patterns"

assets/
  js/                 x-editable field type implementations (fields/*.js + *.min.js)
  css/  sass/         Styles (sass compiles to css)
  fonts/  images/

bower_components/     Vendored x-editable + poshytip (the actual editor UI)
templates/            buttons.php, buttons-edit.php, toggle.php
tests/
  bootstrap.php                 PHPUnit bootstrap, loads GF + this plugin
  GravityEdit_UnitTestCase.php  Base test case + the inline-edit save harness
  unit-tests/                   PHPUnit suite
  E2E/                          Playwright specs, wp-env setup, fixtures
translations/         .pot / .mo
```

`vendor/`, `vendor_prefixed/`, `node_modules/` and `reports/` are build/tooling output, not source.

## Architecture

### Initialization Flow

1. `gravityview-inline-edit.php` requires `vendor_prefixed/gravitykit/foundation/src/preflight_check.php` and bails unless `GravityKit\GravityEdit\Foundation\should_load()` says otherwise.
2. Defines `GRAVITYEDIT_VERSION`, `GRAVITYEDIT_DIR`, `GRAVITYVIEW_INLINE_URL`, `GRAVITYEDIT_FILE`.
3. Loads `vendor/autoload.php` + `vendor_prefixed/autoload.php`, then `Foundation\Core::register( GRAVITYEDIT_FILE )`.
4. On `after_setup_theme`, requires the core class and the GFAddOn, then `GravityView_Inline_Edit::get_instance( GRAVITYEDIT_VERSION, GravityView_Inline_Edit_GFAddon::get_instance() )`.
5. The core singleton globs `includes/fields/class-gravityview-inline-edit-field*.php` (`class-gravityview-inline-edit.php:158`) and requires each. Every field file ends with a bare `new GravityView_Inline_Edit_Field_Foo;`. **That constructor call is the registration.**
6. `GravityView_Inline_Edit_AJAX::get_instance()` hooks `process_inline_edit_callbacks` onto `init` at priority 20 (deliberately after `gp_inventory_type_choices`).

### Core Concepts

- **`GravityView_Inline_Edit`** (`class-gravityview-inline-edit.php`) is the final singleton. Owns `can_edit_entry()`, the supported/ignored field lists, and the edit mode/style.
- **`GravityView_Inline_Edit_Render`** (abstract, `includes/class-gravityview-inline-edit-render-abstract.php`): `wrap_field_value()` is where rendered output gets its `gv-inline-editable-field-*` wrapper and `data-*` attributes. Two concrete subclasses: `_Gravity_Forms` (entries page) and `_GravityView` (frontend).
- **`GravityView_Inline_Edit_Field`** (`includes/fields/class-gravityview-inline-edit-field.php`) is the base for all 27 handlers. Five properties define behavior:

  | Property | Meaning |
  |---|---|
  | `$gv_field_name` | which field type this handles; drives the wrapper-attributes hook name |
  | `$inline_edit_type` | the x-editable type; drives the entry-updated hook name |
  | `$set_value` | whether the base class emits `data-value` |
  | `$standard_live_update` | whether the live-update payload uses the standard shape |
  | `$live_update_json_encode` | whether the payload value is JSON. **False means the raw value is passed through** |

- **`GravityView_Inline_Edit_AJAX`** is the save path. `_edit_gravityview_field()` is a large `switch ( $type )` that builds `$values_to_update`, then `_update_entry()` writes and fires the `entry-updated` filters.

### Data Model

There is none of its own. Everything is a Gravity Forms **entry** (`GFAPI::get_entry` / `update_entry`) against a **form**. No custom tables, no CPTs, no options beyond the GFAddOn settings.

Two kinds of "field" flow through the same save path, and the distinction matters:

- **Form fields**: numeric IDs (`1`, `4.2`). `GFFormsModel::get_field()` returns a `GF_Field`.
- **Entry meta**: `created_by`, `source_url`, `date_created`, entry tags. **No `GF_Field` exists**; `get_field()` legitimately returns null and the save switch writes them with a null field.

### Request Lifecycle (a save)

1. Browser posts to `init` (not `admin-ajax`) with `gv_inline_edit_field` set.
2. `process_inline_edit_callbacks()` → `_edit_gravityview_field()`.
3. Nonce (`gravityview_inline_edit`) → `can_edit_entry()` → load entry → **entry/form agreement check** → load form → `gform_pre_render` → resolve `$gf_field`.
4. `switch ( $type )` builds `$values_to_update`.
5. `validate_field()`, then per-input `GF_Field::get_value_save_input()` (GF 3.0+) or `GFFormsModel::prepare_value()`.
6. `_update_entry()` → `GFAPI::update_entry()` → `gravityview-inline-edit/entry-updated` and `…/entry-updated/{$type}` filters.
7. `wp_send_json( $update_result )`. The payload tells the browser which selectors to update and what to display.

## Conventions

### File & Class Naming

- Files: `class-gravityview-inline-edit-{thing}.php`; field handlers `class-gravityview-inline-edit-field-{type}.php`.
- Classes: `GravityView_Inline_Edit_{Thing}`, global namespace, underscores.
- The glob in step 5 above matches `class-gravityview-inline-edit-field*.php`, so a new field file is picked up by filename alone.
- **Inconsistency worth knowing:** the product is "GravityEdit" but nearly every symbol, the text domain path, and the plugin file still say `gravityview-inline-edit`. The text domain itself is `gk-gravityedit`.

### Hook Naming

Two generations coexist. Both are live; neither is deprecated:

- **Legacy:** `gravityview-inline-edit/{thing}`, the bulk of the surface.
- **Current:** `gk/gravityedit/{scope}/{name}`, used by the newer User Registration work. New hooks should follow this form (`gk/<product>/<scope>/<kebab-case-name>`).

### Version Annotations

`@since {version}` for released, `@since TBD` for unreleased (5 in the tree today). `gktools replace-since` rewrites TBD at release. The changelog in `readme.txt` uses a `= TBD =` heading for the same reason. **Do not hand-replace it with a version.**

### Code Style

No `.phpcs.xml` in the repo. Follow WordPress spacing as written in the existing files. Two rules this codebase actively enforces via tests:

- `rgar( $array, $key )` over `$array['key']` for entry and choice access.
- Strict comparison: `in_array( …, true )`, `===` over `==` for string literals.

## Extension Patterns

### Adding a new field type

1. Create `includes/fields/class-gravityview-inline-edit-field-{type}.php`. The glob picks it up; no registration list to edit.
2. Extend `GravityView_Inline_Edit_Field`, set `$gv_field_name` (the GF field type) and `$inline_edit_type` (the x-editable type).
3. Override `modify_inline_edit_attributes()` to add `data-*` the JS needs. **Call `parent::` at the end**: the base adds `data-type` and optionally `data-value`.
4. Override `updated_result()` to shape the live-update payload.
5. End the file with `new GravityView_Inline_Edit_Field_{Type};`.

The base wires both hooks for you (`includes/fields/class-gravityview-inline-edit-field.php:105`):

```php
add_filter( "gravityview-inline-edit/{$this->gv_field_name}-wrapper-attributes", [ $this, 'modify_inline_edit_attributes' ], 10, 6 );
add_filter( "gravityview-inline-edit/entry-updated/{$this->inline_edit_type}",   [ $this, 'updated_result' ],              10, 4 );
```

`accepted_args` is **6** for the attributes filter even though `apply_filters` passes 9 (`class-gravityview-inline-edit-render-abstract.php:219`). WordPress slices to `accepted_args`, so a 6-parameter signature is correct. This is not a bug.

`updated_result()` contract: return `$update_result` untouched unless it is exactly `true`. `is_bool()` is not enough: `false` is a failed save and must not produce a display payload.

### Adding a JS-side field type

`assets/js/fields/{type}.js` registers an x-editable type. **Production loads `{type}.min.js`** unless `SCRIPT_DEBUG` (`includes/class-gravityview-inline-edit-scripts.php:334`), so rebuild with `node_modules/.bin/grunt uglify:main` after editing. (The release build runs grunt too, so a stale committed min file does not break a release, only git checkouts.)

### Changing what is editable

- `gravityview-inline-edit/supported-fields` (`class-gravityview-inline-edit.php:233`)
- `gravityview-inline-edit/ignored-fields` (`class-gravityview-inline-edit.php:186`)
- `gravityview-inline-edit/user-can-edit-entry` and `…/inline-edit-caps` for permissions.

## Hook Reference

### Filters

| Hook | Purpose |
|---|---|
| `gravityview-inline-edit/wrapper-attributes` | All wrapper `data-*`; 9 args passed |
| `gravityview-inline-edit/{$input_type}-wrapper-attributes` | Per-type variant; what field classes hook |
| `gravityview-inline-edit/entry-updated` | Save result, all types |
| `gravityview-inline-edit/entry-updated/{$type}` | Save result, per type |
| `gravityview-inline-edit/supported-fields` | Allow list |
| `gravityview-inline-edit/ignored-fields` | Deny list |
| `gravityview-inline-edit/user-can-edit-entry` | Per-entry permission |
| `gravityview-inline-edit/inline-edit-caps` | Caps checked (default `gravityforms_edit_entries`) |
| `gravityview-inline-edit/edit-mode` · `…/edit-style` | UI mode/theme |
| `gravityview-inline-edit/js-settings` | The `gv_inline_x` JS object |
| `gravityview-inline-edit/form-buttons` · `…/toggle-labels` | Button/label markup |
| `gravityview-inline-edit/jquery-ui-theme` · `…/poshytip-theme` | Vendored theme choice |
| `gravityview-inline-edit/remove-gf-update-hooks` | Which GF hooks to drop during save |
| `gk/gravityedit/user_registration/{config,entry,preserve_role,restored_user,trigger_update}` | User Registration integration |
| `gk/gravityedit/restore_display_name` | Display-name restore |

### Actions

| Hook | Purpose |
|---|---|
| `gravityview-inline-edit/enqueue-scripts` · `…/enqueue-styles` | Asset hooks |
| `gravityview_clear_entry_cache` | Fired after a save so GravityView drops its cache |

## Development

### Setup

```bash
composer install
npm ci
```

### Testing

**Unit (PHPUnit), local only.** `gkunit test` (Docker; PHP 8.2 / MySQL 8.0). Add `-- --filter <Name>` for one class or test.

> **CI runs this suite via `@gravitykit/phpunit`** (the published name for `gkunit`), in the `run_unit_tests` job, the same harness and invocation as GravitySearch. `prepare` caches its WordPress test deps.
>
> **`build_package_release` deliberately does NOT wait on the test jobs.** It requires only `prepare`, so a release can be built and merged on confidence without blocking on the suites. The test jobs report alongside it; they do not gate it. That is intentional, not an oversight.
>
> `[skip tests]` in the commit subject skips the unit job.

**E2E (Playwright + wp-env), local and CI.** Built on the shared Tooling harness: `@gravitykit/e2e-bootstrap`
supplies the Playwright config, global setup/teardown, and `.wp-env.json` generation (`tests/E2E/setup/*.js`), and
`@gravitykit/e2e-fixtures` supplies the `api` / `fixtures` helpers (`tests/E2E/helpers/*.js`). Both are
devDependencies; CI additionally smoke-tests the packaged zip with `npx @gravitykit/e2e-bootstrap smoke`. Don't
hand-roll Playwright or wp-env wiring here, extend the harness.

Requires a `.env`. CI creates one from `.env.example`:

```bash
cp .env.example .env
# WP_ENV_PLUGINS must point at a Gravity Forms checkout or unzipped build
# GRAVITY_FORMS_LICENSE_KEY / GRAVITYKIT_LICENSE_KEY must be set
npm run tests:e2e:setup     # writes .wp-env.json, starts wp-env
npm run tests:e2e:run
```

Without `.env`, every spec fails with `API baseUrl not configured. Call setConfig() or initFromEnv() first`. That is a missing environment, not a code failure.

**Real local WordPress (Siteminter).** For what neither suite above can reach, mint a disposable site with the
`/gk:siteminter` skill. It symlinks plugin source into a Docker WordPress and pre-activates it, so edits on disk
are live immediately.

Reach for it when a third-party plugin the suites never load is part of the behavior under test. The unit
bootstrap loads WP + Gravity Forms + GravityEdit and nothing else (`tests/bootstrap.php:119-123`), so any
integration with another plugin (Gravity Flow, GravityView, User Registration) is unverifiable there by
construction, and a stub of that plugin's classes only ever tests the stub. Mount the real plugins on a Siteminter
site instead. Pass absolute plugin paths: they are usually siblings in the same `wp-content/plugins` directory.

It replaces neither suite. Pure PHPUnit stays on `gkunit test`; reproducing a committed Playwright spec stays on
the E2E harness above, which builds the fixtures those specs expect.

### Building

`node_modules/.bin/grunt sass uglify imagemin translate` is what `.gktools.json` runs; `npx @gravitykit/gktools build` runs those steps and packages a zip.

## Gotchas

1. **Entry meta has no `GF_Field`.** `GFFormsModel::get_field()` returning null is normal for `created_by`, `source_url`, `date_created` and entry tags. The save switch handles a null field on purpose. A blanket "field not found" rejection silently disables inline editing for all of them. Only a **numeric** field ID promises a form field. (`includes/class-gravityview-inline-edit-ajax.php`)

2. **Choice values are not always strings.** Lookup fields and dynamically populated choices (GP Populate Anything) carry DB IDs, so `$choice['value']` can be an `int`, while posted values are always strings (sanitize map + HTTP). A strict `in_array()` against un-cast choices matches nothing and the "unchecked" branch then clears every checked input. That is silent data loss, and the bug 2.9.2 fixed. Compare both sides as strings; `is_choice_selected()` is the single place that does it.

3. **`gform_pre_render` runs at save time**, not just render, so populated choices exist when the save resolves them. Dropping it breaks dynamically populated fields.

4. **The save endpoint is `init`, not `admin-ajax`**, at priority 20, gated on `$_POST['gv_inline_edit_field']`.

5. **File uploads post as `input_1..input_N` by file index**, not `input_{field_id}`, so `GFFormsModel::get_submission_files()` finds nothing and `GF_Field_FileUpload::validate()` returns early. The handler's own size check is the only enforcement on that path.

6. **`x-editable` ignores the `params` callback's return value.** Returning `false` there does not cancel a submit; the request fires anyway. Cancel from `ajaxOptions.beforeSend` (jQuery honors `false`) or x-editable's `validate`. (`bower_components/x-editable/dist/jquery-editable/js/jquery-editable-poshytip.js:327-345`)

7. **`GFAddOn::log_error()` takes one argument and interpolates nothing** (`gravityforms/includes/addon/class-gf-addon.php:5821`). `log_error( 'x {id}', [ 'id' => $id ] )` logs the literal `{id}` and drops the context. Build the message with `sprintf()`. A test enforces this.

8. **PHPUnit here does not fail on PHP warnings.** A new deprecation sails through green. To gate on one, grep the captured run output.

9. **Testing the AJAX handler:** `wp_send_json()` only routes through `wp_die()` while `wp_doing_ajax()` is true; otherwise it calls a bare `die()` that no filter can intercept and that kills the run mid-suite. `GravityEdit_UnitTestCase::do_inline_edit_save()` handles this, plus the two related traps: PHP notices share the output buffer (so the JSON is decoded from the tail), and WordPress slashes the superglobals via `wp_magic_quotes()` before any handler runs (so the harness must `wp_slash()`).

10. **Several tests scan source text rather than behavior** (`test-code-quality.php`, `test-strict-comparisons.php`, `test-template-escaping.php`). They are cheap but blind: a regex-based scanner passes happily while the violation sits in front of it. If you touch one, **verify it by re-introducing the violation and watching it fail.** That is the only thing that proves it scans. Prefer `token_get_all()` over regex; every scanner here that used a regex had a hole.

## Related Resources

- `CLAUDE.md` → `AGENTS.md` (this file)
- `readme.txt`: user-facing changelog (`= TBD =` until release)
- `.gktools.json`: build/package/release config
- Foundation: bundled, Strauss-prefixed to `GravityKit\GravityEdit\Foundation`
- Product docs: https://www.gravitykit.com/extensions/gravityview-inline-edit/

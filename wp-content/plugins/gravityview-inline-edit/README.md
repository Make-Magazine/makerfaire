# GravityEdit

Edit Gravity Forms entry values inline, without opening the entry. Works on the Gravity Forms entries page and on the GravityView frontend.

This is the developer README. For the user-facing description and changelog, see `readme.txt`. For architecture, hooks, and extension patterns, see [AGENTS.md](AGENTS.md).

## Requirements

- PHP 7.2+
- WordPress with Gravity Forms active
- GravityView (optional; needed for the frontend integration and its E2E specs)
- Docker (for running tests)
- Node 20+ and Composer

## Setup

```bash
composer install
npm ci
```

Composer pulls in GravityKit Foundation, which is Strauss-prefixed into `vendor_prefixed/`.

## Running tests

### Unit tests

Runs in Docker via `gkunit` (PHP 8.2 / MySQL 8.0):

```bash
gkunit test                              # whole suite
gkunit test -- --filter Test_Field_Number  # one class
```

CI runs this suite too, as the `run_unit_tests` job (`npx @gravitykit/phpunit test --parallel`). Note that the release build does not wait on it: `build_package_release` needs only `prepare`, by design, so you can merge on confidence without waiting. Put `[skip tests]` in the commit subject to skip the unit job.

### End-to-end tests

E2E uses Playwright against a `wp-env` site, and needs a `.env` first:

```bash
cp .env.example .env
```

Then edit `.env`:

- `WP_ENV_PLUGINS`: path to a Gravity Forms checkout or unzipped build
- `GRAVITY_FORMS_LICENSE_KEY` and `GRAVITYKIT_LICENSE_KEY`: needed so the lifecycle commands can install licensed plugins

Then:

```bash
npm run tests:e2e:setup   # generates .wp-env.json and starts the site
npm run tests:e2e:run
npm run tests:e2e:debug   # headed, with the Playwright inspector
```

If every spec fails with `API baseUrl not configured`, your `.env` is missing or `WP_ENV_PLUGINS` does not point at Gravity Forms.

Some specs need GravityView active on the test site. If the GravityView frontend specs fail while the rest pass, check it:

```bash
npm run wp-env:cli -- wp plugin list
```

Stop the site with `npm run wp-env:stop`.

## Building

```bash
node_modules/.bin/grunt sass uglify imagemin translate
```

This compiles Sass, minifies JS, optimizes images, and regenerates translations. These are the same steps `.gktools.json` runs at build time.

If you edit anything in `assets/js/`, run at least `node_modules/.bin/grunt uglify:main`: production loads the `.min.js` files unless `SCRIPT_DEBUG` is on.

To produce a release zip:

```bash
npx @gravitykit/gktools build
```

## Layout

The short version; [AGENTS.md](AGENTS.md) has the annotated map.

- `gravityview-inline-edit.php`: plugin header and boot
- `class-gravityview-inline-edit.php`: core singleton
- `includes/`: render, AJAX, scripts, settings, integrations
- `includes/fields/`: one handler per supported field type
- `assets/`: JS, Sass/CSS, images, fonts
- `bower_components/`: vendored x-editable and poshytip
- `templates/`: button and toggle markup
- `tests/`: PHPUnit (`unit-tests/`) and Playwright (`E2E/`)

## Contributing

- Field handlers are picked up by filename. Drop a new `includes/fields/class-gravityview-inline-edit-field-{type}.php` in place and it registers itself. See "Extension Patterns" in [AGENTS.md](AGENTS.md).
- Use `@since TBD` for unreleased changes; the release tooling rewrites it.
- Add changelog entries under the `= TBD =` heading in `readme.txt`.

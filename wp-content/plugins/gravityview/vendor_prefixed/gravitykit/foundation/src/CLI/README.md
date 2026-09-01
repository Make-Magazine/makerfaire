# GravityKit Foundation CLI Commands

Foundation ships with a number of WP-CLI commands to manage licenses, products, and background jobs. It also provides a hook to simplify custom command registration.

## Table of Contents

- [General Commands](#general-commands)
- [License Management Commands](#license-management-commands)
  - [wp gk licenses list](#wp-gk-licenses-list)
  - [wp gk licenses check](#wp-gk-licenses-check-license-key)
  - [wp gk licenses activate](#wp-gk-licenses-activate-license-key---urlsite-url)
  - [wp gk licenses deactivate](#wp-gk-licenses-deactivate-license-key---urlsite-url)
- [Product Management Commands](#product-management-commands)
  - [wp gk products list](#wp-gk-products-list)
  - [wp gk products install](#wp-gk-products-install-product-text-domain)
  - [wp gk products update](#wp-gk-products-update-product-text-domain)
  - [wp gk products activate](#wp-gk-products-activate-product-text-domain)
  - [wp gk products deactivate](#wp-gk-products-deactivate-product-text-domain)
  - [wp gk products delete](#wp-gk-products-delete-product-text-domain)
  - [wp gk products search](#wp-gk-products-search-search-term)
- [Background Jobs Commands](#background-jobs-commands)
  - [Multisite](#multisite)
  - [wp gk jobs list](#wp-gk-jobs-list)
  - [wp gk jobs get](#wp-gk-jobs-get-id)
  - [wp gk jobs health](#wp-gk-jobs-health)
  - [wp gk jobs cancel](#wp-gk-jobs-cancel-id)
  - [wp gk jobs pause](#wp-gk-jobs-pause-id)
  - [wp gk jobs unpause](#wp-gk-jobs-unpause-id)
  - [wp gk jobs retry](#wp-gk-jobs-retry-id)
  - [wp gk jobs delete](#wp-gk-jobs-delete-id)
  - [wp gk jobs run](#wp-gk-jobs-run-id)
- [For Developers: Adding Custom Commands](#for-developers-adding-custom-wp-gk-commands)

## General Commands

### `wp gk version`

Displays the currently installed Foundation version.

**Usage:**

```bash
wp gk version
```

## License Management Commands

Base command: `wp gk licenses`

These commands allow you to manage product licenses.

### `wp gk licenses list`

Lists all activated licenses on the site.

**Usage:**

```bash
wp gk licenses list
```

**Options:**

*   `[--skip-cache]`
    *   Fetches licenses directly from the server instead of the local cache.
    *   Default: `false`.
*   `[--format=<format>]`
    *   Determines the output format.
    *   Accepted values: `table`, `json`.
    *   Default: `table`.

**Example:**

```bash
wp gk licenses list --skip-cache --format=json
```

### `wp gk licenses check <license-key>[,...]`

Checks the status of one or more license keys with the remote server.

**Usage:**

```bash
wp gk licenses check <license-key1>[,<license-key2>,...]
```

**Arguments:**

*   `<license-key>[,...]`
    *   A single license key or a comma-separated list of license keys to check.

**Options:**

*   `[--format=<format>]`
    *   Determines the output format.
    *   Accepted values: `table`, `json`.
    *   Default: `table`.

**Example:**

```bash
wp gk licenses check YOUR_LICENSE_KEY_HERE
wp gk licenses check KEY1,KEY2 --format=json
```

### `wp gk licenses activate <license-key>[,...] --url=<site-url>`

Activates one or more license keys for the specified site URL.

**Usage:**

```bash
wp gk licenses activate <license-key1>[,<license-key2>,...] --url=<site-url>
```

**Arguments:**

*   `<license-key>[,...]`
    *   A single license key or a comma-separated list of license keys to activate.
*   `--url=<site-url>`
    *   **Required.** The site URL for which to activate the license(s).

**Example:**

```bash
wp gk licenses activate YOUR_LICENSE_KEY_HERE --url=https://example.com
wp gk licenses activate KEY1,KEY2 --url=https://example.com
```

### `wp gk licenses deactivate <license-key>[,...] --url=<site-url>`

Deactivates one or more license keys for the specified site URL.

**Usage:**

```bash
wp gk licenses deactivate <license-key1>[,<license-key2>,...] --url=<site-url>
```

**Arguments:**

*   `<license-key>[,...]`
    *   A single license key or a comma-separated list of license keys to deactivate.
*   `--url=<site-url>`
    *   **Required.** The site URL for which to deactivate the license(s).

**Example:**

```bash
wp gk licenses deactivate YOUR_LICENSE_KEY_HERE --url=https://example.com
wp gk licenses deactivate KEY1,KEY2 --url=https://example.com
```

## Product Management Commands

Base command: `wp gk products`

These commands help manage GravityKit products (plugins and add-ons).

### `wp gk products list`

Lists products. By default, only licensed products are displayed.

**Usage:**

```bash
wp gk products list
```

**Options:**

*   `[--only-installed]`
    *   Display only installed products.
    *   Default: `false`.
*   `[--include-hidden]`
    *   Include "hidden" products (internal tracking).
    *   Default: `false`.
*   `[--exclude-unlicensed]`
    *   Exclude unlicensed products from the list.
    *   Default: `false`.
*   `[--skip-cache]`
    *   Fetches the product list from the server instead of local cache.
    *   Default: `false`.
*   `[--format=<format>]`
    *   Determines the output format.
    *   Accepted values: `table`, `json`.
    *   Default: `table`.

**Examples:**

```bash
wp gk products list
wp gk products list --only-installed --format=json
wp gk products list --include-hidden --exclude-unlicensed
```

### `wp gk products install [<product-text-domain>,...]`

Installs one or more products.

**Usage:**

```bash
wp gk products install <product-text-domain1>[,<product-text-domain2>,...]
wp gk products install --all
```

**Arguments:**

*   `[<product-text-domain>,...]`
    *   A single product text domain or a comma-separated list of product text domains to install.

**Options:**

*   `[--all]`
    *   Install all available licensed products.
*   `[--activate]`
    *   Activate the product(s) after installation.
    *   Default: `false`.
*   `[--skip-dependency-check]`
    *   Do not verify if the product has unmet system or plugin dependencies.
    *   Default: `false`.

**Examples:**

```bash
wp gk products install gk-gravityview
wp gk products install gk-gravityview,gk-gravitycharts --activate
wp gk products install gk-gravityimport --skip-dependency-check
wp gk products install --all --activate
```

### `wp gk products update [<product-text-domain>,...]`

Updates one or more installed products.

**Usage:**

```bash
wp gk products update <product-text-domain1>[,<product-text-domain2>,...]
wp gk products update --all
```

**Arguments:**

*   `[<product-text-domain>,...]`
    *   A single product text domain or a comma-separated list of product text domains to update.

**Options:**

*   `[--all]`
    *   Update all installed products that have an available update.
*   `[--skip-dependency-check]`
    *   Do not verify if the product update has unmet system or plugin dependencies.
    *   Default: `false`.
*   `[--skip-git-check]`
    *   Do not check if the product is installed from a Git repository and overwrite the installation folder.
    *   Default: `false`.

**Examples:**

```bash
wp gk products update gk-gravityview
wp gk products update gk-gravityview,gk-gravitymaps --skip-dependency-check
wp gk products update --all
```

### `wp gk products activate [<product-text-domain>,...]`

Activates one or more installed products.

**Usage:**

```bash
wp gk products activate <product-text-domain1>[,<product-text-domain2>,...]
wp gk products activate --all
```

**Arguments:**

*   `[<product-text-domain>,...]`
    *   A single product text domain or a comma-separated list of product text domains to activate.

**Options:**

*   `[--all]`
    *   Activate all inactive installed products.
*   `[--skip-dependency-check]`
    *   Do not verify if the product has unmet system or plugin dependencies before activation.
    *   Default: `false`.

**Examples:**

```bash
wp gk products activate gk-gravityview
wp gk products activate gk-gravityview,gk-gravityexport --skip-dependency-check
wp gk products activate --all
```

### `wp gk products deactivate [<product-text-domain>,...]`

Deactivates one or more active products.

**Usage:**

```bash
wp gk products deactivate <product-text-domain1>[,<product-text-domain2>,...]
wp gk products deactivate --all
```

**Arguments:**

*   `[<product-text-domain>,...]`
    *   A single product text domain or a comma-separated list of product text domains to deactivate.

**Options:**

*   `[--all]`
    *   Deactivate all active products.
*   `[--skip-dependency-check]`
    *   Do not verify if this product is required by other active products.
    *   Default: `false`.

**Examples:**

```bash
wp gk products deactivate gk-gravityview
wp gk products deactivate gk-gravityview,gk-gravitycalendar --skip-dependency-check
wp gk products deactivate --all
```

### `wp gk products delete [<product-text-domain>,...]`

Deletes one or more installed products.

**Usage:**

```bash
wp gk products delete <product-text-domain1>[,<product-text-domain2>,...]
wp gk products delete --all
```

**Arguments:**

*   `[<product-text-domain>,...]`
    *   A single product text domain or a comma-separated list of product text domains to delete.

**Options:**

*   `[--all]`
    *   Delete all installed products.
*   `[--deactivate-before-deletion]`
    *   Deactivate the product before deleting it. If a product is active and this flag is not set, deletion may be prevented.
    *   Default: `false`.

**Examples:**

```bash
wp gk products delete gk-gravityview
wp gk products delete gk-gravityview,gk-gravityimport --deactivate-before-deletion
wp gk products delete --all --deactivate-before-deletion
```

### `wp gk products search <search-term>`

Searches for products based on a search term (matches against name, text domain, and excerpt).

**Usage:**

```bash
wp gk products search <search-term>
```

**Arguments:**

*   `<search-term>`
    *   The term to search for.

**Options:**

*   `[--include-hidden]`
    *   Include "hidden" products in the search.
    *   Default: `false`.
*   `[--skip-cache]`
    *   Fetches the product list from the server before searching.
    *   Default: `false`.
*   `[--format=<format>]`
    *   Determines the output format for the search results.
    *   Accepted values: `table`, `json`.
    *   Default: `table`.

**Example:**

```bash
wp gk products search "import"
wp gk products search gravity --format=json --include-hidden
```

## Background Jobs Commands

Base command: `wp gk jobs`

These commands allow you to manage background jobs (scheduled tasks powered by Action Scheduler).

### Multisite

On WordPress multisite, all `wp gk jobs` subcommands operate on a single site. Use WP-CLI's `--url` flag to target a specific site:

```bash
wp gk jobs list --url=https://example.com/site1/
wp gk jobs get 42 --url=https://example.com/blog/
wp gk jobs health --url=https://example.com/store/
```

Without `--url`, commands default to the main site.

### `wp gk jobs list`

Lists background jobs.

**Usage:**

```bash
wp gk jobs list
```

**Options:**

*   `[--status=<status>]`
    *   Filter by status.
    *   Accepted values: `pending`, `scheduled`, `in-progress`, `complete`, `failed`, `canceled`, `paused`.
*   `[--hook=<hook>]`
    *   Filter by hook name or prefix.
*   `[--per-page=<n>]`
    *   Number of jobs to display.
    *   Default: `20`.
*   `[--format=<format>]`
    *   Determines the output format.
    *   Accepted values: `table`, `json`, `csv`, `count`.
    *   Default: `table`.

**Examples:**

```bash
wp gk jobs list
wp gk jobs list --status=failed
wp gk jobs list --hook=gk/gravityexport
wp gk jobs list --format=json
wp gk jobs list --status=failed --format=count
```

### `wp gk jobs get <id>`

Gets details for a single job, including its tasks and event history.

**Usage:**

```bash
wp gk jobs get <id>
```

**Arguments:**

*   `<id>`
    *   The job ID.

**Options:**

*   `[--format=<format>]`
    *   Determines the output format.
    *   Accepted values: `table`, `json`.
    *   Default: `table`.

**Examples:**

```bash
wp gk jobs get 42
wp gk jobs get 42 --format=json
```

### `wp gk jobs health`

Shows scheduler health and diagnostics, including loopback connectivity and WP-Cron status.

**Usage:**

```bash
wp gk jobs health
```

**Options:**

*   `[--format=<format>]`
    *   Determines the output format.
    *   Accepted values: `table`, `json`.
    *   Default: `table`.

**Examples:**

```bash
wp gk jobs health
wp gk jobs health --format=json
```

> **Note:** The loopback probe is performed from the CLI context and may not reflect web-request behavior.

### `wp gk jobs cancel <id>`

Cancels a running or pending job.

**Usage:**

```bash
wp gk jobs cancel <id>
```

**Arguments:**

*   `<id>`
    *   The job ID to cancel.

**Options:**

*   `[--yes]`
    *   Skip confirmation prompt.

**Examples:**

```bash
wp gk jobs cancel 42
wp gk jobs cancel 42 --yes
```

### `wp gk jobs pause <id>`

Pauses a running or pending job.

**Usage:**

```bash
wp gk jobs pause <id>
```

**Arguments:**

*   `<id>`
    *   The job ID to pause.

**Options:**

*   `[--yes]`
    *   Skip confirmation prompt.

**Examples:**

```bash
wp gk jobs pause 42
wp gk jobs pause 42 --yes
```

### `wp gk jobs unpause <id>`

Unpauses a paused job, allowing it to resume processing.

**Usage:**

```bash
wp gk jobs unpause <id>
```

**Arguments:**

*   `<id>`
    *   The job ID to unpause.

**Example:**

```bash
wp gk jobs unpause 42
```

### `wp gk jobs retry <id>`

Retries a failed job from the point of failure. Resets failed and skipped tasks to pending, clears retry metadata, and restarts the task chain.

**Usage:**

```bash
wp gk jobs retry <id>
```

**Arguments:**

*   `<id>`
    *   The job ID to retry.

**Example:**

```bash
wp gk jobs retry 42
```

### `wp gk jobs delete [<id>]`

Deletes one or more jobs. Supports both single deletion by ID and bulk deletion by status.

**Usage:**

```bash
wp gk jobs delete <id>
wp gk jobs delete --status=<status> --all
```

**Arguments:**

*   `[<id>]`
    *   The job ID to delete.

**Options:**

*   `[--status=<status>]`
    *   When combined with `--all`, delete all jobs matching this status.
*   `[--all]`
    *   Delete all matching jobs. Requires `--status`.
*   `[--yes]`
    *   Skip confirmation prompt.

**Examples:**

```bash
wp gk jobs delete 42
wp gk jobs delete --status=failed --all --yes
wp gk jobs delete --status=complete --all --yes
```

### `wp gk jobs run <id>`

Runs a job synchronously or dispatches it for asynchronous processing. By default, runs inline in the current process with per-task progress output.

**Usage:**

```bash
wp gk jobs run <id>
```

**Arguments:**

*   `<id>`
    *   The job ID to run.

**Options:**

*   `[--reschedule]`
    *   For recurring jobs, reset the next occurrence relative to now.
*   `[--async]`
    *   Dispatch for background processing instead of running synchronously.
*   `[--timeout=<seconds>]`
    *   Maximum seconds for sync execution.
    *   Default: `300`.

**Examples:**

```bash
wp gk jobs run 42
wp gk jobs run 42 --reschedule
wp gk jobs run 42 --async
wp gk jobs run 42 --timeout=60
```

## For Developers: Adding Custom `wp gk` Commands

The recommended and most flexible method to add your own commands under the `wp gk` prefix is by using the `gk/foundation/cli/commands` WordPress filter. This allows you to integrate commands from your own plugins or custom code seamlessly, without needing to modify Foundation's internal files.

**How the Filter Works**

The `gk/foundation/cli/commands` filter passes an associative array. To add your command, you update the object with:

* **Key:** The desired command slug (e.g., `my-tool`). This will be appended to `wp gk`, forming `wp gk my-tool`.
* **Value:** The fully qualified class name of your command handler (e.g., `\MyPlugin\CLI\MyToolCommand::class`).

Any public methods in your command handler class (that don't start with an underscore) will become subcommands. For instance, a public method `sample_action()` in `MyToolCommand` registered under `my-tool` would be callable as `wp gk my-tool sample-action`.

**Steps to Add a Custom Command:**

### Step 1: Create Your Command Class

Define a class that will contain the logic for your CLI command(s). This class should extend `WP_CLI_Command`. While you *could* extend `GravityKit\Foundation\CLI\AbstractCommand` for consistency with Foundation's internal commands, extending the standard `WP_CLI_Command` is sufficient and often simpler for external integrations.

*Example: `MyGkToolCommand.php` (e.g., located in `your-plugin/src/CLI/MyGkToolCommand.php`)*
```php
<?php

namespace GK\MyPlugin\CLI;

use WP_CLI_Command;
use WP_CLI;

/**
 * A custom tool to be integrated with `wp gk`.
 */
class MyGkToolCommand extends WP_CLI_Command {

    /**
     * Executes a sample action for the custom tool.
     *
     * This command demonstrates how a simple action can be added.
     *
     * ## EXAMPLES
     *
     *     wp gk my-tool sample-action
     *
     * @subcommand sample-action
     */
    public function sample_action( $args, $assoc_args ) {
        WP_CLI::success( "The custom command 'wp gk my-tool sample-action' was executed successfully!" );
    }
}
```

### Step 2: Register Your Command via the Filter

In your plugin or theme, hook into the `gk/foundation/cli/commands` filter to register your command class. This is typically done in a main plugin class or a dedicated setup class during its initialization.

Let's say your plugin has a main class called `Foo`:

*Example: Registering the command from `Foo.php` (e.g., in `your-plugin/includes/Foo.php` or `your-plugin/Foo.php`)*
```php
<?php
namespace MyPlugin;

// Ensure your command class is autoloaded (e.g., via Composer)
// or include it manually if not: 
// require_once __DIR__ . '/../src/CLI/MyGkToolCommand.php'; 

class Foo {

    public function __construct() {
        // Other initialization logic for your plugin...
        $this->initialize_cli_integrations();
    }

    private function initialize_cli_integrations() {
        // Check if WP-CLI is running and Foundation's CLI class is available
        if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'GravityKit\GravityView\Foundation\CLI\CLI' ) ) {
            add_filter( 'gk/foundation/cli/commands', [ $this, 'add_my_gk_tool_command' ] );
        }
    }

    /**
     * Adds the custom GK tool command to the list of Foundation CLI commands.
     *
     * @param array $commands An associative array of [command_slug => ClassName::class].
     * @return array The modified array of commands.
     */
    public function add_my_gk_tool_command( array $commands ): array {
        // Register MyGkToolCommand under the slug 'my-tool'
        // This makes `wp gk my-tool` available, with subcommands like `sample-action`.
        $commands['my-tool'] = \MyPlugin\CLI\MyGkToolCommand::class;
        return $commands;
    }

    // ... other methods for the Foo class ...
}

// Example of how Foo might be instantiated in your plugin:
// if ( class_exists( 'MyPlugin\Foo' ) ) {
//     new \MyPlugin\Foo();
// }
```

With these two files in place (and assuming your `MyPlugin\Foo` class is instantiated and `MyGkToolCommand.php` is autoloaded or included), the command `wp gk my-tool sample-action` will become available.

Refer to the official [WP-CLI Commands Cookbook](https://make.wordpress.org/cli/handbook/guides/commands-cookbook/) for comprehensive details on structuring command methods, defining arguments, options, and utilizing various WP-CLI utilities within your command class.
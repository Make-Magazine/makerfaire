# Scheduler

Background job processing for GravityKit products, built on [Action Scheduler](https://actionscheduler.org/).

A **job** is a named container of **tasks**. Each task has a PHP callback. When you schedule a job, the job scheduler executes its tasks one at a time as separate Action Scheduler actions. Tasks can checkpoint their progress and resume in a new execution, share data across the job, declare dependencies on other tasks, and recover automatically from crashes.

## Terminology

These terms appear throughout the API. Getting them straight now saves confusion later. Throughout this document, **"the job scheduler"** refers to Foundation's scheduling layer (the `JobScheduler` class and its handlers). **"Action Scheduler"** or **"AS"** refers to the underlying WooCommerce library.

- **Action** — Action Scheduler's only primitive. A hook name plus serialized args, stored in the database for deferred execution. Essentially a delayed `do_action()` call. AS schedules it, a queue runner fires it, done.

- **Job** — Foundation's top-level unit of work. A named container of one or more tasks. This is what you schedule, pause, cancel, and monitor. Under the hood, a job is stored as an AS action whose args hold the full task list. When AS completes this action, the job scheduler marks it RUNNING and begins executing tasks as separate AS actions. Jobs persist in the database and survive restarts, deploys, and PHP process recycling.

- **Task** — One step within a job. Has a PHP callback, its own args, and optional dependencies on other tasks. Each task executes as its own AS action, so AS handles time limits and failure detection per task.

- **Instance** — A specific execution of a job. A recurring job creates a new instance each recurrence. `JobInstance` is the model returned when querying history or managing running jobs.

- **Checkpoint** — A task saving its progress and requesting re-execution with updated args. This is the normal success path for long-running work: process a batch, checkpoint, resume in a fresh PHP execution. Not an error — no retry counter is incremented. For example, an import task processes 100 rows, then checkpoints with `offset: 100` so the next execution starts at row 101. Short tasks that complete in a single execution simply return `null`. See [Cooperative Time Budgeting](#cooperative-time-budgeting) for the checkpoint API.

- **Dispatch** — An HTTP loopback request that nudges AS's queue runner to process pending actions immediately, rather than waiting for the next cron trigger. `run()` dispatches automatically; `async()` does not.

- **NextRunRules** — The return type from a task callback. Tells the job scheduler what to do next: rerun with updated args (checkpoint), update shared job data, or both. Return `null` when done.

## Job Naming Convention

Job names become Action Scheduler hook names. They appear in the database, CLI output, and the Background Jobs UI.

**Format:** `{plugin}_{job_type}` or `{plugin}_{job_type}_{id}` for per-entity jobs.

| Segment | Description | Example |
|---|---|---|
| `{plugin}` | Plugin slug, no hyphens | `gravityexport`, `gravityimport` |
| `{job_type}` | What the job does — concise verb/noun | `export`, `import`, `cleanup` |
| `_{id}` | Optional — only when jobs are per-entity (per feed, per form, etc.) | `_42` |

**Examples:**

```
gravityexport_export_42      # per-feed scheduled export
gravityimport_import         # single import batch job
```

**Rules:**

- **Underscores only.** `humanize_hook()` in the Background Jobs UI converts underscores to spaces for display. Slashes render as gibberish.
- **Keep it short.** The name appears in CLI output, DB queries, and the Background Jobs UI.
- **Use `set_label()` for human-friendly names** (e.g., "GravityExport: Monthly Report"). The hook name is a machine identifier; the label is what users see.
- **Don't include "job" or "schedule"** — that's implied by context.
- **Don't prefix with `gk_`** — Foundation's scheduler already groups all actions under the `gk_scheduler` AS group.
- **Per-entity jobs append the entity ID** (e.g., `_42`). This creates unique AS hooks per entity so they can be scheduled and cancelled independently.

## How Action Scheduler Works

[Action Scheduler](https://actionscheduler.org/) is a database-backed job queue for WordPress. Like WP-Cron, it piggybacks on page visits for its initial trigger — but where WP-Cron stores events in the options table and offers no execution guarantees, AS uses its own database table, processes actions through a queue runner with batching and time budgets, and provides retries, logging, and failure detection out of the box.

**Action lifecycle.** An action starts as `pending`. When the queue runner claims it, it moves to `in-progress`. After the hook fires, AS marks it `complete` (callback returned normally) or `failed` (callback threw an uncaught exception, after retrying up to 5 times by default).

**Queue runner.** By default, WP-Cron triggers the first batch — on a page visit, AS claims a set of pending actions and executes their hooks one at a time. Between actions, it checks a time budget so the batch doesn't exceed PHP's execution limit — but within a single callback, there's no enforcement or helper to check remaining time. If more actions remain after the batch completes, AS fires its own loopback request (an HTTP request the server makes to itself) to start the next batch immediately, without waiting for another WP-Cron trigger. This loopback chain continues until the queue is drained. On hosts where loopback is unavailable, the fallbacks are `ALTERNATE_WP_CRON` (which runs cron inline on page loads — functional but slower) or a system cron job (`wp cron event run --due-now`). WordPress's built-in `spawn_cron()` also relies on loopback, so it cannot help when loopback is blocked.

**What AS provides out of the box:** one-off, recurring, and cron-expression scheduling. Automatic retry on failure. Per-batch time and memory budgets. Logging. A WP Admin status page.

**What it doesn't provide:** each action is standalone — once it runs, AS is done with it. There's no built-in way to chain actions, pass results between them, or track whether a group of related actions completed.

## Why Foundation Wraps It

AS gives you a reliable way to run a single deferred callback. Building a real multi-step workflow on raw AS means solving the same problems every time:

- **Multi-step orchestration.** You manually schedule the next action from inside each callback, reinventing chaining logic, error propagation, and completion detection every time:

  ```php
  // Raw AS: you wire every step yourself.
  add_action( 'my_fetch', function ( $args ) {
      $rows = fetch_rows( $args['offset'] );
      // Done fetching? Schedule the next step manually.
      if ( empty( $rows ) ) {
          as_enqueue_async_action( 'my_transform', [ 'file' => $args['file'] ] );
          return;
      }
      // More rows? Schedule yourself again.
      as_enqueue_async_action( 'my_fetch', [ 'offset' => $args['offset'] + 100, 'file' => $args['file'] ] );
  } );

  add_action( 'my_transform', function ( $args ) { /* ... schedule my_notify when done ... */ } );
  add_action( 'my_notify', function ( $args ) { /* ... how do you know if fetch or transform failed? ... */ } );
  ```

  Foundation models a job as a container of tasks with declared dependencies. The job scheduler chains them automatically, propagates failures, and detects completion — you define what each task does. See [Quick Start](#quick-start).

- **Progress tracking.** An AS action is opaque — pending, running, or done — with no way to ask "which step is the job on?" Foundation tracks per-task status (pending, running, completed, failed, skipped) and exposes it through `JobInstance::progress()`. See [Job History and Progress](#job-history-and-progress).

- **Cooperative time budgeting.** AS budgets time across its batch of actions, but within a single callback there's no deadline or helper. Foundation injects a wall-clock deadline into every task and provides `GravityKitFoundation::scheduler()->should_continue()` so callbacks can checkpoint before time runs out. See [Cooperative Time Budgeting](#cooperative-time-budgeting).

- **Pause, resume, cancel.** AS lets you unschedule a pending action, but has no concept of pausing a multi-step workflow mid-flight. Canceling mid-execution is also tricky — unscheduling a recurring action during its own callback doesn't prevent AS from rescheduling it ([AS #1304](https://github.com/woocommerce/action-scheduler/issues/1304)). Foundation manages lifecycle through status flags that the task executor checks between steps, halting or resuming the chain cleanly. See [Job Management](#job-management).

- **Inter-step data passing.** Each AS action gets the args it was originally scheduled with. If step 1 produces a file path that step 2 needs, you write it to an option or transient yourself. Foundation gives every task access to shared job data via `set_data()`, and tasks can update it mid-execution with `GravityKitFoundation::scheduler()->checkpoint_with_data()` — no external storage required. See [Inter-Task Data Sharing](#inter-task-data-sharing).

Code focuses on the work; the job scheduler handles the plumbing.

## Table of Contents

- [Terminology](#terminology)
- [Job Naming Convention](#job-naming-convention)
- [How Action Scheduler Works](#how-action-scheduler-works)
- [Why Foundation Wraps It](#why-foundation-wraps-it)
- [Quick Start](#quick-start)
- [Pre-Scheduling Health Check](#pre-scheduling-health-check)
- [How Jobs Execute](#how-jobs-execute)
- [The Builder API](#the-builder-api)
- [Scheduling Methods](#scheduling-methods)
- [Working with Tasks](#working-with-tasks)
- [Cooperative Time Budgeting](#cooperative-time-budgeting)
- [Job Management](#job-management)
- [Job History and Progress](#job-history-and-progress)
- [JobResult: Handling Scheduling Outcomes](#jobresult-handling-scheduling-outcomes)
- [Hooks](#hooks)
- [WP-CLI Commands](#wp-cli-commands)
- [Multisite](#multisite)
- [Under the Hood](#under-the-hood)
  - [Diagnostics and Site Health](#diagnostics-and-site-health)
  - [Table Recovery](#table-recovery)
- [Debugging](#debugging)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)

## Quick Start

```php
use GravityKit\Foundation\Core as GravityKitFoundation;

// 1. Create a job.
$job = GravityKitFoundation::scheduler()->job()->create( 'csv_export' );

// 2. Add a human-readable label and product owner (for execution health notices).
$job->set_label( 'CSV Export' )
    ->set_product( 'gk-gravityexport' );

// 3. Attach shared data that every task can read.
$job->set_data( 'form_id', 42 );

// 4. Add tasks.
$job->task()
    ->create( 'fetch', [ Exporter::class, 'fetch' ], [ 'offset' => 0 ] )
    ->set_label( 'Fetch Entries' )
    ->queue();

$job->task()
    ->create( 'write', [ Exporter::class, 'write' ], [], [ 'fetch' ] )
    ->set_label( 'Write CSV File' )
    ->queue();

// 5. Schedule and run immediately.
$result = $job->run();

if ( ! $result->succeeded() ) {
    // Scheduling failed (Action Scheduler returned 0).
}

if ( $result->has_warning() ) {
    // Job was scheduled, but execution environment has issues (e.g., loopback blocked).
    error_log( $result->warning() );
}
```

**Task callback signature:**

```php
use GravityKit\Foundation\Scheduler\Models\NextRunRules;

/**
 * @param array      $args     Task-specific arguments.
 * @param array|null $job_data Shared job-level data.
 *
 * @return NextRunRules|null Return null when done. Return NextRunRules to checkpoint/rerun.
 */
public static function fetch( array $args = [], $job_data = null ): ?NextRunRules {
    $form_id = $job_data['form_id'];
    $offset  = $args['offset'] ?? 0;

    // Process a batch...

    if ( $has_more ) {
        return GravityKitFoundation::scheduler()->checkpoint( [ 'offset' => $offset + $batch_size ] );
    }

    return null; // Done.
}
```

## Pre-Scheduling Health Check

Before scheduling, check whether the scheduler can dispatch jobs asynchronously:

```php
$scheduler = GravityKitFoundation::scheduler();

if ( $scheduler->can_dispatch() ) {
    // Loopback works — jobs will execute in the background.
    $result = $job->run();
} else {
    // Loopback blocked — show a warning or fall back to synchronous processing.
    $health = $scheduler->health();

    if ( $health->has_failure() ) {
        // No execution path at all (loopback blocked + WP-Cron disabled/broken).
        $code = $health->failure_code(); // HealthCheck::LOOPBACK_AND_CRON_DISABLED or LOOPBACK_AND_CRON_SPAWN
    } else {
        // ALTERNATE_WP_CRON is active — jobs will run inline (slow but functional).
        // can_dispatch() returns false because this is not true background execution.
    }
}
```

**`can_dispatch(bool $fresh = false)`** — Returns `true` only when the scheduler is enabled AND loopback dispatch works. `ALTERNATE_WP_CRON` (inline execution) returns `false`. Pass `$fresh = true` to bypass the cached probe and re-test loopback connectivity.

**`health(bool $fresh = false)`** — Returns a `HealthCheck` object with granular diagnostics: `has_failure()`, `failure_code()`, `message()`, `is_loopback_blocked()`, and `to_array()` for frontend serialization.

**Note:** `can_dispatch()` is a pre-scheduling convenience. You can also schedule first and check `$result->has_warning()` after — the `JobResult` carries the same health information. Use `can_dispatch()` when you need to decide the UI or code path _before_ creating a job.

## How Jobs Execute

Understanding this flow makes the rest of the API click.

**1. Scheduling.** When you call `$job->run()`, a single Action Scheduler action is created. All tasks are serialized into that action's args.

**2. Job start.** When AS fires the action, the job scheduler marks it RUNNING, finds the first eligible task, and schedules it as a separate AS async action. Only one task action exists at a time per job.

**3. Task execution.** The task executor loads the job from the database, runs the task callback, and updates progress. If more tasks remain, it schedules the next one. If none remain, it marks the job complete.

**4. Checkpointing.** When a task returns `NextRunRules` with `rerun(true)`, the same task is rescheduled with updated args. This is how long-running work gets split across multiple PHP executions.

**5. Serial scheduling.** Task N completes, then task N+1 is scheduled. Tasks never run concurrently within a job. Dependencies are checked at each scheduling step.

**6. Recurring overlap guard.** When a recurring job's task chain takes longer than the recurrence interval, AS auto-reschedules the next instance while the current one is still running. The job scheduler detects this in `run_job_tasks()` via `should_skip_overlapping_instance()` and skips the new instance — logging a `[job_skipped]` event. Stuck siblings (expired heartbeat) are excluded from the check so recovery is not blocked. Override with the `recurring/skip-overlap` filter.

**7. Cross-job ordering.** AS sees a flat queue of actions — it has no concept of jobs or which tasks belong together. Serial scheduling guarantees task order *within* a job, but when multiple jobs are active, each has one pending task action in the queue and AS picks among them by priority, then scheduled date (effectively FIFO). This interleaving is fair scheduling by design — since each job has at most one pending task, no single long-running job can monopolize the queue.

```
$job->run()
  |
  v
Job Action (pending) --> AS executes --> overlap check --> mark RUNNING --> schedule task_1
                                            |                                  |
                                      (sibling running?                task_1 runs --> schedule task_2
                                       skip + log                                         |
                                       [job_skipped])              task_2 runs --> no more tasks --> mark complete
```

This serial model means AS handles time limits, memory monitoring, loopback chaining, and failure detection for each task individually.

## The Builder API

The job scheduler uses a builder pattern. Here is how the pieces fit together:

```php
$scheduler = GravityKitFoundation::scheduler();   // JobScheduler (entry point)

$handler = $scheduler->job();                      // JobHandler (builder)
$handler->create( 'my_job' );                      // Creates a Job model, returns JobHandler

$handler->set_label( 'My Job' );                   // Sets display label, returns JobHandler
$handler->set_product( 'gk-gravityexport' );       // Sets owning product, returns JobHandler
$handler->set_data( 'key', $value );               // Sets shared job data (any type), returns JobHandler

$handler->job();                                   // Access the underlying Job model directly

$handler->task();                                  // TaskHandler (task builder)
$handler->task()->create( 'name', $cb )->queue();  // Create + enqueue a task

$handler->run();                                   // Schedule and dispatch -> JobResult

$handler->is_scheduled();                          // Bool: any pending/in-progress instances?
$handler->is_paused();                             // Bool: is this job paused?
$handler->history();                               // JobHistoryHandler for this job
```

Key points:

- `scheduler()->job()` returns a new `JobHandler` each time. Call `create()` on it to set the job name.
- `->task()` returns the same `TaskHandler` for the lifetime of the handler. Call `create()` to register a task, then `queue()` to enqueue it.
- `queue()` with no arguments enqueues the last created task. `queue('task_name')` enqueues by name.
- All scheduling methods (`run()`, `async()`, `schedule_single()`, etc.) return a `JobResult`.

## Scheduling Methods

Every scheduling method returns a [`JobResult`](#jobresult-handling-scheduling-outcomes).

| Method | What it Does |
|--------|-------------|
| `run()` | Schedule + dispatch loopback to start immediately |
| `async()` | Schedule to run ASAP (no immediate dispatch) |
| `schedule( $timestamp )` | Run once at a specific time (alias for `schedule_single`) |
| `schedule_single( $timestamp )` | Run once at a specific time |
| `schedule_recurring( $timestamp, $interval )` | First run at `$timestamp`, repeat every `$interval` seconds |
| `run_recurring( $interval )` | Start recurring + dispatch immediately |
| `schedule_cron( $timestamp, $schedule )` | Cron expression (e.g., `'@daily'`, `'0 */6 * * *'`) |

**Which method should I use?**

- User clicks a button, expects it to start now? Use `run()`.
- Register a recurring background job on plugin init? Use `schedule_recurring()` or `schedule_cron()`.
- Need to defer work but don't need it immediately? Use `async()` or `schedule_single()`.
- The difference between `run()` and `async()`: `run()` fires an HTTP loopback request to trigger execution right away. `async()` relies on AS's own queue runner (WP-Cron or AS loopback) to pick it up.

## Working with Tasks

### Creating and Queueing Tasks

```php
$job->task()
    ->create( 'task_name', [ MyClass::class, 'callback' ], $args, $dependencies, $can_fail )
    ->queue();
```

Parameters:
- **`$name`** (string) -- Unique identifier within the job.
- **`$callback`** (callable) -- The function to execute.
- **`$args`** (array) -- Arguments passed to the callback. Default: `[]`.
- **`$dependencies`** (string[]) -- Names of tasks that must complete first. Default: `[]`.
- **`$can_fail`** (bool) -- If `true`, task failure does not fail the job. Default: `false`.

**Two `queue()` patterns:**

```php
// Immediate queue (no name) -- queues the last created task.
$job->task()->create( 'fetch', $callback )->queue();

// Queue by name -- useful when you create tasks first, then queue them later.
$job->task()->create( 'fetch', $callback );
$job->task()->create( 'write', $callback );
$job->task()->queue( 'fetch' );
$job->task()->queue( 'write' );
```

### Labels

Jobs and tasks can have human-readable labels for the Background Jobs admin page.

```php
$job->set_label( 'CSV Export' );
$job->set_product( 'gk-gravityexport' ); // Text domain of the owning product.

$job->task()
    ->create( 'fetch', $callback )
    ->set_label( 'Fetch Entries' )
    ->queue();
```

To retrieve product information later (e.g., in UI code), call `product_info()` on any job or task. It returns the full product data array from Foundation's product registry, keyed by field name (`name`, `version`, etc.):

```php
$info = $job->product_info();
// $info['name']    → 'GravityExport'
// $info['version'] → '2.5.0'
```

For lookups without a model instance, use the static method:

```php
$info = AbstractAction::resolve_product( 'gk-gravityexport' );
```

### Task Dependencies

```php
$job->task()->create( 'validate', $validate_cb )->queue();
$job->task()->create( 'process', $process_cb, [], [ 'validate' ] )->queue();
$job->task()->create( 'notify', $notify_cb, [], [ 'process' ] )->queue();
```

Tasks with unmet dependencies are skipped. If `validate` fails (and `can_fail` is false), the job fails. If `can_fail` is true, `validate` is marked failed but the job continues -- dependent tasks (`process`, `notify`) are skipped.

### Failure Handling

By default, a task failure fails the entire job. Mark non-critical tasks with `can_fail`:

```php
// Via constructor parameter:
$job->task()->create( 'cleanup', $cb, [], [], true )->queue();

// Via fluent setter:
$job->task()->create( 'cleanup', $cb )->can_fail()->queue();
```

**Recurring jobs and failure.** When a recurring job fails, `fail_job()` calls `reschedule_if_recurring()` so the next scheduled instance is still created. The failed instance is marked failed as usual, but the recurring schedule continues.

### Job-Level Data

Share data across all tasks in a job:

```php
// Set at creation time.
$job->set_data( 'form_id', 42 )
    ->set_data( 'user_id', 7 );

// Read in any task callback.
public static function my_task( array $args, $job_data ): ?NextRunRules {
    $form_id = $job_data['form_id'];
    // ...
}
```

Tasks can also update job data mid-execution using `GravityKitFoundation::scheduler()->checkpoint_with_data()` (see [Inter-Task Data Sharing](#inter-task-data-sharing)).

## Cooperative Time Budgeting

Long-running tasks cannot process everything in one PHP execution. The job scheduler injects a deadline into task args and provides utility functions so tasks can checkpoint their progress and resume in a new execution.

### `GravityKitFoundation::scheduler()->should_continue()`

```php
GravityKitFoundation::scheduler()->should_continue( array $args, int $margin = 2 ): bool
```

Returns `true` if there is still time before the deadline. The `$margin` (default: 2 seconds) leaves room for cleanup. Returns `true` when no deadline is set, so tasks work correctly outside the job scheduler too.

### `GravityKitFoundation::scheduler()->checkpoint()`

```php
GravityKitFoundation::scheduler()->checkpoint( array $next_args = [] ): NextRunRules
```

Returns a `NextRunRules` that reruns the task with updated args. Pass only keys that changed -- existing args are merged automatically.

### `GravityKitFoundation::scheduler()->checkpoint_with_data()`

```php
GravityKitFoundation::scheduler()->checkpoint_with_data( array $next_args, array $job_data ): NextRunRules
```

Same as `GravityKitFoundation::scheduler()->checkpoint()`, but also updates the job-level data shared across all tasks.

### Complete Example

```php
use GravityKit\Foundation\Scheduler\Models\NextRunRules;

class Importer {
    public static function import( array $args = [], $job_data = null ): ?NextRunRules {
        $offset     = $args['offset'] ?? 0;
        $batch_size = $args['batch_size'] ?? 100;
        $rows       = get_rows( $offset, $batch_size );

        foreach ( $rows as $i => $row ) {
            if ( ! GravityKitFoundation::scheduler()->should_continue( $args ) ) {
                return GravityKitFoundation::scheduler()->checkpoint( [ 'offset' => $offset + $i ] );
            }

            process_row( $row );
        }

        if ( count( $rows ) === $batch_size ) {
            // More rows remain.
            return GravityKitFoundation::scheduler()->checkpoint( [ 'offset' => $offset + $batch_size ] );
        }

        return null; // All rows processed.
    }
}
```

### Inter-Task Data Sharing

When a task needs to pass results to downstream tasks (processed count, file path, etc.), use `GravityKitFoundation::scheduler()->checkpoint_with_data()`:

```php
public static function import( array $args = [], $job_data = null ): ?NextRunRules {
    $offset = $args['offset'] ?? 0;
    $count  = $job_data['processed'] ?? 0;

    foreach ( get_rows( $offset, 100 ) as $i => $row ) {
        if ( ! GravityKitFoundation::scheduler()->should_continue( $args ) ) {
            return GravityKitFoundation::scheduler()->checkpoint_with_data(
                [ 'offset' => $offset + $i ],  // Task args (this task only).
                [ 'processed' => $count + $i ] // Job data (shared with all tasks).
            );
        }

        process( $row );
        $count++;
    }

    return GravityKitFoundation::scheduler()->checkpoint_with_data(
        [ 'offset' => $offset + count( $rows ) ],
        [ 'processed' => $count ]
    );
}

// A later task reads the shared data:
public static function send_report( array $args = [], $job_data = null ): ?NextRunRules {
    $total = $job_data['processed'] ?? 0;

    send_email( "Imported {$total} rows." );

    return null;
}
```

### Checkpoint vs Retry

These are two different concepts:

- **Checkpoint** (success path): The task is making progress and wants to continue later. Uses `GravityKitFoundation::scheduler()->checkpoint()` or returns `NextRunRules` with `rerun(true)` and updated args. No error occurred.
- **Retry** (error path): The task encountered a transient error and explicitly requests another attempt. The job scheduler increments the retry counter (`_meta.retries`) and reschedules. After 10 retries (configurable via `gk/foundation/scheduler/task/max-retries`), the task is marked failed.

**Retry requires opt-in.** A plain `\Exception` or `\Throwable` causes immediate permanent failure — no retry. To request a retry, throw `TaskException` with a `NextRunRules` instance that has `rerun()` set:

```php
use GravityKit\Foundation\Scheduler\Exceptions\TaskException;
use GravityKit\Foundation\Scheduler\Models\NextRunRules;

public static function my_task( array $args ): void {
    try {
        // ... work that may transiently fail ...
    } catch ( \RuntimeException $e ) {
        $rules = new NextRunRules();
        $rules->rerun();

        throw new TaskException( $e->getMessage(), $rules );
    }
}
```

This design is intentional: unexpected exceptions (bugs, fatal errors) should fail immediately so they surface for investigation. Only errors the task author has explicitly handled and deemed transient should trigger a retry.

The no-progress watchdog applies to checkpoints: if a task returns `rerun(true)` five times without changing its args (same fingerprint), it is marked failed as stuck. Always advance at least one argument (e.g., offset) when checkpointing.

## Job Management

### Status Checks

```php
$job_handler = GravityKitFoundation::scheduler()->job()->create( 'my_job' );

$job_handler->is_scheduled(); // true if pending or in-progress instances exist.
$job_handler->is_paused();    // true if the job is paused by name.
```

### Pause and Resume

```php
$job_handler = GravityKitFoundation::scheduler()->job()->create( 'my_job' );

$job_handler->pause();
$job_handler->unpause();
```

This pauses/unpauses all instances of the job by name (fires `job/{name}/paused` and `job/{name}/unpaused` hooks).

To pause/resume a specific running instance (from the Background Jobs UI), use the manager directly:

```php
$manager  = GravityKitFoundation::scheduler()->manager();
$instance = $manager->get_job( $job_id );

$manager->pause_job( $instance );   // Fires job/paused hook.
$manager->resume_job( $instance );  // Fires job/resumed hook.
```

### Unschedule and Delete

```php
$job_handler = GravityKitFoundation::scheduler()->job()->create( 'my_job' );

// Cancel all pending instances (marks them canceled -- visible in Background Jobs UI).
$job_handler->unschedule();

// Cancel just the latest pending instance.
$job_handler->unschedule_latest();

// Physical deletion (no ghost entries in the UI). Use when replacing a schedule.
$job_handler->delete();
```

### Cancel a Running Job

```php
$instance = GravityKitFoundation::scheduler()
    ->history( 'my_job' )
    ->running_job();

if ( $instance ) {
    GravityKitFoundation::scheduler()
        ->manager()
        ->cancel_job( $instance );
}
```

## Job History and Progress

### Querying History

```php
$history = GravityKitFoundation::scheduler()->history( 'my_job' );

// Filter by status.
$history->pending(); // ActionScheduler_Action[] keyed by action ID
$history->running();
$history->completed();
$history->failed();
$history->canceled();
$history->all();

// Get specific instances.
$history->running_job();           // JobInstance|null
$history->latest_job( [], true );  // Most recent, with status populated
$history->active_job();            // Running or next pending

// Scope to a specific schedule chain.
$history->since( $action_id )->completed();

// Query parameters.
$history->pending( [ 'per_page' => 50, 'order' => 'DESC' ] );
```

The `history()` method is available both on `JobScheduler` (by name) and on `JobHandler` (for the current job):

```php
// By name (standalone):
GravityKitFoundation::scheduler()->history( 'my_job' )->pending();

// From a job handler:
$job_handler->history()->pending();
```

### Error Inspection

```php
$history  = GravityKitFoundation::scheduler()->history( 'my_job' );
$last_err = $history->last_failed_id();

if ( $last_err ) {
    $error_message = $history->get_action_error( $last_err );
    $error_date    = $history->get_action_date( $last_err );
}
```

### Progress Monitoring

```php
$instance = GravityKitFoundation::scheduler()
    ->history( 'my_job' )
    ->running_job();

if ( $instance ) {
    $progress = $instance->progress();

    $progress->total();                // Total task count
    $progress->completed();            // [ 'task_name' => 'timestamp', ... ]
    $progress->running();
    $progress->pending();
    $progress->failed();
    $progress->skipped();
    $progress->task_status( 'fetch' ); // 'pending'|'running'|'completed'|'failed'|'skipped'|'unknown'

    $pct = ( count( $progress->completed() ) / $progress->total() ) * 100;
}
```

## JobResult: Handling Scheduling Outcomes

Every scheduling method returns a `JobResult`. It wraps three pieces of information: whether scheduling succeeded, the job ID, and any execution health warnings.

```php
$result = $job->run();

$result->succeeded();      // bool -- true if job_id > 0
$result->job_id();         // int  -- the AS action ID (0 on failure)
$result->has_warning();    // bool -- execution environment issue detected
$result->warning();        // string|null -- human-readable warning
$result->failure_code();   // string|null -- typed code for programmatic handling
$result->cancel();         // bool -- cancel the scheduled job if needed
```

**`succeeded()` and `has_warning()` are independent.** A job can be successfully scheduled (`succeeded() === true`) while also having a warning (`has_warning() === true`). This happens when the scheduling itself worked, but the execution environment is degraded (e.g., loopback blocked). The job is in the queue but may not execute until the environment issue is resolved.

### Failure Codes

| Code | Constant | Meaning |
|------|----------|---------|
| `loopback_and_cron_disabled` | `HealthCheck::LOOPBACK_AND_CRON_DISABLED` | Loopback blocked, WP-Cron disabled. No execution path. |
| `loopback_and_cron_spawn` | `HealthCheck::LOOPBACK_AND_CRON_SPAWN` | Loopback blocked, WP-Cron's spawn also needs loopback. |

### Example: Surfacing Warnings in UI

```php
$result = $job->run();

if ( ! $result->succeeded() ) {
    wp_send_json_error( [ 'message' => 'Failed to schedule the export.' ] );
}

if ( $result->has_warning() ) {
    // Job is scheduled but may not run.
    switch ( $result->failure_code() ) {
        case HealthCheck::LOOPBACK_AND_CRON_DISABLED:
            $notice = 'Set up a system cron to process exports.';
            break;
        case HealthCheck::LOOPBACK_AND_CRON_SPAWN:
            $notice = 'Enable ALTERNATE_WP_CRON or set up a system cron.';
            break;
        default:
            $notice = $result->warning();
    }

    wp_send_json_success( [ 'job_id' => $result->job_id(), 'warning' => $notice ] );
}

wp_send_json_success( [ 'job_id' => $result->job_id() ] );
```

## Hooks

All hooks use the prefix `gk/foundation/scheduler/` unless noted otherwise.

### Common Hooks

These are the hooks you will use most often.

**Listen for job completion or failure:**

```php
add_action( 'gk/foundation/scheduler/job/completed', function ( $job ) {
    // $job is a JobInstance. The job finished successfully.
    $job_data = $job->data();
    send_notification( 'Export complete: ' . $job_data['file_path'] );
} );

add_action( 'gk/foundation/scheduler/job/failed', function ( $job ) {
    // A non-optional task failed. Log or notify.
    error_log( 'Job failed: ' . $job->name() );
} );
```

**Listen for task failures:**

```php
add_action( 'gk/foundation/scheduler/task/execute/failed', function ( $task, $job, $exception ) {
    error_log( sprintf(
        'Task "%s" in job "%s" failed: %s',
        $task->name(),
        $job->name(),
        $exception->getMessage()
    ) );
}, 10, 3 );
```

**Filter task args at execution time:**

```php
add_filter( 'gk/foundation/scheduler/task/my_task/args', function ( $args, $task ) {
    $args['api_key'] = get_option( 'my_api_key' );

    return $args;
}, 10, 2 );
```

**Override the time budget for a specific task:**

```php
add_filter( 'gk/foundation/scheduler/task/time-budget', function ( $budget ) {
    // Give tasks more time on this server.
    return 25;
} );
```

**Disable background processing entirely:**

```php
// Override the global setting. Prevents all jobs from being scheduled.
add_filter( 'gk/foundation/scheduler/enabled', '__return_false' );
```

**Disable a specific job or task:**

```php
// Disable a job for a specific schedule type.
add_filter( 'gk/foundation/scheduler/job/my_job/recurring/enable', '__return_false' );

// Disable a task entirely.
add_filter( 'gk/foundation/scheduler/task/my_task/enabled', '__return_false' );
```

**React to pause/resume from the Background Jobs UI:**

```php
add_action( 'gk/foundation/scheduler/job/paused', function ( $job ) {
    // A specific instance was paused from the UI.
} );

add_action( 'gk/foundation/scheduler/job/resumed', function ( $job ) {
    // A paused instance was resumed from the UI.
} );
```

### Filter Reference

#### Global Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `enabled` | `$enabled` | Override the global background processing setting. Default: value of the "Background Processing" setting. |

#### Job Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `job/{name}/unique` | `$unique`, `$job` | Override whether the job allows duplicate instances. Default: `true`. |
| `job/{name}/priority` | `$priority`, `$job` | Override job priority. |
| `job/{name}/data` | `$data`, `$job` | Filter shared job data before scheduling. |
| `job/{name}/{schedule_type}/enable` | `$enable`, `$args`, `$unique`, `$priority` | Disable scheduling for a job + type (`async`, `single`, `recurring`, `cron`). |
| `job/max-tasks` | `$max_tasks`, `$job` | Maximum tasks per job. Default: 1000. |
| `jobs/scheduled` | `$jobs` | Filter the list of scheduled jobs before callbacks are registered. |
| `job/instances/query/default` | `$defaults` | Filter default query args for job instance retrieval. |
| `job/stuck-threshold` | `$threshold` | Seconds before a job is considered stuck. Default: 1200 (20 min). |
| `recurring/skip-overlap` | `$skip`, `$job_id`, `$job_name`, `$sibling_id` | Skip a recurring job instance when a sibling is already running. Default: `true`. Return `false` to allow overlap. |

#### Task Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `task/{name}/callback` | `$callback`, `$task` | Swap the task callback (validated with `is_callable`). |
| `task/{name}/enabled` | `$enabled`, `$task` | Disable a task. |
| `task/{name}/args` | `$args`, `$task` | Filter task args at execution time. |
| `task/set/args` | `$args`, `$task` | Intercept all task arg assignments. |
| `task/set/dependencies` | `$dependencies`, `$task` | Intercept dependency assignments. |
| `task/set/job-data` | `$data`, `$task` | Intercept job data assignments. |
| `task/get/can-fail` | `$can_fail`, `$task` | Override whether task failure is fatal. |
| `task/time-budget` | `$budget` | Override the per-task cooperative time budget in seconds. |
| `task/max-retries` | `$max`, `$task` | Max error-path retries before failing. Default: 10. |
| `task/max-no-progress-reruns` | `$max`, `$task` | Max consecutive identical checkpoint reruns before failing. Default: 5. |

#### Request Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `request/trigger/timeout` | `$timeout` | Loopback dispatch timeout in milliseconds. Default: 100. |
| `loopback-base-url` | `$base_url` | Override the base URL for all loopback requests. |

#### UI Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `ui/poll-interval` | `$seconds` | Background Jobs page auto-refresh interval in seconds. Clamped to a minimum of 1. Default: 5. |

### Action Reference

#### Scheduling Lifecycle

| Action | Parameters | When |
|--------|------------|------|
| `job/schedule/before` | `$name`, `$args`, `$schedule_type` | Before any scheduling operation. |
| `job/schedule/after` | `$job_id`, `$name`, `$schedule_type` | After successful scheduling. |
| `job/schedule/failed` | `$name`, `$args`, `$schedule_type` | When Action Scheduler returns 0. |
| `callbacks/registered` | `$schedule_handler` | All AS callbacks registered in WordPress. |

#### Job State Changes

| Action | Parameters | When |
|--------|------------|------|
| `job/{name}/unschedule` | `$job_name`, `$args` | Before unscheduling all instances. |
| `job/{name}/unschedule/latest` | `$job_name`, `$args` | Before unscheduling the latest instance. |
| `job/{name}/paused` | -- | After pausing all instances by name. |
| `job/{name}/unpaused` | -- | After unpausing all instances by name. |
| `job/{name}/delete` | `$name` | Before physically deleting all instances. |
| `job/paused` | `$job` (JobInstance) | After a specific instance is paused from the UI. |
| `job/resumed` | `$job` (JobInstance) | After a paused instance is resumed from the UI. |
| `job/canceled` | `$job` (JobInstance) | After an instance is canceled. |
| `job/completed` | `$job` (JobInstance) | After a job finishes successfully. |
| `job/failed` | `$job` (JobInstance) | After a job is marked failed. Recurring jobs still get their next instance via `reschedule_if_recurring()`. |

Note the two pause/resume hook types: `job/{name}/paused` fires when pausing by name (all instances, no parameters). `job/paused` fires when pausing a specific instance from the UI (passes the `JobInstance`).

#### Task Execution

| Action | Parameters | When |
|--------|------------|------|
| `task/execute/before` | `$task`, `$job` | Before a task callback runs. A `[task:name] [started]` log event is written immediately after. |
| `task/execute/after` | `$task`, `$job`, `$next_rules` | After a task callback completes. A `[task:name] [completed]` log event is written for tasks that finish (not checkpointing). |
| `task/execute/failed` | `$task`, `$job`, `$exception` | After a task fails (retries exhausted). Fires before `job/failed`. |

#### Internal Hooks

These two hooks are plain WordPress actions (no `gk/foundation/scheduler/` prefix):

| Hook | Description |
|------|-------------|
| `gk_scheduler_maintenance` | Recovery check (every 2 min while jobs are active). Detects and resumes orphaned jobs. |
| `gk_scheduler_run_task` | Executes the next eligible task for a job (one task per AS action). |

## WP-CLI Commands

All commands live under `wp gk jobs`. They use the same `JobQueryService` and `JobActionService` as the admin AJAX controller.

### Command Reference

| Command | Description |
|---------|-------------|
| `wp gk jobs list` | List jobs with filters and sorting |
| `wp gk jobs get <id>` | Show full job details (tasks, events, actions) |
| `wp gk jobs health` | Scheduler diagnostics and infrastructure health |
| `wp gk jobs cancel <id>` | Cancel a running or pending job |
| `wp gk jobs pause <id>` | Pause a job (freezes task chain) |
| `wp gk jobs unpause <id>` | Resume a paused job |
| `wp gk jobs retry <id>` | Retry a failed job from the point of failure |
| `wp gk jobs delete [<id>]` | Delete one or more jobs |
| `wp gk jobs run <id>` | Execute a job synchronously or dispatch it |

### `list`

```bash
wp gk jobs list [--status=<status>] [--hook=<hook>] [--per-page=<n>] [--format=<format>]
```

| Option | Accepted Values | Default | Description |
|--------|----------------|---------|-------------|
| `--status` | `pending`, `scheduled`, `in-progress`, `complete`, `failed`, `canceled`, `paused` | all | Filter by status. `scheduled` = pending with future time; `pending` = async/immediate. |
| `--hook` | hook name or prefix | all | Prefix matching. `--hook=gravityexport` matches `gravityexport_export_42`, etc. |
| `--per-page` | integer | 20 | Number of results. Use `-1` for unlimited. |
| `--format` | `table`, `json`, `csv`, `count` | `table` | Output format. |

**Table columns:** ID, Label, Status, Schedule (recurrence or type), Progress (`completed/total`), Time (GMT).

**Hook prefix matching.** Action Scheduler only supports exact hook matches. The CLI applies prefix matching client-side after querying, which means it runs after pagination. If you need all matching jobs, use `--per-page=-1`.

```bash
wp gk jobs list --status=failed
wp gk jobs list --hook=gravityexport --format=json
wp gk jobs list --status=complete --format=count
```

### `get`

```bash
wp gk jobs get <id> [--format=<format>]
```

Renders a multi-section detail view: header (status, hook, schedule, progress, available actions), task list (name, label, status, time, error), and event log (timestamps, types, messages).

```bash
wp gk jobs get 42
wp gk jobs get 42 --format=json | jq '.progress'
```

### `health`

```bash
wp gk jobs health [--format=<format>]
```

Runs the same diagnostics as the Background Jobs admin page. Checks: Queue Runner, WP-Cron, Loopback, Recovery heartbeat, PHP limits, Task Time Budget, Last Activity (with overdue detection).

**Exit codes:** `0` = healthy, `1` = one or more checks failed. JSON format includes structured `health` and `diagnostics` objects.

**Note:** The loopback probe runs from the CLI context and may not reflect web-request behavior (different network namespace in Docker, different DNS, firewall rules). If the admin UI reports healthy but CLI does not, the issue is environmental isolation.

```bash
wp gk jobs health
wp gk jobs health --format=json
```

### `cancel`

```bash
wp gk jobs cancel <id> [--yes]
```

Marks the job as canceled. Running tasks finish but no new tasks are scheduled. Completed tasks are not undone.

### `pause` and `unpause`

```bash
wp gk jobs pause <id> [--yes]
wp gk jobs unpause <id>
```

Pause freezes the task chain; unpause resumes from where it stopped. Unlike `cancel`, a paused job retains its state and can continue. `unpause` has no confirmation prompt (safe operation).

### `retry`

```bash
wp gk jobs retry <id>
```

Only works on `failed` jobs. Resets the failed task and all subsequent skipped tasks to `pending`, clears retry metadata, and restarts the task chain. Completed tasks before the failure are not re-executed.

### `delete`

```bash
wp gk jobs delete <id> [--yes]
wp gk jobs delete --status=<status> --all [--yes]
```

Single deletion requires `<id>`. Bulk deletion requires `--status` and `--all`. Physical removal from the database — no undo.

If some bulk deletions fail, the command exits with code `1` and reports the count of failures.

```bash
wp gk jobs delete 42 --yes
wp gk jobs delete --status=complete --all --yes
wp gk jobs delete --status=failed --all
```

### `run`

```bash
wp gk jobs run <id> [--reschedule] [--async] [--timeout=<seconds>]
```

| Option | Default | Description |
|--------|---------|-------------|
| `--reschedule` | off | For recurring jobs, reset next occurrence to `now + interval` after execution. |
| `--async` | off | Dispatch for background processing; return immediately. |
| `--timeout` | 300 | Max seconds for sync execution. Ignored with `--async`. |

#### Sync mode (default)

Executes the job inline in the current CLI process. The command:

1. Triggers the job action (marks it `RUNNING`).
2. Blocks Action Scheduler loopback dispatch via a scoped `pre_http_request` filter — only AS/cron URLs are blocked; task callbacks making external HTTP calls work normally.
3. Polls for pending task actions and processes each one inline.
4. Prints per-task progress: `Running task: <name>...` → `Task <name> done.`
5. Exits with `0` on completion, `1` on failure.

```
$ wp gk jobs run 42
Running task: Fetch Entries...
  Task Fetch Entries done.
Running task: Write CSV File...
  Task Write CSV File done.

Success: Job 42 completed (2/2 tasks).
```

**Signal handling.** If `pcntl` is available, SIGINT (Ctrl+C) and SIGTERM are caught. The current task finishes, then the command exits with a warning. The job can be resumed later with `wp gk jobs run <id>`.

**Timeout.** If `--timeout` is exceeded, the loop exits with a warning. The job status is left as-is (likely in-progress) and can be resumed or will be picked up by the recovery mechanism.

#### Async mode

```bash
wp gk jobs run 42 --async
```

Dispatches the job and returns immediately. Requires working loopback or WP-Cron for execution.

#### Run now vs. reschedule

By default, `run` creates a **one-off copy** of recurring jobs and executes it — the original schedule is untouched. With `--reschedule`, the original action is executed and the next occurrence is reset to `now + interval`.

```bash
# Daily export: run now, next occurrence still at original 2am
wp gk jobs run 42

# Daily export: run now AND reset schedule so next run is 24h from now
wp gk jobs run 42 --reschedule
```

### Output Formats

All listing commands support `--format`:

| Format | Use Case |
|--------|----------|
| `table` | Interactive terminal use |
| `json` | Scripting, piping to `jq` |
| `csv` | Spreadsheet export |
| `count` | Monitoring (`wp gk jobs list --status=failed --format=count`) |

### Exit Codes

| Code | Meaning |
|------|---------|
| `0` | Success — operation completed or health check passed |
| `1` | Failure — job not found, action error, health check failed, or partial bulk failure |

### Scripting Examples

```bash
# Count failed jobs for monitoring
FAILED=$(wp gk jobs list --status=failed --format=count)
[ "$FAILED" -gt 0 ] && echo "Alert: $FAILED failed jobs"

# Retry all failed jobs
wp gk jobs list --status=failed --format=json | jq -r '.[].id' | while read id; do
  wp gk jobs retry "$id"
done

# Run all pending jobs synchronously (testing/CI)
wp gk jobs list --status=pending --format=json | jq -r '.[].id' | while read id; do
  wp gk jobs run "$id" --timeout=120
done

# Clean up old completed jobs
wp gk jobs delete --status=complete --all --yes
```

## Multisite

Action Scheduler tables are **per-site** on multisite. Each site gets its own `wp_N_actionscheduler_actions`, `wp_N_actionscheduler_groups`, etc. (AS uses `$wpdb->prefix`, not `$wpdb->base_prefix`, and does not register tables as `ms_global_tables`.)

This means jobs are naturally isolated per site:

- **Each site has its own job queue.** The Background Jobs page shows only that site's jobs. No cross-site visibility or interference.
- **Settings are per-site with inheritance.** Background processing, loopback URL, and the "Show Background Jobs" toggle are per-site. Subsites without local settings inherit defaults from the main site (Foundation's standard settings inheritance).
- **Health checks are per-site.** Loopback connectivity varies by site (different domains, SSL configs).
- **Deactivation is per-site.** Cleaning up scheduler data only affects the current site's tables.

### WP-CLI on multisite

WP-CLI defaults to the main site. Pass `--url` to target a specific subsite:

```bash
# List jobs on the main site (default).
wp gk jobs list

# List jobs on a subsite.
wp gk jobs list --url=https://sub.example.com

# Run a job on a specific subsite.
wp gk jobs run 42 --url=https://sub.example.com
```

Each site has its own AS tables, so `--url` determines which site's jobs you see and act on.

### Network Admin

Super admins can view and manage jobs across all sites from a single page in the network admin dashboard. This is disabled by default.

**Enabling the network admin page:**

```php
add_filter( 'gk/foundation/scheduler/ui/show-in-network-admin', '__return_true' );
```

**How it works:**

- The page aggregates jobs from all sites using `UNION ALL` queries across per-site AS tables. No data is copied or synced — each query fans out to `wp_actionscheduler_actions`, `wp_2_actionscheduler_actions`, etc.
- Jobs use **composite IDs** (`blog_id:action_id`) to uniquely identify actions across sites. The UI strips the blog prefix and displays only the action ID alongside a site badge.
- A **site filter dropdown** lets super admins narrow the view to a single site. When a site is selected, queries run against that site's tables only (same performance as the per-site page).
- **Mutations** (cancel, delete, retry, pause, unpause) use `switch_to_blog()` to execute in the correct site context.
- **Cross-site execution is not supported.** "Run Now" and "Run & Reschedule" are hidden for network admin jobs because hook callbacks must be registered in the target site's request lifecycle.

**Key classes:**

| Class | Role |
|-------|------|
| `NetworkJobQueryService` | Builds UNION ALL queries for cross-site reads, status counts, and pagination |
| `NetworkJobActionService` | Wraps mutations with `switch_to_blog()` / `restore_current_blog()` |

**Performance:** WHERE conditions (status, group slug) push into each sub-SELECT, leveraging AS's composite index. Pagination uses SQL `LIMIT`/`OFFSET` on the union result. Sites without AS tables are auto-discovered and cached for 5 minutes.

## Under the Hood

### Time Budget Calculation

The `public static` method `TaskExecutor::get_task_time_budget()` computes the cooperative time budget as follows:

1. Read PHP's `max_execution_time` via `ini_get()`.
2. If the value is 0 (unlimited) or unset, use 25 seconds as fallback.
3. Cap at 30 seconds: `min(max_execution_time, 30)`.
4. Reserve 30% for overhead (progress saving, scheduling next action): multiply by 0.7.
5. Enforce a floor of 10 seconds.
6. Apply the `task/time-budget` filter, then enforce a hard floor of 5 seconds (`max(5.0, ...)`) to protect against bad filter values returning 0 or negative.

The result is injected into `$args['_meta']['deadline']` as a wall-clock timestamp before each task runs.

### Three-Layer Time Defense

**Layer 1 -- Cooperative deadline.** The primary mechanism. Well-behaved callbacks check `GravityKitFoundation::scheduler()->should_continue($args)` and checkpoint when time runs low.

**Layer 2 -- Hard CPU time cap.** A 5-minute `set_time_limit()` call catches CPU-bound runaways (infinite loops, regex backtracking). On Linux, this measures CPU time only -- sleep, DB queries, and HTTP requests do not count. May be disabled via `disable_functions` on some hosts. Skipped entirely under CLI (WP-CLI, system cron) where there is no execution time limit and CPU-heavy tasks may legitimately run for minutes.

**Layer 3 -- Shutdown handler.** A `register_shutdown_function()` callback detects `E_ERROR` from `set_time_limit()` kills. It marks the task as failed and logs to Action Scheduler so the recovery mechanism can handle it.

Host-level timeouts (PHP-FPM `request_terminate_timeout`, Nginx/Cloudflare proxy timeouts) are a hard kill that cannot be caught.

### Internal Metadata (`_meta`)

Both jobs and tasks store internal scheduler state in a `_meta` key. Do not read or write this key directly.

#### Task `_meta`

Each task's args contain a `_meta` sub-array managed by the scheduler:

```php
$args = [
    'offset'     => 500,       // Your data.
    'batch_size' => 100,       // Your data.
    '_meta' => [               // Scheduler internal — do not touch.
        'deadline'    => 1709000000.5,
        'retries'     => 0,
        'reruns'      => 3,
        'fingerprint' => 'abc123',
        'no_progress' => 0,
        'error'       => '',
        'batch_id'    => 'ff805546',
    ],
];
```

| Key | Purpose |
|-----|---------|
| `deadline` | Wall-clock timestamp when the task should stop processing. Checked by `GravityKitFoundation::scheduler()->should_continue()`. |
| `retries` | How many times this task has failed with an exception and been retried (max 10). |
| `reruns` | How many times this task has checkpointed and re-executed (no limit — this is normal progress, not failure). |
| `fingerprint` | Hash of the task args (excluding `_meta`). Used by the no-progress watchdog to detect stuck tasks. |
| `no_progress` | Consecutive reruns where the fingerprint did not change. Triggers failure when the threshold is exceeded. |
| `error` | Last exception message, if the task failed. |
| `batch_id` | 8-character hex ID of the HTTP request that last executed this task. Used to detect request boundaries in the activity log (see below). |

The `_meta` key is excluded from the args fingerprint so internal state changes do not trigger false "progress" signals in the no-progress watchdog.

#### Request Boundary Tracking

Action Scheduler processes multiple task reruns within a single HTTP request (an "AS batch"). When the batch time expires, AS dispatches a new HTTP loopback request to continue. The activity log marks these boundaries so operators can distinguish same-request reruns from cross-request ones.

How it works:

1. On the first run, `ScheduleHandler` generates a per-process `batch_id` (8-char hex, stable for the lifetime of the PHP process) and stores it in `_meta.batch_id`.
2. On each subsequent rerun, the current `batch_id` is compared against the stored value. If they differ, the log message includes "(new request)" to indicate a request boundary was crossed.
3. The `batch_id` is then updated to the current value for the next comparison.

Activity log output:

```
Started.
Run 1.                      {"offset":2000}
Run 2.                      {"offset":4000}
Run 3 (new request).        {"offset":6000}   ← AS batch expired, new HTTP request
Run 4.                      {"offset":8000}
Exported 10,000 rows.
Completed.
```

Runs 1–2 executed in the same HTTP request as "Started." Run 3 shows "(new request)" because AS dispatched a new loopback. Runs 3–4 shared that second request.

**Reading the batch ID programmatically:**

```php
// Current request's batch ID (stable for the lifetime of the PHP process).
$current = ScheduleHandler::get_request_id();

// The batch ID from the previous run (stored in task meta).
$previous = $task->meta( 'batch_id' );

// Whether this run is in a different HTTP request than the previous one.
$is_new_request = $current !== $previous;
```

`ScheduleHandler::get_request_id()` is available anywhere. `$task->meta('batch_id')` is available in hooks that receive the `$task` object (`gk/foundation/scheduler/task/execute/before`, `gk/foundation/scheduler/task/execute/after`). Foundation uses this internally to add the "(new request)" marker in the activity log.

#### Job `_meta`

The job's serialized args also contain a `_meta` sub-array for job-level bookkeeping:

| Key | Purpose |
|-----|---------|
| `started_at` | Unix timestamp when the job started running. Needed because the heartbeat overwrites `last_attempt_gmt` (see Job Timeout below). |

### Job Timeout and Heartbeat

Action Scheduler marks actions as "failed" if they stay in "running" status for more than 5 minutes (`ActionScheduler_QueueCleaner`). Since the parent job action stays RUNNING while separate task actions execute (which can take much longer), the job scheduler uses a heartbeat to keep the job alive.

**How it works.** Before each task runs, `TaskExecutor::extend_job_timeout()` writes `time() + 1200` to the job's `last_attempt_gmt` column. The stuck-job detector (`DbStore::get_stuck_running_jobs()`) queries for RUNNING jobs where `last_attempt_gmt < NOW() - threshold`. Because the heartbeat pushes `last_attempt_gmt` 20 minutes into the future, the job never matches the stuck query as long as tasks keep running.

**Why a future timestamp.** The heartbeat repurposes `last_attempt_gmt` as a lease. Writing a future value means the job has 20 minutes to complete its current task and schedule the next one. If no task extends the heartbeat within that window (because the process crashed), the value expires into the past and the recovery mechanism picks up the job.

**Display implications.** Because `last_attempt_gmt` contains a heartbeat value during execution, it cannot be used for "Started at" display. The job records its real start time in `_meta.started_at` (set by `ScheduleHandler::run_job_tasks()`). The serializer reads this value for in-progress jobs and falls back to `get_date()` for terminal statuses (where `mark_complete()` has reset `last_attempt_gmt` to the real completion time). At the task level, the serializer extracts `started_at` from the first `[started]` log event in each task's logs; the UI shows this on the task timeline row (falls back to `time` for older jobs without `[started]` events).

Configurable via the `job/stuck-threshold` filter (default: 1200 seconds / 20 minutes).

### Automatic Recovery

When a PHP process crashes mid-task, no next task gets scheduled and the job is orphaned. A recurring AS action (`gk_scheduler_maintenance`) runs every 2 minutes while jobs are active. It:

1. Queries for RUNNING jobs whose timeout has expired.
2. Checks whether a task action is already pending -- if so, just extends the timeout.
3. If no task action exists, schedules the next task and extends the timeout.

The crashed task is retried because progress is only recorded after the callback returns. A mid-execution crash leaves the task in the pending state.

The maintenance action is scheduled on-demand when the first task starts and hard-deleted when no running jobs remain.

**Idempotency note:** Recovery retries the crashed task from scratch. Task callbacks that perform non-idempotent operations (sending emails, financial transactions) should implement their own duplicate guards.

#### Task Sentinel (Dead Man's Switch)

A sentinel row is written to `wp_options` (`gk_scheduler_task_sentinel`) before each task executes and deleted after it succeeds. If the PHP process dies mid-task, the sentinel persists. On the next admin page load, `check_stale_sentinel()` detects the orphaned sentinel and fails the job via `do_action('gk/foundation/scheduler/job/failed')`.

**Flow:** `set_task_sentinel()` writes the sentinel (raw `$wpdb` INSERT) before the try/catch block. If the shutdown handler fires (fatal error), `fail_task_sentinel()` updates the row with `failed = true` and the error message. On success, `clear_task_sentinel()` deletes the row.

**Two detection modes:**
1. **Failed flag:** The sentinel has `failed = true` (shutdown handler ran — PHP detected a fatal it could recover from: memory exhaustion, timeout, uncaught `Throwable`). Acts on the next `admin_init`. Definitive signal.
2. **Age timeout:** Sentinel is older than `max(60, budget * 3)` seconds, capped at 600s. A healthy task clears its sentinel within one budget cycle, so this only trips on truly dead processes. Catches SIGKILL-class kills (kernel OOM-killer, worker restart via `pm.max_requests`, segfault, container eviction) where the PHP shutdown handler cannot run.

**Edge cases:**
- Job already completed/cancelled between crash and detection: sentinel is deleted, no action taken.
- Job already marked FAILED by the shutdown handler: sentinel check fires the hook without calling `fail_job()` again.
- Sentinel references a deleted job: sentinel is deleted, no action taken.
- Multiple tasks per request: only one sentinel exists at a time (overwritten by INSERT ON DUPLICATE KEY UPDATE).

### Why Status Flags Instead of Unschedule

Action Scheduler has a known limitation with recurring actions ([#1304](https://github.com/woocommerce/action-scheduler/issues/1304)): calling `as_unschedule_all_actions()` during a recurring action's own callback does not prevent rescheduling. AS fetches the action into memory before the callback runs, then reschedules based on the in-memory object after the callback returns -- never re-reading DB state. Any cancellation performed during the callback is silently undone.

The job scheduler avoids this entirely by managing job lifecycle through **status flags** rather than AS unschedule calls:

- `pause_job()` and `cancel_job()` set a status flag in the database (`PAUSED` / `CANCELED`) and cancel pending task actions.
- After each task completes, `TaskExecutor` checks the job's current status. If it is no longer `RUNNING`, the task chain stops -- no next task is scheduled.
- The parent recurring AS action is irrelevant at this point. Even if AS auto-reschedules it, the next execution sees the non-running status and does nothing.

This means pausing or canceling a job from the admin UI (or programmatically via `cancel_job()`) is always reliable, regardless of whether the job is mid-execution. **Do not call `unschedule()` from within a task callback to stop a recurring job** -- use `cancel_job()` instead.

### No-Progress Detection

When a task returns `rerun(true)`, the job scheduler computes an MD5 fingerprint of the task's product-facing args (excluding `_meta`). If the fingerprint is identical for 5 consecutive reruns, the task is marked failed as "stuck."

This catches callbacks that return `rerun()` unconditionally without advancing state (e.g., always checkpointing at offset 0 due to a bug).

Configurable via the `task/max-no-progress-reruns` filter.

### Job Uniqueness

By default, jobs are unique -- duplicate pending instances with the same name are not allowed. Override per job:

```php
// Via filter:
add_filter( 'gk/foundation/scheduler/job/my_job/unique', '__return_false' );

// Via the Job model:
$handler = GravityKitFoundation::scheduler()->job()->create( 'my_job' );
$handler->job()->set_unique( false );
```

### Diagnostics and Site Health

There are two layers of health monitoring with different scopes and triggers:

**HealthCheck (proactive alerting).** The `HealthCheck` class answers one question: can the scheduler execute jobs? It probes loopback connectivity and evaluates WP-Cron configuration. This runs proactively — `HealthCheck::run()` fires on every admin page load (for the submenu counter badge) and on every `$job->run()` call (via `JobResult` warnings). Results are cached in a transient (5-minute TTL). If the execution environment is broken (loopback blocked + cron disabled), users see a red badge on the Background Jobs menu and products receive a warning they can surface in UI. This is the critical alerting path.

**Diagnostics (on-demand detail).** The `Diagnostics` class (`Overview/Diagnostics.php`) provides a deeper breakdown: queue runner status, WP-Cron configuration, loopback probe, recovery heartbeat, PHP limits, time budget, and last activity with overdue detection. These are heavier (multiple DB queries + the HealthCheck probe) and run on-demand only — not on every request. The `debug_information` filter is registered on every admin page load but its callback only fires when WordPress renders the Site Health Info page. The Background Jobs diagnostics only fire via AJAX when a user opens the Diagnostics tab. The cache is populated by whichever consumer runs first.

**How they relate.** These are complementary layers, not duplicates. HealthCheck checks infrastructure ("can jobs run?"). Diagnostics checks operational state ("are jobs actually running?"). They cannot diverge on shared data — Diagnostics calls `HealthCheck::run()` and injects the result into `diagnose_wp_cron()` and `diagnose_loopback()`, so the loopback/cron answers are always consistent. However, HealthCheck can report "healthy" while Diagnostics reveals operational issues (queue idle with pending work, recovery heartbeat stale, jobs overdue). These operational issues only surface when someone checks the Diagnostics tab or Site Health — the admin notice only fires for HealthCheck-level failures.

**Caching.** `get_rows()` computes all 7 diagnostic rows and writes them to a transient (5-minute TTL). `Diagnostics::cached()` reads the transient without recomputing. The static factory `Diagnostics::collect()` creates its own `DbStore` and returns fresh rows.

**Consumer access patterns:**

- **Background Jobs UI (AJAX)** — calls `HealthCheck::flush()` first, then `new Diagnostics($store)->get_rows()`. Always fresh, always refreshes the cache.
- **Site Health Info** — `Diagnostics::cached() ?? Diagnostics::collect()`. Reads the cache if a previous UI visit populated it; otherwise falls back to a fresh computation. Registered via `Diagnostics::register_site_health()` (`debug_information` filter). PHP limits are skipped (already in the Server section). Multi-line values use pipe separators.

### Initialization Sequence

1. Action Scheduler loaded via Composer before `plugins_loaded` (priority 0).
2. On `plugins_loaded` (priority 0), AS registers the latest version.
3. On `plugins_loaded` (priority 100), Foundation initializes. `JobScheduler::__construct()` calls `DbStore::schedule_early_recovery()`, which runs a `SHOW TABLES` check against all four AS tables. If any are missing, it clears the AS schema version options and hooks `DbStore::recover_tables()` to `init` at priority 0. See [Table Recovery](#table-recovery).
4. On `init` (priority 0), if tables were missing: `recover_tables()` runs `dbDelta()` via AS's own schema classes to rebuild them — before AS's own `init` at priority 1 queries them.
5. On `init` (priority 1), AS initializes and fires `action_scheduler_init`.
6. On `init` (priority 10+), products create and schedule jobs.
7. On `action_scheduler_before_execute`, the job scheduler lazily registers all pending job callbacks (only for GravityKit actions in the `gk_scheduler` and `gk_scheduler_task` groups).
8. Jobs execute via WP-Cron, loopback dispatch, or manual trigger.

### Table Recovery

Action Scheduler stores all actions, logs, groups, and claims in four database tables (`actionscheduler_actions`, `actionscheduler_logs`, `actionscheduler_groups`, `actionscheduler_claims`). If any of these tables are deleted or corrupted — by a broken migration, an overzealous cleanup plugin, or manual database maintenance — AS queries fail with unhandled SQL errors that cascade through every page load.

AS tracks table existence via schema version options (`schema-ActionScheduler_StoreSchema`, `schema-ActionScheduler_LoggerSchema`) in `wp_options`. Deleting tables does not clear these options, so AS believes its schema is current and skips `dbDelta()` recreation. Tables stay missing indefinitely.

Foundation guards against this with two complementary layers:

**Early detection (proactive).** During `plugins_loaded` (priority 100), `DbStore::schedule_early_recovery()` runs a single `SHOW TABLES LIKE '{prefix}actionscheduler%'` query and checks the result against `DbStore::REQUIRED_TABLES`. If any table is missing, it:

1. Deletes the AS schema version options, forcing AS to believe its tables need creation.
2. Hooks `DbStore::recover_tables()` to `init` at priority 0 — one tick before AS's own init at priority 1.

`recover_tables()` instantiates `ActionScheduler_StoreSchema` and `ActionScheduler_LoggerSchema` directly and calls `register_tables( true )`, which runs `dbDelta()` to rebuild the missing tables. By the time AS initializes at priority 1, the tables exist.

**Runtime safety net (reactive).** Every call to `DbStore::db()` — the single access point for all Foundation database operations — checks two static flags:

- `$tables_verified`: set to `true` after the first successful `tables_exist()` check. Skips further checks for the rest of the request.
- `$recovery_failed`: set to `true` if `recover_tables()` returns `false` (e.g., missing AS classes, insufficient DB permissions). Prevents repeated recovery attempts that would add a `SHOW TABLES` query to every database call.

If neither flag is set, `db()` calls `tables_exist()`. If tables are present, it sets `$tables_verified` and proceeds. If tables are missing, it attempts `recover_tables()`. If recovery fails, it sets `$recovery_failed` to short-circuit — the request proceeds with degraded behavior rather than hammering the database.

**Cache invalidation.** `clear_table_cache()` resets both flags. It is hooked to `gk/foundation/plugin-activated` so that plugin activation (which may trigger AS schema updates) forces a fresh verification on the next database access.

```
plugins_loaded p100          init p0              init p1
       |                       |                    |
  SHOW TABLES ──→ missing? ──→ recover_tables() ──→ AS init (tables exist)
       |                       |
       └─ all present? ──→ $tables_verified = true (skip init hook)

Runtime (any db() call):
  $tables_verified? → yes → proceed
  $recovery_failed? → yes → proceed (degraded)
  tables_exist()?   → yes → $tables_verified = true → proceed
                    → no  → recover_tables() → success? → proceed
                                              → fail?   → $recovery_failed = true → proceed
```

## Debugging

### Enable Logging

Set the log level to "debug" in **GravityKit > Settings > Foundation > Logger**.

### Background Jobs Page

Navigate to **WP Admin > GravityKit > Background Jobs**. Shows job status, task progress, execution logs, and actions (Pause, Resume, Run, Cancel).

The page is hidden by default. Enable it via **GravityKit > Settings > Background Jobs > Show Background Jobs**.

The **Diagnostics** tab shows infrastructure health at a glance:

| Row | What it checks |
|-----|----------------|
| Queue Runner | Active GK-scoped claims, AS async-request-runner lock status. |
| WP-Cron | `DISABLE_WP_CRON`, `ALTERNATE_WP_CRON`, next scheduled run, overdue detection. |
| Loopback | Whether the site can make HTTP requests to itself. |
| Recovery | Stuck-job recovery heartbeat freshness. |
| PHP | `max_execution_time` and `memory_limit`. |
| Task Time Budget | Effective cooperative time budget from `TaskExecutor::get_task_time_budget()`. |
| Last Activity | When GravityKit and Action Scheduler last completed work. Warns when pending jobs are overdue (10+ minutes past scheduled date, filterable via `gk/foundation/scheduler/overdue-threshold`). |

The same data (except PHP limits) also appears in **Tools > Site Health > Info** under "GravityKit Background Processing". See [Diagnostics and Site Health](#diagnostics-and-site-health) for caching and access patterns.

### Debug Mode

Define `GK_SCHEDULER_DEBUG` in `wp-config.php`:

```php
define( 'GK_SCHEDULER_DEBUG', true );
```

This enables step-by-step execution mode. Instead of running tasks through Action Scheduler's loopback queue, visit `https://yoursite.com/?gk_scheduler_debug=1` to execute one task per page load in a normal request context. Refresh to advance to the next task. Particularly useful with Xdebug — set a breakpoint in your task callback and step through it without fighting async execution.

## Action Scheduler Loading Quirks

Foundation bundles Action Scheduler (AS) and loads it using the recommended pattern: `require_once action-scheduler.php` during plugin file loading, **before** `plugins_loaded` fires. This registers a version callback at `plugins_loaded` priority 0, and the version resolution at priority 1 picks the newest version across all plugins and initializes it.

### The theme support block bug (AS < 3.2.1)

AS has a "theme support" block at the bottom of `action-scheduler.php` designed for themes that load AS after `plugins_loaded`:

```php
// AS 3.1.6 (buggy):
if ( did_action( 'plugins_loaded' ) && ! class_exists( 'ActionScheduler' ) ) {
    action_scheduler_initialize_X_X_X();
}

// AS 3.2.1+ (fixed):
if ( did_action( 'plugins_loaded' ) && ! doing_action( 'plugins_loaded' ) && ! class_exists( 'ActionScheduler', false ) ) {
    action_scheduler_initialize_X_X_X();
}
```

The bug: WordPress increments `did_action('plugins_loaded')` at the **start** of `do_action`, before any callbacks fire. So during any `plugins_loaded` callback, `did_action('plugins_loaded')` is already `1`. If a plugin loads an old AS copy from a `plugins_loaded` callback at a negative priority (e.g., `-10`), the broken theme support block fires immediately — initializing the old classes and locking in the old autoloader paths before version resolution at priority 1 can pick the winner.

**Example:** WP Mail SMTP Pro loads AS 3.1.6 at `plugins_loaded` priority `-10`. The theme support block fires, loads 3.1.6 classes, and sets `ActionScheduler::plugin_path()` to WP Mail SMTP Pro's directory. When our 3.9.3 initializer runs at priority 1, it sees `class_exists('ActionScheduler')` is true and skips. All AS classes (including `ActionScheduler_DBStore`) are loaded from the old 3.1.6 copy, which is missing hooks and methods added in later versions.

Fixed in AS 3.2.1 via [#715](https://github.com/woocommerce/action-scheduler/pull/715) — see [#714](https://github.com/woocommerce/action-scheduler/issues/714) for the bug report.

### Foundation's preemptive initialization (Loader.php)

To defend against this, `Loader.php` hooks `plugins_loaded` at priority `-11` (before any known Pattern B plugin). It fires all already-registered version callbacks from priority 0 and calls `initialize_latest_version()`. This ensures `ActionScheduler` class exists before any buggy theme support block can run.

**Tradeoff:** If a newer AS is loaded via Pattern B (negative-priority `plugins_loaded` callback) and its priority-0 callback isn't registered yet at `-11`, it misses the resolution and our version locks in. This is acceptable because:

- Pattern B is an anti-pattern; AS documentation recommends loading during plugin file loading (Pattern A).
- A newer AS (>= 3.2.1) has the `doing_action` guard, so its theme support block won't fire even without our fix.
- Pattern A plugins (WooCommerce, etc.) register before `plugins_loaded` and are fully visible at `-11`.

### Loading patterns

| Pattern | When `require_once action-scheduler.php` runs | Version callback at p0 visible before `plugins_loaded`? | Theme support block risk |
|---------|-----------------------------------------------|----------------------------------------------------------|--------------------------|
| **A** (correct) | During plugin file loading, before `plugins_loaded` | Yes | None — `did_action` is 0 |
| **B** (problematic) | Inside `plugins_loaded` callback at negative priority | No — added during execution | High with AS < 3.2.1 |

Foundation uses Pattern A. Plugins like WP Mail SMTP Pro use Pattern B.

### Why no `after_execute` fallback?

Earlier iterations included an `action_scheduler_after_execute` fallback at `PHP_INT_MAX` in `execute_job()` to call `run_job_tasks` when `completed_action` didn't fire. This was removed because:

- The Loader.php fix ensures the correct AS classes are always loaded, so `completed_action` fires reliably.
- The fallback had complex self-removal logic to prevent accumulation across batch runs.
- It caused status flicker in the UI (task alternating between "skipped" and "running" during chunk transitions).

The Loader.php preemptive initialization is the proper fix at the root cause level.

## Troubleshooting

### "Background tasks cannot run" admin notice

This means loopback requests are failing and no alternative execution path exists.

**Is WP-Cron disabled?** (`DISABLE_WP_CRON` is true)
- Set up a [system cron](https://developer.wordpress.org/plugins/cron/hooking-into-the-system-task-scheduler/), or fix the loopback issue.

**Is WP-Cron enabled but still not working?** (loopback blocked)
- WP-Cron's `spawn_cron()` itself makes a loopback POST, so it cannot fire either.
- Fix: add `define( 'ALTERNATE_WP_CRON', true );` to `wp-config.php` (runs cron inline on page loads -- slower but functional), or set up a system cron.

### Action Scheduler tables missing

Foundation automatically detects and recovers missing AS tables on every request (see [Table Recovery](#table-recovery)). If you still see SQL errors referencing `actionscheduler_*` tables:

1. **Check database permissions.** The MySQL user needs `CREATE TABLE` and `ALTER` privileges. Recovery calls `dbDelta()`, which requires both.
2. **Check for early queries.** Code that runs raw SQL against AS tables before `plugins_loaded` priority 100 (when Foundation initializes) will fire before recovery has a chance to run. Move such queries to `init` or later.
3. **Force manual recovery.** Delete the schema version options and reload:
   ```sql
   DELETE FROM wp_options WHERE option_name IN ('schema-ActionScheduler_StoreSchema', 'schema-ActionScheduler_LoggerSchema');
   ```
   On the next page load, both AS and Foundation will recreate the tables.

### Jobs not running

1. Is background processing enabled? Check **GravityKit > Settings > Background Jobs**.
2. Is WP-Cron disabled? `define( 'DISABLE_WP_CRON', false );`
3. Manually trigger due events: `wp cron event run --due-now`
4. Check for pending instances: `GravityKitFoundation::scheduler()->history( 'my_job' )->pending()`
5. Enable debug logging and check Foundation logs.

### Tasks failing silently

1. Enable debug logging (see "Enable Logging" above).
2. Check the Background Jobs page for error details.
3. Use debug mode with `GK_SCHEDULER_DEBUG` for step-by-step execution.
4. Inspect the error: `$history->get_action_error( $history->last_failed_id() )`

### Job stuck in "running" status

1. The recovery check runs every 2 minutes. If the stuck threshold (20 min) has expired, recovery resumes the job automatically.
2. If recovery is not helping, check Foundation logs for repeated task failures.
3. Common causes: task callback crashes on every retry (OOM), database issues, external service timeouts.
4. Last resort: cancel the job manually from the Background Jobs page.

## Best Practices

### Task Design

- **Keep tasks small.** Each task should do one thing. You cannot stop a running task -- canceling a job only prevents future tasks from starting. Small tasks mean shorter delays before a cancellation takes effect.
- **Use dependencies for ordering.** Chain tasks that must run in a specific sequence.
- **Mark non-critical tasks with `can_fail()`.** A cleanup or notification task failing should not abort the whole job.

### Time Budgeting

- **Always check `GravityKitFoundation::scheduler()->should_continue()` in processing loops.** This respects PHP time limits and AS queue runner budgets.
- **Always advance state when checkpointing.** Update at least one arg (e.g., `offset`) to avoid triggering the no-progress watchdog.
- **Use `GravityKitFoundation::scheduler()->checkpoint_with_data()` for inter-task communication.** Downstream tasks receive the data via `$job_data`.

### Error Handling

- **Wrap task logic in try-catch blocks.** Uncaught exceptions are retried up to 10 times, then the task is marked failed.
- **Make tasks idempotent when possible.** Crash recovery retries the task from scratch. Guard against duplicate side effects (emails, payments).
- **Use application-level retry limits for transient errors.** Track attempts in job data and re-throw after your limit to avoid wasting the 10 automatic retries on a permanently failing operation.

### Naming and Organization

- **Prefix job names with plugin slug.** `gk_export_csv`, not `csv_export`. Avoids collisions with other products.
- **Set labels and product text domains.** They appear in the Background Jobs UI and execution health notices.
- **Use `set_product()` so the execution health notice names the affected product.** When loopback and cron are both unavailable, the job scheduler shows an admin notice: *"Background tasks scheduled by **GravityExport, GravityImport** cannot run…"*. Without `set_product()`, the notice is generic. The text domain getter is `$job->product()`; for full product data (name, version, etc.), use `$job->product_info()`.

### Monitoring

- **Check `JobResult` warnings.** Surface them in UI so users know when the execution environment is degraded.
- **Implement progress tracking for long-running jobs.** Use the `JobProgress` API in AJAX handlers to show completion percentage.
- **Use the Background Jobs page** for debugging. Enable it in GravityKit settings.

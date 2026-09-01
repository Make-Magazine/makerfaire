<?php
/**
 * REST controller for the View entries API (shipped public surface).
 *
 * @package     GravityKit\GravityView\REST\Controllers
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\REST\Controllers;

use GravityKit\GravityView\REST\ViewsRoute;

/**
 * The entries API (`GET /views/`, `/views/{id}`, `/views/{id}/entries`,
 * `/views/{id}/entries/{entry_id}`) historically lived on
 * `\GravityKit\GravityView\REST\ViewsRoute`. This controller is the
 * canonical home for the implementation going forward.
 *
 * `src/REST/ViewsRoute.php` stays as a thin BC shim (`class ViewsRoute
 * extends ViewEntriesController`) so:
 *  - the `GV\REST\Views_Route` legacy alias in `src/Aliases/gv-aliases.php`
 *    keeps resolving;
 *  - `Core::$routes['views']` registration still works;
 *  - existing tests asserting against `\GV\REST\Views_Route` and
 *    `\GravityKit\GravityView\REST\ViewsRoute` both still find the class.
 *
 * Routes, response shapes, status codes, headers all preserved (G4 BC).
 *
 * During the transition, this class inherits FROM ViewsRoute to reuse the
 * existing implementation; once ViewsRoute is reduced to a shim, the
 * direction inverts (ViewsRoute extends ViewEntriesController).
 *
 * @since 3.0.0
 */
class ViewEntriesController extends ViewsRoute {

	// All entries-API behavior inherited from ViewsRoute during the
	// transition. Phase 4i finalization moves the implementation HERE
	// and reduces ViewsRoute to a thin shim `class ViewsRoute extends.
	// ViewEntriesController {}`. NOT marked `final` so the shim swap
	// can land without a follow-up rename.
}

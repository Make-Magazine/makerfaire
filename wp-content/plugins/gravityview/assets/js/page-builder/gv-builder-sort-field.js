/**
 * GravityView Page Builder Sort Field - Dynamic Per-View Filtering
 *
 * Dynamically updates the Sort Field dropdown to show only fields from the
 * currently selected View's form. Targets builders that use native <select>
 * elements (Beaver Builder, Divi Backend Builder).
 *
 * Gutenberg has its own React-based SortFieldSelector.
 * Divi VB and Elementor use text inputs (not affected).
 *
 * All per-View sort field data is preloaded via wp_localize_script (no AJAX).
 *
 * Beaver Builder integration uses BB's official `settings-form-init` hook
 * (FLBuilder.addHook) which fires every time a settings panel finishes
 * rendering. This is more reliable than DOM mutation observation because
 * it runs after BB has fully populated the form.
 *
 * @package GravityKit\GravityView\PageBuilders
 * @since TODO
 */
( function () {
	'use strict';

	var config = window.gkGravityViewBuilderSortField || {};
	var fieldsByView = config.sortFieldsByView || {};
	var defaultLabel = config.defaultLabel || 'Default';

	if ( ! Object.keys( fieldsByView ).length ) {
		return;
	}

	var VIEW_SELECT_NAMES = [ 'view_id', 'viewId' ];
	var SORT_SELECT_NAMES = [ 'sort_field', 'sortField' ];

	var PANEL_SELECTOR =
		'.fl-builder-settings, .fl-lightbox-content, ' +
		'.et-pb-option-modal-container, .et_pb_module_settings';

	function getOptionsForView( viewId ) {
		return fieldsByView[ viewId ] || [];
	}

	function findViewSelect( container ) {
		if ( ! container || ! container.querySelector ) {
			return null;
		}
		for ( var i = 0; i < VIEW_SELECT_NAMES.length; i++ ) {
			var name = VIEW_SELECT_NAMES[ i ];
			var el = container.querySelector(
				'[data-option_name="' + name + '"] select, select[name="' + name + '"]'
			);
			if ( el ) {
				return el;
			}
		}
		return null;
	}

	function findSortFieldSelect( container ) {
		if ( ! container || ! container.querySelector ) {
			return null;
		}
		for ( var i = 0; i < SORT_SELECT_NAMES.length; i++ ) {
			var name = SORT_SELECT_NAMES[ i ];
			var el = container.querySelector(
				'[data-option_name="' + name + '"] select, select[name="' + name + '"]'
			);
			if ( el ) {
				return el;
			}
		}
		return null;
	}

	function findSettingsContainer( el ) {
		return el.closest( PANEL_SELECTOR ) || el.parentElement;
	}

	/**
	 * Replace a <select>'s options with the given list and restore the current
	 * value if still present. Tags the element with a `data-gk-synced-view`
	 * attribute so subsequent syncs can detect already-synced state without
	 * relying on an option count heuristic (which can false-positive when two
	 * forms happen to have the same number of sortable fields).
	 */
	function replaceSelectOptions( selectEl, options, viewId ) {
		var current = selectEl.value;
		selectEl.innerHTML = '';

		var def = document.createElement( 'option' );
		def.value = '';
		def.textContent = defaultLabel;
		selectEl.appendChild( def );

		var found = current === '';
		for ( var i = 0; i < options.length; i++ ) {
			var opt = document.createElement( 'option' );
			opt.value = options[ i ].value;
			opt.textContent = options[ i ].label;
			selectEl.appendChild( opt );
			if ( options[ i ].value == current ) {
				found = true;
			}
		}

		// Record which View the options now belong to, BEFORE dispatching a
		// change event. Observers that re-enter syncPanel will see the match
		// and bail out, preventing a replace → mutation → replace loop.
		selectEl.dataset.gkSyncedView = String( viewId );

		if ( found ) {
			selectEl.value = current;
		} else {
			selectEl.value = '';
			selectEl.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		}
	}

	function syncPanel( panel ) {
		var viewSelect = findViewSelect( panel );
		if ( ! viewSelect || ! viewSelect.value ) {
			return;
		}

		var sortSelect = findSortFieldSelect( panel );
		if ( ! sortSelect ) {
			return;
		}

		var viewId = String( viewSelect.value );
		if ( sortSelect.dataset.gkSyncedView === viewId ) {
			return;
		}

		replaceSelectOptions( sortSelect, getOptionsForView( viewId ), viewId );
	}

	function syncAllPanels() {
		var panels = document.querySelectorAll( PANEL_SELECTOR );
		for ( var i = 0; i < panels.length; i++ ) {
			syncPanel( panels[ i ] );
		}
	}

	// ──────────────────────────────────────────────
	// Beaver Builder hook integration
	//
	// BB's settings UI runs in the same window as the page editor and fires
	// `settings-form-init` after every settings panel is rendered (initial
	// open + tab switches). Hook into it directly instead of guessing from
	// DOM mutations.
	// ──────────────────────────────────────────────

	function bindBeaverBuilderHook() {
		if ( typeof window.FLBuilder === 'undefined' || typeof window.FLBuilder.addHook !== 'function' ) {
			return false;
		}
		window.FLBuilder.addHook( 'settings-form-init', function () {
			// Reset the sync marker so the form is re-evaluated even if BB
			// re-rendered a panel that was previously synced.
			document.querySelectorAll( '.fl-builder-settings select[data-gk-synced-view]' ).forEach( function ( el ) {
				delete el.dataset.gkSyncedView;
			} );
			syncAllPanels();
		} );
		return true;
	}

	// ──────────────────────────────────────────────
	// Event handling
	// ──────────────────────────────────────────────

	// Immediate update on view select change.
	document.addEventListener( 'change', function ( e ) {
		var el = e.target;
		if ( ! el || el.tagName !== 'SELECT' ) {
			return;
		}

		var name = el.name || el.getAttribute( 'data-id' ) || '';
		var matches = VIEW_SELECT_NAMES.indexOf( name ) !== -1;

		if ( ! matches ) {
			for ( var i = 0; i < VIEW_SELECT_NAMES.length; i++ ) {
				if ( el.closest( '[data-option_name="' + VIEW_SELECT_NAMES[ i ] + '"]' ) ) {
					matches = true;
					break;
				}
			}
		}

		if ( ! matches ) {
			return;
		}

		var container = findSettingsContainer( el );
		var sortSelect = findSortFieldSelect( container );
		if ( sortSelect ) {
			replaceSelectOptions( sortSelect, getOptionsForView( el.value ), String( el.value ) );
		}
	} );

	// Debounced re-sync on any DOM mutation (fallback for builders that
	// don't expose a settings-loaded hook, like Divi Backend Builder).
	var syncPending = false;
	function scheduleSync() {
		if ( syncPending ) {
			return;
		}
		syncPending = true;
		requestAnimationFrame( function () {
			requestAnimationFrame( function () {
				syncPending = false;
				syncAllPanels();
			} );
		} );
	}

	var observer = new MutationObserver( function ( mutations ) {
		for ( var m = 0; m < mutations.length; m++ ) {
			if ( mutations[ m ].addedNodes && mutations[ m ].addedNodes.length ) {
				scheduleSync();
				return;
			}
		}
	} );

	function start() {
		observer.observe( document.body, { childList: true, subtree: true } );
		syncAllPanels();

		// Try to bind the BB hook now; if FLBuilder isn't ready yet, retry
		// for up to ~5 seconds. BB usually loads its JS asynchronously.
		if ( ! bindBeaverBuilderHook() ) {
			var attempts = 0;
			var retry = setInterval( function () {
				attempts++;
				if ( bindBeaverBuilderHook() || attempts >= 50 ) {
					clearInterval( retry );
				}
			}, 100 );
		}
	}

	if ( document.body ) {
		start();
	} else {
		document.addEventListener( 'DOMContentLoaded', start );
	}
} )();

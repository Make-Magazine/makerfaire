<?php
/**
 * An entry locking class that syncs with GFEntryLocking.
 *
 * @package   GravityKit\GravityView\Extension\EditEntry
 * @since 2.5.2
 * @since 3.0.0 Migrated to GravityKit\GravityView\Extension\EditEntry namespace.
 */

namespace GravityKit\GravityView\Extension\EditEntry;

use GravityKit\GravityView\Utils\Assets;
use GV\Utils;
use GV\View;
use GV\View_Collection;

/**
 * An entry locking class that syncs with GFEntryLocking.
 *
 * @since 2.5.2
 */
class EditEntryLocking {
	const LOCK_CACHE_KEY_PREFIX = 'lock_entry_';

	/**
	 * The entry meta key that stores the lock heartbeat data (last heartbeat timestamp, interval, and page-load token).
	 *
	 * @since 3.0.0
	 */
	const HEARTBEAT_META_KEY = 'gv_edit_lock_heartbeat';

	/**
	 * The interval in seconds to check for locked entries in the UI.
	 *
	 * @since 2.38.0
	 */
	const LOCK_CHECK_INTERVAL = 10;

	/**
	 * Multiplier for calculating stale threshold.
	 * Stale threshold = heartbeat_interval * STALE_THRESHOLD_MULTIPLIER.
	 *
	 * @since 2.48.5
	 */
	const STALE_THRESHOLD_MULTIPLIER = 2;

	/**
	 * Per-request, per-entry navigate-away release tokens, keyed by entry ID.
	 *
	 * Generated once per request so the head (enqueue_scripts) and body (maybe_lock_object) phases of
	 * the same page load install the same token, even though they run on different class instances.
	 *
	 * @since 3.0.0
	 *
	 * @var array<int,string>
	 */
	protected static $request_lock_tokens = [];

	/**
	 * Load extension entry point.
	 *
	 * DO NOT RENAME this method. Required by the class-edit-entry.php component loader.
	 *
	 * @since 2.5.2
	 *
	 * @see   GravityView_Edit_Entry::load_components()
	 *
	 * @return void
	 */
	public function load() {
		if ( ! has_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_scripts' ] ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_scripts' ] );
		}

		add_filter( 'heartbeat_received', [ $this, 'heartbeat_refresh_nonces' ], 10, 2 );
		add_filter( 'heartbeat_received', [ $this, 'heartbeat_check_locked_objects' ], 10, 2 );
		add_filter( 'heartbeat_received', [ $this, 'heartbeat_refresh_lock' ], 10, 2 );
		add_filter( 'heartbeat_received', [ $this, 'heartbeat_request_lock' ], 10, 2 );

		// Authenticated only: these handlers manipulate lock-request markers (reject
		// deletes one). They must not be exposed to logged-out users via nopriv;
		// front-end Edit Entry users are logged in, so wp_ajax_ already serves them.
		add_action( 'wp_ajax_gf_lock_request_entry', [ $this, 'ajax_lock_request' ], 1 );
		add_action( 'wp_ajax_gf_reject_lock_request_entry', [ $this, 'ajax_reject_lock_request' ], 1 );

		add_action( 'wp_ajax_gv_release_entry_lock', [ $this, 'ajax_release_entry_lock' ] );
	}

	/**
	 * Get the lock request meta for an object.
	 *
	 * @since 2.34.1
	 *
	 * @param int $object_id The object ID.
	 *
	 * @return int|null The User ID or null.
	 */
	protected function get_lock_request_meta( $object_id ) {
		return \GFCache::get( 'lock_request_entry_' . $object_id );
	}

	// TODO: Convert to extending Gravity Forms
	public function ajax_lock_request() {
		$object_id = rgget( 'object_id' );

		if ( ! $this->current_user_can_edit_locked_entry( $object_id ) ) {
			wp_send_json( false );
		}

		$response = $this->request_lock( $object_id );

		wp_send_json( $response );
	}

	// TODO: Convert to extending Gravity Forms
	public function ajax_reject_lock_request() {
		$object_id = rgget( 'object_id' );

		if ( ! $this->current_user_can_edit_locked_entry( $object_id ) ) {
			wp_send_json( false );
		}

		$response = $this->delete_lock_request_meta( $object_id );

		wp_send_json( $response );
	}

	/**
	 * Whether the current user may edit the entry a lock-request marker belongs to.
	 *
	 * The request/reject handlers take an entry id straight from the request, so
	 * without this per-object check any logged-in user could plant or clear another
	 * entry's take-over request.
	 *
	 * Editability is resolved server-side, never from a request-supplied View id: a
	 * View id in the payload is attacker-controlled, and check_user_cap_edit_entry()
	 * does not bind the entry to the View. The `user_edit` setting is per-View while
	 * this lock is per-entry, so a user may edit the entry iff a global entry cap
	 * applies OR at least one View connected to the entry's form grants them edit
	 * access (e.g. they authored the entry on a View with Edit Entry enabled).
	 *
	 * @since 3.0.2
	 *
	 * @param int|string $object_id The entry ID from the request.
	 *
	 * @return bool
	 */
	protected function current_user_can_edit_locked_entry( $object_id ) {
		$entry = \GFAPI::get_entry( $object_id );

		if ( is_wp_error( $entry ) ) {
			return false;
		}

		// Global entry caps grant edit access independent of any View; check first so
		// admins/editors (the Gravity Forms admin lock UI) never pay the View query.
		$can_edit_any_entry = \GVCommon::has_cap( [ 'gravityforms_edit_entries', 'gravityview_edit_others_entries' ], $entry['id'] );

		if ( $can_edit_any_entry ) {
			return true;
		}

		// Otherwise the user reaches Edit Entry only through a View whose `user_edit`
		// applies to them, so the entry's `user_edit` is meaningless without one.
		foreach ( \GVCommon::get_connected_views( $entry['form_id'] ) as $view_post ) {
			if ( \GravityView_Edit_Entry::check_user_cap_edit_entry( $entry, (int) $view_post->ID ) ) {
				return true;
			}
		}

		return false;
	}

	// TODO: Convert to extending Gravity Forms
	protected function delete_lock_request_meta( $object_id ) {
		\GFCache::delete( 'lock_request_entry_' . $object_id );

		return true;
	}

	// TODO: Convert to extending Gravity Forms
	protected function request_lock( $object_id ) {
		if ( 0 == ( $user_id = get_current_user_id() ) ) {
			return false;
		}

		$lock_holder_user_id = $this->check_lock( $object_id );

		$result = [];

		if ( ! $lock_holder_user_id ) {
			$this->set_lock( $object_id );

			$result['html']   = __( 'You now have control', 'gk-gravityview' );
			$result['status'] = 'lock_obtained';

			return $result;
		}

		if ( \GVCommon::has_cap( 'gravityforms_edit_entries' ) ) {
			$user = get_userdata( $lock_holder_user_id );

			/* translators: %s: the display name of the user the request was sent to. */
			$result['html'] = sprintf( __( 'Your request has been sent to %s.', 'gk-gravityview' ), $user->display_name );
		} else {
			$result['html'] = __( 'Your request has been sent.', 'gk-gravityview' );
		}

		$this->update_lock_request_meta( $object_id, $user_id );

		$result['status'] = 'lock_requested';

		return $result;
	}

	/**
	 * Updates the lock request meta for an object.
	 *
	 * @since 2.34
	 *
	 * @param string $object_id
	 * @param string $lock_request_value
	 *
	 * @return void
	 */
	protected function update_lock_request_meta( $object_id, $lock_request_value ) {
		\GFCache::set( 'lock_request_entry_' . $object_id, $lock_request_value, true, 120 );
	}

	/**
	 * Checks whether to enqueue scripts based on:
	 *
	 * - Is it Edit Entry?
	 * - Is the entry connected to a View that has `edit_locking` enabled?
	 * - Is the entry connected to a form connected to a currently-loaded View?
	 * - Does the user have the capability to edit the entry?
	 * - Is the nonce valid?
	 *
	 * @internal
	 * @since 2.7
	 *
	 * @global \WP_Post $post
	 *
	 * @return void
	 */
	public function maybe_enqueue_scripts() {
		global $post;

		if ( ! $entry = gravityview()->request->is_edit_entry() ) {
			return;
		}

		$entry = $entry->as_entry();

		/**
		 * Overrides whether to load the entry lock UI assets.
		 *
		 * This filter runs before checking whether if the edit entry link is valid, user has the capability to edit the entry, etc.
		 *
		 * @since 2.34.1
		 *
		 * @param bool  $load  Whether to load the entry lock UI assets. Default: false.
		 * @param array $entry The entry.
		 */
		if ( apply_filters( 'gk/gravityview/edit-entry/renderer/enqueue-entry-lock-assets', false, $entry ) ) {
			$this->enqueue_scripts( $entry );
		}

		if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$views = View_Collection::from_post( $post );

		// If any Views being loaded have entry locking, enqueue the scripts.
		foreach ( $views->all() as $view ) {
			// Make sure the View has edit locking enabled
			if ( ! $view->settings->get( 'edit_locking' ) ) {
				continue;
			}

			// Make sure that the entry belongs to the View form.
			if ( $view->form->ID !== (int) $entry['form_id'] ) {
				continue;
			}

			// Check user capabilities.
			if ( ! \GravityView_Edit_Entry::check_user_cap_edit_entry( $entry, $view ) ) {
				continue;
			}

			// Check the nonce.
			$edit_entry_render             = new \GravityView_Edit_Entry_Render( \GravityView_Edit_Entry::getInstance() );
			$edit_entry_render::$nonce_key = ( \GravityView_Edit_Entry::getInstance() )::get_nonce_key( $view->ID, $entry['form_id'], $entry['id'] );

			if ( ! $edit_entry_render->verify_nonce() ) {
				continue;
			}

			$this->enqueue_scripts( $entry, $view );

			break;
		}
	}

	/**
	 * Enqueues the required scripts and styles from Gravity Forms.
	 *
	 * Called via load() and `wp_enqueue_scripts`
	 *
	 * @since 2.5.2
	 * @since 2.38.0 Added $view parameter.
	 *
	 * @param array     $entry Gravity Forms entry array.
	 * @param View|null $view  (optional) The View object.
	 *
	 * @return void
	 */
	protected function enqueue_scripts( $entry, ?View $view = null ) {
		$lock_user_id = $this->check_lock( $entry['id'] );

		$request_check_interval = $view ? $view->settings->get( 'edit_locking_check_interval', self::LOCK_CHECK_INTERVAL ) : self::LOCK_CHECK_INTERVAL;

		// When the current user holds (or is about to hold) the lock, store the interval and a fresh
		// page-load token. The token lets the navigate-away beacon release this exact session's lock
		// without an in-flight beacon from a previous page load (e.g. a reload) clearing a newer lock.
		$lock_token = '';

		if ( ! $lock_user_id && get_current_user_id() ) {
			$lock_token = $this->get_request_lock_token( $entry['id'] );

			$this->update_lock_heartbeat( $entry['id'], $request_check_interval, $lock_token );
		}

		// Gravity forms locking checks if #wpwrap exist in the admin dashboard,
		// So we have to add the lock UI to the body before the gforms locking script is loaded.
		wp_add_inline_script(
			'heartbeat',
			'
			jQuery( document ).ready( function( $ ) {
					if ( $( "#wpwrap" ).length === 0 ) {
						var lockUI = ' . wp_json_encode( $this->get_lock_ui( $lock_user_id, $entry ) ) . ';
						$( "body" ).prepend( lockUI );
					}

					if ( typeof wp !== "undefined" && wp.heartbeat ) {
						setTimeout( function() {
							wp.heartbeat.interval( ' . (int) $request_check_interval . ', 0 );
						}, 1000 );
					}

				} );
			'
		);

		$min          = isset( $_GET['gform_debug'] ) ? '' : Assets::min();
		$locking_path = \GFCommon::get_base_url() . '/includes/locking/';

		wp_enqueue_script( 'gforms_locking', $locking_path . "js/locking{$min}.js", [ 'jquery', 'heartbeat' ], \GFCommon::$version );
		wp_enqueue_style( 'gforms_locking_css', $locking_path . "css/locking{$min}.css", [ 'edit' ], \GFCommon::$version );

		// add inline css to hide notification-dialog-wrap if it has the hidden class
		wp_add_inline_style(
			'gforms_locking_css',
			'
			.notification-dialog-wrap.hidden {
				display: none;
			}
		'
		);

		$translations = array_map( 'wp_strip_all_tags', $this->get_strings() );

		$strings = [
			'noResponse'    => $translations['no_response'],
			'requestAgain'  => $translations['request_again'],
			'requestError'  => $translations['request_error'],
			'gainedControl' => $translations['gained_control'],
			'rejected'      => $translations['request_rejected'],
			'pending'       => $translations['request_pending'],
		];

		$lock_user_id = $this->check_lock( $entry['id'] );

		$vars = [
			'hasLock'           => ! $lock_user_id ? 1 : 0,
			'lockUI'            => $this->get_lock_ui( $lock_user_id, $entry ),
			'objectID'          => $entry['id'],
			'objectType'        => 'entry',
			'strings'           => $strings,
			'heartbeatInterval' => $request_check_interval,
			'releaseNonce'      => $lock_token ? wp_create_nonce( 'gv_release_lock_' . $entry['id'] ) : '',
			'releaseToken'      => $lock_token,
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
		];

		wp_localize_script( 'gforms_locking', 'gflockingVars', $vars );

		// Add script to send heartbeat interval with every heartbeat request, and to release the lock
		// immediately when the holder navigates away (Cancel, tab close, etc.). The release is a
		// best-effort beacon; the server-side stale-lock detection remains the backstop for sessions
		// that end without a beacon (crash, force-quit, lost connection).
		wp_add_inline_script(
			'gforms_locking',
			'
			( function( $ ) {
				$( document ).on( "heartbeat-send.gform-refresh-lock-entry", function( e, data ) {
					if ( data["gform-refresh-lock-entry"] && window.gflockingVars ) {
						data["gform-refresh-lock-entry"].heartbeatInterval = window.gflockingVars.heartbeatInterval;
					}
				} );

				$( window ).on( "beforeunload pagehide", function() {
					var vars = window.gflockingVars;

					if ( ! vars || ! vars.hasLock || ! vars.releaseToken || ! navigator.sendBeacon ) {
						return;
					}

					var formData = new FormData();

					formData.append( "action", "gv_release_entry_lock" );
					formData.append( "entry_id", vars.objectID );
					formData.append( "nonce", vars.releaseNonce );
					formData.append( "token", vars.releaseToken );

					navigator.sendBeacon( vars.ajaxUrl, formData );
				} );
			} )( jQuery );
		',
			'after'
		);
	}

	/**
	 * Returns a string with the Lock UI HTML markup.
	 *
	 * Called script enqueuing, added to JavaScript `gforms_locking` global variable.
	 *
	 * @since 2.5.2
	 *
	 * @see   GravityView_Edit_Entry_Locking::check_lock
	 *
	 * @param int   $user_id The User ID that has the current lock. Will be empty if entry is not locked
	 *                       or is locked to the current user.
	 * @param array $entry   The entry array.
	 *
	 * @return string The Lock UI dialog box, etc.
	 */
	public function get_lock_ui( $user_id, $entry ) {
		$user = get_userdata( $user_id );

		$locked = $user_id && $user;

		$hidden = $locked ? '' : ' hidden';

		if ( $locked ) {
			if ( \GVCommon::has_cap( 'gravityforms_edit_entries' ) || $entry['created_by'] == get_current_user_id() ) {
				$avatar              = get_avatar( $user->ID, 64 );
				$person_editing_text = $user->display_name;
			} else {
				$current_user        = wp_get_current_user();
				$avatar              = get_avatar( $current_user->ID, 64 );
				$person_editing_text = _x( 'the person who is editing the entry', 'Referring to the user who is currently editing a locked entry', 'gk-gravityview' );
			}

			// Anonymous users can't request control (requires logged-in user).
			$show_request_control_default = get_current_user_id() > 0;

			/**
			 * Filters whether to show the Request Control button in the lock dialog.
			 *
			 * @since 2.50.1
			 *
			 * @param bool  $show_button Whether to show the Request Control button. Default: true for logged-in users, false for anonymous.
			 * @param array $entry       The entry being edited.
			 */
			$show_request_control = apply_filters( 'gk/gravityview/edit-entry/lock-dialog/show-request-control', $show_request_control_default, $entry );

			$request_control_button = $show_request_control
				? '<button id="gform-lock-request-button" class="button button-primary wp-tab-last">' . esc_html__( 'Request Control', 'gk-gravityview' ) . '</button>'
				: '';

			// Use appropriate message based on whether Request Control is available.
			$message_key = $show_request_control ? 'currently_locked' : 'currently_locked_no_takeover';

			// Build Cancel URL: single entry page (remove edit parameter from current URL).
			$cancel_url = remove_query_arg( 'edit' );

			$message = '<div class="gform-locked-message">
                            <div class="gform-locked-avatar">' . $avatar . '</div>
                            <p class="currently-editing" tabindex="0">' . esc_html( sprintf( $this->get_string( $message_key ), $person_editing_text ) ) . '</p>
                            <p>
                                <a id="gform-take-over-button" style="display:none" class="button button-primary wp-tab-first" href="' . esc_url( add_query_arg( 'get-edit-lock', '1' ) ) . '" onclick="window.location.replace( this.href ); return false;">' . esc_html__( 'Take Over', 'gk-gravityview' ) . '</a>
                                ' . $request_control_button . '
                                <a class="button" href="' . esc_url( $cancel_url ) . '">' . esc_html( $this->get_string( 'cancel' ) ) . '</a>
                            </p>
                            <div id="gform-lock-request-status">
                                <!-- placeholder -->
                            </div>
                        </div>';
		} else {
			$message = '<div class="gform-taken-over">
                            <div class="gform-locked-avatar"></div>
                            <p class="wp-tab-first" tabindex="0">
                                <span class="currently-editing"></span><br>
                            </p>
                            <p>
                                <a id="gform-release-lock-button" class="button button-primary wp-tab-last"  href="' . esc_url( add_query_arg( 'release-edit-lock', '1' ) ) . '" onclick="window.location.replace( this.href ); return false;">' . esc_html( $this->get_string( 'accept' ) ) . '</a>
                                <button id="gform-reject-lock-request-button" style="display:none"  class="button button-primary wp-tab-last">' . esc_html__( 'Reject Request', 'gk-gravityview' ) . '</button>
                            </p>
                        </div>';

		}

		$html  = '<div id="gform-lock-dialog" class="notification-dialog-wrap' . $hidden . '">
                    <div class="notification-dialog-background"></div>
                    <div class="notification-dialog">' . $message . '</div>';
		$html .= '</div>';

		/**
		 * Modifies the edit entry lock UI markup.
		 *
		 * @since 2.34.1
		 *
		 * @param string $html The HTML markup.
		 */
		return apply_filters( 'gk/gravityview/edit-entry/renderer/entry-lock-dialog-markup', $html );
	}

	/**
	 * Returns localized text strings used in the UI.
	 *
	 * @since 2.5.2
	 *
	 * @return array An array of translations.
	 */
	public function get_strings() {
		$translations = [
			/* translators: %s: the display name of the user currently editing the entry. */
			'currently_locked'             => __( 'This entry is currently locked. Click on the "Request Control" button to let %s know you\'d like to take over.', 'gk-gravityview' ),
			'currently_locked_no_takeover' => __( 'This entry is currently being edited. Please try again later.', 'gk-gravityview' ),
			/* translators: %s: the display name of the user currently editing the entry. */
			'currently_editing'            => __( '%s is currently editing this entry', 'gk-gravityview' ),
			/* translators: %s: the display name of the user who took over editing. */
			'taken_over'                   => __( '%s has taken over and is currently editing this entry.', 'gk-gravityview' ),
			/* translators: %s: the display name of the user requesting editing control. */
			'lock_requested'               => __( '%s has requested permission to take over control of this entry.', 'gk-gravityview' ),
			'accept'                       => __( 'Accept', 'gk-gravityview' ),
			'cancel'                       => __( 'Cancel', 'gk-gravityview' ),
			'gained_control'               => __( 'You now have control', 'gk-gravityview' ),
			'request_pending'              => __( 'Pending', 'gk-gravityview' ),
			'no_response'                  => __( 'No response', 'gk-gravityview' ),
			'request_again'                => __( 'Request again', 'gk-gravityview' ),
			'request_error'                => __( 'Error', 'gk-gravityview' ),
			'request_rejected'             => __( 'Your request was rejected', 'gk-gravityview' ),
		];

		return array_map( 'wp_strip_all_tags', $translations );
	}

	/**
	 * Returns a localized string.
	 *
	 * @param string $string The string to get.
	 *
	 * @return string A localized string. See self::get_strings()
	 */
	public function get_string( $string ) {
		return Utils::get( $this->get_strings(), $string, '' );
	}

	/**
	 * Locks the entry... maybe.
	 *
	 * Has 3 modes of locking:
	 *  - acquire (get), which reloads the page after locking the entry
	 *  - release, which reloads the page after unlocking the entry
	 *  - default action to lock on load if not locked
	 *
	 * @since 2.34
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return void
	 */
	public function maybe_lock_object( $entry_id ) {
		$current_url = home_url( add_query_arg( null, null ) );

		if ( isset( $_GET['get-edit-lock'] ) ) {
			$this->set_lock( $entry_id, null, $this->get_request_lock_token( $entry_id ) );

			// Use replace() so the transient `?get-edit-lock=1` URL is not left in browser history;
			// otherwise the edit form's "Cancel" (history.go(-1)) lands on it and re-triggers the takeover.
			echo '<script>window.location.replace( ' . wp_json_encode( remove_query_arg( 'get-edit-lock', $current_url ) ) . ' );</script>';

			exit();
		} elseif ( isset( $_GET['release-edit-lock'] ) ) {
			$this->delete_lock_meta( $entry_id );

			$current_url = remove_query_arg( 'edit', $current_url );

			echo '<script>window.location.replace( ' . wp_json_encode( remove_query_arg( 'release-edit-lock', $current_url ) ) . ' );</script>';

			exit();
		} elseif ( ! $this->check_lock( $entry_id ) ) {
			$this->set_lock( $entry_id, null, $this->get_request_lock_token( $entry_id ) );
		}
	}

	/**
	 * Checks if this entry is locked to some other user.
	 *
	 * @since 2.34.1
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return boolean Yes or no.
	 */
	public function check_lock( $entry_id ) {
		$user_id = $this->get_lock_meta( $entry_id );

		if ( ! $user_id || $user_id == get_current_user_id() ) {
			return false;
		}

		return $user_id;
	}

	/**
	 * Check if the current user has a lock request for an object.
	 *
	 * @since 2.34.1
	 *
	 * @param int $object_id The object ID.
	 *
	 * @return int|false The User ID or false.
	 */
	protected function check_lock_request( $object_id ) {
		$user_id = (int) $this->get_lock_request_meta( $object_id );

		if ( ! $user_id || $user_id === get_current_user_id() ) {
			return false;
		}

		return $user_id;
	}

	/**
	 * Returns the lock status by leveraging GF's persistent caching mechanism.
	 *
	 * @since 2.34.1
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return int|null The User ID or null.
	 */
	public function get_lock_meta( $entry_id ) {
		return \GFCache::get( $this->get_lock_cache_key_for_entry( $entry_id ) );
	}

	/**
	 * Sets the lock for an entry.
	 *
	 * @since 2.34.1
	 * @since 3.0.0 The lock now expires after 150 seconds (matching Gravity Forms) instead of 1500.
	 *
	 * @param int $entry_id The entry ID.
	 * @param int $user_id  The user ID to lock the entry to.
	 *
	 * @return void
	 */
	public function update_lock_meta( $entry_id, $user_id ) {
		\GFCache::set( $this->get_lock_cache_key_for_entry( $entry_id ), $user_id, true, 150 );
	}

	/**
	 * Returns the cache key used to retrieve/save the lock status for an entry.
	 *
	 * @since 2.34.1
	 *
	 * @param int $entry_id
	 *
	 * @return string
	 */
	public function get_lock_cache_key_for_entry( $entry_id ) {
		return self::LOCK_CACHE_KEY_PREFIX . $entry_id;
	}

	/**
	 * Returns the navigate-away release token for the current page load.
	 *
	 * Generated once per request and reused across both the enqueue (head) and lock-acquisition (body)
	 * phases, so a reload installs its own token before its lock is acquired. This prevents the prior
	 * page's in-flight beacon (which carries the old token) from releasing the reloaded page's lock.
	 *
	 * @since 3.0.0
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return string The page-load token.
	 */
	protected function get_request_lock_token( $entry_id ) {
		if ( empty( self::$request_lock_tokens[ $entry_id ] ) ) {
			// UUID v4, not the filterable wp_generate_password(): a third-party
			// `random_password` filter could make it collide and defeat the
			// stale-beacon guard.
			self::$request_lock_tokens[ $entry_id ] = wp_generate_uuid4();
		}

		return self::$request_lock_tokens[ $entry_id ];
	}

	/**
	 * Releases the lock for an entry.
	 *
	 * @since 2.34.1
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return void
	 */
	public function delete_lock_meta( $entry_id ) {
		\GFCache::delete( $this->get_lock_cache_key_for_entry( $entry_id ) );

		gform_delete_meta( $entry_id, self::HEARTBEAT_META_KEY );
	}

	/**
	 * Returns the lock heartbeat data (last heartbeat timestamp and interval) for an entry.
	 *
	 * @since 3.0.0
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return array The heartbeat data, or an empty array if none is stored.
	 */
	protected function get_lock_heartbeat( $entry_id ) {
		$heartbeat = gform_get_meta( $entry_id, self::HEARTBEAT_META_KEY );

		return is_array( $heartbeat ) ? $heartbeat : [];
	}

	/**
	 * Updates the lock heartbeat data for an entry.
	 *
	 * Stores the last heartbeat timestamp (used to detect stale sessions) and, when provided,
	 * the heartbeat interval and page-load token. Stored as a single entry meta row instead of
	 * transients to avoid bloating the options table on every heartbeat.
	 *
	 * @since 3.0.0
	 *
	 * @param int         $entry_id The entry ID.
	 * @param int|null    $interval (optional) The heartbeat interval in seconds. When null, the stored interval is kept.
	 * @param string|null $token    (optional) The page-load release token. When null, the stored token is kept.
	 *
	 * @return void
	 */
	protected function update_lock_heartbeat(
		$entry_id,
		$interval = null,
		#[\SensitiveParameter]
		$token = null
	) {
		$heartbeat = $this->get_lock_heartbeat( $entry_id );

		$heartbeat['timestamp'] = time();

		if ( (int) $interval > 0 ) {
			$heartbeat['interval'] = (int) $interval;
		}

		if ( null !== $token ) {
			$heartbeat['token'] = (string) $token;
		}

		gform_update_meta( $entry_id, self::HEARTBEAT_META_KEY, $heartbeat );
	}

	/**
	 * Gets the timestamp of the last heartbeat for a lock.
	 *
	 * @since 2.48.5
	 * @since 3.0.0 Reads from entry meta instead of a transient.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return int|null Timestamp or null if not found.
	 */
	protected function get_lock_timestamp( $entry_id ) {
		return $this->get_lock_heartbeat( $entry_id )['timestamp'] ?? null;
	}

	/**
	 * Gets the stored heartbeat interval for a lock.
	 *
	 * @since 2.48.5
	 * @since 3.0.0 Reads from entry meta instead of a transient.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return int The heartbeat interval in seconds, or default if not found.
	 */
	protected function get_lock_interval( $entry_id ) {
		$interval = $this->get_lock_heartbeat( $entry_id )['interval'] ?? 0;

		return $interval ? (int) $interval : self::LOCK_CHECK_INTERVAL;
	}

	/**
	 * Gets the stale threshold for a specific entry's lock.
	 * Calculates based on the stored heartbeat interval.
	 *
	 * @since 2.48.5
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return int Stale threshold in seconds (interval * multiplier).
	 */
	protected function get_lock_stale_threshold( $entry_id ) {
		$interval  = $this->get_lock_interval( $entry_id );
		$threshold = $interval * self::STALE_THRESHOLD_MULTIPLIER;

		/**
		 * Filters the stale lock threshold for an entry.
		 *
		 * @since 2.48.5
		 *
		 * @param int $threshold The calculated threshold in seconds.
		 * @param int $entry_id  The entry ID.
		 * @param int $interval  The heartbeat interval for this lock.
		 */
		return (int) apply_filters( 'gk/gravityview/edit-entry/lock-stale-threshold', $threshold, $entry_id, $interval );
	}

	/**
	 * Checks if a lock is stale (holder hasn't sent heartbeat recently).
	 *
	 * @since 2.48.5
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return bool True if lock is stale (2+ missed heartbeats).
	 */
	protected function is_lock_stale( $entry_id ) {
		$timestamp = $this->get_lock_timestamp( $entry_id );

		if ( ! $timestamp ) {
			// No timestamp means old lock - consider stale
			return true;
		}

		$age       = time() - $timestamp;
		$threshold = $this->get_lock_stale_threshold( $entry_id );

		return $age > $threshold;
	}

	/**
	 * Locks the entry to the current user.
	 *
	 * @since 2.5.2
	 * @since 3.0.0 Added the optional $interval and $token parameters.
	 *
	 * @param int         $entry_id The entry ID.
	 * @param int|null    $interval (optional) The heartbeat interval in seconds to store with the lock.
	 * @param string|null $token    (optional) The page-load release token to store with the lock.
	 *
	 * @return int|false Locked or not.
	 */
	public function set_lock(
		$entry_id,
		$interval = null,
		#[\SensitiveParameter]
		$token = null
	) {
		$entry = \GFAPI::get_entry( $entry_id );

		if ( ! \GravityView_Edit_Entry::check_user_cap_edit_entry( $entry ) ) {
			return false;
		}

		if ( 0 === ( $user_id = get_current_user_id() ) ) {
			return false;
		}

		$this->update_lock_meta( $entry_id, $user_id );
		$this->update_lock_heartbeat( $entry_id, $interval, $token );

		return $user_id;
	}

	/**
	 * Checks if the objects are locked.
	 *
	 * @since 2.34.1
	 *
	 * @param array $response The response array.
	 * @param array $data     The data array.
	 *
	 * @return array The response array.
	 */
	public function heartbeat_check_locked_objects( $response, $data ) {
		$checked = [];

		$heartbeat_key = 'gform-check-locked-objects-entry';

		if ( array_key_exists( $heartbeat_key, $data ) && is_array( $data[ $heartbeat_key ] ) ) {
			foreach ( $data[ $heartbeat_key ] as $object_id ) {
				if ( ( $user_id = $this->check_lock( $object_id ) ) && ( $user = get_userdata( $user_id ) ) ) {
					$send = [ 'text' => sprintf( $this->get_string( 'currently_editing' ), $user->display_name ) ];

					if ( ( $avatar = get_avatar( $user->ID, 18 ) ) && preg_match( "|src='([^']+)'|", $avatar, $matches ) ) {
						$send['avatar_src'] = $matches[1];
					}

					$checked[ $object_id ] = $send;
				}
			}
		}

		if ( ! empty( $checked ) ) {
			$response[ $heartbeat_key ] = $checked;
		}

		return $response;
	}

	/**
	 * Refreshes the lock for an entry.
	 *
	 * @since 2.34.1
	 *
	 * @param array $response The response array.
	 * @param array $data     The data array.
	 *
	 * @return array The response array.
	 */
	public function heartbeat_refresh_lock( $response, $data ) {
		$heartbeat_key = 'gform-refresh-lock-entry';

		if ( array_key_exists( $heartbeat_key, $data ) ) {
			$received = $data[ $heartbeat_key ];

			$send = [];

			if ( ! isset( $received['objectID'] ) ) {
				return $response;
			}

			$object_id = $received['objectID'];

			if ( ( $user_id = $this->check_lock( $object_id ) ) && ( $user = get_userdata( $user_id ) ) ) {
				$error = [
					'text' => sprintf( $this->get_string( 'taken_over' ), $user->display_name ),
				];

				$avatar = get_avatar( $user->ID, 64 );

				if ( $avatar && preg_match( "|src='([^']+)'|", $avatar, $matches ) ) {
					$error['avatar_src'] = $matches[1];
				}

				$send['lock_error'] = $error;
			} elseif ( $new_lock = $this->set_lock( $object_id, (int) ( $received['heartbeatInterval'] ?? 0 ) ) ) {
				// Successfully acquired/refreshed lock. set_lock() also records the heartbeat timestamp and interval.
				$send['new_lock'] = $new_lock;

				if ( ( $lock_requester = $this->check_lock_request( $object_id ) ) && ( $user = get_userdata( $lock_requester ) ) ) {
					$lock_request = [
						'text' => sprintf( $this->get_string( 'lock_requested' ), $user->display_name ),
					];

					$avatar = get_avatar( $user->ID, 64 );

					if ( $avatar && preg_match( "|src='([^']+)'|", $avatar, $matches ) ) {
						$lock_request['avatar_src'] = $matches[1];
					}

					$send['lock_request'] = $lock_request;
				}
			}

			$response[ $heartbeat_key ] = $send;
		}

		return $response;
	}

	/**
	 * Requests the lock for an entry.
	 *
	 * @since 2.34.1
	 *
	 * @param array  $response  The response array.
	 * @param array  $data      The data array.
	 * @param string $screen_id The screen ID.
	 *
	 * @return array The response array.
	 */
	public function heartbeat_request_lock( $response, $data ) {
		$heartbeat_key = 'gform-request-lock-entry';

		if ( ! array_key_exists( $heartbeat_key, $data ) ) {
			return $response;
		}

		$received = $data[ $heartbeat_key ];

		$send = [];

		if ( ! isset( $received['objectID'] ) ) {
			return $response;
		}

		$object_id = $received['objectID'];

		$user_id = $this->check_lock( $object_id );

		if ( $user_id && get_userdata( $user_id ) ) {
			// Entry is locked by another user

			// Check if lock is stale (holder closed tab)
			if ( $this->is_lock_stale( $object_id ) ) {
				// Lock holder hasn't sent heartbeat (2+ missed) - they're gone!
				// Clean up old lock and grant immediately
				$this->delete_lock_meta( $object_id );
				$this->delete_lock_request_meta( $object_id );

				if ( $this->set_lock( $object_id ) ) {
					$send['status'] = 'granted';

					/**
					 * Fires when a lock is automatically granted due to stale session.
					 *
					 * @since 2.48.5
					 *
					 * @param int $object_id The entry ID.
					 * @param int $user_id   The previous lock holder's user ID.
					 */
					do_action( 'gk/gravityview/edit-entry/lock-granted-stale', $object_id, $user_id );
				}
			} else {
				// Lock holder is still active - normal request flow
				$send['status'] = $this->get_lock_request_meta( $object_id ) ? 'pending' : 'deleted';
			}
		} elseif ( $this->set_lock( $object_id ) ) {
			// No lock exists - grant immediately
			$send['status'] = 'granted';
		}

		$response[ $heartbeat_key ] = $send;

		return $response;
	}

	/**
	 * Refreshes nonces for an entry.
	 *
	 * @since 2.34.1
	 *
	 * @param array $response The response array.
	 * @param array $data     The data array.
	 *
	 * @return array The response array.
	 */
	public function heartbeat_refresh_nonces( $response, $data ) {
		if ( ! array_key_exists( 'gform-refresh-nonces', $data ) ) {
			return $response;
		}

		$received = $data['gform-refresh-nonces'];

		$response['gform-refresh-nonces'] = [ 'check' => 1 ];

		if ( ! isset( $received['objectID'] ) ) {
			return $response;
		}

		$object_id = $received['objectID'];

		if ( ! \GVCommon::has_cap( 'gravityforms_edit_entries' ) || empty( $received['post_nonce'] ) ) {
			return $response;
		}

		if ( 2 === wp_verify_nonce( $received['object_nonce'], 'update-contact_' . $object_id ) ) {
			$response['gform-refresh-nonces'] = [
				'replace'        => [
					'_wpnonce' => wp_create_nonce( 'update-object_' . $object_id ),
				],
				'heartbeatNonce' => wp_create_nonce( 'heartbeat-nonce' ),
			];
		}

		return $response;
	}

	/**
	 * Releases the lock when the holder navigates away from the edit page.
	 *
	 * Called via a best-effort `navigator.sendBeacon()` on `beforeunload`/`pagehide`. The lock is only
	 * released when the request carries the same page-load token currently stored for the lock, so an
	 * in-flight beacon from a previous page load (e.g. a reload that already re-acquired the lock) cannot
	 * clear the newer lock.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function ajax_release_entry_lock() {
		$entry_id = absint( $_POST['entry_id'] ?? 0 );
		$nonce    = sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) );
		$token    = sanitize_text_field( wp_unslash( $_POST['token'] ?? '' ) );

		if ( ! $entry_id || ! wp_verify_nonce( $nonce, 'gv_release_lock_' . $entry_id ) ) {
			wp_send_json_error( 'Invalid request' );
		}

		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			wp_send_json_error( 'User not authenticated' );
		}

		if ( (int) $this->get_lock_meta( $entry_id ) !== $user_id ) {
			wp_send_json_error( 'Lock not owned by current user' );
		}

		$stored_token = $this->get_lock_heartbeat( $entry_id )['token'] ?? '';

		if ( '' === $stored_token || ! hash_equals( (string) $stored_token, $token ) ) {
			wp_send_json_error( 'Lock token mismatch' );
		}

		$this->delete_lock_meta( $entry_id );

		wp_send_json_success( 'Lock released' );
	}
}

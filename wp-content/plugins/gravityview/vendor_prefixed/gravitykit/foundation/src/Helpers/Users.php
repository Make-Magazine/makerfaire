<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 05-December-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Helpers;

use WP_User;
use GravityKit\GravityView\Foundation\Exceptions\UserException;

/**
 * Central utilities for fetching WordPress users inside Foundation.
 *
 * @since 1.3.0
 */
final class Users {
    /**
     * Cached look-ups keyed by user ID.
     *
     * @since 1.3.0
     *
     * @var array<int,WP_User|UserException>
     */
    private static $cache = [];

    /**
     * Returns a valid WP_User or UserException when the user does not exist.
     *
     * @since 1.3.0
     *
     * @param int|null $uid User ID or null for current.
     *
     * @return WP_User|UserException
     */
    public static function get( ?int $uid = null ) {
        if ( null === $uid ) {
            $uid = get_current_user_id();
        }

        // Normalise logged-out users as 0 for cache key purposes.
        $key = (int) ( $uid ?: 0 );

        if ( isset( self::$cache[ $key ] ) ) {
            return self::$cache[ $key ];
        }

        if ( ! $key ) {
            // Not logged in.
            self::$cache[ $key ] = UserException::not_logged_in();

            return self::$cache[ $key ];
        }

        $user = get_user_by( 'id', $key );

        if ( ! $user instanceof WP_User || ! $user->exists() ) {
            self::$cache[ $key ] = UserException::not_found( $key );

            return self::$cache[ $key ];
        }

        self::$cache[ $key ] = $user;

        return self::$cache[ $key ];
    }

    /**
     * Returns current user ID.
     *
     * @since 1.3.0
     *
     * @return int User ID or 0 when no user is logged in.
     */
    public static function current_id(): int {
        return (int) get_current_user_id();
    }
}

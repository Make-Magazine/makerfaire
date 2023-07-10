/**
 * Configures the GravityKit main variable globally.
 *
 * @since 2.2
 *
 * @type   {Object}
 */
window.GravityKit = window.GravityKit || {};

/**
 * Configures the GravityMaps main variable globally.
 *
 * @since 2.2
 *
 * @type   {Object}
 */
window.GravityKit.GravityMaps = window.GravityKit.GravityMaps || {};


/**
 * Configures the GravityMaps hooks instance from WP.
 *
 * @since 2.2
 *
 * @type {(function(): Hooks)|*}
 */
window.GravityKit.GravityMaps.hooks = window.wp.hooks;
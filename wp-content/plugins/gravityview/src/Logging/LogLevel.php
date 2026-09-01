<?php
/**
 * Describes log levels.
 *
 * @package GravityKit\GravityView\Logging
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Logging;

/**
 * Describes log levels.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Logging namespace.
 */
class LogLevel {
	const EMERGENCY = 'emergency';
	const ALERT     = 'alert';
	const CRITICAL  = 'critical';
	const ERROR     = 'error';
	const WARNING   = 'warning';
	const NOTICE    = 'notice';
	const INFO      = 'info';
	const DEBUG     = 'debug';
}

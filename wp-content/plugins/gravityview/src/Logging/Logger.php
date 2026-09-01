<?php
/**
 * The Logger abstract class.
 *
 * PSR-3 inspired logging interface.
 *
 * @package GravityKit\GravityView\Logging
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Logging;

/**
 * The Logger abstract class.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Logging namespace.
 */
abstract class Logger {
	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function emergency( $message, array $context = [] ) {
		$this->log( LogLevel::EMERGENCY, $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function alert( $message, array $context = [] ) {
		$this->log( LogLevel::ALERT, $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function critical( $message, array $context = [] ) {
		$this->log( LogLevel::CRITICAL, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function error( $message, array $context = [] ) {
		$this->log( LogLevel::ERROR, $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function warning( $message, array $context = [] ) {
		$this->log( LogLevel::WARNING, $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function notice( $message, array $context = [] ) {
		$this->log( LogLevel::NOTICE, $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function info( $message, array $context = [] ) {
		$this->log( LogLevel::INFO, $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function debug( $message, array $context = [] ) {
		$this->log( LogLevel::DEBUG, $message, $context );
	}

	/**
	 * Bake the context into { } placeholders in the message.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return string The baked message;
	 */
	protected function interpolate( $message, $context ) {
		foreach ( $context as $key => $val ) {
			if ( false !== strpos( $message, "{{$key}}" ) ) {
				$message = str_replace( "{{$key}}", (string) $val, $message );
			}
		}

		return $message;
	}

	/**
	 * Logs with an arbitrary level using `do_action` and our
	 *  old action handlers.
	 *
	 * $context['data'] will be passed to the action.
	 *
	 * @param mixed  $level   The log level.
	 * @param string $message The message to log.
	 * @param array  $context The context.
	 *
	 * @return void
	 */
	abstract protected function log( $level, $message, $context );
}

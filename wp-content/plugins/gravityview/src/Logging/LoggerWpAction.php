<?php
/**
 * The WordPress action-based logger implementation.
 *
 * @package GravityKit\GravityView\Logging
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Logging;

/**
 * The WP Action Logger implementation.
 *
 * Uses the old logging stuff for now.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Logging namespace.
 */
class LoggerWpAction extends Logger {

	/**
	 * Logs with an arbitrary level using `do_action` and our
	 *  old action handlers.
	 *
	 * $context['data'] will be passed to the action.
	 *
	 * @param mixed  $level The log level.
	 * @param string $message The message to log.
	 * @param array  $context The context.
	 *
	 * @return void
	 */
	protected function log( $level, $message, $context ) {
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 3 );
		$location  = $this->interpolate( '{class}{type}{function}', $backtrace[2] );
		$message   = $this->interpolate( "[$level, $location] $message", $context );

		switch ( $level ) :
			case LogLevel::EMERGENCY:
			case LogLevel::ALERT:
			case LogLevel::CRITICAL:
			case LogLevel::ERROR:
				$action = 'error';
				break;
			case LogLevel::WARNING:
			case LogLevel::NOTICE:
			case LogLevel::INFO:
			case LogLevel::DEBUG:
				$action = 'debug';
				break;
		endswitch;

		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			/** Let's make this testable! */
			do_action(
				sprintf( 'gravityview_log_%s_test', $action ),
				$this->interpolate( $message, $context ),
				empty( $context['data'] ) ? [] : $context['data']
			);
		}

		do_action(
			sprintf( 'gravityview_log_%s', $action ),
			$this->interpolate( $message, $context ),
			empty( $context['data'] ) ? [] : $context['data']
		);
	}
}

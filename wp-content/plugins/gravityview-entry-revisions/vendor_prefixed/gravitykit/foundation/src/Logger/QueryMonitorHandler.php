<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by GravityKit on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\Logger;

use GravityKit\GravityRevisions\Foundation\ThirdParty\Monolog\Logger as MonologLogger;
use GravityKit\GravityRevisions\Foundation\ThirdParty\Monolog\Handler\AbstractProcessingHandler;

/**
 * Handler for the Query Monitor plugin.
 *
 * @see https://github.com/johnbillion/query-monitor
 */
class QueryMonitorHandler extends AbstractProcessingHandler {
	/**
	 * {@inheritdoc}
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $level  The minimum logging level at which this handler will be triggered.
	 * @param bool       $bubble Whether the messages that are handled can bubble up the stack or not.
	 *
	 * @return void
	 */
	public function __construct( $level = MonologLogger::DEBUG, $bubble = true ) { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct( $level, $bubble );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since 1.0.0
	 *
	 * @param array $record The record to process.
	 *
	 * @return void
	 */
	protected function write( array $record ) {
		$level = strtolower( $record['level_name'] );

		do_action( "qm/{$level}", $record['formatted'] );
	}
}

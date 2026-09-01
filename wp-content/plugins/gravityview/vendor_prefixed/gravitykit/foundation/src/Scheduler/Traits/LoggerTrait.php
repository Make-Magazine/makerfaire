<?php

namespace GravityKit\GravityView\Foundation\Scheduler\Traits;

use GravityKit\GravityView\Foundation\Logger\Framework as Logger;

/**
 * Logging framework for GravityKit.
 */
trait LoggerTrait {

	/**
	 * Logger object.
	 *
	 * @since 1.12.0
	 *
	 * @var Logger|null
	 */
	protected $logger;

	/**
	 * Gets Logger object.
	 *
	 * @since 1.12.0
	 *
	 * @return Logger
	 */
	public function logger(): Logger {
		if ( ! $this->logger ) {
			$this->logger = Logger::get_instance( 'gk_scheduler', 'GravityKit Scheduler' );
		}

		return $this->logger;
	}
}

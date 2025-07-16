<?php
declare(strict_types=1);

namespace WPMedia\Mixpanel;

class TrackingPlugin extends Tracking {
	/**
	 * Plugin name & version
	 *
	 * @var string
	 */
	private $plugin;

	/**
	 * Mixpanel token
	 *
	 * @var string
	 */
	private $mixpanel_token;

	/**
	 * Constructor
	 *
	 * @param string $mixpanel_token Mixpanel token.
	 * @param string $plugin         Plugin name.
	 */
	public function __construct( string $mixpanel_token, string $plugin ) {
		$options = [
			'consumer'  => 'wp',
			'consumers' => [
				'wp' => 'WPMedia\\Mixpanel\\WPConsumer',
			],
		];

		parent::__construct( $mixpanel_token, $options );

		$this->plugin         = $plugin;
		$this->mixpanel_token = $mixpanel_token;
	}

	/**
	 * Track an event in Mixpanel with plugin context
	 *
	 * @param string  $event      Event name.
	 * @param mixed[] $properties Event properties.
	 */
	public function track( string $event, array $properties ): void {
		$host = wp_parse_url( get_home_url(), PHP_URL_HOST );

		if ( ! $host ) {
			$host = '';
		}

		$defaults = [
			'domain'      => $this->hash( $host ),
			'wp_version'  => $this->get_wp_version(),
			'php_version' => $this->get_php_version(),
			'plugin'      => $this->plugin,
		];

		$properties = array_merge( $properties, $defaults );

		parent::track( $event, $properties );
	}

	/**
	 * Get the Mixpanel token
	 *
	 * @return string
	 */
	public function get_token(): string {
		return $this->mixpanel_token;
	}
}

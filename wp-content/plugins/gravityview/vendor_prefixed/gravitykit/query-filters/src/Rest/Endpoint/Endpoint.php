<?php

namespace GravityKit\GravityView\QueryFilters\Rest\Endpoint;

/**
 * Immutable value object describing a single REST endpoint that backs a ChoicesField.
 *
 * Carries the fully resolved URL plus optional method, static params, and extra headers. The
 * UI consumes the `to_array()` shape verbatim when building its endpoint callback.
 *
 * @since 2.12.0
 */
final class Endpoint {
	/**
	 * The fully qualified REST URL.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * The HTTP method, normalized to uppercase.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private string $method;

	/**
	 * Static query string parameters baked into every request.
	 *
	 * @since 2.12.0
	 *
	 * @var array<string, scalar>
	 */
	private array $params;

	/**
	 * Extra HTTP headers added to every request on top of the standard auth headers.
	 *
	 * @since 2.12.0
	 *
	 * @var array<string, string>
	 */
	private array $headers;

	/**
	 * Creates the endpoint descriptor.
	 *
	 * @since 2.12.0
	 *
	 * @param string                $url     The fully qualified REST URL.
	 * @param string                $method  The HTTP method. Defaults to `GET`.
	 * @param array<string, scalar> $params  Optional static query string parameters.
	 * @param array<string, string> $headers Optional extra request headers.
	 */
	public function __construct( string $url, string $method = 'GET', array $params = [], array $headers = [] ) {
		$this->url     = $url;
		$this->method  = strtoupper( $method );
		$this->params  = $params;
		$this->headers = $headers;
	}

	/**
	 * Returns the URL.
	 *
	 * @since 2.12.0
	 *
	 * @return string The REST URL.
	 */
	public function url(): string {
		return $this->url;
	}

	/**
	 * Returns the HTTP method.
	 *
	 * @since 2.12.0
	 *
	 * @return string The uppercase HTTP method.
	 */
	public function method(): string {
		return $this->method;
	}

	/**
	 * Returns the static parameters.
	 *
	 * @since 2.12.0
	 *
	 * @return array<string, scalar> The params.
	 */
	public function params(): array {
		return $this->params;
	}

	/**
	 * Returns the extra headers.
	 *
	 * @since 2.12.0
	 *
	 * @return array<string, string> The headers.
	 */
	public function headers(): array {
		return $this->headers;
	}

	/**
	 * Returns the descriptor shape consumed by the UI.
	 *
	 * @since 2.12.0
	 *
	 * @return array{url:string,method:string,params:array,headers:array} The serialized descriptor.
	 */
	public function to_array(): array {
		return [
			'url'     => $this->url,
			'method'  => $this->method,
			'params'  => $this->params,
			'headers' => $this->headers,
		];
	}
}

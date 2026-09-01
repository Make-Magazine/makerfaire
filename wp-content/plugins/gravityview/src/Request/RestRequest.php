<?php
/**
 * The default REST Request class.
 *
 * @package GravityKit\GravityView\Request
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Request;

/**
 * The default REST Request class.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Request namespace.
 */
class RestRequest extends Request {
	/**
	 * The WP REST request.
	 *
	 * @since 2.0
	 *
	 * @var \WP_REST_Request
	 */
	private $request;

	/**
	 * @param \WP_REST_Request $request The WordPress REST request object.
	 */
	public function __construct( \WP_REST_Request $request ) {
		$this->request = $request;
	}

	/**
	 * Retrieve paging parameters if any.
	 *
	 * @return array
	 */
	public function get_paging() {
		return [
			'paging' => [
				'page_size'    => $this->request->get_param( 'limit' ),
				'current_page' => $this->request->get_param( 'page' ),
			],
		];
	}
}

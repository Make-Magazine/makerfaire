<?php
namespace GravityKit\GravityView\GravityForms\QueryExtensions;

use GF_Query_Call;

/**
 * A GF_Query_Call whose parameters can be replaced without losing its concrete class.
 *
 * Union and join rewriting remaps a call's column parameters onto another form. The
 * columns are private on the base class, so rewriters rebuild the call; implementing
 * this contract lets them rebuild it as the same class, keeping its custom *_sql().
 *
 * @since 3.2.0
 */
interface RewritableCall {
	/**
	 * Returns a same-class call with the given parameters.
	 *
	 * @since 3.2.0
	 *
	 * @param array $parameters The replacement parameters.
	 *
	 * @return GF_Query_Call
	 */
	public function with_parameters( array $parameters ): GF_Query_Call;
}

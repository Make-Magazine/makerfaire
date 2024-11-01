<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 15-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string  $name
     * @return \GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null);
}

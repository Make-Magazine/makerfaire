<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 28-March-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection($name = null);
}

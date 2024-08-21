<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 16-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Redis\RedisManager
 * @see \GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Redis\Factory
 */
class Redis extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'redis';
    }
}

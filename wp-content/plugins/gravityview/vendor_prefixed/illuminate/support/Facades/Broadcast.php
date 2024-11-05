<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Support\Facades;

use GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @see \GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Broadcasting\Factory
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}

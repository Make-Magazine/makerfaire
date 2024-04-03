<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 28-March-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Config\Repository
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}

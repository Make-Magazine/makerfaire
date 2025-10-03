<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Support\Facades;

use GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Support\Testing\Fakes\BusFake;
use GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;

/**
 * @see \GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Bus\Dispatcher
 */
class Bus extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new BusFake);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BusDispatcherContract::class;
    }
}

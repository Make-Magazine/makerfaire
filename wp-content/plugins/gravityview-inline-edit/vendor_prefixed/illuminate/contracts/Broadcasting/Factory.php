<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 13-March-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string  $name
     * @return void
     */
    public function connection($name = null);
}

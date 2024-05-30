<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 29-May-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Broadcasting;

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

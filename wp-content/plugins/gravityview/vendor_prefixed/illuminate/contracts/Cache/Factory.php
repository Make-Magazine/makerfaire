<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 14-March-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Cache;

interface Factory
{
    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function store($name = null);
}

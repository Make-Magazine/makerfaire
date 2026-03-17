<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Validation;

interface ValidatesWhenResolved
{
    /**
     * Validate the given class instance.
     *
     * @return void
     */
    public function validate();
}

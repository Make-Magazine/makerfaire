<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Support;

interface Htmlable
{
    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml();
}

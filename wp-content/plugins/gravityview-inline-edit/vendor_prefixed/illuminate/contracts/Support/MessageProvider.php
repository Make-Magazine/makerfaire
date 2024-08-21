<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 16-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}

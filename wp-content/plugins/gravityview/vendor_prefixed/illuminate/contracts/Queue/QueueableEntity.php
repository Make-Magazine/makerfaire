<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 08-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Queue;

interface QueueableEntity
{
    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId();
}

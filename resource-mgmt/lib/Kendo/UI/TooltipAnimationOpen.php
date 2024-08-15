<?php

namespace Kendo\UI;

class TooltipAnimationOpen extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The effect that will be used for opening the Tooltip.
    * @param string $value
    * @return \Kendo\UI\TooltipAnimationOpen
    */
    public function effects($value) {
        return $this->setProperty('effects', $value);
    }

    /**
    * Defines the duration of the open animation.
    * @param float $value
    * @return \Kendo\UI\TooltipAnimationOpen
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

//<< Properties
}

?>

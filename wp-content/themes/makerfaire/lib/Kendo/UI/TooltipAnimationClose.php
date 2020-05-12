<?php

namespace Kendo\UI;

class TooltipAnimationClose extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The effect that will be used for closing the Tooltip.
    * @param string $value
    * @return \Kendo\UI\TooltipAnimationClose
    */
    public function effects($value) {
        return $this->setProperty('effects', $value);
    }

    /**
    * Defines the duration of the close animation.
    * @param float $value
    * @return \Kendo\UI\TooltipAnimationClose
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

//<< Properties
}

?>

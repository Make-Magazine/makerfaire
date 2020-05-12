<?php

namespace Kendo\UI;

class WindowAnimationClose extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The effect that will be used when the popup closes.
    * @param string $value
    * @return \Kendo\UI\WindowAnimationClose
    */
    public function effects($value) {
        return $this->setProperty('effects', $value);
    }

    /**
    * Defines the duration of the closing animation.
    * @param float $value
    * @return \Kendo\UI\WindowAnimationClose
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

//<< Properties
}

?>

<?php

namespace Kendo\UI;

class WindowAnimationOpen extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The effect that will be used when the popup opens.
    * @param string $value
    * @return \Kendo\UI\WindowAnimationOpen
    */
    public function effects($value) {
        return $this->setProperty('effects', $value);
    }

    /**
    * Defines the duration of the opening animation.
    * @param float $value
    * @return \Kendo\UI\WindowAnimationOpen
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

//<< Properties
}

?>

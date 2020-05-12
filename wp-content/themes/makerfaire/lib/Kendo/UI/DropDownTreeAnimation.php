<?php

namespace Kendo\UI;

class DropDownTreeAnimation extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param \Kendo\UI\DropDownTreeAnimationClose|array $value
    * @return \Kendo\UI\DropDownTreeAnimation
    */
    public function close($value) {
        return $this->setProperty('close', $value);
    }

    /**
    * The animation played when the suggestion popup is opened.
    * @param \Kendo\UI\DropDownTreeAnimationOpen|array $value
    * @return \Kendo\UI\DropDownTreeAnimation
    */
    public function open($value) {
        return $this->setProperty('open', $value);
    }

//<< Properties
}

?>

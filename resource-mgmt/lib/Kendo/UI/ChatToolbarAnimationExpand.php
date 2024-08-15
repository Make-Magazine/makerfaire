<?php

namespace Kendo\UI;

class ChatToolbarAnimationExpand extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the effects of the expand animation.
    * @param string $value
    * @return \Kendo\UI\ChatToolbarAnimationExpand
    */
    public function effects($value) {
        return $this->setProperty('effects', $value);
    }

    /**
    * Configures the duration of the expand animation.
    * @param float $value
    * @return \Kendo\UI\ChatToolbarAnimationExpand
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

//<< Properties
}

?>

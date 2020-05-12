<?php

namespace Kendo\UI;

class ChatToolbarAnimationCollapse extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the effects for the collapse animation of the toolbar.
    * @param string $value
    * @return \Kendo\UI\ChatToolbarAnimationCollapse
    */
    public function effects($value) {
        return $this->setProperty('effects', $value);
    }

    /**
    * Configures the duration (in milliseconds) for the collapse animation of the toolbar.
    * @param float $value
    * @return \Kendo\UI\ChatToolbarAnimationCollapse
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

//<< Properties
}

?>

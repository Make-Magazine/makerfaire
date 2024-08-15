<?php

namespace Kendo\UI;

class ChatToolbarAnimation extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the collapse animation of the toolbar.
    * @param \Kendo\UI\ChatToolbarAnimationCollapse|array $value
    * @return \Kendo\UI\ChatToolbarAnimation
    */
    public function collapse($value) {
        return $this->setProperty('collapse', $value);
    }

    /**
    * Configures the expand animation of the toolbar.
    * @param \Kendo\UI\ChatToolbarAnimationExpand|array $value
    * @return \Kendo\UI\ChatToolbarAnimation
    */
    public function expand($value) {
        return $this->setProperty('expand', $value);
    }

//<< Properties
}

?>

<?php

namespace Kendo\UI;

class ChatToolbar extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the toggle animation of the toolbar. If disabled, an animation will not be played.
    * @param boolean|\Kendo\UI\ChatToolbarAnimation|array $value
    * @return \Kendo\UI\ChatToolbar
    */
    public function animation($value) {
        return $this->setProperty('animation', $value);
    }

    /**
    * Adds ChatToolbarButton to the ChatToolbar.
    * @param \Kendo\UI\ChatToolbarButton|array,... $value one or more ChatToolbarButton to add.
    * @return \Kendo\UI\ChatToolbar
    */
    public function addButton($value) {
        return $this->add('buttons', func_get_args());
    }

    /**
    * Enables or disables the scrollable behavior of the toolbar.
    * @param boolean $value
    * @return \Kendo\UI\ChatToolbar
    */
    public function scrollable($value) {
        return $this->setProperty('scrollable', $value);
    }

    /**
    * Enables or disables the toggleable behavior of the toolbar.
    * @param boolean $value
    * @return \Kendo\UI\ChatToolbar
    */
    public function toggleable($value) {
        return $this->setProperty('toggleable', $value);
    }

//<< Properties
}

?>

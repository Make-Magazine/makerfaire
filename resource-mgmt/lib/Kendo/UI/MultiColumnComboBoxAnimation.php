<?php

namespace Kendo\UI;

class MultiColumnComboBoxAnimation extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param \Kendo\UI\MultiColumnComboBoxAnimationClose|array $value
    * @return \Kendo\UI\MultiColumnComboBoxAnimation
    */
    public function close($value) {
        return $this->setProperty('close', $value);
    }

    /**
    * The animation played when the suggestion popup is opened.
    * @param \Kendo\UI\MultiColumnComboBoxAnimationOpen|array $value
    * @return \Kendo\UI\MultiColumnComboBoxAnimation
    */
    public function open($value) {
        return $this->setProperty('open', $value);
    }

//<< Properties
}

?>

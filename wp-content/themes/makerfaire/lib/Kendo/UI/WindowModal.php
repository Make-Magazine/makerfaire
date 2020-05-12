<?php

namespace Kendo\UI;

class WindowModal extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies whether the document will stop scrolling when a modal dialog is opened. Closing the modal dialog has to restore the initial document overflow. The modal.preventScroll setting will modify the overflow rule of the document and, therefore, cannot be used together with the containment option.
    * @param boolean $value
    * @return \Kendo\UI\WindowModal
    */
    public function preventScroll($value) {
        return $this->setProperty('preventScroll', $value);
    }

//<< Properties
}

?>

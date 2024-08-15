<?php

namespace Kendo\UI;

class DialogModal extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies whether the document should stop scrolling when modal dialog is opened. Closing it should restore the initial document overflow. Note that it's not supported to have multiple dialogs with different preventScroll setting.
    * @param boolean $value
    * @return \Kendo\UI\DialogModal
    */
    public function preventScroll($value) {
        return $this->setProperty('preventScroll', $value);
    }

//<< Properties
}

?>

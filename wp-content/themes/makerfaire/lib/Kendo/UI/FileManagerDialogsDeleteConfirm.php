<?php

namespace Kendo\UI;

class FileManagerDialogsDeleteConfirm extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies width of the dialog.
    * @param float $value
    * @return \Kendo\UI\FileManagerDialogsDeleteConfirm
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The text in the dialog title bar.
    * @param string $value
    * @return \Kendo\UI\FileManagerDialogsDeleteConfirm
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * Specifies whether a close button should be rendered at the top corner of the dialog.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerDialogsDeleteConfirm
    */
    public function closable($value) {
        return $this->setProperty('closable', $value);
    }

    /**
    * Specifies the content of a Dialog.
    * @param string $value
    * @return \Kendo\UI\FileManagerDialogsDeleteConfirm
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

    /**
    * Adds FileManagerDialogsDeleteConfirmAction to the FileManagerDialogsDeleteConfirm.
    * @param \Kendo\UI\FileManagerDialogsDeleteConfirmAction|array,... $value one or more FileManagerDialogsDeleteConfirmAction to add.
    * @return \Kendo\UI\FileManagerDialogsDeleteConfirm
    */
    public function addAction($value) {
        return $this->add('actions', func_get_args());
    }

//<< Properties
}

?>

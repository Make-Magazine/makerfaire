<?php

namespace Kendo\UI;

class FileManagerDialogsUpload extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies width of the dialog.
    * @param float $value
    * @return \Kendo\UI\FileManagerDialogsUpload
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The text in the dialog title bar.
    * @param string $value
    * @return \Kendo\UI\FileManagerDialogsUpload
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * Specifies whether a close button should be rendered at the top corner of the dialog.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerDialogsUpload
    */
    public function closable($value) {
        return $this->setProperty('closable', $value);
    }

    /**
    * Specifies the content of a Dialog.
    * @param string $value
    * @return \Kendo\UI\FileManagerDialogsUpload
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

    /**
    * Adds FileManagerDialogsUploadAction to the FileManagerDialogsUpload.
    * @param \Kendo\UI\FileManagerDialogsUploadAction|array,... $value one or more FileManagerDialogsUploadAction to add.
    * @return \Kendo\UI\FileManagerDialogsUpload
    */
    public function addAction($value) {
        return $this->add('actions', func_get_args());
    }

//<< Properties
}

?>

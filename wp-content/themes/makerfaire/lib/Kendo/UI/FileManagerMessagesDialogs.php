<?php

namespace Kendo\UI;

class FileManagerMessagesDialogs extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param \Kendo\UI\FileManagerMessagesDialogsUpload|array $value
    * @return \Kendo\UI\FileManagerMessagesDialogs
    */
    public function upload($value) {
        return $this->setProperty('upload', $value);
    }

    /**
    * 
    * @param \Kendo\UI\FileManagerMessagesDialogsMoveConfirm|array $value
    * @return \Kendo\UI\FileManagerMessagesDialogs
    */
    public function moveConfirm($value) {
        return $this->setProperty('moveConfirm', $value);
    }

    /**
    * 
    * @param \Kendo\UI\FileManagerMessagesDialogsDeleteConfirm|array $value
    * @return \Kendo\UI\FileManagerMessagesDialogs
    */
    public function deleteConfirm($value) {
        return $this->setProperty('deleteConfirm', $value);
    }

    /**
    * 
    * @param \Kendo\UI\FileManagerMessagesDialogsRenamePrompt|array $value
    * @return \Kendo\UI\FileManagerMessagesDialogs
    */
    public function renamePrompt($value) {
        return $this->setProperty('renamePrompt', $value);
    }

//<< Properties
}

?>

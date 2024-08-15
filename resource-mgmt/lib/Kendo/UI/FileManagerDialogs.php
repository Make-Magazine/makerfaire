<?php

namespace Kendo\UI;

class FileManagerDialogs extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the Upload dialog - kendoDialog
    * @param \Kendo\UI\FileManagerDialogsUpload|array $value
    * @return \Kendo\UI\FileManagerDialogs
    */
    public function upload($value) {
        return $this->setProperty('upload', $value);
    }

    /**
    * Configures the Move (when drag/drop action is performed) dialog - kendoConfirm
    * @param \Kendo\UI\FileManagerDialogsMoveConfirm|array $value
    * @return \Kendo\UI\FileManagerDialogs
    */
    public function moveConfirm($value) {
        return $this->setProperty('moveConfirm', $value);
    }

    /**
    * Configures the Delete dialog - kendoConfirm
    * @param \Kendo\UI\FileManagerDialogsDeleteConfirm|array $value
    * @return \Kendo\UI\FileManagerDialogs
    */
    public function deleteConfirm($value) {
        return $this->setProperty('deleteConfirm', $value);
    }

    /**
    * Configures the Rename dialog - kendoPrompt
    * @param \Kendo\UI\FileManagerDialogsRenamePrompt|array $value
    * @return \Kendo\UI\FileManagerDialogs
    */
    public function renamePrompt($value) {
        return $this->setProperty('renamePrompt', $value);
    }

//<< Properties
}

?>

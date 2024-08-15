<?php

namespace Kendo\UI;

class FileManagerMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the localization messages for the toolbar.
    * @param \Kendo\UI\FileManagerMessagesToolbar|array $value
    * @return \Kendo\UI\FileManagerMessages
    */
    public function toolbar($value) {
        return $this->setProperty('toolbar', $value);
    }

    /**
    * Defines the localization messages for the views.
    * @param \Kendo\UI\FileManagerMessagesViews|array $value
    * @return \Kendo\UI\FileManagerMessages
    */
    public function views($value) {
        return $this->setProperty('views', $value);
    }

    /**
    * Defines the localization messages for the dialogs.
    * @param \Kendo\UI\FileManagerMessagesDialogs|array $value
    * @return \Kendo\UI\FileManagerMessages
    */
    public function dialogs($value) {
        return $this->setProperty('dialogs', $value);
    }

    /**
    * Defines the localization messages for the preview pane.
    * @param \Kendo\UI\FileManagerMessagesPreviewPane|array $value
    * @return \Kendo\UI\FileManagerMessages
    */
    public function previewPane($value) {
        return $this->setProperty('previewPane', $value);
    }

//<< Properties
}

?>

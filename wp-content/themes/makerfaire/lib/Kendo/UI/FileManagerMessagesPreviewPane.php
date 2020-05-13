<?php

namespace Kendo\UI;

class FileManagerMessagesPreviewPane extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesPreviewPane
    */
    public function noFileSelected($value) {
        return $this->setProperty('noFileSelected', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesPreviewPane
    */
    public function extension($value) {
        return $this->setProperty('extension', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesPreviewPane
    */
    public function size($value) {
        return $this->setProperty('size', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesPreviewPane
    */
    public function created($value) {
        return $this->setProperty('created', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesPreviewPane
    */
    public function modified($value) {
        return $this->setProperty('modified', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesPreviewPane
    */
    public function items($value) {
        return $this->setProperty('items', $value);
    }

//<< Properties
}

?>

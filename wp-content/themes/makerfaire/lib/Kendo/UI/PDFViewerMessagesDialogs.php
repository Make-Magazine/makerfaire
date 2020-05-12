<?php

namespace Kendo\UI;

class PDFViewerMessagesDialogs extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog|array $value
    * @return \Kendo\UI\PDFViewerMessagesDialogs
    */
    public function exportAsDialog($value) {
        return $this->setProperty('exportAsDialog', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogs
    */
    public function okText($value) {
        return $this->setProperty('okText', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogs
    */
    public function save($value) {
        return $this->setProperty('save', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogs
    */
    public function cancel($value) {
        return $this->setProperty('cancel', $value);
    }

//<< Properties
}

?>

<?php

namespace Kendo\UI;

class PDFViewerMessagesDialogsExportAsDialog extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog
    */
    public function defaultFileName($value) {
        return $this->setProperty('defaultFileName', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog
    */
    public function png($value) {
        return $this->setProperty('png', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog
    */
    public function svg($value) {
        return $this->setProperty('svg', $value);
    }

    /**
    * 
    * @param \Kendo\UI\PDFViewerMessagesDialogsExportAsDialogLabels|array $value
    * @return \Kendo\UI\PDFViewerMessagesDialogsExportAsDialog
    */
    public function labels($value) {
        return $this->setProperty('labels', $value);
    }

//<< Properties
}

?>

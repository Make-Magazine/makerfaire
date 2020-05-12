<?php

namespace Kendo\UI;

class PDFViewerMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the default file name used for Download.
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessages
    */
    public function defaultFileName($value) {
        return $this->setProperty('defaultFileName', $value);
    }

    /**
    * Specifies the localization messages of the toolbar.
    * @param \Kendo\UI\PDFViewerMessagesToolbar|array $value
    * @return \Kendo\UI\PDFViewerMessages
    */
    public function toolbar($value) {
        return $this->setProperty('toolbar', $value);
    }

    /**
    * 
    * @param \Kendo\UI\PDFViewerMessagesErrorMessages|array $value
    * @return \Kendo\UI\PDFViewerMessages
    */
    public function errorMessages($value) {
        return $this->setProperty('errorMessages', $value);
    }

    /**
    * 
    * @param \Kendo\UI\PDFViewerMessagesDialogs|array $value
    * @return \Kendo\UI\PDFViewerMessages
    */
    public function dialogs($value) {
        return $this->setProperty('dialogs', $value);
    }

//<< Properties
}

?>

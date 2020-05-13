<?php

namespace Kendo\UI;

class PDFViewerDplProcessingUpload extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the url that will receive the submitted file. The handler must accept POST requests.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingUpload
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

    /**
    * Specifies the name of the form field which is submitted to saveUrl.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingUpload
    */
    public function saveField($value) {
        return $this->setProperty('saveField', $value);
    }

//<< Properties
}

?>

<?php

namespace Kendo\UI;

class PDFViewerDplProcessing.upload extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the url that will receive the submitted file. The handler must accept POST requests.
    * @param  $value
    * @return \Kendo\UI\PDFViewerDplProcessing.upload
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

    /**
    * Specifies the name of the form field which is submitted to saveUrl.
    * @param  $value
    * @return \Kendo\UI\PDFViewerDplProcessing.upload
    */
    public function saveField($value) {
        return $this->setProperty('saveField', $value);
    }

//<< Properties
}

?>

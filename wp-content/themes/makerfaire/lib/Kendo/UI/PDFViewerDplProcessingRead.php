<?php

namespace Kendo\UI;

class PDFViewerDplProcessingRead extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the url to which the request is sent.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingRead
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

    /**
    * Specifies the page field parameter submitted to the read url. It is used in scenario with loadOnDemand when requests are sent for each page.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingRead
    */
    public function pageField($value) {
        return $this->setProperty('pageField', $value);
    }

    /**
    * Specifies the type of the request.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingRead
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * The type of result expected from the server. Used values are "json" and "jsonp". The PDFViewer expects a json to render the geometries.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingRead
    */
    public function dataType($value) {
        return $this->setProperty('dataType', $value);
    }

//<< Properties
}

?>

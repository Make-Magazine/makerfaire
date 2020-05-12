<?php

namespace Kendo\UI;

class PDFViewerDplProcessing.read extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the url to which the request is sent.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessing.read
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

    /**
    * Specifies the type of the request.
    * @param  $value
    * @return \Kendo\UI\PDFViewerDplProcessing.read
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * The type of result expected from the server. Used values are "json" and "jsonp". The PDFViewer expects a json to render the geometries.
    * @param  $value
    * @return \Kendo\UI\PDFViewerDplProcessing.read
    */
    public function dataType($value) {
        return $this->setProperty('dataType', $value);
    }

//<< Properties
}

?>

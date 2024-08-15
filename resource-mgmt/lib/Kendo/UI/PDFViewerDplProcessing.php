<?php

namespace Kendo\UI;

class PDFViewerDplProcessing extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the configuration of the jQuery.ajax to make an HTTP request to the remote service.
    * @param \Kendo\UI\PDFViewerDplProcessingRead|array $value
    * @return \Kendo\UI\PDFViewerDplProcessing
    */
    public function read($value) {
        return $this->setProperty('read', $value);
    }

    /**
    * Specifies the configuration of the jQuery.ajax to make an HTTP POST request to the remote service.
    * @param \Kendo\UI\PDFViewerDplProcessingUpload|array $value
    * @return \Kendo\UI\PDFViewerDplProcessing
    */
    public function upload($value) {
        return $this->setProperty('upload', $value);
    }

    /**
    * Specifies the download configuration.
    * @param \Kendo\UI\PDFViewerDplProcessingDownload|array $value
    * @return \Kendo\UI\PDFViewerDplProcessing
    */
    public function download($value) {
        return $this->setProperty('download', $value);
    }

    /**
    * Specifies whether read requests should be sent for each page. Note that on the server the pageField should be nullable.
    * @param boolean $value
    * @return \Kendo\UI\PDFViewerDplProcessing
    */
    public function loadOnDemand($value) {
        return $this->setProperty('loadOnDemand', $value);
    }

//<< Properties
}

?>

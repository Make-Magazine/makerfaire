<?php

namespace Kendo\UI;

class PDFViewerDplProcessingDownload extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the download action url  that will be navigated to.
    * @param string $value
    * @return \Kendo\UI\PDFViewerDplProcessingDownload
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

//<< Properties
}

?>

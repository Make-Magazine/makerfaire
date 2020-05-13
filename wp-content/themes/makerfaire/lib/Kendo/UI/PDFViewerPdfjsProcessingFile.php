<?php

namespace Kendo\UI;

class PDFViewerPdfjsProcessingFile extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the data to be passed to the pdfjs processor. Accepts blob, byte array or base64 string.
    * @param string $value
    * @return \Kendo\UI\PDFViewerPdfjsProcessingFile
    */
    public function data($value) {
        return $this->setProperty('data', $value);
    }

    /**
    * Specifies the url to be passed to the pdfjs processor.
    * @param string $value
    * @return \Kendo\UI\PDFViewerPdfjsProcessingFile
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

//<< Properties
}

?>

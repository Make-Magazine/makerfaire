<?php

namespace Kendo\UI;

class PDFViewerPdfjsProcessing extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the default file to be displayed.
    * @param string|\Kendo\UI\PDFViewerPdfjsProcessingFile|array $value
    * @return \Kendo\UI\PDFViewerPdfjsProcessing
    */
    public function file($value) {
        return $this->setProperty('file', $value);
    }

//<< Properties
}

?>

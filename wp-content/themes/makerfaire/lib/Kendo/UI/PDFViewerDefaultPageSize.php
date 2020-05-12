<?php

namespace Kendo\UI;

class PDFViewerDefaultPageSize extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param float $value
    * @return \Kendo\UI\PDFViewerDefaultPageSize
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * 
    * @param float $value
    * @return \Kendo\UI\PDFViewerDefaultPageSize
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

//<< Properties
}

?>

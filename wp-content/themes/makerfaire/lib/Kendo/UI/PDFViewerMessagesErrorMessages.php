<?php

namespace Kendo\UI;

class PDFViewerMessagesErrorMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesErrorMessages
    */
    public function notSupported($value) {
        return $this->setProperty('notSupported', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesErrorMessages
    */
    public function parseError($value) {
        return $this->setProperty('parseError', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesErrorMessages
    */
    public function notFound($value) {
        return $this->setProperty('notFound', $value);
    }

//<< Properties
}

?>

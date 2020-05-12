<?php

namespace Kendo\UI;

class PDFViewerMessagesToolbar extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbar
    */
    public function open($value) {
        return $this->setProperty('open', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbar
    */
    public function exportAs($value) {
        return $this->setProperty('exportAs', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbar
    */
    public function download($value) {
        return $this->setProperty('download', $value);
    }

    /**
    * 
    * @param \Kendo\UI\PDFViewerMessagesToolbarPager|array $value
    * @return \Kendo\UI\PDFViewerMessagesToolbar
    */
    public function pager($value) {
        return $this->setProperty('pager', $value);
    }

//<< Properties
}

?>

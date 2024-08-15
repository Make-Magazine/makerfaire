<?php

namespace Kendo\UI;

class PDFViewerToolbar extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds PDFViewerToolbarItem to the PDFViewerToolbar.
    * @param \Kendo\UI\PDFViewerToolbarItem|array,... $value one or more PDFViewerToolbarItem to add.
    * @return \Kendo\UI\PDFViewerToolbar
    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }

//<< Properties
}

?>

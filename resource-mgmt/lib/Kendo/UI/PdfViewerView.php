<?php

namespace Kendo\UI;

class PDFViewerView extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the surface type. It accepts canvas or svg. This option is supported only for DPL.
    * @param string $value
    * @return \Kendo\UI\PDFViewerView
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

//<< Properties
}

?>

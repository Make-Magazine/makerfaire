<?php

namespace Kendo\Data;

class DataSourceTransportBatch extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The odata-v4 batch endpoint to which the request is sent.If set to a function, the data source will invoke it and use the result as the URL.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Data\DataSourceTransportBatch
    */
    public function url($value) {
        return $this->setProperty('url', $value);
    }

//<< Properties
}

?>

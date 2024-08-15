<?php

namespace Kendo\UI;

class FilterCustomOperator extends \Kendo\SerializableObject {

    /**
    * The name of the custom operator.
    * @param string $value
    * @return \Kendo\UI\FilterCustomOperator
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * The displayed text in the operators drop down.
    * @param string $value
    * @return \Kendo\UI\FilterCustomOperator
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Sets the handler of the custom operator.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FilterCustomOperator
    */
    public function handler($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('handler', $value);
    }
}

?>

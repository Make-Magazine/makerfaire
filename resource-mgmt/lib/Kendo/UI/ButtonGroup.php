<?php

namespace Kendo\UI;

class ButtonGroup extends \Kendo\UI\Widget {

    public function name() {
        return 'ButtonGroup';
    }

    protected function createElement() {
        $element = new \Kendo\Html\Element('div');

        $element->attr('class', 'k-button-group');
        return $element;
    }
//>> Properties

    /**
    * Defines if the widget is initially enabled or disabled. By default, it is enabled.
    * @param boolean $value
    * @return \Kendo\UI\ButtonGroup
    */
    public function enable($value) {
        return $this->setProperty('enable', $value);
    }

    /**
    * Defines the initially selected Button (zero based index).
    * @param float $value
    * @return \Kendo\UI\ButtonGroup
    */
    public function index($value) {
        return $this->setProperty('index', $value);
    }

    /**
    * Defines the selection type.
    * @param string $value
    * @return \Kendo\UI\ButtonGroup
    */
    public function selection($value) {
        return $this->setProperty('selection', $value);
    }

    /**
    * Adds ButtonGroupItem to the ButtonGroup.
    * @param \Kendo\UI\ButtonGroupItem|array,... $value one or more ButtonGroupItem to add.
    * @return \Kendo\UI\ButtonGroup
    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }

    /**
    * Sets the select event of the ButtonGroup.
    * Fires when a Button is selected.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ButtonGroup
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }


//<< Properties
}

?>

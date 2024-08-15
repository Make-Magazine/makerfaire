<?php

namespace Kendo\UI;

class Filter extends \Kendo\UI\Widget {
    public function name() {
        return 'Filter';
    }
//>> Properties

    /**
    * If set to true the filter will display a button which when clicked will apply filtering over the datasource.
    * @param boolean $value
    * @return \Kendo\UI\Filter
    */
    public function applyButton($value) {
        return $this->setProperty('applyButton', $value);
    }

    /**
    * An object which represents a filter expression which the kendo.data.DataSource can use to filter the data.
    * @param  $value
    * @return \Kendo\UI\Filter
    */
    public function expression($value) {
        return $this->setProperty('expression', $value);
    }

    /**
    * If set to true the filter will visualize the filter expression that will be applied to the datasource.
    * @param boolean $value
    * @return \Kendo\UI\Filter
    */
    public function expressionPreview($value) {
        return $this->setProperty('expressionPreview', $value);
    }

    /**
    * Adds FilterField to the Filter.
    * @param \Kendo\UI\FilterField|array,... $value one or more FilterField to add.
    * @return \Kendo\UI\Filter
    */
    public function addField($value) {
        return $this->add('fields', func_get_args());
    }

    /**
    * Defines the value of the logical operator at the root level of the filter expression.
    * @param string $value
    * @return \Kendo\UI\Filter
    */
    public function mainLogic($value) {
        return $this->setProperty('mainLogic', $value);
    }

    /**
    * The text messages displayed in the filter. Use it to customize or localize the filter messages.
    * @param \Kendo\UI\FilterMessages|array $value
    * @return \Kendo\UI\Filter
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * The text of the filter operators displayed in the filter.
    * @param \Kendo\UI\FilterOperators|array $value
    * @return \Kendo\UI\Filter
    */
    public function operators($value) {
        return $this->setProperty('operators', $value);
    }

    /**
    * Sets the change event of the Filter.
    * Fired when the generated filter expression is changed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Filter
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }


//<< Properties

    /**
    * Sets the data source of the Filter.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\Filter
    */
    public function dataSource($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }
        return $this->setProperty('dataSource', $value);
    }
}

?>

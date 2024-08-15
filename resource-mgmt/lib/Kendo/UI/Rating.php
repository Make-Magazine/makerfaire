<?php

namespace Kendo\UI;

class Rating extends \Kendo\UI\Widget {
    public function name() {
        return 'Rating';
    }

    protected function createElement() {
        return new \Kendo\Html\Element('input', true);
    }
//>> Properties

    /**
    * The value from which the Rating items will be rendered.
    * @param float $value
    * @return \Kendo\UI\Rating
    */
    public function min($value) {
        return $this->setProperty('min', $value);
    }

    /**
    * The value to which the Rating items will be rendered.
    * @param float $value
    * @return \Kendo\UI\Rating
    */
    public function max($value) {
        return $this->setProperty('max', $value);
    }

    /**
    * Specifies the selection behavior. The available options are:*continuous - all items, starting from the first one, are marked as selected. *single - a single item is marked as selected.
    * @param string $value
    * @return \Kendo\UI\Rating
    */
    public function selection($value) {
        return $this->setProperty('selection', $value);
    }

    /**
    * Specifies the precision with which an item is selected. The available options are:*item - rate by selecting the whole item. *half - rate by selecting half of the item or the whole item.
    * @param string $value
    * @return \Kendo\UI\Rating
    */
    public function precision($value) {
        return $this->setProperty('precision', $value);
    }

    /**
    * The Rating displays the value of the item through the title attribute when it is hovered.If tooltip is set to false, the widget will not display the value of the items when hovering over them.The tooltips are not visible when the Rating is disabled.
    * @param boolean $value
    * @return \Kendo\UI\Rating
    */
    public function tooltip($value) {
        return $this->setProperty('tooltip', $value);
    }

    /**
    * The Rating displays a label by default that shows the current value out of the max value 3 / 5. If the widget does not have a selected value, the label will not be displayed initially and will be toggled after an item is selected.If label is set to false, the widget will not display the label.
    * @param boolean|\Kendo\UI\RatingLabel|array $value
    * @return \Kendo\UI\Rating
    */
    public function label($value) {
        return $this->setProperty('label', $value);
    }

    /**
    * If the option is set, the widget will automatically select the specified item when the Rating receives focus and no previous value has been set.
    * @param float $value
    * @return \Kendo\UI\Rating
    */
    public function selectValueOnFocus($value) {
        return $this->setProperty('selectValueOnFocus', $value);
    }

    /**
    * Sets the itemTemplate option of the Rating.
    * Specifies the template which is used for rendering the items of the Rating.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Rating
    */
    public function itemTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('itemTemplate', $value);
    }

    /**
    * Sets the itemTemplate option of the Rating.
    * Specifies the template which is used for rendering the items of the Rating.
    * @param string $value The template content.
    * @return \Kendo\UI\Rating
    */
    public function itemTemplate($value) {
        return $this->setProperty('itemTemplate', $value);
    }

    /**
    * Sets the selectedTemplate option of the Rating.
    * Specifies the template which is used for rendering the selected state of the items.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Rating
    */
    public function selectedTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('selectedTemplate', $value);
    }

    /**
    * Sets the selectedTemplate option of the Rating.
    * Specifies the template which is used for rendering the selected state of the items.
    * @param string $value The template content.
    * @return \Kendo\UI\Rating
    */
    public function selectedTemplate($value) {
        return $this->setProperty('selectedTemplate', $value);
    }

    /**
    * Sets the hoveredTemplate option of the Rating.
    * Specifies the template which is used for rendering the hovered state of the items.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Rating
    */
    public function hoveredTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('hoveredTemplate', $value);
    }

    /**
    * Sets the hoveredTemplate option of the Rating.
    * Specifies the template which is used for rendering the hovered state of the items.
    * @param string $value The template content.
    * @return \Kendo\UI\Rating
    */
    public function hoveredTemplate($value) {
        return $this->setProperty('hoveredTemplate', $value);
    }

    /**
    * If set to false, the Rating will be: disabled and will not allow the user to change its state.; excluded from the tab order and not receiving focus. or will not submit its value if part of a form..
    * @param boolean $value
    * @return \Kendo\UI\Rating
    */
    public function enabled($value) {
        return $this->setProperty('enabled', $value);
    }

    /**
    * If set to true, the Rating will: be in readonly state and will not allow the user to change its state.; be included in the tab order and able to receive focus. or submit data if part of a form..
    * @param boolean $value
    * @return \Kendo\UI\Rating
    */
    public function readonly($value) {
        return $this->setProperty('readonly', $value);
    }

    /**
    * Specifies the initial value of the Rating.If a greater value than the max option is used, then the max value will be set.If a value less than the min option is used, then the min value will be set.
    * @param float $value
    * @return \Kendo\UI\Rating
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Sets the change event of the Rating.
    * Fires when the value of the Rating is changed through user interaction.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Rating
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the select event of the Rating.
    * Fires when an item is selected through user interaction.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Rating
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

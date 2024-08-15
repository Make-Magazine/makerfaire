<?php

namespace Kendo\UI;

class FilterField extends \Kendo\SerializableObject {
//>> Properties

    /**
    * A value which will be initially set as a filter value when an expression item for the specific field is created.
    * @param  $value
    * @return \Kendo\UI\FilterField
    */
    public function defaultValue($value) {
        return $this->setProperty('defaultValue', $value);
    }

    /**
    * Sets the editorTemplate option of the FilterField.
    * The template which the widget will use to create the field editor.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FilterField
    */
    public function editorTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('editorTemplate', $value);
    }

    /**
    * Sets the editorTemplate option of the FilterField.
    * The template which the widget will use to create the field editor.
    * @param string $value The template content.
    * @return \Kendo\UI\FilterField
    */
    public function editorTemplate($value) {
        return $this->setProperty('editorTemplate', $value);
    }

    /**
    * The format of the value displayed in the preview.
    * @param string $value
    * @return \Kendo\UI\FilterField
    */
    public function previewFormat($value) {
        return $this->setProperty('previewFormat', $value);
    }

    /**
    * Defines a value which will be visualized instead of the data feild name.
    * @param string $value
    * @return \Kendo\UI\FilterField
    */
    public function label($value) {
        return $this->setProperty('label', $value);
    }

    /**
    * A value matching a field name from the datasource model.
    * @param string $value
    * @return \Kendo\UI\FilterField
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * Defines the value type of the field.
    * @param string $value
    * @return \Kendo\UI\FilterField
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

//<< Properties

    public function editorTemplateHandler($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('editorTemplate', $value);
    }

    /**
    * The text of the filter operators displayed in the filter.
    * @param \Kendo\UI\FilterOperators|array $value
    * @return \Kendo\UI\Filter
    */
    public function operators($value) {
        return $this->setProperty('operators', $value);
    }
}

?>

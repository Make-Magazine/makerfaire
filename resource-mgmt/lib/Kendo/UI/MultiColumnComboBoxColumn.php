<?php

namespace Kendo\UI;

class MultiColumnComboBoxColumn extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the field for the column.
    * @param string $value
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function field($value) {
        return $this->setProperty('field', $value);
    }

    /**
    * Defines the text of the column title in the header.
    * @param string $value
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * Sets the template option of the MultiColumnComboBoxColumn.
    * Renders a template for the column.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the MultiColumnComboBoxColumn.
    * Renders a template for the column.
    * @param string $value The template content.
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Sets the headerTemplate option of the MultiColumnComboBoxColumn.
    * Renders a template for the column header.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function headerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the MultiColumnComboBoxColumn.
    * Renders a template for the column header.
    * @param string $value The template content.
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function headerTemplate($value) {
        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * The width of the column. Numeric values are treated as pixels.
    * @param string|float $value
    * @return \Kendo\UI\MultiColumnComboBoxColumn
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>

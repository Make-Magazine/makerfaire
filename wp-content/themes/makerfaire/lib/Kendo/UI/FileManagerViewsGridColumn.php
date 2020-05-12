<?php

namespace Kendo\UI;

class FileManagerViewsGridColumn extends \Kendo\SerializableObject {
//>> Properties

    /**
    * If set to true the column value will be HTML-encoded before it is displayed. If set to false the column value will be displayed as is. By default the column value is HTML-encoded.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function encoded($value) {
        return $this->setProperty('encoded', $value);
    }

    /**
    * The field to which the column is bound. The value of this field is displayed in the column's cells during data binding. Only columns that are bound to a field can be sortable or filterable. The field name should be a valid Javascript identifier and should contain only alphanumeric characters (or "$" or "_"), and may not start with a digit.
    * @param string $value
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function field($value) {
        return $this->setProperty('field', $value);
    }

    /**
    * Sets the headerTemplate option of the FileManagerViewsGridColumn.
    * The template which renders the column header content. By default the value of the title column option is displayed in the column header cell.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function headerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the FileManagerViewsGridColumn.
    * The template which renders the column header content. By default the value of the title column option is displayed in the column header cell.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function headerTemplate($value) {
        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * If set to true the user can click the column header and sort the grid by the column field when sorting is enabled. If set to false sorting will be disabled for this column.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function sortable($value) {
        return $this->setProperty('sortable', $value);
    }

    /**
    * Sets the template option of the FileManagerViewsGridColumn.
    * If set to true the user can click the column header and sort the grid by the column field when sorting is enabled. If set to false sorting will be disabled for this column.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the FileManagerViewsGridColumn.
    * If set to true the user can click the column header and sort the grid by the column field when sorting is enabled. If set to false sorting will be disabled for this column.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The text that is displayed in the column header cell. If not set the field is used.
    * @param string $value
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * The text that is displayed in the column header cell. If not set the field is used.
    * @param float $value
    * @return \Kendo\UI\FileManagerViewsGridColumn
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>

<?php

namespace Kendo\UI;

class GridColumnCommandItem extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The CSS class applied to the command button.
    * @param string $value
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function className($value) {
        return $this->setProperty('className', $value);
    }

    /**
    * Sets the click option of the GridColumnCommandItem.
    * The JavaScript function executed when the user clicks the command button. The function receives a jQuery Event as an argument.The function context (available via the this keyword) will be set to the grid instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * The class for the web font icon of the button. When it is defined as an object it allows to customize the web font icon for the "edit", "update" and "cancel" command buttons.
    * @param string|\Kendo\UI\GridColumnCommandItemIconClass|array $value
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function iconClass($value) {
        return $this->setProperty('iconClass', $value);
    }

    /**
    * The name of the command. The built-in commands are "edit" and "destroy". Can be set to a custom value.
    * @param string $value
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * Sets the template option of the GridColumnCommandItem.
    * The template of the command column.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the GridColumnCommandItem.
    * The template of the command column.
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The text displayed by the command button and the "cancel", "edit" and "update" texts of the edit command. If not set the name option is used as the button text.
    * @param string|\Kendo\UI\GridColumnCommandItemText|array $value
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Sets the visible option of the GridColumnCommandItem.
    * The JavaScript function executed on initialization of the row which will determine whether the command button will be visible. The function receives a the data item object for the row as an argument.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\GridColumnCommandItem
    */
    public function visible($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visible', $value);
    }

//<< Properties
}

?>

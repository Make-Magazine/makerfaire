<?php

namespace Kendo\UI;

class TreeListEditable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Enables the drag-and-drop UI of rows between parents.
    * @param boolean $value
    * @return \Kendo\UI\TreeListEditable
    */
    public function move($value) {
        return $this->setProperty('move', $value);
    }

    /**
    * Sets the template option of the TreeListEditable.
    * The template which renders the popup editor.The template has to contain elements whose name HTML attribute is set to the name of the editable field. In this way, the TreeList recognizes the field to which it has to bind the each editor. Alternatively, use MVVM bindings for binding HTML elements to data item fields.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\TreeListEditable
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the TreeListEditable.
    * The template which renders the popup editor.The template has to contain elements whose name HTML attribute is set to the name of the editable field. In this way, the TreeList recognizes the field to which it has to bind the each editor. Alternatively, use MVVM bindings for binding HTML elements to data item fields.
    * @param string $value The template content.
    * @return \Kendo\UI\TreeListEditable
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Configures the Kendo UI Window instance which is used when the TreeList edit mode is set to popup. For more information, refer to the configuration API of the Window.
    * @param  $value
    * @return \Kendo\UI\TreeListEditable
    */
    public function window($value) {
        return $this->setProperty('window', $value);
    }

    /**
    * The editing mode to use. The supported editing modes are "inline", "popup" and "incell".
    * @param string $value
    * @return \Kendo\UI\TreeListEditable
    */
    public function mode($value) {
        return $this->setProperty('mode', $value);
    }

//<< Properties
}

?>

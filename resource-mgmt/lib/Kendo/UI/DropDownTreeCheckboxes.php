<?php

namespace Kendo\UI;

class DropDownTreeCheckboxes extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Indicates whether checkboxes of child items should get checked when the checkbox of a parent item is checked. This also enables tri-state checkboxes with an indeterminate state.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTreeCheckboxes
    */
    public function checkChildren($value) {
        return $this->setProperty('checkChildren', $value);
    }

    /**
    * Sets the name attribute of the checkbox inputs. That name will be posted to the server.
    * @param string $value
    * @return \Kendo\UI\DropDownTreeCheckboxes
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * Sets the template option of the DropDownTreeCheckboxes.
    * The template which renders the checkboxes. Can be used to allow posting of additional information along the TreeView checkboxes.The fields which can be used in the template are: item - the data item of the given node or treeview - the TreeView options.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTreeCheckboxes
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the DropDownTreeCheckboxes.
    * The template which renders the checkboxes. Can be used to allow posting of additional information along the TreeView checkboxes.The fields which can be used in the template are: item - the data item of the given node or treeview - the TreeView options.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTreeCheckboxes
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

//<< Properties
}

?>

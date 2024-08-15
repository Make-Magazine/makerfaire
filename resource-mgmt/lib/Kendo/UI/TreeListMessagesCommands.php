<?php

namespace Kendo\UI;

class TreeListMessagesCommands extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the text of the Cancel button that discards the changes during editing.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function canceledit($value) {
        return $this->setProperty('canceledit', $value);
    }

    /**
    * Defines the text of the Add new record button that adds new data rows.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function create($value) {
        return $this->setProperty('create', $value);
    }

    /**
    * Defines the text of the Add child record button that adds new child data rows.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function createchild($value) {
        return $this->setProperty('createchild', $value);
    }

    /**
    * Defines the text of the Delete button that deletes a data row.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function destroy($value) {
        return $this->setProperty('destroy', $value);
    }

    /**
    * Defines the text of the Edit button that shows the editable fields for the row.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function edit($value) {
        return $this->setProperty('edit', $value);
    }

    /**
    * Defines the text of the Save Changes button that saves modifed data rows.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function save($value) {
        return $this->setProperty('save', $value);
    }

    /**
    * Allows the customization of the placeholder text in the treelist search panel.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function search($value) {
        return $this->setProperty('search', $value);
    }

    /**
    * Defines the text of the Cancel Changes button that cancels all data modifications.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function cancel($value) {
        return $this->setProperty('cancel', $value);
    }

    /**
    * Defines the text of the Export to Excel button that exports the widget data in spreadsheet format.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function excel($value) {
        return $this->setProperty('excel', $value);
    }

    /**
    * Defines the text of the Export to PDF button that exports the widget data in PDF format.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * Defines the text of the Update button that applies the changes during editing.
    * @param string $value
    * @return \Kendo\UI\TreeListMessagesCommands
    */
    public function update($value) {
        return $this->setProperty('update', $value);
    }

//<< Properties
}

?>

<?php

namespace Kendo\UI;

class FileManagerContextMenu extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds FileManagerContextMenuItem to the FileManagerContextMenu.
    * @param \Kendo\UI\FileManagerContextMenuItem|array,... $value one or more FileManagerContextMenuItem to add.
    * @return \Kendo\UI\FileManagerContextMenu
    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }

    /**
    * Sets the close option of the FileManagerContextMenu.
    * Fires before a sub menu or the ContextMenu gets closed. You can cancel this event to prevent closure.  ContextMenu Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerContextMenu
    */
    public function close($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('close', $value);
    }

    /**
    * Sets the open option of the FileManagerContextMenu.
    * Fires before a sub menu or the ContextMenu gets opened. You can cancel this event to prevent opening the sub menu. ContextMenu Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerContextMenu
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the activate option of the FileManagerContextMenu.
    * Fires when a sub menu or the ContextMenu gets opened and its animation finished. ContextMenu Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerContextMenu
    */
    public function activate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('activate', $value);
    }

    /**
    * Sets the deactivate option of the FileManagerContextMenu.
    * Fires when a sub menu or the ContextMenu gets closed and its animation finished. ContextMenu Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerContextMenu
    */
    public function deactivate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('deactivate', $value);
    }

    /**
    * Sets the select option of the FileManagerContextMenu.
    * Fires when a menu item gets selected. ContextMenu Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerContextMenu
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

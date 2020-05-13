<?php

namespace Kendo\UI;

class FileManagerToolbar extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds FileManagerToolbarItem to the FileManagerToolbar.
    * @param \Kendo\UI\FileManagerToolbarItem|array,... $value one or more FileManagerToolbarItem to add.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }

    /**
    * Sets the click option of the FileManagerToolbar.
    * Fires when the user clicks a command button. Toolbar Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * Sets the close option of the FileManagerToolbar.
    * Fires when the SplitButton's popup closes. Toolbar Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function close($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('close', $value);
    }

    /**
    * Sets the open option of the FileManagerToolbar.
    * Fires when the Split Button's popup opens. Toolbar Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the toggle option of the FileManagerToolbar.
    * Fires when the user changes the checked state of a toggle button. Toolbar Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function toggle($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('toggle', $value);
    }

    /**
    * Sets the overflowClose option of the FileManagerToolbar.
    * Fires when the overflow popup container is about to close. Toolbar Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function overflowClose($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('overflowClose', $value);
    }

    /**
    * Sets the overflowOpen option of the FileManagerToolbar.
    * Fires when the overflow popup container is about to open. Toolbar Events.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManagerToolbar
    */
    public function overflowOpen($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('overflowOpen', $value);
    }

//<< Properties
}

?>

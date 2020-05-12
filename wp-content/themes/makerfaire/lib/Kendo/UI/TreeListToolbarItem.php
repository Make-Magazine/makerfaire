<?php

namespace Kendo\UI;

class TreeListToolbarItem extends \Kendo\SerializableObject {
    function __construct($name = null) {
        $this->name($name);
    }
//>> Properties

    /**
    * Sets the click option of the TreeListToolbarItem.
    * The click handler of the toolbar command. Used for custom toolbar commands.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * A class name that will be rendered inside the toolbar button. When you set this option, the TreeList renders an additional span element inside the toolbar button which has a class name set to the option value. This approach allows you to display an icon inside your custom toolbar commands.
    * @param string $value
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function imageClass($value) {
        return $this->setProperty('imageClass', $value);
    }

    /**
    * The name of the toolbar command. Can be either a built-in ("create", "excel", or "pdf") or a custom string. The name is output in the HTML as a value of the data-command attribute of the button.
    * @param string $value
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * The text that is displayed by the command button. If not set, the TreeList will use the name` option as the button text instead.
    * @param string $value
    * @return \Kendo\UI\TreeListToolbarItem
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

//<< Properties
}

?>

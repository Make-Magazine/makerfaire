<?php

namespace Kendo\UI;

class BreadcrumbItem extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the navigation link's url of the item (rendered if navigation is true).
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function href($value) {
        return $this->setProperty('href', $value);
    }

    /**
    * Defines the text of the item.
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Defines the icon to be rendered.
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function icon($value) {
        return $this->setProperty('icon', $value);
    }

    /**
    * Defines the item classes (the li element).
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function itemClass($value) {
        return $this->setProperty('itemClass', $value);
    }

    /**
    * Defines the link classes (the a element).
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function linkClass($value) {
        return $this->setProperty('linkClass', $value);
    }

    /**
    * Defines the icon classes (the span element).
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function iconClass($value) {
        return $this->setProperty('iconClass', $value);
    }

    /**
    * Defines whether to show the icon. Default value is true for rootItem and false for item.
    * @param boolean $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function showIcon($value) {
        return $this->setProperty('showIcon', $value);
    }

    /**
    * Defines whether to show the text. Default value is false for rootItem and true for item.
    * @param boolean $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function showText($value) {
        return $this->setProperty('showText', $value);
    }

    /**
    * The item type.
    * @param string $value
    * @return \Kendo\UI\BreadcrumbItem
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

//<< Properties
}

?>

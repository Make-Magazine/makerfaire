<?php

namespace Kendo\UI;

class ScrollViewItem extends \Kendo\SerializableObject {
    public function createContentElement() {
        $content = $this->getProperty('content');

        if ($content) {
            $contentElement = new \Kendo\Html\Element('div');

            $contentElement->html($content)
                           ->attr('data-role','page');

            return $contentElement;
        }
    }

//>> Properties

    /**
    * Specifies the content of the item
    * @param string $value
    * @return \Kendo\UI\ScrollViewItem
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

//<< Properties

/**
    * Starts output bufferring. Any following markup will be set as the content of the ScrollViewItem.
    */
    public function startContent() {
        ob_start();
    }

    /**
    * Stops output bufferring and sets the preceding markup as the content of the ScrollViewItem.
    */
    public function endContent() {
        $this->content(ob_get_clean());
    }
    /**
    * Adds one or more \Kendo\UI\ScrollViewItem.
    * @param \Kendo\UI\ScrollViewItem|array,... $value
    * @return \Kendo\UI\ScrollView    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }
}

?>

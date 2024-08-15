<?php

namespace Kendo\UI;

class PDFViewerMessagesToolbarPager extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function first($value) {
        return $this->setProperty('first', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function previous($value) {
        return $this->setProperty('previous', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function next($value) {
        return $this->setProperty('next', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function last($value) {
        return $this->setProperty('last', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function of($value) {
        return $this->setProperty('of', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function page($value) {
        return $this->setProperty('page', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerMessagesToolbarPager
    */
    public function pages($value) {
        return $this->setProperty('pages', $value);
    }

//<< Properties
}

?>

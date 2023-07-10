<?php

/**
 * Prepare single share buttons' settings for a display position.
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 8.5
 */
abstract class ESSB_Share_Position {
    
    /**
     * The active position key
     * @var string
     */
    public $position = '';
    
    public $networks = array();
 
    /**
     * There are specific positions that do not support the automatic settings. 
     * 
     * @return boolean
     */
    public function dontHaveAutomaticOptions() {
        return ($this->position == 'mobile' || $this->position == 'sharebottom' || 
            $this->position == 'sharebar' || $this->position == 'sharepoint');
    }
}
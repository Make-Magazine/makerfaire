<?php

/** 
 * Single post sharing information
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 7.1
 */
class ESSB_Single_Post_Information extends ESSB_Post_Information {

    /**
     * Create instance of a single sharing post information
     * 
     * @param string $post_id
     */
    public function __construct ($post_id = null) {        
        $this->load($post_id);
    }
}


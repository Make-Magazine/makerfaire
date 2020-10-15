<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mf-sharing-cards
 *
 * @author rich.haynie
 */
class mf_sharing_cards {

    public $project_photo;
    public $project_short;
    public $project_title;
    public $canonical_url;

    public function set_values() {
        global $project_photo;
        $project_photo = $this->project_photo;
        // Photo Image
        add_filter('wpseo_opengraph_image', 'change_wpseo_opengraph_image');

        function change_wpseo_opengraph_image($url) {
            global $project_photo;
            $url = $project_photo;
            return $url;
        }

        add_filter('wpseo_twitter_image', 'change_wpseo_twitter_image');

        function change_wpseo_twitter_image($url) {
            global $project_photo;
            $url = $project_photo;
            return $url;
        }

        // Card Type
        add_filter('wpseo_twitter_card_type', 'change_wpseo_twitter_card_type');

        function change_wpseo_twitter_card_type($url) {
            $url = 'summary_large_image';
            return $url;
        }

        global $project_short;
        $project_short = $this->project_short;
        add_filter('wpseo_opengraph_desc', 'change_wpseo_metadesc');

        function change_wpseo_metadesc($text) {
            global $project_short;      
            $text = $project_short;
            return $text;
        }

        global $project_title;
        $project_title = $this->project_title;
        add_filter('wpseo_title', 'change_wpseo_title');

        function change_wpseo_title($title) {
            global $project_title;
            $title = $project_title;
            return $title;
        }

        global $project_title;
        $project_title = $this->project_title;
        add_filter('wpseo_opengraph_title', 'change_wpseo_opengraph_title');

        function change_wpseo_opengraph_title($title) {
            global $project_title;
            $title = $project_title;
            return $title;
        }

        global $canonical_url;
        $canonical_url = $this->canonical_url;
        add_filter('wpseo_canonical', 'change_wpseo_canonical');

        function change_wpseo_canonical($url) {
            global $canonical_url;
            $url = $canonical_url;
            return $url;
        }

       
        add_filter('wpseo_opengraph_url', 'change_wpseo_opengraph_url');
        function change_wpseo_opengraph_url($url) {
            global $canonical_url;
            $url = $canonical_url;
            return $url;
        }
        
        return true;
//   
//    add_filter( 'wpseo_twitter_image', 'change_wpseo_twitter_image' );
//    function change_wpseo_twitter_image( $url ) {
//    global $project_photo;
//    $url = $project_photo;
//    return $url;
//    }
//    // Card Type
//    add_filter( 'wpseo_twitter_card_type', 'change_wpseo_twitter_card_type' );
//    function change_wpseo_twitter_card_type( $url ) {
//    $url = 'summary_large_image';
//    return $url;
//    }
//    
//    
//    
//    
//    // Description
//    global $project_short;
//    $project_short = $entry['16'];
//    function change_wpseo_metadesc( $project_short ) {
//        global $project_short;
//        $text = $project_short;
//        return $text;
//    }
//    add_filter( 'essb_opengraph_desc', 'change_wpseo_metadesc' );
// 
//    //Website
//    global $project_website;
//    $project_website = $entry['27'];
//    
//    
//    //Video
//    global $project_video;
//    $project_video = $entry['32'];
//    
//    //Title
//    global $project_title;
//        $project_title = (string)$entry['151'];
//    $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
//    function change_wpseo_title( $title ) {
//    global $project_title;
//    $title = $project_title;
//    return $title;
//    }
//    add_filter( 'wpseo_title', 'change_wpseo_title' );
//   
//    //Url
//    global $canonical_url;
//    global $wp;
//     $canonical_url = home_url( $wp->request ) . '/' ;    
//    function change_wpseo_canonical( $url ) {
//    global $canonical_url;
//    $url = $canonical_url;
//    return $url;
//    }
//    add_filter( 'wpseo_canonical', 'change_wpseo_canonical' );
    }

}

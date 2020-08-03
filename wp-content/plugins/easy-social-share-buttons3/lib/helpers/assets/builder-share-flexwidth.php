<?php

if (!function_exists('essb_rs_css_build_flexwidth_buttons')) {
    function essb_rs_css_build_flexwidth_buttons($width, $button_width) {
        $main_css = 'essb_flex_'.$width.'_'.$button_width;
        
        $snippet = '';
        
        if ($button_width != '') {
            $snippet .= '.essb_links.essb_width_flex.'.$main_css.' li { width: '.$button_width.'% !important; }';
        }
        else {
            $snippet .= '.essb_links.essb_width_flex.'.$main_css.' li { width: auto !important; }';
        }
        
        if ($width != '') {
            $snippet .= '.essb_links.essb_width_flex.'.$main_css.' li.essb_totalcount_item { width: '.$width.'% !important; }';
            $snippet .= '.essb_links.essb_width_flex.'.$main_css.' li.essb_totalcount_item .essb_totalcount { float: left; }';
            $snippet .= '.essb_links.essb_width_flex.'.$main_css.' li.essb_totalcount_item .essb_totalcount.essb_t_r_big { float: right; }';
        }
        
        return $snippet;
    }
}
<?php

namespace WPGMZA;

class MarkerRatingWidget extends DOMDocument
{
	public function __construct()
	{
		
	}
	
	public static function enqueueStyles()
	{
		//wp_enqueue_script('wpgmza-rateit', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/rateit/jquery.rateit.min.js');
		//wp_enqueue_style('wpgmza-rateit', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/rateit/rateit.css');
	}
}

<?php

namespace WPGMZA;

#[\AllowDynamicProperties]
class MarkerGalleryItem
{
	public $attachment_id;
	public $url;
	public $thumbnail;
	
	public function __construct($data)
	{
		global $wpgmza;

		foreach($data as $key => $value){
			$this->{$key} = $value;
		}
		
		// NB: This forces thumbnail regeneration following a typo which prevented the thumbnail from being used
		if(isset($this->thunbnail)){
			unset($this->thunbnail);
			unset($this->thumbnail);
		}
		
		if(empty($this->thumbnail)){
			$default = 'medium';
			if(!empty($wpgmza) && !empty($wpgmza->settings)){
				if(!empty($wpgmza->settings->gallery_item_source_size)){
					$default = $wpgmza->settings->gallery_item_source_size;
				}
			}
			
			/* Developer Hook (Filter) - Modify default gallery thumbnail size */
			$size = apply_filters('wpgmza_marker_gallery_item_thumbnail_size', $default);
			
			$src = wp_get_attachment_image_src($this->attachment_id, $size);
			
			if($src)
				$this->thumbnail = $src[0];
			
			if(!$this->thumbnail)
				$this->thumbnail = $this->url;
		}
	}
}

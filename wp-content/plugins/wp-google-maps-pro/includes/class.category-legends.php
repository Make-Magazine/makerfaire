<?php

namespace WPGMZA;

class CategoryLegends extends Factory {
	private $_document;
	private $_map;

	public function __construct(Map $map){
		global $wpgmza;
		
		$this->_map = $map;
		$this->_document = new DOMDocument();

		$this->load();
	}
	
	public function __get($name){
		if(isset($this->{"_$name"})){
			return $this->{"_$name"};
		}
		
		switch($name){
			case "document":
				return $this->_document;
				break;

			case "html":
				return $this->_document->html;
				break;
		}
	}

	private function load(){
		$this->document->loadHTML('<div class="wpgmza-category-legends"></div>');
		$this->wrapper = $this->document->querySelector(".wpgmza-category-legends");

		$this->heading = $this->document->createElement("label");
		$this->heading->appendText(__("Legends", "wp-google-maps"));
		$this->heading->addClass('wpgmza-category-legends-heading');

		$this->wrapper->appendChild($this->heading);

		$this->build($this->map->categoryTree, $this->wrapper);
	}

	private function build($node, $element){
		global $wpgmza;
		
		if(empty($node->children)){
			return;
		}
		
		$ul = $this->document->createElement('ul');
		$ul->addClass('wpgmza-category-legend-group');
		
		foreach($node->children as $child){
			$li		= $this->document->createElement("li");
			$label	= $this->document->createElement("label");
			
			$label->import($child->category_name);

			$iconUrl = false;
			if(!empty($child->category_icon->url)){
				$iconUrl = $child->category_icon->url;
			} else {
				/* Get dfault from map */
				if(!empty($this->map->default_marker)){
					$iconUrl = $this->map->default_marker;
				} else {
					$iconUrl = Marker::DEFAULT_ICON;						
				}
			}

			if(!empty($iconUrl)){
				$img = $this->document->createElement("img");
				$img->setAttribute('src', $iconUrl);

				$li->appendChild($img);
			}
			
			$li->appendChild($label);
			$li->addClass("wpgmza-category-legend wpgmza-category-legend-{$child->id}");
			
			$li->setAttribute('data-category-id', $child->id);

			$ul->appendChild($li);

			$this->build($child, $li);
		}
		
		$element->appendChild($ul);
	}
}
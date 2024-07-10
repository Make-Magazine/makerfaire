<?php

namespace WPGMZA\MarkerListing;

class Panel extends \WPGMZA\MarkerListing{
	public function __construct($map_id){
		\WPGMZA\MarkerListing::__construct($map_id);
	}
	
	public function getAjaxResponse($request){
		global $wpgmza;

		$response = $this->getRecords($request);

		$document = new \WPGMZA\DOMDocument();
		$document->loadPHPFile($wpgmza->internalEngine->getTemplate('marker-listings/panel-item.html.php', WPGMZA_PRO_DIR_PATH));

		$template = $document->querySelector("body>*");
		$template->remove();

		if(!$this->map->isDirectionsEnabled()){
			foreach($template->querySelectorAll(".wpgmza_gd") as $el){
				$el->remove();
			}
		}

		foreach($response->data as $marker){
			$item = $template->cloneNode(true);
			
			if(isset($request['map_id'])){
				$item->setAttribute('mapid', $request['map_id']);
			}
				
			$item->setAttribute('id', "wpgmza_marker_{$marker->id}");
			$item->setAttribute('mid', $marker->id);
			

			$img = $item->querySelector('.wpgmza_map_image');
			$imageWrap = $item->querySelector('.wpgmza-gallery-container');
			if(!empty($marker->pic)) {
				$img->setAttribute('src', $marker->pic);
			} else {
				$imageWrap->remove();
			}
				
			$title = $item->querySelector('.wpgmza-title');
			$title->setAttribute('title', $marker->title);
			$title->appendText($marker->title);
			
			$address = $item->querySelector(".wpgmza-address");
			$address->appendText($marker->address);

			$this->appendListingItem($document, $item, $marker);
		}
		
		$response->html = $document->saveInnerBody();
		
		unset($response->data);
		
		return $response;
	}
}
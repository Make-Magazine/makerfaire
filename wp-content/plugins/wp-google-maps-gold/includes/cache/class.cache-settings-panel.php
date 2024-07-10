<?php

namespace WPGMZA;

class CacheSettingsPanel extends DOMDocument {
	public function __construct() {
		global $wpgmza;

		DOMDocument::__construct();
		
		if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && $wpgmza->goldAddOn->checkCoreAtlasNovus()){
			/* TODO: V6 should only use this approach and not be blocked into an IF statement */
			// V9.0.0 + Atlas Novus
			$this->loadPHPFile($wpgmza->internalEngine->getTemplate('caching-settings-panel.html.php', WPGMZA_GOLD_DIR_PATH));
		} else {
			$this->loadPHPFile(plugin_dir_path(__DIR__) . '../html/caching-settings-panel.html.php');
		}

		if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && !empty($wpgmza->goldAddOn->cache)){
			$cacheReport = $wpgmza->goldAddOn->cache->report();

			$reportHtml = array();
			$reportHtml[] = "<div><strong>Your data has not been cached</strong></div>";
			$reportHtml[] = "<div>The cache will automatically be generated when your maps are loaded, or you can preload the cache if preferred.</div>";

			if(!empty($cacheReport)){
				$reportHtml = array();

				foreach($cacheReport as $instance => $report){
					$title = ucwords($instance);
					$improvement = number_format((($report->runtime - $report->filetime) / $report->runtime) * 100, 2) . '%';
					$improvement .= " (" . ($report->runtime > 0 ? number_format($report->runtime / 1000, 2) : 0) . "s → " . ($report->filetime ? number_format($report->filetime / 1000, 2) : 0) . "s)";

					if(empty($report->fromFile)){
						$improvement = "Caching in Progress";
					}

					$reportHtml[] = "<div class='cache-dataset'>";
					$reportHtml[] = 	"<div><strong>{$title}:</strong></div>";

					$reportHtml[] = 	"<div>- Total Records: {$report->total}</div>";
					$reportHtml[] = 	"<div>- Improvement: {$improvement}</div>";
					$reportHtml[] = 	"<div>- Last Update: {$report->updated}</div>";
					$reportHtml[] = "</div>";
				}

			}

			$reportHtml = implode("", $reportHtml);
			if($container = $this->querySelector('.cache-info')){
				$container->import($reportHtml);
			}

		}
	}
}

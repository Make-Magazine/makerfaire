<?php

namespace WPGMZA;

if(!defined('ABSPATH'))
	return;

wpgmza_require_once( wpgmza_gold_get_basic_dir() . 'includes/class.script-loader.php' );

class GoldScriptLoader extends ScriptLoader
{
	public function __construct()
	{
		$this->scriptsFileLocation = plugin_dir_path(WPGMZA_GOLD_FILE) . 'js/v8/scripts.json';
	}
	
	protected function getLibraryScripts()
	{
		return array(
			'kdTree'		=> 'lib/kdTree-min.js',
			'cheap-ruler'	=> 'lib/cheap-ruler.js'
		);
	}
	
	protected function getScanDirectories()
	{
		return array(
			plugin_dir_path(WPGMZA_GOLD_FILE) . 'js/v8' => plugin_dir_url(WPGMZA_GOLD_FILE) . 'js/v8'
		);
	}
	
	public function getPluginScripts()
	{
		global $wpgmza;
		
		if(!$wpgmza->isInDeveloperMode())
		{
			$dir = plugin_dir_path(WPGMZA_GOLD_FILE);
			
			$combined = 'js/v8/wp-google-maps-gold.combined.js';
			$minified = 'js/v8/wp-google-maps-gold.min.js';
			
			$src = $minified;
			
			$minified_file_exists = file_exists($dir . $minified);
			
			if($minified_file_exists)
				$delta = filemtime($dir . $combined) - filemtime($dir . $minified);
			
			if(!$minified_file_exists || $delta > 0)
				$src = $combined;
			
			$scripts = array('wpgmza-gold' => (object)array(
				'src' => $src,
				'dependencies' => array_merge( array('wpgmza'), array_keys($this->getLibraryScripts()) )
			));
		}
		else
		{
			$scripts = (array)json_decode(file_get_contents($this->scriptsFileLocation));
		}
		
		return $scripts;
	}
	
	public function buildCombinedFile()
	{
		global $wpgmza;
		
		$order = $this->getCombineOrder();
		
		$combined = array();
		$dest = plugin_dir_path(WPGMZA_GOLD_FILE) . 'js/v8/wp-google-maps-gold.combined.js';
		
		foreach($order as $file)
		{
			if(preg_match('/\.(combined|min)\.js$/', $file))
				continue;
			
			$src = plugin_dir_path(__DIR__) . $file;
			
			$contents = "\r\n// $file\r\n" . file_get_contents($src);
			$combined[] = $contents;
		}
		
		$combined = implode("\r\n", $combined);
		
		if(file_exists($dest) && md5(file_get_contents($dest)) == md5($combined))
			return;	// No changes, leave the file alone. Updating the file would cause the combined script to be newer than the minified script
		
		file_put_contents($dest, $combined);
	}
	
	public function enqueueStyles($forceLoad = false)
	{
		global $wpgmza;

		$version_string = WPGMZA_GOLD_VERSION;

		// Bridging stylesheet
		if(is_admin() && !empty($wpgmza->getCurrentPage())){
			wp_enqueue_style('wpgmza_gold_admin', plugin_dir_url(WPGMZA_GOLD_FILE) . 'css/admin.css', array(), $version_string);
		}
	}
	
	public function enqueueScripts($forceLoad = false)
	{
		global $wpgmza;
		
		$version_string = WPGMZA_GOLD_VERSION;

		$scriptArgs = apply_filters('wpgmza-get-scripts-arguments', array());
		
		// Dependencies
		$this->dependencies = $this->getLibraryScripts();
		
		foreach($this->dependencies as $handle => $src)
		{
			$fullpath = plugin_dir_url(WPGMZA_GOLD_FILE) . $src;
			
			wp_enqueue_script($handle, $fullpath, array(), $version_string, $scriptArgs);
		}
		
		// Scripts
		$this->scripts = $this->getPluginScripts();
		
		foreach($this->scripts as $handle => $script)
		{
			$fullpath = plugin_dir_url(__DIR__) . $script->src;
			
			wp_enqueue_script($handle, $fullpath, $script->dependencies, $version_string, $scriptArgs);
		}
	}
}
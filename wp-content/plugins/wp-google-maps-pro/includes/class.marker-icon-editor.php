<?php
namespace WPGMZA;

#[\AllowDynamicProperties]
class MarkerIconEditor {
    const STORAGE_CORE_DIR = "wp-google-maps";
	const STORAGE_ICON_DIR = "icons";
	const STORAGE_TEMPLATES_DIR = "templates";

    /**
     * Constructor
     */
	public function __construct(){

        $this->_document = new DOMDocument();
        
        $this->prepareStorage();
        $this->getIcons();
        $this->load();
    }

    /**
     * Simple accessor
     * 
     * @param string $name The name of the property you are trying to access
     * 
     * @return mixed
     */
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

    /**
     * Populate the document, builds the basic structures which can then be more fully controlled by the JS module which accompanies this class
     * 
     * @return void
     */
    private function load(){
        global $wpgmza;
        $this->document->loadPHPFile($wpgmza->internalEngine->getTemplate("marker-icon-editor.html.php", WPGMZA_PRO_DIR_PATH));

        if(!empty($this->icons)){
            /* Template Icon Loop */
            if(!empty($this->icons->templates)){
                $templateIconContainer = $this->document->querySelector('.wpgmza-marker-icon-editor-templates');
                $hasTemplateHistory = false;

                if($templateIconContainer){
                    foreach ($this->icons->templates as $icon) {
                        $iconWrapper = $this->document->createElement('div');
                        $iconWrapper->addClass('shape-template');
                        $iconWrapper->setAttribute('data-src', $icon->url);
                        $iconWrapper->setAttribute('data-type', $icon->type);

                        $iconWrapper->setAttribute('data-template', json_encode($icon->template));
        
                        $iconImg = $this->document->createElement('img');
                        $iconImg->setAttribute('src', $icon->url);
        
                        $iconWrapper->appendChild($iconImg);
        
                        $templateIconContainer->appendChild($iconWrapper);
        
                        if($icon->type === 'storage'){
                            $hasTemplateHistory = true;
                        }
                    }

                    if($hasTemplateHistory){
                        $templateIconContainer->addClass('has-history');
                    }
                }

                
            }

            /* Static Icon Loop */
            if(!empty($this->icons->static)){
                $staticIconContainer = $this->document->querySelector('.wpgmza-marker-icon-editor-list');
                $hasStaticHistory = false;

                if($staticIconContainer){
                    foreach ($this->icons->static as $icon) {
                        $iconWrapper = $this->document->createElement('div');
                        $iconWrapper->addClass('base-icon');
                        $iconWrapper->setAttribute('data-src', $icon->url);
                        $iconWrapper->setAttribute('data-type', $icon->type);
        
                        $iconImg = $this->document->createElement('img');
                        $iconImg->setAttribute('src', $icon->url);
        
                        $iconWrapper->appendChild($iconImg);
        
                        $staticIconContainer->appendChild($iconWrapper);
        
                        if($icon->type === 'storage'){
                            $hasStaticHistory = true;
                        }
                    }

                    if($hasStaticHistory){
                        $staticIconContainer->addClass('has-history');
                    }
                }

                
            }
        }

        if($wpgmza->internalEngine->isLegacy()){
            $colorInputs = $this->document->querySelectorAll('.wpgmza-color-input');
            foreach($colorInputs as $colorInput){
                $colorInput->removeClass('wpgmza-color-input');
                $colorInput->setAttribute('type', 'color');
                $colorInput->addClass('wpgmza-legacy-color-input');

            }
        }
        

        /* Developer Hook (Action) - Alter output of the marker icon creator/editor, passes DOMDocument for mutation */
        do_action("wpgmza_marker_icon_editor_created", $this->document);
    }
    
    /**
     * Get icons from the folder
     * 
     * This simply populates a local array with paths which are then hydrated in the load method into usable HTML
     * 
     * @return void
     */
    private function getIcons(){
        $this->icons = (object) array(
            'templates' => array(),
            'static' => array()
        );

        $paths = $this->getIconPaths();

        foreach ($paths as $type => $path) {
            try{
    			$files = \list_files($path);
                if(!empty($files)){
                    $base = rtrim(WPGMZA_PRO_DIR_URL, "/") . "/images/markers/";
                    if($type === 'storage'){
                        $base = $this->getStoragePath(true) . "/";
                        $files = array_reverse($files);

                        $files = array_splice($files, 0, 100, true); // Max history
                    }

                    foreach ($files as $file) {
                        $name = basename($file);

                        $extension = explode(".", $name);
                        $extension = !empty($extension) ? array_pop($extension) : false;
                        if(empty($extension) || $extension === 'json'){
                            /* Probably a template config, skip it */
                            continue;
                        }

                        $config = (object) array(
                            "url" => "{$base}{$name}",
                            "type" => $type
                        );

                        $template = $this->getIconTemplate($path, $name);
                        if(!empty($template)){
                            $config->template = $template;
                            $this->icons->templates[] = $config;
                        } else {
                            $this->icons->static[] = $config;
                        }
                    }
                }
                
            } catch (\Exception $ex){
                // Do nothing, say nothing
            } catch (\Exception $err){
                // Bad times but who cares
            }
        }
    }

    /**
     * Get and check if this icon has a template linked to it
     * 
     * Template based icons will only use the icon image as a preview for the grid, but the actual 
     * icon will be generated based on the template JSON
     * 
     * It will return the template object, if it has a matching file
     * 
     * @param string $path 
     * @param string $filename
     * 
     * @return object
     */
    private function getIconTemplate($path, $filename){
        if(!empty($path) && !empty($filename)){
            $filename = explode('.', $filename);
            if(!empty($filename)){
                $filename = array_shift($filename);

                $path = rtrim($path, "/");
                $path = "{$path}/" . self::STORAGE_TEMPLATES_DIR . "/{$filename}.json";
                
                if(file_exists($path)){
                    /* We have a JSON file for this icon, so we need to parse it out */
                    try{
                        $json = file_get_contents($path);
                        $json = json_decode($json);

                        if(!empty($json)){
                            return $json;
                        }
                    } catch(\Exception $ex){

                    } catch(\Error $err){

                    }
                }

            }
        }

        return false;
    }

    /**
     * Get the paths where base icons are stored
     * 
     * This is two fold, user created and system based paths
     * 
     * @return array
     */
    private function getIconPaths(){
        $paths = array();
        $paths['system'] = rtrim(WPGMZA_PRO_DIR_PATH, "/") . "/images/markers/";

        $storagePath = $this->getStoragePath();
        if(!empty($storagePath) && file_exists($storagePath)){
            // Only add if it exists, this keeps processing time down, although minimally
            $paths['storage'] = $storagePath;
        }
        return $paths;
    }

    /**
     * Get the path for user storage icons
     * 
     * @param bool $asUrl Get a URL instead of a DIR path
     * 
     * @return string
     */
    private function getStoragePath($asUrl = false){
        $uploadDir = wp_upload_dir();
		if (!empty($uploadDir['basedir'])){
            if($asUrl && !empty($uploadDir['baseurl'])){
    			$uploadDir = $uploadDir['baseurl'];
            } else {
                $uploadDir = $uploadDir['basedir'];
            }

    		return implode("/", 
    			array(
    				$uploadDir,
    				self::STORAGE_CORE_DIR,
    				self::STORAGE_ICON_DIR
    			)
    		);
    	}
    	return false;
    }

    /**
     * Creates the WP Media storage directory if it does not already exist 
     * 
     * @return void
     */
    private function prepareStorage(){
        $path = $this->getStoragePath();

        /* Check base path and create it */
        $basePath = str_replace("/" . self::STORAGE_ICON_DIR, "", $path);
        if(!file_exists($basePath)){
        	wp_mkdir_p($basePath);
        }

        /* Check icon path and create it */
        if(!file_exists($path)){
        	wp_mkdir_p($path);
        }

        /* Check templates sub-path and create it */
        $templatesPath = $path . "/" . self::STORAGE_TEMPLATES_DIR;
        if(!file_exists($templatesPath)){
        	wp_mkdir_p($templatesPath);
        }
    }
}
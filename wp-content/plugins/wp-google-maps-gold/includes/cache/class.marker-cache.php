<?php 

namespace WPGMZA;

class MarkerCache implements GenericCache {
    const BATCH_SIZE = 300;

    public $cache;
    public $queryData;

    private $fromFile;

    /**
     * Constructor
     */
    public function __construct(){
        $this->cache = false;
        $this->fromFile = false;
        $this->queryData = (object) array(
            'maps' => false,
            'filter' => false,
            'batches' => 0,
            'data' => array()
        );
    }

    /**
     * Localize the data
     * 
     * @return void
     */
    public function localize(){
        $data = $this->getMarkers();
        if(!empty($data)){
            if(is_array($data) || is_object($data)){
                return $data;
            }
        }
        return false;
    }

    /**
     * Get markers from the cache
     * 
     * @return object
     */
    public function getMarkers(){
        if(!empty($this->cache)){
            return $this->cache;
        }

        /* Get the marker data and cache it */
        $data = $this->load();
        if(empty($data)){
            /* Data is empty, meaning cache file is invalid */
            $data = $this->query();
            if(!empty($data)){
                /* Store the cache data */
                $this->store($data);
            }
        }

        if(!empty($data)){
            /* We have data, let's localize it */
            $this->cache = $data;
            return $data;
        } 
        return false;
    }

    /**
     * Store data to a file
     * 
     * @return void
     */
    public function store($data){
        if(is_array($data) || is_object($data)){
            $this->write(json_encode($data));
        }
    }

    /**
     * Load data from file
     * 
     * @return object
     */
    public function load(){
        $data = $this->read();
        if(!empty($data)){
            try{
                $json = json_decode($data);
                $this->fromFile = true;
                

                return $json;
            } catch (\Exception $ex){

            } catch (\Error $err){

            }
        }
        return false;
    }

    /**
     * Clear the cache file
     * 
     * @return void
     */
    public function clear(){
        $this->write(false);
    }

    /**
     * Preload the cache
     * 
     * @return void
     */
    public function preload(){
        $data = $this->getMarkers();
    }

    /**
     * Provides a report on the current cache data
     * 
     * @return object
     */
    public function report(){
        $time = (object) array('start' => floor(microtime(true) * 1000));
        $data = $this->localize();

        $time->end = floor(microtime(true) * 1000);
        $time->delta = $time->end - $time->start;

        if(!empty($data->info)){
            $data->info->filetime = $time->delta;
            $data->info->fromFile = $this->fromFile;
            return $data->info;
        }
        return false;
    }

    /**
     * Database query to populate a file store
     * 
     * @return object
     */
    public function query(){
        $this->queryData->maps = $this->queryMaps();
        
        $time = (object) array('start' => floor(microtime(true) * 1000));

        foreach($this->queryData->maps as $row){
            $this->queryData->filter = MarkerFilter::createInstance();
            $this->queryData->filter->limit = self::BATCH_SIZE;
            $this->queryData->filter->offset = 0;
            $this->queryData->filter->map_id = intval($row->id);

            $this->queryBatch();
        }

        $time->end = floor(microtime(true) * 1000);
        $time->delta = $time->end - $time->start;

        if(!empty($this->queryData->data)){
            return (object) array(
                'info' => (object) array(
                    'updated' => date("Y-m-d H:i"),
                    'batchSize' => self::BATCH_SIZE,
                    'batches' => $this->queryData->batches,
                    'total' => count($this->queryData->data),
                    'runtime' => $time->delta
                ),
                'data' => $this->queryData->data
            );
        }
        return false;
    }

    /**
     * Query a marker batch
     * 
     * Batching improves the reliability of the caching module by doing actions in steps 
     * instead of doing it all at once
     * 
     * @return void
     */
    public function queryBatch(){
        $results = $this->queryData->filter->getFilteredMarkers();
        if(!empty($results)){
            if(!isset($this->queryData->data[$this->queryData->filter->map_id])){
                $this->queryData->data[$this->queryData->filter->map_id] = array();
            }

            $classImplementsJsonSerializableCache = array();
            foreach($results as $marker){
                if($marker instanceof Marker) {
                    $json = $marker->jsonSerialize();
                    foreach($json as $key => $value) {
                        if(!is_object($value)) {
                            continue;
                        }
                        
                        if(!isset($classImplementsJsonSerializableCache[$key])) {
                            $reflection = new \ReflectionClass($value);
                            $classImplementsJsonSerializableCache[$key] = $reflection->implementsInterface('JsonSerializable');
                        }
                        
                        if(!$classImplementsJsonSerializableCache[$key]){
                            continue;
                        }
                        
                        $json[$key] = $value->jsonSerialize();
                    }
                    
                    $this->queryData->data[$this->queryData->filter->map_id][] = $json;
                }
            }

            $this->queryData->batches += 1;

            /* Continue to next batch */
            $this->queryData->filter->offset += $this->queryData->filter->limit;
            $this->queryBatch();
        }
    }

    /**
     * Get all the map ID's in the system, so that the cache
     * is isolated only to these maps
     * 
     * This was introduced later to ensure icon SQL is applied during the caching process 
     * 
     * @return array
     */
    private function queryMaps(){
        global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		
        $maps = array();
        if(!empty($WPGMZA_TABLE_NAME_MAPS)){
    		$maps = $wpdb->get_results("SELECT id FROM $WPGMZA_TABLE_NAME_MAPS WHERE active = 0");
        }

        return $maps;
    }

    /**
     * Get the file path to the cache file
     * 
     * @return string
     */
    private function path(){
        return WPGMZA_CACHE_DIR . 'markers.json';
    }

    /**
     * Write to the cache
     * 
     * @return void
     */
    private function write($data){
        $file = $this->path();
        try{
            if(empty($data)){
                $data = "";
            }

            file_put_contents($file, $data);
        } catch(\Exception $ex){

        } catch (\Error $err){

        }
    }

    /**
     * Read from the cache
     * 
     * @return string
     */
    private function read(){
        $file = $this->path();
        try{
            $fileHandle = fopen($file, "r");
            $data = fgets($fileHandle);
            fclose($fileHandle);

            return $data;
        } catch(\Exception $ex){

        } catch(\Error $err){

        }
        return false;
    }
}
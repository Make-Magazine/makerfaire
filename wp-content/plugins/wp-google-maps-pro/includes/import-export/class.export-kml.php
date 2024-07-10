<?php
namespace WPGMZA;

use SimpleXMLElement;

/**
 * KML Exporter
 * 
 * Note: Markers, Polygons and Polylines are supported, however, rectangles, circles, point labels, heatmaps, and image overlays cannot be stored in
 * KML wrapper in a standard way at this stage. We could work around this, however, it would mean losing persistence between shape types on import/export operations
 * 
 * For the time being, we will avoid non-standard KML structures as this would break spec and serves a selfish purpose. This can be considered a stay in your lane situation,
 * respect the spec and adapt as it changes in the future. 
 *
 * @since 9.0.0
 * 
*/
class ExportKML extends Export{
    const TYPE_MARKER = "markers";
    const TYPE_POLYGON = "polygons";
    const TYPE_POLYLINE = "polylines";

	/**
	 * Constructor
	*/
	public function __construct($types, $map = false, $applyStyles = false) {
        $this->types = is_array($types) ? $types : array();
        $this->map = !empty($map) ? intval($map) : false;
        $this->applyStyles = !empty($applyStyles) ? true : false;

        $this->data = array();
        $this->xmlContent = "";
    }

    /**
	 * Generates and dowload the KMLfiles
	 *
	 * @return void
	*/
	public function download() {
        $this->getData();
        
        try{
            $this->buildXML();
        } catch (\Exception $ex){
            /* Nothing to do */
        } catch (\Error $err){
            /* Silence, I kill you */
        }

        $this->formatXML();

        $id = !empty($this->map) ? $this->map : "all";
        $filename = "wpgmza_export_{$id}.kml";

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/kml");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Expires: 0");
        header("Pragma: public");

        $handle = @fopen('php://output', 'w');
        fwrite($handle, $this->xmlContent);
        fclose($handle);
    }

    /**
     * Build the XML data (KML namespaces) for the output
     * 
     * This can essentially be used to catch issues with the XML output 
     * 
     * It loops through the data stored in the instance and generates each block type 
     * 
     * @return void
     */
    public function buildXML(){
        if(!class_exists("SimpleXMLElement")){
            return;
        }

        $this->xmlDocument = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><kml xmlns=\"http://www.opengis.net/kml/2.2\"></kml>");
        $document = $this->xmlDocument->addChild('Document');
        $document->addChild("name", "Map ID: " . $this->map);

        /* Build the styles for each marker, polygon, and polyline - Separated so that it can be toggled off if needed */
        $this->buildStyles($document);

        /* Push the data */
        if(!empty($this->data)){
            foreach($this->data as $type => $data){
                if(!empty($data)){
                    $folder = $document->addChild("Folder");
                    $folder->addChild("name", ucwords($type));

                    foreach($data as $row){
                        switch($type){
                            case self::TYPE_MARKER:
                                /* Note lng then lat in KML data for some reason */
                                $latLngAlt = implode(',', 
                                    array(
                                        $row->lng,
                                        $row->lat,
                                        0
                                    )
                                );

                                $placemark = $folder->addChild("Placemark");
                                if(!empty($row->title)){
                                    $placemark->addChild("name", $row->title);
                                }

                                if(!empty($row->description)){
                                    $placemark->addChild("description", $row->description);
                                }

                                $point = $placemark->addChild("Point");
                                $coords = $point->addChild("coordinates", $latLngAlt);
                               
                                break;
                            case self::TYPE_POLYGON:
                                if(empty($row->polydata)){
                                    /* Skip this one */
                                    break;
                                }

                                $latLngAltGroup = array();
                                $polydata = json_decode($row->polydata);

                                /* Patch for older poly data, which is not stored in spatial data types */
                                if(empty($polydata) && !empty($row->polydata)){
                                    /* We have polydata, but it was not JSON */
                                    $polydata = array();
                                    $rawPoints = explode("),(", $row->polydata);
                                    foreach($rawPoints as $rawPoint){
                                        $rawPoint = str_replace(array("(", ")"), "", $rawPoint);
                                        $splitPoint = explode(",", $rawPoint);
                                        if(count($splitPoint) >= 2){
                                            $polydata[] = (object) array(
                                                "lng" => floatval($splitPoint[1]),
                                                "lat" => floatval($splitPoint[0])
                                            );
                                        }
                                    }
                                }

                                foreach($polydata as $point){
                                    /* Note lng then lat in KML data for some reason */
                                    $latLngAlt = array(
                                        $point->lng,
                                        $point->lat,
                                        0
                                    );

                                    $latLngAltGroup[] = implode(",", $latLngAlt);
                                }

                                $latLngAltGroup = implode("\n ", $latLngAltGroup);
                                
                                $placemark = $folder->addChild("Placemark");
                                if(!empty($row->title)){
                                    $placemark->addChild("name", $row->title);
                                }

                                if(!empty($row->description)){
                                    $placemark->addChild("description", $row->description);
                                }

                                $styleKey = "style-{$type}-{$row->id}";
                                if(in_array($styleKey, $this->styleKeys)){
                                    $placemark->addChild("styleUrl", "#{$styleKey}");
                                }

                                $polygon = $placemark->addChild("Polygon");
                                $boundary = $polygon->addChild("outerBoundaryIs");

                                $ring = $boundary->addChild("LinearRing");
                                $tessellate = $ring->addChild("tessellate", "1");
                                $coords = $ring->addChild("coordinates", $latLngAltGroup);     
                                break;
                            case self::TYPE_POLYLINE:
                                if(empty($row->polydata)){
                                    /* Skip this one */
                                    break;
                                }

                                $latLngAltGroup = array();
                                $polydata = json_decode($row->polydata);

                                /* Patch for older poly data, which is not stored in spatial data types */
                                if(empty($polydata) && !empty($row->polydata)){
                                    /* We have polydata, but it was not JSON */
                                    $polydata = array();
                                    $rawPoints = explode("),(", $row->polydata);
                                    foreach($rawPoints as $rawPoint){
                                        $rawPoint = str_replace(array("(", ")"), "", $rawPoint);
                                        $splitPoint = explode(",", $rawPoint);
                                        if(count($splitPoint) >= 2){
                                            $polydata[] = (object) array(
                                                "lng" => floatval($splitPoint[1]),
                                                "lat" => floatval($splitPoint[0])
                                            );
                                        }
                                    }

                                }

                                foreach($polydata as $point){
                                    /* Note lng then lat in KML data for some reason */
                                    $latLngAlt = array(
                                        $point->lng,
                                        $point->lat,
                                        0
                                    );

                                    $latLngAltGroup[] = implode(",", $latLngAlt);
                                }

                                $latLngAltGroup = implode("\n ", $latLngAltGroup);

                                $placemark = $folder->addChild("Placemark");
                                if(!empty($row->polyname)){
                                    $placemark->addChild("name", $row->polyname);
                                }

                                $styleKey = "style-{$type}-{$row->id}";
                                if(in_array($styleKey, $this->styleKeys)){
                                    $placemark->addChild("styleUrl", "#{$styleKey}");
                                }

                                $polyline = $placemark->addChild("LineString");
                                $tessellate = $polyline->addChild("tessellate", "1");
                                $coords = $polyline->addChild("coordinates", $latLngAltGroup);
                                break;
                        }
                    }
                }
            }
        }

        $this->xmlContent = $this->xmlDocument->asXML();
    }
    
    /**
     * Build the style blocks for the document
     * 
     * This only runs if the applyStyles property is enabled, which means it can be wp_ajax_toggle_auto_updates(  )
     * 
     * @param \SimpleXMLElement $document The parent document 
     * 
     * @return void
     */
    public function buildStyles($document){
        $this->styleKeys = array();
        if($this->applyStyles){
            if(!empty($this->data)){
                foreach($this->data as $type => $data){
                    if(!empty($data)){
                        foreach($data as $row){
                            $styleKey = "style-{$type}-{$row->id}";

                            switch($type){
                                case self::TYPE_MARKER:
                                    /* No way to consistently export marker icons at this stage */
                                    break;
                                case self::TYPE_POLYGON:
                                    /* Normal */
                                    $normalStyle = $document->addChild('Style');
                                    $normalStyle->addAttribute("id", "{$styleKey}-normal");

                                    $normalLineStyle = $normalStyle->addChild("LineStyle");
                                    $normalLineStyle->addChild("color", $this->buildColor($row->linecolor));
                                    $normalLineStyle->addChild("width", $row->linethickness);

                                    $normalPolyStyle = $normalStyle->addChild("PolyStyle");
                                    $normalPolyStyle->addChild("color", $this->buildColor($row->fillcolor, $row->opacity));
                                    $normalPolyStyle->addChild("fill", "1");
                                    $normalPolyStyle->addChild("outine", "1");

                                    /* Hover */
                                    $highlightStyle = $document->addChild('Style');
                                    $highlightStyle->addAttribute("id", "{$styleKey}-highlight");

                                    $hoverLineStyle = $highlightStyle->addChild("LineStyle");
                                    $hoverLineStyle->addChild("color", $this->buildColor($row->ohlinecolor));
                                    $hoverLineStyle->addChild("width", $row->linethickness);

                                    $hoverPolyStyle = $highlightStyle->addChild("PolyStyle");
                                    $hoverPolyStyle->addChild("color", $this->buildColor($row->ohfillcolor, $row->ohopacity));
                                    $hoverPolyStyle->addChild("fill", "1");
                                    $hoverPolyStyle->addChild("outine", "1");

                                    /* State Map */
                                    $this->styleKeys[] = $styleKey;
                                    break;
                                case self::TYPE_POLYLINE:
                                    /* Normal */
                                    $normalStyle = $document->addChild('Style');
                                    $normalStyle->addAttribute("id", "{$styleKey}-normal");

                                    $normalLineStyle = $normalStyle->addChild("LineStyle");
                                    $normalLineStyle->addChild("color", $this->buildColor($row->linecolor, $row->opacity));
                                    $normalLineStyle->addChild("width", $row->linethickness);

                                    /* Hover */
                                    $highlightStyle = $document->addChild('Style');
                                    $highlightStyle->addAttribute("id", "{$styleKey}-highlight");

                                    $hoverLineStyle = $highlightStyle->addChild("LineStyle");
                                    $hoverLineStyle->addChild("color", $this->buildColor($row->linecolor, $row->opacity));
                                    $hoverLineStyle->addChild("width", $row->linethickness);
                                    
                                    /* State Map */
                                    $this->styleKeys[] = $styleKey;
                                    break;
                            }

                            if(in_array($styleKey, $this->styleKeys)){
                                $styleMap = $document->addChild("StyleMap");
                                $styleMap->addAttribute("id", $styleKey);

                                $pairs = array("normal", "highlight");

                                foreach($pairs as $key){
                                    $pair = $styleMap->addChild("Pair");
                                    $pair->addChild("key", $key);
                                    $pair->addChild("styleUrl", "#{$styleKey}-{$key}");

                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Using DomDocument, we convert the Simple XML document 
     * 
     * This allows us to format the indendations correctly without much effort
     * 
     * Note: This is not efficient, in that it spins up a second document, however, this is simple. Converting all logic to use DomDocument would improve performance, but 
     * it is not worth it at this stage, and to be fair, the performance impact should not be noticeable in most cases 
     * 
     * @return void
     */
    public function formatXML(){
        if(class_exists("DOMDocument")){
            if(!empty($this->xmlContent)){
                try{
                    $document = new \DOMDocument("1.0");
                    $document->preserveWhiteSpace = false;
                    $document->formatOutput = true;
                    $document->loadXML($this->xmlContent);

                    $formatted = $document->saveXML();
                    if(!empty($formatted)){
                        $this->xmlContent = $formatted;
                    }
                } catch (\Exception $ex){
                    /* Ignore */
                } catch (\Error $err){
                    /* Ignore */
                }
            }
        }
    }

    /**
     * Convert a hex an opacity to a KML valid hex
     * 
     * This hex is the compiled value of ABGR, which means we convert the hex in this order:
     * - Hex to RGB
     * - Opacity to A
     * - Compile ABGR
     * - ABGR to Hex
     * 
     * @param string $hex The hex as stored in the database
     * @param float $opacity The opacity of the color 
     * 
     * @return string
     */
    public function buildColor($hex, $opacity = false){
        $rgba = (object) array(
            'r' => 0,
            'g' => 0,
            'b' => 0,
            'a' => 255
        );

        if(!empty($opacity)){
            $opacity = floatval($opacity);
            $opacity = $opacity * 255;
            $rgba->a = $opacity;
        }

        $hex = str_replace("#", "", trim($hex));
        $parts = str_split($hex, strlen($hex) / 3);

        
        $channelMap = array('r', 'g', 'b', 'a');
        foreach($parts as $index => $hex){
            if(!empty($channelMap[$index])){
                $mapped = $channelMap[$index];
                if(strlen($hex) === 1){
                    $hex = str_repeat($hex, 2);
                }

                $rgba->{$mapped} = hexdec($hex);
            }
        }
        
        $abgrHex = array_reverse((array) $rgba);

        foreach($abgrHex as $index => $value){
            $abgrHex[$index] = dechex($value);
            if(strlen($abgrHex[$index]) === 1){
                $abgrHex[$index] = str_repeat($abgrHex[$index], 2);
            }
        }

        return implode("", $abgrHex);
    }

    /**
     * Iterate over the data types needed for the export, call a sub method to get the needed data 
     * 
     * Once called, cache/store that data in this instanced class to make it available in the primary output loop 
     * 
     * Supported datasets: Markers, Polylines, Polygons
     * 
     * @return void
     */
    public function getData(){
        foreach($this->types as $type){
            $method = "get" . ucwords($type);
            if(method_exists($this, $method)){
                $subset = $this->{$method}();
                if(!empty($subset)){
                    $this->data[$type] = $subset;
                }
            } else {
                throw new \Error("Unknown import method '{$method}'");
            }
        }
    }

    /**
     * Get markers from the database
     * 
     * @return array
     */
    public function getMarkers(){
        global $wpdb, $wpgmza, $WPGMZA_TABLE_NAME_MARKERS;
        $where = $this->buildWhereSql();
        
        $data = $wpdb->get_results("SELECT *, {$wpgmza->spatialFunctionPrefix}X(latlng) AS lat, {$wpgmza->spatialFunctionPrefix}Y(latlng) AS lng FROM `{$WPGMZA_TABLE_NAME_MARKERS}` {$where}");
        return $data;
    }

    /**
     * Get polylines from the database
     * 
     * @return array
     */
    public function getPolylines(){
        global $wpdb, $WPGMZA_TABLE_NAME_POLYLINES;
		$where = $this->buildWhereSql();
		$data = $wpdb->get_results("SELECT * FROM `{$WPGMZA_TABLE_NAME_POLYLINES}` {$where}");
		return $data;
    }

    /**
     * Get polygons from the database
     * 
     * @return array
     */
    public function getPolygons(){
        global $wpdb, $WPGMZA_TABLE_NAME_POLYGONS;
		$where = $this->buildWhereSql();
		$data = $wpdb->get_results("SELECT * FROM `{$WPGMZA_TABLE_NAME_POLYGONS}` {$where}");
        return $data;
    }

    
    /**
	 * Builds the where condition for any data query in this class
     * 
     * In this instance, only a map ID as a filter, but this coul be expanded later
	 * 
	 * @return string
	*/
	public function buildWhereSql(){
		$where = array();

        if(!empty($this->map)){
		    $where[] = "`map_id` = '{$this->map}'";
        }

		return !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
	}
}
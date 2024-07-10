<?php
/*
WP Go Maps - KML Importer

*/

class wpGoogleMapsKMLImporter{

	function __construct(){

		add_action( 'admin_menu', array( $this, 'wpgmza_kml_importer_menu' ), 11 );
		add_action( 'admin_head', array( $this, 'wpgmza_kml_importer_admin_head' ) );		

	}

	public function wpgmza_kml_importer_menu(){
		
		global $wpgmza;
		
		if($wpgmza && method_exists($wpgmza, 'getProVersion') && version_compare($wpgmza->getProVersion(), '7.0.0', '>='))
			return;

		add_submenu_page( 'wp-google-maps-menu', __('WP Go Maps - KML Importer', 'wp-google-maps'), __('KML File Importer', 'wp-google-maps') , 'manage_options', 'wp-google-maps', array( $this, 'wpgmza_kml_importer_page_contents' ) );

	}

	public function wpgmza_kml_importer_page_contents(){

		global $wpdb;
		global $wpgmza_tblname_maps;
    
	    if ($wpgmza_tblname_maps) { $table_name = $wpgmza_tblname_maps; } else { $table_name = $wpdb->prefix . "wpgmza_maps"; }

    	$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE `active` = %d ORDER BY `id` DESC", 0 ) );
    	
		$ret = "";

		$ret .= "<div class='wrap'>";

		$ret .= "<h2>".__( 'KML File Importer', 'wp-google-maps' )."</h2>";

		$ret .= "<div class='container' style='background: white; padding: 10px;'>";

		$ret .= "<form method='POST' enctype='multipart/form-data'><table class='form-table'>";

		$ret .= "<tr>";

		$ret .= "<th>".__( 'KML File', 'wp-google-maps')."</th>";

		$ret .= "<td><input type='file' name='wpgmza_kml_importer_file' id='wpgmza_kml_importer_file' /><br/><small>".__( 'Please select a KML or KMZ file to import', 'wp-google-maps' )."</small></td>";		

		$ret .= "</tr>";
		
		$ret .= "<tr>";
	
		$ret .= "<th>".__( 'Assigned Map', 'wp-google-maps' )."</th>";

		$ret .= "<td>";

		if( $results ){

			$ret .= "<select name='wpgmza_kml_importer_assigned_map' id='wpgmza_kml_importer_assigned_map'>";

			foreach( $results as $result ){
				$ret .= "<option value='".$result->id."'>[".$result->id."] ". stripslashes( $result->map_title )."</option>";
			}

			$ret .= "</select>";

		} else {
			$ret .= __( 'No maps found. Please create a map ', 'wp-google-maps' )."<a href='".admin_url("?page=wp-google-maps-menu&action=new")."'>".__( 'new map', 'wp-google-maps' )."</a> ".__( 'before importing your KML/KMZ file', 'wp-google-maps' );
		}

		$ret .= "</td>";

		$ret .= "</tr>";

		if( function_exists( 'wpgmaps_pro_activate' ) ){

			global $wpgmza_tblname_categories;
	    	global $wpgmza_tblname_category_maps;

			$ret .= "<tr>";

			$ret .= "<th>".__( 'Assigned Category', 'wp-google-maps' )."</th>";

			$ret .= "<td>";

		    $sql = "SELECT * FROM `$wpgmza_tblname_category_maps` LEFT JOIN `$wpgmza_tblname_categories` ON $wpgmza_tblname_category_maps.cat_id = $wpgmza_tblname_categories.id WHERE $wpgmza_tblname_categories.active = '0' ORDER BY `category_name` ASC";

		    $cat_results = $wpdb->get_results($sql);
		    
			if( $results ){

				$ret .= "<select name='wpgmza_kml_importer_assigned_category' id='wpgmza_kml_importer_assigned_category'>";
			    
			    $ret .= "<option value='0'>".__( 'None', 'wp-google-maps' )."</option>";
				
				foreach( $cat_results as $result ){

					$ret .= "<option value='".$result->id."'>".stripslashes( $result->category_name )."</option>";
				
				}

				$ret .= "</select>";

			} else {
				$ret .= __( 'No categories found. Please create a ', 'wp-google-maps' )."<a href='".admin_url("?page=wp-google-maps-menu-categories&action=new")."'>".__( 'new category', 'wp-google-maps' )."</a> ".__( 'should you wish to assign these markers to a category', 'wp-google-maps' );
			}

			$ret .= "</td>";

			$ret .= "</tr>";

		}

		$ret .= "<tr>";

		$ret .= "<td><input type='submit' name='wpgmza_kml_importer_run_import' id='wpgmza_kml_importer_run_import' class='button button-primary' value='".__( 'Upload', 'wp-google-maps' )."' /></td>";		

		$ret .= "</tr>";

		$ret .= "</table></form>";

		$ret .= "<small>".__( 'Please note that this functionality currently only supports markers. KMZ file imports are still in beta phase.', 'wp-google-maps' )."</small>";

		$ret .= "</div>";

		echo $ret;

	}

	public function wpgmza_kml_importer_admin_head(){

		global $wpdb;

		$wpgmza_markers_table = $wpdb->prefix."wpgmza";

		if( isset( $_POST['wpgmza_kml_importer_run_import'] ) ){	

			$file_to_import = $_FILES['wpgmza_kml_importer_file']['name'];
			
			$file_extension = pathinfo( $file_to_import, PATHINFO_EXTENSION );		

			$assigned_map = sanitize_text_field( $_POST['wpgmza_kml_importer_assigned_map'] );

			$assigned_category = isset( $_POST['wpgmza_kml_importer_assigned_category'] ) ? sanitize_text_field( $_POST['wpgmza_kml_importer_assigned_category'] ) : '0';

			if( $file_extension == 'kml' ){
			
				$xml = simplexml_load_file($_FILES['wpgmza_kml_importer_file']['tmp_name']);

				if ( isset( $xml->Document->Folder ) && $xml->Document->Folder!== null ) {
					$children = $xml->Document->Folder->children();
				} else {
					$children = false;	
				}

			} else {

				$contents = null;
				if ( class_exists( 'ZipArchive' ) ) {
			        $zip = new ZipArchive;

			        if ($zip->open($_FILES['wpgmza_kml_importer_file']['tmp_name']) === TRUE) {
			            
			            $contents = $zip->getFromIndex(0);
			            
			            $zip->close();
			        
			        }

			        $xml = (array) simplexml_load_string( $contents );

			        $children = $xml;
			    } else {
			    	$xml = false;
			    	echo "<div class='updated'><p>To import KMZ files, the ZipArchive module needs to be enabled on your server. Please contact your host.'</p></div>";
			    	return;
			    }

		    }
		    
			if( $xml ){

				if( $children ){

					foreach( $children as $child ){

						$title = (string) $child->name;
						$description = (string) $child->description;
						$coordinates = (string) $child->Point->coordinates;
						$coords_array = explode( ",", $coordinates );
						
						if( is_array( $coords_array ) ){
							$lat = isset( $coords_array[1] ) ? $coords_array[1] : '';
							$lng = isset( $coords_array[0] ) ? $coords_array[0] : '';
						} else {
							$lat = "";
							$lng = "";
						}
						
						$insert = $wpdb->insert( $wpgmza_markers_table, array(
							'map_id' 		=> $assigned_map,
							'address' 		=> '',
							'description' 	=> $description,
							'pic' 			=> '',							
							'link' 			=> '',
							'icon' 			=> '',
							'lat' 			=> $lat,
							'lng' 			=> $lng,
							'anim' 			=> '0',
							'title' 		=> $title,
							'infoopen' 		=> '0',
							'category' 		=> intval( $assigned_category ),
							'approved' 		=> '1',
							'retina' 		=> '0',
						) );	

					}

				}	

			}

			$ret = "<div class='updated'><p>".__('Your KML file has been successfully imported. View your markers on your ', 'wp-google-maps' )."<a href='".admin_url( '?page=wp-google-maps-menu&action=edit&map_id='.$assigned_map )."'>".__( 'map here', 'wp-google-maps') ."</a></p></div>";

			echo $ret;

		}

	}

}

new wpGoogleMapsKMLImporter();

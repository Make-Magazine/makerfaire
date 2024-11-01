<?php

define('WPGMZA_GOLD_FILE', __FILE__);

require_once(plugin_dir_path(__FILE__) . 'constants.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class.add-on.php');

global $wpgmza_t;
global $wpgmza_p;
global $wpgmza_g;
$wpgmza_gold_string = "gold";
$wpgmza_p = true;
$wpgmza_g = true;

global $wpgmza_count;
$wpgmza_count = 0;


include ("modules/wp-google-maps-kml-importer.php");


register_activation_hook( __FILE__, 'wpgmaps_gold_activate' );
register_deactivation_hook( __FILE__, 'wpgmaps_gold_deactivate' );
add_action('init', 'wpgmza_register_gold_version');
// add_action('admin_head', 'wpgmaps_head_gold');
//add_action('admin_footer', 'wpgmaps_reload_map_on_post_gold');

function wpgmaps_gold_activate() { wpgmza_cURL_response_gold("activate"); }
function wpgmaps_gold_deactivate() { wpgmza_cURL_response_gold("deactivate"); }

add_action('wp_enqueue_scripts', 'wpgmza_gold_on_wp_enqueue_scripts');
function wpgmza_gold_on_wp_enqueue_scripts()
{
	global $wpgmza_pro_version;
	
	$dependencies = array('wpgmza');
	
	if(version_compare($wpgmza_pro_version, '7.10.00', '<'))
		wp_enqueue_script(
			'wpgmza_gold_legacy_marker_offset_support', 
			plugin_dir_url(__FILE__) . 'js/v8/legacy-marker-offset-support.js', 
			$dependencies
		);
}

function wpgmza_register_gold_version() {
    global $wpgmza_gold_version;
    global $wpgmza_gold_string;
    if (!get_option('WPGMZA_GOLD')) {
        add_option('WPGMZA_GOLD',array("version" => $wpgmza_gold_version, "version_string" => $wpgmza_gold_string));
    }
}




function wpgmaps_admin_javascript_gold() {
    global $wpdb;
    global $wpgmza_tblname_maps;
    $ajax_nonce = wp_create_nonce("wpgmza");
	
    if( isset( $_POST['wpgmza_save_google_api_key_list'] ) ){  
        if( $_POST['wpgmza_google_maps_api_key'] !== '' ){      
            update_option('wpgmza_google_maps_api_key', sanitize_text_field($_POST['wpgmza_google_maps_api_key']) );
            echo "<div class='updated'><p>";
            $settings_page = "<a href='".admin_url('/admin.php?page=wp-google-maps-menu-settings#tabs-4')."'>".__('settings', 'wp-google-maps')."</a>";
            echo sprintf( __('Your Google Maps API key has been successfully saved. This API key can be changed in the %s page', 'wp-google-maps'), $settings_page );
			echo "<script> window.location.reload(); </script>";
            echo "</p></div>";
        }          
    }

    if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_marker") {
        wpgmaps_admin_edit_marker_javascript();
    }
    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_poly") {
        wpgmaps_b_admin_add_poly_javascript($_GET['map_id']);
    }
    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_poly") {
        wpgmaps_b_admin_edit_poly_javascript($_GET['map_id'],$_GET['poly_id']);
    }
    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_polyline") {
        wpgmaps_b_admin_add_polyline_javascript($_GET['map_id']);
    }
    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_polyline") {
        wpgmaps_b_admin_edit_polyline_javascript($_GET['map_id'],$_GET['poly_id']);
    }
    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_heatmap") {
        wpgmaps_b_admin_add_heatmap_javascript($_GET['map_id'],$_GET['id']);
    }
    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_heatmap") {
        wpgmaps_b_admin_edit_heatmap_javascript($_GET['map_id'],$_GET['id']);
    }


    else if (isset($_GET['page']) && isset($_GET['action']) && is_admin() && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit") {
        wpgmaps_update_xml_file($_GET['map_id']);

        $res = wpgmza_get_map_data($_GET['map_id']);
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

        $wpgmza_lat = $res->map_start_lat;
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_width = $res->map_width;
        $wpgmza_height = $res->map_height;
        $wpgmza_width_type = $res->map_width_type;
        $wpgmza_height_type = $res->map_height_type;
        $wpgmza_map_type = $res->type;
        $wpgmza_default_icon = $res->default_marker;
        $kml = $res->kml;
        $fusion = $res->fusion;
        $wpgmza_traffic = $res->traffic;
        $wpgmza_bicycle = $res->bicycle;
        $wpgmza_dbox = $res->dbox;
        $wpgmza_dbox_width = $res->dbox_width;


        $map_other_settings = maybe_unserialize($res->other_settings);
        if (isset($map_other_settings['weather_layer'])) { $weather_layer = $map_other_settings['weather_layer']; } else { $weather_layer = ""; }
        if (isset($map_other_settings['weather_layer_temp_type'])) { $weather_layer_temp_type = $map_other_settings['weather_layer_temp_type']; } else { $weather_layer_temp_type = 0; }
        if (isset($map_other_settings['cloud_layer'])) { $cloud_layer = $map_other_settings['cloud_layer']; } else { $cloud_layer = ""; }
        if (isset($map_other_settings['transport_layer'])) { $transport_layer = $map_other_settings['transport_layer']; } else { $transport_layer = ""; }
        if (isset($map_other_settings['map_max_zoom'])) { $wpgmza_max_zoom = intval($map_other_settings['map_max_zoom']); } else { $wpgmza_max_zoom = 0; }
        if (isset($map_other_settings['wpgmza_theme_data'])) { $wpgmza_theme_data = $map_other_settings['wpgmza_theme_data']; } else { $wpgmza_theme_data = false; }


        
        if ($wpgmza_default_icon == "0") { $wpgmza_default_icon = ""; }
        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }
        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) { $start_zoom = 5; }
        if (!$wpgmza_lat || !$wpgmza_lng) { $wpgmza_lat = "51.5081290"; $wpgmza_lng = "-0.1280050"; }
    
        $wpgmza_styling_enabled = $res->styling_enabled;
        $wpgmza_styling_json = $res->styling_json;
        
        // marker sorting functionality
        if ($res->order_markers_by == 1) { $order_by = 0; }
        else if ($res->order_markers_by == 2) { $order_by = 2; }
        else if ($res->order_markers_by == 3) { $order_by = 3; }
        else if ($res->order_markers_by == 4) { $order_by = 4; }
        else { $order_by = 0; }
        if ($res->order_markers_choice == 1) { $order_choice = "asc"; }
        else { $order_choice = "desc"; }    
        if (isset($wpgmza_settings['wpgmza_api_version'])) { $api_version = $wpgmza_settings['wpgmza_api_version']; } else { $api_version = ""; }
        if (isset($api_version) && $api_version != "") {
            $api_version_string = "v=$api_version&";
        } else {
            $api_version_string = "v=3.14&";
        }

        if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $marker_pull = "1"; }
        if (isset($marker_pull) && $marker_pull == "0") {
            if (!defined('PHP_VERSION_ID')) {
                $phpversion = explode('.', PHP_VERSION);
                define('PHP_VERSION_ID', ($phpversion[0] * 10000 + $phpversion[1] * 100 + $phpversion[2]));
            }
            if (PHP_VERSION_ID < 50300) {
                $markers = json_encode(wpgmaps_return_markers_pro($_GET['map_id']));
            } else {
                $markers = json_encode(wpgmaps_return_markers_pro($_GET['map_id']),JSON_HEX_APOS);    
            }
        }
        
    ?>

    <link rel="stylesheet" type="text/css" media="all" href="<?php echo wpgmaps_get_plugin_url(); ?>/css/data_table.css" />

    <script type="text/javascript" src="<?php echo wpgmaps_get_plugin_url(); ?>/js/markerclusterer.js"></script>
    <script type="text/javascript" src="<?php echo wpgmaps_get_plugin_url(); ?>/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" >
    var heatmap = [];

    var marker_pull = '<?php echo $marker_pull; ?>';
    <?php if (isset($markers) && strlen($markers) > 0 && $markers != "[]"){ ?>var db_marker_array = JSON.stringify(<?php echo $markers; ?>);<?php } else { echo "var db_marker_array = '';"; } ?>
    jQuery(function() {

            var placeSearch, autocomplete, wpgmza_def_i;

            function fillInAddress() {
                 var place = autocomplete.getPlace();  
            }

            jQuery(document).ready(function(){
    
                    if (typeof document.getElementById('wpgmza_add_address') !== "undefined") {
                       /* initialize the autocomplete form */
                       autocomplete = new google.maps.places.Autocomplete(
                         /** @type {HTMLInputElement} */(document.getElementById('wpgmza_add_address')),
                         { types: ['geocode'] });
                       // When the user selects an address from the dropdown,
                       // populate the address fields in the form.
                       google.maps.event.addListener(autocomplete, 'place_changed', function() {
                       fillInAddress();
                       });
                    }

                    
                    jQuery("#wpgmaps_show_advanced").click(function() {
                      jQuery("#wpgmaps_advanced_options").show();
                      jQuery("#wpgmaps_show_advanced").hide();
                      jQuery("#wpgmaps_hide_advanced").show();

                    });
                    jQuery("#wpgmaps_hide_advanced").click(function() {
                      jQuery("#wpgmaps_advanced_options").hide();
                      jQuery("#wpgmaps_show_advanced").show();
                      jQuery("#wpgmaps_hide_advanced").hide();

                    });
                    wpgmzaTable = jQuery('#wpgmza_table').dataTable({
                        "bProcessing": true,
                        "aaSorting": [[ <?php echo "$order_by";?>, "<?php echo $order_choice; ?>" ]]
                    });
                    function wpgmza_reinitialisetbl() {
                        wpgmzaTable.fnClearTable( 0 );
                        wpgmzaTable = jQuery('#wpgmza_table').dataTable({
                            "bProcessing": true,
                            "aaSorting": [[ <?php echo "$order_by";?>, "<?php echo $order_choice; ?>" ]]
                        });
                    }
                    function wpgmza_InitMap() {
                        var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                        MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                        UniqueCode=Math.round(Math.random()*10000);
                        MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($_GET['map_id']); ?>?u='+UniqueCode,<?php echo $_GET['map_id']; ?>);
                    }

                    jQuery("#wpgmza_map").css({
                        height:'<?php echo $wpgmza_height; ?><?php echo $wpgmza_height_type; ?>',
                        width:'<?php echo $wpgmza_width; ?><?php echo $wpgmza_width_type; ?>'

                    });
                    var geocoder = new google.maps.Geocoder();
                    wpgmza_InitMap();




                    jQuery("body").on("click", ".wpgmza_del_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_marker',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                marker_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
                                db_marker_array = JSON.stringify(returned_data.marker_data);
                                wpgmza_InitMap();
                                jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                wpgmza_reinitialisetbl();
                        });

                    });
                    jQuery("body").on("click", ".wpgmza_polyline_del_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_polyline',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                poly_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                wpgmza_InitMap();
                                jQuery("#wpgmza_polyline_holder").html(response);
                                window.location.reload();

                        });

                    });

                    jQuery("body").on("click", ".wpgmza_edit_btn", function() {
                        var cur_id = jQuery(this).attr("id");

                        var wpgmza_edit_title = jQuery("#wpgmza_hid_marker_title_"+cur_id).val();
                        wpgmza_edit_address = jQuery("#wpgmza_hid_marker_address_"+cur_id).val();
                        wpgmza_edit_lat = jQuery("#wpgmza_hid_marker_lat_"+cur_id).val();
                        wpgmza_edit_lng = jQuery("#wpgmza_hid_marker_lng_"+cur_id).val();
                        
                        
                        var wpgmza_edit_desc = jQuery("#wpgmza_hid_marker_desc_"+cur_id).val();
                        var wpgmza_edit_pic = jQuery("#wpgmza_hid_marker_pic_"+cur_id).val();
                        var wpgmza_edit_link = jQuery("#wpgmza_hid_marker_link_"+cur_id).val();
                        var wpgmza_edit_icon = jQuery("#wpgmza_hid_marker_icon_"+cur_id).val();
                        var wpgmza_edit_anim = jQuery("#wpgmza_hid_marker_anim_"+cur_id).val();
                        var wpgmza_edit_category = jQuery("#wpgmza_hid_marker_category_"+cur_id).val();
                        var wpgmza_edit_retina = jQuery("#wpgmza_hid_marker_retina_"+cur_id).val();
                        var wpgmza_edit_approved = jQuery("#wpgmza_hid_marker_approved_"+cur_id).val();
                        var wpgmza_edit_infoopen = jQuery("#wpgmza_hid_marker_infoopen_"+cur_id).val();
                        jQuery("#wpgmza_edit_id").val(cur_id);
                        jQuery("#wpgmza_add_title").val(wpgmza_edit_title);
                        jQuery("#wpgmza_add_address").val(wpgmza_edit_address);
                        if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                            var tinymce_editor_id = 'wpgmza_add_desc'; 
                            tinyMCE.get(tinymce_editor_id).setContent(wpgmza_edit_desc);
                        }else{
                            jQuery("#wpgmza_add_desc").val(wpgmza_edit_desc);
                        }
                        jQuery("#wpgmza_add_pic").val(wpgmza_edit_pic);
                        jQuery("#wpgmza_link_url").val(wpgmza_edit_link);
                        jQuery("#wpgmza_animation").val(wpgmza_edit_anim);
                        
                        jQuery('input[name=wpgmza_add_retina]').removeAttr('checked');
                        if (wpgmza_edit_retina === 0 || wpgmza_edit_retina === "0") { } else {
                            jQuery("#wpgmza_add_retina").prop('checked', true);
                        }

                        var cat_array = wpgmza_edit_category.split(",");
                        jQuery('input[name=wpgmza_cat_checkbox]').removeAttr('checked');
                        cat_array.forEach(function(entry) {
                            if (entry === 0) { } else {
                                jQuery("#wpgmza_cat_checkbox_"+entry).prop('checked', true);
                            }
                        });
                        
                        jQuery("#wpgmza_infoopen").val(wpgmza_edit_infoopen);
                        jQuery("#wpgmza_approved").val(wpgmza_edit_approved);
                        jQuery("#wpgmza_add_custom_marker").val(wpgmza_edit_icon);
                        if (wpgmza_edit_icon != "")
                          jQuery("#wpgmza_cmm").html("<img src='"+wpgmza_edit_icon+"' />");
                        else
                          jQuery("#wpgmza_cmm").html("&nbsp;"); 
                        jQuery("#wpgmza_addmarker_div").hide();
                        jQuery("#wpgmza_editmarker_div").show();


                    });
                    jQuery("body").on("click", ".wpgmza_approve_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'approve_marker',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                marker_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
                                db_marker_array = JSON.stringify(returned_data.marker_data);
                                wpgmza_InitMap();
                                jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                wpgmza_reinitialisetbl();

                        });

                    });
                    jQuery("body").on("click", ".wpgmza_poly_del_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_poly',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                poly_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                wpgmza_InitMap();
                                jQuery("#wpgmza_poly_holder").html(response);
                                window.location.reload();

                        });

                    });

                    jQuery("#wpgmza_addmarker").click(function(){
                        jQuery("#wpgmza_addmarker").hide();
                        jQuery("#wpgmza_addmarker_loading").show();



                        var wpgm_title = "";
                        var wpgm_address = "0";
                        var wpgm_desc = "0";
                        var wpgm_pic = "0";
                        var wpgm_link = "0";
                        var wpgm_icon = "0";
                        var wpgm_approved = "0";
                        var wpgm_gps = "0";

                        var wpgm_anim = "0";
                        var wpgm_category = "0";
                        var wpgm_retina = "0";
                        var wpgm_infoopen = "0";
                        var wpgm_map_id = "0";
                        var wpgmza_add_custom_marker_on_click = '';
                        if (document.getElementsByName("wpgmza_add_title").length > 0) { wpgm_title = jQuery("#wpgmza_add_title").val(); }
                        if (document.getElementsByName("wpgmza_add_address").length > 0) { wpgm_address = jQuery("#wpgmza_add_address").val(); }

                        if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                            var tinymce_editor_id = 'wpgmza_add_desc'; 
                            wpgm_desc = tinyMCE.get(tinymce_editor_id).getContent();
                        }else{
                            if (document.getElementsByName("wpgmza_add_desc").length > 0) { wpgm_desc = jQuery("#wpgmza_add_desc").val(); }
                        }

                                                
                        if (document.getElementsByName("wpgmza_add_pic").length > 0) { wpgm_pic = jQuery("#wpgmza_add_pic").val(); }
                        if (document.getElementsByName("wpgmza_link_url").length > 0) { wpgm_link = jQuery("#wpgmza_link_url").val(); }
                        if (document.getElementsByName("wpgmza_add_custom_marker").length > 0) { wpgm_icon = jQuery("#wpgmza_add_custom_marker").val(); }
                        if (document.getElementsByName("wpgmza_add_custom_marker_on_click").length > 0) { wpgmza_add_custom_marker_on_click = jQuery("#wpgmza_add_custom_marker_on_click").val(); }
                        if (document.getElementsByName("wpgmza_animation").length > 0) { wpgm_anim = jQuery("#wpgmza_animation").val(); }
                        
                        var Checked = jQuery('input[name="wpgmza_add_retina"]:checked').length > 0;
                        if (Checked) { wpgm_retina = "1"; } else { wpgm_retina = "0"; }

                        if (document.getElementsByName("wpgmza_category").length > 0) { wpgm_category = jQuery("#wpgmza_category").val(); }
                        
                    
                        var checkValues = jQuery('input[name=wpgmza_cat_checkbox]:checked').map(function() {
                            return jQuery(this).val();
                        }).get();
                        if (checkValues.length > 0) { wpgm_category = checkValues; }
                        wpgm_category.toString();
                        
                        
                        if (document.getElementsByName("wpgmza_infoopen").length > 0) { wpgm_infoopen = jQuery("#wpgmza_infoopen").val(); }
                        if (document.getElementsByName("wpgmza_approved").length > 0) { wpgm_approved = jQuery("#wpgmza_approved").val(); }
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        /* first check if user has added a GPS co-ordinate */
                        checker = wpgm_address.split(",");
                        var wpgm_lat = "";
                        var wpgm_lng = "";
                        wpgm_lat = checker[0];
                        wpgm_lng = checker[1];
                        checker1 = parseFloat(checker[0]);
                        checker2 = parseFloat(checker[1]);
                        if ((wpgm_lat.match(/[a-zA-Z]/g) === null && wpgm_lng.match(/[a-zA-Z]/g) === null) && checker.length === 2 && (checker1 != NaN && (checker1 <= 90 || checker1 >= -90)) && (checker2 != NaN && (checker2 <= 90 || checker2 >= -90))) {
                            var data = {
                                action: 'add_marker',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                title: wpgm_title,
                                address: wpgm_address,
                                desc: wpgm_desc,
                                link: wpgm_link,
                                icon: wpgm_icon,
                                icon_on_click: wpgmza_add_custom_marker_on_click,
                                retina: wpgm_retina,
                                pic: wpgm_pic,
                                anim: wpgm_anim,
                                category: wpgm_category,
                                infoopen: wpgm_infoopen,
                                approved: wpgm_approved,
                                lat: wpgm_lat,
                                lng: wpgm_lng

                            };


                            jQuery.post(ajaxurl, data, function(response) {
                                    returned_data = JSON.parse(response);
                                    
                                    db_marker_array = JSON.stringify(returned_data.marker_data);
                                    wpgmza_InitMap();

                                    jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                    
                                    jQuery("#wpgmza_addmarker").show();
                                    jQuery("#wpgmza_addmarker_loading").hide();
                                    jQuery("#wpgmza_add_title").val("");
                                    jQuery("#wpgmza_add_address").val("");
                                    if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                                        var tinymce_editor_id = 'wpgmza_add_desc'; 
                                        tinyMCE.get(tinymce_editor_id).setContent('');
                                    }else{
                                        jQuery("#wpgmza_add_desc").val("");
                                    }
                                    jQuery("#wpgmza_add_pic").val("");
                                    jQuery("#wpgmza_link_url").val("");
                                    jQuery("#wpgmza_animation").val("0");
                                    jQuery("#wpgmza_approved").val("1");
                                    jQuery("#wpgmza_add_retina").attr('checked',false);
                                    jQuery("#wpgmza_edit_id").val("");
                                    jQuery("#wpgmza_cmm").html(wpgmza_def_i);
                                    jQuery("#wpgmza_cmm_custom").html(wpgmza_def_i);
                                    jQuery("#wpgmza_add_custom_marker").val("");
                                    jQuery("#wpgmza_add_custom_marker_on_click").val("");
                                    jQuery('input[name=wpgmza_cat_checkbox]').attr('checked',false);

                                    wpgmza_reinitialisetbl();
                                    if( jQuery("#wpgmaps_marker_cache_reminder").length > 0 ){
                                        jQuery("#wpgmaps_marker_cache_reminder").fadeIn();
                                    }
                            });
                            
                            
                        } else { 
                            geocoder.geocode( { 'address': wpgm_address}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    wpgm_gps = String(results[0].geometry.location);
                                    var latlng1 = wpgm_gps.replace("(","");
                                    var latlng2 = latlng1.replace(")","");
                                    var latlngStr = latlng2.split(",",2);
                                    var wpgm_lat = parseFloat(latlngStr[0]);
                                    var wpgm_lng = parseFloat(latlngStr[1]);

                                    var data = {
                                        action: 'add_marker',
                                        security: '<?php echo $ajax_nonce; ?>',
                                        map_id: wpgm_map_id,
                                        title: wpgm_title,
                                        address: wpgm_address,
                                        desc: wpgm_desc,
                                        link: wpgm_link,
                                        icon: wpgm_icon,
                                        icon_on_click: wpgmza_add_custom_marker_on_click,
                                        retina: wpgm_retina,
                                        pic: wpgm_pic,
                                        anim: wpgm_anim,
                                        category: wpgm_category,
                                        infoopen: wpgm_infoopen,
                                        approved: wpgm_approved,
                                        lat: wpgm_lat,
                                        lng: wpgm_lng
                                    };


                                    jQuery.post(ajaxurl, data, function(response) {
                                            returned_data = JSON.parse(response);
                                            db_marker_array = JSON.stringify(returned_data.marker_data);
                                            wpgmza_InitMap();


                                            jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                            jQuery("#wpgmza_addmarker").show();
                                            jQuery("#wpgmza_addmarker_loading").hide();

                                            jQuery("#wpgmza_add_title").val("");
                                            jQuery("#wpgmza_add_address").val("");
                                            if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                                                var tinymce_editor_id = 'wpgmza_add_desc'; 
                                                tinyMCE.get(tinymce_editor_id).setContent('');
                                            }else{
                                                jQuery("#wpgmza_add_desc").val("");
                                            }
                                            jQuery("#wpgmza_add_pic").val("");
                                            jQuery("#wpgmza_link_url").val("");
                                            jQuery("#wpgmza_animation").val("0");
                                            jQuery("#wpgmza_approved").val("1");
                                            jQuery("#wpgmza_add_retina").attr('checked',false);
                                            jQuery("#wpgmza_cmm").html(wpgmza_def_i);
                                            jQuery("#wpgmza_cmm_custom").html(wpgmza_def_i);
                                            jQuery("#wpgmza_add_custom_marker").val("");
                                            jQuery("#wpgmza_add_custom_marker_on_click").val("");
                                            jQuery("#wpgmza_edit_id").val("");
                                            jQuery('input[name=wpgmza_cat_checkbox]').attr('checked',false);


                                            wpgmza_reinitialisetbl();
                                            if( jQuery("#wpgmaps_marker_cache_reminder").length > 0 ){
                                                jQuery("#wpgmaps_marker_cache_reminder").fadeIn();
                                            }
                                    });

                                } else {
                                    alert("<?php _e("Geocode was not successful for the following reason","wp-google-maps"); ?>: " + status);
                                    jQuery("#wpgmza_addmarker").show();
                                    jQuery("#wpgmza_addmarker_loading").hide();
                                }
                            });
                        }


                    });
                    jQuery("#wpgmza_editmarker").click(function(){

                        jQuery("#wpgmza_editmarker_div").hide();
                        jQuery("#wpgmza_editmarker_loading").show();


                        var wpgm_edit_id;
                        wpgm_edit_id = parseInt(jQuery("#wpgmza_edit_id").val());
                        var wpgm_title = "";
                        var wpgm_address = "0";
                        var wpgm_desc = "0";
                        var wpgm_pic = "0";
                        var wpgm_link = "0";
                        var wpgm_anim = "0";
                        var wpgm_category = "0";
                        var wpgm_infoopen = "0";
                        var wpgm_approved = "0";
                        var wpgm_icon = "";
                        var wpgm_retina = "0";
                        var wpgm_map_id = "0";
                        var wpgm_gps = "0";
                        var wpgmza_add_custom_marker_on_click = '';

                        if (document.getElementsByName("wpgmza_add_title").length > 0) { wpgm_title = jQuery("#wpgmza_add_title").val(); }
                        if (document.getElementsByName("wpgmza_add_address").length > 0) { wpgm_address = jQuery("#wpgmza_add_address").val(); }

                        if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                            var tinymce_editor_id = 'wpgmza_add_desc'; 
                            wpgm_desc = tinyMCE.get(tinymce_editor_id).getContent();
                        }else{
                            if (document.getElementsByName("wpgmza_add_desc").length > 0) { wpgm_desc = jQuery("#wpgmza_add_desc").val(); }
                        }


                        if (document.getElementsByName("wpgmza_add_pic").length > 0) { wpgm_pic = jQuery("#wpgmza_add_pic").val(); }
                        if (document.getElementsByName("wpgmza_link_url").length > 0) { wpgm_link = jQuery("#wpgmza_link_url").val(); }
                        if (document.getElementsByName("wpgmza_animation").length > 0) { wpgm_anim = jQuery("#wpgmza_animation").val(); }
                        if (document.getElementsByName("wpgmza_category").length > 0) { wpgm_category = jQuery("#wpgmza_category").val(); }
                        var Checked = jQuery('input[name="wpgmza_add_retina"]:checked').length > 0;
                        if (Checked) { wpgm_retina = "1"; } else { wpgm_retina = "0"; }
                        
                        
                        var checkValues = jQuery('input[name=wpgmza_cat_checkbox]:checked').map(function() {
                            return jQuery(this).val();
                        }).get();
                        if (checkValues.length > 0) { wpgm_category = checkValues; }
                        wpgm_category.toString();
                        if (document.getElementsByName("wpgmza_infoopen").length > 0) { wpgm_infoopen = jQuery("#wpgmza_infoopen").val(); }
                        if (document.getElementsByName("wpgmza_approved").length > 0) { wpgm_approved = jQuery("#wpgmza_approved").val(); }
                        if (document.getElementsByName("wpgmza_add_custom_marker").length > 0) { wpgm_icon = jQuery("#wpgmza_add_custom_marker").val(); }
                        if (document.getElementsByName("wpgmza_add_custom_marker_on_click").length > 0) { wpgmza_add_custom_marker_on_click = jQuery("#wpgmza_add_custom_marker_on_click").val(); }
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }


                        var do_geocode;
                        if (wpgm_address === wpgmza_edit_address) {
                            do_geocode = false;
                            var wpgm_lat = wpgmza_edit_lat;
                            var wpgm_lng = wpgmza_edit_lng;
                        } else { 
                            do_geocode = true;
                        }

                        if (do_geocode === true) {


                        geocoder.geocode( { 'address': wpgm_address}, function(results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                wpgm_gps = String(results[0].geometry.location);
                                var latlng1 = wpgm_gps.replace("(","");
                                var latlng2 = latlng1.replace(")","");
                                var latlngStr = latlng2.split(",",2);
                                var wpgm_lat = parseFloat(latlngStr[0]);
                                var wpgm_lng = parseFloat(latlngStr[1]);

                                var data = {
                                        action: 'edit_marker',
                                        security: '<?php echo $ajax_nonce; ?>',
                                        map_id: wpgm_map_id,
                                        edit_id: wpgm_edit_id,
                                        title: wpgm_title,
                                        address: wpgm_address,
                                        lat: wpgm_lat,
                                        lng: wpgm_lng,
                                        icon: wpgm_icon,
                                        icon_on_click: wpgmza_add_custom_marker_on_click,
                                        retina: wpgm_retina,
                                        desc: wpgm_desc,
                                        link: wpgm_link,
                                        pic: wpgm_pic,
                                        approved: wpgm_approved,
                                        anim: wpgm_anim,
                                        category: wpgm_category,
                                        infoopen: wpgm_infoopen
                                };

                                jQuery.post(ajaxurl, data, function(response) {
                                    returned_data = JSON.parse(response);
                                    db_marker_array = JSON.stringify(returned_data.marker_data);
                                    wpgmza_InitMap();
                                    jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                    jQuery("#wpgmza_addmarker_div").show();
                                    jQuery("#wpgmza_editmarker_loading").hide();
                                    jQuery("#wpgmza_add_title").val("");
                                    jQuery("#wpgmza_add_address").val("");
                                    if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                                        var tinymce_editor_id = 'wpgmza_add_desc'; 
                                        tinyMCE.get(tinymce_editor_id).setContent('');
                                    }else{
                                        jQuery("#wpgmza_add_desc").val("");
                                    }
                                    jQuery("#wpgmza_add_pic").val("");
                                    jQuery("#wpgmza_cmm").html(wpgmza_def_i);
                                    jQuery("#wpgmza_cmm_custom").html(wpgmza_def_i);
                                    jQuery("#wpgmza_add_custom_marker").val("");
                                    jQuery("#wpgmza_add_custom_marker_on_click").val("");
                                    jQuery("#wpgmza_link_url").val("");
                                    jQuery("#wpgmza_edit_id").val("");
                                    jQuery("#wpgmza_add_retina").attr('checked',false);
                                    jQuery("#wpgmza_animation").val("0");
                                    jQuery("#wpgmza_approved").val("1");
                                    jQuery('input[name=wpgmza_cat_checkbox]').attr('checked',false);
                                    wpgmza_reinitialisetbl();
                                    if( jQuery("#wpgmaps_marker_cache_reminder").length > 0 ){
                                        jQuery("#wpgmaps_marker_cache_reminder").fadeIn();
                                    }

                                });

                            } else {
                                alert("<?php _e("Geocode was not successful for the following reason","wp-google-maps"); ?>: " + status);
                                jQuery("#wpgmza_addmarker").show();
                                jQuery("#wpgmza_addmarker_loading").hide();
                            }
                        });
                        } else {
                            /* address was the same, no need for geocoding */
                            var data = {
                                action: 'edit_marker',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                edit_id: wpgm_edit_id,
                                title: wpgm_title,
                                address: wpgm_address,
                                lat: wpgm_lat,
                                lng: wpgm_lng,
                                icon: wpgm_icon,
                                icon_on_click: wpgmza_add_custom_marker_on_click,
                                retina: wpgm_retina,
                                desc: wpgm_desc,
                                link: wpgm_link,
                                approved: wpgm_approved,
                                pic: wpgm_pic,
                                anim: wpgm_anim,
                                category: wpgm_category,
                                infoopen: wpgm_infoopen
                            };

                            jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
                                db_marker_array = JSON.stringify(returned_data.marker_data);
                                wpgmza_InitMap();
                                jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                jQuery("#wpgmza_addmarker_div").show();
                                jQuery("#wpgmza_editmarker_loading").hide();
                                jQuery("#wpgmza_add_title").val("");
                                jQuery("#wpgmza_add_address").val("");
                                    if (jQuery("#wp-wpgmza_add_desc-wrap").hasClass("tmce-active")){
                                        var tinymce_editor_id = 'wpgmza_add_desc'; 
                                        tinyMCE.get(tinymce_editor_id).setContent('');
                                    }else{
                                        jQuery("#wpgmza_add_desc").val("");
                                    }
                                jQuery("#wpgmza_cmm").html(wpgmza_def_i);
                                jQuery("#wpgmza_cmm_custom").html(wpgmza_def_i);
                                jQuery("#wpgmza_add_custom_marker").val("");
                                jQuery("#wpgmza_add_custom_marker_on_click").val("");
                                jQuery("#wpgmza_add_pic").val("");
                                jQuery("#wpgmza_link_url").val("");
                                jQuery("#wpgmza_add_retina").attr('checked',false);
                                jQuery("#wpgmza_edit_id").val("");
                                jQuery("#wpgmza_animation").val("0");
                                jQuery("#wpgmza_approved").val("1");
                                jQuery("#wpgmza_category").val("Select");
                                jQuery('input[name=wpgmza_cat_checkbox]').attr('checked',false);
                                wpgmza_reinitialisetbl();
                                if( jQuery("#wpgmaps_marker_cache_reminder").length > 0 ){
                                    jQuery("#wpgmaps_marker_cache_reminder").fadeIn();
                                }

                            });
                        }





                    });
            });

            });



            <?php if ($wpgmza_styling_enabled == "1" && $wpgmza_styling_json != "" && $wpgmza_styling_enabled != null) { ?>

            var wpgmza_adv_styling_json = <?php echo html_entity_decode(stripslashes($wpgmza_styling_json)); ?>;

            <?php } ?>



            var MYMAP = {
                map: null,
                bounds: null,
                mc: null
            }
            MYMAP.init = function(selector, latLng, zoom) {
              var myOptions = {
                zoom:zoom,
                center: latLng,
                zoomControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_zoom']) && $wpgmza_settings['wpgmza_settings_map_zoom'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                panControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_pan']) && $wpgmza_settings['wpgmza_settings_map_pan'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                mapTypeControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_type']) && $wpgmza_settings['wpgmza_settings_map_type'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                streetViewControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_streetview']) && $wpgmza_settings['wpgmza_settings_map_streetview'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                fullscreenControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_full_screen_control']) && $wpgmza_settings['wpgmza_settings_map_full_screen_control'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $wpgmza_map_type; ?>
              }
           

            this.map = new google.maps.Map(jQuery(selector)[0], myOptions);
            <?php if ($wpgmza_theme_data !== false && isset($wpgmza_theme_data) && $wpgmza_theme_data != "") { ?>
            this.map.setOptions({styles: <?php echo stripslashes($wpgmza_theme_data); ?>});

            <?php } ?>    

            <?php
                $total_poly_array = wpgmza_b_return_polygon_id_array($_GET['map_id']);
                if ($total_poly_array > 0) {
                foreach ($total_poly_array as $poly_id) {
                    $polyoptions = wpgmza_b_return_poly_options($poly_id);
                    $linecolor = $polyoptions->linecolor;
                    $fillcolor = $polyoptions->fillcolor;
                    $fillopacity = $polyoptions->opacity;
                    $lineopacity = $polyoptions->lineopacity;
                    if (!$linecolor) { $linecolor = "000000"; }
                    if (!$fillcolor) { $fillcolor = "66FF00"; }
                    if ($fillopacity == "") { $fillopacity = "0.5"; }
                    if ($lineopacity == "") { $lineopacity = "1"; }
                    $linecolor = "#".$linecolor;
                    $fillcolor = "#".$fillcolor;
            ?> 
            var WPGM_PathData_<?php echo $poly_id; ?> = [
                <?php
                $poly_array = wpgmza_b_return_polygon_array($poly_id);
                
                foreach ($poly_array as $single_poly) {
                    $poly_data_raw = str_replace(" ","",$single_poly);
                    $poly_data_raw = explode(",",$poly_data_raw);
                    $lat = $poly_data_raw[0];
                    $lng = $poly_data_raw[1];
                    ?>
                    new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                    <?php
                }
                ?>
                
               
            ];
            var WPGM_Path_<?php echo $poly_id; ?> = new google.maps.Polygon({
              path: WPGM_PathData_<?php echo $poly_id; ?>,
              strokeColor: "<?php echo $linecolor; ?>",
              strokeOpacity: "<?php echo $lineopacity; ?>",
              fillOpacity: "<?php echo $fillopacity; ?>",
              fillColor: "<?php echo $fillcolor; ?>",
              strokeWeight: 2
            });

            WPGM_Path_<?php echo $poly_id; ?>.setMap(this.map);
            <?php } } ?>
                
                
                
<?php
                // polylines
                    $total_polyline_array = wpgmza_b_return_polyline_id_array($_GET['map_id']);
                    if ($total_polyline_array > 0) {
                    foreach ($total_polyline_array as $poly_id) {
                        $polyoptions = wpgmza_b_return_polyline_options($poly_id);
                        $linecolor = $polyoptions->linecolor;
                        $fillopacity = $polyoptions->opacity;
                        $linethickness = $polyoptions->linethickness;
                        if (!$linecolor) { $linecolor = "000000"; }
                        if (!$linethickness) { $linethickness = "4"; }
                        if (!$fillopacity) { $fillopacity = "0.5"; }
                        $linecolor = "#".$linecolor;
                ?> 
                var WPGM_PathLineData_<?php echo $poly_id; ?> = [
                    <?php
                    $poly_array = wpgmza_b_return_polyline_array($poly_id);

                    foreach ($poly_array as $single_poly) {
                        $poly_data_raw = str_replace(" ","",$single_poly);
                        $poly_data_raw = explode(",",$poly_data_raw);
                        $lat = $poly_data_raw[0];
                        $lng = $poly_data_raw[1];
                        ?>
                        new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                        <?php
                    }
                    ?>
                ];
                var WPGM_PathLine_<?php echo $poly_id; ?> = new google.maps.Polyline({
                  path: WPGM_PathLineData_<?php echo $poly_id; ?>,
                  strokeColor: "<?php echo $linecolor; ?>",
                  strokeOpacity: "<?php echo $fillopacity; ?>",
                  strokeWeight: "<?php echo $linethickness; ?>"
                  
                });

                WPGM_PathLine_<?php echo $poly_id; ?>.setMap(this.map);
                <?php } } ?>                  
                
                
            this.bounds = new google.maps.LatLngBounds();
            google.maps.event.addListener(MYMAP.map, 'zoom_changed', function() {
                zoomLevel = MYMAP.map.getZoom();

                jQuery("#wpgmza_start_zoom").val(zoomLevel);

              });
              
              google.maps.event.addListener(MYMAP.map, 'rightclick', function(event) {
                var marker = new google.maps.Marker({
                    position: event.latLng, 
                    map: MYMAP.map
                });
                marker.setDraggable(true);
                google.maps.event.addListener(marker, 'dragend', function(event) { 
                    jQuery("#wpgmza_add_address").val(event.latLng.lat()+','+event.latLng.lng());
                } );
                jQuery("#wpgmza_add_address").val(event.latLng.lat()+', '+event.latLng.lng());
                jQuery("#wpgm_notice_message_save_marker").show();
                setTimeout(function() {
                    jQuery("#wpgm_notice_message_save_marker").fadeOut('slow')
                }, 3000);
               
            });

          <?php
            $total_dataset_array = wpgmza_b_return_dataset_id_array(sanitize_text_field($_GET['map_id']));
            if ($total_dataset_array > 0) {
            foreach ($total_dataset_array as $poly_id) {
                $polyoptions = wpgmza_b_return_dataset_options($poly_id);
                $dataset_options = maybe_unserialize($polyoptions->options);
                //var_dump($dataset_options);
                $poly_array = wpgmza_b_return_dataset_array($poly_id);                    

                    if (isset($dataset_options['heatmap_opacity'])) { $opacity = floatval($dataset_options['heatmap_opacity']); } else { $opacity = floatval(0.6); }
                    if (isset($dataset_options['heatmap_gradient'])) { $gradient = stripslashes(html_entity_decode($dataset_options['heatmap_gradient'])); } else { $gradient = false; }
                    if (isset($dataset_options['heatmap_radius'])) { $radius = intval($dataset_options['heatmap_radius']); } else { $radius = intval(20); }



                    if (sizeof($poly_array) >= 1) { ?>
                        var WPGM_PathLineData_<?php echo $poly_id; ?> = [
                        <?php
                        $poly_array = wpgmza_b_return_dataset_array($poly_id);

                        foreach ($poly_array as $single_poly) {
                            $poly_data_raw = str_replace(" ","",$single_poly);
                            $poly_data_raw = explode(",",$poly_data_raw);
                            $lat = floatval($poly_data_raw[0]);
                            $lng = floatval($poly_data_raw[1]);
                            ?>
                new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                            <?php
                        }
                        ?>
                    ];
                heatmap[<?php echo $poly_id; ?>] = new google.maps.visualization.HeatmapLayer({
                    data: WPGM_PathLineData_<?php echo $poly_id; ?>,

                });
                console.log("Eh");

                heatmap[<?php echo $poly_id; ?>].setMap(this.map);
                heatmap[<?php echo $poly_id; ?>].set('opacity', <?php echo $opacity; ?>);
                <?php if ($gradient) { ?> heatmap[<?php echo $poly_id; ?>].set('gradient', <?php echo $gradient; ?>); <?php } ?>
                heatmap[<?php echo $poly_id; ?>].set('radius', <?php echo $radius; ?>);

            <?php  } } ?>

            <?php } ?>
              
            google.maps.event.addListener(MYMAP.map, 'center_changed', function() {
                var location = MYMAP.map.getCenter();
                jQuery("#wpgmza_start_location").val(location.lat()+","+location.lng());
                jQuery("#wpgmaps_save_reminder").show();
            });

            <?php if ($wpgmza_bicycle == "1") { ?>
            var bikeLayer = new google.maps.BicyclingLayer();
            bikeLayer.setMap(this.map);
            <?php } ?>
            <?php if ($wpgmza_traffic == "1") { ?>
            var trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(this.map);
            <?php } ?>
            <?php if ($weather_layer == 1) { ?>
            var weatherLayer = new google.maps.weather.WeatherLayer();
            weatherLayer.setMap(this.map);
            <?php } ?>
            <?php if ($cloud_layer == 1) { ?>
            var cloudLayer = new google.maps.weather.CloudLayer();
            cloudLayer.setMap(this.map);
            <?php } ?>
            <?php if ($transport_layer == 1) { ?>
            var transitLayer = new google.maps.TransitLayer();
            transitLayer.setMap(this.map);
            <?php } ?>



            <?php if ($kml != "") { ?>
            var georssLayer = new google.maps.KmlLayer('<?php echo $kml; ?>?tstamp=<?php echo time(); ?>');
            georssLayer.setMap(this.map);
            <?php } ?>
            <?php if ($fusion != "") { ?>
                var fusionlayer = new google.maps.FusionTablesLayer('<?php echo $fusion; ?>', {
                      suppressInfoWindows: false
                });
                fusionlayer.setMap(this.map);
            <?php } ?>



            }
            var infoWindow = new google.maps.InfoWindow();
            <?php
                $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                if (isset($wpgmza_settings['wpgmza_settings_infowindow_width'])) { $wpgmza_settings_infowindow_width = $wpgmza_settings['wpgmza_settings_infowindow_width']; } else { $wpgmza_settings_infowindow_width = ""; }
                if (!$wpgmza_settings_infowindow_width || !isset($wpgmza_settings_infowindow_width)) { $wpgmza_settings_infowindow_width = "200"; }
            ?>
            infoWindow.setOptions({maxWidth:<?php echo $wpgmza_settings_infowindow_width; ?>});

            google.maps.event.addDomListener(window, 'resize', function() {
                var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                MYMAP.map.setCenter(myLatLng);
            });



            MYMAP.placeMarkers = function(filename,map_id) {
                marker_array = [];
                if (marker_pull === '1') {
                        jQuery.get(filename, function(xml) {
                                jQuery(xml).find("marker").each(function(){
                                        var wpgmza_def_icon = '<?php echo $wpgmza_default_icon; ?>';
                                        var wpmgza_map_id = jQuery(this).find('map_id').text();

                                        if (wpmgza_map_id == map_id) {
                                            var wpmgza_title = jQuery(this).find('title').text();
                                            var wpmgza_show_address = jQuery(this).find('address').text();
                                            var wpmgza_address = jQuery(this).find('address').text();
                                            var wpmgza_mapicon = jQuery(this).find('icon').text();
                                            var wpmgza_image = jQuery(this).find('pic').text();
                                            var wpmgza_desc  = jQuery(this).find('desc').text();
                                            var wpmgza_anim  = jQuery(this).find('anim').text();
                                            var wpmgza_retina  = jQuery(this).find('retina').text();
                                            var wpmgza_infoopen  = jQuery(this).find('infoopen').text();
                                            var wpmgza_linkd = jQuery(this).find('linkd').text();
                                            if (wpmgza_title != "") {
                                                wpmgza_title = wpmgza_title+'<br />';
                                            }

                                            /* check image */
                                            if (wpmgza_image != "") {

                                        <?php
                                            $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                                            if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = false; }
                                            if (!$wpgmza_settings_infowindow_link_text) { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }
                                            
                                            if (isset($wpgmza_settings['wpgmza_settings_image_resizing']) && $wpgmza_settings['wpgmza_settings_image_resizing'] == 'yes') { $wpgmza_image_resizing = true; } else { $wpgmza_image_resizing = false; }
                                            if (isset($wpgmza_settings['wpgmza_settings_use_timthumb'])) { $wpgmza_use_timthumb = $wpgmza_settings['wpgmza_settings_use_timthumb']; } else { $wpgmza_use_timthumb = true; }
                                            if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_image_height = $wpgmza_settings['wpgmza_settings_image_height']; } else { $wpgmza_image_height = false; }
                                            if (isset($wpgmza_settings['wpgmza_settings_image_width'])) { $wpgmza_image_width = $wpgmza_settings['wpgmza_settings_image_width']; } else { $wpgmza_image_width = false; }
                                            if (!$wpgmza_image_height || !isset($wpgmza_image_height)) { $wpgmza_image_height = "100"; }
                                            if (!$wpgmza_image_width || !isset($wpgmza_image_width)) { $wpgmza_image_width = "100"; }
                                            
                                            /* check if using timthumb */
                                            /* timthumb completely removed in 3.29
                                            if (!isset($wpgmza_use_timthumb) || $wpgmza_use_timthumb == "" || $wpgmza_use_timthumb == 1) { ?>
                                                wpmgza_image = "<img src='<?php echo wpgmaps_get_plugin_url(); ?>/timthumb.php?src="+wpmgza_image+"&h=<?php echo $wpgmza_image_height; ?>&w=<?php echo $wpgmza_image_width; ?>&zc=1' title='' alt='' style=\"float:right; width:"+<?php echo $wpgmza_image_width; ?>+"px; height:"+<?php echo $wpgmza_image_height; ?>+"px;\" />";
                                            <?php } else { 
                                            */
                                                
                                                /* User has chosen not to use timthumb. excellent! */
                                                if ($wpgmza_image_resizing) {
                                                    ?>
                                                    wpgmza_resize_string = "width='<?php echo $wpgmza_image_width; ?>' height='<?php echo $wpgmza_image_height; ?>'";
                                                    <?php
                                                } else {
                                                    ?>
                                                    wpgmza_resize_string = "";
                                                    <?php
                                                }
                                                ?>
                                                
                                                wpmgza_image = "<img src='"+wpmgza_image+"' class='wpgmza_map_image wpgmza_map_image_"+wpmgza_map_id+"' style='float:right;' "+wpgmza_resize_string+" />";




                                            <?php /* } */ ?>

                                            /* end check image */
                                            } else { wpmgza_image = "" }

                                            <?php
                                            if (isset($wpgmza_settings['wpgmza_settings_retina_width'])) { $wpgmza_settings_retina_width = intval($wpgmza_settings['wpgmza_settings_retina_width']); } else { $wpgmza_settings_retina_width = 31; };
                                            if (isset($wpgmza_settings['wpgmza_settings_retina_height'])) { $wpgmza_settings_retina_height = intval($wpgmza_settings['wpgmza_settings_retina_height']); } else { $wpgmza_settings_retina_height = 45; };
                                            ?>

                                            if (wpmgza_linkd != "") {
                                                    <?php
                                                        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                                                        if (isset($wpgmza_settings['wpgmza_settings_infowindow_links'])) { $wpgmza_settings_infowindow_links = $wpgmza_settings['wpgmza_settings_infowindow_links']; }
                                                        if (isset($wpgmza_settings_infowindow_links) && $wpgmza_settings_infowindow_links == "yes") { $wpgmza_settings_infowindow_links = "target='_BLANK'";  } else { $wpgmza_settings_infowindow_links = ""; }
                                                    ?>

                                                    wpmgza_linkd = "<a href='"+wpmgza_linkd+"' <?php echo $wpgmza_settings_infowindow_links; ?> title='<?php echo $wpgmza_settings_infowindow_link_text; ?>'><?php echo $wpgmza_settings_infowindow_link_text; ?></a>";
                                                }
                                            if (wpmgza_mapicon == "" || !wpmgza_mapicon) { if (wpgmza_def_icon != "") { wpmgza_mapicon = '<?php echo $wpgmza_default_icon; ?>'; } }
                                            var wpgmza_optimized = true;
                                            if (wpmgza_retina === "1" && wpmgza_mapicon !== "") {
                                                wpmgza_mapicon = new google.maps.MarkerImage(wpmgza_mapicon, null, null, null, new google.maps.Size(<?php echo $wpgmza_settings_retina_width; ?>,<?php echo $wpgmza_settings_retina_height; ?>));
                                                wpgmza_optimized = false;
                                            }
                                            var lat = jQuery(this).find('lat').text();
                                            var lng = jQuery(this).find('lng').text();
                                            var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                                            MYMAP.bounds.extend(point);
                                            if (wpmgza_anim == "1") {
                                            var marker = new google.maps.Marker({
                                                    position: point,
                                                    map: MYMAP.map,
                                                    icon: wpmgza_mapicon,
                                                    animation: google.maps.Animation.BOUNCE
                                            });
                                            }
                                            else if (wpmgza_anim == "2") {
                                                var marker = new google.maps.Marker({
                                                        position: point,
                                                        map: MYMAP.map,
                                                        icon: wpmgza_mapicon,
                                                        animation: google.maps.Animation.DROP
                                                });
                                            }
                                            else {
                                                var marker = new google.maps.Marker({
                                                        position: point,
                                                        map: MYMAP.map,
                                                        icon: wpmgza_mapicon
                                                });
                                            }
                                            //var html=''+wpmgza_image+'<strong>'+wpmgza_address+'</strong><br /><span style="font-size:12px;">'+wpmgza_desc+'<br />'+wpmgza_linkd+'</span>';
                                            <?php
                                                    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                                                    if (isset($wpgmza_settings['wpgmza_settings_infowindow_address'])) { 
                                                        $wpgmza_settings_infowindow_address = $wpgmza_settings['wpgmza_settings_infowindow_address'];
                                                    } else { $wpgmza_settings_infowindow_address = ""; }
                                                    if ($wpgmza_settings_infowindow_address == "yes") {

                                            ?>
                                                        wpmgza_show_address = "";
                                            <?php } ?>


                                            var html='<div id="wpgmza_markerbox" style="min-width:'+<?php echo $wpgmza_settings_infowindow_width; ?>+'px;">'+wpmgza_image+'<p><strong>'+wpmgza_title+'</strong>'+wpmgza_show_address+'<br />'
                                                    +wpmgza_desc+
                                                    '<br />'
                                                    +wpmgza_linkd+
                                                    ''
                                                    +'</p></div>';
                                            if (wpmgza_infoopen == "1") {

                                                infoWindow.setContent(html);
                                                infoWindow.open(MYMAP.map, marker);
                                            }

                                            <?php if (isset($wpgmza_open_infowindow_by) && $wpgmza_open_infowindow_by == '2') { ?>
                                            google.maps.event.addListener(marker, 'mouseover', function() {
                                                infoWindow.close();
                                                infoWindow.setContent(html);
                                                infoWindow.open(MYMAP.map, marker);

                                            });
                                            <?php } else { ?>
                                            google.maps.event.addListener(marker, 'click', function() {
                                                infoWindow.close();
                                                infoWindow.setContent(html);
                                                infoWindow.open(MYMAP.map, marker);

                                            });
                                            <?php } ?>


                                        }

                            });
                    });
                
                } else {
                    
                    if (db_marker_array.length > 0) {
                    var dec_marker_array = jQuery.parseJSON(db_marker_array);
                    jQuery.each(dec_marker_array, function(i, val) {


                        var wpgmza_def_icon = '<?php echo $wpgmza_default_icon; ?>';
                        var wpmgza_map_id = val.map_id;

                        if (wpmgza_map_id == map_id) {
                            var wpmgza_title = val.title;
                            var wpmgza_show_address = val.address;
                            var wpmgza_address = val.address;
                            var wpmgza_mapicon = val.icon;
                            var wpmgza_image = val.pic;
                            var wpmgza_desc  = val.desc;
                            var wpmgza_anim  = val.anim;
                            var wpmgza_retina  = val.retina;
                            var wpmgza_infoopen  = val.infoopen;
                            var wpmgza_linkd = val.linkd;
                            if (wpmgza_title != "") {
                                wpmgza_title = wpmgza_title+'<br />';
                            }
                           /* check image */
                            if (wpmgza_image != "") {

                        <?php
                            $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                            if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = false; }
                            if (!$wpgmza_settings_infowindow_link_text) { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }
                            
                            if (isset($wpgmza_settings['wpgmza_settings_image_resizing']) && $wpgmza_settings['wpgmza_settings_image_resizing'] == 'yes') { $wpgmza_image_resizing = true; } else { $wpgmza_image_resizing = false; }
                                if (isset($wpgmza_settings['wpgmza_settings_use_timthumb'])) { $wpgmza_use_timthumb = $wpgmza_settings['wpgmza_settings_use_timthumb']; } else { $wpgmza_use_timthumb = true; }
                            if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_image_height = $wpgmza_settings['wpgmza_settings_image_height']; } else { $wpgmza_image_height = false; }
                            if (isset($wpgmza_settings['wpgmza_settings_image_width'])) { $wpgmza_image_width = $wpgmza_settings['wpgmza_settings_image_width']; } else { $wpgmza_image_width = false; }
                            if (!$wpgmza_image_height || !isset($wpgmza_image_height)) { $wpgmza_image_height = "100"; }
                            if (!$wpgmza_image_width || !isset($wpgmza_image_width)) { $wpgmza_image_width = "100"; }
                            
                            /* check if using timthumb */
                            /* timthumb completely removed in 3.29
                            if (!isset($wpgmza_use_timthumb) || $wpgmza_use_timthumb == "" || $wpgmza_use_timthumb == 1) { ?>
                                wpmgza_image = "<img src='<?php echo wpgmaps_get_plugin_url(); ?>/timthumb.php?src="+wpmgza_image+"&h=<?php echo $wpgmza_image_height; ?>&w=<?php echo $wpgmza_image_width; ?>&zc=1' title='' alt='' style=\"float:right; width:"+<?php echo $wpgmza_image_width; ?>+"px; height:"+<?php echo $wpgmza_image_height; ?>+"px;\" />";
                            <?php } else { 
                            */
                                
                                /* User has chosen not to use timthumb. excellent! */
                                if ($wpgmza_image_resizing) {
                                    ?>
                                    wpgmza_resize_string = "width='<?php echo $wpgmza_image_width; ?>' height='<?php echo $wpgmza_image_height; ?>'";
                                    <?php
                                } else {
                                    ?>
                                    wpgmza_resize_string = "";
                                    <?php
                                }
                                ?>
                                
                                wpmgza_image = "<img src='"+wpmgza_image+"' class='wpgmza_map_image wpgmza_map_image_"+wpmgza_map_id+"' style='float:right;' "+wpgmza_resize_string+" />";




                            <?php /* } */ ?>

                            /* end check image */
                            } else { wpmgza_image = "" }

                            <?php
                            if (isset($wpgmza_settings['wpgmza_settings_retina_width'])) { $wpgmza_settings_retina_width = intval($wpgmza_settings['wpgmza_settings_retina_width']); } else { $wpgmza_settings_retina_width = 31; };
                            if (isset($wpgmza_settings['wpgmza_settings_retina_height'])) { $wpgmza_settings_retina_height = intval($wpgmza_settings['wpgmza_settings_retina_height']); } else { $wpgmza_settings_retina_height = 45; };
                            ?>
                            if (wpmgza_linkd != "") {
                                    <?php
                                        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                                        if (isset($wpgmza_settings['wpgmza_settings_infowindow_links'])) { $wpgmza_settings_infowindow_links = $wpgmza_settings['wpgmza_settings_infowindow_links']; }
                                        if (isset($wpgmza_settings_infowindow_links) && $wpgmza_settings_infowindow_links == "yes") { $wpgmza_settings_infowindow_links = "target='_BLANK'";  } else { $wpgmza_settings_infowindow_links = ""; }
                                    ?>

                                    wpmgza_linkd = "<a href='"+wpmgza_linkd+"' <?php echo $wpgmza_settings_infowindow_links; ?> title='<?php echo $wpgmza_settings_infowindow_link_text; ?>'><?php echo $wpgmza_settings_infowindow_link_text; ?></a>";
                                }
                            if (wpmgza_mapicon == "" || !wpmgza_mapicon) { if (wpgmza_def_icon != "") { wpmgza_mapicon = '<?php echo $wpgmza_default_icon; ?>'; } }
                            var wpgmza_optimized = true;
                            if (wpmgza_retina === "1" && wpmgza_mapicon !== "") {
                                wpmgza_mapicon = new google.maps.MarkerImage(wpmgza_mapicon, null, null, null, new google.maps.Size(<?php echo $wpgmza_settings_retina_width; ?>,<?php echo $wpgmza_settings_retina_height; ?>));
                                wpgmza_optimized = false;
                            }
                            var lat = val.lat;
                            var lng = val.lng;
                            var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                            MYMAP.bounds.extend(point);
                            if (wpmgza_anim == "1") {
                            var marker = new google.maps.Marker({
                                    position: point,
                                    map: MYMAP.map,
                                    icon: wpmgza_mapicon,
                                    animation: google.maps.Animation.BOUNCE
                            });
                            }
                            else if (wpmgza_anim == "2") {
                                var marker = new google.maps.Marker({
                                        position: point,
                                        map: MYMAP.map,
                                        icon: wpmgza_mapicon,
                                        animation: google.maps.Animation.DROP
                                });
                            }
                            else {
                                var marker = new google.maps.Marker({
                                        position: point,
                                        map: MYMAP.map,
                                        icon: wpmgza_mapicon
                                });
                            }
                            //var html=''+wpmgza_image+'<strong>'+wpmgza_address+'</strong><br /><span style="font-size:12px;">'+wpmgza_desc+'<br />'+wpmgza_linkd+'</span>';
                            <?php
                                    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                                    if (isset($wpgmza_settings['wpgmza_settings_infowindow_address'])) { 
                                        $wpgmza_settings_infowindow_address = $wpgmza_settings['wpgmza_settings_infowindow_address'];
                                    } else { $wpgmza_settings_infowindow_address = ""; }
                                    if ($wpgmza_settings_infowindow_address == "yes") {

                            ?>
                                        wpmgza_show_address = "";
                            <?php } ?>

                            var html='<div id="wpgmza_markerbox" style="min-width:'+<?php echo $wpgmza_settings_infowindow_width; ?>+'px;">'+wpmgza_image+'<p><strong>'+wpmgza_title+'</strong>'+wpmgza_show_address+'<br />'
                                    +wpmgza_desc+
                                    '<br />'
                                    +wpmgza_linkd+
                                    ''
                                    +'</p></div>';
                            if (wpmgza_infoopen == "1") {

                                infoWindow.setContent(html);
                                infoWindow.open(MYMAP.map, marker);
                            }

                            <?php if (isset($wpgmza_open_infowindow_by) && $wpgmza_open_infowindow_by == '2') { ?>
                            google.maps.event.addListener(marker, 'mouseover', function() {
                                infoWindow.close(); 
                               infoWindow.setContent(html);
                                infoWindow.open(MYMAP.map, marker);

                            });
                            <?php } else { ?>
                            google.maps.event.addListener(marker, 'click', function() {
                                infoWindow.close();
                                infoWindow.setContent(html);
                                infoWindow.open(MYMAP.map, marker);
                            });
                            <?php } ?>
                        }
                  });
                    var mcOptions = {
                        gridSize: 50,
                        maxZoom: 15
                    };
                   
                  }
                }
            }


            

        </script>
        <!-- <script type="text/javascript" src="<?php //echo wpgmaps_get_plugin_url(); ?>/js/wpgmaps.js"></script> -->
<?php
}

}

add_filter("wpgmza_pro_filter_save_map_other_settings","wpgmza_pro_gold_filter_control_save_map_other_settings",10,1);
function wpgmza_pro_gold_filter_control_save_map_other_settings($other_settings) {

    if (isset($_POST['wpgmza_rtlt_enabled'])) { $other_settings['rtlt_enabled'] = isset($_POST['wpgmza_rtlt_enabled']) ? 1 : 0; }
    if (isset($_POST['wpgmza_rtlt_enable_polylines'])) { $other_settings['wpgmza_rtlt_enable_polylines'] = isset($_POST['wpgmza_rtlt_enable_polylines']) ? 1 : 0; }

    if (isset($_POST['wpgmza_rtlt_route'])) { $other_settings['rtlt_route'] = isset($_POST['wpgmza_rtlt_route']) ? 1 : 0; }

    /*RTLT Route Styling*/

    if (isset($_POST['wpgmza_rtlt_route_col_normal'])) { $other_settings['rtlt_route_col_normal'] = isset($_POST['wpgmza_rtlt_route_col_normal']) ? $_POST['wpgmza_rtlt_route_col_normal'] : "5fa8e8"; }
    if (isset($_POST['wpgmza_rtlt_route_col_hover'])) { $other_settings['rtlt_route_col_hover'] = isset($_POST['wpgmza_rtlt_route_col_hover']) ? $_POST['wpgmza_rtlt_route_col_hover'] : "98cfff"; }

    if (isset($_POST['wpgmza_rtlt_route_opacity'])) { 
        $other_settings['rtlt_route_opacity'] = isset($_POST['wpgmza_rtlt_route_opacity']) ? $_POST['wpgmza_rtlt_route_opacity'] : "0.6"; 
        if(floatval($other_settings['rtlt_route_opacity']) > 1.0){$other_settings['rtlt_route_opacity'] = "1.0";}
        if(floatval($other_settings['rtlt_route_opacity']) < 0.0){$other_settings['rtlt_route_opacity'] = "0.1";}
    }

    if (isset($_POST['wpgmza_rtlt_route_thickness'])) { 
        $other_settings['rtlt_route_thickness'] = isset($_POST['wpgmza_rtlt_route_thickness']) ? $_POST['wpgmza_rtlt_route_thickness'] : "12"; 
        if(intval($other_settings['rtlt_route_thickness']) > 50){$other_settings['rtlt_route_thickness'] = "50";}
        if(intval($other_settings['rtlt_route_thickness']) < 0){$other_settings['rtlt_route_thickness'] = "1";}
    }

    if (isset($_POST['upload_default_rtlt_marker'])) { 
        $map_default_rtlt_marker = str_replace('http:', '', $_POST['upload_default_rtlt_marker']);
        $other_settings['upload_default_rtlt_marker'] = $map_default_rtlt_marker;
    }
    if (isset($_POST['wpgmza_rtlt_qty'])) { $other_settings['wpgmza_rtlt_qty'] = intval($_POST['wpgmza_rtlt_qty']); }

    


    
    return $other_settings;

}


add_action('admin_print_scripts', 'wpgmaps_gold_admin_scripts_pro');


function wpgmaps_gold_admin_scripts_pro() {
    
    if (isset($_GET['page'])) {
        if ($_GET['page'] == "wp-google-maps-menu") {
                $scriptArgs = apply_filters('wpgmza-get-scripts-arguments', array());
                wp_register_script('admin-gold-wpgmaps', plugins_url('js/wpgmaps-gold-admin.js', __FILE__), false, false, $scriptArgs);
                wp_enqueue_script('admin-gold-wpgmaps');

        }
    }
}




function wpgmza_gold_addon_display() {

    global $wpgmza_pro_version;
    if (floatval($wpgmza_pro_version) < 6) {

        $res = wpgmza_get_map_data($_GET['map_id']);

        
        if ($res->styling_enabled) { $wpgmza_adv_styling[$res->styling_enabled] = "SELECTED"; } else { $wpgmza_adv_styling[2] = "SELECTED"; }
        if ($res->mass_marker_support) { $wpgmza_adv_mass_marker_support[$res->mass_marker_support] = "SELECTED"; } else { $wpgmza_adv_mass_marker_support[2] = "SELECTED"; }
        
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_adv_mass_marker_support[$i])) { $wpgmza_adv_mass_marker_support[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_adv_styling[$i])) { $wpgmza_adv_styling[$i] = ""; }
        }
        
        /*
        $ret = "
            <div style=\"display:block; overflow:auto; background-color:#FFFBCC; padding:10px; border:1px solid #E6DB55; margin-top:35px; margin-bottom:5px;\">
                <h2 style=\"padding-top:0; margin-top:0;\">".__("Advanced Map Settings","wp-google-maps")."</h2>
                <p>".__("Use the <a href='http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html' target='_BLANK'>Google Maps API Styled Map Wizard</a> to get your style settings","wp-google-maps")."!</p>
                    <form action='' method='post' id='wpgmaps_gold_option_styling'>
                        <table>
                        <input type=\"hidden\" name=\"wpgmza_map_id\" id=\"wpgmza_map_id\" value=\"".$_GET['map_id']."\" />
                            <tr style='margin-bottom:20px;'>
                                <td>".__("Enable Mass Marker Support","wp-google-maps")."?:</td>
                                <td>
                                    <select id='wpgmza_adv_enable_mass_marker_support' name='wpgmza_adv_enable_mass_marker_support'>
                                        <option value=\"1\" ".$wpgmza_adv_mass_marker_support[1].">".__("Yes","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_adv_mass_marker_support[2].">".__("No","wp-google-maps")."</option>
                                    </select>
                                </td>
                             </tr>
                            <tr style='margin-bottom:20px;'>
                                <td>".__("Enable Advanced Styling","wp-google-maps")."?:</td>
                                <td>
                                    <select id='wpgmza_adv_styling' name='wpgmza_adv_styling'>
                                        <option value=\"1\" ".$wpgmza_adv_styling[1].">".__("Yes","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_adv_styling[2].">".__("No","wp-google-maps")."</option>
                                    </select>
                                </td>
                             </tr>
                             <tr>
                                <td valign='top'>".__("Paste the JSON data here","wp-google-maps").":</td>
                                <td><textarea name=\"wpgmza_adv_styling_json\" id=\"wpgmza_adv_styling_json\" rows=\"8\" cols=\"40\">".stripslashes($res->styling_json)."</textarea></td>
                             </tr>
                         </table>
                        <p class='submit'><input type='submit' name='wpgmza_save_style_settings' value='".__("Save Style Settings","wp-google-maps")." &raquo;' /></p>
                    </form>
            </div>
        ";
        */



        $ret = "
            <div style=\"display:block; overflow:auto; background-color:#FFFBCC; padding:10px; border:1px solid #E6DB55; margin-top:35px; margin-bottom:5px;\">
                <h2 style=\"padding-top:0; margin-top:0;\">".__("Advanced Map Settings","wp-google-maps")."</h2>
                    <form action='' method='post' id='wpgmaps_gold_option_styling'>
                        <table>
                        <input type=\"hidden\" name=\"wpgmza_map_id\" id=\"wpgmza_map_id\" value=\"".$_GET['map_id']."\" />
                            <tr style='margin-bottom:20px;'>
                                <td>".__("Enable Mass Marker Support","wp-google-maps")."?:</td>
                                <td>
                                    <select id='wpgmza_adv_enable_mass_marker_support' name='wpgmza_adv_enable_mass_marker_support'>
                                        <option value=\"1\" ".$wpgmza_adv_mass_marker_support[1].">".__("Yes","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_adv_mass_marker_support[2].">".__("No","wp-google-maps")."</option>
                                    </select>
                                </td>
                             </tr>

                         </table>
                        <p class='submit'><input type='submit' name='wpgmza_save_style_settings' value='".__("Save","wp-google-maps")." &raquo;' /></p>
                    </form>
            </div>
        ";
        return $ret;
    }


}


$wpgmaps_gold_api_url = 'http://ccplugins.co/api-wpgmza-gold-v5/';
$wpgmaps_gold_plugin_slug = basename(dirname(__FILE__));

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'wpgmaps_gold_check_for_plugin_update');

function wpgmaps_gold_check_for_plugin_update($checked_data) {
	global $wpgmaps_gold_api_url, $wpgmaps_gold_plugin_slug, $wp_version, $wpgmza_gold_version;
	
	//Comment out these two lines during testing.
	if (empty($checked_data->checked))
		return $checked_data;
	
        
        
	$args = array(
		'slug' => $wpgmaps_gold_plugin_slug,
		'version' => trim( $wpgmza_gold_version ),
	);
	$request_string = array(
			'body' => array(
				'action' => 'basic_check', 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	
	// Start checking for an update
	$raw_response = wp_remote_post($wpgmaps_gold_api_url, $request_string);
        
    
    $response = false;    
	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = @unserialize($raw_response['body']);
	
	if (is_object($response) && !empty($response)) // Feed the update data into WP updater
		$checked_data->response[$wpgmaps_gold_plugin_slug .'/'. $wpgmaps_gold_plugin_slug .'.php'] = $response;
	
	return $checked_data;
}



add_filter('plugins_api', 'wpgmaps_gold_plugin_api_call', 10, 3);

function wpgmaps_gold_plugin_api_call($def, $action, $args) {
	global $wpgmaps_gold_plugin_slug, $wpgmaps_gold_api_url, $wp_version;
	
	if (!isset($args->slug) || ($args->slug != $wpgmaps_gold_plugin_slug))
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');

    if(empty($plugin_info) || empty($plugin_info->checked) || empty($plugin_info->checked[$wpgmaps_gold_plugin_slug .'/'. $wpgmaps_gold_plugin_slug .'.php'])){
        /* Failure */
        return false;
    }

	$current_version = $plugin_info->checked[$wpgmaps_gold_plugin_slug .'/'. $wpgmaps_gold_plugin_slug .'.php'];
	$args->version = $current_version;
	
	$request_string = array(
			'body' => array(
				'action' => $action, 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	
	$request = wp_remote_post($wpgmaps_gold_api_url, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}


add_action("wpgooglemaps_hook_save_map","wpgooglemaps_gold_hook_control_save_map",10,1);
function wpgooglemaps_gold_hook_control_save_map($map_id) {
    $enable_mass_marker_support = intval($_POST['wpgmza_adv_enable_mass_marker_support']);

    global $wpdb;
    global $wpgmza_tblname_maps;

    $rows_affected = $wpdb->query( $wpdb->prepare(
            "UPDATE $wpgmza_tblname_maps SET
            mass_marker_support = %d
            WHERE id = %d",
            $enable_mass_marker_support,
            $map_id)
    );
}


function wpgmaps_head_gold() {
   if (isset($_POST['wpgmza_save_style_settings'])){

        global $wpdb;
        global $wpgmza_tblname_maps;

        $map_id = $_POST['wpgmza_map_id'];
        /*$styling_enabled = esc_attr($_POST['wpgmza_adv_styling']);
        $styling_json = esc_attr($_POST['wpgmza_adv_styling_json']);*/
        $enable_mass_marker_support = esc_attr($_POST['wpgmza_adv_enable_mass_marker_support']);


        /* $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_maps SET
                styling_enabled = %d,
                styling_json = %s,
                mass_marker_support = %d
                WHERE id = %d",

                $styling_enabled,
                $styling_json,
                $enable_mass_marker_support,
                $map_id)
        );
        */
        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_maps SET
                mass_marker_support = %d
                WHERE id = %d",
                $enable_mass_marker_support,
                $map_id)
        );



//    update_option('WPGMZA_GOLD', $data);
//    $wpgmza_data_gold = get_option('WPGMZA_GOLD');
    echo "
    <div class='updated'>
        ".__("Your settings have been saved.","wp-google-maps")."
    </div>
    ";
   }




}

function wpgmza_cURL_response_gold($action) {
    if (function_exists('curl_version')) {
        global $wpgmza_gold_version;
        global $wpgmza_gold_string;
        $request_url = "http://www.wpgmaps.com/api/rec.php?action=$action&dom=".$_SERVER['HTTP_HOST']."&ver=".$wpgmza_gold_version.$wpgmza_gold_string;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

}
/*
add_action('init', 'wpgmaps_gold_activate_au');
function wpgmaps_gold_activate_au() {
	require_once ('wp_autoupdate.php');
        global $wpgmza_gold_version;
	$wpgmaps_plugin_remote_path = 'http://wpgmaps.com/api/update-gold.php';
	$wptuts_plugin_slug = plugin_basename(__FILE__);
	new wp_auto_update_gold ($wpgmza_gold_version, $wpgmaps_plugin_remote_path, $wptuts_plugin_slug);
}
*/

add_action("wpgmza_wizard_jquery_action","wpgmza_wizard_gold_action_control_jquery");
function wpgmza_wizard_gold_action_control_jquery() {
?>

                    jQuery('#wpgmza_wizard_rtlt_btn').click(function(){
                        updateLink("#wpgmza_wizard_rtlt_btn",  [
                            '#wpgmza-wizard-rtlt-title',
                            '#wpgmza-wizard-rtlt-enabled'
                            ]);
                        window.location = jQuery(this).attr('url');
                    });

<?php


}

/*add_filter("wpgmaps_filter_pro_map_editor_tabs","wpgmaps_gold_filter_control_pro_map_editor_tabs",10,1);
function wpgmaps_gold_filter_control_pro_map_editor_tabs($content) {
    $res = wpgmza_get_map_data($_GET['map_id']);
    $map_other_settings = maybe_unserialize($res->other_settings);
    $content .= "<li style='margin-right: 3px;'><a href=\"#tabs-rtlt\">".__("Location Tracking","wp-google-maps")."</a></li>";
    return $content;
}



add_filter("wpgmaps_filter_pro_map_editor_tab_content","wpgmaps_gold_filter_control_pro_map_editor_tab_content",10,1);
function wpgmaps_gold_filter_control_pro_map_editor_tab_content($content) {
	
	require_once(plugin_dir_path(__FILE__) . 'includes/class.live-tracking-settings-panel.php');
	
    $document = new WPGMZA\LiveTrackingSettingsPanel();
	return "<div id='tabs-rtlt'>" . $document->html . "</div>";
	
}*/



add_filter("wpgmaps_filter_pro_map_editor_tabs","wpgmaps_gold_filter_control_pro_map_editor_tabs_marker_clustering",11,1);
function wpgmaps_gold_filter_control_pro_map_editor_tabs_marker_clustering($content) {
    $content .= "<li style='margin-right: 3px;'><a href=\"#tabs-marker-clustering\">".__("Marker Clustering","wp-google-maps")."</a></li>";
    return $content;
}

add_filter("wpgmaps_filter_pro_map_editor_tab_content","wpgmaps_gold_filter_control_pro_map_editor_tab_content_marker_clustering",11,1);
function wpgmaps_gold_filter_control_pro_map_editor_tab_content_marker_clustering($content) {
    if(!empty($_GET['map_id'])){
        $content .= "<div id='tabs-marker-clustering'>";
        $res = wpgmza_get_map_data($_GET['map_id']);

        $wpgmza_adv_mass_marker_support = array();
        
        if ($res->styling_enabled) { $wpgmza_adv_styling[$res->styling_enabled] = "SELECTED"; } else { $wpgmza_adv_styling[2] = "SELECTED"; }
        if ($res->mass_marker_support) { $wpgmza_adv_mass_marker_support[intval($res->mass_marker_support)] = "SELECTED"; } else { $wpgmza_adv_mass_marker_support[2] = "SELECTED"; }
        
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_adv_mass_marker_support[$i])) { $wpgmza_adv_mass_marker_support[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_adv_styling[$i])) { $wpgmza_adv_styling[$i] = ""; }
        }
        
        $ret = "
                        <table>
                            <tr style='margin-bottom:20px;'>
                                <td>".__("Enable Mass Marker Support","wp-google-maps")."?:</td>
                                <td>
                                    <select id='wpgmza_adv_enable_mass_marker_support' name='wpgmza_adv_enable_mass_marker_support'>
                                        <option value=\"1\" ".$wpgmza_adv_mass_marker_support[1].">".__("Yes","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_adv_mass_marker_support[2].">".__("No","wp-google-maps")."</option>
                                    </select>
                                     <a target='_BLANK' href='".admin_url( 'admin.php?page=wp-google-maps-menu-settings#tabs-gold-cluster')."' class='button-primary'>".__("Advanced Settings","wp-google-maps")."</a>
                                </td>
                             </tr>

                         </table>
        ";


        $content .= $ret;
        $content .= "</div>";
    }
    return $content;  
}




function wpgmaps_gold_rtlt_notice() {
    return "Please note that in order to track your current location you will need to use the WP Go Maps Real Time Location Tracker app available for <a href='https://play.google.com/store/apps/details?id=com.CodeCabin.WPGoogleMapsApp&hl=en' target='_BLANK'>Android</a> (iOS coming soon!)";
}


add_filter("wpgmza_wizard_content_filter", "wpgmza_wizard_item_control_gold_real_time_tracking",11,1);
function wpgmza_wizard_item_control_gold_real_time_tracking($content){
    $content .= "
           <div class='wpgmza-listing-comp wpgmza-listing-wizard'>
                <div class='wpgmza-listing-wizard-1'>
                    <div class='wpmgza-listing-1-icon'>
                        <i class='fa fa-location-arrow'></i>
                    </div>  
                    <h2 style='text-align:center'>".__("Real Time Location Tracking", "wp-google-maps")."</h2>
                </div>
                <div class='wpgmza-listing-wizard-2' style='display:none;'>
                    <div style='font-size:18px'><i class='fa fa-location-arrow'></i> ".__("Real Time Location Tracking", "wp-google-maps")."</div> 
                        <hr>
                        <div style='height:70%;'>
                            <input type='text' wpgmza-key='map_title' style='display:none' id='wpgmza-wizard-rtlt-title' value='".__("Real Time Location Tracking","wp-google-maps")."'>

                            <table style='width:100%; height:100%;'>
                                <tr>
                                    <td align='center' style='height:100%;'>
                                        <span style='display:block; margin-top:auto; margin-bottom:auto;'>".__("Track your location via our app and plot your current location on a map, publicly or privately.", "wp-google-maps")."</span>
                                        <input type='text' wpgmza-other-setting='true' wpgmza-key='rtlt_enabled' class='cmn-toggle cmn-toggle-round-flat' id='wpgmza-wizard-rtlt-enabled' value='1' style='display:none;' />
                                    </td>

                                </tr>
                            </table>
                             
                             
                        </div>
                   <button style='position:absolute;bottom:5px;' class='wpgmza_createmap_btn' id='wpgmza_wizard_rtlt_btn' url=''>".__("Create Map", "wp-google-maps")."</button>
                </div>
            </div>
    ";
    return $content;
}



add_action("init","wpgmza_gold_rtlt_api");
function wpgmza_gold_rtlt_api() {
    if (isset($_POST['wpgmza_action']) && $_POST['wpgmza_action'] == "wpgmza_rtlt") {

        if (isset($_POST)) {

            if (isset($_POST['did'])) {

                $linked_did = get_option("wpgmza_gold_dids");
                //var_dump($linked_did);
                if (is_array($linked_did)) {
                    $safe = 0;
                    foreach ($linked_did as $did => $otp) {
                        if ($did == $_POST['did']) {
                            $safe++;
                        }

                        
                    }
                    if ($safe > 0) {

                        /* we have a linked DID in the system with the sent DID */

                        if ($_POST['action'] == 'add_marker') {
                            $lat = $_POST['lat'];
                            $lng = $_POST['lng'];
                            $mid = $_POST['mid'];
                            $did = $_POST['did'];
                            $mtitle = $_POST['marker_title'];

                            if (!$lat || !$lng || !$mid) { die('error2'); }
                            
                            $res = wpgmza_get_map_data(intval($mid));
                            $map_other_settings = maybe_unserialize($res->other_settings);
                            $def_icon = isset($map_other_settings['upload_default_rtlt_marker']) ? $map_other_settings['upload_default_rtlt_marker'] : '';
                            $address = $lat.",".$lng;
                            $desc_text = sprintf( __( 'Location as at %1$s', 'wp-google-maps' ),
                                date("Y-m-d H:i:s")
                            );
                            global $wpdb;
                            $table_name = $wpdb->prefix . "wpgmza";

                            //Delete if not route
                            if($map_other_settings['rtlt_route'] != 1){
                                $results = $wpdb->get_results("DELETE FROM $table_name WHERE `type` = 1 AND `did` = '".$did."'");
                            } else{
                                $update_results = $wpdb->update($table_name, array( 'other_data' => 'hide'), array( 'type' => 1, 'did' => $did ));
                            }
                            $rows_affected = $wpdb->insert( 
                                $table_name, 
                                array( 
                                    'map_id' => $mid, 
                                    'address' => $address, 
                                    'lat' => $lat, 
                                    'lng' => $lng, 
                                    'pic' => '', 
                                    'link' => '', 
                                    'icon' => $def_icon, 
                                    'anim' => '', 
                                    'title' => $mtitle, 
                                    'infoopen' => '', 
                                    'description' => $desc_text, 
                                    'category' => 0, 
                                    'retina' => 0,
                                    'type' => 1,
                                    'did' => $did,
                                    'other_data' => ''
                                )
                            );
                            die("1");


                        }


                    } else {
                        /* havent seen this DID before, let's send the admin an email to approve it */

                        /* first check if we have sent an email about this DID before so we dont send multiple emails */
                        wpgmza_gold_check_did($_POST['did']);
                    }
                } else {
                    /* havent seen this DID before, let's send the admin an email to approve it */

                    /* first check if we have sent an email about this DID before so we dont send multiple emails */
                    wpgmza_gold_check_did($_POST['did']);
                }


            } else {
                die('0');
            }

        } else {
            die('0');
        }

        
        die('0');
        
    }

    if (isset($_GET['wpgmza_action']) && $_GET['wpgmza_action'] == "accept_did") {
        
        $did = $_GET['did'];
        $otp = $_GET['otp'];

        if (!$did || !$otp) { die(); }
        $linked_did_emails = get_option("wpgmza_gold_did_emails");
        if (isset($linked_did_emails) && $linked_did_emails[$did] == $otp) {
            /* success */
            $linked_did = get_option("wpgmza_gold_dids");
            $linked_did[$did] = $otp;
            update_option("wpgmza_gold_dids",$linked_did);

            if(isset($_GET['via_ajax'])){
                die("1");
            }else{
                die(__("Successfully accepted the device. Thank you","wp-google-maps"));
            }

        } else {
            die(5);
            /* nope.. */

        }


    }

    if (isset($_GET['wpgmza_action']) && $_GET['wpgmza_action'] == "remove_did") {
        
        $did = $_GET['did'];
        $otp = $_GET['otp'];

        if (!$did || !$otp) { die(); }

        $linked_did = get_option("wpgmza_gold_dids");
        $linked_did_emails = get_option("wpgmza_gold_did_emails");

        if (isset($linked_did_emails) && $linked_did_emails[$did] == $otp) {
            //Remove Device From list 
            if(array_key_exists($did , $linked_did_emails)){
                unset($linked_did_emails[$did]);
                update_option("wpgmza_gold_did_emails",$linked_did_emails);
            }
        } 

        if (isset($linked_did) && $linked_did[$did] == $otp) {
            //Remove Device From list 
            if(array_key_exists($did , $linked_did)){
                unset($linked_did[$did]);
                update_option("wpgmza_gold_dids",$linked_did);
            }
        } 

        if(isset($_GET['via_ajax'])){
                die("1");
        }else{
            die(__("Device has been removed. Thank you", "wp-google-maps"));
        }


    }

    if (isset($_GET['wpgmza_action']) && $_GET['wpgmza_action'] == "clear_did_data") {
        
        $did = $_GET['did'];
        $mid = $_GET['mid'];

        if (!$did || !$mid) { die(); }

        global $wpdb;
        $table_name = $wpdb->prefix . "wpgmza";
        
        $results = $wpdb->get_results("DELETE FROM $table_name WHERE `type` = 1 AND `did` = '".$did."' AND `map_id' = '".$mid."'");

        if(isset($_GET['via_ajax'])){
                die("1");
        }else{
            die(__("Marker Data Cleared.", "wp-google-maps"));
        }

    }

}
function wpgmza_gold_check_did($did_to_check) {
    $linked_did_emails = get_option("wpgmza_gold_did_emails");
    //var_dump($linked_did_emails);
    if (is_array($linked_did_emails)) {
        $checked = 0;
        foreach ($linked_did_emails as $did => $otp) {
            
            if ($did == $did_to_check) {
                $checked++;
            }  
            
        }
        if ($checked > 0) {
            
            /* we have already notified the admin of this DID */
            die('Awaiting approval of device');
            
        } else {
            /* lets email the admin now so he can approve this DID */
           wpgmza_gold_add_did_for_approval($did_to_check);

        }
    } else {
        wpgmza_gold_add_did_for_approval($did_to_check);

    }
}
function wpgmza_gold_add_did_for_approval($did) {
    $admin_email = get_option( 'admin_email' );
    $did_otp = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    if ($admin_email) {
        $did_message = __("A new device needs your approval to enable real time location tracking.","wp-google-maps");
        $did_message .= "\n\r".__("Device ID:","wp-google-maps").$did;
        $accept_text = sprintf( __( 'To accept this device, Please follow this link: %1$s', 'wp-google-maps' ),
            get_option('siteurl')."/?wpgmza_action=accept_did&did=".$did."&otp=".$did_otp
        );


        $did_message .= "\n\n\r\r".$accept_text;
        $did_message .= "\n\r".__("To reject the device, simply ignore this email.","wp-google-maps");
        wp_mail($admin_email,__("A new device needs your approval to enable real time location tracking - WP Go Maps","wp-google-maps"),$did_message);
    }
    $linked_did_emails = get_option("wpgmza_gold_did_emails");
    $linked_did_emails[$did] = $did_otp;
    update_option("wpgmza_gold_did_emails",$linked_did_emails);
    die('Approval notice sent');
}






add_filter("wpgooglemaps_filter_map_output","wpgooglemaps_gold_filter_control_map_output",10,2);
function wpgooglemaps_gold_filter_control_map_output($content,$mapid) {
   // $content .= "<button id='wpgmza_rtlt_refresh' mid='$mapid'>Refresh Real Time Data</button>";
    return $content;
}


add_action("wpgooglemaps_hook_user_js_after_core","wpgooglemaps_gold_hook_control_user_js_after_core");
function wpgooglemaps_gold_hook_control_user_js_after_core() {
    $scriptArgs = apply_filters('wpgmza-get-scripts-arguments', array());
    wp_enqueue_script('wpgmaps_gold_core', plugins_url('js/wpgmaps-gold-user.js', __FILE__), false, false, $scriptArgs);
}

function return_marker_array_localized($mapid) {
    $mid = $_POST['map_id'];
    
    $temp_marker_array[$mid] = wpgmaps_return_markers($mid);

    $markers_to_hide = array();
    for($i = 0; $i < count($temp_marker_array[$mid]); $i++){
        if($temp_marker_array[$mid][$i]['other_data'] === "hide"){
            $markers_to_hide[$i] = true; //flag
        }else{
            $markers_to_hide[$i] = false;
        }
    }

    for($i = 0; $i < count($markers_to_hide); $i++){
        if($markers_to_hide[$i]){
            unset($temp_marker_array[$mid][$i]); //Remove Marker from placement
        }
    }

    echo json_encode($temp_marker_array);
    die();


}


add_action( 'wp_ajax_nopriv_wpgmza_refresh_markers', 'return_marker_array_localized');
add_action( 'wp_ajax_wpgmza_refresh_markers','return_marker_array_localized');

function return_route_array_localized() {
    global $wpdb;

    $mid = $_POST['map_id'];
    $table_name = $wpdb->prefix . "wpgmza";

    $sql = "SELECT * FROM $table_name WHERE `map_id` = '$mid' AND `approved` = 1 AND `type` = 1 ";
    $results = $wpdb->get_results($sql);
    //var_dump($results);
    $route_array = array();

    //Create all dids - And all marker data to array
    foreach ( $results as $result ) {  
        $id = $result->id; 
        $lat = $result->lat;
        $lng = $result->lng;
        $did = $result->did;

        if(!array_key_exists($did , $route_array)){
            $route_array[$did] = array(); //Create a new array
        }

        array_push($route_array[$did], array(
            'map_id' => $mid,
            'marker_id' => $id,
            'lat' => $lat,
            'lng' => $lng
        ) );
    }

    echo json_encode($route_array);
    
    die();
}

add_action( 'wp_ajax_nopriv_wpgmza_refresh_routes', 'return_route_array_localized');
add_action( 'wp_ajax_wpgmza_wpgmza_refresh_routes','return_route_array_localized');

function wpgmza_gold_get_device_table_content(){

    $content = "";

    $linked_did = get_option("wpgmza_gold_dids");
    $linked_did_emails = get_option("wpgmza_gold_did_emails");

    if(is_array($linked_did_emails)){
        foreach ($linked_did_emails as $did => $otp) {

            $device_approved = false;

            if(array_key_exists($did, $linked_did)){
                $device_approved = true;
                
            }

            $row_style = $device_approved ? "border-left: 2px solid #0073AA;padding:2px;" : "border-left: 2px solid #b20019;padding:2px;" ;

            $content .= "<tr>";
            $content .= "  <td style='".$row_style."'>".$did."</td>";
            $content .= "  <td>";

            if(!$device_approved){
                $content .= "<a title='Approve Device' wpgmza_action='accept_did' wpgmza_did='".$did."' wpgmza_otp='".$otp."' class='wpgmza_approve_device button'><i class='fa fa-check-circle'></i></a>";
                $content .= "<a title='Revoke Device Access' wpgmza_action='remove_did' wpgmza_did='".$did."' wpgmza_otp='".$otp."' class='wpgmza_remove_device button'><i class='fa fa-times-circle'></i></a>";
            }else{
                $content .= "<a title='Clear Device Data' wpgmza_action='clear_did_data' wpgmza_did='".$did."' wpgmza_mid='".$_GET['map_id']."' class='wpgmza_clear_device button'><i class='fa fa-trash-o'></i></a>";
                $content .= "<a title='Revoke Device Access' wpgmza_action='remove_did' wpgmza_did='".$did."' wpgmza_otp='".$otp."' class='wpgmza_remove_device button'><i class='fa fa-times-circle'></i></a>";
            }

            $content .= "  </td>";
            $content .= "</tr>";
            
        }
    } else {
        $content .= "<tr><td colspan='2'>".__("No devices","wp-google-maps")."</td></tr>";
    }

    return $content;
}



//add_filter("wpgooglemaps_map_settings_output_bottom", "wpgmza_gold_near_vicinity_control_settings", 10, 2);
function wpgmza_gold_near_vicinity_control_settings($content, $wpgmza_settings){
    if (isset($wpgmza_settings['wpgmza_near_vicinity_control_enabled']) && $wpgmza_settings['wpgmza_near_vicinity_control_enabled'] == 'yes') { $wpgmza_near_vicinity_control_enabled_checked = "checked='checked'"; } else { $wpgmza_near_vicinity_control_enabled_checked = ''; }
    if (isset($wpgmza_settings['wpgmza_near_vicinity_aff_radius'])){ $wpgmza_near_vicinity_aff_radius_val = intval($wpgmza_settings['wpgmza_near_vicinity_aff_radius']); } else { $wpgmza_near_vicinity_aff_radius_val = '50'; }
    if (isset($wpgmza_settings['wpgmza_near_vicinity_hide_line']) && $wpgmza_settings['wpgmza_near_vicinity_hide_line'] == 'yes'){ $wpgmza_near_vicinity_hide_line_checked = "checked='checked'"; } else { $wpgmza_near_vicinity_hide_line_checked = ''; }
    if (isset($wpgmza_settings['wpgmza_near_vicinity_line_col'])){ $wpgmza_near_vicinity_line_col_val = htmlspecialchars($wpgmza_settings['wpgmza_near_vicinity_line_col']); } else { $wpgmza_near_vicinity_line_col_val = '#000000'; }
    if (isset($wpgmza_settings['wpgmza_near_vicinity_line_opacity'])){ $wpgmza_near_vicinity_line_opacity_val = floatval($wpgmza_settings['wpgmza_near_vicinity_line_opacity']); } else { $wpgmza_near_vicinity_line_opacity_val = '1.0'; }
    if (isset($wpgmza_settings['wpgmza_near_vicinity_line_thickness'])){ $wpgmza_near_vicinity_line_thickness_val = intval($wpgmza_settings['wpgmza_near_vicinity_line_thickness']); } else { $wpgmza_near_vicinity_line_thickness_val = '1'; }
    if (isset($wpgmza_settings['wpgmza_near_vicinity_shape']) && $wpgmza_settings['wpgmza_near_vicinity_shape'] == 'yes') { $wpgmza_near_vicinity_shape_checked = "checked='checked'"; } else { $wpgmza_near_vicinity_shape_checked = ''; }

    $ret = "";
    $ret .= "<h3>".__("Near-Vicinity Marker Control Settings","wp-google-maps")."</h3>";
    $ret .= "<table class='form-table'>";
    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Enable Near-Vicinity Marker Control","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <div class='switch'><input name='wpgmza_near_vicinity_control_enabled' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza_near_vicinity_control_enabled' value='yes' $wpgmza_near_vicinity_control_enabled_checked /> <label for='wpgmza_near_vicinity_control_enabled' data-on='".__("Yes", "wp-google-maps")."' data-off='".__("No", "wp-google-maps")."'></label></div>";
    $ret .= "    </td>";
    $ret .= "  </tr>";

    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Near-Vicinity Affected Radius","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <input name='wpgmza_near_vicinity_aff_radius' type='number' id='wpgmza_near_vicinity_aff_radius' value='$wpgmza_near_vicinity_aff_radius_val' placeholder='50'/> " . __("Meters (This is an approximate value)", "wp-google-maps")."";
    $ret .= "    </td>";
    $ret .= "  </tr>";

    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Near-Vicinity Hide Lines","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <div class='switch'><input name='wpgmza_near_vicinity_hide_line' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza_near_vicinity_hide_line' value='yes' $wpgmza_near_vicinity_hide_line_checked /> <label for='wpgmza_near_vicinity_hide_line' data-on='".__("Yes", "wp-google-maps")."' data-off='".__("No", "wp-google-maps")."'></label></div>";
    $ret .= "    </td>";
    $ret .= "  </tr>";

    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Near-Vicinity Line Color","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <input name='wpgmza_near_vicinity_line_col' type='color' id='wpgmza_near_vicinity_line_col' value='$wpgmza_near_vicinity_line_col_val' placeholder='#000000'/> ";
    $ret .= "    </td>";
    $ret .= "  </tr>";

    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Near-Vicinity Line Opacity","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <input name='wpgmza_near_vicinity_line_opacity' type='text' id='wpgmza_near_vicinity_line_opacity' value='$wpgmza_near_vicinity_line_opacity_val' placeholder='1.0'/> (".__("Value between 0.1 and 1.0", "wp-google-maps").")";
    $ret .= "    </td>";
    $ret .= "  </tr>";

    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Near-Vicinity Line Thinkness","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <input name='wpgmza_near_vicinity_line_thickness' type='text' id='wpgmza_near_vicinity_line_thickness' value='$wpgmza_near_vicinity_line_thickness_val' placeholder='1'/> (".__("Value between 1 and 50", "wp-google-maps").")";
    $ret .= "    </td>";
    $ret .= "  </tr>";

    $ret .= "  <tr>";
    $ret .= "    <td width='400' valign='top'>".__("Near-Vicinity Shape","wp-google-maps").":</td>";
    $ret .= "    <td>";
    $ret .= "      <div class='switch'><input name='wpgmza_near_vicinity_shape' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza_near_vicinity_shape' value='yes' $wpgmza_near_vicinity_shape_checked /> <label for='wpgmza_near_vicinity_shape' data-on='".__("Spiral", "wp-google-maps")."' data-off='".__("Circle", "wp-google-maps")."'></label></div>";
    $ret .= "    </td>";
    $ret .= "  </tr>";


    $ret .= "</table>";

    return $ret;

}

add_filter("wpgooglemaps_filter_save_settings", "wpgmza_gold_near_vicinity_control_settings_save", 10, 1);
function wpgmza_gold_near_vicinity_control_settings_save($wpgmza_data){
    if (isset($_POST['wpgmza_near_vicinity_control_enabled'])) { 
        $wpgmza_data['wpgmza_near_vicinity_control_enabled'] = esc_attr($_POST['wpgmza_near_vicinity_control_enabled']); 
    } else {  
        $wpgmza_data['wpgmza_near_vicinity_control_enabled'] = ""; 
    }

    if (isset($_POST['wpgmza_near_vicinity_aff_radius'])) { 
        $wpgmza_data['wpgmza_near_vicinity_aff_radius'] = intval($_POST['wpgmza_near_vicinity_aff_radius']); 
    } else {  
        $wpgmza_data['wpgmza_near_vicinity_aff_radius'] = 50; 
    }

    if (isset($_POST['wpgmza_near_vicinity_hide_line'])) { 
        $wpgmza_data['wpgmza_near_vicinity_hide_line'] = esc_attr($_POST['wpgmza_near_vicinity_hide_line']); 
    } else {  
        $wpgmza_data['wpgmza_near_vicinity_hide_line'] = ""; 
    }

    if (isset($_POST['wpgmza_near_vicinity_line_col'])) { 
        $wpgmza_data['wpgmza_near_vicinity_line_col'] = esc_attr($_POST['wpgmza_near_vicinity_line_col']); 
    } else {  
        $wpgmza_data['wpgmza_near_vicinity_line_col'] = ""; 
    }

    if (isset($_POST['wpgmza_near_vicinity_line_opacity'])) { 
        $opacity = floatval($_POST['wpgmza_near_vicinity_line_opacity']);
        if($opacity > 1.0){
           $opacity = 1.0; 
        }

        if($opacity < 0.1){
           $opacity = 0.1; 
        }

        $wpgmza_data['wpgmza_near_vicinity_line_opacity'] = $opacity; 

    } else {  
        $wpgmza_data['wpgmza_near_vicinity_line_opacity'] = "1.0"; 
    }

    if (isset($_POST['wpgmza_near_vicinity_line_thickness'])) { 
        $thickness = intval($_POST['wpgmza_near_vicinity_line_thickness']);
        if($thickness > 50){
           $thickness = 1; 
        }

        if($thickness < 1){
           $thickness = 1; 
        }

        $wpgmza_data['wpgmza_near_vicinity_line_thickness'] = $thickness; 

    } else {  
        $wpgmza_data['wpgmza_near_vicinity_line_thickness'] = "1"; 
    }

    if (isset($_POST['wpgmza_near_vicinity_shape'])) { 
        $wpgmza_data['wpgmza_near_vicinity_shape'] = esc_attr($_POST['wpgmza_near_vicinity_shape']); 
    } else {  
        $wpgmza_data['wpgmza_near_vicinity_shape'] = ""; 
    }


    return $wpgmza_data;
}

add_action("wpgooglemaps_hook_user_js_after_core", "wpgmza_gold_near_vicinity_scripts");
function wpgmza_gold_near_vicinity_scripts(){
    global $wpgmza_gold_version;
	
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
	
    if (isset($wpgmza_settings['wpgmza_near_vicinity_control_enabled']) && $wpgmza_settings['wpgmza_near_vicinity_control_enabled'] == 'yes') { 
	
		if(!empty($wpgmza_settings['marker_separator_use_legacy_module']))
		{
			wp_enqueue_script('wpgmaps_nvc', plugins_url('js/wpgmaps-gold-near-vicinity-marker-control.js', __FILE__), array('wpgmaps_core'), $wpgmza_gold_version.'g' , false);
		}
		else
		{
			// NB: Removed as of 5.0.0
			// wp_enqueue_script('wpgmza_marker_separator', plugin_dir_url(__FILE__) . 'js/v8/marker-separator-group.js', array('wpgmza'), $wpgmza_gold_version);
			// wp_enqueue_script('wpgmza_marker_separator_group', plugin_dir_url(__FILE__) . 'js/v8/marker-separator.js', array('wpgmza'), $wpgmza_gold_version);
		}
    }

	if(!empty($wpgmza_settings['marker_separator_use_legacy_module']))
	{
		$affected_radius = 50; //default
		if (isset($wpgmza_settings['wpgmza_near_vicinity_aff_radius']) && $wpgmza_settings['wpgmza_near_vicinity_aff_radius'] != "") {
			$affected_radius = intval($wpgmza_settings['wpgmza_near_vicinity_aff_radius']);
		} 

		wp_localize_script( 'wpgmaps_nvc', 'wpgmza_nvc_affected_radius', ($affected_radius / 100000) . "");

		if (isset($wpgmza_settings['wpgmza_near_vicinity_hide_line']) && $wpgmza_settings['wpgmza_near_vicinity_hide_line'] == 'yes') { 
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_hide_webs', "true");
		}

		if (isset($wpgmza_settings['wpgmza_near_vicinity_line_col'])) { 
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_line_col', $wpgmza_settings['wpgmza_near_vicinity_line_col']);
		} else {
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_line_col', "#000000");
		}

		if (isset($wpgmza_settings['wpgmza_near_vicinity_line_opacity'])) { 
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_line_opacity', $wpgmza_settings['wpgmza_near_vicinity_line_opacity'] . "");
		} else {
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_line_opacity', "1.0");
		}

		if (isset($wpgmza_settings['wpgmza_near_vicinity_line_thickness'])) { 
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_line_thickness', $wpgmza_settings['wpgmza_near_vicinity_line_thickness'] . "");
		} else {
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_line_thickness', "1");
		}

		if (isset($wpgmza_settings['wpgmza_near_vicinity_shape']) && $wpgmza_settings['wpgmza_near_vicinity_shape'] == 'yes') { 
			wp_localize_script( 'wpgmaps_nvc', 'wpgmza_near_vicinity_shape', "true");
		}
	}
}

add_filter("wpgmza_global_settings_tabs", "wpgmza_gold_cluster_settings_tab", 10, 1);
function wpgmza_gold_cluster_settings_tab($content){
    $content .= "<li><a href='#tabs-gold-cluster'>".__("Marker Clustering","wp-google-maps")."</a></li>";
    return $content;
}

add_filter("wpgmza_global_settings_tab_content", "wpgmza_gold_cluster_settings_tab_content", 10, 1);
function wpgmza_gold_cluster_settings_tab_content($content){
    global $wpgmza;
    wp_enqueue_media();

    $wpgmza_gold_clustering_data = get_option('WPGMZA_OTHER_SETTINGS', "false");


    //Cluster Icon Defaults
    $wpgmza_gold_cluster_level1 = plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m1.png";
    $wpgmza_gold_cluster_level1_width = 53;
    $wpgmza_gold_cluster_level1_height = 53;
    $wpgmza_gold_cluster_level2 = plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m2.png";
    $wpgmza_gold_cluster_level2_width = 56;
    $wpgmza_gold_cluster_level2_height = 56;
    $wpgmza_gold_cluster_level3 = plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m3.png";
    $wpgmza_gold_cluster_level3_width = 66;
    $wpgmza_gold_cluster_level3_height = 66;
    $wpgmza_gold_cluster_level4 = plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m4.png";
    $wpgmza_gold_cluster_level4_width = 78;
    $wpgmza_gold_cluster_level4_height = 78;
    $wpgmza_gold_cluster_level5 = plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m5.png";
    $wpgmza_gold_cluster_level5_width = 90;
    $wpgmza_gold_cluster_level5_height = 90;

    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_advanced_enabled']) && $wpgmza_gold_clustering_data['wpgmza_cluster_advanced_enabled'] == 'yes'){ $wpgmza_cluster_advanced_enabled_checked = "checked=checked";  } else { $wpgmza_cluster_advanced_enabled_checked = ""; }
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_grid_size'])){ $wpgmza_cluster_grid_size = intval($wpgmza_gold_clustering_data['wpgmza_cluster_grid_size']);  } else { $wpgmza_cluster_grid_size = 20; }
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_max_zoom'])){ $wpgmza_cluster_max_zoom = intval($wpgmza_gold_clustering_data['wpgmza_cluster_max_zoom']);  } else { $wpgmza_cluster_max_zoom = 15; }
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_min_cluster_size'])){ $wpgmza_cluster_min_cluster_size = intval($wpgmza_gold_clustering_data['wpgmza_cluster_min_cluster_size']);  } else { $wpgmza_cluster_min_cluster_size = 2; }

    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_font_color'])){ $wpgmza_cluster_font_color = esc_attr($wpgmza_gold_clustering_data['wpgmza_cluster_font_color']);  } else { $wpgmza_cluster_font_color = "#000000"; }
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_font_size'])){ $wpgmza_cluster_font_size = intval($wpgmza_gold_clustering_data['wpgmza_cluster_font_size']);  } else { $wpgmza_cluster_font_size = 12; }


    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_zoom_click']) && $wpgmza_gold_clustering_data['wpgmza_cluster_zoom_click'] == 'yes'){ $wpgmza_cluster_zoom_click_checked = "checked=checked";  } else { $wpgmza_cluster_zoom_click_checked = ""; }

    //Icon Parsing - URL
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level1'])){ $wpgmza_gold_cluster_level1 = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level1']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level3'])){ $wpgmza_gold_cluster_level2 = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level2']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level3'])){ $wpgmza_gold_cluster_level3 = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level3']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level4'])){ $wpgmza_gold_cluster_level4 = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level4']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level5'])){ $wpgmza_gold_cluster_level5 = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level5']);}

    //Icon Parsing - width
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level1_width'])){ $wpgmza_gold_cluster_level1_width = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level1_width']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level2_width'])){ $wpgmza_gold_cluster_level2_width = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level2_width']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level3_width'])){ $wpgmza_gold_cluster_level3_width = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level3_width']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level4_width'])){ $wpgmza_gold_cluster_level4_width = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level4_width']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level5_width'])){ $wpgmza_gold_cluster_level5_width = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level5_width']);}

    //Icon Parsing - height
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level1_height'])){ $wpgmza_gold_cluster_level1_height = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level1_height']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level2_height'])){ $wpgmza_gold_cluster_level2_height = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level2_height']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level3_height'])){ $wpgmza_gold_cluster_level3_height = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level3_height']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level4_height'])){ $wpgmza_gold_cluster_level4_height = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level4_height']);}
    if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_level5_height'])){ $wpgmza_gold_cluster_level5_height = urldecode($wpgmza_gold_clustering_data['wpgmza_cluster_level5_height']);}

    
    $content .= wpgmza_gold_cluster_settings_push_js();

    if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && $wpgmza->goldAddOn->checkCoreAtlasNovus()){
        /* TODO: This conditional introduces duplication. It should load a template and populate it */
        /* For the moment, we are doing it this way, but in the future, we need to rebuild this as it creates points of failure */

        $content .= "<div id='tabs-gold-cluster'>";
        $content .=     "<div class='heading'>" . __("Marker Clustering","wp-google-maps") . "</div>";

        // Helper
        $content .=     "<div class='general-heading-notice'>";
        $content .=         "<p>" . __("Changing these settings is only suggested for experienced users.","wp-google-maps") . "</p>";
        $content .=     "</div>";

        // Enabled
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Enable Advanced Options","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<div class='switch'>";
        $content .=             "<input name='wpgmza_cluster_advanced_enabled' id='wpgmza_cluster_advanced_enabled' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' value='yes' />";
        $content .=             "<label for='wpgmza_cluster_advanced_enabled'></label>";
        $content .=         "</div>";
        $content .=     "</div>";

        // Grid size
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Grid Size","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<input name='wpgmza_cluster_grid_size' id='wpgmza_cluster_grid_size' value='$wpgmza_cluster_grid_size' type='number' />";
        $content .=     "</div>";

        // Max zoom
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Max Zoom","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<input name='wpgmza_cluster_max_zoom' id='wpgmza_cluster_max_zoom' value='$wpgmza_cluster_max_zoom' type='number' />";
        $content .=     "</div>";

        // Min cluster size
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Minimum Cluster Size","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<input name='wpgmza_cluster_min_cluster_size' id='wpgmza_cluster_min_cluster_size' value='$wpgmza_cluster_min_cluster_size' type='number' />";
        $content .=     "</div>";

        // Cluster Font Color
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Cluster Font Color","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<input name='wpgmza_cluster_font_color' id='wpgmza_cluster_font_color' value='$wpgmza_cluster_font_color' type='color' />";
        $content .=     "</div>";

        // Cluster Font Size
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Cluster Font Size","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<input name='wpgmza_cluster_font_size' id='wpgmza_cluster_font_size' value='$wpgmza_cluster_font_size' type='number' />";
        $content .=     "</div>";

        // Zoom on click
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Zoom On Click","wp-google-maps");
        $content .=         "</div>";

        $content .=         "<div class='switch'>";
        $content .=             "<input name='wpgmza_cluster_zoom_click' id='wpgmza_cluster_zoom_click' class='cmn-toggle cmn-toggle-round-flat' type='checkbox' value='yes' />";
        $content .=             "<label for='wpgmza_cluster_zoom_click'></label>";
        $content .=         "</div>";
        $content .=     "</div>";


        $content .=     "<div class='tab-row'><strong>" . __("Icons", "wp-google-maps") . "</strong></div>";

        //Level 1
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Level 1","wp-google-maps");
        $content .=         "</div>";

        $content .=          "<div>";
        $content .=             "<img style='max-width:30px;' id='wpgmza_cluster_level1_img' src='{$wpgmza_gold_cluster_level1}' /> ";
        $content .=             "<input type='text' value='{$wpgmza_gold_cluster_level1}' name='wpgmza_cluster_level1' id='wpgmza_cluster_level1' readonly /> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level1_img' wpgmza-rel-input='wpgmza_cluster_level1'>";
        $content .=                 __("Change","wp-google-maps");
        $content .=             "</a> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level1' wpgmza-rel-size1='wpgmza_cluster_level1_width' wpgmza-rel-size2='wpgmza_cluster_level1_height'  wpgmza-rel-img='wpgmza_cluster_level1_img' wpgmza-rel-level='1'>";
        $content .=                 __("Reset","wp-google-maps");
        $content .=             "</a>";
        $content .=          "</div>";
        $content .=     "</div>";

        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'></div>";
        $content .=         "<div>";
        $content .=             __("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level1_width' name='wpgmza_cluster_level1_width' id='wpgmza_cluster_level1_width'/> ";
        $content .=             __("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level1_height' name='wpgmza_cluster_level1_height' id='wpgmza_cluster_level1_height' type='number' />";
        $content .=         "</div>";
        $content .=     "</div>";

        //Level 2
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Level 2","wp-google-maps");
        $content .=         "</div>";

        $content .=          "<div>";
        $content .=             "<img style='max-width:30px;' id='wpgmza_cluster_level2_img' src='{$wpgmza_gold_cluster_level2}' /> ";
        $content .=             "<input type='text' value='{$wpgmza_gold_cluster_level2}' name='wpgmza_cluster_level2' id='wpgmza_cluster_level2' readonly /> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level2_img' wpgmza-rel-input='wpgmza_cluster_level2'>";
        $content .=                 __("Change","wp-google-maps");
        $content .=             "</a> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level2' wpgmza-rel-size1='wpgmza_cluster_level2_width' wpgmza-rel-size2='wpgmza_cluster_level2_height'  wpgmza-rel-img='wpgmza_cluster_level2_img' wpgmza-rel-level='2'>";
        $content .=                 __("Reset","wp-google-maps");
        $content .=             "</a>";
        $content .=          "</div>";
        $content .=     "</div>";

        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'></div>";
        $content .=         "<div>";
        $content .=             __("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level2_width' name='wpgmza_cluster_level2_width' id='wpgmza_cluster_level2_width'/> ";
        $content .=             __("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level2_height' name='wpgmza_cluster_level2_height' id='wpgmza_cluster_level2_height' type='number' />";
        $content .=         "</div>";
        $content .=     "</div>";

        //Level 3
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Level 3","wp-google-maps");
        $content .=         "</div>";

        $content .=          "<div>";
        $content .=             "<img style='max-width:30px;' id='wpgmza_cluster_level3_img' src='{$wpgmza_gold_cluster_level3}' /> ";
        $content .=             "<input type='text' value='{$wpgmza_gold_cluster_level3}' name='wpgmza_cluster_level3' id='wpgmza_cluster_level3' readonly /> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level3_img' wpgmza-rel-input='wpgmza_cluster_level3'>";
        $content .=                 __("Change","wp-google-maps");
        $content .=             "</a> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level3' wpgmza-rel-size1='wpgmza_cluster_level3_width' wpgmza-rel-size2='wpgmza_cluster_level3_height'  wpgmza-rel-img='wpgmza_cluster_level3_img' wpgmza-rel-level='3'>";
        $content .=                 __("Reset","wp-google-maps");
        $content .=             "</a>";
        $content .=          "</div>";
        $content .=     "</div>";

        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'></div>";
        $content .=         "<div>";
        $content .=             __("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level3_width' name='wpgmza_cluster_level3_width' id='wpgmza_cluster_level3_width'/> ";
        $content .=             __("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level3_height' name='wpgmza_cluster_level3_height' id='wpgmza_cluster_level3_height' type='number' />";
        $content .=         "</div>";
        $content .=     "</div>";

        //Level 4
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Level 4","wp-google-maps");
        $content .=         "</div>";

        $content .=          "<div>";
        $content .=             "<img style='max-width:30px;' id='wpgmza_cluster_level4_img' src='{$wpgmza_gold_cluster_level4}' /> ";
        $content .=             "<input type='text' value='{$wpgmza_gold_cluster_level4}' name='wpgmza_cluster_level4' id='wpgmza_cluster_level4' readonly /> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level4_img' wpgmza-rel-input='wpgmza_cluster_level4'>";
        $content .=                 __("Change","wp-google-maps");
        $content .=             "</a> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level4' wpgmza-rel-size1='wpgmza_cluster_level4_width' wpgmza-rel-size2='wpgmza_cluster_level4_height'  wpgmza-rel-img='wpgmza_cluster_level4_img' wpgmza-rel-level='4'>";
        $content .=                 __("Reset","wp-google-maps");
        $content .=             "</a>";
        $content .=          "</div>";
        $content .=     "</div>";

        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'></div>";
        $content .=         "<div>";
        $content .=             __("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level4_width' name='wpgmza_cluster_level4_width' id='wpgmza_cluster_level4_width'/> ";
        $content .=             __("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level4_height' name='wpgmza_cluster_level4_height' id='wpgmza_cluster_level4_height' type='number' />";
        $content .=         "</div>";
        $content .=     "</div>";

        //Level 5
        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'>";
        $content .=             __("Level 5","wp-google-maps");
        $content .=         "</div>";

        $content .=          "<div>";
        $content .=             "<img style='max-width:30px;' id='wpgmza_cluster_level5_img' src='{$wpgmza_gold_cluster_level5}' /> ";
        $content .=             "<input type='text' value='{$wpgmza_gold_cluster_level5}' name='wpgmza_cluster_level5' id='wpgmza_cluster_level5' readonly /> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level5_img' wpgmza-rel-input='wpgmza_cluster_level5'>";
        $content .=                 __("Change","wp-google-maps");
        $content .=             "</a> ";
        
        $content .=             "<a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level5' wpgmza-rel-size1='wpgmza_cluster_level5_width' wpgmza-rel-size2='wpgmza_cluster_level5_height'  wpgmza-rel-img='wpgmza_cluster_level5_img' wpgmza-rel-level='5'>";
        $content .=                 __("Reset","wp-google-maps");
        $content .=             "</a>";
        $content .=          "</div>";
        $content .=     "</div>";

        $content .=     "<div class='tab-row'>";
        $content .=         "<div class='title'></div>";
        $content .=         "<div>";
        $content .=             __("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level5_width' name='wpgmza_cluster_level5_width' id='wpgmza_cluster_level5_width'/> ";
        $content .=             __("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level5_height' name='wpgmza_cluster_level5_height' id='wpgmza_cluster_level5_height' type='number' />";
        $content .=         "</div>";
        $content .=     "</div>";


        $content .= "</div>";

    } else {
        // Very very legacy
        $content .= "<div id='tabs-gold-cluster'>";
        $content .=     "<h3>".__("Marker Clustering - Advanced Settings","wp-google-maps")."</h3>";
        $content .=     "<p>".__("Changing these settings is only suggested for experienced users.","wp-google-maps")."</p>";
        $content .=     "<hr/>";
        $content .=     "<table class='form-table'>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Enable Advanced Options","wp-google-maps")."</td>";
        $content .=         "<td>";
        $content .=             "<div class='switch'>";
        $content .=               "<input name='wpgmza_cluster_advanced_enabled' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza_cluster_advanced_enabled' value='yes' $wpgmza_cluster_advanced_enabled_checked />";
        $content .=               "<label for='wpgmza_cluster_advanced_enabled' data-on='".__("Yes", "wp-google-maps")."' data-off='".__("No", "wp-google-maps")."'></label>";
        $content .=             "</div>";
        $content .=         "</td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>";
        $content .=             "<h4>".__("Options","wp-google-maps")."</h4>";
        $content .=         "</td><td></td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Grid Size","wp-google-maps")."</td>";
        $content .=         "<td><input name='wpgmza_cluster_grid_size' id='wpgmza_cluster_grid_size' value='$wpgmza_cluster_grid_size' type='number' /></td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Max Zoom","wp-google-maps")."</td>";
        $content .=         "<td><input name='wpgmza_cluster_max_zoom' id='wpgmza_cluster_max_zoom' value='$wpgmza_cluster_max_zoom' type='number' /></td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Minimum Cluster Size","wp-google-maps")."</td>";
        $content .=         "<td><input name='wpgmza_cluster_min_cluster_size' id='wpgmza_cluster_min_cluster_size' value='$wpgmza_cluster_min_cluster_size' type='number' /></td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Cluster Font Color","wp-google-maps")."</td>";
        $content .=         "<td><input name='wpgmza_cluster_font_color' id='wpgmza_cluster_font_color' value='$wpgmza_cluster_font_color' type='color' /></td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Cluster Font Size","wp-google-maps")."</td>";
        $content .=         "<td><input name='wpgmza_cluster_font_size' id='wpgmza_cluster_font_size' value='$wpgmza_cluster_font_size' type='number' /></td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Zoom On Click","wp-google-maps")."</td>";
        $content .=         "<td>";
        $content .=             "<div class='switch'>";
        $content .=               "<input id='wpgmza_cluster_zoom_click' name='wpgmza_cluster_zoom_click' type='checkbox' class='cmn-toggle cmn-toggle-yes-no' value='yes' $wpgmza_cluster_zoom_click_checked />";
        $content .=               "<label for='wpgmza_cluster_zoom_click' data-on='".__("Yes", "wp-google-maps")."' data-off='".__("No", "wp-google-maps")."'></label>";
        $content .=             "</div>";
        $content .=         "</td>";
        $content .=       "</tr>";

        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>";
        $content .=             "<h4>".__("Cluster Icons","wp-google-maps")."</h4>";
        $content .=         "</td>";
        $content .=         "<td></td>";
        $content .=       "</tr>";

        //Level1
        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Level 1","wp-google-maps")."</td>";
        $content .=         "<td><img style='max-width:30px;' id='wpgmza_cluster_level1_img' src='$wpgmza_gold_cluster_level1' /> <input type='text' value='$wpgmza_gold_cluster_level1' name='wpgmza_cluster_level1' id='wpgmza_cluster_level1' readonly /> <a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level1_img' wpgmza-rel-input='wpgmza_cluster_level1'>".__("Change","wp-google-maps")."</a> <a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level1' wpgmza-rel-size1='wpgmza_cluster_level1_width' wpgmza-rel-size2='wpgmza_cluster_level1_height'  wpgmza-rel-img='wpgmza_cluster_level1_img' wpgmza-rel-level='1'>".__("Reset","wp-google-maps")."</a></td>";
        $content .=       "</tr>";
        $content .=       "<tr><td></td>";
        $content .=         "<td>".__("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level1_width' name='wpgmza_cluster_level1_width' id='wpgmza_cluster_level1_width'/> ".__("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level1_height' name='wpgmza_cluster_level1_height' id='wpgmza_cluster_level1_height' type='number' /></td>";
        $content .=       "</tr>";

        //Level2
        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Level 2","wp-google-maps")."</td>";
        $content .=         "<td><img style='max-width:30px;' id='wpgmza_cluster_level2_img' src='$wpgmza_gold_cluster_level2' /> <input type='text' value='$wpgmza_gold_cluster_level2' name='wpgmza_cluster_level2' id='wpgmza_cluster_level2' readonly /> <a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level2_img' wpgmza-rel-input='wpgmza_cluster_level2'>".__("Change","wp-google-maps")."</a> <a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level2' wpgmza-rel-size1='wpgmza_cluster_level2_width' wpgmza-rel-size2='wpgmza_cluster_level2_height' wpgmza-rel-img='wpgmza_cluster_level2_img' wpgmza-rel-level='2'>".__("Reset","wp-google-maps")."</a></td>";
        $content .=       "</tr>";
        $content .=       "<tr><td></td>";
        $content .=         "<td>".__("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level2_width' name='wpgmza_cluster_level2_width' id='wpgmza_cluster_level2_width'/> ".__("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level2_height' name='wpgmza_cluster_level2_height' id='wpgmza_cluster_level2_height' type='number' /></td>";
        $content .=       "</tr>";

        //Level3
        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Level 3","wp-google-maps")."</td>";
        $content .=         "<td><img style='max-width:30px;' id='wpgmza_cluster_level3_img' src='$wpgmza_gold_cluster_level3' /> <input type='text' value='$wpgmza_gold_cluster_level3' name='wpgmza_cluster_level3' id='wpgmza_cluster_level3' readonly /> <a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level3_img' wpgmza-rel-input='wpgmza_cluster_level3'>".__("Change","wp-google-maps")."</a> <a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level3' wpgmza-rel-size1='wpgmza_cluster_level3_width' wpgmza-rel-size2='wpgmza_cluster_level3_height' wpgmza-rel-img='wpgmza_cluster_level3_img' wpgmza-rel-level='3'>".__("Reset","wp-google-maps")."</a></td>";
        $content .=       "</tr>";
        $content .=       "<tr><td></td>";
        $content .=         "<td>".__("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level3_width' name='wpgmza_cluster_level3_width' id='wpgmza_cluster_level3_width'/> ".__("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level3_height' name='wpgmza_cluster_level3_height' id='wpgmza_cluster_level3_height' type='number' /></td>";
        $content .=       "</tr>";

        //Level4
        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Level 4","wp-google-maps")."</td>";
        $content .=         "<td><img style='max-width:30px;' id='wpgmza_cluster_level4_img' src='$wpgmza_gold_cluster_level4' /> <input type='text' value='$wpgmza_gold_cluster_level4' name='wpgmza_cluster_level4' id='wpgmza_cluster_level4' readonly /> <a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level4_img' wpgmza-rel-input='wpgmza_cluster_level4'>".__("Change","wp-google-maps")."</a> <a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level4' wpgmza-rel-size1='wpgmza_cluster_level4_width' wpgmza-rel-size2='wpgmza_cluster_level4_height' wpgmza-rel-img='wpgmza_cluster_level4_img' wpgmza-rel-level='4'>".__("Reset","wp-google-maps")."</a></td>";
        $content .=       "</tr>";
        $content .=       "<tr><td></td>";
        $content .=         "<td>".__("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level4_width' name='wpgmza_cluster_level4_width' id='wpgmza_cluster_level4_width'/> ".__("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level4_height' name='wpgmza_cluster_level4_height' id='wpgmza_cluster_level4_height' type='number' /></td>";
        $content .=       "</tr>";

        //Level5
        $content .=       "<tr>";
        $content .=         "<td width='200' valign='top' style='vertical-align:top;'>".__("Level 5","wp-google-maps")."</td>";
        $content .=         "<td><img style='max-width:30px;' id='wpgmza_cluster_level5_img' src='$wpgmza_gold_cluster_level5' /> <input type='text' value='$wpgmza_gold_cluster_level5' name='wpgmza_cluster_level5' id='wpgmza_cluster_level5' readonly /> <a class='button-primary wpgmza-cluster-icon-change' wpgmza-rel-img='wpgmza_cluster_level5_img' wpgmza-rel-input='wpgmza_cluster_level5'>".__("Change","wp-google-maps")."</a> <a class='button-primary wpgmza-cluster-icon-reset' wpgmza-rel-input='wpgmza_cluster_level5' wpgmza-rel-size1='wpgmza_cluster_level5_width' wpgmza-rel-size2='wpgmza_cluster_level5_height' wpgmza-rel-img='wpgmza_cluster_level5_img' wpgmza-rel-level='5'>".__("Reset","wp-google-maps")."</a></td>";
        $content .=       "</tr>";
        $content .=       "<tr><td></td>";
        $content .=         "<td>".__("Width", "wp-google-maps").": <input type='number' value='$wpgmza_gold_cluster_level5_width' name='wpgmza_cluster_level5_width' id='wpgmza_cluster_level5_width'/> ".__("Height", "wp-google-maps").": <input value='$wpgmza_gold_cluster_level5_height' name='wpgmza_cluster_level5_height' id='wpgmza_cluster_level5_height' type='number' /></td>";
        $content .=       "</tr>";


        $content .=     "</table>";
        $content .= "</div>";
    }

    return $content;
}

function wpgmza_gold_cluster_settings_push_js(){
    $scripts = "<script>";
    
    $scripts .= "var media_uploader = null;
                function wpgmza_open_media_uploader_image(current_input, current_image){
                    media_uploader = wp.media({
                        frame:    'post', 
                        state:    'insert', 
                        multiple: false
                    });

                    media_uploader.on('insert', function(){
                        var json = media_uploader.state().get('selection').first().toJSON();
                        var image_url = json.url;

                        jQuery('#' + current_input).val(image_url);
                        jQuery('#' + current_image).attr('src', image_url);

                    });

                    media_uploader.open();
                }";

    $scripts .= "
                var wpgmza_cluster_resets = { 
                    '1' : {
                        'url' : '" . plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m1.png',
                        'size1' : '53',
                        'size2' : '53'
                    },
                    '2' : {
                        'url' : '" . plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m2.png',
                        'size1' : '56',
                        'size2' : '56'
                    },
                    '3' : {
                        'url' : '" . plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m3.png',
                        'size1' : '66',
                        'size2' : '66'
                    },
                    '4' : {
                        'url' : '" . plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m4.png',
                        'size1' : '78',
                        'size2' : '78'
                    },
                    '5' : {
                        'url' : '" . plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m5.png',
                        'size1' : '90',
                        'size2' : '90'
                    }
                };

                jQuery(function(){
                    jQuery(document).ready(function(){
                        jQuery('.wpgmza-cluster-icon-change').click(function(){
                            var the_img = jQuery(this).attr('wpgmza-rel-img');
                            var the_input = jQuery(this).attr('wpgmza-rel-input');

                            wpgmza_open_media_uploader_image(the_input, the_img);
                        });

                        jQuery('.wpgmza-cluster-icon-reset').click(function(){
                            var the_id = jQuery(this).attr('wpgmza-rel-level');
                            var the_img = jQuery(this).attr('wpgmza-rel-img');
                            var the_input = jQuery(this).attr('wpgmza-rel-input');
                            var the_size1 = jQuery(this).attr('wpgmza-rel-size1');
                            var the_size2 = jQuery(this).attr('wpgmza-rel-size2');

                            jQuery('#' + the_input).val(wpgmza_cluster_resets[the_id]['url']);
                            jQuery('#' + the_img).attr('src', wpgmza_cluster_resets[the_id]['url']);
                            jQuery('#' + the_size1).val(wpgmza_cluster_resets[the_id]['size1']);
                            jQuery('#' + the_size2).val(wpgmza_cluster_resets[the_id]['size2']);
                        });
                    });
                });";

    $scripts .= "</script>";

    return $scripts;

}

add_filter("wpgooglemaps_filter_save_settings", "wpgmza_gold_clister_settings_save", 10, 1);
function wpgmza_gold_clister_settings_save($wpgmza_data){

    $wpgmza_gold_options = array();

    if (isset($_POST['wpgmza_cluster_advanced_enabled'])) { $wpgmza_gold_options['wpgmza_cluster_advanced_enabled'] = esc_attr($_POST['wpgmza_cluster_advanced_enabled']); }
    
    if (isset($_POST['wpgmza_cluster_grid_size'])) { $wpgmza_gold_options['wpgmza_cluster_grid_size'] = intval($_POST['wpgmza_cluster_grid_size']); } else { $wpgmza_gold_options['wpgmza_cluster_grid_size'] = 20;}
    if (isset($_POST['wpgmza_cluster_max_zoom'])) { $wpgmza_gold_options['wpgmza_cluster_max_zoom'] = intval($_POST['wpgmza_cluster_max_zoom']); } else { $wpgmza_gold_options['wpgmza_cluster_max_zoom'] = 15; }
    if (isset($_POST['wpgmza_cluster_min_cluster_size'])) { $wpgmza_gold_options['wpgmza_cluster_min_cluster_size'] = intval($_POST['wpgmza_cluster_min_cluster_size']); } else { $wpgmza_gold_options['wpgmza_cluster_min_cluster_size'] = 2; }
    if (isset($_POST['wpgmza_cluster_font_color'])) { $wpgmza_gold_options['wpgmza_cluster_font_color'] = esc_attr($_POST['wpgmza_cluster_font_color']); } else {  $wpgmza_gold_options['wpgmza_cluster_font_color'] = "#000000"; }
    if (isset($_POST['wpgmza_cluster_font_size'])) { $wpgmza_gold_options['wpgmza_cluster_font_size'] = intval($_POST['wpgmza_cluster_font_size']); } else { $wpgmza_gold_options['wpgmza_cluster_font_size'] = 12; }
    if (isset($_POST['wpgmza_cluster_zoom_click'])) { $wpgmza_gold_options['wpgmza_cluster_zoom_click'] = esc_attr($_POST['wpgmza_cluster_zoom_click']); }

    if (isset($_POST['wpgmza_cluster_level1'])) { $wpgmza_gold_options['wpgmza_gold_cluster_level1'] = urlencode(str_replace("http:", "", str_replace("https:", "", $_POST['wpgmza_cluster_level1']))); } else {  $wpgmza_gold_options['wpgmza_gold_cluster_level1'] = urlencode(plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m1.png"); }
    if (isset($_POST['wpgmza_cluster_level2'])) { $wpgmza_gold_options['wpgmza_gold_cluster_level2'] = urlencode(str_replace("http:", "", str_replace("https:", "",$_POST['wpgmza_cluster_level2']))); } else {  $wpgmza_gold_options['wpgmza_gold_cluster_level2'] = urlencode(plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m2.png"); }
    if (isset($_POST['wpgmza_cluster_level3'])) { $wpgmza_gold_options['wpgmza_gold_cluster_level3'] = urlencode(str_replace("http:", "", str_replace("https:", "",$_POST['wpgmza_cluster_level3']))); } else {  $wpgmza_gold_options['wpgmza_gold_cluster_level3'] = urlencode(plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m3.png"); }
    if (isset($_POST['wpgmza_cluster_level4'])) { $wpgmza_gold_options['wpgmza_gold_cluster_level4'] = urlencode(str_replace("http:", "", str_replace("https:", "",$_POST['wpgmza_cluster_level4']))); } else {  $wpgmza_gold_options['wpgmza_gold_cluster_level4'] = urlencode(plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m4.png"); }
    if (isset($_POST['wpgmza_cluster_level5'])) { $wpgmza_gold_options['wpgmza_gold_cluster_level5'] = urlencode(str_replace("http:", "", str_replace("https:", "",$_POST['wpgmza_cluster_level5']))); } else {  $wpgmza_gold_options['wpgmza_gold_cluster_level5'] = urlencode(plugin_dir_url(WPGMZA_GOLD_FILE) . "images/m5.png"); }

    if (isset($_POST['wpgmza_cluster_level1_width'])) { $wpgmza_gold_options['wpgmza_cluster_level1_width'] = intval($_POST['wpgmza_cluster_level1_width']); } else {  $wpgmza_gold_options['wpgmza_cluster_level1_width'] = 53; }
    if (isset($_POST['wpgmza_cluster_level2_width'])) { $wpgmza_gold_options['wpgmza_cluster_level2_width'] = intval($_POST['wpgmza_cluster_level2_width']); } else {  $wpgmza_gold_options['wpgmza_cluster_level2_width'] = 56; }
    if (isset($_POST['wpgmza_cluster_level3_width'])) { $wpgmza_gold_options['wpgmza_cluster_level3_width'] = intval($_POST['wpgmza_cluster_level3_width']); } else {  $wpgmza_gold_options['wpgmza_cluster_level3_width'] = 66; }
    if (isset($_POST['wpgmza_cluster_level4_width'])) { $wpgmza_gold_options['wpgmza_cluster_level4_width'] = intval($_POST['wpgmza_cluster_level4_width']); } else {  $wpgmza_gold_options['wpgmza_cluster_level4_width'] = 78; }
    if (isset($_POST['wpgmza_cluster_level5_width'])) { $wpgmza_gold_options['wpgmza_cluster_level5_width'] = intval($_POST['wpgmza_cluster_level5_width']); } else {  $wpgmza_gold_options['wpgmza_cluster_level5_width'] = 90; }

    if (isset($_POST['wpgmza_cluster_level1_height'])) { $wpgmza_gold_options['wpgmza_cluster_level1_height'] = intval($_POST['wpgmza_cluster_level1_height']); } else {  $wpgmza_gold_options['wpgmza_cluster_level1_height'] = 53; }
    if (isset($_POST['wpgmza_cluster_level2_height'])) { $wpgmza_gold_options['wpgmza_cluster_level2_height'] = intval($_POST['wpgmza_cluster_level2_height']); } else {  $wpgmza_gold_options['wpgmza_cluster_level2_height'] = 56; }
    if (isset($_POST['wpgmza_cluster_level3_height'])) { $wpgmza_gold_options['wpgmza_cluster_level3_height'] = intval($_POST['wpgmza_cluster_level3_height']); } else {  $wpgmza_gold_options['wpgmza_cluster_level3_height'] = 66; }
    if (isset($_POST['wpgmza_cluster_level4_height'])) { $wpgmza_gold_options['wpgmza_cluster_level4_height'] = intval($_POST['wpgmza_cluster_level4_height']); } else {  $wpgmza_gold_options['wpgmza_cluster_level4_height'] = 78; }
    if (isset($_POST['wpgmza_cluster_level5_height'])) { $wpgmza_gold_options['wpgmza_cluster_level5_height'] = intval($_POST['wpgmza_cluster_level5_height']); } else {  $wpgmza_gold_options['wpgmza_cluster_level5_height'] = 90; }
   
    update_option('WPGMZA_GOLD_CLUSTERING_SETTINGS', $wpgmza_gold_options);

   return $wpgmza_data; //We don't alter this, but instead create our own option for clustering 
}

add_action("wpgooglemaps_hook_user_js_after_core", "wpgmza_gold_cluster_custom_js");
function wpgmza_gold_cluster_custom_js(){
    $wpgmza_gold_clustering_data = get_option('WPGMZA_GOLD_CLUSTERING_SETTINGS', "false");
    
    if($wpgmza_gold_clustering_data !== "false"){
        if(isset($wpgmza_gold_clustering_data['wpgmza_cluster_advanced_enabled']) && $wpgmza_gold_clustering_data['wpgmza_cluster_advanced_enabled']  == 'yes'){
            wp_localize_script( 'wpgmaps_core', 'wpgmaps_custom_cluster_options', $wpgmza_gold_clustering_data);
        }
    }
}

// NB: Removed as of 5.0.0
/*function wpgmza_gold_enqueue_clustering_scripts()
{
	global $wpgmza;
	global $wpgmza_gold_version;
	
	if(!$wpgmza)
		return;
	
	$base = plugin_dir_url(__FILE__);
	
	$dependencies = array('wpgmza');
	
	if($wpgmza->isInDeveloperMode())
		$dependencies = array('wpgmza-event-dispatcher');
	
	wp_enqueue_script('wpgmza-gold-marker-clusterer', 			$base . 'js/v8/marker-clusterer.js', $dependencies, $wpgmza_gold_version);
	wp_enqueue_script('wpgmza-gold-marker-cluster', 			$base . 'js/v8/marker-cluster.js', $dependencies, $wpgmza_gold_version);
	wp_enqueue_script('wpgmza-gold-marker-cluster-icon',		$base . 'js/v8/marker-cluster-icon.js', $dependencies, $wpgmza_gold_version);
	
	wp_enqueue_script('wpgmza-gold-google-marker-cluster-icon',	$base . 'js/v8/google-maps/google-marker-cluster-icon.js', $dependencies, $wpgmza_gold_version);
	wp_enqueue_script('wpgmza-gold-ol-marker-cluster-icon',		$base . 'js/v8/open-layers/ol-marker-cluster-icon.js', $dependencies, $wpgmza_gold_version);
}

if(!is_admin())
{
	add_action('wp_enqueue_scripts', 'wpgmza_gold_enqueue_clustering_scripts');
}*/

add_filter("wpgmza_global_settings_save_redux", "wpgmza_gold_81_settings_filter", 10, 1);
function wpgmza_gold_81_settings_filter($data){
    $toggleControls = array(
        'wpgmza_cluster_zoom_click',
        'wpgmza_near_vicinity_control_enabled',
        'enable_live_tracking',
        'wpgmza_cluster_advanced_enabled',
        'marker_separator_use_legacy_module',
        'marker_separator_animate',
        'marker_separator_stagger_animation',
        'wpgmza_near_vicinity_shape',
        'wpgmza_near_vicinity_hide_line'
    );

    foreach ($toggleControls as $propKey) {
        if(empty($_POST[$propKey])){
            $data[$propKey] = false; 
        }
    }

    return $data;

}

add_filter("wpgmza_plugin_get_localized_data", "wpgmza_gold_81_localize_filter", 10, 1);
function wpgmza_gold_81_localize_filter($data){
    $data['goldPluginDirURL'] = plugin_dir_url(__FILE__);
    return $data;
}


// Atlas Novus (V9.0.0) editor compatibility code

/* TODO: Move to a class, and deprecate the old stuff. Temporary for Atast Novus (9.0.0 support) */
add_filter("wpgmza-map-editor-navigation-map-settings-behaviour", "wpgmza_gold_filter_atlas_novus_map_editor_navigation", 11, 1);
function wpgmza_gold_filter_atlas_novus_map_editor_navigation($navigation){
    $navigation .= "<div class='item caret-right' data-group='map-settings-behaviour-marker-clustering'>";
    $navigation .=   "<span class='dashicons dashicons-image-filter'></span>";
    $navigation .=    __("Marker Clustering", "wp-google-maps");
    $navigation .= "</div>";
    return $navigation;
}

/* TODO: Move to a class, and deprecate the old stuff. Temporary for Atast Novus (9.0.0 support) */
add_filter("wpgmza-map-editor-settings-blocks", "wpgmza_gold_filter_atlas_novus_map_editor_blocks",11,1);
function wpgmza_gold_filter_atlas_novus_map_editor_blocks($content) {
    if(!empty($_GET['map_id'])){
        $res = wpgmza_get_map_data($_GET['map_id']);
        $wpgmza_adv_mass_marker_support = array();

        if ($res->styling_enabled) { $wpgmza_adv_styling[$res->styling_enabled] = "SELECTED"; } else { $wpgmza_adv_styling[2] = "SELECTED"; }
        if ($res->mass_marker_support) { $wpgmza_adv_mass_marker_support[intval($res->mass_marker_support)] = "SELECTED"; } else { $wpgmza_adv_mass_marker_support[2] = "SELECTED"; }
        
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_adv_mass_marker_support[$i])) { $wpgmza_adv_mass_marker_support[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_adv_styling[$i])) { $wpgmza_adv_styling[$i] = ""; }
        }

        $settingsLink = admin_url( 'admin.php?page=wp-google-maps-menu-settings#tabs-gold-cluster');

        $content .= "<div class='grouping' data-group='map-settings-behaviour-marker-clustering'>";
        $content .=     "<div class='heading block has-back'>";
        $content .=         "<div class='item caret-left' data-group='map-settings-behaviour'></div>";
        $content .=         __("Marker Clusters", "wp-google-maps");
        $content .=     "</div>";

        $content .=     "<div class='settings'>";
        $content .=         "<fieldset class='wpgmza-pro-feature wpgmza-row'>";

        $content .=             '<div class="wpgmza-col">';
        $content .=                 "<legend>" . __("Enable Clustering", "wp-google-maps") . "</legend>";
        $content .=             '</div>';

        $content .=             '<div class="wpgmza-col">';
        $content .=                 "<select id='wpgmza_adv_enable_mass_marker_support' name='wpgmza_adv_enable_mass_marker_support'>";
        $content .=                     "<option value='1' {$wpgmza_adv_mass_marker_support[1]}>" . __("Yes","wp-google-maps") . "</option>";
        $content .=                     "<option value='2' {$wpgmza_adv_mass_marker_support[2]}>" . __("No","wp-google-maps") . "</option>";
        $content .=                 "</select>";

        $content .=             '</div>';

        $content .=         "</fieldset>";

        $content .=         "<fieldset class='wpgmza-pro-feature wpgmza-row'>";
        $content .=             '<div class="wpgmza-col">';
        $content .=             '</div>';
        $content .=             '<div class="wpgmza-col">';
        $content .=                 "<a target='_BLANK' href='{$settingsLink}' class='wpgmza-button'>" . __("Advanced Settings","wp-google-maps") . "</a>"; 
        $content .=             '</div>';
        $content .=         "</fieldset>";

        $content .=     "</div>";
        $content .= "</div>";
    }
    return $content;  
}
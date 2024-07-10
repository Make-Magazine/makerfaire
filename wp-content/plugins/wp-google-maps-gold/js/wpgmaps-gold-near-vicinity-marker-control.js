var wpgmza_nvc_marker_status = {};
var wpgmza_nvc_currently_expanded = new Array();
var wpgmza_nvc_current_parents = new Array();

var wpgmza_nvc_web_range = 0.0005;
var wpgmza_nvc_web_degree_increment = 25;
var wpgmza_nvc_web_radius_increment = 0.0005;
var wpgmza_nvc_web_degree_step_down = 5;
var wpgmza_nvc_web_min_degree = 15;

var wpgmza_nvc_web_line_col  = "#000000";
var wpgmza_nvc_web_line_opacity = "1.0";
var wpgmza_nvc_web_line_thickness = "1";

var wpgmza_nvc_web_force_spiral = false;

var wpgmza_nvc_suppress_logs = true;

jQuery(document).ready(function() {
	setTimeout(function(){
		wpgmza_nvc_initialize_webbing();
	}, 1000);
});

	
function wpgmza_nvc_initialize_webbing(){

	if(typeof wpgmza_nvc_affected_radius !== "undefined"){
		wpgmza_nvc_web_range = parseFloat(wpgmza_nvc_affected_radius);
		wpgmza_nvc_web_radius_increment = parseFloat(wpgmza_nvc_affected_radius);
	}

	if(typeof wpgmza_near_vicinity_line_col !== "undefined"){
		wpgmza_nvc_web_line_col = wpgmza_near_vicinity_line_col;
	}

	if(typeof wpgmza_near_vicinity_line_opacity !== "undefined"){
		wpgmza_nvc_web_line_opacity = parseFloat(wpgmza_near_vicinity_line_opacity);
		if(wpgmza_nvc_web_line_opacity > 1.0){
			wpgmza_nvc_web_line_opacity = 1.0;
		} 

		if(wpgmza_nvc_web_line_opacity < 0.1){
			wpgmza_nvc_web_line_opacity = 0.1;
		}
	}

	if(typeof wpgmza_near_vicinity_line_thickness !== "undefined"){
		wpgmza_nvc_web_line_thickness = parseInt(wpgmza_near_vicinity_line_thickness);
		if(wpgmza_nvc_web_line_thickness > 50){
			wpgmza_nvc_web_line_thickness = 50;
		} 

		if(wpgmza_nvc_web_line_thickness < 1){
			wpgmza_nvc_web_line_thickness = 1;
		}
	}

	if(typeof marker_array !== "undefined"){
		for(var map_id in marker_array){
			for(var marker_id in marker_array[map_id]){
				wpgmza_nvc_register_click_checker(map_id, marker_id, marker_array[map_id][marker_id]);
			}
		}
	} else {
		wpgmza_nvc_log("No Marker Data Present");
	}

	if(typeof wpgmza_near_vicinity_shape !== "undefined"){
		wpgmza_nvc_web_force_spiral = true;
	}
	
}

function wpgmza_nvc_register_click_checker(map_id, marker_id, current_marker){
	if(typeof wpgmza_nvc_marker_status[map_id] === "undefined"){
		wpgmza_nvc_marker_status[map_id] = {};
	}

	wpgmza_nvc_marker_status[map_id][marker_id] = {};
	wpgmza_nvc_marker_status[map_id][marker_id]['original_pos'] = current_marker.position;
	wpgmza_nvc_marker_status[map_id][marker_id]['currently_parent'] = false;
	wpgmza_nvc_marker_status[map_id][marker_id]['in_open_web'] = false;

	function callback(event)
	{
		wpgmza_nvc_log("Clicked Marker Location:" + current_marker.position.lat + " - " + current_marker.position.lng);
		wpgmza_nvc_toggle_web(map_id, marker_id, current_marker);
	}
	
	if(window.google && current_marker instanceof google.maps.Marker)
		google.maps.event.addListener(current_marker, "click", callback);
	else
		current_marker.on("click", callback);
}

function wpgmza_nvc_toggle_web(map_id, marker_id, current_marker){
	if(typeof wpgmza_nvc_marker_status !== "undefined" && typeof wpgmza_nvc_marker_status[map_id] !== "undefined" && typeof wpgmza_nvc_marker_status[map_id][marker_id] !== "undefined"){
		if(wpgmza_nvc_marker_status[map_id][marker_id]['currently_parent'] !== true){
			if(wpgmza_nvc_marker_status[map_id][marker_id]['in_open_web'] !== true){
				//Marker can be expanded into web if applicable
				wpgmza_nvc_open_web(map_id, marker_id, current_marker); //Open
				console.log('open');
			}
		} else {
			//is Parent -> Let's close 'ALL' webs
			while(wpgmza_nvc_currently_expanded.length > 0){
				var marker_details = wpgmza_nvc_currently_expanded.shift();
				wpgmza_nvc_close_web(marker_details['map_id'], marker_details['marker_id'], marker_details['current_marker']); //Close
			}
			wpgmza_nvc_marker_status[map_id][marker_id]['currently_parent'] = false;

			while(wpgmza_nvc_current_parents.length > 0){
				var parent_id = wpgmza_nvc_current_parents.shift();
				wpgmza_nvc_marker_status[map_id][parent_id]['currently_parent'] = false;
			}
		}
	}
}

function wpgmza_nvc_open_web(map_id, marker_id, current_marker){
	
	var min_lat, max_lat, min_lng, max_lng;
	
	if(window.WPGMZA && current_marker instanceof WPGMZA.Marker)
	{
		min_lat = current_marker.position.lat - wpgmza_nvc_web_range;
		max_lat = current_marker.position.lat + wpgmza_nvc_web_range;
		min_lng = current_marker.position.lng - wpgmza_nvc_web_range;
		max_lng = current_marker.position.lng + wpgmza_nvc_web_range;
	}
	else
	{
		min_lat = current_marker.position.lat() - wpgmza_nvc_web_range;
		max_lat = current_marker.position.lat() + wpgmza_nvc_web_range;
		min_lng = current_marker.position.lng() - wpgmza_nvc_web_range;
		max_lng = current_marker.position.lng() + wpgmza_nvc_web_range;
	}
    
    wpgmza_nvc_log("Lat Range = " + min_lat + " - " + max_lat);
    wpgmza_nvc_log("Lng Range = " + min_lng + " - " + max_lng);
    
    if(typeof marker_array !== "undefined" && typeof marker_array[map_id] !== "undefined"){
	    var starting_degree = 0;
	    var web_bounds = new google.maps.LatLngBounds();
	    var atleast_one_in_web = false;
	    for(var i in marker_array[map_id]){
	       	if(parseInt(i) !== parseInt(marker_id)){
		        
				var current_lat, current_lng;

	       		if(window.google && marker_array[map_id][i] instanceof google.maps.Marker)
				{
		        	current_lat = marker_array[map_id][i].position.lat();
		        	current_lng = marker_array[map_id][i].position.lng();
				}
				else
				{
					current_lat = marker_array[map_id][i].position.lat;
		        	current_lng = marker_array[map_id][i].position.lng;
				}
				
		        if(current_lat > min_lat && current_lat < max_lat){
		          	if(current_lng > min_lng && current_lng < max_lng){
		             	var originalLatLng = wpgmza_nvc_marker_status[map_id][i]['original_pos'];
						
						var googleLatLng = wpgmza_nvc_get_point_in_circle(wpgmza_nvc_marker_status[map_id][marker_id]['original_pos'], starting_degree);
		                var newLatlng = new WPGMZA.LatLng({
							lat: googleLatLng.lat(),
							lng: googleLatLng.lng()
						});
		                 
		                starting_degree += wpgmza_nvc_web_degree_increment;
						
						if(window.WPGMZA && marker_array[map_id][i] instanceof WPGMZA.Marker)
							marker_array[map_id][i].setPosition(newLatlng);
						else
							marker_array[map_id][i].setPosition({
								lat: newLatlng.lat,
								lng: newLatlng.lng
							});
						
						var pathFromClick = [];
						
						if(window.WPGMZA && originalLatLng instanceof WPGMZA.LatLng)
							pathFromClick.push(new google.maps.LatLng({
								lat: originalLatLng.lat,
								lng: originalLatLng.lng
							}));
						else
							pathFromClick.push(new google.maps.LatLng({
								lat: originalLatLng.lat(),
								lng: originalLatLng.lng()
							}));
							
						if(window.WPGMZA && newLatlng instanceof WPGMZA.LatLng)
							pathFromClick.push(new google.maps.LatLng({
								lat: newLatlng.lat,
								lng: newLatlng.lng
							}));
						else
							pathFromClick.push(new google.maps.LatLng({
								lat: newLatlng.lat(),
								lng: newLatlng.lng()
							}));
		                
		                if(typeof MYMAP[map_id] !== "undefined"){
		                	if(typeof wpgmza_near_vicinity_hide_webs === "undefined"){
			                	var path = new google.maps.Polyline({
				                  path: pathFromClick,
				                  geodesic: true,
				                  strokeColor: wpgmza_nvc_web_line_col,
				                  strokeOpacity: wpgmza_nvc_web_line_opacity,
				                  strokeWeight: wpgmza_nvc_web_line_thickness
				                });
				                
								if(window.WPGMZA && MYMAP[map_id].map instanceof WPGMZA.Map)
									path.setMap(MYMAP[map_id].map.googleMap);
								else
									path.setMap(MYMAP[map_id].map);
								
				                wpgmza_nvc_marker_status[map_id][i]['path'] = path;
				            }
			            }

			            wpgmza_nvc_marker_status[map_id][i]['in_open_web'] = true;

			            var expanded_details = {
			            	map_id : map_id,
			            	marker_id : i,
			            	current_marker : marker_array[map_id][i]
			            };

			            wpgmza_nvc_currently_expanded.push(expanded_details);

			            web_bounds.extend(googleLatLng);
			            atleast_one_in_web = true;
		           	}
		       	}
		    }

		    if(atleast_one_in_web){
		    	if(typeof MYMAP[map_id] !== "undefined"){
		    		MYMAP[map_id].map.fitBounds(web_bounds);
		    	}
		    }
		}

		wpgmza_nvc_marker_status[map_id][marker_id]['currently_parent'] = true;
		wpgmza_nvc_current_parents.push(marker_id);

	} else {
		wpgmza_nvc_log("No Marker Data Present");
	}
}

function wpgmza_nvc_close_web(map_id, marker_id, current_marker)
{
	if(typeof wpgmza_nvc_marker_status !== "undefined" && typeof wpgmza_nvc_marker_status[map_id] !== "undefined" && typeof wpgmza_nvc_marker_status[map_id][marker_id] !== "undefined")
	{
        if(typeof wpgmza_nvc_marker_status[map_id][marker_id]['original_pos'] !== "undefined")
		{
            current_marker.setPosition(wpgmza_nvc_marker_status[map_id][marker_id]['original_pos']);
			
            if(typeof wpgmza_nvc_marker_status[map_id][marker_id]['path'] !== "undefined"){
                wpgmza_nvc_marker_status[map_id][marker_id]['path'].setMap(null);
            }
        }
    } else {
        //Force the use of the localize variable
        wpgmza_nvc_log("Marker Data usage");
    }
    wpgmza_nvc_marker_status[map_id][marker_id]['in_open_web'] = false;
}

function wpgmza_nvc_get_point_in_circle(center, dg){
  	var r = wpgmza_nvc_web_range; 
  	if(wpgmza_nvc_web_force_spiral){
		//Make a sprial neh
		r = wpgmza_nvc_web_range / 25;
		r += dg / 300000;

  		if(dg > 360){
		  	while(dg > 360){
		      	dg -= 360;
		      	if(wpgmza_nvc_web_degree_increment - wpgmza_nvc_web_degree_step_down >= wpgmza_nvc_web_min_degree){
			    	wpgmza_nvc_web_degree_increment -= wpgmza_nvc_web_degree_step_down;
			    }
		    }
	  	}
  	} else {
	  	if(dg > 360){
		  	while(dg > 360){
		    	r += wpgmza_nvc_web_radius_increment;
		      	dg -= 360;
			    if(wpgmza_nvc_web_degree_increment - wpgmza_nvc_web_degree_step_down >= wpgmza_nvc_web_min_degree){
			    	wpgmza_nvc_web_degree_increment -= wpgmza_nvc_web_degree_step_down;
			    }
		    }
	  	}
	}
 
	var lat, lng;
	
	if(window.WPGMZA && center instanceof WPGMZA.LatLng)
	{
		lat = Math.sin(dg * Math.PI / 180) * r + center.lat;
		lng = Math.cos(dg * Math.PI / 180) * r + center.lng;
	}
	else
	{
		lat = Math.sin(dg * Math.PI / 180) * r + center.lat();
		lng = Math.cos(dg * Math.PI / 180) * r + center.lng();
	}
  	
  	return new google.maps.LatLng(lat,lng);
}


function wpgmza_nvc_log(msg){
	if(window.console){
		if(msg !== "" && wpgmza_nvc_suppress_logs !== true){
			console.log("WPGMZA NVC: " + msg);
		}
	}
}
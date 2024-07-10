jQuery(document).ready(function() {
            
            jQuery("body").on("click", "#wpgmza_rtlt_refresh", function() {
               var wpgmza_map_id = jQuery(this).attr("mid");
                wpgmza_gold_update_markers(wpgmza_map_id);
                
                

            }); 


});
jQuery(document).ready(function() {
    wpgmza_gold_update_markers();
    
    var sumttdf = setInterval(function() {
        wpgmza_gold_update_markers()
    },60000);
    

});

var wpgmza_route_data = new Array(); //Holds device route data -- var[device_id][id (0-200)] - [lat] OR [lng]
var wpgmza_route_polylines = new Array();
//var wpgmza_route_midway_markers; //Holds marker id's to hide
function wpgmza_gold_update_markers() {

    if(typeof wpgmaps_localize === 'undefined'){
        return;
    }

    for (var entry in wpgmaps_localize) {
        if ("undefined" !== typeof wpgmaps_localize[entry]['other_settings']['rtlt_enabled'] && wpgmaps_localize[entry]['other_settings']['rtlt_enabled'] === 1) {

            var data = {
                action: 'wpgmza_refresh_markers',
                security: wpgmaps_pro_nonce,
                map_id: entry
            };
            jQuery.post(ajaxurl, data, function(response) {
                document.marker_data_array = JSON.parse(response);
                MYMAP[entry].placeMarkers(wpgmaps_markerurl+entry+'markers.xml?u='+UniqueCode,entry,'all',null,null,null,true);       
            });

            //Second ajax request
            var data = {
                action: 'wpgmza_refresh_routes',
                security: wpgmaps_pro_nonce,
                map_id: entry
            };
            jQuery.post(ajaxurl, data, function(response) {
                wpgmza_route_data = JSON.parse(response);
                //console.log(wpgmza_route_data);   

                wpgmza_gold_update_routes(entry); 

            });
        }

    }
}

function wpgmza_gold_clear_midway_markers(mid){
    for(g = 0; g < wpgmza_route_midway_markers.length; g++){
            marker_array[mid][wpgmza_route_midway_markers[g]].setMap(null);
    }
}

function wpgmza_gold_update_routes(mid){
    //wpgmza_route_midway_markers = new Array(); //Start fresh
    for(var g in wpgmza_route_data){
        var current_route = new Array(); //Create an array with data
        for(i = 0; i < wpgmza_route_data[g].length; i++){
            current_route[i] = new Array();
            current_route[i]['lat'] = parseFloat(wpgmza_route_data[g][i]['lat']);
            current_route[i]['lng'] = parseFloat(wpgmza_route_data[g][i]['lng']);
            /*if(i !== 0 && i !== (wpgmza_route_data[g].length -1)){
                //Hide all markers in between range
                wpgmza_route_midway_markers.push(wpgmza_route_data[g][i]['marker_id'])
            }*/
        }
        
        if(wpgmza_route_polylines[g] !== null && typeof wpgmza_route_polylines[g] !== 'undefined'){
            wpgmza_route_polylines[g].setMap(null);
        }

        route_normal_color = "#" + wpgmaps_localize[mid]['other_settings']['rtlt_route_col_normal'];
        route_hover_color = "#" + wpgmaps_localize[mid]['other_settings']['rtlt_route_col_hover'];
        route_opacity = wpgmaps_localize[mid]['other_settings']['rtlt_route_opacity'];
        route_thickness = wpgmaps_localize[mid]['other_settings']['rtlt_route_thickness'];

        wpgmza_route_polylines[g] =  new google.maps.Polyline({
            path: current_route,
            geodesic: true,
            strokeColor: route_normal_color,
            strokeOpacity: parseFloat(route_opacity),
            strokeWeight: parseInt(route_thickness)
        });

        wpgmza_route_polylines[g].setMap(MYMAP[mid].map);

        google.maps.event.addListener(wpgmza_route_polylines[g], 'mouseover', function(event) {
            this.setOptions({strokeColor: route_hover_color});
        });

        google.maps.event.addListener(wpgmza_route_polylines[g], 'mouseout', function(event) {
            this.setOptions({strokeColor: route_normal_color});
        });

       // wpgmza_gold_clear_midway_markers(mid);
    }
   


}
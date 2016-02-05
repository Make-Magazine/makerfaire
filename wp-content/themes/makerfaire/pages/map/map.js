var FairesGlobalMap = (function() {
  'use strict';
  var faireMarkers;
  var gmarkers1 = [];
  function initMap () {
    var map;
    var customMapType = new google.maps.StyledMapType([
      {
        stylers: [{
          hue: '#FFFFFF'
        }, {
          visibility: 'simplified'
        }, {
          gamma: 2
        }, {
          weight: 0.5
        }]
      }, {
        elementType: 'labels',
        stylers: [{
          visibility: 'off'
        }]
      }, {
        featureType: 'landscape',
        stylers: [{
          color: '#FFFFFF'
        }]
      }, {
        featureType: 'water',
        stylers: [{
          color: '#EDF2F5'
        }]
      }
    ], {
      name: 'Custom Style'
    });
    var customMapTypeId = 'custom_style';
    map = new google.maps.Map(document.getElementById('faire-global-map'), {
      center: {
        lat: -34.397,
        lng: 150.644
      },
      zoom: 2
    });
    map.mapTypes.set(customMapTypeId, customMapType);
    map.setMapTypeId(customMapTypeId);
    function setMarkers(data, map) {
      // var image = {
      //   url: 'images/beachflag.png',
      //   size: new google.maps.Size(20, 32),
      //   origin: new google.maps.Point(0, 0),
      //   anchor: new google.maps.Point(0, 32)
      // };
      // var shape = {
      //   coords: [1, 1, 1, 20, 18, 20, 18, 1],
      //   type: 'poly'
      // };
      var marker;
      for (var i = 0; i < data.length; i++) {
        marker = data[i];
        marker = new google.maps.Marker({
          position: {
            lat: marker.coordinates[0],
            lng: marker.coordinates[1]
          },
          map: map,
          // icon: image,
          // shape: shape,
          title: marker.description,
          category: marker.category
          // zIndex: marker[3]
        });
        gmarkers1.push(marker);
      }
    }
    jQuery(document).ready(function() {
      jQuery.get('/wp-admin/admin-ajax.php?action=get_faires_map_data', function(data) {
        faireMarkers = JSON.parse(data);
        setMarkers(faireMarkers, map);
      });
    });
  }
  function filterMarkers(category) {
    var marker;
    for (var i = 0; i < gmarkers1.length; i++) {
      marker = gmarkers1[i];
      // If is same category or category not picked
      if (marker.category == category || category.length === 0) {
        marker.setVisible(true);
      }
      // Categories don't match
      else {
        marker.setVisible(false);
      }
    }
  }
  return {
    initMap: initMap,
    filterMarkers: filterMarkers,
    faireMarkers: faireMarkers
  };
})();

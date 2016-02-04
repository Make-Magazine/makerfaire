var FairesGlobalMap = (function() {
  'use strict';
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
      var image = {
        url: 'images/beachflag.png',
        size: new google.maps.Size(20, 32),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(0, 32)
      };
      // var shape = {
      //   coords: [1, 1, 1, 20, 18, 20, 18, 1],
      //   type: 'poly'
      // };
      for (var i = 0; i < data.length; i++) {
        for (var n = 0; n < data[i].data.length; n++) {
          var marker = data[i].data[n];
          var marker = new google.maps.Marker({
            position: {
              lat: marker.coordinates[0],
              lng: marker.coordinates[1]
            },
            map: map,
            // icon: image,
            // shape: shape,
            title: marker.description,
            // zIndex: marker[3]
          });
        }
      }
    }
    jQuery(document).ready(function() {
      jQuery.get('/wp-admin/admin-ajax.php?action=get_faires_map_data', function(data) {
        var data = JSON.parse(data);
        setMarkers(data, map);
      });
    });
  };
  return {
    initMap: initMap
  };
})();

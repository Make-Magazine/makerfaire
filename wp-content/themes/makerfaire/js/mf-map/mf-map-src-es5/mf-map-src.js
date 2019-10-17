"use strict";

jQuery(document).ready(function () {
  Vue.use(VueTables.ClientTable);
  Vue.use(VueTables.Event);
  var vm = new Vue({
    el: "#directory",
    data: {
      columns: ['mmap_date', 'mmap_eventname', 'physLoc', 'mmap_country', 'mmap_city', 'mmap_state', 'mmap_start_dt', 'mmap_end_dt'],
      tableData: [],
      options: {
        headings: {
          mmap_date: 'Date',
          mmap_eventname: 'Name',
          physLoc: 'Location',
          mmap_country: 'Country'
        },
        templates: {
          physLoc: function physLoc(h, row, index) {
            var text = row.mmap_city;

            if (row.mmap_state) {
              text += ', ' + row.mmap_state;
            }

            return text;
          },
          mmap_date: function mmap_date(h, row, index) {
            var text = formatDate(row.mmap_start_dt);

            if (row.mmap_end_dt != row.mmap_start_dt) {
              text += ' - ' + formatDate(row.mmap_end_dt);
            }

            return text;
          }
        },
        columnsDisplay: {
          mmap_country: 'desktop'
        },
        columnsClasses: {
          mmap_eventname: 'col-name',
          physLoc: 'col-location',
          mmap_country: 'col-country',
          mmap_date: 'col-date',
          mmap_city: 'col-hidden',
          mmap_state: 'col-hidden',
          mmap_start_dt: 'col-hidden',
          mmap_end_dt: 'col-hidden'
        },
        pagination: {
          chunk: 5
        } // undocumented :(

      },
      filterVal: '',
      map: null,
      mapDefaultZoom: 2,
      mapDefaultPos: {
        lat: 29.1070772,
        lng: -24.2299966
      },
      markers: ''
    },
    created: function created() {
      var _self = this;

      axios.get('/wp-json/makemap/v1/mapdata/242').then(function (response) {
        _self.$refs.loadingIndicator.classList.add("hidden");

        _self.tableData = response.data.Locations;

        _self.$refs.directoryGrid.setOrder('mmap_eventname', true);

        _self.detectBrowser();

        _self.getLocation();

        _self.initMap();
      })["catch"](function (error) {
        console.log(error);

        _self.$refs.loadingIndicator.classList.add("hidden");

        _self.$refs.errorIndicator.classList.remove("hidden");
      });
    },
    methods: {
      detectBrowser: function detectBrowser() {
        var useragent = navigator.userAgent,
            mapdiv = this.$refs.map;

        if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('Android') != -1) {
          mapdiv.style.width = '100%';
          mapdiv.style.height = '300px';
        } else {
          mapdiv.style.width = '100%';
          mapdiv.style.height = '400px';
        }
      },
      initMap: function initMap() {
        this.$refs.mapTableWrapper.classList.remove("map-table-hidden");
        var styledMapType = new google.maps.StyledMapType([{
          elementType: 'geometry',
          stylers: [{
            color: '#ebe3cd'
          }]
        }, {
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#523735'
          }]
        }, {
          elementType: 'labels.text.stroke',
          stylers: [{
            color: '#f5f1e6'
          }]
        }, {
          featureType: 'administrative',
          elementType: 'geometry.stroke',
          stylers: [{
            color: '#c9b2a6'
          }]
        }, {
          featureType: 'administrative.land_parcel',
          elementType: 'geometry.stroke',
          stylers: [{
            color: '#dcd2be'
          }]
        }, {
          featureType: 'administrative.land_parcel',
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#ae9e90'
          }]
        }, {
          featureType: 'landscape.natural',
          elementType: 'geometry',
          stylers: [{
            color: '#dfd2ae'
          }]
        }, {
          featureType: 'poi',
          elementType: 'geometry',
          stylers: [{
            color: '#dfd2ae'
          }]
        }, {
          featureType: 'poi',
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#93817c'
          }]
        }, {
          featureType: 'poi.park',
          elementType: 'geometry.fill',
          stylers: [{
            color: '#a5b076'
          }]
        }, {
          featureType: 'poi.park',
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#447530'
          }]
        }, {
          featureType: 'road',
          elementType: 'geometry',
          stylers: [{
            color: '#f5f1e6'
          }]
        }, {
          featureType: 'road.arterial',
          elementType: 'geometry',
          stylers: [{
            color: '#fdfcf8'
          }]
        }, {
          featureType: 'road.highway',
          elementType: 'geometry',
          stylers: [{
            color: '#f8c967'
          }]
        }, {
          featureType: 'road.highway',
          elementType: 'geometry.stroke',
          stylers: [{
            color: '#e9bc62'
          }]
        }, {
          featureType: 'road.highway.controlled_access',
          elementType: 'geometry',
          stylers: [{
            color: '#e98d58'
          }]
        }, {
          featureType: 'road.highway.controlled_access',
          elementType: 'geometry.stroke',
          stylers: [{
            color: '#db8555'
          }]
        }, {
          featureType: 'road.local',
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#806b63'
          }]
        }, {
          featureType: 'transit.line',
          elementType: 'geometry',
          stylers: [{
            color: '#dfd2ae'
          }]
        }, {
          featureType: 'transit.line',
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#8f7d77'
          }]
        }, {
          featureType: 'transit.line',
          elementType: 'labels.text.stroke',
          stylers: [{
            color: '#ebe3cd'
          }]
        }, {
          featureType: 'transit.station',
          elementType: 'geometry',
          stylers: [{
            color: '#dfd2ae'
          }]
        }, {
          featureType: 'water',
          elementType: 'geometry.fill',
          stylers: [{
            color: '#b9d3c2'
          }]
        }, {
          featureType: 'water',
          elementType: 'labels.text.fill',
          stylers: [{
            color: '#92998d'
          }]
        }], {
          name: 'Styled Map'
        });
        var element = this.$refs.map;
        var options = {
          center: this.mapDefaultPos,
          zoom: this.mapDefaultZoom,
          mapTypeControlOptions: {
            mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
          }
        };
        this.map = new google.maps.Map(element, options);
        this.map.mapTypes.set('styled_map', styledMapType);
        this.map.setMapTypeId('styled_map');
        this.addMarkers();
      },
      getLocation: function getLocation() {
        var infoWindow = new google.maps.InfoWindow(),
            _self = this; // Try HTML5 geolocation.


        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };

            _self.map.setCenter(pos);

            _self.map.setZoom(8);
          }, function () {
            _self.handleLocationError(true, infoWindow, _self.map.getCenter());
          });
        } else {
          // Browser doesn't support Geolocation
          _self.handleLocationError(false, infoWindow, _self.map.getCenter());
        }
      },
      handleLocationError: function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        // NOTE (ts): handle this event in some other way? putting a popup on the map isn't very helpful
        console.error('User location check failed'); // infoWindow.setPosition(pos);
        // infoWindow.setContent(browserHasGeolocation ? 'Error: The Geolocation service failed.' : 'Error: Your browser doesn\'t support geolocation.');
        // infoWindow.open(this.map);
      },
      doFilter: function doFilter(data) {
        this.$refs.directoryGrid.setFilter(this.filterVal);
        this.addMarkers();
      },
      filterOverride: function filterOverride(data) {
        data.preventDefault();
      },
      onRowClick: function onRowClick(data) {
        var pos = {
          lat: parseFloat(data.row.mmap_lat),
          lng: parseFloat(data.row.mmap_lng)
        };
        this.map.panTo(pos);
        this.map.setZoom(16);
      },
      addMarkers: function addMarkers() {
        // an attempt to clear the markers first for filtering, but not so good
        // for (var i = 0; i < this.markers.length; i++) {
        //    this.markers[i].setMap(null);
        // }
        // this.markers = [];
        // Create an array of alphabetical characters used to label the markers.
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        this.markers = this.tableData.map(function (location, i) {
          //console.log(location);
          var latLng = {
            lat: parseFloat(location.mmap_lat),
            lng: parseFloat(location.mmap_lng)
          };
          var marker = new google.maps.Marker({
            position: latLng,
            label: '' //labels[i % labels.length]

          });
          marker.addListener('click', function () {
            var myWindow = new google.maps.InfoWindow({
              content: '<div style=""><h4>' + location.mmap_eventname + '</h4><p><a href="' + location.mmap_url + '" target="_blank">' + location.mmap_url + '</a></p></div>'
            });
            myWindow.open(this.map, marker);
          });
          return marker;
        }); //Add a marker clusterer to manage the markers.

        var markerCluster = new MarkerClusterer(this.map, this.markers, {
          imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        });
      }
    }
  });
}); // end doc ready

function formatDate(date) {
  var theDate = new Date(date);
  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  var day = theDate.getDate();
  var monthIndex = theDate.getMonth();
  var year = theDate.getFullYear();
  return monthNames[monthIndex] + ', ' + day + ' ' + year;
}
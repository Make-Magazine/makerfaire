"use strict";

jQuery(document).ready(function () {
  var currentDate = new Date();
  Vue.use(VueTables.ClientTable);
  Vue.use(VueTables.Event);
  var vm = new Vue({
    el: "#directory",
    data: {
      columns: ['faire_year', 'faire_name', 'venue_address_street', 'venue_address_country', 'venue_address_city', 'venue_address_state', 'event_start_dt', 'event_end_dt'],
      tableData: [],
      options: {
        headings: {
          faire_year: 'Date',
          faire_name: 'Name',
          venue_address_street: 'Location',
          venue_address_country: 'Country'
        },
        templates: {
          venue_address_street: function venue_address_street(h, row, index) {
            var text = row.venue_address_city;

            if (row.venue_address_state) {
              text += ', ' + row.venue_address_state;
            }

            return text;
          },
          faire_year: function faire_year(h, row, index) {
            var text = formatDate(row.event_start_dt); // "11\/30\/-0001 12:00:00 am" is a default day or something we need to filter out from these calculations

            if (row.event_end_dt != row.event_start_dt && row.event_end_dt != "11\/30\/-0001 12:00:00 am") {
              text += ' - ' + formatDate(row.event_end_dt);
            }

            return text;
          }
          /*event_start_dt: function(h, row, index) {
          	var text = Date.parse(row.event_start_dt);
          	return text;
          }	 */

        },
        columnsDisplay: {
          venue_address_country: 'desktop'
        },
        columnsClasses: {
          faire_name: 'col-name',
          venue_address_street: 'col-location',
          venue_address_country: 'col-country',
          faire_year: 'col-date',
          venue_address_city: 'col-hidden',
          venue_address_state: 'col-hidden',
          event_start_dt: 'col-test',
          event_end_dt: 'col-hidden'
        },
        pagination: {
          chunk: 5
        } // undocumented :(

      },
      filterVal: '',
      pastFaires: false,
      buttonMessage: "Show Past Faires",
      map: null,
      mapDefaultZoom: 2,
      mapMinZoom: 2,
      mapMaxZoom: 20,
      mapDefaultPos: {
        lat: 29.1070772,
        lng: -4.2299966
      },
      markers: ''
    },
    created: function created() {
      var _self = this;

      axios.get('/query/?type=map').then(function (response) {
        _self.$refs.loadingIndicator.classList.add("hidden");

        _self.outputData = response.data.Locations;

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
          mapdiv.style.height = '450px';
        }
      },
      initMap: function initMap() {
        this.$refs.mapTableWrapper.classList.remove("map-table-hidden"); //console.log(this.$refs.directoryGrid);
        // this sorts the order by the event start date, unfortunately it's alphabetical
        //				/this.$refs.directoryGrid.setOrder('event_start_dt', 'asc');
        // filter out the past faires

        this.tableData = this.outputData.filter(function (values) {
          var startDate = new Date(values.event_start_dt);

          if (startDate > currentDate) {
            return values;
          }
        });
        var styledMapType = new google.maps.StyledMapType([{
          "elementType": "geometry",
          "stylers": [{
            "color": "#f5f5f5"
          }]
        }, {
          "elementType": "labels.icon",
          "stylers": [{
            "visibility": "off"
          }]
        }, {
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#616161"
          }]
        }, {
          "elementType": "labels.text.stroke",
          "stylers": [{
            "color": "#f5f5f5"
          }]
        }, {
          "featureType": "administrative.land_parcel",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#bdbdbd"
          }]
        }, {
          "featureType": "poi",
          "elementType": "geometry",
          "stylers": [{
            "color": "#eeeeee"
          }]
        }, {
          "featureType": "poi",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#757575"
          }]
        }, {
          "featureType": "poi.park",
          "elementType": "geometry",
          "stylers": [{
            "color": "#e5e5e5"
          }]
        }, {
          "featureType": "poi.park",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#9e9e9e"
          }]
        }, {
          "featureType": "road",
          "elementType": "geometry",
          "stylers": [{
            "color": "#ffffff"
          }]
        }, {
          "featureType": "road.arterial",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#757575"
          }]
        }, {
          "featureType": "road.highway",
          "elementType": "geometry",
          "stylers": [{
            "color": "#dadada"
          }]
        }, {
          "featureType": "road.highway",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#616161"
          }]
        }, {
          "featureType": "road.local",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#9e9e9e"
          }]
        }, {
          "featureType": "transit.line",
          "elementType": "geometry",
          "stylers": [{
            "color": "#e5e5e5"
          }]
        }, {
          "featureType": "transit.station",
          "elementType": "geometry",
          "stylers": [{
            "color": "#eeeeee"
          }]
        }, {
          "featureType": "water",
          "elementType": "geometry",
          "stylers": [{
            "color": "#c9c9c9"
          }]
        }, {
          "featureType": "water",
          "elementType": "labels.text.fill",
          "stylers": [{
            "color": "#9e9e9e"
          }]
        }], {
          name: 'Styled Map'
        });
        var element = this.$refs.map;
        var options = {
          center: this.mapDefaultPos,
          zoom: this.mapDefaultZoom,
          minZoom: this.mapMinZoom,
          maxZoom: this.mapMaxZoom,
          restriction: {
            latLngBounds: {
              north: 85,
              south: -85,
              west: -180,
              east: 180
            },
            strictBounds: true
          },
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
      // search filter
      searchFilter: function searchFilter(data) {
        this.$refs.directoryGrid.setFilter(this.filterVal);
        this.addMarkers();
      },
      // past faires filter
      psFilter: function psFilter(data) {
        if (this.pastFaires == true) {
          this.buttonMessage = "Show Past Faires";
          this.initMap();
        } else {
          this.buttonMessage = "Show Upcoming Faires";
          this.tableData = this.outputData.filter(function (values) {
            return values;
          });
        }

        this.addMarkers();
      },
      filterOverride: function filterOverride(data) {
        data.preventDefault();
      },
      onRowClick: function onRowClick(data) {
        var pos = {
          lat: parseFloat(data.row.lat),
          lng: parseFloat(data.row.lng)
        };
        this.map.panTo(pos);
        this.map.setZoom(16);
      },
      // adding the markers to the map
      addMarkers: function addMarkers() {
        this.markers = this.tableData.map(function (location, i) {
          //this math random business keeps faires that were in the same location year after year from being on top of each other and not individually clickable
          var latLng = {
            lat: parseFloat(location.lat) + Math.random() / 1000,
            lng: parseFloat(location.lng) + Math.random() / 1000
          };
          var marker = new google.maps.Marker({
            position: latLng,
            label: ''
          });
          marker.addListener('click', function () {
            var dateRange = formatDate(location.event_start_dt);

            if (location.event_end_dt != location.event_start_dt && location.event_end_dt != "11\/30\/-0001 12:00:00 am") {
              dateRange += ' - ' + formatDate(location.event_end_dt);
            }

            var myWindow = new google.maps.InfoWindow({
              content: '<div style=""><h4>' + location.faire_name + '</h4><p>' + dateRange + '</p><p><a href="' + location.faire_url + '" target="_blank">' + location.faire_url + '</a></p></div>'
            });
            myWindow.open(this.map, marker);
          });
          return marker;
        }); //Add a marker clusterer to manage the markers.

        var markerCluster = new MarkerClusterer(this.map, this.markers, {
          imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        });
      }
    },
    computed: {
      // none of this works
      sortedItems: function sortedItems() {
        var _self = this;

        console.log("table data: ", _self.tableData);

        _self.tableData.sort(function (a, b) {
          console.log(a.event_start_dt);
          return new Date(a.event_start_dt) - new Date(b.event_start_dt);
        });

        return _self.tableData;
      }
    }
  });
  vm.sortedItems; // remove with above
}); // end doc ready

function formatDate(date) {
  var theDate = new Date(date);
  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  var day = theDate.getDate();
  var monthIndex = theDate.getMonth();
  var year = theDate.getFullYear();
  return monthNames[monthIndex] + ', ' + day + ' ' + year;
}
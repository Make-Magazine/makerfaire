
jQuery(document).ready(function() {
   var currentDate = new Date();
	var oneYearAgo = new Date(new Date().setFullYear(new Date().getFullYear() - 1));
	var firstLoaded = true; // we only want to sort by date on the first load, otherwise keep their selected sorting order
	var typeFilters = ["Featured", "Flagship", "Mini", "School"];
   Vue.use(VueTables.ClientTable);
   Vue.use(VueTables.Event);

   var vm = new Vue({
      el: "#directory",
      data: {
			columns: ['faire_name', 'annual', 'event_start_dt', 'venue_address_city', 'venue_address_country', 'venue_address_street', 'venue_address_state', 'event_dt', 'category', 'event_end_dt'],
         tableData: [], // this keeps the whole table
			filteredData: [], // this is the tableData with the filters applied
         options: {
            headings: {
               faire_name: 'Name',
					annual: 'Annual',
					event_start_dt: 'Date',
               venue_address_city: 'Location', 
               venue_address_country: 'Country',
            },
            templates: {
               venue_address_city: function (h, row, index) {
                  var text = row.venue_address_city;
                  if(row.venue_address_state) {
                     text += ', ' + row.venue_address_state;
                  }
                  return text;
               },
            },
            columnsDisplay: { // the sizes the columns disappear
					venue_address_city: 'min_tabletL',
					annual: 'min_tabletP',
               venue_address_country: 'desktop',
            },
            columnsClasses: {
               faire_name: 'col-name',
					annual: 'col-num',
					event_start_dt: 'col-date',
               venue_address_city: 'col-location', 
               venue_address_country: 'col-country',
					venue_address_street: 'col-hidden',
					venue_address_state: 'col-hidden',
					event_dt: 'col-hidden',
					category: 'col-hidden',
					event_end_dt: 'col-hidden',
            },
            pagination: { chunk: 5 }, // undocumented :(
				multiSorting: {
					  faire_name: [
							{
								 column: 'event_start_dt',
								 matchDir: false
							},
					  ],
					  venue_address_country: [
							{
								 column: 'event_start_dt',
								 matchDir: false
							},
					  ],
					  venue_address_city: [
							{
								 column: 'event_start_dt',
								 matchDir: false
							},
					  ],
				 }
         },
         filterVal: '',
			pastFaires: false,
			types: [
				{name: "Featured", description: "Larger-scale regional events"}, 
				{name: "Mini", description: "Community events"}, 
				{name: "School", description: "K-12 Faires (closed to general public)"}
			],
			buttonMessage: "Show Past Faires",
         map: null,
			markerCluster: null,
         mapDefaultZoom: 2,
			mapMinZoom: 2, 
			mapMaxZoom: 20,
         mapDefaultPos: {
				lat: 29.1070772,
				lng: -4.2299966
         },
         markers: ''
      },

      created: function() {
         var _self = this;
         axios.get('/query/?type=map')
            .then(function (response) {
               _self.$refs.loadingIndicator.classList.add("hidden");
               _self.outputData = response.data.Locations;
				   // convert start dt to numeric string for ease of ordering
				   Object.keys(_self.outputData).forEach(function(key) {
					  _self.outputData[key].event_start_dt = Date.parse(_self.outputData[key].event_start_dt);
					});
               _self.detectBrowser();
               //_self.getLocation(); we don't really need the users location for this map, as it starts zoomed out
               _self.initMap();
            })
            .catch(function (error) {
               console.log(error);
               _self.$refs.loadingIndicator.classList.add("hidden");
               _self.$refs.errorIndicator.classList.remove("hidden");
            });
      },
		updated: function() {
			firstLoaded = false; 
		},
      methods: {
         detectBrowser: function() {
            var useragent = navigator.userAgent,
               mapdiv = this.$refs.map;
          
            if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('Android') != -1 ) {
              mapdiv.style.width = '100%';
              mapdiv.style.height = '300px';
            } else {
              mapdiv.style.width = '100%';
              mapdiv.style.height = '450px';
            }
         },
         initMap: function() {
            this.$refs.mapTableWrapper.classList.remove("map-table-hidden");
				// on the first load, by date, otherwise, remember user choices
				if(firstLoaded == true) {
					this.$refs.directoryGrid.setOrder('event_start_dt', 'asc');
				}
				// filter out the past faires
				this.tableData = this.outputData.filter( function(values) {
					var endDate = new Date(values.event_end_dt);
					endDate.setDate(endDate.getDate() + 1);
					if(endDate > currentDate) {
						return values;
					}
				});
				this.filteredData = this.tableData; // filtered Data is used to draw the map
				
				var styledMapType = new google.maps.StyledMapType(
            [
					  {
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#f5f5f5"
							}
						 ]
					  },
					  {
						 "elementType": "labels.icon",
						 "stylers": [
							{
							  "visibility": "off"
							}
						 ]
					  },
					  {
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#616161"
							}
						 ]
					  },
					  {
						 "elementType": "labels.text.stroke",
						 "stylers": [
							{
							  "color": "#f5f5f5"
							}
						 ]
					  },
					  {
						 "featureType": "administrative.land_parcel",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#bdbdbd"
							}
						 ]
					  },
					  {
						 "featureType": "poi",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#eeeeee"
							}
						 ]
					  },
					  {
						 "featureType": "poi",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#757575"
							}
						 ]
					  },
					  {
						 "featureType": "poi.park",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#e5e5e5"
							}
						 ]
					  },
					  {
						 "featureType": "poi.park",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#9e9e9e"
							}
						 ]
					  },
					  {
						 "featureType": "road",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#ffffff"
							}
						 ]
					  },
					  {
						 "featureType": "road.arterial",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#757575"
							}
						 ]
					  },
					  {
						 "featureType": "road.highway",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#dadada"
							}
						 ]
					  },
					  {
						 "featureType": "road.highway",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#616161"
							}
						 ]
					  },
					  {
						 "featureType": "road.local",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#9e9e9e"
							}
						 ]
					  },
					  {
						 "featureType": "transit.line",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#e5e5e5"
							}
						 ]
					  },
					  {
						 "featureType": "transit.station",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#eeeeee"
							}
						 ]
					  },
					  {
						 "featureType": "water",
						 "elementType": "geometry",
						 "stylers": [
							{
							  "color": "#c9c9c9"
							}
						 ]
					  },
					  {
						 "featureType": "water",
						 "elementType": "labels.text.fill",
						 "stylers": [
							{
							  "color": "#9e9e9e"
							}
						 ]
					  }
					],
            {name: 'Styled Map'});

            const element = this.$refs.map;
            const options = {
               center: this.mapDefaultPos,
               zoom: this.mapDefaultZoom,
					minZoom: this.mapMinZoom,
					maxZoom: this.mapMaxZoom,
					restriction: {
						 latLngBounds: {north: 85, south: -85, west: -180, east: 180},
						 strictBounds: true
					  },
					mapTypeControlOptions: {
            		mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
                    'styled_map']
					}
            }
            this.map = new google.maps.Map(element, options);
				this.map.mapTypes.set('styled_map', styledMapType);
        		this.map.setMapTypeId('styled_map');
            this.addMarkers();
         },
         getLocation: function() {
            var infoWindow = new google.maps.InfoWindow,
               _self = this;
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
               navigator.geolocation.getCurrentPosition(
                  function(position) {
                     var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                     };
                     _self.map.setCenter(pos);
                     _self.map.setZoom(8);
                  },
                  function() {
                     _self.handleLocationError(true, infoWindow, _self.map.getCenter());
                  }
               );
            } else {
               // Browser doesn't support Geolocation
               _self.handleLocationError(false, infoWindow, _self.map.getCenter());
            }
         },
         handleLocationError: function(browserHasGeolocation, infoWindow, pos) {
            // NOTE (ts): handle this event in some other way? putting a popup on the map isn't very helpful
            console.error('User location check failed');
            // infoWindow.setPosition(pos);
            // infoWindow.setContent(browserHasGeolocation ? 'Error: The Geolocation service failed.' : 'Error: Your browser doesn\'t support geolocation.');
            // infoWindow.open(this.map);
         },
			// search filter
         searchFilter: function(data) { 
				this.$refs.directoryGrid.setFilter(this.filterVal);
            this.addMarkers();
         },
			// past faires filter
			psFilter: function(data){ 
				if(this.pastFaires == true) {
					this.buttonMessage = "Show Past Faires";
					this.tableData = this.outputData.filter( function(values) {
						var endDate = new Date(values.event_end_dt);
						endDate.setDate(endDate.getDate() + 1);
						if(endDate > currentDate) {
							return values;
						}
					});
				} else {
					this.buttonMessage = "Show Upcoming Faires";
					this.tableData = this.outputData.filter( function(values) {
						var endDate = new Date(values.event_end_dt);
						endDate.setDate(endDate.getDate() + 1);
						if(endDate > oneYearAgo) { // this shows 365 days of faires, to show more just return all values
							return values;
						}
					});
				}
				// there's gotta be a better way than just filtering by type again like this
				this.filteredData = this.tableData.filter( function(values) {
					var type = values.category;
						if(typeFilters.length < 1) {
							return values;
						}else if(typeFilters.includes(type)) {
							return values;
						}
				});
				this.addMarkers();
			},
			// type/category of faire filter
			typeFilter: function(data) { 
				// add to type filter array if checked on click, remove if unchecked
				if(data.originalTarget.checked == true) {
					typeFilters.push(data.originalTarget._value);
					if(data.originalTarget._value == 'Featured') {
						typeFilters.push('Flagship');
					}
				}
				if(data.originalTarget.checked == false) {;
					var index = typeFilters.indexOf(data.originalTarget._value);
					if (index !== -1) typeFilters.splice(index, 1);
					if(data.originalTarget._value == 'Featured') {
						typeFilters.splice(typeFilters.indexOf('Flagship'), 1);
					}
				}
				this.filteredData = this.tableData.filter( function(values) {
					var type = values.category;
					if(typeFilters.includes(type)) {
						return values;
					}
				});
            this.addMarkers();
         },
         filterOverride: function(data) {
            data.preventDefault();
         },
         onRowClick: function(data) {
            var pos = {
               lat: parseFloat(data.row.lat),
               lng: parseFloat(data.row.lng)
            };
            this.map.panTo(pos);
            this.map.setZoom(16);
         },
			// adding the markers to the map
         addMarkers: function() {
				// first clear all existing markers and markerClusters
				for(var i=0; i < this.markers.length; i++){
					  this.markers[i].setMap(null);
				}
				this.markers = new Array();
				if(this.markerCluster){
					this.markerCluster.clearMarkers();
				}
				var gMarkerIcon = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 6,
                fillOpacity: 1,
                strokeOpacity: 0,
            };
            this.markers = this.filteredData.map(function(location, i) {		
					// styling for the various types of faires... flagship and featured are now the same color
					switch (location.category) {
						case 'Flagship':
							gMarkerIcon.fillColor = '#D42410';
							break;
						case 'Featured':
							gMarkerIcon.fillColor = '#D42410';
							break;
						case 'School':
							gMarkerIcon.fillColor = '#F5A623';
							break;
						default:
							gMarkerIcon.fillColor = '#67D0F7';
					}
               //this math random business keeps faires that were in the same location year after year from being on top of each other and not individually clickable
               var latLng = {lat: parseFloat(location.lat) + Math.random()/1000, lng: parseFloat(location.lng) +Math.random()/1000};
               var marker =  new google.maps.Marker({
						icon: gMarkerIcon,
                  position: latLng,
                  label: '',
               });
               marker.addListener('click', function() {
						// for faires that have a start and end date
                  var myWindow = new google.maps.InfoWindow({
                     content: '<div style=""><h4>'+location.faire_name+'</h4><p>' + location.venue_address_street + '</p><p>' + location.event_dt + '</p><p><a href="'+location.faire_url+'" target="_blank">'+location.faire_url+'</a></p></div>'
                  });
                  myWindow.open(this.map, marker);
               });
               return marker;
            });
            //Add a marker clusterer to manage the markers.
            this.markerCluster = new MarkerClusterer(this.map, this.markers, {imagePath: '/wp-content/themes/makerfaire/js/mf-map/markers/m'});
         }
      },
  });
  jQuery("label[for=Mini] span").html("Community");
  jQuery("#pastFaires").on("click", function(){
	  jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
  });
}); // end doc ready



function formatDate(date) {
  var theDate = new Date(date);
  var monthNames = [
    "January", "February", "March",
    "April", "May", "June", "July",
    "August", "September", "October",
    "November", "December"
  ];

  var day = theDate.getDate();
  var monthIndex = theDate.getMonth();
  var year = theDate.getFullYear();

  return monthNames[monthIndex] + ', ' + day + ' ' + year;
}
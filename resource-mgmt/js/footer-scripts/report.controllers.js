// reports controller
rmgControllers.controller('reportsCtrl', ['$scope', '$routeParams', '$http', '$interval', 'uiGridConstants', 'uiGridGroupingConstants', function ($scope, $routeParams, $http, $interval, uiGridConstants, uiGridGroupingConstants) {
      $scope.reports = {};
      $scope.reports.loading = true;
      $scope.reports.showGrid = false;
      $scope.reports.showForms = false;
      $scope.reports.selFaire = '';

      $scope.reports.selectedFields = {};
      $scope.msg = {};
      $scope.sortField = '';
      $scope.sortChanged = function ( grid, sortColumns ) {         
         var sortField = [];
         angular.forEach(sortColumns, function (value, key) { 
            if(value !== undefined && value.displayName !== undefined){
               sortField.push(value.displayName);
            }                   
         });
         $scope.sortField = sortField.join(", "); // set the default sorting for the PDF header display
       }

      //set location based on url parameters
      if ("location" in $routeParams) {
         if ($routeParams.location === 'true') {
            $scope.reports.location = true;
         }
      }

      //set up gridOptions for reports
      $scope.gridOptions = {
         enableFiltering: true,
         enableSorting: true,
         enableGridMenu: true,
         rowHeight: 100,
         exporterMenuPdf: true, // hide PDF export          
         exporterPdfMaxGridWidth: 680,
         exporterPdfFooter: function ( currentPage, pageCount ) {
            return { text: 'Page ' + currentPage.toString() + ' of ' + pageCount.toString(), style: 'footerStyle' };
         },
         
         exporterPdfHeader: function ( currentPage, pageCount ) {
            if($scope.reports.subRoute =='lookup'){
               return { text: 'Maker Lookup By '+$scope.sortField, alignment: 'center',  style: 'headerStyle' };
            }
            if($scope.reports.subRoute =='schedule'){
              return { text: 'Schedule By '+$scope.sortField, alignment: 'center',  style: 'headerStyle' };
            }
            return;
         },
         exporterPdfCustomFormatter: function (docDefinition) {
            docDefinition.styles.headerStyle = {
              fontSize: 14,
              bold: true,
              margin:20,
              fillColor: '#f0f0f0' // Light gray background
            };
            return docDefinition;
          },
         
         exporterPdfDefaultStyle: {fontSize: 9},         
         exporterPdfTableHeaderStyle: {fontSize: 10, bold: true},
         exporterPdfTableStyle: {margin: [-20, 10, -10, -20]},    //need this to center grid
         exporterPdfOrientation: 'landscape',
         exporterPdfPageSize: 'LETTER',
         
         exporterHeaderFilter: function( displayName ) {
           if( displayName === 'Name' ) {
             return 'Person Name';
           } else {
             return displayName;
           }
         },
         exporterCsvFilename: 'export.csv',
         exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
         exporterFieldCallback: function (grid, row, col, input) {
            if (("editDropdownOptionsArray" in col.colDef)) {
               //convert gridArray to usable hash
               var optionsHash = {};
               var gridArray = col.colDef.editDropdownOptionsArray;
               for (var i = 0; i < gridArray.length; i++) {
                  optionsHash[gridArray[i].id] = gridArray[i].fkey;
               }
               
               if (!input) {
                  return '';
               } else {
                  if (optionsHash[input] !== undefined) {
                     return optionsHash[input];
                  } else {
                     return input;
                  }
               }
            } else {
               return input;
            }
         },
          //Allows external buttons to be pressed for exporting
    onRegisterApi: function( gridApi ) {
      $scope.gridApi = gridApi;
      $scope.gridApi.core.on.sortChanged( $scope, $scope.sortChanged );
      $scope.sortChanged($scope.gridApi.grid, [ $scope.gridOptions.columnDefs[1] ] );
    },
      };

      //set report column grouping
      $scope.reports.changeGrouping = function (groupBy) {
         $scope.gridApi.grouping.clearGrouping();
         if (groupBy == 'item') {
            $scope.gridApi.grouping.groupColumn('item');
            $scope.gridApi.grouping.groupColumn('resource_id');
         } else if (groupBy == 'faire') {
            $scope.gridApi.grouping.groupColumn('faire');
            $scope.gridApi.grouping.groupColumn('area');
            $scope.gridApi.grouping.groupColumn('subarea');
            $scope.gridApi.grouping.groupColumn('location');
         }
         $scope.gridApi.grouping.aggregateColumn('qty', uiGridGroupingConstants.aggregation.SUM);
      };

      //function to retrieve grid Data by faire
      $scope.retGridData = function () {
         $scope.reports.loading = true;
         $scope.reports.showGrid = true;
         var url = '/resource-mgmt/ajax/reports.ajax.php';
         var selTerm = '';
         $scope.gridOptions.columnDefs = [];
         //get grid data
         $http({
            method: 'post',
            url: url,
            data: JSON.stringify({'table': $scope.reports.tableName, 'type': type, 
               'faire': $scope.reports.selFaire, 'formSelect': $scope.reports.selForm,
               'subRoute': $scope.reports.subRoute
            }),
            headers: {'Content-Type': 'application/json'}
         })
                 .then(function (response) {
                  var sortField = [];
                    angular.forEach(response.data.columnDefs, function (value, key) {
                       if (value.name == 'faire' && $scope.reports.selFaire != '') {
                          angular.forEach(value.filter.selectOptions, function (selValue, selKey) {
                             if (selValue.label == $scope.reports.selFaire) {
                                selTerm = selValue.value;
                             }
                          });
                          if (selTerm != '') {
                             response.data.columnDefs[key].filter.term = selTerm;
                          }
                       }
                       if('sort' in value){                        
                        sortField.push(value.displayName);
                        if('direction' in value.sort){
                           if(value.sort.direction=='uiGridConstants.ASC'){
                              value.sort.direction = uiGridConstants.ASC;
                           }else if(value.sort.direction=='uiGridConstants.DESC'){
                              value.sort.direction = uiGridConstants.DESC;
                           }
                        }
                        value.sortingAlgorithm =  function(a, b, rowA, rowB, direction) {
                           var nulls = $scope.gridApi.core.sortHandleNulls(a, b);
                           if( nulls !== null ) {
                             return nulls;
                           } else {
                             if(a=='') return 0;
                             if(b=='') return -1;
                             if( a === b ) {
                               return 0;
                             }                
                             if(a>b) return 1;
                             if(b>a) return -1;
                             return 0;
                           }
                         }
                       }
                      
                       //apply select filter
                       if (("filter" in value)) {
                          value.filter.type = uiGridConstants.filter.SELECT;
                       }
                       //apply sort filter
                       if (("sort" in value)) {
                          value.sort.direction = uiGridConstants.ASC;
                       }
                    });                    
                    $scope.sortField = sortField.join(", "); // set the default sorting for the PDF header display

                    //populate column defs and data
                    $scope.gridOptions.columnDefs = response.data.columnDefs;
                    $scope.gridOptions.data = response.data.data;

                    //set up build your own data
                    if (("forms" in response.data)) {
                       if (("formSelect" in $routeParams)) {
                          $scope.reports.formSelect = $routeParams.formSelect.split(',');
                       } else {
                          $scope.reports.formSelect = [];
                       }
                       $scope.reports.forms = response.data.forms;
                    }
                    if (("fields" in response.data)) {
                       $scope.fieldSelect.data = response.data.fields;
                       var selfields = [];
                       //loop thru passed parameter fields
                       if ("fields" in $routeParams) {
                          selfields = $routeParams.fields.split(',');
                       }
                       //always check field 303 and 151
                       selfields.push('303', '151');

                       if ($scope.fieldSelect.gridApi.selection.selectRow) {
                          //var selfields = $routeParams.fields.split(',');
                          angular.forEach($scope.fieldSelect.data, function (value, key) {
                             if (selfields.indexOf(String(value.id)) != -1) {
                                // $interval whilst we wait for the grid to digest the data we just gave it
                                $interval(function () {
                                   $scope.fieldSelect.gridApi.selection.selectRow($scope.fieldSelect.data[key]);
                                }, 0, 1);
                             }
                          });

                       }
                    }
                    if (("rmt" in response.data)) {
                       $scope.reports.rmt = response.data.rmt;
                       //set resources based on url parameters
                       if ("resource" in $routeParams) {
                          var selResources = $routeParams.resource.split(',');
                          angular.forEach($scope.reports.rmt.resource, function (value, key) {
                             if (selResources.indexOf(String(value.value)) != -1) {
                                $scope.reports.rmt.resource[key].checked = true;

                             }
                          });
                       }
                       //set attributes based on url parameters
                       if ("attribute" in $routeParams) {
                          var selAttributes = $routeParams.attribute.split(',');
                          angular.forEach($scope.reports.rmt.attribute, function (value, key) {
                             if (selAttributes.indexOf(String(value.value)) != -1) {
                                $scope.reports.rmt.attribute[key].checked = true;
                             }
                          });
                       }
                       //set attention based on url parameters
                       if ("attention" in $routeParams) {
                          var selAttentions = $routeParams.attention.split(',');
                          angular.forEach($scope.reports.rmt.attention, function (value, key) {
                             if (selAttentions.indexOf(String(value.value)) != -1) {
                                $scope.reports.rmt.attention[key].checked = true;
                             }
                          });
                       }
                       //set attention based on url parameters
                       if ("other" in $routeParams) {
                          var selMeta = $routeParams.other.split(',');
                          angular.forEach($scope.reports.rmt.meta, function (value, key) {
                             if (selMeta.indexOf(String(value.value)) != -1) {
                                $scope.reports.rmt.meta[key].checked = true;
                             }
                          });
                       }
                    }
                 })
                 .finally(function () {
                    $scope.reports.loading = false;
                    $scope.reports.showGrid = true;
                 });
      };

      //default
      var tablename = 'wp_rmt_entry_resources';
      var type = 'tableData';

      //check route parameters to set header details
      if ($routeParams) {
         $scope.reports.subRoute = $routeParams.sub;
         var subRoute = $routeParams.sub;
         var pageTitle = 'Reports';
         var subTitle = '';

         if (subRoute === 'change') {
            tablename = 'wp_mf_lead_detail_changes';
            subTitle = 'Entry Change Report';
         } else if (subRoute === 'drill') {
            tablename = 'wp_rmt_entry_resources';
            subTitle = 'Resource Drill Down';
         } else if (subRoute === 'schedule') {
            tablename = 'wp_mf_location';
            subTitle = 'Faire Schedule Report';
         }else if (subRoute === 'lookup') {      
            tablename = 'wp_mf_location';            
            subTitle = 'Faire Lookup Report';            
         } else if (subRoute === 'tasksComp') {
            tablename = 'wp_mf_entity_tasks';
            subTitle = 'Tasks Completed';
         } else if (subRoute === 'sponsorPay') {
            tablename = 'sponsorOrder';
            type = "paymentRpt";
            subTitle = 'Sponsor Payments';
         } else if (subRoute === 'exhibitPay') {
            tablename = 'exhibitOrder';
            type = "paymentRpt";
            subTitle = 'Sponsor Payments';
         } else if (subRoute === 'notes') {
            tablename = 'notes';
            type = "notesRpt";
            subTitle = 'Faire Notes Report';
         } else if (subRoute === 'tickets') {
            tablename = 'tickets';
            type = "ticketsRpt";
            subTitle = 'Faire Entry Passes Report';
         } 

         jQuery('#pageTitle').html(pageTitle);
         jQuery('#subTitle').html(subTitle);
         $scope.reports.tableName = tablename;

         if ("faire" in $routeParams) {
            $scope.reports.selFaire = $routeParams.faire;            
            $scope.retGridData(); //pull the faire data
         } else {
            $scope.reports.selFaire = '';
         }
      }

      $scope.filterExport = function (grid, row, col, input) {
         return 'unknown';
      };

      $scope.highlightFilteredHeader = function (row, rowRenderIndex, col, colRenderIndex) {
         if (col.filters[0].term) {
            return 'header-filtered';
         } else {
            return '';
         }
      };
      $scope.checkSubroute = function (type) {
         if ($routeParams.sub === 'tasksComp') {
            $scope.retrieveData('forms');
         } else {
            $scope.retGridData();
         }
      };
      
      //faire dropdown
      $scope.retrieveData = function (type) {         
         if (type === 'faires') {
            var vars = jQuery.param({'type': type});
            var url = '/resource-mgmt/ajax/ajax.php';
            var head2pass = {'Content-Type': 'application/x-www-form-urlencoded'};
         } else if (type === 'forms') {
            var vars = jQuery.param({'type': type, 'faire': $scope.reports.selFaire});
            var url = '/resource-mgmt/ajax/ajax.php';
            var head2pass = {'Content-Type': 'application/x-www-form-urlencoded'};
         }

         //get grid data
         $http({
            method: 'post',
            url: url,
            data: vars,
            headers: head2pass
         })
                 .then(function (response) {
                  
                    if ("error" in response.data) {
                       alert(response.data.error);
                    } else if (type === 'faires' || type === 'forms') {
                       $scope.reports[type] = response.data[type];                       
                       if(type==='faires'){
                        $scope.reports.selFaire = $scope.reports.faires[0].ID;  
                        $scope.retGridData(); //pull the faire data                     
                       }
                    }                    
                 }).finally(function () {
            if (type === 'faires') {
               faires = $scope.reports.faires;
               angular.forEach(faires, function (value, key) {
                  if (value.faire === $scope.subRoute) {
                     $scope.reports.selFaire = key;
                     $scope.retGridData(); //pull the faire data
                  }
               });
            } else if (type === 'forms') {
               $scope.reports.showForms = true;
            }
         });
      }; //end faire drop down
     
      //export functionality
      $scope.export = function(export_format='pdf'){    
         var export_row_type       = 'visible';
         var export_column_type    = 'visible';
         
         $scope.gridApi.exporter.pdfExport( export_row_type, export_column_type );
         
      };
      
 
   }])
        .filter('griddropdown', function () {
           return function (input, map) {
              var result;
              var match;
              //convert gridArray to usable hash
              var optionsHash = {};
              if (map.col) {
                 var gridArray = map.col.colDef.editDropdownOptionsArray;
                 for (var i = 0; i < gridArray.length; i++) {
                    optionsHash[gridArray[i].id] = gridArray[i].fkey;
                 }
              }

              if (!input) {
                 return '';
              } else if (result === optionsHash[input]) {
                 return result;
              } else if ((match === input.match(/(.+)( \(\d+\))/)) && (result === optionsHash[match[1]])) {
                 return result + match[2];
              } else {
                 return input;
              }
           };
        });

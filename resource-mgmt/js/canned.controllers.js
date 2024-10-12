// reports controller
rmgControllers.controller('cannedCtrl', ['$scope', '$routeParams', '$http','$interval','uiGridConstants', function ($scope, $routeParams, $http,$interval,uiGridConstants) {
  $scope.reports            = {};
  $scope.reports.loading    = true;
  $scope.reports.showGrid   = false;
  $scope.reports.showLinks  = false;
  $scope.reports.selFaire   = '';
  $scope.data               = [];
  $scope.msg                = {};
  $scope.canned_rpt         = [];
  $scope.reports_only       = jQuery('#reports_only').val();  
  $scope.display_faire      = '';

  $scope.handleSelect = function() {       
    if($scope.reports.subRoute!=''){
      //redirect to the correct canned report based on selection 
      window.location = '#canned/'+ $scope.reports.subRoute+'/'+$scope.reports.selFaire;
    }        
  };

  //faire dropdown
  $scope.retrieveData = function(type) {
    if(type=='faires'){
      var vars = jQuery.param({ 'type' :  type});
      var head2pass = {'Content-Type': 'application/x-www-form-urlencoded'};
      
      //get grid data
      $http({
        method: 'post',
        url: '/resource-mgmt/ajax/ajax.php',
        data: vars,
        headers: head2pass
      })
      .then(function(response){
        if("error" in response.data) {
          alert(response.data.error);
        }else if(type=='faires'){          
          $scope.data[type] = response.data[type];  
          if($scope.reports.selFaire=='') {
            setFaire = 0;
            $scope.reports.selFaire  = $scope.data.faires[0].ID;              
            $scope.display_faire     = $scope.data.faires[0].faire_name;
          }          
                            
          $scope.reports.showLinks = true;            
        }
      }).finally(function () {        
        if(type=='faires'){                  
          faires = $scope.data.faires;          
          angular.forEach(faires, function(value,key){                        
            if(value.ID==$scope.reports.selFaire){              
              $scope.display_faire     = $scope.data.faires[key].faire_name;              
            }
          });
        }
      });
    }    
  };

  //set up gridOptions for predefined reports
  $scope.gridOptions = {
    exporterMenuExcel: false,
    enableFiltering: true,
    enableGridMenu: true,
    showGridFooter: true,
    showColumnFooter: true,
    rowHeight: 100,
    exporterMenuPdf: true, // hide PDF export
    exporterPdfDefaultStyle: {fontSize: 6},    
    exporterPdfTableStyle: {margin: [-20, -10, -10, -20]},    //need this to center grid
    exporterPdfTableHeaderStyle: {fontSize: 6, bold: false},
    exporterCsvFilename: $routeParams.sub+'-export.csv',
    exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
     //Allows external buttons to be pressed for exporting
    onRegisterApi: function(gridApi){$scope.gridApi = gridApi;},
    exporterPdfMaxGridWidth: 680,
    exporterPdfFooter: function ( currentPage, pageCount ) {
      return { text: 'Page ' + currentPage.toString() + ' of ' + 
        pageCount.toString(), style: 'footerStyle' };
    },
    exporterFieldCallback: function( grid, row, col, input ) {

      if(("editDropdownOptionsArray" in col.colDef)){        
        //console.log(col);
        //convert gridArray to usable hash
        var optionsHash =  {};
        var gridArray = col.colDef.editDropdownOptionsArray;
        for (var i = 0; i < gridArray.length; i++) {
          optionsHash[gridArray[i].id] = gridArray[i].fkey;
        }
        if (!input){
          return '';
        } else {
          return optionsHash[input];
        }
      }else{
        //return grid.getCellDisplayValue(row, col);
        return input;
      }
    }
  };

//export functionality
$scope.export = function(export_format='pdf'){    
  var export_row_type       = 'visible';
  var export_column_type    = 'visible';
  if ($scope.export_format == 'csv') {
    var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));    
    $scope.gridApi.exporter.csvExport( export_row_type, export_column_type, myElement );
  } else if (export_format == 'pdf') {    
    $scope.gridApi.exporter.pdfExport( export_row_type, export_column_type );
  };
};

 //get report data
  $scope.reports.callAJAX = function(pvars){
    $scope.reports.showGrid  = true;
    $scope.reports.loading = true;
    var sortParams = {
      //'field_303':  {'direction': uiGridConstants.ASC, 'priority':4},
      'area':       {'direction': uiGridConstants.ASC, 'priority':0},
      'subarea':    {'direction': uiGridConstants.ASC, 'priority':1},
      'location':   {'direction': uiGridConstants.ASC, 'priority':2}
                };

    //get grid data
    $http({
      method: 'post',
      url: '/resource-mgmt/ajax/reports.ajax.php',
      data: JSON.stringify(pvars),
      headers: {'Content-Type': 'application/json'}
    })
    .then(function(response){
      if(("success" in response.data)){
        //if success = false, display error message
        if(!response.data.success){
          alert(response.data.message);
        }
      }else{
        //get grid data
        angular.forEach(response.data.columnDefs, function(value, key) {
          //add sorting - status, area, sub area, location
          var findMe = value.field;
          if(findMe in sortParams){            
            value.sort = {'direction':sortParams[findMe].direction, 'priority': sortParams[findMe].priority};

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
          if('aggregationType' in value){
            if(value.aggregationType =='uiGridConstants.aggregationTypes.sum'){
              value.aggregationType = uiGridConstants.aggregationTypes.sum;
              value.aggregationHideLabel = true;
            }
          }          
          if ('filter' in value) {
            if('selectOptions' in value.filter) {
              value.filter.type = uiGridConstants.filter.SELECT;
            }            
          }
          if('sortingAlgorithm' in value){
            if(value.sortingAlgorithm =='numeric'){
              value.sortingAlgorithm =  function(a, b, rowA, rowB, direction) {    
                // this keeps the set values to the top, for both ascending and descending order
                if(direction == "asc") {
                  if(!a) { // a is null, push it to the bottom                                
                    return 1;
                  }else if(!b) { // b is null, push it to the bottom                
                    return -1;
                  } else {
                    return a - b; // Regular sorting for non-null values
                  }      
                } else if(direction == "desc") {
                  if(!a) { // a is null, push it to the bottom                                
                    return -1;
                  }else if(!b) { // b is null, push it to the bottom                
                    return 1;
                  } else {
                    return a - b; // Regular sorting for non-null values
                  }  
                }                     
              }                
            }
          }            
        
            
        });
        $scope.gridOptions.columnDefs = response.data.columnDefs;
        $scope.gridOptions.data       = response.data.data;
        $scope.reports.showGrid = true;
        $scope.reports.showbuild = false;

      }
    })
    .finally(function () {
      $scope.reports.loading = false;
    });
  };
  
  $scope.filterAfterDate = function(){
    var year = jQuery("#year").val();
    var month = jQuery("#month").val();
    var day = jQuery("#day").val();
    
    vars = {
      "faire": faire,
      "table": "wp_mf_lead_detail_changes",
      "type": "tableData",
      "dateAfter": year+"-"+month+"-"+day,
      "subRoute":subRoute
    };

    var subTitle = 'Sponsor Internet';
    $scope.reports.callAJAX(vars);
  }

  if($routeParams){
    if(typeof $routeParams.sub !== 'undefined' && $routeParams.sub !== 'undefined'){
      if(!jQuery("#wrapper").hasClass("toggled")){
        //hide the sidebar
        $("#menu-toggle").click();
      }
    }

    $scope.reports.subRoute = $routeParams.sub;
    $scope.reports.selFaire = $routeParams.faire;

    if("faire" in $routeParams){
      var faire = $routeParams.faire;                 
    }else{
      $scope.reports.selFaire = '';
      var faire = '';
    }
    
    var subRoute  = $routeParams.sub;
    var pageTitle = 'Reports';
    var subTitle  = '';
    if(subRoute=='sponsor_order'){
      vars = {"faire": faire,
               "table": "sponsorOrder",                
               "type":  "sponsorOrderRpt"
             };
     var subTitle = 'Sponsor Orders(s)';
     $scope.reports.callAJAX(vars);
   }else
    if(subRoute=='sponsor_pymt'){
       vars = {"faire": faire,
                "table": "sponsorOrder",                
                "type":  "paymentRpt"
              };
      var subTitle = 'Sponsor Payment(s)';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=='cm_pymt'){
       vars = {"faire": faire,
               "table": "exhibitOrder",
               "type": "paymentRpt"
            };
      var subTitle = 'Exhibit Payment(s)';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=='nonprofit_pymt'){
       vars = {"formSelect":[],
              "formType":["Exhibit", "Master"],
              "faire": faire,
              "payments":true,
              "selectedFields":[
                {"id":"304.17","label":"Flags","choices":"NP Fee Full","type":"checkbox"},
                {"id":"304.18","label":"Flags","choices":"NP Fee Discount","type":"checkbox"},
                {"id":"304.19","label":"Flags","choices":"NP Fee Waived","type":"checkbox"},

                {"id":151,"label":"Exhibit Name","choices":"","type":"text","inputs":"", "order":25},
                {"id":45,"label":"Are you a:","choices":"Non-profit or Cause or Mission based organization (Exhibit Fee applicable)","type":"radio","exact":true},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true}
              ],
              "rmtData":{"resource":[],"attribute":[],"attention":[],
              "meta":[{"id":"res_status","type":"meta","value":"Resource Status","checked":true}]},
              "type":"customRpt"
            };
      var subTitle = 'Nonprofit Payment(s)';
      $scope.reports.callAJAX(vars);        
    }else
    if(subRoute=='am_summary'){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "entryIDorder": 50,
              "entryIDwidth": 30,
              "locationOrder": 10,
              "formTypeorder": 400,                            
              "dispFormType":false,
              "dispFormID":false,
              "useFormSC": true,
              "placedOnly":true,
              "selectedFields":[                
                {"id":96, "label":"Maker", "choices":"", "type":"name",
                 "inputs":[{"id":"96.3","label":"First","name":""},{"id":"96.6","label":"Last","name":""}], 
                 "order":250, "width":'*'
                },
                //{"id":98,"label":"Contact Email","choices":"","type":"email","inputs":"", "order":260},
                {"id":99,"label":"Contact #","choices":"","type":"phone","inputs":"", "order":270},
                {"id":151,"label":"Proj Name","choices":"","type":"text","inputs":"", "order":40},                    
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true, "order":150},                          
                {"id":879,"label":"Days","choices":"all","type":"checkbox","order":120},
                {"id":339,"label":"Type","choices":"all","type":"checkbox", "order":230, "width":40}
             ],
             "rmtData":{
                "resource":[
                  {"id":"all","value":"ALL RESOURCES","checked":true, "order":60}
                ],
                "attribute":[
                  {"id":"2",  "value":"Final Space Size","checked":true, "order":110}                  
                ],
                "attention":[
                  {"id":"9","value":"Area Manager Notes","checked":true, "order":80},
                  {"id":"10","value":"Early Setup","checked":true, "order":90},
                  {"id":"11","value":"No Friday","checked":true, "order":100}
                ],                
                "meta":[]
              },
             "type":"customRpt",
             "location":true};
      var subTitle = 'AM summary(short)';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=='zoho'){
      vars = {"formSelect":[],
              "formType":["Master", "Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "entryIDorder": 200,
              "locationOrder": 300,
              "formTypeorder":400,
              "selectedFields":[          
                {"id":879,"label":"Days","choices":"all","type":"checkbox"},
                {"id":339,"label":"Exhibit Type","choices":"all","type":"checkbox"},
                {"id":16,"label":"EXHIBIT SUMMARY","choices":"","type":"textarea","inputs":"", "order":1500},
                {"id":83,"label":"FIRE","choices":"all","type":"radio", "order":1800},
                
                {"id":85,"label":"Describe any fire or safety issues.","choices":"","type":"textarea","inputs":""},
                {"id":96,"label":"MAKER NAME","choices":"","type":"name", "order":500,
                  "inputs":[
                    {"id":"96.3","label":"First","name":""},
                    {"id":"96.6","label":"Last","name":""},
                    ]
                },
                {"id":98,"label":"EMAIL","choices":"","type":"email","inputs":"", "order":700},
                {"id":99,"label":"PHONE","choices":"","type":"phone","inputs":"", "order":600},
                {"id":151,"label":"EXHIBIT","choices":"","type":"text","inputs":"", "order":100},
                {"id":293,"label":"WRIST","choices":"Yes","type":"radio", "order":1700},
                {"id":293,"label":"WRIST","choices":"No","type":"radio", "order":1700},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":303,"label":"Status","choices":"Rejected","type":"radio"},
                {"id":303,"label":"Status","choices":"Wait List","type":"radio"},
                {"id":303,"label":"Status","choices":"Cancelled","type":"radio"},
                {"id":317,"label":"WASTE","choices":"Yes","type":"radio", "order":1900},
                {"id":317,"label":"Will your exhibit produce any waste?","choices":"No","type":"radio"}
              ],
              "rmtData":{
                "resource":[
                  {"id":"all","value":"ALL RESOURCES","checked":true, "order":1600},
                  {"id":"2","value":"TABLE","checked":true, "order":900},
                  {"id":"3","value":"CHAIR","checked":true, "order":1000},
                  {"id":"9","value":"ELEC","checked":true, "order":1100},
                  {"id":"15","value":"H2O","checked":true, "order":2000},
                  {"id":"18","value":"FENCE","checked":true, "order":2100},
                  {"id":"19","value":"BARR","checked":true, "order":2200},
                  {"id":"45","value":"UMB","checked":true, "order":2300},
                  {"id":"47","value":"GRIND","checked":true, "order":2400}],
                "attribute":[
                  {"id":"2","value":"SIZE","checked":true, "order":800},
                  {"id":"4","value":"IN/OUT","checked":true, "order":1400},
                  {"id":"9","value":"NZ","checked":true, "order":1300},
                  {"id":"11","value":"INT","checked":true, "order":1200}
                ],
                "attention":[
                  {"id":"9","value":"Area Manager Notes","checked":true, "order":2500},
                  {"id":"10","value":"Early Setup","checked":true},
                  {"id":"11","value":"No Friday","checked":true},
                  ],"meta":[]},
              "type":"customRpt",
              "location":true
            };
      var subTitle = 'Zoho';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=="am_tcp"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "entryIDorder": 200,
              "locationOrder": 300,
              "formTypeorder":400,
              "selectedFields":[
                {"id":151,"label":"EXHIBIT","choices":"","type":"text","inputs":"", "order":100},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":303,"label":"Status","choices":"Rejected","type":"radio"},
                {"id":303,"label":"Status","choices":"Wait List","type":"radio"},
                {"id":303,"label":"Status","choices":"Cancelled","type":"radio"},
                {"id":879,"label":"Days","choices":"all","type":"checkbox"},
                {"id":339,"label":"Exhibit Type","choices":"all","type":"checkbox"}
              ],
              "rmtData":{
                "resource":[
                  {"id":"2","value":"TABLE","checked":true,"aggregated":false, "order":2000,"comments":false},
                  {"id":"3","value":"CHAIR","checked":true,"aggregated":false, "order":3000,"comments":false},
                  {"id":"9","value":"ELEC","checked":true, "order":4000,"comments":false},
                  {"id":"10","value":"ELEC 220V","checked":true, "order":5000,"comments":false}
                ],
                "attribute":[
                  {"id":"2","value":"SIZE","checked":true, "order":1000}
                ],
                "attention":[],
                "meta":[]
              },
              "type":"customRpt",
              "location":true};
      var subTitle = 'AM TCP';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=="table_chairs"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "rtnIfRMTempty" : false,
              "faire": faire,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "entryIDorder": 50,
              "locationOrder": 10,              
              
              "placedOnly":true,
              "selectedFields":[                
                {"id":879,"label":"Days","choices":"all","type":"checkbox", "order":80},
                {"id":339,"label":"Entry Type","choices":"all","type":"checkbox", "order":90},
                {"id":151,"label":"Proj Name","choices":"","type":"text","inputs":"", "order":40},                
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":false, "order":110},
                
              ],
              "rmtData":{
                "resource":[
                  {"id":"2","value":"Tables","checked":true,"aggregated":false,"order":60,"columns":true, 'sortingAlgorithm':true},
                  {"id":"3","value":"Chairs","checked":true,"aggregated":false,"order":70,"columns":true, 'sortingAlgorithm':true}
                ],
                "attribute":[],"attention":[],"meta":[]
              },
              "type":"customRpt",
              "location":true};
      var subTitle = 'Table/Chairs';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=="barr_fence"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "rtnIfRMTempty" : false,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "entryIDorder": 50,
              "locationOrder": 10,
              "placedOnly":true,   
              "selectedFields":[
                {"id":151,"label":"Proj Name","type":"text", "order":40},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true, "order":140},
                {"id":879,"label":"Days","choices":"all","type":"checkbox", "order":110},
                {"id":339,"label":"Entry Type","choices":"all","type":"checkbox", "order":120},
              ],
              "orderBy":'location',
              "rmtData":{
                "resource":[                  
                  {"id":"19","value":"Barricade","checked":true,"aggregated":false, "order":70,"columns":true},
                  {"id":"18","value":"Fencing","checked":true,"aggregated":false, "order":80,"columns":true},
                  {"id":"22","value":"Pipe & Drape","checked":true,"aggregated":false, "order":90,"columns":true},
                  {"id":"23","value":"Pipe Only","checked":true,"aggregated":false, "order":100,"columns":true},

                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'Barr/Fence';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="electrical"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "rtnIfRMTempty" : false,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "entryIDorder": 50,
              "locationOrder": 10,  
              "placedOnly":true,
              "selectedFields":[
                {"id":74,"label":"What are you powering","choices":"","type":"text", "order":100},                                 
                {"id":"879",  "label":"Days","choices":"all","type":"checkbox", "order":110},
                {"id":339,"label":"Entry Type","choices":"all","type":"checkbox", "order":120},                
                {"id":151,"label":"Proj Name","choices":"","type":"text","inputs":"", "order":40}, 
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"order":140},                                
              ],
              "rmtData":{
                "resource":[
                  {"id":"9","value":"Electrical 120V","checked":true,"aggregated":false,  "comments":true, "columns":true, "totals":true,"order":70},
                  {"id":"10","value":"Electrical 220V","checked":true,"aggregated":false, "comments":true, "columns":true, "totals":true,"order":80}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
            var subTitle = 'Electrical';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="guest_seat"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "selectedFields":[
                {"id":151,"label":"Proj Name","choices":"","type":"text","inputs":"", "order":25},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":879,"label":"Days","choices":"all","type":"checkbox"},
                {"id":339,"label":"Exhibit Type","choices":"all","type":"checkbox"},
              ],
              "rmtData":{
                "resource":[
                  {"id":"11","value":"Bench","checked":true,"aggregated":false},
                  {"id":"12","value":"Bleachers","checked":true,"aggregated":false}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'Guest Seating';
      $scope.reports.callAJAX(vars);

    //work bench and stools report
    }else if(subRoute=="wb_stools"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "rtnIfRMTempty" : false,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "placedOnly":true,
              "entryIDorder": 50,
              "locationOrder": 10,
              "selectedFields":[
                {"id":879,"label":"Days","choices":"all","type":"checkbox","order":100},
                {"id":339,"label":"Entry Type","choices":"all","type":"checkbox", "order":110},
                {"id":151,"label":"Proj Name","choices":"","type":"text","inputs":"", "order":40},                
                {"id":303,"label":"Status","choices":"Accepted","type":"radio", "order":130},                
                
              ],
              "rmtData":{
                "resource":[
                  {"id":"11","value":"Bench","checked":true,"aggregated":false, "order":60, "columns":true, "totals":true},
                  {"id":"12","value":"Bleachers","checked":true,"aggregated":false, "order":85, "columns":true, "totals":true},
                  {"id":"41","value":"Work Bench","checked":true,"aggregated":false, "order":70, "columns":true, "totals":true},
                  {"id":"42","value":"Stools","checked":true,"aggregated":false,"order":80, "columns":true, "totals":true}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'Workbench/Stools';
      $scope.reports.callAJAX(vars);

    }else if(subRoute=="specials"){
      vars = {"formSelect":[],
              "formType":["Master","Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "dispFormType":false,
              "useFormSC": false,
              "entryIDorder": 50,
              "locationOrder": 10,
              "rtnIfRMTempty": false,     
              "placedOnly":true,         
              "selectedFields":[
                {"id":879,"label":"Days","choices":"all","type":"checkbox","order":100},
                {"id":339,"label":"Entry Type","choices":"all","type":"checkbox", "order":110},
                {"id":151,"label":"Proj Name","choices":"","type":"text","inputs":"", "order":40},                
                {"id":303,"label":"Status","choices":"Accepted","type":"radio", "order":130},                
                
              ],
              "rmtData":{
                "resource":[
                  {"id":"15","value":"Water","checked":true,"aggregated":true, "order":60},
                  {"id":"19","value":"Barricade","checked":true,"aggregated":true, "order":61},
                  {"id":"35","value":"Heavy Equipment","checked":true,"aggregated":true,"order":62},
                  {"id":"47","value":"Storage","checked":true,"aggregated":true,"order":63},
                  {"id":"26","value":"Rigging","checked":true,"aggregated":true,"order":64},
                  {"id":"28","value":"Sand","checked":true,"aggregated":true,"order":65},
                  {"id":"29","value":"Sand Bags","checked":true,"aggregated":true,"order":66},
                  {"id":"14","value":"Garbage","checked":true,"aggregated":true,"order":66},
                  {"id":"16","value":"Water Coolers","checked":true,"aggregated":true,"order":66},
                  {"id":"17","value":"Whiteboard","checked":true,"aggregated":true,"order":67},
                  {"id":"20","value":"Road Plate","checked":true,"aggregated":true,"order":68},
                  {"id":"21","value":"Milk Crates","checked":true,"aggregated":true,"order":69},
                  {"id":"24","value":"Port A Pottie","checked":true,"aggregated":true,"order":70},
                  {"id":"25","value":"Propane","checked":true,"aggregated":true,"order":71},
                  {"id":"27","value":"Safety Glasses","checked":true,"aggregated":true,"order":72},
                  {"id":"30","value":"Linens","checked":true,"aggregated":true,"order":73},
                  {"id":"31","value":"Yellow Jacket","checked":true,"aggregated":true,"order":74},
                  {"id":"32","value":"Signage","checked":true,"aggregated":true,"order":75},
                  {"id":"33","value":"Fire Extinguisher","checked":true,"aggregated":true,"order":76},
                  {"id":"34","value":"Container","checked":true,"aggregated":true,"order":77},
                  {"id":"45","value":"Umbrella","checked":true,"aggregated":true,"order":78},
                  {"id":"46","value":"Umbrella Stand","checked":true,"aggregated":true,"order":79},
                  {"id":"48","value":"Tent Walls","checked":true,"aggregated":true,"order":80},
                  {"id":"49","value":"Tent - Extra","checked":true,"aggregated":true,"order":81},
                  {"id":"50","value":"Special","checked":true,"aggregated":true,"order":82},
                  {"id":"51","value":"Stage","checked":true,"aggregated":true,"order":83},
                  {"id":"52","value":"Extension Cords","checked":true,"aggregated":true,"order":84},
                  {"id":"53","value":"Security","checked":true,"aggregated":true,"order":85},
                  {"id":"54","value":"Internet _Sponsors","checked":true,"aggregated":true,"order":86},
                  {"id":"55","value":"Audio Visual","checked":true,"aggregated":true,"order":87},
                  {"id":"56","value":"Flooring","checked":true,"aggregated":true,"order":88},
                  {"id":"57","value":"Structure","checked":true,"aggregated":true,"order":89},
                  {"id":"58","value":"Traveler","checked":true,"aggregated":true,"order":90},
                  {"id":"59","value":"Weekend","checked":true,"aggregated":true,"order":91},
                ],
                "attention":[
                  {"id":"10","value":"Early Setup","checked":true, "order":95},
                  {"id":"11","value":"No Friday","checked":true, "order":96},                                                      
                ],
                "attribute":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'Specials';
      $scope.reports.callAJAX(vars);

    }else if(subRoute=="label"){
      vars = {"formSelect":[],"formType":["Master","Exhibit","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "useFormSC": true,
              "formTypeorder": 10,
              "formTypeLabel": "SALES",
              "entryIDLabel": "PROJECT_ID",
              "selectedFields":[
                {"id":"151",  "label":"PROJECT_NAME","choices":"","type":"text","inputs":"", "order":20},//column C
                {"id": "73",  "label":"POWER","choices":"all","type":"radio","order":50},   //column F
                {"id": "75",  "label":"AMPS","choices":"all","type":"radio","order":60},    //column G
                {"id":"303",  "label":"STATUS","choices":"all","type":"radio","order":70}, //column H
                {"id": "56",  "label":"CROWDSOURCE_FUNDING","choices":"all","type":"radio", "order":80},  //column I
                {"id":"320",  "label":"TOPIC","choices":"all","type":"select", "order":90}, //column J
                {"id":"302",  "label":"PRE_LOC","choices":"all","type":"checkbox", "order":100}, //column K
                {"id":"307",  "label":"REQUEST","choices":"","type":"text","inputs":"", "order":110},  //column L
                {"id": "70",  "label":"OTHER","choices":"all","type":"checkbox", "order":120}, //column M
                {"id":"101.6","label":"COUNTRY","choices":"Country","type":"address", "order":130},  //column N
                {"id": "66",  "label":"ACTIVITY","choices":"all","type":"radio","order":160},   //column Q
                {"id": "44",  "label":"FOOD","choices":"all","type":"radio","order":180},    //column S
                {"id": "84",  "label":"TOOL","choices":"all","type":"radio","order":190},  //column T
                {"id": "83",  "label":"FIRE","choices":"all","type":"radio","order":200}, //column U
                {"id":"879",  "label":"Days","choices":"all","type":"checkbox"},
                {"id":"339",  "label":"Exhibit Type","choices":"all","type":"checkbox"},
              ],
              "rmtData":{
                "resource":[
                  //{"id":"9","value":"ELEC 120V","checked":true,"order":2000},
                  //{"id":"10","value":"ELEC 220V","checked":true,"order":2100}
                ],
                "attribute":[
                  {"id":"19","value":"BOOTH_SIZE","checked":true,"order":30,'comments':true},//column D
                  {"id":"20","value":"BOOTH_LOCATION","checked":true,"order":40,'comments':false},//column E
                  {"id":"6","value":"LIGHTING","checked":true,"order":140,'comments':true},//column O
                  {"id":"9","value":"NOISE","checked":true,"order":150,'comments':true},  //column P
                  {"id":"11","value":"INTERNET","checked":true,"order":170,'comments':true}  //column R
                ],
                "attention":[],
                "meta":[]
              },"type":"customRpt"};

      var subTitle = 'Label Placement';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="spdist"){
      vars = {"formSelect":[],
              "formType":["Sponsor"],
              "faire": faire,
              "selectedFields":[
                {"id":151,"label":"Sponsor Company Name","choices":"","type":"text","inputs":""},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":303,"label":"Status","choices":"Rejected","type":"radio"},
                {"id":303,"label":"Status","choices":"Wait List","type":"radio"},
                {"id":303,"label":"Status","choices":"Cancelled","type":"radio"},                
                {"id":879,"label":"Days","choices":"all","type":"checkbox"},
                {"id":339,"label":"Exhibit Type","choices":"all","type":"checkbox"},
                {"id":737,"label":"Additional Items","choices":"","type":"textarea"},
                {"id":749,"label":"Custom Order","choices":"","type":"textarea","inputs":""},
                {"id":750,"label":"Custom Order Price","choices":"","type":"number","inputs":""}
              ],
              "rmtData":{
                "resource":[
                  {"id":"all","value":"All Resources","checked":true,"aggregated":true},
                  {"id":"2","value":"Tables","checked":true,"aggregated":true},
                  {"id":"3","value":"Chairs","checked":true,"aggregated":true},
                  {"id":"9","value":"Electrical 120V","checked":true,"aggregated":true},
                  {"id":"10","value":"Electrical 220V","checked":true,"aggregated":true},
                  {"id":"11","value":"Bench","checked":true,"aggregated":true},
                  {"id":"14","value":"Garbage","checked":true,"aggregated":true},
                  {"id":"19","value":"Barricade","checked":true,"aggregated":true},
                  {"id":"22","value":"Pipe & Drape","checked":true,"aggregated":true},
                  {"id":"29","value":"Sand Bags","aggregated":true},
                  {"id":"30","value":"Linens","checked":true,"aggregated":true},
                  {"id":"41","value":"Work Bench","checked":true,"aggregated":true},
                  {"id":"42","value":"Stools","checked":true,"aggregated":true},
                  {"id":"45","value":"Umbrella","checked":true,"aggregated":true},
                  {"id":"52","value":"Extension Cords","checked":true,"aggregated":true},
                  {"id":"53","value":"Security","checked":true,"aggregated":true},
                  {"id":"54","value":"Internet _Sponsors","checked":true,"aggregated":true},
                  {"id":"55","value":"Audio Visual","checked":true,"aggregated":true},
                  {"id":"56","value":"Flooring","checked":true,"aggregated":true}
                ],
                "attribute":[
                  {"id":"all","value":"All Attributes","checked":true,"aggregated":true}
                ],
                "attention":[],"meta":[],"comment":[]
              },
              "type":"customRpt",
              "location":true};
      var subTitle = 'Sponsor Distribution';
      $scope.reports.callAJAX(vars);
    }else if(subRoute==="spint"){
      vars = {"formSelect":[],
              "formType":["Sponsor"],
              "faire": faire,
              "selectedFields":[
                {"id":78,"label":"Does your exhibit use or disrupt radio frequencies?","choices":"all","type":"radio"},
                {"id":"79","label":"Booth includes: (check all that apply)","choices":"all","type":"checkbox"},
                {"id":81,"label":"Describe additional details of your RF use.","choices":"","type":"textarea","inputs":""},
                {"id":151,"label":"Sponsor Company Name","choices":"","type":"text","inputs":""},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":303,"label":"Status","choices":"Rejected","type":"radio"},
                {"id":303,"label":"Status","choices":"Wait List","type":"radio"},
                {"id":303,"label":"Status","choices":"Cancelled","type":"radio"},
                {"id":793,"label":"Let's Talk","choices":"all","type":"radio"},
                {"id":160,"label":"Name Maker 1","choices":"","type":"name","inputs":[
                  {"id":"160.2","label":"Prefix","name":"","choices":[
                      {"text":"Mr.","value":"Mr.","isSelected":false,"price":""},
                      {"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},
                      {"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},
                      {"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}
                    ],"isHidden":true,"inputType":"radio"},
                  {"id":"160.3","label":"First","name":""},
                  {"id":"160.4","label":"Middle","name":"","isHidden":true},
                  {"id":"160.6","label":"Last","name":""},
                  {"id":"160.8","label":"Suffix","name":"","isHidden":true}
                ]},
                {"id":161,"label":"Email Maker 1","choices":"","type":"email","inputs":""},
                {"id":99,"label":"Contact Phone Number [ALL]","choices":"","type":"phone","inputs":""},
              ],
              "rmtData":{
                "resource":[
                  {"id":"54","value":"Internet _Sponsors","checked":true,"aggregated":true}
                ],
                "attribute":[],"attention":[],"meta":[],"comment":[]
              },
              "type":"customRpt",
              "location":true};
      var subTitle = 'Sponsor Internet';
      $scope.reports.callAJAX(vars);
    } else if (subRoute === "prod_change") {
      vars = {
        "faire": faire,
        "table": "wp_mf_lead_detail_changes",
        "type": "tableData",
        "dateAfter": "",
        "subRoute":subRoute
      };

      var subTitle = 'Sponsor Internet';
      $scope.reports.callAJAX(vars);
    }
    jQuery('#pageTitle').html(pageTitle);
    jQuery('#subTitle').html(subTitle);
  } //close check for route params
  $scope.filterExport = function( grid, row, col, input ) {
      return 'unknown';
    };

  $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
    if( col.filters[0].term ){
      return 'header-filtered';
    } else {
      return '';
    }
  };
}])
  .filter('griddropdown', function () {
    return function (input, map) {
      var result;
      var match;
      //convert gridArray to usable hash
      var optionsHash =  {};
      if(map.col){
        var gridArray = map.col.colDef.editDropdownOptionsArray;
        for (var i = 0; i < gridArray.length; i++) {
          optionsHash[gridArray[i].id] = gridArray[i].fkey;
        }
      }

      if (!input){
        return '';
      } else if (result = optionsHash[input]) {
        return result;
      } else if ( ( match = input.match(/(.+)( \(\d+\))/) ) && ( result = optionsHash[match[1]] ) ) {
        return result + match[2];
      } else {
        return input;
      }
    };
  });
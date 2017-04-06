// reports controller
rmgControllers.controller('cannedCtrl', ['$scope', '$routeParams', '$http','$interval','uiGridConstants', function ($scope, $routeParams, $http,$interval,uiGridConstants) {
  $scope.reports    = {};
  $scope.reports.loading   = true;
  $scope.reports.showGrid  = false;
  $scope.reports.showLinks = false;
  $scope.reports.selFaire  = '';
  $scope.data     = [];
  $scope.msg = {};

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
        }
      }).finally(function () {
        if(type=='faires'){
          faires = $scope.data.faires;
          angular.forEach(faires, function(value,key){
            if(value.faire==$scope.subRoute){
              $scope.reports.selFaire = key;
            }
          });
        }
      });
    }
  };



  //set up gridOptions for predefined reports
  $scope.gridOptions = {
    enableFiltering: true,
    enableGridMenu: true,
    rowHeight: 100,
    showColumnFooter: true,
    exporterMenuPdf: false, // hide PDF export
    exporterCsvFilename: $routeParams.sub+'-export.csv',
    exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
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
        return input;
      }
    },
    onRegisterApi: function(gridApi){
      $scope.gridApi = gridApi;
    }
  };

 //get report data
  $scope.reports.callAJAX = function(pvars){
    $scope.reports.showGrid  = true;
    $scope.reports.loading = true;
    var sortParams = {
      'field_303':  {'direction': uiGridConstants.ASC, 'priority':0},
      'area':       {'direction': uiGridConstants.ASC, 'priority':1},
      'subarea':    {'direction': uiGridConstants.ASC, 'priority':2},
      'location':   {'direction': uiGridConstants.ASC, 'priority':3}
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
          }
          if('aggregationType' in value){
            if(value.aggregationType =='uiGridConstants.aggregationTypes.sum'){
              value.aggregationType = uiGridConstants.aggregationTypes.sum;
              value.aggregationHideLabel = true;
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
  }
//default
  var tablename = 'wp_rmt_entry_resources';

  if($routeParams){
    $scope.reports.subRoute = $routeParams.sub;

    if("faire" in $routeParams){
      var faire = $routeParams.faire;
      $scope.reports.selFaire = $routeParams.faire;
      $scope.reports.showLinks = true;
    }else{
      $scope.reports.selFaire = '';
      var faire = '';
    }
    var subRoute  = $routeParams.sub;
    var pageTitle = 'Reports';
    var subTitle  = '';

    if(subRoute=='cm_pymt'){
       vars = {"formSelect":[],
              "formType":["Exhibit"],
              "faire": faire,
              "payments":true,
              "selectedFields":[
                {"id":"442","label":"Fee Management","choices":"all","type":"checkbox"},
                {"id":151,"label":"Exhibit Name","choices":"","type":"text","inputs":""},
                {"id":376,"label":"CM Ind","choices":"Yes","type":"radio"},
                {"id":434,"label":"Fee Ind","choices":"Yes","type":"radio","exact":true},
                {"id":"55", "label":"What are your plans at Maker Faire?", "choices":"all", "type":"checkbox"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true}
              ],
              "location":true,
              "rmtData":{"resource":[],"attribute":[],"attention":[],
              "meta":[{"id":"res_status","type":"meta","value":"Resource Status","checked":true}]},
              "type":"customRpt"
            };
      var subTitle = 'Payment(s)';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=='nonprofit_pymt'){
       vars = {"formSelect":[],
              "formType":["Exhibit"],
              "faire": faire,
              "payments":true,
              "selectedFields":[
                {"id":"304.17","label":"Flags","choices":"NP Fee Full","type":"checkbox"},
                {"id":"304.18","label":"Flags","choices":"NP Fee Discount","type":"checkbox"},
                {"id":"304.19","label":"Flags","choices":"NP Fee Waived","type":"checkbox"},

                {"id":151,"label":"Exhibit Name","choices":"","type":"text","inputs":""},
                {"id":45,"label":"Are you a:","choices":"Non-profit or Cause or Mission based organization (Exhibit Fee applicable)","type":"radio","exact":true},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true}
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
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "useFormSC": true,
              "entryIDorder": 200,
              "locationOrder": 300,
              "formTypeorder":400,
              "selectedFields":[
                {"id":16, "label":"EXHIBIT SUMMARY", "choices":"", "type":"textarea", "inputs":"", "order":1700},
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio"},
                {"id":96, "label":"MAKER NAME", "choices":"", "type":"name",
                 "inputs":[{"id":"96.3","label":"First","name":""},{"id":"96.6","label":"Last","name":""},], "order":700
                },
                {"id":98,"label":"Contact Email","choices":"","type":"email","inputs":"", "order":800},
                {"id":99,"label":"PHONE","choices":"","type":"phone","inputs":"", "order":900},
                {"id":151,"label":"EXHIBIT","choices":"","type":"text","inputs":"", "order":100},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":303,"label":"Status","choices":"Rejected","type":"radio"},
                {"id":303,"label":"Status","choices":"Wait List","type":"radio"},
                {"id":303,"label":"Status","choices":"Cancelled","type":"radio"}
             ],
             "rmtData":{
                "resource":[
                  {"id":"all","value":"ALL RESOURCES","checked":true, "order":1800},
                  {"id":"2","value":"TABLE","checked":true, "order":1100},
                  {"id":"3","value":"CHAIR","checked":true, "order":1200},
                  {"id":"9","value":"ELEC","checked":true, "order":1300}
                ],
                "attribute":[
                  {"id":"2","value":"SIZE","checked":true, "order":1000},
                  {"id":"4","value":"IN/OUT","checked":true, "order":1600},
                  {"id":"9","value":"NZ","checked":true, "order":1500},
                  {"id":"11","value":"INT","checked":true, "order":1400}
                ],
                "attention":[
                  {"id":"9","value":"Area Manager Notes","checked":true, "order":1900},
                  {"id":"10","value":"Early Setup","checked":true},
                  {"id":"11","value":"No Friday","checked":true}
                ],
                "meta":[]
              },
             "type":"customRpt",
             "location":true};
      var subTitle = 'AM summary';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=='zoho'){
      vars = {"formSelect":[],
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "useFormSC": true,
              "entryIDorder": 200,
              "locationOrder": 300,
              "formTypeorder":400,
              "selectedFields":[
                {"id":16,"label":"EXHIBIT SUMMARY","choices":"","type":"textarea","inputs":"", "order":1500},
                {"id":376,"label":"CM","choices":"Yes","type":"radio"},
                {"id":83,"label":"FIRE","choices":"Yes","type":"radio", "order":1800},
                {"id":83,"label":"FIRE","choices":"No","type":"radio", "order":1800},
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
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "useFormSC": true,
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
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio"},
              ],
              "rmtData":{
                "resource":[
                  {"id":"2","value":"TABLE","checked":true,"aggregated":false, "order":600,"comments":false},
                  {"id":"3","value":"CHAIR","checked":true,"aggregated":false, "order":700,"comments":false},
                  {"id":"9","value":"ELEC","checked":true, "order":800,"comments":false},
                  {"id":"10","value":"ELEC 220V","checked":true, "order":900,"comments":false}
                ],
                "attribute":[
                  {"id":"2","value":"SIZE","checked":true, "order":500}
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
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "useFormSC": true,
              "selectedFields":[
                {"id":151,"label":"Exhibit","choices":"","type":"text","inputs":""},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true},
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio","hide":true},
              ],
              "rmtData":{
                "resource":[
                  {"id":"2","value":"Tables","checked":true,"aggregated":false},
                  {"id":"3","value":"Chairs","checked":true,"aggregated":false}
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
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "useFormSC": true,
              "selectedFields":[
                {"id":151,"label":"Exhibit","type":"text"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true},
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio","hide":true},

              ],
              "orderBy":'location',
              "rmtData":{
                "resource":[
                  {"id":"19","value":"Barricade","checked":true,"aggregated":false},
                  {"id":"18","value":"Fencing","checked":true,"aggregated":false}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'Barr/Fence';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="electrical"){
      vars = {"formSelect":[],
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "useFormSC": true,
              "selectedFields":[
                {"id":151,"label":"EXHIBIT","choices":"","type":"text","inputs":""},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true},
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio","hide":true},
              ],
              "rmtData":{
                "resource":[
                  {"id":"9","value":"Electrical 120V","checked":true,"aggregated":false},
                  {"id":"10","value":"Electrical 220V","checked":true,"aggregated":false}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
            var subTitle = 'Electrical';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="guest_seat"){
      vars = {"formSelect":[],
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "selectedFields":[
                {"id":151,"label":"Record Name","choices":"","type":"text","inputs":""},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio"},
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
    }else if(subRoute=="wb_stools"){
      vars = {"formSelect":[],
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "selectedFields":[
                {"id":151,"label":"Record Name","choices":"","type":"text","inputs":""},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"},
                {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio"},
              ],
              "rmtData":{
                "resource":[
                  {"id":"41","value":"Work Bench","checked":true,"aggregated":false},
                  {"id":"42","value":"Stools","checked":true,"aggregated":false}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'WB/Stools';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="label"){
      vars = {"formSelect":[],
              "formType":["Exhibit"],
              "faire": faire,
        "selectedFields":[
          {"id":151,"label":"Record Name","choices":"","type":"text","inputs":""},
          {"id":320,"label":"Primary Category","choices":"all","type":"select"},
          {"id":376,"label":"CM Indicator","choices":"Yes","type":"radio"}
        ],
        "rmtData":{
          "resource":[
            {"id":"9","value":"Electrical 120V","checked":true},
            {"id":"10","value":"Electrical 220V","checked":true}],
          "attribute":[
            {"id":"2","value":"Space Size","checked":true},
            {"id":"4","value":"Exposure","checked":true},
            {"id":"6","value":"Light Level","checked":true},
            {"id":"9","value":"Noise Level","checked":true},
            {"id":"11","value":"Internet","checked":true}],
          "attention":[],
          "meta":[]},
        "type":"customRpt"};
      var subTitle = 'Label Placement';
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

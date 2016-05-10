// reports controller
rmgControllers.controller('cannedCtrl', ['$scope', '$routeParams', '$http','$interval','uiGridConstants','uiGridGroupingConstants', function ($scope, $routeParams, $http,$interval,uiGridConstants,uiGridGroupingConstants) {
  $scope.reports    = {};
  $scope.reports.loading   = true;
  $scope.reports.showGrid  = false;

  var url = '/resource-mgmt/reports.ajax.php';
  $scope.msg = {};

  //set up gridOptions for predefined reports
  $scope.gridOptions = {enableFiltering: true,
    enableGridMenu: true,
    rowHeight: 100,

    exporterCsvFilename: 'export.csv',
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

    //get grid data
    $http({
      method: 'post',
      url: url,
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
    var subRoute  = $routeParams.sub;
    var pageTitle = 'Reports';
    var subTitle  = '';

    if(subRoute=='commercial')        {
      vars = {"formSelect":["46", "60", "47", "71"],"selectedFields":[{"id":"55.2","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Promoting a product or service [Commercial Maker]","type":"checkbox","$$hashKey":"uiGrid-002B"},{"id":"55.3","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Launching a product or service","type":"checkbox","$$hashKey":"uiGrid-002D"},{"id":151,"label":"Record Name (Project/Title/Company) [ALL]","choices":"","type":"text","inputs":"","$$hashKey":"uiGrid-00B3"}],"rmtData":{"resource":[{"id":"all","value":"All Resources","$$hashKey":"object:178","checked":true}],"attribute":[{"id":"all","value":"All Attributes","$$hashKey":"object:252","checked":true}],"attention":[{"id":"all","value":"All Attention","$$hashKey":"object:272","checked":true}],"meta":[]},"type":"customRpt","location":true};
      var subTitle = 'Commercial';
      $scope.reports.callAJAX(vars);
    }
    if(subRoute=='am_summary'){
      vars = {"formSelect":["46", "60", "47", "71"],"selectedFields":[{"id":16,"label":"Short/Public Description [check notes] [ALL]","choices":"","type":"textarea","inputs":"","$$hashKey":"uiGrid-011P"},{"id":"55.1","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Selling at Maker Faire [Commercial Maker]","type":"checkbox","$$hashKey":"uiGrid-012Z"},{"id":"55.2","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Promoting a product or service [Commercial Maker]","type":"checkbox","$$hashKey":"uiGrid-0131"},{"id":"55.3","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Launching a product or service","type":"checkbox","$$hashKey":"uiGrid-0133"},{"id":"55.4","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Active Networking (collecting names, signatures, etc)","type":"checkbox","$$hashKey":"uiGrid-0135"},{"id":"55.5","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Launching a crowdfunding campaign","type":"checkbox","$$hashKey":"uiGrid-0137"},{"id":"55.6","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"Soliciting crowdfunding support for a campaign","type":"checkbox","$$hashKey":"uiGrid-0139"},{"id":"55.7","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]","choices":"None of the above","type":"checkbox","$$hashKey":"uiGrid-013B"},{"id":96,"label":"Contact Name [ALL]","choices":"","type":"name","inputs":[{"id":"96.2","label":"Prefix","name":"","choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},{"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},{"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},{"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],"isHidden":true,"inputType":"radio"},{"id":"96.3","label":"First","name":""},{"id":"96.4","label":"Middle","name":"","isHidden":true},{"id":"96.6","label":"Last","name":""},{"id":"96.8","label":"Suffix","name":"","isHidden":true}],"$$hashKey":"uiGrid-017X"},{"id":98,"label":"Contact Email [ALL]","choices":"","type":"email","inputs":"","$$hashKey":"uiGrid-017Z"},{"id":99,"label":"Contact Phone Number [ALL]","choices":"","type":"phone","inputs":"","$$hashKey":"uiGrid-0181"},{"id":151,"label":"Record Name (Project/Title/Company) [ALL]","choices":"","type":"text","inputs":"","$$hashKey":"uiGrid-01BT"},{"id":303,"label":"Status","choices":"Accepted","type":"radio","$$hashKey":"uiGrid-01L1"}],"rmtData":{"resource":[{"id":"all","value":"All Resources","$$hashKey":"object:695","checked":true},{"id":"2","value":"Tables","$$hashKey":"object:696","checked":true},{"id":"3","value":"Chairs","$$hashKey":"object:697","checked":true},{"id":"9","value":"Electrical 120V","$$hashKey":"object:698","checked":true}],"attribute":[{"id":"2","value":"Space Size","$$hashKey":"object:770","checked":true},{"id":"4","value":"Exposure","$$hashKey":"object:771","checked":true},{"id":"9","value":"Noise Level","$$hashKey":"object:773","checked":true},{"id":"11","value":"Internet","$$hashKey":"object:774","checked":true}],"attention":[{"id":"9","value":"Area Manager Notes","$$hashKey":"object:792","checked":true},{"id":"10","value":"Early Setup","$$hashKey":"object:793","checked":true},{"id":"11","value":"No Friday","$$hashKey":"object:794","checked":true}],"meta":[]},"type":"customRpt","location":true};
      var subTitle = 'AM summary';
      $scope.reports.callAJAX(vars);
    }
    if(subRoute=='zoho'){
      vars = {"formSelect":["46", "60", "47", "71"],
              "selectedFields":[
                {"id":16,
                 "label":"Short/Public Description [check notes] [ALL]",
                 "choices":"",
                 "type":"textarea",
                 "inputs":"",
                 "$$hashKey":"uiGrid-022D"},
               {"id":"55.1",
                "label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"Selling at Maker Faire [Commercial Maker]",
                "type":"checkbox",
                "$$hashKey":"uiGrid-023N"},
              {"id":"55.2",
                "label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"Promoting a product or service [Commercial Maker]",
                "type":"checkbox","$$hashKey":"uiGrid-023P"},
              {"id":"55.3",
                "label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"Launching a product or service","type":"checkbox","$$hashKey":"uiGrid-023R"},
              {"id":"55.4","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"Active Networking (collecting names, signatures, etc)",
                "type":"checkbox","$$hashKey":"uiGrid-023T"},
              {"id":"55.5","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"Launching a crowdfunding campaign","type":"checkbox","$$hashKey":"uiGrid-023V"},
              {"id":"55.6","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"Soliciting crowdfunding support for a campaign","type":"checkbox","$$hashKey":"uiGrid-023X"},
              {"id":"55.7","label":"What are your plans at Maker Faire? Check all that apply: [MF_E, SP_SU]",
                "choices":"None of the above","type":"checkbox","$$hashKey":"uiGrid-023Z"},
              {"id":83,"label":"Does your exhibit make use of fire (any size flame), chemicals, or other dangerous materials or tools (propane, welders, etc)? [MF_E, SP_SU, MC_E]",
              "choices":"Yes","type":"radio","$$hashKey":"uiGrid-02F0"},
            {"id":85,"label":"Describe any fire or safety issues. [MF_E, SP_SU, MC_E]","choices":"","type":"textarea","inputs":"","$$hashKey":"uiGrid-02F8"},
              {"id":96,"label":"Contact Name [ALL]","choices":"","type":"name",
                "inputs":[
                  {"id":"96.2","label":"Prefix","name":"",
                    "choices":[{"text":"Mr.","value":"Mr.","isSelected":false,"price":""},
                      {"text":"Mrs.","value":"Mrs.","isSelected":false,"price":""},{"text":"Miss","value":"Miss","isSelected":false,"price":""},
                      {"text":"Ms.","value":"Ms.","isSelected":false,"price":""},{"text":"Dr.","value":"Dr.","isSelected":false,"price":""},
                      {"text":"Prof.","value":"Prof.","isSelected":false,"price":""},{"text":"Rev.","value":"Rev.","isSelected":false,"price":""}],
                    "isHidden":true,"inputType":"radio"},{"id":"96.3","label":"First","name":""},
                  {"id":"96.4","label":"Middle","name":"","isHidden":true},
                  {"id":"96.6","label":"Last","name":""},
                  {"id":"96.8","label":"Suffix","name":"","isHidden":true}],"$$hashKey":"uiGrid-028L"},
              {"id":98,"label":"Contact Email [ALL]","choices":"","type":"email","inputs":"","$$hashKey":"uiGrid-028N"},
              {"id":99,"label":"Contact Phone Number [ALL]","choices":"","type":"phone","inputs":"","$$hashKey":"uiGrid-028P"},
              {"id":151,"label":"Record Name (Project/Title/Company) [ALL]","choices":"","type":"text","inputs":"","$$hashKey":"uiGrid-02CH"},
              {"id":293,"label":"Does your exhibit require attendees to wear an activity wristband?","choices":"Yes","type":"radio","$$hashKey":"uiGrid-02JZ"},
              {"id":293,"label":"Does your exhibit require attendees to wear an activity wristband?","choices":"No","type":"radio","$$hashKey":"uiGrid-02K1"},
              {"id":303,"label":"Status","choices":"Accepted","type":"radio","$$hashKey":"uiGrid-02LP"},
              {"id":317,"label":"Will your exhibit produce any waste?","choices":"Yes","type":"radio","$$hashKey":"uiGrid-02OZ"},
              {"id":317,"label":"Will your exhibit produce any waste?","choices":"No","type":"radio","$$hashKey":"uiGrid-02P1"},
              {"id":293,"label":"Does your exhibit require attendees to wear an activity wristband?","choices":"Yes","type":"radio","$$hashKey":"uiGrid-02RE"}],
            "rmtData":{"resource":[{"id":"all","value":"All Resources","$$hashKey":"object:1236","checked":true},
                {"id":"2","value":"Tables","$$hashKey":"object:1237","checked":true},
                {"id":"3","value":"Chairs","$$hashKey":"object:1238","checked":true},
                {"id":"9","value":"Electrical 120V","$$hashKey":"object:1239","checked":true},
                {"id":"15","value":"Water","$$hashKey":"object:1244","checked":true},
                {"id":"18","value":"Fencing","$$hashKey":"object:1247","checked":true},
                {"id":"19","value":"Barricade","$$hashKey":"object:1248","checked":true},
                {"id":"45","value":"Umbrella","$$hashKey":"object:1267","checked":true},
                {"id":"47","value":"Grindings","$$hashKey":"object:1269","checked":true}],
              "attribute":[{"id":"2","value":"Space Size","$$hashKey":"object:1311","checked":true},
                {"id":"4","value":"Exposure","$$hashKey":"object:1312","checked":true},
                {"id":"9","value":"Noise Level","$$hashKey":"object:1314","checked":true},
                {"id":"11","value":"Internet","$$hashKey":"object:1315","checked":true}],
              "attention":[{"id":"9","value":"Area Manager Notes","$$hashKey":"object:1333","checked":true},
                {"id":"10","value":"Early Setup","$$hashKey":"object:1334","checked":true},
                {"id":"11","value":"No Friday","$$hashKey":"object:1335","checked":true},
                ],"meta":[]},
              "type":"customRpt","location":true}

      var subTitle = 'Zoho';
      $scope.reports.callAJAX(vars);
    }
    if(subRoute=="am_tcp"){
      vars = {"formSelect":["46", "60", "47", "71"],"selectedFields":[{"id":151,"label":"Record Name (Project/Title/Company) [ALL]","choices":"","type":"text","inputs":"","$$hashKey":"uiGrid-061A"},{"id":303,"label":"Status","choices":"Accepted","type":"radio","$$hashKey":"uiGrid-06AI"}],"rmtData":{"resource":[{"id":"2","value":"Tables","$$hashKey":"object:4415","checked":true},{"id":"3","value":"Chairs","$$hashKey":"object:4416","checked":true},{"id":"9","value":"Electrical 120V","$$hashKey":"object:4417","checked":true},{"id":"10","value":"Electrical 220V","$$hashKey":"object:4418","checked":true}],"attribute":[{"id":"2","value":"Space Size","$$hashKey":"object:4489","checked":true}],"attention":[],"meta":[]},"type":"customRpt","location":true}
      var subTitle = 'AM TCP';
      $scope.reports.callAJAX(vars);
    }
    jQuery('#pageTitle').html(pageTitle);
    jQuery('#subTitle').html(subTitle);
  } //close check for route params
  $scope.filterExport = function( grid, row, col, input ) {
      return 'unknown';
    };

  var url = '/resource-mgmt/reports.ajax.php';
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

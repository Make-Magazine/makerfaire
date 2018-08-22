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
    showGridFooter: true,
    showColumnFooter: true,
    rowHeight: 100,
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
  };

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
    if(subRoute=='sponsor_pymt'){
       vars = {"formSelect":[],
              "formType":["Sponsor"],
              "faire": faire,
              "payments":true,
              "paymentOrder": 100,
              "entryIDorder": 200,
              "locationOrder": 400,
              "selectedFields":[
                {"id":151,"label":"Exhibit Name","choices":"","type":"text","inputs":"", "order":300},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true, "order":800,"hide":true}
              ],
              "location":true,
              "rmtData":{"resource":[],"attribute":[],"attention":[],
              "meta":[{"id":"res_status","type":"meta","value":"Resource Status","checked":true}]},
              "type":"customRpt"
            };
      var subTitle = 'Payment(s)';
      $scope.reports.callAJAX(vars);
    }else
    if(subRoute=='cm_pymt'){
       vars = {"formSelect":[],
              "formType":["Exhibit"],
              "faire": faire,
              "payments":true,
              "paymentOrder": 100,
              "entryIDorder": 200,
              "locationOrder": 900,
              "CMOrder": 500,
              "selectedFields":[
                {"id":442,"label":"Fee Management","choices":"all","type":"checkbox", "order":400},
                {"id":151,"label":"Exhibit Name","choices":"","type":"text","inputs":"", "order":300},
                {"id":434,"label":"Fee Ind","choices":"all","type":"radio","order":600},
                {"id":55, "label":"What are your plans at Maker Faire?", "choices":"all", "type":"checkbox", "order":700},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true, "order":800,"hide":true}
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
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "useFormSC": true,
              "entryIDorder": 200,
              "locationOrder": 300,
              "formTypeorder":400,
              "selectedFields":[
                {"id":16, "label":"EXHIBIT SUMMARY", "choices":"", "type":"textarea", "inputs":"", "order":1700},
                {"id":96, "label":"MAKER NAME", "choices":"", "type":"name",
                 "inputs":[{"id":"96.3","label":"First","name":""},{"id":"96.6","label":"Last","name":""}], 
                 "order":700
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
                  {"id":"2",  "value":"SIZE","checked":true, "order":1000},
                  {"id":"4",  "value":"IN/OUT","checked":true, "order":1600},
                  {"id":"9",  "value":"NZ","checked":true, "order":1500},
                  {"id":"11", "value":"INT","checked":true, "order":1400}
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
                {"id":303,"label":"Status","choices":"Cancelled","type":"radio"}
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
              "formType":["Exhibit","Performance","Startup Sponsor","Sponsor","Show Management"],
              "faire": faire,
              "dispFormID":false,
              "useFormSC": true,
              "entryIDorder": 200,
              "locationOrder": 300,
              "formTypeorder":400,
              "CMOrder": 700,
              "selectedFields":[
                {"id":151,"label":"Exhibit","choices":"","type":"text","inputs":"", "order":250},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true, "order":600},
              ],
              "rmtData":{
                "resource":[
                  {"id":"2","value":"Tables","checked":true,"aggregated":false,"order":2000},
                  {"id":"3","value":"Chairs","checked":true,"aggregated":false,"order":3000}
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
                {"id":151,"label":"Exhibit","type":"text", "order":25},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true}
              ],
              "orderBy":'location',
              "rmtData":{
                "resource":[
                  {"id":"19","value":"Barricade","checked":true,"aggregated":true},
                  {"id":"18","value":"Fencing","checked":true,"aggregated":true}
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
                {"id":151,"label":"EXHIBIT","choices":"","type":"text","inputs":"", "order":25},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio","exact":true,"hide":true}
              ],
              "rmtData":{
                "resource":[
                  {"id":"9","value":"Electrical 120V","checked":true,"aggregated":true},
                  {"id":"10","value":"Electrical 220V","checked":true,"aggregated":true}
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
                {"id":151,"label":"Record Name","choices":"","type":"text","inputs":"", "order":25},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"}
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
                {"id":151,"label":"Record Name","choices":"","type":"text","inputs":"", "order":25},
                {"id":303,"label":"Status","choices":"Proposed","type":"radio"},
                {"id":303,"label":"Status","choices":"Accepted","type":"radio"}
              ],
              "rmtData":{
                "resource":[
                  {"id":"41","value":"Work Bench","checked":true,"aggregated":true},
                  {"id":"42","value":"Stools","checked":true,"aggregated":true}
                ],
                "attribute":[],"attention":[],"meta":[]},
              "type":"customRpt",
              "location":true};
      var subTitle = 'WB/Stools';
      $scope.reports.callAJAX(vars);
    }else if(subRoute=="label"){
      vars = {"formSelect":[],"formType":["Exhibit","Startup Sponsor","Sponsor","Show Management"],
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

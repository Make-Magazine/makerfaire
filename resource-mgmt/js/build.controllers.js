// reports controller
rmgControllers.controller('buildCtrl', ['$scope', '$routeParams', '$http','$interval','uiGridConstants','uiGridGroupingConstants', function ($scope, $routeParams, $http,$interval,uiGridConstants,uiGridGroupingConstants) {
  $scope.reports    = {};
  $scope.reports.loading   = true;
  $scope.reports.showGrid  = false;
  $scope.reports.showbuild = false;
  $scope.reports.selectedFields = {};

  //set location based on url parameters
  if("location" in $routeParams){
    if($routeParams.location==='true'){
      $scope.reports.location = true;
    }
  }

  $scope.msg = {};

  //begin create your own report - show selected forms
  $scope.reports.formName = function(rptID){
    var reportName = rptID;
    angular.forEach($scope.reports.forms, function(value, key) {
      if(value.id==rptID) reportName = value.name;
    });
    return reportName;
  };

  //set up gridOptions for build your own report - field selection
  $scope.fieldSelect = {
    enableRowSelection: true,
    enableSelectAll: false,
    selectionRowHeaderWidth: 35,
    rowHeight: 35,
    showGridFooter:true,
    enableFiltering: true,
    onRegisterApi: function(gridApi){
      $scope.fieldSelect.gridApi = gridApi;

      gridApi.selection.on.rowSelectionChanged($scope,function(rows){
        $scope.reports.selectedFields = gridApi.selection.getSelectedRows();
      });
    }
  };

  $scope.fieldSelect.columnDefs = [
    { name: 'id',width:'50' },
    { name: 'label'},
    { name: 'choices'}
  ];

  $scope.fieldSelect.multiSelect = true;
  $scope.selectAll = function() {
    $scope.fieldSelect.gridApi.selection.selectAllRows();
  };

  $scope.clearAll = function() {
    $scope.fieldSelect.gridApi.selection.clearSelectedRows();
  };

  $scope.generateReport = function() {
    var formSelect     = $scope.reports.formSelect;
    var selectedFields = $scope.reports.selectedFields;
    var rmtData            = {};
    var aggregated = false;
    if($scope.reports.rmt.comment==true){
      var aggregated = true;
    }

    angular.forEach($scope.reports.rmt, function(type, key) {
      build = [];
      angular.forEach(type,function(field){
        if(field.checked){
          field.aggregated = aggregated;
          build.push(field);
        }
      })
      rmtData[key] = build;
    });

    var vars = { 'formSelect' : formSelect , 'selectedFields' : selectedFields, 'rmtData' : rmtData, 'type' : 'customRpt', 'location' : $scope.reports.location, 'tickets' : $scope.reports.tickets};
    $scope.reports.callAJAX(vars);
  };
  /*end build your own report */

  //set report column grouping
  $scope.reports.changeGrouping = function(groupBy) {
    $scope.gridApi.grouping.clearGrouping();
    if(groupBy=='item'){
      $scope.gridApi.grouping.groupColumn('item');
      $scope.gridApi.grouping.groupColumn('resource_id');
    }else if(groupBy=='faire'){
      $scope.gridApi.grouping.groupColumn('faire');
      $scope.gridApi.grouping.groupColumn('area');
      $scope.gridApi.grouping.groupColumn('subarea');
      $scope.gridApi.grouping.groupColumn('location');
    }
    $scope.gridApi.grouping.aggregateColumn('qty', uiGridGroupingConstants.aggregation.SUM);
  };

  //get report data
  $scope.reports.callAJAX = function(pvars){
    $scope.reports.loading = true;
    //console.log(pvars);

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

  //set up gridOptions for predefined reports
  $scope.gridOptions = {enableFiltering: true,
    enableSorting: true,
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

  //default
  $scope.reports.showbuild = true;
  jQuery('#pageTitle').html('Reports');
  jQuery('#subTitle').html('Build Your Own Report');
  $scope.reports.tableName = 'formData';
  var type      = 'tableData';

  $scope.filterExport = function( grid, row, col, input ) {
    return 'unknown';
  };

  var url = '/resource-mgmt/ajax/reports.ajax.php';
  $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
    if( col.filters[0].term ){
      return 'header-filtered';
    } else {
      return '';
    }
  };


  var selTerm = '';
  //get grid data
  $http({
    method: 'post',
    url: url,
    data: JSON.stringify({ 'table' : $scope.reports.tableName , 'type' : type,'viewOnly':true }),
    headers: {'Content-Type': 'application/json'}
  })
  .then(function(response){
      angular.forEach(response.data.columnDefs, function(value, key) {
        if(value.name=='faire' && $scope.reports.selFaire!=''){
          angular.forEach(value.filter.selectOptions, function(selValue, selKey) {
            if(selValue.label==$scope.reports.selFaire){
              selTerm= selValue.value;
            }
          });
          if(selTerm!=''){
            response.data.columnDefs[key].filter.term=selTerm;
          }
        }

        if(("filter" in value)){
          value.filter.type = uiGridConstants.filter.SELECT;
        }
        if(("sort" in value)){
          value.sort.direction = uiGridConstants.ASC;
        }
      });

      $scope.gridOptions.columnDefs = response.data.columnDefs;

      $scope.gridOptions.data       = response.data.data;

      //set up build your own data
      if(("forms" in response.data)){
        if(("formSelect" in $routeParams)){
          $scope.reports.formSelect = $routeParams.formSelect.split(',');
        }else{
          $scope.reports.formSelect = [];
        }
        $scope.reports.forms = response.data.forms;
      }
      if(("fields" in response.data)){
        $scope.fieldSelect.data = response.data.fields;
        var selfields = [];
        //loop thru passed parameter fields
        if("fields" in $routeParams){
          selfields = $routeParams.fields.split(',');
        }
        //always check field 303 and 151
        selfields.push('303','151');

        if($scope.fieldSelect.gridApi.selection.selectRow){
          //var selfields = $routeParams.fields.split(',');
          angular.forEach($scope.fieldSelect.data, function(value, key) {
            if(selfields.indexOf(String(value.id)) != -1){
              // $interval whilst we wait for the grid to digest the data we just gave it
              $interval( function() {$scope.fieldSelect.gridApi.selection.selectRow($scope.fieldSelect.data[key]);}, 0, 1);
            }
          });

        }
      }
      if(("rmt" in response.data)){
        $scope.reports.rmt  = response.data.rmt;
        //set resources based on url parameters
        if("resource" in $routeParams){
          var selResources = $routeParams.resource.split(',');
          angular.forEach($scope.reports.rmt.resource, function(value, key) {
            if(selResources.indexOf(String(value.value)) != -1){
              $scope.reports.rmt.resource[key].checked=true;

            }
          });
        }
        //set attributes based on url parameters
        if("attribute" in $routeParams){
          var selAttributes = $routeParams.attribute.split(',');
          angular.forEach($scope.reports.rmt.attribute, function(value, key) {
            if(selAttributes.indexOf(String(value.value)) != -1){
              $scope.reports.rmt.attribute[key].checked=true;
            }
          });
        }
        //set attention based on url parameters
        if("attention" in $routeParams){
          var selAttentions = $routeParams.attention.split(',');
          angular.forEach($scope.reports.rmt.attention, function(value, key) {
            if(selAttentions.indexOf(String(value.value)) != -1){
              $scope.reports.rmt.attention[key].checked=true;
            }
          });
        }
        //set attention based on url parameters
        if("other" in $routeParams){
          var selMeta = $routeParams.other.split(',');
          angular.forEach($scope.reports.rmt.meta, function(value, key) {
            if(selMeta.indexOf(String(value.value)) != -1){
              $scope.reports.rmt.meta[key].checked=true;
            }
          });
        }
      }
    })
    .finally(function () {
      $scope.reports.loading = false;
      $scope.reports.showGrid = true;
    });
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

// reports controller
rmgControllers.controller('reportsCtrl', ['$scope', '$routeParams', '$http','uiGridConstants', function ($scope, $routeParams, $http,uiGridConstants) {
  $scope.reports    = {};
  $scope.reports.loading   = true;
  $scope.reports.showGrid  = false;
  $scope.reports.showbuild = false;
  $scope.reports.selectedFields = {};

  $scope.msg = {};
  //create your own report - show selected forms
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
    enableSelectAll: true,
    selectionRowHeaderWidth: 35,
    rowHeight: 35,
    showGridFooter:true,
    enableFiltering: true,
    onRegisterApi: function(gridApi){
      $scope.gridApi = gridApi;

      gridApi.selection.on.rowSelectionChanged($scope,function(rows){
        $scope.reports.selectedFields = gridApi.selection.getSelectedRows();
      });
    }
  };

  $scope.fieldSelect.columnDefs = [
    { name: 'id',width:'50' },
    { name: 'label'},
    {name: 'choices'}
  ];

  $scope.fieldSelect.multiSelect = true;
  $scope.selectAll = function() {
    $scope.gridApi.selection.selectAllRows();
  };

  $scope.clearAll = function() {
    $scope.gridApi.selection.clearSelectedRows();
  };

  $scope.generateReport = function() {
    var formSelect     = $scope.reports.formSelect;
    var selectedFields = $scope.reports.selectedFields;
    var vars = { 'formSelect' : formSelect , 'selectedFields' : selectedFields, 'type' : 'customRpt'}
    $scope.reports.callAJAX(vars);
  }

  $scope.reports.callAJAX = function(vars){
    $scope.reports.loading = true;
    //get grid data
    $http({
      method: 'post',
      url: url,
      data: jQuery.param(vars),
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
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
    //minRowsToShow:20,enableCellEdit:false,enableGridMenu: true,
    treeRowHeaderAlwaysVisible: false,
    //exporterCsvFilename: 'myFile.csv',
    //exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
    exporterFieldCallback: function( grid, row, col, input ) {

      if(("editDropdownOptionsArray" in col.colDef)){
        console.log(col);
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
  var tablename = 'wp_rmt_entry_resources';

  if($routeParams){
    var subRoute  = $routeParams.sub;
    if(subRoute=='change')        tablename = 'wp_rg_lead_detail_changes';
    if(subRoute=='location')      tablename = 'wp_mf_faire_subarea';
    if(subRoute=='ent2resource')  tablename = 'wp_rg_lead';
    if(subRoute=='build'){
      tablename = 'formData';
      $scope.reports.showbuild = true;
    }
    $scope.reports.tableName = tablename;
  }
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



  //get grid data
  $http({
    method: 'post',
    url: url,
    data: jQuery.param({ 'table' : $scope.reports.tableName , 'type' : 'tableData','viewOnly':true }),
    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
  })
  .then(function(response){
      angular.forEach(response.data.columnDefs, function(value, key) {
        /*if(value.field=='qty'){
          value.filters = [
            { condition: uiGridConstants.filter.GREATER_THAN,
              placeholder: '>'
            },
            { condition: uiGridConstants.filter.LESS_THAN,
              placeholder: '<'
            }
          ]
        }*/
        if(("filter" in value)){
          value.filter.type = uiGridConstants.filter.SELECT;
        }
      });
      $scope.gridOptions.columnDefs = response.data.columnDefs;
      $scope.gridOptions.data       = response.data.data;

      //set up build your own data
      if(("forms" in response.data)){
        $scope.reports.formSelect = [];
        $scope.reports.forms = response.data.forms;
      }
      if(("fields" in response.data)){
        $scope.fieldSelect.data = response.data.fields;
      }
      if(("field_filters" in response.data)){
        jQuery('#entry_filters').gfFilterUI(response.data.field_filters, response.data.init_field_filters, false);
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

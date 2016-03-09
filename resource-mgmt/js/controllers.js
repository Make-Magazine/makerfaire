var rmgControllers = angular.module('rmgControllers', []);

rmgControllers.controller('VendorsCtrl', ['$scope', '$routeParams', '$http', '$q', '$interval','uiGridConstants', function ($scope, $routeParams, $http, $q, $interval,uiGridConstants) {
  var routeArray = {
    'vendors'  : {'list':'wp_rmt_vendors', 'resources': 'wp_rmt_vendor_resources'},
    'resources': {'list':'wp_rmt_resources', 'items': 'wp_rmt_resource_categories'},
    'faire'    : {'data':'wp_mf_faire', 'orders':'wp_rmt_vendor_orders','areas':'wp_mf_faire_area','subareas':'wp_mf_faire_subarea','global-faire':'wp_mf_global_faire'},
    'entry'    : {'resources': 'wp_rmt_entry_resources','attention':'wp_rmt_entry_attn','attributes': 'wp_rmt_entry_attributes', 'atttibuteCategories': 'wp_rmt_entry_att_categories', 'workflow':'wp_rmt_entry_workflow'}
  };

  $scope.resource    = {};
  $scope.resource.loading = true;
  var mainRoute = ''; var subRoute = '';
  if($routeParams){
    mainRoute = $routeParams.main;
    subRoute  = $routeParams.sub;
    $scope.dispTablename=routeArray[mainRoute][subRoute];
  }
  $scope.filterExport = function( grid, row, col, input ) {
    return 'unknown';
  };
  var url = '/resource-mgmt/ajax.php';
  $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
    if( col.filters[0].term ){
      return 'header-filtered';
    } else {
      return '';
    }
  };

  $scope.msg = {};
  $scope.gridOptions = {enableCellEditOnFocus: true,
    enableFiltering: true,minRowsToShow:20,rowEditWaitInterval: 1,
    enableGridMenu: true,
    exporterCsvFilename: mainRoute+'_'+subRoute+'_export.csv',
    exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
    exporterFieldCallback: function( grid, row, col, input ) {

      if(("editDropdownOptionsArray" in col.colDef)){

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
    }};

  //add a new row to the tale
  $scope.addNew = function() {
    if($scope.gridOptions.data){
      $scope.gridOptions.data.unshift({});
    }else{
      $scope.gridOptions.data = [];
      $scope.gridOptions.data.push({});
    }
  };
  $scope.save = function() {
    $scope.gridApi.rowEdit.flushDirtyRows( $scope.gridApi.grid );
  };

  //get grid data
  $http({
    method: 'post',
    url: url,
    data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'tableData' }),
    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	})
  .then(function(response){
    angular.forEach(response.data.columnDefs, function(value, key) {
        if(("filter" in value)){
          value.filter.type = uiGridConstants.filter.SELECT;
        }
      });
    $scope.gridOptions.columnDefs = response.data.columnDefs;
    $scope.gridOptions.data       = response.data.data;
    $scope.resource.pInfo         = response.data.pInfo;
  })
  .finally(function () { $scope.resource.loading = false; });

  $scope.saveRow = function( rowEntity ) {
    var data = {'table'     : $scope.dispTablename ,
                'type'      : 'updateData',
                'data'      : rowEntity,
                'pKeyField' : $scope.resource.pInfo};

    var promise = $q.defer();
    $http({
        method: 'post',
        url: url,
        data: jQuery.param(data),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function successCallback(response) {
        var pkey = $scope.resource.pInfo;
        var index = $scope.gridOptions.data.indexOf(rowEntity);
        if(!rowEntity.pkey) $scope.gridOptions.data[index][pkey] = response.data.id;
        promise.resolve();
      }, function errorCallback(response) {
        promise.reject();
    });
    $scope.gridApi.rowEdit.setSavePromise( rowEntity, promise.promise );
  }

  $scope.deleteRow = function(row) {
    var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
    if (r == true) {
      $http({
        method: 'post',
        url: url,
        data: jQuery.param({ 'id':row.entity.ID,'type' : 'deleteData','table' : $scope.dispTablename,'pKeyField':$scope.resource.pInfo }),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      })
      .then(function(response){
        if(response.data.success){
          var index = $scope.gridOptions.data.indexOf(row.entity);
          $scope.gridOptions.data.splice(index, 1);
        }
      }, function errorCallback(response) {
        //
      });
    }

  };

 $scope.gridOptions.onRegisterApi = function(gridApi){
    //set gridApi on scope
    $scope.gridApi = gridApi;
    gridApi.rowEdit.on.saveRow($scope, $scope.saveRow);
  };
  $scope.getArray = function(obj){
    var arr = Object.keys(obj).map(function (key) {return obj[key]});
    return arr;
  }
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

var rmgControllers = angular.module('rmgControllers', []);
var routeArray = {'vendors': {'list':'wp_rmt_vendors', 'resources': 'wp_rmt_vendor_resources'},
  'resources': {'list':'wp_rmt_resources', 'categories': 'wp_rmt_resource_categories','attributes':'wp_rmt_resource_att'},
  'faire':{'data':'wp_mf_faire', 'orders':'wp_rmt_vendor_orders','areas':'wp_mf_faire_area','subareas':'wp_mf_faire_subarea'}
};

rmgControllers.controller('VendorsCtrl', ['$scope', '$routeParams', '$http', '$q', '$interval', function ($scope, $routeParams, $http, $q, $interval) {
  $scope.resource    = {};
  $scope.resource.loading = true;
  if($routeParams){
    var mainRoute = $routeParams.main;
    var subRoute  = $routeParams.sub;
    $scope.dispTablename=routeArray[mainRoute][subRoute];
  }

  var url = '/resource-mgmt/ajax.php';
  $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
    if( col.filters[0].term ){
      return 'header-filtered';
    } else {
      return '';
    }
  };

  $scope.msg = {};
  $scope.gridOptions = {enableCellEditOnFocus: true,enableFiltering: true,minRowsToShow:20,rowEditWaitInterval: 1};


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
    $scope.gridOptions.columnDefs = response.data.columnDefs;

    $scope.gridOptions.data       = response.data.data;
    $scope.resource.pInfo         = response.data.pInfo;
  })
  .finally(function () { $scope.resource.loading = false; })
  ;

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
      } else {
        return optionsHash[input];
      }
    };
  });

rmgControllers.controller('resourcesCtrl', ['$scope', '$routeParams',
  function($scope, $routeParams) {
    //$scope.phoneId = $routeParams.phoneId;
  }
]);

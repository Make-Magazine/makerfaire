var rmgControllers = angular.module('rmgControllers', []);

rmgControllers.controller('VendorsCtrl', ['$scope', '$routeParams', '$http', '$q', '$interval', function ($scope, $routeParams, $http, $q, $interval) {
  var routeArray = {
    'vendors'  : {'list':'wp_rmt_vendors', 'resources': 'wp_rmt_vendor_resources'},
    'resources': {'list':'wp_rmt_resources', 'items': 'wp_rmt_resource_categories'},
    'faire'    : {'data':'wp_mf_faire', 'orders':'wp_rmt_vendor_orders','areas':'wp_mf_faire_area','subareas':'wp_mf_faire_subarea'},
    'entry'    : {'attributes': 'wp_rmt_entry_att', 'atttibuteCategories': 'wp_rmt_entry_att_categories', 'workflow':'wp_rmt_entry_workflow'}
  };

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

rmgControllers.controller('faireCtrl', ['$scope', '$routeParams',
  function($scope, $routeParams) {
    $scope.faire.loading = true;
    $scope.faire.init();

    $scope.faire.init = function(){
      $http({
        method: 'post',
        url: url,
        data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'tableData' }),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      })
      .then(function(response){
        $scope.faire.data       = response.data.data;
      }).finally(function () { $scope.faire.loading = false; });
    };
  }
]);

rmgControllers.controller('entryCtrl',  ['$scope', '$routeParams', '$http', '$q', '$interval', function ($scope, $routeParams, $http, $q, $interval) {
  $scope.resources  = {enableCellEditOnFocus: true,enableFiltering: true,rowEditWaitInterval: 1};
  $scope.attributes  = {enableCellEditOnFocus: true,enableFiltering: true,rowEditWaitInterval: 1};

  var url = '/resource-mgmt/ajax.php';

  $scope.resources.addNew = function(){
    if($scope.resources.data){
      $scope.resources.data.unshift({});
    }else{
      $scope.resources.data = [];
      $scope.resources.data.push({});
    }
    //TBD  this feature does not like to play nice wiht saverow
    //focus on the first cell of the newly added row to open it for edit
    /*setTimeout(function(){
		  $scope.resources.gridApi.cellNav.scrollToFocus($scope.resources.data[0], $scope.resources.columnDefs[0]);
		}, 100)*/
  }

  $scope.resources.saveRow = function( rowEntity ) {
    var addData = {entry_id:'54781',
                   resource_id: rowEntity.resource_id,
                   qty : rowEntity.qty,
                   comment: rowEntity.comment,
                   ID: rowEntity.ID
                 };

    var data = {'table'     : 'wp_rmt_entry_resources' ,
                'type'      : 'updateData',
                'data'      : addData,
                'pKeyField' : 'ID'};

    var promise = $q.defer();
    $http({
        method: 'post',
        url: url,
        data: jQuery.param(data),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function successCallback(response) {
        var index = $scope.resources.data.indexOf(rowEntity);
        if(!rowEntity.ID) $scope.resources.data[index]['ID'] = response.data.id;
        promise.resolve();
      }, function errorCallback(response) {
        promise.reject();
    });
    $scope.gridApi.rowEdit.setSavePromise( rowEntity, promise.promise );
  }

  $scope.resources.remove = function(row){
    var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
    if (r == true) {
      $http({
        method: 'post',
        url: url,
        data: jQuery.param({ 'id':row.entity.ID,'type' : 'deleteData','table' : 'wp_rmt_entry_resources','pKeyField':'ID' }),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      })
      .then(function(response){
        if(response.data.success){
          var index = $scope.resources.data.indexOf(row.entity);
          $scope.resources.data.splice(index, 1);
        }
      }, function errorCallback(response) {
        //
      });
    }
  }

    $http({
      method: 'post',
      url: url,
      data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'entryData' }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
    .then(function(response){
      //attributes
      $scope.attributes.data       = response.data.attribute.gridData;
      $scope.attributes.columnDefs = response.data.attribute.columnDefs;

      //resources
      $scope.resources.data       = response.data.resource.gridData;
      $scope.resources.columnDefs = response.data.resource.columnDefs;
      $scope.resources.typeSelect = response.data.resource.typeSelect;

      angular.forEach($scope.resources.columnDefs, function(value, key) {
        if(value.field=='resource_id'){ //set the drop down menu for resource id to change based on the resource category id
          $scope.resources.columnDefs[1].editDropdownOptionsFunction =  function(rowEntity, colDef) {
            return $scope.resources.typeSelect[rowEntity.resource_category_id];
          }
        }
        if(value.field=='comment'){
          $scope.resources.columnDefs[key].editableCellTemplate = '<textarea  rows="5"     ui-grid-editor style="width:100%" ng-class="\'colt\' + col.index" ng-model="MODEL_COL_FIELD"></textarea>';
        }
      });
    });
    $scope.resources.onRegisterApi = function(gridApi){
      //set gridApi on scope
      $scope.gridApi = gridApi;
      gridApi.rowEdit.on.saveRow($scope, $scope.resources.saveRow);
    };
    $scope.save = function() {
      $scope.resources.rowEdit.flushDirtyRows( $scope.resources.grid );
    };
  }]) .filter('griddropdown', function () {
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


rmgControllers.controller('entryAttCtrl',  ['$scope', '$routeParams', '$http', '$q', '$interval', function ($scope, $routeParams, $http, $q, $interval) {
  $scope.attributes = {enableCellEditOnFocus: true,enableFiltering: true,rowEditWaitInterval: 1};

  var url = '/resource-mgmt/ajax.php';

  $scope.attributes.addNew = function(){
    if($scope.attributes.data){
      $scope.attributes.data.unshift({});
    }else{
      $scope.attributes.data = [];
      $scope.attributes.data.push({});
    }
    //TBD  this feature does not like to play nice wiht saverow
    //focus on the first cell of the newly added row to open it for edit
    /*setTimeout(function(){
		  $scope.attributes.gridApi.cellNav.scrollToFocus($scope.attributes.data[0], $scope.attributes.columnDefs[0]);
		}, 100)*/
  }

  $scope.attributes.saveRow = function( rowEntity ) {
    var addData = {entry_id:'54781',
                   resource_id: rowEntity.resource_id,
                   qty : rowEntity.qty,
                   comment: rowEntity.comment,
                   ID: rowEntity.ID
                 };

    var data = {'table'     : 'wp_rmt_entry_resources' ,
                'type'      : 'updateData',
                'data'      : addData,
                'pKeyField' : 'ID'};

    var promise = $q.defer();
    $http({
        method: 'post',
        url: url,
        data: jQuery.param(data),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function successCallback(response) {
        var index = $scope.attributes.data.indexOf(rowEntity);
        if(!rowEntity.ID) $scope.attributes.data[index]['ID'] = response.data.id;
        promise.resolve();
      }, function errorCallback(response) {
        promise.reject();
    });
    $scope.gridApi.rowEdit.setSavePromise( rowEntity, promise.promise );
  }

  $scope.attributes.remove = function(row){
    var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
    if (r == true) {
      $http({
        method: 'post',
        url: url,
        data: jQuery.param({ 'id':row.entity.ID,'type' : 'deleteData','table' : 'wp_rmt_entry_resources','pKeyField':'ID' }),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      })
      .then(function(response){
        if(response.data.success){
          var index = $scope.attributes.data.indexOf(row.entity);
          $scope.attributes.data.splice(index, 1);
        }
      }, function errorCallback(response) {
        //
      });
    }
  }

    $http({
      method: 'post',
      url: url,
      data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'entryData' }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
    .then(function(response){
      //attributes
      $scope.attributes.data       = response.data.attribute.gridData;
      $scope.attributes.columnDefs = response.data.attribute.columnDefs;

    });
    $scope.attributes.onRegisterApi = function(gridApi){
      //set gridApi on scope
      $scope.gridApi = gridApi;
      gridApi.rowEdit.on.saveRow($scope, $scope.attributes.saveRow);
    };
    $scope.save = function() {
      $scope.attributes.rowEdit.flushDirtyRows( $scope.attributes.grid );
    };
  }]) .filter('griddropdown', function () {
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
var rmgControllers = angular.module('rmgControllers', []);

rmgControllers.controller('VendorsCtrl', ['$scope', '$routeParams', '$http', '$q', '$interval','uiGridConstants', function ($scope, $routeParams, $http, $q, $interval,uiGridConstants) {
  var routeArray = {
    'vendors': {
      'list':      { table: 'wp_rmt_vendors', pageTitle : 'Vendors', subTitle  : 'Vendor List'},
      'resources': { table: 'wp_rmt_vendor_resources', pageTitle : 'Vendors', subTitle  : 'Vendor Resources'},
      },
    'resources': {
      'list':     { table: 'wp_rmt_resources',pageTitle:'Manage RMT Data',subTitle:'Resource Type'},
      'items':    { table: 'wp_rmt_resource_categories',pageTitle:'Manage RMT Data',subTitle : 'Resource Items'},
    },
    'faire': {
      'data'                : {table : 'wp_mf_faire',pageTitle:'Faire Data',subTitle:'Faire Data'},
      'orders'              : {table : 'wp_rmt_vendor_orders',pageTitle:'',subTitle:''},
      'areas'               : {table : 'wp_mf_faire_area',pageTitle:'Faire Data',subTitle:'Faire Areas'},
      'subareas'            : {table : 'wp_mf_faire_subarea',pageTitle:'Faire Data',subTitle:'Faire SubAreas'},
      'global-faire'        : {table : 'wp_mf_global_faire',pageTitle:'Faire Data',subTitle:'Global Faire DAta'}
    },
    'entry' : {
      'resources'           : {table : 'wp_rmt_entry_resources', pageTitle:'Entry Specific Data',subTitle:'Assigned Resources'},
      'attention'           : {table : 'wp_rmt_entry_attn', pageTitle:'Entry Specific Data',subTitle:'Assigned Attention'},
      'attributes'          : {table : 'wp_rmt_entry_attributes',pageTitle:'Entry Specific Data',subTitle:'Assigned Attributes'},
      'atttibuteCategories' : {table : 'wp_rmt_entry_att_categories',pageTitle:'Manage RMT Data',subTitle:'Attributes'},
      'workflow'            : {table :'wp_rmt_attn',pageTitle:'Manage RMT Data',subTitle:'Workflow/Attention'},
    }
  };

  $scope.data     = [];
  $scope.resource = {};
  $scope.resource.selFaire = '';
  $scope.resource.loading = true;
  var mainRoute = ''; var subRoute = '';
  if($routeParams){
    mainRoute = $routeParams.main;
    subRoute  = $routeParams.sub;
    $scope.resource.subRoute = subRoute;
    pageTitle = routeArray[mainRoute][subRoute]['pageTitle'];
    subTitle  = routeArray[mainRoute][subRoute]['subTitle'];
    jQuery('#pageTitle').html(pageTitle);
    jQuery('#subTitle').html(subTitle);
    jQuery('#pageTitle').html(pageTitle);
    $scope.dispTablename=routeArray[mainRoute][subRoute]['table'];
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

  //faire dropdown
  $scope.retrieveData = function(type) {
    var vars = { 'type' :  type};
    if(type == 'subareas') {
      vars = { 'table' : 'wp_mf_faire_subarea' , 'type' : 'tableData', 'selfaire':$scope.resource.selFaire};
      $scope.resource.loading = true;
    }
    //get grid data
    $http({
      method: 'post',
      url: url,
      data: jQuery.param(vars),
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
    .then(function(response){
      if("error" in response.data) {
        alert(response.data.error);
      }else if(type=='faires'){
        $scope.data[type] = response.data[type];
      }else if(type=='subareas'){
        angular.forEach(response.data.columnDefs, function(value, key) {
          if(("filter" in value)){
            value.filter.type = uiGridConstants.filter.SELECT;
          }
        });
        $scope.gridOptions.columnDefs = response.data.columnDefs;
        $scope.gridOptions.data       = response.data.data;
        $scope.resource.pInfo         = response.data.pInfo;
      }
    }).finally(function () {
      if(type=='faires'){
        faires = $scope.data.faires;
        angular.forEach(faires, function(value,key){
          if(value.faire==$scope.subRoute){
            $scope.resource.selFaire = key;
          }
        });
      }
      $scope.resource.loading = false;
    });
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

// reports controller
rmgControllers.controller('reportsCtrl', ['$scope', '$routeParams', '$http','uiGridConstants', function ($scope, $routeParams, $http,uiGridConstants) {
  $scope.reports    = {};
  $scope.reports.loading = true;
  $scope.reports.showGrid = false;
  $scope.msg = {};
  $scope.gridOptions = {enableFiltering: true,minRowsToShow:20,enableCellEdit:false,
    enableGridMenu: true,
    exporterCsvFilename: 'myFile.csv',
    exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
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

  if($routeParams){
    var subRoute  = $routeParams.sub;
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


  $scope.reports.loadData = function(tableName) {
    //get grid data
    $http({
      method: 'post',
      url: url,
      data: jQuery.param({ 'table' : tableName , 'type' : 'tableData','viewOnly':true }),
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
    })
    .finally(function () { $scope.reports.loading = false; $scope.reports.showGrid = true;});
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

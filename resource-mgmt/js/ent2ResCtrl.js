// reports controller
rmgControllers.controller('ent2ResCtrl', ['$scope', '$routeParams', '$http','uiGridConstants', function ($scope, $routeParams, $http,uiGridConstants) {
  $scope.reports    = {};
  $scope.reports.loading   = true;
  $scope.reports.showGrid  = true;
  $scope.reports.showbuild = false;

  $scope.msg = {};


  //set up gridOptions
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
  var tablename = 'wp_rmt_entry_resources';
  var type      = 'ent2resource';
  var pageTitle = 'Reports';
  var subTitle = 'Entry to Resource';

  jQuery('#pageTitle').html(pageTitle);
  jQuery('#subTitle').html(subTitle);

  var url = '/resource-mgmt/reports.ajax.php';

  if("faire" in $routeParams){
    $scope.reports.selFaire = $routeParams.faire;
  }else{
    $scope.reports.selFaire = '';
  }

  //get grid data
  $http({
    method: 'post',
    url: url,
    data: JSON.stringify({ 'table' : $scope.reports.tableName , 'type' : type,'viewOnly':true }),
    headers: {'Content-Type': 'application/json'}
  })
  .then(function(response){
    angular.forEach(response.data.columnDefs, function(value, key) {
      if(value.field=='faire' && $scope.reports.selFaire!=''){
        response.data.columnDefs[key].filter = { term: $scope.reports.selFaire, type: uiGridConstants.filter.INPUT };
      }
      if(("sort" in value)){
        value.sort.direction = uiGridConstants.ASC;
      }
    });
    $scope.gridOptions.columnDefs = response.data.columnDefs;
    $scope.gridOptions.data       = response.data.data;

  }) //end response
  .finally(function () {
    $scope.reports.loading  = false;
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

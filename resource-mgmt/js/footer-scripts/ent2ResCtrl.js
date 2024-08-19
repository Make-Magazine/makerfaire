// reports controller
rmgControllers.controller('ent2ResCtrl', ['$scope', '$routeParams', '$http','uiGridConstants', function ($scope, $routeParams, $http, uiGridConstants) {
  $scope.reports    = {};
  $scope.reports.loading   = true;
  $scope.reports.showGrid  = false;
  $scope.reports.selFaire  = '';

  $scope.msg = {};

  var reportName = 'ent2resource';
  if("type" in $routeParams && $routeParams.type!='all'){
    $scope.reports.type = $routeParams.type;
    reportName = $routeParams.type+'_'+ reportName;
  }

  //set up gridOptions
  $scope.gridOptions = {
    enableFiltering: true,
    enableSorting: true,
    enableGridMenu: true,
    minRowsToShow:22,
    exporterCsvFilename: reportName+'.csv',
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
  var pageTitle = 'Reports';
  var subTitle  = 'Entry to Resource';

  jQuery('#pageTitle').html(pageTitle);
  jQuery('#subTitle').html(subTitle);

  if("faire" in $routeParams){
    $scope.reports.selFaire = $routeParams.faire;
    $scope.reports.subRoute = $routeParams.faire;
  }else if("type" in $routeParams){
    $scope.reports.type = $routeParams.type;
  }else{
    $scope.reports.selFaire = '';
  }

  $scope.reports.route = 'ent2resources';

    //faire dropdown
  $scope.retrieveData = function(type) {
    if(type=='faires'){
      var vars = jQuery.param({ 'type' :  type});
      var url = '/resource-mgmt/ajax/ajax.php';
      var head2pass = {'Content-Type': 'application/x-www-form-urlencoded'};
    }else if(type=='ent2res'){
      $scope.reports.loading = true;
      $scope.reports.showGrid = true;
      var vars = JSON.stringify({ 'table' : 'wp_rmt_entry_resources' , 'type' : 'ent2resource','faire':$scope.reports.selFaire, 'formType':$scope.reports.type });
      var url = '/resource-mgmt/ajax/reports.ajax.php';
      var head2pass =  {'Content-Type': 'application/json'};
    }

    //get grid data
    $http({
      method: 'post',
      url: url,
      data: vars,
      headers: head2pass
    })
    .then(function(response){
      if("error" in response.data) {
        alert(response.data.error);
      }else if(type=='faires'){
        $scope.reports[type] = response.data[type];
      }else if(type=='ent2res'){
        //get grid data
        angular.forEach(response.data.columnDefs, function(value, key) {
          if(("sort" in value)){
            value.sort.direction = uiGridConstants.ASC;
          }
        });

        $scope.gridOptions.columnDefs = response.data.columnDefs;
        $scope.gridOptions.data       = response.data.data;
      }
    }).finally(function () {
      if(type=='faires'){
        faires = $scope.reports.faires;
        angular.forEach(faires, function(value,key){
          if(value.faire==$scope.subRoute){
            $scope.reports.selFaire = key;
          }
        });
      }else if(type=='ent2res'){
        $scope.reports.loading  = false;
      }
    });
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

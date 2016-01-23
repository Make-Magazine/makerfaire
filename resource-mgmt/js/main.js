var resourceApp = angular.module('resourceApp', []);

resourceApp.controller('resourceController', function ($scope, $http) {
  var url = '/resource-mgmt/ajax.php';

  $scope.delData = function(pkey,data){
    var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
    $http({
      method: 'post',
      url: url,
		  data: jQuery.param({ 'id' : pkey , 'type' : 'deleteData','table' : $scope.dispTablename,'pKeyField':$scope.resource.pInfo }),
		  headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function(response){
      console.log(response);
      if(response.data.success){
          //TBD remove row from table
          var index = $scope.resource.tableData.indexOf(data);
          $scope.resource.tableData.splice(index, 1);
        }
        $scope.resource.retMsg = response.data.message;

    },
		function(response) { // optional
      // failed
      alert ('error in deleting data from the database')
    });
  }

  $scope.insertData = function(){
    //jQuery('.btn-save').button('loading');

    //update the DB
    $http({
      method: 'post',
      url: url,
		  data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'insertData', 'data':$scope.resource.newEntry}),
		  headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function(response){
      if(response.data.success){
        //alert('Insert Successfull');
      }
      $scope.resource.retMsg = response.data.message;
    },
		function(response) { // optional
      // failed
      alert ('error in inserting data into the database')
    });

    console.log($scope.resource.tableData);
    //add the new data to the displayed table
    if($scope.resource.tableData){
      $scope.resource.tableData.push($scope.resource.newEntry);
    }else{
      $scope.resource.tableData = $scope.resource.newEntry;
    }
    $scope.resource.newEntry = '';
  }

	$scope.getTableData = function(){
    jQuery("#tableData > tbody").empty();
    $scope.resource.tableData  = '';
    $scope.resource.fieldNames = '';
    $scope.resource.pInfo      = ''; //primary key of db table
		$http({
      method: 'post',
      url: url,
		  data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'tableData' }),
		  headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function(response){
      $scope.resource.tableData  = response.data.tableData;
      $scope.resource.fieldNames = response.data.fieldNames;
      if(!response.data.pInfo){
        alert('no primary key found for '+$scope.dispTablename+'! Please alert dev team');
      }else{
        $scope.resource.pInfo      = response.data.pInfo; //primary key of db table
      }
    },
		function(response) { // optional
      // failed
      alert ('error in retrieving table data')
    });
	}

	$scope.init = function(){
    $scope.resource=[];

    $http({
        url: '/resource-mgmt/ajax.php',
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        data: 'type=getTables'
    })
    .then(function(response) {
      // success
      $scope.resource.tables = response.data.tables;
    },
    function(response) { // optional
      // failed
      alert ('error in retrieving table list')
    });
	}
});
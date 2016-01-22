var resourceApp = angular.module('resourceApp', []);

resourceApp.controller('resourceController', function ($scope, $http) {
	var url = '/resource-mgmt/ajax.php';

  $scope.insertData = function(){

  jQuery('#dataInsert > input').each(function () { /* ... */
    //get id of each input to get field name
    console.log($(this));
  //    alert(this.value);
  })

//jQuery('.btn-save').button('loading');
		//$scope.saveData();
		//$scope.editMode = false;
		//$scope.index = '';
	}

  $scope.insertRow = function(tableName){
    var numCol = $scope.fieldNames.length;

    var insRow = "<tr>";
    insRow = "<td><span data-ng-click='insertData(\""+$scope.dispTablename+"\")'>Add</span></td>";
    for(i=0; i<numCol; i++){
      insRow = insRow + "<td><input id='"+$scope.fieldNames[i] +"' type='text' /></td>";
    }
    insRow = insRow + '</tr>';

    jQuery('#tableData tbody').prepend(insRow);
  }

	$scope.getTableData = function(){
    $scope.tableData     = '';
    $scope.fieldNames    = '';
    jQuery("#tableData > tbody").empty();

		$http({
      method: 'post',
      url: url,
		  data: jQuery.param({ 'table' : $scope.dispTablename , 'type' : 'tableData' }),
		  headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
    .then(function(response){
      $scope.resource.tableData = response.data.tableData;
      $scope.resource.fieldNames    = response.data.fieldNames;
    },
		function(response) { // optional
      // failed
      alert ('error in retrieving table data')
    });
	}

	$scope.init = function(){

    $http({
        url: '/resource-mgmt/ajax.php',
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        data: 'type=getTables'
    })
    .then(function(response) {
      // success
      $scope.resource = response.data;
    },
    function(response) { // optional
      // failed
      alert ('error in retrieving table list')
    });
	}
/*
	$scope.messageFailure = function (msg){
		jQuery('.alert-failure-div > p').html(msg);
		jQuery('.alert-failure-div').show();
		jQuery('.alert-failure-div').delay(5000).slideUp(function(){
			jQuery('.alert-failure-div > p').html('');
		});
	}

	$scope.messageSuccess = function (msg){
		jQuery('.alert-success-div > p').html(msg);
		jQuery('.alert-success-div').show();
		jQuery('.alert-success-div').delay(5000).slideUp(function(){
			jQuery('.alert-success-div > p').html('');
		});
	}


	$scope.getError = function(error, name){
		if(angular.isDefined(error)){
			if(error.required && name == 'name'){
				return "Please enter name";
			}else if(error.email && name == 'email'){
				return "Please enter valid email";
			}else if(error.required && name == 'company_name'){
				return "Please enter company name";
			}else if(error.required && name == 'designation'){
				return "Please enter designation";
			}else if(error.required && name == 'email'){
				return "Please enter email";
			}else if(error.minlength && name == 'name'){
				return "Name must be 3 characters long";
			}else if(error.minlength && name == 'company_name'){
				return "Company name must be 3 characters long";
			}else if(error.minlength && name == 'designation'){
				return "Designation must be 3 characters long";
			}
		}
	}*/

});
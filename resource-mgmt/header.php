<?php
$my_theme = wp_get_theme();
$my_version = $my_theme->get('Version');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MakerFaire Resource Management Tool</title>

	<!-- Bootstrap -->
	<link href="/wp-content/themes/makerfaire/css/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
  	<link href="/wp-content/themes/makerfaire/css/angular-reporting.min.css" rel="stylesheet">

  	<!-- jQuery -->
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  	<script type="text/javascript">
  		<!-- This script was moved into the universal scripts so I had to add it back here as we don't use that library -->
		function getUrlParam(name) {
			if(window.location.search) {
				name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
				var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
				var results = regex.exec(location.search);
				return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
			}
		};
		</script>
	  	<!-- scripts built with grunt -->
	  	<script src="/wp-content/themes/makerfaire/js/built-libs.min.js?ver=<?php echo $my_version;?>"></script>
	  	<script src="/wp-content/themes/makerfaire/js/built-angular-libs.min.js?ver=<?php echo $my_version;?>"></script>
	  	<script src="/wp-content/themes/makerfaire/js/built-angular-reporting.min.js?ver=<?php echo $my_version;?>"></script>
	
	  	<!-- to export grid data -->
		<script src="/resource-mgmt/js/grunt-scripts/csv.js"></script>
		<script src="/resource-mgmt/js/grunt-scripts/pdfmake.js"></script>
		<script src="/resource-mgmt/js/grunt-scripts/vfs_fonts.js"></script>
	<base href="/resource-mgmt/"></base>
</head>
<body>
	<div id="wpadminbar" class="nojq">
        <div class="quicklinks" id="wp-toolbar" role="navigation" aria-label="Toolbar">
            <ul id="wp-admin-bar-root-default" class="ab-top-menu">                
                <li id="wp-admin-bar-site-name" class="menupop">
                    <a class="ab-item" href="../wp-admin" target="_none"><i class="bi bi-house"></i> Maker Faire Admin</a>
                    
                </li>      
            </ul>

        </div>

    </div>
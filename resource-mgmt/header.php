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
  <link href="/wp-content/themes/makerfaire/css/angular-reporting.css" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- scripts built with grunt -->
  <script src="/wp-content/themes/makerfaire/js/built-libs.js?ver=<?php echo $my_version;?>"></script>
  <script src="/wp-content/themes/makerfaire/js/built-angular-libs.js?ver=<?php echo $my_version;?>"></script>
  <script src="/wp-content/themes/makerfaire/js/built-angular-reporting.js?ver=<?php echo $my_version;?>"></script>

  <!-- to export grid data -->
  <script src="http://ui-grid.info/docs/grunt-scripts/csv.js"></script>
  <script src="http://ui-grid.info/docs/grunt-scripts/pdfmake.js"></script>
  <script src="http://ui-grid.info/docs/grunt-scripts/vfs_fonts.js"></script>
  <base href="/resource-mgmt/"></base>
</head>
<body>
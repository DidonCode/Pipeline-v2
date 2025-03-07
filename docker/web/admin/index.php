<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="../images/logos/logoButifyIcon.png">
		
		<link rel="stylesheet" type="text/css" href="../css/Bootstrap/css/bootstrap.css">
		
		<link rel="stylesheet" type="text/css" href="../css/FontAwesome/css/all.css" />

		<link rel="stylesheet" type="text/css" href="css/main.css">
		<link rel="stylesheet" type="text/css" href="css/nav_bar.css">
		<link rel="stylesheet" type="text/css" href="css/accueil.css">
	</head>

	<body class="secondary">
		<div id="progress-bar-container">
			<div id="progress-bar"></div>
		</div>
		<?php        
			include_once('components/nav_bar.php');
		?>
		<div id="main-container" class="pl-0 pr-0 d-flex">
			<?php
				include_once('components/side_bar.php');
			?>

			<div id="content-container"></div>
		</div>
	</body>
</html>

<script>
	function callBeforeRoute(path){
		console.log("route before");
	}

	function callAfterRoute(path){
		console.log("route after");
	}
</script>

<script src="js/router.js"></script>
<script src="js/page/filter.js"></script>
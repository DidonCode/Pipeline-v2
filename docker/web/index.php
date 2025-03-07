<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="images/logos/logoButifyIcon.png">
		
		<link rel="stylesheet" type="text/css" href="css/Bootstrap/css/bootstrap.css">
		
		<link rel="stylesheet" type="text/css" href="css/FontAwesome/css/all.css">

		<link rel="stylesheet" type="text/css" href="css/Butify/main.css">
		<link rel="stylesheet" type="text/css" href="css/Butify/components/nav_bar.css">

		<link rel="stylesheet" type="text/css" href="css/Butify/player.css">
		<link rel="stylesheet" type="text/css" href="css/Butify/error.css">

		<link rel="stylesheet" type="text/css" href="css/Butify/widgets/card_list.css">

		<link rel="stylesheet" type="text/css" href="css/Butify/widgets/sound/sound_card.css">
		<link rel="stylesheet" type="text/css" href="css/Butify/widgets/sound/sound_setting.css">
		
		<link rel="stylesheet" type="text/css" href="css/Butify/widgets/playlist/playlist_card.css">
		<link rel="stylesheet" type="text/css" href="css/Butify/widgets/playlist/playlist_popup.css">

		<link rel="stylesheet" type="text/css" href="css/Butify/widgets/artist/artist_card.css">
		
		<script src="https://www.youtube.com/iframe_api"></script>
		<script src="https://js.stripe.com/v3/"></script>

		<script src="js/session.js"></script>
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
			
			<div id="player-container" style="display: none">
				<div id="player-progress-container">
					<input id="player-progress-bar" type="range" value="0" min="0"></input>
				</div>

				<div id="player-controller" class="row">
					<div class="col-4 my-auto mx-left d-inline-flex">
						<button id="player-previous">
							<i class="fa-solid fa-backward-step contrast-text"></i>
						</button>

						<button id="player-play" class="mx-4">
							<i class="fa-solid fa-play contrast-text"></i>
						</button>

						<button id="player-pause" class="mx-4" style="display: none;">
							<i class="fa-solid fa-pause contrast-text"></i>
						</button>

						<button id="player-next">
							<i class="fa-solid fa-forward-step contrast-text"></i>
						</button>

						<p id="player-timecode" class="m-0 pl-3 my-auto contrast-text">0:00 / 0:00</p>
					</div>
					<div class="col-4 my-auto mx-auto h-100 d-flex">
 						<div id="player-preview" class="h-100 d-flex" style="width: 80%">

						</div>
						<div class="h-100 ml-4 mx-auto d-flex" style="width: calc(20% - 2rem)">
							<button id="player-like">
								<i class="fa-light fa-thumbs-up contrast-text"></i>	
							</button>

							<button id="player-unlike" hidden>
								<i class="fa-solid fa-thumbs-up contrast-text"></i>
							</button>
						</div>
						
					</div>
					<div class="col-4 my-auto" style="text-align: right;">
						<div class="d-inline">
							
							<input id="player-volume" type="range" min="0" step="0.1" max="100" class="volumeBar" style="display: none">
							

							<button id="player-mute">
								<i class="fa-sharp fa-light fa-volume-high contrast-text"></i>
							</button>
						</div>

						<button id="player-unmute" style="display: none;">
							<i class="fa-sharp fa-light fa-volume-slash contrast-text"></i>
						</button>

						<button id="player-repeat">
							<i class="fa-sharp fa-light fa-arrows-repeat boolean-text"></i>
						</button>

						<button id="player-random">
							<i class="fa-light fa-shuffle boolean-text"></i>
						</button>

						<button id="player-switch">
							<i class="fa-light fa-expand boolean-text"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php
			include_once('widgets/sound/sound_setting.php');
			include_once('widgets/playlist/playlist_popup.php');
			include_once('widgets/report_popup.php');
		?>
	</body>
</html>

<script src="js/player.js"></script>

<script>
	function callBeforeRoute(path){
		if(path !== "/web/play"){
			if(alreadyExist()){
				switchView(true);
			} 
		}
	}

	function callAfterRoute(path){
		if(path === "/web/play"){
			alreadyExist() ? switchView(false) : loadPlayer();
		}
	}
</script>

<script src="js/router.js"></script>

<script src="js/widgets/card_list.js"></script>

<script src="js/widgets/sound/sound_setting.js"></script>
<script src="js/widgets/sound/sound_card.js"></script>

<script src="js/widgets/playlist/playlist_card.js"></script>
<script src="js/widgets/playlist/playlist_popup.js"></script>


<script src="js/widgets/artist/artist_card.js"></script>

<script src="js/components/side_bar.js"></script>
<script src="js/components/nav_bar.js"></script>
<script src="js/widgets/report_popup.js"></script>
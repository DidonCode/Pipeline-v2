<?php  

    require_once("database/connect/database.php");
    require_once("youtube/connect/youtube.php");

    include_once("class/http.php");

    include_once("class/sound.php");
    include_once("class/playlist.php");
    include_once("class/artist.php");

    include_once("database/sound.php");
    include_once("database/playlist.php");
    include_once("database/artist.php");
    include_once("database/like.php");

    include_once("youtube/sound.php");
    include_once("youtube/playlist.php");
    include_once("youtube/artist.php");

    include_once("settings.php");
    include_once("youtube/functions.php");

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET");
	header("Content-Type: application/json");
	header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    if(count(array_keys($_GET)) == 2 AND isset($_GET['artist'], $_GET['type'])){

        try{
            if(empty($_GET['artist']) OR empty($_GET['type'])) throw new Exception("Argument not valid", 400);

            if(is_numeric($_GET['artist'])){
                $artist = DatabaseArtist::byId($_GET['artist']);

                if($_GET['type'] == "sound") $sounds = DatabaseLike::likedSound($artist);
                if($_GET['type'] == "playlist") $sounds = DatabaseLike::likedPlaylist($artist);
                if($_GET['type'] == "artist") $sounds = DatabaseLike::likedArtist($artist);
            }else{
                $sounds = [];
            }
            
            Http::sendResponse(200, $sounds);
        } catch(Exception $e){
            Http::sendError($e);
        }

        return;
    }

    if(count(array_keys($_GET)) == 1 AND isset($_GET['type'])){

		try{

			if($_GET['type'] == "mostListened"){
				Http::sendResponse(200, DatabaseLike::mostListened());
				return;
			}

			if($_GET['type'] == "leastListened"){
				Http::sendResponse(200, DatabaseLike::leastListened());
				return;
			}

            if($_GET['type'] == "mostLikedSound"){
                Http::sendResponse(200, DatabaseLike::mostLikedSound());
                return;
            }
            
            if($_GET['type'] == "mostLikedPlaylist"){
                Http::sendResponse(200, DatabaseLike::mostLikedPlaylist());
                return;
            }

            if($_GET['type'] == "leastArtist"){
                Http::sendResponse(200, DatabaseLike::leastArtist());
                return;
            }

			Http::sendResponse(400, "Invalid type");
		} catch(Exception $e){
			Http::sendError($e);
		}

		return;
	}

<?php
require_once("../php/setting.php");

function delete_image_verif($link) {
    global $HOST_NAME;

    $ext = pathinfo($link, PATHINFO_EXTENSION);

    if(in_array($ext, array("mp3", "mp4"))){
        unlink("../../..".$link);
    }else{
        if(!str_ends_with($link, 'default.png')){
            unlink("../../..".$link);
        }
    }
}


?>

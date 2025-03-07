<?php
require_once("../php/database.php");
require_once("delete_image.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $deleteUser = intval($_POST['delete_user']);

    try {
        $pdoDatabase->beginTransaction();

        $stmt = $pdoDatabase->prepare('SELECT image, banner FROM user WHERE id = ?');
        $stmt->execute(array($_POST['delete_user']));
        $userData = $stmt->fetch();

        if ($userData) {
            delete_image_verif($userData['image']);
            delete_image_verif($userData['banner']);
        }

        $stmt = $pdoDatabase->prepare('SELECT id, image FROM playlist WHERE owner = ?');
        $stmt->execute(array($_POST['delete_user']));
        $playlists = $stmt->fetchAll();

        $stmt = $pdoDatabase->prepare('SELECT id, image, link FROM sound WHERE artist = ?');
        $stmt->execute(array($_POST['delete_user']));
        $sounds = $stmt->fetchAll();

        foreach ($sounds as $sound) {
            delete_image_verif($sound['image']);
            delete_image_verif($sound['link']);

            $stmt = $pdoDatabase->prepare('DELETE FROM like_sound WHERE sound = ?');
            $stmt->execute(array($sound['id']));

            $stmt = $pdoDatabase->prepare('DELETE FROM playlist_sound WHERE sound = ?');
            $stmt->execute(array($sound['id']));

            $stmt = $pdoDatabase->prepare('DELETE FROM activity WHERE sound = ?');
            $stmt->execute(array($sound['id']));

            $stmt = $pdoDatabase->prepare('DELETE FROM sound WHERE id = ?');
            $stmt->execute(array($sound['id']));
        }

        $stmt = $pdoDatabase->prepare('SELECT id, image FROM playlist WHERE owner = ?');
        $stmt->execute(array($_POST['delete_user']));
        $playlists = $stmt->fetchAll();

        foreach ($playlists as $playlist) {
            delete_image_verif($playlist['image']);

            $stmt = $pdoDatabase->prepare('DELETE FROM playlist_collaborator WHERE playlist = ?');
            $stmt->execute(array($playlist['id']));

            $stmt = $pdoDatabase->prepare('DELETE FROM playlist_sound WHERE playlist = ?');
            $stmt->execute(array($playlist['id']));

            $stmt = $pdoDatabase->prepare('DELETE FROM like_playlist WHERE playlist = ?');
            $stmt->execute(array($playlist['id']));

            $stmt = $pdoDatabase->prepare('DELETE FROM playlist WHERE id = ?');
            $stmt->execute(array($playlist['id']));
        }

        $stmt = $pdoDatabase->prepare('DELETE FROM playlist WHERE owner = ?');
        $stmt->execute(array($_POST['delete_user']));

        $stmt = $pdoDatabase->prepare('DELETE FROM like_sound WHERE user = ?');
        $stmt->execute(array($_POST['delete_user']));

        $stmt = $pdoDatabase->prepare('DELETE FROM like_playlist WHERE user = ?');
        $stmt->execute(array($_POST['delete_user']));

        $stmt = $pdoDatabase->prepare('DELETE FROM like_artist WHERE user = ?');
        $stmt->execute(array($_POST['delete_user']));

        $stmt = $pdoDatabase->prepare('DELETE FROM like_artist WHERE artist = ?');
        $stmt->execute(array($_POST['delete_user']));

        $stmt = $pdoDatabase->prepare('DELETE FROM activity WHERE user = ?');
        $stmt->execute(array($_POST['delete_user']));

        $stmt = $pdoDatabase->prepare('DELETE FROM user WHERE id = ?');
        $stmt->execute(array($_POST['delete_user']));

        $pdoDatabase->commit();
        header('Location: ../utilisateur');

    } catch (PDOException $e) {
        die("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_playlist'])) {
    $deletePlaylist = intval($_POST['delete_playlist']);

    try {
        $pdoDatabase->beginTransaction();

        $stmt = $pdoDatabase->prepare('SELECT image FROM playlist WHERE id = ?');
        $stmt->execute(array($_POST['delete_playlist']));
        $playlistData = $stmt->fetch();

        if ($playlistData) {
            delete_image_verif($playlistData['image']);
        }

        $stmt = $pdoDatabase->prepare('DELETE FROM playlist_collaborator WHERE playlist = ?');
        $stmt->execute(array($_POST['delete_playlist']));

        $stmt = $pdoDatabase->prepare('DELETE FROM playlist_sound WHERE playlist = ?');
        $stmt->execute(array($_POST['delete_playlist']));

        $stmt = $pdoDatabase->prepare('DELETE FROM like_playlist WHERE playlist = ?');
        $stmt->execute(array($_POST['delete_playlist']));

        $stmt = $pdoDatabase->prepare('DELETE FROM playlist WHERE id = ?');
        $stmt->execute(array($_POST['delete_playlist']));

        $pdoDatabase->commit();
        header('Location: ../playlist');
    } catch (PDOException $e) {
        die("Erreur lors de la suppression de la playlist : " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_sound'])) {
    $deleteSound = $_POST['delete_sound'];

    try {
        $pdoDatabase->beginTransaction();

        $stmt = $pdoDatabase->prepare('SELECT image, link FROM sound WHERE id = ?');
        $stmt->execute(array($_POST['delete_sound']));
        $soundData = $stmt->fetchAll();

        if (count($soundData) > 0) {
            delete_image_verif($soundData[0]['image']);
            delete_image_verif($soundData[0]['link']);
        }

        $stmt = $pdoDatabase->prepare('DELETE FROM like_sound WHERE sound = ?');
        $stmt->execute(array($_POST['delete_sound']));

        $stmt = $pdoDatabase->prepare('DELETE FROM playlist_sound WHERE sound = ?');
        $stmt->execute(array($_POST['delete_sound']));

        $stmt = $pdoDatabase->prepare('DELETE FROM activity WHERE sound = ?');
        $stmt->execute(array($_POST['delete_sound']));

        $stmt = $pdoDatabase->prepare('DELETE FROM sound WHERE id = ?');
        $stmt->execute(array($_POST['delete_sound']));

        $pdoDatabase->commit();
        header('Location: ../titre');
    } catch (PDOException $e) {
        die("Erreur lors de la suppression de la musique : " . $e->getMessage());
    }
}
?>

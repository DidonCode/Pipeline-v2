<?php

require_once("../php/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_user']) && isset($_POST['grade'])) {

    $admin_id = intval($_POST['admin_user']);
    $grade_nouveau = intval($_POST['grade']);

    try {
        $updateQuery = $pdoDatabase->prepare('UPDATE user SET grade = :grade WHERE id = :id');
        $updateQuery->bindParam(':grade', $grade_nouveau, PDO::PARAM_INT);
        $updateQuery->bindParam(':id', $admin_id, PDO::PARAM_INT);
        $updateQuery->execute();
        
        header('Location: ../permission');
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
    }
}

?>
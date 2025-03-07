<?php
require_once("../php/database.php");
require_once("../php/setting.php");

$searchQuery = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

?>


<h1 class="titre_admin">Utilisateur</h1>

<div class="search-container">
    <input type="text" id="recherche_user" class="search-box" onkeyup="filtre('recherche_user', 'table_user', [0, 2, 3])" placeholder="Rechercher par ID, pseudo ou email">
</div>

<div class="table-container">
    <table id="table_user" class="table contrast-text mt-3 admin_tab">
        <thead class="admin_head">
            <tr>
                <th class="contrast-text" scope="col">ID</th>
                <th class="contrast-text" scope="col">Image</th>
                <th class="contrast-text" scope="col">Utilisateur</th>
                <th class="contrast-text" scope="col">Mail</th>
                <th class="contrast-text" scope="col">Statut</th>
                <th class="contrast-text" scope="col">Artiste</th>
                <th class="contrast-text" scope="col">Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $query = 'SELECT * FROM user';
                $request_user_table = $pdoDatabase->prepare($query);

                $request_user_table->execute();
                $usersData = $request_user_table->fetchAll();

                if (empty($usersData)) {
                    echo '<tr><td colspan="7" class="text-center">Aucun utilisateur trouvé.</td></tr>';
                }

                foreach ($usersData as $user) {
                    
            ?>
                <tr>
                    <td class="contrast-text"><?php echo htmlspecialchars($user['id']); ?></td>
                    <td class="contrast-text"><img class="image_album" src="<?php echo $HOST_NAME.$user['image']; ?>"></td>
                    <td class="contrast-text"><?php echo htmlspecialchars($user['pseudo']); ?></td>
                    <td class="contrast-text"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="contrast-text"><?php echo $user['public'] == 1 ? 'Public' : 'Privée'; ?></td>
                    <td class="contrast-text"><?php echo $user['artist'] == 1 ? 'Artiste' : 'Membre'; ?></td>
                    <td>
                        <form method="POST" action="php/delete.php">
                            <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>

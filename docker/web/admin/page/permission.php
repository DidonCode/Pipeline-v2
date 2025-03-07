<?php
require_once("../php/database.php");
require_once("../php/setting.php");
?>

<div>
    <h1 class="titre_admin">Permission</h1>
</div>



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
                <th class="contrast-text" scope="col">Grade</th>
                <th class="contrast-text" scope="col">Action</th>
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
                    <td class="contrast-text"><img class="image_album" src="<?php echo $HOST_NAME.$user['image']; ?>" alt="Image"></td>
                    <td class="contrast-text"><?php echo htmlspecialchars($user['pseudo']); ?></td>
                    <td class="contrast-text"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="contrast-text"><?php echo $user['grade'] == 1 ? 'Admin' : 'Membre'; ?></td>
                    <td class="contrast-text">
                        <form method="POST" action="./php/admin_user.php">
                            <input type="hidden" name="admin_user" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <select name="grade" onchange="this.form.submit()">
                                <option value="1" <?php echo ($user['grade'] == 1) ? 'selected' : ''; ?>>Admin</option>
                                <option value="0" <?php echo ($user['grade'] == 0) ? 'selected' : ''; ?>>Membre</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>

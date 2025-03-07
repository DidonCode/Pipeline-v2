<?php
require_once("../php/database.php");
require_once("../php/setting.php");

$request_playlist_table = $pdoDatabase->prepare('SELECT * FROM playlist');
$request_playlist_table->execute();
$playlistsData = $request_playlist_table->fetchAll();

?>


<h1 class="titre_admin">Playlist</h1>

<div class="search-container">
    <input type="text" id="recherche_playlist" class="search-box" onkeyup="filtre('recherche_playlist', 'table_playlist', [0, 2, 3, 4])" placeholder="Rechercher par ID, titre, description ou propriétaire">
</div>

<div class="table-container">
    <table class="table contrast-text mt-3 admin_tab" id="table_playlist">
        <thead class="admin_head">
            <tr>
                <th class="contrast-text" scope="col">ID</th>
                <th class="contrast-text" scope="col">Image</th>
                <th class="contrast-text" scope="col">Titre</th>
                <th class="contrast-text" scope="col">Description</th>
                <th class="contrast-text" scope="col">Propriétaire</th>
                <th class="contrast-text" scope="col">Visibilité</th>
                <th class="contrast-text" scope="col">Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($playlistsData as $playlist){

            $request_user_pseudo = $pdoDatabase->prepare('SELECT pseudo FROM user WHERE id = ?');
            $request_user_pseudo->execute([$playlist['owner']]);
            $pseudo_user = $request_user_pseudo->fetch(PDO::FETCH_ASSOC);

            $pseudo_verif = isset($pseudo_user['pseudo']) ? htmlspecialchars($pseudo_user['pseudo']) : 'Inconnu';

            ?>
                <tr>
                    <td><?php echo htmlspecialchars($playlist['id']); ?></td>
                    <td><img class="image_album" src="<?php echo $HOST_NAME.$playlist['image']; ?>" alt="Image de la playlist"></td>
                    <td><?php echo htmlspecialchars($playlist['title']); ?></td>
                    <td><?php echo htmlspecialchars($playlist['description']); ?></td>
                    <td class="contrast-text">
                        <a href="utilisateur" class="contrast-text">
                            <?php echo $pseudo_verif; ?>
                        </a>
                    </td>
                    <td><?php echo $playlist['public'] == 1 ? 'Public' : 'Privée'; ?></td>
                    <td>
                        <form method="POST" action="php/delete.php">
                            <input type="hidden" name="delete_playlist" value="<?php echo htmlspecialchars($playlist['id']); ?>">
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
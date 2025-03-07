<?php
require_once("../php/database.php");
require_once("../php/setting.php");

$request_sound_table = $pdoDatabase->prepare('SELECT * FROM sound');
$request_sound_table->execute();
$soundsData = $request_sound_table->fetchAll();
?>

<h1 class="titre_admin">Titre</h1>

<div class="search-container">
    <input type="text" id="recherche_user" class="search-box" onkeyup="filtre('recherche_user', 'table_user', [0, 2, 3])" placeholder="Rechercher par ID, pseudo ou email">
</div>

<div class="table-container">
    <table id="table_title" class="table contrast-text mt-3 admin_tab">
        <thead class="admin_head">
            <tr>
                <th class="contrast-text" scope="col">ID</th>
                <th class="contrast-text" scope="col">Image</th>
                <th class="contrast-text" scope="col">Titre</th>
                <th class="contrast-text" scope="col">Type</th>
                <th class="contrast-text" scope="col">Musique</th>
                <th class="contrast-text" scope="col">Artiste</th>
                <th class="contrast-text" scope="col">Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($soundsData as &$sound) { ?>
                <?php
				$request_artist_pseudo = $pdoDatabase->prepare('SELECT pseudo FROM user WHERE id = ?');
                $request_artist_pseudo->execute([$sound['artist']]);
                $pseudo_artist = $request_artist_pseudo->fetch(PDO::FETCH_ASSOC);

                $pseudo_verif = !empty($pseudo_artist['pseudo']) ? htmlspecialchars($pseudo_artist['pseudo']) : 'Inconnu';
                ?>

                <tr>
                    <td class="contrast-text"><?php echo $sound['id']; ?></td>
                    <td class="contrast-text"><img class="image_album" src="<?php echo $HOST_NAME.$sound['image']; ?>"></td>
                    <td class="contrast-text"><?php echo $sound['title']; ?></td>
                    <td class="contrast-text"><?php echo $sound['type']; ?></td>
                    <td class="contrast-text">
						<a href="<?php echo $HOST_NAME.$sound['link']; ?>" target="_blank">
    					    <i class="fa-solid fa-play"></i>
    					</a>
                    </td>

					
                    <td class="contrast-text">
                        <a href="utilisateur" class="contrast-text">
                            <?php echo $pseudo_verif; ?>
                        </a>
                    </td>
                    <td>
                        <form method="POST" action="php/delete.php">
                            <input type="hidden" name="delete_sound" value="<?php echo $sound['id']; ?>">
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
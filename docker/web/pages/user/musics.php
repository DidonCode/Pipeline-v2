<table id="music-table" class="table contrast-text mt-3 w-75 mx-auto">
    <thead class="primary">
        <tr>
            <th scope="col">Image</th>
            <th scope="col">Titre</th>
            <th scope="col">Type</th>
            <th scope="col">Supprimer</th>
        </tr>
    </thead>
    <tbody id="music-table-result"></tbody>
<table>
<div id="musicEdition" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Éditer la musique</h4>
                <button id="closeEditModal" type="button" class="close">&times;</button>
            </div>
            <div class="modal-body secondary">
                <form class="flex-column mx-auto" id="audio-form" style="display: flex">
                    <input type="text" id="audio-title" name="title" placeholder="Titre"
                        class="primary-secondary contrast-text clRounded1 pt-2 pb-2 pl-2">
                    <input type="file" id="audio-image-upload" name="image" accept=".png, .jpg" hidden>
                    <div id="audio-image-click">
                        <div class="upload-file" id="audio-image-click-view">
                            <i class="fa-solid fa-arrow-up-from-bracket contrast-text"></i>
                            <p class="contrast-text">Cliquez pour importer l'image</p>
                        </div>
                        <img id="audio-image-preview" src="" style="display: none;">
                    </div>
                    <input type="file" id="audio-audio-upload" name="audio" accept=".mp3" hidden>
                    <button class="btn action clRounded1 clTextBtn pl-2 pt-2">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="collection" class="mx-auto">
    <div class="h-100 row">
        <div class="col-4">
            <div class="text-center">
                <div id="collection-image-container" class="mx-auto">
                    <img id="collection-image" src="/storage/playlist/default.png">
                    <button id="collection-image-edit" style="display: none;">
                        <i class="fa-light fa-pen contrast-text"></i>
                    </button>
                    <input type="file" id="collection-image-upload" name="collection-image" accept=".png, .jpg" hidden>
                </div>
                <h3 id="collection-title" class="contrast-text"></h3>
                <div style="margin: 10px 0 20px;">
                    <img id="collection-owner-image" src="">
                    <a id="collection-owner" class="contrast-text"></a>
                </div>
                <p id="collection-description" class="contrast-text"></p>
            </div>
            <div id="collection-actions" class="text-center">
                <button id="collection-edit" class="btn rounded1 contrast-text" style="display: none;">
                    <i class="fa-light fa-pen"></i>
                </button>

                <button id="collection-play" class="btn action contrast-text">
                    <i class="fa-solid fa-play"></i>
                </button>

                <div id="collection-plus" class="btn rounded1 contrast-text">
                    <i class="fa-solid fa-ellipsis-vertical"></i>

                    <div id="collection-action-popup" hidden>
                        <button id="collection-play-random" class="contrast-text">
                            <i class="fa-solid fa-shuffle my-auto"></i>
                            Lecture aléatoire
                        </button>
                        <button id="collection-partage" class="contrast-text">
                            <i class="fa-light fa-share my-auto"></i>
                            Partage
                        </button>
                        <button id="collection-like" class="contrast-text">
                            <i class="fa-regular fa-heart my-auto"></i>
                            Aimer
                        </button>
                        <button id="collection-unlike" class="contrast-text" hidden>
                            <i class="fa-solid fa-heart my-auto"></i>
                            Supprimer
                        </button>
                        <button id="collection-clone" class="contrast-text">
                            <i class="fa-solid fa-clone"></i>
                            Dupliquer
                        </button>
                        <button id="collection-exit" class="contrast-text">
                        <i class="fa-solid fa-arrow-right-from-bracket my-auto"></i>
                            Quitter
                        </button>
                        </button>
                        <button id="collection-delete" class="contrast-text">
                            <i class="fa-regular fa-trash-can my-auto"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 mx-auto h-100">
            <div id="collection-sounds" style="align-content: center;">
                <div class="loader mx-auto"></div>
            </div>  
        </div>
    </div>
</div>
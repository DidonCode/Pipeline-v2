<div id="playlist-popup" class="modal" role="dialog">
    <div class="modal-dialog bg-transparent">
        <div class="modal-content">
            <div class="modal-header border-0 primary">
                <h4 id="playlist-popup-title" class="modal-title contrast-text">Créer une playlist</h4>
                <button id="playlist-popup-close" type="button" class="close my-auto"><i class="fa-solid fa-xmark contrast-text"></i></button>
            </div>
            <form id="playlist-form">  
                <div class="modal-body secondary">                                          
                    <input type="text" placeholder="Titre *" id="playlist-title" class="w-100 mb-3 mt-1 form-control border-secondary" require>
                    <input type="text" placeholder="Description" id="playlist-description" class="w-100 mb-3 form-control border-secondary" require>
                    <div class="d-flex justify-content-between mb-1" style="height: 38px;">
                        <select id="playlist-visibility" class="form-select" style="width:100%;">
                            <option value="0">Privée</option>
                            <option value="1">Publique</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 primary">                                                    
                    <button id="playlist-submit" type="submit" class="btn action clRounded1 clBtnGrad mx-auto">Ajouter des musiques</button>
                </div>
            </form>
        </div>                
    </div>
</div>

<div id="collaborator-popup" class="modal" role="dialog">
    <div class="modal-dialog bg-transparent">
        <div class="modal-content">
            <div class="modal-header border-0 primary">
                <h4 class="modal-title contrast-text">Collaborer</h4>
                <button id="collaborator-popup-close" type="button" class="close my-auto"><i class="fa-solid fa-xmark contrast-text"></i></button>
            </div>
            <div class="modal-body secondary">
                <input type="text" id="collaborator-pseudo" placeholder="Recherche" class="clRounded1 w-100 mb-2 form-control border-secondary">

                <div id="collaborator-search-result"></div>

                <hr class="seperation">

                <div id="collaborator-result"></div>

            </div>
        </div>                
    </div>
</div>
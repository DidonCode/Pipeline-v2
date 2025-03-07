<div id="reporting-popup" class="modal md" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title d-inline">Êtes vous sûr de vouloir signaler :</h4>
                <h4 id="reportName" class="modal-title d-inline"></h4>
                <h4 class="modal-title d-inline">?</h4>
                <button id="close-popup" type="button" class="close">&times;</button>
            </div>

            <div class="modal-body secondary">
                <h6>Raison du signalement :</h6>
                <form class="flex-column mx-auto" id="report-form" style="display: flex">
                    <label class="checkWrap">
                        <span class="check-label-report">Nom déplacé ou à connotation négative</span>
                        <input id="negative-name" type="checkbox" hidden>
                        <span class="checkmark"></span>
                    </label>
                    <label class="checkWrap">
                        <span class="check-label-report">Musique dégradante</span>
                        <input id="degrading-sound" type="checkbox" hidden>
                        <span class="checkmark"></span>
                    </label>
                    <label class="checkWrap">
                        <span class="check-label-report">Description méchante</span>
                        <input id="mean-description" type="checkbox" hidden>
                        <span class="checkmark"></span>
                    </label>
                    <label class="checkWrap">
                        <span class="check-label-report">Autre</span>
                        <input id="other-choice" type="checkbox" hidden>
                        <span class="checkmark"></span>
                    </label>
                    <div class="textArea mb-3" id="report-comment" hidden>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Entrer la raison du signalement"></textarea>
                    </div>
                    <button class="btn action clRounded1 clTextBtn pl-2 pt-2 mt-2">Signaler</button>
                </form>
            </div>
        </div>
    </div>
</div>
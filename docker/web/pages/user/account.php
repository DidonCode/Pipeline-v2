<div id="account" class="mx-auto mt-4">
  <div>
    <div>
      <h3 class="contrast-text mb-4">Mon compte</h3>
    </div>
    <div class="d-flex justify-content-between">
      <div>
        <img id="idProfileImg" src="/storage/user/profile/default.png" alt="Photo de profil" style="width:100px; aspect-ratio:1" class="rounded-circle">
        <button id="profileImg-editBtn" class="btn px-3 py-2 rounded-circle contrast-text" style="height:50px; width:50px; background-color:transparent">
          <i class="fa-light fa-pen"></i>
        </button>
        <input id="profileImg-editInp" type="file" hidden>
      </div>
      <div>
        <div>
          <span class="contrast-text">Privé</span>
          <span class="contrast-text m-3">Public</span>
        </div>
        <label class="littleSwitch">
          <input id="visibility" type="checkbox">
          <span class="littleSlider round"></span> 
        </label>
        <div>
        <button id="profile-exposure" class="btn action clRounded1 me-2" style="width: 100px;">Profil</button>
        </div>
      </div>
    </div>
    
    <form id="updateForm">
      <div>
        <h5 class="contrast-text my-3">Mes informations</h5>
      </div>
      <div>     
        <div>
          <div class="mb-3 mt-3">
            <label for="pseudo" class="form-label contrast-text">Pseudonyme :</label>
            <input type="text" class="form-control" id="pseudo" placeholder="" name="pseudo">
          </div>
        </div>
        <div class="">
          <div class="mb-3 mt-3">
            <label for="email" class="form-label contrast-text">E-mail :</label>
            <input type="email" class="form-control" id="email" placeholder="" name="email">
          </div>
          <div class="mb-3">
            <label for="newPwd" class="form-label contrast-text">Nouveau mot de passe :</label>
            <input type="password" class="form-control" id="newPwd" placeholder="" name="newPswd">
          </div>
        </div>
      </div>
      <div class="text-center my-4">
        <button type="submit" class="btn actionGrad">Enregister les modifications</button>
      </div>
    </form>
  </div>

  <div id="subscription-container" class="mt-5">
    <div>
      <h3 class="contrast-text mb-4">Mon Abonnement</h3>
    </div>
    
    <div id="subscription">
        <i class="fa-thin fa-headphones"></i>
        <div>
            <h3 id="subscription-title">Premium</h3>
            <h6 id="subscription-description" class="contrast-text">expire le: 15-01-3435</h6>
        </div>
        <button id="subscription-cancel">Annuler</button>
    </div>
</div>
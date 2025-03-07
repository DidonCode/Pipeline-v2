<div class="container-fluid h-7" style="border-bottom: 1.5px solid var(--border)">
    <div class="row primary py-2 h-100">
        <div class="col-4">
            <div class="d-flex h-100">
                <a href="/web/" onclick="route(event)" class="nav-wave-text">
                    <img src="images/logos/logoButify.png" alt="Logo Butify" style="width: 70px;">
                    <span class="h2 ml-1 my-auto" style="--i:0">B</span>
                    <span class="h2 my-auto" style="--i:1">u</span>
                    <span class="h2 my-auto" style="--i:2">t</span>
                    <span class="h2 my-auto" style="--i:3">i</span>
                    <span class="h2 my-auto" style="--i:4">f</span>
                    <span class="h2 my-auto" style="--i:5">y</span>
                </a>
            </div>
        </div>
        <div class="col-4">
            <div class="d-flex justify-content-center h-100">
                <?php
                    include_once('widgets/search.php');
                ?>
            </div>
        </div>
        <div class="col-4">
            <div class="d-flex justify-content-end h-100">
                <div id="user-login" class="my-auto">
                    <a href="/web/login.php" class="btn action clRounded1 text-dark clTextBtn my-auto">Se connecter</a>
                </div>

                <div id="user-connected" class="my-auto" hidden>
                    <div class="dropdown">
                        <img src="/storage/user/profile/default.png" id="profil-image" class="clGrey2 rounded-circle"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />

                        <div id="profil-menu" class="dropdown-menu primary-secondary mt-3 p-2">
                            <div>
                                <a href="/web/account" onclick="route(event)"
                                    class="dropdown-item contrast-text px-2 mb-2">
                                    <i class="fa-solid fa-user pr-3"></i>
                                    Compte
                                </a>
                                <a href="/web/upload" onclick="route(event)"
                                    class="dropdown-item contrast-text px-2 mb-2">
                                    <i class="fa-regular fa-file-import pr-3"></i>
                                    Publier
                                </a>
                                <a href="/web/musics" onclick="route(event)"
                                    class="dropdown-item contrast-text px-2 mb-2">
                                    <i class="fa-solid fa-list-check pr-3"></i>
                                    Gérer
                                </a>
                                <span onclick="sessionDestroy()" class="dropdown-item contrast-text px-2 mb-2"
                                    style="cursor: pointer">
                                    <i class="fa-solid fa-lock pr-3"></i>
                                    Déconnexion
                                </span>
                                <button id="dark-mode" class="dropdown-item contrast-text px-2 mb-1"
                                    style="display: none; height: auto;">
                                    <i class="fa-solid fa-moon pr-3"></i>
                                    Sombre
                                </button>
                                <button id="light-mode" class="dropdown-item contrast-text px-2 mb-1"
                                    style="height: auto;">
                                    <i class="fa-sharp fa-solid fa-sun-bright pr-3"></i>
                                    Clair
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
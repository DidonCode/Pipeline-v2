<div class="pl-2 pr-2 primary nav-bar">
    <div class="d-flex flex-column align-items-center align-items-sm-start contrast-text h-100">
        <a href="/web/home" onclick="route(event)" class="mx-auto" style="margin-top: 1rem;">
            <button class="btn action clRounded1 clTextBtn clBtnMenuSize mb-1 mt-1">
                <div class="d-flex">
                    <i class="fa-solid fa-house mb-auto mt-auto pr-3"></i>
                    <p class="m-0">Accueil</p>
                </div>
            </button>
        </a>
        <a href="/web/explore" onclick="route(event)" class="mx-auto">
            <button class="btn action clRounded1 clTextBtn clBtnMenuSize mb-1 mt-1">
                <div class="d-flex">
                    <i class="fa-solid fa-music mb-auto mt-auto pr-3"></i>
                    <p class="m-0">Explorer</p>
                </div>                        
            </button>
        </a>
        <a href="/web/library" onclick="route(event)" class="mx-auto">
            <button class="btn action clRounded1 clTextBtn clBtnMenuSize mb-1 mt-1">
                <div class="d-flex">
                    <i class="fa-solid fa-compact-disc mb-auto mt-auto pr-3"></i>
                    <p class="m-0">Bibliothèque</p>
                </div>
            </button>
        </a>

        <hr style="border: 1px solid var(--border); width: 65%;" />

        <div id="personal-playlist-side-bar" class="w-100">
            <button id="personal-new-playlist" class="btn mt-1 mb-1 mx-auto clRounded1 w-100 nav-bar-playlist-add">
                <i class="fa-light fa-plus contrast-text"></i>
            </button>
        </div>
    </div>
</div>
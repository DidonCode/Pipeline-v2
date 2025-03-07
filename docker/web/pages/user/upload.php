<div id="upload-choice" class="d-flex mx-auto">
	<button id="video-view" class="btn primary-secondary contrast-text clRounded1 mx-auto">Vidéo</button>
	<button id="audio-view" class="btn action clRounded1 mx-auto">Musique</button>
</div>

<form class="flex-column mx-auto" id="audio-form" style="display: flex">
	<input type="text" id="audio-title" name="title" placeholder="Titre" class="primary-secondary contrast-text clRounded1 pt-2 pb-2 pl-2">
	<input type="file" id="audio-image-upload" name="image" accept=".png, .jpg" hidden>
	<div id="audio-image-click">
		<div class="upload-file" id="audio-image-click-view">
			<i class="fa-solid fa-arrow-up-from-bracket contrast-text"></i>
			<p class="contrast-text">Cliquez pour importer l'image</p>
		</div>
		<img id="audio-image-preview" src="" style="display: none;">
	</div>
	<input type="file" id="audio-audio-upload" name="audio" accept=".mp3" hidden>
	<div id="audio-audio-click">
		<div class="upload-file">
			<i class="fa-solid fa-arrow-up-from-bracket contrast-text"></i>
			<p class="contrast-text">Cliquez pour importer le fichier audio</p>
		</div>
		<audio id="audio-audio-preview" controls style="display: none;" controlsList="nodownload noplaybackrate"></audio>
	</div>
	<button class="btn action clRounded1 clTextBtn pl-2 pt-2">Publier</button>
</form>

<form class="flex-column mx-auto" id="video-form" style="display: none;">
	<input type="text" id="video-title" name="title" placeholder="Titre" class="primary-secondary contrast-text clRounded1 pt-2 pb-2 pl-2">
	<input type="file" id="video-image-upload" name="image" accept=".png, .jpg" hidden>
	<div id="video-image-click">
		<div class="upload-file" id="video-image-click-view">
			<i class="fa-solid fa-arrow-up-from-bracket contrast-text"></i>
			<p class="contrast-text">Cliquez pour importer l'image</p>
		</div>
		<img id="video-image-preview" src="" style="display: none;">
	</div>
	<input type="file" id="video-video-upload" name="video" accept=".mp4" hidden>
	<div id="video-video-click">
		<div class="upload-file" id="video-video-click-view">
			<i class="fa-solid fa-arrow-up-from-bracket contrast-text"></i>
			<p class="contrast-text">Cliquez pour importer la vidéo</p>
		</div>
		<video id="video-video-preview" controls style="display: none;" controlsList="nodownload noplaybackrate" disablepictureinpicture></video>
	</div>
	<button class="btn action clRounded1 clTextBtn pl-2 pt-2">Publier</button>
</form>
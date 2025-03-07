const soundSetting = document.getElementById('sound-setting');
const soundSettingClose = document.getElementById('sound-setting-close');
const soundSettingPlaylist = document.getElementById('sound-setting-playlist');

function makeSoundSetting(callback) {
	soundSetting.style.display = 'block';

	soundSettingClose.onclick = function () {
		soundSetting.style.display = 'none';
	};

	const load = document.createElement('div');
	load.classList.add('loader', 'm-auto');

	soundSettingPlaylist.innerHTML = '';
	soundSettingPlaylist.append(load);

	let formData = new FormData();

	formData.append('token', token);

	apiCall('api/user/playlist', formData, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData['error'] !== undefined) {
				console.log(parsedData['error']);
			} else {
				soundSettingPlaylist.innerHTML = '';

				if (parsedData.length == 0) {
					const message = document.createElement('p');
					message.classList.add('contrast-text', 'text-center');
					message.innerText = "Vous n'avez pas encore de playlist";

					soundSettingPlaylist.append(message);
				}

				parsedData.map((playlist) => {
					(async () => {
						const card = new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, false);

						soundSettingPlaylist.append(await card.getSkeleton());
						card.makeCard();

						card.playlistCard.href = '';
						card.playlistCard.onclick = function (e) {
							e.preventDefault();

							callback(playlist.id);

							soundSetting.style.display = 'none';
						};

						card.playlistCardPlus.remove();
					})();
				});
			}
		}
	});
}

const sideBarPlaylist = document.getElementById('personal-playlist-side-bar');
const sideBarNewPlaylist = document.getElementById('personal-new-playlist');
const sideBarEdit = document.getElementById('sideBarMusicEdit');

function addPlaylistSideBar(image, link) {
	let playlistLink = document.createElement('a');
	playlistLink.href = link;
	playlistLink.onclick = function () {
		route(event);
	};

	let playlistImg = document.createElement('img');

	playlistImg.src = image;

	playlistImg.classList.add('mt-1', 'mb-1', 'mx-auto', 'clRounded1', 'img-fluid', 'nav-bar-playlist');

	playlistLink.appendChild(playlistImg);
	sideBarPlaylist.appendChild(playlistLink);
}

function loadPlaylistSideBar() {
	let formData = new FormData();

	formData.append('token', token);

	apiCall('api/user/playlist', formData, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData != null) {
				if (parsedData['error']) {
					routeError(parsedData['error']);
					return;
				} else {
					sideBarPlaylist.querySelectorAll('a').forEach((playlist) => {
						playlist.remove();
					});

					addPlaylistSideBar('http://localhost:8081/storage/playlist/liked.png', '/web/collection?id=liked');

					parsedData.forEach((playlist) => {
						addPlaylistSideBar(playlist.image, '/web/collection?id=' + playlist.id);
					});
				}
			}
		}
	});
}

sideBarNewPlaylist.onclick = function () {
	if (!sessionExist()) {
		sessionDestroy();
		return;
	}

	function addPlaylist(title, description, visibility) {
		let formData = new FormData();

		formData.append('title', title);
		formData.append('description', description);
		formData.append('public', visibility);
		formData.append('token', token);

		apiCall('api/user/playlist', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
					} else {
						loadPlaylistSideBar();
						window.location.href = '/web/collection?id=' + parsedData.id;
						route(event);
					}
				}
			}
		});
	}

	makeCreatePlaylistPopup(addPlaylist);
};

if (sessionExist()) loadPlaylistSideBar();

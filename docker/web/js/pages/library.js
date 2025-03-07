(async () => {
	if (!sessionExist()) {
		sessionDestroy();
		return;
	}

	const contentContainer = document.getElementById('content-container');
	const buttonNewPlaylist = document.getElementById('button-new-playlist');

	let myPlaylistList = null;

	alreadyExist() ? buttonNewPlaylist.classList.add('button-new-playlist-player') : buttonNewPlaylist.classList.remove('button-new-playlist-player');

	async function likedArtist() {
		let artistFormData = new FormData();

		artistFormData.append('type', 'artist');
		artistFormData.append('token', token);

		await apiCall('api/user/like', artistFormData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'like-artist',
							'Vos artistes aimés',
							parsedData.map((artist) => {
								return new ArtistCard(artist.id, artist.image, artist.pseudo, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.edit((card) => card.artistCardPlus.remove());
						list.makeList();
					}
				}
			}
		});
	}

	async function likedPlaylist() {
		let playlistFormData = new FormData();

		playlistFormData.append('type', 'playlist');
		playlistFormData.append('token', token);

		await apiCall('api/user/like', playlistFormData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'like-playlist',
							'Vos playlists aimées',
							parsedData.map((playlist) => {
								return new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.edit((card) => card.playlistCardPlus.remove());
						list.makeList();
					}
				}
			}
		});
	}

	async function likedSound() {
		let soundFormData = new FormData();

		soundFormData.append('type', 'sound');
		soundFormData.append('token', token);

		await apiCall('api/user/like', soundFormData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'like-sound',
							'Vos musiques aimées',
							parsedData.map((sound) => {
								return new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.edit((card) => card.soundCardPlus.remove());
						list.makeList();
					}
				}
			}
		});
	}

	async function myPlaylist() {
		let persoPlaylistFormData = new FormData();

		persoPlaylistFormData.append('token', token);

		await apiCall('api/user/playlist', persoPlaylistFormData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						myPlaylistList = new List(
							'my-playlist',
							'Vos playlists',
							parsedData.map((playlist) => {
								return new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, true);
							}),
						);

						contentContainer.append(await myPlaylistList.getSkeleton());
						myPlaylistList.edit((card) => {
							card.playlistCardPlus.remove();
							card.playlistCardBadge.remove();
						});
						myPlaylistList.makeList();
					}
				}
			}
		});
	}

	await myPlaylist();
	await likedSound();
	await likedPlaylist();
	await likedArtist();

	buttonNewPlaylist.onclick = function () {
		async function addPlaylist(title, description, visibility) {
			let formData = new FormData();

			formData.append('title', title);
			formData.append('description', description);
			formData.append('public', visibility);
			formData.append('token', token);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData['error'] == undefined) {
						const card = new PlaylistCard(parsedData.id, parsedData.image, parsedData.title, parsedData.description, parsedData.owner, true);

						if (myPlaylistList == null) {
							myPlaylistList = new List('my-playlist', 'Vos playlists', [card]);

							if (contentContainer.getElementsByTagName('h3').length == 1) {
								contentContainer.getElementsByTagName('h3')[0].remove();
								contentContainer.append(await myPlaylistList.getSkeleton());
							} else {
								contentContainer.insertBefore(await myPlaylistList.getSkeleton(), contentContainer.firstChild);
							}
						}

						function edit(card) {
							card.playlistCardPlus.remove();
							myPlaylistList.scrollEnd();
						}

						myPlaylistList.makeList();
						myPlaylistList.edit(edit);
					}
				}
			});
		}

		makeCreatePlaylistPopup(addPlaylist);
	};

	if (document.getElementsByClassName('card-list').length === 0) {
		const text = document.createElement('h3');
		text.innerText = 'Partez à la découverte et revenez ici pour voir vos récoltes !';
		text.classList.add('contrast-text', 'text-center');
		text.style.marginTop = '40vh';
		contentContainer.append(text);
	}
})();

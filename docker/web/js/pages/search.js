(() => {
	const url = window.location.search;
	const urlParams = new URLSearchParams(url);
	const query = urlParams.get('query');

	search.value = query; // JS du widget search

	const soundsResult = document.getElementById('result-sounds');
	const playlistsResult = document.getElementById('result-playlists');
	const artistsResult = document.getElementById('result-artists');

	function viewMore(list, callback, argument) {
		const viewMore = document.createElement('button');
		viewMore.innerText = 'Plus';
		viewMore.classList.add('btn', 'action', 'viewMore');
		viewMore.onclick = function () {
			callback(argument);
			viewMore.remove();
		};

		list.append(viewMore);
	}

	function querySound(page) {
		apiCall('api/sound?title=' + query + '&page=' + page, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error']) {
					console.log(parsedData['error']);
				} else {
					const loader = soundsResult.parentNode.getElementsByClassName('loader')[0];
					if (loader != undefined) loader.remove();

					parsedData.database.map((sound) => {
						(async () => {
							const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

							soundsResult.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					parsedData.youtube.map((sound) => {
						(async () => {
							const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

							soundsResult.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					if (parsedData.database.length === parsedData.page.per_page || parsedData.youtube.length === parsedData.page.per_page) {
						viewMore(soundsResult.parentNode, querySound, page + 1);
					}
				}
			}
		});
	}

	function queryPlaylist(page) {
		apiCall('api/playlist?title=' + query + '&page=' + page, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error']) {
					console.log(parsedData['error']);
				} else {
					const loader = playlistsResult.parentNode.getElementsByClassName('loader')[0];
					if (loader != undefined) loader.remove();

					parsedData.database.map((playlist) => {
						(async () => {
							const card = new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, false);

							playlistsResult.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					parsedData.youtube.map((playlist) => {
						(async () => {
							const card = new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, false);

							playlistsResult.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					if (parsedData.database.length === parsedData.page.per_page || parsedData.youtube.length === parsedData.page.per_page) {
						viewMore(playlistsResult.parentNode, queryPlaylist, page + 1);
					}
				}
			}
		});
	}

	function queryArtist(page) {
		apiCall('api/artist?pseudo=' + query + '&page=' + page, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error']) {
					console.log(parsedData['error']);
				} else {
					const loader = artistsResult.parentNode.getElementsByClassName('loader')[0];
					if (loader != undefined) loader.remove();

					parsedData.database.map((artist) => {
						(async () => {
							const card = new ArtistCard(artist.id, artist.image, artist.pseudo);

							artistsResult.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					parsedData.youtube.map((artist) => {
						(async () => {
							const card = new ArtistCard(artist.id, artist.image, artist.pseudo);

							artistsResult.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					if (parsedData.database.length === parsedData.page.per_page || parsedData.youtube.length === parsedData.page.per_page) {
						viewMore(artistsResult.parentNode, queryArtist, page + 1);
					}
				}
			}
		});
	}

	querySound(1);
	queryPlaylist(1);
	queryArtist(1);
})();

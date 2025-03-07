(async () => {
	const contentContainer = document.getElementById('content-container');

	async function mostListened() {
		await apiCall('api/like?type=mostListened', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'most-listened',
							'Musiques les plus écoutées',
							parsedData.map((sound) => {
								return new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.makeList();
					}
				}
			}
		});
	}

	async function leastListened() {
		await apiCall('api/like?type=leastListened', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'least-listened',
							'Les musiques émergentes',
							parsedData.map((sound) => {
								return new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.makeList();
					}
				}
			}
		});
	}

	async function mostLikedSound() {
		await apiCall('api/like?type=mostLikedSound', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'most-liked-sound',
							'Musiques les plus aimées',
							parsedData.map((sound) => {
								return new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.makeList();
					}
				}
			}
		});
	}

	async function mostLikedPlaylist() {
		await apiCall('api/like?type=mostLikedPlaylist', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'most-liked-playlist',
							'Playlists les plus aimées',
							parsedData.map((playlist) => {
								return new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.makeList();
					}
				}
			}
		});
	}

	async function leastArtist() {
		await apiCall('api/like?type=leastArtist', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'least-artist',
							'Les artistes émergents',
							parsedData.map((artist) => {
								return new ArtistCard(artist.id, artist.image, artist.pseudo, true);
							}),
						);

						contentContainer.append(await list.getSkeleton());
						list.makeList();
					}
				}
			}
		});
	}

	await mostListened();
	await leastListened();
	await mostLikedSound();
	await mostLikedPlaylist();
	await leastArtist();
})();

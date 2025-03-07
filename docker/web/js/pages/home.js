(async () => {
	const contentContainer = document.getElementById('content-container');

	async function recent() {
		let recentFormData = new FormData();

		recentFormData.append('type', 'recent');
		recentFormData.append('token', token);

		await apiCall('api/user/activity', recentFormData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'play-back',
							'Réécouter',
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

	async function last() {
		let recentFormData = new FormData();

		recentFormData.append('type', 'last');
		recentFormData.append('token', token);

		await apiCall('api/user/activity', recentFormData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'play-favorite',
							'Favoris à redécouvrir',
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

	async function lastLikedSound() {
		let formData = new FormData();

		formData.append('type', 'sound');
		formData.append('token', token);

		await apiCall('api/user/activity', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'last-liked-sounds',
							'Vos dernières musiques aimées',
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

	async function lastLikedPlaylist() {
		let formData = new FormData();

		formData.append('type', 'playlist');
		formData.append('token', token);

		await apiCall('api/user/activity', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'last-liked-playlist',
							'Vos dernières playlists aimées',
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

	async function lastLikedArtist() {
		let formData = new FormData();

		formData.append('type', 'artist');
		formData.append('token', token);

		await apiCall('api/user/activity', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) return;

						const list = new List(
							'last-liked-artist',
							'Vos derniers artistes aimés',
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

	await recent();
	await last();
	await lastLikedSound();
	await lastLikedPlaylist();
	await lastLikedArtist();
})();

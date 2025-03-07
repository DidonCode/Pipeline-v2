(async () => {
	const exposureImage = document.getElementById('exposure-image');
	const exposurePseudo = document.getElementById('exposure-pseudo');
	const exposureBanner = document.getElementById('exposure-banner');

	const exposureLike = document.getElementById('exposure-like');
	const exposureUnlike = document.getElementById('exposure-unlike');
	const bannerEditImport = document.getElementById('banner-editInp');
	const bannerEditBtn = document.getElementById('banner-edit');

	const sounds = document.getElementById('sounds');
	const playlists = document.getElementById('playlists');
	const likedSound = document.getElementById('liked-sounds');
	const likedPlaylist = document.getElementById('liked-playlists');
	const contentContainer = document.getElementById('content-container');

	const url = window.location.search;
	const urlParams = new URLSearchParams(url);
	const id = urlParams.get('id');

	bannerEditBtn.addEventListener('click', function (e) {
		e.stopPropagation();
		bannerEditImport.click();
	});

	bannerEditImport.onclick = function (e) {
		e.stopPropagation();
	};

	if (user && id == user['id']) {
		exposureLike.remove();
		exposureUnlike.remove();
	} else {
		bannerEditBtn.remove();
	}

	let artist = null;

	await apiCall('api/artist?id=' + id, null, async function (data) {
		if (data != '') {
			artist = JSON.parse(data);
			if (artist['error'] != undefined) {
				routeError(artist['error']);
				return;
			} else {
				document.title += ' - ' + artist.pseudo;

				exposureImage.src = artist.image;
				exposurePseudo.innerText = artist.pseudo;
				exposureBanner.src = artist.banner;
			}
		}
	});

	bannerEditImport.addEventListener('change', function () {
		if (!bannerEditImport.files[0]) return;

		exposureBanner.src = '';

		let formData = new FormData();
		formData.append('banner', bannerEditImport.files[0]);
		formData.append('token', token);

		apiCall('api/user/account', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);

					exposureBanner.src = user.banner;
				} else {
					user = parsedData;

					exposureBanner.src = parsedData.banner;
				}
			}
		});
	});

	function like(action) {
		if (action == 2) {
			if (!sessionExist()) return;
		} else {
			if (!sessionExist()) sessionDestroy();
		}

		let formData = new FormData();

		formData.append('artist', id);
		formData.append('action', action);
		formData.append('token', token);

		apiCall('api/user/like', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);
				} else {
					if (parsedData) {
						exposureLike.setAttribute('hidden', '');
						exposureUnlike.removeAttribute('hidden');
					} else {
						exposureLike.removeAttribute('hidden');
						exposureUnlike.setAttribute('hidden', '');
					}
				}
			}
		});
	}

	like(2);

	exposureLike.onclick = function () {
		like(3);
	};

	exposureUnlike.onclick = function () {
		like(1);
	};

	if (artist.public == 0 || isNaN(artist.id)) {
		likedPlaylist.parentNode.remove();
		likedSound.parentNode.remove();
		const loader = document.getElementsByClassName('loader');
		if (loader.length > 0) {
			loader[loader.length - 1].remove();
			loader[loader.length - 1].remove();
		}
	}

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

	async function listSound(page) {
		await apiCall('api/sound?artist=' + id + '&page=' + page, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);

					return;
				} else {
					const loader = document.getElementsByClassName('loader');
					if (loader.length > 0) loader[0].remove();

					if (parsedData.sounds.length == 0 && page == 1) {
						sounds.parentNode.remove();
						return;
					}

					sounds.parentNode.removeAttribute('hidden');

					parsedData.sounds.forEach((sound) => {
						(async () => {
							const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

							sounds.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					if (parsedData.sounds.length == parsedData.page.per_page) {
						viewMore(sounds.parentNode, listSound, page + 1);
					}
				}
			}
		});
	}

	async function listPlaylist(page) {
		await apiCall('api/playlist?owner=' + id + '&page=' + page, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);
					return;
				} else {
					const loader = document.getElementsByClassName('loader');
					if (loader.length > 0) loader[0].remove();

					if (parsedData.playlists.length == 0) {
						playlists.parentNode.remove();
						return;
					}

					playlists.parentNode.removeAttribute('hidden');

					parsedData.playlists.forEach((playlist) => {
						(async () => {
							const card = new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, false);

							playlists.append(await card.getSkeleton());
							card.makeCard();
						})();
					});

					if (parsedData.playlists.length == parsedData.page.per_page) {
						viewMore(playlists.parentNode, listPlaylist, page + 1);
					}
				}
			}
		});
	}

	async function listLikedSound() {
		await apiCall('api/like?artist=' + artist.id + '&type=sound', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);
					return;
				} else {
					const loader = document.getElementsByClassName('loader');
					if (loader.length > 0) loader[0].remove();

					if (parsedData.length == 0) {
						likedSound.parentNode.remove();
						return;
					}

					likedSound.parentNode.removeAttribute('hidden');

					parsedData.map((sound) => {
						(async () => {
							const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

							likedSound.append(await card.getSkeleton());
							card.makeCard();
						})();
					});
				}
			}
		});
	}

	async function listLikedPlaylist() {
		await apiCall('api/like?artist=' + artist.id + '&type=playlist', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);
					return;
				} else {
					const loader = document.getElementsByClassName('loader');
					if (loader.length > 0) loader[0].remove();

					if (parsedData.length == 0) {
						likedPlaylist.parentNode.remove();
						return;
					}

					likedPlaylist.parentNode.removeAttribute('hidden');

					parsedData.map((playlist) => {
						(async () => {
							const card = new PlaylistCard(playlist.id, playlist.image, playlist.title, playlist.description, playlist.owner, false);

							likedPlaylist.append(await card.getSkeleton());
							card.makeCard();
						})();
					});
				}
			}
		});
	}

	async function listLikedArtist() {
		await apiCall('api/like?artist=' + artist.id + '&type=artist', null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);
					return;
				} else {
					if (parsedData.length == 0) return;

					const list = new List(
						'liked-artist',
						'Artistes Aimés',
						parsedData.map((artist) => {
							return new ArtistCard(artist.id, artist.image, artist.pseudo, true);
						}),
					);

					contentContainer.append(await list.getSkeleton());
					list.makeList();
				}
			}
		});
	}

	await listSound(1);
	await listPlaylist(1);

	if (artist.public == 1 && !isNaN(artist.id)) {
		await listLikedSound();
		await listLikedPlaylist();
		await listLikedArtist();
	}
})();

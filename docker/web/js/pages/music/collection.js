(async () => {
	let collectionData = null;

	const collection = document.getElementById('collection');

	const collectionPlus = document.getElementById('collection-plus');
	const collectionPlay = document.getElementById('collection-play');
	const collectionDelete = document.getElementById('collection-delete');
	const collectionImageEdit = document.getElementById('collection-image-edit');
	const collectionEdit = document.getElementById('collection-edit');
	const collectionPlayRandom = document.getElementById('collection-play-random');
	let collectionLike = document.getElementById('collection-like');
	let collectionUnlike = document.getElementById('collection-unlike');
	const collectionPartage = document.getElementById('collection-partage');
	const collectionClone = document.getElementById('collection-clone');
	const collectionExit = document.getElementById('collection-exit');

	const collectionPopup = document.getElementById('collection-action-popup');
	const collectionActions = document.getElementById('collection-actions');

	const collectionImage = document.getElementById('collection-image');
	const collectionTitle = document.getElementById('collection-title');
	const collectionOwner = document.getElementById('collection-owner');
	const collectionOwnerImage = document.getElementById('collection-owner-image');
	const collectionDescription = document.getElementById('collection-description');
	const collectionSounds = document.getElementById('collection-sounds');

	const collectionImageUpload = document.getElementById('collection-image-upload');

	const url = window.location.search;
	const urlParams = new URLSearchParams(url);
	const id = urlParams.get('id');

	collectionPlus.onclick = function (e) {
		e.stopPropagation();
		collectionPopup.hasAttribute('hidden') ? collectionPopup.removeAttribute('hidden') : collectionPopup.setAttribute('hidden', '');
	};

	window.addEventListener('click', function () {
		collectionPopup.setAttribute('hidden', '');
	});

	collectionDelete.onclick = function (event) {
		event.stopPropagation();

		let formData = new FormData();

		formData.append('id', id);
		formData.append('action', 1);
		formData.append('token', token);

		apiCall('api/user/playlist', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						loadPlaylistSideBar();

						event.target.href = '/web/home';
						route(event);
					}
				}
			}
		});
	};

	collectionClone.onclick = function (event) {
		function clone(title, description, visibility) {
			let formData = new FormData();

			formData.append('title', title);
			formData.append('description', description);
			formData.append('public', visibility);
			formData.append('playlist', id);
			formData.append('token', token);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							routeError(parsedData['error']);
							return;
						} else {
							if (parsedData) {
								event.target.href = '/web/collection?id=' + parsedData.id;
								route(event);
							}
						}
					}
				}
			});
		}

		makeCreatePlaylistPopup(clone);
	};

	function setEmpty() {
		const text = document.createElement('h5');
		text.classList.add('text-center', 'boolean-text');
		text.innerText = 'Cette playlist ne contient pas encore de musique';

		const button = document.createElement('button');
		button.classList.add('btn', 'action', 'mx-auto', 'd-block');

		button.onclick = function (e) {
			e.stopPropagation();
			search.focus();
		};

		const icon = document.createElement('i');
		icon.classList.add('fa-solid', 'fa-plus');

		button.append(icon);

		collectionSounds.innerHTML = '';
		collectionSounds.append(text);
		collectionSounds.append(button);
	}

	function setErrorAccess() {
		const text = document.createElement('h3');
		text.classList.add('text-center', 'contrast-text');
		text.innerText = "Vous n'avez pas accès à cette playlist";

		collection.innerHTML = '';
		collection.style.alignContent = 'center';
		collection.appendChild(text);
	}

	function setCollection(data, permission) {
		collectionData = data;

		document.title += ' - ' + data.title;
		collectionImage.src = data.image;
		collectionTitle.innerText = data.title;
		collectionOwner.href = '/web/exposure?id=' + data.owner;

		apiCall('api/artist?id=' + data.owner, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						collectionOwnerImage.src = parsedData.image;
						collectionOwner.innerText = parsedData.pseudo;
					}
				}
			}
		});

		collectionExit.onclick = function (event) {
			let formData = new FormData();

			formData.append('token', token);
			formData.append('owner', data.owner);
			formData.append('id', id);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							console.log(parsedData['error']);
						} else {
							if (parsedData) {
								clear();
								window.history.pushState({}, '', '/web/home');
								route(event);
							}
						}
					}
				}
			});
		};

		collectionDescription.textContent = data.description;

		if (!sessionExist()) {
			collectionClone.remove();
			collectionExit.remove();
		}

		if (permission) {
			collectionDelete.style.display = '';
			collectionEdit.style.display = '';
			collectionImageEdit.style.display = '';
			collectionPartage.style.display = 'flex';
			collectionExit.remove();
		}

		if (!permission) {
			collectionDelete.remove();
			collectionEdit.remove();
			collectionImageEdit.remove();
			collectionPartage.remove();

			if (collectionLike != null) {
				collectionLike.classList.add('btn', 'rounded1');

				for (let child of collectionLike.childNodes) {
					if (child.nodeType === Node.TEXT_NODE) {
						child.remove();
					}
				}

				collectionActions.insertBefore(collectionLike, collectionActions.firstChild);
			}

			if (collectionUnlike != null) {
				collectionUnlike.classList.add('btn', 'rounded1');

				for (let child of collectionUnlike.childNodes) {
					if (child.nodeType === Node.TEXT_NODE) {
						child.remove();
					}
				}

				collectionActions.insertBefore(collectionUnlike, collectionActions.firstChild);
			}

			if (collectionUnlike == null && collectionLike == null) {
				collectionActions.insertBefore(collectionPopup.firstChild, collectionActions.firstChild);
			} else {
			}
		}

		if (collectionPopup.children.length > 2 && collectionLike == null && collectionUnlike == null && !permission) {
			const child = collectionPopup.children[0];
			child.classList.add('btn', 'rounded1');
			const icon = child.children[0];
			child.innerText = '';
			child.append(icon);
			collectionActions.insertBefore(child, collectionActions.firstChild);
		} else if (collectionPopup.children.length == 2 && collectionLike == null && collectionUnlike == null) {
			for (let i = 0; i < 2; i++) {
				const child = collectionPopup.children[0];
				child.classList.add('btn', 'rounded1');
				const icon = child.children[0];
				child.innerText = '';
				child.append(icon);
				i == 0 ? collectionActions.insertBefore(child, collectionActions.firstChild) : collectionActions.append(child);
			}
			collectionPlus.remove();
		} else if (collectionPopup.children.length == 1) {
			collectionPlus.remove();
			const child = collectionPopup.children[0];
			child.classList.add('btn', 'rounded1');
			const icon = child.children[0];
			child.innerText = '';
			child.append(icon);
			collectionActions.append(child);
		}
	}

	function removeSoundPlaylist(id) {
		sound = document.getElementById('sound-' + id);
		if (sound !== null) sound.remove();
	}

	async function addSoundPlaylist(sound, title, type, artist, image, permission) {
		const card = new SoundCard(sound, image, title, type, artist, false);

		collectionSounds.append(await card.getSkeleton());

		if (permission) {
			const remove = document.createElement('button');
			remove.classList.add('sound-card-remove', 'my-auto');

			const removeIcon = document.createElement('i');
			removeIcon.classList.add('fa-solid', 'fa-xmark', 'contrast-text');

			remove.onclick = function (e) {
				e.preventDefault();
				e.stopPropagation();

				let formData = new FormData();

				formData.append('id', id);
				formData.append('sound', sound);
				formData.append('action', 1);
				formData.append('token', token);

				apiCall('api/user/playlist', formData, async function (data) {
					if (data != '') {
						const parsedData = JSON.parse(data);

						if (parsedData != null) {
							if (parsedData['error'] != undefined) {
								console.log(parsedData['error']);
							} else {
								if (parsedData) removeSoundPlaylist(sound);
							}
						}
					}
				});
			};

			remove.append(removeIcon);
			card.soundCardPlus.insertBefore(remove, card.soundCardPlus.firstChild);
		}

		card.makeCard();
	}

	function loadPrivateCollectionSounds(id, permission) {
		let formData = new FormData();

		formData.append('playlist', id);
		formData.append('token', token);

		apiCall('api/user/sound', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) {
							setEmpty();
							return;
						} else {
							collectionPlay.addEventListener('click', function (event) {
								clear();

								sessionStorage.setItem('player-index', 0);
								sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));

								window.history.pushState({}, '', '/web/play?list=' + id);
								route(event);
							});

							collectionPlayRandom.addEventListener('click', function (event) {
								let random = Math.floor(Math.random() * parsedData.length);
								clear();

								sessionStorage.setItem('player-index', random);
								sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));

								window.history.pushState({}, '', '/web/play?list=' + id);
								route(event);
							});

							collectionSounds.innerHTML = '';
							collectionSounds.style.alignContent = '';

							parsedData.forEach(async (sound) => {
								await addSoundPlaylist(sound.id, sound.title, sound.type, sound.artist, sound.image, permission);
							});
						}
					}
				}
			}
		});
	}

	function loadPublicCollectionSounds(id) {
		apiCall('api/sound?playlist=' + id, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.length == 0) {
							setEmpty();
							return;
						} else {
							collectionPlay.addEventListener('click', function (event) {
								clear();

								sessionStorage.setItem('player-index', 0);
								sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));

								window.history.pushState({}, '', '/web/play?list=' + id);
								route(event);
							});

							collectionPlayRandom.addEventListener('click', function (event) {
								clear();

								let random = Math.floor(Math.random() * parsedData.length);
								sessionStorage.setItem('player-index', random);
								sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));

								window.history.pushState({}, '', '/web/play?list=' + id);
								route(event);
							});

							collectionSounds.innerHTML = '';
							collectionSounds.style.alignContent = '';

							parsedData.forEach((sound) => {
								addSoundPlaylist(sound.id, sound.title, sound.type, sound.artist, sound.image, false);
							});
						}
					}
				}
			}
		});
	}

	async function loadPrivateCollection(id) {
		if (!sessionExist()) {
			loadPublicCollection(id);
			return;
		}

		let formData = new FormData();

		formData.append('id', id);
		formData.append('action', 2);
		formData.append('token', token);

		await apiCall('api/user/playlist', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						if (parsedData.permission.modify !== undefined || parsedData.permission.owner !== undefined) {
							collectionLike.remove();
							collectionLike = null;
							collectionUnlike.remove();
							collectionUnlike = null;
						}
						setCollection(parsedData, parsedData.permission.owner !== undefined);
						loadPrivateCollectionSounds(id, (parsedData.permission.modify !== undefined && parsedData.permission.modify === 1) || parsedData.permission.owner !== undefined);
					}
				} else {
					setErrorAccess();
				}
			}
		});
	}

	async function loadPublicCollection(id) {
		await apiCall('api/playlist?id=' + id, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						setCollection(parsedData, false);
						loadPublicCollectionSounds(id);
					}
				}
			}
		});
	}

	async function loadLikedCollection() {
		if (!sessionExist()) {
			sessionDestroy();
			return;
		}

		let formData = new FormData();

		formData.append('id', 'liked');
		formData.append('action', 2);
		formData.append('token', token);

		await apiCall('api/user/playlist', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						collectionLike.remove();
						collectionLike = null;
						collectionUnlike.remove();
						collectionUnlike = null;
						collectionExit.remove();
						setCollection(parsedData, false);
					}
				}
			}
		});

		formData = new FormData();

		formData.append('type', 'sound');
		formData.append('token', token);

		apiCall('api/user/like', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						collectionSounds.innerHTML = '';

						if (parsedData.length == 0) {
							const text = document.createElement('h5');
							text.classList.add('text-center', 'boolean-text');
							text.innerText = 'Aimé des musiques pour les voir apparaître dans cette playlist';
							return;
						}

						collectionSounds.style.alignContent = '';

						parsedData.forEach(async (sound) => {
							const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

							collectionSounds.append(await card.getSkeleton());
							card.makeCard();
						});

						collectionPlay.addEventListener('click', function (event) {
							clear();

							sessionStorage.setItem('player-index', 0);
							sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));

							window.history.pushState({}, '', '/web/play?list=liked');
							route(event);
						});

						collectionPlayRandom.addEventListener('click', function (event) {
							let random = Math.floor(Math.random() * parsedData.length);
							clear();

							sessionStorage.setItem('player-index', random);
							sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));

							window.history.pushState({}, '', '/web/play?list=liked');
							route(event);
						});
					}
				}
			}
		});
	}

	if (id == 'liked') {
		loadLikedCollection();
	} else {
		isNaN(parseInt(id)) ? await loadPublicCollection(id) : await loadPrivateCollection(id);
	}

	collectionImageEdit.onclick = function (e) {
		e.stopPropagation();
		collectionImageUpload.click();
	};

	collectionImageUpload.onclick = function (e) {
		e.stopPropagation();
	};

	collectionImageUpload.addEventListener('change', function () {
		let formData = new FormData();

		if (!collectionImageUpload.files[0]) return;

		formData.append('id', id);
		formData.append('image', collectionImageUpload.files[0]);
		formData.append('token', token);

		collectionImage.src = '/storage/playlist/default.png';

		apiCall('api/user/playlist', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
						return;
					} else {
						loadPlaylistSideBar();
						setCollection(parsedData, parsedData.permission.owner !== undefined);
					}
				}
			}
		});
	});

	collectionEdit.onclick = function () {
		function edit(title, description, visibility) {
			let formData = new FormData();

			formData.append('token', token);
			formData.append('title', title);
			formData.append('description', description);
			formData.append('public', visibility);
			formData.append('id', id);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							routeError(parsedData['error']);
							return;
						} else {
							setCollection(parsedData, parsedData.permission.owner !== undefined);
						}
					}
				}
			});
		}

		makeModifyPlaylistPopup(collectionData.title, collectionData.description, collectionData.public, edit);
	};

	collectionPartage.onclick = function () {
		function addCollaborator(user) {
			let formData = new FormData();

			formData.append('token', token);
			formData.append('id', id);
			formData.append('action', 2);
			formData.append('collaborator', user);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							console.log(parsedData['error']);
						}
					}
				}
			});
		}

		makeCollaboratorPopup(id, addCollaborator);
	};

	if (collectionLike != null && collectionUnlike != null) {
		collectionLike.onclick = function () {
			like(3);
		};

		collectionUnlike.onclick = function () {
			like(1);
		};

		function like(action) {
			if (action == 2) {
				if (!sessionExist()) return;
			} else {
				if (!sessionExist()) {
					sessionDestroy();
					return;
				}
			}

			let formData = new FormData();

			formData.append('token', token);
			formData.append('action', action);
			formData.append('playlist', id);

			apiCall('api/user/like', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							console.log(parsedData['error']);
						} else {
							if ((action == 1 && parsedData) || (action == 2 && !parsedData)) {
								collectionLike.removeAttribute('hidden');
								collectionUnlike.setAttribute('hidden', '');
							}

							if ((action == 3 && parsedData) || (action == 2 && parsedData)) {
								collectionLike.setAttribute('hidden', '');
								collectionUnlike.removeAttribute('hidden');
							}
						}
					}
				}
			});
		}

		if (id !== 'liked') like(2);
	}
})();

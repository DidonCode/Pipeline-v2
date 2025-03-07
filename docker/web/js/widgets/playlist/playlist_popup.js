const playlistPopup = document.getElementById('playlist-popup');
const playlistPopupTitle = document.getElementById('playlist-popup-title');
const playlistPopupClose = document.getElementById('playlist-popup-close');

const playlistTitle = document.getElementById('playlist-title');
const playlistDescription = document.getElementById('playlist-description');
const playlistVisibility = document.getElementById('playlist-visibility');

const playlistSubmit = document.getElementById('playlist-submit');
const playlistForm = document.getElementById('playlist-form');

function makeCreatePlaylistPopup(callback) {
	playlistPopup.style.display = 'block';
	playlistPopupTitle.innerText = 'Créer une playlist';
	playlistSubmit.innerText = 'Créer la playlist';

	playlistTitle.value = '';
	playlistTitle.classList.remove('error');
	playlistDescription.value = '';
	for (let option of playlistVisibility.options) {
		option.selected = option.value === '0';
	}

	playlistPopupClose.onclick = function () {
		playlistPopup.style.display = 'none';
	};

	playlistForm.onsubmit = function (e) {
		e.preventDefault();

		if (!playlistTitle.value) {
			playlistTitle.classList.add('error');
			return;
		} else {
			playlistTitle.classList.remove('error');
		}

		playlistPopup.style.display = 'none';
		callback(playlistTitle.value, playlistDescription.value, playlistVisibility.options[playlistVisibility.selectedIndex].value);
	};
}

function makeModifyPlaylistPopup(title, description, visibility, callback) {
	playlistPopup.style.display = 'block';
	playlistPopupTitle.innerText = 'Modifier votre playlist';
	playlistSubmit.innerText = 'Enregister les modifications';

	playlistTitle.value = title;
	playlistTitle.classList.remove('error');
	playlistDescription.value = description;
	for (let option of playlistVisibility.options) {
		option.selected = option.value == visibility.toString();
	}

	playlistPopupClose.onclick = function () {
		playlistPopup.style.display = 'none';
	};

	playlistForm.onsubmit = function (e) {
		e.preventDefault();

		if (!playlistTitle.value) {
			playlistTitle.classList.add('error');
			return;
		} else {
			playlistTitle.classList.remove('error');
		}

		playlistPopup.style.display = 'none';
		callback(playlistTitle.value, playlistDescription.value, playlistVisibility.options[playlistVisibility.selectedIndex].value);
	};
}

const collaboratorPopup = document.getElementById('collaborator-popup');
const collaboratorPopupClose = document.getElementById('collaborator-popup-close');

const collaboratorPseudo = document.getElementById('collaborator-pseudo');
const collaboratorSearchResult = document.getElementById('collaborator-search-result');
const collaboratorResult = document.getElementById('collaborator-result');

function makeCollaboratorPopup(id, callback) {
	let typingTimeout;

	collaboratorSearchResult.innerHTML = '';
	collaboratorResult.innerHTML = '';
	collaboratorPseudo.value = '';

	collaboratorPopup.style.display = 'block';

	collaboratorPopupClose.onclick = function () {
		collaboratorPopup.style.display = 'none';
	};

	collaboratorPseudo.addEventListener('keydown', function () {
		clearTimeout(typingTimeout);

		typingTimeout = setTimeout(collaboratorSearch, 1000);
	});

	function collaboratorCard(playlist, id, pseudo, image, modify) {
		const card = document.createElement('div');
		card.classList.add('collaborator-card');

		const collaboratorImage = document.createElement('img');
		collaboratorImage.classList.add('collaborator-card-image');
		collaboratorImage.src = image;

		const detail = document.createElement('div');
		detail.classList.add('collaborator-card-detail');

		const collaboratorPseudo = document.createElement('p');
		collaboratorPseudo.innerText = pseudo;
		collaboratorPseudo.classList.add('contrast-text', 'collaborator-card-title');
		collaboratorPseudo.onclick = function (e) {
			collaboratorPopup.style.display = 'none';
			window.history.pushState({}, '', '/web/exposure?id=' + id);
			route(event);
		};

		detail.append(collaboratorPseudo);

		const plus = document.createElement('div');
		plus.classList.add('collaborator-card-plus');

		const labelCheck = document.createElement('label');
		labelCheck.classList.add('checkWrap');
		const modifyInput = document.createElement('input');
		const checkmark = document.createElement('span');
		checkmark.classList.add('checkmark');
		modifyInput.type = 'checkbox';
		modifyInput.checked = modify;
		modifyInput.setAttribute('hidden', true);
		labelCheck.append(modifyInput);
		labelCheck.append(checkmark);

		modifyInput.onclick = function (e) {
			e.stopPropagation();
		};

		modifyInput.onchange = function () {
			let formData = new FormData();

			formData.append('id', playlist);
			formData.append('collaborator', id);
			formData.append('modify', modifyInput.checked === true ? 1 : 0);
			formData.append('token', token);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							console.log(parsedData['error']);
						} else {
							modifyInput.checked = parsedData;
						}
					}
				}
			});
		};

		const remove = document.createElement('button');
		remove.classList.add('collaborator-card-plus');
		remove.onclick = function () {
			let formData = new FormData();

			formData.append('id', playlist);
			formData.append('collaborator', id);
			formData.append('action', 1);
			formData.append('token', token);

			apiCall('api/user/playlist', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							console.log(parsedData['error']);
						} else {
							if (parsedData) card.remove();
						}
					}
				}
			});
		};

		const removeIcon = document.createElement('i');
		removeIcon.classList.add('fa-solid', 'fa-xmark', 'contrast-text');
		remove.append(removeIcon);

		plus.append(labelCheck);
		labelCheck.append(remove);

		card.append(collaboratorImage);
		card.append(detail);
		card.append(plus);

		collaboratorResult.append(card);
	}

	function collaboratorSearch() {
		if (collaboratorPseudo.value == '') return;

		collaboratorSearchResult.innerHTML = '<div class="loader m-5 mx-auto"></div>';

		apiCall('api/member?pseudo=' + collaboratorPseudo.value, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						console.log(parsedData['error']);
					} else {
						if (parsedData.length == 0) {
							collaboratorSearchResult.innerHTML = '<p class="contrast-text m-3 text-center">Utilisateur introuvable.</p>';
							return;
						}

						collaboratorSearchResult.innerHTML = '';

						parsedData.forEach((user) => {
							const card = document.createElement('div');
							card.classList.add('collaborator-card');
							card.onclick = function () {
								callback(user.id);
								collaboratorCard(id, user.id, user.pseudo, user.image);
								card.remove();
							};

							const collaboratorImage = document.createElement('img');
							collaboratorImage.classList.add('collaborator-card-image');
							collaboratorImage.src = user.image;

							const detail = document.createElement('div');
							detail.classList.add('collaborator-card-detail');

							const collaboratorPseudo = document.createElement('p');
							collaboratorPseudo.innerText = user.pseudo;
							collaboratorPseudo.classList.add('contrast-text', 'collaborator-card-title');

							detail.append(collaboratorPseudo);

							card.append(collaboratorImage);
							card.append(detail);

							collaboratorSearchResult.append(card);
						});
					}
				}
			}
		});
	}

	let formData = new FormData();

	formData.append('id', id);
	formData.append('token', token);

	apiCall('api/user/playlist', formData, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData != null) {
				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);
				} else {
					parsedData.forEach((collaborator) => {
						collaboratorCard(id, collaborator.user.id, collaborator.user.pseudo, collaborator.user.image, collaborator.modify);
					});
				}
			}
		}
	});
}

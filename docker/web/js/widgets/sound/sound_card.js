class SoundCard {
	constructor(id, image, title, type, artist, column) {
		this.id = id;
		this.image = image;
		this.title = title;
		this.type = type;
		this.artist = artist;
		this.column = column;
	}

	async loadTemplate() {
		const parser = new DOMParser();

		let html = await fetch('/web/widgets/sound/sound_card.php').then((data) => data.text());
		html = parser.parseFromString(html, 'text/html');

		this.soundCard = html.getElementsByClassName('sound-card')[0];
		this.soundCardImage = html.getElementsByClassName('sound-card-image')[0];
		this.soundCardImageContainer = html.getElementsByClassName('sound-card-image-container')[0];
		this.soundCardImageSkeleton = html.getElementsByClassName('sound-card-image-skeleton')[0];
		this.soundCardTitle = html.getElementsByClassName('sound-card-title')[0];
		this.soundCardDescription = html.getElementsByClassName('sound-card-description')[0];
		this.soundCardBadge = html.getElementsByClassName('sound-card-badge')[0];

		this.soundCardPlus = html.getElementsByClassName('sound-card-plus')[0];
		this.soundCardPlusButton = html.getElementsByClassName('sound-card-plus-button')[0];
		this.soundCardPlusPopup = html.getElementsByClassName('sound-card-plus-popup')[0];

		this.soundCardLike = html.getElementsByClassName('sound-card-like')[0];
		this.soundCardUnlike = html.getElementsByClassName('sound-card-unlike')[0];
		this.soundCardPlaylist = html.getElementsByClassName('sound-card-playlist')[0];

		if (this.column) {
			const div = document.createElement('div');
			div.classList.add('sound-card-plus-column');

			div.append(this.soundCardImage);
			div.append(this.soundCardImageSkeleton);
			div.append(this.soundCardPlus);

			this.soundCardImageContainer.remove();

			this.soundCard.insertBefore(div, this.soundCard.firstChild);

			this.soundCard.classList.add('sound-card-colunm');
		}

		if (isNaN(parseInt(this.id)) || parseInt(this.id) != this.id) this.soundCardBadge.remove();
	}

	async makeCard(list = null, finishCallback = null) {
		let loadTimeout = setTimeout(() => {
			this.soundCard.remove();
		}, 10000);

		this.soundCard.id = 'sound-' + this.id;
		this.soundCard.href = '/web/play?id=' + this.id;
		this.soundCard.onclick = function () {
			clear();
			route(event);
		};

		this.soundCardImage.src = this.image;
		this.soundCardTitle.innerText = this.title;
		this.soundCardDescription.innerText = this.type === 0 ? 'Titre' : 'Video';

		this.soundCardPlus.onclick = function (e) {
			e.preventDefault();
			e.stopPropagation();
		};

		this.soundCardPlaylist.onclick = (e) => {
			e.preventDefault();
			e.stopPropagation();

			if (!sessionExist()) {
				sessionDestroy();
				return;
			}

			const id = this.id;

			function addSound(playlist) {
				let formData = new FormData();

				formData.append('id', playlist);
				formData.append('action', 2);
				formData.append('sound', id);
				formData.append('token', token);

				apiCall('api/user/playlist', formData, async function (data) {
					if (data != '') {
						const parsedData = JSON.parse(data);

						if (parsedData['error'] != undefined) {
							console.log(parsedData['error']);
						}
					}
				});
			}

			makeSoundSetting(addSound);
		};

		this.like(null, 2);

		this.soundCardLike.onclick = (e) => this.like(e, 3);
		this.soundCardUnlike.onclick = (e) => this.like(e, 1);

		await apiCall('api/artist?id=' + this.artist, null, async (data) => {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);
				} else {
					this.soundCardDescription.innerText += ' • ';

					const soundCardArtist = document.createElement('span');
					soundCardArtist.classList.add('sound-card-description-artist');
					soundCardArtist.innerText = parsedData.pseudo;
					soundCardArtist.onclick = function (event) {
						event.stopPropagation();

						window.history.pushState({}, '', '/web/exposure?id=' + parsedData.id);
						route(event);
					};

					this.soundCardDescription.append(soundCardArtist);
				}
			}
		});

		this.soundCardBadge.removeAttribute('hidden');
		this.soundCard.classList.remove('sound-card-skeleton');

		clearTimeout(loadTimeout);
		if (list !== null || finishCallback !== null) finishCallback(list, this);
	}

	async getSkeleton() {
		await this.loadTemplate();

		this.soundCard.classList.add('sound-card-skeleton');

		return this.soundCard;
	}

	popup(e, close) {
		e.preventDefault();
		e.stopPropagation();

		close === false ? this.soundCardPlusPopup.removeAttribute('hidden') : this.soundCardPlusPopup.setAttribute('hidden', '');
	}

	like(e, action) {
		if (e !== null) {
			e.preventDefault();
			e.stopPropagation();
		}

		if (action == 2) {
			if (!sessionExist()) return;
		} else {
			if (!sessionExist()) {
				sessionDestroy();
				return;
			}
		}

		let formData = new FormData();

		formData.append('sound', this.id);
		formData.append('action', action);
		formData.append('token', token);

		apiCall('api/user/like', formData, async (data) => {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);
				} else {
					if ((action == 1 && parsedData) || (action == 2 && !parsedData)) {
						this.soundCardLike.removeAttribute('hidden');
						this.soundCardUnlike.setAttribute('hidden', '');
					}

					if ((action == 3 && parsedData) || (action == 2 && parsedData)) {
						this.soundCardLike.setAttribute('hidden', '');
						this.soundCardUnlike.removeAttribute('hidden');
					}
				}
			}
		});
	}
}

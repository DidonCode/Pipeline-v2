class PlaylistCard {
	constructor(id, image, title, description, owner, column) {
		this.id = id;
		this.image = image;
		this.title = title;
		this.description = description;
		this.owner = owner;
		this.column = column;
	}

	async loadTemplate() {
		const parser = new DOMParser();

		let html = await fetch('/web/widgets/playlist/playlist_card.php').then((data) => data.text());
		html = parser.parseFromString(html, 'text/html');

		this.playlistCard = html.getElementsByClassName('playlist-card')[0];
		this.playlistCardImage = html.getElementsByClassName('playlist-card-image')[0];
		this.playlistCardTitle = html.getElementsByClassName('playlist-card-title')[0];
		this.playlistCardDescription = html.getElementsByClassName('playlist-card-description')[0];
		this.playlistCardBadge = html.getElementsByClassName('playlist-card-badge')[0];

		this.playlistCardDetail = html.getElementsByClassName('playlist-card-detail')[0];
		this.playlistCardPlus = html.getElementsByClassName('playlist-card-plus')[0];

		this.playlistCardLike = html.getElementsByClassName('playlist-card-like')[0];
		this.playlistCardUnlike = html.getElementsByClassName('playlist-card-unlike')[0];

		if (this.column) {
			const div = document.createElement('div');
			div.classList.add('d-flex', 'w-100');

			div.append(this.playlistCardDetail);
			div.append(this.playlistCardPlus);

			this.playlistCard.append(div);

			this.playlistCard.classList.add('playlist-card-colunm');
		}

		if (isNaN(parseInt(this.id)) || parseInt(this.id) != this.id) this.playlistCardBadge.remove();
	}

	async makeCard(list = null, finishCallback = null) {
		let loadTimeout = setTimeout(() => {
			this.playlistCard.remove();
		}, 10000);

		this.playlistCard.id = 'playlist-' + this.id;
		this.playlistCard.href = '/web/collection?id=' + this.id;
		this.playlistCard.onclick = function () {
			route(event);
		};

		this.playlistCardImage.src = this.image;
		this.playlistCardTitle.innerText = this.title;
		this.playlistCardDescription.innerText = this.description;
		if (this.description.length == 0) this.playlistCardDescription.remove();

		this.like(null, 2);

		this.playlistCardLike.onclick = (e) => this.like(e, 3);
		this.playlistCardUnlike.onclick = (e) => this.popup(e, 1);

		this.playlistCardBadge.removeAttribute('hidden');
		this.playlistCard.classList.remove('playlist-card-skeleton');

		clearTimeout(loadTimeout);
		if (list !== null || finishCallback !== null) finishCallback(list, this);
	}

	async getSkeleton() {
		await this.loadTemplate();

		this.playlistCard.classList.add('playlist-card-skeleton');

		return this.playlistCard;
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

		formData.append('playlist', this.id);
		formData.append('action', action);
		formData.append('token', token);

		apiCall('api/user/like', formData, async (data) => {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);
				} else {
					if ((action == 1 && parsedData) || (action == 2 && !parsedData)) {
						this.playlistCardLike.removeAttribute('hidden');
						this.playlistCardUnlike.setAttribute('hidden', '');
					}

					if ((action == 3 && parsedData) || (action == 2 && parsedData)) {
						this.playlistCardLike.setAttribute('hidden', '');
						this.playlistCardUnlike.removeAttribute('hidden');
					}
				}
			}
		});
	}
}

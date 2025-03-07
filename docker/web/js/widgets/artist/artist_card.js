class ArtistCard {
	constructor(id, image, pseudo, column) {
		this.id = id;
		this.image = image;
		this.pseudo = pseudo;
		this.column = column;
	}

	async loadTemplate() {
		const parser = new DOMParser();

		let html = await fetch('/web/widgets/artist/artist_card.php').then((data) => data.text());
		html = parser.parseFromString(html, 'text/html');

		this.artistCard = html.getElementsByClassName('artist-card')[0];
		this.artistCardImage = html.getElementsByClassName('artist-card-image')[0];
		this.artistCardTitle = html.getElementsByClassName('artist-card-title')[0];
		this.artistCardPlus = html.getElementsByClassName('artist-card-plus')[0];
		this.artistCardDetail = html.getElementsByClassName('artist-card-detail')[0];
		this.artistCardBadge = html.getElementsByClassName('artist-card-badge')[0];

		this.artistCardLike = html.getElementsByClassName('artist-card-like')[0];
		this.artistCardUnlike = html.getElementsByClassName('artist-card-unlike')[0];

		if (this.column) {
			this.artistCardDetail.append(this.artistCardPlus);

			this.artistCard.classList.add('artist-card-colunm');
		}

		if (isNaN(parseInt(this.id)) || parseInt(this.id) != this.id) this.artistCardBadge.remove();
	}

	async makeCard(list = null, finishCallback = null) {
		let loadTimeout = setTimeout(() => {
			this.artistCard.remove();
		}, 10000);

		this.artistCard.id = 'artist-' + this.id;
		this.artistCard.href = '/web/exposure?id=' + this.id;
		this.artistCard.onclick = function () {
			route(event);
		};

		this.artistCardImage.src = this.image;
		this.artistCardTitle.innerText = this.pseudo;

		this.like(null, 2);

		this.artistCardLike.onclick = (e) => this.like(e, 3);
		this.artistCardUnlike.onclick = (e) => this.popup(e, 1);

		this.artistCardBadge.removeAttribute('hidden');
		this.artistCard.classList.remove('artist-card-skeleton');

		clearTimeout(loadTimeout);
		if (list !== null || finishCallback !== null) finishCallback(list, this);
	}

	async getSkeleton() {
		await this.loadTemplate();

		this.artistCard.classList.add('artist-card-skeleton');

		return this.artistCard;
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

		formData.append('artist', this.id);
		formData.append('action', action);
		formData.append('token', token);

		apiCall('api/user/like', formData, async (data) => {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData['error'] != undefined) {
					console.log(parsedData['error']);
				} else {
					if ((action == 1 && parsedData) || (action == 2 && !parsedData)) {
						this.artistCardLike.removeAttribute('hidden');
						this.artistCardUnlike.setAttribute('hidden', '');
					}

					if ((action == 3 && parsedData) || (action == 2 && parsedData)) {
						this.artistCardLike.setAttribute('hidden', '');
						this.artistCardUnlike.removeAttribute('hidden');
					}
				}
			}
		});
	}
}

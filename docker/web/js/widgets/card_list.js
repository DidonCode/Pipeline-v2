class List {
	constructor(id, title, cards) {
		this.id = id;
		this.title = title;
		this.cards = cards;

		this.position = 0;
		this.totalWidth = 0;
		this.speed = 0;
		this.threshold = 50;
	}

	async loadTemplate() {
		const parser = new DOMParser();

		let html = await fetch('/web/widgets/card_list.php').then((data) => data.text());
		html = parser.parseFromString(html, 'text/html');

		this.cardList = html.getElementsByClassName('card-list')[0];
		this.cardListTitle = html.getElementById('card-list-title');

		this.content = this.cardList.getElementsByClassName('card-list-container')[0];
		this.left = this.cardList.getElementsByClassName('card-list-button-left')[0];
		this.right = this.cardList.getElementsByClassName('card-list-button-right')[0];
	}

	async makeList() {
		this.cardList.id = this.id;
		this.cardListTitle.innerText = this.title;

		this.left.onclick = () => {
			this.updateSpeed();
			this.move(this.speed);
		};

		this.right.onclick = () => {
			this.updateSpeed();
			this.move(-this.speed);
		};

		function test(list, card) {
			list.update();
		}

		for (const card of this.cards) {
			card.makeCard(this, test);
		}
	}

	async getSkeleton() {
		await this.loadTemplate();

		for (const card of this.cards) {
			this.content.appendChild(await card.getSkeleton());
		}

		const resizeObserver = new ResizeObserver(() => {
			this.update();
		});

		resizeObserver.observe(this.content);

		this.update();

		return this.cardList;
	}

	scrollEnd() {
		this.update();
		this.move(-this.totalWidth);
	}

	edit(editFunction) {
		this.cards.map((card) => editFunction(card));
	}

	async add(cards, editFunction) {
		await cards.map(async (card) => {
			this.cards.push(card);
			this.content.appendChild(await card.getSkeleton());
			editFunction(card);
			card.makeCard();
			this.update();
		});
	}

	update() {
		this.totalWidth = this.calculateTotalWidth();
		this.updateSpeed();
		this.updateArrowsVisibility();
		this.updateTranslate();
		this.updateArrowStates();
	}

	calculateTotalWidth() {
		return Array.from(this.content.children).reduce((sum, card) => {
			const cardStyle = getComputedStyle(card);
			const marginLeft = parseFloat(cardStyle.marginLeft);
			const paddingLeft = parseFloat(cardStyle.paddingLeft);

			return sum + card.getBoundingClientRect().width + marginLeft + paddingLeft;
		}, 0);
	}

	updateSpeed() {
		this.speed = this.content.getBoundingClientRect().width;
	}

	updateTranslate() {
		const contentWidth = this.content.getBoundingClientRect().width;

		if (this.totalWidth <= contentWidth) {
			this.position = 0;
		} else if (this.position < -(this.totalWidth - contentWidth)) {
			this.position = -(this.totalWidth - contentWidth);
		}

		this.content.style.translate = this.position + 'px 0';
	}

	updateArrowsVisibility() {
		const contentWidth = this.content.getBoundingClientRect().width;
		if (this.totalWidth <= contentWidth) {
			this.left.style.display = 'none';
			this.right.style.display = 'none';
		} else {
			this.left.style.display = 'block';
			this.right.style.display = 'block';
		}
	}

	snapToEdges() {
		if (this.content == undefined) return;

		const contentWidth = this.content.getBoundingClientRect().width;

		if (this.position > -threshold) {
			this.position = 0;
		}

		const maxPosition = -(this.totalWidth - contentWidth);
		if (this.position < maxPosition + threshold) {
			this.position = maxPosition;
		}

		this.content.style.translate = this.position + 'px 0';
	}

	updateArrowStates() {
		const contentWidth = this.content.getBoundingClientRect().width;
		const maxPosition = -(this.totalWidth - contentWidth);

		if (this.position <= maxPosition) {
			this.right.classList.add('list-disabled');
		} else {
			this.right.classList.remove('list-disabled');
		}

		if (this.position >= 0) {
			this.left.classList.add('list-disabled');
		} else {
			this.left.classList.remove('list-disabled');
		}
	}

	move(value) {
		this.position += value;

		if (this.position > 0) this.position = 0;
		const maxPosition = -(this.totalWidth - this.content.getBoundingClientRect().width);
		if (this.position < maxPosition) this.position = maxPosition;

		this.content.style.translate = this.position + 'px 0';

		setTimeout(this.snapToEdges, 200);

		this.updateArrowStates();
	}
}

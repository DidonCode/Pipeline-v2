class Sound {
	constructor(id, title, artist, type, image, link) {
		this.id = id;
		this.title = title;
		this.artist = artist;
		this.type = type;
		this.image = image;
		this.link = link;
	}

	isValid() {
		if (!this.id || typeof this.id !== 'string') return false;
		if (!this.title || typeof this.title !== 'string') return false;
		if (!this.artist || typeof this.artist !== 'string') return false;
		if ((parseInt(this.type) != 0 && parseInt(this.type) != 1) || typeof this.type !== 'string') return false;
		if (!this.image || typeof this.image !== 'string') return false;
		if (!this.link || typeof this.link !== 'string') return false;

		return true;
	}

	static toClass(data) {
		return new Sound(data['id'], data['title'], data['artist'], data['type'], data['image'], data['link']);
	}
}

module.exports = { Sound };

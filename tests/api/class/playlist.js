class Playlist {
	constructor(id, owner, title, description, image, isPublic) {
		this.id = id;
		this.owner = owner;
		this.title = title;
		this.description = description;
		this.image = image;
		this.isPublic = isPublic;
	}

	isValid() {
		if (!this.id || typeof this.id !== 'string') return false;
		if (!this.owner || typeof this.owner !== 'string') return false;
		if (!this.title || typeof this.title !== 'string') return false;
		if (!this.description || typeof this.description !== 'string') return false;
		if (!this.image || typeof this.image !== 'string') return false;
		if ((parseInt(this.isPublic) != 0 && parseInt(this.isPublic) != 1) || typeof this.isPublic !== 'string') return false;

		return true;
	}

	static toClass(data) {
		return new Playlist(data['id'], data['owner'], data['title'], data['description'], data['image'], data['public']);
	}
}

module.exports = { Playlist };

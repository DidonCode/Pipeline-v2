const fs = require('fs');

const { User } = require('./class/user.js');
const { Artist } = require('./class/artist.js');
const { Playlist } = require('./class/playlist.js');
const { Sound } = require('./class/sound.js');
const { Member } = require('./class/member.js');

function randomString(length) {
	const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	let result = '';
	for (let i = 0; i < length; i++) {
		result += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	return result;
}

describe("Tests de l'API Butify (user)", () => {
	const apiUrl = 'http://localhost:8082/';

	let token = '';
	let playlistId = 0;
	let soundId = 0;

	test('POST api/user/account - Retourne un statut 201 et les informations du compte suivies de son token', async () => {
		const formData = new FormData();
		formData.append('email', randomString(20) + '@' + randomString(5) + '.com');
		formData.append('pseudo', randomString(20));
		formData.append('password', 'butify');
		formData.append('artist', 1);

		const response = await fetch(apiUrl + 'api/user/account', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(201);

		const data = await response.json();

		expect(data).toHaveProperty('token');
		token = data['token'];

		expect(User.toClass(data['user']).isValid()).toBe(true);
	});

	test('POST api/user/playlist - Retourne un statut 201 et les informations de la playlist créée', async () => {
		const formData = new FormData();
		formData.append('title', 'Test');
		formData.append('description', 'Test');
		formData.append('public', 1);
		formData.append('token', token);

		const response = await fetch(apiUrl + 'api/user/playlist', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(201);

		const data = await response.json();
		playlistId = data['id'];
		expect(Playlist.toClass(data).isValid()).toBe(true);
	});

	test('POST api/user/like - Retourne un statut 201 car on ne peut pas se liké notre propre playlist', async () => {
		const formData = new FormData();
		formData.append('token', token);
		formData.append('action', 3);
		formData.append('playlist', playlistId);

		const response = await fetch(apiUrl + 'api/user/like', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(201);

		const data = await response.json();

		expect(data).toBe(false);
	});

	test('POST api/user/playlist - Retourne un statut 200 et les informations de la playlist modifiées', async () => {
		const formData = new FormData();
		formData.append('token', token);
		formData.append('title', 'TestTest');
		formData.append('description', 'Test');
		formData.append('public', 1);
		formData.append('id', playlistId);

		const response = await fetch(apiUrl + 'api/user/playlist', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(200);

		const data = await response.json();
		expect(Playlist.toClass(data).isValid()).toBe(true);
	});

	test('POST api/user/playlist - Retourne un statut 200 et si la playlist a bien été supprimée', async () => {
		const formData = new FormData();
		formData.append('id', playlistId);
		formData.append('action', 1);
		formData.append('token', token);

		const response = await fetch(apiUrl + 'api/user/playlist', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(200);

		const data = await response.json();

		expect(data).toBe(true);
	});

	test('POST api/user/sound - Retourne un statut 201 et les informations de la musique uploadée', async () => {
		const formData = new FormData();
		formData.append('image', new Blob([fs.readFileSync('./tests/api/example/image.png')]), './tests/api/example/image.png');
		formData.append('audio', new Blob([fs.readFileSync('./tests/api/example/audio.mp3')]), './tests/api/example/audio.mp3');
		formData.append('title', 'Test');
		formData.append('token', token);

		const response = await fetch(apiUrl + 'api/user/sound', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(201);

		const data = await response.json();
		soundId = data['id'];
		expect(Sound.toClass(data).isValid()).toBe(true);
	});

	test('POST api/user/like - Retourne un statut 201 et si la musique a bien été likée', async () => {
		const formData = new FormData();
		formData.append('token', token);
		formData.append('action', 3);
		formData.append('sound', soundId);

		const response = await fetch(apiUrl + 'api/user/like', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(201);

		const data = await response.json();

		expect(data).toBe(true);
	});

	test('POST api/user/sound - Retourne un statut 200 et si la musique a bien été supprimée', async () => {
		const formData = new FormData();
		formData.append('sound', soundId);
		formData.append('token', token);

		const response = await fetch(apiUrl + 'api/user/sound', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(200);

		const data = await response.json();

		expect(data).toBe(true);
	});

	test('POST api/user/account - Retourne un statut 200 et les informations du compte modifiées', async () => {
		const formData = new FormData();
		formData.append('password', 'a');
		formData.append('token', token);

		const response = await fetch(apiUrl + 'api/user/account', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(200);

		const data = await response.json();
		expect(User.toClass(data).isValid()).toBe(true);
	});

	test('POST api/user/account - Retourne un statut 200 et si le compte a bien été déconnecté', async () => {
		const formData = new FormData();

		formData.append('token', token);
		formData.append('type', 'disconnect');

		const response = await fetch(apiUrl + 'api/user/account', {
			method: 'POST',
			body: formData,
		});

		expect(response.status).toBe(200);

		const data = await response.json();

		expect(data).toBe('disconnect');
	});
});

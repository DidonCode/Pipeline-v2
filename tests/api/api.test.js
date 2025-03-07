const { Artist } = require('./class/artist.js');
const { Playlist } = require('./class/playlist.js');
const { Sound } = require('./class/sound.js');
const { Member } = require('./class/member.js');

// Attention, ne marche plus si plus de quotas

describe("Tests de l'API Butify (global)", () => {
	const apiUrl = 'http://localhost:8082/';

	test("GET api/artist?id - Retourne un statut 200 et l'artiste correspondant à l'identifiant", async () => {
		const response = await fetch(apiUrl + 'api/artist?id=1');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Artist.toClass(data).isValid()).toBe(true);
	});

	test("GET api/artist?pseudo - Retourne un statut 200 et une liste d'artistes ayant un pseudo similaire", async () => {
		const response = await fetch(apiUrl + 'api/artist?pseudo=a');
		expect(response.status).toBe(200);

		const data = await response.json();

		if (data['database'].length == 0) expect(true).toBe(true);
		expect(Artist.toClass(data['database'][0]).isValid()).toBe(true);
	});

	test("GET api/playlist?id - Retourne un statut 200 et la laylist correspondante à l'identifiant", async () => {
		const response = await fetch(apiUrl + 'api/playlist?id=23');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Playlist.toClass(data).isValid()).toBe(true);
	});

	test('GET api/playlist?title - Retourne un statut 200 et une liste de playlists ayant un titre similaire', async () => {
		const response = await fetch(apiUrl + 'api/playlist?title=Sport mood');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Playlist.toClass(data['database'][0]).isValid()).toBe(true);
	});

	test("GET api/playlist?owner - Retourne un statut 200 et une liste de playlists ayant comme propriétaire l'utilisateur", async () => {
		const response = await fetch(apiUrl + 'api/playlist?owner=3');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Playlist.toClass(data['playlists'][0]).isValid()).toBe(true);
	});

	test("GET api/sound?id - Retourne un statut 200 et la musique correspondante à l'identifiant", async () => {
		const response = await fetch(apiUrl + 'api/sound?id=28');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Sound.toClass(data).isValid()).toBe(true);
	});

	test('GET api/sound?title - Retourne un statut 200 et une liste de musiques ayant un titre similaire', async () => {
		const response = await fetch(apiUrl + 'api/sound?title=Squid Game Jazz');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Sound.toClass(data['database'][0]).isValid()).toBe(true);
	});

	test("GET api/sound?owner - Retourne un statut 200 et une liste de musiques ayant comme propriétaire l'utilisateur", async () => {
		const response = await fetch(apiUrl + 'api/sound?artist=1');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Sound.toClass(data['sounds'][0]).isValid()).toBe(true);
	});

	test("GET api/member?id - Devrait retourner un statut 200 et l'utilisateur correspondant à l'identifiant", async () => {
		const response = await fetch(apiUrl + 'api/member?id=3');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Member.toClass(data).isValid()).toBe(true);
	});

	test("GET api/member?pseudo - Devrait retourner un statut 200 et une liste d'utilisateur ayant un pseudo similaire", async () => {
		const response = await fetch(apiUrl + 'api/member?pseudo=Agathe');
		expect(response.status).toBe(200);

		const data = await response.json();

		expect(Member.toClass(data[0]).isValid()).toBe(true);
	});
});

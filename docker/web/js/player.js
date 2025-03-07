let player = null;
let playerUpdate = null;

const play = document.getElementById('player-play');
const pause = document.getElementById('player-pause');

const previous = document.getElementById('player-previous');
const next = document.getElementById('player-next');

const volume = document.getElementById('player-volume');
const mute = document.getElementById('player-mute');
const unmute = document.getElementById('player-unmute');

const view = document.getElementById('player-switch');
const viewIcon = view.getElementsByTagName('i')[0];

const repeat = document.getElementById('player-repeat');
const repeatIcon = repeat.getElementsByTagName('i')[0];

const random = document.getElementById('player-random');
const randomIcon = random.getElementsByTagName('i')[0];

const likeButton = document.getElementById('player-like');
const unlikeButton = document.getElementById('player-unlike');

const progress = document.getElementById('player-progress-bar');
const time = document.getElementById('player-timecode');

const preview = document.getElementById('player-preview');

const playerContainer = document.getElementById('player-container');
const mainContainer = document.getElementById('main-container');

//--------------------------------------\\

function alreadyExist() {
	const playerElement = document.getElementById('player');

	return player !== null || playerElement !== null;
}

function switchView(requestView) {
	const playerElement = document.getElementById('player');
	let playerPlay = document.getElementById('player-container-play');

	if (sessionStorage.getItem('player-view') != undefined && requestView.toString() === sessionStorage.getItem('player-view')) return;

	if (requestView !== false) {
		player !== null ? playerContainer.appendChild(player.getIframe()) : playerContainer.appendChild(playerElement);
		playerElement.classList.add('minised-player');

		sessionStorage.setItem('player-view', true);
	} else {
		playerElement.classList.remove('minised-player');

		player !== null ? playerPlay.appendChild(player.getIframe()) : playerPlay.appendChild(playerElement);

		setList(JSON.parse(sessionStorage.getItem('player-sounds')));
		updateList();

		sessionStorage.setItem('player-view', false);
	}
}

async function like(id, action) {
	if (action == 2) {
		if (!sessionExist()) return;
	} else {
		if (!sessionExist()) {
			sessionDestroy();
			return;
		}
	}

	let formData = new FormData();

	formData.append('sound', id);
	formData.append('action', action);
	formData.append('token', token);

	await apiCall('api/user/like', formData, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData['error'] != undefined) {
				console.log(parsedData['error']);
			} else {
				if (parsedData) {
					unlikeButton.removeAttribute('hidden');
					likeButton.setAttribute('hidden', '');
				} else {
					unlikeButton.setAttribute('hidden', '');
					likeButton.removeAttribute('hidden');
				}
			}
		}
	});
}

//--------------------------------------\\

function booleanAction(element, boolean) {
	if (element !== null) {
		if (boolean === false) {
			element.classList.add('contrast-text');
			element.classList.remove('boolean-text');
		} else {
			element.classList.remove('contrast-text');
			element.classList.add('boolean-text');
		}
	}
}

function formatTime(seconds) {
	let minutes = Math.floor(seconds / 60);
	var seconds = Math.floor(seconds % 60);
	return minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
}

function updateTimecode(timeCode, duration) {
	time.innerText = formatTime(timeCode) + ' / ' + formatTime(duration);
	sessionStorage.setItem('player-timecode', timeCode);

	progress.max = duration;
	progress.value = timeCode;

	const value = (timeCode / duration) * 100;
	progress.style.setProperty('--progress', `${value}%`);

	if (value >= 99) {
		sessionStorage.setItem('player-timecode', 0);
		atEnd();
	}
}

function updateVolume(playerVolume) {
	sessionStorage.setItem('player-volume', playerVolume);
}

function updateList() {
	const playerList = document.getElementById('player-list');

	for (let i = 0; i < playerList.children.length; i++) {
		i == sessionStorage.getItem('player-index') ? playerList.children[i].classList.add('selected-card') : playerList.children[i].classList.remove('selected-card');
	}
}

async function setList(sounds) {
	const playerList = document.getElementById('player-list');

	if (sounds === null || sounds === undefined || playerList === null) return;

	playerList.innerHTML = '';

	for (const sound of sounds) {
		const index = playerList.children.length;

		const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

		playerList.append(await card.getSkeleton());

		index == sessionStorage.getItem('player-index') ? card.soundCard.classList.add('selected-card') : card.soundCard.classList.remove('selected-card');

		card.makeCard();

		card.soundCard.href = '';
		card.soundCard.onclick = function (e) {
			e.preventDefault();

			sessionStorage.setItem('player-index', index);
			sessionStorage.setItem('player-timecode', 0);

			loadSound(sounds[index]);
		};
	}
}

function clear() {
	if (player != null) {
		clearInterval(playerUpdate);
		player.g.remove();
		player = null;
	}

	const playerElement = document.getElementById('player');
	if (playerElement != null) playerElement.remove();

	sessionStorage.removeItem('player-index');
	sessionStorage.removeItem('player-music');
	sessionStorage.removeItem('player-sounds');
	sessionStorage.removeItem('player-timecode');
}

//--------------------------------------\\

function loadSound(video) {
	sessionStorage.setItem('player-music', JSON.stringify(video));

	const card = new SoundCard(video.id, video.image, video.title, video.type, video.artist, false);

	preview.innerHTML = '';

	(async () => {
		preview.append(await card.getSkeleton());
		card.soundCardPlus.remove();
		card.soundCard.style.width = '100%';
		card.makeCard();
		card.soundCard.href = '';
		card.soundCard.onclick = function (e) {
			e.preventDefault();
		};
	})();

	const storedVolume = sessionStorage.getItem('player-volume');
	if (storedVolume === null) sessionStorage.setItem('player-volume', 25);
	volume.value = storedVolume;

	const storedRepeat = sessionStorage.getItem('player-repeat');
	if (storedRepeat === null) sessionStorage.setItem('player-repeat', false);

	const storedRandom = sessionStorage.getItem('player-random');
	if (storedRandom === null) sessionStorage.setItem('player-random', false);

	const storedView = sessionStorage.getItem('player-view');
	if (storedView === null) sessionStorage.setItem('player-view', false);

	if (repeatIcon !== null) booleanAction(repeatIcon, !(storedRepeat === 'true'));
	if (randomIcon !== null) booleanAction(randomIcon, !(storedRandom === 'true'));

	if (player != null) {
		clearInterval(playerUpdate);
		player.g.remove();
		player = null;
	}

	if (!alreadyExist()) {
		const playerElement = document.createElement('div');
		playerElement.setAttribute('id', 'player');

		const playerPlay = document.getElementById('player-container-play');
		if (playerPlay !== null) {
			playerPlay.append(playerElement);
			sessionStorage.setItem('player-view', false);
		} else {
			playerElement.classList.add('minised-player');
			playerContainer.append(playerElement);
			sessionStorage.setItem('player-view', true);
		}
	}

	clearInterval(playerUpdate);

	isNaN(parseInt(video.id)) ? loadYoutubePlayer(video) : loadButifyPlayer(video);

	updateList();

	repeat.onclick = function () {
		const storedRepeat = sessionStorage.getItem('player-repeat');
		storedRepeat !== null ? sessionStorage.setItem('player-repeat', !(storedRepeat === 'true')) : sessionStorage.setItem('player-repeat', true);

		if (repeatIcon !== null) booleanAction(repeatIcon, storedRepeat === 'true');
	};

	random.onclick = function () {
		const storedRandom = sessionStorage.getItem('player-random');
		storedRandom !== null ? sessionStorage.setItem('player-random', !(storedRandom === 'true')) : sessionStorage.setItem('player-random', true);

		if (randomIcon !== null) booleanAction(randomIcon, storedRandom === 'true');
	};

	like(video.id, 2);

	likeButton.onclick = function () {
		like(video.id, 3);
	};

	unlikeButton.onclick = function () {
		like(video.id, 1);
	};

	view.onclick = function (event) {
		const storedView = sessionStorage.getItem('player-view');

		if (storedView === 'false') {
			window.history.pushState({}, '', '/web/home');
			route(event);
		} else {
			window.history.pushState({}, '', '/web/play?id=' + JSON.parse(sessionStorage.getItem('player-music')).id);
			route(event);
		}

		if (viewIcon !== null) booleanAction(viewIcon, storedView === 'true');
	};

	playerContainer.style.display = 'block';
	mainContainer.classList.add('main-container-player');

	if (sessionExist()) {
		let formData = new FormData();

		formData.append('sound', video.id);
		formData.append('token', token);

		apiCall('api/user/activity', formData, async function (data) {});
	}

	window.history.pushState({}, '', '/web/play?id=' + JSON.parse(sessionStorage.getItem('player-music')).id);
}

function loadPlaylist(playlistId) {
	apiCall('api/sound?playlist=' + playlistId, null, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData != null) {
				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);
					return;
				} else {
					sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));
					sessionStorage.setItem('player-index', 0);
					setList(parsedData);
					loadSound(parsedData[0]);
				}
			}
		}
	});
}

function loadPlayer() {
	const url = window.location.search;
	const urlParams = new URLSearchParams(url);

	const videoId = urlParams.get('id');
	const playlistId = urlParams.get('list');

	const index = parseInt(sessionStorage.getItem('player-index'));
	const sounds = JSON.parse(sessionStorage.getItem('player-sounds'));

	if (index != undefined && index != null && sounds != undefined && sounds != null) {
		setList(sounds);
		loadSound(sounds[index]);

		return;
	}

	if (videoId != undefined) {
		if (sessionExist()) {
			let formData = new FormData();

			formData.append('sound', videoId);
			formData.append('token', token);

			apiCall('api/user/play', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							routeError(parsedData['error']);
							return;
						} else {
							sessionStorage.setItem('player-sounds', JSON.stringify(parsedData));
							sessionStorage.setItem('player-index', 0);
							setList(parsedData);
							loadSound(parsedData[0]);
						}
					}
				}
			});
		} else {
			apiCall('api/sound?id=' + videoId, null, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData != null) {
						if (parsedData['error'] != undefined) {
							routeError(parsedData['error']);
							return;
						} else {
							sessionStorage.setItem('player-sounds', JSON.stringify([parsedData]));
							sessionStorage.setItem('player-index', 0);
							setList([parsedData]);
							loadSound(parsedData);
						}
					}
				}
			});
		}
	}

	if (playlistId != undefined) {
		loadPlaylist(playlistId);
	}
}

//--------------------------------------\\

function randomSound() {
	let index = sessionStorage.getItem('player-index');
	const sounds = JSON.parse(sessionStorage.getItem('player-sounds'));

	if (sounds !== null && sounds !== undefined && index !== null && index !== undefined) {
		index = Math.floor(Math.random() * sounds.length);

		sessionStorage.setItem('player-index', index);
		sessionStorage.setItem('player-timecode', 0);

		loadSound(sounds[index]);
	}
}

function repeatSound() {
	const music = JSON.parse(sessionStorage.getItem('player-music'));

	if (music !== null) {
		sessionStorage.setItem('player-timecode', 0);

		loadSound(music);
	}
}

function previousSound() {
	let index = parseInt(sessionStorage.getItem('player-index'));
	const sounds = JSON.parse(sessionStorage.getItem('player-sounds'));
	const timeCode = sessionStorage.getItem('player-timecode');

	if (sounds !== null && sounds !== undefined && index !== null && index !== undefined) {
		clearInterval(playerUpdate);

		index - 1 < 0 ? (index = sounds.length - 1) : (index -= 1);

		sessionStorage.setItem('player-index', index);
		sessionStorage.setItem('player-timecode', 0);

		loadSound(sounds[index]);
	}
}

function nextSound() {
	let index = parseInt(sessionStorage.getItem('player-index'));
	const sounds = JSON.parse(sessionStorage.getItem('player-sounds'));

	if (sounds !== null && sounds !== undefined && index !== null && index !== undefined) {
		clearInterval(playerUpdate);

		index + 1 >= sounds.length ? (index = 0) : (index += 1);

		sessionStorage.setItem('player-index', index);
		sessionStorage.setItem('player-timecode', 0);

		loadSound(sounds[index]);
	}
}

function atEnd() {
	const repeat = sessionStorage.getItem('player-repeat');
	const random = sessionStorage.getItem('player-random');

	if (repeat !== null && repeat === 'true') {
		repeatSound();
		return;
	}

	if (random !== null && random === 'true') {
		randomSound();
		return;
	}

	nextSound();
}

//--------------------------------------\\

function loadButifyPlayer(video) {
	const playerContainer = document.getElementById('player');

	let source = document.getElementById('player-source');
	let player = document.getElementById('player-video');
	let image = document.getElementById('player-image');

	if (source === null) {
		player = document.createElement('video');
		player.setAttribute('id', 'player-video');
		player.autoplay = true;
		player.muted = true;
		player.currentTime = sessionStorage.getItem('player-timecode');
		player.play();
		player.muted = false;
		player.volume = sessionStorage.getItem('player-volume') / 100;

		source = document.createElement('source');
		source.src = video.link;
		source.setAttribute('id', 'player-source');

		image = document.createElement('img');
		image.setAttribute('id', 'player-image');

		player.appendChild(source);

		playerContainer.appendChild(image);
		playerContainer.appendChild(player);
	} else {
		source.src = video.link;

		player.load();
		player.currentTime = sessionStorage.getItem('player-timecode');
		player.play();
	}

	if (video.link.endsWith('.mp3')) {
		source.setAttribute('type', 'audio/mpeg');
		player.style.display = 'none';

		image.style.display = '';
		image.src = video.image;
	} else {
		source.setAttribute('type', 'video/mp4');

		player.style.display = '';

		image.style.display = 'none';
		image.src = '';
	}

	//-------------\\

	player.onplaying = function () {
		play.style.display = 'none';
		pause.style.display = 'block';
	};

	player.onpause = function () {
		pause.style.display = 'none';
		play.style.display = 'block';
	};

	player.muted === true ? (mute.style.display = 'none') : (unmute.style.display = 'none');

	player.ontimeupdate = function () {
		if (player == null) return;
		updateTimecode(player.currentTime, player.duration);
	};

	//-------------\\

	play.onclick = function () {
		player.play();
	};
	pause.onclick = function () {
		player.pause();
	};

	previous.onclick = function () {
		previousSound();
	};
	next.onclick = function () {
		nextSound();
	};

	mute.onclick = function () {
		player.volume = 0;
		volume.value = 0;

		mute.style.display = 'none';
		unmute.style.display = '';
		volume.style.display = 'none';
	};

	let hideTimeout;

	mute.onmouseover = function () {
		clearTimeout(hideTimeout);
		volume.style.display = '';
		volume.style.width = '';
	};

	mute.onmouseout = function () {
		hideTimeout = setTimeout(() => {
			volume.style.width = '0px';
			setTimeout(() => {
				volume.style.display = 'none';
			}, 300);
		}, 500);
	};

	unmute.onclick = function () {
		volume.value = sessionStorage.getItem('player-volume');
		player.volume = volume.value / 100;

		unmute.style.display = 'none';
		mute.style.display = '';
		volume.style.display = '';
	};

	volume.oninput = function () {
		player.volume = volume.value / 100;
		updateVolume(volume.value);
	};

	volume.onmouseover = function () {
		clearTimeout(hideTimeout);
	};

	volume.onmouseout = function () {
		hideTimeout = setTimeout(() => {
			volume.style.width = '0px';
			setTimeout(() => {
				volume.style.display = 'none';
			}, 300);
		}, 500);
	};

	progress.onchange = function () {
		player.currentTime = progress.value;
	};
}

function loadYoutubePlayer(video) {
	player = new YT.Player('player', {
		videoId: video.id,
		playerVars: {
			autoplay: 1,
			controls: 0,
			modestbranding: 1,
			fs: 0,
			rel: 0,
			showinfo: 0,
			iv_load_policy: 3,
			cc_load_policy: 0,
		},
		events: {
			onReady: onPlayerReady,
			onStateChange: onPlayerStateChange,
		},
	});
}

function onPlayerReady(event) {
	if (player == null) return;

	let eventPlayer = event.target;

	eventPlayer.seekTo(0, true);
	eventPlayer.isMuted() ? (mute.style.display = 'none') : (unmute.style.display = 'none');
	eventPlayer.setVolume(sessionStorage.getItem('player-volume'));

	play.onclick = function () {
		eventPlayer.playVideo();
	};
	pause.onclick = function () {
		eventPlayer.pauseVideo();
	};

	previous.onclick = function () {
		previousSound();
	};
	next.onclick = function () {
		nextSound();
	};

	mute.onclick = function () {
		eventPlayer.mute();
		volume.value = 0;

		mute.style.display = 'none';
		unmute.style.display = '';
		volume.style.display = 'none';
	};

	let hideTimeout;

	mute.onmouseover = function () {
		clearTimeout(hideTimeout);
		volume.style.display = '';
		volume.style.width = '';
	};

	mute.onmouseout = function () {
		hideTimeout = setTimeout(() => {
			volume.style.width = '0px';
			setTimeout(() => {
				volume.style.display = 'none';
			}, 300);
		}, 500);
	};

	unmute.onclick = function () {
		eventPlayer.unMute();
		volume.value = sessionStorage.getItem('player-volume');

		unmute.style.display = 'none';
		mute.style.display = '';
		volume.style.display = '';
	};

	volume.oninput = function () {
		eventPlayer.setVolume(volume.value);
		updateVolume(volume.value);
	};

	volume.onmouseover = function () {
		clearTimeout(hideTimeout);
	};

	volume.onmouseout = function () {
		hideTimeout = setTimeout(() => {
			volume.style.width = '0px';
			setTimeout(() => {
				volume.style.display = 'none';
			}, 300);
		}, 500);
	};

	progress.onchange = function () {
		eventPlayer.seekTo(progress.value, true);
	};

	playerUpdate = setInterval(function () {
		updateTimecode(eventPlayer.getCurrentTime(), eventPlayer.getDuration());
	}, 500);
}

function onPlayerStateChange(event) {
	if (player == null) return;

	if (event.data === YT.PlayerState.PLAYING) {
		play.style.display = 'none';
		pause.style.display = 'block';
	} else if (event.data === YT.PlayerState.PAUSED) {
		pause.style.display = 'none';
		play.style.display = 'block';
	}
}

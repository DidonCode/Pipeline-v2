(() => {
	const search = document.getElementById('search');
	const searchPanel = document.getElementById('search-panel');
	searchPanel.style.border = 'unset';

	let typingTimeout;

	search.addEventListener('click', function (event) {
		event.stopPropagation();

		if (event.key === 'Enter') {
			searchPanel.style.border = 'unset';
			searchPanel.setAttribute('hidden', '');
			window.history.pushState({}, '', '/web/search?query=' + search.value);
			route(event);
			return;
		}

		if (searchPanel.children.length == 0) {
			searchPanel.style.border = 'unset';
		} else {
			searchPanel.style.border = '';
			searchPanel.removeAttribute('hidden');
		}
	});

	search.addEventListener('keydown', function (event) {
		clearTimeout(typingTimeout);

		if (event.key === 'Enter') {
			searchPanel.style.border = 'unset';
			searchPanel.setAttribute('hidden', '');
			window.history.pushState({}, '', '/web/search?query=' + search.value);
			route(event);
			return;
		}

		if (searchPanel.children.length == 0) {
			searchPanel.style.border = 'unset';
		} else {
			searchPanel.style.border = '';
			searchPanel.removeAttribute('hidden');
		}

		typingTimeout = setTimeout(makeSearch, 1000);
	});

	window.addEventListener('click', function () {
		searchPanel.setAttribute('hidden', '');
		searchPanel.style.border = 'unset';
	});

	async function makeSearch() {
		if (search.value.length == 0) {
			searchPanel.innerHTML = '';
			searchPanel.style.border = 'unset';
			return;
		}

		searchPanel.innerHTML = '<div class="loader"></div>';
		searchPanel.style.border = '';
		searchPanel.removeAttribute('hidden');

		await apiCall('api/sound?title=' + search.value, null, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					searchPanel.innerHTML = '';

					if (parsedData['database'] != undefined) {
						for (let i = 0; i < 5 - searchPanel.children.length; i++) {
							(async () => {
								const sound = parsedData['database'][i];
								if (sound === undefined) return;
								const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

								searchPanel.append(await card.getSkeleton());
								card.makeCard();
							})();
						}
					}

					if (parsedData['youtube'] != undefined) {
						for (let i = 0; i < 5 - searchPanel.children.length; i++) {
							(async () => {
								const sound = parsedData['youtube'][i];
								if (sound === undefined) return;
								const card = new SoundCard(sound.id, sound.image, sound.title, sound.type, sound.artist, false);

								searchPanel.append(await card.getSkeleton());
								card.makeCard();
							})();
						}
					}
				}
			}
		});
	}
})();

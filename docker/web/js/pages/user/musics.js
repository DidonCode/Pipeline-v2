(() => {
	if (!sessionExist()) {
		sessionDestroy();
		return;
	}

	const musicTableResult = document.getElementById('music-table-result');

	function addSound(id, sound) {
		const row = document.createElement('tr');
		row.id = id;

		sound.map((element) => {
			const col = document.createElement('td');
			col.append(element);
			row.append(col);
		});

		musicTableResult.append(row);
	}

	apiCall('api/sound?artist=' + user['id'], null, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData['error'] != undefined) {
				routeError(parsedData['error']);
				return;
			} else {
				if (parsedData.sounds.length == 0) {
					const row = document.createElement('td');
					row.colSpan = 4;

					musicTableResult.append(row);
				} else {
					parsedData.sounds.map((sound) => {
						const id = 'sound-' + sound.id;

						const image = document.createElement('img');
						image.src = sound.image;

						const remove = document.createElement('button');
						remove.classList.add('btn');
						remove.innerHTML = '<i class="fa-solid fa-xmark contrast-text m-auto"></i>';
						remove.onclick = function () {
							let formData = new FormData();

							formData.append('sound', sound.id);
							formData.append('token', token);

							apiCall('api/user/sound', formData, async function (data) {
								if (data != '') {
									const parsedData = JSON.parse(data);
									if (parsedData != null) {
										if (parsedData['error'] != undefined) {
											routeError(parsedData['error']);
											return;
										} else {
											document.getElementById(id).remove();
										}
									}
								}
							});
						};

						const soundIcon = document.createElement('i');
						soundIcon.classList.add('fa-solid', 'fa-waveform-lines', 'm-auto');

						const videoIcon = document.createElement('i');
						videoIcon.classList.add('fa-regular', 'fa-clapperboard-play', 'm-auto');

						const type = sound.type === 0 ? soundIcon : videoIcon;

						addSound(id, [image, sound.title, type, remove]);
					});
				}
			}
		}
	});
})();

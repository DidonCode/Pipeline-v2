(() => {
	if (!sessionExist()) {
		sessionDestroy();
		return;
	}

	if (user.artist == 0) {
		window.location.href = '/web/home';
		route(event);
	}

	const audioImageUpload = document.getElementById('audio-image-upload');
	const audioImageClick = document.getElementById('audio-image-click');
	const audioImageClickView = document.getElementById('audio-image-click-view');
	const audioImagePreview = document.getElementById('audio-image-preview');

	audioImageClick.onclick = function () {
		audioImageUpload.click();
	};

	audioImageUpload.onclick = function (e) {
		e.stopPropagation();
	};

	audioImageUpload.addEventListener('change', function () {
		const file = event.target.files[0];

		if (file) {
			const reader = new FileReader();

			reader.onload = function (e) {
				audioImagePreview.src = e.target.result;
				audioImagePreview.style.display = '';

				audioImageClickView.style.display = 'none';
			};

			reader.readAsDataURL(file);
		} else {
			audioImagePreview.src = '';
			audioImagePreview.style.display = 'none';

			audioImageClickView.style.display = 'block';
		}
	});

	const audioAudioUpload = document.getElementById('audio-audio-upload');
	const audioAudioClick = document.getElementById('audio-audio-click');
	const audioAudioPreview = document.getElementById('audio-audio-preview');

	audioAudioClick.onclick = function () {
		audioAudioUpload.click();
	};

	audioAudioUpload.onclick = function (e) {
		e.stopPropagation();
	};

	audioAudioUpload.addEventListener('change', function () {
		const file = event.target.files[0];

		if (file) {
			const fileURL = URL.createObjectURL(file);
			audioAudioPreview.src = fileURL;

			audioAudioPreview.style.display = '';
		} else {
			audioAudioPreview.style.display = 'none';
		}
	});

	const audioTitle = document.getElementById('audio-title');
	const audioForm = document.getElementById('audio-form');

	audioForm.addEventListener('submit', function (event) {
		event.preventDefault();

		audioImageClick.classList.remove('error');
		audioTitle.classList.remove('error');
		audioAudioClick.classList.remove('error');

		const formData = new FormData(audioForm);
		formData.append('token', token);

		apiCall('api/user/sound', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						if (parsedData['error']['error_code'] == 1) {
							audioTitle.classList.add('error');
						} else if (parsedData['error']['error_code'] == 2) {
							audioImageClick.classList.add('error');
						} else if (parsedData['error']['error_code'] == 3) {
							audioAudioClick.classList.add('error');
						} else {
							routeError(eventCopy);
						}
					} else {
						clear();
						event.target.href = '/web/play?id=' + parsedData.id;
						route(event);
					}
				}
			}
		});
	});

	//----------------------------------------------\\

	const videoImageUpload = document.getElementById('video-image-upload');
	const videoImageClick = document.getElementById('video-image-click');
	const videoImageClickView = document.getElementById('video-image-click-view');
	const videoImagePreview = document.getElementById('video-image-preview');

	videoImageClick.onclick = function () {
		videoImageUpload.click();
	};

	videoImageUpload.onclick = function (e) {
		e.stopPropagation();
	};

	videoImageUpload.addEventListener('change', function () {
		const file = event.target.files[0];

		if (file) {
			const reader = new FileReader();

			reader.onload = function (e) {
				videoImagePreview.src = e.target.result;
				videoImagePreview.style.display = '';

				videoImageClickView.style.display = 'none';
			};

			reader.readAsDataURL(file);
		} else {
			videoImagePreview.src = '';
			videoImagePreview.style.display = 'none';

			videoImageClickView.style.display = 'block';
		}
	});

	const videoVideoUpload = document.getElementById('video-video-upload');
	const videoVideoClick = document.getElementById('video-video-click');
	const videoVideoPreview = document.getElementById('video-video-preview');

	videoVideoClick.onclick = function () {
		videoVideoUpload.click();
	};

	videoVideoUpload.onclick = function (e) {
		e.stopPropagation();
	};

	videoVideoUpload.addEventListener('change', function () {
		const file = event.target.files[0];

		if (file) {
			const fileURL = URL.createObjectURL(file);
			videoVideoPreview.src = fileURL;

			videoVideoPreview.style.display = '';
		} else {
			videoVideoPreview.style.display = 'none';
		}
	});

	const videoTitle = document.getElementById('video-title');
	const videoForm = document.getElementById('video-form');

	videoForm.addEventListener('submit', function (event) {
		event.preventDefault();

		videoVideoClick.classList.remove('error');
		videoTitle.classList.remove('error');

		const formData = new FormData(videoForm);
		formData.append('token', token);

		apiCall('api/user/sound', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						if (parsedData['error']['error_code'] == 1) {
							videoTitle.classList.add('error');
						}

						if (parsedData['error']['error_code'] == 2) {
							videoVideoClick.classList.add('error');
						}

						if (parsedData['error']['error_code'] == 3) {
							videoVideoClick.classList.add('error');
						}
					} else {
						clear();
						window.history.pushState({}, '', '/web/play?id=' + parsedData.id);
						route(event);
					}
				}
			}
		});
	});

	const audioButton = document.getElementById('audio-view');
	const videoButton = document.getElementById('video-view');

	audioButton.onclick = function () {
		videoForm.style.display = 'none';

		audioForm.style.display = 'flex';

		audioButton.classList.add('action');
		audioButton.classList.remove('primary-secondary', 'contrast-text');

		videoButton.classList.add('primary-secondary', 'contrast-text');
		videoButton.classList.remove('action');
	};

	videoButton.onclick = function () {
		audioForm.style.display = 'none';

		videoForm.style.display = 'flex';

		audioButton.classList.remove('action');
		audioButton.classList.add('primary-secondary', 'contrast-text');

		videoButton.classList.remove('primary-secondary', 'contrast-text');
		videoButton.classList.add('action');
	};
})();

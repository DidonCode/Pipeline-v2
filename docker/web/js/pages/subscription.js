(() => {
	const free = document.getElementById('free-buy');
	const basic = document.getElementById('basic-buy');
	const premium = document.getElementById('premium-buy');
	const loading = document.getElementById('subscription-loading');

	if (user['subscription'] != null) {
		if (user['subscription']['type'] === 'premium') {
			basic.setAttribute('disabled', '');
			premium.setAttribute('disabled', '');
			premium.innerText = 'possédé';
		} else {
			basic.setAttribute('disabled', '');
			basic.innerText = 'possédé';
			premium.innerText = 'amélioré';
		}
	} else {
		free.innerText = 'possédé';
	}

	function createSparkle() {
		const box = document.getElementById('premium');
		const sparkle = document.createElement('div');
		sparkle.style.position = 'absolute';
		sparkle.style.width = '5px';
		sparkle.style.height = '5px';
		sparkle.style.backgroundColor = 'gold';
		sparkle.style.borderRadius = '50%';

		const borderSize = -40;
		const boxRect = box.getBoundingClientRect();
		const position = Math.random() * ((boxRect.width - borderSize) * 2 + boxRect.height * 2);

		if (position < boxRect.width) {
			sparkle.style.top = `${-borderSize}px`;
			sparkle.style.left = `${position}px`;
		} else if (position < boxRect.width + boxRect.height) {
			sparkle.style.top = `${position - boxRect.width}px`;
			sparkle.style.left = `${boxRect.width}px`;
		} else if (position < boxRect.width * 2 + boxRect.height) {
			sparkle.style.top = `${boxRect.height}px`;
			sparkle.style.left = `${boxRect.width - (position - (boxRect.width + boxRect.height))}px`;
		} else {
			sparkle.style.top = `${boxRect.height - (position - (boxRect.width * 2 + boxRect.height))}px`;
			sparkle.style.left = `${-borderSize}px`;
		}

		sparkle.style.opacity = '1';
		sparkle.style.transition = 'opacity 1s ease-out, transform 1s ease-out';
		box.appendChild(sparkle);

		setTimeout(() => {
			sparkle.style.opacity = '0';
			sparkle.style.transform = 'scale(2)';
		}, 50);

		setTimeout(() => {
			sparkle.remove();
		}, 1000);
	}

	setInterval(createSparkle, 100);

	function createSession(subscription) {
		if (!sessionExist()) {
			sessionDestroy();
			return;
		}

		loading.removeAttribute('hidden');

		let formData = new FormData();

		formData.append('subscription', subscription);
		formData.append('token', token);

		apiCall('api/user/subscription', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				await Stripe(parsedData['key']).redirectToCheckout({ sessionId: parsedData['session'] });
			}

			loading.setAttribute('hidden', '');
		});
	}

	premium.onclick = () => createSession('premium');
	basic.onclick = () => createSession('basic');
})();

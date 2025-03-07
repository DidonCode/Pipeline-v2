(() => {
	if (!sessionExist()) {
		sessionDestroy();
		return;
	}

	const subscriptionContainer = document.getElementById('subscription-container');
	const subscription = document.getElementById('subscription');

	const url = window.location.search;
	const urlParams = new URLSearchParams(url);
	const checkout = urlParams.get('checkout');

	if (checkout == 'completed') {
		console.log("Bravo merci pour l'achat");
		sessionToken();
	}

	if (user['subscription'] != null) {
		const subscriptionTitle = document.getElementById('subscription-title');
		const subscriptionDescription = document.getElementById('subscription-description');
		const subscriptionCancel = document.getElementById('subscription-cancel');

		subscription.classList.add(user['subscription']['type']);
		subscriptionTitle.innerText = user['subscription']['type'];
		subscriptionDescription.innerText = user['subscription']['updateAt'];

		subscriptionCancel.onclick = function () {
			console.log('oui');
			let formData = new FormData();

			formData.append('action', 1);
			formData.append('token', token);

			apiCall('/api/user/subscription', formData, async function (data) {
				if (data != '') {
					const parsedData = JSON.parse(data);

					if (parsedData) subscription.remove();
				}
			});
		};
	} else {
		subscription.remove();

		const viewSubscription = document.createElement('div');
		viewSubscription.classList.add('text-center', 'mt-5');

		const addSubscription = document.createElement('button');
		addSubscription.classList.add('btn', 'clRounded1', 'action', 'px-4');
		addSubscription.innerText = 'Voir';
		addSubscription.onclick = function (event) {
			event.target.href = '/web/subscription';
			route(event);
		};

		viewSubscription.append(addSubscription);
		subscriptionContainer.append(viewSubscription);
	}

	/**
	 * @brief constantes du formulaire
	 */
	const profileImg = document.getElementById('profileImg-editInp');
	const visibility = document.getElementById('visibility');
	const pseudo = document.getElementById('pseudo');
	const email = document.getElementById('email');
	const newPwd = document.getElementById('newPwd');

	const imgCurrentPage = document.getElementById('idProfileImg');
	const profileEdit = document.getElementById('profileImg-editBtn');
	const exposure = document.getElementById('profile-exposure');

	/**
	 * @brief Affichage des informations de l'utlisateur sur la page
	 */
	if (user['public'] == 1) {
		visibility.checked = true;
	} else {
		visibility.checked = false;
	}
	pseudo.value = user['pseudo'];
	email.value = user['email'];
	imgCurrentPage.src = user['image'];
	//! À modifier
	/**
	 * @brief Gestion de l'ouverture de la page de profil (exposure)
	 */
	exposure.onclick = function (event) {
		event.target.href = '/web/exposure?id=' + user['id'];
		route(event);
	};
	//! /À modifier

	/**
	 * @brief Gestion du clic sur le bouton de modification de la photo de profil
	 */
	profileEdit.onclick = function () {
		profileImg.click();
	};

	/**
	 * @brief Lancé à la soumission du formulaire : enregistre les modifications en base de données
	 */
	updateForm.onsubmit = function (e) {
		e.preventDefault();
		email.classList.remove('error');

		pseudo.classList.remove('error');
		/**
		 * @brief Appel api : Entregistrement des modifications des infos de compte dans la base de données
		 */

		if (pseudo.value != user['pseudo']) {
			let formData = new FormData();
			formData.append('pseudo', pseudo.value);
			saveInput(formData);
		}

		if (user.artist === 0 && email.value != user['email']) {
			let formData = new FormData();
			formData.append('email', email.value);
			saveInput(formData);
		}

		if (newPwd.value) {
			let formData = new FormData();
			formData.append('password', newPwd.value);
			saveInput(formData);
		}

		if (visibility.checked != user.public) {
			let formData = new FormData();
			formData.append('public', visibility.checked ? 1 : 0);
			saveInput(formData);
		}
	};

	function saveInput(formData) {
		formData.append('token', token);

		apiCall('api/user/account', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);
				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						if (parsedData['error']['error_code'] === 1) {
							email.classList.add('error');
						}

						if (parsedData['error']['error_code'] === 2) {
							pseudo.classList.add('error');
						}
					} else {
						sessionUpdate(parsedData, token);
					}
				}
			}
		});
	}

	profileImg.addEventListener('change', function () {
		if (!profileImg.files[0]) return;

		let formData = new FormData();
		formData.append('image', profileImg.files[0]);
		imgCurrentPage.src = '/storage/user/profile/default.png';

		saveInput(formData);

		imgCurrentPage.src = user['image'];
	});
})();

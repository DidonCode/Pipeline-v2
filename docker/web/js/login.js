(() => {
	const signUpButton = document.getElementById('signUp');
	const signInButton = document.getElementById('signIn');
	const container = document.getElementById('container');

	signUpButton.addEventListener('click', () => {
		container.classList.add('right-panel-active');
	});

	signInButton.addEventListener('click', () => {
		container.classList.remove('right-panel-active');
	});

	const signUpForm = document.getElementById('sign-up');
	const signInForm = document.getElementById('sign-in');

	signUpForm.onsubmit = async function (e) {
		e.preventDefault();

		const pseudo = document.getElementsByName('sign-up-pseudo')[0];
		const email = document.getElementsByName('sign-up-email')[0];
		const password = document.getElementsByName('sign-up-password')[0];
		const artist = document.getElementById('artist');

		let formData = new FormData();

		formData.append('pseudo', pseudo.value);
		formData.append('email', email.value);
		formData.append('password', password.value);
		if (artist.checked === true) {
			formData.append('artist', 1);
		}
		email.classList.remove('error');
		pseudo.classList.remove('error');

		const response = await fetch('http://localhost:8082/api/user/account', {
			method: 'POST',
			body: formData,
		});

		const data = await response.text();

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
				}

				if (parsedData['token'] != undefined) {
					sessionCreate(parsedData['token'], JSON.stringify(parsedData['user']));
				} else {
					console.log("Désolé, nous n'arrivons pas à vous connecter.");
				}
			} else {
			}
		}
	};

	signInForm.onsubmit = async function (e) {
		e.preventDefault();

		const email = document.getElementsByName('sign-in-email')[0];
		const password = document.getElementsByName('sign-in-password')[0];

		let formData = new FormData();

		formData.append('email', email.value);
		formData.append('password', password.value);

		const response = await fetch('http://localhost:8082/api/user/account', {
			method: 'POST',
			body: formData,
		});

		const data = await response.text();

		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData != null) {
				if (parsedData['error'] != undefined) {
					console.log('Désolé, nous rencontrons un problème.');
				}

				if (parsedData['token'] != undefined) {
					sessionCreate(parsedData['token'], JSON.stringify(parsedData['user']));
				} else {
					console.log("Désolé, nous n'arrivons pas à vous connecter.");
				}
			} else {
				email.classList.add('error');
				password.classList.add('error');
			}
		}
	};
})();

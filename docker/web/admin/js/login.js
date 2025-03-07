(() => {
	const signInButton = document.getElementById('signIn');
	const container = document.getElementById('container');

	const signInForm = document.getElementById('sign-in');

	signInForm.onsubmit = function (e) {
		e.preventDefault();

		const email = document.getElementsByName('sign-in-email')[0];
		const password = document.getElementsByName('sign-in-password')[0];

		let formData = new FormData();

		formData.append('email', email.value);
		formData.append('password', password.value);

		fetch('../../api/user/account', {
			method: 'POST',
			body: formData,
		})
			.then((response) => response.text())
			.then((data) => {
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
			});
	};
})();

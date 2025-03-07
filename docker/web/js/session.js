let token = localStorage.getItem('token');
let user = localStorage.getItem('user');

if (token) {
	if (user) user = JSON.parse(user);
	setInterval(async () => {
		await sessionToken();
	}, 1800000);
}

async function sessionCreate(loginToken, loginUser) {
	token = loginToken;
	localStorage.setItem('token', loginToken);

	user = loginUser;
	localStorage.setItem('user', loginUser);

	window.location.href = '/web/home';
}

function sessionDestroy() {
	if (token) {
		let formData = new FormData();

		formData.append('token', token);
		formData.append('type', 'disconnect');

		apiCall('api/user/account', formData, async function (data) {
			if (data != '') {
				const parsedData = JSON.parse(data);

				if (parsedData != null) {
					if (parsedData['error'] != undefined) {
						routeError(parsedData['error']);
					}
				}
			}
		});
	}

	localStorage.removeItem('token');
	localStorage.removeItem('user');

	sessionStorage.clear();

	window.location.href = '/web/login.php';
}

async function sessionToken() {
	let formData = new FormData();

	formData.append('token', token);
	formData.append('type', 'new');

	await apiCall('api/user/account', formData, async function (data) {
		if (data != '') {
			const parsedData = JSON.parse(data);

			if (parsedData != null) {
				if (parsedData['error'] != undefined) {
					routeError(parsedData['error']);
				} else {
					sessionUpdate(parsedData.user, parsedData.token);
				}
			}
		}
	});
}

function sessionUpdate(newUser, newToken) {
	localStorage.setItem('user', JSON.stringify(newUser));
	user = newUser;

	if (newToken != null) {
		localStorage.setItem('token', newToken);
		token = newToken;
	}
}

function sessionExist() {
	return token != null && token != undefined && user != null && user != undefined;
}

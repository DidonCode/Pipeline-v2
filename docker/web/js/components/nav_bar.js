(() => {
	const userLogin = document.getElementById('user-login');
	const userConnected = document.getElementById('user-connected');

	const profilImage = document.getElementById('profil-image');
	const profilMenu = document.getElementById('profil-menu');

	window.addEventListener('click', function () {
		profilMenu.style.display = 'none';
	});

	if (sessionExist()) {
		userLogin.style.display = 'none';
		userConnected.removeAttribute('hidden');
		let action = userConnected.getElementsByTagName('img')[0];
		if (action) action.src = user.image;
		profilImage.addEventListener('click', function (e) {
			e.stopPropagation();
			profilMenu.style.display === 'block' ? (profilMenu.style.display = 'none') : (profilMenu.style.display = 'block');
		});
	} else {
		userConnected.setAttribute('hidden', '');
	}

	const lightMode = document.getElementById('light-mode');
	const darkMode = document.getElementById('dark-mode');

	lightMode.onclick = function () {
		document.body.classList.remove('darkMode');
		localStorage.setItem('theme', 'light');
		lightMode.style.display = 'none';
		darkMode.style.display = 'block';
	};

	darkMode.onclick = function () {
		document.body.classList.add('darkMode');
		localStorage.setItem('theme', 'dark');
		darkMode.style.display = 'none';
		lightMode.style.display = 'block';
	};

	if (localStorage.getItem('theme') === 'dark') {
		darkMode.click();
	} else {
		lightMode.click();
	}
})();

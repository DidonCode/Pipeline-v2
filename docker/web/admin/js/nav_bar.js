(() => {
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

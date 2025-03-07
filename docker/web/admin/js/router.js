const updateProgressBar = (percentage) => {
	const progressBar = document.getElementById('progress-bar');
	document.getElementById('progress-bar-container').style.display = 'block';
	progressBar.style.width = `${percentage}%`;
};

const resetProgressBar = () => {
	const progressBar = document.getElementById('progress-bar');
	document.getElementById('progress-bar-container').style.display = 'none';
	progressBar.style.width = '0';
};

const route = (event) => {
	event = event || window.event;
	event.preventDefault();
	const url = event.currentTarget.href;
	window.history.pushState({}, '', url);
	handleLocation();
};

const routes = {
	'/web/admin/': { title: 'Accueil', page: '/web/admin/page/accueil.php' },
	'/web/admin/accueil': {
		title: 'Accueil',
		page: '/web/admin/page/accueil.php',
	},
	'/web/admin/titre': { title: 'Accueil', page: '/web/admin/page/titre.php' },
	'/web/admin/playlist': {
		title: 'Accueil',
		page: '/web/admin/page/playlist.php',
	},
	'/web/admin/utilisateur': {
		title: 'Accueil',
		page: '/web/admin/page/utilisateur.php',
	},
	'/web/admin/permission': {
		title: 'Accueil',
		page: '/web/admin/page/permission.php',
	},
};

function routeError(error) {
	const route = routes[parseInt(error['error_code'])];

	fetch(route.page)
		.then((response) => response.text())
		.then((data) => {
			document.title = route.title;

			const contentContainer = document.getElementById('content-container');
			console.log(data);
			contentContainer.innerHTML = data;

			const errorText = document.getElementById('error-message');
			errorText.innerText = error['error_message'];
		});
}

let dynamicallyLoadedScripts = [];

const handleLocation = async () => {
	const path = window.location.pathname;
	console.log(path);
	const route = routes[path] || routes[404];

	callBeforeRoute(path);

	try {
		updateProgressBar(10);

		const html = await fetch(route.page).then((data) => data.text());
		document.title = route.title;

		updateProgressBar(50);

		dynamicallyLoadedScripts.forEach((script) => script.remove());
		dynamicallyLoadedScripts = [];

		const contentContainer = document.getElementById('content-container');
		contentContainer.innerHTML = html;

		updateProgressBar(90);

		let scripts = contentContainer.querySelectorAll('script[src]');
		scripts.forEach((script) => {
			const newScript = document.createElement('script');
			newScript.src = script.src;
			newScript.async = true;

			script.remove();

			document.body.appendChild(newScript);
			dynamicallyLoadedScripts.push(newScript);
		});

		scripts = contentContainer.querySelectorAll('script');
		scripts.forEach((script) => {
			const newScript = document.createElement('script');
			newScript.textContent = script.textContent;

			script.remove();

			document.body.appendChild(newScript);
			dynamicallyLoadedScripts.push(newScript);
		});

		updateProgressBar(100);
	} catch (error) {
		updateProgressBar(100);
	} finally {
		setTimeout(resetProgressBar, 500);
	}

	callAfterRoute(path);
};

window.onpopstate = handleLocation;
window.route = route;

handleLocation();

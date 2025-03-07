async function apiCall(url, formData, callback) {
	let response = null;

	url = 'http://localhost:8082/' + url;

	if (formData != null) {
		response = await fetch(url, {
			method: 'POST',
			body: formData,
		});
	} else {
		response = await fetch(url, {
			method: 'GET',
		});
	}

	return callback(await response.text());
}

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
	const target = event.currentTarget || event.target;
	window.history.pushState({}, '', target.href);
	handleLocation();
};

const routes = {
	'/web/': {
		title: 'Accueil',
		html: '/web/pages/home.php',
		css: '/web/css/Butify/pages/home.css',
		js: '/web/js/pages/home.js',
	},

	'/web/search': {
		title: 'Recherche',
		html: '/web/pages/search.php',
		css: '/web/css/Butify/pages/search.css',
		js: '/web/js/pages/search.js',
	},

	'/web/home': {
		title: 'Accueil',
		html: '/web/pages/home.php',
		css: '/web/css/Butify/pages/home.css',
		js: '/web/js/pages/home.js',
	},
	'/web/explore': {
		title: 'Explorer',
		html: '/web/pages/explore.php',
		js: '/web/js/pages/explore.js',
	},
	'/web/library': {
		title: 'Bibliothèque',
		html: '/web/pages/library.php',
		css: '/web/css/Butify/pages/library.css',
		js: '/web/js/pages/library.js',
	},
	'/web/account': {
		title: 'Compte',
		html: '/web/pages/user/account.php',
		css: '/web/css/Butify/pages/user/account.css',
		js: '/web/js/pages/user/account.js',
	},

	'/web/upload': {
		title: 'Publier',
		html: '/web/pages/user/upload.php',
		css: '/web/css/Butify/pages/user/upload.css',
		js: '/web/js/pages/user/upload.js',
	},
	'/web/musics': {
		title: 'Gestion des musiques',
		html: '/web/pages/user/musics.php',
		css: '/web/css/Butify/pages/user/musics.css',
		js: '/web/js/pages/user/musics.js',
	},
	'/web/play': {
		title: 'Écoute',
		html: '/web/pages/music/play.php',
	},
	'/web/swipe': {
		title: 'Swipe',
		html: '/web/pages/music/swipe.php',
		css: '/web/css/Butify/pages/music/swipe.css',
		js: '/web/js/pages/music/swipe.js',
	},
	'/web/exposure': {
		title: 'Artiste',
		html: '/web/pages/music/exposure.php',
		css: '/web/css/Butify/pages/music/exposure.css',
		js: '/web/js/pages/music/exposure.js',
	},
	'/web/collection': {
		title: 'Playlist',
		html: '/web/pages/music/collection.php',
		css: '/web/css/Butify/pages/music/collection.css',
		js: '/web/js/pages/music/collection.js',
	},
	'/web/subscription': {
		title: 'Abonnements',
		html: '/web/pages/subscription.php',
		css: '/web/css/Butify/pages/subscription.css',
		js: '/web/js/pages/subscription.js',
	},
};

function routeError(error) {
	if (error['error_code'] === 401 || error['error_code'] === 403) {
		sessionDestroy();
		return;
	}

	fetch('/web/error.html')
		.then((response) => response.text())
		.then((data) => {
			document.title = 'ᕱ UwU ᕱ';

			const contentContainer = document.getElementById('content-container');
			contentContainer.innerHTML = data;

			const errorCode = document.getElementById('error-code');
			errorCode.innerText = 'ERROR ' + error['error_code'];

			const errorText = document.getElementById('error-message');
			errorText.innerText = error['error_message'];
		});
}

let dynamicallyLoadedLink = [];

let dynamicallyLoadedScripts = [];
let activeScriptControllers = [];

async function loadCSS(path) {
	return new Promise((resolve, reject) => {
		const link = document.createElement('link');
		link.rel = 'stylesheet';
		link.href = path;

		link.onload = () => resolve(path);
		link.onerror = () => reject(new Error(`Failed to load CSS: ${cssHref}`));

		document.body.appendChild(link);
		dynamicallyLoadedLink.push(link);
	});
}

async function loadedClear() {
	const contentContainer = document.getElementById('content-container');
	contentContainer.innerHTML = '';

	dynamicallyLoadedLink.forEach((link) => link.remove());
	dynamicallyLoadedLink = [];

	dynamicallyLoadedScripts.forEach((script) => {
		script.remove();
	});
	dynamicallyLoadedScripts = [];
}

function loadJS(path) {
	return new Promise((resolve, reject) => {
		const script = document.createElement('script');
		script.src = path;

		script.onload = () => resolve(path);
		script.onerror = () => reject(new Error(`Script loading aborted:  ${path}`));

		document.body.appendChild(script);
		dynamicallyLoadedScripts.push(script);
	});
}

const handleLocation = async () => {
	let path = window.location.pathname;

	if ((path === '/web/home' || path === '/web/') && !sessionExist()) path = '/web/explore';

	const route = routes[path] || routeError({ error_code: 404, error_message: 'Page introuvable' });

	if (window.callBeforeRoute) callBeforeRoute(path);

	document.getElementById('content-container').remove();
	const contentContainer = document.createElement('div');
	contentContainer.id = 'content-container';

	const mainContainer = document.getElementById('main-container');
	mainContainer.append(contentContainer);

	try {
		updateProgressBar(10);

		loadedClear();

		if (route.css) await loadCSS(route.css);

		updateProgressBar(50);

		const html = await fetch(route.html).then((data) => data.text());
		document.title = route.title;

		contentContainer.innerHTML = html;

		updateProgressBar(90);

		if (route.js) await loadJS(route.js);

		updateProgressBar(100);
	} catch (error) {
		updateProgressBar(100);
	} finally {
		setTimeout(resetProgressBar, 500);
	}

	if (window.callAfterRoute) callAfterRoute(path);
};

window.onpopstate = handleLocation;
window.route = route;

handleLocation();

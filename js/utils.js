/*
 * Mostra messaggio di errore a seguito di validazione
 */
function showErrorMessage(id, message) {
	var element = document.getElementById(id);
	var messageTarget = document.getElementById(id + '-hint');

	removeErrorMessage(id);

	element.classList.add('invalid');
	if (element.tagName != 'DIV') {
		element.setAttribute("aria-invalid", true);
	}

	messageTarget.classList.add("error-message");
	messageTarget.innerHTML = message;
	return;
}

/*
 * Rimuove messaggio di errore a seguito di validazione
 */
function removeErrorMessage(id) {
	var element = document.getElementById(id);
	var messageTarget = document.getElementById(id + '-hint');

	element.classList.remove('invalid');
	if (element.tagName != 'DIV') {
		element.setAttribute("aria-invalid", false);
	}

	messageTarget.classList.remove("error-message");
	messageTarget.innerHTML = '';
	return;
}

/*
 * Pone il focus sul primo errore del form.
 */
function focusOnTopmostError() {
	var invalidFields = document.getElementsByClassName('invalid');
	if (invalidFields) {
		invalidFields[0].focus();
	}
	return;
}

/*
 * Controlla se il browser supporta <input type="date" />
 */
function inputDateBrowserSupport() {
	const fallbackTestElement = document.createElement('input');
	try {
		fallbackTestElement.type = 'date';
	} catch (e) {
		return false;
	}
	return true;
}

/*
 * Gestisce interruttore hamburger menu
 */

var isOpen = false;
function toggleMenu() {
	var btn = document.getElementById("dropdown-menu-toggle");
	var links = document.getElementById("dropdown-link-container");
	isOpen = !isOpen;
	btn.setAttribute("data-open", isOpen);
	links.setAttribute("data-open", isOpen);
}

/*
 * Gestisce bottone back to top
 */

let previousScrollPosition = 10;

function isScrollingDown() {
	let goingDown = false;
	let scrollPosition = window.pageYOffset;
	if (scrollPosition > previousScrollPosition) { goingDown = true };
	previousScrollPosition = scrollPosition;
	return goingDown;
}

function handleScroll() {
	let backToTopLink = document.getElementById("back-to-top");
	if (backToTopLink != null) {
		let viewportHeight = window.innerHeight;
		let scrollPosition = window.pageYOffset;

		if (isScrollingDown() || scrollPosition == 0) {
			backToTopLink.classList.remove('show');
		} else if (isScrollingDown() === false && scrollPosition > viewportHeight/5) {
			backToTopLink.classList.add('show');
		}
	}
};

window.addEventListener("scroll", handleScroll);


/*
 * Gestisce cambio tema alternando chiaro e scuro.
 * Dà priorità al tema dell'utente, se definito.
 */

const theme = (() => {
	// localStorage contiene già la preferenza?
	if (typeof localStorage !== 'undefined' && localStorage.getItem('theme')) {
		return localStorage.getItem('theme');
	}
	// L'utente ha una preferenza?
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
		return 'dark';
	}
	// Fallback
	return 'light';
})();

// Cicla tra le due opzioni
if (theme === 'light') {
	document.documentElement.classList.remove('dark');
} else {
	document.documentElement.classList.add('dark');
}

// Imposta la preferenza iniziale su localStorage
window.localStorage.setItem('theme', theme);

// Applica la classe .dark
const handleThemeSwitch = () => {
	const element = document.documentElement; // L'elemento è la root <html>
	element.classList.toggle("dark");

	const isDark = element.classList.contains("dark");
	localStorage.setItem("theme", isDark ? "dark" : "light");
}

// Aggiunge funzionalità al bottone
const button = document.getElementById("themeToggle");
if (button != null) button.addEventListener("click", handleThemeSwitch);

/*
 * Mostra filtri di ricerca solo quando necessario.
 */
function toggleRelevantSearchFilters() {

	if ( document.getElementById('tipo-filtro') ) {
		var type = document.getElementById('tipo-filtro').value;
		var inputGenere = document.getElementById('filtro-genere');
		var labelGenere = document.getElementById('filtro-genere-label');
		var inputPaese = document.getElementById('filtro-paese');
		var labelPaese = document.getElementById('filtro-paese-label');

		if (type == '') {
			inputGenere.hidden = true;
			labelGenere.hidden = true;
			inputPaese.hidden = true;
			labelPaese.hidden = true;
		}
		if (type == 'genere') {
			inputGenere.hidden = false;
			labelGenere.hidden = false;
			inputPaese.hidden = true;
			labelPaese.hidden = true;
		}
		if (type == 'paese') {
			inputPaese.hidden = false;
			labelPaese.hidden = false;
			inputGenere.hidden = true;
			labelGenere.hidden = true;
		}
	}
}

/*
 * Rappresenta un numero in (Mega/Kilo) Bytes.
 */
function returnFileSize(number) {
	if (number < 1024) {
		return `${number} bytes`;
	} else if (number >= 1024 && number < 1048576) {
		return `${(number / 1024).toFixed(1)} KB`;
	} else if (number >= 1048576) {
		return `${(number / 1048576).toFixed(1)} MB`;
	}
}

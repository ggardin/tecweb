/*
 * Mostra messaggio di errore a seguito di validazione
 */
function showErrorMessage(id, message) {
	var element = document.getElementById(id);
	var messageTarget = document.getElementById(id + '-hint');

	removeErrorMessage(id);

	element.setAttribute("aria-invalid", true);
	element.classList.add('invalid');

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

	element.setAttribute("aria-invalid", false);
	element.classList.remove('invalid');

	messageTarget.classList.remove("error-message");
	messageTarget.innerHTML = '';
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
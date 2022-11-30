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

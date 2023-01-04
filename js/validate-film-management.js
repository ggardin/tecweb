/*
 * Richiama validatori del form di aggiornamento dati film
 */
function validateMovie() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( true ) {
			event.preventDefault();
		}
	});
}

/*
 * Avvisa se il budget indicato è oltre la soglia
 */
function validateMovieBudget() {
	validateMoney("budget");
}

/*
 * Avvisa se il budget indicato è oltre la soglia
 */
function validateMovieBoxOfficeEarnings() {
	validateMoney("incassi");
}

/*
 * Avvisa se la cifra indicata è oltre la soglia
 */
function validateMoney(name) {
	if ( document.forms['gestione'][name].value > 1000000 ) {
		alert('La cifra indicata in ' + name + ' è molto alta. Puoi verificare che il dato immesso sia corretto?')
	}
}

window.addEventListener('load', function () {
	validateMovie();
});
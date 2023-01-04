/*
 * Richiama validatori del form di aggiornamento dati film
 */
function validateMovie() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( validateMovieReleaseDate() && validateMovieRuntime() ) {
			event.preventDefault();
		}
	});
}

/*
 * Verifica che:
*	1. la data fornita sia una data valida
 *	2. la data rispetti i bound impostati in HTML
 *
 * Se <input type="date" /> non è disponibile, c'è fallback su type = "text".
 * Per questa ragione è stata definito pattern dd/mm/yyyy sull'input.
 * NB: la data divisa da "/" è un formato di localizzazione.
 *     Il browser utilizza il separatore "-".
 */
 function validateMovieReleaseDate() {
	var releaseDate = document.forms['gestione']['data'].value;
	var dateLowerBound = new Date(document.forms['gestione']['data'].min);
	var dateUpperBound = new Date(document.forms['gestione']['data'].max);

	// Controlla che ci sia una stringa
	if (releaseDate == null || releaseDate == '') {
		alert('Data di rilascio non inserita');
		return false;
	}

	// Non c'è fallback
	if (inputDateBrowserSupport()) {
		var dateOfRelease = new Date(releaseDate);
	}
	// Se c'è fallback, sto ricevendo una stringa potenzialmente non formattata
	else {
		const yearRegex = /(((0|1)[0-9]|2[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/((19|20)\d\d))$/;
		// Controllo che sia nel formato dd/mm/yyyy
		if (yearRegex.test(releaseDate)) {
			var parts = releaseDate.split("/");
			var dateOfRelease = new Date(parts[2], parts[1], parts[0]);
		}
		else {
			alert('Formato della data non corretto. Usa dd/mm/yyyy');
			return false;
		}
	}

	// Controlla se la data è inferiore al limite minimo
	if (dateOfRelease.getTime() < dateLowerBound.getTime()) {
		alert('Data immessa antecedente al limite minimo');
		return false;
	}
	// Controlla se la data è superiore al limite massimo
	if (dateOfRelease.getTime() > dateUpperBound.getTime()) {
		alert('Data immessa successiva al limite massimo');
		return false;
	}

	return true;
}

/*
 * Contralla se il browser supporta <input type="date" />
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
 * Valida durata.
 * Avvisa se la durata in minuti è troppo alta
 */
function validateMovieRuntime() {
	var runtime = document.forms['gestione']['durata'].value;

	// se durata negativa, segnala errore
	if (runtime <= 0) {
		alert('Durata in minuti inferiore a 0 minuti');
		return false;
	}
	// se durata elevata, segnala possibile errore ma non blocca
	else if ( runtime < 1000 && runtime > 240 ) {
		alert('La cifra indicata in durata è elevata. Puoi verificare che il dato immesso sia corretto?');
	}
	// se durata oltre soglia, segnala errore
	else if (runtime > 1000) {
		alert('Durata in minuti superiore a 1000 minuti');
		return false;
	}
	return true;
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
		alert('La cifra indicata in ' + name + ' è molto alta. Puoi verificare che il dato immesso sia corretto?');
	}
}

window.addEventListener('load', function () {
	validateMovie();
});
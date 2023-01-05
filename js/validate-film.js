/*
 * Richiama validatori del form di aggiornamento dati film
 */
function validateMovie() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( validateMovieReleaseDate() && validateMovieRuntime() && validateMovieBudget() && validateMovieBoxOfficeEarnings() ) {
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
	var id = 'data';
	var releaseDate = document.forms['gestione']['data'].value;
	var dateLowerBound = new Date(document.forms['gestione']['data'].min);
	var dateUpperBound = new Date(document.forms['gestione']['data'].max);

	// Controlla che ci sia una stringa
	if (releaseDate == null || releaseDate == '') {
		showErrorMessage(id, 'Data di rilascio non inserita.');
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
			showErrorMessage(id, 'Formato della data non corretto. Usa dd/mm/yyyy.');
			return false;
		}
	}

	// Controlla se la data è inferiore al limite minimo
	if (dateOfRelease.getTime() < dateLowerBound.getTime()) {
		showErrorMessage(id, 'Data immessa antecedente al limite minimo.');
		return false;
	}
	// Controlla se la data è superiore al limite massimo
	if (dateOfRelease.getTime() > dateUpperBound.getTime()) {
		showErrorMessage(id, 'Data immessa successiva al limite massimo.');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Valida durata.
 * Avvisa se la durata in minuti è troppo alta
 */
function validateMovieRuntime() {
	var id = 'durata';
	var runtime = document.forms['gestione']['durata'].value;

	// se durata negativa, segnala errore
	if (runtime <= 0) {
		showErrorMessage(id, "Durata in minuti inferiore a 0 minuti.");
		return false;
	}
	// se durata oltre soglia, segnala errore
	else if (runtime > 1000) {
		showErrorMessage(id, "Durata in minuti superiore a 1000 minuti.");
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Avvisa se il budget indicato è oltre la soglia
 */
function validateMovieBudget() {
	validateMoney('budget');
}

/*
 * Avvisa se il budget indicato è oltre la soglia
 */
function validateMovieBoxOfficeEarnings() {
	validateMoney('incassi');
}

/*
 * Valida la cifra
 */
function validateMoney(id) {
	if ( document.forms['gestione'][id].value <= 0 ) {
		showErrorMessage(id, 'La cifra non può essere inferiore a 0.');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

window.addEventListener('load', function () {
	validateMovie();
});

var instance = 0;

function addNewCrewMember(element) {
	// Create new input fields
	var newNameLabel = document.createElement("label");
	var newNameInput = document.createElement("input");
	var newRoleLabel = document.createElement("label");
	var newRoleSelect = document.createElement("select");

	newNameInput.id = "crew-name" + instance;
	newNameInput.name = "crew-name" + instance;
	newNameInput.type = "text";

	newRoleSelect.id = "crew-role" + instance;
	newRoleSelect.name = "crew-role" + instance;

	newNameLabel.htmlFor = "crew-name" + instance;
	newNameLabel.innerHTML = "Nome e cognome";

	newRoleLabel.htmlFor = "crew-role" + instance;
	newRoleLabel.innerHTML = "Ruolo";

	element.appendChild(newNameInput, element.previousSibling.previousSibling);
	element.insertBefore(newNameLabel, newNameInput);

	element.appendChild(newRoleSelect, element.previousSibling.previousSibling);
	element.insertBefore(newRoleLabel, newRoleSelect);

	instance++;
}

function removeLastCrewMember(element) {

	element.removeChild(element.lastChild);
	element.removeChild(element.lastChild);
	element.removeChild(element.lastChild);
	element.removeChild(element.lastChild);

	instance--;
}
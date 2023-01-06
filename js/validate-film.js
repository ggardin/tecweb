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
	initiateInstanceCount();
	validateMovie();
});

/*
 * =================================
 * CREW MEMBERS
 * =================================
 */

var instance = 0;

/*
 * Ottiene il numero di membri della crew già presenti
 */
function initiateInstanceCount() {
	instance = document.querySelectorAll('.crew-member').length;
}

/*
 * Aggiunge i campi usati per l'inserimento dei dati di un nuovo membro
 */
function addNewCrewMember(element) {
	// Crea i nuovi elementi
	var newCrewNameLabel = document.createElement("label");
	var newCrewNameInput = document.createElement("input");
	var newCrewRoleLabel = document.createElement("label");
	var newCrewRoleSelect = document.createElement("select");
	var newCrewRoleRegista = document.createElement("option");
	var newCrewRoleSceneggiatore = document.createElement("option");
	var newCrewRoleProduttore = document.createElement("option");
	var newCrewRoleCompositore = document.createElement("option");

	// <label for="crew-name0">Nome e cognome</label>
	newCrewNameLabel.htmlFor = "crew-name" + instance;
	newCrewNameLabel.innerHTML = "Nome e cognome";
	newCrewNameLabel.classList.add('crew-member');

	// <input id="crew-name0" name="crew-name0" type="text">
	newCrewNameInput.id = "crew-name" + instance;
	newCrewNameInput.name = "crew-name" + instance;
	newCrewNameInput.type = "text";

	// <label for="crew-role0">Ruolo</label>
	newCrewRoleLabel.htmlFor = "crew-role" + instance;
	newCrewRoleLabel.innerHTML = "Ruolo";

	// <select id="crew-role0" name="crew-role0"></select>
	newCrewRoleSelect.id = "crew-role" + instance;
	newCrewRoleSelect.name = "crew-role" + instance;
	newCrewRoleRegista.value = 0;
	newCrewRoleRegista.text = "Regista";
	newCrewRoleSceneggiatore.value = 1;
	newCrewRoleSceneggiatore.text = "Sceneggiatore"
	newCrewRoleProduttore.value = 2;
	newCrewRoleProduttore.text = "Produttore";
	newCrewRoleCompositore.value = 3;
	newCrewRoleCompositore.text = "Compositore";

	// Innesta gli elementi nel nodo padre
	element.appendChild(newCrewNameInput, element.previousSibling.previousSibling);
	element.insertBefore(newCrewNameLabel, newCrewNameInput);

	// Innesta gli elementi nel nodo padre
	element.appendChild(newCrewRoleSelect, element.previousSibling.previousSibling);
	var select = document.getElementById("crew-role" + instance);
	select.add(newCrewRoleRegista);
	select.add(newCrewRoleSceneggiatore);
	select.add(newCrewRoleProduttore);
	select.add(newCrewRoleCompositore);
	element.insertBefore(newCrewRoleLabel, newCrewRoleSelect);

	// Aggiorna il numero di istanze e il contatore
	instance++;
	updateCrewHint();
}

function removeLastCrewMember(element) {
	// Cancella solo se ci sono istanze
	if (instance > 0) {
		element.removeChild(element.lastChild);
		element.removeChild(element.lastChild);
		element.removeChild(element.lastChild);
		element.removeChild(element.lastChild);
		instance--;
	}
	updateCrewHint();
}

function updateCrewHint() {
	const hint = document.getElementById("crew-hint");

	if (instance == 0) {
		hint.innerHTML = "Non è stato definito alcun membro della <span lang=\"en\">crew</span>.";
	}
	else {
		hint.innerHTML = (instance > 1 ? instance + " membri." : instance + " membro.");
	}
}
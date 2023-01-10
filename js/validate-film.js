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
	if (releaseDate == null) {
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
	if (runtime != "" && runtime <= 0) {
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
	if ( document.forms['gestione'][id].value != "" && document.forms['gestione'][id].value <= 0 ) {
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

var instanceCrew = 0;
var instanceNations = 0;

/*
 * Ottiene il numero di membri della crew e nazioni già presenti
 */
function initiateInstanceCount() {
	instanceCrew = document.querySelectorAll('.crew-member').length;
	instanceNations = document.querySelectorAll('.nation').length;
}

/*
 * =================================
 * CREW MEMBERS
 * =================================
 */


/*
 * Aggiunge i campi usati per l'inserimento dei dati di un nuovo membro
 */
function addNewCrewMember(element) {
	// Elementi per #nome
	var newCrewNameLabel = document.createElement("label");
	var newCrewNameInput = document.createElement("input");
	// Elementi per #crew
	var newCrewRoleLabel = document.createElement("label");
	var newCrewRoleSelect = document.createElement("select");
	var newCrewRoleRegista = document.createElement("option");
	var newCrewRoleSceneggiatore = document.createElement("option");
	var newCrewRoleProduttore = document.createElement("option");
	var newCrewRoleCompositore = document.createElement("option");
	// Elementi per tasto "elimina"
	var newCrewDeleteInput = document.createElement("input");

	// <label for="crew-name0">Nome e cognome</label>
	newCrewNameLabel.htmlFor = "crew-name" + instanceCrew;
	newCrewNameLabel.innerHTML = "Nome e cognome";
	newCrewNameLabel.classList.add('crew-member');

	// <input id="crew-name0" name="crew-name0" type="text">
	newCrewNameInput.id = "crew-name" + instanceCrew;
	newCrewNameInput.name = "crew-name" + instanceCrew;
	newCrewNameInput.type = "text";

	// <label for="crew-role0">Ruolo</label>
	newCrewRoleLabel.htmlFor = "crew-role" + instanceCrew;
	newCrewRoleLabel.innerHTML = "Ruolo";

	// <select id="crew-role0" name="crew-role0"></select>
	newCrewRoleSelect.id = "crew-role" + instanceCrew;
	newCrewRoleSelect.name = "crew-role" + instanceCrew;
	newCrewRoleRegista.value = 0;
	newCrewRoleRegista.text = "Regista";
	newCrewRoleSceneggiatore.value = 1;
	newCrewRoleSceneggiatore.text = "Sceneggiatore"
	newCrewRoleProduttore.value = 2;
	newCrewRoleProduttore.text = "Produttore";
	newCrewRoleCompositore.value = 3;
	newCrewRoleCompositore.text = "Compositore";

	// <input id="crew-delete0" type="button" onclick="removeCrewMember(this);" value="Elimina" />
	newCrewDeleteInput.id = "crew-delete" + instanceCrew;
	newCrewDeleteInput.type = "button";
	newCrewDeleteInput.value = "Elimina";
	newCrewDeleteInput.onclick = function() { removeCrewMember(this); };

	// Innesta gli elementi di #crew-nome
	console.log(element)
	element.insertAdjacentElement('beforebegin', newCrewNameLabel);
	element.insertAdjacentElement('beforebegin', newCrewNameInput);

	// Innesta gli elementi di #crew-role
	element.insertAdjacentElement('beforebegin', newCrewRoleLabel);
	element.insertAdjacentElement('beforebegin', newCrewRoleSelect);
	var select = document.getElementById("crew-role" + instanceCrew);
	select.add(newCrewRoleRegista);
	select.add(newCrewRoleSceneggiatore);
	select.add(newCrewRoleProduttore);
	select.add(newCrewRoleCompositore);

	// Innesta tasto per eliminare
	element.insertAdjacentElement('beforebegin', newCrewDeleteInput);

	// Aggiorna il numero di istanze e il contatore
	instanceCrew++;
	updateCrewHint();
}

/*
 * Rimuove il membro della crew corrispondente.
 */
function removeCrewMember(element) {
	element.previousSibling.remove();
	element.previousSibling.remove();
	element.previousSibling.remove();
	element.previousSibling.remove();
	element.remove();
	instanceCrew--;
	updateCrewHint();
}

/*
 * Aggiorna il suggerimento che riporta il numero di membri della crew.
 */
function updateCrewHint() {
	const hint = document.getElementById("crew-hint");

	if (instanceCrew == 0) {
		hint.innerHTML = "Non è stato definito alcun membro.";
	}
	else {
		hint.innerHTML = (instanceCrew > 1 ? instanceCrew + " membri." : instanceCrew + " membro.");
	}
}

/*
 * =================================
 * NATIONS
 * =================================
 */

/*
 * Aggiunge i campi usati per l'inserimento dei dati di un nuovo membro
 */
function addNewNation(element) {
	// Elementi per #nome
	var newNationLabel = document.createElement("label");
	var newNationInput = document.createElement("input");
	// Elementi per tasto "elimina"
	var newNationDeleteInput = document.createElement("input");

	// <label for="nation-name0">Nome del Paese</label>
	newNationLabel.htmlFor = "nation-name" + instanceNations;
	newNationLabel.innerHTML = "Nome del Paese";
	newNationLabel.classList.add('nation');

	// <input id="nation-name0" name="nation-name0" type="text">
	newNationInput.id = "nation-name" + instanceNations;
	newNationInput.name = "nation-name" + instanceNations;
	newNationInput.type = "text";
	newNationInput.setAttribute("list", "lista-paesi");

	// <input id="nation-delete0" type="button" onclick="removeNation(this);" value="Elimina" />
	newNationDeleteInput.id = "nation-delete" + instanceNations;
	newNationDeleteInput.type = "button";
	newNationDeleteInput.value = "Elimina";
	newNationDeleteInput.onclick = function() { removeNation(this); };

	// Innesta gli elementi di #nation-nome
	console.log(element)
	element.insertAdjacentElement('beforebegin', newNationLabel);
	element.insertAdjacentElement('beforebegin', newNationInput);

	// Innesta tasto per eliminare
	element.insertAdjacentElement('beforebegin', newNationDeleteInput);

	// Aggiorna il numero di istanze e il contatore
	instanceNations++;
	updateNationHint();
}

/*
 * Rimuove il membro della nation corrispondente.
 */
function removeNation(element) {
	element.previousSibling.remove();
	element.previousSibling.remove();
	element.remove();
	instanceNations--;
	updateNationHint();
}

/*
 * Aggiorna il suggerimento che riporta il numero di membri della nation.
 */
function updateNationHint() {
	const hint = document.getElementById("nation-hint");

	if (instanceNations == 0) {
		hint.innerHTML = "Non è stato definito alcun Paese.";
	}
	else {
		hint.innerHTML = (instanceNations > 1 ? instanceNations + " Paesi." : instanceNations + " Paese.");
	}
}
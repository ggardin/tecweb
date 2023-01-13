/*
 * Richiama validatori del form di aggiornamento dati film
 */
function validateMovie() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( !(validateMovieReleaseDate() && validateMovieRuntime() && validateMovieBudget() && validateMovieBoxOfficeEarnings()) ) {
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
	updateCrewCounter();
	updateNationsCounter();
	updateGenresHint();
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
	newCrewNameInput.setAttribute("list", "lista-persone");

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
	updateCrewCounter();
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
	updateCrewCounter();
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
 * Aggiorna il contatore dei membri.
 */
function updateCrewCounter() {
	const counter = document.getElementById('crew-count');
	counter.value = instanceCrew;
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
	// Elemento da duplicare
	var original = document.getElementById('nation-sample');

	// Duplicato
	var clone = original.cloneNode(true);
	var label = clone.getElementsByTagName('label')[0];
	var input = clone.getElementsByTagName('input')[0];
	var button = clone.getElementsByTagName('input')[1];

	// Incrementa contatore
	instanceNations++;

	// Aggiorna id
	clone.removeAttribute('id');
	input.id = 'nation-name' + instanceNations;

	// Rimuove attributo hidden
	clone.removeAttribute('hidden');

	// Aggiunge classe nation
	clone.classList.add('nation');

	// Aggiorna for
	label.setAttribute('for', 'nation-name' + instanceNations);

	// Innesta
	element.insertAdjacentElement('beforebegin', clone);

	// Aggiorna il numero di istanze e il contatore
	updateNationsCounter();
	updateNationHint();
}

/*
 * Rimuove il membro della nation corrispondente.
 */
function removeNation(element) {
	element.parentNode.remove();
	instanceNations--;
	updateNationsCounter();
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

/*
 * Aggiorna il contatore dei Paesi.
 */
function updateNationsCounter() {
	const counter = document.getElementById('nations-count');
	counter.value = instanceNations;
}

/*
 * =================================
 * GENRES
 * =================================
 */

/*
 * Aggiorna il suggerimento che riporta il numero di generi selezionati.
 */
function updateGenresHint() {
	const hint = document.getElementById('genre-hint');
	var count = countGenres();

	if (count == 0) {
		hint.innerHTML = "Non è stato selezionato alcun genere cinematografico.";
	}
	else {
		hint.innerHTML = (count > 1 ? count + " generi." : count + " genere.");
	}
}

/*
 * Aggiorna il contatore dei generi selezionati.
 */
function countGenres() {
	const checkboxes = document.getElementsByName('genere');
	var count = 0;

	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked == true) {
			count++;
		}
	}

	return count;
}
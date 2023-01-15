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
		showErrorMessage(id, "La durata del film non può valere meno di 1 minuto.");
		return false;
	}
	// se durata oltre soglia, segnala errore
	else if (runtime > 1000) {
		showErrorMessage(id, "La durata del film non può superare i 1000 minuti.");
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Avvisa se il budget indicato è oltre la soglia
 */
function validateMovieBudget() {
	return validateMoney('budget');
}

/*
 * Avvisa se il budget indicato è oltre la soglia
 */
function validateMovieBoxOfficeEarnings() {
	return validateMoney('incassi');
}

/*
 * Valida la cifra
 */
function validateMoney(id) {
	if ( document.forms['gestione'][id].value != '' && document.forms['gestione'][id].value <= 0 ) {
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
var clicksOnAddButtonCrew = 0;
var clicksOnAddButtonNation = 0;

/*
 * Ottiene il numero di membri della crew e nazioni già presenti
 */
function initiateInstanceCount() {
	// Inizializza contatori per Crew member
	instanceCrew = document.querySelectorAll('.crew-member').length;
	clicksOnAddButtonCrew = document.querySelectorAll('.crew-member').length;
	// Inizializza contatori per Nation
	instanceNations = document.querySelectorAll('.nation').length;
	clicksOnAddButtonNation = document.querySelectorAll('.nation').length;

	// Aggiorna hint e contatori
	updateCrewCounter();
	updateNationsCounter();
	updateCrewHint();
	updateNationHint();
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
	// Elemento da duplicare
	var original = document.getElementById('crew-sample');

	// Duplicato
	var clone = original.cloneNode(true);
	var personLabel = clone.getElementsByTagName('label')[0];
	var personInput = clone.getElementsByTagName('input')[0];
	var roleLabel = clone.getElementsByTagName('label')[1];
	var roleSelect = clone.getElementsByTagName('select')[0];

	// Aggiorna id
	clone.removeAttribute('id');
	personInput.id = 'crew-person' + clicksOnAddButtonCrew;
	roleSelect.id = 'crew-role' + clicksOnAddButtonCrew;

	// Aggiunge classe crew
	clone.classList.add('crew-member');

	// Aggiorna for
	personLabel.setAttribute('for', 'crew-person' + clicksOnAddButtonCrew);
	roleLabel.setAttribute('for', 'crew-role' + clicksOnAddButtonCrew);

	// Imposta name per PHP
	personInput.setAttribute('name', 'crew-person[]');
	roleSelect.setAttribute('name', 'crew-role[]');

	// Innesta
	element.insertAdjacentElement('beforebegin', clone);

	// Incrementa contatori
	instanceCrew++;
	clicksOnAddButtonCrew++;

	// Rimuove attributo hidden
	clone.removeAttribute('hidden');

	// Aggiorna il contatore
	updateCrewCounter();
	updateCrewHint();
}

/*
 * Rimuove il membro della crew corrispondente.
 */
function removeCrewMember(element) {
	element.parentNode.remove();
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

	// Aggiorna id
	clone.removeAttribute('id');
	input.id = 'nation-name' + clicksOnAddButtonNation;

	// Aggiunge classe nation
	clone.classList.add('nation');

	// Aggiorna for
	label.setAttribute('for', 'nation-name' + clicksOnAddButtonNation);

	// Imposta name per PHP
	input.setAttribute('name', 'nation[]');

	// Innesta
	element.insertAdjacentElement('beforebegin', clone);

	// Incrementa contatori
	instanceNations++;
	clicksOnAddButtonNation++;

	// Rimuove attributo hidden
	clone.removeAttribute('hidden');

	// Aggiorna il contatore
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
	const checkboxes = document.getElementsByName('genere[]');
	var count = 0;

	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked == true) {
			count++;
		}
	}

	return count;
}
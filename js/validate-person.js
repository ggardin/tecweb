/*
 * Richiama validatori del form di aggiornamento dati persona
 */
function validatePerson() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( !(validatePersonName() && validatePersonDateOfBirth() && validatePersonDateOfDeath() && comparePersonDates() && validateFileUpload()) ) {
			focusOnTopmostError();
			event.preventDefault();
		}
	});
}

/*
 * Valida il nome della persona
 */
function validatePersonName() {
	var id = 'nome';
	var title = document.forms['gestione'][id].value;

	if (title == null || title == '') {
		showErrorMessage(id, 'Nome è un campo richiesto.');
		return false;
	}
	else {
		const titleRegex = /^[a-zA-Z\.\s\-\'\[\]\/\u00C0-\u017F]+$/;
		if (! titleRegex.test(title)) {
			showErrorMessage(id, 'Il nome inserito contiene caratteri non ammessi.');
			return false;
		}
	}

	removeErrorMessage(id);
	return true;
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
 function validatePersonDate(date, event) {
	var id = 'data_' + event;
	var dateLowerBound = new Date(document.forms['gestione']['data_' + event].min);
	var dateUpperBound = new Date(document.forms['gestione']['data_' + event].max);
	var today = new Date();

	// Controlla che ci sia una stringa
	if (date == null || date == '') {
		removeErrorMessage(id);
		return true;
	}

	// Non c'è supporto data, controllo formato
	if (! inputDateBrowserSupport()) {
		const yearRegex = /^([\d]{4})\-(0[1-9]|1[0-2])\-((0|1)[0-9]|2[0-9]|3[0-1])$/;
		if (! yearRegex.test(date)) {
			showErrorMessage(id, 'Data di ' + event + ' non corretta. Usa il formato YYYY-MM-DD.');
			return false;
		}
	}

	var dateOfEvent = new Date(date);

	// Controlla se la data è inferiore al limite minimo
	if (dateOfEvent.getTime() < dateLowerBound.getTime()) {
		showErrorMessage(id, 'Data di ' + event + ' immessa antecedente al limite minimo.');
		return false;
	}
	// Controlla se la data è superiore ad oggi
	if (dateOfEvent.getTime() > today.getTime()) {
		showErrorMessage(id, 'Data di ' + event + ' successiva alla data odierna.');
		return false;
	}
	// Controlla se la data è superiore al limite massimo
	if (dateOfEvent.getTime() > dateUpperBound.getTime()) {
		showErrorMessage(id, 'Data di ' + event + ' immessa successiva al limite massimo.');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

function validatePersonDateOfBirth() {
	var birth = document.forms['gestione']['data_nascita'].value;
	return validatePersonDate(birth, 'nascita');
}

function validatePersonDateOfDeath() {
	var death = document.forms['gestione']['data_morte'].value;
	return validatePersonDate(death, 'morte');
}

/*
 * Confronta le date di nascita e di morte.
 */
function comparePersonDates() {

	// Le date sono state entrambe inserite e validate
	if (validatePersonDateOfBirth() && validatePersonDateOfDeath()) {
		var id = 'data_morte';
		var birth = document.forms['gestione']['data_nascita'].value;
		var death = document.forms['gestione']['data_morte'].value;
		var lifeExpectancy = 120;

		var dateOfBirth = new Date(birth);
		var dateOfDeath = new Date(death);

		// Confronto le date
		if (dateOfBirth.getTime() >= dateOfDeath.getTime()) {
			showErrorMessage(id, 'Le data di morte indicata è antecedente a quella di nascita.')
			return false;
		}
		// Confronto le date
		if (dateOfDeath.getFullYear() - dateOfBirth.getFullYear() >= lifeExpectancy) {
			showErrorMessage(id, 'Le date indicate sono troppo distanti fra loro.')
			return false;
		}

		removeErrorMessage(id);
	}

	return true;
}

/*
 * Valida l'immagine caricata.
 */
function validateFileUpload() {
	var id = 'immagine';
	var input = document.forms['gestione'][id];
	const maxUploadSize = 1572864; // 1.5 MB (base 2)
	const allowedMimeTypes = ["image/jpeg", "image/png", "image/webp"];

	if (input.files.length > 0) {
		var file = input.files[0];
		// Filtro MIME type per <input> non ha supporto completo. Controlla MIME
		if (! allowedMimeTypes.includes(file.type) ) {
			showErrorMessage(id, "Il <span lang=\"en\">file</span> selezionato non è un'immagine <abbr>JPG</abbr>, <abbr>PNG</abbr> o <abbr>WEBP</abbr>." );
			return false;
		}

		// È un'immagine. Controlla dimensioni
		var size = file.size;
		if (size > maxUploadSize) {
			showErrorMessage(id, "L'immagine caricata ha dimensione di " + returnFileSize(file.size) + ". Il limite massimo di caricamento è " + returnFileSize(maxUploadSize) + "." );
			return false;
		}
	}
	removeErrorMessage(id);
	return true;
}

window.addEventListener('load', function () {
	validatePerson();
});
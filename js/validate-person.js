/*
 * Richiama validatori del form di aggiornamento dati persona
 */
function validatePerson() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( validatePersonDateOfBirth() && validatePersonDateOfDeath() && comparePersonDates() ) {
			event.preventDefault();
		}
	});
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
	var dateLowerBound = new Date(document.forms['gestione']['data'].min);
	var dateUpperBound = new Date(document.forms['gestione']['data'].max);

	// Controlla che ci sia una stringa
	if (date == null || date == '') {
		alert('Data di ' + event + ' non inserita');
		return false;
	}

	// Non c'è fallback
	if (inputDateBrowserSupport()) {
		var dateOfEvent = new Date(date);
	}
	// Se c'è fallback, sto ricevendo una stringa potenzialmente non formattata
	else {
		const yearRegex = /(((0|1)[0-9]|2[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/((19|20)\d\d))$/;
		// Controllo che sia nel formato dd/mm/yyyy
		if (yearRegex.test(date)) {
			var parts = date.split("/");
			var dateOfEvent = new Date(parts[2], parts[1], parts[0]);
		}
		else {
			alert('Formato della data di ' + event + ' non corretto. Usa dd/mm/yyyy');
			return false;
		}
	}

	// Controlla se la data è inferiore al limite minimo
	if (dateOfEvent.getTime() < dateLowerBound.getTime()) {
		alert('Data di ' + event + ' immessa antecedente al limite minimo');
		return false;
	}
	// Controlla se la data è superiore al limite massimo
	if (dateOfEvent.getTime() > dateUpperBound.getTime()) {
		alert('Data di ' + event + ' immessa successiva al limite massimo');
		return false;
	}

	return true;
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
 * Confronta le date di nascita e di morte.
 */
function comparePersonDates() {
	// Le date sono state entrambe inserite e validate
	if (validatePersonDateOfBirth() && validatePersonDateOfDeath()) {
		var birth = document.forms['gestione']['data_nascita'].value;
		var death = document.forms['gestione']['data_morte'].value;

		// Non c'è fallback, procedo
		if (inputDateBrowserSupport()) {
			var dateOfBirth = new Date(birth);
			var dateOfDeath = new Date(death);
		}
		// Se c'è fallback, splitto la stringa
		else {
			var dateOfBirth = new Date(birth.split("/")[2], birth.split("/")[1], birth.split("/")[0]);
			var dateOfDeath = new Date(death.split("/")[2], death.split("/")[1], death.split("/")[0]);
		}

		// Confronto le date
		if (dateOfBirth.getTime() >= dateOfDeath.getTime()) {
			alert('Le date non sono coerenti')
			return false;
		}
	}

	return true;
}

window.addEventListener('load', function () {
	validatePerson();
});
/*
 * Richiama validatori del form di modifica dati utente
 */
function validateUserData() {

	let form = document.getElementById("update-user-data");

	form.addEventListener("submit", function (event) {
		if ( validateUserUsername() && validateUserName() && validateUserEmail() && validateUserBirthday() ) {
			event.preventDefault();
		}
	});
}

/*
 * Verifica che il nome utente (username) contenga solo lettere A~z o numeri
 */
function validateUserUsername() {
	var id = 'username';
	var username = document.forms['update-user-data']['username'].value;
	const allowedChars = /^[A-Za-z0-9]+$/; // lettere maiuscole e minuscole, numeri
	if (!allowedChars.test(username)) {
		showErrorMessage(id, 'Nome utente non valido, usa solo lettere');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che il nome dell'utente sia valido
 */
function validateUserName() {
	var id = 'nome';
	var name = document.forms['update-user-data']['nome'].value;
	const allowedChars = /^[A-Za-z\s'][^\d]*$/; // lettere, spazi, apostrofi
	if (!allowedChars.test(name)) {
		showErrorMessage(id, 'Nome non valido');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che:
*	1. la data fornita sia una data valida
 *	2. utente abbia età compresa fra 13 e 100 anni
 *
 * Se <input type="date" /> non è disponibile, c'è fallback su type = "text".
 * Per questa ragione è stata definito pattern dd/mm/yyyy sull'input.
 * NB: la data divisa da "/" è un formato di localizzazione.
 *     Il browser utilizza il separatore "-".
 */
function validateUserBirthday() {
	var id = 'data';
	var birthday = document.forms['update-user-data']['data'].value;
	var today = new Date();

	// Controlla che ci sia una stringa
	if (birthday == null || birthday == '') {
		showErrorMessage(id, 'Data di nascita non inserita');
		return false;
	}

	// Non c'è fallback
	if (inputDateBrowserSupport()) {
		var dateOfBirth = new Date(birthday);
	}
	// Se c'è fallback, sto ricevendo una stringa potenzialmente non formattata
	else {
		const yearRegex = /(((0|1)[0-9]|2[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/((19|20)\d\d))$/;
		// Controllo che sia nel formato dd/mm/yyyy
		if (yearRegex.test(birthday)) {
			var parts = birthday.split("/");
			var dateOfBirth = new Date(parts[2], parts[1], parts[0]);
		}
		else {
			showErrorMessage(id, 'Formato della data non corretto. Usa dd/mm/yyyy');
			return false;
		}
	}

	// Ho a disposizione la data di nascita
	var age = today.getFullYear() - dateOfBirth.getFullYear();

	// controlla che l'utente abbia almeno 13 anni
	if (age < 13) {
		showErrorMessage(id, 'Devi avere almeno 13 anni. Aspetta di crescere, oppure fingi come tutti i minorenni che usano TikTok.');
		return false;
	}
	// se la differenza è 13 potrebbe comunque non averli ancora compiuti
	else if (age == 13) {
		if (today.getMonth() < dateOfBirth.getMonth()) {
			showErrorMessage(id, 'Devi avere almeno 13 anni. Aspetta di crescere, oppure fingi come tutti i minorenni che usano TikTok.');
			return false;
		}
		else if (today.getMonth() == dateOfBirth.getMonth()) {
			if (today.getDate() < dateOfBirth.getDate()) {
				showErrorMessage(id, 'Devi avere almeno 13 anni. Aspetta di crescere, oppure fingi come tutti i minorenni che usano TikTok.');
				return false;
			}
		}
	}
	// controlla che l'utente non abbia più di 100 anni
	else if (age > 100) {
		showErrorMessage(id, 'Guarda, non è per fare i guastafeste, ma non ti sembra di essere in là con gli anni?');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che la stringa sia una mail
 */
function validateUserEmail() {
	var id = 'email';
	var email = document.forms['update-user-data']['email'].value;
	if (email == null || email == '') {
		showErrorMessage(id, 'Nessuna data inserita');
		return false;
	}
	if (!validateEmail(email)) {
		showErrorMessage(id, 'Non è una email');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

function validateEmail(email) {
	const emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return emailRegex.test(String(email).toLowerCase());
}

function validateUserDisplayName() {}

window.addEventListener('load', function () {
	validateUserData();
});
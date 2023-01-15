/*
 * Richiama validatori del form di modifica dati utente
 */
function validateUserData() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if (!( validateUserUsername() && validateUserName() && validateUserEmail() && validateUserBirthday() && validatePassword() && validatePasswordConfirm() )) {
			event.preventDefault();
		}
	});
}

/*
 * Verifica che il nome utente (username) contenga solo lettere A~z o numeri
 */
function validateUserUsername() {
	var id = 'username';
	var username = document.forms['gestione']['username'].value;
	const allowedChars = /^[A-Za-z0-9]+$/; // lettere maiuscole e minuscole, numeri
	if (!allowedChars.test(username)) {
		showErrorMessage(id, 'Nome utente non valido, usa solo lettere o numeri');
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
	var name = document.forms['gestione']['nome'].value;
	const allowedChars = /^[A-Za-z\s'][^\d]*$/; // lettere, spazi, apostrofi

	if (name == null || name == '') {
		removeErrorMessage(id);
		return true;
	}
	if (!allowedChars.test(name)) {
		showErrorMessage(id, 'Nome non valido');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che la password rispetti vincolo di lunghezza e simboli.
 */
function validatePassword() {
	var id = 'new_password';
	var password  = document.forms['gestione']['new_password'].value;

	// Requisito di lunghezza minima
	if (password.length < 8) {
		showErrorMessage(id, 'La password deve essere lunga almeno 8 caratteri.');
		return false;
	}

	// Requisito di simboli
	if (!/\d/.test(password) || !/[a-zA-Z]/.test(password)) {
		showErrorMessage(id, 'La password deve contenere almeno una lettera e un numero.');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che la conferma della password corrisponda all'originale.
 */
function validatePasswordConfirm() {
	var id = 'new_password_confirm';
	var first_password  = document.forms['gestione']['new_password'].value;
	var second_password = document.forms['gestione']['new_password_confirm'].value;

	if ((second_password != null || second_password != '') && first_password != second_password) {
		showErrorMessage(id, 'Le password non corrispondono.');
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
	var birthday = document.forms['gestione']['data'].value;
	var today = new Date();

	// Controlla che ci sia una stringa
	if (birthday == null || birthday == '') {
		removeErrorMessage(id);
		return true;
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
	const underAgeErrorMessage = 'Devi avere almeno 13 anni. Aspetta di crescere, oppure fingi come tutti i minorenni che usano TikTok.';

	// controlla che l'utente abbia almeno 13 anni
	if (age < 13) {
		showErrorMessage(id, underAgeErrorMessage);
		return false;
	}
	// se la differenza è 13 potrebbe comunque non averli ancora compiuti
	else if (age == 13) {
		if (today.getMonth() < dateOfBirth.getMonth()) {
			showErrorMessage(id, underAgeErrorMessage);
			return false;
		}
		else if (today.getMonth() == dateOfBirth.getMonth()) {
			if (today.getDate() < dateOfBirth.getDate()) {
				showErrorMessage(id, underAgeErrorMessage);
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
	var email = document.forms['gestione']['email'].value;
	if (email == null || email == '') {
		removeErrorMessage(id);
		return true;
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
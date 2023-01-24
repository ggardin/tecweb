/*
 * Richiama validatori del form di modifica dati utente
 */
function validateUserSignup() {

	let form = document.getElementById("auth_form");

	form.addEventListener("submit", function (event) {
		if (! (validateNewUsername() && validatePassword() && validatePasswordConfirm()) ) {
			event.preventDefault();
		}
	});
}

/*
 * Verifica che il nome utente (username) contenga solo lettere A~z o numeri
 */
function validateNewUsername() {
	var id = 'username';
	var username = document.forms['auth_form']['username'].value;

	const allowedChars = /^[A-Za-z0-9]+$/; // lettere maiuscole e minuscole, numeri
	if (!allowedChars.test(username)) {
		showErrorMessage(id, '<span lang="en">Username</span> non valido, usa solo lettere o numeri.');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che la password rispetti vincolo di lunghezza e simboli.
 */
function validatePassword() {
	var id = 'password';
	var password  = document.forms['auth_form']['password'].value;

	// Requisito di lunghezza minima
	if (password.length < 8) {
		showErrorMessage(id, 'La <span lang="en">password</span> deve essere lunga almeno 8 caratteri.');
		return false;
	}

	// Requisito di simboli
	if (!/\d/.test(password) || !/[a-zA-Z]/.test(password)) {
		showErrorMessage(id, 'La <span lang="en">password</span> deve contenere almeno una lettera e un numero.');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Verifica che la conferma della password corrisponda all'originale.
 */
function validatePasswordConfirm() {
	var id = 'password_confirm';
	var first_password  = document.forms['auth_form']['password'].value;
	var second_password = document.forms['auth_form']['password_confirm'].value;

	if (first_password != second_password) {
		showErrorMessage(id, 'Le <span lang="en">password</span> non coincidono.');
		return false;
	}

	removeErrorMessage(id);
	return true;

}

window.addEventListener('load', function () {
	validateUserSignup();
});
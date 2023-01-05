/*
 * Richiama validatori del form di modifica dati utente
 */
function validaUserSignup() {

	let form = document.getElementById("auth_form");

	form.addEventListener("submit", function (event) {
		if ( validateNewUsername() ) {
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
		showErrorMessage(id, 'Nome utente non valido, usa solo lettere o numeri.');
		return false;
	}
	removeErrorMessage(id);
	return true;
}

window.addEventListener('load', function () {
	validaUserSignup();
});
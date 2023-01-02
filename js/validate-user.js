/*
 * Richiama validatori del form di modifica dati utente
 */
function validateUserData() {

	let form = document.getElementById("update-user-data");

	form.addEventListener("submit", function (event) {
		if ( true ) {
			event.preventDefault();
		}
	});
}

function validateUserBirthday() {}

function validateUserEmail() {}

function validateUserDisplayName() {}

window.addEventListener('load', function () {
	validateUserData();
});
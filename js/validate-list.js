/*
 * Richiama validatori del form di aggiornamento lista o collezione
 */
function validateList() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( !(validateListTitle()) ) {
			event.preventDefault();
		}
	});
}

/*
 * Valida il nome della lista o collezione
 */
function validateListTitle() {
	var id = 'titolo';
	var title = document.forms['gestione'][id].value;

	if (title == null || title == '') {
		showErrorMessage(id, 'Titolo è un campo richiesto.');
		return false;
	}
	else {
		const titleRegex = /^[\w\s\-\:\'\[\]\,\/\"\u00C0-\u017F]+$/;
		if (! titleRegex.test(title)) {
			showErrorMessage(id, 'Il titolo inserito contiene caratteri non ammessi.');
			return false;
		}
	}

	removeErrorMessage(id);
	return true;
}

window.addEventListener('load', function () {
	validateList();
});
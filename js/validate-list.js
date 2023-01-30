/*
 * Richiama validatori del form di aggiornamento lista o collezione
 */
function validateList() {

	let form = document.getElementById("gestione");

	form.addEventListener("submit", function (event) {
		if ( !(validateListTitle() && validateListDescription() && validateFileUpload()) ) {
			focusOnTopmostError();
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
		const titleRegex = /^[\w\s\-\.\:\'\[\]\,\/\"\u00C0-\u017F]+$/;
		if (! titleRegex.test(title)) {
			showErrorMessage(id, 'Il titolo inserito contiene caratteri non ammessi.');
			return false;
		}
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Valida la descrizione della lista o collezione
 */
function validateListDescription() {
	var id = 'descrizione';
	var description = document.forms['gestione'][id].value;

	if (description != null || description != '') {
		const descriptionRegex = /^[^<>]*$/;
		if (! descriptionRegex.test(description)) {
			showErrorMessage(id, 'La descrizione inserita contiene caratteri non ammessi.');
			return false;
		}
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Valida l'immagine caricata.
 */
function validateFileUpload() {
	var id = 'locandina';
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

const listeners = {
	"titolo" : ["input", validateListTitle ],
	"descrizione" : ["input", validateListDescription ],
	"locandina" : ["change", validateFileUpload ],
};

window.addEventListener('load', function () {
	registerRequiredListeners();
	validateList();
});
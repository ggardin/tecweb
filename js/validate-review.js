/*
 * Richiama validatori del form di inserimento nuova recensione
 */
function validateNewReview() {

	let form = document.getElementById("add-review");

	if (form != null) {
		form.addEventListener("submit", function (event) {
			if ( !( validateNewReviewRatingRadiobox() && validateNewReviewText() )) {
				focusOnTopmostError();
				event.preventDefault();
			}
		});
	}

}

/*
 * Controlla che sia stato espresso un voto
 */
function validateNewReviewRatingRadiobox() {
	var id = 'ratings';
	var ratings = document.getElementsByName('voto');
	var ratingFound = false;

	if (ratings != null) {
		for (const rating of ratings) {
			if (rating.checked) {
				ratingFound = true;
				break;
			}
		}
	}

	if ( !ratingFound ) {
		showErrorMessage(id, 'Non hai espresso un voto.');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Controlla che il testo della recensione sia lungo almeno 3 caratteri.
 */
function validateNewReviewText() {
	var id = 'review-text';
	var element = document.forms['add-review']['review-text'];
	const maxLength = element.getAttribute("maxlength");
	const currentLength = element.value.length;
	const reviewRegex = /^[^<>{}]*$/;
	const reviewEmpty = /^[\s]+$/;

	if (! reviewRegex.test(element.value)) {
		showErrorMessage(id, 'La recensione inserita contiene caratteri non ammessi.');
		return false;
	}
	else if ( currentLength < 5 ) {
		showErrorMessage(id, 'La recensione è troppo breve.');
		return false;
	}
	else if ( currentLength > maxLength ) {
		showErrorMessage(id, 'La recensione è troppo lunga.');
		return false;
	}
	else if (reviewEmpty.test(element.value)) {
		showErrorMessage(id, 'La recensione non può contenere solo spazi.');
		return false;
	}

	removeErrorMessage(id);
	return true;
}

/*
 * Controlla che la lunghezza della recensione non superi i 1000 caratteri.
 */
function checkNewReviewCharactersCounter() {
	var id = 'review-text';
	const element = document.forms['add-review']['review-text'];
	const charactersCounter = document.getElementById('review-chars');
	const maxLength = element.getAttribute("maxlength");
	const currentLength = element.value.length;

	if (currentLength >= maxLength) {
		charactersCounter.innerHTML = "Hai raggiunto il limite massimo di caratteri (" + maxLength + ").";
	}

	charactersCounter.innerHTML = "Hai a disposizione " + (maxLength - currentLength) + " caratteri.";
}

const listeners = {
	"review-text" : ["input", checkNewReviewCharactersCounter ],
	"review-text" : ["input", validateNewReviewText ],
};

window.addEventListener('load', function () {
	registerRequiredListeners();
	validateNewReview();
});
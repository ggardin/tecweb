/*
 * Richiama validatori del form di inserimento nuova recensione
 */
function validateNewReview() {

	let form = document.getElementById("add-review");

	form.addEventListener("submit", function (event) {
		if ( !( validateNewReviewRatingRadiobox() && validateNewReviewText() )) {
			event.preventDefault();
		}
	});
}

/*
 * Controlla che sia stato espresso un voto
 */
function validateNewReviewRatingRadiobox() {
	var id = 'rating0';
	if ( document.forms['add-review']['rating0'].checked ) {
		showErrorMessage(id, 'Non hai espresso un voto');
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

	if ( currentLength < 2 ) {
		showErrorMessage(id, 'La recensione è troppo breve');
		return false;
	}
	else if ( currentLength > 1000 ) {
		showErrorMessage(id, 'La recensione è troppo lunga');
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

window.addEventListener('load', function () {
	validateNewReview();
});
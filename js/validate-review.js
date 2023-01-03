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
	if ( document.forms['add-review']['rating0'].checked ) {
		alert('Non hai espresso un voto');
		return false;
	}
	return true;
}

/*
 * Controlla che il testo della recensione sia lungo almeno 3 caratteri.
 */
function validateNewReviewText() {
	if ( document.forms['add-review']['review-text'].value.length < 2 ) {
		alert('La recensione è troppo breve');
		return false;
	}
	return true;
}

/*
 * Controlla che la lunghezza della recensione non superi i 1000 caratteri.
 */
function checkNewReviewCharactersCounter() {
	const textarea = document.forms['add-review']['review-text'];
	const charactersCounter = document.getElementById('review-chars');
	const maxLength = textarea.getAttribute("maxlength");
	const currentLength = textarea.value.length;

	if (currentLength >= maxLength) {
		charactersCounter.innerHTML = "Hai raggiunto il limite massimo di caratteri (" + maxLength + ").";
		return false;
	}

	charactersCounter.innerHTML = "Hai a disposizione " + (maxLength - currentLength) + " caratteri.";

	return true;
}

window.addEventListener('load', function () {
	validateNewReview();
});
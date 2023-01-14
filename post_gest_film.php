<?php

require_once("php/tools.php");
require_once("php/database.php");

// TODO: controlli admin

$user = isset($_SESSION["id"]) ? $_SESSION["id"] : ""; echo "user:" . ($user) . "\n";
$id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : ""; echo "id:" . ($id) . "\n";
$titolo = isset($_POST["titolo"]) ? $_POST["titolo"] : ""; echo "titolo:" . ($titolo) . "\n";
$descrizione = isset($_POST["descrizione"]) ? $_POST["descrizione"] : ""; echo "descrizione:" . ($descrizione) . "\n";
$data = isset($_POST["data"]) ? $_POST["data"] : ""; echo "data:" . ($data) . "\n";
$durata = isset($_POST["durata"]) ? $_POST["durata"] : ""; echo "durata:" . ($durata) . "\n";
$crew_count = isset($_POST["crew-count"]) ? $_POST["crew-count"] : ""; echo "crew_count:" . ($crew_count) . "\n";
$genere = isset($_POST["genere"]) ? $_POST["genere"] : ""; echo ("genere:"); print_r ($genere ) ;echo ("\n");
$nations_count = isset($_POST["nations-count"]) ? $_POST["nations-count"] : ""; echo "nations_count:" . ($nations_count) . "\n";
$titolo_originale = isset($_POST["titolo_originale"]) ? $_POST["titolo_originale"] : ""; echo "titolo_originale:" . ($titolo_originale) . "\n";
$stato = isset($_POST["stato"]) ? $_POST["stato"] : ""; echo "stato:" . ($stato) . "\n";
$budget = isset($_POST["budget"]) ? $_POST["budget"] : ""; echo "budget:" . ($budget) . "\n";
$incassi = isset($_POST["incassi"]) ? $_POST["incassi"] : ""; echo "incassi:" . ($incassi) . "\n";
$collezione = isset($_POST["collezione"]) ? $_POST["collezione"] : ""; echo "collezione:" . ($collezione) . "\n";

// print_r($genere);

// for i < crew-count
// 	crew-name$i
// 	crew-role$i

// for i < nations-count
// 	nation-name$i


for ($i = 0; $i < $crew_count; $i++) {
	echo ("crew persona $i: " . $_POST["crew-name$i"] . "\n");
	echo ("crew ruolo $i: " . $_POST["crew-role$i"] . "\n");
}

for ($i = 0; $i < $nations_count; $i++) {
	echo ("nazione $i: " . $_POST["nation-name$i"] . "\n");
}


$submit = isset($_POST["submit"]) ? $_POST["submit"] : ""; echo( "submit:" . $submit);

// echo ($submit);

// if (isset($_FILES["locandina"])) {
// 	$img = Tools::uploadImg($_FILES['locandina']);
// 	if ($img[0])
// 		$locandina = $img[1];
// 	else
// 		$locandina = "";
// } else {
// 	$locandina = "";
// }

// if ($user == "" || ! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
// 	header("location: index.php");
// 	exit();
// }

// if ($titolo == "") {
// 	header("location: gest_collezione.php?id=$id");
// 	exit();
// }

// try {
// 	$connessione = new Database();
// 	if ($submit == "aggiungi" || $submit = "modifica")
// 		$res = $connessione->updateCollezione($id, $titolo, $descrizione, $locandina);
// 	elseif ($submit == "elimina")
// 		$res = $connessione->deleteCollezione($id);
// 	else
// 		$res = false;
// 	unset($connessione);
// } catch (Exception) {
// 	unset($connessione);
// 	Tools::errCode(500);
// 	exit();
// }

// if ($res) {
// 	if ($submit == "modifica")
// 		header("location: gest_collezione.php?id=" . $id);
// 	elseif ($submit == "aggiungi")
// 		header("location: gest_collezione.php?id=" . $res[1]);
// 	elseif ($submit == "elimina")
// 		header("location: cerca_collezione.php");
// } else {
// 	header("location: gest_collezione.php?id=$id");
// 	$_SESSION["error"] = "qualcosa è andato storto";
// }

?>
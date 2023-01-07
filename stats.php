<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$visti = $connessione->totalFilms($_SESSION["id"]);
	$films = $connessione->FilmByTime($_SESSION["id"]);
	$genere = $connessione->Genre($_SESSION["id"]);

	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if(empty($films)){
	Tools::replaceAnchor($page, "message", "Aggiungi dei film alle tue liste per vedere le statistiche");
	Tools::replaceSection($page, "stats", "");
} else {
	Tools::replaceAnchor($page, "message", "");
	Tools::replaceAnchor($page, "numeroFilm", count($visti));
	$minuti = 0;
	foreach($films as $f){
		$minuti = $minuti + $f["durata"];
	}
	Tools::replaceAnchor($page, "minutiVisti", $minuti);

	$i = 0;
	foreach($films as $f){
		if($i == 0){
			$longest = $f["nome"];
			$longestTime = $f["durata"];
		} else if($i == 1){
			$secondLongest = $f["nome"];
			$secondLongestTime = $f["durata"];
		} else if($i == 2){
			$thirdLongest = $f["nome"];
			$thirdLongestTime = $f["durata"];
		}
		$i = $i + 1;
		if($i == 3) break;
	}

	Tools::replaceAnchor($page, "filmLungo", $longest);
	Tools::replaceAnchor($page, "primoFilmLungo", $longest);
	Tools::replaceAnchor($page, "durataFilmLungo", $longestTime);
	if($secondLongest){
		Tools::replaceAnchor($page, "secondoFilmLungo", $secondLongest);
		Tools::replaceAnchor($page, "durataSecondoFilmLungo", $secondLongestTime);
	} else {
		Tools::replaceAnchor($page, "secondoFilmLungo", "Aggiungi altri film per vedere ulteriori statistiche");
		Tools::replaceAnchor($page, "durataSecondoFilmLungo", "");
	}
	if($thirdLongest){
		Tools::replaceAnchor($page, "terzoFilmLungo", $thirdLongest);
		Tools::replaceAnchor($page, "durataTerzoFilmLungo", $thirdLongestTime);
	} else {
		Tools::replaceAnchor($page, "terzoFilmLungo", "Aggiungi altri film per vedere ulteriori statistiche");
		Tools::replaceAnchor($page, "durataTerzoFilmLungo", "");
	}

	$i = 0;
	foreach($genere as $g){
		if($i == 0){
			$firstGenre = $g["nome"];
			$firstGenreCount = $g["count(*)"];
		} else if($i == 1){
			$secondGenre = $g["nome"];
			$secondGenreCount = $g["count(*)"];
		} else if($i == 2){
			$thirdGenre = $g["nome"];
			$thirdGenreCount = $g["count(*)"];
		}
		$i = $i + 1;
	}
	Tools::replaceAnchor($page, "primoGenere", $firstGenre);
	Tools::replaceAnchor($page, "filmVistiPrimoGenere", $firstGenreCount);
	if($secondGenre){
		Tools::replaceAnchor($page, "secondoGenere", $secondGenre);
		Tools::replaceAnchor($page, "filmVistiSecondoGenere", $secondGenreCount);
	} else {
		Tools::replaceAnchor($page, "secondoGenere", "Aggiungi altri film per vedere ulteriori statistiche");
		Tools::replaceAnchor($page, "filmVistiSecondoGenere", "");
	}
	if($thirdGenre){
		Tools::replaceAnchor($page, "terzoGenere", $thirdGenre);
		Tools::replaceAnchor($page, "filmVistiTerzoGenere", $thirdGenreCount);
	} else {
		Tools::replaceAnchor($page, "terzoGenere", "Aggiungi altri film per vedere ulteriori statistiche");
		Tools::replaceAnchor($page, "filmVistiTerzoGenere", "");
	}
}

Tools::showPage($page);

?>




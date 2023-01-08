<?php

require_once("ini.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class Database {
	private const HOST = "mariadb";
	private const NAME = "rbonavig";
	private const USER = "rbonavig";
	private const PASS = "paJa5The1eiM4hei";

	private const ERR = "Errore in database.php";

	private $connection;

	// see: https://phpdelusions.net/mysqli/mysqli_connect
	public function __construct() {
		try {
			$this->connection = new mysqli(self::HOST, self::USER, self::PASS, self::NAME);
			$this->connection->set_charset("utf8mb4");
			$this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
		} catch (mysqli_sql_exception $e) {
			// throw new mysqli_sql_exception($e->getMessage(), $e->getCode()); // to avoid leaking connection parameters in the stack trace
			throw new Exception(self::ERR);
		}
	}

	public function __destruct() {
		if ($this->connection)
			$this->connection->close();
	}

	// adattata da quella vista a lezione
	private function pulisciInput(&$params) : void {
		foreach ($params as &$p) {
			if (is_string($p)) {
				$p = trim($p);
				$p = strip_tags($p);
				// convertiamo in entità durante output, qui facciamo il contrario
				$p = html_entity_decode($p, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
			}
		}
	}

	// see: https://phpdelusions.net/mysqli
	private function preparedQuery(&$query, &$params, $types = "") : mysqli_stmt {
		$this->pulisciInput($params);
		$types = $types ?: str_repeat("s", count($params));
		$stmt = $this->connection->prepare($query);
		if (!empty($params)) $stmt->bind_param($types, ...$params);
		$stmt->execute();
		return $stmt;
	}

	private function preparedSelect(&$query, &$params, $types = "") : array {
		try {
			$stmt = $this->preparedQuery($query, $params, $types);
			$result = $stmt->get_result();
			$ret = $result->fetch_all(MYSQLI_ASSOC);
			$result->close();
			$stmt->close();
			return $ret;
		} catch (mysqli_sql_exception $e) {
			throw new Exception(self::ERR);
		}
	}

	private function preparedUpdates(&$query, &$params, $types = "") : bool {
		try {
			$stmt = $this->preparedQuery($query, $params, $types);
			$a_r = $stmt->affected_rows;
			$stmt->close();
		} catch (mysqli_sql_exception $e) {
			if ($e->getCode() == 1062)
				return false;
			else
				throw new Exception(self::ERR);
		}
		return $a_r > 0;
	}

	public function getCollezioneById($id) : array {
		$query = "select *
			from collezione
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getFilmByCollezioneId($id) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio, f.descrizione
			from collezione as c
				join film as f
					on c.id = f.collezione
			where c.id = ?
			order by f.data_rilascio is null, f.data_rilascio";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getGenderById($id) : array {
		$query = "select *
			from gender
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getPersonaById($id) : array {
		$query = "select *
			from persona
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getStati() : array {
		$query = "select id, nome
			from stato
			order by id";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getCollezioni() : array {
		$query = "select id, nome
			from collezione
			order by id";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getGenders() : array {
		$query = "select id, nome
			from gender
			order by id";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getFilmByPersonaId($id) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio, r.nome as ruolo
			from persona as p
				join crew as c
					on p.id = c.persona
				join film as f
					on c.film = f.id
				join ruolo as r
					on c.ruolo = r.id
			where p.id = ?
			order by f.data_rilascio desc, f.id, r.id";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getStatoById($id) : array {
		$query = "select id, nome
			from stato
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getFilmById($id) : array {
		$query = "select *
			from film
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getCrewByFilmId($id) : array {
		$query = "select r.nome as ruolo, p.id as p_id, p.nome as p_nome
			from film as f
				join crew as c
					on f.id = c.film
				join persona as p
					on c.persona = p.id
				join ruolo as r
					on c.ruolo = r.id
			where f.id = ?
			order by r.id";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getPaeseByFilmId($id) : array {
		$query = "select p.nome
			from film as f
				join film_paese as fp
					on f.id = fp.film
				join paese as p
					on fp.paese = p.iso_3166_1
			where f.id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getGenereByFilmId($id) : array {
		$query = "select g.nome
			from film as f
				join film_genere as fg
					on f.id = fg.film
				join genere as g
					on fg.genere = g.id
			where f.id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getValutazioneByFilmId($id) : array {
		$query = "select u.username as utente, v.voto, v.testo
			from film as f
				join valutazione as v
					on f.id = v.film
				join utente as u
					on v.utente = u.id
			where f.id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getPaesi() : array {
		$query = "select iso_3166_1 as id, nome
			from paese";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getGeneri() : array {
		$query = "select id, nome
			from genere";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function searchFilm($str, $limit, $offset) : array {
		$base = "from film
			where nome like ?
			order by data_rilascio desc";

		$search = [];

		$q0 = "select id, nome, locandina, data_rilascio " . $base . " limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), $limit, $offset];
		$t0 = "sii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n " . $base;
		$p1 = [("%" . trim($str) . "%")];
		$t1 = "s";
		$search[1] = $this->preparedSelect($q1, $p1, $t1)[0];

		return $search;
	}

	public function searchFilmFilteredByGenere($str, $limit, $offset, $genere) : array {
		$base = "from film as f
				join film_genere as fg
					on f.id = fg.film
				join genere as g
					on fg.genere = g.id
			where f.nome like ?
				and g.nome = ?
			order by data_rilascio desc";

		$search = [];

		$q0 = "select f.id, f.nome, f.locandina, f.data_rilascio " . $base . " limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), $genere, $limit, $offset];
		$t0 = "ssii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n " . $base;
		$p1 = [("%" . trim($str) . "%"), $genere];
		$t1 = "ss";
		$search[1] = $this->preparedSelect($q1, $p1, $t1)[0];

		return $search;
	}

	public function searchFilmFilteredByPaese($str, $limit, $offset, $paese) : array {
		$base = "from film as f
				join film_paese as fp
					on f.id = fp.film
				join paese as p
					on fp.paese = p.iso_3166_1
			where f.nome like ?
				and p.nome = ?
			order by data_rilascio desc";

		$search = [];

		$q0 = "select f.id, f.nome, f.locandina, f.data_rilascio " . $base . " limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), $paese, $limit, $offset];
		$t0 = "ssii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n " . $base;
		$p1 = [("%" . trim($str) . "%"), $paese];
		$t1 = "ss";
		$search[1] = $this->preparedSelect($q1, $p1, $t1)[0];

		return $search;
	}

	public function searchCollezione($str, $limit, $offset) : array {
		$base = "from collezione
			where nome like ?";

		$search = [];

		$q0 = "select id, nome, locandina " . $base . " limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), $limit, $offset];
		$t0 = "sii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n " . $base;
		$p1 = [("%" . trim($str) . "%")];
		$t1 = "s";
		$search[1] = $this->preparedSelect($q1, $p1, $t1)[0];

		return $search;
	}

	public function searchPersona($str, $limit, $offset) : array {
		$base = "from persona
			where nome like ?";

		$search = [];

		$q0 = "select id, nome, immagine " . $base . " limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), $limit, $offset];
		$t0 = "sii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n " . $base;
		$p1 = [("%" . trim($str) . "%")];
		$t1 = "s";
		$search[1] = $this->preparedSelect($q1, $p1, $t1)[0];

		return $search;
	}

	public function getUsernameByUtenteId($id) : array {
		$query = "select username
			from utente
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getListeByUtenteId($id) : array {
		$query = "select id, nome
			from lista
			where utente = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getListeSenzaFilm($user_id, $film_id) : array {
		$query = "select id, nome
			from lista
			where utente = ? and id not in
				(select l.id
				from lista as l
				join lista_film as lf
					on l.id = lf.lista
				where lf.film = ?)";

		$params = [$user_id, $film_id];
		$types = "ii";

		return $this->preparedSelect($query, $params, $types);
	}

	public function isListaDiUtente($list_id, $user_id) : bool {
		$query = "select nome
			from lista
			where id = ? and utente = ?";

		$params = [$list_id, $user_id];
		$types = "ii";

		return (!empty($this->preparedSelect($query, $params, $types)));
	}

	public function getNomeListaById($id) : array {
		$query = "select nome
			from lista
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getFilmInLista($id) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio
			from lista as l
				join lista_film as lf
					on l.id = lf.lista
				join film as f
					on lf.film = f.id
			where l.id = ?
			order by lf.id";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function insertUtente($username, $pass) : bool {
		$query = "insert into utente(username, password, gender)
			values (?, ?, 0)";

		$pass = password_hash($pass, PASSWORD_DEFAULT);
		$params = [$username, $pass];

		return $this->preparedUpdates($query, $params);
	}

	public function insertLista($user_id, $list_name) : bool {
		$query = "insert into lista(utente, nome)
			values (?, ?)";

		$params = [$user_id, $list_name];
		$types = "is";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function insertFilmInLista($list_id, $film_id) : bool {
		$query = "insert into lista_film(lista, film)
			values (?, ?)";

		$params = [$list_id, $film_id];
		$types = "ii";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function canUtenteValutare($user_id, $film_id) : bool {
		$query = "select *
			from valutazione
			where utente = ? and film = ?";

		$params = [$user_id, $film_id];
		$types = "ii";

		return empty($this->preparedSelect($query, $params, $types));
	}

	public function updateFilm($film_id, $nome, $nome_originale, $durata, $locandina, $descrizione, $stato, $data_rilascio, $budget, $incassi, $collezione) : bool {
		$query = "update film
			set nome = ?, nome_originale = ?, durata = ?, locandina = ?, descrizione = ?,
				stato = ?, data_rilascio = ?, budget = ?, incassi = ?, collezione = ?
			where id = ?";

		$params = [$nome, $nome_originale, $durata, $locandina, $descrizione, $stato, $data_rilascio, $budget, $incassi, $collezione, $film_id];
		$types = "ssissisiiii";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function insertPersona($nome, $gender, $immagine, $data_nascita, $data_morte) : bool {
		$query = "insert into persona(nome, gender, immagine, data_nascita, data_morte)
			values (?, ?, ?, ?, ?)";

		$params = [$nome, $gender, $immagine, $data_nascita, $data_morte];
		$types = "sisss";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updatePersona($id, $nome, $gender, $immagine, $data_nascita, $data_morte) : bool {
		$query = "update persona
			set nome = ?, gender = ?, immagine = ?, data_nascita = ?, data_morte = ?
			where id = ?";

		$params = [$nome, $gender, $immagine, $data_nascita, $data_morte, $id];
		$types = "sisssi";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updateCollezione($id, $nome, $descrizione, $locandina) : bool {
		$query = "update collezione
			set nome = ?, descrizione = ?, locandina = ?
			where id = ?";

		$params = [$nome, $descrizione, $locandina, $id];
		$types = "sssi";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updateUtente($id, $username, $mail, $nome, $gender, $data_nascita, $password) : bool {
		$query = "update utente
			set username = ?, mail = ?, nome = ?, gender = ?, data_nascita = ?, password = ?
			where id = ?";

		$params = [$username, $mail, $nome, $gender, $data_nascita, $password, $id];
		$types = "sssissi";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function deletePersona($id) : bool {
		$query = "delete from persona
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function deleteLista($id) : bool {
		$query = "delete from lista
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updateLista($list_id, $name) : bool {
		$query = "update lista
			set nome = ?
			where id = ?";

		$params = [$name, $list_id];
		$types = "si";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function deleteFromList($list_id, $film_id) : bool {
		$query = "delete from lista_film
			where lista = ? and film = ?";

		$params = [$list_id, $film_id];
		$types = "ii";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updateVoto($film_id) : bool {
		$query = "update film
			set voto = (select avg(voto)
				from valutazione
				group by film
				having film = ?)
			where id = ?";

		$params = [$film_id, $film_id];
		$types = "ii";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function insertValutazione($user_id, $film_id, $voto, $testo) : bool {
		$query = "insert into valutazione(utente, film, voto, testo)
			values (?, ?, ?, ?)";

		$params = [$user_id, $film_id, $voto, $testo];
		$types = "iiis";

		return $this->preparedUpdates($query, $params, $types) &&
			$this->updateVoto($film_id);
	}

	public function login($username, $password) : array {
		$query = "select id, is_admin, password
			from utente
			where username = ?";

		$params = [$username];

		$res = $this->preparedSelect($query, $params);

		$status = [];
		if (!empty($res) && password_verify($password, $res[0]["password"])) {
			$status["id"] = $res[0]["id"];
			$status["is_admin"] = $res[0]["is_admin"];
		}

		return $status;
	}

	public function signup($username, $pass) : array {
		if (insertUtente($username, $pass))
			return [true, $this->connection->insert_id];
		else
			return [false, 0];
	}

	public function totalFilms($user_id) : array {
		$query = "select distinct(lf.film)
			from lista as l
			join lista_film as lf
					on l.id = lf.lista
			where l.utente = ?";

		$params = [$user_id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function FilmByTime($user_id) : array {
		$query = "select distinct(f.id), f.durata, f.nome
			from lista as l
				join lista_film as lf
					on l.id = lf.lista
				join film as f
					on lf.film = f.id
			where l.utente = ?
			order by f.durata desc";

		$params = [$user_id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);

	}

	public function Genre($user_id) : array {
		$query = "select g.nome, count(*)
				from lista as l
					join lista_film as lf
						on l.id = lf.lista
					join film as f
						on lf.film = f.id
					join film_genere as fg
						on f.id = fg.film
					join genere as g
						on fg.genere = g.id
				where l.utente = ?
				group by g.nome
				order by count(*) desc";


				$params = [$user_id];
				$types = "i";

				return $this->preparedSelect($query, $params, $types);
	}
}

?>

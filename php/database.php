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

	private function pulisciInputHelper (&$item) {
		if (is_string($item)) {
			// convertiamo in entità durante output, qui facciamo il contrario
			$item = html_entity_decode($item, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
			$item = trim($item);
			$item = strip_tags($item);
		}
	}

	// adattata da quella vista a lezione
	private function pulisciInput(&$in) : void {
		if (is_array($in))
			array_walk_recursive($in, "self::pulisciInputHelper");
		elseif (is_string($in))
			$this->pulisciInputHelper($in);
	}

	// see: https://phpdelusions.net/mysqli
	private function preparedQuery(&$query, &$params, $types = "", $stmt = null) : mysqli_stmt {
		$this->pulisciInput($params);
		$types = $types ?: str_repeat("s", count($params));
		if (is_null($stmt)) $stmt = $this->connection->prepare($query);
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
			$ar = $stmt->affected_rows;
			$stmt->close();
		} catch (mysqli_sql_exception $e) {
			if ($e->getCode() == 1062)
				return false;
			else
				throw new Exception(self::ERR);
		}
		return $ar > 0;
	}

	private function preparedInsertMultiple(&$query, &$params, $types = "") : bool {
		try {
			$stmt = null;
			$ar = 0;
			for ($i = 0; $i < count($params); $i++) {
				$stmt = $this->preparedQuery($query, $params[$i], $types, $stmt);
				$ar += $stmt->affected_rows;
			}
			if (! is_null($stmt)) $stmt->close();
		} catch (mysqli_sql_exception $e) {
			if ($e->getCode() == 1062)
				return false;
			else
				throw new Exception(self::ERR);
		}
		return $ar > 0;
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

	public function getPersonaById($id) : array {
		$query = "select p.id, p.nome, p.gender, p.immagine, p.data_nascita, p.data_morte, g.nome as gender_nome
			from persona as p
				join gender as g
					on p.gender = g.id
			where p.id = ?";

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

	public function getPaesi() : array {
		$query = "select iso_3166_1 as id, nome
			from paese
			order by nome";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getRuoli() : array {
		$query = "select id, nome
			from ruolo
			order by id";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getPersone() : array {
		$query = "select id, nome
			from persona
			order by id";

		$params = [];

		return $this->preparedSelect($query, $params);
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
		$query = "select r.id as r_id, r.nome as r_nome, p.id as p_id, p.nome as p_nome
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
		$query = "select p.iso_3166_1 as id, p.nome
			from film as f
				join film_paese as fp
					on f.id = fp.film
				join paese as p
					on fp.paese = p.iso_3166_1
			where f.id = ?
			order by p.nome";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getGenereByFilmId($id) : array {
		$query = "select g.id, g.nome
			from film as f
				join film_genere as fg
					on f.id = fg.film
				join genere as g
					on fg.genere = g.id
			where f.id = ?
			order by g.nome";

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

	public function getPaesiConFilm() : array {
		$query = "select distinct p.iso_3166_1 as id, p.nome
			from paese as p
				join film_paese as fp
					on p.iso_3166_1 = fp.paese
			order by p.nome";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getGeneri() : array {
		$query = "select id, nome
			from genere
			order by nome";

		$params = [];

		return $this->preparedSelect($query, $params);
	}

	public function getGeneriConFilm() : array {
		$query = "select distinct g.id, g.nome
			from genere as g
				join film_genere as fg
					on g.id = fg.genere
			order by g.nome";

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
		$search = [];

		$q0 = "select t.id, t.nome, t.locandina, t.n_film from (
				select c.id, c.nome, c.locandina, count(*) as n_film
				from collezione as c
					join film as f
						on c.id = f.collezione
				where c.nome like ?
				group by c.id
				union
				select c.id, c.nome, c.locandina, 0 as n_film
				from collezione as c
				where c.nome like ?
			) as t
			group by t.id
			limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), ("%" . trim($str) . "%"), $limit, $offset];
		$t0 = "ssii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n
			from collezione
			where nome like ?";
		$p1 = [("%" . trim($str) . "%")];
		$t1 = "s";
		$search[1] = $this->preparedSelect($q1, $p1, $t1)[0];

		return $search;
	}

	public function searchPersona($str, $limit, $offset) : array {
		$search = [];

		$q0 = "select t.id, t.nome, t.immagine, t.n_film from (
				select p.id, p.nome, p.immagine, count(distinct f.id) as n_film
				from persona as p
					join crew as c
						on p.id = c.persona
					join film as f
						on c.film = f.id
				where p.nome like ?
				group by p.id
				union
				select p.id, p.nome, p.immagine, 0 as n_film
				from persona as p
				where p.nome like ?
			) as t
			group by t.id
			limit ? offset ?";
		$p0 = [("%" . trim($str) . "%"), ("%" . trim($str) . "%"), $limit, $offset];
		$t0 = "ssii";
		$search[0] = $this->preparedSelect($q0, $p0, $t0);

		$q1 = "select count(*) as n
			from persona
			where nome like ?";
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
			where utente = ?
			order by nome";

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
				where lf.film = ?)
			order by nome";

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

	private function updateArgs(&$query, &$values, &$params, &$types, &$args) {
		$q = "";
		$v = "";
		foreach ($args as $arg) {
			if (sizeof($arg) == 3 || ! empty($arg[0]) || is_null($arg[0])) {
				$q .= ", " . $arg[1];
				if (! $values)
					$q .= " = ?";
				else
					$v .= ", ?";
				array_push($params, ($arg[0] != "" ? $arg[0] : null));
				$types .= $arg[2];
			}
		}
		$query .= substr($q, 1);
		if ($values) $values .= substr($v, 1);
	}

	public function updateFilm($id, $nome, $nome_originale, $durata, $locandina, $descrizione, $stato, $data_rilascio, $budget, $incassi, $collezione) : array {
		if ($id != "") {
			$query = "update film
				set";
			$values = "";
		} else {
			$query = "insert into film(";
			$values = "values (";
		}

		$params = [];
		$types = "";

		$args = [
			[$nome, "nome", "s"],
			[$nome_originale, "nome_originale", "s"],
			[$stato, "stato", "i"],
			[$durata, "durata", "i"],
			[$locandina, "locandina", "s", false],
			[$descrizione, "descrizione", "s"],
			[$data_rilascio, "data_rilascio", "s"],
			[$budget, "budget", "i"],
			[$incassi, "incassi", "i"],
			[$collezione, "collezione", "i"],
			];

		$this->updateArgs($query, $values, $params, $types, $args);

		if ($id != "") {
			$query .= " where id = ?";
			array_push($params, $id);
			$types .= "i";
		} else {
			$query .= ") " . $values . ")";
		}

		return [$this->preparedUpdates($query, $params), $this->connection->insert_id];
	}

	public function deleteFilm($id) : bool {
		$query = "delete from film
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updateCollezione($id, $nome, $descrizione, $locandina) : array {
		if ($id != "") {
			$query = "update collezione
				set";
			$values = "";
		} else {
			$query = "insert into collezione(";
			$values = "values (";
		}

		$params = [];
		$types = "";

		$args = [
			[$nome, "nome", "s"],
			[$descrizione, "descrizione", "s"],
			[$locandina, "locandina", "s", false]
			];

		$this->updateArgs($query, $values, $params, $types, $args);

		if ($id != "") {
			$query .= " where id = ?";
			array_push($params, $id);
			$types .= "i";
		} else {
			$query .= ") " . $values . ")";
		}

		return [$this->preparedUpdates($query, $params), $this->connection->insert_id];
	}

	public function deleteCollezione($id) : bool {
		$query = "delete from collezione
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updatePersona($id, $nome, $gender, $immagine, $data_nascita, $data_morte) : array {
		if ($id != "") {
			$query = "update persona
				set";
			$values = "";
		} else {
			$query = "insert into persona(";
			$values = "values (";
		}

		$params = [];
		$types = "";

		$args = [
			[$nome, "nome", "s"],
			[$gender, "gender", "i"],
			[$immagine, "immagine", "s", false],
			[$data_nascita, "data_nascita", "s"],
			[$data_morte, "data_morte", "s"]
		];

		$this->updateArgs($query, $values, $params, $types, $args);

		if ($id != "") {
			$query .= " where id = ?";
			array_push($params, $id);
			$types .= "i";
		} else
			$query .= ") " . $values . ")";

		return [$this->preparedUpdates($query, $params), $this->connection->insert_id];
	}

	public function deletePersona($id) : bool {
		$query = "delete from persona
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function updateUtente($id, $username, $mail, $nome, $gender, $data_nascita, $password) : array {
		if ($id != "") {
			$query = "update utente
				set";
			$values = "";
		} else {
			$query = "insert into utente(";
			$values = "values (";
		}

		$params = [];
		$types = "";

		if ($password != "")
			$password = password_hash($password, PASSWORD_DEFAULT);

		$args = [
			[$username, "username", "s"],
			[$mail, "mail", "s"],
			[$nome, "nome", "s"],
			[$gender, "gender", "i"],
			[$data_nascita, "data_nascita", "s"],
			[$password, "password", "s", false]
			];

		$this->updateArgs($query, $values, $params, $types, $args);

		if ($id != "") {
			$query .= " where id = ?";
			array_push($params, $id);
			$types .= "i";
		} else {
			$query .= ") " . $values . ")";
		}

		return [$this->preparedUpdates($query, $params), $this->connection->insert_id];
	}

	public function deleteUtente($id) : bool {
		$query = "delete from utente
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function insertLista($user_id, $list_name) : array {
		$query = "insert into lista(utente, nome)
			values (?, ?)";

		$params = [$user_id, $list_name];
		$types = "is";

		return [$this->preparedUpdates($query, $params, $types), $this->connection->insert_id];
	}

	public function updateLista($list_id, $name) : bool {
		$query = "update lista
			set nome = ?
			where id = ?";

		$params = [$name, $list_id];
		$types = "si";

		return $this->preparedUpdates($query, $params, $types);
	}

	public function deleteLista($id) : bool {
		$query = "delete from lista
			where id = ?";

		$params = [$id];
		$types = "i";

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
		$query = "insert into utente(username, password, gender)
			values (?, ?, 0)";

		$pass = password_hash($pass, PASSWORD_DEFAULT);
		$params = [$username, $pass];

		return [$this->preparedUpdates($query, $params), $this->connection->insert_id];
	}

	public function getNumeroFilmPerUtente($user_id) : array {
		$query = "select count(distinct lf.film) as n
			from lista as l
				join lista_film as lf
					on l.id = lf.lista
			where l.utente = ?";

		$params = [$user_id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getNumeroListePerUtente($user_id) : array {
		$query = "select count(*) as n
			from lista
			where utente = ?";

		$params = [$user_id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getFilmPiuLunghiPerUtente($user_id, $limit) : array {
		$query = "select distinct f.nome, f.durata
			from lista as l
				join lista_film as lf
					on l.id = lf.lista
				join film as f
					on lf.film = f.id
			where l.utente = ?
				and f.durata is not null
			order by f.durata desc
			limit ?";

		$params = [$user_id, $limit];
		$types = "ii";

		return $this->preparedSelect($query, $params, $types);

	}

	public function getNumeroPerGenerePerUtente($user_id) : array {
		$query = "select g.nome, count(*) as n
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
			order by count(*) desc, g.nome";


		$params = [$user_id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function setFilmCrew($film_id, $persone, $ruoli) : bool {
		$q0 = "delete from crew
			where film = ?";
		$p0 = [$film_id];
		$t0 = "i";
		$ar0 = $this->preparedUpdates($q0, $p0, $t0);

		$q1 = "insert into crew(film, persona, ruolo)
			values (?, ?, ?)";
		$p1 = [];
		for ($i = 0; $i < count($ruoli); $i++)
			$persone[$i] != "" && $ruoli[$i] != "" &&
				array_push($p1, [$film_id, $persone[$i], $ruoli[$i]]);
		$t1 = "iii";
		$ar1 = $this->preparedInsertMultiple($q1, $p1, $t1);

		return $ar0 != $ar1;
	}

	public function setFilmPaesi($film_id, $paesi) : bool {
		$q0 = "delete from film_paese
			where film = ?";
		$p0 = [$film_id];
		$t0 = "i";
		$ar0 = $this->preparedUpdates($q0, $p0, $t0);

		$q1 = "insert into film_paese(film, paese)
			values (?, ?)";
		$p1 = [];
		for ($i = 0; $i < count($paesi); $i++)
			$paesi[$i] != "" && array_push($p1, [$film_id, $paesi[$i]]);
		$t1 = "is";
		$ar1 = $this->preparedInsertMultiple($q1, $p1, $t1);

		return $ar0 != $ar1;
	}

	public function setFilmGeneri($film_id, $generi) : bool {
		$q0 = "delete from film_genere
			where film = ?";
		$p0 = [$film_id];
		$t0 = "i";
		$ar0 = $this->preparedUpdates($q0, $p0, $t0);

		$q1 = "insert into film_genere(film, genere)
			values (?, ?)";
		$p1 = [];
		for ($i = 0; $i < count($generi); $i++)
			$generi[$i] != "" && array_push($p1, [$film_id, $generi[$i]]);
		$t1 = "ii";
		$ar1 = $this->preparedInsertMultiple($q1, $p1, $t1);

		return $ar0 != $ar1;
	}

	public function getUtenteById($user_id) : array {
		$query = "select *
			from utente
			where id = ?";

		$params = [$user_id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

}

?>

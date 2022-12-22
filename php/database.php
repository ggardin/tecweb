<?php

require_once("ini.php");
require_once("tools.php");

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
				$p = htmlspecialchars_decode($p, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
				// $p = htmlspecialchars($p, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
			}
		}
	}

	private function pulisciOutput(&$item, $key) : void {
		if (is_string($item)) {
			$item = htmlspecialchars($item, ENT_QUOTES | ENT_SUBSTITUTE| ENT_HTML5);
			$item = Tools::toSpanLang($item);
			$item = Tools::toAbbr($item);
		}
	}

	// see: https://phpdelusions.net/mysqli
	private function preparedQuery(&$query, &$params, $types = "") : mysqli_stmt {
		$this->pulisciInput($params);
		$types = $types ?: str_repeat("s", count($params));
		$stmt = $this->connection->prepare($query);
		$stmt->bind_param($types, ...$params);
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
			array_walk_recursive($ret, "self::pulisciOutput");
			return $ret;
		} catch (mysqli_sql_exception $e) {
			throw new Exception(self::ERR);
		}
	}

	private function preparedInsert(&$query, &$params, $types = "") : bool {
		try {
			$stmt = $this->preparedQuery($query, $params, $types);
			$stmt->close();
		} catch (mysqli_sql_exception $e) {
			if ($e->getCode() == 1062)
				return false;
			else
				throw new Exception(self::ERR);
		}
		return true;
	}

	public function getCollezioneById($id) : array {
		$query = "select nome, descrizione, locandina
			from collezione
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getFilmByCollezioneId($id) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio
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
		$query = "select p.nome, g.nome as gender, p.immagine, p.data_nascita, p.data_morte
			from persona as p
				join gender as g
					on p.gender = g.id
			where p.id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
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
			order by f.id, r.id";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getFilmById($id) : array {
		$query = "select f.id, f.nome, f.nome_originale, f.durata, f.locandina, f.descrizione, f.data_rilascio, f.budget, f.incassi, f.collezione, f.voto, s.nome as stato
			from film as f
				join stato as s
					on f.stato = s.id
			where f.id = ?";

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
		$query = "select u.username as utente, v.valore, v.testo
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

	public function searchFilm($str) : array {
		$query = "select id, nome, locandina, data_rilascio
			from film
			where nome like ?";

		$params = [("%" . trim($str) . "%")];

		return $this->preparedSelect($query, $params);
	}

	public function searchFilmFilteredByGenere($str, $genere) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio
			from film as f
				join film_genere as fg
					on f.id = fg.film
				join genere as g
					on fg.genere = g.id
			where f.nome like ?
				and g.nome = ?";

		$params = [("%" . trim($str) . "%"), $genere];

		return $this->preparedSelect($query, $params);
	}

	public function searchFilmFilteredByPaese($str, $paese) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio
			from film as f
				join film_paese as fp
					on f.id = fp.film
				join paese as p
					on fp.paese = p.iso_3166_1
			where f.nome like ?
				and p.nome = ?";

		$params = [("%" . trim($str) . "%"), $paese];

		return $this->preparedSelect($query, $params);
	}

	public function searchFilmFilteredByData($str, $data) : array {
		// TODO
		return [];
	}

	public function searchCollezione($str) : array {
		$query = "select id, nome, locandina
			from collezione
			where nome like ?";

		$params = [("%" . trim($str) . "%")];

		return $this->preparedSelect($query, $params);
	}

	public function searchPersona($str) : array {
		$query = "select id, nome, immagine
			from persona
			where nome like ?";

		$params = [("%" . trim($str) . "%")];

		return $this->preparedSelect($query, $params);
	}

	public function getUsernameByUserId($id) : array {
		$query = "select username
			from utente
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getListsByUserId($id) : array {
		$query = "select id, nome
			from lista
			where utente = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getListNameByIds($user, $list) : array {
		$query = "select nome
			from lista
			where utente = ? and id = ?";

		$params = [$user, $list];
		$types = "ii";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getListItemsByIds($user, $list) : array {
		$query = "select 'film' as tipo, f.id, f.nome, f.locandina, f.data_rilascio
			from lista as l
				join lista_film as lf
					on l.id = lf.lista
				join film as f
					on lf.film = f.id
			where l.utente = ? and l.id = ?
			union
			select 'collezione' as tipo, c.id, c.nome, c.locandina, null as data_rilascio
			from lista as l
				join lista_collezione as lc
					on l.id = lc.lista
				join collezione as c
					on lc.collezione = c.id
			where l.utente = ? and l.id = ?";

		$params = [$user, $list, $user, $list];
		$types = "iiii";

		return $this->preparedSelect($query, $params, $types);
	}

	public function insertUtente($username, $pass) : bool {
		$query = "insert into utente(username, password)
			values (?, ?)";

		$pass = password_hash($pass, PASSWORD_DEFAULT);
		$params = [$username, $pass];

		return $this->preparedInsert($query, $params);
	}

	public function insertLista($user_id, $list) : bool {
		$query = "insert into lista(utente, nome)
			values (?, ?)";

		$params = [$user_id, $list];

		return $this->preparedInsert($query, $params);
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

	public function signup($username, $password) : array {
		$s = $this->insertUtente($username, $password);

		if ($s) {
			$user_id = $this->connection->insert_id;
			// TODO : transazione
			if ($this->insertLista($user_id, "Da guardare") &&
				$this->insertLista($user_id, "Visti"));
				return $this->login($username, $password);
		}

		return [];
	}

}

?>

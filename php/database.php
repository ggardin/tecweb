<?php

require_once("ini.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class Database {
	private const HOST = "mariadb";
	private const NAME = "rbonavig";
	private const USER = "rbonavig";
	private const PASS = "paJa5The1eiM4hei";

	private const ERR = "Siamo spiacenti, abbiamo riscontrato un errore. Riprova più tardi.";

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
				$p = htmlspecialchars($p, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
			}
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

	public function getFilmInCollezioneById($id) : array {
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

	public function getFilmById($id) : array {
		$query = "select *
			from film
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->preparedSelect($query, $params, $types);
	}

	public function getCrewByFilmId($id) : array {
		$query = "select r.nome as ruolo, p.nome as persona
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

	public function searchCollezione($str) : array {
		$query = "select id, nome, locandina
			from collezione
			where nome like ?";

		$params = [("%" . trim($str) . "%")];

		return $this->preparedSelect($query, $params);
	}

	public function searchFilmByGenere($str) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio
			from film as f
				join film_genere as fg
					on f.id = fg.film
				join genere as g
					on fg.genere = g.id
			where g.nome like ?";

		$params = [trim($str)];

		return $this->preparedSelect($query, $params);
	}

	public function searchFilmByPaese($str) : array {
		$query = "select f.id, f.nome, f.locandina, f.data_rilascio
			from film as f
				join film_paese as fp
					on f.id = fp.film
				join paese as p
					on fp.paese = p.iso_3166_1
			where p.nome like ?";

		$params = [trim($str)];

		return $this->preparedSelect($query, $params);
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

	public function signup($username, $pass) : bool {
		$s = $this->insertUtente($username, $pass);
		if ($s) {
			$user_id = $this->connection->insert_id;
			return ($this->insertLista($user_id, "Da guardare") &&
				$this->insertLista($user_id, "Visti"));
			// TODO: o serve transaction?
		}
		return false;
	}

	public function login($username, $pass) : bool {
		$query = "select password
			from utente
			where username = ?";

		$params = [$username];

		$res = $this->preparedSelect($query, $params);

		$pw = !empty($res) ? $res[0]["password"] : null;
		if ($pw && password_verify($pass, $pw))
			return true;
		return false;
	}
}

?>

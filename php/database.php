<?php

require_once("response.php");

class Database {
	private const HOST = "mariadb";
	private const NAME = "rbonavig";
	private const USER = "rbonavig";
	private const PASS = "paJa5The1eiM4hei";

	private $connection;

	// see: https://phpdelusions.net/mysqli
	public function __construct() {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		try {
			$this->connection = new mysqli(self::HOST, self::USER, self::PASS, self::NAME);
			$this->connection->set_charset("utf8mb4");
			$this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
		} catch (\mysqli_sql_exception $e) { // to avoid leaking connection parameters
			throw new \mysqli_sql_exception($e->getMessage(), $e->getCode());
		}
	}

	public function __destruct() {
		$this->connection->close();
	}

	private function pulisciInput(&$params) : void {
		foreach ($params as &$p) {
			if (is_string($p)) {
				$p = trim($p); // elimina gli spazi
				$p = strip_tags($p); // rimuove tag html (non sempre è una buona idea!)
				$p = htmlentities($p); // converte i caratteri speciali in entità html (ex. &lt;)
			}
		}
	}

	// see: https://phpdelusions.net/mysqli
	private function prepared_query($sql, &$params, $types = "") : mysqli_stmt|false {
		$this->pulisciInput($params);
		$types = $types ?: str_repeat("s", count($params));
		$stmt = $this->connection->prepare($sql);
		array_walk($params, "trim"); // NON FUNZIONA
		$stmt->bind_param($types, ...$params);
		$stmt->execute();
		return $stmt;
	}

	// see: https://phpdelusions.net/mysqli
	private function mysqli_info_array() : array {
		$pattern = '~Rows matched: (?<matched>\d+)  Changed: (?<changed>\d+)  Warnings: (?<warnings>\d+)~';
		preg_match($pattern, $this->connection->info, $matches);
		return array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
	}

	public function getCollezioneById($id) : response {
		$query = "select c.nome, f.titolo, f.data_rilascio
			from collezione as c
				join film as f on c.id = f.collezione
			where c.id = ?";

		$params = [$id];
		$res = $this->prepared_query($query, $params)->get_result();

		return new response(1, "", $res->fetch_all());
		// TODO: altri casi
	}

	public function getPersoneByFilmId($id) : response {
		$query = "SELECT fp.id, fp.persona, p.id, p.nome
			from film as f
				join film_partecipazione as fp on f.id=fp.film
				join persona as p on p.id = fp.persona
			where f.titolo = ?";

		$params = [$id];
		$res = $this->prepared_query($query, $params)->get_result();

		return new response(1, "", $res->fetch_all());
		// TODO: altri casi
	}

	public function getPersonaById($id) : response {
		$query = "select p.id, p.nome, f.id, f.titolo
			from persona as p
				join film_partecipazione as fp on p.id=fp.persona
				join film as f on f.id = fp.film
			where p.id = ?";

		$params = [$id];
		$res = $this->prepared_query($query, $params)->get_result();

		return new response(1, "", $res->fetch_all());
		// TODO: altri casi
	}

	public function signup($user, $pass) : response {
		$query = "insert into utente(username, password)
			values (?, ?)";

		$pass = password_hash($pass, PASSWORD_DEFAULT);
		$params = [$user, $pass];

		try {
			$res = $this->prepared_query($query, $params);
			return new response(1, "Registrato correttamente.");
		} catch (\mysqli_sql_exception $e) {
			return new response(0, "Utente già esistente.");
		}
	}

	public function login($user, $pass) : response {
		$query = "select password
			from utente
			where username=?";

		$params = [$user];
		$res = $this->prepared_query($query, $params)->get_result();
		$user = $res->fetch_assoc();

		if ($user && password_verify($pass, $user["password"]))
			return new response(1, "Accesso eseguito.");
		else
			return new response(0, "Credenziali errate.");
	}
}

?>

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
	private function prepared_query(&$query, &$params, $types = "") : mysqli_stmt {
		$this->pulisciInput($params);
		$types = $types ?: str_repeat("s", count($params));
		$stmt = $this->connection->prepare($query);
		$stmt->bind_param($types, ...$params);
		$stmt->execute();
		return $stmt;
	}

	private function prepared_select(&$query, &$params, $types = "") : array {
		try {
			$stmt = $this->prepared_query($query, $params, $types);
			$result = $stmt->get_result();
			$ret = $result->fetch_all(MYSQLI_ASSOC);
			$result->close();
			$stmt->close();
			return $ret;
		} catch (mysqli_sql_exception $e) {
			throw new Exception(self::ERR);
		}
	}

	private function prepared_insert(&$query, &$params, $types = "") : bool {
		try {
			$stmt = $this->prepared_query($query, $params, $types);
			$stmt->close();
		} catch (mysqli_sql_exception $e) {
			if ($e->getCode() == 1062)
				return false;
			else
				throw new Exception(self::ERR);
		}
		return true;
	}

	// see: https://phpdelusions.net/mysqli
	private function mysqli_info_array() : array {
		$pattern = '~Rows matched: (?<matched>\d+)  Changed: (?<changed>\d+)  Warnings: (?<warnings>\d+)~';
		preg_match($pattern, $this->connection->info, $matches);
		return array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
	}

	public function getCollezioneById($id) : array {
		$query = "select nome, descrizione, copertina
			from collezione
			where id = ?";

		$params = [$id];
		$types = "i";

		return $this->prepared_select($query, $params, $types);
	}

	public function getFilmInCollezioneById($id) : array {
		$query = "select f.id, f.nome, f.copertina, f.data_rilascio
			from collezione as c
				join film as f on c.id = f.collezione
			where c.id = ?
			order by f.data_rilascio";

		$params = [$id];
		$types = "i";

		return $this->prepared_select($query, $params, $types);
	}

	public function signup($user, $pass) : bool {
		$query = "insert into utente(username, password)
			values (?, ?)";

		$pass = password_hash($pass, PASSWORD_DEFAULT);
		$params = [$user, $pass];

		return $this->prepared_insert($query, $params);
	}

	public function login($user, $pass) : bool {
		$query = "select password
			from utente
			where username=?";

		$params = [$user];
		$res = $this->prepared_select($query, $params);

		$pw = !empty($res) ? $res[0]["password"] : null;
		if ($pw && password_verify($pass, $pw))
			return true;
		return false;
	}
}

?>

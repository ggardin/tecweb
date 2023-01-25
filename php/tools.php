<?php

require_once("ini.php");

session_start();

class Tools {
	public static function errCode($num) : void {
		http_response_code($num);
		require ("${num}.php");
	}

	public static function getStringBetween(&$in, $start, $end) : string {
		$pos = strpos($in, $start);
		if ($pos !== false) {
			$pos += strlen($start);
			$len = strpos($in, $end, $pos) - $pos;
			return substr($in, $pos, $len);
		}
		return "";
	}

	public static function replaceAnchor(&$in, $anchor, $content, $comment = false) : void {
		if (! $comment)
			$from = "@@${anchor}@@";
		else
			$from = "<!-- $anchor -->";
		$pos = strpos($in, $from);
		if ($pos !== false) {
			$len = strlen($from);
			$in = substr_replace($in, $content, $pos, $len);
		}
	}

	public static function deleteSectionAnchor(&$in, $name): void {
		self::replaceAnchor($in, ($name . "_start"), "", true);
		self::replaceAnchor($in, ($name . "_end"), "", true);
	}

	public static function deleteAllSectionAnchors(&$in) : void {
		$from = ["/<!-- (\w)+_start -->/", "/<!-- (\w)+_end -->/"];
		$to = ["", ""];
		$in = preg_replace($from, $to, $in);
	}

	public static function getSection(&$in, $name) : string {
		return self::getStringBetween($in, "<!-- ${name}_start -->", "<!-- ${name}_end -->");
	}

	public static function replaceSection(&$in, $name, $content) : void {
		$start = "<!-- ${name}_start -->";
		$end = "<!-- ${name}_end -->";
		$pos = strpos($in, $start);
		if ($pos !== false) {
			$len = strpos($in, $end, $pos) - $pos + strlen($end);
			$in = substr_replace($in, $content, $pos, $len);
		}
	}

	private static function convLang($in, $strip=false) : string {
		$from = ["/\[([a-z]{2,3})\]/", "/\[\/([a-z]{2,3})\]/"];
		$to = (! $strip) ? ['<span lang="${1}">', '</span>'] : ['', ''];
		return preg_replace($from, $to, $in);
	}

	private static function convAbbr($in, $strip=false) : string {
		$from = ["/\{abbr\}([^;]*?){\/abbr\}/", "/\{abbr\}([^\{\};]*?);(.*?){\/abbr\}/s"];
		$to = (! $strip) ? ['<abbr>${1}</abbr>', '<abbr title="${1}">${2}</abbr>'] : ['', ''];
		return preg_replace($from, $to, $in);
	}

	private static function convHelper(&$item, $key, $conv_level) : void {
		if (is_string($item)) {
			$item = htmlspecialchars($item, ENT_QUOTES | ENT_SUBSTITUTE| ENT_HTML5);
			if ($conv_level != 0) {
				$strip = ($conv_level == 1);
				$item = Tools::convLang($item, $strip);
				$item = Tools::convAbbr($item, $strip);
			}
		}
	}

	// conv_level
	// 0: conv specials, keep makers (for editing)
	// 1: conv specials, strip markers (for titles)
	// 2: conv specials, conv markers (for normal pages), DEFAULT
	public static function toHtml(&$in, $conv_level = 2) : void {
		if (is_array($in))
			array_walk_recursive($in, "self::convHelper", $conv_level);
		elseif (is_string($in))
			self::convHelper($in, null, $conv_level);
	}

	private static function deleteCircularLinks(&$page, $name) : void {
		$from = '/<a href="' . $name . '\.php.*?"([^>]*?)>(.*?)<\/a>/s';
		$to = '<span class="active"${1}>${2}</span>';
		$page = preg_replace($from, $to, $page);
	}

	public static function buildPage($name, $type = "std", $active = "") : string {
		$name = basename($name, ".php");

		$page = file_get_contents(__DIR__ . "/../html/${name}.html");
		$shared = file_get_contents(__DIR__ . "/../html/shared.html");

		self::replaceAnchor($page, "head", self::getSection($shared, "head"), true);
		self::replaceAnchor($page, "header", self::getSection($shared, "header"), true);

		if (isset($_SESSION["success"]) || isset($_SESSION["error"])) {
			$server = self::getSection($shared, "server_messages");
			if (isset($_SESSION["success"]))
				self::buildMessages($server, "success", "Successo.");
			elseif (isset($_SESSION["error"]))
				self::buildMessages($server, "error", "Errore.");
			self::replaceAnchor($page, "server_messages", $server, true);
		}

		if ($type == "std") {
			if (isset($_SESSION["id"])) {
				self::replaceAnchor($page, "account_button", "Utente");
				if ($_SESSION["is_admin"] == 0)
					self::replaceSection($page, "header_admin", "");
			} else {
				self::replaceSection($page, "header_user", "");
				self::replaceAnchor($page, "account_button", "Accedi");
			}
      self::replaceAnchor($page, "footer", self::getSection($shared, "footer"), true);
		} elseif ($type == "auth") {
			self::replaceSection($page, "header_user", "");
			self::replaceSection($page, "account", "");
		}

		self::deleteCircularLinks($page, ($active ?: $name));
		return $page;
	}

	private static function buildMessages(&$in, $name, $intro) : void {
		self::replaceAnchor($in, "server_message_intro", $intro);
		self::replaceAnchor($in, "server_message_type", $name);
		self::toHtml($_SESSION[$name]);
		$tmp = self::getSection($in, "server_message");
		$res = "";
		foreach ($_SESSION[$name] as $s) {
			$t = $tmp;
			self::replaceAnchor($t, "server_message", $s);
			$res .= $t;
		}
		self::replaceSection($in, "server_message", $res);
		unset($_SESSION[$name]);
	}

	public static function showPage(&$page) : void {
		self::deleteAllSectionAnchors($page);
		$page = preg_replace('/^\h*\v+/m', '', $page);
		echo($page);
	}

	public static function minutiAStringa($minuti) : string {
		$h = floor($minuti/60);
		$m = $minuti%60;
		$s = "";
		if ($h)
			$s .= $h . ($h>1 ? " ore" : " ora");
		if ($m)
			$s .= ($h ? " " : "") . $m . ($m>1 ? " minuti" : " minuto");
		return $s;
	}

	private static function randString() : string {
		// https://stackoverflow.com/a/31107425
		$length = 32;
		$keyspace = '0123456789abcdefghijklmnopqrstuvwxyz';
		$length = strlen($keyspace) - 1;
		$pieces = [];
		for ($i = 0; $i < $length; ++$i)
			$pieces []= $keyspace[random_int(0, $length)];
		return implode('', $pieces);
	}

	public static function uploadImg($file) : array {
		if (! (file_exists($file['tmp_name']) && is_uploaded_file($file['tmp_name'])))
			return [false, "Errore durante l'upload dell'immagine."];

		// https://www.w3schools.com/php/php_file_upload.asp
		$target_dir = "pics/";

		if (! getimagesize($file["tmp_name"])) {
			unlink ($file["tmp_name"]);
			return [false, "Il file caricato non sembra essere un immagine."];
		}

		if ($file["size"] > 1600000) {
			unlink ($file["tmp_name"]);
			return [false, "Questa immagine pesa troppo. Dimensione massima: 1.5MB."];
		}

		$imagetype = mime_content_type($file["tmp_name"]);
		if ($imagetype != "image/jpeg" && $imagetype != "image/png" && $imagetype != "image/webp") {
			unlink ($file["tmp_name"]);
			return [false, "Formato immagine non supportato. Carica uno tra: JPG, JPEG, PNG, WEBP."];
		}

		$r = 1.5;
		$w0 = 200; $h0 = $w0 * $r;
		$w1 = 500; $h1 = $w1 * $r;

		do {
			$filename = self::randString();
		} while (file_exists($target_dir . "${w0}_" . $filename . ".webp"));

		if ($imagetype == "image/jpeg")
			$source = imagecreatefromjpeg($file["tmp_name"]);
		elseif ($imagetype == "image/png")
			$source = imagecreatefrompng($file["tmp_name"]);
		else
			$source = imagecreatefromwebp($file["tmp_name"]);

		list($width, $height) = getimagesize($file["tmp_name"]);

		if(($height / $width) < $r) {
			$y = 0;
			$x = intval(($width - ($height / $r)) / 2);
			$width -= 2 * $x;
		} else {
			$x = 0;
			$y = intval(($height - ($width * $r)) / 2);
			$height -= 2 * $y;
		}

		$pic0 = imagecreatetruecolor($w0, $h0);
		$pic1 = imagecreatetruecolor($w1, $h1);

		imagecopyresampled($pic0, $source, 0, 0, $x, $y, $w0, $h0, $width, $height);
		imagecopyresampled($pic1, $source, 0, 0, $x, $y, $w1, $h1, $width, $height);

		$fn0 = $target_dir . "w${w0}_" . $filename . ".webp";
		$fn1 = $target_dir . "w${w1}_" . $filename . ".webp";

		imagewebp($pic0, $fn0);
		imagewebp($pic1, $fn1);

		unlink ($file["tmp_name"]);

		return [true, $filename];
	}

}

?>
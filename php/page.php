<?php

class Page {
	private static function getStringBetween(&$in, $start, $end) {
		$p = strpos($in, $start);
		if ($p) {
			$p += strlen($start);
			$len = strpos($in, $end, $p) - $p;
			return trim(substr($in, $p, $len));
		}
		return "";
	}

	private static function replaceSection(&$page, $shared, $name) {
		$s = self::getStringBetween($shared, "<!-- ${name}Start -->", "<!-- ${name}End -->");
		$page = str_replace("<!-- ${name} -->", $s, $page);
	}

	private static function setActiveHeader (&$page, $active) {
		// !!! preg_replace; passando per reference magari, invece che fare copia e assegnare ogni volta
		//
		// $a = self::getStringBetween($page, "<li><a href=\"${active}\">", "</a></li>", $page);
		// $page = str_replace("<li><a href=\"${active}\">", "<li id=\"active\">", $page);
	}

	public static function build($name, $activeHeader) {
		$page = file_get_contents(__DIR__ . "/../html/${name}.html");
		$shared = file_get_contents(__DIR__ . "/../html/shared.html");

		self::replaceSection($page, $shared, "head");
		self::replaceSection($page, $shared, "header");
		self::replaceSection($page, $shared, "footer");

		self::setActiveHeader($page, $activeHeader);

		return $page;
	}
}

?>

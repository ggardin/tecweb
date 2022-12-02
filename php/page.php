<?php

class Page {
	public static function getStringBetween(&$in, $start, $end) {
		$p = strpos($in, $start);
		if ($p) {
			$p += strlen($start);
			$len = strpos($in, $end, $p) - $p;
			return trim(substr($in, $p, $len));
		}
		return "";
	}

	private static function getSection(&$page, $name) {
		return self::getStringBetween($page, "<!-- ${name}Start -->", "<!-- ${name}End -->");
	}

	private static function replaceSection(&$page, &$shared, $name) {
		$from = "<!-- ${name} -->";
		$pos = strpos($page, $from);
		$len = strlen($from);
		$page = substr_replace($page, self::getSection($shared, $name), $pos, $len);
	}

	private static function setActiveHeader(&$page, &$shared, $name) {
		$open = '<li><a href="' . $name . '.php">';

		if (strpos(self::getSection($shared, "header"), $open)) {
			$close = "</a></li>";

			$bw = self::getStringBetween($page, $open, $close);

			$from = $open . $bw . $close;
			$to = '<li class="active">' . $bw . '</li>';

			$pos = strpos($page, $open);
			$len = strlen($from);

			$page = substr_replace($page, $to, $pos, $len);
		}
	}

	public static function build($name) {
		$page = file_get_contents(__DIR__ . "/../html/${name}.html");
		$shared = file_get_contents(__DIR__ . "/../html/shared.html");

		self::replaceSection($page, $shared, "head");
		self::replaceSection($page, $shared, "header");
		self::replaceSection($page, $shared, "footer");

		self::setActiveHeader($page, $shared, $name);

		return $page;
	}
}

?>

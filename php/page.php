<?php

require_once("ini.php");

class Page {
	public static function getStringBetween(&$in, $start, $end) : string {
		if ($p = strpos($in, $start)) {
			$p += strlen($start);
			$len = strpos($in, $end, $p) - $p;
			return trim(substr($in, $p, $len));
		}
		return "";
	}

	public static function replaceAnchor(&$page, $anchor, $content) : void {
		$from = "<!-- $anchor -->";
		if ($pos = strpos($page, $from)) {
			$len = strlen($from);
			$page = substr_replace($page, $content, $pos, $len);
		}
	}

	public static function langToNone($str) : string {
		$str = preg_replace("#\[([a-z]{2})\]#", '', $str);
		$str = preg_replace("#\[\/([a-z]{2})\]#", '', $str);
		return $str;
	}

	public static function langToSpan($str) : string {
		$str = preg_replace("#\[([a-z]{2})\]#", '<span lang="${1}">', $str);
		$str = preg_replace("#\[\/([a-z]{2})\]#", '</span>', $str);
		return $str;
	}

	private static function getSection(&$page, $name) : string {
		return self::getStringBetween($page, "<!-- ${name}_start -->", "<!-- ${name}_end -->");
	}

	private static function replaceSection(&$page, &$shared, $name) : void {
		self::replaceAnchor($page, $name, self::getSection($shared, $name));
	}

	private static function setActiveHeader(&$page, &$shared, $name) : void {
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

	public static function build($name, $type="std", $active=false) : string {
		$page = file_get_contents(__DIR__ . "/../html/${name}.html");
		$shared = file_get_contents(__DIR__ . "/../html/shared_${type}.html");

		self::replaceSection($page, $shared, "head");
		self::replaceSection($page, $shared, "header");
		self::replaceSection($page, $shared, "footer");

		if ($active) self::setActiveHeader($page, $shared, $name);

		return $page;
	}
}

?>

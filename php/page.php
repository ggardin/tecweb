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

	public static function langToTag($str, $tag = "span") : string {
		$from = ["#\[([a-z]{2})\]#", "#\[\/([a-z]{2})\]#"];
		if ($tag != '')
			$to = ['<' . $tag . ' lang="${1}">', '</' . $tag . '>'];
		else
			$to = ['', ''];
		return preg_replace($from, $to, $str);
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

	public static function build($name, $type = "std", $setActive = false) : string {
		$page = file_get_contents(__DIR__ . "/../html/${name}.html");
		$shared = file_get_contents(__DIR__ . "/../html/shared_${type}.html");

		self::replaceSection($page, $shared, "head");
		self::replaceSection($page, $shared, "header");
		self::replaceSection($page, $shared, "footer");

		if ($setActive) self::setActiveHeader($page, $shared, $name);

		return $page;
	}
}

?>

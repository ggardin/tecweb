<?php

require_once("ini.php");

class Tools {
	public static function getStringBetween(&$in, $start, $end) : string {
		$pos = strpos($in, $start);
		if ($pos !== false) {
			$pos += strlen($start);
			$len = strpos($in, $end, $pos) - $pos;
			return substr($in, $pos, $len);
		}
		return "";
	}

	public static function replaceAnchor(&$in, $anchor, $content) : void {
		$from = "<!-- $anchor -->";
		$pos = strpos($in, $from);
		if ($pos !== false) {
			$len = strlen($from);
			$in = substr_replace($in, $content, $pos, $len);
		}
	}

	public static function deleteSectionAnchor(&$in, $name): void {
		self::replaceAnchor($in, ($name . "_start"), "");
		self::replaceAnchor($in, ($name . "_end"), "");
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

	public static function langToTag(&$in, $tag = "span") : string {
		$from = ["/\[([a-z]{2})\]/", "/\[\/([a-z]{2})\]/"];
		if ($tag != '')
			$to = ['<' . $tag . ' lang="${1}">', '</' . $tag . '>'];
		else
			$to = ['', ''];
		return preg_replace($from, $to, $in);
	}

	private static function replacePageSection(&$page, &$shared, $name) : void {
		self::replaceAnchor($page, $name, self::getSection($shared, $name));
	}

	private static function setPageActiveHeader(&$page, $name) : void {
		$open = '<li><a href="' . $name . '.php">';
		$pos = strpos($page, $open);
		if ($pos !== false) {
			$close = "</a></li>";
			$bw = self::getStringBetween($page, $open, $close);
			$len = strlen($open . $bw . $close);
			$to = '<li class="active">' . $bw . '</li>';
			$page = substr_replace($page, $to, $pos, $len);
		}
	}

	public static function buildPage($name, $type = "std", $setActive = false) : string {
		$page = file_get_contents(__DIR__ . "/../html/${name}.html");
		$shared = file_get_contents(__DIR__ . "/../html/shared_${type}.html");

		if ($setActive) self::setPageActiveHeader($shared, $name);

		self::replacePageSection($page, $shared, "head");
		self::replacePageSection($page, $shared, "header");
		self::replacePageSection($page, $shared, "footer");

		return $page;
	}

	public static function showPage(&$page) : void {
		self::deleteAllSectionAnchors($page);
		$page = preg_replace('/^\h*\v+/m', '', $page);
		echo($page);
	}
}

?>

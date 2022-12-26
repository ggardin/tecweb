<?php

require_once("ini.php");

class Tools {
	public static function errCode($num) : void {
		http_response_code($num);
		include ("${num}.php");
		exit();
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

	private static function convAbbr1($in, $strip=false) : string {
		$from = "/\{abbr\}(.*?);(.*)\{\/abbr\}/";
		$to = (! $strip) ? '<abbr title="${1}">${2}</abbr>' : '';
		return preg_replace($from, $to, $in);
	}

	private static function convAbbr2($in, $strip=false) : string {
		$from = "/\{abbr\}(.*?)\{\/abbr\}/";
		$to = (! $strip) ? '<abbr>${1}</abbr>' : '';
		return preg_replace($from, $to, $in);
	}

	private static function convHelper(&$item, $key, $conv_level) : void {
		if (! is_null($item)) {
			if ($conv_level)
				$item = htmlspecialchars($item, ENT_QUOTES | ENT_SUBSTITUTE| ENT_HTML5);
			if ($conv_level != 1) {
				$strip = ($conv_level == 0);
				$item = Tools::convLang($item, $strip);
				$item = Tools::convAbbr1($item, $strip);
				$item = Tools::convAbbr2($item, $strip);
			}
		}
	}

	// conv_level
	// 0: keep specials, strip markers (for titles)
	// 1: conv specials, keep makers (for editing)
	// 2: conv specials, conv markers (for normal pages), DEFAULT
	public static function toHtml(&$in, $conv_level = 2) : void {
		if (is_array($in))
			array_walk_recursive($in, "self::convHelper", $conv_level);
		elseif (is_string($in))
			self::convHelper($in, null, $conv_level);
	}

	private static function replacePageSection(&$page, &$shared, $name) : void {
		self::replaceAnchor($page, $name, self::getSection($shared, $name), true);
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

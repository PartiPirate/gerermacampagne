<?php /*
	Copyright 2016 Cédric Levieux, Parti Pirate

	This file is part of GererMaCampagne.

    GererMaCampagne is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GererMaCampagne is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GererMaCampagne.  If not, see <http://www.gnu.org/licenses/>.
*/

if (isset($lang)) {
    $lang = array();
}

function getLanguage() {
	if (isset($_SESSION["language"]) && $_SESSION["language"]) {
		return $_SESSION["language"];
	}

	global $config;

	if (isset($config["default_language"]) && $config["default_language"]) {
		return $config["default_language"];
	}

    return "fr";
}

function isLanguageKey($key, $language = null) {
	global $lang;

	if (!$language) {
		$language = getLanguage();
	}

	if (!count($lang)) {
		$directoryHandler = dir("language/" . $language);
		while(($fileEntry = $directoryHandler->read()) !== false) {
			if($fileEntry != '.' && $fileEntry != '..' && strpos($fileEntry, ".php")) {
				include_once("language/" . $language . "/" . $fileEntry);
			}
		}
		$directoryHandler->close();

		// Uncomment if you're not in UTF-8
		// $lang = changeCharset($lang);
	}

	if (array_key_exists($key, $lang)) {
		return true;
	}

	return false;
}

function lang($key, $htmlencode = true, $language = null) {
    global $lang;

    if (isLanguageKey($key, $language)) {
    	$text = $lang[$key];

    	if ($htmlencode) {
	    	$text = htmlentities($text, ENT_NOQUOTES);
	    	$text = htmlspecialchars_decode($text);
    	}

        return $text;
    }

    return '$lang["'.$key.'"] = "";';
}
?>
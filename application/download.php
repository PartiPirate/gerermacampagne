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
session_start();

function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

include_once("config/database.php");
require_once("engine/bo/DocumentBo.php");

$requestDocumentPath = $_REQUEST["document"];

$connection = openConnection();

$documentBo = DocumentBo::newInstance($connection);
$document = $documentBo->getByPath($requestDocumentPath);
//$document = array("doc_name" => "Koala.jpg", "doc_mime_type" => "image/jpeg");
$basePath = $_SERVER["SCRIPT_FILENAME"];
$basePath = substr($basePath, 0, strrpos($basePath, "/") + 1);

$documentPath = $config["document_directory"];
if (!endsWith($documentPath, "/")) {
	$documentPath .= "/";
}
$documentPath .= $requestDocumentPath;

header("Content-Type: " . $document["doc_mime_type"]);
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . $document["doc_name"] . "\"");
readfile($documentPath);

?>
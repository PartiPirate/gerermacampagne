<?php // $Id$
	ini_set("include_path", ini_get("include_path").":/usr/local/apache/htdocs");
	require_once "HTTP/WebDAV/Server/gmc_filesystem.php";
	include_once "config/database.php";

	$server = new HTTP_WebDAV_Server_GMCFilesystem();
// Pour corriger passer outre le code interne de la classe
	$server->_SERVER['SCRIPT_NAME'] = '';
	$server->documentBase = "/var/www/html/gerermacampagne/";
	$server->ServeRequest($_SERVER["DOCUMENT_ROOT"]);
?>
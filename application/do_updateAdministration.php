<?php /*
	Copyright 2015 Cédric Levieux, Parti Pirate

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

$data = array();

if (!$_SESSION["administrator"]) {
	$data["ko"] = "not_enough_rights";
	echo json_encode($data, JSON_NUMERIC_CHECK);
	exit();
}

$data["ok"] = "ok";

// config.php
$configDotPhp = "<?php
if(!isset(\$config)) {
	\$config = array();
}

\$config[\"administrator\"] = array();
\$config[\"administrator\"][\"login\"] = \"" . $_REQUEST["administrator_login_input"] . "\";
\$config[\"administrator\"][\"password\"] = \"" . $_REQUEST["administrator_password_input"] . "\";

\$config[\"database\"] = array();
\$config[\"database\"][\"host\"] = \"" . $_REQUEST["database_host_input"] . "\";
\$config[\"database\"][\"port\"] = " . $_REQUEST["database_port_input"] . ";
\$config[\"database\"][\"login\"] = \"" . $_REQUEST["database_login_input"] . "\";
\$config[\"database\"][\"password\"] = \"" . $_REQUEST["database_password_input"] . "\";
\$config[\"database\"][\"database\"] = \"" . $_REQUEST["database_database_input"] . "\";
\$config[\"database\"][\"prefix\"] = \"\";

\$config[\"server\"] = array();
\$config[\"server\"][\"base\"] = \"" . $_REQUEST["server_base_input"] . "\";
// The server line, ex : dev, beta - Leave it empty for production
\$config[\"server\"][\"line\"] = \"" . $_REQUEST["server_line_input"] . "\";
\$config[\"server\"][\"timezone\"] = \"" . $_REQUEST["server_timezone_input"] . "\";

?>";

// mail.config.php
$mailConfigDotPhp = "<?php
if(!isset(\$config)) {
	\$config = array();
}

\$config[\"smtp\"] = array();
\$config[\"smtp\"][\"host\"] = \"" . $_REQUEST["smtp_host_input"] . "\";
\$config[\"smtp\"][\"port\"] = \"" . $_REQUEST["smtp_port_input"] . "\";
\$config[\"smtp\"][\"username\"] = \"" . $_REQUEST["smtp_username_input"] . "\";
\$config[\"smtp\"][\"password\"] = \"" . $_REQUEST["smtp_password_input"] . "\";
\$config[\"smtp\"][\"secure\"] = \"" . $_REQUEST["smtp_secure_input"] . "\";
\$config[\"smtp\"][\"from.address\"] = \"" . $_REQUEST["smtp_from_address_input"] . "\";
\$config[\"smtp\"][\"from.name\"] = \"" . $_REQUEST["smtp_from_name_input"] . "\";

?>";

// salt.php
$saltDotPhp = "<?php
if(!isset(\$config)) {
	\$config = array();
}

\$config[\"salt\"] = \"" . $_REQUEST["salt_input"] . "\";
\$config[\"default_language\"] = \"" . $_REQUEST["default_language_input"] . "\";
\$config[\"document_directory\"] = \"" . $_REQUEST["document_directory_input"] . "\";

?>";

if (file_exists("config/config.php")) {
	if (file_exists("config/config.php~")) {
		unlink("config/config.php~");
	}
	rename("config/config.php", "config/config.php~");
}
file_put_contents("config/config.php", $configDotPhp);

if (file_exists("config/mail.config.php")) {
	if (file_exists("config/mail.config.php~")) {
		unlink("config/mail.config.php~");
	}
	rename("config/mail.config.php", "config/mail.config.php~");
}
file_put_contents("config/mail.config.php", $mailConfigDotPhp);

if (file_exists("config/salt.php")) {
	if (file_exists("config/salt.php~")) {
		unlink("config/salt.php~");
	}
	rename("config/salt.php", "config/salt.php~");
}
file_put_contents("config/salt.php", $saltDotPhp);

echo json_encode($data, JSON_NUMERIC_CHECK);
?>
<?php /*
	Copyright 2016-2017 Cédric Levieux, Parti Pirate

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
include_once("config/database.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/MessageBo.php");

session_start();

$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$messageBo = MessageBo::newInstance($connection, $config);

$data = array();

$message = $messageBo->getById($_REQUEST["mes_id"]);
if ($message == null) {
	$data["ko"] = "ko";
}
else if ($message["mes_code"] != $_REQUEST["mes_code"]) {
	$data["ko"] = "ko";
//	$data["message"] = $message;
}
else {
	$message = array("mes_id" => $message["mes_id"]);
	$message["mes_read"] = 1;
	
	$messageBo->save($message);

	$data["ok"] = "ok";
}

echo json_encode($data);
?>
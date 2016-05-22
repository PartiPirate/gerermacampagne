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
include_once("config/database.php");
require_once("engine/bo/UserBo.php");

// TODO verify referer
if (!isset($_SERVER["HTTP_REFERER"]) || !$_SERVER["HTTP_REFERER"]) {
	exit();
}

$userBo = UserBo::newInstance(openConnection());

$user = $_REQUEST["user"];

$data = array();
$userId = $userBo->getUserId($user);

if ($userId) {
	$data["id"] = $userId;
}

echo json_encode($data);
?>
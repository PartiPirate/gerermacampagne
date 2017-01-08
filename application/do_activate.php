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
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/UserBo.php");

$userBo = UserBo::newInstance(openConnection());

$login = $_REQUEST["login"];
$password = $_REQUEST["password"];
$confirmation = $_REQUEST["confirmation"];
$code = $_REQUEST["code"];
$mail = $_REQUEST["mail"];

$user = $userBo->getUserByMail($mail);

$data = array();

if ($user && ($user["use_activation_key"] == $code) && $login && $password && ($confirmation == $password)) {
	
	$updateUser = array();
	$updateUser["use_login"] = $login;
	
	$hashedPassword = UserBo::computePassword($password);

	$updateUser["use_password"] = $hashedPassword;
	$updateUser["use_id"] = $user["use_id"];
	
	$userBo->update($updateUser);
	$userBo->activate($mail, $code);

	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
}

echo json_encode($data);

?>
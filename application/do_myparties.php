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
require_once("engine/bo/PoliticalPartyBo.php");
require_once("engine/utils/SessionUtils.php");

$user = SessionUtils::getUser($_SESSION);
$userId = SessionUtils::getUserId($_SESSION);
$ppBo = PoliticalPartyBo::newInstance(openConnection());
$data = array();

$partyId = $_REQUEST["id"];

$party = array();
$party["ppa_id"] = $_REQUEST["id"];
$party["ppa_name"] = $_REQUEST["name"];

$administratorIds = json_decode($_REQUEST["administratorIds"]);

$party["administrators"] = array();
foreach($administratorIds as $administratorId) {
	$party["administrators"][] = array("use_id" => $administratorId, "use_login" => "");
}

$ppBo->save($party);

// If the id is 0 then it's an add, so we return the id
if (!$partyId) {
	$data["id"] = $party["ppa_id"];
}
$data["name"] = $party["ppa_name"];
$data["ok"] = "ok";

echo json_encode($data);
?>
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
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/PoliticalPartyBo.php");

$affiliationId = $_REQUEST["affiliationId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$ppBo = PoliticalPartyBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);

$administratedParties = array();
if ($userId) {
	$administratedParties = $ppBo->getAdministratedParties($userId);

	$campaignBo->refuseAffiliation($affiliationId, $administratedParties);
}

$data["ok"] = "ok";

echo json_encode($data);
?>
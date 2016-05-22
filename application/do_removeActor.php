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
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$campaignId = $_REQUEST["campaignId"];
$rightId = $_REQUEST["rightId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$campaignBo = CampaignBo::newInstance($connection);
$userBo = UserBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);

$canDelete = false;

if (!$campaign) {
}
else if ($campaign["uri_right"] == "candidate" || $campaign["uri_right"] == "listHead")
{
	$canDelete = true;
}
else {
	$campaign["actors"] = $campaignBo->getRighters($campaign["cam_id"], array("substitute", "representative"));

	foreach($campaign["actors"] as $actor) {
		if ($actor["uri_id"] == $rightId && $actor["use_id"] == $userId) {
			$canDelete = true;
			break;
		}
	}
}

if ($canDelete) {
	$userBo->removeRight($rightId, $campaignId);

	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
	$data["message"] = "not_enough_right";
}

echo json_encode($data);
?>
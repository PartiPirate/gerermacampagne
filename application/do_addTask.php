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
require_once("engine/bo/TaskBo.php");
require_once("engine/utils/SessionUtils.php");

$campaignId = $_REQUEST["campaignId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$taskBo = TaskBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);
$userBo = UserBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);
$canAdd = false;

if (!$campaign) {
}
else //if ($campaign["uri_right"] == "candidate" || $campaign["uri_right"] == "listHead")
{
	$canAdd = true;
}

if ($canAdd) {
	$task = array();

	$task["tas_campaign_id"] = $campaign["cam_id"];

	// Compute order
	if ($_REQUEST["order"] == "after") {
		$task["tas_order"] = $taskBo->computeAfterOrder($campaign["cam_id"], $_REQUEST["afterTaskId"]);
	}
	else if ($_REQUEST["order"] == "toEnd") {
		$task["tas_order"] = $taskBo->computeToEndOrder($campaign["cam_id"]);
	}

	if (isset($_REQUEST["dependencies"])) {
		$dependencies = $_REQUEST["dependencies"];
	}
	else {
		$dependencies = array();
	}

	$task["tas_dependencies"] = "[".implode(",", $dependencies)."]";
	$task["tas_implies"] = "[]";

	$task["tas_label"] = $_REQUEST["labelInput"];
	$task["tas_form"] = "doTask";

	$task["tas_righters"] = $_REQUEST["validatorInput"];

	if (isset($_REQUEST["limitDateInput"]) && $_REQUEST["limitDateInput"]) {
		$task["tas_limit_date"] = $_REQUEST["limitDateInput"];
	}
	else {
		$task["tas_limit_date"] = "0000-00-00";
	}

	$task["tas_status"] = "inProgress";
	$task["tas_documents"] = "[]";

	$taskBo->insertTask($task);

	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
	$data["message"] = "not_enough_right";
}

echo json_encode($data);
?>
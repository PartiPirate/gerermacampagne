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
require_once("engine/bo/AddressBo.php");
require_once("engine/bo/BookInlineBo.php");
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/DocumentBo.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/UserBo.php");

$campaignId = $_REQUEST["campaignId"];

$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$campaignBo = CampaignBo::newInstance($connection);

$campaign = $campaignBo->getCampaign($campaignId);

if (!$campaign) exit();
if ($campaign["cam_reject_code"] != $_REQUEST["rejectCode"]) exit();

$campaign = array(	"cam_id" => $campaignId,
					"cam_political_party_id" => null,
					"cam_political_party_date" => "0000-00-00");
$campaignBo->save($campaign);

$data["ok"] = "ok";

echo json_encode($data);
?>
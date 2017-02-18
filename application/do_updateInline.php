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
require_once("engine/bo/BookInlineBo.php");
require_once("engine/bo/CampaignBo.php");

$campaignId = $_REQUEST["campaignId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$bookBo = BookInlineBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);

if (!$campaign) exit();

$inlineId = $_REQUEST["inlineId"];
$inlines = $bookBo->getInlines($campaign, array("bin_id" => $inlineId));

if (!count($inlines)) exit();

$inline = $inlines[$inlineId];

if ($inline["bin_secure_code"] != $_REQUEST["inlineCode"]) exit();

$inline = array("bin_id" => $inline["bin_id"]);

$property = $_REQUEST["property"];
$value = $_REQUEST["value"];

switch($property) {
    case "bin_amount":
    case "bin_label":
        break;
    default:
        exit();
}

$inline[$property] = $value;

$bookBo->update($inline);

$data["ok"] = "ok";

echo json_encode($data);
?>
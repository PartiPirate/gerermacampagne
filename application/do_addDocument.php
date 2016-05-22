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
require_once("engine/bo/DocumentBo.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/PoliticalPartyBo.php");
require_once("engine/bo/UserBo.php");

$target = $_REQUEST["target"];
$targetId = $_REQUEST["targetId"];
$userId = SessionUtils::getUserId($_SESSION);

if (!isset($_FILES["documentFile"])) {
	echo json_encode(array("ko" => "no_file"));
	exit();
}

$connection = openConnection();

$file = $_FILES["documentFile"];

$data = array();

$document = array();
$document["doc_task_id"] = null;

if ($target == "party") {
	$document["doc_campaign_id"] = null;
	$document["doc_political_party_id"] = $targetId;

	$ppBo = PoliticalPartyBo::newInstance($connection);

	$administratedParties = $ppBo->getAdministratedParties($userId);

	$partyFound = false;
	foreach($administratedParties as $party) {
		if ($party["ppa_id"] == $targetId) {
			$partyFound = true;
			break;
		}
	}

	if (!$partyFound) {
		$data["ko"] = "no_party";
		echo json_encode($data);
		exit();
	}
}
else if ($target == "campaign") {
	$document["doc_campaign_id"] = $targetId;
	$document["doc_political_party_id"] = null;

	$campaignBo = CampaignBo::newInstance($connection);

	$campaign = $campaignBo->getUserCampaign($userId, $targetId);

	if (!$campaign) {
		echo json_encode(array("ko" => "no_campaign"));
	}
}
else {
	exit();
}

$basePath = $_SERVER["SCRIPT_FILENAME"];
$basePath = substr($basePath, 0, strrpos($basePath, "/") + 1);

$documentPath = $config["document_directory"];
if (!endsWith($documentPath, "/")) {
	$documentPath .= "/";
}

$data['files']["checkFile"]['src'] = $file["name"];

$filename = time() . rand(0, time());
$computeFilename = UserBo::computePassword($filename);

move_uploaded_file($file["tmp_name"], $basePath . $documentPath . $computeFilename);

$document["doc_name"] = $file["name"];
$document["doc_size"] = $file["size"];
$document["doc_mime_type"] = $file["type"];
$document["doc_label"] = $_REQUEST["label"];
$document["doc_path"] = $documentPath . $computeFilename;

$documentBo = DocumentBo::newInstance($connection);
$documentBo->addDocument($document);

$data["ok"] = "ok";

echo json_encode($data);
?>
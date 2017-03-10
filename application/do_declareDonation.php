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
require_once("engine/bo/AddressBo.php");
require_once("engine/bo/BookInlineBo.php");
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/DocumentBo.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/UserBo.php");

$campaignId = $_REQUEST["campaignId"];
$amount = $_REQUEST["amount"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$addressBo = AddressBo::newInstance($connection);
$bookBo = BookInlineBo::newInstance($connection);
$taskBo = TaskBo::newInstance($connection);
$documentBo = DocumentBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);

if (!$campaign) exit();

$taskBo->duplicateTaskModel($campaign, "ask_received_donation");
$taskBo->duplicateTaskModel($campaign, "receive_received_donation");
$taskBo->duplicateTaskModel($campaign, "fill_received_donation");
$taskBo->duplicateTaskModel($campaign, "search_chartered_accountant");

$task = $taskBo->getTaskByLabel($campaign, "search_chartered_accountant");
$righters = $campaignBo->getRighters($campaignId, array("charteredAccountant"));

if (count($righters)) {
	$task = array("tas_id" => $task["tas_id"], "tas_status" => "done");
	$taskBo->updateTask($task);
}

$basePath = $_SERVER["SCRIPT_FILENAME"];
$basePath = substr($basePath, 0, strrpos($basePath, "/") + 1);

$documentPath = $config["document_directory"];
if (!endsWith($documentPath, "/")) {
	$documentPath .= "/";
}

$inline = array();
$inline["bin_campaign_id"] = $campaignId;
$inline["bin_label"] = $_REQUEST["label"];
$inline["bin_amount"] = $amount;
$inline["bin_book"] = "campaign";
$inline["bin_column"] = "input";
$inline["bin_type"] = "donation";
$inline["bin_code"] = $_REQUEST["code"];
$inline["bin_payment_type"] = $_REQUEST["paymentType"];

if (isset($_REQUEST["inlineDate"]) && $_REQUEST["inlineDate"]) {
	$inline["bin_transaction_date"] = $_REQUEST["inlineDate"];
}
else {
	$date = new DateTime();
	$inline["bin_transaction_date"] = $date->format("Y-m-d");
}

$bookBo->addInline($inline);

if (isset($_FILES["checkFile"]) && $inline["bin_payment_type"] == "check") {
	$file = $_FILES["checkFile"];

	$document = array();

	$data['files']["checkFile"]['src'] = $file["name"];

	$filename = time() . rand(0, time());
	$computeFilename = UserBo::computePassword($filename);

	move_uploaded_file($file["tmp_name"], $basePath . $documentPath . $computeFilename);

	$document = array();
	$document["doc_task_id"] = null;
	$document["doc_political_party_id"] = null;
	$document["doc_campaign_id"] = $campaignId;
	$document["doc_name"] = $file["name"];
	$document["doc_size"] = $file["size"];
	$document["doc_mime_type"] = $file["type"];
	$document["doc_label"] = "check";
	$document["doc_path"] = $documentPath . $computeFilename;
	$documentBo->addDocument($document);

	$inlineDocument = array();
	$inlineDocument["ido_document_id"] = $document["doc_id"];
	$inlineDocument["ido_book_inline_id"] = $inline["bin_id"];
	$inlineDocument["ido_type"] = "check";
	$bookBo->addInlineDocument($inlineDocument);
}

// Donation
$address = array();
$address["add_entity"] = $_REQUEST["firstname"] . " " . $_REQUEST["lastname"];
$address["add_line_1"] = $_REQUEST["line1"];
$address["add_line_2"] = $_REQUEST["line2"];
$address["add_zip_code"] = $_REQUEST["zipCode"];
$address["add_city"] = $_REQUEST["city"];
$address["add_company_name"] = "";
$address["add_country_id"] = 1;
$addressBo->addAddress($address);

$donation = array();
$donation["don_book_inline_id"] = $inline["bin_id"];
$donation["don_firstname"] = $_REQUEST["firstname"];
$donation["don_lastname"] = $_REQUEST["lastname"];
$donation["don_address_id"] = $address["add_id"];
$bookBo->addDonation($donation);

$data["ok"] = "ok";

echo json_encode($data);
?>
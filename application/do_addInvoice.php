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
require_once("engine/bo/InvoicePaymentBo.php");

$campaignId = $_REQUEST["campaignId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$addressBo = AddressBo::newInstance($connection);
$bookBo = BookInlineBo::newInstance($connection);
$taskBo = TaskBo::newInstance($connection);
$documentBo = DocumentBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);
$invoicePaymentBo = InvoicePaymentBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);

if (!$campaign) exit();

$amount = $_REQUEST["amount"];
$invoiceSource = $_REQUEST["invoiceSource"];

$basePath = $_SERVER["SCRIPT_FILENAME"];
$basePath = substr($basePath, 0, strrpos($basePath, "/") + 1);

$documentPath = $config["document_directory"];
if (!endsWith($documentPath, "/")) {
	$documentPath .= "/";
}

if ($invoiceSource == "fromQuotation") {

	$inline = array("bin_id" => $_REQUEST["quotationSelect"]);
	$inlines = $bookBo->getInlines($campaign, array("bin_id" => $inline["bin_id"]));

	if (count($inlines)) {

		$inline["bin_amount"] = $amount;
		$inline["bin_type"] = "invoice";

		if (isset($_REQUEST["inlineDate"]) && $_REQUEST["inlineDate"]) {
			$inline["bin_transaction_date"] = $_REQUEST["inlineDate"];
		}
		else {
			$date = new DateTime();
			$inline["bin_transaction_date"] = $date->format("Y-m-d");
		}

		$bookBo->update($inline);
	}
}
else {

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

	$inline = array();
	$inline["bin_campaign_id"] = $campaignId;
	$inline["bin_label"] = $_REQUEST["label"];
	$inline["bin_amount"] = $amount;
	$inline["bin_book"] = "campaign";
	$inline["bin_column"] = "output";
	$inline["bin_type"] = "invoice";
	$inline["bin_code"] = $_REQUEST["code"];
	$inline["bin_payment_type"] = null;

	if (isset($_REQUEST["inlineDate"]) && $_REQUEST["inlineDate"]) {
		$inline["bin_transaction_date"] = $_REQUEST["inlineDate"];
	}
	else {
		$date = new DateTime();
		$inline["bin_transaction_date"] = $date->format("Y-m-d");
	}

	$bookBo->addInline($inline);
}

if (isset($_FILES["invoiceFile"])) {
	$file = $_FILES["invoiceFile"];

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
	$document["doc_label"] = "invoice";
	$document["doc_path"] = $documentPath . $computeFilename;
	$documentBo->addDocument($document);

	$inlineDocument = array();
	$inlineDocument["ido_document_id"] = $document["doc_id"];
	$inlineDocument["ido_book_inline_id"] = $inline["bin_id"];
	$inlineDocument["ido_type"] = "invoice";
	$bookBo->addInlineDocument($inlineDocument);
}

// The candidate pay something, so we need to add the payment and add a credit line
if ($inline["bin_code"] == "6613" || $inline["bin_code"] == "6789") {

	$creditInline = array();
	$creditInline["bin_amount"] = $inline["bin_amount"];
	$creditInline["bin_book"] = $inline["bin_book"];
	$creditInline["bin_column"] = "input";
	$creditInline["bin_type"] = "donation";
	$creditInline["bin_campaign_id"] = $inline["bin_campaign_id"];
	$creditInline["bin_label"] = "Paiement de la facture &laquo; " . $inline["bin_label"] . " &raquo;";
	$creditInline["bin_transaction_date"] = $inline["bin_transaction_date"];
	$creditInline["bin_payment_type"] = null;

	switch($inline["bin_code"]) {
		case "6613":
			$creditInline["bin_code"] = 7026;
			break;		
		case "6789":
			$creditInline["bin_code"] = 7027;
			break;		
	}

	// Save it;
	$bookBo->addInline($creditInline);

	$invoicePayment = array();
	$invoicePayment["ipa_date"] = $inline["bin_transaction_date"];
//	$invoicePayment["ipa_type"] = "DA";
	$invoicePayment["ipa_book_inline_id"] = $inline["bin_id"]; 
	$invoicePayment["ipa_credit_book_inline_id"] = $creditInline["bin_id"];

	$invoicePaymentBo->save($invoicePayment);
}

$data["ok"] = "ok";
//$data["creditInline"] = $creditInline;
//$data["invoicePayment"] = $invoicePayment;

echo json_encode($data);
?>
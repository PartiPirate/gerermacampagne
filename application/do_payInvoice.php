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
require_once("engine/bo/BookInlineBo.php");
require_once("engine/bo/InvoicePaymentBo.php");
require_once("engine/bo/CampaignBo.php");
//require_once("engine/bo/DocumentBo.php");
//require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/UserBo.php");

$campaignId = $_REQUEST["campaignId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$bookBo = BookInlineBo::newInstance($connection);
$taskBo = TaskBo::newInstance($connection);
//$documentBo = DocumentBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);
$invoicePaymentBo = InvoicePaymentBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);

$data = array();

// If there is no campaign, there is a problem stop here
if (!$campaign) exit();

$invoice = $bookBo->getInlines($campaign, array("bin_id" => $_REQUEST["invoiceSelect"]));

// print_r($invoice);

// If there is more than one invoice, there is a problem stop here
if (count($invoice) != 1) exit();

$invoice = $invoice[$_REQUEST["invoiceSelect"]];

// If there is an IPA_ID, it's already paid
if ($invoice["payment"]["ipa_id"]) exit();

$invoicePayment = array();

if (isset($_REQUEST["inlineDate"]) && $_REQUEST["inlineDate"]) {
	$invoicePayment["ipa_date"] = $_REQUEST["inlineDate"];
}
else {
	$date = new DateTime();
	$invoicePayment["ipa_date"] = $date->format("Y-m-d");
}

// If it's not paid by the representative, give a credit inline 
if ($_REQUEST["payment_type"] != InvoicePaymentBo::$DA) {
	$creditInline = array();
	$creditInline["bin_amount"] = $invoice["bin_amount"];
	$creditInline["bin_book"] = $invoice["bin_book"];
	$creditInline["bin_column"] = "input";
	$creditInline["bin_type"] = "donation";
	$creditInline["bin_campaign_id"] = $campaign["cam_id"];
	$creditInline["bin_label"] = "Paiement de la facture &laquo; " . $invoice["bin_label"] . " &raquo;";
	$creditInline["bin_transaction_date"] = $invoicePayment["ipa_date"];
	
	// Save it;
	
	// Put into the payment information
	$invoicePayment["ipa_credit_book_inline_id"] = "toto";

	$data["creditInline"] = $creditInline;
}

$invoicePayment["ipa_book_inline_id"] = $invoice["bin_id"];
$invoicePayment["ipa_type"] = $_REQUEST["payment_type"];

$data["ok"] = "ok";
$data["invoice"] = $invoice;
$data["payment"] = $invoicePayment;

echo json_encode($data);
?>
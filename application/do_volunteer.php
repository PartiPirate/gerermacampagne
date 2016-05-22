<?php
function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

include_once("config/database.php");
include_once("config/mail.php");
require_once("engine/bo/AddressBo.php");
require_once("engine/bo/DocumentBo.php");
require_once("engine/bo/UserBo.php");

$connection = openConnection();
$addressBo = AddressBo::newInstance($connection);
$documentBo = DocumentBo::newInstance($connection);

$data = array();

if (!count($_REQUEST)) {
	$data["ko"] = "ko";
	echo json_encode($data);
	exit();
}

if ($_REQUEST["email"]) {
	exit();
}

$email = $_REQUEST["xxx"];
$confirmationMail = $_REQUEST["confirmationMail"];

if ($confirmationMail != $email) {
	$data["ko"] = "ko";
}
else {
	$address = array();
	$firstname = $_REQUEST["firstname"];
	$lastname = $_REQUEST["lastname"];

	$address["add_entity"] = $firstname . " " . $lastname;
	$address["add_line_1"] = $_REQUEST["line1"];
	$address["add_line_2"] = $_REQUEST["line2"];
	$address["add_zip_code"] = $_REQUEST["zipCode"];
	$address["add_city"] = $_REQUEST["city"];
	$address["add_company_name"] = "";
	$address["add_country_id"] = 1;

	$addressBo->addAddress($address);

	$data["address_id"] = $address["add_id"];

	$candidature = array();
	$candidature["can_bodyshot_id"] = null;

	$basePath = $_SERVER["SCRIPT_FILENAME"];
	$basePath = substr($basePath, 0, strrpos($basePath, "/") + 1);

	$documentPath = $config["document_directory"];
	if (!endsWith($documentPath, "/")) {
		$documentPath .= "/";
	}

	if (isset($_FILES["bodyshotFile"])) {
		$file = $_FILES["bodyshotFile"];

		if ($file["name"]) {
			$document = array();

			$data['files']["bodyshotFile"]['src'] = $file["name"];

			$filename = time() . rand(0, time());
			$computeFilename = UserBo::computePassword($filename);

			move_uploaded_file($file["tmp_name"], $basePath . $documentPath . $computeFilename);

			$document = array();
			$document["doc_task_id"] = null;
			$document["doc_campaign_id"] = null;
			$document["doc_name"] = $file["name"];
			$document["doc_size"] = $file["size"];
			$document["doc_mime_type"] = $file["type"];
			$document["doc_label"] = "bodyshot";
			$document["doc_path"] = $documentPath . $computeFilename;
			$documentBo->addDocument($document);

			$data['files']["bodyshotFile"]['id'] = $document["doc_id"];
			$candidature["can_bodyshot_id"] = $document["doc_id"];
		}
	}

	$positions = explode(",", $_REQUEST["candidateInput"]);
//	$positions = json_encode($positions);

	$circonscriptions = explode(",", $_REQUEST["circonscriptions"]);
//	$circonscriptions = json_encode($circonscriptions);

	$candidature["can_address_id"] = $address["add_id"];
	$candidature["can_sex"] = $_REQUEST["sexInput"];
	$candidature["can_firstname"] = $firstname;
	$candidature["can_lastname"] = $lastname;
	$candidature["can_telephone"] = $_REQUEST["telephone"];
	$candidature["can_mail"] = $email;
	$candidature["can_authorize"] = isset($_REQUEST["authorize"]) ? $_REQUEST["authorize"] : 0;

	$candidature["circonscriptions"] = $circonscriptions;
	$candidature["positions"] = $positions;

	$addressBo->addCandidature($candidature);

	$data["candidature_id"] = $candidature["can_id"];

	$data["ok"] = "ok";
}

echo json_encode($data);
?>
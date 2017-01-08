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
include_once("config/mail.php");
include_once("language/language.php");
require_once("engine/bo/AddressBo.php");
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/TelephoneBo.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$campaignId = $_REQUEST["campaignId"];
$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$addressBo = AddressBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);
$userBo = UserBo::newInstance($connection);
$telephoneBo = TelephoneBo::newInstance($connection);
$taskBo = TaskBo::newInstance($connection);

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);
$canAdd = false;

$chars = array();
for($index = 0; $index < 26; $index++) {
	if ($index < 10) {
		$chars[] = $index;
	}
	$chars[] = chr(65 + $index);
	$chars[] = chr(97 + $index);
}

$loggedUser = $userBo->get($userId);

$nbChars = count($chars);

if (!$campaign) {
}
else if ($campaign["uri_right"] == "candidate" || $campaign["uri_right"] == "listHead")
{
	$canAdd = true;
}

if ($canAdd) {
	$email = $_REQUEST["mail"];

	// First, verify if the target user exists
	$user = $userBo->getUserByMail($email);
	$userExists = ($user != null);

	$right = $_REQUEST["functionInput"];

	if (!$userExists) {
		// Next insert address
		$address = array();
		$address["add_entity"] = $_REQUEST["firstname"] . " " . $_REQUEST["lastname"];
		$address["add_line_1"] = $_REQUEST["line1"];
		$address["add_line_2"] = $_REQUEST["line2"];
		$address["add_zip_code"] = $_REQUEST["zipCode"];
		$address["add_city"] = $_REQUEST["city"];

		if (isset($_REQUEST["companyName"]) && $_REQUEST["companyName"]) {
			$address["add_company_name"] = $_REQUEST["companyName"];
		}
		else {
			$address["add_company_name"] = "";
		}

		$address["add_country_id"] = 1;
		$addressBo->addAddress($address);

		// Next insert user

		if (isset($_REQUEST["pseudo"]) && $_REQUEST["pseudo"]) {
			$login = $_REQUEST["pseudo"];
		}
		else {
			$login = "anonymous" . time();
		}

		$language = SessionUtils::getLanguage($_SESSION);

		$password = "";
		for($index = 0; $index < 32; $index++) {
			$password .= $chars[rand(0, $nbChars - 1)];
		}

		$hashedPassword = UserBo::computePassword($password);
		$activationKey = UserBo::computePassword($config["salt"] . time());

		$user = array();
		$user["use_id"] = $userBo->register($login, $email, $hashedPassword, $activationKey, $language);

		if ($right != "charteredAccountant") {
			// send mail
		}
		else {
			// Validate the task search_chartered_accountant
			$task = $taskBo->getTaskByLabel($campaign, "search_chartered_accountant");
			if ($task) {
				$task = array("tas_id" => $task["tas_id"], "tas_status" => "done");
				$taskBo->updateTask($task);
			}
		}

		$user["use_address_id"] = $address["add_id"];
		$userBo->update($user);
	}
	else {
		$login = $user["use_login"];
	}

	$telephoneBo->deleteUserPhones($user);

	if (isset($_REQUEST["telephone"])) {
		$telephone = array();
		$telephone["tel_type"] = TelephoneBo::TYPE_TELEPHONE;
		$telephone["tel_user_id"] = $user["use_id"];
		$telephone["tel_telephone"] = $_REQUEST["telephone"];

		$telephoneBo->save($telephone);
	}

	if (isset($_REQUEST["fax"])) {
		$telephone = array();
		$telephone["tel_type"] = TelephoneBo::TYPE_FAX;
		$telephone["tel_user_id"] = $user["use_id"];
		$telephone["tel_telephone"] = $_REQUEST["fax"];

		$telephoneBo->save($telephone);
	}

	// Finally give the user right

	$userBo->addRight($user["use_id"], $right, $campaignId);

	$mail = getMailInstance();

	$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addAddress($email);
		
	$listHead = $loggedUser["use_login"];
//		if (startsWith($listHead, "anonymous")) {
//			$listHead = $loggedUser["use_login"]
//		}

	if (!$userExists) {
		// Send new add user mail
		$url = $config["base_url"] . "activate.php?code=$activationKey&mail=" . urlencode($email);
		
		$mailMessage = lang("register_add_mail_content", false);
		$mailMessage = str_replace("{activationUrl}", $url, $mailMessage);
		$mailMessage = str_replace("{login}", $login, $mailMessage);
		$mailMessage = str_replace("{list_head}", $listHead, $mailMessage);
		$mailMessage = str_replace("{password}", $password, $mailMessage);
		
		$mailSubject = lang("register_add_mail_subject", false);
	}
	else {
		// send attachment user mail
		$applicationUrl = $config["base_url"] . "index.php";

		$mailMessage = lang("add_mail_content", false);
		$mailMessage = str_replace("{applicationUrl}", $applicationUrl, $mailMessage);
		$mailMessage = str_replace("{login}", $login, $mailMessage);
		$mailMessage = str_replace("{list_head}", $listHead, $mailMessage);

		$mailSubject = lang("add_mail_subject", false);
	}
	
	$mail->Subject = mb_encode_mimeheader(utf8_decode($mailSubject), "ISO-8859-1");
	$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
	$mail->AltBody = utf8_decode($mailMessage);

	$mail->send();

	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
	$data["message"] = "not_enough_right";
}

echo json_encode($data);
?>
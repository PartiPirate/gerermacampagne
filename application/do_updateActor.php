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
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$data = array();

$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$addressBo = AddressBo::newInstance($connection);
$userBo = UserBo::newInstance($connection);

$address = array();
$user = array();

if ($_REQUEST["addressId"]) {
	$address = $addressBo->getById($_REQUEST["addressId"]);
	
	if ($address["add_code"] != $_REQUEST["addressCode"]) {
		$data["ko"] = "ko";
	}
	else {
		unset($address["add_code"]);
	}
}

if ($_REQUEST["userId"]) {
	$user = $userBo->get($_REQUEST["userId"]);
	
	if ($user["use_code"] != $_REQUEST["userCode"]) {
		$data["ko"] = "ko";
	}
	else {
		unset($address["use_code"]);
	}
}

if (!isset($data["ko"])) {

	$address["add_entity"] = $_REQUEST["entity"];
	$address["add_line_1"] = $_REQUEST["line1"];
	$address["add_line_2"] = $_REQUEST["line2"];
	$address["add_zip_code"] = $_REQUEST["zipCode"];
	$address["add_city"] = $_REQUEST["city"];
	$address["add_country_id"] = $_REQUEST["countryId"];
	
	if (isset($_REQUEST["companyName"]) && $_REQUEST["companyName"]) {
		$address["add_company_name"] = $_REQUEST["companyName"];
	}
	else {
		$address["add_company_name"] = "";
	}
	
	if (!$address["add_country_id"]) {
		$address["add_country_id"] = 1;
	}

	$addressBo->save($address);

	$user = array("use_id" => $user["use_id"], "use_address_id" => $address["add_id"]);

	$userBo->update($user);
}

if (!isset($data["ko"])) {
	$data["ok"] = "ok";
}

echo json_encode($data);
?>
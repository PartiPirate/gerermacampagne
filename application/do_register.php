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
include_once("config/database.php");
include_once("config/mail.php");
include_once("language/language.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$userBo = UserBo::newInstance(openConnection());

$data = array();

if (isset($_REQUEST["mail"]) && $_REQUEST["mail"]) {
	$data["ok"] = "ok";
	$data["message"] = "ok";
	echo json_encode($data);
	exit();
}

if (isset($_REQUEST["cgv"]) && $_REQUEST["cgv"] != "okgirls") {
	$data["ok"] = "ok";
	$data["message"] = "ok";
	echo json_encode($data);
	exit();
}

$login = $_REQUEST["login"];
$email = $_REQUEST["xxx"];
$password = $_REQUEST["password"];
$confirmation = $_REQUEST["confirmation"];
$language = $_REQUEST["language"];

SessionUtils::setLanguage($language, $_SESSION);

if ($password != $confirmation) {
	$data["ko"] = "ko";
	$data["message"] = "error_passwords_not_equal";
	echo json_encode($data);
	exit();
}

$hashedPassword = UserBo::computePassword($password);
$activationKey = UserBo::computePassword($config["salt"] . time());
$url = $config["server"]["base"] . "activate.php?code=$activationKey&mail=" . urlencode($email);

$mail = getMailInstance();

$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addAddress($email);

$mailMessage = lang("register_mail_content", false);
$mailMessage = str_replace("{activationUrl}", $url, $mailMessage);
$mailMessage = str_replace("{login}", $login, $mailMessage);
$mailSubject = lang("register_mail_subject", false);

$mail->Subject = mb_encode_mimeheader(utf8_decode($mailSubject), "ISO-8859-1");
$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
$mail->AltBody = utf8_decode($mailMessage);

//$mail->SMTPDebug = 3;
//$mail->SMTPSecure = "tls";

if (!$mail->send()) {
	$data["ko"] = "ko";
	$data["message"] = "error_cant_send_mail";
	$data["mail"] = $mail->ErrorInfo;
	echo json_encode($data);
	exit();
}

if ($userBo->register($login, $email, $hashedPassword, $activationKey, $language)) {
	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
	$data["message"] = "error_cant_register";
}

echo json_encode($data);
?>

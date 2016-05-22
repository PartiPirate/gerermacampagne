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
//session_start();
include_once("config/database.php");
include_once("config/mail.php");
include_once("language/language.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");


$email = "contact@levieuxcedric.com";

$mail = getMailInstance();

$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addAddress($email);

$mail->Subject = mb_encode_mimeheader(utf8_decode("Bouh"), "ISO-8859-1");
$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode("Bah")));
$mail->AltBody = utf8_decode("Bah");

$mail->SMTPDebug = 3;
$mail->SMTPSecure = "ssl";

if (!$mail->send()) {
	$data["ko"] = "ko";
	$data["message"] = "error_cant_send_mail";
	$data["mail"] = $mail->ErrorInfo;
	echo json_encode($data);
	exit();
}

echo json_encode($data);

echo "\n";
?>

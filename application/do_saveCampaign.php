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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("config/database.php");
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$user = SessionUtils::getUser($_SESSION);
$userId = SessionUtils::getUserId($_SESSION);
$campaignBo = CampaignBo::newInstance(openConnection());
$data = array();

$campaign = array();

$campaign["cam_name"] = $_REQUEST["name"];
$campaign["cam_electoral_district"] = $_REQUEST["electoralDistrict"];
$campaign["cam_start_date"] = $_REQUEST["startDate"];
$campaign["cam_finish_date"] = $_REQUEST["finishDate"];
if (!$campaign["cam_finish_date"]) {
	$campaign["cam_finish_date"] = "0000-00-00";
}

$campaign["actors"] = array();
$campaign["actors"][] = array("uri_user_id" => $userId, "uri_right" => $_REQUEST["right"]);

$campaignBo->save($campaign);

$data["ok"] = "ok";

echo json_encode($data);
?>

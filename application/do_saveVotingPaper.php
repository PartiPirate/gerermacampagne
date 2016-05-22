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
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/VotingPaperBo.php");
require_once("engine/utils/SessionUtils.php");

$user = SessionUtils::getUser($_SESSION);

$userId = SessionUtils::getUserId($_SESSION);
$campaignId = $_REQUEST["campaignId"];

$campaignBo = CampaignBo::newInstance(openConnection());
$votingPaperBo = VotingPaperBo::newInstance(openConnection());
$data = array();

$campaign = $campaignBo->getUserCampaign($userId, $campaignId);

if (!$campaign) exit();

$votingPaper = array();
$votingPaper["vpa_id"] = $_REQUEST["votingPaperId"];
$votingPaper["vpa_campaign_id"] = $campaignId;
$votingPaper["vpa_format"] = $_REQUEST["paperFormat"];
$votingPaper["vpa_code"] = $_REQUEST["votingPaperCode"];

$votingPaperBo->save($votingPaper);

$data["ok"] = "ok";
$data["votingPaperId"] = $votingPaper["vpa_id"];

echo json_encode($data);
?>
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
include_once("language/language.php");
require_once("engine/bo/PoliticalPartyBo.php");
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/utils/IcsFormatter.php");

$user = SessionUtils::getUser($_SESSION);
$userId = SessionUtils::getUserId($_SESSION);
$language = SessionUtils::getLanguage($_SESSION);

$connection = openConnection();

$ppBo = PoliticalPartyBo::newInstance($connection);
$campaignBo = CampaignBo::newInstance($connection);
$taskBo = TaskBo::newInstance($connection);

$administratedParties = array();
$campaign = null;
$userCampaigns = array();

$entries = array();
$timestamp = new DateTime();

$eveAlarm = new Alarm();
//$eveAlarm->repeat = 3;
//$eveAlarm->duration = Alarm::EVERY_DAY;
$eveAlarm->trigger = Alarm::TRIGGER_EVE;
$eveAlarm->action = Alarm::ACTION_DISPLAY;

if ($userId) {
	$userCampaigns = $campaignBo->getCampaigns(array("userId" => $userId, "withRights" => true));

//	print_r($userCampaigns);

	foreach ($userCampaigns as $campaign) {
		if ($campaign["cam_id"] == $_REQUEST["campaignId"]) {

			$campaign["tasks"] = $taskBo->getTasks($campaign["cam_id"]);

//			print_r($campaign);

			foreach($campaign["tasks"] as $task) {
				if ($task["tas_limit_date"] && $task["tas_limit_date"] != "0000-00-00") {
					if ($_REQUEST["role"]) {
						$found = false;
						foreach($task["tas_righters"] as $righter) {
							if ($_REQUEST["role"] == $righter) {
								$found = true;
							}
							else if ($_REQUEST["role"] == "candidate" && "listHead" == $righter) {
								$found = true;
							}
						}

						if (!$found) {
							continue;
						}
					}
					$entry = new Event();
					$entry->startDate = new DateTime($task["tas_limit_date"] . "08:00:00");
					$entry->endDate = new DateTime($task["tas_limit_date"] . "20:00:00");
					$entry->summary = isLanguageKey($task["tas_label"]) ? lang($task["tas_label"], false) : $task["tas_label"];
					$entry->uid = "GMC_" . $campaign["cam_id"] . "_" . $task["tas_id"];
					$entry->timestamp = $timestamp;

					$entry->alarms[] = $eveAlarm;

					$entries[] = $entry;
				}
			}

			$entry = new Event();
			$entry->startDate = new DateTime($campaign["cam_start_date"] . "08:00:00");
			$entry->endDate = new DateTime($campaign["cam_start_date"] . "20:00:00");
			$entry->summary = "Premier tour";
			$entry->uid = "GMC_" . $campaign["cam_id"] . "_0";
			$entry->timestamp = $timestamp;

			$entry->alarms[] = $eveAlarm;

			$entries[] = $entry;

			break;
		}
	}
}

$icsFormatter = new IcsFormatter();
$icsFormatter->company = "Parti Pirate";
$icsFormatter->product = "GererMaCampagne";

$calendarName = "calendar.ics";

header('Content-Type: text/calendar');
header("Content-Transfer-Encoding: 8BIT");
header("Content-disposition: attachment; filename=\"".$calendarName."\"");

echo $icsFormatter->format($entries);

?>
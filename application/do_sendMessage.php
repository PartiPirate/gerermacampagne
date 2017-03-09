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
require_once("engine/bo/CampaignBo.php");
require_once("engine/bo/MessageBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/UserBo.php");

$userId = SessionUtils::getUserId($_SESSION);

$connection = openConnection();

$campaignBo = CampaignBo::newInstance($connection);
$messageBo = MessageBo::newInstance($connection, $config);
$taskBo = TaskBo::newInstance($connection);

$fromId = $_REQUEST["fromId"];
$fromType = $_REQUEST["fromType"];

// if party, is the user administrator
if ($fromType == "party") {
    
}

// if cadidate, is the user candidate
if ($fromType == "candidate") {
    
}

// if representative, is the user representative
if ($fromType == "representative") {
    
}

$tos = array();

if ($_REQUEST["toId"]) {
    $to = array("id" => $_REQUEST["toId"], "type" => $_REQUEST["toType"]);
    $tos[] = $to;
}
else if (isset($_REQUEST["toIds"]) && $_REQUEST["toIds"]) {
    foreach($_REQUEST["toIds"] as $toId) {
        $toId = explode("-", $toId);
        $tos[] = array("id" => $toId[1], "type" => $toId[0]);
    }
}
else if ($_REQUEST["toTaskStatus"]) {
    // Get all the task from a party id
    $campaigns = $campaignBo->getCampaigns(array("partyId" => $_REQUEST["fromId"], "withRights" => true));
    foreach($campaigns as $campaign) {
        $to = array("id" => $campaign["cam_id"], "type" => $_REQUEST["toType"]);
        
        $found = false;
        
        if ($_REQUEST["toTaskStatus"] == "status-all") {
            $found = true;
        }
        else {
        	$tasks = $taskBo->getTasks($campaign["cam_id"]);
//            $to["tasks"] = $tasks;

            $triggerStatus = ($_REQUEST["toTaskStatus"] == "status-done" ? "done" : "inProgress");

            foreach($tasks as $task) {
//                echo $task["tas_status"] . " vs " . $triggerStatus . "\n";
                if ($task["tas_status"] != $triggerStatus) continue;
                $hasTask = false;
                foreach($_REQUEST["toTasks"] as $toTaskLabel) {
//                    echo "\t" . $task["tas_label"] . " vs " . $toTaskLabel . "\n";
                    if ($task["tas_label"] == $toTaskLabel) {
                        $hasTask = true;
//                        echo "\t\t hasTask" . "\n";
                        break;
                    }
                }

                if ($hasTask) {
                    $found = true;
//                    echo "\t\t found" . "\n";
                    break;
                }
            }
        }

        if ($found) {
            $tos[$to["id"]] = $to;
        }
    }    
}

$numberOfMessages = 0;
$messages = array();

foreach($tos as $to) {
    $message = array();
    $message["mes_from_id"] = $fromId;
    $message["mes_from_type"] = $fromType;
    $message["mes_to_id"] = $to["id"];
    $message["mes_to_type"] = $to["type"];

//    $message["to"] = $to;

    $message["mes_subject"] = $_REQUEST["subject"];
    $message["mes_message"] = $_REQUEST["message"];
    
    $messageBo->save($message);
  
    // Send mail ?
    
//    $messages[] = $message;
    
    $numberOfMessages++;
}

$data["ok"] = "ok";
$data["numberOfMessages"] = $numberOfMessages;
$data["messages"] = $messages;

echo json_encode($data);
?>
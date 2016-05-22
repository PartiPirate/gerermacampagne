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
require_once("engine/bo/DocumentBo.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TaskBo.php");
require_once("engine/bo/UserBo.php");

$taskId = $_REQUEST["taskId"];
$campaignId = $_REQUEST["campaignId"];

$connection = openConnection();

$taskBo = TaskBo::newInstance($connection);
$documentBo = DocumentBo::newInstance($connection);

$task = $taskBo->getTask($taskId, $campaignId);

if (!$task) {
	exit();
}

$data = array();

// print_r($_SERVER);
// print_r($_REQUEST);
// print_r($_FILES);

$basePath = $_SERVER["SCRIPT_FILENAME"];
$basePath = substr($basePath, 0, strrpos($basePath, "/") + 1);

$documentPath = $config["document_directory"];
if (!endsWith($documentPath, "/")) {
	$documentPath .= "/";
}

if (count($_FILES)) {
	foreach($_FILES as $key => $file) {
		$index = str_replace("documentInput-", "", $key);

		$data['files'][$key]['src'] = $file["name"];

		$filename = time() . rand(0, time());
		$computeFilename = UserBo::computePassword($filename);

//		$data['files'][$key]['dest'] = $storePath . $computeFilename;
		move_uploaded_file($file["tmp_name"], $basePath . $documentPath . $computeFilename);

		$document = array();
		$document["doc_task_id"] = $taskId;
		$document["doc_political_party_id"] = null;
		$document["doc_campaign_id"] = $campaignId;
		$document["doc_name"] = $file["name"];
		$document["doc_size"] = $file["size"];
		$document["doc_mime_type"] = $file["type"];
		$document["doc_label"] = $task["tas_documents"][$index]["label"];
		$document["doc_path"] = $documentPath . $computeFilename;

		$documentBo->addDocument($document);
//		print_r($document);
	}
}

//print_r($task);

if (count($task["tas_implies"])) {
	foreach($task["tas_implies"] as $imply) {
		$taskBo->duplicateTaskModel(array("cam_id" => $campaignId), $imply);
	}
}

$task = array("tas_id" => $taskId, "tas_status" => "done");
$taskBo->updateTask($task);

$data["ok"] = "ok";

echo json_encode($data);
?>
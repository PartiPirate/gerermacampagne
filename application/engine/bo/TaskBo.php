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

class TaskBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new TaskBo($pdo);
	}

	function insertTask(&$task) {
		$query = "	INSERT INTO tasks
						(tas_campaign_id, tas_label, tas_documents, tas_status, tas_form,
							tas_dependencies, tas_implies, tas_righters, tas_limit_date, tas_order)
					VALUES
						(:tas_campaign_id, :tas_label, :tas_documents, :tas_status, :tas_form,
							:tas_dependencies, :tas_implies, :tas_righters, :tas_limit_date, :tas_order) ";

		$statement = $this->pdo->prepare($query);

// 		print_r($task);
// 		echo showQuery($query, $task);

		try {
			$statement->execute($task);
			$task["tas_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function updateTask($task) {
		$query = "	UPDATE tasks SET ";

		$querySeparator = "";
		if (isset($task["tas_status"])) {
			$query .= $querySeparator;
			$query .= "	tas_status = :tas_status ";
			$querySeparator = ", ";
		}

		if (isset($task["tas_dependencies"])) {
			$query .= $querySeparator;
			$query .= "	tas_dependencies = :tas_dependencies ";
			$querySeparator = ", ";
		}

		$query .= "	WHERE
						tas_id = :tas_id ";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($task);

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function getTask($taskId, $campaignId) {
		$tasks = $this->getTasks($campaignId, $taskId);

		if (count($tasks)) {
			return $tasks[0];
		}

		return null;
	}

	function getTaskByLabel($campaign, $taskLabel) {
		$tasks = $this->getTasks($campaign["cam_id"], null, $taskLabel);

		if (count($tasks)) {
			return $tasks[0];
		}

		return null;
	}

	function hasTask($campaign, $taskLabel) {
		return $this->getTaskByLabel($campaign, $taskLabel) != null;
	}

	function getTasks($campaignId, $taskId = null, $taskLabel = null) {
		$args = array("tas_campaign_id" => $campaignId);
		$query = "	SELECT *
					FROM tasks
					WHERE
						tas_campaign_id = :tas_campaign_id ";

		if ($taskId) {
			$query .= "	AND tas_id = :tas_id ";
			$args["tas_id"] = $taskId;
		}

		if ($taskLabel) {
			$query .= "	AND tas_label = :tas_label ";
			$args["tas_label"] = $taskLabel;
		}

		$query .= "	ORDER BY tas_order ";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args) . "\n<br/>";

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			foreach($results as $key => $result) {

				$results[$key] = TaskBo::unmarshall($results[$key]);
			}

			return $results;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function duplicateTaskModel($campaign, $taskLabel) {
		if ($this->hasTask($campaign, $taskLabel)) return;

		$query = "	SELECT *
					FROM task_models
					WHERE tmo_label = :tmo_label ";
		$args = array("tmo_label" => $taskLabel);

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (!count($results)) return false;

			$model = $results[0];
			$model["tmo_dependencies"] = json_decode($model["tmo_dependencies"], true);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		$task = array();
		$task["tas_righters"] = $model["tmo_righters"];
		$task["tas_documents"] = $model["tmo_documents"];
		$task["tas_implies"] = $model["tmo_implies"];
		$task["tas_label"] = $model["tmo_label"];
		$task["tas_form"] = $model["tmo_form"];
		$task["tas_order"] = $model["tmo_order"];

		// calcul de la date limite
		if ($model["tmo_computation_date"] != "{}") {
			$model["tmo_computation_date"] = json_decode($model["tmo_computation_date"], true);

			$date = new DateTime($campaign[$model["tmo_computation_date"]["from"]]);
			$interval = new DateInterval($model["tmo_computation_date"]["interval"]);

			if ($model["tmo_computation_date"]["direction"] == "add") {
				$date = $date->add($interval);
			}
			else {
				$date = $date->sub($interval);
			}

			$task["tas_limit_date"] = $date->format("Y-m-d");
		}
		else {
			$task["tas_limit_date"] = "0000-00-00";
		}

		$task["tas_campaign_id"] = $campaign["cam_id"];
		$task["tas_status"] = "inProgress";

		$task["tas_dependencies"] = array();
		foreach($model["tmo_dependencies"] as $dependency) {
			$dependantTask = $this->getTaskByLabel($campaign, $dependency);
			if (!$dependantTask) continue;

			$task["tas_dependencies"][] = "" . $dependantTask["tas_id"];
		}

		$task["tas_dependencies"] = json_encode($task["tas_dependencies"]);

		$this->insertTask($task);

		$query = "	SELECT *
					FROM task_models
					WHERE
						tmo_dependencies LIKE :like_tmo_dependencies ";

		$args = array("like_tmo_dependencies" => "%\"" . $taskLabel . "\"%");

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			foreach($results as $line) {
				$dependantTaskLabel = $line["tmo_label"];
				$dependantTask = $this->getTaskByLabel($campaign, $dependantTaskLabel);

				if (!$dependantTask) continue;

				$dependantTask["tas_dependencies"][] = "" . $task["tas_id"];

				$toUpdateTask = array();
				$toUpdateTask["tas_id"] = $dependantTask["tas_id"];
				$toUpdateTask["tas_dependencies"] = json_encode($dependantTask["tas_dependencies"]);

				$this->updateTask($toUpdateTask);
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		return true;
	}

	function startCampaign($campaign) {
		if ($this->hasTask($campaign, "deposit_representative_declaration")) return;

		$this->duplicateTaskModel($campaign, "deposit_representative_declaration");
		$this->duplicateTaskModel($campaign, "open_bank_account");
		$this->duplicateTaskModel($campaign, "ask_electoral_list_registration");
		$this->duplicateTaskModel($campaign, "receive_electoral_list_registration");
		$this->duplicateTaskModel($campaign, "deposit_candidature");
		$this->duplicateTaskModel($campaign, "ask_campaign_accounts_form");
		$this->duplicateTaskModel($campaign, "want_voting_paper");
		$this->duplicateTaskModel($campaign, "want_posters");
		$this->duplicateTaskModel($campaign, "want_circular");
		$this->duplicateTaskModel($campaign, "receive_campaign_accounts_form");
		$this->duplicateTaskModel($campaign, "close_bank_account");
		$this->duplicateTaskModel($campaign, "fill_campaign_accounts_form");
		$this->duplicateTaskModel($campaign, "deposit_campaign_account");
	}

	static function unmarshall($task) {
		$task["tas_dependencies"] = json_decode($task["tas_dependencies"], true);
		$task["tas_righters"] = json_decode($task["tas_righters"], true);
		$task["tas_documents"] = json_decode($task["tas_documents"], true);
		$task["tas_implies"] = json_decode($task["tas_implies"], true);

		foreach($task as $key => $value) {
			if (is_numeric($key))  {
				unset($task[$key]);
			}
		}

		return $task;
	}

	static function getStyle($task, $date) {

		switch($task["tas_status"]) {
			case "inProgress" :
				if ($task["tas_limit_date"] != "0000-00-00") {
					$limit = new DateTime($task["tas_limit_date"]);
					$diff = $date->diff($limit);
					$diff = $diff->format("%r%a");

					if ($diff < 7) {
						$style = "danger";
					}
					else if ($diff < 14) {
						$style = "warning";
					}
					else {
						$style = "primary";
					}
				}
				else {
					$style = "primary";
				}
				break;
			case "done" :
				$style = "success";
				break;
		}

		return $style;
	}

	static function hasPossibilityToDoTask($task, $taskMap) {
		if (!count($task["tas_dependencies"])) return true;

		foreach($task["tas_dependencies"] as $dependencyTaskId) {
			$dependencyTask = $taskMap[$dependencyTaskId];

			if ($dependencyTask["tas_status"] != "done") return false;
		}

		return true;
	}

	static function hasRightToDoTask($userId, $campaign, $task) {
		$right = null;

		$numberOfCandidates = 0;
		$numberOfListHeads = 0;

		foreach($campaign["actors"] as $actor) {
			if ($actor["use_id"] == $userId) {
				$right = $actor["uri_right"];
				break;
			}
			switch($actor["uri_right"]) {
				case "listHead" :
					$numberOfListHeads++;
					break;
				case "candidate" :
					$numberOfCandidates++;
					break;
			}
		}

		if (!$right) return false;

		foreach($task["tas_righters"] as $righter) {
			if ($righter == $right) return true;
			if ($righter == "listHead" && $right == "candidate" && $numberOfListHeads == 0) return true;
		}

		return false;
	}

	function computeAfterOrder($campaignId, $afterTaskId) {
		$afterTask = $this->getTask($afterTaskId, $campaignId);

		$afterOrder = $afterTask["tas_order"] + 1;

		$args = array("after_order" => $afterOrder, "campaign_id" => $campaignId);

		// Add +1 of all orders after the added one
		$query = "	UPDATE tasks
					SET tas_order = tas_order + 1
					WHERE tas_order >= :after_order
					AND tas_campaign_id = :campaign_id";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		$statement->execute($args);

		return $afterOrder;
	}

	function computeToEndOrder($campaignId) {
		$tasks = $this->getTasks($campaignId);

		$order = 0;
		foreach($tasks as $task) {
			if ($task["tas_order"] > $order) {
				$order = $task["tas_order"];
			}
		}

		return $order + 1;
	}

}

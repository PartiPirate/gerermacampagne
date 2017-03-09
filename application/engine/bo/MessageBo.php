<?php /*
	Copyright 2015-2017 Cédric Levieux, Parti Pirate

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
    along with GererMaCampagne.  If age, see <http://www.gnu.org/licenses/>.
*/

class MessageBo {
	var $pdo = null;
	var $config = null;
	var $galetteDatabase = "";
	var $personaeDatabase = "";

	var $TABLE = "messages";
	var $ID_FIELD = "mes_id";

	static $USER = "user";
	static $PARTY = "user";
	static $CAMPAIGN = "campaign";
	static $CANDIDATE = "candidate";
	static $REPRESENTATIVE = "representative";

	function __construct($pdo, $config) {
		$this->pdo = $pdo;
		$this->config = $config;
	}

	static function newInstance($pdo, $config) {
		return new MessageBo($pdo, $config);
	}

	function create(&$message) {
		$query = "	INSERT INTO $this->TABLE () VALUES ()	";

		$statement = $this->pdo->prepare($query);
//				echo showQuery($query, $args);

		try {
			$statement->execute();
			$message[$this->ID_FIELD] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($message) {
		$query = "	UPDATE $this->TABLE SET ";

		$separator = "";
		foreach($message as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE $this->ID_FIELD = :$this->ID_FIELD ";

//		echo showQuery($query, $message);

		$statement = $this->pdo->prepare($query);
		$statement->execute($message);
	}

	function save(&$message) {
 		if (!isset($message[$this->ID_FIELD]) || !$message[$this->ID_FIELD]) {
			$this->create($message);
		}

		$this->update($message);
	}

	function getById($id) {
		$filters = array($this->ID_FIELD => intval($id));

		$results = $this->getByFilters($filters);

		if (count($results)) {
			return $results[0];
		}

		return null;
	}

	function getByFilters($filters = null) {
		if (!$filters) $filters = array();
		$args = array();

		$query = "	SELECT $this->TABLE.*
						, from_ppa.ppa_name as from_ppa_name
						, from_campaign.cam_name as from_cam_name
						, from_user.use_login as from_use_login
						, to_ppa.ppa_name as to_ppa_name
						, to_campaign.cam_name as to_cam_name
						, to_user.use_login as to_use_login
					FROM $this->TABLE
					
					LEFT JOIN political_parties from_ppa ON from_ppa.ppa_id = mes_from_id
					LEFT JOIN campaigns from_campaign ON from_campaign.cam_id = mes_from_id
					LEFT JOIN users from_user ON from_user.use_id = mes_from_id
					LEFT JOIN political_parties to_ppa ON to_ppa.ppa_id = mes_to_id
					LEFT JOIN campaigns to_campaign ON to_campaign.cam_id = mes_to_id
					LEFT JOIN users to_user ON to_user.use_id = mes_to_id

					WHERE
						1 = 1 \n";

		if (isset($filters[$this->ID_FIELD])) {
			$args[$this->ID_FIELD] = $filters[$this->ID_FIELD];
			$query .= " AND $this->ID_FIELD = :$this->ID_FIELD \n";
		}

		if(isset($filters["froms"]) || isset($filters["tos"])) {
			$query .= " AND (";
			$separator = "";

			if(isset($filters["froms"])) {
				foreach($filters["froms"] as $from) {
					$query .= "$separator(";

					$query .= " mes_from_id = " . $from["id"];
					$query .= " AND mes_from_type = '" . $from["type"] . "'";

					$query .= " AND mes_status NOT IN ('deleted_both', 'deleted_from') ";

					$query .= ")";
					$separator = " OR ";
				}			
			}

			if(isset($filters["tos"])) {
				foreach($filters["tos"] as $to) {
					$query .= "$separator(";

					$query .= " mes_to_id = " . $to["id"];
					$query .= " AND mes_to_type = '" . $to["type"] . "'";

					$query .= " AND mes_status NOT IN ('deleted_both', 'deleted_to') ";

					$query .= ")";
					$separator = " OR ";
				}			
			}
			$query .= ")";
		}
		else {
				
		}

//		$query .= "	ORDER BY cte_parent_id ASC , cte_order ASC ";

		$query .= "	ORDER BY mes_id DESC  ";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args);

		$results = array();

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			foreach($results as $index => $line) {
				foreach($line as $field => $value) {
					if (is_numeric($field)) {
						unset($results[$index][$field]);
					}
				}

				switch($results[$index]["mes_from_type"]) {
					case "party":
						$results[$index]["mes_from_label"] = $results[$index]["from_ppa_name"];
						break;
					case "user":
						$results[$index]["mes_from_label"] = $results[$index]["from_use_login"];
						break;
					case "candidate":
						$results[$index]["mes_from_label"] = $results[$index]["from_cam_name"];
						$results[$index]["mes_from_label"] = "candidate of " . $results[$index]["mes_from_label"];
						break;
					case "representative":
						$results[$index]["mes_from_label"] = $results[$index]["from_cam_name"];
						$results[$index]["mes_from_label"] = "representative of " . $results[$index]["mes_from_label"];
						break;
					case "campaign":
						$results[$index]["mes_from_label"] = $results[$index]["from_cam_name"];
						break;
					default:
						break;
						$results[$index]["mes_from_label"] = $results[$index]["from_cam_name"];
				}

				switch($results[$index]["mes_to_type"]) {
					case "party":
						$results[$index]["mes_to_label"] = $results[$index]["to_ppa_name"];
						break;
					case "user":
						$results[$index]["mes_to_label"] = $results[$index]["to_use_login"];
						break;
					case "candidate":
						$results[$index]["mes_to_label"] = $results[$index]["to_cam_name"];
						$results[$index]["mes_to_label"] = "candidate of " . $results[$index]["mes_to_label"];
						break;
					case "representative":
						$results[$index]["mes_to_label"] = $results[$index]["to_cam_name"];
						$results[$index]["mes_to_label"] = "representative of " . $results[$index]["mes_to_label"];
						break;
					case "campaign":
						$results[$index]["mes_to_label"] = $results[$index]["to_cam_name"];
						break;
					default:
						break;
						$results[$index]["mes_to_label"] = $results[$index]["to_cam_name"];
				}

				$results[$index]["mes_code"] = md5($line["mes_id"] . "-" . $this->config["salt"]);
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $results;
	}
}
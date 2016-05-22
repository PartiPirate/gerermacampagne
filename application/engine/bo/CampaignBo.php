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

class CampaignBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new CampaignBo($pdo);
	}

	function create(&$campaign) {
		$query = "	INSERT INTO campaigns
					(	cam_id)
					VALUES
					(	null) ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute(array());
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		$campaign["cam_id"] = $this->pdo->lastInsertId();

		return $campaign["cam_id"];
	}

	function update(&$campaign) {
		$query = "	UPDATE campaigns
					SET ";

		$separator = "";

		if (isset($campaign["cam_webdav"])) {
			$query .= $separator;
			$query .= " 	cam_webdav = :cam_webdav";
			$separator = ", ";
		}

		if (isset($campaign["cam_political_party_id"])) {
			$query .= $separator;
			$query .= " 	cam_political_party_id = :cam_political_party_id";
			$separator = ", ";
		}

		if (isset($campaign["cam_political_party_date"])) {
			$query .= $separator;
			$query .= " 	cam_political_party_date = :cam_political_party_date";
			$separator = ", ";
		}

		$query .= " WHERE
						cam_id = :cam_id ";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $campaign);

		try {
			$statement->execute($campaign);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		return true;
	}

	function save(&$campaign) {
		// if ppa_id = 0 create first
		if (!isset($campaign["cam_id"]) || $campaign["cam_id"] == 0) {
			$this->create($campaign);
			$campaign["cam_webdav"] = UserBo::computePassword($campaign["cam_id"]);
		}
		// then update

		if (isset($campaign["actors"])) {
			$actors = $campaign["actors"];
			unset($campaign["actors"]);
		}

		$query = "	UPDATE campaigns
					SET ";

		$separator = "";
		foreach($campaign as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :" . $field;
			$separator = ", ";
		}
		$query .= " WHERE
						cam_id = :cam_id ";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $campaign);

		try {
			$statement->execute($campaign);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		if (isset($actors)) {
			$query = "	DELETE FROM user_rights
						WHERE uri_right IN ('listHead', 'candidate', 'substitute', 'representative')
						AND uri_target_id = :uri_target_id ";
			$rights = array("uri_target_id" => $campaign["cam_id"]);

			$statement = $this->pdo->prepare($query);
	//		echo showQuery($query, $rights);

			try {
				$statement->execute($rights);
			}
			catch(Exception $e){
				echo 'Erreur de requète : ', $e->getMessage();
				return false;
			}

			$query = "	INSERT INTO user_rights
							(uri_user_id, uri_right, uri_target_id)
						VALUES
							(:uri_user_id, :uri_right, :uri_target_id) ";
			$statement = $this->pdo->prepare($query);

			foreach($actors as $actor) {
				$args = $actor;
				$args["uri_target_id"] = $campaign["cam_id"];
	//			echo showQuery($query, $args);

				try {
					$statement->execute($args);
				}
				catch(Exception $e){
					echo 'Erreur de requète : ', $e->getMessage();
					return false;
				}
			}
		}

		return $campaign["cam_id"];
	}

	function getCurrentCampaign($userId) {
		$campaigns = $this->getCampaigns(array("userId" => $userId, "withRights" => true));

		if (count($campaigns)) {
			return $campaigns[0];
		}

		return null;
	}

	function getUserCampaign($userId, $campaignId) {
		$campaigns = $this->getCampaigns(array("userId" => $userId, "campaignId" => $campaignId, "withRights" => true));

		if (count($campaigns)) {

			foreach($campaigns as $campaign) {
				if ($campaign["ucc_id"]) {
					return $campaign;
				}
			}

			return $campaigns[0];
		}

		return null;
	}

	function getCampaigns($filters = null) {
		$campaigns = array();
		$args = array();
		$query = "	SELECT DISTINCT campaigns.*, political_parties.*, user_current_campaigns.*";

		if ($filters && isset($filters["withRights"]) && $filters["withRights"]) {
			$query .= "	,user_rights.*";
		}

		$query .= "	FROM user_rights
					RIGHT JOIN campaigns ON uri_target_id = cam_id
							AND uri_right IN ('listHead', 'candidate', 'substitute', 'representative')
					LEFT JOIN political_parties ON ppa_id = cam_political_party_id
					LEFT JOIN user_current_campaigns ON ucc_user_id = uri_user_id AND ucc_campaign_id = cam_id
					WHERE
						1 = 1 ";

		if ($filters && isset($filters["userId"])) {
			$query .= "	AND uri_user_id = :uri_user_id	";
			$args["uri_user_id"] = $filters["userId"];
		}

		if ($filters && isset($filters["partyId"])) {
			$query .= "	AND cam_political_party_id = :cam_political_party_id	";
			$args["cam_political_party_id"] = $filters["partyId"];
		}

		if ($filters && isset($filters["campaignId"])) {
			$query .= "	AND cam_id = :cam_id	";
			$args["cam_id"] = $filters["campaignId"];
		}

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args) . "<br/>\n";

		try {
			$statement->execute($args);
			$campaigns = $statement->fetchAll();

// 			print_r($campaigns);
// 			echo "<br/>\n";
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $campaigns;
	}

	function getRighters($targetId, $rights) {
		if (!is_array($rights)) {
			$rights = array($rights);
		}
		$rights = "'" . implode("', '", $rights) . "'";

		$query = "	SELECT *, 	CASE uri_right
									WHEN 'listHead' THEN 0
									WHEN 'candidate' THEN 1
									WHEN 'substitute' THEN 2
									WHEN 'representative' THEN 3
									WHEN 'charteredAccountant' THEN 4
								END
										AS uri_order
					FROM user_rights
					JOIN users ON uri_user_id = use_id
					LEFT JOIN addresses ON add_id = use_address_id
					WHERE
						uri_target_id = :uri_target_id AND uri_right IN ($rights)
					ORDER BY uri_order, uri_user_id ";
		$statement = $this->pdo->prepare($query);
		$args = array("uri_target_id" => $targetId);//, "uri_right" => $rights);
//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			return $statement->fetchAll();
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function acceptAffiliation($affiliationId, $administratedParties) {
		if (!count($administratedParties)) return;

		$query = "	UPDATE campaigns
					SET cam_political_party_date = NOW()
					WHERE cam_id = :cam_id
					AND ";

		$whereQuery = "	cam_political_party_id IN (";
		$separator = "";

		foreach($administratedParties as $party) {
			$whereQuery .= $separator;
			$whereQuery .= $party["ppa_id"];
			$separator = ", ";
		}

		$whereQuery .= ")";
		$query .= $whereQuery;

		$args = array("cam_id" => $affiliationId);

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}
	}

	function refuseAffiliation($affiliationId, $administratedParties) {
		if (!count($administratedParties)) return;

		$query = "	UPDATE campaigns
					SET cam_political_party_id = null
					WHERE cam_id = :cam_id
					AND ";

		$whereQuery = "	cam_political_party_id IN (";
		$separator = "";

		foreach($administratedParties as $party) {
			$whereQuery .= $separator;
			$whereQuery .= $party["ppa_id"];
			$separator = ", ";
		}

		$whereQuery .= ")";
		$query .= $whereQuery;

		$args = array("cam_id" => $affiliationId);

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}
	}
}

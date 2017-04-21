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

class PoliticalPartyBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new PoliticalPartyBo($pdo);
	}

	function create(&$party) {
		$query = "	INSERT INTO addresses
					(	add_id)
					VALUES
					(	null) ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute();
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		$addressId = $this->pdo->lastInsertId();

		$query = "	INSERT INTO political_parties
					(	ppa_id, ppa_address_id)
					VALUES
					(	null, 	:ppa_address_id) ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute(array("ppa_address_id" => $addressId));
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		$party["ppa_id"] = $this->pdo->lastInsertId();

		return $party["ppa_id"];
	}

	function save(&$party) {
		// if ppa_id = 0 create first
		if (!isset($party["ppa_id"]) || $party["ppa_id"] == 0) {
			$this->create($party);
		}
		// then update

		$administrators = $party["administrators"];
		unset($party["administrators"]);

		$query = "	UPDATE political_parties
					SET
						ppa_name = :ppa_name
					WHERE
						ppa_id = :ppa_id ";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $party);

		try {
			$statement->execute($party);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
			return false;
		}

		$query = "	DELETE FROM user_rights
					WHERE uri_right = 'partyAdmin' AND uri_target_id = :uri_target_id ";
		$rights = array("uri_target_id" => $party["ppa_id"]);

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
						(:uri_user_id, 'partyAdmin', :uri_target_id) ";
		$statement = $this->pdo->prepare($query);

		foreach($administrators as $administrator) {
			$args = array("uri_user_id" => $administrator["use_id"], "uri_target_id" => $party["ppa_id"]);
//			echo showQuery($query, $args);

			try {
				$statement->execute($args);
			}
			catch(Exception $e){
				echo 'Erreur de requète : ', $e->getMessage();
				return false;
			}
		}

		return $party["ppa_id"];
	}

	function getWaitingAffiliations($parties) {
		if (!count($parties)) return array();

		$args = array();
		$query = "	SELECT
						*, cam_id as aff_id";

		$query .= ",(
						SELECT
							group_concat(use_login ORDER BY uri_right DESC SEPARATOR ', ')
						FROM users
						JOIN user_rights ON use_id = uri_user_id AND uri_right IN ('candidate', 'listHead')
						WHERE uri_target_id = cam_id
						) as aff_candidates ";


		$query .= "		FROM campaigns
						JOIN political_parties ON cam_political_party_id = ppa_id
						LEFT JOIN campaign_templates ON cte_id = cam_campaign_template_id
						WHERE
							 cam_political_party_date = '0000-00-00' ";


		$wherePartyQuery = " AND ppa_id IN (";
		$separator = "";
		foreach($parties as $party) {
			$wherePartyQuery .= $separator;
			$wherePartyQuery .= $party["ppa_id"];
			$separator = ", ";
		}
		$wherePartyQuery .= ")";

		$query .= $wherePartyQuery;

		$query .= " ORDER BY ppa_id";

		$statement = $this->pdo->prepare($query);
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

	function getParties($filters = null) {
		if (!$filters) $filters = array();
		$args = array();
		
		$query = "	SELECT political_parties.*";
		
		$query .= "	FROM political_parties
					WHERE
						1 = 1 ";


		$statement = $this->pdo->prepare($query);
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

	function getAdministratedParties($userId = null) {
		$args = array();
		$query = "	SELECT user_rights.*, political_parties.*";

		$query .= ",(
						SELECT
						count(cam_id)
						FROM campaigns
						WHERE cam_political_party_id = ppa_id
					) as ppa_number_of_campaigns ";

		$query .= ",(
						SELECT
						count(cam_id)
						FROM campaigns
						WHERE cam_political_party_id = ppa_id AND cam_political_party_date = '0000-00-00'
					) as ppa_number_of_waiting_affiliations ";

		$query .= "	FROM user_rights
					JOIN political_parties ON uri_target_id = ppa_id AND uri_right = 'partyAdmin'
					WHERE
						1 = 1 ";

		if ($userId) {
			$args["uri_user_id"] = $userId;
			$query .= " AND	uri_user_id = :uri_user_id";
		}

		$statement = $this->pdo->prepare($query);
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

	function getAdministrators($partyId) {
		return $this->getRighters($partyId, "partyAdmin");
	}

	function getRighters($targetId, $right) {
		$query = "	SELECT *
					FROM user_rights
					JOIN users ON uri_user_id = use_id
					WHERE
						uri_target_id = :uri_target_id AND uri_right = :uri_right ";
		$statement = $this->pdo->prepare($query);
		$args = array("uri_target_id" => $targetId, "uri_right" => $right);
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
}

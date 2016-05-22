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

class AddressBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new AddressBo($pdo);
	}

	function addCandidature(&$candidature) {
		$positions = $candidature["positions"];
		unset($candidature["positions"]);
		$circonscriptions = $candidature["circonscriptions"];
		unset($candidature["circonscriptions"]);

		$query = "	INSERT INTO candidatures
						(can_address_id, can_firstname, can_lastname, can_sex,
						can_mail, can_telephone, can_authorize,
						can_bodyshot_id)
					VALUES
						(:can_address_id, :can_firstname, :can_lastname, :can_sex,
						:can_mail, :can_telephone, :can_authorize,
						:can_bodyshot_id)	";

		$deletePositionsQuery = " DELETE FROM candidature_positions WHERE cpo_candidature_id = :cpo_candidature_id ";
		$deleteCirconscriptionsQuery = " DELETE FROM candidature_circonscriptions WHERE cci_candidature_id = :cci_candidature_id ";

		$insertPositionsQuery = " 	INSERT INTO candidature_positions
										(cpo_candidature_id, cpo_position)
									VALUES
										(:cpo_candidature_id, :cpo_position)";

		$insertCirconscriptionsQuery = " 	INSERT INTO candidature_circonscriptions
												(cci_candidature_id, cci_circonscription)
											VALUES
												(:cci_candidature_id, :cci_circonscription)";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $candidature);

		try {
			$statement->execute($candidature);

			$candidature["can_id"] = $this->pdo->lastInsertId();
			$positionDeleter = array("cpo_candidature_id" => $candidature["can_id"]);
			$circonscriptionDeleter = array("cci_candidature_id" => $candidature["can_id"]);

			$statement = $this->pdo->prepare($deletePositionsQuery);
			$statement->execute($positionDeleter);

			$statement = $this->pdo->prepare($deleteCirconscriptionsQuery);
			$statement->execute($circonscriptionDeleter);

			$statement = $this->pdo->prepare($insertPositionsQuery);
			foreach($positions as $position) {
				$positionInserter = array(	"cpo_candidature_id" => $candidature["can_id"],
											"cpo_position" => trim($position));
				$statement->execute($positionInserter);

//				echo showQuery($insertPositionsQuery, $positionInserter);
			}

			$statement = $this->pdo->prepare($insertCirconscriptionsQuery);
			foreach($circonscriptions as $circonscription) {
				$circonscriptionInserter = array(	"cci_candidature_id" => $candidature["can_id"],
													"cci_circonscription" => trim($circonscription));
				$statement->execute($circonscriptionInserter);

//				echo showQuery($insertCirconscriptionsQuery, $circonscriptionInserter);
			}

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function getCandidatureStats($departments = array()) {
		$query = "	SELECT
						SUBSTRING(REPLACE(cci1.cci_circonscription, '#circo', ''), 1, 2) as department,
						REPLACE(cci1.cci_circonscription, '#circo', '') as circonscription,
						cpo_position as position,
						SUM( 1 / (SELECT COUNT(cci2.cci_id) FROM candidature_circonscriptions cci2 WHERE cci2.cci_candidature_id = can_id) / (SELECT COUNT(cpo2.cpo_id) FROM candidature_positions cpo2 WHERE cpo2.cpo_candidature_id = can_id)) as nb_parted_persons
					FROM candidatures
					LEFT JOIN candidature_circonscriptions cci1 ON cci1.cci_candidature_id = can_id
					LEFT JOIN candidature_positions cpo1 ON cpo1.cpo_candidature_id = can_id
					GROUP BY department, circonscription, position WITH ROLLUP";

		if (count($departments)) {
			$query .= " HAVING department IN (";

			$query .= implode($departments, ", ");

			$query .= ")";
		}

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $candidature);

		$results = array();

		try {
			$statement->execute();
			$lines = $statement->fetchAll();

			foreach($lines as $line) {
				$results[$line["department"]][$line["circonscription"]][$line["position"]] = $line["nb_parted_persons"];
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $results;
	}

	function addAddress(&$address) {
		$query = "	INSERT INTO addresses
						(add_entity, add_line_1, add_line_2,
						add_zip_code, add_city, add_country_id, add_company_name)
					VALUES
						(:add_entity, :add_line_1, :add_line_2,
						:add_zip_code, :add_city, :add_country_id, :add_company_name)	";

		$statement = $this->pdo->prepare($query);
// 		echo showQuery($query, $address);

		try {
			$statement->execute($address);

			$address["add_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}
}
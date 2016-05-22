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

class VotingPaperBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new VotingPaperBo($pdo);
	}

	function create(&$votingPaper) {
		$query = "	INSERT INTO voting_papers () VALUES ()	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute();
			$votingPaper["vpa_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($votingPaper) {
		$query = "	UPDATE voting_papers SET ";

		$separator = "";
		foreach($votingPaper as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE vpa_id = :vpa_id ";

//		echo showQuery($query, $votingPaper);

		$statement = $this->pdo->prepare($query);
		$statement->execute($votingPaper);
	}

	function save(&$votingPaper) {
		if (!isset($votingPaper["vpa_id"]) || !$votingPaper["vpa_id"]) {
			$this->create($votingPaper);
		}

		$this->update($votingPaper);
	}

	function getLastVotingPaper($campaignId) {
		$args = array("vpa_campaign_id" => $campaignId);

		$query = "	SELECT *
					FROM
						voting_papers
					WHERE
						vpa_campaign_id = :vpa_campaign_id	";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (count($results)) {
				return $results[0];
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}
}
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

class DocumentBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new DocumentBo($pdo);
	}

	function getPartyDocuments($party, $filters) {
		$query = "	SELECT *
					FROM
						documents
					WHERE
						doc_political_party_id = :doc_political_party_id	";

		$args = array("doc_political_party_id" => $party["ppa_id"]);

		if (isset($filters["like_doc_mime_type"])) {
			$query .= " AND doc_mime_type LIKE :like_doc_mime_type ";
			$args["like_doc_mime_type"] = $filters["like_doc_mime_type"];
		}

		if (isset($filters["sorts"])) {
			$orderSeparator = "	ORDER BY ";
			foreach($filters["sorts"] as $sort) {
				$query .= $orderSeparator;

				$query .= $sort["field"] . " " . $sort["direction"];
				$orderSeparator = ", ";
			}
		}

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			return $results;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getDocuments($campaign, $filters) {
		$query = "	SELECT *
					FROM
						documents
					LEFT JOIN tasks ON tas_id = doc_task_id
					WHERE
						doc_campaign_id = :doc_campaign_id	";

		$args = array("doc_campaign_id" => $campaign["cam_id"]);

		if (isset($filters["like_doc_mime_type"])) {
			$query .= " AND doc_mime_type LIKE :like_doc_mime_type ";
			$args["like_doc_mime_type"] = $filters["like_doc_mime_type"];
		}

		if (isset($filters["doc_label"])) {
			$query .= " AND doc_label = :doc_label ";
			$args["doc_label"] = $filters["doc_label"];
		}

		if (isset($filters["sorts"])) {
			$orderSeparator = "	ORDER BY ";
			foreach($filters["sorts"] as $sort) {
				$query .= $orderSeparator;

				$query .= $sort["field"] . " " . $sort["direction"];
				$orderSeparator = ", ";
			}
		}

//		echo showQuery($query, $args);
		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			return $results;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function addDocument(&$document) {

		$query = "	INSERT INTO documents
						(doc_task_id, doc_campaign_id, doc_political_party_id, doc_name, doc_size,
						doc_mime_type, doc_label, doc_path)
					VALUES
						(:doc_task_id, :doc_campaign_id, :doc_political_party_id, :doc_name, :doc_size,
						:doc_mime_type, :doc_label, :doc_path)	";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $document);

		try {
			$statement->execute($document);

// 			print_r($statement->errorInfo());
// 			echo "<br />\n";
// 			echo $statement->errorCode();
// 			echo "<br />\n";

			$document["doc_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function getByPath($path) {
		$query = "	SELECT *
					FROM
						documents
					LEFT JOIN tasks ON tas_id = doc_task_id
					WHERE
						doc_path like :doc_path	";

		$args = array("doc_path" => "%/" . $path);

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

	function getDocument($documentId) {
		$query = "	SELECT *
					FROM
						documents
					LEFT JOIN tasks ON tas_id = doc_task_id
					WHERE
						doc_id = :doc_id	";

		$args = array("doc_id" => $documentId);

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
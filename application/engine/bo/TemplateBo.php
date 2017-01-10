<?php /*
	Copyright 2015 Cédric Levieux, Parti Pirate

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

class TemplateBo {
	var $pdo = null;
	var $galetteDatabase = "";
	var $personaeDatabase = "";

	var $TABLE = "campaign_templates";
	var $ID_FIELD = "cte_id";

	function __construct($pdo, $config) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo, $config) {
		return new TemplateBo($pdo, $config);
	}

	function create(&$template) {
		$query = "	INSERT INTO $this->TABLE () VALUES ()	";

		$statement = $this->pdo->prepare($query);
//				echo showQuery($query, $args);

		try {
			$statement->execute();
			$template[$this->ID_FIELD] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($template) {
		$query = "	UPDATE $this->TABLE SET ";

		$separator = "";
		foreach($template as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE $this->ID_FIELD = :$this->ID_FIELD ";

//		echo showQuery($query, $template);

		$statement = $this->pdo->prepare($query);
		$statement->execute($template);
	}

	function save(&$template) {
 		if (!isset($template[$this->ID_FIELD]) || !$template[$this->ID_FIELD]) {
			$this->create($template);
		}

		$this->update($template);
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

		$query = "	SELECT *
					FROM $this->TABLE
					WHERE
						1 = 1 \n";

		if (isset($filters[$this->ID_FIELD])) {
			$args[$this->ID_FIELD] = $filters[$this->ID_FIELD];
			$query .= " AND $this->ID_FIELD = :$this->ID_FIELD \n";
		}

		if (isset($filters["cte_active"])) {
			$args["cte_active"] = $filters["cte_active"];
			$query .= " AND cte_active = :cte_active \n";
		}

//		$query .= "	ORDER BY cte_parent_id ASC , cte_order ASC ";

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
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $results;
	}
}
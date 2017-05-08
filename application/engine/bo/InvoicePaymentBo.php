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

class InvoicePaymentBo {
	var $pdo = null;
	var $config = null;
	var $galetteDatabase = "";
	var $personaeDatabase = "";

	var $TABLE = "invoice_payments";
	var $ID_FIELD = "ipa_id";

	static $DA = "DA"; // by the representative
	static $DB = "DB"; // by a political party
	static $DC = "DC"; // by a "concours en nature"

	function __construct($pdo, $config) {
		$this->pdo = $pdo;
		$this->config = $config;
	}

	static function newInstance($pdo, $config) {
		return new InvoicePaymentBo($pdo, $config);
	}

	function create(&$invoicePayment) {
		$query = "	INSERT INTO $this->TABLE () VALUES ()	";

		$statement = $this->pdo->prepare($query);
//				echo showQuery($query, $args);

		try {
			$statement->execute();
			$invoicePayment[$this->ID_FIELD] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($invoicePayment) {
		$query = "	UPDATE $this->TABLE SET ";

		$separator = "";
		foreach($invoicePayment as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE $this->ID_FIELD = :$this->ID_FIELD ";

//		echo showQuery($query, $invoicePayment);

		$statement = $this->pdo->prepare($query);
		$statement->execute($invoicePayment);
	}

	function save(&$invoicePayment) {
 		if (!isset($invoicePayment[$this->ID_FIELD]) || !$invoicePayment[$this->ID_FIELD]) {
			$this->create($invoicePayment);
		}

		$this->update($invoicePayment);
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
					FROM $this->TABLE

					WHERE
						1 = 1 \n";

		if (isset($filters[$this->ID_FIELD])) {
			$args[$this->ID_FIELD] = $filters[$this->ID_FIELD];
			$query .= " AND $this->ID_FIELD = :$this->ID_FIELD \n";
		}

//		$query .= "	ORDER BY cte_parent_id ASC , cte_order ASC ";

//		$query .= "	ORDER BY mes_id DESC  ";

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
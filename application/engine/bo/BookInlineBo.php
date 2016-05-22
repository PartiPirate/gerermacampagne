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

class BookInlineBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new BookInlineBo($pdo);
	}

	function addDonation(&$donation) {
		$query = "	INSERT INTO donations
						(don_book_inline_id, don_firstname, don_lastname, don_address_id)
					VALUES
						(:don_book_inline_id, :don_firstname, :don_lastname, :don_address_id)	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($donation);
			$donation["don_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function addInlineDocument(&$inlineDocument) {
		$query = "	INSERT INTO inline_documents
						(ido_document_id, ido_book_inline_id, ido_type)
					VALUES
						(:ido_document_id, :ido_book_inline_id, :ido_type)	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($inlineDocument);
			$inlineDocument["ido_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function addInline(&$inline) {
		$query = "	INSERT INTO book_inlines
						(bin_campaign_id, bin_label, bin_amount,
						bin_book, bin_column, bin_type, bin_transaction_date)
					VALUES
						(:bin_campaign_id, :bin_label, :bin_amount,
						:bin_book, :bin_column, :bin_type, :bin_transaction_date)	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($inline);

// 			print_r($statement->errorInfo());
// 			echo "<br />\n";
// 			echo $statement->errorCode();
// 			echo "<br />\n";

			$inline["bin_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($inline) {
		$query = "	UPDATE book_inlines SET ";

		$separator = "";
		foreach($inline as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE bin_id = :bin_id ";

		$statement = $this->pdo->prepare($query);
		$statement->execute($inline);
	}

	function getInlines($campaign, $filters) {
		$args = array("bin_campaign_id" => $campaign["cam_id"]);

		$query = "	SELECT *
					FROM
						book_inlines
					LEFT JOIN donations ON bin_id = don_book_inline_id
					LEFT JOIN inline_documents ON bin_id = ido_book_inline_id
					LEFT JOIN documents ON doc_id = ido_document_id
					WHERE
						bin_campaign_id = :bin_campaign_id	";

		if (isset($filters["sorts"])) {
			$orderSeparator = "	ORDER BY ";
			foreach($filters["sorts"] as $sort) {
				$query .= $orderSeparator;

				$query .= $sort["field"] . " " . $sort["direction"];
				$orderSeparator = ", ";
			}
		}

		if (isset($filters["bin_id"])) {
			$query .= " AND bin_id = :bin_id ";
			$args["bin_id"] = $filters["bin_id"];
		}

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			$inlines = array();

			foreach($results as $line) {
				$inlines[$line["bin_id"]]["bin_id"] = $line["bin_id"];
				$inlines[$line["bin_id"]]["bin_campaign_id"] = $line["bin_campaign_id"];
				$inlines[$line["bin_id"]]["bin_label"] = $line["bin_label"];
				$inlines[$line["bin_id"]]["bin_amount"] = $line["bin_amount"];
				$inlines[$line["bin_id"]]["bin_book"] = $line["bin_book"];
				$inlines[$line["bin_id"]]["bin_column"] = $line["bin_column"];
				$inlines[$line["bin_id"]]["bin_type"] = $line["bin_type"];
				$inlines[$line["bin_id"]]["bin_transaction_date"] = $line["bin_transaction_date"];

				if (!isset($inlines[$line["bin_id"]]["documents"])) {
					$inlines[$line["bin_id"]]["documents"] = array();
				}

				$inlines[$line["bin_id"]]["documents"][$line["doc_id"]]["doc_id"] = $line["doc_id"];
				$inlines[$line["bin_id"]]["documents"][$line["doc_id"]]["ido_type"] = $line["ido_type"];
				$inlines[$line["bin_id"]]["documents"][$line["doc_id"]]["doc_label"] = $line["doc_label"];
				$inlines[$line["bin_id"]]["documents"][$line["doc_id"]]["doc_name"] = $line["doc_name"];
//				$inlines[$line["bin_id"]]["documents"][$line["doc_id"]]["doc_id"] = $line["doc_id"];
			}

// 			foreach($results as $key => $result) {
// 				$results[$key]["tas_dependencies"] = json_decode($results[$key]["tas_dependencies"], true);
// 				$results[$key]["tas_righters"] = json_decode($results[$key]["tas_righters"], true);
// 				$results[$key]["tas_documents"] = json_decode($results[$key]["tas_documents"], true);
// 			}

			return $inlines;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}
}
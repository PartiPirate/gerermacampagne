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

class TelephoneBo {
	const TYPE_TELEPHONE = "telephone";
	const TYPE_FAX = "fax";

	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new TelephoneBo($pdo);
	}

	function create(&$telephone) {
		$query = "	INSERT INTO telephones () VALUES () ";

		$statement = $this->pdo->prepare($query);
// 		echo showQuery($query, $telephone);

		$statement->execute();

		$telephone["tel_id"] = $this->pdo->lastInsertId();

		return true;
	}

	function update($telephone) {
		$query = "	UPDATE telephones SET ";

		$separator = "";
		foreach($telephone as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE tel_id = :tel_id ";

		//		echo showQuery($query, $telephone);

		$statement = $this->pdo->prepare($query);
		$statement->execute($telephone);
	}

	function save(&$telephone) {
		if (!isset($telephone["tel_id"]) || !$telephone["tel_id"]) {
			$this->create($telephone);
		}

		$this->update($telephone);
	}

	function delete($telephone) {
		$query = "	DELETE FROM telephone
		WHERE
		tel_id = :tel_id";

		$statement = $this->pdo->prepare($query);
		$statement->execute(array("tel_id" => $telephone["tel_id"]));
	}

	function deleteUserPhones($user) {
		$query = "	DELETE FROM telephone
					WHERE
						tel_user_id = :tel_user_id";

		$statement = $this->pdo->prepare($query);
		$statement->execute(array("tel_user_id" => $user["use_id"]));
	}
}
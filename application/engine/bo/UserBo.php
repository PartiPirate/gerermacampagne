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

class UserBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new UserBo($pdo);
	}

	static function computePassword($password) {
		global $config;

		return hash("sha256", $config["salt"] . $password . $config["salt"], false);
	}

	function update($user) {
		$query = "UPDATE users ";
		$separator = " SET ";

		if (isset($user["use_language"])) {
			$query .= $separator . "	use_language = :use_language ";
			$separator = ", ";
		}

		if (isset($user["use_address_id"])) {
			$query .= $separator . "	use_address_id = :use_address_id ";
			$separator = ", ";
		}

		if (isset($user["use_mail"])) {
			$query .= $separator . "	use_mail = :use_mail ";
			$separator = ", ";
		}

		if (isset($user["use_password"])) {
			$query .= $separator . "	use_password = :use_password ";
			$separator = ", ";
		}

		$query .= "WHERE use_id = :use_id";

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($user);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}
	}

	function login($login, $password, &$session) {
		$args = array("use_login" => $login);
		$query = "SELECT * FROM users WHERE (use_login = :use_login OR use_mail = :use_login) AND use_activated = 1 ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				if ($user["use_password"] == UserBo::computePassword($password)) {
					SessionUtils::login($session, $user);
					return true;
				}
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function get($userId) {
		$args = array("use_id" => $userId);
		$query = "SELECT * FROM users WHERE use_id = :use_id";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				return $user;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getUserByMail($email) {
		$args = array("use_mail" => $email);
		$query = "SELECT * FROM users WHERE use_mail = :use_mail";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				return $user;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getUserId($user) {
		$args = array("use_login" => $user);
		$query = "SELECT * FROM users WHERE use_login = :use_login";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				return $user["use_id"];
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function activate($mail, $code) {
		$args = array("use_activated" => 0, "use_mail" => $mail, "use_activation_key" => $code);
		$query = "	UPDATE users
					SET use_activated = 1, use_activation_key = ''
					WHERE
						use_activated = :use_activated
					AND	use_activation_key = :use_activation_key
					AND	use_mail = :use_mail ";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$rowCount = $statement->rowCount();

			if ($rowCount) {
				return true;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function forgotten($mail, $hashedPassword) {
		$args = array(	"use_mail" => $mail,
						"use_password" => $hashedPassword);

		$query = "	UPDATE users
					SET use_password = :use_password
					WHERE
						use_mail = :use_mail ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function register($login, $mail, $hashedPassword, $activationKey, $language) {
		$args = array(	"use_activated" => 0,
						"use_mail" => $mail,
						"use_activation_key" => $activationKey,
						"use_language" => $language,
						"use_login" => $login,
						"use_password" => $hashedPassword);

		$query = "	INSERT INTO users
						(	use_login, use_password, use_mail, use_activated,
							use_activation_key, use_language)
					VALUES
						(	:use_login, :use_password, :use_mail, :use_activated,
							:use_activation_key, :use_language) ";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			return $this->pdo->lastInsertId();
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function hasDataExist($field, $value, $exceptUserId = null) {
		$args = array($field => $value);
		$query = "SELECT * FROM users WHERE $field = :$field ";

		if ($exceptUserId) {
			$args["user_id"] = $exceptUserId;
			$query .= " AND use_id != :use_id ";
		}

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				return true;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function addRight($userId, $right, $targetId) {
		$query = "	INSERT INTO user_rights
						(uri_user_id, uri_right, uri_target_id)
					VALUES
						(:uri_user_id, :uri_right, :uri_target_id) ";
		$statement = $this->pdo->prepare($query);

		$right = array(	"uri_user_id" => $userId,
						"uri_right" => $right,
						"uri_target_id" => $targetId);

		try {
			$statement->execute($right);

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function removeRight($rightId, $targetId = null) {
		$args = array(	"uri_id" => $rightId);
		$query = "	DELETE FROM user_rights
					WHERE uri_id = :uri_id  ";

		if ($targetId) {
			$query .= " AND uri_target_id = :uri_target_id ";
			$args["uri_target_id"] = $targetId;
		}

		$statement = $this->pdo->prepare($query);


		try {
			$statement->execute($args);

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}
}
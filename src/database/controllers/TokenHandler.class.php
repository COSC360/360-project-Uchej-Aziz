<?php 
require_once $_SERVER["DOCUMENT_ROOT"].'/server/services/DatabaseConnector.class.php';

class TokenHandler extends Controller {
		
	/**
	 * get
	 *
	 * @param  array $parameters
	 * @return array
	 */
	public function fetch(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		
		if ($parameters[1] === 1) {
			$sqlQuery = "SELECT users.is_email_verified, expires_datetime FROM tokens JOIN users ON tokens.userId = users.userId WHERE is_confirmation_token = 1 AND token='$parameters[0]'";
			$result = mysqli_query($dbConnection, $sqlQuery);
			$tableData = mysqli_fetch_row($result);	 
			
			if (is_null($tableData)) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Token is invalid."));
			}
			
			if ((int) $tableData[0] === 1) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "This email has already been confirmed."));
			}
			
			if (strtotime($tableData[1]) < time()) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Token has expired."));
			}
		} else if ($parameters[1] === 0) {
			$sqlQuery = "SELECT expires_datetime FROM tokens WHERE is_confirmation_token = 0 AND token='$parameters[0]'";
			$result = mysqli_query($dbConnection, $sqlQuery);
			$tableData = mysqli_fetch_row($result);	 

			if (is_null($tableData)) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Token is invalid."));
			}

			if (strtotime($tableData[0]) < time()) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Token has expired."));
			}
		}

		mysqli_close($dbConnection);
		return array("response" => 200);
	}
	
	/**
	 * post
	 *
	 * @param  array $parameters
	 * @return array
	 */
	public function post(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		if ($parameters[3] === 1) {
			$verificationcode = rand(1000, 99999);
			$sqlQuery = "INSERT INTO tokens(token_string, session_code, expires_datetime, userId) VALUES ('$parameters[0]', $verificationcode, '".date('Y-m-d H:i:s', $parameters[1])."', $parameters[2])";
		} else if ($parameters[3] === 0) {
			$charSet = 'V7lGKjRgBzZ0MD39x1fHk8wJdYtQyapNTqX6ScsPbnhuEI5iUme2OoA4WFrC';
		
			$token_expiration = time() + 86400;
			$token = '';

			for ($i = 0; $i < 60; $i++)
				$token .= $charSet[mt_rand(0, 61)];
			
			$sqlQuery = "SELECT userId FROM users WHERE email_address='$parameters[0]' LIMIT 1";
			$result = mysqli_query($dbConnection, $sqlQuery);
			
			$tableData = mysqli_fetch_row($result);
			$sqlQuery = "INSERT INTO tokens(token_string, expires_datetime, userId, is_confirmation_token) VALUES ('$token', '".date('Y-m-d H:i:s', $token_expiration)."', $tableData[0], 0)";
		}
		mysqli_query($dbConnection, $sqlQuery);
	
		return array("response" => 200);
	}

	public function updateData(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		if ($parameters[2] === 1) {
			$sqlQuery = "SELECT session_code, userId FROM tokens WHERE token_string='$parameters[1]'";
			$result = mysqli_query($dbConnection, $sqlQuery);
			$tableData = mysqli_fetch_row($result);	 

			if ($tableData[0] !== $parameters[0]) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Confirmation code not valid"));
			}

			$sqlQuery = "UPDATE users SET is_email_verified = 1 WHERE userId=$tableData[1]";
			mysqli_query($dbConnection, $sqlQuery);
		} else if ($parameters[2] === 0) {

			$sqlQuery = "SELECT userId FROM tokens WHERE token_string='$parameters[1]'";
			$result = mysqli_query($dbConnection, $sqlQuery);
			$tableData = mysqli_fetch_row($result);	 

			$charSet = 'V7lGKjRgBzZ0MD39x1fHk8wJdYtQyapNTqX6ScsPbnhuEI5iUme2OoA4WFrC';
			$hash = '';
			
			for($i = 0; $i < 10; $i++)
				$hash .= $charSet[mt_rand(0, 61)];
					
			$password_hash = hash('sha256', $parameters[0] . $hash);

			$sqlQuery = "UPDATE users SET password_hash = '$password_hash', salt = '$hash' WHERE userId = $tableData[0]";
			mysqli_query($dbConnection, $sqlQuery);

			$sqlQuery = "UPDATE tokens SET expires_datetime = '".date('Y-m-d H:i:s', time())."' WHERE token = '$parameters[1]'";
			mysqli_query($dbConnection, $sqlQuery);
		}


		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function discard(array $parameters) : array {
		return array();
	}

	public function getById(int $id) : array {
		return array();
	}

	public function getAll(array $parameters) : array {
		return array();
	}
}
?>
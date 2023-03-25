<?php 
@session_start();

require_once $_SERVER["DOCUMENT_ROOT"].'/server/helpers/Controller.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/TokenController.class.php';

class UserHandler extends Controller {
		
	/**
	 * getRegistered
	 *
	 * @param  array $parameters
	 * @return array
	 */
	public function getRegistered(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$charSet = 'V7lGKjRgBzZ0MD39x1fHk8wJdYtQyapNTqX6ScsPbnhuEI5iUme2OoA4WFrC';
		$hash = '';
		
		for($i = 0; $i < 10; $i++)
			$hash .= $charSet[mt_rand(0, 43)];
        
		$password_hash = hash('sha256', $parameters[2] . $hash);
		
		$sqlQuery = "INSERT INTO users(username, email_address, password_hash, password_salt) VALUES ('$parameters[0]', '$parameters[1]', '$password_hash', '$hash')";
		mysqli_query($dbConnection, $sqlQuery);

		$sqlQuery = "SELECT userId FROM users WHERE username='$parameters[0]' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$tableData = mysqli_fetch_row($result);
		
		$token_expiration = time() + 86400;
		$token = '';

		for ($i = 0; $i < 60; $i++)
			$token .= $charSet[mt_rand(0, 61)];

		$token = (new TokenController())->post([$token, $token_expiration, $tableData[0], 1]);

		mysqli_close($dbConnection);
		return array("response" => 200);
	}
		
	/**
	 * ensureLogin
	 *
	 * @param  array $parameters
	 * @return array
	 */
	public function ensureLogin(array $parameters) : array {
		$result = array("response" => 400, "data" => array("message" => "This user does not exist."));
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT username, email_address, password_hash, is_email_verified, password_salt, is_account_disabled, avatar_image_url, is_admin FROM users WHERE email='$parameters[0]'";
		$resp = mysqli_query($dbConnection, $sqlQuery);
		
		while($tableData = mysqli_fetch_assoc($resp)) {
			if (!$tableData['is_email_verified']) {
				$result["data"] = array("message" => "This email isn't verified.");
				break;
			}

			if ($tableData['is_account_disabled']) {
				$result["data"] = array("message" => "Your account has been disabled by the admins.");
				break;
			}

			$hashed_password = hash('sha256', $parameters[1] . $tableData['password_salt']);

			if ($tableData['password_hash'] === $hashed_password) {
				$result =array("response" => 200);
				$_SESSION['USER_AUTHENTICATED'] = true;
				$_SESSION['IS_ADMIN'] = $tableData['is_admin'];
				$_SESSION['USER_ICON'] = 'http://'.$_SERVER['HTTP_HOST'].''.$tableData['avatar_image_url'];
				$_SESSION['USERNAME'] = $tableData['username'];
				break;
			} else {
				$result["response"] = 400;
				$result["data"] = array("message" => "Incorrect Password");
				break;
			}
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function changeProfileImg($imgFile) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "UPDATE users SET avatar_image_url = '$imgFile' WHERE username='".$_SESSION['USERNAME']."'";

		$result = mysqli_query($dbConnection, $sqlQuery);
		$_SESSION['USER_ICON'] = 'http://'.$_SERVER['HTTP_HOST'].''.$imgFile;
		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function post(array $parameters) : array {
		return array();
	}

	public function fetch(array $parameters) : array {
		return array();
	}

	public function updateData(array $parameters) : array {
		if (count($parameters) === 1) {
			$dbConnection = (new DatabaseConnector())->getConnection();
			$sqlQuery = "UPDATE users SET username = '$parameters[0]' WHERE username='".$_SESSION['USERNAME']."'";
			$result = mysqli_query($dbConnection, $sqlQuery);
			
			mysqli_close($dbConnection);
			$_SESSION['USERNAME'] = $parameters[0];
			return array("response" => 200);
		} else if (count($parameters) === 2) {
			$dbConnection = (new DatabaseConnector())->getConnection();

			$sqlQuery = "SELECT password_hash, password_salt FROM users WHERE username='".$_SESSION['USERNAME']."' LIMIT 1";
			$result = mysqli_query($dbConnection, $sqlQuery);
			
			$tableData = mysqli_fetch_row($result);

			$hashed_password = hash('sha256', $parameters[0] . $tableData[1]);

			if ($hashed_password != $tableData[0]) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Incorrect old password."));
			}

			$charSet = 'V7lGKjRgBzZ0MD39x1fHk8wJdYtQyapNTqX6ScsPbnhuEI5iUme2OoA4WFrC';
			$hash = '';
			
			for($i = 0; $i < 10; $i++)
				$hash .= $charSet[mt_rand(0, 43)];
					
			$password_hash = hash('sha256', $parameters[1] . $hash);

			$sqlQuery = "UPDATE users SET password_hash = '$password_hash', password_salt = '$hash' WHERE username='".$_SESSION['USERNAME']."'";
			mysqli_query($dbConnection, $sqlQuery);

			mysqli_close($dbConnection);
			return array("response" => 200);
		} else if (count($parameters) === 3) {
			$dbConnection = (new DatabaseConnector())->getConnection();

			$sqlQuery = "SELECT password_hash, password_salt FROM users WHERE username='".$_SESSION['USERNAME']."' LIMIT 1";
			$result = mysqli_query($dbConnection, $sqlQuery);
			
			$tableData = mysqli_fetch_row($result);

			$hashed_password = hash('sha256', $parameters[1] . $tableData[1]);

			if ($hashed_password != $tableData[0]) {
				mysqli_close($dbConnection);
				return array("response" => 400, "data" => array("message" => "Incorrect old password."));
			}

			$charSet = 'V7lGKjRgBzZ0MD39x1fHk8wJdYtQyapNTqX6ScsPbnhuEI5iUme2OoA4WFrC';
			$hash = '';
			
			for($i = 0; $i < 10; $i++)
				$hash .= $charSet[mt_rand(0, 43)];
					
			$password_hash = hash('sha256', $parameters[2] . $hash);

			$sqlQuery = "UPDATE users SET password_hash = '$password_hash', password_salt = '$hash', username='$parameters[0]' WHERE username='".$_SESSION['USERNAME']."'";
			mysqli_query($dbConnection, $sqlQuery);

			mysqli_close($dbConnection);
			$_SESSION['USERNAME'] = $parameters[0];
			return array("response" => 200);
		}
		return array();
	}

	public function discard(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "UPDATE users SET is_account_disabled = 1 WHERE username='".$_SESSION['USERNAME']."'";
		mysqli_query($dbConnection, $sqlQuery);

		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function getById(int $id) : array {
		return array();
	}

	public function findUserById(int $id) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT userId FROM users WHERE userId='$id'";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		while($tableData = mysqli_fetch_assoc($resp)) {
			mysqli_close($dbConnection);
			return true;
		}
		mysqli_close($dbConnection);
		return false;
	}

	public function isAccountSuspended(string $username) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT is_account_disabled FROM users WHERE username='$username' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$tableData = mysqli_fetch_row($result);
		
		mysqli_close($dbConnection);
		return $tableData[0];
	}

	public function getUserByEmail(string $email_address) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT email_address FROM users WHERE email_address='$email_address'";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		while($tableData = mysqli_fetch_assoc($resp)) {
			mysqli_close($dbConnection);
			return true;
		}
		mysqli_close($dbConnection);
		return false;
	}

	public function usernameEmailConfirmation(string $username) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT is_email_verified FROM users WHERE username='$username' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$tableData = mysqli_fetch_row($result);

		mysqli_close($dbConnection);
		return $tableData[0];
	}

	public function emailConfirmation(string $email_address) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT is_email_verified FROM users WHERE email_address='$email_address' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$tableData = mysqli_fetch_row($result);
		mysqli_close($dbConnection);
		return $tableData[0];
	}

	public function getUserDataByUsername(string $username) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT username FROM users WHERE username='$username'";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		while($tableData = mysqli_fetch_assoc($resp)) {
			mysqli_close($dbConnection);
			return true;
		}
		mysqli_close($dbConnection);
		return false;
	}

	public function getAll(array $parameters) : array {
		return array();
	}

	public function retrieveOriginalPostsAndComments(array $parameters) : array {
		
		$dbConnection = (new DatabaseConnector())->getConnection();
		
		if ($parameters[1] == 0) {
			$sqlQuery = "(SELECT body, forum_threads.threadUrl, 'comments' as comment_content FROM comments JOIN users ON users.userId = comments.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id WHERE users.username = '$parameters[0]' ORDER BY comments.timestamp_submitted) UNION (SELECT body, forum_threads.threadUrl, 'OriginalPosts' as body_post FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id WHERE users.username = '$parameters[0]' ORDER BY OriginalPosts.timestamp_submitted)";
		} else if ($parameters[1] == 1) {
			$sqlQuery = "(SELECT body, forum_threads.threadUrl, 'comments' as comment_content FROM comments JOIN users ON users.userId = comments.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id WHERE users.userId = '$parameters[0]' ORDER BY comments.timestamp_submitted) UNION (SELECT body, forum_threads.threadUrl, 'OriginalPosts' as body_post FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id WHERE users.userId = '$parameters[0]' ORDER BY OriginalPosts.timestamp_submitted)";
		}
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, array(
				"content" => $tableData['body'], 
				"link" =>  $tableData['threadUrl'], 
				"type" => $tableData['comment_content'])
			);
		}
		mysqli_close($dbConnection);
		return $result;
	}
}
?>
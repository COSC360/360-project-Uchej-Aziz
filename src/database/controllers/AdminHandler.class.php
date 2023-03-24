<?php 
	@session_start();
	require_once $_SERVER["DOCUMENT_ROOT"].'/server/helpers/Controller.class.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/server/services/DatabaseConnector.class.php';


	class AdminHandler extends Controller {
		public function fetch(array $parameters) : array {
			return array();
		}
		
		public function fetchStats() : array {
			$dbConnection = (new DatabaseConnector())->getConnection();
			$sqlQuery = "SELECT 'users' AS tableName, COUNT(*) as record_count FROM users UNION SELECT 'forum_threads' AS tableName, COUNT(*) as record_count FROM forum_threads UNION SELECT 'comments' AS tableName, COUNT(*) as record_count FROM comments UNION SELECT 'posts' AS tableName, COUNT(*) as record_count FROM posts";
			$resp = mysqli_query($dbConnection, $sqlQuery);
			$result = array();
			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, $tableData['result']);
			}

			mysqli_close($dbConnection);
			return $result;
		}

		public function fetchThreads(array $parameters) : array {
			$dbConnection = (new DatabaseConnector())->getConnection();
			$sqlQuery = "SELECT forum_threads.related_thread_id, forum_threads.thread_title, forum_threads.threadUrl, DATE_FORMAT((forum_threads.timestamp_submitted), '%M %D, %Y') as submission_date, users.userId as creatorId, users.username as creatorName, forum_threads.is_disabled, forum_threads.is_refracted, COUNT(userThreads.related_thread_id) as threadMembers FROM `forum_threads` JOIN users ON forum_threads.ownerId = users.userId LEFT JOIN userThreads ON userThreads.related_thread_id = forum_threads.related_thread_id GROUP BY forum_threads.related_thread_id ORDER BY forum_threads.related_thread_id";

			$resp = mysqli_query($dbConnection, $sqlQuery);
			$result = array();

			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"related_thread_id" => $tableData['related_thread_id'],
					"thread_title" => $tableData['thread_title'],
					"threadUrl" => $tableData['threadUrl'],
					"submission_date" => $tableData['submission_date'],
					"creatorId" => $tableData['creatorId'],
					"creatorName" => $tableData['creatorName'],
					"is_disabled" => $tableData['is_disabled'],
					"is_refracted" => $tableData['is_refracted'],
					"threadMembers" => $tableData['threadMembers']
				]);
			}
			mysqli_close($dbConnection);
			return $result;
		}

		public function fetchUsers() : array {
			$dbConnection = (new DatabaseConnector())->getConnection();
			$sqlQuery = "SELECT userId, username, DATE_FORMAT((timestamp_submitted), '%M %D, %Y') as registration_date, email_address, is_email_verified, is_admin, is_account_disabled FROM users";

			$resp = mysqli_query($dbConnection, $sqlQuery);
			$result = array();

			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"userId" => $tableData['userId'],
					"username" => $tableData['username'],
					"registration_date" => $tableData['registration_date'],
					"email_address" => $tableData['email_address'],
					"is_email_verified" => $tableData['is_email_verified'],
					"is_admin" => $tableData['is_admin'],
					"is_account_disabled" => $tableData['is_account_disabled']
				]);
			}
			mysqli_close($dbConnection);
			return $result;
		}

		public function post(array $parameters) : array {
			return array();
		}

		public function modifyUserAccess(bool $parameter, int $userId) : void {
			$dbConnection = (new DatabaseConnector())->getConnection();
			
			if ((int)$parameter === 1) {
				$sqlQuery = "UPDATE users SET is_account_disabled = 1 WHERE userId = $userId";
				mysqli_query($dbConnection, $sqlQuery);
			} else if ((int)$parameter === 0){
				$sqlQuery = "UPDATE users SET is_account_disabled = 0 WHERE userId = $userId";
				mysqli_query($dbConnection, $sqlQuery);
			}
			mysqli_close($dbConnection);
		}

		public function modifyUserPermission(bool $parameter, int $userId) : void {
			$dbConnection = (new DatabaseConnector())->getConnection();
			
			if ((int)$parameter === 1) {
				$sqlQuery = "UPDATE users SET is_admin = 1 WHERE id = $userId";
				mysqli_query($dbConnection, $sqlQuery);
			} else if ((int)$parameter === 0){
				$sqlQuery = "UPDATE users SET is_admin = 0 WHERE id = $userId";
				mysqli_query($dbConnection, $sqlQuery);
			}
			mysqli_close($dbConnection);
		}

		public function lookupThreadTitle(string $query) : array {
			$dbConnection = (new DatabaseConnector())->getConnection();
			$sqlQuery = "SELECT forum_threads.related_thread_id, forum_threads.thread_title, forum_threads.threadUrl, DATE_FORMAT((forum_threads.timestamp_submitted), '%M %D, %Y') as submission_date, users.userId as creatorId, users.username as creatorName, forum_threads.is_disabled, forum_threads.is_refracted, COUNT(userThreads.related_thread_id) as threadMembers FROM `forum_threads` JOIN users ON forum_threads.creatorId = users.userId JOIN userThreads ON userThreads.related_thread_id = forum_threads.related_thread_id WHERE forum_threads.thread_title LIKE '%$query%' GROUP BY forum_threads.related_thread_id ORDER BY forum_threads.related_thread_id";

			$resp = mysqli_query($dbConnection, $sqlQuery);
			$result = array();
			$matchFound = false;

			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"related_thread_id" => $tableData['related_thread_id'],
					"thread_title" => $tableData['thread_title'],
					"threadUrl" => $tableData['threadUrl'],
					"submission_date" => $tableData['submission_date'],
					"creatorId" => $tableData['creatorId'],
					"creatorName" => $tableData['creatorName'],
					"is_disabled" => $tableData['is_disabled'],
					"is_refracted" => $tableData['is_refracted'],
					"threadMembers" => $tableData['threadMembers']
				]);
				$matchFound = true;
			}
			mysqli_close($dbConnection);
			if (!$matchFound) return array("resp" => 400);
			return ["resp" => 200, "data" => [$result]];
		}

		public function getUserByUsername(string $query) : array {
			$dbConnection = (new DatabaseConnector())->getConnection();
			$sqlQuery = "SELECT userId, username, DATE_FORMAT((timestamp_submitted), '%M %D, %Y') as registration_date, email_address, is_email_verified, is_admin, is_account_disabled FROM users WHERE username LIKE '%".$query."%'";

			$resp = mysqli_query($dbConnection, $sqlQuery);
			$result = array();
			$matchFound = false;

			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"userId" => $tableData['userId'],
					"username" => $tableData['username'],
					"registration_date" => $tableData['registration_date'],
					"email_address" => $tableData['email_address'],
					"is_email_verified" => $tableData['is_email_verified'],
					"is_admin" => $tableData['is_admin'],
					"is_account_disabled" => $tableData['is_account_disabled']
				]);
				$matchFound = true;
			}
			mysqli_close($dbConnection);
			if (!$matchFound) return array("resp" => 400);
			return ["resp" => 200, "data" => [$result]];
		}

		public function discard(array $parameters) : array {
			return array();
		}
	
		public function updateData(array $parameters) : array {
			return array();
		}

		public function getAll(array $parameters) : array {
			return array();
		}
	
		public function getById(int $id) : array {
			return array();
		}
	
		
	}
?>
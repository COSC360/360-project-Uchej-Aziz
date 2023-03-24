<?php 
@session_start();
require_once $_SERVER["DOCUMENT_ROOT"].'/server/helpers/Controller.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/UserController.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/services/DatabaseConnector.class.php';

class ThreadHandler extends Controller {
  public function fetch(array $parameters) : array {
		return array();
	}

	public function fetchTopThreads() : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT forum_threads.threadUrl, COUNT(OriginalPosts.related_post_id) as top FROM `threads` LEFT JOIN OriginalPosts ON threads.related_thread_id = OriginalPosts.related_thread_id GROUP BY threads.related_thread_id ORDER BY top DESC LIMIT 5";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
		
				"threadUrl" => $tableData['threadUrl'],
				"total_posts" => $tableData['top'] 
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function fetchThreads() : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT forum_threads.thread_title, forum_threads.threadUrl, forum_threads.background_image, forum_threads.thread_image FROM forum_threads";

		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"thread_title" => $tableData['thread_title'],
				"threadUrl" => $tableData['threadUrl'],
				"thread_background_image" => $tableData['background_image'],
				"thread_cover_picture" => $tableData['thread_image']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function fetchThreadByQuery(string $query) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT forum_threads.thread_title, forum_threads.threadUrl, forum_threads.background_image, forum_threads.thread_image FROM forum_threads WHERE thread_title LIKE '%$query%' OR threadUrl LIKE '%$query%'";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"thread_title" => $tableData['thread_title'],
				"threadUrl" => $tableData['threadUrl'],
				"thread_background_image" => $tableData['background_image'],
				"thread_cover_picture" => $tableData['thread_image']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function post(array $parameters) : array {
		$result = array("response" => 400, "data" => array("message" => "Thread cannot be created"));
		
		$dbConnection = (new DatabaseConnector())->getConnection();

		$get_user_query = "SELECT userId FROM users WHERE username = '".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $get_user_query);

		while ($tableData = mysqli_fetch_assoc($result)) {
			$userId = $tableData["userId"];
		}

		$sqlQuery = "INSERT INTO forum_threads(thread_title, threadUrl, background_image, thread_image, creatorId) 
				VALUES ('$parameters[0]', '$parameters[1]', '$parameters[2]', '$parameters[3]', $userId)";
		mysqli_query($dbConnection, $sqlQuery);
		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function fetchThreadByUrl(string $url) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT threadUrl FROM forum_threads where threadUrl = '$url' AND is_refracted = 0";
		
		$result = mysqli_query($dbConnection, $sqlQuery);
		while ($tableData = mysqli_fetch_assoc($result)) {
			mysqli_close($dbConnection);
			return true;
		}
		mysqli_close($dbConnection);
		return false;
	}

	public function updateData(array $parameters) : array {
		return array();
	}

	public function discard(array $parameters) : array {

		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "UPDATE forum_threads SET is_refracted = 1 WHERE related_thread_id = ".$parameters[0]."";
		mysqli_query($dbConnection, $sqlQuery);

		$sqlQuery = "SELECT userId FROM users WHERE username = '".$_SESSION['USERNAME']."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$admin = mysqli_fetch_row($result);

		$sqlQuery = "SELECT creatorId FROM forum_threads WHERE related_thread_id = ".$parameters[0]." LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$tableData = mysqli_fetch_row($result);

		$sqlQuery = "INSERT INTO UserNotifications(userId, replied_user_id, type_action, related_thread_id) VALUES (".$tableData[0].", ".$admin[0].", 6, ".$parameters[0].")";
		mysqli_query($dbConnection, $sqlQuery);
		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function hide(array $parameters) : array {

		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "UPDATE forum_threads SET is_disabled = 1 WHERE related_thread_id = ".$parameters[0]."";
		mysqli_query($dbConnection, $sqlQuery);

		$sqlQuery = "SELECT userId FROM users WHERE username = '".$_SESSION['USERNAME']."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$admin = mysqli_fetch_row($result);

		$sqlQuery = "SELECT creatorId FROM forum_threads WHERE related_thread_id = ".$parameters[0]." LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$tableData = mysqli_fetch_row($result);

		$sqlQuery = "INSERT INTO UserNotifications(userId, replied_user_id, type_action, related_thread_id) VALUES (".$tableData[0].", ".$admin[0].", 6, ".$parameters[0].")";
		mysqli_query($dbConnection, $sqlQuery);
		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function recoverThread(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "UPDATE forum_threads SET is_disabled = 0, is_refracted = 0 WHERE related_thread_id = ".$parameters[0]."";
		mysqli_query($dbConnection, $sqlQuery);

		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function getById(int $id) : array {
		return array();
	}

	public function getAll(array $parameters) : array {

		return array();
	}

	public function getTitle(string $parameters): string {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT thread_title FROM forum_threads WHERE threadUrl = '$parameters' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		$title = mysqli_fetch_row($result);
		return $title[0];
	}

	public function getThread(string $threadUrl) {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT forum_threads.thread_title, forum_threads.background_image, forum_threads.thread_image, forum_threads.is_disabled, CASE WHEN EXISTS(SELECT userThreads.userId FROM userThreads JOIN users ON userThreads.userId = users.userId WHERE users.username = '".$_SESSION["USERNAME"]."' AND userThreads.related_thread_id = forum_threads.related_thread_id) THEN 1 ELSE 0 END as subscribed FROM forum_threads WHERE forum_threads.threadUrl = '$threadUrl'";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"thread_title" => $tableData['thread_title'],
				"thread_background" => $tableData['background_image'],
				"thread_profile" => $tableData['thread_image'],
				"is_disabled" => $tableData['is_disabled'],
				"subscribed" => $tableData['subscribed']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function threadActions(array $parameters): array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$dataStatus = (int)$parameters[1];
		$get_user_query = "SELECT userId FROM users WHERE username = '".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $get_user_query);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$userId = $tableData["userId"];
		}
		
		$sqlQuery = "SELECT related_thread_id FROM forum_threads WHERE threadUrl = '$parameters[0]' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$related_thread_id = $tableData["related_thread_id"];
		}
		
		switch ($dataStatus) {
			case 0:
				$sqlQuery = "INSERT INTO userThreads(related_thread_id, userId) VALUES ($related_thread_id, $userId)";
				break;
			case 1:
				$sqlQuery = "DELETE FROM userThreads WHERE related_thread_id=$related_thread_id AND userId=$userId";
				break;
		}

		mysqli_query($dbConnection, $sqlQuery);
		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function retrieveTopUsers(string $url): array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT forum_threads.related_thread_id FROM forum_threads WHERE forum_threads.threadUrl = '$url' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$related_thread_id = $tableData["related_thread_id"];
		}
		
		$sqlQuery = "SELECT count(OriginalPosts.userId), users.userId, users.username, users.avatar_image_url FROM OriginalPosts JOIN users ON OriginalPosts.userId=users.userId WHERE OriginalPosts.related_thread_id=$related_thread_id GROUP BY (OriginalPosts.userId) ORDER BY count(OriginalPosts.userId) DESC LIMIT 5";
		$result = mysqli_query($dbConnection, $sqlQuery);
		$OutputArray = array();
		while ($tableData = mysqli_fetch_assoc($result)) {
			array_push($OutputArray, [
				"count" => $tableData["count(OriginalPosts.user_id)"],
				"username" => $tableData["username"],
				"avatar_image_url" => $tableData["avatar_image_url"],
				"userId" => $tableData["id"]
			]);
		}
		return $OutputArray;
	}
}
?>
<?php 
@session_start();
require_once $_SERVER["DOCUMENT_ROOT"].'/server/helpers/Controller.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/services/DatabaseConnector.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/CommentController.class.php';

class PostHandler extends Controller {

	public function fetch(array $parameters) : array {
		return array();
	}

	public function post(array $parameters) : array {
		$result = array("response" => 400, "data" => array("message" => "Thread cannot be created."));
		$dbConnection = (new DatabaseConnector())->getConnection();
		
		$threadUrl = end($parameters);
		$threadIdQuery = "SELECT related_thread_id, creatorId from forum_threads WHERE threadUrl = '$threadUrl' AND is_refracted != 1";
		$result = mysqli_query($dbConnection, $threadIdQuery);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$related_thread_id = $tableData["related_thread_id"];
			$creatorId = $tableData["creatorId"];
		}
		
		$get_user_query = "SELECT userId FROM users WHERE username = '".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $get_user_query);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$userId = $tableData["userId"];
		}

		$caseNumber = (int)$parameters[0];
        switch ($caseNumber) {
            case 1:
				$sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_body, post_image, post_media_url) 
						VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."', '".$parameters[3]."', '".$parameters[4]."')";
				mysqli_query($dbConnection, $sqlQuery);
				break;

            case 2:
                $sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_body, post_image) 
						VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."', '".$parameters[3]."')";
				mysqli_query($dbConnection, $sqlQuery);
                break;
            case 3:
                $sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_body, post_media_url) 
						VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."', '".$parameters[3]."')";
				mysqli_query($dbConnection, $sqlQuery);
                break;
            case 4:
                $sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_image, post_media_url) 
						VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."', '".$parameters[3]."')";
				mysqli_query($dbConnection, $sqlQuery);
                break;
            case 5:
				$sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_body) VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."')";
				mysqli_query($dbConnection, $sqlQuery);
				break;
            case 6:
				$sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_image) VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."')";
				mysqli_query($dbConnection, $sqlQuery);
				break;
            case 7:
				$sqlQuery = "INSERT INTO OriginalPosts(userId, related_thread_id, post_title, post_media_url) VALUES ($userId, $related_thread_id, '".$parameters[1]."', '".$parameters[2]."')";
				mysqli_query($dbConnection, $sqlQuery);
				break;
		}

		$sqlQuery = "INSERT INTO UserNotifications (userId,	replied_user_id, type_action, related_thread_id) VALUES ($creatorId, $userId, 1, $related_thread_id)";
		$result = mysqli_query($dbConnection, $sqlQuery);

		mysqli_close($dbConnection);
		return array("response" => 200);
	}

	public function updateData(array $parameters) : array {
		return array();
	}

	public function discard(array $parameters) : array {
		return array();
	}

	public function doesExist(int $id) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT related_post_id FROM OriginalPosts WHERE related_post_id = $id AND is_confidential = 0 AND is_refracted = 0 LIMIT 1";
		$resp = mysqli_query($dbConnection, $sqlQuery);
		while($tableData = mysqli_fetch_assoc($resp)) {
			mysqli_close($dbConnection);
			return true;
		}
		mysqli_close($dbConnection);
		return false;
	}

	public function getById(int $id) : array {
		return array();
	}

	public function submitVote(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];
		if ($parameters[1] === "upVote") {
			$sqlQuery = "SELECT related_post_id FROM PostVotes WHERE related_post_id = $parameters[0] AND userId = $userId LIMIT 1";
			$resp = mysqli_query($dbConnection, $sqlQuery);
			
			if(mysqli_num_rows($resp) === 0){
				$sqlQuery = "INSERT INTO PostVotes VALUES($parameters[0], $userId, 1)";
				mysqli_query($dbConnection, $sqlQuery);

				$sqlQuery = "SELECT userId, related_thread_id FROM OriginalPosts WHERE related_post_id = $parameters[0] LIMIT 1";
				$result = mysqli_query($dbConnection, $sqlQuery);
				$post = mysqli_fetch_row($result);
				$postCreator = $post[0];
				$postThreadId = $post[1];
				$sqlQuery = "INSERT INTO UserNotifications(userId, replied_user_id, type_action, related_thread_id) VALUES($postCreator, $userId, 4, $postThreadId)";
				mysqli_query($dbConnection, $sqlQuery);
			} else{
				$sqlQuery = "UPDATE PostVotes SET votes = 1 WHERE related_post_id = $parameters[0] AND userId = $userId";
				mysqli_query($dbConnection, $sqlQuery);
			}
		} else {
			$sqlQuery = "SELECT related_post_id FROM PostVotes WHERE related_post_id = $parameters[0] AND userId = $userId LIMIT 1";
			$resp = mysqli_query($dbConnection, $sqlQuery);
			
			if(mysqli_num_rows($resp) === 0){
				$sqlQuery = "INSERT INTO PostVotes VALUES($parameters[0], $userId, 0)";
				mysqli_query($dbConnection, $sqlQuery);

				$sqlQuery = "SELECT userId, related_thread_id FROM OriginalPosts WHERE related_post_id = $parameters[0] LIMIT 1";
				$result = mysqli_query($dbConnection, $sqlQuery);
				$post = mysqli_fetch_row($result);
				$postCreator = $post[0];
				$postThreadId = $post[1];
				$sqlQuery = "INSERT INTO UserNotifications(userId, replied_user_id, type_action, related_thread_id) VALUES($postCreator, $userId, 3, $postThreadId)";
				mysqli_query($dbConnection, $sqlQuery);
			} else{
				$sqlQuery = "UPDATE PostVotes SET votes = 0 WHERE related_post_id = $parameters[0] AND userId = $userId";
				mysqli_query($dbConnection, $sqlQuery);
			}
		}
		$sqlQuery = "SELECT 
		(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
		FROM OriginalPosts LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
		WHERE OriginalPosts.is_confidential = 0 AND OriginalPosts.is_refracted = 0 AND OriginalPosts.related_post_id = $parameters[0]";
		$result = mysqli_query($dbConnection, $sqlQuery);
		$post = mysqli_fetch_row($result);
		$voteCount = $post[0];

		mysqli_close($dbConnection);
		return array("response" => 200, "voteCount" => $voteCount);
	}

	public function findPostByQuery(string $query) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		if(!isset($_SESSION['USERNAME'])) {
			$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal,
            CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = -1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
            IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = -1 AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
            (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
            FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
            WHERE OriginalPosts.is_refracted = 0 AND (OriginalPosts.post_title LIKE '%$query%' OR OriginalPosts.post_body LIKE '%$query%')
            GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
			
			$resp = mysqli_query($dbConnection, $sqlQuery);

			$result = array();

			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"related_post_id" => $tableData['related_post_id'],
					"post_title" => $tableData['post_title'],
					"post_body" => $tableData['post_body'],
					"post_image" => $tableData['post_image'],
					"post_media_url" => $tableData['post_media_url'],
					"timestamp" => $tableData['getTimeElapsedInSeconds'],
					"username" => $tableData['username'],
					"creatorId" => $tableData['creatorId'],
					"avatar_image_url" => $tableData['avatar_image_url'],
					"threadUrl" => $tableData['threadUrl'],
					"commentsTotal" => $tableData['commentsTotal'],
					"votingStatus" => 0,
					"typeVote" => 0,
					"voteCount" => $tableData['voteCount']
				]);
			}

			mysqli_close($dbConnection);
			return $result;
		}

		$sqlQuery = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];

		$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal,
		CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
		IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
		(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
		FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
		WHERE OriginalPosts.is_refracted = 0 AND (OriginalPosts.post_title LIKE '%$query%' OR OriginalPosts.post_body LIKE '%$query%')
		GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"related_post_id" => $tableData['related_post_id'],
				"post_title" => $tableData['post_title'],
				"post_body" => $tableData['post_body'],
				"post_image" => $tableData['post_image'],
				"post_media_url" => $tableData['post_media_url'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"threadUrl" => $tableData['threadUrl'],
				"commentsTotal" => $tableData['commentsTotal'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function findPostsByQueryInThread(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$query = $parameters[0];
		$threadUrl = $parameters[1];
		$sqlQuery = "SELECT forum_threads.related_thread_id FROM forum_threads WHERE forum_threads.threadUrl = '$threadUrl' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$related_thread_id = $tableData["related_thread_id"];
		}

		$sqlQuery = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];

		$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, COUNT(comments.related_post_id) as commentsTotal, OriginalPosts.is_confidential,
		CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
		IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
		(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
		FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
		WHERE OriginalPosts.is_refracted = 0 AND (OriginalPosts.post_title LIKE '%$query%' OR OriginalPosts.post_body LIKE '%$query%') AND OriginalPosts.related_thread_id = $related_thread_id
		GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"related_post_id" => $tableData['related_post_id'],
				"post_title" => $tableData['post_title'],
				"post_body" => $tableData['post_body'],
				"post_image" => $tableData['post_image'],
				"post_media_url" => $tableData['post_media_url'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"threadUrl" => $tableData['threadUrl'],
				"commentsTotal" => $tableData['commentsTotal'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount'],
				"isConfidential" => $tableData['is_confidential'],
				"isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
				"isOwner" => $_SESSION["USERNAME"] == $tableData['username'] ? true : false,
				"comments" => (new CommentController())->loadCommentsByPost($tableData['related_post_id'], 0)
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function loadAllOriginalPosts() : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		if (!isset($_SESSION['USERNAME'])) {
			$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal,
            CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = -1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
            IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = -1 AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
            (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
            FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
            WHERE OriginalPosts.is_refracted = 0 
            GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
			$resp = mysqli_query($dbConnection, $sqlQuery);

			$result = array();
	
			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"related_post_id" => $tableData['related_post_id'],
					"post_title" => $tableData['post_title'],
					"post_body" => $tableData['post_body'],
					"post_image" => $tableData['post_image'],
					"post_media_url" => $tableData['post_media_url'],
					"timestamp" => $tableData['getTimeElapsedInSeconds'],
					"username" => $tableData['username'],
					"creatorId" => $tableData['creatorId'],
					"avatar_image_url" => $tableData['avatar_image_url'],
					"threadUrl" => $tableData['threadUrl'],
					"commentsTotal" => $tableData['commentsTotal'],
					"votingStatus" => 0,
					"typeVote" => 0,
					"voteCount" => $tableData['voteCount']
				]);
			}
			mysqli_close($dbConnection);
			return $result;

		}

		$sqlQuery = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];

		$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal,
		CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
		IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
		(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
		FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
		WHERE OriginalPosts.is_refracted = 0 
		GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"related_post_id" => $tableData['related_post_id'],
				"post_title" => $tableData['post_title'],
				"post_body" => $tableData['post_body'],
				"post_image" => $tableData['post_image'],
				"post_media_url" => $tableData['post_media_url'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"threadUrl" => $tableData['threadUrl'],
				"commentsTotal" => $tableData['commentsTotal'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function fetchPostsByThread(array $parameters): array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];
		if (!empty($parameters[1])) {
			if ($parameters[1] == "Top") {
				$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal, OriginalPosts.is_confidential,
				CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
				IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
				(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
				FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
				WHERE OriginalPosts.is_refracted = 0 AND forum_threads.threadUrl = '$parameters[0]'
				GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
			} else if ($parameters[1] == "New") {
				$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal, OriginalPosts.is_confidential,
				CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
				IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
				(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
				FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
				WHERE OriginalPosts.is_refracted = 0 AND forum_threads.threadUrl = '$parameters[0]'
				GROUP BY OriginalPosts.related_post_id ORDER BY getTimeElapsedInSeconds ASC";
			}
		} else {
			$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal, OriginalPosts.is_confidential,
			CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
			IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
			(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
			FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
			WHERE OriginalPosts.is_refracted = 0 AND forum_threads.threadUrl = '$parameters[0]'
			GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
		}
		
		$resp = mysqli_query($dbConnection, $sqlQuery);

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"related_post_id" => $tableData['related_post_id'],
				"post_title" => $tableData['post_title'],
				"post_body" => $tableData['post_body'],
				"post_image" => $tableData['post_image'],
				"post_media_url" => $tableData['post_media_url'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"threadUrl" => $tableData['threadUrl'],
				"commentsTotal" => $tableData['commentsTotal'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount'],
				"isConfidential" => $tableData['is_confidential'],
				"isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
				"isOwner" => $_SESSION["USERNAME"] == $tableData['username'] ? true : false,
				"comments" => (new CommentController())->loadCommentsByPost($tableData['related_post_id'], 0)
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function fetchSpecificPost(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		$user = mysqli_fetch_row($result);
		$userId = $user[0];

		$sqlQuery = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, (SELECT COUNT(comments.related_post_id) FROM comments WHERE comments.is_refracted=0 AND comments.related_post_id=OriginalPosts.related_post_id) as commentsTotal, OriginalPosts.is_confidential,
				CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
				IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
				(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
				FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
				WHERE OriginalPosts.is_refracted = 0 AND forum_threads.threadUrl = '$parameters[0]' AND OriginalPosts.related_post_id = $parameters[1]
				GROUP BY OriginalPosts.related_post_id";
		
		$resp = mysqli_query($dbConnection, $sqlQuery);
		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			$result = [ 
				"related_post_id" => $tableData['related_post_id'],
				"post_title" => $tableData['post_title'],
				"post_body" => $tableData['post_body'],
				"post_image" => $tableData['post_image'],
				"post_media_url" => $tableData['post_media_url'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"threadUrl" => $tableData['threadUrl'],
				"commentsTotal" => $tableData['commentsTotal'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount'],
				"isConfidential" => $tableData['is_confidential'],
				"isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
				"isOwner" => $_SESSION["USERNAME"] == $tableData['username'] ? true : false,
				"comments" => (new CommentController())->loadCommentsByPost($tableData['related_post_id'], 1)
			];

			$result["currentUserId"] = $userId;
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function getAll(array $parameters) : array {

		$result = array(
			"response" => 400,
			"data" => array()
		);

		if ($_SERVER['REQUEST_METHOD'] == "GET") {

			$result["response"] = 200;
			return $result;
		}

		return $result;
	}

	public function removePost(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery = "UPDATE OriginalPosts SET is_refracted=1 WHERE OriginalPosts.related_post_id=$parameters[0] LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery);
		
		$sqlQuery = "SELECT userId , related_thread_id FROM OriginalPosts WHERE related_post_id = $parameters[0] LIMIT 1";
		$resp = mysqli_query($dbConnection, $sqlQuery);
		$tableData = mysqli_fetch_assoc($resp);
		$userId = $tableData['userId'];
		$threadId = $tableData['related_thread_id'];

		$get_user_query = "SELECT userId FROM users WHERE username = '".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $get_user_query);
		while ($tableData = mysqli_fetch_assoc($result)) {
			$repliedUserId = $tableData["userId"];
		}

		$sqlQuery = "INSERT INTO UserNotifications (userId,	replied_user_id, type_action, related_thread_id) VALUES ($userId, $repliedUserId, 5, $threadId)";
		$result = mysqli_query($dbConnection, $sqlQuery);

		mysqli_close($dbConnection);
		return array("response" => 200);
	} 

	public function disablePost(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		if ($parameters[1] == "hide") {
			$sqlQuery = "UPDATE OriginalPosts SET is_confidential = 1 WHERE OriginalPosts.related_post_id=$parameters[0] LIMIT 1";
			$TextUpdater = "Unhide";
			$result = mysqli_query($dbConnection, $sqlQuery);

			$sqlQuery = "UPDATE comments SET is_confidential = 1 WHERE comments.related_post_id = $parameters[0]";
			$result = mysqli_query($dbConnection, $sqlQuery);
		} else {
			$sqlQuery = "UPDATE OriginalPosts SET is_confidential = 0 WHERE OriginalPosts.related_post_id=$parameters[0] LIMIT 1";
			$TextUpdater = "Hide";
			$result = mysqli_query($dbConnection, $sqlQuery);

			$sqlQuery = "UPDATE comments SET is_confidential = 0 WHERE comments.related_post_id = $parameters[0]";
			$result = mysqli_query($dbConnection, $sqlQuery);
		}
		
		mysqli_close($dbConnection);
		return array("response" => 200, "TextUpdater" => $TextUpdater);
	}
}
?>
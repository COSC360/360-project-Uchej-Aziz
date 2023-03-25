<?php 
@session_start();
require_once $_SERVER["DOCUMENT_ROOT"].'/server/helpers/Controller.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/services/DatabaseConnector.class.php';

class CommentHandler extends Handler {

	public function fetch(array $parameters) : array {
		return array();
	}

	public function post(array $parameters) : array {
		return array();
	}

	public function updateData(array $parameters) : array {
		return array();
	}

	public function discard(array $parameters) : array {
		return array();
	}

	public function getById(int $id) : array {
		return array();
	}

	public function fetchCommentsByPostId(int $id) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery  = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];
		$sqlQuery  = "SELECT comments.commentId, comments.body, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(comments.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url,
		CASE WHEN EXISTS(SELECT recorded_votes_comments.userId FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId) THEN 1 ELSE 0 END as voted,
		IF ((SELECT recorded_votes_comments.vote FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId AND recorded_votes_comments.vote = 1), 1, -1) as votingSelectionType,
		(SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 1 AND comments.commentId = recorded_votes_comments.commentId) - (SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 0 AND comments.commentId = recorded_votes_comments.commentId) as voteCount
		FROM comments JOIN users ON comments.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id LEFT JOIN recorded_votes_comments ON recorded_votes_comments.commentId = comments.commentId 
		WHERE comments.is_refracted = 0 AND comments.related_post_id = $id GROUP BY comments.commentId ORDER BY voteCount DESC";
		$resp = mysqli_query($dbConnection, $sqlQuery );

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"commentId" => $tableData['commentId'],
				"body" => $tableData['body'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function fetchCommentsByQuery(string $query) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		if(!isset($_SESSION['USERNAME'])) {
			$sqlQuery  = "SELECT comments.commentId, comments.body, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(comments.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url,
			CASE WHEN EXISTS(SELECT recorded_votes_comments.userId FROM recorded_votes_comments WHERE recorded_votes_comments.userId = -1 AND comments.commentId = recorded_votes_comments.commentId) THEN 1 ELSE 0 END as voted,
			IF ((SELECT recorded_votes_comments.vote FROM recorded_votes_comments WHERE recorded_votes_comments.userId = -1 AND comments.commentId = recorded_votes_comments.commentId AND recorded_votes_comments.vote = 1), 1, -1) as votingSelectionType,
			(SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 1 AND comments.commentId = recorded_votes_comments.commentId) - (SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 0 AND comments.commentId = recorded_votes_comments.commentId) as voteCount
			FROM comments JOIN users ON comments.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id LEFT JOIN recorded_votes_comments ON recorded_votes_comments.commentId = comments.commentId 
			WHERE comments.is_refracted = 0 AND comments.body LIKE '%$query%' GROUP BY comments.commentId ORDER BY voteCount DESC";
			$resp = mysqli_query($dbConnection, $sqlQuery );

			$result = array();

			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"commentId" => $tableData['commentId'],
					"body" => $tableData['body'],
					"timestamp" => $tableData['getTimeElapsedInSeconds'],
					"username" => $tableData['username'],
					"creatorId" => $tableData['creatorId'],
					"avatar_image_url" => $tableData['avatar_image_url'],
					"votingStatus" => 0,
					"typeVote" => 0,
					"voteCount" => $tableData['voteCount']
				]);
			}
			mysqli_close($dbConnection);
			return $result;
		}
		$sqlQuery  = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];
		$sqlQuery  = "SELECT comments.commentId, comments.body, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(comments.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url,
		CASE WHEN EXISTS(SELECT recorded_votes_comments.userId FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId) THEN 1 ELSE 0 END as voted,
		IF ((SELECT recorded_votes_comments.vote FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId AND recorded_votes_comments.vote = 1), 1, -1) as votingSelectionType,
		(SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 1 AND comments.commentId = recorded_votes_comments.commentId) - (SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 0 AND comments.commentId = recorded_votes_comments.commentId) as voteCount
		FROM comments JOIN users ON comments.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id LEFT JOIN recorded_votes_comments ON recorded_votes_comments.commentId = comments.commentId 
		WHERE comments.is_refracted = 0 AND comments.body LIKE '%$query%' GROUP BY comments.commentId ORDER BY voteCount DESC";
		$resp = mysqli_query($dbConnection, $sqlQuery );

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"commentId" => $tableData['commentId'],
				"body" => $tableData['body'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount']
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function doesExist(int $id) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery  = "SELECT commentId FROM comments WHERE commentId = $id AND is_confidential = 0 AND is_refracted = 0 LIMIT 1";
		$resp = mysqli_query($dbConnection, $sqlQuery );
		while($tableData = mysqli_fetch_assoc($resp)) {
			mysqli_close($dbConnection);
			return true;
		}
		mysqli_close($dbConnection);
		return false;
	}

	public function submitVote(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		$sqlQuery  = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];
		if ($parameters[1] === "upVote") {
			$sqlQuery  = "SELECT commentId FROM recorded_votes_comments WHERE commentId = $parameters[0] AND userId = $userId LIMIT 1";
			$resp = mysqli_query($dbConnection, $sqlQuery );
			
			if(mysqli_num_rows($resp) === 0){
				$sqlQuery  = "INSERT INTO recorded_votes_comments VALUES($parameters[0], $userId, 1)";
				mysqli_query($dbConnection, $sqlQuery );
			} else{
				$sqlQuery  = "UPDATE recorded_votes_comments SET vote = 1 WHERE commentId = $parameters[0] AND userId = $userId";
				mysqli_query($dbConnection, $sqlQuery );
			}
		} else {
			$sqlQuery  = "SELECT commentId FROM recorded_votes_comments WHERE commentId = $parameters[0] AND userId = $userId LIMIT 1";
			$resp = mysqli_query($dbConnection, $sqlQuery );
			
			if(mysqli_num_rows($resp) === 0){
				$sqlQuery  = "INSERT INTO recorded_votes_comments VALUES($parameters[0], $userId, 0)";
				mysqli_query($dbConnection, $sqlQuery );
			} else{
				$sqlQuery  = "UPDATE recorded_votes_comments SET vote = 0 WHERE commentId = $parameters[0] AND userId = $userId";
				mysqli_query($dbConnection, $sqlQuery );
			}
		}
		$sqlQuery  = "SELECT 
		(SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 1 AND comments.commentId = recorded_votes_comments.commentId) - (SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 0 AND comments.commentId = recorded_votes_comments.commentId) as voteCount
		FROM comments LEFT JOIN recorded_votes_comments ON recorded_votes_comments.commentId = comments.commentId 
		WHERE comments.is_confidential = 0 AND comments.is_refracted = 0 AND comments.commentId = $parameters[0]";
		$result = mysqli_query($dbConnection, $sqlQuery );
		$comment = mysqli_fetch_row($result);
		$voteCount = $comment[0];

		mysqli_close($dbConnection);
		return array("response" => 200, "voteCount" => $voteCount);
	}

	public function getAllComments() : array {
		$dbConnection = (new DatabaseConnector())->getConnection();

		if (!isset($_SESSION['USERNAME'])) {
			$sqlQuery  = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url, forum_threads.threadUrl, COUNT(comments.related_post_id) as commentsTotal,
			CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = -1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
			IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = -1 AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
			(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
			FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.uderId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
			WHERE OriginalPosts.is_refracted = 0 
			GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
			$resp = mysqli_query($dbConnection, $sqlQuery );

			$result = array();
	
			while($tableData = mysqli_fetch_assoc($resp)) {
				array_push($result, [
					"related_post_id" => $tableData['related_post_id'],
					"post_title" => $tableData['post_title'],
					"body" => $tableData['body'],
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

		$sqlQuery  = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		
		$user = mysqli_fetch_row($result);
		$userId = $user[0];

		$sqlQuery  = "SELECT OriginalPosts.related_post_id, OriginalPosts.post_title, OriginalPosts.post_body, OriginalPosts.post_image, OriginalPosts.post_media_url, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(OriginalPosts.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.usersId as creatorId, users.avatar_image_url, forum_threads.threadUrl, COUNT(comments.related_post_id) as commentsTotal,
		CASE WHEN EXISTS(SELECT PostVotes.userId FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id) THEN 1 ELSE 0 END as voted,
		IF ((SELECT PostVotes.votes FROM PostVotes WHERE PostVotes.userId = $userId AND OriginalPosts.related_post_id = PostVotes.related_post_id AND PostVotes.votes = 1), 1, -1) as votingSelectionType,
		(SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 1 AND OriginalPosts.related_post_id = PostVotes.related_post_id) - (SELECT COUNT(*) FROM PostVotes WHERE PostVotes.votes = 0 AND OriginalPosts.related_post_id = PostVotes.related_post_id) as voteCount
		FROM OriginalPosts JOIN users ON OriginalPosts.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = OriginalPosts.related_thread_id LEFT JOIN comments ON OriginalPosts.related_post_id = comments.related_post_id LEFT JOIN PostVotes ON PostVotes.related_post_id = OriginalPosts.related_post_id 
		WHERE OriginalPosts.is_refracted = 0 
		GROUP BY OriginalPosts.related_post_id ORDER BY voteCount DESC";
		$resp = mysqli_query($dbConnection, $sqlQuery );

		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"related_post_id" => $tableData['related_post_id'],
				"post_title" => $tableData['post_title'],
				"body" => $tableData['body'],
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

	public function fetchPostComments(int $postId, bool $getAllComments) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery  = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		$user = mysqli_fetch_row($result);
		$userId = $user[0];

		if ($getAllComments == false) {
			$sqlQuery  = "SELECT comments.commentId, comments.body, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(comments.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url,
			CASE WHEN EXISTS(SELECT recorded_votes_comments.userId FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId) THEN 1 ELSE 0 END as voted,
			IF ((SELECT recorded_votes_comments.vote FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId AND recorded_votes_comments.vote = 1), 1, -1) as votingSelectionType,
			(SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 1 AND comments.commentId = recorded_votes_comments.commentId) - (SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 0 AND comments.commentId = recorded_votes_comments.commentId) as voteCount
			FROM comments JOIN users ON comments.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id LEFT JOIN recorded_votes_comments ON recorded_votes_comments.commentId = comments.commentId 
			WHERE comments.is_refracted = 0 AND comments.related_post_id=$postId GROUP BY comments.commentId ORDER BY voteCount DESC LIMIT 3";
		} else if ($getAllComments == true) {
			$sqlQuery  = "SELECT comments.commentId, comments.body, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(comments.timestamp_submitted) as getTimeElapsedInSeconds, users.username, users.userId as creatorId, users.avatar_image_url,
			CASE WHEN EXISTS(SELECT recorded_votes_comments.userId FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId) THEN 1 ELSE 0 END as voted,
			IF ((SELECT recorded_votes_comments.vote FROM recorded_votes_comments WHERE recorded_votes_comments.userId = $userId AND comments.commentId = recorded_votes_comments.commentId AND recorded_votes_comments.vote = 1), 1, -1) as votingSelectionType,
			(SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 1 AND comments.commentId = recorded_votes_comments.commentId) - (SELECT COUNT(*) FROM recorded_votes_comments WHERE recorded_votes_comments.vote = 0 AND comments.commentId = recorded_votes_comments.commentId) as voteCount
			FROM comments JOIN users ON comments.userId = users.userId JOIN forum_threads ON forum_threads.related_thread_id = comments.related_thread_id LEFT JOIN recorded_votes_comments ON recorded_votes_comments.commentId = comments.commentId 
			WHERE comments.is_refracted = 0 AND comments.related_post_id=$postId GROUP BY comments.commentId ORDER BY getTimeElapsedInSeconds DESC";
		}
		
		$resp = mysqli_query($dbConnection, $sqlQuery );
		$result = array();

		while($tableData = mysqli_fetch_assoc($resp)) {
			array_push($result, [
				"commentId" => $tableData['commentId'],
				"body" => $tableData['body'],
				"timestamp" => $tableData['getTimeElapsedInSeconds'],
				"username" => $tableData['username'],
				"creatorId" => $tableData['creatorId'],
				"avatar_image_url" => $tableData['avatar_image_url'],
				"votingStatus" => $tableData['voted'],
				"typeVote" => $tableData['votingSelectionType'],
				"voteCount" => $tableData['voteCount'],
				"isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
				"isOwner" => $_SESSION["USERNAME"] == $tableData['username'] ? true : false
			]);
		}
		mysqli_close($dbConnection);
		return $result;
	}

	public function getAll(array $parameters) : array {
		return array();
	}

	public function deleteComment(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery  = "UPDATE comments SET is_refracted=1 WHERE comments.commentId=$parameters[0] LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		mysqli_close($dbConnection);
		return array("response" => 200);
	} 

	public function authenticatePostAndThread(array $parameters) : bool {
		$dbConnection = (new DatabaseConnector())->getConnection();
		
		$sqlQuery  = "SELECT forum_threads.related_thread_id FROM forum_threads WHERE forum_threads.threadUrl = '$parameters[2]' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		while ($tableData = mysqli_fetch_assoc($result)) {
			$threadId = $tableData["related_thread_id"];
		}

		$sqlQuery  = "SELECT COUNT(*) FROM OriginalPosts WHERE related_post_id=$parameters[1] AND related_thread_id=$threadId AND is_refracted=0 AND is_confidential=0";
		$result = mysqli_query($dbConnection, $sqlQuery );
		while ($tableData = mysqli_fetch_assoc($result)) {
			$postCount = $tableData["COUNT(*)"];
		}
	
		return $postCount == 1 ? true : false;
	}

	public function postComment(array $parameters) : array {
		$dbConnection = (new DatabaseConnector())->getConnection();
		$sqlQuery  = "SELECT userId FROM users WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		$user = mysqli_fetch_row($result);
		$userId = $user[0];
		
		$sqlQuery  = "SELECT forum_threads.related_thread_id, forum_threads.creatorId FROM forum_threads WHERE forum_threads.threadUrl = '$parameters[2]' LIMIT 1";
		$result = mysqli_query($dbConnection, $sqlQuery );
		while ($tableData = mysqli_fetch_assoc($result)) {
			$threadId = $tableData["related_thread_id"];
			$creatorId = $tableData["creatorId"];
		}
		
		$sqlQuery  = "INSERT INTO comments (body, userId, related_post_id, related_thread_id) VALUES ('$parameters[0]', $userId, $parameters[1], $threadId)";
		$result = mysqli_query($dbConnection, $sqlQuery );


		$sqlQuery  = "INSERT INTO UserNotifications (userId, replied_user_id, type_action, related_thread_id) VALUES ($creatorId, $userId, 2, $threadId)";
		$result = mysqli_query($dbConnection, $sqlQuery );

		mysqli_close($dbConnection);
		return array("response" => 200);
	}
}
?>
<?php 
@session_start();
require_once $_SERVER["DOCUMENT_ROOT"].'/server/settings/ConnectDB.class.php';

class CommentsClass {


	 function getByID(int $id) : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT id FROM tblUsers WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$user = mysqli_fetch_row($sqlResult);
		$userId = $user[0];
		$sqlQuery = "SELECT tblComments.idComment, tblComments.content, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblComments.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image,
		CASE WHEN EXISTS(SELECT tblCommentVotes.idUser FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment) THEN 1 ELSE 0 END as voted,
		IF ((SELECT tblCommentVotes.vote FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment AND tblCommentVotes.vote = 1), 1, -1) as voteType,
		(SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 1 AND tblComments.idComment = tblCommentVotes.idComment) - (SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 0 AND tblComments.idComment = tblCommentVotes.idComment) as numOfVotes
		FROM tblComments JOIN tblUsers ON tblComments.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblComments.idThread LEFT JOIN tblCommentVotes ON tblCommentVotes.idComment = tblComments.idComment 
		WHERE tblComments.isRowDeleted = 0 AND tblComments.idPost = $id GROUP BY tblComments.idComment ORDER BY numOfVotes DESC";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "idComment" => $row['idComment'],
                "content" => $row['content'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes']
            ];
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function viewByQuery(string $inputQuery) : array {
		$dbCon = (new ConnectDB())->connect();
		if(!isset($_SESSION['USERNAME'])) {
			$sqlQuery = "SELECT tblComments.idComment, tblComments.content, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblComments.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image,
			CASE WHEN EXISTS(SELECT tblCommentVotes.idUser FROM tblCommentVotes WHERE tblCommentVotes.idUser = -1 AND tblComments.idComment = tblCommentVotes.idComment) THEN 1 ELSE 0 END as voted,
			IF ((SELECT tblCommentVotes.vote FROM tblCommentVotes WHERE tblCommentVotes.idUser = -1 AND tblComments.idComment = tblCommentVotes.idComment AND tblCommentVotes.vote = 1), 1, -1) as voteType,
			(SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 1 AND tblComments.idComment = tblCommentVotes.idComment) - (SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 0 AND tblComments.idComment = tblCommentVotes.idComment) as numOfVotes
			FROM tblComments JOIN tblUsers ON tblComments.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblComments.idThread LEFT JOIN tblCommentVotes ON tblCommentVotes.idComment = tblComments.idComment 
			WHERE tblComments.isRowDeleted = 0 AND tblComments.content LIKE '%$inputQuery%' GROUP BY tblComments.idComment ORDER BY numOfVotes DESC";
			$dbResult = mysqli_query($dbCon, $sqlQuery);

			$sqlResult = array();

			while($row = mysqli_fetch_assoc($dbResult)) {
				$sqlResult[] = [
                    "idComment" => $row['idComment'],
                    "content" => $row['content'],
                    "timestamp" => $row['createdFromNowInSeconds'],
                    "username" => $row['username'],
                    "ownerId" => $row['ownerId'],
                    "profile_image" => $row['profile_image'],
                    "isVoted" => 0,
                    "typeVote" => 0,
                    "numOfVotes" => $row['numOfVotes']
                ];
			}
			mysqli_close($dbCon);
			return $sqlResult;
		}
		$sqlQuery = "SELECT id FROM tblUsers WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$user = mysqli_fetch_row($sqlResult);
		$userId = $user[0];
		$sqlQuery = "SELECT tblComments.idComment, tblComments.content, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblComments.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image,
		CASE WHEN EXISTS(SELECT tblCommentVotes.idUser FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment) THEN 1 ELSE 0 END as voted,
		IF ((SELECT tblCommentVotes.vote FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment AND tblCommentVotes.vote = 1), 1, -1) as voteType,
		(SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 1 AND tblComments.idComment = tblCommentVotes.idComment) - (SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 0 AND tblComments.idComment = tblCommentVotes.idComment) as numOfVotes
		FROM tblComments JOIN tblUsers ON tblComments.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblComments.idThread LEFT JOIN tblCommentVotes ON tblCommentVotes.idComment = tblComments.idComment 
		WHERE tblComments.isRowDeleted = 0 AND tblComments.content LIKE '%$inputQuery%' GROUP BY tblComments.idComment ORDER BY numOfVotes DESC";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "idComment" => $row['idComment'],
                "content" => $row['content'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes']
            ];
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function doesCommentExist(int $id) : bool {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT idComment FROM tblComments WHERE idComment = $id AND isRowHidden = 0 AND isRowDeleted = 0 LIMIT 1";
		$dbResult = mysqli_query($dbCon, $sqlQuery);
		while($row = mysqli_fetch_assoc($dbResult)) {
			mysqli_close($dbCon);
			return true;
		}
		mysqli_close($dbCon);
		return false;
	}

	 function commentStatus(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT id FROM tblUsers WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$user = mysqli_fetch_row($sqlResult);
		$userId = $user[0];
		if ($inputs[1] === "voteUp") {
			$sqlQuery = "SELECT idComment FROM tblCommentVotes WHERE idComment = $inputs[0] AND idUser = $userId LIMIT 1";
			$dbResult = mysqli_query($dbCon, $sqlQuery);
			
			if(mysqli_num_rows($dbResult) === 0){
				$sqlQuery = "INSERT INTO tblCommentVotes VALUES($inputs[0], $userId, 1)";
				mysqli_query($dbCon, $sqlQuery);
			} else{
				$sqlQuery = "UPDATE tblCommentVotes SET vote = 1 WHERE idComment = $inputs[0] AND idUser = $userId";
				mysqli_query($dbCon, $sqlQuery);
			}
		} else {
			$sqlQuery = "SELECT idComment FROM tblCommentVotes WHERE idComment = $inputs[0] AND idUser = $userId LIMIT 1";
			$dbResult = mysqli_query($dbCon, $sqlQuery);
			
			if(mysqli_num_rows($dbResult) === 0){
				$sqlQuery = "INSERT INTO tblCommentVotes VALUES($inputs[0], $userId, 0)";
				mysqli_query($dbCon, $sqlQuery);
			} else{
				$sqlQuery = "UPDATE tblCommentVotes SET vote = 0 WHERE idComment = $inputs[0] AND idUser = $userId";
				mysqli_query($dbCon, $sqlQuery);
			}
		}
		$sqlQuery = "SELECT 
		(SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 1 AND tblComments.idComment = tblCommentVotes.idComment) - (SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 0 AND tblComments.idComment = tblCommentVotes.idComment) as numOfVotes
		FROM tblComments LEFT JOIN tblCommentVotes ON tblCommentVotes.idComment = tblComments.idComment 
		WHERE tblComments.isRowHidden = 0 AND tblComments.isRowDeleted = 0 AND tblComments.idComment = $inputs[0]";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		$comment = mysqli_fetch_row($sqlResult);
		$voteCount = $comment[0];

		mysqli_close($dbCon);
		return array("response" => 200, "numOfVotes" => $voteCount);
	}

	 function loadAllComments() : array {
		$dbCon = (new ConnectDB())->connect();

		if (!isset($_SESSION['USERNAME'])) {
			$sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, COUNT(tblComments.idPost) as totalComments,
			CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = -1 AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
			IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = -1 AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
			(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
			FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
			WHERE tblPosts.isRowDeleted = 0 
			GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
			$dbResult = mysqli_query($dbCon, $sqlQuery);

			$sqlResult = array();
	
			while($row = mysqli_fetch_assoc($dbResult)) {
				$sqlResult[] = [
                    "idPost" => $row['idPost'],
                    "postTitle" => $row['postTitle'],
                    "content" => $row['content'],
                    "image" => $row['image'],
                    "media_link" => $row['link'],
                    "timestamp" => $row['createdFromNowInSeconds'],
                    "username" => $row['username'],
                    "ownerId" => $row['ownerId'],
                    "profile_image" => $row['profile_image'],
                    "link" => $row['link'],
                    "totalComments" => $row['totalComments'],
                    "isVoted" => 0,
                    "typeVote" => 0,
                    "numOfVotes" => $row['numOfVotes']
                ];
			}
			mysqli_close($dbCon);
			return $sqlResult;

		}

		$sqlQuery = "SELECT id FROM tblUsers WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$user = mysqli_fetch_row($sqlResult);
		$userId = $user[0];

		$sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, COUNT(tblComments.idPost) as totalComments,
		CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
		IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
		(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
		FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
		WHERE tblPosts.isRowDeleted = 0 
		GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "idPost" => $row['idPost'],
                "postTitle" => $row['postTitle'],
                "content" => $row['content'],
                "image" => $row['image'],
                "media_link" => $row['link'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "link" => $row['link'],
                "totalComments" => $row['totalComments'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes']
            ];
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function getAllCommentsFromPostId(int $postId, bool $loadAllComments) : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT id FROM tblUsers WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		$user = mysqli_fetch_row($sqlResult);
		$userId = $user[0];

		if (!$loadAllComments) {
			$sqlQuery = "SELECT tblComments.idComment, tblComments.content, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblComments.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image,
			CASE WHEN EXISTS(SELECT tblCommentVotes.idUser FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment) THEN 1 ELSE 0 END as voted,
			IF ((SELECT tblCommentVotes.vote FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment AND tblCommentVotes.vote = 1), 1, -1) as voteType,
			(SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 1 AND tblComments.idComment = tblCommentVotes.idComment) - (SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 0 AND tblComments.idComment = tblCommentVotes.idComment) as numOfVotes
			FROM tblComments JOIN tblUsers ON tblComments.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblComments.idThread LEFT JOIN tblCommentVotes ON tblCommentVotes.idComment = tblComments.idComment 
			WHERE tblComments.isRowDeleted = 0 AND tblComments.idPost=$postId GROUP BY tblComments.idComment ORDER BY numOfVotes DESC LIMIT 3";
		} else {
			$sqlQuery = "SELECT tblComments.idComment, tblComments.content, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblComments.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image,
			CASE WHEN EXISTS(SELECT tblCommentVotes.idUser FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment) THEN 1 ELSE 0 END as voted,
			IF ((SELECT tblCommentVotes.vote FROM tblCommentVotes WHERE tblCommentVotes.idUser = $userId AND tblComments.idComment = tblCommentVotes.idComment AND tblCommentVotes.vote = 1), 1, -1) as voteType,
			(SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 1 AND tblComments.idComment = tblCommentVotes.idComment) - (SELECT COUNT(*) FROM tblCommentVotes WHERE tblCommentVotes.vote = 0 AND tblComments.idComment = tblCommentVotes.idComment) as numOfVotes
			FROM tblComments JOIN tblUsers ON tblComments.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblComments.idThread LEFT JOIN tblCommentVotes ON tblCommentVotes.idComment = tblComments.idComment 
			WHERE tblComments.isRowDeleted = 0 AND tblComments.idPost=$postId GROUP BY tblComments.idComment ORDER BY createdFromNowInSeconds DESC";
		}
		
		$dbResult = mysqli_query($dbCon, $sqlQuery);
		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "idComment" => $row['idComment'],
                "content" => $row['content'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes'],
                "isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
                "isOwner" => $_SESSION["USERNAME"] == $row['username'] ? true : false
            ];
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function findAll(array $inputs) : array {
		return array();
	}

	 function removeCommentById(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "UPDATE tblComments SET isRowDeleted=1 WHERE tblComments.idComment=$inputs[0] LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		mysqli_close($dbCon);
		return array("response" => 200);
	} 

	 function approveThreadAndPost(array $inputs) : bool {
		$dbCon = (new ConnectDB())->connect();
		
		$sqlQuery = "SELECT tblThreads.idThread FROM tblThreads WHERE tblThreads.link = '$inputs[2]' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$threadId = $row["idThread"];
		}

		$sqlQuery = "SELECT COUNT(*) FROM tblPosts WHERE idPost=$inputs[1] AND idThread=$threadId AND isRowDeleted=0 AND isRowHidden=0";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$postCount = $row["COUNT(*)"];
		}
	
		return $postCount == 1 ? true : false;
	}

	 function addCommentToPost(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT id FROM tblUsers WHERE username='".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		$user = mysqli_fetch_row($sqlResult);
		$userId = $user[0];
		
		$sqlQuery = "SELECT tblThreads.idThread, tblThreads.idUser FROM tblThreads WHERE tblThreads.link = '$inputs[2]' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$threadId = $row["idThread"];
			$owner_id = $row["idUser"];
		}
		
		$sqlQuery = "INSERT INTO tblComments (content, idUser, idPost, idThread) VALUES ('$inputs[0]', $userId, $inputs[1], $threadId)";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);


		$sqlQuery = "INSERT INTO tblNotifications (idUser,	idUserReply, notificationType, idThread) VALUES ($owner_id, $userId, 2, $threadId)";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);

		mysqli_close($dbCon);
		return array("response" => 200);
	}
}
?>
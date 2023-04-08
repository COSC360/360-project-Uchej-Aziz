<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/CommentsClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/UsersClass.class.php';
header('Content-Type: application/json; charset=utf-8');

$dbResult = array("response" => 400, "data" => array("message" => "Fields are empty."));
if ($_SERVER['REQUEST_METHOD'] === "GET") {
	if (!empty($_GET['query']) && isset($_GET['commentSearch']) && $_GET['commentSearch']) {
		$dbResult = (new CommentHelperClass())->searchComments([$_GET['query']]);
	} else if (!isset($_GET['query']) && !empty($_GET['postUrl']) && isset($_GET['commentFind'])) {
		$dbResult = (new CommentHelperClass())->postAjaxComments([$_GET['postUrl']]);
	} else if (empty($_GET['query'])) {
		$dbResult = (new CommentsClass())->loadAllComments();
	}
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
	if (!empty($_POST['commentId']) && !empty($_POST['type']) && ($_POST['type'] === "voteUp" || $_POST['type'] === "voteDown")) {
		$dbResult = (new CommentHelperClass())->commentStatus([$_POST['commentId'], $_POST['type']]);
	} else if (!empty($_POST['commentId']) && $_POST['removeCommentById']) {
        $dbResult = (new CommentHelperClass())->removeComment($_POST['commentId']);
	} else if (!empty($_POST['threadUrl']) && !empty($_POST['postId']) && !empty($_POST['commentBody'])) {
        $dbResult = (new CommentHelperClass())->postComment([$_POST['commentBody'], $_POST['postId'], $_POST['threadUrl']]);
	}
}

class CommentHelperClass {
    
   function loginStatus() : bool {
		if (isset($_SESSION['IS_AUTHORIZED'])) return true;
		return false;
	}

	 function postAjaxComments(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unauthorized attempt. Account is disabled."));
		
		$postId = intVal($inputs[0]);

		if ($postId <= 0) return array("response" => 403);

		return (new CommentsClass())->getByID($postId);
		
	}

	 function commentStatus(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unauthorized attempt. Account is disabled."));
		
		$commentId = intval($inputs[0]);
		if ($commentId <= 0) return array("response" => 403);
		
		if (!(new CommentsClass())->doesCommentExist($commentId)) return array("response" => 403);

		if ($inputs[1] === "voteUp" || $inputs[1] === "voteDown")
			return (new CommentsClass())->commentStatus([$commentId, $inputs[1]]);
		
		return array("response" => 403);
	}

	 function searchComments(array $inputs) : array {
	
		$inputQuery = filter_var($inputs[0], FILTER_SANITIZE_STRING);
		$inputQuery = trim($inputQuery);
		$inputQuery = stripslashes($inputQuery);
		$inputQuery = htmlspecialchars($inputQuery);

		return (new CommentsClass())->viewByQuery($inputQuery);
	}

	 function removeComment(int $commentId) : array {
		if (empty($commentId)) {
			return array("response" => 400, "data" => array("message" => "You must click a valid delete button in a valid thread of a valid post of a valid comment."));
		} 

		if ($commentId <= 0) {
			return array("response" => 400, "data" => array("message" => "You must click a valid delete button in a valid thread of a valid post of a valid comment."));
		}
		
		return (new CommentsClass())->removeCommentById([$commentId]);
	}

	 function postComment(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));

		if (empty($inputs[0]) || empty($inputs[1]) || empty($inputs[2])) {
			return array("response" => 400, "data" => array("message" => "Fields are empty."));
		}

		if (!is_string($inputs[0])) {
			return array("response" => 400, "data" => array("message" => "You may only enter a string comment"));
		}

		$inputs[0] = htmlspecialchars($inputs[0]);
		$sqlResults = (new CommentsClass())->approveThreadAndPost([$inputs[0], $inputs[1], $inputs[2]]);
		
		if ($sqlResults)
			return (new CommentsClass())->addCommentToPost([$inputs[0], $inputs[1], $inputs[2]]);
		else
			return array("response" => 400, "data" => array("message" => "The following post or thread do not exist."));
	}
}

$sqlResponse = json_encode($dbResult, true);
echo $sqlResponse;
?>
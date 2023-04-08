<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/UsersClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/PostsClass.class.php';

header('Content-Type: application/json; charset=utf-8');

$dbResult = array("response" => 400, "data" => array("message" => "Fields are empty."));

if ($_SERVER['REQUEST_METHOD'] === "POST") {
	if (!empty($_POST['postTitle'])) {
		if (!empty($_POST['postBody']) && !empty($_FILES['postImage']) && !empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([1, $_POST['postTitle'], $_POST['postBody'], $_FILES['postImage'], $_POST['postYoutubeLink'], $_POST['threadUrl']]);
		} else if (!empty($_POST['postBody']) && !empty($_FILES['postImage']) && empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([2, $_POST['postTitle'], $_POST['postBody'], $_FILES['postImage'], $_POST['threadUrl']]);
		} else if (!empty($_POST['postBody']) && empty($_FILES['postImage']) && !empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([3, $_POST['postTitle'], $_POST['postBody'], $_POST['postYoutubeLink'], $_POST['threadUrl']]);
		} else if (empty($_POST['postBody']) && !empty($_FILES['postImage']) && !empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([4, $_POST['postTitle'], $_FILES['postImage'], $_POST['postYoutubeLink'], $_POST['threadUrl']]);
		} else if (!empty($_POST['postBody']) && empty($_FILES['postImage']) && empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([5, $_POST['postTitle'], $_POST['postBody'], $_POST['threadUrl']]);
		} else if (empty($_POST['postBody']) && !empty($_FILES['postImage']) && empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([6, $_POST['postTitle'], $_FILES['postImage'], $_POST['threadUrl']]);
		} else if (empty($_POST['postBody']) && empty($_FILES['postImage']) && !empty($_POST['postYoutubeLink'])) {
			$dbResult = (new PostHelperClass())->createPost([7, $_POST['postTitle'], $_POST['postYoutubeLink'], $_POST['threadUrl']]);
		} 
	} else if (!empty($_POST['postId']) && !empty($_POST['type']) && ($_POST['type'] === "voteUp" || $_POST['type'] === "voteDown")) {
		$dbResult = (new PostHelperClass())->commentStatus([$_POST['postId'], $_POST['type']]);
	} else if (!empty($_POST['postId']) && isset($_POST['postDelete'])) {
		$dbResult = (new PostHelperClass())->removePost($_POST['postId']);
	} else if (!empty($_POST['postId']) && (bool)$_POST['hidePost'] && ($_POST['buttonText'] == "hide" || $_POST['buttonText'] == "unhide")) {
		$dbResult = (new PostHelperClass())->hidePost([$_POST['postId'], $_POST['buttonText']]);
	}
} else if ($_SERVER['REQUEST_METHOD'] === "GET") {
	if(!empty($_GET['threadUrl']) && !empty($_GET['postId'])) {
		$dbResult = (new PostsClass())->loadSpecificPost([$_GET['threadUrl'], $_GET['postId']]);
	} else {
		if (!empty($_GET['threadUrl']) && !empty($_GET['sortType'])) {
			$dbResult = (new PostHelperClass())->sortPosts([$_GET['threadUrl'], $_GET['sortType']]);
		} else if (!empty($_GET['query']) && !empty($_GET['threadUrl']) && isset($_GET['postSearch']) && $_GET['postSearch']) {
			$dbResult = (new PostHelperClass())->searchPostsInThread([$_GET['query'], $_GET['threadUrl']]);
		} else if (!empty($_GET['threadUrl']) && empty($_GET['sortType'])) {
			$dbResult = (new PostHelperClass())->sortPosts([$_GET['threadUrl']]);
		} else if (!empty($_GET['query']) && isset($_GET['postSearch']) && $_GET['postSearch']) {
			$dbResult = (new PostHelperClass())->searchPosts([$_GET['query']]);
		} else if (empty($_GET['query'])) {
			$dbResult =(new PostsClass())->loadAllPosts();
		}
	}
}

class PostHelperClass {
    
   function loginStatus() : bool {
		if (isset($_SESSION['IS_AUTHORIZED'])) return true;
		return false;
	}

	 function searchPosts(array $inputs) : array {

		$inputQuery = filter_var($inputs[0], FILTER_SANITIZE_STRING);
		$inputQuery = trim($inputQuery);
		$inputQuery = stripslashes($inputQuery);
		$inputQuery = htmlspecialchars($inputQuery);

		return (new PostsClass())->getPostByQuery($inputQuery);
	}

	 function searchPostsInThread(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		$inputQuery = filter_var($inputs[0], FILTER_SANITIZE_STRING);
		$inputQuery = trim($inputQuery);
		$inputQuery = stripslashes($inputQuery);
		$inputQuery = htmlspecialchars($inputQuery);

		return (new PostsClass())->searchPostByQueryInThread([$inputQuery, $inputs[1]]);
	}

	 function commentStatus(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		$postId = intval($inputs[0]);
		if ($postId <= 0) return array("response" => 403);

		if (!(new PostsClass())->doesPostExists($postId)) return array("response" => 403);

		if ($inputs[1] === "voteUp" || $inputs[1] === "voteDown")
			return (new PostsClass())->commentStatus([$postId, $inputs[1]]);
		
		return array("response" => 403);
	}

	 function createPost(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
			
		$postTitle = $inputs[1];
		$threadUrl = end($inputs);

		if (empty($postTitle)) {
			return array("response" => 400, "data" => array("message" => "Post title cannot be empty."));
		}

		if (!preg_match("/^[a-zA-Z0-9\s]+$/", $postTitle)) {
			return array("response" => 400, "data" => array("message" => "The thread title cannot contain special characters."));
		}

		if (strlen($postTitle) > 75) {
			return array("response" => 400, "data" => array("message" => "The post title cannot be longer than 75 characters."));
		}

		if (strlen($postTitle) < 5) {
			return array("response" => 400, "data" => array("message" => "The post title cannot be shorter than 5 characters."));
		}
         $targetDirThreadBackground = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/post_images/';
         // Check if file already exists
         // Added by Aziz
         // uploads folders requires chmod -R 777 to work
         if (!is_dir($targetDirThreadBackground) && !mkdir($targetDirThreadBackground)){
             die("Error creating thread background folder $targetDirThreadBackground");
         }
		$caseNumber = $inputs[0];
		switch ($caseNumber) {
			case 1:
				$postBody = htmlspecialchars($inputs[2]);
				$postImage = $inputs[3];
				$youtubeLink = $inputs[4];

				if ($postImage['size'] == 0 || $postImage['size'] > (5 * 1024 * 1024)) {
						return array("response" => 400, "data" => array("message" => "The post image must be less than 5MB."));
				}

				$targetDir = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/post_images/';
				$imgFileType = strtolower(pathinfo($postImage['name'], PATHINFO_EXTENSION));

				if ($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "gif")
			return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));
				
				$imgFile = "";
				$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				for ($i = 0; $i < 16; $i++)
						$imgFile .= $randString[mt_rand(0, 61)];
				
				$targetFile = $targetDir . 
												basename($imgFile.'.'.
												strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)));
				
				move_uploaded_file($postImage["tmp_name"], $targetFile);


				if (strlen($youtubeLink) > 0 && !preg_match("/^(https|http):\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+$/", $youtubeLink)) {
						return array("response" => 400, "data" => array("message" => "The YouTube link is not valid. It should contain \"embed\" in the link."));
				}

				return (new PostsClass())->post([
						$caseNumber,
						$postTitle,
						$postBody,
						$imgFile . '.' . strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)),
						$youtubeLink,
						$threadUrl
				]);
		
			case 2:
				$postBody =  htmlspecialchars($inputs[2]);
				$postImage = $inputs[3];
				
				if ($postImage['size'] == 0 || $postImage['size'] > (5 * 1024 * 1024)) {
						return array("response" => 400, "data" => array("message" => "The post image must be less than 5MB."));
				}

				$targetDir = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/post_images/';
				$imgFileType = strtolower(pathinfo($postImage['name'], PATHINFO_EXTENSION));

				if ($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "gif")
			return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));
				
				$imgFile = "";
				$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				for ($i = 0; $i < 16; $i++)
						$imgFile .= $randString[mt_rand(0, 61)];
				
				$targetFile = $targetDir . 
												basename($imgFile.'.'.
												strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)));
				
				move_uploaded_file($postImage["tmp_name"], $targetFile);

				return (new PostsClass())->post([
						$caseNumber,
						$postTitle,
						$postBody,
						$imgFile . '.' . strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)),
						$threadUrl
				]);
			
			case 3:
				$postBody =  htmlspecialchars($inputs[2]);
				$youtubeLink = $inputs[3];

				if (strlen($youtubeLink) > 0 && !preg_match("/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/", $youtubeLink)) {
						return array("response" => 400, "data" => array("message" => "The YouTube link is not valid."));
				}

				return (new PostsClass())->post([
						$caseNumber,
						$postTitle,
						$postBody,
						$youtubeLink,
						$threadUrl
				]);
		
			case 4:
				$postImage = $inputs[2];
				$youtubeLink = $inputs[3];

				if ($postImage['size'] == 0 || $postImage['size'] > (5 * 1024 * 1024)) {
						return array("response" => 400, "data" => array("message" => "The post image must be less than 5MB."));
				}

				$targetDir = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/post_images/';
				$imgFileType = strtolower(pathinfo($postImage['name'], PATHINFO_EXTENSION));

				if ($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "gif") {
					return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));
				}
				$imgFile = "";
				$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				for ($i = 0; $i < 16; $i++)
					$imgFile .= $randString[mt_rand(0, 61)];
				
				$targetFile = $targetDir . 
												basename($imgFile.'.'.
												strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)));
				
				move_uploaded_file($postImage["tmp_name"], $targetFile);
				
				if (strlen($youtubeLink) > 0 && !preg_match("/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/", $youtubeLink)) {
					return array("response" => 400, "data" => array("message" => "The YouTube link is not valid."));
				}

				return (new PostsClass())->post([
					$caseNumber,
					$postTitle,
					$imgFile . '.' . strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)),
					$youtubeLink,
					$threadUrl
				]);
		
			case 5:
				return (new PostsClass())->post([
					$caseNumber,
					$postTitle,
					$inputs[2],
					$threadUrl
				]);
					
			case 6:
				$postImage = $inputs[2];
				if ($postImage['size'] == 0 || $postImage['size'] > (5 * 1024 * 1024)) {
						return array("response" => 400, "data" => array("message" => "The post image must be less than 5MB."));
				}

				$targetDir = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/post_images/';
				$imgFileType = strtolower(pathinfo($postImage['name'], PATHINFO_EXTENSION));

				if ($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "gif")
			return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));
				
				$imgFile = "";
				$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				for ($i = 0; $i < 16; $i++)
						$imgFile .= $randString[mt_rand(0, 61)];
				
				$targetFile = $targetDir . basename($imgFile.'.'. strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)));
				
				move_uploaded_file($postImage["tmp_name"], $targetFile);

				return (new PostsClass())->post([
						$caseNumber,
						$postTitle,
						$imgFile . '.' . strtolower(pathinfo($postImage["name"], PATHINFO_EXTENSION)),
						$threadUrl
			]);

			case 7:
				$youtubeLink = $inputs[2];
				if (strlen($youtubeLink) > 0 && !preg_match("/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/", $youtubeLink)) {
					return array("response" => 400, "data" => array("message" => "The YouTube link is not valid."));
				}

				return (new PostsClass())->post([
					$caseNumber,
					$postTitle,
					$youtubeLink,
					$threadUrl
			]);
		}
	}

	 function sortPosts(array $inputs) {
		if (empty($inputs[0])) {
			return array("response" => 400, "data" => array("message" => "You must click a sort button in a valid thread."));
		}

		if (!empty($inputs[1]) && !($inputs[1] == "Top" || $inputs[1] == "New")) {
			return array("response" => 400, "data" => array("message" => "You must click a sort button in a valid thread."));
		}

		if (empty($inputs[1])) {
			return (new PostsClass())->loadPostByThread([$inputs[0]]);
		}

		return (new PostsClass())->loadPostByThread([$inputs[0], $inputs[1]]);
	}

	 function removePost(int $postId) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		if (empty($postId)) {
			return array("response" => 400, "data" => array("message" => "You must click a valid delete button in a valid thread of a valid post."));
		} 

		if ($postId <= 0) {
			return array("response" => 400, "data" => array("message" => "You must click a valid delete button in a valid thread of a valid post."));
		}

		return (new PostsClass())->deletePost([$postId]);
	}

	 function hidePost(array $inputs) : array {
		$postId = $inputs[0];
		$buttonText = $inputs[1];
		if (!$this->loginStatus()) return array("response" => 403);
		
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
	
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		if (empty($postId)) {
			return array("response" => 400, "data" => array("message" => "You must click a valid delete button in a valid thread of a valid post."));
		} 

		if ($postId <= 0) {
			return array("response" => 400, "data" => array("message" => "You must click a valid delete button in a valid thread of a valid post."));
		}
		return (new PostsClass())->disablePost([$postId, $buttonText]);
	}
}

$sqlResponse = json_encode($dbResult, true);
echo $sqlResponse;
?>